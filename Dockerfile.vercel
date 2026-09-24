FROM dunglas/frankenphp:php8.3-bookworm

WORKDIR /app

RUN install-php-extensions pdo_pgsql

COPY . /app

CMD ["sh", "-c", "frankenphp php-server --listen :${PORT:-80} --root /app"]