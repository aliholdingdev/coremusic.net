---
title: "CoreMusic — ADR-002: PDO Zorunlu, ORM Yasak"
type: adr
category: database
date: 2026-09-24
updated: 2026-09-24
version: 1.0.0
status: accepted
authority: ADR-002 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — ADR-002: PDO Zorunlu, ORM Yasak

**Durum:** accepted (kabul — frozen YOK; okunur + yazılabilir)
**Tarih:** 2026-09-24
**Karar Veren:** Vault Steward (kullanıcı onaylı) · debate: ✅ TAMAMLANDI (§7.1 — 3 tur / 20 persona, 18/2/0 KABUL)
**İlgili ADR'ler:** [[ADR-001-vanilla-js-itcss]] (aynı ilke — kütüphane serbest / iskelet yasak) · karar dizini [[../index]] §3 (ADR-002 = bu dosya; ADR-003-multi-db-9-databases ve ADR-033-sql-normalization-strategy tekil dosyaları diskte YOK → `⚠️ VERIFICATION REQUIRED`)

---

## 1. Bağlam (Context)

CoreMusic backend veri katmanı (A0 Altyapı + A2 Routing; PHP 8.4, MySQL 9, 18 BCNF veritabanı) için erişim katmanı teknolojisi kararlaştırılmalıdır: her sorgu hangi arayüzle yürütülecek, ORM'e izin var mı, query builder'ın statüsü nedir ve bağlantı havuzu / timeout / retry / transaction / charset kuralları nelerdir? Karar; SQL injection (OWASP Injection ailesi), 18 BCNF şemasında sorgu öngörülebilirliği ve bağımlılık yüzeyi kontrolü olmak üzere üç bağımsız baskıyı aynı anda karşılamalıdır.

### 1.1 Mevcut Durum

- Backend hedefi: PHP 8.4 + **PDO** + PageRouter — [[../../AGENTS.md]] §4 (Backend Architect) ve §15 (teknoloji sütunu: "PHP strict_types, PDO, PageRouter").
- Kalite standardı zaten yazılı: Data Engineer satırı "**BCNF, no ORM, no SELECT \*, prepared** — %100" (§16) ve uyarı §18 #7 "**ORM kullanımı → SQL injection riski**" — [[../../AGENTS.md]].
- Disk kanıtı: `shared/src/Database/` **3 PHP dosyası** (DatabaseRegistry.php, DatabaseManager.php, Config/DatabaseConfig.php) + `.ai/.sql/mysql/` **18 .sql şeması** (coremusic_ai … coremusic_wireless).
- Routing kuralı: "database, SQL, BCNF, migration, query, schema, MySQL, PDO, index" keyword grubu → Data Engineer (birincil) + Backend Architect (ikincil) — [[../../AGENTS.md]] §6.
- Emsal karar yürürlükte: Composer paketi kütüphane olabilir, uygulama iskeleti olamaz — [[ADR-001-vanilla-js-itcss]] §2 (aynı sınır bu ADR'ye taşınır).

### 1.2 Sorun Tanımı

ORM mi, query builder mı, düz PDO mu? Sorun üç eksenli: (1) **Güvenlik** — OWASP, injection'ı en yaygın/test edilen sınıf yapar ve enjeksiyon türleri arasında **ORM injection**'ı da sayar; hazır-ORM'in `save()`/hydration yüzeyi yeni bir interpreter ekler; (2) **Kontrol** — 18 BCNF şemasında sorgu planı, join sırası ve indeks kullanımı görünür ve denetlenebilir olmalı, "magic" katman bu görüşü kapatır; (3) **Bağımlılık** — ORM, uygulama iskeleti bağımlılığıdır (geri dönüşü pahalı), kütüphane bağımlılığından farklıdır. Ayrıca query builder'ın statüsü (zorunlu mu, serbest mi) ve operasyonel kurallar (havuz, connect/query timeout, retry, transaction kapsamı, utf8mb4/collation) bu kararla yazıya bağlanmalıdır.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: prompt-maker web araştırma protokolü (`.claude/skills/prompt-maker/references/10-web-research-protocol.md`) — resmi spesifikasyon önce (OWASP, php.net, MySQL docs), her iddiaya kaynak, 2+ bağımsız çapraz doğrulama. **Odak: GÜVENLİK.**

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "OWASP Top 10 2025 A03 injection SQL injection prepared statements" · (2) "PDO ATTR_EMULATE_PREPARES false best practice 2025 2026 SQL injection PHP 8.4" · (3) "PDO prepared statements sufficient SQL injection charset utf8mb4 GBK bypass" · (4) "PHP PDO persistent connection ATTR_PERSISTENT transaction state ATTR_TIMEOUT connect timeout" · (5) "MySQL utf8mb4 utf8mb4_0900_ai_ci default collation MySQL 8 9 best practice 2025" |
| Web Search **Konusu** | PDO zorunluluğu + ORM yasağının güvenlik gerekçesi: SQL injection OWASP sınıfı ve sırası, prepared statement resmi kanıtı, zorunlu PDO atribütleri (`ATTR_EMULATE_PREPARES=false` vb.), bağlantı havuzu/timeout/retry/transaction operasyonel kuralları, utf8mb4/collation standardı. |
| Web Search **Bağlam** | 2025-2026 güncel kaynaklar okundu: OWASP Top 10:2025 (**Injection = A05:2025**; A03:2025 = Software Supply Chain Failures — 2021'deki A03:2021-Injection yerine bu kavram geçti), OWASP Top 10:2021 incelemesi, php.net resmi dokümanı (pdo.prepared-statements, pdo.prepare, ref.pdo-mysql, pdo.connections, features.persistent-connections), MySQL 8.0/8.4/9.7 charset referansları, PHP 8.4 PDO parser değişiklik notu, CVE-2025-14180 (emulated prepares, 27.12.2025), Stack Overflow kanonik PDO-UTF8/timeout başlıkları. |
| Web Search **Kısa Açıklama** | OWASP 2025'te injection'ı %100 test edilen, 37 CWE'li, >14.000 SQLi CVE'li sınıf olarak raporlar; php.net "uygulama yalnızca prepared statement kullanıyorsa SQL injection oluşmaz" der — ama PDO_MYSQL **varsayılan olarak emulated prepares** açıktır, bu yüzden `ATTR_EMULATE_PREPARES=false` + DSN `charset=utf8mb4` native ve doğru koruma için zorunludur; ORM/concat/`SET NAMES` kaçış kapıları bu korumayı deler. |
| Web Search **Uzun Açıklama** | **OWASP:** Top 10:2021'de Injection **A03:2021**'de 3. sıradaydı (%94 uygulama injection'a test edildi); Top 10:2025'te Injection **A05:2025**'e (5. sıraya) indi — "%100 of applications tested for some form of injection", kategoride **37 CWE**, "SQL Injection … more than 14k CVEs" (XSS >30k CVE). **A03:2025'in adı Software Supply Chain Failures'tır, Injection değildir** (görevdeki "A03:2025" etiketi bu doğrultuda düzeltilerek kayda geçirilir — Truth Mode). OWASP, yaygın enjeksiyon türleri arasında **ORM injection**'ı da listeler ve "parameterized olsalar bile stored procedure'ler PL/SQL/T-SQL'de concat + EXECUTE IMMEDIATE/exec() ile yine SQLi üretir" uyarısını yapar — ORM ve "SP korur" argümanlarını doğrudan zayıflatır. **Prepared statement kanıtı (php.net):** "If an application exclusively uses prepared statements, the developer can be sure that no SQL injection will occur" — ancak sorgunun başka bölümü escape'siz kuruluyorsa (concat, 2. kademe injection) açık kalır; koruma yalnız HER sorguda prepared ile gelir. **PDO atribütleri:** ref.pdo-mysql — "PDO_MYSQL uses emulated prepares by default" → `setAttribute(PDO::ATTR_EMULATE_PREPARES, false)` native sunucu tarafı prepared için zorunlu; `ATTR_ERRMODE=ERRMODE_EXCEPTION` (php.net pdo.prepare); PHP 8.4.0'dan itibaren PDO placeholder'ları driver-özel SQL parser ile bulur (string literal/comment içi `?`/`:name` artık placeholder sayılmaz) — native mod 2026'da daha güvenli. Emülasyon yüzeyi gerçek CVE üretti: **CVE-2025-14180** (disclosed 27.12.2025; `ATTR_EMULATE_PREPARES` açıkken PDO PostgreSQL'de PQescapeStringConn NULL → pdo_parse_params() null-deref crash, PHP 8.1-8.5). **Charset bypass:** DSN'e `charset=` yazılmadan (PHP <5.3.6 alışkanlığı `SET NAMES`) GBK gibi çok-bytelı charset'lerde `\xbf\x27` kaçışı prepared statement'ı bypass edebilir (kanonik SO 134099) → bağlantı charset'i DSN'de `charset=utf8mb4` sabit. **Operasyonel:** `ATTR_TIMEOUT` yalnız constructor `$options` array'inde etkilidir (sonradan `setAttribute` çalışmaz) ve **yalnız connect** süresini etkiler (MySQL'de query timeout'a dokunmaz) — query tarafı sunucu `max_execution_time` ile korunur; persistent connection (php.net) temizlik yapmaz: açık transaction/temp tablo/lock sonraki isteğe sızar (SO: "already in a transaction → chaos" kanıtı) → varsayılan kapalı, havuz kuralı `max_connections > FPM worker + cron` + `wait_timeout` izlenmeli. **Collation:** MySQL 8.0/8.4/9.x varsayılanı `utf8mb4` + `utf8mb4_0900_ai_ci`; `utf8` = utf8mb3 deprecated aliastır ("utf8mb4 whenever possible"). |
| Web Search **Paragraf Veri Uzun** | %94 uygulama injection test edildi (OWASP 2021) · %100 uygulama injection test edildi (OWASP 2025) · Injection sırası #3 A03:2021 → **#5 A05:2025** · A03:2025 = Software Supply Chain Failures (Injection DEĞİL) · 37 CWE (A05:2025) · >14.000 SQLi CVE · >30.000 XSS CVE · "exclusively prepared statements → no SQL injection" (php.net) · PDO_MYSQL emulate default = **true** → `ATTR_EMULATE_PREPARES=false` zorunlu · `ATTR_ERRMODE=ERRMODE_EXCEPTION` · `ATTR_DEFAULT_FETCH_MODE=FETCH_ASSOC` · PHP 8.4.0 driver-specific placeholder parser · CVE-2025-14180 (27.12.2025, emulate=true → NULL deref, PHP 8.1-8.5) · GBK `\xbf\x27` bypass → DSN `charset=utf8mb4` (PHP ≥5.3.6) · `ATTR_TIMEOUT=5` sn, constructor-only, connect-only · `ATTR_PERSISTENT=false` varsayılan · `MYSQL_ATTR_MULTI_STATEMENTS=false` · `max_connections > FPM worker + cron`, `wait_timeout` izleme · retry max 3 (vault AGENTS §8) · transaction: commit/finally-rollback zorunlu, persistent'te açık transaction bırakma yasak · MySQL varsayılan `utf8mb4` / `utf8mb4_0900_ai_ci` · `utf8`=utf8mb3 deprecated · ORM injection OWASP enjeksiyon türleri arasında · stored procedure concat + EXECUTE IMMEDIATE = hâlâ SQLi. |
| Web Search **Sonucu** | 1) **Sınıf doğru adlandırılmalı:** 2021'de Injection A03:2021'di; 2025 listesinde Injection **A05:2025**'tir, A03:2025 Supply Chain'dir (kaynak: owasp.org/Top10/2025 + top10.owasp.org + OWASP/Top10 GitHub). 2) **Güvenlik standardı tek:** prepared statement + `ATTR_EMULATE_PREPARES=false` + DSN `charset=utf8mb4` + `MULTI_STATEMENTS=false`; emülasyon CVE üretir (kaynak: php.net ×3 + CVE-2025-14180 + SO 134099). 3) **ORM/SP "sihir" korumaz:** OWASP enjeksiyon listesinde ORM injection ayrı tür, SP concat hâlâ SQLi (kaynak: OWASP A05:2025 GitHub md). 4) **Operasyonel davranışlar resmi dokümanla sabit:** timeout constructor-only/connect-only, persistent temizlik yapmaz, havuz worker sayısına bağlanır (kaynak: php.net connections ×2 + SO ×2). 5) **Charset kombinasyonu tek doğru:** utf8mb4 + utf8mb4_0900_ai_ci, utf8mb3 yasak (kaynak: MySQL 8.4 + 9.7 + 8.0). 6) **Çapraz doğrulama:** her iddia ≥2 bağımsız kaynakla örtüştü. |
| Web Search **Alınan Karar** | **PDO zorunlu, ORM (Eloquent/Doctrine Entities vb.) yasak**; prepared statement HER sorguda zorunlu; zorunlu bağlantı imzası: `ATTR_EMULATE_PREPARES=false` + `ATTR_ERRMODE=ERRMODE_EXCEPTION` + DSN `charset=utf8mb4` + `MYSQL_ATTR_MULTI_STATEMENTS=false` + `ATTR_TIMEOUT=5` (connect) + `ATTR_PERSISTENT=false`. **Query builder = kütüphane → serbest, ama zorunlu değildir** (ADR-001 Composer sınırı ile aynı). Operasyonel kurallar (bağlantı havuzu, connect/query timeout, retry policy, transaction kapsamı, utf8mb4/collation) §2.2 b'de bağlayıcıdır. Zaruri fallback §4.4. |
| Web Search **Sonuç** | Karar 2025-2026 verisiyle **desteklendi**: OWASP güvenlik (4 kaynak), PDO resmi doküman (5 kaynak), charset/timeout/persistent kanonik başlıklar (3 kaynak), CVE kanıtı (1 kaynak), MySQL collation (3 kaynak) — toplam **16 kaynak**, 5 sorgu, çapraz doğrulama tam. Kaynaksız iddia yok; tek düzeltme OWASP sınıf adıdır: 2025'te Injection **A05** ("A03:2025" etiketi yanlıştır — Sonucu madde 1). |

**Kaynak listesi (16):**
1. https://owasp.org/Top10/2025/ — OWASP Top 10:2025 resmi liste (A03:2025 Supply Chain, A05:2025 Injection)
2. https://top10.owasp.org/2025/A05_2025-Injection — A05 Injection 2025 resmi sayfası
3. https://github.com/OWASP/Top10/blob/master/2025/docs/en/A05_2025-Injection.md — %100 test, 37 CWE, >14k SQLi CVE, ORM/SP concat uyarıları
4. https://owasptopten.org/ — OWASP Top 10 2021 verisi (A03:2021-Injection, %94 test oranı)
5. https://www.php.net/manual/en/pdo.prepared-statements.php — "exclusively prepared → no SQL injection" + emülasyon/2. kademe uyarısı
6. https://www.php.net/manual/en/pdo.prepare.php — prepare(), ERRMODE_EXCEPTION, PHP 8.4 driver-specific parser
7. https://www.php.net/manual/en/ref.pdo-mysql.php — "PDO_MYSQL uses emulated prepares by default"
8. https://www.php.net/manual/en/pdo.connections.php — ATTR_PERSISTENT temizlik uyarısı, ATTR_TIMEOUT, ERRMODE
9. https://www.php.net/manual/en/features.persistent-connections.php — havuz/worker/wait_timeout/timeout kuralları
10. https://stackoverflow.com/questions/134099/are-pdo-prepared-statements-sufficient-to-prevent-sql-injection — GBK `\xbf\x27` bypass, DSN charset, 2. kademe injection
11. https://stackoverflow.com/questions/21403082/setting-a-connect-timeout-with-pdo — ATTR_TIMEOUT constructor-only + connect-only
12. https://stackoverflow.com/questions/5995982/should-pdoattr-persistent-be-used-every-time — persistent + açık transaction "chaos" kanıtı
13. https://security.snyk.io/vuln/SNYK-ROCKY9-PHPPECLRRD-15134040 — CVE-2025-14180 (emulated prepares, disclosed 27.12.2025)
14. https://dev.mysql.com/doc/refman/8.4/en/charset-server.html — utf8mb4 + utf8mb4_0900_ai_ci varsayılan
15. https://dev.mysql.com/doc/refman/9.7/en/charset-unicode-utf8mb4.html — utf8mb4 4-byte, utf8mb3 üstü küme
16. https://dev.mysql.com/doc/refman/8.0/en/charset.html — "use the utf8mb4 character set whenever possible", utf8 deprecated

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ORM yasağı (uçtan uca) | Eloquent, Doctrine Entities, Propel, RedBean, Medoo-ORM benzeri ActiveRecord/Entity/hydration katmanları KULLANILMAZ; ilişki metotları, dirty tracking, lazy-load sihri yasak desendir. Sınır: ORM = uygulama iskeleti sınıfı; Composer'da dursa bile yasak kalır. |
| Prepared statement zorunluluğu | Her sorgu `prepare()` + placeholder (`?`/`:name`) ile yürütülür; user input'un concat/`sprintf` ile sorguya girmesi yasak; manuel kaçış (`addslashes`, `mysql_real_escape_string`) prepared statement yerine geçmez. `SELECT *` yasak (AGENTS §16). |
| Composer / query builder sınırı | [[ADR-001-vanilla-js-itcss]] ile aynı ilke: paket **kütüphane** olabilir, uygulama **iskeleti** olamaz. Query builder = kütüphane → **serbest, zorunlu değil**; builder kullanılsa bile prepared şartı değişmez. |
| Charset / collation sabiti | Bağlantı DSN'i `charset=utf8mb4`; kolasyon `utf8mb4_0900_ai_ci` (MySQL 8+ varsayılan). `utf8`/utf8mb3, `SET NAMES` ile charset kurma ve latin1 karışımı yasak. |
| Operasyonel bütçe | §2.2 b'deki havuz/timeout/retry/transaction/collation değerleri bağlayıcıdır; değiştirilmesi bu ADR'nin revizyonunu gerektirir. |
| Vault şablon zorunluluğu | PHP erişim kodu Guardrail #16 ile [[../../.templates/backend/php-template]]'ten üretilir; şemalar `.ai/.sql/mysql/` (18 dosya) içinde Data Engineer sorumluluğundadır. |

---

## 2. Karar (Decision)

**PDO zorunludur. ORM yasaktır.** CoreMusic backend'inde veritabanına erişim yalnızca PDO üzerinden yapılır; Eloquent, Doctrine Entities ve benzeri **ORM / ActiveRecord / Entity-hydration katmanları KULLANILMAZ**. **Prepared statement her koşulda zorunludur** — user input içerse de içermezse de tüm sorgularda (`prepare` + placeholder); string concat ile sorgu kurmak yasaktır. **Composer paketleri serbesttir** (ADR-001 ile aynı sınır): paket kütüphane olabilir, uygulama iskeleti olamaz. **Query builder kütüphane sayılır → serbest, ama zorunlu değildir.** Bağlantı havuzu, connect/query timeout, retry policy, transaction kapsamı ve utf8mb4/collation kuralları §2.2 b'de bağlayıcıdır.

### 2.1 Neden Bu Seçenek?

1. **Güvenlik:** OWASP, injection'ı 2025'te %100 test edilen, 37 CWE'li ve en çok CVE'ye sahip sınıf yapar (SQL injection >14k CVE); resmi kanıt prepared statement'ın SQL injection'ı önlediğini — fakat **yalnızca her sorguda kullanıldığında** — söyler (php.net). `ATTR_EMULATE_PREPARES=false` native bağlamayı zorlar; emülasyon yüzeyi CVE üretmiştir (CVE-2025-14180). *(§1.3 kaynakları: 1-3, 5-7, 13.)*
2. **Kontrol / öngörülebilirlik:** 18 BCNF şemasında sorgu planı, join sırası ve indeks kullanımı göründür; ORM'in otomatik join/lazy-load'u tam da bu denetimi kapatır. Kod incelemesinde her sorgu okunabilir ve denetlenebilirdir.
3. **Bağımlılık yüzeyi:** ORM = uygulama iskeleti bağımlılığı (mapping, service provider, migration köprüsü — geri dönüşü pahalı); query builder = kütüphane (çıkarılabilir). Bu ayrım ADR-001'in "kütüphane serbest / iskelet yasak" sınırıyla birebir aynıdır.
4. **Tutarlılık (vault):** [[../../AGENTS.md]] §16 zaten "%100 BCNF, no ORM, prepared" şart koşuyor, §18 #7 ORM'i açıkça risk olarak işaretliyor; bu ADR bu standardı tek kaynak, denetlenebilir karar cümlesine dönüştürür ve operasyonel kuralları ilk kez yazar.

### 2.2 Teknik Detaylar

**(a) Zorunlu bağlantı imzası (her PDO örneği — php-template iskeleti):**

```php
$pdo = new PDO(
    'mysql:host=' . $host . ';dbname=' . $db . ';charset=utf8mb4', // charset DSN'de — SET NAMES yasak
    $user,
    $pass, // REDACTED: değerler .env'de tutulur, koda/vault'a yazılmaz
    [
        PDO::ATTR_EMULATE_PREPARES      => false, // native prepared (PDO_MYSQL default: true)
        PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE    => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT               => 5,     // connect timeout (sn) — constructor-only
        PDO::ATTR_PERSISTENT            => false, // varsayılan: request başına bağlantı
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => false, // çoklu ifade (stack eden SQLi) kapalı
    ]
);
```

**(b) Operasyonel Kurallar (bağlayıcı):**

| Kural | Değer | Gerekçe / Kaynak (§1.3) |
|-------|-------|--------------------------|
| **Bağlantı havuzu** | Varsayılan `ATTR_PERSISTENT=false`; request başına tek PDO, `DatabaseRegistry` üzerinden. Persistent yalnız ölçüm + yazılı gerekçeyle açılır; havuz bütçesi `max_connections > FPM worker + cron`, `wait_timeout` izlenir; persistent bağlantıda temp tablo/lock/açık transaction bırakmak yasak | php.net persistent connections + SO "chaos" kanıtı (#9, #12) |
| **Connect timeout** | `ATTR_TIMEOUT=5` sn — **yalnız constructor `$options` array'inde** (sonradan `setAttribute` etkisiz); 5 sn'de kurulamazsa istek 503 + log + §2.2 b retry'si devrede | php.net connections + SO 21403082 (#8, #11) |
| **Query timeout** | `ATTR_TIMEOUT` MySQL'de query'yi **etkilemez** → sunucu tarafı `max_execution_time` optimizer hint'i + uygulama sayfa bütçesi; uzun rapor sorguları iş kuyruğuna | SO 21403082 notu: "only affects connection time" (#11) |
| **Retry policy** | Yalnız **bağlantı kurulumunda** yeniden denenir: max 3, exponential backoff (100 ms ×2ⁿ). **Sorgu/transaction hatasında otomatik retry YASAK** (idempotent değilse çift yazar): hata → rollback + log + eskalasyon | AGENTS §8 (retry 3) + php.net "retry the connection after some timeout" (#8) |
| **Transaction kapsamı** | Tek business operation = tek transaction: `beginTransaction` → commit / hatada `rollBack` (finally). Nested → `SAVEPOINT`. Uzun DDL/rapor transaction'ı yasak; persistent bağlantıda açık transaction bırakmak yasak | php.net PDO API + SO persistent-transaction kanıtı (#9, #12) |
| **Charset / collation** | DSN `charset=utf8mb4`; kolasyon `utf8mb4_0900_ai_ci` (MySQL 8/8.4/9 varsayılan); `utf8` (=utf8mb3) deprecated, şemalarda kullanılmaz | MySQL 8.4/9.7/8.0 docs + GBK bypass (#10, #14-16) |

**(c) Yasak desenler (code review / CI kapıları):** user input'u sorguya concat etmek (`"... WHERE id=$id"`, `sprintf` ile); `SELECT *`; `ATTR_EMULATE_PREPARES=true` veya unset bırakmak; `SET NAMES` ile charset kurmak; `addslashes`/`mysql_real_escape_string` ile "kaçış"; `MYSQL_ATTR_MULTI_STATEMENTS=true`; ORM hydration; transaction'ı commit'siz/rollback'siz bırakmak.

**(d) Query builder statüsü:** Doctrine DBAL Query Builder, Illuminate (Eloquent'siz), Mezzio/Laminas DbSql vb. **kütüphane** sınıfindadır → **serbesttir, zorunlu değildir**. Kullanılırsa: builder'ın parametre bağlaması yine placeholder ile olur; `whereRaw`/`orderByRaw` gibi raw-ifade kaçış kapıları §2.2 c yasağına tabidir (concat edilmiş raw = ihlal).

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **ORM — Eloquent / Doctrine Entities** (ActiveRecord + hydration) | Hızlı CRUD, hazır ilişki/lazy-load, ekip alışkanlığı | OWASP enjeksiyon listesinde **ORM injection** ayrı türdür; 18 BCNF'de otomatik join ve seçim planı kapatır; mapping + service provider = iskelet bağımlılığı (geri dönüşü pahalı); sorgu görememe → denetim açığı | Uygulama iskeleti yasağı (ADR-001 ilkesi) + OWASP kanıtı: sihir katmanı concat riskini ortadan kaldırmaz, yalnız gizler (#3); vault §16 zaten "no ORM" |
| 2 | **Düz mysqli (PDO'suz)** | MySQL-native API, `bind_param` tip kodlaması | İki erişim yolu = standartsızlık; PDO'nun tek exception modeli ve sürücü soyutlaması kaybolur; denetim yüzeyi ikiye katlanır | Karar zaten "PDO zorunlu"; ikinci bir erişim katmanı §5.1 statik kapılarını ve php-template iskeletini parçalar (vault stack'i: AGENTS §4/§15) |
| 3 | **Manuel kaçış + concat** (`addslashes` / `mysql_real_escape_string`) | Kod kısa, hazırlık gerektirmez | Kaçış charset'e bağlıdır: DSN charset yoksa GBK `\xbf\x27` bypass (kanonik vaka); 2. kademe injection kaçışı deler; koruma yalnız HER sorguda prepared ile gelir | Güvenlik kanıtıyla (#5, #10) doğrudan çelişir; encoding/entity hataları tek başına kaçışı kırar — OWASP "pozitif doğrulama + parametrize" rehberine aykırı |
| 4 | **Query builder'ı zorunlu kılmak** (tüm sorgular builder ile) | Tek sorgu API'si, tutarlı çıktı, parametrik yapı yerleşik | Ek öğrenme/soyutlama katmanı; raw-ifade kapıları (`whereRaw`) yine concat riski taşır; "zorunlu" etiketi ADR-001'in kütüphane/iskelet ayrımını bulandırır | Koruma katmanı **prepared statement**'tir, builder değil; zorunlu kılınsa kendi kaçış kapılarıyla ek güvenlik + stil maliyeti getirir — karar: serbest bırakıldı (§2.2 d) |
| 5 | **İş mantığını stored procedure'a taşımak** | Merkezi iş mantığı, DB içinde modüler | OWASP: parameterized olsa bile PL/SQL/T-SQL concat + `EXECUTE IMMEDIATE`/`exec()` **yine SQLi üretir**; 9+ veritabanında SP yayınlama/karmaşıklık; kod-şema sürüm bağımlılığı | OWASP A05:2025 bu yöntemi açıkça "hâlâ SQLi üretir" diye sayar (#3); iş mantığı PHP'de kalır, DB yalnız veri + constraint |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **SQL injection saldırı yüzeyi asgariye iner:** prepared + `ATTR_EMULATE_PREPARES=false` + DSN `charset=utf8mb4` + `MYSQL_ATTR_MULTI_STATEMENTS=false` dörtlüsü OWASP A05:2025'e karşı katmanlı kanıt üretir; emülasyon CVE'leri (CVE-2025-14180 sınıfı) ve charset-bypass ailesi kapanır. *(vault: [[../../AGENTS.md]] §16/§18 #7; `.ai/.sql/mysql/` 18 şemanın charset doğrulaması §5.1 #5)*
- **18 BCNF şeması korunur:** sorgu planı, join ve indeks kullanımı kodda görünür → Data Engineer denetimi ve query tuning doğrudan yapılır; tek erişim kapısı `shared/src/Database/` (3 dosya).
- **Bağımlılık yüzeyi dar kalır:** ORM iskeleti yok → Composer yüzeyi yalnız kütüphane ölçeğinde (ADR-001 ile aynı sınır); tedarik zinciri ve sürüm-yükseltme baskısı azalır.
- **Denetlenebilirlik:** her sorgu code review'da okunur; `SELECT *`, concat, açık transaction, `SET NAMES` gibi yasaklar statik kapıda (grep + PHPStan) otomatik yakalanır.
- **Taşınabilirlik / standart:** PDO, PHP ekosisteminin de facto erişim standardıdır; çok-veritabanlı hedef (18 BCNF DB) için tek arayüz ve tek hata modeli korunur.

### 4.2 Olumsuz Sonuçlar

- **ORM konforu yok:** migration yardımcıları, hydration, ilişki metotları, eager-loading elde yazılır → ilk başta geliştirme hızı düşer.
- **N+1 riski elle yönetilir:** lazy-load olmadığı için sorgu sayısı bilinçli kontrole bağlıdır (repository + batch/JOIN disiplini şart).
- **CRUD tekrarı:** benzer sorgu blokları çoğalır; php-template + hafif repository yardımcıları yoksa kod şişer.
- **Ekip alışkanlığı kırılır:** ORM geçmişine sahip geliştiriciler için uyum/öğrenme maliyeti oluşur.
- **"Query builder zorunlu değil" belirsizliği** pratikte stil dağılması yaratabilir → §2.2 d sınırı ve review disiplini olmadan tutarsızlık riski.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| Concat ile tek satır input sızıntısı (ihlal) | 3 (olası) | 4 (yüksek) | Yasak desen taraması (CI grep + PHPStan kuralı, §5.1 #4), code review, SQLi pen-test turu |
| Persistent/temporary state sızıntısı (açık transaction, temp tablo, lock) | 2 (mümkün) | 4 (yüksek) | Varsayılan `ATTR_PERSISTENT=false` (§2.2 b), `finally { rollBack }` zorunluluğu, registry denetimi |
| Retry ile çift yazma (timeout sonrası) | 2 (mümkün) | 4 (yüksek) | Retry yalnız bağlantı aşamasında max 3 (§2.2 b); sorgu retry'ı yasak; idempotency anahtarı |
| Timeout değersizliği (connect timeout'u query timeout sanmak) | 3 (olası) | 3 (orta) | §2.2 b ayrımı + sunucu `max_execution_time` + sayfa bütçesi + monitoring alarmı |
| Charset karışımı (utf8mb3 / SET NAMES / mojibake) | 2 (mümkün) | 3 (orta) | DSN `charset=utf8mb4` zorunlu, `utf8mb4_0900_ai_ci` sabiti, 18 şema doğrulaması (§5.1 #5) |
| ORM talebi / geçiş baskısı (ekip konfor arar) | 3 (olası) | 3 (orta) | Karar bu ADR ile sabit; değişiklik yalnız §4.4 koşulları + YENİ ADR ile |

### 4.4 Zaruri Fallback — İstisna Kaposu

Bu madde kararın **tek istisna kapısıdır**; koşulları sağlanmadan hiçbir agent ORM önerip uygulayamaz.

| # | Koşul (hepsi sağlanmalı) | Onay |
|---|--------------------------|------|
| 1 | Query builder dâhil tüm **kütüphane** seçenekleri denenmiş ve gereksinim karşılanamamıştır (ör. 3. parti paket ORM'siz çalışmayan) — kanıt: teknik değerlendirme notu | Backend Architect + Data Engineer |
| 2 | **Yeni ADR** yazılır (yeni numara; bu dosya asla düzenlenmez — `superseded by` bağı yeni ADR'nin §6'sına konur) | Vault Steward → Tech Lead → Arch Lead |
| 3 | Kapsam dar tutulur: tek izole bileşen/adayı — CoreMusic çekirdek auth/catalog/sosyal şeması DEĞİL | Arch Lead |
| 4 | ORM dâhil tüm sorgular prepared statement ile bağlanır (**prepared kuralı istisnasız kalır**); SQL injection pen-test + statik kapı zorunlu | Security Engineer + QA Engineer |
| 5 | Composer yüzeyi audit edilir (lockfile + `composer audit`), sürüm pin + upgrade planı yazılı | Security Engineer |

*Geçici (acil teslimat) istisna en fazla 5 iş günü sürer; süre sonunda kod geri alınır (`git revert`) + `log.md`'ye ERROR satırı eklenir. Geçici çözüm hiçbir koşulda ORM kurulumu olamaz — yalnızca elde yazılmış prepared sorgu veya serbest query builder kullanımıdır.*

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | Bu ADR'yi `.ai/.decisions/accepted/` altına yaz (slug `ADR-002-pdo-mandatory-no-orm`) + `log.md` append + dizin satırı doğrula (`[[ADR-002-pdo-mandatory-no-orm]]` — [[../index]] §3) | Vault Steward | 15 dk |
| 2 | `shared/src/Database/` (3 dosya) §2.2 a imzasıyla denetle: `EMULATE_PREPARES=false`, `ERRMODE_EXCEPTION`, `charset=utf8mb4`, `MULTI_STATEMENTS=false`, `ATTR_TIMEOUT=5`, `PERSISTENT=false` — eksik varsa uygula | Backend Architect + Data Engineer | 1 gün |
| 3 | [[../../.templates/backend/php-template]] Guardrail #16 denetimi: hazır bağlantı imzası + yasak desenler (§2.2 c) + operasyonel kurallar (§2.2 b) şablona işlendi mi? | Vault Steward + Backend Architect | 2 saat |
| 4 | Statik kapı (CI): (a) `SELECT *`, (b) input concat desenleri, (c) `EMULATE_PREPARES` unset/true, (d) `SET NAMES` taraması + PHPStan kuralı → hedefi hepsi 0 | QA Engineer + DevOps Engineer | 1 gün |
| 5 | `.ai/.sql/mysql/` **18 şemada** charset/collation doğrulaması: `utf8mb4` + `utf8mb4_0900_ai_ci`, utf8mb3 kolon/şema yok → uyuşmazlık varsa kapat | Data Engineer | 1 gün |
| 6 | Debate şart 1 — **N+1 / query budget + `EXPLAIN`:** her endpoint/liste sorgusu için query budget yazılır; elle yazılmış sorgularda JOIN/batch disiplini zorunlu, kritik sorgular review öncesi `EXPLAIN` ile doğrulanır (indeks kullanımı görünür olmalı) | Data Engineer + Backend Architect | sürekli (CI review) |
| 7 | Debate şart 3 — **ADR-081 outbox uyumu:** outbox satırı MySQL transaction'ında, PDO prepared statement ile yazılır ([[ADR-081-multi-provider-data-sync]]) | Backend Architect + Data Engineer | 1 gün |

### 5.2 Geri Dönüş Planı

Karar veri erişim mimarisini kilitler; geri dönüş yalnız **yeni ADR** ile olur (In-Place Refactoring yasağı — bu dosya frozen olmasa da keyfi düzenlenmez). Senaryolar: (1) §4.4 koşulları sağlanıp ORM/iskelet geçişi gerekirse → **yeni ADR** yazılır; `superseded by ADR-NNN` bağı **yeni ADR'nin** §6'sına konur, bu dosya olduğu gibi kalır; (2) operasyonel bir değerde (timeout/retry/havuz) hata çıkarsa → bu ADR **revize edilmez**: değer düzeltmesi `log.md` append + php-template senkronuyla yapılır, karar cümlesi değişiyorsa yeni ADR açılır; (3) acil durum geçici istisnası 5 iş günü sonunda otomatik kapanır: `git revert` + `log.md` ERROR satırı; (4) **veri kaybı riski yoktur** — karar katmanıdır, şema/veri değişmez; vault bozulmasında standart kurtarma `git checkout` + son commit ([[../../AGENTS.md]] §17 #10).

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[ADR-001-vanilla-js-itcss]] | Aynı ilke: kütüphane serbest / iskelet yasak — Composer sınırının frontend'deki ikizi (§2, §1.4) |
| [[ADR-081-multi-provider-data-sync]] | Debate şart 3 — uyum: outbox satırı MySQL transaction'ında, PDO prepared statement ile yazılır (§5.1 #7) |
| [[../index]] | Karar dizini — bu ADR'nin kaydı (`[[ADR-002-pdo-mandatory-no-orm]]`) + ilgili satırlar: ADR-003-multi-db-9-databases, ADR-033-sql-normalization-strategy (tekil dosyalar diskte YOK → `⚠️ VERIFICATION REQUIRED`) |
| [[../CLAUDE.md]] | Karar alt registry kuralı (accepted/) |
| [[../../CLAUDE.md]] | Ana sözleşme, 16 Hard Guardrail (Guardrail #16) |
| [[../../AGENTS.md]] | Backend/Data domain (§4, §5, §15), keyword routing (§6), kalite standardı "no ORM, prepared" (§16), ORM uyarısı (§18 #7), retry/timeout (§8, §17) |
| [[../../brain]] | Mimari karar özeti (ADR-002 satırı) |
| [[../../keys]] | Keyword haritası — "PDO, ORM, prepared, SQL injection, database" eşlemeleri |
| [[../../.templates/adr/adr-template]] | Bu ADR'nin 7 bölümlük şablonu (Guardrail #16) |
| [[../../.templates/backend/php-template]] | PHP kod iskeleti — bağlantı imzası + yasak desenler uygulayıcısı |
| `shared/src/Database/` | Uygulama kodu — DatabaseRegistry.php · DatabaseManager.php · Config/DatabaseConfig.php (§5.1 #2 denetim hedefi) |
| `.ai/.sql/mysql/` | 18 BCNF şeması — charset/collation doğrulaması (§5.1 #5) |
| [[../../log]] | Audit trail (bu kaydın append satırı) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı karar) | 2026-09-24 | ✅ |
| Tech Lead | Debate 3/20 onayı (18/2/0 KABUL) | 2026-09-24 | ✅ |
| Arch Lead | ⏳ | — | ⏳ |

### 7.1 Tartışma Kaydı (Debate — kaynak alanı)

| Alan | Değer |
|------|-------|
| Biçim | 3 tur / 20 persona (ajan debate) — Tur 1: 20 görüşme (14 kabul/neutral · 3 uyarı · 3 nötr) · Tur 2: 3 itiraz → çözüm · Tur 3: oylama |
| Sonuç | **18 kabul / 2 çekimser / 0 red → KABUL** (2026-09-24) |
| Çekimser gerekçeleri | 2 çekimser oy — gerekçe metni raporlanmadı ⚠️ VERIFICATION REQUIRED. Tur 1 uyarıları: QA (repository mock maliyeti), DevOps (migration elle yapılması), PM (geliştirme hızı); 3 nötr oy: altyapı agentları |
| Karara dönüşen şartlar | (1) query budget + `EXPLAIN` zorunluluğu (N+1 riski — §5.1 #6) · (2) repository şablonu zorunlu (DAO/şablon tutarsızlığı) · (3) ADR-081 multi-provider outbox ile uyum maddesi (§5.1 #7) |
| Çözüm | Tur 2 itirazları: #1 N+1 → şart 1 (query budget + EXPLAIN) eklendi; #2 tutarsızlık → şart 2 (repository şablonu zorunlu); #3 timeout davranışı → bağlantı imzasında mevcut (§2.2 a/b) teyit edildi. Tur 3: 18/2/0 KABUL → Tech Lead ✅ (2026-09-24) |

---

*ADR-002 v1.0.0 | 2026-09-24 | Created — CoreMusic Vault (.decisions/ yeni seri)*
*Authority: ADR-002 Karar Metni · Mode: Red Team · Human Mode · Truth Mode*
