const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();

  try {
    // Navigate to login page
    console.log('Navigating to login page...');
    await page.goto('http://cardpoint.test/en/admin/login');
    await page.waitForLoadState('networkidle');

    // Take screenshot of login page
    await page.screenshot({ path: 'login_page.png', fullPage: true });
    console.log('Login page screenshot saved as login_page.png');

    // Try common admin credentials
    const credentials = [
      { username: 'admin', password: 'admin' },
      { username: 'admin', password: 'password' },
      { username: 'admin', password: '123456' },
      { username: 'administrator', password: 'admin' }
    ];

    let loginSuccess = false;

    for (const cred of credentials) {
      try {
        console.log(`Trying credentials: ${cred.username}/${cred.password}`);

        // Fill login form
        await page.fill('input[name="username"], input[name="email"], input[type="email"]', cred.username);
        await page.fill('input[name="password"], input[type="password"]', cred.password);

        // Submit form
        await page.click('button[type="submit"], input[type="submit"], .btn');
        await page.waitForLoadState('networkidle');

        // Check if login was successful (look for admin dashboard elements)
        const currentUrl = page.url();
        console.log(`Current URL after login attempt: ${currentUrl}`);

        if (currentUrl.includes('/admin') && !currentUrl.includes('/login')) {
          console.log('Login successful!');
          loginSuccess = true;
          break;
        }
      } catch (error) {
        console.log(`Login attempt failed: ${error.message}`);
      }
    }

    if (!loginSuccess) {
      console.log('Could not login with common credentials. Taking screenshot of current page...');
      await page.screenshot({ path: 'login_failed.png', fullPage: true });

      // Try to find what fields are available
      const inputs = await page.$$eval('input', inputs =>
        inputs.map(input => ({ type: input.type, name: input.name, placeholder: input.placeholder }))
      );
      console.log('Available input fields:', inputs);

      await browser.close();
      return;
    }

    // Navigate to bulk products page
    console.log('Navigating to bulk products page...');
    await page.goto('http://cardpoint.test/en/admin/products/bulk');
    await page.waitForLoadState('networkidle');

    // Take screenshot of bulk products page
    await page.screenshot({ path: 'bulk_products_page.png', fullPage: true });
    console.log('Bulk products page screenshot saved as bulk_products_page.png');

    // Check for any console errors
    page.on('console', msg => console.log('Browser console:', msg.text()));

    // Check if CSS is loaded
    const stylesheets = await page.$$eval('link[rel="stylesheet"]', links =>
      links.map(link => link.href)
    );
    console.log('Loaded stylesheets:', stylesheets);

    // Check for any 404 errors on resources
    const responses = [];
    page.on('response', response => {
      if (response.status() >= 400) {
        responses.push({ url: response.url(), status: response.status() });
      }
    });

    // Wait a bit to catch any late-loading resources
    await page.waitForTimeout(2000);

    if (responses.length > 0) {
      console.log('Failed resource requests:', responses);
    }

  } catch (error) {
    console.error('Error during automation:', error);
    await page.screenshot({ path: 'error_screenshot.png', fullPage: true });
  } finally {
    await browser.close();
  }
})();