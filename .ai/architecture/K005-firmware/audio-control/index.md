---
node_id: K005-firmware/audio-control
node_type: subdomain
status: draft
parent_id: "K005-firmware"
children:
  - audio-control-anayasa.md
  - audio-control-surucu.md
  - audio-control-ornekler.md
depends_on:
  - K001-donanım
evidence:
  - "v2 §2.5 mini-ağaç — ad kaynağı"
  - "firmware-master.md §3 satır 4 · K003 bağımlılığı (§5)"
validation: "draft — A1 tur-1; D-kriterleri K005-firmware/firmware-dogrulama.md §2 · 2026-10-14 §GRUPLAR disk ile birebir"
open_questions:
  - "kime ait: K003 mü K005 mı register'ı yazar? (bagimliliklar §4/7)"
group: mimari
---

# K005-firmware/audio-control — ELI15

Ses donanımının ayar ve kiplerini kuran düğüm: örnekleme hızı, format, kanal düzeni. K003'ün hazır durum sorgusuna cevap veren taraf.

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
| Kapsam | `audio-control-kapsam.md` içinde tanımlı (2026-10-14) |
| Üretim | 2026-10-14 — eksik modüller yazıldı, §GRUPLAR 6/6 |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `audio-control-anayasa.md` | Anayasa — kural, ilke ve iddialar | Kademe-2 |
| mimari | `audio-control-surucu.md` | Arayüz ve sözleşme katmanı | Kademe-2 |
| mimari | `audio-control-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| kod | `audio-control-ornekler.md` | Uygulama örnekleri ve kalıp gösterimi | Kademe-2 |
| kanit | `audio-control-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
