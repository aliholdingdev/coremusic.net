---
title: "ui-analyzer Reference — WCAG 2.2 AA Kriter Matrisi"
type: reference
version: 4.0.0
updated: 2026-10-21
---

# WCAG 2.2 AA Kriter Matrisi (Doğrulanmış)

`SKILL.md` §8'in derinlik dosyası (orphan=0). **Tüm eşikler `sources.md` S1-S3'ten
2026-10-21'de doğrulandı** (W3C Recommendation 12 December 2024). Bu dosyadaki sayısal
eşikler yeniden doğrulanmadan değişmez; değişirse `sources.md` + `SKILL.md` §8 senkronlanır.

> Derin WCAG denetimi bu skill'in işi **değildir** → devir:
> `../ui-code-generator/references/wcag-2.2-checklist.md` + `accessibility` skill'i.
> Bu dosya, analiz sırasında **ön-hesap ve sınıflandırma** için eşik tablosudur.

---

## 1. Metin Kontrastı

| Kriter | Level | Eşik | Kapsam | Not (kaynak) |
|--------|-------|------|--------|--------------|
| 1.4.3 Contrast (Minimum) | AA | **4.5:1** | Normal metin | Yuvarlanmaz: 4.499 geçersiz (S2) |
| 1.4.3 Contrast (Minimum) | AA | **3:1** | Large text | ≥18pt veya ≥14pt bold ≈ **24px / 18.5px** (S2) |
| 1.4.11 Non-text Contrast | AA | **3:1** | UI bileşenleri + grafikler | Sınır, odak halkası, ikon, grafik (S1) |

**Formül (S2):** `oran = (L1+0.05)/(L2+0.05)` · `L = 0.2126R + 0.7152G + 0.0722B` (linear sRGB).

**Kapsam notları (S2):** placeholder metni ve tooltip metni kritere tabidir ·
pasif (inactive) bileşen muaf, ancak kullanıcı etkinleştirince tekrar tabidir ·
logo/marka adları muaf (SC 1.4.3 istisnası).

---

## 2. Dokunma Hedefi / Etkileşim Alanı

| Kriter | Level | Eşik | İstisna |
|--------|-------|------|---------|
| 2.5.8 Target Size (Minimum) | **AA** | **24×24 CSS px** | 5 istisna: Spacing, Equivalent, Inline, User Agent Control, Essential |
| 2.5.5 Target Size Enhanced | AAA | 44×44 CSS px | **AA denetiminde bağlayıcı değil** |
| Proje zemini (CoreMusic) | — | **48px** (`--touch-min`, C2) | WCAG'dan katıdır; 24px'e düşürme YASAK |

- **Spacing testi (S3):** hedefin kendisi 24px'ten küçükse, hedef merkezinde **24px
  çaplı daire** alanına başka hedef giriyorsa ihlal vardır.
- **Onaylı teknik C42 (S3):** `min-height` / `min-width` ile 24px garantisi.
- Raporda **her iki eşik ayrı satır** yazılır: `WCAG AA 24px: UYGUN/İHLAL` ·
  `Proje 48px: UYGUN/İHLAL`. Proje eşiği ihlali WCAG ihlali sayılmaz (ayrı severity).
- Tolerans/ölçüm: alan = `getBoundingClientRect()` genişlik × yükseklik (S4, salt-okunur).

---

## 3. Görünür Odak (Keyboard)

| Kriter | Level | Kural | Proje zemini |
|--------|-------|-------|--------------|
| 2.4.7 Focus Visible | **AA** | Klavye odak göstergesi görünür olmalı | Odak outline **3px**, `--cm-*` token ile |
| 2.4.11 Focus Not Obscured (Min) | **AA (2.2 yeni)** | Odaklanmış eleman tamamen kapatılmamalı | Sticky header/footer altında kalma denetimi |
| 2.4.13 Focus Appearance | AAA | (2.2 — yalnız AAA) | Bağlayıcı değil |

**Analiz sorusu:** `:focus-visible` tanımı var mı · outline `none` + yerine bir şey
konmamış mı (→ HIGH ihlal adayı) · sticky eleman odakta mı örtüyor (→ 2.4.11 adayı).

---

## 4. Reflow / Responsive (2.2 öncesi AA, hâlâ geçerli)

| Kriter | Level | Eşik |
|--------|-------|------|
| 1.4.10 Reflow | AA | **320 CSS px** genişlikte tek sütun akış (dikey okuma için 256 CSS px) — 400% zoom'da kaydırma yok |
| 1.4.4 Resize Text | AA | 200% zoom'da içerik kaybolmaz |
| 1.4.12 Text Spacing | AA | Satır yüksekliği/karakter arası/paragraf/harf arası artırılınca içerik kaybı yok |

---

## 5. 2.2 ile GELEN ve ÇIKAN Kriterler (S1)

**Seviye AA ile gelenler (denetim listesine eklenir):**

| Kriter | Başlık | Level |
|--------|--------|-------|
| 2.4.11 | Focus Not Obscured (Min) | AA |
| 2.4.12 | Focus Not Obscured (Enhanced) | AAA |
| 2.4.13 | Focus Appearance | AAA |
| 2.5.7 | Dragging Movements | AA |
| 2.5.8 | Target Size (Minimum) | AA |
| 3.2.6 | Consistent Help | A |
| 3.3.7 | Redundant Entry | A |
| 3.3.8 | Accessible Authentication (Minimum) | AA |
| 3.3.9 | Accessible Authentication (Enhanced) | AAA |

**Çıkan:** **4.1.1 Parsing, WCAG 2.2'de silinmiştir** — yeni denetimde listelenmez.

**2.5.7 Dragging Movements (AA):** sürükle-bırak için eşdeğer tek-tıklama/klavye
alternatimi yoksa ihlal (ör. slider'ı sürüklemek zorunluysa).

---

## 6. Analizde Severity Eşlemesi (ön-hesap)

| Bulgunun durumu | Severity | Aksiyon |
|-----------------|----------|---------|
| AA eşiği açıkça ihlal + ölçüm kanıtı var (ör. 3.1:1) | HIGH | Düzeltme planı → `ui-code-generator` + `accessibility` |
| AA adayı ama ölçüm tam değil (kontrast hesaplanamadı) | `⚠️ VERIFICATION REQUIRED` (−5 puan) | Ölçüm tamamlanır; sonuç yoksa varsayım yazılmaz |
| Proje zemini (48px / outline 3px) ihlali, WCAG AA uygun | MEDIUM | Tasarım sistemi ihlali — ayrı rapor satırı |
| AAA eşiği (44×44 vb.) kaçırıldı | LOW (bilgi) | Bağlayıcı değil; not olarak yazılır |

**Ölçüm kanıtı formatı:**

```text
[WCAG-NN] {kriter no} {dosya:satır veya DOM seçicisi} — ölçülen: {değer}
  Beklenen: {eşik} (kaynak: sources.md S{n}) · Severity: {…}
```

---

## 7. Yeni Kriter Eklerken (Truth Mode)

1. `sources.md` §1'e kaynak ekle (Ad + URL + tür + erişim tarihi + ne doğrulandı).
2. Bu matrise tablo satırı ekle.
3. `SKILL.md` §8 özet eşiklerini senkronla.
4. Eşiği hafızadan yazma — fetch + karşılaştırma yap; çelişki varsa üst karar iste.

*CoreMusic Skill v3.0 — metadata.version: 4.0.0 — Updated: 2026-10-21*
