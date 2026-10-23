---
title: "CoreMusic — shared/ PRD (Ortak Altyapı Paketi Gereksinim Belgesi)"
type: prd
category: shared
version: 1.0.0
status: active
authority: "Derived — SSOT: [[../PRD.md]] (platform + aile indeksi) · bağ: [[CONTEXT.md]] · [[AGENTS.md]]"
updated: 2026-10-23
---

# shared/ — PRD

**docType:** prd · **Klasör:** `shared/` · **Yüzey:** #7 (aile indeksi §5) · **Sorumlu:** Backend Architect (+ Security/Data denetimi)

**Zorunlu Bağlantılar:** [[../PRD.md]] · [[CONTEXT.md]] · [[AGENTS.md]] · [[CLAUDE.md]] · [[WORKFLOW.md]]

**Bağlı platform gereksinimleri (tekrar edilmez, yalnız bağlanır):** PRD-PLT-005 · PRD-PLT-006 · PRD-PLT-007 · PRD-PLT-008

---

## 1. Amaç

Bu PRD, `shared/` paketinin (**`coremusic/shared-infrastructure` v2.0.0**) ortak altyapı gereksinimlerini tanımlar: tüm subdomain'lerin (api · auth · home) ortak kullandığı Router, Middleware, Repository, OAuth, Security, Session, Cache, Event ve API katmanı. Paket Composer path-repo (symlink) ile tüketilir; bir kez yazılır, üç site kullanır — kırıcı değişiklik üç siteyi aynı anda bozar.

> **eli10 (basit):** Bu dosya, üç sitenin ortak kullandığı kutunun kurallarını söyler.
> **eli15 (detay):** Ayrı yazıldı çünkü ortak paket tüm sistemin temelidir; gereksinimi yoksa her
> subdomain kendi kopyasını üretir (PRD-PLT-005 ihlali). Okunması, modül değişikliğinde etki alanını
> gösterir. Yazılması, Security/Data/QA denetim kapılarını tanımlar. Olmazsa sürüm dağılır, yama ikiye bölünür.

---

## 2. Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `src/**` modülleri (alt PRD'ler #9–#17 ile derinleştirilir) | `vendor/**` (Composer malı, elle düzenlenmez — 2743 dosya) |
| `composer.json` / `composer.lock` (paket sözleşmesi, 43 kilitli paket) | Subdomain uygulama kodu (api/auth/home PRD'leri) |
| `config/**` (routes · auth-routes · oauth-platforms · domain · `.env.example` · JWT çifti) | Frontend/CSS (assets PRD #3) |
| `phpunit.xml` + `tests/**` (9 grup) · `database/migrations/**` (3 migration) | `.phpunit.cache/**` (artefakt) |

- **Ölçüm (disk kanıtı, 2026-10-12):** kök **15 girdi = 9 dosya + 6 dizin**; `src/` 203 dosya / 20 modül — sayı `[[CONTEXT.md]]` §3.1'dedir, burada tekrarlanmaz. **Not:** `src/` 20 alt dizinden **19 aktiftir** — `AI/` ve `Api/Bff/` DECOMMISSIONED 2026-10-18 (ADR-030/ADR-084, 0 dış referans kanıtıyla silindi); eski "20 modül" sayımı tarihsel kayıttır.
- **Ön koşul:** Tüketici matrisi — api/auth/home path-repo (`^2.0`) kullanır; **media ve assets kullanmaz**.
- **Dokunulmaz:** `vendor/**` · `.phpunit.cache/**` · `config/jwt/*` anahtar çifti içeriği (private pem REDACTED) · frozen ADR metinleri.

> **eli10 (basit):** Kapsam ortak kod, ayar, test ve migration; üçüncü taraf kod ve subdomain dışı her şey.
> **eli15 (detay):** Sınır, uygulama iş kuralının ortak pakete gömülmesini engeller (§6 risk 1). Okunması,
> modül/ayar/tampon ayrımını gösterir. Sınır yoksa paket uygulamaya dönüşür ve sürümlenemez.

---

## 3. Gereksinimler (PRD-SHR-001..006)

| ID | Gereksinim | Kabul Kriteri |
|----|-----------|----------------|
| PRD-SHR-001 | Paket API'si tek sürüm kalır | `composer.json` sürümü tek kaynak; alt domain kendi kopyasını tutmaz |
| PRD-SHR-002 | PSR-4 kökü `CoreMusic\` → `src/` bozulmaz | Namespace sapması yok; `src/Interfaces/` → `src/Contracts/` taşıması korunur |
| PRD-SHR-003 | DB erişimi yalnız `DatabaseManager` + PDO prepared (ADR-002/003) | İkinci bağlantı yok; `SELECT *` yasak; ORM yasak |
| PRD-SHR-004 | Middleware ekleme `MiddlewarePipeline` kaydıyla olur | Kayıtsız middleware devrede değil; bypass kapsamı ADR'siz genişlemez (ADR-008) |
| PRD-SHR-005 | Yeni domain event `src/Events/Domain/` + arayüz + test ile gelir | `DomainEventInterface` zorunlu; test yeşil |
| PRD-SHR-006 | Test/PHPStan kapısı yeşil kalır | `composer test` + `composer phpstan` PASS; coverage hedefi ≥%80 (PRD-PLT-006) |

> **eli10 (basit):** Altı kural: tek sürüm, düz namespace, tek DB kapısı, kayıtlı middleware, testli event, yeşil kapı.
> **eli15 (detay):** Kimlikler ayrı çünkü ortak paket ihlali üç siteye birden yansır; denetlenebilir olmalıdır.
> Okunması, modül değişikliği öncesi kontrol verir. Kimliksiz kural "uyarı"ya döner, PHPStan kapısı hedefsiz kalır.

---

## 4. Kabul Kriteri (bu yüzeyin kapıları)

| # | Kapı | Ölçüt |
|---|------|-------|
| 1 | Paket | PRD-SHR-001/002 — sürüm + PSR-4 |
| 2 | Veri | PRD-SHR-003 — DatabaseManager tek kapı (PRD-PLT-007) |
| 3 | Güvenlik | PRD-SHR-004 — Security denetimi; bypass yalnız ADR ile (PRD-PLT-006) |
| 4 | Test | PRD-SHR-005/006 — `composer test` + `composer phpstan` |
| 5 | Kanıt | Ölçülmeyen modül/coverage iddiası `⚠️ VERIFICATION REQUIRED` (PRD-PLT-011) |

> **eli10 (basit):** Beş kapı: paket, veri, güvenlik, test, kanıt.
> **eli15 (detay):** Kapılar üretimden ayrıdır; denetim üretime karışmaz. Okunması, kapanış raporu
> checklist'ini verir. Kapı yoksa "üç siteyi bozmadı" iddiası kanıtsız olur.

---

## 5. ADR Bağları

| Karar | ADR | Yüzeye etkisi |
|-------|-----|---------------|
| ORM yasak / PDO / DatabaseManager | ADR-002 · ADR-003 · ADR-022 | PRD-SHR-003 |
| BypassAuth kapsamı | ADR-008 | PRD-SHR-004 |
| CSRF · RateLimit · Session | ADR-010 · ADR-013 · ADR-011 | Alt PRD #10 (Middleware) |
| SPA router deseni | ADR-021 / ADR-083 | Alt PRD #9 (PageRouter) |
| AI/Bff decommission | ADR-030 · ADR-084 | §2 modül sayımı notu |
| Servis yerleşimi | ADR-039 | Paket tüketici matrisi |
| Yeni ADR numarası | `.ai/.decisions/index.md` — next: **111** | Paket sözleşmesi değişince |

> **eli10 (basit):** Mimari kararlar ADR'dedir; bu PRD yalnız bağ verir.
> **eli15 (detay):** Bağ, frozen ADR metninin tek sürüm kalmasını sağlar.

---

## 6. Risk

| # | Risk | Etki | Azaltma |
|---|------|------|---------|
| 1 | Subdomain iş kuralının `src/`'e gömülmesi | Paket şişer, sürüm kırılır | PRD-SHR-002 + review; kural ilgili subdomain'e |
| 2 | Symlink nedeniyle kırıcı değişimin anında üç siteye yansıması | Toplu kırılma | PRD-SHR-006 + tüketici testleri; değişiklik öncesi kapı |
| 3 | `config/domain.php` onaysız değişmesi | Yeni/eksik alan adı davranışı | PRD §2 dokunulmazlık + onay |
| 4 | BypassAuth kapsam genişletilmesi | Auth deliği | PRD-SHR-004 + ADR-008 |
| 5 | `composer.lock`'un elle düzenlenmesi | Ortam bağımsızlığı kaybı | Yalnız `composer update` |

> **eli10 (basit):** En büyük tehlike: ortak kutuya özel iş koymak ve kilidi elle bozmak.
> **eli15 (detay):** Her riskin azaltması bir kapıya bağlıdır; kapı atlanırsa risk üç siteye birden yayılır.

---

## 7. Sonraki Adım

1. #9–#17 (PageRouter · Middleware · Api · Security · Database · OAuth · Events · Cache · Session) alt PRD'leri bu dosyaya bağlanır.
2. #23 (`shared/database/migrations`) ve #24 (`shared/config`) alt PRD'leri şema/konfigürasyon sözleşmesini derinleştirir.
3. DECOMMISSIONED modüller (AI · Bff) için envanter notu `[[CONTEXT.md]]` §3.5'te taşınır; geri ekleme yalnız ADR ile.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
