---
type: instruction
category: l3
title: "AI Sistem Talimatları — Scale, Router, CSS, Device Sistemleri"
date: 2026-09-06
updated: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# AI Sistem Talimatları — Scale, Router, CSS, Device Sistemleri

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[scale-router-css-frontend-guide]] · [[device-breakpoint-guide]] · [[00-mockup-index]] · [[01-component-inventory]] · [[ADR-001-vanilla-js-itcss]]

---

## 1. Amaç

Bu dosya, bir AI ajanının CoreMusic frontend sistemlerinde (ölçek, router, CSS, cihaz, frontend entegrasyonu) çalışırken **uyacağı bağlayıcı talimatları** tanımlar. Bu talimatlar CLAUDE.md Hard Guardrails'in L3 uzmanlaşmış alt kümesidir; çelişki durumunda CLAUDE.md geçerlidir.

---

## 2. Boot Talimatı (Her Oturum Başında)

1. `.ai/CLAUDE.md` → 16 Hard Guardrails
2. `.ai/AGENTS.md` → domain sınırı (ui-designer = L3 yalnız)
3. `.ai/ui-design/00-mockup-index.md` → 19 PNG mockup indeksi (Guardrail #11)
4. `.ai/ui-design/01-component-inventory.md` → C01-C16 BEM/ölçüm SSOT
5. `[[scale-router-css-frontend-guide]]` → sistem davranışı
6. `[[device-breakpoint-guide]]` → cihaz/breakpoint prosedürleri
7. `.ai/log.md` → son 20 satır (önceki oturum durumu)

**Kural:** Bu dosyalar okunmadan kod yazmak, plan yapmak veya dosya oluşturmak YASAKTIR.

---

## 3. SSOT Zinciri ve Karar Hiyerarşisi

```
Karar sırası (üst kazanır):
1. PNG mockup (.ai/.png/) + MD specification
2. ADR kararları (ADR-001, ADR-044, ADR-045, ADR-083)
3. .ai/architecture/l3-presentation/ rehber dosyaları (bu klasör)
4. Mevcut kaynak kod (doğrulanmış davranış)
5. Kullanıcı talebi (1-4 ile çelişiyorsa DUR ve bildir)
```

**Cihaz SSOT zinciri:** `a-breakpoint-tokens.css` = `device-loader.js BP` = `DeviceManager.js BREAKPOINTS` = `DeviceDetector.php` → `devices.config.js` = `DeviceCssMap.php` → `08_Devices/d-*.css`.

**Scale SSOT zinciri:** `ScaleManager.js DEFAULT_SCALE_TARGETS` → `a-scale-hybrid.css` → `a-layout-tokens.css` → `d-4k.css` (≥2561px skip sözleşmesi).

---

## 4. Sistem Sözleşmeleri Özeti (Bilinmesi Zorunlu)

| Sözleşme | Değer | Kaynak |
|----------|-------|--------|
| Cihaz kümesi | embedded, phone, tablet, laptop, desktop, 4k-tv, 4k-monitor | devices.config.js `ALL` |
| Tier kümesi | phone, embedded, wide | device-loader.js `getTier()` |
| Tier uyuşmazlığı | max 2 deneme → `Router.navigate(pathname)` → `location.reload()` | device-loader.js init |
| Resize debounce | 300ms (device-loader), 200ms (DeviceManager.js), RAF (ScaleManager) | kaynak kod |
| ScaleManager skip | ≥2561px tüm hedeflerde skip:true → CSS zoom devralır | ScaleManager.js |
| 4K ölçek | ≥3840px zoom×2, ≥7680px zoom×3, fallback transform | d-4k.css |
| Device CSS | self-contained; auth varyantı `_home.css` import etmez | d-4k.css / d-auth-*.css |
| PHP sunum yasağı | PHP'de margin/padding/width/height/font-size YASAK (sadece davranışsal konfigürasyon) | brain.md §18C |
| Event akışı | `devicechange` → DeviceLayoutUpdater.updateAll; EventBus `scale:applied` | kaynak kod |
| Legacy köprüler | window.ScaleCoordinator, scaleHeaderForScreen, scaleFooterForScreen, scaleHomeForScreen | ScaleManager.js |

---

## 5. Üretim Akışı (Zorunlu 7 Adım)

1. **OKU:** İlgili ekranın PNG mockup'ı + C01-C16 envanteri + mevcut kaynak dosyalar (sınır komşu 2 dosya dahil).
2. **PLANLA:** Değişecek dosya listesi + senkron zinciri etkisi (cihaz işi varsa 13 nokta kontrolü).
3. **ONAY:** Mimari değişiklik, dosya adı/konumu değişimi, breakpoint değişimi → kullanıcı onayı bekle (Human Mode).
4. **KOD:** Mevcut deseni birebir takip et (BEM, ITCSS katmanı, `const`/`var` kuralları, DOMParser + TrustedTypes).
5. **DOĞRULA:** `php -l`, PHPUnit (varsa ilgili test), DevTools canlı test, console temizliği. Maksimum 1 doğrulama denemesi; başarısızsa durumu raporla.
6. **LOG:** `.ai/log.md`'ye `[YYYY-MM-DD HH:MM:SS] [LEVEL] [AGENT] [ACTION]` formatında ekle.
7. **VAULT-SYNC:** Vault değişikliği varsa oturum sonunda vault-sync çalıştır; index.md ve keys.md kayıtlarını güncelle.

---

## 6. Yasaklar (Hard Rules — L3)

1. **Framework YASAK** (React, Vue, jQuery) — ADR-001 / R-001, R-003.
2. **`innerHTML` YASAK** — DOMParser + TrustedTypes; `textContent` + DocumentFragment.
3. **`var` ve `eval()` YASAK** — `const`/`let` (IIFE içindeki mevcut `var`'lar geriye dönük uyumluluk için korunur; yenisi yazılmaz).
4. **Token'sız boyut YASAK** — her renk/boşluk/font/breakpoint token ile (`--fs-*`, `--gap-*`, `--radius-*`).
5. **`:root` default (RPi5 1024×600) düzenleme YASAK** — sadece yeni `@media` bloğu.
6. **Cihaz adı/aralık değiştirme YASAK** — yalnızca ekleme ([[device-breakpoint-guide]] §6).
7. **PHP'de CSS özellik YASAK** — davranışsal konfigürasyon (sayı, bool, nav listesi) hariç.
8. **PNG'den sapma YASAK** — ölçüler PNG piksel ölçümüdür; yalnızca hover/focus/responsive/a11y eklenir.
9. **Ölçülemez iddia YASAK** — doğrulanamayan bilgi için `**VERIFICATION REQUIRED**` yazılır.
10. **Emoji/illustration ekleme YASAK** — PNG'de yoksa koda da yok.

---

## 7. Doğrulama Protokolü (Zero Hallucination)

- Her API adı, dosya yolu, token adı kodda grep edilerek doğrulanır; bulunamazsa yazılmaz.
- Doğrulanamayan ama gerekli alan: `**VERIFICATION REQUIRED**` + önerilen doğrulama yöntemi.
- Çıkarım (inference) sunulursa mutlaka `ÇIKARIM` etiketi taşır.
- Örnek doğrulanmış negatif bulgu: kod tabanında iframe renderer YOKTUR; `DomPatcher.js` `DANGEROUS_ELEMENTS` listesinde `iframe` temizlenir.

---

## 8. Hata ve Çakışma Protokolü

| Durum | Aksiyon |
|-------|---------|
| Rehber ↔ kod çelişkisi | Kod kazanır, rehber güncellenir (yerinde), log'a yazılır |
| İki rehber çelişkisi | SSOT hiyerarşisi (§3) uygulanır; çelişki kullanıcıya bildirilir |
| Doğrulama 3 kez başarısız | DUR; şüpheli varsayım adıyla raporlanır |
| Tier-sync reload döngüsü | `cm_tier_sync_count` + `getTier()` eşlemesi kontrol edilir (4k→wide kuralı) |
| Paralel oturum çakışması | log.md son girişleri kontrol edilir; home.php gibi ortak dosyalarda koordinasyon notu düşülür |

---

## 9. Çıktı Formatı Kuralları

- **Log:** `[2026-09-06 HH:MM:SS] [INFO] [ui-designer] [CREATE|UPDATE|DELETE|VERIFY] açıklama — değişen dosya listesi.`
- **Yeni dosya:** frontmatter 7 zorunlu alan (`type, category, title, date, updated, status, version, authority, governance`) + sonunda Quality Report bölümü.
- **Şablon zorunluluğu (Guardrail #16):** yeni kod dosyası `.ai/.templates/frontend/js-template.md` veya `css-template.md` kalıplarıyla uyumlu yazılır.
- **Yerinde değişiklik:** mevcut doküman güncellenir; dosya adı/konumu onaysız değiştirilemez (WORKFLOW Kural 2).

---

## 10. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **Talimat Bölümü** | 10 |
| **Kaynak Doğrulaması** | ✅ ScaleManager.js v7, device-loader.js, devices.config.js, DeviceCssMap.php, DeviceManager.php (PHP v2.0.0 / JS v5.0.0), device-layout-updater.js, a-layout-tokens.css v3.1.0, a-scale-hybrid.css v3.0.0, d-4k.css v3.0.0, DomPatcher.js |
| **ADR Uyumlu** | ✅ ADR-001, ADR-044, ADR-045 |

---

---

## 11. Ekran Bazlı Görev Rehberi

| Ekran (qr-*) | Tipik Görev | İlgili Bileşen/Dosya | Kritik Dikkat |
|--------------|-------------|----------------------|---------------|
| home-1024 | Widget/grid düzeni | home.php 3 blok + _home-layout.css + _home-components.css | 42/58 split token'lı; PHP sunum yasak |
| home-1920 | Wide 3 sütun | --home-top-split 42% 58% + bottom 1fr 1.5fr 1fr | desktop media query ≥1920 |
| login | Form + sosyal butonlar | p-login-view.css + C06/C08 | auth shell minimal (auth-bundled) |
| register-step1/2-3 | Form akışı | p-register-*.css + C06 | adım geçişleri JS |
| gender-select | C07 kart seçimi | p-select-gender.css | set-gender bypass (tek meşru) |
| welcome-popup | C14 modal (embedded only) | _home-components + shouldRenderWelcomePopup | yalnız RPi5 1024×600 |
| albums/artists | C09 grid + scroll-snap | c-cards.css + CardManager | scroll-snap mobil |
| album-detail | C10 panel + track list | c-lists.css + C13 | art boyut token (280/400/600) |
| playlist | C13 satırlar + upNext | c-lists.css | 48px row height |
| video-playback | Player entegrasyon | PlayerController | audio kesintisiz (shell sabit) |
| disk-browser/file-list | Dosya listesi | c-lists.css + C15 toggle | embedded disk erişimi |
| wifi/wifi-connect/bluetooth | C14 modallar + C16 satırlar | c-modals.css + DeviceManager status | Status Widget C02 bağlantısı |
| settings | C15 toggle'lar | ayarlar.php (PLANNED tam) | dark/light + gender ikilisi |

Kural: Ekran görevi başlarken karşılık gelen qr-* dosyası + PNG okunur (Guardrail #11); tablo "hangi dosyaya dokunacağım" haritasıdır.

---

## 12. Yaygın Hata Giderimi (L3)

| Belirti | Muhtemel Kök | Çözüm |
|---------|--------------|-------|
| Stil uygulanmıyor | CSS import zinciri kırık (self-contained) | d-*.css @import listesini doğrula (device-breakpoint §4 Adım 12) |
| Cihazda yanlış layout | Viewport cookie eski | cookie temizle + reload (§12.9.4 paralel) |
| Script CSP engellendi | nonce eksik/yanlış | shell nonce akışı (html-shell §13) |
| Scale çift uygulama | JS scale + CSS zoom çakışması | ScaleManager skip sözleşmesi (§14) |
| Tema değişmiyor | data-gender attribute güncellenmiyor | ThemeManager attribute-swap (theme-engine §4) |
| Widget sayısı yanlış | PHP/JS nav-config sapması | DeviceManager NAV_LINKS çift SSOT kontrolü (breakpoint-guide Adım 3+4) |
| Token bulunamadı | 01_Abstracts dışında tanım | §6 kural 4 — token master'a taşı |

---

## 13. Token Hızlı Referans (AI Üretimi İçin)

| İhtiyaç | Token | Kaynak Dosya |
|---------|-------|--------------|
| Header/Footer yükseklik | --header-h / --footer-h | a-layout-tokens.css |
| İçerik yükseklik | --content-h | a-layout-tokens.css |
| Touch minimum | --touch-min (48px / car 56px+) | a-layout-tokens |
| Kart görsel boyutu | --card-thumb-size, --now-playing-art-size, --mini-card-art-size, --detail-panel-art-size | a-layout-tokens (11 component token) |
| Widget ızgarası | --widget-grid-cols, --widget-min-height | a-layout-tokens |
| Footer ikon/alan | --footer-icon-size, --footer-btn-min-size, --footer-album-art-size | a-layout-tokens |
| Grid split | --home-top-split, --home-bottom-split | a-layout-tokens |
| Tema ana rengi | data-gender blokları (#ff4fd8/#4f9fff/#a0a0b0) | a-semantic-token.css |
| Dark/light zemin-metin | --bg-base, --text-primary... | a-color-mode-tokens.css |
| Scale | --cm-scale-step-1/2/3 | a-scale-hybrid.css / d-4k.css |
| Breakpoint | --bp-{device} | a-breakpoint-tokens.css |
| Font | --text-base, font ailesi | a-fonts-token.css |

Kural: Bu tablo AI'a "hangi token var" der; DEĞER uydurmaz — dosyadan okunur. Yeni token ihtiyacı → 01_Abstracts'e ekleme (kullanıcı onayı, §5 adım 3).

---

## 14. Scale Sistemi Talimat Detay

| Konu | Kural |
|------|-------|
| Zoom kademeleri | Kademe 2: ≥3840px zoom×2; Kademe 3: ≥7680px zoom×3 (d-4k.css) |
| Fallback | Transform (ScaleManager) zoom desteklemeyen ortamda |
| Skip sözleşmesi | ≥2561px tüm ScaleManager hedefleri skip:true — CSS zoom devralır |
| Yeni cihaz ölçek | Önce d-4k paylaşımı dene; bağımsız gerekirse YENİ kural, eski kurallara dokunma (breakpoint-guide Adım 11 Yöntem A/B) |
| Kural sırası | ScaleCalculator ilk eşleşmede durur — ekleme sona |
| Legacy köprüler | window.ScaleCoordinator + scaleXForScreen korunur (geriye uyum) |
| Olay | scale:applied — EventBus ile layout haberdar olur |

Talimat: Scale ile oynayan görevde ÖNCE [[scale-router-css-frontend-guide]] (995 ✓) okunur; bu dosya özet, guide tam rehber.

---

## 15. Router Görev Talimatları

| Görev | Kural |
|-------|-------|
| Yeni sayfa route'u | routes.php kayıt + SpaRoute alanları + sayfa dosyası tek bileşen (route-config §14) |
| SPA navigasyon | Router.js doğrudan (SPARouterAdapter DEPRECATED — kullanma) |
| Guard ekleme | guards.js + GuardPipeline; PHP AuthGuard nihai karar |
| DOM patch | DomPatcher + ContentPatcher; innerHTML yasak |
| Scroll restore | ScrollManager route anahtarlı |
| CSRF sync | CsrfSyncManager — meta tag güncel tutulur |
| Deep-link | PHP shell ilk yükleme (ADR-083 hibrit) |

---

## 16. CSS Katman Karar Ağacı ("Bu stili nereye yazacağım?")

```
Değer cihazdan bağımsız sabit mi?
  ├─ Evet → 01_Abstracts token (yeni token = onay) veya ilgili c-*.css
  └─ Hayır (breakpoint'e bağlı mı?)
       ├─ Evet → a-layout-tokens.css @media bloğu (yeni blok — :root dokunma)
       └─ Cihaza özgü davranış mı? (hover/touch/scrollbar)
            ├─ Evet → d-{device}.css (yalnız behavioral)
            └─ View mode'a özgü mü? → v-{mode}.css
Sayfa düzeni ise → 05_Pages/_{sayfa}.css (tek bileşen ilkesi)
Header/Footer ise → 03_Layout/_header|_footer.css
```

Karar ağacı geçersiz hedef: 04_Components'e sayfa düzeni, 08_Devices'e token, 01_Abstracts'e davranış.

---

## 17. Device İşleri Talimat Özeti

1. 13 nokta senkron (§3 zincir) — [[device-breakpoint-guide]] tam prosedür.
2. d-auth-* dosyaları Test-Path ile teyit et (device-css §9 çelişki görevi — yazmadan önce VAR/YOK).
3. DeviceDetector.php okumadan tespit kuralı ekleme (breakpoint-guide Adım 5 VERIFICATION REQUIRED kapısı).
4. PHP DeviceManager metotları yalnız davranışsal sayı/bool döner (§6 kural 7).
5. NAV_LINKS PHP↔JS birebir (Adım 3+4+7 çift SSOT).

---

## 18. Erişilebilirlik Talimatları

| Konu | Kural |
|------|-------|
| Touch target | 48px (phone/embedded), car 56px+ (dark-light §13) |
| Focus | :focus-visible outline — outline:none yasak |
| Kontrast | 4.5:1 — tema token değişiminde korunur (theme-engine §9) |
| aria-busy | SPA navigasyonunda true/false (js-module shell §28 SSS) |
| aria-current | navLinks active (device-css §4A.8) |
| Ekran okuyucu | Live region — mode/state değişim bildirimleri (PLANNED) |
| Renk tek bilgi olamaz | Tema değişse işlev korunur (theme-engine §9) |

---

## 19. Ek SSS

**S: AI olarak PNG ölçüsü ile token çakışırsa?**
C: PNG kanonik (§3 karar sırası 1) ama token sistemi kanonik da — çözüm: token değerini PNG'ye göre güncelle (onaylı), yeni hardcoded değer YAZMA. §6 kural 4+8 birlikte.

**S: Aynı anda iki ekran görevi?**
C: Tek aktif görev ilkesi (engine §5) — sıralı yürüt; ortak dosya (_home-components vb.) çakışması Context Lock'a tabidir.

**S: Kodda iframe görürsem?**
C: §7 negatif bulgu: DomPatcher DANGEROUS_ELEMENTS iframe temizler — iframe renderer yoktur. Yeni iframe eklemek yasak deseni ihlalidir.

**S: DomainConfig değerlerini nereden okurum?**
C: `shared/config/domain.php` (0.5KB) — kod oku; hardcode etme (subdomain-routing §12/§15 uyarıları).

**S: 19 PNG sayımını nerede doğrularım?**
C: `.ai/.png/home-1024/` (12) + `home-1920/` (1) + `shared-1024/` (6) — l3 index §11 komut 6.

**S: Bu dosya ile ULTRA-THINKING farkı?**
C: ULTRA-THINKING genel düşünme protokolü; bu dosya L3 domain-spesifik talimatlardır. Boot'ta ikisi de okunur (§2 + ULTRA-THINKING §3.1).

---

## 20. Risk Kaydı

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 1 | Guardrail #11 atlanması | Orta | Kritik | §2 boot + §5 adım 1 |
| 2 | Token dışı değer | Orta | Orta | §13 referans + §6 kural 4 |
| 3 | :root default bozulma | Orta | Yüksek | §6 kural 5 |
| 4 | d-auth-* varlığını varsayma | Kesin (çelişki) | Orta | §17 madde 2 |
| 5 | SPARouterAdapter kullanımı | Düşük | Düşük | §15 tablo |
| 6 | Scale çift uygulama | Orta | Orta | §14 skip sözleşmesi |

---

## 21. İzlenebilirlik Ek

| İddia | Kaynak | Doğrulama |
|-------|--------|-----------|
| ScaleManager v7 referansı | Bu dosya §10 | Kendi kalite raporu — dosya tarihçesinden güncel |
| d-4k.css v3.0.0 kademe 2/3 | §4 tablo | Kod-dogrulanmış kaynak listesi |
| iframe negatif bulgu | §7 | DomPatcher.js DANGEROUS_ELEMENTS |
| 300/200ms/RAF debounce | §4 tablo | Kaynak kod |
| Legacy köprüler | §4 tablo | ScaleManager #registerGlobalBridge |
| cm_tier_sync_count | §8 | device-loader init |

---

## 22. Revizyon Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 1.0.0 | 2026-09-06 | AI talimatları (kod-dogrulanmış) |
| 1.1.0 | 2026-09-08 | Faz 2d: §11 ekran rehberi; §12 hata giderim; §13 token hızlı referans; §14-§18 scale/router/katman-ağacı/device/a11y talimatları; §19-§21 ekler |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-08
**Mode:** Red Team · Human Mode · Truth Mode
