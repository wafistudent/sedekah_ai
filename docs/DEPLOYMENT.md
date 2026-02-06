# WhatsApp Auto Message System - Deployment Guide

This comprehensive guide covers deploying the WhatsApp Auto Message System to production servers.

---

## 📋 Table of Contents

1. [Pre-Deployment Requirements](#pre-deployment-requirements)
2. [Server Setup](#server-setup)
3. [Application Deployment](#application-deployment)
4. [Queue Worker Setup](#queue-worker-setup)
5. [Cron Jobs Configuration](#cron-jobs-configuration)
6. [Environment Configuration](#environment-configuration)
7. [Database Migration](#database-migration)
8. [Asset Compilation](#asset-compilation)
9. [Security Hardening](#security-hardening)
10. [Performance Optimization](#performance-optimization)
11. [Monitoring & Logging](#monitoring--logging)
12. [Backup Strategy](#backup-strategy)
13. [Rollback Plan](#rollback-plan)
14. [Post-Deployment Checklist](#post-deployment-checklist)
15. [Troubleshooting](#troubleshooting)

---

## 1. Pre-Deployment Requirements

### 1.1 Server Requirements

**Minimum Specifications:**
- **OS:** Ubuntu 20.04+ / CentOS 8+ / Debian 11+
- **CPU:** 2 cores (4+ recommended)
- **RAM:** 2GB (4GB+ recommended)
- **Storage:** 20GB SSD (50GB+ recommended)
- **Network:** Static IP, open ports 80, 443

**Software Stack:**
- **PHP:** 8.1+
- **MySQL:** 8.0+ or MariaDB 10.5+
- **Nginx:** 1.18+ or Apache 2.4+
- **Node.js:** 18+ (for asset compilation)
- **Composer:** 2.5+
- **Supervisor:** 4.0+
- **Redis:** 6.0+ (optional, for caching)

### 1.2 WhatsApp API Access

- Valid API key from Waajo.id or your provider
- API URL: `https://api.waajo.id/go-omni-v2/public/wa`
- Test API connectivity before deployment

### 1.3 Domain & SSL

- Domain name pointed to server IP
- SSL certificate (Let's Encrypt recommended)
- Configured DNS records (A, CNAME, etc.)

---

## 2. Server Setup

### 2.1 Initial Server Configuration

```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Install basic utilities
sudo apt install -y curl wget git unzip software-properties-common

# Set timezone (adjust as needed)
sudo timedatectl set-timezone Asia/Jakarta
```

### 2.2 Install PHP 8.1+

```bash
# Add PHP repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP and required extensions
sudo apt install -y php8.1-fpm php8.1-cli php8.1-common \
    php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl \
    php8.1-zip php8.1-gd php8.1-bcmath php8.1-intl \
    php8.1-redis

# Verify installation
php -v
```

### 2.3 Install MySQL

```bash
# Install MySQL Server
sudo apt install -y mysql-server

# Secure MySQL installation
sudo mysql_secure_installation

# Login to MySQL
sudo mysql

# Create database and user
CREATE DATABASE sedekah_ai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'sedekah_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON sedekah_ai.* TO 'sedekah_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 2.4 Install Nginx

```bash
# Install Nginx
sudo apt install -y nginx

# Start and enable Nginx
sudo systemctl start nginx
sudo systemctl enable nginx

# Check status
sudo systemctl status nginx
```

### 2.5 Install Composer

```bash
# Download and install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Verify installation
composer --version
```

### 2.6 Install Node.js & NPM

```bash
# Install Node.js 18.x
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Verify installation
node -v
npm -v
```

### 2.7 Install Supervisor

```bash
# Install Supervisor (for queue workers)
sudo apt install -y supervisor

# Start and enable Supervisor
sudo systemctl start supervisor
sudo systemctl enable supervisor
```

---

## 3. Application Deployment

### 3.1 Create Application User

```bash
# Create user for running the application
sudo useradd -m -s /bin/bash sedekah
sudo passwd sedekah

# Add to www-data group
sudo usermod -aG www-data sedekah
```

### 3.2 Clone Repository

```bash
# Switch to application user
sudo su - sedekah

# Clone repository
cd /var/www/
git clone https://github.com/wafistudent/sedekah_ai.git
cd sedekah_ai

# Set correct branch
git checkout main  # or your production branch
```

### 3.3 Install Dependencies

```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies
npm ci --production
```

### 3.4 Set File Permissions

```bash
# Exit from sedekah user
exit

# Set ownership
sudo chown -R sedekah:www-data /var/www/sedekah_ai

# Set directory permissions
sudo find /var/www/sedekah_ai -type d -exec chmod 755 {} \;
sudo find /var/www/sedekah_ai -type f -exec chmod 644 {} \;

# Set storage and cache permissions
sudo chmod -R 775 /var/www/sedekah_ai/storage
sudo chmod -R 775 /var/www/sedekah_ai/bootstrap/cache
```

---

## 4. Queue Worker Setup

### 4.1 Create Supervisor Configuration

Create file: `/etc/supervisor/conf.d/sedekah-queue.conf`

```ini
[program:sedekah-queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/sedekah_ai/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=sedekah
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/sedekah_ai/storage/logs/queue-worker.log
stopwaitsecs=3600
```

**Explanation:**
- `numprocs=2`: Runs 2 worker processes
- `--sleep=3`: Sleeps 3 seconds when no jobs available
- `--tries=3`: Retries failed jobs up to 3 times
- `--max-time=3600`: Restarts worker after 1 hour (prevents memory leaks)

### 4.2 Start Queue Workers

```bash
# Reload Supervisor configuration
sudo supervisorctl reread
sudo supervisorctl update

# Start queue workers
sudo supervisorctl start sedekah-queue-worker:*

# Check status
sudo supervisorctl status sedekah-queue-worker:*
```

### 4.3 Manage Queue Workers

```bash
# Stop workers
sudo supervisorctl stop sedekah-queue-worker:*

# Restart workers
sudo supervisorctl restart sedekah-queue-worker:*

# View logs
sudo tail -f /var/www/sedekah_ai/storage/logs/queue-worker.log
```

---

## 5. Cron Jobs Configuration

### 5.1 Laravel Scheduler

The Laravel scheduler handles scheduled tasks like:
- Auto-retry failed messages
- Send scheduled messages
- Generate daily reports
- Clean old logs

```bash
# Edit crontab for sedekah user
sudo crontab -u sedekah -e

# Add this line
* * * * * cd /var/www/sedekah_ai && php artisan schedule:run >> /dev/null 2>&1
```

### 5.2 Verify Cron Setup

```bash
# List cron jobs
sudo crontab -u sedekah -l

# Check cron logs
sudo grep CRON /var/log/syslog | tail -20
```

---

## 6. Environment Configuration

### 6.1 Create .env File

```bash
cd /var/www/sedekah_ai
sudo cp .env.example .env
sudo nano .env
```

### 6.2 Configure Environment Variables

**Essential Variables:**

```bash
# Application
APP_NAME="Sedekah AI MLM"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sedekah_ai
DB_USERNAME=sedekah_user
DB_PASSWORD=YOUR_STRONG_PASSWORD_HERE

# Queue
QUEUE_CONNECTION=database

# WhatsApp API
WHATSAPP_API_URL=https://api.waajo.id/go-omni-v2/public/wa
WHATSAPP_API_KEY=your_actual_api_key_here

# Mail (for error notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# Cache (if using Redis)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 6.3 Generate Application Key

```bash
cd /var/www/sedekah_ai
php artisan key:generate
```

### 6.4 Cache Configuration

```bash
# Cache config for better performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 7. Database Migration

### 7.1 Run Migrations

```bash
cd /var/www/sedekah_ai

# Run migrations (PRODUCTION - be careful!)
php artisan migrate --force

# Seed initial data
php artisan db:seed --class=AdminSeeder --force
php artisan db:seed --class=WhatsappSeeder --force
```

### 7.2 Verify Database

```bash
# Login to MySQL
mysql -u sedekah_user -p sedekah_ai

# Check tables
SHOW TABLES;

# Check seeded data
SELECT COUNT(*) FROM whatsapp_templates;
SELECT COUNT(*) FROM whatsapp_settings;

EXIT;
```

---

## 8. Asset Compilation

### 8.1 Build Production Assets

```bash
cd /var/www/sedekah_ai

# Build optimized assets
npm run build

# Verify assets created
ls -lh public/build/
```

### 8.2 Optimize Autoloader

```bash
# Optimize Composer autoloader
composer dump-autoload --optimize --no-dev
```

---

## 9. Security Hardening

### 9.1 File Permissions

```bash
# Restrict .env file
sudo chmod 600 /var/www/sedekah_ai/.env
sudo chown sedekah:sedekah /var/www/sedekah_ai/.env

# Restrict sensitive files
sudo chmod 600 /var/www/sedekah_ai/config/*.php
```

### 9.2 Nginx Configuration

Create file: `/etc/nginx/sites-available/sedekah_ai`

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    
    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/sedekah_ai/public;

    index index.php index.html;

    # SSL Configuration (Let's Encrypt)
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_prefer_server_ciphers on;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' 'unsafe-inline' 'unsafe-eval' https: data:;" always;

    # Logging
    access_log /var/log/nginx/sedekah_ai_access.log;
    error_log /var/log/nginx/sedekah_ai_error.log;

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/json;

    # PHP-FPM Configuration
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny access to sensitive files
    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~ /\.env {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

### 9.3 Enable Nginx Site

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/sedekah_ai /etc/nginx/sites-enabled/

# Test configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

### 9.4 Setup SSL with Let's Encrypt

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtain SSL certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Test auto-renewal
sudo certbot renew --dry-run
```

### 9.5 Configure Firewall

```bash
# Install UFW (if not installed)
sudo apt install -y ufw

# Allow SSH, HTTP, HTTPS
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Enable firewall
sudo ufw enable

# Check status
sudo ufw status
```

---

## 10. Performance Optimization

### 10.1 PHP-FPM Tuning

Edit: `/etc/php/8.1/fpm/pool.d/www.conf`

```ini
; Process Manager
pm = dynamic
pm.max_children = 20
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 10
pm.max_requests = 500

; Resource Limits
php_admin_value[memory_limit] = 256M
php_admin_value[max_execution_time] = 300
php_admin_value[upload_max_filesize] = 10M
php_admin_value[post_max_size] = 10M
```

Restart PHP-FPM:
```bash
sudo systemctl restart php8.1-fpm
```

### 10.2 MySQL Optimization

Edit: `/etc/mysql/mysql.conf.d/mysqld.cnf`

```ini
[mysqld]
# InnoDB Settings
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT

# Query Cache (for MySQL 5.7, disabled in 8.0)
# query_cache_type = 1
# query_cache_size = 64M

# Connection Settings
max_connections = 200
connect_timeout = 10
wait_timeout = 600
```

Restart MySQL:
```bash
sudo systemctl restart mysql
```

### 10.3 Redis Cache (Optional)

```bash
# Install Redis
sudo apt install -y redis-server

# Configure Redis
sudo nano /etc/redis/redis.conf

# Set max memory
maxmemory 256mb
maxmemory-policy allkeys-lru

# Start Redis
sudo systemctl start redis-server
sudo systemctl enable redis-server
```

### 10.4 Laravel Optimizations

```bash
cd /var/www/sedekah_ai

# Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optimize autoloader
composer dump-autoload --optimize --classmap-authoritative
```

---

## 11. Monitoring & Logging

### 11.1 Application Logs

```bash
# Laravel log location
/var/www/sedekah_ai/storage/logs/laravel.log

# View live logs
tail -f /var/www/sedekah_ai/storage/logs/laravel.log

# Queue worker logs
tail -f /var/www/sedekah_ai/storage/logs/queue-worker.log
```

### 11.2 Nginx Logs

```bash
# Access logs
tail -f /var/log/nginx/sedekah_ai_access.log

# Error logs
tail -f /var/log/nginx/sedekah_ai_error.log
```

### 11.3 Logrotate Configuration

Create: `/etc/logrotate.d/sedekah_ai`

```
/var/www/sedekah_ai/storage/logs/*.log {
    daily
    rotate 14
    compress
    delaycompress
    notifempty
    create 0644 sedekah www-data
    sharedscripts
    postrotate
        php /var/www/sedekah_ai/artisan queue:restart
    endscript
}
```

### 11.4 Health Check Endpoint

Create a health check endpoint to monitor application status:

File: `routes/web.php`

```php
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'database' => DB::connection()->getDatabaseName(),
        'queue' => Queue::size(),
    ]);
});
```

Test:
```bash
curl https://yourdomain.com/health
```

---

## 12. Backup Strategy

### 12.1 Database Backup Script

Create: `/usr/local/bin/backup-sedekah-db.sh`

```bash
#!/bin/bash

# Configuration
BACKUP_DIR="/var/backups/sedekah_ai"
DB_NAME="sedekah_ai"
DB_USER="sedekah_user"
DB_PASS="YOUR_PASSWORD"
DATE=$(date +%Y%m%d_%H%M%S)
FILENAME="sedekah_ai_${DATE}.sql.gz"

# Create backup directory if not exists
mkdir -p $BACKUP_DIR

# Dump database
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/$FILENAME

# Delete backups older than 30 days
find $BACKUP_DIR -name "*.sql.gz" -mtime +30 -delete

echo "Database backup completed: $FILENAME"
```

Make executable:
```bash
sudo chmod +x /usr/local/bin/backup-sedekah-db.sh
```

### 12.2 Schedule Database Backups

```bash
# Add to crontab
sudo crontab -e

# Daily backup at 2 AM
0 2 * * * /usr/local/bin/backup-sedekah-db.sh >> /var/log/backup.log 2>&1
```

### 12.3 File System Backup

```bash
# Backup uploaded files and storage
tar -czf /var/backups/sedekah_ai/files_$(date +%Y%m%d).tar.gz \
    /var/www/sedekah_ai/storage/app/public \
    /var/www/sedekah_ai/.env

# Delete old file backups (older than 14 days)
find /var/backups/sedekah_ai -name "files_*.tar.gz" -mtime +14 -delete
```

### 12.4 Remote Backup (Recommended)

Consider using cloud storage for off-site backups:

```bash
# Install rclone
curl https://rclone.org/install.sh | sudo bash

# Configure rclone (follow prompts)
rclone config

# Sync backups to cloud
rclone sync /var/backups/sedekah_ai remote:sedekah-backups
```

---

## 13. Rollback Plan

### 13.1 Pre-Deployment Backup

Before deploying:

```bash
# 1. Backup database
mysqldump -u sedekah_user -p sedekah_ai > /tmp/pre-deploy-db-backup.sql

# 2. Backup current application
cd /var/www/
tar -czf sedekah_ai_pre_deploy_$(date +%Y%m%d).tar.gz sedekah_ai/

# 3. Note current Git commit
cd sedekah_ai
git log -1 --oneline > /tmp/current_commit.txt
```

### 13.2 Rollback Database

```bash
# Stop queue workers
sudo supervisorctl stop sedekah-queue-worker:*

# Restore database
mysql -u sedekah_user -p sedekah_ai < /tmp/pre-deploy-db-backup.sql

# Restart queue workers
sudo supervisorctl start sedekah-queue-worker:*
```

### 13.3 Rollback Application

```bash
# Stop queue workers
sudo supervisorctl stop sedekah-queue-worker:*

# Checkout previous commit
cd /var/www/sedekah_ai
git reset --hard PREVIOUS_COMMIT_HASH

# Reinstall dependencies
composer install --no-dev --optimize-autoloader
npm ci --production
npm run build

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Re-cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo supervisorctl restart sedekah-queue-worker:*
sudo systemctl reload php8.1-fpm
sudo systemctl reload nginx
```

---

## 14. Post-Deployment Checklist

### 14.1 Functional Testing

- [ ] Homepage loads correctly
- [ ] Admin login works
- [ ] Templates page accessible
- [ ] Create new template works
- [ ] Send test message works
- [ ] Logs page loads
- [ ] Settings page loads and saves

### 14.2 Queue Testing

- [ ] Queue workers running (check Supervisor)
- [ ] Test message queued successfully
- [ ] Queue worker processes job
- [ ] Check queue-worker.log for errors

### 14.3 Database Verification

- [ ] All tables created
- [ ] Seeders ran successfully
- [ ] Test data present (templates, settings)

### 14.4 Performance Testing

- [ ] Page load times < 2 seconds
- [ ] No PHP errors in logs
- [ ] No Nginx errors in logs
- [ ] Memory usage acceptable

### 14.5 Security Testing

- [ ] SSL certificate valid
- [ ] HTTPS redirect working
- [ ] .env file not accessible via web
- [ ] Firewall rules active

### 14.6 Monitoring Setup

- [ ] Application logs rotating
- [ ] Health check endpoint responding
- [ ] Backups scheduled and working
- [ ] Error notifications configured

---

## 15. Troubleshooting

### 15.1 Queue Workers Not Processing

**Symptoms:** Jobs stay in "pending" status

**Solutions:**

```bash
# Check worker status
sudo supervisorctl status sedekah-queue-worker:*

# If not running, start them
sudo supervisorctl start sedekah-queue-worker:*

# Check worker logs
tail -f /var/www/sedekah_ai/storage/logs/queue-worker.log

# Restart workers
sudo supervisorctl restart sedekah-queue-worker:*
```

### 15.2 WhatsApp Messages Not Sending

**Symptoms:** Messages fail with API error

**Check:**

1. API credentials in `.env`:
   ```bash
   grep WHATSAPP /var/www/sedekah_ai/.env
   ```

2. Test API connectivity:
   ```bash
   curl -X POST https://api.waajo.id/go-omni-v2/public/wa \
     -H "Authorization: Bearer YOUR_API_KEY" \
     -H "Content-Type: application/json" \
     -d '{"phone":"628123456789","message":"Test"}'
   ```

3. Check logs:
   ```bash
   tail -50 /var/www/sedekah_ai/storage/logs/laravel.log | grep WhatsApp
   ```

### 15.3 Database Connection Issues

**Symptoms:** "SQLSTATE[HY000] [2002] Connection refused"

**Solutions:**

1. Check MySQL running:
   ```bash
   sudo systemctl status mysql
   ```

2. Verify credentials in `.env`

3. Test connection:
   ```bash
   mysql -u sedekah_user -p sedekah_ai
   ```

4. Check MySQL logs:
   ```bash
   sudo tail -f /var/log/mysql/error.log
   ```

### 15.4 Permission Denied Errors

**Symptoms:** Cannot write to storage or cache

**Solutions:**

```bash
# Fix ownership
sudo chown -R sedekah:www-data /var/www/sedekah_ai

# Fix permissions
sudo chmod -R 775 /var/www/sedekah_ai/storage
sudo chmod -R 775 /var/www/sedekah_ai/bootstrap/cache

# Clear cache
cd /var/www/sedekah_ai
php artisan cache:clear
php artisan config:clear
```

### 15.5 500 Internal Server Error

**Steps:**

1. Check Laravel logs:
   ```bash
   tail -50 /var/www/sedekah_ai/storage/logs/laravel.log
   ```

2. Check Nginx error log:
   ```bash
   sudo tail -50 /var/log/nginx/sedekah_ai_error.log
   ```

3. Check PHP-FPM errors:
   ```bash
   sudo tail -50 /var/log/php8.1-fpm.log
   ```

4. Enable debug mode temporarily:
   ```bash
   # Edit .env
   APP_DEBUG=true
   
   # Clear config
   php artisan config:clear
   
   # Reproduce error, then disable debug
   APP_DEBUG=false
   php artisan config:cache
   ```

### 15.6 Assets Not Loading (404)

**Symptoms:** CSS/JS files return 404

**Solutions:**

```bash
# Rebuild assets
cd /var/www/sedekah_ai
npm run build

# Check public/build directory exists
ls -lh public/build/

# Clear view cache
php artisan view:clear
```

---

## 📞 Support Contacts

**Development Team:**
- Email: dev@yourdomain.com
- Slack: #sedekah-ai-support

**Emergency Contacts:**
- On-call: +62-XXX-XXXX-XXXX
- Email: emergency@yourdomain.com

---

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Nginx Documentation](https://nginx.org/en/docs/)
- [Supervisor Documentation](http://supervisord.org/)
- [MySQL Optimization](https://dev.mysql.com/doc/refman/8.0/en/optimization.html)

---

**Deployment Complete! 🚀**

Monitor your application closely for the first 24-48 hours after deployment. Check logs regularly and be prepared to rollback if critical issues arise.
