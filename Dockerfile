FROM php:7.1.9-apache

RUN \
    apt-get update && apt-get install -y \
        libldap2-dev \
        libssl-dev \
        libmcrypt-dev \
        git \
        zlib1g-dev \
        moreutils \
        libpng-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && pecl install mongodb \
    && echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/ext-mongodb.ini \
    && docker-php-ext-install mcrypt pdo_mysql zip gd

RUN curl -sS https://getcomposer.org/installer | php \
        && mv composer.phar /usr/local/bin/ \
        && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

RUN curl -fsSL http://stedolan.github.io/jq/download/linux64/jq -o /usr/bin/jq \
    && chmod +x /usr/bin/jq

RUN php /usr/local/bin/composer create-project --no-install --no-scripts laravel/laravel /var/www/html
RUN cd /var/www/html \
    && jq '.repositories = [ { "type": "path", "url": "../laravel-reports" } ]' composer.json | sponge composer.json \
    && jq '.extra += { "merge-plugin": { "include": [ "vendor/hfletcher/laravel-reports/composer.json" ] } }' composer.json | sponge composer.json

RUN php /usr/local/bin/composer require --no-update wikimedia/composer-merge-plugin
RUN php /usr/local/bin/composer install --prefer-dist


# change doc root
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN a2enmod rewrite

# Change uid and gid of apache to docker user uid/gid
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

RUN chown -R www-data: /var/www/html/storage

WORKDIR /var/www/html
