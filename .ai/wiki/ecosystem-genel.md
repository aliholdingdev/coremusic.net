---
title: "Ekosistem Genel — Servis, Panel ve Subdomain Haritası"
type: dokuman
created: 2026-10-06
updated: 2026-10-06
sources: [ecosystem-genel]
tags: [ekosistem, servis, panel, subdomain, event-bus]
---

# Ekosistem Genel

`.ai/raw/ecosystem/` altındaki üç genel dosyanın (`README.md`, `index.md`, `service-integration.md`) tema özeti sayfası: 7 servis / 10 panel / 11 subdomain sayımı, servis iletişim akışı (API Gateway → K7 middleware → Control → PSR-14 Event Bus), 5 entegrasyon protokolü ve 5 event tipi; ayrıca ekosistem dizininin K0-K20 çapraz referansı (19/21 katman) ve 5 sınıflı lisans kapısı.

## Dosyalar

| Dosya | İçerik/Boyut |
|-------|--------------|
| `raw/ecosystem/README.md` | Servis + panel haritası, iletişim akışı · 2.2KB · 81 satır |
| `raw/ecosystem/index.md` | Ekosistem indeksi: envanter, K çapraz referans, lisans kapısı, entegrasyon/risk · 33KB · 507 satır |
| `raw/ecosystem/service-integration.md` | 5 protokol + 5 event tipi tablosu · 1KB · 38 satır |

## Ana Bulgular

1. 7 servis: IMPLEMENTED = Control (81, PHP 8.4), Media (5000/6000, PHP+FFmpeg), Download (3001, Node.js+TS); PLANNED = Audio (9741/9742, C++20 JUCE), Device, Network Audio, AI.
2. 10 panel alt alan adı (coremusic.net + music/admin/download/media/auth/home/car/studio/pro) + akış şemasındaki `api.coremusic.net` → README'nin "11 subdomain" sayısı iki tablodan **çıkarımla** türetilir.
3. Akış: Client → API Gateway → Middleware (K7) → Control Service (K8-1) → PSR-14 Event Bus → Media/Audio/Device/AI/Download (K8-2…K8-7) → MySQL 18 DB (K5) → Redis (K5) → Response.
4. Protokoller: HTTP/REST · WebSocket (Audio, Download) · Event Bus (PSR-14) · gRPC (Audio → Device) · IPC; event'ler: `user.login`, `track.play`, `download.complete`, `device.connect`, `eq.preset.change`.
5. index.md: 8 dosyalık envanter, 6 konu kategorisi, §3.7 on somut giriş (her satır katman dosyasına ya ADR'ye, yoksa "BLOKE"), authority: reference.
6. ⚠ Katalog drift: `README.md:33` Download `IMPLEMENTED` · ADR-039 `PLANNED` → `⚠ VERIFICATION REQUIRED`.
7. ⚠ Otorite çelişkisi: README `authority: SSOT` · index.md "SSOT iddiası yok" → `.ai/CLAUDE.md` §2.1 kazanır.

## İlgili Sayfalar

- [[ecosystem-mimarileri]] — servis/pipeline mimarisi dersleri
- [[ecosystem-audio-dsp]] · [[ecosystem-donanim]] — DSP ve donanım tarafı
- [[coremusic-platform]] — platform çatısı · [[arch-katman]] — K0-K20 katmanları
- [[vault-workflow]] — ingest/güncelleme süreci
