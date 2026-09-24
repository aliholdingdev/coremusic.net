---
title: "CoreMusic — Agent Registry & Coordination Protocol"
type: guide
category: agent-registry
version: 22.0.3
status: active
authority: SSOT
updated: 2026-09-24
---

# CoreMusic — Agent Registry & Coordination Protocol

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]] · [[VISION.md]] · [[PROJECTS.md]] · [[.templates/index]] · [[.agents/AGENTS.md]]

**Skills:** `.opencode/skills/` (8 aktif skill — arşiv kaldırıldı, 20 benzersiz dosya _archive-keep/ — Guardrail #16 zorunlu)

---

## Purpose

### §1 Purpose

CoreMusic ekosistemindeki 11 yapay zeka ajanının (Master Orchestrator + 10 uzman); ses analizinden donanım optimizasyonuna, bilgi bankası/RAG yönetiminden prompt üretimine kadar tüm AI süreçlerini koordine eden yetki sınırlarını, rollerini, iletişim protokollerini ve kalite standartlarını tanımlayan **Tek Doğruluk Kaynağıdır (SSOT)**.
- **Ekosistem Vizyonu & Mülkiyet Felsefesi:** [[VISION.md]]
- **Proje Tanımı & 10 Temel Yetenek:** [[PROJECTS.md]]

---

## Scope

### §2 Scope

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Tüm agent'ların domain yetkileri ve kısıtlamaları | Teknik uygulama detayları |
| Görev dağıtımı algoritması (Task Dispatch) | İş mantığı |
| Ajanlar arası handover ve eskalasyon protokolü | Veritabanı işlemleri |
| Sağlık kontrolü ve context lock mekanizması | Güvenlik politikası |

### §2.1 Registry Authority

Bu dosya agent registry'nin tek SSOT'udur; `.ai/.agents/AGENTS.md` **profil indeksi** olarak hizmet eder — v1.0.0 iken kendi SSOT iddiasını taşırken Faz 4'te (2026-09-23) demote edilmiştir (kayıt: v1.1.0; güncel: **v1.2.3**), `authority: Alt Registry — SSOT: .ai/AGENTS.md (v22.0.0)` (detay §26.2). Çelişkide kök dosya kazanır.

---

## Architecture

### §4 Agent Overview

| # | Agent | Kod Adı | Domain | Katman | Teknoloji |
|---|-------|---------|--------|--------|-----------|
| 1 | **Master Orchestrator** | `mo` | Görev dağıtımı, koordinasyon | Koordinasyon | Vault System, log.md |
| 2 | **Backend Architect** | `backend` | PHP 8.4 API, routing, middleware | A2 | PHP strict_types, PDO, PageRouter |
| 3 | **UI Designer** | `ui` | Vanilla JS, ITCSS, CSS, responsive | A3 | Vanilla JS ES6+, ITCSS 9-layer |
| 4 | **Security Engineer** | `security` | OWASP, encryption, CSRF, CSP | A1 | Argon2id, AES-256-GCM, APCu |
| 5 | **Data Engineer** | `data` | MySQL 18 BCNF, PDO, migration | A0 | MySQL 9, PDO, BCNF |
| 6 | **Embedded Engineer** | `embedded` | C++20, JUCE, ASIO, DSP | A0 | C++20, JUCE 9, ASIO SDK 2.3.4 |
| 7 | **QA Engineer** | `qa` | Test, coverage, E2E | Cross-cutting | PHPUnit 11, Vitest, Playwright |
| 8 | **DevOps Engineer** | `devops` | CI/CD, GitHub Actions, deploy | CI/CD | GitHub Actions, GitLeaks |
| 9 | **Audio Hardware Engineer** | `audio-hw` | DAC/ADC, PCB, amplifier | HW | PCM3168A, AK4458, Class AB |
| 10 | **DSP Firmware Engineer** | `dsp-fw` | XMOS, PCM3168A, DSP chain | FW | XMOS XU316, I2S, TDM |
| 11 | **Windows Software Engineer** | `win-sw` | WASAPI, driver, platform | PLAT | WASAPI, COM, WinRT, WDK |

---

### §5 Domain Boundaries

| Dosya Tipi | Sorumlu Agent | Diğerleri Erişebilir mi? |
|------------|---------------|--------------------------|
| `*.php` (Controller, Service, Repository) | Backend Architect | ✅ |
| `*.js` (Frontend) | UI Designer | ✅ |
| `*.css` (ITCSS layers) | UI Designer | ✅ |
| `*.sql` (Schema, Migration) | Data Engineer | ✅ |
| `*.cpp` / `*.h` (Audio Engine) | Embedded Engineer | ✅ |
| `*.yml` / `*.yaml` (CI/CD) | DevOps Engineer | ✅ |
| `tests/**/*.php` | QA Engineer | ✅ |
| `tests/**/*.test.js` | QA Engineer | ✅ |
| Security middleware | Security Engineer | ✅ |
| `.env` dosyası | Security Engineer | ✅ |
| `log.md` (audit trail) | Tüm ajanlar (append-only) | ✅ Sadece ekleme |
| `.ai/` vault | MO (koordinasyon) | ✅ Okuma serbest |

**A0-A5 Alan Etiketleri (yalnız etiket; kanonik K matrisi [[.ai/architecture/katman-baglilik-matrisi.md]]):**

| Alan | K katmanları | Kapsam |
|------|-------------|--------|
| A0 | K0-K5 | Altyapı/Donanım (çekirdek, firmware K1.f, surucu, DSP, seyyar, PCB) |
| A1 | K6-K7 | Güvenlik |
| A2 | K8-K9 | Routing/Backend |
| A3 | K10-K11 | Presentation/Gösterim |
| A4 | K12-K15 | Veri/Entegrasyon |
| A5 | K16-K20 | Bileşenler (donanım bileşenleri) |

**Layer Violation:** Denetim K matrisine göre yapılır (A2 → A0/A4 ihlali = K8 → K0-K5 veya K12-K15 ihlali gibi); bu dosya yalnız bağlantıdır, ikinci kaynak değildir. İhlal tespit edilirse derhal revert + log ERROR.

---

### §15 Agent Details

| # | Agent | Katman | Teknoloji | Profil |
|---|-------|--------|-----------|--------|
| 1 | Master Orchestrator | Koordinasyon | Vault System, log.md | [[.agents/master-orchestrator]] |
| 2 | Backend Architect | A2 (K8-K9) | PHP 8.4, PDO, PageRouter | [[.agents/backend-architect]] |
| 3 | UI Designer | A3 (K10-K11) | Vanilla JS ES6+, ITCSS 9-layer | [[.agents/ui-designer]] |
| 4 | Security Engineer | A1 (K6-K7) | OWASP, Argon2id, AES-256-GCM | [[.agents/security-engineer]] |
| 5 | Data Engineer | A0 (K0-K5) | MySQL 9, PDO, BCNF | [[.agents/data-engineer]] |
| 6 | Embedded Engineer | A0 (K1-K3) | C++20, JUCE 9, ASIO SDK 2.3.4 | [[.agents/embedded-engineer]] |
| 7 | QA Engineer | Cross-cutting | PHPUnit 11, Vitest, Playwright | [[.agents/qa-engineer]] |
| 8 | DevOps Engineer | CI/CD | GitHub Actions, GitLeaks | [[.agents/devops-engineer]] |
| 9 | Audio HW Engineer | HW | PCM3168A, AK4458, Class AB | [[.agents/audio-hardware-engineer]] |
| 10 | DSP Firmware Engineer | FW | XMOS XU316, I2S, TDM | [[.agents/dsp-firmware-engineer]] |
| 11 | Windows SW Engineer | PLAT | WASAPI, COM, WinRT, WDK | [[.agents/windows-software-engineer]] |

**Stack notu (Faz 1):** Agent tablosundaki "Teknoloji" sütunları hedef yığınları yansıtır; fiziksel kanıt sütunu için §25.2 tablosuna bak. IMPLEMENTED/PLANNED etiket disiplini [[engine.md]] §9.2 matrisiyle birebir uyumludur.

**Detaylı profiller için:** [[.agents/AGENTS.md]]

---

## Rules

### §13 — Reading Order & Mockup Gate

**Kurallar:**
1. P0 → P1 → P2 → P3 sırasıyla okunur
2. Fallback: `index.md`
3. Token aşımı önlenir: gereksiz dosya okunmaz
4. **İstisna:** Görsel referanslar (`.ai/ui-design/screens/**`, `.ai/.png/**`)
5. **Şablon Zorunlu Okuma (Guardrail #16):** `.ai/ui-design/**` içine dosya yazmadan ÖNCE `.ai/.templates/ui-design/` altındaki kalıp şablonu okunur — referans/tokens/index → `[[.templates/ui-design/reference-template]]` (Kalıp A) · flow → `[[.templates/ui-design/flow-template]]` (Kalıp B) · prompt → `[[.templates/ui-design/prompt-template]]` (Kalıp C) · screens → `[[.templates/ui-design/screen-spec-template]]` (Kalıp D). Şablonsuz ui-design dosyası üretilmez (§7.3).

**Mockup Before Frontend:** CSS/HTML/JS/layout/bileşen görevlerinde `.ai/ui-design/` altındaki ilgili görsel okunmadan kod yazılamaz. Görsel okunamıyorsa DUR ve bildir.

---

### §14 Mandatory 5 Skills (ADR-042/C4)

> **Faz 1 doğrulama notu (2026-09-08):** Bu tablo **disiplin maskesidir** — diskte mevcut skill klasörleri `.opencode/skills/` altındaki 8 aktif SKILL.md'dir: composer-sync, db-engine, orchestration, truth-engine, ui-workbench, vault-sync-post, agent-debate, context-report (_archive/ kaldırıldı; [[index.md]] §11B). `/brainstorming` ve `/vault-sync` için ayrı skill klasörü YOKTUR; bu işlevler sırasıyla sistem promptundaki brainstorming becerisi ve `.workflows/vault-sync.md` akışıyla yürütülür.

| # | Skill | Amaç | Kullanım |
|---|-------|------|----------|
| 1 | `/prompt-maker` | Prompt üretim motoru | Her görev başlangıcında |
| 2 | `/brainstorming` | Fikir üretimi ve keşif *(klasör yok — sistem becerisi)* | Yaratıcı work öncesi |
| 3 | `/vault-sync` | Vault senkronizasyonu *(klasör yok — .workflows akışı)* | Seans sonunda |
| 4 | `/hallucination-control` | Halüsinasyon doğrulama | Kod yazma öncesi |
| 5 | `Red Team · Truth Mode · Human Mode` | Her zaman aktif | Sürekli |
| 6 | `Template Mandatory` | Yeni dosya için template zorunlu (Guardrail #16) | Her dosya oluşturmada |

#### §14.1 Prompt-Agent Mapping Table

| Prompt | Birincil Agent | İkincil Agent | Kullanım Anı |
|--------|----------------|---------------|-------------|
| prompt0 (Genel) | MO (dağıtıyor) | Tüm agentlar | Her görev başında zorunlu |
| prompt1 (SPA Router) | Backend Architect | UI Designer | SPA route tasarımında |
| prompt2 (Auth) | Security Engineer | Backend Architect | Auth middleware'de |
| prompt3 (API) | Backend Architect | DevOps Engineer | API gateway'de |

**Kural:** prompt0 her zaman okunur. prompt1-3 sadece ilgili domain görevlerinde okunur. *(Faz 1 düzeltmesi: prompt arşiv dosyaları `archives/prompt*-2026-09-01` konumundadır — eski `2026-08-13` hedefleri güncellenmiştir.)*

---

### §17 Edge Cases

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

> ⚠️ Not: Orijinal tabloda 2 numaralı satır yok (Faz 1'den beri bilinen boşluk — korunmuştur).

---

### §18 Warnings

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

## Workflow

### §6 Keyword → Agent Routing

| Keyword Grubu                                                                                    | Birincil Agent              | İkincil Agent     |
| ------------------------------------------------------------------------------------------------ | --------------------------- | ----------------- |
| API, endpoint, routing, middleware, PHP, controller, repository                                  | Backend Architect           | Security Engineer |
| CSS, UI, responsive, accessibility, ITCSS, BEM, frontend, design, JS, mockup, ui-design, c01-c16, 45-tier, device-matrix, screen-spec, token, glassmorphism | UI Designer | QA Engineer       |
| CSRF, CSP, XSS, OWASP, auth, encryption, security, session, rate limit                           | Security Engineer           | Backend Architect |
| database, SQL, BCNF, migration, query, schema, MySQL, PDO, index                                 | Data Engineer               | Backend Architect |
| C++, ASIO, JUCE, audio, DSP, Neva Engine, ring buffer, WASAPI, hardware                          | Embedded Engineer           | DevOps Engineer   |
| test, coverage, PHPUnit, Vitest, Playwright, E2E, unit test, integration                         | QA Engineer                 | —                 |
| CI/CD, GitHub Actions, deploy, infrastructure, pipeline, monitoring, GitLeaks                    | DevOps Engineer             | QA Engineer       |
| vault, documentation, ADR, wiki-link, index, keys, brain                                         | MO (vault-updater)          | —                 |
| template, şablon, şablon, template usage, .templates                                             | Tüm ajanlar (guardrail #16) | MO (koordinasyon) |

---

### §7 Task Dispatch Algorithm

```
Kullanıcı İsteği
  → [1. Analiz] — Keyword çıkarma, domain eşleme
    → [2. Pre-flight Checks] — Bağımlılık, dosya kontrolü, UI Design mockup kontrolü
      → [3. Task Assignment] — Doğru ajanı seç ve görev ata
        → [4. Execution] — Ajan görevi yürütür
          → [5. Handover] — Gerekirse diğer ajana transfer
            → [6. Validation] — Çıktıyı doğrula
              → [7. Completion] — Görevi tamamla ve logla
```

#### §7.1 Step 1: Analysis

| Kontrol | Yöntem | Kaynak |
|---------|--------|--------|
| Keyword çıkarma | Routing tablosuna başvur | Bu dosya §6 |
| Domain eşleme | Dosya uzantısı ve içerik analizi | Bu dosya §5 |
| Öncelik belirleme | CRITICAL > HIGH > MEDIUM > LOW | Bu dosya §8 |
| Ajan seçimi | Birincil + ikincil ajan | Bu dosya §6 |

#### §7.2 Step 2: Pre-flight Checks

| Kontrol | Değer | İhlal |
|---------|-------|-------|
| Domain boundary | Doğru ajan | Layer violation → revert |
| Dosya etkileniyor mu? | Eşzamanlı erişim | Context lock |
| Bağımlılık var mı? | Handover gerekli | Transfer başlat |
| UI Design uyumu | [[ui-design/01-mockup-index]] (19 PNG + 45-tier cihaz matrisi) ve C01-C16 kontrolü; referans sırası: PNG > ASCII art > Inventory > Tokens > Reference (01-10) | Mockup okunmadıysa → DUR |
| 45-Tier uyumu | [[ui-design/reference/10-device-specific-guidelines]] tier kontrolü; responsive token'lar | Tier kuralı ihlal edilmişse → RED |
| Responsive uyum | [[ui-design/05-responsive-architecture]] §7.4 (4K'da ortalamama) + §12 (fallback zorunlu) | Tier kuralı ihlal edilmişse → RED |
| Şablon zorunluluğu | `.ai/.templates/ui-design/{reference,flow,prompt,screen-spec}-template.md` (Kalıp A-D) — görev tipine göre ilgili şablon okundu mu? | Şablon okunmadıysa → DUR (Guardrail #16) |
| Önceki görev başarısız mı? | Retry / escalation | Max 3 retry |

#### §7.3 Step 3: Task Assignment

Öncelik seviyeleri, timeout ve retry değerleri: §8 Priority Levels.

#### §7.4 Step 4: Execution

Ajan görevi yürütür. Kurallar:
- Domain boundary'yi koru
- Zero Code Before Plan uygula
- Çıktıyı standardize et

#### §7.5 Step 5: Handover

Gerekirse diğer ajana transfer. Handover protokolü §9'da tanımlıdır.

#### §7.6 Step 6: Verification

| Kontrol | Değer |
|---------|-------|
| Çıktı formatı | Uygun |
| Domain uyumluluğu | Doğru ajan |
| Cross-reference | Geçerli wiki-link'ler |
| Security | Hassas veri redaction |

#### §7.7 Step 7: Completion

- Görev tamamlanır
- `log.md`'ye giriş eklenir
- MEMORY.md session state güncellenir
- Gerekirse vault-sync yapılır

---

### §8 Priority Levels

| Öncelik | Tanım | Timeout | Max Retry | Yanıt Süresi |
|---------|-------|---------|-----------|-------------|
| CRITICAL | Sistem durması, güvenlik açığı | 5s | 1 | Anlık |
| HIGH | Kritik işlev kaybı | 15s | 3 | 15s |
| MEDIUM | Normal geliştirme görevi | 30s | 3 | 30s |
| LOW | İyileştirme, optimizasyon | 60s | 2 | 60s |

---

### §9 Handover Protocol

```
[Kaynak Agent] → [Handover Request] → [Hedef Agent] → [Onay/Red] → [Confirmation]
```

#### §9.1 Handover Message Format

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

#### §9.2 Handover Rules

| Kural | Değer |
|-------|-------|
| Onay zorunlu | Hedef agent onayı olmadan tamamlanamaz |
| Timeout | 30 saniye |
| Max retry | 3 |
| Red durumunda | MO devreye girer |
| Logging | Tüm handover'lar `log.md`'ye yazılır |

#### §9.3 Handover Scenarios

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

### §10 Escalation Protocol

```
Level 1 (Domain Lead) → Level 2 (Tech Lead) → Level 3 (Arch Lead) → İnsan
```

#### §10.1 Escalation Scenarios

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

#### §10.2 Escalation Rules

| Kural | Değer |
|-------|-------|
| L1 timeout | 30 saniye |
| L2 timeout | 60 saniye |
| L3 timeout | 120 saniye |
| Max retry | 3 her seviyede |
| İnsan müdahalesi | Son çare |

---

### §11 Health Check

#### §11.1 Health Parameters

| Parametre | Değer |
|-----------|-------|
| Timeout | 30 saniye |
| Max Retry | 3 |
| Check Interval | Her görev başında |
| Heartbeat | 10 saniye |

#### §11.2 Health States

| Durum | Kod | Açıklama |
|-------|-----|----------|
| Healthy | 200 | Görev tamamlandı |
| Degraded | 301 | Yavaş yanıt (>15s) |
| Retry | 408 | Timeout, yeniden deneniyor |
| Failed | 500 | 3 retry başarısız, queue reset |
| Dead | 503 | Yanıt yok, escalation |

#### §11.3 Health Check Flow

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

### §12 Context Lock

Eşzamanlı erişimi önlemek için dosya kilitleme mekanizması.

#### §12.1 Lock Rules

| Kurallar | Değer |
|---------|-------|
| Kilitleme süresi | Max 30 saniye |
| Deadlock prevention | MO en eski kilidi kırar |
| Öncelik | CRITICAL > HIGH > MEDIUM > LOW |
| Logging | Lock acquire/release `log.md`'ye yazılır |

#### §12.2 Lock Flow

```
Ajan dosyaya erişmek ister
  → Lock acquire (max 30s bekleme)
    → Başarılı → dosyayı düzenle
    → Başarısız → kuyruk → öncelik sırası
      → Timeout → escalation
```

#### §12.3 Deadlock Prevention

| Yöntem | Açıklama |
|--------|----------|
| Timeout | Max 30s sonra lock serbest |
| Priority override | CRITICAL diğer kilidi kırar |
| MO intervention | MO en eski kilidi kırar |
| Queue reset | Tüm kilitler sıfırlanır |

---

### §19 Forward-Looking Roadmap

| Versiyon | Özellik |
|----------|---------|
| v19.0 | Semantic Agent Routing (mevcut) |
| v20.0 | Self-Healing Agents |
| v21.0 | Multi-Agent Learning |
| v22.0 | Tam Otonom Çalışma (Zero Human Intervention) |
| v23.0 | Cross-Project Memory (WirelessConnect) |

---

### §24 Ultra Thinking Protocol (OpenCode Integration)

**⚠️ ZORUNLULUK:** Tüm agent'lar kod yazmadan önce bu protokolü uygulamak ZORUNDADIR.

#### §24.1 5-Step Thinking Protocol

| Adım | Kontrol | Kaynak | Timeout |
|------|---------|--------|---------|
| 1. Vault Oku | CLAUDE.md → AGENTS.md → WORKFLOW.md → brain.md → ROLE.md → ilgili ADR'ler | `.ai/` vault | Max 25s |
| 2. Bağlamı Anla | Domain, katman, dosyalar, bağımlılıklar | Mevcut kod | Değişken |
| 3. Hata Kontrolü | Syntax, imports, types, style, security | LSP + Manuel | Anlık |
| 4. Sonuç Tahmini | Etki alanı, edge cases, performance | Düşünce | Değişken |
| 5. Doğrulama | LSP, typecheck, test, template uyumu | Build araçları | Anlık |

#### §24.2 .ai Reference Tracking

**Her görev başında bu dosyaları OKU:**

| Sıra | Dosya | Amaç |
|------|-------|------|
| 1 | `.ai/CLAUDE.md` | AI anayasası, 16 Hard Guardrails |
| 2 | `.ai/AGENTS.md` | Agent sınırları, routing (bu dosya) |
| 3 | `.ai/WORKFLOW.md` | Süreçler, fazlar |
| 4 | `.ai/brain.md` | Mimari kararlar |
| 5 | `.ai/ROLE.md` | Rol tanımı |
| 6 | `.ai/index.md` | Master katalog |
| 7 | `.ai/keys.md` | Keyword haritası |
| 8 | `.ai/MEMORY.md` | Session hafızası |
| 9 | `.ai/log.md` | Audit trail |
| 10 | `.ai/ULTRA-THINKING.md` | Ultra düşünme protokolü |
| 11 | `.ai/engine.md` | Orkestrasyon motoru — agent koordinasyonu |
| 12 | `.ai/glossary.md` | Terim sözlüğü — kod-referanslı |
| 13 | `.ai/VISION.md` | Vizyon ve yol haritası |
| 14 | `.ai/PROJECTS.md` | Proje envanteri |

#### §24.3 Domain-Based Reading

> *Faz 1 düzeltmesi: `testing/*.md` ve `.sql/*.sql` eski yolları gerçek mevcut yollarla değiştirildi; kanıt: [[ULTRA-THINKING.md]] §3.2.*

| Agent | Zorunlu Okuma |
|-------|---------------|
| Backend | `architecture/k9-api-routing/*.md`, `.ai/.decisions/index.md` (ADR-083), `shared/src/PageRouter/` |
| Frontend | `ui-design/01-mockup-index.md`, `ui-design/02-component-inventory.md`, `architecture/k11-ux/*.md`, `.ai/.templates/ui-design/*-template.md` (Kalıp A-D — Guardrail #16 zorunlu okuma) |
| Security | `architecture/k6-guvenlik/*.md`, `.ai/.decisions/index.md` (ADR-010), `shared/src/Middleware/` |
| Data | `architecture/k0-isletim-sistemi/*.md`, `.ai/.sql/mysql/*.sql`, `shared/src/Database/` |
| Embedded | `.ai/projects/NevaEngine/*.md` ⚠️ VERIFICATION REQUIRED (dizin var, 0 dosya); `electronic/dsp/*.md`, `electronic/firmware/*.md` ⚠️ YOK → gerçek: `architecture/firmware/*.md`, `architecture/k3-ses-motoru/*.md`, `architecture/k1-donanim/*.md` |
| QA | `ui-design/04-accessibility-gaps.md`, `ui-design/screens/**/*.md`, `.ai/reports/` |
| DevOps | `architecture/k13-cicd/*.md`, `.ai/ecosystem/*.md` |

#### §24.4 Automatic Cleanup

| Durum | Aksiyon |
|-------|---------|
| LSP hata tespit | "FIX IMMEDIATELY" mesajı, devam yasak |
| Hallüsinasyon | "VERIFICATION REQUIRED" etiketi |
| Çelişki | DUR + kullanıcıya sor |
| Eksik dosya | Hemen tamamla veya sil |

#### §24.5 Quality Checklist

Her dosya için kontrol et:
- [ ] Syntax doğru mu?
- [ ] Import'lar mevcut mu?
- [ ] Types uyumlu mu?
- [ ] Style tutarlı mı?
- [ ] Security riski yok mu?
- [ ] Template'e uygun mu?
- [ ] Cross-reference'lar geçerli mi?
- [ ] Frontmatter tam mı? (7 zorunlu alan)

---

## Validation

### §16 Quality Standards (Per Agent)

| Agent | Standart | Hedef |
|-------|----------|-------|
| Backend | strict_types, PSR-12, prepared statement | %100 |
| UI | ITCSS uyum, BEM namespace, WCAG 2.2 AA, ui-design C01-C16 uyumu | %100 |
| Security | OWASP Top 10, CSRF=`csrf_token`, Argon2id | %100 |
| Data | BCNF, no ORM, no SELECT *, prepared | %100 |
| Embedded | Zero-allocation, lock-free, noexcept | %100 |
| QA | Test coverage ≥80%, flaky test %0 | ≥80% |
| DevOps | CI/CD success ≥95%, GitLeaks clean | ≥95% |

---

### §23 Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 22.0.3 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 8 |
| Agent Count | 11 (1 MO + 10 specialist) |
| Domain Boundaries | 12 dosya tipi |
| Routing Rules | 9 keyword grubu |
| Edge Cases | 9 |
| Mandatory Skills | 5 başlık + 2 kural satırı (§14 tablosu 6 satır) |
| Handover Scenarios | 8 |
| Escalasyon Senaryoları | 9 |
| Health States | 5 |
| Lock Rules | 4 |
| Quality Standards | 7 |
| Faz Kaydı | §25 — 7 faz tablosu + stack kanıt + boot uzlaşması |
| Stack Kanıtı | §25.2 — agent → teknoloji satırları (ROLE §11 ile uyumlu) |
| Orkestrasyon Kuralları | §25.3 — 5 kural (satır edit, frozen dokunulmaz, append-only, stack direktifi, faz kapanışı) |

---

### §25 Orchestration Phase Record (Vault Revision 2026-09-08)

#### §25.1 Phase Table (live)

| Faz | Kapsam | Durum |
|-----|--------|-------|
| 0 | Envanter + kaynak kod cross-check | TAMAMLANDI |
| 1 | Kök 14 boot dosyası (satır-satır edit, 500+ hedef) | ÇALIŞIYOR |
| 2 | architecture/ alt fazlar | Pending |
| 3 | ecosystem, servers, subdomains, scripts | Pending |
| 4 | ui-design çekirdek + tokens + flow (`.templates/ui-design/` Kalıp A-D üretildi 2026-09-24 — 4 şablon; ui-design dosyaları beklemede) | Pending |
| 5 | decisions appendix (frozen ADR'ler) | Pending |
| 6 | electronic, projects | Pending |

#### §25.2 Agent Stack Evidence Table (ROLE §11 summary)

| Agent | Teknoloji | Kanıt (IMPLEMENTED) |
|-------|-----------|---------------------|
| Backend | PHP 8.4, PSR, php-di, fast-route | 3 composer.json |
| Security | Middleware ×11 (PSR-15) | shared/src/Middleware/ |
| Data | SQL şema (18 DB) | .ai/.sql/mysql/ |
| QA | PHPUnit ^10.5/^11.0, PHPStan | require-dev · `shared/tests/` 22 dosya |
| UI/Embedded/DSP/Windows/DevOps | Vanilla JS/C++20/xcc/C#/CI | PLANNED (spec mevcut; `.github/workflows/` = 0 dosya) |

#### §25.3 Orchestration Rules of This Revision

1. **Satır-satır edit:** Yapı korunur (ADR-042); silme yerine düzeltme+ekleme.
2. **Frozen dokunulmaz:** ADR metinleri okunur, referans edilir — değiştirilmez.
3. **log.md append-only:** Revizyon kayıtları eklenir, geçmiş satıra dokunulmaz.
4. **Teknoloji direktifi:** Dinamik stack (Node.js/C++/C#/PHP + diğer) — [[engine.md]] §9, [[ROLE.md]] §11.
5. **Faz kapanışı:** engine §12.6 kontrol listesi 8/8 → §12.7 rapor → vault-sync.

#### §25.4 Boot List Compatibility

Bu dosya §24.2 (14 dosya) ile [[MEMORY.md]] §5 (20 adım) arasındaki adım sayısı farkı bilinen durumdur: §5 genişletilmiş okuma setidir (`.claude/rules/*`, prompt arşivleri ve mockup indeksi dahil). Kanonik kök boot listesi bu dosyanın §24.2'si ve [[WORKFLOW.md]] §8.7A'dır (14 .ai kök dosya); FULL boot 17 öğedir (root CLAUDE.md + root WORKFLOW.md + root README.md + 14 .ai kök dosya + 3 dizin: .workflows, .ai/.templates, .ai/.agents).

---

### §26 Document Skeleton & SSOT Reconciliation (Vault Refactor Engine — 2026-09-23)

> **Not:** Bu dosya Vault Refactor Engine ile v21.0.0 → v22.0.0'a yükseltildi; 8 bölümlük evrensel iskelete göre yeniden düzenlendi (içerik taşındı, silinmedi).

#### §26.1 8-Section Skeleton Mapping

| İskelet Bölümü | Karşılık Gelen § |
|----------------|------------------|
| Başlık | H1 + frontmatter (7 zorunlu alan) |
| Amaç | §1 Purpose |
| Kapsam | §2 Scope |
| Mimari | §4 Agent Overview + §5 Domain Boundaries + §15 Agent Details + [[.agents/AGENTS.md]] |
| Kurallar | §13 Reading Order + §14 Mandatory Skills + §17 Edge Cases + §18 Warnings |
| Workflow | §6 Routing + §7 Task Dispatch + §8-§12 + §19 Roadmap + §24 Ultra Thinking |
| Doğrulama | §16 Quality Standards + §23 Quality Report + §25 Phase Record + §26 |
| Referanslar | §3 Terminology + §20 Related Documents + §21 Cross References + §22 Glossary + PDF §1.4-§1.6 |

#### §26.2 SSOT Conflict Resolution (Registry Reconciliation)

| Kaynak | Eski Durum | Yeni Durum |
|--------|-----------|------------|
| `.ai/AGENTS.md` (kök) | v21.0.0, SSOT iddiası | **v22.0.3 — tek SSOT** |
| `.ai/.agents/AGENTS.md` (alt) | v1.0.0, kendini SSOT ilan ediyordu | v1.2.1 — **alt registry** (`authority: "Alt Registry — SSOT: .ai/AGENTS.md (v22.0.0)"`) |

Çözüm kuralı: SSOT hiyerarşisinde çelişkide kök dosya kazanır. Alt registry yalnızca profil/özet detayını taşır; routing, handover, escalation, öncelik kurallarının tamamı bu dosyadadır.

**Faz 4 notu (2026-09-23):** `.ai/.agents/AGENTS.md` v1.0.0 → v1.1.0 olarak **profil indeksine demote edildi** (kendi SSOT iddiası kaldırıldı, `authority: Alt Registry`); aynı fazda 11 agent profili `.ai/.agents/*.md` v1.0.0 → v2.0.0 olarak yeniden yazıldı. Alt registry artık yalnızca bu profillerin indeksidir — routing/handover/escalation/öncelik bu dosyanın tekelindedir.

#### §26.3 Phase 1 Verification

- [x] Frontmatter 7 alan; version 22.0.1; updated 2026-09-24
- [x] Mojibake temizlendi (vault geneli 745 düzeltme)
- [x] §1-§25 korundu, silme yok; yeni bölüm §26 olarak eklendi
- [x] `.agents/AGENTS.md` SSOT iddiası kaldırıldı → alt registry uyarısı + H1 düzeltmesi eklendi
- [x] REFACTOR REPORT: FILE: AGENTS.md · PURPOSE: Agent Registry SSOT · VALIDATION: § korundu, link hedefleri korundu · RELATED: [[CLAUDE.md]] · [[WORKFLOW.md]] · [[.agents/AGENTS.md]] · [[index.md]] · [[log.md]]

---

## References

### §3 Terminology (Stub)

> 📌 **Stub:** Aşağıdaki 12 terim bu dosyaya özgüdür ve satır içinde tutulur; son 3 terim eski §22 Glossary tablosundan birleştirildi (SSOT dedup, 2026-09-23). ⚠️ VERIFICATION REQUIRED: Hiçbiri glossary.md'de yer almıyor — sonraki fazda eklenmeli. Tam terim sözlüğü: [[glossary]].

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
| **Stack Etiketi** | IMPLEMENTED (kod kanıtlı) / PLANNED (hedef) ayırımı — engine §9.2 |
| **Uncertainty Flag** | Alt agent belirsizlik raporu formatı — engine §6.4 |
| **Faz Kapanışı** | 8 maddelik kontrol listesi tamamı — engine §12.6 |

---

### §20 Related Documents

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

### §21 Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 5 Domain | [[CLAUDE.md]] §5 | K0-K20 katmanları |
| § 6 Routing | [[engine.md]] §2 | Orkestrasyon bölümleri |
| § 9 Handover | [[WORKFLOW.md]] §8.6 | Session init |
| § 10 Eskalasyon | [[ADR-008-bypass-auth-middleware]] | Auth bypass |
| § 15 Agent | [[.agents/AGENTS.md]] | Agent profilleri |
| § 17 Edge | [[ADR-017-dsp-hardware-mode]] | ASIO/WASAPI |

---

### §22 Glossary

> 📌 **Stub (SSOT dedup — 2026-09-23):** Bu tablo §3 Terminology ile birleştirildi. 16 terimin 13'ü §3'teki karşılıklarıyla örtüşüyor; benzersiz 3 terim (**Stack Etiketi**, **Uncertainty Flag**, **Faz Kapanışı**) §3 tablosuna taşındı — bilgi silinmedi. Genel/proje terimleri için: [[glossary]].

---

### PDF §1.4 — Target Users

İçerik (6 hedef kitle + sorumlu agent eşlemesi): [[PROJECTS.md]] §4 — Hedef Kullanıcı Profilleri (6 Kitle)

### PDF §1.5 — Sectoral Solutions

İçerik (6 sektörel çözüm + subdomain eşlemesi): [[PROJECTS.md]]

### PDF §1.6 — Problems Solved

İçerik (6 sorun + CoreMusic çözüm eşlemesi): [[VISION.md]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-24
**Mode:** Red Team · Human Mode · Truth Mode
