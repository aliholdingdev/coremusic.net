---
type: system
category: electronic-architecture
title: "CoreMusic Electronics — Master Index"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic Electronics — Master Index

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[brain.md]] · [[index.md]]

## 1. Amaç

Bu dosya, CoreMusic ELECTRONICS platformunun tüm donanım, firmware, driver, DSP ve elektronik mimarisinin ana navigasyon noktasıdır. Uzun tabloların tamamı alt mimari indekslerinde (`dsp/index`, `drivers/index`, `amplifier/index`, `hardware/index`, `firmware/index`) taşınır; bu indeks yalnız yönlendirme ve sınır beyanı verir.

## 2. Quick Reference

| İhtiyaç | İlk Adım |
|---------|----------|
| DSP Engine | [[dsp/index]] |
| Driver Framework | [[drivers/index]] |
| Amplifier | [[amplifier/index]] |
| Hardware Design | [[hardware/index]] |
| Firmware | [[firmware/index]] |
| Software Architecture | [[electronic/software-architecture]] |
| Service Architecture | [[electronic/service-architecture]] |
| Device Ecosystem | [[electronic/device-ecosystem]] |
| Development Workflow | [[electronic/development-workflow]] |
| Electronics Diagrams | [[diagrams/electronics-diagrams]] |

## 3. Electronics Katmanları

Hardware → Firmware → Driver → DSP Engine → Amplifier → Software Architecture → Service Architecture → Device Ecosystem → Middleware (`architecture/06-audio/`) — her katmanın indeksi §2 tablosundadır.

## 4. Device Families

| Aile | Cihaz Sayısı | Referans |
|------|-------------|----------|
| Home Audio | 7 | [[electronic/device-ecosystem]]#3.1 |
| Car Audio | 5 | [[electronic/device-ecosystem]]#3.2 |
| Professional Audio | 6 | [[electronic/device-ecosystem]]#3.3 |
| Embedded Audio | 4 | [[electronic/device-ecosystem]]#3.4 |

## 5. ADR Referansları

Bağlayıcı ADR'ler: [[ADR-017-dsp-hardware-mode]] (DSP hardware mode · XMOS, JUCE, ASIO) · [[ADR-025-professional-eq-system]] (31-band EQ) · [[ADR-038-8.1-sound-card-chip-selection]] (PCM3168A + XMOS XU316) · [[ADR-061-electronics-architecture]] (L6 Layer) · [[ADR-062-dsp-pipeline-architecture]] (15 aşamalı pipeline) · [[ADR-063-hardware-design-standards]] (PCB, EMI/EMC).

## 6. Kapsam Sınırı ve Dürüstlük Beyanı

| Konu | Durum | Dayanak |
|------|-------|---------|
| Repo'da electronics/devre/firmware kaynak kodu | **YOK** — CoreMusic repo'su PHP 8.x + JS web platformudur | Dizin taraması: `*.cpp` / `*.h` / `*.ino` = 0 |
| Ses çevirici + veri tipi | Yalnız PCM3168A (ADR-038 H001) · 32-bit float (ADR-017) | Bağlayıcı ADR'ler |
| Buffer / örnekleme hedefi | 512 sample · 48 kHz — **PLANNED** (ölçüm yapılmadı) | Şartname · `⚠️ VERIFICATION REQUIRED` |
| ASIO Exclusive Lock | Tek uygulama · çakışma = 0 zorunlu | L0 kuralı |
| JUCE 9 · ASIO SDK 2.3.4 sürüm kanıtı | **YOK** — diskte doğrulanmadı | `⚠️ VERIFICATION REQUIRED` |
| Yıldırım/EMC/termal sayısal değerleri | Vault içi tasarım iddiasıdır, ölçülmedi | `UNKNOWN` |

**Yasak yüzeyler (bu dosya dahil, dokunulmaz):** `.ai/electronic/AGENTS.md` · `.ai/electronic/CLAUDE.md` · alt klasör `AGENTS.md` / `CLAUDE.md` dosyaları · `.ai/architecture/**` · `*.php` · `*.js` · `*.css` · `*.sql`.

**İmla koruması:** Vault içindeki bilinen yazım kaymaları (`electroncis`, `donanaım-type`, `identitiy`, `integraion`, `comilance`, `comere`, `recomenataon`) **dosya/dizin adı olduğu için düzeltilmez**; düzeltilirse mevcut wiki-link'ler kırılır.

## 7. Web Kaynakları

Her satır 2026-10-13 tarihinde HTTP 200 ile doğrulanmış, vault URL envanteriyle kesişmeyen benzersiz bir kaynaktır; kaynaklar yalnız bu bölümdeki iddiaları destekler, vault içi donanım değerleri (ölçülmüş THD/SNR, termal, PCB katman sayısı) için kaynak **vault'tur** ve `UNKNOWN` işaretlenmeden dış kaynakla desteklenemez.

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| Mikrodenetleyici (MCU) temel tanımı ve gömülü sistemlerde rolü | https://www.arm.com/technologies/iot/what-is-an-mcu | 2026-10-13 | HTTP 200 ✓ |
| Arm denetleyici IP ailesi (Cortex-M serisi) ürün listesi | https://www.arm.com/products/silicon-ip-mcus | 2026-10-13 | HTTP 200 ✓ |

## 8. Riskler ve Açık Sorular

| # | Soru | Durum |
|---|------|-------|
| 1 | Repo'da firmware/devre kodu yok; vault iddiaları firmware tarafında nasıl kanıtlanır? | `UNKNOWN` (kod kanıtı imkânsız) — `.ai/electronic/**` kaynağının açıkça beyanı |
| 2 | JUCE 9 / ASIO SDK 2.3.4 sürümleri diskte doğrulanamadı | `⚠️ VERIFICATION REQUIRED` — bağımlılık manifesti okuması |
| 3 | 512 sample / 48 kHz hedefi ölçülmedi (gerçek callback süresi bilinmiyor) | `⚠️ VERIFICATION REQUIRED` — gerçek ASIO cihazında ölçüm |

## 9. İlişkili Dosyalar (Wikilink Envanteri)

| Bölüm | Wikilink |
|-------|----------|
| Alt mimari | `[[dsp/index]]` · `[[drivers/index]]` · `[[amplifier/index]]` · `[[hardware/index]]` · `[[firmware/index]]` |
| Kök içerik | `[[electronic/software-architecture]]` · `[[electronic/service-architecture]]` · `[[electronic/device-ecosystem]]` · `[[electronic/development-workflow]]` · `[[electronic/core-music-electronics-overview]]` |
| ADR | `[[ADR-017-dsp-hardware-mode]]` · `[[ADR-025-professional-eq-system]]` · `[[ADR-038-8.1-sound-card-chip-selection]]` · `[[ADR-061-electronics-architecture]]` · `[[ADR-062-dsp-pipeline-architecture]]` · `[[ADR-063-hardware-design-standards]]` |
| Vault kökü | `[[CLAUDE.md]]` · `[[AGENTS.md]]` · `[[brain.md]]` · `[[index.md]]` |
| Dış | `[[architecture/06-audio/index]]` · `[[architecture/07-security/index]]` · `[[architecture/l0-infrastructure]]` · `[[architecture/10-network/index]]` |
| Diyagramlar | `[[diagrams/electronics-diagrams]]` |

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-10
**Mode:** Red Team · Human Mode · Truth Mode
