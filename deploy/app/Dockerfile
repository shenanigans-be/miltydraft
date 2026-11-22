FROM dunglas/frankenphp:1-php8.5-alpine AS base

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY deploy/app/php/php.ini $PHP_INI_DIR/conf.d/php.ini
COPY deploy/app/Caddyfile /etc/frankenphp/Caddyfile

RUN frankenphp validate --config /etc/frankenphp/Caddyfile

WORKDIR /code

# Run as a non-root user that can still bind to ports 80/443
RUN addgroup -g 1000 -S app \
  && adduser -u 1000 -S app -G app \
  && setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp \
  && chown -R app:app /code /config/caddy /data/caddy

USER app

FROM base AS development

FROM base AS production

COPY --chown=app:app . /code

RUN composer install --no-dev --optimize-autoloader
