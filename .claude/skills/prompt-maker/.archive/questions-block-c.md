# BLOK C — MİMARİ & TASARIM (280+ Soru)

**Format:** Seçenekli (A-O alt alta) | Turkish primary | 2026-06-11

---

## C1 — SİSTEM MİMARİSİ (50 Soru)

C1.1. Sistem mimarisi?
A) Monolith
B) Modular monolith
C) Microservices
D) Serverless
E) Hybrid SPA
F) Jamstack
G) Edge computing
H) CQRS
I) Event sourcing
J) SOA

C1.2. Katmanlı mimari?
A) 4-layer (Presentation→App→Domain→Infra)
B) 3-layer (Presentation→App→Data)
C) N-layer (many custom)
D) No explicit layers

C1.3. Domain-Driven Design (DDD)?
A) Full DDD (bounded contexts)
B) Partial DDD
C) CQRS + Event Sourcing
D) None (CRUD-based)

C1.4. Event-driven?
A) Fully async
B) Event sourcing
C) Pub/Sub
D) Webhooks
E) None

C1.5. Service communication?
A) REST (JSON/HTTP)
B) gRPC
C) Message queues
D) WebSocket
E) Multiple

C1.6. Service discovery?
A) DNS
B) Service registry (Consul)
C) Kubernetes
D) Hardcoded
E) None

C1.7. Circuit breaker?
A) Implemented
B) Exponential backoff
C) None

C1.8. Load balancing?
A) Round-robin
B) Least connections
C) IP hash
D) Random
E) Custom

C1.9. Graceful degradation?
A) Cached fallback
B) Default response
C) 500 error
D) None

C1.10. Geographic distribution?
A) Single region
B) Multi-region passive
C) Multi-region active-active
D) Global CDN

---

## C2 — API TASARIMI (50 Soru)

C2.1. API style?
A) REST
B) GraphQL
C) gRPC
D) WebSocket
E) SOAP
F) SSE
G) Custom
H) Multiple

C2.2. REST conventions?
A) RESTful (strict)
B) REST-ish (pragmatic)
C) HTTP API

C2.3. Pagination?
A) Offset-limit
B) Cursor
C) Keyset
D) None

C2.4. Versioning?
A) URL path
B) Header
C) Query param
D) None

C2.5. Rate limiting?
A) Per IP
B) Per API key
C) Per user
D) Per endpoint
E) None

C2.6. Authentication?
A) API keys
B) JWT
C) OAuth2
D) mTLS
E) Session

C2.7. HTTPS?
A) Mandatory
B) Recommended
C) Optional
D) None

C2.8. CORS?
A) Specific origins
B) Wildcard
C) None

C2.9. Input validation?
A) JSON schema
B) DTO validation
C) Custom
D) None

C2.10. Error format?
A) {error: {code, msg}}
B) Detailed
C) Minimal

C2.11. Caching?
A) ETag
B) Cache-Control
C) Redis
D) None

C2.12. API documentation?
A) OpenAPI/Swagger
B) GraphQL introspection
C) Custom
D) None

C2.13. Rate limit headers?
A) X-RateLimit-*
B) Standard RateLimit-*
C) None

C2.14. Idempotency?
A) Idempotency keys
B) None

C2.15. Webhook security?
A) HMAC signatures
B) JWT
C) None

---

## C3 — DATABASE TASARIMI (50 Soru)

C3.1. Normalization?
A) 3NF
B) BCNF
C) Denormalized
D) Mixed

C3.2. Surrogate keys?
A) Auto-increment
B) UUID
C) Composite
D) Natural

C3.3. Soft deletes?
A) deleted_at
B) Hard delete
C) Flags
D) None

C3.4. Timestamps?
A) created_at/updated_at
B) deleted_at
C) Custom
D) None

C3.5. Audit trail?
A) Triggers
B) App logging
C) History tables
D) None

C3.6. Indexing?
A) Single column
B) Composite
C) Full-text
D) Custom

C3.7. Sharding?
A) By user_id
B) By time
C) Geographically
D) No sharding

C3.8. Read replicas?
A) Enabled
B) None

C3.9. Backup?
A) Hourly
B) Daily
C) Weekly
D) Manual

C3.10. Encryption?
A) TDE (Transparent)
B) Column-level
C) App-level
D) None

---

## C4 — FRONTEND ARCHITECTURE (50 Soru)

C4.1. Rendering?
A) CSR (Client-Side)
B) SSR (Server-Side)
C) SSG (Static)
D) Hybrid (Islands)
E) MPA (Multi-Page)

C4.2. SPA framework?
A) React
B) Vue
C) Angular
D) Svelte
E) Vanilla
F) None

C4.3. Code splitting?
A) Route-based
B) Component-based
C) Vendor bundles
D) No splitting

C4.4. State management?
A) Redux
B) Context
C) Zustand
D) Pinia
E) None

C4.5. Form handling?
A) React Hook Form
B) Formik
C) Uncontrolled
D) Custom
E) None

C4.6. Error boundaries?
A) Implemented
B) None

C4.7. Lazy loading?
A) Images
B) Routes
C) Components
D) All
E) None

C4.8. PWA?
A) Install prompt
B) Offline
C) Push notifications
D) None

C4.9. Meta tags?
A) OG
B) Twitter
C) Canonical
D) Generated
E) Static

C4.10. Web Vitals?
A) LCP <2.5s
B) INP <200ms
C) CLS <0.1
D) Monitored
E) None

---

## C5 — DEPLOYMENT & SCALING (40 Soru)

C5.1. Deployment?
A) Blue-green
B) Canary
C) Rolling
D) All-at-once
E) Feature flags

C5.2. Downtime tolerance?
A) Zero
B) <1min
C) <5min
D) >5min

C5.3. Rollback?
A) Instant
B) <1min
C) <5min
D) >5min
E) None

C5.4. Scaling?
A) Horizontal (stateless)
B) Vertical (CPU+RAM)
C) Both
D) None

C5.5. Load balancer?
A) ALB (layer 7)
B) NLB (layer 4)
C) Classic
D) None

C5.6. Auto-scaling?
A) CPU-based
B) Memory-based
C) Custom metrics
D) None

C5.7. Database scaling?
A) Read replicas
B) Sharding
C) Partitioning
D) None

C5.8. Monitoring?
A) Prometheus
B) CloudWatch
C) Datadog
D) None

C5.9. Alerting?
A) PagerDuty
B) Opsgenie
C) Slack
D) Email
E) None

C5.10. Incident response?
A) Plan exists
B) Partial
C) None

---

## C6 — REAL-TIME & EVENT-DRIVEN (40 Soru)

C6.1. Real-time requirements?
A) Sub-100ms
B) <1s
C) <5s
D) Not critical

C6.2. Real-time tech?
A) WebSocket
B) Server-Sent Events
C) Polling
D) GraphQL Subscriptions
E) None

C6.3. Message queue?
A) RabbitMQ
B) Kafka
C) Redis Streams
D) NATS
E) None

C6.4. Message ordering?
A) Guaranteed
B) Best-effort
C) No guarantee

C6.5. Message durability?
A) Persisted
B) In-memory
C) Configurable

C6.6. Message retention?
A) 1 hour
B) 1 day
C) 1 week
D) Unlimited

C6.7. Dead-letter queue?
A) Implemented
B) Planned
C) None

C6.8. Consumer groups?
A) Supported
B) Not applicable
C) None

C6.9. Stream processing?
A) Kafka Streams
B) Flink
C) Spark Streaming
D) None

C6.10. Event sourcing?
A) Full event store
B) Event-driven
C) Traditional
D) Hybrid

C6.11. Idempotency?
A) Enforced
B) Partial
C) None

C6.12. Exactly-once processing?
A) Supported
B) At-least-once
C) At-most-once

C6.13. Batch processing?
A) Nightly
B) Hourly
C) On-demand
D) Real-time

C6.14. Job scheduling?
A) Cron
B) Airflow
C) Custom
D) Manual

C6.15. Job parallelization?
A) Parallel
B) Sequential
C) Adaptive

---

## C7 — OBSERVABILITY & OPERATIONS (40 Soru)

C7.1. Logging?
A) Structured logging
B) Levels + Correlation ID
C) Distributed tracing
D) None

C7.2. Log format?
A) JSON
B) Plain text
C) Custom
D) Mixed

C7.3. Log retention?
A) 1 day
B) 7 days
C) 30 days
D) 90 days
E) 1 year

C7.4. Metrics collection?
A) Prometheus
B) Statsd
C) CloudWatch
D) Custom
E) None

C7.5. APM tool?
A) New Relic
B) DataDog
C) Dynatrace
D) Prometheus
E) None

C7.6. Health checks?
A) /health + /status
B) Readiness
C) Custom
D) None

C7.7. Alerting?
A) PagerDuty
B) Opsgenie
C) Slack
D) Email
E) None

C7.8. Alert routing?
A) Severity-based
B) Team-based
C) Escalation
D) Custom

C7.9. On-call?
A) Roster-based
B) Random
C) Volunteer
D) None

C7.10. Runbook?
A) Complete
B) Partial
C) Missing

C7.11. Incident severity?
A) SEV-0/1/2/3
B) Custom
C) None

C7.12. Post-incident review?
A) Formal
B) Informal
C) Ad-hoc
D) None

C7.13. Distributed tracing?
A) Jaeger
B) Zipkin
C) Datadog
D) None

C7.14. Trace sampling?
A) 100%
B) 10%
C) 1%
D) Adaptive

C7.15. Log aggregation?
A) ELK
B) CloudWatch
C) Splunk
D) None

---

## C8 — TESTING STRATEGY (40 Soru)

C8.1. Test pyramid?
A) 80:15:5 (unit:integration:e2e)
B) Custom ratio
C) No pyramid

C8.2. Unit test coverage?
A) >80%
B) 60-80%
C) <60%
D) Unknown

C8.3. Integration tests?
A) Comprehensive
B) Selective
C) None

C8.4. E2E tests?
A) Comprehensive
B) Smoke
C) None

C8.5. Contract testing?
A) Consumer-driven
B) Provider-driven
C) None

C8.6. Chaos engineering?
A) Regular
B) Ad-hoc
C) None

C8.7. Performance testing?
A) Load/Stress/Spike
B) None

C8.8. Security testing?
A) SAST/DAST
B) Penetration
C) None

C8.9. Visual regression?
A) Percy/Chromatic
B) Custom
C) None

C8.10. Accessibility testing?
A) Automated
B) Manual
C) Both
D) None

C8.11. Load testing frequency?
A) Per release
B) Weekly
C) Monthly
D) Never

C8.12. Test data?
A) Factory/Faker
B) Fixtures
C) Database clone
D) None

C8.13. Test environment?
A) Dev/Staging/Prod-like
B) Multiple
C) Single

C8.14. Test execution?
A) CI/CD
B) Developer machine
C) Scheduled
D) Manual

C8.15. Flakiness tracking?
A) Tracked
B) Ignored
C) No issue

---

## C9 — RELIABILITY & RTO/RPO (30 Soru)

C9.1. RTO target?
A) <15 min
B) <1 hour
C) <4 hours
D) <1 day
E) Not defined

C9.2. RPO target?
A) Real-time
B) <1 hour
C) <1 day
D) Not defined

C9.3. Disaster recovery?
A) Documented + tested
B) Partial
C) None

C9.4. DR testing?
A) Quarterly
B) Biannually
C) Annually
D) Never

C9.5. Failover?
A) Automatic
B) <1 min
C) Manual
D) Semi-automatic

C9.6. Backup automation?
A) Managed
B) Custom
C) Cron
D) None

C9.7. Backup frequency?
A) Real-time
B) Hourly
C) Daily
D) Weekly

C9.8. Backup encryption?
A) At rest
B) In transit
C) Both

C9.9. Backup validation?
A) Restore tests
B) Integrity checks
C) None

C9.10. Backup retention?
A) 7 days
B) 30 days
C) 90 days
D) 1 year+

C9.11. Point-in-time recovery?
A) Continuous
B) Daily
C) No

C9.12. Multi-region?
A) Active-active
B) Active-passive
C) None

C9.13. Change management?
A) Formal process
B) Code review + CI/CD
C) Ad-hoc

C9.14. Release notes?
A) Automated
B) Manual
C) None

C9.15. Changelog?
A) Automated
B) Manual
C) None

---

## C10 — CACHE & PERFORMANCE OPTIMIZATION (50 Soru)

C10.1. Cache layers?
A) Browser + CDN + Server + DB
B) Selective
C) None

C10.2. Cache invalidation?
A) TTL-based
B) Event-based
C) Manual
D) No strategy

C10.3. Cache key design?
A) Composite (user+resource+version)
B) Simple
C) Random

C10.4. Distributed cache?
A) Redis
B) Memcached
C) Hazelcast
D) None

C10.5. Cache warm-up?
A) On-demand
B) Pre-load (scheduled)
C) Never

C10.6. Cache hit rate?
A) Monitored
B) Not monitored

C10.7. Query optimization?
A) EXPLAIN analysis
B) Index tuning
C) Denormalization
D) Manual

C10.8. N+1 query prevention?
A) ORM eager loading
B) Batch queries
C) Awareness
D) None

C10.9. Connection pooling?
A) Mandatory
B) Recommended
C) Not used

C10.10. Pool size?
A) 10
B) 20
C) 50
D) 100+
E) Custom

C10.11. Slow query monitoring?
A) Threshold logged
B) Sampled
C) Never

C10.12. Full-text search?
A) Elasticsearch
B) Database FTS
C) Linear search

C10.13. Replication?
A) Master-slave
B) Master-master
C) Multi-region
D) None

C10.14. Read replicas?
A) 1-2
B) 3-5
C) 5+
D) None

C10.15. Replica lag?
A) <1s
B) 1-10s
C) 10s+
D) Not monitored

C10.16. Read/write splitting?
A) Application level
B) Proxy level
C) None

C10.17. Data consistency?
A) Strong
B) Eventual
C) Causal
D) Weak

C10.18. Eventual consistency window?
A) <1s
B) <1 min
C) <1 hour
D) Unbounded

C10.19. Data warehouse?
A) Snowflake/BigQuery
B) None

C10.20. Data lake?
A) Exists
B) Planned
C) None

C10.21. ETL/ELT pipeline?
A) Airflow/Prefect/dbt
B) Custom
C) None

C10.22. Data quality checks?
A) Automated
B) Manual
C) None

C10.23. Data lineage?
A) Tracked
B) Partial
C) None

C10.24. GDPR compliance?
A) Full
B) Partial
C) Not applicable

C10.25. Data retention?
A) Defined policy
B) Ad-hoc
C) Unknown

---

## C11 — SECURITY & COMPLIANCE (50 Soru)

C11.1. Secrets in code?
A) No (Vault/KMS)
B) Encrypted
C) .env file
D) Hardcoded (RISK)

C11.2. Secret rotation?
A) Automated
B) Manual
C) Never

C11.3. Secrets scanning?
A) Git repo
B) CI/CD
C) Runtime
D) None

C11.4. Input validation?
A) Server-side strict
B) Client+server
C) Lenient
D) None

C11.5. SQL injection?
A) Prepared statements
B) ORM
C) Escaping
D) None

C11.6. XSS protection?
A) Output encoding
B) CSP
C) Trusted Types
D) None

C11.7. CSRF?
A) Tokens (hash_equals)
B) SameSite cookie
C) Origin check
D) None

C11.8. Rate limiting?
A) Per IP
B) Per user
C) Per endpoint
D) None

C11.9. HTTPS enforcement?
A) Mandatory
B) Recommended
C) Optional
D) None

C11.10. TLS version?
A) 1.3
B) 1.2
C) Older
D) None

C11.11. Certificate pinning?
A) Implemented
B) Planned
C) None

C11.12. HSTS?
A) Implemented
B) Enabled
C) Disabled
D) None

C11.13. Security headers?
A) CSP + X-Frame-Options
B) Minimal
C) None

C11.14. WAF?
A) AWS WAF/Cloudflare
B) ModSecurity
C) None

C11.15. DDoS protection?
A) Cloudflare/AWS Shield
B) None

C11.16. Bot detection?
A) reCAPTCHA
B) Rate limiting
C) None

C11.17. MFA enforcement?
A) Mandatory
B) Optional
C) None

C11.18. Password policy?
A) Complex + rotated
B) Simple
C) None

C11.19. Access control?
A) RBAC
B) ABAC
C) ACL
D) None

C11.20. Encryption at rest?
A) AES-256-GCM
B) TDE
C) None

C11.21. Encryption in transit?
A) TLS 1.3
B) TLS 1.2
C) None

C11.22. Key management?
A) Vault
B) KMS
C) .env
D) Hardcoded

C11.23. Audit logging?
A) All API calls
B) Auth events
C) None

C11.24. Log retention?
A) 30 days
B) 90 days
C) 1 year
D) Unlimited

C11.25. Penetration testing?
A) Annual
B) Quarterly
C) Never

C11.26. Vulnerability scanning?
A) Automated
B) Manual
C) None

C11.27. Code review?
A) Mandatory
B) Recommended
C) None

C11.28. SAST tools?
A) SonarQube/Semgrep
B) None

C11.29. DAST tools?
A) OWASP ZAP/Burp
B) None

C11.30. Dependency scanning?
A) Snyk/npm audit
B) None

C11.31. Supply chain security?
A) Signed dependencies
B) Checksum verification
C) None

C11.32. Third-party reviews?
A) SaaS vendor assessed
B) Self-assessed
C) Never

C11.33. Security training?
A) Annual mandatory
B) Quarterly
C) None

C11.34. Incident response?
A) Plan exists
B) Team trained
C) None

C11.35. Post-incident review?
A) Formal
B) Informal
C) None

C11.36. Zero-trust network?
A) Full (mTLS)
B) Partial
C) Traditional

C11.37. Network segmentation?
A) VPC isolation
B) Security groups
C) None

C11.38. Firewall rules?
A) Default deny
B) Default allow

C11.39. Container security?
A) Pod security policy
B) Network policies
C) RBAC
D) None

C11.40. Image scanning?
A) Trivy/Snyk
B) None

C11.41. Kubernetes RBAC?
A) Implemented
B) Basic
C) None

C11.42. Secrets in K8s?
A) Sealed Secrets
B) External Secrets
C) ConfigMaps

C11.43. mTLS enforcement?
A) Service mesh (Istio)
B) Manual
C) None

C11.44. Zero-knowledge?
A) User data unreadable
B) E2E encryption
C) None

C11.45. Data minimization?
A) Enforced
B) Recommended
C) Not considered

C11.46. Privacy by design?
A) Implemented
B) Planned
C) No

C11.47. GDPR right to delete?
A) Implemented
C) Not applicable

C11.48. Data portability?
A) Supported
B) Not supported

C11.49. Compliance audit?
A) Annual
B) On-demand
C) Never

C11.50. Compliance reporting?
A) Automated
B) Manual
C) None

---

## C12 — MONITORING & OBSERVABILITY (50 Soru)

C12.1. Monitoring stack?
A) Prometheus + Grafana
B) Datadog
C) New Relic
D) CloudWatch
E) Custom

C12.2. Metrics cardinality?
A) Controlled
B) Unlimited
C) High risk

C12.3. Metric retention?
A) 1 day
B) 30 days
C) 1 year
D) Unlimited

C12.4. Alerting rules?
A) 50+ rules
B) 10-50 rules
C) <10 rules
D) None

C12.5. Alert fatigue?
A) Managed
B) Not managed
C) Critical problem

C12.6. Alert routing?
A) Severity-based
B) Team-based
C) Escalation
D) Custom

C12.7. On-call rotation?
A) Roster-based
B) Random
C) Volunteer
D) None

C12.8. On-call handoff?
A) Automated
B) Manual
C) None

C12.9. Incident severity?
A) SEV-0/1/2/3
B) Custom
C) None

C12.10. Incident response SLA?
A) <1 hour
B) <4 hours
C) <8 hours
D) >1 day
E) None

C12.11. Runbook?
A) Complete + automated
B) Partial
C) Missing

C12.12. Post-mortem?
A) Formal blameless
B) Informal
C) Ad-hoc
D) None

C12.13. MTTR (Mean Time To Recover)?
A) <15 min
B) <1 hour
C) >1 hour
D) Unknown

C12.14. MTTD (Mean Time To Detect)?
A) <5 min
B) <15 min
C) >15 min
D) Unknown

C12.15. Observability maturity?
A) Logs+Metrics+Traces
B) Logs+Metrics
C) Metrics only
D) None

C12.16. Distributed tracing?
A) Jaeger
B) Zipkin
C) Datadog
D) None

C12.17. Trace sampling?
A) 100%
B) 10%
C) 1%
D) Adaptive

C12.18. Trace retention?
A) 7 days
B) 30 days
C) 1 year
D) Custom

C12.19. Log aggregation?
A) ELK
B) CloudWatch
C) Splunk
D) None

C12.20. Log sampling?
A) 100%
B) 10%
C) 1%
D) Adaptive

C12.21. Error tracking?
A) Sentry
B) Rollbar
C) Custom
D) None

C12.22. Performance profiling?
A) Continuous
B) On-demand
C) Never

C12.23. CPU profiling?
A) Enabled
B) Disabled

C12.24. Memory profiling?
A) Enabled
B) Disabled

C12.25. Database monitoring?
A) Query analysis
B) Replication lag
C) Lock contention
D) All

C12.26. Network monitoring?
A) Bandwidth tracking
B) Packet loss
C) Latency
D) None

C12.27. Storage monitoring?
A) Disk usage
B) IOPS
C) Both
D) None

C12.28. Cost monitoring?
A) Budget alerts
B) Anomaly detection
C) None

C12.29. Capacity planning?
A) 3-month horizon
B) 6-month
C) 1-year
D) None

C12.30. Trend analysis?
A) Automated
B) Manual
C) None

C12.31. Visualization?
A) Real-time dashboards
B) Static reports
C) None

C12.32. Custom metrics?
A) Business metrics
B) Technical only
C) None

C12.33. User analytics?
A) Session tracking
B) Conversion funnels
C) None

C12.34. Synthetic monitoring?
A) Pingdom
B) Uptime Robot
C) None

C12.35. Real user monitoring?
A) Enabled
B) Partial
C) None

C12.36. SLO definition?
A) Documented
B) Ad-hoc
C) None

C12.37. SLI measurement?
A) Automated
B) Manual
C) None

C12.38. Error budget?
A) Tracked
B) Not tracked

C12.39. Burn rate alerts?
A) Implemented
B) None

C12.40. Audit trail?
A) All API calls
B) Auth events
C) Modifications
D) None

C12.41. Audit retention?
A) 1 year
B) Unlimited
C) Custom

C12.42. Compliance audit?
A) Automated
B) Manual
C) None

C12.43. Security monitoring?
A) Threat detection
B) Intrusion detection
C) None

C12.44. Anomaly detection?
A) ML-based
B) Rule-based
C) None

C12.45. Alert correlation?
A) Implemented
B) None

C12.46. Root cause analysis?
A) Automated
B) Manual
C) None

C12.47. Incident timeline?
A) Automated
B) Manual
C) None

C12.48. Status page?
A) Real-time
B) Manual
C) None

C12.49. Transparency?
A) Public incidents
B) Internal only
C) None

C12.50. Notification?
A) Multiple channels
B) Email only
C) None

---

## C13 — COST OPTIMIZATION (40 Soru)

C13.1. Cloud provider?
A) AWS
B) Azure
C) GCP
D) Multi-cloud
E) On-prem

C13.2. Compute optimization?
A) Reserved instances
B) Spot instances
C) On-demand
D) Hybrid

C13.3. Savings plan?
A) 1-year + 3-year
B) On-demand

C13.4. Auto-scaling?
A) CPU-based
B) Memory-based
C) Custom metrics
D) None

C13.5. Scaling frequency?
A) <1 min
B) <5 min
C) >5 min

C13.6. Unused resource cleanup?
A) Automated
B) Manual
C) None

C13.7. Right-sizing?
A) Quarterly review
B) Annual
C) Ad-hoc

C13.8. Database optimization?
A) Read replicas pruned
B) Unused DBs deleted
C) None

C13.9. Storage tiering?
A) Hot/warm/cold
B) Single tier
C) None

C13.10. Archive strategy?
A) Automated
B) Manual
C) None

C13.11. Backup optimization?
A) Incremental backups
B) Full backups only
C) None

C13.12. CDN usage?
A) Cloudflare
B) AWS CloudFront
C) None

C13.13. Network optimization?
A) NAT gateway pruned
B) Not optimized

C13.14. Load balancer?
A) Needed
B) Unnecessary
C) Unknown

C13.15. Data transfer costs?
A) Monitored
B) Not monitored

C13.16. Inter-region transfer?
A) Minimized
B) Unlimited
C) Unknown

C13.17. API gateway?
A) Cost-aware
B) Not monitored

C13.18. Lambda cold starts?
A) Optimization needed
B) Not a concern

C13.19. Function duration?
A) Optimized
B) Not measured

C13.20. DynamoDB throughput?
A) On-demand + provisioned
B) Provisioned only
C) On-demand only

C13.21. S3 storage class?
A) Intelligent tiering
B) Standard only
C) None

C13.22. CloudFront cache?
A) Optimized
B) Not used

C13.23. Budget alerts?
A) Set + monitored
B) Not set

C13.24. Cost anomalies?
A) ML detection
B) Manual review
C) None

C13.25. Chargeback model?
A) Per team/project
B) Central
C) None

C13.26. Cost forecast?
A) Trending
B) No forecast

C13.27. Discount negotiations?
A) Enterprise discount
B) Standard pricing

C13.28. Multi-cloud strategy?
A) Cost arbitrage
B) Single vendor

C13.29. RI management?
A) Automated
B) Manual
C) None

C13.30. Spot instance risk?
A) Acceptable
B) Avoided
C) Unknown

C13.31. Serverless cost?
A) Predictable
B) Variable
C) Unknown

C13.32. Containerization savings?
A) High density
B) Underutilized
C) None

C13.33. Infrastructure waste?
A) <10% waste
B) 10-20%
C) >20%
D) Unknown

C13.34. Cost tracking?
A) Granular tags
B) Basic grouping
C) None

C13.35. Cost optimization tool?
A) CloudHealth/Flexera
B) Native provider tools
C) None

C13.36. Vendor negotiations?
A) Annual
B) Ad-hoc
C) Never

C13.37. Commitment-based discount?
A) 1/3-year commitments
B) On-demand

C13.38. Reserved capacity?
A) Purchased ahead
B) On-demand

C13.39. Licensing strategy?
A) BYOL allowed
B) Vendor licensing

C13.40. Total cost of ownership?
A) Tracked
B) Unknown

---

## C14 — DOCUMENTATION & KNOWLEDGE (40 Soru)

C14.1. Architecture documentation?
A) ADRs (Architecture Decision Records)
B) Wiki/Confluence
C) None

C14.2. API documentation?
A) OpenAPI/Swagger
B) Postman
C) Custom
D) None

C14.3. Runbooks?
A) Complete
B) Partial
C) Missing

C14.4. Playbooks?
A) Documented
B) Ad-hoc
C) None

C14.5. Incident templates?
A) Standardized
B) Free-form
C) None

C14.6. Knowledge base?
A) Searchable
B) Hard to find
C) None

C14.7. Code comments?
A) Extensive (WHY)
B) Minimal
C) None

C14.8. README quality?
A) Detailed + updated
B) Basic
C) Outdated

C14.9. Setup guide?
A) Step-by-step
B) Assumes knowledge
C) None

C14.10. Contributing guide?
A) Detailed
B) Basic
C) None

C14.11. Architecture diagram?
A) C4 model
B) ASCII/Mermaid
C) None

C14.12. Deployment guide?
A) Automated
B) Manual steps
C) None

C14.13. Troubleshooting guide?
A) Common issues
B) None

C14.14. Performance guide?
A) Tips documented
B) Not documented

C14.15. Security guide?
A) Secrets management
B) Security best practices
C) None

C14.16. Testing guide?
A) How to test
B) Where to test
C) None

C14.17. Release notes?
A) Automated
B) Manual
C) None

C14.18. Changelog?
A) Detailed
B) Minimal
C) None

C14.19. Migration guide?
A) Step-by-step
B) Incomplete
C) None

C14.20. Version history?
A) Tracked
B) Not tracked

C14.21. Decision log?
A) ADRs
B) Git commits
C) None

C14.22. Team onboarding?
A) Structured program
B) Ad-hoc
C) None

C14.23. Mentoring?
A) Formal program
B) Informal
C) None

C14.24. Knowledge sharing?
A) Regular sessions
B) Ad-hoc
C) None

C14.25. Best practices?
A) Documented
B) Tribal knowledge
C) None

C14.26. Code standards?
A) Style guide
B) Linting rules
C) None

C14.27. PR template?
A) Standardized
B) Free-form
C) None

C14.28. Commit message guide?
A) Conventional commits
B) Free-form
C) None

C14.29. Glossary?
A) Defined terms
B) Not documented

C14.30. Acronyms?
A) Explained
B) Not documented

C14.31. Deployment checklist?
A) Automated
B) Manual
C) None

C14.32. Post-deployment checklist?
A) Documented
B) Ad-hoc
c) None

C14.33. Documentation versioning?
A) Tracked
B) Not tracked

C14.34. Documentation review?
A) Regular audit
B) Ad-hoc
C) Never

C14.35. Documentation tools?
A) Docs-as-code (MDX/AsciiDoc)
B) Wiki (Confluence)
C) Markdown files
D) None

C14.36. Internal docs?
A) Accessible
B) Hard to find
C) None

C14.37. External docs?
A) Public + complete
B) Basic
C) None

C14.38. Video tutorials?
A) Recorded
B) Not available

C14.39. Live demos?
A) Regular
B) Ad-hoc
C) None

C14.40. FAQ?
A) Comprehensive
B) Minimal
C) None

---

## C15 — TEAM & PROCESS (40 Soru)

C15.1. Team structure?
A) Cross-functional
B) Siloed
C) Mixed

C15.2. Team size?
A) <5
B) 5-10
C) 10-20
D) 20+

C15.3. Seniority mix?
A) Balanced
B) Senior-heavy
C) Junior-heavy

C15.4. Remote policy?
A) Fully remote
B) Hybrid
C) On-site

C15.5. Code ownership?
A) Clear CODEOWNERS
B) Shared
C) Unclear

C15.6. Code review process?
A) Mandatory 2+ reviewers
B) 1 reviewer
C) Self-review
D) None

C15.7. Review SLA?
A) <4 hours
B) <1 day
C) >1 day
D) None

C15.8. Pair programming?
A) Regular
B) Occasional
C) None

C15.9. Mob programming?
A) Regular
B) Occasional
C) None

C15.10. Sprint planning?
A) 2-week sprints
B) 1-week sprints
C) Continuous
D) No sprints

C15.11. Standups?
A) Daily
B) 3x weekly
C) Weekly
D) None

C15.12. Retros?
A) Regular (weekly/biweekly)
B) Monthly
C) Never

C15.13. 1-on-1s?
A) Bi-weekly
B) Monthly
C) Ad-hoc
D) None

C15.14. Career development?
A) Growth plan
B) Ad-hoc
C) None

C15.15. Training budget?
A) Available
B) Limited
C) None

C15.16. Conference attendance?
A) Supported
B) Self-funded
c) Not allowed

C15.17. Certifications?
A) Supported
B) Self-funded
C) Not encouraged

C15.18. Internal tech talks?
A) Monthly
B) Ad-hoc
C) None

C15.19. Documentation culture?
A) Encouraged
B) Minimal
C) Not valued

C15.20. Knowledge sharing?
A) Regular
B) Ad-hoc
C) None

C15.21. Decision-making?
A) Consensus
B) Leadership decides
C) Democratic vote

C15.22. Transparency?
A) High (all info shared)
B) Medium (need-to-know)
C) Low (restricted)

C15.23. Psychological safety?
A) High (blameless culture)
B) Medium
C) Low (blame-driven)

C15.24. Experimentation?
A) Encouraged
B) Controlled
C) Discouraged

C15.25. Failure tolerance?
A) Learning opportunity
B) Reviewed
C) Punished

C15.26. Change management?
A) Structured + planned
B) Ad-hoc
C) Chaotic

C15.27. On-call rotation?
A) Fair + documented
B) Ad-hoc
C) Burnout risk

C15.28. On-call compensation?
A) Time off
B) Extra pay
C) No compensation

C15.29. Burnout awareness?
A) Monitored + addressed
B) Ignored

C15.30. Work-life balance?
A) Enforced
B) Flexible
C) Overworked

C15.31. Hiring process?
A) Standardized + fair
B) Ad-hoc
C) Biased

C15.32. Diversity & inclusion?
A) Actively pursued
B) Acknowledged
C) Not a priority

C15.33. Mentoring junior devs?
A) Structured program
B) Ad-hoc
C) Sink-or-swim

C15.34. Tech debt discussions?
A) Regular
B) Ad-hoc
C) Ignored

C15.35. Refactoring time?
A) Scheduled (10-20% sprint)
B) Ad-hoc
C) Never

C15.36. Automation culture?
A) Automate everything
B) Selective automation
C) Manual work

C15.37. DRY principle?
A) Strict enforcement
B) Pragmatic
C) Ignored

C15.38. Testing culture?
A) TDD mandatory
B) Tests expected
C) Tests optional
D) No tests

C15.39. Linting & formatting?
A) Automated + enforced
B) Manual
C) None

C15.40. Performance review?
A) Quarterly + structured
B) Annual
C) Ad-hoc
D) None

---