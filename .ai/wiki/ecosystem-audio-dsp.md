---
title: "Ekosistem Audio/DSP — ASIO, WASAPI ve DSP Açık Kaynak"
type: dokuman
created: 2026-10-06
updated: 2026-10-06
sources: [ecosystem-audio-dsp]
tags: [ekosistem, asio, wasapi, juce, dsp, surucu, lisans]
---

# Ekosistem Audio/DSP

İki ham dosyanın (`asio-wasapi-rehber.md` — ASIO SDK açık kaynak lisansı, WASAPI exclusive/shared, 64-bit-only kısıtı; `ses-dsp-acik-kaynak.md` — JUCE, YUP, DaisySP, Awesome-Audio-DSP, Dusk IPC, OpenStudio) tema özeti: CoreMusic ses zincirinin K2/K0 sürücü sözleşmesi ile K3/K10 DSP motoru için lisans matrisi, fallback zinciri ve ADR karar noktaları.

## Dosyalar

| Dosya | İçerik/Boyut |
|-------|--------------|
| `raw/ecosystem/asio-wasapi-rehber.md` | ASIO SDK lisans gate, WASAPI karşılaştırması, 64-bit-only, fallback zinciri · 29.6KB · 504 satır |
| `raw/ecosystem/ses-dsp-acik-kaynak.md` | JUCE modülleri + GPL/ticari ADR, YUP/DaisySP/Awesome-Audio-DSP, Dusk IPC, OpenStudio · 30.7KB · 501 satır |

## Ana Bulgular

1. ASIO SDK açık kaynak lisansla yayınlandı (exa 2026-09-24) → dağıtılabilir; 7 maddelik lisans gate tamamlanmadan release BLOKE; **lisansın tam adı `⚠ VERIFICATION REQUIRED`** (asio §3.8 madde 2).
2. Evrensel yerleşik ASIO **yalnız 64-bit sürücü** çalıştırır, 32-bit sürücü desteklenmez → K0 "64-bit only" ADR önerisi, 32-bit hedef RED.
3. K2 fallback zinciri: ASIO → WASAPI exclusive → WASAPI shared; her halkada yapılandırılabilir buffer + `xrun_count` metriği K14'e; ASIO callback'i lock-free `bufferSwitch`.
4. JUCE = GPL v3 / ticari → kapalı kaynak hedefle uyumsuz; ADR seçenekleri: (a) ticari lisans · (b) GPL + açık kaynak · (c) permissive stack (YUP ISC + DaisySP + PortAudio/miniaudio).
5. Dusk çok süreçli IPC dersi (GUI ↔ ses ayrı süreç): Linux shm+eventfd · macOS shm_open+kqueue · Windows CreateFileMapping+WaitOnAddress — desen referans, kod yok.
6. YUP 148★ ISC `Effect → EffectChain → FxContainer` zincir deseni Efekt Eviii'ne (Delay/Chorus/Phaser/Distortion/Compressor) referans; Awesome-Audio-DSP 1.289★ tarama matrisi; DaisySP/OL_DSP/wolfsound lisansları doğrulanmadan kopya yok.
7. OpenStudio `window.__JUCE__` hybrid köprüsü (S4 senaryosu); JUCE `AudioIODeviceType` seçimi: 64-bit ASIO → ASIO, yok/32-bit → exclusive, paylaşımlı → shared.
8. ⚠ Raw içi star çelişkisi: JUCE `index.md`'de **8.811★**, bu iki dosyada **8.011★** → `⚠ VERIFICATION REQUIRED` (güncel değer okunmadı).

## İlgili Sayfalar

- [[ecosystem-genel]] — lisans kapısı ve K çapraz referans
- [[ecosystem-mimarileri]] — pipeline/telemetri kesişimi (xrun metrikleri)
- [[ecosystem-donanim]] — sürücü↔donanım keşfi (XU316, DAC)
- [[neva-engine]] — DSP motoru · [[vault-brain]] — ADR defteri
