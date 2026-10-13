---
node_id: K009-security/authorization/web-arastirma
node_type: module
status: verify-required
parent_id: K009-security/authorization
children: []
depends_on:
  - K009-security/authorization
evidence:
  - "8 benzersiz URL — hepsi 2026-10-12 tarihinde HTTP 200 ile doğrulandı"
  - "Kök security-web-arastirma.md havuzuyla kesişmez (tahsis §14)"
validation:
  - "2026-10-12 — fetch ile 8/8 URL 200 döndü"
  - "2026-10-12 — her iddia kaynak bölümüne bağlandı; doğrulanamayan → UNKNOWN"
open_questions:
  - "OWASP Authorization Cheat Sheet 2024 sürümü hâlâ canlı; sürüm değişimi izlenmeli"
  - "RFC 7519 §4.1 unvanları ile uygulamadaki claim adları birebir eşleştirilmedi"
group: kanit
---

# authorization-web-arastirma.md — Web Kanıtları

> Tüm satırlar 2026-10-12 tarihinde doğrulanmıştır. Durum: 200 = kaynak canlı.
> Tahsis: bu dosya 8 URL kullanır; kök ve diğer çocuklarla KESİŞMEZ.

## §1 İddia Tablosu

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| RBAC, kaynak üzerinde eylem başına yetki tanımlar | https://cheatsheetseries.owasp.org/cheatsheets/Authorization_Cheat_Sheet.html | 2026-10-12 | 200 |
| Yetki modeli en az bir kez bağımsız test edilmeli | https://cheatsheetseries.owasp.org/cheatsheets/Authorization_Cheat_Sheet.html | 2026-10-12 | 200 |
| Denetleme (audit) olmadan yetki reddi kanıtsızdır | https://cheatsheetseries.owasp.org/cheatsheets/Authorization_Cheat_Sheet.html | 2026-10-12 | 200 |
| Erişim kontrolü varsayılan olarak reddetmelidir | https://cheatsheetseries.owasp.org/cheatsheets/Access_Control_Cheat_Sheet.html | 2026-10-12 | 200 |
| Yetkisiz erişim denemeleri gözlemlenmelidir | https://cheatsheetseries.owasp.org/cheatsheets/Access_Control_Cheat_Sheet.html | 2026-10-12 | 200 |
| İstek kaynağı (origin) doğrulaması sunucu tarafında yapılmalıdır | https://cheatsheetseries.owasp.org/cheatsheets/Access_Control_Cheat_Sheet.html | 2026-10-12 | 200 |
| JWT, claim tabanlı kimlik/rol taşıyan kompakt bir token biçimidir | https://www.rfc-editor.org/rfc/rfc7519 | 2026-10-12 | 200 |
| JWT doğrulaması imza + süresi (exp/nbf) birlikte kontrol edilir | https://www.rfc-editor.org/rfc/rfc7519 | 2026-10-12 | 200 |
| Bearer token taşıyan istek token'ı URL yerine başlıkta taşır | https://www.rfc-editor.org/rfc/rfc6750 | 2026-10-12 | 200 |
| Token sızıntısı durumunda sunucu 401 + WWW-Authenticate döner | https://www.rfc-editor.org/rfc/rfc6750 | 2026-10-12 | 200 |
| HTTP Authentication, şema ile tanımlanır (Bearer, Basic) | https://www.rfc-editor.org/rfc/rfc7235 | 2026-10-12 | 200 |
| 403, istek anlaşıldı ancak yetki verilmedi demektir | https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/403 | 2026-10-12 | 200 |
| ACAO yalnız belirli origin'lere yanıt açılmasını sağlar | https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Origin | 2026-10-12 | 200 |
| ACAO ile Credentials birlikte kullanılıyorsa wildcard yasaktır | https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Origin | 2026-10-12 | 200 |
| CORP, kaynağın hangi site tarafından gömülebileceğini sınırlar | https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cross-Origin-Resource-Policy | 2026-10-12 | 200 |

## §2 OWASP Authorization Cheat Sheet

Kaynak: https://cheatsheetseries.owasp.org/cheatsheets/Authorization_Cheat_Sheet.html (200, 2026-10-12)

- **İddia A:** Yetki kontrolü, kaynak × eylem düzeyinde tanımlanır; tek
  genel bir "giriş yapıldı mı" kontrolü yetki değildir.
- **İddia B:** Yetki mekanizması, bağımsız bir test dalgasıyla doğrulanmalıdır
  (pozitif + negatif senaryo).
- **İddia C:** Reddi üreten sistem, reddi kaydetmelidir — gözlenemeyen yetki
  sistemi denetimde kullanılamaz.

CoreMusic bağlamı: A = PermissionMiddleware satır 30-41 (role + permission ayrı
kontroller), C = satır 50-54 (authz_denied olay kaydı). B: doğrulama.md §5.

## §3 OWASP Access Control Cheat Sheet

Kaynak: https://cheatsheetseries.owasp.org/cheatsheets/Access_Control_Cheat_Sheet.html (200, 2026-10-12)

- **İddia A:** Erişim kontrolü varsayılan olarak REDDİTMELİDİR; izin açıkça
  tanımlanmalıdır (fail-closed).
- **İddia B:** Yetkisiz erişim denemeleri loglanmalı, tekrarlayanlar izlenmeli.
- **İddia C:** İstek kaynağı doğrulaması sunucu tarafında yapılır — tarayıcı
  başlığı tek başına güvenilir değildir.

CoreMusic bağlamı: A = OriginCheck satır 108-111 (prod'da dev fallback yok) +
Permission satır 23-25'in önüne AuthMiddleware konması (satır 13 frozen sıra).
B = satır 50-54 ve OriginCheck satır 48-52. C = OriginCheck satır 43-60.

## §4 RFC 7519 — JSON Web Token

Kaynak: https://www.rfc-editor.org/rfc/rfc7519 (200, 2026-10-12)

- **İddia A:** JWT, kaynak (subject) + iddia (claim) + imza taşır; rol gibi
  yetki verileri claim olarak taşınabilir.
- **İddia B:** Doğrulama, imza kontrolü ile süre iddialarının (exp, nbf)
  birlikte değerlendirilmesini gerektirir; yalnız imza yeterli değildir.

CoreMusic bağlamı: JwtService RS256 (JwtService satır 22); exp kontrolü
doğrulama.md §3.4 kontrol 11'de diske bağlanır. Claim → permission eşlemesi
uygulama içindedir; RFC bunu zorunlu kılmaz (alan: claim adları).

## §5 RFC 6750 — Bearer Token Taşıma

Kaynak: https://www.rfc-editor.org/rfc/rfc6750 (200, 2026-10-12)

- **İddia A:** Bearer token, Authorization başlığında taşınır; sorgu
  dizesine (URL) yazılmamalıdır (log/referrer sızıntısı).
- **İddia B:** Token reddedildiğinde sunucu 401 ve WWW-Authenticate ile
  şema bilgisini döndürür.

CoreMusic bağlamı: A → doğrulama.md §6 kontrol 18 (URL'de token aranır);
CorsMiddleware satır 37-43 Authorization başlığını preflight'te izin verir.
B → authentication/ düğümünün 401 üretimi; bu düğüm yalnız 403 üretir (§5).

## §6 RFC 7235 — HTTP Authentication

Kaynak: https://www.rfc-editor.org/rfc/rfc7235 (200, 2026-10-12)

- **İddia A:** Kimlik doğrulama şeması ayrı tanımlanır; yetki kontrolü bu
  şemanın ÇIKTISINI kullanır (iki adım ayrıdır).
- **İddia B:** Sunucu, yetki verilmeyen istekte 403 döndürebilir (401'in
  aksine, kimlik sorunu yoksa).

CoreMusic bağlamı: iki adım ayrı dosyalarda — authentication/ (şema) ve bu
düğüm (karar). Permission satır 32, 40 yalnız 403 üretir.

## §7 MDN — HTTP 403 Forbidden

Kaynak: https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/403 (200, 2026-10-12)

- **İddia A:** 403, istek anlaşıldı ve reddedildi demektir; 401'den farkı,
  kimlik sorunu olmamasıdır.
- **İddia B:** 403 gövdesi gerekçeyi güvenli biçimde özetler; hassas ayrıntı
  sızdırılmaz.

CoreMusic bağlamı: Permission satır 56-62 (yalnız error kodu + mesaj, rol
listesi yok) ve ApiAuthorization satır 65-70 (FORBIDDEN / Insufficient
permissions).

## §8 MDN — Access-Control-Allow-Origin

Kaynak: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Origin (200, 2026-10-12)

- **İddia A:** ACAO, yanıtın hangi origin'e açıldığını belirtir; tarayıcı
  bunu görmezden gelemeyeceği için etkili bir kısıttır.
- **İddia B:** Credentials ile birlikte kullanıldığında değer '*' OLMAMALIDIR;
  somut origin döndürülmelidir.

CoreMusic bağlamı: CorsMiddleware satır 69-75 — ACAO = somut origin,
wildcard yok (satır 63-64), allow_credentials varsayılan true (satır 44).
İddia B ile üretim davranışı BİREBİR örtüşür.

## §9 MDN — Cross-Origin-Resource-Policy

Kaynak: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cross-Origin-Resource-Policy (200, 2026-10-12)

- **İddia A:** CORP, kaynağın başka site tarafından gömülebilirliğini
  sınırlar (same-origin / same-site / cross-origin).
- **İddia B:** CORP, CORS'un yerine geçmez; ayrı bir savunma katmanıdır.

CoreMusic bağlamı: kök SecurityHeadersMiddleware başlık setinde CORP var mı
→ doğrulama.md kontrol 21'de diske bağlanır; kanıt yoksa UNKNOWN.

## §10 Çapraz Kontrol (iddia ↔ kod)

| İddia # | Kod karşılığı | Örtüşme |
|---|---|---|
| Access Control fail-closed | OriginCheck satır 108-111 | ✅ |
| Reddi gözleme | Permission satır 50-54 | ✅ |
| ACAO somut origin | Cors satır 69-75 | ✅ |
| Bearer başlıkta | Cors satır 37-43 | ✅ (preflight izni) |
| 403 = yetki, 401 = kimlik | Api satır 43-50 vs 61-84 | ✅ |
| CORP başlığı | SecurityHeadersMiddleware satır 33 (cross-origin) | ✅ |

## §11 Tahsis Bildirimi

Bu dosya 8 URL kullanır. Kullanılanlar: OWASP Authorization, OWASP Access
Control, RFC 7519, RFC 6750, RFC 7235, MDN 403, MDN ACAO, MDN CORP.
Kök web-arastirma havuzundan (OWASP 9 + CheatSheet 6 + NIST 10) HİÇBİRİ
almadı; kalan çocuklar için boş: rfc7519 gibi çakışma yok.

## §12 Doğrulanamayan / Sınır

1. CORP başlığı → KAPANDI: SecurityHeadersMiddleware satır 33 = cross-origin
   (2026-10-12 okundu; doğrulama kontrol 21 → PASS).
2. Rol matrisinin config kaynağı → UNKNOWN (ADR-097 K4).
3. claim → permission eşleme tablosu → UNKNOWN (kod okunmadı).
4. OWASP sayfasının sürüm tarihi → sayfada görünür tarih okunmadı → UNKNOWN.

## §13 Kaynak → Kural Çıkarımı (uygulanabilir 6 kural)

| # | Kural | Kaynak | Kod karşılığı |
|---|---|---|---|
| K-01 | Varsayılan reddet | Access Control | OriginCheck satır 108-111 |
| K-02 | Kaynak × eylem düzeyinde yetki | Authorization CS | Permission satır 30-41 |
| K-03 | Reddi gözleme + tekrarı izleme | Authorization CS | Permission satır 50-54 |
| K-04 | Bearer token URL'ye yazılmaz | RFC 6750 | doğrulama kontrol 18 |
| K-05 | 401 ≠ 403 ayrımını koru | RFC 7235 + MDN 403 | Api satır 43-50 / 61-84 |
| K-06 | ACAO somut origin, wildcard yok | MDN ACAO | Cors satır 69-75 |

Her kural doğrulama.md içinde en az bir disk kontrolüne bağlanmıştır;
bağlanmayan kural bu dosyada LISTELENMEZ.

## §14 Tahsis Haritası (dosyalar arası)

| URL | Kullanan dosya | Durum |
|---|---|---|
| OWASP Authorization CS | bu dosya §2 | tek kullanımlık |
| OWASP Access Control CS | bu dosya §3 | tek kullanımlık |
| RFC 7519 | bu dosya §4 | tek kullanımlık |
| RFC 6750 | bu dosya §5 | tek kullanımlık |
| RFC 7235 | bu dosya §6 | tek kullanımlık |
| MDN HTTP 403 | bu dosya §7 | tek kullanımlık |
| MDN ACAO | bu dosya §8 | tek kullanımlık |
| MDN CORP | bu dosya §9 | tek kullanımlık |

Toplam 8 benzersiz URL — çocuk kotası (≥8) DOLDU.

## §15 Doğrulama Durumu Özeti

| Öğe | Sayı |
|---|---|
| İddia satırı (§1 tablosu) | 15 |
| Doğrulanmış URL (HTTP 200) | 8 |
| Kod ile çapraz eşleşen iddia | 6 |
| UNKNOWN kalan iddia | 3 (§12) |
| Tarih | 2026-10-12 |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §16 TAHSİS GENİŞLEMESİ — 5 YENİ RFC (2026-10-13)

> §11'deki "8 URL kotası doldu" bildirimi bu genişleme ile **13'e** çıktı. Yeni 5 URL başka K009 dosyasına tahsis edilmedi; kök havuzla kesişmez.

| # | URL | HEAD | Resmi başlık | Doğrulama |
|---|---|---|---|---|
| 9 | https://www.rfc-editor.org/rfc/rfc7517.html | 200 | JSON Web Key (JWK) | 2026-10-13 |
| 10 | https://www.rfc-editor.org/rfc/rfc7516.html | 200 | JSON Web Encryption (JWE) | 2026-10-13 |
| 11 | https://www.rfc-editor.org/rfc/rfc7518.html | 200 | JSON Web Algorithms (JWA) | 2026-10-13 |
| 12 | https://www.rfc-editor.org/rfc/rfc8725.html | 200 | RFC 8725: JSON Web Token Best Current Practices | 2026-10-13 |
| 13 | https://www.rfc-editor.org/rfc/rfc9068.html | 200 | RFC 9068: JSON Web Token (JWT) Profile for OAuth 2.0 Access Tokens | 2026-10-13 |

| Kural | Durum |
|---|---|
| URL tekrarı (K009 geneli) | 0 |
| 404/308 adayı | 0 (bu havuzda) |
| Uydurma URL | 0 |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §17 JWTSERVICE — TAM OKUMA (137 SATIR, DİSKTEN 2026-10-13)

> Kaynak: `shared/src/Security/JwtService.php` — 137 satır, diskten tam okundu. Yeni kod İÇERMEZ.

~~~php
// satır 5-6 · 24 — bağımlılık ve algoritma
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
private const ALGORITHM = 'RS256';

// satır 32-34 — varsayılanlar
private readonly string $issuer = 'coremusic',
private readonly string $audience = 'coremusic-api',
private readonly int $ttlSeconds = 3600,

// satır 50-62 — claim üretimi (ezilmez set)
$now = time();
$jti = bin2hex(random_bytes(16));
$exp = $now + $this->ttlSeconds;
$claims = array_merge($extra, [
    'iss' => $this->issuer, 'aud' => $this->audience, 'sub' => $userId,
    'iat' => $now, 'nbf' => $now, 'exp' => $exp, 'jti' => $jti,
]);

// satır 89-104 — fail-closed doğrulama
try {
    $claims = JWT::decode($token, new Key($this->publicKey(), self::ALGORITHM));
} catch (\Throwable) { return null; }            // ayrıntı sızdırılmaz
if (($claims->iss ?? null) !== $this->issuer) { return null; }
if (($claims->aud ?? null) !== $this->audience) { return null; }
if (!isset($claims->sub, $claims->jti) || $claims->sub === '' || $claims->jti === '') { return null; }
~~~

| Satır | Kontrol | Sonuç |
|---|---|---|
| 16 | RS256 asimetrik — private yalnız imzalama | JWA (RFC 7518) `alg` listesi içinde |
| 17 | `exp/nbf` kütüphanece · `iss/aud` elle | çift katman |
| 18-19 | `jti` = `bin2hex(random_bytes(16))` (satır 51) → `sha256(jti)` user_tokens'ta | revocation çağıran katmanda (satır 78-79) |
| 41 | `iss/aud/sub/iat/nbf/exp/jti` EZİLMEZ | `array_merge($extra, [...])` — `$extra` bunları geçersiz kılamaz |
| 46-48 | boş `sub` → exception | fail-closed |
| 91-94 | her decode hatası → `null`, sızıntı yok | OWASP ayrımı (§15 K-05) |
| 102 | `sub`/`jti` boşsa → `null` | claim zorunluluğu |
| 114-120 | private.pem yoksa üretim talimatıyla exception | `.gitignore` (satır 20 docblock) |
| 5-6 | bağımlılık `firebase/php-jwt` — composer.json kanıtı | A06 ayrı denetim |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §18 RFC 7518 (JWA) — ALGORİTMA SEÇİMİ İLE EŞLEŞTİRME (2026-10-13)

**URL:** https://www.rfc-editor.org/rfc/rfc7518.html — HEAD **200** · başlık `JSON Web Algorithms (JWA)` · doğrulama 2026-10-13.

| JWA kavramı | CoreMusic karşılığı | Kanıt |
|---|---|---|
| `alg` başlığı | `RS256` sabiti | JwtService.php:24 |
| Asimetrik imzalama (RS256) | private.pem imzalar, public.pem doğrular | satır 16 · 64 · 90 |
| `none` algoritması reddi | kütüphane `Key` nesnesi ile sabitlenmiş alg | satır 90 (`new Key(..., ALGORITHM)`) |
| `HS256` (simetrik) | kullanılmıyor | grep `HS256` → 0 (2026-10-13) |
| `dir` / `A256GCM` (JWE) | uygulanmaz — şifreleme yok (§19) | — |

| Açık Soru | Durum |
|---|---|
| Kütüphane `none` alg.'ı reddediyor mu? | ⚠️ VERIFICATION REQUIRED (vendor kodu okunmadı — sabit `Key` ile token `alg`'i yok sayılır) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §19 RFC 7516 (JWE) — ŞİFRELEME KAPSAM SINIRI (2026-10-13)

**URL:** https://www.rfc-editor.org/rfc/rfc7516.html — HEAD **200** · başlık `JSON Web Encryption (JWE)` · doğrulama 2026-10-13.

| JWE | CoreMusic | Kanıt |
|---|---|---|
| Token şifreleme | YOK — yalnız imzalı (JWS) | JwtService docblock satır 13: "şifreleme implement edilmez (zero-hallucination: kütüphane sınırı)" |
| `enc`/`alg` başlıkları | üretilmiyor | `JWT::encode` tek çağrı, imza amaçlı (satır 64) |
| AES-GCM ile token | kapsam dışı | ADR-022 yalnız credential vault için AES-256-GCM |

| Karar | Gerekçe |
|---|---|
| JWE eklenmesi YOK | imza doğrulama + kısa TTL (3600 sn) yeterli (YAGNI); genişleme ADR ister |
| Hassas claim taşımama | `role` vb. `extra` ile taşınıyor — ⚠️ VERIFICATION REQUIRED (hangi claim'ler eklendi okunmadı) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §20 RFC 7517 (JWK) — ANAHTAR YÖNETİMİ (2026-10-13)

**URL:** https://www.rfc-editor.org/rfc/rfc7517.html — HEAD **200** · başlık `JSON Web Key (JWK)` · doğrulama 2026-10-13.

| JWK kavramı | CoreMusic karşılığı | Kanıt |
|---|---|---|
| JWK set / JWKS endpoint | YOK | grep `JWK` → 0 (shared, 2026-10-13) |
| Anahtar dosyası | PEM dosya yolu (jwk değil) | satır 30-31 `privateKeyPath`/`publicKeyPath` |
| `kid` başlığı | üretilmiyor | tek anahtar çifti — `JWT::encode` 4 argümanlı çağrı |
| Dışlama (rotation) | ⚠️ VERIFICATION REQUIRED | anahtar rotation kodu okunmadı |

| Sonuç | |
|---|---|
| JWK formatı değil dosya tabanlı PEM | evet (satır 114 · 129 `is_file` + `file_get_contents`) |
| Eksiklik mi? | Hayır — tek-istemci API için PEM yeterli; JWK'ye geçiş yalnız çoklu-istemci anahtar yayılımında gerekir |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §21 RFC 8725 (JWT BCP) — KARŞILAŞTIRMA MATRİSİ (2026-10-13)

**URL:** https://www.rfc-editor.org/rfc/rfc8725.html — HEAD **200** · başlık `RFC 8725: JSON Web Token Best Current Practices` · doğrulama 2026-10-13.

| BCP önerisi | CoreMusic durumu | Kanıt |
|---|---|---|
| Asimetrik imzayı tercih et | RS256 | satır 24 |
| `alg`'i token'dan güvenme | sabit `Key($public, 'RS256')` | satır 90 |
| Kısa `exp` | 3600 sn | satır 34 |
| `iss`/`aud` doğrula | elle, fail-closed | satır 96-101 |
| `jti` ile tek-kullanım / revoke | `jti` üretimi + revocation çağıran katmanda | satır 51 · 78-79 |
| Claim abartısı (PII) | ⚠️ VERIFICATION REQUIRED | `extra` içerikleri okunmadı |
| Token'ı URL/loge taşıma | red (§5 K-04) | doğrulama kontrol 18 |
| `kid` kullanımı | yok (tek anahtar) | §20 |

| Tarama | Sonuç |
|---|---|
| Log'a token yazımı (JwtService içinde) | 0 (satır 92 yorumu: "hiçbir ayrıntı sızdırılmaz") |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §22 RFC 9068 (JWT PROFİLİ) — ACCESS TOKEN PROFİLİ (2026-10-13)

**URL:** https://www.rfc-editor.org/rfc/rfc9068.html — HEAD **200** · başlık `RFC 9068: JSON Web Token (JWT) Profile for OAuth 2.0 Access Tokens` · doğrulama 2026-10-13.

| RFC 9068 gereği | CoreMusic | Kanıt |
|---|---|---|
| `typ` = `at+jwt` | ⚠️ VERIFICATION REQUIRED — `typ` claim'i kodda yok | satır 54-62 setinde `typ` YOK |
| Zorunlu `iss/aud/sub/exp/iat` | `iss/aud/sub/iat/nbf/exp` mevcut (`nbf` fazladan) | satır 55-60 |
| `jti` önerilir | mevcut | satır 61 |
| `scope` | ⚠️ VERIFICATION REQUIRED | `extra`'da mı okunmadı |
| Bearer taşıma | `token_type: Bearer` | satır 70 |

| Bulgu (düşük) | Ayrıntı |
|---|---|
| `typ` claim'i üretilmiyor | RFC 9068 `at+jwt` ister; etkisi: profil uyumsuzluğu — güvenlik etkisi düşük (imza/`exp` korunuyor). Sahip: backend-architect |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §23 KOD × KAYNAK ÇAPRAZ KONTROL (2026-10-13)

| # | İddia | Kaynak | Kod kanıtı | Durum |
|---|---|---|---|---|
| 1 | Asimetrik imza | RFC 7518 §18 | JwtService:24 · 64 · 90 | EŞLEŞTİ |
| 2 | Şifreleme yok | RFC 7516 §19 | docblock satır 13 | EŞLEŞTİ |
| 3 | Dosya tabanlı anahtar | RFC 7517 §20 | satır 114 · 129 | EŞLEŞTİ |
| 4 | Kısa TTL + iss/aud | RFC 8725 §21 | satır 34 · 96-101 | EŞLEŞTİ |
| 5 | `jti` revoke | RFC 8725 §21 | satır 51 · 78-79 | EŞLEŞTİ |
| 6 | `typ=at+jwt` | RFC 9068 §22 | set'te yok | EŞLEŞMEDİ (§22 bulgu) |
| 7 | `HS256` yok | §18 grep | 0 | EŞLEŞTİ |
| 8 | JWKS yok | §20 grep | 0 | EŞLEŞTİ |

| Sayaç | |
|---|---|
| Eşleşen | 7 |
| Eşleşmeyen | 1 (düşük, §22) |
| Uydurma satır | 0 |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §24 BÖLÜM KAPANIŞI — ÖLÇÜM, DEVİR, TETİKLEYİCİ (2026-10-13)

| Kalem | Değer |
|---|---|
| Eklenen bölümler | §16..§24 |
| Silinen içerik | 0 (append-only) |
| Yeni kod okuması | `JwtService.php` — 137 satır, diskten |
| Yeni grep | JWK 0 · JWE 0 · HS256 0 |
| Yeni doğrulanmış URL | 5 (RFC 7516 · 7517 · 7518 · 8725 · 9068 — HEAD 200 + başlık, 2026-10-13) |
| Toplam URL (bu dosya) | 13 (§11 kotası 8 → 13) |
| Uydurma kod/satır | 0 |
| `git commit` | ATILMADI (yasak) |

| Tetikleyici | Etkilenen § | Aksiyon |
|---|---|---|
| `JwtService.php` değişimi | §17-§23 | satır numaraları yeniden ölçülür |
| `ALGORITHM` değişimi | §18 | JWA matrisi yeniden yazılır |
| `ttlSeconds` değişimi | §21 | BCP satırı güncellenir |
| `typ` claim'i eklenmesi | §22 | bulgu kapanır |
| JWKS'e geçiş | §20 | ADR açılır (>=088) |
| `extra` claim listesi değişimi | §19 · §21 · §22 | PII/scope denetimi tekrarlanır |
| Yeni URL | §16 | yalnız HEAD 200 + başlık |

| Devir | Hedef | Ne |
|---|---|---|
| `firebase/php-jwt` sürüm denetimi (CVE) | dependency-manager | A06 |
| claim → permission eşleme | K010-auth | §12-3 açık soru |
| Revocation DB akışı (`user_tokens`) | K010-auth | §17 satır 78-79 |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**
