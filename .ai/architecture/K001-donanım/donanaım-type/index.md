---
node_id: K001-donanım/donanaım-type
node_type: subdomain
status: draft
parent_id: "K001-donanım"
children:
  - hdd
  - audio
  - ssd
  - hdd/hdd-kapsam.md
  - hdd/hdd-dogrulama.md
  - hdd/hdd-ornekler.md
  - ssd/ssd-kapsam.md
  - ssd/ssd-dogrulama.md
  - ssd/ssd-ornekler.md
  - audio/audio-kapsam.md
  - audio/audio-dogrulama.md
  - audio/audio-ornekler.md
depends_on:
  - K000-isletim-sistemi
evidence:
  - "v2 §2.2 mini-ağaç — ad kaynağı (yazım kaynağa sadık: donanaım-type)"
  - "donanım-anayasa.md §3 — 3 alt bağlam"
validation: "draft — A1 tur-1; D-kriterleri K001-donanım/donanım-dogrulama.md §2"
open_questions:
  - "Envanter eksik: ekran, kablo, güç kaynağı bu ağaçta yok (K001 Q4)"
group: mimari
---

# K001-donanım/donanaım-type — ELI15

Donanımın **türlerine** göre ayrıldığı raf: içinde hangi parçaların olduğu değil,
hangi sınıftan parçaların bulunduğunu tarayan düğüm. Üç alt bağlam açıldı:
hdd · audio · ssd.

> yazım notu: donanaım-type yazımı v2 kaynağına sadıktır — **düzeltme yasak**
> (v2 §3 KURAL 1). Klasör adı bu yazımı taşır.

> ⚠️ VERIFICATION REQUIRED: v2 bu düğümü yalnız adıyla tanımlar; envanter
> (ekran/kablo/güç kaynağı) bilinmiyor — UNKNOWN olarak bırakıldı.

## Durum

| Alan | Değer |
|---|---|
| node_type | subdomain |
| parent | K001-donanım |
| children | hdd · audio · ssd (bounded-context) |
| status | draft |
| Kapsam | Envanter tamamlanacak — K001 Q4 |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `donanaım-type-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| kanit | `donanaım-type-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |
| mimari | `hdd/hdd-kapsam.md` | Mekanik disk kapsamı (kapasite · risk · 3-2-1) | Kademe-3 |
| kanit | `hdd/hdd-dogrulama.md` | HDD kapıları: SMART · yüzey · gürültü · kurtarma | Kademe-3 |
| kod | `hdd/hdd-ornekler.md` | Tarama · streaming-buffer · rotasyon şablonları | Kademe-3 |
| mimari | `ssd/ssd-kapsam.md` | Katı hal kapsamı: flash · WAF · TRIM · TBW · boot | Kademe-3 |
| kanit | `ssd/ssd-dogrulama.md` | SSD kapıları: hız · TRIM · sağlık · sıcaklık | Kademe-3 |
| kod | `ssd/ssd-ornekler.md` | İndeksleme · önbellek · izleme şablonları | Kademe-3 |
| mimari | `audio/audio-kapsam.md` | Ses donanımı kapsamı: PCM3168A · AK4458 · TPA3255 · ADR-038 (TEYİT) | Kademe-3 |
| kanit | `audio/audio-dogrulama.md` | Ses kapıları: M1–M6 eşik · envanter · HEAD-200 kanıt matrisi | Kademe-3 |
| kod | `audio/audio-ornekler.md` | I2S/TDM pin · init sırası · kanal eşleme · jitter şablonları | Kademe-3 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12

## İlişkili dosyalar

- `donanaım-type-kapsam.md` — yazım korumalı kapsam · 3 alt bağlam sınırı · envanter UNKNOWN kaydı (2026-10-12)
- `donanaım-type-dogrulama.md` — 17 satırlık disk + tur-3 web kanıt tablosu (AK4458 · PCM3168A · Class AB sabiti, 2026-10-12)