---
node_id: K009-security/policies/policies-dogrulama
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on: K009-security/policies/policies-surucu
evidence: shared/tests/Middleware/CsrfMiddlewareTest.php · shared/tests/Middleware/MiddlewarePipelineTest.php · shared/tests/Middleware/BypassAuthMiddlewareFailClosedTest.php · shared/tests/Unit/Log/LoggerRedactionTest.php
validation: partial (17 AD-xx: 6 GREEN · 9 RED · 2 OPEN — test KOŞTURULMADI, kanıt = dosya okuması)
open_questions: OriginCheck fail-open (P-09f) · parola min 8 vs 12+ (P-01c) · KVKK saklama metni · JWT stub null
group: kanit
---

# K009 · policies · policies-dogrulama

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Doğrulama Kaydı
> Her politika için **beklenen kanıt** ve **dosyalanmış durum**.
> Durum: `GREEN` (kanıt dosyada görüldü) · `RED` (kanıt yok/çalıştırılmadı) ·
> `OPEN` (karar üst mercide). RED = politika `verify-required` kalır.
> P-numaraları `policies-anayasa.md` §4'e göredir.

## BAĞLANTILAR

- [[policies-surucu]] — politika → kod → test yönlendirme tablosu
- [[policies-anayasa]] — P-01..P-18 + §12 ihlal sınıfı
- [[../security-testing/index]] — test zinciri hedefi
- [[../audit/index]] — RED bulgu landı
- [[index]] — dizin indeksi

---

## 1. Dogrulama Kayitlari (AD-xx)

| AD | Politika | Beklenen kanıt | Dosya·Satır | Durum |
|---|---|---|---|---|
| AD-01 | P-11 (`csrf_token` adı) | test sabiti `csrf_token`; kodda `_csrf_token` yok | `CsrfMiddlewareTest.php:11` · `CsrfMiddleware.php` (ad:35,39) | GREEN |
| AD-02 | P-12a (GET/HEAD/OPTIONS hariç) | metot kontrolü kodda | `CsrfMiddleware.php:22-27` | GREEN |
| AD-03 | P-12b (hash_equals) | timing-safe karşılaştırma; `==`/`strcmp` yok | `CsrfMiddleware.php:42-44` | GREEN |
| AD-04 | P-12c (403 + olay) | `securityEvent('csrf_invalid')` + httpStatus 403 | `CsrfMiddleware.php:51-63` | GREEN |
| AD-05 | P-12d (bypass listesi boş) | `bypassRoutes ?? []`; eski `['set-gender']` kaldırılmış | `CsrfMiddleware.php:15-17` | GREEN |
| AD-06 | P-14b (nonce sırası) | pipeline sırası testi | `MiddlewarePipelineTest.php:13` | GREEN |
| AD-07 | P-16 (`[REDACTED]`) | log redaksiyon testi | `LoggerRedactionTest.php:74-82,127` | GREEN |
| AD-08 | P-09b/c (bypass fail-closed) | credential eksik → `[]`; config yok → bypass yok | `BypassAuthMiddleware.php:11,29-31,41-44` + FailClosedTest | GREEN |
| AD-09 | P-01a/b (min 8 + istisna) | `MIN_PASSWORD_LENGTH = 8` + ValidationException | `Password.php:13-14,28-30` | GREEN (kod) · test RED |
| AD-10 | P-02a/b (Argon2id maliyeti) | `PASSWORD_ARGON2ID` + 65536/4/2 | `Password.php:44-48` · `.ai/CLAUDE.md:336` | GREEN (kod) · test RED |
| AD-11 | P-03 (pepper fail-closed) | boş pepper → configError / false | `Password.php:40-42,56-58` | GREEN (kod) · test RED |
| AD-12 | P-15 (başlık seti) | yanıtta nosniff/DENY/Referrer/frame-ancestors/form-action | `SecurityHeadersMiddleware.php:26-36,71,73` | RED (yanıt testi yok) |
| AD-13 | P-14a (CSP nonce) | `strict-dynamic 'nonce-…'` kodda | `SecurityHeadersMiddleware.php:54-56` | GREEN (kod) · başlık testi RED |
| AD-14 | P-09f/P-13b (Origin fail-closed) | Origin yoksa 403 (ADR-010:179-180) | anayasa §9 P-09f → `OriginCheckMiddleware.php:39-41` | **OPEN** (kod fail-open) |
| AD-15 | P-17 (rate-limit fail-closed) | auth ucu cache çözülemezse RED; genel uç log'lu fail-open | `RateLimiterMiddleware.php:20-21,55-57,66-67` (anayasa §9/§4) | RED (test okunmadı/çalıştırılmadı) |
| AD-16 | P-05/P-06 (kripto) | MD5/SHA-1/DES/ECB taraması boş | `.ai/CLAUDE.md:336` (kural) · `security-master.md:49` | RED (tarama çalıştırılmadı) |
| AD-17 | P-07 (sır) | repo'da hardcoded secret yok; `.env` gitignore | `.ai/CLAUDE.md:687,721` · `auth AGENTS.md` §4 Yasak 1 | RED (tarama çalıştırılmadı) |

**Özet**: 17 kayıt → GREEN 8 (kod kanıtlı; 4'ü test RED'i ile kısmi) · RED 7 · OPEN 1.
Modül `verify-required` kalır (status alanıdır).

---

## 2. Kanit Testlari (okunan test dosyalari)

| Test dosyası | Kapsadığı politika | AD |
|---|---|---|
| `shared/tests/Middleware/CsrfMiddlewareTest.php` | P-11, P-12 | AD-01..AD-05 |
| `shared/tests/Middleware/MiddlewarePipelineTest.php` | P-08f, P-14b | AD-06 |
| `shared/tests/Unit/Log/LoggerRedactionTest.php` | P-04b, P-07e, P-16 | AD-07 |
| `shared/tests/Middleware/BypassAuthMiddlewareFailClosedTest.php` | P-09, P-18 | AD-08 |

**Çalıştırılmadı**: bu modülde test **koşturulmadı** (kanıt = dosya içeriği okuması).
`GREEN` = “beklenen assertion dosyada mevcut”; “koştu ve geçti” anlamına gelmez →
CI'da `shared/tests` çalıştırılınca satırlar güncellenir.

---

## 3. Kanit Biçimi (her AD satırının 4 alanı)

1. **Politika**: anayasadaki P-xx (yoksa satır geçersiz).
2. **Beklenen kanıt**: gözlemlenebilir şey (assertion / başlık / tarama sonucu).
3. **Dosya·Satır**: diskte var yol + satır (uydurma yasak — zero-hallucination).
4. **Durum**: GREEN / RED / OPEN + bir cümle gerekçe.

Dosya·Satır doğrulanamıyorsa satır `⚠️ VERIFICATION REQUIRED` ile işaretlenir ve
`open_questions`'a düşer. İkinci el iddia (başka dosyanın cümlesi) doğrudan kanıt
sayılmaz; **ilk el okuma** gerekir.

---

## 4. RED Yonetimi

```text
AD-xx = RED
  → policies-surucu.md §6 akışı: BLOCK/C1 (üretim kodu commit edilmez)
  → aksiyon seç:
        test yok          → test yaz (AD-09..AD-11, AD-12/13)
        tarama yok        → çalıştır (AD-16, AD-17)
        karar beklİyor    → OPEN, üst merci (AD-14)
  → kanıt dosyalanır: ad + dosya·satır + tarih → §1 satırı güncellenir
  → GREEN olan politika anayasada status'ü ile oynar; anayasa SSOT
```

3 başarısız turdan sonra durulur; şüpheli varsayım yazılır ve üst karara çıkılır
(KVKK metni, parola min, OriginCheck bu yolla `⚠️ ONAY BEKLİYOR`).

---

## 4.1 RED Gerekce Defteri

| AD | Neden RED | Aksiyon | Sahip |
|---|---|---|---|
| AD-09 | parola roundtrip testi okunmadı | auth test oku/çalıştır | qa-engineer |
| AD-10 | Argon2id testi okunmadı | auth test | qa-engineer |
| AD-11 | pepper fail-closed testi yok | test yaz | qa-engineer |
| AD-12 | yanıt başlık testi yok | browser/header testi | qa-engineer |
| AD-13 | başlık (nonce) yanıt testi yok | header testi | qa-engineer |
| AD-15 | rate-limit testi yok/koşturulmadı | test yaz + çalıştır | qa-engineer |
| AD-16 | kripto taraması çalıştırılmadı | MD5/SHA-1/DES/ECB taraması | security-engineer |
| AD-17 | hardcoded secret taraması yok | env/tarama (değerler yazılmaz) | security-engineer |

**Kural**: her RED satırında aksiyon + sahip + hedef tarih yoksa satır eksik sayılır
(surucu §6: BLOCK devam eder). Hedef tarih bu turda yazılmaz — CI/planlama sahibindedir.

---

## 5. Zaman Damgasi

| Kayıt | Tarih | Sonuç |
|---|---|---|
| İlk doğrulama (17 AD) | 2026-10-15 | 8 GREEN · 7 RED · 1 OPEN · 1 kısmi |
| Test koşturması | — | YAPILMADI (CI devri) |
| OriginCheck (P-09f) kararı | — | OPEN (§6) |

---

## 6. Acik Sorular (open_questions)

1. **OriginCheck fail-open** — `OriginCheckMiddleware.php:39-41` boş Origin'de
   geçiyor; ADR-010 katman 3 (anayasa P-09f) 403 istiyor → **C2 çelişki**,
   `⚠️ ONAY BEKLİYOR` (AD-14 OPEN).
2. **Parola min uzunluk** — OWASP 12+ önerisi vs repo 8 (anayasa P-01c,
   `index.md:21/49`) → `⚠️ ONAY BEKLİYOR`.
3. **KVKK saklama metni** — politika metni üretilmedi (`policies-kapsam.md` §3) →
   üst karar, K019-comilance sahipliğinde.
4. **JWT stub** — `validateJwtToken` hep null döner (anayasa §15 U-5) →
   [[../authentication/index]] node'una devir.
5. **`.ai/CLAUDE.md §Security` başlığı yok** — 78 başlık/0 isabet (2026-10-15);
   index.md §1 atfı `⚠️`, anayasa §4 notunda kayıtlı (U-2).
6. **Parola karmaşıklık/sözlük kontrolü** — kod kanıtı yok (anayasa U-6) → RED.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
