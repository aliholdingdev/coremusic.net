---
type: architecture
category: layer-topic
title: "K000 · İşletim Sistemi Türü — macOS (Core Audio / HAL)"
date: 2026-10-10
status: active
version: 1.0.0
authority: SSOT
---

# macOS — Ses Yolu ve Zemin

> Kaynak: Apple Developer — Core Audio · Core Audio Overview (archive) · AVAudioEngine docs — 2026-10-10 erişimli.
> Vault: `[[ADR-019-per-os-neva-player]]` (macOS = CoreAudio) · `[[../os-master]]`

## §1 Çekirdek Gerçekler (web-doğrulanmış)

| Konu | Gerçek | Kaynak |
|---|---|---|
| Altyapı | Core Audio, iOS ve macOS'un dijital ses altyapısı; HAL (Hardware Abstraction Layer) üzerinde katmanlı | Apple — What Is Core Audio? |
| Donanıma erişim | Uygulamalar çoğunlukla HAL'e **doğrudan** dokunmaz; **AUHAL** (macOS) / **AURemoteIO** (iOS) birimi aradan geçer | Apple — Core Audio Overview §HAL |
| Üç katman | Alt: HAL + I/O Kit · Orta: Converter/File/Unit/Graph/Clock servisleri · Üst: Audio Queue, AVAudioPlayer, AVAudioEngine | Apple — API Architectural Layers |
| Real-time | Çekirdek API (callback'li) en yüksek performansı verir; AVAudioEngine üst seviye graf (node/bağlantı, manual render modu dâhil) | Apple — AVAudioEngine docs |
| MIDI | Core MIDI (macOS; iOS'ta yok) · Core Audio Kit yalnız macOS | Apple — Frameworks appendix |
| Eşdeğerlik | JACK FAQ: CoreAudio callback-merkezli ama **uygulamalar arası yönlendirme yok**; HAL donanım soyutlaması sağlar | jackaudio.org FAQ |

## §2 CoreMusic Karşılığı
- ADR-019: macOS adapter'ı **CoreAudio** (ortak `IAudioBackend` ince adapter).
- Düşük gecikme hedefi ADR-006'da yalnız ASIO/WASAPI için sayılmıştır → macOS hedefi
  **ölçülmemiş** → `⚠️ VERIFICATION REQUIRED`.

## §3 Durum
**PLANNED (kod kanıtı YOK)** — repo'da Swift/ObjC dosyası 0 · yalnız vault kararı (ADR-019).

## §4 Bilinmeyenler
- AVAudioEngine mi yoksa doğrudan AudioUnit/IOProc mı kullanılacağı → ADR-019 adapter kapsamı, seçim **UNKNOWN**.
- Core Audio sürüm/API sürüm hedefi → UNKNOWN.
