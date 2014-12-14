#!/bin/bash

# inital working directory is '/home/vagrant'

TMPL_DIR=/vagrant/vagrant

##########################################
# install packages
##########################################

sudo rpm -Uvh http://dl.fedoraproject.org/pub/epel/6/x86_64/epel-release-6-8.noarch.rpm
sudo rpm -Uvh http://rpms.famillecollet.com/enterprise/remi-release-6.rpm

sudo yum -y install httpd
sudo chkconfig httpd on

sudo yum install -y --enablerepo=remi mysql-server
sudo chkconfig mysqld on

sudo yum install -y --enablerepo=remi --enablerepo=remi-php55 \
         php php-mbstring php-mcrypt php-pdo php-mysqlnd php-opcache

sudo service mysqld start

sudo cp -f $TMPL_DIR/00_httpd_port_8080.conf /etc/httpd/conf.d/port_8080.conf

mysql -u root < $TMPL_DIR/00_mysql_set_password.sql

##########################################
# setup phpMyAdmin
##########################################

sudo yum install -y --enablerepo=remi --enablerepo=remi-php55 phpMyAdmin

sudo mkdir /usr/share/phpMyAdmin/config/
sudo cp $TMPL_DIR/01_phpMyAdmin_config.inc.php /usr/share/phpMyAdmin/config/config.inc.php

##########################################
# setup piwik
##########################################

sudo curl -L -O http://builds.piwik.org/piwik.zip > /dev/null 2>&1
sudo unzip piwik.zip piwik/\* > /dev/null 2>&1
sudo rm -f piwik.zip

sudo mv piwik /usr/share/piwik

sudo cp -f $TMPL_DIR/02_piwik_httpd.conf /etc/httpd/conf.d/piwik.conf

sudo mkdir -m 0755 -p /usr/share/piwik/tmp/{assets,cache,logs,tcpdf,templates_c}
sudo chown -R apache:apache /usr/share/piwik
sudo chmod -R 0755 /usr/share/piwik/tmp

mysql -u root --password=root < $TMPL_DIR/02_piwik_create_database.sql

sudo cp -f $TMPL_DIR/02_piwik_config.ini.php /usr/share/piwik/config/config.ini.php

##########################################
# setup
##########################################

sudo cp -pf "/usr/share/zoneinfo/Asia/Tokyo" "/etc/localtime"

pushd /vagrant

mysql -u root --password=root < $TMPL_DIR/03_site_create_database.sql

php composer.phar update

rm -f fuel/app/config/development/migrations.php
php oil r migrate --all

sudo cp -f $TMPL_DIR/03_site_httpd.conf /etc/httpd/conf.d/site.conf

popd

sudo service httpd start
