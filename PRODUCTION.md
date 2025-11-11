# Production Deployment Checklist

Complete checklist for deploying Cardpoint to a live production server.

## 🚀 Pre-Deployment Checklist

### 1. Environment Configuration

#### Required Steps:
- [ ] Copy `.env.example` to `.env` on production server
- [ ] Set `DEBUG=false` in production `.env`
- [ ] Set correct `APP_URL` (e.g., `https://yourdomain.com`)
- [ ] Generate strong `APP_SECRET` (32+ characters random string)
- [ ] Generate strong `CRON_SECRET_KEY` (use `openssl rand -base64 32`)
- [ ] Configure Stripe production keys (not test keys)
- [ ] Configure SMTP/Gmail settings for email delivery
- [ ] Review and update `MAIL_FROM_ADDRESS` and `MAIL_FROM_NAME`

#### Optional but Recommended:
- [ ] Set up Sentry or error monitoring service
- [ ] Configure CDN for static assets
- [ ] Set up automated backups

### 2. Database Setup

- [ ] Upload SQLite database to production server
- [ ] Verify database file permissions (readable by web server)
- [ ] Ensure database directory has write permissions
- [ ] Run `php console/create_admin_user.php` to create admin account
- [ ] Test admin login credentials
- [ ] Import initial card data: `php console/import_cards.php`

**Important:** Keep your local database as backup before deploying!

### 3. File Structure & Permissions

#### Upload Files:
```
Your Project Root (e.g., /home/username/cardpoint/)
├── core/              → Upload
├── views/             → Upload
├── routes/            → Upload
├── database/          → Upload (with database.sqlite)
├── content/           → Upload
├── mail/              → Upload
├── console/           → Upload
├── cards/             → Upload
├── logs/              → Create empty directory
├── vendor/            → Run composer install on server
├── bootstrap.php      → Upload
├── cron.php           → Upload
├── .env               → Create with production values
└── public_html/       → This is your web root
    ├── index.php      → Upload
    ├── css/           → Upload
    ├── js/            → Upload
    ├── assets/        → Upload
    └── .htaccess      → Upload
```

#### Set Permissions (via FTP or cPanel):

**Good news:** Most shared hosting providers set sensible default permissions automatically. You likely don't need to change anything!

**If you need to adjust permissions**, use one of these methods:

**Method 1: FileZilla (or other FTP client)**
1. Right-click on file/folder → File Permissions
2. Set the numeric value or check the boxes

**Method 2: cPanel File Manager**
1. Navigate to File Manager in cPanel
2. Right-click file/folder → Change Permissions
3. Set the numeric value

**Recommended Permissions:**

| Path | Permission | Numeric | Critical? |
|------|-----------|---------|-----------|
| `logs/` (directory) | Writable by server | 755 or 775 | ✓ Yes |
| `database/` (directory) | Writable by server | 755 or 775 | ✓ Yes |
| `database/database.sqlite` | Readable/writable by server | 644 or 664 | ✓ Yes |
| `.env` | Readable by server only | 600 or 644 | Optional* |
| `public_html/` (directory) | Readable by server | 755 | Auto-set |
| `.htaccess` files | Readable by server | 644 | Auto-set |
| PHP files | Readable by server | 644 | Auto-set |

***Note:** `.htaccess` already protects `.env` from web access, so 644 permissions are usually fine.

**Testing Permissions:**
After upload, try these tests:
1. Visit your site - if it loads, file permissions are OK
2. Try to register/login - if it works, database permissions are OK
3. Check logs directory is created - if yes, log permissions are OK

**Most shared hosts use default permissions like:**
- Directories: 755 (readable/executable by all, writable by owner)
- Files: 644 (readable by all, writable by owner)

These defaults work fine for most cases!

### 4. Security Configuration

#### .htaccess Security:
- [ ] Verify `public_html/.htaccess` is uploaded
- [ ] Verify root `.htaccess` is uploaded (denies access to parent directory)
- [ ] Uncomment HTTPS enforcement in `public_html/.htaccess` (lines 29-33)
- [ ] Test that `.env` file is not accessible via browser

#### SSL/HTTPS Setup:
- [ ] Install SSL certificate (Let's Encrypt recommended)
- [ ] Enable HTTPS enforcement in `.htaccess`
- [ ] Update `APP_URL` to use `https://`
- [ ] Test all pages load over HTTPS
- [ ] Verify mixed content warnings are resolved

#### Test Security:
```bash
# Try to access sensitive files (should get 403/404):
https://yourdomain.com/.env
https://yourdomain.com/../database/database.sqlite
https://yourdomain.com/.git/
https://yourdomain.com/logs/
```

### 5. Cronjob Setup

Follow the guide in `CRONJOBS.md` to set up automated tasks:

- [ ] Set up email queue processing (every 5 minutes)
- [ ] Set up sitemap generation (daily)
- [ ] Set up card image caching (weekly)
- [ ] Set up card data import (weekly/monthly)
- [ ] Test cronjobs run successfully

**Example cron configuration:**
```bash
*/5 * * * * php /home/username/cardpoint/cron.php emails YOUR_SECRET_KEY
0 3 * * * php /home/username/cardpoint/cron.php sitemap YOUR_SECRET_KEY
0 4 * * 0 php /home/username/cardpoint/cron.php cache-images YOUR_SECRET_KEY
```

### 6. Email Configuration

#### Gmail Setup (if using Gmail):
- [ ] Enable 2-factor authentication on Gmail account
- [ ] Generate app password for Cardpoint
- [ ] Configure SMTP settings in `.env`
- [ ] Send test email to verify configuration
- [ ] Review `GMAIL.md` for troubleshooting

#### Alternative Email Services:
Consider using dedicated email services for better deliverability:
- SendGrid (100 free emails/day)
- Mailgun (5,000 free emails/month)
- Amazon SES (very cheap, $0.10/1,000 emails)

### 7. Payment Processing

#### Stripe Production Setup:
- [ ] Switch from test keys to live keys in `.env`
- [ ] Verify webhook endpoint is configured in Stripe dashboard
- [ ] Test payment processing with real card
- [ ] Verify order confirmation emails are sent
- [ ] Check that Stripe dashboard shows payments correctly

**Important:** Always test with Stripe test mode first!

### 8. Performance Optimization

#### Server Configuration:
- [ ] Enable gzip compression (configured in `.htaccess`)
- [ ] Enable browser caching (configured in `.htaccess`)
- [ ] Optimize PHP settings (`memory_limit`, `max_execution_time`)
- [ ] Enable OPcache if available
- [ ] Run `php console/cache_card_images.php` to pre-cache images

#### CDN Setup (Optional):
- [ ] Configure CloudFlare or similar CDN
- [ ] Update asset URLs to use CDN
- [ ] Test that all assets load correctly

### 9. Monitoring & Logging

#### Log Monitoring:
- [ ] Verify logs directory is writable
- [ ] Check logs are being created:
  - `logs/app-YYYY-MM-DD.log`
  - `logs/error-YYYY-MM-DD.log`
  - `logs/security-YYYY-MM-DD.log`
  - `logs/php-errors.log`
- [ ] Set up log rotation (automatically handled, old logs deleted after 30 days)
- [ ] Monitor logs for errors regularly

#### Check Logs:
```bash
# View recent errors
tail -f logs/error-*.log

# View recent security events
tail -f logs/security-*.log

# View application logs
tail -f logs/app-*.log
```

#### Error Monitoring (Recommended):
- [ ] Set up Sentry.io for error tracking (optional)
- [ ] Configure uptime monitoring (UptimeRobot, Pingdom)
- [ ] Set up email alerts for critical errors

### 10. SEO Configuration

- [ ] Run `php console/generate_sitemap.php` to create sitemap
- [ ] Submit sitemap to Google Search Console: `https://yourdomain.com/sitemap.xml`
- [ ] Submit sitemap to Bing Webmaster Tools
- [ ] Verify all meta tags are correctly set
- [ ] Test Open Graph tags with Facebook Debugger
- [ ] Test Twitter Cards with Twitter Card Validator
- [ ] Add Google Analytics tracking code (optional)

### 11. Testing

#### Functional Testing:
- [ ] Test user registration
- [ ] Test user login/logout
- [ ] Test password reset flow
- [ ] Test product browsing and search
- [ ] Test adding items to cart
- [ ] Test checkout process
- [ ] Test payment processing
- [ ] Test order confirmation email
- [ ] Test admin login
- [ ] Test admin product management
- [ ] Test admin order management

#### Cross-Browser Testing:
- [ ] Chrome/Edge
- [ ] Firefox
- [ ] Safari
- [ ] Mobile browsers (iOS Safari, Chrome Mobile)

#### Security Testing:
- [ ] Verify sensitive files are not accessible
- [ ] Test SQL injection prevention
- [ ] Test XSS prevention
- [ ] Verify HTTPS enforcement
- [ ] Test session security
- [ ] Verify password hashing is working

### 12. Backup Strategy

#### Initial Backup:
- [ ] Create full backup of database before going live
- [ ] Backup all uploaded files
- [ ] Store backups securely off-server

#### Automated Backups:
- [ ] Set up daily database backups via cron
- [ ] Set up weekly full site backups
- [ ] Test restore process

**Example backup cron:**
```bash
# Daily database backup at 2am
0 2 * * * cp /home/username/cardpoint/database/database.sqlite /home/username/backups/db-$(date +\%Y\%m\%d).sqlite
```

### 13. DNS & Domain Configuration

- [ ] Point domain to hosting server (A record)
- [ ] Configure www subdomain (CNAME or A record)
- [ ] Set up SPF record for email deliverability
- [ ] Set up DKIM record (if using Gmail or dedicated service)
- [ ] Set up DMARC record
- [ ] Wait for DNS propagation (24-48 hours)

### 14. Legal & Compliance

- [ ] Add privacy policy page
- [ ] Add terms of service page
- [ ] Add shipping policy page
- [ ] Add return/refund policy page
- [ ] Verify GDPR compliance if serving EU customers
- [ ] Add cookie consent banner if required
- [ ] Set up business email addresses

## 📋 Post-Deployment Checklist

### Immediate (First 24 Hours):

- [ ] Monitor error logs closely
- [ ] Watch for failed payment attempts
- [ ] Monitor email delivery
- [ ] Check server resource usage
- [ ] Verify cronjobs are running
- [ ] Test all critical user flows

### First Week:

- [ ] Review analytics data
- [ ] Monitor conversion rates
- [ ] Check for broken links
- [ ] Review customer feedback
- [ ] Optimize slow pages
- [ ] Review security logs

### Ongoing Maintenance:

- [ ] Weekly: Review error logs
- [ ] Weekly: Check order processing
- [ ] Monthly: Review and update card database
- [ ] Monthly: Security updates and patches
- [ ] Monthly: Performance optimization
- [ ] Quarterly: Full security audit
- [ ] Yearly: SSL certificate renewal (if not auto-renewing)

## 🔧 Common Issues & Solutions

### Issue: Site shows PHP errors
**Solution:** Set `DEBUG=false` in `.env`

### Issue: Database errors
**Solution:** Check file permissions on database directory and file

### Issue: Emails not sending
**Solution:** Verify SMTP configuration, check error logs, review `GMAIL.md`

### Issue: Cronjobs not running
**Solution:** Verify PHP path, check cron secret key matches `.env`

### Issue: Images not loading
**Solution:** Check file permissions, verify asset paths, run image cache script

### Issue: Stripe payments failing
**Solution:** Verify using production keys, check webhook configuration

### Issue: Slow page loads
**Solution:** Enable OPcache, optimize images, enable CDN, check server resources

## 📞 Support Resources

### Hosting Issues:
- Contact your hosting provider's support
- Check cPanel documentation
- Review hosting provider's knowledge base

### Payment Issues:
- Stripe Dashboard: https://dashboard.stripe.com
- Stripe Documentation: https://stripe.com/docs
- Stripe Support: https://support.stripe.com

### Email Issues:
- Gmail Support: https://support.google.com/mail
- Review `GMAIL.md` in project root
- Consider using dedicated email service

### General PHP/Server Issues:
- PHP Documentation: https://php.net/docs
- Stack Overflow: https://stackoverflow.com
- Check error logs first!

## 🎉 Launch Checklist

Final steps before announcing your store:

- [ ] All testing completed and passed
- [ ] SSL certificate installed and working
- [ ] Payment processing tested with real transaction
- [ ] Email notifications tested
- [ ] Backup system verified
- [ ] Monitoring systems active
- [ ] Legal pages published
- [ ] Contact information added
- [ ] Social media accounts set up
- [ ] Google Analytics configured
- [ ] Marketing materials prepared

---

## Production Environment Variables Template

Save this as your production `.env`:

```env
# Application
APP_NAME="Cardpoint"
APP_URL="https://yourdomain.com"
APP_SECRET="[GENERATE-32-CHARACTER-RANDOM-STRING]"
DEBUG=false

# Stripe (PRODUCTION KEYS)
STRIPE_SECRET_KEY="sk_live_..."
STRIPE_PUBLISHABLE_KEY="pk_live_..."

# Database
DB_PATH="database/database.sqlite"

# Email (Gmail or SMTP)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME=Cardpoint

# Cron Security
CRON_SECRET_KEY="[GENERATE-32-CHARACTER-RANDOM-STRING]"
```

---

**Remember:** Always keep a backup before making changes, and test thoroughly before going live!

Good luck with your launch! 🚀
