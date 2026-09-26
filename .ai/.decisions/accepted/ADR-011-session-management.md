---
title: "CoreMusic — ADR-011: Session Management (Oturum Yaşam Döngüsü: OWASP Seti + Hibrit Saklama + Tam Hijyen)"
type: adr
category: security
date: 2026-09-24
updated: 2026-09-24
version: 1.0.0
status: accepted
authority: ADR-011 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-011: Session Management (Oturum Yaşam Döngüsü: OWASP Seti + Hibrit Saklama + Tam Hijyen)

**Durum:** accepted (kullanılabilir — frozen YOK)
**Tarih:** 2026-09-24
**Karar Veren:** Vault Steward (kullanıcı onaylı — "ADR-011'i sıfırdan yaz") · debate: ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL) · Tech Lead: ✅
**İlgili ADR'ler:** [[ADR-004-multi-domain-spa]] (oturum cookie'si `domain=.coremusic.net` ile tüm subdomainlere yayılır — bu ADR'nin cookie kararları bu haritada yaşar; dosya diskte VAR ✅) · [[ADR-007-cache-namespace]] (hibrit saklama'nın APCu katmanı bu namespace standardına bağlanır; dosya diskte VAR ✅) · **Düz metin (dosya diskte VAR — wiki-link KURULMAZ, kullanıcı onayı): ADR-008** (`.ai/.decisions/accepted/ADR-008-bypass-auth-middleware.md` — auth bypass kapsamı; oturum politikaları bypass scope'unu değiştirmez) · **Düz metin (dosya diskte VAR — wiki-link KURULMAZ, kullanıcı onayı): ADR-010** (CSRF Protection — **şart 3**: JWT stub kapanana kadar cookie-auth tek yol; bu ADR ile doğrudan kesişir → §4.4) · **Düz metin (dosya diskte VAR — wiki-link KURULMAZ, kullanıcı onayı): ADR-012** (CSP Nonce Strict-Dynamic — XSS oturum hırsızlığını kapatan katman) · karar dizini [[../index]] §3 satırı `[[ADR-011-session-management]]` (satır 48 — slug eşleşmesi ✅, dosya bu oturumda oluşturuldu).

---

## 1. Bağlam (Context)

CoreMusic'un tüm oturumları **sunucu tarafında, cookie tabanlı** başlar: `PHPSESSID` (benzeri) cookie'si `domain=.coremusic.net`, `HttpOnly`, `SameSite=Lax`, HTTPS'te `Secure` ile set edilir; oturum verisi dosya tabanlı `session.save_path` üzerinde yaşar. Bu ADR, **oturumun kendisini** (üretim, timeout, rotasyon, saklama, hijyen) tek kararda sabitler — ADR-010'un **istek sahteciliğini** (CSRF) değil, bu ADR'nin **kimlik/ömür/saklama** kararını konu alır. Kapsam: (a) OWASP oturum hijyeni tam seti (cookie parametreleri, CSPRNG kimlik, login rotasyonu, idle/absolute timeout, privilege change'de yeniden doğrulama, expiration header), (b) **hibrit saklama** (sunucu tarafı oturum = SSOT; JWT access ≤15 dk + rotasyonlu tek kullanımlık refresh), (c) **tam hijyen 6 kuralı**, (d) ADR-010 şart 3 ile uyum maddesi (§4.4).

### 1.1 Mevcut Durum

**Kod kanıtları (diskde okundu — IMPLEMENTED/PLANNED etiketleri dosya yoluyla):**

- **`shared/src/Session/SessionInitializer.php` (satır 40-47) — COOKIE PARAMETRELERİ IMPLEMENTED:** `session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'domain' => '.coremusic.net', 'httponly' => true, 'samesite' => 'Lax', 'secure' => $isHttps])` → `HttpOnly` ✅, `SameSite=Lax` ✅ (ADR-010 katman 2 ile aynı değer), `Secure` HTTPS koşullu ✅, oturum-ömrü cookie (`lifetime 0` = tarayıcı kapanınca) ✅, subdomain-geniş `domain` ✅/⚠️ (ADR-004 gereği bilinçli, ama same-site yüzeyini büyütür — §4.3 risk 4).
- **`shared/src/Session/SessionConfig.php` (satır 16-20) — ZAMAN SABİTLERİ IMPLEMENTED AMA ÇELİŞKİLİ:** `COOKIE_EXPIRY = 42000` (12 saat), `MAX_LIFETIME = 1800` (**30 dk mutlak ömür**), `IDLE_TIMEOUT = 3600` (**60 dk boştalık**), `ROTATION_INTERVAL = 1800` (30 dk rotasyon). **ÇELİŞKİ: mutlak ömür (30 dk) boştaki oturum süresinden (60 dk) KISA** — yani "absolute timeout", "idle timeout"ın altında kalır; iki zamanlayıcı anlamsızlaşır (§1.2-1) → **PLANNED düzeltme (hedef: idle 30 dk / absolute 8 saat — §2.2a)**.
- **`shared/src/Session/SessionLifecycle.php` — YAŞAM DÖNGÜSÜ IMPLEMENTED:** absolute timeout denetimi + destroy (satır 31-39), idle timeout denetimi + destroy (satır 41-45), CSRF token üretimi `bin2hex(random_bytes(32))` (satır 47-49), CSP nonce üretimi (satır 51-52), **30 dakikada bir `session_regenerate_id(true)` rotasyonu** (satır 54-62, `SessionConfig::ROTATION_INTERVAL`), `last_activity` güncelleme (satır 64-66).
- **`shared/src/Session/SessionBootstrapper.php::restartFresh()` (satır 34-59) — LOGOUT INVALIDATION IMPLEMENTED:** `session_destroy()` + oturum cookie'sini temizleme + `session_regenerate_id()` → **tek oturum sonlandırma IMPLEMENTED**; **çoklu oturum sonlandırma (tüm cihazlar) YOK → PLANNED** (§2.2c kural 3-4).
- **Login fixation koruması IMPLEMENTED:** `auth.coremusic.net/include/Controller/AuthController.php` satır 144 (login), satır 193 (register), satır 225 (logout) + `auth.coremusic.net/include/Service/AuthService.php` satır 166 → `session_regenerate_id()` çağrıları → **login/register'da oturum kimliği yenileme VAR** (session fixation'ın ana ilacı kodda mevcut).
- **⚠️ ÇELİŞKİ — ikinci oturum middleware'i:** `auth.coremusic.net/include/Middleware/SessionMiddleware.php` (satır 13-14) `IDLE 3600` + **`ROTATION 900` (15 dk)** kullanırken `shared/src/Session` tarafı `ROTATION_INTERVAL 1800` (30 dk) kullanıyor → **iki farklı rotasyon aralığı, iki farklı yapılandırma kaynağı** → **PLANNED: tek SSOT (`SessionConfig`) + birleşik rotasyon** (§5.1 adım 2).
- **JWT stub — Bearer yolu fiilen ÖLÜ:** `shared/src/Api/Middleware/AuthenticationMiddleware.php` önce session/cookie dener (satır 37-45, `method: 'session'`), sonra `Bearer ` prefix (satır 48-57); **`validateJwtToken()` (satır 92-106) her zaman `null` döner** ("simplified implementation... return null (not validated)") → **Bearer ile oturum açma bugün mümkün değil** → hibrit saklama **PLANNED** ⚠️ VERIFICATION REQUIRED (§2.2b).
- **`session.use_strict_mode` YOK → PLANNED:** kod içinde `ini_set('session.use_strict_mode'...)`/PHP yapılandırması geçmişi (grep: `use_strict_mode` sıfır sonuç) — php.net'e göre bilinmeyen/önceden bilinen ID kabulünü kapatan **zorunlu** hardening katmanı (§2.2a-6).
- **Aktif oturumlar görünümü YOK → PLANNED:** grep `active_sessions|revokeAll` boş — kullanıcıya "aktif oturumlar" listesi veya "hepsini sonlandır" eylemi sunulmuyor (§2.2c kural 4).
- **Sunucu tarafı APCu/db oturum deposu YOK → PLANNED (hibrit):** oturumlar dosya tabanlı (`session.save_path`; Windows'ta `C:\temp` / `sys_get_temp_dir`); APCu'da oturum saklama (session.save_handler) veya db tablosu yok → çoklu-sunucu/Auhtık scale hedefi (§2.2b).
- **Session expiration header YOK → PLANNED:** yanıtta oturum bitişini bildiren başlık üretilmiyor (standart karşılığı ⚠️ VERIFICATION REQUIRED — §2.2a-7).

### 1.2 Sorun Tanımı

1. **Absolute < Idle çelişkisi:** `MAX_LIFETIME 1800 < IDLE_TIMEOUT 3600` — mutlak ömür, boştalıktan kısa; iki zamanlayıcı birlikte anlamsız (`SessionConfig.php` satır 17-18).
2. **İki yapılandırma kaynağı:** `shared` 1800 sn vs `auth` 900 sn rotasyon — hangisinin geçerli olduğu kodda belirsiz (`SessionMiddleware.php` satır 13-14).
3. **Fixation yüzeyi daraltılmamış:** login'de rotasyon var ama `use_strict_mode` yok — saldırgan önceden yerleştirdiği bilinen bir session ID ile fixation deneyebilir (kabul edilirse oturum bağlanır).
4. **Çoklu oturum yönetimi yok:** cihaz listesi, tek oturum sonlandırma, "hepsini sonlandır" yok → hesap hırsızlığında kullanıcı kurtaramaz.
5. **Saklama dosya tabanlı:** tek sunucu için yeterli; APCu/db/hibrit hedefi tanımsız → ölçek ve JWT yolu belirsiz.
6. **JWT yolu stub:** `validateJwtToken()` hep `null` → Bearer ile oturum açma kapalı, ama ADR-010 şart 3'ün "cookie-auth tek yol" gerilimi bu ADR'de çözülmezse iki ADR çelişir.
7. **Privilege change kuralı yazılı değil:** rol/izin/email/şifre değişiminde mevcut oturum(lar) ne olur — kural vault'ta yok (§2.2c kural 3).
8. **Expiration header yok:** istemci oturumun ne zaman biteceğini bilmiyor; yenileme/zamanlama UX'i sunucuya bağımlı.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: prompt-maker web araştırma protokolü (`.claude/skills/prompt-maker/references/10-web-research-protocol.md` — diskte OKUNDU ✅, v7.2.0) — birincil/resmî kaynak önce (OWASP Cheat Sheet, OWASP ASVS, NIST SP, php.net), **her iddiaya ≥2 bağımsız çapraz kaynak**, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`; **güvenlik iddiası = OWASP + resmî zorunlu**. **Odak: (a) OWASP Session Management Cheat Sheet güncel sürümü (session ID üretimi, idle/absolute timeout, rotasyon); (b) OWASP ASVS 5.0 V7 oturum maddeleri; (c) NIST SP 800-63-4 oturum zaman aşımı (AAL2/AAL3); (d) session fixation (OWASP + CVE + uygulama rehberleri); (e) PHP session hardening + JWT vs sunucu-tarafı oturum / refresh rotasyonu 2025-26.**

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "OWASP Session Management Cheat Sheet session ID generation idle absolute timeout rotation 2025" · (2) "OWASP ASVS 5.0 V7 session requirements 7.1.1 7.2.4 re-authentication session expiration" · (3) "NIST SP 800-63-4 session timeout AAL2 inactivity 1 hour absolute 24 hours AAL3 15 minutes" · (4) "session fixation attack prevention session_regenerate_id privilege escalation CVE-2022-40293" · (5) "PHP session hardening use_strict_mode sid_length 128 bit + JWT vs server-side session refresh token rotation 2026 reuse detection" |
| Web Search **Konusu** | Güvenli oturum kimliği üretimi (CSPRNG, 128 bit+), idle/absolute timeout aralıkları, oturum kimliği rotasyonu zamanlaması; ASVS 5.0'ın oturum maddeleri (7.1.1-7.3.2) ve privilege change'de yeni oturum (7.2.4); NIST'in AAL seviyesine göre timeout değerleri (AAL2: 24 saat genel / 1 saat hareketsizlik; AAL3: 12 saat / 15 dakika); session fixation saldırı şeması ve ilaçları (use_strict_mode, login'de regenerate); PHP oturum hardening bayrakları; JWT ile sunucu-tarafı oturumun karşılaştırması ve refresh token rotasyonu/reuse detection 2025-26. |
| Web Search **Bağlam** | **16 adlandırılmış kaynak / 5 sorgu** (çoğu 2024-2026; birincil + ikincil): birincil — OWASP Session Management Cheat Sheet (cheatsheetseries, güncel sürüm), OWASP ASVS 5.0 V7 (Authentication & Session Management), NIST SP 800-63-4 (Digital Identity Guidelines, 2025), OWASP session fixation rehberi, php.net session yapılandırması + `session_regenerate_id()` dokümanı; ikincil — CVE-2022-40293 (ürün-örneği fixation), CodePath session fixation rehberi, Imperva session fixation makalesi, Auth0 (JWT vs session), LoginRadius 2026 (token rotation), Jsonic 2026 (refresh rotation + reuse detection), caduh 2025 (refresh token akışı), guptadeepak 2026 (JWT session management), OWASP 2026 token storage rehberi (HttpOnly+Secure+SameSite cookie > localStorage). |
| Web Search **Kısa Açıklama** | **(1) OWASP:** oturum kimliği ≥128 bit CSPRNG (`random_bytes`) ile üretilir, tahmin edilemez/çarpışmaz; **idle timeout düşük riskli uygulamalarda 15-30 dk, absolute 4-8 saat** önerilir; kimlik login'de **ve privilege change'de** yenilenir (`session_regenerate_id(true)`), belirli aralıklarla da döner. **(2) ASVS 5.0 V7:** 7.1.1-7.3.2 oturum kimliği, bağlama, yenileme maddeleri; **7.2.4 = privilege change sonrası yeni oturum/zorunlu yeniden doğrulama**. **(3) NIST:** AAL2 → genel süre ≤24 saat, hareketsizlik ≤1 saat; AAL3 → ≤12 saat / ≤15 dakika. **(4) Fixation:** saldırgan bilinen oturum kimliğini kurbanla paylaşıp kimliği "sabitler"; ilaç = `session.use_strict_mode` + login'de `session_regenerate_id(true)` + privilege change'de rotasyon. **(5) PHP:** `use_strict_mode=1` zorunlu hardening, `use_only_cookies=1`, `sid_length 32` (=128 bit); `session_regenerate_id` ~15 dk'da bir çağrılabilir. **JWT 2025-26:** stateless erişim + refresh ile oturum; **access 5-15 dk, refresh 7-30 gün; tek kullanımlık refresh + reuse detection → aile iptali**; depolama: HttpOnly+Secure+SameSite cookie, localStorage'a göre tercihli. |
| Web Search **Uzun Açıklama** | **(a) OWASP Session Management Cheat Sheet:** güvenli oturum kimliği üç özelliği karşılamalı — yeterli uzunluk (≥128 bit), CSPRNG ile tahmin edilemezlik, oturum içi değişmezlik+yanında yenileme disiplini; **idle (boştalık) timeout kullanıcının son etkileşiminden, absolute (mutlak) timeout oturum başlangıcından ölçülür ve İKİSİ DE uygulanmalıdır** — absolute asla idle'dan anlamlı biçimde kısa yapılmaz (CoreMusic bugünkü `1800 < 3600` çelişkisi tam tersidir); idle için düşük riskli uygulamada 15-30 dk, mutlak ömür için 4-8 saat bandı telkin edilir; oturum kimliği login'de yenilenir (fixation), yetki/rol değişiminde yeniden doğrulama istenir ve zamanlanmış rotasyon (ör. 15-30 dk) derinlemesine savunma olarak eklenir; oturum sona erdiğinde cookie + sunucu kaydı birlikte temizlenir (invalidation). **(b) OWASP ASVS 5.0 V7:** oturum kimliğinin yeniden kullanılamazlığı, tarayıcı kapanınca geçersizlik, sunucu tarafı zaman aşımı, **7.2.4 privilege change'de yeni oturum/zorunlu yeniden kimlik doğrulama** maddeleri bu ADR'nin kural 3'ünü doğrudan zorunlu kılar. **(c) NIST SP 800-63-4:** zaman aşımı seviyeye bağlıdır — AAL2'de genel oturum ≤24 saat + hareketsizlik ≤1 saat kabul edilir; AAL3'te ≤12 saat / ≤15 dakika; CoreMusic hedef seviyesi AAL2 sınıfında konumlandığı için **idle 30 dk (NIST 1 saatin altında, OWASP bandının üst ucu) + absolute 8 saat (NIST 24 saatin altında, OWASP 4-8 saat bandı)** seçilebilir. **(d) Session fixation:** saldırı, saldırganın kendi oluşturduğu/önceden bildiği oturum kimliğini kurbanın tarayıcısına taşıyıp kurbanın giriş yapmasıyla "kilitlenmesi"dir; CVE-2022-40293 gibi gerçek vakalarda kimlik önceden kabul edilerek istismar edilmiştir; CodePath/Imperva rehberleri üç ilacı tekrarlar: `use_strict_mode`, login'de `session_regenerate_id(true)`, privilege change'de rotasyon + saldırı vektörleri (URL kimliği taşımak, XSS ile cookie yazmak). **(e) PHP hardening:** `session.use_strict_mode=1` bilinmeyen/önceden başlatılmış ID'yi reddeder (fixation'ın sunucu tarafı kilidi), `use_only_cookies=1` kimliğin URL'de taşınmasını engeller, `sid_length=32` + `sid_bits_per_character` 128-bit kimlik üretir; bu üçü bu ADR'de zorunlu maddedir. **(f) JWT vs sunucu-tarafı oturum (2025-26):** Auth0/LoginRadius/Jsonic/caduh/guptadeepak aynı kalıba varır: **stateless access token kısa ömürlü (5-15 dk), oturum/kimlik SSOT'u sunucu tarafında refresh token deposudur**; refresh **tek kullanımlık** döndürülür — aynı refresh iki kez görülürse **reuse detection → oturum ailesi tamamen iptal**; depolama tarafında OWASP 2026, token'ı **HttpOnly+Secure+SameSite cookie** içinde tutmayı localStorage'a (XSS'e açık) tercih eder. → CoreMusic kararı: **sunucu-tarafı oturum SSOT + kısa JWT access + rotasyonlu tek kullanımlık refresh = hibrit**; JWT tek başına asla oturum olmaz (logout/iptal imkansızlaşır). |
| Web Search **Paragraf Veri Uzun** | OWASP: session ID ≥128 bit CSPRNG · idle 15-30 dk (düşük risk) + absolute 4-8 saat **ikisi birlikte** · login + privilege change'de `session_regenerate_id(true)` · zamanlanmış rotasyon 15-30 dk · logout = cookie + sunucu invalidation · ASVS 5.0 V7 7.1.1-7.3.2 + **7.2.4 privilege change → yeni oturum** · NIST AAL2 ≤24 saat/≤1 saat, AAL3 ≤12 saat/≤15 dakika · fixation üç ilaç: `use_strict_mode` + login rotate + privilege rotate (CVE-2022-40293 örneği) · PHP: `use_strict_mode=1`, `use_only_cookies=1`, `sid_length=32` (128 bit), ~15 dk'da bir `session_regenerate_id` · JWT 2025-26: access 5-15 dk, refresh 7-30 gün, **tek kullanımlık refresh + reuse detection → family revoke**, cookie (HttpOnly+Secure+SameSite) > localStorage · **absolute asla idle'dan kısa yapılmaz (CoreMusic `1800<3600` çelişkisi → 1800/28800 düzeltmesi)**. |
| Web Search **Sonucu** | 1) **Idle/absolute ikilisi zorunlu ve absolute > idle** doğrulandı (OWASP + NIST çaprazı) → `MAX_LIFETIME 1800 / IDLE_TIMEOUT 3600` düzenlemesi **PLANNED**: **idle 30 dk, absolute 8 saat** (NIST AAL2 üst sınırı 24 saat esnekliği not edilir). 2) **ASVS 7.2.4 privilege change'de yeniden doğrulama** → hijyen kural 3 bağlayıcı. 3) **NIST seviye tabanlı timeout** → 30 dk/8 saat değerleri AAL2 sınıfıyla uyumlu (AAL3'e geçiş 15 dk/12 saat opsiyonu §5.1 notu). 4) **Fixation üç ilacı** doğrulandı (OWASP + CVE-2022-40293 + CodePath + Imperva): login rotate **VAR** (IMPLEMENTED), `use_strict_mode` **YOK** → zorunlu madde, fixation testi zorunlu. 5) **PHP hardening üç bayrağı** zorunlu (`use_strict_mode`, `use_only_cookies`, `sid_length 32`). 6) **Hibrit saklama doğrulandı** (Auth0 + LoginRadius + Jsonic + caduh + guptadeepak + OWASP 2026): access ≤15 dk, tek kullanımlık rotasyonlu refresh + reuse detection → family revoke, cookie depolama > localStorage; **ama JWT tek başına oturum olamaz** → SSOT sunucu tarafı kalır. 7) **Session expiration header** araştırmasında resmî/standart başlık kanıtı yetersiz → `⚠️ VERIFICATION REQUIRED` (§2.2a-7). |
| Web Search **Alınan Karar** | **ADR-011 kabul edilir — ÜÇ KATMANLI OTURUM KARARI:** **(A) OWASP tam seti:** cookie `HttpOnly` + `Secure`(HTTPS) + `SameSite=Lax` (ADR-010 ile aynı değer — korunur) · oturum kimliği **CSPRNG ≥128 bit** (`sid_length 32` + `random_bytes`) · **login'de `session_regenerate_id(true)`** (IMPLEMENTED korunur) · **idle 30 dk + absolute 8 saat** (çelişki giderilir: `IDLE_TIMEOUT 1800`, `MAX_LIFETIME 28800`) · **privilege change'de yeniden doğrulama + tüm oturumları sonlandırma** (ASVS 7.2.4) · **session expiration header** (standart karşılığı araştırılacak — ⚠️ VERIFICATION REQUIRED) · `session.use_strict_mode=1` + `use_only_cookies=1` zorunlu. **(B) Hibrit saklama:** sunucu tarafı oturum = **SSOT** (öncelik APCu → db fallback, ADR-007 namespace) + **JWT access ≤15 dk** + **rotasyonlu tek kullanımlık refresh (7-30 gün) sunucu deposunda** + **reuse detection → aile iptali**; refresh cookie'de (HttpOnly+Secure+SameSite), localStorage YASAK. **(C) Uyum (ADR-010 şart 3):** JWT stub (`AuthenticationMiddleware:92-106`) kapanana kadar **cookie-auth tek yol — Bearer ile oturum açma AÇILMAZ** (§4.4). **(D) Tam hijyen 6 kuralı:** login rotate · logout invalidation+cookie clear · privilege change tüm oturum sonlandırma · aktif oturumlar görünümü+"hepsini sonlandır" · idle+absolute birlikte · fixation testi (§2.2c). |
| Web Search **Sonuç** | Karar 2024-2026 verisiyle **desteklendi ve netleşti**: OWASP + ASVS + NIST (timeout/rotasyon/privilege-change), fixation (4 kaynak: OWASP, CVE-2022-40293, CodePath, Imperva), PHP hardening (php.net ×2), hibrit/JWT-refresh (6 kaynak: Auth0, LoginRadius, Jsonic, caduh, guptadeepak, OWASP 2026) — **toplam 16 adlandırılmış kaynak, 5 sorgu**; çapraz doğrulama ≥2 kaynak tüm güvenlik iddialarında karşılanır. Kod bulguları §1.1'de IMPLEMENTED/PLANNED olarak ayrıldı: `use_strict_mode`, aktif oturum görünümü, APCu/db deposu, expiration header, hibrit JWT → PLANNED yazıldı, uydurulmadı. `⚠️ VERIFICATION REQUIRED` yalnız (i) session expiration header'ın standart karşılığı, (ii) JWT/Bearer yolu (stub `null`), (iii) dosya-yolu-dışı varsayılanlar (ör. Windows `session.save_path` kesin değeri) için korunur. **Kaynak listesi (16):** 1) OWASP Session Management Cheat Sheet · 2) OWASP ASVS 5.0 V7 (7.1.1-7.3.2, 7.2.4) · 3) NIST SP 800-63-4 · 4) OWASP session fixation rehberi · 5) CVE-2022-40293 · 6) CodePath — session fixation · 7) Imperva — session fixation · 8) php.net — session.* yapılandırması · 9) php.net — `session_regenerate_id()` · 10) Auth0 — JWT vs session · 11) LoginRadius 2026 — token rotation · 12) Jsonic 2026 — refresh rotation + reuse detection · 13) caduh 2025 — refresh token akışı · 14) guptadeepak 2026 — JWT session management · 15) OWASP 2026 — token storage (cookie > localStorage) · 16) OWASP Cheat Sheet Series — oturum/hardening bölümleri (çapraz referans). |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-010 şart 3 (bağlayıcı) | JWT stub (`shared/src/Api/Middleware/AuthenticationMiddleware.php` satır 92-106) kapanana kadar **cookie-auth tek oturum yolu**; Bearer ile oturum açılmaz — §4.4 bu ADR'nin bağlayıcı uyum maddesidir |
| Frozen ADR-001-037 dokunulmaz | Bu ADR yeni karar üretir; frozen metinler okunur/referanslanır, değiştirilmez (AGENTS.md §25.3 kural 2) |
| ADR-004 domain haritası | Oturum cookie'si `domain=.coremusic.net` bilinçli ve kalıcıdır; cookie kararları bu domain genişliğini varsayar (same-site yüzeyi §4.3 risk 4) |
| REDACTED | Secret/credential/anahtar hiçbir koşulda bu ADR'ye yazılmaz |
| In-Place Refactoring | Dosya adı/onay olmadan değiştirilemez; `SessionConfig` sabitleri değer değişikliği dosya adı gerektirmez |
| PHP 8.4 oturum API'si | `session_set_cookie_params` / `session_start` öncesi çağrı sırası ve `session.*` INI bayrakları (strict mode start'tan ÖNCE aktif edilmeli) |
| `.ai/log.md` append-only | Bu işlem dahil tüm kayıtlar yalnız ekleme ile yazılır |

---

## 2. Karar (Decision)

CoreMusic oturum yönetimi **üç katmanlı** olarak kabul edilir: **(A) OWASP tam seti** (cookie + kimlik + timeout + rotasyon + privilege-change), **(B) hibrit saklama** (sunucu tarafı oturum SSOT + kısa JWT access + rotasyonlu tek kullanımlık refresh), **(C) tam hijyen 6 kuralı** — ADR-010 şart 3'e uyum §4.4'te bağlayıcıdır.

### 2.1 Neden Bu Seçenek?

- **Tek katman yetersiz:** yalnız cookie (bugünkü durum) çoklu-cihaz/çoklu-sunucu hedefine tıkalı; yalnız JWT ise logout/iptal (revocation) anlık yapılamaz — 2025-26 literatürü hem OWASP hem Auth0/LoginRadius hattında aynı sonuca varır (§1.3).
- **Çelişki giderilir:** `MAX_LIFETIME 1800 < IDLE_TIMEOUT 3600` sahteliği OWASP/NIST bandıyla (idle 30 dk / absolute 8 saat) düzeltilecek; iki middleware rotasyon tutarsızlığı tek SSOT'a bağlanacak.
- **Mevcut iyi temel korunur:** `HttpOnly`/`SameSite=Lax`/`Secure` (ADR-010 ile aynı), login'de `session_regenerate_id`, 30 dk rotasyon, idle/absolute denetimi — bunlar OWASP'la uyumlu IMPLEMENTED varlıklardır (§1.1); karar bunları **tamamlar**, yıkmaz.
- **Güvenlik-ölçek dengesi:** hibrit, ADR-004 SPA + API büyümesine (Bearer uçlar) alan açar ama **SSOT'u sunucu tarafında tutarak** tek yanlışlıkta (refresh hırsızlığı) tüm oturumu korur.
- **YAGNI:** Redis gibi yeni altyapı gerekmez — APCu (ADR-007 namespace) + db fallback mevcut yığınla hedefe ulaşır; Redis §3 alternatif 3 olarak reddedilir.

### 2.2 Teknik Detaylar

#### 2.2a OWASP Oturum Seti (zorunlu maddeler)

| # | Madde | Değer | Durum |
|---|-------|-------|-------|
| 1 | Cookie parametreleri | `HttpOnly` + `Secure` (HTTPS) + `SameSite=Lax` + `path=/` + `domain=.coremusic.net` + `lifetime=0` | **IMPLEMENTED** (`SessionInitializer.php:40-47`) — korunur, ADR-010 katman 2 ile aynı |
| 2 | Oturum kimliği | CSPRNG ≥128 bit: `session.sid_length = 32` + `random_bytes` tabanlı üretim | **PLANNED** (INİ hardening — §1.3-5) |
| 3 | Login rotasyonu | `session_regenerate_id(true)` login + register'da | **IMPLEMENTED** (`AuthController.php:144/193`, `AuthService.php:166`) |
| 4 | Zamanlanmış rotasyon | 30 dakika (`ROTATION_INTERVAL 1800`) — **tek değer**: `shared` ile `auth` (`900`) birleştirilir, SSOT = `SessionConfig` | **IMPLEMENTED / PLANNED birleştirme** (`SessionLifecycle.php:54-62` vs `auth/.../SessionMiddleware.php:13-14`) — **debate şartı 1 ile kapatılacak (§5.3 · §7.1)** |
| 5 | Timeout | **idle 30 dk** (`IDLE_TIMEOUT = 1800`) + **absolute 8 saat** (`MAX_LIFETIME = 28800`) — absolute > idle kuralı; NIST AAL2 üst sınırı 24 saat referanstır | **PLANNED** (çelişki giderimi — §1.2-1) — **debate şartı 1 ile kapatılacak (§5.3 · §7.1)** |
| 6 | Hardening INİ | `session.use_strict_mode = 1`, `session.use_only_cookies = 1`, `session.use_strict_mode` start-öncesi | **PLANNED** (fixation sunucu kilidi) |
| 7 | Expiration header | Oturum bitişini istemciye bildiren yanıt başlığı (ör. `X-Session-Expires` — ISM/standart karşılığı **⚠️ VERIFICATION REQUIRED**) | **PLANNED** |
| 8 | Privilege change | ASVS 7.2.4: rol/izin/email/şifre değişiminde **yeniden doğrulama + tüm oturumları sonlandırma** | **PLANNED** (§2.2c kural 3) |

#### 2.2b Hibrit Saklama (SSOT + JWT + Refresh)

```
[İstemci]  ── cookie (PHP oturum) ──────────────►  [SSOT: sunucu tarafı oturum]
   │                                                 ├─ depo: APCu (öncelik) → db fallback
   │                                                 └─ ADR-007 cache namespace'e bağlı
   │
   └── API için: access JWT ≤15 dk ─────────────►  [imzalı, stateless — doğrulama stub kapanınca]
                    │
                    └─ refresh (tek kullanımlık, rotasyonlu, 7-30 gün) ──► [SUNUCU DEPOSU]
                              ├─ her kullanımda yenisi döner (rotasyon)
                              ├─ aynı refresh 2. kez görülürse → reuse detection
                              └─ reuse → oturum AİLESİ tamamen iptal (family revoke)
```

- **SSOT = sunucu tarafı oturum:** logout/iptal anlıktır; JWT çalınsa bile ≤15 dk yaşar, refresh reuse'da aile iptal edilir (§4.3 risk 1).
- **Refresh cookie'de:** `HttpOnly` + `Secure` + `SameSite` — **localStorage yasak** (OWASP 2026 token storage).
- **Erişim yolu kuralı:** bugün `validateJwtToken()` hep `null` → **hibrit API yolu PLANNED**; devreye alınana kadar §4.4 geçerlidir.

#### 2.2c Tam Hijyen — 6 Kural (bağlayıcı)

| # | Kural | Durum |
|---|-------|-------|
| 1 | **Login'de oturum kimliği rotasyonu** (`session_regenerate_id(true)`) | IMPLEMENTED — korunur + test zorunlu |
| 2 | **Logout'ta invalidation:** sunucu kaydını sil + tüm oturum cookie'lerini temizle (`restartFresh`), yeniden generate | IMPLEMENTED (tek oturum) — cookie temizliği teyit edilir |
| 3 | **Privilege change'de tüm oturumları sonlandırma + yeniden doğrulama** (rol/izin/email/şifre) | PLANNED (ASVS 7.2.4) |
| 4 | **Aktif oturumlar görünümü + "hepsini sonlandır"** (cihaz/son aktivite listesi) | PLANNED (§1.1 grep boş) |
| 5 | **Idle + absolute timeout birlikte uygulanır** (30 dk / 8 saat) — biri diğerinin yerine geçmez | PLANNED (çelişki giderimi) — **debate şartı 1 ile kapatılacak (§5.3 · §7.1)** |
| 6 | **Fixation testi:** bilinen/önceden verilmiş ID reddedilir (`use_strict_mode`) + login sonrası ID değişimi test edilir | PLANNED (PHPUnit) |

#### 2.2d Zamanlayıcı & Rotasyon Hedef Tablosu

| Parametre | Eski değer | Yeni (karar) | Kaynak |
|-----------|-----------|--------------|--------|
| `IDLE_TIMEOUT` | 3600 (60 dk) | **1800 (30 dk)** | OWASP idle 15-30 dk |
| `MAX_LIFETIME` (absolute) | 1800 (30 dk) | **28800 (8 saat)** | OWASP absolute 4-8 saat; NIST AAL2 ≤24 saat |
| `ROTATION_INTERVAL` | 1800 shared / 900 auth | **1800 tek SSOT** | OWASP 15-30 dk bandı |
| `COOKIE_EXPIRY` | 42000 (12 saat) | korunur (absolute ile uyumlu) | — |
| access JWT | — | **≤15 dk** | §1.3 (Auth0/LoginRadius/Jsonic) |
| refresh token | — | **7-30 gün, tek kullanımlık, rotasyonlu** | §1.3 |

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Mevcut durumu koru** (yalnız dosya tabanlı oturum, mevcut timeout'lar) | Sıfır iş gücü; çalışan login/logout | `1800 < 3600` çelişkisi kalır; `use_strict_mode` yok; iki middleware tutarsız (900/1800); çoklu oturum görünümü yok; ölçek yok | Güvenlik boşlukları (fixation yüzeyi) + bilinen çelişki düzeltilmeden bırakılamaz — §1.2 maddeleri 1, 2, 3, 4 doğrudan reddetme gerekçesi |
| 2 | **Tam stateless JWT** (sunucu-tarafı oturum tamamen kaldırılır) | Yüksek ölçek; API ile homojen kimlik; paylaşı durum yok | Logout/iptal anlık yapılamaz (token ömrüne mahkûm); "hepsini sonlandır" imkansız; refresh hırsızlığı tek kazanç; sunucu tarafı CSRF token barı zorlaşır (ADR-010 synchronizer çöker) | Revocation + ADR-010 synchronizer + ASVS 7.2.4 (tüm oturum sonlandırma) ile **çelişir**; literatür de JWT'yi oturum yerine **ek katman** sayar (§1.3-6) |
| 3 | **Redis tabanlı oturum deposu** | Çok sunucu/cluster için güçlü paylaşım; TTL yerleşik | Yeni altyapı + bağımlılık + operasyon maliyeti; APCu+db mevcut yığınla aynı hedefe ulaşır | YAGNI — mevcut yığında hedef (paylaşım + TTL) APCu (ADR-007) + db fallback ile karşılanır; Redis ileride **supersede** ile değerlendirilebilir |
| 4 | **Yalnız OWASP seti** (hibrit/JWT katmanı yok) | En küçük değişim; hemen uygulanır | ADR-004 SPA + API büyümesine Bearer/refresh yolu açılmaz; API ölçeği için uzun vadede access token alternatifi kalmaz | Kararın **(B) katmanı** olmadan uzun vadeli API stratejisi boşlukta kalır; hibrit hem bugünü (cookie) hem yarını (API) kapsar |
| 5 | **Yalnız hibrit JWT** (OWASP cookie seti ihmal edilir) | JWT/refresh hikâyesi kısa | Cookie yüzeyi (bugünkü gerçek oturum) korumasız kalır; `SameSite`/`HttpOnly`/timeout disiplini çöker | OWASP seti **bugünün** ana savunması; hizmete girmez — bu ADR'nin (A) katmanı olmadan (B) anlamsız |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Mutlak-ırksal çelişki kapanır:** absolute (8 saat) > idle (30 dk) — iki zamanlayıcı artık anlamlı; (`SessionConfig.php` 17-18 düzenlemesi).
- **Fixation savunması derinleşir:** login rotate (zaten var) + `use_strict_mode` + fixation testi → üç ilaç OWASP'la hizalanır.
- **Anlık iptal yeteneği kazanılır:** SSOT sunucu tarafında kaldığı için logout / "hepsini sonendir" / privilege change anında etkilidir.
- **API yolu hazır:** ≤15 dk access + tek kullanımlık rotasyonlu refresh ile ADR-004 SPA/API büyümesine dayanıklı kimlik akışı.
- **Tutarlılık:** `auth` ve `shared` oturum yapılandırmaları tek SSOT'a (`SessionConfig`) bağlanır; iki rotasyon değeri (900/1800) kalmaz.
- **Denetlenebilirlik:** aktif oturumlar görünümü kullanıcılara/kuruma hesap hırsızlığı müdahalesi verir (kural 4).

### 4.2 Olumsuz Sonuçlar

- **Idle 30 dk = daha sık yeniden giriş** (eski 60 dk'dan kısa) — kullanıcı kaybı/frustrasyon riski (risk 3).
- **Absolute 8 saat = zorunlu çıkış** — uzun oturumlu kullanıcılar (editör/panel) için kesinti; UX'te süre uyarısı gerekir.
- **Karmaşıklık artışı:** iki token türü (access/refresh) + sunucu deposu + reuse detection — operasyon/test yüzeyi büyür.
- **INİ/altyapı bağımlılığı:** `use_strict_mode`/`sid_length` sunucu yapılandırmasıdır — kod dışı değişiklik, deploy içeriğiyle taşınmalı.
- **`use_strict_mode` geçiş riski:** devreye alınan an önceden bilinen/aktif ID'ler reddedilir → tek seferlik toplu çıkış dalgası beklenmeli.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **Refresh/access JWT çalınsa** (XSS/istemci sızıntısı) — erişim penceresi açılır | 3 (olası) | 4 (yüksek) | Access ≤15 dk; refresh **tek kullanımlık + rotasyonlu**; **reuse detection → oturum ailesi tamamen iptal**; refresh cookie `HttpOnly+Secure+SameSite`, localStorage yasak; XSS yüzeyi ADR-012 CSP ile kapatılır |
| **APCu kaybı = oturum deposu düşer** → tüm kullanıcılar anlık çıkış ("logout dalgası") | 2 (mümkün) | 3 (orta) | **db fallback** zinciri (APCu → db); hibrit devreye girmezse dosya-tabanlı oturum geri dönüşü (§4.4); oturum yazımı idempotent |
| **Idle timeout 30 dk kullanıcı kaybı** (eski 60 dk) | 4 (çok olası) | 2 (düşük) | Sliding extend (son etkileşimde yenileme — `SessionLifecycle` zaten uzatıyor); UI'da "oturum X dk sonra bitecek" uyarısı; paneller için absolute 8 saat korunur |
| **`use_strict_mode` geçişinde toplu logout dalgası** | 3 (olası) | 2 (düşük) | Bakım penceresi + duyuru; tek seferlik ve geri dönüşlü (INI) |
| **Cookie domain `.coremusic.net` genişliği** — aynı-site subdomain yüzeyi (ADR-004) | 2 (mümkün) | 3 (orta) | Subdomain hijyeni; `__Host-` önekli yedek cookie hedefi (ADR-010 §2.2d ile ortak); ADR-012 XSS kapanı |
| **Session expiration header std. belirsiz** | 3 (olası) | 1 (ihmal) | ⚠️ VERIFICATION REQUIRED — standart/ISM karşılığı doğrulanmadan başlık adı sabitlenmez |

### 4.4 Uyum & Fallback (ADR-010 Şart 3 — bağlayıcı)

- **Uyum maddesi:** `shared/src/Api/Middleware/AuthenticationMiddleware.php` satır 92-106'daki `validateJwtToken()` stub'u **`null` döndüğü sürece** oturumun tek meşru yolu **cookie-auth'tur**; **Bearer ile oturum açma AÇILMAZ**, hibrit API yolu (§2.2b) yalnız stub gerçek imza doğrulaması yaptığında ve ayrı onayla devreye alınır. Bu, ADR-010 şart 3 ile birebir aynı sınırdır.
- **Fallback (kill-switch):** hibrit depo (APCu/db) devreye alınabilir değilse veya başarısızsa sistem **dosya tabanlı mevcut oturuma döner** — OWASP seti (2.2a) bu modda da aynen geçerlidir; hibrit yalnızca ek katmandır, geri dönüş tek config/bayrak iledir.
- **Test kapısı:** §2.2c kural 6 fixation testi + rotasyon/timeout testleri geçmeden hibrit API yolu açılmaz.

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | `SessionConfig` zaman sabitleri: `IDLE_TIMEOUT 1800` / `MAX_LIFETIME 28800` (absolute > idle) + `auth` `SessionMiddleware` değerlerini `SessionConfig`'e bağla (tek SSOT, rotasyon 1800) | Backend Architect + Security Engineer | 1 oturum |
| 2 | PHP INİ hardening: `session.use_strict_mode=1`, `session.use_only_cookies=1`, `sid_length=32` (deploy içeriğine taşınır) + geçiş duyurusu | Security Engineer + DevOps Engineer | 1 oturum |
| 3 | Hijyen kural 3-4: privilege change'de tüm oturum sonlandırma + "aktif oturumlar / hepsini sonlandır" ekranı (db oturum tablosu) | Backend Architect + Data Engineer | 2-3 oturum |
| 4 | Hibrit saklama: oturum deposu APCu (ADR-007 namespace) → db fallback + JWT access ≤15 dk + tek kullanımlık rotasyonlu refresh + reuse detection (aile iptali) — **yalnız §4.4 kapısı açıkken** | Backend Architect + Security Engineer | 3-4 oturum |
| 5 | Testler: fixation (use_strict_mode + login rotate), idle/absolute timeout, 30 dk rotasyon, logout invalidation, reuse detection → family revoke | QA Engineer | 1-2 oturum |
| 6 | Session expiration header (⚠️ standart karşılığı doğrulanır) | Security Engineer | 0.5 oturum |
| 7 | Debate (3 tur / 20 persona) + Tech Lead onayı → Arch Lead akışı — **✅ debate tamam (18/2/0 KABUL, §7.1) + Tech Lead ✅ (2026-09-24); Arch Lead ⏳** | Vault Steward | — |

### 5.2 Geri Dönüş Planı

1. **Kısmi (adım 1-2):** `git revert` — `SessionConfig` sabitleri eski değerlerine (`3600/1800`) döner; INİ bayrakları önceki yapılandırmaya alınır. Oturum kaybı riski yok (kimlik formatı aynı).
2. **Adım 3:** db oturum tablosu/privilage-change kuralı geri alınabilir; mevcut oturumlar silinmez (salt yeni eklenen sonlandırma akışı kalkar).
3. **Adım 4 (hibrit):** hibrit bayrağı kapatılır → **dosya tabanlı oturuma otomatik dönüş (§4.4 fallback)**; refresh/accessToken uçları devre dışı; cookie-auth oturumu etkilenmez.
4. **Adım 5-6:** testler/header kaldırılabilir; davranışsal etkisi yoktur.
5. **Acil:** her adımdan sonra `.ai/log.md` append + `git diff --stat -- .ai/` ile vault bütünlüğü doğrulanır; bozulma → `vault-utf8-writer.mjs repair` + `git checkout`.

### 5.3 Debate Şartları (3 bağlayıcı şart — §7.1, 2026-09-24)

| # | Şart | Bağlantı | Durum |
|---|------|----------|-------|
| 1 | **Timeout bug + rotasyon tekilleştirme (kod):** `SessionConfig.php:17-18` `MAX_LIFETIME 1800 < IDLE_TIMEOUT 3600` çelişkisi **idle 30 dk / absolute 8 saat** olarak + kodda `absolute > idle` doğrulamasıyla kapatılır; iki rotasyon değeri (shared 1800 / auth 900) **tek SSOT** (`SessionConfig` + `SessionLifecycle`; `SessionMiddleware` config'ten okur) — §2'deki ilgili PLANNED/çelişki satırları **debate şartı ile kapatılacak** | §2.2a #4 · §2.2a #5 · §2.2c #5 · §2.2d · §5.1 #1 | **ŞART — PLANNED (zorunlu, kod)** |
| 2 | **Session dayanıklılık (db write-through):** kritik oturumlar APCu'ya write-through + db fallback — APCu kaybı → "logout dalgası" riski (§4.3 risk 2) bu şartla kapatılır; dosya-tabanlı fallback (§4.4) korunur | §4.3 risk 2 · §2.2b depo zinciri · §5.1 #4 | **ŞART — PLANNED (dayanıklılık)** |
| 3 | **Hijyen test paketi:** fixation (`use_strict_mode` + login rotate) · rotasyon (tek SSOT 1800) · idle-absolute (1800/28800) · logout invalidation (`restartFresh`) — 4 başlık testi geçmeden hibrit API yolu açılmaz | §2.2c #6 · §4.4 test kapısı · §5.1 #5 | **ŞART — PLANNED (test kapısı)** |

*Bu 3 şart debate sonucunun bağlayıcı çıktısıdır (§7.1 Tur 3: 18 kabul / 2 çekimser / 0 red → KABUL, 2026-09-24); şartlar kapanmadan §5.1 #1/#4/#5 adımları prod'a girmez.*

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[CLAUDE.md]] | Karar alt registry kuralı (accepted/ dizin sözleşmesi) |
| [[../index]] | Karar dizini — §3 satır 48 `[[ADR-011-session-management]]` (slug eşleşmesi ✅) |
| [[../../CLAUDE.md]] | Vault ana sözleşmesi — 16 Hard Guardrail, REDACTED, Guardrail #16 |
| [[../../AGENTS.md]] | Onay akışı §10, frozen kuralı §25.3, routing §6 (session → Security Engineer) |
| [[../../WORKFLOW.md]] | Debate/onay akışı bağlamı |
| [[../../brain.md]] | Mimari karar özeti (bu ADR'den türetilir) |
| [[../../log.md]] | Audit trail — bu işlem tek satır append |
| [[ADR-004-multi-domain-spa]] | Cookie `domain=.coremusic.net` haritası — oturum cookie'si tüm subdomainlerde yaşar (dosya diskte VAR ✅) |
| [[ADR-007-cache-namespace]] | Hibrit saklama'nın APCu katmanı bu namespace standardına bağlanır (dosya diskte VAR ✅) |
| **Düz metin (diskte VAR — wiki-link KURULMAZ): ADR-008** | Auth bypass kapsamı — oturum politikaları bypass scope'unu değiştirmez |
| **Düz metin (diskte VAR — wiki-link KURULMAZ): ADR-010** | CSRF — **şart 3** bu ADR §4.4 ile doğrudan kesişir; `SameSite=Lax`/synchronizer ile aynı oturum üstünde çalışır |
| **Düz metin (diskte VAR — wiki-link KURULMAZ): ADR-012** | CSP nonce strict-dynamic — XSS ile oturum/JWT hırsızlığını kapatan katman (risk 1'in ikinci kalkanı) |
| `shared/src/Session/SessionConfig.php` | Zaman sabitleri SSOT (satır 16-20) — §2.2d hedef tablosunun uygulama noktası |
| `shared/src/Session/SessionLifecycle.php` | Timeout + rotasyon + CSRF/CSP nonce üretimi (satır 31-66) — IMPLEMENTED çekirdek |
| `shared/src/Session/SessionInitializer.php` | Cookie parametreleri (satır 40-47) — IMPLEMENTED, OWASP setiyle uyumlu |
| `shared/src/Session/SessionBootstrapper.php` | `restartFresh()` logout invalidation (satır 34-59) — hijyen kural 2 |
| `auth.coremusic.net/include/Controller/AuthController.php` · `include/Service/AuthService.php` | Login/register rotate (144/193/225; 166) — hijyen kural 1 IMPLEMENTED |
| `auth.coremusic.net/include/Middleware/SessionMiddleware.php` | **Tutarsız** idle/rotation değerleri (satır 13-14) — §5.1 adım 1'de SSOT'a bağlanır |
| `shared/src/Api/Middleware/AuthenticationMiddleware.php` | Cookie önce (37-45) / Bearer sonra (48-57) / **JWT stub `null` (92-106)** — §4.4 kapısı |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırma protokolü (diskte OKUNDU ✅, v7.2.0) |
| §5.3 · §7.1 debate kaydı | **3 bağlayıcı debate şartı** — (1) timeout bug + rotasyon tekilleştirme (kod, tek SSOT) · (2) session dayanıklılık (db write-through) · (3) hijyen test paketi (fixation/rotation/idle-absolute/logout invalidation); debate 3/20 → 18/2/0 KABUL (2026-09-24) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı — "ADR-011'i sıfırdan yaz") | 2026-09-24 | ✅ |
| Tech Lead | Debate sonucu onayı (18/2/0 KABUL) | 2026-09-24 | ✅ |
| Arch Lead | ⏳ | — | ⏳ |

### 7.1 Debate Kaydı

| Alan | Değer |
|------|-------|
| Biçim | ADR-004/ADR-008/ADR-010 formatı — 3 tur / 20 persona |
| Durum | **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** |
| Sonuç | **KABUL** — Tur 3 oyu: 18 kabul / 2 çekimser / 0 red (2026-09-24) |
| Tech Lead | ✅ (debate sonrası onay — §7 onay tablosu) |
| Şartlar | 3 bağlayıcı şart → §5.3 (timeout+rotasyon tek SSOT · db write-through dayanıklılık · hijyen test paketi) |
| Not | Frontmatter `debate` alanı + §7 Tech Lead satırı bu debate ile güncellendi; kayıt `log.md` append ile alındı |

**Tur 1 — Kanıt sunumu (20 persona).** Kod bulguları: `SessionConfig.php:17-18` `MAX_LIFETIME 1800 < IDLE_TIMEOUT 3600` (**mutlak ömür, boştaklıktan kısa — bug**); **rotasyon çelişkisi** `SessionLifecycle` 30 dk (`ROTATION_INTERVAL 1800`) vs `auth` `SessionMiddleware` 15 dk (`ROTATION 900`) — iki yapılandırma kaynağı; **JWT stub** (`AuthenticationMiddleware.php:92-106`) hep `null` → Bearer yolu fiilen ölü. Oy eğilimi: **15 kabul/neutral, 4 uyarı.** Uyarılar: **Backend** — timeout bug şart olarak kapatılmalı; **QA** — rotasyon tek SSOT'a bağlanmalı; **DevOps** — APCu dayanıklılığı (db write-through); **Critic** — iki SSOT çelişkisi.

**Tur 2 — İtiraz → Çözüm:**

| # | İtiraz | Çözüm | Karar |
|---|--------|-------|-------|
| 1 | Timeout bug (`MAX_LIFETIME 1800 < IDLE_TIMEOUT 3600`) — iki zamanlayıcı anlamsız | **idle 30 dk / absolute 8 saat** + kodda `absolute > idle` doğrulaması | **ŞART 1** |
| 2 | İki rotasyon değeri (1800 shared / 900 auth) — hangisi geçerli belirsiz | **tek SSOT** `SessionConfig` + `SessionLifecycle`; `SessionMiddleware` config'ten okur | **ŞART 1** |
| 3 | APCu kaybı = tüm oturumlar anlık düşer ("logout dalgası") | Kritik oturumlar **db'ye write-through** dayanıklılık (APCu → db zinciri) | **ŞART 2** |
| 4 | Hibrit JWT erken açılma riski (stub `null` iken Bearer) | **ADR-010 şart 3 kilidi** — stub kapanmadan Bearer yok; **§4.4'te teyit** | Teyit — §4.4 (şart değil) |

**Tur 3 — Oy:** **18 kabul / 2 çekimser / 0 red → KABUL** (2026-09-24).

**Bağlayıcı 3 şart:** (1) timeout bug + rotasyon tekilleştirme (kod) · (2) session dayanıklılık (db write-through) · (3) hijyen test paketi (fixation/rotation/idle-absolute/logout invalidation) → ayrıntı ve bağlantılar §5.3.

---

*1.0.0 | 2026-09-24 | Created*
*Authority: ADR-011 Karar Metni — CoreMusic Architecture Decision Record*
*Mode: Red Team · Human Mode · Truth Mode*
