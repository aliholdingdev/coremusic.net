---
title: "Neva Engine — SDK & Library Envanteri (Doğrulanmış)"
type: doc
category: audio-engine
version: 1.0.0
status: active
authority: "SSOT: .ai/CLAUDE.md §19 · §24 — bu dosya envanter uzantısıdır"
updated: 2026-10-14
---

# Neva Engine — SDK & Library Envanteri

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[brain.md]] · [[01-gelistirme-plani]] · [[02-dsp-zincir-mimari]]

> Amaç: K2 (sürücü) ve K3 (DSP) katmanları için GitHub/rezmi kaynaklardan **doğrulanmış**
> SDK ve kütüphane envanteri. Zero-Hallucination: her satır bu oturumda kaynakla
> doğrulanmıştır; doğrulanamayan `⚠️ VERIFICATION REQUIRED` işaretlidir.

## §1 Doğrulanmış Envanter (kaynaklı)

| # | Kütüphane / SDK | GitHub / Kaynak | Lisans | Kanıt (2026-10-14) | Rol |
|---|----------------|-----------------|--------|--------------------|-----|
| 1 | **JUCE 9** | github.com/juce-framework/JUCE | **AGPLv3 + Commercial** (dual) | repo LICENSE.md ("dual-licensed under AGPLv3 and the commercial JUCE licence") | K3 ana framework (audio device + dsp module + GUI) |
| 2 | **miniaudio** | github.com/mackron/miniaudio | **Public Domain / MIT-0** (seçimli) | repo LICENSE: "ALTERNATIVE 1 - Public Domain / ALTERNATIVE 2 - MIT No Attribution" | Tek-dosya fallback I/O (WASAPI/DirectSound/CoreAudio/ALSA/PulseAudio/JACK); offline mixer |
| 3 | **ASIO SDK 2.3.x** | steinberg.net/developers/asiosdk-open | **GPLv3 açık varyant** + tescilli lisans seçeneği (royalty-free) | Steinberg sayfası: "available in open source form under GPLv3"; tescilli lisans PDF'i §2.5 Steinberg'e bildirim şartı | K2 Windows düşük-gecikme sürücü backend'i |
| 4 | **libsamplerate** | github.com/libsndfile/libsamplerate | **BSD-2-Clause** | GitHub (735★ · son sürüm 0.2.2 · 2021-09) | Sample-rate conversion (kaliteli SRC) |
| 5 | **SoXR** | sourceforge.net/projects/soxr (mirror: github.com/chirlu/soxr, 172★) | **LGPL-2.1+** | repo LICENCE: "GNU Lesser General Public License ... version 2.1 or later" | Alternatif resampler (LGPL → dinamik link / kaynak sunma şartı) |
| 6 | **kissfft** | github.com/mborgerding/kissfft | **BSD-3-Clause** | repo LICENSES/BSD-3-Clause | RTA/FFT çekirdeği (bağımsız, JUCE bağımsız doğrulama) |
| 7 | **dr_libs** (dr_wav/dr_flac/dr_mp3) | github.com/mackron/dr_libs | **Public Domain** | repo LICENSE: "free and unencumbered ... public domain" | PCM decode (WAV/FLAC/MP3) — tek-header, real-time thread dışı decode |
| 8 | **Airwindows** | github.com/airwindows/airwindows | **MIT** | airwindows.com + repo ("free and open source under the MIT license") | 500+ stüdyo seviyesi DSP referans algoritması (limiter, EQ, konsol emülasyonu) |
| 9 | **libebur128** | github.com/jiixyj/libebur128 | **MIT** | repo: "implements the EBU R 128 standard ... licensed under the MIT license" | LUFS loudness metering (ITU-R BS.1770-4 / EBU R128) |
| 10 | **LSP (Linux Studio Plugins)** | github.com/lsp-plugins (repo adı ⚠️) | **GPLv3+** | repology: "Code licensed under GPLv3+" · sürüm 1.2.23 (2025-08) | Parametric EQ / limiter / dynamics referans implementations |
| 11 | **iPlug2** | github.com/iplug2/iplug2 | "free, liberally licensed" — tam lisans adı ⚠️ VERIFICATION REQUIRED | GitHub org + KVR ("permissively licensed ... commercial use") | JUCE alternatifi plugin framework (hafif, izinli) |

## §2 Doğrulanamayanlar (⚠️ VERIFICATION REQUIRED — klon öncesi doğrula)

| Kütüphane | Bilinen hedef | Doğrulanacak |
|-----------|---------------|--------------|
| RtAudio (github.com/thestk/rtaudio) | Audio I/O alternatifi | lisans + bakım durumu |
| PortAudio | Audio I/O alternatifi | lisans + son sürüm |
| libsoundio (github.com/andrewrk/libsoundio) | Real-time uyumlu I/O | lisans + bakım |
| FFTW | FFT (GPL → lisans riski) | lisans + lisans etkileşimi |
| libsndfile / libFLAC | Dosya IO/encode | lisans + son sürüm |
| x42-plugins | Limiter/EQ referans | lisans + repo yolu |
| DPF (DISTRHO Plugin Framework) | Plugin framework alternatifi | lisans + J9 uyumu |
| MVerb / plate reverb impl. | Reverb referans | lisans + kalite |
| syams86/Virtual-Audio-Pipeline | Windows sanal audio driver | WDM uyumluluğu (Win 10/11) + bakım |

## §3 Karar Matrisi (kategori → seçim)

| Kategori | Seçim | Gerekçe (lisans + teknik) |
|----------|-------|---------------------------|
| Çapraz-platform I/O | **JUCE 9** | Vault otoritesi (CLAUDE §24); tek API → ASIO/WASAPI/CoreAudio/ALSA/PipeWire |
| Fallback / offline | **miniaudio** | Public domain; tek dosya; bağımlılık yok; JUCE bağımsız test harness |
| ASIO | **ASIO SDK (GPLv3 varyant)** | ⚠️ **LİSANS KARARI — kullanıcı onayı gerekli**: GPLv3 → tüm ürün kaynak açılır; tescilli varyant → Steinberg'e bildirim, royalty-free |
| Sanal ses kartı | **WASAPI loopback + engine routing** (kısa vade) | Açık kaynak Windows sanal audio driver bulunmuyor denecek kadar az; sürücü tabanlı çözüm sonraki faz (⚠️ envanter §2) |
| Resampler | **libsamplerate** | BSD-2, kararlı; SoXR LGPL olduğu için ikincil |
| FFT/RTA | **juce::dsp::FFT** (ana) + kissfft (doğrulama) | JUCE zaten bağımlılık; kissfft bağımsız test |
| PCM decode | **dr_libs** | Public domain, tek-header, zero-dep |
| DSP altyapı | **JUCE 9 dsp module** | IIR biquad (TPT), reverb, waveshaper; vault guardrail uyumlu |
| Referans algoritma | **Airwindows (MIT)** | Stüdyo kalitesi; MIT → gömme serbest |
| LUFS | **libebur128** | MIT; EBU R128/BS.1770-4 tam uyum |
| EQ/limiter referans | **LSP (GPLv3+)** | Sadece referans/algoritma — GPLv3 kod doğrudan gömülmez |

## §4 Lisans Risk Özeti

| Risk | Etki | Önlem |
|------|------|-------|
| JUCE AGPLv3 | Kapalı kaynak dağıtımda sorun | CoreMusic lisans modeli belirlenmeli: AGPL uyumlu (kaynak açılımı) **veya** JUCE commercial lisans |
| ~~ASIO GPLv3 varyant~~ | ✅ KARAR VERİLDİ (2026-10-14): **GPLv3 açık varyant** — ürün GPLv3 modeline hizalanır (JUCE AGPLv3 ile tutarlı); tescilli varyant reddedildi | Karar: [[01-gelistirme-plani]] §7 #1 |
| LSP GPLv3 referans | Kod kopyalanırsa bulaşıcı | Yalnız kara-kutu referans; algoritmayı yeniden yaz |
| SoXR LGPL | Statik linkte kaynak sunma | libsamplerate tercih et |

**Authority:** Master Orchestrator · Embedded Engineer (L1)
**Source of Truth:** `.ai/CLAUDE.md` §19 · §24 — bu dosya envanter uzantısıdır
