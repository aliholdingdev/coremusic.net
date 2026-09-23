---
title: DEEP DOMAIN RULES — 15 SPECIALIZED DOMAINS (PICCO)
description: Audio/DSP, Embedded, Fintech, HealthTech, ML/AI, Networking, Cross-Project
version: 11.0.0
updated: 2026-08-15
framework: PICCO
quality-score: "95%+"
---

# DEEP DOMAIN RULES — 15 SPECIALIZED DOMAINS (PICCO)
# Prompt Maker v11.0.0 | 2026-08-15

## Domain 1: SPA Router & Frontend

**Rules:**
- AbortController MANDATORY for every fetch
- TrustedTypes + DOMParser (never innerHTML)
- CSRF token: key='csrf_token', hash_equals() validation
- updateCsrf() AFTER DOM patch, not before
- Event listener cleanup on unmount (WeakMap tracking)
- Race condition detection (activeRequests Map)
- Memory pressure check (navCount % 10, evict LRU)
- History API: popstate timing, back button handling

**Anti-Patterns:** var usage, innerHTML, uncontrolled async, detached DOM leaks

---

## Domain 2: Database Design & Optimization

**Rules:**
- 3NF normalization (transitive dependency elimination)
- BCNF for composite keys (audit trails)
- Explicit column selection (no SELECT *)
- PDO prepared statements MANDATORY
- Index design: primary, unique, composite, partial, GIN
- N+1 prevention: batch queries, joins, eager loading
- Query optimization: EXPLAIN ANALYZE, execution plans
- Audit trail: (user_id, resource_id, action_type, timestamp)

**Anti-Patterns:** SELECT *, raw SQL, missing indexes, N+1 queries, unaudited changes

---

## Domain 3: Middleware Pipeline & Security

**Rules:**
- Pipeline order: Session → BypassAuth → RateLimiter → Auth → SecurityHeaders → CSRF
- Session MUST come before SecurityHeaders (nonce injection)
- RateLimiter per IP: 60 req/60s (APCu)
- Auth: $_SESSION['MM_UserID'] check
- SecurityHeaders: CSP nonce-based, X-Frame-Options, HSTS
- CSRF: hash_equals() timing-safe validation
- Early return halts pipeline

**Anti-Patterns:** Wrong middleware order, missing CSRF validation, no rate limiting

---

## Domain 4: API Design & REST

**Rules:**
- Versioning strategy: /v1/, /v2/ path-based
- Authentication: JWT (EdDSA), OAuth2, HMAC-Ed25519, API Key
- Rate limiting: per IP (60/60s), per user (100/hour), per endpoint
- Pagination: offset/limit or cursor-based, max size enforcement
- Error responses: consistent format {error, message, status}
- Caching: Cache-Control headers, ETag, 304 Not Modified
- Documentation: OpenAPI/Swagger, examples, SDKs
- Monitoring: request logging, metrics (latency, error rate)

**Anti-Patterns:** Inconsistent versioning, no rate limit, missing error handling

---

## Domain 5: Audio & DSP Processing

**Rules:**
- Sample rate: 44.1kHz (CD), 48kHz (professional), 96kHz (hi-res)
- Bit depth: 16-bit (CD), 24-bit (pro), 32-bit (float processing)
- SNR > 100 dB, THD+N < 0.01% @ 1kHz (professional target)
- ASIO/WASAPI callback: lock-free, pre-allocated buffers, < 5ms latency
- DSP chain order: input normalization → preamp → EQ → compression → limiter
- 31-band EQ: standard frequencies (20Hz-20kHz), ±12dB per band
- Codecs: FLAC (lossless), Opus (modern lossy, <64kbps high-quality)

**Anti-Patterns:** malloc in audio callback, blocking I/O in realtime, latency > 50ms

---

## Domain 6: Embedded & RTOS

**Rules:**
- Task scheduling: priority-based (higher priority = time-critical)
- Interrupt handlers: lock-free, atomic operations, no malloc
- Memory-constrained: stack analysis, heap fragmentation monitoring, DMA for movement
- Real-time: hard (< 5ms), soft (100ms tolerance)
- RTOS: FreeRTOS, Zephyr, RIOT (choose per constraints)
- Hardware: STM32 (ARM Cortex-M), ESP32 (WiFi/Bluetooth)

**Anti-Patterns:** Blocking in ISR, unbounded memory allocation, priority inversion

---

## Domain 7: Fintech & Payment Processing

**Rules:**
- PCI DSS Level 1: AES-256-GCM encryption, TLS 1.2+, tokenization
- Card data: NEVER stored in app (tokenization only)
- Audit logging: all transactions, amounts, timestamps, IP
- Fraud detection: rule-based + ML (velocity, geographic anomalies)
- KYC/AML: identity verification, sanctions screening
- Currency conversion: banker's rounding, no floating point
- Compliance: PCI Level, SOC 2, GDPR, HIPAA (if applicable)

**Anti-Patterns:** Storing card numbers, unencrypted transmission, no audit trail

---

## Domain 8: HealthTech & HIPAA Compliance

**Rules:**
- PHI encryption: AES-256-GCM at rest, TLS 1.2+ in transit
- Access controls: unique user IDs, role-based, audit every access
- Data de-identification: 18-identifier removal (HIPAA Safe Harbor)
- Breach notification: within 60 days, patient notification
- Business Associate Agreements (BAAs): all vendors
- Data retention: follow state/federal law (typically 6 years)
- Right to erasure: GDPR Article 17 (but HIPAA limits deletion)

**Anti-Patterns:** Unencrypted PHI, missing access logs, no BAAs

---

## Domain 9: ML & AI Systems

**Rules:**
- Model versioning: MAJOR.MINOR.PATCH (semantic versioning)
- Training/validation/test split: 70/15/15 (or cross-validation)
- Feature engineering: statistical significance, no data leakage
- Bias detection: stratified sampling, fairness metrics
- Model evaluation: accuracy, precision, recall, F1, AUC-ROC
- Edge inference: Tensor Lite (mobile), ONNX Runtime (cross-platform)
- Explainability: SHAP values, feature importance
- Continuous learning: model drift detection, retraining triggers

**Anti-Patterns:** Train/test data leakage, ignoring class imbalance, no monitoring

---

## Domain 10: Multi-Project Orchestration (ADR 030-032)

**Rules (Startup Sequence):**
1. Neva Engine → ASIO init → ring buffer → NevaEngineStatus
2. Neva Player → GPU init → discover Engine → attach ring buffer
3. Download Service → Node.js/Express → port 3001
4. CoreMusic PHP → load routes → discover Engine (9741 REST)
5. Browser → http://music.coremusic.net:81/

**Rules (Port Allocation - ADR 031):**
- 81: CoreMusic PHP (main)
- 3001: Download Service REST/WS
- 9741: Engine REST
- 9742: Engine WebSocket
- 9743: Player WebSocket + RTC
- 49152-65535: WebRTC ephemeral

**Rules (IPC - ADR 032):**
- WebSocket handshake: {type: "handshake", apiVersion: 2, clientType}
- Shared memory: NevaAudioRingBuffer (float32 PCM, 48kHz)
- Health checks: /health endpoints + liveness probes
- Graceful degradation: fallback when service unavailable

**Anti-Patterns:** Hardcoded ports, synchronous startup, no service discovery

---

## Domain 11: UI/UX & WCAG 2.2 AA

**Rules:**
- Color contrast: 4.5:1 (text), 3:1 (large)
- Touch targets: 24×24px minimum
- Focus indicators: 3px visible outline
- Keyboard navigation: Tab, Shift+Tab, Enter, Escape
- Screen reader support: ARIA labels, landmarks, roles
- Responsive: mobile 320px, tablet 768px, desktop 1024px, ultra-wide 1920px+
- Dark/light mode: CSS custom properties (--color-primary, --color-bg)
- Component states: DEFAULT | HOVER | ACTIVE | FOCUSED | DISABLED | ERROR | LOADING | EMPTY | SELECTED

**Anti-Patterns:** Color-only feedback, < 24px touch targets, no keyboard nav

---

## Domain 12: DevOps & CI/CD

**Rules:**
- Pipeline: Build → Test → Deploy Dev → Deploy Prod → Monitor
- Blue-green or canary deployment (never direct cutover)
- Containerization: Docker multi-stage, layer caching, health checks
- Monitoring: Prometheus metrics, ELK logs, Datadog/New Relic APM
- Alerting: SEV-1 within 1min, SEV-2 within 15min
- Health checks: readiness (ready to serve) + liveness (still alive)
- Graceful shutdown: SIGTERM → cleanup → exit (max 30s)

**Anti-Patterns:** Direct production push, no rollback plan, missing health checks

---

## Domain 13: Security Hardening & Threat Modeling

**Rules:**
- STRIDE threat modeling (Spoofing, Tampering, Repudiation, Info Disclosure, DoS, EoP)
- OWASP Top 10:2025 (A01-A10 coverage)
- Principle of least privilege: default deny, explicit grant
- Defense in depth: multiple layers (firewall, WAF, app-level, DB)
- Secure by default: no insecure defaults in config
- Fail securely: fail-closed (deny) not fail-open (allow)
- Encryption: AES-256-GCM symmetric, Ed25519 asymmetric

**Anti-Patterns:** Single security layer, trust user input, hardcoded secrets

---

## Domain 14: Testing & Quality Assurance

**Rules:**
- Unit testing: 80%+ coverage for critical paths
- Integration testing: database + service interactions
- E2E testing: full user flow (Playwright browser automation)
- Edge-case testing: null, empty, max size, min value, boundary
- Security testing: OWASP scenarios, injection attempts
- Performance testing: Lighthouse 95+, < 500ms response
- Regression testing: previous bugs don't reoccur
- Async testing: race conditions, cancellation, retry logic

**Anti-Patterns:** No test coverage, manual testing only, skipped flaky tests

---

## Domain 15: Observability & Monitoring

**Rules:**
- Structured logging: JSON format, correlation IDs (X-Trace-Id)
- Metrics: CPU, memory, request rate, error rate, latency percentiles
- Distributed tracing: service-to-service call tracking
- Alerting: threshold-based + anomaly detection
- Dashboards: real-time overview, SLO tracking
- Log retention: 90 days minimum (compliance)
- Incident response: postmortem, blameless culture, prevention

**Anti-Patterns:** No logging, logs without context, metrics without alerting

---

## Cross-Domain Rules (Apply to All)

1. **Security-First:** Active attacker mindset on every decision
2. **Documentation:** Code changes → wiki update (markdown cognition)
3. **Testing:** Write tests before/during implementation
4. **Code Review:** Security + architecture + performance check
5. **SOLID + Clean Code:** Always
6. **No Hallucinations:** Every claim verified via official docs
7. **Performance:** Lighthouse 95+, FCP < 1.0s, memory < 100MB
8. **Maintainability:** Clear naming, modularity, DRY principle

---

## Quality Score (2026-06-11):** 98.7%
