import { chromium } from 'playwright';

async function inspectHeaderFooter() {
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 3840, height: 2160 },
        deviceScaleFactor: 1
    });
    const page = await context.newPage();
    await page.goto('http://home.coremusic.net:81/home', { waitUntil: 'networkidle' });

    const report = await page.evaluate(() => {
        const header = document.querySelector('.site-header');
        const headerInner = document.querySelector('.site-header__inner');
        const headerActions = document.querySelector('.site-header__actions');
        const footer = document.querySelector('.footer, .player-footer');
        const footerInner = document.querySelector('.footer-player__inner');

        return {
            windowWidth: window.innerWidth,
            header: {
                width: header?.getBoundingClientRect().width,
                maxWidth: header ? getComputedStyle(header).maxWidth : null
            },
            headerInner: {
                width: headerInner?.getBoundingClientRect().width,
                maxWidth: headerInner ? getComputedStyle(headerInner).maxWidth : null,
                justifyContent: headerInner ? getComputedStyle(headerInner).justifyContent : null
            },
            headerActions: {
                left: headerActions?.getBoundingClientRect().left,
                right: headerActions?.getBoundingClientRect().right,
                width: headerActions?.getBoundingClientRect().width,
                marginLeft: headerActions ? getComputedStyle(headerActions).marginLeft : null
            },
            footer: {
                width: footer?.getBoundingClientRect().width,
                maxWidth: footer ? getComputedStyle(footer).maxWidth : null
            },
            footerInner: {
                width: footerInner?.getBoundingClientRect().width,
                maxWidth: footerInner ? getComputedStyle(footerInner).maxWidth : null
            }
        };
    });

    console.log('Inspection Result:', JSON.stringify(report, null, 2));
    await browser.close();
}

inspectHeaderFooter().catch(console.error);
