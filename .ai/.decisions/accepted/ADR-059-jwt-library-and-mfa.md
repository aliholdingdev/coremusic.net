---
title: "CoreMusic — ADR-059: JWT Kütüphanesi ve MFA TOTP (lcobucci/jwt + RS256 algoritma kilidi · TOTP MFA akışları · kurtarma kodları · recovery/bypass)"
type: "architecture-decision"
category: "security"
date: "2026-09-29"
updated: "2026-09-29"
version: "1.0.0"
status: "accepted"
authority: "SSOT — CoreMusic JWT + MFA kararı: (a) JWT kütüphanesi = `lcobucci/jwt` + **RS256 algoritma kilidi** (RFC 8725 allowlist; `alg` yalnız RS256, `none`/HS256 yasak), imzalama anahtarı **yalnız `auth.coremusic.net`** (ADR-058), public anahtar JWKS ile yayılır → ADR-052 stub'ını doldurur, (b) MFA TOTP = **tek nokta `auth.coremusic.net`**; login **opsiyonel kurulum / kurulmuşsa her login'de zorunlu**, **admin rolü + hassas işlemler zorunlu (step-up)**, **API/servisler-arası MFA YOK** (makine kimliği), (c) **kurtarma kodları** = 10 adet, tek kullanımlık, ≥64 bit rastgele, **hash'li** set; yenilenince önceki set iptal; e-posta ile **asla** gönderilmez, (d) **bypass/recovery** = tek kullanımlık bypass kodu **YOK**; yol = kurtarma kodu → yeni TOTP bağlama + yeni set → tüm oturumların iptali → olay kaydı + kullanıcı bildirimi; son çare insan destek süreci (ADR-058 break-glass ile hizalı), (e) **hizalama** = ADR-052 (hibrit/stub) + ADR-056 (RBAC step-up) + ADR-058 (tek auth noktası) + ADR-020 (API auth) + ADR-022 (secret şifreleme) + ADR-047 (köprü) + ADR-011/010/013/008 — uygular, yeniden karar vermez"
kaynak: "Disk/kod kanıtı taraması (2026-09-29: 5 composer.json — `lcobucci|firebase|jwt|pragmarx|google2fa|otp` = **0 isabet** → ADR-052 bulgusu tekrar doğrulandı; stub `shared/src/Api/Middleware/AuthenticationMiddleware.php:117` → `return null` `:130`; MFA kod yüzeyi grep = **0 gerçek isabet**; `.ai/.sql/mysql` `mfa|totp|two_factor|backup_code|otp` = **0**; google2fa yalnız vault metinlerinde `brain.md:113` + `vault-template.md:606` + `archives/prompt1-spa-router-2026-09-01.md:1482` + `opencode.json:79`; arşiv `prompt2-auth-2026-08-15.md:164,666` + `prompt-unified-2026-08-15.md:592`) + web araştırması (**6 sorgu / ~30 adlandırılmış kaynak**)"
governance: "Red Team → Human Mode → Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-059: JWT Kütüphanesi ve MFA TOTP

> **Durum:** ✅ **ACCEPTED** — **Tarih:** 2026-09-29 — **Debate:** ✅ **TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** — **Tech Lead:** ✅ — **Arch Lead:** ⏳
> **Karar serisi:** `.ai/.decisions/accepted/` — **Slug:** `ADR-059-jwt-library-and-mfa`
> **İlgili kararlar:** [[ADR-052-hybrid-auth-session-jwt]] (hibrit session/JWT zemini + **stub `null` bulgusu** — bu ADR o stub'ın **doldurulma kararını** yazar) · [[ADR-056-auth-module-implementation]] (RBAC `user_roles` + Permission middleware — **step-up'ın rol kaynağı**) · [[ADR-058-centralized-auth-service]] (tek auth noktası + fail-closed + break-glass — **MFA'nın barınağı ve recovery'nin son çaresi**) · [[ADR-020-api-public-security]] (API auth üçlüsü + Bearer kilidi — **MFA kapsamı DIŞI**, makine kimliği) · [[ADR-047-login-redirect-session-bridge]] (imzalı tek kullanımlık köprü token'ı — **MFA'yı atlamaz**) · [[ADR-022-database-hardened-security]] (AES-256-GCM + Argon2id — **TOTP secret'ının saklama zemini**) · [[ADR-011-session-management]] · [[ADR-010-csrf-protection-strategy]] · [[ADR-013-rate-limiting-apcu]] (MFA deneme limiti) · [[ADR-008-bypass-auth-middleware]] (bypass = üretimde fail-closed — **MFA bypass kodu yok** kararıyla aynı yön) · [[ADR-043-auth-subdomain-consolidation]] · [[ADR-007-cache-namespace]] · [[ADR-004-multi-domain-spa]] · [[../index.md]] · [[../../CLAUDE.md]] · [[../../raw/brain.md]] · [[../../raw/WORKFLOW.md]]
> **Konu çakışması notu:** arşiv prompt bu numarayı **JWT kütüphanesine**, vault-template ise **MFA TOTP'ye** bağlamıştı (ikisi de diskte doğrulandı — §1.1 Tablo C). İki konu **tek ADR'de birleştirildi** (kullanıcı onaylı); numara **bölünmedi**, yenisine ayrılmadı.
> **Index durumu:** `.ai/.decisions/index.md` **ADR-051–ADR-060 satırlarını İÇERMEZ** — dizin **050 (satır 91) → 061 (satır 92)** arasında **atlıyor** (bu işlemde yeniden doğrulandı). Bu işlemde index.md'ye **yeni satır eklenmedi** (report-only — In-Place Refactoring + SRP); satır ekleme **bir sonraki vault reset'ine ertelenmiştir** (§5.1 adım 12).
> **Kaynaksız numara boşlukları:** **ADR-051, ADR-053, ADR-054, ADR-055, ADR-057, ADR-060** diskte dosya olarak **YOK** (`Test-Path`/glob = False). Bu ADR o numaraları **doldurmaz**; yalnız düz metinle işaretler. **⚠️ VERIFICATION REQUIRED:** bu altı numara için karar metni diskte yoktur — her biri kendi kanıtıyla doldurulmalıdır.
> **⚠️ VERIFICATION REQUIRED (diğer):** (i) `brain.md`'de ADR-059 slotu **YOK** (grep = 0) — özet MO (vault-updater) ekleyecektir. (ii) `lcobucci/jwt`'in sürüm aralığı ve `endroid/qr-code` entegrasyonu **kodda 0** — PLANNED, `⚠️` altında. (iii) JWKS yayım yolu deploy'a bağlı (ADR-058 §2.2-b ile aynı belirsizlik).
> **Frozen değil:** ADR-001–037 frozen kapsamı dışındadır; bu dosya **yeni** bir accepted ADR'dir (numara, arşiv/üst görevin atadığı ADR-059 slotunu doldurur — kural 4'teki ADR-088+ aralığı bu seriyle çelişir; **numara çakışması raporlanır, düzeltilmez**, §5.1 adım 13).

---

## 1. Bağlam (Context)

CoreMusic'in auth mimarisi iki **açık** noktayla ilerliyor. Birincisi: ADR-052 hibrit modeli "JWT = API/mobil/servisler-arası" kararını verdi, ama kod tarafında JWT **hiç yok** — `validateJwtToken()` imzası `null` döndürecek şekilde yazılmış bir stub ve hiçbir `composer.json`'da JWT kütüphanesi bulunmuyor. İkincisi: tüm arşiv ve vault dokümanları ADR-059'u **MFA TOTP**'ye bağlamış (Security Engineer promptu kural 10'a kadar), ama kod yüzeyi **tamamen boş** — MFA ile ilgili tek bir satır PHP/JS yok, tek bir veritabanı kolonu yok. Bu karar, iki konuyu (JWT kütüphanesi + algoritma, MFA TOTP akışları) **tek kayıtta** bağlar: kütüphane/anahtar/algoritma seçimi, MFA'nın hangi akışlarda zorunlu olduğu, kurtarma kodları ve bypass/recovery yolu — ve bunları ADR-052/056/058'e hizalar.

### 1.1 Mevcut Durum (disk + kod kanıtı — dürüst etiket, 2026-09-29 taraması)

Etiketler: **IMPLEMENTED** = diskte kod kanıtıyla ispatlı · **PLANNED** = kararlaştırılmış, karşılığı kodda yok · **STUB** = kod var ama gövde boş (geri dönüş değeri sabit) · **0 YÜZEY** = aranıyor, isabet yok.

**Tablo A — JWT yüzeyi (ADR-052 bulgusunun tekrar doğrulanması)**

| İddia | Kanıt | Etiket |
|-------|-------|--------|
| composer'da JWT kütüphanesi var mı? | **5** `composer.json` (`api` · `auth` · `home` · `media` · `shared`; `vendor` hariç) tarandı → `lcobucci\|firebase\|jwt\|pragmarx\|google2fa\|totp\|otp` = **0 isabet** | **0 YÜZEY — ADR-052 bulgusu (0/0) TEKRAR DOĞRULANDI** → PLANNED |
| JWT stub nerede? | `shared/src/Api/Middleware/AuthenticationMiddleware.php:117` → `private function validateJwtToken(string $token): null` · `:130` → `return null` · `:114-115` → "gerçek JWT doğrulaması eklenene kadar dönüş tipi yalnızca `null`'dır" · `:119-121` → "In production, this would use a proper JWT library **with RS256 verification**" | **STUB** (ADR-052 `:111-131` ile hizalı — dosya adı bu işlemde netleştirildi) |
| Stub'ın ürettiği davranış | `:72-76` → `Bearer ` başlığı varsa stub çağrılır, sonuç **atlanır** → `:79-83` → **401 UNAUTHORIZED** · `:73-74` yorumu: "validateJwtToken() her zaman null döndüğü için bu dal 401'e düşer (davranış değişmez)" | **IMPLEMENTED (fail-closed)** — yani stub **açık değil**, yol kapalı |
| Session dalı çalışıyor mu | `:60-68` → `sessionManager->isAuthenticated()` doğruysa `method: session` ile geç | **IMPLEMENTED** (ADR-052 hibritinin session ayağı) |
| Vault'ta beyan edilen paketler | `.ai/raw/brain.md:109` `lcobucci/jwt` (JWT token yönetimi, RS256) · `:114` `endroid/qr-code` · `:113` `pragmarx/google2fa` (MFA/2FA TOTP) · `:115` `league/oauth2-server` | **PLANNED** (vault beyanı — composer'da karşılığı **0**) |

**Tablo B — MFA / 2FA kod yüzeyi (kod + şema taraması)**

| Tarama | Kapsam | Sonuç | Etiket |
|--------|--------|-------|--------|
| `totp\|mfa\|2fa\|twoFactor\|otp` (büyük/küçük harf duyarsız) | tüm `*.php` | **0 gerçek isabet** — yalnız 2 yanlış pozitif: `HomeSongButtonTest.php:54,63` (`useDefault**Art**Fallback` → "Art"), `AIEngine.php:350,362` (`room**Factor**`) | **0 YÜZEY** |
| `google2fa\|pragmarx\|otpauth\|recovery_code\|backup_code` | repo geneli | kodda **0**; isabetler yalnız **metin**: `.ai/raw/brain.md:113` · `.ai/.templates/coremusic-vault-template.md:606` · `.ai/archives/prompt1-spa-router-2026-09-01.md:1482` · `.opencode/opencode.json:79` (Security Engineer promptu kural 10: "MFA: TOTP via pragmarx/google2fa - ADR-059") | **0 kod = PLANNED** |
| `mfa\|totp\|two_factor\|backup_code\|otp` | `.ai/.sql/mysql/*.sql` (18 DB) | **0** — MFA için **hiçbir kolon yok** (`user`/`credential_keys`/`user_tokens` dahil) | **0 şema = PLANNED** |
| Halüsinasyon sinyali (H019) | `.opencode/skills/truth-engine/SKILL.md:166` → "MFA/TOTP iddiası — CoreMusic'de yok (ADR-011)" · `.claude/skills/red-team-truth-mode/SKILL.md:279,570` · `.claude/skills/hallucination-control/SKILL.md:272` | 4 ayrı skill **aynı şeyi** söylüyor: MFA iddiası **kanıtlanamaz** | ⚠️ **Bu ADR MFA'yı "var" değil, "kararlaştırıldı / PLANNED" olarak yazar** |

**Tablo C — Arşiv/vault kaynakları (ADR-059'un konu çakışmasının disk kanıtı)**

| Kaynak (diskte mevcut) | Satır | İçerik | Ne bağladı |
|------------------------|-------|--------|-----------|
| `.ai/archives/prompt2-auth-2026-08-15.md` | `:164` | `\| Algorithm \| RS256 \| ADR-059 \|` | **algoritma** → ADR-059 |
| `.ai/archives/prompt2-auth-2026-08-15.md` | `:666` | `\| Firebase JWT \| lcobucci/jwt \| ADR-059 \|` | **JWT kütüphanesi** → ADR-059 |
| `.ai/archives/prompt-unified-2026-08-15.md` | `:592` | `❌ firebase/php-jwt → ✅ lcobucci/jwt (ADR-059)` | **JWT kütüphanesi** → ADR-059 |
| `.ai/.templates/coremusic-vault-template.md` | `:606` | `MFA: TOTP pragmarx/google2fa (ADR-059)` | **MFA TOTP** → ADR-059 |
| `.ai/archives/prompt1-spa-router-2026-09-01.md` | `:1482` | `pragmarx/google2fa` | paket adayı (yan satırda `45. Multi-Factor Authentication (MFA)`, `:1479`) |

**Dürüst etiket:** dört arşiv/vault dosyasının **tamamı diskte** (`Test-Path = True`) — "eski dosya yok" **değildir**. **Konu çakışması:** aynı numara iki farklı konuya bağlanmıştı (JWT lib ↔ MFA); bu ADR'de **tek dosyada birleştirildi**.

**Tablo D — ADR-059'u referans alan (henüz yazılmamış) kararların geri bildirimi**

| Dosya | İddia | Bu ADR ile durum |
|-------|-------|------------------|
| `ADR-052` (kabul) | stub `null` bulgusu + "JWT kütüphanesi ayrı ADR" iması | ✅ bu ADR'de kapanıyor |
| `ADR-056` (kabul) | "ADR-059'a uygular ama yeniden karar vermez" + `⚠️` "`ADR-059-mfa.md` Test-Path = False" | ⚠️ artık **slug farklı**: dosya `ADR-059-jwt-library-and-mfa.md` — ADR-056'daki `ADR-059-mfa` düz metin atfı **eski tahmin idi** (§5.1 adım 11) |
| `ADR-058` (kabul) | "**ADR-059 (MFA)** bu ADR'nin kapsamı dışıdır" | ✅ sınır korundu — bu ADR auth zaten **MFA'nın barınağını** verir, auth mimarisini **yeniden karar almaz** |
| `opencode.json:79` | Security Engineer kural 10 "MFA: TOTP via pragmarx/google2fa - ADR-059" | ✅ bu ADR ile **kaynağa bağlandı** (önceden kaynağı yoktu) |

### 1.2 Sorun Tanımı

Dört sorun üst üste binmektedir:

1. **JWT kararı verilmiş ama kütüphane seçilmemiş, kod boş.** ADR-052 "API/mobil/servisler-arası JWT" dedi; stub yorumu "proper JWT library with RS256" yazdı; `brain.md:109` `lcobucci/jwt` dedi — ama **hiçbir** composer'da paket yok ve hangi kütüphane, hangi algoritma, anahtar nerede tutulacak **yazılı değil**. "JWT var" iddiası bugün **yanlış** olurdu (H-class halüsinasyon).
2. **Algoritma zayıflığı sınıfı henüz kapatılmamış.** Literatür (`alg: none`, RS256→HS256 key confusion, `kid` keyring confusion — CVE-2025-45769) tek bir savunmayı şart koşar: **algoritma allowlist'i, token'dan *değil* sunucu yapılandırmasından** okunacak. Bu kural hiçbir yerde sabitlenmemiş.
3. **MFA sıfır.** Kod, şema, composer: üçü de 0. Buna rağmen dört farklı vault/skill dosyası MFA'dan **varmış gibi** bahsediyor (H019). Karar verilmezse ya halüsinasyon ya da sessizce askıda kalır.
4. **Kurtarma/bypass yolu hiç yazılmamış.** MFA eklenirken en çok gözden kaçan şey budur: kodu kaybeden kullanıcı **kilitlenir**, ya da operatör "geçici bypass" açar → ADR-008'in üretimde fail-closed olmasıyla çelişir. Yolun **önceden** yazılması gerekir (NIST "account recovery" §4.2, OWASP "Resetting MFA").

### 1.3 Web'den Araştırma Raporu & Sonuçları

Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (diskte mevcut) · **6 sorgu** (web search) · **~30 adlandırılmış kaynak**.

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "lcobucci/jwt vs firebase/php-jwt comparison 2025 PHP JWT library algorithm agility RS256 EdDSA" · (2) "JWT algorithm confusion attack alg none RS256 key confusion RFC 8725 best current practice 2025" · (3) "NIST SP 800-63B AAL2 multi-factor authentication TOTP requirements verifier look-up secret recovery codes 2025" · (4) "MFA recovery codes best practices one-time use hashed rate limited bypass account recovery OWASP 2025" · (5) "RFC 6238 TOTP implementation best practices time window drift replay secret encryption at rest PHP 2025" · (6) "WebAuthn passkeys migration path 2026 replace TOTP phishing resistant AAL3 adoption" |
| Web Search **Konusu** | PHP JWT kütüphanesi seçimi ve **algoritma çevikliği/alg-confusion** saldırı sınıfı; TOTP MFA'nın doğrulayıcı (verifier) şartları; MFA **kurtarma kodu ve bypass** kalıpları; **NIST AAL2**'nin MFA'dan anladığı; **WebAuthn/passkey** ile TOTP'ten geçiş yolu |
| Web Search **Bağlamı** | CoreMusic'te JWT **kodda 0** (stub `null`, 5 composer 0 paket); MFA **kod/şema 0**; `auth.coremusic.net` tek kimlik üretici (ADR-058); cookie `domain=.coremusic.net` tüm alt alanlara yayılıyor (ADR-043/011); rol modeli `user_roles` JSON (ADR-056); `FORCE_AUTH_BYPASS` geçmişte açık kalmış (ADR-058 audit C1) |
| Web Search **Kısa Açıklama** | **Kütüphane:** `firebase/php-jwt` en yaygın ve bakımı kolay (WorkOS: v7.0.5, Nisan 2026, PHP 8.0+, HMAC/RSA/ECDSA/EdDSA; Packagist RS256/EdDSA/PS256 örnekleri), ama **<6.0.0'da `kid` üzerinden algoritma-confusion** geçmişi var (GitLab Advisory / CVE-2025-45769, CVSS 6.5); `lcobucci/jwt` tip-güvenli, immutable builder API'siyle bilinir ama **bakım/zorluk itirazı** kayıtlı (GitHub `PHP-Open-Source-Saver/jwt-auth#228`), r/PHP'de iki kütüphane de "istikrarlı" bulunuyor, Prodjex ikisini güvenlik/uisel yönüyle karşılaştırıyor. **Saldırı sınıfı:** PortSwigger/WorkOS/Auth0/RFC 8725 — `alg:none` ve **RS256 public-key'in HS256 secret'ı olarak kullanımı**; savunma = **algoritmayı açıkça sabitle** (allowlist'te `RS256` ve başkası yok) + `kid`'i keyring'de tek tipte anahtarla kullan. **MFA:** NIST SP 800-63B — AAL2 iki ayrı faktör ister; TOTP (multi-factor OTP) AAL2'yi **destekler ama AAL3'e ulaşmaz** (phishing-resistant değil); doğrulayıcının işi: **rate-limit (≈100 ardışık hatadan sonra kilitleme), replay direnci (her kod 1 kez), e-posta/VoIP ile OTP yasak, SMS "restricted"**. **Kurtarma kodu:** NIST §4.2 — ≥64 bit rastgele, **hash'lenmiş** saklanır; OWASP MFA Cheat Sheet — **tek kullanımlık** kod seti + MFA sıfırlama usulü; Keycloak #8518 — set hash'li, **yeni set eskiyi iptal eder**. **Gelecek:** NIST SP 800-63-4 (Temmuz 2025) passkey'leri AAL2 olarak tanıdı; LoginRadius/Dashlane/IDManagement 2026'da **passkey/FIDO2'yi birincil** yapan trendi yazıyor. |
| Web Search **Uzun Açıklama** | **(i) Kütüphane + algoritma:** Literatürde "hangi kütüphane" sorusu **ikincil**, "algoritma nasıl sabitleniyor" sorusu **birincil** olarak duruyor. WorkOS'un defensive pattern'i net: "If you are using RS256, the algorithms list should contain `RS256` and nothing else." Auth0, RFC 8725 taslağını "RS256 public-key as HS256 secret" saldırısının çözümü olarak tanıtır; PortSwigger bunu laboratuvar saldırısı olarak öğretir; RFC 8725 §3.2 hem `none` algoritmasını hem uygunsuz algoritmaları yasaklar ve "her anahtar tam olarak **bir** algoritma ile kullanılmalı" der. Yani CoreMusic'in stub yorumunda yazdığı "RS256 verification" tek başına yeterli değil — **allowlist + tek-algoritma/anahtar eşleşmesi + `kid` yönetimi** gerekiyor. Kütüphane tarafında iki aday da üretimde kullanılıyor; **firebase**'in dezavantajı geçmiş `kid`-keyring confusion'u (CVE-2025-45769 — "straightforward way to use the library unsafely"), **lcobucci**'nin dezavantajı bağımsız projelerde bakım zorluğu şikayeti. Her ikisi de RFC 8725'e doğru yapılandırıldığında güvenli; seçim **vault'un kendi beyanıyla** (brain.md:109 + arşiv :592/:666 "firebase ❌ → lcobucci ✅") örtüşen yönde yapılmalı, aksi halde vault ile kod çelişir. **(ii) TOTP MFA:** RFC 6238 §5.2 — varsayılan **30 sn** time-step; "at most one time step" gecikme önerilir; **"verifier, başarılı doğrulama sonrası aynı OTP'nin ikinci denemesini KABUL ETMEMELİ"** (replay); §6 — ±1-2 step drift resync (≈89 sn). §5.1 — anahtarlar **güvenli/şifreli** saklanmalı, RAM'de maruziyet kısa tutulmalı. NIST SP 800-63B (800-63-4) AAL2: iki ayrı faktör, **replay-resistant** en az bir authenticator, **en az bir phishing-resistant seçenek sunma** (SHOULD); OTP kategorisi **phishing-resistant değildir** → AAL3'e çıkmaz. Avatier 2026 rehberi verifier şartlarını dört maddede toplar: rate-limit/throttle (≈100 deneme tavanı), tek-kullanım replay, **e-posta ile OTP yasak**, SMS "restricted". OWASP MFA Cheat Sheet: kurulumda **tek kullanımlık kurtarma kodları** + sıfırlama usulü. **(iii) Kurtarma/bypass:** NIST §4.2 "Saved Recovery Code": ≥64 bit, onaylı rastgele üreteçten, **hash'li** saklanır, abone yenileme isteyebilir; "Issued Recovery Code" sınırlı ömür. Keycloak uygulaması: SHA256 hash'li, **yeni set üretimi eski seti siler**. security.stackexchange tartışması kurtarma kodunu "backdoor" değil **erişim kaybı çözümü** olarak konumlar (kayıp riski vs hırsızlık riski) — kodun gücü ve tek-kullanımı belirleyicidir. Twilio: kurtarma akışının **kendisi** ek risk üretir (destek maliyeti $40-70/arama) → kanal seçimi dikkatli olmalı. **(iv) WebAuthn geçişi:** Passkey'ler origin'e kriptografik bağlı olduğundan AiTM phishing'ine dirençlidir; NIST SP 800-63-4 (Temmuz 2025) passkey'leri AAL2-compliant tanıdı, donanım FIDO2 anahtarları AAL3 karşılıyor (capetron checklist, Avatier 2026, IDManagement playbook). TOTP **kaldırılmıyor**: "fallback/recovery kanalı + düşük etkili hesaplar + geçiş dönemi" rolünde tutuluyor. |
| Web Search **Paragraf Veri Uzun** | 6 sorgu / ~30 adlandırılmış kaynak; her iddia en az 2 bağımsız kaynakla çaprazlandı. **Birincil standart/RFC:** NIST SP 800-63B (800-63-4 + 800-63-3), NIST SP 800-63-4 (Temmuz 2025), RFC 8725 (JWT BCP), RFC 6238 (TOTP), RFC 4226 (HOTP), FIPS 140. **Saldırı/analiz:** PortSwigger Web Security Academy (algorithm confusion), WorkOS (algorithm confusion + PHP JWT rehberi), Auth0 (RFC 8725 draft), GitLab Advisory Database + OpenCVE (CVE-2025-45769), TrustedSec "Keys to JWT Assessments", Vaadata JWT vulnerabilities. **Kütüphane/karşılaştırma:** Packagist `firebase/php-jwt`, Prodjex (Lcobucci vs Firebase), GitHub `PHP-Open-Source-Saver/jwt-auth#228`, Reddit r/PHP "What php jwt Library are people using?". **MFA/kurtarma:** OWASP Multifactor Authentication Cheat Sheet, OWASP Authentication Cheat Sheet, NIST §4.2 account recovery, Keycloak discussion #8518, Twilio MFA account recovery, security.stackexchange (recovery codes + TOTP drift), Avatier OTP/NIST 2026, SentinelOne broken-auth timeline. **TOTP uygulama:** RFC 6238 §5-§6, authgear "5 Common TOTP Mistakes (2026)", OLOID TOTP guide, Protectimus TOTP algorithm. **WebAuthn/gelecek:** capetron NIST 800-63 checklist, LoginRadius phishing-resistant, Dashlane passkeys 2026, Avatier passwordless 2026, IDManagement.gov playbook, guptadeepak passwordless 2026. |
| Web Search **Sonucu** | (1) **JWT kütüphanesi seçilebilir — ama asıl karar algoritma kilididir.** Her iki aday da `RS256` allowlist + tek-algoritma/anahtar eşleşmesi + `kid` disipliniyle güvenli; kütüphane seçimi vault beyanı (`lcobucci/jwt`) ile **aynı** yönde yapılmalı. (2) **`alg: none` / RS256→HS256 sınıfı kapatılır** — allowlist sunucu yapılandırmasında, token header'ından okunmaz. (3) **TOTP AAL2'yi karşılar, AAL3'ü karşılamaz** → WebAuthn/passkey **gelecek yol** olarak yazılır, bugünün şartı değil. (4) **Doğrulayıcı şartları (rate-limit, tek-kullanım, e-posta OTP yasak) kütüphaneden bağımsız, bu ADR'nin bağlayıcı maddeleri** olur. (5) **Kurtarma kodu zorunlu** — yoksa MFA = erişim kaybı; ama kod seti hash'li + tek kullanımlık + yenisi eskisini iptal eder. (6) **Bypass kodu hiçbir kaynakta önerilmiyor**; OWASP/NIST sıfırlama usulünü (kayıtlı kanal + kayıt + yeniden bağlama) tarif ediyor, "operatör atlaması" tarif etmiyor. **Karşıt bulgu:** firebase'in yaygın oluşu ve lcobucci bakım itirazı **bu karara ters** bir ağırlık taşır → §3 alternatif 1 + §4.3 risk 6'da açıkça kayda geçer, **gizlenmez**. |
| Web Search **Alınan Karar** | **(a) JWT:** `lcobucci/jwt` (vault beyanı: `brain.md:109`, arşiv `:592`/`:666`) + **RS256 tek algoritma** (allowlist sunucuda; `none`/HS256/ES *token'dan* kabul edilmez), imzalama anahtarı **yalnız `auth.coremusic.net`** (`.env`/credential vault — **REDACTED**, ADR-022 zemini), public anahtar **JWKS + `kid`** ile yayılır (ADR-058 §2.2-b hedefi), `iss/aud/exp/nbf` zorunlu, `exp ≤ 15 dk` (ADR-052). **(b) MFA TOTP:** tek nokta `auth.coremusic.net`; **login: opsiyonel kurulum, kurulmuşsa her girişte zorunlu** · **admin rolü (`user_roles`): zorunlu** · **hassas işlemler: step-up zorunlu** (şifre değişimi/sıfırlama onayı, e-posta değişimi, API anahtarı/certificate üretimi [ADR-020], OAuth bağlantı yönetimi, kurtarma kodu yenileme) · **API/servisler-arası: MFA yok** (makine kimliği; ADR-020/058 kapsamı). Doğrulayıcı: 30 sn step, **±1 step pencere**, **tek-kullanım (replay reddi)**, **hash_equals** karşılaştırma, **ADR-013 rate limit** ile ardışık deneme tavanı, tüm denemelerde olay kaydı. Secret **ADR-022 ile şifreli** (AES-256-GCM) + QR yalnız kurulum anında (`endroid/qr-code`, PLANNED). **(c) Kurtarma kodları:** 10 adet, her biri ≥64 bit onaylı rastgele, **tek kullanımlık**, veritabanında **hash'li**; yalnızca kurulumda gösterilir (indirme/kopya), **e-posta/SMS ile gönderilmez**; yeni set üretimi **önceki seti iptal eder**; tüm kurtarma işlemleri **oturumları iptal eder + kullanıcıya bildirilir**. **(d) Bypass/recovery:** **tek kullanımlık bypass kodu / "admin atla" YOK** (ADR-008 fail-closed ile aynı yön); yol = kurtarma kodu → yeni TOTP bağlama + yeni kod seti → `ADR-052` uyarınca tüm oturum/refresh iptali → denetim kaydı + kullanıcı bildirimi; kurtarma kodu da yoksa **insan destek süreci** (kimlik kanıtı + zaman gecikmeli onay — ADR-058 break-glass ile hizalı), **e-posta tek başına yeterli kanal DEĞİLDİR** (NIST: e-posta OOB değil). **(e) Hizalama:** ADR-052 stub'ı bu ADR doldurur; ADR-056 rolü step-up'a bağlar; ADR-058 tek barınak + fail-closed; ADR-047 köprü token'ı MFA'yı **atlamaz**; ADR-020 API MFA dışı; ADR-022 secret şifrelemesi; ADR-013 deneme limiti; ADR-010 CSRF; ADR-011 oturum. |
| Web Search **Sonuç** | Karar **destekleniyor**: algoritma kilidi, verifier şartları, kurtarma kodu ve "bypass kodu yok" kararları **bağımsız kaynaklarda oybirliğiyle** var (RFC 8725 · RFC 6238 · NIST 800-63B · OWASP ×2 · Keycloak uygulaması). **İki gerilim** açıkça kabul edildi: (1) **lcobucci bakım itirazı** → §4.3 risk 6 + §5.1 adımlarda sürüm/izleme maddesi; (2) **TOTP phishing'e dirençli değil** → §2.2-f WebAuthn geçiş yolu + §4.3 risk 4. **⚠️ VERIFICATION REQUIRED:** `lcobucci/jwt` sürümü, JWKS yayım yolu ve QR entegrasyonu **kod kanıtıyla doğrulanmadı** (hepsi PLANNED — paket composer'da yok). |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Frozen ADR'ler (001–037) | Değiştirilemez; bu ADR yalnızca okur ve atıf yapar |
| ADR-052 sınırı | "Session = SSOT, JWT = API/servisler-arası, access ≤15 dk" — bu ADR **JWT'nin kütüphanesini/algoritmasını** yazar, **ömür/rotasyon/iptal kararını yeniden almaz** |
| ADR-058 sınırı | Tek auth noktası + fail-closed + 5 fazlı migrasyon **korunur**; MFA `auth.coremusic.net` dışında **kurulamaz**; ADR-058 "ADR-059 kapsam dışıdır" dedi → bu ADR auth mimarisini **değiştirmez** |
| ADR-056 sınırı | Rol/permission modeline dokunulmaz; step-up **yalnız** mevcut rolü okur (`user_roles` JSON) |
| ADR-020/039 | API anahtarı/makine kimliği **MFA'ya tabi değildir** — MFA insan oturumuna aittir |
| In-Place Refactoring | Dosya adı değişikliği **yok**; `index.md`'ye satır eklenmedi (report-only) |
| UTF-8 yazım protokolü | Tüm vault yazımları `vault-utf8-writer.mjs` üzerinden; log.md yalnız `append` |
| Hallucination sweep | Diskte olmayan dosyaya wiki-link **yok**; ADR-051/053/054/055/057/060 = düz metin + `⚠️ VERIFICATION REQUIRED` |
| Kanıt = kod | Yalnız `Test-Path`/satır numarası ile doğrulanan iddialar; paket/şema/kod **0** olan her şey **PLANNED** (H019) |
| Debate | `debate: ✅ TAMAMLANDI` — 3 tur / 20 persona, 18/2/0 KABUL; Tech Lead ✅, Arch Lead ⏳ (§7.1) |
| REDACTED | `.env`, JWT imzalama anahtarı, TOTP secret'ı, kurtarma kodu, DB parolası **hiçbir** ADR'ye yazılmaz |
| Numara serisi | Yeni ADR'ler kural 4 gereği ADR-088+ aralığına ayrılmıştır; bu dosya arşivin atadığı **059** slotunu doldurur → çakışma **raporlanır, düzeltilmez** (§5.1 adım 13) |

---

## 2. Karar (Decision)

JWT kütüphanesi + algoritma, MFA TOTP akışları, kurtarma kodları ve bypass/recovery yolu aşağıdaki beş başlıkta **sabitlenir**.

### (a) JWT kütüphanesi + algoritma — ADR-052 stub'ını doldurur

| # | Karar | Değer | Durum |
|---|-------|-------|-------|
| 1 | **Kütüphane** | `lcobucci/jwt` (vault beyanı `brain.md:109` + arşiv `:592`/`:666` — `firebase/php-jwt` → `lcobucci/jwt` geçişi zaten kararlaştırılmış) | ⏳ **PLANNED** (composer'da 0) |
| 2 | **Algoritma kilidi** | **RS256 tek başına**. Sunucu yapılandırmasındaki allowlist = `{RS256}`; token header'ındaki `alg` **yalnızca** bu listeyle eşleşirse kabul. `none`, `HS256`, `ES*`, `PS*` **reddedilir** (RFC 8725 §3.2) | ⏳ PLANNED |
| 3 | **Algoritma kaynağı** | Allowlist **kod/yapılandırmadan** okunur; token'dan okunmaz → alg-confusion sınıfı kapanır | ⏳ PLANNED |
| 4 | **Anahtar yönetimi** | İmzalama (private) anahtarı **yalnız `auth.coremusic.net`** — `.env`/credential vault (**REDACTED**, ADR-022 AES-256-GCM zemini). Diğer servisler **yalnız public** okur (JWKS + `kid`). **Bir anahtar = bir algoritma** (RFC 8725); `kid` keyring'de **tek algoritma** ile kullanılır (CVE-2025-45769 dersi) | ⏳ PLANNED |
| 5 | **Zorunlu claim'ler** | `iss` (auth.coremusic.net) · `aud` (hedef servis) · `exp` (**≤ 15 dk** — ADR-052) · `nbf`/`iat` · `jti` (reddetme listesi için, ADR-052 (d)) | ⏳ PLANNED |
| 6 | **Doğrulama yolu** | `AuthenticationMiddleware::validateJwtToken()` (`:117`) gerçek gövdeye kavuşur; dönüş tipi `null` → doğrulanmış kimlik DTO'su; **doğrulanamazsa 401 (fail-closed)** — bugünkü davranış **korunur** | ⏳ PLANNED |
| 7 | **Bugün geçerli yol** | JWT gelmeden önce **yalnız session** geçerli (`:60-68`); `Bearer` dalı 401'de kalır (ADR-052/058 hattı korunur) | ✅ **IMPLEMENTED** |

### (b) MFA TOTP — hangi akışta zorunlu / opsiyonel

| Akış | MFA kararı | Gerekçe / hizalama |
|------|-----------|--------------------|
| **Login — standart kullanıcı** | **Opsiyonel kurulum; kurulmuşsa her girişde ZORUNLU** (kapatma yok, atlanamaz) | NIST AAL2 hedefi; kullanıcı dostu; `auth.coremusic.net` login sayfası |
| **Login — admin rolü** | **ZORUNLU** (kurulum şartı; devre dışı bırakma **yok**) | ADR-056 `user_roles` → rol okunur, yetki **artırılmaz**; privileged hesap = en yüksek patlama yarıçapı |
| **Hassas işlem (step-up)** | **ZORUNLU** — oturumda son **5 dk** içinde MFA doğrulanmadıysa istenir | · şifre değişimi ve şifre sıfırlama **onayı** — e-posta değişimi — API anahtarı/sertifika üretimi ([[ADR-020-api-public-security]]) — OAuth bağlantı ekleme/kaldırma — kurtarma kodu yenileme — rol/izin değişimi |
| **API / servisler-arası** | **YOK** | Makine kimliği (API key / JWT) — insan faktörü yok; [[ADR-020-api-public-security]] + [[ADR-058-centralized-auth-service]] (b) kapsamı |
| **BypassAuth / servis-içi okuma** | **YOK — MFA da fail-closed** | [[ADR-008-bypass-auth-middleware]] ile aynı yön: bypass açıkken bile MFA **atlanamaz**; MFA doğrulanamazsa step-up **reddedilir** |
| **Yeniden kimlik doğrulama** | Step-up MFA **oturumda kalıcı sayılmaz** (her hassas işlemde yeni doğrulama) | NIST: reauth intent + replay direnci |

**Doğrulayıcı kuralları (bağlayıcı — kütüphaneden bağımsız):** time-step **30 sn** (RFC 6238 §5.2) · kabul penceresi **±1 step** (~90 sn) · **başarılı doğrulama sonrası aynı kodun ikinci denemesi REDDEDİLİR** (replay — RFC 6238 §5.2 son paragraf) · karşılaştırma **`hash_equals()`** (timing-safe) · ardışık başarısız deneme **[[ADR-013-rate-limiting-apcu]]** rate limit'i + ek MFA tavanı · **e-posta ile OTP gönderilmez**, SMS **kullanılmaz** (NIST restricted / OOB değil) · tüm deneme/sıfırlama olayları **denetim kaydına** yazılır.

### (c) MFA kurtarma kodları — kayıp senaryosu

| # | Kural |
|---|-------|
| 1 | **10 adet** tek kullanımlık kod; her biri **≥ 64 bit** onaylı rastgele üreteçten (NIST §4.2 "Saved Recovery Code") |
| 2 | Veritabanında **hash'li** (onaylı tek yönlü fonksiyon) — **düz metin asla** saklanmaz |
| 3 | **Yalnızca kurulum anında** gösterilir (indir/kopyala); sunucu **düz metni tutmaz**, e-posta/SMS ile **göndermez** |
| 4 | **Tek kullanımlık**; kullanılan kod anında geçersizleşir; **yeni set üretimi önceki setin tamamını iptal eder** (Keycloak #8518 modeli) |
| 5 | Kod kullanıldığında: **tüm oturumlar + refresh token'lar iptal** (ADR-052 (d)), **yeni TOTP bağlanır**, kullanıcıya **bildirim** gönderilir, denetim kaydı yazılır |
| 6 | Kod seti **sadece MFA kurulumu tamamlandıysa** verilir (MFA'sız hesaba kurtarma kodu **üretilmez**) |

### (d) Bypass / recovery akışı — güvenli kurtarma, yedek kanal

**Karar: kalıcı veya tek kullanımlık "bypass kodu" YOK. Üç kademeli, denetlenebilir yol:**

```
Kademe 1 — TOTP erişilebilir      → normal MFA girişi
Kademe 2 — TOTP secret kaybı      → PAROLA + kurtarma kodu (tek kullanım)
                                   → yeni TOTP bağla + yeni kod seti üret
                                   → TÜM oturumları iptal et + kullanıcıya bildir + denetim kaydı
Kademe 3 — kurtarma kodu da yok   → İNSAN DESTEK SÜRECİ: kimlik kanıtı + kayıtlı kanal
                                   → ZAMAN GECİKMELİ onay (bekleme penceresi) + olay kaydı
                                   → ADR-058 break-glass ile hizalı (test edilmiş acil yol)
```

- **Yedek kanal = e-posta DEĞİL.** NIST e-postayı out-of-band kanal olarak kabul etmez; e-posta hesabı **parolanın yanında** durur → ikinci faktörü "bilinen şeye" indirger. Yedek kanal = **kurtarma kodu** + (kademe 3) **insan süreci**.
- **Operatör bypass'ı YOK:** destek ekibi "MFA'yı kapat" diyemez; yalnız **yeniden bağlama** yapabilir (kademe 3), o da kayıt + gecikme ile.
- **Brute-force'ı kapatar:** kurtarma kodu denemeleri de rate-limit + tavan kapsamındadır; kod **hash'li** olduğu için DB sızıntısında bile doğrudan kullanılamaz.
- **MFA yorgunluğu (push-fatigue) bu tasarımda yok:** TOTP push göndermez → NIST'in "authentication fatigue" riski uygulanmaz.

### (e) Hizalama — ADR-052 / ADR-056 / ADR-058

| Karar | Hizalandığı ADR | İlişki |
|-------|-----------------|--------|
| JWT kütüphanesi + RS256 kilidi | **[[ADR-052-hybrid-auth-session-jwt]]** | Onun **stub `null` bulgusunu doldurur**; `exp ≤ 15 dk` + `jti`/reddetme listesi **onun** kararıdır — yeniden alınmaz |
| Step-up = rol + hassas işlem | **[[ADR-056-auth-module-implementation]]** | `user_roles` JSON okunur; Permission middleware **fail-closed** → step-up da fail-closed |
| MFA tek noktada + break-glass | **[[ADR-058-centralized-auth-service]]** | `auth.coremusic.net` dışında MFA **kurulmaz**; kademe 3 onun **L3 break-glass**'ı ile hizalıdır; fail-open **yok** |
| API/servisler-arası MFA yok | **[[ADR-020-api-public-security]]** | Bearer kilidi + API anahtarı makine kimliğidir |
| Köprü token'ı MFA'yı atlamaz | **[[ADR-047-login-redirect-session-bridge]]** | Köprü token'ı **tek kullanımlık/imzalı**; MFA step-up'ı **atlatma kanalı** yapılmaz |
| TOTP secret şifreli | **[[ADR-022-database-hardened-security]]** | AES-256-GCM (96-bit IV) + anahtar yönetimi; parola Argon2id |
| Deneme limiti | **[[ADR-013-rate-limiting-apcu]]** | MFA + kurtarma kodu denemeleri kapsam içi |
| Oturum/cookie | **[[ADR-011-session-management]]** · **[[ADR-010-csrf-protection-strategy]]** | MFA oturumu **yeni cookie üretmez**; step-up POST'u CSRF tabanlı |
| Bypass yönü | **[[ADR-008-bypass-auth-middleware]]** | Üretimde fail-closed → MFA da fail-closed |

### 2.1 Neden Bu Seçenek?

1. **Kütüphane seçimi vault'u takip eder, vault'u çürümez.** `brain.md:109` ve iki arşiv satırı (`:666`, `:592`) zaten `lcobucci/jwt` diyor; alternatif seçmek **SSOT çelişkisi** üretirdi ve ayrı bir revizyon ADR'si isterdi. Ayrıca lcobucci'nin immutable/tip-güvenli API'si, algoritma allowlist'inin **tip düzeyinde** sabitlenmesine uygun.
2. **RS256 tek başına seçilir çünkü** hizmetler-arası zaten asimetrik anahtar istiyor (ADR-058 JWKS hedefi); HS256 tüm servislere aynı secret'ı dağıtmak demek (sızıntı = tüm servisler düşer); EdDSA **esneklik olarak** açık bırakılır ama bugün **aktif değildir** (paket 0 → sürüm desteği doğrulanamadı, `⚠️`).
3. **Bypass kodu yok denmesinin nedeni:** her bypass kanalı, saldırganın hedeflediği **tek şeydir**. NIST/OWASP kurtarmayı "kayıtlı kanal + tek kullanımlık kod + kayıt" ile tarif eder; operatör atlaması tarif etmez. ADR-008'in üretimi fail-closed kararının MFA ayağı da böyle kapanır.
4. **MFA'nın "login'de opsiyonel ama hassas işlemde zorunlu" oluşu** denge içindir: tüm kullanıcıları zorlamak kurulum/kayıp yükü getirir (Twilio: destek maliyeti), **hiç zorlamamak** ise parola sızıntısı (Verizon DBIR hattı) + credential-stuffing açık bırakır. Adım step-up, **patlama yarıçapı yüksek olan yerde** zorunlu kılar.
5. **WebAuthn bugün şart değil, yarın yol:** NIST 800-63-4 passkey'leri AAL2'ye aldı; ADR bunu **faz 2 hedefi** olarak yazar, TOTP'yi bugünün çözümü olarak korur — erken zorunluluk ADR-039 "big-bang yasak" ilkesine aykırı olurdu.

### 2.2 Teknik Detaylar

1. **JWT doğrulama zinciri (hedef):** `Authorization: Bearer <jwt>` → `AuthenticationMiddleware::handle` (`:72-76`) → `validateJwtToken()` (`:117`, gövde PLANNED) → (i) header/payload ayrıştır, (ii) **allowlist `['RS256']`** ile eşleştir — uyuşmazsa **401**, (iii) `kid` → JWKS public anahtarı (tek algoritma), (iv) `iss`/`aud`/`exp`/`nbf` denetimi, (v) doğrulanmış kimlik DTO'su → `$request['_auth_user']` (`method: jwt`). **Herhangi bir adım başarısız = 401** (bugünkü 401 davranışı **korunur**; geriye dönük kırılma yok).
2. **Kütüphane bağımlılığı (PLANNED):** `composer require lcobucci/jwt` → `shared/composer.json` (tüm servisler path-repo üzerinden paylaşır, ADR-039); `endroid/qr-code` (`brain.md:114`) **yalnız** `auth.coremusic.net`'de QR üretir. Sürüm aralığı **`⚠️ VERIFICATION REQUIRED`** (composer'da 0 → seçilecek sürüm disk kanıtıyla doğrulanmadı).
3. **Şema (PLANNED — 18 DB'de bugün yok):** `mfa_settings` (`user_id`, `secret_enc` AES-256-GCM, `confirmed_at`, `alg=TOTP`, `digits=6`, `step=30`) · `mfa_recovery_codes` (`user_id`, `code_hash`, `used_at`, `created_at`) · `mfa_events` (`user_id`, `type`, `ip`, `ua`, `created_at`). **Migration** `[[../../.templates/infrastructure/migration-template.md]]`'ten türetilir (Data Engineer).
4. **Step-up mekanizması:** hassas işlem handler'ı önce `requireMfaFresh(≤5dk)` çağırır → oturumda `mfa_verified_at` yok/eskiyse `401 {reason: "MFA_REQUIRED"}` → `auth.coremusic.net` step-up ekranı → doğrulama → **aynı** dönerek devam. **Yeni oturum/cookie üretilmez.** Step-up reddi **fail-closed** (ADR-056 Permission middleware ile aynı davranış).
5. **Kurtarma kodu üretimi:** `random_bytes(8)` minimum (64 bit) + gruplanmış gösterim; **kayıtta** `hash('sha256', $code . $pepper)` (pepper `.env`, REDACTED); karşılaştırma `hash_equals`. Üretim **tek transaction'da**: yeni set yaz → eski setleri sil.
6. **Oturum iptali:** kurtarma kodu kullanıldığında session id + refresh aile listesi toplanır ve **toplu iptal** (ADR-052 (d) "aile iptali" ile aynı mekanizma) → MFA kırılması oturum çalmayı da temizler.
7. **Rate limit:** `RateLimiterMiddleware` (ADR-013) zaten pipeline'da; MFA doğrulama ucu **muaf listeye alınmaz** (ADR-058'in `/validate-key` muafiyeti dersi ile aynı kural) + **per-account** ek tavan.
8. **Geçiş (fazlı, flag-day yok):** faz 1 = şema + kütüphane + RS256 kilidi + stub'ın gerçek gövdesi (401 davranışı **değişmez**) → faz 2 = TOTP kurulumu UI + kurtarma kodları → faz 3 = admin rolü için **zorunlu** → faz 4 = hassas işlemler step-up → faz 5 = WebAuthn/passkey **hazırlık** (opsiyonel pilot).
9. **Test (QA):** `exp` süresi dolan token → 401 · `alg: HS256` ile imzalanmış token → **401** (alg-confusion regresyon testi) · aynı TOTP kodunun 2. kullanımı → **401** · pencere dışı (±2 step) → 401 · kullanılmış kurtarma kodu → 401 · kurtarma sonrası eski oturum → 401 · `validateJwtToken` stub'ın **hiçbir** yerde `null` döndürmediği → tip/test kanıtı.

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **`firebase/php-jwt`** | En yaygın PHP JWT kütüphanesi; basit API; RS256/EdDSA/PS256 hazır (Packagist); WorkOS'a göre PHP 8.0+ / v7.0.5 (Nisan 2026) aktif | **<6.0.0 `kid` keyring algoritma-confusion** geçmişi (CVE-2025-45769, CVSS 6.5); vault `brain.md` ve iki arşiv satırı **bu kütüphaneyi terk etmiş** görünüyor (`❌ firebase/php-jwt → ✅ lcobucci/jwt`) | **SSOT çelişkisi:** vault zaten `lcobucci/jwt` diyor; seçmek ayrı ADR isterdi. **Not (dürüst kayıt):** r/PHP ve Prodjex bu kütüphaneyi üretimde güvenli buluyor — karar **güvenlik reddi değil, vault hizası**; kütüphane değişikliği §5.1 adımla geri alınabilir |
| 2 | **Kütüphane yok — stub'ı elle doldur** (`JWT::decode` yerine kendi base64/HMAC kodu) | Sıfır bağımlılık; ADR-002 "minimal" ruhu | Algoritma-confusion sınıfını **elle** kapatmak gerekir; RFC 8725/RFC 7519 uyumu test edilemez; imzalama hatası **sessiz** olur | Elle kripto uygulama **yazılmaz**; stub'ın kendi yorumu bile "proper JWT library" istiyor (`AuthenticationMiddleware.php:120`) |
| 3 | **HS256 (simetrik)** | Hızlı, anahtar yönetimi basit (tek secret) | Tüm servislere **aynı secret** dağıtılır → tek sızıntıda token sahteciliği site geneli; JWKS/ADR-058 hedefiyle çelişir; `kid` keyring sorunu anlamsızlaşır | Servisler-arası asimetrik zorunluluk (ADR-058 (b): "imzalama anahtarı yalnız auth'ta") — simetrik model bunu **fiziksel olarak** karşılayamaz |
| 4 | **MFA'yı herkese + SMS OTP olarak zorunlu kıl** | Devreye alma kolay; kullanıcı tanıdık; dönüşüm yüksek görünür | NIST'te SMS **restricted** (SIM-swap/SS7); e-posta/SMS OOB **yapısal olarak zayıf**; tüm kullanıcıları zorlamak kurulum/kayıp yükü + destek maliyeti üretir (Twilio); CoreMusic'in **SIM-swap yüzeyi yok**, ama TOTP secret kaybı **var** | NIST/OWASP düzeyinde **zayıf kanal** + erken zorunluluk; adım adım model §2.2-b'de daha düşük riskle aynı korumayı verir |
| 5 | **Hazır IdP + MFA (Auth0 / Keycloak)** | Yönetilen HA, hazır MFA/WebAuthn, literatürle birebir | 18 BCNF DB + `credential_keys`/`user_tokens` koda gömülü; veri egemenliği ADR-040/003 riski; büyük migrasyon | **Kapsam aşımı** — ADR-058 aynı itirazı reddetti (alt.5); CoreMusic **mevcut** `auth.coremusic.net`'i MFA'lı hâle getirir. Gelecekte ayrı ADR ile değerlendirilebilir |
| 6 | **Hiç MFA koyma (statüko)** | Sıfır maliyet; H019'daki "CoreMusic'te yok" durumu **sürer** | Credential-stuffing/parola sızıntısı **tek faktörle** karşılanır; AAL2 **ulaşılamaz**; hassas işlemler (API anahtarı, e-posta değişimi) parola tek başına korur | Vault'un **dört ayrı dosyası** (opencode kural 10, vault-template:606, brain:113, arşiv) MFA'yı **kararlaştırılmış** gösteriyor; bu ADR olmazsa iddia kanıtsız kalır (H019 halüsinasyonu) |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **ADR-052'nin tek açık kalemi kapanır:** stub artık *ne yapacağı* belli bir kararla donanımlı (kütüphane + algoritma + anahtar yeri + fail-closed davranış); "JWT var mı?" sorusunun cevabı artık **"karar verildi, uygulama PLANNED"** — halüsinasyon değil.
- **Algoritma zayıflığı sınıfı (alg:none / RS256→HS256 / kid confusion) tek maddeyle kapatılır** ve **regresyon testi** yazılır (§2.2-9).
- **MFA yüzeyi dürüstçe etiketlendi:** kod/şema/composer = 0 olduğu yazıldı; H019 (4 skill) ile çelişen hiçbir iddia üretilmedi.
- **Erişim kaybı senaryosu önceden çözüldü:** kurtarma kodu seti + 3 kademeli yol → MFA, kullanıcıyı **kilitlemez**; bypass kodu olmadığı için operatör kanalı da **saldırı yüzeyi değil**.
- **Vault-çapraz referans ağı kuruldu:** ADR-020/047/052/056/058/022/011/010/013/008/043/039/007/004 + index/CLAUDE/brain/WORKFLOW/şablon — 14 ADR + 5 vault dosyası.
- **Gelecek yol açık:** WebAuthn/passkey faz 2 hedefi olarak yazıldı; TOTP bugünün çözümü olarak **kaldırılmak zorunda değil**.

### 4.2 Olumsuz Sonuçlar

- **Her şey PLANNED:** paket 0, şema 0, kod 0 — bu ADR **kod üretmez**; uygulanana kadar "MFA var" denemez (H019 geçerliliğini korur).
- **`lcobucci/jwt` bakım itirazı kayda geçti** (GitHub `jwt-auth#228`): firebase kadar yaygın değil → sürüm/izleme yükü bu ekibe kalır.
- **`index.md` hâlâ 051–060 satırlarını içermiyor**; bu ADR dizinde **görünmez** (report-only, §5.1 adım 12'ye ertelendi).
- **`brain.md`'de ADR-059 slotu YOK** (grep = 0) → özet MO tarafından eklenmeyi bekliyor (**⚠️ VERIFICATION REQUIRED**).
- **Debate ✅ TAMAMLANDI** (3 tur / 20 persona, 18/2/0 KABUL — §7.1) — Tech Lead ✅, Arch Lead ⏳; `status: accepted` **kapsam kararını** ifade eder, **onay zinciri Arch Lead onayıyla tamamlanacaktır** (§7).
- **MFA = yeni kilitlenme riski:** secret kaybı + kurtarma kodu kaybı birlikte olursa **kademe 3 (insan süreç)** çalışır → gecikme + operasyonel yük.
- **`ADR-056`'daki `ADR-059-mfa` düz metin atfı artık yanlış slug** (gerçek: `ADR-059-jwt-library-and-mfa`) → §5.1 adım 11'de raporlanır, **o dosya düzenlenmez**.
- **Numara serisi riski:** 059 arşiv tarafından verilmiş; kural 4 ise ADR-088+ diyor → çakışma **raporlandı, düzeltilmedi** (§5.1 adım 13).

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **Algoritma çevikliği (alg:none / RS256→HS256 / `kid` keyring)** — sahte token kabulü | Orta (%40-70) | **Kritik** | Allowlist **sunucu yapılandırmasında** (`{RS256}`), tek algoritma↔anahtar eşleşmesi, `kid` tek-tipli keyring (CVE-2025-45769), **regresyon testi** (§2.2-9) |
| **Kurtarma kodu sızıntısı** (masaüstü/kopyala-panoya) → hesap devralma | Orta (%40-70) | **Kritik** | ≥64 bit + **hash'li** saklama + **tek kullanım** + set iptali + kod kullanıldığında **tüm oturumların iptali + bildirim**; e-posta/SMS ile **gönderim yok** |
| **TOTP replay / zaman penceresi genişletme** | Düşük (%10-40) | Yüksek | **Tek-kullanım** (RFC 6238 §5.2) · ±1 step pencere · `hash_equals` · rate limit + tavan · olay kaydı |
| **TOTP phishing'e dirençli değil** (AiTM ile kod yankılanır) | Orta (%40-70) | Yüksek | **Açıkça kabul edildi**: AAL3/`phishing-resistant` **iddia edilmiyor**; WebAuthn/passkey **faz 2 hedefi** (§2.2-8) · hassas işlemde **step-up + kısa tazeleme** (5 dk) |
| **TOTP secret kaybı** (SIM-swap yok, cihaz kaybı/temizlik var) → kullanıcı kilitlenir | Orta (%40-70) | Orta | 3 kademeli yol (§2.2-d) + kurtarma kodu seti + kademe 3 insan süreç; **e-posta tek kanal sayılmaz** |
| **`lcobucci/jwt` bakım/sürüm riski** (yaygın değil, sürüm desteği doğrulanamadı) | Orta (%40-70) | Orta | Alternatif 1 (`firebase/php-jwt`) **reddedilmedi, ertelendi** → tek yerden değiştirilebilir (yalnız JWT adapter'ı); sürüm `⚠️ VERIFICATION_REQUIRED` + §5.1 adım 6'da sabitleme |
| **MFA = erişim kilidi** (secret + kurtarma kodu kaybı) | Düşük (%10-40) | Yüksek | Kademe 3 insan süreç + ADR-058 **break-glass** (test edilmesi şart) + MFA'sız hesaba kurtarma kodu **üretilmez** |
| **Step-up'un bypass edilmesi** (BypassAuth açık kalırsa) | Orta (%40-70) | **Kritik** | MFA **da fail-closed**; `FORCE_AUTH_BYPASS` prod `false` (ADR-058 audit C1); step-up ucu **rate-limit muafiyetinden** çıkarılır |
| **Şema/paket olmadan "MFA var" denmesi** (halüsinasyon) | Yüksek (>70) | Orta | Her iddia **PLANNED/0 YÜZEY** etiketli; H019 skill'leri **değiştirilmez**; `brain.md` slotu MO işi |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **Dokümante et (bu ADR):** JWT kütüphanesi + RS256 kilidi · MFA akışları · kurtarma kodları · 3 kademeli recovery · hizalama. **Kod değiştirilmez.** | Vault Steward | 0.5 gün |
| 2 | **Tarama kaydı (yapıldı):** 5 composer.json → paket **0**; stub `AuthenticationMiddleware.php:117/130` **STUB**; MFA kod **0**; şema **0**; H019 ×4. | Vault Steward + Security | tamamlandı |
| 3 | **PLANNED — Kütüphane + algoritma kilidi:** `shared/composer.json` → `lcobucci/jwt` (+ `endroid/qr-code` auth'ta); `validateJwtToken()` gerçek gövde; **allowlist `['RS256']` sunucuda**; `iss/aud/exp/nbf/jti` zorunlu; **alg-confusion regresyon testi** | Security + Backend | 2 gün |
| 4 | **PLANNED — Anahtar yönetimi:** RS256 anahtar çifti üretimi; private **yalnız auth** (`.env`/vault — REDACTED), public **JWKS + `kid`** yayımı (ADR-058 §2.2-b ile aynı kapı) | Security + DevOps | 1.5 gün |
| 5 | **PLANNED — Şema (Data):** `mfa_settings` · `mfa_recovery_codes` · `mfa_events` migration'ı (şablon: migration-template); **bugün 0 kolon** olan 18 DB'ye eklenir | Data | 1 gün |
| 6 | **PLANNED — Sürüm sabitleme:** `lcobucci/jwt` sürüm aralığı composer.lock ile **disk kanıtıyla** sabitlenir; ADR-056'daki `ADR-059-mfa` slug tahmini düzeltilmez ama **raporlanır** (§4.2) | Backend | 0.5 gün |
| 7 | **PLANNED — TOTP akışı:** kurulum (QR + confirm code) · login doğrulama · kurtarma kodu üretimi (10 × ≥64 bit, hash'li, set iptali) | Security + Backend | 3 gün |
| 8 | **PLANNED — Step-up:** hassas işlem handler'larında `requireMfaFresh(5dk)` · admin rolünde zorunlu MFA · BypassAuth ile **çakışma testi** | Backend + Security | 2 gün |
| 9 | **PLANNED — Recovery testi:** secret kaybı → kurtarma kodu → oturum iptali → bildirim; **kurtarma kodu da yok** → kademe 3 insan süreç simülasyonu | QA | 1.5 gün |
| 10 | **PLANNED — Doğrulayıcı testleri:** ±1 pencere · 2. kullanım reddi · rate-limit tavanı · `hash_equals` · `exp`/`alg` 401 (§2.2-9) | QA | 1 gün |
| 11 | **Raporlanan — slug çelişkisi:** `ADR-056`'da "`ADR-059-mfa.md`" düz metin atfı ≠ gerçek slug `ADR-059-jwt-library-and-mfa` → **o dosya düzenlenmez**, yalnız rapor | Vault Steward | sonraki reset |
| 12 | **ERTELENEN — index:** `.ai/.decisions/index.md`'ye ADR-059 satırı **eklenmedi**; 051–060 boşluğu + ADR-088+ numara çelişkisi raporlandı (düzeltme **YAPILMAZ**) | Vault Steward | sonraki reset |
| 13 | **ERTELENEN — `brain.md` ADR-059 slotu** (grep = 0, MO işi) + `.ai/.decisions/index.md` kayıt satırı bir sonraki vault reset'ine ertelendi (report-only; In-Place Refactoring + SRP) | Vault Steward / MO | sonraki reset |
| 14 | **✅ Debate TAMAMLANDI (3 tur / 20 persona):** kayıt §7.1'e yazıldı, `debate` alanına işlendi; Tur 3 oyu **18 kabul / 2 çekimser / 0 red → KABUL**; **Tech Lead ✅** (§7) | Vault Steward + Tech Lead | tamamlandı |
| 15 | **Faz 2 (PLANNED) — WebAuthn/passkey pilotu:** NIST 800-63-4 / 2026 literatürü → opsiyonel ikinci faktör; TOTP **kaldırılmaz** | Security | sonraki çeyrek |
| 16 | **ŞART 1a (debate kabul koşulu):** `lcobucci/jwt` **sürüm kilidi** — sürüm aralığı composer.lock ile disk kanıtıyla sabitlenir (adım 6) + **algoritma allowlist `['RS256']` CI denetimi** — allowlist dışına sapma CI'da başarısız olur | Security + Backend | adım 3/6 ile |
| 17 | **ŞART 1b (debate kabul koşulu):** MFA **uygulama fazı** bağlayıcı — (i) şema: `mfa_settings` · `mfa_recovery_codes` · `mfa_events` (adım 5), (ii) kurulum akışı: QR + confirm code + kurtarma kodu seti (adım 7), (iii) **admin rolünde zorunlu MFA** (faz 3, adım 8) | Data + Security + Backend | adım 5/7/8 ile |
| 18 | **ŞART 2 (debate kabul koşulu):** ADR-056'daki `ADR-059-mfa` **çapraz referans düzeltmesi** → gerçek slug `ADR-059-jwt-library-and-mfa` (adım 11 ile aynı kayıt; **o dosya bu işlemde düzenlenmez**, düzeltme sonraki reset'te) | Vault Steward | sonraki reset |
| 19 | **ŞART 3 (debate kabul koşulu):** **TOTP pencere/replay testi** (±1 step kabul · ±2 step red · aynı kodun 2. kullanımı red) + **kurtarma kodu tek-kullanım testi** (kullanılan kod red + yeni set eski seti iptal eder) — adım 10'un **geçiş şartı** | QA | adım 10 ile |

### 5.2 Geri Dönüş Planı

Bu ADR **kod üretmez** (karar kaydıdır) → doğrudan geri dönüş riski yoktur. Uygulama adımları için:

1. **Adım 3 (kütüphane) geri dönüşü:** `validateJwtToken()` eski stub gövdesine döner (`return null`) → davranış **bugüne, yani 401'e** döner (stub zaten fail-closed idi) → **güvenlik düşmez**, yalnız Bearer yolu yeniden kapalı hâle gelir. `composer remove lcobucci/jwt` tek commit. **Alternatif 1'e geçiş** (`firebase/php-jwt`) geri dönüş değil **değişimdir**: JWT adapter'ı tek dosyada tutulduğu için allowlist/claim kuralları **aynı kalır**, yalnız encode/decode çağrısı değişir.
2. **Adım 4 (anahtar/JWKS) geri dönüşü:** JWKS yayımı durdurulur → servisler public anahtarı alamaz → **fail-closed** (401), sahte token kabulü **artmaz**; anahtar çifti silinmez (yedeğe alınır).
3. **Adım 5 (şema) geri dönüşü:** migration **ters** migration ile kaldırılır; `mfa_settings`/`mfa_recovery_codes`/`mfa_events` boşaltılır → MFA kurulumu devre dışı, mevcut oturumlar **etkilenmez** (MFA zorunlu değil, opsiyoneldi — adım 8'e kadar zorunlu hâle gelmediyse).
4. **Adım 7/8 (TOTP + step-up) geri dönüşü:** step-up bayrağı (`MFA_STEPUP_ENABLED=false`) → hassas işlemler parola+oturumla devam eder (bugünkü davranış); admin zorunluluğu bayrakla geri alınır. **Kurtarma kodları devre dışı bırakılmaz** (üretilmiş olanlar geçersiz kılınır ve kullanıcıya bildirilir — sessiz bırakılmaz).
5. **Adım 12/13 (index/brain) geri dönüşü:** hiç başlamadı → geri alınacak bir şey yok; eklenirse `git revert` tek commit.
6. **Adım 14 (debate) geri dönüşü:** debate olumsuz çıkarsa ADR `status: rejected` veya **yeni ADR** ile `superseded by` bağlanır (`[[../../.templates/adr/adr-template]]` §6.3) — **bu metin düzenlenmez**.
7. **Tam geri dönüş:** tüm adımlar tek commit serisinde; **ADR-011 cookie değeri, ADR-052 `exp ≤15 dk` ömür/rotasyon kararı ve ADR-058 fail-closed hiçbir adımda değiştirilmez** (rollback = o üç karara hiç dokunulmadığının kanıtı).
8. **`vault-utf8-writer` yedeği** (`<file>.bak`) bozulma halinde eski içeriği verir; `.ai/log.md`'ye tek satır revert append edilir.

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../../CLAUDE.md]] | Ana sözleşme (vault kuralları, UTF-8 protokolü, 16 Hard Guardrail) |
| [[../../raw/brain.md]] | Mimari kararlar — **ADR-059 slotu YOK (⚠️ VERIFICATION REQUIRED)**; `:109` `lcobucci/jwt`, `:113` `pragmarx/google2fa`, `:114` `endroid/qr-code` |
| [[../../raw/WORKFLOW.md]] | Süreçler |
| [[../index.md]] | ADR dizini — **051–060 satırları eksik, bu ADR satırı eklenmedi (report-only)** |
| [[../../.templates/adr/adr-template.md]] | Guardrail #16 zorunlu iskelet (§1.3 web araştırması + 19 doğrulama kapısı) |
| [[ADR-052-hybrid-auth-session-jwt]] | Hibrit session/JWT + `exp ≤15 dk` + reddetme listesi — **bu ADR o kararın kütüphane/algoritma ayağını yazar** |
| [[ADR-056-auth-module-implementation]] | RBAC (`user_roles`) + Permission middleware fail-closed — **step-up'ın rol kaynağı**; `ADR-059-mfa` slug tahmini §5.1 adım 11 |
| [[ADR-058-centralized-auth-service]] | Tek auth noktası + fail-closed + break-glass — **MFA'nın barınağı**, recovery kademe 3'ün kaynağı; "ADR-059 kapsam dışı" sınırı **korunmuştur** |
| [[ADR-020-api-public-security]] | API auth üçlüsü + Bearer kilidi — **MFA kapsamı DIŞI** (makine kimliği); hassas işlem listesinde API anahtarı üretimi var |
| [[ADR-047-login-redirect-session-bridge]] | İmzalı tek kullanımlık köprü token'ı — **MFA'yı atlatma kanalı yapılmaz** |
| [[ADR-022-database-hardened-security]] | AES-256-GCM + Argon2id — **TOTP secret + kurtarma kodu hash'i** saklama zemini |
| [[ADR-011-session-management]] | Oturum yaşam döngüsü + cookie — step-up **yeni cookie üretmez** |
| [[ADR-010-csrf-protection-strategy]] | CSRF üç katmanı — step-up/kurtarma POST'u |
| [[ADR-013-rate-limiting-apcu]] | Per-account rate limit — MFA + kurtarma kodu denemeleri kapsam içi, **muafiyet yok** |
| [[ADR-008-bypass-auth-middleware]] | Bypass = üretimde fail-closed — **"MFA bypass kodu yok"** kararıyla aynı yön |
| [[ADR-043-auth-subdomain-consolidation]] | Cookie domain tekliği + JWT anahtarının tek kaynak olması — MFA'nın subdomain'lerde nasıl göründüğü |
| [[ADR-039-7-service-platform-architecture]] | Servis↔servis doğrudan HTTP yasağı — JWKS yayım yolu bu sınırla uyumlu |
| [[ADR-007-cache-namespace]] | APCu namespace — step-up tazelik damgası (`mfa_verified_at`) TTL için |
| [[ADR-004-multi-domain-spa]] | Çoklu domain SPA — step-up ekranının `auth`'tan dönüş akışı |

**Kod kanıtı (düz metin, wiki-link değil):** `shared/src/Api/Middleware/AuthenticationMiddleware.php:60-83,72-76,93-109,111-131,117,119-121,130` · `shared/composer.json` · `api.coremusic.net/composer.json` · `auth.coremusic.net/composer.json` · `home.coremusic.net/composer.json` · `media.coremusic.net/composer.json` · `.ai/raw/brain.md:105-115` · `.ai/.templates/coremusic-vault-template.md:606` · `.ai/archives/prompt2-auth-2026-08-15.md:164,666` · `.ai/archives/prompt-unified-2026-08-15.md:592` · `.ai/archives/prompt1-spa-router-2026-09-01.md:1479,1482` · `.opencode/opencode.json:79` · `.opencode/skills/truth-engine/SKILL.md:166` · `.claude/skills/red-team-truth-mode/SKILL.md:279,570` · `.claude/skills/hallucination-control/SKILL.md:272` · `.ai/.sql/mysql/*.sql` (0 MFA kolonu) · `.ai/.decisions/index.md:91,92`

**Düz metin referanslar (diskte YOK — wiki-link KURULMAZ, `⚠️ VERIFICATION REQUIRED`):** ADR-051 · ADR-053 · ADR-054 · ADR-055 · ADR-057 · ADR-060 · `brain.md` ADR-059 slotu · `ADR-059-mfa` (ADR-056'daki eski tahmini slug)

### 6.1 Debate Şartları (çapraz referans — 3 şart / 4 madde)

| # | Şart (debate kabul koşulu) | İlgili doküman / madde | Durum |
|---|---------------------------|------------------------|-------|
| 1a | Kütüphane **sürüm kilidi** + **algoritma allowlist CI denetimi** | [[ADR-052-hybrid-auth-session-jwt]] · §2.2-2 · §5.1 adım 3/6/**16** | ⏳ PLANNED (uygulama) |
| 1b | MFA **uygulama fazı**: şema + kurulum akışı + admin zorunluluk | [[ADR-056-auth-module-implementation]] (rol kaynağı) · §2.2-3/8 · §5.1 adım 5/7/8/**17** | ⏳ PLANNED (uygulama) |
| 2 | ADR-056 **çapraz referans düzeltmesi** (`ADR-059-mfa` → `ADR-059-jwt-library-and-mfa`) | [[ADR-056-auth-module-implementation]] · §5.1 adım 11/**18** | ⏳ ERTELENEN (sonraki reset) |
| 3 | **TOTP pencere/replay** + **kurtarma kodu tek-kullanım** testi | §2.2-9 · §5.1 adım 10/**19** | ⏳ PLANNED (QA) |

> Bu dört satır **debate 3. turunda 18/2/0 KABUL'ün bağlayıcı koşullarıdır** (§7.1); yerine getirilmeden ADR **Frozen'a geçmez** (Arch Lead onayı da ayrıca beklenir, §7).

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | CoreMusic Vault Documentation Specialist | 2026-09-29 | ✅ |
| Tech Lead | CoreMusic Tech Lead | 2026-09-29 | ✅ |
| Arch Lead | — | — | ⏳ |

### 7.1 Debate Kaydı

| Alan | Değer |
|------|-------|
| Debate durumu | ✅ **TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** |
| Tur sayısı | 3 / 3 |
| Persona | 20 |
| Tur 1 — bulgu/oy | 20 persona, 6 sorgu / ~30 adlandırılmış kaynak. **Bulgu:** 5 `composer.json` `lcobucci\|firebase\|jwt\|google2fa\|totp\|otp` = **0 isabet** (ADR-052 0/0 teyidi) · `AuthenticationMiddleware.php:117` stub → `:130` `return null` + `:72-76` Bearer → **401 fail-closed** · google2fa kodda 0 (yalnız metin `brain.md:113`, `vault-template.md:606`, `opencode.json:79`) · MFA PHP **0 gerçek isabet** + `.ai/.sql/mysql` 0 kolon → **0 YÜZEY / PLANNED**. **Karar:** `lcobucci/jwt` + **RS256** + **JWKS/kid allowlist (token'dan okunmaz)** · TOTP **3 kademe** (login opsiyonel-kurulmuşsa-zorunlu, admin + step-up zorunlu, API/servis yok) · **10 kurtarma kodu** hash'li tek-kullanım · **bypass kodu YOK** · WebAuthn faz 2 · ~30 kaynak / 6 sorgu (RFC 8725, RFC 6238, NIST 800-63B/63-4, CVE-2025-45769) · konu çakışması birleştirildi (arşiv = JWT lib, template = MFA) · ADR-056'da `ADR-059-mfa` slug tahmini ≠ gerçek slug · index 051–060 satırı YOK (reset'e) · 051/053–055/057/060 atlanan boşluk notu. **Oy:** 16 kabul/neutral, **4 uyarı** (QA: TOTP testi · DevOps: lcobucci bakım · Critic: MFA 0 + slug · DB: şema PLANNED) |
| Tur 2 — itiraz/çözüm | **4 itiraz → 4 çözüm = 4 şart.** (1) Sürüm/bakım → kütüphane **sürüm kilidi** + algoritma allowlist **CI denetimi** → **Şart 1a** · (2) MFA 0 yüzey → uygulama fazı: **şema + kurulum akışı + admin zorunluluk** → **Şart 1b** · (3) Slug çelişkisi (`ADR-059-mfa`) → **ADR-056 çapraz referans düzeltmesi** → **Şart 2** · (4) Test yok → **TOTP pencere/replay + kurtarma kodu tek-kullanım testi** → **Şart 3** |
| Oy dağılımı (Tur 3) | **18 kabul / 2 çekimser / 0 red → KABUL** |
| Sonuç | ✅ **KABUL** — **3 şart** (1a-1b, 2, 3): §5.1 adım **16/17/18/19** + §6.1 tablosu |
| Tech Lead | ✅ (2026-09-29) |
| Arch Lead | ⏳ (ayrı onay — bu işlem kapsamında değil) |

> **Bu ADR debate'den GEÇMİŞTİR** (`debate: ✅ TAMAMLANDI` — 3 tur / 20 persona, 18/2/0 KABUL — §7.1; Tech Lead ✅). `status: accepted` **kapsam kararının** (§2 a-e) kabul edildiğini gösterir; **onay zincirinde Arch Lead onayı ⏳ beklenmektedir** ve **3 debate şartı** (§6.1 / §5.1 adım 16-19) yerine getirilmeden **Frozen'a geçiş YOK**.
> **Kaynaksız numara boşlukları (not):** ADR-051, ADR-053, ADR-054, ADR-055, ADR-057, ADR-060 diskte dosya olarak **yok** ve bu ADR tarafından **atlandı** (kaynak = `.ai/.decisions/accepted/` glob taraması, 2026-09-29). Bu numaralar bu ADR'nin **kapsamı dışındadır**; her biri kendi kanıtıyla doldurulmalıdır.
> **Index boşluğu (not):** `.ai/.decisions/index.md` **051–060 satırlarını içermez** (050 → 061 atlıyor, satır 91/92); bu ADR'nin dizin satırı **eklenmedi** — §5.1 adım 12'de bir sonraki reset'e ertelendi.
> **Konu çakışması (not):** arşiv prompt (JWT kütüphanesi) ↔ vault-template (MFA TOTP) — iki konu bu tek ADR'de birleştirildi (§1.1 Tablo C, disk kanıtlı).

---

*ADR-059 v1.0.0 — CoreMusic Architecture Decision Record*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-29*
*Mode: Red Team · Human Mode · Truth Mode*
