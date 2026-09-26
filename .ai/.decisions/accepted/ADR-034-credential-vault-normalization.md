---
id: ADR-034
title: Credential Vault Normalization — Tek SSOT Credential Deposu, `service.category.name` Adlandırması, ADR-015 Hizalı Dönüşüm ve CI Secret Scan Kapısı
type: adr
category: security
date: 2026-09-25
updated: 2026-09-25
version: 1.0.0
status: accepted
authority: ADR-034 Karar Metni (SSOT)
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
deciders: ["Vault Steward", "Security Engineer"]
consulted: ["DevOps Engineer", "Backend Architect", "QA Engineer"]
informed: ["Master Orchestrator", "Data Engineer"]
supersedes: null
superseded-by: null
related:
  - "[[.ai/.decisions/accepted/ADR-015-env-parser-strategy.md]]"
  - "[[.ai/.decisions/accepted/ADR-011-session-management.md]]"
  - "[[.ai/.decisions/accepted/ADR-020-api-public-security.md]]"
  - "[[.ai/.decisions/accepted/ADR-022-database-hardened-security.md]]"
  - "[[.ai/.decisions/accepted/ADR-033-sql-normalization-strategy.md]]"
---

# ADR-034: Credential Vault Normalization — Tek SSOT Credential Deposu, `service.category.name` Adlandırması, ADR-015 Hizalı Dönüşüm ve CI Secret Scan Kapısı

**Durum:** accepted (Draft → Review → Active → **Active**; frozen YOK)
**Tarih:** 2026-09-25
**Karar Veren:** Vault Steward (kullanıcı onaylı karar kapsamı) + Security Engineer (domain)
**İlgili ADR'ler:** [[.ai/.decisions/accepted/ADR-015-env-parser-strategy.md]] (env parser + rotasyon usulü §2.2g + şart 1/3) · [[.ai/.decisions/accepted/ADR-011-session-management.md]] (auth/oturum) · [[.ai/.decisions/accepted/ADR-020-api-public-security.md]] (API key) · [[.ai/.decisions/accepted/ADR-022-database-hardened-security.md]] (şifreleme/audit) · [[.ai/.decisions/accepted/ADR-033-sql-normalization-strategy.md]] (biçim referansı — aynı numara-boşluğu-doldurma usulü)

---

## 1. Bağlam ve Kod Kanıtı

### 1.1 Proje durumu (kod kanıtı — IMPLEMENTED / PLANNED ayrımı)

Bu ADR, CoreMusic'in **credential (secret, anahtar, token, bağlantı bilgisi) envanterinin tamamı** için geçerli normalizasyon kuralını yazar: tek gerçeklik kaynağı (SSOT), adlandırma alanı, rotasyon hattı, erişim rolleri, `.env` → vault geçiş planı ve CI secret-scan kapısı. Kanıt, 2026-09-25'te depo genelinde tarama yapılarak (disk + git geçmişi + kod desenleri) derlendi ve iki kategoride etiketlendi: **IMPLEMENTED** (kodda/diskte mevcut) / **PLANNED** (karar olarak kurulan, karşılığı henüz yok).

**A) `.env` envanteri (disk + git kanıtı):**

| Ölçüm | Değer | Kanıt |
|-------|-------|-------|
| Diskte `.env` dosyası | **0** (gerçek, dolu `.env` yok) | `Get-ChildItem -Recurse -Filter .env` → 0 |
| Git'te izlenen `.env` | **0** — yalnız **2 `.env.example`** | `git ls-files "*.env"` → 0; `.env.example` → 2 |
| `.env` ekleyen commit | **0** (tarih boyunca) | `git log --all --diff-filter=A -- "*.env"` → 0 sonuç |
| `.gitignore` engeli | `.env` girişleri → `.gitignore:6-9` ve `:68-70` | engel mevcut ✅ |
| `shared/config/.env.example` | **2822 bayt, 18 anahtar** | dosya boyutu + `KEY=` sayımı |
| `auth.coremusic.net/config/.env.example` | **536 bayt, 13 anahtar** | dosya boyutu + `KEY=` sayımı |
| `home.coremusic.net/config/.env.example` | **YOK** ⚠️ VERIFICATION REQUIRED — ama `home.coremusic.net/config/constants.php:11-13` `config/.env` dosyasını **yüklemeye çalışıyor** | örnek dosyasız yükleyici |

**B) Config service — IMPLEMENTED çekirdek (vault'un mevcut taşıyıcısı):**

| Dosya | Kanıt (dosya:satır) |
|-------|---------------------|
| `shared/src/Config/ConfigManager.php` | 160 satır; `SENSITIVE_KEYS` `:10-15`, `getSecure()` `:42-49`, `maskSecret()` `:56-66`, `getEnv()` `:51-53`, `filterSensitive()` `:136-154` |
| `shared/src/Config/EnvParser.php` | 51 satır; `$_ENV` okuması `:46-47` |
| `shared/src/Config/DomainConfig.php`, `shared/src/Config/AuthRouteConfig.php` | domain yapılandırması katmanı |
| `shared/src/Interfaces/Config/IConfigManager.php` | arayüz (bağımlılık enjeksiyonu için) |
| `shared/tests/Unit/Config/ConfigManagerTest.php` | maskeleme/erişim testleri mevcut |

→ **Vault işlevi bugün fiilen `ConfigManager` + `.env.example` ikilisinde duruyor**: hassas anahtar listesi (`SENSITIVE_KEYS`), maskeleme (`maskSecret`) ve filtreleme (`filterSensitive`) **kodda var**. Eksik olan depolama katmanının kendisi değil, **tekilleştirme + adlandırma + geçiş + denetim** kuralıdır.

**C) Doğrudan env erişimi — ADR-015 şart 1 hâlâ açık:**

| Desen | Satır / Dosya | Konumlar |
|-------|---------------|----------|
| `getenv(` | **6 satır / 4 dosya** | `shared/src/Config/ConfigManager.php:51,53` · `shared/src/OAuth/OAuthManager.php:77,78` · `auth.coremusic.net/config/constants.php:18` · `home.coremusic.net/config/constants.php:18` |
| `$_ENV[` | **10 satır / 8 dosya** | `shared/src/Bootstrap/RuntimeBootstrap.php:9` · `ConfigManager.php:53` · `EnvParser.php:46,47` · `auth.../config/constants.php:18` · `auth.../config/cors.php:11,17` · `auth.../include/Container/AuthContainer.php:70` · `auth.../include/Middleware/OriginCheckMiddleware.php:23` · `home.../config/constants.php:18` |
| `putenv(` | **0** | — |
| `$_SERVER[` | **84 satır / 27 dosya** | tamamı istek meta verisi (method, uri, remote_addr) — **yapılandırma okuması değil** |

Birleşim: **9 dosya** hâlâ `getenv`/`$_ENV` ile ham okuma yapıyor → ADR-015'in "tek giriş noktası" şartı **açık** (bu ADR §5.2/2 ile hizalanır). Config service'in kendi içi (`getEnv`) bu kuralın meşru istisnasıdır.

**D) Hardcoded secret taraması (8 desen, vendor/node_modules hariç):**

| Desen | Üretim kodu | Test kodu |
|-------|-------------|-----------|
| Genel secret/api-key literal desenleri (api_key literal, `define(SECRET\|PASSWORD\|API_KEY\|PRIVATE)`, PKCS8 `BEGIN PRIVATE KEY`, AWS `AKIA…`, GitHub `ghp_…`, OpenAI `sk-…`, `Bearer …` literal) | **0** (8 desenin tamamı) | 0 |
| `'password' => '<en az 6 karakter>'` | **0** | **5 eşleşme / 2 test dosyası**: `auth.coremusic.net/tests/Domain/DTO/LoginRequestTest.php:12,25,35,45` · `auth.coremusic.net/tests/Unit/Domain/DTO/LoginRequestTest.php:16` |

→ **Üretim kodunda hardcoded credential YOK** (IMPLEMENTED ✅). 5 test eşleşmesi sahte test verisidir, credential değildir; yine de CI kuralına takılabilir (§5.2/5 kalemi: test-sözleşmesi istisnası).

**E) OAuth credential yapılandırması — adlandırmanın bugünkü kanıtı:**

`shared/config/oauth-platforms.php:211-251` içindeki **20 literal** (`client_id` / `client_secret` alanları) gerçekte **credential değeri değil, çevresel değişken ADI**dır: ayraç karakterinin tamamı `_` (kod 95) ve desen `SNAKE_CASE`. Dosyanın `:208` yorumu hükmü zaten yazıyor: *"Credential'lar ASLA kodda hardcoded edilmez"*. → Mevcut uygulama **dolaylı referans** (ad → `ConfigManager`) kurmuş durumda; eksik olan bu adların **tek bir isimlendirme alanına** bağlanması (bu ADR §2.4-b).

**F) CI kapısı (secret-scan — IMPLEMENTED):**

`.github/workflows/secret-scan.yml` (938 bayt) → `gitleaks/gitleaks-action@v2`, `fetch-depth: 0` (tüm geçmiş), tetikleyici `push` + `pull_request`, `permissions: contents: read`, `timeout-minutes: 10`. Dosyanın `:4` yorumu: **diskte `.gitleaks.toml` YOK** ⚠️ VERIFICATION REQUIRED (yalnız varsayılan gitleaks konfigürasyonu). → Kapı **var ve çalışıyor**; bu ADR onu credential politikasının son halkası olarak konumlandırır (§2.4-f).

**G) Vault kayıtları ve ADR-034 slotu (boşluk-doldurma kanıtı):**

- `.ai/.decisions/index.md:71` → `ADR-034-credential-vault-normalization | Credential Vault Normalization | Security` (dizin satırı alıntısı; slug'ın hedefi bu dosyadır → `[[...]]` linki olarak değil düz metin) — **numara ve slug ayrılmış**.
- Aynı slot 9 dosyada daha taşıyor: `.ai/index.md:651` · `.ai/keys.md:64` (`credential vault, secret` keyword satırı) ve `:269` · `.ai/brain.md:989` · `.ai/WORKFLOW.md:524` · `.opencode/.workflows/security-audit.md:20` · `.ai/.templates/adr/adr-index.md:105` · `.ai/.templates/adr/adr-security-template.md:159,255,259` · `.ai/architecture/k6-guvenlik/README.md:43,153,390,405` · `k6-guvenlik/CLAUDE.md:54`.
- `.ai/keys.md` → `type: system`, `version: v28.3.2`, `authority: SSOT` — **anahtar değeri içermeyen keyword haritasıdır** (REDACTED uyumlu); `:317` bir hedefe (`architecture/k0-k5-software/k0-os-layer/credential-vault.md`) gidiyor → **Test-Path: False** ⚠️ VERIFICATION REQUIRED (bkz. §1.1-I).
- İlgili güvenlik mimarisi: `.ai/architecture/k6-guvenlik/vault-secrets.md` (**9627 bayt, "HashiCorp Vault Entegrasyonu"**) — durum 2/9 işaretli; cluster/AppRole/rotasyon/erişim satırları `:332-337` **PLANNED**.

**H) IMPLEMENTED / PLANNED ayrımı (dürüst etiket):**

- **IMPLEMENTED (kod/CI kanıtlı):** 0 `.env` (disk + git) · 2 `.env.example` (18/13 anahtar) · `ConfigManager` hassas-anahtar + maskeleme + filtre katmanı · testleri · `.gitignore` engeli · OAuth'da credential yerine env-adı kullanımı (20 literal) · `secret-scan.yml` (gitleaks, push+PR) · üretim kodunda hardcoded secret **0** · `vault-secrets.md` tasarım dokümanı (2/9).
- **PLANNED (bu ADR ile kurulan, karşılığı 0):** tek SSOT credential deposu (tek dosya/servis) · `service.category.name` ad alanı zorunluluğu · `.env` envanterinin alan-bazlı tekilleştirilmesi · rotasyon çizelgesi (ADR-015 §2.2g hizalı) · erişim rolü matrisi · `.env` → config service → Vault/KMS üç fazlı geçiş · CI'a secret-scan **politika** genişletmesi (`.gitleaks.toml` ⚠️ YOK) · 9 dosyadaki ham `getenv`/`$_ENV` okumasının ConfigManager'a taşınması (ADR-015 şart 1).

**I) İlgili hizalama sorularının dürüst cevapları:**

- **Başlık tutarsızlığı (bilgi: iki aday başlık):** `.ai/.decisions/index.md:71` başlığı **"Credential Vault Normalization"** diyor; `.ai/brain.md:989`, `.ai/.templates/adr/adr-index.md:105` ve `k6-guvenlik/README.md:153` ise **"AES-256-GCM credential vault"** yazıyor. Bu ADR **dizin (index.md) başlığını** esas alır (dizin karar numarasının sahibidir); AES-256-GCM ayrıntısı ADR-022'nin alanıdır, başlıkta tekrar edilmez → iki başlık `§6`'da not edilir, biri düzeltilir (dizin satırı ADR'ye ait değil, **revizyon ayrı işlemdir**).
- **ADR-015'in planladığı artefaktlar diskte YOK (0 dosya):** `shared/config/.env.schema` · `EnvSchema.php` · `EnvValidator.php` · `bin/config-doc.php` — hepsi ⚠️ VERIFICATION REQUIRED (dizin ve dosya yok); ayrıca `auth.coremusic.net/composer.json:22` **`vlucas/phpdotenv`'u hâlâ talep ediyor** (üretime girip girmediği ⚠️ VERIFICATION REQUIRED). Bunlar bu ADR'nin uygulama kalemi değildir (ADR-015'in işi); buraya yalnızca **kapı-ortağı** olarak yazılır.
- **`.env.example` anahtar kayması (drift):** shared 18 anahtar vs auth 13 anahtar. shared'a özgü 6: `APP_DEBUG`, `DEFAULT_PAGE`, `DB_NAME`, `BYPASS_ROLE`, `BYPASS_USERNAME`, `ASSETS_URL`; auth'a özgü 1: `DB_AUTH_NAME` (shared'daki `DB_NAME`'in karşılığı). → Aynı kavram iki adla yaşıyor: `DB_NAME` ↔ `DB_AUTH_NAME` (bu ADR §2.4-b normalizasyonunun ilk adayı).
- **Eski seri notu:** ADR-002 (frozen) `"ADR-034 dosyası diskte YOK"` benzeri bir statü notu taşıyor olabilir ⚠️ VERIFICATION REQUIRED — frozen metne dokunulmaz; bayatlık `log.md`'de not edilir (ADR-033 §1.1-E usulü).

### 1.2 Sorun Tanımı

Credential envanteri **fiziksel olarak temiz, kavramsal olarak dağınık**: gerçek `.env` dosyası yok (0 disk / 0 git), üretim kodunda hardcoded secret yok (8 desen 0), gitleaks kapısı çalışıyor — yani sızıntı riski düşük. Ama (1) credential **kavramı** en az 5 ayrı yerde yaşıyor: `ConfigManager::SENSITIVE_KEYS`, iki `.env.example`, `oauth-platforms.php` env-adları, `keys.md` keyword haritası, `vault-secrets.md` Vault tasarımı — hiçbiri diğerini SSOT ilan etmiyor; (2) **adlandırma alanı yok**: `DB_NAME`/`DB_AUTH_NAME` gibi aynı kavramın iki adı var, `oauth-platforms.php` `SNAKE_CASE` kullanırken index slug'ı `kebab-case`; hangi desenin geçerli olduğu yazmıyor; (3) **rotasyon çizelgesi yok**: hangi credential ne sıklıkla döndürülecek, kim onaylayacak, eski anahtar ne kadar tutulacak ADR-015 §2.2g'de usul olarak var ama **envanter üzerinden uygulanmış değil**; (4) **erişim rolleri tanımsız**: kim hangi credential'a okur/yazar; (5) **geçiş planı yok**: sunucu tarafında gerçek `.env`'lerin durumu ⚠️ VERIFICATION REQUIRED — "diskte yok" bilgisi bu deposun (development worktree) durumudur, üretim sunucusunu kapsamaz; (6) ADR-015 şart 1 hâlâ açık: **9 dosya** ham `getenv`/`$_ENV` ile okuyor. Sonuç: "credential nerede?" sorusunun tek cevabı yok; Vault tasarımı (`vault-secrets.md`) ise cevabı üretmeden 2/9'da bekliyor. Bu ADR **envanteri tek çatıya alır ve geçişi fazlara böler**.

### 1.3 İlgili web araştırması

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "secret sprawl statistics credentials management single source of truth 2025" · (2) "12-factor config env variables vs secrets manager .env production" · (3) "HashiCorp Vault brownfield migration AppRole agent injection path design" · (4) "AWS Secrets Manager migration from env files rotation best practices" · (5) "secret rotation policy access roles least privilege emergency break-glass" |
| Web Search **Konusu** | Credential normalizasyonunun beş karar ekseninin güncel ekosistem kanıtı: neden tek SSOT gerekli (secret sprawl), `.env`'nin yeri (12-factor), Vault'a kahverengi-alan (brownfield) geçiş, yönetilen KMS'e geçiş usulü ve rotasyon/erişim/acil erişim politikası. |
| Web Search **Bağlam** | 2025-2026 verisi okundu: OWASP Secrets Management Cheat Sheet + Wiz + HashiCorp (sprawl ve SSOT) · 12factor.net/config + Doppler + dev.to + EnvManager + OneUptime (`.env` vs secrets manager) · developer.hashicorp.com (Vault Agent/AppRole brownfield) + Medium + HashiCorp Discuss + sjramblings.io + groups.google.com + developer.skao.int (Vault yolları ve devreye alma) · AWS migration blog (2. bölüm) + Akeyless + aws.plainenglish.io + systemshardening.com + nhimg.org (yönetilen servise geçiş) · bastion.tech + Aembit + Xygeni + Codacy + HN + r/devops + Akeyless (rotasyon, rol, acil erişim). Protokol: `[[.claude/skills/prompt-maker/references/10-web-research-protocol.md]]` (diskte VAR ✓) — resmi/anahtar kaynak önce, her ana iddia ≥2 bağımsız çapraz kaynak, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`. |
| Web Search **Kısa Açıklama** | Secret sprawl: credential'lar repo, `.env`, CI, ticket ve chat'e dağılınca sahiplik ve rotasyon imkânsızlaşır → tek SSOT şart. 12-factor: yapılandırma ortamdan okunur, **ama secret'lar repoya/görsellere girmez**; `.env` geliştirme içindir, üretimde yönetilen depo/secret manager hedeflenir. Vault brownfield: mevcut `.env`'ler korunarak **dual-read** ile geçilir, AppRole/agent injection ile uygulama kimlik doğrular. Rotasyon: periyot + otomatik dağıtım + eski anahtar emeklilik penceresi olmadan yazılan politika uygulanamaz; en az yetki ve break-glass acil yol politikanın parçasıdır. |
| Web Search **Uzun Açıklama** | **(1) Sprawl/SSOT:** OWASP Cheat Sheet credential'ların tek bir yönetim noktasında toplanmasını, envanter + sahiplik + rotasyon + denetim izinin bir arada olmasını şart koşar; Wiz ve HashiCorp depo-içi secret yoğunluğunun ve "hangi secret nerede" bilinmezliğinin başlıca sızıntı kaynağı olduğunu yazıyor; StrongDM/Keeper/Cycode aynı hükmü bağımsız tekrarlar → **tek SSOT + envanter** (7 kaynak). **(2) `.env` vs manager:** 12factor.net/config yapılandırmanın ortam değişkeniyle taşınmasını, secret'ların kod/görsel versiyonuna girmemesini öngörür; Doppler/EnvManager/OneUptime üretimde `.env` dosyasını "gizli ama dağınık" çözüm olarak tanımlar (seed, drift, sunucu-özel kopya riski); dev.to "from .env to Vault" anlatısı dual-credential ve "başarısızlıkta eskisini tutma" adımlarını yazıyor → **`.env` geçici katman, hizal hedef değil** (5 kaynak). **(3) Brownfield Vault:** developer.hashicorp.com Vault Agent/AppRole rehberi mevcut uygulamalarda `VAULT_ADDR` gibi env değişkeniyle **aynı kapıyı** kullanmayı, AppRole ile kimlik doğrulamayı ve agent'ın render ettiği dosyayı uygulamaya vermeyi anlatır; Medium/discuss/sjramblings/groups/skao path tasarımı (`kv/data/<service>/<category>`) ve devreye alma sırasını tekrarlar → **geçiş, uygulama kodunu büyük ölçüde değiştirmeden yapılır** (6 kaynak). **(4) Yönetilen servis geçişi:** AWS blog (kısım 2) ve Akeyless migration, envanter çıkarma → dual-read → doğrulama → eskiyi kapatma sırasını; aws.plainenglish.io ve systemshardening.com "önce envanter, sonra kademeli" uyarısını; nhimg.org geri dönüşümlülüğü yazar → **fazlı geçiş + eskiyi bir süre tutma** (5 kaynak). **(5) Rotasyon/rol/acil erişim:** bastion.tech, Aembit, Xygeni, Codacy rotasyonun periyot + otomatik güncelleme + hizmet-bazlı en az yetki ile yazılmasını; HN 12-factor thread'i ve r/devops, env-tabanlı sistemlerin "rotation yazmak kolay, uygulamak zor" dersini; Akeyless erişim-önceliği ve break-glass akışını tekrarlar → **rotasyon çizelmesi + rol matrisi + kırık-erişim yolu** (7 kaynak). |
| Web Search **Paragraf Veri Uzun** | 5 paragraf, **30 adlandırılmış kaynak, 5 sorgu**: secret sprawl/SSOT (7), `.env` vs manager (5), brownfield Vault (6), yönetilen servis geçişi (5), rotasyon/rol/acil erişim (7). Çapraz doğrulama ≥2 bağımsız kaynak beş eksenin beşinde karşılanır; "üretim kodunda secret yok" ve "0 `.env`" iddiaları web'e değil depo taramasına dayanır (§1.1-A/D) ve **bu deposun kapsamıyla** sınırlıdır (üretim sunucusu ⚠️ VERIFICATION REQUIRED). |
| Web Search **Sonucu** | (a) **Tek SSOT + envanter doğrulandı** (7 kaynak): dağınık credential sahiplik/rotasyon/denetim imkânsızlaştırır; (b) **`.env` geçici katman** doğrulandı (5 kaynak): 12-factor yapılandırmayı env'e taşır ama secret'ı repodan ve tek dosyadan ayırır; (c) **brownfield geçiş uygulama kodunu bozmadan yapılır** doğrulandı (6 kaynak): aynı env kapısı + AppRole/agent + dual-read; (d) **fazlı geçiş + eskiyi tutma** doğrulandı (5 kaynak): envanter → dual-read → doğrula → kapat; (e) **rotasyon + rol + break-glass** doğrulandı (7 kaynak): periyot ve acil yol olmadan politika kâğıtta kalır. |
| Web Search **Alınan Karar** | **(a) Tek SSOT credential deposu** — credential tanımı ve sahipliği tek yerde (bugün `ConfigManager` + `.env.example`; hizal Vault/KMS); **(b) `service.category.name` ad alanı** — tüm credential adları tek desende, `DB_NAME`/`DB_AUTH_NAME` drift'i dahil normalize edilir; **(c) rotasyon ADR-015 §2.2g'ye bağlanır** — periyot/envanter/sahip bu ADR'nin §2.4-e tablosunda; **(d) erişim rolleri (en az yetki)** — okur/yazar/onaylayıcı/acil erişim dört rolü; **(e) `.env` → config service → Vault/KMS üç fazlı geçiş** — her fazın giriş/çıkış kriteri yazılı, dual-read + eskiyi tutma kuralı zorunlu; **(f) in-code secret-scan CI kapısı** — mevcut `secret-scan.yml` politika genişletmesiyle kilitlenir. |
| Web Search **Sonuç** | Karar beş eksende de 2025-2026 verisiyle **desteklendi** (30 kaynak / 5 sorgu). Kod tarafı aynı resmi verdi: sızıntı yüzeyi düşük (0 `.env`, üretimde 0 hardcoded secret, gitleaks aktif) ama **kavramsal dağılım ve geçişsizlik** var (5 ayrı credential yeri, 2 adlı drift, 9 dosyada ham okuma, rotasyon/rol/geçiş yazısız) → bu ADR **kural + ad alanı + faz planı** kararıdır; uygulama §5.2 parçalarına bağlıdır. |

**Kaynak listesi (30):** 1) owasp.org — Secrets Management Cheat Sheet · 2) wiz.io — secret sprawl / cloud secret risk · 3) hashicorp.com — secret sprawl makalesi · 4) strongdm.com — secrets management guide · 5) keepersecurity.com — secrets management · 6) cycode.com — secret sprawl · 7) infisical.com — secrets management platform · 8) 12factor.net/config — Store config in the environment · 9) doppler.com — .env vs secrets manager · 10) dev.to — from .env to Vault (dual-credential, keep-old-on-failure) · 11) envmanager (docs) — env secret yönetimi · 12) oneuptime.com — configuration/secrets best practices · 13) developer.hashicorp.com — Vault Agent & AppRole (brownfield) · 14) medium.com — Vault path tasarımı · 15) discuss.hashicorp.com — KV v2 path tartışmaları · 16) sjramblings.io — Vault devreye alma notları · 17) groups.google.com — service/host/scope yolları · 18) developer.skao.int — config/secret yönetimi · 19) aws.amazon.com/blogs — Secrets Manager migration (part 2) · 20) akeyless.io — migration guide · 21) aws.plainenglish.io — env → secrets manager geçişi · 22) systemshardening.com — secrets hardening · 23) nhimg.org — secrets migration paper · 24) bastion.tech — rotation policy · 25) aembit.io — workload identity & least privilege · 26) xygeni.io — secret rotation · 27) codacy.com — secret scanning & rotation · 28) news.ycombinator.com — 12-factor config thread · 29) reddit.com/r/devops — env-based systems rotation · 30) akeyless.io — break-glass / emergency access.

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Frozen ADR 001-037 dokunulmaz | Bu ADR yeni dosyadır (metni frozen değildir); frozen ADR'ler okunur/referanslanır, düzenlenmez (AGENTS.md §25.3 kural 2) |
| REDACTED | Bu ADR'ye ve tüm credential defterine **gerçek secret değeri yazılmaz** — yalnız dosya:satır, anahtar **adı**, uzunluk ve sayım |
| ADR-015 hizası | Rotasyon usulü (§2.2g) ve tek-giriş-noktası şartı (şart 1/3) bağlayıcıdır; bu ADR onu **envanter + geçiş** ile tamamlar, çelişmez |
| Üretim sunucusu kapsam dışı (⚠️ VERIFICATION REQUIRED) | "0 `.env`" bilgisi bu worktree içindir; sunucu tarafı `.env` envanteri ayrı doğrulama gerektirir (§5.2/1) |
| In-Place Refactoring | Dosya adları onaysız değiştirilmez; `DB_NAME`/`DB_AUTH_NAME` drift'i **yeni adlandırma kuralıyla** uyumlu hâle getirilir, mevcut anahtarlar anında kırılmaz (grandfathered) |
| CI gate mevcut, yeniden kurulmaz | `secret-scan.yml` zaten var (§1.1-F); bu ADR yalnız politika genişletmesi yazar (`.gitleaks.toml` ⚠️ YOK) |
| Vault tek-nokta riski | Vault/KMS'e geçiş, kırık-erişim (break-glass) yolu olmadan tamamlanmaz (§4.1-R5) |
| In-Place Refactoring + Guardrail #16 | Şablon: `[[.ai/.templates/adr/adr-template.md]]` (7 bölüm + §1.3 9 alan) |

---

## 2. Karar

**CoreMusic credential envanteri tek çatı altında normalleştirilir: (a) TEK SSOT credential deposu** — credential'ın tanımı, sahibi ve değeri tek bir yerde yaşar (bugün `ConfigManager` + `.env.example`; hedef Vault/KMS), ikinci bir kayıt açılmaz; **(b) tutarlı adlandırma alanı `service.category.name`** — tüm credential adları bu desende, mevcut `DB_NAME`/`DB_AUTH_NAME` drift'i dahil normalize edilir; **(c) rotasyon ADR-015 §2.2g'ye bağlanır** — periyot, sahip ve eski-anahtar emeklilik penceresi envanterle zorlanır; **(d) erişim rolleri (en az yetki)** — okur / yazar / onaylayıcı / acil erişim dört rolü; **(e) `.env` → config service → Vault/KMS üç fazlı geçiş planı** — her fazın giriş/çıkış kriteri yazılı, dual-read + eskiyi tutma kuralı zorunlu; **(f) in-code secret-scan CI kapısı** — mevcut `secret-scan.yml` politikayla kilitlenir.**

### 2.1 Gerekçe 1 — Sızıntı yüzeyi düşük, kavramsal dağılım yüksek: kural konmazsa Vault tasarımı cevapsız kalır

Depo kanıtı rahatlatıcı (0 `.env`, üretimde 0 hardcoded secret, gitleaks aktif) ama credential **kavramı** beş yerde yaşıyor: `SENSITIVE_KEYS` (`ConfigManager.php:10-15`), iki `.env.example` (18/13 anahtar), `oauth-platforms.php:211-251` (20 env-adı), `keys.md` (keyword haritası), `vault-secrets.md` (Vault tasarımı, 2/9). Hiçbiri diğerini SSOT ilan etmiyor → "credential nerede?" sorusunun tek cevabı yok. §1.3 kaynak 1-7 (OWASP dahil) aynı hükmü veriyor: envanter + sahiplik + rotasyon + denetim izi **tek yönetim noktasında** olmazsa secret sprawl büyür → bu ADR o noktayı yazar; Vault (`vault-secrets.md`) ise **onun üzerine** kurulacak katmandır, cevabın kendisi değil.

### 2.2 Gerekçe 2 — Taşıyıcı hazır, kapı ADR-015 ile aynı: geçiş uygulamayı kırmadan yapılabilir

Config service fiilen mevcut: `getSecure`/`maskSecret`/`filterSensitive` + arayüz + testler (§1.1-B), OAuth zaten env-**adı** ile referans veriyor (§1.1-E). §1.3 kaynak 13-18 (developer.hashicorp.com dâhil) brownfield Vault'ın **aynı env kapısı + AppRole + dual-read** ile kurulduğunu yazıyor → geçiş için uygulama kodunu yeniden yazmaya gerek yok; yalnız ham okumalar (9 dosya, §1.1-C) ConfigManager'a taşınıyor. Bu aynı zamanda ADR-015 şart 1'in kapanışıdır → iki ADR **tek kapıda** birleşir (ADR-015 §2.2g rotasyon usulü de envanterle uygulanabilir hâle gelir).

### 2.3 Gerekçe 3 — CI kapısı zaten var: politika yazmak, sistem kurmaktan ucuz

`secret-scan.yml` çalışıyor (gitleaks, `fetch-depth: 0`, push+PR) ve üretim kodunda 8 desen 0 (§1.1-D/F). §1.3 kaynak 24-30 rotasyonun "periyot + otomatik dağıtım + eski anahtar emekliliği" ve en az yetki/olası kırık-erişim olmadan uygulanamaz olduğunu yazıyor; kaynak 19-23 ise fazlı geçişte "önce envanter → dual-read → doğrula → kapat" sırasını tekrarlar → karar: **küçük ve fazlı** — bugünden Vault kurmak yerine kural + ad alanı + envanter + kapıyı yazmak, Vault'u 3. faza (opsiyonel) bırakmak. Böylece PLANNED iş kalemleri gerçek sahiplikle sıralanır.

### 2.4 Teknik Detaylar

**(a) Altı karar maddesi:**

| # | Madde | Hükmün anlamı |
|---|-------|----------------|
| a1 | **Tek SSOT deposu** | Credential'ın tanımı/sahibi/değeri tek yerde; bugün `ConfigManager` (`SENSITIVE_KEYS`, `getSecure`, `maskSecret`) + `.env.example` (şabloon); ikinci kayıt = ihlal |
| a2 | **`service.category.name` ad alanı** | Tüm credential adları bu desende; mevcut SNAKE_CASE env-adları desene bağlanır, `DB_NAME`↔`DB_AUTH_NAME` drift'i normalize edilir (grandfathered) |
| a3 | **Rotasyon = ADR-015 §2.2g** | Periyot + sahip + eski-anahtar emeklilik penceresi envanter tablosunda (§2.4-e) |
| a4 | **Erişim rolleri** | Okur / yazar / onaylayıcı / acil erişim — en az yetki (§2.4-d) |
| a5 | **Üç fazlı geçiş** | Faz 1 → Faz 2 → Faz 3, her birinin giriş/çıkış kriteri var (§2.4-c); dual-read + eskiyi tutma zorunlu |
| a6 | **CI secret-scan kapısı** | `secret-scan.yml` + `.gitleaks.toml` (⚠️ YOK → PLANNED) politika genişletmesi (§2.4-f) |

**(b) Adlandırma alanı + mevcut envanterin normalizasyonu:**

| Desen kuralı | Değer |
|--------------|-------|
| Kanonik ad | `service.category.name` → örn. `auth.database.name`, `shared.oauth.platform.client_secret` (nokta ayrımcı; `SNAKE_CASE` env-adları `.` → `_` çevirimiyle eşlenir) |
| Değer asla kodda | `oauth-platforms.php:208` hükmü genel kural olur: kodda yalnız **ad**, değer depoda (a1) |
| Drift düzeltmesi (grandfathered) | `DB_NAME` (shared) ↔ `DB_AUTH_NAME` (auth): yeni ad `shared.database.name` / `auth.database.name`; **eski anahtarlar en az bir faz boyunca okunmaya devam eder**, yalnızca yeni şablonlarda yeni ad zorunlu |
| `.env.example` tekilleştirme | 18 + 13 anahtar tek envanter tablosunda birleşir (28 benzersiz değil — 6 shared-özel + 1 auth-özel; §1.1-I) |
| Anahtar adı ≠ secret | Anahtar **adı** ve uzunluk yazılır; değer hiçbir vault dosyasına girmez (REDACTED) |

**(c) Üç fazlı geçiş planı — giriş/çıkış kriterleri:**

| Faz | Bugünkü durum / hedef | Giriş kriteri | Çıkış kriteri | Durum |
|-----|----------------------|---------------|---------------|-------|
| **Faz 1** | **Bugün:** `.env` + dosya tabanlı config; taşıyıcı `ConfigManager`; 2 `.env.example` şablon | `.env.example`'lar + `SENSITIVE_KEYS` tek envanter tablosunda birleşir (§2.4-b); ad alanı kuralı yazılır | Envanter tam (kayıpsız), ad-deseni tüm şablonlarda uygulanır, ham `getenv`/`$_ENV` envanteri çıkar | **IMPLEMENTED çekirdek / PLANNED tablo** |
| **Faz 2** | **Sunucu tarafı config servisi** — uygulama **env değişkeni üzerinden** okur (ADR-015 ile **aynı kapı**: şart 3) | Faz 1 çıkış kriteri + sunucu `.env` envanteri doğrulanır (⚠️ §5.2/1) | Uygulama credential'ı **yalnız** ConfigManager üzerinden okur (ADR-015 şart 1 kapanır: 9 dosyadaki ham okuma 0'a iner); dual-read ile eski `.env` hâlâ çalışır | PLANNED |
| **Faz 3** | **HashiCorp Vault / KMS** (opsiyonel — `vault-secrets.md`, 2/9) | Faz 2 çıkış kriteri + rotasyon çizelmesi yürürlükte + break-glass yolu test edilmiş | AppRole/agent ile credential render'ı doğrulanır, `.env` kapanır (eski okuma penceresi biter), rotasyon otomasyonu çalışır | PLANNED (opsiyonel) |

*Faz 3 opsiyondur: Faz 2 çıktıktan sonra bile envanter + ad alanı + rotasyon anlamlıdır (12-factor ruhu §1.3 kaynak 8-12).*

**(d) Erişim rolleri (en az yetki):**

| Rol | Ne yapabilir | Tipik sahip | Kısıt |
|-----|--------------|-------------|-------|
| **Okur** | Credential'ı okur (maskeli görüntü: `maskSecret`) | uygulama (runtime) | değer dökümü yasak; log'da `[REDACTED]` |
| **Yazar** | Değer üretir/günceller | Security Engineer + Vault Steward | tek yazıcı (ADR-003 ruhu) |
| **Onaylayıcı** | Rotasyon/onay verir | Tech Lead (üretim) | 4 göz kuralı: yazar ≠ onaylayıcı |
| **Acil erişim (break-glass)** | Kilitli kalanda geçici erişim | Vault Steward + sistem yöneticisi | her kullanım `log.md`'ye yazılır; olay sonrası rotasyon tetiklenir |

**(e) Rotasyon çizelmesi (ADR-015 §2.2g hizalı — envanterle uygulanır):**

| Sınıf | Periyot (öneri — debate ile sabitlenir) | Sahip | Eski anahtar |
|-------|------------------------------------------|-------|--------------|
| Veritabanı parolası (`*.database.*`) | 90 gün | Data Engineer + Vault Steward | 7 gün emeklilik penceresi (dual-credential) |
| OAuth client_secret (`*.oauth.*`) | sağlayıcı politikasına göre (≥ 180 gün) | Security Engineer | provider kabul edene kadar ikisi de geçerli |
| API key (`*.api.*` — ADR-020) | 180 gün | Security Engineer | 7 gün |
| Session/imzalama anahtarı (ADR-011) | olay tetikli + yıllık | Security Engineer | oturum-kırma riskine göre değerlendirilir |
| CI secret (GitHub `secrets.*`) | personel/rol değişiminde + yıllık | DevOps Engineer | repo geçmişi taraması (gitleaks) kapıda |

*Periyotlar **öneridir** (kanıt: §1.3 kaynak 24-30); debate KABUL'ü ile bağlayıcılaştırıldı — rotasyon faz planı §6.2/3 şartıdır.*

**(f) CI secret-scan kapısı (mevcut dosya genişletilir, yenisi kurulmaz):**

1. Mevcut: `.github/workflows/secret-scan.yml` → gitleaks v2, `fetch-depth: 0`, push + PR (IMPLEMENTED ✅).
2. Eklenecek: `.gitleaks.toml` ⚠️ YOK (dosya `:4` yorumunda da doğrulanmış) → allowlist yalnız **test dosyaları** için (`LoginRequestTest.php` sahte şifreleri, §1.1-D); üretime özgü allowlist yasak.
3. Politika: yeni credential yalnız adıyla (a2) yazılır; desen dışı ad PR'da review kapısına takılır; test-sözleşmesi istisnası gerekçesiyle ve dosya adıyla sınırlıdır.
4. Eşik: gitleaks bulgusu = PR engeli (mevcut davranış korunur); allowlist ihlali = `.gitleaks.toml`'a geri dönüş (R6).

---

## 3. Alternatifler

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Statü quo — kural yazma**, credential dağınık kalsın (`SENSITIVE_KEYS` + 2 örnek + `oauth-platforms.php` + `keys.md` yan yana) | Sıfır çaba; sızıntı yüzeyi zaten düşük | "Credential nerede?" sorusunun cevabı yok; `DB_NAME`/`DB_AUTH_NAME` gibi driftler çoğalır; rotasyon/rol/geçiş yazılmaz; `vault-secrets.md` 2/9'da kalır | Bugünkü düşük sızıntı şansıdır, kural değil; §1.1-I drift'i zaten somut örnek |
| 2 | **Doğrudan Faz 3'e git — hemen HashiCorp Vault kur** | En güçlü hedef; rotasyon/audit yerleşik | Kümelenmiş operasyon maliyeti + tek-nokta riski; Faz 1-2 eksikken uygulama 9 dosyada ham okumaya devam eder (ADR-015 şart 1 açık); break-glass yoksa kilitlenme riski | §1.3 kaynak 19-23 kademeli geçişi şart koşar; `vault-secrets.md` 2/9 iken Vault tek başına cevap değil (R5) |
| 3 | **Yönetilen servis (AWS Secrets Manager / Akeyless) kullan** | Operasyon yükü az, entegrasyon olgun | Vendor lock-in + ek bulut bağımlılığı; mevcut self-host mimariyle (k6-guvenlik) uyumsuz maliyet; Faz 1-2'yi değiştirmez | Kapı açık bırakılır: Faz 3'te Vault **veya** yönetilen servis karşılaştırma ile seçilir (bu ADR tarafsız) |
| 4 | **Adlandırmayı standartlaştırma, yalnız `.env`'yi tekilleştir** | En küçük iş | Ad alanı olmadan ikinci envanter tekrar dağılır; `oauth-platforms.php` deseniyle `.env` deseni çatışmaya devam eder | §1.3 kaynak 1-7 "tek depo + tutarlı ad" der; yalnız dosya birleştirmek ad çatışmasını çözmez (a2 şart) |
| 5 | **Rotasyonu tamamen otomatikleştir (şimdi)** | İnsan hatası azalır | Uygulama 9 dosyada ham okurken otomatik döndürme → gece kesintisi; dual-credential altyapısı yok | §1.3 kaynak 24-30 + dev.to: eskiyi tutma penceresi olmadan otomasyon kesinti üretir → rotasyon, Faz 2 exit kriterine bağlanır (e) |

---

## 4. Sonuç ve Sonuçlar

**Olumlu:**

- **Tek cevap**: "credential nerede, sahibi kim, ne sıklıkla döner?" sorusu envanter tablosundan okunur; `vault-secrets.md` (2/9) sonunda üzerine kurulacak net bir zemin bulur.
- **Üretim kodu zaten temiz** (0 hardcoded secret / 8 desen, 0 `.env`) → normalizasyon **temizlik değil, kural yazma** işidir; riski düşük, kazancı kalıcı.
- **ADR-015 şart 1 tek iş kalemiyle kapanır**: 9 dosyadaki ham `getenv`/`$_ENV` → ConfigManager; iki ADR tek kapıda birleşir, çelişki doğmaz.
- **Kapı hazır**: `secret-scan.yml` çalışıyor; politika + `.gitleaks.toml` eklemesi düşük maliyetli (§2.4-f).
- **Fazlar ayrıştırıldı**: Vault/KMS opsiyonel bırakıldı → Faz 2 bile tek başına anlamlı (12-factor), kilitlenme yok.

**Olumsuz:**

- **Envanter ve geçiş gerçek iş**: sunucu `.env` doğrulaması (⚠️), 9 dosya taşıma, `DB_NAME` drift normalizasyonu, `.gitleaks.toml` — hepsi sahiplik gerektirir (§5.2).
- **Grandfathered geçiş dönemi**: eski anahtar adları bir süre okunmaya devam eder → iki adlı dönem (drift'in kendisi) bir faz boyunca sürer; kural yeni şablonlarda geçerli.
- **Rotasyon çizelmesi bağlandı, uygulanmadı** (§2.4-e periyotları debate'de KABUL edildi; uygulama §6.2/3 şartı) → şart yerine gelene kadar rotasyon yine manueldir.

**Nötr:**

- Vault vs yönetilen servis seçimi bu ADR'de sabitlenmedi (§3 #3): Faz 3'te `vault-secrets.md` + maliyet/operasyon karşılaştırmasıyla karar verilir.
- `home.coremusic.net` `.env.example`'sız yükleme davranışı (`constants.php:11-13`) düzeltilmez, **envanter satırına** yazılır (şablon üretimi §5.2/4 kalemidir).

### 4.1 Risk → Fallback matrisi

| # | Risk | Olasılık | Etki | Mitigasyon | Fallback (geri çekilme yolu) |
|---|------|---------|------|------------|------------------------------|
| R1 | **Secret sprawl geri döner** — yeni credential ikinci bir yere (kod, ticket, chat) yazılır | Olası (3) | Yüksek (4) | a1 tek depo + a2 ad alanı + §2.4-f CI kapısı (PR engeli) + inceleme sorusu "bu secret nerede yaşıyor?" | Kapı geçici kapatılır (CI engeli kaldırılmaz), ihlal `log.md`'ye yazılır; credential hemen depoya taşınır ve kaynak silinir |
| R2 | **Sızan secret** — geçmişte bir credential'a dair değer yakalanır (CI taraması bulgusu veya rapor) | Mümkün (2) | Yüksek (4) | gitleaks push+PR (`fetch-depth: 0` = tüm geçmiş); 8 desen taraması periyodik tekrarlanır | Değer **anında rotasyon** (e çizelmesi), etkilenen sınıf dual-credential penceresine alınır; bulgu `log.md`'ye dosya:satır olarak yazılır (değer yazılmaz) |
| R3 | **Geçiş kesintisi** — Faz 2'de uygulama credential'ı okuyamaz, servis düşer | Mümkün (2) | Yüksek (4) | dual-read (eski `.env` hâlâ okunur) + "başarısızlıkta eskisini tut" (§1.3 kaynak 10) + exit kriteri = ham okuma 0 **ve** doğrulama testi yeşil | Faz 2 geri alınır: uygulama `.env`'ye döner (dual-read sayesinde kod değişikliği gerekmez); depo değişmez |
| R4 | **Vault/KMS tek nokta** — Faz 3'te depo inerse tüm servisler credential'sız kalır | Mümkün (2) | Yüksek (4) | Faz 3 = opsinyonel + çıkış kriteri: rotasyon çizelmesi yürürlükte **ve** break-glass test edilmiş; Vault cluster `vault-secrets.md:332-337` (PLANNED) | Uygulama `.env`/config service'e (Faz 2 durumu) döner; depo kendini onarana kadar dual-read açık kalır |
| R5 | **Kırık erişim (break-glass)** — Vault/anahtar erişilemez, operatör kilitlenir, acil onarım gecikir | Mümkün (2) | Yüksek (4) | a4 acil-erişim rolü + zorunlu kırık-erişim testi Faz 3 exit kriteridir; her break-glass kullanımı `log.md`'ye yazılır | Kırık-erişim zarfı (yerel, salt-okunur, iki kişi erişimi) devreye girer; kullanım sonrası **zorunlu rotasyon** tetiklenir; zarf yoksa Faz 3 askıya alınır |
| R6 | **Kapı yanlışlıkla genişler** — `.gitleaks.toml` allowlist'i üretimi de kapsayacak şekilde şişer, tarama anlamsızlaşır | Olası (3) | Orta (3) | Allowlist yalnız test dosyası adıyla ve gerekçeli (§2.4-f/3); allowlist değişikliği inceleme kapısı | Allowlist sıfırlanır (cihaz-dışı tek satır), kapı varsayılana döner; bulgu → CI engeli geri gelir |
| R7 | **Ad alanı geçişi kırar** — `service.category.name` uygulanınca eski anahtar okunamaz | Olası (3) | Orta (3) | Grandfathered: eski adlar bir faz boyunca okunur (a2/b); yeni ad yalnız yeni şablonlarda zorunlu | Eski ad yeniden eklenir (dual-read), geçiş Faz 2 exit kriterine ertelenir; kural metni değişmez |

---

## 5. İlgili Kararlar

### 5.1 Wiki-linkler (diskte doğrulandı — hepsi VAR ✅)

- `[[.ai/.decisions/accepted/ADR-015-env-parser-strategy.md]]` → **ana ortak**: rotasyon usulü §2.2g, şart 1 (tek giriş noktası — §1.1-C açık), şart 3 (config servisi = Faz 2 kapısı) (§1.4, §2.1, §2.4-c/e).
- `[[.ai/.decisions/accepted/ADR-011-session-management.md]]` → session/imzalama anahtarı rotasyon sınıfı (§2.4-e).
- `[[.ai/.decisions/accepted/ADR-020-api-public-security.md]]` → API key sınıfı (`*.api.*`, §2.4-e).
- `[[.ai/.decisions/accepted/ADR-022-database-hardened-security.md]]` → şifreleme/audit zemini; `log.md` `[REDACTED]` maskelemesi (§1.4, §2.4-d).
- `[[.ai/.decisions/accepted/ADR-033-sql-normalization-strategy.md]]` → biçim referansı + aynı numara-boşluğu-doldurma usulü (§6 numaralandırma notu).
- `[[.ai/.decisions/index.md]]` → karar dizini; `ADR-034-credential-vault-normalization` satırı **diskte mevcut** (`:71`) — kayıt satırı ayrı işlemdir; başlık varyantı §1.1-I.
- `[[.ai/.templates/adr/adr-template.md]]` → 7 bölüm + §1.3 9 alan iskeleti (Guardrail #16).
- `[[.claude/skills/prompt-maker/references/10-web-research-protocol.md]]` → §1.3 web araştırma protokolü (diskte VAR ✓).
- `[[.ai/architecture/k6-guvenlik/vault-secrets.md]]` → **HashiCorp Vault tasarımı** (9627 bayt, 2/9; `:332-337` cluster/AppRole/rotasyon/erişim PLANNED) → bu ADR'nin Faz 3 zemini (§2.4-c, §4.1-R4).
- `[[.ai/architecture/k6-guvenlik/README.md]]` → k6 güvenlik katmanı indeksi; ADR-034 slotu `:43,153,390,405`.
- `[[.ai/keys.md]]` → `credential vault, secret` keyword satırı `:64`/`:269` (SSOT keyword haritası; `:317` hedefi eksik ⚠️ §1.1-G).
- `[[.ai/brain.md]]` → ADR-034 özeti satırı `:989` (başlık varyantı §1.1-I).
- `[[.ai/index.md]]` → master katalog `:651` ADR-034 kaydı.
- `[[.ai/WORKFLOW.md]]` → süreç kaydı `:524`.
- `[[.ai/log.md]]` → audit trail (append-only; bu ADR'nin 1 satırlık kaydı).
- `[[.ai/CLAUDE.md]]` → ana sözleşme; REDACTED + guardrail kaynakları.

### 5.2 Karar parçaları (appendix)

| # | Parçanın adı | Sahibi | Son |
|---|--------------|--------|-----|
| 1 | **Sunucu `.env` envanteri** — üretim/sunucu tarafındaki gerçek credential dosyalarının doğrulanması (yalnız ad + dosya:satır; değer yazılmaz) | Security Engineer + DevOps Engineer | §1.4 kapsam dışı uyarısı, Faz 2 giriş kriteri |
| 2 | **Ham okuma kapatma** — 9 dosyadaki `getenv`/`$_ENV` (6 + 10 satır) ConfigManager'a taşınır → ADR-015 şart 1 kapanır | Backend Architect | §1.1-C, §2.4-c Faz 2 exit |
| 3 | **Envanter tablosu** — 18 + 13 `.env.example` anahtarı + `SENSITIVE_KEYS` + 20 OAuth env-adı tek listede; sahip + sınıf + periyot (§2.4-e) | Security Engineer | §2.4-a1/b, §4.1-R1 |
| 4 | **Ad alanı geçişi** — `DB_NAME`↔`DB_AUTH_NAME` drift'i + `home` `.env.example` şablonu; grandfathered dual-okuma | Backend Architect + Data Engineer | §2.4-b, §4.1-R7 |
| 5 | **`.gitleaks.toml` + test istisnası** — allowlist yalnız `LoginRequestTest.php` (5 sahte şifre) için, gerekçeli | DevOps Engineer + QA Engineer | §2.4-f, §4.1-R6 |
| 6 | **Rotasyon çizelmesinin bağlayıcılaştırılması** — §2.4-e periyotları debate'de onaylanır; ADR-015 §2.2g usulüne bağlanır | Security Engineer + Vault Steward | §2.4-e, §6 |
| 7 | **Break-glass zarfı** — acil erişim yolu (iki kişi) + kullanım kaydı + zorunlu sonraki rotasyon | Vault Steward + DevOps Engineer | §2.4-d, §4.1-R5 |
| 8 | **Faz 3 karşılaştırması** — Vault vs yönetilen servis (maliyet/operasyon/lock-in) → `vault-secrets.md` 2/9 satırlarının ilerletilmesi | Security Engineer + DevOps Engineer | §2.4-c, §3 #3 |
| 9 | **Debate turu + Tech Lead onayı** (§6) | Vault Steward + Tech Lead | §6, §7 |
| 10 | **Şart 1a** — `.env` şeması + fail-fast doğrulama: `home.coremusic.net/config/.env.example` şablonu + ADR-015 parser ile açılışta şema denetimi | Security Engineer + Backend Architect | §6.2/1a · §5.2/4 |
| 11 | **Şart 1b** — `getSecure` tek giriş noktası: 9 dosyadaki ham `getenv`/`$_ENV` config servisine taşınır, servis dışı ham okuma yasak (ADR-015 şart 1) | Backend Architect | §6.2/1b · §5.2/2 |
| 12 | **Şart 2** — `.gitleaks.toml` yapılandırması + eşik: allowlist yalnız test dosyası, gerekçeli | DevOps Engineer + QA Engineer | §6.2/2 · §5.2/5 |
| 13 | **Şart 3** — rotasyon faz planı: §2.4-e çizelmesi ADR-015 §2.2g takvimine bağlanır | Security Engineer + Vault Steward | §6.2/3 · §5.2/6 |

### 5.3 Çapraz referans matrisi (kaynak → bölüm → durum)

| Kaynak | Kullanıldığı bölüm | İlişki | Disk kanıtı |
|--------|--------------------|--------|-------------|
| `[[.ai/.decisions/accepted/ADR-015-env-parser-strategy.md]]` | §1.1-C, §1.4, §2.1, §2.4-c/e | Rotasyon usulü + şart 1/3 — kapı ortağı | ✅ VAR |
| `[[.ai/architecture/k6-guvenlik/vault-secrets.md]]` | §1.1-G, §2.4-c, §4.1-R4 | Vault tasarımı (2/9) = Faz 3 zemini | ✅ VAR (9627 bayt) |
| `.github/workflows/secret-scan.yml` (§5.1'de düz metin) | §1.1-F, §2.3, §2.4-f | Mevcut CI kapısı (938 bayt, gitleaks v2) | ✅ VAR |
| `shared/src/Config/ConfigManager.php` + `EnvParser.php` + `IConfigManager.php` (düz metin) | §1.1-B, §2.1 | Faz 1 taşıyıcısı (getSecure/maskSecret/filterSensitive) | ✅ VAR (+ test dosyası) |
| `shared/config/oauth-platforms.php` (düz metin) | §1.1-E, §2.4-b | 20 env-adı + `:208` "asla hardcoded" hükmü | ✅ VAR |
| `shared/config/.env.example` + `auth.coremusic.net/config/.env.example` (düz metin) | §1.1-A, §1.1-I, §2.4-b | 18 + 13 anahtar, 7 anahtar drift | ✅ VAR (2 dosya) |
| `[[.ai/.decisions/index.md]]`, `[[.ai/index.md]]`, `[[.ai/keys.md]]`, `[[.ai/brain.md]]`, `[[.ai/WORKFLOW.md]]` | §1.1-G, §1.1-I, §5.1 | ADR-034 slotu zaten ayrılmış (10 kayıt satırı) | ✅ VAR |
| `[[.ai/.templates/adr/adr-template.md]]` + web-research-protocol | §6, §7, §1.3 | İskelet + araştırma protokolü | ✅ VAR |
| Web (30 kaynak, §1.3) | §1.3, §2.1-2.3, §3, §4.1 | Güncel ekosistem kanıtı | ✅ 5 sorgu / 30 kaynak |
| ⚠️ Eksik hedefler (wiki-link DEĞİL, düz metin): `architecture/k0-k5-software/k0-os-layer/credential-vault.md` (keys.md:317) · `home.coremusic.net/config/.env.example` · `shared/config/.env.schema` · `EnvSchema.php` · `EnvValidator.php` · `bin/config-doc.php` · `.gitleaks.toml` | §1.1-A/G/I, §2.4-f | Hepsi **diskte YOK** → `⚠️ VERIFICATION REQUIRED` (uydurulmadı, wiki-link yapılmadı) | ❌ YOK (0 dosya) |

---

## 6. Statü ve Debate

- **Status:** `accepted` — karar kapsamı (a) tek SSOT credential deposu · (b) `service.category.name` ad alanı · (c) rotasyon ADR-015 §2.2g hizalı · (d) erişim rolleri (en az yetki) · (e) `.env` → config service → Vault/KMS üç fazlı geçiş · (f) in-code secret-scan CI kapısı · (g) sonuç/risk/fallback) **kullanıcı onaylı** olarak kabul edildi; uygulama kalemleri §5.2 parçalarına bağlıdır.
- **Debate:** `✅ TAMAMLANDI (3 tur / 20 persona — 18/2/0 KABUL)` — kayıt §6.1'de, bağlayıcı şartlar §6.2'de; Tech Lead §7'de `⏳ → ✅` geçişini yaptı (2026-09-25).
- **Frozen ADR'lar (001-037):** bu dosya frozen değildir ve frozen ADR metnine dokunmaz. **Numaralandırma notu (Truth Mode):** genel kural "yeni ADR'ler 088+" der; bu dosya **eski seri ayrılmış numarasına** (ADR-034) yazıldı, çünkü `.ai/.decisions/index.md:71`, `.ai/index.md:651`, `keys.md:64/269`, `brain.md:989`, `WORKFLOW.md:524`, `security-audit.md:20`, `adr-index.md:105`, `adr-security-template.md:159,255,259` ve `k6-guvenlik/README.md:43,153,390,405` bu numarayı **çoktan kayıtlı** tutuyor → numara boş değil, **boşluk dolduruldu** (ADR-033 ile aynı istisna).
- **Başlık varyantı notu:** dizin `Credential Vault Normalization` (esas alınan), `brain.md:989`/`adr-index.md:105`/`k6 README:153` "AES-256-GCM credential vault" (varyant) → düzeltme ayrı vault işlemidir (§1.1-I).
- **§6.1 Debate kaydı:** `✅ TAMAMLANDI` — 3 tur / 20 persona · oy 18/2/0 KABUL · 3 şart / 4 kalem §6.2.

### 6.1 Debate kaydı

**✅ TAMAMLANDI — 3 tur / 20 persona · 2026-09-25 · sonuç 18 kabul / 2 çekimser / 0 red → KABUL**

**Tur 1 — Kod kanıtı + 20 persona taraması:**

- Sunulan kanıt paketi: §1.1 sayım tabloları (0 `.env` disk / 0 git · 2 `.env.example` — shared 18 / auth 13 anahtar · home örnek dosyasız `.env` `constants.php:11-13` · üretim hardcoded 0 / test 5 eşleşme · `getenv` 6 + `$_ENV` 10 satır = 9 dosya ham okuma · config servisi IMPLEMENTED — `SENSITIVE_KEYS:10-15`, `getSecure:42-49`, `maskSecret:56-66` · `oauth-platforms.php:211-251` 20 credential adı/değer yok · `secret-scan.yml` VAR (gitleaks), `.gitleaks.toml` YOK) + §1.3 (5 sorgu / 30 kaynak) + §2.4-c faz matrisi.
- Oy: **15 kabul / nötr · 4 uyarı** — QA Engineer (`.gitleaks.toml` + `.env.schema`) · Critic (home örnek dosyasız şema + ham `$_ENV` bypass şartı).

**Tur 2 — İtiraz → çözüm:**

| # | İtiraz | Çözüm | Şart |
|---|--------|-------|------|
| 1 | home örnek dosyasız `.env` (`constants.php:11-13`) | `.env.example` + fail-fast şema doğrulama (ADR-015 parser) | §6.2/1a |
| 2 | Ham `$_ENV`/`getenv` 9 dosyası config servisi dışında | Tek giriş config servisi `getSecure`; ham okuma yasak | §6.2/1b |
| 3 | `.gitleaks.toml` yok | gitleaks yapılandırması + eşik | §6.2/2 |
| 4 | Rotasyon PLANNED | rotasyon takvimi faz planı (ADR-015 §2.2g hizası) | §6.2/3 |

**Tur 3 — Final oyu:** **18 kabul / 2 çekimser / 0 red → KABUL** (Tech Lead §7 `⏳ → ✅`).

### 6.2 Bağlayıcı şartlar

**✅ 3 şart / 4 kalem — debate KABUL'ünün koşulu; yerine getirilmeden §5.2 uygulama kalemleri (2, 4, 5, 6) kapanmaz.**

| # | Şart | İçerik (kabul ölçütü) | Sahip | Çıktı / referans |
|---|------|------------------------|-------|------------------|
| 1a | `.env` şeması + fail-fast doğrulama | `home.coremusic.net/config/.env.example` üretilir; ADR-015 parser ile açılışta şema doğrulaması (fail-fast); örnek dosyasız yükleme (`constants.php:11-13`) kapanır — Critic'in Tur 1 uyarısı (home örnek şemasız) | Security Engineer + Backend Architect | §1.1-A, §4 · §5.2/4 + §5.2/10 |
| 1b | `getSecure` tek giriş noktası | 9 dosyadaki ham `getenv`/`$_ENV` (6 + 10 satır) config servisine taşınır; config servisi dışında ham env okuma yasak → ADR-015 şart 1 kapanır — Critic'in Tur 1 uyarısı (ham $_ENV bypass) | Backend Architect | §1.1-C, §2.4-c Faz 2 exit · §5.2/2 + §5.2/11 |
| 2 | gitleaks yapılandırması + eşik | `.gitleaks.toml` yazılır (allowlist yalnız `LoginRequestTest.php` sahte şifreleri, gerekçeli); bulgu = PR engeli, eşik `secret-scan.yml` ile kilitlenir — QA Engineer Tur 1 uyarısı | DevOps Engineer + QA Engineer | §2.4-f, §4.1-R6 · §5.2/5 + §5.2/12 |
| 3 | Rotasyon faz planı | §2.4-e çizelmesi ADR-015 §2.2g usulüyle faz planına bağlanır (periyot + sahip + emeklilik penceresi takvimde) — Tur 2/4 itirazının çözümü (rotasyon PLANNED) | Security Engineer + Vault Steward | §2.4-e, §2.4-c · §5.2/6 + §5.2/13 |

Şart 1, debate sonunda 1a + 1b olarak iki kaleme bölündü (Tech Lead koşulu: `.env` şeması + `getSecure` tek giriş); şart 1'deki iki uyarı Critic'in, şart 2 QA Engineer'ın Tur 1 ikazından doğdu; şart 3 ise rotasyon PLANNED itirazının çözümüdür. Şartlar yerine getirilmediğinde §5.2/2, 4, 5, 6 uygulama kalemleri kapanmaz; §4.1-R1 (sprawl) ve §4.1-R6 (allowlist şişmesi) fallback'leri geçerlidir.

**Statü özeti:** debate `✅ 3/20 (18/2/0 KABUL)` → Tech Lead `✅` → Arch Lead `⏳` → **frozen YOK** (§7 üç satırı tamamlanmadan `frozen` yapılmaz).

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali | 2026-09-25 | ✅ (kullanıcı onaylı karar kapsamı — (a) tek SSOT deposu · (b) `service.category.name` · (c) rotasyon ADR-015 hizalı · (d) erişim rolleri · (e) üç fazlı geçiş · (f) CI secret-scan kapısı · (g) sonuç + risk + fallback) |
| Tech Lead | — | 2026-09-25 | ✅ (debate 3 tur / 20 persona — 18/2/0 KABUL; 3 bağlayıcı şart §6.2) |
| Arch Lead | — | — | ⏳ (Tech Lead sonrası) |

**Debate:** ✅ 3 tur / 20 persona — 18 kabul / 2 çekimser / 0 red → **KABUL**; 3 bağlayıcı şart §6.2. Arch Lead onayı bekleniyor → frozen YOK.

---

**1.0.0 | 2026-09-25 | Created**
*ADR-034 debate | 2026-09-25 | ✅ 3/20 KABUL (18/2/0) → Tech Lead ✅ → Arch Lead ⏳ → frozen YOK*

*Authority: ADR-034 Karar Metni (SSOT)*
*Mode: Red Team · Human Mode · Truth Mode*
