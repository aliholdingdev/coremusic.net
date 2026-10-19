---
node_id: K009-security/security-testing/security-testing-dogrulama
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/security-testing/security-testing-surucu
  - K009-security/security-testing/security-testing-ornekler
evidence:
  - C:\www\coremusic.net\shared\tests\Middleware\CsrfMiddlewareTest.php·85-97
  - C:\www\coremusic.net\shared\tests\Middleware\RateLimiterMiddlewareTest.php·52-128
  - C:\www\coremusic.net\shared\tests\Middleware\MiddlewarePipelineTest.php·176-264
  - C:\www\coremusic.net\.github\workflows\ci.yml·44-186
  - C:\www\coremusic.net\.github\workflows\secret-scan.yml·24-31
validation: Faz 7 ön draft — 15 AD-xx satırı disk kanıtı ile hazırlandı, 0 çalıştırılmış test
open_questions:
  - "AD-05 fail-open ↔ T-10 fail-closed çatışması çözülmedi ⚠️ VERIFICATION REQUIRED"
  - "AD-13 SecurityHeaders dedicated test dosyası yok ⚠️ VERIFICATION REQUIRED"
group: kanit
---

# K009 · security-testing · Doğrulama

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Doğrulama Tablosu

Bu modül, güvenlik testi disiplininin **kanıt satırlarıdır**: her satır bir kontrol,
bir beklenen sonuç, bir `Dosya·Satır` kaynağı ve bir durum. Bu dosya Faz 7 ön
draft'ıdır — **hiçbir test bu oturumda çalıştırılmadı**; durum sütunu kod/gözlem
kanıtına dayanır, koşu sonucuna değil (gerçek koşu sonucu için `Status: passed`
gerektirir).

**TT-10 · Doğrulama KK:** Yeni kanıt satırı = `AD-xx` + kaynak `Dosya·Satır` +
`Beklenen`; kaynaksız satır sayılmaz. Durum `⚠️` olan satır üste raporlanır.

---

## §1. Doğrulama İlkeleri

1. **Kanıt önce, iddia sonra:** Her satırın kaynak satır aralığı dosyada okunur.
2. **Koşmadan PASS yazılmaz:** `Pass` yalnız `composer test` çıktısıyla elde edilir;
   bu dosyanın mevcut hali `verify-required`'tır.
3. **Çatışma gizlenmez:** Kod ↔ strateji uyuşmazlığı satırda `⚠️` ile durur (AD-05).
4. **Gate ≠ test:** CI job'un varlığı (AD-11..AD-12) testin geçtiği anlamına gelmez.

---

## §2. Doğrulama Zinciri (bu dosya nereye bağlanır)

```text
security-test-stratejisi.md (T-01..T-20, plan)
        ↓
security-testing-surucu.md (G-01..G-13, gate zinciri)
        ↓
shared/tests/** + .github/workflows/*.yml (kanıt kaynağı = kod)
        ↓
>>> security-testing-dogrulama.md (AD-xx satırları = BURASI) <<<
        ↓
index.md (durum: verify-required → passed yalnız koşu sonrası)
```

---

## §3. Doğrulama Satırları (AD-xx)

| Kontrol | Beklenen | Dosya·Satır | Durum |
|---|---|---|---|
| AD-01 · CSRF token zorunluluğu | Token yoksa 419 + `csrf_token_valid=false` | `shared/tests/Middleware/CsrfMiddlewareTest.php·85-97` | ✅ kod kanıtı (okundu) |
| AD-02 · CSRF response body sözleşmesi | Body'de `reason: csrf_token_invalid` alanı | `shared/tests/Middleware/CsrfMiddlewareTest.php·306-324` | ✅ kod kanıtı (okundu) |
| AD-03 · Rate limit penceresi | 3 istek sonrası 429, `Retry-After` başlığı | `shared/tests/Middleware/RateLimiterMiddlewareTest.php·52-74` | ✅ kod kanıtı (okundu) |
| AD-04 · Rate limit cevap body'si | 429 body'de `error: rate_limited` | `shared/tests/Middleware/RateLimiterMiddlewareTest.php·77-103` | ✅ kod kanıtı (okundu) |
| AD-05 · Rate limit fail davranışı | ⚠️ Test: cache hatasında fail-open (istek geçer) | `shared/tests/Middleware/RateLimiterMiddlewareTest.php·105-128` | ⚠️ strateji T-10 ile ÇATIŞIYOR (fail-closed) |
| AD-06 · Pipeline hata taşıma | 1. middleware 500 atarsa zincir durur, hata request'e biniyor | `shared/tests/Middleware/MiddlewarePipelineTest.php·176-221` | ✅ kod kanıtı (okundu) |
| AD-07 · 401/403 akış sırası | 401 → 403 ardışık, sıradaki middleware hiç çalışmıyor | `shared/tests/Middleware/MiddlewarePipelineTest.php·254-264` | ✅ kod kanıtı (okundu) |
| AD-08 · CORS API güvenlik | Method/Origin/Headers doğrulaması yanıtta | `shared/tests/Api/ApiPipelineSecurityTest.php·52-63` | ✅ kod kanıtı (okundu) |
| AD-09 · Origin reddi | geçersiz Origin → 403 + `origin_invalid` | `shared/tests/Middleware/OriginCheckMiddlewareTest.php·67-74` | ✅ kod kanıtı (okundu) |
| AD-10 · Log redaksiyonu | hassas anahtarlar logda maskeli | `shared/tests/Unit/LoggerRedactionTest.php·70-88` | ✅ kod kanıtı (okundu) |
| AD-11 · CI test kapısı | `composer test` job'i ci.yml'de tanımlı, `paths: shared/**` tetikli | `.github/workflows/ci.yml·62-64,104-108,123-127` | ✅ yapı kanıtı (okundu) |
| AD-12 · CI lint + PHPStan | `composer lint` / `composer stan` job'ları | `.github/workflows/ci.yml·141-186` | ✅ yapı kanıtı (okundu) |
| AD-13 · SecurityHeaders dedicated test | ⚠️ `shared/tests/` içinde ayrı `SecurityHeadersTest` dosyası YOK | glob: `shared/tests/**` (45 dosya) | ⚠️ boşluk — kapsam: yalnız pipeline testi (`MiddlewarePipelineTest.php·176-221`) |
| AD-14 · Secret tarama gate | `add-build-trigger` + `rev` + kod taraması ayrı job | `.github/workflows/secret-scan.yml·24-31` | ✅ yapı kanıtı (okundu) |
| AD-15 · Test/Lint bağımlılıkları | phpunit `^10.5`, phpstan `^1.10` require-dev'de | `shared/composer.json·46-49` | ✅ yapı kanıtı (okundu) |

**Sayım:** 15 satır · 13 ✅ kod/yapı kanıtı · 2 ⚠️ (AD-05, AD-13) · **0 çalıştırılmış test**.

---

## §4. ⚠️ Açık Çatışma ve Boşluklar

### §4.1 AD-05 · fail-open ↔ fail-closed

| Kaynak | Ne diyor |
|---|---|
| `RateLimiterMiddlewareTest.php·105-128` | cache hatası → istek **geçer** (fail-open), `expectNotToPerformAssertions` |
| `security-test-stratejisi.md` T-10 | rate limit'te **fail-closed** beklenir (strateji satırı: `security-test-stratejisi.md·51-75` plan aralığı) |

Sonuç: kod stratejiyle uyuşmuyor. Bu dosya **kodu tarif eder, stratejiyi düzeltmez**;
karar üst otoritedir. Seçenekler: (a) test stratejiye uydurulur (fail-closed), (b)
strateji koda uydurulur (fail-open gerekçesi yazılır), (c) ADR açılır.
**Durum: ⚠️ VERIFICATION REQUIRED — çözülmedi.**

### §4.2 AD-13 · SecurityHeaders test boşluğu

`shared/tests/` içinde header doğrulayan ayrı dosya yok (45 dosyalık glob'da
`SecurityHeadersTest` eşleşmesi 0). Header assertion'ları yalnız
`MiddlewarePipelineTest` içinde (`MiddlewarePipelineTest.php·176-221` ile
`MiddlewarePipelineTest.php·254-264` aralıklarında). **Durum: ⚠️ VERIFICATION REQUIRED**
— dedicated test açılması `security-test-stratejisi.md` T-01..T-20 planına eklendi
mi, kontrol edilecek.

### §4.3 Diğer boşluklar

| # | Boşluk | ⚠️ |
|---|---|---|
| B-06 | AD-01..AD-14 hiçbirinin bu oturumda koşu kanıtı yok | ⚠️ VERIFICATION REQUIRED (`composer test` çalıştırılmadı — shell reddedildi) |
| B-07 | Kod tarama job'ları (`ci.yml·141-186`) hata eşiği/exit-code davranışı okunmadı | ⚠️ VERIFICATION REQUIRED |
| B-08 | `secret-scan.yml` tetikleyici aralığı (`.github/workflows/secret-scan.yml·1-45` tamamı) yalnız job tanımını kapsıyor; `on:` tetikleyicisi okunmadı | ⚠️ VERIFICATION REQUIRED |
| B-09 | Kapsam dışı iade (kapsam.md C-01..C-05) için sahip domain onayı yok | ⚠️ VERIFICATION REQUIRED |

---

## §5. Geçici Durum Özeti (Faz 7 ön draft)

| Metrik | Değer | Kanıt |
|---|---|---|
| AD satırı | 15 | §3 |
| Kod/yapı kanıtı satırı | 13 | §3 `✅` |
| ⚠️ satırı | 2 (AD-05, AD-13) | §3/§4 |
| Çalıştırılmış test | **0** | B-06 (shell izni yok) |
| Gate'ler (G-01..G-13) | 13 tanım, koşu yok | `security-testing-surucu.md` §1 |
| Strateji bağlantısı | T-01..T-20 bağlandı, tekrarlanmadı | `security-test-stratejisi.md·51-75` |

**Status geçiş kuralı:** bu dosya `verify-required` → `passed` yalnız şunlarla olur:
(1) `composer test` + `composer stan` + `composer lint` yeşil koşu çıktısı,
(2) AD-05 çatışmasının kararı (ADR/karar satırı), (3) AD-13 boşluğunun kapanması
veya bilinçli kapsam dışı kararı.

---

## §6. Doğrulama Protokolü (Faz 7'de çalıştırılacak)

1. **Adım 1 — lokal kanıt:** `composer test` + `composer stan` (kaynak:
   `shared/composer.json·23-26` script'leri) → çıktı AD satırına `Pass/Fail` olarak işlenir.
2. **Adım 2 — gate kanatları:** `ci.yml` job'ları (AD-11, AD-12) yeşil mi → `security-testing-surucu.md` §3 D-xx akışına bağlanır.
3. **Adım 3 — secret-scan:** `secret-scan.yml` job tanımı (AD-14) + tetikleyici (B-08) okunur.
4. **Adım 4 — çatışma kararı:** AD-05 için üst karar alınır, satır `⚠️` → kararlı duruma döner.
5. **Adım 5 — boşluk kararı:** AD-13 dedicated test açılır veya kapsam dışı ilan edilir.
6. **Adım 6 — status:** tüm satırlar çalıştırılmışsa `status: passed` + `validation: PASS - [tarih]`.

**Yasak:** koşu yapılmadan `Pass`/`PASS` yazmak (zero-hallucination §3); bu dosyanın
mevcut hali bunu **yazmaz** — 0 çalıştırılmış test açıkça beyan edilir (§5).

---

## §BAĞLANTILAR

- [[K009-security/security-testing/index]] — giriş, §3 dosya haritası (doğrulama satırı)
- [[K009-security/security-testing/security-testing-surucu]] — G-01..G-13 gate zinciri (AD bağlandığı motor)
- [[K009-security/security-testing/security-testing-ornekler]] — Ö-01..Ö-11 gerçek kod alıntıları
- [[K009-security/security-testing/security-testing-anayasa]] — R-01..R-10 kırmızı kriterler
- [[K009-security/security-test-stratejisi]] — T-01..T-20 planı (AD satırlarının girdisi)
- [[authentication-dogrulama]] — kardeş doğrulama örneği (paralel yapı)

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
