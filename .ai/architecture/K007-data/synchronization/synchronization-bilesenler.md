---
node_id: "K007-synchronization-bilesenler"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/transactions"
evidence:
  - "microservices.io — saga (orchestration/choreography) · database-per-service · api-composition (2026-10-12)"
validation: "2026-10-15 — kalıp envanteri T1; uygulama kanıtı YOK"
open_questions:
  - "Hangi kalıp seçilecek → UNKNOWN (K-07)"
group: mimari
---

# synchronization — Bileşenler (K007)

## §1 Kalıp envanteri (web · T1)

| # | Kalıp | İş | Repo uygulaması |
|---|-------|-----|-----------------|
| 1 | **Outbox** | Aynı transaction'da olay kaydı → sonra yay | ❌ YOK |
| 2 | **CDC (Debezium)** | DB change log'unu akışa çevir | ❌ YOK |
| 3 | **Saga** (orchestration) | Adım-adım geri alma zinciri | ❌ YOK |
| 4 | **Api-composition** | Çok kaynaktan okuma birleştirme | ⚠️ kısmi (çok-DB registry var) |

## §2 Mevcut durum (repo gerçeği)

`DatabaseRegistry` 18 DB'yi anahtarla kaydeder (`registerMySql`) — bu **bağlantı**
topolojisidir, **senkronizasyon** değildir. DB'ler arası tutarlılık bugünkü mimaride
uygulama sorumluluğundadır (K012-services) ve tek transaction sınırı dışına çıkamaz.

---

**Authority:** Bayram Ali / Vault Steward