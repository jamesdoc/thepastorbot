sudo apt-get install -y build-essential software-properties-common python-software-properties
sudo add-apt-repository -y ppa:ondrej/php5-5.6
sudo apt-get update

sudo apt-get install -y --force-yes php5-gd apache2 php5 php5-curl

# Enable modrewrite because we can
a2enmod rewrite
sed -i '/AllowOverride None/c AllowOverride All' /etc/apache2/sites-available/default

service apache2 restart

echo 'And away we go...'
