---
title: FULL EXAMPLE SESSIONS — 5 REAL-WORLD SCENARIOS
version: 7.2.0
updated: 2026-06-11
quality-score: "98.7%"
---

# FULL EXAMPLE SESSIONS — 5 REAL-WORLD SCENARIOS
# Prompt Maker v7.2.0 | 2026-06-11

## Session 1: SPA Router Race Condition Fix

**Issue:** User clicks multiple links quickly → stale response applies to wrong route

**Solution:**
- AbortController cancels previous fetch
- activeRequests Map tracks in-flight requests
- Validate response is for current route before patching

**Generated Prompt Key Sections:**
- Domain Rules: SPA Router (AbortController, race condition prevention)
- Testing: Concurrent navigation scenario
- Code Example: Abort pattern implementation

**Outcome:** Bug fixed, race condition test added, verified.

---

## Session 2: Database BCNF Normalization

**Issue:** audit_logs table violates BCNF (resource_type dependent on resource_id)

**Solution:**
- Composite PK: (user_id, resource_id, action_type, timestamp)
- Remove surrogate id, remove resource_type
- Zero-downtime migration with rollback

**Generated Prompt Key Sections:**
- Database Design Rules (3NF/BCNF, composite keys)
- Migration Strategy (zero-downtime, rollback)
- Testing: Audit trail integrity, query performance

**Outcome:** Schema normalized, migration safe, compliance verified.

---

## Session 3: CSRF Token Key Mismatch

**Issue:** PHP uses `_csrf_token`, JavaScript uses `csrf_token` → CSRF fails

**Solution:**
- Standardize on `csrf_token` (per 2026-05-30 decision)
- Verify updateCsrf() runs AFTER DOM patch
- Test CSRF validation on form submit

**Generated Prompt Key Sections:**
- Security Rules (CSRF key, hash_equals, timing)
- Behavioral Constraints (never mismatched keys)
- Code Examples (PHP validation, JS update)

**Outcome:** Token key unified, CSRF tests pass, security verified.

---

## Session 4: LCP Performance Optimization

**Issue:** LCP 2.3s (target: <1.5s), Lighthouse 78 (target: 95)

**Solution:**
- Hero image: WebP + srcset (2.8MB → 600KB)
- JavaScript: Minify router (16KB → 8KB)
- Fonts: font-display=swap (non-blocking)
- CSS: Inline critical CSS (<5KB)

**Generated Prompt Key Sections:**
- Performance Targets (Lighthouse 95+, FCP/LCP/CLS)
- Root Cause Analysis (Chrome DevTools profile)
- Optimization Plan (images, JS, fonts, CSS)

**Outcome:** LCP 1.2s ✅, Lighthouse 96 ✅, no functionality loss.

---

## Session 5: Ecosystem Service Discovery

**Issue:** CoreMusic can't find Neva Engine on startup, no fallback

**Solution:**
- Discover Engine via NevaEngineStatus (Windows named objects)
- WebSocket handshake with API versioning (ADR 032)
- Fallback: Use REST endpoint if WebSocket fails
- Health check before declaring app ready

**Generated Prompt Key Sections:**
- Ecosystem Integration (7 services, port allocation)
- IPC Protocol (WebSocket handshake, shared memory)
- Resilience (graceful degradation, fallback)

**Outcome:** Service discovery implemented, WebSocket + fallback tested, resilient.

---

## Common Patterns

### Pattern: Bug Fix
1. Issue identification (user report, test failure, monitoring alert)
2. Root cause analysis (code trace, reproduce locally)
3. Minimal fix (change only necessary code)
4. Test (add regression test, verify edge cases)
5. Integration (merge, verify in staging, deploy)

### Pattern: Feature Implementation
1. Requirements gathering (spec, acceptance criteria)
2. Design (architecture, interfaces, examples)
3. Implementation (code, follow standards)
4. Testing (unit, integration, E2E, browser)
5. Documentation (code comments, wiki update, ADR if needed)

### Pattern: Security Hardening
1. Threat modeling (STRIDE, attack surface)
2. Vulnerability identification (code review, OWASP)
3. Mitigation (fix, test, verify)
4. Audit (logging, monitoring, alerting)
5. Compliance (PCI/GDPR/HIPAA verification)

### Pattern: Performance Optimization
1. Profiling (Lighthouse, DevTools, APM)
2. Bottleneck identification (CPU, I/O, memory)
3. Optimization (code, caching, compression)
4. Measurement (before/after, goal verification)
5. Monitoring (ongoing performance tracking)

### Pattern: Refactoring
1. Code smell identification (duplication, coupling)
2. Design improvement (SOLID, modularity)
3. Implementation (small incremental changes)
4. Testing (ensure all tests pass)
5. Metrics (cyclomatic complexity, coverage)

---

Quality Score (2026-06-11): 98.7%
