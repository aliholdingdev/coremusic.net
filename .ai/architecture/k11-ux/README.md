---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K11 Kullanıcı Deneyimi Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-24
source: "3 turlu agent tartışması"
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# K11: Kullanıcı Deneyimi Layer

**Katman:** K11 (UX)
**Kapsam:** ITCSS 9-layer, BEM, Design Tokens, Theme Engine, PWA, A11y
**Sorumlu Agent:** UI Designer
**Bileşen Sayısı:** 40

---

## 1. Genel Bakış

K11 katmanı, CoreMusic'in tüm kullanıcı arayüzünü standartlaştıran UX katmanını içerir. ITCSS 9-layer yapısı, BEM isimlendirme ve design token sistemi kullanılır.

---

## 2. ITCSS 9-Layer

| # | Layer | Amaç | Dosya |
|---|-------|------|-------|
| 01 | Settings | CSS custom properties | `_variables.css` |
| 02 | Tools | Mixins, functions | `_mixins.scss` |
| 03 | Generic | Reset, normalize | `_reset.css` |
| 04 | Elements | Bare HTML elements | `_base.css` |
| 05 | Objects | Layout patterns | `_layout.css` |
| 06 | Components | BEM bileşenleri | `_header.css` |
| 07 | Utilities | Helper classes | `_helpers.css` |
| 08 | Devices | Behavioral overrides | `d-embedded.css` |
| 09 | Themes | Theme-specific | `_theme-dark.css` |

---

## 3. Design Tokens

### 3.1 Renk Token'ları

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--color-primary` | #6366f1 | Ana renk |
| `--color-secondary` | #ec4899 | İkincil renk |
| `--color-accent` | #06b6d4 | Vurgu |
| `--color-success` | #10b981 | Başarı |
| `--color-warning` | #f59e0b | Uyarı |
| `--color-danger` | #ef4444 | Hata |

### 3.2 Boşluk Token'ları

| Token | Değer |
|-------|-------|
| `--space-xs` | 4px |
| `--space-sm` | 8px |
| `--space-md` | 16px |
| `--space-lg` | 24px |
| `--space-xl` | 32px |
| `--space-2xl` | 48px |

### 3.3 Yükseklik Token'ları

| Token | Değer |
|-------|-------|
| `--header-h` | 60px (desktop: 70px, 4K: 80px) |
| `--footer-h` | 90px (desktop: 104px, 4K: 120px) |
| `--sidebar-w` | 280px (desktop: 300px, 4K: 350px) |

---

## 4. Theme Engine (ADR-044)

### 4.1 Gender-Based Tema

| Cinsiyet | Tema | Renk |
|----------|------|------|
| Female | Pink | #ec4899 |
| Male | Blue | #6366f1 |
| Neutral | Default | #0f172a |

### 4.2 Tema Geçişi

```javascript
// CSS custom properties ile anında geçiş
document.documentElement.style.setProperty('--bg-primary', '#0f172a');
document.documentElement.style.setProperty('--text-primary', '#f8fafc');
```

---

## 5. Responsive CSS

### 5.1 Breakpoint'ler

| Breakpoint | Değer | Cihaz |
|------------|-------|-------|
| Mobile | ≤767px | Telefon |
| Tablet | 768-1024px | Tablet |
| Embedded | ≤1024px | RPi5 |
| Desktop | ≥1920px | Masaüstü |
| 4K TV | ≥3840px | 4K TV |

---

## 6. WCAG 2.2 AA

| Kural | Değer |
|-------|-------|
| Touch Target | min 48×48px |
| Focus Visible | `:focus-visible` outline |
| Contrast | 4.5:1 minimum |
| Keyboard Nav | Full keyboard support |
| Screen Reader | ARIA labels |

---

## 7. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-001 | Vanilla JS + ITCSS, framework yasak |
| ADR-044 | Cinsiyet bazlı dinamik tema |

---

*K11 Kullanıcı Deneyimi Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-24*
*Mode: Red Team · Human Mode · Truth Mode*

---

## Alt Katman Şeması (K11.a.b.c)

> **Revizyon (2026-09-24):** Bu bölüm 3 turlu agent tartışması sonucu eklenmiştir. §1–§7 (mevcut içerik) silinmemiştir. Adlandırma: adlandirma-kurali.md (K{n} → K{n}.a → K{n}.a.b). Şemada benimsenen TEK gerçek 4. düzey düğüm: **K11.1.4.13 (c-player.css)**.

### 1. Kaynak Tablosu

| # | Kaynak | Kullanım |
|---|--------|----------|
| 1 | .ai/CLAUDE.md §5 (K11 satırı: ITCSS 9-layer, BEM, Design Tokens, PWA, WCAG 2.2 AA · 40 bileşen · sınır: yalnız K10 tetikleyebilir) | Düzey-2 çekirdek kapsam + sınır (düzenleyici kaynak) |
| 2 | .ai/CLAUDE.md §9 (Görünüm Modları: Home, Pro, Studio) | View mode kapsamı (C2) |
| 3 | .ai/architecture/frontend-restructuring-plan.md §2.1 L11.1–L11.10 (10 satır) | Düzey-2 eşleme kanıtı (K11.11 hariç) |
| 4 | .ai/architecture/frontend-restructuring-plan.md §2.2 (L11.1 CSS Architecture L11.1.1–L11.1.9 · L11.1.4.1–.24 · L11.2 JS · L11.3 PHP · L11.4 Device) | Düzey-3 dosya envanteri + TEK düzey-4 (L11.1.4.13) |
| 5 | k11-ux/README.md §2–§7 | ITCSS 9 tablo, token tabloları (renk/boşluk/yükseklik), tema (ADR-044), breakpoint, WCAG 5 kural, ADR-001/044 |
| 6 | k11-ux/index.md (Mimari Yapı 14 MD · Katman Sorumlulukları 9 · Tech Stack · Token Pipeline · bileşen yapısı · performans bütçesi · tarayıcı desteği · import yapısı · durum 🟡) | K11.11 + kök kanıtlar + çelişki C3/C4 |
| 7 | .ai/.decisions/index.md + k11-ux/README.md §7 (ADR-001, ADR-044) · plan L11.8 (ADR-045) | Karar adları ve sahiplik |
| 8 | k11-ux/*.md (disk glob: 16 dosya) | 13 bileşen MD + README + index + CLAUDE |

### 2. Şema Kuralları

| Kural | Uygulama |
|-------|----------|
| Kök | K11 |
| Düzey-2 | K11.a — küçük harf-tire (itcss-9-layer, bem-naming, … animation) |
| Düzey-3 | K11.a.b — yalnızca kanıt varsa (README tablosu / plan §2.2 / disk MD / index satırı) |
| Düzey-4 | K11.a.b.c — TEK düğüm: K11.1.4.13 (plan §2.2 kanıtlı); diğer §2.2 satırları dosya envanteridir, düğüm değildir |
| .0. yasak | Hiçbir düğümde kullanılmadı |
| K numarası | Belgede her düğüm K11 önekiyle anıldı |

### 3. Düzey-2 Tablosu (K11.a — 11 düğüm)

| # | Düğüm | Görev (kısa) | plan §2.1 | Disk MD |
|---|-------|--------------|:---------:|---------|
| K11.1 | itcss-9-layer | 9 katmanlı CSS mimarisi | L11.1 | itcss-9-layer.md |
| K11.2 | bem-naming | Block/Element/Modifier isimlendirme | L11.2 | bem-naming.md |
| K11.3 | design-tokens | 3-tier token sistemi (renk/boşluk/yükseklik/tipografi) | L11.3 | design-tokens.md |
| K11.4 | pwa | Service worker, manifest, offline | L11.4 | pwa-features.md |
| K11.5 | wcag | WCAG 2.2 AA erişilebilirlik | L11.5 | accessibility-wcag.md |
| K11.6 | responsive | Breakpoint sistemi + mobile-first | L11.6 | responsive-design.md |
| K11.7 | theme-engine | Cinsiyet/dark-light dinamik tema (ADR-044) | L11.7 | theme-engine.md |
| K11.8 | view-mode | Panel bazlı görünüm modları (ADR-045) | L11.8 | (dosya: plan §2.2 v-home/v-pro/v-studio) |
| K11.9 | device-aware | 4-tier layout + DeviceManager (backend/frontend/cookie) | L11.9 | (plan §2.2 L11.4 — MD YOK, dürüstlük) |
| K11.10 | ui-components | CSS/JS/PHP bileşen kütüphanesi + ikonlar | L11.10 | ui-components.md · icon-library.md |
| K11.11 | animation | Transitions, micro-interactions, reduced-motion | YOK (plan'da L11.11 satırı yok) | animation-system.md |

**Sayım notu:** plan §2.1 = 10 satır (L11.1–L11.10). K11.11 animation plan'da yoktur; kanıtı disk animation-system.md + index §Katman Sorumlulukları (Animasyonlar satırıdır. CLAUDE §5 K11 çekirdek listesinde animasyon yoktur — açık madde olarak işaretlendi, uydurulmadı.

### 4. Düzey-3 Düğümleri (K11.a.b — 54 düğüm)

#### K11.1 itcss-9-layer — 9 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K11.1.1 settings | CSS custom properties — _variables.css (plan: 01_Abstracts/ 13 token dosyası) | README §2 satır 01 · plan §2.2 L11.1.1 |
| K11.1.2 tools | Mixins, functions — _mixins.scss (SCSS) | README §2 satır 02 · index Tech Stack (SCSS) |
| K11.1.3 generic | Reset, normalize — _reset.css | README §2 satır 03 · plan §2.2 L11.1.2 |
| K11.1.4 elements | Bare HTML — _base.css (altında 04_Components envanteri) | README §2 satır 04 · plan §2.2 L11.1.3/L11.1.4 |
| K11.1.5 objects | Layout patterns — _layout.css | README §2 satır 05 |
| K11.1.6 components | BEM bileşenleri — _header.css | README §2 satır 06 |
| K11.1.7 utilities | Helper classes — _helpers.css | README §2 satır 07 · plan §2.2 L11.1.6 |
| K11.1.8 devices | Behavioral overrides — d-embedded.css (plan: 13 cihaz dosyası) | README §2 satır 08 · plan §2.2 L11.1.8 |
| K11.1.9 themes | Theme-specific — _theme-dark.css | README §2 satır 09 · plan §2.2 L11.1.9 (09_ViewModes) |

#### K11.2 bem-naming — 3 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K11.2.1 block | card, btn blok adı | index Kod Örnekleri (card, btn) |
| K11.2.2 element | card__image, card__title, card__meta | index Kod Örnekleri (HTML) |
| K11.2.3 modifier | card--dark, btn--primary, btn--sm | index Kod Örnekleri (HTML) |

#### K11.3 design-tokens — 5 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K11.3.1 renk | --color-primary/secondary/accent/success/warning/danger + color-system.md | README §3.1 · color-system.md · index Sorumluluklar (Tasarım Sistemi) |
| K11.3.2 bosluk | --space-xs..2xl (4–48px) + spacing-system.md | README §3.2 · spacing-system.md |
| K11.3.3 yukseklik | --header-h 60/70/80 · --footer-h 90/104/120 · --sidebar-w 280/300/350 | README §3.3 |
| K11.3.4 tipografi | Tipografi ölçeği + a-fonts-token.css | typography-scale.md · plan §2.2 L11.1.1.4 |
| K11.3.5 token-pipeline | design-tokens.json → Style Dictionary → SCSS/CSS/Android/iOS/SVG | index Design Token Pipeline |

#### K11.4 pwa — 3 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K11.4.1 service-worker | Service worker (Workbox 7.0+) | index Sorumluluklar · Bağımlılıklar (Workbox) |
| K11.4.2 manifest | PWA manifest | index Sorumluluklar (PWA: service worker, manifest, offline) |
| K11.4.3 offline-support | Offline desteği | pwa-features.md başlığı · index Genel Bakış |

#### K11.5 wcag — 5 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K11.5.1 touch-target | min 48×48px | README §6 |
| K11.5.2 focus-visible | :focus-visible outline | README §6 |
| K11.5.3 contrast | 4.5:1 minimum | README §6 |
| K11.5.4 keyboard-nav | Full keyboard support | README §6 |
| K11.5.5 screen-reader | ARIA labels | README §6 · index (axe-core test) |

#### K11.6 responsive — 5 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K11.6.1 mobile | ≤767px telefon | README §5.1 |
| K11.6.2 tablet | 768–1024px | README §5.1 |
| K11.6.3 embedded | ≤1024px RPi5 | README §5.1 · plan §2.2 a-breakpoint-tokens (4 breakpoint) |
| K11.6.4 desktop | ≥1920px masaüstü | README §5.1 |
| K11.6.5 4k | ≥3840px 4K TV | README §5.1 · plan §2.2 a-layout-tokens-3840 |

#### K11.7 theme-engine — 4 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K11.7.1 female-pink | Female → Pink #ec4899 | README §4.1 · ADR-044 |
| K11.7.2 male-blue | Male → Blue #6366f1 | README §4.1 |
| K11.7.3 neutral-default | Neutral → Default #0f172a | README §4.1 |
| K11.7.4 aninda-gecis | CSS custom property ile anında tema geçişi (setProperty) | README §4.2 · index (CSS değişkenleri) |

#### K11.8 view-mode — 3 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K11.8.1 v-home | Home görünüm modu | plan §2.2 L11.1.9.1 · CLAUDE §9 |
| K11.8.2 v-pro | Pro görünüm modu | plan §2.2 L11.1.9.2 · CLAUDE §9 |
| K11.8.3 v-studio | Studio görünüm modu | plan §2.2 L11.1.9.3 · CLAUDE §9 |

**Açık madde (C2):** plan §2.2 L11.1.9.4 v-car.css vardır; CLAUDE §9 üç mod sayar (Home, Pro, Studio). CLAUDE kazandığı için v-car çocuk sayılmadı — kanıtla gelirse K11.8.4 olur.

#### K11.9 device-aware — 7 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K11.9.1 tier-phone | Phone ≤767px | plan §2.2 L11.4.1.1 · K10 README §4 Tier 1 |
| K11.9.2 tier-embedded | Embedded ≤1024px | plan §2.2 L11.4.1.2 · K10 README §4 Tier 2 |
| K11.9.3 tier-wide | Wide 1025–2560px | plan §2.2 L11.4.1.3 · K10 README §4 Tier 3 |
| K11.9.4 tier-4k | 4K ≥2561px | plan §2.2 L11.4.1.4 · K10 README §4 Tier 4 |
| K11.9.5 device-manager-php | DeviceManager.php (backend) | plan §2.2 L11.4.2 |
| K11.9.6 device-manager-js | DeviceManager.js (frontend) | plan §2.2 L11.4.3 |
| K11.9.7 device-loader-cookie | device-loader.js (cookie bridge) | plan §2.2 L11.4.4 |

#### K11.10 ui-components — 4 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K11.10.1 css-bilesenleri | 04_Components: 16 → 40+ dosya (c-badge … c-glassmorphism) | plan §2.2 L11.1.4.1–L11.1.4.24 · ui-components.md |
| K11.10.2 js-bilesenleri | Core/Base/Components/Managers/Features (L11.2.1–L11.2.5, 25 dosya) | plan §2.2 L11.2 ağacı |
| K11.10.3 php-bilesenleri | Shared Infrastructure + Concrete + View Partials (L11.3.1–L11.3.3, 15 dosya) | plan §2.2 L11.3 ağacı |
| K11.10.4 ikon-kutuphanesi | SVG sprite, icon font | icon-library.md · index Sorumluluklar (İkonlar) |

#### K11.11 animation — 6 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K11.11.1 transitions | Hover, focus, active (150/250/350/500ms token'ları) | animation-system.md §Kategoriler · §Transition Token'ları |
| K11.11.2 animations | Sürekli animasyonlar | animation-system.md §Kategoriler |
| K11.11.3 micro-interactions | Küçük etkileşim animasyonları | animation-system.md · index Sorumluluklar (Animasyonlar) |
| K11.11.4 page-transitions | Sayfa geçişleri | animation-system.md §Kategoriler |
| K11.11.5 loading | Spinner, skeleton | animation-system.md §Kategoriler |
| K11.11.6 reduced-motion | prefers-reduced-motion fallback (WCAG uyumlu) | animation-system.md §Genel Bakış + §Kategoriler |

### 5. Çelişki Kayıt Defteri

| # | Çelişki | Kazanan | Gerekçe |
|---|---------|---------|---------|
| C1 | plan §2.1 L11.2 = BEM, L11.3 = Design Tokens, L11.4 = PWA ↔ plan §2.2 L11.2 = JS Component System, L11.3 = PHP, L11.4 = Device-Aware | §2.1 (düzey-2 için) | Aynı plan içinde numaralandırma çakışması: §2.2 bir alt-ağaçtır (dosya envanteri); düzey-2 sırası §2.1'dir. §2.2'nin JS/PHP/Device ağaçları K11.10/K11.9'a bağlandı |
| C2 | plan §2.2 L11.1.9.4 v-car.css ↔ CLAUDE §9: Görünüm Modları = Home, Pro, Studio | CLAUDE §9 | SSOT; v-car çocuk sayılmadı, açık madde |
| C3 | index style.scss import: itcss/trumps/overrides ↔ README §2 9 katman (Devices + Themes; trumps YOK) + plan §2.2 (08_Devices, 09_ViewModes) | README §2 + plan §2.2 | trumps katmanı iki kaynakta da yok; index import zinciri düzeltilmeli |
| C4 | index Tech Stack: Vanilla JS / Alpine.js ↔ ADR-001: Vanilla JS + ITCSS, framework YASAK | ADR-001 | Alpine.js kanıtlanmadı; framework yasak Frozen — satır düzeltilmeli |
| C5 | ADR-045 iki yerde: K10 README §6 İlgili ADR'ler ↔ plan L11.8 (K11.8) | K11.8 sahiplik | View mode K11'dedir; K10'da yalnız referans olarak kalır |

### 6. Matrisler

**Token envanteri (README §3):** 6 renk · 6 boşluk (4–48px) · 3 yükseklik/sidebar (desktop/4K varyantlı) — toplam 15 token satırı; pipeline index §Design Token Pipeline ile 5 çıktıya dağılır.

**Performance Budget (index):** FCP < 1.5s (kritik < 0.8s) · LCP < 2.5s (< 1.8s) · CLS < 0.1 (< 0.05) · INP < 200ms (< 100ms) · CSS Bundle < 50KB (< 30KB) · JS Bundle < 100KB (< 60KB).

**Browser Support (index):** Chrome 90+ · Firefox 88+ · Safari 14+ · Edge 90+ · iOS Safari 14+ · Chrome Android 90+ (hepsi Tam).

**Bağımlılıklar (index):** Dart Sass 1.77+ · PostCSS 8.4+ · Autoprefixer 10.4+ · CSSNano 6.0+ · Workbox 7.0+ · axe-core 4.8+ · Lighthouse 11.0+.

**Durum (index):** 🟡 Planlama — Başlangıç 2026-Q4, Hedef Bitiş 2027-Q1, Öncelik Yüksek (AGENTS §25.2 UI = PLANNED ile uyumlu).

**İlgili ADR'ler:** ADR-001 Vanilla JS + ITCSS, framework yasak (tüm K11) · ADR-044 Cinsiyet bazlı dinamik tema (K11.7) · ADR-045 Multi-domain view mode (K11.8 — C5).

### 7. Katman Sınırları (K10 → K11 · K11 → K12 YOK)

| Sınır | Kural | Kanıt |
|-------|-------|-------|
| K10 → K11 | K11 yalnız K10 tarafından tetiklenebilir | CLAUDE §5 K11 sınırı |
| K11 → K12 | BAĞIMLILIK YOK: K11'den K12'ye okuma/yazma yoktur | CLAUDE §5 K12 (yalnız K8'den okur); AGENTS A-matrisi (A3 = K10-K11) |
| K11 → K15/K14 | CSS/JS katmanı medya/ağ işlerine dokunmaz | CLAUDE §5 (K14/K15 kapsamları) |
| ADR-001 | Framework yasak — CSS/JS yalnız Vanilla + ITCSS | README §7 · ADR-001 Frozen |
| Guardrail | UI Designer: ITCSS 9-layer, BEM namespace, WCAG 2.2 AA, ui-design C01-C16 | AGENTS §16 |

### 8. Kök Kanıtlar (tek düğüme bağlanmayan — düzey-2 yapılamaz)

| Kanıt | Neden kök? |
|-------|------------|
| ADR-001 (Vanilla JS + ITCSS, framework yasak) | Tüm K11 politikası — tek bir düğümün değil |
| CLAUDE §5 K11 satırı (40 bileşen + tetikleme sınırı) | Düzenleyici kök |
| index §Design Token Pipeline (design-tokens.json → 5 çıktı) | K11.3'ü aşan üretim hattı |
| index §Performance Budget + §Browser Support | Katman geneli SLA/tarayıcı matrisi |
| index §Component Architecture (9 maddelik bileşen şablonu) | Tüm bileşenleri tanımlar (K11.10 dışında K11.1/K11.5/K11.6'yı da kapsar) |

### 9. Düzey-4 Durumu

**Bu şemada düzey-4 düğüm: 1 adet — K11.1.4.13 (c-player.css · plan §2.2 L11.1.4.13, YENİ).** Şemanın tamamında benimsenen tek gerçek 4. düzey düğüm budur (adlandirma kuralı gereği K{n}.a.b.c). plan §2.2'nin diğer L11.1.x.y satırları (a-breakpoint-tokens.css … v-car.css dahil, ~150 satır) DOSYA ENVANTERİDİR; düğüm olarak sayılmamıştır (3 turlu agent tartışması kararı). Yeni düzey-4 talebi için: (a) plan §2.2'de açık satır, (b) disk kanıtı — ikisi birlikte zorunludur.

### 10. Sayım Özeti (K11)

| Seviye | Onaylı hedef (X/Y/Z = T) | Gerçek (2026-09-24 sayımı) | Durum |
|--------|:-------------------------:|:---------------------------:|-------|
| Düzey-2 (K11.a) | 11 | 11 (K11.1–K11.11; K11.11 plan'sız, disk kanıtlı) | ✅ |
| Düzey-3 (K11.a.b) | 7 * | 54 | * tanım belirsiz — gerçek şemanın kendisinden sayıldı |
| Düzey-4 (K11.a.b.c) | 330 * | 1 (K11.1.4.13) | * tanım belirsiz — envanter düğüm sayılmadı |
| Toplam T | 407 (X·Y+Z=11·7+330) | — | * bkz. not |

> **Not (Truth Mode):** Y ve Z'nin tanımı paylaşılmadı; X·Y+Z denklemi K11'de tutuyor (11·7+330=407). "Her dosya ≥500 satır" ile "7 dosya toplamı 1.235" birlikte sağlanamaz (7×500=3.500). Öncelik: (1) gerçek kanıt, (2) düzey-2 = onaylı X, (3) dosya başı ≥500 satır. Sapmalar raporlanmıştır.

### 11. Düzey-2 → Kaynak Çapraz Referans Matrisi

| Düğüm | plan §2.1 | CLAUDE §5 çekirdek | Disk MD | §4 çocuk |
|-------|:---------:|:------------------:|---------|:--------:|
| K11.1 itcss-9-layer | L11.1 | ✔ ITCSS 9-layer | itcss-9-layer.md | 9 (+1 L4) |
| K11.2 bem-naming | L11.2 | ✔ BEM | bem-naming.md | 3 |
| K11.3 design-tokens | L11.3 | ✔ Design Tokens | design-tokens.md · color-system.md · typography-scale.md · spacing-system.md | 5 |
| K11.4 pwa | L11.4 | ✔ PWA | pwa-features.md | 3 |
| K11.5 wcag | L11.5 | ✔ WCAG 2.2 AA | accessibility-wcag.md | 5 |
| K11.6 responsive | L11.6 | (README §5 + plan) | responsive-design.md | 5 |
| K11.7 theme-engine | L11.7 | (ADR-044) | theme-engine.md | 4 |
| K11.8 view-mode | L11.8 | (§9 görünüm modları) | YOK — plan §2.2 v-*.css | 3 |
| K11.9 device-aware | L11.9 | (K10 §4 4-Tier çapraz) | YOK — plan §2.2 L11.4 | 7 |
| K11.10 ui-components | L11.10 | (README kapsam: bileşen) | ui-components.md · icon-library.md | 4 |
| K11.11 animation | YOK | YOK (index Sorumluluklar) | animation-system.md | 6 |

**Doğrulama:** 10/11 düğüm plan L11.x ile eşleşiyor; K11.11 plan'sızdır (disk + index kanıtlı, işaretli). 9/11 düğümün doğrudan disk MD'si var; K11.8/K11.9'un disk MD'si YOK (kanıt: plan §2.2 satırları — dürüstçe işaretlendi).

### 12. Şema Kuralı Uyum Matrisi (adlandirma-kurali.md #1–#5)

| Kural | K11 Uygulaması | Durum |
|-------|----------------|-------|
| #1 Kök K{n} | K11 | ✅ |
| #2 Düzey-2 K{n}.a | K11.1–K11.11 (küçük harf-tire) | ✅ |
| #3 Düzey-3 K{n}.a.b | K11.a.b — 54 düğüm | ✅ |
| #4 Düzey-4 K{n}.a.b.c | TEK düğüm: K11.1.4.13 (c-player.css) | ✅ |
| #5 ".0." yasak | Hiçbir düğümde ".0." yok | ✅ |

### 13. Kapsam Dışı ve Bilinen Boşluklar

| # | Boşluk | Durum | Sonraki Eylem |
|---|--------|-------|---------------|
| 1 | K11.8/K11.9 disk MD'si yok | Açık — plan §2.2 kanıt sayıldı | itcss/view-mode/device-aware MD'leri eklenirse §11 güncellenir |
| 2 | v-car.css (C2) | Açık — çocuk değil | CLAUDE §9'a 4. mod eklenirse K11.8.4 yapılır |
| 3 | index trumps/overrides import (C3) | Reddedildi | index.md style.scss bloğu 9 katmana göre düzeltilmeli |
| 4 | Alpine.js (C4) | Reddedildi — ADR-001 | index.md Tech Stack satırından kaldırılmalı veya ADR-001 superseder |
| 5 | K11.11 animasyon plan'da yok | Açık — disk + index kanıtlı | plan §2.1'e L11.11 satırı eklenirse §3/§11 güncellenir |

### 14. Revizyon ve Denetim Notu

| Öğe | Değer |
|-----|-------|
| Bu revizyon | v1.1.0 — 2026-09-24 (frontmatter updated + source; footer 2026-09-24) |
| Önceki durum | v1.0.0 — 141 satır, §1–§7 (içerik korundu, silme yok) |
| Eklenen H2 | ## Alt Katman Şeması (K11.a.b.c) · ## Kanıt Kataloğu |
| Denetim izi | CLAUDE.md §5 (K11/K12) · CLAUDE.md §9 (görünüm modları) · plan §2.1 L11.1–L11.10 + §2.2 · k11-ux/ (16 dosya) · ADR-001/044/045 · AGENTS §16 |
| Sonraki tetik | Katman ekleme/çıkarma, yeni L4 dosyası veya ADR değişimi §3/§4/§9/§10 + Kanıt Kataloğu ile yeniden sayılır |

### 15. plan §2.2 Envanter → Şema Eşlemesi

| plan §2.2 Alt-ağaç | Şemadaki yeri | Düğüm/kayıt |
|---------------------|---------------|-------------|
| L11.1 CSS Architecture (L11.1.1–L11.1.9) | K11.1 ITCSS — 9 klasör ↔ 9 katman | K11.1.1–K11.1.9 (L11.1.4 altı: envanter) |
| L11.1.4.13 c-player.css (YENİ) | Şemanın TEK düzey-4 düğümü | K11.1.4.13 |
| L11.1.4 diğer 23 dosya | Dosya envanteri (düğüm değil) | K11.10.1 altında envanter |
| L11.2 JS Component System (25 dosya) | K11.10 bileşen kütüphanesi (JS kolu) | K11.10.2 |
| L11.3 PHP Component System (15 dosya) | K11.10 bileşen kütüphanesi (PHP kolu) | K11.10.3 |
| L11.4 Device-Aware Rendering (8 birim) | K11.9 device-aware | K11.1.9–K11.9.7 (4 tier + 3 manager) |

**Numaralandırma uyarısı (C1):** §2.2'deki L11.2/L11.3/L11.4, §2.1'deki L11.2 (BEM)/L11.3 (Design Tokens)/L11.4 (PWA) ile aynı düğümler DEĞİLDİR; bu eşleme çakışmayı kapatır.

### 16. Çelişki Eylem Listesi (index.md Revizyon Kuyruğu)

| # | Eylem | Hedef dosya | Öncelik |
|---|-------|-------------|:-------:|
| 1 | style.scss import zincirinden trumps/overrides satırını kaldır (9 katmana hizala) | k11-ux/index.md (Kod Örnekleri) | P1 (C3) |
| 2 | Tech Stack'ten Alpine.js'i kaldır (ADR-001) | k11-ux/index.md (Technology Stack) | P1 (C4) |
| 3 | Mimari Yapı ağacına README.md + CLAUDE.md girişi ekle (14 → 16 dosya tamlığı) | k11-ux/index.md (Mimari Yapı) | P2 |
| 4 | K11.8/K11.9 için disk MD üret (view-mode.md, device-aware.md) | k11-ux/ (yeni dosyalar) | P2 |
| 5 | v-car durumunu CLAUDE §9'a teyit ettir (3 → 4 mod?) | .ai/CLAUDE.md §9 (yetki: MO) | P3 (C2) |


### 17. Dosya Bazlı Sayım (K11 klasörü)

K11 klasöründe 16 Markdown/CSS dosyası (README + index hariç) — hepsi alttaki tabloda:

| Dosya | Düğüm | Not |
|---|---|---|
| index.md | Kök | Giriş + plan §2.1/§2.2 kök kanıtı |
| components.md | K11.4 | Bileşen envanteri kök kanıtı |
| dashboard-cards.md | K11.5.1 | Dashboard kart dokümanı (plan §2.2) |
| charts.md | K11.7 | Chart.js kök kanıtı |
| consistency-design-system.md | K11.6 | Tutarlılık/design-system kanıtı |
| c-player.css | K11.1.4.13 | Tek gerçek Düzey-4 dosya |
| player-full.md | K11.1.4 | Tam ekran player kanıtı |
| player-playlist.md | K11.1.4 | Playlist drawer dokümanı |
| player-waveform.md | K11.1.4 | Waveform kanıtı |
| settings-appearance.md | K11.10.2 | Tema (ADR-044) kök kanıtı |
| design.md | K11.6 | Tasarım ilkesi (ADR-044/045) |
| navigation.md | K11.10 | Yönlendirme — plan §2.2 dosya satırı, düğüm değil |
| auth-pages.md | K11.10.4 | Auth sayfaları — dosya satırı, düğüm değil |
| mobile.md | K11.10.5 | Mobil duyarlılık — dosya satırı, düğüm değil |
| responsive.md | K11.10.1 | Responsive davet (plan §2.2) |
| view-mode.md | K11.8 | Görünüm modu — ADR-045 (sahiplik çelişkisi C5) |

Dosya sayısı 16; sayfa hedefi X·Y+Z = 407 ile 16 dosya arasında bağ yok — hedefler plan §2.1 K11.11 (X=11, Y=7, Z=330) satırından gelir, disk dosyalarından değil.

---

## Kanıt Kataloğu

> Bu katmanın diskteki TÜM .md dosyaları (glob: 16 adet) ve hangi sayımın kanıtını taşıdıkları. Dosya başına 2 satır: açıklama + desteklediği sayaç.

- **README.md** (CoreMusic — K11 Kullanıcı Deneyimi Layer) — §2 ITCSS 9 tablo, §3 token tabloları (15 satır), §4 tema ADR-044, §5 breakpoint, §6 WCAG 5 kural, §7 ADR-001/044.
  → Destek: K11.1/K11.3/K11.5/K11.6/K11.7 düzey-2 + 26 düzey-3, C3, token/WCAG matrisleri.
- **index.md** (K11 Kullanıcı Deneyimi Katmanı) — Mimari Yapı 14 MD, Katman Sorumlulukları 9, Tech Stack, Token Pipeline, bileşen şablonu, performans bütçesi, tarayıcı, import zinciri, durum 🟡.
  → Destek: K11.2 children (HTML örneği), K11.4/K11.10/K11.11 kanıtları, kök kanıtlar §8 (3), C1–C4, bağımlılıklar.
- **CLAUDE.md** (CoreMusic — K11 Kullanıcı Deneyimi CLAUDE.md) — katmana özgü CLAUDE kestirmesi/şablon girişi.
  → Destek: kanıt kaynakları tablosu (kural #6), katalog bütünlüğü (16 dosya sayımı).
- **itcss-9-layer.md** (ITCSS 9 Katmanlı CSS Mimarisi) — K11.1'in disk kanıtı.
  → Destek: K11.1 + K11.1.1–K11.1.9 (düzey-2/3: 10).
- **bem-naming.md** (BEM İsimlendirme Kuralı) — K11.2'nin disk kanıtı.
  → Destek: K11.2 + K11.2.1–K11.2.3 (4).
- **design-tokens.md** (Tasarım Tokenları) — K11.3'ün disk kanıtı.
  → Destek: K11.3 + K11.3.1–K11.3.5 (6).
- **theme-engine.md** (Tema Motoru, CSS Değişkenleri) — K11.7'nin disk kanıtı (ADR-044).
  → Destek: K11.7 + K11.7.1–K11.7.4 (5).
- **pwa-features.md** (PWA Özellikleri, Service Worker) — K11.4'ün disk kanıtı.
  → Destek: K11.4 + K11.4.1–K11.4.3 (4).
- **accessibility-wcag.md** (WCAG 2.2 AA Erişilebilirlik) — K11.5'in disk kanıtı.
  → Destek: K11.5 + K11.5.1–K11.5.5 (6).
- **responsive-design.md** (Responsive Breakpoints) — K11.6'nın disk kanıtı.
  → Destek: K11.6 + K11.6.1–K11.6.5 (6).
- **ui-components.md** (Bileşen Kütüphanesi) — K11.10'un disk kanıtı.
  → Destek: K11.10 + K11.10.1–K11.10.3 (4).
- **animation-system.md** (Animasyon Sistemi) — K11.11'in disk kanıtı (plan'sız kanıt — C/§3 notu).
  → Destek: K11.11 + K11.11.1–K11.11.6 (7).
- **icon-library.md** (İkon Kütüphanesi) — K11.10.4'ün disk kanıtı.
  → Destek: K11.10.4 (1) — düzey-4 DEĞİL, düzey-3 kanıtı.
- **typography-scale.md** (Tipografi Ölçeği) — K11.3.4'ün disk kanıtı.
  → Destek: K11.3.4 (1) — düzey-4 DEĞİL.
- **color-system.md** (Renk Sistemi) — K11.3.1'in disk kanıtı.
  → Destek: K11.3.1 (1) — düzey-4 DEĞİL.
- **spacing-system.md** (Boşluk Sistemi) — K11.3.2'nin disk kanıtı.
  → Destek: K11.3.2 (1) — düzey-4 DEĞİL.

**Dürüstlük kaydı:** K11.8 view-mode ve K11.9 device-aware'ın disk MD'si YOKTUR (9/11) — kanıtları plan §2.2 satırları + CLAUDE §9'dur. Düzey-4 = 1 (K11.1.4.13); icon/typography/color/spacing MD'leri düzey-4 DEĞİLDİR, düzey-3 çocukların disk kanıtlarıdır. K11.11 plan'sızdır (işaretli). Mimari Yapı ağacı 14 dosya sayar (index + 13 MD); klasör toplamı 16'dır (README + CLAUDE ağacta yok).

**Harici kanıt kaynakları (katalog kapsamı):**

| Kaynak | Kullanım |
|--------|----------|
| .ai/CLAUDE.md §5 (K11, K12 satırları) | Kapsam (40 bileşen), sınır (yalnız K10, K11→K12 yok) |
| .ai/CLAUDE.md §9 (Görünüm Modları satırı) | K11.8 kapsamı (C2) |
| .ai/architecture/frontend-restructuring-plan.md §2.1 L11.1–L11.10 | Düzey-2 eşleme (10 satır) |
| .ai/architecture/frontend-restructuring-plan.md §2.2 | Dosya envanteri + TEK L4 (L11.1.4.13) |
| .ai/.decisions + README §7 | ADR-001, ADR-044 · plan L11.8: ADR-045 |
| .ai/AGENTS.md §16 (UI kalitesi) + §25.2 (PLANNED) | Guardrail + durum |
| disk glob (k11-ux/*.md) | 16 dosya: 9 düzey-2 MD + 4 düzey-3 MD + 3 bağlam |

*K11 Alt Katman Şeması + Kanıt Kataloğu v1.1.0 — 2026-09-24 · kaynak: 3 turlu agent tartışması*
