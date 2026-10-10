---
type: architecture
category: layer-topic
title: "K000 · İşletim Sistemi Türü — Android (AAudio / Oboe / OpenSL ES)"
date: 2026-10-10
status: active
version: 1.0.0
authority: SSOT
---

# Android — Ses Yolu ve Zemin

> Kaynak: Android NDK (AAudio · Audio latency · Oboe low-latency) · AOSP (AAudio and MMAP) — 2026-10-10 erişimli.
> Vault: `[[../os-master]]`

## §1 Çekirdek Gerçekler (web-doğrulanmış)

| Konu | Gerçek | Kaynak |
|---|---|---|
| Modern API | **AAudio** (Android 8.0 O+) — C API; stream'e read/write veya callback ile veri | developer.android.com — AAudio |
| Önerilen sarmalayıcı | **Oboe** (C++): AAudio varsa çağırır, yoksa OpenSL ES'e düşer | Android NDK — AAudio notu |
| Sharing | `EXCLUSIVE` (MMAP buffer'a doğrudan yazım — en düşük gecikme; kopmaya yatkın) vs `SHARED` (AudioFlinger karıştırır) | AOSP — AAudio and MMAP |
| Perf mode | `LOW_LATENCY` / `NONE` / `POWER_SAVING` — en düşük gecikme için callback + düşük periyot | developer.android.com — AAudio |
| Donanım garantisi | `android.hardware.audio.low_latency` ≤45 ms çıkış · `android.hardware.audio.pro` ≤20 ms round-trip (CDD) | NDK — Audio latency |
| MMAP yoksa | AAudio eski **AudioFlinger** yoluna düşer — aynı API, daha yüksek gecikme | AOSP — AAudio architecture |
| Ölçü (kılavuz) | Tüm önerilere uyulursa ~20 ms; SHARED →26 ms; 44100 Hz →160 ms (kılavuz tablosu) | Android — Low latency audio tablosu |

## §2 CoreMusic Karşılığı
- Vault'ta Android-native oynatıcı ADR'si **YOK** (ADR-019 3 masaüstü platformunu kapsar) →
  Android erişimi mevcut durumda **mobil web** (ADR-004 SPA) üzerinden → native ses yolu **UNKNOWN**.
- Oboe/AAudio seçimi ileride K002/K003 kapsamında değerlendirilir; karar henüz yok.

## §3 Durum
**PLANNED / UNKNOWN** — repo'da Kotlin/C++ 0 · vault'ta Android ses kararı YOK.

## §4 Bilinmeyenler
- Native Android hedefi → kanıt yok → `⚠️ VERIFICATION REQUIRED`.
- Cihaz/noise-guard politikası → UNKNOWN.
