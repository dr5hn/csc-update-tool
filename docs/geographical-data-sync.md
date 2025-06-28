# Geographical Data Sync System

This document describes the automated geographical data synchronization system that ensures your application always has up-to-date geographical data from the [countries-states-cities-database](https://github.com/dr5hn/countries-states-cities-database) repository.

## Overview

The system automatically downloads and syncs geographical data (regions, subregions, countries, states, cities) from the GitHub repository, ensuring your database is always current and preventing data loss during migrations.

## Features

- ✅ **Automatic Weekly Sync**: Runs every Sunday at 2 AM
- ✅ **Migration Protection**: Automatically seeds data during migrations
- ✅ **Backup System**: Creates backups before each sync
- ✅ **Error Handling**: Robust error handling with logging
- ✅ **Reliable Downloads**: Retry logic with configurable attempts
- ✅ **Configurable**: Fully configurable via environment variables
- ✅ **Status Monitoring**: Commands to check data status
- ✅ **Manual Control**: Force sync when needed

## Commands

### Sync Geographical Data
```bash
# Sync data (skips if data already exists)
php artisan geo:sync

# Force sync (overwrites existing data)
php artisan geo:sync --force
```

### Check Status
```bash
# Check current data status and configuration
php artisan geo:status
```

### Manual Seeding
```bash
# Run seeder manually
php artisan db:seed GeographicalDataSeeder
```

## Configuration

The system is configured via `config/geographical.php` and environment variables:

### Environment Variables

```env
# Sync Settings
GEO_SYNC_ENABLED=true
GEO_SYNC_TIMEOUT=120
GEO_SYNC_RETRY_ATTEMPTS=3
GEO_SYNC_CHUNK_SIZE=1000

# Backup Settings
GEO_BACKUP_ENABLED=true
GEO_BACKUP_KEEP_DAYS=30

# Notification Settings
GEO_NOTIFICATIONS_ENABLED=true
GEO_ADMIN_EMAIL=admin@example.com
GEO_NOTIFY_SUCCESS=false
GEO_NOTIFY_FAILURE=true
GEO_NOTIFY_LARGE_CHANGES=true
```

## Scheduled Tasks

The system runs automatically via Laravel's task scheduler. To enable, add this to your cron:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Data Source

The system downloads data from:
- **URL**: `https://raw.githubusercontent.com/dr5hn/countries-states-cities-database/master/sql/world.sql`
- **Retry Logic**: 3 attempts with 2-second delays between retries
- **Timeout**: 120 seconds (configurable)

## Data Structure

The system manages these tables:
- **regions**: 6 records (Africa, Americas, Asia, Europe, Oceania, Antarctica)
- **subregions**: 22 records (Western Europe, Eastern Asia, etc.)
- **countries**: ~250 records (All world countries)
- **states**: ~5,144 records (States/provinces by country)
- **cities**: ~151,908 records (Cities by state/country)

## Troubleshooting

### Common Issues

#### 1. No Data After Migration
```bash
php artisan geo:status
php artisan geo:sync --force
```

#### 2. Sync Failures
```bash
tail -f storage/logs/laravel.log
php artisan geo:sync --force
```

#### 3. Schedule Not Running
```bash
crontab -l
php artisan schedule:run
```
