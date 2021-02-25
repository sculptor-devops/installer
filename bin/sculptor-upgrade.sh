cd /var/www/html
sudo dep deploy:unlock
sudo dep deploy
sudo dep deploy:migrate
sudo sculptor queue:restart
sudo sculptor system:daemons reload web

echo -n "Now version is "
sculptor system:info --version-only
