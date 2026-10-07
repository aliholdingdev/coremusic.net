---
title: "ui-analyzer Reference — Mockup Karşılaştırma Protokolü"
type: reference
category: skills
version: 1.0.0
updated: 2026-10-07
---

# Mockup Karşılaştırma Protokolü (Guardrail #11 bağlayıcı)

Bu protokol, **mockup (tasarım) ile gerçek kod/sayfa arasındaki farkları** sistematik
bulmak için kullanılır. Kaynağı: `.ai/CLAUDE.md` §7.1 (Guardrail #11) + `.ai/ui-design/`.

---

## 1. Referans Sırası (çelişkide bağlayıcı — §7.1)

```text
PNG (ilk)  >  ASCII art  >  Component Inventory  >  Tokens  >  Implementation Plan
```

1. **PNG** — `.ai/.png/` altındaki onaylı görsel (19 PNG: home-1024 12, home-1920 1,
   shared-1024 6). PNG her şeyi ezer: “ben gördüm” iddiası PNG'siz yazılmaz.
2. **ASCII art** — `.ai/ui-design/screens/00-ascii-art-index.md` (piksel ASCII layout).
3. **Component Inventory** — `.ai/ui-design/02-component-inventory.md` (C01-C16 BEM).
4. **Tokens** — `.ai/ui-design/tokens/design-tokens-master.md`.
5. **Implementation Plan** — en düşük öncelik.

**Kural:** Görsel okunamıyorsa → **DUR** + kullanıcıya bildir (Guardrail #11 ihlal prosedürü).
Uydurma piksel/renk/ölçü YASAK — okunmayan değer `⚠️ VERIFICATION REQUIRED`.

---

## 2. Karşılaştırma Adımları (ölçüm listesi)

| # | Ölçüm | Kaynak (mockup) | Kaynak (canlı/kod) | Tolerans |
|---|-------|-----------------|---------------------|----------|
| 1 | Layout bölgeleri (header/içerik/footer) | ASCII: Header 60px y:0-60, İçerik 450px y:60-510, Footer 90px y:510-600 (1024×600) | DOM ölçüleri (Chrome DevTools) | ±2px |
| 2 | Renk paleti | PNG + token tablosu | computed styles / CSS custom props | HEX birebir (token) |
| 3 | Tipografi | PNG + inventory (font, boyut, weight) | computed font | ±0.5px / ağırlık birebir |
| 4 | Grid | PNG (sütun/gutter) | CSS grid/flex ölçüleri | ±2px |
| 5 | Boşluk hiyerarşisi | token spacing scale | hesaplanmış padding/margin | token katmanına oturmalı |
| 6 | BEM sınıfları | inventory C01-C16 | DOM class'ları | birebir eşleşme |
| 7 | Cihaz davranışı | 45-tier matrisi (`01-mockup-index.md`) | media query'ler | tier kuralına uyum |

---

## 3. Sapma Sınıflandırması (severity)

| Severity | Tanım | Örnek |
|----------|-------|-------|
| **HIGH** | Layout/ölçü kırıldı — pixel reference ihlali | Footer 90px yerine 120px |
| **MEDIUM** | Token sapması / yanlış BEM adı | Ham hex ile `#ff69b4` (token yerine) |
| **LOW** | Görsel olmayan fark (yer tutucu metin vb.) | Placeholder kopya kalmış |

**Kural:** Her bulgu = **Ölçüm + Kanıt (satır/PIXEL) + Beklenen + Gerçek** 4'lüsü olmadan
yazılmaz (kanıtsız bulgu yok).

---

## 4. Durdurucu Koşullar (DUR + bildir)

- PNG okunamıyor (dosya yok / görsel bozuk) → Guardrail #11 ihlal prosedürü:
  1) kod revert, 2) `log.md` CRITICAL, 3) Vault Steward bildirimi (kod tarafında ise).
- Referans dosyalardan hiçbiri diskte yoksa → `⚠️ VERIFICATION REQUIRED` + DUR.
- 45-tier matrisinde hedef tier belirsizse → tier uydurulmaz, kullanıcıya sorulur.

---

## 5. Çıktı (bu protokolün girdisi)

ADIM 6 raporuna her karşılaştırma satırı şöyle girer:

```text
| Ölçüm | Mockup (kaynak) | Gerçek (kanıt) | Sapma | Severity |
```
