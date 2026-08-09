---
type: adr
category: electronics
title: "ADR-080: Electronics Development Workflow"
date: 2026-08-10
updated: 2026-08-10
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-080: Electronics Development Workflow

**Status:** Active (güncellenebilir)
**Kategorisi:** Electronics
**İlgili Agent:** [[.agents/embedded-engineer]]

---

## 1. Context

CoreMusic ELECTronics platformu, 6 katmanlı (Hardware → Firmware → Driver → DSP → Backend → Frontend) bir geliştirme süreci gerektirir. Her katman farklı uzmanlık alanı ve araç seti gerektirir. Standart bir geliştirme süreci tanımlanması gerekmektedir.

## 2. Decision

CoreMusic Electronics geliştirme süreci **20 fazlı** bir yaşam döngüsüne sahiptir:

### 2.1 Faz Grupları

| Grup | Fazlar | Amaç |
|------|--------|------|
| Vizyon & Analiz | Faz 1-5 | Gereksinim analizi, rekabet, risk |
| Donanım Tasarımı | Faz 6-10 | PCB, bileşen, güç, termal |
| Firmware & Driver | Faz 11-15 | RTOS, boot, DSP firmware, driver |
| Yazılım & DSP | Faz 16-18 | Audio engine, DSP chain, API |
| Test & Doğrulama | Faz 19-20 | Entegrasyon, validasyon, sertifikasyon |

### 2.2 Hard Gates

| Faz | Hard Gate | Açıklama |
|-----|-----------|----------|
| Faz 3 | Gereksinim Onayı | Ürün gereksinimleri onaylanmadan devam edilmez |
| Faz 7 | PCB Onayı | PCB tasarımı onaylanmadan üretime geçilmez |
| Faz 10 | Donanım Doğrulama | Hardware test geçmeden firmware başlanmaz |
| Faz 18 | Yazılım Entegrasyonu | Tüm modüller entegre edilmeden test başlamaz |

## 3. Alternatives

| Alternatif | Artıları | Eksileri | Red Sebebi |
|------------|----------|----------|------------|
| Agile Sprint | Esnek | Donanım için uygun değil | Donanım iterative değildi |
| Waterfall | Basit | Çok yavaş | Çok katı |
| **20-Faz Hybrid** | Hem esnek hem kontrollü | Karmaşık | **Kabul edildi** |

## 4. Consequences

### Positive
- Her katman için net sorumluluk ve çıkış noktaları
- Hard Gate'ler ile kalite garantisi
- Web-doğrulanmış standartlar (XMOS lib_i2s v6.0.1, JUCE 9, ASIO SDK 2.3.4)
- Microsoft in-box ASIO driver desteği

### Negative
- 20 faz karmaşık görünür
- Her faz için ayrı ekip gerektirir

### Risks
- Fazlar arası iletişim kopukluğu → Mitigasyon: Message Bus + API Gateway
- Donanım hatası geç tespit → Mitigasyon: Faz 10'da donanım doğrulama

## 5. Web-Verified References

| Kaynak | Doğrulama |
|--------|-----------|
| XMOS lib_i2s v6.0.1 | XMOS GitHub, 32 commits |
| JUCE 9.0.0 | juce.com, Jul 21 2026 |
| ASIO SDK 2.3.4 | Steinberg, Oct 15 2025 |
| USB Audio Class 2.0 | usb.org, Apr 2025 errata |
| Microsoft in-box ASIO | Microsoft, 2026 preview |

## 6. Cross References

| Referans | Amaç |
|----------|------|
| [[electronic/development-workflow]] | Detaylı 20 faz planı |
| [[architecture/03-contracts/development-standards]] | Geliştirme standartları |
| [[.claude/rules/engineering-rules]] | Mühendislik kuralları |
| [[ADR-061-electronics-architecture]] | Elektronik mimarisi |
| [[ADR-062-dsp-pipeline-architecture]] | DSP pipeline |
| [[ADR-063-hardware-design-standards]] | Donanım tasarım standartları |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-10
**Mode:** Red Team · Human Mode · Truth Mode
