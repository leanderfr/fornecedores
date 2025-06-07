

FROM php:8.4-apache
RUN docker-php-ext-install mysqli

WORKDIR /var/www/html

COPY . /var/www/html/

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN a2enmod rewrite

EXPOSE 80

# docker system prune --all --volumes

# docker build -t revendamais_backend_php .

# docker run -d -p 8073:80 revendamais_backend_php