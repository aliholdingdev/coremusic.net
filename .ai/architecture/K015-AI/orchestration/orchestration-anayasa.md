---
node_id: orchestration-anayasa
node_type: module
status: active
parent_id: orchestration
children: []
depends_on: [orchestration-index]
evidence:
  - ".ai/AGENTS.md §6-§12 (routing/dispatch/handover/escalation/health/lock, okundu 2026-10-17)"
  - "shared/src/AI/AIOrchestrator.php (AGENT_MAP/TASK_TYPES kod kanıtı)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı"
open_questions: []
group: mimari
---

# orchestration-anayasa - Ajan Orkestrasyon Kuralları

> **Bu dosya ne değildir:** 11 ajanın **yetki tanımını** (`.ai/AGENTS.md` §4/§5/§15 SSOT) ve orkestrasyon
> motorunun **kodunu** taşımaz; burada **görev dağıtım anayasası** vardır — bir istek hangi adımlardan
> geçer, hangi ajana gider, ne zaman el değiştirir, ne zaman yukarı eskale edilir, kilit nasıl kırılır.

## §1 Rol (ELI15)

Orkestrasyon alt-domaini, "bu istek hangi ajana, hangi sırayla, hangi onay kapılarından geçerek verilmeli?"
sorusunun **tek kaynaktan** cevaplanmasını sağlayan kural setinin sahibidir. Dosya bir süreç/plan dosyası
değildir; `.ai/AGENTS.md` §6-§12'yi **künyeli özetler**, kod karşılığını gösterir ve bu anayasada
üç kavramı birbirinden ayırır: **dispatch** (görevin atanması), **routing** (ajana eşleme), **lock**
(aynı dosyaya erişim kilidi).

| Soru | Cevap | Kanıt |
|------|-------|-------|
| Kuralların SSOT'u nerede? | `.ai/AGENTS.md` §6-§12 (v22.2.0) | `.ai/AGENTS.md:7` (version) |
| Motor indeksi ne der? | Bölüm→§ eşlemesi AGENTS.md'de, ek içerik engine.md'de | `.ai/engine.md:35-43` |
| Kod karşılığı var mı? | `AIOrchestrator` — AGENT_MAP + TASK_TYPES | `AIOrchestrator.php:26-49` |
| Kim koordine eder? | Master Orchestrator (`mo`) | `.ai/AGENTS.md:79` |

> **eli10:** Kurallar vault'ta, kodun birebir aynısı değil iskeleti var — ikisini karıştırma.
> **eli15:** Yönlendirme tablosu kural metninden daha sık değişir → ayrı bölümde künyeli taşınır
> (`Dosya: .ai/engine.md · Satır: 38`).

## §2 Kapsam (in / out)

| Kapsam | Kapsam Dışı | Kanıt |
|--------|-------------|-------|
| Task Dispatch 7 adım | İş mantığı, alt domain kodu | `.ai/AGENTS.md:62` |
| Keyword → Agent routing tabloları | Agent yetki/kısıt tanımı (SSOT: §4/§5) | `.ai/AGENTS.md:61` |
| Handover + eskalasyon protokolü | Veritabanı işlemleri | `.ai/AGENTS.md:64` |
| Health check + context lock | Güvenlik politikası (SSOT: Security Engineer) | `.ai/AGENTS.md:65` |
| Öncelik seviyeleri + timeout/retry | Ajan profilleri (`[[.agents/*]]`) | `.ai/AGENTS.md:130` |

**Sınır ayrımı (bağlayıcı):** *orkestrasyon* görevin **akışını** yönetir · *routing* yalnız
**anahtar kelime→ajan** eşlemesidir (§6) · *context lock* yalnız **dosya/alan erişim sırasını** korur
(`Dosya: .ai/AGENTS.md · Satır: 441` "Eşzamanlı erişimi önlemek için dosya kilitleme mekanizması").
Biri diğerinin yerine geçmez; lock kararı routing sonucunu **değiştirmez**, erteleyebilir.

## §3 Terminoloji (12 terim)

| Terim | Tanım | Kanıt |
|-------|-------|-------|
| Agent | Belirli bir alanda uzmanlaşmış AI birimi (11 adet) | `.ai/AGENTS.md:709` |
| Master Orchestrator (MO) | Görev dağıtan koordinasyon birimi | `.ai/AGENTS.md:710` |
| Domain Boundary | Ajanın yalnız kendi alanında çalışması | `.ai/AGENTS.md:711` |
| Handover | Görevin başka ajana devri | `.ai/AGENTS.md:712` |
| Eskalasyon | Çözülemeyen sorunun üst seviyeye çıkması | `.ai/AGENTS.md:713` |
| Context Lock | Eşzamanlı dosya erişimini önleyen kilit | `.ai/AGENTS.md:714` |
| Health Check | Ajan çalışma durumu kontrolü | `.ai/AGENTS.md:715` |
| Task Queue | Öncelik sırasıyla bekleyen görevler | `.ai/AGENTS.md:716` |
| Pre-flight Check | Görev öncesi kontroller | `.ai/AGENTS.md:717` |
| Stack Etiketi | IMPLEMENTED / PLANNED ayırımı | `.ai/AGENTS.md:718` |
| Uncertainty Flag | Alt agent belirsizlik raporu | `.ai/AGENTS.md:719` |
| Faz Kapanışı | 8 maddelik kapanış listesi | `.ai/AGENTS.md:720` |

⚠️ VERIFICATION REQUIRED: bu 12 terim `glossary.md`'de kayıtlı değil — vault'a eklenmesi açık madde
(`Dosya: .ai/AGENTS.md · Satır: 705`).

## §4 Dispatch 7 Adım (SSOT §7)

| # | Adım | Ne olur | İhlal | Kanıt |
|---|------|---------|-------|-------|
| 1 | Analiz | Keyword çıkarma, domain eşleme, öncelik, ajan seçimi | — | `.ai/AGENTS.md:259-266` |
| 2 | Pre-flight | Boundary, lock, bağımlılık, mockup, şablon, gate script | Mockup/şablon okunmadıysa → DUR | `.ai/AGENTS.md:268-281` |
| 3 | Task Assignment | Doğru ajan atanır (§8 öncelik tablosu) | — | `.ai/AGENTS.md:283-285` |
| 4 | Execution | Domain boundary korunur, Zero Code Before Plan | Layer violation → revert | `.ai/AGENTS.md:287-292` |
| 5 | Handover | Gerekirse diğer ajana transfer (§10) | Onaysız devir yok | `.ai/AGENTS.md:294-296` |
| 6 | Validation | Format, domain, cross-reference, redaction | Hassas veri → maskelenir | `.ai/AGENTS.md:298-305` |
| 7 | Completion | Görev kapanır, `log.md` append, MEMORY güncelle | Logsuz kapanış yasak | `.ai/AGENTS.md:307-312` |

Aynı algoritma motor indeksinde 7 satır olarak tekrarlanır (adım 4 lock almadır):
`Dosya: .ai/engine.md · Satır: 95-105`. İki kayıt **aynı** sırayı taşır; çelişirse `.ai/AGENTS.md` kazanır.

## §5 Domain Boundary — Dosya Tipi Sahipliği (§5)

| Dosya Tipi | Sorumlu Agent | Diğerleri | Kanıt |
|------------|---------------|-----------|-------|
| `*.php` (Controller/Service/Repository) | Backend Architect | ✅ | `.ai/AGENTS.md:97` |
| `*.js` (Frontend) | UI Designer | ✅ | `.ai/AGENTS.md:98` |
| `*.css` (ITCSS layers) | UI Designer | ✅ | `.ai/AGENTS.md:99` |
| `*.sql` (Schema, Migration) | Data Engineer | ✅ | `.ai/AGENTS.md:100` |
| `*.cpp` / `*.h` (Audio Engine) | Embedded Engineer | ✅ | `.ai/AGENTS.md:101` |
| `*.yml` / `*.yaml` (CI/CD) | DevOps Engineer | ✅ | `.ai/AGENTS.md:102` |
| `tests/**/*.php` · `tests/**/*.test.js` | QA Engineer | ✅ | `.ai/AGENTS.md:103-104` |
| Security middleware · `.env` | Security Engineer | ✅ | `.ai/AGENTS.md:105-106` |
| `log.md` (audit trail) | Tüm ajanlar | ✅ Sadece ekleme | `.ai/AGENTS.md:107` |
| `.ai/` vault | MO (koordinasyon) | ✅ Okuma serbest | `.ai/AGENTS.md:108` |

**İhlal sonucu:** "Domain boundary ihlali → Sistem durur, MO müdahale eder"
(`Dosya: .ai/AGENTS.md · Satır: 217`); execution adımında "Layer violation → derhal revert + log ERROR"
(`Dosya: .ai/AGENTS.md · Satır: 203`).

## §6 Routing Tabloları (Keyword → Agent, 9 grup)

| Keyword Grubu | Birincil | İkincil | Kanıt |
|---------------|----------|---------|-------|
| API, endpoint, routing, middleware, PHP, controller, repository | Backend Architect | Security Engineer | `.ai/AGENTS.md:234` |
| CSS, UI, responsive, accessibility, ITCSS, BEM, frontend, design, JS, mockup, ui-design, token | UI Designer | QA Engineer | `.ai/AGENTS.md:235` |
| CSRF, CSP, XSS, OWASP, auth, encryption, security, session, rate limit | Security Engineer | Backend Architect | `.ai/AGENTS.md:236` |
| database, SQL, BCNF, migration, query, schema, MySQL, PDO, index | Data Engineer | Backend Architect | `.ai/AGENTS.md:237` |
| C++, ASIO, JUCE, audio, DSP, Neva Engine, ring buffer, WASAPI, hardware | Embedded Engineer | DevOps Engineer | `.ai/AGENTS.md:238` |
| test, coverage, PHPUnit, Vitest, Playwright, E2E, unit test, integration | QA Engineer | — | `.ai/AGENTS.md:239` |
| CI/CD, GitHub Actions, deploy, infrastructure, pipeline, monitoring, GitLeaks | DevOps Engineer | QA Engineer | `.ai/AGENTS.md:240` |
| vault, documentation, ADR, wiki-link, index, keys, brain | MO (vault-updater) | — | `.ai/AGENTS.md:241` |
| template, şablon, template usage, .templates | Tüm ajanlar (guardrail #16) | MO | `.ai/AGENTS.md:242` |

**Kural:** birden fazla grup eşleşirse birincil ajan önceliklidir; ikincil ajan **yalnız handover ile**
devreye girer (`Dosya: .ai/AGENTS.md · Satır: 330` zinciri). Motor indeksi bu tabloyu "11 keyword grubu"
olarak sayar (`Dosya: .ai/engine.md · Satır: 38`) — tabloda **9 satır** vardır; sayı farkı bilinen durumdur
(uyumsuzluk: `⚠️ VERIFICATION REQUIRED`).

## §7 PHP Kod Kanıtı — AGENT_MAP (routing iskeleti)

```php
// Dosya: shared/src/AI/AIOrchestrator.php · Satır: 26-38
private const AGENT_MAP = [
    'mo'         => ['domain' => 'coordination', 'layer' => 'coordination'],
    'backend'    => ['domain' => 'php-api',     'layer' => 'l2-routing'],
    'ui'         => ['domain' => 'frontend',    'layer' => 'l3-presentation'],
    'security'   => ['domain' => 'security',    'layer' => 'l1-security'],
    'data'       => ['domain' => 'database',    'layer' => 'l0-infrastructure'],
    'embedded'   => ['domain' => 'audio-dsp',   'layer' => 'l0-electronics'],
    'qa'         => ['domain' => 'testing',     'layer' => 'cross-cutting'],
    'devops'     => ['domain' => 'cicd',        'layer' => 'ci-cd'],
    'audio-hw'   => ['domain' => 'hardware',    'layer' => 'hw'],
    'dsp-fw'     => ['domain' => 'firmware',    'layer' => 'fw'],
    'win-sw'     => ['domain' => 'platform',    'layer' => 'plat'],
];
```

11 anahtar = vault §4 tablosunun 11 satırı ile birebir aynıdır (`Dosya: .ai/AGENTS.md · Satır: 77-89`).
`routeToAgent()` bilinmeyen ajanı **hata döndürerek** reddeder (`Dosya: shared/src/AI/AIOrchestrator.php · Satır: 127-133`).

## §8 Öncelik Seviyeleri (timeout + retry)

| Öncelik | Tanım | Timeout | Max Retry | Yanıt | Kanıt |
|---------|-------|---------|-----------|-------|-------|
| CRITICAL | Sistem durması, güvenlik açığı | 5s | 1 | Anlık | `.ai/AGENTS.md:320` |
| HIGH | Kritik işlev kaybı | 15s | 3 | 15s | `.ai/AGENTS.md:321` |
| MEDIUM | Normal geliştirme görevi | 30s | 3 | 30s | `.ai/AGENTS.md:322` |
| LOW | İyileştirme, optimizasyon | 60s | 2 | 60s | `.ai/AGENTS.md:323` |

Örnekler (motor indeksi): CRITICAL → CSRF/CSP ihlali, DB şema bozulması · HIGH → vault çelişkisi ·
MEDIUM → doküman revizyonu · LOW → YAML format düzeltmesi (`Dosya: .ai/engine.md · Satır: 109-114`).
Retry sınırı **her görev tipinde** geçerlidir; "Max 3 retry, sonra escalation" (`Dosya: .ai/AGENTS.md · Satır: 218`).

## §9 PHP Kod Kanıtı — TASK_TYPES (öncelik eşlemesi)

```php
// Dosya: shared/src/AI/AIOrchestrator.php · Satır: 40-49
private const TASK_TYPES = [
    'recommendation'    => ['agent' => 'backend', 'priority' => 'medium'],
    'analysis'          => ['agent' => 'embedded', 'priority' => 'high'],
    'eq-optimization'   => ['agent' => 'embedded', 'priority' => 'medium'],
    'hardware'          => ['agent' => 'audio-hw', 'priority' => 'high'],
    'fault'             => ['agent' => 'dsp-fw',   'priority' => 'critical'],
    'security-audit'    => ['agent' => 'security', 'priority' => 'critical'],
    'documentation'     => ['agent' => 'mo',       'priority' => 'low'],
    'testing'           => ['agent' => 'qa',       'priority' => 'medium'],
];
```

**Dürüstlük notu:** görev tanımında anılan `TASK_PRIORITY` **sabit adı dosyada YOKTUR** — öncelik,
`TASK_TYPES` satırlarındaki `priority` alanıdır (`Satır: 41-48`) ve `dispatchTask` çağrısında
parametre olarak ezilebilir (`Satır: 71` `string $priority = 'medium'`). Bu anayasada
`TASK_PRIORITY` sabiti **uydurulmaz**.

## §10 Görev Yaşam Döngüsü (queue durum makinesi)

| Durum | Anlam | Geçiş Kuralı | Kanıt |
|-------|-------|--------------|-------|
| Pending | Kuyrukta bekliyor | Dispatch algoritması sıraya alır | `.ai/engine.md:82` |
| Approved | Kullanıcı onayı alındı | Yalnız Human Mode onay kapısından geçer | `.ai/engine.md:83` |
| Running | Aktif çalışıyor | Aynı anda tek in_progress görev | `.ai/engine.md:84` |
| Completed | Tamamlandı + doğrulandı | Doğrulama çalıştırılmadan kapatılamaz | `.ai/engine.md:85` |
| Failed | Başarısız | Blocker notu + escalation tetiklenebilir | `.ai/engine.md:86` |

Kuyruk kuralları 4 maddedir: tek kilit · bağımlılık sırası (şema→API→UI→test) · atomik tamamlanma ·
onay kapısı (`Dosya: .ai/engine.md · Satır: 90-94`). Kod tarafında durum alanı `pending|running|completed|failed`'ır
(`Dosya: shared/src/AI/AIOrchestrator.php · Satır: 51` phpdoc, `:83` atama) — `Approved` yalnız vault
sürecinde vardır, kodda **yoktur**.

## §11 Handover Kuralları (onay zorunlu)

| Kural | Değer | Kanıt |
|-------|-------|-------|
| Onay zorunlu | Hedef agent onayı olmadan tamamlanamaz | `.ai/AGENTS.md:350` |
| Timeout | 30 saniye | `.ai/AGENTS.md:351` |
| Max retry | 3 | `.ai/AGENTS.md:352` |
| Red durumunda | MO devreye girer | `.ai/AGENTS.md:353` |
| Logging | Tüm handover'lar `log.md`'ye yazılır | `.ai/AGENTS.md:354` |

Zincir: `[Kaynak Agent] → [Handover Request] → [Hedef Agent] → [Onay/Red] → [Confirmation]`
(`Dosya: .ai/AGENTS.md · Satır: 330`). Motor indeksi handover mesajına **zorunlu 5 alan** ekler
(Context · Current State · Requested Action · Constraints · Expected Output) ve "eksik alanlı handover
L1 escalation üretir" der (`Dosya: .ai/engine.md · Satır: 150-173`). İki kayıt birlikte geçerlidir:
8 alanlık format (§12) + 5 alanlık içerik.

## §12 Handover Mesaj Formatı (8 alan)

| Alan | Değer | Kanıt |
|------|-------|-------|
| Konu | Görevin kısa açıklaması | `.ai/AGENTS.md:336` |
| Kaynak Agent | Adı | `.ai/AGENTS.md:337` |
| Hedef Agent | Adı | `.ai/AGENTS.md:338` |
| Öncelik | CRITICAL / HIGH / MEDIUM / LOW | `.ai/AGENTS.md:339` |
| Etkilenen Dosyalar | Dosya yolu listesi | `.ai/AGENTS.md:340` |
| İstek | Ne yapılması gerektiği | `.ai/AGENTS.md:341` |
| Onay Durumu | PENDING / APPROVED / REJECTED | `.ai/AGENTS.md:342` |
| Timestamp | `YYYY-MM-DD HH:MM:SS` (UTC) | `.ai/AGENTS.md:343` |

## §13 Handover Senaryoları (8 kayıt)

| Senaryo | Kaynak | Hedef | Öncelik | Kanıt |
|---------|--------|-------|---------|-------|
| Güvenlik açığı tespiti | Backend | Security | CRITICAL | `.ai/AGENTS.md:360` |
| DB schema değişikliği | Backend | Data | HIGH | `.ai/AGENTS.md:361` |
| Frontend test eksikliği | UI | QA | MEDIUM | `.ai/AGENTS.md:362` |
| CI/CD pipeline hatası | DevOps | QA | HIGH | `.ai/AGENTS.md:363` |
| Auth middleware değişikliği | Security | Backend | HIGH | `.ai/AGENTS.md:364` |
| Audio DSP optimizasyonu | Embedded | DevOps | MEDIUM | `.ai/AGENTS.md:365` |
| Vault doküman güncelleme | MO | vault-updater | LOW | `.ai/AGENTS.md:366` |
| Security audit | Security | QA | HIGH | `.ai/AGENTS.md:367` |

## §14 Eskalasyon Zinciri (L1 → L2 → L3 → İnsan)

| Seviye | Tanım | Timeout | Kanıt |
|--------|-------|---------|-------|
| L1 Domain Lead | Belirsizlik (teknik/kapsam) | 30s | `.ai/AGENTS.md:395` · `.ai/engine.md:179` |
| L2 Tech Lead | Çelişki (iki agent farklı karar) | 60s | `.ai/AGENTS.md:396` · `.ai/engine.md:180` |
| L3 Arch Lead | Kısıt ihlali (yetki/dosya erişimi) | 120s | `.ai/AGENTS.md:397` · `.ai/engine.md:181` |
| İnsan | 3 başarısız düzeltme / son çare | Anlık | `.ai/AGENTS.md:399` · `.ai/engine.md:182` |

Max retry **her seviyede 3** (`Dosya: .ai/AGENTS.md · Satır: 398`). Zincir grafiği:
`Level 1 (Domain Lead) → Level 2 (Tech Lead) → Level 3 (Arch Lead) → İnsan` (`Dosya: .ai/AGENTS.md · Satır: 374`).
Aynı dosyanın iki tanımı var: §10.1 senaryolar tablosu (9 satır, `.ai/AGENTS.md:379-389`) ve engine §6.3
(4 seviye, tetikleyici bazlı) — biri **nerede** (senaryo), diğeri **neyde** (tetikleyici) ayrımıyla uyumludur.

## §15 Eskalasyon Senaryoları (9 kayıt)

| Senaryo | Başlangıç | Hedef | Timeout | Kanıt |
|---------|-----------|-------|---------|-------|
| Agent aynı dosyayı değiştiremiyor | L1 | L2 | 30s | `.ai/AGENTS.md:381` |
| BCNF çelişkisi | L1 (Data) | L2 | 30s | `.ai/AGENTS.md:382` |
| CSRF/CSP uyumsuzluğu | L1 (Security) | L2 | 15s | `.ai/AGENTS.md:383` |
| ASIO cihaz kaybı | L1 (Embedded) | L2 | 30s | `.ai/AGENTS.md:384` |
| Test coverage %80 altı | L1 (QA) | L2 | 60s | `.ai/AGENTS.md:385` |
| Deployment başarısız | L1 (DevOps) | L2 | 30s | `.ai/AGENTS.md:386` |
| Mimari çelişki (ADR) | L2 | L3 | 60s | `.ai/AGENTS.md:387` |
| Güvenlik açığı | L2 | L3 | 15s | `.ai/AGENTS.md:388` |
| Sistem durması | L2 | İnsan | Anlık | `.ai/AGENTS.md:389` |

## §16 Health States (200/301/408/500/503)

| Durum | Kod | Açıklama | Tepki | Kanıt |
|-------|-----|----------|-------|-------|
| Healthy | 200 | Görev tamamlandı | Normal akış | `.ai/AGENTS.md:418` |
| Degraded | 301 | Yavaş yanıt (>15s) | Uyar, devam | `.ai/AGENTS.md:419` |
| Retry | 408 | Timeout, yeniden deneniyor | Max 3 retry | `.ai/AGENTS.md:420` |
| Failed | 500 | 3 retry başarısız | Queue reset + escalation | `.ai/AGENTS.md:421` |
| Dead | 503 | Yanıt yok | Derhal escalation | `.ai/AGENTS.md:422` |

Kodlar HTTP benzeridir ama **HTTP status değildir** — ajan sağlık etiketidir
(`Dosya: .ai/AGENTS.md · Satır: 414-422`, "Health States" tablosu).

## §17 Health Check Parametreleri + Akış

| Parametre | Değer | Kanıt |
|-----------|-------|-------|
| Timeout | 30 saniye | `.ai/AGENTS.md:408` |
| Max Retry | 3 | `.ai/AGENTS.md:409` |
| Check Interval | Her görev başında | `.ai/AGENTS.md:410` |
| Heartbeat | 10 saniye | `.ai/AGENTS.md:411` |

Akış: `Görev başlangıcı → Health check tetikle → Durum kontrolü → {Healthy devam · Degraded uyar ·
Retry max 3 · Failed queue reset+escalation · Dead derhal escalation}`
(`Dosya: .ai/AGENTS.md · Satır: 426-435`). Vault sağlık boyutu (GREEN/YELLOW/ORANGE/RED/DEAD) **farklı**
bir tablodur (`Dosya: .ai/engine.md · Satır: 211-217`) — ajan durumu (§16) ile karıştırılmaz.

## §18 Context Lock Kuralları

| Kural | Değer | Kanıt |
|-------|-------|-------|
| Kilitleme süresi | Max 30 saniye | `.ai/AGENTS.md:447` |
| Deadlock prevention | MO en eski kilidi kırar | `.ai/AGENTS.md:448` |
| Öncelik | CRITICAL > HIGH > MEDIUM > LOW | `.ai/AGENTS.md:449` |
| Logging | Lock acquire/release `log.md`'ye yazılır | `.ai/AGENTS.md:450` |

Akış: `Ajan dosyaya erişmek ister → Lock acquire (max 30s bekleme) → Başarılı: düzenle ·
Başarısız: kuyruk → öncelik sırası → Timeout: escalation`
(`Dosya: .ai/AGENTS.md · Satır: 454-460`). Kod kanıtı: **`AIOrchestrator.php` içinde lock uygulaması
YOKTUR** — kilit yalnız vault kuralıdır; kodlanmış eşzamanlılık koruması `⚠️ VERIFICATION REQUIRED`
(`Dosya: shared/src/AI/AIOrchestrator.php · Satır: 1-210` — lock/kilit referansı 0 satır, okundu 2026-10-17).

## §19 Deadlock Önleme (4 yöntem)

| Yöntem | Açıklama | Kanıt |
|--------|----------|-------|
| Timeout | Max 30s sonra lock serbest | `.ai/AGENTS.md:466` |
| Priority override | CRITICAL diğer kilidi kırar | `.ai/AGENTS.md:467` |
| MO intervention | MO en eski kilidi kırar | `.ai/AGENTS.md:468` |
| Queue reset | Tüm kilitler sıfırlanır | `.ai/AGENTS.md:469` |

Motor ek kuralı: çapraz kilitli iki `Running` görev → ikisi de `Pending`'e döner, kullanıcı
bilgilendirilir (`Dosya: .ai/engine.md · Satır: 325-327`). Kilitsiz dosya düzenleme **YASAK**;
lock dolu görev başka ajana devredilemez, önce serbest bırakılır (`Dosya: .ai/engine.md · Satır: 133`).

## §20 Güvenlik — Redaction ve CRITICAL Override

| Kural | Değer | Kanıt |
|-------|-------|-------|
| Sensitive data log'da | `[REDACTED]` ile maskeleme | `.ai/AGENTS.md:201` |
| Handover/redaction doğrulama | Validation adımında hassas veri redaction | `.ai/AGENTS.md:304` |
| Credential yüzeyi | `.env` yalnız Security Engineer | `.ai/AGENTS.md:106` |
| Figma anahtarı | yalnız `.ai/.env.figma`, `.md`'ye yazılmaz | `.ai/AGENTS.md:170` |

**CRITICAL override dikkati:** priority override CRITICAL'in başka bir ajana ait kilidi **kırmasına**
izin verir (`Dosya: .ai/AGENTS.md · Satır: 467`) → bu yetki **yalnız** §8 tablosundaki CRITICAL
tanımıyla (sistem durması / güvenlik açığı) sınırlıdır; MEDIUM/LOW bir görev kilit kıramaz.
Kırılan kilidin sahibi ve nedeni `log.md`'ye yazılır (`Dosya: .ai/AGENTS.md · Satır: 450`).

## §21 Zero-Hallucination (orkestrasyona uyarlanmış)

| # | Kural | Sonuç | Kanıt |
|---|-------|-------|-------|
| 1 | Var olmayan agent/route/URL uydurulmaz | `⚠️ VERIFICATION REQUIRED` | `.ai/AGENTS.md:42` |
| 2 | Bilinmeyen değer | `UNKNOWN` | `.ai/AGENTS.md:42` |
| 3 | Bilinmeyen class/API | `// ⚠️ VERIFICATION REQUIRED` yorumu | `.ai/AGENTS.md:202` |
| 4 | Hallüsinasyon tespiti | `VERIFICATION REQUIRED` etiketi zorunlu | `.ai/AGENTS.md:219` |
| 5 | Sayı iddiaları (9 grup, 8 senaryo …) | yalnız okuma künyesiyle yazılır | `.ai/AGENTS.md:280` |

Bu dosyadaki her sayı (11 agent, 9 routing grubu, 4 öncelik, 5 health state, 8 handover senaryosu,
9 eskalasyon senaryosu) `.ai/AGENTS.md` satır künyesiyle taşınmıştır; künyesiz sayı = ihlal.

## §22 Doğrulama Kapıları (dispatch'e bağlı)

| # | Kapı | Kriter | İhlal | Kanıt |
|---|------|--------|-------|-------|
| 1 | Pre-flight mockup | Mockup okunmadıysa kod yazılmaz | DUR | `.ai/AGENTS.md:275` |
| 2 | Şablon (Guardrail #16) | Şablon okunmadıysa üretilmez | DUR | `.ai/AGENTS.md:278` |
| 3 | Gate script | Repo **kökünden** çalıştırılır | `.ai/` içinden geçersiz | `.ai/AGENTS.md:279` |
| 4 | Varlık envanteri | İddia envanterle uyuşmuyorsa | DUR | `.ai/AGENTS.md:280` |
| 5 | Validation adımı | Format/domain/wiki-link/redaction | Görev kapanamaz | `.ai/AGENTS.md:300-304` |

## §23 PHP Kod Kanıtı — dispatch akışı

```php
// Dosya: shared/src/AI/AIOrchestrator.php · Satır: 71-97
public function dispatchTask(string $type, array $params, string $priority = 'medium'): string
{
    if (!isset(self::TASK_TYPES[$type])) {
        throw new \InvalidArgumentException("Unknown task type: {$type}");
    }
    $taskId = $this->generateTaskId();
    $taskConfig = self::TASK_TYPES[$type];
    $this->tasks[$taskId] = [
        'id' => $taskId, 'type' => $type, 'status' => 'pending',
        'priority' => $priority, 'agent' => $taskConfig['agent'], 'params' => $params,
        'result' => null, 'error' => null,
        'created_at' => date('Y-m-d H:i:s'), 'completed_at' => null,
    ];
    $this->executeTask($taskId);   // Priority weighted execution (yorum satırı: Satır 93)
    return $taskId;
}
```

Bilinmeyen görev tipi **exception** ile reddedilir (Satır: 73-75) — "uydurulan tip sessizce
yönlendirilmez". `executeTask` başarısızlıkta `failed` + `error` yazar ve exception yutup
task'ı kapatır (`Dosya: shared/src/AI/AIOrchestrator.php · Satır: 185-209`).

## §24 Repo Durumu — Kod vs Kural (dürüstlük tablosu)

| Bileşen | Vault Kuralı | Kod Durumu | Kanıt |
|---------|--------------|-----------|-------|
| Agent routing | 9 keyword grubu | AGENT_MAP 11 ajan — anahtar bazlı, keyword tabanlı **DEĞİL** | `AIOrchestrator.php:26-38` |
| Görev atama | 7 adım dispatch | dispatchTask + executeTask (tek çağrı, kuyruk **yok**) | `AIOrchestrator.php:71-97` |
| Öncelik | 4 seviye timeout/retry | `priority` alanı var; timeout/retry **kodlanmamış** | `AIOrchestrator.php:40-49` |
| Handover | onay zorunlu + 30s | Kodda handover metodu **YOK** | dosya tamamı (210 satır) |
| Health check | 5 durum | Kodda health metodu **YOK** | dosya tamamı |
| Context lock | 30s + deadlock | Kodda lock **YOK** | dosya tamamı |

**Sonuç:** orkestrasyon kuralları **vault'ta IMPLEMENTED, kodda PLANNED/iskelettir** — bu anayasada
"orkestrasyon motoru çalışıyor" ifadesi **geçmez**; geçerse ihlaldir.

## §25 Web Kaynakları — Endüstri Karşılaştırması (doğrulandı 2026-10-17)

| # | Kaynak | Ne doğrular | Erişim |
|---|--------|-------------|--------|
| 1 | LangGraph — Workflows and agents (docs.langchain.com) | Orchestrator-worker: orkestratör alt görevleri dağıtır, çıktıyı sentezler; `Send` API ile dinamik worker | websearch 2026-10-17 |
| 2 | langgraph-supervisor (github.com/langchain-ai/langgraph-supervisor-py) | Supervisor + handoff tool mekanizması; `output_mode: last_message/full_history` | websearch 2026-10-17 |
| 3 | AutoGen — Group Chat (microsoft.github.io/autogen/stable/.../group-chat.html) | GroupChatManager sırayı yönetir; round-robin veya LLM selector; `RequestToSpeak` akışı | websearch 2026-10-17 |

**CoreMusic karşılığı:** MO ≈ supervisor/group-chat manager; handover ≈ handoff tool;
routing ≈ selector (bugün **tablo**, upstream'te LLM selector). Fark: CoreMusic'te onay/kilit
vault kuralıdır, framework primitive'i **değildir**. ⚠️ VERIFICATION REQUIRED: bu 3 kaynak
CoreMusic koduna **bağlı değildir** — yalnız kavramsal doğrulamadır.

## §26 İllüstratif Örnek — Python (priority queue)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# upstream docs (heapq) referansı; repo'da karşılığı YOK
import heapq, itertools

class TaskQueue:
    def __init__(self):
        self._h, self._seq = [], itertools.count()
    def push(self, priority: int, task_id: str):        # CRITICAL=0 > HIGH > MEDIUM > LOW=3
        heapq.heappush(self._h, (priority, next(self._seq), task_id))
    def pop(self) -> str:
        return heapq.heappop(self._h)[2]                # eşit öncelikte FIFO (seq)
```

Vault kuralı: öncelik `CRITICAL > HIGH > MEDIUM > LOW` (`Dosya: .ai/AGENTS.md · Satır: 449`);
eşit öncelikte sıralı bekleme (`Dosya: .ai/engine.md · Satır: 326`).

## §27 İllüstratif Örnek — TypeScript (retry with backoff)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```ts
// max 3 retry kuralının illüstrasyonu — repo'da karşılığı YOK
export async function withRetry<T>(fn: () => Promise<T>, maxRetry = 3): Promise<T> {
  let lastErr: unknown;
  for (let attempt = 1; attempt <= maxRetry; attempt++) {
    try { return await fn(); }
    catch (e) {
      lastErr = e;
      if (attempt === maxRetry) break;
      await new Promise(r => setTimeout(r, 2 ** attempt * 250));  // backoff
    }
  }
  throw lastErr;   // Failed → queue reset + escalation (§16)
}
```

Kural karşılığı: "Max retry 3, sonra escalation" (`Dosya: .ai/AGENTS.md · Satır: 352`, `:218`).

## §28 İllüstratif Örnek — Go (round-robin dispatcher)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
// round-robin speaker seçimi — upstream docs (AutoGen RoundRobinGroupChat) referansı
type Dispatcher struct{ agents []string; idx int }

func (d *Dispatcher) Next() string {          // eşit öncelikli gruplarda sıralı dağıtım
    a := d.agents[d.idx%len(d.agents)]
    d.idx++
    return a
}
func (d *Dispatcher) RouteByKeyword(kw string, table map[string]string) string {
    if a, ok := table[kw]; ok { return a }    // §6 tablosunun tek eşleşmesi
    return "mo"                               // belirsizlik → MO (L1)
}
```

İlgili kural: "Belirsizlik → L1 → Uncertainty flag + kullanıcıya seçenek sun"
(`Dosya: .ai/engine.md · Satır: 179`).

## §29 İllüstratif Örnek — Rust (circuit breaker)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```rust
// ajan sağlık kapısı — upstream docs referansı; repo'da karşılığı YOK
pub enum Health { Healthy, Degraded, Retry, Failed, Dead }

pub struct Breaker { failures: u8, max_retry: u8 }

impl Breaker {
    pub fn on_result(&mut self, ok: bool, elapsed_ms: u64) -> Health {
        if ok && elapsed_ms <= 15_000 { return Health::Healthy }        // 200
        if ok { return Health::Degraded }                               // 301 (>15s)
        self.failures += 1;
        if self.failures >= self.max_retry { Health::Failed }           // 500 → reset+escalation
        else { Health::Retry }                                          // 408
    }
}
```

5 durum tablosu: `Dosya: .ai/AGENTS.md · Satır: 416-422`; heartbeat 10s (`Satır: 411`).

## §30 İllüstratif Örnek — C# (context lock manager)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```csharp
// 30s kilit + CRITICAL override — upstream docs referansı; repo kodu YOK (§18)
public sealed class ContextLock
{
    private readonly Dictionary<string,(string Owner,int Priority,DateTime Exp)> _locks = new();
    public bool TryAcquire(string file, string owner, int prio, TimeSpan max)
    {
        if (_locks.TryGetValue(file, out var l) && l.Exp > DateTime.UtcNow && l.Prio >= prio)
            return false;                                   // dolu → kuyruk (§18)
        _locks[file] = (owner, prio, DateTime.UtcNow.Add(max));   // max 30s
        return true;                                        // CRITICAL (prio 0) kıracaksa §20
    }
    public void ForceBreakOldest(string byMo) { /* MO en eski kilidi kırar — §19 */ }
}
```

Kural: kilitleme max 30s · MO en eskiyi kırar · acquire/release `log.md`'ye
(`Dosya: .ai/AGENTS.md · Satır: 447-450`).

## §31 Açık Sorular

| # | Soru | Öncelik | Durum / Kanıt |
|---|------|---------|----------------|
| 1 | engine.md "11 keyword grubu" ↔ AGENTS.md 9 satır farkı | HIGH | `⚠️ VERIFICATION REQUIRED` (`engine.md:38` ↔ `AGENTS.md:232-242`) |
| 2 | Context lock kodda ne zaman implemente edilecek? | HIGH | `UNKNOWN` — kodda lock 0 satır (`AIOrchestrator.php` okundu 2026-10-17) |
| 3 | Handover/health metotları arayüze eklenecek mi? | HIGH | `⚠️ VERIFICATION REQUIRED` — `AIOrchestratorInterface` kapsamı doğrulanmadı |
| 4 | `TASK_PRIORITY` sabiti isteniyor mu (task promptu)? | MEDIUM | Kodda yok (§9) — istek netleştirilmeli |
| 5 | §16 health kodları HTTP status ile çakışır mı? | LOW | Dokümante edilmiş ayrım yeterli mi karar yok |
| 6 | 12 terim `glossary.md`'ye ne zaman taşınacak? | LOW | `.ai/AGENTS.md:705` stub uyarısı açık |

## §32 Kaynaklar

| # | Kaynak | Yol | Kullanım |
|---|--------|-----|----------|
| 1 | Agent Registry & Coordination Protocol (SSOT) | `.ai/AGENTS.md` (v22.2.0, okundu 2026-10-17) | §4-§22 |
| 2 | Orchestration Engine (motor indeksi) | `.ai/engine.md` (v21.1.0, okundu 2026-10-17) | §1, §10, §14, §17, §19 |
| 3 | AI Orchestrator (kod kanıtı) | `shared/src/AI/AIOrchestrator.php` (210 satır, salt-okunur) | §7, §9, §10, §23, §24 |
| 4 | Anayasa şablonu (yapı referansı) | `.ai/architecture/K015-AI/rag/rag-anayasa.md` | H1 + § + tablo iskeleti |
| 5 | LangGraph docs (web) | docs.langchain.com — workflows-agents · langgraph-supervisor | §25-§27 |
| 6 | AutoGen docs (web) | microsoft.github.io/autogen/stable — group-chat | §25, §28 |

## §33 Ölçüm / Gate Notu

| # | Eşik | Araç | Not |
|---|------|------|-----|
| 1 | ≥500 tam satır | `[IO.File]::ReadAllLines(..., UTF8).Count` | 500-520 bandı |
| 2 | FM 10 alan | gözden geçirme | `title`/`parent` anahtarı YOK |
| 3 | Authority son satır | gözden geçirme | birebir son satır |
| 4 | Kod bloğu | PHP 3 (repo kanıtı) + 5 dil illüstratif | illüstratif bloklarda uyarı etiketi zorunlu |
| 5 | Web kaynak | 3 resmi kaynak | websearch 2026-10-17, uydurma URL yok |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode