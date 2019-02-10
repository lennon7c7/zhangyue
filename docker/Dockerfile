FROM alpine:3.8

MAINTAINER lennon <lennonandjune@gmail.com>

RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.ustc.edu.cn/g' /etc/apk/repositories && \
apk update && apk upgrade && \
apk add --no-cache \
certbot \
curl \
git \
htop \
nginx \
openssl \
php7 \
php7-bcmath \
php7-common \
php7-ctype \
php7-curl \
php7-dom \
php7-exif \
php7-fpm \
php7-gd \
php7-iconv \
php7-fileinfo \
php7-intl \
php7-json \
php7-mysqli \
php7-mysqlnd \
php7-mbstring \
php7-mcrypt \
php7-openssl \
php7-pdo \
php7-pdo_mysql \
php7-pdo_pgsql \
php7-pgsql \
php7-phar \
php7-redis \
php7-session \
php7-simplexml \
php7-soap \
php7-sqlite3 \
php7-tokenizer \
php7-xdebug \
php7-xml \
php7-xmlrpc \
php7-xmlwriter \
php7-xsl \
php7-zlib \
php7-zip \
supervisor \
tzdata \
vim \
wget && \
cp /usr/share/zoneinfo/Asia/Shanghai /etc/localtime && \
apk del tzdata

RUN mkdir -p /etc/nginx && \
mkdir -p /var/www/app && \
mkdir -p /run/nginx && \
mkdir -p /var/log/supervisor

# copy composer
ADD composer.phar /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer && \
composer config -g repo.packagist composer https://packagist.phpcomposer.com && \
composer global require "fxp/composer-asset-plugin:^1.4.1" --no-plugins

# copy setting
ADD etc /etc
ADD errors /var/www/errors

ADD crontab.sh /crontab.sh
ADD files.sh /files.sh
ADD start.sh /start.sh
RUN chmod 755 /crontab.sh && dos2unix /crontab.sh && chmod 755 /files.sh && dos2unix /files.sh && chmod 755 /start.sh && dos2unix /start.sh

VOLUME /var/www/html

EXPOSE 80 81 82 83 84 85

CMD ["/start.sh"]
