FROM newdeveloper/apache-php-composer:latest
WORKDIR var/www/html
COPY . /var/www/html
RUN chmod -R 777 /var/www/html/storage
RUN ln -s public html
RUN apt-get update && apt-get install -y libzip-dev libpng-dev
RUN apt-get update && apt-get install -y build-essential

