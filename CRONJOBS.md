# Cronjob Setup Guide for Shared Hosting

This guide explains how to set up automated tasks (cronjobs) for Cardpoint on shared hosting environments.

## 🎯 Simple Setup (Recommended)

**NEW:** You only need **ONE cronjob** that runs every minute!

The master cronjob checks the time and runs tasks automatically based on your schedule. Much simpler than managing multiple cronjobs!

Jump to: [Quick Setup](#quick-setup-one-cronjob) | [Advanced Setup](#advanced-setup-multiple-cronjobs)

---

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

---

## Quick Setup (One Cronjob)

### Recommended for Most Users

This is the **easiest way** to set up cronjobs. You only need to create ONE cronjob in cPanel!

### Step 1: Configure Your Secret Key

Make sure your `.env` file has a `CRON_SECRET_KEY`:

```env
CRON_SECRET_KEY="your-generated-secret-key-here"
```

### Step 2: Add Single Cronjob in cPanel

1. Log in to your cPanel
2. Find and click on "Cron Jobs" (usually under "Advanced" section)
3. Add **ONE cronjob** with these settings:

**Common Settings:**
- Minute: `*` (every minute)
- Hour: `*`
- Day: `*`
- Month: `*`
- Weekday: `*`

**Command:**
```bash
php /home/username/cardpoint/cron_master.php YOUR_SECRET_KEY
```

Replace:
- `username` with your hosting username
- `YOUR_SECRET_KEY` with your actual `CRON_SECRET_KEY` from `.env`

**If `/usr/bin/php` doesn't work**, the command might need to be:
```bash
/usr/bin/php /home/username/cardpoint/cron_master.php YOUR_SECRET_KEY
```

Or try:
```bash
/usr/local/bin/php /home/username/cardpoint/cron_master.php YOUR_SECRET_KEY
```

### Step 3: Save and You're Done! ✓

That's it! The master cronjob will automatically run:
- **Email queue** - Every 5 minutes
- **Sitemap generation** - Daily at 3:00 AM
- **Card image caching** - Every Sunday at 4:00 AM
- **Card data import** - Every Monday at 2:00 AM
- **Log cleanup** - Monthly on the 1st at 5:00 AM

### Customizing the Schedule

Want to change when tasks run? Edit `cron_master.php` and modify the `$schedule` array:

```php
$schedule = [
    'emails' => [
        'frequency' => 'every_5_minutes',  // Can be: every_5_minutes, every_10_minutes, every_15_minutes, hourly
        'script' => __DIR__ . '/console/send_emails.php',
        'description' => 'Process email queue'
    ],
    'sitemap' => [
        'frequency' => 'daily',
        'time' => '03:00',  // Change this to run at different time
        'script' => __DIR__ . '/console/generate_sitemap.php',
        'description' => 'Generate XML sitemap'
    ],
    // ... more tasks
];
```

**Frequency options:**
- `every_5_minutes`, `every_10_minutes`, `every_15_minutes` - For frequent tasks
- `hourly` - Every hour on the hour
- `daily` - Once per day at specified `time`
- `weekly` - Once per week on specified `day` and `time`
- `monthly` - Once per month on specified `day` (1-31) and `time`

---

## Advanced Setup (Multiple Cronjobs)

### For Advanced Users Who Want Fine Control

If you prefer separate cronjobs for each task, you can use the individual `cron.php` script.

### Step 1: Access Cron Jobs

1. Log in to your cPanel
2. Find and click on "Cron Jobs" (usually under "Advanced" section)

### Step 2: Configure Individual Cronjobs

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

#### Log Cleanup (Monthly on 1st at 5:00 AM)

**When to use:** To archive old log files and keep the logs directory clean

**Schedule:** Monthly on the 1st at 5:00 AM

**Command:**
```bash
0 5 1 * * php /home/username/cardpoint/cron.php cleanup-logs YOUR_SECRET_KEY
```

**Note:** Old logs are moved to `logs/archive/` folder, not deleted. You can manually delete archived logs if needed.

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

## Which Setup Should I Use?

### ✅ Use Master Cronjob (Recommended) If:
- You want the simplest setup
- You have limited cronjob slots in cPanel
- You want to easily change schedules later
- You're new to cronjobs

### ⚙️ Use Individual Cronjobs If:
- You need fine-grained control over each task
- You want different error handling per task
- You have unlimited cronjob slots
- You're comfortable with cPanel cronjobs

**For most users, the master cronjob is the best choice!**

## Default Task Schedule

The master cronjob runs tasks on this schedule:

1. **Email Queue** - Every 5 minutes (essential for timely notifications)
2. **Sitemap** - Daily at 3:00 AM (good for SEO)
3. **Card Images** - Weekly on Sunday at 4:00 AM (reduces server load)
4. **Card Import** - Weekly on Monday at 2:00 AM (new cards from API)
5. **Log Cleanup** - Monthly on 1st at 5:00 AM (keeps logs organized)

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
