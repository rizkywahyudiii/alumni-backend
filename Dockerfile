FROM php:8.2-apache

# 1. Install System Dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Install Extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql zip gd bcmath

# 3. Aktifkan mod_rewrite (Wajib)
RUN a2enmod rewrite

# --- FIX CRASH MPM APACHE ---
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load \
    && a2enmod mpm_prefork

# 4. Config Document Root & IZIN AKSES (FIX UTAMA)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Ganti root folder di config default
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 👇 INI DIA: Paksa Apache baca .htaccess Laravel
RUN echo '<Directory /var/www/html/public>' >> /etc/apache2/apache2.conf && \
    echo '    Options Indexes FollowSymLinks' >> /etc/apache2/apache2.conf && \
    echo '    AllowOverride All' >> /etc/apache2/apache2.conf && \
    echo '    Require all granted' >> /etc/apache2/apache2.conf && \
    echo '</Directory>' >> /etc/apache2/apache2.conf

# 5. Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 6. Working Directory
WORKDIR /var/www/html

# 7. Copy Files
COPY . .

# 8. Install Dependencies
RUN composer install --no-dev --optimize-autoloader

# 9. Permissions
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# 10. Expose
EXPOSE 80
