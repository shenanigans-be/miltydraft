#!/usr/bin/env sh

# Add a non-root user we can use to run the app
addgroup -g 1000 -S app \
  && adduser -u 1000 -S app -G app \
  && chown app /code

# Install container dependencies
apk --update --no-cache add \
  autoconf \
  curl \
  cyrus-sasl-dev \
  g++ \
  git \
  libtool \
  make
