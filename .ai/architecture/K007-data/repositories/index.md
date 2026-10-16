---
node_id: repositories
node_type: subdomain
status: draft
parent_id: K007-data
children:
  - repositories-kapsam
  - repositories-bilesenler
  - repositories-dogrulama
  - repositories-ornekler
  - repositories-web-arastirma
depends_on:
  - K007-data/schema
  - K007-data/transactions
evidence:
  - "v2 §2.7 mini-ağaç — coremusic-katmanli-mimari-agaci-v2.md satır 280"
  - "shared/src/Repository/ApiKeyRepository.php — tek repository (2026-10-15)"
  - "shared/src/Contracts/Database/IDatabaseManager.php:8-14 — 6 metot"
validation:
  - "2026-10-15 — 5 içerik MD'si yazıldı; children disk enumerate 5/5"
open_questions:
  - "171 tablo vs 1 repository olgunluk farkı → kademe-3 (ADR-097 K4)"
group: mimari
---

# repositories — K007-data (kademe-2)

| Alan | Değer |
|---|---|
| node_id | repositories |
| parent_id | K007-data |
| Kaynak | v2 §2.7 (satır 280) |
| Durum | draft |

## Ne işe yarar?

Domain'in veriye **tek kapıdan** erişimi: PDO prepared-only, SELECT * yasak, ORM yasak
(ADR-002). Bugünkü repo gerçeği: 1 repository (api_keys); 171 tablo için kademe-3 planı
ADR-097 K4'te.

## §1 İçerik dosyaları

| # | Dosya | Rol | Grup |
|---|-------|-----|------|
| 1 | [repositories-kapsam.md](repositories-kapsam.md) | sınır | mimari |
| 2 | [repositories-bilesenler.md](repositories-bilesenler.md) | envanter + olgunluk | mimari |
| 3 | [repositories-dogrulama.md](repositories-dogrulama.md) | D-R1…D-R6 | kanit |
| 4 | [repositories-ornekler.md](repositories-ornekler.md) | ApiKeyRepository iskeleti | kod |
| 5 | [repositories-web-arastirma.md](repositories-web-arastirma.md) | PDO 3 URL | kanit |

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Künye | Kademe-2 |
| mimari | `repositories-kapsam.md` | Kapsam | Kademe-2 |
| mimari | `repositories-bilesenler.md` | Bileşen envanteri | Kademe-2 |
| kanit | `repositories-dogrulama.md` | İddia defteri | Kademe-2 |
| kod | `repositories-ornekler.md` | Repo kodu | Kademe-2 |
| kanit | `repositories-web-arastirma.md` | Kaynak taraması | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward