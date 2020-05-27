echo "Preparing Sculptor installation..."

export DEBIAN_FRONTEND=noninteractive
add-apt-repository -y ppa:ondrej/php >> installer.log 2>&1
if [ $? -ne 0 ]; then
    echo "Apt repository addition failed (see installer.log for details)"
    exit 1
fi

apt-get update >> installer.log 2>&1
if [ $? -ne 0 ]; then
    echo "Apt update failed (see installer.log for details)"
    exit 1
fi

echo "Upgrading..."
apt-get -y dist-upgrade >> installer.log 2>&1
if [ $? -ne 0 ]; then
    echo "Apt upgrade failed (see installer.log for details)"
    exit 1
fi

echo "Installi base package..."
apt-get -y install php7.4-fpm php7.4-common php7.4-mbstring php7.4-mysql php7.4-xml php7.4-zip php7.4-bcmath php7.4-imagick >> installer.log 2>&1
if [ $? -ne 0 ]; then
    echo "Apt install php failed (see installer.log for details)"
    exit 1
fi

update-alternatives --set php /usr/bin/php7.4 >> installer.log 2>&1
if [ $? -ne 0 ]; then
    echo "Apt update alternatives failed (see installer.log for details)"
    exit 1
fi

echo "Downloading installer..."
wget -O installer https://github.com/sculptor-devops/installer/releases/latest/download/installer >> installer.log 2>&1
if [ $? -ne 0 ]; then
    echo "Installer download failed (see installer.log for details)"
    exit 1
fi

chmod +x installer >> installer.log 2>&1
if [ $? -ne 0 ]; then
    echo "Chmod installer failed (see installer.log for details)"
    exit 1
fi

echo "Running installer..."
./installer run
if [ $? -ne 0 ]; then
    echo "Installer run failed (see installer.log for details)"
    exit 1
fi

