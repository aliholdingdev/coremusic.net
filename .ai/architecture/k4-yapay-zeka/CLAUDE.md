---
title: "CoreMusic — K4 Yapay Zeka CLAUDE.md"
type: layer-guide
folder: "architecture/k4-yapay-zeka"
category: vault
date: 2026-09-20
status: active
version: 1.0.0
authority: reference
---

# K4 Yapay Zeka — CLAUDE.md

## 1. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Offline-first zorunlu | Çevrimdışı çalışamaz |
| 2 | Privacy: veri yerel işlenir | Veri sızıntısı |
| 3 | Model <200MB | Deployment başarısız |
| 4 | Inference <200ms | Yavaş yanıt |

## 2. ML Pipeline Sırası

```
Audio Input → Feature Extraction → Model Inference → Post-processing → Output
```

## 3. Model Formatları

| Format | Kullanım | Boyut |
|--------|----------|-------|
| ONNX | Edge inference | <50MB |
| TFLite | Mobile | <20MB |
| PyTorch | Training | >200MB |

## 4. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-030 | AI öneri motoru |

---

*K4 CLAUDE.md v1.0.0 — CoreMusic*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
