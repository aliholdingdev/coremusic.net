# PROMPT-MAKER SKILL ARCHITECTURE
# Complete Reference Document System & Question-Bank Integration
# Version: 8.0.0 | 2026-06-11 | Production Ready

---

## SYSTEM ARCHITECTURE

```
┌─────────────────────────────────────────────────────────────────┐
│ PROMPT-MAKER SKILL SYSTEM                                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │ INPUT LAYER — User Project Context                       │   │
│  │ (Project name, description, scale, languages, etc.)      │   │
│  └──────────────────────┬───────────────────────────────────┘   │
│                         │                                        │
│  ┌──────────────────────▼───────────────────────────────────┐   │
│  │ QUESTION DISTRIBUTION LAYER                              │   │
│  │ (Select 15-25 questions from 830+ question bank)         │   │
│  │ Based on: Project type, language, scale, domain          │   │
│  │ References: 02-question-bank.md (master index)           │   │
│  └──────────────────────┬───────────────────────────────────┘   │
│                         │                                        │
│  ┌──────────────────────▼───────────────────────────────────┐   │
│  │ ANSWER VALIDATION LAYER                                  │   │
│  │ Validate against 27 reference documents                  │   │
│  │ Domain-specific validators using refs 03-10, 11-25       │   │
│  └──────────────────────┬───────────────────────────────────┘   │
│                         │                                        │
│  ┌──────────────────────▼───────────────────────────────────┐   │
│  │ WEB RESEARCH LAYER                                       │   │
│  │ Follow protocol from 10-web-research-protocol.md          │   │
│  │ Min 50-500 sources based on complexity                   │   │
│  │ Validate findings against references                     │   │
│  └──────────────────────┬───────────────────────────────────┘   │
│                         │                                        │
│  ┌──────────────────────▼───────────────────────────────────┐   │
│  │ CONTENT SYNTHESIS LAYER                                  │   │
│  │ Pull from 20-section template (05-output-templates.md)   │   │
│  │ Fill with domain-specific content from refs 03-25        │   │
│  │ Apply language standards (04-language-standards-full)    │   │
│  │ Apply architecture patterns (07-architecture-patterns)   │   │
│  │ Apply security rules (03-security-owasp-full)            │   │
│  └──────────────────────┬───────────────────────────────────┘   │
│                         │                                        │
│  ┌──────────────────────▼───────────────────────────────────┐   │
│  │ OUTPUT LAYER — MASTER PROMPT GENERATION                  │   │
│  │ Format: Markdown / JSON / YAML                           │   │
│  │ Size: 5000-15000 tokens                                 │   │
│  │ Sections: 20 complete sections                           │   │
│  └──────────────────────┬───────────────────────────────────┘   │
│                         │                                        │
│  ┌──────────────────────▼───────────────────────────────────┐   │
│  │ QUALITY ASSURANCE LAYER                                  │   │
│  │ Score 8 dimensions using 09-quality-scoring-rubric       │   │
│  │ Gate 1: Overall >85/100 (production-ready)               │   │
│  │ Gate 2: No section < 70/100                              │   │
│  │ Return score breakdown                                   │   │
│  └──────────────────────┬───────────────────────────────────┘   │
│                         │                                        │
│  ┌──────────────────────▼───────────────────────────────────┐   │
│  │ OUTPUT FORMATS                                           │   │
│  │ ✓ Markdown (default) → .md file                          │   │
│  │ ✓ JSON → .json file                                      │   │
│  │ ✓ YAML → .yaml file                                      │   │
│  │ ✓ HTML → displayable reference                           │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## REFERENCE DOCUMENT ECOSYSTEM

### Directory Structure

```
.claude/skills/prompt-maker/
├── prompt-maker.md                  ← Skill definition
├── ARCHITECTURE.md                  ← This file
├── REFERENCES-COMPLETION-REPORT.md  ← Deployment status
│
├── questions-block-*.md             ← 10 question blocks
│   ├── questions-block-a.md         (8 questions: Project basics)
│   ├── questions-block-b.md         (3 questions: Tech stack)
│   ├── questions-block-c.md         (15 questions: Architecture)
│   ├── questions-block-d.md         (309 questions: Security)
│   ├── questions-block-e.md         (453 questions: Performance)
│   ├── questions-block-f.md         (8 questions: UI/UX)
│   ├── questions-block-g.md         (11 questions: DevOps)
│   ├── questions-block-h.md         (6 questions: Domain)
│   ├── questions-block-ı.md         (12 questions: Prompt)
│   └── questions-block-j.md         (5 questions: Session)
│
└── references/                      ← 27 reference documents
    ├── CORE TIER (10 documents)
    │   ├── 01-prompt-types-deep.md
    │   ├── 02-question-bank.md
    │   ├── 03-security-owasp-full.md
    │   ├── 04-language-standards-full.md
    │   ├── 05-output-templates.md
    │   ├── 06-deep-domain-rules.md
    │   ├── 07-architecture-patterns.md
    │   ├── 08-full-example-sessions.md
    │   ├── 09-quality-scoring-rubric.md
    │   └── 10-web-research-protocol.md
    │
    ├── EXTENDED TIER (15 documents)
    │   ├── 11-coremusic-deep-rules.md
    │   ├── 12-performance-testing-devops.md
    │   ├── 13-uiux-accessibility.md
    │   ├── 14-embedded-audio-electronics.md
    │   ├── 15-api-design-patterns.md
    │   ├── 16-database-design-patterns.md
    │   ├── 17-prompt-engineering-deep.md
    │   ├── 18-security-deep-dive.md
    │   ├── 19-master-prompt-full-example.md
    │   ├── 20-kiro-hooks-steering-deep.md
    │   ├── 21-glossary-and-references.md
    │   ├── 22-nodejs-typescript-patterns.md
    │   ├── 23-csharp-dotnet-patterns.md
    │   ├── 24-ml-ai-patterns.md
    │   └── 25-fintech-payment-patterns.md
    │
    ├── SUPPORT TIER (2 documents)
    │   ├── validation-engine.md
    │   └── multi-agent-patterns.md
    │
    └── METADATA (1 document)
        └── INDEX-SYNC.md
```

---

## QUESTION BLOCK INTEGRATION

### Block A — Project Basics (8 questions)
**Maps to:** 01-prompt-types-deep.md, 11-coremusic-deep-rules.md

```
A01: Project name & description?
A02: Project scale? (MVP/Startup/Enterprise)
A03: Languages? (PHP, JS, Python, etc.)
A04: Project type? (Web/Backend, DevOps, Security, Multi-domain)
A05: Team size? (Solo/Startup/Enterprise)
A06: Timeline? (Weeks/Months/Quarters)
A07: Budget constraints? (Unlimited/Constrained/Per-feature)
A08: Existing codebase? (Greenfield/Brownfield)
```

### Block B — Technology Stack (3 questions)
**Maps to:** 04-language-standards-full.md, 16-database-design-patterns.md, 22-23 (Node/C#)

```
B01: Framework? (Laravel/Django/Express/Spring/etc.)
B02: Database? (MySQL/PostgreSQL/MongoDB/etc.)
B03: Additional services? (Redis, Elasticsearch, Message queue, etc.)
```

### Block C — Architecture & API (15 questions)
**Maps to:** 07-architecture-patterns.md, 15-api-design-patterns.md, 16-database-design-patterns.md

```
C01-C15: Architecture patterns, API design, deployment strategy
```

### Block D — Security & Authentication (309 questions)
**Maps to:** 03-security-owasp-full.md, 18-security-deep-dive.md

```
D01-D30: OWASP Top 10, encryption, authentication, session handling
D31-D100: XSS, CSRF, injection, access control, data protection
D101-D200: Compliance (PCI DSS, HIPAA, GDPR, CCPA)
D201-D309: Security hardening, audit logging, incident response
```

### Block E — Performance & Testing (453 questions)
**Maps to:** 12-performance-testing-devops.md

```
E01-E50: Performance targets, optimization, profiling
E51-E150: Unit testing, integration testing, E2E testing
E151-E300: Load testing, security testing, regression testing
E301-E453: DevOps practices, monitoring, observability
```

### Block F — UI/UX & Accessibility (8 questions)
**Maps to:** 13-uiux-accessibility.md

```
F01-F08: WCAG compliance, responsive design, dark mode, accessibility
```

### Block G — DevOps & Infrastructure (11 questions)
**Maps to:** 12-performance-testing-devops.md

```
G01-G11: CI/CD, containerization, deployment, monitoring
```

### Block H — Domain-Specific (6 questions)
**Maps to:** 06-deep-domain-rules.md, 14-embedded-audio-electronics.md, 24-ml-ai-patterns.md, 25-fintech-payment-patterns.md

```
H1: Embedded/Firmware? (Electronics, RTOS, real-time)
H2: Audio/DSP? (SNR, latency, algorithms)
H3: ML/AI? (Models, training, inference)
H4: Fintech/Payment? (PCI DSS, encryption)
H5: Healthcare? (HIPAA, HL7/FHIR)
H6: Other domains?
```

### Block Ι — Prompt Type & Output (12 questions)
**Maps to:** 01-prompt-types-deep.md, 05-output-templates.md, 17-prompt-engineering-deep.md, 20-kiro-hooks-steering-deep.md

```
I01-I12: Prompt type selection, output format, generation options
```

### Block J — Session & Validation (5 questions)
**Maps to:** 09-quality-scoring-rubric.md, validation-engine.md

```
J01-J05: Quality requirements, validation rules, output preferences
```

---

## REFERENCE DOCUMENT MAPPING TABLE

| Block | Coverage | Primary Reference | Secondary References | Tertiary References |
|-------|----------|------------------|----------------------|---------------------|
| **A** | Project basics | 11-coremusic | 01-prompt-types | 02-question-bank |
| **B** | Tech stack | 04-language-standards | 16-database, 22-23 | 02-question-bank |
| **C** | Architecture | 07-architecture | 15-api, 16-database | 02-question-bank |
| **D** | Security | 03-security-owasp | 18-security-deep | 02-question-bank |
| **E** | Performance | 12-perf-testing | — | 02-question-bank |
| **F** | UI/UX | 13-uiux | — | 02-question-bank |
| **G** | DevOps | 12-perf-testing | — | 02-question-bank |
| **H** | Domain | 06-deep-rules | 14-embedded, 24-ml, 25-fintech | 02-question-bank |
| **Ι** | Prompt | 01-prompt-types | 05-templates, 17-pe, 20-kiro | 02-question-bank |
| **J** | Validation | 09-quality-rubric | validation-engine | 02-question-bank |

---

## CORE 10 REFERENCE DOCUMENTS — DETAILED PURPOSE

### Tier 1: Foundation (Essential for MASTER PROMPT generation)

**01-prompt-types-deep.md**
- Defines 20+ prompt types and their use cases
- Essential for selecting correct prompt type (System, Steering, Hook, Skill, CLAUDE.md, etc.)
- Used in MASTER PROMPT section: "Prompt Type & Format"

**02-question-bank.md**
- Master index of all 830 questions across blocks A-J
- Used to determine which questions to ask user
- Maps each question to its block and answer type
- Foundation for dynamic question selection

**03-security-owasp-full.md**
- OWASP Top 10:2025 complete reference
- Code examples in PHP, JS, Python, C#
- Used in MASTER PROMPT section: "Security (OWASP)"
- Mandatory for all projects requiring security compliance

**04-language-standards-full.md**
- Per-language standards: PHP, JS, Python, C#, C++, Go, Rust, Java
- Best practices, security checklist, testing approach
- Used in MASTER PROMPT sections: "Language Standards", "Code Quality"
- Language-specific rule enforcement

**05-output-templates.md**
- 20-section MASTER PROMPT template structure
- Section purposes, content guidelines, examples
- Core template for all generated MASTER PROMPTs
- Output format variants (Markdown, JSON, YAML)

### Tier 2: Content Synthesis (For filling MASTER PROMPT sections)

**06-deep-domain-rules.md**
- Domain-specific rules: Audio/DSP, Embedded, ML/AI, Fintech, Healthcare
- Used in MASTER PROMPT section: "Domain-Specific Rules"
- Conditional inclusion based on project domain

**07-architecture-patterns.md**
- Architecture patterns, anti-patterns, layer design
- Used in MASTER PROMPT sections: "Architecture Standards", "Design Patterns"
- Foundation for system design guidance

### Tier 3: Validation & Quality Assurance

**08-full-example-sessions.md**
- 5 complete prompt-maker sessions with outputs
- Shows expected flow from answers → MASTER PROMPT
- Training/reference for system behavior
- Quality examples for calibration

**09-quality-scoring-rubric.md**
- 8-dimension quality scoring algorithm
- Evaluates completeness, consistency, production-readiness, security, etc.
- Quality gates (>85/100 minimum)
- Essential for output validation

**10-web-research-protocol.md**
- Web research requirements and validation
- Min source counts: 50-500 depending on complexity
- Anti-hallucination enforcement
- Live knowledge reconciliation rules

---

## ACTIVATION FLOW

### When User Invokes /prompt-maker

```
1. Load prompt-maker.md (skill definition)
2. Parse user input (project name, language, type, etc.)
3. Create canonical form JSON from user data
4. Consult 02-question-bank.md → Select 15-25 questions
5. Display questions dynamically (based on project type)
6. For each answer:
   a. Validate against relevant reference doc (03-07, 11-25)
   b. Store answer in context
   c. Prepare next question
7. After all questions answered:
   a. Execute web research (10-web-research-protocol.md)
   b. Synthesize MASTER PROMPT:
      - Load template (05-output-templates.md)
      - Fill section 1: Identity (01-prompt-types-deep.md)
      - Fill section 2: Experience (from user answers)
      - Fill section 3: Project context (from answers)
      - Fill section 4-5: Tech stack, objective (from answers)
      - Fill section 6: Activation triggers (01-prompt-types-deep.md)
      - Fill section 7-8: Rules, execution (from answers + 03-07)
      - Fill section 9: Language standards (04-language-standards-full.md)
      - Fill section 10: Architecture (07-architecture-patterns.md)
      - Fill section 11-12: Security, performance (03-security-owasp, 12-perf-testing)
      - Fill section 13-15: Testing, observability, error handling
      - Fill section 16-17: Code quality, git workflow
      - Fill section 18: Multi-project (if applicable)
      - Fill section 19: Domain-specific (06-deep-domain-rules.md + relevant)
      - Fill section 20: Forbidden practices (03-04, 07)
   c. Score output (09-quality-scoring-rubric.md)
   d. Return MASTER PROMPT (default: Markdown)
   e. Offer format conversion (JSON, YAML, HTML)
```

---

## CONTENT SOURCING MATRIX

When filling MASTER PROMPT sections, the skill consults:

| Section | Primary Sources | Validation Sources |
|---------|-----------------|-------------------|
| Identity & Role | 01-prompt-types | 11-coremusic |
| Experience Level | Block A answers | — |
| Project Context | Block A answers | — |
| Tech Stack | Block B answers + 04-language-standards | 16-database, 22-23 |
| Core Objective | Block A answers + project domain | 06-deep-domain-rules |
| Activation Triggers | 01-prompt-types | — |
| Execution Pipeline | Block J answers + 08-examples | validation-engine |
| Rules (Hard/Soft) | 04-language-standards + domain refs | 03-security-owasp, 07-architecture |
| Language Standards | 04-language-standards | 22-23 (if Node/C#) |
| Architecture Standards | 07-architecture | Block C answers + 15-api, 16-db |
| Security (OWASP) | 03-security-owasp | 18-security-deep |
| Performance | 12-perf-testing | Block E answers |
| Testing Strategy | 12-perf-testing | Block E answers |
| Observability | 12-perf-testing | — |
| Error Handling | 07-architecture + 10-web-research | — |
| Code Quality | 04-language-standards | 03-security-owasp |
| Git Workflow | Block J answers | — |
| Multi-Project (if applicable) | 11-coremusic + 20-kiro | — |
| Domain-Specific | 06-deep-domain-rules + H answers | 14-embedded, 24-ml, 25-fintech |
| Forbidden Practices | 04-language-standards, 03-security | 07-architecture |

---

## QUALITY ASSURANCE GATES

### Quality Scoring (09-quality-scoring-rubric.md)

```
OVERALL SCORE = (
  Completeness      × 0.15 +
  Consistency       × 0.12 +
  Production-Ready  × 0.18 +
  Security          × 0.16 +
  Scalability       × 0.12 +
  Clarity           × 0.12 +
  Depth             × 0.10 +
  Documentation     × 0.05
) / 100

QUALITY GATES:
✓ EXCELLENT: 95-100 (ship immediately)
✓ GOOD:      85-94  (production-ready with minor review)
⚠️ ACCEPTABLE: 75-84 (usable, needs revision)
❌ NEEDS WORK: 60-74 (missing sections, gaps)
❌ FAIL:      <60   (unsuitable)
```

### Validation Checks

From validation-engine.md:

```
PRE-GENERATION:
✓ All 830 questions indexed correctly
✓ All blocks A-J have questions
✓ All references synced with blocks
✓ Web research sources available

POST-GENERATION:
✓ All 20 MASTER PROMPT sections present
✓ No placeholder text remaining
✓ Security rules applied (OWASP)
✓ Language-specific standards enforced
✓ Architecture patterns appropriate
✓ No undefined terminology
✓ All examples production-grade
✓ Quality score >85/100

OUTPUT VALIDATION:
✓ Format valid (Markdown/JSON/YAML)
✓ File encoding UTF-8
✓ No line length >100 chars (unless code)
✓ All external refs valid
✓ Frontmatter complete
```

---

## EXTENSIBILITY

### Adding New Domains

When adding a new domain (e.g., Healthcare, IoT):

1. **Create Question Block**
   - Add questions to questions-block-h.md (or new block)
   - Map to question-bank.md (02-question-bank.md)

2. **Create Reference Document**
   - Create `NN-domain-name-patterns.md` in references/
   - Follow structure of existing refs (11-25)
   - Include: domain rules, patterns, checklists, examples

3. **Update Cross-References**
   - Add mapping in INDEX-SYNC.md
   - Update ARCHITECTURE.md (this file)
   - Add links in 21-glossary-and-references.md

4. **Integration Points**
   - Update prompt-maker.md (skill definition)
   - Add to 06-deep-domain-rules.md if general rules
   - Add specific handling in answer validation

### Adding New Question Types

When expanding question blocks:

1. Maintain block structure (A-J)
2. Update 02-question-bank.md index
3. Link to relevant reference docs
4. Update 08-full-example-sessions.md with new examples
5. Recalibrate quality scoring if major change

---

## PERFORMANCE & SCALING

### Current Capacity

- **Questions:** 830+ (sustainable up to 2000+)
- **Reference Documents:** 27 (sustainable up to 50+)
- **Question-Block Size:** 5-450 questions per block (uneven distribution OK)
- **Skill Execution Time:** 2-5 minutes (end-to-end with web research)
- **MASTER PROMPT Size:** 5000-15000 tokens (average 10KB file)

### Scaling Strategies

1. **Lazy-Load References:** Load only relevant docs based on project type
2. **Question Caching:** Cache selected questions between turns
3. **Research Batching:** Group web research by domain
4. **Template Preprocessing:** Pre-compile template sections
5. **Parallel Processing:** Simultaneous answer validation + web research

---

## INTEGRATION WITH KIRO IDE

### Skill Activation

Trigger keywords from prompt-maker.md:
```
prompt maker
prompt oluştur
prompt yaz
sistem promptu
MASTER PROMPT
steering yaz
hook yaz
rules yaz
CLAUDE.md yaz
```

### Output Destinations

- **Default:** Display in chat (Markdown)
- **Option 1:** Save as .md file
- **Option 2:** Save as .json file
- **Option 3:** Save as .yaml file
- **Option 4:** Export to Kiro IDE steering/
- **Option 5:** Copy to clipboard

### IDE Integration Points

- Load from `.claude/skills/prompt-maker/` directory
- References automatically available
- Question blocks loaded on demand
- Output can be saved to IDE workspace
- Quality score displayed in IDE status bar

---

## MAINTENANCE & VERSIONING

### Version Control

- **Current:** v8.0.0 (2026-06-11)
- **Core Docs:** 01-10 (last updated 2026-06-11)
- **Extended Docs:** 11-25 (last updated 2026-06-11)
- **Question Blocks:** A-J (last updated 2026-06-11)

### Update Cycle

- **Critical Updates:** As-needed (security, OWASP changes)
- **Periodic Sync:** Quarterly (reference refresh)
- **Question Review:** Annually (expansion & refinement)
- **Deprecation:** Archive old versions in `.archive/`

### Backward Compatibility

- MASTER PROMPT output format stable (20 sections)
- Reference document names immutable (01-10)
- Question IDs immutable (A01-J05+)
- New docs added with next numbers (11+)

---

## CONCLUSION

The prompt-maker skill is a comprehensive system for generating production-grade MASTER PROMPTs:

- **Input:** Project context questions (15-25 questions)
- **Processing:** Multi-layer validation, web research, synthesis
- **Output:** 20-section MASTER PROMPT (Markdown/JSON/YAML)
- **Quality:** 8-dimension scoring, >85/100 gate
- **Foundation:** 27 reference documents, 830+ questions
- **Scope:** All project types, all domains, all languages

The system is production-ready, scalable, and extensible.

---

*Prompt Maker v8.0.0 — System Architecture Complete*
*27 reference documents × 10 question blocks × 830+ questions*
*Production Ready — 2026-06-11*
