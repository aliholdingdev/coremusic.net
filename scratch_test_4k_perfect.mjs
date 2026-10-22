import { chromium } from 'playwright';

async function test4kPerfect() {
    const browser = await chromium.launch({ headless: true });
    const ctx = await browser.newContext({
        viewport: { width: 3840, height: 2160 }
    });

    const page = await ctx.newPage();
    await page.addInitScript(() => {
        localStorage.setItem('cm_welcome_seen', '1');
    });

    await page.goto('http://home.coremusic.net:81/home?_test_resp=1920', { waitUntil: 'networkidle' });

    // 4K'da header, page-layout ve footer için tam 2x ölçekleme uygulayalım
    await page.evaluate(() => {
        const style = document.createElement('style');
        style.id = 'perfect-4k-style';
        style.innerHTML = `
            @media (min-width: 2561px) {
                html {
                    font-size: 32px !important; /* 16px -> 32px (2x) */
                }
                
                /* Tüm sayfa gövdesini 1920 bazından 3840'a 2x tam ekran ölçekleme */
                .site-header {
                    height: 130px !important; /* 65px * 2 */
                    padding: 0 40px !important;
                }
                .site-header__inner {
                    height: 100% !important;
                }
                .site-header__logo {
                    font-size: 36px !important;
                }
                .site-header__nav .nav-link {
                    font-size: 26px !important;
                    margin: 0 16px !important;
                }
                .site-header__actions {
                    gap: 20px !important;
                }
                .header-user {
                    height: 60px !important;
                    padding: 0 16px !important;
                }
                .header-user__avatar {
                    width: 48px !important;
                    height: 48px !important;
                }
                .header-user__name {
                    font-size: 24px !important;
                }
                .header-border {
                    height: 60px !important;
                    padding: 0 16px !important;
                }
                .header-action-btn img, .header-widget img {
                    width: 32px !important;
                    height: 32px !important;
                }

                /* Ana Sayfa Layout 2x */
                .page-layout.home-layout--wide {
                    margin-top: 130px !important;
                    height: calc(100vh - 130px - 184px) !important;
                    min-height: calc(100vh - 130px - 184px) !important;
                    max-height: calc(100vh - 130px - 184px) !important;
                    padding: 60px 56px 24px !important;
                    zoom: 1 !important;
                    transform: scale(2);
                    transform-origin: top left;
                    width: 1920px !important;
                    max-width: 1920px !important;
                }

                /* Footer Player 2x */
                .footer {
                    height: 184px !important; /* 92px * 2 */
                }
                .footer-player__progress {
                    height: 8px !important;
                }
                .footer-player__inner {
                    height: calc(184px - 8px) !important;
                    padding: 0 48px !important;
                }
                .footer-player__controls:not(:has(.footer__utility-icons)) {
                    transform: translate(-50%, -50%) scale(1.8) !important;
                }
                .footer-player__meta {
                    transform: scale(1.6) !important;
                    transform-origin: left center !important;
                }
                .footer-player__volume, .footer__utility-icons {
                    transform: scale(1.6) !important;
                    transform-origin: right center !important;
                }
            }
        `;
        document.head.appendChild(style);
    });

    await page.waitForTimeout(500);
    await page.screenshot({ path: 'test_4k_perfect_preview.png' });
    await browser.close();
    console.log('4K perfect preview generated.');
}

test4kPerfect().catch(console.error);
