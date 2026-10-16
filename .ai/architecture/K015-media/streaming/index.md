---
node_id: K015-media/streaming
node_type: subdomain
status: draft
parent_id: K015-media
children: []
depends_on: []
evidence:
  - "v2 §2.15 — çocuk adı kaynağı (birebir)"
  - "streaming-kapsam.md · streaming-web-arastirma.md (10 URL, 2026-10-16) · streaming-ornekler.md (JS ×2 + PHP 8.4 ×1)"
validation:
  - "İsim v2 §2.15 ile birebir karşılaştırıldı — 2026-10-12"
  - "Grup dosyaları üretildi — 2026-10-16 (K009 format referansı)"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Akış biçimi/protokolü → ⚠️ UNKNOWN (taşıma K014'te)"
  - "Codec seti → ⚠️ UNKNOWN (media-master §12) — web-arastirma §4/2"
  - "buffering vs rebuffering çelişkisi (§12/4 × §14/2) → açık — streaming-kapsam §6/3"
group: mimari
---


# streaming — K015-media (kademe-2 · subdomain)

**ELI15:** Akışla dinleme: kaynağından parça parça okuyup çalma.

**Sınır (ne DEĞİL):** Taşıma K014-network · dış kaynak K013-integraion · çalma K015/playback.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler media-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `streaming-kapsam.md` | Kapsam sözleşmesi: §1-§6 + §14.2 zinciri birebir | Kademe-2 |
| kanit | `streaming-web-arastirma.md` | 10 canlı URL doğrulama (2026-10-16) + 6 cross-check | Kademe-2 |
| kod | `streaming-ornekler.md` | Vanilla JS ES6+ ×2 + PHP 8.4 kuyruk ×1 iskeleti | Kademe-2 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | Akış biçimi/protokolü → ⚠️ UNKNOWN (taşıma K014'te) | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |
| 4 | Codec seti → ⚠️ UNKNOWN (media-master §12) | FM open_questions |
| 5 | buffering vs rebuffering çelişkisi (§12/4 × §14/2) → açık | FM open_questions |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
