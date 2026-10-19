---
title: "Neva Engine — HTML/CSS Kod Örnekleri (Web Mixer Arayüzü)"
type: reference
category: nevaengine
version: 1.0.0
status: active
authority: "Master Orchestrator → UI Designer inceleme bekliyor"
updated: 2026-10-15
---

# Neva Engine — HTML/CSS Kod Örnekleri (Web Mixer Arayüzü)

**Zorunlu Bağlantılar:** [[11-cok-dilli-kod-ornekleri]] (index · 20 dil · 6 örnek şeması) · [[05-neva-mixer-web-pcm-plani]] (web mixer Layer 3) · [[10-io-format-32bit-192khz-kurallari]] (K1-K6 SSOT) · [[06-gereksinimler-ve-farkliliklar]] (R10 · R18)

> **Durum:** v1.0.0 · tarayıcıda görsel doğrulanmadı (araştırma MD) · mockup gate: gerçek UI işi için .ai/ui-design/ görseli zorunlu

## §1 Bağlam

**Neva web mixer** (doc 05): PCM'i tarayıcıya değil **çekirdeğe** işlettirir; tarayıcı yalnızca
kontrol + görselleştirme yapar (Layer 3 — vanilla JS, framework yok). Bu nedenle HTML/CSS katmanı
**DSP yapmaz, DSP'nin durumunu gösterir**: format (K1), örnekleme hızı (K4), bit-perfect kapısı
(K6), seviye (K2), gain-reduction/klips (R10·K3), cihaz/OS (R18·K5).

Bu dosya, index doc `11-cok-dilli-kod-ornekleri` §4 şemasının **10. dosyasıdır** ve 6 UI örneği
taşır (DSP dillerinin sıra numaralarıyla aynı: strip · rozet/SR · meter · worklet yükleyici ·
gain-reduction · OS rozeti).

**Durum notları (zorunlu):**

1. **Görsel doğrulanmadı:** tüm örnekler araştırma MD'sidir — tarayıcıda render, klavye ve ekran
   okuyucu testi **yapılmadı**. Üretim öncesi Faz 0 build + tarayıcı testi doc 01/03 planından geçer.
2. **Mockup gate:** bu dosya *referans doküman* olduğu için gate burada **geçerli değildir**; ancak
   **gerçek canlı UI işinde** `.ai/ui-design/` görseli okunmadan kod yazılmaz (Mockup Before Frontend —
   kök `AGENTS.md` §13). Görsel okunamıyorsa DUR ve bildir.
3. **JS ayrımı:** buradaki JS 3-5 satırlık **bağlayıcıdır**; tam JS (WebSocket meter akışı 30fps,
   worklet `process()` gömülü hesabı, SR handshake, OS tespiti detayı) → `11-examples/05-javascript.md`
   + doc 05 §2. Çakışma yok: bu dosya **tam JS üretmez**.
4. **Token kuralı:** CSS yalnız `--cm-*` okur; ham hex/px **yalnızca** "01_Abstracts token dosyasına
   eklenecek" yorumu olarak geçer (global kural: sabit değer yalnız token dosyasında).
5. Zero-hallucination: doğrulanamayan API/satır `⚠️ VERIFICATION REQUIRED` işaretlidir (§10 listesi).

### §1.1 Örnek şeması (index §4 ile aynı sıra)

| # | Örnek | Bölüm | Kural / R |
|---|---|---|---|
| 1 | Mixer kanal strip (HTML + BEM) | §2 | K1 · K2 |
| 2 | Format & SR rozetleri + bit-perfect | §3 | K1 · K4 · K6 |
| 3 | dB meter (seviye barı) | §4 | K2 |
| 4 | AudioWorklet yükleyici iskeleti | §5 | K2 · K4 |
| 5 | Kompresör/Limiter görselleştirme | §6 | R10 · K3 |
| 6 | Cihaz/OS rozeti | §7 | R18 · K5 |

### §1.2 Token kaynağı (disk kanıtı — `assets.coremusic.net/Css/01_Abstracts/`)

| Grup | Token | Dosya (kanıt) |
|---|---|---|
| Durum renkleri | `--cm-success` · `--cm-success-light` · `--cm-warning` · `--cm-warning-light` · `--cm-danger` · `--cm-danger-light` | `a-primitive-tokens.css` L57-62 |
| Aksan / odak rengi | `--cm-accent` · `--cm-accent-light` · `--cm-accent-deep` · `--cm-accent-soft` | `a-color-mode-tokens.css` L353-356 (odak halkası rengi: L353 yorumu) |
| Dokunma hedefi | `--cm-hit-target: 48px` | `a-primitive-tokens.css` L67 |
| Yazı / opaklık | `--cm-ui-font-xs` · `--cm-font-ui` · `--cm-button-disabled-opacity: 0.4` · `--cm-white` · `--cm-gray-light` · `--cm-gray-dark` | `a-primitive-tokens.css` L28-72, L109 |
| Boşluk / yarıçap / gölge | `--cm-space-*` · `--cm-radius-*` · `--cm-shadow-*` | `a-login-tokens.css` L136-156 — **kapsam notu §8.3** |
| Odak ölçüsü | prior-art `--focus-ring-width: 2px` · `--focus-ring-offset: 2px` | `a-design-tokens.css` L567-568 (prefix'siz — Neva'da `--cm-*`'e taşınacak, §8.2) |

Eksik token'lar her CSS bloğunun üstündeki **"Token kaydı — EKLENECEK"** yorumundadır; o değerler
**01_Abstracts** dosyasına eklenmeden örnek tam görünmez (kasıtlı: ham değer koda sızmaz).

---

## §2 Örnek 1 — Mixer kanal strip (K1 · K2)

Semantik: `main > section > header/h3 + footer`; tüm etkileşimli öğeler gerçek `<button>` /
`<input>` — `div`+`click` yok. Mute/solo `aria-pressed` ile durum taşır (WCAG 2.2 — 4.1.2 Name, Role,
Value). Fader ve meter okunur değer (`aria-valuetext`) içerir.

```html
<!-- Örnek 1 (K1 · K2) — kanal strip iskeleti · katman: 04_Components (c-channel-strip.css önerisi)
     K1: format rozeti 32-bit float · K2: internal seviye/processing burada görünür -->
<main class="neva-mixer" aria-label="Neva Mixer kontrol paneli">
  <section class="channel-strip" data-state="default" aria-label="Kanal 1 — giriş">
    <header class="channel-strip__head">
      <h3 class="channel-strip__title">Kanal 1</h3>
      <span class="channel-strip__badge" data-format="float32">32-bit float</span>
    </header>

    <div class="channel-strip__body">
      <input class="channel-strip__fader" type="range" id="ch1-fader"
             min="-60" max="12" step="0.5" value="-6"
             aria-label="Kanal 1 seviyesi" aria-valuetext="-6 desibel">
      <!-- meter dolumu §4 · burada iskelet -->
      <div class="channel-strip__meter" role="meter"
           aria-label="Kanal 1 seviyesi (RMS)"
           aria-valuemin="-60" aria-valuemax="0" aria-valuenow="-18"
           aria-valuetext="-18 desibel">
        <span class="channel-strip__meter-fill" data-meter="ok"></span>
      </div>
    </div>

    <footer class="channel-strip__foot">
      <button class="channel-strip__btn channel-strip__btn--mute" type="button"
              aria-pressed="false" aria-label="Kanal 1 sustur">MUTE</button>
      <button class="channel-strip__btn channel-strip__btn--solo" type="button"
              aria-pressed="false" aria-label="Kanal 1 solo">SOLO</button>
    </footer>
  </section>
</main>
```

```css
/* Örnek 1 (K1 · K2) — 04_Components · BEM: block__element--modifier
   MEVCUT token (disk kanıtı §1.2): --cm-space-lg/md/sm · --cm-radius-md/sm · --cm-shadow-sm
     · --cm-ui-font-xs · --cm-hit-target (48px) · --cm-accent* · --cm-success/warning/danger
     · --cm-button-disabled-opacity · --cm-font-ui
   EKLENECEK — ham px/renk YALNIZ 01_Abstracts token dosyasına yazılır:
     --cm-focus-ring-width: 3px      (global odak kuralı; prior-art --focus-ring-width: 2px)
     --cm-focus-ring-offset: 2px     (prior-art mevcut, a-design-tokens.css)
     --cm-border-width: 1px          (prior-art --cm-player-border-w: 1px, a-layout-tokens-1024)
     --cm-surface-panel: <hex>       (Neva panel yüzeyi — ham hex 01_Abstracts'a)
     --cm-border-hair: <hex>         (ince kenar rengi) · --cm-text-primary panel temasına uyar
     --cm-text-on-state: <hex>       (durum rengi üstü metin, 4.5:1 QA kapısında ölçülecek)
     --cm-meter-track: <hex>         (metre izi) · --cm-meter-w / --cm-meter-h / --cm-meter-tau
     --cm-strip-w: <px>              (strip genişliği — ⚠️ değer mockup gate ile netleşecek) */

.neva-mixer {
  display: flex;
  flex-wrap: wrap;
  gap: var(--cm-space-lg);
  padding: var(--cm-space-lg);
}

.channel-strip {
  display: flex;
  flex-direction: column;
  gap: var(--cm-space-md);
  inline-size: var(--cm-strip-w);
  padding: var(--cm-space-md);
  background: var(--cm-surface-panel);
  border: var(--cm-border-width) solid var(--cm-border-hair);
  border-radius: var(--cm-radius-md);
  box-shadow: var(--cm-shadow-sm);
  color: var(--cm-text-primary);
  font-family: var(--cm-font-ui);
}

.channel-strip__head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--cm-space-sm);
}

.channel-strip__title {
  margin: 0;
  font-size: var(--cm-ui-font-xs);
}

.channel-strip__badge {
  padding: var(--cm-space-sm);
  font-size: var(--cm-ui-font-xs);
  border: var(--cm-border-width) solid var(--cm-border-hair);
  border-radius: var(--cm-radius-sm);
  color: var(--cm-success);
}

.channel-strip__body {
  display: flex;
  align-items: stretch;
  gap: var(--cm-space-sm);
}

/* Fader — dikey yönelim (orientation) uyarısı: writing-mode/direction kalıbı tarayıcılar
   arasında farklı çalışabilir → ⚠️ VERIFICATION REQUIRED (Faz 0 Chrome/Firefox/Safari testi) */
.channel-strip__fader {
  writing-mode: vertical-lr;
  direction: rtl;                 /* değer artışı yukarı */
  accent-color: var(--cm-accent); /* thumb/track dolgusu — token'dan, ham renk yok */
  min-inline-size: var(--cm-hit-target);
  min-block-size: var(--cm-hit-target);
  cursor: pointer;
}
/* Özgün thumb/track kaplaması (::-webkit-slider-thumb / ::-moz-range-thumb) 04_Components
   işidir ve mockup gate ile netleşir → bu örneğe alınmadı. */

.channel-strip__meter {
  position: relative;
  overflow: hidden;
  min-inline-size: var(--cm-meter-w);
  min-block-size: var(--cm-meter-h);
  background: var(--cm-meter-track);
  border-radius: var(--cm-radius-sm);
}

.channel-strip__meter-fill {
  position: absolute;
  inset-block: 0;
  inset-inline: 0;
  transform: scaleY(var(--level, 0));   /* dolum mantığı §4'te */
  transform-origin: bottom;
  background: var(--cm-success);
  transition: transform var(--cm-meter-tau);
}

.channel-strip__btn {
  appearance: none;
  min-inline-size: var(--cm-hit-target);   /* WCAG 2.2 AA 2.5.8 ≥24px (token 48px) */
  min-block-size: var(--cm-hit-target);
  padding: var(--cm-space-sm) var(--cm-space-md);
  font: inherit;
  font-size: var(--cm-ui-font-xs);
  color: var(--cm-text-primary);
  background: transparent;
  border: var(--cm-border-width) solid var(--cm-border-hair);
  border-radius: var(--cm-radius-sm);
  cursor: pointer;
}

.channel-strip__btn:hover { background: var(--cm-accent-soft); }   /* HOVER */
.channel-strip__btn:active { background: var(--cm-accent-deep); }  /* ACTIVE */
.channel-strip__btn:disabled {                                     /* DISABLED */
  opacity: var(--cm-button-disabled-opacity);
  cursor: not-allowed;
}
.channel-strip__btn:focus-visible {                                /* FOCUSED */
  outline: var(--cm-focus-ring-width) solid var(--cm-accent);
  outline-offset: var(--cm-focus-ring-offset);
}

/* SELECTED/ERROR durumları — aria-pressed durumu JS yazar, sınıf değil */
.channel-strip__btn--mute[aria-pressed="true"] {
  background: var(--cm-danger);
  color: var(--cm-text-on-state);
}
.channel-strip__btn--solo[aria-pressed="true"] {
  background: var(--cm-warning);
  color: var(--cm-text-on-state);
}
.channel-strip[data-state="loading"] .channel-strip__meter { opacity: 0.5; }  /* LOADING */
.channel-strip[data-state="empty"] .channel-strip__body { visibility: hidden; } /* EMPTY */
```

**WCAG 2.2 AA notları:** `:focus-visible` odak halkası token'dan 3px (2.4.7 / 2.4.11) · dokunma
hedefi `--cm-hit-target` 48px (2.5.8 ≥24px eşiğinin üstü) · mute/solo yalnız renkle değil
`aria-pressed` ile ayırt edilir (1.4.1) · kontrast ≥4.5:1 **QA kapısında ölçülür** (1.4.3 — özellikle
`--cm-danger`/`--cm-warning` üzeri `--cm-text-on-state`) · `role="meter"` desteği ⚠️ VERIFICATION
REQUIRED (ARIA 1.2 rolü — dar AT desteği varsa `role="img"` + canlı metin alternatifi §10.4).

---

## §3 Örnek 2 — Format & SR rozetleri (K1 · K4 · K6)

- **K1:** "32-bit float" rozeti — giriş/çıkış zinciri float32 (doc 10 §1).
- **K4:** SR seçici — 44.1 / 48 / 96 / 192 kHz (destek listesi 44.1-192, varsayılan 48).
- **K6:** bit-perfect göstergesi — **yalnız** kaynak SR == cihaz SR **ve** format float32 ise `on`
  (doc 10 §5.3); aksi halde `off` + amber (tek noktada SRC/dither var demektir).

```html
<!-- Örnek 2 (K1 · K4 · K6) — format bar: rozet + SR seçici + bit-perfect LED -->
<section class="format-bar" aria-label="Format ve örnekleme hızı">
  <span class="format-bar__badge" data-format="float32">32-bit float</span>

  <label class="format-bar__label" for="sr-select">Örnekleme hızı (K4)</label>
  <select class="format-bar__select" id="sr-select" name="sample-rate">
    <option value="44100">44.1 kHz</option>
    <option value="48000" selected>48 kHz — varsayılan</option>
    <option value="96000">96 kHz</option>
    <option value="192000">192 kHz</option>
  </select>

  <p class="format-bar__bit-perfect" data-bit-perfect="on" role="status">
    <span class="format-bar__led" aria-hidden="true"></span>
    <span class="format-bar__bit-perfect-text">Bit-perfect (K6)</span>
  </p>
</section>
```

```css
/* Örnek 2 (K1 · K4 · K6) — 04_Components · MEVCUT: --cm-space-*, --cm-radius-*,
   --cm-ui-font-xs, --cm-success, --cm-warning, --cm-accent, --cm-hit-target
   EKLENECEK (01_Abstracts): --cm-surface-panel · --cm-border-hair <hex> · --cm-border-width: 1px
   (prior-art --cm-player-border-w: 1px, §8.2) · --cm-text-primary panel temasına uyar (§8.3) */
.format-bar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: var(--cm-space-md);
  padding: var(--cm-space-md);
  background: var(--cm-surface-panel);
  border: var(--cm-border-width) solid var(--cm-border-hair);
  border-radius: var(--cm-radius-md);
}

.format-bar__badge {
  font-size: var(--cm-ui-font-xs);
  color: var(--cm-success);
}

.format-bar__label {
  font-size: var(--cm-ui-font-xs);
}

.format-bar__select {
  min-block-size: var(--cm-hit-target);
  font: inherit;
  color: var(--cm-text-primary);
  background: var(--cm-surface-panel);
  border: var(--cm-border-width) solid var(--cm-border-hair);
  border-radius: var(--cm-radius-sm);
}
.format-bar__select:focus-visible {
  outline: var(--cm-focus-ring-width) solid var(--cm-accent);
  outline-offset: var(--cm-focus-ring-offset);
}

/* K6 — durum: SR eşitse yeşil ("on"), değilse amber ("off").
   Metin içeriği JS yazar (§3.1); CSS yalnız durum rengini/LED'i bilir. */
.format-bar__bit-perfect {
  display: flex;
  align-items: center;
  gap: var(--cm-space-sm);
  margin: 0;
  font-size: var(--cm-ui-font-xs);
}
.format-bar__led {
  display: inline-block;
  min-inline-size: var(--cm-meter-w);
  min-block-size: var(--cm-meter-w);
  border-radius: var(--cm-radius-full);
}
.format-bar__bit-perfect[data-bit-perfect="on"] { color: var(--cm-success); }
.format-bar__bit-perfect[data-bit-perfect="on"] .format-bar__led { background: var(--cm-success); }
.format-bar__bit-perfect[data-bit-perfect="off"] { color: var(--cm-warning); }
.format-bar__bit-perfect[data-bit-perfect="off"] .format-bar__led { background: var(--cm-warning); }
```

### §3.1 K6 kapak satırı (bağlayıcı — 1 satır, tam JS: 05-javascript.md)

```js
// K6: kaynak SR == cihaz SR ve format float32 → "on", aksi "off" (deviceSr: çekirdekten, R18/K5)
bitPerfectEl.dataset.bitPerfect = (srSelect.value === deviceSr && format === 'float32') ? 'on' : 'off';
```

**Not:** `<select>` bilinçli seçim — yerel klavye/tarayıcı davranışı ücretsiz gelir (2.1.1); SR
listesinde 88.2/176.4 de doc 10 K4 kapsamındadır, bu örnekte dört seçenek gösterildi. Cihaz 192
sunmuyorsa seçenek **disabled** değil, **yeniden negotiate** edilir (K5 — çekirdek tarafı).

---

## §4 Örnek 3 — dB meter (K2 görselleştirme)

- **K2:** internal processing 32-bit float → gösterilen değer zincirin RMS seviyesidir.
- Ölçek: **-60..0 dB** · bar `transform: scaleY(var(--level))` ile sürülür (`--level` 0..1).
- RMS etiketi okunur metin + `aria-valuetext` (sadece renk/bar değil, sayı da görünür — 1.4.1).
- JS bağlantısı **yalnız 3 satır** `setProperty`; WebSocket/worklet akışı → `05-javascript.md`.

```html
<!-- Örnek 3 (K2) — dB meter · yardımcı sınıf .visually-hidden 06_Utilities'a eklenecek (§8.1) -->
<div class="db-meter">
  <div class="db-meter__scale" aria-hidden="true">
    <span class="db-meter__tick" data-db="0">0</span>
    <span class="db-meter__tick" data-db="-6">-6</span>
    <span class="db-meter__tick" data-db="-12">-12</span>
    <span class="db-meter__tick" data-db="-60">-60</span>
  </div>

  <div class="db-meter__track" role="meter" data-state="ok"
       aria-label="K2 seviye (RMS)" aria-valuemin="-60" aria-valuemax="0"
       aria-valuenow="-18" aria-valuetext="-18 desibel">
    <span class="db-meter__fill"></span>
  </div>

  <p class="db-meter__rms">
    <span class="db-meter__rms-value">-18.0</span> dBFS RMS
  </p>
</div>
```

```css
/* Örnek 3 (K2) — 04_Components · MEVCUT: --cm-success/--cm-warning/--cm-danger, --cm-space-*,
   --cm-radius-sm, --cm-ui-font-xs, --cm-text-secondary*
   EKLENECEK (01_Abstracts): --cm-meter-track <hex> · --cm-meter-w · --cm-meter-h · --cm-meter-tau
   (*--cm-text-secondary yalnız a-login-tokens kapsamında — §8.3) */
.db-meter {
  display: flex;
  align-items: stretch;
  gap: var(--cm-space-sm);
}

.db-meter__scale {
  position: relative;
  min-block-size: var(--cm-meter-h);
}

.db-meter__tick {
  position: absolute;
  inset-inline-end: 0;
  transform: translateY(-50%);
  font-size: var(--cm-ui-font-xs);
}
/* Ölçek konumları yüzde tabanlı (px değil): doğrusal dB haritası -60..0 → %100..0 */
.db-meter__tick[data-db="0"]   { inset-block-start: 0%; }
.db-meter__tick[data-db="-6"]  { inset-block-start: 10%; }
.db-meter__tick[data-db="-12"] { inset-block-start: 20%; }
.db-meter__tick[data-db="-60"] { inset-block-start: 100%; }

.db-meter__track {
  position: relative;
  overflow: hidden;
  min-inline-size: var(--cm-meter-w);
  min-block-size: var(--cm-meter-h);
  background: var(--cm-meter-track);
  border-radius: var(--cm-radius-sm);
}

.db-meter__fill {
  position: absolute;
  inset: 0;
  transform: scaleY(var(--level, 0));   /* JS: -60..0 dB → 0..1 */
  transform-origin: bottom;
  background: linear-gradient(to top, var(--cm-success), var(--cm-warning), var(--cm-danger));
  transition: transform var(--cm-meter-tau);
}

.db-meter__track[data-state="clip"] .db-meter__fill { background: var(--cm-danger); } /* ERROR */
.db-meter__rms {
  margin: 0;
  font-size: var(--cm-ui-font-xs);
}
```

```js
// Örnek 3 (K2) — meter bağlayıcısı (3 satır; tam JS + WS 30fps akışı: 05-javascript.md)
const onRms = (db) => {
  fillEl.style.setProperty('--level', String(Math.min(1, Math.max(0, (db + 60) / 60))));  // -60..0 dB → 0..1
  rmsEl.textContent = `${db.toFixed(1)} dBFS RMS`;                                          // okunur etiket
  trackEl.setAttribute('aria-valuenow', String(Math.round(db)));                           // AT değeri
};
```

**Notlar:**
- HTML'de `style=""` attribute **yasak** (CSP / Guardrail #3) → seviye CSSOM `setProperty` ile yazılır.
  CSSOM'un `style-src` politikasına etkisi ve güncelleme sıklığı (rAF kuyruğu) ⚠️ VERIFICATION
  REQUIRED — Faz 0 tarayıcı testinde doğrulanır.
- Tick haritası **doğrusal dB** varsayar (tasarım kararı) — gerçek log ölçek/pozisyon mockup gate ile.
- Tepe-hold (peak-hold) çizgisi bu örneğe alınmadı (kapsam dışı, doc 05'te de tanımlı değil → UNKNOWN).

---

## §5 Örnek 4 — AudioWorklet yükleyici (K2 · K4)

HTML iskeleti + modül iskeleti. **Tam değil**: `process()` hesabı, WS köprüsü ve parametre
handshake'i `05-javascript.md` işidir.

```html
<!-- Örnek 4 (K2 · K4) — yükleyici HTML iskeleti.
     Harici modül: harici <script> etiketi — satır-içi <script> CSP ile yasak (Guardrail #3). -->
<body data-boot="neva-mixer">
  <main class="neva-mixer" aria-label="Neva Mixer kontrol paneli">
    <!-- §2 kanal strip'leri + §4 meter'lar buraya yerleştirilir -->
  </main>

  <script type="module" src="/js/neva/audio-worklet-loader.js"></script>
</body>
```

```js
// Örnek 4 (K2 · K4) — audio-worklet-loader.js İSKELETİ (TAM DEĞİL — tam JS: 05-javascript.md)
'use strict';

const ctx = new AudioContext({ sampleRate: 48000 });                 // K4 varsayılan 48 kHz
await ctx.audioWorklet.addModule('/js/neva/neva-meter-worklet.js');  // worklet modül kaydı
const meterNode = new AudioWorkletNode(ctx, 'neva-meter', { numberOfOutputs: 0 });

// ⚠️ VERIFICATION REQUIRED (Faz 0):
//  - AudioContext({ sampleRate }) seçeneğinin tarayıcı kapsamı; destek yoksa varsayılan SR
//    alınır ve K6 bit-perfect kapısı buna göre değerlendirilir (doc 10 §5.3).
//  - numberOfOutputs: 0 konfigürasyonu (yalnız ölçüm düğümü) tarayıcıda doğrulanacak.
// Worklet registerProcessor/process() gömülü hesabı + WS meter çıkışı → 05-javascript.md
```

**Notlar:**
- **Güvenli bağlam (secure context):** `http://127.0.0.1:port` localhost = güvenilir köken olarak
  sayılır → doc 05 yerel sunucu modeli AudioWorklet için uygundur (W3C Secure Contexts).
- **K2 bağlamı:** worklet render quantum'u 128 frame'tir (şema doc 11 §6: "AudioWorklet 128-frame").
- `modül`de `'use strict'` örtük de olsa açıkça yazıldı (görev şeması gereği) — ES modülleri
  her koşulda strict mode'dadır.

---

## §6 Örnek 5 — Kompresör/Limiter görselleştirme (R10 · K3)

- **R10:** gain-reduction bar **0..-12 dB** (geri emilim göstergesi).
- **K3:** limiter 0 dBFS üstü clamp + (24-bit int çıkışa düşüşte) TPDF dither — burada **yalnız
  görselleştirme**: düşüş/dither varlığı K6 `off` rozetiyle (§3) ima edilir.
- CLIP LED `data-state="off|on"` CSS durumları; animasyon + `prefers-reduced-motion` kapatması.

```html
<!-- Örnek 5 (R10 · K3) — dinamik bölüm -->
<section class="dynamics" aria-label="Kompresör ve limiter göstergesi">
  <h4 class="dynamics__title">Kompresör <span class="dynamics__meta">4:1</span></h4>

  <div class="dynamics__gr" role="meter"
       aria-label="Gain reduction" aria-valuemin="-12" aria-valuemax="0"
       aria-valuenow="-3" aria-valuetext="-3 desibel">
    <span class="dynamics__gr-fill"></span>
    <span class="dynamics__ticks" aria-hidden="true">0 · −6 · −12 dB</span>
  </div>

  <p class="dynamics__clip" data-state="off">
    <span class="dynamics__led" aria-hidden="true"></span>
    <span class="dynamics__clip-label">CLIP</span>
    <span class="visually-hidden" role="status">Limiter klipsi: kapalı</span>
  </p>
</section>
```

```css
/* Örnek 5 (R10 · K3) — 04_Components · MEVCUT: --cm-warning, --cm-danger, --cm-radius-full,
   --cm-space-*, --cm-ui-font-xs, --cm-hit-target, --cm-accent, --cm-button-disabled-opacity
   EKLENECEK (01_Abstracts — ham px/renk buraya):
     --cm-led-size: 12px · --cm-led-blink: 1s (≈1 çevrim/sn — WCAG 2.3.1 3/sn sınırının altı)
     · --cm-led-off-opacity · --cm-gr-tau: 60ms · --cm-surface-panel · --cm-border-hair <hex>
     · --cm-meter-track <hex> · --cm-meter-w (bar yüksekliği) — paylaşılan metro token'ları §4 */
.dynamics {
  display: flex;
  flex-direction: column;
  gap: var(--cm-space-sm);
  padding: var(--cm-space-md);
}

.dynamics__title {
  margin: 0;
  font-size: var(--cm-ui-font-xs);
}
.dynamics__meta { color: var(--cm-warning); }

.dynamics__gr {
  position: relative;
  min-block-size: var(--cm-meter-w);      /* yatay bar yüksekliği */
  background: var(--cm-meter-track);
  border-radius: var(--cm-radius-sm);
}

/* --gr: 0..1 aralığına normalize edilmiş gain reduction (JS: dB / -12) — tam JS: 05-javascript.md */
.dynamics__gr-fill {
  position: absolute;
  inset-block: 0;
  inset-inline-start: 0;
  inline-size: 100%;
  transform: scaleX(var(--gr, 0));
  transform-origin: left;
  background: var(--cm-warning);
  transition: transform var(--cm-gr-tau);
}
.dynamics__gr[data-state="clip"] .dynamics__gr-fill { background: var(--cm-danger); } /* ERROR */

.dynamics__ticks {
  position: absolute;
  inset-inline-end: var(--cm-space-sm);
  margin: 0;
  font-size: var(--cm-ui-font-xs);
}

.dynamics__clip {
  display: flex;
  align-items: center;
  gap: var(--cm-space-sm);
  margin: 0;
  font-size: var(--cm-ui-font-xs);
}
.dynamics__led {
  display: inline-block;
  min-inline-size: var(--cm-led-size);
  min-block-size: var(--cm-led-size);
  border-radius: var(--cm-radius-full);
  background: var(--cm-danger);
  opacity: var(--cm-led-off-opacity);
}
.dynamics__clip[data-state="on"] .dynamics__led {
  opacity: 1;
  animation: neva-clip-blink var(--cm-led-blink) ease-in-out infinite;
}
@keyframes neva-clip-blink {
  0%, 100% { opacity: 1; }
  50%      { opacity: 0.3; }
}
@media (prefers-reduced-motion: reduce) {
  .dynamics__clip[data-state="on"] .dynamics__led { animation: none; }
}
```

**Not:** `@media` bloğu içinde `var()` **okumuyoruz** (global token kuralı: `@media` içinde `var()`
ile okuma yasak) — yalnız `animation: none` kapatması var. Yanıp sönme ≤3 çevrim/sn (WCAG 2.3.1)
**token değeriyle** (`--cm-led-blink: 1s`) güvence altında; değer 01_Abstracts'ta tutulur.

---

## §7 Örnek 6 — Cihaz/OS rozeti (R18 · K5)

- **R18:** otomatik cihaz/OS tespiti → rozet sunumu; **SR kararı çekirdektedir** (K5: cihaz 192
  sunmuyorsa en yüksek yerel SR — rozet bunu *gösterir*, hesaplamaz).
- `data-os="win|mac|linux|android|ios"` (+ `unknown` güvenli başlangıç değeri).
- Renk tek başına ayırt edici değildir → **metin etiketi her zaman görünür** (WCAG 1.4.1).

```html
<!-- Örnek 6 (R18 · K5) — cihaz/OS rozeti -->
<p class="os-badge" data-os="unknown">
  <span class="os-badge__key">Sistem</span>
  <span class="os-badge__val">—</span>
</p>
```

```js
// Örnek 6 (R18 · K5) — OS tespiti (bağlayıcı; tam JS: 05-javascript.md)
const uad = navigator.userAgentData;                                   // ⚠️ yalnız Chromium ailesi
const raw = uad ? uad.platform
                : (navigator.userAgent.match(/Win|Mac|Linux|Android|iPhone|iPad/i) || [''])[0];
badgeEl.dataset.os = /win/i.test(raw) ? 'win'   : /mac/i.test(raw) ? 'mac'
                   : /android/i.test(raw) ? 'android' : /iphone|ipad|ios/i.test(raw) ? 'ios'
                   : /linux/i.test(raw) ? 'linux' : 'unknown';
badgeEl.querySelector('.os-badge__val').textContent = raw || 'Bilinmiyor';
```

```css
/* Örnek 6 (R18 · K5) — 04_Components · MEVCUT: --cm-accent*, --cm-success*, --cm-warning,
   --cm-space-*, --cm-radius-md, --cm-ui-font-xs, --cm-hit-target
   EKLENECEK (§8.2): --cm-border-width · --cm-border-hair <hex> · --cm-text-secondary (§8.3 kapsam) */
.os-badge {
  display: inline-flex;
  align-items: center;
  gap: var(--cm-space-sm);
  min-block-size: var(--cm-hit-target);
  padding: var(--cm-space-sm) var(--cm-space-md);
  font-size: var(--cm-ui-font-xs);
  border: var(--cm-border-width) solid var(--cm-border-hair);
  border-radius: var(--cm-radius-md);
}
.os-badge__key { color: var(--cm-text-secondary); }

.os-badge[data-os="win"]     { border-color: var(--cm-accent); }
.os-badge[data-os="mac"]     { border-color: var(--cm-accent-light); }
.os-badge[data-os="linux"]   { border-color: var(--cm-accent-deep); }
.os-badge[data-os="android"] { border-color: var(--cm-success); }
.os-badge[data-os="ios"]     { border-color: var(--cm-success-light); }
.os-badge[data-os="unknown"] { border-color: var(--cm-warning); }
```

**⚠️ VERIFICATION REQUIRED (§7 tespiti):**

1. `navigator.userAgentData` (UA Client Hints) **yalnız Chromium** ailesinde tanımlıdır; Firefox /
   Safari'de `undefined` → fallback `userAgent` regex **zorunlu** (kodda var). `getHighEntropyValues`
   alan kapsamı tarayıcıya bağlıdır.
2. **iPadOS**: masaüstü UA'sı taklidi (`Macintosh`) yapabilir → `ios` eşleşmeyi kaçırabilir; cihaz
   sınıfı için nihai kaynak **çekirdekten gelen cihaz raporu** (R18) olmalıdır, UA değil.
3. Cihazın yerel SR'si (K5) UA'dan **çıkarılmaz** — WS/REST ile çekirdekten gelir (doc 05 §2).

---

## §8 ITCSS / BEM / uyum notları

### §8.1 Katman sırası ve örnek yerleşimi

Sıra (css-template v4.0.0 — kök `AGENTS.md` §10): `01_Abstracts` → `02_Base` → `03_Layout` →
`04_Components` → `05_Pages` → `06_Utilities` → `07_Vendors` → `08_Devices` → `09_ViewModes` →
`10_Helpers` → `11_OAuth`.

| Örnek | Katman | Not |
|---|---|---|
| Token'lar (§8.2) | `01_Abstracts/a-*.css` | tek ev; ham hex/px buraya |
| Strip · format-bar · db-meter · dynamics · os-badge | `04_Components` | `c-*.css` adlandırması (disk: `c-badge.css` … `c-toggle.css`) |
| `.visually-hidden` (§4/§6) | `06_Utilities` | **diskte tanımlı değil** (`u-helpers-utility.css` "boşaltılmış" — yorum satırı) → **eklenecek** |
| Özgün slider thumb/track kaplaması | `04_Components` | mockup gate ile netleşir, bu örneğe alınmadı |
| Tema/renk değişimi | `01_Abstracts` | bileşen teması **bilmez**, yalnız `--cm-*` okur |

`Css/main.css` **YOKTUR** (2026-09-30 silindi) — CoreMusic'te giriş `08_Devices/*` +
`auth-bundled.css`'tir; **Neva mixer CSS boot/import zinciri bu dosyanın kapsamı dışı** (doc 05
Layer 3 — oluşturulacak, şimdilik UNKNOWN).

### §8.2 Token kuralı (bağlayıcı)

1. Sabit değer **yalnız** `01_Abstracts/a-*.css` içinde `--cm-*` ile; **ham hex/px katman
   dosyasına yazılmaz** (bu dosyada ham değer **yalnız yorum satırıdır**).
2. `@media` içinde `var()` ile **okuma yasak** (§6 örneğindeki `prefers-reduced-motion` bloğu bu
   yüzden yalnız `animation: none` yapar).
3. Bu örneklerde **eklenmesi gereken** token özeti (değerler 01_Abstracts'a yazılacak):
   `--cm-focus-ring-width: 3px` · `--cm-focus-ring-offset: 2px` · `--cm-border-width: 1px` ·
   `--cm-surface-panel` · `--cm-text-on-state` · `--cm-border-hair` · `--cm-meter-track` ·
   `--cm-meter-w` · `--cm-meter-h` · `--cm-meter-tau` · `--cm-strip-w` (⚠️ mockup gate) ·
   `--cm-led-size: 12px` · `--cm-led-blink: 1s` · `--cm-led-off-opacity` · `--cm-gr-tau: 60ms`.
   Prior-art: `--focus-ring-width: 2px` / `--focus-ring-offset: 2px` (`a-design-tokens.css`) ve
   bileşenlerde ham `outline: 3px solid var(--cm-accent)` kalıbı (`c-buttons.css` L198) — ikisi de
   **raporlandı, taşınmadı** (taşımak/silmek onay + katman kararı gerektirir).

### §8.3 Kapsam (scope) uyarısı — disk çelişkisi raporu

`--cm-space-*` · `--cm-radius-*` · `--cm-shadow-*` · `--cm-text-primary` · `--cm-surface-soft` ·
`--cm-input-border` tanımları bugün **yalnız** `01_Abstracts/a-login-tokens.css` (auth kapsamı)
içindedir. Örnekler bu `--cm-*` adlarını kullanır (görev kuralı); Neva mixer bu token'ları
**paylaşırsa** auth'a özel değerlerin genel katman'a (ör. `a-design-tokens`/`a-scale-hybrid`)
taşınması gerekir → **UI Designer + QA kararı, MO onayı**; bu dosya taşımaz, yalnızca raporlar.

### §8.4 BEM & durum yönetimi

- Format: `block__element--modifier` (disk kuralı) — örn. `channel-strip__btn--mute`.
- JS **sınıf değil** `data-*` / `aria-*` durumu yazar (`aria-pressed`, `data-bit-perfect`,
  `data-state`, `data-os`) → stil/sunum ayrımı korunur, SSR dostu.
- **Component State Matrix** (her bileşen için 9 durum):

| Durum | Seçici kalıbı (örnek) |
|---|---|
| DEFAULT | `.channel-strip[data-state="default"]` |
| HOVER | `.channel-strip__btn:hover` |
| ACTIVE | `.channel-strip__btn:active` |
| FOCUSED | `.channel-strip__btn:focus-visible` |
| DISABLED | `.channel-strip__btn:disabled` |
| ERROR | `.db-meter__track[data-state="clip"]` |
| LOADING | `.channel-strip[data-state="loading"]` |
| EMPTY | `.channel-strip[data-state="empty"]` |
| SELECTED | `.channel-strip__btn--solo[aria-pressed="true"]` |

### §8.5 WCAG 2.2 AA & kural özeti

| Kural | Uygulama (bu örnekler) |
|---|---|
| 2.4.7 / 2.4.11 odak | `:focus-visible` → `var(--cm-focus-ring-width)` (3px) + offset |
| 2.5.8 hedef ≥24px | `--cm-hit-target` (48px) — buton/select/fader |
| 1.4.3 kontrast ≥4.5:1 | **QA ölçümü** — `--cm-text-on-state` eklendikten sonra |
| 1.4.1 renk-yalnız | mute/solo `aria-pressed` + metin etiketi; OS rozeti metinli |
| 2.3.1 parlama ≤3/sn | `--cm-led-blink: 1s` + `prefers-reduced-motion` kapatması |
| 4.1.2 ad/rol/değer | `aria-label` · `aria-pressed` · `role="meter"` + `aria-valuenow/text` · `role="status"` |
| CSP (Guardrail #3) | satır-içi `style=""` / `<script>` **yok** — harici modül + CSSOM |

### §8.6 Dark tema

`html[data-mode="dark"]` + `:root` default'ları koyu (disk kanıtı: `a-color-mode-tokens.css`
L363-371). Bileşen CSS'i **tema rengini bilmez**; Neva panel token'ları (`--cm-surface-panel`,
`--cm-text-on-state`, `--cm-meter-track`) `01_Abstracts`'ta **dark-default** tanımlanacak (öneri:
`a-neva-mixer-tokens.css` — dosya adı/UI onayı: UI Designer + MO). Bileşenler yalnız `--cm-*`
okuduğu için tema değişimi tek dosyadan döner.

### §8.7 Süreç (bu dosya kapsamı)

Bu dosya **araştırma MD**dir → gate: referans doc (mockup gate bu dosya için geçerli değil).
Gerçek CSS üretimi akışı: CSS/keyword routing → **UI Designer** → **QA** (WCAG/responsive/BEM) →
**Backend** (inline style denetimi) → **Security** (CSP/vendor) → **MO commit** (subagent commit
atmaz). Guardrail #16 şablonu (`.ai/.templates/frontend/css-template.md`) canlı CSS yazımında
zorunlu okunur.

---

## §9 Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0.0 | 2026-10-15 | İlk sürüm: 6 UI örneği (strip · rozet/SR · meter · worklet · GR/CLIP · OS) + K1-K6/K5/R10/R18 atıfları + ITCSS/BEM/token notları |

**Authority:** Master Orchestrator → **UI Designer inceleme bekliyor** · örnekler tarayıcıda
**görsel doğrulanmadı** · canlı UI için mockup gate (`.ai/ui-design/`) zorunlu · JS tamamı
`05-javascript.md`'de · commit'siz (orkestratöre ait).

---

## §10 ⚠️ VERIFICATION REQUIRED listesi (özet)

1. Dikey fader kalıbı (`writing-mode: vertical-lr` + `direction: rtl`) — Chrome/Firefox/Safari Faz 0.
2. `AudioContext({ sampleRate })` tarayıcı kapsamı (§5) — destek yoksa K6 kapısı SR'ye göre yeniden.
3. `AudioWorkletNode` `numberOfOutputs: 0` konfigürasyonu (§5) — Faz 0 tarayıcı testi.
4. `role="meter"` ekran okuyucu/AT desteği (§2/§4/§6) — değilse `role="img"` + canlı metin.
5. CSSOM `setProperty` + CSP `style-src` davranışı ve rAF güncelleme sıklığı (§4).
6. `navigator.userAgentData` kapsamı + iPadOS UA taklidi (§7) — nihai kaynak çekirdek raporu (R18).
7. `--cm-strip-w` / tick haritası (doğrusal dB) / thumb kaplama — **görsel karar** → mockup gate.
8. `--cm-text-on-state` kontrastı ≥4.5:1 — QA ölçümü (1.4.3).
