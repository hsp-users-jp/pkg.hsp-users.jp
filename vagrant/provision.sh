#!/bin/bash

# inital working directory is '/home/vagrant'

##########################################
# install packages
##########################################

sudo rpm -Uvh http://dl.fedoraproject.org/pub/epel/6/x86_64/epel-release-6-8.noarch.rpm
sudo rpm -Uvh http://rpms.famillecollet.com/enterprise/remi-release-6.rpm

sudo yum -y install httpd
sudo chkconfig httpd on

sudo yum -y install mysql-server
sudo chkconfig mysqld on

sudo yum -y install --enablerepo=remi --enablerepo=remi-php55 \
         php php-mbstring php-mcrypt php-pdo php-mysqlnd php-opcache

sudo service mysqld start
sudo service httpd start

##########################################
# create public directory for web page
##########################################

if [ -L /var/www/html ]
  then sudo rm -f  /var/www/html
  else sudo rm -rf /var/www/html
fi
sudo ln -sf /vagrant/public /var/www/html

##########################################
# setup
##########################################

sudo cp -pf "/usr/share/zoneinfo/Asia/Tokyo" "/etc/localtime"

pushd /vagrant

php composer.phar update

mysql -u root                 < vagrant/00_set_password.sql
mysql -u root --password=root < vagrant/01_create_database.sql

rm -f fuel/app/config/development/migrations.php
php oil r migrate --all

#sudo chmod -R a+rw /vagrant/fuel/app/{logs,tmp,cache}
#sudo chown -R root:root /vagrant/fuel/app/{logs,tmp,cache}
#sudo chmod -R a+rw /vagrant/fuel/app/{logs,tmp,cache}

popd
