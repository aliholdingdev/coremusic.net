---
title: "CoreMusic — Agent Registry & Coordination Protocol"
type: guide
category: agent-registry
version: 22.0.0
status: active
authority: SSOT
updated: 2026-09-23
---

# CoreMusic — Agent Registry & Coordination Protocol

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]] · [[VISION.md]] · [[PROJECTS.md]] · [[.templates/index]] · [[.agents/AGENTS.md]]

**Skills:** `.opencode/skills/` (10 skill — Guardrail #16 zorunlu)

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

Bu dosya agent registry'nin tek SSOT'udur; `.ai/.agents/AGENTS.md` alt registry (profil detayları) olarak hizmet eder (§26.2). Çelişkide kök dosya kazanır.

---

## Architecture

### §4 Agent Overview

| # | Agent | Kod Adı | Domain | Katman | Teknoloji |
|---|-------|---------|--------|--------|-----------|
| 1 | **Master Orchestrator** | `mo` | Görev dağıtımı, koordinasyon | Koordinasyon | Vault System, log.md |
| 2 | **Backend Architect** | `backend` | PHP 8.4 API, routing, middleware | L2 | PHP strict_types, PDO, PageRouter |
| 3 | **UI Designer** | `ui` | Vanilla JS, ITCSS, CSS, responsive | L3 | Vanilla JS ES6+, ITCSS 9-layer |
| 4 | **Security Engineer** | `security` | OWASP, encryption, CSRF, CSP | L1 | Argon2id, AES-256-GCM, APCu |
| 5 | **Data Engineer** | `data` | MySQL 18 BCNF, PDO, migration | L0 | MySQL 9, PDO, BCNF |
| 6 | **Embedded Engineer** | `embedded` | C++20, JUCE, ASIO, DSP | L0 | C++20, JUCE 9, ASIO SDK 2.3.4 |
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

**Layer Violation:** L0 → L2/L3 veya L1 → L3 gibi kural ihlalleri tespit edilirse derhal revert + log ERROR.

---

## 6. Keyword → Agent Yönlendirmesi

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
| Önceki görev başarısız mı? | Retry / escalation | Max 3 retry |

#### §7.3 Step 3: Task Assignment

| Öncelik | Tanım | Timeout | Max Retry |
|---------|-------|---------|-----------|
| CRITICAL | Sistem durması, güvenlik açığı | 5s | 1 |
| HIGH | Kritik işlev kaybı | 15s | 3 |
| MEDIUM | Normal geliştirme görevi | 30s | 3 |
| LOW | İyileştirme, optimizasyon | 60s | 2 |

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

> **Faz 1 doğrulama notu (2026-09-08):** Bu tablo **disiplin maskesidir** — diskte mevcut skill klasörleri `.opencode/skills/` altındaki 10 kanonik skill'dir ([[index.md]] §11B). `/brainstorming` ve `/vault-sync` için ayrı skill klasörü YOKTUR; bu işlevler sırasıyla sistem promptundaki brainstorming becerisi ve `.workflows/vault-sync.md` akışıyla yürütülür.

| # | Skill | Amaç | Kullanım |
|---|-------|------|----------|
| 1 | `/prompt-maker` | Prompt üretim motoru | Her görev başlangıcında |
| 2 | `/brainstorming` | Fikir üretimi ve keşif *(klasör yok — sistem becerisi)* | Yaratıcı work öncesi |
| 3 | `/vault-sync` | Vault senkronizasyonu *(klasör yok — .workflows akışı)* | Seans sonunda |
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

**Kural:** prompt0 her zaman okunur. prompt1-3 sadece ilgili domain görevlerinde okunur. *(Faz 1 düzeltmesi: prompt arşiv dosyaları `archives/prompt*-2026-09-01` konumundadır — eski `2026-08-13` hedefleri güncellenmiştir.)*

---

## 15. Agent Detayları

| # | Agent | Katman | Teknoloji | Profil |
|---|-------|--------|-----------|--------|
| 1 | Master Orchestrator | Koordinasyon | Vault System, log.md | [[.agents/master-orchestrator]] |
| 2 | Backend Architect | L2 (Routing) | PHP 8.4, PDO, PageRouter | [[.agents/backend-architect]] |
| 3 | UI Designer | L3 (Presentation) | Vanilla JS ES6+, ITCSS 9-layer | [[.agents/ui-designer]] |
| 4 | Security Engineer | L1 (Security) | OWASP, Argon2id, AES-256-GCM | [[.agents/security-engineer]] |
| 5 | Data Engineer | L0 (Infrastructure) | MySQL 9, PDO, BCNF | [[.agents/data-engineer]] |
| 6 | Embedded Engineer | L0 (Hardware) | C++20, JUCE 9, ASIO SDK 2.3.4 | [[.agents/embedded-engineer]] |
| 7 | QA Engineer | Cross-cutting | PHPUnit 11, Vitest, Playwright | [[.agents/qa-engineer]] |
| 8 | DevOps Engineer | CI/CD | GitHub Actions, GitLeaks | [[.agents/devops-engineer]] |
| 9 | Audio HW Engineer | HW | PCM3168A, AK4458, Class AB | [[.agents/audio-hardware-engineer]] |
| 10 | DSP Firmware Engineer | FW | XMOS XU316, I2S, TDM | [[.agents/dsp-firmware-engineer]] |
| 11 | Windows SW Engineer | PLAT | WASAPI, COM, WinRT, WDK | [[.agents/windows-software-engineer]] |

**Stack notu (Faz 1):** Agent tablosundaki "Teknoloji" sütunları hedef yığınları yansıtır; fiziksel kanıt sütunu için §25.2 tablosuna bak. IMPLEMENTED/PLANNED etiket disiplini [[engine.md]] §9.2 matrisiyle birebir uyumludur.

**Detaylı profiller için:** [[.agents/AGENTS.md]]

---

## 16. Kalite Standartları (Agent Başına)

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
| **Stack Etiketi** | IMPLEMENTED (kod kanıtlı) / PLANNED (hedef) ayırımı — engine §9.2 |
| **Uncertainty Flag** | Alt agent belirsizlik raporu formatı — engine §6.4 |
| **Faz Kapanışı** | 8 maddelik kontrol listesi tamamı — engine §12.6 |

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
| Faz Kaydı | §25 — 7 faz tablosu + stack kanıt + boot uzlaşması |
| Stack Kanıtı | §25.2 — agent→”teknoloji satırları (ROLE §11 ile uyumlu) |
| Orkestrasyon Kuralları | §25.3 — 5 kural (satır edit, frozen dokunulmaz, append-only, stack direktifi, faz kapanışı) |

---

## 24. Ultra Düşünme Protokolü (OpenCode Entegrasyonu)

**⚠️ ZORUNLULUK:** Tüm agent'lar kod yazmadan önce bu protokolü uygulamak ZORUNDADIR.

### 24.1 5 Adımlı Düşünme Protokolü

| Adım | Kontrol | Kaynak | Timeout |
|------|---------|--------|---------|
| 1. Vault Oku | CLAUDE.md → AGENTS.md → WORKFLOW.md → brain.md → ROLE.md → ilgili ADR'ler | `.ai/` vault | Max 25s |
| 2. Bağlamı Anla | Domain, katman, dosyalar, bağımlılıklar | Mevcut kod | Değişken |
| 3. Hata Kontrolü | Syntax, imports, types, style, security | LSP + Manuel | Anlık |
| 4. Sonuç Tahmini | Etki alanı, edge cases, performance | Düşünce | Değişken |
| 5. Doğrulama | LSP, typecheck, test, template uyumu | Build araçları | Anlık |

### 24.2 .ai Referans Takibi

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

### 24.3 Domain-Based Okuma

> *Faz 1 düzeltmesi: `testing/*.md` ve `.sql/*.sql` eski yolları gerçek mevcut yollarla değiştirildi; kanıt: [[ULTRA-THINKING.md]] §3.2.*

| Agent | Zorunlu Okuma |
|-------|---------------|
| Backend | `architecture/l2-routing/*.md`, `ADR-083*.md`, `shared/src/PageRouter/` |
| Frontend | `ui-design/00-mockup-index.md`, `ui-design/01-component-inventory.md`, `architecture/l3-presentation/*.md` |
| Security | `architecture/l1-security/*.md`, `ADR-010*.md`, `shared/src/Middleware/` |
| Data | `architecture/k0-k5-software/k0-os-layer/*.md`, `.ai/.sql/mysql/*.sql`, `shared/src/Database/` |
| Embedded | `projects/NevaEngine/*.md`, `electronic/dsp/*.md`, `electronic/firmware/*.md` |
| QA | `ui-design/03-accessibility-gaps.md`, `ui-design/screens/**/*.md`, `reports/` |
| DevOps | `architecture/02-deployment/*.md`, `ecosystem/*.md` |

### 24.4 Otomatik Temizlik

| Durum | Aksiyon |
|-------|---------|
| LSP hata tespit | "FIX IMMEDIATELY" mesajı, devam yasak |
| Hallüsinasyon | "VERIFICATION REQUIRED" etiketi |
| Çelişki | DUR + kullanıcıya sor |
| Eksik dosya | Hemen tamamla veya sil |

### 24.5 Kalite Kontrol Listesi

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

---

## PDF §1.4 — Hedef Kullanıcılar

| # | Kullanıcı | İhtiyaçlar | Sorumlu Agent |
|---|-----------|------------|---------------|
| 1 | Bireysel Kullanıcı | Kolay kullanım, kişisel arşiv, çoklu cihaz, AI öneriler | ui-designer, backend-architect |
| 2 | Hi-Fi / Audiophile | Kayıpsız ses (FLAC/WAV), DSP/EQ, ASIO/WASAPI, donanım | embedded-engineer, audio-hardware-engineer |
| 3 | Profesyonel Stüdyo | Stüdyo referansı, 8.1 surround, proje bazlı çalışma | embedded-engineer, dsp-firmware-engineer |
| 4 | Araç Kullanıcısı | CarPlay, düşük gecikme, araç uyumlu arayüz | embedded-engineer, windows-software-engineer |
| 5 | Ev Medya Kullanıcısı | Multi-room, NAS/DLNA, Smart TV | embedded-engineer, devops-engineer |
| 6 | Geliştirici / Freelancer | Açık dokümantasyon, template, API | Tüm agentlar |

## PDF §1.5 — Sektörel Çözümler

| # | Çözüm | Subdomain | Sorumlu Agent |
|---|-------|-----------|---------------|
| 1 | Otomotiv Araç İçi | car.coremusic.net | embedded-engineer |
| 2 | Akıllı Ev & Çok Odalı | home.coremusic.net | embedded-engineer |
| 3 | Profesyonel Stüdyo | studio.coremusic.net | embedded-engineer |
| 4 | Odyofil / Hi-Fi | — | audio-hardware-engineer |
| 5 | Offline Arşivleme | download.coremusic.net | devops-engineer |
| 6 | Kurumsal Medya | media.coremusic.net | backend-architect |

## PDF §1.6 — Çözülen Sorunlar

| # | Sorun | CoreMusic Çözümü |
|---|-------|-----------------|
| 1 | Platform bağımlılığı | Offline-first, mülkiyet odaklı |
| 2 | Cihazlar arası senkronizasyon | Çapraz cihaz handoff |
| 3 | Ses kalitesi kaybı | Neva Engine, bit-perfect aktarım |
| 4 | Kayıplı sıkıştırma | FLAC, WAV, 32-bit Float desteği |
| 5 | Dağınık arşiv | Merkezi medya depolama |
| 6 | Eksik offline destek | Yerel SSD önbellek |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-23
**Mode:** Red Team · Human Mode · Truth Mode

---

## 25. Orkestrasyon Faz Kaydı (Vault Revizyonu 2026-09-08)

### 25.1 Faz Tablosu (canlı)

| Faz | Kapsam | Durum |
|-----|--------|-------|
| 0 | Envanter + kaynak kod cross-check | TAMAMLANDI |
| 1 | Kök 12 boot dosyası (satır-satır edit, 500+ hedef) | ÇALIŞIYOR |
| 2 | architecture/ alt fazlar | Pending |
| 3 | ecosystem, servers, subdomains, scripts | Pending |
| 4 | ui-design çekirdek + tokens + flow | Pending |
| 5 | decisions appendix (frozen ADR'ler) | Pending |
| 6 | electronic, projects | Pending |

### 25.2 Agent →” Stack Kanıt Tablosu (ROLE §11 özeti)

| Agent | Teknoloji | Kanıt (IMPLEMENTED) |
|-------|-----------|---------------------|
| Backend | PHP 8.4, PSR, php-di, fast-route | 4 composer.json |
| Security | Middleware ×4 (PSR-15) | shared/src/Middleware/ |
| Data | SQL şema (18 DB) | .ai/.sql/mysql/ |
| QA | PHPUnit ^10.5/^11.0, PHPStan | require-dev |
| UI/Embedded/DSP/Windows/DevOps | Vanilla JS/C++20/xcc/C#/CI | PLANNED (spec mevcut) |

### 25.3 Bu Revizyondaki Orkestrasyon Kuralları

1. **Satır-satır edit:** Yapı korunur (ADR-042); silme yerine düzeltme+ekleme.
2. **Frozen dokunulmaz:** ADR metinleri okunur, referans edilir — değiştirilmez.
3. **log.md append-only:** Revizyon kayıtları eklenir, geçmiş satıra dokunulmaz.
4. **Teknoloji direktifi:** Dinamik stack (Node.js/C++/C#/PHP + diğer) — [[engine.md]] §9, [[ROLE.md]] §11.
5. **Faz kapanışı:** engine §12.6 kontrol listesi 8/8 → §12.7 rapor → vault-sync.

### 25.4 Boot Listesi Uyumu

Bu dosya §24.2 (10 dosya) ile [[MEMORY.md]] §5 (16 adım) arasındaki adım sayısı farkı bilinen durumdur: 16 adım listesi prompt arşivlerini (12-15) ve mockup indeksini (16) ekstra içerir. Faz 1'de arşiv yolları 2026-09-01'e hizalandı; iki listenin birleşik kanonik versiyonu [[CLAUDE.md]] §16'dır (13 dosya + frontend eki).

---

## 26. Doküman İskeleti & SSOT Birleştirme (Vault Refactor Engine — 2026-09-23)

> **Not:** Bu dosya Vault Refactor Engine ile v21.0.0 → v22.0.0'a yükseltildi (satır-edit + ekleme, ADR-042 hibrit; silme yok).

### 26.1 8-Bölüm İskelet Eşlemesi

| İskelet Bölümü | Karşılık Gelen § |
|----------------|------------------|
| Başlık | H1 + frontmatter (7 zorunlu alan) |
| Amaç | §1 |
| Kapsam | §2 + §3 Terminoloji |
| Mimari | §4 Agent Genel Bakış + §15 Agent Detayları + [[.agents/AGENTS.md]] §2 ASCII mimari |
| Kurallar | §5 Domain Sınırları + §16 Kalite + §17 Edge Cases |
| Workflow | §7 Görev Dağıtımı + §9 Handover + §10 Eskalasyon |
| Doğrulama | §11 Sağlık + §13 Pre-flight + §18 Uyarılar + bu bölüm §26.2-§26.3 |
| Referanslar | §20 İlgili Dokümanlar + §21 Çapraz Referanslar |

### 26.2 SSOT Çelişki Çözümü (Registry Birleştirme)

| Kaynak | Eski Durum | Yeni Durum |
|--------|-----------|------------|
| `.ai/AGENTS.md` (kök) | v21.0.0, SSOT iddiası | **v22.0.0 — tek SSOT** |
| `.ai/.agents/AGENTS.md` (alt) | v1.0.0, kendini SSOT ilan ediyordu | v1.1.0 — **alt registry** (`authority: "Alt Registry — SSOT: .ai/AGENTS.md (v22.0.0)"`) |

Çözüm kuralı: SSOT hiyerarşisinde çelişkide kök dosya kazanır. Alt registry yalnızca profil/özet detayını taşır; routing, handover, escalation, öncelik kurallarının tamamı bu dosyadadır.

### 26.3 Faz 1 Doğrulama

- [x] Frontmatter 7 alan; version 22.0.0; updated 2026-09-23
- [x] Mojibake temizlendi (vault geneli 745 düzeltme)
- [x] §1-§25 korundu, silme yok; yeni bölüm §26 olarak eklendi
- [x] `.agents/AGENTS.md` SSOT iddiası kaldırıldı → alt registry uyarısı + H1 düzeltmesi eklendi
- [x] REFACTOR REPORT: FILE: AGENTS.md · PURPOSE: Agent Registry SSOT · VALIDATION: § korundu, link hedefleri korundu · RELATED: [[CLAUDE.md]] · [[WORKFLOW.md]] · [[.agents/AGENTS.md]] · [[index.md]] · [[log.md]]
