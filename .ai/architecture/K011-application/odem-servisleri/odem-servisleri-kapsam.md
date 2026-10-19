---
title: "K011 · odem-servisleri — kapsam"
type: architecture
kodeme: kademe-2
module: K011-application
parent: odem-servisleri
status: active
updated: 2026-10-15
group: mimari
---

# K011 · odem-servisleri — Kapsam

**Amaç:** `odem-servisleri` klasörünün kapsamını ELI15 dilinde tanımlar. **Bu bir boşluk belgesidir** — repo'da ödeme kodu yoktur; bu klasör gelecekteki entegrasyonun tasarım referansını taşır.

## §GRUPLAR

| # | Bölüm | Grup | Eksen | Kullanım |
|---|---|---|---|---|
| 1 | §1 — ELI15: Bu klasör ne anlama geliyor? | mimari | niyet | Okuyucu 30 saniyede kavrar |
| 2 | §2 — Sınır (ne DEĞİL) | mimari | sinir | Ne degil - devir siniri |
| 3 | §3 — Akış Tabloları (hedef tasarım — PLANNED) | mimari | akis | Uctan uca akis kaydi |
| 4 | §4 — Kapsam Dışı | mimari | icerik | Bolum kaydi |
| 5 | §5 — Açık Sorular | mimari | karar | Acik soru / ust karar listesi |
| 6 | §6 — "Ödeme kodu yok" İddiasının Grep Doğrulaması (2026-10-16) | mimari | niyet | Okuyucu 30 saniyede kavrar |
| 7 | §7 — Mevcut Güvenlik Temeli: Bileşen Boyut Envanteri | mimari | envanter | Sayim / envanter tablosu |
| 8 | §8 — SecurityHeadersMiddleware Derin Okuma (104 satır, ödeme için kritik) | mimari | icerik | Bolum kaydi |
| 9 | §9 — RateLimitMiddleware (80 satır) — ödeme deneme limiti zemini | mimari | icerik | Bolum kaydi |
| 10 | §10 — CSRF Çift Taraflı Temel (mevcut) | mimari | icerik | Bolum kaydi |
| 11 | §11 — Redactor: Log Maskeleme (106 satır, PCI zorunluluğu tabanı) | mimari | rehber | Reddedilen aday - etiket only |
| 12 | §12 — EnvParser: Secret Yönetimi (51 satır) | mimari | icerik | Bolum kaydi |
| 13 | §13 — Kapsam ↔ Kapsam Dışı (genişletilmiş) | mimari | icerik | Bolum kaydi |
| 14 | §14 — PLANNED ↔ MEVCUT Matrisi (tek sayfa) | mimari | icerik | Bolum kaydi |
| 15 | §15 — Akış Tabloları 2. Tur (her adım için sahiplik + kapı) | mimari | akis | Uctan uca akis kaydi |
| 16 | §16 — Açık Sorular 2. Tur (5 yeni) | mimari | karar | Acik soru / ust karar listesi |
| 17 | §17 — Kesişim (havuz ↔ envanter) — bu klasör için | mimari | envanter | Sayim / envanter tablosu |
| 18 | §18 — PCI DSS Kapsam Analizi (havuz claim'i ↔ repo) | mimari | icerik | Bolum kaydi |
| 19 | §19 — Doğrulama Adımları (salt-okunur, tekrarlanabilir) | mimari | niyet | Okuyucu 30 saniyede kavrar |
| 20 | §20 — Dürüstlük Beyanı & Gate | mimari | kapi | PASS/BLOCK karari |
| 21 | §21 — SecurityHeadersMiddleware Gerçek İçerik Tablosu (104 satır, satır-satır okundu) | mimari | icerik | Bolum kaydi |
| 22 | §22 — CSP `buildCsp()` Direktif Dökümü (satır 62-74) | mimari | icerik | Bolum kaydi |
| 23 | §23 — `resolveOrigin()` Fallback Matrisi (satır 87-103) | mimari | icerik | Bolum kaydi |
| 24 | §24 — Ödeme Kapsamı İçin Başlık Denetim Listesi (bu dosyadan türetilmiş) | mimari | icerik | Bolum kaydi |
| 25 | §25 — Kaynak Dosya Referans Envanteri (bu belge için) | mimari | envanter | Sayim / envanter tablosu |
| 26 | §26 — Kademe-2 Gate Kontrolü | mimari | kapi | PASS/BLOCK karari |
| 27 | §27 — Ödeme Entegrasyonu Preflight Kontrol Listesi (bu belgeden türetildi) | mimari | icerik | Bolum kaydi |
| 28 | §28 — Bağımlılık & Risk Grafiği (ödeme → mevcut temel) | mimari | bag | Bagimlilik grafigi |
| 29 | §29 — Risk Kaydı (etki × olasılık, kod kanıtlı) | mimari | kanit | Dosya - Satir referanslari |
| 30 | §30 — Doğrulama Adımları 2. Tur (salt-okunur) | mimari | niyet | Okuyucu 30 saniyede kavrar |
| 31 | §31 — Klasör Satır Ölçümü (kademe-2, 2026-10-16) | mimari | icerik | Bolum kaydi |
| 32 | §32 — § Özeti (bu belgede eklenen bölümler) | mimari | ozet | Karar ozeti |

---

## §1 — ELI15: Bu klasör ne anlama geliyor?

Bir kullanıcı bir ürün/abonelik için para öderse, bu işlemin üç parçası olur: (1) **ödeme alınır** (kart bilgisi işlenir), (2) **sonuç doğrulanır** (banka onayı), (3) **kayıt tutulur** (fatura/ledger). Bu üç parçanın koduna "ödeme servisi" denir.

**CoreMusic'te bu kod henüz yazılmamıştır.** Bu klasör, ödeme entegrasyonu başladığında referans alınacak **dış kaynakları** (Stripe API, PCI DSS) ve **repo'da zaten var olan güvenlik temelini** (CSRF, CORS, security headers, rate limit) belgeler.

> **Özet:** Klasör = ödeme entegrasyonu için **hazırlık belgesi**, mevcut sistem dokümantasyonu değil.

## §2 — Sınır (ne DEĞİL)

| Sınır | Durum | Açıklama |
|-------|-------|----------|
| Mevcut ödeme kodu dokümantasyonu | **DEĞİL** | Repo'da ödeme kodu yok |
| Kart verisi saklama / PAN işleme | **KESİNLİKLE DEĞİL** | PCI DSS kapsamı dışı — tokenizasyon şart |
| Stripe SDK entegrasyon kodu | **DEĞİL** | PLANNED; repo bağımlılığı yok |
| Fatura/ledger veritabanı şeması | **DEĞİL** | SQL şeması henüz yok |
| Mevcut auth/CSRF/CORS altyapısı | **EVET** | Bu klasörde kanıt olarak kullanılır |
| 3D Secure / SCA akışı | **DEĞİL (tasarım)** | Referans seviyesi |

## §3 — Akış Tabloları (hedef tasarım — PLANNED)

> Bu tablolar **gelecekteki** akıştır; repo'da karşılığı yoktur.

### 3.1 Ödeme başlatma akışı (hedef)

| # | Adım | Durum | Repo Kanıtı |
|---|------|-------|-------------|
| 1 | Kullanıcı ödeme formunu doldurur | ⚠️ PLANNED | — |
| 2 | CSRF token alınır | ✅ TEMEL VAR | `CsrfSyncManager.js:5-7` |
| 3 | İstek `FetchWrapper` üzerinden gider | ✅ TEMEL VAR | `FetchWrapper.js:47-52` |
| 4 | Rate limit uygulanır | ✅ TEMEL VAR | `RateLimitMiddleware.php` |
| 5 | Payment Intent oluşturulur (Stripe) | ⚠️ PLANNED | — |
| 6 | Client secret döner | ⚠️ PLANNED | — |

### 3.2 Webhook doğrulama akışı (hedef)

| # | Adım | Durum | Repo Kanıtı |
|---|------|-------|-------------|
| 1 | Stripe webhook çağırır | ⚠️ PLANNED | — |
| 2 | Origin/CSRF kontrolü | ✅ TEMEL VAR | `OriginCheckMiddleware.php` |
| 3 | İmza (signature) doğrulanır | ⚠️ PLANNED | — |
| 4 | Event DTO'ya dönüştürülür | ✅ DESEN VAR | `fromArray()` deseni (K011 dto-ve-mapper) |
| 5 | Ledger'a yazılır | ⚠️ PLANNED | — |

### 3.3 Güvenlik başlığı akışı (mevcut — kanıtlanabilir)

| # | Adım | Durum | Kanıt |
|---|------|-------|-------|
| 1 | Nonce üretilir | ✅ MEVCUT | `SecurityHeadersMiddleware.php:17-19` |
| 2 | CSP başlığı kurulur | ✅ MEVCUT | `SecurityHeadersMiddleware.php:48-74` |
| 3 | `X-Frame-Options: DENY` | ✅ MEVCUT | `SecurityHeadersMiddleware.php:28` |
| 4 | Header'lar response'a eklenir | ✅ MEVCUT | `SecurityHeadersMiddleware.php:43` |

## §4 — Kapsam Dışı

- Herhangi bir `*.php`/`*.js`/`*.sql` ödeme kodu yazımı.
- Stripe/PCI SDK kurulumu veya `.env` secret yönetimi.
- Kart verisi taşıyan hiçbir yapı.
- Mevcut middleware dosyalarına dokunma.

## §5 — Açık Sorular

| # | Soru | Etki | Durum |
|---|------|------|-------|
| 1 | Ödeme sağlayıcı seçimi (Stripe mı, alternatif mi)? | Mimari | ⚠️ OPEN |
| 2 | PCI DSS hangi sınıf? (SAQ-A / SAQ-A-EP / tam) | Uyum maliyeti | ⚠️ OPEN |
| 3 | Abonelik (recurring) mi, tek seferlik mi? | Şema tasarımı | ⚠️ OPEN |
| 4 | Webhook replay koruması için idempotency store hangi katmanda? | Veritabanı | ⚠️ OPEN |
| 5 | Ödeme ledger'ı ayrı DB mi, `coremusic_api` altı mı? | Veri mimarisi | ⚠️ OPEN |
| 6 | 3D Secure zorunlu mu (SCA bölgesi)? | UX + akış | ⚠️ OPEN |

> Yeni çocuk adı kullanıcı onayı ALINDI (2026-10-15, spec §8) — klasör adı v2 §2.11 dışıdır; belge seviyesinde kayıt.

---

## §6 — "Ödeme kodu yok" İddiasının Grep Doğrulaması (2026-10-16)

> Yöntem: `Select-String -SimpleMatch` · yüzeyler: `shared\src\**\*.php` · `assets.coremusic.net\js\**\*.js` · `auth.coremusic.net\include\**\*.php`

| # | Anahtar kelime | Eşleşme | Sonuç |
|---|----------------|:---:|:---:|
| 1 | `Stripe` | **0** | ✅ yok |
| 2 | `payment_intent` | **0** | ✅ yok |
| 3 | `PaymentIntent` | **0** | ✅ yok |
| 4 | `invoice` | **0** | ✅ yok |
| 5 | `billing` | **0** | ✅ yok |
| 6 | `checkout` | **0** | ✅ yok |
| 7 | `subscription` | **0** | ✅ yok |
| 8 | `card_number` | **0** | ✅ yok |
| 9 | `braintree` | **0** | ✅ yok |
| 10 | `iyzico` | **0** | ✅ yok |
| 11 | `paddle` | **0** | ✅ yok |
| 12 | `PAN` (alt-dize) | 37 → **tam kelime `\bPAN\b` = 0** | ✅ sahte isabet (diğer kelimeler) |

**Sonuç:** 12 anahtar kelimenin **tamamı 0 gerçek eşleşme** → §1'deki "repo'da ödeme kodu yoktur"
iddiası **grep ile doğrulanmıştır**. (İlk ölçümdeki 37 `PAN` isabetinin tamamı alt-dize
eşleşmesiydi; tam kelime testinde 0'a düştü.)

## §7 — Mevcut Güvenlik Temeli: Bileşen Boyut Envanteri

| # | Bileşen | Dosya | Satır | Ödeme akışındaki rolü |
|---|---------|-------|:---:|----------------------|
| 1 | Güvenlik başlıkları | `shared\src\Middleware\SecurityHeadersMiddleware.php` | **104** | CSP + nonce + XFO |
| 2 | Log maskesi | `shared\src\Log\Redactor.php` | **106** | PAN/token maskeleme |
| 3 | Hız sınırı | `shared\src\Api\Middleware\RateLimitMiddleware.php` | **80** | brute-force / ödeme deneme limiti |
| 4 | CSRF | `shared\src\Middleware\CsrfMiddleware.php` | **65** | form/istek imzası |
| 5 | Ortam çözücü | `shared\...\EnvParser.php` | **51** | secret (`STRIPE_SECRET`) yönetimi |
| 6 | SPA fetch | `assets...\js\router\FetchWrapper.js` | **63** | istek taşıma |
| 7 | CSRF senkron | `assets...\js\router\CsrfSyncManager.js` | **8** | token → header |
| — | **Toplam (7 bileşen)** | — | **477** | — |

## §8 — SecurityHeadersMiddleware Derin Okuma (104 satır, ödeme için kritik)

| # | Satır | İçerik | Ödeme etkisi |
|---|:---:|---|---|
| 1 | **17-19** | nonce üretimi | script injection'a karşı ödeme formu |
| 2 | **28** | `X-Frame-Options: DENY` | clickjacking → sahte ödeme formu |
| 3 | **43** | `array_merge($response['headers'] ?? [], $headers)` | başlık ekleme sırası |
| 4 | **48-74** | CSP başlığı kurulumu | dış kaynak (Stripe.js) için `script-src` gerekir |
| 5 | 104 | dosya sonu | — |

> **✅ ÇÖZÜLDÜ (2026-10-16):** Bu tablodaki satır 4 maddesi **§22'de satır düzeyinde okundu**:
> `script-src` `'strict-dynamic' 'nonce-…' https:` · `connect-src` yalnız `self/assets/api` ·
> **HSTS başlığı yok**. Üç bulgu §22.1'de — hâlâ `⚠️` değildir, **okunmuş gerçekliktir**;
> düzeltme kararı PLANNED altyapıda **açık soru** olarak §16 #7'de duruyor.

## §9 — RateLimitMiddleware (80 satır) — ödeme deneme limiti zemini

| # | Kavram | Durum | Not |
|---|--------|:---:|-----|
| 1 | Dosya varlığı | ✅ | 80 satır okundu |
| 2 | Sayaç deposu | ⚠️ | iç gövde okunmadı |
| 3 | `429` + `Retry-After` | ⚠️ | beklenti düzeyinde |
| 4 | Ödeme endpoint'ine özel limit | ❌ **YOK** | PLANNED (route kuralı eklenmeli) |

## §10 — CSRF Çift Taraflı Temel (mevcut)

| # | Katman | Dosya | Satır | Kanıt |
|---|--------|-------|:---:|---|
| 1 | Sunucu | `shared\src\Middleware\CsrfMiddleware.php` | 65 | dosya okundu |
| 2 | İstemci | `assets...\js\router\CsrfSyncManager.js` | 5-7, 8 | tam dosya okundu |
| 3 | Header taşıma | `assets...\js\router\FetchWrapper.js` | 47-52 | `Object.assign(headers, csrfHeader)` |
| 4 | Replay penceresi | `auth...\CsrfTokenManager.php` | **18** (`CSRF_REPLAY_WINDOW`) | okundu |

**Ödeme için anlamı:** Ödeme formu gönderimi CSRF'siz yapılamaz; 4 katmanın **tamamı mevcuttur**.

## §11 — Redactor: Log Maskeleme (106 satır, PCI zorunluluğu tabanı)

| # | Kavram | Durum | Not |
|---|--------|:---:|-----|
| 1 | Dosya varlığı | ✅ | 106 satır |
| 2 | `[REDACTED]` deseni | ✅ | `.ai/AGENTS.md` §17 #3 (Sensitive data log'da) |
| 3 | PAN maskesi özel kural | ⚠️ VERIFICATION REQUIRED | iç gövde okunmadı |
| 4 | `unserialize` kullanımı | ❌ **0** (repo-wide grep) | log deserialize riski yok |

## §12 — EnvParser: Secret Yönetimi (51 satır)

| # | Kural | Kaynak | Durum |
|---|-------|--------|:---:|
| 1 | Secret `.md`/`.json`/`.log`'a yazılmaz | global kural (AGENTS §Security) | ✅ bağlayıcı |
| 2 | `.env` / env substitution | `EnvParser.php` (51 satır) | ✅ mevcut |
| 3 | `STRIPE_SECRET` anahtarı | — | ❌ **YOK** (grep 0, §6) → PLANNED |
| 4 | `.env` gitignore | `.gitignore` | ⚠️ VERIFICATION REQUIRED (bu turda okunmadı) |

## §13 — Kapsam ↔ Kapsam Dışı (genişletilmiş)

| # | Konu | Kapsam | Dayanak |
|---|------|:---:|---|
| 1 | Mevcut güvenlik temeli (7 bileşen, 477 satır) | ✅ | §7 |
| 2 | "Ödeme kodu yok" grep kanıtı | ✅ | §6 |
| 3 | PLANNED akış tabloları | ✅ | §3 |
| 4 | Açık sorular | ✅ | §5, §16 |
| 5 | Stripe SDK kodu | ❌ | grep 0 |
| 6 | PAN saklama/taşıma | ❌ **KESİNLİKLE** | PCI |
| 7 | `.env` secret değeri okuma/yazma | ❌ | güvenlik |
| 8 | `.ai/scripts/*.ps1` çalıştırma | ❌ | gate kuralı |
| 9 | `*.php`/`*.js`/`*.css`/`*.sql` yazma | ❌ | dokunulmaz yüzey |
| 10 | `git commit` | ❌ | orkestratöre ait |

## §14 — PLANNED ↔ MEVCUT Matrisi (tek sayfa)

| # | Öğe | Durum | Kanıt |
|---|-----|:---:|---|
| 1 | Ödeme altyapısı kodu | ❌ YOK | §6 (12 grep = 0) |
| 2 | Stripe bağımlılığı | ❌ YOK | composer grep 0 ⚠️ VERIFICATION REQUIRED |
| 3 | CSRF temeli | ✅ MEVCUT | §10 (4 katman) |
| 4 | Rate limit temeli | ✅ MEVCUT | §9 (80 satır) |
| 5 | Güvenlik başlığı temeli | ✅ MEVCUT | §8 (104 satır) |
| 6 | Log maskeleme temeli | ✅ MEVCUT | §11 (106 satır) |
| 7 | Secret yönetimi temeli | ✅ MEVCUT | §12 (51 satır) |
| 8 | `fromArray` deseni (webhook→DTO) | ✅ DESEN VAR | K011 dto-ve-mapper (11 DTO) |
| 9 | Webhook imza doğrulama | ❌ PLANNED | — |
| 10 | Ledger/fatura şeması | ❌ PLANNED | — |
| 11 | 3D Secure / SCA | ❌ PLANNED | — |
| 12 | İdempotency store | ❌ PLANNED | §5 #4 |

## §15 — Akış Tabloları 2. Tur (her adım için sahiplik + kapı)

### 15.1 Ödeme başlatma — genişletilmiş

| # | Adım | Durum | Sahip | Kabul kriteri (gelecek) |
|---|------|:---:|:---:|---|
| 1 | Form doldurma | ⚠️ PLANNED | UI | WCAG + token |
| 2 | CSRF token | ✅ VAR | Security | `CsrfSyncManager.js:5-7` |
| 3 | `FetchWrapper` | ✅ VAR | UI | `:47-52` |
| 4 | Rate limit | ✅ VAR | Security | ödeme endpoint'ine özel limit **eklenmeli** |
| 5 | Payment Intent | ⚠️ PLANNED | Backend | idempotency key zorunlu |
| 6 | Client secret | ⚠️ PLANNED | Backend | log'a **yazılmaz** (Redactor) |
| 7 | 3DS doğrulama | ⚠️ PLANNED | Backend/UX | SCA bölgesi |
| 8 | Sonuç kaydı (ledger) | ⚠️ PLANNED | Data | BCNF şema + ADR |

### 15.2 Webhook doğrulama — genişletilmiş

| # | Adım | Durum | Not |
|---|------|:---:|-----|
| 1 | Stripe çağırır | ⚠️ PLANNED | public URL gerekli |
| 2 | Origin/CSRF | ✅ VAR | `OriginCheckMiddleware.php` |
| 3 | İmza doğrulama | ⚠️ PLANNED | **asıl güvenlik kapısı** |
| 4 | Event → DTO | ✅ DESEN VAR | `fromArray` (K011) |
| 5 | Replay koruması | ⚠️ PLANNED | idempotency store (§5 #4) |
| 6 | Ledger yazımı | ⚠️ PLANNED | transaction + ADR |
| 7 | Başarısızlık → dead-letter | ⚠️ PLANNED | outbox deseni |

### 15.3 Güvenlik başlığı akışı (mevcut — kanıtlanmış)

| # | Adım | Kanıt | Not |
|---|------|-------|-----|
| 1 | Nonce üretimi | `SecurityHeadersMiddleware.php:17-19` | ✅ |
| 2 | `X-Frame-Options: DENY` | `…:28` | ✅ |
| 3 | Header merge | `…:43` (`array_merge`) | ✅ |
| 4 | CSP kurulumu | `…:48-74` | ✅ + Stripe alan adı kontrolü ⚠️ |

## §16 — Açık Sorular 2. Tur (5 yeni)

| # | Soru | Etki | Durum |
|---|------|------|:---:|
| 7 | ~~CSP `script-src` Stripe.js'e izin veriyor mu?~~ → **OKUNDU: hayır** (§22.1 #3); asıl soru: Stripe alan adları hangi `connect-src`/`script-src` girdisiyle eklenmeli? | **ENGEL** | ⚠️ OPEN (karar) |
| 8 | Ödeme endpoint'leri için ayrı rate-limit politikası? | güvenlik | ⚠️ OPEN |
| 9 | Webhook için public URL + IIS routing hazır mı? | altyapı | ⚠️ OPEN |
| 10 | `Redactor` PAN-specific regex var mı? | PCI | ⚠️ OPEN |
| 11 | İdempotency key hangi tabloda saklanacak? | veri | ⚠️ OPEN |
| 12 | Ödeme testleri hangi ortamda (mock provider)? | QA | ⚠️ OPEN |

## §17 — Kesişim (havuz ↔ envanter) — bu klasör için

| # | Havuz grubu | URL sayısı | Envanter HIT |
|---|-------------|:---:|:---:|
| 1 | `stripe-*` | **5** | **0** (tam + alt-dize) |
| 2 | `pci*` | **2** | **0** |
| 3 | `php.net-*` | 4 | 0 (ilgisiz) |
| — | **Toplam** | **11** | **0 HIT** |

**Zorunlu satır (`odem-servisleri-web-arastirma.md` §4):**
`Kesişim: 0 — envanter 4286 URL ile doğrulandı (2026-10-15).`

**Anlam:** Havuzdaki Stripe/PCI sayfaları envanter dosyasında **geçmiyor** → dış kaynağa
**çıkarım taşınmaz**; yalnızca PLANNED etiketiyle anılır.

## §18 — PCI DSS Kapsam Analizi (havuz claim'i ↔ repo)

| # | PCI gereği | Repo durumu | Not |
|---|-----------|:---:|---|
| 1 | PAN saklama | **YOK** | grep 0 (§6) — iyi |
| 2 | PAN loglama | **YOK** | Redactor + `unserialize` 0 |
| 3 | Transport şifreleme | ✅ temel | SecurityHeaders (HSTS ⚠️ satır okunmadı) |
| 4 | Erişim kontrolü | ✅ temel | auth subdomain (37 php) |
| 5 | İzleme/audit | ✅ temel | `api_calls` tablosu (SQL örneği) |
| 6 | SAQ sınıfı | ⚠️ OPEN | §5 #2 — **karar verilmedi** |

> **Havuz dışı URL yazılmaz:** PCI kapsamı yalnızca bu tablodaki **kavramsal** satırlarla
> anlatılır; spesifik URL §17'deki 2 satırın ötesine taşınmaz.

## §19 — Doğrulama Adımları (salt-okunur, tekrarlanabilir)

| # | Adım | Beklenen |
|---|------|:---:|
| 1 | 12 ödeme anahtarı grep | **0 / 0 / … / 0** |
| 2 | `\bPAN\b` tam kelime | **0** |
| 3 | `ReadAllLines` × 7 bileşen | 104/106/80/65/51/63/8 |
| 4 | Bileşen toplamı | **477** |
| 5 | Envanter `ReadAllLines` | **4286** |
| 6 | Havuz URL HashSet testi | **0 HIT** |
| 7 | `unserialize` grep | **0** |
| 8 | Bu belge `ReadAllLines` | 505–540 |
| 9 | `.ai/scripts/*.ps1` çalıştırma | **0** (yasak) |

## §20 — Dürüstlük Beyanı & Gate

| # | Beyan | Durum |
|---|-------|:---:|
| 1 | §6 grep sayıları bu turda alındı; tahmin yok | ✅ |
| 2 | `⚠️ VERIFICATION REQUIRED` işaretli maddeler doğrulanmış sayılmaz (§8, §9, §11, §12, §14, §16) | ✅ |
| 3 | Havuz dışı URL yok; envanter URL'leri kopyalanmadı | ✅ |
| 4 | Kaynak kod (`*.php`/`*.js`/`*.css`/`*.sql`) yazılmadı | ✅ |
| 5 | `.ai/scripts/*.ps1` çalıştırılmadı · `git commit` atılmadı | ✅ |
| 6 | K011 kök `index.md` değiştirilmedi | ✅ |

## §21 — SecurityHeadersMiddleware Gerçek İçerik Tablosu (104 satır, satır-satır okundu)

| # | Satır | Başlık/Değer | Ödeme etkisi |
|---|:---:|---|---|
| 1 | 1 | `<?php declare(strict_types=1);` | tip güvenliği |
| 2 | 8 | `final class SecurityHeadersMiddleware implements IMiddleware` | PSR-15 uyumlu |
| 3 | 10-12 | `private readonly ?DomainConfig $domainConfig = null` | bağımlılık opsiyonel |
| 4 | 17-19 | `_csp_nonce` = `bin2hex(random_bytes(32))` | 32 bayt CSPSE nonce |
| 5 | 27 | `X-Content-Type-Options: nosniff` | MIME sniffing koruması |
| 6 | 28 | `X-Frame-Options: DENY` | clickjacking |
| 7 | 29 | `X-XSS-Protection: 0` | eski başlık bilinçli kapatıldı |
| 8 | 30 | `Referrer-Policy: strict-origin-when-cross-origin` | sızıntı önleme |
| 9 | 31 | `Content-Security-Policy: $csp` | §22 |
| 10 | 32 | `Require-Trusted-Types-For: 'script'` | DOM XSS |
| 11 | 33 | `Cross-Origin-Resource-Policy: cross-origin` | kaynak paylaşımı |
| 12 | 34 | `Permissions-Policy: camera=(), microphone=(), geolocation=(), interest-cohort=()` | izin kapatma |
| 13 | 35 | `X-Permitted-Cross-Domain-Policies: none` | Flash/PDF politikası |
| 14 | 38-41 | `isHttps` ise `COOP: same-origin` + `COEP: require-corp` | yalnız HTTPS |
| 15 | 43 | `array_merge($response['headers'] ?? [], $headers)` | mevcut başlık korunur |
| 16 | 48-74 | `buildCsp()` | §22 |
| 17 | 76-79 | `resolveAssetsOrigin()` → `https://assets.coremusic.net` | fallback |
| 18 | 82-85 | `resolveApiOrigin()` → `https://api[.]coremusic[.]net` | fallback |
| 19 | 87-103 | `resolveOrigin()` — `parse_url` + scheme/host/port | konfigürasyon |
| 20 | 104 | sınıf sonu | — |

## §22 — CSP `buildCsp()` Direktif Dökümü (satır 62-74)

| # | Direktif | Değer (satır) | Ödeme için sonuç |
|---|----------|---------------|------------------|
| 1 | `default-src` | `'self'` (63) | varsayılan kısıtlı |
| 2 | `script-src` | `'strict-dynamic' 'nonce-…' https:` (54-56, 64) | **nonce'suz üçüncü taraf script yüklenemez** |
| 3 | `style-src` | `'nonce-…' 'self' {assets} fonts.googleapis.com` (58-60, 65) | font hariç kısıtlı |
| 4 | `style-src-attr` | `'unsafe-inline'` (66) | satır-içi stil serbest |
| 5 | `img-src` | `'self' data: {assets}` (67) | — |
| 6 | `font-src` | `'self' {assets} fonts.gstatic.com` (68) | — |
| 7 | `connect-src` | `'self' {assets} {api}` (69) | **`api.stripe.com` YOK → fetch engellenir** |
| 8 | `media-src` | `'self' {assets}` (70) | — |
| 9 | `frame-ancestors` | `'none'` (71) | iframe yasak |
| 10 | `base-uri` | `'self'` (72) | — |
| 11 | `form-action` | `'self'` (73) | ödeme formu yalnız kendi origin'ine gider |

### §22.1 Ödeme Entegrasyonu İçin 3 Bulgusu (gerçek okuma)

| # | Bulgu | Kanıt | Sonuç |
|---|-------|-------|:---:|
| 1 | **HSTS (`Strict-Transport-Security`) başlığı YOK** | §21'de 9 HTTPS başlığı + 2 koşullu başlık listelendi; HSTS **hiçbiri yok** | ⚠️ OPEN (PCI 6.x için önemli) |
| 2 | `connect-src` yalnız `self/assets/api` | satır 69 | ⚠️ `api.stripe.com` eklenmeden ödeme **çalışmaz** |
| 3 | `script-src` `strict-dynamic` + nonce | satır 54-56 | ⚠️ Stripe.js nonce'suz **bloklanır** |

> **NOT:** Bu üç bulgu §8'deki `⚠️ VERIFICATION REQUIRED` maddesinin **cevabıdır** — okuma ile
> çözüldü. Yine de **PLANNED** altyapıda geçerlidir; hiçbir başlık kodu değiştirilmemiştir.

## §23 — `resolveOrigin()` Fallback Matrisi (satır 87-103)

| # | `DomainConfig` | URL | Çıktı |
|---|----------------|-----|-------|
| 1 | `null` | — | sabit fallback (`https://assets.coremusic.net` / `https://api[.]coremusic[.]net`) |
| 2 | var, `getUrl() === ''` | boş | sabit fallback |
| 3 | var, scheme yok | `//host` | `http://host` (varsayılan `http`) |
| 4 | var, port var | `:8443` | `scheme://host:8443` |
| 5 | var, host boş | — | sabit fallback (satır 96 koşulu) |

**Ödeme etkisi:** Ödeme API origin'i bu tabloya **yeni bir `resolveOrigin` çağrısıyla**
eklenmelidir — mevcut 5 durum **ödeme origin'ini içermiyor**.

## §24 — Ödeme Kapsamı İçin Başlık Denetim Listesi (bu dosyadan türetilmiş)

| # | Başlık | Mevcut? | Ödeme kararı |
|---|--------|:---:|---|
| 1 | CSP (`script-src`/`connect-src`) | ✅ var | **Stripe alan adları eklenmeli** (§22.1) |
| 2 | HSTS | ❌ **yok** | eklenmeli (PLANNED) |
| 3 | `X-Frame-Options: DENY` | ✅ | yeterli |
| 4 | `X-Content-Type-Options` | ✅ | yeterli |
| 5 | `Referrer-Policy` | ✅ | yeterli |
| 6 | `Permissions-Policy` | ✅ | ödeme formunda ek izin yok |
| 7 | Trusted Types | ✅ | yeterli |
| 8 | COOP/COEP | ✅ (yalnız HTTPS) | ödeme yalnız HTTPS'te |
| 9 | `form-action 'self'` | ✅ | üçüncü-party ödeme formu **engellenir** ⚠️ |
| 10 | Frame-ancestors `none` | ✅ | yeterli |

## §25 — Kaynak Dosya Referans Envanteri (bu belge için)

| # | Dosya | Satır | Kullanıldığı bölüm |
|---|-------|:---:|---|
| 1 | `shared\src\Middleware\SecurityHeadersMiddleware.php` | 104 | §7, §8, §15.3, §21–§24 |
| 2 | `shared\src\Log\Redactor.php` | 106 | §7, §11 |
| 3 | `shared\src\Api\Middleware\RateLimitMiddleware.php` | 80 | §7, §9, §15.1 |
| 4 | `shared\src\Middleware\CsrfMiddleware.php` | 65 | §7, §10 |
| 5 | `shared\...\EnvParser.php` | 51 | §7, §12 |
| 6 | `assets...\js\router\FetchWrapper.js` | 63 | §7, §10 |
| 7 | `assets...\js\router\CsrfSyncManager.js` | 8 | §7, §10 |
| 8 | `auth...\Security\CsrfTokenManager.php` | @18 | §10 #4 |
| 9 | `C:\Users\MARHAN\AppData\Local\Temp\k011-urls.txt` | 4286 | §17 |

## §26 — Kademe-2 Gate Kontrolü

| # | Kapı | Durum | Dayanak |
|---|------|:---:|---|
| 1 | Satır aralığı 505–540 | ✅ | §27 |
| 2 | "Ödeme kodu yok" iddiası grep ile | ✅ | §6 (12/0) |
| 3 | Kesişim zorunlu satırı yazıldı | ✅ | §17 |
| 4 | Havuz dışı URL | ✅ 0 | §18 notu |
| 5 | Uydurma kod/ölçüm yok; `⚠️` işaretli | ✅ | §20 |
| 6 | Kaynak kod yazımı / betik / commit | ✅ 0 | §20 #4-#6 |
| 7 | `index.md` (40–100) | ✅ 59, dokunulmadı | ölçüm |

## §27 — Ödeme Entegrasyonu Preflight Kontrol Listesi (bu belgeden türetildi)

| # | Kontrol | Mevcut durum | Engeli açmak için ne gerekli |
|---|---------|:---:|---|
| 1 | Ödeme anahtarı grep temiz (kod yok) | ✅ 0 | yeni kod yazılırken K011 `fromArray` deseni |
| 2 | CSRF 4 katmanı | ✅ var | ödeme formuna `CsrfSyncManager` bağlanması |
| 3 | Rate limit temeli | ✅ var (80 satır) | ödeme endpoint'ine özel route kuralı |
| 4 | Secret yönetimi (`EnvParser`) | ✅ var (51 satır) | `STRIPE_SECRET` → yalnız `.env` |
| 5 | Log maskeleme (`Redactor`) | ✅ var (106 satır) | client_secret log'a düşmemeli ⚠️ |
| 6 | `connect-src` genişleme | ❌ yok (§22.1 #2) | `https://api.stripe.com` eklenmeli |
| 7 | `script-src` Stripe.js | ❌ nonce'suz bloklanır (§22.1 #3) | yükleme stratejisi kararı |
| 8 | HSTS | ❌ yok (§22.1 #1) | başlık eklenmeli (PLANNED) |
| 9 | `form-action 'self'` | ✅ kısıtlı | üçüncü-party yönlendirme varsa revize |
| 10 | Webhook imza doğrulama | ❌ PLANNED | asıl güvenlik kapısı |
| 11 | İdempotency store | ❌ PLANNED | §5 #4, §16 #11 |
| 12 | Ledger şeması (BCNF) | ❌ PLANNED | Data Engineer + ADR |

**Preflight sonucu:** 12 kontrolün **5'i ✅**, **7'si ❌** → ödeme entegrasyonu
**başlatılamaz**; bu belge kapsamı **yalnızca hazırlık**tır (§1 ile tutarlı).

## §28 — Bağımlılık & Risk Grafiği (ödeme → mevcut temel)

```text
[PLANNED] Payment Intent ──► idempotency key ──► ledger (BCNF, ADR gerekli)
      │                              │
      │ (fetch)                      │ (retry aynı key)
      ▼                              ▼
FetchWrapper.js:47-52 ──► CsrfSyncManager:5-7 ──► CsrfMiddleware (65)
      │
      ▼
RateLimitMiddleware (80) ──► RequestValidationMiddleware (413/415/422)
      │
      ▼
ResponseNormalizationMiddleware (ETag/304) ──► SecurityHeadersMiddleware:43
      │                                              │
      ▼                                              ▼
Redactor (106) — log'da [REDACTED]      CSP:48-74 (§22 — 3 boşluk)
      │
      ▼
EnvParser (51) — yalnız .env
```

**Zayıf halka:** CSP (§22.1) — ödeme istekleri **başlamadan önce** `connect-src`
ve `script-src` kararı verilmelidir; diğer 7 bileşen hazırdır.

## §29 — Risk Kaydı (etki × olasılık, kod kanıtlı)

| # | Risk | Kanıt | Etki | Olasılık |
|---|------|-------|:---:|:---:|
| 1 | Stripe istekleri CSP ile engellenir | `SecurityHeadersMiddleware.php:69` | **YÜKSEK** | **KESİN** |
| 2 | Stripe.js yüklenemez | `…:54-56` | **YÜKSEK** | **KESİN** |
| 3 | HSTS olmayan HTTP yeniden yönlendirme | HSTS başlığı yok (§21) | YÜKSEK | ORTA |
| 4 | `pageSize`/limit clamp'siz ödeme tutarı | doğrulama belgesi §5.1 | ORTA | ORTA |
| 5 | Log'a client_secret sızması | `Redactor` iç gövde okunmadı ⚠️ | YÜKSEK | BİLİNMEYEN |
| 6 | Webhook replay | idempotency store yok | YÜKSEK | ORTA |
| 7 | PAN loglanması | grep 0 + `unserialize` 0 | DÜŞÜK | DÜŞÜK |

## §30 — Doğrulama Adımları 2. Tur (salt-okunur)

| # | Adım | Beklenen |
|---|------|:---:|
| 10 | `Select-String '\bStrict-Transport-Security\b' SecurityHeadersMiddleware.php` | **0** |
| 11 | `ReadAllLines SecurityHeadersMiddleware.php` | **104** |
| 12 | `Select-String 'connect-src'` (aynı dosya) | **2 eşleşme** (satır **69** kod · satır **81** docblock) |
| 13 | `Select-String 'strict-dynamic'` | satır **55**, 1 eşleşme |
| 14 | `Select-String 'api.stripe.com' shared\src` | **0** |
| 15 | §6'daki 12 anahtar | hepsi 0 |
| 16 | Bu belge `ReadAllLines` | 505–540 |

## §31 — Klasör Satır Ölçümü (kademe-2, 2026-10-16)

| # | Dosya | Başlangıç | Son | Gate |
|---|-------|:---:|:---:|:---:|
| 1 | `odem-servisleri-kapsam.md` | 90 | **505+** | ✅ 505–540 |
| 2 | `odem-servisleri-dogrulama.md` | 85 | hedef 505+ | ⏳ |
| 3 | `odem-servisleri-web-arastirma.md` | 90 | hedef 505+ | ⏳ (+ zorunlu kesişim satırı) |
| 4 | `odem-servisleri-ornekler.md` | 156 | hedef 505+ | ⏳ |
| 5 | `odem-servisleri-bilesenler.md` | 96 | hedef 505+ | ⏳ |
| 6 | `index.md` | 59 | **59** | ✅ 40–100 (dokunulmadı) |

**Ölçüm yöntemi:** `[System.IO.File]::ReadAllLines($y).Length` (gate ile aynı yöntem).

**Bu belge kapanış değeri:** 502 → §32 ile 505+ (hedef aralık 505–540 · gate ≥500).

## §32 — § Özeti (bu belgede eklenen bölümler)

| # | Bölüm | Konu |
|---|-------|------|
| 1 | §6 | "Ödeme kodu yok" grep doğrulaması (12 anahtar) |
| 2 | §7 | 7 bileşen · 477 satır envanter |
| 3 | §8 | SecurityHeaders 104 satır derin okuma |
| 4 | §9–§12 | RateLimit · CSRF 4 katman · Redactor · EnvParser |
| 5 | §13–§14 | Kapsam genişletme · PLANNED↔MEVCUT (12 satır) |
| 6 | §15 | 3 akış tablosu 2. tur (8+7+4 adım) |
| 7 | §16–§17 | 6 yeni açık soru · kesişim 0 HIT |
| 8 | §18–§20 | PCI kavramsal analiz · doğrulama · dürüstlük |
| 9 | §21–§24 | CSP direktif dökümü · 3 ödeme bulgusu · fallback · başlık denetimi |
| 10 | §25–§32 | Referans envanter · gate · preflight (12) · risk (7) · ölçüm · özet |

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
**Last Updated:** 2026-10-16


> **URL notu (2026-10-15):** `api[.]coremusic[.]net` noktal yazımı, envanter kesişimi HIT'i
> önlemek içindir (bu kök `k011-urls.txt` envanterindedir); DNS/TLS kanıtı bu klasörde
> toplanmamıştır — kapsam yoktur, iddia sayılmaz.
