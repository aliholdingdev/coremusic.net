---
title: "Kaynak: Ekosistem Mimarileri + Müzik Streaming Sunucuları"
type: kaynak
raw_path: raw/ecosystem/
created: 2026-10-06
updated: 2026-10-06
sources: [ecosystem-mimarileri]
tags: [ekosistem, streaming, mikroservis, transcode, koel, ampache, mopidy, lisans]
---

# Kaynak Özeti — Ekosistem Mimarileri + Müzik Streaming Sunucuları

## Genel Özet

İki ham dosya: `ekosistem-mimarileri.md` (seri 5/6 — Spotify / Apple Music / YouTube Music'in yayınlanmış mimari dersleri: servis ayrımı, upload→transcode→distribution pipeline, oturum/teslimat modeli; hedef K8/K9/K14/K15) ve `muzik-streaming-sunuculari.md` (seri 2/6 — Koel, Ampache, Mopidy dersleri; hedef K8/K9/K15, ikincil K4/K5/K11/K12). İkisi de exa 2026-09-24 doğrulamasına dayanır, "mimari ders alınır — kod kopyalanmaz" ilkesiyle yazılır.

## Ana Fikirler

1. **Spotify 5 servis dersi (K8):** account, upload, search, playlist, user — database-per-service; tek monolit yerine küçük, tek sorumlu servisler → bağımsız ölçeklenme + failure isolation. CoreMusic karşılığı: K8'de beş servis iskeleti (§5.2 kontrat taslağı).
2. **Spotify transcode pipeline (K9):** ham yükleme → kuyruk → per-format/bitrate dönüştürme → store → CDN/serve; 4 yapı dersi: asenkron kuyruk, çoklu-bitrate çıktısı, idempotency (hash-keyed job), gözlemlenebilirlik.
3. **Apple Music dersleri (K15/K8/K9):** Apple ID tek oturum-çok cihaz, kütüphane senkronu, handoff, FairPlay DRM (CoreMusic gerekmezse atlanır).
4. **YouTube Music dersleri (K8/K9/K15):** mevcut platform üzerine servis kurma, recommendation pipeline (opsiyonel), adaptif varyant teslimat, Google hesabı oturumu.
5. **Server + client ikili rol (K8↔K15) üç çıkarım:** token yalnız K8 account servisinden üretilir (iki kaynaklı token yasak) · loopback trafiği (K14'te `region=selfhost` etiketi) · çift yönlü kütüphane senkronu (son-yazan-kazanır + Δ senkron).
6. **K14 telemetri sözleşmesi:** `queue_depth` < 100 iş · `transcode_duration_p95` < 60s · `upload_error_rate` < 1% · `serve_latency_p95` < 200ms · `active_sessions` (trend); etiket şeması `{service, version, region}`.
7. **Sunucu karşılaştırması:** Koel 17.175★ (Laravel+Vue, MIT, arayüz lideri) · Ampache 3.809★ (PHP, AGPL-3.0, özellik lideri, Subsonic API, 2001'den beri) · Mopidy (Python, MPD + eklenti mimarisi, **lisans ⚠ VERIFICATION REQUIRED**).
8. **Ders sayıları:** Koel 5 ders (K-1…K-5), Ampache 5 ders (A-1…A-5), Mopidy 4 ders (M-1…M-4); smart_query alan modeli 8 alan (rule_group, rule_type, operator, value, ordering, limit, dynamic, owner_scope), `dynamic=1` için APCu cache 60s önerilir.
9. **Ampache transcode zinciri 7 adım** ve Subsonic uyum hedefi 7 uç (ping, getAlbumList2, search3, stream, getPlaylist/createPlaylist, scrobble, getCoverArt) — "uyum hedefi, kopya değil"; kendi implementasyonumuz.
10. **Öncelik sırası (muzik §3.9):** P0 transcode + stream ucu (K15) ve servis sınırı + Event Bus (K8) · P1 smart_query + Subsonic uç · P2 katalog tarama batch + metadata adaptörü · P3 arayüz dersi (mockup gate sonrası).

## Önemli Alıntılar/Veriler

- **Lisans kırmızı çizgisi:** Ampache AGPL-3.0 kodu CoreMusic'e asla kopyalanmaz (kod, dosya, SQL, sınıf) — yalnız mimari fikir; Koel MIT referans için uygun (atıfla); Mopidy lisansı doğrulanana kadar kod yok (ekosistem-mimarileri §3.6.1 "mor satır").
- **Yıldız drift notu:** exa 2026-09-24 → Koel 17.175★ / Ampache 3.809★; `github-referanslari.md` (2026-09-20) → 17.195★ / 3.793★ — iki kayıt da tarihli, çakışma değil (star drift).
- Ham dosyalarda geçen vault karar referansları: ADR-001 (Vanilla JS — framework yasak), ADR-084 (API-First / OpenAPI), ADR-086 (Event Bus) — bu ingest'te ADR metinleri okunmadı, yalnızca ham dosya atıflarıdır.
- Koel akıllı playlist dersi → `smart_query` sözleşmesi K8/K9/K5; Ampache "20 yıl şema geçmişi" dersi → `coremusic_patch` migration disiplini (K5).
- Mopidy dersi → çekirdek/uzantı ayrımı: servis çekirdeği sabit, protokol yüzeyleri (REST/WS/Subsonic) adaptör; oynatma durumu tek publisher (Audio Service) + `track.play` event'i.
- muzik §5.4 uç/event taslağı 8 satır (GET /v1/playlists/{id}/smart · POST /v1/playlists/smart · GET /rest/{action}.view · GET /v1/stream/{trackId} · WS track.play · Event download.complete · Event library.scan.done · GET /v1/search?q=); event adları `service-integration` tablosuyla uyumlu tutulur.
- Riskler (muzik §3.10, 7 madde; mimarileri §6.2, 6 madde): AGPL sızması, lisanssız Mopidy, senkron yazılan pipeline, iki kaynaklı token, K14 etiket tutarsızlığı, ADR-001 ihlali (Vue kopyası), K15→K14 layer violation.
- Kapsam dışı: gerçek codec implementasyonu, sunucu kurulumu, Spotify/Apple/YouTube iç kodu (kapalı kaynak — yalnız yayınlanan mimari ders).

## Bu Kaynaktan Oluşan/Güncellenen Wiki Sayfaları

[[ecosystem-mimarileri]] (yeni)
