---
title: "ui-analyzer Reference — Puanlama Şeması & Rapor Şablonu"
type: reference
version: 3.0.0
updated: 2026-10-07
---

# Puanlama Şeması & Tam Rapor Şablonu (ADIM 6 çıktısının tam hali)

---

## 1. Severity Tanımı

| Severity | Kriter | Aksiyon |
|----------|--------|---------|
| **HIGH** | Layout/ölçü kırıldı (pixel reference ihlali) · token ihlali (ham hex/px, @media var()) · Guardrail ihlali | Düzeltme planı zorunlu → ui-code-generator + onay |
| **MEDIUM** | Tolerans dışı ama işlevsel sapma (±2-4px, yanlış BEM adı, konvansiyon dışı token) | Sprint planına |
| **LOW** | Kozmetik / tolerans içi (yer tutucu metin vb.) | Backlog |

---

## 2. Puanlama Şeması

```text
Başlangıç: 100 puan
  her HIGH   → −15
  her MEDIUM → −7
  her LOW    → −3
  her ⚠️ VERIFICATION REQUIRED → −5 (ayrı satır; "bulgu" sayılmaz ama belirsizlik cezası)

Skor = max(0, 100 − Σ cezalar)
```

| Skor | Sınıf | Anlamı |
|------|-------|--------|
| 90–100 | **A** | Onaya hazır — yalnız LOW/backlog |
| 75–89 | **B** | Küçük düzeltmelerle onaylanabilir |
| 60–74 | **C** | Düzeltme turu gerekli; otomatik onay yok |
| 0–59 | **D** | Onaylanmaz; HIGH'lar kapanana kadar UI işi durur |

**Kural:** Kanıtsız satır puana girmez (etiket: `⚠️ VERIFICATION REQUIRED`, §2 cezası
ayrı uygulanır). Öneriler (suggestions) puanı **düşürmez** — yalnız İHLAL'ler düşürür
(kriter ayrımı: `design-system-criteria.md` §1).

---

## 3. Zorunlu Başlık Bloğu

```markdown
# {SAYFA/BİLEŞEN ADI} — UI Analiz Raporu
- Analiz tarihi: {YYYY-MM-DD}
- Analiz yöntemi: [PNG mockup | Canlı sayfa (Chrome DevTools) | Kod (CSS/JS) | Karışık]
- Referans kaynağı: [PNG yolu / inventory / token dosyası]
- Yetki: Guardrail #11 referans sırası (PNG > ASCII > Inventory > Tokens)
```

---

## 4. Tam Rapor Şablonu (bölüm sırası değişmez)

```markdown
# {SAYFA/BİLEŞEN ADI} — UI Analiz Raporu
- Analiz tarihi / Yöntem / Referans kaynak / Yetki

## 1. Genel Bakış
- Sayfa/Bileşen · Analiz tarihi · Yöntem · Referans kaynak

## 2. Renk Paleti
| Renk | HEX | RGB | Kullanım | Kaynak (token var mı?) |
|------|-----|-----|----------|------------------------|
| Primary | #FF69B4 | 255,105,180 | Ana buton, link | var(--cm-accent-primary) |

## 3. Tipografi
| Font | Weight | Boyut | Satır Yüksekliği | Kullanım |
|------|--------|-------|------------------|----------|
| Inter | 400 | 16px | 1.5 | Gövde metni |

## 4. Grid Yapısı
| Breakpoint | Sütun | Gutter | Margin | Düzen |
|------------|-------|--------|--------|-------|
| 320px+ | 4 | 16px | 16px | Tek sütun |
| 768px+ | 8 | 24px | 32px | İki sütun |
| 1024px+ | 12 | 32px | 64px | Üç sütun |

## 5. Boşluk Hiyerarşisi
| Seviye | Değer | Kullanım |
|--------|-------|----------|
| xs | 4px | İçi boşluk (padding) |
| sm | 8px | Eleman arası boşluk |
| md | 16px | Bileşen içi boşluk |
| lg | 24px | Bölüm arası boşluk |
| xl | 32px | Büyük bölüm arası |
| 2xl | 48px | Sayfa üst boşluğu |
| 3xl | 64px | Ana bölüm başlığı |

## 6. Font Analizi
| Özellik | Değer | Not |
|---------|-------|-----|
| Font Ailesi | Inter | Google Fonts |
| Weight Aralığı | 100-900 | Tüm ağırlıklar mevcut |
| Glyph Kapsamı | Latin Extended | Türkçe karakter desteği |
| OpenType | Tabular Figures | Sayısal tablolar için |
| Web Formatı | WOFF2 | Ana format, WOFF fallback |

## 7. Responsive Breakpoint Analizi
| Breakpoint | Durum | Not |
|------------|-------|-----|
| 320px+ | ✅ Mevcut | Mobile-First |
| 768px+ | ✅ Mevcut | Tablet |
| 1024px+ | ✅ Mevcut | Desktop |
| 1440px+ | ❌ Eksik | Wide desktop |

## 8. Sorunlar ve Öneriler
### Tespit Edilen Sorunlar
1. [HIGH] {bulgu} — {kanıt dosya:satır / piksel}
### Öneriler
1. {öneri} — devir: {skill}
```

---

## 5. Sapma Tablosu (mockup karşılaştırması varsa zorunlu)

```markdown
| Ölçüm | Mockup (kaynak+değer) | Gerçek (kanıt) | Sapma | Severity |
|-------|------------------------|----------------|-------|----------|
| Footer yüksekliği | ASCII: 90px (y:510-600) | DOM: 92px | +2px | LOW (±2px tolerans içi) |
| Primary renk | token: --cm-accent-primary | computed: ham #ff69b4 | token değil | HIGH |
```

---

## 6. Öneri Kuralı

- Öneri her zaman **hangi skill'e devredildiğini** söyler: kod üretimi →
  `ui-code-generator` · WCAG → `../ui-code-generator/references/wcag-2.2-checklist.md`
  + `accessibility` · taşınacak dosya varsa → **onay + ADR** (taşıma/silme yasak).
- Öneri mevcut token'lardan türetilir; yeni tasarım **uydurmaz**.

---

## 7. Çıkış Kapanışı (puanlı)

```markdown
---
Özet: {n} HIGH · {n} MEDIUM · {n} LOW · {n} ⚠️ VERIFICATION REQUIRED
Puanlama: 100 − (15·HIGH + 7·MEDIUM + 3·LOW + 5·⚠️) = {skor} → Sınıf {A/B/C/D}
Sonraki adım: {skill/devir}
```