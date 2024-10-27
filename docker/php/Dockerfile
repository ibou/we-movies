FROM php:8.3-fpm


# add composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# add xdebug
RUN apt-get update && apt-get install -y git
# add zip
RUN apt-get update && apt-get install -y zip unzip
# add vim
RUN apt-get update && apt-get install -y vim
# add xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug
# Install symfony CLI (https://symfony.com/download)
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt install symfony-cli -y

# Clear apt cache to reduce image size
RUN rm -rf /var/cache/apt/archives /var/lib/apt/lists

ENTRYPOINT ["/usr/bin/symfony"]

# add group and user
RUN groupadd -g 1000 app
RUN useradd -u 1000 -ms /bin/bash -g app app

# change owner
RUN chown -R app:app /var/www/html

CMD bash -c "composer install"

WORKDIR /var/www/html

CMD ["php-fpm"]
