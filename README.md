# Demo
https://www.youtube.com/playlist?list=PLuzKPfv02Rx1uXPIOov5o74JG6QUtPvyG


# Server Site

## Change Base URL Server
application->config->config.php
## Change re-capcha key
application->config->constants.php
## Config database
application->config->database.php
## Config mail server
application->controllers->api->BaseController.php


# Client Site

## Change API URL
web->app.js
## Change API schedule
web->templates->layout->sidebar.html
## Change re-capche key client
web->templates->index.html
## Change http to https
videocall.js -> openVideoCall
video-controller.js -> openInvitePopup

# Deploy server
```
git init
git remote add origin https://tan_ozu@gitlab.com/tan_ozu/Khoa_Luan_TN.git
git pull origin master
```

# Codeigniter in ubuntu 14.04
```
sudo a2enmod rewrite
sudo service apache2 restart
sudo nano /etc/apache2/apache2.conf
AccessFileName .htaccess (uncomment this line if it is commented)
Change AllowOverride to All - note my root is var/www/html yours might be just var/www
<Directory /var/www/html/>
Options Indexes FollowSymLinks
AllowOverride All
Require all granted
```

# Set up Cron-job
(minutes) (hours) (day of month) (month) (day of week) (command)
0 0 * * * curl --request GET 'https://viettops.net/autobackup/run.php'
