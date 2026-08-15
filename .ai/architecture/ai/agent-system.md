---
title: "CoreMusic — Agent System"
type: architecture
category: agent-system
updated: 2026-08-09
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
total_agents: 11
---

# CoreMusic — Agent System

**Zorunlu Bağlantılar:** [[index]] · [[AGENTS.md]] · [[brain.md]]

---

## 1. Amaç

CoreMusic 11-lı agent sisteminin teknik mimarisini tanımlar. Her agent uzmanlık alanında çalışır, Master Orchestrator (MO) koordine eder. Domain boundary, handover protokolü ve eskalasyon mekanizması dahil.

---

## 2. Agent Envanteri (11 Agent)

| # | Agent | Kod Adı | Katman | Teknoloji | Sorumluluk |
|---|-------|---------|--------|-----------|------------|
| 1 | **Master Orchestrator** | `mo` | Koordinasyon | Vault System, log.md | Görev dağıtımı, handover, eskalasyon |
| 2 | **Backend Architect** | `backend` | L2 | PHP 8.4, PDO, PageRouter | API endpoint'leri, middleware, routing |
| 3 | **UI Designer** | `ui` | L3 | Vanilla JS, ITCSS, BEM | Frontend, CSS, responsive, accessibility |
| 4 | **Security Engineer** | `security` | L1 | OWASP, Argon2id, AES-256-GCM | Güvenlik, CSRF, CSP, session |
| 5 | **Data Engineer** | `data` | L0 | MySQL 9, PDO, BCNF | Veritabanı, schema, migration |
| 6 | **Embedded Engineer** | `embedded` | L0 | C++20, JUCE 9, ASIO SDK | Ses motoru, DSP, donanım sürücüleri |
| 7 | **QA Engineer** | `qa` | Cross-cutting | PHPUnit 11, Vitest, Playwright | Test, coverage, E2E |
| 8 | **DevOps Engineer** | `devops` | CI/CD | GitHub Actions, Docker | CI/CD, container, monitoring |
| 9 | **Audio HW Engineer** | `audio-hw` | HW | PCM3168A, AK4458, Class AB | DAC/ADC, PCB, amplifikatör |
| 10 | **DSP Firmware Engineer** | `dsp-fw` | FW | XMOS XU316, I2S/TDM | DSP zinciri, firmware, sürücüler |
| 11 | **Windows SW Engineer** | `win-sw` | PLAT | WASAPI, COM, WinRT | Windows ses, sürücü, platform |

---

## 3. Agent Detayları

### 3.1 Master Orchestrator

| Özellik | Değer |
|---------|-------|
| Katman | Koordinasyon |
| Sorumluluk | Görev dağıtımı, handover, eskalasyon, loglama |
| Karar Yetkisi | Task assignment, priority setting, handover authorization |
| Yasak | Doğrudan kod yazma (sadece koordinasyon) |
| Dosya Erişimi | Tüm `.ai/` vault'u okuyabilir, `log.md`'ye yazabilir |
| Başlatma | Her oturumun başında aktif olur |
| Kapanış | Tüm görevler tamamlandığında otomatik kapanır |

### 3.2 Backend Architect

| Özellik | Değer |
|---------|-------|
| Katman | L2 (Routing) |
| Teknoloji | PHP 8.4 (strict_types), PDO, PageRouter |
| Sorumluluk | API endpoint'leri, middleware pipeline, routing |
| Yasak | Frontend kodu, DB şeması tasarımı, security testi |
| Dosya Erişimi | `*.php`, `*.json` (composer), `*.ini` |
| Test | PHPUnit 11, ≥%80 coverage |
| Kurallar | `declare(strict_types=1)`, PDO prepared statement, explicit column list |

### 3.3 UI Designer

| Özellik | Değer |
|---------|-------|
| Katman | L3 (Presentation) |
| Teknoloji | Vanilla JS ES6+, ITCSS 9-layer, BEM, TrustedTypes |
| Sorumluluk | Frontend kodlama, CSS mimarisi, responsive, accessibility |
| Yasak | PHP backend kodu, DB sorgusu, security konfigürasyonu |
| Dosya Erişimi | `*.js`, `*.css`, `*.html`, `*.svg` |
| Test | Vitest, ≥%80 coverage |
| Kurallar | `innerHTML` yasak (DOMParser+TrustedTypes), framework yasak, `var` yasak |

### 3.4 Security Engineer

| Özellik | Değer |
|---------|-------|
| Katman | L1 (Security) |
| Teknoloji | OWASP Top 10:2025, Argon2id, AES-256-GCM, APCu |
| Sorumluluk | Güvenlik middleware'leri, şifreleme, CSRF, CSP, rate limiting |
| Yasak | Backend/Frontend kodu, DB şeması tasarımı |
| Dosya Erişimi | Security middleware, `.env`, credential vault |
| Test | OWASP checklist, penetration test |
| Kurallar | `csrf_token` key (ADR-010), `hash_equals()`, Argon2id (64MB/4/2) |

### 3.5 Data Engineer

| Özellik | Değer |
|---------|-------|
| Katman | L0 (Infrastructure) |
| Teknoloji | MySQL 9, SQLite, PDO, BCNF |
| Sorumluluk | 18 BCNF DB yönetimi, schema tasarımı, migration, query optimization |
| Yasak | PHP/JS kodu, security politikası |
| Dosya Erişimi | `*.sql`, `*.php` (migration), `*.json` (schema) |
| Test | Schema validation, BCNF audit |
| Kurallar | ORM yasak (ADR-002), SELECT * yasak, BCNF zorunlu, prepared statement |

### 3.6 Embedded Engineer

| Özellik | Değer |
|---------|-------|
| Katman | L0 (Hardware) |
| Teknoloji | C++20, JUCE 9, ASIO SDK 2.3.4, XMOS XU316 |
| Sorumluluk | C++ ses motoru, DSP, donanım sürücüleri, ASIO/WASAPI |
| Yasak | PHP/JS/SQL kodu |
| Dosya Erişimi | `*.cpp`, `*.h`, `*.cmake`, `*.json` (vcpkg) |
| Test | Google Test, ≥%80 coverage |
| Kurallar | Zero-allocation, lock-free, noexcept, cache-line alignment, PCM5122 yasak |

### 3.7 QA Engineer

| Özellik | Değer |
|---------|-------|
| Katman | Cross-cutting |
| Teknoloji | PHPUnit 11, Vitest, Playwright 1.40, Google Test |
| Sorumluluk | Test coverage ≥80%, unit/integration/E2E, kalite güvencesi |
| Yasak | Production kodu, DB tasarımı, security politikası |
| Dosya Erişimi | `tests/**/*.php`, `tests/**/*.test.js`, `*.test.cpp` |
| Piramid | Unit %70, Integration %20, E2E %10 |

### 3.8 DevOps Engineer

| Özellik | Değer |
|---------|-------|
| Katman | CI/CD |
| Teknoloji | GitHub Actions, Docker, GitLeaks, PowerShell/Bash |
| Sorumluluk | CI/CD pipeline, container yönetimi, monitoring |
| Yasak | Application kodu, DB tasarımı, security politikası |
| Dosya Erişimi | `*.yml`, `*.yaml`, `Dockerfile`, `*.sh`, `*.ps1` |
| Test | Pipeline test, smoke test |
| Kurallar | GitLeaks her commit'te, health check tüm servislerde, rollback stratejisi |

### 3.9 Audio HW Engineer

| Özellik | Değer |
|---------|-------|
| Katman | HW (Hardware) |
| Teknoloji | PCM3168A, AK4458, Class AB, PCB design |
| Sorumluluk | DAC/ADC tasarımı, PCB layout, amplifikatör devreleri |
| Yasak | Yazılım kodu |
| Dosya Erişimi | `*.kicad_sch`, `*.kicad_pcb`, `*.csv` (BOM) |
| Test | Hardware test protokolleri, SNR/THD ölçümü |

### 3.10 DSP Firmware Engineer

| Özellik | Değer |
|---------|-------|
| Katman | FW (Firmware) |
| Teknoloji | XMOS XU316, I2S, TDM, RTOS |
| Sorumluluk | DSP firmware geliştirme, XMOS programming, audio streaming |
| Yasak | Web/PHP kodu |
| Dosya Erişimi | `*.xc`, `*.c`, `*.h`, `*.xn` |
| Test | Hardware-in-the-loop test |

### 3.11 Windows SW Engineer

| Özellik | Değer |
|---------|-------|
| Katman | PLAT (Platform) |
| Teknoloji | WASAPI, COM, WinRT, WDK |
| Sorumluluk | Windows ses sürücüleri, WASAPI entegrasyonu, platform optimizasyonu |
| Yasak | Donanım tasarımı |
| Dosya Erişimi | `*.cpp`, `*.h`, `*.inf`, `*.inx` |
| Test | Windows driver test, WHQL certification |

---

## 4. Agent Lifecycle

```
Initialize → Health Check → Task Receive → Execute → Report → Standby
     ↓           ↓              ↓            ↓         ↓          ↓
  Boot (25s)   10s heartbeat   Queue       Agent    Log.md    Ready
```

---

## 5. Agent Communication Matrix

| Kaynak → Hedef | İletişim Tipi | Protokol |
|-----------------|---------------|----------|
| MO → Specialist | Task dispatch | Internal API |
| Specialist → MO | Task complete/fail | Internal API |
| Specialist ↔ Specialist | Handover (MO üzerinden) | Handover Protocol |
| Any → Log | Append-only audit | File System |
| Any → Memory | Session state | Internal |

---

## 6. Agent Skills (Zorunlu 5)

| # | Skill | Amaç | Kullanım |
|---|-------|------|----------|
| 1 | `/prompt-maker` | Prompt üretim motoru | Her görev başlangıcında |
| 2 | `/brainstorming` | Fikir üretimi ve keşif | Yaratıcı work öncesi |
| 3 | `/vault-sync` | Vault senkronizasyonu | Seans sonunda |
| 4 | `/hallucination-control` | Halüsinasyon doğrulama | Kod yazma öncesi |
| 5 | `Red Team · Truth Mode · Human Mode` | Her zaman aktif | Sürekli |

---

## 7. Domain Boundary (Dosya Tipi → Agent)

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
| `*.kicad_*` (PCB) | Audio HW Engineer | ❌ |
| `*.xc` (XMOS firmware) | DSP Firmware Engineer | ❌ |
| `*.inf` (Windows driver) | Windows SW Engineer | ❌ |
| `log.md` (audit trail) | Tüm ajanlar (append-only) | ✅ Sadece ekleme |
| `.ai/` vault | MO (koordinasyon) | ✅ Okuma serbest |

**Layer Violation:** L0 → L2/L3 veya L1 → L3 gibi kural ihlalleri tespit edilirse derhal revert + log ERROR.

---

## 8. Agent Health States

| Durum | Kod | Tanım | Aksiyon |
|-------|-----|-------|---------|
| Healthy | 200 | Görev tamamlandı | Devam |
| Degraded | 301 | Yavaş yanıt (>15s) | Uyar, devam |
| Retry | 408 | Timeout, yeniden dene | Max 3 retry |
| Failed | 500 | 3 retry başarısız | Queue reset |
| Dead | 503 | Yanıt yok, escalation | Derhal escalation |

---

## 9. Context Lock

| Kural | Değer |
|-------|-------|
| Max lock süresi | 30s |
| Deadlock prevention | MO en eski kilidi kırar |
| Öncelik | CRITICAL > HIGH > MEDIUM > LOW |
| Logging | Lock acquire/release log.md'ye |
| Deadlock çözümü | Timeout + priority override |

---

## 10. Handover Protokolü

```
[Kaynak Agent] → [Handover Request] → [Hedef Agent] → [Onay/Red] → [Confirmation]
```

### 10.1 Handover Mesaj Formatı

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

### 10.2 Handover Senaryoları

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

## 11. Eskalasyon Protokolü

```
Level 1 (Domain Lead) → Level 2 (Tech Lead) → Level 3 (Arch Lead) → İnsan
```

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

---

## 12. Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Agent | [[AGENTS.md]] §4 | Agent tanımları |
| § 3 Detay | [[AGENTS.md]] §15 | Agent profilleri |
| § 4 Lifecycle | [[AGENTS.md]] §11 | Sağlık kontrolü |
| § 7 Domain | [[brain.md]] §5 | L0-L3 katmanları |
| § 9 Lock | [[AGENTS.md]] §12 | Context lock |
| § 10 Handover | [[AGENTS.md]] §9 | Handover protokolü |
| § 11 Eskalasyon | [[AGENTS.md]] §10 | Eskalasyon protokolü |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
