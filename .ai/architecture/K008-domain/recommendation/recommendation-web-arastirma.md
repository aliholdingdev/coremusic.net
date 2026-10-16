---
node_id: "K008-recommendation-web-arastirma"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-domain/web-arastirma"
evidence:
  - "9 URL — öneri sistemleri bilimsel/teknik kaynakları (2026-10-15 tarama, hepsi HTTP 200)"
  - "k008-child-urls-ok.txt — havuz satır 18, 25, 30, 31, 46, 49, 53, 65, 81"
validation: "2026-10-15 — 9 benzersiz URL; K008 geneli çakışma 0 (C4)"
open_questions:
  - "AIEngine'de matris faktörizasyonu YOK (yalnız işbirlikçi + içerik tabanlı) — W-5 hedef model olarak duruyor"
group: kanit
---

# recommendation — Web Araştırması (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> 9 URL **yalnız bu dosyada** (C4). Doğrulama: **2026-10-15 (HTTP GET, hepsi 200)**.
> URL'ler havuzdan byte-exact kopyalanmıştır (mojibake korunmuştur).

## §1 YENİ TARAMA (W-1…W-9 · 2026-10-15)

| # | URL | Ne için (bu dosyanın iddiası) | Durum |
|---|-----|-------------------------------|-------|
| W-1 | https://developer.spotify.com/documentation/web-api/reference/get-audio-features | Özellik uzayı referansı — içerik tabanlı skorun girdi modeli | HTTP 200 |
| W-2 | https://doi.org/10.1016/j.cosrev.2024.100618 | Müzik öneri sistemleri akademik incelemesi | HTTP 200 |
| W-3 | https://en.wikipedia.org/wiki/cold_start | Soğuk başlangıç riski — yeni kullanıcı/şarkı sorunu | HTTP 200 |
| W-4 | https://en.wikipedia.org/wiki/collaborative_filtering | İşbirlikçi filtreleme — AIEngine adım 1'inin temeli (satır 58–59) | HTTP 200 |
| W-5 | https://en.wikipedia.org/wiki/matrix_factorization_(recommender_systems) | Matris faktörizasyonu — AIEngine'de henüz YOK (hedef model) | HTTP 200 |
| W-6 | https://en.wikipedia.org/wiki/netflix_prize | İşbirlikçi filtrelemenin tarihsel bağlamı | HTTP 200 |
| W-7 | https://en.wikipedia.org/wiki/recommender_system | Öneri sistemleri genel çerçevesi | HTTP 200 |
| W-8 | https://link.springer.com/article/10.1007/s13735-018-0154-2 | Müzik öneri sistemleri dergi makalesi | HTTP 200 |
| W-9 | https://www.jku.at/fileadmin/gruppen/173/research/a_survey_of_music_similarity_and_recommendation.pdf | Müzik benzerlik + öneri anketi (benzerlik–öneri sınırı için) | HTTP 200 |

## §2 DAĞITIM (C4 KAYDI)

| Alıcı dosya | Adet | Havuz satırları |
|---|---|---|
| bu dosya (recommendation) | 9 | 18, 25, 30, 31, 46, 49, 53, 65, 81 |

- K008 genelinde aynı URL iki dosyada **bulunmaz** (C4) — 2026-10-15 sayım: 0 çakışma.
- Kardeş `recommendation-dogrulama` bu URL'lere **pointer ile** geçer (`§1 W-x`).

## §3 KAPSAM DIŞI

| Konu | Nereye gitti |
|---|---|
| Arama/keşif ölçümü (search_logs, radar) | [[discovery-web-arastirma]] |
| Benzerlik çiftleri şeması (`music_similar`) | [[music-web-arastirma]] |
| Çalma listesi formatları (JSPF, XSPF) | [[playlist-web-arastirma]] |
| Koleksiyon/favori (saved tracks, folksonomy) | [[library-web-arastirma]] |

**Kardeş dosyalar:** [[recommendation-kapsam]] · [[recommendation-dogrulama]] · [[recommendation-ornekler]] · [[recommendation-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
