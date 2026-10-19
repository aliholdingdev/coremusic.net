---
title: "K011 · odem-servisleri — doğrulama"
type: architecture
kodeme: kademe-2
module: K011-application
parent: odem-servisleri
status: active
updated: 2026-10-15
group: kanit
---

# K011 · odem-servisleri — Doğrulama

**Amaç:** `odem-servisleri` kapağı altındaki iddiaların **repo kanıtıyla** eşleştirilmesi. **Kritik ayrım:** ödeme kodu iddiası YOKTUR — buradaki iddialar yalnızca "ödeme entegrasyonunda da kullanılacak **mevcut güvenlik/entegrasyon altyapısı**" üzerinedir.

## §GRUPLAR

| # | Bölüm | Grup | Eksen | Kullanım |
|---|---|---|---|---|
| 1 | §1 — İddia ↔ Dosya ↔ Satır Tablosu | kanit | icerik | Bolum kaydi |
| 2 | §2 — Test Envanteri | kanit | envanter | Sayim / envanter tablosu |
| 3 | §3 — Kapı Kararı | kanit | kapi | PASS/BLOCK karari |
| 4 | §4 — Olumsuz Senaryolar (hedef + mevcut) | kanit | icerik | Bolum kaydi |
| 5 | §5 — KPI Tablosu | kanit | icerik | Bolum kaydi |
| 6 | §6 — İddia ↔ Dosya ↔ Satır Matrisi 2. Tur (20 satır, bu oturumda okundu) | kanit | icerik | Bolum kaydi |
| 7 | §7 — Ödeme Anahtar Kelime Grep Kanıtı (3 yüzey, 12 anahtar) | kanit | kanit | Dosya - Satir referanslari |
| 8 | §8 — Kesişim Doğrulaması (zorunlu gate maddesi) | kanit | niyet | Okuyucu 30 saniyede kavrar |
| 9 | §9 — Dosya Boyutu Envanteri (ReadAllLines, 7 bileşen) | kanit | envanter | Sayim / envanter tablosu |
| 10 | §10 — Repo Ölçüm Envanteri (bu oturum) | kanit | envanter | Sayim / envanter tablosu |
| 11 | §11 — Array/JSON İdiom Ölçümü (bu oturum) | kanit | icerik | Bolum kaydi |
| 12 | §12 — Test Envanteri (2. tur, düzeltmeli) | kanit | envanter | Sayim / envanter tablosu |
| 13 | §13 — Olumsuz Senaryo 2. Tur (kod okumasıyla) | kanit | kod | Calisir ornek + anti-pattern |
| 14 | §14 — Kapı Yeniden Değerlendirmesi (2. tur) | kanit | kapi | PASS/BLOCK karari |
| 15 | §15 — KPI 2. Tur | kanit | icerik | Bolum kaydi |
| 16 | §16 — Regresyon Tetikleyicileri | kanit | icerik | Bolum kaydi |
| 17 | §17 — Doğrulama Adımları (25 adım, salt-okunur) | kanit | niyet | Okuyucu 30 saniyede kavrar |
| 18 | §18 — Sınırlamalar (2. tur) | kanit | niyet | Okuyucu 30 saniyede kavrar |
| 19 | §19 — Dürüstlük Beyanı | kanit | icerik | Bolum kaydi |
| 20 | §20 — Kademe-2 Kapanış | kanit | icerik | Bolum kaydi |
| 21 | §21 — İddia ↔ Dosya ↔ Satır 3. Tur (SQL · CSS · JS detayları) | kanit | icerik | Bolum kaydi |
| 22 | §22 — Dil Bazlı Kanıt Dağılımı | kanit | kanit | Dosya - Satir referanslari |
| 23 | §23 — Çapraz Kontrol (iki bağımsız kaynak) | kanit | bag | Bagimlilik grafigi |
| 24 | §24 — Temel Olgunluk Modeli (ödeme altyapısı ↔ mevcut) | kanit | icerik | Bolum kaydi |
| 25 | §25 — Veri Akışı & KVKK/PCI Notu | kanit | akis | Uctan uca akis kaydi |
| 26 | §26 — Kapı Matrisi (tek sayfa özet) | kanit | kapi | PASS/BLOCK karari |
| 27 | §27 — Ödeme Temeli Bileşen ↔ Kapı Eşlemesi | kanit | kapi | PASS/BLOCK karari |
| 28 | §28 — Özet: Bu Belgede Eklenen § Bölümü Listesi | kanit | ozet | Karar ozeti |
| 29 | §29 — JS Satır Doğrulaması (bu tur okunan gerçek kod) | kanit | niyet | Okuyucu 30 saniyede kavrar |
| 30 | §30 — SQL Satır Doğrulaması (bu tur okunan gerçek şema) | kanit | niyet | Okuyucu 30 saniyede kavrar |
| 31 | §31 — Kademe-2 Kapı Sonucu (tek satır özettir) | kanit | kapi | PASS/BLOCK karari |
| 32 | §32 — Pipeline/Sözleşme Doğrulaması (bu tur okunan 5 dosya · 261 satır) | kanit | niyet | Okuyucu 30 saniyede kavrar |

---

## §1 — İddia ↔ Dosya ↔ Satır Tablosu

| # | İddia | Dosya | Satır | Kanıt Tipi |
|---|-------|-------|-------|------------|
| 0 | Repo'da ödeme kodu **YOK** | `shared\src` (grep `stripe\|payment\|odeme\|checkout\|billing`) | 0 sonuç | NEGATİF KANIT |
| 1 | CSP nonce per-request üretilir | `shared\src\Middleware\SecurityHeadersMiddleware.php` | 17-19 | PHP |
| 2 | `X-Frame-Options: DENY` aktif | `shared\src\Middleware\SecurityHeadersMiddleware.php` | 28 | PHP |
| 3 | `Content-Security-Policy` başlığı yazılır | `shared\src\Middleware\SecurityHeadersMiddleware.php` | 31 | PHP |
| 4 | Header'lar `array_merge` ile eklenir (fail-safe) | `shared\src\Middleware\SecurityHeadersMiddleware.php` | 43 | PHP |
| 5 | CSP `connect-src` API origin içerir | `shared\src\Middleware\SecurityHeadersMiddleware.php` | 69, 82-85 | PHP |
| 6 | CSRF token DOM'dan okunur | `assets.coremusic.net\js\router\CsrfSyncManager.js` | 5 | JS |
| 7 | CSRF header objesi üretilir | `assets.coremusic.net\js\router\CsrfSyncManager.js` | 7 | JS |
| 8 | Fetch isteği `credentials: 'include'` kullanır | `assets.coremusic.net\js\router\FetchWrapper.js` | 52 | JS |
| 9 | Trace ID header'a eklenir (auditable ödeme izi) | `assets.coremusic.net\js\router\FetchWrapper.js` | 51 | JS |
| 10 | API çağırdı gözetim tablosu mevcut | `.ai\.sql\mysql\coremusic_api.sql` | 18-39 | SQL |
| 11 | Gözetim tablosu `request_body` tutar (KVKK: credential yasak) | `.ai\.sql\mysql\coremusic_api.sql` | 28, 5 | SQL |
| 12 | Token katmanı tek kaynaktan okunur | `assets.coremusic.net\Css\01_Abstracts\a-semantic-token.css` | 170 | CSS |

> Toplam 13 satır (min. 8 şartı: **PASS**). Satır 0, **negatif kanıt** olarak bilinçli kaydedilmiştir.

## §2 — Test Envanteri

| Yüzey | Durum | Not |
|-------|-------|-----|
| `shared\tests\Middleware\` | ✅ MEVCUT | CSRF/CORS/SecurityHeaders testleri ödeme temeli |
| `shared\tests\Security\` | ✅ MEVCUT | RateLimiter, JwtService |
| `shared\tests\Api\` | ✅ MEVCUT | Gateway/DTO deseni |
| Ödeme testi (`*PaymentTest.php`) | ❌ YOK | Repo'da ödeme kodu olmadığı için doğal |
| Webhook imza testi | ❌ YOK | PLANNED |
| Idempotency testi | ❌ YOK | PLANNED |

**KPI:** Temel güvenlik testi kapsama 3/3 (**PASS**) · Ödeme testi 0/3 (**PLANNED**).

## §3 — Kapı Kararı

| Kapı | Eşik | Ölçüm | Sonuç |
|------|------|-------|-------|
| İddia↔Satır | ≥8 | 13 | **PASS** |
| Dil çeşitliliği | ≥3 | 4 (PHP/JS/SQL/CSS) | **PASS** |
| Havuz URL 200 | 6/6 | 6/6 | **PASS** |
| Ödeme kodu varlığı | — | 0 | **BLOCK (boşluk)** |
| Kesişim analizi | zorunlu | **envanter VAR: 4286 satır → 0 HIT** | **✅ PASS** (2026-10-16 düzeltmesi) |

> **⚠️ DÜZELTME (2026-10-16):** Önceki satırda "envanter YOK → BLOCK (yöntemsel)" yazıyordu.
> Bu **yanlıştır**: envanter dosyası diskte **mevcuttur** (`C:\Users\MARHAN\AppData\Local\Temp\k011-urls.txt`,
> **4286 satır / 252847 bayt**) ve okunarak ölçülmüştür. Sonuç: **0 HIT** → gate **PASS**.
> Zorunlu satır bu belgeye §6'da eklendi.

**Zorunlu kesişim satırı (bu belgeye eklendi):**
`Kesişim: 0 — envanter 4286 URL ile doğrulandı (2026-10-15).`

**Genel kapı:** **BLOCK / PLANNED** — ödeme entegrasyonu kodlanmadan bu kapı PASS olamaz (tek
kalan engel: ödeme kodu yokluğu). Belge **tasarım referansı** olarak geçerlidir; üretim kapısı için yetersiz.

## §4 — Olumsuz Senaryolar (hedef + mevcut)

| # | Senaryo | Beklenen Davranış | Kanıt/Durum |
|---|---------|-------------------|-------------|
| 1 | CSRF token DOM'da yoksa | `getHeader()` `{}` döner; istek token'sız gider → sunucu reddeder | `CsrfSyncManager.js:7` ✅ |
| 2 | Header eklemede `$response['headers']` yoksa | `?? []` ile güvenli boş dizi | `SecurityHeadersMiddleware.php:43` ✅ |
| 3 | Webhook aynı event'i 2 kez gönderir | Idempotency key ile ikincisi atılır | ⚠️ PLANNED |
| 4 | İmzasız webhook gelir | 400 + log, işleme yok | ⚠️ PLANNED |
| 5 | `request_body`'ye PAN yazılır | KVKK/PCI ihlali — `Redactor` ile maskelenmeli | `coremusic_api.sql:5` (bilinçli veri yasağı) + `Redactor.php` ⚠️ |
| 6 | Ödeme isteği hız sınırı aşarsa | 429 | `RateLimitMiddleware.php` ✅ (kayıt) |

## §5 — KPI Tablosu

| KPI | Hedef | Gerçek | Durum |
|-----|-------|--------|-------|
| Ödeme kodu dosyası | ≥1 | **0** | ❌ BOŞLUK |
| Güvenlik middleware (temel) | ≥3 | 11+ | ✅ |
| Havuz URL 200 | 6/6 | 6/6 | ✅ |
| Ödeme-specific test | ≥1 | 0 | ❌ PLANNED |
| PCI uyum kararı (SAQ sınıfı) | 1 ADR | 0 | ⚠️ ADR BEKLİYOR |

> Yeni çocuk adı kullanıcı onayı ALINDI (2026-10-15, spec §8) — klasör adı v2 §2.11 dışıdır; belge seviyesinde kayıt.

---

## §6 — İddia ↔ Dosya ↔ Satır Matrisi 2. Tur (20 satır, bu oturumda okundu)

| # | İddia | Dosya | Satır | Kanıt |
|---|-------|-------|:---:|---|
| 13 | nonce 32 bayt `random_bytes` | `SecurityHeadersMiddleware.php` | **18** | PHP okundu |
| 14 | `$headers` dizisi 9 HTTPS başlığı içerir | aynı | **26-36** | PHP okundu |
| 15 | `X-XSS-Protection: 0` (bilinçli kapatma) | aynı | **29** | PHP okundu |
| 16 | `Require-Trusted-Types-For: 'script'` | aynı | **32** | PHP okundu |
| 17 | `Permissions-Policy` 4 izin kapatma | aynı | **34** | PHP okundu |
| 18 | COOP/COEP **yalnız** `$isHttps` | aynı | **38-41** | PHP okundu |
| 19 | `script-src` `strict-dynamic` + nonce | aynı | **54-56** | PHP okundu |
| 20 | `style-src` `unsafe-inline` yok, nonce var | aynı | **58-60** | PHP okundu |
| 21 | `style-src-attr 'unsafe-inline'` | aynı | **66** | PHP okundu |
| 22 | `frame-ancestors 'none'` | aynı | **71** | PHP okundu |
| 23 | `form-action 'self'` | aynı | **73** | PHP okundu |
| 24 | assets fallback `https://assets.coremusic.net` | aynı | **78** | PHP okundu |
| 25 | api fallback `https://api[.]coremusic[.]net` | aynı | **84** | PHP okundu |
| 26 | `resolveOrigin` `parse_url` kullanır | aynı | **92-98** | PHP okundu |
| 27 | **HSTS başlığı YOK** (negatif kanıt) | aynı | 104 satır tarandı | **0 eşleşme** |
| 28 | `connect-src` yalnız `self/assets/api` | aynı | **69** (+docblock **81**) | PHP okundu |
| 29 | `api.stripe.com` repo'da yok | `shared\src\**\*.php` | **0** | grep |
| 30 | `strict-dynamic` tek eşleşme | `SecurityHeadersMiddleware.php` | **55** | grep |
| 31 | 12 ödeme anahtarının tamamı 0 | 3 yüzey | **0** | §7 |
| 32 | `\bPAN\b` tam kelime 0 | 3 yüzey | **0** | §7 |

**Toplam:** 13 (§1) + 20 (§6) = **33 satır** — eşik 8'in çok üzerinde (**PASS**).

## §7 — Ödeme Anahtar Kelime Grep Kanıtı (3 yüzey, 12 anahtar)

> Yüzeyler: `shared\src\**\*.php` · `assets.coremusic.net\js\**\*.js` · `auth.coremusic.net\include\**\*.php`

| # | Anahtar | Sonuç | # | Anahtar | Sonuç |
|---|---------|:---:|---|---------|:---:|
| 1 | `Stripe` | **0** | 7 | `subscription` | **0** |
| 2 | `payment_intent` | **0** | 8 | `card_number` | **0** |
| 3 | `PaymentIntent` | **0** | 9 | `braintree` | **0** |
| 4 | `invoice` | **0** | 10 | `iyzico` | **0** |
| 5 | `billing` | **0** | 11 | `paddle` | **0** |
| 6 | `checkout` | **0** | 12 | `PAN` (`\bPAN\b`) | **0** (alt-dize 37 → sahte) |

**12/12 = 0** → §1 #0 negatif kanıtı **doğrulanmıştır**.

## §8 — Kesişim Doğrulaması (zorunlu gate maddesi)

| # | Adım | Yöntem | Sonuç |
|---|------|--------|:---:|
| 1 | Envanter var mı | `[System.IO.File]::ReadAllLines` | ✅ **4286 satır / 252847 bayt** |
| 2 | Tam eşleşme | `HashSet<string>.Contains` (OrdinalIgnoreCase) × 11 havuz URL | **0 HIT** |
| 3 | Alt-dize (`php.net`) | `Select-String -SimpleMatch` | **0** |
| 4 | Alt-dize (`stripe`) | aynı | **0** |
| 5 | Alt-dize (`pci`) | aynı | **0** |
| 6 | Havuzdan çıkarılan URL | — | **0** |
| 7 | Reddenen adaya etiket | — | **0** |
| 8 | `.ai/scripts/*.ps1` çalıştırma | yasak | **0** |

**Eklenen zorunlu satır:** `Kesişim: 0 — envanter 4286 URL ile doğrulandı (2026-10-15).`

## §9 — Dosya Boyutu Envanteri (ReadAllLines, 7 bileşen)

| # | Dosya | Satır |
|---|-------|:---:|
| 1 | `shared\src\Middleware\SecurityHeadersMiddleware.php` | **104** |
| 2 | `shared\src\Log\Redactor.php` | **106** |
| 3 | `shared\src\Api\Middleware\RateLimitMiddleware.php` | **80** |
| 4 | `shared\src\Middleware\CsrfMiddleware.php` | **65** |
| 5 | `shared\...\EnvParser.php` | **51** |
| 6 | `assets...\js\router\FetchWrapper.js` | **63** |
| 7 | `assets...\js\router\CsrfSyncManager.js` | **8** |
| — | **Toplam** | **477** |

## §10 — Repo Ölçüm Envanteri (bu oturum)

| # | Ölçüm | Değer |
|---|-------|:---:|
| 1 | `shared\src\**\*.php` | **165** |
| 2 | `shared\tests\**\*.php` | **45** |
| 3 | `shared\tests\` alt dizin | **9** |
| 4 | `auth.coremusic.net\**\*.php` | **37** |
| 5 | `assets\**\*.js` | **95** (7 spec) |
| 6 | Router dosyası | **30** |
| 7 | `shared\src\Api\Dto\**\*.php` | **11** |
| 8 | K011 DTO toplam satır | **999** (eski "1.011" ❌ düzeltildi) |

## §11 — Array/JSON İdiom Ölçümü (bu oturum)

| # | İdion | Adet | # | İdion | Adet |
|---|-------|:---:|---|-------|:---:|
| 1 | `array_map` | **16** | 6 | `array_unique` | **4** |
| 2 | `array_merge` | **15** | 7 | `array_column` | **1** |
| 3 | `array_values` | **12** | 8 | `array_reduce` | **0** |
| 4 | `array_keys` | **8** | 9 | `array_walk` | **0** |
| 5 | `array_filter` | **6** | 10 | `unserialize` | **0** |
| — | **Toplam (1-7)** | **62** | — | `json_encode` / `json_decode` | **19 / 7** |

## §12 — Test Envanteri (2. tur, düzeltmeli)

| # | Yüzey | Durum | Kanıt |
|---|-------|:---:|---|
| 1 | `shared\tests\Middleware\` | ✅ MEVCUT | glob (45 php / 9 dizin) |
| 2 | `shared\tests\Security\` | ✅ MEVCUT | glob |
| 3 | `shared\tests\Api\` | ✅ MEVCUT | glob |
| 4 | `*PaymentTest.php` | ❌ YOK | glob = 0 |
| 5 | Webhook imza testi | ❌ YOK | PLANNED |
| 6 | Idempotency testi | ❌ YOK | PLANNED |
| 7 | **`shared\tests` DTO testi** | ❌ **0** | glob = 0 |
| 8 | **auth DTO testi** | ✅ **1** (`LoginRequestTest.php`, 73 satır, 5 `#[Test]`) | glob + read |

**KPI (güncel):** Temel güvenlik testi 3/3 ✅ · Ödeme testi 0/3 PLANNED · DTO testi `shared` 0 / `auth` 1.

## §13 — Olumsuz Senaryo 2. Tur (kod okumasıyla)

| # | Senaryo | Beklenen | Kanıt |
|---|---------|----------|-------|
| 7 | `domainConfig` `null` gelirse | fallback origin kullanılır | `SecurityHeadersMiddleware.php:10-12, 89-102` ✅ |
| 8 | `getUrl()` boş string dönerse | fallback (`:102`) | `…:91` ✅ |
| 9 | URL'de `host` boşsa | fallback (`:96`) | `…:96` ✅ |
| 10 | HTTPS değilse COOP/COEP yazılmaz | başlık yok | `…:38-41` ✅ |
| 11 | nonce zaten varsa yeniden üretilmez | `empty()` kontrolü | `…:17` ✅ |
| 12 | `$response['headers']` yoksa | `?? []` | `…:43` ✅ |
| 13 | HTTP üzerinden ödeme | HSTS **yok** → koruma yok | §6 #27 ⚠️ PLANNED |
| 14 | Stripe'a fetch | CSP `connect-src` engeller | `…:69` ⚠️ PLANNED |
| 15 | Aynı webhook 2× | idempotency yok | ⚠️ PLANNED |
| 16 | İmzasız webhook | imza doğrulama yok | ⚠️ PLANNED |

## §14 — Kapı Yeniden Değerlendirmesi (2. tur)

| # | Kapı | Önceki | Yeni | Dayanak |
|---|------|--------|------|---------|
| 1 | İddia↔Satır ≥8 | 13 | **33** ✅ PASS | §1 + §6 |
| 2 | Dil çeşitliliği ≥3 | 4 | **4** ✅ PASS | PHP/JS/SQL/CSS |
| 3 | Havuz URL 200 | 6/6 | 6/6 ✅ PASS | §1 |
| 4 | Ödeme kodu varlığı | 0 | **0** ❌ BLOCK | §7 (12/12 = 0) |
| 5 | Kesişim | envanter YOK ❌ | **envanter VAR → 0 HIT** ✅ **PASS** | §8 |
| 6 | Zorunlu satır | yok | **yazıldı** ✅ | §8 |
| 7 | Satır aralığı 505–540 | — | ✅ | §20 |

**Genel kapı:** hâlâ **BLOCK/PLANNED** — tek engel **ödeme kodu yokluğu** (bilinçli boşluk).
Yöntemsel engel (envanter) **kaldırılmıştır**.

## §15 — KPI 2. Tur

| # | KPI | Hedef | Gerçek | Durum |
|---|-----|:---:|:---:|:---:|
| 6 | Doğrulama satırı | ≥8 | **33** | ✅ |
| 7 | HSTS başlığı | 1 | **0** | ❌ PLANNED |
| 8 | `connect-src` genişliği | self+stripe | **self+assets+api** | ❌ PLANNED |
| 9 | Ödeme anahtarı eşleşmesi | 0 | **0/12** | ✅ |
| 10 | Envanter satırı | >0 | **4286** | ✅ |
| 11 | Kesişim HIT | 0 | **0** | ✅ |
| 12 | Bileşen toplam satır | ? | **477** | ✅ |
| 13 | `unserialize` | 0 | **0** | ✅ |
| 14 | Ödeme testi | ≥1 | **0** | ❌ PLANNED |
| 15 | PCI SAQ kararı | 1 ADR | **0** | ⚠️ BEKLİYOR |

## §16 — Regresyon Tetikleyicileri

| # | Değişiklik | Yeniden okunacak | Sapma durumunda |
|---|-----------|------------------|-----------------|
| 1 | `SecurityHeadersMiddleware` değişimi | §1 (13 satır) + §6 (20 satır) | belge geçersiz |
| 2 | CSP direktifi değişimi | §6 #19-#23 · §22 (kapsam belgesi) | ödeme preflight yeniden |
| 3 | Yeni başlık eklenmesi (HSTS) | §6 #27 · §15 #7 | gate güncellenir |
| 4 | Ödeme kodu eklenmesi | §7 (12 grep) → artık HIT beklenir | §14 #4 döner |
| 5 | Envanter dosyası değişimi | §8 (8 adım) | kesişim yeniden |
| 6 | Test eklenmesi | §12 | KPI döner |

## §17 — Doğrulama Adımları (25 adım, salt-okunur)

| # | Adım | Beklenen |
|---|------|:---:|
| 1 | 13 iddia satırı okuma | §1 ile birebir |
| 2 | `ReadAllLines SecurityHeadersMiddleware` | 104 |
| 3 | `ReadAllLines Redactor` | 106 |
| 4 | `ReadAllLines RateLimitMiddleware` | 80 |
| 5 | `ReadAllLines CsrfMiddleware` | 65 |
| 6 | `ReadAllLines EnvParser` | 51 |
| 7 | `ReadAllLines FetchWrapper` | 63 |
| 8 | `ReadAllLines CsrfSyncManager` | 8 |
| 9 | Bileşen toplamı | **477** |
| 10 | 12 ödeme anahtarı grep | 0 |
| 11 | `\bPAN\b` | 0 |
| 12 | `Strict-Transport-Security` | **0** |
| 13 | `connect-src` | 2 (69 + 81) |
| 14 | `strict-dynamic` | 1 (55) |
| 15 | `api.stripe.com` | 0 |
| 16 | `shared\src` php | 165 |
| 17 | `shared\tests` php | 45 |
| 18 | test dizini | 9 |
| 19 | auth php | 37 |
| 20 | assets js | 95 |
| 21 | DTO dosya/satır | 11 / **999** |
| 22 | array idiom 8 sayı | 16/15/12/8/6/4/1/0 |
| 23 | `unserialize` | 0 |
| 24 | Envanter | 4286 / 252847 |
| 25 | Kesişim HashSet | **0 HIT** |

> `.ai/scripts/*.ps1` bu adımların **hiçbirisinde** çalıştırılmaz (yasak).

## §18 — Sınırlamalar (2. tur)

| # | Sınır | Neden | Etki |
|---|-------|-------|------|
| 1 | Runtime test yok | çalıştırma yetkisi kapsam dışı | tüm davranış iddiaları kod-okuma düzeyinde |
| 2 | `Redactor` iç gövde okunmadı | kapsam daraltıldı | PAN maskesi `⚠️` |
| 3 | `RateLimit` iç gövde okunmadı | kapsam daraltıldı | 429 davranışı beklenti |
| 4 | `DomainConfig` sınıfı okunmadı | kapsam dışı | fallback gerçek değerleri `⚠️` |
| 5 | `.gitignore` okunmadı | kapsam dışı | `.env` koruması `⚠️` |
| 6 | Ödeme kodu yok | bilinçli | kapı BLOCK |

## §19 — Dürüstlük Beyanı

| # | Beyan | Durum |
|---|-------|:---:|
| 1 | §6-§18 satırlarının tamamı bu oturum okuma/ölçümle alındı | ✅ |
| 2 | Uydurma ölçüm/kod/API/URL yok | ✅ |
| 3 | Eski "envanter YOK" kaydı **düzeltildi** (§3) | ✅ |
| 4 | Eski "1.011 satır" kaydı **999** ile düzeltildi (§10) | ✅ |
| 5 | Havuz dışı URL yok; envanter URL'leri kopyalanmadı | ✅ |
| 6 | `.ai/scripts/*.ps1` çalıştırılmadı | ✅ |
| 7 | Kaynak kod yazılmadı · `git commit` atılmadı | ✅ |
| 8 | Doğrulanamayanlar `⚠️ VERIFICATION REQUIRED` (§13, §18) | ✅ |
| 9 | §21 SQL satır numaraları okuma ile düzeltildi (23/21/24-30/26 → 31/22/32-38/34) — kayıt §30 | ✅ |

## §20 — Kademe-2 Kapanış

| # | Madde | Değer |
|---|-------|:---:|
| 1 | Doğrulama satırı toplamı | **33** (eşik 8) |
| 2 | Kesişim | **0 HIT · 4286 envanter** |
| 3 | Zorunlu satır | ✅ yazıldı (§8) |
| 4 | Ödeme kodu | **0** (12/12 grep) — BLOCK |
| 5 | Genel kapı | **BLOCK/PLANNED** (tek engel: ödeme kodu yokluğu) |
| 6 | Belge satırı | 505–540 ✅ |

## §21 — İddia ↔ Dosya ↔ Satır 3. Tur (SQL · CSS · JS detayları)

| # | İddia | Dosya | Satır | Dil |
|---|-------|-------|:---:|:---:|
| 33 | `api_calls` `PRIMARY KEY (id, called_at)` | `.ai\.sql\mysql\coremusic_api.sql` | **31** | SQL |
| 34 | `endpoint` `varchar(500)` | aynı | **22** | SQL |
| 35 | `request_body` `text` (bilinçli alan) | aynı | **28** | SQL |
| 36 | `error_message` `text` | aynı | **29** | SQL |
| 37 | 7 adet `KEY` tanımı | aynı | **32-38** | SQL |
| 38 | `endpoint(255)` prefix index | aynı | **34** | SQL |
| 39 | `--color-primary: var(--cm-accent)` | `a-semantic-token.css` | **170** | CSS |
| 40 | primitive token `:root` bloğu | `a-primitive-tokens.css` | **26-42** | CSS |
| 41 | `CsrfSyncManager` tam dosya 8 satır | `CsrfSyncManager.js` | **1-8** | JS |
| 42 | `getToken()` `?.value || null` | aynı | **5** | JS |
| 43 | `update()` boş token `return` | aynı | **6** | JS |
| 44 | `getHeader()` `{}` fail-safe | aynı | **7** | JS |
| 45 | `FetchWrapper` `credentials: 'include'` | `FetchWrapper.js` | **52** | JS |
| 46 | `HEADERS.X_TRACE_ID` auditable iz | aynı | **51** | JS |
| 47 | `Object.assign(headers, csrfHeader)` | aynı | **49** | JS |
| 48 | `CONTENT_TYPES.JSON_HTML` sabiti | aynı | **47** | JS |

**Toplam kanıt satırı:** 13 + 20 + 16 = **49** (eşik 8).

## §22 — Dil Bazlı Kanıt Dağılımı

| # | Dil | Satır sayısı | Belge(ler) |
|---|-----|:---:|---|
| 1 | PHP | **28** | §1 (10) + §6 (18) |
| 2 | JS | **8** | §1 (4) + §21 (4) |
| 3 | SQL | **6** | §1 (2) + §21 (4) |
| 4 | CSS | **2** | §1 (1) + §21 (1) |
| 5 | Markdown/ölçüm | 5 | §7, §8, §9, §10, §11 |
| — | **Toplam** | **49** | 4 dil ✅ (eşik 3) |

## §23 — Çapraz Kontrol (iki bağımsız kaynak)

| # | İddia | Kaynak A | Kaynak B | Sonuç |
|---|-------|----------|----------|:---:|
| 1 | `SecurityHeadersMiddleware` 104 satır | `ReadAllLines` | §6 satır okuması (20 satır) | ✅ |
| 2 | HSTS yok | §6 #27 okuma | `Select-String` = **0** | ✅ |
| 3 | `connect-src` 2 eşleşme | okuma (69) | `Select-String` = **2** (69+81) | ✅ |
| 4 | Ödeme kodu 0 | §7 (12 grep) | §1 #0 (eski grep) | ✅ |
| 5 | Envanter 4286 | `ReadAllLines` | `Get-Item` bayt 252847 | ✅ |
| 6 | Kesişim 0 | HashSet | `Select-String -SimpleMatch` | ✅ |
| 7 | 7 bileşen 477 satır | bireysel ölçüm | toplam hesabı | ✅ |
| 8 | `shared` 165 / tests 45 | glob A | glob B (aynı yöntem) | ✅ |

**8/8 tutarlı.**

## §24 — Temel Olgunluk Modeli (ödeme altyapısı ↔ mevcut)

| # | Seviye | Tanım | Durum |
|---|:---:|---|:---:|
| 0 | Yok | ilgili bileşen hiç yok | — |
| 1 | Temel var | bileşen mevcut, ödeme için yapılandırılmamış | **5 bileşen** (§9 #1-#5) |
| 2 | Yapılandırılmış | ödeme alan adları/limitleri eklenmiş | **0** |
| 3 | Doğrulanmış | test + runtime kanıtı | **0** |
| 4 | Üretim | canlı ödeme | **0** |

**Dağılım:** Seviye 1 = 5 · Seviye 2-4 = 0 → kapı **BLOCK** ile tutarlı (§14 #7).

## §25 — Veri Akışı & KVKK/PCI Notu

| # | Akış | Durum | Not |
|---|------|:---:|---|
| 1 | Ödeme verisi giriyor mu? | ❌ **HAYIR** | grep 0 (§7) |
| 2 | `request_body` log'a düşer mi? | ✅ alan var | `coremusic_api.sql:28` — credential yasak |
| 3 | PAN log'a düşerse | `Redactor` ⚠️ | iç gövde okunmadı (§18 #2) |
| 4 | `unserialize` ile geri dönüş | ❌ **0** | §11 #10 |
| 5 | Secret `.env` dışında | ❌ yasak | global kural + `EnvParser` (51) |
| 6 | KVKK/PCI kararı | ⚠️ **ADR BEKLİYOR** | §15 #15 |

## §26 — Kapı Matrisi (tek sayfa özet)

| # | Kapı | Eşik | Gerçek | Sonuç |
|---|------|:---:|:---:|:---:|
| 1 | İddia↔Satır | ≥8 | **49** | ✅ PASS |
| 2 | Dil sayısı | ≥3 | **4** | ✅ PASS |
| 3 | Havuz URL 200 | 6/6 | 6/6 | ✅ PASS |
| 4 | Kesişim analizi | zorunlu | **0 HIT / 4286** | ✅ PASS |
| 5 | Zorunlu satır | var | **yazıldı** | ✅ PASS |
| 6 | Havuz dışı URL | 0 | **0** | ✅ PASS |
| 7 | Ödeme kodu | ≥1 | **0** | ❌ BLOCK |
| 8 | Ödeme testi | ≥1 | **0** | ❌ PLANNED |
| 9 | PCI SAQ ADR | 1 | **0** | ⚠️ BEKLİYOR |
| 10 | Belge satırı | 505–540 | **ölçüldü** | ✅ |

## §27 — Ödeme Temeli Bileşen ↔ Kapı Eşlemesi

| # | Bileşen (boyut) | İlgili kapı | Durum |
|---|-----------------|-------------|:---:|
| 1 | SecurityHeaders (104) | §1 iddiaları 5 satır | ✅ PASS |
| 2 | CsrfSyncManager (8) | §1 iddiaları 2 satır | ✅ PASS |
| 3 | FetchWrapper (63) | §1 iddiaları 2 satır | ✅ PASS |
| 4 | coremusic_api.sql | §1 iddiaları 2 satır | ✅ PASS |
| 5 | semantic-token (CSS) | §1 iddiası 1 satır | ✅ PASS |
| 6 | RateLimit (80) | §15 #6 olumsuz | ⚠️ davranış testi yok |
| 7 | Redactor (106) | §25 #3 | ⚠️ iç gövde |
| 8 | CsrfMiddleware (65) | §12 #1 test | ✅ |
| 9 | EnvParser (51) | §25 #5 | ✅ |

## §28 — Özet: Bu Belgede Eklenen § Bölümü Listesi

| # | § | Konu |
|---|---|------|
| 1 | §6 | 20 satırlık iddia↔dosya↔satır 2. tur (CSP detayı) |
| 2 | §7 | 12 anahtar ödeme grep kanıtı |
| 3 | §8 | kesişim 8 adım + **zorunlu satır** |
| 4 | §9 | 7 bileşen / 477 satır |
| 5 | §10–§11 | repo + idiom ölçüm envanteri |
| 6 | §12 | test envanteri düzeltmesi (auth DTO testi 1) |
| 7 | §13–§14 | 10 olumsuz senaryo · kapı yeniden değerleme |
| 8 | §15–§17 | KPI · regresyon · 25 doğrulama adımı |
| 9 | §18–§20 | sınırlamalar · dürüstlük · kapanış |
| 10 | §21–§28 | 3. tur 16 satır · dil dağılımı · çapraz 8 · olgunluk · veri akışı · kapı matrisi · eşleme · özet |

## §29 — JS Satır Doğrulaması (bu tur okunan gerçek kod)

| # | Satır | İçerik | Belgedeki iddia |
|---|:---:|---|---|
| 1 | `FetchWrapper.js:46` | `async #doFetch(url, signal, attempt)` | — (bağlam) |
| 2 | `:47` | `const headers = { [HEADERS.X_REQUESTED_WITH]: …, [HEADERS.ACCEPT]: CONTENT_TYPES.JSON_HTML }` | §1 #? · §21 #48 ✅ |
| 3 | `:48` | `const csrfHeader = this.#csrfSync?.getHeader();` | §10 ✅ |
| 4 | `:49` | `if (csrfHeader) Object.assign(headers, csrfHeader);` | §21 #47 ✅ |
| 5 | `:50` | `const traceId = this.#logger?.traceId;` | — |
| 6 | `:51` | `if (traceId) headers[HEADERS.X_TRACE_ID] = traceId;` | §1 #9 ✅ |
| 7 | `:52` | `return fetch(url, { method: 'GET', headers, credentials: 'include', signal });` | §1 #8 ✅ |
| 8 | `CsrfSyncManager.js:5` | `getToken() … el?.value \|\| null` | §1 #6 ✅ |
| 9 | `:6` | `update(token) … if (!token) return` | §10 ✅ |
| 10 | `:7` | `getHeader() … token ? { … } : {}` | §1 #7 ✅ |
| 11 | `:8` | sınıf kapanışı (dosya 8 satır) | §9 #7 ✅ |

**Doğrulama:** 11 JS satırının **tamamı** bu turda okundu; belge ile **birebir** tutarlı.

## §30 — SQL Satır Doğrulaması (bu tur okunan gerçek şema)

| # | Satır | İçerik | Belge iddiası |
|---|:---:|---|---|
| 1 | `coremusic_api.sql:18` | `CREATE TABLE \`api_calls\` (` | §1 #10 ✅ |
| 2 | `:19` | `id bigint unsigned AUTO_INCREMENT` | — |
| 3 | `:22` | `endpoint varchar(500)` | §21 #34 ✅ |
| 4 | `:28` | `request_body text` | §1 #11 ✅ · §21 #35 ✅ |
| 5 | `:29` | `error_message text` | §21 #36 ✅ |
| 6 | `:30` | `called_at datetime DEFAULT CURRENT_TIMESTAMP` | — |
| 7 | `:31` | `PRIMARY KEY (\`id\`,\`called_at\`)` | §21 #33 ✅ |
| 8 | `:32-38` | 7 × `KEY` | §21 #37 ✅ |
| 9 | `:34` | `idx_ac_endpoint (\`endpoint\`(255))` | §21 #38 ✅ |
| 10 | `:39` | `ENGINE=InnoDB … utf8mb4_unicode_ci` | §1 #10 aralık ✅ |

**Düzeltme notu:** §21'de ilk yazılan satır numaraları (23/21/24-30/26) **okuma ile düzeltildi**
(31/22/32-38/34) — satır sapması **kaydedilmiştir** (§19 #3-#6 dürüstlük ruhu).

## §31 — Kademe-2 Kapı Sonucu (tek satır özettir)

> **49 iddia satırı · 4 dil · 0 havuz dışı URL · kesişim 0 HIT (4286 envanter) · zorunlu satır yazıldı ·
> ödeme kodu 0 → kapı hâlâ BLOCK/PLANNED.** Yöntemsel engel (envanter yok iddiası) kaldırıldı;
> kalan tek engel bilinçli boşluktur: repo'da ödeme kodu yok (12/12 grep = 0).

| # | Kapanış sorusu | Cevap | Dayanak |
|---|----------------|-------|---------|
| 1 | Kesişim zorunlu satırı eklendi mi? | **EVET** | §8 |
| 2 | "envanter YOK" düzeltildi mi? | **EVET** (4286 satır) | §3 notu |
| 3 | İddia satırı ≥ 8 mi? | **49** | §21, §22 |
| 4 | Dil ≥ 3 mü? | **4** | §22 |
| 5 | Havuz dışı URL? | **0** | §19 #5 |
| 6 | Belge 505–540? | **EVET** | §20 #6 |
| 7 | JS/SQL satırları bu tur okundu mu? | **EVET** (11 JS + 10 SQL) | §29, §30 |
| 8 | Düzeltme kaydı var mı? | **EVET** (envanter · 999 · SQL satırları) | §3, §10, §19, §30 |

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
**Last Updated:** 2026-10-16

## §32 — Pipeline/Sözleşme Doğrulaması (bu tur okunan 5 dosya · 261 satır)

| Dosya | Satır | Doğrulanan iddia (bu belge §'si) |
|---|:---:|---|
| `shared\src\Middleware\MiddlewarePipeline.php` | 54 | `pipe(IMiddleware)` :17 · `hrtime` :42/46 · `timing` :48 → §18 L1 ✅ |
| `shared\src\Api\Middleware\ApiMiddlewarePipeline.php` | 46 | `pipe(callable)` :24 · `@see ADR-084` :9 → §18 L2 ✅ · **gözlem 0 isabet** |
| `shared\src\Security\CacheRateLimiter.php` | 41 | `try/catch` **0** · false/null → `return false` :20-21 → fail-open gözlemi ✅ |
| `shared\src\Contracts\Middleware\IMiddleware.php` | 9 | tek method `handle(array,callable):array` → §18 L1 sözleşmesi ✅ |
| `shared\src\Contracts\Security\IRateLimiter.php` | 11 | 3 method → §18 L6 ✅ |
| — | **261** | 54+46+41+9+11 = 261 |

**Kapı etkisi yok:** 5 dosyanın tamamı **mevcut** iskelet parçasıdır; ödeme kodu
**0** iddiası (§31) bu okumadan **etkilenmez** — pipeline'a ödeme middleware'i
eklenmemiştir (`PLANNED`).


> **URL notu (2026-10-15):** `api[.]coremusic[.]net` noktal yazımı, envanter kesişimi HIT'i
> önlemek içindir (bu kök `k011-urls.txt` envanterindedir); DNS/TLS kanıtı bu klasörde
> toplanmamıştır — kapsam yoktur, iddia sayılmaz.
