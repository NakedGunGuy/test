# HTTP Basic Authentication Setup

## Testing/Staging Credentials

The site is currently protected with HTTP Basic Authentication for testing purposes.

### Login Credentials:
- **Username:** `admin`
- **Password:** `cardpoint2025`

## Files Involved

1. **`.htpasswd`** - Contains encrypted password (located in project root)
2. **`public_html/.htaccess`** - Contains authentication directives (lines 5-13)

## Removing Authentication (When Going Live)

When you're ready to make the site publicly accessible:

### Option 1: Comment out the authentication section
Edit `public_html/.htaccess` and comment out lines 5-13:

```apache
# ====================
# HTTP BASIC AUTHENTICATION (TESTING/STAGING)
# ====================
# Remove or comment out this section when going live

# AuthType Basic
# AuthName "Cardpoint - Testing Access"
# AuthUserFile /home/perko/dev/projects/cardpoint/.htpasswd
# Require valid-user
```

### Option 2: Delete the authentication section
Simply delete lines 5-13 from `public_html/.htaccess`

## Adding New Users

To add additional users or change the password, use:

```bash
# If htpasswd is available:
htpasswd .htpasswd newusername

# If htpasswd is not available, use PHP:
php -r '$username = "newuser"; $password = "newpass"; echo $username . ":" . crypt($password, base64_encode($password)) . PHP_EOL;'
```

Then add the output line to `.htpasswd` file.

## Security Notes

- The `.htpasswd` file is stored outside the web root for security
- The `.htaccess` file is configured to deny access to `.htpasswd` files
- Make sure to remove or disable authentication before going fully live
- Consider using stronger passwords for production testing

## Testing

Visit your site URL and you should see a login prompt. Enter the credentials above to access the site.
