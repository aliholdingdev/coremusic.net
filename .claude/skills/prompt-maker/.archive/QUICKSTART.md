# PROMPT-MAKER REFERENCE GUIDE — QUICK START
# How to Use the 27 Reference Documents
# Version: 8.0.0 | 2026-06-11

---

## 🚀 WHAT YOU NEED TO KNOW

### The System at a Glance

**Goal:** Generate production-grade MASTER PROMPTs (20-section AI system prompts)

**Input:** Answer 15-25 questions about your project
**Process:** Validate answers → Research → Synthesize → Score
**Output:** MASTER PROMPT (Markdown, JSON, or YAML)
**Quality:** 8-dimension scoring (target >85/100)

### Core Files You Need

| File | Purpose | When to Use |
|------|---------|-----------|
| `prompt-maker.md` | Skill definition & flow | First read this |
| `references/02-question-bank.md` | Question index | To understand all 830 questions |
| `references/05-output-templates.md` | MASTER PROMPT template | To see output structure |
| `references/09-quality-scoring-rubric.md` | Quality scoring | To understand what makes a good prompt |
| `ARCHITECTURE.md` | System overview | To understand how pieces fit together |

---

## 📋 QUICK WORKFLOW

### Step 1: Invoke the Skill

```bash
/prompt-maker
# or
prompt maker
# or
MASTER PROMPT
```

### Step 2: Answer Project Questions

The skill asks 15-25 questions based on your project type. Answer honestly:

```
Q1: What's your project name?
→ MyMusicApp

Q2: What's the project scale?
→ Enterprise (500 users/month, significant budget)

Q3: Which language(s)?
→ PHP 8.4 + JavaScript

Q4: What type of project?
→ Web/Backend with Database
```

### Step 3: Skill Validates & Researches

Behind the scenes, the skill:
- Validates your answers (refs 03-07, 11-25)
- Researches latest best practices (ref 10)
- Prepares content for synthesis

### Step 4: Receive Your MASTER PROMPT

```markdown
# MASTER PROMPT — MyMusicApp AI Assistant

## 1. SYSTEM IDENTITY & ROLE
You are an AI assistant specialized for MyMusicApp...

## 2. EXPERIENCE LEVEL
Senior software architect (10+ years)...

... (18 more sections) ...

## 20. FORBIDDEN PRACTICES
- `var` in JavaScript (use const/let)
- Unserialize user input in PHP
- Raw SQL concatenation
...
```

### Step 5: Use Your MASTER PROMPT

Copy the MASTER PROMPT and use it as a system prompt when:
- Creating a project-specific Claude Code session
- Onboarding new team members
- Starting new AI-assisted development tasks
- Setting up project guidelines

---

## 🎯 REFERENCE DOCUMENT QUICK LOOKUP

### By Question Block

**Q: I'm answering questions from Block A (Project Basics)**
→ Read: `01-prompt-types-deep.md`, `11-coremusic-deep-rules.md`

**Q: I'm answering questions from Block B (Tech Stack)**
→ Read: `04-language-standards-full.md`, `16-database-design-patterns.md`

**Q: I'm answering questions from Block C (Architecture)**
→ Read: `07-architecture-patterns.md`, `15-api-design-patterns.md`

**Q: I'm answering questions from Block D (Security)**
→ Read: `03-security-owasp-full.md`, `18-security-deep-dive.md`

**Q: I'm answering questions from Block E (Performance)**
→ Read: `12-performance-testing-devops.md`

**Q: I'm answering questions from Block F (UI/UX)**
→ Read: `13-uiux-accessibility.md`

**Q: I'm answering questions from Block G (DevOps)**
→ Read: `12-performance-testing-devops.md`

**Q: I'm answering questions from Block H (Domain-Specific)**
→ Read: `06-deep-domain-rules.md` + domain-specific refs (14, 24, 25)

**Q: I'm answering questions from Block Ι (Prompt Type)**
→ Read: `01-prompt-types-deep.md`, `05-output-templates.md`

**Q: I'm answering questions from Block J (Validation)**
→ Read: `09-quality-scoring-rubric.md`, `validation-engine.md`

### By Project Type

**Building a Web/REST API backend?**
1. Read: `04-language-standards-full.md` (your language)
2. Read: `07-architecture-patterns.md` (REST/microservice)
3. Read: `03-security-owasp-full.md` (security essentials)
4. Read: `15-api-design-patterns.md` (API design)
5. Read: `16-database-design-patterns.md` (database)

**Building a Frontend SPA?**
1. Read: `04-language-standards-full.md` (JavaScript)
2. Read: `13-uiux-accessibility.md` (UI/UX/WCAG)
3. Read: `07-architecture-patterns.md` (SPA patterns)
4. Read: `03-security-owasp-full.md` (frontend security: XSS, CSRF)

**Building an ML/AI system?**
1. Read: `06-deep-domain-rules.md` § ML/AI rules
2. Read: `24-ml-ai-patterns.md` (ML-specific patterns)
3. Read: `04-language-standards-full.md` (Python)
4. Read: `12-performance-testing-devops.md` (model deployment)

**Building embedded/firmware?**
1. Read: `06-deep-domain-rules.md` § Embedded rules
2. Read: `14-embedded-audio-electronics.md` (hardware specifics)
3. Read: `04-language-standards-full.md` (C/C++)
4. Read: `12-performance-testing-devops.md` (testing)

**Building fintech/payment system?**
1. Read: `06-deep-domain-rules.md` § Fintech rules
2. Read: `25-fintech-payment-patterns.md` (payment specifics)
3. Read: `03-security-owasp-full.md` (security CRITICAL)
4. Read: `12-performance-testing-devops.md` (monitoring/audit)

**Building healthcare system?**
1. Read: `06-deep-domain-rules.md` § Healthcare rules
2. Read: `03-security-owasp-full.md` (security CRITICAL)
3. Read: `12-performance-testing-devops.md` (audit logging)
4. Read: `04-language-standards-full.md` (your language)

**Building a multi-service ecosystem?**
1. Read: `11-coremusic-deep-rules.md` (CoreMusic patterns)
2. Read: `20-kiro-hooks-steering-deep.md` (orchestration)
3. Read: `07-architecture-patterns.md` (microservices)
4. Read: `12-performance-testing-devops.md` (deployment)

### By Language

**PHP Developer?**
1. `04-language-standards-full.md` § PHP 8.x
2. `03-security-owasp-full.md` (PHP examples)
3. `16-database-design-patterns.md` (MySQL/PostgreSQL)

**JavaScript/TypeScript Developer?**
1. `04-language-standards-full.md` § JavaScript/TypeScript
2. `03-security-owasp-full.md` (JS examples)
3. `13-uiux-accessibility.md` (frontend specifics)
4. `22-nodejs-typescript-patterns.md` (if Node.js)

**Python Developer?**
1. `04-language-standards-full.md` § Python
2. `24-ml-ai-patterns.md` (if ML/AI)
3. `03-security-owasp-full.md` (Python examples)

**C# / .NET Developer?**
1. `04-language-standards-full.md` § C#
2. `23-csharp-dotnet-patterns.md` (.NET specifics)
3. `03-security-owasp-full.md` (C# examples)

**C / C++ Developer?**
1. `04-language-standards-full.md` § C/C++
2. `14-embedded-audio-electronics.md` (if embedded/audio)
3. `06-deep-domain-rules.md` § relevant domain

### By Concern

**"What are the security best practices?"**
→ Read: `03-security-owasp-full.md` (complete OWASP framework)

**"What architecture pattern should I use?"**
→ Read: `07-architecture-patterns.md` (monolith, microservice, SPA, etc.)

**"How should I test this?"**
→ Read: `12-performance-testing-devops.md` (unit, integration, E2E, security)

**"What are the performance targets?"**
→ Read: `12-performance-testing-devops.md` (Lighthouse, Core Web Vitals, latency)

**"How do I design my API?"**
→ Read: `15-api-design-patterns.md` (REST, GraphQL, versioning, auth)

**"How should I structure my database?"**
→ Read: `16-database-design-patterns.md` (normalization, indexing, security)

**"What's the MASTER PROMPT format?"**
→ Read: `05-output-templates.md` (20-section template)

**"How is the MASTER PROMPT scored?"**
→ Read: `09-quality-scoring-rubric.md` (8 dimensions, scoring algorithm)

**"How is web research validated?"**
→ Read: `10-web-research-protocol.md` (research requirements, anti-hallucination)

---

## 📊 DOCUMENT SIZES & READING TIME

| Document | Size | Read Time | Priority |
|----------|------|-----------|----------|
| 01-prompt-types-deep.md | 10KB | 10 min | Medium |
| 02-question-bank.md | 20KB | 15 min | High (ref only) |
| 03-security-owasp-full.md | 15KB | 20 min | High (if security critical) |
| 04-language-standards-full.md | 16KB | 20 min | High (reference) |
| 05-output-templates.md | 8KB | 8 min | Medium |
| 06-deep-domain-rules.md | 25KB | 25 min | Medium (if domain-specific) |
| 07-architecture-patterns.md | 16KB | 20 min | High (if designing) |
| 08-full-example-sessions.md | 18KB | 20 min | Low (examples only) |
| 09-quality-scoring-rubric.md | 10KB | 10 min | Medium (at end) |
| 10-web-research-protocol.md | 11KB | 10 min | Low (automatic) |
| 11-coremusic-deep-rules.md | 16KB | 15 min | Low (CoreMusic only) |
| 12-performance-testing-devops.md | 18KB | 20 min | High (if performance critical) |
| 13-uiux-accessibility.md | 13KB | 15 min | High (if frontend) |
| 14-embedded-audio-electronics.md | 13KB | 15 min | High (if embedded/audio) |
| 15-api-design-patterns.md | 17KB | 20 min | High (if building API) |
| 16-database-design-patterns.md | 15KB | 18 min | High (if database design) |
| 17-prompt-engineering-deep.md | 14KB | 15 min | Low (reference) |
| 18-security-deep-dive.md | 21KB | 25 min | High (if security critical) |
| 19-master-prompt-full-example.md | 17KB | 15 min | Low (example) |
| 20-kiro-hooks-steering-deep.md | 17KB | 15 min | Low (if using Kiro) |
| 21-glossary-and-references.md | 13KB | 10 min | Low (reference) |
| 22-nodejs-typescript-patterns.md | 13KB | 12 min | High (if Node.js) |
| 23-csharp-dotnet-patterns.md | 15KB | 15 min | High (if C#/.NET) |
| 24-ml-ai-patterns.md | 14KB | 15 min | High (if ML/AI) |
| 25-fintech-payment-patterns.md | 17KB | 18 min | High (if fintech) |

**Total Reference Material:** ~355 KB, ~280-320 minutes (5-6 hours) to read all
**Essential Reading:** ~80 KB, ~40-60 minutes (1 hour) for most projects

---

## 🎓 STUDY PATHS

### Path 1: Web Backend Developer (PHP + MySQL)
**Time: 90 minutes**

1. Read `ARCHITECTURE.md` (15 min) — Overview
2. Read `04-language-standards-full.md` § PHP (15 min)
3. Read `16-database-design-patterns.md` (20 min)
4. Read `07-architecture-patterns.md` § Web (15 min)
5. Read `03-security-owasp-full.md` (first half, 20 min)
6. Skim `12-performance-testing-devops.md` (10 min)

### Path 2: Frontend SPA Developer (JavaScript/React)
**Time: 80 minutes**

1. Read `ARCHITECTURE.md` (15 min)
2. Read `04-language-standards-full.md` § JavaScript (15 min)
3. Read `13-uiux-accessibility.md` (15 min)
4. Read `03-security-owasp-full.md` (XSS/CSRF sections, 15 min)
5. Skim `07-architecture-patterns.md` § SPA (10 min)
6. Skim `12-performance-testing-devops.md` (5 min)

### Path 3: Security Engineer
**Time: 150 minutes**

1. Read `ARCHITECTURE.md` (15 min)
2. Read `03-security-owasp-full.md` (complete, 30 min)
3. Read `18-security-deep-dive.md` (25 min)
4. Read `04-language-standards-full.md` (language-specific sections, 20 min)
5. Read `10-web-research-protocol.md` (10 min)
6. Skim domain-specific refs (14, 24, 25 if relevant, 20 min)

### Path 4: DevOps/SRE
**Time: 100 minutes**

1. Read `ARCHITECTURE.md` (15 min)
2. Read `12-performance-testing-devops.md` (complete, 25 min)
3. Read `07-architecture-patterns.md` (20 min)
4. Skim `06-deep-domain-rules.md` (10 min)
5. Skim `20-kiro-hooks-steering-deep.md` (15 min)
6. Skim relevant docs (22-25 if applicable, 15 min)

### Path 5: ML/AI Engineer
**Time: 110 minutes**

1. Read `ARCHITECTURE.md` (15 min)
2. Read `24-ml-ai-patterns.md` (15 min)
3. Read `06-deep-domain-rules.md` § ML/AI (15 min)
4. Read `04-language-standards-full.md` § Python (10 min)
5. Read `12-performance-testing-devops.md` (20 min)
6. Read `03-security-owasp-full.md` (basics, 15 min)
7. Skim `10-web-research-protocol.md` (5 min)

---

## 💡 TIPS FOR SUCCESS

### 1. Start with ARCHITECTURE.md
Get the 30,000-foot view first. Understand how the system works.

### 2. Find Your Primary Reference
Identify which 1-3 reference docs are most relevant to your project type.

### 3. Keep a Bookmark
Bookmark the high-value references for your domain:
- Web backend: 04, 07, 16, 03, 12
- Frontend: 04, 13, 03, 07
- Security: 03, 18, 10
- DevOps: 12, 07, 06

### 4. Use as Checklist
Print the relevant reference and use as a development checklist.

### 5. Reference During Answer
When answering skill questions, have the relevant reference open.

### 6. Validate Your MASTER PROMPT
After generating MASTER PROMPT, score it using `09-quality-scoring-rubric.md`.

### 7. Update Over Time
As your project evolves, revisit references and update your MASTER PROMPT.

---

## ❓ FAQ

**Q: Do I need to read all 27 documents?**
A: No. Read only what's relevant to your project type. Most projects need 3-5 references.

**Q: What if I can't find what I'm looking for?**
A: Check `21-glossary-and-references.md` for term definitions and cross-references.

**Q: How often should I update my MASTER PROMPT?**
A: After major architecture changes, language upgrades, or security updates. Quarterly review recommended.

**Q: Can I share my MASTER PROMPT with others?**
A: Yes! Share it with your team as a onboarding document or project standard.

**Q: What if the references are outdated?**
A: The skill validates against latest research. Report outdated references via INDEX-SYNC.md.

**Q: How long does MASTER PROMPT generation take?**
A: 2-5 minutes total (including web research). Web research is automatic.

**Q: What quality score should I target?**
A: Minimum 85/100 (production-ready). Target 92+/100 for critical systems.

**Q: Can I customize the MASTER PROMPT after generation?**
A: Yes. It's your document. Edit any section as needed. Quality score is just a guide.

**Q: Which reference document is most important?**
A: `02-question-bank.md` (master index) + domain-specific refs (your language, architecture, security).

---

## 🔗 QUICK LINKS

| Need | Read This |
|------|-----------|
| System overview | ARCHITECTURE.md |
| Question index | references/02-question-bank.md |
| MASTER PROMPT template | references/05-output-templates.md |
| Quality scoring | references/09-quality-scoring-rubric.md |
| All 830 questions | references/02-question-bank.md |
| Language-specific rules | references/04-language-standards-full.md |
| Architecture patterns | references/07-architecture-patterns.md |
| Security framework | references/03-security-owasp-full.md |
| Testing & DevOps | references/12-performance-testing-devops.md |
| Synchronization status | references/INDEX-SYNC.md |

---

## 🎯 NEXT STEPS

1. **Invoke the skill:** `/prompt-maker` or `MASTER PROMPT`
2. **Answer the questions:** Be honest about your project
3. **Wait for synthesis:** The skill validates, researches, and generates
4. **Review output:** Check quality score and all 20 sections
5. **Use it:** Copy-paste as system prompt for your Claude Code sessions
6. **Share it:** Distribute to your team as project standard
7. **Update it:** Review and update quarterly or after major changes

---

*Prompt Maker v8.0.0 — Quick Start Guide*
*27 references × 830 questions × 20-section MASTER PROMPT output*
*Production Ready — 2026-06-11*
