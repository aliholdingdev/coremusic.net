---
title: "CoreMusic — ADR-082: Dev/Staging Environment Architecture (dev.coremusic.net PLANNED · ortam ayrımı APP_ENV_MODE · ayrı DB kümeleri · cookie/domain scope ayrışı · deploy dev→staging→prod PLANNED — deploy job 0 · staging enum'da YOK · subdomain tablosunda dev satırı YOK · 18 DB şeması ADR-040 — kod/DB PLANNED, vault kanıtı IMPLEMENTED)"
type: "architecture-decision"
category: "infrastructure"
date: "2026-10-01"
updated: "2026-10-01"
version: "1.0.0"
status: "accepted"
authority: "SSOT — CoreMusic dev/staging ortam mimarisi kararı: (a) **dev ortamı** = `dev.coremusic.net` (ADR-039 §2.1 satır 11 — **PLANNED**, dizin YOK) ve **staging** = kod deploy hedefi; development (lokal .env) ↔ staging ↔ production üçlemesi `APP_ENV_MODE` (`development|production|test` — `shared/config/.env.example:9`, `home.coremusic.net/config/constants.php:22-25`) ile ayrılır; **staging enum'da YOK** (dokümanlarda `APP_ENV: \"staging\"` yalnız `.ai/architecture/k13-cicd/staging-environment.md:104` = PLANNED) → enum genişlemesi debate şartı; (b) **kod/DB izolasyonu** — kod **aynı repo/branch ayrımıyla**, DB **her ortam için ayrı 18 DB kümeleri** (ADR-040 18-DB şeması); staging/prod veri sızıntısına karşı ortak DB **yasak**; (c) **domain scope/cookie/URL** — `SESSION_COOKIE_DOMAIN=.coremusic.net` (`.env.example:35`) dev/staging'e de yayıldığı için **ortama göre cookie scope ayrışı** kararlaştırılır (prod cookie'si dev host'una sızmaz); URL katmanı + domain varsayılanları **ortamlar arası aynı** (ADR-045 domain varsayılanları + ADR-046 URL birincil kalıcılık — host değişir, route sözleşmesi değişmez); (d) **deploy akışı** dev → staging → prod, **bugün PLANNED** (`.github/workflows/` = `ci.yml` lint/test + `secret-scan.yml` gitleaks = 2 dosya, **deploy job 0**, rsync/deploy script 0); gizli veri `.env` gitignore (`:6,:9`) + `.env.example` şablon + ADR-034 credential vault SSOT; (e) **sınır** = ADR-058 · 064 · 039 · 045 · 046 · 040 · 015 — tekrar yok"
kaynak: "Disk kanıtı taraması (2026-10-01: `dev.coremusic.net` grep tüm repoda **7 isabet = hepsi vault** → ADR-039-…:127 (11. servis satırı: \"dizin YOK; ADR-082 diskte + index.md'de YOK · PLANNED\") + ADR-023-persona-driven-testing.md 6 satır (§2.2f düz metin notu) · **kodda/domain.php'de 0** · `shared/config/domain.php:7-15` = **7 subdomain** (auth/home/assets/music/admin/media/api — **dev YOK**) · `shared/src/Config/CLAUDE.md:45-55` domain tablosu = **9 satır, dev satırı YOK** (ADR-058/064 bu tabloyu kullandı) · env bayrakları: `APP_ENV_MODE` (`home.coremusic.net/config/constants.php:21-28` enum `development|production|test` + geçersizse fail-fast `:25` · `DEBUG_MODE` `:28` · `auth.coremusic.net/index.php:59` `$isProductionEnv`) + `APP_DEBUG` + `TEST_MODE` + `FORCE_AUTH_BYPASS` (ADR-008 kill, `.env.example:17`) · `staging` grep `*.php` = **0** (yalnız k13-cicd PLANNED dokümanlar: `staging-environment.md:104` `APP_ENV: \"staging\"` · `kubernetes-deploy.md:215` `APP_ENV: \"production\"` · `docker-build.md:189` `APP_ENV=local`) · `.env` diskte **2 gerçek** (`api.coremusic.net/config/.env`, `auth.coremusic.net/config/.env` — gitignore `:6,:9`, içerik okunmadı/REDACTED) + `.env.example` **3** (`api`, `auth/config`, `shared/config`) · `.gitignore:68-70` `.ai/.env.*` + `!.env.example` · deploy yüzeyi: `.github/workflows/` = **2 dosya** (`ci.yml` = composer install + phpstan/ PHPUnit lint-test pipeline, `secret-scan.yml` = gitleaks) — **deploy/rsync/ssh adımı 0** (`ci.yml:3` yalnız plan referansı) · `bin/` = `api-key`, `api-key-create.php` (deploy script YOK) · `.workflows/deployment.md` = onay akışı dokümanı · `.ai/architecture/k13-cicd/` = 14 PLANNED doküman (staging-environment, kubernetes-deploy, docker-build, rollback-strategy…) · ADR-064 kaynak sayımı: `domain.php`=7 subdomain, `Config/CLAUDE.md`=9 satır, 11 alan servisi (dev dahil) · ADR-039:127 dev satırı **PLANNED** · ADR-058 `/validate-key` Origin muafiyeti + `HomeAuthBridge` doğrudan HTTP · ADR-045 domain varsayılanları + ADR-046 URL katmanı · ADR-040 18-DB · ADR-015 env parser · ADR-034 credential vault (disk başlığı \"Credential Vault Normalization — tek SSOT credential deposu\"; **görev etiketi \"kademeli config\" disk başlığıyla uyuşmuyor** → ⚠️ · ADR-035 disk başlığı \"System Prompt Engineering\", görev etiketi \"Vault SSOT\" → ⚠️) · `.ai/.decisions/index.md:104` = ADR-081, `:105` = ADR-083 → **ADR-082 satırı YOK** · şablon gerçek yol `.ai/.templates/adr/adr-template.md` (görevdeki `.ai/templates/…` YOK — ADR-077/078 ile aynı sınıf rapor) · `.claude/skills/prompt-maker/references/10-web-research-protocol.md` Test-Path True) + web araştırması (**3 sorgu / ~30 isabet**)"
governance: "Red Team → Human Mode → Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-082: Dev/Staging Environment Architecture

> **Durum:** accepted (**debate ✅ TAMAMLANDI**) — **Tarih:** 2026-10-01 — **Debate:** ✅ **TAMAMLANDI** (3 tur / 20 persona — **18/2/0 KABUL** · 3 şart §5.1/11-13) — **Tech Lead:** ✅ — **Arch Lead:** ⏳
> **Karar serisi:** `.ai/.decisions/accepted/` — **Slug:** `ADR-082-dev-environment` — **Dizin satırı YOK** (`.ai/.decisions/index.md` `:104` = ADR-081, `:105` = ADR-083 → **index satırı bu işlemde EKLENMEDİ, son sıfırlamaya ertelendi → §5.1/9 + §7.1**)
> **İlgili kararlar:** [[ADR-039-7-service-platform-architecture]] (11 servis envanteri — **dev = satır 11, PLANNED**, bu ADR o satırın mimarisini yazar) · [[ADR-064-electronics-platform-architecture]] (11 alan servisi + sayım çelişkisi 13/11/9/7 — subdomain/servis tabloları hizası) · [[ADR-058-centralized-auth-service]] (tek auth noktası + `domain=.coremusic.net` SSO ön koşulu + `/validate-key` Origin muafiyeti — cookie scope ayrışının dayanağı) · [[ADR-045-multi-domain-view-mode-architecture]] (domain bazlı varsayılanlar — ortamlar arası **aynı** kalır) · [[ADR-046-cross-view-state-preservation]] (3 katmanlı URL/sessionStorage/servis katmanı — ortamlar arası **aynı** kalır) · [[ADR-040-database-authority]] (18 DB sahiplik matrisi — her ortam bu şemanın kopyasını taşır) · [[ADR-015-env-parser-strategy]] (`APP_ENV_MODE` enum + fail-fast + profil tekilleştirme) · [[ADR-034-credential-vault-normalization]] (gizli veri tek SSOT deposu + CI secret scan kapısı) · [[ADR-035-system-prompt-engineering]] (prompt dosyaları gizli veri sınıfı sınırı) · [[ADR-043-auth-subdomain-consolidation]] + [[ADR-011-session-management]] (cookie domain konsolidasyonu) · [[ADR-008-bypass-auth-middleware]] (`FORCE_AUTH_BYPASS` production kill) · [[ADR-004-multi-domain-spa]] (subdomain SPA iskeleti — dev yeni domain eki) · [[ADR-023-persona-driven-testing]] (test kişisi #8 \"yazılımcı\" — dev kararı bu ADR'ye devredildi) · [[ADR-014-multi-db-migration-strategy]] (migration tek kapısı — ortam DB kurulumu) · [[ADR-081-multi-provider-data-sync]] (sync sınırı) — karar dizini: **satır YOK** (§7.1).
> **⚠️ VERIFICATION REQUIRED:** `dev.coremusic.net` **kodda/domain.php'de/config tablosunda 0** (yalnız vault: ADR-039:127 + ADR-023) → subdomain DNS/konfigürasyonu **kurulmamıştır** · **staging subdomain/adresi hiç yazılmamış** (k13-cicd dokümanı PLANNED; `APP_ENV_MODE` enum'unda `staging` **YOK**) · deploy pipeline **deploy job = 0** (ci.yml yalnız lint/test; CI'ın geçtiği doğrulanmadı — ADR-064/AGENTS §25.2 ile aynı dürüst etiket) · gerçek `.env` dosyalarının **içeriği okunmadı** (REDACTED) → environment değerleri yalnız `.env.example`'dan · `dev.coremusic.net` için **stack** (hangi teknoloji, hangi DB, hangi port) ADR-039:127'de `⚠️ VERIFICATION REQUIRED` → bu ADR'de de sabitlenmedi (**debate şartı §5.1/6**) · `ADR-080` + `ADR-083`–`ADR-088` (dizin satırı var/dosya YOK — 079-082 hariç) + **14 atlanan boşluk** `ADR-051/053/054/055/057/060` + `ADR-065`–`ADR-071` + `ADR-080` (dosya **ve** dizin satırı YOK; ADR-080 wiki-link **0** · dosya **0** · index satırı **0** (yalnız düz metin boşluk notu — 49 isabet)) → §5.1/10 + §7.1 · `APP_ENV_MODE` enum genişlemesi (`staging`) · cookie scope ayrışının ADR-058 `domain=.coremusic.net` SSO ön koşuluyla çatışması · görev etiketleri ↔ disk başlıkları (ADR-034/035) uyuşmazlığı.
> **Bölüm sınırı (kenetli):** ADR-039 **servis envanteri (dev satırı 11)**, ADR-064 **platform/cihaz-servis sayımı**, ADR-058 **auth + cookie domain SSO + validate-key**, ADR-045 **domain varsayılanları**, ADR-046 **URL katmanları**, ADR-040 **DB sahipliği/18 şema**, ADR-015 **env parser/enum**, ADR-034 **credential SSOT**, ADR-043/011 **cookie konsolidasyonu** yazdı; **dev/staging ortam mimarisi (ortam üçlemesi, DB izolasyonu, ortam-bazlı cookie scope, deploy akışı) bu ADR'nindir** — hiçbiri yeniden yazılmaz.
> **Frozen değil:** bu dosya ADR-001–037 frozen kapsamı dışındadır; kural 7'deki \"yeni ADR ≥ 088\" ile 082 slotu arasındaki **numara çakışması** ADR-061–064/072–079 künyelerinden tekrar raporlanır, düzeltilmez.

---

## 1. Bağlam (Context)

CoreMusic'in prod yüzeyi tek bir ortam gibi yaşıyor: `domain.php` 7 subdomain, Config tablosu 9 satır, `APP_ENV_MODE` üç değer (`development|production|test`) — ama **staging yok, deploy yok, dev subdomain'i vault'ta bir satır olarak duruyor**. `dev.coremusic.net` 2026-09-25 kullanıcı duyurusuyla ADR-023'e düz metin not olarak girdi (\"yazılımcılar için işletim sistemi benzeri arayüz\") ve kararı bu ADR'ye devredildi; ADR-039 onu 11. servis olarak `PLANNED` yazdı (dizin YOK). Aynı anda CI yalnız lint/test üretiyor (`ci.yml` + `secret-scan.yml`), hiçbir kod prod'ga otomatik gitmiyor; `staging` kelimesi kodda **0**, yalnızca `.ai/architecture/k13-cicd/` PLANNED dokümanlarında geçiyor. Bu ADR beş boşluğu tek yerde bağlar: (a) dev ortamı mimarisi ve subdomain yapısı, (b) kod/DB izolasyonu, (c) domain scope/cookie/URL stratejisi, (d) deploy akışı + gizli veri, (e) sınır (tekrar yok).

### 1.1 Mevcut Durum (disk kanıtı — dürüst etiket, 2026-10-01 taraması)

| # | İddia | Kanıt | Etiket |
|---|-------|-------|--------|
| 1 | `dev.coremusic.net` gerçek kullanım | grep tüm repoda **7 isabet** → **hepsi vault**: `ADR-039-…:127` (`dev.coremusic.net (PLANNED)` · dizin YOK · `ADR-082` diskte+index'te YOK) + `ADR-023-…` 6 satır (`:97,:123,:181,:206,:268,:314` — §2.2f düz metin, \"ADR-082'ye bırakıyoruz\") | ⚠️ **kod 0 / config 0 / domain.php 0** → dev **yalnız vault kararıydı**, şimdi bu ADR ile yazılıyor |
| 2 | Subdomain tabloları — dev satırı var mı? | `shared/src/Config/CLAUDE.md:45-55` = **9 satır** (coremusic · music · admin · download · media · auth · home · car · studio) → **dev satırı YOK** · `shared/config/domain.php:7-15` = **7 subdomain** (auth/home/assets/music/admin/media/api) → **dev YOK** · repo kök dizin listesi = 5 subdomain dizini (`api/auth/assets/home/media` — `dev.coremusic.net` dizini YOK) | ❌ **iki tabloda da dev yok** → ekleme bu ADR'nin uygulama adımı (§5.1/2) — bu ADR vault'ta karar yazar, kod tablosuna dokunmaz |
| 3 | Environment bayrakları | `APP_ENV_MODE` enum `development\|production\|test` (`home.coremusic.net/config/constants.php:22-25` + fail-fast `:25`; `.env.example:9`) · `DEBUG_MODE = APP_ENV_MODE !== 'production'` (`:28`) · `APP_ENV_MODE=production → bayrak hiç okunmaz` (`:39`) · `auth.coremusic.net/index.php:59` `$isProductionEnv` · `APP_DEBUG` (`.env.example:13`) · `TEST_MODE` (`:16`) · `FORCE_AUTH_BYPASS` (`:17`, ADR-008 kill: env=production → bypass kapalı) · `staging` grep `*.php` = **0** | ✅ **development/production/test IMPLEMENTED** · ❌ **staging YOK** (enum + kod) → §5.1/6 debate |
| 4 | `.env` / config ayrımı | `.env` diskte **2 gerçek** (`api.coremusic.net/config/.env`, `auth.coremusic.net/config/.env` — **gitignore `:6`,`:9`**, içerik okunmadı → REDACTED) · `.env.example` **3** (`api/.env.example`, `auth/config/.env.example`, `shared/config/.env.example`) · master şablon `shared/config/.env.example` = 96 satır: `DB_NAME` per-subdomain (`:29`), `SESSION_COOKIE_DOMAIN=.coremusic.net` (`:35`), `AUTH_URL/MUSIC_URL/…` (`:51-55`), `APP_PEPPER` zorunlu (`:41`), OAuth credential alanları boş (`:57-96`) · `.gitignore:68-70` `.ai/.env.*` + `!.env.example` | ✅ **gizli veri yönetimi gitignore + example şablonla IMPLEMENTED**; **ortam-bazlı .env seti yok** (tek şablon, ortam ayrımı yalnız `APP_ENV_MODE` satırıyla) |
| 5 | Deploy yüzeyi | `.github/workflows/` = **2 dosya**: `ci.yml` (PHP lint/composer install/phpstan/PHPUnit — **deploy adımı 0**, `:3` yalnız plan referansı) + `secret-scan.yml` (gitleaks, `fetch-depth: 0`, `.gitleaks.toml` **VAR** (579 bayt, tracked `a5b680c`; allowlist yalnız test dosyası; `secret-scan.yml:5` yorumu "YOK" = **eski yorum, disk kazanır**) · `bin/` = `api-key`, `api-key-create.php` — deploy/rsync script **0** · repo geneli `rsync` grep = **0** · `.workflows/deployment.md` = onay akışı dokümanı · `.ai/architecture/k13-cicd/` = **14 dosya** (staging-environment · kubernetes-deploy · docker-build · rollback-strategy · ci-pipeline · cd-pipeline …) | ⏳ **CI lint/test IMPLEMENTED, deploy PLANNED** (deploy job 0; CI'ın geçtiği doğrulanmadı) |
| 6 | İlişkili ADR disk durumu | `ADR-039/064/058/045/046/040/015/034/035/043/011/008/004/023/014/081` glob = **hepsi diskte** · `ADR-080` + `ADR-083`–`088` = dosya YOK (083-088 satırı var) · `ADR-051/053/054/055/057/060` + `ADR-065`–`071` + `ADR-080` = **14 boşluk** (dosya + satır YOK; 080 wiki-link **0** · dosya **0** · index satırı **0** (düz metin boşluk notu 49 isabet)) | ✅ wiki-link hedefleri doğrulandı / eksikler → düz metin + ⚠️ |
| 7 | Dizin satırı | `.ai/.decisions/index.md` `:104` = `[[ADR-081-multi-provider-data-sync]]` · `:105` = `[[../../raw/brain.md]] ADR-083-spa-router` | ❌ **ADR-082 satırı YOK** → bu işlemde eklenmedi (talimat), §5.1/9 + §7.1 raporu |
| 8 | Domain/URL karar zemini | ADR-045: URL birincil kalıcılık + **domain bazlı varsayılan** (müzik=liste, galeri=kart, admin=tablo) + kullanıcı override'ı · ADR-046: 3 katman (URL + sessionStorage + servis tercihi) + \"durum yoksa domain varsayılanı\" · ADR-058: cookie `domain=.coremusic.net` **SSO ön koşulu olarak korunur** | ✅ dosya var — bu ADR onlara hizalanır, tekrar etmez |
| 9 | Aşama/deploy dokümanları | `staging-environment.md:76-78,104-105` = `APP_ENV: \"staging\"` + `APP_DEBUG: \"true\"` · `kubernetes-deploy.md:215` = `APP_ENV: \"production\"` · `docker-build.md:189-190` = `APP_ENV=local` | ⏳ **PLANNED doküman** — kodda karşılığı **0** (`local` enum'da bile yok: enum `development\|production\|test`) → **env drift** bulgusu (§4.3/R4) |
| 10 | Şablon yolu | `.ai/.templates/adr/adr-template.md` (görev yolu) = **YOK**; gerçek = `.ai/.templates/adr/adr-template.md` (7 bölüm + §1.3 9 alan + §7 onay, Guardrail #16) · format referansı `.ai/.decisions/accepted/ADR-079-i18n-database-schema.md` diskte | ✅ gerçek şablon kullanıldı — görevdeki eksik nokta raporlandı (ADR-077/078 ile aynı sınıf) |

### 1.2 Sorun Tanımı

1. **Dev ortamı mimarisi yazılmamış.** `dev.coremusic.net` 2 kod satırı + 1 vault satırından ibaret: hangi amaçla kurulacağı (development ↔ staging ayrımı), hangi stack'i taşıyacağı (ADR-039:127 `⚠️`), subdomain tablolarına (Config `:45-55` 9 satır · `domain.php` 7 satır) ekleme yapılacak mı — hiçbiri karar değil.
2. **Ortam izolasyonu yok.** Tek `.env` şablonu, tek DB kümesi (18 DB — ADR-040), ortam bayrağı yalnız `APP_ENV_MODE` → staging ile prod aynı DB'ye yazabilir; veri sızıntısı kapısı açık.
3. **Cookie/domain scope ortam ayrımı taşımıyor.** `SESSION_COOKIE_DOMAIN=.coremusic.net` prod'a ait session cookie'sini `dev.coremusic.net`'e de gönderir (aynı üst alan) → dev'de prod oturumu, prod'da dev denemesi; ADR-058 SSO ön koşuluyla (paylaşımlı cookie kalıbı) **aynı üst alan içinde** çatışma riski.
4. **Deploy akışı yok.** `ci.yml` lint/test üretir, deploy job **0** → prod'ga geçiş elle ve tarifsiz; `k13-cicd` 14 dokümanı PLANNED; hangi aşamanın (dev→staging→prod) hangi kapıdan geçtiği yazılı değil.
5. **Env drift büyüyor.** Dokümanlar `staging`/`local` yazıyor, enum `development|production|test` — üç farklı sözlük; `staging` kodda 0 iken staging dokümanı `APP_ENV: "staging"` emrediyor → enum'a `staging` eklenmeden hiçbir staging deploy'u fail-fast'ten geçemez (`constants.php:25` geçersizse 500+exit).

### 1.3 Web'den Araştırma Raporu & Sonuçları

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "dev staging production environment architecture subdomain isolation best practices 2025" → (2) "environment secret management dev to staging to production deploy pipeline DB strategy separate database per environment" → (3) "session cookie domain scope sharing across subdomains staging environment SameSite cookie security pitfall" |
| Web Search **Konusu** | **(1)** dev/staging/prod ortam yapısı + izolasyon (klasör/db paylaşımı yasağı) · subdomain-isolation/host düzeni · tek VPS vs ayrı sunucu · **(2)** ortam-bazlı secret kapsamı (CI'a enjeksiyon, tekrar/drift önleme), dev→staging→prod kademeli promote pipeline'ı, **ortam başına ayrı veritabanı** stratejisi (migration'ın hangi ortamda çalıştırıldığı) · **(3)** cookie'nin üst alana (`parent domain`) yayılması riski, en spesifik subdomain'e scope kuralı, SameSite ↔ domain-scope ayrımı |
| Web Search **Bağlam** | CoreMusic: dev subdomain'i **kodda 0** (yalnız ADR-039/023 vault satırları), `APP_ENV_MODE` enum `development\|production\|test` (**staging YOK**), deploy job **0** (ci.yml lint/test + gitleaks), `.env` gitignore + 3 example (tek şablon, ortam ayrımı yok), `SESSION_COOKIE_DOMAIN=.coremusic.net`, 18 DB tek küme (ADR-040). Araştırmanın hedefi: ortam üçlemesinin ayrım eksenleri, ortam-bazlı DB/secret/deploy kanıtları ve cookie scope riski — CoreMusic'in iç kararlarını (port, DB adı, servis sayısı) değil **dış olgunluk kalıplarını** doğrulamak. Araştırma 2026-10-01'de 3 sorgu / ~30 isabet ile yapıldı; protokol `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (Test-Path True). |
| Web Search **Kısa Açıklama** | **(1)** Üç ortam **genuinely separate** olmalı — \"three folders sharing one database\" anti-patterndir (thinkby.ai); staging prod'un mümkün olduğunca kopyasıdır ama **gerçek prod verisi değil** (Kodekx: realistic, not real, data), dev hızlı iterasyon için izole sandbox'tır (Bunnyshell isolation+replicability; Signadot her geliştiricinin sandbox'ı); tek VPS'te çalıştırılabilir ama **networking/servis izinleri staging'de prod ile aynı** olmalı (Mergify IaC), izolasyon = bağımlılık çakışması ve outage koruması (eXact/exact.gg). **(2)** Environment **drift**'in ana sebebi kopyalanmış yapılandırmadır (DeployHQ); secret'lar **scope edilip** CI/CD'den güvenli enjekte edilir, kopyalanmaz (Doppler); Vault gibi araçlar **ortam başına ayrı instance** bekler (r/devops + HashiCorp Discuss); veritabanı için endüstri kalıbı: dev/staging'de migration **manuel/CI**, prod'da **yalnız CI gate** (Supabase discussions ×2), schema ayrımı (Replit: ayrı `production` schema). **(3)** Cookie **en spesifik subdomain'e** scope edilmeli — parent domain'e verilen cookie daha zayıf alt alanlara da gider (Acunetix), paylaşımlı üst alan cookie'si uygulamalar arası oturum/veri sızıntısı yaratır (Xebia, nhimg); SameSite domain-scope'un **yerine geçmez** (SO `:59785832`: \"Domain doesn't care about same-site, SameSite doesn't care about domain/subdomain scope\") ve `SameSite=None` zorunlu `Secure` ister (web.dev); `SameSite` alt alanlarda davranışını origin/site eşleşmesiyle belirler (PortSwigger, security.SE). |
| Web Search **Uzun Açıklama** | **(i)** Ortam mimarisi: thinkby.ai üç ortamın gerçekten ayrı olmasını (app/db/anahtar) şart koşar; back4app staging-prod izolasyonunu \"testler canlı veriye asla dokunmasın\" diye tanımlar; Kodekx staging'i prod'un kopyası + gerçekçi-ama-gerçek-olmayan veri + otomatik deploy; Bunnyshell dev'de izolasyon ve yeniden üretilebilirlik; Signadot ana staging = trunk/main, her geliştirici sandbox'ı = feature branch izolasyonu; DCHost tek VPS vs ayrı sunucu kıyasında riski network/bağımlılık çakışması olarak yazar; exact.gg aynı VPS'te bile izole çalışma dizinleri + hizalı konfigürasyon ister; mergify IaC: staging trafiği küçüktür ama **network kuralları ve servis izinleri prod ile birebir** olmalı. **(ii)** Pipeline/secret/DB: DeployHQ drift'i kopyalanmış config'in kaçınılmaz sonucu sayar ve tekilleştirme/senkron araçları önerir; Doppler staging secret'larını scope'layıp duplication+drift'i engellemeyi, secret'ı pipeline'a inject etmeyi anahtar sayar; massivegrid Git-temelli deploy + Docker akışını; alamrafiul.com kademeli promote (dev→staging→prod) görselini; AWS DevOps çok-ortam pipeline'larında approval gate'leri; Supabase 542'de dev DB migration'ı elle, staging/prod'ı CI'da; Supabase \"Managing Environments\" şema değişikliklerinin dev→staging→prod sırayla otomatik test edilmesi; Replit ayrı `production` schema; HashiCorp/r/devops Vault'ın ortam başına instance beklemesi (tek instance çok-ortam riskli). **(iii)** Cookie: Acunetix \"en spesifik subdomain\" kuralını açık ihlal olarak tanımlar; Xebia cookie-domain tuzaklarını (paylaşımlı auth/session → güvenlik + güvenilirlik riski) yazar; nhimg cookie-scope'ün bir uygulamanın cookie'sini daha zayıf alt alanlara otomatik göndermesini risk sayar; SO 59785832 domain-scope ile same-site'ın **bağımsız** olduğunu kanıtlar (aynı üst alan = her ikisi de devreye girer); web.dev `SameSite=None`+`Secure` zorunluluğunu; PortStack/PortSwigger SameSite bypass'larını; andrewlock SameSite'ın sınırlarını. |
| Web Search **Paragraf Veri Uzun** | **3 sorgu / ~30 isabet** (her sorgu ~10): **(1)** thinkby.ai \"dev-staging-production\" · back4app \"staging vs production isolation\" · Kodekx \"best practices for startups\" · mergify \"IaC 2025\" · r/devops \"prod-like dev environments\" · Bunnyshell \"Dev, Test, Prod 2026\" · DCHost \"one VPS or separate servers\" · GoReplay microservices · exact.gg \"VPS for Dev and Staging\" · Signadot \"Staging Bottleneck\". **(2)** DeployHQ \"keeping environments in sync\" · massivegrid \"development and staging on VPS\" · r/devops \"Secret Management across environments/Vault\" · Supabase Docs \"Managing Environments\" · Replit \"DEV STAGING AND PROD\" · Doppler \"securing staging secrets\" · GitHub Supabase #542 \"multiple environments and migrations\" · NareshIT \"Multi-Environment Deployments AWS DevOps\" · HashiCorp Discuss \"Vault multiple environments\" · alamrafiul \"Multi-Environment Pipeline: Dev → Staging → Production\". **(3)** Medium \"SameSite and Subdomains\" · Acunetix \"session cookies scoped to parent domain\" · nhimg \"session cookie scope\" · r/node cross-domain cookies · web.dev \"SameSite cookies explained\" · security.SE \"same-site for cookies with subdomains\" · PortSwigger \"bypassing SameSite restrictions\" · Xebia \"caveats and pitfalls of cookie domains\" · SO 59785832 \"SameSite, frames, subdomains\" · andrewlock \"Understanding SameSite cookies\". |
| Web Search **Sonucu** | **(1) Karar destekleniyor:** ortamlar **gerçekten ayrı** olmalı (db/anahtar paylaşımı yasak) + staging prod'un kopyası ama gerçek veri değil + staging'de networking/servis izinleri prod ile eş + dev hızlı iterasyon izolasyonu → CoreMusic kararı (ayrı 18-DB kümeleri, deploy gate, dev sandbox) kalıba uyar. **(2) Karar destekleniyor:** secret **scope** + CI enjeksiyonu (kopya yok), ortam başına ayrı veri deposu/instance, **migration'ın yalnız CI gate'ten** geçmesi, kademeli promote + approval → ADR-034 credential SSOT + `.env.example` + k13-cicd dokümanları bu kalıba bağlanır; **env drift** uyarısı doğrudan CoreMusic `staging`/`local`/enum üçlüsüne uyar. **(3) Karar destekleniyor + çatışma açığa çıktı:** cookie en spesifik scope'a gitmeli (Acunetix) fakat **CoreMusic'te `domain=.coremusic.net` ADR-058/043/011 tarafından SSO ön koşulu olarak bilinçli korunuyor** → çözüm \"ortam **alan adı** ayrışır\" (dev.host'ları için scope daraltma, prod SSO aynen) — SameSite domain-scope'un yerine geçmez (SO 59785832) → iki mekanizma birlikte yazılır. **Karşıt/itiraz bulgu:** dış kaynaklar CoreMusic'in hangi subdomain'in staging olduğu, dev stack'i, portları, DB adlandırma desenini ve deploy aracını **seçemez** (iç karar) → o kararlar bu ADR'nin §2'sinden, kaynaklar yalnız **kalıp** verir; ayrıca \"tek VPS\" seçeneği kaynaklarda reddedilmiyor (DCHost) → sunucu topolojisi bu ADR'de sabitlenmez, `⚠️ VERIFICATION REQUIRED`. |
| Web Search **Alınan Karar** | **(a) Ortam üçlemesi:** `development` (lokal, `APP_ENV_MODE=development`) ↔ `staging` (deploy hedefi — **enum'a eklenecek**, debate şartı) ↔ `production`; her ortam **kendi .env + kendi 18 DB kümeleri + kendi secret kapsamına** sahip (web 1+2); staging'e gerçek prod verisi **kopyalanmaz** (maskelenmiş/gerçekçi veri). **(b) Subdomain yapısı:** `dev.coremusic.net` ADR-039 satır 11'e eklenir (PLANNED → uygulama ile IMPLEMENTED); Config `:45-55` ve `domain.php` tablolarına satır **kod fazında** eklenir (bu ADR vault'ta karar verir); staging'in adresi (subdomain mi, aynı host + path mi) **debate şartı** (diskte hiç yazılmadı). **(c) Cookie/domain scope:** prod'da `domain=.coremusic.net` **korunur** (ADR-058 SSO — web 3'ün \"en spesifik\" kuralına bilinçli istisna, gerekçe = SSO); dev/staging host'ları için scope **daraltılır** (host'a özel cookie) — prod cookie'si dev'e sızmaz; `SameSite` + `Secure` scope'tan **bağımsız** ikinci katman olarak yazılır (web 3). **(d) URL/domain varsayılanları:** ADR-045 domain varsayılanları + ADR-046 URL katmanı **ortamlar arası birebir aynı** — host dışında hiçbir rota/parametre değişmez (env drift'e karşı tek koruma). **(e) Deploy akışı:** dev → staging → prod kademeli promote; **deploy adımları kapıya bağlanır** (ci.yml'e deploy job eklenmeden prod'ga geçiş yok); migration **yalnız ADR-014 tek kapısından + CI gate** (web 2 Supabase); secret yalnız `.env`/CI secret store'dan (`.gitleaks.toml` diskte VAR — allowlist yalnız test dosyası). **(f) Sınır:** ADR-058/064/039/045/046/040/015/034/043/011/008/004/023/014/081 tekrar yazılmaz. |
| Web Search **Sonuç** | **3/3 araştırmada karar destekleniyor** (ortam izolasyonu + secret/pipeline/DB + cookie scope). **Dört gerilim açıkça debate'e taşındı:** (1) `APP_ENV_MODE`'a `staging` eklenip eklenmeyeceği (enum `constants.php:22-25` fail-fast — dokümanlar `staging`/`local` yazıyor) · (2) staging'in **adresi** (subdomain vs host+path) · (3) dev/staging stack'i + sunucu topolojisi (tek VPS vs ayrı — DCHost reddetmiyor) · (4) cookie scope daraltmasının ADR-058 SSO ön koşuluyla **aynı üst alan içinde** nasıl birleşeceği. Ayrıca raporlandı: deploy job **0** (CI lint/test — geçtiği doğrulanmadı) · `secret-scan.yml` yorumu eski (dosya diskte VAR) · 14 atlanan boşluk + `ADR-080`–`088` dosya boşlukları + `ADR-082` index satırı YOK + görev etiketi↔disk başlığı uyuşmazlığı (ADR-034/035). **⚠️ VERIFICATION REQUIRED:** gerçek `.env` değerleri (okunmadı, REDACTED) · deploy aracının seçimi · DNS/host kurulumu · CI'ın geçip geçmediği. |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| In-Place Refactoring (kural 1) | `domain.php` ve `shared/src/Config/CLAUDE.md` tablolarına satır **eklenir, mevcut satırlar değiştirilmez**; dosya adı değişmez — ADR-064 sayım tabloları (7 subdomain / 9 satır / 11 servis) çelişkisiz kalır |
| ADR-058 SSO ön koşulu | `SESSION_COOKIE_DOMAIN=.coremusic.net` prod'da **korunur** (kimlik paylaşımının dayanağı); scope daraltması yalnız dev/staging host'ları içindir ve bu ADR'de kural olarak yazılır — kod ADR-058'in onayı olmadan değiştirilemez |
| ADR-015 enum + fail-fast | `APP_ENV_MODE ∈ development\|production\|test` (`constants.php:22-25`); `staging` eklenmeden staging deploy'u **fail-fast ile durur** → enum değişikliği ADR-015 revizyonu/kapısı gerektirir |
| ADR-014 migration tek kapısı | Her ortamın DB kümeleri (18 DB) yalnız bu kapıdan kurulur/güncellenir; deploy pipeline'ı DDL'yi kendisi çalıştıramaz |
| ADR-040 18-DB sahiplik matrisi | Ortam izolasyonu **DB adlarını/sahipliğini değiştirmez** — her ortam aynı 18 şemanın kopyasını taşır |
| Kod = PLANNED | Deploy job 0 · dev dizini 0 · staging enum 0 · ortam-bazlı .env seti 0 → bu ADR **çalışan staging kurduğunu iddia etmez**; IMPLEMENTED/PLANNED ayrımı §1.1'de sabittir |
| Durum döngüsü | status `accepted` — debate ✅ TAMAMLANDI (18/2/0) · Arch Lead ⏳ → Frozen yapılmaz (şablon §4.5: onay 3/3 + §1.3 dolu + §5.2 yazılı) |

---

## 2. Karar (Decision)

Dev/staging ortam mimarisi **beş başlıkta** kararlaştırılır: (a) dev ortamı mimarisi (amaç + subdomain yapısı), (b) kod/DB izolasyonu, (c) domain scope/cookie/URL stratejisi, (d) deploy akışı + gizli veri, (e) sınır. Karar **vault'ta yazılır**; kod/DCM değişiklikleri §5.1 kapılarına bağlanır (bugün deploy **0** dürüst etiketle PLANNED kalır).

### 2.1 Neden Bu Seçenek?

Çünkü mevcut zemin tersinemez değil ama boş: dev subdomain'i 2 vault satırı (ADR-039:127 + ADR-023), env ayrımı tek bayrak (`APP_ENV_MODE`), deploy **0**, cookie tek scope. Üç ortamın ayrı DB/secret taşıması (web 1+2) endüstri kalıbıdır ve staging-prod veri sızıntısını kapatmanın tek yolu; cookie'de ise iki zorunlu gereksinim çatışır — web 3 \"en spesifik scope\", ADR-058 \"`.coremusic.net` SSO ön koşulu\". Çözüm alan adı **boyunca** değil **ortam boyunca** ayrışmak: prod SSO aynen kalır, dev/staging host'ları dar scope alır — hem ADR-058 bozulmaz hem prod cookie'si dev'e sızmaz. Deploy tarafında big-bang pipeline kurmak yerine mevcut `ci.yml`'in (lint/test + gitleaks) üzerine kapı-adımı eklemek, ADR-043'ün \"flag day yasak, fazlı geçiş\" dersiyle aynı yöntemdir.

### 2.2 Teknik Detaylar

**(a) Dev ortamı mimarisi — amaç + subdomain yapısı:**

| # | Karar | Kanıt / bağ |
|---|-------|-------------|
| A1 | **Üç ortam:** `development` (lokal kod + `.env` = `APP_ENV_MODE=development`) · `staging` (kodun deploy edildiği ilk gerçek host) · `production` (canlı). Bayrak: `APP_ENV_MODE`; **`staging` enum'a eklenir** (debate şartı §5.1/6) — eklenene kadar staging, `development` değeri + ayrı host ile temsil edilir | `.env.example:9` · `constants.php:22-25` · web 1 |
| A2 | **`dev.coremusic.net` = geliştirici/sandbox yüzeyi** (ADR-023 §2.2f: \"yazılımcılar için işletim sistemi benzeri arayüz\") — ADR-039 §2.1 satır 11 olarak **kaldırılır, yeniden sayılmaz**; stack `⚠️ VERIFICATION REQUIRED` (debate §5.1/6) | ADR-039:127 · ADR-023:181 · katalog: dizin **YOK** |
| A3 | **Subdomain tablolarına ekleme (kod fazı):** `shared/src/Config/CLAUDE.md:45-55` (9 satır) ve `shared/config/domain.php:7-15` (7 satır) tablolarına `dev` satırı **eklenir, mevcut satırlar değişmez** (In-Place); `shared/AGENTS.md` \"config/domain.php listesini onaysız değiştirmek\" yasağı gereği **onaylı uygulama** (§5.1/2) | Config CLAUDE.md `:45-55` · domain.php `:7-15` |
| A4 | **Staging adresi DEĞİŞKEN** (subdomain `staging.coremusic.net` mı, `dev` host'unda ortam ayrımı mı) → diskte hiç yazılmadı → **debate şartı** (§5.1/6); uydurma host adı **bu ADR'de yazılmaz** | diskte `staging*.coremusic` grep = 0 |
| A5 | Sınır: ADR-064 sayım çelişkisi (13/11/9/7) ve ADR-039 servis listesi **bu ADR ile değişmez** — dev zaten 11'de | ADR-064 §2(b) · ADR-039:127 |

**(b) Kod/DB izolasyonu:**

| # | Karar | Kanıt / bağ |
|---|-------|-------------|
| B1 | **Kod:** tek repo; ortam ayrımı **branch/deploy hedefi** ile — `development` lokal çalışır, `staging` dala deploy edilir, `production` yalnız staging kapısından (gate) geçer | `.github/workflows/ci.yml` (deploy 0 → PLANNED) · web 2 (kademeli promote) |
| B2 | **DB:** her ortam **kendi 18 DB kümesini** taşır (ADR-040 şeması kopyalanır) — **ortak/staging-prod paylaşımlı DB yasak**; adlandırma deseni (sonek/onek) debate §5.1/7 | ADR-040 · `.env.example:22-29` (DB_NAME per-subdomain) · web 1 (\"sharing one database\" anti-pattern) + web 2 (ayrı schema) |
| B3 | **Migration:** DDL yalnız ADR-014 tek kapısından; prod'ika migration **CI gate** (staging'de başarılı olmadan prod yok) | ADR-014 · web 2 (Supabase: dev elle, staging/prod CI) |
| B4 | **Veri:** staging'e gerçek prod verisi **kopyalanmaz** (maskelenmiş/gerçekçi veri); seed ile kurulum debate dışı — ADR-040 seed'leri (ör. i18n 12 dil) her ortamda aynı | web 1 (\"realistic, not real\") · ADR-040 |
| B5 | `.env` **her ortam için ayrı dosya seti** (mevcut 3 `.env.example` şablonunun kopyaları) — gerçek `.env` gitignore'da kalır, vault'a **hiç girmez** (REDACTED) | `.gitignore:6,:9,:70` · `.env.example` ×3 · ADR-034 |

**(c) Domain scope / cookie / URL stratejisi:**

| # | Karar | Kanıt / bağ |
|---|-------|-------------|
| C1 | **Prod cookie:** `SESSION_COOKIE_DOMAIN=.coremusic.net` **korunur** (SSO ön koşulu — ADR-058/043/011) | `.env.example:35` · ADR-058 (\"domain=.coremusic.net korunur\") · web 3 istisnası bilinçli |
| C2 | **Dev/Staging cookie scope DARALTILIR:** dev/staging host'ları için cookie **host'a özel** (parent-domain yazılmaz) → prod oturumu dev host'una sızmaz, dev oturumu prod'ya sızmaz. Uygulama `APP_ENV_MODE`/host'a göre seçilir; `SameSite` + `Secure` **ayrı katman** (scope'un yerine geçmez — web 3) | web 3 (Acunetix/Xebia/SO 59785832) · ADR-011 |
| C3 | **URL/domain varsayılanları ortamlar arası AYNI:** ADR-045 domain bazlı varsayılanlar (müzik=liste, galeri=kart, admin=tablo + override) ve ADR-046 3 katman (URL birincil → sessionStorage → servis tercihi; durum yoksa domain varsayılanı) **host dışında değişmez** → URL kırılmaz, paylaşım linkleri ortam değiştirince çalışmaya devam eder | ADR-045 · ADR-046 · ADR-004/021 router sözleşmesi |
| C4 | **Origin/CORS:** ortam başına origin allowlist'i (bugün boş allowlist + `/validate-key` muafiyeti ADR-058 PLANNED kapılarıdır — bu ADR onları **tekrar etmez**, yalnız ortam boyutunu ekler: her ortamın origin'i ayrı kayıt) | ADR-058 (E2/E4) · `.env.example:42` `TRUSTED_PROXIES` |
| C5 | `FORCE_AUTH_BYPASS` yalnız `development`/`test` — staging'de **kapalı** mı debate (ADR-008 kill yalnız production'a bağlı) | `.env.example:17` · ADR-008 |

**(d) Deploy akışı + gizli veri:**

| # | Karar | Kanıt / bağ |
|---|-------|-------------|
| D1 | **Akış:** `dev (lokal) → staging → production`; **prod'ka geçiş = staging'de doğrulanmış artifact/deploy adımı**; big-bang yok, fazlı (ADR-043 yöntemi) | `ci.yml:3` (k13-cicd planı) · web 2 (kademeli promote) |
| D2 | **Bugünkü dürüst etiket:** `ci.yml` = lint/test/PHPStan (**IMPLEMENTED**), `secret-scan.yml` = gitleaks (**IMPLEMENTED**), **deploy job = 0 (PLANNED)**, rsync/deploy script = 0, `.gitleaks.toml` = VAR (allowlist yalnız test dosyası; workflow yorumu eski) → §5.1/4 kapsam denetimiyle kapatılır | `.github/workflows/` (2 dosya) · `bin/` (deploy 0) |
| D3 | **Gizli veri:** secret yalnız ortam `.env`'inden + CI secret store'undan okunur; `.env.example` = şablon (değer yok), `.gitignore:6,:9,:70` koruması **değiştirilmez**; credential tek SSOT = ADR-034; **vault'a/ADR'ye secret yazılmaz** (REDACTED) | `.gitignore` · ADR-034 · `.env.example` (APP_PEPPER boş, OAuth boş) |
| D4 | **Kapılar (gate):** (i) CI lint/test geçmezse deploy yok · (ii) gitleaks temiz değilse deploy yok · (iii) staging'de smoke doğrulanmadan prod yok · (iv) migration ADR-014'ten · (v) k13-cicd 14 dokümanı bu akışın PLANNED spesifikasyonudur — bu ADR ile **amaç** bağlanır, dokümanlar tekrar yazılmaz | web 2 (approval gate) · k13-cicd/ (14 dosya) |
| D5 | **Rollback:** prod arızasında staging'e geri dönüş + k13-cicd `rollback-strategy.md` bu ADR'nin geri dönüş kanalıdır (detay orada — tekrar yok) | `k13-cicd/rollback-strategy.md` |

**(e) Sınır (tekrar yok):** servis envanteri + dev satırı → ADR-039 · platform/sayım → ADR-064 · auth/cookie SSO + validate-key → ADR-058 · domain varsayılanları → ADR-045 · URL katmanları → ADR-046 · 18 DB sahipliği → ADR-040 · env parser/enum → ADR-015 · credential SSOT → ADR-034 · prompt gizliliği → ADR-035 · cookie konsolidasyonu → ADR-043/011 · bypass kill → ADR-008 · subdomain SPA → ADR-004 · migration → ADR-014 · test personası → ADR-023 · sync → ADR-081 — **hiçbiri bu ADR'de yeniden yazılmaz.**

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Tek ortam (her şey prod'da, branch ile ayrım)** | Deploy altyapısı kurulmaz, tek DB | Web 1: staging/prod izolasyonu yok → test verisi canlıya dokunur; web 2 secret kopyalanmaz/tek kapsam olur; rollback imkânsız; `dev.coremusic.net` hiç kurulmaz (ADR-039 satırı hayalet kalır) | Veri sızıntısı + rollback yokluğu → **ret** |
| 2 | **Ortam ayrımı yalnız `APP_ENV_MODE` (aynı host, subdomain yok)** | Cookie scope sorunu hiç doğmaz (tek host) | `dev.coremusic.net` kararı (ADR-039/023) anlamsızlaşır; dev/staging/prod **aynı** `domain.php` 7 subdomain'ine sığmaz (ayrı surface ister); staging deploy'u prod dosyalarına birebir dokunur (staging=prod kopyası imkânsız) | İki onaylı kararı (039/023) çürütür + izolasyon yok → **ret** |
| 3 | **Ayrı üst alan adı / ayrı domain (dev.example.com tarzı)** | Cookie sorunu tamamen kopsun | Yeni alan adı kaydı + yeni TLS + yeni allowlist (C4) + ADR-004 domain listesi revizyonu; SSO köprüsü (`domain=.coremusic.net`) dev için yeniden kurulur | Maliyet + ADR-058/043'ü zorlar; üst alan zaten aynı (`.coremusic.net`) → **ret** (üst alan içinde host-yeterli ayrım C2 ile sağlanır) |
| 4 | **Tüm ortamlarda ortak tek DB (şema `staging`/`production` ayrımı ile)** | Migration tek sefer çalışır, kurulum ucuz | Web 1 anti-pattern (\"sharing one database\"); staging doğrulaması prod verisine yazar/okur → **staging-prod veri sızıntısı** (R1); 18-DB sahiplik matrisi (ADR-040) iki ortam davranışı taşıyamaz; geri dönüşü olmayan DDL riski | R1'in doğrudan kaynağı → **ret** (B2 bunun tersidir) |
| 5 | **Deploy'u k13-cicd dokümanlarına bırak (pipeline kodlanmaz)** | Dokümanlar zaten 14 dosya hazır | Doküman PLANNED kalır, kapılar denetimsiz; \"prod'ga elle geçilir\" drift'i büyütür (web 2); gitleaks/ci kapısız | Denetimsiz geçiş + env drift → **ret**; dokümanlar **spesifikasyon** olarak kalır, kapılar §5.1/3'te kodlanır |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **`dev.coremusic.net` ilk kez karar olarak yazılıyor:** amaç (geliştirici/sandbox yüzeyi), tablo ekleme yolu (Config `:45-55` + `domain.php` — ekleme, değiştirme) ve stack'in tartışmalı olduğu gerçeği (`⚠️`) tek dosyada; ADR-039:127 ve ADR-023'in \"ADR-082'ye devrettiği\" boşluk kapanıyor.
- **Ortam üçlemesi + izolasyon ilk kez yazılı:** development/staging/production ayrım ekseni (`APP_ENV_MODE`), her ortam ayrı `.env` + ayrı 18 DB kümesi + ayrı secret kapsamı → staging-prod veri sızıntısı (R1) **kural olarak** kapanıyor; migration tek kapı + CI gate ile destekleniyor.
- **Cookie riski iki katmanla yazılı:** prod `domain=.coremusic.net` SSO korunur (ADR-058 bozulmaz), dev/staging host scope'u daraltılır → prod cookie'si dev'e sızmaz; `SameSite`'in scope'un yerine geçmediği (web 3) ayrıca yazıldı.
- **Deploy dürüst etiketi:** lint/test + gitleaks **IMPLEMENTED**, deploy job **0** ve rsync script **0** açıkça yazıldı → \"pipeline var\" yanılgısı üretilmedi; 5 kapılı gate (D4) yol haritası oldu.
- **Sınır temiz:** 15 ilgili ADR (039/064/058/045/046/040/015/034/035/043/011/008/004/014/081) + ADR-023 diskte doğrulandı; tekrar yok.

### 4.2 Olumsuz Sonuçlar

- **Hiçbiri bugün çalışmıyor:** dev dizini 0 · staging enum 0 · deploy job 0 · ortam .env seti 0 · staging adresi yazılmamış → ADR kabul edildi ama **kod PLANNED**; \"staging kuruldu\" yanılgısı yasak (§1.1 dürüst etiket).
- **Dört açık debate'e taşındı (sayılmadı):** `staging` enum'u · staging'in adresi (A4) · dev stack'i (ADR-039:127 `⚠️`) · cookie scope daraltmasının SSO ile birleşmesi (C2↔C1) → debate öncesi bu dört soru cevapsız.
- **Tablolara ekleme yapılmadı (bu işlem = vault-only):** Config 9 satır + `domain.php` 7 satır hâlâ dev'siz; kod fazında onaylı ekleme bekliyor (§5.1/2) — bu süre zarfında ADR-064/039 sayım tabloları ile kod tabloları arasında bilinçli geçici fark var.
- **Index satırı eklenmedi:** `.decisions/index.md` `:104/105` (081/083) arasına ADR-082 satırı **bilinçli olarak yazılmadı** → sıfırlamaya ertelendi (§5.1/9 + §7.1); bu süre boyunca dizin bu ADR'yi **görmez**.
- **`secret-scan.yml` yorumu eski + CI geçmedi doğrulanmadı:** `.gitleaks.toml` diskte VAR ama allowlist yalnız test dosyasını kapsar; gate'ler (D4) henüz denetlenmemiş kapılar.

### 4.3 Riskler

| # | Risk | Olasılık | Etki | Mitigasyon |
|---|------|---------|------|-----------|
| R1 | **Staging→prod veri sızıntısı** — tek DB/paylaşımlı `.env` ya da staging'e kopyalanmış gerçek veri ile test canlıya yazar/okur | 3 (olası) | 4 (yüksek) | B2 ayrı 18-DB kümeleri + B4 gerçek veri kopyası yasağı + B3 migration tek kapı/CI gate; `.env` ortam başına ayrı (B5) |
| R2 | **Domain scope ihlali** — `.coremusic.net` cookie'si dev host'una sızmaya devam ederse dev'de prod oturumu (ya da tersi) çalışır; ADR-058 `/validate-key` Origin/CORS boşlukları dev origin'iyle genişler | 3 (olası) | 4 (yüksek) | C2 host-scope daraltması + C4 ortam başına origin allowlist'i (ADR-058 E2/E4 kapıları — tekrar yok); SameSite ikinci katman (web 3) |
| R3 | **Gizli veri sızıntısı** — `.env`/CI secret'ının yanlışlıkla commit'i ya da vault'a yazımı; `.gitleaks.toml` allowlist'inin yalnız test dosyasını kapsaması kapsamı daraltır | 2 (mümkün) | 4 (yüksek) | D3 gitignore koruması (`:6,:9,:70`) + example şablon + gitleaks + `.gitleaks.toml` allowlist kapsam denetimi §5.1/4 + ADR-034 SSOT + REDACTED |
| R4 | **Env drift** — doküman `staging`/`local`, enum `development\|production\|test` üçlüsü büyür; `constants.php:25` fail-fast staging deploy'u reddeder | 4 (çok olası) | 3 (orta) | A1 enum genişlemesi debate (§5.1/6) + tek sözlük kuralı: k13-cicd dokümanları kod enum'una hizalanır (rapor §5.1/5) |
| R5 | **Deploy yokluğu/elle geçiş** — deploy job 0; kapılar (D4) kodlanmadan prod'ga elle publish → geri dönüşü olmayan hata | 4 (çok olası) | 3 (orta) | D1/D4 kademeli promote + §5.1/3 gate adımı; rollback-strategy.md kanalı (D5) |

### 4.4 Fallback (geri birleşim / geri dönüş)

Debate **RED** çıkarsa: dosya `.ai/.decisions/rejected/` taşınır (dosya adı **değiştirilmez** — In-Place Refactoring), `log.md` satırı **silinmez** (append-only), index satırı zaten yazılmadı → geri alınacak satır **yok**. Bu durumda: (i) `dev.coremusic.net` yeniden **düz metin** haline döner (ADR-039:127 + ADR-023'teki mevcut düz metin hali zaten diskte duruyor — wiki-link'ler bu ADR'ye bağlı kalır, o ADR'ler değiştirilmez), (ii) B2/C2/D4 kapıları **kural olmaktan çıkar** (öneriye döner), (iii) §1.1 ölçümleri (deploy 0 · enum 3 değer · 7 subdomain · 9 satır · 2 gerçek .env · 14 k13-cicd dosyası) **bağımsız bulgu olarak geçerlidir**. Karar **kabul edilip değiştirilirse** yeni ADR açılır (≥088 serisi; numara çakışması notuyla) ve bu metin `superseded by` ile bağlanır. Geri alınabilecek yüzeyler yalnız vault dosyalarıdır: bu ADR'nin eklediği tek dosya (kendisi) kaldırılır; kod/dizin değişikliği yapılmadığı için revert gerekmez. Frozen olduktan sonra hiçbir düzenleme yapılmaz (şablon §4 kural 10).

### 4.5 Cross-Reference

| İlişki | Hedef | Durum |
|--------|-------|-------|
| 11 servis + dev satırı (PLANNED) | [[ADR-039-7-service-platform-architecture]] (`:127`) | ✅ dosya var — bu ADR o satırın mimarisini yazar |
| Platform/sayım çelişkisi + subdomain tablo sayıları (7/9/11) | [[ADR-064-electronics-platform-architecture]] | ✅ dosya var — sayım değişmez |
| Cookie domain SSO + `/validate-key` Origin kapıları | [[ADR-058-centralized-auth-service]] | ✅ dosya var — C1 onun korumasıdır, C2/C4 oraya bağlı |
| Domain varsayılanları (URL birincil + override) | [[ADR-045-multi-domain-view-mode-architecture]] | ✅ dosya var — C3 hizası |
| 3 katmanlı URL/sessionStorage/servis durumu | [[ADR-046-cross-view-state-preservation]] | ✅ dosya var — C3 hizası |
| 18 DB sahiplik matrisi | [[ADR-040-database-authority]] | ✅ dosya var — B2/B4 |
| env parser + `APP_ENV_MODE` enum + fail-fast | [[ADR-015-env-parser-strategy]] | ✅ dosya var — A1/R4 |
| Credential tek SSOT + CI secret scan kapısı | [[ADR-034-credential-vault-normalization]] | ✅ dosya var — D3 (görev etiketi disk başlığından farklı → §1.1/10 + ⚠️) |
| Prompt gizliliği sınırı | [[ADR-035-system-prompt-engineering]] | ✅ dosya var — D3 sınırı (etiket farkı ⚠️) |
| Cookie konsolidasyonu + session yaşam döngüsü | [[ADR-043-auth-subdomain-consolidation]] · [[ADR-011-session-management]] | ✅ dosya var — C1/C2 |
| Bypass kill (env=production) | [[ADR-008-bypass-auth-middleware]] | ✅ dosya var — C5 |
| Subdomain SPA iskeleti | [[ADR-004-multi-domain-spa]] | ✅ dosya var — A2/A3 |
| Migration tek kapısı | [[ADR-014-multi-db-migration-strategy]] | ✅ dosya var — B3/D4 |
| Test kişisi #8 dev devri | [[ADR-023-persona-driven-testing]] | ✅ dosya var — A2 |
| Sync sınırı | [[ADR-081-multi-provider-data-sync]] | ✅ dosya var — kapsam dışı |
| Format referansı | [[ADR-079-i18n-database-schema]] | ✅ dosya var — iskelet + dürüst etiket yöntemi |
| Karar dizini | `.ai/.decisions/index.md` (`:104`=081 · `:105`=083) | ❌ **ADR-082 satırı YOK** → §5.1/9 + §7.1 |
| Düz metin + ⚠️ (linklenmeyen) | `ADR-080` · `ADR-083`–`ADR-088` · `ADR-051` · `ADR-053` · `ADR-054` · `ADR-055` · `ADR-057` · `ADR-060` · `ADR-065`–`ADR-071` | ⚠️ 14 boşluk (dosya+satır YOK) + 083-088 (satır var/dosya YOK; 080 wiki-link **0** · dosya **0** · index satırı **0** (düz metin boşluk notu 49 isabet)) — **wiki-link kurulmadı** |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre | Durum |
|---|------|---------|------|-------|
| 1 | **Debate (3 tur / 20 persona) + Tech Lead onayı** — dört açık: `staging` enum'u (A1/R4) · staging adresi (A4) · dev stack'i (A2, ADR-039:127 `⚠️`) · cookie scope ↔ SSO birleşimi (C2↔C1) + `FORCE_AUTH_BYPASS` staging davranışı (C5) + DB adlandırma deseni (B2) | MO + persona | 1 gün | ✅ **TAMAMLANDI** (3 tur / 20 persona — 18/2/0 KABUL · sonuç §7.2 · şartlar §5.1/11-13) |
| 2 | **Subdomain tablolarına `dev` satırı (onaylı kod ekleme):** `shared/src/Config/CLAUDE.md:45-55` (9→10 satır) + `shared/config/domain.php:7-15` (7→8) + `shared/AGENTS.md` envanter satırı — **ekleme, mevcut satır değişmez**; ardından ADR-064/039 sayım tablolarıyla çapraz doğrulama | Backend + MO | 1 oturum | ⏳ PLANNED (bu işlem vault-only — tablolara dokunulmadı) |
| 3 | **Deploy gate'leri:** `ci.yml`'e deploy job eklenir (staging hedefi) + D4 beş kapı (lint/test · gitleaks · staging smoke · ADR-014 migration · prod approval) + rollback-strategy.md kanalının bağlanması | DevOps | 2-3 gün | ⏳ PLANNED (bugün deploy job **0**) |
| 4 | **Secret taraması sertleştirme:** `.gitleaks.toml` kapsamı denetlenir (dosya VAR, yalnız test dosyası; `secret-scan.yml` eski yorumu düzeltilir) + `.env.example` ortam seti (dev/staging/prod türevleri, **değer yok**) + gitignore `:6,:9,:70` değişmezliği denetimi | Security + DevOps | 1 gün | ⏳ PLANNED |
| 5 | **Rapor (düzeltilmedi):** k13-cicd dokümanlarındaki `staging`/`local` ↔ enum `development\|production\|test` sözlük farkı (14 dosya) — kod enum'una hizalanması ayrı işlem; bu ADR yalnız işaretler | MO (vault-updater) | sonraki vault reset | ⏳ **rapor-only** |
| 6 | **Şart adayı 1 (debate — bağlayıcı):** `APP_ENV_MODE` enum'a `staging` eklenip eklenmeyeceği (`constants.php:22-25` fail-fast — ADR-015 kapısı) + staging'in **adresi** (subdomain vs host+path) + `local`/`development` sözlük tekliği | Backend + MO | debate | ⏳ debate PENDING → şart bu ADR'de yazılmaz |
| 7 | **Şart adayı 2 (debate):** DB izolasyonu — ortam başına 18 DB kümelerinin **adlandırma deseni** + gerçek veri kopyasının yasağı + seed stratejisi (ADR-040/014 kapısı) | Data + MO | debate | ⏳ debate PENDING |
| 8 | **Şart adayı 3 (debate):** cookie scope daraltmasının uygulanışı — `SESSION_COOKIE_DOMAIN`'in host/`APP_ENV_MODE`'a göre davranışı, ADR-058 SSO ile aynı üst alan içinde birlikte yaşama, `SameSite`/`Secure` katmanı; dev stack'i (ADR-039:127 `⚠️`) | Security + Backend | debate | ⏳ debate PENDING |
| 9 | **`.ai/.decisions/index.md` kayıt satırı EKLENMEDİ** — `:104`=081 / `:105`=083 arasına `ADR-082-dev-environment` satırı **son sıfırlamaya ertelendi** (görev talimatı: index satırını ekleme) → bu ADR dizinde **görünmez** | MO (vault-updater) | sonraki vault reset | ⏳ **ertelendi (rapor-only)** |
| 10 | **Numara boşlukları raporu (düzeltilmedi):** **(10a)** 14 atlanan boşluk `ADR-051` · `053` · `054` · `055` · `057` · `060` · `065`–`071` · `080` (dosya **+** `.decisions/index.md` satırı YOK; **ADR-080 wiki-link 0 · dosya 0 · index satırı 0 (düz metin boşluk notu 49 isabet)**) · **(10b)** `ADR-083`–`ADR-088` dizin satırı var / dosya YOK · **(10c)** `ADR-080` dosya+satır YOK · **(10d)** numara çakışması (kural 7 ≥ 088 ↔ 082 slotu) · **(10e)** görev etiketi↔disk başlığı: ADR-034 \"kademeli config\" ↔ disk \"Credential Vault Normalization\"; ADR-035 \"Vault SSOT\" ↔ disk \"System Prompt Engineering\" | MO (vault-updater) | sonraki vault reset | ⏳ **bu işlemde düzeltilmedi** (rapor-only) |
| 11 | **Şart 1 (debate — bağlayıcı): staging kararı + deploy pipeline** — **1a)** `APP_ENV_MODE` enum'a `staging` eklenmesi kararı ya da 2'li karara düşme (ADR-015 kapısı · `constants.php:22-25` fail-fast — düzeltme fazı) · **1b)** deploy pipeline fazı (PLANNED + gate'li promote — D4 kapıları §5.1/3 ile bağ) | Backend + DevOps + MO | düzeltme fazı | ✅ debate şartı (§7.2) |
| 12 | **Şart 2 (debate — bağlayıcı): DB izolasyon/backup politikası** — ortam başına ayrı 18-DB kümeleri için backup + şema migration politikası; **prod'a promote'te şema testi** (ADR-014 tek kapısı · ADR-040) | Data + MO | düzeltme fazı | ✅ debate şartı (§7.2) |
| 13 | **Şart 3 (debate — bağlayıcı): dev alt alan doğrulaması** — `dev.coremusic.net` gerçekliği `⚠️ VERIFICATION REQUIRED` + iskelet doğrulama (**dizin + DNS kaydı**) | DevOps + MO | düzeltme fazı | ✅ debate şartı (§7.2) |

### 5.2 Geri Dönüş Planı

Debate **RED** çıkarsa: dosya `.ai/.decisions/rejected/` taşınır (dosya adı **değiştirilmez**), `log.md`'ye `ADR-082 RED (debate …)` append edilir; **index satırı hiç yazılmadığı için silinecek kayıt yoktur**. Bu durumda (i) `dev.coremusic.net` düz metin olarak ADR-039:127 ve ADR-023'te **zaten duruyor** — vault kaybı yok, (ii) B2/C2/D4 kapıları kural olmaktan çıkar (öneriye döner), (iii) §1.1 ölçümleri (deploy 0 · enum 3 değer · 7 subdomain · 9 satır · 2 gerçek `.env` · 14 k13-cicd dosyası · `staging` kodda 0) **bağımsız bulgu olarak geçerlidir**, (iv) kod tarafında hiçbir değişiklik yapılmadığı için revert gerekmez. Karar değiştirilirse yeni ADR (≥088 serisi, numara çakışması notuyla) açılır, bu metin `superseded by` ile bağlanır. Geri alınabilir yüzey = bu ADR'nin eklediği tek dosya (kendisi) + `log.md`'e eklenen append satırı (o **silinmez**). Frozen olduktan sonra hiçbir düzenleme yapılmaz (şablon §4 kural 10).

---

## 6. İlgili Dokümanlar

### 6.1 Kaynak Kanıtlar

| Dosya | Satır/Kapsam | Ne | Etiket |
|-------|-------------|----|--------|
| `.ai/.decisions/index.md` | `:104` · `:105` | ADR-081 · ADR-083 satırları → **ADR-082 satırı YOK** | ❌ ekleme ertelendi (§5.1/9) |
| `shared/src/Config/CLAUDE.md` | `:45-55` | 9 satır domain tablosu — **dev satırı YOK** | ✅ birincil kanıt (ADR-058/064 kullandı) |
| `shared/config/domain.php` | `:4-17` | `primary=coremusic.net` + 7 subdomain + portlar — **dev YOK** | ✅ birincil kanıt |
| `shared/config/.env.example` | `:1-96` | `APP_ENV_MODE` (`:9`) · `APP_DEBUG` (`:13`) · `TEST_MODE` (`:16`) · `FORCE_AUTH_BYPASS` (`:17`) · `DB_NAME` (`:29`) · `SESSION_COOKIE_DOMAIN` (`:35`) · `APP_PEPPER` (`:41`) · URL'ler (`:51-55`) · OAuth boş (`:57-96`) | ✅ şablon (değer yok — secret okunmadı) |
| `home.coremusic.net/config/constants.php` | `:10,:21-28,:39-40` | enum `development\|production\|test` + fail-fast + `DEBUG_MODE` + prod'da bayrak okunmaz | ✅ env bayrağı IMPLEMENTED |
| `auth.coremusic.net/index.php` | `:59` | `$isProductionEnv` | ✅ ikinci kullanım |
| `.github/workflows/ci.yml` · `secret-scan.yml` | tamam · `:1-29` | lint/test pipeline (deploy **0**) · gitleaks (`fetch-depth:0`, `.gitleaks.toml` **VAR** — `secret-scan.yml` yorumu eski) | ⏳ **deploy PLANNED** |
| `.gitignore` | `:6,:9,:68-70` | `.env` · `config/.env` · `.ai/.env.*` + `!.env.example` | ✅ gizli veri koruması IMPLEMENTED |
| `.env` (2 gerçek) | `api.coremusic.net/config/.env` · `auth.coremusic.net/config/.env` | varlık **kanıtı** — içerik **okunmadı** (REDACTED) | ⚠️ içerik ⚠️ VERIFICATION REQUIRED |
| `.ai/architecture/k13-cicd/` (14 dosya) | `staging-environment.md:76-78,104-105` · `kubernetes-deploy.md:215` · `docker-build.md:189-190` | `APP_ENV: staging/production/local` — **kod enum'unda yok** | ⏳ PLANNED doküman + **env drift** (R4) |
| `.workflows/deployment.md` | `:55,:64,:275` | dağıtım onay akışı dokümanı | ⏳ süreç dokümanı |
| `.ai/.decisions/accepted/ADR-039-…` | `:127` · `:129` | dev satırı (PLANNED, dizin YOK) · `api` 11'e girmez (C2) | ✅ dosya var — **bağlayıcı envanter** |
| `.ai/.decisions/accepted/ADR-064-…` | künye · §2 | 11 alan servisi · 7 subdomain/9 satır sayımı · 13 servis hayaleti | ✅ dosya var — sayım sınırı |
| `.ai/.decisions/accepted/ADR-058-…` | künye · Tablo B · web sonucu | `.coremusic.net` SSO korunur · `/validate-key` Origin muafiyeti · boş allowlist | ✅ dosya var — **cookie/origin dayanağı** |
| `.ai/.decisions/accepted/ADR-045-…` · `ADR-046-…` | künye | domain varsayılanları · URL 3 katman | ✅ dosya var — C3 |
| `.ai/.decisions/accepted/ADR-040-database-authority.md` · `ADR-015-…` · `ADR-034-…` · `ADR-035-…` · `ADR-043-…` · `ADR-011-…` · `ADR-008-…` · `ADR-004-…` · `ADR-014-…` · `ADR-023-…` · `ADR-081-…` · `ADR-079-…` | künye | 18 DB · env parser · credential SSOT · prompt · cookie konsolidasyonu · session · bypass kill · SPA · migration · test personası · sync · format referansı | ✅ hepsi diskte — sınır/bağ |
| `.ai/.templates/adr/adr-template.md` | §3 · §6-§7 | 7 bölüm + §1.3 9 alan + §7 onay (Guardrail #16) | ✅ gerçek yol `.ai/.templates/…` (görevdeki `.ai/templates/…` **YOK** → §1.1/10) |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | — | web araştırma protokolü | ✅ Test-Path True |
| Web | 3 sorgu / ~30 isabet | thinkby.ai · back4app · Kodekx · mergify · r/devops ×2 · Bunnyshell · DCHost · GoReplay · exact.gg · Signadot · DeployHQ · massivegrid · Supabase Docs + #542 · Replit · Doppler · NareshIT · HashiCorp Discuss · alamrafiul · Medium · Acunetix · nhimg · web.dev · security.SE · PortSwigger · Xebia · SO · andrewlock | ✅ 3/3 karar destekli (§1.3) |

### 6.2 Bağlantılar

- Şablon: [[../../.templates/adr/adr-template.md]] (Guardrail #16) · format referansı: [[ADR-079-i18n-database-schema]]
- İlgili ADR'ler: [[ADR-039-7-service-platform-architecture]] · [[ADR-064-electronics-platform-architecture]] · [[ADR-058-centralized-auth-service]] · [[ADR-045-multi-domain-view-mode-architecture]] · [[ADR-046-cross-view-state-preservation]] · [[ADR-040-database-authority]] · [[ADR-015-env-parser-strategy]] · [[ADR-034-credential-vault-normalization]] · [[ADR-035-system-prompt-engineering]] · [[ADR-043-auth-subdomain-consolidation]] · [[ADR-011-session-management]] · [[ADR-008-bypass-auth-middleware]] · [[ADR-004-multi-domain-spa]] · [[ADR-014-multi-db-migration-strategy]] · [[ADR-023-persona-driven-testing]] · [[ADR-081-multi-provider-data-sync]]
- Vault kökü: [[../index.md]] · [[../../index.md]] · [[../../raw/brain.md]] · [[../../raw/keys.md]] · [[../../log.md]] · [[../../CLAUDE.md]]
- Düz metin (dizin/klasör — wiki-link değil): `.ai/architecture/k13-cicd/` (14 dosya) · `.github/workflows/` (2 dosya) · `.workflows/deployment.md` · `shared/config/domain.php` · `shared/config/.env.example` · `home.coremusic.net/config/constants.php` · `.claude/skills/prompt-maker/references/10-web-research-protocol.md`
- Diskte **olmayan** (düz metin + ⚠️, linklenmez): **`ADR-080`** · `ADR-083`–`ADR-088` · `ADR-051` · `ADR-053` · `ADR-054` · `ADR-055` · `ADR-057` · `ADR-060` · `ADR-065`–`ADR-071` · `.ai/.templates/adr/adr-template.md` (görev yolu — gerçek: `.ai/.templates/…`) · staging host adı (yazılmadı)

### 6.3 Wiki-Link Sayımı

| Öğe | Değer |
|------|-------|
| Wiki-link toplamı (bu dosya) | **25 benzersiz link** (sayım betiğiyle doğrulandı): **17 ADR slug-linki** (`ADR-004/008/011/014/015/023/034/035/039/040/043/045/046/058/064/079/081`) + **8 path-linki** (`../index.md`, `../brain.md`, `../../index.md`, `../../brain.md`, `../../keys.md`, `../../log.md`, `../../CLAUDE.md`, `../../.templates/adr/adr-template.md`) |
| Diskte olan hedef | **24/25 çözüldü** — 17 ADR dosyası + 6 kök dosya + `.decisions/index.md` + şablon 2026-10-01 Test-Path ile doğrulandı; **`../brain.md` = 1 çözülmeyen** — o, §1.1/7'de `index.md:105`'in **kusurlu satırının alıntısıdır** (backtick içinde, ADR-079 §1.1 ile aynı yöntem) — aktif link değil, alıntı; gerçek hedef `.ai/raw/brain.md` YOK → kusur zaten §5.1/9'da ertelenen düzeltmenin konusu |
| Düz metin + ⚠️ (linklenmeyen) | `ADR-080` · `ADR-083`–`088` · `ADR-051` · `053` · `054` · `055` · `057` · `060` · `065`–`071` (14 boşluk + 080 + 083-088) + görev şablon yolu + staging host adı |

### 6.4 Debate Notu (⏳ PENDING)

| Alan | Değer |
|------|-------|
| Debate | ✅ **TAMAMLANDI** — 3 tur / 20 persona · **18 kabul / 2 çekimser / 0 red = KABUL** · sonuç §7.2 · Tech Lead ✅ |
| Uygulanan format | 3 tur / 20 persona (ADR-072–079 ile aynı) — sonuç §7.2'ye işlendi, Tech Lead onayı verildi |
| Debate öncesi açık kapılar → aday şartlar | `staging` enum'u + staging adresi + sözlük tekliği → **şart adayı 1** (§5.1/6) · DB adlandırma/seed/veri kopyası → **şart adayı 2** (§5.1/7) · cookie scope ↔ SSO + dev stack'i → **şart adayı 3** (§5.1/8) · `FORCE_AUTH_BYPASS` staging davranışı (C5) → şart 1 girdisi |
| Debate şartları (bağlayıcı) | **(1)** staging kararı + deploy pipeline 1a-1b (§5.1/11) · **(2)** DB izolasyon/backup politikası (§5.1/12) · **(3)** dev alt alan doğrulaması (§5.1/13) → sonuç §7.2 |
| Debate RED ise | §5.2 geri dönüş + §4.4 fallback birlikte uygulanır |

---

## 7. Onay

### 7.1 Onay Akışı

| Rol | Kişi | Tarih | Durum |
|-----|------|-------|-------|
| Vault Steward | CoreMusic Vault Steward | 2026-10-01 | ✅ |
| Tech Lead | — | 2026-10-01 | ✅ (debate 3/20 KABUL — §7.2) |
| Arch Lead | — | — | ⏳ |

**Index durumu raporu:** `.ai/.decisions/index.md` **ADR-082 satırı YOK** (`:104` = ADR-081 · `:105` = ADR-083) — talimat gereği satır **bu işlemde EKLENMEDİ, son sıfırlamaya ertelendi** (§5.1/9); slug `ADR-082-dev-environment` dosya adıyla birebir, dizin hizası sıfırlamada sağlanacaktır.

**Numara boşlukları raporu (§5.1/10 ile aynı — rapor-only, düzeltilmedi):** **14 atlanan boşluk** `ADR-051` · `ADR-053` · `ADR-054` · `ADR-055` · `ADR-057` · `ADR-060` · `ADR-065` · `ADR-066` · `ADR-067` · `ADR-068` · `ADR-069` · `ADR-070` · `ADR-071` · `ADR-080` = dosya **ve** `.decisions/index.md` satırı YOK (ikisi birden yok — ADR-079 §5.1/10a ile aynı kümeler; **ADR-080 wiki-link 0 · dosya 0 · index satırı 0 (düz metin boşluk notu 49 isabet)**). Ayrıca `ADR-083`–`ADR-088` = dizin satırı var / dosya YOK. Bu ADR **numara serisine dokunmaz**, yalnızca raporlar; `ADR-082` bu işlemde dolduruldu (dosya var, **dizin satırı yok** → §5.1/9).

### 7.2 Debate

| Alan | Değer |
|------|-------|
| Debate | ✅ **TAMAMLANDI** — 3 tur / 20 persona · **18 kabul / 2 çekimser / 0 red → KABUL** (ADR-072–079 formatı) |
| Tur 1 — bulgu (20 persona) | `dev.coremusic.net` gerçek kullanım **yalnız vault** (ADR-039:127 11. servis satırı PLANNED, dizin YOK + ADR-023 6 satır; kod/domain.php/config = 0) · subdomain tablolarında **dev satırı YOK** (`shared/src/Config/CLAUDE.md:45-55` = 9 satır · `shared/config/domain.php:7-15` = 7 subdomain) · `APP_ENV_MODE` enum `development\|production\|test` (`home.coremusic.net/config/constants.php:22-25`, geçersizse fail-fast `:25`) + `DEBUG_MODE` `:28` + `auth/index.php:59` `$isProductionEnv` + `.env.example` · `staging` grep `*.php` = **0** · deploy yüzeyi `.github/workflows/` = 2 dosya (ci.yml, secret-scan.yml) — **deploy/rsync adımı 0** · `k13-cicd/` = 14 PLANNED doküman · **halüsinasyon düzeltmesi:** `.gitleaks.toml` **VAR** (579 bayt, tracked `a5b680c`) — 11 "YOK" iddiası 14 replacement ile düzeltildi · ADR-080 netleşmesi: wiki-link 0 · 49 düz metin isabet · 3 sorgu / ~30 isabet · karar: **ortam üçlemesi + ayrı 18-DB + host-scope cookie + gate'li promote** · 14 atlanan boşluk §5.1/10 + §7.1'de · `.decisions/index.md:105` ADR-082 satırı YOK **eklenmedi** (son sıfırlamaya erte) · wiki-link 24/25 diskte (brain.md backtick alıntısı) |
| Tur 1 — uyarılar | 16 kabul/neutral · 4 uyarı (DevOps: staging boşluğu şart · Critic: deploy şart · Cloud: DB maliyeti · Security: gitleaks) |
| Tur 2 — itiraz→çözüm | **1)** staging enum'da yok ↔ 3'lü ortam kararı → enum'a staging ekle veya 2'li karara düş (düzeltme fazı) → **şart 1a** · **2)** deploy adımı 0 (workflow'da rsync/promote yok) → deploy pipeline fazı (PLANNED + gate'li promote) → **şart 1b** · **3)** ayrı 18-DB izolasyonu → backup/şema migration politikası (prod'a promote'te şema testi) → **şart 2** · **4)** `dev.coremusic.net` DNS/konfigürasyon gerçekliği → V.R. + iskelet doğrulama (dizin + DNS kaydı) → **şart 3** |
| Tur 3 — oy | **18 kabul / 2 çekimser / 0 red → KABUL** |
| Şartlar (bağlayıcı) | **(1)** staging kararı + deploy pipeline (1a-1b) · **(2)** DB izolasyon/backup politikası · **(3)** dev alt alan doğrulaması → §5.1/11-13 · §6.4 |
| Beklenen tartışma eksenleri | `staging` enum genişlemesi (ADR-015 kapısı, `constants.php:25` fail-fast) · staging'in adresi (subdomain vs host+path) · dev stack'i (ADR-039:127 `⚠️`) · DB adlandırma deseni + gerçek veri kopyası yasağı · cookie scope daraltması ↔ ADR-058 `domain=.coremusic.net` SSO · `FORCE_AUTH_BYPASS` staging davranışı · deploy gate'lerinin (D4) sırası · `.gitleaks.toml` kapsamı |
| Debate RED ise | §5.2 geri dönüş + §4.4 fallback birlikte uygulanır |

---

*ADR-082 v1.0.0 — 2026-10-01 Created — Authority: CoreMusic Vault Steward — Mode: Red Team · Human Mode · Truth Mode*
