# FTP Deployment Guide

Simple step-by-step guide for deploying Cardpoint to shared hosting using only FTP and cPanel.

## What You'll Need

- FTP credentials from your hosting provider
- cPanel access
- FileZilla or similar FTP client (free download: https://filezilla-project.org/)

## Step 1: Prepare Your Files Locally

### On Your Local Machine:

1. **Create production `.env` file:**
   - Copy `.env.example` to `.env`
   - Edit `.env` with production settings:
     ```env
     APP_NAME="Cardpoint"
     APP_URL="https://yourdomain.com"
     DEBUG=false
     # ... add your Stripe, Gmail, etc. settings
     ```

2. **Install dependencies (if not already done):**
   ```bash
   composer install --no-dev
   ```

3. **Create necessary directories:**
   - Make sure `logs/` and `logs/archive/` exist (they should)
   - Make sure `database/` has your database file

## Step 2: Connect via FTP

### Using FileZilla:

1. Open FileZilla
2. Enter your FTP credentials:
   - **Host:** ftp.yourdomain.com (or IP address from your host)
   - **Username:** Your FTP username
   - **Password:** Your FTP password
   - **Port:** 21 (or 22 for SFTP if available)
3. Click "Quickconnect"

### Find Your Directory Structure:

You'll see something like:
```
/home/yourusername/
├── public_html/     ← Your web root
├── logs/            ← Create this directory
├── mail/            ← Create this directory
└── ...
```

## Step 3: Upload Files

### What Goes Where:

**Upload to `/home/yourusername/` (parent of public_html):**

Drag these folders/files from your local project:
- `core/` folder
- `views/` folder
- `routes/` folder
- `database/` folder (with your database.sqlite file)
- `content/` folder
- `mail/` folder
- `console/` folder
- `cards/` folder
- `vendor/` folder (after running composer install)
- `bootstrap.php` file
- `cron.php` file
- `cron_master.php` file ← **NEW: Master cronjob runner**
- `.env` file (your production version)
- `.htaccess` file (the one in project root)

**Upload to `/home/yourusername/public_html/`:**

Drag these folders/files from your local `public_html/`:
- `index.php` file
- `.htaccess` file
- `robots.txt` file
- `css/` folder
- `js/` folder
- `assets/` folder
- `icons/` folder (if you have one)

**Create Empty Directories (if not exist):**
- `/home/yourusername/logs/`
- `/home/yourusername/logs/archive/`

### FileZilla Tips:

- **Left side** = Your local computer
- **Right side** = Your server
- **Drag and drop** to upload files
- **Progress** shown at bottom
- You can **right-click → Upload** as well

## Step 4: Set Permissions (if needed)

### In FileZilla:

Right-click on folder/file → **File permissions** (or File Attributes)

**Only change these if you have issues:**

1. **logs folder:**
   - Right-click `logs/` → File Permissions
   - Enter: `755` or check: Owner: Read+Write+Execute, Group: Read+Execute, Public: Read+Execute
   - Check: "Recurse into subdirectories"

2. **database folder:**
   - Right-click `database/` → File Permissions
   - Enter: `755`

3. **database.sqlite file:**
   - Right-click `database/database.sqlite` → File Permissions
   - Enter: `644` (Owner: Read+Write, Group: Read, Public: Read)

**Most of the time, default permissions are fine!** Only change if you get errors.

## Step 5: Initial Setup via cPanel

### 5.1: Create Admin User

In cPanel, find **Terminal** (if available) or use **Cron Jobs** to run once:

If Terminal is available:
```bash
cd ~/cardpoint
php console/create_admin_user.php
```

**If no Terminal access**, you'll need to:
1. Create a temporary PHP file in public_html called `setup.php`:
   ```php
   <?php
   require_once __DIR__ . '/../bootstrap.php';
   require_once CORE_PATH . '/autoload.php';

   // Create admin user
   if (!isset($_GET['confirm'])) {
       die('Add ?confirm=yes to URL to run setup');
   }

   require_once __DIR__ . '/../console/create_admin_user.php';
   echo "Done! DELETE THIS FILE NOW!";
   ?>
   ```
2. Visit: `https://yourdomain.com/setup.php?confirm=yes`
3. Follow prompts to create admin user
4. **IMMEDIATELY DELETE setup.php after use!**

### 5.2: Import Card Data

Same as above - create temporary setup file or use Terminal:
```php
<?php
require_once __DIR__ . '/../bootstrap.php';
require_once CORE_PATH . '/autoload.php';

if (!isset($_GET['confirm'])) {
    die('Add ?confirm=yes to URL to run setup');
}

require_once __DIR__ . '/../console/import_cards.php';
echo "Done! DELETE THIS FILE NOW!";
?>
```

Visit: `https://yourdomain.com/import.php?confirm=yes`

**Remember to delete these setup files after using them!**

## Step 6: Set Up Cronjob in cPanel

### Easy Setup - Just ONE Cronjob!

Good news: You only need **ONE cronjob** that runs everything!

### In cPanel → Cron Jobs:

1. Find "Cron Jobs" in cPanel
2. Look for "Add New Cron Job" section
3. Set these values:

**Common Settings (select from dropdowns):**
- Minute: Select `*` or type `*`
- Hour: Select `*` or type `*`
- Day: Select `*` or type `*`
- Month: Select `*` or type `*`
- Weekday: Select `*` or type `*`

**Command (type in the text box):**
```bash
php /home/yourusername/cardpoint/cron_master.php YOUR_SECRET_KEY
```

**Replace:**
- `yourusername` with your actual cPanel username (check the path in File Manager)
- `YOUR_SECRET_KEY` with the `CRON_SECRET_KEY` from your `.env` file

**Example:**
```bash
php /home/john123/cardpoint/cron_master.php cron_d8f7a9b2e4c6f1a3d5e7b9c2a4f6e8d0
```

**If `php` doesn't work**, try:
```bash
/usr/bin/php /home/yourusername/cardpoint/cron_master.php YOUR_SECRET_KEY
```

Or:
```bash
/usr/local/bin/php /home/yourusername/cardpoint/cron_master.php YOUR_SECRET_KEY
```

4. Click **"Add New Cron Job"**

### What This Does

The master cronjob runs **every minute** and automatically handles:
- ✅ **Email queue** - Processes every 5 minutes
- ✅ **Sitemap** - Generates daily at 3:00 AM
- ✅ **Cache images** - Updates every Sunday at 4:00 AM
- ✅ **Import cards** - Updates every Monday at 2:00 AM
- ✅ **Archive logs** - Cleans up monthly on 1st at 5:00 AM

No need to set up multiple cronjobs!

## Step 7: Configure SSL/HTTPS

### In cPanel → SSL/TLS:

1. **Install SSL Certificate:**
   - Find "SSL/TLS" in cPanel
   - Use "Let's Encrypt" (usually free and automatic)
   - Or install custom SSL certificate

2. **Enable HTTPS Redirect:**
   - After SSL is installed, edit `public_html/.htaccess`
   - Uncomment these lines (remove the `#`):
     ```apache
     # Uncomment for production with SSL:
     <IfModule mod_rewrite.c>
         RewriteEngine On
         RewriteCond %{HTTPS} off
         RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
     </IfModule>
     ```

## Step 8: Test Everything

### Basic Tests:

1. **Visit your site:** `https://yourdomain.com`
   - Should load without errors

2. **Try to access .env:** `https://yourdomain.com/../.env`
   - Should get 403 Forbidden (good!)

3. **Try to access database:** `https://yourdomain.com/../database/database.sqlite`
   - Should get 403 Forbidden (good!)

4. **Test registration:**
   - Create a new user account
   - Should receive confirmation email

5. **Test admin login:**
   - Go to `/admin`
   - Login with admin credentials

6. **Test shopping:**
   - Add product to cart
   - Checkout process
   - Test payment (use Stripe test cards first!)

7. **Check logs:**
   - Via FTP, browse to `logs/` directory
   - Should see log files being created

## Step 9: Monitor for Issues

### First 24 Hours:

**Check logs via FTP:**
1. Download `logs/error-YYYY-MM-DD.log`
2. Open in text editor
3. Look for errors

**Check via cPanel File Manager:**
1. Navigate to `cardpoint/logs/`
2. View/download error logs
3. Check for issues

### Common Issues:

**Issue: White screen / 500 error**
- Check `logs/error-*.log` for details
- Verify `.env` has `DEBUG=false`
- Check database file permissions

**Issue: Database errors**
- Right-click `database/database.sqlite` → Permissions → `644` or `664`
- Right-click `database/` folder → Permissions → `755` or `775`

**Issue: Can't write logs**
- Right-click `logs/` folder → Permissions → `755` or `775`

**Issue: Images not loading**
- Check `public_html/assets/` uploaded correctly
- Check paths in code match your structure

## Updating Your Site Later

### To Deploy Updates:

1. **Always backup first!**
   - Download `database/database.sqlite` via FTP
   - Keep a backup copy

2. **Upload changed files only:**
   - Use FileZilla to upload only files you changed
   - Common files to update:
     - `core/` - if you updated core code
     - `views/` - if you updated templates
     - `public_html/css/` - if you updated styles
     - `public_html/js/` - if you updated scripts

3. **Test after each update**

## Helpful FTP Tips

### FileZilla Bookmarks:
- After connecting, go to: File → Site Manager
- Click "New Site"
- Save your connection settings
- Quick reconnect anytime!

### Transfer Settings:
- Use **Binary** transfer mode (usually automatic)
- For large databases, use **Resume** if transfer fails

### Synchronize Directories:
- Right-click on server directory → Synchronized browsing
- Automatically matches local and remote directories

## Getting Help

### Where to Check:

1. **Error Logs:** `logs/error-*.log`
2. **cPanel Error Log:** In cPanel → Error Log
3. **PHP Error Log:** `logs/php-errors.log`

### Contact Support If:

- Can't find correct directory structure
- Can't find PHP path for cronjobs
- SSL certificate won't install
- Database permissions won't change
- FTP won't connect

Most hosting providers have 24/7 support and can help with these!

---

## Quick Reference: File Structure

```
/home/yourusername/               ← FTP: Root directory
├── cardpoint/                    ← Create this folder OR upload directly to root
│   ├── core/                     ← Upload
│   ├── views/                    ← Upload
│   ├── routes/                   ← Upload
│   ├── database/                 ← Upload
│   ├── logs/                     ← Create empty
│   │   └── archive/              ← Create empty
│   ├── mail/                     ← Upload
│   ├── console/                  ← Upload
│   ├── cards/                    ← Upload
│   ├── vendor/                   ← Upload
│   ├── bootstrap.php             ← Upload
│   ├── cron.php                  ← Upload
│   ├── .env                      ← Upload (production version)
│   └── .htaccess                 ← Upload (root protection)
└── public_html/                  ← Web root (already exists)
    ├── index.php                 ← Upload
    ├── .htaccess                 ← Upload (security rules)
    ├── robots.txt                ← Upload
    ├── css/                      ← Upload
    ├── js/                       ← Upload
    └── assets/                   ← Upload
```

**Important:** You can either:
- Upload everything to `/home/yourusername/cardpoint/` and `public_html/`
- OR upload directly to `/home/yourusername/` (with core, views, etc.) and `public_html/`

Both work fine!

---

You got this! 🚀 Most of it is just drag-and-drop in FileZilla. The hosting provider handles permissions automatically in 99% of cases.
