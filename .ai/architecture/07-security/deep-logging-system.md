---
title: "deep-logging-system — Eski Derin Logging Dokümanı (stub-with-truth)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız eski link uyumu stub'udur"
updated: 2026-10-07
tier: 3
domain: architecture-security
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# deep-logging System — Güvenlik/Derin Loglama (stub)

**Durum:** `architecture/07-security/deep-logging-system` **diskte YOK** (eski ağaç silindi) — 5 link referansı.

## Bugünkü Karşılığı (gerçek kanıt — P5 enterprise logger, 2026-10-05/07)

| Bileşen | Dosya |
|---------|-------|
| PSR-3 FileHandler (emergency→error dispatcher) | `shared/src/Log/FileHandler.php` |
| LoggerFactory | `shared/src/Log/LoggerFactory.php` |
| Redactor (e-posta maskesi `***@domain`) | `shared/src/Log/Redactor.php` |
| StructuredLogger (traceId dikişi) | `shared/src/PageRouter/StructuredLogger.php` |
| 5 log akışı | `coremusic_php_errors.log` · `_warnings.log` · `_info.log` · `_kernel_debug.log` · `_security.log` (kök) |
| securityEvent akışı (Permission/OriginCheck/CSRF/429) | `.ai/log.md` (P5 kaydı, 2026-10-07) |

**İlgili domain:** d09 (İzleme) → [[architecture/18-domain-d09-izleme-cicd-ag]] (K400–K408)
