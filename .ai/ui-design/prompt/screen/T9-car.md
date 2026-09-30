---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Screen Prompt T9 Automotive"
type: prompt
category: ui-design
date: 2026-09-20
version: 2.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
status: active
tier: T9-car
---

# T9: Automotive Screen Prompt

## AI Code Generation Prompt

### Context

| Özellik | Değer |
|---------|-------|
| Tier | T9-car |
| Viewport | Değişken (720×480 — 1920×1200) |
| Cihaz | Android Auto, CarPlay, araç içi bilgi-eğlence |
| Input | Large touch + Voice control |
| Layout Pattern | Simplified grid |
| Orientation | Landscape (varsayılan) |
| Safe Area | Araç paneli sınırları |
| CSS Media Query | `@media (pointer: coarse) and (min-width: 720px)` |

---

### Required Inputs

| Kural | Değer | Gerekçe |
|-------|-------|---------|
| Min touch target | 80×80px | Sürüş sırasında kolay kullanım |
| Max text amount | Minimal | Dikkat dağıtma |
| Animation | ≥400ms | Yavaş geçiş |
| Color contrast | ≥7:1 | Güneş ışığında okunabilirlik |
| Voice control | Zorunlu | Sürüş güvenliği — matrix "Safety-first, ... voice-first, driver mode" (00-device-matrix.md L182); Input = Touch+Voice AU-T29/T30 (L176-L179); entegrasyon bu dosya L304; istisna: AU-T30 Tesla satırı Input = Touch (L180) |
| Max menu depth | 2 | Hızlı erişim |
| Font scale | 1.8× | Büyük okunabilirlik |

---

**Genel Kurallar**

| Kural | Değer |
|-------|-------|
| Touch target min | 80×80px |
| Touch target rec | 96×96px |
| Touch spacing | ≥24px |
| Font scale | 1.8× |
| Base size | 28.8px |
| Min font size | 24px |
| Max font size | 48px |
| Header height | Yok (simplified) |
| Footer height | 100px (transport bar) |
| Grid | 2-3 sütun max |
| Glass blur | Yok (performans + güvenlik) |
| Hover | YOKTUR |
| Background | Koyu, düşük parlaklık |
| Kontrast | ≥7:1 |

---

### ASCII Reference

> ⚠️ KAYNAK YOK — screens/ içinde bu tier'a (veya bu sayfaya) ait ASCII karşılığı bulunamadı. Gerekçe: 00-ascii-art-index.md §3–§5 listesi yalnız T07-embedded (12 dosya, 1024×600) + shared/auth (6 dosya, 1024×600) + T17-monitor-22fhd (2 dosya, 1920×1080) kapsar; 01-mockup-index.md L221 viewport kapsamı da 2 (1024×600, 1920×1080) ile sınırlıdır. Kutu ölçüsü kaynağı olmadığından ölçüsüz wireframe üretilmedi.

### Prompt Template

```json
{
  "task": "Create automotive screen for CoreMusic",
  "screen": "automotive",
  "viewport": "720x480 - 1920x1200",
  "tier": "T9-car",
  "components": [
    "now-playing",
    "quick-actions",
    "navigation-hint",
    "transport-controls"
  ],
  "tokens": {
    "--cm-text-2xl": "1.5rem",
    "--cm-space-6": "1.5rem"
  }
}
```

### Expected Output

```css
@media (pointer: coarse) and (min-width: 720px) {
  :root {
    --footer-h: 100px;
    --grid-gap: 24px;
    --font-scale: 1.8;
    --touch-min: 80px;
    --touch-rec: 96px;
  }

  body {
    background: #0a0a0f;
    color: #ffffff;
    font-family: 'Arima', sans-serif;
    font-size: 28.8px;
    line-height: 1.4;
    -webkit-font-smoothing: antialiased;
    user-select: none;
    -webkit-user-select: none;
  }

  .main-content {
    padding: 24px;
    padding-bottom: calc(var(--footer-h) + 24px);
    min-height: 100dvh;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--grid-gap);
  }

  .car-card {
    background: #1a1a24;
    border-radius: var(--radius-lg);
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  .car-card--now-playing {
    grid-column: span 2;
    flex-direction: row;
    align-items: center;
    gap: 24px;
  }

  .car-card__cover {
    width: 120px;
    height: 120px;
    border-radius: var(--radius-lg);
    flex-shrink: 0;
  }

  .car-card__info {
    flex: 1;
    min-width: 0;
  }

  .car-card__title {
    font-size: 36px;
    font-weight: 700;
    color: #ffffff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .car-card__artist {
    font-size: 24px;
    color: #b0b0c0;
  }

  .car-touch-target {
    min-width: var(--touch-min);
    min-height: var(--touch-min);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--radius-lg);
    background: #1a1a24;
    color: #ffffff;
    font-size: 24px;
    touch-action: manipulation;
    -webkit-tap-highlight-color: transparent;
  }

  .car-touch-target:active {
    background: var(--accent);
    transform: scale(0.95);
    transition: all 100ms ease;
  }

  .car-transport {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: var(--footer-h);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 24px;
    background: #12121a;
    border-top: 1px solid rgba(255,255,255,0.10);
    z-index: 1000;
    padding: 0 24px;
  }

  .car-transport__btn {
    min-width: 80px;
    min-height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #1a1a24;
    color: #ffffff;
    touch-action: manipulation;
  }

  .car-transport__btn--play {
    min-width: 96px;
    min-height: 96px;
    background: var(--accent);
  }

  .car-transport__btn:active {
    transform: scale(0.9);
    transition: transform 100ms ease;
  }

  .car-voice-btn {
    min-width: 96px;
    min-height: 96px;
    border-radius: 50%;
    background: var(--accent);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    touch-action: manipulation;
    animation: voice-pulse 2s ease-in-out infinite;
  }

  @keyframes voice-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(124, 92, 255, 0.4); }
    50% { box-shadow: 0 0 0 12px rgba(124, 92, 255, 0); }
  }

  /* Hover devre dışı */
  *:hover { /* boş */ }

  /* Scroll bar gizli */
  * { scrollbar-width: none; }
  *::-webkit-scrollbar { display: none; }

  /* Düşük parlaklık modu */
  @media (prefers-reduced-motion: reduce) {
    * { animation: none !important; transition-duration: 0ms !important; }
  }
}
```

---

### Validation

- [ ] Dokunma hedefi en az 48px proje zeminine göre ölçülür (AA SC 2.5.8 = 24px; 44px yalnız AAA referansı) (kanıt: 04-accessibility-gaps.md L27, L37-L41)
- [ ] Gövde ve normal etiket metni kontrastı ≥ 4.5:1 (Primary 18.1:1, Secondary 9.8:1 ✅); Tertiary #707088 4.2:1 ❌ → #8888a0 5.2:1 düzeltmesi uygulanmış (kanıt: 04-accessibility-gaps.md L105-L112, L118)
- [ ] Odaklanabilir tüm öğelerde `outline: 2px solid var(--cm-primary); outline-offset: 2px` görünür; mouse kullanıcısında `outline: none` (kanıt: 04-accessibility-gaps.md L197-L207)
- [ ] `Tab` sırası DOM sırasıyla doğal; kaybolan/kirli odak yok, Toggle/Slider `:focus-visible` belirteci korunmuş (kanıt: 04-accessibility-gaps.md L130-L138, L144-L152)
- [ ] `aria-label`/`role`/`aria-live` eşlemesi korunmuş; Input aria-live, Toast/Toggle/Progress aria-label eksikleri giderilmiş (kanıt: 04-accessibility-gaps.md L167-L175)
- [ ] `prefers-reduced-motion` altında animasyon/transition süreleri `0.01ms`, iteration-count 1 (kanıt: 04-accessibility-gaps.md L181-L190)

---

### Ekran Promptları

### 4.1 Home (Dashboard)

| Bileşen | Konum | Token |
|---------|-------|-------|
| Now Playing | Large card, sol | `--radius-lg: 16px` |
| Quick Actions | 2-3 buton, sağ | `--touch-min: 80px` |
| Navigation Hint | Alt bar | `--text-muted` |
| Transport Controls | Bottom fixed 100px | `--footer-h: 100px` |

**Notlar:** Sürüş sırasında minimum etkileşim. Voice-first tasarım.

### 4.2 Player

| Bileşen | Konum | Token |
|---------|-------|-------|
| Cover Art | 120×120px, sol | `--radius-lg: 16px` |
| Track Info | Büyük font | `--font-size-2xl: 48px` |
| Controls | Play 96px, others 80px | `--touch-min: 80px` |
| Seek Bar | Full-width, 8px | `--accent` |
| Voice Button | Large, center | `--touch-min: 96px` |

### 4.3 Settings (Minimal)

| Bileşen | Konum | Token |
|---------|-------|-------|
| Volume | Slider, 80px high | `--touch-min: 80px` |
| Source | Toggle buttons | `--touch-min: 80px` |
| EQ Preset | 3-4 buton | `--touch-min: 80px` |

---

### Voice Control Entegrasyonu

```javascript
// Voice command mapping
const voiceCommands = {
  'oynat': () => play(),
  'duraklat': () => pause(),
  'sonraki': () => nextTrack(),
  'önceki': () => prevTrack(),
  'sesi aç': () => volumeUp(),
  'sesi kıs': () => volumeDown(),
  'radyo aç': () => openRadio(),
  'sesli asistan': () => activateVoiceAssistant()
};
```

---

### Yasaklar

| Yasak | Doğru |
|-------|-------|
| Touch target < 80px | Min 80×80px |
| Çok fazla metin | Minimal, large font |
| Animasyon < 400ms | Min 400ms |
| Derin menü (3+ seviye) | Max 2 seviye |
| Kontrast < 7:1 | Min 7:1 |
| Glass blur | Yok |
| Kompleks grid | Max 3 sütun |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
