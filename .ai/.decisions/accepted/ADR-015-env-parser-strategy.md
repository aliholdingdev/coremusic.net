---
title: "CoreMusic — ADR-015: Env Parser Strategy (Şemalı Fail-Fast Parser · Tek Sınıf · Profil + Config Servisi · Üretilebilir .env.example · Secret Rotation)"
type: adr
category: infrastructure
date: 2026-09-24
updated: 2026-09-24
version: 1.0.0
status: accepted
authority: ADR-015 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-015: Env Parser Strategy (Şemalı Fail-Fast Parser · Tek Sınıf · Profil + Config Servisi · Üretilebilir .env.example · Secret Rotation)

**Durum:** accepted (kullanılabilir — **frozen YOK**)
**Tarih:** 2026-09-24
**Karar Veren:** Vault Steward (kullanıcı onaylı — "ADR-015'i sıfırdan yaz"; karar içeriğinin tamamı kullanıcı onaylı/Önerilen) · debate: ✅ TAMAMLANDI (3 tur / 20 persona — 18 kabul / 2 çekimser / 0 red → KABUL) · Tech Lead: ✅ (2026-09-24)
**İlgili ADR'ler:** [[ADR-002-pdo-mandatory-no-orm]] (framework'süz, doğrudan PDO/PHP — config katmanı da framework kurmaz; dosya diskte VAR ✅) · [[ADR-005-ultrathink-protocol]] (doğrulama/kanıt disiplini + **REDACTED**: log'a/hataya secret değeri asla yazılmaz; dosya diskte VAR ✅) · [[ADR-008-bypass-auth-middleware]] (bypass yalnız test profilinde — profil ayrımı bu kararın dayanağı; dosya diskte VAR ✅) · [[ADR-011-session-management]] (`SESSION_*` anahtarlarının sahibi; dosya diskte VAR ✅) · [[ADR-014-multi-db-migration-strategy]] (fail-fast + forward-only + append-only log ruhu; dosya diskte VAR ✅) · karar dizini [[../index]] **satır 52** `[[ADR-015-env-parser-strategy]]` (slug eşleşmesi ✅).

---

## 1. Bağlam (Context)

CoreMusic'in üç PHP alan adı (`shared/`, `auth.coremusic.net/`, `home.coremusic.net/`) yapılandırmayı **`.env` dosyası + ortam değişkenleri + `define()` sabitleri** üzerinden yürütür. Parser, config servisi ve `.env.example` **zaten vardır** (§1.1); eksik olan **şema, tip güvenliği, bütünsel fail-fast ve tek erişim kapısı**dır. Bu ADR; `.env` okuma stratejisini **şemalı, fail-fast ve üretilebilir** hâle getirir; config erişiminin **tek kapıdan** (DI `Config` servisi) yapılmasını ve **secret rotation** prosedürünü sabitler. Kapsam: (a) tek parser/doğrulayıcı sınıf, (b) tip dönüştürme (bool/int/enum/string), (c) şema doğrulama (zorunlu anahtar + tip + aralık), (d) eksik/yanlışta fail-fast, (e) `.env.example`/`.env.schema` üretimi, (f) `.env`'in git'e girmemesi + rotation prosedürü, (g) `env=dev/test/prod` profilleri ve `getenv` yasağı.

### 1.1 Mevcut Durum

**Kod/vault kanıtları (diskde okundu — IMPLEMENTED/PLANNED etiketleri dosya yolu + satır ile):**

- **`shared/src/Config/EnvParser.php` — parser VAR, şema YOK (IMPLEMENTED + PLANNED):** sınıf `final class EnvParser`, `load(string $envFile): array` (satır 8-40) satır satır parse eder: `#` yorum atlanır (satır 19), `=` yoksa satır atlanır (satır 22-24), `explode('=', $line, 2)` (satır 25), tek/çift tırnak kırpımı (satır 29-34). `loadIntoEnv()` (satır 42-49) `$_ENV[$key]`'e yazar ve **mevcut değeri ezmez** (`if (!isset($_ENV[$key]))`, satır 45) — yani süreç-içi env, dosyadan önce gelmişse üstün. **Tip dönüştürme, zorunlu anahtar listesi, aralık/enum kontrolü ve hata üretimi YOK** → `PLANNED` (bu ADR §2.2a-d).
- **`shared/src/Config/ConfigManager.php` — DI config servisi VAR (IMPLEMENTED):** `implements IConfigManager` (`shared/src/Interfaces/Config/IConfigManager.php`), `get()` noktalı key + `cache` (satır 20-37), `getSecure()` boş/değerde `ServerException::configError()` fırlatır (satır 39-46), `maskSecret()` (satır 57-66), `isSensitiveKey()` + `SENSITIVE_KEYS` (satır 9-14: `database.mysql.password`, `security.app_pepper`, `security.encryption_key`, `security.session_key`), `filterSensitive()` → `[REDACTED]` (satır 117-137), `isProduction()` (`app.env === 'production'`, satır 73), `all(false)` hassas key'leri maskeler. **Ama `getEnv()` (satır 51) `$_ENV[$key] ?? getenv($key) ?: $default` der — doğrudan env erişimi hâlâ servis içinde gömülü** → bu ADR ile kaldırılır (§2.2h).
- **DI konteyneri php-di VAR (IMPLEMENTED) — ADR-085 düz metin:** `php-di/php-di: ^7.0` üç `composer.json`'da (`shared/composer.json`, `auth.coremusic.net/composer.json`, `home.coremusic.net/composer.json`). `auth.coremusic.net/include/Container/AuthContainer.php:25,35` ve `home.coremusic.net/include/Container/HomeContainer.php:22,32` konteynere `ConfigManager` parametre olarak girer; `auth.coremusic.net/index.php:57` `$config = new ConfigManager($appConfig);`, `home.coremusic.net/config/config.php:21` aynı. **ADR-085 dosyası `.ai/.decisions/**` altında YOK** (glob kanıtı) → düz metin, wiki-link kurulmaz.
- **`.env` yükleyici iki noktada IMPLEMENTED:** `auth.coremusic.net/config/constants.php:13` ve `home.coremusic.net/config/constants.php:13` → `\CoreMusic\Config\EnvParser::loadIntoEnv(dirname(__DIR__).'/config/.env')`; yardımcı closure `constants.php:17-18` = `$_ENV[$key] ?? getenv($key) ?: $default`.
- **Bugünkü tip dönüşümü elle ve parçalı (IMPLEMENTED):** `constants.php:22-25` `APP_ENV_MODE` enum kontrolü (`development|production|test`, geçersizse `http_response_code(500); exit('Invalid APP_ENV_MODE')`) ve `constants.php:61-66` `APP_PEPPER` boşsa `exit('Server misconfiguration: APP_PEPPER not set.')` — **tek tek fail-fast örnekleri var, bütünsel şema doğrulama yok**; `(int)` dönüşümleri satır 40, 46, 55-57; bool dönüşümü `['true','1','yes','on']` kümesi satır 72-73. `error_log` mesajı yalnız **anahtar adını** içerir, değer içermez (satır 65) → REDACTED uyumlu ✅.
- **Doğrudan env erişimi mantık katmanında hâlâ VAR (IMPLEMENTED — bu ADR ile yasaklanacak):** `shared/src/OAuth/OAuthManager.php:77` `getenv($envKey['client_id'])`; `shared/src/Bootstrap/RuntimeBootstrap.php:9` `$_ENV['APP_TIMEZONE'] ?? 'Europe/Istanbul'` (bootstrap katmanı, hariç tutulabilir); `auth.coremusic.net/config/cors.php:11` `$_ENV['CORS_ALLOWED_ORIGINS']`; `auth.coremusic.net/include/Container/AuthContainer.php:70` `$_ENV['APP_PEPPER'] ?? ''`; `auth.coremusic.net/include/Middleware/OriginCheckMiddleware.php:23` `$_ENV['ORIGIN_ALLOWED_HOSTS']`. Toplam: **`getenv` mantık katmanında 1 çağrı + `$_ENV[]` doğrudan 4 erişim**.
- **`.env.example` VAR, `.env.schema` YOK (kısım IMPLEMENTED / kısım PLANNED):** `shared/config/.env.example` (2822 bayt) + `auth.coremusic.net/config/.env.example`; içerik **yalnız placeholder** (`DB_PASSWORD=`, `APP_PEPPER=  # REQUIRED: random 64-char hex string`) ✅. `.env.schema` glob'u **0 dosya** → şema **PLANNED**. `.env` dosyası diskte de **yok** (yalnızca iki `.env.example`) → yerel kurulum PLANNED/kullanıcıya ait.
- **`.gitignore` doğrulandı (IMPLEMENTED):** satır 6 `.env`, satır 7 `.env.local`, satır 8 `.env.*.local`, satır 9 `config/.env`, satır 68 `.ai/.env.figma`, satır 69 `.ai/.env.*`, satır 70 `!.env.example`. Ek kanıt: `git ls-files` içinde `.env` geçen **yalnız 2 `.env.example`**; `git log --all --diff-filter=A -- "*.env" ".env"` = **0 commit** → gerçek `.env` hiç commit edilmemiş.
- **`vlucas/phpdotenv` declare edilmiş ama kullanılmıyor (PLANNED/ölü bağımlılık):** `auth.coremusic.net/composer.json:22` `"vlucas/phpdotenv": "^5.7"`; `vendor/vlucas/phpdotenv` **kurulu değil** (Test-Path = false, iki yerde de) ve PHP kodunda `dotenv|Dotenv` eşleşmesi **0**; `shared/` ve `home.coremusic.net/` composer'larında yok.
- **Sahte config test edilebilirliği VAR (IMPLEMENTED):** `ConfigManager` constructor'ı `array $config` alır → test'te fake config mümkün; `shared/tests/Unit/Config/ConfigManagerTest.php` diskte (22 test dosyası içinde).
- **Vault indeksleri bu ADR'yi bekliyor (IMPLEMENTED — kayıt):** `.ai/.decisions/index.md:52` `[[ADR-015-env-parser-strategy]] | Env Parser Strategy | Infrastructure`; `.ai/brain.md:970` `ADR-015 | .env dosya okuma stratejisi`; `.ai/keys.md:250` `ADR-015 | env parser, .env | Infrastructure`; `.ai/.templates/adr/adr-index.md:86` `.env dosya okuma stratejisi | infra`; `.ai/.templates/adr/adr-security-template.md:258` "`.env` okuma stratejisi … anahtar vault'a YAZILMAZ (REDACTED) | ADR-015".
- **İddia-kod çelişkisi (⚠️ VERIFICATION REQUIRED):** `.ai/.agents/data-engineer.md:24,68,95,119,238,271` ADR-015'i **"cache stratejisi / migration aracı"** olarak etiketler; `data-engineer.md:343` ise "ADR-015 (eski profilden taşındı, **yok**)" der. Dizin (`index.md:52`), `brain.md:970`, `keys.md:250` ve `adr-index.md:86` ise **env parser** der → **karar metni env parser'dır**; `data-engineer.md` düzeltmesi ayrı vault işlemi (§5.1 adım 6).
- **Şablon/protokol kanıtları:** `.ai/.templates/adr/adr-template.md` (Guardrail #16, 7 bölüm + §1.3 9 alan) VAR ✅ · format referansı `.ai/.decisions/accepted/ADR-014-multi-db-migration-strategy.md` VAR ✅ · `.claude/skills/prompt-maker/references/10-web-research-protocol.md` VAR ✅.

### 1.2 Sorun Tanımı

1. **Şema yok:** `.env` içindeki anahtarların zorunlu olup olmadığı, tipi ve aralığı dosyada değil, **kodun içinde dağılmış** `define()`/`$env()` çağrılarında. Yeni anahtar eklendiğinde şema/örnek senkronu elle — **drift** kaçınılmaz.
2. **Tip güvenliği yok:** `EnvParser::load()` her şeyi **string** döndürür; `DB_PORT` "abc" yazıldığında `(int)` sessizce `0` verir. Bool için her dosya kendi `['true','1','yes','on']` kümesini tekrarlar (DRY ihlali).
3. **Fail-fast parçalı:** Yalnız `APP_ENV_MODE` ve `APP_PEPPER` eksikliğinde uygulama kapanır; `DB_HOST`, `SESSION_COOKIE_DOMAIN`, `CSRF_TOKEN_LENGTH` gibi onlarca anahtar **sessiz varsayılanla** çalışır ve hata üretimde farkedilir.
4. **Çoklu kapı:** Kod hem `$_ENV[]` hem `getenv()` hem `define()` üzerinden config okuyor (§1.1) → hangisinin üstün olduğu dosyadan dosyaya değişir; test'te tek bir yeri sahtelemek yetmez.
5. **`getEnv()` ikilemi:** `ConfigManager::getEnv()` (satır 51) var olduğu sürece "tek erişim kapısı" iddiası kırılgandır; OAuth katmanı `getenv()` ile hâlâ doğrudan okuyor (§1.1).
6. **Rotation prosedürü yazılı değil:** `.env` git'te olmadığı için sızıntı yüzeyi dar, ama **yenileme (rotation) sırası, süresi, kimin hangi adımı attığı ve log'a ne yazılacağı** tanımsız → olay anında paniğe kalır.
7. **`phpdotenv` ölü bağımlılık:** composer'da declare, diskte kurulu değil, kodda kullanılmıyor → "hangi parser?" belirsizliği (EnvParser mı, phpdotenv mi?) açık.
8. **ADR numarası istisnası:** genel kural "yeni ADR ≥ 088" iken `ADR-015` numarası `index.md:52`'de **çoktan rezerve edilmiş boş slottur** (ADR-016'dan 023'e kadar da öyle) → bu yazımda numara **yeni üretilmez, mevcut rezervasyon doldurulur**.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: prompt-maker web araştırması protokolü (`.claude/skills/prompt-maker/references/10-web-research-protocol.md` — diskte VAR ✅) — resmi/anahtar kaynak önce (12factor.net, OWASP, GitGuardian), her iddiaya ≥2 bağımsız çapraz kaynak, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`. **Odak: (a) secret management 2025-26 (vault/cloud secret manager vs env dosyası), (b) 12-factor config, (c) secret leakage incident'ları (git history leak), (d) dotenv best practice + şemalı doğrulama.** Not: bu tur **websearch sonuç başlığı/özet** düzeyinde derlendi; sayfa-içi derin okuma ayrı bir doğrulama turuna bırakıldı (aşağıda açıkça işaretli).

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "secret management 2026 environment variables vs vault secrets manager best practices dotenv" · (2) "12-factor app config store config in environment secrets separate git history .env leak incident" · (3) "secret leaked in git history rotating secrets required not just deleting repo purge best practice 2025" · (4) "dotenv parsing validation typed environment variables fail fast schema validation best practice PHP" · (5) "secret rotation procedure order revoke old credential before revoking access zero downtime rotation checklist" |
| Web Search **Konusu** | (1) `.env` dosyası ile Vault/cloud secret manager arasındaki güvenlik-denetim-eksiklik farkları; (2) 12-Factor "Config" ilkesi ve config ile secret ayrımı; (3) git geçmişine giren secret'ın "silinince geçtiği" yanılgısı, rotate-first yaklaşımı; (4) parse sonrası tip/coercion + şema ile startup'ta doğrulama kalıbı (Zod/ajv örnekleri PHP olmayan ama kalıp genel); (5) rotation sırası: üret → yay → doğrula → eskisini iptal (overlap window). |
| Web Search **Bağlam** | **~20 adlandırılmış kaynak / 5 sorgu**: 12factor.net (Config), OWASP temelli sekmeler (aquilax.ai), GitGuardian Blog (env secret best practices + leak müdahalesi), Doppler, dev.to, Smallcase Engineering, Arcjet, nodejs-security.com, GitHub Community Discussion #161907, nhimg.org, elegantsoftwaresolutions, dev.to/Chris Winnen, r/devsecops, Stack Overflow (dotenv vs 12-factor), creatures.sh, dev.to/Zod, Medium (startup validation), pkgpulse (2026 dotenv limitleri), Decryption Digest, Big Iron, passwork.pro. |
| Web Search **Kısa Açıklama** | **(1) Env vs Vault:** `.env` dosyası küçük ekip/yerel için kabul görür ama senkronu ölçeklenmez (Doppler: ".env sync etmek secret management olmaz"); üretimde audit + rotation için Vault/AWS/GCP Secret Manager önerilir (GitGuardian: "production için HashiCorp Vault ya da AWS Secrets Manager"); env değişkenleri süreç belleğinde görünür ve `/proc` gibi yüzeylerden okunabilir, bu yüzden "secret'ı env'e koy" tartışmalıdır (nodejs-security, Arcjet). **(2) 12-Factor:** config ortam değişkenlerinde tutulur, kodda sabitlenmez (12factor.net III); dotenv 12-factor'e aykırı mı tartışması, çözüm genelde "dotenv yalnız yerelde, üretimde gerçek env" (Stack Overflow). **(3) Git leak:** GitHub'a push edilen secret geçmişte kalır; silmek yetmez → **önce revoke/rotate**, tarih temizliği opsiyonel ve zordur (GitHub Community #161907, GitGuardian, nhimg.org, r/devsecops). **(4) Şemalı doğrulama:** dotenv tek başına validation/types vermez (pkgpulse); doğru kalıp = tek şema → startup'ta coerce + validate → başlangıçta anlamlı hata (creatures.sh, dev.to, Medium, Grasp). **(5) Rotation sırası:** yeni secret üret → tüm tüketicilere yay → doğrula → **sonra** eskisini iptal; anlık takas değil **overlap penceresi** (Decryption Digest, Big Iron, passwork). |
| Web Search **Uzun Açıklama** | **(a) Secret yönetim yüzeyi:** 12-Factor config ilkesi, config'i koddan ayırır ama **secret ile config'i aynı yere koymaz**; `.env` dosyası repo köküne komşu olduğu için bilinçsizce commit edilme riski taşır (GitGuardian, dev.to "doing much better than your .env file" → cloud secret manager'a geçiş gerekçesi). Vault tarafının farkı üç başlıkta toplanır: **audit trail** (kim ne zaman okudu), **dynamic/rotating secret** (otomatik kısa ömürlü kimlik) ve **erişim politikası** (kim hangi anahtarı görür) — bunların hiçbiri düz `.env` dosyasında yok (Smallcase Engineering, bastion.tech, OWASP-t temelli aquilax yazımı). Karşı argüman da güçlü: küçük ölçek/tek sunucu + izole ağ için `.env` yeterlidir ve vault bağımlılığı kendi operasyon maliyetini getirir (r/devops tartışması, HN "tools for env files"). CoreMusic'in mevcut yüzeyi (yalnız `.env` + gitignore) bu yüzden **önce doğru davranışla (git'e girmez, şemalı, fail-fast) güçlendirilir, vault'a geçiş ayrı ADR konusu** olarak bırakılır. **(b) Git history leak:** asıl kural "delete ≠ gone": GitHub PR'ları/commit geçmişinde secret kalır; **ilk iş revoke/rotate etmektir**, `git filter-repo` ile tarih temizliği **sonradan ve opsiyonel** gelir (GitHub Community #161907; GitGuardian "Step 1 revoke the secret, Step 2 (optional) cleanup"; dev.to/Chris Winnen "goal is to make the leaked secret unusable"; nhimg.org "treat a pushed secret as exposed in every historical reference until it is rotated"). 2026 yazılarında aynı ders tekrarlanır: "Git history is forever — rotate-first fix" (elegantsoftwaresolutions). **Bu, `.gitignore` doğrulamasını tek başına yeterli yapmaz: repo genelinde `.env` hiç commit edilmemiş olsa bile geçmişte temizlenmiş bir sızıntı dahi olsa rotation gerekir.** **(c) Şemalı/fail-fast parse:** dotenv'un bilinen eksikleri "validation ve type yok"; endüstri kalıbı tek şemadan hem **runtime doğrulama** hem **`.env.example` üretimi** çıkarmaktır (pkgpulse: "the limitation of dotenv alone is the lack of validation and types"; creatures.sh: schema first → coerce → export parsed config; Grasp: "config is an executable contract … start the service only after configuration succeeds"; dev.to/Medium: startup'ta validate, aksi halde hata saatler sonra runtime'da). Bu kalıp, ADR-005'in "uygulama başlamadan doğrula" ruhuyla birebir örtüşür. **(d) Rotation sırası:** güvenli rotation **seri bir sıralama**dır — "provision new → propagate to all consumers → verify active → only then revoke old" (Decryption Digest); anlık değişim yerine eski+yeni geçerli **overlap penceresi** (Big Iron "sequence with an overlap window, not an instantaneous swap"); lifecycle'ın kendisi create→store→use→rotate→revoke→retire olarak tanımlanır (passwork). **Süre/kişi sırası** için OWASP-t kaynaklar vault sahibi ekip + deployment sahibinin ayrı adımlarda olmasını, incident'ta ise "önce revoke" kuralını önerir (aquilax, GitGuardian). |
| Web Search **Paragraf Veri Uzun** | 12-Factor: config env'de, kodda sabit yok → dotenv yalnız yerel yardımcı, hukuku env verir · `.env` vs vault: dosya = hızlı+ucuz ama audit/dynamic/policy yok; vault = audit + dynamic + policy; küçük ekip `.env`'de kalabilir ama **git'e asla girmez** · git leak: push edilen secret geçmişte yaşar → **ilk adım revoke/rotate**, `filter-repo` temizliği opsiyonel ve son adım · "silip geçtim" yanılgısı GitHub Community, GitGuardian, r/devsecops, nhimg.org, elegantsoftwaresolutions'ta 5 kez yinelenir · dotenv limiti = types + validation yok → tek şema → startup'ta coerce+validate → servis ancak config başarılıysa başlar · hata mesajında **anahtar adı + beklenen tip/aralık**, değer **asla** (REDACTED) · rotation: üret → yay → doğrula → iptal; overlap penceresi, anlık takas yok; env değişkenleri bellekte/`/proc` yüzeyinde görünür → uzun ömürlü secret'ı doğrudan env'e koyma tartışmalı · sonuc: **dosya git'te olmaz, şema git'te olur, örnek şemadan üretilir, rotation yazılı prosedür olur.** |
| Web Search **Sonucu** | 1) **12-Factor config doğrulandı** (12factor.net + Stack Overflow tartışma + HN) → config env'den okunur, kodda sabitlenmez; `.env` yerel yardımcıdır. 2) **`.env` dosyasının vault karşısında zaafı netleşti** (5 kaynak: GitGuardian, Doppler, Smallcase, bastion.tech, OWASP-t aquilax) → audit/dynamic/policy yok; CoreMusic için **önce davranış (gitignore + şema + fail-fast), vault geçişi ayrı karar**. 3) **Git history leak = rotate-first** (5 kaynak: GitHub Community #161907, GitGuardian, dev.to/Winnen, nhimg.org, r/devsecops) → "`.gitignore` var, rahatız" **tek başına yeterli değildir**; sarsıntıda önce revoke. 4) **Şemalı startup doğrulama kalıbı doğrulandı** (6 kaynak: pkgpulse, creatures.sh, dev.to Zod, Medium/Minaya, mkabumattar, Grasp) → tek şema = runtime validation + örnek üretim + anlamlı erken hata. 5) **Rotation sırası sabitlendi** (4 kaynak: Decryption Digest, Big Iron, passwork, GitGuardian) → üret → yay → doğrula → iptal; overlap penceresi; anlık takas yok. **Toplam ~20 adlandırılmış kaynak, 5 sorgu**; her ana iddia ≥2 çapraz kaynakla karşılanır. |
| Web Search **Alınan Karar** | **ADR-015 KABUL EDİLİR — ŞEMALI, FAIL-FAST ENV PARSER + TEK ERİŞİM KAPISI + ÜRETİLEBİLİR ÖRNEK + SECRET ROTATION:** **(A) Tek sınıf:** `.env` parse + tip dönüştürme (bool/int/enum/string) + şema doğrulama (zorunlu anahtar listesi + tip + aralık) tek yerde; **eksik/yanlış → fail-fast** (uygulama başlamaz, tüm hatalar **tek seferde** toplanıp anahtar adıyla listelenir; değer asla yazılmaz — ADR-005/REDACTED); hatalı tip "0"/boş string'e sessiz düşmez (kaynak 16-19). **(B) Şema = tek kaynak:** `.env.schema` dosyası zorunlu anahtar/varsayılan/profil/arıalık bilgisini tutar; **`.env.example` şemadan üretilir** (`bin/` komutu), el ile senkron yasak (kaynak 16, 19). **(C) Güvenlik:** `.env` **git'e girmez** (`.gitignore` satır 6-9, 68-70 doğrulandı + `git log` 0 commit); git'e yalnız şema/örnek girer; **rotation**: üret → yay → doğrula → eskisini iptal, süreli (periyodik) ve olay anında anında; sırada kim: **Security Engineer (talep/iptal) → DevOps Engineer (uygulama) → Vault Steward (log kaydı)**; log'a/hataya secret **asla** yazılmaz; production rotation **iki kişi kuralı** (break-glass) ile (kaynak 8, 10, 18-20). **(D) Profil + Config servisi:** `env=development/test/production` profilleri; **kod asla `.env`'den doğrudan okumaz** → DI üzerinden `Config` servisi tek erişim kapısı (sahte config ile test mümkün, §1.1); **mantık katmanında `getenv()`/`$_ENV[]` çağrıları YASAK** (bugün 1+4 ihlal var — §1.1), istisna yalnız bootstrap/Config kaydı; `ConfigManager::getEnv()` (satır 51) kaldırılır (kaynak 1-4, 16). **(E) Fallback:** üretimde fail-fast kesindir; `development`/`test` profilinde **şema dosyası yoksa** uyarı + `.env.example` varsayılanları ile devam (kod içi varsayılan son çare) — böylece yanlış alarm üretime taşınmaz. **(F) Vault geçişi ayrı ADR:** `.env`→Vault/cloud secret manager geçişi bu kararın **kapsam dışı** olduğu, ayrı ADR ile değerlendirilir (kaynak 2-3, 7). |
| Web Search **Sonuç** | Karar 2025-2026 verisiyle **desteklendi ve netleşti**: 12-Factor config (3 kaynak), `.env` vs vault (5 kaynak), git-history rotate-first (5 kaynak), şemalı startup doğrulama (6 kaynak), rotation sırası (4 kaynak) → **~20 adlandırılmış kaynak, 5 sorgu**; çapraz doğrulama ≥2 kaynak tüm ana iddialarda karşılanır. Kanıt tarafı kodda da aynı resmi verdi: parser **var** (EnvParser), config servisi **var** (ConfigManager + php-di), `.env.example` **var**, `.gitignore` **doğrulandı**, `.env` **hiç commit edilmemiş** → bu ADR **sıfırdan sistem kurmaz, mevcut üç parçayı şema + fail-fast + tek kapı ile birleştirir** (§1.1). Vault bulguları ayrıldı: IMPLEMENTED (parser, servis, örnek, gitignore, DI, test), PLANNED (şema, üretim, `getenv` yasağı, rotation prosedürü); `phpdotenv` ölü bağımlılık ve `data-engineer.md` "cache stratejisi" iddiası **çelişki** olarak işaretlendi, uydurulmadı. **Kaynak listesi (~20):** 1) 12factor.net — III. Config (store config in the environment) · 2) GitGuardian Blog — Best Practices for Environment Variables Secrets · 3) Doppler Blog — Why syncing .env files doesn't scale for secrets management · 4) Smallcase Engineering — Decoding Security: Secrets Manager or Environment Variables · 5) dev.to/Dang Tony — Doing much better than your .env file (Google Secret Manager) · 6) bastion.tech — Secrets Management 101: Stop Storing Credentials in .env Files · 7) Arcjet — Should you store secrets in environment variables? · 8) nodejs-security.com — Do Not Use Secrets in Environment Variables · 9) aquilax.ai — OWASP Secrets Management & Environment Variables (vault, .env, CI/CD injection, rotation) · 10) GitGuardian Blog — Leaking secrets on GitHub: what to do (revoke first, cleanup optional) · 11) GitHub Community Discussion #161907 — How does GitHub handle exposed secrets (delete ≠ gone, revoke) · 12) nhimg.org — Git history exposes secrets long after deletion on GitHub · 13) dev.to/Chris Winnen — You've leaked a secret in your git repository – now what? · 14) r/devsecops — Why deleting leaked secrets from git doesn't fix it · 15) elegantsoftwaresolutions — Git History Is Forever (rotate-first) · 16) pkgpulse — Best Environment Variable Management 2026 (dotenv: no validation, no types) · 17) creatures.sh — Environment variables type safety and validation (schema → coerce → export) · 18) dev.to + Medium (D. Minaya) + mkabumattar — Validating Environment Variables at Startup (fail-fast contract) · 19) Decryption Digest — Secrets Rotation in Production Without an Outage (üret → yay → doğrula → iptal) · 20) Big Iron — Secret rotation automation: the zero-touch pattern (overlap window) + passwork.pro — Secrets rotation lifecycle. |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-005 (doğrulama + REDACTED) | Fail-fast hatalarında **anahtar adı + beklenen tip/aralık** yazılır; **değer, secret, connection string asla** hata/log çıktısına girmez (`ConfigManager::filterSensitive()` bunu zaten `[REDACTED]` ile yapar) |
| ADR-002 (no ORM / no framework) | Yeni parser/şema katmanı **bağımlılık eklenmeden** yazılır (php-dotenv kullanılmaz — §1.1 ölü bağımlılık); `php-di` zaten var, yeni konteyner kurulmaz |
| ADR-008 + ADR-011 (bypass + session) | `FORCE_AUTH_BYPASS`/`BYPASS_*` yalnız `test` profilinde geçerli; `SESSION_*` anahtarlarının aralığı ADR-011'e tabidir — şema bu iki ADR'nin alanına **girmez**, yalnız tipini/zorunluluğunu tanımlar |
| ADR-014 (fail-fast + append-only) | İlk hata → dur disiplini ve `.ai/log.md` append-only yazım bu ADR'nin süreç dayanağıdır |
| In-Place Refactoring | `EnvParser.php`, `ConfigManager.php`, `constants.php` dosya adları **onaysız değiştirilemez**; davranış genişletilir, ad sabit kalır |
| Frozen ADR-001-037 dokunulmaz | Yalnız okunur + referanslanır (AGENTS.md §25.3 kural 2) |
| REDACTED | Secret/credential hiçbir koşulda bu ADR'ye, `.env.example`'a (boş bırakılır) veya log'a yazılmaz |
| `.ai/log.md` append-only | Bu işlem dahil tüm kayıtlar yalnız ekleme ile yazılır |
| Numara kuralı istisnası | "Yeni ADR ≥ 088" bu yazımda **uygulanmaz**: `ADR-015` numarası `index.md:52`'de rezerve edilmiş boş slottur (doldurma, yeni numara tahsisi değil) |

---

## 2. Karar (Decision)

**Şemalı, fail-fast `.env` parser'ı tek sınıf olarak yazılır; config erişimi yalnız DI `Config` servisinden yapılır; `.env` git'e girmez, `.env.example` şemadan üretilir; secret rotation yazılı prosedüre bağlanır.**

### 2.1 Neden Bu Seçenek?

- **Mevcut yapı zaten üçte üç doğru yerde:** parser (`EnvParser`), servis (`ConfigManager` + php-di), örnek (`.env.example`) ve gitignore **var** (§1.1). Karar "yeni sistem" değil, **mevcut üç parçanın şema + fail-fast + tek kapı ile birleştirilmesi** → en düşük maliyetli, en düşük riskli yol (YAGNI).
- **Şema tek kaynak olduğu için drift kapanır:** zorunluluk, tip, aralık, varsayılan ve profil bilgisi tek dosyada durur; `.env.example` **üretildiği** için "örnek güncellenmedi" hatası teknik olarak imkânsız hâle gelir (kaynak 16, 19).
- **Fail-fast, üretim maliyetini öne çeker:** eksik `APP_PEPPER` bugün zaten kapanıyor (§1.1); bunu **tüm zorunlu anahtarlara** genişletmek, hatayı kurulum anında (açıklanabilir mesaj) gösterir; ADR-005'in "uygulama başlamadan doğrula" ruhuyla birebir örtüşür.
- **Tek erişim kapısı test edilebilirliği çözer:** `ConfigManager` constructor'ının `array $config` alması + `ConfigManagerTest.php`'nin varlığı, **sahte config ile test'in bugün mümkün** olduğunu kanıtlar (§1.1); `getEnv()` kalkınca kapı gerçekten tek olur.
- **Vault'a hemen geçilmez:** 2026 kaynakları production'da vault önerse de (§1.3), CoreMusic'in mevcut ölçeğinde **önce doğru davranış** (git'e girmez + şemalı + fail-fast + rotation) vault'tan daha yüksek getirili ve geri alınabilir (§3 3, §4.4).

### 2.2 Teknik Detaylar

**a) Tek sınıf — `shared/src/Config/EnvSchema.php` + `EnvParser` genişlemesi (dosya adları yeni, mevcutlar korunur):**
`EnvParser::load()` parse etmeye devam eder (satır/koment/tırnak kuralları korunur, §1.1); yeni `EnvSchema` **şemayı** tutar: `['KEY' => ['type' => 'string|int|bool|enum', 'required' => bool, 'default' => mixed, 'enum' => [...], 'min' => n, 'max' => n, 'profiles' => ['development','test','production']]]`. `EnvValidator::validate(array $env, EnvSchema $schema, string $profile): array` **dönüştürülmüş + doğrulanmış** değerleri döndürür.

**b) Tip dönüştürme (tek yerde, dosyalar tekrar etmez):** `bool` → `true|false|1|0|yes|no|on|off` (case-insensitive; **tanımsız değer hata**, "maybe" sessiz `false` olmaz); `int` → `ctype_digit`/negatif işaret + `min/max` aralığı (ör. `SESSION_LIFETIME > 0`, `CSRF_TOKEN_LENGTH >= 16`); `enum` → `APP_ENV_MODE ∈ development|test|production` (bugünkü `constants.php:22-25` kontrolünün genelleşmişi); `string` → `trim`, boşluk/zorunlu durum kontrolü.

**c) Şema doğrulama:** (1) zorunlu anahtar listesi (**profile'a göre**: `production`'da `DB_PASSWORD`, `APP_PEPPER` zorunlu; `development`'ta varsayılanla geçilebilir), (2) tip, (3) aralık/enum, (4) bilinmeyen anahtar → **uyarı** (hata değil, ileri uyumluluk).

**d) Fail-fast (uygulama başlamaz):** doğrulama bootstrap'te, **HTTP yanıtı ve oturum açılmadan önce** çalışır; tüm hatalar **tek seferde toplanır** ve `stderr`'e listelenir → kullanıcı tek turda hepsini görür (parçalı kapanış yok). Çıkış kodu ≠ 0. Mesaj biçimi: `CONFIG ERROR [production] DB_PASSWORD: required key missing (expected: string, non-empty)` — **değer hiçbir yerde yazılmaz** (ADR-005). Uygulama yanıtı `500` + genel mesaj; detay yalnız log (maskelenmiş).

**e) Üretilebilir `.env.example` / `.env.schema`:** tek kaynak `.env.schema` (veya şema dizisi) → `php bin/config-doc.php --emit-schema` ve `--emit-example` komutları **ikisini de üretir**; `.env.example`'ın elle düzenlenmesi **drift sayılır** ve CI'da "üretim çıktısı ≠ dosya" kontrolü ile yakalanır. Varsayılan değerler **şemadadır**, kod içinde ikinci kez yazılmaz (DRY).

**f) Güvenlik + git:** `.gitignore` satır 6-9 ve 68-70 doğrulanmıştır (§1.1); **git'e yalnız `.env.schema` ve `.env.example` girer**; gerçek `.env` hiç commit edilmemiştir (`git log --all --diff-filter=A` = 0). `.env.example` içinde değer **boş** bırakılır (`APP_PEPPER=`), yorum satırı gerekliliği/türü tarif eder.

**g) Secret rotation prosedürü (yazılı, bu ADR ile zorunlu):**

| # | Adım | Kim | Sür |
|---|------|-----|-----|
| 1 | Yeni secret üret (eski **henüz geçerliyken**) | DevOps Engineer | 0 dk |
| 2 | Tüm tüketicilere yay (`.env` / süreç env) + hizalı deploy | DevOps Engineer + Backend Architect | ≤ 15 dk |
| 3 | Doğrula (yeni değer çalışıyor mu) | Security Engineer | ≤ 5 dk |
| 4 | **Sonra** eskisini iptal/revoke | Security Engineer | hemen doğrulama sonrası |
| 5 | Kayıt (anahtar adı + tarih + uygulayan — **değer yok**) | Vault Steward → `.ai/log.md` append | aynı gün |

**Süre:** periyodik rotation **90 günde bir** (üretim secret'ları), **olay/sızıntı şüphesinde anında** (önce revoke — §1.3 kaynak 10-15). **Erişim:** production secret rotation **iki kişi kuralı** ile (talep eden + uygulayan farklı); erişim listesi `keys.md`/Security Engineer'da. **Log kuralı:** hiçbir adımda secret değeri `log.md`'ye, `error_log`'a veya konsola **yazılmaz**; yalnız anahtar adı + zaman damgası (`ConfigManager::maskSecret()` ile maskelenmiş çıktı gerekirse).

**h) Profil + Config servisi (tek erişim kapısı):**
- `env=development|test|production` (`APP_ENV_MODE` mevcut anahtarı korunur; `isProduction()`/`isDevelopment()` `ConfigManager` satır 73-79'da zaten vardır → profil tek okuma).
- **Kural:** uygulama kodu `.env`'i **hiç okumaz**; yalnız `Config::get()` / `Config::getSecure()` kullanılır (DI: `AuthContainer`/`HomeContainer` üzerinden, §1.1).
- **Yasak:** mantık katmanında `getenv(...)` ve `$_ENV[...]`/`$_SERVER[...]` üzerinden config okumak. Bugünkü ihlaller (§1.1: `OAuthManager.php:77`, `cors.php:11`, `AuthContainer.php:70`, `OriginCheckMiddleware.php:23`, `ConfigManager::getEnv` satır 51) **kapatılır**; `getEnv()` metodu kaldırılır (arayüz `IConfigManager` ile birlikte). **İstisna:** `EnvParser`/bootstrap (config'i **kayan** tek katman) ve `RuntimeBootstrap.php:9` (timezone — bootstrap).
- **Zorunlu test:** `test` profilinde sahte config ile startup testi (mevcut `ConfigManagerTest.php` genişletilir).

**i) Vault geçişi (kapsam dışı, açık kapı):** Vault / cloud secret manager'a geçiş **bu ADR ile yapılmaz**; gerekçe §1.3 (audit/dynamic/policy) ve §3 3. Tetikleyici: 2. parti entegrasyon, çoklu ortam secret'ı veya denetim gereksinimi → **yeni ADR** (≥ 088).

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **`vlucas/phpdotenv` + elle doğrulama (mevcut declare'i canlandır)** | Olgun parse, az kod | Composer'da declare ama **kurulu değil, kodda kullanılmıyor** (§1.1); yine de tip/şema/fail-fast **o da vermez**; parse davranışı `constants.php` içindeki `$_ENV` önceliğiyle çakışır | Karar zaten "şemalı parser" — hazır kütüphane parse kısmını kapar ama **§2.2a-e'nin hiçbirini** vermez; ADR-002 ruhu (bağımlılık azalt) + YAGNI. Ölü bağımlılık temizliği §5.1 adım 5'te ayrıca yapılır |
| 2 | **Şema olmadan yalnız `EnvParser`'ı genişlet (tip dönüşümü + uyarı)** | En hızlı uygulama, geri uyumlu | Zorunluluk/aralık denetimi olmaz; eksik anahtar sessiz varsayılanla devam eder; `.env.example` elle kalır (drift sürer) | §1.2 1-3'ün **hiçbirini** çözmez; "neden ADR yazıyoruz" sorusu yanıtlanmaz — kısmi çözüm, karar değil |
| 3 | **HashiCorp Vault / cloud secret manager'a geçiş** | Audit, dynamic secret, policy, merkezi rotation (§1.3 kaynak 4-6) | Yeni altyapı + operasyon yükü; uygulama kodu Vault client'a bağlanır (ADR-002 ile gerilir); mevcut tek-sunucu ölçeğinde getirisi düşük; geçiş riskli ve **geri alınması pahalı** | **Reddedilmedi — ertelendi:** kapsam dışı (§2.2i); önce davranış (gitignore+şema+fail-fast+rotation) Vault'tan daha yüksek getirili; tetikleyicisi için ayrı ADR yazılır |
| 4 | **`.env`'i tamamen yasak, config yalnız ortam değişkenleri (CI/deploy enjeksiyonu)** | 12-Factor'a en saf hâli; dosya yüzeyi sıfır | Yerel geliştirme herkes için `.env`/export demek olur; Windows local kurulumda pratik değil; örnek/şema dosyası yine gerekir | 12-Factor "config env'de" kuralı **`.env` dosyasının yerel yardımcı olmasını yasaklamaz** (§1.3 kaynak 1-3: dotenv yalnız yerelde tartışması); pratik maliyet > getiri |
| 5 | **Config'i koda sabitle / PHP config dosyası döndürsün (`return [...]`)** | Basit, tip güvenli PHP dizisi | **12-Factor ihlali** (config kodda); ortam başına kod değişimi; secret koda girme riski | §1.3 kaynak 1 doğrudan karşı; secret değişimi için deploy gerekir |
| 6 | **Fail-fast yerine "uyar ve devam et" (production'da da)** | Kesinti yok, yanlış alarm üretmez | Eksik `DB_PASSWORD` ile canlıya çıkılır; hata ancak kullanıcı şikâyetinde görülür; ADR-005 ruhuyla çelişir | §1.2 3'ün devamı demek; **yalnızca `development`/`test` profilinde** fallback öngörülür (§2.2d), production'da kesin fail-fast |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Tek doğruluk kaynağı:** zorunluluk/tip/aralık/varsayılan/profil tek şemada; `.env.example` **üretildiği** için örnek-şema drift'i imkânsız hâle gelir.
- **Hata kurulum anında, anlaşılır:** tüm eksikler tek seferde, anahtar adı + beklenen tip/aralık ile listelenir; üretimde "sessiz varsayılan sürprizi" kalkar (§1.2 3).
- **Gerçek tip güvenliği:** `DB_PORT=abc` artık `0` değil **hata** olur; bool/enum tek kümede yorumlanır, dosyalar arası tekrar (DRY) kalkar.
- **Tek erişim kapısı + test:** `getEnv()`/`getenv()`/`$_ENV` erişimi kalkınca config'i **tek yerden sahtelemek** yeterli olur; mevcut `ConfigManagerTest.php` bunu zaten kanıtlıyor.
- **Güvenlik duruşu:** `.env` git'te **hiç olmadı** ve bu ADR ile kural olarak **olamaz**; git'e giren şema/örnek, gerçek secret taşımaz; rotation **yazılı ve denetlenebilir** (tarih/kişi/log).
- **ADR-005/014 uyumu:** doğrulama-önce-başlama ve append-only audit aynı disiplinle yürür.

### 4.2 Olumsuz Sonuçlar

- **Kurulum bariyeri yükselir:** eksik anahtar artık **açılışta kapanış** demektir; yeni geliştiricinin ilk deneyimi hata listesiyle karşılaşabilir (fallback yalnız dev/test — §2.2d).
- **Şema bakımı ek iş:** yeni env anahtarı = **iki** yerde değişiklik (kod + şema) olmak zorunda; şema unutulursa uygulama başlamaz (bu, hata değil **erken uyarı** ama bariyerdir).
- **Geçiş işçiliği:** bugünkü 1 `getenv` + 4 `$_ENV` ihlali tek tek taşınmalı (`OAuthManager`, `cors.php`, `AuthContainer`, `OriginCheckMiddleware`, `RuntimeBootstrap` istisna) — küçük ama yayılmış refactor.
- **Ölü bağımlılık temizliği:** `auth.coremusic.net/composer.json`'daki `vlucas/phpdotenv` kaldırılınca composer lock değişir (düşük riskli ama kayıt gerekir).
- **Rotation erişim bağımlılığı:** iki kişi kuralı + 90 günlük periyot, kişiler/devre dışı kalırsa prosedür **işleyemez** (risk 2).
- **Vault'a geçiş ertelenir:** audit/dynamic/policy capability'si şimdilik yok (§4.3 risk 5).

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **Fail-fast erken kapanış → yanlış alarm** — şema gereğinden katı, geçerli bir `.env` üretimde düşer (ör. opsiyonel anahtar zorunlu işaretlenmiş) | 3 (olası) | 4 (yüksek) | Profile göre zorunluluk (`development`/`test` esnek, `production` katı) + **fallback zinciri** (şema → `.env.example` varsayılanı → kod içi varsayılan, §2.2d) + çıkış öncesi **dry-run komutu** (`bin/config-doc.php --check`) + tüm hataların tek seferde raporlanması (tek düzeltilme turu) + staging'de bir tur doğrulama |
| **Secret rotation erişim kaybı** — 2 kişi kuralında yetkili kişiye ulaşılamaz / provision adımında yeni secret kaybolursa erişim kesilir | 3 (olası) | 4 (yüksek) | Rotation **overlap penceresi** (üret → yay → doğrula → **sonra** iptal — §1.3 kaynak 19-20): eski secret doğrulama tamamlanana kadar **iptal edilmez** → geri dönüş her zaman açık; yedek ikinci yetkili + break-glass kaydı `keys.md`'de; adımlar `.ai/log.md`'ye append edilir |
| **Şema drift** — kodda okunan anahtar şemaya eklenmezse fail-fast gereksiz hata üretir ya da bilinmeyen anahtar sessiz kalır | 3 (olası) | 3 (orta) | Şema = tek kaynak ve `.env.example` ondan **üretilir**; CI'da "üretim çıktısı ≠ dosya" kontrolü + "kodda geçen `Config::get('X')` anahtarları şemada var mı" taraması; bilinmeyen anahtar **uyarı** (hata değil) |
| **Geçiş süresince ikili erişim** — `getEnv()`/`getenv()` kaldırılırken bir çağrı noktası unutulur, çalışma zamanı `null` döner | 4 (çok olası) | 3 (orta) | Yavaş geçiş: önce **yeni kod** servisi kullanır, sonra `getEnv()` `trigger_error` ile işaretlenir, **en son** kaldırılır; repo geneli `grep -rn "getenv(\|\$_ENV\["` taraması CI'a eklenir (§5.1 adım 4) |
| **Vault'a geçiş ertelendi** — denetim/dynamic secret gereksinimi çıktığında `.env` yetmez | 2 (mümkün) | 3 (orta) | Açık kapsam dışı beyanı (§2.2i) + tetikleyiciler yazılı; ayrı ADR (≥ 088) ile değerlendirilir; bu ADR Vault'a **karşıt değil, ön koşul** (önce doğru davranış) |
| **Ölü `phpdotenv` bağımlılığı** — iki parser beklentisi, kafa karışıklığı | 3 (olası) | 2 (düşük) | `composer.json`'dan kaldırma + `.ai/.agents/*.md` düzeltmeleri (§5.1 adım 5-6); tek parser: `EnvParser` |
| **Vault iddia-karar çelişkisi** — `data-engineer.md` ADR-015'i "cache stratejisi" der | 4 (çok olası) | 2 (düşük) | §1.1'de işaretli; düzeltme §5.1 adım 6 (append-only ayrı işlem); `brain.md:970`/`keys.md:250`/`index.md:52` zaten hizalı |

### 4.4 Vault Çapraz Referans

| Kaynak | İlişki |
|--------|--------|
| [[ADR-002-pdo-mandatory-no-orm]] | Config/şema katmanı **framework ve bağımlılık eklenmeden** yazılır; `phpdotenv` bu yüzden kullanılmaz (§3 1) |
| [[ADR-005-ultrathink-protocol]] | Fail-fast'in **ruhu**: kanıtsız iddia yok, doğrulama uygulama başlamadan; **REDACTED**: hata/log'a secret değeri asla (§2.2d-g) |
| [[ADR-008-bypass-auth-middleware]] | `FORCE_AUTH_BYPASS`/`BYPASS_*` yalnız `test` profilinde — profil ayrımı olmadan bypass güvenlik açığına döner |
| [[ADR-011-session-management]] | `SESSION_NAME`/`SESSION_LIFETIME`/`SESSION_COOKIE_DOMAIN`/`SESSION_SAVE_PATH` anahtarlarının sahibi; şema yalnız tip+zorunluluk tanımını alır, politikayı almaz |
| [[ADR-014-multi-db-migration-strategy]] | "İlk hata → dur" fail-fast disiplini + `.ai/log.md` append-only yazım kuralı bu ADR'nin süreç dayanağı |
| [[../index]] | Satır 52 `[[ADR-015-env-parser-strategy]]` — slug eşleşmesi ✅ (bu dosya dizindeki rezervasyonu doldurur) |
| [[../../architecture/k6-guvenlik/vault-secrets.md]] | Güvenlik mimarisi "vault/secrets" sayfası — §2.2g rotation prosedürü burayla hizalanır (kapsam: secret saklama), bu ADR kapsama alır |
| security/02-credentials (düz metin) | ⚠️ **VERIFICATION REQUIRED** — `.ai/`, `.claude/`, `.opencode/` altında `02-credentials` adlı dosya bulunamadı (glob = 0) → **wiki-link kurulmaz**, düz metin anılır; varsa sonraki vault işlemi bağlanır |
| ADR-085 (düz metin) | Composer/bağımlılık kararının sahibi olarak anılır; **`.ai/.decisions/**` altında dosyası YOK** (glob kanıtı) → wiki-link kurulmaz |
| `.ai/.agents/data-engineer.md:24,68,95,119,238,271` | "ADR-015 = cache stratejisi" iddiası → **çelişki**, düzeltme §5.1 adım 6 |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırması protokolü (diskte VAR ✅) |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **Şema dosyası (PLANNED → IMPLEMENTED):** `shared/config/.env.schema` — tüm anahtarlar: tip, zorunluluk, varsayılan, aralık/enum, profil; `.env.example` içerik bilgisi de buradan | Backend Architect + Security Engineer | 1 oturum |
| 2 | **Tek sınıf parser+validator (PLANNED → IMPLEMENTED):** `EnvSchema` + `EnvValidator::validate()` (§2.2a-c) + bootstrap'ta tek seferlik toplu hata raporu + exit ≠ 0 (§2.2d) | Backend Architect | 1-2 oturum |
| 3 | **Profil + Config kapısı:** `APP_ENV_MODE` profil olarak tekilleştir; `Config::get/getSecure` dışındaki her erişim taşınır (`OAuthManager.php:77`, `cors.php:11`, `AuthContainer.php:70`, `OriginCheckMiddleware.php:23`); `getEnv()` + `IConfigManager` karşılığı kaldırılır; `RuntimeBootstrap.php:9` bootstrap istisnası olarak **açıkça** işaretlenir | Backend Architect | 1-2 oturum |
| 4 | **`getenv`/`$_ENV` yasağı denetimi:** repo tarama scripti + CI adımı (`grep -rn "getenv(\|_ENV\["` → yalnız izinli dosya listesi); ihlal = PR reddi | DevOps Engineer + QA Engineer | 0.5 oturum |
| 5 | **Örnek üretimi + ölü bağımlılık:** `bin/config-doc.php --emit-schema/--emit-example/--check`; CI'da "üret ≠ dosya" kontrolü; `auth.coremusic.net/composer.json`'dan `vlucas/phpdotenv` kaldırma | Backend Architect + DevOps Engineer | 0.5 oturum |
| 6 | **Vault düzeltmeleri (append-only, ayrı işlem):** `data-engineer.md` "ADR-015 = cache/migration" iddiası → env parser (§1.1 çelişkisi); `security/02-credentials` ve `ADR-085` dosya varlığı doğrulanıp bağlanır ya da `⚠️` kalır | Vault Steward | 0.5 oturum |
| 7 | **Testler:** eksik anahtar → toplu hata + exit ≠ 0; `DB_PORT=abc` → hata (0 değil); bool/enum geçersiz değer → hata; üretim profilinde zorunlu anahtar eksikse kapanış; `development`+şema yoksa fallback uyarısı; sahte config ile `ConfigManager` testleri; **hata metninde secret değeri geçmez** (negatif assertion) | QA Engineer | 1-2 oturum |
| 8 | **Rotation runbook'u:** §2.2g beş adımı `.ai/architecture/k6-guvenlik/vault-secrets.md` ile hizala; 90 günlük periyot + iki kişi kuralı + `.ai/log.md` kayıt şablonu (anahtar adı, değer YOK) | Security Engineer + DevOps Engineer | 0.5 oturum |

### 5.2 Geri Dönüş Planı

1. **Parser/validator (adım 2):** `git revert` — `EnvParser`'ın eski davranışı (parse + `loadIntoEnv`) **geri gelir**; `.env` formatı hiç değişmediği için veri kaybı yok. Fail-fast kapatılır → sistem eski "sessiz varsayılan"a döner (bilinçli gerileme, §4.3 risk 1).
2. **`getEnv()` kaldırması (adım 3):** kaldırma **en son** yapılır; geri dönüş için `getEnv()` geçici olarak tekrar eklenir ve çağrı noktaları commit diff ile bilinir → tek `git revert` yeterli.
3. **CI yasağı (adım 4):** yasak kuralı kaldırılır, tarama adımı çıkar — kod etkilenmez.
4. **`phpdotenv` temizliği (adım 5):** `composer.json` revert + `composer update` → eski lock döner; kod zaten hiç kullanmadığı için etki yok.
5. **Şema silinirse:** `.env.schema` kaldırılır, `.env.example` elle kalır → sistem şemaya bakmadan çalışır (fallback zinciri devreye girer); **üretilen örnek komutu durur**, CI kontrolü atlanır.
6. **Rotation prosedürü geri alınamaz bir işlem değildir:** prosedür bir metin/kural olduğu için geri dönüş = yalnız kuralın askıya alınması; **edilmiş rotation'lar geri alınamaz** (yeni secret zaten aktif) → eski secret'a dönüş **yapılmaz**, iptal edilen değer kurtarılmaz (beklenen durum).
7. **Vault bozulması:** her adımdan sonra `.ai/log.md` append + `git diff --stat -- .ai/`; bozulma → `vault-utf8-writer.mjs repair` + `git checkout` (eski satıra dokunulmaz).

### 5.3 Debate Kaydı

| Alan | Değer |
|------|-------|
| Durum | **✅ TAMAMLANDI** — 3 tur / 20 persona · 18 kabul / 2 çekimser / 0 red → **KABUL** (2026-09-24) |
| Karar içeriği | Tümü **kullanıcı onaylı / Önerilen** (üst görev kapsamı) |
| Beklenen biçim | ADR-004/008/010/011/012/013/014 formatı — 3 tur / 20 persona (uygulandı — sonuç §7.1) |
| Tech Lead | **✅** — debate sonrası onay (2026-09-24) |
| Kural | Debate tamamlanmadan bu ADR `frozen` yapılmaz; sonuç §5.3/§7.1'e ve frontmatter `debate` alanına işlenir, `.ai/log.md` append ile kaydedilir |

### 5.4 Debate Şartları (Kabul Koşulları — 3/3)

| # | Şart | Kapsam | Sorumlu | Durum | Kanıt |
|---|------|--------|---------|-------|-------|
| 1 | **`getenv`/`$_ENV` ihlallerinin Config'e taşınması** | 5 doğrudan env erişimi (`OAuthManager.php:77`, `cors.php:11`, `AuthContainer.php:70`, `OriginCheckMiddleware.php:23`, `ConfigManager::getEnv` satır 51) DI `Config` servisine taşınır; mantık katmanında `getenv()`/`$_ENV[]` **yasak** (§2.2h) + CI taraması (§5.1 adım 3-4); `RuntimeBootstrap.php:9` bootstrap istisnası olarak açıkça işaretlenir | Backend Architect | ⏳ PLANNED → §5.1 adım 3-4 | Debate Tur 2 itiraz 1 |
| 2 | **`.env.schema` + `.env.example` otomatik üretimi** | `.env.schema` zorunlu anahtar/tip/aralık/profil kaydı; `.env.example` `bin/config-doc.php --emit-schema/--emit-example` ile **üretilir**, elle senkron **drift sayılır** (§2.2e; §5.1 adım 1-2-5) | Backend Architect + Security Engineer | ⏳ PLANNED → §5.1 adım 1-2-5 | Debate Tur 2 itiraz 2 |
| 3a | **`data-engineer.md` ADR-015 etiket düzeltmesi** | `.ai/.agents/data-engineer.md` satır 24, 30, 68, 95, 119, 238, 271, 343'teki "ADR-015 = cache stratejisi / migration aracı" yanlış atıfı → **ADR-015 = Env Parser Strategy** (§1.1 çelişki satırı, §4.3 risk 7) | Vault Steward | ✅ UYGULANDI (2026-09-24, bu debate turu) | Debate Tur 2 itiraz 1 → §1.1 |
| 3b | **Rotation prosedürünün dokümante edilmesi** | §2.2g 5 adımlık rotation runbook'u + 90 gün periyot + iki kişi kuralı `.ai/architecture/k6-guvenlik/vault-secrets.md` ile hizalanır ve `.ai/log.md` kayıt şablonuna bağlanır (§5.1 adım 8) | Security Engineer + DevOps Engineer | ⏳ PLANNED → §5.1 adım 8 | Debate Tur 2 şartı 3 |

**Tur 2 teyitleri (şart sayılmadı):** fail-fast erken kapanış riski → `development`/`test` fallback + `production` fail-fast ayrımı ile **teyit edildi** (§2.2d, §4.3 risk 1); vault geçişi → **ayrı ADR** (≥ 088) olarak kapsam dışı bırakıldı (§2.2i).

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[CLAUDE.md]] | Karar alt registry kuralı (accepted/ dizin sözleşmesi) |
| [[../index]] | Karar dizini — **satır 52** `[[ADR-015-env-parser-strategy]]` (slug eşleşmesi ✅) |
| [[../../CLAUDE.md]] | Vault ana sözleşmesi — 16 Hard Guardrail, REDACTED, Guardrail #16 |
| [[../../AGENTS.md]] | Onay akışı §10, frozen kuralı §25.3, routing §6 (`config/env` → Backend), §5 domain sınırı (`.env` dosyası → Security Engineer) |
| [[../../WORKFLOW.md]] | Debate/onay akışı başlangıcı |
| [[../../brain.md]] | Mimari karar özeti — satır 970 `ADR-015 | .env dosya okuma stratejisi` (bu ADR ile hizalı ✅) |
| [[../../keys.md]] | Keyword haritası — satır 250 `ADR-015 | env parser, .env | Infrastructure` ✅ |
| [[../../glossary.md]] | Terimler (`env parser`, `fail-fast`, `rotation`) — eklenecek |
| [[../../log.md]] | Audit trail — bu işlem tek satır append |
| Debate şartları (3/20 KABUL) | §5.4 — (1) `getenv`/`$_ENV` ihlallerinin Config tek kapıya taşınması · (2) `.env.schema` + `.env.example` otomatik üretimi · (3) `data-engineer.md` ADR-015 etiketi + rotation prosedürü dokümantasyonu |
| [[../../.templates/adr/adr-template.md]] | Bu ADR'nin şablonu (Guardrail #16, 7 bölüm + §1.3 9 alan) |
| [[../../.templates/adr/adr-index.md]] | Satır 86 "ADR-015 · .env dosya okuma stratejisi · infra" ✅ |
| [[../../.templates/adr/adr-security-template.md]] | Satır 258 ".env okuma stratejisi · anahtar vault'a YAZILMAZ (REDACTED) · ADR-015" ✅ |
| [[../../architecture/k6-guvenlik/vault-secrets.md]] | Secret saklama/vault güvenlik sayfası — rotation prosedürü burayla hizalanır |
| [[ADR-002-pdo-mandatory-no-orm]] | Bağımlılıksız parser kararı (dosya diskte VAR ✅) |
| [[ADR-005-ultrathink-protocol]] | Doğrulama + REDACTED ruhu (dosya diskte VAR ✅) |
| [[ADR-008-bypass-auth-middleware]] | Profil/`test` şartlı bypass (dosya diskte VAR ✅) |
| [[ADR-011-session-management]] | `SESSION_*` sahibi (dosya diskte VAR ✅) |
| [[ADR-014-multi-db-migration-strategy]] | Fail-fast + append-only süreç dayanağı (dosya diskte VAR ✅) |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırması protokolü (diskte VAR ✅) |
| `shared/src/Config/EnvParser.php` · `shared/src/Config/ConfigManager.php` · `auth.coremusic.net/config/constants.php` | Uygulanacak/hedef kod (§1.1 satır kanıtları) |
| `shared/config/.env.example` · `.gitignore` (satır 6-9, 68-70) | Örnek dosya + git kuralı (IMPLEMENTED ✅) |
| security/02-credentials · ADR-085 (düz metin) | ⚠️ VERIFICATION REQUIRED — diskte dosya yok, wiki-link kurulmaz |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı — "ADR-015'i sıfırdan yaz"; karar içeriğinin tamamı Önerilen/onaylı) | 2026-09-24 | ✅ |
| Tech Lead | ✅ — debate KABUL (3 tur / 20 persona, 18/2/0) | 2026-09-24 | ✅ |
| Arch Lead | ⏳ | ⏳ | ⏳ |

### 7.1 Debate Kaydına İlişkin Not

| Alan | Değer |
|------|-------|
| Biçim | ADR-004/008/010/011/012/013/014 formatı — 3 tur / 20 persona |
| Debate | **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** — 2026-09-24 (frontmatter `debate` alanı ile aynı) |
| Tur 1 | **20 persona** — kod bulguları: `EnvParser` **VAR** (şema/fail-fast **YOK** → PLANNED) · `ConfigManager` + php-di **VAR** · `.gitignore` ✓ (`.env` hiç commit edilmemiş, `git log --all --diff-filter=A` = 0) · **5 doğrudan env erişimi** (`OAuthManager.php:77`, `cors.php:11`, `AuthContainer.php:70`, `OriginCheckMiddleware.php:23`, `RuntimeBootstrap.php:9`) · `vlucas/phpdotenv` composer'da declare ama kurulu değil. Oy: **15 kabul/neutral, 4 uyarı**; **Critic:** "tek erişim kapısı" kararı **5 dosyada ihlal**. |
| Tur 2 | **İtiraz → çözüm:** (1) 5 `getenv`/`$_ENV` ihlali → Config tek kapıya geçiş + mantık katmanında yasak → **şart 1**; (2) `.env.schema` yok → şema + `.env.example` otomatik üretimi → **şart 2**; (3) fail-fast erken kapanış → `development`/`test` fallback + `production` fail-fast ayrımı **teyit edildi** (§2.2d, §4.3 risk 1); (4) vault geçişi → **ayrı ADR** (≥ 088) — §2.2i kapsam dışı. |
| Tur 3 | **Oy: 18 kabul / 2 çekimser / 0 red → KABUL** |
| Sonuç | **KABUL — 3 şart (§5.4):** (1) `getenv` ihlallerinin Config'e taşınması, (2) `.env.schema` + örnek otomatik üretim, (3) `data-engineer.md` etiket düzeltmesi + rotation prosedürü dokümante |
| Tech Lead | **✅** (2026-09-24) — debate sonrası onay |
| Durum | `accepted` (kullanılabilir — **frozen YOK**; §7 üç satırı ✅ olmadan `frozen` yapılmaz) |

---

*1.0.0 | 2026-09-24 | Created*
*Authority: ADR-015 Karar Metni — CoreMusic Architecture Decision Record*
*Mode: Red Team · Human Mode · Truth Mode*
