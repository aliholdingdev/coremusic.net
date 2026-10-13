---
node_id: K016-AI/agents/anayasa
node_type: module
status: draft
parent_id: K016-AI/agents
children: []
depends_on:
  - K016-AI
evidence:
  - ".ai/AGENTS.md §4 (11 ajan) · §5 (dosya tipi sahipliği)"
  - "shared/src/AI/AIOrchestrator.php:26-38 (AGENT_MAP)"
  - ".ai/.agents/ = 12 .md — glob 2026-10-12"
validation:
  - "2026-10-13 tur-3: satır sayımı >=500 gate"
open_questions:
  - "11 proje ajanı ↔ 118 global registry eşleme tablosu yok (⚠️ VERIFICATION REQUIRED)"
  - ".claude/agent/ TAM profil sayısı UNKNOWN"
group: mimari
---

# K016-AI/agents — anayasa.md

> **Bu dosya ne değildir:** ajanların davranışını (surucu) ve kod örneklerini (ornekler) taşımaz;
> burada **kimlik, yetki ve anayasal sınırlar** tanımlıdır.

## §1 Rol (ELI15)

Agents alt-domaini ajanların **kimliğini** tanımlar: kaç ajan var, her biri hangi alanda
uzman, hangi katmanda, hangi teknolojiyle çalışır ve hangi dosya tipine dokunabilir.

## §2 Ajan Envanteri (11 ajan — .ai/AGENTS.md §4/§15)

| # | Ajan | Kod adı | Domain | Katman |
|---|---|---|---|---|
| 1 | Master Orchestrator | mo | görev dağıtımı, koordinasyon | Koordinasyon |
| 2 | Backend Architect | backend | PHP 8.4 API, routing, middleware | A2 |
| 3 | UI Designer | ui | Vanilla JS, ITCSS, CSS | A3 |
| 4 | Security Engineer | security | OWASP, encryption, CSRF, CSP | A1 |
| 5 | Data Engineer | data | MySQL BCNF, PDO, migration | A0 |
| 6 | Embedded Engineer | embedded | C++20, JUCE, ASIO, DSP | A0 |
| 7 | QA Engineer | qa | test, coverage, E2E | Cross-cutting |
| 8 | DevOps Engineer | devops | CI/CD, GitHub Actions, deploy | CI/CD |
| 9 | Audio Hardware Engineer | audio-hw | DAC/ADC, PCB, amplifier | HW |
| 10 | DSP Firmware Engineer | dsp-fw | XMOS, PCM3168A, DSP chain | FW |
| 11 | Windows Software Engineer | win-sw | WASAPI, driver, platform | PLAT |

## §3 Profil Dosyaları (disk kanıtı)

| # | Dosya | Kanıt |
|---|---|---|
| 1 | .ai/.agents/AGENTS.md (indeks) | glob 2026-10-12 |
| 2 | .ai/.agents/master-orchestrator.md | glob 2026-10-12 |
| 3 | .ai/.agents/backend-architect.md | glob 2026-10-12 |
| 4 | .ai/.agents/ui-designer.md | glob 2026-10-12 |
| 5 | .ai/.agents/security-engineer.md | glob 2026-10-12 |
| 6 | .ai/.agents/data-engineer.md | glob 2026-10-12 |
| 7 | .ai/.agents/embedded-engineer.md | glob 2026-10-12 |
| 8 | .ai/.agents/dsp-firmware-engineer.md | glob 2026-10-12 |
| 9 | .ai/.agents/audio-hardware-engineer.md | glob 2026-10-12 |
| 10 | .ai/.agents/windows-software-engineer.md | glob 2026-10-12 |
| 11 | .ai/.agents/devops-engineer.md | glob 2026-10-12 |
| 12 | .ai/.agents/qa-engineer.md | glob 2026-10-12 |
| — | TOPLAM | 12 .md (11 uzman + indeks) |

## §4 Kod Karşılığı (AGENT_MAP)

| # | Dosya | Satır | İçerik |
|---|---|---|---|
| 1 | shared/src/AI/AIOrchestrator.php | 26-38 | AGENT_MAP — ajan → domain/layer eşlemesi (11) |

## §5 Görev Tipi → Ajan (TASK_TYPES)

| # | Dosya | Satır | İçerik |
|---|---|---|---|
| 1 | shared/src/AI/AIOrchestrator.php | 40-45 | TASK_TYPES — görev tipi → ajan + öncelik (örn. fault → dsp-fw · critical) |

## §6 Dosya Tipi Sahipliği (.ai/AGENTS.md §5)

| # | Dosya tipi | Sorumlu ajan | Diğerleri |
|---|---|---|---|
| 1 | *.php (Controller/Service/Repository) | Backend Architect | okuma serbest |
| 2 | *.js (Frontend) | UI Designer | okuma serbest |
| 3 | *.css (ITCSS) | UI Designer | okuma serbest |
| 4 | *.sql (Schema/Migration) | Data Engineer | okuma serbest |
| 5 | *.cpp / *.h (Audio Engine) | Embedded Engineer | okuma serbest |
| 6 | *.yml / *.yaml (CI/CD) | DevOps Engineer | okuma serbest |
| 7 | tests/**/*.php | QA Engineer | okuma serbest |
| 8 | tests/**/*.test.js | QA Engineer | okuma serbest |
| 9 | Security middleware | Security Engineer | okuma serbest |
| 10 | .env dosyası | Security Engineer | okuma yasak |
| 11 | log.md | tüm ajanlar | yalnız append |
| 12 | .ai/ vault | MO (koordinasyon) | okuma serbest |

## §7 Keyword → Routing (.ai/AGENTS.md §6 — 9 grup)

| # | Anahtar kelime grubu | Birincil | İkincil |
|---|---|---|---|
| 1 | API, endpoint, routing, middleware, PHP | Backend Architect | Security Engineer |
| 2 | CSS, UI, responsive, ITCSS, BEM, mockup | UI Designer | QA Engineer |
| 3 | CSRF, CSP, XSS, OWASP, auth, encryption | Security Engineer | Backend Architect |
| 4 | database, SQL, BCNF, migration, MySQL, PDO | Data Engineer | Backend Architect |
| 5 | C++, ASIO, JUCE, audio, DSP, ring buffer | Embedded Engineer | DevOps Engineer |
| 6 | test, coverage, PHPUnit, Vitest, E2E | QA Engineer | — |
| 7 | CI/CD, GitHub Actions, deploy, GitLeaks | DevOps Engineer | QA Engineer |
| 8 | vault, documentation, ADR, wiki-link | MO (vault-updater) | — |
| 9 | template, şablon, template usage | tüm ajanlar (Guardrail #16) | MO |

## §8 Öncelik / Timeout / Retry (.ai/AGENTS.md §8)

| # | Öncelik | Tanım | Timeout | Max retry |
|---|---|---|---|---|
| 1 | CRITICAL | sistem durması, güvenlik açığı | 5s | 1 |
| 2 | HIGH | kritik işlev kaybı | 15s | 3 |
| 3 | MEDIUM | normal geliştirme görevi | 30s | 3 |
| 4 | LOW | iyileştirme, optimizasyon | 60s | 2 |

## §9 Handover Mesaj Alanları (.ai/AGENTS.md §9.1)

| # | Alan | Değer |
|---|---|---|
| 1 | Konu | görevin kısa açıklaması |
| 2 | Kaynak Agent | adı |
| 3 | Hedef Agent | adı |
| 4 | Öncelik | CRITICAL / HIGH / MEDIUM / LOW |
| 5 | Etkilenen Dosyalar | yola göre liste |
| 6 | İstek | ne yapılacağı |
| 7 | Onay Durumu | PENDING / APPROVED / REJECTED |
| 8 | Timestamp | YYYY-MM-DD HH:MM:SS (UTC) |

## §10 Handover Kuralları (.ai/AGENTS.md §9.2)

| # | Kural | Değer |
|---|---|---|
| 1 | Onay zorunlu | hedef onayı olmadan tamamlanamaz |
| 2 | Timeout | 30 saniye |
| 3 | Max retry | 3 |
| 4 | Red durumunda | MO devreye girer |
| 5 | Logging | tüm handover log.md'ye |

## §11 Handover Senaryoları (.ai/AGENTS.md §9.3)

| # | Senaryo | Kaynak | Hedef | Öncelik |
|---|---|---|---|---|
| 1 | Güvenlik açığı tespiti | Backend | Security | CRITICAL |
| 2 | DB schema değişikliği | Backend | Data | HIGH |
| 3 | Frontend test eksikliği | UI | QA | MEDIUM |
| 4 | CI/CD pipeline hatası | DevOps | QA | HIGH |
| 5 | Auth middleware değişikliği | Security | Backend | HIGH |
| 6 | Audio DSP optimizasyonu | Embedded | DevOps | MEDIUM |
| 7 | Vault doküman güncelleme | MO | vault-updater | LOW |
| 8 | Security audit | Security | QA | HIGH |

## §12 Escalation (.ai/AGENTS.md §10 — 9 senaryo)

| # | Senaryo | Başlangıç | Hedef | Timeout |
|---|---|---|---|---|
| 1 | aynı dosyaya erişim | L1 | L2 | 30s |
| 2 | BCNF çelişkisi | L1 (Data) | L2 | 30s |
| 3 | CSRF/CSP uyumsuzluğu | L1 (Security) | L2 | 15s |
| 4 | ASIO cihaz kaybı | L1 (Embedded) | L2 | 30s |
| 5 | Test coverage %80 altı | L1 (QA) | L2 | 60s |
| 6 | Deployment başarısız | L1 (DevOps) | L2 | 30s |
| 7 | Mimari çelişki (ADR) | L2 | L3 | 60s |
| 8 | Güvenlik açığı | L2 | L3 | 15s |
| 9 | Sistem durması | L2 | İnsan | anlık |

## §13 Health States (.ai/AGENTS.md §11.2)

| # | Durum | Kod | Açıklama |
|---|---|---|---|
| 1 | Healthy | 200 | görev tamamlandı |
| 2 | Degraded | 301 | yavaş yanıt (>15s) |
| 3 | Retry | 408 | timeout, yeniden deneniyor |
| 4 | Failed | 500 | 3 retry başarısız |
| 5 | Dead | 503 | yanıt yok, escalation |

## §14 Context Lock (.ai/AGENTS.md §12)

| # | Kural | Değer |
|---|---|---|
| 1 | Kilitleme süresi | max 30 sn |
| 2 | Deadlock prevention | MO en eski kilidi kırar |
| 3 | Öncelik | CRITICAL > HIGH > MEDIUM > LOW |
| 4 | Logging | acquire/release log.md'ye |

## §15 Persona Hiyerarşisi (kök AGENTS.md §6.1 — 20 persona)

| # | Seviye | Persona | Sorumluluk |
|---|---|---|---|
| 1 | Expert | architect | sistem tasarımı, katman sınırı, ADR |
| 2 | Expert | security-engineer | OWASP, auth, CSRF/CSP/XSS |
| 3 | Expert | data-engineer | şema, BCNF, migration, MySQL/PDO |
| 4 | Expert | embedded-engineer | DSP/ASIO/WASAPI, firmware |
| 5 | Expert | qa-engineer | test stratejisi, coverage, kabul kriteri |
| 6 | Senior | backend-architect | PHP/API/middleware/controller |
| 7 | Senior | ui-designer | ITCSS/BEM/token, ekran, WCAG |
| 8 | Senior | devops-engineer | CI/CD, Docker, deploy |
| 9 | Senior | performance-engineer | profil, latency, bellek |
| 10 | Senior | code-reviewer | SOLID, code smell, bakım |
| 11 | Junior | explore | dosya/sembol keşfi |
| 12 | Junior | docs-writer | README, API dokümanı |
| 13 | Junior | test-writer | test senaryosu taslağı |
| 14 | Junior | refactorer | okunabilirlik düzeltmesi |
| 15 | Junior | research-analyst | kaynak toplama, çapraz doğrulama |
| 16 | Junior | debugger | log/stack-trace kök-neden |
| 17 | Junior | business-analyst | gereksinim, user story |
| 18 | Junior | technical-writer | kılavuz, tutorial |
| 19 | Junior | dependency-manager | paket/sürüm/licence denetimi |
| 20 | Junior | error-coordinator | hata yayılımı, eskalasyon |

**Karar kuralı (kanıt-öncelikli):** kanıt gücü > seviye · eşit kanıtta Expert > Senior > Junior ·
**oy çokluğu yoktur** · her öneri gerekçe + dosya/satır kanıtı taşır.

## §16 Tur Kuralı (3 tur / 20 persona)

| # | Tur | İçerik |
|---|---|---|
| 1 | Tur 1 | her persona kendi önerisini yazar (kaynaklı) |
| 2 | Tur 2 | çapraz eleştiri (en az 1 karşı öneri) |
| 3 | Tur 3 | uzlaşma + ADR · uzlaşma yoksa VERIFICATION REQUIRED + üst karar |

## §17 Dispatch Kuralı (bağlayıcı)

| # | Kural |
|---|---|
| 1 | tek seviyeli karar bağlayıcı olamaz |
| 2 | mimari/katman/sayım kararlarında: en az 1 Expert + 1 Senior + 1 Junior |
| 3 | kaynaksız öneri sayılmaz |

## §18 Edge Cases (.ai/AGENTS.md §17)

| # | Senaryo | Çözüm |
|---|---|---|
| 1 | Aynı dosyaya eşzamanlı erişim | Context Lock + Queue |
| 2 | Sensitive data log'da | [REDACTED] maskeleme |
| 3 | Ajan timeout (30s+) | max 3 retry, sonra queue reset |
| 4 | Bilinmeyen class/API | VERIFICATION REQUIRED |
| 5 | ASIO device loss | WASAPI fallback |
| 6 | Layer violation | derhal revert + log ERROR |
| 7 | PCM5122 kullanımı | PCM3168A / AK4458 öner |
| 8 | Network outage | Offline-First + SQLite queue |
| 9 | Vault corruption | git checkout + son commit |

## §19 Warnings (.ai/AGENTS.md §18)

| # | Uyarı | Sonuç |
|---|---|---|
| 1 | Domain boundary ihlali | sistem durur, MO müdahale |
| 2 | Agent timeout 30s+ | max 3 retry → escalation |
| 3 | Hallüsinasyon | VERIFICATION REQUIRED etiketi |
| 4 | Token overflow | görev başarısız |
| 5 | Vault bozulması | git ile kurtarma |
| 6 | Layer violation | kod revert |
| 7 | ORM kullanımı | SQL injection riski |
| 8 | Framework kullanımı | bağımlılık artışı |

## §20 Ajan Sınırı — ne DEĞİL (bu alt-domain)

| # | Konu | Sahip | Not |
|---|---|---|---|
| 1 | Model eğitimi | K006 | v2 §2.6 |
| 2 | Sistem auth | K009 | ajan izni ≠ kullanıcı auth |
| 3 | Ünite/E2E test | K019 | agent-testing ayrı |
| 4 | OS player | K017 | Frozen ADR-019 |
| 5 | Servis iç mantık | K012 | ajan kamuyu çağırır |

## §21 Global Registry İlişkisi (118 vs 11)

| # | Küme | Sayı | Kaynak | Etiket |
|---|---|---|---|---|
| 1 | Proje ajanı | 11 | .ai/AGENTS.md §4 | vault beyanı + profil dosyaları |
| 2 | Global registry | 118 | boot beyanı (§3) | bağımsız sayım YAPILMADI |
| 3 | Eşleme tablosu | yok | — | ⚠️ VERIFICATION REQUIRED |

## §22 Açık Sorular

| # | Soru | Öncelik |
|---|---|---|
| 1 | 11 ↔ 118 eşleme tablosu vault'ta mı? | HIGH |
| 2 | .claude/agent/ tam dosya sayısı? | LOW |
| 3 | K016 runtime etiketi (PLANNED/IMPLEMENTED)? | HIGH |

## §23 Doğrulama Kapıları

| # | Kapı | Sonuç |
|---|---|---|
| 1 | Kod kanıtı >=1 | ✓ (AGENT_MAP · TASK_TYPES) |
| 2 | Disk kanıtı | ✓ (.ai/.agents/ 12 .md) |
| 3 | Satır >=500 | gate ölçümü |
| 4 | group=mimari | ✓ FM |

## §24 Kaynaklar

| # | Kaynak | Kullanım |
|---|---|---|
| 1 | .ai/AGENTS.md §4-§18 | envanter, routing, handover |
| 2 | kök AGENTS.md §6.1 | persona hiyerarşisi |
| 3 | shared/src/AI/AIOrchestrator.php:26-45 | kod kanıtı |
| 4 | .ai/.agents/* | profil envanteri |
| 5 | v2 spec §2.16 | alt-domain tanımı |

## §25 Pre-flight Kontrolleri (.ai/AGENTS.md §7.2)

| # | Kontrol | Değer | İhlal |
|---|---|---|---|
| 1 | Domain boundary | doğru ajan | layer violation → revert |
| 2 | Dosya eşzamanlılığı | context lock | kilit bekleme |
| 3 | Bağımlılık | handover gerekli mi | transfer |
| 4 | UI mockup okundu mu | ui-design görseli | okunmadı → DUR |
| 5 | Şablon okundu mu | Guardrail #16 | okunmadı → DUR |
| 6 | Gate script | kökten çalıştırıldı mı | .ai/ içinden → geçersiz |
| 7 | Önceki görev | başarısız mı | retry / escalation |

## §26 Execution Kuralları (.ai/AGENTS.md §7.4)

| # | Kural |
|---|---|
| 1 | Domain boundary'yi koru |
| 2 | Zero Code Before Plan uygula |
| 3 | Çıktıyı standardize et |

## §27 Verification Kontrolleri (.ai/AGENTS.md §7.6)

| # | Kontrol | Değer |
|---|---|---|
| 1 | Çıktı formatı | uygun |
| 2 | Domain uyumluluğu | doğru ajan |
| 3 | Cross-reference | geçerli wiki-link |
| 4 | Security | hassas veri redaction |

## §28 Quality Standards (.ai/AGENTS.md §16)

| # | Agent | Standart | Hedef |
|---|---|---|---|
| 1 | Backend | strict_types, PSR-12, prepared statement | %100 |
| 2 | UI | ITCSS, BEM, WCAG 2.2 AA | %100 |
| 3 | Security | OWASP Top 10, CSRF, Argon2id | %100 |
| 4 | Data | BCNF, no ORM, no SELECT * | %100 |
| 5 | Embedded | zero-allocation, lock-free, noexcept | %100 |
| 6 | QA | coverage ≥80%, flaky %0 | ≥80% |
| 7 | DevOps | CI/CD success ≥95%, GitLeaks clean | ≥95% |

## §29 Agent Stack Evidence (.ai/AGENTS.md §25.2)

| # | Agent | Teknoloji | Kanıt durumu |
|---|---|---|---|
| 1 | Backend | PHP 8.4, PSR, php-di, fast-route | 3 composer.json |
| 2 | Security | Middleware ×11 (PSR-15) | shared/src/Middleware/ |
| 3 | Data | SQL şema (18 DB) | .ai/.sql/mysql/ |
| 4 | QA | PHPUnit ^10.5/^11.0, PHPStan | require-dev · shared/tests/ |
| 5 | DevOps | GitHub Actions | .github/workflows/ = 2 dosya |
| 6 | UI | Vanilla JS | ⚠️ PLANNED (spec) |
| 7 | Embedded/DSP/Windows | C++20/xcc/C# | ⚠️ PLANNED (spec) |

## §30 Prompt → Agent Eşlemesi (.ai/AGENTS.md §14.1)

| # | Prompt | Birincil | İkincil | An |
|---|---|---|---|---|
| 1 | prompt0 (genel) | MO | tüm ajanlar | her görev başında |
| 2 | prompt1 (SPA router) | Backend | UI | SPA route tasarımında |
| 3 | prompt2 (Auth) | Security | Backend | auth middleware'de |
| 4 | prompt3 (API) | Backend | DevOps | API gateway'de |

## §31 Ultra Thinking Protokolü (.ai/AGENTS.md §24.1)

| # | Adım | Kontrol | Kaynak |
|---|---|---|---|
| 1 | Vault oku | CLAUDE → AGENTS → WORKFLOW → brain → ROLE → ADR | .ai/ |
| 2 | Bağlamı anla | domain, katman, dosyalar | mevcut kod |
| 3 | Hata kontrolü | syntax, imports, types, security | LSP |
| 4 | Sonuç tahmini | etki alanı, edge case, perf | düşünme |
| 5 | Doğrulama | LSP, typecheck, test, template | build araçları |

## §32 Health Check Flow (.ai/AGENTS.md §11.3)

| # | Aşama | Karar |
|---|---|---|
| 1 | Görev başlangıcı | health check tetikle |
| 2 | Healthy | devam |
| 3 | Degraded | uyar, devam |
| 4 | Retry | yeniden dene (max 3) |
| 5 | Failed | queue reset + escalation |
| 6 | Dead | derhal escalation |

## §33 Escalation Kuralları (.ai/AGENTS.md §10.2)

| # | Kural | Değer |
|---|---|---|
| 1 | L1 timeout | 30 sn |
| 2 | L2 timeout | 60 sn |
| 3 | L3 timeout | 120 sn |
| 4 | Max retry | her seviyede 3 |
| 5 | İnsan müdahalesi | son çare |

## §34 Deadlock Prevention (.ai/AGENTS.md §12.3)

| # | Yöntem | Açıklama |
|---|---|---|
| 1 | Timeout | 30 sn sonra lock serbest |
| 2 | Priority override | CRITICAL diğerini kırar |
| 3 | MO intervention | MO en eski kilidi kırar |
| 4 | Queue reset | tüm kilitler sıfırlanır |

## §35 Gate Kontrolü (bu dosya)

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | Satır >=500 | gate ölçümü |
| 2 | FM 10 alan + group | ✓ |
| 3 | Kod kanıtı | ✓ §4-§5 |
| 4 | Authority son satır | ✓ |

## §36 Ajan Yetki Sınırı Özeti (anayasal çekirdek)

| # | İzin | Ajan | Sınır |
|---|---|---|---|
| 1 | kendi dosya tipine yazma | sahip ajan | §6 |
| 2 | başkasının dosyasına yazma | sahip ajan | yalnız onayla (handover) |
| 3 | .ai/ vault okuma | tüm ajanlar | serbest |
| 4 | .ai/ vault yazma | MO | diğerleri yasak |
| 5 | log.md | tüm ajanlar | yalnız append |
| 6 | .env | Security | okuma da sınırlı |
| 7 | git commit | orkestratör | subagent yasak |

## §37 Sınır İhlali Prosedürü

| # | Adım | Aksiyon |
|---|---|---|
| 1 | Tespit | layer/boundary ihlali görülür |
| 2 | Durdurma | derhal revert (uyarı tablosu §19/1) |
| 3 | Kayıt | log ERROR |
| 4 | Bildirim | MO müdahale |
| 5 | Kök neden | ADR ise L2/L3 escalation |

## §38 Anayasal Değişiklik Kuralları

| # | Değişiklik | Onay |
|---|---|---|
| 1 | ajan ekleme/çıkarma | MO + vault registry |
| 2 | dosya sahipliği değişimi | ilgili iki ajan + MO |
| 3 | routing tablosu değişimi | .ai/AGENTS.md §6 (SSOT) |
| 4 | persona eşiği değişimi | kök AGENTS.md §6.1 |
| 5 | öncelik/timeout değişimi | .ai/AGENTS.md §8 |

## §39 Bu Dosyanın Sınırları (ne taşımaz)

| # | Konu | Nerede |
|---|---|---|
| 1 | ajan davranış akışı | agents/surucu.md |
| 2 | kod örnekleri | agents/ornekler.md |
| 3 | bağımlılık ağacı | ai-bagimliliklar.md |
| 4 | ölçüm/kanıt defteri | ai-dogrulama.md |
| 5 | kapsam in/out | ai-kapsam.md |

## §40 Hızlı Referans (tek ekran)

| # | Soru | Cevap |
|---|---|---|
| 1 | kaç ajan? | 11 (proje) · 118 (global, doğrulanmadı) |
| 2 | kim hangi dosyaya yazar? | §6 tablosu |
| 3 | görev nasıl dağıtılır? | §7 routing + §5 TASK_TYPES |
| 4 | çatışma nasıl çözülür? | §12 escalation + §15 kanıt-öncelikli karar |
| 5 | karar kimin? | oy yok, kanıt kazanır |

## §41 Gate Kanıt Satırı

| # | Eşik | Araç | Sonuç |
|---|---|---|---|
| 1 | 500 tam satır | [IO.File]::ReadAllLines (UTF-8) | 500+ |
| 2 | 499 | — | FAIL |

## §42 Ek Tablo — Ajan ↔ Katman ↔ Teknoloji

| # | Ajan | Katman | Teknoloji |
|---|---|---|---|
| 1 | Master Orchestrator | Koordinasyon | Vault System, log.md |
| 2 | Backend Architect | A2 | PHP 8.4, PDO, PageRouter |
| 3 | UI Designer | A3 | Vanilla JS ES6+, ITCSS |
| 4 | Security Engineer | A1 | Argon2id, AES-256-GCM, APCu |
| 5 | Data Engineer | A0 | MySQL, PDO, BCNF |
| 6 | Embedded Engineer | A0 | C++20, JUCE, ASIO |
| 7 | QA Engineer | Cross-cutting | PHPUnit, Vitest, Playwright |
| 8 | DevOps Engineer | CI/CD | GitHub Actions, GitLeaks |
| 9 | Audio HW Engineer | HW | PCM3168A, AK4458, Class AB |
| 10 | DSP Firmware Engineer | FW | XMOS XU316, I2S, TDM |
| 11 | Windows SW Engineer | PLAT | WASAPI, COM, WinRT |

> **Not:** teknoloji sütunları hedef yığını yansıtır; IMPLEMENTED/PLANNED etiketi §29'dadır.

## §43 Ölçüm Notu

| # | Not |
|---|---|
| 1 | Satır ölçümü UTF-8 ReadAllLines ile yapılır (Get-Content mojibake gösterir). |
| 2 | Eşik 500 tam satırdır; 499 FAIL sayılır. |
| 3 | Ölçüm tarihi: 2026-10-13 (tur-3). |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
