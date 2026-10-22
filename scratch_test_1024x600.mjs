import { chromium } from 'playwright';

async function test1024x600() {
    const browser = await chromium.launch({ headless: true });
    const ctx = await browser.newContext({ viewport: { width: 1024, height: 600 } });
    const page = await ctx.newPage();
    // Normal /home yükleme (cookie veya test resp olmadan ve test resp ile)
    await page.goto('http://home.coremusic.net:81/home', { waitUntil: 'networkidle' });
    await page.screenshot({ path: 'test_1024x600_normal.png' });

    await page.goto('http://home.coremusic.net:81/home?_test_resp=1024', { waitUntil: 'networkidle' });
    await page.screenshot({ path: 'test_1024x600_testresp.png' });

    await browser.close();
    console.log('1024x600 captured.');
}

test1024x600().catch(console.error);
