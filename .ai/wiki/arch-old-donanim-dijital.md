---
title: "vault: architecture.old — Donanım Dijital 2. Nesil"
type: dokuman
created: 2026-10-06
updated: 2026-10-06
sources: [arch-old-donanim-dijital]
tags: [dac, adc, i2s, xmos, usb-audio]
---

# Donanım Dijital 2. Nesil (k054–k059 · 6 klasör · 20 dosya)

`raw/architecture-old/` — dijital ses donanımı, yeniden yazılmış 2. nesil.

| Klasör | Konu | Dikkat |
|---|---|---|
| k054-dac-adc-zinciri | Clock/jitter, ölçüm, zincir mimarisi | 99.8KB çapraz kontrol dosyası |
| k055-ak4458-dac | AK4458 ana DAC rehberi | kaynak karşılaştırma |
| k056-pcm3168a-dac-adc | PCM3168A DAC+ADC | kaynak karşılaştırma |
| k057-i2s-interface | I2S/TDM haberleşme + firmware | yedek kaynak entegrasyonu |
| k058-xmos-xu316 | XU316 USB ses kokteyi + firmware | — |
| k059-usb-audio | USB Audio Class yolu + firmware | — |

## İlgili Sayfalar

- [[arch-old-donanim-1nesil]] — 1. nesil (k006–k013)
- [[arch-old-donanim-analog]] — 2. nesil analog taraf
- [[arch-old-neva-dsp]] — yazılım DSP zinciri
