/**
 * DropdownComponent — Açılan/kapanan dropdown menü.
 *
 * HTML contract:
 * <div class="cm-dropdown" data-cm-component="cm-dropdown">
 *   <button class="cm-dropdown__trigger" aria-haspopup="listbox">Seçenek</button>
 *   <ul class="cm-dropdown__menu" role="listbox" hidden>
 *     <li class="cm-dropdown__item" role="option" data-value="opt1">Seçenek 1</li>
 *   </ul>
 * </div>
 *
 * @package CoreMusic\Components\Interactive
 */
import ComponentBase from '../base/ComponentBase.js';

export default class DropdownComponent extends ComponentBase {
    /** @type {boolean} */
    #isOpen = false;

    /** @type {HTMLElement|null} */
    #trigger = null;

    /** @type {HTMLElement|null} */
    #menu = null;

    /** @type {HTMLElement[]} */
    #items = [];

    /** @type {number} */
    #selectedIndex = -1;

    defaultState() {
        return { isOpen: false, selectedIndex: -1 };
    }

    init() {
        this.#trigger = this.$('.cm-dropdown__trigger');
        this.#menu = this.$('.cm-dropdown__menu');
        this.#items = [...this.$$('.cm-dropdown__item')];

        // ARIA
        this.#trigger?.setAttribute('aria-haspopup', 'listbox');
        this.#trigger?.setAttribute('aria-expanded', 'false');
    }

    mount() {
        // Trigger click
        if (this.#trigger) {
            this.on(this.#trigger, 'click', () => this.toggle());
        }

        // Item clicks
        this.#items.forEach((item, index) => {
            this.on(item, 'click', () => this.select(index));
        });

        // Keyboard: Escape kapatır
        this.on(this.el, 'keydown', (e) => {
            if (e.key === 'Escape' && this.#isOpen) {
                this.close();
                this.#trigger?.focus();
            }
        });

        // Dışarı tıklama ile kapatma
        this.on(document, 'click', (e) => {
            if (this.#isOpen && !this.el.contains(/** @type {Node} */ (e.target))) {
                this.close();
            }
        });
    }

    open() {
        if (this.#isOpen) return;
        this.#isOpen = true;
        this.#menu.hidden = false;
        this.#trigger?.setAttribute('aria-expanded', 'true');
        this.setState({ isOpen: true });
        this.emit('cm:dropdown:open');
    }

    close() {
        if (!this.#isOpen) return;
        this.#isOpen = false;
        this.#menu.hidden = true;
        this.#trigger?.setAttribute('aria-expanded', 'false');
        this.setState({ isOpen: false });
        this.emit('cm:dropdown:close');
    }

    toggle() {
        this.#isOpen ? this.close() : this.open();
    }

    /**
     * Belirli bir item'ı seçer.
     * @param {number} index
     */
    select(index) {
        if (index < 0 || index >= this.#items.length) return;

        // Önceki seçimi kaldır
        this.#items[this.#selectedIndex]?.classList.remove('cm-dropdown__item--selected');

        // Yeni seçimi ekle
        this.#selectedIndex = index;
        this.#items[index].classList.add('cm-dropdown__item--selected');

        // Trigger text'ini güncelle
        if (this.#trigger) {
            this.#trigger.textContent = this.#items[index].textContent;
        }

        this.setState({ selectedIndex: index });
        this.emit('cm:dropdown:select', {
            index,
            value: this.#items[index].dataset.value,
            element: this.#items[index],
        });

        this.close();
    }

    destroy() {
        this.#trigger = null;
        this.#menu = null;
        this.#items = [];
        super.destroy();
    }
}
