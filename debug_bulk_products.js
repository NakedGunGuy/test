const { chromium } = require('playwright');

async function debugBulkProducts() {
    const browser = await chromium.launch({
        headless: false,
        slowMo: 1000,
        devtools: true
    });

    const context = await browser.newContext();
    const page = await context.newPage();

    // Enable request/response logging
    page.on('request', request => {
        if (request.url().includes('bulk') || request.method() === 'POST') {
            console.log('📤 REQUEST:', request.method(), request.url());
            if (request.postData()) {
                console.log('📦 POST DATA:', request.postData());
            }
        }
    });

    page.on('response', response => {
        if (response.url().includes('bulk') || response.status() >= 400) {
            console.log('📥 RESPONSE:', response.status(), response.url());
        }
    });

    // Listen for console messages
    page.on('console', msg => {
        console.log('🔍 CONSOLE:', msg.type(), msg.text());
    });

    // Listen for page errors
    page.on('pageerror', error => {
        console.log('❌ PAGE ERROR:', error.message);
    });

    try {
        console.log('🚀 Starting bulk product creation debug...');

        // Step 1: Navigate to login page
        console.log('📝 Step 1: Navigating to admin login...');
        await page.goto('http://cardpoint.test/en/admin/login');
        await page.waitForLoadState('networkidle');

        // Step 2: Login with admin credentials
        console.log('🔐 Step 2: Logging in as admin...');
        await page.fill('input[name="username"]', 'admin');
        await page.fill('input[name="password"]', 'admin');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');

        // Check if login was successful
        const currentUrl = page.url();
        console.log('🌐 Current URL after login:', currentUrl);

        // Step 3: Navigate to bulk products page
        console.log('📦 Step 3: Navigating to bulk products page...');
        await page.goto('http://cardpoint.test/en/admin/products/bulk');
        await page.waitForLoadState('networkidle');

        // Step 4: Select a set to load cards
        console.log('🎯 Step 4: Selecting a set...');

        // Wait for set selector to be available
        await page.waitForSelector('select[name="set_id"]', { timeout: 10000 });

        // Get available sets
        const setOptions = await page.$$eval('select[name="set_id"] option', options =>
            options.map(option => ({ value: option.value, text: option.textContent.trim() }))
        );
        console.log('📋 Available sets:', setOptions);

        // Select the first non-empty set
        const validSet = setOptions.find(option => option.value && option.value !== '');
        if (!validSet) {
            throw new Error('No valid sets found');
        }

        console.log(`🎮 Selecting set: ${validSet.text} (${validSet.value})`);
        await page.selectOption('select[name="set_id"]', validSet.value);

        // Wait for cards to load
        console.log('⏳ Waiting for cards to load...');
        await page.waitForTimeout(2000);

        // Step 5: Check if product rows are generated
        console.log('🔍 Step 5: Checking for product rows...');
        const productRows = await page.$$('.product-row');
        console.log(`📊 Found ${productRows.length} product rows`);

        if (productRows.length === 0) {
            console.log('❌ No product rows found! This might be the issue.');

            // Check for any error messages
            const errorMessages = await page.$$eval('.error, .alert, .message',
                elements => elements.map(el => el.textContent.trim())
            );
            if (errorMessages.length > 0) {
                console.log('🚨 Error messages found:', errorMessages);
            }

            // Check the form HTML structure
            const formHTML = await page.$eval('form', form => form.outerHTML);
            console.log('📝 Form HTML structure (first 500 chars):', formHTML.substring(0, 500));

            return;
        }

        // Step 6: Fill in product details for the first 1-2 rows
        console.log('✏️ Step 6: Filling in product details...');

        for (let i = 0; i < Math.min(2, productRows.length); i++) {
            console.log(`📝 Filling row ${i + 1}...`);

            const row = productRows[i];

            // Check what fields are available in this row
            const fields = await row.$$eval('input, select, textarea', inputs =>
                inputs.map(input => ({
                    name: input.name,
                    type: input.type,
                    id: input.id,
                    className: input.className
                }))
            );
            console.log(`🔧 Fields in row ${i + 1}:`, fields);

            // Fill in the name field
            const nameInput = await row.$('input[name*="name"], input[id*="name"]');
            if (nameInput) {
                await nameInput.fill(`Test Product ${i + 1}`);
                console.log(`✅ Filled name for row ${i + 1}`);
            }

            // Fill in the price field
            const priceInput = await row.$('input[name*="price"], input[id*="price"]');
            if (priceInput) {
                await priceInput.fill(`${(i + 1) * 10}.99`);
                console.log(`✅ Filled price for row ${i + 1}`);
            }

            // Fill in the quantity field
            const quantityInput = await row.$('input[name*="quantity"], input[name*="stock"], input[id*="quantity"]');
            if (quantityInput) {
                await quantityInput.fill(`${(i + 1) * 5}`);
                console.log(`✅ Filled quantity for row ${i + 1}`);
            }
        }

        // Step 7: Test row duplication (if available)
        console.log('🔄 Step 7: Testing row duplication...');
        const duplicateButton = await page.$('button[onclick*="duplicate"], .duplicate-row, button:has-text("Duplicate")');
        if (duplicateButton) {
            console.log('🔄 Found duplicate button, clicking...');
            await duplicateButton.click();
            await page.waitForTimeout(1000);

            const newRowCount = await page.$$eval('.product-row', rows => rows.length);
            console.log(`📊 Rows after duplication: ${newRowCount}`);
        } else {
            console.log('ℹ️ No duplicate button found');
        }

        // Step 8: Capture form data before submission
        console.log('📊 Step 8: Capturing form data structure...');

        const formData = await page.evaluate(() => {
            const form = document.querySelector('form');
            if (!form) return null;

            const formData = new FormData(form);
            const data = {};

            for (let [key, value] of formData.entries()) {
                if (data[key]) {
                    if (Array.isArray(data[key])) {
                        data[key].push(value);
                    } else {
                        data[key] = [data[key], value];
                    }
                } else {
                    data[key] = value;
                }
            }

            return data;
        });

        console.log('📋 Current form data:', JSON.stringify(formData, null, 2));

        // Step 9: Submit the form and capture the response
        console.log('🚀 Step 9: Submitting the form...');

        const submitButton = await page.$('button[type="submit"], input[type="submit"]');
        if (!submitButton) {
            throw new Error('Submit button not found');
        }

        // Set up response listener before clicking submit
        const responsePromise = page.waitForResponse(response =>
            response.url().includes('bulk') && response.request().method() === 'POST'
        );

        await submitButton.click();

        try {
            const response = await responsePromise;
            console.log('📥 Submit response status:', response.status());
            console.log('📥 Submit response URL:', response.url());

            const responseText = await response.text();
            console.log('📄 Response content (first 500 chars):', responseText.substring(0, 500));

            // Wait for any page updates
            await page.waitForTimeout(2000);

            // Check for success/error messages
            const messages = await page.$$eval('.success, .error, .alert, .message, .flash',
                elements => elements.map(el => el.textContent.trim())
            );
            console.log('💬 Messages after submission:', messages);

        } catch (error) {
            console.log('❌ Error waiting for response:', error.message);
        }

        // Step 10: Check final state
        console.log('🏁 Step 10: Checking final state...');
        await page.waitForTimeout(1000);

        const finalUrl = page.url();
        console.log('🌐 Final URL:', finalUrl);

        // Check if we're still on the form or redirected
        const isStillOnForm = await page.$('form') !== null;
        console.log('📝 Still on form page:', isStillOnForm);

    } catch (error) {
        console.error('❌ Error during debugging:', error.message);
        console.error('Stack trace:', error.stack);
    } finally {
        console.log('🏁 Debug session complete. Browser will remain open for manual inspection.');
        // Don't close the browser so we can inspect manually
        // await browser.close();
    }
}

// Run the debug function
debugBulkProducts().catch(console.error);