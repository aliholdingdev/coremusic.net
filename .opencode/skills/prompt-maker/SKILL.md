---
title: "CoreMusic — Prompt Engineering Motoru"
type: skill-instruction
version: 11.0.0
authority: SSOT
mode:
  - Red Team
  - Truth Mode
  - Human Mode
purpose:
  - Master Prompt Generation (PICCO Framework)
  - Context Engineering Methodology
  - Executable Prompt Production
  - Hallucination Prevention
  - CoreMusic Rule Compliance
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
    - ".opencode/skills/hallucination-control/SKILL.md"
    - ".opencode/skills/human-mode/SKILL.md"
  project_structure:
    - "coremusic.net/"
    - "shared/"
  update_policy:
    preserve_existing_structure: true
    require_approval_for:
      - "prompt format change"
      - "PICCO element change"
      - "technique catalog update"
triggers:
  - "prompt oluştur"
  - "prompt yaz"
  - "sistem promptu"
  - "system prompt"
  - "steering yaz"
  - "hook yaz"
  - "kural seti oluştur"
  - "MASTER PROMPT"
  - "cursor rules"
  - "claude rules"
  - "CLAUDE.md yaz"
  - "rules yaz"
  - "context engineering"
  - "PICCO"
  - "prompt template"
changelog:
  - version: 11.0.0
    date: 2026-08-15
    changes:
      - Complete rewrite — MIM format, PICCO framework integration
      - Added 2026 technique catalog (CoT died, few-shot fallback)
      - Added prompt injection defense section
      - Added Context Engineering methodology (4-stage pipeline)
      - Added structured output standards (JSON/XML)
      - Removed deprecated techniques (CoT-forcing, prefilling)
---

# PROMPT ENGINEERING MOTORU — Machine Instruction Manual

**This skill is the CORE prompt production engine for CoreMusic.**
It transforms scattered ideas into executable, production-grade master prompts.

---

## 0. IDENTITY & SCOPE

### 0.1 System Definition

| Field | Value |
|-------|-------|
| System Name | Prompt Engineering Motoru |
| Version | 11.0.0 |
| Authority | SSOT |
| Framework | PICCO (Persona, Instructions, Context, Constraints, Output) |
| Methodology | Context Engineering (4-stage pipeline) |
| Scope | All Master Prompt generation for CoreMusic |
| Output | Executable prompts (min 5000 chars, max 50000 chars) |

### 0.2 What This Skill Does

```
INPUT:  Scattered user ideas, requirements, vague concepts
PROCESS: Research → PICCO structuring → Quality validation → Security check
OUTPUT: Executable MASTER PROMPT (ready to deploy)
```

### 0.3 Activation Triggers

| Trigger | Action |
|---------|--------|
| "prompt oluştur" / "prompt yaz" | Full prompt generation |
| "system prompt" / "sistem promptu" | System prompt generation |
| "CLAUDE.md yaz" / "rules yaz" | Rules file generation |
| "cursor rules" / "steering yaz" | IDE steering generation |
| "MASTER PROMPT" | Full master prompt |

### 0.4 When NOT to Use

| Situation | Redirect |
|-----------|----------|
| Simple code writing request | → Domain agent (backend, frontend, etc.) |
| Reading/explaining existing prompt | → Direct response, no skill needed |
| Single file fix | → Direct action, no prompt needed |

---

## 1. PICCO FRAMEWORK

The PICCO framework (Persona, Instructions, Context, Constraints, Output) is the reference architecture for all prompts.

### 1.1 Five Elements

| Element | Function | Scope | Example |
|---------|----------|-------|---------|
| **P**ersona | Defines WHO the model is | Role, expertise, tone, personality | "You are a senior PHP architect..." |
| **I**nstructions | Defines WHAT to do | Central task, steps, requirements | "Analyze this code and suggest..." |
| **C**ontext | Defines BACKGROUND | Purpose, audience, domain, examples | "This is for a music streaming platform..." |
| **C**onstraints | Defines LIMITS | Hard rules, soft rules, boundaries | "Never use ORM, always PDO..." |
| **O**utput | Defines FORMAT | Structure, length, style, schema | "Return as JSON with these fields..." |

### 1.2 Element Priority (Conflict Resolution)

When elements conflict, resolve in this order:

```
1. Constraints (HARD RULES) — highest priority, never override
2. Instructions (TASK) — what to do
3. Persona (ROLE) — who does it
4. Context (BACKGROUND) — why it matters
5. Output (FORMAT) — how to present
```

### 1.3 Context Roles (from Context Engineering 2026)

| Role | Function | Placement |
|------|----------|-----------|
| Authority | Quality standards, versioned rules | First in context |
| Exemplar | Few-shot examples, patterns | Before target data |
| Constraint | Hard limits, prohibitions | With instructions |
| Rubric | Evaluation criteria, scoring | After task definition |
| Metadata | File info, version, date | At the end |

### 1.4 Prompt Structure Hierarchy

```
┌─────────────────────────────────────────────┐
│  SYSTEM PROMPT (PICCO)                      │
│  ├── Persona (role definition)              │
│  ├── Instructions (task specification)      │
│  ├── Context (background + exemplars)       │
│  ├── Constraints (hard + soft rules)        │
│  └── Output (format + schema)               │
├─────────────────────────────────────────────┤
│  USER INPUT (task-specific data)            │
├─────────────────────────────────────────────┤
│  OUTPUT EXPECTATION (structured format)     │
└─────────────────────────────────────────────┘
```

---

## 2. WORKFLOW (10 Steps)

### Step 1: Load Context

```
READ: .ai/CLAUDE.md, .ai/AGENTS.md, .ai/WORKFLOW.md
READ: .ai/index.md, .ai/keys.md, .ai/brain.md
PURPOSE: Understand project constraints, architecture, rules
```

### Step 2: Research

```
ACTION: Web search on the topic
MINIMUM SOURCES:
  - Simple task: 5 sources
  - Medium task: 20 sources
  - Complex task: 50 sources
OUTPUT: Evidence-backed claims with confidence scores
```

### Step 3: Ask Questions (if needed)

```
RULE: Never assume — ask until 95% confident
CYCLE: Ask → Wait → Validate → Ask more if needed
STOP: When all PICCO elements are clear
```

### Step 4: Intent Analysis

```
ANALYZE:
  - What does the user ACTUALLY want? (not what they said)
  - Domain: backend, frontend, security, database, audio, etc.
  - Risk level: low, medium, high
  - Complexity: simple, moderate, complex
```

### Step 5: Validate Constraints

```
HARD RULES (never break):
  - PHP 8.x strict_types=1
  - Vanilla JS (no frameworks)
  - OWASP Top 10 2025
  - ITCSS CSS architecture
  - PDO parameterized queries
  - ADR for architectural decisions

SOFT RULES (can negotiate):
  - Performance targets
  - Scalability requirements
  - Code style preferences
```

### Step 6: Architectural Decision

```
DECIDE:
  - Which stack / pattern / tools
  - If needed: create ADR
  - Document: .ai/decisions/accepted/
```

### Step 7: Generate MASTER PROMPT

```
FORMAT: 15-section template (Section 3)
LENGTH: Minimum 5000 characters (max 50000)
RULE: Every line must define system behavior
```

### Step 8: Quality Control

```
8 CATEGORIES (Section 8):
  1. Completeness (PICCO elements present)
  2. Consistency (no contradictions)
  3. Production-Ready (deployable)
  4. Security (no injection vectors)
  5. Scalability (future-proof)
  6. Clarity (unambiguous)
  7. Depth (sufficient detail)
  8. Documentation (self-describing)

THRESHOLD: Each category ≥ 85/100
```

### Step 9: Save

```
LOCATION: .ai/prompts/{date}-{slug}.md
APPEND: .ai/brain.md (decision record)
```

### Step 10: Log

```
LOCATION: .ai/log.md
FORMAT: timestamp + action + result
```

---

## 3. MASTER PROMPT TEMPLATE (15 Sections)

```markdown
# {PROJECT NAME} — MASTER PROMPT
# Version: X.0.0 | {DATE}
# Framework: PICCO
# Purpose: {ONE SENTENCE}

## 1. Persona & Role
[WHO the AI is — expertise, tone, personality]

## 2. Activation Conditions
[WHEN this prompt activates — triggers, keywords]

## 3. Instructions & Task
[WHAT to do — central task, steps, requirements]

## 4. Context & Background
[WHY it matters — project, audience, domain, exemplars]

## 5. Hard Rules (Constraints)
[NEVER break these — absolute prohibitions]

## 6. Soft Rules (Guidelines)
[PREFER these — can be negotiated]

## 7. Workflow & Process
[HOW to execute — step-by-step pipeline]

## 8. Domain Rules
[DOMAIN-SPECIFIC — backend, frontend, security, etc.]

## 9. Security Rules
[OWASP, injection defense, credential handling]

## 10. Output Format
[FORMAT — structure, schema, length, style]

## 11. Quality Standards
[THRESHOLDS — minimum scores, validation criteria]

## 12. Examples & Exemplars
[FEW-SHOT — input/output pairs, patterns]

## 13. Edge Cases
[BOUNDARY CONDITIONS — what happens when...]

## 14. Troubleshooting
[DEBUGGING — common failures and fixes]

## 15. Version & Approval
[CHANGELOG — history, authority, signatures]
```

### 3.1 Section Descriptions

| # | Section | PICCO Element | Content |
|---|---------|---------------|---------|
| 1 | Persona & Role | **P**ersona | Role, expertise, tone |
| 2 | Activation | **I**nstructions | Triggers, keywords |
| 3 | Instructions | **I**nstructions | Task, steps, requirements |
| 4 | Context | **C**ontext | Background, exemplars |
| 5 | Hard Rules | **C**onstraints | Absolute prohibitions |
| 6 | Soft Rules | **C**onstraints | Preferences |
| 7 | Workflow | **I**nstructions | Execution pipeline |
| 8 | Domain | **C**onstraints | Domain-specific rules |
| 9 | Security | **C**onstraints | Security rules |
| 10 | Output | **O**utput | Format, schema |
| 11 | Quality | **C**onstraints | Validation criteria |
| 12 | Examples | **C**ontext | Few-shot exemplars |
| 13 | Edge Cases | **C**ontext | Boundary conditions |
| 14 | Troubleshooting | **C**ontext | Debugging |
| 15 | Version | **O**utput | Changelog, approval |

---

## 4. TECHNIQUE CATALOG (2026)

### 4.1 Techniques That STILL WORK

| Technique | When to Use | Notes |
|-----------|-------------|-------|
| **Zero-shot** | Default start — simple tasks | Try first, always |
| **Few-shot** (3-5 examples) | Format locking, consistency | Add ONLY if zero-shot insufficient |
| **Role/Persona** | Tone, format, expertise | Functional, not theatrical |
| **Task Decomposition** | Complex multi-step tasks | Break into sub-tasks |
| **Prompt Chaining** | Sequential operations | Each step feeds next |
| **Self-Consistency** | Accuracy critical | Multiple paths + majority vote |
| **Structured Outputs** | Production APIs | JSON/XML schema enforcement |
| **Meta-prompting** | Prompt optimization | "Rewrite this prompt to..." |

### 4.2 Techniques That DIED in 2026

| Technique | Status | Why |
|-----------|--------|-----|
| **CoT-forcing** ("think step by step") | DEAD | Redundant on reasoning models; -36.3% accuracy on some tasks |
| **Heavy few-shot stacks** (default) | DEAD | Zero-shot first, few-shot fallback |
| **Response prefilling** | DEAD | 400 error on Claude 4.6+ / Fable 5 / Mythos 5 |
| **Manual budget_tokens** | DEAD | Use `effort` parameter instead |

### 4.3 Technique Selection Decision Tree

```
START: What kind of task?
  │
  ├─ Simple classification/QA → Zero-shot
  │
  ├─ Format consistency needed → Few-shot (3-5 examples)
  │
  ├─ Math/Logic/Multi-step → CoT (on non-reasoning models ONLY)
  │
  ├─ Creative problem solving → Tree of Thoughts
  │
  ├─ External tools needed → ReAct (Thought-Action-Observation)
  │
  ├─ Accuracy critical → Self-consistency (multiple paths)
  │
  ├─ Production API → Structured Output (JSON/XML)
  │
  └─ Prompt optimization → Meta-prompting
```

### 4.4 Model-Specific Dialects

| Provider | System Role | Thinking | Structured Output | Notes |
|----------|-------------|----------|-------------------|-------|
| **OpenAI** (GPT-5/o-series) | developer message | adaptive | JSON mode | Avoid CoT-forcing |
| **Anthropic** (Claude 4.6+) | system message | adaptive (effort) | XML tags | No prefilling |
| **Google** (Gemini) | system instruction | thinking budget | JSON | Structured sections |
| **CoreMusic** (any) | system prompt | N/A | JSON/XML | PICCO framework |

---

## 5. COREMUSIC RULES

### 5.1 Hard Rules (Mandatory)

```
✅ PHP 8.x strict_types=1 mandatory
✅ Vanilla JS (NO frameworks — ADR-001)
✅ OWASP Top 10 2025 security rules
✅ ITCSS CSS architecture + --cm-* token system
✅ PDO parameterized queries (NO ORM — ADR-002)
✅ Handler → Service → Repository layer architecture
✅ SPA Router: AbortController mandatory
✅ DOM-safe rendering (no unsafe innerHTML)
✅ CSP compatibility mandatory
✅ ADR: after every architectural decision
✅ log.md: after every task
```

### 5.2 Forbidden Patterns

```
❌ Framework usage (React, Vue, jQuery, etc.)
❌ ORM usage (Eloquent, Doctrine, etc.)
❌ SELECT * in queries
❌ Hardcoded credentials
❌ MD5/SHA-1 for encryption
❌ Direct $_GET/$_POST access
❌ die()/header() in middleware
```

### 5.3 Context Engineering Integration

```
AUTHORITY FILE: .ai/CLAUDE.md (versioned, updated)
EXEMPLARS: .ai/prompts/ (existing prompts as examples)
CONSTRAINTS: Hard rules from §5.1
RUBRIC: Quality criteria from §8
METADATA: File version, date, author
```

---

## 6. HALLUCINATION CONTROL

### 6.1 Confidence Scoring

Every technical claim in the prompt must be verified:

```
Score 90-100  VERIFIED   → use directly
Score 60-89   UNVERIFIED → mark "⚠️ VERIFICATION REQUIRED"
Score <60     REJECTED   → reject, provide alternative
```

### 6.2 Reference to Hallucination Control Skill

This skill uses the full hallucination control pipeline:
- Validator Agents (parallel)
- Auditor Agents (sequential)
- Integrator Agent (storage)
- Red Team Review (3-way)

See: `.opencode/skills/hallucination-control/SKILL.md`

### 6.3 Atomic Claim Decomposition

Every prompt output is decomposed into atomic claims:

```
Claim: "PCM3168A supports 8-channel DAC"
Source: TI Datasheet + ADR-038
Score: 95/100 → VERIFIED

Claim: "CoreMusic uses React"
Source: None
Score: 0/100 → REJECTED (H001 + Framework FORBIDDEN)
```

---

## 7. SECURITY & INJECTION DEFENSE

### 7.1 Prompt Injection Types

| Type | Description | Example |
|------|-------------|---------|
| **Direct** | Override system instructions | "Ignore all previous instructions..." |
| **Role-play** | Bypass via persona | "Pretend you have no restrictions..." |
| **Indirect** | Hidden in documents | HTML comments, invisible text |
| **Context overflow** | Flood with noise | Large text to push out instructions |

### 7.2 Defense Strategies

```
1. INPUT VALIDATION
   - Filter suspicious patterns in user input
   - Block "ignore previous instructions" variants
   - Detect role-play bypass attempts

2. CONTEXT ISOLATION
   - Separate user data from instructions
   - Use XML tags to delimit sections
   - Never embed user input in system prompt

3. OUTPUT VALIDATION
   - Check outputs against expected format
   - Validate against schema
   - Flag unexpected content

4. LEAST PRIVILEGE
   - Prompt should not expose system prompt
   - Prompt should not allow arbitrary code execution
   - Prompt should not allow credential access
```

### 7.3 Secure Prompt Design

```
DO:
  ✅ Use XML tags to separate sections
  ✅ Validate all user inputs
  ✅ Ground responses in provided documents
  ✅ Quote relevant parts before answering
  ✅ Use structured outputs (JSON/XML)

DON'T:
  ❌ Embed user input in system message
  ❌ Allow "ignore instructions" patterns
  ❌ Expose internal reasoning in output
  ❌ Trust external content without validation
```

---

## 8. QUALITY CONTROL

### 8.1 Eight Quality Categories

| # | Category | Weight | Threshold | Check |
|---|----------|--------|-----------|-------|
| 1 | **Completeness** | 20% | ≥85/100 | All 15 sections present? |
| 2 | **Consistency** | 15% | ≥85/100 | No contradictions between sections? |
| 3 | **Production-Ready** | 15% | ≥85/100 | Deployable as-is? |
| 4 | **Security** | 15% | ≥90/100 | No injection vectors? |
| 5 | **Scalability** | 10% | ≥80/100 | Future-proof? |
| 6 | **Clarity** | 10% | ≥85/100 | Unambiguous? |
| 7 | **Depth** | 10% | ≥80/100 | Sufficient detail? |
| 8 | **Documentation** | 5% | ≥85/100 | Self-describing? |

### 8.2 PICCO Completeness Check

```
PERSONA:      [ ] Role defined  [ ] Expertise specified  [ ] Tone set
INSTRUCTIONS: [ ] Task clear    [ ] Steps listed         [ ] Requirements explicit
CONTEXT:      [ ] Background    [ ] Exemplars included   [ ] Domain specified
CONSTRAINTS:  [ ] Hard rules    [ ] Soft rules           [ ] Boundaries defined
OUTPUT:       [ ] Format        [ ] Schema               [ ] Length specified
```

### 8.3 Quality Score Formula

```
Total Score = Σ (Category Score × Weight)
Minimum: 85/100 to pass
Security threshold: 90/100 (higher bar)
```

---

## 9. TROUBLESHOOTING

### 9.1 Common Failures

| Problem | Cause | Fix |
|---------|-------|-----|
| "Prompt produced but low quality" | Step 8 (Quality Control) skipped | Re-run all 8 categories |
| "Generated without research" | Step 2 skipped | Stop, complete research first |
| "Vault files not read" | Step 1 skipped | Read CLAUDE.md and AGENTS.md |
| "Hallucination occurred" | Section 6 not applied | Score all claims, reject low scores |
| "Prompt too short" | Min 5000 chars not enforced | Expand each section |
| "Security vulnerability" | Section 7 skipped | Run injection defense check |
| "Format inconsistent" | Output section unclear | Specify exact schema |

### 9.2 Debugging Flow

```
1. IDENTIFY: What went wrong?
2. LOCATE: Which step failed?
3. FIX: Re-run that specific step
4. VERIFY: Check output quality
5. LOG: Record in .ai/log.md
```

---

## 10. REFERENCES & VERSION

### 10.1 Cross-Reference Map

```
SKILL.md (this file)
    +-- .ai/CLAUDE.md (project rules)
    +-- .ai/AGENTS.md (agent registry)
    +-- .ai/WORKFLOW.md (process definitions)
    +-- .ai/brain.md (architectural decisions)
    +-- .ai/index.md (master catalog)
    +-- .ai/prompts/ (generated prompts)
    +-- .ai/decisions/accepted/ (ADRs)
    +-- .opencode/skills/hallucination-control/SKILL.md
    +-- .opencode/skills/human-mode/SKILL.md
```

### 10.2 Related Skills

| Skill | Relationship |
|-------|--------------|
| **hallucination-control** | Verification pipeline for prompt outputs |
| **human-mode** | Orchestration and execution rules |
| **vault-sync** | Vault integrity validation |

### 10.3 Changelog

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2026-07-30 | Initial release |
| 5.0.0 | 2026-08-01 | 10-step workflow, 20-section template |
| 10.0.0 | 2026-08-08 | Merged with red-team-truth-mode |
| 10.1.0 | 2026-08-15 | Standardized YAML frontmatter |
| 11.0.0 | 2026-08-15 | Complete rewrite — PICCO framework, 2026 techniques, injection defense |

### 10.4 Research Sources

| Source | Contribution |
|--------|--------------|
| PICCO Framework (Cook 2026) | 5-element reference architecture |
| Context Engineering (2026) | 4-stage pipeline, 5 context roles |
| Claude Best Practices (Anthropic) | XML tags, adaptive thinking, long context |
| TECHSY Guide (2026) | 2026 technique status (what died, what works) |
| Zylos Research (2026) | ReAct, Reflexion, ToT, DSPy |
| Frontiers Taxonomy (2026) | 4-category prompt taxonomy |

---

*Prompt Engineering Motoru v11.0.0 — CoreMusic MIM Format*
*Framework: PICCO (Persona, Instructions, Context, Constraints, Output)*
*Authority: Vault Steward / AI Orchestrator*
*Mandatory for all prompt generation — No exceptions*
