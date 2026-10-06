/**
 * Widget Grid (Wide/4K üst satır) bileşeni — Guardrail #16 ile js-template §3.2'den türetilir.
 * @module assets.coremusic.net/js/components/composites/WidgetAreaComponent
 * @requires ADR-001 (framework yasak)
 *
 * Figma SSOT: node 2831:13747 (1920 Home) → "Div2 Button" id 2850:21494 (752×184)
 * PHP view: home.coremusic.net/pages/components/widget-grid.php (server-render, statik veri)
 * CSS:      assets.coremusic.net/Css/03_Layout/_widget-grid.css
 *
 * KARAR (2026-09-30 — widget grid görev kapsamı):
 *   Önceki sürüm ".home-widget-grid/.home-widget" (WidgetManager.js ile aynı hayalet
 *   isimlendirme) üretiyordu; bu markup hiçbir PHP şablonunda kullanılmıyordu (orphan).
 *   Bu bileşen artık gerçek üretim markup'ını (.widget-grid / .widget-card / .widget-card__*)
 *   hedefler ve sadece server-render edilmiş içeriği "canlı" tutar:
 *     1. Saat/tarih widget'ı — her dakika günceller (TR ay/gün isimleri).
 *     2. Depolama progressbar — data-progress attribute'unu CSS custom property'e taşır
 *        (player-info__progress__fill ile aynı desen: player component'i .style.width
 *         kullanıyor, burada CSS var + attr köprüsü ile aynı sonuca ulaşılır).
 *   DOM yeniden inşa edilmez (innerHTML/yeniden oluşturma YOK) — PHP'nin render ettiği
 *   düğümler olduğu gibi kalır, yalnız metin/CSS custom property güncellenir.
 *
 * data-cm-component="cm-home-widget-grid" (kayıt: main.js → ComponentRegistry)
 */

'use strict';

import ComponentBase from '../base/ComponentBase.js';

const TR_DAYS = ['Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi'];
const TR_MONTHS = [
    'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran',
    'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık',
];

export default class WidgetAreaComponent extends ComponentBase {
    /** @type {number|null} */
    #clockIntervalId = null;

    defaultState() {
        return {};
    }

    init() {
        if (!this.el) return;
        this.#applyStoragePercent();
    }

    mount() {
        this.#startClock();
    }

    /** Depolama progressbar — data-progress → --widget-storage-percent CSS custom property. */
    #applyStoragePercent() {
        const fill = this.$('.widget-card__progress-fill');
        if (!fill) return;

        const pct = Number.parseInt(fill.getAttribute('data-progress') || '0', 10);
        const clamped = Number.isFinite(pct) ? Math.min(100, Math.max(0, pct)) : 0;
        fill.style.setProperty('--widget-storage-percent', `${clamped}%`);
    }

    /** Saat/tarih widget'ı — her dakika başında günceller (07:00 / 5 Haziran 2026). */
    #startClock() {
        const timeEl = this.$('[data-widget="clock-time"]');
        const dateEl = this.$('[data-widget="clock-date"]');
        if (!timeEl && !dateEl) return;

        const update = () => {
            const now = new Date();
            if (timeEl) {
                const hh = String(now.getHours()).padStart(2, '0');
                const mm = String(now.getMinutes()).padStart(2, '0');
                timeEl.textContent = `${hh}:${mm}`;
            }
            if (dateEl) {
                dateEl.textContent = `${now.getDate()} ${TR_MONTHS[now.getMonth()]} ${now.getFullYear()}`;
                dateEl.setAttribute('data-weekday', TR_DAYS[now.getDay()]);
            }
        };

        update();
        this.#clockIntervalId = window.setInterval(update, 30_000);
    }

    destroy() {
        if (this.#clockIntervalId !== null) {
            window.clearInterval(this.#clockIntervalId);
            this.#clockIntervalId = null;
        }
        super.destroy();
    }
}
