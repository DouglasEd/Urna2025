# Use a imagem base do PHP com Apache
FROM php:8.2-apache

# Habilita o mod_rewrite do Apache
RUN a2enmod rewrite

# Instala as dependências necessárias
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    mariadb-client \
    && docker-php-ext-install zip pdo_mysql mysqli

# Instala o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copia os arquivos do projeto para o contêiner
COPY . /var/www/html

# Define o diretório de trabalho
WORKDIR /var/www/html
    
# Copia o arquivo .env.example para .env (se necessário)
RUN cp .env.example .env

# Expõe a porta 80
EXPOSE 80

# Comando para iniciar o Apache
CMD ["apache2-foreground"]