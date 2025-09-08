# Use a imagem oficial do PHP 8.2 com Apache
FROM php:8.2-apache

# Define o diretório de trabalho correto
WORKDIR /var/www/html

# Instala dependências do sistema para o Laravel (gd para imagens, pdo_mysql para base de dados, zip)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

# Habilita o mod_rewrite do Apache para URLs amigáveis
RUN a2enmod rewrite

# Instala o Composer globalmente
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

EXPOSE 80
