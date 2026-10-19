---
node_id: "K008-discovery-web-arastirma"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-domain/web-arastirma"
evidence:
  - "9 URL — keşif/arama/bilgi erişimi kaynakları (2026-10-15 tarama, hepsi HTTP 200)"
  - "k008-child-urls-ok.txt — havuz satır 19, 20, 35, 37, 44, 47, 48, 61, 68"
validation: "2026-10-15 — 9 benzersiz URL; K008 geneli çakışma 0 (C4)"
open_questions:
  - "Spotify referans uçları bağlam sahipliği çelişkisini (master §6 C2) çözmez — yalnız arayüz modeli verir"
group: kanit
---

# discovery — Web Araştırması (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> 9 URL **yalnız bu dosyada** (C4). Doğrulama: **2026-10-15 (HTTP GET, hepsi 200)**.
> URL'ler havuzdan byte-exact kopyalanmıştır (mojibake korunmuştur).

## §1 YENİ TARAMA (W-1…W-9 · 2026-10-15)

| # | URL | Ne için (bu dosyanın iddiası) | Durum |
|---|-----|-------------------------------|-------|
| W-1 | https://developer.spotify.com/documentation/web-api/reference/get-categories | Kategori ağacı arayüzü (keşif yüzeyi referansı) | HTTP 200 |
| W-2 | https://developer.spotify.com/documentation/web-api/reference/get-featured-playlists | Öne çıkan içerik vitrini (radar benzeri) | HTTP 200 |
| W-3 | https://en.wikipedia.org/wiki/faceted_search | Açımlı (faceted) arama modeli — search_type karşılığı | HTTP 200 |
| W-4 | https://en.wikipedia.org/wiki/filter_bubble | Keşif daralması riski (trend/radar uyarısı) | HTTP 200 |
| W-5 | https://en.wikipedia.org/wiki/long_tail | Uzun kuyruk katalog erişimi | HTTP 200 |
| W-6 | https://en.wikipedia.org/wiki/music_genre | Tür (genre) kavramı — arama tipi bağlamı | HTTP 200 |
| W-7 | https://en.wikipedia.org/wiki/music_information_retrieval | Müzik bilgi erişimi (MIR) — arama/benzerlik bilimi | HTTP 200 |
| W-8 | https://ismir.net | MIR topluluğu (ISMIR) — akademik kaynak | HTTP 200 |
| W-9 | https://musicbrainz.org/doc/musicbrainz_apı/json | MusicBrainz JSON API — katalog sorgu referansı | HTTP 200 |

## §2 DAĞITIM (C4 KAYDI)

| Alıcı dosya | Adet | Havuz satırları |
|---|---|---|
| bu dosya (discovery) | 9 | 19, 20, 35, 37, 44, 47, 48, 61, 68 |

- K008 genelinde aynı URL iki dosyada **bulunmaz** (C4) — 2026-10-15 sayım: 0 çakışma.
- Kardeş `discovery-dogrulama` bu URL'lere **pointer ile** geçer (`§1 W-x`).

## §3 KAPSAM DIŞI

| Konu | Nereye gitti |
|---|---|
| Öneri algoritmaları (collaborative, cold start, Netflix Prize) | [[recommendation-web-arastirma]] |
| Koleksiyon/favori (saved tracks, folksonomy) | [[library-web-arastirma]] |
| Çalma listesi formatları (JSPF, XSPF, PLS) | [[playlist-web-arastirma]] |
| DSP akademik (AES ısrc) | [[music-web-arastirma]] |

**Kardeş dosyalar:** [[discovery-kapsam]] · [[discovery-dogrulama]] · [[discovery-ornekler]] · [[discovery-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
