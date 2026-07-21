# Production Deployment Checklist

## Pre-Deployment (Development Phase)

### Code Quality
- [ ] All PHP files validated (no syntax errors)
- [ ] No debug output (var_dump, print_r, etc.)
- [ ] Security review completed
- [ ] Input validation implemented
- [ ] Error handling comprehensive
- [ ] Comments added untuk complex logic
- [ ] Tests passed (manual testing)

### Database
- [ ] `setup.sql` tested locally
- [ ] All migrations verified
- [ ] Database backups created
- [ ] Table indexes optimized
- [ ] Foreign keys defined (if any)
- [ ] Test data cleaned up

### Frontend
- [ ] All forms tested
- [ ] File upload tested dengan berbagai format
- [ ] Mobile responsiveness verified
- [ ] Browser compatibility checked (Chrome, Firefox, Safari, Edge)
- [ ] Console errors cleared
- [ ] Performance optimized

### Documentation
- [ ] README.md complete dan updated
- [ ] API.md documented
- [ ] INSTALL.md verified step-by-step
- [ ] CHANGELOG.md updated
- [ ] Code comments added

---

## Server Setup (Before Going Live)

### System Requirements
- [ ] PHP 7.4+ installed
  ```bash
  php -v
  ```

- [ ] Required PHP extensions enabled
  ```bash
  php -m | grep -E "pdo|pdo_mysql|zip|dom|SimpleXML"
  ```

- [ ] MySQL/MariaDB running
  ```bash
  mysql --version
  ```

- [ ] Web server configured (Apache/Nginx)

### Folder Setup
- [ ] Create project folder: `/var/www/prabasini`
- [ ] Upload all project files
- [ ] Set proper permissions:
  ```bash
  chmod 755 /var/www/prabasini
  chmod 755 /var/www/prabasini/public/uploads
  chmod 755 /var/www/prabasini/config
  chmod 755 /var/www/prabasini/logs
  ```

- [ ] Create necessary directories:
  ```bash
  mkdir -p /var/www/prabasini/logs
  mkdir -p /var/www/prabasini/public/uploads
  chmod 777 /var/www/prabasini/logs
  chmod 777 /var/www/prabasini/public/uploads
  ```

### SSL/HTTPS
- [ ] SSL certificate installed
- [ ] Force HTTPS redirect
- [ ] Update all URLs to use HTTPS

---

## Database Setup

### Production Database
- [ ] New database created
  ```sql
  CREATE DATABASE prabasini_prod;
  ```

- [ ] Database user created (separate dari root)
  ```sql
  CREATE USER 'pap_user'@'localhost' IDENTIFIED BY 'strong_password';
  GRANT ALL PRIVILEGES ON prabasini_prod.* TO 'pap_user'@'localhost';
  FLUSH PRIVILEGES;
  ```

- [ ] `setup.sql` imported
  ```bash
  mysql -u pap_user -p prabasini_prod < setup.sql
  ```

- [ ] Backup created
  ```bash
  mysqldump -u pap_user -p prabasini_prod > backup_$(date +%Y%m%d).sql
  ```

- [ ] Database user permissions verified

### Database Optimization
- [ ] Indexes created
- [ ] Auto-increment seeded
- [ ] Character set: utf8mb4
- [ ] Collation: utf8mb4_unicode_ci

---

## Configuration Update

### config/database.php
- [ ] Update DB_HOST to production server
- [ ] Update DB_USER (use dedicated user, not root)
- [ ] Update DB_PASS (use strong password)
- [ ] Update DB_NAME to production database
- [ ] Set DEBUG_MODE to false
- [ ] Test connection before deployment

Example:
```php
define('DB_HOST', 'db.production.com');
define('DB_USER', 'pap_user');
define('DB_PASS', 'super_secure_password_123!@#');
define('DB_NAME', 'prabasini_prod');
define('DEBUG_MODE', false);
```

### .gitignore
- [ ] Sensitive files excluded (config/database.php)
- [ ] Logs excluded
- [ ] Uploads excluded
- [ ] Vendor files handled properly

---

## Security Hardening

### File Permissions
- [ ] Web server can read files
  ```bash
  chmod 644 *.php
  ```

- [ ] config/database.php extra protected
  ```bash
  chmod 600 config/database.php
  ```

- [ ] Uploads folder writable
  ```bash
  chmod 755 public/uploads
  ```

### PHP Configuration
- [ ] `display_errors` = Off
- [ ] `log_errors` = On
- [ ] Error log path configured
- [ ] `upload_max_filesize` = 5M (or appropriate)
- [ ] `post_max_size` = 10M (or appropriate)

### Web Server Configuration

#### Apache (.htaccess)
```apache
<FilesMatch "\.(php|phtml|php3|php4|php5|phps|pht|phar)$">
    Order allow,deny
    Allow from all
</FilesMatch>

# Protect sensitive files
<FilesMatch "database\.php">
    Order allow,deny
    Deny from all
</FilesMatch>

# Enable HTTPS
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

#### Nginx (server block)
```nginx
server {
    listen 443 ssl http2;
    server_name prabasini.com;
    
    ssl_certificate /etc/ssl/certs/cert.pem;
    ssl_certificate_key /etc/ssl/private/key.pem;
    
    root /var/www/prabasini/public;
    index index.php;
    
    # Protect sensitive files
    location ~ /config/ {
        deny all;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### API Security
- [ ] Rate limiting implemented (future)
- [ ] Input validation added
- [ ] Output escaping enabled
- [ ] CORS configured appropriately
- [ ] CSRF protection ready (future)

---

## Testing on Production

### Functionality Tests
- [ ] Landing page loads (index.php)
- [ ] Health check page accessible (health-check.php)
- [ ] PAP page loads (pap.php)
- [ ] File upload works
- [ ] Data displays correctly
- [ ] Delete function works
- [ ] WhatsApp button generates correct URL
- [ ] Copy function works

### Database Tests
- [ ] Connection verified
  ```bash
  curl -s http://localhost/prabasini/public/health-check.php | grep -i database
  ```

- [ ] Data persists after import
- [ ] Queries optimized
- [ ] No SQL errors in logs

### Performance Tests
- [ ] Page load time acceptable
- [ ] Upload handles large files
- [ ] Database queries efficient
- [ ] No memory leaks detected

### Security Tests
- [ ] Direct file access blocked
- [ ] Database credentials not exposed
- [ ] HTTPS working correctly
- [ ] No sensitive data in logs

---

## Monitoring & Maintenance

### Logging Setup
- [ ] Error logs configured
  ```bash
  tail -f /var/log/php-errors.log
  ```

- [ ] Access logs monitored
- [ ] Activity logs recorded
- [ ] Log rotation configured

### Backups
- [ ] Database backup automated (daily)
  ```bash
  0 2 * * * mysqldump -u pap_user -p prabasini_prod > /backups/db_$(date +\%Y\%m\%d).sql
  ```

- [ ] File backups automated (weekly)
- [ ] Backup storage off-site
- [ ] Restore procedure tested

### Monitoring
- [ ] Disk space monitored
- [ ] Database size monitored
- [ ] Error rate monitored
- [ ] Uptime alerts configured

### Maintenance
- [ ] PHP updated regularly
- [ ] MySQL updated regularly
- [ ] Security patches applied
- [ ] Dependencies checked

---

## Post-Deployment

### Documentation
- [ ] Production deployment steps documented
- [ ] Access credentials secured (in password manager)
- [ ] Admin contacts listed
- [ ] Support procedures documented

### User Training
- [ ] Users trained on system usage
- [ ] Documentation shared
- [ ] Support channels established

### Go-Live
- [ ] DNS updated (if needed)
- [ ] Email alerts configured
- [ ] Support team standby
- [ ] Client notified

---

## Rollback Plan

If something goes wrong:

1. **Stop the application**
   ```bash
   # Take down the site
   mv public/index.php public/index.php.bak
   ```

2. **Restore from backup**
   ```bash
   # Restore database
   mysql -u pap_user -p prabasini_prod < backup_latest.sql
   
   # Restore files
   cp -r backup/prabasini/* /var/www/prabasini/
   ```

3. **Verify**
   ```bash
   # Check health
   curl http://localhost/prabasini/public/health-check.php
   ```

4. **Restart service**
   ```bash
   systemctl restart php-fpm
   systemctl restart apache2
   ```

---

## Deployment Commands

Quick deployment script:
```bash
#!/bin/bash

# Stop application
echo "Stopping application..."

# Backup database
echo "Backing up database..."
mysqldump -u pap_user -p prabasini_prod > backup_$(date +%Y%m%d_%H%M%S).sql

# Update files
echo "Updating files..."
cd /var/www/prabasini
git pull origin main

# Set permissions
chmod 755 public/uploads
chmod 600 config/database.php

# Clear cache (if any)
# rm -rf cache/*

# Verify
echo "Verifying installation..."
curl -s http://localhost/prabasini/public/health-check.php

echo "Deployment complete!"
```

---

## Troubleshooting

### 500 Internal Server Error
- [ ] Check PHP error log
- [ ] Verify file permissions
- [ ] Check database connection
- [ ] Validate PHP syntax

### Database Connection Failed
- [ ] Check MySQL running
- [ ] Verify credentials
- [ ] Check firewall rules
- [ ] Ping database server

### Upload Fails
- [ ] Check upload permissions
- [ ] Check disk space
- [ ] Check PHP upload limits
- [ ] Check file format

---

**Date**: ________________
**Deployed By**: ________________
**Approved By**: ________________
**Notes**: ________________

---

**Safety First! Always test in staging before production.** ✅
