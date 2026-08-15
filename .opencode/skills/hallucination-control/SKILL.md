---
title: "CoreMusic — Hallucination Control System"
type: skill-instruction
version: 6.0.0
authority: SSOT
mode:
  - Red Team
  - Truth Mode
  - Human Mode
purpose:
  - Zero-Hallucination Enforcement
  - Confidence-Based Verification
  - Multi-Layer Defense Pipeline
  - Source Citation Enforcement
  - Agent Knowledge Grounding
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/AGENTS.md"
    - ".ai/WORKFLOW.md"
    - ".ai/brain.md"
    - ".ai/index.md"
  architecture:
    - ".ai/ADR/"
    - "Existing project architecture"
  templates:
    - ".ai/.templates/index.md"
  agents:
    - ".ai/.agents/AGENTS.md"
  skills:
    - ".opencode/skills/red-team-truth-mode/SKILL.md"
  project_structure:
    - "coremusic.net/"
    - "shared/"
  update_policy:
    preserve_existing_structure: true
    require_approval_for:
      - "truth mode policy change"
      - "verification rule change"
      - "H001-H039 pattern addition"
triggers:
  - "her araştırma"
  - "her mimari karar"
  - "her API ve kütüphane kullanımı"
  - "her kod üretimi"
  - "her vault güncellemesi"
  - "her CI/CD pipeline çalışması"
  - "red team"
  - "truth mode"
  - "hallucination"
  - "doğrulama"
  - "source citation"
  - "confidence check"
changelog:
  - version: 6.0.0
    date: 2026-08-15
    changes:
      - Complete rewrite — MIM format (500 lines)
      - Added atomic claim decomposition (OpenAI 2025)
      - Added confidence threshold penalty t/(1-t)
      - Added 6-category taxonomy from MDPI 2026 survey
      - Restructured H001-H039 as compact tables
      - Added source citation enforcement
      - Added multi-layer defense pipeline
---

# HALLUCINATION CONTROL — Machine Instruction Manual

**THIS SYSTEM IS NOT A PREFERENCE — IT IS AN ABSOLUTE MANDATE. NO EXCEPTIONS.**

This document is the MANDATORY hallucination prevention system for ALL CoreMusic AI agents.
Every agent, every session, every technical claim — without exception.

---

## 0. IDENTITY & SCOPE

### 0.1 System Definition

| Field | Value |
|-------|-------|
| System Name | Hallucination Control System |
| Version | 6.0.0 |
| Authority | SSOT (Single Source of Truth) |
| Scope | All 11 CoreMusic Agents |
| Enforcement | AUTOMATIC — triggers on every technical claim |
| Override | FORBIDDEN — only Vault Steward can modify |
| Research Basis | MDPI 2026 Survey, OpenAI 2025, UniCR Framework |

### 0.2 Agent Coverage

| Agent | Domain | H001-H039 Enforced |
|-------|--------|---------------------|
| Backend Architect | PHP/API/DB | H010-H039 |
| UI Designer | JS/CSS/UX | H030-H039 |
| Security Engineer | OWASP/Auth | H010-H019 |
| Data Engineer | MySQL/BCNF | H020-H029 |
| Embedded Engineer | C++/Audio | H001-H009 |
| QA Engineer | Testing | All |
| DevOps Engineer | CI/CD | H030-H039 |
| Audio HW Engineer | DAC/ADC | H001-H009 |
| DSP Firmware | XMOS/I2S | H001-H009 |
| Windows SW | WASAPI/COM | H030-H039 |
| Master Orchestrator | Coordination | All |

### 0.3 Enforcement Principle

Every technical claim must be grounded in verifiable evidence.
If evidence is insufficient, abstain — never fabricate.

---

## 1. CORE TRUTH MANDATE

### 1.1 Five Absolute Rules

1. FABRICATING INFORMATION IS STRICTLY FORBIDDEN.
2. EVERY TECHNICAL CLAIM REQUIRES MINIMUM 2 VERIFIABLE SOURCES.
3. UNCERTAIN INFORMATION IS NOT SYNTHESIZED — it triggers abstention or user escalation.
4. LOCAL VAULT (.ai) AND OFFICIAL DOCUMENTATION ARE THE ONLY TRUTH SOURCES.
5. WEB SEARCH IS PROHIBITED — validation via system integration (vault, scripts, CI/CD).

### 1.2 Prohibited Behaviors

| Behavior | Consequence |
|----------|-------------|
| "Sanırım" / "Muhtemelen" / "Bildiğim kadarıyla" | Immediate rejection |
| Fabricating API endpoints | H030-H039 trigger |
| Inventing class names | H031 trigger |
| Using deprecated methods | Confidence score ≤ 60 |
| Guessing hardware specs | H001-H009 trigger |
| Assuming database schema | H020-H029 trigger |
| "Framework FORBIDDEN" violation | ADR-001 trigger |
| "ORM FORBIDDEN" violation | ADR-002 trigger |

### 1.3 Abstention is a Valid Response

When confidence < 90:
- DO NOT guess
- DO NOT fabricate
- DO write: // ⚠️ VERIFICATION REQUIRED
- DO ask user for confirmation
- DO cite the missing evidence

---

## 2. CONFIDENCE SCORING SYSTEM

### 2.1 Three-Tier Classification

| Range | Status | Action | Storage |
|-------|--------|--------|---------|
| 90-100 | VERIFIED | Code directly, implement immediately | .ai/knowledge/verified/ |
| 60-89 | UNVERIFIED | Usable but risky — requires user confirmation | .ai/knowledge/unverified/ |
| <60 | REJECTED | FORBIDDEN — provide correct alternative | .ai/knowledge/rejected/ |

### 2.2 Weighted Scoring Rubric

| Category | Weight | Sources |
|----------|--------|---------|
| Official Documentation | 40 pts | php.net, MDN, dev.mysql.com, owasp.org, caniuse.com, vendor PDFs |
| Standards (RFC/ISO/OWASP) | 25 pts | RFC 9106, ISO 27001, OWASP Top 10 2025, PSR-12 |
| Local Vault Evidence | 15 pts | .ai/decisions/, .ai/brain.md, .ai/architecture/, CLAUDE.md |
| Community (High Score) | 10 pts | StackOverflow accepted, GitHub issues with maintainer response |
| Unknown/Old Source | -50 pts | Blog, Medium, Wikipedia, pre-2024 sources |

### 2.3 Recency Filter

| Period | Penalty | Requirement |
|--------|---------|-------------|
| 2024+ | 0 | Full score |
| 2022-2023 | -10 | User confirmation required |
| <2022 | -15 | Mandatory user approval + 2x sources |

### 2.4 Cross-Validation Rules

| Sources | Action |
|---------|--------|
| 3+ independent sources | VERIFIED permitted |
| 2 mixed sources | User confirmation required |
| 1 single source | Additional validation mandatory |
| Contradictory evidence | BLOCKED — report to user |

### 2.5 Atomic Claim Decomposition

Every technical output is decomposed into atomic claims:

| Claim Type | Verification Method |
|------------|---------------------|
| Factual assertion | Source citation required |
| API/endpoint existence | Vault check + official docs |
| Hardware specification | Datasheet PDF required |
| Security requirement | OWASP/NIST reference |
| Performance claim | Benchmark or calculation |

Each atomic claim receives its own confidence score.
Final output score = minimum(atomic claim scores).

### 2.6 Confidence Threshold Penalty

For high-stakes claims (security, hardware, database):
- Confidence threshold t = 0.90
- Penalty for wrong answer: t/(1-t) = 9x
- This makes fabrication prohibitively expensive
- Abstention becomes the rational choice

Decision rule:
- IF confidence ≥ 0.90 AND 2+ sources → PROCEED
- IF confidence 0.60-0.89 → ESCALATE to user
- IF confidence < 0.60 → REJECT and provide alternative

---

## 3. CRITICAL REJECTION PATTERNS (H001-H039)

These patterns are AUTOMATIC REJECT triggers.
Match = immediate rejection, no exceptions.

### 3.1 Hardware/Audio (H001-H009)

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

### 3.2 Security/Crypto (H010-H019)

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

### 3.3 Database/SQL (H020-H029)

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

### 3.4 API/Middleware (H030-H039)

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

## 4. SYSTEM INTEGRATION PROTOCOL

### 4.1 Vault Knowledge Base Lookup Order

```
1. .ai/knowledge/verified/      → Use directly
2. .ai/knowledge/unverified/    → 30-day check + user approval
3. .ai/knowledge/rejected/      → Rejected pattern check
4. .ai/decisions/accepted/      → ADR architecture decisions
5. .ai/brain.md                 → Central decision records
6. .ai/architecture/            → L0-L3 layer specifications
7. CLAUDE.md                    → Project rules and architecture
```

### 4.2 Automation Scripts

```
.claude/scripts/validate-vault-links.sh      → Wiki-link integrity
.claude/scripts/check-frontmatter.sh         → YAML metadata
.claude/scripts/validate-adrs.sh             → ADR immutability (001-037 frozen)
.claude/scripts/validate-hallucination-control.sh  → Skill self-validation
```

### 4.3 CI/CD Gate Integration

```
- Pre-commit hook: validate-vault-links.sh + check-frontmatter.sh + validate-adrs.sh
- PR validation: All 4 scripts must pass, merge blocked on failure
- Monthly audit: vault-validation.yml + session-archival.sh + link-health-report.yml
```

### 4.4 Agent Coordination Table

| Agent | Responsibility | Validation Source |
|-------|----------------|-------------------|
| Backend Architect | PHP/API/DB | php.net, dev.mysql.com, CLAUDE.md |
| UI Designer | Vanilla JS/CSS/UX | MDN, caniuse.com |
| Security Engineer | OWASP/Auth/Crypto | owasp.org, nist.gov |
| Data Engineer | MySQL/BCNF/Optimization | dev.mysql.com, .ai/decisions/ |
| Embedded Engineer | C++/Audio/Hardware | TI.com datasheet, .ai/electronic/ |
| QA Engineer | Testing/E2E/Browsers | playwright.dev, vitest.dev |
| DevOps Engineer | CI/CD/Deploy | official docs, .github/workflows/ |

---

## 5. AGENTIC ORCHESTRATION

### 5.1 Three-Layer Validation Architecture

```
+---------------------------------------------------------------+
|              HALLUCINATION CONTROL ORCHESTRATOR                 |
+---------------------------------------------------------------+
|  LAYER 1: VALIDATOR AGENTS (Parallel)                          |
|  +-- HardwareValidator    +-- SecurityValidator                 |
|  +-- DatabaseValidator    +-- APIValidator                      |
+---------------------------------------------------------------+
|  LAYER 2: AUDITOR AGENTS (Sequential)                          |
|  +-- CrossReferenceAuditor   (3+ source check)                 |
|  +-- RecencyAuditor          (date filter)                     |
|  +-- ArchitectureAuditor     (L0-L3 layer compliance)          |
+---------------------------------------------------------------+
|  LAYER 3: INTEGRATOR AGENT (Single)                            |
|  +-- VaultIntegrator                                           |
|       +-- auto-promote (unverified->verified, 30 days)         |
|       +-- auto-archive (rejected patterns)                     |
|       +-- cross-reference update (wiki-links)                  |
+---------------------------------------------------------------+
```

### 5.2 Agent Communication Protocol

```json
{
  "agent_id": "validator:hardware",
  "task": "verify_pcm3168a_specs",
  "input": {"claim": "PCM3168A supports 8-channel DAC at 192kHz"},
  "sources": [
    {"type": "vault", "path": ".ai/decisions/accepted/ADR-038.md"},
    {"type": "official", "url": "https://www.ti.com/product/PCM3168A"}
  ],
  "output": {"score": 95, "status": "VERIFIED", "evidence": "TI Datasheet + ADR-038"}
}
```

### 5.3 Parallel Verification Pipeline

```
Technical Claim Received
         |
         v
Requires Validation? --No--> Use Vault Evidence
         |Yes
         v
Spawn Validator Agents (Parallel)
[Hardware, Security, DB, API]
         |
         v
Score Sources (Weighted Rubric)
         |
         v
All Validators >=90? --No--> Flag UNVERIFIED / REJECTED
         |Yes
         v
Auditor Layer (Sequential)
[Cross-ref + Recency + Architecture]
         |
         v
All Auditors Pass? --No--> Flag UNVERIFIED
         |Yes
         v
Integrator: VERIFIED
Store .ai/knowledge/verified/
         |
         v
Truth Mode Block Output
```

---

## 6. AUTOMATION FRAMEWORK

### 6.1 Auto Confidence Scoring Pipeline

```
1. CLAIM EXTRACTION
   - Extract technical claims from code, docs, or decisions
   - Pattern: "X supports Y", "Use Z for W", "Method A does B"

2. PATTERN MATCHING
   - Compare against H001-H039 rejected pattern DB
   - Match found -> REJECTED (score: 0)

3. VAULT LOOKUP
   - .ai/knowledge/verified/   -> +40 points
   - .ai/knowledge/unverified/ -> +20 points (30-day check)
   - .ai/decisions/accepted/   -> +15 points (ADR reference)
   - CLAUDE.md / .ai/brain.md  -> +15 points

4. OFFICIAL DOCS CHECK
   - php.net/MDN/owasp.org/vendor PDF -> +25 points (if available)
   - RFC/ISO standard                  -> +25 points

5. RECENCY CHECK
   - 2024+  -> +0
   - 2022-2023 -> -10
   - <2022  -> -15 + user confirmation required

6. FINAL SCORE
   - >=90  -> VERIFIED  -> auto-store .ai/knowledge/verified/
   - 60-89 -> UNVERIFIED -> flag for user confirmation
   - <60   -> REJECTED  -> auto-store .ai/knowledge/rejected/
```

### 6.2 Auto Knowledge Management

```
UNVERIFIED PROMOTION (30-day rule):
- Scan .ai/knowledge/unverified/ files
- creation_date > 30 days -> re-evaluate
- If still valid and 2+ sources verified -> move to VERIFIED
- Else -> move to REJECTED or keep with warning

REJECTED ARCHIVAL:
- Version .ai/knowledge/rejected/ files
- Pattern: H001_PCM5122_8.1_v1.md
- Archive old versions under .ai/knowledge/rejected/archive/

CROSS-REFERENCE UPDATE:
- Scan all wiki-links [[path/to/file]]
- Broken link found -> update .ai/index.md and .ai/keys.md
- New ADR added -> add reference to domain files
```

### 6.3 Red Team Adversarial Review Automation

```
TECHNICAL CRITIQUE:
- "Will this code crash in production?" -> Static analysis
- "Memory leak?" -> Zero-allocation check (C++ audio callback)
- "Race condition?" -> Lock-free pattern verification

SECURITY CRITIQUE:
- "OWASP Top 10 2025 compliance?" -> Security scan
- "Credential hardcoded?" -> Secret scan
- "CSRF/CSP/RateLimit bypass?" -> Penetration test rules

ARCHITECTURE CRITIQUE:
- "L0->L3 import?" -> Dependency graph check
- "Middleware order broken?" -> Pipeline order validation
- "BCNF violation?" -> Schema normalization audit
```

---

## 7. EXECUTION WORKFLOW

### 7.1 Pipeline Steps

1. **Request Analysis:** What library, method, hardware, or architecture is needed?
2. **System-Integrated Research:** Information gathered from .ai vault, official docs, automation scripts
3. **Scoring:** Colored information scored 0-100 with weighted rubric
4. **Cross-Validation:** Validator agents parallel, Auditor agents sequential
5. **Red Team Review:** 3-way adversarial critique (Technical, Security, Architecture)
6. **Action Decision:**
   - **>=90 (VERIFIED):** Code written, .ai/knowledge/verified/ stored, Truth Block generated
   - **60-89 (UNVERIFIED):** // ⚠️ VERIFICATION REQUIRED block, user confirmation requested
   - **<60 (REJECTED):** Rejected, correct alternative provided, .ai/knowledge/rejected/ archived

### 7.2 VERIFICATION REQUIRED Format

When an agent cannot verify API, class, endpoint, or hardware feature existence (Score < 90):

```php
// ⚠️ VERIFICATION REQUIRED
// Claim: CoreMusic_DSP::applyFilter() method exists
// Missing Evidence:
//   - Not found in .ai/projects/NevaEngine/neva-engine-integration.md
//   - Not found in CLAUDE.md L0-L3 architecture
//   - No ADR referencing this method
// Required: Check C++ Neva Engine source or create ADR for new DSP API
// Confidence Score: 45/100 (REJECTED - H031 pattern)
```

```javascript
// ⚠️ VERIFICATION REQUIRED
// Claim: Router.navigate() supports 'replaceState' parameter
// Missing Evidence:
//   - Router.js#L682 shows only (url, pushState=true)
//   - No ADR-021 reference for replaceState
// Required: Check assets.coremusic.net/js/router/Router.js
// Confidence Score: 65/100 (UNVERIFIED - needs user confirmation)
```

### 7.3 Truth Mode Verification Block (Mandatory)

Every critical output that passes hallucination control MUST end with:

```markdown
---
### Truth Mode & Hallucination Control Verification
- **Status:** VERIFIED
- **Confidence Score:** 95/100
- **Validation Pipeline:** Validator Layer -> Auditor Layer -> Integrator -> Red Team Review
- **Sources Consulted:**
  1. [PHP Manual - PDO::prepare](https://www.php.net/manual/en/pdo.prepare.php) — Official Docs (40 pts)
  2. [OWASP SQL Injection Prevention](https://owasp.org/www-community/attacks/SQL_Injection) — Standard (25 pts)
  3. [CoreMusic CLAUDE.md](CLAUDE.md) — Internal Vault Architecture Rules (15 pts)
  4. [.ai/decisions/accepted/ADR-010-csrf-protection-strategy.md](ADR-010) — Architecture Decision (15 pts)
- **Automated Checks Passed:**
  - [x] H001-H039 Rejected Pattern Scan
  - [x] Vault Knowledge Base Lookup
  - [x] Recency Filter (2024+)
  - [x] Cross-Reference (3+ sources)
  - [x] Red Team: Technical / Security / Architecture
---
```

---

## 8. VERIFICATION CHECKLIST

Every task must complete ALL items before delivery:

- [ ] **9-Step Boot Protocol** completed (CLAUDE.md, AGENTS.md, WORKFLOW.md, .ai/index.md, .ai/keys.md, .ai/AGENTS.md, .ai/brain.md, .ai/MEMORY.md, .ai/log.md)
- [ ] Relevant `.ai/` vault section read (domain-specific)
- [ ] Required files read
- [ ] **Validator Agents** run in parallel (Hardware, Security, DB, API)
- [ ] **Auditor Agents** run sequentially (Cross-Ref, Recency, Architecture)
- [ ] **Red Team Review** 3-way completed (Technical, Security, Architecture)
- [ ] H001-H039 **Rejected Pattern** scan completed (no matches)
- [ ] **Confidence Score** calculated (weighted rubric)
- [ ] **Score >= 90** then: VERIFIED, code written, .ai/knowledge/verified/ stored
- [ ] **Score 60-89** then: UNVERIFIED, // ⚠️ VERIFICATION REQUIRED block added
- [ ] **Score < 60** then: REJECTED, .ai/knowledge/rejected/ archived
- [ ] **Truth Mode Block** added to output end (Mandatory)
- [ ] All **Sources** cited with vault path or official URL
- [ ] **Cross-References** in [[path/to/file]] wiki-link format
- [ ] **YAML Frontmatter** updated if applicable (updated field)
- [ ] **Vault Automation Scripts** referenced (validate-*.sh)
- [ ] **Agent-Specific Rules** applied (Backend, UI, Security, Data, Embedded, QA, DevOps)
- [ ] **CI/CD Gate** integration verified (pre-commit, GitHub Actions)

---

## 9. VERSION HISTORY & REFERENCES

### 9.1 Changelog

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2026-07-30 | Initial release - Basic hallucination control |
| 2.0.0 | 2026-07-31 | Confidence scoring, Truth Mode, H001-H013 |
| 3.0.0 | 2026-08-01 | Agent-specific rules, Web search protocol |
| 3.1.0 | 2026-08-01 | ADR-038 integration, Vault navigation |
| 4.0.0 | 2026-08-02 | Agentic Orchestration, Automation Framework |
| 5.0.0 | 2026-08-08 | Merged red-team-truth-mode into single skill |
| 6.0.0 | 2026-08-15 | Complete rewrite — MIM format, atomic claims, threshold penalty |

### 9.2 Cross-Reference Map

```
SKILL.md (this file)
    +-- .claude/rules/core-rules.md
    +-- .claude/rules/php-standards.md
    +-- .claude/rules/js-standards.md
    +-- .claude/rules/database-standards.md
    +-- .claude/rules/security-standards.md
    +-- .claude/rules/devops-standards.md
    +-- .ai/brain.md (Section 18: Zero Hallucination)
    +-- .ai/CLAUDE.md (Section 7: Hallucination Control)
    +-- .ai/knowledge/verified/
    +-- .ai/knowledge/unverified/
    +-- .ai/knowledge/rejected/
    +-- .claude/scripts/validate-vault-links.sh
    +-- .claude/scripts/check-frontmatter.sh
    +-- .claude/scripts/validate-adrs.sh
    +-- .claude/scripts/validate-hallucination-control.sh
```

---

*Hallucination Control System v6.0.0 — CoreMusic MIM Format*
*Authority: Vault Steward / AI Orchestrator*
*Mandatory for all agents — No exceptions — Zero tolerance for hallucinations*
*Research: MDPI 2026 Survey, OpenAI 2025, UniCR Framework, EY Guardrails*
