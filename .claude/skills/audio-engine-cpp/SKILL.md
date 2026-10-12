---
name: audio-engine-cpp
description: "Use when touching Neva Engine, JUCE 9, ASIO, DSP chain, or realtime C++ audio code — Tetikleyiciler: 'C++ ses motoru', 'DSP kodla', 'ASIO gecikme', 'EQ/reverb ekle'."
license: MIT
metadata:
  version: 3.0.0
  format: claude-skill-v3
  author: Bayram Ali (ULTRATHINK Engineering)
  category: audio-engine
  tags: [cpp20, juce, asio, dsp, realtime, coremusic]
  updated: 2026-10-07
---

# Audio Engine C++ — Neva Engine

## §1 Genel Bakış

CoreMusic **Neva Engine** (K3 Ses İşleme Motoru) ve K2 sürücü katmanı üzerindeki
gerçek-zamanlı C++ ses kodu için uygulama skill'i. Kapsam: audio callback'ler,
DSP zinciri (EQ → reverb → crossover → limiter), sürücü/gecikme yapılandırması,
kanal matrisi (1.0 → 8.1) ve Google Test kapsamı.

Doğrulanmış çekirdek spec (kaynak: `.ai/CLAUDE.md` §19 · §24 · `.ai/PROJECTS.md` §7.1.1):

| Özellik | Değer |
|---------|-------|
| Dil / Framework / SDK | C++20 · JUCE 9 · ASIO SDK 2.3.4 |
| Örnek formatı / hız | Float32 (32-bit) · 48kHz standart (96/192kHz desteği) |
| Kanal | 1.0 Mono → 8.1 Surround (+1 LFE) |
| Gecikme hedefi | <10ms (ASIO) · <20ms (WASAPI) |
| DSP | 31-band EQ · 4 reverb modu · compressor · limiter (True Peak) · crossover |
| Kalite | THD+N <%0.005 · SNR >105dB · 64-bit float yol haritası |
| C++ Guardrails | Zero-allocation · lock-free · noexcept · cache-line (64-byte) |
| Test | Google Test · coverage ≥80% (min) / ≥90% (hedef) |

## §2 When-to-use

| Trigger | Ne zaman | Bu skill |
|---------|----------|----------|
| 'C++ ses motoru' | Neva Engine / audio callback kodu | ✅ |
| 'DSP kodla' | EQ, reverb, compressor, limiter, crossover değişikliği | ✅ |
| 'ASIO gecikme' | Buffer/sürücü/gecikme yapılandırması | ✅ |
| 'EQ/reverb ekle' | DSP zincirine efekt/parametre ekleme | ✅ |
| Vault `.md` / prompt işi | Dokümantasyon kurgusu | ❌ → `prompt-maker` / `.ai/.templates/` |

## §3 Otonom Çalışma Protokolü

1. **Analiz:** Talep incelenir; hangi katmana (K3 DSP mi, K2 sürücü mü) değdiği belirlenir.
2. **Doğrulama (Truth Mode):** Vault kaynakları çapraz teyit edilir; teyitsiz iddia
   → `⚠️ VERIFICATION REQUIRED`; vault çelişkisi → DUR + kullanıcıya sor (Guardrail #12).
3. **Execution — okuma:** İlgili referans okunur (callback kodu → `references/realtime-rules.md`,
   gecikme/sürücü → `references/latency-targets.md`, DSP zinciri → `references/dsp-chain-map.md`).
4. **Execution — kod:** Guardrail'ler uygulanır (zero-allocation, lock-free, noexcept,
   64-byte align); yasaklı kalıp (callback içinde `new`/`delete`, mutex, throw) → H001 RED.
5. **Test + Raporlama:** Google Test ile doğrulama (coverage ≥80% min / ≥90% hedef);
   sıfır halüsinasyon garantisi ile çıktı sunulur.

## §4 Zorunlu Okumalar

| Dosya | İçerik | Ne zaman okunur |
|-------|--------|-----------------|
| [references/realtime-rules.md](references/realtime-rules.md) | Zero-allocation / lock-free / noexcept / 64B kuralları + doğru-yanlış C++ örnekleri | Audio callback veya hot-path kodu yazmadan/incelemeden önce |
| [references/latency-targets.md](references/latency-targets.md) | ASIO <10ms / WASAPI <20ms, Float32/48kHz, exclusive mod notları, ölçüm yaklaşımı | Buffer, sürücü veya gecikme konfigürasyonunda |
| [references/dsp-chain-map.md](references/dsp-chain-map.md) | Sinyal zinciri, 31-band EQ, 4 reverb modu, 1.0→8.1 yönlendirme matrisi | DSP zincirine müdahale / kanal matrisi işinde |

## §5 Örnekler

| Dosya | Ne gösterir |
|-------|-------------|
| [examples/add-reverb-mode.md](examples/add-reverb-mode.md) | Yeni reverb modu ekleme: param struct → DSP insertion point → Google Test → doğrulama adımları (girdi → çıktı tam döngü) |

## §6 Truth Mode & Güvenlik

- **Zero Hallucination:** JUCE/ASIO/DSP API'si, sürüm, config veya dosya yolu tahminiyle
  yazılmaz; vault'ta karşılığı olmayan iddia → `⚠️ VERIFICATION REQUIRED`; bilinmeyen → `UNKNOWN`.
- **H001 Kritik Reddi:** Deprecated/uyumsuz/güvensiz yapı (callback içinde tahsis, kilitli
  hot-path, vault'a ters katman bağımlılığı) reddedilir ve kullanıcı uyarılır.
- **Vault uyumu:** `.ai/` kurallarıyla (CLAUDE §7, AGENTS §5) çelişilemez → DUR + sor.
- **ASIO Exclusive Lock:** Aynı anda yalnız tek uygulama; çoklu deneme sürücü çökmesine
  yol açabilir (`.ai/brain.md` §Kritik Uyarılar #6) →exclusive cihaz erişimi kodu dikkatli yazılır.
- **Güvenlik:** API key/secret hardcode edilemez; yıkıcı komut (silme/deploy) kullanıcı
  onayına bağlıdır.
- **Test kapısı:** Google Test çalıştırılmadan "tamam" denmez; coverage <80% → TECH DEBT bildirimi.

## §7 Otorite & Vault Bağlantıları

| Kaynak | Bölüm | Bu skilldeki rolü |
|--------|-------|-------------------|
| `.ai/CLAUDE.md` | §19 Audio Engine Standards | Sample format, gecikme, DSP, reverb modları, C++ guardrails |
| `.ai/CLAUDE.md` | §24 Dependencies | C++20 · JUCE 9 · ASIO SDK 2.3.4 sürüm otoritesi |
| `.ai/VISION.md` | §17.2 Ses Sinyal Zinciri | Hardware DSP zinciri (XMOS → ADC → DSP → DAC → amfi) |
| `.ai/PROJECTS.md` | §7.1.1 / §7.1.7 | NevaEngine / DSP Engine spec (96/192kHz, Google Test, coverage) |
| `.ai/ecosystem/asio-wasapi-rehber.md` | §3.2–§3.4 | WASAPI exclusive vs shared, ASIO gecikme karakteri (2026-09-24 doğrulandı) |

SSOT sırası: `.ai/CLAUDE.md` > `.ai/AGENTS.md` > `.ai/WORKFLOW.md` > `.ai/brain.md` >
`.ai/index.md` > `.ai/.templates/` — harici kaynak (web/model hafızası) her zaman vault'un altındadır.

Anti-overthink: kök AGENTS.md §5 (MAX THINKING 7 madde) geçerlidir.

*CoreMusic Skill v3.0 — metadata.version: 3.0.0 — Updated: 2026-10-07*