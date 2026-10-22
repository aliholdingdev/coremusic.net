import { chromium } from 'playwright';

async function test4kFullScale() {
    const browser = await chromium.launch({ headless: true });

    // 1920x1080 (Orijinal Doğal)
    const ctx1920 = await browser.newContext({ viewport: { width: 1920, height: 1080 } });
    const p1920 = await ctx1920.newPage();
    await p1920.goto('http://home.coremusic.net:81/home?_test_resp=1920', { waitUntil: 'networkidle' });
    await p1920.screenshot({ path: 'test_1920_baseline_clean.png', fullPage: true });

    // 3840x2160 (4K Full Scale - 1920'deki gibi tam ekran dolumu)
    const ctx4k = await browser.newContext({ viewport: { width: 3840, height: 2160 } });
    const p4k = await ctx4k.newPage();
    await p4k.goto('http://home.coremusic.net:81/home?_test_resp=1920', { waitUntil: 'networkidle' });
    
    // 4K'da html/body veya ana taşıyıcıları 2x ölçekleyerek tam ekrana oturtma denemesi
    await p4k.evaluate(() => {
        // body'ye zoom: 2 veya transform scale
        document.documentElement.style.zoom = '2';
    });
    await p4k.waitForTimeout(500);
    await p4k.screenshot({ path: 'test_3840_zoom2_clean.png', fullPage: true });

    await browser.close();
    console.log('Screenshots generated: test_1920_baseline_clean.png, test_3840_zoom2_clean.png');
}

test4kFullScale().catch(console.error);
