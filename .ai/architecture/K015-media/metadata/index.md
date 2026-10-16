---
node_id: K015-media/metadata
node_type: subdomain
status: draft
parent_id: K015-media
children: []
depends_on: []
evidence:
  - "v2 §2.15 — çocuk adı kaynağı (birebir)"
  - "https://id3.org/id3v2.3.0 — ID3v2.3 resmi spesifikasyonu (TIT2/TPE1/TALB/TYER/TCON/TRCK, 2026-10-16 doğrulandı)"
  - "https://xiph.org/vorbis/doc/v-comment.html — VorbisComment resmi dokümanı (TITLE/ARTIST/ALBUM, UTF-8, 2026-10-16 doğrulandı)"
validation:
  - "İsim v2 §2.15 ile birebir karşılaştırıldı — 2026-10-12"
  - "8/8 web kaynağı 2026-10-16'da HTTP 200 ile doğrulandı (metadata-web-arastirma.md §1)"
open_questions:
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C)"
  - "Bilginin kaynağı (dış servis mi yerel mi) → ⚠️ VERIFICATION REQUIRED (§7/8)"
  - "Desteklenecek etiket kapsayıcıları (ID3 mi VorbisComment mi MP4 mü) → ⚠️ VERIFICATION REQUIRED (web-arastirma W-1)"
  - "Etiket okuma: hazır PHP kütüphanesi mi yerel reader mı → ⚠️ VERIFICATION REQUIRED (W-2)"
  - "ID3v2.3 mü 2.4 mü öncelikli → ⚠️ UNKNOWN (W-3)"
group: mimari
---


# metadata — K015-media (kademe-2 · subdomain)

**ELI15:** Şarkı bilgileri: sanatçı, albüm, süre, tür vb. açıklama verisi.

**Sınır (ne DEĞİL):** Dosya/akış K015/streaming · dış kaynak K013-integraion · şema K007-data.

**Bağımlılık (özet):** Düğümün tamamı üst kademeye bağlıdır; ayrıntı ve etiketler media-bagimliliklar.md §1'dedir. Bu dosyadaki satırlar TASARIM İDDİASI'dır.

**Durum:** draft · kademe-3 (bounded-context) ve kademe-4 genişletmesi ADR-097 K4 fazı (C) bekliyor · module'e inilmez.


## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `metadata-kapsam.md` | Kapsam içi/dışı · durum makinesi · veri akışı · hata durumları | Kademe-2 |
| kanit | `metadata-web-arastirma.md` | 8 canlı doğrulanmış kaynak (ID3v2, VorbisComment, getID3, Picard, Mutagen) | Kademe-2 |
| kod | `metadata-ornekler.md` | MetadataReader + ID3 iskeleti + SQL eskizi (K007) + fallback/JS | Kademe-2 |

## §A Kapilar (bu index)

| # | Kapilar | Durum |
|---|---|---|
| 1 | Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C) | FM open_questions |
| 2 | Bilginin kaynağı (dış servis mi yerel mi) → ⚠️ VERIFICATION REQUIRED (§7/8) | FM open_questions |
| 3 | Kademe-3/4 genisletmesi | ADR-097 K4 faz (C) bekliyor |
| 4 | Etiket kapsayıcı seti → ⚠️ VERIFICATION REQUIRED | FM open_questions (W-1) |
| 5 | Okuma stratejisi (kütüphane mi yerel reader mı) → ⚠️ VERIFICATION REQUIRED | FM open_questions (W-2) |
| 6 | ID3v2.3 mü 2.4 mü → ⚠️ UNKNOWN | FM open_questions (W-3) |

> Bu tablo FM `open_questions` alaniyi index`e gorunur kilir; yeni iddia eklemez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
