---
title: "vault: architecture — mimari katman kökü"
type: dokuman
created: 2026-10-06
updated: 2026-10-06
sources: [arch-katman]
tags: [mimari, katman, agent, workflow]
---

# Mimari Katman Kökü (5 dosya)

`.ai/raw/architecture/` — mimari katmanın yönlendirme dosyaları.

| Dosya | İçerik |
|---|---|
| AGENTS.md | Mimari katman agent hiyerarşisi |
| CLAUDE.md | Mimari katman çalışma kuralları |
| INDEX.md | Navigasyon kataloğu |
| README.md | Vault genel bakış |
| WORKFLOW.md | Mimari iş akışı |

## İlgili Sayfalar

- [[arch-k0-isletim-sistemi]] — aynı ağaçtaki teknik derinlik
- [[vault-workflow]] · [[vault-agents]] — .ai/raw kök sürümleri

---

## Ham Veri — `.ai/raw/architecture/` (tam metin, satır içine gömülü)

### AGENTS.md

---
title: "architecture — AGENTS.md — Mimari Katman Agent Hiyerarşisi"
type: guide
category: architecture
version: 1.0.0
status: active
updated: 2026-10-06
authority: "SSOT — .ai/architecture/AGENTS.md (registry SSOT: .ai/AGENTS.md v22.0.8)"
---

# architecture — AGENTS.md — Agent Hiyerarşisi, Sahiplik ve Kanıt Kuralları

> **Otorite:** agent registry'nin tek SSOT'u `.ai/AGENTS.md` (v22.0.8)'dir. Bu dosya **bu vault'a özgü sahiplik/akış katmanıdır**; routing, handover, escalation, öncelik tabloları burada tekrar tanımlanmaz (çelişkide `.ai/AGENTS.md` kazanır).

## §1 Amaç

Bu vault'ta doküman üreten/denetleyen rolleri, sorumluluk sınırlarını ve kanıt kurallarını tanımlamak.

## §2 Kapsam

`CLAUDE.md` §2 ile aynı: `.ai/architecture/**` (5 kök dosya + `k0-isletim-sistemi/` 14 md). Kapsam dışı katman ve komşu vault için `CLAUDE.md` §4.9.

## §3 Mimari — Agent Hiyerarşisi

```text
Master Orchestrator (mo)  — kapsam onayı, commit, log.md
        │
   ┌────┴─────────────────────────────┐
   │ uzman agent'lar (görev tipine göre) │
   └────┬─────────────────────────────┘
        │
   QA / code-reviewer (denetim) → VERIFY → kullanıcı raporu
```

**Registry'deki 11 agent** (`.ai/AGENTS.md` §4 — kaynak tablo): `mo` · `backend` · `ui` · `security` · `data` · `embedded` · `qa` · `devops` · `audio-hw` · `dsp-fw` · `win-sw`.

## §4 Responsibilities — K0 Sahipliği (routing kanıtına dayalı)

| Vault grubu | Birincil agent | Dayanak (`.ai/AGENTS.md` §6 keyword routing) |
|---|---|---|
| `01-platformlar/` (windows/linux/rpi5/macos) | `embedded` (birincil) · `win-sw` (Windows yüzeyi) | "C++, ASIO, JUCE, audio, DSP, ring buffer, WASAPI, hardware" → embedded; WASAPI/COM/platform → win-sw |
| `02-cekirdek-mekanizmalar/` (memory, IPC, syscall, threading) | `embedded` · `data` (`architecture/k0-*.md` okuma listesi §24.3'te data agent'ındır) | §24.3 "Data → `architecture/k0-isletim-sistemi/*.md`" |
| `03-guvenlik-izolasyon/` (container, process isolation) | `security` · `devops` | OWASP/auth/encryption → security; Docker/deploy/infra → devops |
| `04-tasinabilirlik/` (cross-platform API) | `embedded` · `backend` (gRPC/API yüzeyi) | API/endpoint/middleware → backend |
| Kök 5 dosya (CLAUDE/AGENTS/WORKFLOW/INDEX/README), log, index | `mo` (vault-updater) | "vault, documentation, ADR, wiki-link, index" → MO |
| Denetim (frontmatter, wiki-link, mojibake) | `qa` | "test, coverage" → QA |

## §5 Scope — Yetki Sınırı

1. **Yazma yetkisi:** yalnız `.ai/architecture/**` + `.ai/log.md` (append).
2. **Okuma serbest:** `.ai/architecture.old/k000-* … k005`, `_backup/arch-2026-10-06_1057/**`, `.ai/.templates/**`.
3. **Yasak:** komşu vault'a yazma, frozen ADR değişimi, dosya adı taşıma/silme, onaysız `k1…` üretimi.
4. **Commit:** subagent **atmaz** — orkestratöre aittir.

## §6 Research Rules

1. Her teknik iddia için kaynak satırı: gerçek dosya yolu + satır, veya URL + erişim tarihi.
2. Web araştırması `exa.web_search_exa` / `exa.web_fetch_exa` ile yapılır; **web > vault değildir** (kanıt katmanı altta kalır).
3. RFC/yama durumundaki bulgular "merged" diye yazılmaz; durum sütununda `RFC` / `PATCH` yazılır.
4. Araştırma yapılmayan konu `UNKNOWN` olarak kalır — araştırılmamış gibi **görünmez**.

## §7 Validation Rules

1. Yazım sonrası: `vault-utf8-writer verify` (BOM yok, mojibake 0) + `scan` (dirty=0).
2. Yapısal: frontmatter 7/7 · tek H1 · wiki-link hedefi var.
3. İçerik: satır-sayı/klasör-sayı iddiaları disk sayımıyla eşleşmeli.
4. Hata → `.ai/.rules/error-recovery.md` → düzelt → tekrar kontrol.

## §8 Conflict Rules (Çakışma)

| Senaryo | Çözüm |
|---|---|
| Bu vault ile komşu vault arasında farklı iddia | Kanıt gücü karşılaştırılır (birincil kaynak > ikincil; taze > eski; bağımsız ölçüm > yazar beyanı). Eşitse → `⚠️ VERIFICATION REQUIRED` + `.ai/AGENTS.md` §6.1 **Expert > Senior > Junior** hiyerarşisi uygulanır (oy çokluğu yoktur) |
| Kök 5 dosya içi çelişki | `CLAUDE.md` > `AGENTS.md` > `WORKFLOW.md` > `INDEX.md` > `README.md` |
| Vault ile bu vault çelişir | `.ai/CLAUDE.md` kazanır (SSOT) |

## §9 Evidence Rules (Kanıt)

1. **Kanıt zorunlu:** her öneri/iddia **gerekçe + dosya/satır veya URL** taşır; kaynaksız öneri sayılmaz.
2. **Seviye-kurallı:** yalnız tek seviyeli (ör. yalnız Junior) karar **bağlayıcı değildir**; mimari/katman/sayım kararlarında en az 1 Expert + 1 Senior + 1 Junior görüşü zorunludur (`.ai/AGENTS.md` §6.1).
3. **Sayım uydurması yasak:** başlıkta/§/tablo sayısı diskten sayılır.
4. Bulunamayan kanıt → `⚠️ VERIFICATION REQUIRED` (uydurulmaz).

## §10 Workflow — Görev Akışı

`CLAUDE.md` §5'teki `READ → … → DOCUMENT` zinciri uygulanır; her görev sonunda:

1. `log.md` append kaydı (append-only),
2. `verify` + `scan`,
3. Kapsam dışı dokunma yoksa rapor.

## §11 Doğrulama

- [ ] Agent atamaları `.ai/AGENTS.md` §6 routing tablosuyla çelişmiyor
- [ ] Her iddia kanıt satırı taşıyor
- [ ] Çakışma durumunda Expert+Senior+Junior kuralı işletildi
- [ ] Yazım yalnız `vault-utf8-writer` ile

## §12 Referanslar

[[CLAUDE]] · [[WORKFLOW]] · [[INDEX]] · [[README]] · [[k0-isletim-sistemi/index]] · `.ai/AGENTS.md` (registry SSOT v22.0.8) · `.ai/WORKFLOW.md` · `.ai/.rules/error-recovery.md`

### CLAUDE.md

---
title: "architecture — CLAUDE.md — Mimari Katman Çalışma Kuralları"
type: guide
category: architecture
version: 1.0.0
status: active
updated: 2026-10-06
authority: "SSOT — .ai/architecture/CLAUDE.md"
---

# architecture — CLAUDE.md — Mimari Katman Çalışma Kuralları

> **Bu vault-içi otorite sırası:** `CLAUDE.md` → `AGENTS.md` → `WORKFLOW.md` → `INDEX.md` → `README.md`. Çelişkide üstteki kazanır. Vault-içi **genel** otorite zinciri `.ai/CLAUDE.md` §2.1'dedir (CLAUDE > AGENTS > WORKFLOW > brain > index > templates) — bu dosya oradan **türetilmiştir**, ikinci bir anayasa değildir.

## §1 Amaç

`.ai/architecture/` altında katman dokümantasyonunun nasıl üretildiğini, doğrulandığını ve güncellendiğini tanımlar. Amaç: tek SSOT, çelişkisiz çapraz referans, kanıtsız iddia üretmemek.

## §2 Kapsam

| Kapsam içi | Kapsam dışı (dokunma) |
|---|---|
| `.ai/architecture/` kök 5 dosya: `CLAUDE.md` · `AGENTS.md` · `WORKFLOW.md` · `INDEX.md` · `README.md` | `.ai/architecture.old/k000-* … k079-*` ve altındaki tüm dosyalar — **salt-okunur komşu vault** |
| `k0-isletim-sistemi/` → 4 grup + `README.md` + `index.md` = **14 md** (2026-10-06 disk sayımı) | DAC/ADC (k006), donanım, CSS/frontend, Figma, server katmanları |
| | `k1…` ve sonrası katman üretimi (**onaysız yasak**) |

**Disk kanıtı (2026-10-06):** `.ai/architecture/` kökünde 5 `.md` (bu dosyalar) + 1 dizin (`k0-isletim-sistemi/`) vardır; `k1-*` dizini **yoktur**.

## §3 Mimari

```text
.ai/architecture/
├── CLAUDE.md      ← bu dosya: çalışma kuralları (§4-§5)
├── AGENTS.md      ← agent hiyerarşisi, sahiplik, çakışma/evidence kuralları
├── WORKFLOW.md    ← mimari iş akışı zinciri + kapılar
├── INDEX.md       ← navigasyon (tüm dosyalar)
├── README.md      ← genel bakış
└── k0-isletim-sistemi/
    ├── README.md      (katman kataloğu + Kanıt Kataloğu)
    ├── index.md       (grup dizini + yedek gövde 1:1 korunum)
    ├── 01-platformlar/        (5 md)
    ├── 02-cekirdek-mekanizmalar/ (4 md)
    ├── 03-guvenlik-izolasyon/ (2 md)
    └── 04-tasinabilirlik/     (1 md)
```

**Kaynak:** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/` (14 dosya, salt-okunur yedek).

## §4 Kurallar (Guardrail)

1. **Zero-Hallucination:** repo, dosya, URL, API, sürüm, benchmark uydurulmaz. Doğrulanamayan → `⚠️ VERIFICATION REQUIRED`; bilinmeyen → `UNKNOWN`. Web kanıtı vault içeriğinin **altındadır**.
2. **Frontmatter 7 zorunlu alan:** `title` · `type` · `category` · `version` · `status` · `updated` · `authority`. Her dosyada tek H1.
3. **Wiki-link formatı:** `[[relative/path/to/file]]` — code-fence içi örnekler link sayılmaz.
4. **Yazım arayüzü:** vault dosyalarına yazım **yalnız** `node .ai/scripts/vault-utf8-writer.mjs` ile (`append` / `insert-before-marker` / `write` / `verify` / `scan`). PowerShell dosya yazma cmdlet'leri yasaktır.
5. **`log.md` append-only** — geçmiş satıra dokunulmaz.
6. **Frozen ADR'ler değişmez**; yeni ADR numarası **088+** (onay kapısı: `.ai/.decisions` süreci).
7. **In-place refactoring:** dosya adı/klasör adı **onaysız değişmez**; taşıma/silme yalnızca raporlanır.
8. **Template-first (Guardrail #16):** yeni `.md` üretmeden önce `.ai/.templates/documentation/docs-md-template.md` §1-§7 iskeleti okunur (`claude-md-template` / `katman-readme-template` bu vault için ayrıca referanstır).
9. **Komşu vault yazma yasağı:** `.ai/architecture.old/k000-* … k005` içeriği **yalnız okunur**; eksik bilgi bu vault'a kopya içerik olarak eklenir, kaynak dosya silinmez/taşınmaz.
10. **REDACTED:** parola, token, API anahtarı hiçbir vault dosyasına yazılmaz.

## §5 Workflow (Architecture Operating Rules)

```text
READ → ANALYZE → RESEARCH → VALIDATE → PLAN → APPROVE → IMPLEMENT → TEST → DOCUMENT
```

| Kapı | Şart | Onaysız geçer mi? |
|---|---|---|
| RESEARCH → VALIDATE | her iddia disk/web kanıtıyla eşleşti | Hayır |
| PLAN → APPROVE | kapsam + dosya listesi kullanıcıya sunuldu | Hayır |
| IMPLEMENT → TEST | `verify` (BOM/mojibake) + `scan` (dirty=0) | Hayır |
| TEST → DOCUMENT | `log.md`'ye append kaydı | Hayır |

- **Kapsam dışı iş sinyali** (CSS/Figma/server/k1) → DUR + rapor.
- **Çelişki** (iki dosya farklı iddia) → kanıt gücünü karşılaştır; eşitse `⚠️ VERIFICATION REQUIRED` + üst otorite kararı (§ başlığı).

## §6 Doğrulama (checklist)

- [ ] Frontmatter 7/7 + tek H1
- [ ] Wiki-linkler hedefe gidiyor (inline-code örnekleri hariç)
- [ ] `vault-utf8-writer verify` → `hasBom:false`, `mojibake:0`
- [ ] `vault-utf8-writer scan` → `dirty:0`
- [ ] Yeni/Değişen dosya `log.md`'ye append edildi
- [ ] Kapsam sınırı ihlali yok (k000+ dosyalarına yazma yok)

## §7 Referanslar

| Bağlantı | Amaç |
|---|---|
| [[AGENTS]] | Agent hiyerarşisi, sahiplik, research/validation/conflict/evidence kuralları |
| [[WORKFLOW]] | Mimari iş akışı zinciri ve kapıları |
| [[INDEX]] | Dosya navigasyonu |
| [[README]] | Genel bakış |
| [[k0-isletim-sistemi/index]] | K0 katmanı dizini |
| `.ai/CLAUDE.md` · `.ai/AGENTS.md` · `.ai/WORKFLOW.md` | Vault geneli otorite (üst zincir) |
| `.ai/.templates/documentation/docs-md-template.md` | Şablon-önce kuralı (Guardrail #16) |
| `.ai/scripts/vault-utf8-writer.mjs` | Tek yazma arayüzü |

### INDEX.md

---
title: "architecture — INDEX.md — Navigasyon Kataloğu"
type: architecture-index
category: architecture
version: 1.0.0
status: active
updated: 2026-10-06
authority: "SSOT — .ai/architecture/INDEX.md"
---

# architecture — Navigasyon Kataloğu

**Kapsam:** `.ai/architecture/` · **İçerik (2026-10-06 disk sayımı):** kök 5 md + 1 katman (`k0-isletim-sistemi/`, 14 md) = **19 md** · `k1…` dizini **yok**.

## §1 Kök Dosyalar (5)

| # | Dosya | Görev |
|---|---|---|
| 1 | [[CLAUDE]] | Çalışma kuralları, guardrail, onay kapıları (§4-§5) |
| 2 | [[AGENTS]] | Agent hiyerarşisi, K0 sahipliği, research/validation/conflict/evidence |
| 3 | [[WORKFLOW]] | Mimari iş akışı zinciri + G1-G6 kapıları |
| 4 | [[INDEX]] | Bu dosya — navigasyon |
| 5 | [[README]] | Genel bakış ve kullanım |

## §2 Katman — `k0-isletim-sistemi/` (14 md)

| Grup | Dosya | Konu |
|---|---|---|
| kök | [[k0-isletim-sistemi/README]] | Katman kataloğu, bileşen haritası, Kanıt Kataloğu |
| kök | [[k0-isletim-sistemi/index]] | Grup dizini + yedek index.md gövdesi (1:1) |
| 01-platformlar | [[k0-isletim-sistemi/01-platformlar/windows-core]] | NT çekirdek katmanı, process/memory/thread/registry/Job Objects |
| 01-platformlar | [[k0-isletim-sistemi/01-platformlar/windows-api]] | ASIO 2.3, WASAPI, COM, Windows threading/memory |
| 01-platformlar | [[k0-isletim-sistemi/01-platformlar/linux-core]] | cgroups, namespaces, systemd, epoll, seccomp, capabilities |
| 01-platformlar | [[k0-isletim-sistemi/01-platformlar/rpi5-core]] | GPIO, DMA, PWM, I2S (RPi5 gömülü platform) |
| 01-platformlar | [[k0-isletim-sistemi/01-platformlar/macos-core]] | GCD, XPC, Core Foundation, IOKit, App Sandbox, Hardened Runtime |
| 02-cekirdek-mekanizmalar | [[k0-isletim-sistemi/02-cekirdek-mekanizmalar/memory-management]] | Virtual memory, page tables, pool, slab, ring/double buffer |
| 02-cekirdek-mekanizmalar | [[k0-isletim-sistemi/02-cekirdek-mekanizmalar/system-calls]] | POSIX syscall, Windows NT API, io_uring, epoll, kqueue |
| 02-cekirdek-mekanizmalar | [[k0-isletim-sistemi/02-cekirdek-mekanizmalar/threading-model]] | Thread pools, lock-free, atomics, condvar, mutex hiyerarşisi |
| 02-cekirdek-mekanizmalar | [[k0-isletim-sistemi/02-cekirdek-mekanizmalar/ipc-mekanizmalari]] | UDS, named pipes, shared memory, message queues, gRPC |
| 03-guvenlik-izolasyon | [[k0-isletim-sistemi/03-guvenlik-izolasyon/process-isolation]] | Sandbox, capability dropping, chroot, namespaces, seccomp-bpf |
| 03-guvenlik-izolasyon | [[k0-isletim-sistemi/03-guvenlik-izolasyon/container-runtime]] | Docker, containerd, Kubernetes, compose, health check |
| 04-tasinabilirlik | [[k0-isletim-sistemi/04-tasinabilirlik/cross-platform-api]] | pthreads, SDL2, libuv, libevent, Boost.Asio, tokio |

## §3 Kaynaklar (salt-okunur)

| Kaynak | Kullanım |
|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/` | 14 dosyalık içerik kaynağı (içerik kaybı: 0 — doğrulandı 2026-10-06) |
| `.ai/architecture.old/k000-windows-core/ … k005-bellek-container/` | Komşu vault — K0 ile ilgili içerik **okuma** amacıyla (bkz. [[k0-isletim-sistemi/index]] §8) |
| `.ai/.templates/documentation/` | Şablon-önce kuralı (Guardrail #16) |
| `.ai/scripts/vault-utf8-writer.mjs` | Tek yazma arayüzü |

## §4 Okuma Sırası

`README` (genel bakış) → `CLAUDE` (kurallar) → `k0-isletim-sistemi/README` (katman kataloğu) → `k0-isletim-sistemi/index` (gruplar) → ilgili detay dosyası.

## §5 Doğrulama

- [ ] 19 hedefin tamamı diskte mevcut (2026-10-06 sayımı)
- [ ] Frontmatter 7/7 · tek H1
- [ ] Wiki-link hedefleri var (code-fence içi örnek link sayılmaz)
- [ ] `verify` BOM=0/mojibake=0 · `scan` dirty=0

### README.md

---
title: "architecture — README.md — Mimari Katman Vault'u Genel Bakış"
type: guide
category: architecture
version: 1.0.0
status: active
updated: 2026-10-06
authority: "SSOT — .ai/architecture/README.md"
---

# architecture — Mimari Katman Vault'u (Genel Bakış)

## §1 Amaç

`.ai/architecture/`, **katman bazlı mimari dokümantasyonun** yeniden yapılandırıldığı vault alanıdır. Bu README giriş noktasıdır: ne var, nasıl okunur, nasıl güncellenir.

## §2 Kapsam

| Var (2026-10-06) | Yok / yasak |
|---|---|
| Kök 5 governance dosyası: `CLAUDE` · `AGENTS` · `WORKFLOW` · `INDEX` · `README` | `k1…` ve sonrası katmanlar (onaysız üretim yasak) |
| **1** katman: `k0-isletim-sistemi/` → 4 grup + 12 detay + `README` + `index` = **14 md** | `.ai/architecture.old/k000-* … k079-*` dosyalarına yazma (salt-okunur) |
| | DAC/ADC (k006), donanım, CSS/frontend, Figma, server katmanı kapsam dışı |

## §3 Mimari — Gruplar

```text
k0-isletim-sistemi/                 (işletim sistemi katmanı — K0)
├── 01-platformlar/          5 md  windows-core · windows-api · linux-core · rpi5-core · macos-core
├── 02-cekirdek-mekanizmalar 4 md  system-calls · threading-model · ipc-mekanizmalari · memory-management
├── 03-guvenlik-izolasyon     2 md  process-isolation · container-runtime
└── 04-tasinabilirlik         1 md  cross-platform-api
```

**İçerik kaynağı:** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/` (14 dosya) — taşımada içerik kaybı **0** (2026-10-06 doğrulaması: frontmatter/H1 normalizasyonu dışında birebir korunum).

## §4 Kurallar (özet — tam metin `CLAUDE.md` §4)

1. Zero-Hallucination: kanıtsız iddia yok → `⚠️ VERIFICATION REQUIRED` / `UNKNOWN`.
2. Frontmatter 7 alan + tek H1 + `[[relative/path]]` wiki-link.
3. Yazım yalnız `node .ai/scripts/vault-utf8-writer.mjs`; `log.md` append-only.
4. Komşu vault (`k000-* … k005`) salt-okunur; frozen ADR değişmez; yeni ADR 088+.
5. Dosya adı/klasör taşıma onaysız yasak (in-place refactoring).

## §5 Workflow

`WORKFLOW.md` §3 zinciri: `PROJECT DISCOVERY → … → INDEX UPDATE` (kapılar G1-G6).

## §6 Doğrulama

- [ ] `INDEX.md` listesi ile disk eşleşiyor (19 md)
- [ ] `verify` + `scan` temiz · frontmatter 7/7 · tek H1
- [ ] Değişiklikler `log.md`'ye append edildi
- [ ] Kapsam dışı yüzeye dokunulmadı · **commit atılmadı** (orkestratöre ait)

## §7 Referanslar

[[CLAUDE]] · [[AGENTS]] · [[WORKFLOW]] · [[INDEX]] · [[k0-isletim-sistemi/README]] · [[k0-isletim-sistemi/index]] · `.ai/CLAUDE.md` · `.ai/WORKFLOW.md` · `.ai/.templates/documentation/docs-md-template.md`

### WORKFLOW.md

---
title: "architecture — WORKFLOW.md — Mimari İş Akışı"
type: guide
category: architecture
version: 1.0.0
status: active
updated: 2026-10-06
authority: "SSOT — .ai/architecture/WORKFLOW.md"
---

# architecture — WORKFLOW.md — Mimari İş Akışı Zinciri

> Bu zincir `.ai/architecture/` vault'u için geçerlidir; vault geneli süreç `.ai/WORKFLOW.md`'dedir (çelişkide o kazanır).

## §1 Amaç

Bir katman dokümanının keşiften indeks güncellemesine kadar izlediği deterministik adımları ve zorunlu kapıları tanımlamak.

## §2 Kapsam

`.ai/architecture/**` üretim/güncelleme işleri. Kapsam dışı: kod değişikliği, CSS/Figma/server katmanları, `k1…` üretimi (onay gerektirir).

## §3 Mimari — Zincir

```text
PROJECT DISCOVERY
   ↓
EXISTING ARCHITECTURE   (disk taraması: .ai/architecture + komşu .ai/architecture.old k000-k005)
   ↓
OLD ARCHITECTURE        (_backup/arch-2026-10-06_1057/** salt-okunur)
   ↓
AI VAULT                (.ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md — ihtiyaç anında @)
   ↓
REQUIREMENTS            (kullanıcı kapsamı + onay kapıları)
   ↓
WEB RESEARCH            (exa: güncel teknik doğrulama)
   ↓
SOURCE VALIDATION       (kanıt gücü karşılaştırması → çelişki/UNKNOWN işaretleme)
   ↓
DOMAIN ANALYSIS         (konu alanı sınırları)
   ↓
LAYER ANALYSIS          (K katmanı sınırı — DAC/ADC, CSS, frontend'e taşma yok)
   ↓
DEPENDENCY GRAPH        (alt/üst katman okları)
   ↓
ARCHITECTURE PLAN       (dosya listesi + gruplama)
   ↓
AGENT REVIEW            (Expert+Senior+Junior — .ai/AGENTS.md §6.1)
   ↓
ADR                     (gerekirse; frozen 001-037 dokunulmaz, yeni 088+)
   ↓
MULTI-MD DOCUMENTATION  (§1-§7 şablon iskeleti, frontmatter 7, wiki-link)
   ↓
VALIDATION              (verify + scan + frontmatter/H1/wiki-link + kapsam kontrolü)
   ↓
INDEX UPDATE            (INDEX.md · k0 index/README · log.md append)
```

## §4 Kurallar — Zorunlu Kapılar

| # | Kapı | Geçme şartı | İhlalde |
|---|---|---|---|
| G1 | REQUIREMENTS → WEB RESEARCH | kapsam yazılı onaylandı | DUR, kullanıcıya sor |
| G2 | SOURCE VALIDATION | her iddia kanıt satırı taşıyor | `⚠️ VERIFICATION REQUIRED` |
| G3 | ARCHITECTURE PLAN → MULTI-MD | şablon (`.ai/.templates/documentation/docs-md-template.md`) okundu (Guardrail #16) | dosya üretme |
| G4 | MULTI-MD → VALIDATION | yazım `vault-utf8-writer` ile | geri al |
| G5 | VALIDATION → INDEX UPDATE | `verify` BOM=0/mojibake=0 · `scan` dirty=0 · frontmatter 7/7 · H1 tekil | düzelt, tekrar |
| G6 | INDEX UPDATE → kapanış | `log.md` append kaydı | görev açık kalır |

**Commit kapısı:** zincirin son adımı **commit DEĞİLDİR** — commit orkestratöre aittir (subagent atmaz).

## §5 Workflow — Özet Pseudo

```text
if kapsam disi: DUR + rapor
read (shablon + hedef dosya + komsu vault)
research (exa) -> validate (kanit) -> plan (dosya listesi)
onay -> write (vault-utf8-writer) -> verify/scan -> index + log append
```

## §6 Doğrulama

- [ ] G1-G6 kapılarının tamamı işaretlendi
- [ ] Kapsam dışı yüzeye dokunulmadı (k000-k005 yazma yok, `k1…` üretilmedi)
- [ ] Yeni/Değişen her dosya `INDEX.md` ve ilgili katman `index.md`/`README.md` içinde görünür
- [ ] `log.md`'ye append edildi; geçmiş satırlar değişmedi

## §7 Referanslar

[[CLAUDE]] (§5 operating rules) · [[AGENTS]] (§6-§9 kanıt/çakışma) · [[INDEX]] (dönüş çıktısı) · [[k0-isletim-sistemi/index]] · `.ai/WORKFLOW.md` · `.ai/.templates/documentation/docs-md-template.md` · `.ai/scripts/vault-utf8-writer.mjs`
