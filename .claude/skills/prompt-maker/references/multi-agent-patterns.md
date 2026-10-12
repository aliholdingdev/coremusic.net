# MULTI-AGENT PATTERNS — TAM REHBER
# Prompt Maker v7.2.0 | 2026-06-11 | Kiro IDE Native
# Orchestration, coordination, fault tolerance, communication protocols

---

## TEMEL KAVRAMLAR

```
Orchestrator Agent  → Koordinasyon, görev dağıtımı, sonuç birleştirme
Specialist Agent    → Domain-specific görev (Backend, Security, QA, UX)
Worker Agent        → Atomik görev (dosya yaz, test çalıştır, lint)
Monitor Agent       → Kalite kontrol, hata tespiti, raporlama
```

### Kiro IDE Multi-Agent Desteği

```javascript
// invokeSubAgent ile agent çağırma
{
  name: "context-gatherer",     // Codebase analizi
  name: "general-task-execution", // Genel görev
  name: "custom-agent-creator",   // Yeni agent oluşturma
  name: "requirement-detailer",   // Gereksinim detaylandırma
}

// contextFiles ile bağlam verme
contextFiles: [
  { path: "/project/src/auth.php", startLine: 1, endLine: 50 },
  { path: "/project/tests/AuthTest.php" }
]
```

---

## PATTERN 1 — SEQUENTIAL PIPELINE (Sıralı Boru Hattı)

### Ne Zaman Kullanılır?
- Her adım bir öncekinin çıktısına bağımlı
- Spec workflow (Requirements → Design → Tasks → Implementation)
- Veri dönüşüm pipeline'ları
- Onay gerektiren süreçler

### Diyagram
```
Kullanıcı İsteği
  ↓
[Agent 1: Planner]
  Input:  Kullanıcı isteği
  Output: Plan + görev listesi
  ↓
[Agent 2: Developer]
  Input:  Plan
  Output: Kod + testler
  ↓
[Agent 3: QA]
  Input:  Kod
  Output: Test raporu + coverage
  ↓
[Agent 4: Security]
  Input:  Kod + test raporu
  Output: Güvenlik raporu + öneriler
  ↓
[Orchestrator: Merge]
  Input:  Tüm çıktılar
  Output: Final deliverable
```

### Prompt Şablonu
```markdown
# SEQUENTIAL PIPELINE ORCHESTRATOR

## ROL
Sen [proje adı] sequential pipeline orchestrator'ısın.
4 specialist agent'ı sırayla koordine edersin.

## PIPELINE TANIMI

### Agent 1: Planner
- Görev: Kullanıcı isteğini analiz et, görev listesi oluştur
- Input: Kullanıcı isteği (string)
- Output: { tasks: [], priorities: [], dependencies: [] }
- Timeout: 60 saniye
- Retry: 2x

### Agent 2: Developer
- Görev: Plan'a göre kod yaz
- Input: Planner output
- Output: { files: [], tests: [], documentation: [] }
- Timeout: 300 saniye
- Retry: 1x

### Agent 3: QA
- Görev: Kodu test et
- Input: Developer output
- Output: { passed: bool, coverage: %, issues: [] }
- Timeout: 120 saniye
- Retry: 2x

### Agent 4: Security
- Görev: OWASP Top 10:2025 audit
- Input: Developer output
- Output: { vulnerabilities: [], risk_level: "low|medium|high|critical" }
- Timeout: 120 saniye
- Retry: 1x

## HANDOFF PROTOKOLÜ
1. Her agent tamamlandıktan sonra output doğrula
2. Output geçersizse → retry
3. Retry başarısızsa → pipeline durdur + kullanıcıya raporla
4. Tüm agent'lar başarılıysa → merge + final output

## HATA YÖNETİMİ
- Agent timeout → retry 1x → fallback (basit versiyon) → kullanıcıya bildir
- Agent conflict → orchestrator çözer (son karar orchestrator'da)
- Critical error → pipeline durdur + rollback + kullanıcıya raporla

## ÇIKTI FORMATI
{
  "status": "success | partial | failed",
  "pipeline_id": "uuid",
  "agents_completed": ["planner", "developer", "qa", "security"],
  "final_output": { ... },
  "warnings": [],
  "errors": []
}
```

---

## PATTERN 2 — PARALLEL FAN-OUT (Paralel Yayılma)

### Ne Zaman Kullanılır?
- Bağımsız görevler paralel çalışabilir
- Zaman optimizasyonu kritik
- Farklı domain'lerde eş zamanlı analiz

### Kiro Kısıtı
```
⚠️ Kiro IDE spec workflow'da paralel subagent YASAK.
   Sadece bağımsız, non-spec görevler için kullanılabilir.
```

### Diyagram
```
Orchestrator
  ├──→ [Agent A: Backend Analysis]  (bağımsız)
  ├──→ [Agent B: Frontend Analysis] (bağımsız)
  ├──→ [Agent C: Security Analysis] (bağımsız)
  └──→ [Agent D: Performance Analysis] (bağımsız)
         ↓ (tümü tamamlandıktan sonra)
  [Orchestrator: Merge & Synthesize]
         ↓
  Final Report
```

### Prompt Şablonu
```markdown
# PARALLEL FAN-OUT ORCHESTRATOR

## ROL
Sen [proje adı] parallel analysis orchestrator'ısın.
4 bağımsız agent'ı paralel çalıştırır, sonuçları birleştirirsin.

## PARALLEL TASKS

### Agent A: Backend Analysis
- Görev: PHP/API katmanını analiz et
- Bağımlılık: YOK (bağımsız)
- Output: { issues: [], recommendations: [], severity: [] }

### Agent B: Frontend Analysis
- Görev: JS/CSS katmanını analiz et
- Bağımlılık: YOK (bağımsız)
- Output: { issues: [], recommendations: [], severity: [] }

### Agent C: Security Analysis
- Görev: OWASP Top 10:2025 taraması
- Bağımlılık: YOK (bağımsız)
- Output: { vulnerabilities: [], risk_level: string }

### Agent D: Performance Analysis
- Görev: Response time, cache, query analizi
- Bağımlılık: YOK (bağımsız)
- Output: { bottlenecks: [], optimizations: [] }

## MERGE PROTOKOLÜ
1. Tüm agent'lar tamamlanana kadar bekle
2. Partial failure: Tamamlanan agent'ların sonuçlarını kullan
3. Çakışma: Severity'ye göre önceliklendir
4. Final report: Tüm bulguları birleştir, öncelik sırasına koy

## ÇAKIŞMA ÇÖZÜMÜ
- Aynı issue farklı agent'lardan gelirse → tek kayıt, çoklu kaynak
- Çelişkili öneri → her ikisini listele, kullanıcıya seçtir
- Severity çakışması → en yüksek severity'yi kullan
```

---

## PATTERN 3 — HIERARCHICAL DELEGATION (Hiyerarşik Delegasyon)

### Ne Zaman Kullanılır?
- Büyük, karmaşık projeler
- Domain'ler arası koordinasyon gerekli
- Alt-orchestrator'lar kendi domain'lerini yönetir

### Diyagram
```
Master Orchestrator
  ├── Sub-Orchestrator: Backend
  │     ├── Worker: API Developer
  │     ├── Worker: DB Optimizer
  │     └── Worker: Cache Manager
  ├── Sub-Orchestrator: Frontend
  │     ├── Worker: UI Developer
  │     ├── Worker: CSS Architect
  │     └── Worker: JS Developer
  └── Sub-Orchestrator: Quality
        ├── Worker: Unit Tester
        ├── Worker: Integration Tester
        └── Worker: Security Auditor
```

### Prompt Şablonu
```markdown
# HIERARCHICAL MASTER ORCHESTRATOR

## ROL
Sen [proje adı] master orchestrator'ısın.
3 sub-orchestrator'ı koordine edersin.

## SUB-ORCHESTRATOR TANIMI

### Sub-Orchestrator: Backend
- Domain: PHP, MySQL, Redis, API
- Workers: API Developer, DB Optimizer, Cache Manager
- Autonomy: Backend kararlarını bağımsız alır
- Escalation: Mimari karar → Master'a eskalasyon

### Sub-Orchestrator: Frontend
- Domain: JS, CSS, SPA Router, UI
- Workers: UI Developer, CSS Architect, JS Developer
- Autonomy: Frontend kararlarını bağımsız alır
- Escalation: API contract değişikliği → Master'a eskalasyon

### Sub-Orchestrator: Quality
- Domain: Testing, Security, Performance
- Workers: Unit Tester, Integration Tester, Security Auditor
- Autonomy: Test stratejisini bağımsız belirler
- Escalation: Critical vulnerability → Master'a eskalasyon

## ESCALATION PROTOKOLÜ
Worker hata → Sub-orchestrator'a eskalasyon (retry 2x)
Sub-orchestrator hata → Master'a eskalasyon (retry 1x)
Master hata → Kullanıcıya raporla + rollback

## INTER-AGENT COMMUNICATION
- Backend ↔ Frontend: API contract (OpenAPI spec)
- Backend ↔ Quality: Test fixtures, mock data
- Frontend ↔ Quality: E2E test scenarios
- Tüm → Master: Status updates (her 5 dakikada)
```

---

## PATTERN 4 — REVIEW & APPROVE (İnceleme ve Onay)

### Ne Zaman Kullanılır?
- Kritik değişiklikler onay gerektirir
- Code review workflow
- Security-sensitive operasyonlar

### Diyagram
```
Developer Agent → [Kod üretir]
  ↓
Reviewer Agent → [İnceler]
  ├── APPROVE → Deploy Agent → [Deploy eder]
  └── REJECT  → Developer Agent → [Düzeltir]
                    ↓ (max 3 iterasyon)
                Escalate → Human Review
```

### Prompt Şablonu
```markdown
# REVIEW & APPROVE ORCHESTRATOR

## ROL
Sen code review pipeline orchestrator'ısın.
Developer → Reviewer → Deploy döngüsünü yönetirsin.

## REVIEW KRİTERLERİ

### Otomatik APPROVE Koşulları
- SOLID violations: 0
- Security issues (critical/high): 0
- Test coverage: ≥ 90%
- Code complexity: ≤ 10 (cyclomatic)
- Documentation: tam

### Otomatik REJECT Koşulları
- SQL injection riski
- Hardcoded credentials
- Missing auth
- Test coverage < 70%
- Critical OWASP violation

### Human Review Gerektiren Durumlar
- Architecture değişikliği
- Breaking change
- Security model değişikliği
- 3. iterasyonda hâlâ REJECT

## İTERASYON LİMİTİ
Max 3 iterasyon → Human review
```

---

## COREMUSIC AGENCY-AGENT PROFİLLERİ

```
.kiro/skills/agency-agents/

engineering-backend-architect.md
  Domain: PHP 8.x, MySQL, Redis, Handler/Service/Repository
  Aktivasyon: "backend", "PHP", "API", "veritabanı"

engineering-frontend-developer.md
  Domain: Vanilla JS, IIFE, CSS, ITCSS, --cm-* tokens
  Aktivasyon: "frontend", "JS", "CSS", "SPA", "UI"

engineering-security-engineer.md
  Domain: OWASP Top 10:2025, Auth, CSRF, XSS, SQL injection
  Aktivasyon: "güvenlik", "security", "OWASP", "auth"

engineering-database-optimizer.md
  Domain: MySQL 8.0, PDO, N+1, query optimization, indexing
  Aktivasyon: "veritabanı", "MySQL", "query", "performans"

engineering-devops-automator.md
  Domain: IIS, Apache, deployment, CI/CD, monitoring
  Aktivasyon: "deployment", "CI/CD", "DevOps", "altyapı"

engineering-embedded-firmware-engineer.md
  Domain: C++, DSP, miniaudio, KFR, STM32, RTOS
  Aktivasyon: "gömülü", "firmware", "DSP", "audio", "C++"

design-ux-architect.md
  Domain: WCAG 2.2, ITCSS, glassmorphism, component states
  Aktivasyon: "UX", "tasarım", "accessibility", "UI/UX"

engineering-code-reviewer.md
  Domain: SOLID, Clean Code, security audit, refactoring
  Aktivasyon: "code review", "refactor", "SOLID", "kalite"
```

---

## FAULT TOLERANCE (HATA TOLERANSI)

### Circuit Breaker Pattern
```
Agent çağrısı başarısız olursa:
  1. İlk hata → retry (1x)
  2. İkinci hata → circuit OPEN (agent devre dışı)
  3. 30 saniye sonra → circuit HALF-OPEN (test isteği)
  4. Test başarılı → circuit CLOSED (normal)
  5. Test başarısız → circuit OPEN (tekrar)

Fallback stratejisi:
  - Basit versiyon üret (daha az özellik)
  - Cache'den önceki sonucu kullan
  - Kullanıcıya bildir + manuel devam et
```

### Retry Strategy
```
Retry politikası:
  Max retry: 3
  Backoff: exponential (1s, 2s, 4s)
  Jitter: ±500ms (thundering herd önleme)
  Timeout: 60s (her deneme)

Retry edilmeyecek durumlar:
  - 4xx HTTP errors (client error)
  - Validation failures
  - Security violations
  - Prompt injection attempts
```

### Partial Failure Handling
```
Senaryo: 4 agent'tan 3'ü başarılı, 1'i başarısız

Strateji:
  1. Başarılı agent'ların sonuçlarını kullan
  2. Başarısız agent'ın görevini basitleştir
  3. Kullanıcıya partial result bildir
  4. Eksik kısım için manual tamamlama öner

Örnek mesaj:
  "3/4 agent tamamlandı. Security analysis başarısız oldu.
   Mevcut sonuçlar: [Backend ✅, Frontend ✅, QA ✅, Security ❌]
   Security analizi manuel olarak yapılması önerilir."
```

---

## AGENT COMMUNICATION PROTOCOL

### Message Format
```json
{
  "message_id": "uuid",
  "from_agent": "planner",
  "to_agent": "developer",
  "timestamp": "2026-05-29T00:00:00Z",
  "type": "task | result | error | status",
  "payload": {
    "task_id": "uuid",
    "content": { ... },
    "metadata": {
      "priority": "low | medium | high | critical",
      "timeout_ms": 60000,
      "retry_count": 0
    }
  }
}
```

### Status Updates
```json
{
  "agent": "developer",
  "status": "running | completed | failed | waiting",
  "progress": 75,
  "current_task": "Writing UserRepository.php",
  "estimated_completion_ms": 30000
}
```

### Error Format
```json
{
  "agent": "security",
  "error_type": "timeout | validation | execution | dependency",
  "error_message": "Agent timed out after 60s",
  "retry_count": 2,
  "fallback_available": true,
  "escalate_to": "orchestrator"
}
```

---

## MULTI-AGENT PROMPT ŞABLONu (TAM — 20 BÖLÜM)

```markdown
# MASTER PROMPT — [Proje Adı] Multi-Agent System
# Version: X.Y.Z | Tarih: YYYY-MM-DD
# Pattern: Sequential | Parallel | Hierarchical | Review

## BÖLÜM 01 — SYSTEM IDENTITY
Sen [proje adı] multi-agent orchestrator'ısın.
[N] specialist agent'ı koordine edersin.
Deneyim: Senior Principal Orchestration Engineer.

## BÖLÜM 02 — AGENT ROSTER
| Agent | Rol | Domain | Timeout |
|-------|-----|--------|---------|
| Planner | Görev planlama | Tüm | 60s |
| Developer | Implementasyon | [Stack] | 300s |
| QA | Test | Testing | 120s |
| Security | Audit | OWASP | 120s |

## BÖLÜM 03 — COORDINATION PATTERN
Pattern: [Sequential | Parallel | Hierarchical]
Açıklama: [Neden bu pattern seçildi?]

## BÖLÜM 04 — COMMUNICATION PROTOCOL
[Message format, status updates, error format]

## BÖLÜM 05 — FAULT TOLERANCE
[Circuit breaker, retry, partial failure]

## BÖLÜM 06 — SECURITY MODEL
[Agent arası güvenlik, injection prevention]

## BÖLÜM 07 — PERFORMANCE TARGETS
[Her agent için timeout, throughput hedefleri]

## BÖLÜM 08 — RULES ENGINE
[Hard + soft rules, agent-specific constraints]

## BÖLÜM 09 — ESCALATION PROTOCOL
[Ne zaman, kime, nasıl eskalasyon?]

## BÖLÜM 10 — MERGE STRATEGY
[Çıktıları nasıl birleştir, çakışmaları nasıl çöz?]

[... 20 bölüm devam eder ...]
```

---

*Multi-Agent Patterns v7.0.0 — 2026-05-29 | Kiro IDE Native*
*Sequential, Parallel, Hierarchical, Review & Approve patterns*
*Fault tolerance, circuit breaker, retry, partial failure handling*
