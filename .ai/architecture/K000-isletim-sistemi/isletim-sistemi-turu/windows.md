---
type: architecture
category: layer-topic
title: "K000 · İşletim Sistemi Türü — Windows (WASAPI / ASIO / DirectSound)"
date: 2026-10-10
status: active
version: 1.0.0
authority: SSOT
---

# Windows — Ses Yolu ve Zemin

> Kaynak: Microsoft Learn (Core Audio) · FlexASIO BACKENDS · Kodi Wiki — hepsi 2026-10-10 erişimli.
> Vault bağlantısı: `[[ADR-019-per-os-neva-player]]` (Windows adapter'ı bu katmanın taşır) · `[[../os-master]]`

## §1 Çekirdek Gerçekler (web-doğrulanmış)

| Konu | Gerçek | Kaynak |
|---|---|---|
| Ana API | **WASAPI** (Windows Vista+'tan beri; MMDevice API ile birlikte Core Audio bileşeni) | learn.microsoft.com — User-Mode Audio Components |
| Shared mode | Windows ses motoru (`Audiodg.dll`) karıştırır; stream mix formatına uymalı | learn.microsoft.com — Device Formats |
| Exclusive mode | Donanıma doğrudan erişim; bit-exact/özel sample-rate için; sistem sesleri susar | learn.microsoft.com — Exclusive-Mode Streams |
| Düşük gecikme (shared) | **IAudioClient3** ile Windows 10+ shared düşük-period (exclusive'a yakın gecikme) | learn.microsoft.com — Exclusive-Mode Streams |
| Eski API'ler | DirectSound (1995) ve MME modern Windows'ta **WASAPI Shared'e sarınır** — ikinci sınıf | FlexASIO BACKENDS.md |
| ASIO | Steinberg ASIO donanıma özel driver yolu; universal sürücüler (FlexASIO/ASIO4ALL) WASAPI veya WDM-KS üzerine kurulur | github.com/dechamps/FlexASIO |
| Gerçekçi taban | WASAPI Exclusive + WDM-KS dışında paylaşımlı hatta **<10 ms ölçülemez** (paylaşımlı buffer ~10 ms) | FlexASIO BACKENDS.md |
| Öncelik | Exclusive PCM <10 ms periyotta WASAPI taşıma iş parçacığına **"Pro Audio"** MMCSS görevi | learn.microsoft.com — Exclusive-Mode Streams |

## §2 CoreMusic Karşılığı
- ADR-019: Windows adapter'ı **ASIO + WASAPI (Exclusive/Shared)** — `IAudioBackend` ince adapter.
- ADR-006 performans kapısı: `<10 ms ASIO / <20 ms WASAPI` (ADR-019 §1 atfı).
- Fallback: ASIO cihaz kaybı → WASAPI (AGENTS §17-6).
- K002 (Sürücü) katmanındaki `wasapi-exclusive` / `asio-drivers` spec'leri bu gerçeklere oturur.

## §3 Durum
**PLANNED (kod kanıtı YOK)** — repo'da `*.cpp/*.cs` = 0 (ADR-061 kaynak taraması) ·
Windows native kodu/ASIO SDK entegrasyonu diskte yok. Yalnız vault kararı (ADR-017/019/032).

## §4 Bilinmeyenler
- Hangi WASAPI period/buffer değerlerinin seçileceği → **UNKNOWN** (ölçüm yok).
- ASIO sürüm lisansı/kurulumu → ADR-019'da karar var, uygulama kanıtı yok → `⚠️ VERIFICATION REQUIRED`.
