#!/bin/sh

yum -y upgrade

IP=$(curl -s https://api.ipify.org?format=text)
RUN="docker run -d -p 80:80 -p 443:443 -v /etc/certbot:/etc/letsencrypt/live"

{dns}

rm -rf ~/docker
mkdir -p ~/docker /etc/certbot

base64 -d << EOF > ~/docker/update.zip
{update}
EOF

cd ~/docker
unzip update.zip
docker stop $(docker ps -q)
docker build -t img --memory-swap -1 .
$RUN img

CONTAINER=$(docker ps -q)
docker exec -t $CONTAINER ~/ssl/letsencrypt/letsencrypt-auto --staging --apache --noninteractive --agree-tos --email=admin@{domain} -d www.{domain}
docker commit $CONTAINER img2

echo "$RUN img2" >> /etc/rc.local;
{cron}

echo alias debug=\"docker exec -i -t \\\$\(docker ps -q\) /bin/bash\" > ~/.bash_profile
echo alias start=\"$RUN img2\" >> ~/.bash_profile
echo alias stop=\"docker stop \\\$\(docker ps -q\) \" >> ~/.bash_profile

