# 07 — CoreMusic Entegrasyonu

> Bu skill bağımsız üretebilir, ama CoreMusic bağlamında vault/ADR/agent yönlendirmesine uyar.
> SSOT: `.ai/.sql/mysql/*.sql` + ADR-040. Bu dosya sayı uydurmaz — envanter vault'tadır.

## Mimari bağlam

- **Stack:** PHP 8.x (PDO, `strict_types`) + Vanilla JS + MySQL (InnoDB, utf8mb4).
- **Yasaklar (ADR):** ORM yok (ADR-002), framework yok (ADR-001), `SELECT *` yok (ADR-014),
  hard delete yok (soft delete zorunlu), `FLOAT` para yok (DECIMAL).
- **Katman:** SQL yalnız repository/Infrastructure katmanında; iş mantığı PHP'de, SP minimum.

## Vault & ADR referansları

| İhtiyaç | Kaynak |
|---------|--------|
| DB authority / envanter | ADR-040 · `.ai/.sql/mysql/*.sql` |
| No ORM | ADR-002 (`pdo-mandatory-no-orm`) |
| No SELECT * | ADR-014 |
| Brain / mimari kararlar | `.ai/brain.md` |
| ADR indeksi | `.ai/.decisions/` |

> **Envanter notu (zero-hallucination):** Eski skill sürümünde “11 BCNF veritabanı / 68 tablo”
> listesi vardı ve bayat olarak işaretlenmişti (gerçek sayı vault'ta farklı olabilir). Bu sürüm
> **sayı iddia etmez**; güncel tablo/DB sayısı için `.ai/.sql/mysql/` sayımı esastır ve
> gerektiğinde `VERIFICATION REQUIRED` ile doğrulanır.

## Agent routing

| Görev | Rol |
|-------|-----|
| Şema/normalizasyon/BCNF | `data-engineer` (Expert #3) |
| OWASP/PII/audit veto | `security-engineer` |
| Gelişmiş sorgu/EXPLAIN | `sql-pro` / `postgres-pro` |
| Repository/PDO katmanı | `backend-developer` / `php-pro` |

## CoreMusic tip/standart tercihleri

| Konu | Tercih |
|------|--------|
| PK | `id BIGINT UNSIGNED AUTO_INCREMENT` |
| Charset | `utf8mb4` + tutarlı collation (vault'taki ile aynı — karıştırma yasak) |
| Zaman | `created_at`, `updated_at`, `deleted_at` her varlık tablosunda |
| Para | `DECIMAL(10,2)` |
| Audit | Kritik tablo için `{tablo}_audit` + trigger |

## Uygulama notu (repository)

- Tüm sorgular **prepared statement** (PDO); ad/şema sabitleri bile parametre disiplininde.
- `SELECT *` yerine açık kolon listesi (ADR-014).
- Soft delete filtresi her sorguda (`deleted_at IS NULL`); view/base query ile merkezileştir.

## Çapraz referans

- Kalite kapısı: [checklist.md](checklist.md).
- QA listesi: [10-checklist.md](10-checklist.md).
- Örnek çıktı: [08-examples.md](08-examples.md).
