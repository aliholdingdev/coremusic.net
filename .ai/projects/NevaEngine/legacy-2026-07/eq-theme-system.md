---
type: technical-spec
project: Neva Engine
component: eq-theme-system
updated: 2026-06-03
---

# Neva Engine — EQ Theme System

> Tema paketleri (.zip) ile UI özelleştirme.
> Admin tema editörü ayrı projede. Kullanıcılar tema yükleyip renk özelleştirebilir.
> PHP + CSS + JS ile müzik.coremusic.net üzerinden çalışır.

---

## 1. Tema Paketi Yapısı

```
theme-vaporwave.zip
├── theme.json        ← Tema yapılandırması (renk, font, spacing, vb.)
├── theme.css         ← CSS override'ları (ITCSS 7-layer uyumlu)
├── theme.js          ← JS override'ları (opsiyonel, event handler vb.)
├── preview.png       ← Önizleme resmi (800×600)
└── assets/           ← Tema özel asset'leri (ikon, font, resim)
    ├── icons/
    ├── fonts/
    └── images/
```

---

## 2. theme.json Yapısı

```json
{
  "theme_id": "vaporwave-v1",
  "name": "Neva Vaporwave",
  "version": "1.0.0",
  "author": "CoreMusic Team",
  "description": "Neon pink futuristik tema",
  "preview": "preview.png",
  "modes": ["home", "studio", "gamer", "car", "tv"],
  "variables": {
    "--eq-bg-primary": "#1a0a2e",
    "--eq-bg-secondary": "#2d1b4e",
    "--eq-accent": "#ff4fd8",
    "--eq-text-primary": "#ffffff",
    "--eq-text-secondary": "rgba(255,255,255,0.7)",
    "--eq-slider-fill": "linear-gradient(180deg, #ff4fd8, #e879a0)",
    "--eq-slider-track": "rgba(255,255,255,0.15)",
    "--eq-spectrum-gradient": "linear-gradient(0deg, #ff4fd8, #ff994f, #00ff88, #00aaff)",
    "--eq-border-radius": "8px",
    "--eq-font-family": "'Avalon Medium', 'Rajdhani', sans-serif",
    "--eq-font-size": "11px",
    "--eq-glow-color": "rgba(255,79,216,0.6)",
    "--eq-panel-shadow": "0 4px 24px rgba(255,79,216,0.15)",
    "--eq-mode-bg-home": "#1a0a2e",
    "--eq-mode-bg-studio": "#0a0a0f",
    "--eq-mode-bg-gamer": "#0d1117",
    "--eq-mode-bg-car": "#1a1a2e",
    "--eq-mode-bg-tv": "#1a1a1a"
  },
  "user_customizable": [
    "--eq-accent",
    "--eq-bg-primary",
    "--eq-text-primary",
    "--eq-border-radius"
  ]
}
```

---

## 3. Klasör Yapısı (music.coremusic.net)

```
music.coremusic.net/
├── themes/                          ← Tema ana dizini
│   ├── default/                     ← Varsayılan tema
│   │   ├── theme.json
│   │   ├── theme.css
│   │   └── preview.png
│   ├── vaporwave/                   ← Yüklenmiş tema
│   │   ├── theme.json
│   │   ├── theme.css
│   │   ├── theme.js
│   │   ├── preview.png
│   │   └── assets/
│   └── ...                          ← Diğer temalar
├── assets.coremusic.net/
│   └── Css/
│       └── themes/                  ← Tema CSS'leri (build edilmiş)
│           ├── default.css
│           └── vaporwave.css
```

---

## 4. Tema Yükleme (PHP)

```php
class ThemeManager
{
    private string $themesDir;

    public function install(string $zipPath): ThemeInfo
    {
        $zip = new \ZipArchive();
        $zip->open($zipPath);

        // 1. theme.json oku ve validate
        $themeJson = $zip->getFromName('theme.json');
        $config = json_decode($themeJson, true);
        $this->validateThemeConfig($config);

        // 2. Tema klasörü oluştur
        $themeDir = $this->themesDir . '/' . $config['theme_id'];
        mkdir($themeDir, 0755, true);

        // 3. Dosyaları çıkar
        $zip->extractTo($themeDir);
        $zip->close();

        // 4. CSS'i assets'e kopyala
        copy(
            $themeDir . '/theme.css',
            ASSETS_DIR . '/Css/themes/' . $config['theme_id'] . '.css'
        );

        // 5. Veritabanına kaydet
        $this->registerTheme($config);

        return ThemeInfo::fromConfig($config);
    }

    public function activate(string $themeId, int $userId): void
    {
        $_SESSION['active_theme'] = $themeId;
        $this->db->updateUserTheme($userId, $themeId);

        // CSS değişkenlerini <style> tag'ine inject et
        $config = $this->loadThemeConfig($themeId);
        $this->injectCSSVariables($config['variables']);
    }

    private function injectCSSVariables(array $variables): void
    {
        echo "<style id=\"theme-variables\">\n";
        echo ":root {\n";
        foreach ($variables as $key => $value) {
            echo "  {$key}: {$value};\n";
        }
        echo "}\n";
        echo "</style>";
    }
}
```

---

## 5. Tema CSS Yapısı (ITCSS Uyumlu)

```css
/* theme.css — ITCSS Layer: 01_Settings (variables override) */
:root {
  --eq-accent: #ff4fd8;
  --eq-bg-primary: #1a0a2e;
  /* ... */
}

/* 03_Generic — Reset varsa override */
.eq-panel * { box-sizing: border-box; }

/* 04_Elements — EQ HTML element'leri */
.eq-band-slider { /* ... */ }

/* 05_Objects — EQ layout grid */
.eq-panel { /* ... */ }

/* 06_Components — EQ bileşenleri */
.eq-slider__thumb { /* ... */ }
.eq-spectrum__bar { /* ... */ }

/* 07_Trumps — !important override'lar (acil durum) */
.eq-emergency-reset { /* ... */ }
```

---

## 6. Tema JS (Opsiyonel)

```javascript
// theme.js — Sadece EQ bileşenleri için JS override
window.NevaTheme = {
    onActivate: function() {
        // Tema aktif olduğunda çalışır
        console.log('Vaporwave theme activated');
    },

    onDeactivate: function() {
        // Tema deaktif olduğunda cleanup
    },

    // EQ slider custom behavior
    eqSliderAnimation: function(slider, gain) {
        // Custom slider animasyonu
    },

    // Spectrum analyzer custom render
    spectrumRenderer: function(ctx, data, width, height) {
        // Custom FFT visualization
    }
};
```

---

## 7. Admin Tema Editörü (Ayrı Proje)

```
neva-theme-editor/          ← Ayrı Git repo
├── Web UI (PHP/JS)
│   ├── Color picker
│   ├── Live preview
│   ├── Variable editor
│   └── CSS editor (syntax highlight)
├── Export (.zip package)
└── Preview generator
```

Admin bu editör ile tema oluşturur, export eder, CoreMusic'e yükler.

---

## 8. Kullanıcı Renk Özelleştirme

```json
// user_settings.theme_overrides
{
  "--eq-accent": "#00ff88",        // Kullanıcı accent rengi değiştirdi
  "--eq-bg-primary": "#0a0a0f"     // Daha koyu arka plan
}
```

Sadece `user_customizable` listesindeki değişkenler değiştirilebilir.

---

*Neva Engine EQ Theme System v1 · 2026-06-03*
