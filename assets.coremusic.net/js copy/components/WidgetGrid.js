/**
 * CoreMusic — Widget Grid Manager (v1.0)
 * Figma SSOT: node-id=1639-9775 (1024) / node-id=2850-21494 (1920)
 * BEM: .widget-grid, .widget-grid__row, .widget-card
 * Layer: L3 Presentation · JS Module
 * Version: 1.0.0 — 2026-09-22
 *
 * Widget grid yapısını yönetir:
 *   1024: Row1=4col quickapps, Row2=2col info, Row3=2col info
 *   1920: Row1=4col quickapps, Row2=8col info, Row3=8col info
 */

'use strict';

/**
 * WidgetGrid — Widget grid layout manager
 * Figma pixel-perfect ölçümlerle çalışır
 */
class WidgetGrid {
  /** @type {HTMLElement|null} */
  #container = null;

  /** @type {string} */
  #device = 'embedded';

  /** @type {Object} */
  #config = {
    embedded: {
      row1Cols: 4,
      row2Cols: 2,
      row3Cols: 2,
      gap: 8,
    },
    wide: {
      row1Cols: 4,
      row2Cols: 8,
      row3Cols: 8,
      gap: 12,
    },
  };

  /**
   * @param {HTMLElement} container — .widget-grid elementi
   * @param {string} device — 'embedded' | 'wide' | '4k'
   */
  constructor(container, device = 'embedded') {
    this.#container = container;
    this.#device = device;
    this.#init();
  }

  /** Grid yapısını başlatır */
  #init() {
    if (!this.#container) return;

    const cfg = this.#config[this.#device] || this.#config.embedded;
    const rows = this.#container.querySelectorAll('.widget-grid__row');

    rows.forEach((row, idx) => {
      const rowKey = `row${idx + 1}Cols`;
      const cols = cfg[rowKey] || cfg.row1Cols;
      row.style.setProperty('--wg-cols', cols);
      row.style.setProperty('--wg-gap', `${cfg.gap}px`);
    });
  }

  /**
   * Cihaz değişikliğinde grid'i güncelle
   * @param {string} device — Yeni cihaz türü
   */
  updateDevice(device) {
    this.#device = device;
    this.#init();
  }

  /**
   * Widget sayısını döndür
   * @returns {number}
   */
  getWidgetCount() {
    if (!this.#container) return 0;
    return this.#container.querySelectorAll('.widget-card').length;
  }

  /**
   * Widget grid'i yeniden oluştur
   * @param {Array<Object>} widgets — [{type, label, icon, value}]
   */
  render(widgets) {
    if (!this.#container) return;

    const cfg = this.#config[this.#device] || this.#config.embedded;

    // Row 1: Quick apps
    const row1 = this.#container.querySelector('.widget-grid__row--quickapps');
    if (row1) {
      row1.innerHTML = '';
      const quickApps = widgets.filter(w => w.type === 'quickapp');
      quickApps.forEach(w => {
        row1.appendChild(this.#createCard(w, 'quickapp'));
      });
    }

    // Row 2-3: Info widgets
    const infoWidgets = widgets.filter(w => w.type === 'info');
    const row2 = this.#container.querySelector('.widget-grid__row--info');
    if (row2 && infoWidgets.length > 0) {
      row2.innerHTML = '';
      infoWidgets.forEach(w => {
        row2.appendChild(this.#createCard(w, 'info'));
      });
    }
  }

  /**
   * Widget kartı oluştur
   * @param {Object} data — {label, icon, value}
   * @param {string} variant — 'quickapp' | 'info' | 'clock' | 'filemanager'
   * @returns {HTMLElement}
   */
  #createCard(data, variant) {
    const card = document.createElement('div');
    card.className = `widget-card widget-card--${variant}`;
    card.setAttribute('role', 'button');
    card.setAttribute('tabindex', '0');

    if (data.icon) {
      const icon = document.createElement('img');
      icon.className = 'widget-card__icon';
      icon.src = data.icon;
      icon.alt = data.label || '';
      icon.width = 20;
      icon.height = 20;
      icon.loading = 'lazy';
      card.appendChild(icon);
    }

    if (data.label) {
      const label = document.createElement('span');
      label.className = 'widget-card__label';
      label.textContent = data.label;
      card.appendChild(label);
    }

    if (data.value) {
      const value = document.createElement('span');
      value.className = 'widget-card__value';
      value.textContent = data.value;
      card.appendChild(value);
    }

    return card;
  }
}

// Export for module use
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { WidgetGrid };
}

// Global registration
if (typeof window !== 'undefined') {
  window.WidgetGrid = WidgetGrid;
}
