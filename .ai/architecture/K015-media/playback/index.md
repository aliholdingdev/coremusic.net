---
node_id: K015-media/playback
node_type: subdomain
status: draft
parent_id: K015-media
children: []
depends_on: []
evidence:
  - "v2 §2.15 — çocuk adı kaynağı (birebir)"
  - "playback-kapsam.md · playback-web-arastirma.md (10 URL, 2026-10-16) · playback-ornekler.md (C++20 ×3)"
validation:
  - "İsim v2 §2.15 ile birebir karşılaştırıldı — 2026-10-12"
  - "Grup dosyaları üretildi — 2026-10-16 (K009 format referansı)"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Sahiplik: K015 mi K012 mi → ⚠️ VERIFICATION REQUIRED (§7/1)"
  - "Codec seti → ⚠️ UNKNOWN (media-master §12) — web-arastirma §4/2"
  - "Dosya biçimi (mp3/ogg/flac) → ⚠️ UNKNOWN — playback-kapsam §6/5"
group: mimari
---


# playback — K015-media (kademe-2 · subdomain)

**ELI15:** Dosyanın/sinyalin çalınması: okuma, konum, oynatma durumu.

**Sınır (ne DEĞİL):** Ses işleme K003-audio · durum orkestrasyonu K012/playback-service ile çakışma riski.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler media-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `playback-kapsam.md` | Kapsam sözleşmesi: §1-§6 + §14.1 zinciri birebir | Kademe-2 |
| kanit | `playback-web-arastirma.md` | 10 canlı URL doğrulama (2026-10-16) + 6 cross-check | Kademe-2 |
| kod | `playback-ornekler.md` | C++20 Neva üslubu 3 iskelet (durum makinesi, ring-buffer, K003 çağrısı) | Kademe-2 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | Sahiplik: K015 mi K012 mi → ⚠️ VERIFICATION REQUIRED (§7/1) | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |
| 4 | Codec seti → ⚠️ UNKNOWN (media-master §12) | FM open_questions |
| 5 | Dosya biçimi (mp3/ogg/flac) → ⚠️ UNKNOWN | FM open_questions |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
