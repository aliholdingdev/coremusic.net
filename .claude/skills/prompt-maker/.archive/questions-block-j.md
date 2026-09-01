# BLOK J — KISITLAR & GEREKSINIMLER (220+ Soru)

## Kategori: J1 — Teknoloji Yasakları (45 soru)

```
J1.1. Framework yasaklanmış mı?
    A) Evet, özel olarak (hangi framework?)
    B) Sınırlı kullanım (nur mit Approval)
    C) Tavsiyeleri var ama seçmeli
    D) Hayır, herhangi bir framework ok

J1.2. Build tool yasaklanmış mı?
    A) Bundler yasak (Webpack, Vite, esbuild)
    B) Transpiler yasak (Babel, TypeScript compiler)
    C) Minifier yasak
    D) Hiçbiri yasak değil

J1.3. ORM/Database kütüphanesi kullanım?
    A) Raw SQL zorunlu
    B) Prepared statements + PDO/Mysqli
    C) ORM allowed (Eloquent, Doctrine)
    D) NoSQL (MongoDB, Firebase)
    E) Hybrid approach
    F) Yok, sadece files/memory

J1.4. Cache teknolojisi kısıtlaması?
    A) Hiçbir cache yasak
    B) Only in-memory cache
    C) Distributed cache required (Redis, Memcached)
    D) File-based cache only
    E) SPL cache allowed
    F) No restriction

J1.5. Package manager kısıtlaması?
    A) npm/yarn yasak
    B) composer yasak
    C) pip yasak
    D) gem yasak
    E) Belirli paketler blacklist (hangileri?)
    F) No restriction

J1.6. Test framework kısıtlaması?
    A) PHPUnit zorunlu
    B) Jest zorunlu
    C) Pytest zorunlu
    D) Custom testing framework
    E) Any testing framework ok
    F) No testing required

J1.7. Authentication library kısıtlaması?
    A) Custom implementation required
    B) Passport allowed (JS/Node)
    C) Laravel Auth allowed
    D) Auth0/Firebase allowed
    E) Any auth library ok
    F) No auth needed

J1.8. Logging framework kısıtlaması?
    A) Slog (Go)
    B) Monolog (PHP)
    C) Winston (JS)
    D) Log4j (Java)
    E) Custom logging
    F) No logging required

J1.9. Message queue yasaklı mı?
    A) RabbitMQ only
    B) Redis only
    C) SQS only
    D) Kafka only
    E) Any queue system
    F) No message queue needed

J1.10. Containerization teknolojisi?
    A) Docker required
    B) Podman required
    C) No containerization
    D) Optional but recommended

J1.11. Monitoring/Observability stack?
    A) Prometheus + Grafana
    B) DataDog
    C) New Relic
    D) ELK Stack
    E) Custom monitoring
    F) No monitoring needed

J1.12. CI/CD platform kısıtlaması?
    A) GitHub Actions
    B) GitLab CI
    C) Jenkins
    D) CircleCI
    E) Any platform ok
    F) No CI/CD

J1.13. Cloud provider kısıtlaması?
    A) AWS only
    B) Azure only
    C) GCP only
    D) Multi-cloud required
    E) On-premises required
    F) No restriction

J1.14. Database engine kısıtlaması?
    A) PostgreSQL only
    B) MySQL only
    C) SQLite only
    D) MSSQL only
    E) NoSQL (MongoDB, DynamoDB)
    F) Multi-DB support required

J1.15. API specification kısıtlaması?
    A) REST only
    B) GraphQL required
    C) gRPC required
    D) Any style ok
    E) WebSocket required

J1.16. Front-end framework kısıtlaması?
    A) React only
    B) Vue only
    C) Angular only
    D) Vanilla JS only (ADR 001)
    E) Any framework ok
    F) No front-end

J1.17. CSS preprocessor kısıtlaması?
    A) SCSS required
    B) CSS-in-JS (Styled Components)
    C) PostCSS required
    D) Vanilla CSS only
    E) Any method ok

J1.18. API Gateway kısıtlaması?
    A) Kong
    B) AWS API Gateway
    C) Nginx
    D) Custom implementation
    E) None needed

J1.19. Service mesh kısıtlaması?
    A) Istio required
    B) Linkerd required
    C) No service mesh
    D) Optional

J1.20. API documentation tool?
    A) OpenAPI/Swagger required
    B) AsyncAPI required
    C) Markdown docs only
    D) No documentation
    E) Any format ok

J1.21. Code style/linting tool?
    A) ESLint required
    B) PHPStan required
    C) Prettier required
    D) Custom linting rules
    E) No linting

J1.22. Type checking requirement?
    A) TypeScript required
    B) JSDoc required
    C) PHPStan static analysis
    D) No type checking
    E) Optional but recommended

J1.23. Security scanning tool?
    A) SAST scanning required
    B) DAST required
    C) Dependency scanning
    D) Manual security review
    E) No scanning

J1.24. Dependency management?
    A) Automatic updates (Dependabot)
    B) Manual review + update
    C) Pinned versions (no updates)
    D) No external dependencies

J1.25. Documentation generator?
    A) JSDoc/PHPDoc
    B) Typedoc
    C) Sphinx
    D) Markdown only
    E) None

J1.26. Code coverage requirement?
    A) >80% coverage required
    B) >60% coverage required
    C) >40% coverage required
    D) No coverage requirement
    E) Coverage measurement only

J1.27. Performance profiling tool?
    A) Lighthouse required
    B) Chrome DevTools
    C) Performance.now() only
    D) No profiling

J1.28. Memory profiling tool?
    A) Chromium heap snapshots
    B) Valgrind (C/C++)
    C) Go pprof
    D) None

J1.29. Tracing/Observability framework?
    A) OpenTelemetry
    B) Jaeger
    C) Zipkin
    D) Custom tracing
    E) No tracing

J1.30. Serialization format?
    A) JSON only
    B) Protocol Buffers
    C) MessagePack
    D) CBOR
    E) Any format ok

J1.31. Error tracking service?
    A) Sentry required
    B) Bugsnag required
    C) Custom error tracking
    D) No error tracking

J1.32. Feature flag service?
    A) LaunchDarkly
    B) Unleash
    C) Custom flags
    D) None

J1.33. A/B testing tool?
    A) Optimizely
    B) VWO
    C) Custom implementation
    D) Not needed

J1.34. CMS technology?
    A) Headless CMS required
    B) WordPress.com
    C) Custom CMS
    D) No CMS

J1.35. Search engine?
    A) Elasticsearch
    B) Algolia
    C) Database full-text search
    D) Not needed

J1.36. Real-time communication?
    A) WebSocket required
    B) Server-Sent Events
    C) HTTP polling
    D) No real-time

J1.37. File storage?
    A) S3/Cloud storage required
    B) File system only
    C) Database BLOB
    D) CDN required

J1.38. Payment processing?
    A) Stripe integration required
    B) PayPal integration required
    C) Custom payment gateway
    D) No payment processing

J1.39. Email service?
    A) SendGrid required
    B) AWS SES
    C) SMTP server
    D) No email needed

J1.40. SMS service?
    A) Twilio
    B) AWS SNS
    C) Custom SMS gateway
    D) Not needed

J1.41. Video/Media processing?
    A) FFmpeg required
    B) MediaConvert
    C) Stream custom processing
    D) Not needed

J1.42. Encoding/Compression?
    A) GZIP required
    B) Brotli required
    C) Custom compression
    D) No compression

J1.43. Mobile development?
    A) React Native only
    B) Flutter only
    C) Native (Swift/Kotlin)
    D) Web-only (no mobile)
    E) Any approach

J1.44. Desktop development?
    A) Electron only
    B) Tauri only
    C) Native (C#/C++)
    D) No desktop app
    E) Any approach

J1.45. Hardware integration?
    A) GPIO required
    B) Serial communication
    C) USB
    D) No hardware
    E) Other: ___
```

---

## Kategori: J2 — Mimari Yasakları (50 soru)

```
J2.1. Monolith vs Microservices?
    A) Strict monolith required
    B) Microservices required
    C) Modular monolith preferred
    D) No preference

J2.2. Singleton pattern kısıtlaması?
    A) Singletons yasak
    B) Singletons allowed
    C) Dependency injection required
    D) No restriction

J2.3. Global state kısıtlaması?
    A) Global state yasak
    B) Global scope allowed
    C) Controlled global state ok
    D) No restriction

J2.4. Callback hell yasaklı mı?
    A) Promises/async-await mandatory
    B) Callback allowed
    C) RxJS streams required
    D) No restriction

J2.5. Mutable state?
    A) Immutability required
    B) Mutation allowed
    C) Immutable data structures only
    D) No restriction

J2.6. Inheritance restrictions?
    A) No inheritance (composition only)
    B) Single inheritance only
    C) Multiple inheritance allowed
    D) No restriction

J2.7. Interface segregation?
    A) Small interfaces required
    B) Large interfaces ok
    C) No interfaces needed
    D) No restriction

J2.8. Dependency injection style?
    A) Constructor injection only
    B) Property injection allowed
    C) Service locator ok
    D) No restriction

J2.9. Circular dependency?
    A) Circular deps strictly forbidden
    B) Allowed if documented
    C) No check needed

J2.10. Layer separation (L0-L3)?
    A) Strict 4-layer architecture (ADR ...)
    B) Layered but flexible
    C) No strict layers
    D) Other: ___

J2.11. Database abstraction?
    A) Repository pattern required
    B) Direct DB queries allowed
    C) ORM only
    D) No restriction

J2.12. Configuration management?
    A) Environment variables only
    B) Config files (.env)
    C) Configuration server
    D) Hardcoded config allowed

J2.13. Error handling?
    A) Exceptions mandatory
    B) Error codes allowed
    C) try-catch required
    D) No error handling

J2.14. Logging architecture?
    A) Structured logging required
    B) String logging ok
    C) Silent (no logging)
    D) No restriction

J2.15. Caching strategy?
    A) Cache-aside pattern
    B) Write-through pattern
    C) Custom caching
    D) No caching

J2.16. Transaction management?
    A) ACID required
    B) Eventual consistency ok
    C) No transactions
    D) No restriction

J2.17. Concurrency model?
    A) Lock-free required
    B) Mutex allowed
    C) Semaphore ok
    D) No concurrency

J2.18. Memory management?
    A) Manual memory (C/C++)
    B) GC required
    C) No restriction

J2.19. Resource cleanup?
    A) RAII required (C++)
    B) Finally blocks required
    C) Garbage collection ok
    D) No cleanup

J2.20. API versioning?
    A) Semantic versioning
    B) URL versioning (v1, v2)
    C) Header versioning
    D) No versioning

J2.21. Backward compatibility?
    A) Breaking changes forbidden
    B) Deprecation period required
    C) Breaking changes allowed
    D) No restriction

J2.22. Feature flags?
    A) Feature flags required
    B) Feature flags recommended
    C) Optional
    D) Not needed

J2.23. Circuit breaker pattern?
    A) Circuit breaker required
    B) Recommended
    C) Optional
    D) Not needed

J2.24. Retry strategy?
    A) Exponential backoff required
    B) Linear retry ok
    C) Immediate retry
    D) No retries

J2.25. Timeout handling?
    A) Timeouts mandatory
    B) Timeouts optional
    C) No timeouts

J2.26. Rate limiting?
    A) Per-client rate limit
    B) Per-endpoint rate limit
    C) Global rate limit
    D) No rate limiting

J2.27. Idempotency?
    A) Idempotent operations required
    B) Idempotency keys
    C) Optional
    D) Not needed

J2.28. Data partitioning?
    A) Horizontal sharding
    B) Vertical partitioning
    C) No partitioning

J2.29. Replication strategy?
    A) Master-slave
    B) Master-master
    C) Multi-region
    D) No replication

J2.30. Disaster recovery?
    A) RTO < 1 hour required
    B) RTO < 1 day required
    C) Backup-only
    D) No DR plan

J2.31. Load balancing?
    A) Active-active required
    B) Active-passive ok
    C) No load balancing

J2.32. Auto-scaling?
    A) Horizontal auto-scaling
    B) Vertical scaling ok
    C) Manual scaling
    D) No scaling

J2.33. Deployment strategy?
    A) Blue-green deployment
    B) Canary deployment
    C) Rolling deployment
    D) Big bang deployment

J2.34. Rollback capability?
    A) Instant rollback required
    B) Quick rollback (< 5 min)
    C) Manual rollback ok
    D) No rollback

J2.35. Multi-tenancy?
    A) Tenant isolation required
    B) Shared database ok
    C) Separate databases per tenant
    D) Single tenant

J2.36. Event-driven architecture?
    A) Event-driven required
    B) Command-driven ok
    C) Request-response
    D) No preference

J2.37. CQRS pattern?
    A) CQRS required
    B) CQRS recommended
    C) Optional
    D) Not used

J2.38. Event sourcing?
    A) Event sourcing required
    B) Event sourcing recommended
    C) Optional
    D) Not used

J2.39. Saga pattern?
    A) Distributed saga required
    B) Local transactions ok
    C) No saga pattern

J2.40. Domain-driven design?
    A) Strict DDD required
    B) DDD principles preferred
    C) Optional
    D) Not used

J2.41. Value objects?
    A) Value objects required
    B) Primitives ok
    C) No restriction

J2.42. Entity modeling?
    A) Rich domain entities
    B) Anemic models ok
    C) No restriction

J2.43. Repository pattern?
    A) Repository required
    B) Active record ok
    C) No restriction

J2.44. Service layer?
    A) Service layer required
    B) Services optional
    C) No restriction

J2.45. Controller responsibilities?
    A) Thin controllers (request/response only)
    B) Controllers can have business logic
    C) No controllers

J2.46. Validation layer?
    A) Separated validation layer
    B) Inline validation ok
    C) No validation

J2.47. Authorization model?
    A) RBAC required
    B) ABAC required
    C) Simple allow/deny ok
    D) No authorization

J2.48. Multi-language support?
    A) i18n framework required
    B) Manual translation ok
    C) English-only

J2.49. Accessibility compliance?
    A) WCAG 2.2 AA required
    B) WCAG 2.1 AA required
    C) Basic accessibility
    D) No accessibility requirements

J2.50. Performance optimization?
    A) < 1s LCP required
    B) < 2s FCP required
    C) No performance targets
    D) Other: ___
```

---

## Kategori: J3 — Bütçe & Timeline (40 soru)

```
J3.1. Mali bütçe var mı?
    A) Evet, sınırlı (< $1000)
    B) Evet, orta ($1000-$10000)
    C) Evet, yüksek (> $10000)
    D) Sınırsız
    E) Hayır, bütçe yok

J3.2. Bütçe en çok nereye kullanılacak?
    A) Yazılım lisansları
    B) Bulut altyapısı
    C) İşgücü maliyeti
    D) Dış danışmanlar
    E) Diğer: ___

J3.3. Zaman sınırlaması?
    A) Çok acil (< 1 gün)
    B) Acil (1-3 gün)
    C) Normal (1-2 hafta)
    D) Rahat (1-3 ay)
    E) Uzun vadeli (> 3 ay)

J3.4. Proje bitmesi gereken tarih?
    A) Belirli tarih: ___
    B) ASAP ama esnek
    C) Belirsiz

J3.5. Kaç haftalık işçilik ayrılmış?
    A) 0-1 hafta
    B) 1-2 hafta
    C) 2-4 hafta
    D) 1-3 ay
    E) > 3 ay

J3.6. Takım üye sayısı?
    A) Solo (1 kişi)
    B) Küçük (2-3 kişi)
    C) Orta (4-10 kişi)
    D) Büyük (> 10 kişi)

J3.7. Takımda hangi roller var?
    A) Backend engineer(ler)
    B) Frontend engineer(ler)
    C) DevOps engineer(ler)
    D) QA/Test engineer(ler)
    E) Product manager
    F) Diğer: ___

J3.8. Dış danışman/kontraktor kullanılacak mı?
    A) Evet, tam zamanlı
    B) Evet, parça zamanlı
    C) Evet, belirli görevler için
    D) Hayır

J3.9. Outsourcing maliyeti bütçeye dahil mi?
    A) Evet, sınırlı
    B) Evet, sınırsız
    C) Hayır
    D) Kısmen

J3.10. Yazılım lisansı bütçesi?
    A) Open source only
    B) Kısmen ticari ürünler
    C) Ticari yazılım ok
    D) Premium tools required

J3.11. Bulut altyapısı bütçesi?
    A) < $100/ay
    B) $100-$1000/ay
    C) $1000-$10000/ay
    D) > $10000/ay

J3.12. Maintenance bütçesi?
    A) Belirli değil
    B) 10% proje maliyeti
    C) 20% proje maliyeti
    D) 30%+ proje maliyeti

J3.13. Eğitim & onboarding bütçesi?
    A) Dahil değil
    B) 5-10 saat
    C) 20-40 saat
    D) Sürekli

J3.14. Dokümantasyon bütçesi?
    A) Kod içi comments
    B) Detaylı API docs
    C) Video tutorials
    D) Diğer: ___

J3.15. Testing bütçesi?
    A) Unit tests only
    B) Unit + integration
    C) Unit + integration + E2E
    D) Comprehensive testing

J3.16. Security audit bütçesi?
    A) Internal review only
    B) Dış penetration test
    C) Tekrarlayan audits
    D) Dahil değil

J3.17. Performance optimization bütçesi?
    A) Dahil değil
    B) İlk sürümde optimize
    C) Sonradan optimize
    D) Özel optimizasyon bütçesi

J3.18. Monitoring & observability bütçesi?
    A) DIY (prometheus, grafana)
    B) Ticari tool (DataDog, New Relic)
    C) Dahil değil

J3.19. Backup & disaster recovery bütçesi?
    A) Basit backup (B2, Backblaze)
    B) Managed backup service
    C) Multi-region DR
    D) Dahil değil

J3.20. Scaling infrastructure maliyeti?
    A) Dahil değil (Fixed resources)
    B) Auto-scaling enabled
    C) Planlı kapasitesi

J3.21. Hukuki & compliance bütçesi?
    A) Dahil değil
    B) Kısmen (legal review)
    C) Tam (audit included)

J3.22. Marketing & launch bütçesi?
    A) Dahil değil
    B) Minimal (social media)
    C) Orta (paid ads)
    D) Yüksek

J3.23. User support/helpdesk bütçesi?
    A) Dahil değil
    B) Community support
    C) Email support
    D) 24/7 support

J3.24. Iteration & feedback loop süresi?
    A) Haftada bir
    B) İki haftada bir
    C) Ayda bir
    D) Proje sonunda

J3.25. MVP scope vs full scope?
    A) MVP önce (80/20 kural)
    B) Tam özellik set
    C) Tam özellik + nice-to-haves

J3.26. Bütçe aşılırsa?
    A) Scope azalacak
    B) Timeline uzayacak
    C) Kalite düşecek
    D) Ek bütçe var

J3.27. Timeline uzarsa?
    A) Bütçe artar
    B) Scope azalır
    C) Takım artırılır
    D) Kabul edilir

J3.28. Scope creep politikası?
    A) Katı (no scope creep)
    B) Kontrollü (change management)
    C) Esnek

J3.29. Change request süreci?
    A) Formal approval gerekli
    B) Verbal ok
    C) No change control

J3.30. Milestone reviews?
    A) Haftalık
    B) İki haftada bir
    C) Aylık
    D) Proje sonunda

J3.31. Progress tracking?
    A) Jira/Azure DevOps
    B) Spreadsheet
    C) Informal
    D) None

J3.32. Reporting frequency?
    A) Günlük
    B) Haftalık
    C) İki haftada bir
    D) Aylık
    E) Proje sonunda

J3.33. Risk management plan?
    A) Formal risk register
    B) Informal contingency
    C) No risk planning

J3.34. Contingency buffer?
    A) < 5% extra time
    B) 5-10% extra time
    C) 10-20% extra time
    D) > 20% extra time
    E) No buffer

J3.35. Technical debt tracking?
    A) Formal debt log
    B) Informal notes
    C) Not tracked

J3.36. Refactoring time allocation?
    A) 20% of sprint
    B) 10% of sprint
    C) Ad-hoc
    D) No refactoring

J3.37. Knowledge transfer plan?
    A) Formal documentation
    B) Video walkthrough
    C) Code comments only
    D) Assumption: internal team knows

J3.38. Handoff to operations?
    A) Runbook required
    B) Basic documentation
    C) Minimal documentation
    D) Code is documentation

J3.39. Post-launch support period?
    A) 1 hafta
    B) 1 ay
    C) 3 ay
    D) 6 ay+
    E) Indefinite

J3.40. Bug fix SLA?
    A) Critical: 1 hour, High: 4 hours, Normal: 1 day
    B) Critical: 4 hours, High: 1 day, Normal: 3 days
    C) All bugs: best effort
    D) No SLA
```

---

## Kategori: J4 — Hukuki & Uyum (45 soru)

```
J4.1. GDPR uyumluluğu gerekli mi?
    A) Evet, kesin
    B) Belki (AB müşteri varsa)
    C) Hayır
    D) Belirsiz

J4.2. GDPR veri işleme alanları?
    A) Kişisel veri var
    B) Hassas veri (health, race)
    C) Biometrik veri
    D) Hiçbiri

J4.3. PCI DSS uyumluluğu (ödeme işleme)?
    A) Evet, Level 1 (< $6M)
    B) Evet, Level 2 ($6M-$20M)
    C) Evet, Level 3 ($20M-$75M)
    D) Evet, Level 4 (> $75M)
    E) Hayır, ödeme işlemesi yok

J4.4. HIPAA uyumluluğu (sağlık)?
    A) Evet, kesin
    B) Belki (sağlık verisi var)
    C) Hayır

J4.5. SOC 2 audit gerekli mi?
    A) Evet, SOC 2 Type I
    B) Evet, SOC 2 Type II
    C) Tavsiye ediliyor
    D) Hayır

J4.6. ISO 27001 sertifikası?
    A) Gerekli
    B) Tavsiye ediliyor
    C) Optional
    D) Hayır

J4.7. COPPA (çocuklar) uyumluluğu?
    A) Evet, 13 yaş altı kullanıcı
    B) Evet, 18 yaş altı kullanıcı
    C) Hayır

J4.8. FTC Act 5 Section uyumluluğu?
    A) Evet
    B) Tavsiye
    C) Hayır

J4.9. California CCPA uyumluluğu?
    A) Evet
    B) Belki (CA kullanıcı varsa)
    C) Hayır

J4.10. Veri rezidensi gereksinimleri?
    A) Evet, EU-only (GDPR)
    B) Evet, US-only (HIPAA)
    C) Evet, belirli ülke: ___
    D) Hayır

J4.11. Veri silme talepleri (GDPR "right to be forgotten")?
    A) Otomatik sistem
    B) Manual process
    C) Tamamen silinemez
    D) Gerekli değil

J4.12. Data privacy impact assessment (DPIA) gerekli mi?
    A) Evet
    B) Tavsiye
    C) Hayır

J4.13. Data Protection Officer (DPO) atanması?
    A) Gerekli
    B) Tavsiye
    C) Hayır

J4.14. Veri işleme sözleşmesi (DPA) gerekli mi?
    A) Evet
    B) Tavsiye
    C) Hayır

J4.15. Privacy policy gereksinimleri?
    A) Detaylı (GDPR mutable)
    B) Temel (sadece önemli bilgiler)
    C) Yoktur

J4.16. Terms of Service (ToS) gereksinimleri?
    A) Detaylı hukuki ToS
    B) Temel ToS
    C) Yoktur

J4.17. Cookie consent banner gerekli mi?
    A) Evet, opt-in (GDPR)
    B) Evet, opt-out (US)
    C) Hayır

J4.18. Tracking/Analytics consent?
    A) Explicit consent required
    B) Implicit consent ok
    C) No tracking

J4.19. Third-party data sharing policy?
    A) No third-party sharing allowed
    B) Explicit user consent required
    C) Privacy policy disclosure sufficient
    D) No restrictions

J4.20. Subprocessor management?
    A) List maintained, transparency required
    B) Internal use only, no disclosure
    C) Not applicable

J4.21. Data breach notification timeline?
    A) GDPR 72 hours
    B) HIPAA 60 days
    C) Custom: ___ days
    D) No notification required

J4.22. Vendor/supplier security audits?
    A) Annual audit required
    B) Ad-hoc review
    C) Contractual assurance only
    D) No audits

J4.23. Employee data access logging?
    A) Audit log maintained
    B) Manual logs
    C) No logging

J4.24. Encryption at-rest requirement?
    A) AES-256 mandatory
    B) Any encryption ok
    C) Not required

J4.25. Encryption in-transit requirement?
    A) TLS 1.3 mandatory
    B) TLS 1.2+ ok
    C) Not required

J4.26. Incident response plan?
    A) Formal plan, tested
    B) Plan exists, not tested
    C) No formal plan

J4.27. Business continuity/DR testing?
    A) Quarterly tests
    B) Annual tests
    C) No testing

J4.28. Accessibility compliance (ADA/WCAG)?
    A) WCAG 2.2 AA required
    B) WCAG 2.1 AA required
    C) Basic compliance
    D) Not required

J4.29. Environmental compliance?
    A) Carbon-neutral required
    B) Monitored/reported
    C) Not tracked

J4.30. Labor/Employment law compliance?
    A) Remote work restrictions
    B) Geographic restrictions
    C) No restrictions

J4.31. Export control/ITAR?
    A) ITAR-controlled (defense tech)
    B) Standard controls ok
    C) Not applicable

J4.32. Sanctions list compliance (OFAC)?
    A) OFAC SDN check required
    B) On-demand only
    C) Not required

J4.33. Anti-corruption (FCPA/Bribery Act)?
    A) Compliance program required
    B) Policy only
    C) Not required

J4.34. Open source license compliance?
    A) FOSS policy required
    B) License audit before release
    C) Ad-hoc review
    D) No open source

J4.35. Copyright/trademark protection?
    A) Copyright registration
    B) Trademark registration
    C) Informal protection
    D) Not required

J4.36. Intellectual property assignment?
    A) All IP to company
    B) Shared IP (vendor retains)
    C) Public domain
    D) Case-by-case

J4.37. Customer data ownership?
    A) Company owns customer data
    B) Customer owns data
    C) Shared ownership
    D) Not applicable

J4.38. Competitors & non-compete?
    A) Non-compete clause (team)
    B) Non-solicitation clause
    C) No restrictions

J4.39. Data localization (sovereignty)?
    A) Required (specific country)
    B) Recommended
    C) Not required

J4.40. Government data requests?
    A) Transparency report published
    B) Compliant but no transparency
    C) Custom process

J4.41. Regulatory approvals?
    A) FDA approval required
    B) FCC approval required
    C) Financial authority approval
    D) No regulatory approval

J4.42. Audit trail retention?
    A) 7 years
    B) 5 years
    C) 3 years
    D) 1 year
    E) No retention

J4.43. Legal review of code/contracts?
    A) All contracts reviewed
    B) Spot-check review
    C) Internal review only
    D) No legal review

J4.44. Indemnification clause?
    A) Company indemnifies customer
    B) Customer indemnifies company
    C) Mutual indemnification
    D) No indemnification

J4.45. Liability limitations?
    A) Limited liability cap
    B) Unlimited liability
    C) No limitation
    D) Not applicable
```

---

## Kategori: J5 — Takım & Yetenekler (40 soru)

```
J5.1. Takım lider/mimarının seniority seviyesi?
    A) Junior (< 2 yıl)
    B) Mid-level (2-5 yıl)
    C) Senior (5-10 yıl)
    D) Staff/Principal (> 10 yıl)

J5.2. Frontend developer seniority?
    A) Junior
    B) Mid-level
    C) Senior
    D) Yok (designer yapacak)

J5.3. Backend developer seniority?
    A) Junior
    B) Mid-level
    C) Senior
    D) Yok

J5.4. DevOps/Infrastructure seniority?
    A) Junior
    B) Mid-level
    C) Senior
    D) Yok (gerekli değil)

J5.5. Security expert availability?
    A) Full-time
    B) Part-time
    C) Consulting (as-needed)
    D) Yok

J5.6. QA/Testing expertise?
    A) Dedicated QA
    B) Dev writes tests
    C) Manual testing
    D) No formal QA

J5.7. Product manager availability?
    A) Full-time
    B) Part-time
    C) Shared with other projects
    D) Yok

J5.8. Scrum master/Agile facilitator?
    A) Evet
    B) Hayır (ad-hoc agile)
    C) Agile olmayan yöntem

J5.9. Technical writer/documentation specialist?
    A) Dedicated
    B) Developer docu-writes
    C) Yok

J5.10. Data scientist/Analytics?
    A) Evet
    B) Basic analytics
    C) Hayır

J5.11. Mobile developer available?
    A) Evet
    B) Desktop only
    C) Web-only

J5.12. Database administrator (DBA)?
    A) Dedicated DBA
    B) Developer manages DB
    C) Cloud managed DB

J5.13. Team co-location?
    A) Fully co-located
    B) Hybrid (some remote)
    C) Fully remote
    D) Distributed globally

J5.14. Time zone spread?
    A) Same time zone
    B) Adjacent time zones (< 4 hours)
    C) Global spread (> 8 hours)

J5.15. Communication language?
    A) English
    B) Turkish
    C) Other: ___
    D) Mixed

J5.16. Team member turnover risk?
    A) Low (stable team)
    B) Medium (possible departures)
    C) High (uncertain)

J5.17. Knowledge silos?
    A) No silos (cross-trained)
    B) Some silos (manageable)
    C) Heavy silos (risk)

J5.18. Code review culture?
    A) Mandatory reviews (all PRs)
    B) Selective reviews
    C) No formal reviews

J5.19. Pair programming practice?
    A) Regular
    B) Occasional
    C) Never

J5.20. Mentoring/onboarding budget?
    A) 40+ hours
    B) 20-40 hours
    C) < 20 hours
    D) None

J5.21. Technology training budget?
    A) Regular courses/conferences
    B) Occasional online courses
    C) Self-learning only
    D) None

J5.22. Team morale/engagement?
    A) Very high
    B) Good
    C) Neutral
    D) Low

J5.23. Technical debt ownership?
    A) Team owns it + pays down regularly
    B) Acknowledged but deprioritized
    C) Ignored

J5.24. Risk management expertise?
    A) Senior experienced team
    B) Basic risk awareness
    C) Risk-unaware

J5.25. Compliance expertise (security/legal)?
    A) Compliance expert
    B) Basic compliance knowledge
    C) No compliance expertise

J5.26. Performance optimization expertise?
    A) Dedicated performance engineer
    B) General optimization skills
    C) Ad-hoc optimization

J5.27. Scalability expertise?
    A) Architect designed for scale
    B) Basic scalability awareness
    C) Not planned for

J5.28. DevOps/Infrastructure expertise?
    A) Dedicated DevOps engineer(s)
    B) Developer+ops responsibility
    C) Manual deployment

J5.29. Observability/Monitoring expertise?
    A) Dedicated + tooling
    B) Basic monitoring
    C) No monitoring

J5.30. Database expertise level?
    A) Expert (optimization, sharding)
    B) Competent (normal queries)
    C) Basic (simple CRUD)

J5.31. Architecture decision-making?
    A) Collaborative (team consensus)
    B) Lead architect decides
    C) Ad-hoc decisions

J5.32. Code quality standards?
    A) High (strict review + static analysis)
    B) Medium (basic standards)
    C) Low (works = good)

J5.33. Testing discipline?
    A) Test-first (TDD)
    B) Write tests (after code)
    C) Ad-hoc testing
    D) No testing

J5.34. Documentation discipline?
    A) Well-documented culture
    B) Some documentation
    C) Minimal docs
    D) Code is documentation

J5.35. Version control discipline?
    A) Strict branching (gitflow)
    B) Trunk-based development
    C) Feature branches
    D) Shared main branch

J5.36. CI/CD maturity?
    A) Continuous deployment (CD)
    B) Continuous integration (CI) + manual deploy
    C) Manual testing + deployment
    D) No CI/CD

J5.37. Incident response capability?
    A) Formal incident command
    B) Ad-hoc response
    C) No formal process

J5.38. Retrospective/Learning culture?
    A) Regular retros + action items
    B) Occasional retros
    C) No retros

J5.39. Technology diversity?
    A) Many languages/frameworks
    B) Standardized stack
    C) Single monolithic platform

J5.40. External expert consultation?
    A) Available (budget for consultants)
    B) Limited (emergency only)
    C) Not available
```

---

## Özet & Tasnif

```
BLOK J KATEGORI ÖZET:
- J1 (45 soru): Teknoloji Yasakları & Kısıtlamaları
  → Framework, build tools, libraries, cloud services, monitoring
  
- J2 (50 soru): Mimari Yasakları & Tasarım Kısıtlamaları
  → SOLID, patterns, database strategy, scaling, API design
  
- J3 (40 soru): Bütçe & Timeline
  → Mali kısıtlamalar, zaman, takım boyutu, iteration planning
  
- J4 (45 soru): Hukuki & Uyum
  → GDPR, PCI DSS, HIPAA, compliance, data protection
  
- J5 (40 soru): Takım & Yetenekler
  → Seniority, expertise, team structure, culture, maturity

TOPLAM: 220+ Soru

KULLANIM:
1. Prompt maker, bu blok J soruları sorarak kısıtlamaları öğrenir
2. Kullanıcı cevaplarını kategorize eder
3. Constraint map oluşturur
4. Prompt strategisi bu constraints etrafında şekillendirilir
5. Her cevap master prompt'a feed-forward edilir

ÇIKTI ÖRNEĞI:
"J1.3: ORM allowed (Eloquent, Doctrine) → Database constraint: Eloquent ORM, not raw SQL
 J2.10: Strict 4-layer L0-L3 → Architecture constraint: Hybrid SPA with layer enforcement
 J3.5: 2-4 hafta işçilik → Timeline: 2-week implementation + 1 week testing
 J4.1: GDPR uyumlu → Compliance: Data protection, privacy policy, consent management
 J5.1: Senior (5-10 yıl) → Audience: Experienced architects, expects deep knowledge"
```

---

*BLOK J: 220+ SORU | KISITLAR & ÖZEL GEREKSINIMLER | KATEGORI: J1-J5 | DOSYA: questions-block-j.md*
