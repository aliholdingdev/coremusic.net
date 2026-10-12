# BLOK I — PROMPT TİPİ & ÇIKTI (180+ Soru)

## KATEGORI I1: OUTPUT FORMAT (40 Soru)

```
I1. Prompt tipi?
    A) SYSTEM_PROMPT (temel AI davranışı)
    B) STEERING (Kiro steering dosyası)
    C) HOOK (Kiro hook aksiyonu)
    D) SKILL (Kiro skill)
    E) MASTER_PROMPT (20 bölüm)
    F) SECURITY (güvenlik özel)
    G) API (API security)
    H) DEVOPS (DevOps özel)
    I) TESTING (test strategy)
    J) DATABASE (database rules)
    K) ARCHITECTURE (mimari)
    L) PERFORMANCE (performans)
    M) MOBILE (mobil app)
    N) EMBEDDED (gömülü sistem)
    O) ML/AI (makine öğrenmesi)
    P) DATA_ENGINEERING (data pipeline)
    Q) INFRASTRUCTURE (infra as code)
    R) DOCUMENTATION (docs standard)
    S) CODE_REVIEW (code review rules)
    T) COMPLIANCE (uyum/standart)

I1a. Output dosya türü?
     A) Single markdown (.md)
     B) Multi-file markdown (folder)
     C) JSON (.json)
     D) YAML (.yaml)
     E) TOML (.toml)
     F) XML (.xml)
     G) Code files (.js, .php, .py)
     H) Mixed formats

I1b. Output dosya adlandırması?
     A) Standard pattern (name-description.md)
     B) Date-based (2026-06-11-*.md)
     C) Version-based (v1.0-*.md)
     D) Project-based (project-name-*.md)
     E) Custom naming

I1c. File organization?
     A) Single file
     B) Modular (5-10 files)
     C) Hierarchical (nested folders)
     D) Flat structure
     E) Hybrid approach

I1d. Markdown flavor?
     A) GitHub Flavored Markdown (GFM)
     B) CommonMark
     C) Pandoc
     D) Custom with frontmatter
     E) Plain markdown

I1e. Frontmatter metadata?
     A) Full YAML frontmatter
     B) Simple key-value
     C) No frontmatter
     D) Custom format

I1f. Code block styling?
     A) Language-specific syntax highlighting
     B) Triple backticks only
     C) Indented code blocks
     D) Fenced with language tags

I1g. Table formatting?
     A) Markdown tables (GFM)
     B) ASCII tables
     C) No tables
     D) Data tables + narrative

I1h. Link format?
     A) Markdown links [text](url)
     B) Obsidian wiki links [[path]]
     C) Reference-style links
     D) Plain URLs

I1i. List formatting?
     A) Numbered lists (ordered)
     B) Bullet points (unordered)
     C) Definition lists
     D) Mixed styles

I1j. Heading hierarchy?
     A) H1-H6 (full range)
     B) H2-H4 (limited)
     C) Custom headings
     D) Flat structure

I1k. Code example format?
     A) Inline code snippets
     B) Standalone code blocks
     C) Complete working examples
     D) Pseudo-code

I1l. Visual diagrams?
     A) Mermaid diagrams
     B) ASCII art diagrams
     C) PlantUML
     D) SVG references
     E) No diagrams

I1m. Callout boxes?
     A) Blockquotes with emoji
     B) Custom alert syntax
     C) No special formatting
     D) Styled boxes (GitHub alerts)

I1n. Cross-references?
     A) Hyperlinks to sections
     B) File-to-file references
     C) None
     D) External resource links

I1o. Output validation?
     A) Markdown linting (markdownlint)
     B) Link validation
     C) No validation
     D) Manual review only
```

## KATEGORI I2: SCOPE DEFINITION (35 Soru)

```
I2. AI sistem hedefi?
    A) Claude (current)
    B) OpenAI (GPT-4/4-turbo)
    C) Google Gemini (Pro)
    D) Anthropic (other)
    E) Kiro IDE (internal)
    F) Local LLM (Llama/Mistral)
    G) Custom/Unknown
    H) Multi-model (fallback strategy)

I2a. Model sürümü?
     A) Latest stable
     B) Specific version (which?)
     C) Multiple versions
     D) No preference

I2b. Model capabilities?
     A) Text only
     B) Vision (images)
     C) Code execution
     D) Function calling
     E) All capabilities
     F) Mixed

I2c. Token limit?
     A) Short context (4K)
     B) Medium context (8K)
     C) Long context (32K+)
     D) Ultra-long (100K+)
     E) Unknown

I2d. Scope - Proje boyutu?
    A) Component (tek bileşen)
    B) Module (kod modülü)
    C) Microservice (tek hizmet)
    D) System (tüm sistem)
    E) Monolith (bir project)
    F) Multi-project (7+ proje)
    G) Organization (kurumsal)
    H) Enterprise (global)

I2e. Scope - Kod satır sayısı?
     A) Small (< 1K LOC)
     B) Medium (1K-10K LOC)
     C) Large (10K-100K LOC)
     D) Very large (100K+ LOC)
     E) Unknown

I2f. Scope - Team size?
     A) Solo developer
     B) Small team (2-5)
     C) Medium team (6-20)
     D) Large team (20+)
     E) Distributed team

I2g. Scope - Tech stack?
     A) Monolithic (single tech)
     B) Polyglot (multiple languages)
     C) Microservices (multi-tech)
     D) Serverless (FaaS)
     E) Complex ecosystem

I2h. Scope - Time horizon?
     A) Immediate (< 1 week)
     B) Short-term (1-4 weeks)
     C) Medium-term (1-3 months)
     D) Long-term (3-12 months)
     E) Ongoing/perpetual

I2i. Scope - Domain knowledge?
     A) Assumed (deep knowledge)
     B) Beginner-friendly
     C) Mixed audience
     D) Specialized domain
     E) General audience

I2j. Scope - Regulatory?
     A) None
     B) GDPR only
     C) HIPAA/SOC2
     D) PCI-DSS
     E) Multiple compliance
     F) Custom requirements

I2k. Scope - Data sensitivity?
     A) Public data
     B) Internal only
     C) Personal data (PII)
     D) Sensitive (HIPAA/classified)
     E) Mixed sensitivity

I2l. Scope - Geographic?
     A) Single region
     B) Multi-region
     C) Global
     D) No geographic constraints

I2m. Scope - Performance SLA?
     A) Best effort
     B) 99.9% uptime
     C) 99.99% uptime
     D) 99.999% uptime
     E) Custom SLA

I2n. Scope - Disaster recovery?
     A) None needed
     B) Backup + restore
     C) RPO/RTO targets
     D) Multi-region failover
     E) Custom DR strategy

I2o. Scope - Integration breadth?
     A) Standalone
     B) Few integrations (2-5)
     C) Many integrations (5-20)
     D) Extensive integrations (20+)
     E) Open ecosystem

I2p. Scope - Data volume?
     A) Small (< 1GB)
     B) Medium (1GB-1TB)
     C) Large (1TB-100TB)
     D) Very large (100TB+)
     E) Streaming/realtime

I2q. Scope - User base?
     A) < 100 users
     B) 100-1K users
     C) 1K-100K users
     D) 100K-1M users
     E) 1M+ users

I2r. Scope - Maintenance burden?
     A) Low (< 5% time)
     B) Medium (5-20% time)
     C) High (20-50% time)
     D) Very high (50%+ time)
     E) Unknown
```

## KATEGORI I3: MASTER PROMPT SECTIONS (45 Soru)

```
I3. Master prompt bölümleri?
    A) All 20 sections (complete)
    B) Core sections only (5-10)
    C) Custom selection
    D) Minimal (2-3 key sections)
    E) Extended (20+ sections)

I3a. Bölüm 1: Identity & Role?
     A) Comprehensive persona (500+ words)
     B) Brief role definition
     C) Omit this section
     D) Custom identity

I3b. Bölüm 2: Core Execution Rules?
     A) Detailed rules (20+)
     B) Key rules only (5)
     C) No execution rules
     D) Project-specific rules

I3c. Bölüm 3: Communication Style?
     A) Formal/corporate
     B) Casual/friendly
     C) Technical/precise
     D) No style guide

I3d. Bölüm 4: Architecture Standards?
     A) SOLID + DDD
     B) Clean Architecture
     C) Layered Architecture
     D) No standards
     E) Custom approach

I3e. Bölüm 5: Security Standards?
     A) OWASP Top 10
     B) Custom security rules
     C) Industry standards (ISO, etc.)
     D) No security focus

I3f. Bölüm 6: Code Quality Rules?
     A) Linting + formatting
     B) Design patterns
     C) Refactoring rules
     D) No quality rules

I3g. Bölüm 7: Testing Strategy?
     A) Unit + Integration + E2E
     B) TDD approach
     C) Coverage targets
     D) No testing rules

I3h. Bölüm 8: Documentation Standards?
     A) Code comments + README
     B) Architecture Decision Records
     C) API documentation
     D) No documentation

I3i. Bölüm 9: Performance Targets?
     A) Latency targets (< 100ms)
     B) Throughput targets
     C) Memory/CPU constraints
     D) No performance targets

I3j. Bölüm 10: Error Handling?
     A) Comprehensive strategy
     B) Recovery patterns
     C) Logging strategy
     D) No error handling

I3k. Bölüm 11: Dependency Management?
     A) Version pinning rules
     B) Vulnerability scanning
     C) Upgrade strategy
     D) No dependency rules

I3l. Bölüm 12: Configuration Management?
     A) Environment variables
     B) Config files pattern
     C) Secrets management
     D) No config rules

I3m. Bölüm 13: Deployment Strategy?
     A) CI/CD pipelines
     B) Blue-green deployment
     C) Canary releases
     D) No deployment rules

I3n. Bölüm 14: Monitoring & Observability?
     A) Logging strategy
     B) Metrics collection
     C) Tracing/debugging
     D) No monitoring rules

I3o. Bölüm 15: Incident Response?
     A) On-call procedures
     B) Escalation paths
     C) Post-mortems
     D) No incident rules

I3p. Bölüm 16: Team Collaboration?
     A) Code review standards
     B) Pull request guidelines
     C) Communication norms
     D) No collaboration rules

I3q. Bölüm 17: Learning & Development?
     A) Documentation culture
     B) Knowledge sharing
     C) Onboarding guide
     D) No learning rules

I3r. Bölüm 18: Legal & Compliance?
     A) License compliance
     B) Data privacy (GDPR)
     C) Accessibility (WCAG)
     D) No compliance rules

I3s. Bölüm 19: Technical Debt Management?
     A) Refactoring schedule
     B) Debt tracking
     C) Priority guidelines
     D) No debt management

I3t. Bölüm 20: Tools & Infrastructure?
     A) Development tools list
     B) CI/CD tools
     C) Monitoring tools
     D) No tool guidance

I3u. Bölüm 21: Language-Specific Rules?
     A) PHP + JavaScript
     B) Python + Go
     C) Java + C++
     D) All major languages
     E) No language-specific rules

I3v. Bölüm 22: Framework-Specific Rules?
     A) Laravel + React
     B) Django + FastAPI
     C) Spring + Hibernate
     D) Multiple frameworks
     E) No framework rules

I3w. Bölüm 23: Database Rules?
     A) SQL best practices
     B) NoSQL patterns
     C) Migration strategy
     D) No database rules

I3x. Bölüm 24: API Design Rules?
     A) RESTful conventions
     B) GraphQL patterns
     C) gRPC guidelines
     D) No API rules

I3y. Bölüm 25: Frontend Rules?
     A) Accessibility (WCAG)
     B) Performance (Core Web Vitals)
     C) UX best practices
     D) No frontend rules

I3z. Bölüm 26: Infrastructure Rules?
     A) Cloud provider specifics
     B) Container orchestration
     C) IaC standards
     D) No infra rules

I3aa. Bölüm 27: AI/ML Rules?
      A) Model governance
      B) Data quality standards
      C) Bias detection
      D) No AI/ML rules

I3ab. Bölüm 28: Security Checklist?
      A) Pre-deployment checklist
      B) Vulnerability scanning
      C) Penetration testing
      D) No security checklist

I3ac. Bölüm 29: Performance Checklist?
      A) Load testing results
      B) Resource utilization
      C) Bottleneck analysis
      D) No performance checklist

I3ad. Bölüm 30: Troubleshooting Guide?
      A) Common issues + fixes
      B) Log interpretation
      C) Debug strategies
      D) No troubleshooting

I3ae. Custom bölüm 1?
      A) Project-specific rules
      B) Domain-specific guidance
      C) Skip custom section
      D) Add custom section

I3af. Custom bölüm 2?
      A) Team-specific preferences
      B) Historical decisions
      C) Skip custom section
      D) Add custom section
```

## KATEGORI I4: CODE EXAMPLES (40 Soru)

```
I4. Kod örnekleri?
    A) Comprehensive (all scenarios)
    B) Key examples only
    C) Context-dependent
    D) No examples
    E) Real-world examples

I4a. Örnek programlama dili?
     A) PHP (Laravel/Symfony)
     B) JavaScript/TypeScript
     C) Python (Django/FastAPI)
     D) Java (Spring)
     E) Go (Gin/Echo)
     F) Rust
     G) C#/.NET
     H) Multiple languages
     I) Language-agnostic (pseudo)

I4b. Örnek framework?
     A) Full framework examples
     B) Vanilla/no framework
     C) Framework-agnostic
     D) Multiple frameworks

I4c. Örnek database?
     A) PostgreSQL
     B) MongoDB
     C) MySQL
     D) Redis + SQL
     E) Multiple databases
     F) No database examples

I4d. Örnek authentication?
     A) JWT examples
     B) OAuth2 examples
     C) Session-based examples
     D) Multi-factor auth
     E) All authentication types
     F) No auth examples

I4e. Örnek error handling?
     A) Try-catch examples
     B) Error logging examples
     C) Recovery patterns
     D) No error examples

I4f. Örnek async operations?
     A) Promise examples
     B) Async/await examples
     C) Event-driven examples
     D) No async examples

I4g. Örnek testing?
     A) Unit test examples
     B) Integration test examples
     C) E2E test examples
     D) Multiple testing frameworks
     E) No test examples

I4h. Örnek API responses?
     A) Success response examples
     B) Error response examples
     C) Paginated responses
     D) Rate-limited responses
     E) No API examples

I4i. Örnek configuration?
     A) Environment config examples
     B) Feature flag examples
     C) Database config examples
     D) No config examples

I4j. Örnek deployment?
     A) Docker examples
     B) Kubernetes examples
     C) CI/CD examples
     D) No deployment examples

I4k. Example complexity level?
     A) Hello World level
     B) Simple but realistic
     C) Production-ready complex
     D) Varies

I4l. Example completeness?
     A) Minimal viable example
     B) Full working example
     C) Includes dependencies
     D) Includes configuration

I4m. Example comments?
     A) Heavily commented
     B) Minimal comments
     C) Docstring style
     D) No comments

I4n. Example error cases?
     A) Happy path only
     B) Happy + sad path
     C) Exhaustive edge cases
     D) No error examples

I4o. Example performance?
     A) Optimized examples
     B) Standard examples
     C) Anti-patterns shown
     D) No performance focus

I4p. Example security?
     A) Security best practices
     B) Common vulnerabilities shown
     C) Exploitation examples
     D) No security examples

I4q. Example extensibility?
     A) Shows extension points
     B) Shows inheritance
     C) Shows composition
     D) No extensibility

I4r. Example output format?
     A) Copy-paste ready
     B) Pseudo-code style
     C) Conceptual diagrams
     D) Text explanation only

I4s. Example testing coverage?
     A) 100% coverage examples
     B) Critical path examples
     C) No test examples
     D) Mixed coverage

I4t. Example versioning?
     A) Current version (2026)
     B) Multiple versions
     C) Backward compat examples
     D) No versioning
```

## KATEGORI I5: VERSIONING & i18n (20 Soru)

```
I5. Sürüm numaralandırması?
    A) Semantic (v1.2.3)
    B) Calendar (2026-06-11)
    C) Ad-hoc (no version)
    D) Git commit hash
    E) Release number

I5a. Version history?
     A) Detailed changelog
     B) Brief release notes
     C) Git commit log
     D) No history

I5b. Deprecation policy?
     A) Explicit deprecation dates
     B) 6-month grace period
     C) Immediate removal
     D) No deprecation

I5c. Backward compatibility?
     A) Guaranteed (semver)
     B) 1 major version
     C) Best effort
     D) Breaking allowed

I5d. Internationalization?
     A) Turkish (TR)
     B) English (EN)
     C) Bilingual (EN+TR)
     D) Multi-language (5+)
     E) Language-agnostic

I5e. Localization method?
     A) Separate files per language
     B) i18n placeholders
     C) Duplicate content
     D) Links to translations

I5f. Date format?
     A) ISO 8601 (2026-06-11)
     B) Locale-specific
     C) Custom format
     D) No dates

I5g. Number format?
     A) Locale-specific (1.000,00 vs 1,000.00)
     B) Standard (1,000.00)
     C) No numbers
     D) Scientific notation

I5h. Currency examples?
     A) USD only
     B) Multiple currencies
     C) No currency
     D) Crypto examples

I5i. Time zone handling?
     A) UTC only
     B) Locale time zones
     C) User time zone
     D) No time zones

I5j. Accessibility translation?
     A) Screen reader friendly
     B) High contrast versions
     C) Multiple formats
     D) No accessibility focus
```
```

---

## SUMMARY TABLE

| Category | Questions | Topics |
|----------|-----------|--------|
| I1: Output Format | 40 | File types, naming, organization, markdown, links |
| I2: Scope Definition | 35 | AI target, project size, compliance, performance |
| I3: Master Prompt Sections | 45 | 30 major sections + customization |
| I4: Code Examples | 40 | Languages, frameworks, databases, patterns |
| I5: Versioning & i18n | 20 | Versioning, deprecation, localization, formats |
| **TOTAL** | **180+** | **Comprehensive prompt generation** |

---

## QUICK REFERENCE

### Output Format Categories
- Single vs multi-file outputs
- Markdown flavor & styling
- Code block organization
- Visual aids & diagrams
- Cross-references

### Scope Categories
- AI system target (Claude/OpenAI/Gemini/etc.)
- Project size (component to enterprise)
- Code volume (LOC)
- Team size & structure
- Performance SLA & reliability

### Master Prompt Sections
- Identity & core rules (5-6 sections)
- Technical standards (6-8 sections)
- Implementation guidance (5-7 sections)
- Operations & deployment (4-5 sections)
- Custom/project-specific sections

### Code Examples
- Primary language selection
- Framework specificity
- Database examples
- Authentication patterns
- Error handling
- Testing approach

### Versioning & i18n
- Semantic vs calendar versioning
- Deprecation policy
- Backward compatibility
- Multi-language support
- Localization methods

---

*BLOK I: 180+ Sorular | Prompt Tipi & Çıktı Format | Comprehensive Coverage*
