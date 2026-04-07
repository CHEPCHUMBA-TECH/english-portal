# English Portal - Deployment Guide

## Pre-Deployment Checklist

### 1. Environment Configuration
- [ ] Copy `.env.example` to `.env`
- [ ] Update `.env` with production database credentials
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Ensure `.env` is in `.gitignore` (never commit credentials)

### 2. Database Setup
- [ ] Create MySQL database: `english_portal`
- [ ] Import `english_portal.sql`
- [ ] Run password migration: `php migrate_passwords.php`
- [ ] Verify users table has hashed passwords
- [ ] Delete `migrate_passwords.php` after migration

### 3. Security Hardening
- [ ] Update database user credentials in `.env`
- [ ] Remove or restrict access to debug files:
  - `testdb.php`
  - `check_errors.php`
  - `tmp_query.php`
  - `tmp_schema.php`
- [ ] Create `/logs` directory with proper permissions (755)
- [ ] Ensure `.env` file is NOT world-readable (644)
- [ ] Enable HTTPS on production server

### 4. File Permissions
```bash
# Recommended permissions
chmod 755 myportal/
chmod 644 myportal/*.php
chmod 755 myportal/logs
chmod 644 myportal/.env
chmod 000 myportal/.env.example
```

### 5. Create Admin User
```php
// Hash a strong password
$admin_password = password_hash('YourSecurePassword123!', PASSWORD_BCRYPT);
// INSERT INTO users (username, password, role) VALUES ('admin', '$HASHED_PASSWORD', 'teacher')
```

### 6. Testing
- [ ] Test login with valid credentials
- [ ] Test with invalid credentials
- [ ] Verify session management works
- [ ] Check database connections
- [ ] Test error logging

## Deployment Steps

1. **Upload Files**
   ```bash
   scp -r myportal/ user@server:/var/www/html/
   ```

2. **Configure Environment**
   ```bash
   ssh user@server
   cd /var/www/html/myportal
   cp .env.example .env
   # Edit .env with production values
   nano .env
   ```

3. **Migrate Passwords**
   ```bash
   php migrate_passwords.php
   rm migrate_passwords.php
   ```

4. **Set Permissions**
   ```bash
   chmod 755 .
   chmod 644 *.php
   chmod 755 logs
   chmod 644 .env
   ```

5. **Verify Installation**
   - Visit: `https://yourdomain.com/myportal/login.php`
   - Test login with known credentials
   - Check logs for errors

## Production Environment Variables

```
DB_HOST=your-db-host
DB_USER=portal_user
DB_PASSWORD=strong-password-here
DB_NAME=english_portal
APP_ENV=production
APP_DEBUG=false
SECURE_PASSWORD_HASHING=true
```

## Maintenance

### Regular Tasks
- Monitor `/logs/error.log` for errors
- Backup database weekly: `mysqldump -u user -p english_portal > backup_$(date +%Y%m%d).sql`
- Review security logs
- Update PHP version if available

### Emergency Actions
- If compromised: Reset all passwords
- Restore from backup if needed
- Review access logs

## Support & Troubleshooting

**Database Connection Error:**
- Verify credentials in `.env`
- Check MySQL service is running
- Test connection: `php testdb.php` (development only)

**Password Issues:**
- Ensure migration script ran successfully
- Test login with plaintext password in debug mode

**Logging Issues:**
- Verify `/logs` directory exists
- Check directory permissions (755)
- Verify PHP can write to logs

---
Last Updated: April 2026
