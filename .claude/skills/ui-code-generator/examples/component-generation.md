---
title: "ui-code-generator Example — Spektan Bileşen Üretimi (ADIM 1→8)"
type: example
category: skills
version: 1.0.0
updated: 2026-10-07
---

# Örnek: Spektan Bileşen Üretimi (Tam Akış)

**Girdi (spekt):** `02-component-inventory.md` C07 — `cm-footer__progress` (footer player
progress bar): 1024×600 layout'da footer y:510-600 (90px), progress bar 4px yükseklik,
`--cm-accent-primary` token, tıkla/sürükle seek.

---

## ADIM 1 — Girdi analizi

```text
PNG: .ai/.png/home-1024/ (footer bölgesi) — OKUNDU
Inventory: C07 ölçüler — 4px bar, radius var(--cm-radius-full)
Mevcut kod: CSS katmanları okundu (04_Components var mı? → In-Place, yoksa ekle)
```

## ADIM 2 — WCAG 2.2 AA kontrolü

```text
Kontrast: bar rengi --cm-accent-primary ↔ arkaplan --cm-surface → ratio hesapla ≥3:1 (UI)
Dokunmatik: seek alanı yükseklik ≥24px (4px bar'ın TIKLANABİLİR sargısı 24px olmalı!)
ARIA: role="slider" + aria-valuemin/max/now + aria-label="Şarkı ilerlemesi"
Klavye: ←/→ 5sn seek · Space play/pause OLMALI mı? (footer geneli — bileşen kapsamında değil)
```

## ADIM 3 — Breakpoint

```text
320-767: bar tam genişlik, süre metni gizli (ellipsis)
768-1023: süre görünür
1024+ (reference): inventory ölçüsü birebir
1440+: max-width container'a otur (ekran ortalamama YOK — §7.4 4K No-Center!)
```

## ADIM 4 — ITCSS katmanı

```text
→ Components: 04_Components/c-footer.css (yeni dosya DEĞİL, varsa mevcut dosyaya ekle)
→ Token yoksa: 01_Abstracts/a-* içine ekleme YETKİSİ YOK → mevcut token'ı kullan
```

## ADIM 5 — HTML

```html
<div class="cm-footer__progress" data-progress>
  <div class="cm-footer__bar" role="slider" tabindex="0"
       aria-label="Şarkı ilerlemesi" aria-valuemin="0" aria-valuemax="100"
       aria-valuenow="0" aria-valuetext="0 saniye">
    <span class="cm-footer__fill" aria-hidden="true"></span>
  </div>
  <span class="cm-footer__time" aria-hidden="true">0:00</span>
</div>
```

*(Semantik: bölüm landmark'ı footer içinde; `aria-hidden` tekrar eden görsel bilgide.)*

## ADIM 6 — CSS (token + BEM + @layer — özet)

```css
/* 04_Components/c-footer.css — @layer components { … } içinde */
.cm-footer__bar {
  position: relative;
  block-size: 4px;
  inline-size: 100%;
  background: var(--cm-surface-muted);
  border-radius: var(--cm-radius-full);
  /* dokunmatik sargı: 24px hedef — pointer-events yalnız bar+sargı */
  padding-block: 10px;
  box-sizing: content-box;
}
.cm-footer__bar:focus-visible {
  outline: 2px solid var(--cm-focus-ring);
  outline-offset: 2px;
}
.cm-footer__fill {
  background: var(--cm-accent-primary);       /* ham hex YASAK */
  block-size: 100%;
  inline-size: var(--progress, 0%);
  border-radius: inherit;
}
/* mobile-first: 1024 reference ölçüsü inventory'den; yalnız davranış override */
@media (max-width: 767px) {
  .cm-footer__time { display: none; }
}
```

*(Magic number yok — 4px/10px/24px token'lara bağlanacak; token yoksa 01_Abstracts'ta
`--cm-control-touch: 24px` önerilir — ÜRETİM onayı gerektirir.)*

## ADIM 7 — JavaScript (vanilla, özet)

```javascript
const fill = document.querySelector('.cm-footer__fill');
const bar = document.querySelector('[data-progress] .cm-footer__bar');
let dragging = false;

bar.addEventListener('pointerdown', (e) => {
  dragging = true;
  bar.setPointerCapture(e.pointerId);
});
bar.addEventListener('pointermove', (e) => {           // event delegation (parent)
  if (!dragging) return;
  const pct = ((e.clientX - bar.getBoundingClientRect().left) / bar.clientWidth) * 100;
  fill.style.setProperty('--progress', `${Math.min(100, Math.max(0, pct))}%`);
  bar.setAttribute('aria-valuenow', String(Math.round(pct)));  // textContent/attr — innerHTML YOK
});
bar.addEventListener('pointerup', () => { dragging = false; /* seek fetch → AbortController */ });
// var YASAK → const/let ✓ · fetch: AbortController zorunlu (SKILL §ADIM 7)
```

## ADIM 8 — PHP (bu bileşen için gerekmez → atlandı, gerekçe: istemci tarafı bileşen)

---

## Teslim Öncesi (HARD LIMITS + WCAG checklist)

```markdown
## WCAG 2.2 AA
- [ ] Kontrast ≥3:1 (UI) — token kombinasyonu hesaplandı mı? [VERIFY: ratio yaz]
- [ ] Dokunmatik alan ≥24px (sargı ile)
- [ ] ARIA: role/valuenow/valuetext mevcut
- [ ] Klavye: focus-visible + ←/→ (bar üzerinde)

## Test Senaryoları
- [ ] 1024×600: footer 90px, bar 4px (±2px) — browser test ZORUNLU
- [ ] 320px: süre gizli, bar tam genişlik
- [ ] Tıkla-seek: pozisyon güncellenir, aria-valuenow eşitlenir
- [ ] innerHTML/var/framework grep: 0
- [ ] ham hex grep (01_Abstracts dışı): 0
```

**Devir:** browser testi → `verify-loop` (veya browser-testing); analiz geri bildirimi → `ui-analyzer`.
