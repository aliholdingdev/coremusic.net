---
type: guide
category: agent-registry
title: "CoreMusic — Agent Registry & Coordination Protocol"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 19.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Agent Registry & Coordination Protocol

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]]

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
| MSA limiti ve mandatory 5 skills | — |

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
| **MSA Limit** | Görev başına max 15 dosya okuma kısıtı (ADR-042) |
| **Task Queue** | Görevlerin öncelik sırasıyla beklediği kuyruk |
| **Pre-flight Check** | Görev başlamadan önce yapılan kontroller |

---

## 4. Agent Genel Bakış

| # | Agent | Kod Adı | Domain | Katman | Teknoloji |
|---|-------|---------|--------|--------|-----------|
| 1 | **Master Orchestrator** | `mo` | Görev dağıtımı, koordinasyon | Koordinasyon | Vault System, log.md |
| 2 | **Backend Architect** | `backend` | PHP 8.4 API, routing, middleware | L2 | PHP strict_types, PDO, PageRouter |
| 3 | **UI Designer** | `ui` | Vanilla JS, ITCSS, CSS, responsive | L3 | Vanilla JS ES6+, ITCSS 7-layer |
| 4 | **Security Engineer** | `security` | OWASP, encryption, CSRF, CSP | L1 | Argon2id, AES-256-GCM, APCu |
| 5 | **Data Engineer** | `data` | MySQL 9 BCNF, PDO, migration | L0 | MySQL 9, PDO, BCNF |
| 6 | **Embedded Engineer** | `embedded` | C++20, JUCE, ASIO, DSP | L0 | C++20, JUCE 8, ASIO SDK 2.3.4 |
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

---

## 7. Görev Dağıtımı Algoritması

```
Kullanıcı İsteği
  → [1. Analiz] — Keyword çıkarma, domain eşleme
    → [2. Pre-flight Checks] — MSA, bağımlılık, dosya kontrolü
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
| MSA limit | ≤15 dosya | Görev parçalanır |
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
- MSA limitine uygun dosya oku
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
| MSA limiti | ≤15 dosya |
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
| Etkilenen Dosyalar | Dosya yolu listesi (max 15) |
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

## 13. MSA Limit (ADR-042/C5)

**Görev başına MAX 15 dosya okunur.**

| Öncelik | Dosya Grubu | Max | Toplam |
|---------|-------------|-----|--------|
| P0 (Kritik) | CLAUDE.md, AGENTS.md, WORKFLOW.md | 3 | 3 |
| P1 (Yüksek) | index.md, keys.md, brain.md, MEMORY.md, log.md | 5 | 8 |
| P2 (Görev) | decisions/accepted/ADR-NNN, architecture/L[0-3]/* | 5 | 13 |
| P3 (Düşük) | testing/*, ui-design/*, personas/* | 2 | 15 |

**Kurallar:**
1. P0 → P1 → P2 → P3 sırasıyla okunur
2. Fallback: `index.md`
3. Limit aşılırsa `log.md`'ye WARN yazılır
4. Token aşımı önlenir: gereksiz dosya okunmaz
5. Seçici okuma (Sparse Attention) uygulanır

---

## 14. Zorunlu 5 Skills (ADR-042/C4)

| # | Skill | Amaç | Kullanım |
|---|-------|------|----------|
| 1 | `/prompt-maker` | Prompt üretim motoru | Her görev başlangıcında |
| 2 | `/brainstorming` | Fikir üretimi ve keşif | Yaratıcı work öncesi |
| 3 | `/vault-sync` | Vault senkronizasyonu | Seans sonunda |
| 4 | `/hallucination-control` | Halüsinasyon doğrulama | Kod yazma öncesi |
| 5 | `Red Team · Truth Mode · Human Mode` | Her zaman aktif | Sürekli |

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

### 15.3 UI Designer

| Özellik | Değer |
|---------|-------|
| Katman | L3 (Presentation) |
| Teknoloji | Vanilla JS ES6+, ITCSS 7-layer, BEM/BEMIT, TrustedTypes |
| Sorumluluk | Frontend kodlama, CSS mimarisi, responsive, accessibility |
| Yasak | PHP backend kodu, DB sorgusu, security konfigürasyonu |
| Dosya Erişimi | `*.js`, `*.css`, `*.html`, `*.svg` |
| Test Gereksinimi | Vitest, ≥%80 coverage |

**Zorunlu Kurallar:** `innerHTML` yasak (DOMParser + TrustedTypes), framework yasak (ADR-001), `var` yasak, `eval()` yasak, BEM format zorunlu.

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

### 15.5 Data Engineer

| Özellik | Değer |
|---------|-------|
| Katman | L0 (Infrastructure) |
| Teknoloji | MySQL 9, SQLite, PDO, BCNF |
| Sorumluluk | 9 BCNF DB yönetimi, schema tasarımı, migration, query optimization |
| Yasak | PHP/JS kodu, security politikası |
| Dosya Erişimi | `*.sql`, `*.php` (migration), `*.json` (schema) |
| Test Gereksinimi | Schema validation, BCNF audit |

**Zorunlu Kurallar:** ORM yasak (ADR-002), SELECT * yasak, BCNF zorunlu, soft delete, prepared statement.

### 15.6 Embedded Engineer

| Özellik | Değer |
|---------|-------|
| Katman | L0 (Hardware) |
| Teknoloji | C++20, JUCE 8, ASIO SDK 2.3.4, XMOS XU316 |
| Sorumluluk | C++ ses motoru, DSP, donanım sürücüleri, ASIO/WASAPI |
| Yasak | PHP/JS/SQL kodu |
| Dosya Erişimi | `*.cpp`, `*.h`, `*.cmake`, `*.json` (vcpkg) |
| Test Gereksinimi | Google Test, ≥%80 coverage |

**Zorunlu Kurallar:** Zero-allocation, lock-free, noexcept, cache-line alignment, PCM5122 yasak (H001).

### 15.7 QA Engineer

| Özellik | Değer |
|---------|-------|
| Katman | Cross-cutting |
| Teknoloji | PHPUnit 11, Vitest, Playwright 1.40, Google Test |
| Sorumluluk | Test coverage ≥80%, unit/integration/E2E, kalite güvencesi |
| Yasak | Production kodu, DB tasarımı, security politikası |
| Dosya Erişimi | `tests/**/*.php`, `tests/**/*.test.js`, `*.test.cpp` |
| Test Piramidi | Unit %70, Integration %20, E2E %10 |

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
| 2 | Token overflow (>15 dosya) | MSA fallback + index.md |
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
| § 5 Domain | [[CLAUDE.md]] §5 | L0-L3 katmanları |
| § 6 Routing | [[engine.md]] §2 | Orkestrasyon bölümleri |
| § 9 Handover | [[WORKFLOW.md]] §7.6 | Session init |
| § 10 Eskalasyon | [[ADR-008-bypass-auth-middleware]] | Auth bypass |
| § 13 MSA | [[ADR-042-vault-restructuring-2026-08-03]] | MSA limit |
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
| **MSA** | Max 15 dosya okuma limiti |
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
| Version | 19.0.0 |
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
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode