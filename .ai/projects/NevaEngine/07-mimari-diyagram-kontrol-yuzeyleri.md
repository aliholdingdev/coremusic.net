---
title: "Neva Engine — Mimari Diyagram & Kontrol Yüzeyleri (onaya sunulan v3)"
type: doc
category: audio-engine
version: 3.0.0
status: onay-bekliyor
authority: "SSOT: kullanıcı direktifi 2026-10-15 (Q1-Q3) · 02-dsp-zincir · 06-gereksinimler · 05-neva-mixer"
updated: 2026-10-15
---

# Neva Engine — Mimari Diyagram v2 (CP + OS + Mobile)

**Zorunlu Bağlantılar:** [[06-gereksinimler-ve-farkliliklar]] · [[05-neva-mixer-web-pcm-plani]] · [[02-dsp-zincir-mimari]] · [[01-gelistirme-plani]] · [[brain.md]] §7.4

## §1 Diyagram (kullanıcı onayına sunulan)

```text
===============  NEVA ENGINE — v3 (CP + OS araligi + Mobile DSP)  ===========

 KONTROL YUZEYLERI (3 yol)                         SES CEKIRDEGI
 .-------------------------.                 .-----------------------------.
 | [1] WEB ARAYUZU (PHP)   |   WS/REST JSON  |  NEVA CORE (C++ / C)        |
 |  CoreMusic panel +      |================>|  tek process · RT thread    |
 |  127.0.0.1 tarayici     |                 |  lock-free · 32-bit FLOAT   |
 |  fader/EQ/meter/preset  |                 |                             |
 '-------------------------'                 |  DSP ZINCIRI (sabit sira):  |
 .-------------------------.                 |   1. Kazanc / Pan           |
 | [2] WINDOWS APP (C#)    |   native API /  |   2. EQ 31 bant (parametrik |
 |  WinForms/WPF masaustu  |----WS/REST----->|      + grafik — 2 mod)      |
 '-------------------------'                 |   3. FX RACK                |
 .-------------------------.                 |   4. COMPRESSOR             |
 | [3] MOBILE KONTROL      |                 |   5. LIMITER                |
 |  iOS -> Xcode (Swift)   |                 |   6. Dinamik/Bas ayri modul |
 |  Android -> Android     |                 |        v                    |
 |  Studio (Kotlin)        |                 |  YONLENDIRME MATRISI       |
 '-------------------------'                 '-------------|---------------'

 .======================== MOBILE DSP (AYNI cekirdek, yerel isleme) ==========.
 |  NEVA CORE C++ ayni kaynak -> iki platforma derlenir:                     |
 |   [Android] NDK (CMake/ndk-build) · ses: AAudio/Oboe (API 26+) / SL ES   |
 |   [iOS]     Xcode (C++/ObjC++) · ses: CoreAudio/AVAudioEngine             |
 |   Ayni DSP zinciri (EQ parametrik+grafik · FX · comp · limiter) telefon-  |
 |   da CALISIR · PC'ye bagimli DEGIL · web arayuzu telefonda da kontrol     |
 '==========================================================================='

  PLATFORM ARALIGI
  min: Windows XP  <-----------xxx----------->  max: Windows 11 SDK
  [Q1 CELISI hala acik: XP = v141_xp/.NET<=4.0 — C++20+JUCE9 Win10+ ister]
  Linux/macOS: os/03-04 (CoreAudio/ALSA-PipeWire) — ikinci dal
  Android/iOS: yukaridaki MOBILE DSP kutusu (bu tur eklenen kapsam)

==============================================================================
```

## §2 Yeni gereksinimler (tur 2 — 2026-10-14)

| # | Gereksinim | Teknik karşılık | Durum |
|---|-----------|----------------|-------|
| R10 | **Compressor + Limiter** zincirde açık istek | DSP zinciri: FX sonrası compressor → limiter (kayıp güvenliği); detay 02 §zincir + legacy `eq-dsp-chain.md` | BAĞLAYICI |
| R11 | **Web arayüzü control = PHP** | ✅ **NETLEŞTİ (Q2):** Neva kurulumunda **yerel PHP sunucusu** — (1) Neva kendi PHP paneli · (2) **özel API sistem sunucusu** (diğer uygulamaları bağlama/kontrol etme entegrasyon yüzeyi). CoreMusic Control Service (port 81) = ikinci halka (uzaktan) | BAĞLAYICI |
| R12 | **Masaüstü app control = C#** | Windows native istemci (WinForms/WPF) → aynı WS/REST JSON control plane | BAĞLAYICI |
| R13 | **Mobil = native code**: iOS **Xcode** (Swift/ObjC), Android **Android Studio** (Kotlin) — cross-plat (Flutter/RN) **YOK** | 2 native istemci, aynı REST/WS API | BAĞLAYICI |
| R17 | **MOBILE DSP (tur 3):** sistem hem **Android'de hem iOS'ta ses DSP** yapar — mobil sadece kumanda değil, telefonda yerel işlenir; PC DSP'nin tamamı mobilde de | Aynı C++ çekirdek iki platforma derlenir: Android = NDK (CMake) + AAudio/Oboe; iOS = Xcode (C++/ObjC++) + CoreAudio/AVAudioEngine. DSP zinciri (EQ çift-form · FX · compressor · limiter) birebir aynı. OS aralığı: Android API 26+ ⚠️ (VERIFICATION REQUIRED — XP benzeri min sürüm senaryoya bağlı), iOS 15+ ⚠️ | BAĞLAYICI (tur 3) |
| R14 | **OS aralığı: min Windows XP → max Windows 11 SDK** | ⚠️ **ÇELİŞİ** — bkz §3 Q1 | ⚠️ ONAY BEKLİYOR |
| R15 | Çekirdek dil notu: C++ (min C++ sürümü belirtildi — "min C++ version") | XP varsa çekirdek **C++14/17 taahhüdü** tartışılır (C++20 runtime XP'de yok) | ⚠️ Q1 ile bağlı |
| R16 | **EQ çift form: parametrik + grafik, multi uyumlu, tam kontrol, dinleme anında güzelleştirme** | 2 arayüz → 1 biquad motoru (06 R16 — BAĞLAYICI); UI'da mod anahtarı (parametrik sekme / grafik sekme) | BAĞLAYICI (tur 2) |

## §3 Açık onay noktaları (onay kapısı)

| Q | Soru | Seçenekler |
|---|------|------------|
| Q1 | **Windows XP gerçekten mi?** XP = v141_xp toolset, C++20 yok, JUCE 9 Win10+ ister, C# .NET ≤4.0 | ✅ **CEVAPLANDI (2026-10-15):** (a)+(b) — **min = Windows 7 TAM destek** (modern dal: C++20+JUCE9+ASIO) · **XP = ayrı LEGACY modül** (eski API, sınırlı özellik seti, ayrı derleme hedefi) |
| Q2 | PHP control = **CoreMusic Control Service** üzerinden mi (uzaktan panel), yoksa **yerel PHP sunucusu** mu (Neva Mixer kurulumunda)? | ✅ **CEVAPLANDI (2026-10-15):** (b) — **Neva kurulumundaki yerel PHP sunucusu**: (1) Neva'nın kendi PHP paneli · (2) **diğer uygulamalara bağlanmak için özel API sistem sunucusu** (entegrasyon yüzeyi). CoreMusic paneli (port 81) = ikinci halka (uzaktan erişim) |
| Q3 | ~~Mobil işi sadece kumanda mı?~~ | ✅ **CEVAPLANDI (2026-10-14 tur 3): mobilde de yerel DSP var** — Android + iOS'ta ses işlenir (R17); kumanda + yerel DSP ikisi de. PC çekirdeği = ana, mobil = taşınabilir aynı çekirdek |

> Kural: Q1-Q3 cevaplandı (2026-10-15) — OS hedefleri vault'a işlendi:
> **modern dal = min Windows 7 tam destek** (C++20 · JUCE 9 · ASIO <10ms) ·
> **legacy dal = Windows XP ayrı modül** (eski API, sınırlı özellik) ·
> **control plane = yerel PHP sunucu + özel API sistemi** (diğer uygulamalar için entegrasyon yüzeyi) ·
> mobil = yerel DSP (R17). Mevcut spec (C++20 · JUCE 9) modern dal için geçerlidir.

## §4 Onay durumu

- v1 diyagram (tur 1) onaylandı ve 06'ya işlendi (Q1-Q3 cevapları).
- v2 (2026-10-14) → **v3 ile geçersiz** (3 kontrol yüzeyi + MOBILE DSP kutusu + CP notu).
- **Q1-Q3 cevapları vault'a işlendi (2026-10-15):** 01 (OS aralığı + dil sürümü) ve
  05 (yerel PHP control plane + API) **güncellendi** — bkz §3 kural satırı.
- **Bu v3 onay bekliyor** (kullanıcı onayı → 06'ya diyagram onay satırı eklenir).
