---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Power Governor Screen Specification"
type: spec
category: ui-design
date: 2026-10-25
status: draft
version: 0.1.0
tier: "⚠️ VERIFICATION REQUIRED (pro.coremusic.net — 00-device-matrix'te satırı yok)"
viewport: "⚠️ VERIFICATION REQUIRED — PNG bekleniyor"
device: "Pro Control Panel — web (pro.coremusic.net)"
authority: "Derived — SSOT: .ai/electronic/amplifier/power-governor-screen-spec.md (bu dosya) · ui-design/screens/pro/'ye taşınma: tier + PNG sonrası kullanıcı onayı"
governance: Red Team · Human Mode · Truth Mode
---

# Power Governor — Kanal Güç Kontrolü Ekran Spec'i (Kalıp D)

**Zorunlu Bağlantılar:** [[index]] · [[product-line-requirements]] · [[coding-panel]] · [[../ui-design/00-device-matrix]] · [[../ui-design/01-mockup-index]] · [[../ui-design/02-component-inventory]] · [[../ui-design/tokens/design-tokens-master]]

## 1. ASCII Layout (x:0-W, y:0-H · W/H PNG bekleniyor)

```
+--------------------------------------------------+
| y:0   UST SERIT (h:%12): cihaz secici (C14) ·    |
|       mod anahtari Otomatik/Manuel (C08) ·       |
|       [ACIL MUTE] (C04, --cm-error)              |
| y:12  KONTROL SERIDI (h:%10): empedans 4/8/16   |
|       (C06) · hoparlor profili secici (C14) ·    |
|       tavan kilidi rozeti (C10)                  |
| y:22  KANAL GRID 3x3 (h:%48): 8 kanal + 1 BAS · |
|       watt degeri + kilidi rozeti + slider (C09) |
| y:70  AUDIT LOG TABLOSU (h:%30): zaman · kanal · |
|       eski->yeni watt · mod · kullanici         |
+--------------------------------------------------+
```

**Toplam:** %12 + %10 + %48 + %30 = %100 ✓ — piksel eksen/ölçü UYDURULMAZ (Guardrail #11), PNG ile tamamlanır.
**Gereksinim kaynağı:** [[product-line-requirements]] §3 — Otomatik (default): hoparlör profili + empedans doğrulaması → watt tavanı rating'e kilitlenir (slider kilitli) · Manuel: kanal başına watt slider + empedans kilidi (4/8/16 Ω) · Güvenlik (her iki mod): acil mute + her değişiklik audit log'a.

## 2. BEM Sınıfları

| Bölüm | Block | Element / Modifier | Envanter |
|-------|-------|--------------------|----------|
| Cihaz seçici | `.pg-device` | `__select`, `--loading` | C14 |
| Mod anahtarı | `.pg-mode` | `__opt`, `--auto`, `--manual` | C08 |
| Acil mute | `.pg-emergency-mute` | `__label`, `--armed` | C04 |
| Empedans seçici | `.pg-impedance` | `__opt`, `--active` | C06 |
| Hoparlör profili | `.pg-profile` | `__select`, `__lock` | C14 |
| Kanal kartı (8+1 BAS) | `.pg-channel` | `__watt`, `__lock-badge`, `__slider`, `--bas`, `--locked` | C03 · C09 · C10 |
| Audit log | `.pg-audit` | `__table`, `__row`, `__cell` | — |
| Bildirim | `.pg-toasts` | `__item`, `--error` | C16 |

> ⚠️ **VERIFICATION REQUIRED:** `pg-*` ekran özel namespace'dir; `Cxx` adları envanterle birebir (C03 Card · C04 Button · C06 Tab · C08 Toggle · C09 Slider · C10 Badge · C14 Dropdown · C16 Toast). **Tablo bileşeni envanterde YOK** → uydurma `Cxx` yazılmaz.

## 3. Token Referansları

| Kullanım | Token |
|----------|-------|
| Otomatik/aktif vurgu | `var(--cm-primary)` |
| Acil mute yüzeyi | `var(--cm-error)` |
| Tavan kilidi rozeti (kilitli/serbest) | `var(--cm-warning)` · `var(--cm-accent)` |
| Odak halkası (3px) | `var(--cm-focus-ring)` |
| Kanal kartı zemini / seçili kanal | `var(--cm-bg-surface)` · `var(--cm-selected-bg)` |
| Yükseklik: buton 48 · acil mute 56 · toggle 32 · tab/satır 48 | `var(--cm-space-12)` · `var(--cm-space-14)` · `var(--cm-space-8)` |
| Kart/panel yarıçapı | `var(--cm-radius-md)` |
| Glass blur + kenarlık | `var(--cm-glass-blur)` · `var(--cm-glass-border)` |

> **Token-First:** bu tabloya ham hex/px yazılmaz; `01_Abstracts` dışına sabit değer konmaz.

## 4. Touch Target

| Sınıf | Minimum | Not |
|-------|---------|-----|
| Tüm kontroller (SC 2.5.8) | **≥ 24×24 px** | slider tutamağı, rozet, tab, mute |
| Birincil / acil mute | 48 px (acil mute 56 px) | `var(--cm-space-12)` / `var(--cm-space-14)` |
| Yakınlık kuralı | ≥ 8 px boşluk | yanlış basma önleme (`var(--cm-space-2)`) |
| Klavye | Ok tuşları ↔ (tab/empedans), ↑↓ (slider, 1W adım), Home/End | grid `roving tabindex`; mute `Enter/Space` |

## 5. WCAG Uyumu

| # | Kontrol | Kriter | Durum |
|---|---------|--------|-------|
| 1 | Metin kontrastı | ≥ 4.5:1 normal, ≥ 3:1 büyük — ölçüm kodda | **GAP** (PNG+kod ölçümü bekliyor → `04-accessibility-gaps`) |
| 2 | Odak görünür | **3px** outline, kontrast ≥ 3:1, `:focus-visible` | **PASS** (şartname) |
| 3 | Dokunma hedefi | ≥ 24×24 px | **PASS** (şartname; kodda doğrulanacak) |
| 4 | Okuma sırası / DOM sırası | şerit → kontrol → grid → audit | **PASS** |
| 5 | Durum yalnız renkle anlatılmaz + watt duyurusu | ikon+metin · `aria-live="polite"` (watt) · `aria-pressed` (mute) · `role="slider"` + `aria-valuenow/valuemin/valuemax` | **PASS** |

## 6. Glassmorphism Stili

| Öğe | Değer |
|-----|-------|
| `backdrop-filter` | `blur(var(--cm-glass-blur))` |
| Arka plan | `var(--cm-bg-surface)` (fallback: solid aynı token) |
| Kenarlık | `1px solid var(--cm-glass-border)` |
| Gölge | `var(--cm-shadow-sm)` |
| Kontrast etkisi | blur üstü metin ≥ 4.5:1 — değilse opaklık artırılır (§5 madde 1 GAP kapanana kadar glass opak kalır) |

## 7. PNG Referansı

- **Durum: ⚠️ PNG BEKLİYOR** — `.ai/.png/` altında pro.coremusic.net yüzeyine ait görsel YOK.
- **Mockup gate (AGENTS.md §13):** bu spec'ten **kod ÜRETİLEMEZ** — `.ai/ui-design/` altına ekran PNG'si eklenmeden frontend görevi DUR.
- **Mockup indeksi:** [[../ui-design/01-mockup-index]] (pro yüzeyi kapsam dışı → PNG eklenince indekse satır açılır).
- **Kullanım sırası:** PNG > ASCII art > Inventory > Tokens > Reference (AGENTS.md §7.2).

## 8. Responsive Davranış

| Davranış | Kural | Kaynak |
|----------|-------|--------|
| Kırılma | 9'luk grid → dar ekranda 3→2→1 sütun; kontrol şeridi üstte sarar | [[../ui-design/05-responsive-architecture]] §7.4 + §12 |
| Tier sıçraması | `T01 → T03 → T08 → T17 → T25 → T29 → T31` | [[../ui-design/00-device-matrix]] |
| Görsel ölçek | piksel ölçüler `rem`/token'a çevrilir; ham px yalnız §1 ASCII'de | Token-First |
| Fallback | pro tier spec'i yoksa T17 spec'i + §12 fallback | [[../ui-design/05-responsive-architecture]] §12 |
| Portre/Dikey | `N/A (landscape-only)` — pro panel yatay | `reference/10-device-specific-guidelines` |

## 9. State Durumları (Component State Matrix)

| State | Tetikleyici | Görsel | ARIA |
|-------|-------------|--------|------|
| DEFAULT | ilk render (otomatik mod, tavan kilitli) | `var(--cm-bg-surface)` + `var(--cm-text-secondary)` | — |
| HOVER | `@media (hover:hover)` | `var(--cm-hover-bg)` + `var(--cm-primary)` ikon | `aria-describedby` |
| ACTIVE | slider sürükleniyor / mute basılı | `var(--cm-active-bg)` + `var(--cm-error)` (mute) | `aria-pressed="true"` |
| FOCUSED | klavye | `outline: 3px solid var(--cm-focus-ring)` | `:focus-visible` |
| DISABLED | otomatik modda slider (tavan kilitli) | `var(--cm-text-disabled)` + `cursor: not-allowed` | `aria-disabled="true"` |
| ERROR | empedans profili uyuşmuyor / API reddi | `var(--cm-error)` kenarlık + `var(--cm-error-bg)` | `role="alert"` + `--error` toast |
| LOADING | cihaz/telemetri bekleniyor | `var(--cm-bg-surface)` skeleton şerit (C13) | `aria-busy="true"` |
| EMPTY | audit log boş | `var(--cm-text-tertiary)` "kayıt yok" metni | `aria-live="polite"` |
| SELECTED | seçili kanal/empedans/profil | `var(--cm-selected-bg)` + `var(--cm-primary)` kenarlık | `aria-current="true"` / `aria-selected="true"` |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-25
**Mode:** Red Team · Human Mode · Truth Mode