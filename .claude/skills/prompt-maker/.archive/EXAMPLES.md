# PROMPT-MAKER SKILL — KULLANIM ÖRNEKLERI

## ÖRNEK 1: Web/Backend API (Laravel)

### Kullanıcı Girdisi
```
"prompt maker: Laravel REST API için MASTER PROMPT yaz"
```

### Adım 1: İlk Sorular (Soru Turu 1)
```
[1/4] Proje ne zaman teslim edilecek?
      A) Hemen (< 1 hafta)
      B) Normal (1-4 hafta)
      C) Uzun vadeli (1-6 ay)
      D) Devam eden proje

[2/4] Takım büyüklüğü?
      A) Solo (1 kişi)
      B) Küçük (2-5)
      C) Orta (6-20)
      D) Büyük (20+)

[3/4] Güvenlik seviyesi?
      A) Genel (public bilgi)
      B) Standart (login + session)
      C) Yüksek (OWASP uyumlu)
      D) Kritik (fintech/healthtech)

[4/4] Ölçek nedir?
      A) Kişisel (100 user)
      B) Startup (1K-10K user)
      C) SME (10K-100K user)
      D) Enterprise (100K+ user)
```

**Kullanıcı Cevapları:**
- [1/4] → B (Normal)
- [2/4] → C (Orta)
- [3/4] → C (Yüksek)
- [4/4] → C (SME)

### Adım 2: Web Araştırması (paralel)
```
Araştırma başlıyor otomatik:
  - "Laravel REST API best practices 2026"
  - "OWASP Top 10:2025 API security"
  - "Laravel authentication JWT vs session"
  - "Database optimization MySQL 8.4"
  - "API rate limiting strategies"
  - ... (150+ kaynak hedefi)
```

### Adım 3: Soru Turu 2 (Filtered — Web/Backend)
```
[5/10] Hangi database?
       A) MySQL 8.4
       B) PostgreSQL 16
       C) SQLite
       D) MongoDB

[6/10] Cache sistemi?
       A) Redis
       B) Memcached
       C) APCu
       D) Yok

[7/10] Authentication tipi?
       A) JWT
       B) Session cookie
       C) API key
       D) OAuth2

[8/10] Testing framework?
       A) PHPUnit
       B) Pest
       C) Diğer
       D) Yok

[9/10] Deployment?
       A) Docker + K8s
       B) VPS (manual)
       C) Cloud (AWS/GCP/Azure)
       D) Shared hosting

[10/10] Rate limiting?
        A) Per user (60/min)
        B) Per IP (100/min)
        C) Custom
        D) Yok
```

### Adım 4: MASTER PROMPT Üretimi (20 bölüm)

Çıktı başlığı:
```
# MASTER PROMPT — Laravel REST API (SME Scale, OWASP-Compliant)
Generated: 2026-06-11 14:32:45 UTC
Version: 1.0.0
Quality Score: 782/800
Sections: 20
Parça: 3 (part01.md, part02.md, part03.md)
Characters: 285,943
Sources: 187 kaynaktan araştırılmıştır
```

### Adım 5: Parça Parça Kayıt

```
✅ .ai/wiki/prompts-kb/2026-06-11-laravel-rest-api-part01.md (95KB)
   └─ Bölüm 01-07: Identity, Experience, Context, Stack, Objective, Triggers, Pipeline

✅ .ai/wiki/prompts-kb/2026-06-11-laravel-rest-api-part02.md (98KB)
   └─ Bölüm 08-14: Rules, Language, Architecture, Security, Performance, UI/UX(?), Testing

✅ .ai/wiki/prompts-kb/2026-06-11-laravel-rest-api-part03.md (92KB)
   └─ Bölüm 15-20: DevOps, Input, Process, Output, Edge Cases, QC

✅ .ai/wiki/prompts-kb/2026-06-11-laravel-rest-api-INDEX.md (12KB)
   └─ Quick reference, bölüm özeti, quality score breakdown

✅ .ai/brain.md (append)
   └─ Laravel REST API prompt — v1.0.0 — OWASP-compliant, JWT auth, MySQL+Redis, PHPUnit

✅ .ai/log.md (append)
   └─ [2026-06-11 14:35] Prompt generated: Laravel API (3 parts, 285K chars, 782/800 score)
```

---

## ÖRNEK 2: Fintech (PCI DSS Compliant)

### Kullanıcı Girdisi
```
"prompt maker: PCI DSS Level 1 fintech sistemi için MASTER PROMPT"
```

### Adım 1: Sorular
```
[1/4] PCI DSS Level?
      A) Level 2 (6M-1B işlem/yıl)
      B) Level 1 (1B+ işlem/yıl)
      C) Custom
      D) Bilinmiyor

[2/4] Ödeme gateway'i?
      A) Stripe
      B) PayPal
      C) In-house
      D) Farklı

[3/4] Fraud detection?
      A) 3D Secure
      B) ML model
      C) Rule-based
      D) Diğer

[4/4] KYC/AML?
      A) Tam KYC
      B) Kısmi KYC
      C) Hiçbiri
      D) Henüz belirsiz
```

**Cevaplar:**
- Level 1
- Stripe + In-house payment service
- ML model
- Tam KYC

### Adım 2: Web Araştırması (Fintech — 500+ kaynak hedefi)
```
- PCI DSS v3.2.1 / 4.0 specification
- OWASP API Security Top 10:2023
- Encryption standards (AES-256-GCM)
- Key management (HSM, AWS KMS, Vault)
- Fraud detection research papers (2024-2026)
- ML model security (model poisoning, evasion)
- Settlement protocols (ACH, wire, crypto)
- Compliance reporting (FATCA, FBAR)
- Payment security (3DS, tokenization)
- API security best practices
... (+ 490 more sources)
```

### Adım 3: Soru Turu 2 (Fintech Filtered — 45-50 soru)
```
[5/20] Hangi diller?
       A) Python + Node.js
       B) Java + Kotlin
       C) C# + .NET Core
       D) Diğer

[6/20] Database encryption?
       A) AES-256-GCM
       B) Transparent disk encryption
       C) Column-level
       D) Yok

[7/20] Hangi token stratejisi?
       A) JWT + refresh token
       B) Opaque tokens
       C) Session-based
       D) API key

[8/20] Rate limiting?
       A) Per user (10 req/min)
       B) Per IP (100 req/min)
       C) Tiered (user tier-based)
       D) Diğer

... (42 more fintech-specific questions)

[50/50] Compliance audit?
        A) Annual third-party
        B) Quarterly internal
        C) Continuous scanning
        D) On-demand
```

### Adım 4: Quality Score (Fintech Focus)

```
Score Breakdown:

Completeness:        95/100 ✅ (tüm fintech bölümleri dolu)
Consistency:         93/100 ✅ (çelişki yok)
Production Ready:    98/100 ✅ (error handling, audit logging, monitoring)
Security:            99/100 ✅ (PCI DSS uyumlu, encryption mandatory)
Scalability:         94/100 ✅ (distributed transactions, failover)
Clarity:             92/100 ✅ (senior architect derinliği)
Depth:               96/100 ✅ (fintech örnekleri, edge cases)
Completeness_Docs:   97/100 ✅ (kod örnekleri, compliance checklists)

TOTAL:               797/800 ⭐⭐⭐⭐⭐ (EXCELLENT)
```

### Adım 5: Vault Yazma

```
✅ 2026-06-11-fintech-pci-dss-part01.md (140KB)
✅ 2026-06-11-fintech-pci-dss-part02.md (145KB)
✅ 2026-06-11-fintech-pci-dss-part03.md (128KB)
✅ 2026-06-11-fintech-pci-dss-part04.md (135KB)
✅ 2026-06-11-fintech-pci-dss-part05.md (152KB)
✅ 2026-06-11-fintech-pci-dss-part06.md (141KB)
✅ 2026-06-11-fintech-pci-dss-part07.md (135KB)
✅ 2026-06-11-fintech-pci-dss-part08.md (139KB)
✅ 2026-06-11-fintech-pci-dss-INDEX.md (18KB)

TOTAL: 1,173 KB (8 parça)

.ai/brain.md append:
  - Fintech PCI DSS Level 1 prompt
  - v1.0.0 — 2026-06-11
  - 797/800 quality score
  - Stripe + in-house, ML fraud, KYC, AES-256-GCM, HSM
  
.ai/log.md append:
  - [2026-06-11 16:47] Fintech PCI DSS prompt generated (8 parts, 1.1MB, 797/800)
```

---

## ÖRNEK 3: CoreMusic (SPA Router)

### Kullanıcı Girdisi
```
"prompt maker: CoreMusic SPA router için steering yaz"
```

### Adım 1: Sorular
```
[1/4] Bu CoreMusic mu?
      A) Evet, CoreMusic
      B) Hayır, diğer proje
      C) Benzer ama farklı
      D) Bilinmiyor

[2/4] Hangi katman(lar)?
      A) L3 (presentation)
      B) L2 (router)
      C) L1 (security)
      D) L0 (infra)
      E) Birden fazla

[3/4] Hangi dosya?
      A) Router.js
      B) GuardPipeline.js
      C) CacheLayer.js
      D) Diğer

[4/4] Hedef?
      A) Yeni feature
      B) Bug fix
      C) Refactor
      D) Audit/documentation
```

**Cevaplar:**
- Evet, CoreMusic
- L2 + L1
- Router.js
- Bug fix (race condition)

### Adım 2: Web Araştırması (CoreMusic context)
```
Otomatik olarak .ai/ vault'dan yüklenir:
  - brain.md (son kararlar)
  - spa-router-ai-guard.md
  - spa-router-lifecycle.md
  - hybrid-spa-architecture.md
  - decisions/021-spa-router-immutable-contract.md
  - ... (+ web sources)
```

### Adım 3: Soru Turu 2
```
[5/8] Race condition türü?
      A) Duplicate navigation
      B) Stale cache
      C) Lost fetch
      D) Multiple guard passes

[6/8] Hangi edge case?
      A) Network timeout
      B) Browser back/forward
      C) Rapid clicking
      D) CSP violation

[7/8] Logging eklensin mi?
      A) Evet (structured)
      B) Evet (minimal)
      C) Hayır (perf)
      D) Varolan kullan

[8/8] Test eklensin mi?
      A) Unit + integration
      B) Unit sadece
      C) E2E + browser
      D) Hiçbiri
```

### Adım 4: MASTER PROMPT Üretimi (CoreMusic-specific)

```
# STEERING — CoreMusic SPA Router — Race Condition Fix
Generated: 2026-06-11 15:22:10 UTC
Version: 1.0.0
Type: STEERING (Kiro .kiro/steering/)
Quality Score: 765/800
Sections: 20
Format: Markdown + JSON
Characters: 142,856
```

### Adım 5: Vault Yazma

```
✅ .kiro/steering/2026-06-11-spa-router-race-condition.md (143KB)
   └─ Steering file, doğrudan Kiro tarafından yüklenecek

✅ .ai/brain.md append:
   - CoreMusic SPA Router — race condition fix
   - AbortController + activeRequests Map
   - GuardPipeline refactoring
   - Test plan: 12 unit test + 3 integration

✅ .ai/log.md append:
   - [2026-06-11 15:25] Steering generated: SPA Router (1 part, 143K, 765/800)
```

---

## ÖRNEK 4: Security/Audit (OWASP Comprehensive)

### Kullanıcı Girdisi
```
"prompt maker: OWASP Top 10:2025 audit prompts sistemi"
```

### Adım 1: Sorular
```
[1/4] Audit scope?
      A) Full application
      B) API endpoint'leri
      C) Frontend
      D) Infrastructure

[2/4] Application type?
      A) Web SPA
      B) REST API
      C) Monolith
      D) Microservice

[3/4] Risk level?
      A) Public application
      B) Internal enterprise
      C) Payment/fintech
      D) Healthcare/HIPAA

[4/4] Output tipi?
      A) Audit checklist
      B) Security steering
      C) Vulnerability fix guide
      D) Compliance roadmap
```

**Cevaplar:**
- Full application
- REST API
- Payment/fintech
- Audit checklist + Security steering

### Adım 2: Web Araştırması (Security-intensive)
```
- OWASP Top 10:2025 API specification
- OWASP Web Security Testing Guide v4.2
- OWASP Cheat Sheet Series (20+ sheets)
- CWE/CVSS scoring system
- STRIDE threat modeling
- Defense in depth strategies
- Secure coding guidelines (per language)
... (500+ sources)
```

### Adım 3: Soru Turu 2 (Security Full)
```
[5/35] Authentication:
       A) JWT
       B) OAuth2
       C) Session-based
       D) mTLS
       E) API key

[6/35] Encryption at rest?
       A) AES-256-GCM
       B) AES-128
       C) ChaCha20-Poly1305
       D) None

... (30 more security-specific questions)

[35/35] Incident response?
        A) Plan + team
        B) Plan only
        C) Ad-hoc
        D) None
```

### Adım 4: MASTER PROMPT Üretimi

```
# MASTER PROMPT — OWASP Top 10:2025 API Audit & Security Framework
Generated: 2026-06-11 16:45:00 UTC
Type: SECURITY (Multi-faceted)
Quality Score: 801/800 ⭐ (EXCEEDED TARGET!)
Sections: 20+
Parça: 12 (comprehensive coverage)
Characters: 502,841 (maximum mode)
```

### Adım 5: Vault Yazma

```
✅ 2026-06-11-owasp-api-audit-part01.md (95KB)   — Identity, Experience, Context
✅ 2026-06-11-owasp-api-audit-part02.md (98KB)   — Stack, Objective, Triggers
✅ 2026-06-11-owasp-api-audit-part03.md (92KB)   — Pipeline, Rules (HARD)
✅ 2026-06-11-owasp-api-audit-part04.md (89KB)   — Rules (SOFT), Language
✅ 2026-06-11-owasp-api-audit-part05.md (96KB)   — Architecture
✅ 2026-06-11-owasp-api-audit-part06.md (101KB)  — Security Deep Dive
✅ 2026-06-11-owasp-api-audit-part07.md (94KB)   — OWASP Top 10 specifics
✅ 2026-06-11-owasp-api-audit-part08.md (87KB)   — API-specific threats
✅ 2026-06-11-owasp-api-audit-part09.md (91KB)   — Performance, DevOps
✅ 2026-06-11-owasp-api-audit-part10.md (99KB)   — Testing, Compliance
✅ 2026-06-11-owasp-api-audit-part11.md (95KB)   — Input/Output, Process
✅ 2026-06-11-owasp-api-audit-part12.md (102KB)  — Edge Cases, QC, Checklists
✅ 2026-06-11-owasp-api-audit-INDEX.md (25KB)    — Quick reference

TOTAL: 1,219 KB (12 parça + index)

.ai/brain.md append:
  - OWASP Top 10:2025 API Audit Framework
  - v1.0.0 — 2026-06-11
  - 801/800 quality score (EXCEEDED!)
  - Full API + infrastructure audit coverage
  - 187 sources from official OWASP, CVE databases, security research
  
.ai/log.md append:
  - [2026-06-11 17:02] Security audit prompt generated (12 parts, 1.2MB, 801/800 — EXCELLENT)
```

---

## Standart Output Format (Tüm Örnekler)

Her çıktıda şunlar zorunlu:

```markdown
# [TITLE]

**Generated:** YYYY-MM-DD HH:mm:ss UTC
**Version:** X.Y.Z
**Type:** SYSTEM_PROMPT | STEERING | HOOK | SKILL | RULES | MASTER_PROMPT | ...
**Quality Score:** NNN/800 (+ performans deği)
**Sections:** NN
**Parts:** N (+index)
**Characters:** NN,NNN
**Sources Researched:** NNN

---

## Metadata (JSON)

\`\`\`json
{
  "title": "...",
  "version": "1.0.0",
  "generated": "2026-06-11T17:02:45Z",
  "type": "MASTER_PROMPT",
  "scope": "system | module | component | cross-project",
  "target_audience": "ai | developer | team | organization",
  "quality_score": {
    "total": 801,
    "max": 800,
    "completeness": 100,
    "consistency": 98,
    "production_ready": 100,
    "security": 99,
    "scalability": 100,
    "clarity": 96,
    "depth": 99,
    "documentation": 98
  },
  "sources_count": 187,
  "research_categories": [
    "Official Documentation",
    "Security Guides",
    "Browser Runtime",
    "Framework Docs",
    "Research Papers"
  ],
  "parts": 12,
  "total_characters": 502841,
  "vault_path": ".ai/wiki/prompts-kb/",
  "update_frequency": "one-time | quarterly | monthly | per-change",
  "maintainer": "AI Prompt-Maker Engine",
  "tags": ["api", "security", "owasp", "audit", "fintech"]
}
\`\`\`

---

[Content...]
```

---

## Süre & Hedef

| Kompleksite | Parça | Karakter | Zaman | Quality Score |
|-----------|-------|----------|------|----------------|
| Basit | 1-2 | 50-150K | 30-45 min | 700-750/800 |
| Orta | 3-5 | 150-300K | 1-2 saat | 750-800/800 |
| Kompleks | 6-10 | 300-500K | 2-4 saat | 800/800+ |
| Maksimum | 10+ | 500K-1MB | 4-6 saat | 800/800+ |

---

*Final Examples — Production Ready — 2026-06-11*
