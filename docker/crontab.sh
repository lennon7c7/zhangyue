#!/bin/sh
env=$1

#创建文件夹
/files.sh

chown -Rf nginx.nginx /var/www/html
cd /var/www/html

if [ ! $env ]; then
php init --env=Production --overwrite=y
else
# 有参数就是开发环境
php init --env=Development --overwrite=y
fi

tee /etc/crontabs/root <<- EOF
# do daily/weekly/monthly maintenance
# min   hour    day     month   weekday command

# 每1分钟根据推送时间推送
*/1       *       *       *      *      /var/www/html/yii notice/send

# 系统默认
*/15    *       *       *       *       run-parts /etc/periodic/15min
0       *       *       *       *       run-parts /etc/periodic/hourly
0       2       *       *       *       run-parts /etc/periodic/daily
0       3       *       *       6       run-parts /etc/periodic/weekly
0       5       1       *       *       run-parts /etc/periodic/monthly
EOF

crond -f
