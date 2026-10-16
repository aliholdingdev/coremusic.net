---
node_id: K015-media/media-testing
node_type: subdomain
status: draft
parent_id: K015-media
children: []
depends_on: []
evidence:
  - "v2 §2.15 — çocuk adı kaynağı (birebir)"
  - "media-testing-kapsam.md (grup: mimari) — §1-§6 kapsam/durum makinesi/veri akışı/hata/açık sorular, 2026-10-16"
  - "media-testing-web-arastirma.md (grup: kanit) — 8 URL canlı doğrulama + C-1..C-4 cross-check, 2026-10-16"
  - "media-testing-ornekler.md (grup: kod) — PHPUnit ^10.5 decode/tolerans iskeleti + bash/ffmpeg decode sweep, 2026-10-16"
validation:
  - "İsim v2 §2.15 ile birebir karşılaştırıldı — 2026-10-12"
  - "§GRUPLAR 4 satıra genişletildi (index + 3 dosya) — 2026-10-16"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "media-testing ↔ K019-testing sahipliği → ⚠️ VERIFICATION REQUIRED (§7/4) — açık bırakıldı"
  - "Araç seti (§12/10) + tolerans eşikleri → ⚠️ UNKNOWN (kapsam Q-1/Q-3)"
group: mimari
---


# media-testing — K015-media (kademe-2 · subdomain)

**ELI15:** Medya tarafı testleri: dosya, akış, çevrimdışı senaryoları.

**Sınır (ne DEĞİL):** Ortak test altyapısı K019-testing · ses kalitesi testi K003/K019 sınırında.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler media-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `media-testing-kapsam.md` | Kapsam içi/dışı, durum makinesi (draft/running/passed/failed/skipped), veri akışı, hata durumları, açık sorular | Kademe-2 |
| kanit | `media-testing-web-arastirma.md` | Canlı URL doğrulama tablosu + çapraz kontrol (ffprobe, ITU P.863, toleranslı karşılaştırma, E2E ses testi) | Kademe-2 |
| kod | `media-testing-ornekler.md` | TASARIM İDDİASI iskeletler: PHPUnit ^10.5 decode/tolerans testi + bash/ffmpeg decode sweep | Kademe-2 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | media-testing ↔ K019-testing sahipliği → ⚠️ VERIFICATION REQUIRED (§7/4) | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |
| 4 | Araç seti (§12/10) + tolerans eşikleri → ⚠️ UNKNOWN (kapsam Q-1/Q-3) | FM open_questions |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
