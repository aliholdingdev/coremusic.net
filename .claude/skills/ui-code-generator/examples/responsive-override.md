---
title: "ui-code-generator Example — Cihaz Override (Device CSS)"
type: example
category: skills
version: 1.0.0
updated: 2026-10-07
---

# Örnek: Cihaz Override — Davranış Override Kuralı

**Kural kaynağı:** `.ai/CLAUDE.md` §7.1/§7.3 · `.ai/ui-design/05-responsive-architecture.md`
§7.4 (4K No-Center) + §12 (fallback zorunlu) · kök `AGENTS.md` §10 (taşıma yasak).

**Senaryo:** RPi5 (1024×600) footer player — cihaz dosyasında **davranış** override
isteden istek geldi: "konsol ekranında süre metni hep görünsün, gece modunda bar kalsın".

---

## 1. Ne YAPILIR, ne YAPILMAZ

| ✅ Yapılır | ❌ Yapılmaz |
|-----------|------------|
| `08_Devices/d-*.css` içinde davranış override (display, touch, orientation) | Yeni HTML/branch üretmek (Guardrail #17 — tek component) |
| CSS variables ile cihaz token override (`--cm-space-*` yeniden tanımlama) | Ham hex/px yazmak (token tek kaynak) |
| Mevcut import zincirine bağlı kalmak | Dosya taşımak/yeniden adlandırmak (AGENTS §10) |
| Fallback: override YOKSA temel davranışın çalıştığını test etmek (§12) | 4K'da ortalamama eklemek (§7.4 YASAK) |

---

## 2. Kural — Override Yalnız Davranış, Tasarım Değil

```css
/* 08_Devices/d-console.css — davranış override örneği */
@media (min-width: 1440px) {
  /* Davranış: süre metni görünürlüğü (tasarım değil) */
  .cm-footer__time { display: inline; }
}
/* 4K No-Center: §7.4 — içeriği ortala YASAK; yalnız ölçek/sınır */
@media (min-width: 3540px) {
  .cm-footer { inline-size: 100%; }   /* yayılır, ortalanmaz */
}
```

```css
/* token override — 01_Abstracts'taki değer, cihaz dosyasında koşullu değişim */
@layer devices {
  @media (max-width: 640px) {
    :root { --cm-footer-height: 72px; }  /* davranış/space token'ı — ham 90px DEĞİL */
  }
}
```

---

## 3. Zorunlu Test Matrisi (override sonrası)

| Tier | Beklenen | Test |
|------|----------|------|
| 1024×600 (RPi5 reference) | footer 90px (±2px), bar 4px | browser + DOM ölç |
| 320 (fallback) | override OLMADAN temel davranış ✓ (§12) | browser |
| 1440+ konsol | süre görünür (davranış) | browser |
| 3540/3840 | ortalamama YOK; içerik yayılır (§7.4) | screenshot |

**Fark:** override'lı tier geçti diye **fallback testi atlanmaz** — §12 "geriye dönük
uyumluluk fallback ZORUNLU".

---

## 4. Örnek Sapma Raporu (ui-analyzer devriyle döngü)

```text
[OVERRIDE-01] d-console.css — süre display:inline (davranış) → OK
[OVERRIDE-02] d-console.css — HEX #000 yazımı → SAPMA HIGH (token'a bağlanmalı)
  → ui-analyzer raporu → düzeltme: var(--cm-text) — taşıma YOK, satır edit
```

---

## Öğrenilecek dersler

1. **Cihaz CSS = behavioral override** (Guardrail #17) — yeni component/branch yasak.
2. **Fallback her zaman test edilir** (§12) — override'lı tier tek başına yetmez.
3. **4K No-Center** (§7.4) — büyük ekranda ortalamama YASAK; override bunu ihlal edemez.
4. **Taşıma/silme YOK** — override, mevcut import zincirine eklenir; dosya adı sabit.
