---
node_id: K015-media/library
node_type: subdomain
status: draft
parent_id: K015-media
children: []
depends_on: []
evidence:
  - "v2 §2.15 — çocuk adı kaynağı (birebir)"
  - "https://subsonic.org/pages/api.jsp — getArtists (ID3) vs getIndexes (dosya-yapısı) iki katalog modeli (2026-10-16 doğrulandı)"
  - "https://navidrome.org/docs/usage/library/ — Tagging Guidelines · Missing Files · .ndignore (2026-10-16 doğrulandı)"
validation:
  - "İsim v2 §2.15 ile birebir karşılaştırıldı — 2026-10-12"
  - "8/8 web kaynağı 2026-10-16'da HTTP 200 ile doğrulandı (library-web-arastirma.md §1)"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Koleksiyon verisinin kalıcılık sahibi (K007 mi K015 mi) → ⚠️ VERIFICATION REQUIRED"
  - "Çoklu kitaplık (multi-library) desteği → ⚠️ VERIFICATION REQUIRED (library-web-arastirma W-1)"
  - "Listeleme modeli: etiket-based (ID3) mi dosya-yapısı-based mi → ⚠️ VERIFICATION REQUIRED (W-2)"
  - "Tarama motoru yerel process mi servis (K012) mi → ⚠️ VERIFICATION REQUIRED (W-3)"
group: mimari
---


# library — K015-media (kademe-2 · subdomain)

**ELI15:** Şarkılardan oluşan kitaplığın kendisi: ne var, ne çalınabilir.

**Sınır (ne DEĞİL):** Bilgi (metadata) ayrı çocukta · kalıcı şema K007-data · DSP K003-audio.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler media-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `library-kapsam.md` | Kapsam içi/dışı · durum makinesi · veri akışı · hata durumları | Kademe-2 |
| kanit | `library-web-arastirma.md` | 8 canlı doğrulanmış kaynak (kitaplık modelleri, tarama, eksik dosya) | Kademe-2 |
| kod | `library-ornekler.md` | Repository arayüzü + PDO iskeleti + SQL eskizi (K007) + JS liste | Kademe-2 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | Koleksiyon verisinin kalıcılık sahibi (K007 mi K015 mi) → ⚠️ VERIFICATION REQUIRED | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |
| 4 | Çoklu kitaplık desteği → ⚠️ VERIFICATION REQUIRED | FM open_questions (W-1) |
| 5 | Listeleme modeli (ID3 vs dosya-yapısı) → ⚠️ VERIFICATION REQUIRED | FM open_questions (W-2) |
| 6 | Tarama motoru sahibi (yerel vs K012) → ⚠️ VERIFICATION REQUIRED | FM open_questions (W-3) |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
