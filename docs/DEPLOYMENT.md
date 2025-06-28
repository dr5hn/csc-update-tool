# Deployment Guide

This document outlines the improved deployment process for the CSC Update Tool application.

## Overview

The deployment uses GitHub Actions with a robust workflow that includes:
- Automated testing before deployment
- Zero-downtime deployment strategy
- Automatic rollback on failure
- Health checks
- Database migrations
- Release management

## Required GitHub Secrets

Configure the following secrets in your GitHub repository settings:

### Essential Secrets
- `DEPLOY_KEY`: SSH private key for deployment user
- `SERVER_IP`: IP address of your production server
- `DEPLOY_USER`: SSH user for deployment (default: 'deploy')

### Optional Secrets
- `DOMAIN_NAME`: Your domain name for health checks
- `SLACK_WEBHOOK_URL`: Slack webhook for deployment notifications

## Server Setup

### 1. Create Deployment User
```bash
# Create deployment user
sudo adduser deploy
sudo usermod -aG sudo deploy
sudo usermod -aG www-data deploy

# Setup SSH key
sudo mkdir -p /home/deploy/.ssh
sudo touch /home/deploy/.ssh/authorized_keys
sudo chmod 700 /home/deploy/.ssh
sudo chmod 600 /home/deploy/.ssh/authorized_keys
sudo chown -R deploy:deploy /home/deploy/.ssh

# Add your public key to authorized_keys
echo "your-public-key-here" | sudo tee -a /home/deploy/.ssh/authorized_keys
```

### 2. Directory Structure
```bash
sudo mkdir -p /var/www/csc-update-tool/{releases,shared/storage}
sudo chown -R deploy:www-data /var/www/csc-update-tool
sudo chmod -R 775 /var/www/csc-update-tool
```

### 3. Shared Files Setup
```bash
# Create shared directories
sudo mkdir -p /var/www/csc-update-tool/shared/storage/framework/{cache,sessions,views}
sudo mkdir -p /var/www/csc-update-tool/shared/storage/{logs,app/public}

# Create .env file
sudo cp /path/to/your/.env /var/www/csc-update-tool/shared/.env
sudo chown www-data:www-data /var/www/csc-update-tool/shared/.env
sudo chmod 640 /var/www/csc-update-tool/shared/.env
```

### 4. Web Server Configuration

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/csc-update-tool/current/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

### 5. Systemd Services

#### Laravel Queue Worker
```bash
# Create service file
sudo tee /etc/systemd/system/laravel-queue.service > /dev/null <<EOF
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/csc-update-tool/current/artisan queue:work --sleep=3 --tries=3
StandardOutput=journal
StandardError=journal

[Install]
WantedBy=multi-user.target
EOF

# Enable and start service
sudo systemctl daemon-reload
sudo systemctl enable laravel-queue
sudo systemctl start laravel-queue
```

## Deployment Features

### 1. Testing Phase
- Runs PHPUnit tests before deployment
- Prevents deployment if tests fail
- Uses SQLite for fast testing

### 2. Zero-Downtime Deployment
- Creates new release directory
- Builds application in isolation
- Atomic switch to new release
- Keeps previous releases for rollback

### 3. Database Handling
- Automatic database backup (if configured)
- Runs migrations safely
- Rollback capability

### 4. Health Checks
- Verifies application is responding
- Checks database connectivity
- Automatic rollback on health check failure

### 5. Release Management
- Keeps last 5 releases
- Automatic cleanup of old releases
- Easy manual rollback if needed

## Manual Deployment Commands

### Deploy Specific Branch
```bash
# Trigger deployment from specific branch
git push origin feature-branch

# Or use workflow dispatch
gh workflow run deploy.yml --ref feature-branch
```

### Manual Rollback
```bash
ssh deploy@your-server
cd /var/www/csc-update-tool/releases
ls -la  # See available releases
sudo ln -sf /var/www/csc-update-tool/releases/TIMESTAMP /var/www/csc-update-tool/current
sudo systemctl reload php8.4-fpm
```

### Check Deployment Status
```bash
# Check application health
curl https://your-domain.com/health

# Check services
sudo systemctl status php8.4-fpm
sudo systemctl status laravel-queue
sudo systemctl status nginx
```

## Troubleshooting

### Common Issues

#### Permission Problems
```bash
sudo chown -R www-data:www-data /var/www/csc-update-tool/current/storage
sudo chmod -R 775 /var/www/csc-update-tool/current/storage
```

#### Queue Not Processing
```bash
sudo systemctl restart laravel-queue
sudo systemctl status laravel-queue
```

#### Database Connection Issues
```bash
# Check database credentials in .env
sudo cat /var/www/csc-update-tool/shared/.env | grep DB_

# Test database connection
cd /var/www/csc-update-tool/current
sudo -u www-data php artisan tinker
>>> DB::connection()->getPdo();
```

### Logs
```bash
# Application logs
tail -f /var/www/csc-update-tool/shared/storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/error.log

# PHP-FPM logs
tail -f /var/log/php8.4-fpm.log
```

## Security Considerations

1. **SSH Key Management**: Use dedicated deployment keys
2. **User Permissions**: Deploy user has minimal required permissions
3. **File Permissions**: Proper ownership and permissions on all files
4. **Environment Variables**: Secure storage of sensitive configuration
5. **Database Backups**: Regular automated backups before deployments

## Monitoring

The deployment includes:
- Health check endpoint (`/health`)
- Slack notifications (optional)
- Deployment status tracking
- Automatic rollback on failures

## Best Practices

1. **Test Locally**: Always test changes locally first
2. **Review Changes**: Use pull requests for code review
3. **Monitor Deployments**: Watch deployment logs and health checks
4. **Backup Strategy**: Ensure database backups are working
5. **Rollback Plan**: Know how to quickly rollback if needed

## Environment Specific Notes

### Production
- All optimizations enabled
- Error reporting disabled
- Cache enabled
- Debug mode off

### Staging
- Similar to production but with debug enabled
- Separate database and environment

For questions or issues, refer to the application logs or contact the development team.
