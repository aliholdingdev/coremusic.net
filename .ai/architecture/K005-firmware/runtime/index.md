---
node_id: K005-firmware/runtime
node_type: subdomain
status: draft
parent_id: "K005-firmware"
children:
  - runtime-anayasa.md
  - runtime-surucu.md
  - runtime-ornekler.md
  - runtime-kapsam.md
  - runtime-dogrulama.md
depends_on:
  - K001-donanım
evidence:
  - "v2 §2.5 mini-ağaç — ad kaynağı"
  - "firmware-master.md §3 satır 2"
validation: "draft — A1 tur-1; D-kriterleri K005-firmware/firmware-dogrulama.md §2 · 2026-10-14 §GRUPLAR disk ile birebir"
open_questions:
  - "döngü periyodu/öncelik tanımı yok ⚠️"
group: mimari
---

# K005-firmware/runtime — ELI15

Açılıştan sonra sürekli dönen kontrol döngüsü: değerleri okur, karar verir, gerekirse komut yazar. Fabrikanın vardiya şefi — hiç durmaz.

> ⚠️ VERIFICATION REQUIRED: Bu düğümün tanımı yalnızca v2 §2.5'teki **adından**
> türetilmiştir; v2 çocukları içerik olarak tanımlamaz. Register/sürüm/protokol
> bilgileri kanıt turunda netleşecektir — uydurma değer yazılmaz.

## Durum

| Alan | Değer |
|---|---|
| node_type | subdomain |
| parent | K005-firmware |
| children | 5 modül (anayasa · surucu · ornekler · kapsam · dogrulama) · disk 6 md |
| status | draft |
| Kapsam | `runtime-anayasa.md` + `runtime-kapsam.md` içinde tanımlı |
| Üretim | 2026-10-14 çekirdek · 2026-10-15 `runtime-kapsam.md` (mimari) + `runtime-dogrulama.md` (kanit) eklendi — boot dalı kalıbında 6 dosyaya tamamlandı |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `runtime-anayasa.md` | Anayasa — kural, ilke ve iddialar | Kademe-2 |
| kod | `runtime-surucu.md` | Arayüz ve sözleşme katmanı (frontmatter `group: kod` — boot-surucu `mimari` ile çelişir, ⚠️) | Kademe-2 |
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| kod | `runtime-ornekler.md` | Uygulama örnekleri ve kalıp gösterimi | Kademe-2 |
| mimari | `runtime-kapsam.md` | Kapsam sınırı, bileşen envanteri, bağımlılık grafiği | Kademe-2 |
| kanit | `runtime-dogrulama.md` | DV-RT doğrulama senaryoları, eşikler, dürüstlük blokları | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
