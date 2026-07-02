# syntax=docker/dockerfile:1

FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

FROM php:8.4-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libzip-dev \
    && docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

COPY --from=vendor /app/vendor ./vendor
COPY . .

RUN mkdir -p public/uploads/profil public/uploads/kucing public/uploads/vaksin public/uploads/bukti_transfer storage/logs \
    && chown -R www-data:www-data public/uploads storage/logs \
    && chmod -R 775 public/uploads storage/logs

COPY docker/entrypoint.sh /usr/local/bin/petshop-entrypoint.sh
RUN chmod +x /usr/local/bin/petshop-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/petshop-entrypoint.sh"]
CMD ["apache2-foreground"]

EXPOSE 80
