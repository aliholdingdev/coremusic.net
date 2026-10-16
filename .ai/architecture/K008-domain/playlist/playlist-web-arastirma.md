---
node_id: "K008-playlist-web-arastirma"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-domain/web-arastirma"
evidence:
  - "9 URL — HLS/JSPF/PLS/XSPF playlist format kaynakları (2026-10-15 tarama, hepsi HTTP 200)"
  - "k008-child-urls-ok.txt — havuz satır 11, 13, 14, 42, 52, 60, 83, 87, 89"
validation: "2026-10-15 — 9 benzersiz URL; kök + kardeş çakışması 0 (C4 taraması)"
open_questions:
  - "OQ-P1: W-P5 (jspf) sayfasının içeriği okunmadı — JSPF şema ayrıntıları ⚠️ VERIFICATION REQUIRED"
group: kanit
---

# playlist — Web Araştırması (K008)

> **Authority: Bayram Ali / Vault Steward.** Bu dosyanın URL'leri **yalnız burada**
> görünür (C4 — kök `domain-web-arastirma.md` ile kesişim 0). Doğrulama:
> **2026-10-15 (HTTP GET, hepsi 200)**. URL'ler havuz dosyasındaki biçimiyle
> (mojibake dahil) kopyalanmıştır — yeniden yazım yasak.

## §1 YENİ TARAMA (W-P1…W-P9 · 2026-10-15)

| # | URL | Ne için (bu dosyanın iddiası) | Havuz satır | Durum |
|---|-----|-------------------------------|-------------|-------|
| W-P1 | https://datatracker.ietf.org/doc/html/draft-pantos-hls-rfc8216bis-18 | HLS yayın (oynatma) listesi formatı — medya parça listesi referansı | 11 | HTTP 200 |
| W-P2 | https://datatracker.ietf.org/doc/html/rfc8216 | HLS RFC (EXT-X-KEY / oturum anahtarı kavramı — paylaşımlı oturum analojisi) | 13 | HTTP 200 |
| W-P3 | https://developer.apple.com/documentation/http-live-streaming | Apple HLS dokümantasyonu — canlı yayın listesi modeli | 14 | HTTP 200 |
| W-P4 | https://www.rfc-editor.org/info/rfc8216 | RFC 8216 bilgi sayfası (yayımlanma durumu) | 83 | HTTP 200 |
| W-P5 | https://en.wikipedia.org/wiki/jspf | JSPF — JSON playlist formatı (XSPF'in JSON karşılığı) | 42 | HTTP 200 |
| W-P6 | https://en.wikipedia.org/wiki/pls_(file_format) | PLS — minimal playlist dosya formatı | 52 | HTTP 200 |
| W-P7 | https://gonze.com/playlists/playlist-format-survey.html | Playlist format anketi — XSPF/PLS vb. format karşılaştırması | 60 | HTTP 200 |
| W-P8 | https://www.xspf.org/quickstart | XSPF quickstart — playlist başlangıç kılavuzu | 87 | HTTP 200 |
| W-P9 | https://xspf.org/spec | XSPF spec — standart playlist şeması (`title`, `track`, `location`) | 89 | HTTP 200 |

## §2 YORUMLAR

| # | Kaynak | Yorum | Durum |
|---|--------|-------|-------|
| 1 | W-P1 / W-P2 / W-P3 / W-P4 | HLS ailesi: yayın listesi + anahtar kavramları — canlı yayın listeleri için referans; CoreMusic on-demand'dir | uzak bağlam |
| 2 | W-P5 | JSPF = XSPF'in JSON tabanlı karşılığı (Wikipedia başlığı) — sayfa içeriği okunmadı → OQ-P1 | ⚠️ VERIFICATION REQUIRED |
| 3 | W-P6 | PLS formatı minimal (`NumberOfEntries` + `FileN` + `TitleN`) — XSPF'in karşısında sadelik örneği | Wikipedia sözlük satırı (HTTP 200) |
| 4 | W-P7 | Playlist format anketi — format seçimi tartışması için birincil karşılaştırma kaynağı | doğrulandı |
| 5 | W-P8 / W-P9 | XSPF: `playlist` ↔ `track` ↔ `location` ↔ `title` — playlist'in veri modeli standartlaştırılmıştır | doğrulandı (spec/quickstart erişilebilir) |
| 6 | — | `playlists.name` benzersizliği (D-P10) iddiası web'de doğrulanmaz → OQ-P2 benzeri repo-tek | ⚠️ VERIFICATION REQUIRED |

## §3 SINIR VE KAPSAM

- Bu dosya yalnız `playlist` alanına ait 9 URL'yi içerir (C4: aynı URL iki K008 dosyasında geçmez — `k008-c4-scan.ps1` 0 çarpışma).
- Kapsam dışı URL'ler (Spotify playlist concepts, RFC 5215, ...) → ilgili kardeş web dosyaları / kök `domain-web-arastirma.md`.
- dogrulama dosyaları bu URL'leri değil §1'deki W-P* kimliklerini işaretler (C4).

**Kardeş dosyalar:** [[index]] · [[playlist-kapsam]] · [[playlist-dogrulama]] · [[playlist-ornekler]] · [[playlist-bilesenler]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
