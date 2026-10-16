---
node_id: "K008-music-web-arastirma"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-domain/web-arastirma"
evidence:
  - "8 URL — ISRC/ISWC/CISAC/MusicBrainz kimlik belgeleri (2026-10-15 tarama, hepsi HTTP 200)"
  - "k008-child-urls-ok.txt — havuz satır 40, 41, 62, 63, 67, 77, 79, 80"
validation: "2026-10-15 — 8 benzersiz URL; kök havuzla çakışma 0 (C4 taraması: POOL-COLLISION 0)"
open_questions:
  - "Katalog kimlik alanları (ISRC/ISWC) CoreMusic şemasında tanımlı mı → `.ai/.sql/mysql/` ISRC sütunu görülmedi → UNKNOWN"
group: kanit
---

# music — Web Araştırması (K008)

> **Authority: Bayram Ali / Vault Steward.** Bu dosyanın URL'leri **yalnız burada**
> görünür (C4 — kök `domain-web-arastirma.md` ile kesişim 0). Doğrulama tarihi:
> **2026-10-15 (HTTP GET, hepsi 200)**. URL'ler havuz dosyasındaki biçimiyle
> (mojibake dahil) kopyalanmıştır — yeniden yazım yasak.

## §1 YENİ TARAMA (W-1…W-8 · 2026-10-15)

| # | URL | Ne için (bu dosyanın iddiası) | Durum |
|---|-----|-------------------------------|-------|
| W-1 | https://en.wikipedia.org/wiki/ınternational_standard_music_number | ISWC — müzik **eseri** kimliği kavramı (sözlük satırı) | HTTP 200 |
| W-2 | https://en.wikipedia.org/wiki/ınternational_standard_recording_code | ISRC — **kayıt** kimliği kavramı (sözlük satırı) | HTTP 200 |
| W-3 | https://isrc.ifpi.org/images/downloads/ısrc_handbook.pdf | IFPI resmî ISRC kılavuzu (PDF) | HTTP 200 |
| W-4 | https://isrc.ifpi.org/isrc-standard | IFPI ISRC standard sayfası | HTTP 200 |
| W-5 | https://musicbrainz.org/doc/ısrc | MusicBrainz ISRC dokümanı — katalog eşleme referansı | HTTP 200 |
| W-6 | https://www.cisac.org/services/information-services/international-identifiers | CISAC uluslararası tanımlayıcılar (eser/üye kimlikleri) | HTTP 200 |
| W-7 | https://www.iswc.org/iswc | ISWC standard sayfası | HTTP 200 |
| W-8 | https://www.iswc.org/iswc-usage | ISWC kullanım kılavuzu | HTTP 200 |

## §2 DAĞITIM (C4 KAYDI)

| Kontrol | Sonuç |
|---|---|
| Bu dosyada benzersiz URL | 8 (W-1…W-8) |
| Kök `domain-web-arastirma.md` (75 URL) ile kesişim | 0 (2026-10-15 `k008-c4-scan.ps1` → `POOL-COLLISION: 0`) |
| Kardeş çocuk web dosyaları ile kesişim | 0 (aynı havuz taraması — tüm K008 .md) |

## §3 KAPSAM DIŞI

| Dışarıda | Nereye |
|---|---|
| Genel DDD/olay/PSR-14 kaynakları | [[domain-web-arastirma]] (kök) |
| Spotify katalog uç referansları | `music/music-dogrulama.md` §1 (satır 23–24 — URL'ler orada tekildir) |
| Şema/normalizasyon | K007-data (data katmanı) |

**Kardeş dosyalar:** [[music-kapsam]] · [[music-dogrulama]] · [[music-ornekler]] · [[music-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
