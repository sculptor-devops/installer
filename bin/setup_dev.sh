export DEBIAN_FRONTEND=noninteractive
add-apt-repository -y ppa:ondrej/php

apt-get update
apt-get -y dist-upgrade

apt-get -y install php8.0-fpm
apt-get -y install php8.0-common
apt-get -y install php8.0-mbstring
apt-get -y install php8.0-mysql
apt-get -y install php8.0-xml
apt-get -y install php8.0-zip
apt-get -y install php8.0-bcmath
apt-get -y install php8.0-imagick
apt-get -y install php8.0-sqlite3
apt-get -y install php8.0-intl
apt-get -y install php8.0-redis
apt-get -y install php8.0-curl
update-alternatives --set php /usr/bin/php8.0

# install compose
apt-get -y install composer
