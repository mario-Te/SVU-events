# Use an official PHP image with Apache web server
FROM php:8.2-apache

# Copy all your project files into the default Apache directory
COPY . /var/www/html/

# Expose port 80 for web traffic
EXPOSE 80
