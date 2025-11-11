# Cronjob Setup Guide for Shared Hosting

This guide explains how to set up automated tasks (cronjobs) for Cardpoint on shared hosting environments.

## Prerequisites

- Access to cPanel or your hosting control panel
- PHP CLI access (most shared hosting providers support this)
- Your Cardpoint project uploaded to the server

## Security Setup

### 1. Generate a Secret Key

Before setting up cronjobs, you need to generate a secure secret key:

**Option A: Using OpenSSL (if available on your host)**
```bash
openssl rand -base64 32
```

**Option B: Using a random string generator**
Visit a password generator website and create a 32+ character random string.

### 2. Add Secret Key to .env

Edit your `.env` file and add the generated secret key:

```env
CRON_SECRET_KEY="your-generated-secret-key-here"
```

**IMPORTANT:** Never share this key or commit it to version control!

## Setting Up Cronjobs in cPanel

### Step 1: Access Cron Jobs

1. Log in to your cPanel
2. Find and click on "Cron Jobs" (usually under "Advanced" section)

### Step 2: Configure Cronjobs

Add the following cron jobs based on your needs. Replace placeholders with your actual values:

- `/home/username/` - Your hosting account username path
- `YOUR_SECRET_KEY` - The CRON_SECRET_KEY from your .env file

#### Email Queue Processing (Every 5 minutes)

**When to use:** If you have email notifications enabled (order confirmations, password resets, etc.)

**Schedule:** Every 5 minutes

**Command:**
```bash
*/5 * * * * php /home/username/cardpoint/cron.php emails YOUR_SECRET_KEY
```

#### Sitemap Generation (Daily at 3:00 AM)

**When to use:** To keep your sitemap updated for search engines

**Schedule:** Daily at 3:00 AM

**Command:**
```bash
0 3 * * * php /home/username/cardpoint/cron.php sitemap YOUR_SECRET_KEY
```

#### Card Image Caching (Weekly on Sunday at 4:00 AM)

**When to use:** To pre-cache card images for faster loading

**Schedule:** Every Sunday at 4:00 AM

**Command:**
```bash
0 4 * * 0 php /home/username/cardpoint/cron.php cache-images YOUR_SECRET_KEY
```

#### Card Data Import (Weekly on Monday at 2:00 AM)

**When to use:** To import new cards and updates from the Grand Archive API

**Schedule:** Every Monday at 2:00 AM

**Command:**
```bash
0 2 * * 1 php /home/username/cardpoint/cron.php import-cards YOUR_SECRET_KEY
```

## Cron Schedule Format

The cron schedule format is: `minute hour day month weekday command`

- `*` = every unit (e.g., every minute, every hour)
- `*/5` = every 5 units (e.g., every 5 minutes)
- `0` = at zero (e.g., at the top of the hour)
- `1-5` = range (e.g., Monday through Friday)
- `1,3,5` = specific values (e.g., Monday, Wednesday, Friday)

### Common Examples

```bash
# Every 5 minutes
*/5 * * * *

# Every hour at minute 0
0 * * * *

# Every day at 3:00 AM
0 3 * * *

# Every Sunday at 4:00 AM
0 4 * * 0

# Every Monday at 2:00 AM
0 2 * * 1

# Twice a day (6 AM and 6 PM)
0 6,18 * * *

# Every weekday at 9 AM
0 9 * * 1-5
```

## Testing Your Cronjobs

### Manual Testing via SSH (if available)

```bash
cd /home/username/cardpoint
php cron.php emails YOUR_SECRET_KEY
```

### Manual Testing via cPanel Terminal (if available)

Same commands as above, run in the cPanel terminal.

### Check Cron Logs

Most cPanel setups allow you to view cron execution logs:
1. In cPanel, look for "Cron Jobs"
2. Scroll down to see recent cron job executions
3. Check for any error messages

## Troubleshooting

### Common Issues

**Issue:** "CRON_SECRET_KEY not set in .env file"
- **Solution:** Make sure your .env file contains `CRON_SECRET_KEY="your-key"`

**Issue:** "Invalid secret key"
- **Solution:** Verify you're using the exact same key from your .env file (no extra spaces)

**Issue:** "This script can only be run from command line"
- **Solution:** Make sure you're using `php` command, not accessing via URL

**Issue:** Cronjobs not running
- **Solution:** Check that the PHP path is correct. Try `which php` or `/usr/bin/php` instead of `php`

**Issue:** Permission denied
- **Solution:** Make sure `cron.php` has execute permissions: `chmod +x cron.php`

### Finding Your PHP Path

If `php` doesn't work, you may need the full path. Try these commands:

```bash
which php
which php8.3
which php8.2
which php8.1
/usr/bin/php -v
```

Then use the full path in your cron commands:
```bash
*/5 * * * * /usr/bin/php /home/username/cardpoint/cron.php emails YOUR_SECRET_KEY
```

## Recommended Cronjob Setup

For a production e-commerce site, we recommend:

1. **Email Queue** - Every 5 minutes (essential for timely notifications)
2. **Sitemap** - Daily at 3 AM (good for SEO)
3. **Card Images** - Weekly (reduces server load)
4. **Card Import** - Weekly or monthly (depending on how often new cards are released)

## Security Notes

- The `cron.php` file is located in your project root (NOT in public_html)
- This means it's not web-accessible and can only be run via command line
- Always use a strong, random CRON_SECRET_KEY
- Never expose your secret key in public repositories
- The secret key prevents unauthorized execution of maintenance tasks

## Alternative: Web-Based Cron Alternative

If your host doesn't support cron jobs well, you can use external services like:

- [EasyCron](https://www.easycron.com/) (free tier available)
- [cron-job.org](https://cron-job.org/) (free)
- [Cronitor](https://cronitor.io/) (free tier available)

These services can make HTTP requests to a web-accessible endpoint on a schedule.

**Note:** If you need a web-accessible cron endpoint, let me know and I can create a secure webhook system for you.
