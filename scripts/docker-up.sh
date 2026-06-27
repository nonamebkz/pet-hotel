#!/usr/bin/env bash
# Build & jalankan stack Docker, lalu init database.
#
# Usage:
#   ./scripts/docker-up.sh [options]
#
# Options:
#   --no-build    Skip docker compose build
#   --no-seed     Hanya schema, tanpa seed dev
#   --fresh       Hapus volume DB (HAPUS DATA!) lalu init ulang

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

BUILD=1
SEED=1
FRESH=0

for arg in "$@"; do
    case "$arg" in
        --no-build) BUILD=0 ;;
        --no-seed) SEED=0 ;;
        --fresh) FRESH=1 ;;
        -h|--help)
            sed -n '2,10p' "$0" | sed 's/^# \{0,1\}//'
            exit 0
            ;;
        *)
            echo "Opsi tidak dikenal: $arg" >&2
            exit 1
            ;;
    esac
done

if [[ ! -f .env ]]; then
    if [[ -f .env.docker.example ]]; then
        echo ">> .env tidak ada — menyalin dari .env.docker.example"
        cp .env.docker.example .env
    else
        echo "ERROR: Buat .env dulu (cp .env.docker.example .env)" >&2
        exit 1
    fi
fi

if [[ "$FRESH" -eq 1 ]]; then
    echo ">> Menghapus volume database (fresh install)..."
    docker compose down -v
fi

if [[ "$BUILD" -eq 1 ]]; then
    docker compose up -d --build
else
    docker compose up -d
fi

echo ">> Menunggu MariaDB healthy..."
docker compose up -d --wait mariadb

if [[ "$SEED" -eq 1 ]]; then
    ./scripts/db-init.sh --docker --wait all
else
    ./scripts/db-init.sh --docker --wait schema
fi

echo ""
echo "Aplikasi: http://localhost:${APP_PORT:-8080}"
echo "Admin owner: owner@petshop.local / password123"
