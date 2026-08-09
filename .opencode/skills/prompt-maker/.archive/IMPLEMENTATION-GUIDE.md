# PROMPT-MAKER SKILL — Implementation Guide
# Version 7.0.0 | 2026-06-11 | Production System Integration

---

## 📋 OVERVIEW

**What:** Complete system integration for prompt-maker skill with 3,820+ questions (blocks A-J), dynamic filtering, intelligent scoring, and 20-section MASTER PROMPT generation.

**Scope:** 
- 10 question blocks (A-J) × 300-450 questions each = 3,820+ total
- 78 intelligent categories with domain mapping
- 25+ reference documents for enrichment
- 8-category quality scoring (max 800 points)
- 3 output formats (Markdown, JSON, YAML)

**Status:** Phase 2 Complete - Full Skill Definition Ready
- ✅ Updated prompt-maker.md (1,474 lines)
- ✅ System architecture documented
- ✅ Algorithms specified (pseudo-code)
- ✅ Integration checklist provided
- ✅ Error handling designed
- ✅ Full workflow example included

---

## 🏗️ SYSTEM ARCHITECTURE (9-Phase Flow)

### Phase 0: Question Loading (Background)
- Load questions-block-{a..j}.md files
- Index by: block → category → question_id
- Cache in memory or disk (JSON)
- Performance target: 50-200ms full load, <10ms cached lookup

### Phase 1-4: Question Collection (Identity + Context)
- **Phase 1 (4Q):** Project identity, scale, languages, type
- **Phase 2 (8Q):** Context-specific (tech stack, security, devops, etc.)
- **Phase 3+:** Parallel batches of 5 questions
- **Total:** 60-80 questions answered (user can skip)

### Phase 5: Web Research (Parallelized)
- Generate 30-50 research queries based on context
- Execute parallel searches (max 10 concurrent)
- Target: 100-200 sources (can scale to 500)
- Cache results (5-min TTL)

### Phase 6: Answer Scoring & Aggregation
- Score each answer by criticality (1-10 weight)
- Aggregate by category (78 categories)
- Calculate confidence levels (low/medium/high/very_high)
- Output: category_scores with coverage metrics

### Phase 7: MASTER PROMPT Generation (20 Sections)
- Section mapping to answer categories
- Template rendering with user answers
- Enrich with web research findings
- Generate language-specific code examples

### Phase 8: Output Formatting
- Markdown with YAML frontmatter (default)
- JSON for programmatic use
- YAML for configuration
- Auto-chunk at 500KB

### Phase 9-10: Quality Validation & Vault Writing
- Validate 8 quality dimensions (completeness, security, clarity, etc.)
- Gate: Pass if ≥700/800 (87.5%)
- Write to .ai/wiki/prompts-kb/
- Append to .ai/brain.md and .ai/log.md

### Phase 11: Error Handling & Recovery
- Graceful degradation for failures
- Fallback to cached/default values
- Session persistence on interrupt
- User recovery flow

---

## 📊 20-Section MASTER PROMPT Mapping

| # | Section | Source | Reference |
|---|---------|--------|-----------|
| 01 | System Identity & Role | A1 (project name) | 01-prompt-types-deep |
| 02 | Experience Level | A2 + B1 (maturity) | 04-language-standards |
| 03 | Project Context | A1, A2, A4 (scope) | CLAUDE.md |
| 04 | Technology Stack | B1-B5 (tech) | 22-23 (patterns) |
| 05 | Core Objective | A1 (UVP) | 01-types-deep |
| 06 | Activation Conditions | User input keywords | prompt-maker.md |
| 07 | Execution Pipeline | C1-C3 + F1-F3 | 07-architecture |
| 08 | Rules Engine (H+S) | D1-D5 + C1-C3 | 06-domain-rules |
| 09 | Language Standards | B1 + B2-B5 | 04-language |
| 10 | Architecture Standards | C1-C3 + C4 | 07-patterns |
| 11 | Security Model (OWASP) | D1-D5 + D6 | 03,18 (security) |
| 12 | Performance Standards | E1-E5 | 12-testing-devops |
| 13 | UI/UX Standards | H1-H7 (if applicable) | 13-uiux |
| 14 | Testing Strategy | I1-I8 | 09,12 (testing) |
| 15 | DevOps & Infrastructure | F1-F5 | 12-devops |
| 16 | Database Design | G1-G7 | 16-database |
| 17 | API Design | B6-B8 + D2 | 15-api |
| 18 | Real-time Systems | C5 + F4 (if applicable) | multi-agent |
| 19 | Domain-Specific Rules | J1-J15 (domain) | 11,14,24,25 |
| 20 | Constraints & Prohibitions | All domains | 06-domain |

**Code Examples by Language:**
- 5-10 examples per section (if applicable)
- Language-specific based on user answer (PHP, JS, TS, Python, C++, C#, Go, Rust)
- Template source: references/05-output-templates.md

---

## 🎯 FILTERING & WEIGHTING LOGIC

### Project Type Filtering (15 types)
```
Web/Backend, DevOps, Security, Mobile, Desktop, Embedded, 
Audio DSP, ML/AI, Fintech, HealthTech, E-commerce, SaaS, 
Multi-domain, Real-time, Blockchain
```

### Complexity Levels
```
intro (basic concepts)
standard (production baseline)
advanced (optimization, edge cases)
expert (cutting-edge patterns)
```

### Domain Categories (78 total)
```
Project Identity (12), Tech Stack (15), Architecture (10),
Security (12), Performance (8), DevOps (9), Database (8),
Frontend (7), Testing (8), Domain-Specific (14 per domain)
```

### Criticality Weighting (1-10)
```
10: System identity, security, architecture decisions
8-9: Tech stack, compliance, core performance
6-7: DevOps setup, testing strategy
4-5: Features, optimization detail
2-3: Edge cases, advanced patterns
```

---

## 🔧 IMPLEMENTATION COMPONENTS

### Component 1: Question Index
**File:** questions-block-{a..j}.md
**Data Structure:**
```python
QuestionIndex = {
  'blocks': {
    'A': {
      'categories': {
        'A1 Project Identity': [
          {'id': 'A1.1', 'text': '...', 'criticality': 10, 'domain': 'identity'},
          ...
        ],
        ...
      }
    },
    ...
  },
  'lookup': {  # Fast O(1) access
    'A1.1': {...},
    ...
  },
  'metadata': {
    'total_questions': 3820,
    'total_categories': 78,
    'blocks_count': 10
  }
}
```

### Component 2: Question Loader
**Responsibility:** Load, parse, index, cache questions
**Input:** Block ID (A-J) or None (all)
**Output:** QuestionIndex with cached metadata
**Performance:** <200ms first load, <10ms cached

### Component 3: Dynamic Filter
**Responsibility:** Smart question selection based on context
**Input:** user_profile, filters, answered_set, complexity_level
**Output:** List of 5 questions (ranked by criticality)
**Logic:** Top criticality + random sampling + avoid duplicates

### Component 4: Answer Scorer
**Responsibility:** Score answers, aggregate by category
**Input:** all_answers, question_index, user_context
**Output:** category_scores = {category: {score, confidence, coverage}}
**Scoring:** base_criticality × quality × completeness × consistency

### Component 5: Master Prompt Generator
**Responsibility:** Render 20 sections from answers + research
**Input:** category_scores, research_context, reference_docs, user_answers
**Output:** sections = {1-20: {title, content, word_count, confidence}}
**Templates:** Jinja2-style with answer placeholders

### Component 6: Web Research Engine
**Responsibility:** Generate + execute parallel searches
**Input:** languages, project_type, security_level, frameworks, etc.
**Output:** sources_list with URLs, summaries, deduplicated
**Performance:** 30-50 queries, max 10 concurrent, <300 sources target

### Component 7: Output Formatter
**Responsibility:** Format output in multiple formats
**Input:** sections (dict), metadata
**Output:** (markdown_text, json_obj, yaml_str)
**Chunking:** Split at 500KB, create index.md

### Component 8: Quality Validator
**Responsibility:** Validate prompt quality (8 categories × 100)
**Input:** master_prompt_dict
**Output:** validation_report = {passed, scores, total, failures}
**Gate:** Pass if ≥700/800, each category ≥85/100

### Component 9: Error Handler
**Responsibility:** Graceful degradation, fallback, recovery
**Input:** exception, context
**Output:** fallback_value OR user_prompt (ask for help)
**Strategies:** 
- Block load fail → use cache/defaults
- User skip → infer from similar projects
- Web search fail → use reference docs
- Generation fail → use template with [NEEDS REVIEW]

---

## 📈 QUALITY SCORING (8 × 100 = 800 max)

```
1. Completeness (100)
   ✓ All 20 sections filled
   ✓ Min content per section
   ✓ Code examples present
   
2. Consistency (100)
   ✓ No contradictions
   ✓ Clear language
   ✓ Terminology aligned
   
3. Production Ready (100)
   ✓ Checklists present
   ✓ Monitoring/logging covered
   ✓ Deployment documented
   ✓ Error handling specified
   
4. Security (100)
   ✓ OWASP Top 10:2025 coverage
   ✓ Auth/crypto discussed
   ✓ Compliance mentioned
   ✓ Audit logging specified
   
5. Scalability (100)
   ✓ Caching strategies
   ✓ Indexing recommendations
   ✓ Load balancing discussed
   ✓ Data partitioning covered
   
6. Clarity (100)
   ✓ No vague language
   ✓ Examples provided
   ✓ Acronyms explained
   ✓ Links to docs
   
7. Depth (100)
   ✓ Technical depth (avg section length)
   ✓ Code samples
   ✓ Diagrams/lists
   ✓ Edge cases covered
   
8. Completeness_Docs (100)
   ✓ References cited
   ✓ Sources linked
   ✓ Tools mentioned
   ✓ Further reading
```

**Validation Gate:** ≥700/800 (87.5%) = PRODUCTION READY

---

## 🚀 DEPLOYMENT & INTEGRATION

### For Kiro IDE:
1. Copy updated `prompt-maker.md` to `.claude/skills/prompt-maker/`
2. CLI tool developers: Reference IMPLEMENTATION CHECKLIST in prompt-maker.md
3. Register skill activation keywords
4. Point to question blocks (relative paths OK)
5. Set up output directory: `.ai/wiki/prompts-kb/`

### For Claude Code:
1. Update skill manifest in Cursor rules
2. Add activation triggers to CLAUDE.md
3. Test: `prompt maker: [test request]`
4. Verify output files in `.ai/wiki/prompts-kb/`

### For Multi-Project Use:
1. Each project gets unique session_id
2. Session saved as: `.ai/sessions/{YYYY-MM-DD}/{session-id}.json`
3. Resume capability: `prompt maker resume: [session_id]`
4. Version tracking: part01.md, part02.md, etc.

---

## ✅ TESTING & VALIDATION CHECKLIST

**Unit Tests (Component Level):**
- [ ] Question loader: all 10 blocks load correctly
- [ ] Question filter: top criticality selected
- [ ] Answer scorer: weighting calculations correct
- [ ] Master prompt generator: all 20 sections render
- [ ] Output formatter: valid markdown/JSON/YAML
- [ ] Quality validator: gate logic correct

**Integration Tests (End-to-End):**
- [ ] Full flow: question → answer → prompt (STANDARD mode)
- [ ] Deep flow: 100+ questions (DEEP mode)
- [ ] Lite flow: 30-40 questions (LITE mode)
- [ ] Web research: parallel search works
- [ ] Vault writing: files created, appended correctly
- [ ] Error recovery: graceful degradation works

**Production Tests (Real Projects):**
- [ ] Test on 3+ real projects
- [ ] Validate quality score ≥700/800
- [ ] Review generated sections for accuracy
- [ ] Test on slow network (research resilience)
- [ ] Test interruption/resume flow
- [ ] Measure execution time vs estimates

---

## 📚 REFERENCE INTEGRATION

**Built-in Enrichment Sources (references/*.md):**
```
01-prompt-types-deep.md         → Section 1, 5, 6
02-question-bank.md             → Question data
03-security-owasp-full.md       → Section 11, 20
04-language-standards-full.md   → Section 9
05-output-templates.md          → Code examples
06-deep-domain-rules.md         → Section 8, 20
07-architecture-patterns.md     → Section 7, 10
08-full-example-sessions.md     → Examples
09-quality-scoring-rubric.md    → Validation
10-web-research-protocol.md     → Phase 5
11-coremusic-deep-rules.md      → Domain rules
12-performance-testing-devops.md → Section 12, 15
13-uiux-accessibility.md        → Section 13
14-embedded-audio-electronics.md → Audio domain
15-api-design-patterns.md       → Section 17
16-database-design-patterns.md  → Section 16
17-prompt-engineering-deep.md   → Meta patterns
18-security-deep-dive.md        → Section 11, 20
19-master-prompt-full-example.md → Template
20-kiro-hooks-steering-deep.md  → Integration
21-glossary-and-references.md   → Terminology
22-nodejs-typescript-patterns.md → Code examples
23-csharp-dotnet-patterns.md    → Code examples
24-ml-ai-patterns.md            → Domain-specific
25-fintech-payment-patterns.md  → Domain-specific
```

**Auto-Loaded Based on Context:**
- User selects PHP → Load 04-language-standards (PHP section)
- User selects security → Load 03-security-owasp
- User selects audio DSP → Load 14-embedded-audio
- User selects ML → Load 24-ml-ai-patterns
- Etc.

---

## 📝 EXAMPLE: Full Execution Summary

**Project:** CoreMusic
**Mode:** STANDARD
**Date:** 2026-06-11
**Duration:** 127 seconds

```
[QUESTIONS]
- Answered: 72 / 3,820 (1.9%)
- Blocks covered: A-E (primary) + F-J (supplementary)
- Categories: 68 / 78 (87%)
- Skip rate: 8% (user chose "gerekli değil")

[RESEARCH]
- Web queries: 45
- Sources: 127 (deduplicated)
- Languages: PHP, JavaScript, C++, MySQL, Redis

[SCORING]
- Average category score: 87/100
- Lowest: API Design (75)
- Highest: Architecture (92), Security (95)
- Confidence: high → very_high

[GENERATION]
- Sections: 20 / 20 ✅
- Words: 7,820
- Characters: 639,181
- Code examples: 15
- Diagrams: 4

[VALIDATION]
- Quality score: 743 / 800 (92.9%) ✅
- PASSED: All 8 categories ≥85/100 ✅

[OUTPUT]
- Markdown: 4 parts (part01-04.md)
- JSON: 1 file (master.json)
- YAML: 1 file (config.yaml)
- Index: 1 file (master-index.md)
- Total: 7 files, 1.1 MB

[VAULT]
- Written to: .ai/wiki/prompts-kb/
- brain.md: appended ✅
- log.md: appended ✅
```

---

## 🔄 NEXT STEPS (For Developers)

1. **Read prompt-maker.md:** Full skill definition (1,474 lines)
2. **Implement Components:** 9 components in checklist order
3. **Load Question Blocks:** C:\www\coremusic.net\.claude\skills\prompt-maker\questions-block-{a..j}.md
4. **Load References:** C:\www\coremusic.net\.claude\skills\prompt-maker\references\*.md
5. **Test Incrementally:** Unit tests → integration → production
6. **Deploy to IDE:** Copy to .claude/skills/prompt-maker/
7. **Validate:** Test on 3+ real projects
8. **Gather Feedback:** Iterate on quality scoring weights

---

## 📞 SUPPORT

**Questions in prompt-maker.md:**
- IMPLEMENTATION CHECKLIST (detailed)
- Phase-by-phase breakdown
- Algorithm pseudo-code
- Error handling strategies
- Full execution example

**Estimated Development Time:** 80-120 hours (for complete CLI tool)
**Complexity:** High (system design, parallel processing, validation)
**ROI:** Enables automated, high-quality prompt generation for any project

---

*Implementation Guide v7.0.0 | 2026-06-11*
*Status: Ready for development integration*
*Contact: [Maintainer]*
