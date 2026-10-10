---
type: architecture
category: layer-topic
title: "K000 · İşletim Sistemi Türü — Web (Tarayıcı Ses Yolu)"
date: 2026-10-10
status: active
version: 1.0.0
authority: SSOT
---

# Web — Tarayıcı Ses Yolu

> Kaynak: MDN — AudioWorklet · Web Audio API tarayıcı durumu — 2026-10-10 erişimli.
> Vault: `[[ADR-004-multi-domain-spa]]` (çoklu domain SPA) · `[[../os-master]]`

## §1 Çekirdek Gerçekler (web-doğrulanmış)

| Konu | Gerçek | Kaynak |
|---|---|---|
| Üst API | Web Audio API — `AudioContext` düğüm grafiği (decode, filtre, gain, analiz) | MDN Web Audio API |
| Düşük gecikme | **AudioWorklet** — özel işlenme betiği ayrı Web Audio iş parçacığında çalışır; Nisan 2021'den beri yaygın | MDN AudioWorklet (Baseline) |
| Güvenlik | AudioWorklet yalnız **secure context (HTTPS)** içinde | MDN AudioWorklet |
| Eski/ basit | `HTMLAudioElement` (tek dosya oynatma) — kesin senkron/örtük gecikme denetimi yok | Web Audio spesifikasyonu |
| Sınır | Tarayıcı donanım erişimini **kısıtlar** (raw device/exclusive erişim yok) — WASAPI Exclusive/ALSA doğrudan açılamaz | tarayıcı güvenlik modeli |

## §2 CoreMusic Karşılığı
- **IMPLEMENTED yön:** mevcut ürün tamamen tarayıcı tabanlı (5 subdomain SPA — `00-master-index.md` §2).
- Tarayıcıdaki çalma yolu `HTMLAudioElement`/Web Audio üzerinden mi, yoksa ADR-019 native
  player (masaüstü) ile mi ayrıştırılacağı → `[[ADR-004-multi-domain-spa]]` sınırı;
  **tarayıcı-içi gecikme hedefi sayılmamıştır** → `⚠️ VERIFICATION REQUIRED`.
- Streaming gerçekleri (HLS/DASH/AAC) vault'ta ayrıca kararlaştırılmamış → UNKNOWN.

## §3 Durum
**IMPLEMENTED (web yüzeyi)** — kanıt: `home.coremusic.net/` · SPA router
(`shared/src/PageRouter/`) · statik asset zemini `assets.coremusic.net/`.

## §4 Bilinmeyenler
- Oynatıcı elementi seçimi (audio vs Web Audio vs MediaSource) → vault karar metni YOK → UNKNOWN.
- Safari/iOS Web AudioWorklet desteği sınırlı (Chromium issues notu) → tam kapsama `⚠️ VERIFICATION REQUIRED`.
