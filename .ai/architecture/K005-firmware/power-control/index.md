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
validation: "draft — A1 tur-1; D-kriterleri K005-firmware/firmware-dogrulama.md §2 · 2026-10-14 §GRUPLAR disk ile birebir"
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
| children | 3 modül (anayasa · surucu · ornekler) · disk 6 md |
| status | draft |
| Kapsam | `power-control-kapsam.md` içinde tanımlı (2026-10-14) |
| Üretim | 2026-10-14 — eksik modüller yazıldı, §GRUPLAR 6/6 |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `power-control-anayasa.md` | Anayasa — kural, ilke ve iddialar | Kademe-2 |
| mimari | `power-control-surucu.md` | Arayüz ve sözleşme katmanı | Kademe-2 |
| mimari | `power-control-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| kod | `power-control-ornekler.md` | Uygulama örnekleri ve kalıp gösterimi | Kademe-2 |
| kanit | `power-control-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
