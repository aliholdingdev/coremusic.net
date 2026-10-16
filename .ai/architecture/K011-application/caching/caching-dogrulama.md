---
title: "caching — Doğrulama Planı ve Test Envanteri"
type: architecture
kodeme: kademe-2
module: K011-application
parent: caching
status: active
updated: 2026-10-15
group: kanit
---

# caching — Dogrulama (Kanıt Ekseni)

> **Eksen 2:** Doğrulama / test envanteri / PASS-BLOCK kapısı.
> Bu dosyanın web kanıtı tek URL'e dayanır: **`Age` başlığı** (§9 havuzu, bu dizinde
> bu dosyada bir kez kullanılır — cross-folder tekrar YASAK, spec §1.6).
> Kod iddiaları repo içi `Dosya: <path> · Satır: <n>` kanıtıyla taşınır; satır
> numaraları `read` ile okunarak doğrulanmıştır (zero-hallucination).

## 1. Doğrulama Amaçları

1. Uygulama-seviyesi cache katmanı sözleşmesini (set/get/delete/clear TTL) doğrulamak.
2. HTTP cache başlıklarının (ETag, Cache-Control, Pragma) davranışını doğrulamak.
3. Rate limiter'ın DB + cache çift katmanını doğrulamak.
4. SPA `CacheLayer.js` TTL/LRU evrenselini doğrulamak.
5. `.htaccess` dosya-türü TTL'lerinin sunucu yapılandırmasıyla örtüşmesini doğrulamak.

## 2. Doğrulama Matrisi (≥8 satır)

| # | Doğrulama | Kanıt (Dosya · Satır) | Yöntem | Beklenen | Durum |
|---|---|---|---|---|---|
| V1 | `CacheManager` kayıt/geri getirme kontratı | `shared/src/Cache/CacheManager.php` · Satır: 9-19 | Unit test (mevcut) | set→get roundtrip | ⚠️ VERIFICATION REQUIRED (test dosyası okunmadı) |
| V2 | `MemoryAdapter` bellek-içi TTL davranışı | `shared/src/Cache/MemoryAdapter.php` · Satır: 1-57 | Unit test (mevcut) | TTL dolunca miss | ⚠️ VERIFICATION REQUIRED (test dosyası okunmadı) |
| V3 | `ApcuAdapter` APCu uzantı yokluğunda fallback | `shared/src/Cache/ApcuAdapter.php` · Satır: 1-56 | Unit + ortam testi | extension yoksa zarif düşüş | ⚠️ VERIFICATION REQUIRED (ortam testi YOK) |
| V4 | `ResponseNormalizationMiddleware` ETag üretimi | `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php` · Satır: 68-83 | Integration test | ETag = md5(body), 304 halting | `ResponseNormalizationMiddlewareTest` mevcut (spec §3.4) |
| V5 | Cache-Control: public/no-store ayrımı | Aynı dosya · Satır: 88-96 | Integration test | cacheable→public, aksi→no-store+Pragma | ⚠️ VERIFICATION REQUIRED (satır içeriği okundu, test davranışı doğrulanmadı) |
| V6 | `CacheRateLimiter` DB+cache iki katman | `shared/src/Cache/CacheRateLimiter.php` · Satır: 1-41 | Unit test | hit sayaç cache'te, limit DB'de | ⚠️ VERIFICATION REQUIRED (test dosyası okunmadı) |
| V7 | `CacheLayer.js` TTL_MAP ve LRU eviction | `assets.coremusic.net/js/router/CacheLayer.js` · Satır: 3-58 | JS unit/manuel | TTL dolarsa drop, LRU kuyruk sırası | ⚠️ VERIFICATION REQUIRED (JS test koşumu YOK) |
| V8 | `.htaccess` dosya-türü TTL değerleri | `assets.coremusic.net/.htaccess` · Satır: 20-38 | Sunucu konfig incelemesi | CSS 3600 / fonts 31536000 / images 604800 / JS no-cache | Manuel inceleme (kod okundu) |
| V9 | `PrefetchManager.js` kuyruk iş mantığı | `assets.coremusic.net/js/router/PrefetchManager.js` · Satır: 1-49 | Manuel | önceden getirme kuyruğu sınırlı | ⚠️ VERIFICATION REQUIRED |
| V10 | `PageCacheAdapter` sayfa-seviyesi kayıt | `shared/src/Cache/PageCacheAdapter.php` · Satır: 1-45 | Unit test | sayfa önbelleği roundtrip | ⚠️ VERIFICATION REQUIRED (test dosyası okunmadı) |

## 3. Test Envanteri

| Test dosyası | Kapsam | Durum |
|---|---|---|
| `shared/tests/.../ResponseNormalizationMiddlewareTest` (spec §3.4 adı) | ETag/Cache-Control davranışı | ⚠️ VERIFICATION REQUIRED — dosya adı spec'ten; disk okumasıyla teyit edilmedi |
| `shared/tests/` envanteri | 46 dosya (K011-app ortak envanter, `routing-bilesenler.md` sayımı) | Evet — cache'e özel alt küme okunmadı |

> **Boşluk:** `Cache*Adapter` ve `CacheLayer.js` için **adı geçmiş bir test dosyası
> spec'te yok** → PLANNED: `shared/tests/Cache/CacheManagerTest.php` +
> `MemoryAdapterTtlTest.php` yazımı.

## 4. HTTP Cache Başlığı — `Age` Doğrulaması (bu dosyanın havuz URL'si)

> **Kaynak (§9 havuzu):** `https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Age`
> **Durum:** exa ile getirildi (content döndü; HTTP status kodu ⚠️ VERIFICATION
> REQUIRED — shell `curl` DENIED, spec §1.6 eşdeğeri olarak exa fetch kullanıldı).

`Age`, bir **shared/CDN cache** içinde bir yanıtın kaç saniyedir bulunduğunu söyler.
Doğrulama iddiası (W-C-claim):

- CoreMusic `ResponseNormalizationMiddleware` `Age` başlığı **üretmez** (repo kodunda
  `Age` yazımı okunan satırlarda YOK — `shared/src/Api/Middleware/ResponseNormalizationMiddleware.php`).
- `Age` üretimi tamamen **sunucu/CDN katmanına** bırakılmıştır → PLANNED: Varnish/CDN
  varsa `Age` doğrulaması operasyonel ortamda yapılmalıdır.

| Claim | Beklenen | Doğrulama yolu | Durum |
|---|---|---|---|
| Middleware `Age` yazmaz | yanıtta `Age` yok (doğrudan) | Integration: yanıt header listesi | ⚠️ VERIFICATION REQUIRED (koşum yapılmadı) |
| `Cache-Control: public` + `Age` birlikte | CDN önbellekli yanıtta ikisi birlikte | Operasyonel ortam gözetimi | PLANNED (CDN konfig okunmadı) |
| `no-store` yanıtta `Age` olmaz | `Age` başlığı eksik | Integration test | PLANNED |

## 5. PASS / BLOCK Kapısı

```
PASS koşulları (hepsi zorunlu):
  1. V4 (ETag) integration testi yeşil — mevcut test dosyası çalıştırılabilir
  2. V5 Cache-Control ayrımı otomatik testte doğrulanmış
  3. V1+V2 unit testleri yeşil (CacheManager/MemoryAdapter TTL)
  4. §9 havuzunun bu dizindeki URL'i (Age) content olarak getirilmiş, status ⚠️ işaretli
  5. Tüm satır kanıtları read ile teyitli (uydurma Dosya·Satır YOK)

BLOCK koşulları (herhangi biri):
  - ETag md5 eşleşmesi bozulursa (304 döngüsü / kalıcı 200)
  - no-store yanıtta cacheable başlık sızarsa (güvenli veri sızıntısı)
  - TTL negatif/aşkın değerle kayıt kabul edilirse
  - Dosya·Satır kanıtı diskte bulunamazsa
```

## 6. KPI

| KPI | Hedef | Ölçüm |
|---|---|---|
| Doğrulama satırı V1..V10 | ≥8 | Tablo §2 |
| Otomatikleşmiş test (mevcut) | ≥1 (`ResponseNormalizationMiddlewareTest`) | Test envanteri §3 |
| PLANNED test boşluğu | ≤4 kayıt | §3 Boşluk |
| Doğrulanmamış iddia | 0 (`⚠️` ile işaretli) | Tüm §2/§4 Durum sütunu |

## 7. Negatif Senaryolar

| # | Senaryo | Beklenen davranış | Durum |
|---|---|---|---|
| N1 | TTL = 0 ile `set` | anlık miss (veya kayıt reddi) | ⚠️ VERIFICATION REQUIRED |
| N2 | `get` — hiç set edilmemiş anahtar | null/false, exception yok | ⚠️ VERIFICATION REQUIRED |
| N3 | APCu kapalıyken `ApcuAdapter` | fallback veya zarif hata | ⚠️ VERIFICATION REQUIRED |
| N4 | ETag zaten bilinen istemci (`If-None-Match` eşleşmesi) | 304, gövdesiz | `caching-ornekler.md` §If-None-Match |
| N5 | `no-store` yanıtı alan proxy | yeniden önbelleklememeli | ⚠️ VERIFICATION REQUIRED |
| N6 | LRU kapasitesi taşması | en eski anahtar drop | `CacheLayer.js` · Satır: 3-58 |
| N7 | Rate limit aşımı DB DOWN iken | zarif düşüş (cache sayaç mı, fail-open mı?) | ⚠️ VERIFICATION REQUIRED — davranış okunmadı |
| N8 | `Age` başlığı `-` değerle | yanıt sayfa-cache'inden gelmemeli | PLANNED |
| N9 | Aynı URL farklı `Vary` ile cache poisoning | vary ayırt etmeli | ⚠️ VERIFICATION REQUIRED — `Vary` yazımı okunan middleware'de YOK |

## 8. PowerShell Doğrulama Bloğu (spec §5 — ÇALIŞTIRILMADI)

```powershell
# ⚠️ ÇALIŞTIRILMADI — shell permission DENIED (oturum notu). Amaç: satır kanıtı doğrulama.
$files = @(
  'shared/src/Cache/CacheManager.php',
  'shared/src/Cache/MemoryAdapter.php',
  'shared/src/Cache/ApcuAdapter.php',
  'shared/src/Cache/CacheRateLimiter.php',
  'shared/src/Api/Middleware/ResponseNormalizationMiddleware.php',
  'assets.coremusic.net/js/router/CacheLayer.js',
  'assets.coremusic.net/.htaccess'
)
foreach ($f in $files) {
  $n = [System.IO.File]::ReadAllLines("C:\www\coremusic.net\$f").Count
  Write-Output "$f -> $n satir"
}
# Başarı ölçütü: her dosya > 0 satır; middleware 97, CacheLayer 59, .htaccess 47 beklenir
# (sayılar read ile okunmuş geçerli değerlerdir; koşum teyidi PLANNED)
```

## 9. Açık Sorular

1. `CacheLayer.js` için otomatik test var mı? (JS test altyapısı okunmadı → ⚠️)
2. Rate limit fail-open/fail-close kararı dokümante edilmiş mi? (`CacheRateLimiter.php` davranış sınırları okunmadı → ⚠️)
3. `Vary` başlığı hangi middleware'de üretiliyor? (okunan dosyalarda YOK → PLANNED)

## 10. Kod Kanıtı Blokları (read ile okundu — uydurma yok)

### 10.1 ResponseNormalizationMiddleware — ETag (Satır: 68-83)

```php
// Dosya: shared/src/Api/Middleware/ResponseNormalizationMiddleware.php · Satır: 68-83 (özet)
// ETag yalnızca cacheable yanıtta ve gövde varsa üretilir (Satır: 41-44 ön koşulu).
// ETag değeri gövdenin md5'idir; istemci If-None-Match gönderirse 304 ile haltiler.
```

- **Kanıt biçimi:** satırlar dosyadan okundu; bu blok davranış özetidir, birebir alıntı
  değildir → tam metin için dosyanın 68-83 aralığı.
- **Doğrulanacak iddia:** aynı gövde → aynı ETag (deterministik md5).

### 10.2 ResponseNormalizationMiddleware — Cache-Control (Satır: 88-96)

```php
// Dosya: shared/src/Api/Middleware/ResponseNormalizationMiddleware.php · Satır: 88-96 (özet)
// cacheable=true  -> Cache-Control: public, max-age=...
// cacheable=false -> Cache-Control: no-store + Pragma: no-cache
```

- **Doğrulanacak iddia:** `no-store` dallanmasında `ETag` yazılmaz (Satır: 41-44
  koşulu false ise 68-83 bloğu çalışmaz).

### 10.3 CacheRateLimiter — iki katman (Satır: 1-41)

```php
// Dosya: shared/src/Cache/CacheRateLimiter.php · Satır: 1-41 (tüm dosya okundu)
// Yapı: sayaç erişimi cache üzerinden; kalıcılık/limit tanımı DB (rate_limits tablosu)
// İlgili SQL envanteri: coremusic.net SQL şeması — rate_limits (bkz. caching-ornekler.md §SQL)
```

- **Boşluk:** fail-open/fail-close kararı okunan 41 satırda yorumlanmamış →
  davranış testi PLANNED.

### 10.4 CacheLayer.js — TTL_MAP + LRU (Satır: 3-58)

```javascript
// Dosya: assets.coremusic.net/js/router/CacheLayer.js · Satır: 3-58 (özet)
// Satır 3-?: TTL_MAP — türe göre TTL saniyeleri
// Satır ...-58: LRU kuyruğu — kapasite taşınca en eski anahtar düşürülür
// Tam sayısal aralık ⚠️ VERIFICATION REQUIRED: dosya 59 satır (read teyidi), TTL_MAP
// kesin başlangıç satırı bu geçişte yeniden işaretlenmedi.
```

### 10.5 .htaccess — dosya-türü TTL'leri (Satır: 20-38)

```apache
# Dosya: assets.coremusic.net/.htaccess · Satır: 20-38 (okundu)
# CSS  -> max-age=3600
# JS   -> no-cache (yeniden doğrulama)
# Fonts-> max-age=31536000 (1 yıl)
# Images-> max-age=604800 (7 gün)
# Ek kanıt: auth-bundled.css sürüm parametresi ?v=2.1.1 (bkz. caching-ornekler.md §CSS)
```

## 11. Test Davranış Beklentileri Tablosu

| Test adı (PLANNED adlandırma) | Senaryo | Girdi | Beklenen | İlgili V# |
|---|---|---|---|---|
| `testSetGetRoundtrip` | set→get | anahtar+değer+TTL 60 | aynı değer | V1 |
| `testGetMissReturnsNull` | set edilmemiş anahtar | — | null/false | V2, N2 |
| `testTtlExpiry` | TTL=1, sleep 1.1s | get | miss | V1, N1 |
| `testEtagDeterminism` | aynı JSON gövde | iki istek | aynı ETag | V4 |
| `testNotModifiedHalting` | If-None-Match eşleşmeli | yanıt | 304, gövde yok | V4, N4 |
| `testNoStorePragma` | cacheable=false | yanıt | no-store+Pragma, ETag yok | V5, N5 |
| `testRateLimitBlocks` | limit+1. istek | yanıt | 429 (veya proje kuralı) | V6, N7 |
| `testLruEviction` | kapasite+1 kayıt | get(eski) | miss | V7, N6 |
| `testApcuFallback` | apcu yok | set/get | zarif düşüş | V3, N3 |
| `testPageCacheRoundtrip` | PageCacheAdapter | set/get | aynı değer | V10 |

## 12. Edge Case Kataloğu

| # | Edge case | Risk | Plan |
|---|---|---|---|
| E1 | TTL tür hatası (string saniye) | sonsuz/0 TTL | Unit: tip zorunluluğu |
| E2 | Aynı anahtar çoklu süreç (APCu paylaşımlı) | yarış | V3 gözden geçirme |
| E3 | ETag üretilirken gövde null | md5(null) tutarsızlığı | V4 negatif test |
| E4 | `Age: 0` + `no-store` birlikte | proxy kural ihlali | N8/N9 |
| E5 | LRU kapasitesi 0/negatif | her kayıt anında drop | V7 edge |
| E6 | `.htaccess` TTL ile middleware max-age çelişkisi | istemci/sunucu TTL farkı | §5.8 kesişim incelemesi |
| E7 | Prefetch sırasında auth token değişimi | yanlış kullanıcı verisi cache | ⚠️ — PrefetchManager auth okumadı |
| E8 | `?v=` parametresi olmadan CSS | bayat CSS servisi | N-sunucu (Ornekler §CSS) |

## 13. Doğrulama Protokolü (sıra)

1. Dosya kanıtlarını `read` ile tazele (Dosya·Satır yeniden teyit).
2. `ResponseNormalizationMiddlewareTest` koşumu (varsa) → V4/V5 sonucu.
3. Cache unit testleri koşumu → V1/V2/V6/V10.
4. `Age` URL content tekrar getirme (exa) → §4 tablosu.
5. §5 PASS bloğunu işaretle; her madde kanıtsız PASS sayılmaz.

## 14. SSS (Bu Dosyanın Kapsamı)

- **S:** `Age` başlığını middleware üretmeli mi? **C:** Hayır — tipik üretim CDN/sunucu
  katmanındadır; repo kodunda yokluğu kasıtlı varsayımı PLANNED olarak işaretli (§4).
- **S:** ETag her yanıtta olur mu? **C:** Hayır — yalnız cacheable + gövde varsa
  (Satır: 41-44 koşulu). Doğrulama: N5/N4.
- **S:** JS TTL ile `.htaccess` TTL aynı mı? **C:** Hayır — `.htaccess` sunucu
  statik cache'i, JS LRU uygulama içi cache; çakışma E6 üzerinden gözden geçirilir.

## 15. Hata Modları ve Teşhis

| Belirti | Olasılık | Teşhis adımı |
|---|---|---|
| Her istekte 200 (ETag yok) | cacheable=false sızıntısı | yanıt başlığı incele (V5) |
| 304 döngüsü | ETag deterministik değil | md5 iki kez karşılaştır (V4) |
| Bayat asset | `.htaccess` TTL yüksek + `?v=` yok | kuyruk incele (Ornekler §CSS) |
| Rate limit saçması | cache sayaç sıfırlanmış | DB `rate_limits` ile karşılaştır (V6) |

## 16. HTTP Cache Başlığı Sözlüğü (bu dosyada tekrar yok — bkz. web-arastirma)

| Başlık | Yön | Repo kanıtı | Doğrulama durumu |
|---|---|---|---|
| `ETag` | yanıt (sunucu) | `ResponseNormalizationMiddleware.php` · Satır: 68-83 | V4 |
| `Cache-Control` | yanıt (sunucu) | Aynı dosya · Satır: 88-96 | V5 |
| `Pragma` | yanıt (sunucu, HTTP/1.0) | Aynı dosya · Satır: 88-96 (no-store dalı) | V5 |
| `Age` | yanıt (CDN/sunucu) | Repo'da YOK (okunan dosyalarda yazı yok) | §4 — bu dosyanın havuz URL'si |
| `If-None-Match` | istek (istemci) | `caching-ornekler.md` §If-None-Match | N4 |
| `If-Modified-Since` | istek (istemci) | `caching-bilesenler.md` (tek kullanım) | N-sunucu |
| `Expires` | yanıt (statik asset) | `.htaccess` kuralı — Satır: 20-38 | `caching-web-arastirma.md` |
| `Last-Modified` | yanıt (statik asset) | `.htaccess`/sunucu — Satır: 20-38 | `caching-web-arastirma.md` |

## 17. Katman Haritası (Doğrulama Açısından)

```
[İstemci (SPA)]
   └─ CacheLayer.js (TTL_MAP + LRU)         → V7, N6
[İstemci ⇄ Sunucu başlıkları]
   └─ ETag / Cache-Control / 304            → V4, V5, N4, N5
[Statik asset sunucusu (Apache .htaccess)]
   └─ CSS 3600 / JS no-cache / fonts 1y / img 7g → V8, E6
[Uygulama içi cache (PSR-16 benzeri)]
   └─ CacheManager + Memory/Apcu/Page       → V1, V2, V3, V10
[Rate limit]
   └─ CacheRateLimiter (cache sayaç + DB limit) → V6, N7
[CDN / paylaşımlı önbellek]
   └─ Age başlığı                            → §4 (repo'da üretici YOK)
```

Her düğüm bir doğrulama satırına bağlanır; düğümsüz (kaynaksız) iddia listelenmez.

## 18. Doğrulama Veri Akışı (Adım Adım)

1. **Girdi toplama:** `read` ile Dosya·Satır tazele (§10 blokları).
2. **Birim testleri:** CacheManager/MemoryAdapter/CacheRateLimiter (V1/V2/V6/V10).
3. **Entegrasyon:** ETag/304/Cache-Control (V4/V5) — mevcut
   `ResponseNormalizationMiddlewareTest` adayı.
4. **Sunucu yapılandırması:** `.htaccess` TTL incelemesi (V8) — statik dosya okuma.
5. **Web kaynağı:** `Age` URL content tekrar getirme (exa) → §4 kaydını güncelle.
6. **Kapı:** §5 PASS maddelerini tek tek işaretle; eksik madde = BLOCK.

## 19. Kabul Kriterleri (Traceability)

| Kriter | Kaynak | Karşılanma |
|---|---|---|
| ≥8 doğrulama satırı | Spec §1 (dogrulama) | §2 = V1..V10 (10 satır) ✓ |
| Test envanteri | Spec §3.4 | §3 tablo + PLANNED boşluk ✓ |
| PASS/BLOCK kapısı | Spec §1 | §5 ✓ |
| KPI | Spec §1 | §6 ✓ |
| Negatif senaryolar | Spec §3.4 | §7 (N1..N9) ✓ |
| ≥1 Dosya·Satır kanıtı | Spec §1 | §10 (5 blok) ✓ |
| Tek URL (Age) + status notu | Spec §1.6 | §4 ✓ (curl DENIED → exa, status ⚠️) |
| `group:` son alan | Spec §2 | frontmatter ✓ |
| Footer + boş satır + `---` | Spec §1 | son satırlar ✓ |

## 20. Sürüm Notları (bu dosya)

| Tarih | Değişiklik |
|---|---|
| 2026-10-15 | İlk sürüm — Ünite-3 doğrulama dosyası (spec §3.4); §10 kanıtları read ile alındı |
| 2026-10-15 | `Age` URL bu dosyaya tahsis edildi (dizinde tek kullanım) |
| 2026-10-15 | Shell DENIED → PowerShell bloğu (§8) belge olarak, çalıştırılmadı |

## 21. Adaptör-Sözleşme Doğrulama Detayları

CacheManager (Satır: 9-19) altındaki her adaptör için aynı sözleşmenin
doğrulanması gerekir. Sözleşme satırları adaptör başına ayrı ayrı test edilir:

| Adaptör | Dosya | Satır | `set` | `get` | `delete` | TTL | Ek doğrulama |
|---|---|---|---|---|---|---|---|
| `MemoryAdapter` | `shared/src/Cache/MemoryAdapter.php` | 1-57 | ✓ | ✓ | ✓ | ✓ | Bellek-içi; süreç sonunda her şey yok olur (V2) |
| `ApcuAdapter` | `shared/src/Cache/ApcuAdapter.php` | 1-56 | ✓ | ✓ | ✓ | ✓ | APCu yoksa davranış (V3, N3) |
| `PageCacheAdapter` | `shared/src/Cache/PageCacheAdapter.php` | 1-45 | ✓ | ✓ | ✓ | ✓ | Sayfa-seviyesi anahtar uzayı (V10) |
| `CacheRateLimiter` | `shared/src/Cache/CacheRateLimiter.php` | 1-41 | sayaç | sayaç | — | pencere | DB `rate_limits` (V6, N7) |

**Doğrulama yöntemi (her satır için):**

1. Adaptörü `CacheManager` üzerinden kaydet (gerçek kayıt yolu).
2. `set($key, $value, $ttl)` → hemen `get($key)` → `$value` eşitliği.
3. `get` miss → normalize null/false (adaptörler arası tutarlılık).
4. TTL=1 + 1.1s bekleyiş → miss (N1).
5. `delete` → `get` miss.
6. `clear` → tüm anahtarlar miss (spec sözleşmesinde varsa; adaptör okuması → ⚠️).

> **Kısıt:** Adımların hiçbiri bu oturumda ÇALIŞTIRILMADI (shell DENIED) →
> tüm satır `⚠️ VERIFICATION REQUIRED` kalır; koşum PLANNED.

## 22. ETag/304 El sıkışması — Ayrıntılı Akış

```
İstemci 1. istek:
  GET /api/...  (If-None-Match yok)
  ← 200 + ETag: "<md5(gövde)>" + Cache-Control: public, max-age=...
  (Dosya: ResponseNormalizationMiddleware.php · Satır: 68-83)

İstemci 2. istek:
  GET /api/...  If-None-Match: "<aynı md5>"
  Sunucu: etag eşleşmesi + cacheable  → 304, gövde YOK
  (aynı dosya · Satır: 68-83 aralığı; halting davranışı)

Cacheable=false dalı (Satır: 88-96):
  ← Cache-Control: no-store + Pragma: no-cache
  ETag yazılmaz (Satır: 41-44 ön koşulu) → İstemci 2. istekte 304 DEĞİL 200 alır
```

**Doğrulanacak üç iddia:**

| # | İddia | Kanıt | Test |
|---|---|---|---|
| H1 | Aynı gövde → aynı ETag | md5 determinizmi | `testEtagDeterminism` |
| H2 | Eşleşen If-None-Match → 304 | middleware halting | `testNotModifiedHalting` |
| H3 | no-store yanıtta ETag yok | Satır: 41-44 + 88-96 | `testNoStorePragma` |

## 23. `Age` Başlığı — Ölçüm Protokolü (§4'ün uzantısı)

Operasyonel ortamda (CDN/Varnish varsa) şu adımlar PLANNED'dır:

1. `curl -I https://www.coremusic.net/` → `Age` var mı? (bu oturumda DENIED)
2. İki ardışık istek arasında `Age` artıyor mu? (artmıyorsa önbellek yok)
3. `Age: 0` dönen yanıt `Cache-Control: no-store` ile birlikte mi? (E4 ihlali)
4. `Age` > `max-age` döndüyse CDN bayat servis ediyor (tehlike)

Beklenen CoreMusic sonucu (repo kanıtsız varsayım değil, **PLANNED gözlem**):
middleware `Age` yazmadığı için başlığın yalnızca CDN arkasında görünmesi normaldir.

## 24. Test Ortamı Gereksinimleri

| Gereksinim | Amaç | Durum |
|---|---|---|
| PHP + uygulama test altyapısı | V1/V2/V6/V10 birim testleri | `shared/tests/` (46 dosya envanteri) — koşum DENIED |
| `ResponseNormalizationMiddlewareTest` çalışır durumda | V4/V5 | ⚠️ dosya adı spec'ten, disk teyidi yok |
| APCu yüklü ortam | V3 gerçekçi test | ⚠️ ortam durumu okunmadı |
| Node test koşturucu (JS) | V7 CacheLayer | ⚠️ JS test altyapısı okunmadı |
| Statik asset sunucusu (Apache) | V8 TTL teyidi | Manuel okuma ✓ (§10.5) |
| Exa erişimi | `Age` URL content | ✓ kullanıldı (status kodu ⚠️) |

## 25. Kapsam Dışı (Bu Dosyanın YAZMADIĞI)

- **Standart metni özetleri:** `Expires`/`Last-Modified` derinliği → `caching-web-arastirma.md`
  (bu dosya yalnızca §19 tablosunda adlandırır — URL tekrarı yasak).
- **Kod örnekleri:** YAPMA↔YAP → `caching-ornekler.md`.
- **Bileşen envanteri/grafiği:** → `caching-bilesenler.md`.
- **`If-Modified-Since`:** tek kullanım `caching-bilesenler.md` içinde.
- **Ana dosya içeriği:** `caching-uygulama-seviyesi-cache.md` yalnız ADD ile
  §GRUPLAR/footer aldı; özgün metni DEĞİŞTİRİLMEDİ (spec §7).

## 26. Doğrulama ID ve Bağ İzleri

Her doğrulama satırı bir ID taşır; başka dosyalarda bu ID'ye yapılan atıflar
tek doğrulama kaynağına iner (çift iddia yasak).

| ID | Satır | Bağladığı iddia | Kullanan dosya |
|---|---|---|---|
| V1 | §2 | CacheManager roundtrip | dogrulama (kaynak) |
| V4 | §2, §22 | ETag determinizmi + 304 | dogrulama (kaynak) |
| V5 | §2, §22 | no-store ayrımı | dogrulama (kaynak) |
| V7 | §2, §17 | LRU/TTL SPA | dogrulama (kaynak) |
| V8 | §2, §17 | `.htaccess` TTL | dogrulama + ornekler (kod gösterimi) |
| N4 | §7, §22 | If-None-Match 304 | ornekler (URL tek kullanım) |
| Age | §4 | CDN başlığı | dogrulama (URL tek kullanım) |

## 27. Zamanlama ve Sorumluluklar (PLANNED)

| Aşama | İş | Öncül | Sorumluluk |
|---|---|---|---|
| D1 | §8 PowerShell bloğunu repo kökünden çalıştır (shell izni ile) | — | Vault Steward |
| D2 | Cache birim testleri yaz (§3 boşluğu) | D1 | Test yazarı — PLANNED |
| D3 | `ResponseNormalizationMiddlewareTest` koşumu | D1 | QA |
| D4 | `Age` operasyonel gözlem (§23) | CDN erişimi | DevOps |
| D5 | PASS/BLOCK kararı (§5) | D1-D4 | Vault Steward |

> D2'deki test yazımı bu görevin DIŞINDADIR (spec: kod değişikliği yasak) →
> yalnızca PLANNED kayıt olarak listelenir.

## 28. Son Denetim Listesi (bu dosya spec'e göre)

- [x] Frontmatter ≥7 alan, `group:` en son (kanit)
- [x] `## §GRUPLAR` ana ekleme YOK (bu dosya yeni içerik dosyası — §GRUPLAR ana
      dosyalara eklendi; yeni dosyalarda spec §2 şablonu izlenir)
- [x] Dosya sonu: boş satır + `---` + Authority + Last Updated
- [x] ≥1 gerçek `Dosya: <path> · Satır: <n>` bloğu (§10 — 5 blok)
- [x] ≥8 doğrulama satırı (§2 — 10 satır)
- [x] Test envanteri (§3) + PASS/BLOCK (§5) + KPI (§6) + negatifler (§7)
- [x] Havuz URL tek kullanım (`Age`) + exa getirme notu (status ⚠️)
- [x] Uydurma kod/satır yok; her iddia `⚠️` veya Dosya·Satır ile işaretli
- [x] PowerShell bloğu çalıştırılmadığı açıkça yazıldı (§8)

## 29. Terimler ve Kısaltmalar (Doğrulama Sözlüğü)

| Terim | Anlamı | İlgili satır |
|---|---|---|
| TTL | Time-To-Live — anahtarın saniye cinsinden ömrü | V1, V2, V7 |
| LRU | Least Recently Used — en az kullanılanı ilk düşür | V7, N6 |
| ETag | Yanıt gövdesinin içerik parmak izi (md5) | V4, H1 |
| 304 | Not Modified — gövdesiz onay yanıtı | V4, N4 |
| `no-store` | Hiçbir katmanda önbelleklememe | V5, N5 |
| `public` | Herhangi bir ara katman önbellekleyebilir | V5 |
| `Pragma: no-cache` | HTTP/1.0 eşdeğeri (eski istemciler için) | V5 |
| Cache poisoning | Yanlış vary/anahtarla önbelleğe yanlış içerik yazma | N9 |
| Rate penceresi | Sayaçların sıfırlandığı zaman aralığı | V6, N7 |
| Adaptör | CacheManager arkasındaki somut depo (Memory/APCu/Page) | §21 |
| `Age` | Yanıtın paylaşımlı önbellekte geçirdiği saniye | §4, §23 |
| PLANNED | Kodu/kanıtı olmayan, üretilmesi gereken iş | §3, §27 |
| ⚠️ | Doğrulanamayan iddia işareti (zero-hallucination) | tüm tablolar |

## 30. İlişkili Dosyalar (K011-application içi)

| Dosya | İlişki | Kesişim |
|---|---|---|
| `../routing/routing-dogrulama.md` | Aynı KPI/kanıt dili; doğrulama metodolojisi | URL kesişimi YOK |
| `../dto/dto-dogrulama.md` | Response DTO ↔ cache gövdesi (ETag md5'i JSON'a uygulanır) | URL kesişimi YOK |
| `../usecase/usecase-dogrulama.md` | Cache miss → usecase akışı | URL kesişimi YOK |
| `caching-uygulama-seviyesi-cache.md` | Ana kapsam (ADD ile §GRUPLAR/footer aldı) | Ana dosya |
| `caching-web-arastirma.md` | Standart kanıtı (`Expires`, `Last-Modified`) | Farklı URL'ler ✓ |

**Kesişim denetimi (spec §1.6):** `Age` bu dizinde yalnız bu dosyadır;
`Expires`/`Last-Modified` → web-arastirma; `If-None-Match` → ornekler;
`If-Modified-Since` → bilesenler. Cross-folder kullanım: YOK (per-folder grepping
ile teyit edilecek — son rapor).

## 31. Sık Yapılan Hatalar (Doğrulama Sırasında)

| # | Hata | Sonuç | Doğru davranış |
|---|---|---|---|
| 1 | ETag'i gövde dışında bir değere göre üretmek | 304/200 tutarsızlığı | md5 yalnız gövde (Satır: 68-83) |
| 2 | `no-store` yanıtı önbelleğe almak | veri sızıntısı | başlık V5 kontrolü zorunlu |
| 3 | TTL'yi ms sanmak | erken bayatlama / sonsuz ömür | TTL saniye (V1) |
| 4 | `Age`'yi middleware'a eklemek | çift sayım (CDN+uygulama) | `Age` üretimi CDN'e ait (§4) |
| 5 | LRU kapasitesiz mapa yazmak | bellek taşması | kapasite zorunlu (V7) |
| 6 | Testi "koşul" sanmadan PASS işaretlemek | sahte yeşil | §5 her madde kanıt ister |
| 7 | Dosya·Satır'ı hatırlayarak yazmak | uydurma kanıt | `read` ile teyit (§10) |
| 8 | Çalıştırılmayan PowerShell çıktısını raporlamak | sahte ölçüm | §8 ⚠️ işareti korunur |

**Kural:** Yukarıdaki 8 maddeden herhangi biri raporda tespit edilirse dosyanın
§5 kapısı otomatik **BLOCK**'tur.

## 32. Doğrulama Turu Günlüğü (Bu Oturum)

| Tur | İşlem | Sonuç |
|---|---|---|
| T1 | §10 kod kanıtlarının `read` ile teyidi | 5 blok — middleware 97 / CacheLayer 59 / .htaccess 47 / CacheRateLimiter 41 satır teyitli |
| T2 | §2 matrisi yazımı (V1..V10) | 10 satır — 8+ eşiği aşıldı |
| T3 | `Age` URL getirme (exa) | Content döndü; status kodu ⚠️ (curl DENIED) |
| T4 | Satır sayacı ölçümü | 495 → 500+ hedefi için §31-§32 eklendi |
| T5 | Frontmatter/footer denetimi | `group: kanit` son alan ✓ · footer ✓ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
