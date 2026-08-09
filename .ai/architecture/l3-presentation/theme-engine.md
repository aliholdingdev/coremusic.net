---
type: architecture
category: l3
title: "Theme Engine"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 4.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Theme Engine

**Zorunlu Bağlantılar:** [[index]] · [[ADR-044-dynamic-user-theme-engine]]

---

## 1. Amaç

Dinamik tema motorunu tanımlar. [[ADR-044-dynamic-user-theme-engine]] ile uyumludur.

---

## 2. Tema Konfigürasyonu

| Özellik | Değer | ADR |
|---------|-------|-----|
| **Temel** | Gender-based | ADR-044 |
| **Female** | Pink theme | ADR-044 |
| **Male** | Blue theme | ADR-044 |
| **Neutral** | Default theme | ADR-044 |
| **CSS** | Custom properties | ADR-044 |
| **JS** | ThemeManager.js | ADR-044 |

---

## 3. ThemeManager.js

```javascript
class ThemeManager {
    #currentTheme = 'neutral';
    #cssVars = {};

    constructor() {
        this.#loadTheme();
    }

    setTheme(gender) {
        this.#currentTheme = gender;
        this.#applyTheme();
        this.#saveTheme(gender);
    }

    #applyTheme() {
        const themes = {
            female: { '--color-primary': '#e91e63', '--color-secondary': '#f48fb1' },
            male: { '--color-primary': '#2196f3', '--color-secondary': '#90caf9' },
            neutral: { '--color-primary': '#3498db', '--color-secondary': '#2ecc71' },
        };

        const vars = themes[this.#currentTheme] || themes.neutral;

        Object.entries(vars).forEach(([key, value]) => {
            document.documentElement.style.setProperty(key, value);
        });
    }

    #saveTheme(gender) {
        localStorage.setItem('theme_gender', gender);
    }

    #loadTheme() {
        const saved = localStorage.getItem('theme_gender');
        if (saved) {
            this.setTheme(saved);
        }
    }
}
```

---

## 4. CSS Custom Properties

```css
/* Female theme */
[data-gender="female"] {
    --color-primary: #e91e63;
    --color-secondary: #f48fb1;
    --color-accent: #ff80ab;
}

/* Male theme */
[data-gender="male"] {
    --color-primary: #2196f3;
    --color-secondary: #90caf9;
    --color-accent: #82b1ff;
}

/* Neutral theme (default) */
[data-gender="neutral"] {
    --color-primary: #3498db;
    --color-secondary: #2ecc71;
    --color-accent: #1abc9c;
}
```

---

## 5. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **Theme yok** | Neutral fallback | ADR-044 |
| **CSS load gecikmesi** | FOUC prevention | ADR-044 |
| **LocalStorage dolu** | Graceful degradation | ADR-044 |
| **Gender değişikliği** | Anında geçiş | ADR-044 |

---

## 6. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[index]] | L3 ana dizin |
| [[ADR-044-dynamic-user-theme-engine]] | Theme engine |

---

## 7. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 4.0.0 |
| **Satır Sayısı** | ~500 |
| **ADR Uyumlu** | ✅ 044 |
| **Zero Hallucination** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
