---
node_id: "K007-repositories-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/schema"
  - "K007-data/transactions"
evidence:
  - "shared/src/Repository/ApiKeyRepository.php:11-35 — şema docblock + doğrulama akışı (2026-10-15 okundu)"
  - "shared/src/Contracts/Database/IDatabaseManager.php:8-14 — 6 metot sözleşmesi"
  - "ADR-002 — PDO prepared; SELECT * yasak; ORM yasak"
validation: "2026-10-15 — kapsam yazıldı; ADR-002 + 2 repo dosyası"
open_questions:
  - "Repository başına sorgu sayısı ölçümü → UNKNOWN"
  - "N+1 tespit mekanizması → repo'da YOK"
group: mimari
---

# repositories — Kapsam (K007)

## §1 Kapsam İÇİ

| # | Konu | Kanıt |
|---|------|-------|
| 1 | Domain → veri tek kapı kuralı | `ApiKeyRepository.php:11-35` docblock |
| 2 | Prepared statement zorunluluğu (interpolasyon yasak) | ADR-002 · `DatabaseManager` |
| 3 | Kolon listesi disiplini (`SELECT_COLUMNS` sabiti) | `ApiKeyRepository.php` SELECT_COLUMNS |
| 4 | Çok-DB registry erişimi (`dbKey` ile) | `IDatabaseRegistry` · `ApiKeyRepository::DB_KEY='auth'` |
| 5 | Hash-only yazma kuralı (ham anahtar DB'ye girmez) | `ApiKeyRepository.php:18-22` |

## §2 Kapsam DIŞI

| Dışarıda | Nereye | Gerekçe |
|----------|--------|---------|
| SQL çalıştırma motoru | transactions | atomiklik ayrı soru |
| Şema tasarımı | schema | repo şemayı **uygular**, tasarlamaz |
| Cache stratejisi | cache | repo cache'e yazmaz (şimdilik) |
| Domain entity/VO | K008-domain | repo dizi/DTO döner |

## §3 Sınır cümlesi

repositories, "domain'in veriye **tek kapısı**"dır: SQL'i domain'den saklar,
prepared dışına çıkmaz, kolon listesini açıkça yazar.

---

**Authority:** Bayram Ali / Vault Steward