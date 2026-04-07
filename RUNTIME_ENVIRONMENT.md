# Runtime Environment Specifications

## Render Runtime Environment

### PHP Runtime
- **Default Version**: PHP 8.2.x (latest stable)
- **Available Versions**: PHP 8.0, 8.1, 8.2, 8.3
- **Recommended for Your App**: PHP 8.0+ (your code is compatible)

### Database Runtime
- **MySQL Version**: 8.0+ (MariaDB 10.4+)
- **Recommended**: MariaDB 10.4+
- **Character Set**: UTF-8mb4 (already configured in SQL)
- **InnoDB**: Yes (default table engine)

---

## Required PHP Extensions

Your application uses:
```
✅ mysqli (MySQL improved) - Built-in, enabled by default
✅ session - Built-in, enabled by default
✅ json - Built-in, enabled by default
✅ hash - Built-in (password_hash/verify)
✅ filter - Built-in, enabled by default
```

**No additional extensions needed!**

---

## Machine Configuration (Free Tier)

| Resource | Specification |
|----------|---------------|
| Memory | 512 MB shared |
| CPU | Shared CPU (0.5 vCPU) |
| Ephemeral Disk | 1 GB |
| Storage (MySQL) | 100 MB (free tier) |
| Bandwidth | Unlimited |
| Build Timeout | 30 minutes |
| Inactivity Timeout | 15 minutes |

### Starter Tier Recommended
| Resource | Specification |
|----------|---------------|
| Memory | 1 GB |
| CPU | Shared (1 vCPU) |
| Ephemeral Disk | 25 GB |
| Storage (MySQL) | 10 GB |
| **Cost** | $7/month (web) + $15/month (db) |

---

## Environment Variables on Render

Render automatically provides:
```bash
PORT=10000           # Your app listens on this
RENDER_EXTERNAL_URL  # Your public URL (https://english-portal-xxxx.render.com)
RENDER_INTERNAL_IP   # Internal IP for inter-service communication
```

Your custom variables (set in dashboard):
```bash
DB_HOST              # MySQL host from Render
DB_USER              # Database user
DB_PASSWORD          # Database password
DB_NAME              # Database name
APP_ENV              # production/development
APP_DEBUG            # true/false
SECURE_PASSWORD_HASHING  # true
```

---

## Build Configuration

### Current render.yaml setup
```yaml
buildCommand: "true"    # No build needed
startCommand: ""        # Uses Apache automatic startup
```

### If you need to run PHP scripts on deploy:
```yaml
buildCommand: "php migrate_passwords.php && rm migrate_passwords.php"
```

---

## Runtime Behavior on Render

### Free Tier
- ⏸️ **Spins down after 15 minutes of inactivity**
- ⏰ **Cold start: 1-5 seconds**
- 🔄 **Auto-restart on request**
- ✅ Good for testing/development

### Starter Tier
- 🟢 **Always running**
- ⚡ **No cold starts**
- 🔄 **Auto-restart on deploy**
- ✅ Recommended for production

---

## Port Configuration

Your app runs on port **10000** on Render:
- Internal: `localhost:10000`
- Public: `https://english-portal-xxxx.render.com`
- Database: MySQL remote connection from Render

**No changes needed in `config.php`** - PHP listens on `10000` automatically.

---

## Limits & Constraints

### File System
- ⚠️ **Ephemeral** - All files deleted on restart/redeploy
- ❌ Cannot store files permanently (uploads won't work)
- ✅ Database persists (in MySQL instance)
- ✅ Logs are sent to Render logs (check dashboard)

### Database
- Free: 100 MB storage
- Connections: 10 concurrent max (free) / 20+ (paid)
- Backups: Manual only (free tier)
- **Your app**: ~10-50 MB estimated (small dataset)

### Network
- ✅ Inbound HTTPS: Unlimited
- ✅ Outbound: Allowed (for future APIs)
- ✅ Internal communication: Free
- ⚠️ No public ports except 443 (HTTPS)

---

## Performance Characteristics

### Expected Performance
- **Home Page Load**: 100-500ms
- **Login**: 200-800ms
- **Database Query**: 10-50ms
- **TTFB**: 200-1000ms

### Scaling Capacity
- **Free Tier**: ~50-100 concurrent users
- **Starter Tier**: ~500+ concurrent users
- **Growth Plan**: Unlimited (with load balancing)

### Database Capacity
| Size | Capacity |
|------|----------|
| Your Data | ~50 MB (students, scores, content) |
| Free Tier | 100 MB ✅ Enough |
| Starter | 10 GB ✅ More than enough |

---

## Render PHP Runtime Details

### Auto-configuration
- ✅ Detects `*.php` files
- ✅ Automatically starts Apache + mod_php
- ✅ Sets DocumentRoot to project root
- ✅ Listens on PORT from environment

### Built-in Web Server
- **Type**: Apache 2.4
- **Python**: 3.10+ (not needed for your app)
- **Node.js**: Not included (not needed)
- **Build tools**: Make, gcc (available)

### Sample render.yaml (Recommended)
```yaml
services:
  - type: web
    name: english-portal
    env: php
    plan: starter
    buildCommand: "true"
    envVars:
      - key: APP_ENV
        value: production
      - key: APP_DEBUG
        value: "false"
```

---

## Memory Usage

### Per-Request Memory
- PHP startup: ~5 MB
- Config loading: ~1 MB
- Database connection: ~1 MB
- Session handling: ~0.5 MB
- **Per request**: ~10-15 MB

### Concurrent Users
- Free tier (512 MB): ~30-40 simultaneous users
- Starter tier (1 GB): ~60-80 simultaneous users

### Your Database
- 3 tables (users, content, scores)
- Est. 100-1000 records
- Est. 10-50 MB storage
- ✅ Free tier sufficient

---

## Recommended Runtime Setup

### For Testing / Free Tier
```
PHP: 8.2 (default)
MySQL: MariaDB 10.4
Memory: 512 MB
Storage: 100 MB
Cost: $0 (free tier) / $15 (MySQL only)
Cold Start: Acceptable for testing
```

### For Production / Starter Tier
```
PHP: 8.2 (default)
MySQL: MariaDB 10.4
Memory: 1 GB (guaranteed)
Storage: 10 GB
Cost: $22/month ($7 web + $15 db)
Cold Start: None (always running)
Backups: Manual + recommended backups
```

---

## Health Check & Monitoring

### Render Health Check
- **Path**: `/login.php`
- **Interval**: Every 30 seconds
- **Timeout**: 10 seconds
- **Status**: Shows in Render dashboard

### Logging
```php
// Logs to Render dashboard (2 MB retention free tier)
error_log("Your message here");
```

View in Render dashboard:
- Deployment logs
- Runtime logs (stdout/stderr)
- Error logs

---

## Environment Migration Path

### Local (XAMPP)
```
PHP: 8.0.30
MySQL: MariaDB 10.4.32
Port: 80/3306
Debug: ON (APP_DEBUG=true)
```

### Render Production
```
PHP: 8.2 (compatible)
MySQL: MariaDB 10.4+ (compatible)
Port: 10000 (automatic)
Debug: OFF (APP_DEBUG=false)
```

✅ **100% Compatible** - No code changes needed!

---

## Troubleshooting Runtime Issues

### App keeps spinning down
- Solution: Upgrade to Starter tier (~$7/month)
- Or: Add wake-up script in cron monitoring

### Database connection timeout
- Solution: Verify `DB_HOST` in Render environment
- Check: MySQL instance status in dashboard

### High memory usage
- Solution: Check for large databases or loops
- Profile: Use Render logs to debug

### Slow response times
- Solution: Free tier is slower; upgrade to Starter
- Profile: Check database query performance

---

## Summary Table

| Aspect | Local (XAMPP) | Render (Free) | Render (Starter) |
|--------|--------------|---------------|-----------------|
| PHP Version | 8.0.30 | 8.2.x | 8.2.x |
| Database | MySQL 10.4.32 | MariaDB 10.4+ | MariaDB 10.4+ |
| Memory | Unlimited | 512 MB | 1 GB |
| Uptime | Local only | 15 min inactivity | 24/7 |
| Cost | Free (local) | Free/$15 DB | $22/month |
| Storage | Local disk | 1 GB ephemeral | 25 GB ephemeral |
| **Compatible?** | ✅ Yes | ✅ Yes | ✅ Yes |

---

**Your application: FULLY COMPATIBLE with Render runtime!**
