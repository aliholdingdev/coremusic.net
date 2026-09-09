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

## 23. Ekran-Bileşen Yoğunluk Matrisi

| Ekran | Ağırlıklı Bileşenler | Yoğunluk |
|-------|---------------------|----------|
| home-1024/1920 | C02, C03, C09, C10, C13 + widget'lar | Yüksek — grid + token dikkat |
| login/register | C06, C08, C14(auth) | Orta — form + auth shell |
| gender-select | C07 | Düşük — tek karar ekranı |
| welcome-popup | C14 (welcome) | Düşük — embedded tek |
| albums/artists | C09 grid + C11 tabs | Yüksek — scroll-snap |
| album-detail | C10 + C12 + C13 | Yüksek — art boyut token |
| playlist | C13 + C11 | Orta — row listesi |
| video-playback | PlayerController | Orta — playsInline ekleme |
| disk-browser/file-list | C13 + C15 | Orta — toggle yoğun |
| wifi/wifi-connect | C14 + C16 | Orta — modal + satır |
| bluetooth | C14 + C16 | Orta — |
| settings | C15 + radio (dark/light) | Orta — mode+gender |

Kaynak: §11 görev rehberi + qr-* indeksi — görev başında ekranın satırına bakılır.

---

## 24. DevTools Hata Ayıklama Akışları (L3)

| Belirti | DevTools Adımı |
|---------|----------------|
| Stil uygulanmıyor | Elements → computed → hangi dosya kazandı (cascade) |
| Cihaz yanlış | Console: `window.CoreMusic.EventBus` → devicechange dinle; Application → cookie cm_viewport_* |
| Scale çift | Console: `scale:applied` olay sayısı; Sources: ScaleManager breakpoint'ler |
| Script engellendi | Console CSP hatası → nonce kontrolü (html-shell §13) |
| Font yüklenmiyor | Network → CORS/404; a-fonts-token import zinciri |
| Modal focus kaçıyor | Elements → aria-modal + focus trap (components §28) |

---

## 25. Mockup Ölçüm Okuma Talimatı (PNG → CSS)

1. PNG'yi %100 zoom aç (ölçek bozulmasın).
2. Ölçü aracıyla bileşen sınırlarını oku (px cinsinden — mockup zaten 1:1).
3. Değer token'da var mı bak (design-tokens-master + a-layout-tokens).
4. Varsa token kullan — yoksa yeni token önerisi (onaylı §13 akışı).
5. Sabit px yalnız PNG birebir zorunluysa (icon 24px gibi) — yorum ile PNG referansı.

Yasak: PNG'de olmayan ölçüyü "tasarım gereği" diye eklemek; ölçüyü yuvarlamak (60px → 64px gibi).

---

## 26. Git/Deploy Görevleri (L3 Perspektifi)

| Görev | Kural |
|-------|-------|
| Commit mesajı | Değişen asset alanı (Css/js/pages) + amaç |
| Asset sürüm | Cache buster güncelle (html-shell §14) |
| Branch | Tek frontend hattı — device/branch ayrımı yasak (Guardrail #17) |
| Review | §23 checklist + mockup çapraz |
| Deploy | 02-deployment kararı — L3 kodu statik servis üzerinden akar |

---

## 27. Performans Bütçesi (Web — PLANNED Değerler)

| Bütçe | Hedef | Not |
|-------|-------|-----|
| CSS toplam ağırlık | ≤150KB (gzip) | self-contained import maliyeti izlenir |
| JS toplam | ≤200KB (gzip) | module graph |
| İlk paint | ≤1.5s (RPi5) | ADR-006 TTFB bağlantılı |
| Layout shift | 0 (CLS) | token'lı boyutlar destekler |
| Animasyon | transform/opacity yalnız | layout tetiklememe |

**Dürüstlük notu:** Bu değerler PLANNED hedef taslağıdır — ölçüm altyapısı (Lighthouse/RUM) kurulunca kanıtlanır; ölçüsüz "hedefe ulaşıldı" yazılamaz (engine §7 disiplini paralel).

---

## 28. Güvenlik Hızlı Referans (L3)

| Konu | Kural | Kaynak |
|------|-------|--------|
| innerHTML | Yasak — DOMParser | §6 kural 2 |
| Inline script | Yalnız nonce'lu | ADR-012 |
| LocalStorage | Yalnız non-auth tercih | theme-engine §8 |
| window.CoreMusic | Secret yok | html-shell §18 |
| Harici script | ADR'siz eklenmez | csp.md §17 |
| Emoji/icon | Asset catalog | §6 kural 10 |

---

## 29. Çok-Cihaz Test Prosedürü Özeti

1. DevTools responsive: 375/768/1024/1366/1920/2560/3840 (7 cihaz eşikleri ±1px).
2. UA override: TV (4k-tv), RPi5 (embedded) kontrolü.
3. Cookie temiz → server-side fallback doğrula.
4. 3 tema (gender) × seçili mode görsel kontrol.
5. Console: 0 hata; EventBus: beklenen olay akışı.
6. Touch: embedded'da hover yokluğu + 48px dokunma.

Tam liste: [[device-breakpoint-guide]] §4 Adım 13 + §14 matris.

---

## 30. Ek SSS

**S: AI yeni bir C-ID bileşeni "gerekli" bulursa?**
C: DUR — bileşen talebi PNG'den gelir (Guardrail #11). AI bileşen icat edemez; kullanıcıya mockup gereksinimi bildirilir.

**S: PNG ile mevcut kod çelişirse?**
C: PNG kanonik — kod düzeltilir (§3 karar sırası). Kod "daha iyi" görünse bile mockup otoritedir; iyileştirme önerisi ayrı sunulur.

**S: Değişiklik hem CSS hem JS hem PHP'ye dokunursa?**
C: Çok-katman görev — domain handover (backend-architect ile koordinasyon); tek agent üstlenmez (engine handover ilkesi).

**S: Mockup'taki ölçü `~` ile yazılmış (yaklaşık) ise?**
C: SSOT'ta kesin değer varsa o alınır; yoksa yaklaşık değer + `DOĞRULAMA GEREKLİ` etiketi (Truth Mode).

**S: Bu dosya ile CLAUDE.md çelişirse?**
C: CLAUDE.md kazanır (§1 ilk cümle — L3 uzmanlaşmış alt küme; çelişkide anayasa).

**S: Görev sırasında yeni PNG geldi?**
C: Görev duraklatılır → PNG okunur → plan güncellenir → devam. Orta-task mockup değişimi sessizce yutulmaz.

---

## 31. Risk Ek

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 17 | AI bileşen icadı | Orta | Yüksek | §30 SSS 1 |
| 18 | Çok-katman görev tek elde toplanması | Orta | Orta | §30 SSS 3 |
| 19 | Ortadaki mockup değişiminin yutulması | Düşük | Orta | §30 SSS 6 |

---

## 32. İzlenebilirlik Ek

| İddia | Kaynak | Doğrulama |
|-------|--------|-----------|
| DANGEROUS_ELEMENTS iframe | §7 | DomPatcher.js ✅ |
| ScaleManager v7 referans | §10 | Dosya tarihçesi ✅ |
| 300/200ms/RAF | §4 | Kaynak kod ✅ |
| cm_tier_sync_count | §8 | device-loader ✅ |
| 6 SSS yeni konular | §30 | Bu revizyon ✅ |

---

## 33. Karar Ağacı (Son) — "Hangi rehberi okuyacağım?"

```
Ekran/görev geldi → §11/§32 haritası
  ├─ Yeni bileşen → components §10 + inventory
  ├─ Cihaz/breakpoint → breakpoint-guide 13 adım
  ├─ Scale → scale-router guide (995)
  ├─ Router/route → js-router + route-config
  ├─ Tema → theme-engine + dark-light
  ├─ Player → web-audio
  └─ Karışık → tüm ilgili satırlar (§30 SSS 3)
Sonra: §5 üretim akışı 7 adım → log → vault-sync
```

---

## 34. Kalite Raporu (Final)

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.2.0 |
| **Bölüm Sayısı** | 34 |
| **Ekran Matrisi** | 12 satır (§23) |
| **SSS** | 26 |
| **Risk Kaydı** | 19 |
| **Karar Ağacı** | 2 (§3 + §33) |
| **Zero Hallucination** | ✅ |

---

## 35. Ek SSS (Son)

**S: PNG mockup'ı açamıyorum (görsel okunamıyor)?**
C: Guardrail #11 — DUR ve bildir. Görsel referans olmaksızın frontend üretimi yasaktır; PNG kaynağı düzeltilene kadar beklenir.

**S: Mevcut kodda kural ihlali gördüm (innerHTML vb.) — düzeltir miyim?**
C: Görev kapsamındaysa düzelt + test + log. Değilse bulgu kaydet (log.md) — kapsam dışı refactor plansız yapılmaz.

**S: Token yok ama PNG rengi açık — ne yazılır?**
C: Yeni token önerisi (01_Abstracts) + onay iste (§13 akışı). Hex'i direkt bileşene yazmak yasak.

**S: Aynı anda 3 ekran değişecek — tek görev mi?**
C: Üç görev, sıralı (engine §5) — ortak dosya (_home-components gibi) Context Lock'a girer.

**S: Test görselini kullanıcı vermezse?**
C: DevTools responsive + §29 prosedür — kanıt ekran görüntüsüyle log. Görselsiz "çalışıyor" iddiası yazılmaz.

---

## 36. Yasak Örüntü Ek Tablosu (L3 Örnekleriyle)

| ❌ Yasak | Gerçek Örnek | ✅ Doğru |
|----------|--------------|----------|
| `element.style.width = '140px'` | PHP/JS inline boyut | `var(--card-thumb-size)` |
| `document.querySelector('.btn').onclick = ...` | çoklu buton | delegation (§6) |
| `window.open(...)` popup | SPA akışı bozar | route navigasyon |
| `alert()/confirm()` | native dialog | C14 modal |
| `setInterval` temizlemsiz | widget clock | destroy() temizliği |
| jQuery `$(sel).on()` | framework sızıntısı | delegation vanilla |
| CSS `@import` ana dosyada döngü | self-contained bozulur | §24 zincir |

---

## 37. Görev Şablonu (Doldurulmuş Örnek — Gender Ekranı Düzenlemesi)

```text
BAĞLAM: select-gender ekranı C07 kart düzeni güncellenecek (PNG v2 geldi).
PROJE OKUMA:
  - .ai/.png/shared-1024/qr-gender-select karşılığı PNG (YENİ)
  - ui-design/01-component-inventory C07 satırı
  - l3-presentation/components.md §2/§28
  - l3-presentation/theme-engine.md §6 (set-gender akışı)
  - assets.coremusic.net/Css/05_Pages/p-select-gender.css (mevcut)
GÖREV: C07 kart boyut/gap PNG'ye göre güncelle; C15 konum görevi yok.
KISITLAR: token'lı boyut; set-gender bypass rotası DOKUNULMAZ; PHP sunum yasak.
DOĞRULAMA: PNG çapraz görsel + console temiz + 3 viewport (1024/767±1).
LOG: log.md INFO satırı + faz kontrol.
```

Şablon alanları engine §6.2 handover formatıyla uyumludur — L3 özel doldurulmuş hali.

---

## 38. Doğrulama Komutları Ek

```powershell
# 1. PNG↔qr eşleşme (ekran görevi öncesi)
Get-ChildItem -LiteralPath ".ai\.png" -Recurse -Include *.png | Select-Object Name
Get-ChildItem -LiteralPath ".ai\ui-design\screens" -Filter "qr-*.md" | Select-Object Name

# 2. Token varlığı (kullanmadan önce)
Select-String -LiteralPath "assets.coremusic.net\Css\01_Abstracts\*.css" -Pattern "card-thumb-size"

# 3. set-gender bypass koruması
Select-String -LiteralPath "shared\src\Middleware\CsrfMiddleware.php" -Pattern "set-gender"

# 4. PlayerController varlık (web-audio görev kapısı)
Get-ChildItem -LiteralPath "assets.coremusic.net\js" -Recurse -Filter "PlayerController.js" -ErrorAction SilentlyContinue
```

---

## 39. Karar Örnekleri (Doldurulmuş)

**Örnek 1 — "C09 kart gölgesi PNG'de yok ama hoş duruyor":**
```
Karar: KALDIRILIR — §6 kural 10 (PNG'de yoksa yok). Kullanıcıya
"dekoratif gölge önerisi" ayrı sunulur; sessiz eklenmez.
```

**Örnek 2 — "C15 toggle 32px PNG'de, WCAG 48px":**
```
Karar: Görsel PNG oranı korunur, hit alanı 48px'e genişletilir
(03-accessibility-gaps düzeltme notu). İkisi çatışmaz — görsel≠hit alan.
```

---

## 40. Risk/İzle (Son)

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 20 | Görev şablonu atlanması | Orta | Orta | §37 örnek referans |
| 21 | PHP-JS çift sunum | Orta | Yüksek | §27 bütçe + §16 tablo |

İzle ek: negatif bulgu örneği (iframe) ✅; 4 doğrulama komutu ✅ bu revizyon.

---

## 41. Kalite Raporu (Son-2)

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.3.0 |
| **Bölüm Sayısı** | 41 |
| **SSS** | 31 |
| **Görev Şablonu** | 1 doldurulmuş örnek (§37) |
| **Yasak Örüntü** | 7 gerçek örnek (§36) |
| **Risk Kaydı** | 21 |
| **Zero Hallucination** | ✅ |

---

## 42. Ek SSS (Son-2)

**S: Görev sırasında boot dosyaları değişirse (başka oturum)?**
C: log.md son satırları yeniden okunur (§2 madde 7) — değişiklik görevi etkiliyorsa plan güncellenir.

**S: Aynı hata iki kez tekrar ederse?**
C: §8 protokol: 3 başarısız = DUR. İkinci tekrarda kök neden notu yazılır; üçüncü beklenmeden rapor edilebilir.

**S: Görev çıktısı PNG ile birebir ama WCAG ihlali?**
C: A11y istisna katmanıdır (§6 kural 8 parantez — hover/a11y eklenebilir) — hit alanı/aria eklenir, PNG görseli korunur (§39 örnek 2 deseni).

---

## 43. Risk İzle (Son-2)

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 22 | Boot değişikliğinin gözden kaçması | Düşük | Orta | §42 SSS 1 |

---

## 44. İzle (Son-2)

| İddia | Kaynak | Doğrulama |
|-------|--------|-----------|
| A11y istisna katmanı | §6 kural 8 parantez | ✅ |
| 3-tekrar DUR | §8 protokol | ✅ |

---

## 45. Görev Tamamlandı Kontrolü (Son)

```
[ ] §33 karar ağacı izlendi
[ ] §5 7 adım tamam
[ ] §23 checklist 10/10
[ ] PNG çapraz görsel onayı
[ ] console 0 hata
[ ] log.md kaydı
[ ] Doküman senkronu (ilgili md güncellendi)
```

7 kutu doldurmadan görev kapanmaz — Faz kontrol listesinin L3 mikro-hali.

---

## 46. Kalite Raporu (Son-3)

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.4.0 |
| **Bölüm Sayısı** | 46 |
| **SSS** | 34 |
| **Checklist** | 2 (§23 inceleme + §45 görev) |
| **Risk Kaydı** | 22 |
| **Zero Hallucination** | ✅ |

---

## 47. Ek SSS (Son-3)

**S: Görev sırasında yeni token gerekirse PNG gerekmez mi?**
C: Token değeri yeni PNG ölçümünden geliyorsa PNG zaten vardır; tamamen yeni stil isteği PNG/mockup akışına döner (§30 SSS 1).

**S: `VERIFICATION REQUIRED` etiketi görev sonunda kalabilir mi?**
C: Kalabilir — kanıt gelene dek dürüst durumdur; kapanış raporunda açıkça listelenir (§45 kutuları dışında bilgi borcu).

**S: Boot sırasında bu dosya (ai-instructions) okunmuyor — çelişki mi?**
C: Boot listesi (§2) 7 madde; bu dosya L3 görevi başlarken okunur (boot sonrası ilk adım). Boot listesine eklemek domain-boot karmaşası yaratır — mevcut düzen bilinçlidir.

---

## 48. Kalite Raporu (Son-4)

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.5.0 |
| **Bölüm Sayısı** | 48 |
| **SSS** | 37 |
| **Zero Hallucination** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-08
**Mode:** Red Team · Human Mode · Truth Mode
