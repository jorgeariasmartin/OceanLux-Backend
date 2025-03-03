# Usamos una imagen oficial de PHP con FPM (FastCGI Process Manager)
FROM php:8.2-fpm

# Instalar dependencias del sistema y extensiones de PHP necesarias para Symfony
RUN apt-get update && apt-get install -y \
    unzip git libpq-dev nginx \
    && docker-php-ext-install pdo pdo_pgsql

# Configurar el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Copiar los archivos del proyecto Symfony al contenedor
COPY . /var/www/html

# Instalar Composer para gestionar dependencias de PHP
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalar las dependencias de Symfony en modo producción
RUN composer install --no-dev --optimize-autoloader

# Asignar permisos correctos a las carpetas de caché y logs
RUN chown -R www-data:www-data /var/www/html/var
RUN chmod -R 777 /var/www/html/var

# Copiar la configuración de Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Exponer el puerto 80 para acceder al servidor
EXPOSE 80

# Comando de inicio para arrancar Nginx y PHP-FPM
CMD service nginx start && php-fpm
