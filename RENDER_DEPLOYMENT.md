# Deploying to Render - Complete Guide

## Prerequisites

1. **GitHub Account** - Push your code to GitHub
2. **Render Account** - Sign up at https://render.com
3. **Production Build** - Already completed ✅

## Step 1: Prepare Your GitHub Repository

### 1.1 Initialize Git in your project
```bash
cd c:\xampp\htdocs\myportal
git init
```

### 1.2 Create `.gitignore` (should already exist)
```
.env
.env.local
.env.*.local
*.log
logs/
.DS_Store
Thumbs.db
migrate_passwords.php
testdb.php
check_errors.php
tmp_query.php
tmp_schema.php
```

### 1.3 Commit your code
```bash
git add .
git commit -m "Initial production build"
```

### 1.4 Create GitHub repository
- Go to https://github.com/new
- Create repo named `english-portal`
- Copy remote URL

### 1.5 Push to GitHub
```bash
git remote add origin https://github.com/YOUR_USERNAME/english-portal.git
git branch -M main
git push -u origin main
```

## Step 2: Create Render Web Service

1. Go to https://dashboard.render.com
2. Click **"New +"** → Select **"Web Service"**
3. Connect your GitHub account
4. Select the `english-portal` repository
5. Fill in the configuration:
   - **Name**: `english-portal`
   - **Environment**: `PHP`
   - **Region**: Choose closest to your users
   - **Branch**: `main`
   - **Build Command**: `composer install` (if using composer) or leave empty
   - **Start Command**: Leave empty (Render uses Apache/Nginx by default)
   - **Instance Type**: Free or Starter ($7/month)

6. Click **"Create Web Service"**

## Step 3: Create MySQL Database on Render

1. In Render Dashboard, click **"New +"** → **"MySQL"**
2. Configure:
   - **Name**: `english-portal-db`
   - **Database**: `english_portal`
   - **Username**: `portal_user`
   - **Region**: Same as web service
   - **Instance Type**: Free or Starter

3. Click **"Create Database"**
4. Wait ~5 minutes for database to initialize
5. Copy the **Internal Connection String** (looks like: `mysql://user:pass@...`)

## Step 4: Import Your Database

### 4.1 Get Database Credentials
In Render Dashboard:
- Click on your MySQL instance
- Note the connection details

### 4.2 Import SQL using MySQL CLI
```bash
mysql -h [HOST] -u [USER] -p[PASSWORD] [DATABASE] < english_portal.sql
```

Or use a GUI tool like MySQL Workbench to import.

## Step 5: Configure Environment Variables

### 5.1 In Render Dashboard
1. Click on your Web Service (`english-portal`)
2. Go to **Environment** section
3. Click **"Add Environment Variable"**
4. Add these variables:

```
DB_HOST=your-database-host-from-render
DB_USER=portal_user
DB_PASSWORD=your-render-database-password
DB_NAME=english_portal
APP_ENV=production
APP_DEBUG=false
SECURE_PASSWORD_HASHING=true
```

**Example values from Render:**
```
DB_HOST=dpg-xxxxxx.render.com
DB_USER=english_portal_user
DB_PASSWORD=xxxxxxxxxxxxxxx
DB_NAME=english_portal
APP_ENV=production
APP_DEBUG=false
SECURE_PASSWORD_HASHING=true
```

5. Click **"Save Changes"**

## Step 6: Update PHP Configuration (if needed)

Create `render.yaml` in your project root:

```yaml
services:
  - type: web
    name: english-portal
    env: php
    buildCommand: composer install --no-dev
    startCommand: "apache2-foreground"
    envVars:
      - key: DB_HOST
        fromService:
          name: english-portal-db
          property: host
      - key: DB_USER
        value: portal_user
```

## Step 7: Deploy

1. Push changes to GitHub:
```bash
git add render.yaml
git commit -m "Add Render configuration"
git push
```

2. Render automatically deploys on push
3. Watch deployment in Render Dashboard
4. Check logs for errors

## Step 8: Migrate Passwords

After deployment:

1. SSH into Render (if available) OR
2. Use Render Shell in Dashboard:
   - Click Web Service
   - Click **"Shell"** tab
   - Run: `php migrate_passwords.php`

Or add to your deployment:
- Create `.render/build.sh`:
```bash
#!/bin/bash
php migrate_passwords.php
```

## Step 9: Test Your Application

1. Go to your Render Web Service URL
2. Visit: `https://english-portal-xxxx.render.com/login.php`
3. Test login with credentials:
   - Username: `vincent`
   - Password: `0717110505` (will work on first login, then hashed)

## Troubleshooting

### Database Connection Failed
- Verify environment variables match exactly
- Check database is running in Render
- Ensure `SECURE_PASSWORD_HASHING=true` for hashed passwords

### "Cannot connect to database"
```bash
# Test connection manually in Shell
mysql -h [DB_HOST] -u [DB_USER] -p[DB_PASSWORD] -e "USE english_portal; SELECT * FROM users;"
```

### Deployment Fails
- Check Build Logs in Render Dashboard
- Ensure `.env` is in `.gitignore`
- Verify `config.php` can load environment variables

### Application is Slow
- Upgrade from Free tier to Starter
- Check database size
- Enable caching in `config.php`

## Render Resources & Pricing

| Service | Price | Limits |
|---------|-------|--------|
| Web Service (Free) | $0 | Spins down after inactivity |
| Web Service (Starter) | $7/mo | Always running |
| MySQL (Free) | $0 | 100MB storage |
| MySQL (Starter) | $15/mo | 10GB storage |
| **Total** | | **~$22/month** |

## Additional Security Tips for Render

1. **Use Private GitHub Repo** if sensitive
2. **Enable Auto-Deploys** for latest code
3. **Set up Notifications** for deployment failures
4. **Regular Backups** - Export MySQL regularly
5. **Monitor Logs** - Watch for errors in Render dashboard

## Next Steps

- [x] Prepare GitHub repository
- [x] Create Render Web Service
- [x] Create MySQL database
- [x] Configure environment variables
- [x] Test application
- [ ] Monitor performance
- [ ] Set up backups
- [ ] Custom domain (optional)

---

Your app will be live at: `https://english-portal-xxxx.render.com`
