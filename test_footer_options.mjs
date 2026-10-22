import { chromium } from 'playwright';

async function testOptions() {
    const browser = await chromium.launch({ headless: true });

    // 1. 1920 baseline
    const ctx1920 = await browser.newContext({ viewport: { width: 1920, height: 1080 } });
    const p1920 = await ctx1920.newPage();
    await p1920.goto('http://home.coremusic.net:81/home?_test_resp=1920', { waitUntil: 'networkidle' });
    const f1920 = await p1920.$('.footer');
    if (f1920) await f1920.screenshot({ path: 'opt_1920_baseline.png' });

    // 2. 3840 with pure zoom: 2 on footer
    const ctx4k = await browser.newContext({ viewport: { width: 3840, height: 2160 } });
    const p4k = await ctx4k.newPage();
    await p4k.goto('http://home.coremusic.net:81/home?_test_resp=3840', { waitUntil: 'networkidle' });
    
    // Let's test Option A: zoom: 2 on footer, width: 1920px (so scaled it is 3840px = 100vw)
    await p4k.evaluate(() => {
        const style = document.createElement('style');
        style.innerHTML = `
            /* Option A: Pure 1920 scale (zoom: 2) */
            .footer.footer-player {
                zoom: 2 !important;
                width: 1920px !important;
                max-width: 1920px !important;
                height: 90px !important;
                min-height: 90px !important;
                left: 0 !important;
                bottom: 0 !important;
                right: auto !important;
            }
            .footer-player__inner {
                width: 100% !important;
                max-width: 100% !important;
                height: 100% !important;
                padding: 0 16px !important;
            }
            .footer-player__cover-wrap {
                width: auto !important;
                height: calc(90px - 6px) !important;
                max-height: calc(90px - 6px) !important;
            }
            .footer-player__btn {
                width: 36px !important;
                height: 36px !important;
                min-width: 36px !important;
                min-height: 36px !important;
            }
            .footer-player__btn img {
                width: 20px !important;
                height: 20px !important;
            }
            .footer-player__btn--play {
                width: 44px !important;
                height: 44px !important;
            }
            .footer-player__text {
                font-size: 11px !important;
            }
            .footer__utility-icon {
                width: 28px !important;
                height: 28px !important;
                min-width: 28px !important;
                min-height: 28px !important;
            }
            .footer__utility-icon img {
                width: 18px !important;
                height: 18px !important;
            }
            .footer-player__volume img, #volumeIcon {
                width: 20px !important;
                height: 20px !important;
            }
            .footer-player__volume-track {
                width: 180px !important;
                height: 4px !important;
            }
            .footer-player__progress {
                height: 4.8px !important;
            }
        `;
        document.head.appendChild(style);
    });

    const f4k_optA = await p4k.$('.footer');
    if (f4k_optA) await f4k_optA.screenshot({ path: 'opt_4k_scaled.png' });

    // Also screenshot full page 4K with Option A
    await p4k.screenshot({ path: 'opt_4k_full_page.png' });

    await browser.close();
    console.log('Options tested successfully.');
}

testOptions().catch(console.error);
