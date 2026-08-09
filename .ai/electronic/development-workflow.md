---
type: workflow
category: electronic-development
title: "CoreMusic Electronics — Development Workflow (20 Faz)"
date: 2026-08-10
updated: 2026-08-10
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic Electronics — Development Workflow (20 Faz)

**Zorunlu Bağlantılar:** [[WORKFLOW.md]] · [[electronic/index]] · [[ADR-064-electronics-platform-architecture]] · [[ADR-061-electronics-architecture]]

---

## 1. Amaç

CoreMusic Electronics cihaz geliştirme sürecinin **20 aşamalı** standart workflow'unu tanımlar. Her cihaz (amfi, DSP, audio interface, RPi HAT vb.) bu workflow'u takip eder.

---

## 2. Workflow Genel Bakış

```
Fikir → Analiz → Araştırma → Mimari → ADR → DB → API → Güvenlik →
Donanım → Firmware → Driver → DSP → Backend → Frontend → Test →
Optimizasyon → Dokümantasyon → Release → Monitoring → Bakım
```

---

## 3. Faz Detayları

### Faz 1: Fikir (Idea)

| Alan | Değer |
|------|-------|
| **Amaç** | Yeni cihaz/ihtiyaç fikrini tanımla |
| **Çıktı** | `idea-brief.md` |
| **Sorumlu** | Product Owner / Vault Steward |
| **Süre** | 1-2 gün |
| **Hard Gate** | ❌ |

**Adımlar:**
1. Kullanıcı ihtiyacını tanımla
2. Pazar analizi yap
3. Rakip analizi yap
4. Hedef kitleyi belirle
5. Tahmini maliyet hesabı yap

---

### Faz 2: İhtiyaç Analizi (Requirements)

| Alan | Değer |
|------|-------|
| **Amaç** | Detaylı gereksinimleri yaz |
| **Çıktı** | `requirements.md` |
| **Sorumlu** | Business Analyst |
| **Süre** | 3-5 gün |
| **Hard Gate** | ❌ |

**Adımlar:**
1. Fonksiyonel gereksinimleri listele
2. Fonksiyonel olmayan gereksinimleri listele
3. Kullanıcı senaryolarını yaz (Use Cases)
4. Sınırlamaları belirle
5. Kabul kriterlerini tanımla

---

### Faz 3: Araştırma (Research)

| Alan | Değer |
|------|-------|
| **Amaç** | Teknik araştırmanın yap |
| **Çıktı** | `research-report.md` |
| **Sorumlu** | Technical Lead |
| **Süre** | 5-10 gün |
| **Hard Gate** | ❌ |

**Adımlar:**
1. Donanım araştırması (Chip, PCB, Codec)
2. Yazılım araştırması (Driver, SDK, API)
3. Standart araştırması (IEC, IEEE, CE, RoHS)
4. Maliyet araştırması
5. Tedarikçi araştırması
6. Web doğrulama yap (**Zero Hallucination**)

---

### Faz 4: Mimari Tasarım (Architecture)

| Alan | Değer |
|------|-------|
| **Amaç** | Sistem mimarisini oluştur |
| **Çıktı** | `architecture.md` + Mermaid diyagramları |
| **Sorumlu** | Solution Architect |
| **Süre** | 5-10 gün |
| **Hard Gate** | ✅ HARD GATE |

**Adımlar:**
1. L0-L6 katman yapısını uygula
2. Cihaz mimarisini tasarla
3. Bileşen diyagramlarını çiz
4. Veri akış diyagramlarını çiz
5. Entegrasyon noktalarını belirle
6. Risk analizi yap

**⚠️ HARD GATE:** Mimari onay olmadan sonraki faza geçilemez.

---

### Faz 5: ADR Oluşturma (Architecture Decision Record)

| Alan | Değer |
|------|-------|
| **Amaç** | Mimari kararları kaydet |
| **Çıktı** | `ADR-NNN-*.md` |
| **Sorumlu** | Architect |
| **Süre** | 2-3 gün |
| **Hard Gate** | ❌ |

**Adımlar:**
1. Karar bağlamını yaz (Context)
2. Alternatifleri listele
3. Artıları/eksileri değerlendir
4. Kararı ver ve gerekçelendir
5. Sonuçları tanımla
6. `decisions/draft/`'a ekle
7. Tech Lead onayı iste

---

### Faz 6: Veritabanı Tasarımı (Database)

| Alan | Değer |
|------|-------|
| **Amaç** | Veritabanı şemasını tasarla |
| **Çıktı** | `schema.sql` + ER diyagramı |
| **Sorumlu** | Data Engineer |
| **Süre** | 3-5 gün |
| **Hard Gate** | ❌ |

**Adımlar:**
1. Entity'leri tanımla
2. İlişkileri belirle
3. BCNF normalizasyonu uygula
4. Index stratejisini belirle
5. Migration planını oluştur
6. SQL dosyasını yaz
7. ER diyagramını çiz

**Kurallar:**
- ORM yasak (ADR-002)
- SELECT * yasak
- Prepared statement zorunlu
- BCNF zorunlu (ADR-040)

---

### Faz 7: API Tasarımı (API)

| Alan | Değer |
|------|-------|
| **Amaç** | API endpoint'lerini tasarla |
| **Çıktı** | `api-spec.yaml` (OpenAPI) |
| **Sorumlu** | Backend Architect |
| **Süre** | 3-5 gün |
| **Hard Gate** | ❌ |

**Adımlar:**
1. Resource'ları tanımla
2. Endpoint'leri tasarla
3. Request/Response formatlarını belirle
4. Hata kodlarını listele
5. Rate limiting stratejisini belirle
6. Versiyonlama stratejisini belirle
7. Security消毒 (Input Validation) tasarlama

---

### Faz 8: Güvenlik Tasarımı (Security)

| Alan | Değer |
|------|-------|
| **Amaç** | Güvenlik katmanını tasarla |
| **Çıktı** | `security-design.md` |
| **Sorumlu** | Security Engineer |
| **Süre** | 3-5 gün |
| **Hard Gate** | ❌ |

**Adımlar:**
1. OWASP Top 10:2025 kontrol listesini uygula
2. Authentication mekanizmasını tasarla
3. Authorization modelini belirle (RBAC)
4. Encryption stratejisini belirle (AES-256-GCM)
5. Session yönetimini tasarla
6. CSRF korumasını planla
7. CSP politikasını tanımla
8. Rate limiting stratejisini belirle

**Referanslar:**
- [[ADR-010-csrf-protection-strategy]]
- [[ADR-011-session-management]]
- [[ADR-022-database-hardened-security]]
- OWASP Top 10:2025

---

### Faz 9: Donanım Tasarımı (Hardware)

| Alan | Değer |
|------|-------|
| **Amaç** | Donanım bileşenlerini seç ve tasarla |
| **Çıktı** | `hardware-design.md` + şematik |
| **Sorumlu** | Hardware Engineer |
| **Süre** | 10-20 gün |
| **Hard Gate** | ❌ |

**Adımlar:**
1. İşlemci seçimi (ARM, x86, XMOS)
2. Bellek seçimi (RAM, Flash, EEPROM)
3. Ses codec seçimi (PCM3168A, AK4458)
4. DAC/ADC seçimi
5. Amplifier devresini tasarla
6. Güç yönetimini tasarla
7. PCB layout'ını planla
8. EMI/EMC considerations
9. Şematik çizimi

**Kurallar:**
- PCM5122 KESİNLİKLE yasak (H001)
- Sadece PCM3168A veya AK4458
- Modüler PCB tasarımı zorunlu

---

### Faz 10: Firmware Geliştirme (Firmware)

| Alan | Değer |
|------|-------|
| **Amaç** | Cihaz firmware'ını geliştir |
| **Çıktı** | `firmware/` dizini + binary |
| **Sorumlu** | Firmware Engineer |
| **Süre** | 15-30 gün |
| **Hard Gate** | ❌ |

**Adımlar:**
1. Bootloader geliştir
2. RTOS entegrasyonu yap
3. HAL (Hardware Abstraction Layer) oluştur
4. Driver entegrasyonu yap
5. GPIO/SPI/I2C/UART konfigürasyonu
6. USB entegrasyonu
7. OTA update mekanizması
8. Hata yönetimi
9. Güç yönetimi

**Referanslar:**
- [[electronic/firmware/index]]
- [[electronic/firmware/rtos]]
- [[electronic/firmware/boot-sequence]]

---

### Faz 11: Driver Geliştirme (Driver)

| Alan | Değer |
|------|-------|
| **Amaç** | İşletim sistemi sürücülerini geliştir |
| **Çıktı** | `drivers/` dizini + installable |
| **Sorumlu** | Driver Engineer |
| **Süre** | 15-30 gün |
| **Hard Gate** | ❌ |

**Adımlar:**
1. Driver mimarisini tasarla
2. ASIO driver geliştir (Windows) — veya Microsoft in-box ASIO driver'ı kullan (2026 preview, MIT license)
3. WASAPI driver geliştir (Windows)
4. ALSA driver geliştir (Linux)
5. CoreAudio driver geliştir (macOS)
6. Virtual Audio Driver geliştir
7. USB Audio Class 2.0 uygula (Spec Release 2.0, April 2025 errata)
8. Hot plug desteği ekle
9. Driver signing (Güvenlik)

**Web-Verified Referanslar:**
- Microsoft, Yamaha, Qualcomm ile in-box ASIO driver geliştiriyor (2026 public preview)
- USB Audio Class 2.0 Spec Release 2.0 (April 2025 errata)
- MIT license ile açık kaynak
- JUCE 9 ASIO SDK ile bundled (GPLv3 + proprietary dual-license)

**Desteklenen Platformlar:**
- Windows: ASIO, WASAPI, WDM
- Linux: ALSA, PipeWire
- macOS: CoreAudio
- Embedded: I2S, SPI

**Referanslar:**
- [[electronic/drivers/index]]
- [[electronic/drivers/audio-drivers]]
- [[ADR-017-dsp-hardware-mode]]

---

### Faz 12: DSP Geliştirme (DSP Engine)

| Alan | Değer |
|------|-------|
| **Amaç** | Ses işleme motorunu geliştir |
| **Çıktı** | `dsp/` dizini + DSP pipeline |
| **Sorumlu** | DSP Engineer |
| **Süre** | 20-40 gün |
| **Hard Gate** | ❌ |

**Adımlar:**
1. DSP pipeline'ı tasarla (15 aşama)
2. Input Gain geliştir
3. Noise Gate geliştir
4. High Pass / Low Pass Filter geliştir
5. Parametric EQ geliştir
6. Graphic EQ geliştir
7. Compressor geliştir
8. Limiter geliştir
9. Crossover geliştir
10. Delay geliştir
11. Reverb geliştir
12. Loudness geliştir
13. Output Gain geliştir
14. Output Routing geliştir

**Performans Hedefleri:**
- Ultra düşük gecikme (<10ms ASIO)
- Gerçek zamanlı işleme
- Zero-allocation
- Lock-free

**Referanslar:**
- [[electronic/dsp/index]]
- [[electronic/dsp/dsp-pipeline]]
- [[ADR-062-dsp-pipeline-architecture]]
- [[ADR-025-professional-eq-system]]

---

### Faz 13: Backend Geliştirme (Backend)

| Alan | Değer |
|------|-------|
| **Amaç** | PHP/Node.js backend servislerini geliştir |
| **Çıktı** | `src/` dizini + API endpoint'leri |
| **Sorumlu** | Backend Architect |
| **Süre** | 15-30 gün |
| **Hard Gate** | ❌ |

**Adımlar:**
1. Controller katmanını geliştir
2. Service katmanını geliştir
3. Repository katmanını geliştir
4. Middleware'leri uygula
5. API endpoint'lerini uygula
6. Authentication/Authorization uygula
7. Rate limiting uygula
8. Error handling uygula
9. Logging uygula

**Kurallar:**
- `declare(strict_types=1)` her PHP dosyasında
- PSR-12 coding standard
- Prepared statement zorunlu
- SELECT * yasak

**Referanslar:**
- [[architecture/l2-routing]]
- [[ADR-053-enterprise-router-architecture]]
- [[ADR-054-enterprise-composer-stack]]

---

### Faz 14: Frontend Geliştirme (Frontend)

| Alan | Değer |
|------|-------|
| **Amaç** | Vanilla JS arayüzünü geliştir |
| **Çıktı** | `assets/` dizini + JS/CSS dosyaları |
| **Sorumlu** | UI Designer |
| **Süre** | 15-30 gün |
| **Hard Gate** | ❌ |

**Adımlar:**
1. SPA router'ı geliştir
2. DOM manipülasyonu (DOMParser + TrustedTypes)
3. Component yapısını oluştur
4. State management geliştir
5. API entegrasyonu yap
6. Responsive tasarımı uygula
7. Accessibility (WCAG 2.2 AA) uygula
8. Tema desteği ekle (ADR-044)

**Kurallar:**
- Framework yasak (ADR-001) — Sadece Vanilla JS
- `innerHTML` yasak — DOMParser + TrustedTypes
- `var` yasak — `const` / `let`
- `eval()` yasak
- ITCSS + BEM zorunlu

**Referanslar:**
- [[architecture/l3-presentation]]
- [[ADR-001-vanilla-js-itcss]]
- [[ADR-044-dynamic-user-theme-engine]]

---

### Faz 15: Test (Testing)

| Alan | Değer |
|------|-------|
| **Amaç** | Kapsamlı test yap |
| **Çıktı** | Test raporları + Coverage |
| **Sorumlu** | QA Engineer |
| **Süre** | 10-20 gün |
| **Hard Gate** | ✅ HARD GATE |

**Adımlar:**
1. Unit testleri yaz (≥%80 coverage)
2. Integration testleri yaz
3. E2E testleri yaz (Playwright)
4. API contract testleri yaz
5. Performance testleri yap
6. Security testleri yap
7. Hardware-in-the-loop test
8. Regression testleri yap

**Test Piramidi:**
- Unit: %70
- Integration: %20
- E2E: %10

**Minimum Coverage:** ≥%80 (Hedef: ≥%90)

**⚠️ HARD GATE:** Coverage %80 altına düşerse production'a çıkılamaz.

---

### Faz 16: Optimizasyon (Optimization)

| Alan | Değer |
|------|-------|
| **Amaç** | Performans optimizasyonu yap |
| **Çıktı** | Optimizasyon raporu |
| **Sorumlu** | Performance Engineer |
| **Süre** | 5-10 gün |
| **Hard Gate** | ❌ |

**Adımlar:**
1. CPU profiling yap
2. Memory profiling yap
3. I/O profiling yap
4. Database query optimizasyonu
5. Cache stratejisi uygula
6. Lazy loading uygula
7. Bundle optimization (frontend)
8. Gecikme optimizasyonu (DSP)

**Hedefler:**
- TTFB <200ms
- API yanıt <100ms
- DSP gecikme <10ms (ASIO)
- CPU kullanımı <%50 (normal load)

---

### Faz 17: Dokümantasyon (Documentation)

| Alan | Değer |
|------|-------|
| **Amaç** | Kapsamlı dokümantasyon yaz |
| **Çıktı** | `docs/` dizini |
| **Sorumlu** | Technical Writer |
| **Süre** | 5-10 gün |
| **Hard Gate** | ❌ |

**Adımlar:**
1. API dokümantasyonu yaz (OpenAPI)
2. Kullanım kılavuzu yaz
3. Developer kılavuzu yaz
4. Architecture dokümanını güncelle
5. README dosyalarını yaz
6. CHANGELOG oluştur
7. Deployment kılavuzu yaz
8. Troubleshooting kılavuzu yaz

---

### Faz 18: Release

| Alan | Değer |
|------|-------|
| **Amaç** | Ürünü yayınla |
| **Çıktı** | Release notes + binary |
| **Sorumlu** | DevOps Engineer |
| **Süre** | 2-3 gün |
| **Hard Gate** | ✅ HARD GATE |

**Adımlar:**
1. Pre-deployment checklist uygula
2. Tüm testlerin geçtiğini doğrula
3. Vault-sync yap
4. Kullanıcı onayını al
5. Production'a deploy et
6. Health check'leri kontrol et
7. Post-deployment validation yap
8. Release notes yayımla

**⚠️ HARD GATE:** Kullanıcı onayı olmadan production'a çıkılamaz.

---

### Faz 19: Monitoring

| Alan | Değer |
|------|-------|
| **Amaç** | Sistem izleme altyapısını kur |
| **Çıktı** | Monitoring dashboard |
| **Sorumlu** | DevOps Engineer |
| **Süre** | 3-5 gün |
| **Hard Gate** | ❌ |

**Adımlar:**
1. Health check endpoint'leri ekle
2. Metrics toplama (CPU, RAM, Disk, Network)
3. Log toplama (centralized)
4. Alerting konfigürasyonu
5. Dashboard oluştur
6. Uptime monitoring
7. Error tracking
8. Performance monitoring

---

### Faz 20: Bakım (Maintenance)

| Alan | Değer |
|------|-------|
| **Amaç** | Sürekli bakım ve güncelleme |
| **Çıktı** | Bakım raporları |
| **Sorumlu** | Support Engineer |
| **Süre** | Sürekli |
| **Hard Gate** | ❌ |

**Adımlar:**
1. Hata düzeltmeleri yap
2. Güvenlik güncellemeleri uygula
3. Performans optimizasyonu yap
4. Yeni özellikler ekle
5. Kullanıcı geri bildirimlerini değerlendir
6. Aylık bakım raporu oluştur
7. Yıllık planlama yap

---

## 4. Hard Gate Kontrol Matrisi

| Faz | Hard Gate | Onaylayan | Koşul |
|-----|-----------|-----------|-------|
| Faz 4 | Mimari Tasarım | Arch Lead | Mimari onay |
| Faz 15 | Test | QA Lead | Coverage ≥%80 |
| Faz 18 | Release | Vault Steward | Tüm testler geçmeli |

---

## 5. Roll & Sorumluluklar

| Rol | Fazlar | Sorumluluk |
|-----|--------|------------|
| Product Owner | 1-2 | İhtiyaç tanımlama |
| Solution Architect | 3-5, 9 | Mimari tasarım |
| Data Engineer | 6 | Veritabanı tasarımı |
| Backend Architect | 7, 13 | API ve backend |
| Security Engineer | 8 | Güvenlik tasarımı |
| Hardware Engineer | 9 | Donanım tasarımı |
| Firmware Engineer | 10 | Firmware geliştirme |
| Driver Engineer | 11 | Sürücü geliştirme |
| DSP Engineer | 12 | DSP geliştirme |
| UI Designer | 14 | Frontend geliştirme |
| QA Engineer | 15 | Test |
| Performance Engineer | 16 | Optimizasyon |
| Technical Writer | 17 | Dokümantasyon |
| DevOps Engineer | 18-19 | Deploy ve monitoring |
| Support Engineer | 20 | Bakım |

---

## 6. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| Workflow | [[WORKFLOW.md]] | Ana workflow |
| Faz 4 | [[ADR-064-electronics-platform-architecture]] | Mimari ADR |
| Faz 9 | [[ADR-063-hardware-design-standards]] | Donanım standartları |
| Faz 12 | [[ADR-062-dsp-pipeline-architecture]] | DSP ADR |
| Faz 15 | [[testing/coverage-targets]] | Coverage hedefleri |

---

## 7. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Active |
| Total Phases | 20 |
| Hard Gates | 3 |
| Roles | 15 |
| Average Duration | 60-120 gün |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-10
**Mode:** Red Team · Human Mode · Truth Mode
