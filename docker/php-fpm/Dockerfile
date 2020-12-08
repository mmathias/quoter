FROM php:fpm-alpine
COPY wait-for-it.sh /usr/bin/wait-for-it
RUN chmod +x /usr/bin/wait-for-it
RUN apk --update --no-cache add git
RUN docker-php-ext-install pdo_mysql
RUN apk add autoconf
RUN apk add gcc g++
RUN apk add --no-cache --update rabbitmq-c-dev
RUN apk add --no-cache --update --virtual .phpize-deps $PHPIZE_DEPS
RUN pecl install amqp
RUN docker-php-ext-enable amqp
COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
CMD composer install ; wait-for-it database:3306 -- php-fpm
EXPOSE 9000
