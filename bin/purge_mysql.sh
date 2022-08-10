snap remove certbot
rm /usr/bin/certbot

cat /etc/os-release|grep 'VERSION_ID="22.04"' >> /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "22"
    dpkg --purge mysql-client mysql-server mysql-common mysql-client-5.7 mysql-server-5.7 mysql-client-core-5.7 mysql-server-core-5.7
    rm -rf /var/lib/mysql
    exit 0
fi

cat /etc/os-release|grep 'VERSION_ID="20.04"' >> /dev/null 2>&1
if [ $? -eq 0 ]; then
        echo "20"
    dpkg --purge mysql-client mysql-server mysql-common mysql-client-5.7 mysql-server-5.7 mysql-client-core-5.7 mysql-server-core-5.7
    rm -rf /var/lib/mysql
    exit 0
fi

cat /etc/os-release|grep 'VERSION_ID="18.04"' >> /dev/null 2>&1
if [ $? -eq 0 ]; then
   echo "18"
   dpkg --purge mysql-client mysql-server mysql-common mysql-client-8.0 mysql-server-8.0 mysql-client-core-8.0 mysql-server-core-8.0
   rm -rf /var/lib/mysql
   exit 0
fi
