---
title: "vault: architecture.old — Platform Çekirdekleri"
type: dokuman
created: 2026-10-06
updated: 2026-10-06
sources: [arch-old-platform-cekirdekler]
tags: [platform, windows, linux, macos, rpi5]
---

# Platform Çekirdekleri (k000–k022 · 7 klasör · 26 dosya)

`raw/architecture-old/` — dört platformun çekirdek mimarileri (iki nesil).

| Klasör | Platform | Vurgu |
|---|---|---|
| k000-windows-core | Windows | NT/Win32, ASIO, WASAPI, Win32 olay döngüsü |
| k001-linux-rpi5 | Linux/RPi5 | POSIX çekirdek, ARM64 yerleşim |
| k002-macos-tasinabilirlik | macOS | XNU/Darwin, çapraz platform API |
| k019-windows-core | Windows (yeni nesil) | windows-core · windows-api |
| k020-linux-core | Linux (yeni nesil) | linux-core · ALSA native |
| k021-macos-core | macOS (yeni nesil) | macos-core · Core Audio |
| k022-rpi5-core | RPi5 (yeni nesil) | rpi5-core · PWM/GPIO ses |

## İlgili Sayfalar

- [[arch-k0-isletim-sistemi]] — yeni vault'taki karşılığı
- [[arch-old-os-mekanizmalari]] — syscall/thread/IPC/bellek
- [[arch-old-surucu-2nesil]] — ASIO/WASAPI/CoreAudio/ALSA derinlemesine
