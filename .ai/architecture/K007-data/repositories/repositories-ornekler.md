---
node_id: "K007-repositories-ornekler"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/schema"
evidence:
  - "shared/src/Repository/ApiKeyRepository.php:11-46 — docblock + sınıf başı (2026-10-15 okundu)"
validation: "2026-10-15 — gerçek repo kodu; uyarlama yok"
open_questions: []
group: kod
---

# repositories — Örnekler (K007)

## §1 Repo gerçeği: repository iskeleti

Dosya: `shared/src/Repository/ApiKeyRepository.php` (satır 11-46, özet):

```php
final class ApiKeyRepository
{
    public const DB_KEY = 'auth';
    public const RAW_KEY_PATTERN = '/^(cm_live|cm_test)_([A-Za-z0-9]{8})_([A-Za-z0-9\-_]{16,})$/';
    public const KEY_TYPES = ['user', 'service', 'server'];

    private const SELECT_COLUMNS =
        'id, user_id, key_hash, key_prefix, key_name, scopes, key_type, rate_limit, is_active, last_used_at, expires_at';

    public function __construct(
        private readonly IDatabaseRegistry $registry,
        private readonly string $dbKey = self::DB_KEY,
    ) {}

    private function db(): IDatabaseManager
    {
        return $this->registry->get($this->dbKey);
    }
}
```

## §2 Örnek okuma (kalıp dersleri)

| Kalıp | Örnek | Kural |
|-------|-------|-------|
| DB anahtarı sabit | `DB_KEY='auth'` | hangi DB'nin repository'si olduğu görünür |
| Kolon listesi sabit | `SELECT_COLUMNS` | SELECT * yasağının kod karşılığı |
| Registry enjeksiyonu | `IDatabaseRegistry` | testte SQLite sahtesi (K019) |
| Şema docblock | satır 11-22 | repository ile tablo birebir eşleşir |

---

**Authority:** Bayram Ali / Vault Steward