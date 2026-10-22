---
title: "CoreMusic — Ses & Donanım Kuralları (Neva Engine · Class AB)"
type: guide
category: governance
date: 2026-10-22
updated: 2026-10-22
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/CLAUDE.md §19, §23 · .ai/AGENTS.md §16 (Embedded/DSP/HW)"
docType: claude
---

# CoreMusic — Ses & Donanım Kuralları (Audio & Hardware Rules)

**docType:** claude · **Klasör:** `.agents/rules/` · **Sorumlu:** embedded-engineer + dsp-firmware-engineer + audio-hardware-engineer

**Zorunlu Bağlantılar:** [[RULES]] · [[architecture-rules]] · [[../../.ai/CLAUDE]]

> **eli10:** Ses motoru gerçek zamanlıdır: callback'te allocation/lock yok; donanımda Class AB zorunlu, PCM5122 yasak.

---

## 1. Amaç

Neva Engine (C++20), DSP zinciri ve donanım (Class AB, DAC/ADC, XMOS) kurallarını vault SSOT'larından toplar. Embedded kalite hedefi: **Zero-allocation, lock-free, noexcept** — `.ai/AGENTS.md` §16.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `*.cpp` / `*.h` (Neva Engine, OS audio backend) | Port/servis haritası → [[architecture-rules]] §3.6 |
| DSP chain, EQ/reverb, ASIO/WASAPI sürücü | Web/API yüzeyi → [[backend-rules]] |
| Donanım seçimi (DAC/ADC, amplifikatör, PCB/BOM kararları) | Şifreleme → [[security-rules]] |

---

## 3. Mimari

### 3.1 Ses Motoru Standartları (`@.ai/CLAUDE.md` §19)

| Özellik | Değer |
|---------|-------|
| Sample Format | **Float32** (32-bit) |
| Sample Rate | **48kHz** standart |
| Kanal | 2.0 → 8.1 (7.1 surround) |
| Latency Hedefi | **<10ms (ASIO)** · **<20ms (WASAPI)** |
| DSP Efektleri | EQ, Reverb, Compressor, Limiter |
| Reverb Modları | Geniş Konser, Düğün Salonu, Oda, Stüdyo |
| DSP Chain | 31-band EQ, 4 Reverb (K3 katmanı) · Dolby Atmos, DTS:X (hedef) |

### 3.2 C++ Guardrails (`@.ai/CLAUDE.md` §19 + §5 K3)

**Zero-allocation · lock-free · noexcept · cache-line alignment (64-byte).**

| Kural | Değer |
|-------|-------|
| RT callback | Lock, allocation, I/O **yasak**; buffer'lar baştan allocate |
| Bağımlılık | K3 → K2 (sürücü) sıkı bağımlı; sıfır gecikme |
| Fallback | ASIO cihaz kaybı → WASAPI fallback (Edge Case) |
| Exclusive | ASIO Exclusive Lock — aynı anda tek uygulama (§23 #6) |
| OS soyutlama | JUCE 9 + ASIO SDK 2.3.4; `embedded-engineer` OS audio backend soyutlar |

### 3.3 Donanım Kuralları (`@.ai/CLAUDE.md` §5 K1, §12, §23)

| Kural | Değer |
|-------|-------|
| Amplifikatör topolojisi | **Class AB — Class D YASAK** (kök `CLAUDE.md` sabiti) |
| Transistör | MJL21194/MJL21193 (ADR-089) |
| DAC/Codec | **PCM3168A / AK4458** — **PCM5122 KESİNLİKLE YASAK** (8.1 surround'da sistem hatası — H001 REJECT, ADR-038) |
| USB Audio SoC | XMOS XU316 |
| Boost | LM5122 (TI) |
| DC offset | Class AB amfide **>0.5V DC offset → koruma rölesi** (§23 #7) |
| Sinyal zinciri | DC-Only; parazit yasak (K1 guardrail) |
| PCM5122 öneri ihlali | Edge Case #8: PCM5122 görüldü → PCM3168A/AK4458 öner |

### 3.4 Firmware (dsp-firmware-engineer)

I2S/TDM konfigürasyonu, XMOS XU316 firmware şartnamesi, buffer/DMA planı; donanım topolojisine dokunmaz (rol sınırı — `.ai/AGENTS.md` §4).

---

## 4. Kurallar

| # | Kural | İhlal |
|---|-------|-------|
| 1 | RT callback'te allocation/lock | Revert; süreç durur (Embedded) |
| 2 | PCM5122 kullanımı | H001 REJECT + PCM3168A/AK4458 öner |
| 3 | Class D amfi önerisi | Reddedilir (Class AB sabiti) |
| 4 | Domain boundary: `*.cpp`/`*.h` = embedded agent | Layer violation → revert |
| 5 | Donanım değişikliği (BOM/PCB) | Kullanıcı onayı (Guardrail #14) |
| 6 | Ölçüm/sürücü iddiası | Disk/ölçüm kanıtı; yoksa `VERIFICATION REQUIRED` |

---

## 5. Workflow

```text
Ses/donanım işi (DSP/C++/sürücü/BOM)
  → Vault: @.ai/CLAUDE.md §19 + §5 (K1-K3) + .ai/projects/NevaEngine/*.md (38 md)
  → Skill: audio-engine-cpp (kod) / hardware-electronics (donanım)
  → Tasarım/şartname üret (zero-alloc, latency bütçesi, worst-case tolerans)
  → Ölçüm/kanıt (ASIO latency, THD+N) — kanıtsız "bitti" geçersiz
  → MO: log.md
```

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter 7 alan + docType | Mevcut |
| 2 | Ses standartları vault §19 ile birebir | Float32 · 48kHz · <10/<20ms |
| 3 | C++ guardrails 4'lü | zero-alloc · lock-free · noexcept · cacheline |
| 4 | PCM5122 yasağı + Class AB sabiti | §3.3 |
| 5 | DC offset rölesi uyarısı | §23 #7 karşılığı var |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Anayasa §19 | [[../../.ai/CLAUDE]] | Ses standardı |
| Anayasa §5 K1-K3 | [[../../.ai/CLAUDE]] | Donanım/sürücü/motor |
| Uyarılar §23 | [[../../.ai/CLAUDE]] | PCM5122 · DC offset · ASIO lock |
| Neva Engine vault | `@.ai/projects/NevaEngine/` (38 md; ilk dosya: `00-sdk-lib-envanter.md`, plan: `01-gelistirme-plani.md`) | 2026-10-15 ölçüm — kök `index.md` YOK, dosya adları gerçek |
| DSP vault | `.ai/electronic/dsp/` (10 md) · `.ai/electronic/firmware/` (7 md) | 2026-10-14 ölçüm |
| Skills | `.claude/skills/audio-engine-cpp/` · `.claude/skills/hardware-electronics/` | Üretim kapıları |
| Master | [[RULES]] §4.2 | Özet |

**Last Updated:** 2026-10-22 · **Şablon:** context-template (Guardrail #16)
