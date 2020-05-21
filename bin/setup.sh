export DEBIAN_FRONTEND=noninteractive
add-apt-repository -y ppa:ondrej/php
apt-get update
apt-get -y dist-upgrade

apt-get -y install php7.4-fpm
apt-get -y install php7.4-common
apt-get -y install php7.4-mbstring
apt-get -y install php7.4-mysql
apt-get -y install php7.4-xml
apt-get -y install php7.4-zip
apt-get -y install php7.4-bcmath
apt-get -y install php7.4-imagick

update-alternatives --set php /usr/bin/php7.4

# install compose
apt-get -y install composer

# install packages (need only in dev)
composer install
