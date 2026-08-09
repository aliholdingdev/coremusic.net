---
title: "CoreMusic — AI Workflow"
type: architecture
category: ai-workflow
updated: 2026-08-09
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — AI Workflow

**Zorunlu Bağlantılar:** [[index]] · [[WORKFLOW.md]] · [[AGENTS.md]] · [[CLAUDE.md]] · [[ai-engine]]

---

## 1. Amaç

AI iş akış süreçlerini tanımlar. Recommendation, analysis, optimization, documentation, testing ve electronics workflow'larını kapsar. Tüm workflow'lar trigger → process → result → feedback döngüsü içinde çalışır.

---

## 2. AI Workflow Tipleri

| # | Workflow | Tetikleme | Çıktı | ADR |
|---|----------|-----------|-------|-----|
| 1 | Music Recommendation | User request | Öneri listesi | ADR-030 |
| 2 | Audio Analysis | Track upload | Metadata + features | ADR-017 |
| 3 | Auto EQ | Playback start | EQ ayarları | ADR-025 |
| 4 | Smart Playlist | User action | Çalma listesi | ADR-030 |
| 5 | Content Download | User request | Download queue | ADR-026 |
| 6 | Session Management | Login | Session state | ADR-011 |
| 7 | Documentation Gen | Kod değişikliği | Dokümantasyon | ADR-024 |
| 8 | Test Generation | Kod yazımı | Test dosyaları | — |
| 9 | Security Audit | Güvenlik taraması | Güvenlik raporu | ADR-022 |
| 10 | Electronics Analysis | Donanım tasarımı | Analiz raporu | ADR-061 |

---

## 3. Recommendation Workflow

```
User Request → Context Collect → Feature Extract → Model Inference → Rank → Present
     ↓              ↓                ↓                  ↓            ↓        ↓
  "Play jazz"   Session DB      Audio analysis     ML model     Score    UI
```

**Adım Detayları:**
1. **User Request:** Kullanıcı isteği (doğal dil)
2. **Context Collect:** Session DB'den tercihler, geçmiş
3. **Feature Extract:** Audio analysis ile özellik çıkarma
4. **Model Inference:** ML model ile tahmin
5. **Rank:** Skorlama ve sıralama
6. **Present:** UI'a sunma

**Recommendation Parametreleri:**

| Parametre | Değer |
|-----------|-------|
| Max sonuç | 20 |
| Minimum skor | 0.7 |
| Çeşitlilik | %30 farklı tür |
| Cache TTL | 1 saat |

---

## 4. Audio Analysis Workflow

```
Track Upload → Format Detect → Feature Extract → Store Metadata → Index
     ↓              ↓                ↓                ↓            ↓
  File upload    FFprobe         DSP pipeline      DB write    Search
```

**Feature Extraction:**

| Özellik | Yöntem | Çıktı |
|---------|--------|-------|
| Frekans analizi | FFT | Frekans spektrumu |
| Gürültü seviyesi | RMS | SNR değeri |
| Distorsiyon | THD/N | Distorsiyon oranı |
| Faz analizi | Phase correlation | Faz uyumu |
| Tempo | BPM detection | BPM |
| Key | Pitch detection | Musical key |
| Loudness | LUFS | Loudness unit |

---

## 5. Auto EQ Workflow

```
Playback Start → Room Detect → Profile Select → EQ Calculate → Apply
     ↓              ↓              ↓                ↓            ↓
  Audio start    Mic input    Preset/AI        DSP chain    Output
```

**EQ Modları:**

| Mod | Açıklama | Kaynak |
|-----|----------|--------|
| Preset | Hazır ayarlar | Kullanıcı |
| AI Auto | Otomatik ayarlama | ML model |
| Room Correction | Oda düzeltme | Mikrofon |
| Custom | Manuel ayar | Kullanıcı |
| Genre-based | Tür bazlı | Metadata |

**EQ Parametreleri:**

| Parametre | Değer |
|-----------|-------|
| Band sayısı | 31 (ADR-025) |
| Frekans aralığı | 20Hz - 20kHz |
| Gain aralığı | -12dB to +12dB |
| Q factor | 0.5 - 10 |
| Sample rate | 48kHz |

---

## 6. Documentation Generation Workflow

```
Code Change → Parse AST → Extract Docs → Generate Markdown → Validate → Store
     ↓              ↓            ↓              ↓              ↓          ↓
  Git commit    PHP/JS/C++   Functions     Wiki format    ADR check   .ai/
```

**Documentation Rules:**
- Her public function için docblock zorunlu
- ADR referansı zorunlu
- Wiki-link formatı zorunlu
- Frontmatter zorunlu
- Cross-reference zorunlu

---

## 7. Test Generation Workflow

```
Code Input → Analyze Structure → Generate Tests → Validate Coverage → Store
     ↓              ↓                ↓                ↓              ↓
  Source code    AST parse      Unit/Int/E2E     %80 minimum     tests/
```

**Test Pyramidi:**

| Seviye | Oran | Framework |
|--------|------|-----------|
| Unit Test | %70 | PHPUnit 11 / Vitest |
| Integration Test | %20 | PHPUnit 11 / Vitest |
| E2E Test | %10 | Playwright |

---

## 8. AI Electronics Workflow

```
Hardware Design → AI Analysis → PCB Review → Firmware Check → Security Audit
     ↓                ↓             ↓             ↓              ↓
  Schematic       Component     Layout        Code review    Secure boot
```

**Electronics Analysis Modülleri:**

| Modül | Sorumluluk | ADR |
|-------|------------|-----|
| Component Analysis | Bileşen doğrulama | ADR-038 |
| PCB Analysis | PCB layout inceleme | ADR-063 |
| Firmware Analysis | Firmware kod inceleme | ADR-062 |
| Thermal Analysis | Termal hesaplama | — |
| Security Audit | Güvenlik denetimi | ADR-061 |

---

## 9. Workflow Engine

| Bileşen | Sorumluluk |
|---------|------------|
| Trigger | Olay tetikleme |
| Condition | Koşul kontrolü |
| Action | Aksiyon yürütme |
| Loop | Tekrarlama |
| Branch | Koşullu dal |
| Barrier | Senkronizasyon |
| Queue | Sıralı ejecution |

---

## 10. Workflow State Machine

```
Idle → Triggered → Queued → Running → Complete → Idle
  ↓        ↓          ↓          ↓          ↓
  Wait    Validated   Pending    Active    Archived
                                  ↓
                               Failed → Retry → Running
                                  ↓
                               Escalated → Human
```

**State Geçiş Kuralları:**

| Geçiş | Koşul | Aksiyon |
|-------|-------|---------|
| Idle → Triggered | Event tetikleme | Queue'ya ekle |
| Triggered → Queued | Validation passed | Sıraya al |
| Queued → Running | Worker mevcut | Execution başlat |
| Running → Complete | Başarılı | Result'ı kaydet |
| Running → Failed | Hata | Retry logic |
| Failed → Escalated | Max retry aşıldı | İnsan müdahalesi |

---

## 11. Error Recovery

| Hata | Çözüm | Max Retry | ADR |
|------|-------|-----------|-----|
| Model failure | Fallback rule | 3 | — |
| Service timeout | Retry backoff | 3 | — |
| Data missing | Cached default | 2 | — |
| Network error | Offline mode | 3 | — |
| Permission denied | Auth refresh | 1 | ADR-008 |
| Rate limit | Queue + wait | 2 | ADR-013 |
| Hallucination | VERIFICATION REQUIRED | 0 | ADR-005 |
| Layer violation | Derhal revert | 0 | CLAUDE.md |

---

## 12. Monitoring

| Metrik | Hedef | Ölçüm |
|--------|-------|-------|
| Workflow completion | >95% | Günlük |
| Average latency | <5s | P95 |
| Error rate | <1% | Günlük |
| Queue depth | <50 | Anlık |
| Cache hit ratio | >80% | Saatlik |
| ADR compliance | 100% | Haftalık |

---

## 13. Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Workflow | [[WORKFLOW.md]] | Ana süreçler |
| § 3 Recommendation | [[ai-engine]] | AI motoru |
| § 5 Auto EQ | [[ADR-025-professional-eq-system]] | EQ sistemi |
| § 6 Documentation | [[ADR-024-ecosystem-modular-docs]] | Dokümantasyon |
| § 8 Electronics | [[ADR-061-electronics-architecture]] | Elektronik |
| § 8 Electronics | [[ADR-062-dsp-pipeline-architecture]] | DSP |
| § 8 Electronics | [[ADR-063-hardware-design-standards]] | Donanım |
| § 11 Error | [[ADR-008-bypass-auth-middleware]] | Auth |
| § 11 Error | [[ADR-013-rate-limiting-apcu]] | Rate limit |

---

## 14. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 14 |
| SSOT Authority | AI Workflow |
| Last Updated | 2026-08-09 |
| Workflow Count | 10 |
| EQ Modes | 5 |
| Test Pyramid | Unit %70, Int %20, E2E %10 |
| Electronics Modules | 5 |
| Error Recovery | 8 |
| ADR Coverage | ADR-005/008/011/017/022/024/025/026/030/038/061/062/063 |
| Cross References | 9 çapraz referans |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
