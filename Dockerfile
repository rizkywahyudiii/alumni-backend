# Gunakan PHP 8.2 dengan Apache (Sesuai requirement composer.json)
FROM php:8.2-apache

# 1. Install System Dependencies
# libzip-dev: Wajib untuk Maatwebsite Excel
# libpng/jpeg/freetype: Wajib untuk GD (Gambar/Excel)
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

# 2. Install & Configure PHP Extensions
# Mengaktifkan GD, ZIP (Excel), PDO_MYSQL (Database), dan BCMath (Laravel)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql zip gd bcmath

# 3. Aktifkan mod_rewrite Apache (Agar routing Laravel jalan)
RUN a2enmod rewrite

# --- FIX CRASH MPM APACHE ---
# Mencegah error "More than one MPM loaded"
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load \
    && a2enmod mpm_prefork

# 4. Ubah Document Root ke folder /public
# Ini sesuai struktur folder standar Laravel yang kamu kirim
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 5. Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 6. Set Working Directory
WORKDIR /var/www/html

# 7. Copy Project Files
COPY . .

# 8. Install Dependencies Laravel
# Kita skip dev dependencies biar ringan di production
RUN composer install --no-dev --optimize-autoloader

# 9. Permission Folder
# Wajib agar Laravel bisa nulis log dan session
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# 10. Expose Port 80
EXPOSE 80
