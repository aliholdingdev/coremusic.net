---
title: "Kaynak: Ekosistem Genel Harita (README · index · service-integration)"
type: kaynak
raw_path: raw/ecosystem/
created: 2026-10-06
updated: 2026-10-06
sources: [ecosystem-genel]
tags: [ekosistem, servis-haritasi, panel, subdomain, event-bus, entegrasyon]
---

# Kaynak Özeti — Ekosistem Genel Harita (README.md · index.md · service-integration.md)

## Genel Özet

`.ai/raw/ecosystem/` altındaki üç genel dosya: `README.md` (7 servis / 10 panel / 11 subdomain sayım tabloları + servis iletişim akışı), `index.md` (ekosistem dizininin ana indeksi — dosya envanteri, K0-K20 çapraz referans, lisans kapısı, entegrasyon/risk matrisleri) ve `service-integration.md` (entegrasyon protokolleri + 5 event tipi). README ve service-integration.md 2026-09-29, index.md exa 2026-09-24 doğrulaması tarihlidir; index.md kendisini `authority: reference` ilan eder, SSOT iddiası taşımaz.

## Ana Fikirler

1. README sayım özeti: **7 servis · 10 panel · 11 subdomain · 18 BCNF veritabanı**.
2. Servis durumları: IMPLEMENTED = Control (port 81, PHP 8.4), Media (5000/6000, PHP + FFmpeg), Download (3001, Node.js + TS); PLANNED = Audio (9741/9742, C++20 JUCE), Device (C++20), Network Audio (C++20), AI (PHP + Python).
3. Servis iletişim akışı (README §3): Client → API Gateway (`api.coremusic.net`) → Middleware Pipeline (K7) → Control Service (K8-1) → Event Bus (PSR-14) → Media / Audio / Device / AI / Download (K8-2 … K8-7) → MySQL 18 DB (K5) → Redis Cache (K5) → Response.
4. service-integration.md: 5 protokol — HTTP/REST (senkron, tüm servisler) · WebSocket (real-time, Audio + Download) · Event Bus (async, PSR-14) · gRPC (high-perf, Audio → Device) · IPC (native servisler).
5. service-integration.md 5 event tipi: `user.login` (Control → Media, AI) · `track.play` (Audio → AI, Logs) · `download.complete` (Download → Media) · `device.connect` (Device → Audio, Media) · `eq.preset.change` (Audio → Media).
6. index.md envanteri 8 dosya (2 mevcut + 6 ders dosyası) ve 6 konu kategorisi; §3.4'te 21 satırlık K0-K20 çapraz referans — **K7 ve K13'e ekosistem dersi atanmamıştır** (§3.4 notu; §6.2: 19/21 K kapsanıyor).
7. index.md §4.4 lisans kapısı 5 sınıf: MIT/ISC/BSD/Zlib ✅ · GPL v2/v3 ⚠ (kod linklemesi ADR) · AGPL-3.0 ❌ (kod asla) · LGPL ⚠ (linkleme moduna bağlı) · Steinberg ASIO SDK açık kaynak varyantı ✅ (LICENSE + atıf şartıyla).
8. index.md §3.7 on somut giriş satırı (sahipler: backend, embedded, win-sw, audio-hw, ui, devops); kural: her satır katman dosyasına işlenir ya da ADR'ye taşınır — ikisi de olmazsa satır "BLOKE" + CLAUDE.md §7 Contradiction Gate.
9. Tüm sayısal veri exa web doğrulaması 2026-09-24 tarihlidir; bu ingest'te yeni web araştırması yapılmamıştır, yıldız/sürüm değerleri zamanla değişir.

## Önemli Alıntılar/Veriler

- README servis tablosu: Control 81 · Media 5000/6000 · Audio 9741/9742 · Download 3001 · Device / Network Audio / AI port yok ("—").
- README panel tablosu (10 satır, hepsi ✅): `coremusic.net` (Landing) · `music.` · `admin.` · `download.` · `media.` · `auth.` · `home.` · `car.` · `studio.` · `pro.` + `.coremusic.net`.
- **11 subdomain sayısı:** README "Toplam Subdomain: 11" der; panel tablosu yalnız 10 alt alan adı sayar, 11. ad `api.coremusic.net` README §3 akış şemasında geçer → bu eşleme **çıkarımdır** (README'de 11 satırlık subdomain tablosu yok).
- **⚠ KATALOG DRIFT:** `raw/ecosystem/README.md:33` Download Service'i `IMPLEMENTED` yazar; **ADR-039 `PLANNED` der** — bu not görev talimatından gelir, ADR metni bu ingest kapsamında okunmadı → `⚠ VERIFICATION REQUIRED`.
- **⚠ Otorite iddiası çelişkisi:** `README.md` frontmatter'i `authority: Single Source of Truth (SSOT)` taşır; `index.md` §4.2 kural 7 "bu dizin SSOT değildir; authority: reference" der → iki raw dosyası arasında otorite çelişkisi; SSOT sırası `.ai/CLAUDE.md` §2.1'dedir.
- index.md §3.6 doğrulanmış kaynak envanteri (exa 2026-09-24): Koel 17.175★ · Ampache 3.809★ · JUCE 8.811★ · Awesome-Audio-DSP 1.289★ · YUP 148★ (ISC).
- index.md §6.2 metrikleri: 8 dosya · 6 yeni ana doküman (her biri ≥500 satır kuralı) · 19/21 K katmanı · 5 lisans sınıfı · silinen dosya 0.
- index.md §3.8 riskler (5 madde): AGPL sızması (Yüksek) · JUCE GPL/ticari çatışması (Yüksek) · yıldız verisi eskimesi (Düşük) · çift SSOT (Orta) · 32-bit ASIO varsayımı (Orta).
- index.md §7.2 web kaynakları: steinberg.net/developers/asiosdk-open · juce-framework/JUCE · koel/koel · ampache/ampache · dusk-audio/dusk-studio · ddabidov/Headphone-DAC-AMP · TPA3255_ClassD_PBTL · BoostCore-Module.

## Bu Kaynaktan Oluşan/Güncellenen Wiki Sayfaları

[[ecosystem-genel]] (yeni)
