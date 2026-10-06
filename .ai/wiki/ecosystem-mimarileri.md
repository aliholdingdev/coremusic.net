---
title: "Ekosistem Mimarileri — Streaming Devi ve Sunucu Dersleri"
type: dokuman
created: 2026-10-06
updated: 2026-10-06
sources: [ecosystem-mimarileri]
tags: [ekosistem, streaming, mikroservis, transcode, lisans]
---

# Ekosistem Mimarileri

İki ham dosyanın (`ekosistem-mimarileri.md` — Spotify/Apple Music/YouTube Music mimari dersleri, `muzik-streaming-sunuculari.md` — Koel/Ampache/Mopidy dersleri) tema özeti: servis sınırları, upload→transcode→distribution pipeline, oturum/telemetri modeli ve açık kaynak sunucu karşılaştırması; hepsi "mimari ders alınır — kod kopyalanmaz" ilkesiyle K8/K9/K14/K15'e bağlanır.

## Dosyalar

| Dosya | İçerik/Boyut |
|-------|--------------|
| `raw/ecosystem/ekosistem-mimarileri.md` | Spotify 5 servis, transcode pipeline, Apple/YouTube dersleri, K14 telemetri · 28KB · 504 satır |
| `raw/ecosystem/muzik-streaming-sunuculari.md` | Koel/Ampache/Mopidy dersleri, smart_query, Subsonic uçları, lisans tablosu · 31KB · 514 satır |

## Ana Bulgular

1. Spotify 5 servis dersi (account, upload, search, playlist, user; database-per-service) → K8 beş servis iskeleti + §5.2 kontrat taslağı.
2. Transcode pipeline dersi (asenkron kuyruk, çoklu-bitrate, idempotent hash-keyed job, gözlemlenebilirlik) → K9; K14 eşikleri: `queue_depth` < 100 · `transcode_duration_p95` < 60s · `upload_error_rate` < 1% · `serve_latency_p95` < 200ms.
3. Apple (tek oturum-çok cihaz, kütüphane senkronu, handoff) + YouTube (öneri, adaptif varyant) → K15/K9; CoreMusic server+client ikili rolünde token tek kaynaktan (K8 account), loopback'te `region=selfhost` etiketi.
4. Sunucu karşılaştırması: Koel 17.175★ MIT (arayüz lideri, akıllı playlist) · Ampache 3.809★ AGPL-3.0 (özellik lideri, Subsonic API) · Mopidy (Python, çekirdek/uzantı) — **Mopidy lisansı ⚠ VERIFICATION REQUIRED**.
5. Kırmızı çizgi: Ampache AGPL kodu asla kopyalanmaz (kod/dosya/SQL/sınıf) — yalnız mimari fikir; Koel MIT referans, atıfla.
6. smart_query 8 alanlık kural modeli (rule_group, rule_type, operator, value, ordering, limit, dynamic, owner_scope) → K8/K9/K5; `dynamic=1` için APCu cache 60s.
7. Öncelik: P0 = transcode+stream (K15) ve servis sınırı+Event Bus (K8) · P1 = smart_query + Subsonic uç · P2 = katalog tarama + metadata adaptörü · P3 = arayüz dersi (mockup gate sonrası).
8. ⚠ Yıldız drift: exa 2026-09-24 → 17.175★/3.809★; github-referanslari (2026-09-20) → 17.195★/3.793★ — iki tarihli kayıt, çelişki değil.

## İlgili Sayfalar

- [[ecosystem-genel]] — servis/panel/event haritası
- [[ecosystem-audio-dsp]] · [[ecosystem-donanim]] — DSP ve donanım dersleri
- [[neva-engine]] — K3 ses motoru · [[mysql-9]] — K5 veri katmanı
- [[prompt-api]] — API üretim akışı
