---
title: "donanaım-type/display — Ekran Görüntüleme Sınıfı Künyesi"
type: architecture
node_id: K001-donanım/donanaım-type/display
node_type: bounded-context
status: proposed
parent_id: "K001-donanım/donanaım-type"
children:
  - display-kapsam.md
  - display-dogrulama.md
  - display-ornekler.md
depends_on:
  - K001-donanım
evidence:
  - "Vault Steward 2026-10-13 dağıtık onayı — klasör açımı bu oturumda YAPILDI"
  - "donanım-anayasa.md §10/3 (3 bağlam mı fazlası mı — ⚠️) · §12/C10 (yapısal sınıf, envanterle değişebilir)"
  - "donanaım-type-kapsam.md §2/A6 · §7/3-T1 · §12/M6 — mevcut metinde kapsam DIŞINDA (revizyon: APPROVAL REQUIRED)"
  - "Repo ölçümü 2026-10-13: 700 kaynak dosyada EDID=0 · DDC=0 · HDMI=0 · DisplayPort=0 · VESA=0"
validation: "proposed — 2026-10-13 yazıldı; anayasa ve üst kapsam metni revize EDİLMEDİ (PROPOSED — APPROVAL REQUIRED); D-kriterleri K001-donanım/donanım-dogrulama.md §2"
open_questions:
  - "Q-D1: Ekran envanteri (panel tipi / boyut / çözünürlük / arayüz) bilinmiyor — UNKNOWN (K001 Q4)"
  - "Q-D2: anayasa §10/3 ve §12/C10 revizyonu onayı alınmadı — status: proposed kalır"
group: mimari
date: 2026-10-13
updated: 2026-10-13
---

# K001-donanım/donanaım-type/display — ELI15

Ekran (görüntüleme) donanımı sınıfı: panel teknolojileri (LCD · LED-aydınlatmalı
LCD · OLED), GPU görüntü çıkış bloğu, DisplayPort/HDMI arabirimleri, dokunmatik
katman ve ekran kontrolcüsü. K001 tür envanterinin 5. alt bağlamıdır.

> ⚠️ **PROPOSED — APPROVAL REQUIRED:** Bu klasör Vault Steward 2026-10-13 tarihli
> açık onayı ile açılmıştır; ancak `donanım-anayasa.md` (§10/3 · §12/C10) ve
> `donanaım-type-kapsam.md` (§2/A6 · §7/3-T1 · §12/M6) metinleri hâlâ "yalnız 3
> bağlam" ve "ekran/güç kaynağı bağlamı AÇILMAZ" der. **Metin revizyonu
> yapılmadı** → tüm bu dosya seti `status: proposed` taşır; kapanış anayasa +
> üst kapsam revizyonuyla olur.

> yazım notu: `donanaım-type` yazımı v2 kaynağına sadıktır — **düzeltme yasak**
> (v2 §3 KURAL 1). Klasör adı bu yazımı taşır.

## Durum

| Alan | Değer |
|---|---|
| node_type | bounded-context |
| parent | K001-donanım/donanaım-type |
| children | `display-kapsam.md` · `display-dogrulama.md` · `display-ornekler.md` |
| status | proposed (anayasa revizyonu bekleniyor) |
| Kapsam | Tip/envanter sınıflandırması — panel ↔ arayüz ayrı model |
| Envanter | UNKNOWN (Q-D1) — fiziksel ekran listesi yok |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-3 |
| mimari | `display-kapsam.md` | Çelişki kaydı · tip kayıtları (26 alan) · panel↔arayüz ayrımı | Kademe-3 |
| kanit | `display-dogrulama.md` | Ekran kapıları D1–D9 · HEAD-200 kanıt matrisi · envanter | Kademe-3 |
| kod | `display-ornekler.md` | EDID/DDC · eşleme · ölçüm formları · hata akışı şablonları | Kademe-3 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
