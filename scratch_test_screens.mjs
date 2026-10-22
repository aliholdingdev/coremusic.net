import { chromium } from 'playwright';

async function runTestScreens() {
    const browser = await chromium.launch({ headless: true });

    // 1. 1024 Viewport
    const ctx1024 = await browser.newContext({ viewport: { width: 1024, height: 768 } });
    const p1024 = await ctx1024.newPage();
    await p1024.goto('http://home.coremusic.net:81/home?_test_resp=1024', { waitUntil: 'networkidle' });
    await p1024.screenshot({ path: 'test_current_1024.png', fullPage: true });

    // 2. 1920 Viewport
    const ctx1920 = await browser.newContext({ viewport: { width: 1920, height: 1080 } });
    const p1920 = await ctx1920.newPage();
    await p1920.goto('http://home.coremusic.net:81/home?_test_resp=1920', { waitUntil: 'networkidle' });
    await p1920.screenshot({ path: 'test_current_1920.png', fullPage: true });

    // 3. 3840 (4K) Viewport
    const ctx4k = await browser.newContext({ viewport: { width: 3840, height: 2160 } });
    const p4k = await ctx4k.newPage();
    await p4k.goto('http://home.coremusic.net:81/home?_test_resp=3840', { waitUntil: 'networkidle' });
    await p4k.screenshot({ path: 'test_current_3840.png', fullPage: true });

    await browser.close();
    console.log('Screenshots generated: test_current_1024.png, test_current_1920.png, test_current_3840.png');
}

runTestScreens().catch(console.error);
