---
title: "CoreMusic — .ai/architecture/03-contracts Bağlam"
type: context
folder: ".ai/architecture/03-contracts"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
---

# .ai/architecture/03-contracts — CLAUDE.md

**Zorunlu Bağlantılar:** [[../CLAUDE.md]]

## 1. Bağlam

Backend Architect'ın API çalışma referansı. 36 dosya ile en yoğun mimari klasör; BFF katmanı ve versiyonlama kararları buradan beslenir.

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Dosya | 36 + 3 mikro klasör |
| Mikro klasörler | ports (port-registry), protocols (protocol-decision), roles (technology-roles) — her biri tek dosya |

## 3. Komşu İlişkiler

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../CLAUDE.md]] | Mimari kök |
| Kod | [[../../../shared/src/Api/CLAUDE.md]] *(üretilecek)* | Gateway/BFF implementasyonu |
| Test | [[../../../shared/tests/Api/]] | ApiResponse, Dto, Versioning testleri |

## 4. Değişiklik Protokolü

1. Sözleşme değişikliği → kod + doküman + test aynı PR/iş birimi
2. Audit: `[[../../log.md]]`

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
