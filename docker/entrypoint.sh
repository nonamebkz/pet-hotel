#!/bin/bash
set -e

UPLOAD_ROOT="/var/www/html/public/uploads"
LOG_ROOT="/var/www/html/storage/logs"

for dir in profil kucing vaksin bukti_transfer monitoring; do
    mkdir -p "${UPLOAD_ROOT}/${dir}"
done

mkdir -p "${LOG_ROOT}"

chown -R www-data:www-data "${UPLOAD_ROOT}" "${LOG_ROOT}"
chmod -R 775 "${UPLOAD_ROOT}" "${LOG_ROOT}"

exec docker-php-entrypoint "$@"
