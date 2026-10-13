---
node_id: K005-firmware/power-control
node_type: subdomain
status: draft
parent_id: "K005-firmware"
children:
  - power-control-anayasa.md
  - power-control-surucu.md
  - power-control-ornekler.md
depends_on:
  - K001-donanım
evidence:
  - "v2 §2.5 mini-ağaç — ad kaynağı"
  - "firmware-master.md §4 T3–T5"
validation: "draft — A1 tur-1; D-kriterleri K005-firmware/firmware-dogrulama.md §2"
open_questions:
  - "PVDD hangisi — Q2 kararı bekleniyor"
group: mimari
---

# K005-firmware/power-control — ELI15

Güç durumlarıyla ilgili her şey: uyku, kapanma, akım/ısı sınırları. Amfi tarafında PVDD aralığı bu düğümün tasarım girdisidir (18–53.5 V datasheet ↔ 24–52 V manifest — ⚠️).

> ⚠️ VERIFICATION REQUIRED: Bu düğümün tanımı yalnızca v2 §2.5'teki **adından**
> türetilmiştir; v2 çocukları içerik olarak tanımlamaz. Register/sürüm/protokol
> bilgileri kanıt turunda netleşecektir — uydurma değer yazılmaz.

## Durum

| Alan | Değer |
|---|---|
| node_type | subdomain |
| parent | K005-firmware |
| children | 3 dosya (anayasa - surucu - ornekler) |
| status | draft |
| Kapsam | Tanım bekliyor — kanıt turu-2 |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
