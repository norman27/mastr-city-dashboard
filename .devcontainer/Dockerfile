FROM php:8.2-apache

# install php dependencies
RUN apt-get update && apt-get upgrade -y && apt-get install -y net-tools git libzip-dev libpng-dev zsh unzip build-essential libcairo2-dev libpango1.0-dev libjpeg-dev libgif-dev librsvg2-dev python3
RUN docker-php-ext-install pdo pdo_mysql sockets zip gd
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

# install composer
RUN curl -sSL https://getcomposer.org/installer | php \
    && chmod +x composer.phar \
    && mv composer.phar /usr/local/bin/composer

# install starship
RUN sh -c "$(curl -fsSL https://starship.rs/install.sh)" "" -y
RUN echo "eval \"$(starship init zsh)\"" >> ~/.zshrc

RUN a2enmod rewrite
COPY config/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN echo 'Listen 8081\n' > /etc/apache2/ports.conf

