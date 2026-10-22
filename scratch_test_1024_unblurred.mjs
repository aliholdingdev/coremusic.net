import { chromium } from 'playwright';

async function test1024ModalClosed() {
    const browser = await chromium.launch({ headless: true });
    const ctx = await browser.newContext({ viewport: { width: 1024, height: 600 } });
    const page = await ctx.newPage();
    
    // localStorage'da welcome modal kapalı işaretle
    await page.addInitScript(() => {
        localStorage.setItem('cm_welcome_seen', '1');
    });

    await page.goto('http://home.coremusic.net:81/home', { waitUntil: 'networkidle' });
    
    // Modal varsa Başla butonuna bas veya kaldır
    const modalBtn = await page.$('.welcome-modal__btn');
    if (modalBtn) {
        await modalBtn.click();
        await page.waitForTimeout(300);
    }
    
    await page.screenshot({ path: 'test_1024x600_unblurred.png' });
    await browser.close();
    console.log('1024x600 unblurred captured.');
}

test1024ModalClosed().catch(console.error);
