# PROMPT-MAKER QUICK REFERENCE
# Version 7.0.0 | 2026-06-11 | Developer Cheat Sheet

---

## 📋 SYSTEM AT A GLANCE

```
INPUT:   "prompt maker: [project description]"
         ↓
LOAD:    3,820 questions (blocks A-J)
         ↓
ASK:     60-80 questions (4 + 8 + batches)
         ↓
SCORE:   Category aggregation + confidence
         ↓
GENERATE: 20-section MASTER PROMPT
         ↓
OUTPUT:  Markdown + JSON + YAML
         ↓
VALIDATE: Quality check (≥700/800 to pass)
         ↓
SAVE:    .ai/wiki/prompts-kb/ + brain.md + log.md
```

---

## 🔧 THE 9 PHASES

| Phase | ADIM | Task | Duration | Input → Output |
|-------|------|------|----------|-----------------|
| 0 | ADIM 0 | Load questions | 50-200ms | block files → QuestionIndex |
| 1 | ADIM 1 | Ask identity (4Q) | 5 min | user input → canonical JSON |
| 2 | ADIM 2 | Ask context (8Q) | 10 min | filters → dynamic questions |
| 3+ | ADIM 3-4 | Ask batches (5Q×N) | 20-30 min | blocks A-J → 60-80 answered |
| 5 | ADIM 5 | Web research | 8-10 min | 45 queries → 100-200 sources |
| 6 | ADIM 6 | Score answers | 3-5 min | answers → category_scores |
| 7 | ADIM 7 | Generate prompt | 10-15 min | scores → 20 sections |
| 8 | ADIM 8 | Format output | 2-3 min | sections → MD/JSON/YAML |
| 9 | ADIM 9 | Write vault | 1-2 min | files → .ai/wiki/prompts-kb/ |
| 10 | ADIM 10 | Validate quality | 1 min | prompt → quality_score |
| 11 | ADIM 11 | Handle errors | ongoing | exceptions → fallback |

**Total Time:** 45-60 min (STANDARD mode)

---

## 💡 KEY ALGORITHMS

### Question Loading
```python
QuestionIndex = {}
for block in ['A', 'B', ..., 'J']:
  questions = parse_markdown(f'questions-block-{block}.md')
  index_questions(block, questions)
  cache(block, ttl=5min)
return QuestionIndex
```

### Dynamic Filtering
```python
selected = []
for each category:
  top_criticality = sort_by_criticality(questions_in_category)
  selected += top_criticality[:1]
  selected += random_sample(remaining, count=1-2)
return selected[:5]  # Top 5 for this batch
```

### Answer Scoring
```python
for answer in all_answers:
  question = question_index.get(answer.question_id)
  score = (
    question.criticality * 
    answer_quality(answer) * 
    completeness(answer) * 
    consistency(answer)
  )
  category = question.category
  category_scores[category].append(score)

for category in category_scores:
  avg = mean(category_scores[category])
  confidence = calculate_confidence(len(scores), avg)
```

### MASTER PROMPT Generation
```python
for section in range(1, 21):
  source_answers = get_answers_for_section(section)
  template = SECTION_TEMPLATES[section]
  reference = REFERENCE_DOCS[section_mapping[section]]
  
  content = template.render(
    answers=source_answers,
    research=research_context,
    examples=code_examples[primary_language]
  )
  sections[section] = content
```

### Quality Validation
```python
scores = {
  'completeness': score_completeness(prompt),  # 0-100
  'consistency': score_consistency(prompt),    # 0-100
  'production_ready': score_readiness(prompt), # 0-100
  'security': score_security(prompt),          # 0-100
  'scalability': score_scalability(prompt),    # 0-100
  'clarity': score_clarity(prompt),            # 0-100
  'depth': score_depth(prompt),                # 0-100
  'completeness_docs': score_docs(prompt)      # 0-100
}
total = sum(scores.values())  # Max 800

passed = total >= 700 and all(s >= 85 for s in scores.values())
```

---

## 🗂️ DATA STRUCTURES

### QuestionIndex
```json
{
  "blocks": {
    "A": {
      "categories": {
        "A1 Project Identity": [
          {
            "id": "A1.1",
            "text": "Projenin tam resmi adı?",
            "criticality": 10,
            "domain": "identity",
            "language_specific": false
          },
          ...
        ]
      }
    }
  },
  "lookup": {
    "A1.1": {...},
    "A1.2": {...},
    ...
  },
  "metadata": {
    "total_questions": 3820,
    "total_categories": 78,
    "blocks_count": 10
  }
}
```

### CategoryScores
```json
{
  "Architecture": {
    "average_score": 92,
    "total_questions": 8,
    "coverage_percent": 85,
    "confidence": "very_high"
  },
  "Security": {
    "average_score": 95,
    "total_questions": 12,
    "coverage_percent": 92,
    "confidence": "very_high"
  },
  ...
}
```

### ValidationReport
```json
{
  "passed": true,
  "total_score": 743,
  "max_score": 800,
  "percentage": 92.875,
  "by_category": {
    "completeness": 94,
    "consistency": 93,
    "production_ready": 92,
    "security": 98,
    "scalability": 88,
    "clarity": 92,
    "depth": 94,
    "completeness_docs": 90
  },
  "failures": [],
  "timestamp": "2026-06-11T14:30:00Z"
}
```

---

## 📍 20-SECTION STRUCTURE

```
Section 1-5:    Identity & Context
                1. System Identity
                2. Experience Level
                3. Project Context
                4. Technology Stack
                5. Core Objective

Section 6-8:    Rules & Execution
                6. Activation Conditions
                7. Execution Pipeline
                8. Rules Engine (Hard + Soft)

Section 9-10:   Standards
                9. Language Standards
                10. Architecture Standards

Section 11-15:  Core Systems
                11. Security Model
                12. Performance Standards
                13. UI/UX Standards (optional)
                14. Testing Strategy
                15. DevOps & Infrastructure

Section 16-19:  Infrastructure & Domain
                16. Database Design
                17. API Design
                18. Real-time Systems
                19. Domain-Specific Rules

Section 20:     Constraints
                20. Constraints & Prohibitions
```

---

## 📝 QUESTION BLOCK DISTRIBUTION

| Block | Category | Count | Focus |
|-------|----------|-------|-------|
| **A** | Project Identity | 200+ | Name, problem, vision, maturity |
| **B** | Technology Stack | 250+ | Languages, frameworks, databases |
| **C** | Architecture | 280+ | Patterns, layers, dependencies |
| **D** | Security | 300+ | OWASP, auth, encryption, compliance |
| **E** | Performance | 200+ | Targets, optimization, monitoring |
| **F** | DevOps | 250+ | CI/CD, containers, deployment |
| **G** | Database | 220+ | Schema, normalization, optimization |
| **H** | Frontend/UI | 200+ | Design systems, accessibility, responsive |
| **I** | Testing | 180+ | Unit, integration, E2E, coverage |
| **J** | Domain-Specific | 280+ | Audio, ML, Fintech, Healthcare, etc. |

---

## 🎯 FILTERING PARAMETERS

### Project Types (15)
```
Web/Backend, DevOps, Security, Mobile, Desktop,
Embedded, Audio DSP, ML/AI, Fintech, HealthTech,
E-commerce, SaaS, Multi-domain, Real-time, Blockchain
```

### Complexity Levels (4)
```
intro       → Basic concepts only
standard    → Production baseline (DEFAULT)
advanced    → Optimization, edge cases
expert      → Cutting-edge patterns
```

### Domains (78 categories across blocks)
```
Architecture (10), Security (12), Tech Stack (15),
Performance (8), DevOps (9), Database (8),
Frontend (7), Testing (8), Project Identity (12),
Domain-Specific (14 per domain type)
```

### Criticality (1-10 scale)
```
10  → System identity, security, architecture decisions
8-9 → Tech stack, compliance, core performance
6-7 → DevOps setup, testing strategy, schema design
4-5 → Features, optimization detail, nice-to-haves
2-3 → Edge cases, advanced patterns, research
```

---

## 🚀 MODE SELECTION

### STANDARD (Default)
```
Questions: 60-80
Characters: 200-300K
Sections: 20/20
Depth: Normal (code + rules)
Time: 45-60 min
Research: 100-150 sources
Quality gate: ≥700/800
Use: Most projects
```

### DEEP
```
Questions: 100-150
Characters: 400-600K
Sections: 20/20 + supplementary
Depth: MAX (all edge cases)
Time: 2-3 hours
Research: 250-400 sources
Quality gate: ≥750/800
Use: Critical projects, compliance, architecture review
```

### LITE
```
Questions: 30-40
Characters: 80-150K
Sections: 12/20 (core only)
Depth: Essential (critical items)
Time: 20-30 min
Research: 50-80 sources
Quality gate: ≥600/800
Use: MVP, quick start, proof-of-concept
```

---

## ✅ QUALITY GATES

```
Must Pass All 8 Categories:
  ✓ Completeness ≥85/100       (all 20 sections filled)
  ✓ Consistency ≥85/100         (no contradictions)
  ✓ Production Ready ≥85/100    (monitoring, logging, deployment)
  ✓ Security ≥85/100            (OWASP, auth, encryption)
  ✓ Scalability ≥85/100         (caching, load balancing)
  ✓ Clarity ≥85/100             (clear language, examples)
  ✓ Depth ≥85/100               (technical depth, word count)
  ✓ Docs Completeness ≥85/100   (references, sources)

Total Score: ≥700/800 (87.5%)
```

If any category <85 → Ask follow-up questions → Regenerate → Revalidate

---

## 📁 OUTPUT LOCATION

```
.ai/wiki/prompts-kb/
├── 2026-06-11-coremusic-master-part01.md     [Sections 1-5]
├── 2026-06-11-coremusic-master-part02.md     [Sections 6-10]
├── 2026-06-11-coremusic-master-part03.md     [Sections 11-15]
├── 2026-06-11-coremusic-master-part04.md     [Sections 16-20]
├── 2026-06-11-coremusic-master-index.md      [TOC]
├── 2026-06-11-coremusic-master.json          [JSON format]
├── 2026-06-11-coremusic-config.yaml          [YAML format]
└── index.md                                   [Index of all prompts]

Append:
├── .ai/brain.md                              [Execution summary]
└── .ai/log.md                                [Activity log]
```

---

## ⚡ IMPLEMENTATION CHECKLIST

**Kiro IDE Integration Developers:**

```
QUESTION SYSTEM:
  [ ] Load 10 question blocks (A-J)
  [ ] Index by block → category → question_id
  [ ] Implement 5-min cache
  [ ] Add criticality metadata
  
FILTERING:
  [ ] Project type filter (15 types)
  [ ] Complexity level filter (4 levels)
  [ ] Domain filter (78 categories)
  [ ] Multi-filter AND/OR logic
  [ ] Parallel batch selection (5 at a time)
  
QUESTION PRESENTER:
  [ ] Format Q1-4 (identity)
  [ ] Format Q5-8 (context)
  [ ] Format parallel batches (5 at a time)
  [ ] Handle skip ("gerekli değil")
  [ ] Track answered_questions set
  [ ] Show progress (X/Y)
  
SCORER:
  [ ] Criticality weighting
  [ ] Answer quality assessment
  [ ] Category aggregation
  [ ] Confidence levels
  [ ] Coverage calculation
  
GENERATOR:
  [ ] 20-section templates
  [ ] Answer-to-section mapping
  [ ] Reference doc loading
  [ ] Code example generation
  [ ] Section validation
  
WEB RESEARCH:
  [ ] Generate 30-50 queries
  [ ] Parallel execution (max 10)
  [ ] Deduplication & ranking
  [ ] Result caching (5-min)
  
OUTPUT:
  [ ] Markdown formatter
  [ ] JSON formatter
  [ ] YAML formatter
  [ ] Chunking at 500KB
  [ ] Index creation
  
VALIDATOR:
  [ ] 8-category scoring
  [ ] Total score calc
  [ ] Category gates
  [ ] Validation report
  
VAULT:
  [ ] Create .ai/wiki/prompts-kb/
  [ ] Write MD files
  [ ] Append brain.md
  [ ] Append log.md
  
ERROR:
  [ ] Block load fallback
  [ ] User skip handling
  [ ] Web search fallback
  [ ] Generation error recovery
  [ ] Validation failure handling
```

---

## 🔗 KEY REFERENCES

| Resource | Purpose |
|----------|---------|
| prompt-maker.md | Full specification (1,474 lines) |
| IMPLEMENTATION-GUIDE.md | Developer roadmap |
| PHASE-2-COMPLETION-SUMMARY.md | Executive summary |
| questions-block-*.md | 3,820 questions (10 files) |
| references/*.md | 25+ enrichment docs |
| QUICK-REFERENCE.md | This file |

---

## 📞 COMMON PATTERNS

### Load Questions
```python
questions = load_block('A')  # or 'B', 'C', ..., 'J'
# Returns: Dict[category, List[Question]]
```

### Filter Top Questions
```python
selected = filter_questions(
  block='B',
  complexity='standard',
  count=5,
  criticality_min=7
)
# Returns: List[Question] (top 5)
```

### Score an Answer
```python
score = score_answer(
  question_id='A1.1',
  answer='CoreMusic',
  context=user_context
)
# Returns: {score: 9.5, components: {...}}
```

### Generate Section
```python
section = generate_section(
  section_num=7,
  category_scores=scores,
  research_context=research,
  language='PHP'
)
# Returns: {number, title, content, word_count, confidence}
```

### Validate Prompt
```python
validation = validate_master_prompt(prompt_dict)
# Returns: {passed, scores, total, failures}
```

---

*Quick Reference v7.0.0 | 2026-06-11*
*Use this cheat sheet while reading prompt-maker.md*
*For details, see IMPLEMENTATION-GUIDE.md*
