#!/bin/bash
set -e

UPLOAD_ROOT="/var/www/html/public/uploads"
LOG_ROOT="/var/www/html/storage/logs"

php /var/www/html/scripts/ensure-upload-dirs.php || {
    for dir in profil kucing vaksin bukti_transfer monitoring; do
        mkdir -p "${UPLOAD_ROOT}/${dir}"
    done
    mkdir -p "${LOG_ROOT}"
}

chown -R www-data:www-data "${UPLOAD_ROOT}" "${LOG_ROOT}"
chmod -R 775 "${UPLOAD_ROOT}" "${LOG_ROOT}"

auto_db_init() {
    case "${AUTO_DB_INIT:-1}" in
        0|false|FALSE|no|NO|off|OFF) return 0 ;;
    esac

    if [[ ! -x /var/www/html/scripts/db-init.sh ]]; then
        echo ">> AUTO_DB_INIT: db-init.sh tidak ditemukan, dilewati." >&2
        return 0
    fi

    echo ">> AUTO_DB_INIT: memeriksa schema & seed database..."
    export DB_HOST="${DB_HOST:-mariadb}"
    export DB_PORT="${DB_PORT:-3306}"
    /var/www/html/scripts/db-init.sh --local --wait if-needed
}

auto_db_init

exec docker-php-entrypoint "$@"
