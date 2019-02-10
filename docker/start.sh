#!/bin/sh
xdebug_remote_host=$1

#创建文件夹
/files.sh

chown -Rf nginx.nginx /var/www/html
cd /var/www/html

if [ ! $xdebug_remote_host ]; then

php init --env=Production --overwrite=y
else # 有参数就是开发环境

##开启 xdebug 模式
tee /etc/php7/conf.d/xdebug.ini <<-'EOF'
zend_extension = xdebug.so
xdebug.remote_enable = 1
xdebug.remote_autostart = 1
;xdebug.remote_connect_back = 1
xdebug.remote_connect_back = 0
xdebug.remote_port = 9000
xdebug.remote_handler = dbgp
xdebug.idekey = docker
EOF
echo "xdebug.remote_host = $xdebug_remote_host" >> /etc/php7/conf.d/xdebug.ini

php init --env=Development --overwrite=y
fi

# Start supervisord and services
/usr/bin/supervisord -n -c /etc/supervisord.conf
