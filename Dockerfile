FROM php:7.4

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN apt-get update && apt-get -y install \
    mariadb-client \
    apt-transport-https \
    git \
    zlib1g-dev \
    zip \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libsodium-dev \
    libicu-dev \
    libxml2-dev \
    libxslt-dev \
    netcat

RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-install xsl
RUN docker-php-ext-install soap
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install sodium
RUN docker-php-ext-install zip
RUN docker-php-ext-install intl
RUN docker-php-ext-install sockets

COPY tools /tools
COPY entrypoint.sh /entrypoint.sh
RUN chmod 755 /entrypoint.sh

ENTRYPOINT ["bash", "/entrypoint.sh"]