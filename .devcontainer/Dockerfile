FROM php:8.2-apache
ENV NODE_VERSION=16.16.0
ENV YARN_VERSION=1.22.18

# install rust
RUN curl https://sh.rustup.rs -sSf | bash -s -- -y
ENV PATH="/root/.cargo/bin:${PATH}"

# install php dependencies
RUN apt-get update && apt-get upgrade -y && apt-get install -y net-tools git libzip-dev libpng-dev zsh unzip build-essential libcairo2-dev libpango1.0-dev libjpeg-dev libgif-dev librsvg2-dev python3 libxml2-dev
#openssl libssl-dev pkg-config clang lld 
RUN docker-php-ext-install pdo pdo_mysql sockets zip gd soap
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

# install node
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.2/install.sh | bash
ENV NVM_DIR=/root/.nvm
RUN . "$NVM_DIR/nvm.sh" && nvm install ${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm use v${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm alias default v${NODE_VERSION}
ENV PATH="/root/.nvm/versions/node/v${NODE_VERSION}/bin/:${PATH}"

# install yarn
RUN npm i -g yarn@${YARN_VERSION}

# install composer
RUN curl -sSL https://getcomposer.org/installer | php \
    && chmod +x composer.phar \
    && mv composer.phar /usr/local/bin/composer

# install starship
RUN sh -c "$(curl -fsSL https://starship.rs/install.sh)" "" -y
RUN echo "$(starship init zsh)" >> ~/.zshrc

# install deno
# note that deno does not support Linux ARM64 yet
# see: https://github.com/denoland/deno/issues/1846
RUN curl -fsSL https://deno.land/install.sh | sh
RUN echo "export DENO_INSTALL=\"/root/.deno\"" >> ~/.zshrc
RUN echo "export PATH=\"\$DENO_INSTALL/bin:\$PATH\"" >> ~/.zshrc

# install commitizen and envconf
RUN npm i -g commitizen cz-conventional-changelog
RUN echo '{ "path": "cz-conventional-changelog" }' > ~/.czrc
RUN /root/.deno/bin/deno install --allow-read --allow-write --allow-env --name envconf https://deno.land/x/envconf@0.0.4/src/mod.ts

RUN a2enmod rewrite
COPY config/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN echo 'Listen 8081\n' > /etc/apache2/ports.conf

# Get Rust
RUN curl https://sh.rustup.rs -sSf | bash -s -- -y