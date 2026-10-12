---
title: "ui-analyzer Reference — Analiz Raporu Formatı"
type: reference
category: skills
version: 1.0.0
updated: 2026-10-07
---

# Analiz Raporu Formatı (ADIM 6 çıktısının tam hali)

---

## 1. Zorunlu Başlık Bloğu

```markdown
# {SAYFA/BİLEŞEN ADI} — UI Analiz Raporu
- Analiz tarihi: {YYYY-MM-DD}
- Analiz yöntemi: [PNG mockup | Canlı sayfa (Chrome DevTools) | Kod (CSS/JS) | Karışık]
- Referans kaynağı: [PNG yolu / inventory / token dosyası]
- Yetki: Guardrail #11 referans sırası (PNG > ASCII > Inventory > Tokens)
```

## 2. Zorunlu Bölümler (sıra değişmez)

1. **Genel Bakış** — ne analiz edildi, hangi yöntemle
2. **Renk Paleti** — tablo: Renk | HEX | Kullanım | Kaynak (token var mı?)
3. **Tipografi** — tablo: Font | Weight | Boyut | Satır Yüksekliği | Kullanım
4. **Grid Yapısı** — tablo: Breakpoint | Sütun | Gutter | Margin
5. **Boşluk Hiyerarşisi** — tablo: Seviye | Değer | Kullanım
6. **Font Analizi** — tablo: Özellik | Değer | Not (§ SKILL.md 5)
7. **Responsive Breakpoint Analizi** — tablo: Breakpoint | Durum | Not
8. **Sorunlar ve Öneriler** — (a) Tespit Edilen Sorunlar (b) Öneriler

## 3. Sapma Tablosu (mockup karşılaştırması varsa zorunlu)

```markdown
| Ölçüm | Mockup (kaynak+değer) | Gerçek (kanıt) | Sapma | Severity |
|-------|------------------------|----------------|-------|----------|
| Footer yüksekliği | ASCII: 90px (y:510-600) | DOM: 92px | +2px | LOW (±2px tolerans içi) |
| Primary renk | token: --cm-accent | computed: #ff69b4 | − | HIGH (ham hex!) |
```

## 4. Severity Tanımı

| Severity | Kriter | Aksiyon |
|----------|--------|---------|
| HIGH | Layout kırıldı / token ihlali / Guardrail ihlali | Düzeltme planı zorunlu (ui-code-generator + onay) |
| MEDIUM | Sapma tolerans dışı ama işlevsel (±2-4px, yanlış BEM) | Sprint planına |
| LOW | Kozmetik / tolerans içi | Backlog |

**Kanıt kuralı:** Her bulgu = Dosya/satır VEYA piksel/ölçü kanıtı olmadan yazılmaz.
Kanıtsız iddia `⚠️ VERIFICATION REQUIRED` olarak etiketlenir; "bulgu" sayılmaz.

## 5. Öneri Kuralı

- Öneri her zaman **hangi skill'e devredildiğini** söyler:
  kod üretimi → `ui-code-generator` · erişilebilirlik → `accessibility` (global) ·
  taşınacak dosya varsa → **onay + ADR** (taşıma/silme yasak).
- Öneri, mevcut token/tabslophanesiz yeni tasarım **uydurmaz** — eldeki token'lardan önerir.

## 6. Çıkış Kapanışı

```markdown
---
Özet: {n} HIGH · {n} MEDIUM · {n} LOW · {n} ⚠️ VERIFICATION REQUIRED
Sonraki adım: {skill/devir}
```
