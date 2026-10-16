---
node_id: K015-media/artwork
node_type: subdomain
status: draft
parent_id: K015-media
children: []
depends_on: []
evidence:
  - "v2 §2.15 — çocuk adı kaynağı (birebir)"
  - "https://musicbrainz.org/doc/Cover_Art_Archive/API — CAA hazır varyantlar 250/500/1200px, 307 redirect, 503 rate-limit (2026-10-16 doğrulandı)"
  - "https://navidrome.org/docs/usage/library/artwork/ — CoverArtPriority zinciri (cover.*/folder.*/front.*/embedded/external/placeholder), JPEG/PNG/WebP (2026-10-16 doğrulandı)"
validation:
  - "İsim v2 §2.15 ile birebir karşılaştırıldı — 2026-10-12"
  - "8/8 web kaynağı 2026-10-16'da HTTP 200 ile doğrulandı (artwork-web-arastirma.md §1)"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Boyut/format politikası → ⚠️ UNKNOWN"
  - "Varyant boyut seti (CAA/Deezer/iTunes referansları §3a — KARAR değil) → ⚠️ UNKNOWN (media-master §12/7)"
  - "Kabul formatları (JPEG/PNG/WebP) → ⚠️ UNKNOWN (web-arastirma W-2)"
  - "Kaynak öncelik sırası (embedded → yan dosya → dış servis) → ⚠️ VERIFICATION REQUIRED (W-4)"
group: mimari
---


# artwork — K015-media (kademe-2 · subdomain)

**ELI15:** Kapak resimleri: getirme, boyutlandırma ve önbelleğe alma (iddia).

**Sınır (ne DEĞİL):** Önbellek politikası media-cache · dış kaynak K013 · gösterim K011.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler media-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `artwork-kapsam.md` | Kapsam içi/dışı · durum makinesi · veri akışı · hata durumları | Kademe-2 |
| kanit | `artwork-web-arastirma.md` | 8 canlı doğrulanmış kaynak (CAA API, iTunes, Deezer, Navidrome artwork) | Kademe-2 |
| kod | `artwork-ornekler.md` | Öncelik zinciri + varyant servisi + SQL eskizi (K007) + JS placeholder | Kademe-2 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | Boyut/format politikası → ⚠️ UNKNOWN | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |
| 4 | Varyant boyut seti (§3a referans = KARAR değil) → ⚠️ UNKNOWN | FM open_questions (W-1) |
| 5 | Kabul formatları → ⚠️ UNKNOWN | FM open_questions (W-2) |
| 6 | Kaynak öncelik sırası → ⚠️ VERIFICATION REQUIRED | FM open_questions (W-4) |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
