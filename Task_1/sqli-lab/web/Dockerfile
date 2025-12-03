FROM php:7.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    gnupg2 \
    curl \
    wget \
    unixodbc \
    unixodbc-dev \
    libpq-dev \
    libsqlite3-dev \
    && rm -rf /var/lib/apt/lists/*

# Install Microsoft ODBC Driver for SQL Server
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
    && curl https://packages.microsoft.com/config/debian/10/prod.list > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y msodbcsql17 mssql-tools \
    && echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> ~/.bashrc

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql pgsql pdo_pgsql

# Install SQLite3 extension
RUN docker-php-ext-install pdo_sqlite

# Install sqlsrv and pdo_sqlsrv extensions (PHP 7.4-compatible release)
RUN pecl channel-update pecl.php.net \
    && printf "\n" | pecl install sqlsrv-5.10.1 \
    && printf "\n" | pecl install pdo_sqlsrv-5.10.1 \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Create uploads directory
RUN mkdir -p /var/www/uploads && chmod 755 /var/www/uploads

# Copy application files
COPY . /var/www/html/

# Copy Apache configuration
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chown -R www-data:www-data /var/www/uploads 
    # for RCE vector
    
EXPOSE 80
