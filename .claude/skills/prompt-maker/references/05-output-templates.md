---
title: OUTPUT TEMPLATES — 15-SECTION MASTER PROMPT (PICCO)
description: Prompt output formats, templates, validation checklist, scoring rubric
version: 11.0.0
updated: 2026-08-15
framework: PICCO
quality-score: "95%+"
---

# OUTPUT TEMPLATES — 15-SECTION MASTER PROMPT (PICCO)
# Prompt Maker v11.0.0 | 2026-08-15

---

## MASTER PROMPT STRUCTURE (15 SECTIONS — PICCO-ALIGNED)

| # | Section | PICCO Element | Content |
|---|---------|---------------|---------|
| 1 | Persona & Role | **P**ersona | Role, expertise, tone, personality |
| 2 | Activation Conditions | **I**nstructions | Triggers, keywords, when to activate |
| 3 | Instructions & Task | **I**nstructions | Central task, steps, requirements |
| 4 | Context & Background | **C**ontext | Project, audience, domain, exemplars |
| 5 | Hard Rules (Constraints) | **C**onstraints | Absolute prohibitions |
| 6 | Soft Rules (Guidelines) | **C**onstraints | Preferences, negotiable |
| 7 | Workflow & Process | **I**nstructions | Execution pipeline |
| 8 | Domain Rules | **C**onstraints | Domain-specific rules |
| 9 | Security Rules | **C**onstraints | OWASP, injection defense |
| 10 | Output Format | **O**utput | Structure, schema, length |
| 11 | Quality Standards | **C**onstraints | Validation criteria |
| 12 | Examples & Exemplars | **C**ontext | Few-shot patterns |
| 13 | Edge Cases | **C**ontext | Boundary conditions |
| 14 | Troubleshooting | **C**ontext | Debugging, common failures |
| 15 | Version & Approval | **O**utput | Changelog, authority |

---

## SECTION DETAILS

### Section 1: Persona & Role (PICCO: P)

- **Content:** Title, seniority level, domain expertise, years of experience
- **Format:** Table (title, level, domains, languages, platforms)
- **Example:** "Principal Software Architect — 15 years; C/C++/PHP/JS/TS; Security-focused"

### Section 2: Activation Conditions (PICCO: I)

- **Content:** When this prompt activates
- **Format:** Trigger list + keywords
- **Example:** "Activate on: 'system prompt', 'MASTER PROMPT', 'rules yaz'"

### Section 3: Instructions & Task (PICCO: I)

- **Content:** Central task, specific steps, requirements
- **Format:** Numbered list, imperative form
- **Example:** "Analyze code, suggest fixes, generate tests"

### Section 4: Context & Background (PICCO: C)

- **Content:** Project description, audience, domain, exemplars
- **Format:** Narrative + examples
- **Example:** "CoreMusic is a music player ecosystem with 7 services..."

### Section 5: Hard Rules (PICCO: C)

- **Content:** Absolute prohibitions, never-break rules
- **Format:** "NEVER" list
- **Example:** "NEVER: hardcoded secrets, SELECT *, eval(), frameworks"

### Section 6: Soft Rules (PICCO: C)

- **Content:** Preferences, negotiable guidelines
- **Format:** "PREFER" list
- **Example:** "PREFER: early returns, guard clauses, composition over inheritance"

### Section 7: Workflow & Process (PICCO: I)

- **Content:** Execution pipeline, step-by-step process
- **Format:** Numbered steps
- **Example:** "1. Load context → 2. Research → 3. Analyze → 4. Implement → 5. Test"

### Section 8: Domain Rules (PICCO: C)

- **Content:** Domain-specific constraints
- **Format:** Domain → rules mapping
- **Example:** "SPA: AbortController mandatory; Database: BCNF; Security: OWASP Top 10"

### Section 9: Security Rules (PICCO: C)

- **Content:** Security constraints, OWASP, injection defense
- **Format:** Threat categories + defenses
- **Example:** "OWASP A01-A10, CSRF, CSP, rate limiting, input validation"

### Section 10: Output Format (PICCO: O)

- **Content:** Structure, schema, length, style
- **Format:** Schema definition
- **Example:** "JSON with fields: {name, email, role, created_at}"

### Section 11: Quality Standards (PICCO: C)

- **Content:** Validation criteria, thresholds
- **Format:** Scoring rubric
- **Example:** "Completeness ≥85, Security ≥90, Clarity ≥85"

### Section 12: Examples & Exemplars (PICCO: C)

- **Content:** Few-shot examples, input/output pairs
- **Format:** Example blocks
- **Example:** "Input: X → Output: Y"

### Section 13: Edge Cases (PICCO: C)

- **Content:** Boundary conditions, what-ifs
- **Format:** Scenario list
- **Example:** "What if user is null? What if network fails?"

### Section 14: Troubleshooting (PICCO: C)

- **Content:** Common failures, debugging steps
- **Format:** Problem → Solution pairs
- **Example:** "Problem: Race condition → Fix: AbortController + activeRequests Map"

### Section 15: Version & Approval (PICCO: O)

- **Content:** Changelog, authority, signatures
- **Format:** Version table
- **Example:** "v11.0.0 | 2026-08-15 | PICCO framework integration"

---

## TEMPLATE: SYSTEM_PROMPT Output

```markdown
---
inclusion: always
priority: high
version: 11.0
framework: PICCO
language: Turkish
---

# [PROJECT_NAME] — MASTER PROMPT
# Version: X.0.0 | {DATE}
# Framework: PICCO

## 1. Persona & Role
[Who the AI is — expertise, tone, personality]

## 2. Activation Conditions
[When this prompt activates]

## 3. Instructions & Task
[What to do — central task, steps]

## 4. Context & Background
[Why it matters — project, audience]

## 5. Hard Rules
[NEVER break these]

## 6. Soft Rules
[PREFER these]

## 7. Workflow
[How to execute]

## 8. Domain Rules
[Domain-specific]

## 9. Security Rules
[OWASP, injection defense]

## 10. Output Format
[Structure, schema]

## 11. Quality Standards
[Thresholds]

## 12. Examples
[Few-shot]

## 13. Edge Cases
[Boundary conditions]

## 14. Troubleshooting
[Debugging]

## 15. Version & Approval
[Changelog]
```

---

## VALIDATION CHECKLIST (PICCO Completeness)

```
PERSONA:      [ ] Role defined  [ ] Expertise specified  [ ] Tone set
INSTRUCTIONS: [ ] Task clear    [ ] Steps listed         [ ] Requirements explicit
CONTEXT:      [ ] Background    [ ] Exemplars included   [ ] Domain specified
CONSTRAINTS:  [ ] Hard rules    [ ] Soft rules           [ ] Boundaries defined
OUTPUT:       [ ] Format        [ ] Schema               [ ] Length specified
```

---

## SCORING RUBRIC (8 Dimensions × 100 = 800 max)

| Dimension | Weight | Criteria |
|-----------|--------|----------|
| **Clarity** | 100 | Unambiguous, no jargon, actionable |
| **Completeness** | 100 | All 15 sections, no gaps |
| **Security** | 100 | OWASP, crypto, injection defense |
| **Testability** | 100 | Verifiable, edge-case aware |
| **Practicality** | 100 | Real-world examples, runnable code |
| **Consistency** | 100 | No contradictions, aligned terminology |
| **Maintainability** | 100 | Clear structure, versioned |
| **Novelty/Insights** | 100 | Original thinking, depth |

**Quality Gates:**
- Excellent: 750-800/800 (93.8-100%)
- Good: 700-749/800 (87.5-93.7%)
- Acceptable: 650-699/800 (81.3-87.4%)
- Below Target: < 650/800

---

## Cross-References

All templates align with:
- **SKILL.md** (v11.0.0 — PICCO framework)
- **17-prompt-engineering-deep.md** (2026 techniques)
- **03-security-owasp-full.md** (OWASP A01-A10)
- **04-language-standards-full.md** (PHP 8.4, JS ES6, etc.)
- **validation-engine.md** (HARD/SOFT rules, scoring)

---

*Output Templates v11.0.0 — CoreMusic PICCO Framework*
*Updated: 2026-08-15*
