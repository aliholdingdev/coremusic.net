---
node_id: "K008-library-web-arastirma"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-domain/web-arastirma"
evidence:
  - "8 URL — koleksiyon/favori/kütüphane kaynakları (2026-10-15 tarama, hepsi HTTP 200)"
  - "k008-child-urls-ok.txt — havuz satır 21, 22, 32, 33, 36, 39, 43, 55"
validation: "2026-10-15 — 8 benzersiz URL; K008 geneli çakışma 0 (C4)"
open_questions:
  - "folksonomy → kullanıcı-etiketli koleksiyon eşlemesi tasarım yorumudur; CoreMusic'te kullanıcı etiketi tablosu → UNKNOWN"
group: kanit
---

# library — Web Araştırması (K008)

> **Authority: Bayram Ali / Vault Steward.** 8 URL **yalnız bu dosyada** (C4).
> Doğrulama: **2026-10-15 (HTTP GET, hepsi 200)**.

## §1 YENİ TARAMA (L-1…L-8 · 2026-10-15)

| # | URL | Ne için | Durum |
|---|-----|---------|-------|
| L-1 | https://developer.spotify.com/documentation/web-api/reference/get-users-saved-albums | "kaydedilmiş albüm" servisi modeli (referans) | HTTP 200 |
| L-2 | https://developer.spotify.com/documentation/web-api/reference/get-users-saved-tracks | "kaydedilmiş parça" servisi modeli (referans) | HTTP 200 |
| L-3 | https://en.wikipedia.org/wiki/collection | koleksiyon kavramı (sözlük) | HTTP 200 |
| L-4 | https://en.wikipedia.org/wiki/digital_library | dijital kütüphane kavramı (sözlük) | HTTP 200 |
| L-5 | https://en.wikipedia.org/wiki/favourite | favori kavramı (sözlük) | HTTP 200 |
| L-6 | https://en.wikipedia.org/wiki/folksonomy | kullanıcı-etiketli sınıflandırma (koleksiyon dili — ⚠️ tasarım eşlemesi) | HTTP 200 |
| L-7 | https://en.wikipedia.org/wiki/library_(computing) | kütüphane (bilgi işlem) kavramı | HTTP 200 |
| L-8 | https://en.wikipedia.org/wiki/star_rating | yıldız puanlama — geri bildirim dili (`recommendation_history.feedback` ile komşu) | HTTP 200 |

## §2 DAĞITIM (C4 KAYDI)

| Kontrol | Sonuç |
|---|---|
| Benzersiz URL | 8 (L-1…L-8) |
| K008 geneli çakışma | 0 (2026-10-15 `POOL-COLLISION: 0`) |

## §3 KAPSAM DIŞI

| Dışarıda | Nereye |
|---|---|
| ReplayGain/loudness | [[playback-web-arastirma]] (ayrı havuz) |
| Öneri geri bildirimi (`feedback` enum) | `recommendation` çocuğu |
| Favori **uygulama** kodu | repo'da zaten YOK ([[library-dogrulama]] D-L6) |

**Kardeş dosyalar:** [[library-kapsam]] · [[library-dogrulama]] · [[library-ornekler]] · [[library-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
