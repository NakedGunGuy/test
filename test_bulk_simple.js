const { chromium } = require('playwright');

async function testBulkProducts() {
    const browser = await chromium.launch({ headless: false });
    const context = await browser.newContext();
    const page = await context.newPage();

    try {
        console.log('Step 1: Login');
        await page.goto('http://cardpoint.test/en/admin/login');
        await page.fill('input[name="username"]', 'admin');
        await page.fill('input[name="password"]', 'admin');
        await page.click('button[type="submit"]');
        await page.waitForLoadState('networkidle');

        console.log('Step 2: Go to bulk products');
        await page.goto('http://cardpoint.test/en/admin/products/bulk');
        await page.waitForLoadState('networkidle');

        console.log('Step 3: Select a set with data');
        await page.selectOption('select[name="set_id"]', '33'); // Abyssal Heaven
        await page.waitForTimeout(3000);

        console.log('Step 4: Fill in just one product');
        const bulkCards = await page.$$('.bulk-card');
        console.log('Found', bulkCards.length, 'bulk cards');

        if (bulkCards.length > 0) {
            const firstCard = bulkCards[0];
            const nameInput = await firstCard.$('input[name*="[name]"]');
            const priceInput = await firstCard.$('input[name*="[price]"]');
            const quantityInput = await firstCard.$('input[name*="[quantity]"]');

            if (nameInput) await nameInput.fill('Test Product');
            if (priceInput) await priceInput.fill('5.99');
            if (quantityInput) await quantityInput.fill('10');

            console.log('Step 5: Submit form and check response');

            // Listen for the response
            let responseReceived = false;
            page.on('response', async response => {
                if (response.url().includes('bulk/create')) {
                    console.log('Bulk create response status:', response.status());
                    try {
                        const responseText = await response.text();
                        console.log('Response body:', responseText);
                        responseReceived = true;
                    } catch (e) {
                        console.log('Could not read response body:', e.message);
                    }
                }
            });

            await page.click('button[type="submit"]');

            // Wait for response
            for (let i = 0; i < 10; i++) {
                if (responseReceived) break;
                await page.waitForTimeout(500);
            }

            // Check for success message in the page
            await page.waitForTimeout(2000);
            const resultDiv = await page.$('#bulk-result');
            if (resultDiv) {
                const resultText = await resultDiv.textContent();
                console.log('Result div content:', resultText);
            }

        } else {
            console.log('No bulk cards found');
        }

    } catch (error) {
        console.error('Error:', error.message);
    } finally {
        await browser.close();
    }
}

testBulkProducts();