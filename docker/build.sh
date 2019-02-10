#!/usr/bin/env bash

project_name=`basename $(dirname "$PWD")`
project_path=$(dirname "$PWD")
data_path=$HOME/data/${project_name}
#删除镜像
docker rmi -f ${project_name}:dev
#构建镜像
docker build -t ${project_name}:dev .
#当局域网ip改变，xdebug无效（无效后，需要重新run）
xdebug_remote_host=`ifconfig -a|grep inet|grep -v 127.0.0.1|grep -v inet6|awk '{print $2}'|tr -d "addr:"`
#删除容器
docker rm -f ${project_name}
#运行容器
docker run -d --restart=on-failure:10 -p 7070:80 -p 7071:81 -p 7072:82  -v ${project_path}:/var/www/html -v ${data_path}:/data --link=mysql --name ${project_name} ${project_name}:dev /start.sh ${xdebug_remote_host}
#访问
curl http://localhost:7070
