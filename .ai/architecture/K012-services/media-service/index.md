---
node_id: K012-services/media-service
node_type: subdomain
status: draft
parent_id: K012-services
children:
  - "media-service-anayasa.md"
  - "media-service-surucu.md"
  - "media-service-ornekler.md"
depends_on: []
evidence:
  - "v2 §2.12 — çocuk adı kaynağı (birebir)"
validation:
  - "İsim v2 §2.12 ile birebir karşılaştırıldı — 2026-10-12"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "playback-service ile sınırı → ⚠️ VERIFICATION REQUIRED"
group: mimari
---


# media-service — K012-services (kademe-2 · subdomain)

**ELI15:** Medya varlığına dair arka plan işlerini yürüten servis.

**Sınır (ne DEĞİL):** Dosya saklama/akış K015-media · iş kuralı K008-domain.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler services-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `media-service-anayasa.md` | CLI komutları, sınıf envanteri, kural sabitleri (rol: anayasa) | Kademe-3 |
| mimari | `media-service-surucu.md` | Sürücü akışı: tarama→ingest→audit, commit kapısı (rol: surucu) | Kademe-3 |
| kod | `media-service-ornekler.md` | Tam dosya blokları: Ulid, Slugger, Validator başlıkları (rol: ornekler) | Kademe-3 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | playback-service ile sınırı → ⚠️ VERIFICATION REQUIRED | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
