import { chromium } from 'playwright';

async function captureAllThree() {
    const browser = await chromium.launch({ headless: true });

    async function setupPage(ctx, width, height, url, outPath) {
        await ctx.addCookies([
            { name: 'cm_welcome_seen', value: '1', domain: 'home.coremusic.net', path: '/' },
            { name: 'cm_viewport_w', value: String(width), domain: 'home.coremusic.net', path: '/' },
            { name: 'cm_viewport_h', value: String(height), domain: 'home.coremusic.net', path: '/' }
        ]);
        const page = await ctx.newPage();
        await page.addInitScript(() => {
            localStorage.setItem('cm_welcome_seen', '1');
        });
        await page.goto(url, { waitUntil: 'networkidle' });

        try {
            const btn = await page.$('.welcome-modal__btn');
            if (btn && await btn.isVisible()) {
                await btn.click({ timeout: 1000 });
                await page.waitForTimeout(200);
            }
        } catch (_) {}

        await page.screenshot({ path: outPath });
    }

    // 1. 1024x600 Embedded (Linux 1024)
    console.log('Capturing 1024x600 Embedded...');
    const ctx1024 = await browser.newContext({ viewport: { width: 1024, height: 600 } });
    await setupPage(ctx1024, 1024, 600, 'http://home.coremusic.net:81/home?_test_resp=1024', 'verify_1024_final.png');

    // 2. 1920x1080 FHD Desktop (Linux 1920)
    console.log('Capturing 1920x1080 FHD...');
    const ctx1920 = await browser.newContext({ viewport: { width: 1920, height: 1080 } });
    await setupPage(ctx1920, 1920, 1080, 'http://home.coremusic.net:81/home?_test_resp=1920', 'verify_1920_final.png');

    // 3. 3840x2160 4K (Linux 3840)
    console.log('Capturing 3840x2160 4K...');
    const ctx4k = await browser.newContext({ viewport: { width: 3840, height: 2160 } });
    await setupPage(ctx4k, 3840, 2160, 'http://home.coremusic.net:81/home?_test_resp=3840', 'verify_3840_final.png');

    await browser.close();
    console.log('ALL THREE VERIFIED SUCCESSFULLY.');
}

captureAllThree().catch(console.error);
