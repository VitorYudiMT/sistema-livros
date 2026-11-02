#imagem base
FROM php:8.4-apache

# executa comandos durante a construção da imagem instalando bibliotecas php, sql, etc
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    && docker-php-ext-install \
    pdo_mysql \
    intl \
    zip \
    opcache \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# Copia o executável Composer de outra imagem Docker (multi-stage build)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Define somente a pasta pública do Symfony como raiz do Apache (sem o restante do projeto)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Ajusta a configuração padrão do Apache
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/000-default.conf \
    /etc/apache2/apache2.conf

# Copia nossa configuração personalizada do Apache
COPY docker/apache/000-default.conf \
    /etc/apache2/sites-available/000-default.conf

# Define o diretório de trabalho
WORKDIR /var/www/html