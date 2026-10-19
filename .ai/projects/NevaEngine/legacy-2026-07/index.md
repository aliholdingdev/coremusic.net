---
title: "Neva Engine — Legacy Spesifikasyon Arşivi (2026-07 Desktop Snapshot)"
type: index
category: audio-engine
version: 1.0.0
status: archived
authority: "SSOT: kaynak — C:\\Users\\MARHAN\\Desktop\\Yeni klasör (3) · import 2026-10-14"
updated: 2026-10-14
---

# Neva Engine — Legacy Arşiv İndeksi (2026-07 spesifikasyon seti)

**Zorunlu Bağlantılar:** [[../00-sdk-lib-envanter]] · [[../01-gelistirme-plani]] · [[../05-neva-mixer-web-pcm-plani]] · [[../06-gereksinimler-ve-farkliliklar]]

## §0 Arşiv Künyesi

| Alan | Değer |
|------|-------|
| Kaynak | `C:\Users\MARHAN\Desktop\Yeni klasör (3)` (desktop snapshot, dosya tarihleri 2026-07-02; içerik tarihi 2026-06-02) |
| İçe aktarma | 2026-10-14 · 23 dosya · 273.3 KB · UTF-8 (BOM'suz, TR karakter bütünlüğü read-tool ile doğrulandı) |
| Durum | **archived** — bu dosyalar ilk taslak spesifikasyonlardır; güncel SSOT `../00…06` + `../os/*` dosyalarıdır |
| Kural | Çelişkide **yeni (00-06) kazanır**; legacy yalnız derinlik (formül, mimari detay, referans implementasyon) için okunur |

## §1 Envanter (23 dosya) — önem sırası

### A. Yüksek değer — güncel planda henüz işlenmemiş derinlik

| Dosya | KB | İçerik | İlgili güncel belge |
|-------|----|--------|---------------------|
| `routing-matrix.md` | 21 | N×M bus routing, send/return FX sistemi, giriş kaynakları | 05 §3.1 (10 çıkış A1-A10) — **send/return detayı burada** |
| `spatial-audio.md` | 19 | Binaural HRTF, head tracking, kişiselleştirilmiş HRTF, oda simülasyonu | henüz işlenmedi — 06 R6 konser-salonu efektinin uzun vade komşusu |
| `vst3-hosting.md` | 19 | VST3 SDK, plugin tarama, **izole süreç sandbox**, plugin zinciri | 01 Faz planı — plugin hosting henüz faz kapsamında değil |
| `midi-system.md` | 18 | MIDI learn, mapping profilleri, transport kontrolü | 04 (RE'de MIDI kaynakları) — implementasyon detayı burada |
| `overview.md` | 16 | Proje tanımı, ekosistem, API mimarisi, headless servis modu | 01 (geliştirme planı) |
| `equalizer-31band.md` | 16 | IIR biquad implementasyonu, EQ zinciri mimarisi, preset sistemi | 02 (DSP zincir) — **biquad formülleri burada** |
| `audio-core.md` | 15 | JUCE AudioDeviceManager · ASIO/WASAPI/CoreAudio entegrasyon mimarisi | `os/01-04` (kod örnekleri) — mimari anlatım burada |
| `ai-models.md` | 24 | Ses upscaling ONNX modelleri, inference pipeline, AI auto-EQ | 01 Faz 5 (planda) — **24KB, en büyük tek belge** |

### B. Orta değer — EQ/DSP destek dokümanları

| Dosya | KB | İçerik |
|-------|----|--------|
| `equalizer-system.md` | 11 | Multi-band mimari, biquad C++ referans, spectrum analyzer (FFT), hedefler |
| `eq-dsp-chain.md` | 10 | FX chain sabit sıralama, channel strip, denoiser, gate, compressor |
| `eq-plugin-hosting.md` | 7 | VST3/LV2/CLAP slot mimarisi, scanner, per-strip zincir |
| `eq-ai-auto.md` | 7 | Tier 1 genre detection → Tier 2 ML fine-tune → Tier 3 çevresel kalibrasyon |
| `eq-theme-system.md` | 7 | Tema paketi yapısı, theme.json, ITCSS uyumlu CSS (web UI teması) |
| `eq-user-modes.md` | 7 | Ev / Araba / TV modları — **06 R7 tek-tuş preset ile doğrudan ilişkili** |

### C. Düşük değer — kısa referanslar

| Dosya | KB | İçerik |
|-------|----|--------|
| `eq-cross-platform.md` | 4 | Platform matrisi, slider/spectrum render, optimizasyon |
| `eq-presets.md` | 4 | Genre + user + auto preset, FxSound referans preset'leri |
| `index.md` | 0.4 | Stub başlık |
| `index-architecture.md` | 2 | L0-L3 altyapı/security/routing/presentation mimari stub |
| `index-security.md` | 1 | Güvenlik stub |

### D. C++ proje kuralları (`rules/` — 4 dosya, Faz 0 kodlama öncesi)

| Dosya | KB | İçerik |
|-------|----|--------|
| `rules/neva-engine-CLAUDE.md` | 19 | Neva Engine C++ proje kuralları (ilk nesil) |
| `rules/cpp-global-CLAUDE.md` | 20 | C++ projeleri genel kuralları |
| `rules/neva-engine-AGENTS.md` | 10 | Neva Engine agent tanımı (ilk nesil) |
| `rules/cpp-global-AGENTS.md` | 14 | C++ genel agent tanımı |

## §2 Düzeltme notları (zero-hallucination)

1. **README kaydı:** kaynak `projects/README.md` (2026-06-24) 3 proje sayar: `neva-engine` · `neva-player` · `wireless-connect`. Klasörde ayrıca `neva-connect` ve `shared` kuralları vardı — **alınmadı** (kapsam dışı: Neva Engine sessiz değil).
2. **Eski iddiaların durumu:** `overview.md` "JUCE 8.x" der; güncel vault spec **JUCE 9** (`00-sdk-lib-envanter`) — **çelişkide JUCE 9 kazanır**.
3. **keys.md etkisi:** bu arşiv gelmeden önce `keys.md` §6'da ghost olan `spatial-audio` / `vst3-hosting` / `routing-matrix` / `midi-system` hedefleri artık diskte var → §6'ya legacy satırları eklendi (2026-10-14).
4. **`index.md` stub'u:** arşiv kökündeki eski 0 KB stub ("# Neva Engine") bu indeksle **yerinden değiştirildi** (2026-10-14, write tamamen yazdı).

## §3 Okuma sırası (kural)

`00-06` (güncel) → ihtiyaç varsa `legacy-2026-07` (formül/detay) → çelişkide `00-06`.
