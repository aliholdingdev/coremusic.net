---
node_id: K001-donanım/donanım-core
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
  - "donanım-core mu depolamayı (hdd/ssd) mı sahiplenir?"
group: mimari
---

# K001-donanım/donanım-core — ELI15

Donanım çekirdeği: parça envanteri, kapasite/ölçü bilgisi ve temel sağlık verisinin toplandığı merkez. Kernel'deki ham erişimi uygulamaya hazır hale getirir.

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
| mimari | `donanım-core-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| kanit | `donanım-core-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12

## İlişkili dosyalar

- `donanım-core-kapsam.md` — envanter/ölçü/sağlık kapsamı · kernel sınırı Q3 bekleyişi (2026-10-12)
- `donanım-core-dogrulama.md` — 16 satırlık disk + tur-3 web kanıt tablosu (LM5122 · MJL21193/94 · AK4458 ölçüm, 2026-10-12)