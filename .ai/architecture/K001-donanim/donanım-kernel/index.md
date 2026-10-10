---
type: architecture
category: layer-subindex
title: "K001 · donanım-kernel — Firmware/Driver Sınırı"
date: 2026-10-10
status: active
version: 1.0.0
authority: SSOT
---

# donanım-kernel — Firmware / Driver Sınırı

> Üst indeks: `[[../index]]` · ADR: `ADR-061` (L6 = hardware, firmware, driver, DSP, audio engine) · `ADR-017-dsp-hardware-mode`

## §1 Kapsam
Donanımın **programlanabilir yüzeyi**: firmware (XMOS), driver (ASIO/WASAPI, ALSA),
DSP donanım kipleri. Not: bu dosya **sınır** tanımlar — uygulama K002/K003 katmanlarındadır.

## §2 Sınır Kalemleri

| Kalem | Yön | Durum | Kaynak |
|---|---|---|---|
| XMOS XU316 firmware | K001 → K003 (ses motoru) | PLANNED | ADR-061 · ADR-038 |
| Host driver (ASIO/WASAPI/ALSA) | K001 → K002 (Sürücü) | PLANNED | ADR-019 · ADR-032 (IPC contract) |
| Hard-RT kısıtları | callback'te tahsisat/bloklayıcı I/O **yasak** | accepted (karar) | ADR-017 |
| DSP pipeline sırası | sinyal zinciri ↔ hard-RT hizası | accepted | ADR-062 |

## §3 Bağımlılık
- **Alt:** K000 (zemin) — K001'in tek bağımlılığı (matris §2: K001 → K000).
- **Üst (çağıran):** K002 · K003 · K005 (firmware altında K004 var — matris §1-2 notu).

## §4 Durum
**PLANNED** — repo'da firmware/driver kaynağı **0 dosya** (ADR-061 kaynak taraması: `*.cpp/*.cs` = 0;
`.ai/projects/NevaEngine/` 0 dosya — `.ai/AGENTS.md` §24.3 uyarısı).

## §5 Risk / Not
- Firmware/driver ikisi birbirine karıştırılmaz: firmware XMOS'ta çalışır, driver host OS'ta (bkz. `[[../../K000-isletim-sistemi/isletim-sistemi-turu/windows]]`, `[[../../K000-isletim-sistemi/isletim-sistemi-turu/linux]]`).
- Sürücü lisans/dağıtım kararı → ADR-032 (IPC contract versioning) kapsamında → metin okunmadı `UNKNOWN`.
