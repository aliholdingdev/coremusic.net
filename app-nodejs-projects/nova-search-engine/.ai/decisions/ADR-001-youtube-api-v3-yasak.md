# ADR-001 — YouTube Data API v3 YASAK

- **Durum:** Kabul
- **Tarih:** 2026-10-16
- **Alan:** Veri toplama

## Bağlam

Sistem YouTube metadata toplar. İki yol vardır: resmi Data API v3 veya Innertube
(iç API) üzerinden `youtubei.js`.

## Karar

**YouTube Data API v3 KULLANILMAZ.** Tek kaynak `youtubei.js` 18.x.

## Gerekçe

- Data API v3: 10.000 birim/gün kota — sürekli toplama için yetersiz
- `youtubei.js`: kotasız, ücretsiz, doğrudan erişim
- Proje yerel/kişisel kullanım içindir; toplama insansı ritimle (jitter+backoff)
  ve cache-first ile minimum düzeyde tutulur

## Sonuçlar

- `packages/youtube` tek sağlayıcı katmanıdır; yarın Spotify/Vimeo eklenirse
  `packages/providers/` altında yeni adapter yazılır, çekirdek değişmez
- Sorumlu toplama kuralları (token bucket, jitter, backoff) zorunludur
