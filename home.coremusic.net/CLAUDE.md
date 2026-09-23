---
title: "CoreMusic — home.coremusic.net Bağlam"
type: context
folder: "home.coremusic.net"
category: domain
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
 authority: "home.coremusic.net/CLAUDE.md"
 source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md · .ai/WORKFLOW.md"
---

# home.coremusic.net — CLAUDE.md (Detaylı Versiyon)

**Zorunlu Bağlantılar:** · [[../.ai/architecture/k10-uygulama]] · [[../.ai/architecture/k11-ux]] · [[../shared/CLAUDE.md]]

---

## 1. Bağlam & Amaç

CoreMusic'in **ana medya paneli**. Kullanıcıların müzik dinlediği, kütüphane yönettiği, podcast ve radyo eriştiği birincil arayüz. **Tek bileşen ilkesi** ile çalışır: tek HTML yapısı + responsive CSS (Guardrail #17).

**Cihaz Duyarlı Render:**
```
DeviceManager.php → 4-Tier Karar:
 ├── Phone (≤767px) → Tek sütun, dikey scroll
 ├── Embedded (≤1024px) → 42/58 split, 2×2 widget
 ├── Wide (1025-2560px) → 3-sütun, tam widget
 └── 4K (≥2561px) → 4K ölçeklendirilmiş
```

---

## 2. Mevcut Durum (Detaylı)

| Durum | Değer |
|-------|-------|
| Entry point | `index.php` (PageRouterKernel) |
| Ana sayfa | `pages/home.php` v10.0.0 (4-tier conditional rendering) |
| Header | `header.php` v8.0.0 (Phone bottom nav + tier bazlı header) |
| Footer | `footer.php` v11.0.0 (Phone compact player + tier bazlı footer) |
| Include | `include/` (Auth, Session, Container, Config) |
| DeviceManager | `shared/src/Device/DeviceManager.php` v2.0.0 |
| CSS | `assets.coremusic.net/Css/` (ITCSS 9-layer) |
| JS | `assets.coremusic.net/js/` (Vanilla ES6+) |

---

## 3. Dosya Yapısı

```
home.coremusic.net/
├── index.php ← Entry point (PageRouterKernel)
├── header.php ← Single Header View (4-tier conditional)
├── footer.php ← Single Footer View (4-tier conditional)
├── pages/
│ └── home.php ← Single Home View (4-tier conditional v10.0.0)
├── include/
│ ├── Auth/ ← Auth bridge (HomeAuthBridge)
│ ├── Session/ ← Session yönetimi
│ ├── Container/ ← DI konteyneri
│ └── config/ ← Constants, app config
├── config/ ← Uygulama yapılandırması
├── .htaccess ← Apache rewrite
├── web.config ← IIS rewrite
├── composer.json ← Bağımlılıklar
├── phpunit.xml ← Test yapılandırması
└── tests/ ← Test dosyaları
```

---

## 4. 4-Tier Conditional Rendering

### 4.1 Tier Karar Matrisi

| Tier | Cihaz | Layout | Widget | Footer |
|------|-------|--------|--------|--------|
| Phone | ≤767px | Tek sütun | 2 | Kompakt player |
| Embedded | ≤1024px | 42/58 split | 4 | Tam player |
| Wide | 1025-2560px | 3-sütun | 4-6 | Tam player |
| 4K | ≥2561px | 4K ölçekli | 6 | Tam player |

### 4.2 DeviceManager PHP Metotları

```php
$dm = DeviceManager::fromRequest(
 viewportW: (int)($_SERVER['VIEWPORT_W'] ?? 0) ?: null,
 viewportH: (int)($_SERVER['VIEWPORT_H'] ?? 0) ?: null,
);

$isPhone = $dm->isPhone(); // ≤767px
$isEmbedded = $dm->shouldRenderEmbeddedLayout(); // Embedded/Tablet/viewport≤1024
$isWide = $dm->shouldRenderWideLayout(); // 1025-2560px
$is4k = $dm->shouldRender4kLayout(); // ≥2561px
```

### 4.3 Backend Sorumluluk Sınırları

| Sorumluluk | Örnek | PHP Metodu |
|------------|-------|------------|
| Widget sayısı | Embedded: 4, Desktop: 6 | `$dm->widgetCount()` |
| Widget görünürlüğü | Podcast/Radio sadece geniş ekran | `$dm->showPodcastWidget()` |
| Liste kart sayısı | Recent: 3-8, Playlist: 0-6 | `$dm->recentCardCount()` |
| Meta veri anahtarları | Tam metadata sadece geniş ekran | `$dm->showFullMetadata()` |
| Ses/seekbar görünürlüğü | Volume phone'da gizli | `$dm->showVolume()` |
| Navigasyon link sayısı | Phone: 3, Desktop: 8 | `$dm->navLinks()` |
| CSS sınıfı atama | `layout--embedded` | `$dm->layoutClass()` |
| Veri niteliği | `data-device="desktop"` | `$dm->dataAttributes()` |

**Yasak PHP Kodları:** `margin`, `padding`, `width`, `height`, `font-size` → bunlar CSS'e aittir.

---

## 5. Komşu İlişkiler (Detaylı)

| Yön | Hedef | İlişki | Etki |
|-----|-------|--------|------|
| Parent | [[../AGENTS.md]] | Kök registry | — |
| Auth | [[../auth.coremusic.net/CLAUDE.md]] | HomeAuthBridge + auth_callback.php | Yüksek (auth akışı) |
| Asset | [[../assets.coremusic.net/CLAUDE.md]] | CSS (ITCSS) + JS (Vanilla) + DeviceLoader | Yüksek (görsel) |
| Paylaşılan | [[../shared/CLAUDE.md]] | RuntimeBootstrap, Config, Session, DeviceManager | Yüksek (altyapı) |
| Vault | [[../.ai/.subdomains/home.coremusic.net/index.md]] | Subdomain vault kaydı | Düşük |

---

## 6. Değişiklik Protokolü

| Adım | Aksiyon | Kontrol |
|------|---------|---------|
| 1 | Mockup oku | `.ai/ui-design/00-mockup-index.md` (Guardrail #11) |
| 2 | Component inventarı oku | `.ai/ui-design/01-component-inventory.md` |
| 3 | Token'ları oku | `.ai/ui-design/tokens/design-tokens-master.md` |
| 4 | ASCII art oku | `.ai/ui-design/screens/00-ascii-art-index.md` |
| 5 | Responsive kuralları oku | `.ai/ui-design/responsive-device-mode.md` |
| 6 | Kod yaz | Tek dosya + responsive CSS |
| 7 | Test et | Tüm tier'larda test |
| 8 | Audit | `log.md`'ye yaz |

---

## 7. Yasaklar

| # | Yasak | Neden |
|---|-------|-------|
| 1 | Ayrı HTML dosyaları (`home-1024.php`) | Guardrail #17 ihlali |
| 2 | PHP'de sunum kararı (margin, padding) | Layer violation |
| 3 | Framework kullanımı (React, Vue) | ADR-001 yasağı |
| 4 | `innerHTML` | XSS riski |
| 5 | Hardcoded resolution | CSS variables + media queries |
| 6 | Mockup okumadan kod | Guardrail #11 ihlali |

---

## 8. İlgili Kaynaklar

| Kaynak | Yol | İçerik |
|--------|-----|--------|
| Mockup indeksi | `.ai/ui-design/00-mockup-index.md` | 19 PNG mockup |
| Component envanteri | `.ai/ui-design/01-component-inventory.md` | C01-C16 BEM |
| Token'lar | `.ai/ui-design/tokens/design-tokens-master.md` | Renk, boşluk, tipografi |
| ASCII art | `.ai/ui-design/screens/00-ascii-art-index.md` | Piksel düzeyinde layout |
| Responsive | `.ai/ui-design/responsive-device-mode.md` | 4-tier CSS kuralları |
| DeviceManager | `shared/src/Device/DeviceManager.php` | PHP cihaz yönetimi |
| DeviceLoader | `assets.coremusic.net/js/device-loader.js` | JS cihaz tespiti |
| ADR-044 | `.ai/decisions/accepted/ADR-044-dynamic-user-theme-engine.md` | Tema motoru |
| ADR-045 | `.ai/decisions/accepted/ADR-045-multi-domain-view-mode-architecture.md` | View mode |

---

## 9. Test Yapısı

| Test | Konum | Kapsam |
|------|-------|--------|
| DeviceManager | `shared/tests/Unit/Device/` | Cihaz tespit testleri |
| DeviceDetector | `shared/tests/Unit/Device/` | Tespit algoritması |
| Rendering | Manuel test | 4-tier görsel doğrulama |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
