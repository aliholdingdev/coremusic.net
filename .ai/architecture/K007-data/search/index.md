---
node_id: search
node_type: subdomain
status: draft
parent_id: K007-data
children: []
depends_on: []
evidence:
  - "v2 §2.7 mini-ağaç — coremusic-katmanli-mimari-agaci-v2.md satır 284"
validation:
  - "2026-10-12 — v2 mini-ağaç satır 284 ile karşılaştırıldı: ad birebir"
open_questions:
  - "Rol tanımı v2'de yok → ⚠️ VERIFICATION REQUIRED"
  - "Kademe-3 (bounded-context) yapısı tanımlı değil → ADR-097 K4"
  - "depends_on boş: bu kademe için kanıtlanmış bağımlılık yok"
group: mimari
---

# search — K007-data (kademe-2)

| Alan | Değer |
|---|---|
| node_id | search |
| node_type | subdomain |
| parent_id | K007-data |
| Kaynak | v2 §2.7 (satır 284) |
| Durum | draft |

## Ne işe yarar?

v2 bu alt konuya **yalnızca ad verir** — tanım yok → `⚠️ VERIFICATION REQUIRED».
Ad, kademe-3 tasarımında başlangıç noktasıdır; resmi kapsam ADR-097 K4'te kilitlenir.
Tahminle dolgu **yapılmaz**.

## Bağımlılık

- Üst düğüm künyesi: `../index.md»
- Üst düğüm bağımlılıkları: `../data-bagimliliklar.md»
- Bu düğüm: `children: []» (v2 daha derin göstermiyor).

## Durum

| Ölçüt | Sonuç |
|---|---|
| v2'de adı geçiyor mu? | ✅ evet (satır 284) |
| Rol tanımı var mı? | ❌ yok → VERIFY REQUIRED |
| Kademe-3 planı | ⏳ ADR-097 K4 |
| status | draft |

---

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward

