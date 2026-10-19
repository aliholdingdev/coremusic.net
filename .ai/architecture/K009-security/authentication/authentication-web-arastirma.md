---
node_id: K009-security/authentication/authentication-web-arastirma
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/authentication/authentication-kapsam
evidence: "9 benzersiz URL (HTTP 200, 2026-10-12) + kod kanıtı xref"
validation: "≥8 benzersiz URL · tekrarsız (kök havuz hariç) · 404 yok"
open_questions: "MFA kaynağı K010'a devredildi · OAuth ucu kod kanıtı kısmi"
group: kanit
---

# K009 · authentication · Web Araştırması

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-12 · Havuz: rfc-editor(2) + php.net(5) + MDN(2) = 9 URL.
> Tekrar yasağı: bu URL'ler kök dosyalarda KULLANILMAZ (tahsis §14, security-web-arastirma.md).

## §1 KAYNAK TABLOSU (9 URL)

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| OAuth 2.0 çerçevesi (RFC 6749) yetkilendirme/erişim akışlarını tanımlar | https://www.rfc-editor.org/rfc/rfc6749.html | Tarih: 2026-10-12 | HTTP 200 |
| PKCE (RFC 7636) code interceptor saldırılarına karşı kod doğrulaması ekler | https://www.rfc-editor.org/rfc/rfc7636.html | Tarih: 2026-10-12 | HTTP 200 |
| password_needs_rehash: parametre eskimişse yeniden hash gerektiğini söyler | https://www.php.net/manual/en/function.password-needs-rehash.php | Tarih: 2026-10-12 | HTTP 200 |
| password_get_info: hash algoritması/parametrelerini verir | https://www.php.net/manual/en/function.password-get-info.php | Tarih: 2026-10-12 | HTTP 200 |
| password_algos: desteklenen hash algoritmalarını listeler | https://www.php.net/manual/en/function.password-algos.php | Tarih: 2026-10-12 | HTTP 200 |
| random_int: kriptografik güvenli tam sayı | https://www.php.net/manual/en/function.random-int.php | Tarih: 2026-10-12 | HTTP 200 |
| hash_hmac: HMAC üretimi (pepper önişleme) | https://www.php.net/manual/en/function.hash-hmac.php | Tarih: 2026-10-12 | HTTP 200 |
| WWW-Authenticate başlığı kimlik kanıtı isteğini taşır | https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/WWW-Authenticate | Tarih: 2026-10-12 | HTTP 200 |
| HTTP 401 Unauthorized: kimliksiz istek yanıtı | https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status/401 | Tarih: 2026-10-12 | HTTP 200 |

## §2 KAYNAK — KOD X-REF

| Kaynak (§1) | Kural | Kod Kanıtı | Eşleşme |
|---|---|---|---|
| RFC 6749 | Token uçları korumalı olmalı | JwtService (Bearer) · OAuthManager·29/190 | KISMİ (sağlayıcı K010) |
| RFC 7636 | Auth code değişiminde kod doğrulama | — (PKCE kanıtı YOK) | ⚠️ VERIFICATION REQUIRED |
| password_needs_rehash | Parametre artınca yeniden hash | — (rehash akışı YOK) | ⚠️ VERIFICATION REQUIRED |
| password_get_info | Algoritma denetimi | Password.php·26 (password_get_info çağrısı iddiası) | ✅ KANITLI |
| password_algos | Argon2id destek kontrolü | Password.php·43 PASSWORD_ARGON2ID | ✅ EŞLEŞİYOR |
| random_int | Güvenli rastgele tam sayı | UuidV7 (random tabanlı) · random_bytes kanıtları | ✅ (doğrudan random_int kullanımı okunmadı → kısmi) |
| hash_hmac | Pepper önişleme | Password.php·41 hash_hmac('sha256',...) | ✅ EŞLEŞİYOR |
| WWW-Authenticate | 401 başlığı charset ile | — (başlık yayını kanıtı YOK) | ⚠️ VERIFICATION REQUIRED |
| 401 | Kimliksiz istek | AuthMiddleware pasif akış (red metodu okunmadı) | ⚠️ Kısmi |

## §3 OWASP/NIST BAĞLANTILARI (KÖK HAVUZDAN KİMLİK NOTU)

| Kaynak (kök dosya) | Kimlik Etkisi | Not |
|---|---|---|
| Password Storage CS (root §3) | Argon2id zorunlu | bu çocukta doğrulanacak |
| Authentication CS (root §3) | charset/hata mesajı kuralları | hata mesajı sızıntısı ⚠️ |
| 800-63-3 (root §4) | AAL seviyeleri | K010 devri |
| Top10 A07 (root §8) | Auth Failures | R-12/R-01 eşlemesi |

| Tekrar Uyarısı | |
|---|---|
| Bu URL'ler bu dosyada YENİDEN yazılmaz | root web-arastira §2/§3/§4 havuzu |
| Çocuk kendi havuzunu genişletirse | yeni URL doğrula (HTTP 200) → kendi tablosuna ekle |

## §4 DOĞRULANAMAYANLAR (BU ÇOCUK)

| İddia | Durum |
|---|---|
| PKCE uygulanıyor | ⚠️ KANIT YOK |
| password_needs_rehash akışı var | ⚠️ KANIT YOK |
| WWW-Authenticate başlığı yayında | ⚠️ KANIT YOK |
| 401 + JSON hata sözleşmesi | ⚠️ KANIT YOK |
| MFA/2FA | UNKNOWN (K010) |
| Account lockout (kilitli hesap) | UNKNOWN (rate-limiting ile örtüşür) |

## §5 KAYNAK ÖNCELİĞİ (BU ÇOCUK)

| Öncelik | Kaynak | Kullanım |
|---|---|---|
| 1 | OWASP CheatSheet (kök havuz) | parola/hata kuralları |
| 2 | IETF RFC (bu havuz) | OAuth/PKCE sözleşmesi |
| 3 | PHP Manual (bu havuz) | fonksiyon davranışı kanıtı |
| 4 | MDN (bu havuz) | HTTP semantiği (401/WWW-Authenticate) |

## §6 KANIT ZİNCİRİ

| Adım | İşlem | Sonuç |
|---|---|---|
| 1 | Adaylar HTTP GET | 9/9 → 200 (2026-10-12) |
| 2 | Kök havuzla kesişim | 0 (tekrar yok) |
| 3 | Kod eşlemesi | 4 ✅ · 5 ⚠️ |
| 4 | K010 devri | MFA/OAuth sağlayıcı mimarisi |

## §7 SON KONTROL

| Kontrol | Beklenen | Durum |
|---|---|---|
| Benzersiz URL | ≥8 | 9 ✅ |
| Dolgu | 0 | 0 ✅ |
| Uydurma iddia | 0 (işaretli boşluklar hariç) | ✅ |
| Başlık formatı | ## §N | §1..§7 ✅ |
| group | kanit | ✅ |

## §8 KAYNAK-BAŞLIK DETAY ÇIKARIMLARI

| # | URL (kısa) | Çıkarım | Doğrudan K009 Etkisi |
|---|---|---|---|
| 1 | rfc6749 | OAuth2: authorization code, refresh token, istemci kimliği | Bearer doğrulama yüzeyi (API) |
| 2 | rfc7636 | PKCE: code_verifier/challenge | Mobil/SPA uçları için ⚠️ kanıt yok |
| 3 | password-needs-rehash | Argon2id parametresi değişince rehash şart | Parola politikası güncelleme boşluğu |
| 4 | password-get-info | Algoritma adı + options okunabilir | Denetim/test için kullanılabilir |
| 5 | password-algos | PASSWORD_ARGON2ID destek listesinde | Runtime uyum kontrolü |
| 6 | random-int | GMP/openssl tabanlı güvenli int | Doğrudan kullanım kanıtı yok |
| 7 | hash-hmac | hash_hmac(algo, veri, anahtar) | Pepper önişlemenin doğru API'si |
| 8 | WWW-Authenticate | realm + auth parametresi | 401 yanıtının başlık sözleşmesi |
| 9 | 401 Status | Yetkilendirme (kimlik) hatası status kodu | API reddi status kodu |

## §9 OAUTH AKIŞ NOTU (K010 SINIRINDA)

| Adım | RFC 6749 Kuralı | K009'da Durum |
|---|---|---|
| Authorization code | Kısa ömürlü, tek kullanımlık | Sağlayıcı K010 (OAuthManager·29/190 yalnız tüketici) |
| Token dağıtımı | TLS zorunlu | deployment (⚠️ kanıt) |
| Refresh token | Yenileme + iptal | ⚠️ KANIT YOK |
| PKCE | Public istemcilerde zorunlu (7636) | ⚠️ KANIT YOK |
| Hata yanıtı | error/error_description | ⚠️ KANIT YOK |

## §10 PHP PAROLA FONKSİYON MATRİSİ

| Fonksiyon | Kaynak (§1) | K009 Kullanımı | Kanıt |
|---|---|---|---|
| password_hash | kök (dogrulama havuzu) | Password.php·43 | ✅ |
| password_verify | kök | Password.php·54-59 | ✅ |
| password_get_info | 4 | Password.php·26 (iddia) | ✅ |
| password_needs_rehash | 3 | kullanılmıyor | ⚠️ boşluk |
| password_algos | 5 | dolaylı (Argon2id desteği) | ✅ |
| hash_hmac | 7 | Password.php·41 | ✅ |
| random_int | 6 | doğrudan yok (random_bytes tercih) | ⚠️ kısmi |
| hash_equals | kök | ApiKey/Csrf | ✅ |

## §11 401/WWW-AUTHENTICATE SÖZLEŞMESİ NOTU

| Öğe | Beklenen | K009 Durumu |
|---|---|---|
| 401 status | Kimlik kanıtı isteği | ⚠️ yayında mı okunmadı |
| WWW-Authenticate: Bearer | realm + charset | ⚠️ kanıt yok |
| Hata gövdesi | JSON {error} | ⚠️ kanıt yok |
| Bilgi sızıntısı | kullanıcı adı hatalı vs parola hatalı ayrımı | OWASP Authentication CS → kök §3 uyarısı |

## §12 ÇAPRAZ ÇOCUK ETKİLERİ

| Etki | Giden Çocuk | Not |
|---|---|---|
| Brute-force (401 tekrarı) | rate-limiting | /login fail-closed |
| Oturum açılışında ID rotation | session-security | SessionLifecycle·60 |
| Yetki claim'leri (permissions) | authorization | AuthMiddleware·30 |
| Hash/pepper saklama sırrı | secrets | APP_PEPPER, private.pem |

## §13 DOĞRULAMA TETİKLEYİCİLERİ

| Tetikleyici | Yeniden Doğrulanacak |
|---|---|
| Password.php parametre değişimi | §10 (rehash zinciri) |
| OAuthProvider ekleme | §9 (RFC 6749/7636) |
| 401 yanıt biçimi değişimi | §11 |
| PHP sürüm yükseltme | password_algos desteği |

## §14 SAYISAL ÖZET

| Metrik | Değer |
|---|---|
| Benzersiz URL | 9 (HTTP 200 · 2026-10-12) |
| rfc-editor / php.net / MDN | 2 / 5 / 2 |
| 404 aday | 0 |
| Kök havuzla kesişim | 0 |
| Kod eşleşmesi ✅ | 4 |
| ⚠️/UNKNOWN boşluk | 6 (§4) |
| uydurma | 0 |

| Son Durum | authentication-web-arastirma.md · group: kanit · 9 URL |
|---|---|

## §15 URL — İDDİA TEK TEK (DENETİM SATIRLARI)

| URL | Tek Satır İddia | İşaret |
|---|---|---|
| rfc6749 | OAuth2 erişim akışları bu belgede tanımlanır | ✅ |
| rfc7636 | PKCE code_verifier zorunluluğu bu belgede tanımlanır | ✅ |
| password-needs-rehash | Algoritma/maliyet değişince true döner | ✅ |
| password-get-info | İlk dönen eleman algoritma bilgisidir | ✅ |
| password-algos | Desteklenen algoritmalar dizisi verilir | ✅ |
| random-int | Kriptografik olarak güvenli tam sayı üretir | ✅ |
| hash-hmac | HMAC veri + anahtar ile üretilir | ✅ |
| WWW-Authenticate | 401 yanıtında kimlik yöntemi duyurulur | ✅ |
| 401 Status | Yetki (kimlik) hatası için status kodu | ✅ |

## §16 UYGULAMA NOTLARI (KODA YANSIMA)

| Not | Kaynak | Aksiyon | Sahip |
|---|---|---|---|
| rehash akışı eklenmeli | password-needs-rehash | login sonrası needs_rehash kontrolü | bu çocuk (plan) |
| PKCE kanıtı toplanmalı | rfc7636 | OAuth uçlarını tara | K010 + bu çocuk ⚠️ |
| 401 başlık sözleşmesi | MDN 401/WWW-Authenticate | ResponseEmitter'da doğrula | session-security |
| random_int kullanımı | php | UuidV7/random_bytes yeterli mi değerlendir | encryption (kripto) |
| Pepper boşsa ret | hash-hmac kaynağı + kod | ✅ zaten uygulanmış | Password.php·41-44 |

| Son Durum (geniş) | |
|---|---|
| URL | 9 benzersiz · 0 tekrar · 0 dolgu |
| Bölüm | §1..§16 |
| Bağ | authentication-kapsam |
