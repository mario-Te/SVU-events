# Use an official PHP image with Apache web server
FROM php:8.2-apache

# Install and enable PDO MySQL extensions required for database connection
RUN docker-php-ext-install pdo pdo_mysql && docker-php-ext-enable pdo pdo_mysql

# Copy all your project files into the default Apache directory
COPY . /var/www/html/

# Expose port 80 for web traffic
EXPOSE 80
