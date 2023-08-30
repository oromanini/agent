FROM newdeveloper/apache-php-composer:latest
WORKDIR var/www/html
COPY . /var/www/html
RUN chmod -R 777 /var/www/html/storage
RUN ln -s public html
RUN pecl install -o -f redis \
&&  rm -rf /tmp/pear \
&&  docker-php-ext-enable redis
