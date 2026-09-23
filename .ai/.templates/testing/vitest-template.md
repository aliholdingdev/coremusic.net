---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — Vitest Test Template"
type: testing-template
category: template
date: {{DATE}}
updated: {{DATE}}
status: draft
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# {{TITLE}}

**Teknoloji:** Vitest, happy-dom, ES6+
**Kapsam:** Frontend unit test
**Hedef Coverage:** ≥80%

---

## 1. Dosya Yapısı

```
tests/
├── unit/
│   ├── components/
│   │   └── {{COMPONENT}}.test.js
│   ├── services/
│   │   └── {{SERVICE}}.test.js
│   └── utils/
│       └── dom.test.js
├── integration/
│   └── {{MODULE}}.test.js
└── setup.js
```

---

## 2. Component Test Şablonu

```javascript
import { describe, it, expect, beforeEach, vi } from 'vitest';
import {{COMPONENT}} from '../../assets/js/components/{{COMPONENT}}.js';

describe('{{COMPONENT}}', () => {
    let container;

    beforeEach(() => {
        container = document.createElement('div');
        document.body.appendChild(container);
    });

    afterEach(() => {
        container.remove();
    });

    describe('init', () => {
        it('should initialize with empty state', () => {
            {{COMPONENT}}.init(container);

            expect(container.children.length).toBeGreaterThan(0);
            expect(container.querySelector('[data-component]')).not.toBeNull();
        });

        it('should initialize with custom state', () => {
            {{COMPONENT}}.init(container, { title: 'Custom Title' });

            const title = container.querySelector('h2');
            expect(title?.textContent).toBe('Custom Title');
        });
    });

    describe('updateState', () => {
        it('should update state and re-render', () => {
            {{COMPONENT}}.init(container, { title: 'Initial' });

            {{COMPONENT}}.updateState({ title: 'Updated' });

            const title = container.querySelector('h2');
            expect(title?.textContent).toBe('Updated');
        });
    });

    describe('destroy', () => {
        it('should clean up event listeners', () => {
            const removeSpy = vi.spyOn(container, 'removeEventListener');
            {{COMPONENT}}.init(container);
            {{COMPONENT}}.destroy();

            expect(removeSpy).toHaveBeenCalled();
        });

        it('should clear container', () => {
            {{COMPONENT}}.init(container);
            {{COMPONENT}}.destroy();

            expect(container.innerHTML).toBe('');
        });
    });

    describe('event handling', () => {
        it('should handle click actions', () => {
            {{COMPONENT}}.init(container);

            const button = document.createElement('button');
            button.dataset.action = 'action1';
            container.appendChild(button);

            const clickEvent = new Event('click', { bubbles: true });
            button.dispatchEvent(clickEvent);

            // Assert action was handled
        });
    });
});
```

---

## 3. Service Test Şablonu

```javascript
import { describe, it, expect, vi, beforeEach } from 'vitest';
import {{SERVICE}}Service from '../../assets/js/services/{{SERVICE}}.js';

// Mock fetch globally
const mockFetch = vi.fn();
global.fetch = mockFetch;

describe('{{SERVICE}}Service', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    describe('findAll', () => {
        it('should return array of items', async () => {
            const mockData = [
                { id: 1, name: 'Item 1' },
                { id: 2, name: 'Item 2' },
            ];

            mockFetch.mockResolvedValue({
                ok: true,
                json: () => Promise.resolve(mockData),
            });

            const result = await {{SERVICE}}Service.findAll();

            expect(result).toEqual(mockData);
            expect(mockFetch).toHaveBeenCalledWith(
                expect.stringContaining('/api/v1/{{MODULE}}'),
                expect.objectContaining({
                    method: 'GET',
                    headers: expect.objectContaining({
                        'Content-Type': 'application/json',
                    }),
                })
            );
        });

        it('should throw on HTTP error', async () => {
            mockFetch.mockResolvedValue({
                ok: false,
                status: 500,
                statusText: 'Internal Server Error',
            });

            await expect({{SERVICE}}Service.findAll()).rejects.toThrow('HTTP 500');
        });
    });

    describe('create', () => {
        it('should send POST request with CSRF token', async () => {
            document.cookie = 'csrf_token=test-token-123';

            const mockData = { id: 1, name: 'New Item' };
            mockFetch.mockResolvedValue({
                ok: true,
                json: () => Promise.resolve(mockData),
            });

            const result = await {{SERVICE}}Service.create({ name: 'New Item' });

            expect(result).toEqual(mockData);
            expect(mockFetch).toHaveBeenCalledWith(
                expect.stringContaining('/api/v1/{{MODULE}}'),
                expect.objectContaining({
                    method: 'POST',
                    headers: expect.objectContaining({
                        'X-CSRF-Token': 'test-token-123',
                    }),
                })
            );
        });
    });
});
```

---

## 4. DOM Utils Test Şablonu

```javascript
import { describe, it, expect } from 'vitest';
import DOMUtils from '../../assets/js/utils/dom.js';

describe('DOMUtils', () => {
    describe('createElement', () => {
        it('should create element with tag', () => {
            const el = DOMUtils.createElement('div');
            expect(el.tagName).toBe('DIV');
        });

        it('should create element with attributes', () => {
            const el = DOMUtils.createElement('div', {
                className: 'test-class',
                id: 'test-id',
            });

            expect(el.className).toBe('test-class');
            expect(el.id).toBe('test-id');
        });

        it('should create element with data attributes', () => {
            const el = DOMUtils.createElement('div', {
                'data-action': 'test',
            });

            expect(el.dataset.action).toBe('test');
        });

        it('should create element with text content', () => {
            const el = DOMUtils.createElement('span', {}, 'Hello');

            expect(el.textContent).toBe('Hello');
        });
    });

    describe('delegate', () => {
        it('should delegate events to child elements', () => {
            const container = document.createElement('div');
            const child = document.createElement('button');
            child.className = 'delegated';
            container.appendChild(child);
            document.body.appendChild(container);

            let clicked = false;
            DOMUtils.delegate(container, 'click', '.delegated', () => {
                clicked = true;
            });

            child.click();

            expect(clicked).toBe(true);
            container.remove();
        });
    });

    describe('clearChildren', () => {
        it('should remove all children', () => {
            const container = document.createElement('div');
            container.appendChild(document.createElement('span'));
            container.appendChild(document.createElement('p'));

            DOMUtils.clearChildren(container);

            expect(container.children.length).toBe(0);
        });
    });
});
```

---

## 5. vitest.config.js

```javascript
import { defineConfig } from 'vitest/config';

export default defineConfig({
    test: {
        environment: 'happy-dom',
        globals: true,
        include: ['tests/**/*.test.js'],
        coverage: {
            provider: 'v8',
            reporter: ['text', 'html', 'lcov'],
            include: ['assets/js/**/*.js'],
            exclude: ['assets/js/config/**'],
            thresholds: {
                lines: 80,
                functions: 80,
                branches: 80,
                statements: 80,
            },
        },
    },
});
```

---

## 6. Çalıştırma

```bash
# Tüm testler
npx vitest

# Watch modu
npx vitest --watch

# Coverage ile
npx vitest --coverage

# Tek dosya
npx vitest run tests/unit/components/{{COMPONENT}}.test.js
```

---

*Vitest Test Template v1.0.0 — CoreMusic Testing Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
