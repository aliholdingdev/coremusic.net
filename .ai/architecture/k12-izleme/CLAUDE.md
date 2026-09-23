---
title: "CoreMusic — K12 İzleme CLAUDE.md"
type: layer-guide
folder: "architecture/k12-izleme"
category: vault
date: 2026-09-20
status: active
version: 1.0.0
authority: SSOT
---

# K12 İzleme — CLAUDE.md

## 1. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Structured JSON log | Debugging zor |
| 2 | Sensitive data log'da yasak | Veri sızıntısı |
| 3 | Correlation ID zorunlu | Trace edilemez |

## 2. Log Formatı

```json
{
    "timestamp": "ISO-8601",
    "level": "INFO|WARN|ERROR",
    "service": "service-name",
    "message": "description",
    "trace_id": "uuid",
    "context": {}
}
```

## 3. Yasaklı Log'lar

```php
// ❌ YASAK
error_log("Password: " . $password);
error_log("Credit card: " . $card);

// ✅ DOĞRU
error_log(json_encode([
    'level' => 'error',
    'message' => 'Auth failed',
    'user_id' => $userId,
    'ip' => '[REDACTED]'
]));
```

---

*K12 CLAUDE.md v1.0.0 — CoreMusic*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
