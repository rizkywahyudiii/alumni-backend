# Gunakan image PHP dengan Apache agar langsung jalan tanpa Nginx tambahan
FROM php:8.2-apache

# 1. Install dependency sistem operasi yang dibutuhkan
# libzip-dev SANGAT PENTING untuk extension zip
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Install Ekstensi PHP
# Tambahkan 'zip' di sini untuk memperbaiki error kamu
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd zip bcmath

# 3. Aktifkan mod_rewrite Apache (Wajib untuk routing Laravel)
RUN a2enmod rewrite

# 4. Ubah Document Root Apache ke folder /public Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 5. Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 6. Set Working Directory
WORKDIR /var/www/html

# 7. Copy semua file project ke dalam container
COPY . .

# 8. Install dependensi Laravel via Composer
# --no-dev: agar library testing tidak ikut diinstall (lebih ringan)
# --ignore-platform-reqs: opsi darurat jika ada ketidakcocokan versi php minor
RUN composer install --no-dev --optimize-autoloader

# 9. Atur hak akses folder storage dan bootstrap/cache agar bisa ditulisi
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 10. Expose port (Railway akan menginjeksi PORT environment variable)
EXPOSE 80
