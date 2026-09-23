/**
 * TabsComponent — Tab değiştirme interactivity.
 *
 * HTML contract:
 * <div class="cm-tabs" data-cm-component="cm-tabs">
 *   <nav class="cm-tabs__nav" role="tablist">
 *     <button class="cm-tabs__tab" role="tab" data-tab="0">Tab 1</button>
 *     <button class="cm-tabs__tab" role="tab" data-tab="1">Tab 2</button>
 *   </nav>
 *   <div class="cm-tabs__panel" role="tabpanel" data-panel="0">...</div>
 *   <div class="cm-tabs__panel" role="tabpanel" data-panel="1" hidden>...</div>
 * </div>
 *
 * @package CoreMusic\Components\Interactive
 */
import ComponentBase from '../base/ComponentBase.js';

export default class TabsComponent extends ComponentBase {
    /** @type {HTMLElement[]} Tab button elements */
    #tabs = [];

    /** @type {HTMLElement[]} Panel elements */
    #panels = [];

    /** @type {number} Active tab index */
    #activeIndex = 0;

    defaultState() {
        return { activeIndex: 0 };
    }

    init() {
        this.#tabs = [...this.$$('.cm-tabs__tab')];
        this.#panels = [...this.$$('.cm-tabs__panel')];

        // ARIA roles
        this.#tabs.forEach((tab, i) => {
            tab.setAttribute('role', 'tab');
            tab.setAttribute('aria-selected', String(i === this.#activeIndex));
            tab.setAttribute('tabindex', i === this.#activeIndex ? '0' : '-1');
            tab.setAttribute('aria-controls', `cm-panel-${i}`);
        });

        this.#panels.forEach((panel, i) => {
            panel.setAttribute('role', 'tabpanel');
            panel.id = `cm-panel-${i}`;
            panel.hidden = i !== this.#activeIndex;
        });
    }

    mount() {
        // Click events
        this.#tabs.forEach((tab, index) => {
            this.on(tab, 'click', () => this.activate(index));
        });

        // Keyboard navigation (arrow keys)
        this.on(this.el, 'keydown', (e) => {
            if (e.target.role !== 'tab') return;

            if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                e.preventDefault();
                const next = (this.#activeIndex + 1) % this.#tabs.length;
                this.activate(next);
            } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                e.preventDefault();
                const prev = (this.#activeIndex - 1 + this.#tabs.length) % this.#tabs.length;
                this.activate(prev);
            } else if (e.key === 'Home') {
                e.preventDefault();
                this.activate(0);
            } else if (e.key === 'End') {
                e.preventDefault();
                this.activate(this.#tabs.length - 1);
            }
        });
    }

    /**
     * Belirli bir tab'a geç.
     * @param {number} index
     */
    activate(index) {
        if (index < 0 || index >= this.#tabs.length) return;
        if (index === this.#activeIndex) return;

        // Eski tab'ı deaktif et
        this.#tabs[this.#activeIndex]?.setAttribute('aria-selected', 'false');
        this.#tabs[this.#activeIndex]?.setAttribute('tabindex', '-1');
        this.#panels[this.#activeIndex].hidden = true;

        // Yeni tab'ı aktif et
        this.#activeIndex = index;
        this.#tabs[index].setAttribute('aria-selected', 'true');
        this.#tabs[index].setAttribute('tabindex', '0');
        this.#tabs[index].focus();
        this.#panels[index].hidden = false;

        this.setState({ activeIndex: index });
        this.emit('cm:tabs:change', {
            index,
            tab: this.#tabs[index],
            panel: this.#panels[index],
        });
    }

    /** @returns {number} */
    getActiveIndex() {
        return this.#activeIndex;
    }

    /** @returns {number} */
    getTabCount() {
        return this.#tabs.length;
    }

    destroy() {
        this.#tabs = [];
        this.#panels = [];
        super.destroy();
    }
}
