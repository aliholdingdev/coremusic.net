---
title: "CoreMusic — .ai/architecture Agent Talimatları"
type: agent-registry
folder: ".ai/architecture"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# .ai/architecture — AGENTS.md

**Zorunlu Bağlantılar:** [[../CLAUDE.md]] · [[./CLAUDE.md]]

## 1. Amaç

Katmanlı mimari dokümantasyon merkezi (L0→L6) + deployment, contracts, data, audio, security, auth, network konu klasörleri.

## 2. İçerik Envanteri

| Yol | Amaç |
|-----|------|
| Kök 10 dosya | DB tasarımı, CSS device planı, L4/L5/L6 katman tanımları, testing mimarisi, auth migration planı, koşullu render PHP rehberi |
| `00-overview/` | Mimari master, dependency graph, startup stratejisi |
| `02-deployment/` | CI/CD, deployment, observability (index'li) |
| `03-contracts/` | 36 dosya — API sözleşmeleri, endpoint'ler, hata kodları (+ ports/protocols/roles) |
| `04-decisions/` | Karar logu, guardrails (+ device-agent, os-adapters, service-architecture) |
| `05-data/` | BCNF, DB master, migration stratejisi, repository pattern |
| `06-audio/` | 11 dosya — audio pipeline + servis tanımları |
| `07-security/` | 14 dosya — şifreleme katmanları, JWT, driver signing (+ api/, security/) |
| `08-auth/` | 9 dosya — auth domain/flow/API/infrastructure |
| `10-network/` | gRPC-IPC, HTTP(S), WebSocket/MQTT, local socket |
| `ai/` | AI mimarisi 13 dosya (ADR-030) |
| `l0-infrastructure/` | Cache, credential vault, DB, filesystem |
| `l1-security/` | Auth, CSP, CSRF, middleware, session |
| `l2-routing/` | SPA router, guard pipeline, middleware pipeline, subdomain routing |
| `l3-presentation/` | ITCSS, JS modül, responsive, device CSS |
| `sql/` | İlk şema + seed SQL |

## 3. Agent Kuralları

### Zorunlu
1. Yeni mimari doküman ilgili alt klasöre konur; köke yalnızca katman-bağımsız dosya konur
2. Alt klasörlerdeki index.md dosyaları korunur ve güncellenir
3. ADR ile çelişen doküman → ADR kazanır → doküman düzeltilir

### Yasak
1. ADR'yi buraya kopyalamak (tek konum: decisions/)
2. Katman numarası ihdası (L0-L6 sabittir, ADR-039/061)

## 5. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| Master | [[./00-overview/architecture-master.md]] |
| Katman diagramı | [[./system-layers-diagram.md]] |
| ADR'ler | [[../decisions/index.md]] |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
