---
node_id: K001-donanım/donanım-api
node_type: subdomain
status: draft
parent_id: "K001-donanım"
children:
[]
depends_on:
  - K000-isletim-sistemi
evidence:
  - "v2 §2.2 mini-ağaç — ad kaynağı"
  - "donanım-anayasa.md §3 çocuk satırı"
validation: "draft — A1 tur-1; D-kriterleri K001-donanım/donanım-dogrulama.md §2"
open_questions:
  - "API yüzeyi hangi protokole oturuyor (sürücü ioctl / sysfs benzeri)? ⚠️"
group: mimari
---

# K001-donanım/donanım-api — ELI15

Donanımın yazılıma açılan arayüzü: üst katmanların (K000 sürücü, K003 ses motoru) parça bilgisine sormasına izin veren çağrı seti. Donanımın kendisi değil, onun dışa kapalı olmayan yüzü.

> ⚠️ VERIFICATION REQUIRED: Bu dugumun tanimi yalnizca v2 §2.2'deki **adindan**
> turetilmistir; v2 cocuklari icerik olarak tanimlamaz. Gorev, arayuz ve
> kabul kriterleri kanit turunda netlesecek.

## Durum

| Alan | Deger |
|---|---|
| node_type | subdomain |
| parent | K001-donanım |
| children | yok (yaprak) |
| status | draft |
| Kapsam | Kanit turu-2 bekliyor |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `donanım-api-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| kanit | `donanım-api-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12

## İlişkili dosyalar

- `donanım-api-kapsam.md` — erişim arayüzü kapsamı · protokol kararı UNKNOWN · sınır komşuları (2026-10-12)
- `donanım-api-dogrulama.md` — 15 satırlık disk + tur-3 web kanıt tablosu (ASIO GPLv3 · PCM3168A SPI/I2C · XU316 USB Audio, 2026-10-12)