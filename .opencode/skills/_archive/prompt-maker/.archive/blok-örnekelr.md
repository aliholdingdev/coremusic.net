# BLOK ÖRNEKLERI — HER A-J BLOĞUNdan 3 Q&A

---

## BLOK A — PROJE KİMLİĞİ (Örnek Cevaplar)

### A1.1 Projenin tam resmi adı nedir?

**Soru:** Projenin tam resmi adı nedir?

**Cevap Örneği:**
```
CoreMusic GmbH — Musik Streaming & Discovery Platform
Ülke: Türkiye (kayıtlı), EU hukuki entity: Luxembourg
Ticari Ad: CoreMusic Music Tech AG
Resmi Tescil: TR Ticaret Sicili No: 123456
```

### A1.14 Projenin ortaya çıkma sebebi nedir?

**Soru:** Projenin ortaya çıkma sebebi nedir? → Spesifik problem tanımla

**Cevap Örneği:**
```
PROBLEM:
  Spotify/Apple Music'te artist royalty oranları düşük (~30%)
  ve şeffaf değil. Indie artist'ler gelir elde edemiyorlar.

IMPACT:
  - 10M indie artist küresel olarak etkileniyor
  - Yıllık kayıp: ~$2B (royalty gap)
  - Problem aciliyet: CRITICAL (artist'ler yoksulluk çekiyor)

ÇÖZÜM ZAMANI:
  6-9 ay (MVP), 18-24 ay (full platform)

BEKLENEN SONUÇ:
  Artist'ler %50-70% royalty alabilir (Spotify vs CoreMusic)
```

### A1.29 Unique Value Proposition nedir?

**Soru:** Unique Value Proposition (UVP) nedir? → "Sadece bizde olan şey..."

**Cevap Örneği:**
```
UVP: "Artist-first music streaming — highest royalties, complete transparency, ownership control"

Core Benefit:
  - Highest Royalties: 60-70% (vs Spotify 30%)
  - Transparency: Real-time earning dashboard
  - Ownership: Artist owns data, analytics, metadata

Functional:
  - Direct artist payment (no middleman)
  - Blockchain-backed ownership (smart contracts)
  - Advanced analytics dashboard

Emotional:
  - Artist empowerment ("control your music destiny")
  - Fair pay ("you earn what you deserve")
  - Community ("artist-owned platform")

Social:
  - Support indie music (counter Spotify hegemony)
  - Economic justice (fair compensation)
```

---

## BLOK B — TEKNOLOJİ STACK (Örnek Cevaplar)

### B01.0 Backend dili nedir?

**Soru:** Backend dili nedir? (PHP/Python/Node/Go/Rust/C#/.NET/Java)

**Cevap Örneği:**
```
SEÇİM: PHP 8.4 (strict_types=1)

GEREKÇESİ:
  ✅ Mature ecosystem (Laravel 11 LTS)
  ✅ Type safety (strict mode, typed properties)
  ✅ Security-first (built-in SQL injection protection via PDO)
  ✅ Fast iteration (no compilation, hot reload capable)
  ✅ Cost-effective (shared hosting + cloud pricing)

TRADE-OFF:
  ❌ Async programming (need custom queue system)
  ❌ Type system (less powerful than Rust/Go)

ALTERNATIF:
  Python (Django): Better ecosystem, slower
  Go (Gin): Faster, steeper learning curve
  Node.js (Express): Better async, less stable for business logic
```

### B02.0 Backend framework?

**Soru:** Backend framework? (Laravel/Symfony/Django/FastAPI/Express/Gin/Actix)

**Cevap Örneği:**
```
SEÇİM: Laravel 11 LTS

GEREKÇESİ:
  ✅ Rich ecosystem (Eloquent ORM, Queue, Cache, Auth)
  ✅ Security features (CSRF, rate limiting, auth middleware)
  ✅ Opinionated (faster team onboarding)
  ✅ Active community (security updates, packages)
  ✅ LTS support (2-4 years stable)

PACKAGES PLAN:
  - Eloquent ORM (database)
  - Queue (async jobs via RabbitMQ)
  - Cache (Redis + file caching)
  - Auth (JWT + session hybrid)
  - Testing (PHPUnit, Laravel TestCase)

TRADE-OFF:
  ❌ Opinionated (less flexible)
  ❌ Slower than Go/Rust for high-throughput
  ❌ Framework overhead (~50KB minimum binary)
```

### B03.0 Primary database?

**Soru:** Primary database? (MySQL/PostgreSQL/SQLite/MongoDB/DynamoDB)

**Cevap Örneği:**
```
SEÇİM: MySQL 8.4 LTS

GEREKÇESİ:
  ✅ ACID transactions (music library consistency critical)
  ✅ Mature & stable (10+ years production)
  ✅ Scaling ready (read replicas, sharding)
  ✅ Cost-effective (open-source, low licensing)
  ✅ Team expertise (most developers know SQL)

ARCHITECTURE:
  - Primary (writes): MySQL master
  - Replica (reads): 2-3 read replicas (geographic)
  - Backup: Automated daily snapshots
  - Sharding: At 100M rows (by user_id)

TRADE-OFF:
  ❌ Complex joins (denormalization needed)
  ❌ Schema migrations (downtime risk at scale)
  ❌ Less flexible than MongoDB (fixed schema)

ALTERNATIVE:
  PostgreSQL: Better features, 10-15% slower writes
  MongoDB: Flexible schema, eventual consistency risk
```

---

## BLOK D — GÜVENLİK (Örnek Cevaplar)

### D01.0 Genel güvenlik seviyesi nedir?

**Soru:** Genel güvenlik seviyesi nedir? (düşük/orta/yüksek/kritik)

**Cevap Örneği:**
```
SEÇİM: YÜKSEK (Enterprise-grade)

GEREKÇESİ:
  - Finansal veri (royalty payments, transactions)
  - Kişisel veri (kullanıcı profil, yaşama alışkanlıkları)
  - Fikri mülkiyet (şarkı metadata, analytics)
  - Düzenleme (GDPR, KVKK compliance)

STANDARTLAR GEREKLI:
  ✅ OWASP Top 10:2025 (web security)
  ✅ GDPR (EU users — 30+ countries)
  ✅ KVKK (Türkiye users — strict rules)
  ⚠️ PCI DSS (payment processing — if added)

RISK LEVEL:
  - Confidentiality: HIGH (artist data, payment info)
  - Integrity: HIGH (financial accuracy critical)
  - Availability: HIGH (99.9% uptime SLA)
```

### D02.0 Auth mekanizması?

**Soru:** Auth mekanizması? (username/password/OAuth/JWT/Passwordless/MFA)

**Cevap Örneği:**
```
SEÇİM: JWT + Session Hybrid + MFA (Optional)

KOMPONENTLERİ:
  1. Initial login: username + password
  2. Response: Access token (JWT, 15 min) + Refresh token (7 days)
  3. Per-request: Bearer {accessToken} header
  4. Refresh: POST /auth/refresh (sends refresh token)
  5. Logout: Blacklist refresh token (Redis)

MFA (Premium users):
  - TOTP (Google Authenticator)
  - SMS backup codes
  - Optional (not forced, UX friction)

PASSWORD POLICY:
  - Min 12 chars
  - Complexity: 1 upper + 1 lower + 1 digit + 1 special
  - No common patterns (dictionary check)
  - Expiration: 90 days (artists only)

HASHING:
  - Argon2id (RFC 9106)
  - Memory: 65536 KB
  - Time: 4 iterations
  - Threads: 2

TRADE-OFF:
  ❌ JWT stateless (can't revoke immediately)
  ✅ Refresh token + blacklist (hybrid solves this)
```

---

## BLOK E — PERFORMANCE (Örnek Cevaplar)

### E01.0 Response time hedefi?

**Soru:** Response time hedefi? (P50, P95, P99)

**Cevap Örneği:**
```
HEDEFLER:
  P50: <50ms (median user experience)
  P95: <200ms (good experience for 95% users)
  P99: <500ms (acceptable for slow networks)
  P999: <1000ms (worst case, should be rare)

API ENDPOINTS:
  GET /tracks: <20ms (cached)
  GET /search: <100ms (Elasticsearch)
  POST /listen: <30ms (append-only log)
  POST /payment: <200ms (3rd party gateway call)

FRONTEND:
  LCP (Largest Contentful Paint): <1.5s
  FCP (First Contentful Paint): <0.8s
  TTI (Time to Interactive): <2.5s

MONITORING:
  - Prometheus + Grafana (live dashboards)
  - DataDog APM (distributed traces)
  - Alerts: P99 > 500ms trigger page

TRADE-OFF:
  - <50ms P50: Requires Redis cache (cost)
  - <1.5s LCP: Requires CDN + image optimization
```

---

## BLOK F — UI/UX (Örnek Cevaplar)

### F01.0 Design system var mı?

**Soru:** Design system var mı? (evet/hayır/kısmen)

**Cevap Örneği:**
```
SEÇİM: CUSTOM design system (Tailwind CSS base)

COMPONENTS:
  - Button: 4 states (default, hover, active, disabled)
  - Card: Music, artist, playlist variants
  - Modal: Player, settings, confirmation
  - Form: Input, select, checkbox, toggle
  - Header: Navigation + search + profile
  - Footer: Player controls

DESIGN TOKENS:
  Colors: Primary (#ff4fd8), Secondary (#00d4ff), Neutral (#1a1a1a)
  Typography: Inter (headings), Roboto (body)
  Spacing: 4px grid (4, 8, 12, 16, 20, 24, 32, 48, 64)
  Shadows: 3 levels (subtle, medium, strong)

DARK/LIGHT THEME:
  - Dark default (music app UX best practice)
  - Light option (accessibility)
  - System preference detection

WCAG COMPLIANCE:
  - AA level (minimum)
  - AAA where possible (headings, buttons)
  - Focus outlines: 3px
  - Touch targets: 48×48px minimum

ACCESSIBILITY:
  - Screen reader tested (NVDA, JAWS)
  - Keyboard navigation (Tab, Enter, Escape)
  - Color contrast: 4.5:1 minimum
```

---

## BLOK G — DEVOPS (Örnek Cevaplar)

### G01.0 Infrastructure as Code?

**Soru:** Infrastructure as Code? (Terraform/CloudFormation/Ansible/Pulumi)

**Cevap Örneği:**
```
SEÇİM: Terraform (HCL)

STACK:
  - Provider: AWS (EKS, RDS, S3, CloudFront, ALB)
  - State: Remote (S3 + DynamoDB lock)
  - Modules: VPC, EKS, RDS, Redis, monitoring

FILES:
  main.tf                (provider, backend, modules)
  variables.tf           (input variables)
  outputs.tf             (cluster endpoint, DB host)
  environments/
    ├─ dev.tfvars       (dev cluster config)
    ├─ staging.tfvars   (staging cluster)
    └─ prod.tfvars      (production)

DEPLOYMENT:
  - GitOps: Push to main → GitHub Actions → Terraform apply
  - Plan review: Pull request shows infrastructure diff
  - Auto-approval: Staging (auto), Production (manual)

VERSION CONTROL:
  - terraform: 1.6+
  - modules: Git submodules or Terraform registry

MONITORING:
  - Terraform Cloud: Cost estimation, state visualization
  - Policy: Enforce allowed instance types, tag requirements
```

---

## BLOK H — DOMAIN-SPESİFİK (Örnek Cevaplar)

### H3 — ML/AI (Örnek)

**Soru:** Hangi ML framework kullanılıyor? (TensorFlow/PyTorch/ONNX)

**Cevap Örneği:**
```
SEÇİM: PyTorch (inference only, no training)

USE CASE:
  - Music recommendation: Collaborative filtering
  - Playlist generation: Sequence-to-sequence
  - Genre classification: Convolutional neural network

MODELS:
  - Recommendation: 50M parameters (10GB memory)
  - Classification: 12M parameters (2GB)
  - Inference: ONNX format (cross-platform)

DEPLOYMENT:
  - Inference server: ONNX Runtime (C++)
  - Latency: <100ms per request (GPU)
  - Scaling: Kubernetes replicas (GPU pods)
  - Caching: Redis (popular recommendations cached)

TRAINING (External):
  - Separate ML pipeline (not in production servers)
  - Batch training: Weekly (new data)
  - A/B test models before rollout
```

---

## BLOK I — PROMPT TİPİ (Örnek Cevaplar)

### I01.0 Ne tür prompt üretilmeli?

**Soru:** Ne tür prompt üretilmeli? (system/steering/hook/skill/rules/MASTER)

**Cevap Örneği:**
```
SEÇİM: MASTER PROMPT (comprehensive, all sections)

FORMAT:
  - Markdown (.md file)
  - 20 sections (identity, rules, architecture, etc.)
  - 300-500K characters
  - Code examples in PHP, JavaScript, SQL

OUTPUT PARTS:
  1. MAPPING (Q→A matrix)
  2. ANSWERS (all Q&A)
  3. WEB-RESEARCH (200+ sources, BibTeX)
  4. ARCHITECTURE (tech stack matrix)
  5. SECURITY-AUDIT (OWASP mapping)
  6. TECHNOLOGY-MATRIX (trade-offs)
  7. DECISION-FRAMEWORK (decision trees)
  8. MASTER-PROMPT-P1 (sections 1-10)
  9. MASTER-PROMPT-P2 (sections 11-20)
  10. INDEX (metadata)

VERSIONING:
  - Version: 1.0.0
  - Updated: YYYY-MM-DD
  - Deprecation: Link to newer version

UPDATE FREQUENCY:
  - Static (once per project)
  - Quarterly review (for active projects)
```

---

## BLOK J — KISITLAR (Örnek Cevaplar)

### J01.0 Kesinlikle yapılmaması gereken şeyler neler?

**Soru:** Kesinlikle yapılmaması gereken şeyler neler?

**Cevap Örneği:**
```
TEKNOLOJI:
  ❌ MD5/SHA-1 hashing (use Argon2id)
  ❌ unserialize(user_input) (RCE risk)
  ❌ SELECT * queries (performance + security)
  ❌ Raw SQL concatenation (SQL injection)
  ❌ eval() function (any context)
  ❌ Global variables (use DI)
  ❌ Monolithic frontend bundler (use Vanilla JS)

ARCHITECTURE:
  ❌ Single database (no failover)
  ❌ Synchronous payment processing (use queue)
  ❌ No rate limiting (DDoS vulnerable)
  ❌ No audit logging (GDPR violation)
  ❌ Hardcoded credentials (.env MUST use)

TIMELINE:
  ❌ No 6-month runway (too risky)
  ❌ No team senior engineer (mentoring needed)

COMPLIANCE:
  ❌ GDPR non-compliant (EU users = violation)
  ❌ No PCI DSS (if accepting payments = violation)

BUDGET:
  ❌ <$50K/year operations (unsustainable)
  ❌ Self-hosted only (no managed services)
```

---

*BLOK ÖRNEKLERI — Her A-J bloktan representative Q&A*
*Prompt Maker v8.1.0 — 2026-06-11*
