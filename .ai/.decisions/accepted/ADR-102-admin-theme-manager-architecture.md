---
title: "CoreMusic — ADR-102: Admin Theme Manager Architecture & Pembe Işıl-Peri Token Adaptation"
type: "architecture-decision"
category: "frontend"
date: "2026-10-21"
version: "1.0.0"
status: "accepted"
authority: "SSOT — Admin Panel ve Kontrol Merkezi Dinamik Tema Mimarisi, Home Domain Işıl-Peri (#FF00C8) Palet Hizalaması ve Gelecek ThemeManager Entegrasyon Sözleşmesi"
references:
  - "[[ADR-044-dynamic-user-theme-engine]] (Dynamic User Theme Engine SSOT)"
  - "[[ADR-001-vanilla-js-itcss]] (ITCSS 9 Katman & Vanilla Token Standardı)"
  - "[[ADR-018-footer-player-vaporwave]] (Görsel Dil & Hareket Azaltma)"
  - ".ai/ui-design/tokens/tokens-1024.json (Figma L2247-2249, L2656-2677 #FF00C8 SSOT)"
  - "assets.coremusic.net/Css/01_Abstracts/a-color-mode-tokens.css (html[data-mode=\"ışıl-peri\"])"
governance: "Red Team · Human Mode · Truth Mode"
---

# CoreMusic — ADR-102: Admin Theme Manager Architecture & Pembe Işıl-Peri Token Adaptation

> **Durum:** ✅ **ACCEPTED** · **Tarih:** 2026-10-21 · **Kapsam:** Admin Panel & Dashboard Shell
> **Seri:** `.ai/.decisions/accepted/` · **Dosya:** `ADR-102-admin-theme-manager-architecture.md`

---

## 1. Bağlam (Context) & Sorun Tanımı

CoreMusic ekosisteminde `home.coremusic.net` üzerinde tanımlanan birincil görsel kimlik, Figma `Romantic_Background_03` ve `tokens-1024.json` (L2247-2249, L2656-2677) kaynaklı **"Pembe Işıl-Peri"** (Luminous Fairy / Fairy Princess) paletidir. Bu palet:
- **Ana Renk (Primary Brand):** `#FF00C8`
- **İkincil Pembe / Macenta:** `#ff38e3`, `#ff2a85`
- **Işıma / Glow:** `rgba(255, 0, 200, 0.55)` ve `rgba(255, 0, 200, 0.35)`
- **Derin Taban:** `#16061c` - `#220828` (Romantik derin mor-pembe cam katmanları)

Admin panelinde (`admin.coremusic.net`) daha önce geçici olarak kullanılan soğuk mavi/indigo cam tonları `home.coremusic.net` ile marka ve görsel uyumsuzluk yaratıyordu. Ayrıca kullanıcı, gelecekte tüm platform genelinde dinamik tema değişimini yönetecek olan **Theme Manager** modülünün devreye alınacağını ve bu nedenle admin bileşenlerinin **sabit hex renklerle değil**, Theme Manager'a hazır dinamik bir token soyutlamasıyla mimarilendirilmesini talep etmiştir.

---

## 2. Mimari Karar (Decision)

### 2.1 İki Katmanlı Tema Token Mimarisi (`--adm-theme-*`)
Tüm admin bileşenleri (Dashboard kartları, KPI kutuları, Üst Header Navbar, Sol Sidebar, Tablolar, Grafikler ve Footer) doğrudan hex/rgba renk kodları kullanmak yerine **`--adm-theme-*`** semantik token katmanına bağlanacaktır:

| Semantik Token | Işıl-Peri Değeri | Açıklama |
|---|---|---|
| `--adm-theme-primary` | `#FF00C8` | Figma ışıl-peri ANA RENK (Figma L2656) |
| `--adm-theme-primary-hover` | `#ff38e3` | Buton/link hover durum rengi |
| `--adm-theme-primary-glow` | `rgba(255, 0, 200, 0.55)` | 1. derece neon ışıma efekti |
| `--adm-theme-primary-glow-soft` | `rgba(255, 0, 200, 0.35)` | Arka plan yumuşak halo ışıması |
| `--adm-theme-accent` | `#ff2a85` | Aktif kenarlık ve neon çizgi vurgusu |
| `--adm-theme-card-bg` | `rgba(30, 10, 36, 0.54)` | Ethereal pembe-mor derin cam kart zemini |
| `--adm-theme-card-bg-hover` | `rgba(42, 14, 50, 0.65)` | Kart üzerine gelindiğinde yükseltilmiş zemin |
| `--adm-theme-card-border` | `rgba(255, 130, 225, 0.22)` | Luminous pembe cam kenarlık |
| `--adm-theme-card-border-hover` | `rgba(255, 170, 240, 0.38)` | Kart hover kenarlık parlaması |
| `--adm-theme-card-inner` | `rgba(255, 210, 245, 0.22)` | Üst kenar iç ışık yansıması (inset reflection) |
| `--adm-theme-card-shadow` | `0 12px 32px rgba(22, 4, 28, 0.40)` | Kart derinlik gölgesi ve ortam aydınlatması |
| `--adm-theme-active-card-bg` | `linear-gradient(...)` | Menü aktif öğesi zengin pembe gradyan camı |
| `--adm-theme-active-card-border`| `rgba(255, 130, 235, 0.45)` | Menü aktif öğesi parlak cam kenarlık |
| `--adm-theme-navbar-bg` | `rgba(28, 8, 34, 0.52)` | Sağ üst kompakt cam kapsül zemini |
| `--adm-theme-navbar-border` | `rgba(255, 130, 225, 0.22)` | Sağ üst kapsül pembe kenarlık |
| `--adm-theme-sidebar-bg` | `linear-gradient(...)` | Sol panel şeffaf gradyan cam tabanı |
| `--adm-theme-btn-gradient` | `linear-gradient(...)` | Birincil CTA buton pembe ışıl gradyanı |

### 2.2 Theme Manager Entegrasyon Sözleşmesi
Gelecekte devreye girecek `ThemeManager.js` ve `ThemeManager.php` motoru için CSS seçicileri şu sırayla dinler:
1. `:root` → Varsayılan **Işıl-Peri** tema değerleri.
2. `html[data-mode="ışıl-peri"]` / `html[data-gender="female"]` → Işıl-Peri teması tam eşleşme.
3. `html[data-theme="dark"]` / `html[data-mode="dark"]` → Koyu gece teması token ezmesi.
4. `html[data-theme="cyberpunk"]` / `html[data-theme="ocean"]` → Ek temalar eklendiğinde tek bir CSS blok override'ı ile tüm admin anında renk değiştirir.

### 2.3 Sıfır Hardcoded Renk İlkesi (Zero Hardcoded Color Policy)
`p-admin-dashboard.css` ve `_admin-shell.css` içinde hiçbir kart, border, buton veya başlık doğrudan sabit hex kodu taşımaz; tüm stil tanımları `var(--adm-theme-*)` zinciri üzerinden türetilir.

---

## 3. Doğrulama ve Güvenlik

- **WCAG 2.2 AA Kontrastı:** `#FFFFFF` metin, `rgba(30, 10, 36, 0.54)` cam zemin üzerinde `> 9.2:1` kontrast oranı sağlar (minimum 4.5:1 eşiğini fazlasıyla geçer).
- **CSS-Only Performansı:** Tema değişimi JS döngüsü gerektirmez, CSS özel değişkenlerinin doğal tarayıcı yeniden boyama (repaint) motorunu kullanır.
- **PHPUnit Test Uyumu:** Mevcut `AdminMarkupContractTest`, `ThemeManagerModeTest` ve `ThemeManagerTest` sınıfları kırılmaz.
