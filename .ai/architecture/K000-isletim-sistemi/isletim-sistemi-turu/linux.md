---
type: architecture
category: layer-topic
title: "K000 · İşletim Sistemi Türü — Linux (ALSA / PipeWire / JACK)"
date: 2026-10-10
status: active
version: 1.0.0
authority: SSOT
---

# Linux — Ses Yolu ve Zemin

> Kaynak: docs.pipewire.org (Overview) · kernel-internals.org/alsa · Ubuntu Wiki · ArchWiki Pro Audio · LAC2020 PipeWire paper — 2026-10-10 erişimli.
> Vault: `[[ADR-019-per-os-neva-player]]` (Linux = ALSA + PipeWire) · `[[../os-master]]`

## §1 Çekirdek Gerçekler (web-doğrulanmış)

| Katman | Gerçek | Kaynak |
|---|---|---|
| Çekirdek | **ALSA** Linux çekirdeğine gömülü (sürücüler + `/dev/snd/*`); OSS yalnız emülasyon | kernel-internals.org/alsa |
| Sınır | ALSA çekirdekte yalnız: hw_params, buffer, mixer, MIDI — karıştırma/çözünürlük dönüşümü **user-space** | kernel-internals.org/alsa |
| Kısıt | ALSA'da bir cihazı aynı anda genelde **tek uygulama** açar (Dmix/sound server şart) | PipeWire Overview · Ubuntu Wiki |
| Ses sunucusu | **PipeWire**: graph tabanlı (node/port/link); PulseAudio + JACK uyum katmanı (`pipewire-pulse`, `pipewire-jack`) + ALSA plugin'i | docs.pipewire.org |
| Oturum | **WirePlumber** varsayılan session manager; ALSA cihazlarını node'a çevirir, link politikasını yürütür | docs.pipewire.org |
| Pro-audio | JACK 2002'den beri düşük gecikme/prodüksiyon; ArchWiki: modern çekirdek `CONFIG_PREEMPT` çoğu kullanım için yeterli | jackaudio.org · ArchWiki |
| Tarihçe | OSS → ALSA (2.5/2.6, 2002-2003) → PulseAudio (masaüstü) + JACK (stüdyo) → PipeWire (Ubuntu 22.10+ varsayılan) | Ubuntu Wiki · LAC2020 |

## §2 CoreMusic Karşılığı
- ADR-019: Linux adapter'ı **ALSA + PipeWire**; JACK/`pw-jack` uyumu opsiyonel.
- Raspberry Pi hedefi (Tier 4, ARM64/Debian — `brain.md:361`) bu yığına oturur; `EMBEDDED` cihaz = RPi5 (`brain.md:551`).
- ADR-017 hard-RT kısıtları (callback'te bloklayıcı I/O yasağı) Linux'ta `SCHED_FIFO`/düşük periyot beklentisiyle çakışma riskini kontrol eder.

## §3 Durum
**PLANNED (kod kanıtı YOK)** — repo'da C/C++ kaynak dosyası 0 (ADR-061 tarama) ·
ALSA/PipeWire entegrasyonu diskte yok; yalnız vault kararı (ADR-019).

## §4 Bilinmeyenler
- Hedef dağıtım/sürüm (PipeWire vs eski PulseAudio sistemi) → **UNKNOWN**.
- RT_PREEMPT çekirdek gerekliliği ölçülmüş değil → `⚠️ VERIFICATION REQUIRED`.
