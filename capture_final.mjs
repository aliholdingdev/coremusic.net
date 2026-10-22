import { chromium } from 'playwright';

async function capture() {
    const browser = await chromium.launch({ headless: true });

    // 1920 Page
    const ctx1920 = await browser.newContext({ viewport: { width: 1920, height: 1080 } });
    const p1920 = await ctx1920.newPage();
    await p1920.goto('http://home.coremusic.net:81/home?_test_resp=1920', { waitUntil: 'networkidle' });
    await p1920.screenshot({ path: 'final_1920_page.png' });
    const f1920 = await p1920.$('.footer');
    if (f1920) await f1920.screenshot({ path: 'final_1920_footer.png' });

    // 3840 Page
    const ctx4k = await browser.newContext({ viewport: { width: 3840, height: 2160 } });
    const p4k = await ctx4k.newPage();
    await p4k.goto('http://home.coremusic.net:81/home?_test_resp=3840', { waitUntil: 'networkidle' });
    await p4k.screenshot({ path: 'final_4k_page.png' });
    const f4k = await p4k.$('.footer');
    if (f4k) await f4k.screenshot({ path: 'final_4k_footer.png' });

    await browser.close();
    console.log('Final screenshots captured.');
}

capture().catch(console.error);
