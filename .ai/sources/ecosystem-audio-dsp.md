---
title: "Kaynak: ASIO/WASAPI Rehberi + Ses/DSP Açık Kaynak"
type: kaynak
raw_path: raw/ecosystem/
created: 2026-10-06
updated: 2026-10-06
sources: [ecosystem-audio-dsp]
tags: [ekosistem, asio, wasapi, juce, dsp, lisans, surucu, k2, k3, k0]
---

# Kaynak Özeti — ASIO/WASAPI Rehberi + Ses/DSP Açık Kaynak Ekosistemi

## Genel Özet

İki ham dosya: `asio-wasapi-rehber.md` (seri 6/6 — ASIO SDK açık kaynak lisans dağıtım kuralları, WASAPI exclusive vs shared, evrensel yerleşik ASIO'nun yalnız 64-bit sürücü çalıştırması; hedef K2/K0) ve `ses-dsp-acik-kaynak.md` (seri 3/6 — JUCE, DaisySP, YUP, Awesome-Audio-DSP, Dusk IPC, OpenStudio hybrid; hedef K3/K10/K2). İkisi de exa 2026-09-24 doğrulamasına dayanır; lisans seçimi ADR gerektirir, kod kopyası yoktur.

## Ana Fikirler

1. **ASIO SDK açık kaynak lisansla yayınlandı** (exa 2026-09-24) → dağıtılabilir bağımlılık; ama lisans metni bağlayıcıdır. Üç boyut: atıf (LICENSE + About/NOTICE), modifikasyon (değişiklik açıkta + lisans metni korunur), mülkiyet (3. taraf ASIO sürücü kodu SDK'ye karışmaz).
2. **Evrensel yerleşik (universal built-in) ASIO yalnız 64-bit sürücü çalıştırır; 32-bit ASIO sürücüsü desteklenmez** → K0 hedefi 64-bit olmalı, "32-bit only" seçeneği RED (asio §5.3 ADR şablonu: (a) 64-bit only ÖNERİLEN, (b) 32+64 çift hedef maliyetli/kazanımsız, (c) 32-bit only REDDEDİLDİ).
3. **K2 fallback zinciri:** ASIO (en düşük gecikme) → WASAPI exclusive (mix yok, cihaz kilitli) → WASAPI shared (uyumluk); her halkada buffer boyutu yapılandırılabilir + `xrun_count` metriği K14'e.
4. **WASAPI karşılaştırması:** exclusive mod uygulamanın cihazın tamamını devralır, mix motorunu atlar; shared mod sistem mix'ine katılır; ASIO işletim sistemini atlayarak sürücüye doğrudan çift taraflı lock-free `bufferSwitch` callback'i ile bağlanır.
5. **Gecikme sözleşmesi:** "en düşük gecikme" tek sayı değil — buffer boyutu + sürücü yolu + xrun (underflow/overflow) toleransı birlikte sözleşmedir; gecikme öncelikle buffer + sürücü + donanım tarafından belirlenir.
6. **JUCE lisans ikilemi (K3/K10/K2):** JUCE = GPL v3 / ticari (commercial). Kapalı kaynak CoreMusic hedefi GPL ile uyumsuz → ticari lisans ADR'si VEYA permissive stack (S3: YUP ISC + DaisySP + PortAudio/miniaudio) gerekir (ses-dsp §5.3 ADR şablonu, Durum: ÖNERİ).
7. **JUCE modülleri:** `juce_audio_devices` (AudioDeviceManager/AudioIODeviceType → K2/K10), `juce_audio_processors` (AudioProcessor → K3), `juce_gui_basics` + `juce_audio_utils` (K3 UI), `juce_core` (K4 yardımcılarıyla çakışma riski işaretli).
8. **DSP kütüphaneleri:** YUP 148★ ISC — `Effect → EffectChain → FxContainer` hiyerarşisi, efektler Delay/Chorus/Phaser/Distortion/Compressor, header-based C++17 · DaisySP (donanımdan bağımsız algoritma katmanı) · Awesome-Audio-DSP 1.289★ (tarama matrisi: Frameworks/Synthesis/Analysis/Filters/Effects/Spatial/Media) · OL_DSP, wolfsound/DSP-in-Plugins, gordongood/spa-build (lisanslar ⚠ VERIFICATION REQUIRED).
9. **Dusk Studio çok süreçli IPC (crash izolasyonu dersi):** Linux POSIX shm + eventfd · macOS shm_open + kqueue · Windows CreateFileMapping + WaitOnAddress — GUI ve audio ayrı süreçlerde, lock-free paylaşımlı bellek; kod kopyalanmaz, desen/API isimleri referans alınır.
10. **OpenStudio hybrid:** JUCE C++ motor + React UI + WebView2 köprüsü `window.__JUCE__` (fallback `console.log`) — Efectirim webview senaryosu için S4 referansı.
11. **ASIO SDK lisans gate (release gate, 7 madde):** LICENSE repoda mı · modifikasyon metni korundu mu · yürütülebilir dağıtıma atıf eklendi mi · değiştirilen kaynaklar lisans uyumlu yayınlandı mı · 3. taraf sürücü kodu karıştırılmadı mı · AGPL izi yok mu · PCM5122 izi yok mu → tamamı işaretli değilse release BLOKE.

## Önemli Alıntılar/Veriler

- **⚠ Star çelişkisi (raw içi):** `index.md` §3.6 JUCE'yi **8.811★** yazar; `ses-dsp-acik-kaynak.md` (§3.1, §7) ve `asio-wasapi-rehber.md` (§3.9, §7) **8.011★** yazar → iki ham dosya arasında sayısal çelişki, `⚠ VERIFICATION REQUIRED` ( güncel değer depodan okunmadı).
- **⚠ ASIO lisansının tam adı yok:** exa "açık kaynak lisans" dedi; spesifik lisans adı/şart metni gelmedi (asio §3.8 madde 2) → LICENSE dosyası okunarak doğrulanacak, uydurma yazılmaz.
- ASIO/WASAPI kriter tablosu: cihaz erişimi · gecikme (ASIO en düşük, exclusive düşük, shared orta) · sürücü gerekliliği · çakışma riski · CoreMusic rolü (ASIO 1., exclusive 2., shared 3. tercih).
- JUCE AudioIODeviceType seçim tablosu: 64-bit ASIO var → ASIO · ASIO yok/32-bit sürücü → WASAPI exclusive · paylaşımlı kullanım → WASAPI shared · JUCE lisansı seçilmemişse → doğrudan WASAPI/ASIO API.
- Lisans matrisi (ses-dsp §3.6.1): JUCE ⚠ ADR · YUP ISC ✅ · DaisySP / OL_DSP / wolfsound / Dusk ⚠ VERIFICATION REQUIRED · AGPL ❌ asla · PCM5122 ❌ yasaklı.
- ASIO SDK bağımlılık kaydı şeması (§3.7.1): sürüm sabit (tag/hash, reproducible build) · LICENSE metni pakete kopyalanır · AGPL/PCM5122 izi yasak.
- Efekt Eviii modül haritası 9 satır: Delay, Chorus, Phaser, Distortion, Compressor (YUP/ISC referans hazır) · EQ (RBJ biquad), Reverb (FDN/Schroeder — lisans doğrula), Spatial/pan (VBAP/equal-power), Meter/analysis (RMS/FFT, K14'e devredilebilir).
- 4 senaryo (her iki dosyada): S1 kapalı-kaynak hedef → ticari JUCE/permissive · S2 GPL hedef → JUCE ana çerçeve · S3 permissive-only stack · S4 süreç izolasyonu + web UI (Dusk + `window.__JUCE__`).
- K0 ADR şablonu "Windows Platform Hedefi — 64-bit Only" Durum: ÖNERİ (2026-09-24); sonuc `.ai/DECISIONS.md`'ye yazılacak (bu ingest'te yazılmadı).

## Bu Kaynaktan Oluşan/Güncellenen Wiki Sayfaları

[[ecosystem-audio-dsp]] (yeni)
