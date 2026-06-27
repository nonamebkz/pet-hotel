#!/usr/bin/env bash
# Import schema & seed MariaDB untuk aplikasi Petshop.
# Mendukung mode Docker (docker exec) dan lokal (mysql/mariadb CLI).
#
# Usage:
#   ./scripts/db-init.sh [command] [options]
#
# Commands:
#   all        Schema + seed dev (default)
#   schema     Semua file schema-mariadb-*.sql
#   seed       Seed minimal (owner + pengaturan)
#   seed-dev   Semua seed development
#
# Options:
#   --docker           Paksa mode Docker
#   --local            Paksa mode CLI lokal
#   --container NAME   Nama container (default: petshop-mariadb)
#   --wait             Tunggu DB siap sebelum import
#   -h, --help         Bantuan

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

CONTAINER="${DOCKER_CONTAINER:-petshop-mariadb}"
MODE="${DB_MODE:-auto}"
WAIT_DB=0
COMMAND="all"

load_env() {
    if [[ ! -f .env ]]; then
        return 0
    fi
    while IFS= read -r line || [[ -n "$line" ]]; do
        line="${line%%#*}"
        line="$(echo "$line" | sed 's/^[[:space:]]*//;s/[[:space:]]*$//')"
        [[ -z "$line" || ! "$line" == *"="* ]] && continue
        key="${line%%=*}"
        value="${line#*=}"
        value="${value%\"}"
        value="${value#\"}"
        value="${value%\'}"
        value="${value#\'}"
        export "$key=$value"
    done < .env
}

usage() {
    sed -n '2,18p' "$0" | sed 's/^# \{0,1\}//'
    exit "${1:-0}"
}

parse_args() {
    local args=()
    for arg in "$@"; do
        case "$arg" in
            --docker) MODE=docker ;;
            --local) MODE=local ;;
            --container=*) CONTAINER="${arg#*=}" ;;
            --container)
                shift
                CONTAINER="${1:-$CONTAINER}"
                ;;
            --wait) WAIT_DB=1 ;;
            -h|--help) usage 0 ;;
            schema|seed|seed-dev|all) COMMAND="$arg" ;;
            *) args+=("$arg") ;;
        esac
    done
    if [[ ${#args[@]} -gt 0 ]]; then
        COMMAND="${args[0]}"
    fi
}

detect_mode() {
    if [[ "$MODE" == "auto" ]]; then
        if docker ps --format '{{.Names}}' 2>/dev/null | grep -qx "$CONTAINER"; then
            MODE=docker
        else
            MODE=local
        fi
    fi
}

schema_files() {
    cat <<'EOF'
database/schema-mariadb-auth.sql
database/schema-mariadb-kucing.sql
database/schema-mariadb-pet-care.sql
database/schema-mariadb-grooming.sql
database/schema-mariadb-penitipan.sql
database/schema-mariadb-transaksi.sql
database/schema-mariadb-notifikasi.sql
database/schema-mariadb-pengaturan.sql
EOF
}

seed_minimal_files() {
    cat <<'EOF'
database/seeds/seed-owner.sql
database/seeds/seed-pengaturan-dev.sql
EOF
}

seed_dev_files() {
    cat <<'EOF'
database/seeds/seed-owner.sql
database/seeds/seed-pengaturan-dev.sql
database/seeds/seed-pet-care-dev.sql
database/seeds/seed-grooming-dev.sql
database/seeds/seed-penitipan-dev.sql
EOF
}

wait_for_db() {
    local max=30
    local i=0
    echo ">> Menunggu database siap (mode: $MODE)..."

    while (( i < max )); do
        if run_sql_inline "SELECT 1" >/dev/null 2>&1; then
            echo ">> Database siap."
            return 0
        fi
        sleep 2
        (( i++ )) || true
    done

    echo "ERROR: Database tidak merespons setelah $((max * 2)) detik." >&2
    exit 1
}

run_sql_inline() {
    local sql="$1"
    if [[ "$MODE" == "docker" ]]; then
        docker exec -i "$CONTAINER" mariadb \
            -u"${DB_USERNAME:-root}" \
            -p"${DB_PASSWORD:-root}" \
            "${DB_DATABASE:-petshop}" \
            -e "$sql"
    else
        local client=""
        if command -v mariadb >/dev/null 2>&1; then
            client=mariadb
        elif command -v mysql >/dev/null 2>&1; then
            client=mysql
        else
            echo "ERROR: mariadb/mysql CLI tidak ditemukan. Gunakan --docker." >&2
            exit 1
        fi
        "$client" \
            -h"${DB_HOST:-127.0.0.1}" \
            -P"${DB_PORT:-3306}" \
            -u"${DB_USERNAME:-root}" \
            -p"${DB_PASSWORD:-}" \
            "${DB_DATABASE:-petshop}" \
            -e "$sql"
    fi
}

run_sql_file() {
    local file="$1"
    if [[ ! -f "$file" ]]; then
        echo "ERROR: File tidak ditemukan: $file" >&2
        exit 1
    fi
    echo ">> Import: $file"
    if [[ "$MODE" == "docker" ]]; then
        docker exec -i "$CONTAINER" mariadb \
            -u"${DB_USERNAME:-root}" \
            -p"${DB_PASSWORD:-root}" \
            "${DB_DATABASE:-petshop}" < "$file"
    else
        local client=""
        if command -v mariadb >/dev/null 2>&1; then
            client=mariadb
        else
            client=mysql
        fi
        "$client" \
            -h"${DB_HOST:-127.0.0.1}" \
            -P"${DB_PORT:-3306}" \
            -u"${DB_USERNAME:-root}" \
            -p"${DB_PASSWORD:-}" \
            "${DB_DATABASE:-petshop}" < "$file"
    fi
}

run_files() {
    local list="$1"
    while IFS= read -r file; do
        [[ -z "$file" ]] && continue
        run_sql_file "$file"
    done <<< "$list"
}

main() {
    parse_args "$@"
    load_env
    detect_mode

    echo "== Petshop DB init =="
    echo "   Mode      : $MODE"
    echo "   Database  : ${DB_DATABASE:-petshop}"
    echo "   Command   : $COMMAND"
    if [[ "$MODE" == "docker" ]]; then
        echo "   Container : $CONTAINER"
    else
        echo "   Host      : ${DB_HOST:-127.0.0.1}:${DB_PORT:-3306}"
    fi

    if [[ "$WAIT_DB" -eq 1 ]]; then
        wait_for_db
    fi

    case "$COMMAND" in
        schema)
            run_files "$(schema_files)"
            ;;
        seed)
            run_files "$(seed_minimal_files)"
            ;;
        seed-dev)
            run_files "$(seed_dev_files)"
            ;;
        all)
            run_files "$(schema_files)"
            run_files "$(seed_dev_files)"
            ;;
        -h|--help|help)
            usage 0
            ;;
        *)
            echo "ERROR: Perintah tidak dikenal: $COMMAND" >&2
            usage 1
            ;;
    esac

    echo ">> Selesai."
}

main "$@"
