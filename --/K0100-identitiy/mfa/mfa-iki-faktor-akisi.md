---
node_id: K0100-identitiy/mfa/mfa-iki-faktor-akisi
node_type: module
status: draft
parent_id: K0100-identitiy/mfa
children: []
depends_on:
  - K0100-identitiy
evidence: []
validation: []
open_questions: []
group: mimari
---

# mfa-iki-faktor-akisi — İkinci Faktör Akışı Derin Dalı

- **Kademe:** 3 · **Kapsam K:** K0100-identitiy · **Alt klasör:** `mfa`
- **Sınıf:** module · **Durum:** draft · **Frontmatter:** v2 §4.2 (8 alan) + `group`
- **Yetkili kaynak:** v2 §4.2 · ADR-059 (JWT lib + MFA TOTP) · ADR-013 (rate limit)
- **İmla koruması:** düğüm adı `identitiy` yazımı bilinçli korunmuştur.

## §1 — Amaç ve kapsam

Bu dosya, CoreMusic kimlik doğrulamasının **ikinci faktör (MFA / 2FA) akışını** tanımlar:
TOTP tabanlı doğrulama, kurtarma kodları, replay (yeniden kullanım) engeli, oturum
bağlama ve step-up (yeniden kimlik doğrulama) kuralları. Dosya bir **sözleşmedir** —
repoda MFA kodu bulunmadığı için (§7 ölçümü) sözleşme, mevcut session/JWT/CSRF
altyapısının üzerine **nasıl** oturacağını kod kanıtıyla bağlar.

| # | Kapsam alanı | Bu dosyanın tanımı | Kapsam dışı (devredilen) |
|---|---|---|---|
| 1 | TOTP parametreleri |issuer·period·digits·window·hash| Parola üretimi ve saklanması (K010 password) |
| 2 | İkinci faktör doğrulama | Kod kabulü, hata sayacı, kilitleme | Birinci faktör (parola) doğrulaması (K010 password) |
| 3 | Replay engeli | Aynı kodun ikinci kez kullanılmasının engellenmesi | Genel session rotasyonu (K010 session) |
| 4 | Kurtarma kodu | Üretim, tek kullanımlık tüketim, saklama biçimi | Şifreleme altyapısı (K007-cryptography) |
| 5 | Step-up | Hassas işlem öncesi yeniden doğrulama tetikleyicisi | Rol/izin kararı (K010 authorization) |
| 6 | Deneme kotası | Başarısız kod girişlerinin sınırlandırılması | Genel IP rate limit (K011 application caching/rate limit) |
| 7 | WebAuthn | Faz-2 fiziksel anahtar akışı (tasarım düzeyinde) | Donanım anahtarı üretimi (dış donanım, kapsam dışı) |
| 8 | İptal/bypass | Devre dışı bırakma koşulları ve denetim kaydı | Break-glass hesabı (ADR-058) |

## §2 — Terminoloji

| Terim | İngilizce | Tanım | Bu depodaki karşılığı |
|---|---|---|---|
| İkinci faktör | second factor | Birincilden bağımsız kanıt sınıfı | ADR-059 MFA (TOTP) |
| TOTP | Time-based One-Time Password | Zaman tabanlı tek kullanımlık kod (RFC 6238) | ADR-059'da planlanan yöntem |
| HOTP | HMAC-based One-Time Password | Sayaç tabanlı tek kullanımlık kod (RFC 4226) | TOTP'nin temel alınan MAC şeması |
| Gizli anahtar | shared secret | Sunucu ile cihaz arasında paylaşılan byte dizisi | ADR-022 AES-256-GCM saklama zemini |
| Doğrulama penceresi | validation window | Kabul edilen zaman dilimi komşuları | `window` parametresi (§9) |
| Replay | replay attack | Aynı tek kullanımlık kodun yeniden gönderilmesi | §11 ile engellenir |
| Kurtarma kodu | recovery code | TOTP erişimi kaybedildiğinde kullanılan tek kullanımlık kod | ADR-059: 10 adet |
| Step-up | step-up authentication | İşlem sırasında istenen ek doğrulama | §13 |
| Devre dışı bırakma | MFA opt-out | Kullanıcının ikinci faktörü kapatması | §15.4 — vault kararı gerekli |
| Deneme kotası | attempt limit | Yanlış kod girişlerine uygulanacak sınır | ADR-013 OTP kotası |
| Kilitleme | lockout | Kotası dolan hesabın geçici kilitlenmesi | §12.3 |
| Bypass kodu | bypass code | MFA'yı atlatan yönetici kodu | **YOK** — ADR-059/ADR-008 red |
| WebAuthn | Web Authentication API | Fiziksel/tarayıcı içi anahtarla doğrulama | §14 (Faz 2) |
| CredMan | Credential Management API | Tarayıcıda kayıtlı kimlik bilgisi yönetimi | §14.3 |
| Amaca özgü | purpose-bound claim | Yalnız belirli işlem için geçerli hak | TOTP doğrulama sonrası oturum bayrağı |
| Yenileme | re-enrollment | Cihaz kaybında ikinci faktörün yeniden kurulması | §15.3 |
| Denetim kaydı | audit event | Doğrulama denemesinin kayda geçmesi | K013-audit |
| Replay sayacı | used-code watermark | Son kullanılan kodun işaretleme penceresi | §11.2 |

## §3 — Mimari yerleşim (kademe-3 konumu)

| Katman | Bu dosyanın gördüğü arayüz | Sorumluluk |
|---|---|---|
| Presentation | Login formu + ikinci faktör adımının ekranı | Kod girişi arayüzü, hata mesajı |
| Application | AuthService içindeki MFA doğrulama adımı | Doğrulama akışının sıralaması, hata sayaçları |
| Domain | TOTP üretimi/doğrulama saf mantığı, kurtarma kodu değeri | Zaman penceresi matematiği, tek kullanımlık semantik |
| Infrastructure | Gizli anahtar saklama (ADR-022), deneme sayacı deposu (APCu) | Şifreli saklama, sayaç kalıcılığı |
| Cross-cutting | Session bayrağı, JWT claim'i, CSRF bağlaması | Oturuma ikinci faktör durumunu yazma |

## §4 — Web research (2026-10-13 doğrulaması)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| OWASP MFA Cheat Sheet, TOTP tabanlı MFA'da doğrulama penceresi ve deneme sınırlandırılmasını önerir | https://cheatsheetseries.owasp.org/cheatsheets/Multifactor_Authentication_Cheat_Sheet.html | HTTP 200 | Doğrulandı — envanterde yok (2026-10-13 sayımı: 0 isabet) |
| RFC 6238, TOTP algoritmasını zaman adımı + HOTP MAC üzerine tanımlar | https://www.rfc-editor.org/rfc/rfc6238 | HTTP 200 | Doğrulandı — envanterde yok |
| RFC 4226, HOTP sayaç tabanlı doğrulama kodunu (6 hane, HMAC-SHA1) tanımlar | https://www.rfc-editor.org/rfc/rfc4226 | HTTP 200 | Doğrulandı — envanterde yok |
| W3C WebAuthn Level 3, ikinci faktör olarak Web Authentication API'yi tanımlar | https://www.w3.org/TR/webauthn-3/ | HTTP 200 | Doğrulandı — envanterde yok |
| MDN Web Auth API sayfası, `navigator.credentials.get` ile WebAuthn akışını belgeler | https://developer.mozilla.org/en-US/docs/Web/API/Web_Authentication_API | HTTP 200 | Doğrulandı — envanterde yok |
| MDN Credential Management API, tarayıcıda kayıtlı kimlik bilgisi saklama/yeniden kullanma arayüzünü tanımlar | https://developer.mozilla.org/en-US/docs/Web/API/Credential_Management_API | HTTP 200 | Doğrulandı — envanterde yok |

**Doğrulama yöntemi:** altı URL `Invoke-WebRequest` ile tek tek çağrıldı ve `200` döndü;
aynı altı öbek `.ai/` genelinde ripgrep ile tarandı → **0 isabet** (envanterde mevcut değil).

## §5 — Standart ve spesifikasyon tabanı

| Kaynak | Ne düzenler | Bu dosyadaki kullanımı |
|---|---|---|
| RFC 4226 | HOTP üretimi: `HMAC(K, counter)` + dynamic truncation | §10.1 MAC adımı |
| RFC 6238 | `T = floor((unixtime - T0) / X)` zaman adımı | §9 period parametresi |
| RFC 6238 §5.2 | Doğrulama penceresi (± window adım) | §9 window = 1 önerisi |
| OWASP MFA Cheat Sheet | Deneme sınırı, cihaz kaybı, account recovery | §12 · §15 |
| W3C WebAuthn-3 | Fiziksel anahtar, challenge/reply, userHandle | §14 |
| ADR-059 | CoreMusic karar: TOTP + 10 kurtarma kodu, bypass yok | §19 faz planı |
| ADR-013 | OTP deneme kotası şartı (kod henüz yok) | §12.1 |
| ADR-022 | Gizli anahtar saklama zemini: AES-256-GCM + Argon2id | §10.4 |

## §6 — Repo kod kanıtları (2026-10-13 doğrulanmış satırlar)

| # | Dosya | Satır | Ne gösterir |
|---|---|---|---|
| 1 | `shared/src/Security/JwtService.php` | 51 | `$jti = bin2hex(random_bytes(16));` — token başına tekil kimlik üretimi |
| 2 | `shared/src/Security/JwtService.php` | 61 | `'jti' => $jti,` — claim'e yazılır |
| 3 | `shared/src/Security/JwtService.php` | 102 | `sub` ve `jti` yoksa/farklıysa `null` (fail-closed) |
| 4 | `shared/src/Security/JwtService.php` | 5 | `use Firebase\JWT\JWT;` — RFC 6238 üretimi kütüphanede değil, serviste |
| 5 | `shared/src/Security/JwtService.php` | 34 | `private int $ttlSeconds = 3600` — erişim tokenı ömrü |
| 6 | `shared/src/Middleware/CsrfMiddleware.php` | 42 | `hash_equals($sessionToken, (string)$submitted)` — sabit zamanlı karşılaştırma |
| 7 | `shared/src/Session/SessionLifecycle.php` | 48 | `$_SESSION['csrf_token'] = bin2hex(random_bytes(32));` — oturum içi rastgele değer |
| 8 | `shared/src/Session/SessionLifecycle.php` | 41 | `if ($this->isIdleTimedOut()) {` — idle zaman aşımı dalı |
| 9 | `shared/src/Session/SessionLifecycle.php` | 60 | `session_regenerate_id(true);` — 30 dk'da bir id rotasyonu |
| 10 | `shared/composer.json` | 13 | `"firebase/php-jwt": "^7.2",` — JWT kütüphanesi composer'da mevcut |
| 11 | `.ai/.decisions/accepted/ADR-013-rate-limiting-apcu.md` | 41 | `OTP kotası YOK → PLANNED` — grep `otp\|totp\|2fa\|verifyCode` = 0 eşleşme |
| 12 | `.ai/.decisions/accepted/ADR-059-jwt-library-and-mfa.md` | 40 | `lcobucci\|firebase\|jwt\|pragmarx\|google2fa\|totp\|otp` = 0 isabet (2026-10-06 taraması) |

> **Kanıt bütünlüğü:** 1–10 numaralı satırlar dosyadan birebir okunarak (indeks = satır − 1)
> 2026-10-13 tarihinde doğrulanmıştır. 11–12, vault içi ADR kayıtlarıdır ve **araç
> çıktısı olarak** değil, **karar metni olarak** alınmıştır.

## §7 — Ölçüm: repoda MFA kodu var mı?

2026-10-13 tarihinde `*.php` dosyalarında `totp|mfa|two_factor|2fa|webauthn|otp_`
desenleri taranmıştır.

| Ölçüm | Sonuç | Yorum |
|---|---|---|
| Toplam isabet | 4 | Hepsi **yanlış pozitif**: `AIEngine.php:350/:362` `roomFactor` · `HomeSongButtonTest.php:54/:63` `usesDefaultAlbumFallback` |
| Gerçek MFA kodu | **0** | TOTP üretimi/doğrulaması yapan tek bir sınıf yok |
| `verifyCode` / `google2fa` / `recoveryCode` | **0 isabet** | ADR-013 `:41` ölçümü tekrar doğrulandı |
| composer bağımlılığı (`pragmarx/google2fa`) | **0** | `.ai/brain.md:128` hedef bağımlılık olarak listeleniyor, kurulu değil |
| Sonuç durum | **PLANNED** | MFA bu depoda **tasarım aşamasındadır**; bu dosya sözleşme, kod değildir |

**Dürüstlük kaydı:** Bu dosyada "repoda çalışan MFA" iddiası **hiçbir yerde geçmez**;
tüm uygulama satırları §19'da faz etiketiyle taşınır.

## §8 — Faktör sınıflandırması ve CoreMusic eşlemesi

| Faktör sınıfı | Örnek | CoreMusic karşılığı | Durum |
|---|---|---|---|
| Bilme (something you know) | Parola | Argon2id + pepper (K010 password) | IMPLEMENTED |
| Sahip olma (something you have) | Telefon/TOTP uygulaması | ADR-059 TOTP | PLANNED |
| Sahip olma (fiziksel) | FIDO anahtarı | WebAuthn (§14) | PLANNED — Faz 2 |
| Sahip olma (kurtarma) | Yazılı kod | 10 kurtarma kodu (ADR-059) | PLANNED |
| Olma (something you are) | Parmak izi/yüz | Tarayıcı içi WebAuthn user-verification | PLANNED — Faz 2 |
| Davranış (behavior) | Yazım ritmi | Yok | YOK — kapsam dışı |
| Konum (context) | IP/cihaz parmak izi | `DeviceDetector` sinyali (yanıt üretilmez) | Kısmi — MFA kararına girdi olarak §13.2 |

## §9 — TOTP parametre sözleşmesi

| Parametre | Önerilen değer | Gerekçe | Kaynak |
|---|---|---|---|
| `issuer` | `coremusic` | Kullanıcı uygulamasında etiket ayrımı | RFC 6238 uygulama notları |
| `algorithm` | HMAC-SHA1 | Uygulama uyumluluğu varsayılanı | RFC 6238 §1 |
| `digits` | 6 | Uygulama uyumluluğu | RFC 4226 §5.3 |
| `period` | 30 saniye | Uygulama uyumluluğu varsayılanı | RFC 6238 §4 |
| `window` | ±1 adım (toplam 3 aday) | Saat kayması toleransı | OWASP MFA Cheat Sheet |
| `window` üst sınırı | ±2 adım | Aşırı pencere kod tahminini genişletir | OWASP MFA Cheat Sheet |
| Zaman kaynağı | `time()` (sunucu saati) | İstemci saati güvenilmez | RFC 6238 §3 |
| Saat sapması alarmı | \|drift\| > 90 sn ise uyarı | Kullanıcıya yeniden senkron öner | OWASP MFA Cheat Sheet |
| Gizli anahtar uzunluğu | ≥ 160 bit (20 bayt) | HMAC-SHA1 anahtar uzunluğu | RFC 4226 §4 |
| Kod deneme kotası | 5 başarısız deneme → kilit (§12.3) | Brute-force sınırı | ADR-013 OTP kotası şartı |
| Yeniden gönderme | Aynı adım içinde ikinci giriş reddedilir | Replay engeli | §11.2 |

## §10 — TOTP doğrulama algoritması (sözleşme)

### §10.1 Kabul adımı

| # | Adım | İşlem | Hata durumu |
|---|---|---|---|
| 1 | Giriş al | 6 haneli kod + hesap kimliği | Hane dışı karakter → 400 |
| 2 | Zaman adımını hesapla | `T = floor(time() / 30)` | Zaman kaynağı yok → 500 fail-closed |
| 3 | Pencereyi tara | `T-1`, `T`, `T+1` | 3 adayın hiçbiri eşleşmezse → başarısız say |
| 4 | MAC üret | `HMAC-SHA1(secret, T)` + dynamic truncation | İmza üretilemezse → 500 fail-closed |
| 5 | Karşılaştır | Sabit zamanlı eşitlik (`hash_equals`) | Süre duyarlı karşılaştırma yasak |
| 6 | Replay kontrolü | Son kullanılan adım ≤ T ise reddet | Eşleşse → 401 replay |
| 7 | Sayacı işle | Başarısızlıkta artır, başarıda sıfırla | Kota doldu → §12.3 kilit |
| 8 | Oturuma yaz | `mfa_verified_at` + adım damgası | Yazım başarısızsa → doğrulama geçersiz |

### §10.2 Karşılaştırma kuralı

| Kural | Doğru | Yanlış |
|---|---|---|
| Eşitlik fonksiyonu | `hash_equals($expected, $input)` | `$expected === $input` |
| Karşılaştırma öncesi | Uzunluk kontrolü ayrı | Uzunluk farkıyla erken çıkış (zaman sızıntısı) |
| Hata mesajı | "Kod geçersiz veya süresi doldu" | "Kod doğru ama süresi doldu" (bilgi sızıntısı) |
| Deneme sayacı | Kod doğrulama servisinde | Yalnız istemci tarafında |

> **Kanıt bağlantısı:** `hash_equals` kullanımı repoda **vardır** —
> `shared/src/Middleware/CsrfMiddleware.php:42`. Aynı kalıp TOTP karşılaştırması için
> de zorunludur.

### §10.3 Zaman penceresi matematiği

| Durum | Sunucu adımı | Gönderilen adım | Karar |
|---|---|---|---|
| Normal | 1000 | 1000 | Kabul |
| Saat geride (istemci) | 1000 | 999 | Kabul (window = 1) |
| Saat ileride (istemci) | 1000 | 1001 | Kabul (window = 1) |
| Çok gecikmiş | 1000 | 997 | Red |
| Çok erken | 1000 | 1003 | Red |
| Tekrar | 1000 | 1000 (ikinci kez) | Red — replay (§11.2) |

### §10.4 Gizli anahtar saklama

| Zorunluluk | Tanım | Kaynak |
|---|---|---|
| Saklama ortamı | Veritabanında AES-256-GCM ile şifreli sütun | ADR-022 (AES-256-GCM + Argon2id zemini) |
| Anahtar yönetimi | Şifreleme anahtarı `.env` içinde, log'a yazılmaz | K007-cryptography |
| Yazma yolu | Gizli anahtar yalnız devre dışı bırakma/yeniden kurulumda okunur | §15 |
| Loglama | Kod, kodun hash'i ve gizli anahtar **asla** loglanmaz | K013-audit |
| Yedekleme | Şifreli sütun yedekleme kapsamındadır | K018-operasyon |
| Günlük erişim | Doğrulama sırasında anahtar çözülür, bellekte tutulur | §10.1 adım 4 |

## §11 — Replay (yeniden kullanım) engeli

### §11.1 Sınıflar

| Sınıf | Tanım | Savunma |
|---|---|---|
| Aynı adım tekrarı | Aynı 30 sn penceresinde ikinci gönderim | Adım filigranı (§11.2) |
| Geçmiş adım | Daha önce kullanılmış eski kod | Son kullanılan adım kaydı |
| Gelecek adım | İleride geçerli olacak kod (üretilemez, tahmin edilir) | window daraltma |
| Başkasının kodu | Başka hesaba ait kod | Kod ≠ hesap bağlaması zorunlu |

### §11.2 Adım filigranı (watermark) sözleşmesi

| Alan | Tip | Yaşamı | Amaç |
|---|---|---|---|
| `mfa_last_step` | int | Oturum + kalıcı hesap kaydı | En son kabul edilen adım |
| `mfa_pending_step` | int | Oturum (kısa ömür) | Challenge anındaki adım |
| `mfa_verified_at` | unixtime | Oturum | Step-up tazeleme kararı (§13) |
| `mfa_method` | string | Oturum | `totp` / `recovery` / `webauthn` ayrımı |

| Kural | Değer |
|---|---|
| Kabul koşulu | `gelen_adım > mfa_last_step` VEYA (`gelen_adım == mfa_last_step` ve yöntem farklı) |
| Red koşulu | `gelen_adım <= mfa_last_step` → 401 replay |
| Kalıcılık | `mfa_last_step` oturum yıkılsa da **hesap düzeyinde** saklanır |
| Kurtarma kodu karşılaştırması | Aynı kural tek kullanımlık hash seti için uygulanır (§12.2) |

## §12 — Deneme kotası ve kilitleme

### §12.1 ADR-013 şartı

| Kayıt | Metin özeti | Durum |
|---|---|---|
| `.ai/.decisions/accepted/ADR-013-rate-limiting-apcu.md:41` | `OTP kotası YOK → PLANNED` — grep `otp\|totp\|2fa\|verifyCode` = 0 eşleşme; akış gelince uygulanır | YÜRÜRLÜKTE ŞART |
| Ölçüm (2026-10-13) | Aynı grep tekrarlandı → 0 gerçek eşleşme | ŞART HENÜZ KARŞILANMADI |

### §12.2 Kota tablosu

| Sayaç | Eşik | Pencere | Eylem |
|---|---|---|---|
| Yanlış TOTP kodu / hesap | 5 | 15 dk | Hesabı 15 dk kilitle |
| Yanlış kurtarma kodu / hesap | 3 | 1 saat | Hesabı 60 dk kilitle + güvenlik olayı |
| Yanlış TOTP / IP | 20 | 10 dk | IP hız düşürme |
| Doğrulama başarısı | Sıfırla | — | Sayaçlar temizlenir |
| Kilitleme süresi dolumu | — | Süre sonu | Yeniden deneme hakkı, olay kaydı kalır |

### §12.3 Kilitleme davranışı

| Durum | Sunucu yanıtı | Kullanıcıya görünen | Kayıt |
|---|---|---|---|
| Kota dolmadı, kod yanlış | 401 | "Kod geçersiz" + kalan deneme | Başarısızlık sayacı |
| Kota doldu | 429 | "Çok fazla deneme, dakika sonra tekrar" | Güvenlik olayı |
| Kurtarma kodu hatası | 401 | Genel hata metni | Ayrı sayaç (§12.2) |
| Saat kayması şüphesi | 401 + ipucu | "Cihaz saatinizi kontrol edin" | Telemetri (UNKNOWN — altyapı yok) |

## §13 — Step-up (yeniden kimlik doğrulama)

### §13.1 Tetikleyiciler

| İşlem | Step-up zorunlu mu | Dayanak |
|---|---|---|
| Parola değiştirme | Evet | Kimlik yenileme |
| İkinci faktörü devre dışı bırakma | Evet + ek doğrulama | §15.4 |
| E-posta değişimi | Evet | Hesap ele geçirme vektörü |
| Yeni cihaz/token üretimi | Evet | API key / JWT minting |
| Hassas veri dışa aktarımı | Evet | Veri sızıntısı önleme |
| Normal sayfa gezinme | Hayır | UX maliyeti |
| Okuma amaçlı API çağrısı | Hayır | ADR-020 kapsamı |

### §13.2 Tazelik penceresi

| Kural | Değer | Gerekçe |
|---|---|---|
| Taze kabul süresi | 5 dakika | Kısa adım UX maliyeti |
| Süre dolumu | Yeniden TOTP iste | Re-authentication |
| Tazelik kaydı | `mfa_verified_at` oturumda | §11.2 |
| Başarısız step-up | Oturumu kapatma değil, yalnız işlem reddi | Deneme üstünlüğü |
| Aynı oturumda ardışık | 2. işlem tazelik içindeyse isteme | Aşırı sorma yasağı |

## §14 — WebAuthn (Faz 2 — tasarım düzeyinde)

### §14.1 Akış adımları

| # | Adım | Veri | Not |
|---|---|---|---|
| 1 | Challenge üret | Rastgele 32 bayt | Tek kullanımlık, süreli |
| 2 | `navigator.credentials.create/get` | rpId = `coremusic.net` | Tarayıcı içi onay |
| 3 | Yanıtı doğrula | `signCount`, imza, clientDataJSON | Sunucuda imza doğrulaması |
| 4 | Kaydı yaz | credentialId, userHandle, publicKey | Tekil credentialId kısıtı |
| 5 | Oturuma yaz | `mfa_method = webauthn` | §11.2 |

### §14.2 Zorunluluklar

| Zorunluluk | Kaynak |
|---|---|
| `challenge` tek kullanımlık ve süreli olmalı | W3C WebAuthn-3 |
| `rpId` yalnız kendi kök alanıyla eşleşmeli | W3C WebAuthn-3 |
| `signCount` monoton artmalı (artmıyorsa uyarı) | W3C WebAuthn-3 |
| clientDataJSON `type`/`challenge`/`origin` doğrulaması zorunlu | W3C WebAuthn-3 |
| HTTPS zorunlu (secure context) | MDN Web Auth API |

### §14.3 CredMan ile ilişki

| Konu | Karar |
|---|---|
| Credential Management API kullanımı | Kayıtlı kimlik bilgisiyle otomatik doldurma desteği |
| MFA yerine geçebilir mi? | Hayır — yalnız birinci faktör UX'i |
| Depoya etkisi | Ek depo yok, tarayıcı içi |

## §15 — Devre dışı bırakma, yeniden kurulum, bypass

### §15.1 Bypass yasağı

| Kayıt | Hükmü |
|---|---|
| ADR-059 | MFA = TOTP + 10 kurtarma kodu; **bypass kodu YOK** |
| ADR-008 | Bypass = üretimde fail-closed — MFA atlatma kapısı da bu ilkeye bağlı |
| Sonuç | Yönetici "MFA'yı geç" kodu tasarlanmaz; kurtarma kodu tek yasal yol |

### §15.2 Cihaz kaybı senaryosu

| Adım | İşlem | Denetim |
|---|---|---|
| 1 | Kullanıcı "erişemiyorum" der | Olay kaydı |
| 2 | Kurtarma kodu ile giriş | §12.2 ayrı sayaç |
| 3 | TOTP yeniden kurulumu zorunlu | Eski gizli anahtar geçersiz |
| 4 | Yeniden kurulumda parola + kurtarma kodu | Çift kanıt |
| 5 | Güvenlik olayı bildirimi | K013-audit |

### §15.3 Yeniden kurulum (re-enrollment)

| Kural | Değer |
|---|---|
| Eski gizli anahtar | İmha (şifre çözme anahtarıyla silme) |
| Yeni gizli anahtar | Yeni 160 bit rastgele üretim |
| Kurtarma kodları | Yeniden üretilir, eskileri geçersiz |
| Oturumlar | Diğer tüm oturumlar kapatılır |
| Bildirim | E-posta ile bilgilendirme (kanal: UNKNOWN — altyapı yok) |

### §15.4 Devre dışı bırakma

| Koşul | Karar |
|---|---|
| Kullanıcı talebi | Yalnız step-up ile (§13.1) |
| Denetim | Her iki yönde de olay kaydı |
| Varsayılan | Yeni hesaplarda MFA **önerilir**; zorunluluk politikası UNKNOWN (onay bekliyor) |
| Risk kabulü | Devre dışı bırakma, birinci faktör tekil kalır → ADR-013 rate limit şartı kritik |

## §16 — JWT, session ve MFA etkileşimi

| Yüzey | MFA'dan nasıl etkilenir | Kod kanıtı |
|---|---|---|
| Session cookie | `mfa_verified_at` oturum anahtarıyla taşınır | `shared/src/Session/SessionLifecycle.php:48` (oturum anahtarına yazım kalıbı) |
| JWT access token | `amr` (authentication methods reference) claim'i eklenmesi önerilir | `shared/src/Security/JwtService.php:54-62` claim birleştirme |
| Token ömrü | MFA yapılmadan üretilen token kısa ömürlü olmalı | `shared/src/Security/JwtService.php:34` (`ttlSeconds = 3600`) |
| Revocation | `jti` user_tokens kaydında — MFA iptalinde token iptali mümkün | `shared/src/Security/JwtService.php:51` ve `:102` |
| CSRF | İkinci faktör formu da CSRF tokenı ister | `shared/src/Middleware/CsrfMiddleware.php:42` |
| Fail-closed | `sub`/`jti` yoksa token geçersiz — MFA claim'i eklense de kural değişmez | `shared/src/Security/JwtService.php:102` |

**Açık tasarım sorusu:** `amr` claim'i `issue()`'ya `$extra` üzerinden eklenirse
(`shared/src/Security/JwtService.php:44` — `array $extra`), doğrulama tarafında
`validate()` bunu **zorunlu kılmaz** (`:83-108` yalnız iss/aud/sub/jti kontrol eder).
MFA zorunluluğunun token içinde denetlenmesi ayrı bir middleware sözleşmesidir —
§20 açık soru #1.

## §17 — CSRF ve oturum nabzı ile ilişki

| Bileşen | MFA akışına etkisi | Kanıt |
|---|---|---|
| CSRF token üretimi | İkinci faktör formu için yeni token gerekmez, oturumdaki kullanılır | `shared/src/Session/SessionLifecycle.php:47-49` |
| CSRF reddi | Başarısız CSRF, MFA denemesi sayılmaz (sayaç artmaz) | `shared/src/Middleware/CsrfMiddleware.php:42` |
| Idle timeout | MFA beklemesi idle sayacını **uzatmaz** | `shared/src/Session/SessionLifecycle.php:41` |
| Rotasyon | MFA başarılıysa oturum id'si yenilenebilir (fixation savunması) | `shared/src/Session/SessionLifecycle.php:60` |
| Kötü senaryo | MFA adımında oturum çalınırsa CSRF + rotasyon birlikte korur | §21.3 |

## §18 — Politika ↔ kod karşılaştırma tablosu

| Politika (bu dosya) | Kod karşılığı | Durum |
|---|---|---|
| TOTP 6 hane / 30 sn / ±1 pencere | TOTP sınıfı yok | **PLANNED** |
| `hash_equals` ile sabit zamanlı karşılaştırma | `CsrfMiddleware.php:42` mevcut (aynı kalıp) | KALIP HAZIR |
| Deneme kotası 5/15 dk | ADR-013 `:41` şartı, uygulama yok | **PLANNED** |
| Replay: adım filigranı | Kayıt alanı yok | **PLANNED** |
| Kurtarma kodu (10 adet) | Üretim yok | **PLANNED** |
| Step-up 5 dk tazelik | `mfa_verified_at` anahtarı yok | **PLANNED** |
| Bypass kodu yasağı | ADR-059 hükmü — ihlal edecek kod da yok | **UYUMLU** |
| JWT `amr` claim'i | `JwtService::issue` `$extra` ile taşıyabilir | **AÇIK** (§20) |
| Gizli anahtar AES-256-GCM saklama | ADR-022 zemini mevcut, MFA sütunu yok | **PLANNED** |

## §19 — Uygulama fazları

| Faz | Kapsam | Ön koşul | Durum |
|---|---|---|---|
| 0 | Sözleşme (bu dosya) | — | YAZILDI (draft) |
| 1 | TOTP servisi + gizli anahtar sütunu + kurtarma kodu üretimi | ADR-022 saklama, ADR-013 sayaç | PLANNED |
| 2 | Login akışına ikinci faktör adımı + deneme kotası | Faz 1 | PLANNED |
| 3 | Step-up + cihaz kaybı/yeniden kurulum akışı | Faz 2 | PLANNED |
| 4 | WebAuthn (fiziksel anahtar) | Faz 2 · HTTPS · tarayıcı desteği | PLANNED — Faz 2 kapsamı |
| 5 | Denetim olayları + telemetri (K013) | Faz 2 | PLANNED |

**Faz dışı:** SMS OTP — telekom bağımlılığı + SIM-swap riski nedeniyle kapsam dışıdır
(karar metni: **UNKNOWN**, vault'ta bu gerekçeyle yazılı bir red yok).

## §20 — Açık sorular ve doğrulanamayan iddialar

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| 1 | `amr` claim'i doğrulama tarafında zorunlu kılınacak mı? | `⚠️ VERIFICATION REQUIRED` | backend-architect |
| 2 | Yeni hesaplarda MFA zorunlu mu, önerili mi? | `UNKNOWN` (onay bekliyor) | product-manager |
| 3 | OTP kotası hesap başına mı, IP başına mı uygulanacak? | ADR-013 açık bıraktı | security-engineer |
| 4 | Saat kayması telemetrisi hangi depoya yazılacak? | `UNKNOWN` — telemetri altyapısı yok | sre-engineer |
| 5 | Kurtarma kodu dağıtımı hangi kanaldan? | `UNKNOWN` — e-posta altyapısı doğrulanmadı | backend-architect |
| 6 | TOTP gizli anahtarının yedekleme kapsamı K018'de tanımlı mı? | `⚠️ VERIFICATION REQUIRED` | database-administrator |
| 7 | `pragmarx/google2fa` mi, kendi implementasyon mu? | `UNKNOWN` — `.ai/brain.md:128` hedef, karar ADR-059'da kütüphane adı vermiyor | security-engineer |

> **Yasak:** Yukarıdaki sorular "evet" varsayılarak kod yazılamaz; her biri ayrı karar
> (ADR veya onay) gerektirir.

## §21 — Red team: saldırı senaryoları ve savunma

| # | Saldırı | Öncül | Savunma | Bu dosyadaki karşılık |
|---|---|---|---|---|
| 1 | Kod tahmini (brute force) | 10^6 kod | Kota + kilit | §12.2 |
| 2 | Replay | Ele geçirilmiş kod | Adım filigranı | §11.2 |
| 3 | Saat manipülasyonu | İstemci saati | Sunucu saati + window sınırı | §9 |
| 4 | Orta adam (MITM) | HTTPS olmayan kanal | HTTPS zorunlu | §14.2 |
| 5 | Phishing (sahte login) | Kullanıcı yönlendirmesi | WebAuthn origin kısıtı | §14.2 |
| 6 | Cihaz hırsızlığı | Fiziksel erişim | Ekran kilidi + kurtarma kodu | §15.2 |
| 7 | Sim-swap | SMS OTP | SMS kapsam dışı | §19 |
| 8 | Session fixation + MFA | Sabit oturum id'si | `session_regenerate_id(true)` | `SessionLifecycle.php:60` |
| 9 | MFA bypass kodu sızıntısı | Yönetici kodu | Kod türü yok | §15.1 |
| 10 | Yanıt manipülasyonu | İstemci "doğrulandı" der | Karar sunucuda | §10.1 adım 8 |

## §22 — Test senaryoları (taslak)

| # | Senaryo | Beklenen |
|---|---|---|
| 1 | Geçerli kod, doğru adım | Kabul + oturuma yazım |
| 2 | Aynı kod ikinci kez | Red (replay) |
| 3 | window dışı kod (T−3) | Red |
| 4 | 5 yanlış kod | 429 + kilit |
| 5 | Süresi dolmuş kurtarma kodu | Red + ayrı sayaç |
| 6 | CSRF token'sız MFA formu | Red, sayaç artmaz |
| 7 | `hash_equals` kullanılmayan karşılaştırma | Statik analiz reddi |
| 8 | MFA doğrulaması sonrası idle sayacı | Idle aşılmaz |
| 9 | `amr` claim'siz token ile hassas işlem | Red (faz 1 sonrası) |
| 10 | Bypass kodu sorgusu | Kod türü bulunamaz (yalnızlık testi) |

## §23 — İlgili düğümler ve kararlar

| Kaynak | İlişki |
|---|---|
| `K0100-identitiy/session` | Oturum bayrağı, rotasyon, CSRF — MFA'nın taşıyıcısı |
| `K0100-identitiy/password` | Birinci faktör; MFA ondan bağımsız kanıt |
| `K0100-identitiy/authorization` | Step-up sonrası yetki kararı ayrı katmanda |
| ADR-059 | MFA yöntemi kararı (TOTP + kurtarma kodu, bypass yok) |
| ADR-013 | OTP deneme kotası şartı |
| ADR-022 | Gizli anahtar saklama zemini |
| ADR-008 | Fail-closed ilkesi — MFA atlatma kapısına da uygulanır |
| ADR-052 | Hibrit session/JWT — MFA bayrağı iki yüzeyde taşınır |

## §24 — Risk kaydı

| # | Risk | Etki | Olasılık | Azaltma |
|---|---|---|---|---|
| 1 | Faz 1 uygulanana kadar MFA yok | Yüksek | Mevcut | Rate limit + uzun parola (ADR-013) |
| 2 | Kota şartı uygulanmazsa kod tahmini | Yüksek | Orta | §12.2 zorunluluğu |
| 3 | Saat kayması nedeniyle reddedilen meşru kod | Orta | Orta | window = 1 + ipucu mesajı |
| 4 | `amr`'ın doğrulamada yok sayılması | Orta | Yüksek | §20 açık soru #1 |
| 5 | Kurtarma kodunun e-posta ile dağıtımı ele geçirilirse | Yüksek | Orta | Kanal kararı §20 #5 |
| 6 | WebAuthn'a geçişte phishing direncinin eksik kalması | Orta | Orta | Faz 4 öncesi §14 |

## §25 — Özet tablo

| Boyut | Karar | Durum |
|---|---|---|
| Yöntem | TOTP (RFC 6238/4226) | PLANNED |
| Yedek yöntem | 10 kurtarma kodu | PLANNED |
| Üçüncü yöntem | WebAuthn | PLANNED — Faz 2 |
| Bypass | Yok | KARARLAŞMIŞ (ADR-059) |
| Replay | Adım filigranı | SÖZLEŞME (§11.2) |
| Kota | 5/15 dk hesap · 3/1 saat kurtarma | ŞART (ADR-013) |
| Step-up | 5 dk taze pencere | SÖZLEŞME (§13.2) |
| Karar yeri | Sunucu | SÖZLEŞME (§10.1) |
| Karşılaştırma | `hash_equals` | KALIP MEVCUT (`CsrfMiddleware.php:42`) |
| Kod durumu | Repoda MFA kodu 0 | ÖLÇÜLDÜ (§7) |

## §26 — Envanter: bu dosyanın kanıt yazımı istatistikleri

| Metrik | Değer | Kanıt |
|---|---|---|
| Doğrulanmış repo kod alıntısı | 10 | §6 tablosu satır 1–10 |
| Vault ADR alıntısı | 2 | §6 satır 11–12 |
| Doğrulanmış tekil web kaynağı | 6 | §4 tablosu (hepsi HTTP 200) |
| Envanter kesişimi | 0 | §4 ölçüm notu |
| Repoda tespit edilen MFA kodu | 0 | §7 ölçümü |
| Yanlış pozitif isabet | 4 | §7 — `roomFactor` / `usesDefaultAlbumFallback` |
| Açık soru | 7 | §20 |
| Saldırı senaryosu | 10 | §21 |
| Test senaryosu | 10 | §22 |
| Faz sayısı | 6 (0–5) | §19 |

## §27 — Sonraki adımlar

| # | Adım | Öncelik | Sahip |
|---|---|---|---|
| 1 | §20 açık sorularını karara bağla (ADR veya onay) | P0 | Vault Steward |
| 2 | ADR-013 OTP kotasını uygulanabilir spec'e çevir | P0 | security-engineer |
| 3 | TOTP servisi arayüzünü (§10.1) contract test ile sabitle | P1 | qa-engineer |
| 4 | `amr` claim'i + middleware denetimini tasarla (§16) | P1 | backend-architect |
| 5 | Faz 1 için veritabanı şema taslağını hazırla | P1 | data-engineer |
| 6 | Faz 4 (WebAuthn) kapsamını onaya sun | P2 | product-manager |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
