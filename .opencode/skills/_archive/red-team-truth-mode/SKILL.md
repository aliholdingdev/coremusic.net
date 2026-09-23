---
title: "CoreMusic — Red Team & Truth Mode"
type: skill-instruction
version: 7.0.0
authority: SSOT
mode:
  - Red Team
  - Truth Mode
  - Human Mode
purpose:
  - Adversarial Review of All Agent Outputs
  - System-Level Red Teaming (Not Model-Level)
  - Confidence Calibration & Evidence Grounding
  - Truth Enforcement (Zero Fabrication)
  - Attack Path Documentation
  - Break-Fix Cycles for Robustness
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/AGENTS.md"
    - ".ai/WORKFLOW.md"
    - ".ai/brain.md"
    - ".ai/index.md"
    - ".ai/keys.md"
    - ".ai/log.md"
  architecture:
    - ".ai/ADR/"
    - "Existing project architecture"
  templates:
    - ".ai/.templates/index.md"
  agents:
    - ".ai/.agents/AGENTS.md"
  skills:
    - ".opencode/skills/hallucination-control/SKILL.md"
  project_structure:
    - "coremusic.net/"
    - "shared/"
  update_policy:
    preserve_existing_structure: true
    require_approval_for:
      - "red team methodology change"
      - "truth mode policy change"
      - "H001-H039 pattern addition"
      - "confidence threshold change"
triggers:
  - "red team"
  - "truth mode"
  - "adversarial review"
  - "hallucination"
  - "confidence check"
  - "source citation"
  - "verification"
  - "attack path"
  - "break-fix"
  - "her mimari karar"
  - "her API ve kütüphane kullanımı"
  - "her kod üretimi"
  - "her vault güncellemesi"
changelog:
  - version: 7.0.0
    date: 2026-08-15
    changes:
      - Complete rewrite from research (Google, Microsoft AIRT, OWASP, NIST 2025-2026)
      - Separated from hallucination-control as standalone adversarial skill
      - Added system-level red teaming (Microsoft lesson: test system not model)
      - Added threat model ontology (Microsoft AIRT framework)
      - Added 5 attack classes (OWASP GenAI + NIST competition data)
      - Added confidence calibration (HTC/SAVeR academic research)
      - Added break-fix cycle methodology (Microsoft Phi-3)
      - Added attack path documentation (not just success rate)
      - Added rules of engagement protocol (Google Red Team)
      - Restructured H001-H039 as compact lookup tables
      - Added claim-level specificity control (CSS research 2026)
---

# RED TEAM & TRUTH MODE — Adversarial Review System

**THIS SYSTEM IS NOT A PREFERENCE — IT IS AN ABSOLUTE MANDATE.**

This document is the STANDALONE adversarial review and truth enforcement system for ALL CoreMusic AI agents.
Every agent output, every technical claim, every architectural decision — adversarially reviewed before delivery.

**Research basis:** Google AI Red Team (2026), Microsoft AIRT (100+ products retrospective), OWASP GenAI Red Teaming Initiative, NIST/Gray Swan Agent Competition, ACL 2026 (SAVeR, CSS), UAI 2026 (multi-agent fact verification).

---

## 0. IDENTITY & SCOPE

| Field | Value |
|-------|-------|
| System Name | Red Team & Truth Mode |
| Version | 7.0.0 |
| Authority | SSOT (Single Source of Truth) |
| Scope | All 11 CoreMusic Agents |
| Enforcement | AUTOMATIC — triggers on every output |
| Override | FORBIDDEN — only Vault Steward can modify |

### 0.1 What This Skill Does (And Doesn't)

| This Skill IS | This Skill IS NOT |
|---------------|-------------------|
| Adversarial review of agent outputs | Content safety filter for LLMs |
| System-level attack simulation | Model-level gradient attack |
| Confidence calibration for technical claims | General fact-checking |
| Attack path documentation | Penetration testing tool |
| Break-fix cycle methodology | Automated exploit framework |

### 0.2 Agent Coverage

| Agent | Domain | Red Team Focus |
|-------|--------|----------------|
| Backend Architect | PHP/API/DB | API abuse, injection, middleware bypass |
| UI Designer | JS/CSS/UX | XSS, DOM manipulation, accessibility |
| Security Engineer | OWASP/Auth | Full OWASP Top 10 2025, crypto validation |
| Data Engineer | MySQL/BCNF | SQL injection, schema integrity, data leakage |
| Embedded Engineer | C++/Audio | Memory safety, real-time constraints, buffer overflow |
| QA Engineer | Testing | Coverage gaps, flaky tests, false positives |
| DevOps Engineer | CI/CD | Pipeline injection, secret exposure, supply chain |
| Audio HW Engineer | DAC/ADC | Spec fabrication, voltage/thermal limits |
| DSP Firmware | XMOS/I2S | Protocol violations, timing, register config |
| Windows SW | WASAPI/COM | COM misuse, driver safety, privilege escalation |
| Master Orchestrator | Coordination | All — cross-agent attack path analysis |

---

## 1. CORE MANDATES

### 1.1 Five Absolute Rules

1. **EVERY AGENT OUTPUT IS AN ATTACK SURFACE.** No output passes without adversarial review.
2. **TEST THE SYSTEM, NOT THE MODEL.** (Microsoft AIRT Lesson 1) — Agent outputs exist in a system with middleware, DB, APIs, hardware. Review the full stack.
3. **CAPTURE THE ATTACK PATH, NOT JUST THE SUCCESS RATE.** (Microsoft AIRT Lesson 3) — Document how and why an attack works, not just whether it passes.
4. **SIMPLE ATTACKS WORK BEST.** (Microsoft AIRT Lesson 2) — Gradient-based methods are impractical. Prompt injection, hardcoded secrets, wrong specs — these are what real adversaries use.
5. **ABSTAIN IS A VALID RESPONSE.** When confidence < 90, do NOT guess. Write `// ⚠️ VERIFICATION REQUIRED` and cite missing evidence.

### 1.2 Prohibited Behaviors

| Behavior | Trigger | Consequence |
|----------|---------|-------------|
| "Sanırım" / "Muhtemelen" / "Bildiğim kadarıyla" | Language detection | Immediate rejection |
| Fabricating API endpoints | H030-H039 | Score = 0, REJECTED |
| Inventing class names | H031 | Score = 0, REJECTED |
| Guessing hardware specs | H001-H009 | Score = 0, REJECTED |
| Assuming database schema | H020-H029 | Score = 0, REJECTED |
| "Framework FORBIDDEN" violation | ADR-001 | Architectural rejection |
| "ORM FORBIDDEN" violation | ADR-002 | Architectural rejection |
| Reviewing only the model output | System-level check | Incomplete review |
| Reporting only pass/fail | Attack path required | Missing documentation |

---

## 2. THREAT MODEL ONTOLOGY

Based on Microsoft AIRT's ontology for 100+ generative AI products.

### 2.1 Actor Classification

| Actor Type | Intent | CoreMusic Relevance |
|------------|--------|---------------------|
| Adversarial User | Deliberate exploitation | Jailbreak, injection, privilege escalation |
| Benign User (Failure) | Unintentional harm trigger | Edge cases, malformed input, race conditions |
| Malicious Insider | Credential abuse | Admin panel abuse, API key exposure |
| Automated Bot | Mass exploitation | Rate limiting bypass, credential stuffing |

### 2.2 Attack Surface Map (System-Level)

```
+------------------------------------------------------------------+
|                    COREMUSIC ATTACK SURFACE                        |
+------------------------------------------------------------------+
|                                                                    |
|  L3: UI Layer                                                      |
|  +-- XSS (DOM-based, Stored, Reflected)                          |
|  +-- CSRF bypass                                                  |
|  +-- Accessibility exploitation (screen reader manipulation)     |
|  +-- Client-side state manipulation                               |
|                                                                    |
|  L2: API / Middleware Layer                                        |
|  +-- Injection (SQL, NoSQL, Command, LDAP)                       |
|  +-- Authentication bypass (BypassAuth misconfiguration)         |
|  +-- Session hijacking (cookie flags, session fixation)          |
|  +-- Rate limiting bypass                                         |
|  +-- Middleware order violation                                    |
|  +-- Endpoint enumeration                                         |
|                                                                    |
|  L1: Security Layer                                                |
|  +-- JWT/Token weaknesses                                         |
|  +-- CSP/SecurityHeaders bypass                                   |
|  +-- Credential exposure (.env, hardcoded)                       |
|  +-- CORS misconfiguration                                        |
|                                                                    |
|  L0: Data / Hardware Layer                                         |
|  +-- SQL injection (PDO bypass)                                   |
|  +-- Schema fabrication                                           |
|  +-- Hardware spec hallucination                                  |
|  +-- Memory safety (C++ audio callbacks)                         |
|  +-- Timing violations (real-time audio)                          |
|  +-- I2S/TDM protocol errors                                      |
+------------------------------------------------------------------+
```

### 2.3 Impact Categories

| Category | Severity | Examples |
|----------|----------|---------|
| Security Impact | CRITICAL | Auth bypass, data exfiltration, RCE |
| Safety Impact | HIGH | Wrong hardware specs (thermal/fire risk), data corruption |
| Reliability Impact | MEDIUM | Race conditions, memory leaks, timing violations |
| Responsible AI (RAI) | MEDIUM | Bias, harmful content, privacy violations |

---

## 3. FIVE ATTACK CLASSES

Adapted from OWASP GenAI Red Teaming Guide + NIST/Gray Swan competition data (250,000+ attack attempts, 400+ participants, 13 frontier models).

### 3.1 Attack Class 1: Injection & Manipulation

| Attack | CoreMusic Vector | Defense |
|--------|------------------|---------|
| Prompt injection | N/A (no LLM user-facing) | Input sanitization, parameterized queries |
| SQL injection | PDO prepared statements | H020-H029 enforcement |
| XSS (stored/reflected) | UI layer JavaScript | Output encoding, CSP |
| CSRF token bypass | Middleware order | H014, H033 enforcement |
| Middleware order manipulation | SpaRoute pipeline | Immutable middleware order |

### 3.2 Attack Class 2: Authentication & Authorization

| Attack | CoreMusic Vector | Defense |
|--------|------------------|---------|
| Credential hardcoding | .env exposure | H012 enforcement |
| Session hijacking | Cookie flags | H037 (HttpOnly, Secure, SameSite) |
| BypassAuth in production | APP_ENV check | H039 enforcement |
| Privilege escalation | Admin panel | Role-based access control |
| JWT secret exposure | Hardcoded secrets | H010 (credential_vault AES-256-GCM) |

### 3.3 Attack Class 3: Data Integrity

| Attack | CoreMusic Vector | Defense |
|--------|------------------|---------|
| Schema fabrication | Wrong DB names | H025-H028 enforcement |
| Cross-database FK | 9-DB BCNF isolation | H023 (ADR-040) |
| SELECT * data leakage | Explicit columns | H021 enforcement |
| Soft delete bypass | deleted_at mandatory | H024 enforcement |
| SQL string concatenation | PDO prepared only | H029 enforcement |

### 3.4 Attack Class 4: Hardware & Spec Fabrication

| Attack | CoreMusic Vector | Defense |
|--------|------------------|---------|
| Wrong DAC/ADC specs | PCM5122 claim | H001 (PCM3168A or AK4458 only) |
| GPIO voltage mismatch | 5V → 3.3V | H002 (level shifter required) |
| Missing ASIO SDK | XMOS integration | H003 (ASIO SDK mandatory) |
| Wrong amplifier specs | SNR/THD claims | H004 (both specs required) |
| Latency claims without ASIO | 0.5ms at 48kHz | H007 (ASIO Exclusive mandatory) |

### 3.5 Attack Class 5: Speculative & Fabricated Claims

| Attack | CoreMusic Vector | Defense |
|--------|------------------|---------|
| Non-existent endpoints | '/api/v2/auth/login' | H030 (real route: '/login') |
| Non-existent classes | FileUploadHandler | H031 (check system.database) |
| Non-existent directories | storage/, uploads/ | H032 (check root structure) |
| MFA/TOTP feature claim | Does not exist | H019 (ADR-011) |
| Wrong session name | 'COREMUSIC_SID' | H036 ('COREMUSIC_SESS') |

---

## 4. CONFIDENCE CALIBRATION SYSTEM

Based on HTC (Holistic Trajectory Calibration) and SAVeR (Self-Audited Verified Reasoning) frameworks.

### 4.1 Three-Tier Classification

| Range | Status | Action |
|-------|--------|--------|
| 90-100 | **VERIFIED** | Code directly, implement immediately |
| 60-89 | **UNVERIFIED** | Usable but risky — requires user confirmation |
| <60 | **REJECTED** | FORBIDDEN — provide correct alternative |

### 4.2 Weighted Scoring Rubric

| Category | Weight | Sources |
|----------|--------|---------|
| Official Documentation | 40 pts | php.net, MDN, dev.mysql.com, owasp.org, vendor datasheets (TI, AKM) |
| Standards (RFC/ISO/OWASP) | 25 pts | RFC 9106, ISO 27001, OWASP Top 10 2025, PSR-12 |
| Local Vault Evidence | 15 pts | .ai/decisions/, .ai/brain.md, CLAUDE.md |
| Community (High Score) | 10 pts | StackOverflow accepted, GitHub issues with maintainer response |
| Unknown/Old Source | -50 pts | Blog, Medium, Wikipedia, pre-2024 sources |

### 4.3 Recency Filter

| Period | Penalty | Requirement |
|--------|---------|-------------|
| 2024+ | 0 | Full score |
| 2022-2023 | -10 | User confirmation required |
| <2022 | -15 | Mandatory user approval + 2x sources |

### 4.4 Cross-Validation Rules

| Sources | Action |
|---------|--------|
| 3+ independent sources | VERIFIED permitted |
| 2 mixed sources | User confirmation required |
| 1 single source | Additional validation mandatory |
| Contradictory evidence | BLOCKED — report to user |

### 4.5 Atomic Claim Decomposition

Every technical output is decomposed into atomic claims. Each claim receives its own confidence score.

| Claim Type | Verification Method |
|------------|---------------------|
| Factual assertion | Source citation required |
| API/endpoint existence | Vault check + official docs |
| Hardware specification | Datasheet PDF required |
| Security requirement | OWASP/NIST reference |
| Performance claim | Benchmark or calculation |

**Final output score = minimum(atomic claim scores).**

This prevents the "mostly correct" problem — a single unsupported claim in an otherwise excellent output forces the entire output to UNVERIFIED.

### 4.6 Confidence Threshold Penalty

For high-stakes claims (security, hardware, database):
- Confidence threshold t = 0.90
- Penalty for wrong answer: t/(1-t) = 9x
- This makes fabrication prohibitively expensive
- Abstention becomes the rational choice

Decision rule:
- IF confidence ≥ 0.90 AND 2+ sources → **PROCEED**
- IF confidence 0.60-0.89 → **ESCALATE** to user
- IF confidence < 0.60 → **REJECT** and provide alternative

---

## 5. RED TEAM REVIEW PROTOCOL

### 5.1 Three-Way Adversarial Critique

Every agent output undergoes THREE independent adversarial reviews:

```
Agent Output
     |
     v
+----+----+----+
|    |    |    |
v    v    v    v
TECH  SEC  ARCH
 |    |    |
 |    |    |
 v    v    v
+----+----+----+
|  CONSOLIDATE  |
|  Attack Paths |
+----+----+----+
     |
     v
  DELIVER (or REJECT)
```

#### Technical Critique

| Question | Check |
|----------|-------|
| "Will this code crash in production?" | Static analysis, null checks, error handling |
| "Memory leak?" | Zero-allocation check (C++ audio callbacks) |
| "Race condition?" | Lock-free pattern verification |
| "Performance regression?" | Complexity analysis, benchmark comparison |
| "Edge case handling?" | Boundary conditions, empty states |

#### Security Critique

| Question | Check |
|----------|-------|
| "OWASP Top 10 2025 compliance?" | Security scan against OWASP checklist |
| "Credential hardcoded?" | Secret scan, .env verification |
| "CSRF/CSP/RateLimit bypass?" | Penetration test rules |
| "Injection possible?" | Input sanitization verification |
| "Session hijacking possible?" | Cookie flags, timeout, fixation |

#### Architecture Critique

| Question | Check |
|----------|-------|
| "L0→L3 import?" | Dependency graph check (layer violation) |
| "Middleware order broken?" | Pipeline order validation |
| "BCNF violation?" | Schema normalization audit |
| "ADR compliance?" | Cross-reference with accepted ADRs |
| "SOLID violation?" | Single responsibility, dependency inversion |

### 5.2 Attack Path Documentation

**Microsoft AIRT Lesson 3:** "Attack Success Rate tells you how often the attack works. The prompt sequences, tool call traces, and parameter logs tell you the attack path. Always capture both."

For every review, document:

```markdown
## Attack Path: [Attack Name]

**Target:** [component/endpoint/claim]
**Actor:** [adversarial user / benign user failure / automated bot]
**Entry Point:** [how the attack enters the system]
**Propagation:** [how it moves through the system]
**Impact:** [what damage it causes]
**Severity:** [CRITICAL / HIGH / MEDIUM / LOW]
**Fix:** [concrete mitigation with code reference]
**Verification:** [how to confirm the fix works]
```

### 5.3 Break-Fix Cycle Methodology

From Microsoft's Phi-3 safety alignment process:

```
ROUND 1: Manual Red Team
  - 6-hour session per agent domain
  - Produce novel attack categories (target: 2-5 per session)
  - Document full attack paths
          |
          v
ROUND 2: Automate Known Attacks
  - Convert manual findings to automated checks
  - Add to H001-H039 pattern database
  - Run against all agents
          |
          v
ROUND 3: Fix & Re-test
  - Implement mitigations
  - Re-run automated checks
  - Verify fix doesn't introduce new risks
          |
          v
ROUND 4: Purple Team Review
  - Blue team (defense) reviews red team (offense) findings
  - Both teams validate fixes
  - Document lessons learned
          |
          v
CYCLE REPEATS
  - New attack categories → Round 1
  - Regression failures → Round 2
  - System changes → Full cycle
```

---

## 6. TRUTH MODE ENFORCEMENT

### 6.1 Claim-Level Specificity Control

Based on CSS (Compositional Selective Specificity) research, 2026.

When an agent makes a claim, evaluate:

| Precision Level | Description | Action |
|-----------------|-------------|--------|
| Fine-grained | "CoreMusic_DSP::applyFilter() supports 12 filter types" | Requires datasheet/AADR proof |
| Coarse-grained | "CoreMusic DSP supports filtering" | Acceptable with vault reference |
| Vague | "The system processes audio" | Always acceptable, no verification needed |
| Omitted | (claim not made) | N/A |

**Rule:** Express uncertainty as structured semantic backoff, NOT as vague hedging.
- ❌ "This might work, I'm not sure"
- ✅ "CoreMusic DSP supports filtering (coarse). Specific filter types require verification against Neva Engine source code."

### 6.2 Source Citation Enforcement

Every factual claim MUST include a citation in one of these formats:

| Source Type | Citation Format |
|-------------|----------------|
| Vault reference | `[[.ai/decisions/accepted/ADR-XXX]]` |
| Official docs | `[PHP Manual - PDO::prepare](https://www.php.net/...)` |
| Datasheet | `[TI PCM3168A Datasheet](https://www.ti.com/...)` |
| Standard | `[OWASP SQL Injection Prevention](https://owasp.org/...)` |
| Code reference | `assets.coremusic.net/js/router/Router.js#L682` |

### 6.3 VERIFICATION REQUIRED Format

When confidence < 90:

```php
// ⚠️ VERIFICATION REQUIRED
// Claim: [what was claimed]
// Missing Evidence:
//   - [what evidence is missing]
//   - [where to look for it]
// Required: [action needed to verify]
// Confidence Score: [score]/100 ([REJECTED/UNVERIFIED] - [reason])
```

### 6.4 Truth Mode Verification Block

Every critical output that passes review MUST end with:

```markdown
---
### Red Team & Truth Mode Verification
- **Status:** VERIFIED / UNVERIFIED / REJECTED
- **Confidence Score:** [score]/100
- **Red Team Review:** Technical ✅ | Security ✅ | Architecture ✅
- **Attack Paths Documented:** [count] paths found, [count] mitigated
- **Sources Consulted:**
  1. [source 1] — [category] ([weight] pts)
  2. [source 2] — [category] ([weight] pts)
  3. [source 3] — [category] ([weight] pts)
- **Automated Checks Passed:**
  - [x] H001-H039 Rejected Pattern Scan
  - [x] Vault Knowledge Base Lookup
  - [x] Recency Filter (2024+)
  - [x] Cross-Reference (3+ sources)
  - [x] Red Team: Technical / Security / Architecture
  - [x] Attack Path Documentation
- **Break-Fix Status:** [new finding / regression / fixed / verified]
---
```

---

## 7. CRITICAL REJECTION PATTERNS (H001-H039)

These patterns are AUTOMATIC REJECT triggers. Match = immediate rejection, no exceptions.

### 7.1 Hardware/Audio (H001-H009)

| ID | Pattern | Correct Alternative |
|----|---------|---------------------|
| H001 | PCM5122 for 8.1 surround | PCM3168A (8ch, 24-bit, 192kHz) or AKM AK4458 (8ch, 32-bit) |
| H002 | RPi GPIO 5V driving 3.3V logic | Level shifter required |
| H003 | XMOS XU316 without ASIO SDK | ASIO SDK mandatory for USB Audio Class 2.0 |
| H004 | Class AB amp SNR >100dB without THD+N <0.01%@1kHz | Both specs required |
| H005 | 8+1 surround crossover via I2S without DSP | DSP required for crossover |
| H006 | ADAU1467 DSP without programming | XMOS internal DSP cannot substitute |
| H007 | 0.5ms latency (48kHz, 24-sample) without ASIO Exclusive | ASIO Exclusive mandatory |
| H008 | PCM3168A specs wrong | TSSOP-48, 8ch DAC + 6ch ADC, I2S |
| H009 | AKM AK4458 specs wrong | 32-bit, 8ch DAC, DSD512/PCM768kHz |

### 7.2 Security/Crypto (H010-H019)

| ID | Pattern | Correct Alternative |
|----|---------|---------------------|
| H010 | JWT secret hardcoded | credential_vault AES-256-GCM mandatory |
| H011 | MD5 or SHA-1 for encryption | Argon2id (64MB, 4, 2) or AES-256-GCM |
| H012 | Hardcoded API keys / DB passwords | .env or vault usage mandatory |
| H013 | Direct $_GET/$_POST access without sanitize | Filter input first |
| H014 | CSRF token key '_csrf_token' | 'csrf_token' (removed 2026-05-30) |
| H015 | CSP nonce 16 byte | 256-bit random_bytes(32) mandatory |
| H016 | Session idle timeout 1800s | 3600s (1 hour) mandatory |
| H017 | Session name 'PHPSESSID' | 'COREMUSIC_SESS' mandatory |
| H018 | Rate limit key 'rate_limit:' | 'rl:' . md5($ip) (APCu) mandatory |
| H019 | MFA/TOTP feature claim | Does not exist in CoreMusic (ADR-011) |

### 7.3 Database/SQL (H020-H029)

| ID | Pattern | Correct Alternative |
|----|---------|---------------------|
| H020 | Non-existent or version-mismatched SQL functions | Check MySQL 9 docs |
| H021 | SELECT * usage | Explicit column list mandatory |
| H022 | ORM usage | Raw PDO + Prepared Statements (ADR-002) |
| H023 | Cross-database foreign key | 9 DB BCNF isolation (ADR-040) |
| H024 | DELETE without soft delete | deleted_at timestamp mandatory |
| H025 | DB name 'coremusic_music' | 'coremusic_musics' (plural, ADR-040) |
| H026 | DB name 'coremusic_download' | 'coremusic_catalog' |
| H027 | DB 'coremusic_neva' or 'coremusic_credential' | Not in config |
| H028 | Claim of 10 databases | Config has exactly 9 |
| H029 | String concatenation in SQL | Prepared statements mandatory |

### 7.4 API/Middleware (H030-H039)

| ID | Pattern | Correct Alternative |
|----|---------|---------------------|
| H030 | '/api/v2/auth/login' endpoint | Real route: '/login' (SpaRoute) |
| H031 | Non-existent classes (FileUploadHandler, etc.) | Check system.database |
| H032 | Non-existent directories (storage/, uploads/) | Check root structure |
| H033 | Middleware order change | Session→BypassAuth→RateLimit→Auth→SecurityHeaders→Csrf |
| H034 | die()/header() in middleware | Return ['halt' => true] array |
| H035 | Direct $_POST/$_SERVER in middleware | Use normalized $request array |
| H036 | 'COREMUSIC_SID' session name | 'COREMUSIC_SESS' |
| H037 | Missing cookie flags | HttpOnly=1, Secure=1, SameSite=Lax mandatory |
| H038 | SecurityHeaders before SessionManager | SessionManager MUST run first |
| H039 | BypassAuth in production | Disabled when APP_ENV=production |

---

## 8. RULES OF ENGAGEMENT

Adapted from Google AI Red Team's strict rules of engagement protocol.

### 8.1 CoreMusic Scope Rules

| Rule | Description |
|------|-------------|
| **Scope Limitation** | Only review outputs within CoreMusic project boundaries |
| **No Real Data** | Never use production credentials or real user data in reviews |
| **Audit Trail** | Every review must be logged to `.ai/log.md` |
| **Distinguish Red from Real** | Clearly label adversarial findings vs actual bugs |
| **No Fabrication of Findings** | Red team findings must be evidence-based |

### 8.2 Engagement Protocol

```
1. RECEIVE   — Agent output delivered
2. DECOMPOSE — Extract atomic claims, identify attack surface
3. PATTERN   — Match against H001-H039 database
4. SCORE     — Apply weighted rubric per claim
5. REVIEW    — Three-way adversarial critique (Tech/Sec/Arch)
6. DOCUMENT  — Attack paths, not just pass/fail
7. DECIDE    — VERIFIED / UNVERIFIED / REJECTED
8. LOG       — Append to .ai/log.md
```

---

## 9. SYSTEM INTEGRATION

### 9.1 Vault Knowledge Base Lookup Order

```
1. .ai/knowledge/verified/      → Use directly
2. .ai/knowledge/unverified/    → 30-day check + user approval
3. .ai/knowledge/rejected/      → Rejected pattern check
4. .ai/decisions/accepted/      → ADR architecture decisions
5. .ai/brain.md                 → Central decision records
6. .ai/architecture/            → L0-L3 layer specifications
7. CLAUDE.md                    → Project rules and architecture
```

### 9.2 Automation Scripts

```
.claude/scripts/validate-vault-links.sh      → Wiki-link integrity
.claude/scripts/check-frontmatter.sh         → YAML metadata
.claude/scripts/validate-adrs.sh             → ADR immutability (001-037 frozen)
.claude/scripts/validate-hallucination-control.sh  → Skill self-validation
```

### 9.3 CI/CD Gate Integration

```
- Pre-commit hook: validate-vault-links.sh + check-frontmatter.sh + validate-adrs.sh
- PR validation: All 4 scripts must pass, merge blocked on failure
- Monthly audit: vault-validation.yml + session-archival.sh + link-health-report.yml
```

---

## 10. EXECUTION WORKFLOW

### 10.1 Pipeline Steps

1. **Request Analysis:** What library, method, hardware, or architecture is needed?
2. **Threat Model Application:** Map output to attack surface (§2.2)
3. **H001-H039 Pattern Scan:** Automatic reject check
4. **Confidence Scoring:** Weighted rubric per atomic claim (§4.2)
5. **Three-Way Red Team Review:** Technical / Security / Architecture (§5.1)
6. **Attack Path Documentation:** Full path, not just pass/fail (§5.2)
7. **Action Decision:**
   - **≥90 (VERIFIED):** Code written, truth block appended, log entry created
   - **60-89 (UNVERIFIED):** VERIFICATION REQUIRED block, user confirmation
   - **<60 (REJECTED):** Rejected, correct alternative, log entry with attack path

### 10.2 Verification Checklist

Every task must complete ALL items before delivery:

- [ ] **9-Step Boot Protocol** completed
- [ ] Relevant `.ai/` vault section read (domain-specific)
- [ ] **H001-H039 Rejected Pattern** scan completed (no matches)
- [ ] **Confidence Score** calculated (weighted rubric, atomic claims)
- [ ] **Three-Way Red Team Review** completed (Technical / Security / Architecture)
- [ ] **Attack Paths** documented for any findings
- [ ] **Score ≥ 90** then: VERIFIED, code written, truth block appended
- [ ] **Score 60-89** then: UNVERIFIED, VERIFICATION REQUIRED block added
- [ ] **Score < 60** then: REJECTED, correct alternative provided
- [ ] **Truth Mode Block** added to output end (Mandatory)
- [ ] All **Sources** cited with vault path or official URL
- [ ] **Cross-References** in [[path/to/file]] wiki-link format
- [ ] **YAML Frontmatter** updated if applicable
- [ ] **.ai/log.md** entry created (append-only)
- [ ] **Break-Fix Status** noted (new finding / regression / fixed / verified)

---

## 11. AGENT-SPECIFIC RED TEAM CHECKLISTS

### 11.1 Backend Architect

- [ ] Middleware order matches pipeline (H033)
- [ ] No `die()`/`header()` in middleware (H034)
- [ ] Normalized `$request` array used (H035)
- [ ] PDO prepared statements only (ADR-002)
- [ ] No SELECT * (H021)
- [ ] Soft delete with `deleted_at` (H024)
- [ ] CSRF token = 'csrf_token' (H014)
- [ ] Session name = 'COREMUSIC_SESS' (H036/H017)
- [ ] Cookie flags: HttpOnly=1, Secure=1, SameSite=Lax (H037)
- [ ] Rate limiting with 'rl:' prefix (H018)

### 11.2 UI Designer

- [ ] No `innerHTML` with user data (XSS)
- [ ] CSP nonce 256-bit (H015)
- [ ] Output encoding on all dynamic content
- [ ] No inline event handlers (CSP compliance)
- [ ] Responsive design verified (320px-2560px)
- [ ] WCAG 2.2 AA compliance
- [ ] Keyboard navigation functional
- [ ] Screen reader compatibility

### 11.3 Security Engineer

- [ ] OWASP Top 10 2025 compliance check
- [ ] No hardcoded credentials (H012)
- [ ] JWT in credential_vault AES-256-GCM (H010)
- [ ] Argon2id for password hashing (H011)
- [ ] CSP headers configured (H015)
- [ ] Rate limiting active (H018)
- [ ] BypassAuth disabled in production (H039)
- [ ] Session timeout 3600s (H016)

### 11.4 Data Engineer

- [ ] No ORM usage (ADR-002, H022)
- [ ] Explicit column lists (H021)
- [ ] 9 database BCNF isolation (H023, ADR-040)
- [ ] Correct DB names (H025-H028)
- [ ] Prepared statements only (H029)
- [ ] Soft delete pattern (H024)
- [ ] No cross-database foreign keys (H023)

### 11.5 Embedded Engineer

- [ ] PCM3168A or AK4458 only (H001)
- [ ] Level shifter for GPIO voltage (H002)
- [ ] ASIO SDK for XMOS (H003)
- [ ] Real-time audio callback: zero allocation
- [ ] Buffer overflow protection
- [ ] Latency claims verified (H007)
- [ ] Hardware specs from datasheet PDF

### 11.6 QA Engineer

- [ ] Test coverage ≥ 80% for new code
- [ ] No false positive tests
- [ ] Edge cases covered
- [ ] Integration tests for middleware chain
- [ ] Security tests included
- [ ] Regression tests for H001-H039 patterns

### 11.7 DevOps Engineer

- [ ] No secrets in CI/CD configs
- [ ] Docker image scanning enabled
- [ ] GitHub Actions permissions minimal
- [ ] Deployment scripts validated
- [ ] Rollback plan documented
- [ ] Monitoring/alerting configured

---

## 12. VERSION HISTORY & REFERENCES

### 12.1 Changelog

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2026-07-30 | Initial release - Basic red team |
| 2.0.0 | 2026-07-31 | Truth mode, confidence scoring |
| 3.0.0 | 2026-08-01 | Agent-specific rules |
| 4.0.0 | 2026-08-02 | Agentic orchestration |
| 5.0.0 | 2026-08-08 | Merged with hallucination-control |
| 6.0.0 | 2008-08-15 | Hallucination-control v6.0 rewrite |
| 7.0.0 | 2026-08-15 | **Standalone rewrite** — Research-based (Google, Microsoft, OWASP, NIST, ACL 2026) |

### 12.2 Research Sources

| Source | Key Contribution |
|--------|------------------|
| Google AI Red Team (2026) | Rules of engagement, attacker mindset, AI-powered attacks |
| Microsoft AIRT (2025) | 100+ products retrospective, system-level testing, attack paths |
| OWASP GenAI Initiative (2025-2026) | Standardized red teaming methodology, threat taxonomy |
| NIST/Gray Swan (2026) | Agent competition data, 250K+ attacks, attack transferability |
| ACL 2026 — SAVeR | Self-audited verified reasoning, faithfulness verification |
| ACL 2026 — CSS | Claim-level specificity control, semantic backoff |
| UAI 2026 | Multi-agent fact verification, uncertainty quantification |
| Georgetown CSET (2025) | Threat model design, tool selection for red teaming |
| Agentic Confidence Calibration (2025) | HTC framework, trajectory-level calibration |

### 12.3 Cross-Reference Map

```
SKILL.md (this file)
    +-- hallucination-control/SKILL.md (complementary, H001-H039 shared)
    +-- .claude/rules/core-rules.md
    +-- .claude/rules/security-standards.md
    +-- .ai/brain.md (Section 18: Zero Hallucination)
    +-- .ai/CLAUDE.md (Section 7: Hallucination Control)
    +-- .ai/knowledge/verified/
    +-- .ai/knowledge/unverified/
    +-- .ai/knowledge/rejected/
    +-- .ai/decisions/accepted/
    +-- .ai/log.md
```

---

*Red Team & Truth Mode v7.0.0 — CoreMusic Adversarial Review System*
*Authority: Vault Steward / AI Orchestrator*
*Mandatory for all agents — No exceptions — Every output is an attack surface*
*Research: Google, Microsoft AIRT, OWASP, NIST, ACL 2026*
