# Render Deployment - Quick Checklist

## Phase 1: Local Setup (5 min)
- [ ] Ensure `.env` is in `.gitignore`
- [ ] Ensure `.gitignore` includes: `migrate_passwords.php`, `testdb.php`, `check_errors.php`
- [ ] Run: `git init` in project folder
- [ ] Run: `git add .`
- [ ] Run: `git commit -m "Initial production build"`

## Phase 2: GitHub Setup (10 min)
- [ ] Create GitHub account at https://github.com
- [ ] Create new repository: `english-portal`
- [ ] Copy GitHub repository URL
- [ ] Run: `git remote add origin [YOUR_GITHUB_URL]`
- [ ] Run: `git push -u origin main`
- [ ] Verify code appears on GitHub (without `.env`)

## Phase 3: Render Setup (10 min)
- [ ] Sign up at https://render.com
- [ ] Click "New +" → "Web Service"
- [ ] Connect GitHub account
- [ ] Select `english-portal` repository
- [ ] Configure:
  - Name: `english-portal`
  - Environment: PHP
  - Instance: Free
  - Build Command: (leave empty)
  - Start Command: (leave empty)
- [ ] Click "Create Web Service"
- [ ] Wait for deployment to complete

## Phase 4: Database Setup (10 min)
- [ ] In Render Dashboard, click "New +" → "MySQL"
- [ ] Configure:
  - Name: `english-portal-db`
  - Database: `english_portal`
  - Username: `portal_user`
  - Instance: Free
- [ ] Wait ~5 minutes for database to start
- [ ] Copy internal connection string

## Phase 5: Environment Variables (5 min)
- [ ] Go to Web Service → Environment
- [ ] Add variables one by one:

```
DB_HOST = [from MySQL connection string]
DB_USER = portal_user
DB_PASSWORD = [from MySQL creation]
DB_NAME = english_portal
APP_ENV = production
APP_DEBUG = false
SECURE_PASSWORD_HASHING = true
```

- [ ] Save changes
- [ ] Monitor deployment (check logs)

## Phase 6: Database Import (5 min)
- [ ] Go to MySQL instance → "Connect" tab
- [ ] Use MySQL Workbench or command line:
   ```
   mysql -h [HOST] -u [USER] -p[PASSWORD] [DB] < english_portal.sql
   ```
- [ ] Verify tables imported successfully

## Phase 7: Password Migration (5 min)
- [ ] Go to Web Service → Shell tab
- [ ] Run: `php migrate_passwords.php`
- [ ] Verify successful output
- [ ] Verify users table has hashed passwords
- [ ] Delete migration file: `rm migrate_passwords.php`
- [ ] Commit and push to GitHub

## Phase 8: Testing (5 min)
- [ ] Get your Render URL: `https://english-portal-xxxx.render.com`
- [ ] Visit: `https://english-portal-xxxx.render.com/login.php`
- [ ] Test login with:
  - Username: `vincent`
  - Password: `0717110505`
- [ ] Verify student/teacher dashboard loads
- [ ] Check for errors in Render logs

## Phase 9: Security Review (5 min)
- [ ] Verify `.env` NOT in GitHub
- [ ] Confirm debug files NOT deployed
- [ ] Ensure `APP_DEBUG=false`
- [ ] Test HTTPS (should be automatic)

## Phase 10: Customization (Optional)
- [ ] Add custom domain (Render Dashboard → Settings)
- [ ] Enable auto-deploy notifications
- [ ] Set up database backups
- [ ] Monitor application logs

---

**Total Time: ~1 hour**

**Status**: ○ Not Started | ● In Progress | ✅ Complete

**Need Help?**
- Render Docs: https://render.com/docs
- MySQL Issues: Check Render MySQL logs
- PHP Issues: Check Render Web Service logs
