---
title: "CoreMusic Vault — Proje Şablonu (Yeniden Kullanılabilir Vault İskeleti)"
type: playbook
category: templates
version: "1.1.0"
status: active
authority: super-primary
updated: 2026-09-25
---

# CoreMusic Vault — Proje Şablonu

> **Amaç:** CoreMusic `.ai/` vault'unun tamamını başka bir projede yeniden kullanmak için
> çıkarılmış detaylı şablon. Bu dosya **salt doğrulanmış vault içeriğinden** üretilmiştir.
> Her tablo hücresi kaynak dosyadan alınmıştır. Doğrulanamayan/olmayan her yerde
> **`YOK`**, **`[ŞABLON NOTU]`** veya **`⚠️`** işareti vardır.
>
> **Doğrulanan kaynaklar (okunarak):** `.ai/CLAUDE.md` (§5-§12, §7, §17, §29),
> `.ai/AGENTS.md` (§4-§24), `.ai/WORKFLOW.md` (§-başlıkları), `.ai/MEMORY.md` (§-başlıkları),
> `.ai/index.md` (§-başlıkları), `.ai/.rules/senior-mode.md` (§1-§11),
> `.ai/.decisions/index.md` (sayımlar), `.ai/scripts/` (dizin listesi),
> `.workflows/` (9 dosya başlığı + `.workflows/CLAUDE.md` tam), kök `CLAUDE.md` (tam),
> kök `README.md` (§1, §4, §8, §9), `.opencode/opencode.json` (agent/komut/skills/MCP).
>
> **Kullanım:** `{...}` değişkenlerini kendi projenizle değiştirin. §10'daki
> "Olduğu Gibi Kalması Gerekenler" listesine dokunmayın.

---

## §1. Proje Kimliği ve Gerçek Teknoloji Yığını

### 1.1 Proje Künyesi

| Alan | Değer (kaynak) |
|---|---|
| Proje | **CoreMusic** — `coremusic.net` |
| Tanım | "müzik yönetimi, ses işleme, cihaz entegrasyonu ve medya dağıtımı süreçlerini tek bir platform altında birleştiren **kurumsal dijital ses ve medya ekosistemidir**" (README §1.1) |
| Vizyon | *"Aynı Müzik Her Yerde Seninle"* — mutlak veri mülkiyeti (Offline-First), seamless handoff, bit-perfect Hi-Fi (kök CLAUDE.md) |
| Lisans badge'i | `License-Proprietary` (README §0 badge) |
| Vault kökü | `.ai/` (SSOT) — kök `CLAUDE.md` bir **pointer** dosyasıdır: "All AI instructions ... live in `.ai/`" |
| Vault envanteri | **24 klasör · 340 MD dosya** (kök CLAUDE.md, disk ile doğrulanmış 2026-09-24) |
| Alt ağ | 10 subdomain (README §4): `music` · `home` · `car` · `studio` · `download` · `media` · `admin` · `auth` · `pro` · `coremusic.net` |
| Authority | `Bayram Ali / Vault Steward` · Last Updated `2026-09-24` |

**README badge'leri (§0):** PHP 8.4 · JavaScript ES2022 Vanilla · C++ 20 NevaEngine ·
MySQL 9 (18 BCNF) · Architecture 21 Layers (1095 Components) · License Proprietary · Status Aktif Geliştirme

### 1.2 Teknoloji Yığını (README §8 + `.ai/CLAUDE.md` §12)

| Katman | Gerçek değer |
|---|---|
| Backend | **PHP 8.4+** (strict types, PDO, PSR-15 Middleware) + **Node.js / TypeScript** (download microservice) |
| Frontend | **Vanilla JavaScript (ES2022 SPA Router)**, **ITCSS 9-Layer + BEM** CSS, Glassmorphism UI |
| Ses motoru | **C++20**, **JUCE 9** AudioProcessor, Steinberg **ASIO SDK**, Windows **WASAPI Exclusive**, Linux **ALSA** |
| Veritabanı | **MySQL 9 — 18 BCNF** · README: **156 tablo** · `.ai/CLAUDE.md` §5 (K5): **112 tablo** ⚠️ çelişki (Ek A-3) |
| Önbellek | **Redis**, **APCu** (Rate limit: 60 req/60s via APCu — ADR-013) |
| Güvenlik | **Argon2id** parola özeti, **AES-256-GCM** Credential Vault (NIST SP 800-38D), **CSP nonce**, CSRF `csrf_token` |
| Donanım | **XMOS XU316**, **PCM3168A** 8-kanal ADC, **AK4458** 8-kanal DAC, **Class AB** MJL21194/93 (ADR-089) |
| AI / MCP | ⚠️ README'de AI modeli tanımı **YOK**; MCP sunucuları `.opencode/opencode.json` §mcp: **chrome-devtools**, **browsermcp**, **playwright**, **filesystem** |

### 1.3 Komutlar (gerçek)

```bash
# Kök CLAUDE.md §Quick Commands + README §9
php -S localhost:81 -t public/                    # PHP dev server (port 81)
cd download-service && npm install && npm run dev # Download service (port 3001)
cd shared && vendor/bin/phpunit                   # Testler
git clone https://github.com/coremusic/coremusic.net.git
```

**OpenCode komutları (`.opencode/opencode.json` §command — 16 adet):**

`vault-sync` · `vault-update` · `vault-check` · `security-audit` · `db-normalize` ·
`test-run` · `new-feature` · `bug-fix` · `deploy-check` · `adr-create` ·
`vault-post-update` · `prompt-maker` · `ask` · `context` · `debate` · `research`

**UTF-8 Türkçe komut arayüzü (vault-updater promptu):**

```bash
node .ai/scripts/vault-utf8-writer.mjs   # append | insert-before-marker | write | copy | verify | repair | scan
node .ai/scripts/vault-cmd.mjs           # ls/dir, type/oku, kg/ara, chk/dogrula + ekle/yaz/onar/tara  ⚠️ DOSYA YOK (§4.4)
```

### 1.4 Mimari: K0-K20 · A0-A5 · 10 Panel · 7 Servis

**21 katman (`.ai/CLAUDE.md` §5 — 1.095 bileşen · K20 BOM: 1.775 satır):**

| Katman | Kapsam | Bileşen | Katı kısıt |
|---|---|:---:|---|
| **K0** İşletim Sistemi | Win12, Lin10, Mac8, RPi5, ReactOS, Docker, Cross-Platform API | 50 | Alt seviye OS çekirdek servisleri |
| **K1** Donanım | XMOS XU316, PCM3168A, AK4458, 8×Class AB, 14 konnektör, Boost | 120 | DC-Only + Class AB; sinyal zincirine parazit yasak |
| **K2** Sürücü | ASIO, WASAPI, ALSA, PipeWire, CoreAudio, I2S, USB, BT, DLNA | 40 | Donanım-yazılım köprüsü |
| **K3** Ses İşleme Motoru | Neva Engine, DSP Chain (31-band EQ, 4 Reverb), Dolby Atmos, DTS:X | 50 | Sıfır gecikme, K2'ye sıkı bağımlı |
| **K4** Yapay Zeka | Music Analysis (10), Recommendation (5), Voice (5), Edge AI, ML Infra | 50 | K5 harici veriye erişemez |
| **K5** Veri Yönetimi | MySQL 9 (18 BCNF, 112 tablo), Redis, APCu, SQLite, Restic | 55 | 18 DB kesinlikle BCNF |
| **K6** Güvenlik | JWT, RBAC, CSRF, CSP, RateLimit, AES-256, Vault, Audit | 40 | Asla bypass edilemez |
| **K7** Middleware | OriginCheck, CORS, RateLimit, SecurityHeaders, Session, CSRF, PSR-15 | 35 | K6 doğrulamasını atlayamaz |
| **K8** Servis | Control, Media, Audio, Device, Network, AI, Download + Infra (11) | 55 | Servisler arası doğrudan çağrı yasak → Event Bus |
| **K9** API & Routing | Gateway, BFF×6, CQRS, Event Bus, SPA Router, OpenAPI | 30 | OpenAPI sözleşmesi ihlal edilemez |
| **K10** Uygulama | 10 Panel (aşağıda) | 45 | Yalnızca K9 API üzerinden iletişim |
| **K11** Kullanıcı Deneyimi | ITCSS 9-layer, BEM, Design Tokens, PWA, WCAG 2.2 AA | 40 | Yalnızca K10 tetikleyebilir |
| **K12** İzleme & Log | Prometheus, Grafana, Sentry, Matomo, Audit | 35 | Yalnızca K8 servislerinden okur |
| **K13** CI/CD & Deploy | GitHub Actions, Playwright, Vitest, Docker, K8s | 35 | Build/deploy otomasyonu |
| **K14** Ağ & İletişim | HTTP/3, WebRTC P2P, DLNA, UPnP, AirPlay, mDNS | 40 | Ağ iletişim standardı |
| **K15** Medya & Streaming | FFmpeg, FLAC, HLS, DASH, ID3, Radio | 35 | Yalnızca K14 üzerinden iletişim |
| **K16** Class AB Amplifikatör | MJL21194/93 Darlington, 50W/kanal, 8 kanal modüler, THD <0.005% | 120 | Tek kanal bağımsız, enable pinli |
| **K17** Güç Kaynağı ±35V | LM5122 ×2 (boost + inverting), 6S LiPo (22.2V), OR-ing, %96 verim | 85 | UVP/OVP/OCP/OTP |
| **K18** Termal Tasarım | Fischer SK53-100-SA, 80mm PWM fan, KSD301 thermal cutoff | 45 | 41W/kanal ısı yönetimi |
| **K19** PCB Tasarım | 6-layer stackup, impedance matched, thermal vias, star ground | 50 | ENIG finish, 2oz copper |
| **K20** BOM & Üretim | 1.775 BOM satır, Mouser/Digikey, ~$682 sistem maliyeti | 40 | Üretim araçları dahil |

**A0-A5 alt katman eşlemesi (kök CLAUDE.md §Architecture — kanonik):**

```text
A0 = K0-K5   A1 = K6-K7   A2 = K8-K9   A3 = K10-K11   A4 = K12-K15   A5 = K16-K20
```

> `.ai/AGENTS.md` §5 bu alanları **dosya tipi yetkisi** olarak kullanır (aşağıda §6.2).
> ⚠️ Not: `.ai/AGENTS.md` §5'teki A0-A5 "Kapsam" metni (Altyapı/Donanım, Güvenlik,
> Routing/Backend, Presentation, Veri/Entegrasyon, Bileşenler) kök CLAUDE.md ile uyumludur.

**10 panel (`.ai/CLAUDE.md` §9 — Faz 1 notu: kodda fiziksel olarak yalnız `auth` + `home` mevcut):**

| # | Panel | Subdomain | Port | Stack |
|---|---|---|---|---|
| 1 | Landing | `coremusic.net` | 80 | Vanilla JS |
| 2 | Music | `music.coremusic.net` | 81 | PHP 8.4 + JS |
| 3 | Admin | `admin.coremusic.net` | 80 | PHP 8.4 |
| 4 | Download | `download.coremusic.net` | 3001 | Node.js + TS |
| 5 | Media | `media.coremusic.net` | 5000/6000 | PHP + FFmpeg |
| 6 | Auth | `auth.coremusic.net` | — | PHP 8.4 |
| 7 | Home | `home.coremusic.net` | 81 | Vanilla JS |
| 8 | Car | `car.coremusic.net` | — | Vanilla JS |
| 9 | Studio | `studio.coremusic.net` | 81 | Vanilla JS |
| 10 | Pro | `pro.coremusic.net` | 81 | Vanilla JS |

**7 backend servis (`.ai/CLAUDE.md` §10):**

| # | Servis | Port | Protokol | Stack | Sorumluluk |
|---|---|---|---|---|---|
| 1 | Control Service | 81 | HTTP | PHP 8.4 | Auth, session, RBAC |
| 2 | Media Service | 5000/6000 | HTTP | PHP + FFmpeg | Library, metadata, streaming |
| 3 | Audio Service | 9741/9742 | REST/WS | C++20 JUCE | Player, DSP, mixer, EQ |
| 4 | Device Service | — | BLE/WiFi/USB | C++20 | Bluetooth, WiFi, USB |
| 5 | Network Audio | — | WebRTC/P2P | C++20 | Streaming, multi-room |
| 6 | AI Service | — | Internal | PHP + Python | Recommendations |
| 7 | Download Service | 3001 | HTTP/WS | Node.js + TS | Deezer/YouTube indirme |

**Port kayıt defteri (`.ai/CLAUDE.md` §11):** `80` admin · `81` music (Control) ·
`3001` download · `3306` MySQL · `5000/6000` media · `9741` Audio REST · `9742` Audio WS

---

## §2. Gerçek Klasör / Mimari Ağacı

> Aşağıdaki her satır `Test-Path` ile doğrulanmıştır (2026-09-25).

```text
C:\www\coremusic.net\
├── CLAUDE.md                 # Pointer dosya — "instructions live in .ai/" (SSOT: ADR-042)
├── README.md                 # Proje tanımı, subdomain ağı, stack, kurulum
├── WORKFLOW.md               # Pointer → .workflows/ (kök registry)
├── .ai/                      # ★ VAULT — tek doğruluk kaynağı (15 kök .md)
│   ├── CLAUDE.md  AGENTS.md  WORKFLOW.md  MEMORY.md  index.md
│   ├── brain.md  broken-links-report.md  engine.md  glossary.md  keys.md
│   ├── log.md  PROJECTS.md  ROLE.md  ULTRA-THINKING.md  VISION.md
│   ├── .rules/               # senior-mode.md
│   ├── .templates/           # 36 dosya (10 kategori alt klasörü + 3 kök) — §4.3
│   ├── .decisions/           # index.md + accepted/ draft/ rejected/
│   ├── .agents/              # 12 dosya (11 agent profili + AGENTS.md alt indeks)
│   ├── .sql/  .png/          # Şema / görseller
│   ├── architecture/         # k0..k20 (21 klasör) + firmware/ + adr/ + electronics/
│   ├── ui-design/  ecosystem/  reports/  servers/  prompts/  archives/
│   └── scripts/              # §4.4
├── .workflows/               # 9 dosya: CLAUDE.md, session-init, vault-sync, adr-creation,
│                             #   security-audit, deployment, hallucination-control,
│                             #   orchestrator-flow, architecture-write
├── .opencode/
│   ├── CLAUDE.md  opencode.json  skills/ (8 aktif SKILL.md + _archive-keep/)
│   └── .workflows/           # ⚠️ 3 kopya: session-init, vault-sync, security-audit
│                             #    (.workflows/ ile FARKLI içerik — Ek A-2)
├── .claude/  .claude/skills/ # ikinci skills yolu (opencode.json §skills.paths)
└── .docs/                    # ❌ YOK (var olduğu iddia edilmişti, Test-Path=false)
```

> **[ŞABLON NOTU]** Vault'ta `.ai/subdomains/` **YOK** (iddia edilmişti), kök `AGENTS.md`
> **YOK** — ama `.workflows/CLAUDE.md` §3 ona wiki-link veriyor: `[[../AGENTS.md]]` → **kırık link** (Ek A-5).

---

## §3. Vault Kuralları Özeti

### 3.1 Kök `CLAUDE.md` — Pointer (47 satır, tamamı okundu)

| Bölüm | İçerik |
|---|---|
| Başlık | "This file is a **pointer**. All AI instructions ... live in `.ai/`. **Do not use this file for instructions.**" |
| Quick Context | Proje, vizyon, 6 problem-çözüm, stack, 21 katman/A0-A5, vault envanteri (24 klasör/340 MD), **iki ADR serisi**, key rules |
| SSOT Links | 8 satır öncelik listesi: `.ai/CLAUDE.md` → `VISION` → `PROJECTS` → `AGENTS` → `WORKFLOW` → `brain` → `architecture/index` → `architecture/adr/` |
| Quick Commands | `php -S localhost:81 -t public/` · `cd download-service && npm run dev` · `cd shared && vendor/bin/phpunit` |
| Key Rules | No ORM (PDO only) · No JS frameworks (Vanilla JS only) · CSRF = `csrf_token` · Strict BCNF 3NF+ |
| Footer | `Vault Steward: Bayram Ali | SSOT: .ai/ | Last Updated: 2026-09-24` |

**İki ADR serisi (kök CLAUDE.md — birleştirme REDDEDİLDİ, ADR-026 §3.4):**

```text
.ai/.decisions/       → numaralı seri 001-089 (001-037 frozen; yeni için "next new = 088+")
.ai/architecture/adr/ → mimari seri 023-026 (023 hibrit-derinlik, 024 sürücü-firmware birleşimi,
                        025 K8↔K15 sınırı, 026 sayım birimi 5.000)
```

### 3.2 `.ai/CLAUDE.md` — AI Anayasası (34 bölüm, v27.3.2)

**Bölüm haritası (gerçek §-başlıkları):**

```text
Purpose: §1 Purpose & Project Definition · §2 Scope (+§2.1 SSOT Priority Order)
Architecture: §4 System Definition · §5 Architecture K0-K20 · §6 Middleware Pipeline (immutable)
  §6A API-First (ADR-084) · §6B Shared Library (ADR-085 v3.0) · §9 10 Panels · §10 7 Services
  §11 Port Register · §12 Technology Stack · §13 Platform Layers · §14 Deployment Modes
  §15 Theme Engine (ADR-044) · §18 18 BCNF DB (ADR-040) · §19 Audio Engine Standards
  §24 Dependencies · §34 Audio Organization (5 Sections)
Rules: §7 Hard Guardrails (16) · §7A Hybrid Coding Model · §8 Soft Constraints (4)
  §16 Boot Protocol · §18A Template System (Mandatory) · §21 Forbidden Patterns
  §22 Edge Cases · §23 Critical Warnings
Workflow: §25 Roadmap · §30 .ai Reference Tracking Protocol
Validation: §17 Test Coverage Targets · §29 Quality Report · §31 Phase 1 Record · §33 8-Section Skeleton
References: §3 Terminology · §20 Critical ADRs · §26 Related Documents
```

**§7 — 16 Hard Guardrail (numaralar 1-14, 16, 17 — 15 yok):**

| # | Kural | İhlal sonucu |
|---|---|---|
| 1 | **Zero Code Before Plan** | Kod revert edilir |
| 2 | **Vault First** — vault okunadan plan/kod/faaliyet başlamaz | Durdur + revert |
| 3 | **Zero Hallucination** — doğrulanamayan → `VERIFICATION REQUIRED` | İçerik silinir |
| 4 | **In-Place Refactoring** — dosya adı/yolu değişmez | Dosya geri yüklenir |
| 5 | **Single Source of Truth** — bilgi sadece `.ai/` | Harici bilgi reddedilir |
| 6 | **CSRF Token = `csrf_token`** (`_csrf_token` yasak) | Token reddedilir |
| 7 | **Middleware Order Immutable** | Sistem durdurulur |
| 8 | **Port 81 = music.coremusic.net** | Yanlış port yasak |
| 9 | **No ORM** — Raw PDO (ADR-002) | ORM reddedilir |
| 10 | **No Frameworks** — Vanilla JS + ITCSS (ADR-001) | Framework reddedilir |
| 11 | **Mockup Before Frontend** — `01-mockup-index.md` + `02-component-inventory.md` (19 PNG, C01-C16) + `tokens/design-tokens-master.md` + 45-tier cihaz matrisi + `reference/01-10` okunmadan kod YASAK | Revert + CRITICAL log |
| 12 | **Contradiction Gate** — vault'ta çelişki → kullanıcıya sor | İşlem durur |
| 13 | **Session Continuity** | Bağlam kaybolur |
| 14 | **Human Approval Gate** — mimari karar öncesi onay | Kod revert edilir |
| 16 | **Template Mandatory** — `.ai/.templates/index.md`'den şablon zorunlu (+ ui-design Kalıp A-D) | Dosya geçersiz |
| 17 | **Single Component Responsive** — 1024x600 mockup = pixel reference; ayrı HTML/branch YASAK | Kod revert edilir |

**§16 Boot Protocol (canonical okuma listesi)** ve **§17 Test Coverage Targets** → §7.1.

### 3.3 `.ai/.rules/senior-mode.md` — Zero-Hallucination modu (11 bölüm)

| Bölüm | İçerik (gerçek) |
|---|---|
| §1 Amaç | AI **senior mühendis** gibi çalışır: kod öncesi analiz → kod → doğrulama. Çelişkide `.ai/CLAUDE.md` kazanır. Öncelik: `CLAUDE.md > AGENTS.md > WORKFLOW.md > brain.md` |
| §2 Zero-Hallucination | Uydurmak yasak: dosya yolu, API/class/method, paket-sürüm, **ADR numaraları (defter: ADR-001..089)**, benchmark, güvenlik iddiaları. **7 etiket:** `CONFIRMED` · `INFERRED` · `UNKNOWN` · `NEEDS_VALIDATION` · `WEB DOĞRULANDI` · `REFERENCE VERIFIED` · `CIKARIM`. Doğrulanamazsa birebir **`DOĞRULAMA GEREKLI`** |
| §3 16-adım ön-analiz | 1 Repository (git log/dallar) · 2 Dosya yapısı · 3 Mimari (K0-K20 / L0-L6) · 4 Dependency graph · 5 Mevcut pattern'ler · 6 Build · 7 Test · 8 Configuration · 9 Logging · 10 Security yüzeyi · 11 Database (PDO, 18 BCNF) · 12 API yüzeyi · 13 Frontend (ITCSS 9-layer, BEM) · 14 Conventions (strict_types, PSR-12) · 15 Duplicate implementation · 16 Technical debt |
| §4 Vault-first okuma | `CLAUDE.md` → `.ai/CLAUDE.md` → `.ai/AGENTS.md` → `.ai/brain.md` → `.ai/WORKFLOW.md` → görev `.ai/**` → hedef dosya. Frontend'de mockup okunmadan kod **YASAK**; sıra dışı kod **revert** |
| §5 Asking Questions | `P0` DUR · `P1` planı bekle · `P2` işaretle devam. **En fazla 10 soru**, tekrar yok, cevap vault'a işlenir. Çelişki → **`CONFLICT DETECTED`** |
| §6 Review chain | `IMPLEMENT → BUILD → TEST → ARCHITECTURE CHECK → SECURITY CHECK → REVIEW → REFACTOR` (coverage ≥%80). Test edilmeden **"tamamlandı" denmez** |
| §7 Kırmızı çizgiler | 1 ORM→Raw PDO (ADR-002) · 2 `SELECT *` yasak · 3 Framework→Vanilla JS (ADR-001) · 4 `_csrf_token`→`csrf_token` (ADR-010) · 5 Hard→Soft delete (ADR-040) · 6 PCM5122→PCM3168A/AK4458 (ADR-038) · 7 katman yönü · 8 circular dependency · 9 middleware 10 adım (ADR-010/011/012/013) · 10 hardcoded secret → **ihlal: revert + log CRITICAL** |
| §8 Web research | Sürüme bağlı bilgi doğrulanmadan kullanılmaz; resmi dokümantasyon > repo/RFC > ikincil; kritik karar **≥2 bağımsız kaynak** |
| §9 Output Discipline | 9 satır: `CURRENT STATUS · COMPLETED · VERIFIED · UNKNOWN · BLOCKED · OPEN QUESTIONS · DECISIONS · NEXT STEP · REQUIRES APPROVAL` |
| §10 Onay kapıları | Onaysız YOK: ADR freeze, dosya adı/yolu, production migration, silme, yeni harici bağımlılık. Akış: `Change Request → Impact Analysis → onay → uygulama` |
| §11 Session kaydı | `session-save.mjs` → `vault-post-update.mjs` / `/vault-post-update`; tüm yazılar **sadece** `vault-utf8-writer.mjs` |

### 3.4 `.workflows/CLAUDE.md` — Workflow katmanı (51 satır, tamamı okundu)

| Bölüm | İçerik |
|---|---|
| §1 Bağlam | "Bu klasördeki dosyalar **çalıştırılabilir süreç tanımlarıdır**"; kök `WORKFLOW.md` pointer bu klasöre yönlendirir |
| §2 Mevcut Durum | 8 dosya tetikleyici/çıktı tablosu (session-init: her oturum başı · adr-creation: mimari karar · vault-sync: vault değişikliği · security-audit: güvenlik değişikliği · deployment: sürüm çıkışı · hallucination-control: belirsiz bilgi · orchestrator-flow: multi-agent görev · architecture-write: **Batch ≤31 dosya, 0 silme**) |
| §3 Komşu ilişkiler | Parent `[[../AGENTS.md]]` ⚠️ kırık · Canonical `[[../.ai/WORKFLOW.md]]` · Audit `[[../.ai/log.md]]` · ADR hedefi `.ai/.decisions/index.md` |
| §4 Değişiklik protokolü | Yeni workflow → `.ai/WORKFLOW.md` dosya tablosuna kayıt → **kullanıcı onayı**; güncelleme → ilgili dosya + log audit; silme onaya tabi |

### 3.5 `.workflows/security-audit.md` (10 bölüm başlığı)

```text
§1 Amaç · §2 Akış Diyagramı · §3 OWASP Top 10 Kontrol Listesi · §4 CoreMusic Özel Kontroller
  (4.1 Middleware Sırası (Değiştirilemez) · 4.2 CSRF Token · 4.3 Kimlik Doğrulama ·
   4.4 Yetkilendirme · 4.5 Giriş Güvenliği · 4.6 Veri Koruma)
§5 Red Team Protokolü · §6 Rapor Formatı (Denetci: security-engineer) · §7 Hata Yönetimi
§8 Yasaklar · §9 İlgili Dosyalar · §10 Aktivasyon
```

### 3.6 `.workflows/hallucination-control.md` (11 bölüm başlığı)

```text
§3 Halüsinasyon Türleri · §4 Kontrol Noktaları (4.1 Dosya Yolu · 4.2 API · 4.3 Versiyon ·
4.4 Test Sonucu) · §5 Truth Mode Kuralları · §6 Doğrulama Formatı (YANLIŞ/DOĞRU örnekleri)
§7 Red Team Halüsinasyon Kontrolü · §8 Hata · §9 Yasaklar · §10 İlgili · §11 Aktivasyon
```

---

## §4. Vault Yapısı — Dosya Listesi ve Amaçları

### 4.1 Kök `.ai/*.md` — 15 dosya (diskten doğrulandı)

| Dosya | Amaç (kaynak başlık) |
|---|---|
| `CLAUDE.md` | AI Constitution — 16 Hard Guardrail, §1-§34 |
| `AGENTS.md` | Agent Registry & Coordination Protocol (v22.0.3, §1-§26) |
| `WORKFLOW.md` | Vault Workflows & Engineering Processes (§5 12-faz, §6 20-faz, §7 ADR, §9 Hard Gates, §10 Rules, §8 Workflows, §17A 8 skill, §17B 11 agent) |
| `MEMORY.md` | Memory System Index (§5 Boot 14 kök dosya + 20 adım, §6 5 soru, §7 5 adım, §3 Hiyerarşi, §20 Current Session State) |
| `index.md` | Master Index (§2 Quick Reference, §3 SSOT Core 14 kök boot + 1 ek, §4 L0-L6, §6 10 Panel & 7 Servis, §11B Skills, §12 Vault Altyapısı, §20 Teknoloji Yığını) |
| `brain.md` | Mimari kararlar / ADR notları (§18C sorumluluk sınırları referansı) |
| `engine.md` | Orkestrasyon motoru (§6.4 Uncertainty Flag, §9.2 matris, §12.6/§12.7 faz kapanışı) |
| `glossary.md` | Terim sözlüğü (SSOT — 75 terim, CLAUDE.md §29) |
| `keys.md` | Keyword haritası |
| `log.md` | Audit trail — **append-only** |
| `PROJECTS.md` | Proje tanımı (10 yetenek, 6 kullanıcı, 6 sektör) |
| `ROLE.md` | Rol tanımı (§11 stack kanıtı) |
| `ULTRA-THINKING.md` | Ultra düşünme protokolü (§3.2 kanıt) |
| `VISION.md` | Vizyon (6 sorun-çözüm) |
| `broken-links-report.md` | Kırık link tarama raporu |

### 4.2 Alt dizinler (Test-Path ile doğrulandı)

| Dizin | İçerik | Amaç |
|---|---|---|
| `.rules/` | `senior-mode.md` | Kurallar katmanı |
| `.templates/` | 36 dosya | Vault şablonları (§4.3) |
| `.decisions/` | `index.md` + accepted/draft/rejected | Karar günlüğü (numaralı seri) |
| `.agents/` | 12 dosya | 11 agent profili + alt registry `AGENTS.md` |
| `.sql/` · `.png/` | Şema / görsel | Veri + mockup PNG'leri (PNG = source of truth) |
| `architecture/` | 21 katman klasörü + `firmware/` + `adr/` + `electronics/` | Mimari + mimari ADR serisi |
| `ui-design/` | mockup-index, component-inventory, tokens, screens, reference | Guardrail #11 zorunlu okuma |
| `ecosystem/` · `reports/` · `servers/` · `prompts/` · `archives/` | — | Ekosistem, rapor (security-*.md), sunucu, prompt, arşiv |

❌ **YOK:** `.ai/subdomains/` · `.docs/` · `.ai/scripts/katman-sayim.ps1` (kök CLAUDE.md satır 15'te geçiyor, diskte yok)

### 4.3 `.ai/.templates/` — 36 dosya (gerçek yapı: 10 kategori + 3 kök)

| Kategori | Dosyalar |
|---|---|
| (kök) | `index.md` (registry) · `CLAUDE.md` · `session-log-template.md` |
| `adr/` | `adr-template` · `adr-nygard-template` · `adr-audio-template` · `adr-database-template` · `adr-frontend-template` · `adr-security-template` · `adr-index` |
| `agents/` | `agents-template` · `agent-tartisma-turu-template` |
| `backend/` | `php-template` · `nodejs-template` |
| `documentation/` | `claude-md-template` · `docs-md-template` · `katman-readme-template` · `alt-katman-template` · `api-doc-template` · `security-audit-template` · `WikiPage-Template` |
| `frontend/` | `css-template` · `js-template` |
| `hardware/` | `hardware-template` |
| `infrastructure/` | `github-actions-template` · `migration-template` |
| `other/` | `cpp-template` · `c-template` · `aspnet-template` |
| `query/` | `Query-Template` |
| `testing/` | `phpunit-template` · `vitest-template` |
| `ui-design/` | `reference-template` (Kalıp A) · `flow-template` (B) · `prompt-template` (C) · `screen-spec-template` (D) |

> **[ŞABLON NOTU]** Guardrail #16: yeni dosya için bu dizinden şablon seçmek zorunlu;
> `ui-design/**` için ayrıca Kalıp A-D okunur.

### 4.4 `.ai/scripts/` (disk listesi: 4 girdi)

| Dosya | Durum | Amaç |
|---|---|---|
| `vault-utf8-writer.mjs` | ✅ VAR | UTF-8 güvenli yazma: `append` \| `insert-before-marker` \| `write` \| `copy` \| `verify` \| `repair` \| `scan` |
| `vault-faz4-sweep.mjs` | ✅ VAR | Faz-4 tarama |
| `fix-mojibake.py` | ✅ VAR | Mojibake onarımı |
| `index.md` | ✅ VAR | Script envanteri |
| `session-save.mjs` | ❌ **YOK** | CLAUDE.md post-op adım 1 + vault-updater promptu |
| `vault-post-update.mjs` | ❌ **YOK** | Post-op adım 2 + `/vault-post-update` komutu |
| `vault-cmd.mjs` | ❌ **YOK** | Türkçe komut arayüzü (vault-updater promptu) |
| `project-state.md` | ❌ **YOK** | Post-op adım 3 doğrulama hedefi |

---

## §5. Gerçek Adım Adım İş Akışları (`.workflows/` — 9 dosya)

### 5.1 `session-init.md` — Oturum başlatma (12 aşama)

```text
Aşama 1: Sistem Konteksi Yükleme      Aşama 7-8: Geçmiş Bilgileri
Aşama 2-4: Temel Dosyalar              Aşama 9-11: Vault İndeksleri
Aşama 5: Mimari Kararlar               Aşama 12: Hazırlık Doğrulama
Aşama 6: ADR Kontrolü
```

Tetikleyici: her oturum başı. Çıktı: vault boot (`.workflows/CLAUDE.md` §2: "10+ dosya okuma").

### 5.2 `vault-sync.md` — Vault senkronizasyonu (8 aşama)

```text
Aşama 1: Değişiklik Tespiti   Aşama 5: Çapraz Referans Kontrolü
Aşama 2: 5 Soru Analizi       Aşama 6: Log Yazma (YYYY-MM-DD HH:mm)
Aşama 3: Dosya Güncelleme     Aşama 7: Doğrulama
Aşama 4: İndeks Güncelleme    Aşama 8: İşlem Sonrası Vault Senkronu (zorunlu - 2026-09-24)
```

> `.ai/MEMORY.md` §6 ile uyumlu: **Başlangıç = 5 soru**, **Bitiş = 5 adım**.

### 5.3 `adr-creation.md` — ADR oluşturma (6 adım + numaralandırma)

```text
1. Karar Belirle (15 dk)   4. İnceleme (30 dk)
2. Araştırma Yap (30 dk)   5. Onay Al
3. ADR Taslağı Oluştur (1 sa)  6. Uygula ve Dokümante Et
```

**Numaralandırma (dosya §"ADR Numaralandırma" — 2026-09-24 seri notu):**

```text
frozen: ADR-001 → ADR-037 (immutable)      active: ADR-038 → ADR-089 (güncellenebilir)
draft:  yok                                yeni:   ≥ ADR-090   ( ".workflows/adr-creation.md" )
kök CLAUDE.md:                              "next new = 088+"
İki seri kasıtlı ayrıdır (birleştirme REDDEDİLDİ — ADR-026 §3.4):
  .ai/.decisions/ = karar serisi (001-089)  |  .ai/architecture/adr/ = mimari seri (023-026)
Şablon: .ai/.templates/adr/* · Hedef: .ai/.decisions/{status}/ + index.md
```

### 5.4 `orchestrator-flow.md` — Ana orkestrasyon (11 aşama)

```text
1 Talep Alma → 2 Talep Analizi → 3 Alan Sınıflandırması → 4 Risk Değerlendirmesi →
5 Agent Seçimi → 6 Bağlam Yükleme → 7 Görevi Gönder → 8 Çıktı Doğrulama →
9 Tamamlama veya Red → 10 Handover (gerekirse) → 11 Log ve Vault Güncelleme
```

### 5.5 `architecture-write.md` — Mimari yazma (9 adım, batch kuralı)

```text
§3 Batch Kuralı: 31 DOSYA / 0 SİLME (sabit sayılar: 0 / 14 / 8 / 4 beklenir)
Adım 1 Plan kilidi (10 dk)   Adım 5 Adlandırma denetimi     Adım 8 Sayım ve rapor
Adım 2 Şablon seçimi (5 dk)  Adım 6 Bağımlılık/katman ihlali Adım 9 Kapanış + kuyruk devri
Adım 3 Ön tarama (salt okunur, 10 dk)                         §5.1 Zaman bütçesi: ~60 dk / batch
Adım 4 Yazım (batch sırasıyla)   §7 BATCH RAPORU formatı · §9 Yasaklar · §10 salt-okunur doğrulama komutları
```

### 5.6 `deployment.md` — Dağıtım (8 aşama + rollback)

```text
Aşama 1 Ön Kontroller → 2 Güvenlik Doğrulaması → 3 Test Doğrulaması → 4 Onay →
5 Dağıtım → 6 Sağlık Kontrolü → 7 İzleme → 8 Log ("YYYY-MM-DD HH:mm - DAĞITIM")
§4 Geri Dönüş (Rollback) · §5 Hata Yönetimi · §6 Yasaklar · §8 Aktivasyon
```

> **YOK:** Dağıtım sağlayıcısı, ortam adları, somut rollback adımları (vault'ta başlık var, içerik doğrulanmadı).

### 5.7 `security-audit.md` ve `hallucination-control.md`

Bkz. §3.5 ve §3.6 (bölüm başlıkları gerçek).

### 5.8 `.ai/WORKFLOW.md` fazları (§-başlıkları doğrulandı)

| Bölüm | İçerik |
|---|---|
| §4 Core Principles | Temel ilkeler |
| §5 **12-Phase Vault Refactoring** | Vault refactor fazları |
| §6 **20-Phase Product Lifecycle** | Ürün yaşam döngüsü fazları |
| §7 ADR Lifecycle | Karar yaşam döngüsü |
| §8 Workflows | Akış tablosu (session-init, vault-sync, 8.7A kök dosyalar) |
| §9 **Hard Gates** | Sert kapılar |
| §10 Rules | Kurallar |
| §17A Skills | **8 aktif skill** (Guardrail #16 zorunlu) |
| §17B Agent Profiles | **11 agent** |

> **[ŞABLON NOTU]** §5/§6'daki fazların tek tek adları bu dosyada listelenmemiştir —
> kaynak `[[.ai/WORKFLOW.md]]` §5/§6'ya atıfla verilir (uydurma yok).

---

## §6. Agent / Rol Görevlendirme Kuralları

### 6.1 Agent kayıt defteri — `.ai/AGENTS.md` §4 (11 agent: 1 MO + 10 uzman)

| # | Agent | Kod | Domain | Katman | Teknoloji |
|---|---|---|---|---|---|
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

**SSOT kararı (§26.2):** `.ai/AGENTS.md` **v22.0.3 = tek SSOT**; `.ai/.agents/AGENTS.md`
**v1.2.x = alt registry** (yalnız profil indeksi). Çelişkide kök kazanır.

### 6.2 Alan sınırları — §5 (dosya tipi → sorumlu agent)

| Dosya tipi | Sorumlu | Diğerleri |
|---|---|---|
| `*.php` (Controller/Service/Repository) | Backend Architect | ✅ |
| `*.js` · `*.css` | UI Designer | ✅ |
| `*.sql` | Data Engineer | ✅ |
| `*.cpp` / `*.h` | Embedded Engineer | ✅ |
| `*.yml` / `*.yaml` (CI/CD) | DevOps Engineer | ✅ |
| `tests/**/*.php` · `tests/**/*.test.js` | QA Engineer | ✅ |
| Security middleware · `.env` | Security Engineer | ✅ |
| `log.md` | **Tüm ajanlar (append-only)** | ✅ sadece ekleme |
| `.ai/` vault | **MO (koordinasyon)** | ✅ okuma serbest |

**A0-A5 etiketleri (§5):** A0 `K0-K5` Altyapı/Donanım · A1 `K6-K7` Güvenlik ·
A2 `K8-K9` Routing/Backend · A3 `K10-K11` Presentation · A4 `K12-K15` Veri/Entegrasyon ·
A5 `K16-K20` Bileşenler. **Layer violation → derhal revert + log ERROR.**

### 6.3 Keyword → Agent Routing — §6 (9 keyword grubu)

| Keyword grubu | Birincil | İkincil |
|---|---|---|
| API, endpoint, routing, middleware, PHP, controller, repository | Backend Architect | Security Engineer |
| CSS, UI, responsive, accessibility, ITCSS, BEM, frontend, design, JS, mockup, token | UI Designer | QA Engineer |
| CSRF, CSP, XSS, OWASP, auth, encryption, session, rate limit | Security Engineer | Backend Architect |
| database, SQL, BCNF, migration, query, schema, MySQL, PDO, index | Data Engineer | Backend Architect |
| C++, ASIO, JUCE, audio, DSP, Neva Engine, ring buffer, WASAPI, hardware | Embedded Engineer | DevOps Engineer |
| test, coverage, PHPUnit, Vitest, Playwright, E2E, unit/integration | QA Engineer | — |
| CI/CD, GitHub Actions, deploy, infrastructure, pipeline, monitoring, GitLeaks | DevOps Engineer | QA Engineer |
| vault, documentation, ADR, wiki-link, index, keys, brain | MO (vault-updater) | — |
| template, şablon, .templates | **Tüm ajanlar (Guardrail #16)** | MO |

### 6.4 Görev dağıtım — §7 (7 adım)

```text
[1] Analiz (keyword/domain/öncelik/ajan) → [2] Pre-flight Checks (boundary, lock,
bağımlılık, mockup, 45-tier, responsive, şablon, retry) → [3] Task Assignment →
[4] Execution → [5] Handover → [6] Verification → [7] Completion (log.md + MEMORY.md)
```

**§8 Öncelik seviyeleri:**

| Öncelik | Tanım | Timeout | Max Retry | Yanıt |
|---|---|---|---|---|
| CRITICAL | Sistem durması, güvenlik açığı | 5s | 1 | Anlık |
| HIGH | Kritik işlev kaybı | 15s | 3 | 15s |
| MEDIUM | Normal geliştirme | 30s | 3 | 30s |
| LOW | İyileştirme | 60s | 2 | 60s |

**§9 Handover:** `[Kaynak] → Request → [Hedef] → Onay/Red → Confirmation`; hedef onayı
**zorunlu**, timeout **30s**, max retry **3**, redde MO müdahale eder; mesaj alanı 8 kalem
(Konu, Kaynak, Hedef, Öncelik, Etkilenen Dosyalar, İstek, Onay Durumu, Timestamp UTC).
**8 senaryo** (§9.3) — ör. güvenlik açığı: Backend→Security (CRITICAL).

**§10 Eskalasyon:** `L1 Domain Lead → L2 Tech Lead → L3 Arch Lead → İnsan`
(L1 30s · L2 60s · L3 120s · her seviyede max 3 retry · insan = son çare) — 9 senaryo.

**§11 Sağlık durumları:** `200 Healthy` · `301 Degraded (>15s)` · `408 Retry` ·
`500 Failed (3 retry → queue reset)` · `503 Dead (escalation)` — heartbeat 10s.

**§12 Context Lock:** max 30s kilitleme; deadlock'da **MO en eski kilidi kırar**;
CRITICAL diğerini kırabilir; lock acquire/release `log.md`'ye yazılır.

### 6.5 `.opencode/opencode.json` — çalışan config

| Alan | Gerçek değer |
|---|---|
| `default_agent` | `master-orchestrator` |
| **Agent sayısı** | **13** (`master-orchestrator`, `plan`, `backend-architect`, `ui-designer`, `security-engineer`, `data-engineer`, `embedded-engineer`, `qa-engineer`, `devops-engineer`, `audio-hardware-engineer`, `dsp-firmware-engineer`, `windows-software-engineer`, `vault-updater`) — `.ai/AGENTS.md` = 11 ⚠️ Ek A-1 |
| **Komut sayısı** | **16** (§1.3) |
| `instructions` | 11 satır: `CLAUDE.md`, `.ai/CLAUDE.md`, `.ai/AGENTS.md`, `.ai/brain.md`, `.ai/WORKFLOW.md`, `.ai/MEMORY.md`, `.ai/index.md`, `.ai/ULTRA-THINKING.md`, `.ai/.rules/senior-mode.md`, `README.md`, `WORKFLOW.md` |
| `skills.paths` | `.opencode/skills` · `.claude/skills` · `~/.config/opencode/skills` |
| `skills.force` | ~85 skill girişi |
| Diskte aktif | `.opencode/skills/` → **8 SKILL.md** (`composer-sync`, `db-engine`, `orchestration`, `truth-engine`, `ui-workbench`, `vault-sync-post`, `agent-debate`, `context-report`) + `_archive-keep/` (20 dosya) |
| `mcp` | `chrome-devtools` · `browsermcp` · `playwright` · `filesystem` (4 sunucu) |
| `references` | `vault → .ai` · `rules → .opencode` · `workflows → .workflows` |
| `watcher.ignore` | `vendor/**`, `node_modules/**`, `.git/**`, `*.log`, `*.cache`, `.env*`, `*.bak`, `.ai/sessions/**`, `.ai/.png/**` |

**Master Orchestrator dispatch tablosu (config promptundan — anlık yönlendirme):**

```text
security → security-engineer      PHP/API/routing → backend-architect
database/SQL/BCNF/PDO → data-engineer          C++/ASIO/JUCE/DSP → embedded-engineer
CSS/UI/ITCSS/BEM/frontend → ui-designer        test/coverage → qa-engineer
CI/CD/Docker/deploy → devops-engineer          DAC/PCB/hardware → audio-hardware-engineer
XMOS/I2S/TDM/firmware → dsp-firmware-engineer  WASAPI/COM/Windows → windows-software-engineer
vault/documentation/ADR → vault-updater        UI görevleri → ui-designer (anlık)
```

---

## §7. Kalite ve Güvenlik Standartları

### 7.1 Kalite

| Standart | Gerçek değer | Kaynak |
|---|---|---|
| **Test kapsamı hedefi** | **min ≥%80 · hedef ≥%90** (modül bazlı) | `.ai/CLAUDE.md` §17 |
| Modüller | Backend (PHP) `PHPUnit 11` · Frontend (JS) `Vitest` · Audio Engine (C++) `Google Test` · Download Service `Vitest` | §17 |
| Test komutu | `cd shared && vendor/bin/phpunit` | kök CLAUDE.md / README §9 |
| QA agent eşiği | ≥%80 min, ≥%90 modül hedefi; pyramid Unit 70 / Integration 20 / E2E 10 | opencode.json qa-engineer |
| Frontend standardı | ITCSS 9-layer + BEM + WCAG 2.2 AA + Design Tokens | §5/§11 |
| Kod standardı | `declare(strict_types=1)` her PHP dosyasında, PSR-12 | senior-mode §7, qa prompt |
| Encoding | **UTF-8 zorunlu** (vault yazımları sadece `vault-utf8-writer.mjs`) | vault-updater promptu |
| Quality Report | `.ai/CLAUDE.md` §29: version **27.3.2**, 34 bölüm, 16 guardrail, 4 soft constraint, 10 panel, 7 servis, 18 BCNF, 5 platform tier, 5 deployment mode, ADR 001-089, 11 cross-ref, 75 glossary term, 11 forbidden pattern | §29 |
| Review zinciri | `IMPLEMENT → BUILD → TEST → ARCH → SECURITY → REVIEW → REFACTOR` | senior-mode §6 |

> ⚠️ **Düzeltme notu:** "test kapsamı %100 (25/25 class, 451/451 method)" ifadesi
> `.ai/CLAUDE.md` §17'de **YOKTUR** (§17 ≥80/≥90 hedefleridir). `25/25` ifadesi yalnız
> `.ai/.templates/CLAUDE.md`'de şablon üretim sayacı olarak geçer.

### 7.2 Güvenlik

| Kontrol | Gerçek değer | Kaynak |
|---|---|---|
| **Middleware sırası (immutable)** | `OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf → BypassAuth → Auth → Permission → Validation` | `.ai/CLAUDE.md` §6 (ADR-010/011/012/013/022) |
| CSRF token | `csrf_token` (`_csrf_token` yasak) | Guardrail #6, ADR-010 |
| Şifreleme | Argon2id (parola) · AES-256-GCM (credential vault, 96-bit IV, 16-byte tag) | ADR-022 |
| CSP | nonce per-request (256-bit random) | ADR-012 |
| Rate limit | 60 req/60s via APCu | ADR-013 |
| Hybrid Auth | Session + JWT (ADR-052) · Merkezi auth: `auth.coremusic.net` ONLY (ADR-058) · MFA: TOTP `pragmarx/google2fa` (ADR-059) | security-engineer promptu |
| CORS | whitelist ONLY — wildcard yok | opencode.json |
| BypassAuth | production'da **her zaman devre dışı** | opencode.json |
| Denetim kapsamı | OWASP Top 10 + CoreMusic özel kontroller (§4.1-4.6) + Red Team protokolü | `security-audit.md` |
| Secrets | **REDACTED** — hiçbir sır vault'a yazılmaz | Guardrail/vault-updater #11 |
| Sızma testi | ⚠️ salt-okunur tarama; `security-audit.md` §8 "Yasaklar" | workflow |

### 7.3 UTF-8 Yazma Protokolü (vault-updater promptu — zorunlu)

```text
1. TUM vault YAZIMLARI SADECE: node .ai/scripts/vault-utf8-writer.mjs
   (append | insert-before-marker | write | copy | verify | repair | scan)
2. YASAK: Set-Content, Out-File, Add-Content, echo >, New-Item -Value
   (Windows-1254/BOM/UTF-16 bozulması) — IZINLI: salt-okunur komutlar
3. Türkçe komut arayüzü: node .ai/scripts/vault-cmd.mjs   ⚠️ DOSYA YOK (§4.4)
4. log.md icin SADECE append modu (bayt-seviyesi)
5. Yazim sonrasi otomatik dogrulama; bozuk dosya icin repair modu (yedek alır)
```

---

## §8. Versiyonlama / Commit / ADR Kuralları

| Konu | Kural (gerçek) | Kaynak |
|---|---|---|
| Frozen ADR | **ADR-001 → ADR-037 IMMUTABLE** | `.decisions/index.md`, root CLAUDE.md |
| Active ADR | **ADR-038 → ADR-089** (31 adet, güncellenebilir) | `.decisions/index.md` |
| Sayımlar | **37 frozen · 31 active · 12 rejected · 0 draft** (toplam 80 dolu; 89 numaradan 9 boşluk — `DOĞRULAMA GEREKLI`) | `.decisions/index.md`, CLAUDE.md §29 |
| Yeni ADR numarası | ⚠️ **Üç farklı iddia:** `≥090` (`.workflows/adr-creation.md`) · `088+` (kök CLAUDE.md + vault-updater) · `ADR-001..089` defteri (senior-mode §2) → Ek A-4 |
| İki seri | `.ai/.decisions/` (001-089) ≠ `.ai/architecture/adr/` (023-026) — **birleştirme REDDEDİLDİ** (ADR-026 §3.4) | root CLAUDE.md satır 16 |
| ADR durumları | `proposed → accepted → deprecated → superseded` (WORKFLOW §7) / `.decisions/` alt: `accepted`, `draft`, `rejected` | WORKFLOW.md, disk |
| ADR şablonu | `.ai/.templates/adr/adr-template.md` + `adr-nygard-template.md` + 5 domain şablonu | §4.3 |
| ADR formatı | `# Karar: [Başlık]` + `Özet · Bağlam · Karar · Gerekçe · Alternatifler · Sonuçlar · İlgili Kararlar` | adr-creation.md §ADR Formatı |
| Frontmatter | **7 zorunlu alan:** `title, type, category, version, status, authority, updated` | CLAUDE.md §18A |
| Sürüm | SemVer (`version: "27.3.2"` vb.) · `authority` örn. `SSOT`, `super-primary` | vault dosyaları |
| Wiki-link | `[[relative/path/to/file]]` | vault-updater #10 |
| Log | `.ai/log.md` **append-only** (bayt-seviyesi) | Guardrail/domain §5 |
| Yeni workflow | `.ai/WORKFLOW.md` dosya tablosuna kayıt → **kullanıcı onayı** | `.workflows/CLAUDE.md` §4 |
| Onay gerektiren | ADR freeze · dosya adı/yolu · production migration · silme · yeni harici bağımlılık | senior-mode §10 |
| **Commit mesajı formatı** | **YOK** — vault'ta tanımlı değil | — |
| **Branch/tag/release** | **YOK** — vault'ta tanımlı değil | — |

---

## §9. Yeni Proje İçin İlk 30 Dakika (Adım Adım)

> Bütün `{...}` alanlarını kendi projenizle değiştirin. Süreler dakika cinsindendir.

### 0-5 dk — Dizini kur (1. adım)

```text
{PROJE_ADI}/ altında:
.ai/  .ai/.rules/  .ai/.templates/{adr,agents,backend,documentation,frontend,
     hardware,infrastructure,other,query,testing,ui-design}/
.ai/.decisions/{accepted,draft,rejected}/  .ai/.agents/  .ai/architecture/
.ai/reports/  .ai/scripts/  .workflows/  .opencode/{skills/}  .claude/skills/
```

### 5-10 dk — Boot + pointer dosyaları (2. adım)

```text
3 pointer/anayasa dosyası:  CLAUDE.md (pointer) · README.md · WORKFLOW.md (pointer)
14 kök .ai boot dosyası  :  CLAUDE.md · AGENTS.md · WORKFLOW.md · MEMORY.md · index.md
                            brain.md · engine.md · glossary.md · keys.md · log.md
                            PROJECTS.md · ROLE.md · ULTRA-THINKING.md · VISION.md
→ İçlerindeki {PROJE_ADI} / CoreMusic / coremusic.net değişkenlerini değiştir.
```

### 10-15 dk — Kurallar + workflow (3. adım)

```text
4. .ai/.rules/senior-mode.md   → kendi 11 bölümünü yaz (§1-§11 iskeletini koru)
5. .workflows/CLAUDE.md §2     → kendi tetikleyici/çıktı tablon (8 dosya)
6. .workflows/ 8 akış dosyası  → session-init, vault-sync, adr-creation,
                                  security-audit, deployment, hallucination-control,
                                  orchestrator-flow, architecture-write
```

### 15-20 dk — Agent + config (4. adım)

```text
7.  .ai/AGENTS.md §4-§12       → kendi agent tablon (ad, kod, domain, katman, teknoloji
                                  + §5 dosya tipi yetkileri + §6 routing + §7 dispatch)
8.  .opencode/opencode.json     → agent sayısını .ai/AGENTS.md ile EŞİTLE
                                  (CoreMusic'te 11 vs 13 çelişkisi var — Ek A-1)
    instructions[] listesini kendi boot dosyalarınla doldur
```

### 20-25 dk — Şablon + script (5. adım)

```text
9.  .ai/.templates/index.md + kategori şablonları (Guardrail #16 zorunlu)
10. .ai/scripts/ → vault-utf8-writer.mjs ZORUNLU (diğerleri isteğe bağlı)
11. İlk log kaydı (append):
    node .ai/scripts/vault-utf8-writer.mjs append --file .ai/log.md --text "..."
```

### 25-30 dk — Doğrulama (6. adım)

```bash
node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/CLAUDE.md
node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/AGENTS.md
```

### 30. dakika kontrol listesi

- [ ] 3 pointer + 14 kök boot dosyası yerinde, `{...}` değişkenleri değiştirilmiş
- [ ] `.ai/.rules/senior-mode.md` var (11 bölüm)
- [ ] `.workflows/` 9 dosya var (CLAUDE.md + 8 akış)
- [ ] `.opencode/opencode.json` var; **agent sayısı `.ai/AGENTS.md` ile eşit**
- [ ] `.ai/.templates/` şablonları var (Guardrail #16)
- [ ] `vault-utf8-writer.mjs` var ve `verify` çalışıyor
- [ ] `.ai/log.md` ilk append kaydı aldı
- [ ] Secrets hiçbir dosyaya yazılmadı (REDACTED)
- [ ] Eksik-script listesi biliniyor (session-save / vault-post-update / vault-cmd / project-state)

---

## §10. Değiştirilecekler vs. Olduğu Gibi Kalacaklar

### 10.1 `{DEĞİŞTİR}` — yeni projede değiştirmeniz gerekenler (16 madde)

| # | Alan | CoreMusic değeri → sizinkini yazın |
|---|---|---|
| 1 | Proje adı + tanım | `CoreMusic` / "kurumsal dijital ses ve medya ekosistemi" |
| 2 | Alt ağ / subdomain | 10 panel (music, home, car, studio, download, media, admin, auth, pro, coremusic.net) |
| 3 | Teknoloji yığını | PHP 8.4 + Node/TS + Vanilla JS ITCSS/BEM + C++20 JUCE + MySQL 9 |
| 4 | Katmanlar | K0-K20 (21 katman, 1.095 bileşen) + A0-A5 → kendi mimarin |
| 5 | Panel/servis/port | 10 panel, 7 servis, port 80/81/3001/3306/5000/6000/9741/9742 |
| 6 | Agent listesi | 11 (`.ai/AGENTS.md`) / 13 (config) → kendi rollerin |
| 7 | Dispatch routing | §6.3 9 keyword grubu → kendi keyword'lerin |
| 8 | Komutlar | 16 OpenCode komutu (`vault-sync`, `adr-create`, ...) |
| 9 | MCP sunucuları | chrome-devtools, browsermcp, playwright, filesystem |
| 10 | Skills | 8 aktif SKILL.md + `skills.force` listesi |
| 11 | Test metrikleri | ≥%80 min / ≥%90 hedef + PHPUnit/Vitest/Google Test |
| 12 | Vault envanteri | 24 klasör / 340 MD / 36 şablon / 15 kök boot |
| 13 | ADR serisi | 001-089 numaralı + 023-026 mimari seri → kendi numaraların |
| 14 | Dosya yolları | `C:\www\coremusic.net` · `github.com/coremusic/coremusic.net.git` |
| 15 | Donanım referansları | K16-K20, XMOS, PCM3168A, Class AB (donanım projen yoksa sil) |
| 16 | Authority/versiyon | `Bayram Ali / Vault Steward`, `version: 27.3.2` gibi imzalar |

### 10.2 `OLDUĞU GİBİ KALACAK` — kopyala, değiştirme (14 madde)

| # | Kural | Neden değişmez |
|---|---|---|
| 1 | **UTF-8 yazım protokolü** — sadece `vault-utf8-writer.mjs` | Windows-1254/BOM/UTF-16 bozulmasını önler |
| 2 | **`log.md` append-only** (bayt-seviyesi) | Tarih/audit bütünlüğü |
| 3 | **SSOT = `.ai/`**; kök `CLAUDE.md` bir **pointer** | ADR-042 tek doğruluk |
| 4 | **Frontmatter 7 alan** (`title, type, category, version, status, authority, updated`) | §18A |
| 5 | **Wiki-link** `[[relative/path/to/file]]` | Cross-ref bütünlüğü |
| 6 | **Frozen ADR immutable** + iki serinin ayrılığı (ADR-026 §3.4) | Karar geçmişi |
| 7 | **Zero Hallucination** → `VERIFICATION REQUIRED` / `DOĞRULAMA GEREKLI` | Sıfır uydurma |
| 8 | **REDACTED** — sır vault'a yazılmaz | Güvenlik |
| 9 | **16 Hard Guardrail** (1-14, 16, 17) | Kod kalitesi |
| 10 | **16-adım ön-analiz + review chain** (senior-mode §3, §6) | Kod öncesi kalite |
| 11 | **Escalation** L1→L2→L3→İnsan + 3 retry + Contradiction Gate | Döngü kırma |
| 12 | **Middleware sırası immutable** (10 adım) | Güvenlik bütünlüğü |
| 13 | **Template Mandatory** (Guardrail #16) + ui-design Kalıp A-D | Tutarlı şablon |
| 14 | **8 hard gate / post-op vault sync** | Her işlem sonrası doğrulama |

### 10.3 Vault'ta OLMAYAN — `YOK` (üretmeniz gerekir)

```text
YOK → Lisans metni (yalnız badge: Proprietary)
YOK → Commit mesajı formatı / Conventional Commits
YOK → Branch isimlendirme, tag/release süreci
YOK → CI/CD pipeline somut tanımı (K13 hedef; .github/workflows/ = 0 dosya — §25.2)
YOK → session-save.mjs · vault-post-update.mjs · vault-cmd.mjs · project-state.md
YOK → .ai/subdomains/ · .docs/ · kök AGENTS.md (link veriliyor ama dosya yok)
YOK → Dağıtım sağlayıcısı / ortam adları / rollback adımları
YOK → AI modeli / MCP açıklama metni (README'de yok)
```

---

## Ek A — Tespit Edilen Çelişkiler (kayıt altına alındı, çözülmedi)

| # | Çelişki | Kanıt |
|---|---|---|
| **A-1** | **Agent sayısı 11 vs 13** — `.ai/AGENTS.md` §4/§23 "11 (1 MO + 10 specialist)" · `.opencode/opencode.json` 13 anahtar (`plan` + `vault-updater` fazladan; `plan` config'te var, AGENTS.md'de yok) | her iki dosya |
| **A-2** | **`.workflows/` vs `.opencode/.workflows/`** — `session-init.md` (8.109 B vs 2.188 B), `vault-sync.md` (7.394 B vs 1.242 B), `security-audit.md` (7.747 B vs 1.541 B) → 3 kopya **farklı boyutta/farklı sürümde** | disk |
| **A-3** | **Tablo sayısı 156 vs 112** — README §8 ve kök CLAUDE.md "156 Tables" · `.ai/CLAUDE.md` §5 K5 "112 tablo" (her ikisi de 18 BCNF) | README satır 184 / CLAUDE.md satır 109 |
| **A-4** | **Yeni ADR numarası üçlü çelişki** — `≥090` (`.workflows/adr-creation.md` satır 47/218) · `088+` (kök CLAUDE.md satır 16 + vault-updater promptu) · defter `ADR-001..089` (senior-mode §2) | 3 kaynak |
| **A-5** | **Kırık wiki-link** — `.workflows/CLAUDE.md` §3 `[[../AGENTS.md]]` → kök `AGENTS.md` **dosya olarak yok** | Test-Path=false |
| **A-6** | **Eksik script'ler** — `session-save.mjs`, `vault-post-update.mjs`, `vault-cmd.mjs`, `project-state.md` prompt/kural dosyalarında zorunlu, `.ai/scripts/`'te yok | disk |
| **A-7** | **Boot listesi boyut farkı** — `.ai/AGENTS.md` §25.4: §24.2 = 14 dosya, MEMORY §5 = 20 adım, FULL boot = 17 öğe (bilinen durum, vault kendisi kaydetmiş) | AGENTS.md §25.4 |
| **A-8** | **Banka sayımı** — `.ai/.decisions/index.md`: 37+31+12 = 80 dolu / 89 numara / **9 boşluk** `DOĞRULAMA GEREKLI` (CLAUDE.md §29 ile uyumlu) | index.md §sayım |
| **A-9** | **Test kapsamı iddiası** — "25/25 class, 451/451 method, %100" `.ai/CLAUDE.md` §17'de **YOK**; §17 ≥80/≥90 hedefidir. `25/25` yalnız `.ai/.templates/CLAUDE.md` üretim sayacında | §17 |

## Ek B — Cross-Reference Doğrulama Şablonu (vault-sync Aşama 5-7)

```text
1. Bu dosyadaki tüm [[wiki-link]] hedefleri var mı?     → yoksa: VERIFICATION REQUIRED
2. Her § başlığı §N formatında mı?                       → numaralar kesintisiz
3. Her tablo satırı kaynak dosyayla eşleşiyor mu?        → eşleşmiyorsa: YOK işaretle
4. Frontmatter 7 alan dolu mu?                          → title/type/category/version/status/authority/updated
5. UTF-8 verify: node .ai/scripts/vault-utf8-writer.mjs verify --file <dosya>
6. Hallucination sweep: doğrulanamayan satır → "VERIFICATION REQUIRED" / "DOĞRULAMA GEREKLI"
7. log.md'ye append-only kayıt at (Aşama 6)
```

---

*Bu şablon salt CoreMusic vault içeriğinden üretilmiştir (v1.1.0 — kaynak-doğrulamalı revizyon).
Uydurma bilgi yoktur; vault'ta olmayan her bilgi `YOK` ile, çözülmemiş çelişki `Ek A` ile işaretlidir.*
