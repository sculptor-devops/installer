cat /etc/os-release|grep 'VERSION_ID="20.04"' >> /dev/null 2>&1
if [ $? -ne 0 ]; then
    dpkg --purge mysql-client mysql-server mysql-common mysql-client-5.7 mysql-server-5.7 mysql-client-core-5.7 mysql-server-core-5.7
else
   dpkg --purge mysql-client mysql-server mysql-common mysql-client-8.0 mysql-server-8.0 mysql-client-core-8.0 mysql-server-core-8.0
fi
