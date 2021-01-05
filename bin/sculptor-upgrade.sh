cd /var/www/html
sudo dep deploy
sudo dep deploy:migrate
sudo sculptor queue:restart
sudo sculptor system:daemons reload web
