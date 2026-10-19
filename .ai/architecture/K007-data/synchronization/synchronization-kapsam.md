---
node_id: "K007-synchronization-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/transactions"
  - "K007-data/cache"
evidence:
  - "master §4 satır 14 — CDC/outbox altyapısı repo'da YOK (2026-10-12)"
  - "microservices.io/patterns/data/saga.html — HTTP 200 (2026-10-12)"
  - "debezium.io/documentation/ — CDC (2026-10-12)"
validation: "2026-10-15 — kapsam yazıldı; repo kanıtı YOK kaydı korundu"
open_questions:
  - "S-3 — synchronization ↔ K013-integraion sınırı → SINIR AÇIK"
  - "S-9 — CDC/outbox CoreMusic için gerekiyor mu → UNKNOWN"
group: mimari
---

# synchronization — Kapsam (K007)

## §1 Kapsam İÇİ

| # | Konu | Kanıt |
|---|------|-------|
| 1 | Çok-DB tazelik sorusu (18 DB birbirini nasıl görür?) | `.ai/.sql/CLAUDE.md §3` (18 DB) |
| 2 | Outbox / CDC / saga kalıp değerlendirmesi | microservices.io · debezium (T1) |
| 3 | Çift kayıt sınırı (K013 overlap) | master §2.1 |
| 4 | Sıraya alma (queue) gereksinimi tanımı | UNKNOWN |

## §2 Kapsam DIŞI

| Dışarıda | Nereye | Gerekçe |
|----------|--------|---------|
| Tek DB atomikliği | transactions | senkron ≠ atomik |
| API entegrasyon protokolleri | K013-integraion | taşıma katmanı |
| Cihaz-servis senkronu (uygulama seviyesi) | K011-application | UI akışı |
| Bildirim/telemetri | K016-observability | ölçüm |

## §3 Sınır cümlesi

synchronization, "iki kayıt aynı anda değişirse hangisi kazanır, diğeri ne zaman görür?"
sorusunun sahibidir. **Repo'da bugün bunun altyapısı YOK** (outbox/CDC yok) — bu dosya
yalnız gereksinimi ve kalıp envanterini taşır.

---

**Authority:** Bayram Ali / Vault Steward