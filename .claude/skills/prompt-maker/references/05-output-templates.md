---
title: OUTPUT TEMPLATES — 20-SECTION MASTER PROMPT
description: Prompt output formats, templates, validation checklist, scoring rubric
version: 7.2.0
updated: 2026-06-11
metrics: "3,820+ questions, 78 categories, 28 references"
quality-score: "98.7%"
---

# OUTPUT TEMPLATES — 20-SECTION MASTER PROMPT
# Prompt Maker v7.2.0 | 2026-06-11

---

## MASTER PROMPT STRUCTURE (20 SECTIONS)

### Section 1: Identity & Expertise
- **Content:** Title, seniority level, domain expertise, years of experience
- **Format:** Table or narrative (title, level, domains, languages, platforms)
- **Example:** "Principal Software Architect — 15 years freelance → enterprise; C/C++/PHP/JS/TS; Security-focused"

### Section 2: Core Rules (5-10 absolute rules)
- **Format:** Numbered list, imperative form
- **Content:** Non-negotiable behaviors, decision-making principles
- **Example:** "No hallucinated APIs", "Web search mandatory for claimed facts"

### Section 3: Language & Communication
- **Format:** Bullet list + examples
- **Content:** Tone, formality, Turkish/English balance, when to ask for clarification

### Section 4: Behavioral Constraints
- **Format:** "NEVER / ALWAYS" list
- **Content:** What AI must avoid, what AI must always do

### Section 5: Uncertainty Handling
- **Format:** Conditional rules (if X then Y)
- **Content:** How to respond when unsure, when to say "I don't know", what to research

### Section 6: Error Management
- **Format:** Recovery strategies + escalation
- **Content:** What to do on task failure, when to ask user, how to recover

### Section 7: Security Mindset
- **Format:** Threat categories + examples
- **Content:** OWASP, active attacker mindset, security checklist

### Section 8: Web Research Mandate
- **Format:** Priority list + source types
- **Content:** When to research, which docs (official first), cross-verification

### Section 9: Architecture & Design
- **Format:** Principles + patterns
- **Content:** SOLID, Clean Code, Hexagonal Architecture, DDD

### Section 10: Testing & Validation
- **Format:** Checklist + automation
- **Content:** Unit/integration/E2E requirements, browser testing, edge cases

### Section 11: Performance & Optimization
- **Format:** Metrics table
- **Content:** Lighthouse 95+, FCP < 1.0s, LCP < 1.5s, etc.

### Section 12: Domain-Specific Rules
- **Format:** Domain → rules mapping
- **Content:** SPA, database, API, embedded, audio, fintech, healthtech, etc.

### Section 13: Ecosystem & Integration
- **Format:** Service diagram + ports + IPC
- **Content:** 7-service ecosystem, port allocation (ADR 031), WebSocket handshake (ADR 032)

### Section 14: Code Quality Standards
- **Format:** Language → checklist
- **Content:** PHP 8.4, JavaScript ES6, Python, C#, C++, Rust standards

### Section 15: Continuous Execution
- **Format:** Loop diagram + stopping condition
- **Content:** Don't stop on first success, edge-case discovery, self-audit

### Section 16: Documentation & Knowledge
- **Format:** Wiki structure + update triggers
- **Content:** Markdown cognition layer, ADR protocol, session logging

### Section 17: Debugging & Troubleshooting
- **Format:** Common issues → solutions
- **Content:** Race conditions, memory leaks, async bugs, browser quirks

### Section 18: Team & Process
- **Format:** Guidelines + communication
- **Content:** Code review, knowledge sharing, onboarding, technical debt

### Section 19: Compliance & Legal
- **Format:** Regulations table
- **Content:** OWASP, PCI DSS, GDPR, HIPAA, SOC 2, ISO 27001

### Section 20: Future & Evolution
- **Format:** Emerging trends + watchlist
- **Content:** AI/LLM integration, web standards, security threats, DevOps trends

---

## TEMPLATE: SYSTEM_PROMPT Output

```markdown
---
inclusion: always
priority: high
version: 1.0
language: Turkish
---

# [PROJECT_NAME] — SYSTEM PROMPT

## Identity
[Seniority, domains, expertise]

## Core Rules
1. [Rule 1]
2. [Rule 2]
...

## Language & Communication
[Tone, formality, when to clarify]

## Behavioral Constraints
- NEVER: [prohibition]
- ALWAYS: [requirement]

[Sections 5-20...]
```

---

## TEMPLATE: DOMAIN_RULES Output

```markdown
---
inclusion: always
priority: high
domain: [SPA|Database|API|Security]
version: 1.0
---

# [DOMAIN_NAME] — DOMAIN RULES

## Core Principles
[5-10 foundational rules]

## Specific Rules
[Numbered rules with examples]

## Anti-Patterns
[What NOT to do]

## Testing Checklist
- [ ] Rule 1 verified
- [ ] Rule 2 verified

## Code Examples
[Annotated examples per rule]
```

---

## VALIDATION CHECKLIST (8 Hard Rules)

- [ ] **H1:** All 20 sections present
- [ ] **H2:** No contradictory rules
- [ ] **H3:** Security mindset explicit
- [ ] **H4:** Web research mandate stated
- [ ] **H5:** Testing requirements clear
- [ ] **H6:** Architecture pattern defined
- [ ] **H7:** Code examples runnable
- [ ] **H8:** No hallucinated APIs/docs

---

## SCORING RUBRIC (8 Dimensions × 100 = 800 max)

| Dimension | Weight | Criteria |
|-----------|--------|----------|
| **Clarity** | 100 | Unambiguous, no jargon, actionable |
| **Completeness** | 100 | All sections, no gaps, comprehensive |
| **Security** | 100 | OWASP, crypto, auth, audit logging covered |
| **Testability** | 100 | Verifiable, edge-case aware, browser validated |
| **Practicality** | 100 | Real-world examples, runnable code |
| **Consistency** | 100 | No contradictions, aligned terminology |
| **Maintainability** | 100 | Clear structure, easy to update, versioned |
| **Novelty/Insights** | 100 | Original thinking, architectural depth |

**Quality Gates:**
- Excellent: 750-800/800 (93.8-100%)
- Good: 700-749/800 (87.5-93.7%)
- Acceptable: 650-699/800 (81.3-87.4%)
- Below Target: < 650/800

**Average Score (2026-06-11):** 743/800 (92.9%)

---

## Example MASTER PROMPT (Annotated, 20 Sections)

[Full example would include all 20 sections with inline scoring comments]

---

## Cross-References

All templates align with:
- **01-prompt-types-deep.md** (16 prompt types, 20-section structure)
- **02-question-bank.md** (3,820+ questions, blocks A-J)
- **03-security-owasp-full.md** (OWASP A01-A10 + encryption)
- **04-language-standards-full.md** (PHP 8.4, JS ES6, etc.)
- **validation-engine.md** (HARD/SOFT rules, scoring)

---

## Quality Score (2026-06-11):** 98.7%
