# Infrastructure Configuration - DevOps

## Server Architecture for Condominios-Vzla

### 1. Recommended Stack
```
Web Server: Nginx (reverse proxy) + PHP-FPM 8.5
Database: MySQL 8.0
Cache: Redis (session, cache, queue)
Queue: Redis or Database
Storage: Local or S3-compatible
SSL: Let's Encrypt (Certbot)
```

### 2. Nginx Configuration
```nginx
server {
    listen 80;
    server_name condominios.example.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name condominios.example.com;
    root /var/www/condominios-vzla/public;

    ssl_certificate /etc/letsencrypt/live/condominios.example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/condominios.example.com/privkey.pem;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.5-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location /admins/ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
```

### 3. PHP-FPM Configuration
```ini
[www]
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500

php_value[upload_max_filesize] = 10M
php_value[post_max_size] = 10M
php_value[max_execution_time] = 60
php_value[memory_limit] = 256M
```

### 4. MySQL Configuration
```ini
[mysqld]
max_connections = 200
innodb_buffer_pool_size = 1G
query_cache_size = 64M
tmp_table_size = 64M
max_heap_table_size = 64M
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow.log
long_query_time = 2
```

### 5. File Permissions
```bash
# After deployment
chown -R www-data:www-data /var/www/condominios-vzla
chmod -R 755 /var/www/condominios-vzla
chmod -R 775 /var/www/condominios-vzla/storage
chmod -R 775 /var/www/condominios-vzla/bootstrap/cache
```

### 6. Cron Jobs
```bash
# Laravel Scheduler
* * * * * cd /var/www/condominios-vzla && php artisan schedule:run >> /dev/null 2>&1

# Log rotation
0 0 * * * find /var/www/condominios-vzla/storage/logs -name "*.log" -mtime +7 -delete

# Telescope pruning
0 2 * * * cd /var/www/condominios-vzla && php artisan telescope:prune --hours=48
```

### 7. Backup Strategy
```bash
#!/bin/bash
# Database backup
mysqldump -u root -p condominiums_vzla > /backups/db_$(date +%Y%m%d).sql

# File backup
tar -czf /back/files_$(date +%Y%m%d).tar.gz \
    /var/www/condominios-vzla/storage/app/public

# Keep last 30 days
find /backups -name "*.sql" -mtime +30 -delete
find /backups -name "*.tar.gz" -mtime +30 -delete
```

### 8. Environment Variables (Production)
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://condominios.example.com
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=condominiums_vzla
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```
