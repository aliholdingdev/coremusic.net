---
title: "Neva Engine (C++20)"
type: arac
created: 2026-10-06
updated: 2026-10-06
sources: [not-readme-vizyon]
tags: [ses, dsp, c++]
---

# Neva Engine

CoreMusic'in C++20 ses işleme motoru; işletim sisteminin sesi bozan katmanlarını baypas eder.

- Özellikler: **zero-allocation** (sıfır bellek tahsisi) ve **lock-free** (kilitlenmeyen) çalışma
- Mimari yerleştirme: K3 Ses İşlem Motoru — 55 bileşen (Neva Engine, DSP, EQ, Crossover)
- Donanım ilişkisi: ayrık analog Class AB amfi + DSP denetimi (`pro.coremusic.net` paneli README'de; ⚠ Çelişki ✅ çözüldü — plan≠implementasyon, bkz. [[coremusic-platform]])
- Rozet: `C++-20-NevaEngine`

Kaynak: `.ai/raw/not-readme-vizyon.md`

## İlgili Sayfalar
- [[coremusic-platform]] — sahibi olduğu platform
- [[mysql-9]] · [[vanilla-javascript]] — yığının diğer katmanları
- [[bayram-ali]] — otorite
