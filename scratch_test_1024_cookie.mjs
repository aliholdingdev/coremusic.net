import { chromium } from 'playwright';

async function test1024WithCookie() {
    const browser = await chromium.launch({ headless: true });
    const ctx = await browser.newContext({
        viewport: { width: 1024, height: 600 }
    });

    // cm_viewport_w ve cm_viewport_h cookie'lerini ekle
    await ctx.addCookies([
        { name: 'cm_viewport_w', value: '1024', domain: 'home.coremusic.net', path: '/' },
        { name: 'cm_viewport_h', value: '600', domain: 'home.coremusic.net', path: '/' },
        { name: 'cm_welcome_seen', value: '1', domain: 'home.coremusic.net', path: '/' }
    ]);

    const page = await ctx.newPage();
    await page.addInitScript(() => {
        localStorage.setItem('cm_welcome_seen', '1');
    });

    await page.goto('http://home.coremusic.net:81/home', { waitUntil: 'networkidle' });

    const modalBtn = await page.$('.welcome-modal__btn');
    if (modalBtn) {
        await modalBtn.click();
        await page.waitForTimeout(300);
    }

    await page.screenshot({ path: 'test_1024_cookie_embedded.png' });
    await browser.close();
    console.log('1024 cookie test done.');
}

test1024WithCookie().catch(console.error);
