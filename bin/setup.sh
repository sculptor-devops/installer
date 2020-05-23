echo "Preparing the system..."

export DEBIAN_FRONTEND=noninteractive
add-apt-repository -y ppa:ondrej/php >> installer.log 2>&1
apt-get update >> installer.log 2>&1
apt-get -y dist-upgrade >> installer.log 2>&1

apt-get -y install php7.4-fpm php7.4-common php7.4-mbstring php7.4-mysql php7.4-xml php7.4-zip php7.4-bcmath php7.4-imagick >> installer.log 2>&1

update-alternatives --set php /usr/bin/php7.4 >> installer.log 2>&1

wget -O installer https://github.com/sculptor-devops/installer/raw/master/builds/installer >> installer.log 2>&1
chmod +x installer >> installer.log 2>&1

echo "Running installer..."
./installer run
