#!/bin/sh

#apt-get -y update && apt-get -y upgrade
#apt-get -y install curl docker.io unzip
#apt-get -y --purge remove && apt-get -y clean

#sed -i.bak 's/Type=notify/Type=simple/' /lib/systemd/system/docker.service
#service docker start

IP=$(curl -s https://api.ipify.org?format=text)
curl -H 'API-Key: %apiKey%' https://api.vultr.com/v1/dns/create_record --data 'domain=%domain%' --data 'name=*' --data 'type=A' --data "data=$IP"

#dd if=/dev/zero of=/mnt/swapfile bs=1M count=2048
#chown root:root /mnt/swapfile
#chmod 600 /mnt/swapfile
#mkswap /mnt/swapfile
#swapon /mnt/swapfile
#swapon -a

rm -rf ~/docker
mkdir -p ~/docker

base64 -d << EOF > ~/docker/update.zip
%update%
EOF

cd ~/docker
unzip update.zip
docker build -t img --memory-swap -1 .
docker run -d -p 80:80 -p 443:443 img
alias run="docker exec -i -t $(docker ps -q) /bin/bash"