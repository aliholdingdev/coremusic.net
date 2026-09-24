---
title: "GitHub Açık Kaynak Referansları"
project: "COREMUSIC Architecture"
created: 2026-09-20
version: 2.0
description: "COREMUSIC projesi için GitHub üzerindeki açık kaynak referansların derlenmesi — katman bazlı eşleme dahil"
categories:
  - ses-isleme
  - dsp
  - streaming
  - donanim
  - codec
---

# GitHub Açık Kaynak Referansları

Bu dosya, COREMUSIC projesinin farklı katmanları için GitHub üzerindeki açık kaynak projeleri ve referansları içerir.

**Kanonik katman kaynağı:** [[.ai/architecture/katman-baglilik-matrisi.md]] (21 katman: K0-K20).
**Kanonik envanter:** [[.ai/architecture/index.md]] §4 (diskten doğrulanmış klasör listesi).

---

## §0 Red Notu — K{n}.r ayrı katman SAYILMAZ

> 🔴 **RED KURALI:** Bir katman klasöründeki .r dosyaları (ör. K3.r, README dosyalarının kısaltılmış gösterimi, indeks satırları) **ayrı katman olarak SAYILMAZ**.
>
> **Gerekçe:** Katman sayımı dosya sayımından türetilmez; katmanlar K0-K20 arası **21 sabit** değerdir. Bir klasörde 15 dosya varsa bu 15 katman değil, tek katmanın 15 belgesidir.
>
> **Yasak yorumlama:**
> 1. "K3.r dosyası bulundu → bu yeni bir katmandır" — YANLIŞ. K3.zaten mevcut katmandır.
> 2. "K16 klasöründe 22 dosya var → K16-K37 arası 22 katman" — YANLIŞ. Dosya sayısı ≠ katman sayısı.
> 3. "firmware/ klasörü ayrı katmandır (K21)" — YANLIŞ. firmware = **K1.f** (K1'in firmware alt referansıdır), ayrı katman değildir.
>
> **Doğru sayım:** K0'dan K20'ye 21 katman; firmware (K1.f) ve eski sürücü klasörü ayrı katman DEĞİLDİR = **21 katman**.
>
> **ADR-024 notu:** eski sürücü klasörü k2-surucu/ ile birleştirilmiştir (2026-09-24); eski klasör artık 0 dosyadır ve referans eşlemesinde yer almaz. Sürücü referansları K2 altındadır.

---

## §1 Ses İşleme & DSP

| # | Proje | Stars | Lisans | Link |
|---|-------|-------|--------|------|
| 1 | JUCE | 8811 | GPL v3 | [github.com/juce-framework/JUCE](https://github.com/juce-framework/JUCE) |
| 2 | EasyEffects | 9855 | GPL v3 | [github.com/wwmm/easyeffects](https://github.com/wwmm/easyeffects) |
| 3 | Audio-Effects | 883 | GPL v3 | [github.com/juandagilc/Audio-Effects](https://github.com/juandagilc/Audio-Effects) |
| 4 | sndfilter | 486 | BSD-0 | [github.com/velipso/sndfilter](https://github.com/velipso/sndfilter) |
| 5 | DSPark | 90+ | MIT | [github.com/CristianMoresi/DSPark](https://github.com/CristianMoresi/DSPark) |
| 6 | DSP-Cpp-filters | 187 | MIT | [github.com/dimtass/DSP-Cpp-filters](https://github.com/dimtass/DSP-Cpp-filters) |
| 7 | Signalsmith-basics | 109 | MIT | [github.com/Signalsmith-Audio/basics](https://github.com/Signalsmith-Audio/basics) |
| 8 | Patina | — | — | [github.com/ShmKnd/Patina](https://github.com/ShmKnd/Patina) |
| 9 | fxdsp | 17 | MIT | [github.com/kdrag0n/fxdsp](https://github.com/kdrag0n/fxdsp) |
| 10 | MAGDA | 151 | GPL v3 | [github.com/Conceptual-Machines/magda-core](https://github.com/Conceptual-Machines/magda-core) |
| 11 | Open-Synth | — | — | [github.com/synthalorian/open-synth](https://github.com/synthalorian/open-synth) |
| 12 | APC | 289 | MIT | [github.com/Noizefield/audio-plugin-coder](https://github.com/Noizefield/audio-plugin-coder) |
| 13 | SoLoud | — | Zlib | [github.com/jarikomppa/soloud](https://github.com/jarikomppa/soloud) |
| 14 | PortAudio | — | Public Domain | [github.com/PortAudio/portaudio](https://github.com/PortAudio/portaudio) |
| 15 | libsndfile | — | LGPL v2 | [github.com/libsndfile/libsndfile](https://github.com/libsndfile/libsndfile) |
| 16 | FAUST | — | GPL v2 | [github.com/grame-cncm/faust](https://github.com/grame-cncm/faust) |
| 17 | HISE | — | GPL v3 | [github.com/hiseaudio/HISE](https://github.com/hiseaudio/HISE) |
| 18 | AudioEffectTools | 4 | GPL v3 | [github.com/taberhuang/AudioEffectTools](https://github.com/taberhuang/AudioEffectTools) |
| 19 | Dusk Studio | — | — | [github.com/dusk-audio/dusk-studio](https://github.com/dusk-audio/dusk-studio) |

---

## §2 Müzik Streaming & Medya

| # | Proje | Stars | Dil | Link |
|---|-------|-------|-----|------|
| 1 | Koel | 17195 | PHP/Vue | [github.com/koel/koel](https://github.com/koel/koel) |
| 2 | Ampache | 3793 | PHP | [github.com/ampache/ampache](https://github.com/ampache/ampache) |
| 3 | Forte | 355 | Vue/Node | [github.com/kaangiray26/forte](https://github.com/kaangiray26/forte) |
| 4 | Leafplayer | 159 | TypeScript | [github.com/paulschwoerer/leafplayer](https://github.com/paulschwoerer/leafplayer) |
| 5 | Musable | 122 | — | [github.com/musable/musable](https://github.com/musable/musable) |
| 6 | PHP-Music | 27 | PHP | [github.com/HirotakaDango/PHP-Music](https://github.com/HirotakaDango/PHP-Music) |
| 7 | Echo | 3 | NestJS | [github.com/Alexzafra13/echo](https://github.com/Alexzafra13/echo) |
| 8 | Phlix | 4 | PHP 8 | [github.com/detain/phlix-server](https://github.com/detain/phlix-server) |
| 9 | SPlayer | 7423 | Vue/Electron | [github.com/imsyy/splayer](https://github.com/imsyy/splayer) |
| 10 | Neko Music | 3 | NestJS | [github.com/mrcatlait/neko-music](https://github.com/mrcatlait/neko-music) |

---

## §3 Donanım & Devre Tasarımı

| # | Proje | Amaç | Link |
|---|-------|------|------|
| 1 | TPA3255-ClassD-PBTL | TPA3255 Class D amplifikatör | [github.com/EliMattingly22/TPA3255_ClassD_PBTL](https://github.com/EliMattingly22/TPA3255_ClassD_PBTL) |
| 2 | modular-amplituner | Modüler amplifikatör | [github.com/dabigatran/modular-amplituner](https://github.com/dabigatran/modular-amplituner) |
| 3 | PBA MK1 | Taşınabilir BT amplifikatör | [github.com/TeHSiGGi/pba_mk1](https://github.com/TeHSiGGi/pba_mk1) |
| 4 | DA15 | USB-C DAC + Amplifikatör | [github.com/eliachiarucci/DA15](https://github.com/eliachiarucci/DA15) |
| 5 | Headphone-DAC-AMP | XMOS XU316 + ES9039 | [github.com/ddabidov/Headphone-DAC-AMP](https://github.com/ddabidov/Headphone-DAC-AMP) |
| 6 | Spin DAC | USB-C DAC, 24-bit/192kHz | [github.com/honeyoak/spin-dac](https://github.com/honeyoak/spin-dac) |
| 7 | OpAmp-Headphone | 16 paralel OpAmp | [github.com/Wardstein/OpAmp-Headphone-Amplifier](https://github.com/Wardstein/OpAmp-Headphone-Amplifier) |
| 8 | BoostCore-Module | MT3608 boost converter | [github.com/hamzadenizyilmaz/BoostCore-Module](https://github.com/hamzadenizyilmaz/BoostCore-Module) |
| 9 | RP2040-DAC-Amp | RP2040 USB DAC + Class D | [github.com/RonSheely/rp2040-dac-amp](https://github.com/RonSheely/rp2040-dac-amp) |

---

## §4 DSP Kütüphaneleri

Bu bölüm, §1'deki ses işleme projelerinden bağımsız olarak doğrudan DSP algoritmaları ve filtre implementasyonları için kullanılan kütüphaneleri içerir.

| # | Proje | Stars | Lisans | Amaç |
|---|-------|-------|--------|------|
| 1 | DSP-Cpp-filters | 187 | MIT | Biquad, low-pass, high-pass, band-pass filtreler (C++) |
| 2 | Signalsmith-basics | 109 | MIT | Hızlı ve hafif DSP yardımcıları (resampling, freq analysis) |
| 3 | sndfilter | 486 | BSD-0 | Reverb, biquad, chorus, delay, low/high pass filtreler (C) |
| 4 | fxdsp | 17 | MIT | Delay, filter, reverb, chorus, compressor (C++) |
| 5 | FAUST | — | GPL v2 | Real-time DSP programlama dili ve derleyici |
| 6 | DSPark | 90+ | MIT | Hafif DSP framework (C++) |
| 7 | SoLoud | — | Zlib | Ses oynatma ve DSP efektleri motoru |

---

## §5 Medya & Codec

| # | Proje | Stars | Link |
|---|-------|-------|------|
| 1 | FFmpeg | 45000+ | [github.com/FFmpeg/FFmpeg](https://github.com/FFmpeg/FFmpeg) |
| 2 | libsndfile | — | [github.com/libsndfile/libsndfile](https://github.com/libsndfile/libsndfile) |
| 3 | FAUST | — | [github.com/grame-cncm/faust](https://github.com/grame-cncm/faust) |
| 4 | SoLoud | — | [github.com/jarikomppa/soloud](https://github.com/jarikomppa/soloud) |

---

## §6 Katman Eşleme Tablosu (özet)

Her referans tek bir birincil katmana atanır; çapraz referans gerekiyorsa ikincil katman notu olarak yazılır.

| Referans | Birincil Katman | İkincil Katman | Kaynak Bölüm |
|----------|-----------------|----------------|--------------|
| JUCE | K3 (Ses Motoru) | — | §1 |
| Awesome-Audio-DSP | K3 (Ses Motoru) | — | §7.3 |
| DaisySP | K3 (Ses Motoru) | — | §7.3 |
| EasyEffects | K3 (Ses Motoru) | — | §1 |
| sndfilter | K3 (Ses Motoru) | — | §1, §4 |
| DSP-Cpp-filters | K3 (Ses Motoru) | — | §1, §4 |
| Signalsmith-basics | K3 (Ses Motoru) | — | §1, §4 |
| fxdsp | K3 (Ses Motoru) | — | §1, §4 |
| DSPark | K3 (Ses Motoru) | — | §1, §4 |
| FAUST | K3 (Ses Motoru) | K15 (codec) | §1, §4, §5 |
| SoLoud | K3 (Ses Motoru) | K15 (oynatma) | §1, §4, §5 |
| HISE | K3 (Ses Motoru) | — | §1 |
| PortAudio | K2 (Sürücü) | K3 | §1 |
| ASIO SDK | K2 (Sürücü) | — | §7.2 |
| libsndfile | K15 (Medya) | — | §1, §5 |
| FFmpeg | K15 (Medya) | — | §5 |
| Koel | K8 (Servis) | — | §2 |
| Ampache | K8 (Servis) | — | §2 |
| Dusk Studio | K10 (Uygulama) | K3 | §1 |
| OpenStudio | K10 (Uygulama) | K3 | §7.10 |
| TPA3255-ClassD-PBTL | K16 (Class AB) | K1 (Donanım) | §3 |
| Headphone-DAC-AMP | K16 (Class AB) | K1 (Donanım) | §3 |
| BoostCore-Module | K17 (Güç Kaynağı) | — | §3 |
| DA15 / Spin DAC / RP2040-DAC-Amp / OpAmp-Headphone | K1 (Donanım) | K16 | §3 |

---

## §7 Katman Başına Referans Bölümleri (K0-K20)

> **Sayım kuralı:** 21 katman (§0 red notu). firmware = K1.f. Eski sürücü klasörü = ADR-024 ile k2-surucu/ altındadır.

### §7.0 — K0 İşletim Sistemi (k0-isletim-sistemi)

**Kapsam:** Çekirdek işletim sistemi katmanı — 15 dosya (disk envanteri, index.md §4).
**Rol:** Vault boot sırası, dosya sistemi düzeni, temel çalışma zamanı.

| # | Referans | Tür | Eşleme Notu |
|---|----------|-----|-------------|
| 1 | PortAudio | kütüphane | Aday — K2 sürücü arayüzüyle sınırda; birincil K2 |
| 2 | Koel | uygulama | Aday — PHP çalışma zamanı desenleri için |

**Katman Notu:** K0'a özgü doğrudan GitHub referansı henüz atanmadı; atamalar §7.6 havuzundan yapılır ve bu tabloya eklenir.

---

### §7.1 — K1 Donanım (k1-donanim + firmware = K1.f)

**Kapsam:** Donanım katmanı — 23 dosya (k1-donanim) + 8 dosya (firmware, K1.f).
**Rol:** DAC/ADC, amplifikatör, sınıf-D çıkışı, güç arabirimi.

| # | Referans | Stars | Eşleme |
|---|----------|-------|--------|
| 1 | Headphone-DAC-AMP | — | K1 birincil (XMOS XU316 + ES9039); K16 ikincil |
| 2 | TPA3255-ClassD-PBTL | — | K1 ikincil; K16 birincil ( sınıf-D topolojisi ) |
| 3 | DA15 | — | K1 (USB-C DAC + Amplifikatör) |
| 4 | Spin DAC | — | K1 (USB-C DAC, 24-bit/192kHz) |
| 5 | OpAmp-Headphone | — | K1 (16 paralel OpAmp) |
| 6 | RP2040-DAC-Amp | — | K1 (RP2040 USB DAC + Class D) |
| 7 | PBA MK1 | — | K1 (taşınabilir BT amplifikatör) |
| 8 | modular-amplituner | — | K1 (modüler amplifikatör) |

**firmware (K1.f) notu:** Firmware belgeleri ayrı katman DEĞİLDİR (§0 red notu); K1'in firmware alt referansıdır (8 dosya).

---

### §7.2 — K2 Sürücü (k2-surucu)

**Kapsam:** Sürücü katmanı — 14 dosya (ADR-024 birleşim sonrası; eski klasörün 12 dosyası buraya taşındı).
**Rol:** ASIO, WASAPI, donanım sürücü arayüzleri.

| # | Referans | Stars | Lisans | Eşleme |
|---|----------|-------|--------|--------|
| 1 | ASIO SDK | — | Steinberg (özel) | K2 birincil — sürücü katmanının resmi SDK'sı |
| 2 | PortAudio | — | Public Domain | K2 ikincil (K0 adayıyla çakışır; K2 tercih edilir) |
| 3 | WASAPI örnek kodları | — | MIT (MS samples) | K2 (Windows ses arabirimi) |

**ADR-024 notu:** Eski sürücü klasörü artık 0 dosyadır; tüm sürücü referansları K2 altındadır. Eski yollara verilen linkler k2-surucu/README.md'ye yönlendirilir.

---

### §7.3 — K3 Ses Motoru / DSP (k3-ses-motoru)

**Kapsam:** Ses motoru katmanı — 18 dosya.
**Rol:** DSP zinciri, efektler, filtreler, sentez.

| # | Referans | Stars | Lisans | Eşleme |
|---|----------|-------|--------|--------|
| 1 | JUCE | 8811 | GPL v3 | K3 birincil — ses motoru framework'ü |
| 2 | Awesome-Audio-DSP | 10000+ | CC0 (list) | K3 — derleme listesi referansı |
| 3 | DaisySP | 700+ | MIT | K3 — DSP modül kütüphanesi |
| 4 | EasyEffects | 9855 | GPL v3 | K3 — efekt zinciri |
| 5 | sndfilter | 486 | BSD-0 | K3 — reverb/biquad/chorus/delay |
| 6 | DSP-Cpp-filters | 187 | MIT | K3 — biquad filtreler |
| 7 | Signalsmith-basics | 109 | MIT | K3 — resampling, freq analysis |
| 8 | fxdsp | 17 | MIT | K3 — delay/filter/reverb/chorus/compressor |
| 9 | DSPark | 90+ | MIT | K3 — hafif DSP framework |
| 10 | FAUST | — | GPL v2 | K3 birincil (derleyici), K15 ikincil |
| 11 | SoLoud | — | Zlib | K3 birincil (motor), K15 ikincil (oynatma) |
| 12 | HISE | — | GPL v3 | K3 — açık kaynak samplar framework'ü |
| 13 | Audio-Effects | 883 | GPL v3 | K3 — efekt koleksiyonu |
| 14 | AudioEffectTools | 4 | GPL v3 | K3 — efekt araçları |
| 15 | Patina | — | — | K3 — DSP deneyi |
| 16 | MAGDA | 151 | GPL v3 | K3 — (aday; DSP çekirdeği) |
| 17 | Open-Synth | — | — | K3 — (aday; sentez) |
| 18 | APC | 289 | MIT | K3 — plugin coder |
| 19 | OpenStudio | — | — | K3 ikincil (K10 birincil — stüdyo uygulaması) |

**Lisans uyarısı:** JUCE, EasyEffects, HISE, FAUST GPL v3/v2 lisanslıdır — ticari kullanımda kısıtlara tabidir (§8 notu).

---

### §7.4 — K4 Yapay Zeka (k4-yapay-zeka)

**Kapsam:** Yapay zeka katmanı — 14 dosya.
**Rol:** Ses analizi, model çıkarımı, otomasyon.

| # | Referans | Adaylık Durumu | Eşleme |
|---|----------|----------------|--------|
| 1 | (atanmadı) | Havuzdan seçilecek | K4 |
| 2 | MAGDA | Aday (§1 #10) — doğrulanmalı | K4 adayı; K3 ile çakışırsa K3 kalır |

**Katman Notu:** K4 için onaylı referans yoktur; yeni atama bu tabloya eklenir ve §6 özetiyle senkronlanır.

---

### §7.5 — K5 Veri Yönetimi (k5-veri-yonetimi)

**Kapsam:** Veri yönetimi katmanı — 14 dosya.
**Rol:** Şema, migration, depolama, BCNF.

| # | Referans | Tür | Eşleme |
|---|----------|-----|--------|
| 1 | libsndfile | kütüphane | K5 ikincil; K15 birincil (dosya formatı) |
| 2 | (atanmadı) | — | K5 — PHP/MySQL veri katmanı adayları bekleniyor |

**Katman Notu:** Veri katmanı referansları ağırlıkla kendi iç kod tabanındadır (shared/src/Database); dış havuzdan ek referans gerekmez.

---

### §7.6 — K6 Güvenlik (k6-guvenlik)

**Kapsam:** Güvenlik katmanı — 16 dosya.
**Rol:** OWASP, CSRF/CSP, şifreleme, oturum.

| # | Referans | Tür | Eşleme |
|---|----------|-----|--------|
| 1 | OWASP Cheat Sheet Series | rehber | K6 — doğrudan rehber kaynağı |
| 2 | (atanmadı) | — | K6 — kripto kütüphaneleri için onay bekleniyor |

**Katman Notu:** K6'nın birincil kaynağı OWASP belgeleridir; GitHub havuzu §1-§5'te henüz güvenlik kütüphanesi yoktur.

---

### §7.7 — K7 Middleware (k7-middleware)

**Kapsam:** Middleware katmanı — 14 dosya.
**Rol:** PSR-15 middleware zinciri (11 middleware, §25.2 kanıtı).

| # | Referans | Tür | Eşleme |
|---|----------|-----|--------|
| 1 | PSR-15 (php-fig) | standart | K7 — arayüz standardı |
| 2 | (atanmadı) | — | K7 — K6 ile ortak güvenlik middleware'leri |

---

### §7.8 — K8 Servis (k8-servis)

**Kapsam:** Servis katmanı — 14 dosya.
**Rol:** İş mantığı, servis nesneleri, streaming servisleri.

| # | Referans | Stars | Dil | Eşleme |
|---|----------|-------|-----|--------|
| 1 | Koel | 17195 | PHP/Vue | K8 birincil — müzik streaming servis mimarisi |
| 2 | Ampache | 3793 | PHP | K8 birincil — PHP servis/kütüphane desenleri |
| 3 | Forte | 355 | Vue/Node | K8 ikincil (K10) |
| 4 | Leafplayer | 159 | TypeScript | K8 ikincil |
| 5 | PHP-Music | 27 | PHP | K8 — (aday) |
| 6 | Phlix | 4 | PHP 8 | K8 — (aday) |
| 7 | Echo | 3 | NestJS | K8 — (aday; framework farkı) |

---

### §7.9 — K9 API Routing (k9-api-routing)

**Kapsam:** API routing katmanı — 14 dosya.
**Rol:** PageRouter, endpoint, controller.

| # | Referans | Tür | Eşleme |
|---|----------|-----|--------|
| 1 | nikic/fast-route | kütüphane | K9 — routing altyapısı (§25.2 kanıtı: fast-route) |
| 2 | (atanmadı) | — | K9 — Koel/Ampache routing desenleri K8'den okunur |

**Kaynak notu:** K9 birincil iç kaynak shared/src/PageRouter/ ve .ai/.decisions/index.md (ADR-083).

---

### §7.10 — K10 Uygulama (k10-uygulama)

**Kapsam:** Uygulama katmanı — 17 dosya.
**Rol:** UI uygulama katmanı, stüdyo uygulaması, ürün yüzeyi.

| # | Referans | Stars | Dil | Eşleme |
|---|----------|-------|-----|--------|
| 1 | Dusk Studio | — | — | K10 birincil, K3 ikincil (ses motoru entegrasyonu) |
| 2 | OpenStudio | — | — | K10 birincil, K3 ikincil |
| 3 | SPlayer | 7423 | Vue/Electron | K10 — masaüstü oynatıcı arayüzü |
| 4 | Musable | 122 | — | K10 — (aday) |
| 5 | Neko Music | 3 | NestJS | K10 — (aday) |

---

### §7.11 — K11 UX (k11-ux)

**Kapsam:** UX katmanı — 16 dosya.
**Rol:** Responsive mimari, cihaz matrisi, akış tasarımı.

| # | Referans | Tür | Eşleme |
|---|----------|-----|--------|
| 1 | (atanmadı) | — | K11 — ui-design vault içi kaynaklardır (01-mockup-index vb.) |
| 2 | open-source-a11y listeleri | rehber | K11 adayı — doğrulanacak |

**Katman Notu:** K11'in birincil kaynağı .ai/ui-design/ içeriğidir; dış havuz referansı opsiyoneldir.

---

### §7.12 — K12 İzleme (k12-izleme)

**Kapsam:** İzleme katmanı — 13 dosya.
**Rol:** Log, metrik, health check, audit trail.

| # | Referans | Tür | Eşleme |
|---|----------|-----|--------|
| 1 | (atanmadı) | — | K12 — monitör/gözlemcilik kütüphaneleri aday olarak bekliyor |

**Bağımlılık notu:** K12 → K8 tek resmi bağımlılıktır (matris §3.3); izleme verisi servis katmanından okunur.

---

### §7.13 — K13 CI/CD (k13-cicd)

**Kapsam:** CI/CD katmanı — 14 dosya.
**Rol:** GitHub Actions pipeline, deploy, GitLeaks.

| # | Referans | Tür | Eşleme |
|---|----------|-----|--------|
| 1 | github/gitignore | şablon | K13 — repo hijyeni |
| 2 | (atanmadı) | — | K13 — actions/* marketplace aksiyonları kullanımda |

**Kanıt notu:** .github/workflows/ = 0 dosya (§25.2) — PLANNED durumundadır; pipeline kodu henüz yazılmamıştır.

---

### §7.14 — K14 Ağ (k14-ag)

**Kapsam:** Ağ katmanı — 16 dosya.
**Rol:** DNS, CDN, routing altyapısı, subdomain dağılımı.

| # | Referans | Tür | Eşleme |
|---|----------|-----|--------|
| 1 | (atanmadı) | — | K14 — ağ/altyapı adayları havuzdan seçilecek |

---

### §7.15 — K15 Medya Streaming (k15-medya-streaming)

**Kapsam:** Medya streaming katmanı — 16 dosya.
**Rol:** Codec, transcode, dosya formatı, akış protokolleri.

| # | Referans | Stars | Lisans | Eşleme |
|---|----------|-------|--------|--------|
| 1 | FFmpeg | 45000+ | LGPL/GPL | K15 birincil — codec/transcode omurgası |
| 2 | libsndfile | — | LGPL v2 | K15 birincil — dosya formatı okuma/yazma |
| 3 | FAUST | — | GPL v2 | K15 ikincil (K3 birincil) |
| 4 | SoLoud | — | Zlib | K15 ikincil (K3 birincil) |

**Bağımlılık notu:** K15'in fiziksel üretim/bileşen katmanlarına bağımlılığı YOKTUR (matris §2.1 — karar 2 ile tamamen silinmiştir). K15'in tek hedefi K14'tür.

---

### §7.16 — K16 Class AB (k16-class-ab)

**Kapsam:** Class AB amplifikatör bileşen katmanı — 22 dosya.
**Rol:** Sınıf-AB topolojisi, hoparlör çıkışı, bileşen seçimi.

| # | Referans | Stars | Eşleme |
|---|----------|-------|--------|
| 1 | TPA3255-ClassD-PBTL | — | K16 birincil (topolojı referansı), K1 ikincil |
| 2 | Headphone-DAC-AMP | — | K16 birincil (çığnz tasarımı), K1 ikincil |
| 3 | OpAmp-Headphone | — | K16 ikincil (K1 birincil) |

**Sayım notu:** K16-K20 beş bağımsız katmandır; bunları üst/alt hiyerarşiye sokan ifadeler yasaktır (karar 3). K16-K20 bileşen toplamı = 340 dosya (k16-class-ab README ham sayımı SSOT dışıdır).

---

### §7.17 — K17 Güç Kaynağı (k17-guc-kaynagi)

**Kapsam:** Güç kaynağı katmanı — 15 dosya.
**Rol:** Besleme, boost konvertör, güç dağıtımı.

| # | Referans | Stars | Eşleme |
|---|----------|-------|--------|
| 1 | BoostCore-Module | — | K17 birincil — MT3608 boost converter modülü |

---

### §7.18 — K18 Termal (k18-termal)

**Kapsam:** Termal yönetim katmanı — 11 dosya.
**Rol:** Sıcaklık dağıtımı, soğutma, koruma eşikleri.

| # | Referans | Tür | Eşleme |
|---|----------|-----|--------|
| 1 | (atanmadı) | — | K18 — termal simülasyon/analiz adayları bekleniyor |

---

### §7.19 — K19 PCB (k19-pcb)

**Kapsam:** PCB tasarım katmanı — 11 dosya.
**Rol:** Kart tasarımı, yerleşim, üretim dosyaları.

| # | Referans | Tür | Eşleme |
|---|----------|-----|--------|
| 1 | (atanmadı) | — | K19 — KiCad/Altium açık kaynak örnekleri aday |

**Tanım düzeltmesi (karar 6):** K19 = **PCB**. Eski yanlış katman tanımı bu dosyadan kaldırılmıştır; controlled-impedance.md yalnızca K19'un bir alt konusudur, katman tanımı değildir.

---

### §7.20 — K20 BOM (k20-bom)

**Kapsam:** Bill of Materials katmanı — 11 dosya.
**Rol:** Parça listesi, tedarik, maliyet.

| # | Referans | Tür | Eşleme |
|---|----------|-----|--------|
| 1 | (atanmadı) | — | K20 — BOM araçları (InteractiveHtmlBom vb.) aday |

---

## §8 Doğrulama & Kullanım Notları

### §8.1 Lisans Uyarısı

> **Not:** Bu dosya COREMUSIC projesi için bir referans havuzudur. Lisans uyumluluğu her kullanım öncesinde kontrol edilmelidir. GPL lisanslı projeler (JUCE, EasyEffects, HISE, FAUST, MAGDA, Audio-Effects, AudioEffectTools) ticari kullanımda kısıtlamalara tabi olabilir. LGPL (libsndfile, FFmpeg) dinamik linkleme altında daha esnektir.

### §8.2 Eşleme Bütünlük Kuralları

1. Her referans §6 tablosunda tam olarak bir birincil katmana sahiptir.
2. Katman başına §7.x bölümü vardır; 21 bölüm (K0-K20) tamdır.
3. K1.f (firmware) ve eski sürücü klasörü (ADR-024) ayrı bölüm alamaz — §0 red notu.
4. Yeni referans ekleyen kişi: §1-§5 ilgili tabloya + §6 eşleme satırına + ilgili §7.x bölümüne aynı anda ekler.

### §8.3 Doğrulama Listesi

- [ ] §0 red notu mevcut — K{n}.r ayrı katman sayılmaz
- [ ] 21 katman bölümü mevcut (§7.0 - §7.20)
- [ ] §6 eşleme tablosu §7.x bölümleriyle tutarlı
- [ ] K15'in üretim/bileşen katmanlarına bağımlılığı hiçbir yerde yazılmamış (karar 2)
- [ ] K16'yı üst/alt hiyerarşiye sokan ifade yok (karar 3)
- [ ] K19 tanımı PCB (karar 6)
- [ ] Eski sürücü klasörü yalnız ADR-024 bağlamında anılıyor (bu dosyada literal referans 0)
- [ ] Reddedilen eski sayı değerleri yok (tek değerler: 334 ve 340)
- [ ] mojibake yok
- [ ] Dosya ≥500 satır

### §8.4 Sürüm Kaydı

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 1.0 | 2026-09-20 | İlk derleme (§1-§5 havuzları) |
| 2.0 | 2026-09-24 | §0 red notu, §6 eşleme, §7 K0-K20 katman bölümleri, §8 doğrulama; ADR-024 sürücü birleşimi yansıtıldı (karar 1-6, 3 turlu mimari tartışma) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-24
**Mode:** Red Team · Human Mode · Truth Mode
