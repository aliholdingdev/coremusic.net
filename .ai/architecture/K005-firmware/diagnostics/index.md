---
node_id: K005-firmware/diagnostics
node_type: subdomain
status: draft
parent_id: "K005-firmware"
children:
  - diagnostics-anayasa.md
  - diagnostics-surucu.md
  - diagnostics-ornekler.md
depends_on:
  - K001-donanım
evidence:
  - "v2 §2.5 mini-ağaç — ad kaynağı"
  - "firmware-master.md §3 satır 6"
validation: "draft — A1 tur-1; D-kriterleri K005-firmware/firmware-dogrulama.md §2 · 2026-10-14 §GRUPLAR disk ile birebir"
open_questions:
  - "hata kodu seti/rapor formatı tanımlı değil ⚠️"
group: mimari
---

# K005-firmware/diagnostics — ELI15

Cihazın kendini kontrol ettiği, hata bulduğunda raporladığı düğüm: açılma testi, sensör sapması, ısınma uyarısı. Motorun arıza lambası.

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
| Kapsam | `diagnostics-kapsam.md` içinde tanımlı (2026-10-14) |
| Üretim | 2026-10-14 — eksik modüller yazıldı, §GRUPLAR 6/6 |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `diagnostics-anayasa.md` | Anayasa — kural, ilke ve iddialar | Kademe-2 |
| mimari | `diagnostics-surucu.md` | Arayüz ve sözleşme katmanı | Kademe-2 |
| mimari | `diagnostics-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| kod | `diagnostics-ornekler.md` | Uygulama örnekleri ve kalıp gösterimi | Kademe-2 |
| kanit | `diagnostics-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
