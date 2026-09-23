---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K9 API & Routing Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# K9: API & Routing Layer

**Katman:** K9 (API & Routing)
**Kapsam:** Gateway, BFF, CQRS, Event Bus, SPA Router, OpenAPI
**Sorumlu Agent:** Backend Architect
**Bileşen Sayısı:** 30

---

## 1. Genel Bakış

K9 katmanı, tüm istemcilerin API Gateway üzerinden bağlandığı API katmanını içerir. API-First (ADR-084) prensibiyle çalışır.

---

## 2. API Gateway

### 2.1 Gateway Yapısı

```
Client → API Gateway → Middleware Pipeline → Use Case → Repository → Infrastructure

Gateway Responsibilities:
  - Routing
  - Authentication
  - Rate Limiting
  - Validation
  - Logging
  - Correlation ID
```

### 2.2 Gateway Kuralları

| Kural | Açıklama |
|-------|----------|
| Tek giriş noktası | `api.coremusic.net` |
| Auth zorunlu | JWT + Session |
| Rate limit | 60 req/60s |
| Validation | Request/DTO validasyonu |
| Correlation ID | Her isteğe benzersiz ID |

---

## 3. BFF (Backend for Frontend)

| İstemci | BFF | Response |
|---------|-----|----------|
| SPA | SPA BFF | Tam veri |
| Mobile | Mobile BFF | Minimal |
| Embedded (RPi5) | Embedded BFF | Ultra-minimal, gzip |
| Desktop | Desktop BFF | Orta boy |
| Admin | Admin BFF | Full + audit |
| Car | Car BFF | Touch-optimized |

---

## 4. CQRS

```
Write: Command → Use Case → Repository → MySQL Master
Read:  Query → Read Model → Cache → Response
```

### 4.1 Command/Query Ayrımı

| Operation | Type | Target |
|-----------|------|--------|
| Create playlist | Command | MySQL Master |
| Update track | Command | MySQL Master |
| Delete user | Command | MySQL Master |
| Get playlists | Query | Cache → MySQL Read |
| Search tracks | Query | Elasticsearch |
| Get user profile | Query | Redis → MySQL |

---

## 5. SPA Router (ADR-083)

### 5.1 Route Tanımları

```
GET  /                          → Landing page
GET  /music                     → Music panel
GET  /music/library             → Library
GET  /music/albums              → Albums
GET  /music/artists             → Artists
GET  /music/playlists           → Playlists
GET  /home                      → Home panel
GET  /car                       → Car panel
GET  /studio                    → Studio panel
GET  /admin                     → Admin panel
GET  /download                  → Download panel
GET  /pro                       → Pro panel
GET  /media                     → Media panel
GET  /auth/login                → Auth panel
```

### 5.2 SPA Router Implementasyonu

```php
// PageRouter.php
class PageRouter {
    private array $routes = [];

    public function addRoute(string $path, string $handler): void {
        $this->routes[$path] = $handler;
    }

    public function dispatch(string $path): ResponseInterface {
        $handler = $this->routes[$path] ?? null;
        if (!$handler) {
            return new Response(404);
        }
        return $handler->handle($request);
    }
}
```

---

## 6. OpenAPI (API-First)

### 6.1 Sözleşme Akışı

```
OpenAPI Spec → DTO → Contract → Validation → Use Case → Kod

Kod hiçbir zaman sözleşmeden önce yazılmaz.
```

---

## 7. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-083 | SPA Router Architecture |
| ADR-084 | API Gateway Architecture |
| ADR-086 | Event Driven Architecture |

---

*K9 API & Routing Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
*Mode: Red Team · Human Mode · Truth Mode*
