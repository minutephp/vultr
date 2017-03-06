#!/usr/bin/env bash

mkdir ~/ssl
cd ~/ssl
git clone https://github.com/letsencrypt/letsencrypt
cd letsencrypt
sudo chmod g+x letsencrypt-auto

