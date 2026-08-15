---
title: "CoreMusic — AI Orchestrator"
type: architecture
category: ai-orchestrator
updated: 2026-08-09
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — AI Orchestrator

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]] · [[AGENTS.md]]

---

## 1. Amaç

AI görevlerinin koordinasyonunu ve dağıtımını yöneten orkestrasyon motoru. 11 ajanlı sistemi yönetir, context yönetimi sağlar, workflow engine ile süreçleri otomatikleştirir. Master Orchestrator (MO) ile entegre çalışır.

---

## 2. Orchestrator Mimarisi

```
┌─────────────────────────────────────────────────────┐
│                AI Orchestrator                       │
├─────────────────────────────────────────────────────┤
│  Task Queue → Priority Queue → Agent Router          │
├─────────────────────────────────────────────────────┤
│  Context Manager → Memory Store → Response           │
├─────────────────────────────────────────────────────┤
│  Workflow Engine → Tool Calling → Error Recovery     │
└─────────────────────────────────────────────────────┘
```

---

## 3. 11 Ajanlı Agent Sistemi

| # | Agent | Kod Adı | Domain | Katman |
|---|-------|---------|--------|--------|
| 1 | **Master Orchestrator** | `mo` | Görev dağıtımı, koordinasyon | Koordinasyon |
| 2 | **Backend Architect** | `backend` | PHP 8.4 API, routing, middleware | L2 |
| 3 | **UI Designer** | `ui` | Vanilla JS, ITCSS, CSS, responsive | L3 |
| 4 | **Security Engineer** | `security` | OWASP, CSRF, CSP, encryption | L1 |
| 5 | **Data Engineer** | `data` | MySQL 9 (18 BCNF), PDO, migration | L0 |
| 6 | **Embedded Engineer** | `embedded` | C++20, JUCE, ASIO, DSP | L0 |
| 7 | **QA Engineer** | `qa` | PHPUnit, Vitest, Playwright, E2E | Cross-cutting |
| 8 | **DevOps Engineer** | `devops` | CI/CD, Docker, deploy, monitoring | CI/CD |
| 9 | **Audio HW Engineer** | `audio-hw` | DAC/ADC, PCB, amplifier | HW |
| 10 | **DSP Firmware Engineer** | `dsp-fw` | XMOS, PCM3168A, DSP chain | FW |
| 11 | **Windows SW Engineer** | `win-sw` | WASAPI, driver, COM | PLAT |

---

## 4. Görev Dağıtımı Algoritması

### 4.1 Routing Rules

| Keyword Grubu | Hedef Agent | Öncelik |
|---------------|-------------|---------|
| API, endpoint, routing, middleware, PHP | Backend Architect | HIGH |
| CSS, UI, responsive, accessibility, ITCSS, BEM | UI Designer | MEDIUM |
| CSRF, CSP, XSS, OWASP, auth, encryption | Security Engineer | CRITICAL |
| database, SQL, BCNF, migration, query, schema | Data Engineer | HIGH |
| C++, ASIO, JUCE, audio, DSP, ring buffer | Embedded Engineer | HIGH |
| test, coverage, PHPUnit, Vitest, Playwright | QA Engineer | MEDIUM |
| CI/CD, Docker, deploy, infrastructure, pipeline | DevOps Engineer | HIGH |
| vault, documentation, ADR, wiki-link, index | MO (vault-updater) | LOW |

### 4.2 Priority Levels

| Öncelik | Timeout | Max Retry | Yanıt Süresi |
|---------|---------|-----------|-------------|
| CRITICAL | 5s | 1 | Anlık |
| HIGH | 15s | 3 | 15s |
| MEDIUM | 30s | 3 | 30s |
| LOW | 60s | 2 | 60s |

---

## 5. Context Management

| Kaynak | Tip | TTL | Güncelleme |
|--------|-----|-----|------------|
| User Session | Persistent | 3600s | Her istekte |
| Audio Cache | Ephemeral | 300s | Analiz sonunda |
| Model Cache | Persistent | 86400s | Eğitim sonunda |
| Query Cache | Ephemeral | 60s | Sorgu sonunda |
| Agent Context | Session | Görev süresi | Görev başında |
| Memory State | Persistent | Sonsuz | Oturum sonunda |

---

## 6. Tool Calling

| Tool | Kullanım | Protokol | Fallback |
|------|----------|----------|----------|
| Audio Service API | Ses analizi, EQ | REST (9741) | Cached result |
| DSP Firmware | EQ uygulama | IPC | Default preset |
| Device Service | Donanım durumu | BLE/WiFi | Last known |
| Download Service | Müzik indirme | HTTP (3001) | Queue |
| Knowledge Base | Bilgi sorgulama | File System | Index fallback |
| Memory System | Session okuma | Internal | Fresh session |

---

## 7. Workflow Engine

| Workflow | Aşama Sayısı | Hard Gate | Çıktı |
|----------|-------------|-----------|-------|
| Vault Refactoring | 12 faz | Faz 7: Improvement Proposal | Güncellenmiş vault |
| Product Lifecycle | 20 faz | Faz 7: Teknik Mimari | MVP |
| ADR Lifecycle | 4 aşama | Review → Active | Frozen ADR |
| Code Review | 8 adım | — | İnceleme raporu |
| Bug Fix | 8 adım | — | Düzeltme + test |
| Security Audit | 8 adım | — | Güvenlik raporu |
| Deployment | 8 adım | Hard Gate: Onay | Production deploy |

---

## 8. Error Recovery

| Hata Tipi | Çözüm | Retry | Escalasyon |
|-----------|-------|-------|------------|
| Agent Timeout | Fallback agent | 3x | L1→L2→L3 |
| Model Failure | Rule-based fallback | 1x | — |
| Data Missing | Cached response | 2x | — |
| Network Error | Offline mode | 3x | — |
| Domain Violation | MO müdahale | 1x | Anlık |
| Layer Violation | Derhal revert | 0x | CRITICAL |

---

## 9. Health Check

| Durum | Kod | Tanım | Aksiyon |
|-------|-----|-------|---------|
| Healthy | 200 | Görev tamamlandı | Devam |
| Degraded | 301 | Yavaş yanıt (>15s) | Uyar, devam |
| Retry | 408 | Timeout, yeniden dene | Max 3 retry |
| Failed | 500 | 3 retry başarısız | Queue reset |
| Dead | 503 | Yanıt yok | Escalation |

---

## 10. Agent Communication

| Protokol | Kullanım | Port |
|----------|----------|------|
| Internal API | Servisler arası | — |
| WebSocket | Real-time updates | 9742 |
| Queue | Async tasks | — |
| Event Bus | Pub/Sub | — |
| Handover | Agent transfer | — |

---

## 11. Integration with MO

| Olay | MO Aksiyonu |
|------|-------------|
| Agent timeout | Escalation (L1→L2→L3) |
| Task failure | Retry + fallback |
| New task | Priority queue'ya ekle |
| Task complete | Log + memory update |
| Domain violation | Derhal revert + CRITICAL log |
| Handover request | Onay/red + transfer |

---

## 12. Monitoring

| Metrik | Hedef | Alarm |
|--------|-------|-------|
| Task Queue Depth | <100 | >500 |
| Agent Response Time | <15s | >30s |
| Error Rate | <1% | >5% |
| Memory Usage | <80% | >90% |
| Handover Rate | <10% | >30% |

---

## 13. Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 Agentlar | [[AGENTS.md]] §4 | Agent tanımları |
| § 4 Routing | [[AGENTS.md]] §7 | Task dispatch |
| § 5 Context | [[memory-system]] | Session yönetimi |
| § 6 Tool | [[tool-calling]] | Dış servis çağrısı |
| § 7 Workflow | [[WORKFLOW.md]] §5 | 12 faz vault refactoring |
| § 9 Health | [[AGENTS.md]] §11 | Sağlık kontrolü |
| § 11 MO | [[AGENTS.md]] §9 | Handover protokolü |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
