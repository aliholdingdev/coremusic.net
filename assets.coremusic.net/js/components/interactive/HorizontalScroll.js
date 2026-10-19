/**
 * HorizontalScroll — yatay kaydırılabilir kart şeridi için tekerlek (wheel) desteği.
 *
 * Dikey fare tekerleğini (deltaY) şeridin yatay kaydırmasına çevirir. Şerit kenarına
 * ulaşıldığında olay engellenmez; sayfanın dikey kaydırması normal çalışır.
 *
 * HTML contract:
 * <div class="home_song__btn-card-grid home_song__btn-card-grid--scroll" data-cm-component="cm-h-scroll">
 *   ...kartlar...
 * </div>
 *
 * @package CoreMusic\Components\Interactive
 */
import ComponentBase from '../base/ComponentBase.js';

/** deltaMode=1 (satır) için piksel karşılığı */
const LINE_PX = 16;

/**
 * Tekerlek olayından yatay kaydırma miktarını (px) hesaplar.
 * Yatay bileşen (deltaX) baskınsa onu, değilse dikey bileşeni (deltaY) kullanır.
 *
 * @param {number} deltaX
 * @param {number} deltaY
 * @param {number} deltaMode 0=piksel, 1=satır, 2=sayfa
 * @param {number} pageSize  Kapsayıcının görünür genişliği (sayfa modu için)
 * @returns {number}
 */
export function wheelToHorizontalDelta(deltaX, deltaY, deltaMode, pageSize) {
    const raw = Math.abs(deltaX) > Math.abs(deltaY) ? deltaX : deltaY;
    let factor = 1;
    if (deltaMode === 1) {
        factor = LINE_PX;
    } else if (deltaMode === 2) {
        factor = pageSize;
    }
    return raw * factor;
}

/**
 * Verilen yönde (delta işaretine göre) kaydırılabilecek alan kaldı mı?
 *
 * @param {{scrollLeft: number, scrollWidth: number, clientWidth: number}} metrics
 * @param {number} delta
 * @returns {boolean}
 */
export function canScrollHorizontally(metrics, delta) {
    const max = metrics.scrollWidth - metrics.clientWidth;
    if (max <= 0 || delta === 0) {
        return false;
    }
    if (delta > 0) {
        return metrics.scrollLeft < max - 1;
    }
    return metrics.scrollLeft > 1;
}

export default class HorizontalScroll extends ComponentBase {
    init() {}

    mount() {
        const el = this.el;
        if (!el) {
            return;
        }

        this.on(el, 'wheel', (event) => {
            const delta = wheelToHorizontalDelta(event.deltaX, event.deltaY, event.deltaMode, el.clientWidth);
            if (!canScrollHorizontally(el, delta)) {
                return;
            }
            event.preventDefault();
            el.scrollLeft += delta;
        }, { passive: false });
    }
}
