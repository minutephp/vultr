#!/usr/bin/env bash

apt-get -y install libexpat1-dev libpython-dev libpython2.7 libpython2.7-dev python-pip-whl python2.7-dev \
                       python3-pkg-resources python3-virtualenv augeas-lenses libaugeas0 libffi-dev python-dev \
                       python-virtualenv virtualenv
mkdir ~/ssl
cd ~/ssl
git clone https://github.com/letsencrypt/letsencrypt
cd letsencrypt
chmod g+x letsencrypt-auto
