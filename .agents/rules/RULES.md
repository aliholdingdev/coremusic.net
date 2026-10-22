---
title: "CoreMusic — Activity Rules (Kapsamlı Kurallar Defteri)"
type: guide
category: governance
date: 2026-10-22
updated: 2026-10-22
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/CLAUDE.md (anayasa) + .ai/AGENTS.md (registry) + kök AGENTS.md (master)"
docType: claude
---

# CoreMusic — Activity Rules (`.agents/rules/`)

**docType:** claude · **Klasör:** `.agents/rules/` · **Sorumlu:** MO (registry) + Security (denetim)

**Zorunlu Bağlantılar:** [[../../AGENTS.md]] · [[../../CLAUDE.md]] · [[../workflows/AGENTS]] · [[../../.ai/CLAUDE]] · [[../../.ai/AGENTS]] · [[../../.ai/WORKFLOW]]

---

## 1. Amaç

Bu dosya, CoreMusic projesinde **her oturumda, her görevde, her agent tarafından uygulanacak bağlayıcı aktivite kurallarının** tek derli toplu defteridir. Vault SSOT'larının (`.ai/CLAUDE.md`, `.ai/AGENTS.md`, kök `AGENTS.md`) dağılmış kurallarını **uygulama anında akılda kalacak** şekilde burada toplar; çelişkide **her zaman vault kazanır** — bu dosya türevdir, yeniden yazım değildir.

| Karar | Karşılığı |
|-------|-----------|
| 16 Hard Guardrail'ın tamamı burada özetlenir | [[../../.ai/CLAUDE]] §7 |
| Domain boundary (dosya tipi → agent) | [[../../.ai/AGENTS]] §5 |
| Execution Loop (adım 0-9) | [[../../AGENTS]] §7 |
| Quality Standards (agent başına %) | [[../../.ai/AGENTS]] §16 |
| Şablon zorunluluğu (Guardrail #16) | [[../../.ai/.templates/index]] |

> **eli10 (basit):** Bu dosya, projede "her işte ne yapmam/ne yapmamam" sorusunun tek cevap kaynağıdır; asıl kural vault'taysa vault kazanır.
> **eli15 (detay):** Ayrı yazıldı çünkü guardrail'lar vault'ta çok dosyaya dağılmıştır; agent görev anında tek dosya tarayarak tam kural setini görmelidir. Okunması, kural ihlalini baştan önlemek içindir (ihlal = revert + CRITICAL log). Vault değişince bu dosya da güncellenir; türev olduğu için tek başına değiştirilmez.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| CoreMusic repoda yapılan **her** kod/doküman/config/faaliyet | Vault SSOT'larının kendisi (`.ai/*.md` — burada kopyalanmaz, yalnız özetlenir) |
| Tüm agent'lar (MO dahil 11 uzman + subagent'lar) | Harici üçüncü taraf servislerin iç kuralları |
| `.php` · `.js` · `.css` · `.sql` · `.cpp/.h` · `.yml` · `.md` üretim ve düzenlemesi | Frozen ADR metinleri (dokunulmaz; yalnız referans verilir) |
| Commit / deploy / secret yüzeyi kuralları | `node_modules/` (yalnız `npm install`) |

- **Kullananlar:** Tüm agent'lar (boot sonrası ilk okuma); MO (denetim); Security (sır yüzeyi).
- **Ön koşul:** Vault boot zinciri (`.ai/CLAUDE.md` → `.ai/AGENTS.md` → `.ai/WORKFLOW.md`) okunmuş olmalı (Guardrail #2 — Vault First).
- **Not:** Bu dosya **kural kaynağı değil, kural özetidir**; uygulanabilirlik vault'tan gelir.

> **eli10 (basit):** Her iş için geçerlidir; kuralın aslı vault'taysa vault konuşur.
> **eli15 (detay):** Kapsam, bu defterin vault yerine geçmediğini garanti eder. Özet ile asıl aynı hızda değişmez; özet eskiyince vault'a bakılır. Kapsam dışı yüzeyler (frozen ADR, node_modules) buradan da değiştirilemez.

---

## 3. Mimari

### 3.1 Kural Katmanları (bu dosyanın dayandığı hiyerarşi)

| Katman | Kaynak | Ne verir | Bu dosyadaki karşılığı |
|--------|--------|----------|------------------------|
| 1 — Anayasa | `@.ai/CLAUDE.md` | 16 Hard Guardrail, katman tanımı (K0-K15), boot protokolü | §4.1 |
| 2 — Registry | `@.ai/AGENTS.md` | Routing, domain boundary, quality standards, handover | §4.2-§4.4 |
| 3 — Master | `@AGENTS.md` (kök) | Execution loop, output style, persona hiyerarşisi, prompt-maker | §4.5-§4.6 |
| 4 — Süreç | `@.ai/WORKFLOW.md` | Faz kapıları, onay akışı | §5 |
| 5 — Uygulama | **Bu dosya** | Özet + yerel (`.agents/`) ek kurallar | §4.7 |

**Çelişki kuralı:** Alt katman üst katmanı ezemez; fark tespit eden agent DUR + bildirir (Guardrail #12 — Contradiction Gate).

### 3.2 Dosya → Kural Eşlemesi (hızlı bakış)

| Dosya tipi | Birincil kural dosyası | Sorumlu agent |
|------------|------------------------|---------------|
| Mimari / katman / middleware / port | [[architecture-rules]] | architect |
| `*.php` (Controller/Service/Repo) | [[backend-rules]] | backend |
| `*.js` · `*.css` · layout (ITSS/BEM/token/Mockup Gate) | [[frontend-rules]] | ui |
| `*.sql` · migration · BCNF | [[database-rules]] | data |
| `*.cpp` / `*.h` · DSP · donanım (Class AB) | [[audio-hardware-rules]] | embedded + dsp-fw + audio-hw |
| Auth · CSRF/CSP · secret · OWASP | [[security-rules]] | security (veto yetkili) |
| Süreç · QA kapısı · commit · handover | [[workflow-rules]] | MO + qa |
| `*.yml` (CI/CD) · deploy | [[workflow-rules]] §3.5 + kök kurallar | devops |
| `.md` (vault) | §4.6 · Guardrail #16 şablon | MO |
| `.env` / secret | [[security-rules]] §3.3 | security |

> **eli10 (basit):** Hangi dosyaya dokunuyorsam hangi kural tablosuna bakacağım bu tabloda.
> **eli15 (detay):** Eşleme ayrıdır çünkü agent tipini dosya uzantısı belirler, keyword değil. Tablo okunmadan başlanırsa yanlış kural seti uygulanır ve ihlal kaçar. Dosya tipi eklenince satır eklenir.

---

## 4. Kurallar

### 4.1 Hard Guardrails — 16 Madde (özet; tam metin: `@.ai/CLAUDE.md` §7 — bağlayıcı)

| # | Kural | Uygulama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | Zero Code Before Plan | Plan onayı olmadan kod yok | Kod revert |
| 2 | Vault First | `.ai/` vault okunmadan plan/kod/faaliyet başlamaz | İşlem durur + revert |
| 3 | Zero Hallucination | Doğrulanamayan → `⚠️ VERIFICATION REQUIRED`; bilinmeyen → `UNKNOWN` | İçerik silinir |
| 4 | In-Place Refactoring | Dosya adı/yolu değişmez | Dosya geri yüklenir |
| 5 | Single Source of Truth | Bilgi yalnız `.ai/` vault'tan | Harici bilgi reddedilir |
| 6 | CSRF Token = `csrf_token` | `_csrf_token` yasak | Token reddedilir |
| 7 | Middleware Order Immutable | Sıra değişmez | Sistem durdurulur |
| 8 | Port 81 = music.coremusic.net | PHP 8.4 | Yanlış port yasak |
| 9 | No ORM | Raw PDO only (ADR-002) | ORM reddedilir |
| 10 | No Frameworks | Vanilla JS + ITCSS (ADR-001) | Framework reddedilir |
| 11 | Mockup Before Frontend | `ui-design/01-mockup-index` + `02-component-inventory` + tokens + 45-tier matris + `reference/01-10` okunmadan frontend kodu YASAK; görsel okunamıyorsa DUR | Revert + CRITICAL log |
| 12 | Contradiction Gate | Vault çelişkisinde kullanıcıya sor, onay bekle | İşlem durur |
| 13 | Session Continuity | Oturum başında geçmiş session'dan devam | Bağlam kaybı |
| 14 | Human Approval Gate | Mimari karar öncesi kullanıcı onayı zorunlu | Kod revert |
| 16 | Template Mandatory | Yeni `.md` şablonsuz üretilmez (`.ai/.templates/index.md`); ui-design → Kalıp A-D; CSS → `css-template.md` v4.0.0 | Dosya geçersiz |
| 17 | Single Component Responsive | Tek component + responsive CSS; ayrı HTML/branch YASAK; 1024×600 = pixel reference | Kod revert |

*(Not: #15, #2 ile birleştirildi — 2026-10-12 sayı 16'dır; numara atlanmıştır, hata değildir.)*

### 4.2 Dil & Teknoloji Kuralları

| Alan | Kural |
|------|-------|
| **PHP 8.4** | `strict_types=1` · PSR-12 · PDO prepared statement zorunlu · `unserialize(user_input)` yasak · `SELECT *` yasak |
| **JS/TS** | Vanilla ES6+ (framework yasak — ADR-001) · `var` yok · `any` yok · strict · async/await |
| **CSS** | ITCSS 11 katman (01→11 sırası korunur) · BEM namespace · sabit değer yalnız `01_Abstracts` token dosyasında (`--cm-*`) · `07_Vendors` dokunulmaz · **taşıma/silme YOK** (yanlış yerleşim yalnız raporlanır) · import zinciri `08_Devices/d-*.css` + `auth-bundled.css` (`main.css` YOK) · `@media` içinde `var()` okuma yasak |
| **SQL / MySQL 9** | 18 BCNF veritabanı · parametreli sorgu zorunlu · migration dosyalı · index planı yazılır |
| **C++20 (Neva Engine)** | Zero-allocation, lock-free, noexcept, cache-line align (64B) · RT callback'de lock/allocation yasak · buffer'lar baştan allocate |
| **CI/CD** | `.github/workflows/` yalnız devops · GitLeaks taraması temiz · secret `.yml`'e gömülmez (env substitution) |
| **Markdown (vault)** | Frontmatter 7 zorunlu alan + `docType` · wiki-link `[[...]]` · emoji/dekoratif işaret yasak · eli10/eli15 blokları korunur |

### 4.3 Domain Boundary (dosya tipi → tek sorumlu agent — `@.ai/AGENTS.md` §5)

| Dosya tipi | Sorumlu | İhlal |
|------------|---------|-------|
| `*.php` | backend | Layer violation → revert + log |
| `*.js` · `*.css` | ui | revert |
| `*.sql` | data | revert |
| `*.cpp` · `*.h` | embedded | revert |
| `*.yml` · `*.yaml` | devops | revert |
| `tests/**` | qa | revert |
| Security middleware · `.env` | security | revert + CRITICAL |
| `log.md` | tüm ajanlar | **append-only** — geçmiş satıra dokunulmaz |
| `.ai/` vault | MO (vault-updater) | yetkisiz yazım reddedilir |

**Kural:** Agent kendi alanı dışında dosya değiştirmez; gerekirse handover başlatır (`.ai/AGENTS.md` §9).

### 4.4 Security (OWASP Top 10:2025 — bağımsız veto yetkisi: `security-engineer`)

| Kural | Değer |
|-------|-------|
| Şifreleme | AES-256-GCM / ChaCha20-Poly1305 · parola: **Argon2id** |
| Yasak algoritmalar | MD5, SHA-1, DES (üretimde asla) |
| CSRF | Token adı `csrf_token` · her form/mutasyon doğrulaması |
| CSP / SecurityHeaders | PSR-15 middleware zinciri (sıra immutable — Guardrail #7) |
| Rate limit | Login + API gateway'de zorunlu |
| Secret yüzeyi | Anahtar/sır asla `.md` · `.json` · `.log` · `.yml` içine yazılmaz; yalnız `.env` / env substitution · vault'ta `[REDACTED]` |
| Fail-safe | Hata durumunda **fail-closed** (fail-open yasak) |
| Güvenlik ihlali | Agent bulguyu **düzeltmeden** bildirir (CRITICAL) — düzeltme security agent'a aittir |

### 4.5 Workflow & Onay Kapıları

| Kapı | Kural |
|------|-------|
| Faz 1 (keşif) | Onaysız: kod değişikliği · yeni dosya · yeni klasör · yeni bağımlılık · büyük yapısal değişiklik |
| UI işi | `.ai/ui-design/**` görseli okunmadan kod yazılamaz (Guardrail #11) |
| CSS işi | `.ai/.templates/frontend/css-template.md` (v4.0.0) + kök `notes.md` okunur |
| Mimari karar | Kullanıcı onayı (Guardrail #14) |
| Deploy / production | Onay ister; subagent kendi başına production'a dokunmaz |
| 3 başarısız düzeltme | DUR + tek kısa spekülasyonsuz soru (kör deneme döngüsü yasak) |

### 4.6 Commit & Kapanış

| Kural | Değer |
|-------|-------|
| **Commit subagent tarafından ATILMAZ** | Commit kararı ve eylemi orkestratöre (ana oturuma) aittir — 3 yerde teyitli: `.ai/AGENTS.md` §13.9 #5 · `.opencode/AGENTS.md` §4 #10 · kök `AGENTS.md` |
| Message formatı | `tur: kısa açıklama` (Türkçe, düz metin) — mevcut `git log` konvansiyonuna uyar |
| Kapanış | `.ai/log.md`'ye **append** + MEMORY/TODOS güncelle (on-session-end hook) |
| Vault sync | Session kapanışında `vault-sync-post` akışı |
| Yıkıcı işlem | Branch silme / force push / `_archive-keep/` silme → **onay ister** |

### 4.7 Çıktı Stili (bağlayıcı — kök `AGENTS.md` özet)

1. Sonuç/sonraki adım **en başta** (komut, dosya yolu, snippet önce).
2. Numaralı adımlar; adım başına tek sınırlı iş.
3. Her tur ilerleme özeti ("5 adımın 3'ü bitti"); somut süre birimi ("biraz" yasak).
4. Hata: konum + neden + düzeltme; dram/boş övgü yok.
5. Liste üst sınırı 5 madde (tablolar istisna); giriş/özet/kapanış paragrafı yok.
6. Belirsiz istekte **tek kısa soru**; yıkıcı eylemden önce onay.

> **eli10 (basit):** §4, "her işte neye uyacağım"ın tam cevabı: guardrail, dil kuralı, yetki, güvenlik, onay, commit, üslup.
> **eli15 (detay):** Kurallar alt başlıklara ayrıldı çünkü tek tabloda toplansa hangi ihlalin hangi sonucu verdiği kaybolur. Her alt bölüm vault'taki bir SSOT'a bağlanmıştır; türev olduğu için vault değişince burası güncellenir, tersi değil. Uygulama anında §4.1'den §4.7'ye taranır; bir madde vault ile çelişiyorsa DUR + Contradiction Gate.

---

## 5. Workflow

```text
OTURUM BAŞI (boot: kök AGENTS.md → CLAUDE.md → @.ai/*)
  → bu dosya §4.1-§4.7 taraması (guardrail tazeleme)
  → GÖREV ANALİZİ (keyword → agent routing — @.ai/AGENTS.md §6)
  → PRE-FLIGHT (şablon var mı? mockup okundu mu? sır riski var mı? lock var mı?)
  → ÜRET (domain boundary içinde; vault öncelikli)
  → ÖZ-DENETİM (§6 Doğrulama tablosu)
  → QA/GÜVENLİK kapısı (gerekirse handover)
  → ORKESTRATÖR: log.md append + COMMIT (subagent commit atmaz)
  → KAPANIŞ (vault-sync · MEMORY · TODOS)
```

| # | Adım | Kapı | Atlarsan ne olur |
|---|------|------|------------------|
| 1 | Boot + vault okuma | Guardrail #2 | Kural körlüğü → ihlal |
| 2 | Routing (domain boundary) | §4.3 | Yetki ihlali, yanlış dosya |
| 3 | Pre-flight (şablon/mockup/sır) | §4.5 | Şablonsuz dosya, sızıntı, mockupsız kod |
| 4 | Üretim | §4.2 | Standart dışı kod |
| 5 | Öz-denetim | §6 | Kırık iş ilerler |
| 6 | Kapı + handover | `@.ai/AGENTS.md` §9 | Yapan=denetleyen → hata kaçar |
| 7 | Commit (yalnız orkestratör) | §4.6 | Yetkisiz commit |
| 8 | Kapanış/sync | vault-sync-post | İz kaybı |

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType: claude` mevcut |
| 2 | İskelet | §1–§7 aynı sıra (Guardrail #16 / context-template) |
| 3 | Guardrail özeti | §4.1'de 16 madde eksiksiz; #15→#2 birleşim notu var |
| 4 | Türevlik | Hiçbir kural vault'u ezmez; her § vault SSOT'una bağlı |
| 5 | Domain boundary | §4.3 `@.ai/AGENTS.md` §5 ile birebir |
| 6 | Commit kuralı | §4.6 "subagent ATILMAZ" ifadesi mevcut |
| 7 | Zero-Hallucination | Uydurma sayı/ADR/sürüm yok; her sayı vault ölçümü |
| 8 | Wiki-link | `[[...]]` formatı; markdown path linki yok |
| 9 | eli10/eli15 | §1, §2, §3.2, §4 altı etiketli bloklar mevcut |
| 10 | Dokunulmaz | Boot dosyaları (`AGENTS.md`, `CLAUDE.md`, `.ai/*`) değiştirilmedi; commit atılmadı |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| AI Anayasası (SSOT) | [[../../.ai/CLAUDE]] | §7 16 Hard Guardrail · §16 boot protokolü |
| Agent Registry (SSOT) | [[../../.ai/AGENTS]] | §5 domain · §6 routing · §9 handover · §16 quality |
| Master kurallar | [[../../AGENTS]] | §7 execution loop · §16 persona · §8 prompt-maker |
| Süreç | [[../../.ai/WORKFLOW]] | Faz kapıları |
| Şablon registry | [[../../.ai/.templates/index]] | Guardrail #16 şablon seçimi |
| Context şablonu | [[../../.ai/.templates/frontend/context-template]] | Bu dosyanın iskeleti |
| Workflow rolleri | [[../workflows/AGENTS]] | `.agents/workflows/` sahiplik |
| Dizin envanteri | `.agents/rules/` | **7 dosya** (bu defter + 6 domain kuralı) — 2026-10-22 disk ölçümü |
| Mimari kurallar | [[architecture-rules]] | K0-K15 · middleware · API-first · portlar (`.ai/CLAUDE.md` §5-§13) |
| Backend kurallar | [[backend-rules]] | PHP 8.4 · PDO · API-first (`.ai/AGENTS.md` §16) |
| Frontend kurallar | [[frontend-rules]] | Mockup Gate · ITCSS/BEM · token (`.ai/CLAUDE.md` §7 #11/#16/#17) |
| Veritabanı kurallar | [[database-rules]] | 18 BCNF · 156 tablo · ek 2 DB (`.ai/CLAUDE.md` §18) |
| Ses & donanım kurallar | [[audio-hardware-rules]] | Float32/48kHz · Class AB · PCM5122 yasak (`.ai/CLAUDE.md` §19/§23) |
| Güvenlik kurallar | [[security-rules]] | OWASP · kripto · secret yüzeyi (`.ai/CLAUDE.md` §6/§21-§23) |
| Süreç kurallar | [[workflow-rules]] | Dispatch · öncelik · handover · commit (`.ai/AGENTS.md` §7-§12) |
| Workflow envanteri | `.agents/workflows/` | 11 md (2026-10-22 disk ölçümü) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-22
