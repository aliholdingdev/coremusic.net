import { chromium } from 'playwright';

async function captureFooter() {
    const browser = await chromium.launch({ headless: true });

    // 1920x1080 footer
    const ctx1920 = await browser.newContext({ viewport: { width: 1920, height: 1080 } });
    const p1920 = await ctx1920.newPage();
    await p1920.goto('http://home.coremusic.net:81/home?_test_resp=1920', { waitUntil: 'networkidle' });
    const footer1920 = await p1920.$('.footer');
    if (footer1920) {
        await footer1920.screenshot({ path: 'footer_1920_element.png' });
    }

    // 3840x2160 footer
    const ctx4k = await browser.newContext({ viewport: { width: 3840, height: 2160 } });
    const p4k = await ctx4k.newPage();
    await p4k.goto('http://home.coremusic.net:81/home?_test_resp=3840', { waitUntil: 'networkidle' });
    const footer4k = await p4k.$('.footer');
    if (footer4k) {
        await footer4k.screenshot({ path: 'footer_4k_element.png' });
    }

    await browser.close();
    console.log('Footer element screenshots captured.');
}

captureFooter().catch(console.error);
