---
node_id: K001-donanım/donanım-kernel
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
  - "donanım-kernel ile donanım-core sınırı nedir? (K001 Q3)"
group: mimari
---

# K001-donanım/donanım-kernel — ELI15

Donanıma en yakın katman: çekirdek seviyesinde erişim (register okuma-yazma, kesme/hardware interrupt, doğrudan bellek erişimi). İşletim sistemi değil, donanımın kendi kapı eşiği olarak düşünülebilir.

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
| mimari | `donanım-kernel-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| kanit | `donanım-kernel-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |
| kod | `donanım-kernel-ornekler.md` | Erişim yolu şablonları ve kapı denetim kartları | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12

## İlişkili dosyalar

- `donanım-kernel-kapsam.md` — ham erişim (register/kesme/DMA) kapsamı · Q3 sınır bekleyişi · topoloji tartışması kapalı (2026-10-12)
- `donanım-kernel-dogrulama.md` — 16 satırlık disk + tur-3 web kanıt tablosu (XU316 datasheet · TPA3255 kontrol arayüzü, 2026-10-12)