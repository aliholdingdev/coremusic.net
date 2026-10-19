---
node_id: K001-donanım/donanaım-type/hdd
node_type: bounded-context
status: draft
parent_id: "K001-donanım/donanaım-type"
children:
  - hdd-kapsam.md
  - hdd-dogrulama.md
  - hdd-ornekler.md
depends_on:
  - K000-isletim-sistemi
evidence:
  - "v2 §2.2 mini-ağaç — ad kaynağı"
  - "v2 §2.2 alt bağlam satırı"
validation: "draft — A1 tur-1; D-kriterleri K001-donanım/donanım-dogrulama.md §2"
open_questions:
  - "Kullanılan disk envanteri (marka/model/capacity) bilinmiyor — UNKNOWN"
group: mimari
---

# K001-donanım/donanaım-type/hdd — ELI15

Dönen disk (HDD) sınıfı: mekanik depolama, kapasite ve dayanıklılık özellikleri. Arşiv gibi büyük ve yavaş okuma senaryolarının fiziksel karşılığı.

> ⚠️ VERIFICATION REQUIRED: Bu dugumun tanimi yalnizca v2 §2.2'deki **adindan**
> turetilmistir; v2 cocuklari icerik olarak tanimlamaz. Gorev, arayuz ve
> kabul kriterleri kanit turunda netlesecek.

## Durum

| Alan | Deger |
|---|---|
| node_type | bounded-context |
| parent | K001-donanım/donanaım-type |
| children | `hdd-kapsam.md` · `hdd-dogrulama.md` · `hdd-ornekler.md` |
| status | draft |
| Kapsam | Kanit turu-2 bekliyor |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-3 |
| mimari | `hdd-kapsam.md` | Mekanik disk kapsamı: kapasite, risk, terim | Kademe-3 |
| kanıt | `hdd-dogrulama.md` | Kapılar: SMART · yüzey · gürültü · kurtarma | Kademe-3 |
| kod | `hdd-ornekler.md` | Tarama · streaming-buffer · rotasyon adım şablonları | Kademe-3 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12