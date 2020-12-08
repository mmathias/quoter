FROM php:fpm-alpine
COPY wait-for-it.sh /usr/bin/wait-for-it
RUN chmod +x /usr/bin/wait-for-it

RUN apk --update --no-cache add git bash supervisor
RUN mkdir -p /var/log/supervisor

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

COPY messenger-worker.conf /etc/supervisor.d/messenger-worker.conf
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor.d/messenger-worker.conf"]
