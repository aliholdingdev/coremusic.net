# SORU BLOKLARI — ULTRA DETAYLI GENIŞLETME
# Prompt Maker v8.1.0 — 2026-06-11
# Her soru: 10-15 alt soru (katmanlı, hierarchical)

---

## 📊 EXPANSION HEDEFI

**ESKI:** 1,510+ soru (3-8 alt soru/main)
**YENİ:** 3,000-4,000+ soru (10-15 alt soru/main + meta-questions)

**Yapı:**
```
A01 [MAIN SORU]
├─ A01.0.0 [Temel anlama]
├─ A01.0.1 [Belirteci tanımlama]
├─ A01.1 [Bağlam A — X durumu için]
├─ A01.1.1 [Sub-context 1]
├─ A01.1.2 [Sub-context 2]
├─ A01.2 [Bağlam B — Y durumu için]
├─ A01.2.1 [Sub-context 3]
├─ A01.3 [Bağlam C — Z durumu için]
├─ A01.4 [Validation/verification]
├─ A01.5 [Edge case 1]
├─ A01.6 [Edge case 2]
├─ A01.7 [Decision tree]
├─ A01.8 [Follow-up logic]
├─ A01.9 [Risk assessment]
└─ A01.10 [Recommendation framework]
```

---

## 🎯 SORU KATMANI MODELI

Her main soru 4-5 katmanda sorulur:

### KAT 1: Temel Anlama (1-2 soru)
```
A01.0.0 → "Projenin tam resmi adı nedir?"
A01.0.1 → "Bu adın anlamı nedir?"
```

### KAT 2: Bağlamsal Varyasyonlar (3-5 soru)
```
A01.1 → Başarılı proje context'inde
A01.2 → Başarısız proje context'inde
A01.3 → Startup context'inde
A01.4 → Enterprise context'inde
```

### KAT 3: Alt-detaylar (2-3 soru)
```
A01.1.1 → "Bu ad legal mi?"
A01.1.2 → "Trademark var mı?"
A01.1.3 → "Alternatif isimler neler?"
```

### KAT 4: Karar & Tercih (1-2 soru)
```
A01.7 → "Bu adla devam mı, değişim mi?"
A01.8 → "Hangi ad tercih ediliyor ve neden?"
```

### KAT 5: Risk & Validasyon (1-2 soru)
```
A01.9 → "Brandlama riskini değerlendir"
A01.10 → "Karar framework'ü nedir?"
```

---

## 📈 BLOK BAŞI SORU SAYILARI

| Blok | Eski | Yeni | Artış | Detay |
|------|------|------|-------|-------|
| A | 200+ | 350+ | +150 | 35 main × 10 sub = 350 |
| B | 250+ | 450+ | +200 | 45 main × 10 sub = 450 |
| C | 120+ | 250+ | +130 | 25 main × 10 sub = 250 |
| D | 180+ | 400+ | +220 | 40 main × 10 sub = 400 |
| E | 120+ | 280+ | +160 | 28 main × 10 sub = 280 |
| F | 150+ | 320+ | +170 | 32 main × 10 sub = 320 |
| G | 180+ | 380+ | +200 | 38 main × 10 sub = 380 |
| H | 300+ | 600+ | +300 | 60 main × 10 sub = 600 |
| I | 80+ | 180+ | +100 | 18 main × 10 sub = 180 |
| J | 150+ | 320+ | +170 | 32 main × 10 sub = 320 |
| **TOPLAM** | **1,510+** | **3,530+** | **+2,020** | **+134% growth** |

---

## 🏗️ KATMANLI OUTPUT REPORT MODELI

Skill çalıştıktan sonra çıktı: **8-parça report**

```
.ai/wiki/prompts-kb/
├── {YYYY-MM-DD}-{slug}-part00-MAPPING.md          [Soru mapping]
├── {YYYY-MM-DD}-{slug}-part01-ANSWERS.md           [Tüm cevaplar]
├── {YYYY-MM-DD}-{slug}-part02-WEB-RESEARCH.md      [200+ kaynak]
├── {YYYY-MM-DD}-{slug}-part03-ARCHITECTURE.md      [Mimari analiz]
├── {YYYY-MM-DD}-{slug}-part04-SECURITY-AUDIT.md    [Security deep-dive]
├── {YYYY-MM-DD}-{slug}-part05-TECHNOLOGY-MATRIX.md [Tech stack matrix]
├── {YYYY-MM-DD}-{slug}-part06-DECISION-TREE.md     [Decision trees]
├── {YYYY-MM-DD}-{slug}-part07-MASTER-PROMPT-P1.md  [MASTER PROMPT part 1]
├── {YYYY-MM-DD}-{slug}-part08-MASTER-PROMPT-P2.md  [MASTER PROMPT part 2]
└── {YYYY-MM-DD}-{slug}-index.md                     [Index + metadata]
```

### Part 0: MAPPING (Soru → Cevap Haritası)
```markdown
# Soru-Cevap Haritası

| Blok | Soru ID | Kategori | Cevap | Status |
|------|---------|----------|-------|--------|
| A | A01.0.0 | Proje adı | "CoreMusic" | ✅ |
| A | A01.0.1 | Anlam | "Music streaming platform" | ✅ |
| A | A01.1 | Başarı konteksti | "SaaS, 10K users" | ✅ |
| ... | ... | ... | ... | ... |
```

### Part 1: ANSWERS (Tüm Cevaplar)
```markdown
# Soru-Cevap Dökümanı

## BLOK A — PROJESİ KİMLİĞİ

### A01 — Proje Adı & Açıklama

**A01.0.0** Projenin tam resmi adı nedir?
→ **Cevap:** CoreMusic

**A01.0.1** Bu adın anlamı nedir?
→ **Cevap:** Music platform operating on core technology

**A01.1** Başarılı proje konteksti
→ **Cevap:** 10K users, 99.9% uptime

...
```

### Part 2: WEB-RESEARCH (200+ Kaynak)
```markdown
# Web Araştırması Kaynakları

**Total Kaynak:** 247
**Kategoriler:** 8 (Docs, Security, API, Framework, Performance, UI/UX, DevOps, Domain)

## Resmi Dokümantasyon (45 kaynak)
- [PHP Official Docs](php.net) — Latest security advisories
- [MDN Web Docs](mdn.org) — Web APIs, standards
- [Laravel Documentation](laravel.com) — Framework best practices
...

## Güvenlik (38 kaynak)
- [OWASP Top 10:2025](owasp.org)
- [CWE-352: Cross-Site Request Forgery](cwe.mitre.org)
...

## API & Design (42 kaynak)
...

## Performance (35 kaynak)
...

## DevOps (28 kaynak)
...

## UI/UX & Accessibility (22 kaynak)
...

## Framework-Spesifik (18 kaynak)
...

## Domain-Spesifik (19 kaynak)
...
```

### Part 3: ARCHITECTURE (Mimari Analiz)
```markdown
# Mimari Analiz & Teknoloji Stack Matrix

## Önerilen Mimari

| Boyut | Seçim | Gerekçe | Trade-off |
|-------|-------|---------|-----------|
| **Deployment** | Microservice (Kubernetes) | 10K users, scaling need | Complexity +30% |
| **Database** | MySQL + Redis | ACID + cache speed | Multi-DB overhead |
| **Frontend** | Vanilla JS SPA | No framework bloat | Vanilla JS limits |
| **API** | REST + GraphQL hybrid | Flexibility + performance | Maintain 2 API |
| **Auth** | JWT + session hybrid | Security + performance | Token overhead |
| ... | ... | ... | ... |
```

### Part 4: SECURITY-AUDIT (Güvenlik Deep-Dive)
```markdown
# Güvenlik Analizi & OWASP Uyumu

## OWASP Top 10:2025 Checklist

| # | Vuln | Status | Mitigation | Priority |
|----|------|--------|-----------|----------|
| A01 | Broken Access Control | ⚠️ PARTIAL | Implement RBAC + ABAC | HIGH |
| A02 | Cryptographic Failures | ✅ FULL | AES-256-GCM + TLS 1.3 | — |
| A03 | Injection (SQL/Command) | ✅ FULL | PDO prepared statements | — |
| A04 | Insecure Design | ⚠️ PARTIAL | Threat model needed | HIGH |
| A05 | Security Misconfiguration | ⚠️ PARTIAL | Security headers review | MEDIUM |
| ... | ... | ... | ... | ... |

## Threat Model (STRIDE)

**Spoofing:**
- Risk: JWT token hijacking
- Mitigation: Secure cookie storage (HttpOnly, Secure, SameSite)

**Tampering:**
- Risk: XSS payload injection
- Mitigation: Trusted Types + CSP nonce

...
```

### Part 5: TECHNOLOGY-MATRIX (Tech Stack Matrix)
```markdown
# Teknoloji Stack Matrix

## Programlama Dilleri

| Dil | Seçim | Versiyon | Justification | Constraints |
|-----|-------|----------|---------------|-------------|
| PHP | ✅ Backend | 8.4 | strict_types, native | Type safety |
| JavaScript | ✅ Frontend | ES2022 | Vanilla, no bundler | No framework |
| TypeScript | ⚠️ Optional | 5.2 | Type safety for complex | Build step |
| SQL | ✅ Core | MySQL 8.4 | ACID, scaling | Query complexity |
| Bash | ✅ DevOps | 4.0+ | IaC, CI/CD scripts | POSIX compliance |

## Framework & Libraries

| Kategori | Seçim | Versiyon | Why | Trade-off |
|----------|-------|----------|-----|-----------|
| Backend Framework | Laravel | 11 LTS | MVC, rich ecosystem | Opinion heavy |
| Frontend Router | Custom SPA | — | Control, lightweight | Manual routing |
| Database ORM | Eloquent | 11 | Elegant syntax | Laravel-locked |
| Cache | Redis | 7.0+ | High performance | Dependency |
| Testing | PHPUnit + Vitest | 10 / 1.0 | Industry standard | Setup overhead |

...
```

### Part 6: DECISION-TREE (Karar Ağaçları)
```markdown
# Karar Ağaçları & Seçim Mantığı

## Decision Tree 1: Mimari Seçimi

```
Project Scale = 10K users?
├─ YES → Scaling critical
│   ├─ Monolith with horizontal scaling?
│   │   └─ Risk: Single DB bottleneck → Microservice better
│   └─ Microservice architecture?
│       ├─ Complexity HIGH (+30%)
│       ├─ Team size >= 6? YES → Feasible
│       └─ ✅ RECOMMEND: Microservice + K8s
└─ NO → Monolith acceptable
    ├─ Cost lower
    ├─ Faster iteration
    └─ ✅ RECOMMEND: Modular monolith
```

## Decision Tree 2: Database Seçimi

```
Data consistency critical?
├─ YES (financial, auth)
│   ├─ MySQL/PostgreSQL (ACID)
│   ├─ Replication: Master-slave or multi-master?
│   └─ ✅ RECOMMEND: MySQL 8.4 master-slave
└─ NO (events, logs)
    ├─ MongoDB (flexible schema)
    ├─ Cassandra (high write throughput)
    └─ ✅ CHOOSE: Based on write pattern
```

...
```

### Part 7-8: MASTER-PROMPT (20 bölüm, split into 2 parts)
```markdown
# MASTER PROMPT — SYSTEM IDENTITY & RULES

## 1. SYSTEM IDENTITY & ROLE

Sen: **Senior Principal Engineer — 40+ yıllık eşdeğer expertise**

...

## 2. EXPERIENCE LEVEL

Prerequisites:
- 6-10+ yıl freelance → enterprise
- PHP, SQL, distributed systems
...

## 3. PROJECT CONTEXT

**CoreMusic — Music Streaming Platform**

Scale: 10K-100K users
Timeline: 2-4 weeks
...

[Devamı: 18 more sections...]
```

---

## 🔧 /REFERENCES/ FİX EDECEK DOSYALAR

Şu dosyalar **düzeltilmesi gerekiyor** (dangling references):

```bash
01-prompt-types-deep.md
  ├─ Eksik: I-block soruları (I01-I30)
  └─ FIX: questions-block-I-prompt-type.md bağlantısı ekle

04-language-standards-full.md
  ├─ Eksik: PHP/JS/Python/C#/C++ detaylı standartlar
  └─ FIX: questions-block-B-tech-stack.md (B01-B02) ile sync

06-deep-domain-rules.md
  ├─ Eksik: H-block domain spesifik kurallar (H1-H5)
  └─ FIX: questions-block-h.md (H101-H520) ile full sync

07-architecture-patterns.md
  ├─ Eksik: C-block mimari soruları (C01-C30)
  └─ FIX: questions-block-c.md ile detaylı bağlantı

13-uiux-accessibility.md
  ├─ Eksik: F-block UI/UX soruları (F01-F30)
  └─ FIX: questions-block-f.md ile WCAG 2.2 checklist

14-embedded-audio-electronics.md
  ├─ Eksik: H1-H2 soruları (H101-H220)
  └─ FIX: questions-block-h.md (H1/H2 sections) ile sync

... (16 dosya daha)
```

---

## 📋 IMPLEMENTATION PLAN

### PHASE 1: Question Expansion (1 day)
```
1. Edit questions-block-a.md → 200+ to 350+ (A01-A35 × 10 sub-q each)
2. Edit questions-block-B-tech-stack.md → 250+ to 450+ (B01-B45)
3. Edit questions-block-c.md → 120+ to 250+ (C01-C25)
4. Edit questions-block-d.md → 180+ to 400+ (D01-D40)
5. Edit questions-block-e.md → 120+ to 280+ (E01-E28)
6. Edit questions-block-f.md → 150+ to 320+ (F01-F32)
7. Edit questions-block-g.md → 180+ to 380+ (G01-G38)
8. Edit questions-block-h.md → 300+ to 600+ (H1-H5 × 120 each)
9. Edit questions-block-I-prompt-type.md → 80+ to 180+ (I01-I18)
10. Edit questions-block-j.md → 150+ to 320+ (J01-J32)
```

### PHASE 2: Reference File Fixes (2 days)
```
1. Fix 01-prompt-types-deep.md (sync I-block)
2. Fix 04-language-standards-full.md (sync B-block)
3. Fix 06-deep-domain-rules.md (sync H-block)
4. Fix 07-architecture-patterns.md (sync C-block)
5. Fix 13-uiux-accessibility.md (sync F-block)
6. Fix 14-embedded-audio-electronics.md (sync H1-H2)
7. Fix 15-api-design-patterns.md (sync C-block API)
8. Fix 16-database-design-patterns.md (sync B+C)
9. Fix 17-prompt-engineering-deep.md (sync I-block)
10. Fix 18-security-deep-dive.md (sync D-block)
11-16. Fix remaining 6 files
```

### PHASE 3: Report Generation (1 day)
```
1. Create MAPPING document (Q→A matrix)
2. Create ANSWERS document (all answers)
3. Create WEB-RESEARCH document (200+ sources)
4. Create ARCHITECTURE document (tech matrix)
5. Create SECURITY-AUDIT document (OWASP mapping)
6. Create TECHNOLOGY-MATRIX document (stack analysis)
7. Create DECISION-TREE document (choice framework)
8. Create MASTER-PROMPT-P1 + P2 (20 sections)
9. Create INDEX document (metadata + metadata)
```

### PHASE 4: Wiki Sync (1 day)
```
1. Append .ai/log.md with full changelog
2. Append .ai/brain.md with decision summary
3. Create .ai/wiki/prompts-kb/{date}-{slug}-*.md (8 files)
4. Update INDEX.md
5. Update CHANGELOG.md to v8.1.0
```

**Total: 5 days (or 2-3 days parallelized)**

---

## ✅ READY TO EXECUTE

Başla mı?

```
/execute ultra-detailed-expansion
  → PHASE 1: Expand all Q blocks (A-J)
  → PHASE 2: Fix all /references/ files (16 files)
  → PHASE 3: Generate 8-part report
  → PHASE 4: Sync brain.md + log.md
```
