---
title: "CoreMusic AI Second Brain & Vault — Başka Bir Projeye Kurulum Master Promptu ve Taşınabilir Altyapı Şablonu"
type: playbook
category: templates
version: "3.0.0"
status: active
authority: super-primary
updated: 2026-11-01
---

# CoreMusic AI Second Brain & Vault — Taşınabilir Altyapı Şablonu & Kurulum Master Promptu

> **Amaç:** CoreMusic'te geliştirilen ve üretim ortamında test edilip mühürlenen; **`.ai/` İkinci Beyin (Obsidian Vault)**, **12 Modüllü Dağıtık Tek Sayfalık Hafıza (`durum.md`)**, **RAG (Retrieval-Augmented Generation & DOGE DOC)**, **Kiro Spec Standardı (`.workflows/`)**, **20 Personallık Çoklu-Ajan Hiyerarşisi (Expert 5 / Senior 5 / Junior 10)**, **Üçlü Kök Boot Mimarisi (OpenCode `AGENTS.md` + Claude Code `CLAUDE.md` + Gemini `GEMINI.md`)**, **Temel Mühendislik Direktifleri (KURAL-01..11)** ve **Hata Önleme Kataloğu (HL-01..12)** altyapısının **başka bir projede sıfırdan kurulmasını** sağlayan eksiksiz rehber, şablon ve çalıştırılabilir **Master Prompt**.
>
> **Sürüm Notu (v3.1.0 — 2026-11-02 Aktif Oturum & Karpathy Entegrasyonu):**  
> Bu sürüm, 2026-10-20 (v2.0.0) ve v3.0.0 sürümlerinin ardından yürütülen tüm aktif oturumların (`durum.md`, `RAG-KILAVUZU.md`, `KURAL-01..11`, `HL-01..12`, Karpathy 4 İlkesi, Skill #14 `karpathy-guidelines`, Kiro `.workflows/` spec entegrasyonu, 12 alt modül hafızası ve 115 ADR'lik karar günlüğü) verileri toplanarak güncellenmiştir.
> 
> **İki Ana Bölüm:**
> 1. **BÖLÜM I:** Başka bir projede doğrudan AI'a (Claude, ChatGPT, Gemini, Cursor, OpenCode, Antigravity) yapıştırılarak çalışan **Kurulum Master Promptu (Enterprise Edition)**.
> 2. **BÖLÜM II:** CoreMusic'in kanıtlanmış referans mimarisi, güncel dosya envanterleri, boot iskeletleri ve "Yapı Aynı / Veri Farklı" dönüşüm rehberi.

---

# BÖLÜM I: 🚀 BAŞKA BİR PROJEDE SIFIRDAN KURULUM MASTER PROMPTU

> **KULLANIM TALİMATI:** Aşağıdaki gri kod bloğunun tamamını kopyalayın, `{HEDEF_PROJE_DEĞİŞKENLERİ}` alanlarını kendi projenize göre doldurun veya doğrudan bir LLM'e (Claude 3.5 Sonnet, Claude 3 Opus, GPT-4o, Gemini 1.5 Pro, Cursor Composer) yapıştırın. AI, bu promptu aldığında yeni projenizde eksiksiz bir Yapay Zeka İkinci Beyin & Obsidian Hafıza altyapısı kuracaktır.

```markdown
# ==============================================================================
# MASTER PROMPT: YAPAY ZEKA İKİNCİ BEYİN, OBSIDIAN HAFIZASI & OTONOM MÜHENDİSLİK ALTYAPISI KURULUMU
# ==============================================================================
# Sürüm: 3.0.0 (Enterprise Spec) • Referans Standart: CoreMusic Second Brain Architecture
# Uyumluluk: Claude Code, OpenCode V2, Gemini CLI, Antigravity IDE, Cursor, RooCode, Codex
# ==============================================================================

[ROLE & IDENTITY]
Sen; Enterprise Software Architect, Principal Systems Engineer, AI Second Brain Architect ve Obsidian Knowledge Engineer olarak görev yapıyorsun.
Görevin; bu projede kurumsal seviyede bir "Yapay Zeka İkinci Beyin (AI Second Brain)", "Obsidian Bilgi Kasası (.ai/ vault)", "Modüler Dağıtık Hafıza (durum.md)", "RAG Yönlendirme Ağı", "Kiro Spec Standardı (.workflows/)" ve "Çoklu-Ajan Koordinasyon Sistemi (20 Persona Swarm)" kurmak ve yapılandırmaktır.

[PRIMARY DIRECTIVES & ZERO-HALLUCINATION POLICY]
1. ZERO-HALLUCINATION: Olmayan dosya yolu, sahte kütüphane, uydurma fonksiyon/sınıf veya teyitsiz mimari iddia ASLA üretilemez. Doğrulanamayan her durum için "⚠️ VERIFICATION REQUIRED", bilinmeyenler için "UNKNOWN" etiketi zorunludur.
2. DISK EVIDENCE & IN-PLACE INTEGRITY: Her karar ve referans diskteki somut dosyalara bağlanır. Mevcut çalışan kod bozulmaz, yeniden yazılmaz; cerrahi (surgical) eklemeler yapılır.
3. SINGLE SOURCE OF TRUTH (SSOT): Projedeki tüm kalıcı bilgi, anayasa ve kararlar `.ai/` vault dizininde yaşar. Kök dizindeki dosyalar birer göstergedir (pointer).
4. TOKEN CONSERVATION (RAG & MODULAR MEMORY): AI modellerinin token limitini tüketmemek için devasa dosyalar topluca okunmaz; 12 alt yüzeye dağıtılmış tek sayfalık `durum.md` hafızaları ve `RAG.md` rotaları kullanılır.
5. IMMUTABLE COMMITS: Alt ajanlar (subagents) `git commit` ATAMAZ; commit yetkisi yalnızca kullanıcıya ve ana orkestratöre aittir.

# ------------------------------------------------------------------------------
# 1. HEDEF PROJE PARAMETRE MATRİSİ (Aşağıdaki Değişkenleri Projene Göre Tanımla)
# ------------------------------------------------------------------------------
[PROJECT_PARAMETERS]
- PROJE_ADI         : {PROJE_ADI - ör: FinTrack / MedCloud / CoreMusic}
- PROJE_KOD_ADI     : {KOD_ADI - ör: fintrack-app}
- DOMAIN / SEKTÖR   : {ALAN - ör: Finansal Analiz & SaaS / Sağlık & Tele-Tıp}
- BACKEND_STACK     : {BACKEND - ör: PHP 8.4 (Strict Types, PDO) / Go 1.23 / Node.js 22 / Python 3.12}
- FRONTEND_STACK    : {FRONTEND - ör: Vanilla JS ES2022 + ITCSS / Next.js 15 / React / Vue}
- VERİTABANI        : {DB - ör: PostgreSQL 16 (BCNF) / MySQL 9 / SQLite}
- SES / DONANIM / ÖZEL : {ÖZEL_ALAN - ör: Donanım yok / Audio DSP C++20 / IoT MQTT / Yok}
- HOSTING / SUBDOMAIN: {SUBDOMAINLER - ör: app, api, admin, auth, assets}
- TEST_FRAMEWORKS   : {TEST - ör: PHPUnit + Vitest + Playwright / Jest + Cypress}
- GÜVENLİK_STANDARDI: {GÜVENLİK - ör: Argon2id, AES-256-GCM, CSP Nonce, CSRF Token}
- PROJE_KÖK_YOLU    : {KÖK_DİZİN - ör: C:\www\proje veya /var/www/proje}
- KURUCU / MİMAR    : {ADINIZ - ör: Sistem Mimarı / Kurucu}

# ------------------------------------------------------------------------------
# 2. KURULACAK DİZİN VE MİMARİ İSKELETİ (Scaffolding Tree)
# ------------------------------------------------------------------------------
Aşağıdaki dizin ağacını eksiksiz olarak oluştur:

<DİZİN_AĞACI>
[PROJE_KÖKÜ]/
├── CLAUDE.md                   # Claude Code Root Boot Pointer & Skill Registry SSOT
├── AGENTS.md                   # OpenCode V2 Root Boot & Master Agent Rules SSOT
├── GEMINI.md                   # Gemini / Antigravity Boot Instruction Pointer
├── README.md                   # Proje Künyesi, Vizyon, Mimari Katmanlar, Kurulum
├── PRD.md                      # Platform PRD Aile İndeksi
├── TODO.md                     # Proje Kök TODO & Görev Dağıtım İndeksi
├── WORKFLOW.md                 # Kök Süreç Yönlendiricisi (Pointer → .workflows/)
├── CONTEXT.md                  # Proje Dizin & Modül Envanter Bağlamı
├── RAG.md                      # RAG (Retrieval-Augmented Generation) Hızlı Yönlendirici
├── RAG-KILAVUZU.md             # RAG Kullanım & Token Koruma Rehberi (DOGE DOC)
├── .ai/                        # ★ İKİNCİ BEYİN & OBSIDIAN VAULT (SSOT)
│   ├── CLAUDE.md               # AI Anayasası (16 Hard Guardrail, Katmanlar, Standartlar)
│   ├── AGENTS.md               # Tam Agent Kayıt Defteri & Koordinasyon Protokolü
│   ├── durum.md                # Kök Tek Sayfalık Oturum Hafızası & Devir Dosyası
│   ├── WORKFLOW.md             # Kapsamlı Mühendislik Süreçleri & Onay Kapıları
│   ├── MEMORY.md               # Kalıcı Oturum Hafızası, Başlangıç 5 Soru / Kapanış 5 Adım
│   ├── index.md                # Master Vault Kataloğu & Hızlı Başvuru
│   ├── keys.md                 # A-Z Keyword & Dosya Arama Haritası
│   ├── log.md                  # Audit Trail (Zaman damgalı, YALNIZCA APPEND-ONLY)
│   ├── brain.md                # Kabul Edilmiş Mimari Kararlar (ADR Konsolidasyonu)
│   ├── engine.md               # Görev Dağıtım, Risk Değerlendirme & Yürütme Motoru
│   ├── glossary.md             # Kod Referanslı Terimler Sözlüğü (SSOT)
│   ├── PROJECTS.md             # Proje Tanımı, Temel Yetenekler, Kullanıcı Segmentleri
│   ├── ROLE.md                 # Rol & Teknoloji Eşleme Matrisi
│   ├── VISION.md               # Vizyon Belgesi & Pazar Problem-Çözüm Matrisi
│   ├── ULTRA-THINKING.md       # 5 Adımlı Derin Mühendislik Analiz Protokolü
│   ├── CHECKLIST.md            # Oturum Başlama, Geliştirme ve Kapanış Kontrol Listesi
│   ├── SYNC.md                 # Sürekli Senkronizasyon Döngüsü
│   ├── PLAN.md                 # Sıralı Yürütme ve Yol Haritası
│   ├── PLANNER.md              # Context Planner Kontrol Düzlemi (Control Plane FM)
│   ├── RAG.md                  # Vault İçi RAG Pipeline ve Dizin İndeksi
│   ├── TODO.md                 # Vault Seviyesi Kapsamlı Görev Listesi
│   ├── .rules/                 # Kural ve Direktif Dizinleri
│   │   ├── kurallar.md         # Temel Mühendislik Direktifleri (KURAL-01 .. KURAL-11)
│   │   ├── hata-listesi.md     # Bilinen Tuzaklar & Önleme Kataloğu (HL-01 .. HL-12)
│   │   └── senior-mode.md      # Zero-Hallucination & Kod Öncesi 16 Adım Ön-Analiz
│   ├── .decisions/             # Mimari Karar Kayıtları (ADR Repository)
│   │   ├── accepted/           # Kabul Edilen ve Yürürlükteki Kararlar (ADR-001..)
│   │   ├── draft/              # Taslak Aşamasındaki Kararlar
│   │   ├── rejected/           # Reddedilen Kararlar ve Gerekçeleri
│   │   └── index.md            # ADR Master Kayıt Defteri ve Durum Sayaçları
│   ├── .agents/                # Uzman Ajan Rol Profilleri (11 Specialist Profiles)
│   ├── .personas/              # 20 Personallık Tartışma/İnceleme Hiyerarşisi
│   ├── .obsidian/              # Obsidian Entegrasyon Ayarları (app, appearance, core-plugins, graph)
│   ├── .templates/             # Standart Şablon Kütüphanesi (adr, frontend, backend, docs)
│   ├── SESSIONS/               # Günlük/Oturumluk Oturum Kayıtları (YYYY-MM-DD-slug.md)
│   └── scripts/                # UTF-8 Güvenli Yazıcı ve Doğrulama Betikleri
├── .workflows/                 # Kiro Spec Standardı & Çalıştırılabilir İş Akışları
│   ├── design.md               # Sistem Mimarisi, Viewportlar ve Veri Sözleşmeleri
│   ├── tasks.md                # Kabul Kriterli Atomik Görev Şartnamesi
│   ├── workflow.md             # 6 Adımlı Yürütme Döngüsü (Req → Design → Tasks → Impl → Test → Handoff)
│   ├── session-init.md         # Oturum Başlatma ve Bağlam Yükleme Süreci
│   ├── vault-sync.md           # Oturum Sonu Senkronizasyon ve Hafıza Kapatma
│   ├── adr-creation.md         # Mimari Karar Yazım ve Onay Süreci
│   ├── security-audit.md       # OWASP Top 10 ve Güvenlik Denetim Akışı
│   ├── hallucination-control.md# Doğrulama, Kanıt Denetimi ve Red Team Filtresi
│   ├── orchestrator-flow.md    # Multi-Agent Görev Dağıtım ve Handover Protokolü
│   └── deployment.md           # Dağıtım Öncesi Doğrulama ve Rollback Prosedürü
└── [HER_MODÜL]/                # Projenin Alt Paketleri / Subdomainleri
    ├── durum.md                # Modüle Özel Tek Sayfalık Hafıza & Oturum Devir Dosyası
    ├── CONTEXT.md              # Modül Bağlamı & Dosya Haritası
    └── RAG.md                  # Modül İçi Hızlı Bilgi Çekme Rotası
</DİZİN_AĞACI>

# ------------------------------------------------------------------------------
# 3. ADIM ADIM OTONOM KURULUM VE UYGULAMA PROTOKOLÜ (7 AŞAMA)
# ------------------------------------------------------------------------------

### AŞAMA 1: DİZİN AĞACINI VE BOŞ DOSYALARI AÇ
1. Yukarıdaki tüm dizinleri oluştur.
2. UTF-8 güvenliği için `vault-utf8-writer.mjs` betiğini `.ai/scripts/` altına yerleştir. Windows ortamında PowerShell Set-Content/Out-File komutlarının Windows-1254 veya BOM bozulması yaratmasını engellemek için tüm dosya yazımlarında UTF-8 BOM'suz format kullan.

### AŞAMA 2: ÜÇLÜ KÖK BOOT MİMARİSİNİ KUR (Tri-Boot Architecture)
Her yapay zeka aracının projeyi en yüksek verimle okuması için 3 kök dosyayı yapılandır:
- `CLAUDE.md`: Claude Code için ana boot pointer'ıdır. İçinde projenin Skill Registry SSOT'unu ve `.ai/CLAUDE.md` bağlantısını barındırır.
- `AGENTS.md`: OpenCode V2 için ana boot dosyasıdır. Zero-Hallucination politikasını, 15 aşamalı yaşam döngüsünü, 20 persona swarm hiyerarşisini ve prompt-maker kurallarını içerir.
- `GEMINI.md`: Gemini CLI ve Google Antigravity için kök pointer'dır. Mühendislik standartlarını ve `.ai/` SSOT bağlantılarını tutar.
- **Session-End Check & Clean Kuralı:** Her oturum sonunda bu kök dosyaların bozulmadığını, yapıştırma hataları içermediğini ve formatlarının korunduğunu denetleyen protokolü her üç dosyaya ekle.

### AŞAMA 3: TEMEL MÜHENDİSLİK DİREKTİFLERİ VE HATA LİSTESİ (.ai/.rules/)
`.ai/.rules/` altına aşağıdaki 3 anayasal belgeyi yerleştir:
1. `kurallar.md` (KURAL-01 .. KURAL-11):
   - KURAL-01: Kurucu Kimliği ve Sistem Otoritesi (Engineering Authority).
   - KURAL-02: İletişim & Raporlama Standardı (Doğrudan, net, senior ton; dolgu/övgü yasak).
   - KURAL-03: UI/UX & Tema Standardı (Projenin tema kuralı: ör. Light Mode Mandatory veya Dark Mode).
   - KURAL-04: Katmanlı Mimari ve Teknik Guardrail'lar (Clean Code, strict_types, parametreli sorgu).
   - KURAL-05: Tek Sayfalık Oturum Hafızası & Durum Devri (`durum.md` esastır).
   - KURAL-06: Sıfır Halüsinasyon & Disk Kanıtı Zorunluluğu (Kanıtsız iddia silinir).
   - KURAL-07: Değişiklik, Bağımlılık & Onay Kapıları (Kritik işlemlerde Human Approval Gate).
   - KURAL-08: Oturum Yaşam Döngüsü & Token Koruma Protokolü (4 durumda yeni sohbete geçilir).
   - KURAL-09: Dağıtım Öncesi Hata Listesi Denetimi (`hata-listesi.md` taranmadan kod teslim edilmez).
   - KURAL-10: Dinamik Kural Yönetimi ve Yalınlık İlkesi (Kısa, operasyonel, canlı kurallar).
   - KURAL-11: Karpathy Davranışsal İlkeleri (Think Before Coding, Simplicity First, Surgical Changes, Goal-Driven Execution).
2. `hata-listesi.md` (HL-01 .. HL-12):
   - Projede karşılaşılan kritik bug'lar, mimari tuzaklar, yanlış varsayımlar tek tek kodlanarak (HL-01, HL-02...) listelenir. Kod yazmadan önce kontrol edilir.
3. `senior-mode.md`:
   - Kod Öncesi 16 Adım Ön-Analiz listesi (Repo, dizin, mimari, bağımlılık, pattern, build, test, config, logging, security, db, api, frontend, convention, duplicate, tech debt).
   - Zero-Hallucination 7 bilgi etiketi (`CONFIRMED`, `INFERRED`, `UNKNOWN`, `NEEDS_VALIDATION`, `WEB DOĞRULANDI`, `REFERENCE VERIFIED`, `CIKARIM`).

### AŞAMA 4: 12 MODÜLLÜ DAĞITIK HAFIZA VE RAG SİSTEMİ (Token Saver)
Token patlamasını önlemek ve oturumları hafif tutmak için:
1. Kök dizine `RAG.md` ve `RAG-KILAVUZU.md` (DOGE DOC) dosyalarını kur.
2. Projenin 12 mantıksal alt modülünü belirle (ör: `api`, `web`, `admin`, `auth`, `shared`, `core`, `scripts`, `.workflows`, `.agents` vb.).
3. Her modülün içine bağımsız bir `durum.md`, `CONTEXT.md` ve `RAG.md` koy.
4. `durum.md` Formatı:
   - `İş:` [Mevcut görevin adı]
   - `Durum:` [Tek cümlelik özet]
   - `Bitenler:` ✓ [Adım 1] · ✓ [Adım 2]
   - `Kaldığım yer:` [Nokta atışı dosya ve satır]
   - `Sırada:` [Bir sonraki adım]
   - `Kararlar:` [Alınan mimari kararlar]
   - `Açık:` [Kullanıcı onayı bekleyen sorular]
5. Oturum açılışında AI'a tüm depoyu taratmak yerine YALNIZCA ilgili modülün `durum.md` dosyasını okut (`~400 token` ile %90 tasarruf).

### AŞAMA 5: KIRO SPEC STANDARDI (.workflows/)
Modern AI ajanlarının bağlam kaybetmeden adım adım çalışması için `.workflows/` altına Kiro standardı 3 temel şartnameyi yaz:
1. `design.md`: Çok katmanlı mimari, bileşen sözleşmeleri, arayüz eşikleri ve veri akışı.
2. `tasks.md`: Bağımlılık zincirine bağlı, aşamalandırılmış (Faz 1..N), kabul kriterli atomik görev listesi (`[ ]` açık, `[x]` tamamlandı).
3. `workflow.md`: 6 adımlı ajan iş akışı:
   `Requirements (PRD) → Design (design.md) → Tasks (tasks.md) → Implement → Test → Handoff (durum.md)`.

### AŞAMA 6: 20 PERSONALLIK ÇOKLU-AJAN SWARM HİYERARŞİSİ
Tek bir AI modelinin her kararı tek başına alıp hata yapmasını önlemek için 3 kademeli inceleme hiyerarşisi kur:
- **Expert (5 Persona - Otorite):** `architect` (Mimari/ADR), `security-engineer` (OWASP/Auth), `data-engineer` (Şema/BCNF), `domain-specialist` (Özel Donanım/DSP/Çekirdek), `qa-engineer` (Test/Kabul Kriteri).
- **Senior (5 Persona - İnceleme):** `backend-architect`, `ui-designer`, `devops-engineer`, `performance-engineer`, `code-reviewer`.
- **Junior (10 Persona - Yürütme):** `explore`, `docs-writer`, `test-writer`, `refactorer`, `research-analyst`, `debugger`, `business-analyst`, `technical-writer`, `dependency-manager`, `error-coordinator`.
- **Müzakere Kuralı:** Tartışma 3 turda yürütülür: Tur 1 = Kaynaklı öneri; Tur 2 = Çapraz eleştiri; Tur 3 = Uzlaşma ve ADR. Kanıt gücü > seviye; eşit kanıtta Expert > Senior > Junior; oy çokluğu yoktur.

### AŞAMA 7: OBSIDIAN ENTEGRASYONU VE OTOMASYON BETİKLERİ
1. `.ai/.obsidian/` altına `app.json`, `appearance.json`, `core-plugins.json`, `graph.json`, `workspace.json` konfigürasyonlarını koyarak `.ai/` klasörünün doğrudan Obsidian Vault olarak açılabilmesini sağla.
2. Tüm dosyaların frontmatter'ına 7 zorunlu alanı ekle: `title`, `type`, `category`, `version`, `status`, `authority`, `updated`.
3. `.ai/log.md` dosyasını başlat ve ilk satırı ekle:
   `YYYY-MM-DD HH:mm - INIT - .ai/ - İkinci Beyin ve Vault Altyapısı Başarıyla Kuruldu - Master Orchestrator`.

# ------------------------------------------------------------------------------
# 4. ÇIKTI VE DOĞRULAMA TAAHHÜDÜ
# ------------------------------------------------------------------------------
Kurulum tamamlandığında aşağıdaki doğrulama çıktısını üret:
1. Oluşturulan dizinlerin ve dosyaların tam listesi.
2. Kök boot dosyalarının (`CLAUDE.md`, `AGENTS.md`, `GEMINI.md`) çapraz bağlantı testi.
3. 12 modüllü `durum.md` ve RAG sisteminin hazır olduğuna dair onay.
4. `.ai/.rules/kurallar.md` ve `hata-listesi.md` dosyalarının doğrulanması.
5. İlk oturum devir kaydının (`.ai/durum.md`) oluşturulması.
```

---

# BÖLÜM II: 🏛️ COREMUSIC REFERANS MİMARİSİ VE GÜNCEL VAULT ENVANTERİ

> Bu bölüm, CoreMusic projesinde fiilen çalışan, 2026-11-01 tarihi itibarıyla test edilmiş ve doğrulanmış gerçek mimari envanteridir. Yeni bir projeye aktarırken "Yapı Aynı / Veri Farklı" ilkesiyle birebir şablon olarak kullanılır.

---

## §1. Proje Kimliği ve Teknoloji Yığını Referansı

### 1.1 Proje Künyesi

| Alan | Değer (CoreMusic Referansı) | Yeni Projede Ne Yapılacak? |
|---|---|---|
| **Proje** | CoreMusic — `coremusic.net` | Kendi proje adınız ve alan adınız |
| **Tanım** | Kurumsal dijital ses, medya ekosistemi ve donanım yönetim platformu | Kendi sistem tanımınız |
| **Vizyon** | *"Aynı Müzik Her Yerde Seninle"* — Kesintisiz Handoff, Offline-First, Mülkiyet | Kendi ürün vizyonunuz |
| **Vault Kökü** | `.ai/` (SSOT) — Kök `CLAUDE.md`, `AGENTS.md`, `GEMINI.md` birer pointer'dır | Aynen korunur (`.ai/` evrensel standarttır) |
| **Vault Envanteri** | **24 kök .md · 20 alt dizin · 107 ADR · 66 şablon · 16 workflow** | Kendi dosya sayılarınız |
| **Oturum Hafızası** | **12 modüllü tek sayfalık `durum.md` PDF standardı** | Modüllerinize dağıtılmış `durum.md` seti |
| **RAG Sistemi** | `RAG.md` + `RAG-KILAVUZU.md` (DOGE DOC) + konu yönlendirici | Aynen kopyalanır ve uyarlanır |
| **Mühendislik Kuralı**| `KURAL-01 .. 11` + `HL-01 .. 12` + 16 Adım Ön-Analiz | Kurallar kendi stack'inize göre özelleştirilir |

### 1.2 Gerçek Teknoloji Yığını ve Katman Eşlemesi

| Katman | CoreMusic Gerçek Değeri | Açıklama / Kısıt |
|---|---|---|
| **Backend** | **PHP 8.4+** (Strict types, raw PDO, PSR-15 Middleware) | ORM kesinlikle yasak, `SELECT *` yasak, N+1 sorgu yasak |
| **Frontend** | **Vanilla JS (ES2022 SPA Router)** + **ITCSS 11 Katman** | JS framework yasak, TailwindCSS yasak, 0 raw hex (%100 token) |
| **Ses / Motor** | **C++20 NevaEngine**, JUCE 9, ASIO SDK, WASAPI Exclusive | Sıfır bellek tahsisi (zero-allocation), lock-free ring buffer |
| **Veritabanı** | **MySQL 9 (18 BCNF)**, Redis, APCu Önbellek | Şema SSOT dump'larıdır; canlı değişiklik yalnız migration ile (ADR-112) |
| **API Sürümleme** | **V1 default + V2 paralel tek route tablosunda** | Ayrık rota tablosu açılamaz, parite testleri zorunlu (ADR-113) |
| **Repository** | **Interface + final class + IDatabaseRegistry injection** | Yalnızca yeni kod için zorunlu, mevcut kod donduruldu (ADR-114) |
| **Güvenlik** | **Argon2id, AES-256-GCM, CSP Nonce, CSRF Token (`csrf_token`)**| Sırlar asla vault'a yazılmaz (`REDACTED`) |
| **Donanım** | **XMOS XU316, PCM3168A, AK4458, Discrete Class AB amfi** | Class D kesinlikle yasaktır |

---

## §2. Doğrulanmış Dizin ve Dosya Mimarisi Ağacı (2026-11-01 Güncel)

Aşağıdaki yapı, CoreMusic deposunda `Test-Path` ve disk sayımları ile mühürlenmiş güncel yapıdır:

```text
C:\www\coremusic.net\
├── CLAUDE.md                   # Claude Code boot pointer & Skill Registry SSOT (14 skill, karpathy-guidelines dahil)
├── CURSOR.md                   # Cursor IDE Rules & Karpathy Guidelines Pointer
├── .cursor/rules/              # Modern Cursor rules (.cursor/rules/karpathy-guidelines.mdc)
├── AGENTS.md                   # OpenCode V2 boot & Master Agent Rules (20 persona)
├── GEMINI.md                   # Gemini / Antigravity boot pointer
├── README.md                   # Proje künyesi, subdomainler, badge'ler, kurulum
├── PRD.md                      # Platform PRD aile indeksi (35 yüzey satırı)
├── TODO.md                     # Kök TODO aile indeksi (12 yüzey + kök)
├── WORKFLOW.md                 # Kök workflow göstergesi (Pointer → .workflows/)
├── CONTEXT.md                  # Proje genel dizin ve modül haritası
├── RAG.md                      # Hızlı RAG konu-hedef router'ı
├── RAG-KILAVUZU.md             # RAG & Akıllı Bilgi Erişim Kılavuzu (DOGE DOC)
├── notes.md                    # Hızlı geliştirme ve viewport notları
│
├── .ai/                        # ★ TEK DOĞRULUK KAYNAĞI (VAULT - OBSIDIAN ROOT)
│   ├── CLAUDE.md               # AI Anayasası (v27.6.0, 16 Hard Guardrail, §1-§34)
│   ├── AGENTS.md               # Tam Agent Kayıt Defteri (v22.2.0, §1-§26)
│   ├── durum.md                # Kök Tek Sayfalık Oturum Hafızası & Devir Dosyası
│   ├── WORKFLOW.md             # Kapsamlı Süreçler & Onay Kapıları (§1-§17)
│   ├── MEMORY.md               # Hafıza Sistemi (Başlangıç 5 Soru / Kapanış 5 Adım)
│   ├── index.md                # Master Vault Kataloğu (24 kök .md indeksi)
│   ├── keys.md                 # A-Z Keyword & Dosya Eşleme Haritası
│   ├── log.md                  # Audit Trail (Append-only, zaman damgalı)
│   ├── brain.md                # Kabul Edilmiş Mimari Kararlar Özeti
│   ├── engine.md               # Görev Yürütme ve Risk Değerlendirme Motoru
│   ├── glossary.md             # Terimler Sözlüğü (75 terim, kod referanslı)
│   ├── PROJECTS.md             # Proje Tanımı, 10 Yetenek, 6 Hedef Kitle
│   ├── ROLE.md                 # Rol Tanımı & Teknoloji Eşlemesi
│   ├── VISION.md               # Vizyon & Pazar Problem-Çözüm Matrisi
│   ├── ULTRA-THINKING.md       # 5 Adımlı Derin Mühendislik Analiz Protokolü
│   ├── AGENT-BRIEFING.md       # Ajan Görev Brifing Belgesi
│   ├── CHECKLIST.md            # Oturum Başlama / Orta / Kapanış Kontrol Listesi
│   ├── CONTEXT.md              # Vault Kök Bağlamı & Klasör Envanteri
│   ├── PLAN.md                 # Sıralı Yürütme Planı
│   ├── PLANNER.md              # Context Planner Kontrol Düzlemi Belgesi
│   ├── RAG.md                  # Vault İçi RAG Pipeline ve Dizin İndeksi
│   ├── SYNC.md                 # Sürekli Senkronizasyon Döngüsü
│   ├── TODO.md                 # Vault Seviyesi Kapsamlı Görev Listesi
│   ├── .env.figma              # Figma API Token SSOT (.gitignore:69, asla commit edilmez)
│   │
│   ├── .rules/                 # Kural ve Direktif Katmanı (3 dosya)
│   │   ├── kurallar.md         # Temel Mühendislik Direktifleri (KURAL-01 .. 11)
│   │   ├── hata-listesi.md     # Bilinen Hata ve Tuzaklar Kataloğu (HL-01 .. 12)
│   │   └── senior-mode.md      # Zero-Hallucination & Kod Öncesi 16 Adım Ön-Analiz
│   │
│   ├── .decisions/             # Mimari Karar Kayıtları (107 dosya)
│   │   ├── accepted/           # 85 kabul edilmiş karar (ADR-001 .. ADR-115)
│   │   ├── draft/              # 11 taslak karar (ADR-112, 113, 114, 115 dahil)
│   │   ├── rejected/           # 10 reddedilmiş karar ve mimari gerekçeleri
│   │   └── index.md            # ADR Master Kayıt Defteri ve Doğrulama Sayaçları
│   │
│   ├── .agents/                # Uzman Ajan Profilleri (12 dosya)
│   │   ├── AGENTS.md           # Alt Registry (Profil indeksi)
│   │   └── [11-ajan].md        # master-orchestrator, backend, ui, security, data vb.
│   │
│   ├── .personas/              # 20 Personallık Tartışma/İnceleme Hiyerarşisi (84 dosya)
│   ├── .obsidian/              # 5 config (app, appearance, core-plugins, graph, workspace)
│   ├── .templates/             # 66 standart şablon (12 alt kategori)
│   ├── SESSIONS/               # Günlük oturum kayıtları (YYYY-MM-DD-slug.md)
│   ├── scripts/                # 10 otomasyon ve doğrulama betiği
│   ├── architecture/           # 25 katman dizini (K000-K021, 1582 dosya)
│   └── ui-design/              # 313 UI spesifikasyon, ekran, token ve akış dosyası
│
├── .workflows/                 # Kiro Spec Standardı & Çalıştırılabilir İş Akışları (16 dosya)
│   ├── design.md               # Çok katmanlı mimari, viewport ve veri sözleşmeleri
│   ├── tasks.md                # Kabul kriterli atomik görev şartnamesi
│   ├── workflow.md             # 6 adımlı yürütme döngüsü
│   ├── session-init.md         # Oturum başlatma süreci (12 aşama)
│   ├── vault-sync.md           # Oturum kapatma ve senkronizasyon (8 aşama)
│   ├── adr-creation.md         # ADR oluşturma ve numaralandırma süreci
│   ├── security-audit.md       # OWASP Top 10 güvenlik denetim süreci
│   ├── hallucination-control.md# Doğrulama formatları ve Red Team kontrolü
│   ├── orchestrator-flow.md    # Multi-Agent orkestrasyon akışı (11 aşama)
│   ├── deployment.md           # Dağıtım ve rollback süreci
│   ├── durum.md                # .workflows dizinine özel tek sayfalık hafıza
│   ├── CONTEXT.md · RAG.md · AGENTS.md · CLAUDE.md · PRD.md · WORKFLOW.md
│
├── .agents/                    # Özel agent tanımları (CONTEXT, RAG, durum, rules, workflows)
│
└── [12 MODÜL / SUBDOMAIN]/     # 12 Bağımsız Çalışma Alanı
    ├── home.coremusic.net/     # Web Oynatıcı & Kullanıcı Arayüzü (durum.md, CONTEXT, RAG)
    ├── api.coremusic.net/      # REST API & Controller Katmanı (durum.md, CONTEXT, RAG)
    ├── auth.coremusic.net/     # Kimlik Doğrulama & JWT Servisi (durum.md, CONTEXT, RAG)
    ├── admin.coremusic.net/    # Yönetim Paneli & Media Yönetimi (durum.md, CONTEXT, RAG)
    ├── assets.coremusic.net/   # CSS ITCSS Katmanları & İkonlar (durum.md, CONTEXT, RAG)
    ├── media.coremusic.net/    # Medya Servisi & Akış İzinleri (durum.md, CONTEXT, RAG)
    ├── shared/                 # Ortak PHP Çekirdeği, Middleware, DB (durum.md, CONTEXT, RAG)
    ├── app-cpp-projects/       # C++20 NevaEngine & DSP Projeleri (durum.md, CONTEXT, RAG)
    ├── app-nodejs-projects/    # Node.js Mikroservisleri (durum.md, CONTEXT, RAG)
    └── bin/                    # CLI ve Terminal Yardımcıları (durum.md, CONTEXT, RAG)
```

---

## §3. AI Anayasası & 16 Hard Guardrail (Değiştirilemez Kurallar)

`.ai/CLAUDE.md` §7 altında mühürlenen 16 sert kural:

| # | Kural Adı | Tanım & Kısıt | İhlal Sonucu |
|---|---|---|---|
| **1** | **Zero Code Before Plan** | Planlama ve analiz tamamlanmadan tek satır kod yazılamaz. | Kod derhal revert edilir |
| **2** | **Vault First** | İlgili `.ai/` ve `durum.md` dosyaları okunmadan işe başlanamaz. | İşlem durdurulur |
| **3** | **Zero Hallucination** | Doğrulanamayan iddia yasaktır (`⚠️ VERIFICATION REQUIRED`). | İlgili metin silinir |
| **4** | **In-Place Refactoring** | Dosya adı, yolu veya import yapısı keyfi olarak değiştirilemez. | Dosya geri yüklenir |
| **5** | **Single Source of Truth** | Bilgi yalnızca `.ai/` içinde yaşar; harici veri vault'un altındadır. | Harici iddia reddedilir |
| **6** | **CSRF Token Adı** | Form ve header'larda kesinlikle `csrf_token` kullanılır (`_csrf_token` yasak).| İstek reddedilir |
| **7** | **Middleware Order Immutable** | 10 adımlık PSR-15 middleware boru hattı sırası değiştirilemez. | Pipeline durdurulur |
| **8** | **Port İzolasyonu** | Port kayıt defterine (`80`, `81`, `3001`, `3306`, `9741`) sıkı uyum. | Servis durdurulur |
| **9** | **No ORM (Raw PDO Only)** | Eloquent, Doctrine vb. ORM yasaktır; parametreli raw PDO zorunludur. | Kod reddedilir |
| **10** | **No JS Frameworks** | React, Vue, Angular yasaktır; Vanilla JS ES2022+ ve ITCSS kullanılır. | Kod reddedilir |
| **11** | **Mockup Before Frontend** | `.ai/ui-design/` altındaki görsel/spec okunmadan UI kodu yazılamaz. | Revert + CRITICAL log |
| **12** | **Contradiction Gate** | Vault içi çelişki tespit edildiğinde sessiz kalınamaz; kullanıcıya sorulur. | İşlem durur |
| **13** | **Session Continuity** | Oturum kapatılırken `durum.md` ve `log.md` güncellenerek durum devredilir. | Bağlam kaybı uyarısı |
| **14** | **Human Approval Gate** | Mimari şema, DDL migration veya paket eklemede kullanıcı onayı şarttır. | İşlem beklemeye alınır |
| **16** | **Template Mandatory** | Yeni dosya üretilirken `.ai/.templates/` kalıplarına uymak zorunludur. | Dosya geçersiz sayılır |
| **17** | **Single Component Responsive**| 1024 ve 1920 için ayrı HTML üretilemez; tek bileşen CSS ile duyarlı kılınır. | Kod revert edilir |

---

## §4. 12 Modüllü Dağıtık Hafıza ve RAG Sistemi (DOGE DOC)

Token limitlerini tüketmemek amacıyla oluşturulan PDF standardı modüler hafıza yapısı:

```text
[KULLANICI TALEBİ]
        │
        ▼
[RAG ROUTER: RAG.md / RAG-KILAVUZU.md]
        │
        ├───────────────────────────────────────┐
        ▼                                       ▼
[HEDEF MODÜL: durum.md]                 [HEDEF KAYNAK KOD]
- İşin Adı ve Özeti                     - Yalnızca ilgili method
- Bitenler & Kaldığı Yer                - Cerrahi satır aralığı
- Açık Kararlar & Sırada Ne Var?        (~1,500 token)
(~400 token)
        │                                       │
        └───────────────────┬───────────────────┘
                            ▼
                   [LLM ÇALIŞMA ALANI]
                 (Maksimum 2,000 Token ile
                  %90 Tasarruf & Sıfır Hata)
```

### 12 Modülün Dağıtımı:
1. `home.coremusic.net/durum.md` — Kullanıcı arayüzü, web player, müzik sayfaları.
2. `api.coremusic.net/durum.md` — Controller, route, OpenAPI, JSON API sözleşmeleri.
3. `auth.coremusic.net/durum.md` — Giriş, kayıt, MFA, session, JWT güvenlik akışları.
4. `admin.coremusic.net/durum.md` — Yönetim paneli, RBAC, kullanıcı yönetimi, media yönetimi.
5. `assets.coremusic.net/durum.md` — CSS ITCSS katmanları (`01_Abstracts` .. `11_OAuth`), tokenlar, ikonlar.
6. `media.coremusic.net/durum.md` — Medya ayrıştırma, dosya yolları, stream izinleri.
7. `shared/durum.md` — Ortak PHP sınıfları, PageRouter, Middleware, Database Registry.
8. `app-cpp-projects/durum.md` — C++20 NevaEngine, DSP boru hattı, ASIO/WASAPI sürücüleri.
9. `app-nodejs-projects/durum.md` — İndirme servisi, yardımcı JS scriptleri.
10. `bin/durum.md` — CLI terminal araçları, anahtar üretim betikleri.
11. `.agents/durum.md` — Ajan profilleri, yetki haritaları ve promptlar.
12. `.workflows/durum.md` — Görev süreçleri, Kiro spec standartları ve otomasyon.

---

## §5. Temel Mühendislik Direktifleri (KURAL-01..11) ve Hata Listesi (HL-01..12)

### 5.1 Temel Mühendislik Direktifleri (`.ai/.rules/kurallar.md` — v2.3.0)
- **KURAL-01 (Otorite):** Sistem Mimarı ve Kurucu otoritesi esastır.
- **KURAL-02 (İletişim):** Türkçe, doğrudan, net ve senior seviyede. Övgü ve dolgu ifadeler yasaktır.
- **KURAL-03 (Tema):** CoreMusic'te ŞU ANDA YALNIZCA LIGHT MODE MEVCUTTUR. Dark mode henüz yapılmamıştır/yoktur; dark class veya `@media (prefers-color-scheme: dark)` yazmak KESİNLİKLE YASAKTIR.
- **KURAL-04 (Mimari):** strict_types=1, Raw PDO, Vanilla JS, discrete Class AB amfi (Class D yasak), migration zorunluluğu (ADR-112), V1/V2 tek rota tablosu (ADR-113), Repository Pattern yalnız yeni koda (ADR-114), Media web paneli iptal, yönetim admin panelinde (ADR-115).
- **KURAL-05 (Oturum):** İşe `durum.md` okunarak başlanır; iş bitiminde `durum.md`, `log.md`, `MEMORY.md` güncellenerek devredilir.
- **KURAL-06 (Kanıt):** Disk kanıtı olmadan tespit yazılamaz; bilinmeyen = `UNKNOWN`, şüpheli = `⚠️ VERIFICATION REQUIRED`.
- **KURAL-07 (Onay):** Bağımlılık, paket, şema değişikliği veya harici serviste onay şarttır.
- **KURAL-08 (Yaşam Döngüsü):** Konu değiştiğinde, AI ağırlaştığında, büyük dosya analizi bittiğinde veya teslimat yapıldığında YENİ SOHBETE geçilir.
- **KURAL-09 (Hata Kontrolü):** Teslim öncesi `hata-listesi.md` taranır.
- **KURAL-10 (Yalınlık):** Kural dosyası operasyonel ve kısa tutulur.
- **KURAL-11 (Karpathy Davranışsal İlkeleri):** Think Before Coding (varsayım yapma, seçenek sun), Simplicity First (minimum kod, sıfır spekülasyon), Surgical Changes (yalnızca istenen yere dokun, yetimleri temizle), Goal-Driven Execution (test-first doğrulanabilir hedefler).

### 5.2 Hata Önleme Kataloğu (`.ai/.rules/hata-listesi.md` — v1.1.0)
- **HL-01:** Tema Zorunluluğu (YALNIZCA LIGHT MODE).
- **HL-02:** Amplifikatör Mimarisi (CLASS AB; Class D yasak).
- **HL-03:** Sürüm Kontrolü (Subagent commit atamaz).
- **HL-04:** CSS Container Query (Kapsayıcıya `container-type: inline-size`, sütunlara `minmax(0, 1fr)`).
- **HL-05:** JS DOM Yaşam Döngüsü (DOM'da olmayan eleman için sessiz kalma; `dbg.warn` ver).
- **HL-06:** Sayısal Oran Hassasiyeti (Yuvarlanmış yüzde yerine ham oran `$volRatio <= 0.02 / 0.33 / 0.66`).
- **HL-07:** Kimlik Bilgisi Güvenliği (`FIGMA_TOKEN` yalnızca `.ai/.env.figma`'dan okunur, asla loga/md'ye yazılmaz).
- **HL-08:** Veritabanı Bütünlüğü (Canlı şema manuel DDL ile değil, migration scripti ve onay ile güncellenir).
- **HL-09:** Yetkilendirme Sözlüğü (Standart dışı `requiredRole` uydurulamaz; 403 hatası verir).
- **HL-10:** Ağ & Güvenlik Başlıkları (Antivirüs script enjeksiyonları sunucu CSP hatası sanılmamalıdır).
- **HL-11:** Token & Oturum Optimizasyonu (Tek sohbette şişme; 4 tetikleyicide yeni sohbete geç).
- **HL-12:** Stil Mimarisi Bütünlüğü (ITCSS import sırası ve katman yerleşimi onay olmadan değiştirilemez).

---

## §6. Kiro Spec Standardı ve `.workflows/` 16 Süreç Dosyası

`.workflows/` klasöründe yer alan 16 süreç belgesi:
1. `design.md`: Çok katmanlı mimari, responsive viewport eşikleri (1024, 1920, 4K), veri sözleşmeleri.
2. `tasks.md`: Bağımlılık zincirine bağlı, aşamalandırılmış, kabul kriterli görev şartnamesi.
3. `workflow.md`: Kiro standardı 6 adımlı yürütme iş akışı (Requirements → Design → Tasks → Impl → Test → Handoff).
4. `session-init.md`: Oturum başlangıcı ve bağlam yükleme protokolü (12 aşama).
5. `vault-sync.md`: Oturum sonu hafıza senkronizasyon ve kayıt protokolü (8 aşama).
6. `adr-creation.md`: Mimari karar kayıt (ADR) oluşturma ve onay akışı (6 adım).
7. `security-audit.md`: OWASP Top 10 ve CoreMusic özel kontrolleriyle güvenlik denetimi.
8. `hallucination-control.md`: Kanıt kontrolü, truth mode kuralları ve Red Team doğrulaması.
9. `orchestrator-flow.md`: Çok ajanlı görev dağıtımı, handover ve eskalasyon akışı (11 aşama).
10. `deployment.md`: Dağıtım öncesi sağlık kontrolleri ve rollback akışı (8 aşama).
11. `durum.md`: .workflows modülüne özel tek sayfalık hafıza dosyası.
12. `CONTEXT.md`: Süreçler bağlamı ve iş akışı haritası.
13. `RAG.md`: Süreç içi hızlı arama ve yönlendirme rotası.
14. `AGENTS.md`: İş akışı ajan yetki göstergesi.
15. `CLAUDE.md`: İş akışı anayasal göstergesi.
16. `PRD.md`: Süreç gereksinimleri belgesi.

---

## §7. 20 Personallık Çoklu-Ajan Swarm Hiyerarşisi

| Kademe | # | Persona (Agent) | Sorumluluk & Yetki Alanı |
|---|---|---|---|
| **Expert (5)** | 1 | `architect` | Sistem tasarımı, katman sınırları, ADR yönetimi, baş mimar |
| | 2 | `security-engineer` | OWASP, auth, CSRF/CSP/XSS, şifreleme, güvenlik denetimi |
| | 3 | `data-engineer` | Veritabanı şeması, 18 BCNF, migration, PDO, veri bütünlüğü |
| | 4 | `embedded-engineer` | DSP, ASIO/WASAPI, firmware, Class AB donanım elektroniği |
| | 5 | `qa-engineer` | Test stratejisi, coverage (min ≥%80, hedef ≥%90), kabul kriteri |
| **Senior (5)** | 6 | `backend-architect` | PHP 8.4, API, PSR-15 middleware, controller, repository |
| | 7 | `ui-designer` | ITCSS 11 katman, BEM, semantik tokenlar, WCAG 2.2 AA, responsive |
| | 8 | `devops-engineer` | CI/CD, GitHub Actions, Docker, deploy, sunucu altyapısı |
| | 9 | `performance-engineer` | Profiling, bellek sızıntısı, latency, Core Web Vitals, sıfır-tahsis |
| | 10 | `code-reviewer` | SOLID, Clean Code, code smell denetimi, bakım yapılabilirlik |
| **Junior (10)** | 11 | `explore` | Dosya, sembol, class ve fonksiyon arama keşfi |
| | 12 | `docs-writer` | Dokümantasyon, README, API spesifikasyon yazımı |
| | 13 | `test-writer` | PHPUnit, Vitest, Playwright test senaryoları taslağı |
| | 14 | `refactorer` | Okunabilirlik, davranış-koruyucu küçük refactoring |
| | 15 | `research-analyst` | Web ve kaynak araştırması, çapraz doğrulama |
| | 16 | `debugger` | Log, stack trace, hata ayıklama ve kök neden analizi |
| | 17 | `business-analyst` | Ürün gereksinimi, PRD analizleri, user story çıkarma |
| | 18 | `technical-writer` | Kılavuz, tutorial, geliştirici el kitapçığı |
| | 19 | `dependency-manager` | Paket, kütüphane, sürüm ve lisans denetimi |
| | 20 | `error-coordinator` | Hata yayılımı, log takibi ve eskalasyon yönetimi |

---

## §8. "Yapı Aynı / Veri Farklı" Değişken Eşleme Tablosu

CoreMusic'ten başka bir projeye geçerken dönüştürülecek alanlar:

| CoreMusic Yapısal Alanı | Yeni Projedeki Karşılığı | Dönüşüm Kuralı |
|---|---|---|
| `CoreMusic` / `coremusic.net` | `{PROJE_ADI}` / `{ALAN_ADINIZ}` | Projenin resmi ticari adı ve ana domaini |
| `.ai/` Vault Kökü | `.ai/` (Aynen korunur) | Tüm ikinci beyin ve Obsidian standartları `.ai/` altında yaşar |
| `CLAUDE.md`, `AGENTS.md`, `GEMINI.md` | Aynı 3 dosya | Kök dizinde aynı isimlerle açılır, boot pointer görevi görür |
| `.ai/.rules/kurallar.md` | `.ai/.rules/kurallar.md` | `KURAL-01..11` yapısı korunur; teknoloji stack'i projeye uyarlanır |
| `.ai/.rules/hata-listesi.md` | `.ai/.rules/hata-listesi.md` | `HL-01..N` yapısı korunur; projenin kendi tuzakları listelenir |
| `durum.md` Hafıza Sistemi | Her alt modüle bir `durum.md` | Tek sayfalık hafıza kalıbı harfiyen korunur |
| `RAG.md` + `RAG-KILAVUZU.md` | Kök ve modüllere `RAG.md` | RAG router yapısı korunur; rota sözlüğü yeni dosyalara bağlanır |
| `.workflows/` (16 dosya) | `.workflows/` | Süreç dosyaları aynen kopyalanır ve proje komutlarına uyarlanır |
| PHP 8.4 + Vanilla JS + C++20 | `{BACKEND}` + `{FRONTEND}` + `{CORE}`| Kendi dil ve framework seçimleriniz yazılır |
| MySQL 9 (18 BCNF) | `{DATABASE_STACK}` | Kendi veritabanı motorunuz ve normalizasyon hedefiniz |
| Discrete Class AB Amfi | `{DONANIM_VEYA_ÖZEL_ALAN}` | Donanım yoksa "Donanım Katmanı Yoktur" yazılarak devre dışı bırakılır |
| 16 Hard Guardrail | 16 Hard Guardrail | 1-5, 12-16 evrenseldir; 6-11 proje stack'ine göre uyarlanır |

---

## §9. Otomatik Kurulum Betiği (PowerShell — `New-SecondBrain.ps1`)

Yeni projenin kök dizininde çalıştırıldığında tüm dizinleri, kök göstergeleri ve temel `.ai` dosyalarını oluşturan betik:

```powershell
# ==============================================================================
# New-SecondBrain.ps1 — Yapay Zeka İkinci Beyin & Obsidian Vault Kurucu
# Kullanım: powershell -ExecutionPolicy Bypass -File .\New-SecondBrain.ps1 -ProjectName "YeniProje"
# ==============================================================================
param(
  [string]$Root = (Get-Location).Path,
  [string]$ProjectName = "MyProject"
)

$ErrorActionPreference = "Stop"
[Console]::OutputEncoding = [Text.Encoding]::UTF8

Write-Host ">>> [1/5] Dizin Ağacı Oluşturuluyor..." -ForegroundColor Cyan

$dirs = @(
  ".ai", ".ai/.rules", ".ai/.templates", ".ai/.decisions", ".ai/.decisions/accepted",
  ".ai/.decisions/draft", ".ai/.decisions/rejected", ".ai/.agents", ".ai/.personas",
  ".ai/.obsidian", ".ai/.sql", ".ai/architecture", ".ai/projects", ".ai/ui-design",
  ".ai/ecosystem", ".ai/reports", ".ai/index", ".ai/SESSIONS", ".ai/scripts",
  ".workflows", ".agents", ".cursor", ".cursor/rules", ".claude", ".claude/skills"
)

foreach ($d in $dirs) {
  $p = Join-Path $Root $d
  if (-not (Test-Path $p)) { New-Item -ItemType Directory -Force -Path $p | Out-Null }
}

Write-Host ">>> [2/5] Kök Gösterge ve Dokümantasyon Dosyaları Açılıyor..." -ForegroundColor Cyan

$rootFiles = @(
  "CLAUDE.md", "AGENTS.md", "GEMINI.md", "CURSOR.md", "README.md", "PRD.md", "TODO.md",
  "WORKFLOW.md", "CONTEXT.md", "RAG.md", "RAG-KILAVUZU.md", "notes.md"
)

foreach ($rf in $rootFiles) {
  $p = Join-Path $Root $rf
  if (-not (Test-Path $p)) { New-Item -ItemType File -Force -Path $p | Out-Null }
}

Write-Host ">>> [3/5] .ai/ Vault Temel Dosyaları Açılıyor..." -ForegroundColor Cyan

$aiFiles = @(
  "CLAUDE.md", "AGENTS.md", "durum.md", "WORKFLOW.md", "MEMORY.md", "index.md",
  "keys.md", "log.md", "brain.md", "engine.md", "glossary.md", "PROJECTS.md",
  "ROLE.md", "VISION.md", "ULTRA-THINKING.md", "CHECKLIST.md", "SYNC.md",
  "PLAN.md", "PLANNER.md", "RAG.md", "TODO.md"
)

foreach ($af in $aiFiles) {
  $p = Join-Path $Root ".ai/$af"
  if (-not (Test-Path $p)) { New-Item -ItemType File -Force -Path $p | Out-Null }
}

Write-Host ">>> [4/5] Kural ve Süreç Dosyaları Hazırlanıyor..." -ForegroundColor Cyan

$ruleFiles = @("kurallar.md", "hata-listesi.md", "senior-mode.md")
foreach ($rf in $ruleFiles) {
  $p = Join-Path $Root ".ai/.rules/$rf"
  if (-not (Test-Path $p)) { New-Item -ItemType File -Force -Path $p | Out-Null }
}

$workflowFiles = @(
  "design.md", "tasks.md", "workflow.md", "session-init.md", "vault-sync.md",
  "adr-creation.md", "security-audit.md", "hallucination-control.md",
  "orchestrator-flow.md", "deployment.md", "durum.md"
)
foreach ($wf in $workflowFiles) {
  $p = Join-Path $Root ".workflows/$wf"
  if (-not (Test-Path $p)) { New-Item -ItemType File -Force -Path $p | Out-Null }
}

Write-Host ">>> [5/5] Audit Log ve İlk Hafıza Mühürleniyor..." -ForegroundColor Cyan

$stamp = Get-Date -Format "yyyy-MM-dd HH:mm"
$logPath = Join-Path $Root ".ai/log.md"
$logInit = "$stamp - INIT - .ai/ - $ProjectName İkinci Beyin & Vault Altyapısı Kuruldu - Master Orchestrator`n"
[IO.File]::AppendAllText($logPath, $logInit, (New-Object Text.UTF8Encoding $false))

$durumPath = Join-Path $Root ".ai/durum.md"
$durumInit = @"
---
title: "$ProjectName — Oturum Durum & Devir Dosyası (DURUM.md)"
type: state
category: session-state
version: 1.0.0
status: active
authority: "SSOT: .ai/durum.md"
updated: $(Get-Date -Format "yyyy-MM-dd")
---

# DURUM.md — Oturum Durum & Devir Dosyası (Tek Sayfalık Hafıza)

**İş:** $ProjectName İkinci Beyin & Obsidian Altyapı Kurulumu
**Durum:** Temel dizinler, kök boot göstergeleri, kurallar ve hafıza şablonları kuruldu; ilk görev bekleniyor.
**Bitenler:** ✓ Scaffolding tamamlandı · ✓ Kurallar ve hata kataloğu oluşturuldu · ✓ Tri-boot göstergeleri hazırlandı.
**Kaldığım yer:** .ai/ vault dosyalarının proje parametrelerine göre doldurulması.
**Sırada:** Proje bağımlılıklarının taranması ve ilk PRD/TODO tanımlaması.
**Kararlar:** Token koruma amacıyla 12 modüllü dağıtık durum.md sistemi uygulanacak.
**Açık:** Yok.
"@
[IO.File]::WriteAllText($durumPath, $durumInit, (New-Object Text.UTF8Encoding $false))

Write-Host "TEBRİKLER! $ProjectName İkinci Beyin & Obsidian Vault Altyapısı Başarıyla Kuruldu." -ForegroundColor Green
```

---

## §10. Oturum Sonu Kontrol & Temizlik Protokolü (Session-End Check & Clean)

Her geliştirme oturumunun sonunda veya iş tamamlandığında orkestratör agent tarafından aşağıdaki 4 adım yürütülür:

1. **Sözdizimi ve Temizlik Denetimi:** Kök `CLAUDE.md`, `AGENTS.md`, `GEMINI.md` ve ilgili `durum.md` dosyalarında bozuk karakter, eksik kapanmış kod bloğu, yapıştırma hatası veya yinelenen metin blokları bulunmadığı kontrol edilir.
2. **Bağlantı Doğrulaması:** Kök dosyalar ile `.ai/` vault SSOT dosyaları ve `.ai/.rules/` kuralları arasındaki wiki-link (`[[...]]`) bağlantılarının doğruluğu teyit edilir.
3. **Tek Sayfalık Hafıza Devri:** Çalışılan modülün `durum.md` dosyasına ne yapıldığı, nerede kalındığı ve açık kararlar işlenir; bir sonraki oturumun yalnızca o dosyayı okuyarak başlaması garanti edilir.
4. **Denetim Kaydı (Audit Append):** Yapılan değişiklikler `.ai/log.md` dosyasına `YYYY-MM-DD HH:mm - İŞLEM - modül - özet - agent` formatında eklenir (`log.md` salt-okunur ve append-only'dir; geçmiş kayıtlar asla silinemez).

---

*Bu şablon, CoreMusic ekosisteminin fiili disk ölçümleri, aktif oturum devirleri ve kanıtlanmış otonom mühendislik standartları esas alınarak üretilmiştir (v3.0.0 — 2026-11-01).*
