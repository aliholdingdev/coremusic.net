# PROMPT-MAKER SKILL — Claude Code
# Version: 1.0.0 | 2026-06-11 | Production Ready

---

## 🎯 AKTIVASYON

Tetikleyici kelimeler:
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

---

## 📋 İŞ AKIŞI (9 ADIM) — PRODUCTION SYSTEM

### ADIM 0: SORU YÜKLEME (Background)

**Question Bank Index (78 Kategori, 3,820+ Soru)**

```yaml
Blocks: A-J (10 blok, her blok 300-450 soru)
  ├─ A: Proje Kimliği (200+ soru)
  ├─ B: Tech Stack (250+ soru)
  ├─ C: Architecture (280+ soru)
  ├─ D: Security (300+ soru)
  ├─ E: Performance (200+ soru)
  ├─ F: DevOps/Ops (250+ soru)
  ├─ G: Database (220+ soru)
  ├─ H: Frontend/UI (200+ soru)
  ├─ I: Testing/QA (180+ soru)
  └─ J: Domain-Specific (280+ soru)

Kategoriler (78 toplam):
  - Project Identity & Context (12)
  - Technology Stack (15)
  - Architecture Patterns (10)
  - Security & Compliance (12)
  - Performance & Optimization (8)
  - DevOps & Infrastructure (9)
  - Database Design (8)
  - Frontend/UI/UX (7)
  - Testing & QA (8)
  - Domain-Specific (domain type'a göre 10-15)
```

**Yükleme Algoritması:**
```
FOR each block [A-J]:
  1. Load questions-block-{letter}.md
  2. Parse structure: ###{Category} → #### {SubCategory} → Questions
  3. Index by: block → category → question_id
  4. Extract metadata: complexity, domain, criticality, language-specific
  5. Cache in memory (5-min TTL) OR disk (JSON index)
  6. On error: log, skip block, continue

Result: QuestionIndex = { blocks: {}, categories: {}, lookup: {} }
Performance: 50-200ms for full load, <10ms for cached lookup
```

**Criticality Scoring (Built-in):**
```
Each question: criticality: 1-10
  10: System identity, security, architecture decisions
  8-9: Tech stack, compliance, core performance
  6-7: DevOps setup, testing strategy, database design
  4-5: Nice-to-have features, optimization detail
  2-3: Edge case scenarios, advanced patterns

Weighted by category importance for prompt coherence.
```

---

### ADIM 1: SORU TURU 1 — Proje Temelinde (4 Soru)

```
Q1: Projenin adı ve açıklaması?
    → Load: A1.1-A1.35 (Identity questions)
    → Criticality: 10
    → Soru sayısı: 5 soru seç (top criticality + random)

Q2: Proje ölçeği? (MVP / Startup / Enterprise)
    → Load: A2.1-A2.20 (Project maturity)
    → Criticality: 9
    → Soru sayısı: 3 soru

Q3: Hangi dil/diller? (PHP, JS, Python, Rust, Go, Diğer)
    → Load: B1.1-B2.50 (Language selection questions)
    → Filter: tech_stack domain
    → Criticality: 10
    → Soru sayısı: 4 soru

Q4: Proje tipi? (Web/Backend, DevOps, Security, Audio, ML, Fintech, etc.)
    → Load: A3.1-A3.30 (Project classification)
    → Filter: match 15 project types
    → Criticality: 9
    → Soru sayısı: 3 soru

User answers → Canonical JSON created
```

**Sonuç:** Canonical Form oluştur:
```json
{
  "session_id": "uuid-v4",
  "timestamp": "2026-06-11T14:30:00Z",
  "project_name": "CoreMusic",
  "project_scale": "enterprise",
  "languages": ["PHP", "JavaScript", "TypeScript", "C++"],
  "project_types": ["Web/Backend", "Audio DSP", "Ecosystem"],
  "active_filters": {
    "complexity": "standard|advanced|expert",
    "domains": ["architecture", "security", "performance"],
    "criticality_min": 7
  },
  "answered_questions": {},
  "skipped_questions": [],
  "metadata": {}
}
```

---

### ADIM 2: DİNAMİK SORU TURU 2 — Context Filtering (8 Soru)

**Akış:**
1. Project type & languages → Load ilgili blok (B-J)
2. Complexity level sor → Filtre uygula (intro, standard, advanced, expert)
3. Domain selection sor → Filter 78 kategoriden 15-20 seç
4. Parallel 5 soru göster (block A-E'den) → User cevapla

**Web/Backend seçildiyse (B bloğundan):**
```
Q5: Framework? (Laravel, Symfony, Express, FastAPI, Spring Boot, .NET, Diğer)
    → B1.1-B1.40 (Framework selection)
    → Criticality: 9
    → 4 soru göster

Q6: Veritabanı? (MySQL, PostgreSQL, MongoDB, Redis, Elasticsearch, Diğer)
    → B2.1-B2.35 (Database primary selection)
    → Criticality: 9
    → 3 soru göster

Q7: Cache? (Redis, APCu, Memcached, Varnish, yok)
    → B3.1-B3.25 (Caching strategy)
    → Criticality: 7
    → 3 soru göster

Q8: Auth tipi? (JWT, Session, OAuth2, OIDC, API Key, Custom)
    → D1.1-D1.40 (Authentication design)
    → Criticality: 10
    → 4 soru göster
```

**Security seçildiyse (D bloğundan):**
```
Q5: Güvenlik seviyesi? (OWASP, PCI DSS, HIPAA, SOC2, Kritik)
Q6: Mevcut güvenlik önlemleri?
Q7: Son audit ne zaman?
Q8: Kritik veri türleri? (Ödeme, Sağlık, Kişisel, Biometric)
```

**DevOps seçildiyse (F bloğundan):**
```
Q5: CI/CD platform? (GitHub Actions, GitLab CI, Jenkins, Azure DevOps)
Q6: Container? (Docker, Kubernetes, serverless, yok)
Q7: Deployment ortamı? (VPS, AWS, Azure, GCP, On-prem)
Q8: Monitoring? (Prometheus, Datadog, New Relic, CloudWatch, yok)
```

**Audio/DSP seçildiyse (J3 bloğundan):**
```
Q5: Audio format desteği? (PCM, MP3, AAC, FLAC, OggVorbis, Diğer)
Q6: Real-time processing? (Yes/No + latency requirement)
Q7: Plugin support? (VST3, LV2, CLAP, AU, Diğer)
Q8: DSP expertise? (Beginner, Intermediate, Expert)
```

**ML/AI seçildiyse (J5 bloğundan):**
```
Q5: Model types? (LLM, Computer Vision, NLP, Recommender, Audio, Diğer)
Q6: Framework? (PyTorch, TensorFlow, JAX, ONNX, Diğer)
Q7: Training data volume? (Synthetic, <1GB, 1GB-1TB, 1TB+)
Q8: Production serving? (Batch, Online, Real-time, Edge)
```

---

### ADIM 3: PARALEL SORU BLOĞU — Batch 5x (Blocks A-E)

**Göster:** 5 soru at a time, user parallel cevapla

Blok A'dan: 2-3 soru (identity + context)
Blok B'den: 2 soru (tech stack)
Blok C'den: 1-2 soru (architecture)
Blok D'den: 1-2 soru (security)

**Parallelization:**
```python
# Pseudo-code
questions_batch = []
for block in ['A', 'B', 'C', 'D', 'E']:
    category = select_top_category(block, user_profile)
    top_questions = get_questions(
        block=block,
        category=category,
        count=5,
        filter_by_criticality=user_filters.criticality_min,
        filter_by_complexity=user_filters.complexity,
        exclude_answered=answered_set
    )
    questions_batch.extend(top_questions[:1])

# Present first 5, collect batch answers, repeat until user says "yeterli"
show_questions(questions_batch[0:5])
user_answers = await user_response()
questions_asked += len(user_answers)
if questions_asked >= blocks_coverage_threshold:
    break
```

**Flow:**
```
AI: "Blok A-E'den 5 soru (soruları paralel cevaplayabilirsin, gerekli değil diyebilirsin)"
[Q5] Proje mimarisi pattern nedir? (Monolithic / Modular / Microservices / Serverless)
[Q6] Scaling stratejisi? (Vertical / Horizontal / Auto-scaling / Manual)
[Q7] Cache invalidation stratejisi?
[Q8] API versioning approach?
[Q9] Şu anda hangi monitoring tool kullanıyor?

User: "5: Microservices, 6: Horizontal, 7: gerekli değil, 8: URL path, 9: Prometheus"

AI: Score answers, mark answered_questions, continue with next batch
```

---

### ADIM 4: DİNAMİK SORU KAPLAMA (Blocks F-J)

**Logic:**
```
coverage = {
  'DevOps': 0,
  'Database': 0,
  'Frontend': 0,
  'Testing': 0,
  'Domain': 0
}

While coverage < 70% AND questions_asked < 100:
  next_block = select_lowest_coverage_block()
  questions_batch = get_questions(
    block=next_block,
    criticality_min=7,  # Focus on critical questions
    count=5,
    smart_filter=user_project_type  # Domain-specific
  )
  show_questions(questions_batch)
  collect_answers()
  update_coverage()
  questions_asked += 5

Result: ~60-80 questions answered, ~70% domain coverage
```

---

### ADIM 5: WEB ARAŞTIRMASI (Paralel)

**Research Queries (50-200+ sources):**

```python
research_queries = [
  # Core research
  f"{languages.join(' ')} security 2026",
  f"OWASP Top 10:2025",
  f"{project_type} architecture best practices 2026",
  f"{security_level} compliance {year}",
  f"{frameworks.join(' ')} production deployment",
  
  # Tech-specific
  f"{primary_db} optimization benchmarks 2026",
  f"{cache_layer} eviction strategies",
  f"{primary_framework} security hardening",
  
  # Architecture
  f"microservices vs monolithic {year}",
  f"API versioning strategies 2026",
  f"database normalization {project_scale}",
  
  # Performance
  f"Lighthouse 2026 web vitals optimization",
  f"Core Web Vitals target {project_type}",
  f"memory profiling {languages[0]} 2026",
  
  # Security
  f"{security_level} compliance checklist",
  f"encryption at rest {languages[0]} 2026",
  f"authentication flow vulnerabilities",
  
  # DevOps
  f"{cicd_platform} deployment best practices",
  f"{container_tech} orchestration 2026",
  
  # Domain-specific
  f"{project_domain} engineering guidelines",
  f"{project_domain} compliance requirements",
  f"{project_domain} performance benchmarks",
]

# Execute parallelized (max 10 concurrent)
results = parallel_web_search(research_queries, max_concurrent=10)
sources = deduplicate_and_rank(results)

Min: 50 kaynak
Target: 200+ kaynak
Max: 500 kaynak
```

**Output:** `research_context = { sources: [...], key_findings: {...} }`

---

### ADIM 6: CEVAP PUANLAMA & SKOR TOPLAMASI

**Scoring Algorithm:**

```python
class AnswerScorer:
  def __init__(self, question_index, reference_docs):
    self.criticality_weights = question_index.criticality_map
    self.reference_docs = reference_docs
    
  def score_answer(self, question_id, answer, context):
    """Score single answer"""
    question = self.question_index.get(question_id)
    
    # Base criticality score
    base_score = question.criticality  # 1-10
    
    # Answer quality scoring (0-1.0)
    quality = self.assess_answer_quality(answer, question)
    
    # Completeness (partial answers = 0.5-0.8)
    completeness = self.assess_completeness(answer, question)
    
    # Consistency with context
    consistency = self.assess_consistency(answer, context)
    
    weighted_score = base_score * (quality * completeness * consistency)
    
    return {
      'score': weighted_score,
      'components': {
        'base': base_score,
        'quality': quality,
        'completeness': completeness,
        'consistency': consistency
      }
    }

  def aggregate_scores(self, all_answers):
    """Aggregate by category"""
    scores_by_category = {}
    
    for question_id, answer in all_answers.items():
      score = self.score_answer(question_id, answer, context)
      category = self.question_index.get_category(question_id)
      
      if category not in scores_by_category:
        scores_by_category[category] = []
      
      scores_by_category[category].append(score)
    
    # Calculate aggregate per category
    aggregates = {}
    for category, scores in scores_by_category.items():
      avg_score = mean([s['score'] for s in scores])
      confidence = self.calc_confidence(len(scores), avg_score)
      
      aggregates[category] = {
        'average_score': avg_score,
        'total_questions': len(scores),
        'coverage_percent': (len(scores) / self.category_total[category]) * 100,
        'confidence': confidence,  # low, medium, high, very_high
        'recommendations': self.get_category_recommendations(category, avg_score)
      }
    
    return aggregates

  def calc_confidence(self, num_questions, avg_score):
    """Determine confidence level"""
    if num_questions < 3:
      return 'low'
    elif num_questions < 6:
      return 'medium'
    elif num_questions < 10:
      return 'high'
    else:
      return 'very_high'
```

**Output:** `category_scores = { category: { score, confidence, coverage }, ... }`

---

### ADIM 7: MASTER PROMPT GENERATOR (20 Bölüm)

**20 Section Mapping:**

```
01. SYSTEM IDENTITY & ROLE
    ← Answers from: A1 (project name, description)
    ← Reference: references/01-prompt-types-deep.md
    
02. EXPERIENCE LEVEL & EXPERTISE
    ← Answers from: A2 (project maturity), B1 (languages known)
    ← Reference: references/04-language-standards-full.md
    
03. PROJECT CONTEXT & SCOPE
    ← Answers from: A1 (problem), A2 (timeline), A4 (team)
    ← Reference: CLAUDE.md project context
    
04. TECHNOLOGY STACK
    ← Answers from: B1-B5 (languages, frameworks, databases)
    ← Reference: references/22-23 (language patterns)
    
05. CORE OBJECTIVE & DELIVERABLES
    ← Answers from: A1 (UVP), A2 (timeline), A3 (scope)
    ← Reference: references/01 (types-deep)
    
06. ACTIVATION CONDITIONS & TRIGGERS
    ← Answers from: User's primary commands/keywords
    ← Reference: prompt-maker.md activation section
    
07. EXECUTION PIPELINE
    ← Answers from: C1-C3 (architecture decision), F1-F3 (workflow)
    ← Reference: references/07 (architecture-patterns)
    
08. RULES ENGINE (HARD + SOFT)
    ← Answers from: D1-D5 (security), C1-C3 (architecture)
    ← Reference: references/06 (deep-domain-rules)
    ← Hard: 20-30 mandatory rules
    ← Soft: 20-30 guideline rules
    
09. LANGUAGE STANDARDS
    ← Answers from: B1 (language choice), B2-B5 (framework details)
    ← Reference: references/04 (full language standards)
    ← Code: 5-10 language-specific code examples
    
10. ARCHITECTURE STANDARDS (L0-L3 / Layering)
    ← Answers from: C1-C3 (architecture), C4 (dependency)
    ← Reference: references/07 (architecture-patterns)
    ← Diagrams: ASCII layer diagrams, dependency graphs
    
11. SECURITY MODEL (OWASP, Compliance)
    ← Answers from: D1-D5 (security), D6 (compliance)
    ← Reference: references/03 (security-owasp-full), 18 (security-deep-dive)
    ← Threat model, auth, encryption, audit logging
    
12. PERFORMANCE STANDARDS & OPTIMIZATION
    ← Answers from: E1-E5 (performance targets)
    ← Reference: references/12 (performance-testing-devops)
    ← Targets: LCP, FCP, TTI, memory, CPU
    
13. UI/UX STANDARDS (if applicable)
    ← Answers from: H1-H7 (UI/UX requirements)
    ← Reference: references/13 (uiux-accessibility)
    ← WCAG, component design, responsive, dark mode
    
14. TESTING STRATEGY (Unit, Integration, E2E, Performance)
    ← Answers from: I1-I8 (testing approach)
    ← Reference: references/09 (quality-scoring-rubric), 12 (devops)
    ← Test pyramid, coverage targets, tools
    
15. DEVOPS & INFRASTRUCTURE
    ← Answers from: F1-F5 (DevOps, CI/CD, deployment)
    ← Reference: references/12 (performance-testing-devops)
    ← CI/CD pipeline, containers, monitoring, logging
    
16. DATABASE DESIGN & OPTIMIZATION
    ← Answers from: G1-G7 (database schema, normalization)
    ← Reference: references/16 (database-design-patterns)
    ← Schema, indexes, query patterns, backup strategy
    
17. API DESIGN & VERSIONING
    ← Answers from: B6-B8 (API), D2 (API security)
    ← Reference: references/15 (api-design-patterns)
    ← REST/GraphQL, versioning, rate limiting, documentation
    
18. REAL-TIME & ASYNC (if applicable)
    ← Answers from: C5, F4 (async patterns)
    ← Reference: references/multi-agent-patterns.md
    ← WebSocket, event streaming, message queues, state sync
    
19. DOMAIN-SPECIFIC RULES (Audio, ML, Fintech, etc.)
    ← Answers from: J1-J15 (domain questions)
    ← Reference: references/11,14,24,25 (domain-specific)
    ← Audio: SNR, THD, plugin hosting, DSP chains
    ← ML: model serving, training pipelines, inference
    ← Fintech: compliance, PCI DSS, settlement, auditing
    
20. CONSTRAINTS & PROHIBITIONS
    ← Answers from: All domains (what NOT to do)
    ← Reference: references/06 (deep-domain-rules)
    ← Hard prohibitions, anti-patterns, tech debt awareness
```

**Generation Algorithm:**

```python
class MasterPromptGenerator:
  def __init__(self, category_scores, research_context, user_answers):
    self.scores = category_scores
    self.research = research_context
    self.answers = user_answers
    self.reference_docs = load_references()
    
  def generate_master_prompt(self):
    """Generate complete 20-section MASTER PROMPT"""
    sections = {}
    
    for section_num in range(1, 21):
      section = self.generate_section(section_num)
      sections[section_num] = section
    
    # Validate completeness
    validation = self.validate_completeness(sections)
    
    return {
      'sections': sections,
      'metadata': {
        'generated_at': datetime.now(),
        'total_chars': sum(len(s['content']) for s in sections.values()),
        'section_count': 20,
        'quality_score': validation['quality_score'],
        'completeness': validation['completeness'],
        'validation_passed': validation['passed'],
      },
      'validation': validation
    }
  
  def generate_section(self, section_num):
    """Generate single section with content"""
    section_config = SECTION_MAPPING[section_num]
    
    # Collect relevant answers
    answers = self.get_answers_for_section(section_config)
    
    # Load reference document
    reference = self.reference_docs.get(section_config['reference'])
    
    # Generate content using template + answers + research
    content = self.render_section(
      template=section_config['template'],
      answers=answers,
      reference=reference,
      research=self.research,
      project_context=self.answers.get('project_context')
    )
    
    # Add code examples if applicable
    if section_config.get('include_code_examples'):
      examples = self.generate_code_examples(
        languages=self.answers['languages'],
        section_num=section_num
      )
      content += "\n\n" + examples
    
    return {
      'number': section_num,
      'title': section_config['title'],
      'content': content,
      'source_answers': answers,
      'word_count': len(content.split()),
      'confidence': self.calc_section_confidence(answers)
    }
  
  def render_section(self, template, answers, reference, research, context):
    """Render section from template"""
    # Use Jinja2 or similar
    rendered = template.render(
      answers=answers,
      reference=reference,
      research=research,
      context=context,
      date=datetime.now()
    )
    
    # Enrich with web research findings
    enriched = self.enrich_with_research(rendered, research)
    
    return enriched
  
  def generate_code_examples(self, languages, section_num):
    """Generate language-specific code examples"""
    examples = []
    
    for lang in languages:
      template = self.reference_docs['05-output-templates'].get(
        f'code_example_{section_num}_{lang}'
      )
      
      if template:
        example = template.render(context=self.answers)
        examples.append(example)
    
    return "\n\n".join(examples)
  
  def validate_completeness(self, sections):
    """Validate all 20 sections present and substantive"""
    quality_checks = {
      'sections_count': len(sections) == 20,
      'min_content_per_section': all(
        len(s['content']) > 100 for s in sections.values()
      ),
      'code_examples_present': any(
        'code' in s['content'].lower() for s in sections.values()
      ),
      'references_present': any(
        'reference' in s['content'].lower() for s in sections.values()
      ),
      'checklists_present': any(
        '[ ]' in s['content'] for s in sections.values()
      ),
      'consistency': self.check_consistency(sections),
    }
    
    passed = all(quality_checks.values())
    quality_score = sum(quality_checks.values()) / len(quality_checks) * 100
    
    return {
      'passed': passed,
      'quality_score': quality_score,
      'completeness': len(sections) / 20 * 100,
      'checks': quality_checks
    }
```

---

### ADIM 8: MASTER PROMPT OUTPUT FORMATTERS

**3 Output Formats:**

```yaml
Format 1: MARKDOWN (Default)
  File: {slug}-part{NN}.md
  Structure:
    - YAML Frontmatter (metadata)
    - 20 sections (markdown)
    - Code blocks (```language```)
    - Checklists ([ ] items)
    - ASCII diagrams
  
Format 2: JSON (Programmatic)
  File: {slug}-part{NN}.json
  Structure:
    {
      "metadata": {},
      "sections": [
        { "number": 1, "title": "...", "content": "...", "word_count": ... },
        ...
      ],
      "validation": {},
      "sources": [...]
    }
  
Format 3: YAML (Configuration)
  File: {slug}-config.yaml
  Structure:
    project:
      name: CoreMusic
      scale: enterprise
    technology_stack:
      languages: [PHP, JavaScript, C++]
      frameworks: [Vanilla JS, Laravel]
    rules_engine:
      hard_rules: [...]
      soft_rules: [...]
    quality_metrics:
      completeness: 94/100
      security: 98/100
```

**Chunking Strategy (>500KB):**

```python
def chunk_output(content_dict, max_size_kb=500):
  """Split large prompts into multiple files"""
  
  chunks = []
  current_chunk = {
    'sections': [],
    'size': 0,
    'number': 1
  }
  
  for section in content_dict['sections']:
    section_size = len(json.dumps(section).encode('utf-8')) / 1024
    
    if current_chunk['size'] + section_size > max_size_kb:
      # Save current chunk
      chunks.append(current_chunk)
      
      # Start new chunk
      current_chunk = {
        'sections': [],
        'size': 0,
        'number': len(chunks) + 1
      }
    
    current_chunk['sections'].append(section)
    current_chunk['size'] += section_size
  
  # Add final chunk
  if current_chunk['sections']:
    chunks.append(current_chunk)
  
  return chunks
```

**File Output:**
```
.ai/wiki/prompts-kb/
├── 2026-06-11-coremusic-master-part01.md  (sections 1-5, 150KB)
├── 2026-06-11-coremusic-master-part02.md  (sections 6-10, 180KB)
├── 2026-06-11-coremusic-master-part03.md  (sections 11-15, 160KB)
├── 2026-06-11-coremusic-master-part04.md  (sections 16-20, 140KB)
├── 2026-06-11-coremusic-master-index.md   (TOC + overview, 50KB)
├── 2026-06-11-coremusic-master.json       (JSON variant, 600KB)
└── 2026-06-11-coremusic-config.yaml       (Config variant, 80KB)
```

---

### ADIM 9: VAULT YAZMA & DOKUMENTASYON

**1. Dosya Adlandırması & Yazma**

```
.ai/wiki/prompts-kb/{YYYY-MM-DD}-{project-slug}-{type}-part{NN}.md

Örnek output:
├── 2026-06-11-coremusic-master-part01.md
├── 2026-06-11-coremusic-master-part02.md
├── 2026-06-11-coremusic-master-part03.md
├── 2026-06-11-coremusic-master-part04.md
├── 2026-06-11-coremusic-master-index.md (TOC)
├── 2026-06-11-coremusic-master.json
└── 2026-06-11-coremusic-config.yaml
```

**YAML Frontmatter (her .md dosyası):**
```yaml
---
type: master_prompt
version: "1.0.0"
project_name: CoreMusic
generated_at: 2026-06-11T14:30:00Z
generator: prompt-maker v7.0
language: Turkish
target_languages: [PHP, JavaScript, TypeScript, C++]
project_types: [Web Backend, Audio DSP, Ecosystem]
quality_score: 750/800
completeness: 94/100
part_number: 1
total_parts: 4
sections_included: [1, 2, 3, 4, 5]
research_sources: 127
dependencies: [references/*, CLAUDE.md, .ai/brain.md]
---
```

**2. brain.md APPEND**

```markdown
## [YYYY-MM-DD HH:mm:ss] — {PROJECT} MASTER PROMPT

### Özet
Proje: CoreMusic | Type: MASTER_PROMPT | Version: 1.0.0
Ölçek: 250K chars | Bölümler: 20/20 | Parçalar: 4

### İstatistikler
- Sorular cevaplandı: 72/3820 (~1.9%)
- Blok kapsamı: A-J (60% coverage)
- Kategori kapsamı: 68/78 (87% coverage)
- Web araştırması: 127 kaynak incelendi
- Kod örnekleri: 15 (PHP, JS, TS, C++)

### Dosyalar
- 2026-06-11-coremusic-master-part01.md (sections 01-05)
- 2026-06-11-coremusic-master-part02.md (sections 06-10)
- 2026-06-11-coremusic-master-part03.md (sections 11-15)
- 2026-06-11-coremusic-master-part04.md (sections 16-20)
- 2026-06-11-coremusic-master-index.md (TOC + overview)
- 2026-06-11-coremusic-master.json (JSON format)
- 2026-06-11-coremusic-config.yaml (Config format)

### Quality Scoring (8 Kategori × 100)
Completeness: 94/100        ✅ Tüm 20 bölüm dolu
Consistency: 93/100         ✅ Çelişki yok
Production Ready: 92/100    ✅ Deploy hazır
Security: 98/100            ✅ OWASP compliant
Scalability: 88/100         ✅ Enterprise scale
Clarity: 92/100             ✅ Net ve anlaşılır
Depth: 94/100               ✅ Teknik derinlik
Completeness_Docs: 90/100   ✅ Referans dolu

**TOTAL SCORE: 743/800 (92.9%)**

### İstatistikler
- Average section length: 2,100 words
- Code examples: 15
- Checklists: 12
- Diagrams: 8
- References: 127 sources
- Generated time: 45 minutes
- Validation: PASSED ✅

### Kategorileri Ağırlık Dağılımı
- Architecture: 15% (section 7, 10, 20)
- Security: 14% (section 11, 18, 20)
- Technology: 12% (section 4, 9, 15)
- DevOps: 10% (section 15)
- Database: 9% (section 16)
- Testing: 8% (section 14)
- UI/UX: 7% (section 13)
- Performance: 6% (section 12)
- Other: 7% (section 1-6, 8, 17, 19)

### Sonraki Adımlar
1. Master prompt'u AI agent'lara entegre et
2. Performance validation (2-3 proje üzerinde test)
3. Fine-tuning: feedback-driven improvements
4. Archive: Eski versiyonları .archive/ dizinine taşı
5. Wiki update: .ai/prompts-kb/index.md güncelle
```

**3. log.md APPEND**

```markdown
## [2026-06-11 14:30–15:15] PROMPT-MAKER EXECUTION

### Task
Generate MASTER PROMPT for CoreMusic project
- 3,820+ questions across 10 blocks (A-J)
- 78 categories intelligent filtering
- 20-section comprehensive prompt generation
- Multi-format output (MD, JSON, YAML)

### Status: COMPLETED ✅

### Phase Execution
Phase 1 (Question Loading): ✅ 50ms (10 blocks loaded, indexed)
Phase 2 (Initial Questions): ✅ 10 min (user answered 4 + 4 questions)
Phase 3 (Dynamic Filtering): ✅ 12 min (8 batches, 72 total questions)
Phase 4 (Web Research): ✅ 8 min (127 sources, parallel search)
Phase 5 (Scoring & Aggregation): ✅ 3 min (category scores computed)
Phase 6 (MASTER PROMPT Generation): ✅ 9 min (20 sections generated)
Phase 7 (Output Formatting): ✅ 2 min (markdown + JSON + YAML)
Phase 8 (Vault Writing): ✅ 1 min (files written)

### Generated Files
- .ai/wiki/prompts-kb/2026-06-11-coremusic-master-part01.md (152KB) ✅
- .ai/wiki/prompts-kb/2026-06-11-coremusic-master-part02.md (178KB) ✅
- .ai/wiki/prompts-kb/2026-06-11-coremusic-master-part03.md (164KB) ✅
- .ai/wiki/prompts-kb/2026-06-11-coremusic-master-part04.md (145KB) ✅
- .ai/wiki/prompts-kb/2026-06-11-coremusic-master-index.md (48KB) ✅
- .ai/wiki/prompts-kb/2026-06-11-coremusic-master.json (612KB) ✅
- .ai/wiki/prompts-kb/2026-06-11-coremusic-config.yaml (82KB) ✅

### Updated Files
- .ai/brain.md (append) ✅
- .ai/log.md (append) ✅ [THIS ENTRY]

### Quality Metrics
- Questions analyzed: 72 / 3,820 (1.9%)
- Categories covered: 68 / 78 (87%)
- Total characters: 639,181
- Total sections: 20 / 20 (100%)
- Code examples: 15
- Validation score: 743 / 800 (92.9%)

### Impact
- 639K chars (enterprise-grade)
- 4 parça (chunked at 500KB)
- 127 academic/vendor sources
- 20 production-ready sections
- 8/8 quality categories passed
- Ready for multi-project AI integration

### Next Actions
1. ✓ Files written to vault
2. ✓ brain.md updated
3. ✓ log.md updated
4. TODO: Test on 3+ projects for validation
5. TODO: Fine-tune section weights based on feedback
6. TODO: Archive older versions
```

---

## 📊 ADIM 10: KALİTE KONTROL & VALIDATION

**8-Category Quality Scoring (references/09-quality-scoring-rubric.md):**

```python
class QualityValidator:
  def validate_master_prompt(self, prompt_dict):
    """Validate all 8 quality dimensions"""
    
    scores = {
      'completeness': self.score_completeness(prompt_dict),      # 100
      'consistency': self.score_consistency(prompt_dict),         # 100
      'production_ready': self.score_production_readiness(prompt_dict),  # 100
      'security': self.score_security(prompt_dict),               # 100
      'scalability': self.score_scalability(prompt_dict),         # 100
      'clarity': self.score_clarity(prompt_dict),                 # 100
      'depth': self.score_depth(prompt_dict),                     # 100
      'completeness_docs': self.score_doc_completeness(prompt_dict), # 100
    }
    
    # Validation gates
    min_per_category = 85
    passed = all(score >= min_per_category for score in scores.values())
    
    return {
      'passed': passed,
      'scores': scores,
      'total': sum(scores.values()),
      'percentage': (sum(scores.values()) / 800) * 100,
      'failures': [k for k, v in scores.items() if v < min_per_category],
      'timestamp': datetime.now()
    }

  def score_completeness(self, prompt):
    """All 20 sections filled"""
    return 100 if len(prompt['sections']) == 20 else 70
  
  def score_consistency(self, prompt):
    """No contradictions, clear language"""
    contradictions = self.find_contradictions(prompt)
    return max(0, 100 - (len(contradictions) * 5))
  
  def score_production_readiness(self, prompt):
    """Ready for production use"""
    readiness_checks = {
      'has_checklist': any('[ ]' in str(s) for s in prompt['sections'].values()),
      'has_examples': any('```' in str(s) for s in prompt['sections'].values()),
      'has_error_handling': 'error' in str(prompt).lower(),
      'has_monitoring': 'monitor' in str(prompt).lower(),
      'has_deployment': 'deploy' in str(prompt).lower(),
    }
    return (sum(readiness_checks.values()) / len(readiness_checks)) * 100
  
  def score_security(self, prompt):
    """OWASP Top 10:2025 coverage"""
    owasp_topics = [
      'injection', 'broken auth', 'sensitive data', 'xml', 'broken access',
      'security misconfiguration', 'xss', 'insecure deserialization',
      'using components with known vulnerabilities', 'insufficient logging'
    ]
    covered = sum(1 for topic in owasp_topics if topic in str(prompt).lower())
    return (covered / len(owasp_topics)) * 100
  
  def score_scalability(self, prompt):
    """Enterprise-grade patterns"""
    scalability_topics = [
      'caching', 'indexing', 'sharding', 'load balancing', 'auto-scaling',
      'pagination', 'rate limiting', 'connection pooling'
    ]
    covered = sum(1 for topic in scalability_topics if topic in str(prompt).lower())
    return (covered / len(scalability_topics)) * 100
  
  def score_clarity(self, prompt):
    """Clear, actionable language"""
    vague_words = ['maybe', 'perhaps', 'might', 'could', 'probably']
    vague_count = sum(str(prompt).lower().count(word) for word in vague_words)
    return max(0, 100 - (vague_count * 2))
  
  def score_depth(self, prompt):
    """Technical depth, examples"""
    depth_signals = {
      'avg_section_words': sum(len(s.split()) for s in prompt['sections'].values()) / 20,
      'code_samples': sum(1 for s in str(prompt) if '```' in s),
      'lists': sum(1 for s in str(prompt) if '-' in s or '*' in s),
    }
    score = (depth_signals['avg_section_words'] / 1000) * 50 + \
            (depth_signals['code_samples'] / 10) * 30 + \
            (depth_signals['lists'] / 20) * 20
    return min(100, score)
  
  def score_doc_completeness(self, prompt):
    """References, sources cited"""
    references = sum(1 for s in str(prompt) if 'reference' in s.lower() or 'see:' in s.lower())
    return min(100, (references / 5) * 100)
```

**Validation Gate:**
```
IF quality_score < 700/800 (87.5%):
  ⚠️ WARNING: Prompt needs review
  - Identify lowest-scoring categories
  - Ask follow-up questions for low areas
  - Re-run MASTER PROMPT generation
  - Revalidate

IF quality_score >= 700/800:
  ✅ PASS: Ready for production
  - Mark as "Production Ready"
  - Store in vault
  - Append to brain.md
  - Archive old versions
```

---

## 🔧 MODE SEÇİMİ (Configuration)

### STANDARD MODE (Varsayılan)
```
Questions asked: 60-80 (blocks A-F)
Characters: 200-300K
Sections: 20/20 (full coverage)
Depth: Standard (kod örnekleri + kurallar)
Time: 45-60 min
Web research: 100-150 sources
Quality gate: ≥700/800
ULTRATHINK: Balanced (3-adım per section)
```

### DEEP MODE (Detaylı Analiz)
```
Questions asked: 100-150 (blocks A-J)
Characters: 400-600K
Sections: 20/20 + supplementary details
Depth: DEEP (tüm edge-case'ler, tüm örnekler, açıklamalar)
Time: 2-3 hours
Web research: 250-400 sources
Quality gate: ≥750/800
ULTRATHINK: FULL (5-adım per section: problem → research → analysis → decision → examples)
```

### LITE MODE (Hızlı Başlangıç)
```
Questions asked: 30-40 (blocks A-C)
Characters: 80-150K
Sections: 12/20 (core sections only: 1-4, 7-9, 11, 14, 18, 20)
Depth: Essential (critical items only)
Time: 20-30 min
Web research: 50-80 sources
Quality gate: ≥600/800
ULTRATHINK: Basic (2-adım per section: concept → guideline)
Use case: Quick steering, MVP projects, proof-of-concept
```

**Selection:**
```
Kullanıcı: "DEEP mode kullan" OR "standard" OR "lite"
Default: STANDARD mode
```

---

## ⚠️ ADIM 11: HATA YÖNETİMİ & FALLBACK STRATEJISI

**Graceful Degradation:**

```python
class ErrorHandler:
  def handle_question_loading_error(self, block_id, error):
    """If block loading fails"""
    logger.warning(f"Block {block_id} load failed: {error}")
    
    # Use cached version if available
    if cached_version := get_cached_block(block_id):
      return cached_version
    
    # Fall back to default questions
    return DEFAULT_QUESTIONS[block_id]
  
  def handle_user_skip(self, question_id):
    """If user skips a question (says 'gerekli değil')"""
    # Mark as skipped, not unanswered
    skipped_questions.add(question_id)
    
    # Don't penalize scoring for skipped
    # Use intelligent defaults based on similar projects
    default_answer = infer_from_similar_projects(question_id, user_profile)
    
    return {
      'answered': False,
      'skipped': True,
      'inferred_default': default_answer,
      'confidence': 'low'
    }
  
  def handle_web_search_error(self, queries, error):
    """If web search partially fails"""
    logger.error(f"Web search failed: {error}")
    
    # Use cached research results
    if cached := get_cached_research(queries):
      return cached
    
    # Fall back to reference documents
    fallback_sources = load_reference_docs()
    
    return {
      'sources': len(fallback_sources),
      'mode': 'fallback_reference_docs',
      'data': fallback_sources
    }
  
  def handle_generation_error(self, section_num, error):
    """If section generation fails"""
    logger.error(f"Section {section_num} generation failed: {error}")
    
    # Use template version with placeholder
    template = SECTION_TEMPLATES[section_num]
    
    return template.render(
      status='NEEDS_REVIEW',
      error=str(error),
      placeholder='[SECTION GENERATION FAILED - PLEASE REVIEW]'
    )
  
  def handle_validation_failure(self, scores, failures):
    """If quality validation fails"""
    logger.warning(f"Validation failed: {failures}")
    
    # Ask for clarification on lowest-scoring categories
    return {
      'status': 'FAILED_VALIDATION',
      'failures': failures,
      'lowest_scores': sorted(scores.items(), key=lambda x: x[1])[:3],
      'retry_prompt': "Şu kategorilerde eksik var. İlave sorular sorayım mı?"
    }
```

**User Interruption Handling:**

```
If user cancels mid-execution:
  - Save session state to temp file
  - Ask: "Tekrar başlamak istiyorsan, bunu kullan: [session_id]"
  - Store: .ai/sessions/{session_id}.json
  - On resume: Load state, continue from last completed phase

If user runs out of time:
  - Generate partial prompt with completed sections
  - Mark sections with "INCOMPLETE" status
  - Save progress, ask for continuation
```

---

## ⚡ HIZLI REFERANS

| Adım | Araç | Detay |
|------|------|-------|
| 0. Yükleme | QuestionIndex | 3,820 soru indexed, cached |
| 1. Q1-4 | AskUserQuestion | Temel bilgiler (identity) |
| 2. Q5-8 | AskUserQuestion | Context-spesifik (tech stack) |
| 3. Q9+ | ParallelBatch | 5 soru batches (blocks A-J) |
| 4. Araştırma | WebSearch MCP | Min 50, ideal 200+ parallelized |
| 5. Puanlama | AnswerScorer | Category aggregation + confidence |
| 6. Üretim | MasterPromptGen | 20 bölüm template render |
| 7. Format | OutputFormatter | MD + JSON + YAML output |
| 8. Yazma | VaultWriter | .ai/wiki/prompts-kb/ + chunking |
| 9. Validasyon | QualityValidator | 8 kategoride 800 puan scoring |
| 10. Hata | ErrorHandler | Graceful degradation + recovery |
| 11. Log | brain.md + log.md | Append execution summary |

---

## 📋 IMPLEMENTATION CHECKLIST (Kiro IDE Integration)

**Skill Engine Developer'lar için:**

```yaml
QUESTION SYSTEM:
  [ ] Load questions-block-{a..j}.md on startup
  [ ] Index by block → category → question_id
  [ ] Implement 5-min cache for loaded questions
  [ ] Add criticality scoring metadata
  [ ] Create lookup: question_id → criticality/domain/language

FILTERING ENGINE:
  [ ] Implement project_type filter (15 types)
  [ ] Implement complexity_level filter (intro/standard/advanced/expert)
  [ ] Implement domain filter (78 categories)
  [ ] Support AND/OR multi-filter logic
  [ ] Implement parallel batch selection (5 at a time)

QUESTION PRESENTER:
  [ ] Format Q1-4 (identity)
  [ ] Format Q5-8 (context-specific)
  [ ] Format parallel batches (5 at a time)
  [ ] Handle user skip ("gerekli değil")
  [ ] Track answered_questions set
  [ ] Show progress bar (X/Y questions)

ANSWER SCORER:
  [ ] Implement criticality weighting
  [ ] Implement answer quality assessment
  [ ] Implement category aggregation
  [ ] Calculate confidence levels (low/medium/high/very_high)
  [ ] Return: category_scores = { category: { score, confidence, coverage } }

MASTER PROMPT GENERATOR:
  [ ] Implement 20-section templates
  [ ] Map answers to sections (SECTION_MAPPING table)
  [ ] Load reference documents
  [ ] Enrich with web research findings
  [ ] Generate code examples (language-specific)
  [ ] Validate section completeness

WEB RESEARCH:
  [ ] Generate 30-50 research queries
  [ ] Execute parallel searches (max 10 concurrent)
  [ ] Deduplicate and rank sources
  [ ] Cache research results (5-min TTL)
  [ ] Return: sources_list with URLs + summaries

OUTPUT FORMATTERS:
  [ ] Markdown formatter with YAML frontmatter
  [ ] JSON formatter (nested structure)
  [ ] YAML formatter (configuration-friendly)
  [ ] Implement chunking at 500KB
  [ ] Create index.md with TOC

VAULT WRITER:
  [ ] Create .ai/wiki/prompts-kb/ directory
  [ ] Write part{NN}.md files
  [ ] Append to .ai/brain.md
  [ ] Append to .ai/log.md
  [ ] Manage file versioning

QUALITY VALIDATOR:
  [ ] Score 8 categories (completeness, consistency, etc.)
  [ ] Calculate total score (max 800)
  [ ] Validate each category ≥85/100
  [ ] Return validation report
  [ ] Gate: pass if ≥700/800 (87.5%)

ERROR HANDLING:
  [ ] Handle block loading failures (fallback to cache)
  [ ] Handle user skips (infer from defaults)
  [ ] Handle web search errors (use reference docs)
  [ ] Handle generation errors (use templates)
  [ ] Handle validation failures (ask for clarification)
  [ ] Save session state on interruption

UI/UX:
  [ ] Show progress indicators [1/9]
  [ ] Show time estimates per phase
  [ ] Show quality score in real-time
  [ ] Support "resume session" flow
  [ ] Show final summary with file list
```

---

## 🎯 BAŞLAMA KOMUTU & ACTIVATION

**User Input Variants (All activate prompt-maker):**

```
"prompt maker: CoreMusic için MASTER PROMPT yaz"
"sistem promptu: Yeni PHP projesi"
"MASTER PROMPT oluştur: Next.js SPA"
"steering yaz: Security-focused backend"
"prompt oluştur: ML pipeline optimization"
"rules yaz: Audio DSP system"
"CLAUDE.md yaz: [project description]"
"hook yaz: CI/CD automation"
```

**Full Execution Flow:**

```
User: "prompt maker: CoreMusic için MASTER PROMPT yaz"

AI Output:
╔════════════════════════════════════════════════════════════╗
║ PROMPT MAKER v7.0 — MASTER PROMPT GENERATION             ║
╚════════════════════════════════════════════════════════════╝

[PHASE 1] Soru Sistemini Yükleme...
✅ 10 blok yüklendi (A-J)
✅ 3,820 soru indexed
✅ 78 kategori hazır
✅ Yükleme: 45ms

[PHASE 2] Temel Sorular (4 soru)
Q1: Projenin adı ve açıklaması?
→ CoreMusic: Multi-domain music platform

Q2: Proje ölçeği?
→ Enterprise

Q3: Hangi dil/diller?
→ PHP 8.x, JavaScript/TypeScript, C++

Q4: Proje tipi?
→ Web/Backend + Audio DSP + Ecosystem

[PHASE 3] Context-Spesifik Sorular (8 soru)
Seçilen: Web/Backend + Audio → Q5-8 yükleniyor...

Q5: Framework?
→ Vanilla JS (SPA) + PHP custom router

Q6: Veritabanı?
→ MySQL 8.0

Q7: Cache?
→ Redis + APCu

Q8: Auth tipi?
→ Session-based + JWT (API)

[PHASE 4] Paralel Blok Sorular (5 at a time)
Batch 1 (5 soru):
[A1.5] Proje trademark korumalı mı?
[B2.3] Database replication strategy?
[C1.4] Microservices vs Monolith?
[D1.2] OWASP Top 10 coverage?
[E1.3] Performance targets (Lighthouse)?

User answers: "Evet, Replication, Hybrid, OWASP, 95+ score"

Batch 2 (5 soru):
[F1.2] CI/CD platform?
[G1.4] Database normalization?
[H1.3] Dark mode support?
[I1.2] Test coverage targets?
[J3.1] Audio format support?

User answers: "GitHub Actions, BCNF, Yes (dark+light), 85%, MP3/FLAC/OggVorbis"

... 10-15 batches (total 60-80 questions)

Progress: ████████████████░░ 72/100 questions | 72% complete

[PHASE 5] Web Araştırması (Parallelized)
Executing 45 research queries...
✅ PHP 8.x security best practices
✅ OWASP Top 10:2025 compliance
✅ Multi-domain SPA routing patterns
✅ Audio DSP circuit design
✅ MySQL optimization for music catalog
... (127 sources total)

Research complete: 127 sources | 8 min

[PHASE 6] Cevap Puanlama
Analyzing 72 answers...
✅ Category scores computed
✅ Confidence levels calculated
✅ Coverage analysis: 87% (68/78 categories)

Category Scores:
  Architecture: 92/100 (very_high confidence)
  Security: 95/100 (very_high confidence)
  Performance: 88/100 (high confidence)
  Audio DSP: 85/100 (high confidence)
  DevOps: 80/100 (medium confidence)
  Database: 82/100 (medium confidence)
  Frontend: 78/100 (medium confidence)
  API Design: 75/100 (low confidence) ⚠️

[PHASE 7] MASTER PROMPT Generation (20 bölüm)
Generating sections...
✅ Section 01: System Identity & Role (250 words)
✅ Section 02: Experience & Expertise (180 words)
✅ Section 03: Project Context (320 words)
✅ Section 04: Technology Stack (410 words)
✅ Section 05: Core Objective (290 words)
✅ Section 06: Activation Conditions (210 words)
✅ Section 07: Execution Pipeline (380 words)
✅ Section 08: Rules Engine (520 words, 28 hard + 32 soft rules)
✅ Section 09: Language Standards (450 words, 8 code examples)
✅ Section 10: Architecture Standards (520 words, 4 diagrams)
✅ Section 11: Security Model (480 words)
✅ Section 12: Performance Standards (360 words)
✅ Section 13: UI/UX Standards (310 words)
✅ Section 14: Testing Strategy (400 words)
✅ Section 15: DevOps Standards (390 words)
✅ Section 16: Database Design (420 words)
✅ Section 17: API Design (380 words)
✅ Section 18: Real-time Systems (320 words)
✅ Section 19: Domain Rules (450 words, audio-specific)
✅ Section 20: Constraints & Prohibitions (380 words)

Total: 7,820 words | 639K characters | 20/20 sections ✅

[PHASE 8] Output Formatting
📄 Markdown (with YAML frontmatter)
📊 JSON (programmatic)
⚙️ YAML (configuration)
✅ Chunking applied (500KB max)

[PHASE 9] Vault Writing
✅ .ai/wiki/prompts-kb/2026-06-11-coremusic-master-part01.md (152KB)
✅ .ai/wiki/prompts-kb/2026-06-11-coremusic-master-part02.md (178KB)
✅ .ai/wiki/prompts-kb/2026-06-11-coremusic-master-part03.md (164KB)
✅ .ai/wiki/prompts-kb/2026-06-11-coremusic-master-part04.md (145KB)
✅ .ai/wiki/prompts-kb/2026-06-11-coremusic-master-index.md (48KB)
✅ .ai/wiki/prompts-kb/2026-06-11-coremusic-master.json (612KB)
✅ .ai/wiki/prompts-kb/2026-06-11-coremusic-config.yaml (82KB)

[PHASE 10] Kwalite Validasyonu
Scoring 8 categories (max 800):
  ✅ Completeness: 94/100
  ✅ Consistency: 93/100
  ✅ Production Ready: 92/100
  ✅ Security: 98/100
  ✅ Scalability: 88/100
  ✅ Clarity: 92/100
  ✅ Depth: 94/100
  ✅ Completeness_Docs: 90/100
  
TOTAL SCORE: 743/800 (92.9%) ✅

✅ VALIDATION PASSED (≥700/800)

[PHASE 11] Vault Sync
✅ .ai/brain.md append
✅ .ai/log.md append

╔════════════════════════════════════════════════════════════╗
║ ✅ TAMAMLANDI! MASTER PROMPT PRODUCTION READY              ║
╚════════════════════════════════════════════════════════════╝

📊 İstatistikler:
- Sorular: 72 / 3,820 (1.9%)
- Kategori: 68 / 78 (87%)
- Web araştırması: 127 kaynak
- Karakter: 639K (4 parça)
- Yükleme: 127 seconds
- Kalite: 743/800 (92.9%)

📁 Dosyalar:
- .ai/wiki/prompts-kb/2026-06-11-coremusic-master-part01.md ✅
- .ai/wiki/prompts-kb/2026-06-11-coremusic-master-part02.md ✅
- .ai/wiki/prompts-kb/2026-06-11-coremusic-master-part03.md ✅
- .ai/wiki/prompts-kb/2026-06-11-coremusic-master-part04.md ✅
- .ai/wiki/prompts-kb/2026-06-11-coremusic-master-index.md ✅
- .ai/wiki/prompts-kb/2026-06-11-coremusic-master.json ✅
- .ai/wiki/prompts-kb/2026-06-11-coremusic-config.yaml ✅

🎯 Sonraki Adım:
AI agents'lara entegre et veya .claude/ altında kurala ekle!
```

---

*Status: PRODUCTION READY*
*Version: 7.0.0 (2026-06-11)*
*Deploy: Copy updated prompt-maker.md to .claude/skills/prompt-maker/*
*Integration: Kiro IDE + Cursor Rules + Claude Code CLI*
*Test: "prompt maker: [your request]"*
