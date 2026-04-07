# Docker & Deployment Guide

## Option 1: Deploy to Render (RECOMMENDED)

**Best for**: Production hosting, no local Docker needed

### Steps:
```bash
# Just push to GitHub - no Dockerfile needed!
git push origin main
```

Render automatically deploys using its built-in PHP runtime. **No Docker required.**

---

## Option 2: Test Locally with Docker

**Best for**: Testing before Render deployment

### Prerequisites:
- Install [Docker Desktop](https://www.docker.com/products/docker-desktop)
- Install [Docker Compose](https://docs.docker.com/compose/install/)

### Start Local Environment:

```bash
# Navigate to project folder
cd c:\xampp\htdocs\myportal

# Start Docker containers
docker-compose up -d

# Wait 10 seconds for MySQL to start, then test
# Open browser: http://localhost:8080/login.php
```

### Access Points:
- **Web App**: http://localhost:8080
- **Login Page**: http://localhost:8080/login.php
- **MySQL**: localhost:3306 (use MySQL client or Workbench)

### Test Credentials (local):
```
Username: vincent
Password: 0717110505
```

### Stop Docker:
```bash
docker-compose down
```

### View Logs:
```bash
# Web server logs
docker-compose logs web

# Database logs
docker-compose logs db

# Follow logs in real-time
docker-compose logs -f
```

---

## Option 3: Keep Using XAMPP Locally

**Best for**: No Docker, just XAMPP

### Steps:
```bash
# Keep using current setup
# XAMPP is already running
# Visit: http://localhost/myportal/login.php
```

### When Ready to Deploy to Render:
```bash
# Push to GitHub (Docker files included but not used)
git push origin main
```

---

## File Explanations

### `Dockerfile`
- Defines Docker image for local testing
- Based on PHP 8.2 with Apache
- Auto-installs MySQLi extension
- **Not used by Render** (Render has built-in PHP)

### `docker-compose.yml`
- Runs PHP + MariaDB locally
- Maps port 8080 → 80 (web)
- Imports SQL database automatically
- Sets up networking between containers
- **Not needed for Render deployment**

---

## Workflow Recommendation

### For Development:
```
Option A: Use XAMPP (simplest)
Option B: Use Docker (closer to production)
```

### For Production:
```
Deploy to Render
No Docker needed there!
```

---

## Troubleshooting Docker Issues

### "failed to read dockerfile: open Dockerfile: no such file or directory"

**Cause**: Running Docker in wrong directory

**Fix**:
```bash
# Make sure you're in project root
cd c:\xampp\htdocs\myportal

# Run from there
docker-compose up -d
```

### "Cannot connect to MySQL from PHP"

**Cause**: MySQL not fully started

**Fix**:
```bash
# Wait 15 seconds after starting
docker-compose up -d
sleep 15
# Then try http://localhost:8080/login.php
```

### "Port 8080 already in use"

**Fix**: Change docker-compose.yml port:
```yaml
ports:
  - "8081:80"  # Use 8081 instead
```

### "Database not importing"

**Fix**: Restart containers:
```bash
docker-compose down
docker-compose up -d
```

---

## Comparing Environments

| Aspect | XAMPP | Docker Local | Render |
|--------|-------|--------------|--------|
| Setup | Easy | Moderate | Automatic |
| Performance | Fast | Medium | Fast |
| DB Connection | localhost | db (internal) | Remote |
| Port | 80 | 8080 | 10000 (auto) |
| MySQL Version | 10.4.32 | 10.4 | 10.4+ |
| **For Testing** | ✅ Good | ✅ Better | N/A |
| **For Production** | ❌ No | ❌ No | ✅ Yes |
| **Matches Render** | Somewhat | Yes | Perfect |

---

## Database Configuration

### XAMPP (Current Local)
```php
DB_HOST = localhost
DB_USER = root
DB_PASSWORD = 
DB_NAME = english_portal
```

### Docker Local
```php
DB_HOST = db
DB_USER = portal_user
DB_PASSWORD = password123
DB_NAME = english_portal
```

### Render Production
```php
DB_HOST = [from Render MySQL instance]
DB_USER = portal_user
DB_PASSWORD = [your secure password]
DB_NAME = english_portal
```

**Note**: Your `config.php` loads from `.env`, so just update `.env` when switching environments!

---

## Quick Start

### Just deploy to Render:
```bash
git push origin main
# Done! Render handles everything
```

### Test with Docker first:
```bash
docker-compose up -d
# Visit http://localhost:8080/login.php
# Test everything
# Then push to Render
```

### Keep using XAMPP:
```bash
# No changes needed
# When ready: git push origin main
```

---

**Recommendation**: Use XAMPP locally (you already have it), then push straight to Render. The Docker files are included if you ever want them, but optional!
