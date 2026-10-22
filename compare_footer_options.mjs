import { chromium } from 'playwright';

async function compareOptions() {
    const browser = await chromium.launch({ headless: true });

    // 1920 Baseline
    const ctx1920 = await browser.newContext({ viewport: { width: 1920, height: 1080 } });
    const p1920 = await ctx1920.newPage();
    await p1920.goto('http://home.coremusic.net:81/home?_test_resp=1920', { waitUntil: 'networkidle' });
    const f1920 = await p1920.$('.footer');
    if (f1920) await f1920.screenshot({ path: 'cmp_1920_baseline.png' });

    // Test on 3840:
    const ctx4k = await browser.newContext({ viewport: { width: 3840, height: 2160 } });
    const p4k = await ctx4k.newPage();
    await p4k.goto('http://home.coremusic.net:81/home?_test_resp=3840', { waitUntil: 'networkidle' });

    // Option 1: Figma SSOT (3840x92px - exact 1920 element sizes, but 100% width with right controls aligned to right)
    await p4k.evaluate(() => {
        const style = document.createElement('style');
        style.id = 'opt1-style';
        style.innerHTML = `
            .footer, .footer-player {
                height: 90px !important;
                min-height: 90px !important;
                width: 100vw !important;
                max-width: 100% !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                zoom: 1 !important;
            }
            .footer-player__progress {
                height: 5px !important;
                width: 100% !important;
            }
            .footer-player__inner {
                height: calc(90px - 5px) !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 24px !important;
                display: grid !important;
                grid-template-columns: auto 1fr auto auto !important;
                align-items: center !important;
            }
            .footer-player__meta {
                gap: 10px !important;
            }
            .footer-player__cover-wrap {
                height: 84px !important;
                max-height: 84px !important;
                width: 84px !important;
            }
            .footer-player__cover {
                width: 100% !important;
                height: 100% !important;
            }
            .footer-player__cover-case {
                top: -6.52% !important;
                left: 0 !important;
                width: 100% !important;
                height: 113.04% !important;
            }
            .footer-player__cover-cd {
                top: 16.67% !important;
                left: 0 !important;
                width: 100% !important;
                height: 66.67% !important;
            }
            .footer-player__text {
                font-size: 13px !important;
                line-height: 1.3 !important;
            }
            .footer-player__controls:not(:has(.footer__utility-icons)) {
                position: absolute !important;
                left: 50% !important;
                top: 50% !important;
                transform: translate(-50%, -50%) !important;
                gap: 10px !important;
            }
            .footer-player__btn {
                width: 40px !important;
                height: 40px !important;
                min-width: 40px !important;
                min-height: 40px !important;
            }
            .footer-player__btn img {
                width: 22px !important;
                height: 22px !important;
            }
            .footer-player__btn--play {
                width: 46px !important;
                height: 46px !important;
            }
            .footer__utility-icons {
                gap: 2px !important;
            }
            .footer__utility-icon {
                width: 32px !important;
                height: 32px !important;
                min-width: 32px !important;
                min-height: 32px !important;
            }
            .footer__utility-icon img {
                width: 20px !important;
                height: 20px !important;
            }
            .footer-player__volume {
                gap: 6px !important;
                justify-self: end !important;
            }
            .footer-player__volume img, #volumeIcon {
                width: 22px !important;
                height: 22px !important;
            }
            .footer-player__volume-track {
                width: 200px !important;
                height: 4px !important;
            }
        `;
        document.head.appendChild(style);
    });
    const f4k_opt1 = await p4k.$('.footer');
    if (f4k_opt1) await f4k_opt1.screenshot({ path: 'cmp_4k_opt1_figma.png' });

    // Option 2: zoom: 2 (Scale 2x, exact 1920 proportions, total width 3840px = 100vw)
    await p4k.evaluate(() => {
        const style = document.getElementById('opt1-style');
        style.innerHTML = `
            .footer, .footer-player {
                zoom: 2 !important;
                width: 1920px !important;
                max-width: 1920px !important;
                height: 90px !important;
                min-height: 90px !important;
                left: 0 !important;
                right: auto !important;
                bottom: 0 !important;
            }
            .footer-player__progress {
                height: 5px !important;
                width: 100% !important;
            }
            .footer-player__inner {
                height: calc(90px - 5px) !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 16px !important;
                display: grid !important;
                grid-template-columns: auto 1fr auto auto !important;
                align-items: center !important;
            }
            .footer-player__meta {
                gap: 10px !important;
            }
            .footer-player__cover-wrap {
                height: 84px !important;
                max-height: 84px !important;
                width: 84px !important;
            }
            .footer-player__cover {
                width: 100% !important;
                height: 100% !important;
            }
            .footer-player__cover-case {
                top: -6.52% !important;
                left: 0 !important;
                width: 100% !important;
                height: 113.04% !important;
            }
            .footer-player__cover-cd {
                top: 16.67% !important;
                left: 0 !important;
                width: 100% !important;
                height: 66.67% !important;
            }
            .footer-player__text {
                font-size: 13px !important;
            }
            .footer-player__controls:not(:has(.footer__utility-icons)) {
                position: absolute !important;
                left: 50% !important;
                top: 50% !important;
                transform: translate(-50%, -50%) !important;
                gap: 8px !important;
            }
            .footer-player__btn {
                width: 40px !important;
                height: 40px !important;
            }
            .footer-player__btn img {
                width: 22px !important;
                height: 22px !important;
            }
            .footer-player__btn--play {
                width: 44px !important;
                height: 44px !important;
            }
            .footer__utility-icons {
                gap: 1px !important;
            }
            .footer__utility-icon {
                width: 28px !important;
                height: 28px !important;
            }
            .footer__utility-icon img {
                width: 18px !important;
                height: 18px !important;
            }
            .footer-player__volume {
                gap: 4px !important;
                justify-self: end !important;
            }
            .footer-player__volume img, #volumeIcon {
                width: 20px !important;
                height: 20px !important;
            }
            .footer-player__volume-track {
                width: 180px !important;
                height: 4px !important;
            }
        `;
    });
    const f4k_opt2 = await p4k.$('.footer');
    if (f4k_opt2) await f4k_opt2.screenshot({ path: 'cmp_4k_opt2_zoom2.png' });

    // Option 3: Full width 3840 bar, but .footer-player__inner is constrained to 1920px (centered or left aligned)
    await p4k.evaluate(() => {
        const style = document.getElementById('opt1-style');
        style.innerHTML = `
            .footer, .footer-player {
                width: 100vw !important;
                max-width: 100% !important;
                height: 90px !important;
                min-height: 90px !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                zoom: 1 !important;
            }
            .footer-player__progress {
                height: 5px !important;
                width: 100% !important;
            }
            .footer-player__inner {
                height: calc(90px - 5px) !important;
                width: 1920px !important;
                max-width: 1920px !important;
                margin: 0 auto !important; /* Centered 1920 container in full width footer bar */
                padding: 0 16px !important;
                display: grid !important;
                grid-template-columns: auto 1fr auto auto !important;
                align-items: center !important;
                position: relative !important;
            }
            .footer-player__cover-wrap {
                height: 84px !important;
                max-height: 84px !important;
                width: 84px !important;
            }
            .footer-player__controls:not(:has(.footer__utility-icons)) {
                position: absolute !important;
                left: 50% !important;
                top: 50% !important;
                transform: translate(-50%, -50%) !important;
            }
            .footer-player__volume {
                justify-self: end !important;
            }
        `;
    });
    const f4k_opt3 = await p4k.$('.footer');
    if (f4k_opt3) await f4k_opt3.screenshot({ path: 'cmp_4k_opt3_inner1920.png' });

    await browser.close();
    console.log('Compared all options successfully.');
}

compareOptions().catch(console.error);
