---
node_id: K005-firmware/device-control
node_type: subdomain
status: draft
parent_id: "K005-firmware"
children:
  - device-control-anayasa.md
  - device-control-surucu.md
  - device-control-ornekler.md
depends_on:
  - K001-donanım
evidence:
  - "v2 §2.5 mini-ağaç — ad kaynağı"
  - "firmware-master.md §3 satır 3"
validation: "draft — A1 tur-1; D-kriterleri K005-firmware/firmware-dogrulama.md §2 · 2026-10-14 §GRUPLAR disk ile birebir"
open_questions:
  - "hangi register'lara kimin yazdığı belirsiz ⚠️"
group: mimari
---

# K005-firmware/device-control — ELI15

Parçalara komut gönderen düğüm: bir motoru çalıştır, bir röleyi kapat, bir ayarı uygula. Düğmelere basan parmak.

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
| Kapsam | `device-control-kapsam.md` içinde tanımlı (2026-10-14) |
| Üretim | 2026-10-14 — eksik modüller yazıldı, §GRUPLAR 6/6 |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `device-control-anayasa.md` | Anayasa — kural, ilke ve iddialar | Kademe-2 |
| mimari | `device-control-surucu.md` | Arayüz ve sözleşme katmanı | Kademe-2 |
| mimari | `device-control-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| kod | `device-control-ornekler.md` | Uygulama örnekleri ve kalıp gösterimi | Kademe-2 |
| kanit | `device-control-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
