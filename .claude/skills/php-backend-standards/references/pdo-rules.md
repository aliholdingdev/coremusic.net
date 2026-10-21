---
title: "php-backend-standards — PDO Kuralları"
type: reference
category: skills
version: 4.0.0
status: active
date: 2026-10-21
updated: 2026-10-21
---

# PDO Kuralları (ADR-002) — Derin Referans

> Kaynak: `.ai/.decisions/accepted/ADR-002-pdo-mandatory-no-orm.md` (diskte, 251 satır)
> + `references/sources.md` #5, #8, #9, #10. Erişim: 2026-10-21.

## §1 Karar Özeti (tek cümle)

**PDO zorunlu, ORM yasak; her sorgu `prepare()` + placeholder ile yürür;
bağlantı imzası sabittir; query builder kütüphanedir → serbest ama zorunlu değildir.**

| Kural | Değer | Kaynak |
|-------|-------|--------|
| ORM (Eloquent/Doctrine Entity/Propel/RedBean/Medoo-ORM) | **YASAK** — uygulama iskeleti sınıfı sayılır | ADR-002 §1.4 |
| `ATTR_EMULATE_PREPARES` | **`false`** (PDO_MYSQL varsayılanı **true**'dur) | php.net/ref.pdo-mysql |
| `ATTR_ERRMODE` | `ERRMODE_EXCEPTION` | php.net/pdo.prepare |
| `ATTR_DEFAULT_FETCH_MODE` | `FETCH_ASSOC` | ADR-002 §2.2 |
| DSN charset | **`charset=utf8mb4`** (DSN içinde, `SET NAMES` ile DEĞİL) | ADR-002 §1.3 #5 |
| `MYSQL_ATTR_MULTI_STATEMENTS` | `false` | ADR-002 §2.2 |
| `ATTR_TIMEOUT` | `5` sn — **yalnız constructor `$options` array'inde** | ADR-002 §2.2 b |
| `ATTR_PERSISTENT` | `false` (varsayılan; yalnız ölçüm + yazılı gerekçeyle açılır) | ADR-002 §2.2 b |
| `SELECT *` | **YASAK** — açık sütun listesi | `.ai/CLAUDE.md` §21 · php-template Guardrail #3 |
| Manuel kaçış (`addslashes`, `mysql_real_escape_string`) | prepared statement yerine geçmez | ADR-002 §1.4 |

## §2 Zorunlu Bağlantı İmzası

```php
<?php

declare(strict_types=1);

$dsn = 'mysql:host=127.0.0.1;dbname=coremusic;charset=utf8mb4';

$pdo = new PDO(
    $dsn,
    $user,                       // .env / credential vault — KODDA SABİT DEĞİL
    $pass,
    [
        PDO::ATTR_EMULATE_PREPARES       => false,  // native prepared — kritik
        PDO::ATTR_ERRMODE                => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE     => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
        PDO::ATTR_TIMEOUT                => 5,      // connect-only
        PDO::ATTR_PERSISTENT             => false,
    ]
);
```

**Neden `EMULATE_PREPARES=false`?**
PDO_MYSQL emulated prepares **varsayılan olarak açıktır** (php.net/ref.pdo-mysql):
PDO `prepare()` çağrısında sunucuya bir şey göndermez, sorguyu istemci tarafında
escape edip yeniden yazar. İki sonucu vardır:

1. `$pdo->prepare('garbage')` **hata vermez** — hata ancak `execute()`'da çıkar (geç teşhis).
2. İstemci/sunucu charset uyuşmazlığında (kanonik GBK `\xbf\x27` vakası) kaçış
   **bypass edilebilir**; `ATTR_EMULATE_PREPARES=false` bunu kapatır çünkü yapı
   kullanıcı verisiyle **hiçbir zaman** istemcide birleştirilmez.

Ayrıca emülasyon yüzeyi gerçek CVE üretti (ADR-002 §1.3: `CVE-2025-14180`,
27.12.2025, emülasyon açıkken PDO PostgreSQL NULL-deref, PHP 8.1–8.5).

> **Doğru nüans (korkulacak değil, bilinmesi gereken):** emülasyon
> *modern sürücüde* tek başına pratik bir SQLi kapısı değildir; kapatılması
> **savunma derinliği + erken hata tespiti** içindir. ADR-002 bunu "zorunlu"
> yapıyor, çünkü maliyeti sıfır.

## §3 Prepared Statement'ın Kapsamı — Sınır Nerede?

php.net/pdo.prepare kuralı: **"Parameter markers can represent a complete data
literal only."** — bağlanabilir olan tek şey tam bir veri literali

| Bağlanabilir | Bağlanamaz (YASAK — concat gerekir → tasarımı değiştir) |
|---|---|
| `WHERE id = :id` | Tablo adı: `SELECT * FROM :table` |
| `WHERE email = :email` | Sütun adı: `ORDER BY :col` |
| `LIMIT :limit OFFSET :offset` | Anahtar kelime: `WHERE status = :x ORDER BY :dir` |
| `VALUES (:a, :b, :c)` | `IN()` içindeki **çoklu** değer → tek parametre DEĞİL |

Ek kural (php.net/pdo.prepare): `:name` ve `?` **aynı şablonda karıştırılamaz**.
Aynı `:name` işaretcisi emülasyon **kapalıyken** birden fazla kez kullanılamaz.

### §3.1 `IN()` deseni (en sık yazılan hata)

```php
// ❌ YANLIŞ — bağlanamayan yapı: ya concat ya da tek parametre hatası
$sql = 'SELECT id, title FROM tracks WHERE id IN (' . implode(',', $ids) . ')';

// ✅ DOĞRU — yer tutucuyu sorguya değil, sayıyaya göre üret; DEĞERLERİ yine bağla
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$sql = "SELECT id, title FROM tracks WHERE id IN ({$placeholders})";
$stmt = $pdo->prepare($sql);
$stmt->execute(array_values($ids));   // $ids = kullanıcı girdisi → yine placeholder
```

`$ids` hiç validate edilmeden geliyorsa önce **eleman başına tip doğrulama**
(`is_int` / `ctype_digit`) yapın — placeholder `IN` bağlamında yapıyı kurtarmaz,
yalnız değerleri kurtarır.

### §3.2 Dinamik `ORDER BY` / filtre (yine kaçış kapısı)

```php
// ❌ YANLIŞ — "ben prepared kullanıyorum" ama kolon adı concat
$dir = $_GET['dir'];                       // "id; DROP TABLE" gelebilir
$sql = "SELECT id, title FROM tracks ORDER BY title {$dir}";

// ✅ DOĞRU — beyaz liste (whitelist) + sabit sorgu
$allowed = ['ASC' => 'ASC', 'DESC' => 'DESC'];
$dir = $allowed[$_GET['dir'] ?? 'ASC'] ?? 'ASC';
$stmt = $pdo->prepare(
    "SELECT id, title FROM tracks ORDER BY title {$dir} LIMIT :l OFFSET :o"
);
```

Kolon adı için whitelist; değer için placeholder. İkisi ayrı katmandır.

## §4 Doğru / Yanlış Çiftleri

| # | ❌ Yanlış | ✅ Doğru | Neden |
|---|----------|---------|-------|
| 1 | `$pdo->query("SELECT * FROM users WHERE id=$id")` | `$pdo->prepare('SELECT id, name FROM users WHERE id = :id')` | concat + `SELECT *` (2 ihlal) |
| 2 | `$stmt->execute([':q' => "%{$q}%"])` + `LIKE` concat ile kolon | `$stmt->execute([':q' => '%' . addcslashes($q, '%_') . '%'])` | LIKE wildcard kaçışı **değer düzeyinde** yapılır, sorgu yapısı değil |
| 3 | `catch (PDOException $e) { echo $e->getMessage(); }` | log'a tam hata, istemciye `{code: "SERVER_ERROR"}` | MySQL hatasının ham gösterilmesi **CWE-209** (MySQL 2.7 §"Handle MySQL Error Messages Properly") |
| 4 | `$pdo->beginTransaction(); ... ; // commit yok` | `try { begin; ...; commit; } catch { rollBack(); } finally { açık transaction kontrolü }` | persistent bağlantıda açık transaction bırakmak yasak (ADR-002 §2.2 b) |
| 5 | Sorgu hatasında otomatik retry | Retry **yalnız bağlantı kurulumunda**, max 3, backoff 100ms × 2ⁿ | Sorgu retry'ı idempotent değilse **çift yazar** |
| 6 | `SET NAMES utf8mb4` | DSN `charset=utf8mb4` | `SET NAMES` bağlantı başına senkronizasyon riski; charset DSN'de bağlayıcıdır |
| 7 | `foreach ($tracks as $t) { $pdo->prepare('SELECT ... WHERE artist_id = ?') }` | Tek JOIN sorgusu ya da batch (`WHERE artist_id IN (...)`) | **N+1** — sorgu bütçesi ihlali (ADR-002 §5.1 #6) |
| 8 | `LIMIT :l` bağlanamıyor diye concat | `execute([':l' => (int)$limit])` + sunucu tarafında tip zaten int | php.net: işaretciler tam literali temsil eder; int cast **uygulama katmanında** yapılır |

## §5 Transaction, Retry, Havuz — Operasyonel Kurallar (ADR-002 §2.2 b)

```
Tek business operation = tek transaction
  beginTransaction()
    → repo.writeA()
    → repo.writeB()        // aynı PDO, aynı transaction
    → commit()
  catch → rollBack()  (finally'de de kontrol — açık transaction sızıntısı yasak)
  nested → SAVEPOINT
```

| Konu | Kural | İhlal Sonucu |
|------|-------|--------------|
| Havuz | `ATTR_PERSISTENT=false`; `max_connections > FPM worker + cron` | Bağlantı yoksunluğu (503) |
| Connect timeout | `ATTR_TIMEOUT=5` — **constructor `$options`** (sonradan `setAttribute` etkisiz) | Sonsuz bekleme |
| Query timeout | `ATTR_TIMEOUT` MySQL'de query'yi **etkilemez** → sunucu `max_execution_time` + uygulama bütçesi | Uzun sorgu FPM worker'ını bloklar |
| Retry | Yalnız bağlantı aşamasında, max 3 | Çift yazma |
| Charset | `utf8mb4` + `utf8mb4_0900_ai_ci`; `utf8`/`utf8mb3` **deprecated** | Mojibake + kaçış bypass |
| Uzun sorgu | Rapor/analiz sorguları iş kuyruğuna alınır, request içinde değil | Timeout zinciri |

## §6 OWASP A05:2025 Bağlantısı

A05:2025 Injection — %100 uygulama test edildi, **37 CWE**, SQLi **14k+ CVE**
(sources.md #5). Bu skill'in PDO kuralları doğrudan şu CWE'leri hedefler:

| CWE | Ad | Karşılığı |
|-----|----|-----------|
| CWE-89 | SQL Injection | prepared + placeholder + `EMULATE_PREPARES=false` |
| CWE-20 / CWE-116 | Input Validation / Output Encoding | DTO validation + çıktıda encoding |
| CWE-209 *(MySQL notu)* | Information Exposure Through Sent Error | hata ham gösterilmez |
| CWE-943 | Data Query Logic | dinamik filtre whitelist'i |

> OWASP ayrıca **ORM injection**'ı ayrı bir tür olarak listeler ve
> "parameterized olsa bile stored procedure concat + `EXECUTE IMMEDIATE` hâlâ SQLi üretir"
> der → "ORM/SP korur" argümanı geçersizdir.

## §7 Sayfalama Sözleşmesi (liste endpoint'leri)

`.ai/.templates/documentation/api-doc-template.md` §4: `meta` alanı **zorunlu**.

```php
// ✅ — meta: total / page / per_page (sayfalama kırılmaz)
$page    = max(1, (int)($input['page'] ?? 1));
$perPage = min(100, max(1, (int)($input['per_page'] ?? 20)));   // üst sınır clamp
$offset  = ($page - 1) * $perPage;

$total = (int)$repo->countAll($filters);   // ayrı count sorgusu — açık sütun listesi

$stmt = $pdo->prepare(
    'SELECT id, title, artist_id, created_at
       FROM tracks
      WHERE artist_id = :artist_id
      ORDER BY created_at DESC
      LIMIT :limit OFFSET :offset'
);
$stmt->bindValue(':artist_id', $artistId, PDO::PARAM_INT);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$items = $stmt->fetchAll();   // zaten FETCH_ASSOC + açık sütun listesi
```

**Sorgu bütçesi (query budget):** liste endpoint'i ≤ 2 sorgu (count + sayfa).
3. sorguya ihtiyaç varsa JOIN ile çözülür — döngü içinde sorgu yasak.

## §8 Statik Kapı (CI'da 0 hedefi)

| # | Tarama | Hedef |
|---|--------|-------|
| 1 | `SELECT *` regex | 0 |
| 2 | `query(` / `exec(` içine değişken concat deseni | 0 |
| 3 | `ATTR_EMULATE_PREPARES` unset veya `true` | 0 |
| 4 | `SET NAMES` | 0 |
| 5 | `addslashes` / `mysql_real_escape_string` (SQL bağlamında) | 0 |
| 6 | `unserialize(` üzerine kullanıcı girdisi | 0 |
| 7 | ORM import (Eloquent/Doctrine/Propel/RedBean) | 0 |
| 8 | Döngü içi `->query(` (N+1) | review bayrağı |

Kaynak: ADR-002 §5.1 #4 (QA Engineer + DevOps Engineer).

## §9 Bilinen Sınır Durumları

| Durum | Davranış |
|-------|----------|
| `execute()` `false` dönerse | `ERRMODE_EXCEPTION` nedeniyle istisna fırlatır — return tipi `PDOStatement\|false`'u pratikte `PDOStatement` kılar; yine de `prepare()`'ın `false` dönebileceği (sunucu reddi) akılda tutulur |
| PHP 8.4+ placeholder parser | 8.4.0'dan itibariyle string literal/comment içi `?`/`:name` **placeholder sayılmaz** → native modda daha doğru eşleşme (kaynak: ADR-002 §1.3) |
| PHP 8.4 PDO driver alt sınıfları | `Pdo\Mysql`, `Pdo\Pgsql`, `Pdo\Sqlite` … + `PDO::connect()` (sources.md #7) — bu skill'de kullanım **⚠️ VERIFICATION REQUIRED**: repo kodunda hangisinin kullanıldığı diskten doğrulanmalı |
| 18 şemanın charset doğrulaması | Data Engineer sorumluluğu (ADR-002 §5.1 #5) — bu skill kapsamında değil |
