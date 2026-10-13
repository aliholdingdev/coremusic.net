---
node_id: K001-donanım/donanaım-type/audio
node_type: bounded-context
status: draft
parent_id: "K001-donanım/donanaım-type"
children:
  - audio-kapsam.md
  - audio-dogrulama.md
  - audio-ornekler.md
  - cipler/ak4458-dac.md
  - cipler/pcm3168a-codec.md
  - cipler/tpa3255-amplifier.md
depends_on:
  - K000-isletim-sistemi
evidence:
  - "v2 §2.2 mini-ağaç — ad kaynağı"
  - ".ai/AGENTS.md §4 (hedef yığın) — 2026-10-12 okundu"
validation: "draft — A1 tur-1; D-kriterleri K001-donanım/donanım-dogrulama.md §2"
open_questions:
  - "TPA3255 PVDD aralığı hangisi: 24–52 V mi, 18–53.5 V mi? (Q2)"
group: mimari
---

# K001-donanım/donanaım-type/audio — ELI15

Ses donanımı sınıfı: DAC/ADC, amplifikatör ve ses kartı parçaları. Hedef yığın PCM3168A · AK4458 · Class AB olarak kayıtlı; K003'ün fiziksel muhatabı burası.

> ⚠️ VERIFICATION REQUIRED: Bu dugumun tanimi yalnizca v2 §2.2'deki **adindan**
> turetilmistir; v2 cocuklari icerik olarak tanimlamaz. Gorev, arayuz ve
> kabul kriterleri kanit turunda netlesecek.

## Durum

| Alan | Deger |
|---|---|
| node_type | bounded-context |
| parent | K001-donanım/donanaım-type |
| children | `audio-kapsam.md` · `audio-dogrulama.md` · `audio-ornekler.md` · `cipler/` (3 cip kaydı) |
| status | draft |
| Kapsam | Kanit turu-2 bekliyor |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-3 |
| mimari | `audio-kapsam.md` | Ses donanımı kapsam sınırı: cip kaydı · ADR-038 (TEYİT) · K001↔K003 sınırı | Kademe-3 |
| kanit | `audio-dogrulama.md` | Ses kapıları: M1–M6 eşik · envanter · web 200 kanıt matrisi | Kademe-3 |
| kod | `audio-ornekler.md` | I2S/TDM pin · init sırası · kanal eşleme · jitter şablonları | Kademe-3 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12