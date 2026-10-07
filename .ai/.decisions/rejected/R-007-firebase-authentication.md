---
title: "CoreMusic — R-007: Firebase Authentication (REDDEDİLMİŞ — Harici Bağımlılık)"
type: "architecture-decision"
category: "security"
date: "2026-10-06"
updated: "2026-10-06"
version: "1.0.0"
status: "rejected"
authority: "SSOT — R-007 red kararı: CoreMusic kimlik doğrulama yüzeyinde 'Firebase Authentication (harici SaaS kimlik servisi)' KABUL EDİLMEZ. Gerekçe: karar dizini index.md:133 'Firebase Auth | Harici bağımlılık' + kendi auth mimarimiz (ADR-008 bypass fail-closed, ADR-043 auth subdomain konsolidasyonu, ADR-052 hibrit session/JWT, ADR-058 centralized auth, ADR-059 JWT lib + MFA TOTP — hepsi diskte, status: accepted) + kod kanıtı (Firebase SDK composer/PHP/JS = 0). Yerini alan: ADR-008 + ADR-043 + ADR-052 + ADR-058 + ADR-059 (+ ADR-011 session zemini). Kapsam: red HARİCİ SaaS bağımlılığını reddeder; self-hosted açık kaynak IAM (Keycloak/Authelia) bu redin kapsamı DEĞİLDİR (disk'te reddeden ADR yok — §2.1/3, §3/6). Debate ✅ TAMAMLANDI (3 tur / 20 persona — 19/1/0 RED DOĞRULANDI, §7). Bu dosya salt-okunur seridir (rejected/) — değiştirilmez, yalnız referanslanır."
governance: "Red Team · Human Mode · Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 RED DOĞRULANDI)"
---

# CoreMusic — R-007: Firebase Authentication (Rejected)

> **Durum:** rejected (**debate ✅ TAMAMLANDI**) — **Tarih:** 2026-10-06 — **Debate:** ✅ **TAMAMLANDI (3 tur / 20 persona — 19/1/0 RED DOĞRULANDI)** — **Tech Lead:** ✅ — **Arch Lead:** ⏳
> **Seri:** `.ai/.decisions/rejected/` (salt-okunur) — **Slug:** `R-007-firebase-authentication` (dizin otoritesi: [[../index]] **satır 133** — dosya adı ile birebir hizalı ✅; 2026-10-06 glob doğrulaması: `rejected/` içinde `R-007*` = **0 dosyaydı** → bu işlemde yazıldı)
> **Dizin satırı:** `| [[R-007-firebase-authentication]] <!-- dead-link: R-007-firebase-authentication no source 2026-09-24 --> | Firebase Auth | Harici bağımlılık |` — `<!-- dead-link ... -->` bayrağı **bu işlemde DOKUNULMADI** (temizlik son sıfırlamaya ertelendi → §5.1/3 + §7.1/1)
> **İlgili kararlar:** [[../accepted/ADR-008-bypass-auth-middleware]] (yerini alan — bypass = üretimde fail-closed) · [[../accepted/ADR-043-auth-subdomain-consolidation]] (yerini alan — tek kimlik otoritesi auth.coremusic.net) · [[../accepted/ADR-052-hybrid-auth-session-jwt]] (yerini alan — hibrit session/JWT) · [[../accepted/ADR-058-centralized-auth-service]] (yerini alan — tek auth noktası + fail-closed + break-glass) · [[../accepted/ADR-059-jwt-library-and-mfa]] (yerini alan — lcobucci/jwt + MFA TOTP) · [[../accepted/ADR-011-session-management]] (session zemini) · karar dizini [[../index]] §5.
> **R-001…R-006 dersi uygulandı:** wiki-link slug'ları **tahmin edilmedi** — hedefler `rejected/` glob'u ve `accepted/` glob'u ile diskten doğrulandı.

---

## 1. Bağlam (Context)

CoreMusic'in kimlik doğrulama katmanı **kendi yığını** üzerine kuruludur: `auth.coremusic.net` tek kimlik otoritesi (ADR-043/058), hibrit session/JWT (ADR-052), JWT için `lcobucci/jwt` + MFA TOTP (ADR-059), bypass = üretimde fail-closed (ADR-008), session hijyeni (ADR-011). Karar dizini bu tercihi tescil etmiştir (`index.md:133` — "Firebase Auth | Harici bağımlılık") ama **red metni hiç yazılmamıştır**: elde yalnız dizin satırı + `<!-- dead-link ... -->` notu + kendi auth ADR'lerinin uzun gerekçe bağı + `brain.md:145`'te `firebase/php-jwt` yasağı (JWT **kütüphanesi** — ayrı konu, §2.1/4) vardır. Bu dosya, o satırın **gerekçeli red kaydıdır** — yeni bir karar değil, mevcut red'in (a) gerekçe, (b) güncel web araştırması, (c) yerini alan eşleme, (d) yeniden değerlendirme koşulu ile sıfırdan yazımıdır.

### 1.1 Mevcut Durum (disk kanıtı — dürüst etiket, 2026-10-06 taraması)

| # | İddia | Kanıt | Etiket |
|---|-------|-------|--------|
| 1 | Red kaydı var mı? | [[../index]] `:133` → `[[R-007-firebase-authentication]]` + "Firebase Auth" + "Harici bağımlılık" + `<!-- dead-link ... no source 2026-09-24 -->` | ✅ **KAYITLI** (dizin satırı tek kanıt; red metni bu işlemde yazılıyor) |
| 2 | Bu işlem öncesi dosya var mıydı? | `.ai/.decisions/rejected/` içinde `CLAUDE.md`, `index.md`, `R-001-*`…`R-006-*` ; `R-007*` = **0 dosya** | ❌ **YOKTU** → bu işlemde yazılıyor (klasör zaten var — `rejected/` oluşturulmadı) |
| 3 | `R-007` grep isabeti (repo geneli, literal) | `index.md:133` (slug + dead-link) · `R-006:227` ("Eski seri R-007…R-012" düz metni) · `R-005-rest-only-api.md:61` ("Redis adapter ADR-007'de PLANNED" — **yanlış eşleşme, ADR-007**) · ADR-007 ile ilgili WORKFLOW/agents/arşiv satırları (**ADR-007 = cache namespace, R-007 ile alakasız**) | ✅ **R-007-firebase isabeti = 2** (index:133 + R-006:227) — ikisi de **red'in kendi metni değil**; kodda `R-007` = **0** |
| 4 | Red gerekçesi başka yerde yazılı mı? | `index.md:133` ("Harici bağımlılık") · `brain.md:145` ("`firebase/php-jwt` | Yasaklı — RS256 için `lcobucci/jwt` kullanılır" — **JWT kütüphanesi, auth servisi değil**) · arşivler `prompt3-api-2026-09-01.md:1833`, `prompt1-spa-router-2026-09-01.md:1074/1687`, `prompt-unified-2026-08-15.md:592`, `prompt-shared-base.md:265/365` (hepsi `firebase/php-jwt → lcobucci/jwt` eşlemesi) · ADR-059 `:219` (firebase/php-jwt alternatifi ret edildi — vault hizası) | ✅ **6+ referans** — hepsi **JWT kütüphanesi** düzeyi; "Firebase Auth servisi" red gerekçesi **tek satır** (`index.md:133`); uzun gerekçe = auth ADR'lerinin bağı (satır 5-6) |
| 5 | Uzun gerekçe diskte mi? | [[../accepted/ADR-058-centralized-auth-service]] (tek auth noktası + fail-closed + break-glass; harici servis alternatifi tartışması) · [[../accepted/ADR-043-auth-subdomain-consolidation]] (tek kimlik otoritesi; OAuth2/PKCE federasyonu kendi elimizde) · [[../accepted/ADR-052-hybrid-auth-session-jwt]] (hibrit session/JWT; JWT stub → lcobucci) · [[../accepted/ADR-059-jwt-library-and-mfa]] (`lcobucci/jwt` + TOTP MFA; firebase/php-jwt alternatifi ret) · [[../accepted/ADR-029-listening-rooms-social]] `:235` (Firebase/Ably/Supabase Realtime **yönetilen servis reddi** — "kendi vault/stack ilkesi (SSOT + kendi servislerimiz) + veri egemenliği") | ✅ **DISKTE** (bağlayıcı gerekçe — "kendi auth'ımız var + harici bağımlılık riski" zinciri 5 ADR'de yazılı) |
| 6 | Yerini alan kararlar diskte? | `accepted/` glob: `ADR-008-bypass-auth-middleware.md` · `ADR-043-auth-subdomain-consolidation.md` · `ADR-052-hybrid-auth-session-jwt.md` · `ADR-058-centralized-auth-service.md` · `ADR-059-jwt-library-and-mfa.md` — hepsi `status: accepted` (2026-10-06 glob = True ×5) · `ADR-011-session-management.md` (accepted, session zemini) | ✅ **5/5 + 1 zemin IMPLEMENTED** (glob ile doğrulandı — R-001 dersi) |
| 7 | Kod yüzeyi: Firebase bağımlılığı? | `composer*.json` ×5 (api/auth/home/media/shared) içinde `firebase\|kreait\|google/auth\|firebase-php` = **0** · PHP üretim `Firebase\|kreait\|firebase-php` = **0** · JS `Firebase\|firebase` = **0** | ✅ **FIREBASE YÜZEYİ 0** — Firebase Auth SDK hiç kurulmamış; red "girişi engelleme" kararıdır (R-006 deseni) |
| 8 | Kod yüzeyi: kendi auth sınıfı? | `shared/src/Middleware/AuthMiddleware.php` (`final class AuthMiddleware implements IMiddleware`) · `shared/src/Api/Auth/ApiSessionManager.php` (`final class ApiSessionManager implements ISessionManager`) · `shared/src/PageRouter/AuthGuard.php` · `shared/src/Middleware/SessionManagerMiddleware.php` · `auth.coremusic.net/include/Service/SessionManager.php` (`final class SessionManager implements ISessionManager`) · `home.coremusic.net/include/Auth/HomeAuthBridge.php` (`final class HomeAuthBridge`) · testler: `SessionManagerMiddlewareTest.php`, `AuthGuardTest.php` | ✅ **7 kod sınıfı + 2 test** — kendi auth yüzeyi diskte; ADR-052/058 bulguları tekrar doğrulandı (ApiSessionManager IMPLEMENTED, AuthGuard/AuthMiddleware mevcut) |
| 9 | JWT kütüphanesi composer'da? | `lcobucci\|firebase\|jwt\|pragmarx\|google2fa\|totp\|otp` = **0 isabet** (ADR-059 `:40` taraması; 2026-10-06 tekrar: composer `firebase\|kreait\|google/auth` = 0) | ✅ **0 YÜZEY** — JWT lib PLANNED (ADR-059); bu, "kendi yığın" kararını **zayıflatmaz**, auth servisinin de kendi elimizde olduğunu gösterir |
| 10 | `rejected/index.md` durumu? | Dosya **VAR** (v1.0.1, `total: 12`) ama § tablosu **BOŞ** (başlık satırları var, 12 red'in hiçbiri satırlanmamış) | ⚠️ **BOŞ** → bu işlemde **dokunulmadı** → §7.1/2 |
| 11 | Debate sonucu? | Debate **çalıştırıldı** — 3 tur / 20 persona (2026-10-06): **19/1/0 RED DOĞRULANDI** + 4 bağlayıcı şart (§5.4) | ✅ **TAMAMLANDI** → §5.3 + §5.4 + §7 |
| 12 | Araştırma protokolü diskte? | `.claude/skills/prompt-maker/references/10-web-research-protocol.md` → `Test-Path` = **True** | ✅ OKUNDU (§1.3 bu protokolle üretildi) |
| 13 | Şablon yolu? | Görev `.ai/.templates/adr/adr-template.md` der; disk kanıtı `.ai/.templates/adr/adr-template.md` (`glob` = True, v2.0.2) | ✅ **DÜZELTİLDİ** — gerçek yol `.ai/.templates/adr/adr-template.md` (nokta öneki) — §7.1/8 |

> **Ders notu:** bu red **hiçbir zaman kodda denenmedi** — üretim kodunda Firebase Auth **0** (§1.1/7); "reddedildi" = "Firebase Auth **hiç kurulmadı** ve kimlik doğrulama ADR-043/052/058/059 ile **kendi yığınımıza** bağlandı". Gelecekte biri "Firebase Auth kullansak mı?" derse yanıtı bu dosya + 5 auth ADR verir; "zaten denedik mi?" sorusunun yanıtı **hayır, hiç denenmedi** (§5.2). ⚠️ AYRI KONU: `firebase/php-jwt` (JWT **kütüphanesi**) ADR-059'da **ayrıca** ret edilmiştir — bu red'in hedefi **auth servisi**, kütüphane değil (§2.1/4).

### 1.2 Sorun Tanımı

1. **Red kararı kanıtsız duruyor.** `index.md:133` bir sonuç cümlesi ("Harici bağımlılık") ama **ne 2025-26 ekosistem kanıtı (Firebase Auth fiyat modeli değişikliği, vendor lock-in, Auth0/Supabase/Clerk/self-hosted alternatifleri) ne yeniden değerlendirme koşulu ne yerini alan eşleme** yazılı — gelecekteki biri "Firebase neden yok, bugün de mi yok, self-hosted auth alternatif mi, ne zaman tekrar sorulur?" sorusuna vault'tan cevap bulamıyor.
2. **Gerekçe ailesi parçalı ve yanlış konuya kaymış.** Uzun gerekçe auth ADR'lerinde (ADR-058 tek nokta, ADR-043 konsolidasyon, ADR-052/059 hibrit+JWT) ama **"Firebase Auth servisi" red'i hiçbir ADR'de tek satır olarak yok**; vault'taki tek `firebase` gerekçesi `brain.md:145`'teki **JWT kütüphanesi** yasağı — bu, red'in **hedefini daraltıyor** (auth servisi ≠ JWT lib) ve "harici bağımlılık" gerekçesini **açıklamıyor**.
3. **Kapsam belirsizliği.** "Harici bağımlılık" gerekçesi Firebase Auth'u (harici SaaS) mı reddeder, yoksa **self-hosted açık kaynak IAM'ı** (Keycloak, Authelia, Authentik) de mi reddeder? Dizin satırı tek başına "Harici bağımlılık" der — ama slug `firebase-authentication`'ı adlandırır. R-006 dersi (query builder kapsam dışıydı): kapsam dürüstçe daraltılmalı ve disk kanıtıyla sabitlenmeli (§2.1/3).
4. **Koşul tanımsız.** "Hangi durumda Firebase Auth kapısı yeniden açılır?" (ölçülmüş karşı-kanıt: kendi auth'ın kanıtlanmış başarısızlığı, ekip yetkinliği, fiyat avantajı) **hiç belgelenmedi** → yeniden değerlendirme tetikleyicisi tanımsız.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (öncelik: resmî doküman → vendor → bağımsız blog; her iddiaya kaynak). Araştırma 2026-10-06'da yapıldı — **3 sorgu / ~24 adlandırılmış kaynak**.

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "Firebase Authentication pricing 2025 2026 changes phone auth MAU costs" → (2) "vendor lock-in SaaS authentication Auth0 Supabase Clerk Firebase comparison 2025" → (3) "self-hosted authentication Keycloak Authelia vs managed SaaS 2025 pros cons" |
| Web Search **Konusu** | **(1)** Firebase Auth fiyat modeli 2025-26 (Spark 50K MAU ücretsiz; Blaze kademeli $0.0025-0.0055/MAU; phone auth SMS $0.01-0.06/doğrulama + 10K ücretsiz; SAML/OIDC ücretleri) · **(2)** SaaS kimlik sağlayıcı karşılaştırması + **vendor lock-in** gerçeği (Auth0/Supabase/Clerk/Firebase) · **(3)** **self-hosted açık kaynak IAM** (Keycloak/Authelia/Authentik) vs yönetilen SaaS — maliyet/bakım/olgunluk |
| Web Search **Bağlam** | CoreMusic: kendi auth yığını diskte (ADR-008/043/052/058/059 + 7 kod sınıfı, §1.1/5-8) · Firebase SDK composer/PHP/JS **0** (§1.1/7) · vault ilkesi: kendi yığın + veri egemenliği (ADR-029 `:235` yönetilen servis reddi) · hedef soru — *"Firebase Auth red'i bugün hâlâ doğru mu; fiyat modeli/lock-in/self-hosted alternatifleri red'i çürütüyor mu; 'harici bağımlılık' self-hosted'ı da kapsıyor mu?"* |
| Web Search **Kısa Açıklama** | **(1)** Firebase Auth fiyat modeli **cömert ama daralan**: Spark 50K MAU ücretsiz (metacto, toolradar, pricingapis); Blaze kademeli $0.0025-0.0055/MAU (metacto, logto, rapidnative); phone auth SMS $0.01-0.06/doğrulama (toolradar, pricingapis) + 10K ücretsiz SMS (pricingapis); SAML/OIDC ücretleri raporlanıyor (metacto) — fiyat sürprizi **gizli maliyetler** (SMS, SAML/OIDC) tarafında. **(2)** Vendor lock-in **gerçek uzun vadeli trade-off** (startupik: "vendor lock-in and ecosystem dependence can become real long-term trade-offs"); Supabase Auth "en çok kontrol", Auth0 "enterprise", Clerk "DX", Firebase "ölçeklenebilir ama ekosistem bağımlılığı" (krapton, designrevision, veldsystems, dev.to/mukesh_13, webdev.cloud, creativebrain). **(3)** Self-hosted: Keycloak "güçlü/kurumsal/olgun", Authelia "hafif reverse-proxy ilkeli", Authentik "esnek", Zitadel "bulut-öncelikli"; asıl maliyet **altyapı + bakım** (skycloak: "your real costs are infrastructure and maintenance"; cerbos, supertokens, prohomelab, stackharbor, reddit, osohq, phasetwo, zluri) |
| Web Search **Uzun Açıklama** | **(1)** Firebase Auth 2026'da **50K MAU'ya kadar ücretsiz** (Spark) ve Blaze'da MAU başına kademeli ücret ($0.0025-0.0055) ile **ölçeklenebilir** fiyatlandırıyor; ama **phone auth** (SMS) $0.01-0.06/doğrulama + 10K ücretsiz SMS sınırı ve **SAML/OIDC** ücretleri **gizli maliyet** sınıfında (metacto "7 strategies to cut costs 50%", toolradar "hidden gotchas"). Yani "ucuz başlangıç" argümanı **doğru** ama "ölçülebilir avantaj" argümanı **ölçülmemiş** — CoreMusic'in MAU profili bilinmiyor (UNKNOWN). **(2)** Vendor lock-in literatürü tek ses: yönetilen kimlik servisi **veri + kimlik + ekosistem** üçlüsünü sağlayıcıya bağlar; migration maliyeti (user export, custom claim eşlemesi, federasyon portability) **gerçek** ve 2025-26'da **ağırlaşıyor** (supabase-auth-vs-firebase, krapton, veldsystems); Auth0/Okta fiyat değişiklikleri 2023-24'te **kanıtlanmış sürpriz** sınıfıdır (dev.to/mukesh_13 "where each one actually breaks"). **(3)** Self-hosted IAM (Keycloak/Authelia/Authentik) **ücretsiz lisans** + **kendi altyapısı** = "gerçek maliyet altyapı ve bakım" (skycloak); Keycloak kurumsal olgunluk (SSO, MFA, federasyon) sunar ama **Java stack + ops yükü** getirir; Authelia hafiftir ama **forward-auth** odaklıdır (OIDC beta, stackharbor). Self-hosted, "harici bağımlılık" gerekçesini **zayıflatmaz** — bağımlılık **farklı noktaya** taşınır (sağlayıcı → kendi ops ekibi); CoreMusic'in kendi yığın ilkesi (ADR-029 `:235`) ile **çatışmaz**, tamamlar. |
| Web Search **Paragraf Veri Uzun** | **(1)** metacto.com "Firebase Auth Pricing 2026" (2026-05) · blog.logto.io "Firebase Authentication's pricing 2026" · rapidnative.com "Firebase Authentication Pricing 2026" · firebase.google.com/pricing (resmî) · toolradar.com "Firebase Auth Pricing" (hidden gotchas) · pricingapis.com "Firebase Auth Pricing" (2026-03-04) · frontdeskreview.com "Firebase Auth Pricing 2026" — **7**. **(2)** krapton.com "Auth0 vs Clerk vs Supabase Auth" · designrevision.com "Clerk vs Auth0 vs Supabase: Pricing & DX 2026" · veldsystems.com "Clerk vs Auth0 vs Supabase Auth" · dev.to/mukesh_13 "Auth0 vs Clerk vs Supabase vs Firebase: what breaks" · startupik.com "Supabase Auth vs Firebase Auth vs Clerk" (vendor lock-in) · webdev.cloud "Clerk vs Auth0 vs Firebase vs Supabase" · creativebrain.ca "Clerk vs Auth0 vs Supabase" — **7**. **(3)** cerbos.dev "Best Open Source Auth Tools 2026" · reddit r/selfhosted "Keycloak vs Authentik vs Authelia" · supertokens.com "Authelia vs Keycloak" · osohq.com "Keycloak Alternatives 2025" · skycloak.io "Keycloak vs Authelia" · prohomelab.com "Authentik vs Authelia vs Keycloak vs Zitadel" · stackharbor.com "Self-Hosted SSO Keycloak/Authelia" · phasetwo.io "Keycloak vs Auth0 2026" · zluri.com "Self-Hosted IAM Tools" · youtube "Self-Hosted Identity Manager 2025" — **10**. **Toplam ~24 benzersiz adlandırılmış kaynak, 3 sorgu**; çapraz doğrulama ≥2 kaynak ana iddialarda karşılanır. |
| Web Search **Sonucu** | **(1) Red destekleniyor — fiyat modeli netleşti:** Firebase Auth 50K MAU ücretsiz + Blaze kademeli, ama **gizli maliyetler** (SMS $0.01-0.06, SAML/OIDC ücretleri) gerçek → "ucuz başlangıç" argümanı **doğru** ama CoreMusic MAU profili UNKNOWN → red **"ölçülmüş avantaj yok"** diyerek savunulur (kaynak 7). **(2) Red destekleniyor — lock-in kanıtlandı:** vendor lock-in "gerçek uzun vadeli trade-off" + Auth0/Okta fiyat sürprizleri kanıtlanmış → harici SaaS = **veri/ekosistem bağımlılığı** (kaynak 7). **(3) Dürüst gerilim — self-hosted alternatif:** Keycloak/Authelia/Authentik **ücretsiz** ve olgun → "harici bağımlılık" gerekçesi **self-hosted'a uymuyor** (bağımlılık farklı noktaya taşınıyor) → red **kapsamı daralttı**: red **Firebase Auth (harici SaaS)**'ı reddeder; self-hosted açık kaynak IAM bu redin **kapsamı dışındadır** (kaynak 10) — ama kendi yığın ilkesi (ADR-029 `:235`) self-hosted'ı da **ayrı debate + yeni ADR**'ye tabi kılar. **İtiraz/karşıt bulgu (dürüst):** (i) Firebase Auth **50K MAU ücretsiz + olgun** → "popüler/değil" veya "pahalı" argümanı **kullanılamaz**; red gerekçesi **hız/maliyet değil, mimari** olmalıdır (kendi auth'ımız var); (ii) Auth0/Supabase/Clerk **daha esnek** fiyat/özellik sunabilir → red **hepsi harici SaaS** sınıfını reddeder, tek provider'a değil; (iii) sayfa-içi derin tur **yapılmadı** → fiyatlar başlık/özet düzeyindedir (§5.1/6). |
| Web Search **Alınan Karar** | **R-007 RED (Firebase Authentication) YÜRÜRLÜKTE KALIR.** (a) **CoreMusic kimlik doğrulama yüzeyinde "Firebase Authentication (harici SaaS kimlik servisi)" KABUL EDİLMEZ** — kimlik doğrulama ADR-043/052/058/059/011/008 üzerinden **kendi yığınımızda** yürür; bunu `index.md:133` + ADR-058 (tek auth noktası) + ADR-029 `:235` (kendi yığın ilkesi) kilitler. (b) **Red gerekçesi "pahalı/değil" değil mimaridir:** (i) kendi auth'ımız zaten var ve uygulanmış (5 ADR + 7 kod sınıfı — §1.1/5-8); (ii) harici SaaS = **vendor lock-in + veri/ekosistem bağımlılığı + fiyat sürprizi riski** (§1.3-1/2); (iii) **veri egemenliği** (ADR-029 `:235` — kimlik verisi sağlayıcının elinden çıkmaz). (c) **Self-hosted açık kaynak IAM (Keycloak/Authelia/Authentik) bu redin KAPSAM DIŞIDIR** (§1.3-3): red yalnız **harici SaaS**'ı reddeder; self-hosted talebi **ayrı debate + yeni ADR** ister ve bugün ADR-058/043 ile çakışır (kendi auth zaten var → self-hosted "alternatif" değil, **ek bağımlılık** olur). (d) **`firebase/php-jwt` (JWT kütüphanesi) bu redin KAPSAM DIŞIDIR** — o ADR-059'da ayrıca ret edildi (vault hizası `brain.md:145`); bu red'in hedefi **auth servisidir**, kütüphane değil. (e) **Yeniden değerlendirme koşulu** (§2.3) yazılmadan bu red **otomatik olarak güncellenmez**; "Firebase ücretsiz" veya "Keycloak olgun" argümanları tek başına koşul sayılmaz. |
| Web Search **Sonuç** | **3/3 araştırmada red desteklendi** (fiyat modeli/gizli maliyet + vendor lock-in kanıtı + self-hosted kapsam daraltması); **iki dürüst gerilim yazıldı**: (1) Firebase Auth **50K MAU ücretsiz + olgun** → red gerekçesi hız/maliyet değil **mimari** olmalı (kendi auth'ımız var); (2) self-hosted açık kaynak **ücretsiz** → "harici bağımlılık" gerekçesi self-hosted'a uymuyor → kapsam dürüstçe **yalnız harici SaaS** olarak daraltıldı, self-hosted ayrı ADR'ye bırakıldı. **Üç açık işaretlendi:** (i) sayfa-içi derin tur yok (§5.1/6) · (ii) CoreMusic MAU profili **UNKNOWN** → fiyat avantajı **ölçülmedi** (§1.1/7) · (iii) SAML/OIDC ücret iddiası tek kaynak (metacto) → işaretli. **Kaynak sayısı: 3 sorgu; §1.3'te adı geçen benzersiz kaynak ~24.** |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-058 accepted (tek auth noktası) | H1 "Tek Auth Noktası + Fail-Closed + Break-Glass"; `:125` karar (a) "kimlik üretimi + login/logout + oturum **yalnız `auth.coremusic.net`**" — Firebase Auth bu hükmün **doğrudan ihlali** olurdu; **bu red'in bağlayıcı dayanağı** |
| ADR-043 accepted (auth konsolidasyonu) | H1 "Auth Subdomain Consolidation"; `:9` "oturum/cookie domaini tek auth.coremusic.net, diğer alt alanlar yalnızca token doğrular" — harici SaaS auth bu **tek otoriteyi** parçalardı |
| ADR-052 accepted (hibrit session/JWT) | `:9` "session = tarayıcı/sunucu-state'i, API/mobil = JWT"; `:50` "composer'da JWT lib **0** → PLANNED"; kendi JWT yolumuz ADR-059 ile `lcobucci/jwt`'ye bağlandı — Firebase Auth bu **hibrit modeli** atlar |
| ADR-059 accepted (JWT lib + MFA) | `:128` "`lcobucci/jwt` (vault beyanı brain.md:109)"; `:219` alternatif 1 `firebase/php-jwt` **ret** ("vault zaten lcobucci/jwt diyor"); `:19` "MFA = lcobucci + TOTP, kendi elimizde" — Firebase MFA **alternatif değil**, kendi MFA kararımızın (TOTP + kurtarma kodları) **üstüne** bağımlılık getirirdi |
| ADR-008 accepted (bypass fail-closed) | H1 "3 Mod Bypass + Default-Deny Public Route + Üretimde Fail-Closed"; `:19` "bypass = üretimde fail-closed" — Firebase Auth'un kendi bypass/fallback modelleri bu ilkeyle **çatışırdı** (harici servis kesintisinde fail-open riski) |
| ADR-011 accepted (session hijyeni) | H1 "OWASP Seti + Hibrit Saklama + Tam Hijyen"; `:19` cookie `domain=.coremusic.net` + session regen — Firebase cookie'leri bu **hijyen setini** atlar |
| ADR-029 `:235` (kendi yığın ilkesi) | "Firebase / Ably / Supabase Realtime" yönetilen servis reddi — **aynı ilke**: "kendi vault/stack ilkesi (SSOT + kendi servislerimiz) + veri egemenliği → reddedildi; yalnız karşılaştırma/benchmark referansı olarak kullanıldı" — bu red'in **ilke dayanağı** |
| Kod yüzeyi gerçeği | Üretimde Firebase Auth **0** (§1.1/7), kendi auth **7 sınıf + 2 test** (§1.1/8) → red bir "kaldırma" değil, **girişi engelleme** kararıdır; geri dönüş planı kod tarafında işlem gerektirmez (§5.2) |
| Araştırma protokolü | §1.3 `10-web-research-protocol.md` ile üretildi; **"Firebase 50K ücretsiz + olgun"** (ters kanıt) dürüstçe yazıldı ama red'i değiştirmedi (§1.3-3) |

---

## 2. Karar (Decision)

**R-007 REDDEDİLMİŞTİR: CoreMusic kimlik doğrulama yüzeyinde "Firebase Authentication (harici SaaS kimlik servisi: email/password, social, phone, MFA, federasyon)" KABUL EDİLMEZ.** Karar `index.md:133`'ta bugünden vardı; bu dosya onu gerekçelendirir: red, ADR-058'in "tek auth noktası" hükmünün (`:125` karar a) + ADR-043'ün "tek kimlik otoritesi" kuralının (`:9`) + ADR-052'nin "hibrit session/JWT" modelinin + ADR-059'un "lcobucci/jwt + TOTP MFA" seçiminin + ADR-029 `:235`'teki "kendi yığın ilkesi"nin **red-kayıt ayağıdır** ve 2026-10-06 web araştırması (§1.3 — 3 sorgu, ~24 kaynak) red'in **bugün hâlâ doğru olduğunu** doğrulamıştır — "Firebase Auth pahalı/değil" olduğu için değil, **kendi auth'ımız zaten var + harici SaaS = vendor lock-in/veri egemenliği/fiyat sürprizi riski** olduğu için.

### 2.1 Neden Bu Seçenek?

1. **İlke tutarlılığı (kanıtlı):** ADR-058 `:125` (tek auth noktası), ADR-043 `:9` (tek kimlik otoritesi), ADR-052 `:9` (hibrit session/JWT), ADR-059 `:128/:219` (lcobucci + MFA kendi elimizde), ADR-029 `:235` (kendi yığın ilkesi — Firebase/Ably/Supabase yönetilen servis reddi) aynı hükmü farklı katmanlarda tekrarlar; `index.md:133` gerekçesi "Harici bağımlılık" — ayrı bir karar değil, **aynı ilkenin red-kayıt ayağı**.
2. **Kod kanıtı red'i destekliyor (ölçüldü):** composer ×5 → `firebase|kreait|google/auth|firebase-php` = **0**; üretim PHP `Firebase|kreait` = **0**; üretim JS `Firebase|firebase` = **0**; kendi auth yüzeyi **7 sınıf + 2 test** diskte (AuthMiddleware, ApiSessionManager, AuthGuard, SessionManagerMiddleware, SessionManager (auth.coremusic.net), HomeAuthBridge) → ADR-043/052/058 **uygulanmış durumda**; JWT lib composer'da 0 (ADR-059 PLANNED — kendi yığın kararını zayıflatmaz). Red bugün "hiç kurulmamış" bir katmanı engeller.
3. **Kapsam dürüstçe daraltıldı (araştırma + R-006 dersi ile):** literatür **self-hosted açık kaynak IAM'ı** (Keycloak/Authelia/Authentik) **ücretsiz + olgun** buluyor (§1.3-3) → red **bunu inkâr etmez**: red **yalnız harici SaaS kimlik servisini** (Firebase Auth, Auth0, Supabase Auth, Clerk) reddeder; **self-hosted açık kaynak IAM bu redin KAPSAM DIŞIDIR** — disk'te Keycloak/Authelia'yı reddeden ADR **yok**; self-hosted talebi **ayrı debate + yeni ADR** ister ve bugün ADR-058/043 ile çakışır (kendi auth zaten var → self-hosted "alternatif" değil, **ek bağımlılık** olur). ADR-029 `:235`'teki kendi yığın ilkesi **self-hosted'ı da** onaya tabi kılar ama bu dosya onu **adıyla reddetmez** (dizin satırı yalnız Firebase'i adlandırır).
4. **"Hedef yanlış konuya kaymış" uyarısı (dürüst):** vault'taki tek `firebase` gerekçesi `brain.md:145`'teki **JWT kütüphanesi** yasağıdır (`firebase/php-jwt → lcobucci/jwt`); bu ADR-059'un konusudur, **auth servisi red'i değildir**. Bu dosya bu ikisini **ayırmaktadır**: (a) `firebase/php-jwt` (kütüphane) → ADR-059 kapsamı; (b) Firebase Auth (servis) → bu red. "Firebase Auth red'i" ile "firebase/php-jwt red'i" **aynı karar değildir** — §7.1/5'te raporlandı.
5. **Maliyet gerekçesi ölçülmedi (dürüst):** Firebase Auth 50K MAU ücretsiz + Blaze kademeli ($0.0025-0.0055/MAU) + phone auth SMS $0.01-0.06/doğrulama (§1.3-1) → "ucuz başlangıç" argümanı **doğru**; ama CoreMusic MAU profili **UNKNOWN** → fiyat avantajı **ölçülmedi** → red **"ölçülmüş avantaj yok + mimari çatışma var"** diyerek savunulur; §2.3/2'deki ölçüm kapısı bu iddiayı gelecekte sınar.

### 2.2 Teknik Detaylar

- **Reddedilen yüzey (Firebase Auth):** `Firebase\Authentication` SDK (composer `kreait/firebase-php` veya JS `firebase/auth`) · Firebase kimlik üretimi + kullanıcı deposu (Firebase Firestore/Realtime DB veya Firebase Auth backend) · Firebase social login (Google/Apple/facebook sign-in) + phone auth (SMS OTP) · Firebase MFA (Google Prompts / phone second factor) · Firebase federasyon (SAML/OIDC via Firebase) · Firebase token doğrulama (`verifyIdToken`) yerine kendi session/JWT · Firebase Auth UI widget'ları / Firebase Hosting auth entegrasyonu · "Firebase ile başla, sonra migrate et" stratejisi (vendor lock-in kapısı — §1.3-2).
- **İzinli yüzey (yerini alan uygulama):** (a) **tek kimlik otoritesi** `auth.coremusic.net` (ADR-043/058) — kimlik üretimi + login/logout + oturum **yalnız orada**; diğer alt alanlar yalnızca doğrular; (b) **hibrit session/JWT** (ADR-052) — tarayıcı/sunucu-state'i session, API/mobil/servisler-arası JWT; (c) **JWT = `lcobucci/jwt`** (ADR-059, PLANNED — composer bugün 0) + RS256 allowlist + JWKS/kid; (d) **MFA = TOTP + 10 kurtarma kodu** (ADR-059, kendi elimizde; bypass kodu YOK); (e) **session hijyeni** (ADR-011) — cookie `domain=.coremusic.net`, 30 dk'da bir `session_regenerate_id(true)` (`SessionLifecycle.php:54-62`); (f) **bypass = üretimde fail-closed** (ADR-008); (g) **API auth üçlüsü** (ADR-020): API key / JWT / OAuth2 PKCE — OAuth2/PKCE federasyonu kendi elimizde (ADR-043); (h) **rate limit** (ADR-013) auth uçlarına uygulanır.
- **Kod yüzeyi ölçümü (2026-10-06):** composer 5/5 paketinde Firebase **0** · üretim PHP `Firebase|kreait|firebase-php` **0** · üretim JS `Firebase|firebase` **0** · kendi auth sınıfları **7** (AuthMiddleware · ApiSessionManager · AuthGuard · SessionManagerMiddleware · SessionManager (auth.coremusic.net) · HomeAuthBridge + testler SessionManagerMiddlewareTest/AuthGuardTest) → **Firebase 0 / kendi auth 7** katman resmi; ADR-052/058 bulguları tekrar doğrulandı (ApiSessionManager IMPLEMENTED — ADR-052 `:10`; AuthGuard/AuthMiddleware diskte).
- **Firebase kesintisi riski (red'in uygulama karşılığı):** harici SaaS auth = sağlayıcı kesintisinde login/refresh **düşer**; kendi auth'da bu risk **ops ekibinin** sorumluluğundadır (ADR-058 `:175` L2 APCu TTL + `:204` circuit breaker + break-glass — "fail-closed + son-şans okuma + acil yol"); Firebase'de bu katmanlar **sağlayıcıya devredilirdi** → "kendi elimizde" ilkesi burada da geçerlidir.

### 2.3 Yeniden Değerlendirme Koşulu (şart satırı)

> **Bu red yalnız aşağıdaki koşullardan BİRİ yazılırsa yeniden değerlendirilir; aksi hâlde yürürlükte kalır:** (1) **ADR-058 (ve/veya ADR-043/052/059) yeni bir ADR ile değiştirilirse** (mevcut ADR metinleri düzenlenmez — yeni ADR `superseded by` ile bağlar; debate ile) — kapı yalnız "izole bileşen/adayı, çekirdek auth/catalog/sosyal şeması DEĞİL" kapsamıyla açılır; (2) **ölçülmüş karşı-kanıt** Security Engineer + Backend Architect raporuyla belgelenirse — kendi auth'ın **üretimde kanıtlanmış başarısızlığı** (güvenlik olayı, süreklilik kesintisi, bakımın taşınamazlığı) **sayılarla** yazılmalı (metrik: auth kesintisi süresi, kimlik olay sayısı, bakım işçiliği/sprint, MFA bypass olayı) + Firebase Auth'un bu durumda **ölçülebilir avantajı** (fiyat, MAU profili, kesinti toleransı) gösterilmeli — ölçüm yoksa "Firebase gerekli" iddiası kurulamaz; (3) **vault kuralı "yeni harici bağımlılık" işletilirse** (`coremusic-vault-template.md:267/:644` — "Onaysız YOK: … yeni harici bağımlılık") — kullanıcı onayı + etki analizi + debate ile; (4) **debate tamamlanıp red'i kuran koşullar değişirse** (§5.3) yeni debate + yeni ADR ile yeniden açılır. **Bugün: 1 = SAĞLANMADI (ADR-058/043/052/059 diskte, yürürlükte), 2 = ÖLÇÜLMEDİ (karşı-kanıt 0 — §1.1/7-9), 3 = SAĞLANMADI (onay yok), 4 = debate ✅ TAMAMLANDI (3 tur / 20 persona — 19/1/0 RED DOĞRULANDI, §7) → koşul SAĞLANMADI (red değişmedi) → red geçerli.**

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Firebase Auth (tam SaaS)** | 50K MAU ücretsiz (§1.3-1); olgun MFA/social/phone; ölçeklenebilir; Google ekosistemi entegrasyonu | Vendor lock-in + veri/ekosistem bağımlılığı (§1.3-2); fiyat sürprizi riski (SMS $0.01-0.06, SAML/OIDC); veri egemenliği kaybı (ADR-029 `:235`); kendi auth'ımız zaten var (ADR-058) → **ek bağımlılık, alternatif değil**; bypass/fail-closed ilkesiyle çatışma (ADR-008) | Dizin `:133` gerekçesi (**Harici bağımlılık**) + ADR-058 `:125` tek auth noktası + ADR-029 `:235` kendi yığın ilkesi — red'in **doğrudan hedefi** |
| 2 | **Auth0 / Supabase Auth / Clerk (diğer harici SaaS)** | Auth0 = enterprise olgunluk; Supabase = "en çok kontrol"; Clerk = DX | Hepsi **harici SaaS** → aynı vendor lock-in + veri egemenliği riski (§1.3-2); Auth0/Okta fiyat sürprizleri kanıtlanmış (dev.to/mukesh_13) | Red **harici SaaS sınıfını** reddeder, tek provider'a değil (§2.1/3); bu üçü de kendi auth'ımızla (ADR-058/052) çatışır |
| 3 | **Self-hosted açık kaynak IAM (Keycloak / Authelia / Authentik)** | Ücretsiz lisans; Keycloak kurumsal olgunluk (SSO/MFA/federasyon); Authelia hafif | Ops yükü + Java/alchemy stack (Keycloak); bağımlılık farklı noktaya taşınır (sağlayıcı → kendi ops ekibi — §1.3-3); kendi auth'ımız zaten var → **ek bağımlılık + çakışma** (ADR-058 tek nokta) | **REDDEDİLDİ — ama kapsam dışı** (§2.1/3): bu dosya onu **adıyla reddetmez**; disk'te reddeden ADR yok → talep **ayrı debate + yeni ADR** ister; ADR-029 `:235` ilkesi onayı şart koşar |
| 4 | **`firebase/php-jwt` (JWT kütüphanesi)** | Yaygın; basit API; RS256/EdDSA hazır | <6.0.0 `kid` keyring algoritma-confusion geçmişi (CVE-2025-45769); vault zaten `lcobucci/jwt` diyor (`brain.md:145`, arşiv `:592/:666`) | **REDDEDİLDİ — ama kapsam dışı** (ADR-059 `:219`): bu red'in hedefi **auth servisi**, kütüphane değil; JWT lib kararı ADR-059'da |
| 5 | **Kendi auth yığını (bugünkü uygulama)** | Tam kontrol + veri egemenliği; fail-closed + break-glass (ADR-058); hibrit session/JWT (ADR-052); TOTP MFA + kurtarma kodları (ADR-059); session hijyeni (ADR-011) | Ops/bakım yükü bizde; kendi JWT lib'i PLANNED (ADR-059, composer 0); kesinti toleransı ekibin sorumluluğunda | **Reddedilmedi — bu, yerini alan yaklaşımdır** (ADR-043/052/058/059/011/008); bedeli §4.2/§4.3'te yazılı, örtbas edilmedi |

*(Kabul edilen uygulama alternatifi — kendi auth yığını — §3'te "reddedilmedi" olarak ayrılmadı; o, yerini alan yaklaşımdır ve bu red'in gerekçe kaynağıdır. Self-hosted satırı da aynı sebeple "kapsam dışı" olarak durur: red onu adıyla yasaklamaz, ayrı ADR ister.)*

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Kimlik doğrulama kararı tek yerde toplandı:** `grep firebase` → bu dosya + `brain.md:145` (kütüphane) + ADR-059 (JWT lib ret); ADR-058 `:125` + ADR-043 `:9` + ADR-052 `:9` + ADR-029 `:235` + `index.md:133` ile gerekçe ailesi tek çatıya alındı; "Firebase neden yok" sorusunun yanıtı vault'ta yazılı.
- **Red 2026 verisiyle yeniden sınandı:** 3 sorgu / ~24 kaynak — fiyat modeli/gizli maliyet + vendor lock-in kanıtı + self-hosted kapsam daraltması lehine çalıştı; **iki ters/iki-yönlü kanıt dürüstçe yazıldı** (Firebase 50K ücretsiz + olgun → gerekçe mimari oldu; self-hosted ücretsiz → red yalnız SaaS'ı reddeder — §1.3-1/3).
- **Kapsam hatası önlendi:** "Harici bağımlılık" ifadesi **yalnız harici SaaS kimlik servisini** kapsar; **self-hosted açık kaynak IAM kapsam dışındadır** (ayrı ADR) → red, ADR-058/043 ile **çelişmiyor**, onları tamamlıyor (§2.1/3).
- **Yanlış hedef uyarısı konuldu:** `firebase/php-jwt` (kütüphane) ile Firebase Auth (servis) **ayrı kararlardır** — vault'taki tek `firebase` gerekçesi kütüphane düzeyindeydi, bu dosya ikisini ayırdı (§2.1/4, §7.1/5).
- **Geri dönüş temiz:** hiçbir zaman kurulmadığı için `git revert` edilecek değişiklik **0** (§1.1/7, §5.2).
- **Dizin satırı artık kaynağa sahip:** `index.md:133` "no source" iddiası fiilen geçersiz (bu dosya kaynaktır) → bayrak temizliği §5.1/3'te ertelendi, §7.1/1'de raporlandı.

### 4.2 Olumsuz Sonuçlar

- **Kendi auth'ın bedeli kabul edildi:** Firebase Auth gibi hazır olgunluk **bizde yok** → MFA/federasyon/SSO işleri elle yazılır (ADR-059 TOTP + kurtarma kodları, ADR-043 OAuth2/PKCE); bu bedel §4.3/1'de yazılı, gizlenmez.
- **Ops yükü tamamen bize kaldı:** harici sağlayıcı kesintisi riski ortadan kalkmadı, **ops ekibinin sorumluluğuna** taşındı (ADR-058 L2 TTL + circuit breaker + break-glass bugün **PLANNED** — §4.2/3).
- **JWT lib PLANNED durumda:** composer'da JWT kütüphanesi **0** (ADR-059 `:40`) → Bearer yolu stub `null` (fail-closed) → API auth yalnız session/API-key ile çalışıyor; kendi JWT yolu henüz **kurulmadı** (bu red'i zayıflatmaz ama "kendi yığın" iddiasının **yarım** olduğunu dürüstçe işaretler).
- **Fiyat argümanı ölçülemedi:** CoreMusic MAU profili UNKNOWN → "Firebase daha pahalı olurdu" iddiası **kurulamaz**; red **mimari** ile savunulur (§2.1/5) — bu savunma daha zayıftır, itiraz gelecektir (§4.3/3).
- **Benimsenme baskısı sürecek:** Firebase Auth 50K ücretsiz + Google ekosistemi → yeni ekip/agent önerisi "neden Firebase yok?" der; yanıt bu dosyada yazılı (**§2.1/3-5**), savunma kolay değil.
- **Dizin/seri tutarsızlığı sürüyor:** `rejected/index.md` boş (12 red'in hiçbiri satırlanmadı) + `index.md:133` dead-link bayrağı duruyor → §7.1/1-2.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **Kendi auth'ın başarısızlığı** — üretimde güvenlik olayı veya ciddi kesinti (harici sağlayıcı olsaydı bu onların sorunu olurdu) | 3 (olası) | 4 (yüksek — kimlik erişimi) | ADR-058 PLANNED HA katmanı (L2 TTL + circuit breaker + break-glass — §5.1/4); ADR-008 fail-closed; ADR-013 rate limit; §2.3/2 ölçüm kapısı (başarısızlık sayılarla belgelenirse red yeniden değerlendirilir) |
| **Ops/bakım darboğazı** — MFA/federasyon/SSO işleri elle yazılır, teslim süresi uzar | 4 (çok olası) | 3 (orta) | ADR-059 TOTP + kurtarma kodları (yalnızca gerekli MFA); ADR-043 kademeli geçiş fazları; YAGNI — federasyon gereksinimi **ölçülmeden** eklenmez |
| **"Firebase ücretsiz" dış baskısı** (50K MAU + olgun) | 4 (çok olası) | 3 (orta) | §1.3-1/3 + §2.1/3-5 (gerekçe mimari, ölçülmüş fiyat avantajı yok) + §2.3/2 (ölçüm kapısı) — yanıt vault'ta yazılı |
| **Self-hosted IAM talebi** (Keycloak/Authelia "ücretsiz + olgun" → "kendimiz kuralım" önerisi) | 3 (olası) | 3 (orta) | §2.1/3: kapsam dışıdır, **ayrı debate + yeni ADR** ister; ADR-029 `:235` ilkesi onayı şart koşar; ADR-058 tek nokta çakışması — kendi auth zaten var → "alternatif değil, ek bağımlılık" yanıtı hazır |
| **JWT stub'un açık kalması** — Bearer yolu `null` (fail-closed) → API auth yalnız session/API-key | 3 (olası) | 2 (düşük — fail-closed güvenlik düşürmez) | ADR-059 adım 3 (`lcobucci/jwt` + RS256 allowlist) §5.1/4'te; stub **fail-closed** olduğu için güvenlik düşmez, yalnız Bearer yolu kapalı |
| **Firebase'in fiyat sürprizi argümanı tersine dönerse** (sağlayıcı fiyat düşürürse) | 2 (düşük) | 2 (düşük) | §2.3/2: fiyat avantajı **ölçülmeden** red değişmez; ölçüm + mimari çatışma birlikte değerlendirilir (yalnız fiyat tek başına kapıyı açmaz) |

---

## 5. Uygulama (Implementation)

> Bu kayıt **salt-okunur seri**dir (`rejected/`); "uygulama" = kaydın vault'a doğru yerleştirilmesi ve denetimidir — kod değişikliği **yoktur**.

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | Bu dosyayı `.ai/.decisions/rejected/R-007-firebase-authentication.md` olarak yaz (şablon §1-§7, 7 bölüm dolu) + `log.md` append ("R-007 yazıldı (debate PENDING)") | Vault Steward | 25 dk |
| 2 | **Debate** (3 tur / 20 persona — **✅ TAMAMLANDI 2026-10-06 · 19/1/0 RED DOĞRULANDI**; kayıt §7, bağlayıcı şartlar §5.4) + Tech Lead onayı ✅ | MO + Tech Lead | tamamlandı 2026-10-06 |
| 3 | **`index.md:133` `<!-- dead-link ... -->` bayrağı → DOKUNULMADI** (talimat gereği son sıfırlamaya ertelendi — rapor-only) · `rejected/index.md` § tablosu **BOŞ → DOKUNULMADI** (rapor-only) | Vault Steward | son sıfırlama |
| 4 | **PLANNED HA katmanı (§4.3/1):** ADR-058 `:270` adım 5 — `auth/health` + circuit breaker + TTL'li doğrulama cache'i + L2/L3 break-glass + simüle auth-kesinti testi — kendi auth'ın kesinti toleransını **sayıyla** kanıtlar (§2.3/2'nin de zemini) | Security + DevOps | 3 gün |
| 5 | Periyodik denetim: composer ×5 + üretim PHP/JS'te `firebase|kreait|google/auth|firebase-php` = **0** korunur (kimlik doğrulama **yalnız ADR-043/052/058 üzerinden**) | Security Engineer + Backend Architect | her sprint |
| 6 | Sayfa-içi derin doğrulama turu: Firebase fiyat tabloları (MAU/SMS/SAML-OIDC) + Auth0/Okta fiyat sürprizleri + Keycloak/Authelia maliyet kalemleri (§1.3 başlık/özet düzeyi kaldı) | Researcher | üretim öncesi |
| 7 | **Ölçüm adımı (§2.3/2 için kanıt):** kendi auth'ın üretim metrikleri (kesinti süresi, bakım işçiliği/sprint, MFA olay sayısı) + Firebase Auth MAU profili karşılaştırması — **"Firebase gerekli" iddiası bu ölçümle** tartılır; sayı yoksa red değişmez | Security + Backend + Data | bir sonraki sprint |

### 5.2 Geri Dönüş Planı

Bu karar **kod tarafında geri alınacak bir şey üretmedi** (Firebase Auth hiç kurulmadı → `git revert` edilecek değişiklik **0**; §1.1/7). Geri dönüş = **yeniden değerlendirme** demektir ve yalnız §2.3 koşullarından biri yazılırsa yeni ADR ile açılır: (1) ADR-058/043/052/059 yeni ADR ile değişirse → bu dosya `superseded` notuyla **bağlanır, düzenlenmez**; (2) ölçülmüş karşı-kanıt (§2.3/2 metrikleri) → §2.3/2 kapısı; (3) vault "yeni harici bağımlılık" onayı işletilirse → §2.3/3 (kullanıcı onayı + etki analizi + debate); (4) debate sonucu değişirse → debate kaydı + yeni ADR. Vault bozulursa standart kurtarma `git checkout` + son commit (AGENTS.md §17 #10). **Reddedilen teknolojinin kodda izi olmadığı için kullanıcı/veri etkisi YOKTUR.**

### 5.3 Debate Şartları

**Kayıt:** ✅ **TAMAMLANDI — 3 tur / 20 persona, 19/1/0 RED DOĞRULANDI** (2026-10-06 — sonuç §7, bağlayıcı şartlar §5.4; durum `rejected` **kullanıcı onaylı red** olarak zaten kayıtlıdır).

**Planlanan akış:**

- **Tur 1 (20 persona — bulgu):** üretim kodunda Firebase Auth **0** + kendi auth **7 sınıf + 2 test** (§1.1/7-8) · red kaynağı `index.md:133` (dead-link **dokunulmadı** → §5.1/3) · yerini alan 5 ADR glob **True** + ADR-011 zemin · gerekçe ailesi ADR-058 `:125` + ADR-043 `:9` + ADR-029 `:235` · 3 sorgu / ~24 kaynak · `rejected/index.md` boş → dokunulmadı · **kapsam daraltması:** self-hosted kapsam dışı (§2.1/3).
- **Tur 2 (itiraz → çözüm → şart):** (i) "Firebase 50K ücretsiz + olgun" → red gerekçesi **mimari** (kendi auth'ımız var) → **Şart 1 kapsam sabitleme**; (ii) "self-hosted Keycloak ücretsiz" → kapsam dışı, ayrı ADR → **Şart 2 self-hosted ayrımı**; (iii) "kendi auth kesinti riski" → ADR-058 HA + ölçüm kapısı → **Şart 3 ölçüm kapısı** (§2.3/2).
- **Tur 3 (oy):** sonuç + bağlayıcı şartlar buraya ve §7'ye yazılacak.

**Planlanan bağlayıcı şartlar (debate öncesi plan):**

1. **Şart 1 — Kapsam sabitleme:** red kapsamı **"red = harici SaaS kimlik servisi (Firebase Auth dahil; Auth0/Supabase/Clerk de aynı sınıf)"** olarak sabitlenir; **self-hosted açık kaynak IAM açıkça reddin dışındadır** (ayrı debate + yeni ADR).
2. **Şart 2 — Self-hosted ayrımı:** Keycloak/Authelia/Authentik talebi bu dosya üzerinden **tartışılamaz** — yeni ADR + kullanıcı onayı (ADR-029 `:235` ilkesi + vault-template `:267/:644` "yeni harici bağımlılık onayı") + debate ile açılır.
3. **Şart 3 — Ölçüm kapısı:** "Firebase gerekli/avantajlı" iddiası yalnız §2.3/2 metrikleriyle (kendi auth başarısızlık ölçüleri + Firebase MAU/fiyat profili) kurulabilir; ölçüm yoksa red değişmez.
4. **Şart 4 — Yanlış hedef ayrımı:** `firebase/php-jwt` (kütüphane) ADR-059 kapsamındadır; bu red **yalnız auth servisini** kapsar — debate bu ikisini birleştirmez.

### 5.4 Debate Sonucu — Bağlayıcı Şartlar (2026-10-06 · 3 tur / 20 persona)

> Debate **tamamlandı** (kayıt §7): Tur 1 bulgu → Tur 2 çapraz eleştiri (4 itiraz) → Tur 3 kanıt-öncelikli uzlaşma (oy çokluğu yok) → **19/1/0 RED DOĞRULANDI**. Aşağıdaki 4 şart **bağlayıcıdır**; §5.3'teki 4 plan şartı tarihsel kayıt olarak korunur (§6'daki "Debate Şart 1–4" satırları bu planın kaydıdır — bağlayıcı sonuç **F1–F4**'tür).

1. **F1 — Self-hosted boşluğu beyanı + ADR kapısı:** red'in **kapsam dışı** bıraktığı self-hosted açık kaynak IAM (Keycloak/Authelia/Authentik) boşluğu dosyada açıkça beyan edilir (§2.1/3 · §3/3); bu alanda karar **yalnız ayrı debate + yeni ADR** ile verilir — bu dosya üzerinden tartışılamaz (Tur 2 itiraz 1).
2. **F2 — Mimari gerekçe sabitleme:** "Firebase Auth ücretsiz + olgun" ters kanıtı karşısında red gerekçesi **hıza/fiyata değil mimariye** sabitlenir: uygulama katmanı olgunluğu kendi elimizdedir (ADR-052 hibrit session/JWT · ADR-059 JWT lib + TOTP MFA); fiyat argümanı tek başına gerekçe sayılmaz (Tur 2 itiraz 2).
3. **F3 — SAML/OIDC ücreti çift kaynak:** §1.3'teki "SAML/OIDC ücretleri" iddiası **tek kaynaktır** → ya **ikinci bağımsız kaynakla** doğrulanır ya da metinde **kalıcı `⚠️ VERIFICATION REQUIRED`** işareti taşır (Tur 2 itiraz 3).
4. **F4 — MAU bilinmiyorsa yazılmaz:** CoreMusic MAU profili **UNKNOWN** → "Firebase avantajlı/pahalı" gibi **ölçülmemiş fiyat iddiası kurulmaz**; fiyat karşılaştırması yalnız MAU profili ölçüldükten sonra yazılır, aksi hâlde `⚠️ VERIFICATION REQUIRED` (Tur 2 itiraz 4).

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../index]] | Karar dizini — bu red'in kaydı (`:133`, slug + "Harici bağımlılık" + dead-link bayrağı §5.1/3) |
| [[../accepted/ADR-058-centralized-auth-service]] | **Yerini alan (birincil):** tek auth noktası (`:125` karar a — "kimlik üretimi + login/logout + oturum yalnız auth.coremusic.net"); fail-closed + break-glass (`:175/:204`) — Firebase Auth bu hükmün **doğrudan ihlali** |
| [[../accepted/ADR-043-auth-subdomain-consolidation]] | **Yerini alan:** tek kimlik otoritesi + cookie domain + OAuth2/PKCE federasyonu kendi elimizde (`:9`) — harici SaaS bu **tek otoriteyi** parçalardı |
| [[../accepted/ADR-052-hybrid-auth-session-jwt]] | **Yerini alan:** hibrit session/JWT modeli (`:9`) + JWT stub `null` bulgusu (`:50`) + ApiSessionManager IMPLEMENTED (`:10`) — Firebase Auth bu **hibrit modeli** atlar |
| [[../accepted/ADR-059-jwt-library-and-mfa]] | **Yerini alan:** `lcobucci/jwt` + TOTP MFA + 10 kurtarma kodu (`:128/:19`); `:219` alternatif 1 `firebase/php-jwt` ret — JWT/kütüphane kararı **bu ADR'nin**, bu red'in değil (§2.1/4) |
| [[../accepted/ADR-011-session-management]] | **Session zemini:** OWASP seti + hibrit saklama + hijyen (`:19` cookie domain + regen) — Firebase cookie'leri bu **hijyen setini** atlar |
| [[../accepted/ADR-008-bypass-auth-middleware]] | **Bypass ilkesi:** "bypass = üretimde fail-closed" (`:19`) — Firebase Auth'un kendi fallback/bypass modelleri bu ilkeyle **çatışırdı** (harici kesintide fail-open riski) |
| [[../accepted/ADR-029-listening-rooms-social]] | **İlke dayanağı:** `:235` "Firebase / Ably / Supabase Realtime" yönetilen servis reddi — "kendi vault/stack ilkesi (SSOT + kendi servislerimiz) + veri egemenliği → reddedildi" — bu red'in **aynı ilkesinin auth ayağı** |
| [[../accepted/ADR-020-api-public-security]] | API auth üçlüsü (API key / JWT / OAuth2 PKCE + Bearer kilidi) — Firebase Auth bu üçlüyü **atlar** |
| [[../accepted/ADR-013-rate-limiting-apcu]] | Rate limit — auth uçlarına uygulanır (ADR-052 `:19` hizası) |
| [[../accepted/ADR-010-csrf-protection-strategy]] | CSRF + şart 3 (JWT stub kapanana kadar cookie-auth tek yol) — Firebase social/phone login bu **çerez-akışını** atlar |
| [[../accepted/ADR-047-login-redirect-session-bridge]] | İmzalı tek kullanımlık köprü token'ı — kendi login akışının parçası |
| [[../accepted/ADR-056-auth-module-implementation]] | RBAC `user_roles` + Permission middleware — authz katmanı |
| [[../accepted/ADR-004-multi-domain-spa]] | Subdomain iskeleti + cookie haritası — auth yönlendirmelerinin bağlamı |
| [[../../raw/brain]] | `:145` "`firebase/php-jwt` | Yasaklı — RS256 için `lcobucci/jwt` kullanılır" — **kütüphane** düzeyi tek `firebase` gerekçesi (§2.1/4) |
| [[../../CLAUDE]] | Kural metinleri — Zero-Hallucination + onay kapıları |
| [[../../index]] | Master katalog — ADR kayıtları |
| [[../../raw/keys]] | Keyword haritası — "auth / Firebase" arama eşiği |
| [[../../.templates/adr/adr-template]] | Guardrail #16 — bu dosyanın §1-§7 iskeleti + §1.3 9 alan kaynağı (**gerçek yol `.ai/.templates/adr/` — §7.1/8**) |
| Dizin satırı | `index.md:133` — slug otoritesi + dead-link bayrağı (§5.1/3) |
| Debate şartları | Bu dosya **§5.3** (plan) + **§5.4** (bağlayıcı F1–F4) — debate ✅ **TAMAMLANDI (19/1/0 RED DOĞRULANDI)** + debate kaydı **§7** |
| Debate Şart 1 | **Bağlayıcı:** kapsam sabitleme ("red = harici SaaS kimlik servisi; self-hosted kapsam dışı") → §5.3 |
| Debate Şart 2 | **Bağlayıcı:** self-hosted ayrımı — Keycloak/Authelia talebi ayrı debate + yeni ADR ister → §5.3 |
| Debate Şart 3 | **Bağlayıcı:** yeniden değerlendirme kapısı — yalnız §2.3/2 metrikleriyle → §5.3 |
| Debate Şart 4 | **Bağlayıcı:** `firebase/php-jwt` ≠ Firebase Auth — ikisi birleştirilemez → §5.3 |
| Debate Bağlayıcı Şart F1 | **Bağlayıcı (debate 2026-10-06):** self-hosted boşluğu açık beyanı + karar yalnız ayrı debate/yeni ADR → §5.4/1 |
| Debate Bağlayıcı Şart F2 | **Bağlayıcı (debate 2026-10-06):** red gerekçesi mimariye sabit — uygulama katmanı olgunluğu (ADR-052/059) → §5.4/2 |
| Debate Bağlayıcı Şart F3 | **Bağlayıcı (debate 2026-10-06):** SAML/OIDC ücreti ikinci kaynak ya da kalıcı ⚠️ → §5.4/3 |
| Debate Bağlayıcı Şart F4 | **Bağlayıcı (debate 2026-10-06):** MAU profili UNKNOWN → fiyat avantajı iddiası yazılmaz (V.R.) → §5.4/4 |
| [[R-001-redux-style-state-management]] | Seri kardeşi — aynı salt-okunur red kayıt formatı |
| [[R-002-mongodb-document-store]] | Seri kardeşi — aynı salt-okunur red kayıt formatı (kanıt tablosu/dürüst etiket deseni) |
| [[R-003-jquery-ui-framework]] | Seri kardeşi — format referansı (§1.3 9 alan, §7.1 rapor deseni) |
| [[R-004-webpack-bundle-system]] | Seri kardeşi — format referansı (§7.1 rapor deseni) |
| [[R-005-rest-only-api]] | Seri kardeşi — format referansı (künye, §1.1 kanıt tablosu, §2.3 şart satırı, §7.1 rapor) |
| [[R-006-laravel-eloquent-orm]] | Seri kardeşi — format referansı (kapsam daraltma dersi §2.1/3 bu dosyadan uygulandı) |
| Düz metin | Eski seri R-008…R-012 (`rejected/index.md` tablosu boş → §7.1/2) · Keycloak/Authelia/Authentik (disk'te reddeden ADR yok → §2.1/3) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali | 2026-10-06 | ✅ |
| Tech Lead | — | 2026-10-06 | ✅ |
| Arch Lead | — | — | ⏳ |

**Debate kaydı:** ✅ **TAMAMLANDI — 3 tur / 20 persona (AGENTS.md §6.1: Expert 5 · Senior 5 · Junior 10), sonuç 19/1/0 RED DOĞRULANDI.** Durum **rejected** (kullanıcı onaylı red) olarak kayıtlıdır — debate sonucu **red'i değiştirmez**, yalnız **bağlayıcı şartları (F1–F4 — §5.4)** bağladı.

| Tur | Katılım | Çıktı |
|-----|---------|-------|
| **1 — bulgu** | 20/20 persona (Expert 4 kabul + 1 kırmızı · Senior 1 kabul + 1 uyarı · Junior 8 neutral + 1 uyarı) | `R-007-firebase` grep **2 isabet** — index `:133` (slug + "Harici bağımlılık" + dead-link **dokunulmadı**; prompt'taki 132 tahmini düzeltildi → dosya 133) + R-006 `:227`; kodda 0 · yerini alan **6/6 diskte** (ADR-008 · ADR-043 · ADR-052 · ADR-058 · ADR-059 · ADR-011) · Firebase composer/PHP/JS **0** · kendi auth **7 sınıf + 2 test** · `firebase` izi **0** (vault isabetleri = firebase/php-jwt → **kütüphane**, red hedefi **servis**) · kapsam daraltma: yalnız harici SaaS (Firebase/Auth0/Supabase/Clerk), self-hosted kapsam dışı (reddeden ADR yok) · 3 sorgu / ~24 benzersiz kaynak (2 dürüst gerilim + 3 açık: sayfa-içi tur yok · MAU UNKNOWN · SAML ücreti tek kaynak) · **26/26 wiki-link diskte** · `rejected/index.md` boş → dokunulmadı |
| **2 — itiraz→çözüm** | 4 itiraz (çapraz eleştiri) | (1) self-hosted kapsam dışı boşluk → açık boşluk beyanı + ayrı ADR kapısı → **F1**; (2) Firebase ücretsiz + olgun → gerekçe mimariye sabitle (uygulama katmanı olgunluğu, ADR-052/059) → **F2**; (3) SAML ücreti tek kaynak → ikinci kaynak ya da kalıcı ⚠️ → **F3**; (4) MAU UNKNOWN → bilinmiyorsa yazma, V.R. → **F4** |
| **3 — oy** | 20 persona — kanıt-öncelikli uzlaşma (oy çokluğu yok) | Expert kanıtları üstün (security-engineer lock-in + architect self-hosted boşluğu) → **RED DOĞRULANDI 19/1/0** + bağlayıcı şartlar **F1–F4** (§5.4) |

> **Sonuç:** R-007 **REDDEDİLDİ (kullanıcı onaylı) — debate ✅ TAMAMLANDI (3 tur / 20 persona — 19/1/0 RED DOĞRULANDI)** · **Tech Lead:** ✅ · **Arch Lead:** ⏳ · bağlayıcı şartlar **F1–F4** (§5.4).

### 7.1 Rapor Notları (bu işlemde dokunulmayanlar)

1. **Dead-link bayrağı:** `index.md:133` `<!-- dead-link: R-007-firebase-authentication no source 2026-09-24 -->` **olduğu gibi bırakıldı** — artık `no source` iddiası **geçersizdir** (bu dosya kaynaktır) → temizlik son sıfırlamaya ertelendi, burada rapor edildi.
2. **`rejected/index.md` durumu:** dosya **VAR** (v1.0.1, `total: 12`) ama tablo **BOŞ** (başlık satırları, kayıt satırı yok) → bu işlemde **oluşturulmadı ve doldurulmadı** (dizin satırı düzenleme yetkisi kapsam dışı) → rapor-only. Seri **R-001…R-006** dosyalara karşılık tabloda **0 satır** var.
3. **Slug hizası:** dosya adı `R-007-firebase-authentication` = `index.md:133` slug **birebir** ✅ (R-001…R-006 dersi: tahmin yok, `rejected/` glob'u ile doğrulandı — bu işlem öncesi `R-007*` = 0 dosya).
4. **Debate:** ✅ **TAMAMLANDI** (3 tur / 20 persona · 2026-10-06 — sonuç §7, bağlayıcı şartlar §5.4 · 19/1/0 RED DOĞRULANDI) · **Tech Lead:** ✅ · **Arch Lead:** ⏳ · **status: rejected** (kullanıcı onaylı red).
5. **Kod yüzeyi kanıtı:** composer ×5 Firebase **0** · üretim PHP `Firebase|kreait|firebase-php` **0** · üretim JS `Firebase|firebase` **0** · kendi auth **7 sınıf + 2 test** diskte · JWT lib composer **0** (ADR-059 PLANNED) → **Firebase 0 / kendi auth 7**.
6. **`firebase` iz haritası (dürüst):** vault'taki `firebase` isabetleri **tamamı** `firebase/php-jwt` (JWT **kütüphanesi**) düzeyinde: `brain.md:145` + arşivler (`prompt3-api:1833`, `prompt1-spa-router:1074/1687`, `prompt-unified:592`, `prompt-shared-base:265/365`) + ADR-059 `:219` + ADR-052 `:50` (repo tarama notu) + `index.md:133` (red kaydı). **"Firebase Auth servisi" red gerekçesi tek satır** (`index.md:133`); bu dosya kütüphane/servis ayrımını koydu (§2.1/4, Debate Şart 4).
7. **Yerini alan ADR disk kanıtı:** `accepted/` glob = True ×6 (ADR-008 · ADR-043 · ADR-052 · ADR-058 · ADR-059 + ADR-011); hepsi `status: accepted`; frontmatter/authority satırları ön-okundu (ADR-058 `:9/:125`, ADR-043 `:9`, ADR-052 `:9/:50`, ADR-059 `:128/:219`, ADR-011 `:19`, ADR-008 `:19`) — ADR-029 `:235` ilke dayanağı ayrıca doğrulandı.
8. **Şablon yolu notu:** görev `templates/adr/adr-template.md` der; disk kanıtı `.ai/.templates/adr/adr-template.md` (glob = True, v2.0.2) — bu dosya o şablonun §1-§7 iskeletiyle (§1.3 9 alan, §7 Onay) **R-006 formatı** kullanılarak yazıldı (R-006 `:258` ile aynı düzeltme).
9. **Kaynak derinliği:** §1.3 ~24 benzersiz kaynak **başlık/özet düzeyinde** derlendi (sayfa-içi tur yok) → §5.1/6'ya bırakıldı; **Firebase fiyat rakamları** (50K MAU, $0.0025-0.0055/MAU, SMS $0.01-0.06) 2026 blog/vendor kaynaklıdır ve **resmî Firebase fiyat sayfasıyla satır-içi doğrulanmadı** (§1.3-7 işaretli); SAML/OIDC ücret iddiası **tek kaynak** (metacto) → işaretli; CoreMusic MAU profili **UNKNOWN** → fiyat avantajı ölçülmedi (§2.1/5).

---

*R-007 v1.0.0 | 2026-10-06 | Created — CoreMusic Vault (.decisions/rejected/ sıfırdan yazım, salt-okunur seri; debate ✅ TAMAMLANDI — 3/20 persona, 19/1/0 RED DOĞRULANDI)*
