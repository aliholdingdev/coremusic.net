---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — Vitest Test Template"
type: testing-template
category: template
version: 2.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-23
date: 2026-09-23
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# CoreMusic — Vitest Test Template

**Zorunlu Bağlantılar / See also:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]]

## 1. Amaç

CoreMusic frontend unit testlerini standartlaştırmaktır: test dosya yapısı, Component/Service/DOM Utils test metodu iskeletleri, global fetch mock'ı, `vitest.config.js` coverage eşikleri ve çalıştırma komutlarını tek şablonda sunar. Kaynak: `reference_doc: Freelancer Technical Documentation v1.0`.

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `tests/**/*.test.js` Vitest unit/integration testleri | Backend testleri (bkz. phpunit-template) |
| `vitest.config.js` + coverage thresholds (≥80%) | E2E testleri (Playwright) |
| Component/Service/DOM Utils mock kalıpları | Üretim kodu (`assets/js/**`) |

- **Dosya tipi:** JavaScript test dosyası + Markdown şablon dokümanı
- **Teknoloji:** Vitest, happy-dom, ES6+
- **Kullanan agent:** QA Engineer (birincil · AGENTS.md §6: test, coverage, Vitest), UI Designer (ikincil)
- **Hedef Coverage:** ≥80% · **Guardrail:** #16 (Template Mandatory)

## 3. Mimari

Şablonun tam gövdesi. Not: gömme nedeniyle şablon başlıkları iki seviye derinleştirilmiştir (H1 → `###`, H2 → `####`); tüm `{{PLACEHOLDER}}`, JavaScript/bash kod blokları ve `//` yorum satırları birebir korunmuştur. Coverage ve test standartları §4.1'dedir.

### {{TITLE}}

**Teknoloji:** Vitest, happy-dom, ES6+
**Kapsam:** Frontend unit test
**Hedef Coverage:** ≥80%

---

#### 3.1 Dosya Yapısı

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

#### 3.2 Component Test Şablonu

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

#### 3.3 Service Test Şablonu

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

#### 3.4 DOM Utils Test Şablonu

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

#### 3.5 vitest.config.js

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

#### 3.6 Çalıştırma

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

## 4. Kurallar

Zorunlu / yasak kurallar ve kod standartları:

#### 4.1 Test & Coverage Standartları

| Kural | Değer |
|-------|-------|
| Hedef Coverage | ≥80% (lines, functions, branches, statements) |
| Environment | `happy-dom` + `globals: true` |
| Coverage provider | `v8` — reporter: text, html, lcov |
| Kapsam | `assets/js/**/*.js` — hariç: `assets/js/config/**` |
| Fetch mock | `global.fetch = vi.fn()` (§3.3) |
| CSRF | `X-CSRF-Token` header testi zorunlu (§3.3 create) |

Ek kurallar:

- **Zorunlu:** her test bir assertion içerir; DOM temizliği `afterEach` ile yapılır (§3.2 `container.remove()`).
- **Zorunlu:** Service testlerinde fetch global olarak mock'lanır; `beforeEach(() => vi.clearAllMocks())` ile sıfırlanır (§3.3).
- **Zorunlu:** write istekleri (POST) için `X-CSRF-Token` header'ı assert edilir (§3.3) — CSRF contract'ı korunur.
- **Zorunlu:** `npx vitest --coverage` §3.5 thresholds'ları (80/80/80/80) sağlamalıdır; CI `js-test` job `npm run test:coverage` çalıştırır (github-actions-template §3.1).
- **Yasak:** `{{TITLE}}`, `{{COMPONENT}}`, `{{SERVICE}}`, `{{MODULE}}`, `{{DATE}}` placeholder'ları doldurulmadan test dosyası commit edilemez; assertion'sız test yazılamaz.
- **Uyarı:** coverage %80 altına düşerse AGENTS.md §10.1 eskalasyonu (L1 QA → L2, timeout 60s); bilinmeyen DOM behavior `⚠️ VERIFICATION REQUIRED`.

## 5. Workflow

```
ŞABLONU SEÇ → KOPYALA → {{PLACEHOLDER}} DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT
```

1. **ŞABLONU SEÇ:** `.ai/.templates/testing/vitest-template.md` (Guardrail #16).
2. **KOPYALA:** §3.1 dosya yapısına göre `tests/unit/...` altına test dosyasını oluştur; §3.5 `vitest.config.js` konfigürasyonunu kopyala.
3. **{{PLACEHOLDER}} DOLDUR:** `{{TITLE}}`, `{{COMPONENT}}`, `{{SERVICE}}`, `{{MODULE}}` (import yolları, API URL'leri, describe adları dahil).
4. **GUARDRAIL #16 DOĞRULA:** 7 alanlı frontmatter + §1-§7 + tüm placeholder'lar doldu + §4.1 standartları (coverage ≥80%, fetch mock, CSRF header assert) geçti.
5. **COMMIT:** `npx vitest --coverage` ile yerelde doğrula; CI `js-test` job geçmeli; `log.md`'ye giriş ekle.

## 6. Doğrulama

- [ ] 7 alanlı frontmatter var (title, type, category, version, status, authority, updated)
- [ ] §1-§7 var
- [ ] tüm {{PLACEHOLDER}}'lar dolduruldu
- [ ] dosya bu şablona uygun
- [ ] Coverage ≥80% (4 threshold); her test assertion içeriyor; fetch mock + CSRF header assert mevcut

**REFACTOR REPORT:** FILE: vitest-template.md · PURPOSE: Vitest Test Template · VALIDATION: 7 alan + §1-§7 + bilgi korunumu · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

## 7. Referanslar

- [[.templates/index]] — şablon registry (`.ai/.templates/index.md`)
- [[../CLAUDE.md]] — AI anayasası, 16 Hard Guardrail
- [[../../AGENTS.md]] — routing (§6: test/Vitest → QA Engineer), kalite standardı §16 (coverage ≥80%, flaky %0), eskalasyon §10.1 (coverage < %80)
- `.ai/CLAUDE.md` · `.ai/AGENTS.md` · `.ai/brain.md` (frontmatter `reference`)
- `reference_doc: Freelancer Technical Documentation v1.0`

---

*Vitest Test Template v2.0.0 — CoreMusic Testing Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
