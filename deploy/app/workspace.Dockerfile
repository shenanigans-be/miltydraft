FROM caddy:2.7-builder AS caddy-builder

RUN xcaddy build \
    --with github.com/baldinof/caddy-supervisor \
    --with github.com/caddyserver/cache-handler@v0.12.0

FROM php:8.2-fpm-alpine

COPY --from=caddy-builder /usr/bin/caddy /usr/bin/caddy
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY deploy/app/caddy/Caddyfile /etc/caddy/Caddyfile
COPY deploy/app/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN caddy validate --config=/etc/caddy/Caddyfile

WORKDIR /code

# Install container deps that apply to all target environments...
COPY deploy/app/setup.sh .
RUN /code/setup.sh

# Install container deps that only apply to the base image...
RUN apk --no-cache add sqlite supervisor curl

USER app

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
