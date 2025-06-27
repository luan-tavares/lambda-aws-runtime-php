FROM php:8.4-cli

# Atualiza e instala dependências do sistema + extensões necessárias
RUN apt update && apt install -y \
    curl unzip git \
    libcurl4-openssl-dev pkg-config libssl-dev libpq-dev libzip-dev \
    libbrotli-dev \
    && docker-php-ext-install pdo pdo_pgsql curl zip

# Instala Swoole completo (sem desabilitar nada!)
RUN pecl install swoole \
    && docker-php-ext-enable swoole

# Criação de diretórios esperados
RUN mkdir -p /var/runtime /var/task /tmp/storage \
    && chmod -R 777 /tmp/storage

# Instala Composer
RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/local/bin --filename=composer

# Instala dependências PHP do projeto
WORKDIR /var/runtime
COPY ./src/runtime/bootstrap.php /var/runtime/bootstrap
RUN chmod +x ./bootstrap

# Copia código dos handlers
WORKDIR /var/task
COPY ./src /var/task
RUN composer install --no-dev --optimize-autoloader


# CMD padrão
CMD ["/var/runtime/bootstrap"]
