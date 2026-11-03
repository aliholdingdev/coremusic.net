---
title: "CoreMusic — Ses & Donanım Kuralları (Neva Engine · Discrete Class AB)"
type: guide
category: governance
date: 2026-10-22
updated: 2026-11-01
version: 2.0.0
status: active
authority: "Derived — SSOT: .ai/CLAUDE.md §19, §23 · .ai/.rules/kurallar.md · .ai/.rules/hata-listesi.md (HL-02)"
docType: claude
---

# CoreMusic — Ses & Donanım Kuralları (Audio & Hardware Rules)

**docType:** claude · **Klasör:** `.ai/.rules/` · **Sorumlu:** embedded-engineer + dsp-firmware-engineer + audio-hardware-engineer

**Zorunlu Bağlantılar:** [[RULES.md]] · [[kurallar.md]] · [[hata-listesi.md]] · [[architecture-rules.md]] · [[.ai/CLAUDE.md]]

> **eli10:** Ses motoru sıfır gecikmeli gerçek zamanlıdır (RT callback'te allocation/lock yok); donanımda discrete Class AB zorunludur, Class D ve PCM5122 kesinlikle yasaktır.

---

## 1. Amaç

Neva Engine (C++20), gerçek zamanlı DSP işleme zinciri ve donanım elektroniği (discrete Class AB amplifikatör, AK4458 DAC, PCM3168A codec, XMOS XU316) kurallarını vault SSOT'larından toplar. Embedded kalite hedefi: **Zero-allocation, lock-free, noexcept, SNR >105dB, THD+N <0.003%** (`.ai/AGENTS.md` §16).

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `*.cpp` / `*.h` (Neva Engine, OS audio backend, DSP chain) | Port/servis haritası → [[architecture-rules.md]] §3.6 |
| DSP chain: 31-band EQ, 4 Reverb, Compressor, Limiter | Web/API yüzeyi → [[backend-rules.md]] |
| ASIO SDK 2.3.4, WASAPI, PipeWire, ALSA sürücüleri | UI/UX kuralları → [[frontend-rules.md]] |
| Donanım topolojisi (Class AB, PSU, BOM, PCB, koruma rölesi) | Şifreleme/secret → [[security-rules.md]] |

---

## 3. Mimari

### 3.1 Ses Motoru Standartları (`@.ai/CLAUDE.md` §19)

| Özellik | Zorunlu Değer |
|---|---|
| Sample Format | **Float32** (32-bit single precision IEEE 754) |
| Sample Rate | **48kHz** standart (Hi-Fi stüdyo referansı) |
| Kanal Yapısı | 2.0 Stereo → 8.1 Surround (7.1 + Subwoofer / Height kanalları) |
| Latency Hedefi | **<10ms (ASIO)** · **<20ms (WASAPI)** |
| DSP Efekt Zinciri | 31-band Graphic/Parametric EQ, 4 Reverb, Compressor, Peak Limiter |
| Reverb Modları | Geniş Konser, Düğün Salonu, Oda, Stüdyo |
| İleri Hedefler | Dolby Atmos, DTS:X spatial rendering (K3 katmanı) |

### 3.2 C++20 Neva Engine RT Audio Guardrails

**Zero-allocation · lock-free · noexcept · cache-line alignment (64-byte).**

| Kural | Standart / İhlal Sonucu |
|---|---|
| **RT Callback İzolasyonu** | Gerçek zamanlı ses callback'i içerisinde `malloc`, `new`, `free`, `delete`, dosya I/O, `std::mutex`, `printf` veya sistem çağrısı **KESİNLİKLE YASAKTIR**. Bütün bellekler başlatma anında pre-allocate edilir. İhlal → anında revert. |
| **Lock-Free Veri İletimi** | Thread'ler arası iletişim tek üretici/tek tüketici (SPSC) lock-free ring buffer ile sağlanır. |
| **Önbellek Hizalama** | Ses verisi buffer'ları ve DSP katsayıları 64-baytlık cache-line sınırına hizalanır (`alignas(64)`). |
| **Exclusive Cihaz Kilidi** | ASIO Exclusive Lock: Oturum sırasında ses cihazına başka hiçbir uygulamanın müdahale etmesine izin verilmez. |
| **Sürücü Fallback** | ASIO cihaz bağlantısı koptuğunda sistem çökmez; otomatik olarak WASAPI fallback moduna geçer. |

### 3.3 Donanım & Elektronik Standartları (`HL-02` & `@.ai/CLAUDE.md` §23)

| Bileşen / Parametre | Zorunlu Standart | Açıklama / Yasaklar |
|---|---|---|
| **Amplifikatör Mimarisi** | **Discrete Class AB Zorunlu** | **Class D ve PWM mimarisi KESİNLİKLE YASAKTIR (`HL-02`).** Yüksek sadakat, düşük crossover distorsiyonu. |
| **Güç Transistörleri** | **ON Semi MJL21194 / MJL21193** | 200W, 16A complementary bipolar power transistors (ADR-089). |
| **DAC / Codec** | **AK4458 / PCM3168A** | 32-bit 8-kanal DAC / 24-bit 6ch ADC + 8ch DAC. **PCM5122 KESİNLİKLE YASAKTIR** (8.1 surround'da sistem hatası — H001 REJECT, ADR-038). |
| **USB Audio SoC** | **XMOS XU316** | 16 çekirdekli ses işlemcisi, asenkron USB aktarımı. |
| **Boost Dönüştürücü** | **TI LM5122** | Senkronize çok fazlı boost denetleyicisi. |
| **DC Offset Koruması** | **>0.5V DC → Röle Açma** | Hoparlör ve donanım güvenliği için >0.5V DC algılandığında koruma rölesi devreyi anında keser. |
| **Hedef Performans** | **SNR >105dB, THD+N <0.003%** | 1kHz referans sinyalinde ultra düşük gürültü ve distorsiyon. |

### 3.4 Firmware & DSP Ajan Sorumlulukları

- **embedded-engineer:** C++20 Neva Engine mimarisi, OS ses backend soyutlaması (JUCE 9 + ASIO SDK 2.3.4), sürücü yönetimi.
- **dsp-firmware-engineer:** I2S/TDM veri yolu konfigürasyonu, XMOS XU316 firmware şartnamesi, DMA transfer planlaması.
- **audio-hardware-engineer:** Class AB amfi hesaplamaları, termal bütçe, DC koruma devresi, PSU filtresi ve PCB/BOM doğrulaması.

---

## 4. Kurallar

| # | Kural | İhlal Sonucu |
|---|---|---|
| 1 | RT callback'te dynamic allocation veya lock kullanımı | Derhal revert + build durdurma |
| 2 | PCM5122 çipi kullanımı veya önerisi | H001 REJECT + AK4458/PCM3168A düzeltmesi |
| 3 | Class D amfi veya PWM önerisi (`HL-02`) | Tasarım reddi; Class AB zorunludur |
| 4 | Domain boundary: `*.cpp`/`*.h` dosyaları embedded agent yetkisindedir | Layer violation → revert |
| 5 | Donanım değişikliği (BOM/PCB/şema) | Kullanıcı onayı zorunludur (`KURAL-07`) |
| 6 | Ölçüm ve sürücü iddiaları | Disk/ölçüm kanıtı zorunludur; yoksa `⚠️ VERIFICATION REQUIRED` |

---

## 5. Workflow

```text
Ses / Donanım İşi (DSP / C++ / Sürücü / BOM)
  → Vault: @.ai/CLAUDE.md §19 + §5 (K1-K3) + kurallar.md + hata-listesi.md (HL-02)
  → Skill Kontrolü: audio-engine-cpp (kod) / hardware-electronics (donanım)
  → Şartname & Tasarım (zero-alloc, Class AB termal hesap, worst-case tolerans)
  → Ölçüm & Kanıt Doğrulama (ASIO latency <10ms, THD+N <0.003%, SNR >105dB)
  → durum.md güncelleme + log.md append (subagent commit atamaz — HL-03)
```

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---|---|
| 1 | Frontmatter 7 alan + docType | Mevcut |
| 2 | Ses standartları | Float32 · 48kHz · Latency <10ms / <20ms |
| 3 | C++ RT guardrails | Zero-alloc · lock-free · noexcept · cache-line align |
| 4 | Donanım topolojisi | Discrete Class AB zorunlu; Class D yasak (`HL-02`) |
| 5 | Çip doğrulaması | AK4458 / PCM3168A / XMOS XU316; PCM5122 yasak |
| 6 | Koruma rölesi | >0.5V DC offset röle koruması tanımlı |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|---|---|---|
| Anayasa §19 | [[.ai/CLAUDE.md]] | Ses motoru teknik standartları |
| Anayasa §5 K1-K3 | [[.ai/CLAUDE.md]] | Donanım / sürücü / ses işleme katmanları |
| Hata Listesi Kataloğu | [[hata-listesi.md]] | HL-02 Class AB amfi zorunluluğu |
| Temel Direktifler | [[kurallar.md]] | KURAL-04 teknik guardrail'lar |
| Master Kurallar | [[RULES.md]] | Genel aktivite kuralları |
| Neva Engine Vault | `.ai/projects/NevaEngine/` | 38 dokümantasyon dosyası |

---

**Authority:** Bayram Ali / Kurucu & Mimari Otorite  
**Last Updated:** 2026-11-01 · **Version:** 2.0.0
