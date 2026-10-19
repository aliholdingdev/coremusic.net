---
node_id: "K007-cache-ornekler"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/repositories"
evidence:
  - "shared/src/Cache/CacheManager.php:6-22 — tam metin okundu (2026-10-15)"
validation: "2026-10-15 — gerçek repo kodu birebir kopyalandı (uyarlama yok)"
open_questions: []
group: kod
---

# cache — Örnekler (K007)

## §1 Repo gerçeği: adapter seçim zinciri

Dosya: `shared/src/Cache/CacheManager.php` (satır 6-22):

```php
final class CacheManager
{
    private static ?CacheInterface $adapter = null;

    public static function getAdapter(): CacheInterface
    {
        if (self::$adapter === null) {
            if (function_exists('apcu_fetch')) {
                self::$adapter = new ApcuAdapter();
            } else {
                self::$adapter = new MemoryAdapter();
            }
        }
        return self::$adapter;
    }

    public static function setAdapter(CacheInterface $adapter): void
    {
        self::$adapter = $adapter;
    }
}
```

## §2 Örnek okuma (ne anlama gelir)

| Satır | Davranış | Not |
|-------|----------|-----|
| `function_exists('apcu_fetch')` | runtime tespiti | apcu yoksa sessiz fallback — "neden hızlı değil?" gizlenebilir (R-cache) |
| `static $adapter` | process-ömrü singleton | FPM worker bazlı; workerlar arası tutarlılık APCu'ya bağlı |
| `setAdapter` | test seam'i | K18-testing için enjeksiyon kapısı |

---

**Authority:** Bayram Ali / Vault Steward