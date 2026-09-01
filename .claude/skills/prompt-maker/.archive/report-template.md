# ÇOKKATMANLI OUTPUT REPORT — TEMPLATE
# Prompt Maker v8.1.0 — 2026-06-11
# Skill çıktı formatı (8-parça report)

---

## 📋 REPORT YAPISI (8 PARÇA)

```
.ai/wiki/prompts-kb/
├── [YYYY-MM-DD]-[slug]-part00-MAPPING.md
├── [YYYY-MM-DD]-[slug]-part01-ANSWERS.md
├── [YYYY-MM-DD]-[slug]-part02-WEB-RESEARCH.md
├── [YYYY-MM-DD]-[slug]-part03-ARCHITECTURE-ANALYSIS.md
├── [YYYY-MM-DD]-[slug]-part04-SECURITY-AUDIT.md
├── [YYYY-MM-DD]-[slug]-part05-TECHNOLOGY-MATRIX.md
├── [YYYY-MM-DD]-[slug]-part06-DECISION-FRAMEWORK.md
├── [YYYY-MM-DD]-[slug]-part07-MASTER-PROMPT-P1.md
├── [YYYY-MM-DD]-[slug]-part08-MASTER-PROMPT-P2.md
└── [YYYY-MM-DD]-[slug]-INDEX.md
```

---

## 📄 PART 0: MAPPING — Soru-Cevap Haritası

```markdown
# Soru-Cevap Haritası & Index

**Proje:** [Proje Adı]
**Tarih:** [YYYY-MM-DD]
**Slug:** [project-slug]
**Toplam Soru:** [N]
**Toplam Cevap:** [N]
**Bloklar:** A-J (10 blok)

---

## Blok Özeti

| Blok | Kategori | Soru # | Cevap # | Detay |
|------|----------|--------|---------|-------|
| A | Proje Kimliği | 35 | 35 | Ad, yaş, kitle, ölçek, takım, bütçe, KPI |
| B | Teknoloji Stack | 45 | 45 | Dil, framework, DB, cache, deployment |
| C | Mimari & Tasarım | 25 | 25 | System design, API, caching, patterns |
| D | Güvenlik | 40 | 40 | OWASP, auth, encryption, compliance |
| E | Performance | 28 | 28 | Hedefler, load testing, optimization |
| F | UI/UX | 32 | 32 | Design system, accessibility, responsive |
| G | DevOps | 38 | 38 | IaC, deployment, monitoring, backup |
| H | Domain-Spesifik | 60 | 60 | Electronics, Audio, ML/AI, Fintech, Health |
| I | Prompt Tipi | 18 | 18 | Output format, scope, research depth |
| J | Kısıtlar | 32 | 32 | Budget, time, legal, integration |
| **TOPLAM** | — | **313** | **313** | — |

---

## Soru-Cevap Detay Tablosu

| # | Blok.Soru | Kategori | Soru | Cevap | Status | Referans |
|----|-----------|----------|------|-------|--------|----------|
| 1 | A01.0.0 | Proje Adı | Tam resmi ad nedir? | [Cevap] | ✅ | block-a.md#A01 |
| 2 | A01.0.1 | Anlam | Ad anlamı nedir? | [Cevap] | ✅ | block-a.md#A01 |
| 3 | A01.1 | Bağlam (Başarı) | Başarılı context | [Cevap] | ✅ | block-a.md#A01 |
| 4 | A01.1.1 | Sub-context | Legal koruma | [Cevap] | ✅ | block-a.md#A01 |
| ... | ... | ... | ... | ... | ... | ... |
| 313 | J32.10 | Risk | Risk mitigasyon | [Cevap] | ✅ | block-j.md#J32 |

```

---

## 📄 PART 1: ANSWERS — Tüm Cevaplar (Döküman)

```markdown
# Tüm Cevaplar — Proje Konteksti Dökümanı

**Proje:** [Name]
**Soru Sayısı:** 313
**Cevap Sayısı:** 313
**Completed:** 100%

---

## BLOK A — PROJE KİMLİĞİ (35 Soru)

### A01 — Proje Adı & Açıklama (10 Soru)

**A01.0.0 [Temel]** Projenin tam resmi adı nedir?
→ **Cevap:** 
```

Örnek cevap format:
```
CoreMusic — Music Streaming & Discovery Platform

Tam ad: CoreMusic GmbH Musikdienst AG
Ülke: Türkiye / EU
Tescil: Ticari Sicil [XYZ]
```

**A01.0.1 [Anlam]** Ad anlamı nedir?
→ **Cevap:** 
```
"Core" = Özü, ana parça (core technology)
"Music" = Müzik endüstrisi
Combined: "Müzik endüstrisinin özünü/çekirdeğini dönüştüren platform"
```

**A01.1 [Bağlam: Başarılı Proje]** Başarılı bir proje senaryosunda ad ve açıklaması
→ **Cevap:** 
```
Senaryo: 10K+ users, 99.9% uptime, Spotify-level features

Ad: CoreMusic
Slogan: "Core Music — Where Art Meets Technology"
Elevator: "Spotify ve Apple Music'e alternatif, artist-first müzik platformu"
```

**A01.1.1 [Sub: Legal]** Bu adla legal çatışma var mı?
→ **Cevap:** 
```
Trademark search sonuç:
- USA: CLEAR (no conflicts)
- EU: CLEAR (generic + descriptive, low risk)
- TR: CLEAR (no music streaming "CoreMusic")

Risk: LOW (generic combination)
Action: Opsiyonel TM registration (EU, USA)
Cost: ~€1500
```

[... 6 tane daha Q+A ...]

### A02 — Proje Yaşı & Durum (10 Soru)

[... benzer format ...]

### A03-A35 — Diğer kategoriler

[... 32 tane daha soru kategorisi ...]

---

## BLOK B — TEKNOLOJI STACK (45 Soru)

[... benzer format ...]

---

## [DEVAMISI BLOK C-J...]
```

---

## 📄 PART 2: WEB-RESEARCH — 200+ Kaynak Dökümanı

```markdown
# Web Araştırması — 200+ Kaynak Envanteri

**Başlama:** 2026-06-11 HH:MM:SS
**Bitiş:** 2026-06-11 HH:MM:SS
**Toplam Kaynak:** 247
**Kategoriler:** 8
**Geçerlileme:** Cross-check 3+ source per critical info

---

## RESMİ DOKÜMANTASYON (45 kaynak)

### PHP & Framework
1. [PHP Official Docs - Security](https://www.php.net/manual/en/security.php)
   - Topic: Input validation, SQL injection prevention
   - Published: 2024-01-15
   - Relevance: HIGH

2. [Laravel Documentation - Security](https://laravel.com/docs/11.x/security)
   - Topic: CSRF, password hashing, encryption
   - Version: 11.0 LTS
   - Relevance: HIGH

... (43 more official docs)

### JavaScript & Frontend (12 kaynak)
...

### Database (8 kaynak)
...

## GÜVENLİK DOKÜMANTASYONU (38 kaynak)

### OWASP
1. [OWASP Top 10:2025](https://owasp.org/Top10/)
   - Content: A01-A10 vulnerabilities
   - Published: 2024-09-01
   - Relevance: CRITICAL

2. [OWASP Cheat Sheet Series](https://cheatsheetseries.owasp.org/)
   - Coverage: 50+ specific topics
   - Relevance: HIGH

... (36 more security sources)

## API & REST DESIGN (42 kaynak)

### REST Best Practices
...

## PERFORMANCE & OPTIMIZATION (35 kaynak)

### Core Web Vitals
...

## DEVOPS & DEPLOYMENT (28 kaynak)

### Kubernetes
...

## UI/UX & ACCESSIBILITY (22 kaynak)

### WCAG 2.2
...

## FRAMEWORK-SPESİFİK (18 kaynak)

### Laravel Ecosystem
...

## DOMAIN-SPESİFİK (19 kaynak)

### Music Streaming Specific
...

---

## Kaynak Referansları (BibTeX Format)

@article{...}
@book{...}
@web{...}

---

## Research Summary

- ✅ Min 50 reached
- ✅ Target 200+ reached (247 sources)
- ✅ All sources verified
- ✅ 3+ source validation on critical items
```

---

## 📄 PART 3: ARCHITECTURE-ANALYSIS — Mimari Analiz

```markdown
# Teknoloji & Mimari Analiz

**Proje Scale:** 10K-100K users
**Target Uptime:** 99.9% SLA
**Budget Constraint:** Moderate (~$100K/year)

---

## Önerilen Mimari Stack

| Layer | Seçim | Gerekçe | Trade-off |
|-------|-------|---------|-----------|
| **Deployment** | Kubernetes (EKS) | Auto-scaling, high availability | Complexity ↑30% |
| **Load Balancer** | AWS ALB | HTTPS termination, multi-zone | Cost $500/mo |
| **API Gateway** | API Gateway + Kong | Rate limiting, auth | Latency +5ms |
| **Reverse Proxy** | Nginx | Caching, compression | Operational overhead |
| **Backend** | PHP 8.4 + Laravel | Rich ecosystem, security | Opinionated framework |
| **Database (Primary)** | MySQL 8.4 | ACID, scaling, backups | Query complexity |
| **Database (Replica)** | MySQL read replica | Read scaling, fallback | Sync latency |
| **Cache (L1)** | Redis Cluster | High performance, atomic ops | Memory cost $2K/mo |
| **Cache (L2)** | HTTP ETag + CDN | Browser caching, edge | Cache invalidation |
| **Search** | Elasticsearch | Full-text, complex queries | Operational complexity |
| **Queue** | RabbitMQ | Message reliability | AMQP overhead |
| **Storage (Files)** | AWS S3 | Scalable, durable | Vendor lock-in |
| **Storage (Sessions)** | Redis | Fast, atomic | In-memory limit |
| **Monitoring** | Prometheus + Grafana | Open-source, extensible | Setup effort |
| **Logging** | ELK Stack | Powerful search, analysis | Operational complexity |
| **Tracing** | Jaeger | Distributed tracing | Privacy concerns |
| **Frontend** | Vanilla JS SPA | No framework bloat | Manual routing/state |
| **Frontend Build** | None (no bundler) | Simplicity, fast iteration | Module federation needed |
| **Testing** | PHPUnit + Vitest + Playwright | Comprehensive coverage | Maintenance burden |
| **CI/CD** | GitHub Actions | Native, free | GitHub-locked |

---

## Deployment Diagram

```
┌─── Internet ───┐
│                │
▼                ▼
CloudFront    Route53
(CDN)         (DNS)
│                │
└────────┬───────┘
         │
         ▼
      ALB
   (AWS Load Balancer)
         │
   ┌─────┴─────┬─────────┐
   ▼           ▼         ▼
 Pod1        Pod2      Pod3
(PHP+Laravel)
   │           │         │
   └─────┬─────┴────┬────┘
         │          │
         ▼          ▼
     MySQL       Redis
    (Primary)    (Cache)
         │
         ▼
    MySQL Replica
    (Read-only)
```

---

## Scaling Strategy

1. **Horizontal Scaling:** Auto-scale pods 2-20 (CPU > 70%)
2. **Database Scaling:** Read replicas (3x), sharding at 1M rows
3. **Cache Scaling:** Redis cluster mode (6 nodes)
4. **CDN:** CloudFront edge locations (200+)

---

## Cost Breakdown (Annual)

| Component | Cost | Notes |
|-----------|------|-------|
| EKS cluster | $15K | 2 AZs, managed |
| RDS MySQL | $12K | m5.large, 500GB |
| Redis | $6K | 256GB cluster |
| S3 | $4K | 10TB storage |
| CloudFront | $8K | ~1TB/month egress |
| ALB | $18K | $0.025/hour + data |
| Elasticsearch | $3K | 50GB/month |
| **Total (Annual)** | **~$66K** | +30% buffer = $86K |

---

## Alternatives Considered

### 1. Monolith + Auto-scaling
- ✅ Lower complexity
- ❌ Single DB bottleneck at 10K+ users
- ❌ Not recommended

### 2. Serverless (Lambda)
- ✅ Zero ops, auto-scaling
- ❌ Cold starts (300-500ms)
- ❌ Not suitable for music streaming

### 3. Multi-region Active-Active
- ✅ Global availability
- ❌ Data consistency complexity
- ❌ Cost 3x
```

---

## 📄 PART 4: SECURITY-AUDIT — Güvenlik Auditi

```markdown
# Güvenlik Analizi & OWASP Top 10:2025 Mapping

**Audit Date:** 2026-06-11
**Auditor:** AI Prompt Engineer
**Compliance Level:** HIGH (enterprise-grade)

---

## OWASP Top 10:2025 Assessment

| # | Vulnerability | Risk | Current Status | Mitigation | Priority |
|----|----------------|------|----------------|-----------|----------|
| A01 | Broken Access Control | CRITICAL | ⚠️ PARTIAL | Implement RBAC + ABAC layer | HIGH |
| A02 | Cryptographic Failures | CRITICAL | ✅ FULL | AES-256-GCM + TLS 1.3 everywhere | — |
| A03 | Injection (SQL/CMD) | CRITICAL | ✅ FULL | PDO prepared statements 100% | — |
| A04 | Insecure Design | HIGH | ⚠️ PARTIAL | Threat modeling STRIDE needed | HIGH |
| A05 | Security Misconfiguration | HIGH | ⚠️ PARTIAL | Review all security headers | MEDIUM |
| A06 | Vulnerable Components | HIGH | ⚠️ MONITOR | Composer audit, auto-updates | MEDIUM |
| A07 | Auth Failures | CRITICAL | ⚠️ PARTIAL | Upgrade to JWT + MFA | HIGH |
| A08 | Data Integrity | HIGH | ⚠️ PARTIAL | Add HMAC signatures, audit log | HIGH |
| A09 | Logging Failures | HIGH | ⚠️ MONITOR | Centralize to ELK, alerts | MEDIUM |
| A10 | SSRF | MEDIUM | ✅ FULL | URL whitelist + DNS rebind protection | — |

---

## Threat Model (STRIDE)

**Spoofing:** (Fake identity)
- Attack: JWT token hijacking
- Impact: Account takeover
- Mitigation: Secure cookie (HttpOnly, Secure, SameSite=Lax)
- Priority: HIGH

**Tampering:** (Modify data)
- Attack: XSS payload injection
- Impact: User data theft
- Mitigation: Trusted Types + CSP nonce
- Priority: HIGH

[... T, R, D, E remaining ...]

---

## Compliance Checklist

### PCI DSS (Payment Processing)
- [ ] Network segmentation
- [ ] Encryption (AES-256)
- [ ] Access controls
- [ ] Regular security testing
- [ ] Incident response plan
**Status:** ⚠️ PARTIAL (planning stage)

### GDPR (Data Privacy)
- [x] User consent mechanisms
- [x] Data retention policies
- [ ] Right-to-be-forgotten automation
- [x] Privacy policy
- [x] Breach notification (30 days)
**Status:** ✅ MOSTLY COMPLIANT

### SOC 2 (Security Controls)
- [ ] Access logging
- [ ] Encryption controls
- [ ] Incident management
- [ ] Change management
**Status:** ⚠️ IN PROGRESS

---

## Security Recommendations (Ranked)

1. **CRITICAL:** Implement RBAC + ABAC for access control
2. **CRITICAL:** Upgrade auth to JWT + MFA
3. **HIGH:** Add HTTPS everywhere (done), CSP nonce
4. **HIGH:** Implement data integrity (HMAC + audit)
5. **HIGH:** Threat modeling + penetration testing
6. **MEDIUM:** Centralized logging (ELK)
7. **MEDIUM:** Composer audit automation
8. **MEDIUM:** Security headers review (HSTS, X-Frame-Options)
```

---

## 📄 PART 5: TECHNOLOGY-MATRIX — Teknoloji Matrisi

```markdown
# Teknoloji Stack Matrix & Justification

**Review Date:** 2026-06-11
**Decision Maker:** Architecture Team
**Final Status:** APPROVED

---

## 1. PROGRAMLAMA DİLLERİ

| Dil | Seçim | Versiyon | Justification | Performance | Constraints |
|-----|-------|----------|---------------|-------------|-------------|
| PHP | ✅ | 8.4 | strict_types, security | Good | Type system |
| JavaScript | ✅ | ES2022 | Vanilla, no bundler | Excellent | DOM limits |
| SQL | ✅ | MySQL 8.4 | ACID + scaling | Good | Query complexity |
| Bash | ✅ | 4.0+ | IaC + DevOps | N/A | POSIX compliance |
| Python | ⚠️ | 3.11 | Optional (ML/AI) | Depends | Setup complexity |

---

## 2. FRAMEWORK & LIBRARIES

| Category | Seçim | Version | Why | Trade-off | Cost |
|----------|-------|---------|-----|-----------|------|
| Backend Framework | Laravel | 11 LTS | Rich, secure, documented | Opinionated | Free |
| Frontend Router | Custom | — | Control + lightweight | Manual work | Time |
| ORM | Eloquent | 11 | Elegant, Laravel-native | Locked to Laravel | Free |
| Testing (Unit) | PHPUnit | 10 | Industry standard | Setup overhead | Free |
| Testing (E2E) | Playwright | 1.40 | Modern, multi-browser | Learning curve | Free |
| Caching | Redis | 7.0+ | High-perf, atomic | Dependency | Self-hosted |
| Queue | RabbitMQ | 3.12 | Reliable, AMQP | Operational overhead | Self-hosted |
| Search | Elasticsearch | 8.10 | Full-text + complex | Resource-heavy | $2K+/year |
| Monitoring | Prometheus | 2.48 | Open-source, extensible | Setup effort | Free |

---

## 3. DATABASE SELECTIONS

| Tier | Database | Version | Purpose | Why | Limits |
|------|----------|---------|---------|-----|--------|
| Primary | MySQL | 8.4 LTS | User, music, playlist data | ACID, scaling | Complex joins |
| Replica | MySQL | 8.4 | Read scaling, backup | Same engine | Sync latency |
| Cache | Redis | 7.0 | Sessions, leaderboards | Fast, atomic | Memory-limited |
| Search | Elasticsearch | 8.10 | Full-text music search | Powerful, flexible | Complex ops |
| Logs | DynamoDB | On-demand | Application logs | Serverless | Expensive at scale |

---

## 4. DEPLOYMENT SELECTIONS

| Component | Selection | Version | Trade-off | Cost/Month |
|-----------|-----------|---------|-----------|-----------|
| Container | Docker | 24.x | Industry standard | Free |
| Orchestration | Kubernetes | 1.28 (EKS) | Complexity ↑ | $1,250 |
| Registry | ECR | AWS | Vendor lock-in | $10 |
| Load Balancer | ALB | AWS | Native integration | $1,500 |
| CDN | CloudFront | AWS | Fast delivery | $500 |
| DNS | Route 53 | AWS | Health checks | $50 |
| SSL/TLS | ACM | AWS | Free, auto-renewal | Free |

---

## 5. DEVOPS TOOLS

| Function | Tool | Version | Why | Setup Time |
|----------|------|---------|-----|-----------|
| IaC | Terraform | 1.6 | Multi-cloud, HCL | 2 days |
| CI/CD | GitHub Actions | Native | Free, integrated | 1 day |
| Monitoring | Prometheus + Grafana | Latest | Open-source, powerful | 3 days |
| Logging | ELK Stack | 8.10 | Centralized search | 3 days |
| Secrets | AWS Secrets Manager | Native | Encrypted, rotated | 1 day |
| Backup | AWS Backup | Native | Automated, cheap | 1 day |

---

## Compatibility Matrix

```
PHP 8.4 ←→ Laravel 11 ←→ MySQL 8.4
   ↓         ↓              ↓
PDO      Eloquent ORM    Prepared statements
   ↓         ↓              ↓
strict_types Immutable DTO ACID transactions
```
```

---

## 📄 PART 6: DECISION-FRAMEWORK — Karar Ağaçları

```markdown
# Karar Ağaçları & Tercih Mantığı

**Format:** Decision tree with YES/NO branches
**Scope:** 7 critical architectural decisions
**Owner:** Technical Steering Committee

---

## Decision 1: Microservices vs Monolith

\`\`\`
Question: Expected users at launch?
├─ <1K users? → Monolith (faster time-to-market)
├─ 1K-10K users? → Modular monolith (hybrid approach) ← CURRENT
├─ 10K-100K users?
│   ├─ Team size < 5? → Monolith (organizational limit)
│   └─ Team size >= 6? → Microservices (Conway's Law) ← RECOMMENDED
└─ 100K+ users? → Microservices mandatory
\`\`\`

**DECISION:** Microservices (10K+ users, 6+ team)
**Rationale:** Scale requirement, team capacity
**Alternative:** Modular monolith (lower risk, less complexity)
**Trade-off:** +30% operational complexity

---

## Decision 2: Database Selection

\`\`\`
Question: Consistency requirement?
├─ Eventual OK? → MongoDB (flexible, fast writes)
├─ Strong ACID? → PostgreSQL or MySQL ← SELECTED
│   ├─ JSON support needed? → PostgreSQL (JSONB)
│   └─ Simplicity preferred? → MySQL ← CURRENT
└─ High write throughput? → Cassandra

Question: Scale expectations?
├─ <1M rows? → Single MySQL fine
├─ 1M-10M rows? → Read replica + caching ← CURRENT
├─ 10M-100M rows? → Sharding needed
└─ 100M+ rows? → Multi-region cluster

DECISION: MySQL 8.4 master-slave with Redis L1 cache
RATIONALE: ACID compliance, read scaling, mature ops
ALTERNATIVE: PostgreSQL (more features, slower writes)
TRADE-OFF: Complex sharding strategy needed at 100M rows
\`\`\`

---

## Decision 3: Caching Strategy

\`\`\`
Question: Cache invalidation complexity tolerance?
├─ Low tolerance? → HTTP ETag only (simple)
├─ Medium tolerance? → Redis + ETag (hybrid) ← CURRENT
└─ High tolerance? → Redis + pub/sub (complex)

Question: Cache consistency requirements?
├─ Eventual OK? → Redis TTL (simple)
├─ Eventual OK (better)? → Redis + event-driven ← RECOMMENDED
└─ Strong consistency? → Database-backed (slow)

DECISION: Redis cluster + HTTP ETag + event invalidation
RATIONALE: Performance, eventual consistency acceptable
ALTERNATIVE: Simple TTL (faster setup, stale data risk)
TRADE-OFF: Operational complexity +15%
\`\`\`

---

## Decision 4: Authentication Strategy

\`\`\`
Question: Session state allowed?
├─ NO (stateless APIs)? → JWT only
├─ YES? → Session-based or hybrid ← CURRENT

Question: Security level needed?
├─ Low (public app)? → JWT, no refresh
├─ Medium? → JWT + refresh token ← SELECTED
├─ High (fintech)? → JWT + MFA + refresh + rotation
└─ Critical? → Hardware token (FIDO2)

DECISION: JWT + refresh token + session hybrid
RATIONALE: Balance security + performance
ALTERNATIVE: Pure session-based (simpler, less scalable)
TRADE-OFF: Token overhead ~2KB per request
\`\`\`

---

## Decision 5: Frontend Architecture

\`\`\`
Question: Build tool tolerance?
├─ No build step? → Vanilla JS SPA ← CURRENT
├─ Simple bundler? → Vite + Vue (light)
├─ Full framework? → React/Next.js (heavy)
└─ Experimental? → Lit / Web components

DECISION: Vanilla JS SPA (no bundler, no framework)
RATIONALE: Simplicity, fast iteration, vendor-independent
ALTERNATIVE: Vite + Vue (faster iteration, light overhead)
TRADE-OFF: Manual routing, manual state management
\`\`\`

---

## Decision 6: Testing Strategy

\`\`\`
Question: Test coverage requirement?
├─ <50%? → Smoke tests only
├─ 50-75%? → Unit + integration ← CURRENT
├─ 75-90%? → Unit + integration + E2E
└─ >90%? → 100% coverage (diminishing returns)

DECISION: 85% coverage (unit + integration + E2E)
RATIONALE: Enterprise stability + maintainability
ALTERNATIVE: 70% (faster, less overhead)
TRADE-OFF: +30% development time
\`\`\`

---

## Decision 7: DevOps Infrastructure

\`\`\`
Question: Operational expertise?
├─ Low (no DevOps team)? → Managed services (serverless)
├─ Medium (1 DevOps)? → Kubernetes managed (EKS) ← CURRENT
├─ High (2+ DevOps)? → Self-managed K8s
└─ Ultra-high? → Multi-cloud orchestration

DECISION: AWS EKS (managed Kubernetes)
RATIONALE: Balance control + operational overhead
ALTERNATIVE: Fargate (simpler, less control)
TRADE-OFF: $1250/month for control layer
\`\`\`

---

## Summary Matrix

| Decision | Choice | Complexity | Cost | Scalability | Risk |
|----------|--------|------------|------|-------------|------|
| 1. Architecture | Microservices | HIGH | $100K+/y | EXCELLENT | MEDIUM |
| 2. Database | MySQL + Redis | MEDIUM | $18K+/y | GOOD | LOW |
| 3. Caching | Redis + ETag | MEDIUM | $8K+/y | EXCELLENT | LOW |
| 4. Auth | JWT + session | MEDIUM | Free | GOOD | MEDIUM |
| 5. Frontend | Vanilla JS | LOW | Free | GOOD | LOW |
| 6. Testing | 85% coverage | MEDIUM | Time | EXCELLENT | LOW |
| 7. DevOps | EKS | MEDIUM | $100K+/y | EXCELLENT | MEDIUM |

```

---

## 📄 PART 7: MASTER-PROMPT-P1 — Sistem Prompts Bölüm 1

```markdown
# MASTER PROMPT — PART 1/2

## 1. SYSTEM IDENTITY & ROLE

Sen bir **Senior Principal Engineer — 40+ yıllık eşdeğer expertise**'ye sahipsin.

### Domains

- **Backend:** PHP, Python, Node.js, C#, Go, Rust (6-10 yıl)
- **Frontend:** Vanilla JS, React, Vue (8+ yıl)
- **Database:** MySQL, PostgreSQL, MongoDB, Redis (10+ yıl)
- **DevOps:** Kubernetes, Terraform, CI/CD, Docker (7+ yıl)
- **Security:** OWASP, cryptography, threat modeling (8+ yıl)
- **Architecture:** SOLID, Clean Code, DDD, Microservices (10+ yıl)

### Responsibilities

- Code review, architecture decisions, mentoring
- Security-first mindset (active attacker assumption)
- Performance optimization, scaling strategies
- Production reliability engineering

---

## 2. EXPERIENCE LEVEL

**Prerequisites:**
- 6-10+ yıl freelance → enterprise backend engineering
- PHP 7+ (PSR-4, strict_types), Laravel, Symfony
- MySQL, PostgreSQL, Redis, Elasticsearch
- Distributed systems, microservices, messaging
- Docker, Kubernetes, Terraform, CI/CD
- Testing: unit, integration, E2E (PHPUnit, pytest, Jest)
- Security: OWASP Top 10, encryption, auth/authz

**NOT assumed:**
- UI/UX design expertise (use frameworks)
- Bleeding-edge ML (use best practices, not research)
- Kernel development (use OS APIs)

---

## 3. PROJECT CONTEXT

**Project:** CoreMusic — Music Streaming & Discovery Platform
**Scale:** 10K-100K concurrent users
**Budget:** ~$100K/year operational
**Timeline:** 2-4 weeks MVP, 6-12 months full feature set
**Team:** 6-12 engineers (backend, frontend, DevOps, QA)
**Status:** Architecture → Development → Production phases

**Key Requirements:**
- 99.9% uptime SLA
- <100ms response time (p99)
- GDPR/KVKK compliant
- Artist-first design (royalties, analytics)
- Multi-region support (future)

---

## 4. TECHNOLOGY STACK

### Backend
- **Language:** PHP 8.4, strict_types=1 mandatory
- **Framework:** Laravel 11 LTS
- **ORM:** Eloquent with query optimization
- **Database:** MySQL 8.4 (master-slave replication)
- **Cache:** Redis 7.0 (L1 distributed, L2 HTTP ETag, L3 CDN)
- **Queue:** RabbitMQ 3.12 (async processing)
- **Search:** Elasticsearch 8.10 (full-text music search)
- **Logging:** ELK Stack (centralized, searchable)

### Frontend
- **Language:** Vanilla ES2022 (no framework)
- **Architecture:** SPA with custom router
- **CSS:** ITCSS (inverted triangle)
- **Testing:** Vitest (unit) + Playwright (E2E)
- **Performance:** Lighthouse 95+, LCP <2.5s

### DevOps
- **Containers:** Docker (Alpine base, <150MB images)
- **Orchestration:** AWS EKS (Kubernetes 1.28)
- **Load Balancer:** AWS ALB
- **CDN:** CloudFront (200+ edge locations)
- **IaC:** Terraform
- **CI/CD:** GitHub Actions
- **Monitoring:** Prometheus + Grafana
- **Secrets:** AWS Secrets Manager

---

## 5. CORE OBJECTIVE

Produce **production-grade music streaming platform** that:

1. ✅ Handles 10K-100K concurrent users with <100ms latency
2. ✅ OWASP Top 10:2025 compliant (security-first)
3. ✅ 99.9% uptime SLA with auto-recovery
4. ✅ GDPR/KVKK data privacy enforcement
5. ✅ Full audit trail + structured logging
6. ✅ Self-healing + resilient to failures (circuit breaker, retry)
7. ✅ Observable (metrics, logs, traces, alerts)
8. ✅ Maintainable (SOLID, clean code, well-tested)

---

## 6. ACTIVATION TRIGGERS

These keywords automatically activate this prompt:

```
laravel, music streaming, backend architecture, php security,
database scaling, redis caching, kubernetes deployment,
microservice architecture, api design, rest api, jwt authentication,
owasp compliance, rate limiting, payment integration,
distributed tracing, devops pipeline, infrastructure as code,
terraform, docker, ci/cd, performance optimization,
database replication, elasticsearch full-text search,
rabbitmq async processing, api rate limiting, jwt token,
database sharding, load balancing, cdn strategy,
monitoring prometheus, structured logging, iam rbac
```

---

## 7. EXECUTION PIPELINE (Core)

```
USER REQUEST
  → Input validation (type checking, schema)
  → Rate limiting (60 req/min per user)
  → Authentication (JWT + session check)
  → Authorization (RBAC + ABAC)
  → Middleware pipeline (security headers, CORS, CSRF)
  → Request handler (business logic, transactions)
  → Response validation (schema, type safety)
  → Caching (Redis L1 + HTTP ETag L2)
  → Structured logging (traceId, userId, endpoint, latency)
  → APM/monitoring (metrics, error tracking, alerts)
  → Response formatting (JSON + HAL hypermedia)
```

---

## 8. RULES ENGINE — HARD RULES (MUST)

### Security (OWASP Top 10:2025)

```php
// ✅ MANDATORY — Prepared statements
$stmt = $pdo->prepare('SELECT id, username FROM users WHERE id = :id LIMIT 1');
$stmt->bindValue(':id', $userId, PDO::PARAM_INT);
$stmt->execute();

// ❌ FORBIDDEN — SQL injection
$sql = "SELECT * FROM users WHERE id = " . $_GET['id'];

// ✅ MANDATORY — Argon2id hashing
password_hash($password, PASSWORD_ARGON2ID, [
    'memory_cost' => 65536,
    'time_cost' => 4,
    'threads' => 2,
]);

// ❌ FORBIDDEN — Weak hashing
$hash = md5($password);  // RCE risk, no salt
$hash = sha1($password); // Slow, weak
```

### Database

- **Prepared statements mandatory** (100% of queries)
- **SELECT * forbidden** (explicit columns only)
- **No N+1 queries** (eager load with with() / includes)
- **Transactions for multi-step operations** (SERIALIZABLE)
- **Explicit indexes** (EXPLAIN every query)
- **Schema versioning** (migrations tracked in git)

### API Security

- **CORS:** Whitelist known origins only
- **Rate limiting:** 60 req/min per user IP (APCu backend)
- **CSRF:** SameSite=Lax + token (POST/PUT/DELETE)
- **HTTPS mandatory** (TLS 1.3 minimum)
- **CSP:** nonce-based + strict-dynamic
- **Input validation:** Every endpoint validates schema (strict)
- **Output encoding:** JSON safe (no raw HTML)

### Code Quality

- **strict_types=1** — Every PHP file, no coercion
- **Final by default** — Immutable where possible
- **Dependency injection** — Constructor only, no setters
- **No globals** — Banned (super-globals, constants, statics)
- **Immutable DTOs** — readonly properties, frozen objects

---

## 9. RULES ENGINE — SOFT RULES (SHOULD)

```
⚠️ Use repository pattern for data access (IRepository interface)
⚠️ Service layer for business logic (no controllers doing math)
⚠️ Event-driven for async work (events, listeners, queues)
⚠️ Distributed tracing (Jaeger + OpenTelemetry headers)
⚠️ Bulkhead pattern for failure isolation (concurrent limiters)
⚠️ Circuit breaker for external calls (fail-fast, retry backoff)
⚠️ Health check endpoints (/health, /ready, /live)
⚠️ Structured logging (JSON, correlation IDs, sensitive data redaction)
⚠️ APM instrumentation (request latency, error rate, business metrics)
⚠️ Cache warming (preload hot data on startup)
```

---

## 10. LANGUAGE-SPECIFIC STANDARDS (PHP 8.4)

[... kontinua part 2'de ...]
```

---

## 📄 PART 8: MASTER-PROMPT-P2 — Devamı (Bölüm 11-20)

```markdown
[Continuation of MASTER PROMPT sections 11-20...]

11. SECURITY — OWASP TOP 10:2025
12. PERFORMANCE STANDARDS
13. UI/UX STANDARDS
14. TESTING STRATEGY
15. DEVOPS & DEPLOYMENT
16. INPUT HANDLING & VALIDATION
17. PROCESS FLOW & DATA LIFECYCLE
18. OUTPUT FORMAT & RESPONSE STRUCTURE
19. EDGE CASE HANDLING
20. QUALITY CONTROL CHECKLIST
```

---

## 📄 INDEX — Final Metadata & Referanslar

```markdown
# Report Index & Metadata

**Report ID:** [UUID]
**Project:** CoreMusic
**Date:** 2026-06-11
**Status:** COMPLETE (8/8 parts)

---

## File Listing

- Part 0: MAPPING (35 KB)
- Part 1: ANSWERS (150 KB)
- Part 2: WEB-RESEARCH (85 KB)
- Part 3: ARCHITECTURE (65 KB)
- Part 4: SECURITY-AUDIT (55 KB)
- Part 5: TECHNOLOGY-MATRIX (45 KB)
- Part 6: DECISION-FRAMEWORK (60 KB)
- Part 7: MASTER-PROMPT-P1 (120 KB)
- Part 8: MASTER-PROMPT-P2 (135 KB)
- **TOTAL: ~750 KB**

---

## Cross-References

- Questions: questions-block-A.md through J.md
- References: /references/ 27 files
- Brain: .ai/brain.md (append)
- Log: .ai/log.md (append)
- Index: .ai/wiki/index.md (update)

---

*Report Template — Prompt Maker v8.1.0*
