import { chromium } from 'playwright';

async function testAll() {
    const browser = await chromium.launch({ headless: true });

    // 1. Test 1920x1080 (FHD)
    console.log('Testing 1920x1080...');
    const ctx1920 = await browser.newContext({
        viewport: { width: 1920, height: 1080 },
        deviceScaleFactor: 1
    });
    const page1920 = await ctx1920.newPage();
    await page1920.goto('http://home.coremusic.net:81/home?_test_resp=1920', { waitUntil: 'networkidle' });
    const metrics1920 = await page1920.evaluate(() => {
        const pl = document.querySelector('.page-layout');
        return {
            width: pl?.getBoundingClientRect().width,
            height: pl?.getBoundingClientRect().height,
            marginInline: getComputedStyle(pl).marginInline,
            zoom: getComputedStyle(pl).zoom
        };
    });
    console.log('1920 Metrics:', metrics1920);
    await page1920.screenshot({ path: 'home_1920_verified.png' });

    // 2. Test 3840x2160 (4K)
    console.log('Testing 3840x2160 (4K)...');
    const ctx4k = await browser.newContext({
        viewport: { width: 3840, height: 2160 },
        deviceScaleFactor: 1
    });
    const page4k = await ctx4k.newPage();
    await page4k.goto('http://home.coremusic.net:81/home?_test_resp=3840', { waitUntil: 'networkidle' });
    const metrics4k = await page4k.evaluate(() => {
        const pl = document.querySelector('.page-layout');
        return {
            width: pl?.getBoundingClientRect().width,
            height: pl?.getBoundingClientRect().height,
            marginInline: getComputedStyle(pl).marginInline,
            zoom: getComputedStyle(pl).zoom,
            maxWidth: getComputedStyle(pl).maxWidth,
            topGridWidth: document.querySelector('.home-layout__top')?.getBoundingClientRect().width,
            topGridCols: getComputedStyle(document.querySelector('.home-layout__top')).gridTemplateColumns,
            recentWidth: document.querySelector('.home-song-btn-list-div')?.getBoundingClientRect().width
        };
    });
    console.log('4K Metrics:', metrics4k);
    await page4k.screenshot({ path: 'home_4k_verified.png' });

    await browser.close();
    console.log('Done.');
}

testAll().catch(console.error);
