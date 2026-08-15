---
type: guide
category: agent-registry
title: "CoreMusic — Agent Registry & Coordination Protocol"
date: 2026-08-08
updated: 2026-08-13
status: active
version: 21.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/AGENTS.md"
  source_of_truth:
    - ".ai/AGENTS.md"
    - ".ai/.agents/AGENTS.md"
    - ".ai/CLAUDE.md"
    - ".ai/WORKFLOW.md"
    - ".ai/brain.md"
    - ".ai/index.md"
    - ".ai/keys.md"
    - ".ai/MEMORY.md"
    - ".ai/log.md"
    - ".ai/engine.md"
  architecture:
    - ".ai/ADR/"
    - "Existing project architecture"
    - "Existing codebase patterns"
  project_structure:
    - "coremusic.net/"
    - "shared/"
    - "api.coremusic.net/"
    - "auth.coremusic.net/"
    - "music.coremusic.net/"
    - "admin.coremusic.net/"
    - "home.coremusic.net/"
    - "car.coremusic.net/"
    - "studio.coremusic.net/"
    - "pro.coremusic.net/"
    - "media.coremusic.net/"
    - "download.coremusic.net/"
  decision_priority:
    - "ADR decisions"
    - "Architecture documentation"
    - "Security requirements"
    - "Existing implementation"
    - "User requirements"
  update_policy:
    preserve_existing_structure: true
    require_approval_for:
      - "file rename"
      - "directory move"
      - "architecture change"
      - "database schema change"
      - "security policy change"
  skills:
    - path: ".opencode/skills/ui-code-generator/SKILL.md"
      purpose: "UI/CSS kod üretimi, responsive tasarım, WCAG erişilebilirlik"
    - path: ".opencode/skills/ui-analyzer/SKILL.md"
      purpose: "UI analizi, mevcut tasarım değerlendirme"
    - path: ".opencode/skills/skill-maker/SKILL.md"
      purpose: "Yeni skill oluşturma, skill template sistemi"
    - path: ".opencode/skills/red-team-truth-mode/SKILL.md"
      purpose: "Güvenlik testi, truth mode, adversarial analiz"
    - path: ".opencode/skills/prompt-maker/SKILL.md"
      purpose: "Prompt mühendisliği, AI talimat tasarımı"
    - path: ".opencode/skills/composer-sync/SKILL.md"
      purpose: "Composer dependency yönetimi, vendor senkronizasyonu"
    - path: ".opencode/skills/agent-orchestrator/SKILL.md"
      purpose: "Agent görev dağıtımı, multi-agent koordinasyonu"
    - path: ".opencode/skills/human-mode/SKILL.md"
      purpose: "İnsan modu iletişimi, onay süreçleri"
    - path: ".opencode/skills/hallucination-control/SKILL.md"
      purpose: "Halüsinasyon kontrolü, doğrulama protokolleri"
    - path: ".opencode/skills/database-normalize-maker/SKILL.md"
      purpose: "BCNF normalizasyonu, şema tasarımı"
  templates:
    adr:
      - path: ".ai/.templates/adr/adr-template.md"
        purpose: "Architecture Decision Record şablonu"
      - path: ".ai/.templates/adr/adr-frontend-template.md"
        purpose: "Frontend ADR şablonu"
      - path: ".ai/.templates/adr/adr-database-template.md"
        purpose: "Database ADR şablonu"
      - path: ".ai/.templates/adr/adr-security-template.md"
        purpose: "Security ADR şablonu"
      - path: ".ai/.templates/adr/adr-audio-template.md"
        purpose: "Audio/Hardware ADR şablonu"
      - path: ".ai/.templates/adr/adr-index.md"
        purpose: "ADR navigasyon rehberi"
    backend:
      - path: ".ai/.templates/backend/php-template.md"
        purpose: "PHP 8.4 backend geliştirme şablonu"
      - path: ".ai/.templates/backend/nodejs-template.md"
        purpose: "Node.js 20+ backend geliştirme şablonu"
    frontend:
      - path: ".ai/.templates/frontend/js-template.md"
        purpose: "Vanilla JS ES6+ frontend geliştirme şablonu"
      - path: ".ai/.templates/frontend/css-template.md"
        purpose: "ITCSS 9-layer, BEM CSS şablonu"
    testing:
      - path: ".ai/.templates/testing/phpunit-template.md"
        purpose: "PHPUnit 10+ test şablonu"
      - path: ".ai/.templates/testing/vitest-template.md"
        purpose: "Vitest JS/TS test şablonu"
    infrastructure:
      - path: ".ai/.templates/infrastructure/migration-template.md"
        purpose: "MySQL 9 BCNF migration şablonu"
      - path: ".ai/.templates/infrastructure/docker-template.md"
        purpose: "Docker 24+ Compose v2 şablonu"
      - path: ".ai/.templates/infrastructure/github-actions-template.md"
        purpose: "GitHub Actions CI/CD şablonu"
    documentation:
      - path: ".ai/.templates/documentation/api-doc-template.md"
        purpose: "API dokümantasyon şablonu"
      - path: ".ai/.templates/documentation/security-audit-template.md"
        purpose: "Güvenlik denetimi şablonu"
      - path: ".ai/.templates/documentation/WikiPage-Template.md"
        purpose: "Wiki sayfası şablonu"
    hardware:
      - path: ".ai/.templates/hardware/arduino-template.md"
        purpose: "Arduino/IoT prototipleme şablonu"
      - path: ".ai/.templates/hardware/avr-template.md"
        purpose: "AVR mikrodenetleyici şablonu"
      - path: ".ai/.templates/hardware/pic-template.md"
        purpose: "PIC mikrodenetleyici şablonu"
    query:
      - path: ".ai/.templates/query/Query-Template.md"
        purpose: "SQL sorgu şablonu"
    other:
      - path: ".ai/.templates/other/c-template.md"
        purpose: "C11 GCC embedded/driver şablonu"
      - path: ".ai/.templates/cpp-template.md"
        purpose: "C++20 JUCE/ASIO şablonu"
changelog:
  - version: 21.0.0
    date: 2026-08-13
    changes:
      - Added reference section (skills, templates, project_structure)
      - Updated governance format
---

# CoreMusic — Agent Registry & Coordination Protocol

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]] · [[.templates/index]] · [[.agents/AGENTS.md]]

**Skills:** `.opencode/skills/` (10 skill — Guardrail #16 zorunlu)

---

## 1. Amaç

CoreMusic ekosistemindeki 11 yapay zeka ajanının (Master Orchestrator + 10 uzman) yetki sınırlarını, rollerini, iletişim protokollerini ve kalite standartlarını tanımlayan **Tek Doğruluk Kaynağıdır (SSOT)**.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Tüm agent'ların domain yetkileri ve kısıtlamaları | Teknik uygulama detayları |
| Görev dağıtımı algoritması (Task Dispatch) | İş mantığı |
| Ajanlar arası handover ve eskalasyon protokolü | Veritabanı işlemleri |
| Sağlık kontrolü ve context lock mekanizması | Güvenlik politikası |

---

## 3. Terminoloji

| Terim | Tanım |
|-------|-------|
| **Agent** | CoreMusic ekosisteminde belirli bir alanda uzmanlaşmış yapay zeka birimi |
| **Master Orchestrator (MO)** | Tüm ajanları koordine eden ana kontrol birimi |
| **Domain Boundary** | Her ajanın yalnızca kendi alanında çalışması kuralı |
| **Handover** | Bir ajanın görevi başka bir ajana transfer etmesi |
| **Eskalasyon** | Bir sorunun çözülemediği durumda daha üst seviyeye çıkması |
| **Context Lock** | Eşzamanlı dosya erişimini önlemek için kilitleme mekanizması |
| **Health Check** | Ajanların çalışma durumunu kontrol eden mekanizma |
| **Task Queue** | Görevlerin öncelik sırasıyla beklediği kuyruk |
| **Pre-flight Check** | Görev başlamadan önce yapılan kontroller |

---

## 4. Agent Genel Bakış

| # | Agent | Kod Adı | Domain | Katman | Teknoloji |
|---|-------|---------|--------|--------|-----------|
| 1 | **Master Orchestrator** | `mo` | Görev dağıtımı, koordinasyon | Koordinasyon | Vault System, log.md |
| 2 | **Backend Architect** | `backend` | PHP 8.4 API, routing, middleware | L2 | PHP strict_types, PDO, PageRouter |
| 3 | **UI Designer** | `ui` | Vanilla JS, ITCSS, CSS, responsive | L3 | Vanilla JS ES6+, ITCSS 9-layer |
| 4 | **Security Engineer** | `security` | OWASP, encryption, CSRF, CSP | L1 | Argon2id, AES-256-GCM, APCu |
| 5 | **Data Engineer** | `data` | MySQL 18 BCNF, PDO, migration | L0 | MySQL 9, PDO, BCNF |
| 6 | **Embedded Engineer** | `embedded` | C++20, JUCE, ASIO, DSP | L0 | C++20, JUCE 9, ASIO SDK 2.3.4 |
| 7 | **QA Engineer** | `qa` | Test, coverage, E2E | Cross-cutting | PHPUnit 11, Vitest, Playwright |
| 8 | **DevOps Engineer** | `devops` | CI/CD, Docker, deploy | CI/CD | GitHub Actions, Docker, GitLeaks |
| 9 | **Audio Hardware Engineer** | `audio-hw` | DAC/ADC, PCB, amplifier | HW | PCM3168A, AK4458, Class AB |
| 10 | **DSP Firmware Engineer** | `dsp-fw` | XMOS, PCM3168A, DSP chain | FW | XMOS XU316, I2S, TDM |
| 11 | **Windows Software Engineer** | `win-sw` | WASAPI, driver, platform | PLAT | WASAPI, COM, WinRT, WDK |

---

## 5. Domain Sınırları

| Dosya Tipi | Sorumlu Agent | Diğerleri Erişebilir mi? |
|------------|---------------|--------------------------|
| `*.php` (Controller, Service, Repository) | Backend Architect | ❌ |
| `*.js` (Frontend) | UI Designer | ❌ |
| `*.css` (ITCSS layers) | UI Designer | ❌ |
| `*.sql` (Schema, Migration) | Data Engineer | ❌ |
| `*.cpp` / `*.h` (Audio Engine) | Embedded Engineer | ❌ |
| `*.yml` / `*.yaml` (CI/CD) | DevOps Engineer | ❌ |
| `tests/**/*.php` | QA Engineer | ❌ |
| `tests/**/*.test.js` | QA Engineer | ❌ |
| Security middleware | Security Engineer | ❌ |
| `.env` dosyası | Security Engineer | ❌ |
| `log.md` (audit trail) | Tüm ajanlar (append-only) | ✅ Sadece ekleme |
| `.ai/` vault | MO (koordinasyon) | ✅ Okuma serbest |

**Layer Violation:** L0 → L2/L3 veya L1 → L3 gibi kural ihlalleri tespit edilirse derhal revert + log ERROR.

---

## 6. Keyword → Agent Yönlendirmesi

| Keyword Grubu | Birincil Agent | İkincil Agent |
|---------------|----------------|---------------|
| API, endpoint, routing, middleware, PHP, controller, repository | Backend Architect | Security Engineer |
| CSS, UI, responsive, accessibility, ITCSS, BEM, frontend, design, JS | UI Designer | QA Engineer |
| CSRF, CSP, XSS, OWASP, auth, encryption, security, session, rate limit | Security Engineer | Backend Architect |
| database, SQL, BCNF, migration, query, schema, MySQL, PDO, index | Data Engineer | Backend Architect |
| C++, ASIO, JUCE, audio, DSP, Neva Engine, ring buffer, WASAPI, hardware | Embedded Engineer | DevOps Engineer |
| test, coverage, PHPUnit, Vitest, Playwright, E2E, unit test, integration | QA Engineer | — |
| CI/CD, Docker, deploy, infrastructure, pipeline, monitoring, GitLeaks | DevOps Engineer | QA Engineer |
| vault, documentation, ADR, wiki-link, index, keys, brain | MO (vault-updater) | — |
| template, şablon, şablon, template usage, .templates | Tüm ajanlar (guardrail #16) | MO (koordinasyon) |

---

## 7. Görev Dağıtımı Algoritması

```
Kullanıcı İsteği
  → [1. Analiz] — Keyword çıkarma, domain eşleme
    → [2. Pre-flight Checks] — Bağımlılık, dosya kontrolü
      → [3. Task Assignment] — Doğru ajanı seç ve görev ata
        → [4. Execution] — Ajan görevi yürütür
          → [5. Handover] — Gerekirse diğer ajana transfer
            → [6. Validation] — Çıktıyı doğrula
              → [7. Completion] — Görevi tamamla ve logla
```

### 7.1 Adım 1: Analiz

| Kontrol | Yöntem | Kaynak |
|---------|--------|--------|
| Keyword çıkarma | Routing tablosuna başvur | Bu dosya §6 |
| Domain eşleme | Dosya uzantısı ve içerik analizi | Bu dosya §5 |
| Öncelik belirleme | CRITICAL > HIGH > MEDIUM > LOW | Bu dosya §8 |
| Ajan seçimi | Birincil + ikincil ajan | Bu dosya §6 |

### 7.2 Adım 2: Pre-flight Checks

| Kontrol | Değer | İhlal |
|---------|-------|-------|
| Domain boundary | Doğru ajan | Layer violation → revert |
| Dosya etkileniyor mu? | Eşzamanlı erişim | Context lock |
| Bağımlılık var mı? | Handover gerekli | Transfer başlat |
| Önceki görev başarısız mı? | Retry / escalation | Max 3 retry |

### 7.3 Adım 3: Görev Atama

| Öncelik | Tanım | Timeout | Max Retry |
|---------|-------|---------|-----------|
| CRITICAL | Sistem durması, güvenlik açığı | 5s | 1 |
| HIGH | Kritik işlev kaybı | 15s | 3 |
| MEDIUM | Normal geliştirme görevi | 30s | 3 |
| LOW | İyileştirme, optimizasyon | 60s | 2 |

### 7.4 Adım 4: Yürütme

Ajan görevi yürütür. Kurallar:
- Domain boundary'yi koru
- Zero Code Before Plan uygula
- Çıktıyı standardize et

### 7.5 Adım 5: Handover

Gerekirse diğer ajana transfer. Handover protokolü §9'da tanımlıdır.

### 7.6 Adım 6: Doğrulama

| Kontrol | Değer |
|---------|-------|
| Çıktı formatı | Uygun |
| Domain uyumluluğu | Doğru ajan |
| Cross-reference | Geçerli wiki-link'ler |
| Security | Hassas veri redaction |

### 7.7 Adım 7: Tamamlanma

- Görev tamamlanır
- `log.md`'ye giriş eklenir
- MEMORY.md session state güncellenir
- Gerekirse vault-sync yapılır

---

## 8. Öncelik Seviyeleri

| Öncelik | Tanım | Timeout | Max Retry | Yanıt Süresi |
|---------|-------|---------|-----------|-------------|
| CRITICAL | Sistem durması, güvenlik açığı | 5s | 1 | Anlık |
| HIGH | Kritik işlev kaybı | 15s | 3 | 15s |
| MEDIUM | Normal geliştirme görevi | 30s | 3 | 30s |
| LOW | İyileştirme, optimizasyon | 60s | 2 | 60s |

---

## 9. Handover Protokolü

```
[Kaynak Agent] → [Handover Request] → [Hedef Agent] → [Onay/Red] → [Confirmation]
```

### 9.1 Handover Mesaj Formatı

| Alan | Değer |
|------|-------|
| Konu | Görevin kısa açıklaması |
| Kaynak Agent | Adı |
| Hedef Agent | Adı |
| Öncelik | CRITICAL / HIGH / MEDIUM / LOW |
| Etkilenen Dosyalar | Dosya yolu listesi |
| İstek | Ne yapılması gerektiği |
| Onay Durumu | PENDING / APPROVED / REJECTED |
| Timestamp | `YYYY-MM-DD HH:MM:SS` (UTC) |

### 9.2 Handover Kuralları

| Kural | Değer |
|-------|-------|
| Onay zorunlu | Hedef agent onayı olmadan tamamlanamaz |
| Timeout | 30 saniye |
| Max retry | 3 |
| Red durumunda | MO devreye girer |
| Logging | Tüm handover'lar `log.md`'ye yazılır |

### 9.3 Handover Senaryoları

| Senaryo | Kaynak | Hedef | Öncelik |
|---------|--------|-------|---------|
| Güvenlik açığı tespiti | Backend | Security | CRITICAL |
| DB schema değişikliği | Backend | Data | HIGH |
| Frontend test eksikliği | UI | QA | MEDIUM |
| CI/CD pipeline hatası | DevOps | QA | HIGH |
| Auth middleware değişikliği | Security | Backend | HIGH |
| Audio DSP optimizasyonu | Embedded | DevOps | MEDIUM |
| Vault doküman güncelleme | MO | vault-updater | LOW |
| Security audit | Security | QA | HIGH |

---

## 10. Eskalasyon Protokolü

```
Level 1 (Domain Lead) → Level 2 (Tech Lead) → Level 3 (Arch Lead) → İnsan
```

### 10.1 Eskalasyon Senaryoları

| Senaryo | Başlangıç | Hedef | Timeout |
|---------|-----------|-------|---------|
| Agent aynı dosyayı değiştiremiyor | L1 | L2 | 30s |
| BCNF çelişkisi | L1 (Data) | L2 | 30s |
| CSRF/CSP uyumsuzluğu | L1 (Security) | L2 | 15s |
| ASIO cihaz kaybı | L1 (Embedded) | L2 | 30s |
| Test coverage %80 altı | L1 (QA) | L2 | 60s |
| Deployment başarısız | L1 (DevOps) | L2 | 30s |
| Mimari çelişki (ADR) | L2 | L3 | 60s |
| Güvenlik açığı | L2 | L3 | 15s |
| Sistem durması | L2 | İnsan | Anlık |

### 10.2 Eskalasyon Kuralları

| Kural | Değer |
|-------|-------|
| L1 timeout | 30 saniye |
| L2 timeout | 60 saniye |
| L3 timeout | 120 saniye |
| Max retry | 3 her seviyede |
| İnsan müdahalesi | Son çare |

---

## 11. Sağlık Kontrolü

### 11.1 Sağlık Parametreleri

| Parametre | Değer |
|-----------|-------|
| Timeout | 30 saniye |
| Max Retry | 3 |
| Check Interval | Her görev başında |
| Heartbeat | 10 saniye |

### 11.2 Sağlık Durumları

| Durum | Kod | Açıklama |
|-------|-----|----------|
| Healthy | 200 | Görev tamamlandı |
| Degraded | 301 | Yavaş yanıt (>15s) |
| Retry | 408 | Timeout, yeniden deneniyor |
| Failed | 500 | 3 retry başarısız, queue reset |
| Dead | 503 | Yanıt yok, escalation |

### 11.3 Sağlık Kontrolü Akışı

```
Görev başlangıcı
  → Health check tetikle
    → Durum kontrolü
      → Healthy → devam
      → Degraded → uyar, devam
      → Retry → yeniden dene (max 3)
      → Failed → queue reset, escalation
      → Dead → derhal escalation
```

---

## 12. Context Lock

Eşzamanlı erişimi önlemek için dosya kilitleme mekanizması.

### 12.1 Lock Kuralları

| Kurallar | Değer |
|---------|-------|
| Kilitleme süresi | Max 30 saniye |
| Deadlock prevention | MO en eski kilidi kırar |
| Öncelik | CRITICAL > HIGH > MEDIUM > LOW |
| Logging | Lock acquire/release `log.md`'ye yazılır |

### 12.2 Lock Akışı

```
Ajan dosyaya erişmek ister
  → Lock acquire (max 30s bekleme)
    → Başarılı → dosyayı düzenle
    → Başarısız → kuyruk → öncelik sırası
      → Timeout → escalation
```

### 12.3 Deadlock Önleme

| Yöntem | Açıklama |
|--------|----------|
| Timeout | Max 30s sonra lock serbest |
| Priority override | CRITICAL diğer kilidi kırar |
| MO intervention | MO en eski kilidi kırar |
| Queue reset | Tüm kilitler sıfırlanır |

---

**Kurallar:**
1. P0 → P1 → P2 → P3 sırasıyla okunur
2. Fallback: `index.md`
3. Token aşımı önlenir: gereksiz dosya okunmaz
4. **İstisna:** Görsel referanslar (`.ai/ui-design/screens/**`, `.ai/.png/**`)

**Mockup Before Frontend:** CSS/HTML/JS/layout/bileşen görevlerinde `.ai/ui-design/` altındaki ilgili görsel okunmadan kod yazılamaz. Görsel okunamıyorsa DUR ve bildir.

---

## 14. Zorunlu 5 Skills (ADR-042/C4)

| # | Skill | Amaç | Kullanım |
|---|-------|------|----------|
| 1 | `/prompt-maker` | Prompt üretim motoru | Her görev başlangıcında |
| 2 | `/brainstorming` | Fikir üretimi ve keşif | Yaratıcı work öncesi |
| 3 | `/vault-sync` | Vault senkronizasyonu | Seans sonunda |
| 4 | `/hallucination-control` | Halüsinasyon doğrulama | Kod yazma öncesi |
| 5 | `Red Team · Truth Mode · Human Mode` | Her zaman aktif | Sürekli |
| 6 | `Template Mandatory` | Yeni dosya için template zorunlu (Guardrail #16) | Her dosya oluşturmada |

### 14.1 Prompt-Agent Eşleştirme Tablosu

| Prompt | Birincil Agent | İkincil Agent | Kullanım Anı |
|--------|----------------|---------------|-------------|
| prompt0 (Genel) | MO (dağıtıyor) | Tüm agentlar | Her görev başında zorunlu |
| prompt1 (SPA Router) | Backend Architect | UI Designer | SPA route tasarımında |
| prompt2 (Auth) | Security Engineer | Backend Architect | Auth middleware'de |
| prompt3 (API) | Backend Architect | DevOps Engineer | API gateway'de |

**Kural:** prompt0 her zaman okunur. prompt1-3 sadece ilgili domain görevlerinde okunur.

---

## 15. Agent Detayları

### 15.1 Master Orchestrator

| Özellik | Değer |
|---------|-------|
| Katman | Koordinasyon |
| Sorumluluk | Görev dağıtımı, handover, eskalasyon, loglama |
| Karar Yetkisi | Task assignment, priority setting, handover authorization |
| Yasak | Doğrudan kod yazma (sadece koordinasyon) |
| Dosya Erişimi | Tüm `.ai/` vault'u okuyabilir, `log.md`'ye yazabilir |
| Başlatma | Her oturumun başında aktif olur |
| Kapanış | Tüm görevler tamamlandığında otomatik kapanır |
| İlgili Template'ler | [[../.templates/index]] |

### 15.2 Backend Architect

| Özellik | Değer |
|---------|-------|
| Katman | L2 (Routing) |
| Teknoloji | PHP 8.4 (strict_types), PDO, Slim/vanilla router |
| Sorumluluk | API endpoint'leri, middleware pipeline, routing |
| Yasak | Frontend kodu, DB şeması tasarımı, security testi |
| Dosya Erişimi | `*.php`, `*.json` (composer), `*.ini` |
| Test Gereksinimi | PHPUnit 11, ≥%80 coverage |

**Zorunlu Kurallar:** `declare(strict_types=1)` her dosyada, PDO prepared statement, explicit column list, hardcoded secret yasak.
**İlgili Template'ler:** `backend/php-template.md`, `adr/adr-template.md` → [[../.templates/index]]

### 15.3 UI Designer

| Özellik | Değer |
|---------|-------|
| Katman | L3 (Presentation) |
| Teknoloji | Vanilla JS ES6+, ITCSS 9-layer, BEM/BEMIT, TrustedTypes |
| Sorumluluk | Frontend kodlama, CSS mimarisi, responsive, accessibility |
| Yasak | PHP backend kodu, DB sorgusu, security konfigürasyonu |
| Dosya Erişimi | `*.js`, `*.css`, `*.html`, `*.svg` |
| Test Gereksinimi | Vitest, ≥%80 coverage |

**Zorunlu Kurallar:** `innerHTML` yasak (DOMParser + TrustedTypes), framework yasak (ADR-001), `var` yasak, `eval()` yasak, BEM format zorunlu.
**İlgili Template'ler:** `frontend/js-template.md`, `frontend/css-template.md`, `adr/adr-frontend-template.md` → [[../.templates/index]]

### 15.4 Security Engineer

| Özellik | Değer |
|---------|-------|
| Katman | L1 (Security) |
| Teknoloji | OWASP Top 10:2025, Argon2id, AES-256-GCM, APCu |
| Sorumluluk | Güvenlik middleware'leri, şifreleme, CSRF, CSP, rate limiting |
| Yasak | Backend/Frontend kodu, DB şeması tasarımı |
| Dosya Erişimi | Security middleware, `.env`, credential vault |
| Test Gereksinimi | OWASP checklist, penetration test |

**Zorunlu Kurallar:** `csrf_token` key (ADR-010), `hash_equals()` timing-safe, Argon2id (64MB/4/2), credential vault AES-256-GCM, loglarda redaction.
**İlgili Template'ler:** `adr/adr-security-template.md`, `documentation/security-audit-template.md` → [[../.templates/index]]

### 15.5 Data Engineer

| Özellik | Değer |
|---------|-------|
| Katman | L0 (Infrastructure) |
| Teknoloji | MySQL 9, SQLite, PDO, BCNF |
| Sorumluluk | 18 BCNF DB yönetimi, schema tasarımı, migration, query optimization |
| Yasak | PHP/JS kodu, security politikası |
| Dosya Erişimi | `*.sql`, `*.php` (migration), `*.json` (schema) |
| Test Gereksinimi | Schema validation, BCNF audit |

**Zorunlu Kurallar:** ORM yasak (ADR-002), SELECT * yasak, BCNF zorunlu, soft delete, prepared statement.
**İlgili Template'ler:** `adr/adr-database-template.md`, `query/Query-Template.md`, `infrastructure/migration-template.md` → [[../.templates/index]]

### 15.6 Embedded Engineer

| Özellik | Değer |
|---------|-------|
| Katman | L0 (Hardware) |
| Teknoloji | C++20, JUCE 9, ASIO SDK 2.3.4, XMOS XU316 |
| Sorumluluk | C++ ses motoru, DSP, donanım sürücüleri, ASIO/WASAPI |
| Yasak | PHP/JS/SQL kodu |
| Dosya Erişimi | `*.cpp`, `*.h`, `*.cmake`, `*.json` (vcpkg) |
| Test Gereksinimi | Google Test, ≥%80 coverage |

**Zorunlu Kurallar:** Zero-allocation, lock-free, noexcept, cache-line alignment, PCM5122 yasak (H001).
**İlgili Template'ler:** `other/c-template.md`, `adr/adr-audio-template.md` → [[../.templates/index]]

### 15.7 QA Engineer

| Özellik | Değer |
|---------|-------|
| Katman | Cross-cutting |
| Teknoloji | PHPUnit 11, Vitest, Playwright 1.40, Google Test |
| Sorumluluk | Test coverage ≥80%, unit/integration/E2E, kalite güvencesi |
| Yasak | Production kodu, DB tasarımı, security politikası |
| Dosya Erişimi | `tests/**/*.php`, `tests/**/*.test.js`, `*.test.cpp` |
| Test Piramidi | Unit %70, Integration %20, E2E %10 |
| İlgili Template'ler | `testing/phpunit-template.md`, `testing/vitest-template.md` → [[../.templates/index]] |

### 15.8 DevOps Engineer

| Özellik | Değer |
|---------|-------|
| Katman | CI/CD |
| Teknoloji | GitHub Actions, Docker, GitLeaks, PowerShell/Bash |
| Sorumluluk | CI/CD pipeline, container yönetimi, monitoring |
| Yasak | Application kodu, DB tasarımı, security politikası |
| Dosya Erişimi | `*.yml`, `*.yaml`, `Dockerfile`, `*.sh`, `*.ps1` |
| Test Gereksinimi | Pipeline test, smoke test |

**Zorunlu Kurallar:** GitLeaks her commit'te, health check tüm servislerde, rollback stratejisi tanımlı.
**İlgili Template'ler:** `infrastructure/docker-template.md`, `infrastructure/github-actions-template.md` → [[../.templates/index]]

### 15.9 Audio Hardware Engineer

| Özellik | Değer |
|---------|-------|
| Katman | HW (Hardware) |
| Teknoloji | PCM3168A, AK4458, Class AB amplifer, PCB design |
| Sorumluluk | DAC/ADC tasarımı, amplifer devresi, PCB layout, donanım testi |
| Yasak | Yazılım kodu, DB tasarımı |
| Dosya Erişimi | `.ai/electronic/`, `*.kicad_sch`, `*.kicad_pcb` |
| İlgili Template'ler | `hardware/arduino-template.md`, `hardware/avr-template.md`, `adr/adr-audio-template.md` → [[../.templates/index]] |

### 15.10 DSP Firmware Engineer

| Özellik | Değer |
|---------|-------|
| Katman | FW (Firmware) |
| Teknoloji | XMOS XU316, I2S, TDM, RTOS, C++20 |
| Sorumluluk | DSP firmware geliştirme, I2S/TDM iletişimi, crossover, EQ, dynamics |
| Yasak | PHP/JS kodu, DB tasarımı |
| Dosya Erişimi | `*.cpp`, `*.h`, `*.xn`, `*.xe` |
| İlgili Template'ler | `other/c-template.md`, `hardware/avr-template.md` → [[../.templates/index]] |

### 15.11 Windows Software Engineer

| Özellik | Değer |
|---------|-------|
| Katman | PLAT (Platform) |
| Teknoloji | WASAPI, COM, WinRT, WDK, C++20 |
| Sorumluluk | Windows ses sürücüsü, WASAPI entegrasyonu, driver imzalama |
| Yasak | PHP/JS kodu, DB tasarımı |
| Dosya Erişimi | `*.cpp`, `*.h`, `*.inf`, `*.cat` |
| İlgili Template'ler | `other/c-template.md` → [[../.templates/index]] |

---

## 16. Kalite Standartları (Agent Başına)

| Agent | Standart | Hedef |
|-------|----------|-------|
| Backend | strict_types, PSR-12, prepared statement | %100 |
| UI | ITCSS uyum, BEM namespace, WCAG 2.2 AA | %100 |
| Security | OWASP Top 10, CSRF=`csrf_token`, Argon2id | %100 |
| Data | BCNF, no ORM, no SELECT *, prepared | %100 |
| Embedded | Zero-allocation, lock-free, noexcept | %100 |
| QA | Test coverage ≥80%, flaky test %0 | ≥80% |
| DevOps | CI/CD success ≥95%, GitLeaks clean | ≥95% |

---

## 17. Edge Cases

| # | Senaryo | Çözüm |
|---|---------|-------|
| 1 | Aynı dosyaya eşzamanlı erişim | Context Lock + Queue |
| 3 | Sensitive data log'da | `[REDACTED]` ile maskeleme |
| 4 | Ajan timeout (30s+) | Max 3 retry, sonra queue reset |
| 5 | Bilinmeyen class/API | `// ⚠️ VERIFICATION REQUIRED` |
| 6 | ASIO device loss | WASAPI fallback |
| 7 | Layer violation | Derhal revert + log ERROR |
| 8 | PCM5122 kullanımı | PCM3168A / AK4458 öner |
| 9 | Network outage | Offline-First + SQLite queue |
| 10 | Vault corruption | `git checkout` + son commit |

---

## 18. Uyarılar

| # | Uyarı | Sonuc |
|---|-------|-------|
| 1 | Domain boundary ihlali | Sistem durur, MO müdahale eder |
| 2 | Agent timeout 30s+ | Max 3 retry, sonra escalation |
| 3 | Hallüsinasyon | `VERIFICATION REQUIRED` etiketi |
| 4 | Token overflow | Görev başarısız |
| 5 | Vault bozulması | Git ile kurtarma |
| 6 | Layer violation | Kod revert edilir |
| 7 | ORM kullanımı | SQL injection riski |
| 8 | Framework kullanımı | Bağımlılık artışı |

---

## 19. İleriye Yönelik Yol Haritası

| Versiyon | Özellik |
|----------|---------|
| v19.0 | Semantic Agent Routing (mevcut) |
| v20.0 | Self-Healing Agents |
| v21.0 | Multi-Agent Learning |
| v22.0 | Tam Otonom Çalışma (Zero Human Intervention) |
| v23.0 | Cross-Project Memory (WirelessConnect) |

---

## 20. İlgili Dokümanlar

| Dosya | Amaç |
|-------|------|
| [[CLAUDE.md]] | Ana sözleşme, AI anayasası |
| [[WORKFLOW.md]] | Süreçler, fazlar |
| [[index.md]] | Master katalog |
| [[keys.md]] | Keyword haritası |
| [[brain.md]] | Mimari kararlar |
| [[MEMORY.md]] | Session hafızası |
| [[log.md]] | Audit trail |
| [[engine.md]] | Orkestrasyon motoru indeksi |

---

## 21. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 5 Domain | [[CLAUDE.md]] §5 | L0-L6 katmanları |
| § 6 Routing | [[engine.md]] §2 | Orkestrasyon bölümleri |
| § 9 Handover | [[WORKFLOW.md]] §7.6 | Session init |
| § 10 Eskalasyon | [[ADR-008-bypass-auth-middleware]] | Auth bypass |
| § 15 Agent | [[.agents/AGENTS.md]] | Agent profilleri |
| § 17 Edge | [[ADR-017-dsp-hardware-mode]] | ASIO/WASAPI |

---

## 22. Sözlük

| Terim | Tanım |
|-------|-------|
| **Agent** | Belirli bir alanda uzmanlaşmış AI birimi |
| **MO** | Master Orchestrator — Koordinasyon birimi |
| **Handover** | Görev transferi |
| **Eskalasyon** | Seviye yukarı çıkarma |
| **Context Lock** | Dosya kilitleme |
| **Health Check** | Sağlık kontrolü |
| **Task Queue** | Görev kuyruğu |
| **Pre-flight** | Görev öncesi kontrol |
| **Domain Boundary** | Alan sınırları |
| **Layer Violation** | Katman ihlali |
| **Deadlock** | Kilitleme çelişkisi |
| **Retry** | Yeniden deneme |
| **Heartbeat** | Sağlık atışı |

---

## 23. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 21.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 23 |
| Agent Count | 11 (1 MO + 10 specialist) |
| Domain Boundaries | 12 dosya tipi |
| Routing Rules | 11 keyword grubu |
| Edge Cases | 10 |
| Mandatory Skills | 5 |
| Handover Scenarios | 8 |
| Escalasyon Senaryoları | 9 |
| Health States | 5 |
| Lock Rules | 4 |
| Quality Standards | 7 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-12
**Mode:** Red Team · Human Mode · Truth Mode