---
title: "Ses / DSP Açık Kaynak Ekosistemi"
type: guide
category: ecosystem
version: 1.0.0
description: "JUCE 8.011, DaisySP, YUP, wolfsound, OL_DSP, Dusk Studio ve OpenStudio DSP ekosistemi — lisans matrisi, pedagojik dersler ve CoreMusic K3/K10/K2 katmanlarına entegrasyon planı."
durum: active
tarih: 2026-09-24
kaynak: exa web doğrulaması (2026-09-24); JUCE GitHub (juce-framework/JUCE 8.011★), Sasha Martynov YUP (148★ ISC), wolfsound DSP-in-Plugins, eriknl/OL_DSP, austenweekes/DaisySP, Dusk Audio (dusdk.github.io), OpenStudio Docs (docs.openstudio.dev), Awesome-Audio-DSP (1.289★), gordongood/spa-build)
status: active
authority: "JUCE ve DSP açık kaynak ekosistemi; CoreMusic ses zincirinde K3/K10/K2 referansıdır — lisans seçimi ADR gerektirir."
updated: 2026-09-24
---

# CoreMusic — Ses / DSP Açık Kaynak Ekosistemi

> **Kapsam:** Bu belge, `.ai/ecosystem/` altındaki 6 ekosistem dokümanından 3.'südür.
> Kapsadığı K katmanları: **K3 (DSP motoru), K10 (ses I/O / low-latency), K2 (ses kartı sürücü sözleşmesi)**.
> Doğrulama tarihi: **2026-09-24 (exa web doğrulaması)**.
> Bu dosya yeni kaynak araştırması yapmaz; sağlanan doğrulanmış exa sonuçlarını `.ai/architecture/github-referanslari.md` ve `.ai/CLAUDE.md` ile çapraz bağlar.

**⚠️ UYARI — Lisans Kuralı (CLAUDE.md §4, §20, §21):**
- **AGPL kodu CoreMusic'e asla kopyalanmaz** — sadece fikir/akış.
- **JUCE: GPL v3 (açık kaynak) vs ticari lisans** — bu belge §5.3'te ADR kararı olarak işaretler.
- **Açık kaynak DSP kodu kopyalanmadan önce §5.2 lisans matrisi okunmalıdır.**

**Template hizası (Guardrail #16):** docs-md-template.md §1–§7 + §4.1 Şablon-Önce bloğu verbatim korunmuştur.

> 2026-09-24 — CoreMusic Ekosistem Serisi (3/6)

---

## Table of Contents

1. [§1 Amaç](#1-amaç)
2. [§2 Kapsam](#2-kapsam)
3. [§3 Mimari](#3-mimari)
4. [§4 Kurallar](#4-kurallar)
5. [§5 Workflow](#5-workflow)
6. [§6 Doğrulama](#6-doğrulama)
7. [§7 Referanslar](#7-referanslar)

---

## §1 Amaç

**Amaç:** CoreMusic'in ses zincirini (DSP motoru, low-latency I/O, ses kartı sürücü sözleşmesi) besleyen açık kaynak ekosistemi — JUCE 8, DaisySP, YUP, wolfsound, OL_DSP, Awesome-Audio-DSP, Dusk Studio IPC ve OpenStudio hybrid — lisans matrisi ve K3/K10/K2 entegrasyon planıyla birlikte haritalandırmak.

**Kapsam:**

| Kapsar | Kapsamaz |
|--------|----------|
| JUCE 8.011★ yapısı + GPL/ticari lisans ikilemi (K3, K10) | JUCE kodunun kopyalanması (ADR gerektirir) |
| DaisySP / YUP / OL_DSP / wolfsound DSP pedagojisi (K3) | AGPL/uyumsuz lisanslı DSP kodu kopyalama |
| Awesome-Audio-DSP 1.289★ içindekiler haritası (K3/K10) | DSP efekt evinin gerçekleştirmesi (bu belge planlama) |
| Dusk Studio çok süreçli IPC (Linux shm+eventfd, macOS shm_open, Win CreateFileMapping+WaitOnAddress) (K3/K10) | Gerçek driver/backend implementasyonu (yazılım değil doküman) |
| OpenStudio hybrid `window.__JUCE__` köprüsü (K3) | K14/K15 web-stack kararları (bkz. ekosistem-mimarileri.md) |
| ASIO/WASAPI sürücü sözleşmesi etkileşimi (K2) | ASIO SDK lisans detayı (bkz. asio-wasapi-rehber.md) |

**Hedef Kullanıcı:**
- CoreMusic DSP motoru (K3) ve ses I/O (K10) geliştiren senior C/C++ mühendisi
- JUCE lisans kararı (GPL vs ticari) alması gereken teknik yönetici / ADR sahibi
- Düşük gecikmeli ses zinciri (WASAPI exclusive / ASIO) entegre eden sistem mimarı
- DSP algoritmalarını referans alıp "ne kopyalanır, ne kopyalanmaz" ayrımını yapan geliştirici

**Bağlantılar:**
- `.ai/architecture/index.md` → K0–K20 SSOT (K2, K3, K10)
- `.ai/architecture/github-referanslari.md` → §3 (JUCE, Awesome-Audio-DSP, YUP), §5 (Dusk IPC, OpenStudio)
- `.ai/ecosystem/index.md` → Ekosistem dizini (3/6 bu dosya)
- `.ai/CLAUDE.md` §4 / §20 / §21 → Lisans ve yasaklı desen kuralları

---

## §2 Kapsam

### §2.1 CoreMusic İhtiyaçları

| İhtiyaç | K Katmanı | DSP Ekosistemi Karşılığı |
|---------|-----------|--------------------------|
| DSP efekt motoru (reverb, EQ, delay) | K3 | DaisySP / YUP / OL_DSP + wolfsound dersleri |
| Cross-platform framework (Win/macOS/Linux) | K3 | JUCE 8 (ADR: GPL vs ticari) |
| Low-latency ses I/O (callback model) | K10 | JUCE AudioDeviceManager / PortAudio / miniaudio |
| Ses kartı sürücü sözleşmesi (ASIO/WASAPI/CoreAudio) | K2 | JUCE AudioIODeviceType + ASIO SDK lisansı |
| GUI + ses köprüsü (Efectirim bileşeni) | K3/K10 | Dusk IPC (çok süreçli izolasyon) |
| Web tabanlı DSP kontrol (opsiyonel) | K3 | OpenStudio hybrid `window.__JUCE__` |
| "Neyi kopyalarım" taraması | K3/K10 | Awesome-Audio-DSP 1.289★ içindekiler |

### §2.2 Mevcut Durum (CoreMusic)

- **Mevcut donanım referansı:** XMOS XU316 + ES9039 DSP zinciri (CLAUDE.md §22, github-referanslari §2 — "birebir eşleşen").
- **Yasaklı desen:** PCM5122 (CLAUDE.md §21 — §3.6.3 madde 5'te tekrar edilir).
- **Eksik olan:** JUCE lisans ADR'si henüz yazılmamış; DSP motoru K3 için çerçeve seçimi (JUCE mu, yalın C++ mu) belirsiz.
- **Mevcut sürücü sözleşmesi:** K2 katmanı ASIO/WASAPI hedefliyor (ASIO SDK lisansı açık kaynak — bkz. asio-wasapi-rehber.md §1).

### §2.3 Boşluk Analizi

| Boşluk | Etkilenen K | Önem | DSP Ekosistemi Çözümü |
|---------|-------------|------|------------------------|
| JUCE lisans kararı yok | K3, K10, K2 | **Yüksek** | §5.3 ADR şablonu (GPL v3 / ticari / permissive alternatif) |
| DSP algoritma referansı yok | K3 | **Yüksek** | DaisySP/YUP/OL_DSP API haritası (§3.2) + lisans matrisi (§5.2) |
| GUI↔ses izolasyonu yok | K3/K10 | Orta | Dusk IPC modeli (§3.3) — fikir olarak, kod kopyalanmadan |
| Awesome-Audio-DSP içindekileri taranmadı | K3/K10 | Orta | §3.4 kategori→K eşlemesi |
| Web DSP köprüsü araştırılmadı | K3 | Düşük | OpenStudio hybrid (§3.5) |
| Sürücü exclusive-mode davranışı belirsiz | K2 | **Yüksek** | §6.1'de asio-wasapi-rehber.md'ye devir |

---

## §3 Mimari

### §3.1 JUCE 8.011★ (K3, K10, K2)

**Doğrulanmış olgu (2026-09-24):** [github.com/juce-framework/JUCE](https://github.com/juce-framework/JUCE) — **8.011★**, license: **GPL v3 / ticari (commercial)**, "Cross-platform C++ framework for developing desktop and mobile applications" (2026-09-24 exa).

**JUCE 8.011★'in CoreMusic'e değdiği üç katman:**

| JUCE Modülü | CoreMusic K Katmanı | Kullanım Amacı |
|-------------|---------------------|----------------|
| `juce_audio_devices` (AudioDeviceManager, AudioIODeviceType) | **K2, K10** | ASIO / WASAPI exclusive / CoreAudio cihaz keşfi + callback kurulumu |
| `juce_audio_processors` (AudioProcessor) | **K3** | DSP efekt zinciri base class (processBlock deseni) |
| `juce_gui_basics` + `juce_audio_utils` | K3 (Efectirim UI) | Cross-platform GUI + ses parametresi binding |
| `juce_core` (String, MemoryBlock, Threads) | K3/K10 yardımcı | Altyapı — bağımsız K4 yardımcıları ile çakışma riski §5.1'de |

**Yapı dersi (ne öğrenilir):** JUCE, ses cihazını `AudioIODeviceType` soyutlamasıyla platformdan ayırır; DSP kodu `AudioProcessor`'da kalır ve GUI'den ayrı süreçte (veya ayrı thread'de) çalışabilir. CoreMusic'in K2↔K3↔K10 ayrımı bu soyutlama ile hizalanır.

**Lisans ikilemi (§5.3 ADR tetikleyicisi):** GPL v3 açık kaynak sürüm, ticari lisans ücretli. CoreMusickapalı-kaynak (closed-source) hedefiyse JUCE GPL kullanılamaz → ticari lisans VEYA permissive alternatif (§3.6.4 S3) gerekir.

---

### §3.2 DSP Algoritma Kütüphaneleri (K3)

#### §3.2.1 DaisySP — C++ DSP modül kütüphanesi

**Doğrulanmış olgu:** [github.com/austenweekes/DaisySP](https://github.com/austenweekes/DaisySP) — "A collection of open-source DSP modules for audio software and hardware projects" (2026-09-24 exa).

**Ders (K3):** DaisySP, hardware (Daisy Seed) ve software (plugin) hedefleri için aynı DSP modüllerini paylaşır. CoreMusic'in K3 motoru da aynı prensibi izlemeli: **algoritma katmanı donanımdan bağımsız** (ES9039/XU316 zinciri §2.2), yalnızca I/O katmanı K10'da donanıma bağlanır.

**Entegrasyon:** §5.2 lisans matrisindeki "OK" satırı ise — modül API'leri incelenir, kod **kopyalanmaz**, algoritma yapısı referans alınır.

#### §3.2.2 YUP — Basic Audio Effects Library

**Doğrulanmış olgu:** [github.com/Sasha-Martynov/YUP](https://github.com/Sasha-Martynov/YUP) — **148★**, license: **ISC**, "Basic Audio Effects Library" (2026-09-24 exa).

**Yapı:** Header-based, C++17; Efektler: **Delay, Chorus, Phaser, Distortion, Compressor**; Single Amplitude/FrequencyResponse API; Kütüphane arayüzü `AudioFx::Effect` → `EffectChain` → `FxContainer`. Hedefler: Linux (GCC), macOS (Clang), Windows (MinGW).

**Ders (K3 — Efekt Eviii şeması):** YUP'un `Effect` → `EffectChain` → `FxContainer` hiyerarşisi, CoreMusic Efekt Eviii'nin zincir desenine birebir uyar. ISC lisansı GPL uyumlu §5.2'de yeşil.

**Entegrasyon:** Efekt Eviii (Efectirim, K3) için **referans mimari** — zincir şeması ve efekt başına tek arayüz. Kod kopyalanmadan, API şekli incelenerek.

#### §3.2.3 Diğerleri (Awesome-Audio-DSP harmanı)

| Proje | Doğrulanmış Bilgi (2026-09-24) | CoreMusic K3 Dersi |
|-------|--------------------------------|---------------------|
| [OL_DSP](https://github.com/eriknl/OL_DSP) | "A collection of DSP modules for audio applications" | Modül koleksiyonu deseni (DaisySP benzeri) |
| [wolfsound / DSP-in-Plugins](https://github.com/wolfsound/DSP-in-Plugins) | JUCE + C++ DSP eğitim deposu | JUCE AudioProcessor pedagojisi |
| [gordongood/spa-build](https://github.com/gordongood/spa-build) | "SPA: Simple Plugin Architecture demo build" | Plugin mimarisi demo |
| [Awesome-Audio-DSP](https://github.com/Beraliv/Awesome-Audio-DSP) | **1.289★**, "Audio DSP resources in C/C++... frameworks, synthesis, analysis, filters, effects, spatial" | Tarama matrisi §3.4 |

---

### §3.3 Dusk Studio — Çok Süreçli Ses/GUI IPC (K3, K10)

**Doğrulanmış olgu (2026-09-24):** Dusk Audio teknik dokümantasyonu — [dusdk.github.io](https://dusdk.github.io/) — desktop app (GUI) ile web app (audio) arasında **low-latency parametre senkronizasyonu** için platform bazlı IPC:

| Platform | Shared Memory | Sync / Wake |
|----------|---------------|-------------|
| Linux | POSIX shared memory | eventfd |
| macOS | shm_open | kqueue |
| Windows | CreateFileMapping | WaitOnAddress |

**Yapı dersi (K3↔K10 süreç izolasyonu):** Dusk, GUI çökmesinin ses motorunu sürdürmesi ve ses xrun'larının GUI'yi kilitlememesi için **GUI ve audio'yu ayrı süreçlerde** tutar; aralarında lock-free shared memory + low-level wake primitive. CoreMusic'in Efectirim (GUI) + DSP motoru (K3) ikilisi için bu model **doğrudan uygulanabilir fikir** — ama kod değil, mimari desen olarak.

**Entegrasyon (§3.6.3):** Windows tarafında `CreateFileMapping` + `WaitOnAddress`, CoreMusic'in Windows K10/K3 köprüsünde referans desen. Kod kopyalanmaz; API isimleri ve desen dokümante edilir (§3.6.1 mor satır).

### §3.4 Awesome-Audio-DSP 1.289★ — İçindekiler → K Eşlemesi (K3, K10)

**Doğrulanmış olgu:** [github.com/Beraliv/Awesome-Audio-DSP](https://github.com/Beraliv/Awesome-Audio-DSP) — **1.289★** (2026-09-24 exa). README içindekiler: "Frameworks, Synthesis, Analysis, Filters, Effects, Spatial, Media ...".

| Awesome-Audio-DSP Kategorisi | CoreMusic K Katmanı | Not |
|------------------------------|---------------------|-----|
| Frameworks | K3, K10 | JUCE dahil — §3.1 |
| Synthesis | K3 | Efekt Eviii genişletme (oscillator/sampler) |
| Analysis | K3, K14 | Meter/FFT — K14'te telemetriye devredilebilir |
| Filters | K3 | EQ pedagojisi |
| Effects | K3 | Efekt Eviii çekirdeği |
| Spatial | K3 | Stereo/surround — CoreMusic Class AB×8 (§2.2) ile ilişkili |
| Media | K10 | Codec/I/O köprüsü — streaming (bkz. muzik-streaming-sunuculari.md) |

**Kullanım:** Depo, CoreMusic DSP motoru için **tarama matrisidir** — her kategori için 2-3 referans seçilir, §5.2 lisans matrisinden "OK" filtresi uygulanır, kopyalamadan API deseni çıkarılır.

### §3.5 OpenStudio — Hybrid `window.__JUCE__` Köprüsü (K3)

**Doğrulanmış olgu (2026-09-24):** [docs.openstudio.dev](https://docs.openstudio.dev/) — "JavaScript Window Object" section: `window.__JUCE__` JS/native köprüsü, custom webkit.messageHandlers (Safari/WKWebView), standart window.webkit.messageHandlers (macOS/WKWebView) ve fallback console.log approach (mobil WebView/JSKotlin).

**Yapı dersi (K3 — web tabanlı DSP kontrol):** JUCE hybrid uygulamalarda webview HTML/JS, native C++'a `window.__JUCE__` üzerinden mesaj gönderir. CoreMusic'in Efectirim'i webview içinde barındırma senaryosu (varsa) bu köprüyü kullanır — native DSP (K3) ayrı kalır, UI webview'da.

**Entegrasyon:** §3.6.4'teki 4 senaryodan **S4 (hybrid)** için referans. Lisans: JUCE bağımlılığı olduğu için §5.3 ADR'sine bağlı.

### §3.6 Lisans Matrisi, Ders Eşlemesi ve Entegrasyon

### §3.6.1 Lisans Matrisi (§4.1 Şablon bloğu uygulaması)

| Proje | Lisans (Doğrulanmış) | Kopyalanabilir mi? | Kullanım Şekli |
|-------|----------------------|-------------------|----------------|
| JUCE 8.011★ | **GPL v3 / ticari** | ⚠️ Ticari lisans GEREKLİ (ADR §5.3) | Referans mimari / ADR kararı |
| YUP 148★ | **ISC** | ✅ ISC (GPL uyumlu, atıf) | API şekli referans (Effect→Chain) |
| DaisySP | ⚠️ VERIFICATION REQUIRED | Bekleme | Algoritma yapısı (kopyasız) |
| Awesome-Audio-DSP | ⚠️ (derleme deposu) | Fikir/liste OK | Tarama matrisi |
| Dusk IPC (dusdk) | ⚠️ VERIFICATION REQUIRED | **Kod yok — desen OK** | Platform IPC API isimleri |
| OpenStudio (JUCE hybrid) | JUCE lisansına bağlı | ⚠️ ADR'ye bağlı | Köprü deseni |
| OL_DSP / wolfsound | ⚠️ VERIFICATION REQUIRED | Bekleme | Pedagojik ders |
| **AGPL DSP kodu** | AGPL | ❌ **HİÇBİR ZAMAN** | Sadece fikir (CLAUDE.md §4) |

> ⚠️ "VERIFICATION REQUIRED" satırları: exa doğrulamasında lisans bilgisi gelmedi — **kopyalamadan önce resmi repo lisans dosyası okunmalı** (Guardrail #14: kaynaksız üretim yok).

### §3.6.2 Ders → K Katmanı Eşlemesi

| # | Ders | Kaynak | K Katmanı |
|---|------|--------|-----------|
| 1 | Algoritma donanımdan bağımsız (software/hardware aynı modül) | DaisySP | **K3** |
| 2 | `Effect → EffectChain → EffectContainer` zincir deseni | YUP (ISC) | **K3** (Efekt Eviii) |
| 3 | `AudioIODeviceType` ile platform soyutlama | JUCE 8.011★ | **K2, K10** |
| 4 | GUI↔ses çok süreçli lock-free IPC | Dusk Studio | **K3, K10** |
| 5 | `window.__JUCE__` native köprüsü | OpenStudio | **K3** (hybrid) |
| 6 | Kategori bazlı DSP taraması (1.289★) | Awesome-Audio-DSP | **K3, K10** |
| 7 | Plugin mimarisi demo (SPA) | gordongood/spa-build | **K3** |
| 8 | JUCE eğitim (AudioProcessor pedagojisi) | wolfsound/DSP-in-Plugins | **K3** |

### §3.6.3 Copy / Don't Copy

**✅ KOPYALANABİLİR (lisans yeşil / sadece desen):**

1. YUP (ISC) zincir deseni API şekli (§3.2.2) — atıf ile.
2. Dusk IPC **deseni** (shared memory + wake primitive isimleri) — kod yok, mimari şema.
3. Awesome-Audio-DSP **listesi** (derleme deposu, kod değil).
4. DaisySP/OL_DSP/wolfsound **algoritma yapısı** — kod kopyalanmadan API deseni (lisans doğrulanana kadar).

**❌ KOPYALANAMAZ:**

1. **AGPL DSP kodu** (varsa) — CLAUDE.md §4/§21 kırmızı çizgi.
2. **JUCE kodu** — GPL v3 ise CoreMusickapalı-kaynak ile uyumsuz; ticari lisans olmadan çekirdek kod kopyalanmaz (§5.3 ADR).
3. **Lisansı doğrulanmamış** proje kodu (§3.6.1 mor satırlar) — Guardrail #14.
4. **PCM5122 deseni** — zaten CLAUDE.md §21'de yasaklı (§3.7 madde 5).

### §3.6.4 Senaryolar (§4.1 şablon genişletmesi — 4 satır Durum/Aksiyon)

| Senaryo | Durum | Aksiyon |
|---------|-------|---------|
| **S1: CoreMusickapalı-kaynak hedefi** | JUCE GPL v3 ile uyumsuz | Ticari JUCE lisansı AL veya permissive stack (DaisySP/PortAudio benzeri) seç → ADR §5.3 |
| **S2: CoreMusic açık kaynak (GPL) hedefi** | JUCE GPL v3 uyumlu | JUCE 8.011★ K3/K10/K2 ana çerçeve + DaisySP/YUP (ISC) efekt pedagojisi → ADR |
| **S3: Permissive-only stack** | JUCE lisans maliyeti istenmiyor | YUP (ISC, doğrulandı) + DaisySP/OL_DSP (lisans doğrula) + PortAudio/miniaudio (lisans doğrula) → §5.2 matrisi doldur |
| **S4: Süreç izolasyonu + web UI** | Efectirim GUI↔K3 xrun riski | Dusk deseni (CreateFileMapping/WaitOnAddress) + OpenStudio `window.__JUCE__` hybrid köprü → §3.3/§3.5 |

### §3.6.5 Entegrasyon — CoreMusic'e Somut Giriş (K eşlemesi ile)

| DSP Ekosistemi Öğesi | CoreMusic'e Giriş Yolu | Hedef K | Öncelik |
|----------------------|------------------------|---------|---------|
| JUCE `AudioIODeviceType` | K2 sürücü sözleşmesi soyutlaması (ASIO/WASAPI seçimi) | **K2, K10** | **Yüksek** (ADR önce) |
| JUCE `AudioProcessor` | Efekt Eviii base class — processBlock deseni | **K3** | **Yüksek** (ADR ile) |
| DaisySP / YUP (ISC) | Efekt algoritma referansı (Effect→Chain) — kopyasız | **K3** | **Yüksek** |
| Awesome-Audio-DSP 1.289★ | DSP tarama matrisi (Filters/Effects/Spatial) | **K3** | Orta |
| Dusk IPC | Efectirim GUI ↔ K3 motoru çok süreçli köprü (Win: CreateFileMapping+WaitOnAddress) | **K3, K10** | Orta |
| OpenStudio `window.__JUCE__` | Hybrid webview DSP kontrolü (opsiyonel S4) | **K3** | Düşük |
| wolfsound / spa-build / OL_DSP | JUCE/plugin pedagojisi — eğitim amaçlı | **K3** | Düşük |
| ASIO SDK ↔ K2 | Ayrık belge: asio-wasapi-rehber.md (§6.1 devir) | **K2** | **Yüksek** |

**Sıra (2026-09-24):** (1) §5.3 JUCE lisans ADR'si → (2) S1-S4 senaryosu seç → (3) K2 sürücü sözleşmesi (asio-wasapi-rehber.md ile) → (4) K3 Efekt Eviii (YUP zincir deseni + DaisySP algoritma) → (5) Dusk deseni ile GUI↔ses izolasyonu (opsiyonel).

---

### §3.7 Efekt Eviii DSP Modül Haritası (K3 — Uygulama Planı)

Aşağıdaki harita, §3.2'deki DSP kütüphanelerinden çıkan dersleri CoreMusic Efekt Eviii'nin modül listesine çevirir. **Hiçbir satır kod kopyalamaz; yalnızca rol ve referans tanımıdır.**

| Efekt Eviii Modülü | Referans Kaynak (§3.2) | DSP Tekniği | Öncelik | Durum |
|--------------------|-------------------------|-------------|---------|-------|
| Delay | YUP (ISC ✅) | Line/Allpass delay + feedback | Yüksek | Referans hazır |
| Chorus | YUP (ISC ✅) | Modulated tap + LFO | Yüksek | Referans hazır |
| Phaser | YUP (ISC ✅) | Allpass chain + LFO | Orta | Referans hazır |
| Distortion | YUP (ISC ✅) | Waveshaping | Orta | Referans hazır |
| Compressor | YUP (ISC ✅) | Envelope follower + gain | Orta | Referans hazır |
| EQ (Filters) | Awesome-Audio-DSP §3.4 | Biquad / RBJ cookbook | Yüksek | Tarama bekliyor |
| Reverb | DaisySP / OL_DSP (lisans doğrula) | FDN / Schroeder | Yüksek | LICENSE okunacak |
| Spatial / pan | Awesome-Audio-DSP §3.4 | VBAP / equal-power | Düşük | Class AB×8 hedefi (§2.2) |
| Meter / analysis | Awesome-Audio-DSP §3.4 | RMS/FFT | Orta | K14 telemetriye devredilebilir |

**Uygulama kuralı:**

1. Her modül önce §3.6.4 senaryosu seçildikten sonra (S1-S4) kodlanır — JUCE `AudioProcessor` (S2) veya bağımsız `EfektEviProcessor` (S3).
2. ISC (YUP) referansında API şekli serbesttir (atıf ile); VERIFICATION REQUIRED kaynaklarda sadece **matematik/akış şeması** alınır.
3. Her modülün process zincirinde K10 callback sözleşmesi (§5.1 `prepare`/`process`) korunur.
4. Modül başına lisans notu `.ai/DECISIONS.md`'ye işlenir; AGPL izi varsa modül **düşürülür** (CLAUDE.md §4).

### §3.8 Zaman Çizelgesi ve Bağımlılık Grafiği

~~~text
[ADR JUCE lisans] ──> [S1-S4 senaryo seçimi] ──> [K2 sürücü sözleşmesi (asio-wasapi-rehber.md)]
        │                                               │
        │                                               v
        └──────────────> [K3 Efekt Eviii modülleri (§3.7)] <── K10 callback (prepare/process)
                              │
                              v
                     [Opsiyonel: Dusk deseni GUI↔ses izolasyonu (§3.3)]
~~~

| Aşama | Bağımlılık | Tahmini Kapsam |
|-------|-----------|----------------|
| A1: JUCE ADR (§5.3) | Yok — ilk adım | DECISIONS.md kaydı |
| A2: S1-S4 seçimi | A1 | Tek satır karar |
| A3: K2 sürücü sözleşmesi | A2 + asio-wasapi-rehber.md | ASIO/WASAPI exclusive davranışı |
| A4: Efekt Eviii modülleri | A2, A3 (callback sözleşmesi) | §3.7 tablosundaki Yüksek öncelikliler |
| A5: GUI↔ses izolasyonu | A4 (xrun gözlemi varsa) | Dusk deseni (§3.3) |

---

## §4 Kurallar

### §4.1 ZORUNLU: ŞABLON ÖNCE (Template-First)

**Herhangi bir dosyayı yazmadan ÖNCE `.ai/.templates/` dizinindeki ilgili şablonları oku:**

1. Uygun şablonu `[[.templates/index]]` (`.ai/.templates/index.md`) §7.1 tablolarından seç.
2. Şablonu oku; `{{VARIABLE}}` alanlarını doldur, gereksiz bölümleri kaldır.
3. **Şablon varsa ona göre yaz.**
4. **Şablon yoksa** standart 8-bölüm formatına göre yaz ve `⚠️ VERIFICATION REQUIRED` notuyla `log.md`'ye şablon eksiğini bildir.
5. Şablon okunmadan yazılan dosya **geçersizdir** (Guardrail #16): revert edilir, `log.md`'ye ERROR girilir.

| Durum | Aksiyon |
|-------|---------|
| `.ai/.templates/` altında ilgili şablon VAR | Şablonu oku → ona göre yaz |
| Şablon YOK | Standart formata göre yaz + `log.md`'ye "şablon eksiği" kaydı |
| Şablon okundu ama çelişiyor | DUR → `[[../../CLAUDE.md]]` §2.1 SSOT öncelik sırası |
| Vault erişilemiyor | `⚠️ VERIFICATION REQUIRED` → yazım durdurulur, kullanıcıya sor |

> Yukarıdaki blok `.ai/.templates/documentation/docs-md-template.md` §3.3'ten birebir kopyalanmıştır (Guardrail #16 — kısaltılamaz).

### §4.2 Frontmatter Uygulaması (Bu Belge)

| Placeholder | Değer |
|-------------|-------|
| `{{BASLIK}}` | Ses / DSP Açık Kaynak Ekosistemi |
| `{{ACIKLAMA}}` | JUCE 8.011★, DaisySP, YUP, Dusk IPC, OpenStudio — lisans + K3/K10/K2 entegrasyonu |
| `{{TARIH}}` | 2026-09-24 |
| `{{DURUM}}` | active |
| `{{KAYNAK}}` | exa web doğrulaması (2026-09-24) — §7 Referanslar |
| `{{STATUS}}` | active |
| Authority | "JUCE ve DSP ekosistemi; K3/K10/K2 referans — lisans ADR gerektirir" |

### §4.3 §1–§7 Kapsam Haritası

| Şablon Bölümü | Bu Belgede |
|----------------|------------|
| §1 Amaç | Kapsam, kullanıcı, bağlantılar |
| §2 Kapsam | CoreMusic K3/K10/K2 ihtiyaçları + boşluklar |
| §3 Mimari | JUCE, DaisySP/YUP, Awesome-Audio-DSP, Dusk, OpenStudio, §3.6 matris/senaryo/entegrasyon |
| §4 Şablonlar | Şablon-Önce bloğu (verbatim) + frontmatter + §4.4 lisans tablosu |
| §5 Workflow | Lisans matrisi + ADR şablonu + 4 senaryo genişletmesi |
| §6 Doğrulama | Devir (ASIO belgesi), risk, kanıt |
| §7 Referanslar | 9 kaynak (JUCE, Awesome-Audio-DSP, YUP, OL_DSP, wolfsound, DaisySP, Dusk, OpenStudio, spa-build) |

### §4.4 CoreMusic Lisans Tablosu (Bu Belge için)

| Bağımlılık | Lisans | K Katmanı | Durum |
|-----------|--------|-----------|-------|
| JUCE 8.011★ | GPL v3 / ticari | K3, K10, K2 | **ADR GEREKLİ** (§5.3) |
| YUP 148★ | ISC | K3 | ✅ Referans OK |
| Awesome-Audio-DSP (liste) | N/A (liste) | K3, K10 | ✅ Tarama OK |
| DaisySP / OL_DSP / wolfsound | VERIFICATION REQUIRED | K3 | ⚠️ Doğrula → kopyasız kullan |
| Dusk IPC (dusdk) | VERIFICATION REQUIRED | K3, K10 | ⚠️ Desen OK, kod yok |
| AGPL kaynak | AGPL | — | ❌ Asla (CLAUDE.md §4) |
| PCM5122 | — | — | ❌ Yasaklı desen (CLAUDE.md §21) |

---

## §5 Workflow

### §5.1 JUCE Entegrasyon Kod Yüzeysi (Referans — kopya değil)

~~~cpp
// JUCE AudioProcessor deseni (wolfsound/DSP-in-Plugins pedagojisi) — ÖRNEK ŞEMA
// CoreMusic Efekt Eviii (K3) bu deseni JUCE DIŞINDA da uygulayabilir (S3 senaryosu):
class EfektEviProcessor {   // K3 — JUCE'dan bağımsız olabilir
public:
    virtual void prepare(double sampleRate, int blockSize) = 0;  // K10 sözleşme
    virtual void process(float** io, int channels, int n) = 0;   // callback (K10→K3)
    // JUCE'da: AudioProcessor::processBlock — aynı şekil
};
~~~

**Not:** `juce_core` (String, MemoryBlock) kullanılırsa K4 yardımcılarıyla (guid, timestamp) çakışma riski §6.2'de işaretlenir.

### §5.2 Lisans Doğrulama Matrisi (Yükseltilmiş §4.1 4-satır tablo)

| Lisans | Durum | Aksiyon (K3/K10/K2) |
|--------|-------|---------------------|
| ISC (YUP) | ✅ Doğrulandı (2026-09-24) | API desenini incele, atıfla tut |
| GPL v3 (JUCE açık kaynak) | ⚠️kapalı-kaynak CoreMusic ile uyumsuz | Ticari lisans ADR veya S3 permissive stack |
| Ticari (JUCE) | ⚠️ Ücretli | ADR bütçe kalemi |
| VERIFICATION REQUIRED (DaisySP, OL_DSP, wolfsound, Dusk) | ⚠️ Beklemede | Repo LICENSE dosyası oku — okumadan kopya YOK (Guardrail #14) |
| AGPL | ❌ Kırmızı çizgi | Sadece fikir (CLAUDE.md §4, §20, §21) |

### §5.3 JUCE Lisans ADR Şablonu (Karar noktası)

~~~text
ADR: JUCE Çerçeve Seçimi (K3/K10/K2)
Durum: ÖNERİ (2026-09-24)
Bağlam: CoreMusickapalı-kaynak mı, GPL mı belirsiz. JUCE 8.011★ = GPL v3 / ticari.
Karar Seçenekleri:
  (a) JUCE ticari lisans al → en hızlı cross-platform K2/K10 (maliyetli)
  (b) JUCE GPL v3 + CoreMusic GPL → açık kaynak hedefliyse uygun
  (c) Permissive stack (S3): YUP ISC + DaisySP + PortAudio/miniaudio (lisans doğrula)
Sonuç: .ai/DECISIONS.md'ye yazılacak.
~~~

### §5.4 Platform IPC Kod Yüzeysi (Dusk deseni — isimler referans, kod yok)

~~~text
Linux  : shm_open / mmap     + eventfd           → K3↔K10 paylaşımlı parametre bloğu
macOS  : shm_open            + kqueue            → aynı desen
Windows: CreateFileMapping   + WaitOnAddress      → Efectirim GUI ↔ DSP motoru (S4)
Not: Dusk kodu KOPYALANMAZ; API isimleri ve akış şeması referans alınır (§3.6.3).
~~~

### §5.5 OpenStudio Hybrid Köprü Şeması (K3 — S4)

~~~text
Webview (Efectirim UI)
   |  window.__JUCE__.postMessage / webkit.messageHandlers
Native C++ (K3 DSP motoru, ayrı süreç/thread — Dusk deseni)
Fallback: console.log (mobil WebView/JSKotlin benzeri)
~~~

---

## §6 Doğrulama

### §6.1 Devir Noktaları (Belge Sınırları)

| Konu | Devredilen Belge | K |
|------|------------------|---|
| ASIO SDK lisansı + WASAPI exclusive gecikme | `.ai/ecosystem/asio-wasapi-rehber.md` | **K2, K0** |
| Streaming sunucu DSP'si (codec) | `.ai/ecosystem/muzik-streaming-sunuculari.md` | K9/K15 |
| Donanım DSP zinciri (ES9039, XU316) | `.ai/ecosystem/donanım-devre-referanslari.md` | K1/K16/K17 |
| Web-stack DSP kontrolü (K14/K15) | `.ai/ecosystem/ekosistem-mimarileri.md` | K14/K15 |

### §6.2 Riskler

| Risk | Etki | Olasılık | Önlem |
|------|------|----------|-------|
| JUCE lisans ADR'si yazılmadan kodlama başlar | **Yüksek** (hukuki) | Orta | §5.3 ADR → DECISIONS.md |
| "VERIFICATION REQUIRED" projesi doğrulanmadan kopyalanır | **Yüksek** | Orta | Guardrail #14: LICENSE dosyası oku |
| AGPL DSP kodu referansla sızar | **Kritik** | Düşük | §3.6.3 kırmızı çizgi + PR review |
| `juce_core` → K4 yardımcı çakışması (String, timestamp) | Orta | Orta | K3'te K4'e minimal bağımlılık |
| Dusk deseni yanlış anlaşıp tek süreç optimize edilir | Orta | Orta | §3.3: lock-free iki süreç şeması korunur |
| PCM5122 deseni geri gelir | **Kritik** | Düşük | CLAUDE.md §21 + §3.6.3 madde 4 |

### §6.3 KPI / Kanıt Komutları (§4.1-öncesi 5 adıma karşılık)

~~~powershell
# 1) Şablonu bul — zaten bu belgede (Guardrail #16):
Test-Path .ai/.templates/documentation/docs-md-template.md
# 2) Placeholder kalmadı mı:
Select-String -Path .ai/ecosystem/ses-dsp-acik-kaynak.md -Pattern '{{TARIH}}|{{DURUM}}|{{KAYNAK}}'
# 3) Şablon genişletildi mi — §1-§7 başlıkları:
Select-String -Path .ai/ecosystem/ses-dsp-acik-kaynak.md -Pattern '^## [1-7]\.'
# 4) Authority footer var mı:
Select-String -Path .ai/ecosystem/ses-dsp-acik-kaynak.md -Pattern 'Authority|authority:'
# 5) Frontmatter 7 alan:
Get-Content .ai/ecosystem/ses-dsp-acik-kaynak.md -TotalCount 14
~~~

**KPI:** Dosya ≥500 satır; §4.1 Şablon-Önce bloğu verbatim; 0 placeholder; lisans matrisi (§3.6.1) eksiksiz; JUCE ADR işareti (§5.3) mevcut.

### §6.4 Sonraki Adım (2 dakikadan kısa)

**§5.3'teki JUCE ADR şablonunu kopyalayıp `.ai/DECISIONS.md`'ye "ÖNERİ" olarak yapıştırın** — CoreMusickapalı-kaynak/GPL hedefini bir cümleyle yazın; gerisi ADR'de şekillenir.

---

## §7 Referanslar

| # | Kaynak | URL | Doğrulama | Kullanım |
|---|--------|-----|-----------|----------|
| 1 | JUCE 8.011★ | https://github.com/juce-framework/JUCE | exa 2026-09-24 | §3.1, §5.3 |
| 2 | Awesome-Audio-DSP 1.289★ | https://github.com/Beraliv/Awesome-Audio-DSP | exa 2026-09-24 | §3.4 |
| 3 | YUP 148★ ISC | https://github.com/Sasha-Martynov/YUP | exa 2026-09-24 | §3.2.2 |
| 4 | OL_DSP | https://github.com/eriknl/OL_DSP | exa 2026-09-24 | §3.2.3 |
| 5 | wolfsound DSP-in-Plugins | https://github.com/wolfsound/DSP-in-Plugins | exa 2026-09-24 | §3.2.3, §5.1 |
| 6 | DaisySP | https://github.com/austenweekes/DaisySP | exa 2026-09-24 | §3.2.1 |
| 7 | Dusk Audio docs | https://dusdk.github.io/ | exa 2026-09-24 | §3.3, §5.4 |
| 8 | OpenStudio Docs | https://docs.openstudio.dev/ | exa 2026-09-24 | §3.5, §5.5 |
| 9 | spa-build | https://github.com/gordongood/spa-build | exa 2026-09-24 | §3.2.3 |
| 10 | CoreMusic SSOT | .ai/architecture/index.md | yerel | K2/K3/K10 |
| 11 | Lisans kuralları | .ai/CLAUDE.md §4, §20, §21 | yerel | §3.6, §5.2 |

---

## Authority

**JUCE ve DSP açık kaynak ekosistemi, CoreMusic ses zincirinin K3/K10/K2 referansıdır — lisans seçimi ADR ile sabitlenir; AGPL/PCM5122 kırmızı çizgisi değişmez.**

> 2026-09-24 | Belge 3/6 | Durum: active | Kaynak: exa web doğrulaması | Geçmiş: ".ai/ecosystem/index.md" (dizin)