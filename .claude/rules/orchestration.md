---
type: rules
category: orchestration
title: "CoreMusic — Orchestration Rules"
date: 2026-08-15
updated: 2026-08-15
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Orchestration Rules

**See also:** [[AGENTS.md]] · [[engine.md]] · [[WORKFLOW.md]]

---

## 1. Amaç

Master Orchestrator ve tüm agent'lar için **orkestrasyon kuralları**. opencode.json'daki `@.claude/rules/orchestration.md` referansını karşılar.

---

## 2. 7-Adımlı Görev Dağıtımı

```
Kullanıcı İsteği
  → [1. Analiz] — Keyword çıkarma, domain eşleme
    → [2. Pre-flight] — Bağımlılık, dosya kontrolü
      → [3. Routing] — Doğru agent'ı seç
        → [4. Execution] — Agent görevi yürütür
          → [5. Handover] — Gerekirse diğer ajana transfer
            → [6. Validation] — Çıktıyı doğrula
              → [7. Completion] — Görevi tamamla + log
```

---

## 3. Keyword → Agent Routing

| Keyword Grubu | Birincil Agent | Öncelik |
|---------------|----------------|---------|
| CSRF, CSP, XSS, OWASP, auth bypass, session hijack | security-engineer | CRITICAL |
| API, endpoint, routing, middleware, PHP, controller | backend-architect | HIGH |
| database, SQL, BCNF, migration, query, MySQL, PDO | data-engineer | HIGH |
| C++, ASIO, JUCE, audio, DSP, Neva Engine | embedded-engineer | HIGH |
| CSS, UI, responsive, accessibility, ITCSS, BEM | ui-designer | MEDIUM |
| test, coverage, PHPUnit, Vitest, Playwright | qa-engineer | MEDIUM |
| CI/CD, Docker, deploy, infrastructure, pipeline | devops-engineer | MEDIUM |
| DAC, ADC, PCB, amplifier, KiCad, hardware | audio-hardware-engineer | MEDIUM |
| XMOS, xTIMEcomposer, I2S, TDM, DSP firmware | dsp-firmware-engineer | MEDIUM |
| WASAPI, COM, WinRT, WDK, Windows driver | windows-software-engineer | MEDIUM |
| vault, documentation, ADR, wiki-link, index | vault-updater | LOW |

---

## 4. Öncelik Seviyeleri

| Öncelik | Tanım | Timeout | Max Retry |
|---------|-------|---------|-----------|
| CRITICAL | Sistem durması, güvenlik açığı | 5s | 1 |
| HIGH | Kritik işlev kaybı | 15s | 3 |
| MEDIUM | Normal geliştirme görevi | 30s | 3 |
| LOW | İyileştirme, optimizasyon | 60s | 2 |

---

## 5. Handover Protokolü

```
[Kaynak Agent] → [Handover Request] → [Hedef Agent] → [Onay/Red] → [Confirmation]
```

| Kural | Değer |
|-------|-------|
| Onay zorunlu | Hedef agent onayı olmadan tamamlanamaz |
| Timeout | 30 saniye |
| Max retry | 3 |
| Red durumunda | MO devreye girer |
| Logging | Tüm handover'lar `log.md`'ye yazılır |

---

## 6. Eskalasyon Protokolü

```
L1 (Domain Lead, 30s) → L2 (Tech Lead, 60s) → L3 (Arch Lead, 120s) → İnsan
```

---

## 7. Sağlık Kontrolü

| Durum | Kod | Açıklama |
|-------|-----|----------|
| Healthy | 200 | Görev tamamlandı |
| Degraded | 301 | Yavaş yanıt (>15s) |
| Retry | 408 | Timeout, yeniden dene |
| Failed | 500 | 3 retry başarısız |
| Dead | 503 | Yanıt yok, escalation |

---

## 8. Context Lock

| Kural | Değer |
|-------|-------|
| Kilitleme süresi | Max 30 saniye |
| Deadlock prevention | MO en eski kilidi kırar |
| Öncelik | CRITICAL > HIGH > MEDIUM > LOW |
| Logging | Lock acquire/release `log.md`'ye yazılır |

---

## 9. Domain Boundary

| Dosya Tipi | Sorumlu Agent |
|------------|---------------|
| `*.php` | backend-architect |
| `*.js` | ui-designer |
| `*.css` | ui-designer |
| `*.sql` | data-engineer |
| `*.cpp` / `*.h` | embedded-engineer |
| `*.yml` / `*.yaml` | devops-engineer |
| `tests/**/*.php` | qa-engineer |
| Security middleware | security-engineer |
| `.env` | security-engineer |
| `log.md` | Tüm agentlar (append-only) |
| `.ai/` vault | MO (koordinasyon) |

---

## 10. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| Agent kayıt defteri | [[AGENTS.md]] |
| Orkestrasyon motoru | [[engine.md]] |
| Süreçler | [[WORKFLOW.md]] |
| Mimari kararlar | [[brain.md]] |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-15
**Mode:** Red Team · Human Mode · Truth Mode
