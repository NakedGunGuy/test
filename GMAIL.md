# Gmail SMTP Configuration Guide

This guide explains how to configure Cardpoint to send emails using Gmail's SMTP server.

## Prerequisites

- A Gmail account (regular Gmail or Google Workspace)
- Admin access to your Cardpoint installation

## Important Gmail Limits

**Regular Gmail Account:**
- **500 emails per day** (rolling 24-hour period)
- **10MB per email** maximum size

**Google Workspace Account:**
- **2,000 emails per day** per user
- **25MB per email** maximum size

**Note:** If you exceed these limits, your account may be temporarily suspended from sending emails.

## Step 1: Enable 2-Factor Authentication

Gmail requires 2-factor authentication (2FA) to use app passwords.

1. Go to https://myaccount.google.com/security
2. Under "How you sign in to Google," select **2-Step Verification**
3. Follow the steps to enable 2FA if not already enabled

## Step 2: Generate App Password

**Important:** Do NOT use your regular Gmail password. You must create an app-specific password.

1. Go to https://myaccount.google.com/apppasswords
   - Or navigate: Google Account → Security → 2-Step Verification → App passwords
2. You may need to sign in again
3. Select **Mail** and **Other (Custom name)**
4. Enter a name like "Cardpoint Store"
5. Click **Generate**
6. Google will show you a 16-character app password
7. **Copy this password immediately** - you won't be able to see it again

The app password will look like: `abcd efgh ijkl mnop` (remove spaces when using)

## Step 3: Configure Cardpoint

Edit your `.env` file and add the following configuration:

### For TLS (Port 587) - Recommended

```env
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=abcdefghijklmnop
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME=Cardpoint
```

### For SSL (Port 465) - Alternative

```env
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=abcdefghijklmnop
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME=Your Store Name
```

### Configuration Details

- **MAIL_HOST**: Always `smtp.gmail.com`
- **MAIL_PORT**: Use `587` for TLS or `465` for SSL
- **MAIL_USERNAME**: Your full Gmail address
- **MAIL_PASSWORD**: The 16-character app password (remove spaces)
- **MAIL_ENCRYPTION**: `tls` for port 587, `ssl` for port 465
- **MAIL_FROM_ADDRESS**: Your Gmail address (must match MAIL_USERNAME)
- **MAIL_FROM_NAME**: Your store name (appears as sender name)

## Step 4: Test Email Configuration

### Test via Admin Panel

1. Log into your admin panel
2. Navigate to Settings → Email Test (if available)
3. Send a test email to verify configuration

### Test via Console

Run the email queue processor manually to test:

```bash
php console/send_emails.php
```

If configured correctly, you should see emails being sent without errors.

## Troubleshooting

### Error: "SMTP connect() failed"

**Possible causes:**
1. Incorrect username or password
2. App password not generated
3. 2FA not enabled
4. Firewall blocking port 587/465

**Solutions:**
- Verify your Gmail address and app password
- Ensure 2FA is enabled on your Google account
- Generate a new app password if needed
- Contact your hosting provider to ensure SMTP ports aren't blocked

### Error: "Invalid credentials"

**Solution:**
- Regenerate your app password at https://myaccount.google.com/apppasswords
- Make sure to remove spaces from the app password
- Verify MAIL_USERNAME matches the Gmail account

### Error: "Could not authenticate"

**Solution:**
- Ensure 2-Step Verification is enabled
- Try generating a new app password
- Wait 10-15 minutes after creating app password (propagation delay)

### Error: "Daily sending quota exceeded"

**Solution:**
- You've hit Gmail's 500/day limit (or 2000/day for Workspace)
- Wait 24 hours for the limit to reset
- Consider upgrading to Google Workspace
- Consider using a dedicated email service (see alternatives below)

### Emails going to spam

**Solutions:**
1. **SPF Record**: Add to your domain's DNS:
   ```
   v=spf1 include:_spf.google.com ~all
   ```

2. **DKIM**: Set up DKIM in Google Workspace Admin Console

3. **DMARC**: Add DMARC record to DNS:
   ```
   v=DMARC1; p=none; rua=mailto:your-email@gmail.com
   ```

4. Ask recipients to add your email to their contacts

## Security Best Practices

1. **Never commit .env file** to version control
2. **Use unique app passwords** for each application
3. **Revoke unused app passwords** regularly
4. **Monitor your sent mail** for suspicious activity
5. **Use a dedicated Gmail account** for your store (not your personal account)

## Better Alternatives for Production

While Gmail works well for small stores, consider these alternatives for better deliverability and higher limits:

### Recommended Services

**1. SendGrid**
- **Free tier**: 100 emails/day
- **Paid**: Starting at $15/month for 40,000 emails
- **Pros**: Excellent deliverability, detailed analytics, easy integration
- **Website**: https://sendgrid.com

**2. Mailgun**
- **Free tier**: 5,000 emails/month
- **Paid**: Pay-as-you-go starting at $0.80/1,000 emails
- **Pros**: Developer-friendly, powerful API, good documentation
- **Website**: https://mailgun.com

**3. Amazon SES**
- **Pricing**: $0.10 per 1,000 emails
- **Pros**: Very cheap, reliable, integrates with AWS
- **Cons**: Requires AWS account, more technical setup
- **Website**: https://aws.amazon.com/ses/

**4. Postmark**
- **Paid**: Starting at $15/month for 10,000 emails
- **Pros**: Excellent support, great deliverability, transactional focus
- **Website**: https://postmarkapp.com

**5. Brevo (formerly Sendinblue)**
- **Free tier**: 300 emails/day
- **Paid**: Starting at $25/month for 20,000 emails
- **Pros**: Includes SMS and marketing automation
- **Website**: https://brevo.com

## When to Move Beyond Gmail

Consider switching from Gmail if:
- You need to send more than 400 emails per day
- Your emails are frequently going to spam
- You need detailed delivery analytics
- You want better email deliverability rates
- You need dedicated IP address
- You require technical support for email issues

## Using Google Workspace

If you have a Google Workspace account, you get:
- **2,000 emails per day** (vs 500 for regular Gmail)
- Custom domain email (you@yourdomain.com)
- Better business reputation
- Same SMTP configuration as Gmail

**Cost:** Starting at $6/user/month

**Setup:** Same as Gmail, but use your workspace email:
```env
MAIL_USERNAME=you@yourdomain.com
MAIL_FROM_ADDRESS=you@yourdomain.com
```

## Additional Resources

- [Google App Passwords Help](https://support.google.com/accounts/answer/185833)
- [Gmail SMTP Settings](https://support.google.com/mail/answer/7126229)
- [Email Authentication (SPF, DKIM, DMARC)](https://support.google.com/a/answer/33786)

## Support

If you continue having issues:
1. Check Gmail's error messages carefully
2. Verify your app password is correct (regenerate if needed)
3. Ensure your hosting provider allows SMTP connections
4. Check your error logs: `tail -f /path/to/php-error.log`
5. Enable debug mode in mailer.php (set `$mail->SMTPDebug = 2;`)

---

**Remember:** For production e-commerce stores, dedicated email services like SendGrid or Mailgun are recommended over Gmail for better deliverability and higher sending limits.
