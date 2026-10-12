# PDO & BCNF Kuralları

> Kaynak: `.ai/CLAUDE.md` §18/§21/§23 + ADR-002 (PDO Zorunlu, ORM Yasak) + ADR-040 (DB Authority).

## 1. Temel Kurallar

| Kural | Değer | Kaynak |
|-------|-------|--------|
| Erişim katmanı | **Raw PDO only** — ORM (Eloquent, Doctrine Entities) YASAK | ADR-002, Guardrail #9 |
| Sorgu seçimi | `SELECT *` YASAK → explicit kolon listesi | §21, Guardrail (§18) |
| Input bağlama | **Prepared statement her sorguda zorunlu** (`prepare` + `?`/`:name`); concat/`sprintf` ile sorgu YASAK | ADR-002 §2 |
| Veritabanı | MySQL 9 · **18 BCNF veritabanı, 156 tablo, 82 FK** (54 DB-içi + 28 cross-DB) | §18, ADR-040 §1.1 |
| BCNF | 18 şema BCNF kuralına uymalıdır; ihlal → 3NF→BCNF audit | §18, §22 |
| Charset | DSN `charset=utf8mb4` (+ `utf8mb4_0900_ai_ci`); `SET NAMES` YASAK | ADR-002 §2.2 |
| Tipler | `declare(strict_types=1)` · `const`/`let` (PHP tarafı: tipli imzalar) | §12 |
| Manuel kaçış | `addslashes` / `mysql_real_escape_string` prepared statement yerine GEÇMEZ | ADR-002 §1.4 |

**Zorunlu bağlantı imzası (ADR-002 §2.2 a):**

```php
$pdo = new PDO(
    'mysql:host=' . $host . ';dbname=' . $db . ';charset=utf8mb4',
    $user, $pass, // değerler .env'de — koda hardcode YASAK
    [
        PDO::ATTR_EMULATE_PREPARES      => false,
        PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE    => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT               => 5,     // connect-only, constructor-only
        PDO::ATTR_PERSISTENT            => false,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
    ]
);
```

## 2. Yasaklı Sorgular — Yanlış / Doğru

| # | ❌ Yanlış | ✅ Doğru |
|---|----------|----------|
| 1 | `SELECT * FROM playlists` | `SELECT id, name, user_id FROM playlists` |
| 2 | `"SELECT id FROM playlists WHERE id=$id"` (concat) | `$pdo->prepare('SELECT id FROM playlists WHERE id = ?')` + `bindValue` |
| 3 | `sprintf("DELETE FROM t WHERE id=%s", $id)` | prepared placeholder |
| 4 | `$user->save()` / `Playlist::find($id)` (ORM) | repository + PDO prepared |
| 5 | `query("INSERT ... '" . $name . "'")` | `execute([':name' => $name])` |
| 6 | `ATTR_EMULATE_PREPARES => true` / unset | `=> false` (native prepared) |

```php
// ✅ Doğru — prepared + explicit kolonlar
$stmt = $pdo->prepare(
    'SELECT id, name, user_id, created_at FROM playlists WHERE user_id = :uid ORDER BY created_at DESC LIMIT 50'
);
$stmt->execute([':uid' => $userId]);
$rows = $stmt->fetchAll(); // FETCH_ASSOC (imzadan)
```

## 3. BCNF Özet (ADR-040)

- **18 şema** (`coremusic_auth` … `coremusic_patch`), **156 tablo** (156/156 PK), **82 FK**.
- Tek yazar servis kuralı; cross-DB FK varsayılan yasak + 28 istisna (ADR-040 §1.1).
- Migration forward-only (ADR-014 — geri migration yasak).
- BCNF self-declaration şema başlıklarında **iddiadır, denetim DEĞİL** →
  `⚠️ VERIFICATION REQUIRED` (BCNF denetimi planlı, ADR-033'e bağlı).

## 4. Diğer yasak desenler (code review kapıları)

`MYSQL_ATTR_MULTI_STATEMENTS=true` · açık transaction'ı commit/rollback'siz bırakmak ·
sorgu-hatasında otomatik retry (yalnız bağlantı kurulumunda max 3, ADR-002 §2.2 b) ·
hardcoded secret (→ `.env`).