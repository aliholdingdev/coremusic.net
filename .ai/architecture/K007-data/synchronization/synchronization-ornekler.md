---
node_id: "K007-synchronization-ornekler"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/transactions"
evidence:
  - "shared/src/Database/DatabaseRegistry.php:14-19 — registerMySql (okundu 2026-10-12)"
validation: "2026-10-15 — repo örneği = kayıt/topoloji; senkron kodu YOK"
open_questions: []
group: kod
---

# synchronization — Örnekler (K007)

## §1 Repo gerçeği: topoloji kaydı (senkron DEĞİL)

Dosya: `shared/src/Database/DatabaseRegistry.php` (satır 14-19, özet):

```php
public function registerMySql(
    string $key, string $host, string $dbName,
    string $user, string $password,
    int $port = 3306, string $charset = 'utf8mb4'
): void { ... }
```

> Bu kayıt 18 DB'ye **bağlanmayı** sağlar; aralarında veri akışı **kurmaz**.
> Senkronizasyon bugün uygulama kodunda (K012) manuel olarak yapılıyorsa → VERIFICATION REQUIRED.

## §2 Tasarım örneği (kanıtsız — yalnız referans)

```text
[yazma transaction]
 业务事件  →  outbox_event (aynı DB, aynı transaction)
      ↓ (poller/CDC)
  tüketici (search index · cache invalidation · K013 entegrasyon)
```

> Bu kalıp **plan**tır; CoreMusic'te karşılığı yoktur (D-Y1).

---

**Authority:** Bayram Ali / Vault Steward