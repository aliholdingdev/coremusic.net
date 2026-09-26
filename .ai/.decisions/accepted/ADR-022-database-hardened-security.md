---
title: "CoreMusic — ADR-022: Database Hardened Security (Prepared Statement Yasağı · En Az Yetki DB Kullanıcıları · PII AES-256-GCM · Şifreli Backup · DB Audit Log · Production'da Direkt SQL Yasağı)"
type: adr
category: security
date: 2026-09-25
updated: 2026-09-25
version: 1.0.0
status: accepted
authority: ADR-022 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-022: Database Hardened Security (Prepared Statement Yasağı · En Az Yetki DB Kullanıcıları · PII AES-256-GCM · Şifreli Backup · DB Audit Log · Production'da Direkt SQL Yasağı)

**Durum:** accepted (kullanılabilir — **frozen YOK**)
**Tarih:** 2026-09-25
**Karar Veren:** Vault Steward (kullanıcı onaylı — "ADR-022'yi sıfırdan yaz"; karar içeriği kullanıcı onaylı: **(a) prepared statements her yerde (ADR-002 — raw query istisnasız yasak) · (b) en az yetki DB kullanıcıları (uygulama: SELECT/INSERT/UPDATE/DELETE yalnız — DDL/GRANT/FILE yok; migration kullanıcısı ayrı) · (c) app-level AES-256-GCM PII şifreleme · (d) backup şifreleme (mysqldump | age/gpg veya şifreli hedef) · (e) DB audit log (kim, hangi sorgu, ne zaman — ADR-005 logging) · (f) production'da DB'ye direkt SELECT sorgu yasağı (yalnız uygulama üzerinden)** · debate: **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL — 3 şart §5.4)** · Tech Lead: **✅ (2026-09-25)**
**İlgili ADR'ler:** [[ADR-002-pdo-mandatory-no-orm]] (PDO zorunlu, ORM/raw query yasak — bu ADR bunu DB katmanı sertleştirerek güçlendirir; dosya diskte VAR ✅) · [[ADR-003-multi-db-bcnf]] (18 BCNF veritabanı — yetki/audit/şifreleme bu 18 DB'ye uygulanır; dosya diskte VAR ✅; dikkat: karar dizini `../index` satır 40 eski slug `ADR-003-multi-db-9-databases` → `ADR-003-multi-db-bcnf` olarak düzeltildi (2026-09-25 — slug eşleşmesi ✅)) · [[ADR-005-ultrathink-protocol]] (audit/log disiplini + `⚠️ VERIFICATION REQUIRED` kanıt standardı; dosya diskte VAR ✅) · [[ADR-014-multi-db-migration-strategy]] (migration akışı → ayrı migration kullanıcısı bu ADR'nin bağlayıcısı; dosya diskte VAR ✅) · [[ADR-015-env-parser-strategy]] (şifreleme anahtarı .env → DI `Config` servisinden okunur, `getenv()` ihlali yasak; dosya diskte VAR ✅) · [[ADR-020-api-public-security]] ("audit PHP 0" bulgusu bu ADR'de kapatılır; dosya diskte VAR ✅) · [[ADR-021-spa-router-immutable-contract]] (router'a DB sorgusu eklenemez — bu ADR üretimde DB erişimini tek kapıda toplar; dosya diskte VAR ✅) · karar dizini [[../index]] **satır 59** `[[ADR-022-database-hardened-security]]` (slug eşleşmesi ✅).

---

## 1. Bağlam (Context)

CoreMusic'in 18 BCNF veritabanı ([[ADR-003-multi-db-bcnf]]) ve PDO erişim katmanı ([[ADR-002-pdo-mandatory-no-orm]]) mevcut; ancak **veritabanı sertleştirmesi** (least-privilege DB kullanıcısı, PII alan şifreleme, şifreli backup, sorgu audit'i, production'da direkt SQL erişim yasağı) hiçbir dosyada yazılmıyor ve hiçbir kodda uygulanmıyor. SQL injection savunması bugün yalnızca "prepared statement kültüründe" duruyor — kültür tek başına denetlenebilir değil; yetkisiz bir DB kullanıcısı, şifresiz backup veya audit'siz bir sorgu yolu tek başına ihlal yaratır. Bu ADR **kod üretmez** — DB güvenliği sözleşmesini yazar, altı bileşeni bağlayıcı kılar ve mevcut durumu dürüst etiketler (IMPLEMENTED / PLANNED).

### 1.1 Mevcut Durum

**Kod/vault kanıtları (diskte okundu — IMPLEMENTED/PLANNED etiketleri dosya + satır ile):**

**A) PDO / PREPARED STATEMENT — IMPLEMENTED (kod katmanı temiz):**

- **Tek bağlantı kapısı:** `shared/src/Database/DatabaseManager.php:12-28` — `new \PDO(...)` yalnız burada; `:25` **`ATTR_EMULATE_PREPARES => false`** (gerçek server-side prepared), `:23` `ERRMODE_EXCEPTION`, `:26` `PERSISTENT => true`.
- **Tüm erişim prepare üzerinden:** `DatabaseManager.php:30-36` `execute()` → `$this->pdo->prepare($sql)` (`:32`); `:38-42` `write()` → `prepare` (`:40`). Yani **ham `query()` API'si sınıf yüzeyinde hiç yok.**
- **Raw query grep (`->query(`, PHP kodu):** repo geneli 19 eşleşme → **tümü dokümantasyon/şablon/md** (`.claude/skills/ui-code-generator/references/anti-patterns.md:469,565` yasak örnek olarak, `shared/src/Database/CLAUDE.md:96-97` yasak örnek olarak, `.ai/architecture/k5-veri-yonetimi/*.md` kod örnekleri) → **PHP kaynak kodunda `->query(` = 0** → raw query riski **0 (bugün)**.
- **String-concat SQL grep (`'... ' . $` içinde SELECT/WHERE/FROM):** PHP → **0 eşleşme.**
- **`$_GET` kullanımı:** 17 yer (`home.coremusic.net/config/bootstrap.php:29`, `auth.coremusic.net/index.php:142`, `pages/login.php:14-16`, `shared/src/Api/Bff/BffLayer.php:75` …) — **hiçbiri SQL'e concatenate edilmiyor** (concat grep 0 ile çapraz) → parametre yolu hep bind.
- **Repository örneği:** `auth.coremusic.net/include/Repository/UserRepository.php:49` `'SELECT ' . self::USER_COLUMNS . ' FROM users WHERE email = :email ...'` — concat **yalnız sabit sabitlerle** (`self::USER_COLUMNS`), değer bind ile (`:email`); `:117,:168,:197,:211` hep `:param`.
- **OAuth hazırlıklı sorgular:** `shared/src/OAuth/OAuthManager.php:119,164,179,196,216,233` — 6 `prepare`.
- **`SELECT *` / ORM:** ORM yasak (ADR-002, `shared/AGENTS.md` Yasak #3) — repo'da ORM bağımlılığı 0.

**B) ŞİFRELEME / ARGON2 — KISMEN IMPLEMENTED:**

- **Şifre = argon2id IMPLEMENTED:** `auth.coremusic.net/include/Domain/ValueObject/Password.php:38` `password_hash($peppered, PASSWORD_ARGON2ID, [...])` + `:54` `password_verify($peppered, $hash)`; **pepper** `APP_PEPPER` — boşsa kritik log (`auth.coremusic.net/config/constants.php:65`); testler `PasswordTest.php:36,30` `$argon2id$` prefix assert + `UserTest.php:20` `m=65536,t=4,p=2` örneği.
- **bcrypt grep (`PASSWORD_BCRYPT`):** PHP → **0** → proje **bcrypt kullanmıyor** (argon2id zaten standart).
- **AES-256-GCM IMPLEMENTED ama YALNIZCA OAuth token:** `shared/src/OAuth/OAuthManager.php:244-291` — `encrypt()` `:249` `openssl_encrypt(..., 'aes-256-gcm', ...)`, IV `:246` `random_bytes(12)` (benzersiz), tag 16 bayt (`:257`), paketleme `iv‖tag‖ciphertext` (`:260`); `decrypt()` `:266-291` doğrulamalı. **PII alan şifrelemesi (ad/telefon/adres/ödeme) için tek satır yok** → **PLANNED.**
- **Şema denetim aracı (script):** `.claude/skills/database-normalize-maker/scripts/security-audit.php:63` hassas kolonun AES-256-GCM'e uygun (TEXT/VARCHAR(512)) istediğini denetler; `:74-78` kritik tablo için `_audit` tablosu şartını denetler → **araç var, uygulama yok.**
- **DB PII kolonları:** `.ai/.sql/mysql/*.sql` grep `phone|address|payment_card|card_number` → yalnız `ip_address`/`mac_address` ve `email` (`coremusic_auth.sql:25`) gibi **düz kolonlar**; şifreli blob kolonu **0** → **PII field encryption PLANNED.**

**C) DB KULLANICI YAPILANDIRMASI — PLANNED (kanıt 0):**

- **`CREATE USER` / `GRANT ` / `IDENTIFIED BY` / `REVOKE`:** `.ai/.sql/mysql/*.sql` (18 dump) → **0 eşleşme**; repo geneli `.sql` (3 şablon dosyası) → **0**; yalnız ADR-020'nin web araştırma metninde geçiyor (`ADR-020-api-public-security.md:86` — metin, kod değil).
- **Konfigürasyon:** `shared/config/.env.example` + `auth.coremusic.net/config/.env.example` → `DB_HOST/DB_PORT/DB_CHARSET/DB_USER=/DB_PASSWORD=/DB_NAME` — **yetki/rol tanımı yok** (`GRANT` yok); `shared/src/Database/Config/DatabaseConfig.php` yalnız host/port/db/user/charset.
- **Sonuç:** uygulama kullanıcısının yetkileri (SELECT-only mi, DDL var mı, FILE var mı) **diskte hiçbir yerde yazılmıyor** → en az yetki **PLANNED (bu ADR ile sözleşmeye girer).**

**D) BACKUP — PLANNED (şifreli backup kanıt 0):**

- **Backup dosyası/script grep (`*backup*`, `*mysqldump*` — .ai ve vendor/node_modules dışı):** repo → **0 dosya.**
- **`mysqldump` geçen yerler (yalnız vault dokümanı):** `.ai/architecture/k5-veri-yonetimi/backup-strategy.md` (SSOT strateji), `.ai/architecture/k5-veri-yonetimi/README.md:120` (Full/Haftalık/4 hafta/mysqldump), `.ai/PROJECTS.md:657` (`mysqldump + incremental`, günlük, 30 gün), `.ai/.templates/adr/adr-database-template.md:254` (`mysqldump --single-transaction`, günlük 03:00).
- **Şifreleme (`age`, `gpg`, `openssl enc`) geçen backup satırı:** vault geneli **0** → **backup şifreleme PLANNED.**

**E) AUDIT — ŞEMA IMPLEMENTED, PHP YAZICI 0:**

- **DB audit tabloları IMPLEMENTED:** `.ai/.sql/mysql/coremusic_logs.sql:23` `audit_logs` (alanlar `:24-34` — `user_id`, `action`, `entity_type`, `entity_id`, `old_value JSON`, `new_value JSON`, `ip_address`, `session_id`, `created_at`; yorum `:20` "Audit trail for all critical system actions"), `:127` `rate_limit_logs`, `:487` `log_security`; `coremusic_auth.sql:249` `credential_audit`, `:352` `permission_audit`; `coremusic_media.sql:204` `media_audit` → **toplam 6 audit tablosu.**
- **PHP audit yazıcı grep (`audit_logs|log_security|credential_audit`, `*.php`):** **0 eşleşme** → ADR-020 bulgusu **"audit PHP 0" TEYİT EDİLDİ** (hiçbir kod bu tablolara satır atmıyor).
- **"Kim, hangi sorgu, ne zaman" (sorgu-seviyesi audit):** `general log`/audit plugin/sorgu interceptor **0** (vault + kod) → **query-level audit PLANNED.**
- **Dosya log'u IMPLEMENTED (ama DB audit'i değil):** `shared/src/PageRouter/StructuredLogger.php:67` `error_log(json_encode(...))`, `PageRouterKernel.php:134` FATAL traceId.

**F) PRODUCTION'DA DİREKT DB ERİŞİMİ — PLANNED (politika bugün yazılmamış):**

- Production'da `mysql` CLI / BI aracı ile direkt `SELECT` yasağı **hiçbir vault/kod dosyasında yok** → bu ADR §2-f ile bağlayıcı olur; erişim yalnız uygulama kullanıcısı üzerinden (§2-b ile birleşince DB sunucusuna doğrudan oturum açan tek kimlik migration kullanıcısı olur, o da bakım penceresinde).

**Sonuç etiketi:** **IMPLEMENTED:** PDO prepare tek kapı (`DatabaseManager:32,40`), `EMULATE_PREPARES=false` (`:25`), PHP'de raw `query(` 0 / concat SQL 0 / `$_GET`-SQL concat 0, argon2id + pepper (`Password.php:38`, `constants.php:65`), AES-256-GCM sarmalayıcı (`OAuthManager:244-291`), 6 DB audit tablosu (şema), 18 DB dump. **PLANNED:** PII alan şifreleme, en az yetki DB kullanıcıları (+ migration kullanıcısı), şifreli backup, DB audit PHP yazıcı + sorgu-seviyesi audit, production direkt SQL yasağı. `⚠️ VERIFICATION REQUIRED`: DB kullanıcısı yetkileri (dosya yok), backup şifreleme (0), audit PHP yazıcı 0, PII şifreli kolon 0.

### 1.2 Sorun Tanımı

1. **Savunma tek katmanlı:** SQL injection koruması yalnız "iyi alışkanlık" (prepare) — denetlenebilir kapı yok; bir geliştirici yarın ham `query()` yazarsa hiçbir test/kırmızı kapı durdurmaz (ADR-002 kuralı var ama CI kapısı yok).
2. **DB kullanıcısı yetkisi bilinmiyor:** `.env`'de `DB_USER` boş/boş bırakılmış, dump'larda `GRANT` yok → uygulamanın DDL/FILE/GRANT yetkisiyle mi çalıştığı **yazılmamış**; yetki fazlalığı = SQL injection'ın etkisi artar (FILE ile dosya okuma, DDL ile şema yıkma).
3. **PII düz saklanıyor:** `email` (`coremusic_auth.sql:25`), `ip_address` (11 tablo), ileride ad/telefon/adres/ödeme alanları **şifrelenmeden** duruyor; DB dump'ı / backup'ı sızınca sızıntı doğrudan olur. AES-256-GCM sarmalayıcısı yalnız OAuth token'da (`OAuthManager:244`) — PII'da yok.
4. **Backup şifresiz:** vault strateji dosyaları `mysqldump` diyor (`backup-strategy.md`, `README.md:120`), şifreleme **0 satır** → yedek = en kolay sızıntı yüzeyi.
5. **Audit yarım:** 6 audit **tablosu** var, PHP'den yazan **0 kod** (ADR-020 bulgusu teyit) → "kim ne yaptı" sorusu DB'de cevapsız; sorgu-seviyesi audit hiç yok (hangi SQL çalıştı, kim çalıştırdı).
6. **Direkt erişim kapalı değil:** üretimde DB'ye doğrudan `SELECT` çeken bir kişi/araç var mı, bunu engelleyen politika/MySQL rolü **yok** → denetimsiz okuma yolu.
7. **Şifreleme anahtarı prosedürüsüz:** `.env` anahtarı var (ADR-015) ama **rotation** (döndürme) ve **backup anahtarının ayrı saklanması** kuralı yazılmamış; unutulan anahtar = kalıcı veri kaybı.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: prompt-maker web araştırması protokolü (`.claude/skills/prompt-maker/references/10-web-research-protocol.md` — diskte VAR ✅) — resmi/anahtar kaynak önce (dev.mysql.com, mariadb.com, cheatsheetseries.owasp.org, ietf/OWASP), **her ana iddia ≥2 bağımsız çapraz kaynak**, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`. **Odak: (a) MySQL/MariaDB security hardening checklist 2025-26 (least privilege, FILE privilege, dedicated user), (b) application-level field encryption (AES-GCM + blind index, equality leakage), (c) backup encryption practices (age/gpg, anahtar ayrı saklama), (d) least-privilege DB user GRANT matrisi, (e) argon2 vs bcrypt 2025-26.** Erişim: **5 websearch sorgusu**; sonuçlar başlık/özet düzeyinde derlendi — derin sayfa-içi tur ayrı doğrulamaya bırakıldı (açıkça işaretli).

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "MySQL MariaDB security hardening checklist 2025 least privilege database user CREATE USER GRANT FILE privilege" · (2) "application-level field level encryption AES-256-GCM blind index searchable encryption equality leak pattern database PII 2025" · (3) "mysqldump backup encryption best practices age gpg 2025 encrypted backup key separate storage" · (4) "argon2id vs bcrypt 2025 2026 password hashing OWASP recommendation PHP" · (5) "MySQL enable audit log general query log performance production 2025 database activity monitoring slow query log" |
| Web Search **Konusu** | (1) En az yetki: uygulama/kullanıcıya yalnız gereken GRANT; ayrı dedicated user; FILE privilege'nin tüm kullanıcılar için kapatılması (dosya okuma/yazma → `INTO OUTFILE`); (2) Alan-seviyesi şifreleme: ECB desen sızıntısı yasak, CBC yalnız Encrypt-then-MAC, AES-256 standardı; **blind index** = şifreli alan için ayrı deterministic hash kolonu → eşitlik araması çalışır, sıralama/kısmi eşleşme sızar; envelope encryption + Vault/KMS; (3) Backup şifreleme: `mysqldump` + `age`/`gpg`/`openssl` pipe, **anahtar asla backup'la aynı yerde saklanmaz**, offline/off-site anahtar; (4) argon2id vs bcrypt: OWASP argon2id birinci tercih (memory-hard), bcrypt legacy; PHP `PASSWORD_ARGON2ID`; (5) MySQL audit: genel sorgu log her SQL'i yazar (maliyet/yük), audit plugin, uygulama-seviyesi interceptor (kim/sorgu/parametre) — üretimde denetim için "who did what when". |
| Web Search **Bağlam** | **~44 adlandırılmış kaynak / 5 sorgu**: mariadb.com + dev.mysql.com (privileges) + ctrlops.io + percona.community + stackoverflow + scidsg.medium (FILE) + dba.stackexchange + technoroots + **owasp.org Database Security Cheat Sheet** + contabo (10) · besthub.dev + github (vagpap app-level-encryption) + ivanball ADR-037 + stackpractices + bioquro (2026) + github (sequelize-encryption-hooks) + dhdtech (searchable encryption 2026) (7) · reddit (homelab age) + serverfault + oneuptime + severalnines + medium (pg backup) + onenine + linkedin + security.stackexchange (age) + baculasystems + liquidweb (10) · **owasp.org Password Storage Cheat Sheet** + encryptionconsulting + bellatorcyber + guptadeepak 2026 + skycloak + medium (ijas) + shattered (benchmark) + linkedin + khimananda + toolsana (10) · bytebase ×2 + **dev.mysql.com (audit-log + general query log)** + geeksforgeeks + oneuptime + tessell (7) |
| Web Search **Kısa Açıklama** | **(1) Hardening:** Percona/mariadb "over-granting en yaygın hata → uygulama yalnız gereken"; OWASP "FILE privilege her kullanıcıda kapat"; ctrlops wildcard host/süper kullanıcı denetimi → **uygulama kullanıcısı: DML + INDEX/CREATE VIEW gerekirse, DDL/GRANT/FILE/SUPER yok.** **(2) PII şifreleme:** ECB reddedilir (desen sızıntısı), AES-256 kurumsal standart; **blind index ayrı deterministic hash kolonu** → "aranan alan hem şifreli hem sorgulanıyorsa deterministic şema ya da blind index şart" (ivanball ADR-037); envelope/KMS + Vault (vagpap) → **AES-256-GCM + kör indeks + ADR-015 anahtarı.** **(3) Backup:** `mysqldump` + `age` asimetrik (sunucu yalnız public key ile yazar — reddit/homelab), "anahtarları backup'ın yanında **asla** saklama" (medium/onenine/liquidweb) → **ayrı saklama zorunlu.** **(4 argon2):** OWASP cheat sheet "Argon2id, bcrypt veya PBKDF2" ama 2024+ rehber argon2id'yi **birinci** sıraya alır (memory-hard, GPU dirençli); bcrypt legacy/work factor ≥10 → **argon2id (zaten kodda).** **(5) Audit:** genel sorgu log her SQL'i yazar ama üretimde yavaş/verbose; audit plugin veya **uygulama interceptor'ı** (kim, sorgu, parametre) → ADR-005 logging ruhuyla DB audit log'a yaz. |
| Web Search **Uzun Açıklama** | **(a) MySQL/MariaDB hardening (kaynak 1-10):** OWASP Database Security Cheat Sheet doğrudan "disable the FILE privilege for all users" der ve MySQL/MariaDB hardening rehberlerine bağlar; dev.mysql.com privileges sayfası `FILE`'in `INTO OUTFILE`/`LOAD_FILE` kapısı olduğunu, `GRANT OPTION`'ın yetki devrini, `SUPER`'ın bypass olduğunu anlatır; Percona 2026 yazısı en yaygın hatanın over-granting olduğunu, uygulama hesabının minimum GRANT ile açılmasını; mariadb "Principle of Least Privilege + dedicated user"; ctrlops denetim listesi wildcard host (`'%'`), FILE ve fazla süper kullanıcı taraması; stackoverflow/dba.stackexchange pratik ayrımlar (migration/superuser hesabı ayrı, runtime hesabı DML-only). Çapraz: OWASP + Percona + mariadb aynı hükmü veriyor. **(b) Field encryption + blind index (kaynak 11-17):** besthub ECB/CBC/AES-256 çerçevelemesi; vagpap app-level-encryption örneği envelope + blind index + Vault KMS + PostgreSQL; ivanball ADR-037 net cümle: "aranan alan hem şifreli hem lookup gerekiyorsa ayrı deterministic scheme ya da blind index gerekir — bu converter onu vermez"; stackpractices/bioquro 2026 rehberi AES-256-GCM + envelope + rotation; sequelize-encryption-hooks "cipher DB'ye gitmeden önce, DB yalnız ciphertext görür"; dhdtech 2026 "PII'yi aranabilir kıl ama sızdırma: blind index + key management". Çapraz: deterministic eşitlik sızıntısı (sıralama/korelasyon) üç ayrı kaynakta aynı uyarı. **(c) Backup encryption (kaynak 18-27):** age asimetrik model — sunucu public key ile şifreler, private key off-site (reddit homelab + security.stackexchange age başlığı); severalnines/baculasystems "özel anahtarı koru — yedekleri günlük şifreliyorsan erişim tek konu"; medium/onenine/liquidweb "anahtarları backup ile **ayrı** tut, offline"; oneuptime MySQL backup şifreleme openssl/yolları. **(d) argon2 vs bcrypt (kaynak 28-37):** OWASP Password Storage Cheat Sheet (Argon2id/bcrypt/PBKDF2 — güçlü, yavaş), encryptionconsulting "Argon2id önerilen, memory-hardness belirleyici", bellatorcyber/guptadeepak/skycloak/toolsana "OWASP argon2id varsayılan, bcrypt legacy (work factor ≥10)", shattered 2026 benchmark "argon2 kırma maliyeti bcrypt'e göre çok yüksek", PHP tarafı `PASSWORD_ARGON2ID` (zaten kodda `Password.php:38`). **(e) Audit logging (kaynak 38-44):** dev.mysql.com general query log "her alınan SQL ifadesini yazar" (doğru ama pahalı), audit-log plugin yapılandırması (sıkıştırma/şifreme), bytebase "genel log → plugin → binlog CDC → uygulama interceptor; her biri bir yerde eksik", tessell "kim, ne, ne zaman, hangi nesne — doğrulanabilir iz", uygulama cursor interceptor'ı "kullanıcı, sorgu, parametreleri execute'tan önce kaydet". |
| Web Search **Paragraf Veri Uzun** | MySQL/MariaDB hardening 2025-26: en az yetki + dedicated kullanıcı + FILE kapat (OWASP Database Security Cheat Sheet, Percona, mariadb, ctrlops, dev.mysql privileges); over-granting en yaygın hata → uygulama hesabı DML-only, migration hesabı ayrı. PII: ECB yasak, AES-256-GCM + envelope/KMS; aranan şifreli alan için blind index (deterministic hash kolonu) — eşitlik çalışır, sıralama/korelasyon sızar, anahtar ADR-015 env'te + rotation (besthub, vagpap, ivanball ADR-037, stackpractices, bioquro, dhdtech). Backup: `mysqldump` + `age`/`gpg`, anahtar asla backup'la aynı yerde, off-site/offline (reddit, stackexchange, severalnines, onenine, liquidweb, medium). Argon2id OWASP birinci tercih (memory-hard, GPU dirençli), bcrypt legacy ≥10 (OWASP cheat sheet, encryptionconsulting, guptadeepak, skycloak, shattered); PHP `PASSWORD_ARGON2ID` ile aynı. Audit: genel log verbose/pahalı → audit plugin veya uygulama interceptor (kim/sorgu/parametre/ne zaman) — dev.mysql.com, bytebase ×2, tessell. |
| Web Search **Sonucu** | 1) **En az yetki DB kullanıcısı doğrulandı** (kaynak 1-10, ≥2 çapraz: OWASP + Percona + mariadb): uygulama DML-only, DDL/GRANT/FILE/SUPER yok, migration kullanıcısı ayrı → **karar (b) literatürle birebir.** 2) **PII AES-256-GCM + blind index doğrulandı** (kaynak 11-17, ≥2 çapraz: ivanball ADR-037 + vagpap + dhdtech): deterministic eşitlik araması gerekirse blind index şart; **kör indeks eşitlik sızıntısı = bilinen sınır** → §4.3 risk 2. 3) **Backup şifreleme + anahtar ayrılığı doğrulandı** (kaynak 18-27, ≥2 çapraz: medium + onenine + liquidweb): anahtar backup'la aynı yerde **olmaz** → §5.1 adım 4'te ayrı saklama. 4) **argon2id > bcrypt 2025-26 doğrulandı** (kaynak 28-37, ≥2 çapraz: OWASP cheat sheet + guptadeepak/skycloak/toolsana): argon2id birinci tercih, bcrypt legacy → kod zaten `PASSWORD_ARGON2ID` (**IMPLEMENTED**, §1.1-B). 5) **Sorgu-seviyesi audit** (kaynak 38-44, ≥2 çapraz: dev.mysql.com + bytebase + tessell): genel log üretimde maliyetli → **uygulama-seviyesi DB audit log** (kim, sorgu, zaman) tercih edilir, ADR-005 logging'e yazar. **Toplam ~44 adlandırılmış kaynak, 5 sorgu**; sayfa-içi derin tur yapılmadığı için GRANT tam listesi/maliyet sayıları başlık/özet düzeyindedir (açıkça işaretli). |
| Web Search **Alınan Karar** | **ADR-022 KABUL EDİLİR — DATABASE HARDENED SECURITY (6 bileşen):** **(a) Prepared statements her yerde:** `DatabaseManager::execute/write` tek kapı (bugün zaten `prepare` — `DatabaseManager.php:32,40`, `EMULATE_PREPARES=false` `:25`); PHP'de raw `query(` = 0 ve concat SQL = 0 **korunur** → raw query **istisnasız yasak** (ADR-002 ile aynı, bu ADR denetimini ister). **(b) En az yetki DB kullanıcıları:** uygulama kullanıcısı yalnız `SELECT/INSERT/UPDATE/DELETE` (+gerekirse INDEX/VIEW), **DDL/GRANT/FILE/SUPER yok** (OWASP+Percona); **migration kullanıcısı ayrı** (ADR-014 akışı), bakım penceresi dışında devre dışı; dump'lara `CREATE USER/GRANT` bloğu eklenir (**bugün 0 → PLANNED**). **(c) App-level AES-256-GCM PII şifreleme:** hassas alanlar (ad, telefon, adres, ödeme) AES-256-GCM (authenticated encryption, **nonce/IV benzersiz** — `OAuthManager:246` deseninin aynısı, 12 bayt IV + 16 bayt tag); **email gibi sorgulanan alanlar** normalize edilir + ayrıca **indeksli hash (blind index)** → eşitlik araması çalışır, **sıralama/kısmi eşleşme yapılmaz**; anahtar ADR-015 **env Config servisinden** okunur (`getenv()` ihlali yasak — ADR-020 §1.4) + **rotation prosedürü**. **Şifreler argon2id** (`Password.php:38` IMPLEMENTED — bcrypt değil: 2025-26 OWASP tercihi, §1.3 kaynak 28-37). **(d) Backup şifreleme:** `mysqldump --single-transaction` + `age` (veya gpg/openssl) pipe; **şifreleme anahtarı backup ile aynı depoda saklanmaz** (ayrı/offline hedef); restore tatbikatı şart. **(e) DB audit log:** kim, hangi sorgu (sorgu metni + bağlam), ne zaman → `audit_logs`/`log_security` tablolarına (**şema IMPLEMENTED** `coremusic_logs.sql:23,487`; **PHP yazıcı 0 → PLANNED**, ADR-020 bulgusu burada kapanır); sorgu-seviyesi kayıt uygulama katmanında (genel log production'da açılmaz — §1.3 kaynak 38-44). **(f) Production'da direkt DB SELECT yasağı:** üretimde DB'ye doğrudan sorgu yalnız uygulama kullanıcısı üzerinden; doğrudan oturum (CLI/BI) **yalnız bakım penceresinde migration kullanıcısıyla** ve audit'e yazılı. **Koruma:** (1) raw query grep kapısı (CI'da `->query(` = 0), (2) GRANT matrisi dump'larda + gözden geçirme, (3) PII şifreleme testi (ciphertext, blind index eşitlik), (4) backup restore drill, (5) audit satırı doluluğu kontrolü. |
| Web Search **Sonuç** | Karar 2025-2026 verisiyle **desteklendi**: hardening/least-privilege (10 kaynak), PII field encryption + blind index (7), backup encryption (10), argon2id vs bcrypt (10), audit logging (7) → **~44 adlandırılmış kaynak, 5 sorgu**; çapraz doğrulama ≥2 kaynak beş ana iddiada da karşılanır, iki çıkarım (`⚠️` üretim maliyet sayıları sayfa-içi doğrulanmadı; blind index sıralama sızıntısı bağlamı) açıkça işaretlendi. Kod tarafı aynı resmi verdi: **PDO prepare + argon2id + AES-256-GCM sarmalayıcı + 6 audit tablosu IMPLEMENTED**; **DB kullanıcı yetkisi, PII alan şifreleme, şifreli backup, audit PHP yazıcı, direkt SQL yasağı PLANNED** → bu ADR **sözleşme, kod taahhüdü değil**; uygulaması §5.1 adımlarına bağlıdır. **Kaynak listesi (~44):** 1) mariadb.com — Platform Security (least privilege, dedicated user) · 2) dev.mysql.com — Privileges Provided by MySQL · 3) ctrlops.io — Database least-privilege checklist 2026 · 4) percona.community — Hardening MySQL for DBAs · 5) stackoverflow — Minimum privileges for MySQL users · 6) scidsg.medium — Restricting FILE privilege (MySQL/MariaDB) · 7) dba.stackexchange — Reasonable privileges for typical users · 8) technoroots — Secure MariaDB after installation · 9) **cheatsheetseries.owasp.org — Database Security Cheat Sheet** · 10) contabo — MySQL CREATE USER/GRANT guide · 11) besthub.dev — Field-level encryption done right · 12) github (vagpap) — app-level encryption (envelope + blind index + Vault) · 13) ivanball.github.io — ADR-037 Field-Level Encryption (AES-256-GCM) · 14) stackpractices — Encryption at rest (AES-256, KMS, envelope) · 15) bioquro — Database encryption in 2026 · 16) github (satyendra-sagar-singh) — sequelize-encryption-hooks (AES-256-GCM) · 17) dhdtech — Searchable encryption 2026 (blind index) · 18) reddit — age ile backup şifreleme · 19) serverfault — GPG private key saklama · 20) oneuptime — Encrypt MySQL backup files · 21) severalnines — Database backup encryption best practices · 22) medium — PostgreSQL backup encryption + key storage · 23) onenine — Backup encryption best practices · 24) linkedin — Encrypt database backups · 25) security.stackexchange — age tool for encrypted backups · 26) baculasystems — Backup encryption 101 · 27) liquidweb — Encryption key management (anahtar ayrı) · 28) **cheatsheetseries.owasp.org — Password Storage Cheat Sheet** · 29) encryptionconsulting — bcrypt vs Argon2 vs PBKDF2 · 30) bellatorcyber — Password hashing 2026 (Argon2id) · 31) guptadeepak — Argon2 vs bcrypt 2026 guide · 32) skycloak — bcrypt in 2026 (OWASP argon2id #1) · 33) medium (ijas) — bcrypt vs Argon2 · 34) shattered — Argon2 vs bcrypt benchmark 2026 · 35) linkedin — Hashes for 2026 (Argon2id memory-hard) · 36) khimananda — Argon2id vs bcrypt 2026 · 37) toolsana — Password hashing 2026 · 38) bytebase — MySQL audit logging guide · 39) dev.mysql.com — Audit Log Plugin configuration · 40) dev.mysql.com — The General Query Log · 41) geeksforgeeks — MySQL query log · 42) oneuptime — Configure general query log · 43) tessell — MySQL audit logs setup · 44) bytebase — How to enable auditing (application interceptor) |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| [[ADR-002-pdo-mandatory-no-orm]] | PDO prepared zorunlu, ORM yasak — bu ADR **raw query'yi istisnasız yasaklar** ve CI grep kapısı ister; `DatabaseManager` dışında bağlantı açılamaz (`shared/AGENTS.md` Zorunlu 1) |
| [[ADR-003-multi-db-bcnf]] (18 DB) | Yetki matrisi, PII şifreleme ve audit **18 veritabanının tamamına** uygulanır; tek DB'de uygulanmış sayılmaz (domain `coremusic_*` dump'ları `.ai/.sql/mysql/`) |
| [[ADR-015-env-parser-strategy]] | Şifreleme anahtarları yalnız .env → DI `Config` servisi; kodda `getenv()` ihlali (ADR-020 §1.4 `OAuthManager.php:77-78`) → anahtar okuma bu ADR'de de zorunlu; **anahtar/şifre bu ADR'ye yazılmaz (REDACTED)** |
| [[ADR-014-multi-db-migration-strategy]] | Migration kullanıcısı ayrı ve DDL yetkili; uygulama kullanıcısına DDL verilmez → migration akışı o ADR'nin adımlarında kalır (şema değişikliği oradan yürütülür) |
| [[ADR-005-ultrathink-protocol]] | Kod kanıtı olmayan her iddia etiketli: DB kullanıcısı yetkisi 0, şifreli backup 0, audit PHP yazıcı 0, PII şifreli kolon 0 → `⚠️ VERIFICATION REQUIRED` |
| [[ADR-020-api-public-security]] | "audit PHP 0" bulgusu ve `.env` `getenv()` ihlali bu ADR'nin §2-b/e maddeleriyle kapanır; audit **sorgu-seviyesi** burada tanımlanır, API olay audit'i o ADR'de |
| [[ADR-021-spa-router-immutable-contract]] | Router/request çözümlemesine DB sorgusu eklenemez; DB erişimi yalnız repository/DatabaseManager üzerinden (§2-f ile hizalı) |
| Domain boundary (`shared/AGENTS.md` §3-4, §5) | `src/Database/**` → Data Engineer (Database katmanı); `src/Security/**` + `.env` → Security Engineer; `*.sql` şema/migration → Data Engineer; test `shared/tests/**` → QA Engineer |
| In-Place Refactoring | Dosya adları (`DatabaseManager.php`, `Password.php`, `OAuthManager.php`, `coremusic_logs.sql`, `.ai/.sql/mysql/*.sql`, `.ai/.decisions/index.md` vb.) **onaysız değiştirilemez**; bu ADR yalnız karar yazar |
| Frozen ADR-001-037 dokunulmaz | Yalnız okunur + referanslanır (`AGENTS.md` §25.3 kural 2) |
| `.ai/log.md` append-only | Bu işlem dahil tüm kayıtlar yalnız ekleme (bayt-seviyesi) |
| REDACTED | DB şifresi, şifreleme anahtarı, backup anahtarı, API anahtarı hiçbir koşulda bu ADR'ye yazılmaz |
| Numara kuralı | "Yeni ADR ≥ 088" bu yazımda uygulanmaz: `ADR-022` `.ai/.decisions/index.md:59`'da **rezerve boş slottur** (doldurma — ADR-019/020/021 aynı istisnayı kaydetmişti) |

---

## 2. Karar (Decision)

**CoreMusic veritabanı erişimi ALTI maddeyle sertleştirilir ve bağlayıcı ilan edilir: (a) Prepared statements her yerde — `DatabaseManager::execute/write` tek kapı, PHP'de raw `query(` / string-concat SQL istisnasız YASAK (ADR-002'nin denetim kapısı: CI'da grep 0 korunur). (b) En az yetki DB kullanıcıları — uygulama kullanıcısı yalnız SELECT/INSERT/UPDATE/DELETE (gerekirse INDEX/VIEW), DDL/GRANT/FILE/SUPER yok; migration kullanıcısı ayrı (ADR-014), bakım penceresi dışında devre dışı; dump'lara CREATE USER/GRANT bloğu eklenir (bugün 0 → PLANNED). (c) App-level AES-256-GCM PII şifreleme — hassas alanlar (ad, telefon, adres, ödeme) AES-256-GCM authenticated encryption ile (nonce benzersiz — OAuthManager deseni); email gibi sorgulanan alanlar normalize edilip ayrıca indeksli hash (blind index) taşır — kör indeks eşitlik araması verir, sıralama/kısmi eşleşme yapılmaz; anahtar ADR-015 env Config servisi + rotation prosedürü; şifreler argon2id (bcrypt değil — 2025-26 OWASP tercihi). (d) Backup şifreleme — mysqldump | age/gpg pipe veya şifreli hedef; anahtar backup'la aynı depoda saklanmaz; restore drill şart. (e) DB audit log — kim, hangi sorgu, ne zaman: uygulama-seviyesi kayıt `audit_logs`/`log_security` tablolarına (şema var, PHP yazıcı PLANNED — ADR-020 bulgusu kapanır); production'da genel sorgu log'u açılmaz. (f) Production'da DB'ye direkt SELECT yasağı — sorgu yalnız uygulama üzerinden; doğrudan oturum yalnız bakım penceresinde migration kullanıcısıyla ve audit'e yazılı.**

### 2.1 Neden Bu Seçenek?

- **Kod bugün temiz, kapı yok:** raw `query(` 0, concat SQL 0, `$_GET`-SQL 0 (§1.1-A) → mevcut durum iyi; ama **denetlenebilir kılmadan** yarın bozulabilir → CI grep kapısı + bu ADR metni tek cümlede yasağı pekiştirir (ADR-002 kültür → sözleşme).
- **Yetki = derin savunma:** SQL injection/erroneous query bir kez olsa bile uygulama kullanıcısında DDL/FILE olmaması etkiyi "veri okuma" ile sınırlar; OWASP + Percona + mariadb aynı hükme varıyor (§1.3 kaynak 1-10).
- **PII şifreleme sızıntıyı kırar:** backup/dump/DB kopyası sızsa bile AES-256-GCM ciphertext okunmaz; aranan alan (email) blind index ile korunur — "şifrele + arama yapamaz" ikilemi §1.3 kaynak 11-17'deki standart çözüm.
- **argon2id zaten kodda:** `Password.php:38` IMPLEMENTED, bcrypt grep 0 → karar **yeni iş değil, standart teyidi** (2025-26 OWASP argon2id #1 — kaynak 28-37); maliyet (§4.2/§4.3) biliniyor ve kabul.
- **Audit olmadan hepsi görünmez:** 6 tablo var, yazan 0 kod (§1.1-E) → ADR-005 "kim/ne/ne zaman" disiplini DB'ye taşınmadan sertleştirme **denetlenemez**; ADR-020'nin "audit PHP 0" bulgusu burada kapanır.
- **Direkt SQL yasağı denetim tekeli:** uygulama dışındaki her sorgu (BI, CLI, taşınan script) audit zincirini kırar; tek kapı + bakım penceresi (migration kullanıcısı) hem pratik hem denetlenebilir.

### 2.2 Teknik Detaylar

**a) Prepared statement yasağı (bağlayıcı):**

| Kural | Değer | Kanıt / Dayanak |
|-------|-------|-----------------|
| Tek bağlantı kapısı | `DatabaseManager` dışında `new PDO` YASAK | `DatabaseManager.php:12-28`; `shared/AGENTS.md` Zorunlu 1 |
| Tüm sorgu | `prepare` + bind; `->query(` PHP kodunda **0** | `DatabaseManager.php:32,40`, `OAuthManager.php:119-233` (6 prepare) |
| Emülasyon | `ATTR_EMULATE_PREPARES => false` (server-side prepare) | `DatabaseManager.php:25` |
| String-concat SQL | Değer concat **YASAK**; sabit sabit concat serbest (`self::USER_COLUMNS`) | `UserRepository.php:49` örneği; concat-SQL grep 0 |
| `$_GET`/`$_POST` → SQL | Doğrudan geçiş YASAK; her zaman bind | `$_GET` 17 kullanımın 0'ı SQL'de (§1.1-A) |
| CI kapısı | Repo taraması: `->query(` = 0, `'(SELECT\|WHERE).*\.$` = 0 → kırmızı | §5.1 adım 1 (PLANNED) |
| ORM / `SELECT *` | Yasak (ADR-002, `shared/AGENTS.md` Yasak #3) | repo ORM bağımlılığı 0 |

**b) En az yetki DB kullanıcıları (bağlayıcı — GRANT matrisi):**

| Kullanıcı | Yetkiler | Yasak | Kullanım |
|-----------|----------|-------|----------|
| `app` (uygulama — `.env` `DB_USER`) | `SELECT, INSERT, UPDATE, DELETE` (+gerekirse `INDEX, CREATE VIEW, SHOW VIEW`) | **`CREATE, ALTER, DROP, GRANT OPTION, FILE, SUPER, SHUTDOWN, RELOAD, PROCESS`** | Her istek/CLI — 18 DB'nin kendi şemasında |
| `migration` (ADR-014) | Şeması üzerinde `CREATE/ALTER/DROP/INDEX` + `SELECT` | `GRANT OPTION`, `FILE` (_DUMP harici) | Yalnız deploy/migration penceresi, sonra **devre dışı** |
| `backup` (opsiyonel, §d) | `SELECT, LOCK TABLES, SHOW VIEW, TRIGGER` (dump için) | `INSERT/UPDATE/DELETE` | `mysqldump` çalıştıran servis hesabı |
| Host | Uygulama sunucusu IP'si — `'%'` wildcard **YASAK** | — | ctrlops/OWASP denetim maddesi (§1.3 kaynak 3, 9) |
| Dosya kanıtı | **PLANNED:** dump'lara `CREATE USER ... IDENTIFIED BY` (REDACTED) + `GRANT ...` blokları | Bugün `.sql` dump'larında GRANT **0** | §5.1 adım 2 |

**c) PII alan şifreleme + kör indeks (bağlayıcı):**

| Alan tipi | Şema | Örnek | Arama |
|-----------|------|-------|-------|
| Hassas (ad, telefon, adres, ödeme kartı, t.c.) | **AES-256-GCM** (authenticated, nonce/IV **12 bayt benzersiz**, tag 16 bayt) — `iv‖tag‖ciphertext` paketi (`OAuthManager.php:260` deseni) | `<kolon>_enc VARBINARY(512)` | **Yok** (decryption sonra uygulamada) |
| Sorgulanan hassas (email) | Normalize (lowercase + trim) + **ayrı blind index kolonu**: `HMAC-SHA256(normalize_email, anahtar)` → `email_bi VARBINARY(32)` + unique index | `WHERE email_bi = :bi` | **Eşitlik** ✅ · sıralama/`LIKE`/kısmi ❌ (kör indeks notu) |
| Şifre | **Argon2id** (pepper `APP_PEPPER`) — `$argon2id$` | `Password.php:38` (**IMPLEMENTED**) | `password_verify` (`:54`) |
| Anahtar | ADR-015 env → DI `Config` servisi (`getenv()` yasak); **rotation**: yeni anahtar üret → alanı yeniden şifrele (batch) → eski anahtar emekli | `shared/src/Config/` | — |
| Etiket | `coremusic_auth.sql:25` email bugün **düz** → şema değişikliği §5.1 adım 3'te (Data Engineer, migration-template) | PLANNED | — |

**d) Backup şifreleme (bağlayıcı):**

| Kalem | Karar |
|-------|-------|
| Boru hattı | `mysqldump --single-transaction ... \| age -r <public-key> > backup.sql.age` (veya `gpg --encrypt --recipient`); **ham dump diskte bırakılmaz** |
| Anahtar | Şifreleme anahtarı backup deposundan **ayrı** (offline/off-site); `age` asimetrik → sunucu yalnız public key ile yazar (§1.3 kaynak 18-27) |
| Saklama | 18 DB için günlük tam + haftalık/delta (k5 `backup-strategy.md` takvimi korunur — takvim değişmez, **şifreleme eklenir**) |
| Restore | **Restore drill** (çeyreklik): rastgele backup açılır, satır sayısı + checksum doğrulanır; anahtar kaybı riski §4.3/1 |
| Bugünkü durum | Backup script **0 dosya**, şifreleme satırı **0** → **PLANNED** |

**e) DB audit log (bağlayıcı):**

| Kalem | Değer |
|-------|-------|
| Kayıt alanı | `kim` (DB kullanıcısı + uygulama user_id), `ne` (sorgu şablonu/SQL, tablo), `ne zaman` (ts), `sonuç` (satır/hata), `istek bağlamı` (traceId — StructuredLogger ruhu) |
| Hedef tablo | `audit_logs` (`coremusic_logs.sql:23-42`), güvenlik olayı → `log_security` (`:487`) — **şema IMPLEMENTED** |
| Yazıcı | Uygulama katmanı interceptor (repository sarmalayıcı) — **PHP yazıcı bugün 0 → PLANNED** (ADR-020 "audit PHP 0" kapanır) |
| Sorgu-seviyesi | Uygulama interceptor'ü; **MySQL genel sorgu log production'da AÇILMAZ** (verbose/yavaş — §1.3 kaynak 40, 44) |
| Hassas değerler | Kayıtta parametre değerleri `[REDACTED]` (ADR-005/REDACTED politikası) — yalnız şablon + meta |
| Bakım | Retention 90 gün (logs DB), `audit_logs` → arşiv sonra silme (k5 README politikası ile uyumlu — ⚠️ retention rakamı §5.1'de teyit) |

**f) Production'da direkt DB erişim yasağı (bağlayıcı):**

```
Üretim sorgu yolu = uygulama (app kullanıcısı)  →  audit  →  DB
Doğrudan oturum (mysql CLI / BI) = YASAK  — istisna: bakım penceresi + migration kullanıcısı + önceden onaylı tiket + audit kaydı
Anahtar/kimlik = .env (ADR-015)  —  dump/backup'ta şifre REDACTED  ·  ad/telefon/adres/ödeme = AES-256-GCM  ·  email = blind index  ·  şifre = argon2id
```

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Yalnız prepared statement + şifre kuralı** (yetki/backup/audit yok) | Sıfır altyapı; kod zaten temiz (§1.1-A) | Injection tek katmanlı; dump/backup sızıntısında PII açık; "kim sorguladı" bilinmez; ADR-020 audit bulgusu açık kalır | Araştırma + OWASP aynı şeyi söylüyor: prepared tek başına derin savunma değil (§1.3 kaynak 1-10, 38-44); bu ADR'nin doğuş sebebi tam da bu boşluk |
| 2 | **TDE / MySQL Enterprise Audit (sunucu tarafı şifreleme + plugin)** | Uygulama değişmez; şeffaf | TDE backup'ı korur ama **alan düzeyinde** sızıntıyı (tek satır dump) korumaz; Enterprise lisans maliyeti; audit plugin sunucu sürümüne bağımlı (§1.3 kaynak 39) | App-level PII şifreleme sızan satırı da kırar; open-source stack'te lisans/eklenti bağımlılığı istenmiyor → **hibrit:** app-level şifreleme + uygulama audit |
| 3 | **Deterministic encryption (AES-SIV/ECB benzeri) ile email araması** (kör indeks yok) | Tek kolon, arama basit | Eşitlik **herkesçe** doğrulanabilir → rainbow/dictionary ile sızmış dump'da email eşleştirilebilir; örüntü sızıntısı (§1.3 kaynak 11: ECB örüntü sızdırır) | Blind index + HMAC anahtarı eşitlik sızıntısını anahtar olmadan imkânsız kılar; sıralama zaten yapılmıyor (§2.2c) |
| 4 | **Yalnız backup şifreleme + en az yetki** (PII alan şifreleme yok) | Daha az kod işi; arama kısısı yok | DB'ye doğrudan/backup dışı sızıntı (replication, kopya, log) açık kalır; tek katman | Katmanlı savunma: şifreli backup + şifreli alan birbirini tutmaz; §1.3 kaynak 11-17 "alan düzeyi" şart diyor |
| 5 | **Genel sorgu log'u ile audit** (uygulama interceptor yok) | Sıfır PHP kodu | Production'da yavaş + verbose + her değeri yazar (REDACTED ihlali riski); disk basınçlı | §1.3 kaynak 40, 44 üretimde interceptor/audit plugin tercih ediyor; ADR-005 logging zaten uygulama katmanını şart koşuyor |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Derin savunma:** yetki daraltma + şifreleme + şifreli backup + audit → tek katman düşse bile etki sınırlı (dump sızsa PII okunmaz, injection olsa DDL/FILE yok, sorgu geçse iz kalır).
- **Kod zaten hazır:** PDO prepare tek kapı, argon2id, AES-256-GCM sarmalayıcı mevcut (§1.1) → kararın büyük kısmı **teyit**, yeni kod işi dar (§5.1).
- **ADR-020 ile hizalanır:** "audit PHP 0" + `getenv()` ihlali bu ADR'nin e/b maddeleriyle kapanır; API/DB audit çerçevesi tek ruhta.
- **Denetlenebilir:** CI grep kapısı, GRANT matrisi, restore drill, audit satırı doluluğu = dört ölçülebilir kapı (§2.2, §5.1).
- **2025-26 uyumu:** argon2id + AES-256-GCM + blind index + age backup literatürle birebir (§1.3 ~44 kaynak).

### 4.2 Olumsuz Sonuçlar

- **Arama kısıtı:** AES-256-GCM alanlarda DB üzerinden `WHERE`/`LIKE`/sıralama **yapılamaz** → "telefon ara" özelliği uygulamada çözüm ister (blind index yalnız email gibi tek-değerli alanlarda).
- **Argon2id maliyeti:** `m=65536,t=4` (~64 MB × 4) login başına CPU+bellek (test `UserTest.php:20` ile aynı parametre) → yüksek eşzamanlı login'de APCu önbellekli oturum (ADR-011/013) şart; DoS yüzeyi rate limit ile kapatılmış olmalı (ADR-013).
- **Migration iki kullanıcı:** DDL için ayrı hesap + bakım penceresi = deploy akışına ek adım (ADR-014 ile yazılır).
- **Şema değişikliği yükü:** `*_enc` + `*_bi` kolonları, backfill (mevcut düz email → blind index), veri yazma kodunun çift yazması → taşıma işi (§5.1 adım 3-4).
- **Debate tamamlandı:** 3 tur / 20 persona → **18/2/0 KABUL** (§5.3) + **3 bağlayıcı şart** (§5.4); Tech Lead ✅ (2026-09-25), Arch Lead ⏳ → §7'nin Arch Lead satırı ✅ olmadan Active/Frozen olmaz (şablon §4.2); **frozen YOK**.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **Anahtar kaybı = veri kaybı** (PII anahtarı unutulursa ciphertext kalıcı okunmaz) | 2 (mümkün) | 4 (yüksek) | Anahtar rotasyon prosedürü + anahtar yedeği ayrı depoda (koda/vault'a REDACTED); restore drill ile çözülebilirlik çeyreklik doğrulanır; escrow (insan onayı) |
| **kör indeks eşitlik sızıntısı** (dump + HMAC anahtarı sızarsa email eşleştirilir; sıralama/korelasyon yasağı yalnız indeks düzeyinde) | 3 (olası) | 3 (orta) | HMAC anahtarı ayrı (env), yüksek entropi; sıralama için blind index **kullanılmaz** (yalnız `=`); hassas eşitlik sorguları audit'e yazılır |
| **Şifreleme = arama kısıtı** (iş gereksinimi DB'de `LIKE` isterse kod bypass/ihtiyat yaratır) | 3 (olası) | 3 (orta) | Blind index alanı önceden tasarla (email/telefon normalize); uygulama-seviyesi arama; raw düz kolon **eklemek** yeni ADR ister |
| **Argon2id CPU/bellek maliyeti** (login patlaması + rate limit yoksa DoS) | 2 (mümkün) | 3 (orta) | ADR-013 rate limit + ADR-011 oturum önbelleği; parametreleri ölç (§5.1 adım 5) |
| **Backup anahtarı backup'la saklanırsa** (şifreleme sembolik olur) | 3 (olası) | 4 (yüksek) | §2.2d: anahtar ayrı/offline depo; restore drill'de anahtar ayrıca getirilir; depo yolu §5.1'de imzalanır |
| **Migration kullanıcısının kalıcı yetkisi** (pencere dışında DDL yetkisi açık) | 2 (mümkün) | 4 (yüksek) | Deploy sonrası `account LOCK`/`DISABLE` adımı (ADR-014 akışına ek); denetim: `SHOW GRANTS` taraması CI/ops kontrolü |
| **Audit yazıcısı unutulur** (tablo var, kod yok kalır — ADR-020 durumu tekrarlanır) | 3 (olası) | 3 (orta) | §5.1 adım 6'da "audit satırı doluluğu" testi (1 istek → ≥1 satır) CI'a bağlanır |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **CI grep kapısı:** raw `->query(` ve değer-concat SQL taraması (`shared/**`, `.claude` şablonları hariç) → 0 değilse kırmızı; ADR-002 kuralının denetlenebilir hali | QA Engineer + Backend Architect | 0.5 oturum |
| 2 | **GRANT matrisi:** 18 DB için `app`/`migration` (+opsiyonel `backup`) kullanıcıları, host kısıtlı, matris `.ai/.sql/mysql/` dump'larına + runbook'a; `SHOW GRANTS` tarama kontrolü | Data Engineer + Security Engineer | 1.5 oturum |
| 3 | **PII şema:** `*_enc VARBINARY(512)` (ad/telefon/adres/ödeme) + email normalize + `*_bi VARBINARY(32)` unique index — migration-template'den (`shared/AGENTS.md` Zorunlu 4) | Data Engineer | 1.5 oturum |
| 4 | **Şifreleme servisi:** AES-256-GCM sarmalayıcı (OAuthManager deseni ortak sınıfa) + blind index HMAC + **anahtar rotation prosedürü** (env Config — ADR-015); backfill (düz → şifreli/hashed), sonra düz kolon düşürme ayrı ADR ile | Security Engineer + Backend Architect | 2 oturum |
| 5 | **Backup şifreleme + drill:** `mysqldump --single-transaction \| age` pipeline, anahtar ayrı depo, **çeyreklik restore drill** (satır + checksum) | DevOps Engineer | 1 oturum |
| 6 | **Audit yazıcı:** repository sarmalayıcı → `audit_logs`/`log_security` (kim, sorgu şablonu, zaman, sonuç, traceId; değerler `[REDACTED]`) + "1 istek ≥ 1 satır" testi | Backend Architect + QA Engineer | 1.5 oturum |
| 7 | **Direkt SQL yasağı:** üretim runbook'una madde (yalnız uygulama; istisna = bakım penceresi + migration + tiket); DB kullanıcı/rol denetimi | DevOps Engineer + Vault Steward | 0.5 oturum |
| 8 | **Argon2id teyidi + ölçüm:** `Password.php:38` parametreleri (`m,t,p`) üretimde ölçülür; ADR-013 rate limit ile DoS yüzeyi kapatılmış mı kontrol | Security Engineer + QA Engineer | 0.5 oturum |
| 9 | **Vault senkronu:** `.ai/.decisions/index.md:59` satırı bu dosya ile canlanır; debate sonuçlandığında §5.3 + frontmatter `debate` güncellenir; `brain.md:1040`, `keys.md:63`, `index.md:639` kayıtları zaten mevcut (doğrulandı) | Vault Steward | 0.5 oturum |
| 10 | **Debate:** 3 tur / 20 persona (ADR-003/020/021 formatı) → sonuç §5.3'e + frontmatter `debate` alanına + `log.md` append | Vault Steward + adr-debate | 1 oturum |

### 5.2 Geri Dönüş Planı

1. **Karar metni (bu dosya):** karar değişirse **yeni ADR** yazılır (`ADR-088+` serisi), bu dosya `superseded by` bağlanır — metin silinmez (In-Place yasağı).
2. **CI grep kapısı (adım 1):** test/devre dışı bırakma tek commit → koruma kalkar, yasağı §2-a metni tutar; geri alma `log.md` append + neden ile kaydedilir.
3. **GRANT matrisi (adım 2):** uygulama kullanıcısının yetkisi genişletilerek (geri alınarak) operasyon kurtarılabilir → **genişletme = yeni ADR** (yetki genişlemesi güvenlik değişikliğidir); `app` kullanıcısının kilitlenmesi deploy'u durdurur → `SHOW GRANTS` + hızlı GRANT geri alma adımı runbook'ta.
4. **PII şifreleme (adım 3-4):** geri dönüş = **kademeli:** yeni yazma çift yazmaya devam eder, okuma şifreli/düz ikili mod bir süre korunur; tam geri dönüş (düz kolona) veri kaybı yaratmaz ama **eski ciphertext'ler düz kolondan silinmeden** yapılmaz → yeni ADR şart. **Unutulan anahtar geri alınamaz** (§4.3 risk 1) → önce anahtar yedeği, sonra backfill.
5. **Backup şifreleme (adım 5):** pipeline kapatılabilir (ham dump'a dönmek = koruma düşer, operasyon çalışır) → bilinçli, `log.md` kayıtlı geri alma; **anahtar kaybı geri alınamaz** → restore drill durdurulamaz (ayrı kural).
6. **Audit yazıcı (adım 6):** yazıcı kapatılabilir (yalnız iz kaybı; sorgu davranışı değişmez) → anında geri dönüş; ADR-020 bulgusu geri gelir (bilinen).
7. **Direkt SQL yasağı (adım 7):** politika metnidir, teknik kilit değil → kaldırılması = yeni ADR (denetim kaybı §4.1/4).
8. **Argon2id (adım 8):** teyit adımıdır — koda dokunulmaz; parametre değişikliği ayrı onay + yeniden hash akışı (ADR-014 ruhu).
9. **Kural ihlali / layer violation:** derhal revert + log ERROR (`AGENTS.md` §17.7); frozen ADR-001-037'e dokunulduysa `git checkout` (§17.10).
10. **Vault bozulması:** her adımdan sonra `.ai/log.md` append + `git diff --stat -- .ai/`; bozulma → `vault-utf8-writer.mjs repair` + `git checkout` (geçmiş satıra dokunulmaz).

### 5.3 Debate Kaydı

| Alan | Değer |
|------|-------|
| Durum | **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** — debate sonuçlandı, Tech Lead ✅ (2026-09-25); sonuç §5.3 + §5.4 şartlara + §7.1'e + frontmatter `debate` alanına işlendi, `.ai/log.md` append ile kaydedildi |
| Biçim | ADR-003/020/021 formatı — 3 tur / 20 persona |
| Karar içeriği | Kullanıcı onaylı kapsam: **(a) prepared statements her yerde (ADR-002 — raw query istisnasız yasak) · (b) en az yetki DB kullanıcıları (uygulama DML-only, migration ayrı) · (c) app-level AES-256-GCM PII şifreleme + email blind index + argon2id · (d) backup şifreleme (mysqldump \| age/gpg, anahtar ayrı) · (e) DB audit log (kim/sorgu/zaman — ADR-005) · (f) production direkt SQL yasağı** |
| Beklenen tartışma eksenleri | Blind index eşitlik sızıntısı sınırı · argon2id maliyeti vs DoS · anahtar rotation/escrow · migration kullanıcısı penceresi · audit performans maliyeti |
| Tur 1 (20 persona — kanıt paketi) | Raw query **0** (`DatabaseManager` prepare-only, `EMULATE_PREPARES=false`) · argon2id + pepper **IMPLEMENTED** (`Password.php:38`, `constants.php:65`) · AES-GCM yalnız OAuth token (`OAuthManager:244-291`) · PII şifreleme **0** · `CREATE USER/GRANT` **0** · backup **0** · audit **6 tablo + PHP yazıcı 0** → **15 kabul/neutral, 4 uyarı** (Critic: audit altyapısı kâğıtta — ADR-020 ile ortak bulgu) |
| Tur 2 (İtiraz → çözüm) | (1) 3 PLANNED boşluk (PII/backup/GRANT) → uygulama planı → **şart 1** · (2) Audit PHP yazıcı 0 → **tek audit interceptor** (ADR-020 ortak) → **şart 1-d** · (3) Backup anahtarı ayrı → anahtar yönetimi (ADR-015 rotation) **teyit** · (4) Blind index eşitlik sızıntısı → kararda **sınırlı kabul teyit** (yalnız `=`, §2.2c) |
| Tur 3 (Oy) | **18 kabul / 2 çekimser / 0 red → KABUL** |
| Bağlayıcı şartlar | **3 madde → §5.4** (4 PLANNED kapanışı · argon2id + pepper env testi · anahtar rotation + backup kurtarma tatbikatı) |
| Debate sonrası | Sonuç §5.3 + §5.4 şartlara + §7.1'e + frontmatter `debate` alanına işlendi; `.ai/log.md` append ile kaydedildi |
| Kural | Debate sonuçlandı; Tech Lead ✅ verildi; Arch Lead ⏳ olduğundan §7'nin üç satırı henüz tam ✅ değil → Active/Frozen olmaz (şablon §4.2) |

### 5.4 Bağlayıcı Şartlar (Debate — 3 madde, KABUL koşulu)

| # | Şart | İçerik | Sahip | Bağlantı |
|---|------|--------|-------|----------|
| 1 | **4 PLANNED kapanışı** | (a) PII AES-256-GCM (`*_enc` kolonları + email blind index) · (b) şifreli backup (`mysqldump \| age`, anahtar ayrı depo) · (c) least-privilege GRANT (`app`/`migration` matrisi + dump'lara `CREATE USER/GRANT` bloğu) · (d) **tek audit interceptor** → `audit_logs`/`log_security` (ADR-020 "audit PHP 0" ortak kapanışı) — dördü de §5.1 adımlarına bağlanır, kapanana kadar ADR **PLANNED** kalır | Security Engineer + Data Engineer + Backend Architect | §5.1 adım 2-3-5-6, §2.2b/c/d/e, [[ADR-020-api-public-security]] |
| 2 | **argon2id + pepper env testi** | `Password.php:38` (`PASSWORD_ARGON2ID`) + `constants.php:65` (`APP_PEPPER`) üretim env'inde doğrulanır: pepper boşsa kritik log **test edilir**, bcrypt grep 0 korunur, parametreler (`m=65536,t=4,p=2`) ölçülür | Security Engineer + QA Engineer | §5.1 adım 8, §1.1-B, §2.2c |
| 3 | **Anahtar rotation + backup kurtarma tatbikatı** | ADR-015 env anahtarı için rotation prosedürü (yeni anahtar → alanı yeniden şifrele → eski anahtar emekli) + **çeyreklik restore drill** (şifreli backup açılır, satır + checksum doğrulanır, anahtar ayrı depodan getirilir) | Security Engineer + DevOps Engineer | §5.1 adım 4-5, §2.2d, [[ADR-015-env-parser-strategy]] |

**İhlal süreci (Tur 2 teyidi):** şart ihlali = bu ADR'nin **amendmanı = yeni ADR + major version** (metin silinmez — §5.2/1).

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[CLAUDE.md]] | Karar alt registry kuralı (accepted/ dizin sözleşmesi) |
| [[../index]] | Karar dizini — **satır 59** `[[ADR-022-database-hardened-security]]` (slug eşleşmesi ✅) |
| [[../../CLAUDE.md]] | Vault ana sözleşmesi — 16 Hard Guardrail, REDACTED, Guardrail #16 |
| [[../../AGENTS.md]] | Routing §6 (`database, SQL, PDO → Data Engineer`), §5 domain boundary, §16 kalite (`no ORM, no SELECT *, prepared`), §25.3 frozen, §17.5/§17.7 |
| [[../../WORKFLOW.md]] | Satır 753 `§8.4 Security Audit → [[ADR-022-database-hardened-security]]` · satır 521 şifreleme kontrolü · debate/onay akışı |
| [[../../brain.md]] | Satır 1040 `ADR-022 \| AES-256-GCM` kaydı ✅ |
| [[../../keys.md]] | Satır 63 `Argon2id, AES-256-GCM, sifreleme → [[decisions/accepted/ADR-022-database-hardened-security]]` ✅ |
| [[../../index.md]] | Satır 639 `ADR-022-database-hardened-security` kaydı ✅ |
| [[../../glossary.md]] | Şifreleme/audit/argon2 terimleri |
| [[../../log.md]] | Audit trail — bu işlem tek satır append |
| Debate sonucu | §5.3 — **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** (frontmatter `debate` ile aynı) · bağlayıcı şartlar **§5.4** (3 madde) |
| Karar alt registry: [[../CLAUDE.md]] (`.ai/.decisions/CLAUDE.md`) | accepted/ dizin sözleşmesi |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı — "ADR-022'yi sıfırdan yaz"; karar içeriği onaylı: prepared yasağı · en az yetki DB kullanıcıları · PII AES-256-GCM + blind index · şifreli backup · DB audit log · production direkt SQL yasağı) | 2026-09-25 | ✅ |
| Tech Lead | Debate 3/20 — **18/2/0 KABUL + 3 şart** (§5.4) | 2026-09-25 | ✅ |
| Arch Lead | ⏳ | ⏳ | ⏳ |

### 7.1 Debate ve Onay Notu

| Alan | Değer |
|------|-------|
| Debate | **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** — sonuç §5.3'e + §5.4 şartlara + §7'ye + frontmatter `debate` alanına işlendi; `.ai/log.md` append ile kaydedildi |
| Bağlayıcı şartlar | **3 madde → §5.4:** (1) 4 PLANNED kapanışı (PII AES-GCM · şifreli backup · least-privilege GRANT · audit interceptor) · (2) argon2id + pepper env testi · (3) anahtar rotation + backup kurtarma tatbikatı |
| Tech Lead | **✅** (2026-09-25 — debate sonrası) · Arch Lead ⏳ |
| Kanıt durumu | §1.1: PDO prepare tek kapı + argon2id + AES-256-GCM sarmalayıcı + 6 audit tablosu + raw query 0 **IMPLEMENTED**; DB kullanıcı yetkisi, PII alan şifreleme, şifreli backup, audit PHP yazıcı, direkt SQL yasağı **PLANNED** (kapanış → §5.4 şart 1); `⚠️ VERIFICATION REQUIRED`: GRANT 0, backup şifreleme 0, audit PHP yazıcı 0, PII şifreli kolon 0; ADR-003 index slug düzeltildi (`ADR-003-multi-db-9-databases` → `ADR-003-multi-db-bcnf`, 2026-09-25) |
| Frozen | debate ✅ + Tech Lead ✅ + Arch Lead ⏳ → §7'nin Arch Lead satırı ✅ olmadan Active/Frozen olmaz (şablon §4.2); **frozen YOK** |
| Kural | Debate sonuçlandı; §5.3/§5.4/§7.1 ve frontmatter `debate` alanına işlendi, `.ai/log.md` append ile kaydedildi |

---

*ADR-022 v1.0.0 — CoreMusic Architecture Decision Record*
*Authority: ADR-022 Karar Metni (SSOT) · Mode: Red Team · Human Mode · Truth Mode*
*Last Updated: 2026-09-25*

*ADR-022 debate | 2026-09-25 | ✅ TAMAMLANDI (3 tur/20 persona, 18/2/0 KABUL + 3 şart §5.4) · Tech Lead ✅ · Arch Lead ⏳ · frozen YOK*
