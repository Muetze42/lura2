# Installing Prompts Requirements

Tested with Ubuntu 22, WSL2 & Windows 11.

## Essential

```shell
sudo apt update && sudo apt-upgrade -y
```

```shell
sudo apt install wget curl vim -y
```

## PHP

### PHP 7.4

```shell
sudo apt-get install php7.4-fpm php7.4-common php7.4-dev php7.4-curl php7.4-gd php7.4-json php7.4-mysql php7.4-odbc php7.4-pgsql php7.4-pspell php7.4-readline php7.4-sqlite3 php7.4-tidy php7.4-xml php7.4-xmlrpc php7.4-bcmath php7.4-bz2 php7.4-intl php7.4-mbstring php7.4-phpdbg php7.4-soap php7.4-zip php-imagick php-redis php-memcached
```

### PHP 8.0

```shell
sudo apt-get install php8.0-fpm php8.0-common php8.0-dev php8.0-curl php8.0-gd php8.0-mysql php8.0-odbc php8.0-pgsql php8.0-pspell php8.0-readline php8.0-sqlite3 php8.0-tidy php8.0-xml php8.0-xmlrpc php8.0-bcmath php8.0-bz2 php8.0-intl php8.0-mbstring php8.0-phpdbg php8.0-soap php8.0-zip php-imagick php-redis php-memcached
```

### PHP 8.1

```shell
sudo apt-get install php8.1-fpm php8.1-common php8.1-dev php8.1-curl php8.1-gd php8.1-mysql php8.1-odbc php8.1-pgsql php8.1-pspell php8.1-readline php8.1-sqlite3 php8.1-tidy php8.1-xml php8.1-xmlrpc php8.1-bcmath php8.1-bz2 php8.1-intl php8.1-mbstring php8.1-phpdbg php8.1-soap php8.1-zip php-imagick php-redis php-memcached
```

### PHP 8.2

```shell
sudo apt-get install php8.2-fpm php8.2-common php8.2-dev php8.2-curl php8.2-gd php8.2-mysql php8.2-odbc php8.2-pgsql php8.2-pspell php8.2-readline php8.2-sqlite3 php8.2-tidy php8.2-xml php8.2-xmlrpc php8.2-bcmath php8.2-bz2 php8.2-intl php8.2-mbstring php8.2-phpdbg php8.2-soap php8.2-zip php-imagick php-redis php-memcached
```

### PHP 8.3

```shell
sudo apt-get install php8.3-fpm php8.3-common php8.3-dev php8.3-curl php8.3-gd php8.3-mysql php8.3-odbc php8.3-pgsql php8.3-pspell php8.3-readline php8.3-sqlite3 php8.3-tidy php8.3-xml php8.3-xmlrpc php8.3-bcmath php8.3-bz2 php8.3-intl php8.3-mbstring php8.3-phpdbg php8.3-soap php8.3-zip php-imagick php-redis php-memcached
```

## Change CLI PHP-Version

```shell
sudo update-alternatives --config php
```

## Recommended

```shell
sudo apt install unzip
```

```shell
sudo apt install 7-zip
```

## Composer

```shell
sudo apt update
```

```shell
cd ~
```

```shell
curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
```

```shell
HASH=`curl -sS https://composer.github.io/installer.sig`
```

```shell
php -r "if (hash_file('SHA384', '/tmp/composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
```

```shell
sudo php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
```

## References

* https://gist.github.com/patrickmaciel/f9530cc9271c80b4609d03b5e4d716d9
* https://www.digitalocean.com/community/tutorials/how-to-install-composer-on-ubuntu-20-04-quickstart
