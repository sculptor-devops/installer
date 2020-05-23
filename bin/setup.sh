echo "Preparing the system..."

export DEBIAN_FRONTEND=noninteractive
add-apt-repository -y ppa:ondrej/php
apt-get update
apt-get -y dist-upgrade

apt-get -y install php7.4-fpm php7.4-common php7.4-mbstring php7.4-mysql php7.4-xml php7.4-zip php7.4-bcmath php7.4-imagick

update-alternatives --set php /usr/bin/php7.4

wget https://github.com/sculptor-devops/installer/raw/master/builds/installer
chmod +x installer
./installer run
