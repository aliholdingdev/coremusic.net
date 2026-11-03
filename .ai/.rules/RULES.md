---
title: "CoreMusic — Activity Rules (Kapsamlı Aktivite Kuralları Defteri)"
type: guide
category: governance
date: 2026-10-22
updated: 2026-11-01
version: 2.0.0
status: active
authority: "Derived — SSOT: .ai/CLAUDE.md (anayasa) + .ai/AGENTS.md (registry) + .ai/.rules/kurallar.md + kök AGENTS.md"
docType: claude
---

# CoreMusic — Activity Rules (`.ai/.rules/RULES.md`)

**docType:** claude · **Klasör:** `.ai/.rules/` · **Sorumlu:** Master Orchestrator (registry) + Security Engineer (denetim)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[GEMINI.md]] · [[.ai/CLAUDE.md]] · [[.ai/AGENTS.md]] · [[.ai/WORKFLOW.md]] · [[kurallar.md]] · [[hata-listesi.md]] · [[karar-kurallari.md]] · [[senior-mode.md]]

---

## 1. Amaç

Bu dosya, CoreMusic projesinde **her oturumda, her görevde, her agent tarafından uygulanacak bağlayıcı aktivite kurallarının** tek derli toplu defteridir. Vault SSOT'larının (`.ai/CLAUDE.md`, `.ai/AGENTS.md`, kök `AGENTS.md`) ve temel direktiflerin (`.ai/.rules/kurallar.md`, `.ai/.rules/hata-listesi.md`) kurallarını **uygulama anında akılda kalacak** şekilde burada toplar; çelişkide **her zaman vault kazanır** — bu dosya türevdir, yeniden yazım değildir.

| Karar / Standart | Karşılığı |
|---|---|
| 16 Hard Guardrail'ın tamamı burada özetlenir | [[.ai/CLAUDE.md]] §7 |
| Temel Direktifler (`KURAL-01` .. `KURAL-10`) | [[kurallar.md]] |
| Hata Listesi & Önleme Kataloğu (`HL-01` .. `HL-12`) | [[hata-listesi.md]] |
| Onaylı Karar Kuralları (ADR-112, 113, 114, 115) | [[karar-kurallari.md]] |
| Domain boundary (dosya tipi → agent) | [[.ai/AGENTS.md]] §5 |
| Execution Loop (adım 0-9) & Senior Mode 16 analiz adımı | [[AGENTS.md]] §7 · [[senior-mode.md]] |
| Quality Standards (agent başına %) | [[.ai/AGENTS.md]] §16 |
| Şablon zorunluluğu (Guardrail #16) | `.ai/.templates/` |

> **eli10 (basit):** Bu dosya, projede "her işte ne yapmam/ne yapmamam" sorusunun tek cevap kaynağıdır; asıl kural vault'taysa vault kazanır.  
> **eli15 (detay):** Ayrı yazıldı çünkü guardrail'lar vault'ta çok dosyaya dağılmıştır; agent görev anında tek dosya tarayarak tam kural setini görmelidir. Okunması, kural ihlalini baştan önlemek içindir (ihlal = revert + CRITICAL log). Vault değişince bu dosya da güncellenir; türev olduğu için tek başına değiştirilmez.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| CoreMusic repoda yapılan **her** kod/doküman/config/faaliyet | Vault SSOT'larının kendisi (`.ai/*.md` — burada kopyalanmaz, yalnız özetlenir) |
| Tüm agent'lar (MO dahil 11 uzman + subagent'lar) | Harici üçüncü taraf servislerin iç kuralları |
| `.php` · `.js` · `.css` · `.sql` · `.cpp/.h` · `.yml` · `.md` üretim ve düzenlemesi | Frozen ADR metinleri (dokunulmaz; yalnız referans verilir) |
| Commit / deploy / secret yüzeyi kuralları | `node_modules/` · `vendor/` (yalnız package manager) |

- **Kullananlar:** Tüm agent'lar (boot sonrası ilk okuma); MO (denetim); Security (sır yüzeyi).
- **Ön koşul:** Vault boot zinciri (`.ai/CLAUDE.md` → `.ai/AGENTS.md` → `.ai/WORKFLOW.md` → `.ai/durum.md`) okunmuş olmalı (Guardrail #2 — Vault First).
- **Not:** Bu dosya **kural kaynağı değil, kural özetidir**; uygulanabilirlik vault'tan gelir.

---

## 3. Mimari

### 3.1 Kural Katmanları (Hiyerarşi)

| Katman | Kaynak | Ne verir | Bu dosyadaki karşılığı |
|--------|--------|----------|------------------------|
| 1 — Anayasa | `@.ai/CLAUDE.md` | 16 Hard Guardrail, katman tanımı (K0-K15), boot protokolü | §4.1 |
| 2 — Registry | `@.ai/AGENTS.md` | Routing, domain boundary, quality standards, handover | §4.3 |
| 3 — Temel Direktifler | `kurallar.md` · `hata-listesi.md` | KURAL-01..10, HL-01..12 önleme kuralları | §4.1-§4.2 |
| 4 — Master | `@AGENTS.md` (kök) | Execution loop, output style, persona hiyerarşisi, prompt-maker | §4.5-§4.7 |
| 5 — Süreç | `@.ai/WORKFLOW.md` | Faz kapıları, onay akışı, yaşam döngüsü | §5 |
| 6 — Uygulama | **Bu dosya** & alt kurallar | Domain kuralları (`*-rules.md`) | §4.2 |

**Çelişki kuralı:** Alt katman üst katmanı ezemez; fark tespit eden agent DUR + bildirir (Guardrail #12 — Contradiction Gate).

### 3.2 Dosya → Kural Eşlemesi (Hızlı Bakış)

| Dosya tipi | Birincil kural dosyası | Sorumlu agent |
|------------|------------------------|---------------|
| Mimari / katman / middleware / port | [[architecture-rules.md]] | architect |
| `*.php` (Controller/Service/Repo) | [[backend-rules.md]] | backend-architect |
| `*.js` · `*.css` · layout (Light Mode / ITCSS / BEM / Token / Mockup Gate) | [[frontend-rules.md]] | ui-designer |
| `*.sql` · migration · BCNF | [[database-rules.md]] | data-engineer |
| `*.cpp` / `*.h` · DSP · donanım (Class AB) | [[audio-hardware-rules.md]] | embedded-engineer + dsp-fw + audio-hw |
| Auth · CSRF/CSP · secret · OWASP | [[security-rules.md]] | security-engineer (veto yetkili) |
| Süreç · QA kapısı · commit · handover | [[workflow-rules.md]] | MO + qa-engineer |
| `*.yml` (CI/CD) · deploy | [[workflow-rules.md]] + kök kurallar | devops-engineer |
| `.md` (vault) | §4.6 · Guardrail #16 şablon | MO |
| `.env` / secret | [[security-rules.md]] §3.3 | security-engineer |

---

## 4. Kurallar

### 4.1 Hard Guardrails & Temel Direktifler (Özet)

| # | Kural | Uygulama | İhlal Sonucu |
|---|---|---|---|
| **HG-01** | Zero Code Before Plan | Plan onayı olmadan kod yok | Kod revert |
| **HG-02** | Vault First | `.ai/` vault okunmadan plan/kod/faaliyet başlamaz | İşlem durur + revert |
| **HG-03** | Zero Hallucination | Doğrulanamayan → `⚠️ VERIFICATION REQUIRED`; bilinmeyen → `UNKNOWN` | İçerik silinir |
| **HG-04** | In-Place Refactoring | Dosya adı/yolu değişmez (taşıma/silme yasak) | Dosya geri yüklenir |
| **HG-05** | Single Source of Truth | Bilgi yalnız `.ai/` vault'tan | Harici bilgi reddedilir |
| **HG-06** | CSRF Token = `csrf_token` | `_csrf_token` kesinlikle yasak | Token reddedilir |
| **HG-07** | Middleware Order Immutable | 10 adımlı pipeline sırası değişmez | Sistem durdurulur |
| **HG-08** | Port 81 = music.coremusic.net | Control Service portu değişmez | Yanlış port yasak |
| **HG-09** | No ORM | Raw PDO only (ADR-002, ADR-114) | ORM reddedilir |
| **HG-10** | No Frameworks | Vanilla JS + ITCSS (ADR-001) | Framework reddedilir |
| **HG-11** | Mockup Before Frontend | `.ai/ui-design/` görselleri ve tokenler okunmadan frontend kodu YASAK | Revert + CRITICAL log |
| **HG-12** | Contradiction Gate | Vault çelişkisinde kullanıcıya sor, onay bekle | İşlem durur |
| **HG-13** | Session Continuity | Oturum başında geçmiş session'dan (`durum.md`) devam | Bağlam kaybı |
| **HG-14** | Human Approval Gate | Mimari karar / DDL / paket ekleme öncesi kullanıcı onayı zorunlu | Kod revert |
| **HG-16** | Template Mandatory | Yeni `.md` şablonsuz üretilmez (`.ai/.templates/`) | Dosya geçersiz |
| **HG-17** | Single Component Responsive | Tek component + responsive CSS; ayrı HTML/branch YASAK; 1024×600 = pixel reference | Kod revert |
| **HL-01** | **Yalnızca Light Mode** | Dark mode yoktur/yasaktır; dark class veya `@media (prefers-color-scheme: dark)` yasak | Kod revert |
| **HL-02** | **Class AB Amplifikatör** | Donanım topolojisi kesinlikle discrete Class AB'dir (Class D yasak) | Tasarım reddi |
| **HL-03** | **Subagent Commit Yasağı** | Subagent asla `git commit` veya `git add` çalıştıramaz | İhlal bildirimi |

### 4.2 Dil & Teknoloji Kuralları

| Alan | Kural |
|---|---|
| **PHP 8.4** | `declare(strict_types=1);` her dosyada zorunlu · PSR-12 · raw PDO prepared statement zorunlu · `unserialize(user_input)` yasak · `SELECT *` yasak · Tek route tablosu `/api/{version}/` (ADR-113) · Repository standardı `I*` + `final class` (ADR-114) |
| **JS/TS** | Vanilla ES2022+ (framework yasak — ADR-001) · `var` yok · `any` yok · strict · async/await · DOM güvenli init (`HL-05`) |
| **CSS** | **Yalnızca Light Mode (`HL-01`)** · ITCSS 11 katman (01→11 sırası korunur) · BEM namespace · sabit değer yalnız `01_Abstracts` token dosyasında (`--cm-*`) · `07_Vendors` dokunulmaz · **taşıma/silme YOK** · import zinciri `08_Devices/d-*.css` + `auth-bundled.css` (`main.css` YOK) · `@media` içinde `var()` okuma yasak · Container query disiplini (`HL-04`) |
| **SQL / MySQL 9** | 18 BCNF veritabanı · parametreli sorgu zorunlu · migration dosyalı forward/rollback (ADR-112) · index planı yazılır · soft delete `is_deleted = 0` |
| **C++20 (Neva Engine)** | Zero-allocation, lock-free, noexcept, cache-line align (64B) · RT callback'de lock/allocation yasak · buffer'lar baştan allocate |
| **CI/CD** | `.github/workflows/` yalnız devops · GitLeaks taraması temiz · secret `.yml`'e gömülmez (env substitution) |
| **Markdown (vault)** | Frontmatter 7 zorunlu alan + `docType` · wiki-link `[[...]]` · emoji/dekoratif işaret yasak · eli10/eli15 blokları korunur |

### 4.3 Domain Boundary (Dosya Tipi → Tek Sorumlu Agent)

| Dosya tipi | Sorumlu | İhlal |
|---|---|---|
| `*.php` | backend-architect | Layer violation → revert + log |
| `*.js` · `*.css` | ui-designer | revert |
| `*.sql` | data-engineer | revert |
| `*.cpp` · `*.h` | embedded-engineer | revert |
| `*.yml` · `*.yaml` | devops-engineer | revert |
| `tests/**` | qa-engineer | revert |
| Security middleware · `.env` | security-engineer | revert + CRITICAL |
| `log.md` | tüm ajanlar | **append-only** — geçmiş satıra dokunulmaz |
| `.ai/` vault | MO (vault-updater) | yetkisiz yazım reddedilir |

### 4.4 Güvenlik Standartları (OWASP Top 10:2025)

| Kural | Değer |
|---|---|
| Şifreleme | AES-256-GCM / ChaCha20-Poly1305 · parola: **Argon2id** |
| Yasak algoritmalar | MD5, SHA-1, DES (üretimde asla) |
| CSRF | Token adı `csrf_token` · her form/mutasyon doğrulaması |
| CSP / SecurityHeaders | PSR-15 middleware zinciri (sıra immutable — Guardrail #7) |
| Rate limit | Login + API gateway'de zorunlu (APCu 60 req/60s) |
| Secret yüzeyi | Anahtar/sır asla `.md` · `.json` · `.log` · `.yml` içine yazılmaz; yalnız `.env` (`.ai/.env.figma` dahil — `HL-07`) |
| Fail-safe | Hata durumunda **fail-closed** (fail-open yasak) |
| Güvenlik ihlali | Agent bulguyu **düzeltmeden** bildirir (CRITICAL) — düzeltme security agent'a aittir |

### 4.5 Workflow & Onay Kapıları

| Kapı | Kural |
|---|---|
| Faz 1 (keşif) | Onaysız: kod değişikliği · yeni dosya · yeni klasör · yeni bağımlılık · büyük yapısal değişiklik |
| UI işi | `.ai/ui-design/**` görseli ve tokeni okunmadan kod yazılamaz (Guardrail #11) |
| CSS işi | `.ai/.templates/frontend/css-template.md` (v4.0.0) + Light Mode kuralı okunur |
| Mimari karar | Kullanıcı onayı zorunlu (Guardrail #14) |
| Deploy / production | Onay ister; subagent kendi başına production'a dokunmaz |
| 3 başarısız düzeltme | DUR + tek kısa spekülasyonsuz soru (kör deneme döngüsü yasak) |

### 4.6 Commit & Kapanış

| Kural | Değer |
|---|---|
| **Commit subagent tarafından ATILMAZ** | Commit kararı ve eylemi kullanıcıya / ana orkestratöre aittir (`HL-03`) |
| Message formatı | `tur: kısa açıklama` (Türkçe, düz metin) |
| Kapanış | `.ai/durum.md` ve ilgili `[subdomain]/durum.md` güncellenir; `.ai/log.md`'ye **append** edilir; `MEMORY.md` tazelenir |
| Token koruma | 4 durumda (konu değişimi, tekrarlama, büyük dosya kapanışı, teslim sonrası) yeni sohbete geçilir (`KURAL-08`) |
| Yıkıcı işlem | Branch silme / force push / `_archive-keep/` silme → **onay ister** |

---

## 5. Workflow (Yürütme Döngüsü)

```text
OTURUM BAŞI (boot: kök AGENTS.md → CLAUDE.md → .ai/durum.md → subdomain durum.md)
  → RULES.md & hata-listesi.md taraması (pre-flight kontrol)
  → GÖREV ANALİZİ (senior-mode 16 adım analiz → soru sorma ağacı P0-P3)
  → PRE-FLIGHT (şablon var mı? mockup okundu mu? Light Mode mu? Class AB mi?)
  → ÜRETİM (domain boundary içinde; raw PDO; strict types; no ORM)
  → ÖZ-DENETİM (§6 Doğrulama tablosu + hata-listesi HL-01..12)
  → QA/GÜVENLİK kapısı (test coverage ≥%80)
  → ORKESTRATÖR: durum.md güncelle + log.md append (subagent commit atmaz)
  → DEVİR / YENİ SOHBET (KURAL-08 session triggers)
```

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---|---|
| 1 | Frontmatter | 7 zorunlu alan + `docType: claude` mevcut |
| 2 | İskelet | §1–§7 aynı sıra (Guardrail #16 / context-template) |
| 3 | Guardrail özeti | 16 Hard Guardrail + HL-01..03 eksiksiz |
| 4 | Tema kontrolü | Yalnızca Light Mode vurgulandı; Dark mode yasaklandı |
| 5 | Donanım topolojisi | Class AB discrete vurgulandı; Class D yasaklandı |
| 6 | Commit kuralı | Subagent commit atamaz (`HL-03`) |
| 7 | Zero-Hallucination | Uydurma sayı/ADR/sürüm yok; disk kanıtı esas |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|---|---|---|
| AI Anayasası (SSOT) | [[.ai/CLAUDE.md]] | 16 Hard Guardrail · katmanlar |
| Agent Registry (SSOT) | [[.ai/AGENTS.md]] | Domain boundary · routing · handover |
| Temel Direktifler | [[kurallar.md]] | KURAL-01..10 |
| Hata Listesi Kataloğu | [[hata-listesi.md]] | HL-01..12 |
| Onaylı Kararlar | [[karar-kurallari.md]] | ADR-112, 113, 114, 115 |
| Senior Mode | [[senior-mode.md]] | 16 analiz adımı · soru bütçesi |
| Mimari kurallar | [[architecture-rules.md]] | K0-K15 · middleware · portlar |
| Backend kurallar | [[backend-rules.md]] | PHP 8.4 · raw PDO · ADR-113/114 |
| Frontend kurallar | [[frontend-rules.md]] | Light Mode · ITCSS · BEM · Token |
| Veritabanı kurallar | [[database-rules.md]] | 18 BCNF · migration · soft delete |
| Ses & donanım kurallar | [[audio-hardware-rules.md]] | Float32/48kHz · Class AB · AK4458 |
| Güvenlik kurallar | [[security-rules.md]] | OWASP · Argon2id · CSRF · secret |
| Süreç kurallar | [[workflow-rules.md]] | Kiro 6-step workflow · dispatch · handover |

---

**Authority:** Bayram Ali / Kurucu & Mimari Otorite  
**Last Updated:** 2026-11-01 · **Version:** 2.0.0
