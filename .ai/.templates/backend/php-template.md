---
title: "CoreMusic — PHP Backend Development Template"
type: template
category: template
date: 2026-09-06
updated: 2026-09-23
version: 2.0.0
status: active
authority: SSOT
---

# CoreMusic — PHP Backend Development Template

**Teknoloji:** PHP 8.4, strict_types=1
**Katman:** K8 (Servis) / K9 (API & Routing)
**Sorumlu Agent:** Backend Architect

**Zorunlu Bağlantılar:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]] · [[CLAUDE.md]] · [[brain.md]] · [[WORKFLOW.md]] · [[architecture/k8-servis/README.md]]

---

## §1 Amaç

Bu şablon, CoreMusic backend geliştirme standartlarını ve kod iskeletlerini tanımlar; **Guardrail #16** gereği yeni PHP backend dosyası (Controller / Service / Repository / Middleware / Validation) bu şablondan üretilmek ZORUNLUDUR. Şablon; ADR-002 (PDO mandatory), ADR-010 (csrf_token), ADR-011/012/013 (oturum, CSP, rate limit), ADR-022 (şifreleme) ve ADR-040 (18 BCNF veritabanı) kararlarını kod iskeletine gömer. **ADR-042 hibrit kuralı:** `.templates/*` dosyaları tam yeniden yazıma açıktır (bu dosya v2.0.0 ile yeniden yazıldı).

| Alan | Değer |
|------|-------|
| Template Name | `php-template.md` |
| Template Path | `.ai/.templates/backend/php-template.md` |
| Hedef Dosya Tipi | PHP 8.4 backend dosyaları (Controller, Service, Repository, Model, Middleware, Validation, Config) |
| Guardrail | #16 (Template Mandatory) |
| Birincil Yazar | Backend Architect |
| İkincil Yazarlar | Security Engineer (middleware/CSRF), Data Engineer (Repository/BCNF), QA Engineer (test edilebilirlik) |
| Katmanlar | K8 (Servis) · K9 (API & Routing) |
| Kanıt — Kod | `shared/src/**` (PageRouter, Bff, Api, Database, Middleware), 4× `composer.json` (shared, home, auth) |
| Kanıt — Veritabanı | `.ai/.sql/mysql/*.sql` — 18 BCNF şema dosyası (glob kanıtı) |
| Kanıt — Mimari | `architecture/k8-servis/README.md` |
| Korunan İskelet | H1 + §1 Hard Guardrails → §10 İlgili Dokümanlar (10 bölüm) + 7 kod bloğu |
| Değişken Formatı | `{{VARIABLE}}` (MODULE, MIDDLEWARE, TABLE, COLUMN_1, COLUMN_2, TITLE, DATE, VERSION) |
| Dil | Türkçe (ç ğ ı İ ö ş ü doğru; mojibake YASAK); kod/sınıf/tablo adı İngilizce |
| Versiyon | 2.0.0 (Vault Refactor Engine yeniden yazımı) |
| Authority | SSOT (bu dosya); üretilen dosya kendi authority değerini taşır |
| Governance | Red Team · Human Mode · Truth Mode |
| Kayıt Defteri | `.ai/.templates/index.md` (envanter SRP — bu şablonda tekrarlanmaz) |
| İlişkili Şablonlar | `testing/phpunit-template.md`, `infrastructure/migration-template.md` |
| Son Kontrol | 2026-09-23 |

---

## §2 Kapsam

Şablonun kapsadığı ve kapsam dışında bıraktığı alanlar. Kapsam sınırı, katman kuralını korur: PHP backend kodu K8/K9'da yaşar, veri erişimi Data Engineer'a, test QA Engineer'a aittir.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `src/Controller/`, `src/Service/`, `src/Repository/`, `src/Model/`, `src/Middleware/`, `src/Validation/`, `src/Config/` altındaki PHP 8.4 dosyaları | Frontend dosyaları (→ `[[../frontend/js-template]]`, `[[../frontend/css-template]]`) |
| K8 Servis / K9 API & Routing katmanları | Test dosyaları (→ `[[../testing/phpunit-template]]`) |
| Controller / Service / Repository / Middleware / Validation kod iskeletleri | Migration / şema dosyaları (→ `[[../infrastructure/migration-template]]`) |
| BCNF veritabanı kuralları (18 DB) | CI/CD pipeline tanımları (→ `[[../infrastructure/github-actions-template]]`) |
| Hard Guardrail tablosu (8 madde) + ADR referansları | Sorgu şablonları (→ `[[../query/Query-Template]]`) |
| `{{PLACEHOLDER}}` doldurma ve Guardrail #16 doğrulaması | Node.js backend (→ `[[./nodejs-template]]`) |
| Wiki-link ile çapraz referans | API dokümanı üretimi (→ `[[../documentation/api-doc-template]]`) |
| Türkçe doğruluk + mojibake denetimi | `.ai/log.md` append-only kayıt (üst görevin işi) |

**Dosya tipi:** PHP 8.4 · **Uzantı:** `.php` · **Standart:** PSR-12 · **Guardrail:** #16

---

## §3 Mimari

Şablonun tam iskeleti (placeholder'lı frontmatter + H1 + künye + dosya yapısı + 7 kod şablonu + BCNF + ADR/doküman bölümleri, eksiksiz). Dört-çit ` ```markdown ` bloğu iskeletin kendi üç-çit kod bloklarını korur.

````markdown
---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — PHP Backend Development Template"
type: backend-template
category: template
date: {{DATE}}
updated: {{DATE}}
status: draft
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# {{TITLE}}

**Teknoloji:** PHP 8.4, strict_types=1
**Katman:** K8 (Servis) / K9 (API & Routing)
**Sorumlu Agent:** Backend Architect

---

## 1. Hard Guardrails (Kesinlikle Yasak)

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | `declare(strict_types=1)` zorunlu | Kod geçersiz |
| 2 | ORM yasak (ADR-002) — Sadece PDO | SQL injection riski |
| 3 | `SELECT *` yasak — Açık sütun listesi | SQL injection riski |
| 4 | Prepared statement zorunlu | SQL injection riski |
| 5 | CSRF token = `csrf_token` (ADR-010) | CSRF bozulması |
| 6 | Middleware sırası değişmez (ADR-010/011/012/013/022) | CSP/CSRF bozulması |
| 7 | Hardcoded secret yasak — `.env` / credential vault | Veri sızıntısı |
| 8 | PSR-12 kodlama standartları | Kod tutarsızlığı |

---

## 2. Dosya Yapısı

```
src/
├── Controller/
│   ├── {{MODULE}}Controller.php
│   └── AbstractController.php
├── Service/
│   ├── {{MODULE}}Service.php
│   └── Interface/
│       └── {{MODULE}}ServiceInterface.php
├── Repository/
│   ├── {{MODULE}}Repository.php
│   └── Interface/
│       └── {{MODULE}}RepositoryInterface.php
├── Model/
│   ├── {{MODULE}}.php
│   └── DTO/
│       └── {{MODULE}}DTO.php
├── Middleware/
│   └── {{MIDDLEWARE}}Middleware.php
├── Validation/
│   └── {{MODULE}}Validator.php
└── Config/
    └── {{MODULE}}.php
```

---

## 3. Controller Şablonu

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Controller;

use CoreMusic\Service\Interface\{{MODULE}}ServiceInterface;
use CoreMusic\Validation\{{MODULE}}Validator;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};

final class {{MODULE}}Controller
{
    public function __construct(
        private readonly {{MODULE}}ServiceInterface $service,
        private readonly {{MODULE}}Validator $validator,
    ) {}

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        // 1. Request validation
        // 2. Service call
        // 3. Response formatting
    }

    public function show(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        // 1. Validation
        // 2. Service call
        // 3. Response
    }

    public function store(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();
        // 1. Validation
        // 2. Service call
        // 3. Response (201 Created)
    }

    public function update(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $data = $request->getParsedBody();
        // 1. Validation
        // 2. Service call
        // 3. Response
    }

    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        // 1. Service call
        // 2. Response (204 No Content)
    }
}
```

---

## 4. Service Şablonu

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Service;

use CoreMusic\Repository\Interface\{{MODULE}}RepositoryInterface;
use CoreMusic\Service\Interface\{{MODULE}}ServiceInterface;

final class {{MODULE}}Service implements {{MODULE}}ServiceInterface
{
    public function __construct(
        private readonly {{MODULE}}RepositoryInterface $repository,
    ) {}

    public function findAll(array $filters = []): array
    {
        return $this->repository->findAll($filters);
    }

    public function findById(int $id): ?array
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): array
    {
        // 1. Business logic validation
        // 2. Data transformation
        // 3. Repository call
        // 4. Return created entity
    }

    public function update(int $id, array $data): array
    {
        // 1. Existence check
        // 2. Business logic validation
        // 3. Repository call
        // 4. Return updated entity
    }

    public function delete(int $id): bool
    {
        // 1. Existence check
        // 2. Business logic validation
        // 3. Repository call
        // 4. Return success
    }
}
```

---

## 5. Repository Şablonu

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Repository;

use CoreMusic\Repository\Interface\{{MODULE}}RepositoryInterface;
use PDO;

final class {{MODULE}}Repository implements {{MODULE}}RepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo,
    ) {}

    public function findAll(array $filters = []): array
    {
        $sql = 'SELECT id, {{COLUMN_1}}, {{COLUMN_2}}, created_at
                FROM {{TABLE}}
                WHERE is_deleted = 0';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT id, {{COLUMN_1}}, {{COLUMN_2}}, created_at
                FROM {{TABLE}}
                WHERE id = :id AND is_deleted = 0';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function insert(array $data): int
    {
        $sql = 'INSERT INTO {{TABLE}} ({{COLUMN_1}}, {{COLUMN_2}}, created_at)
                VALUES (:{{COLUMN_1}}, :{{COLUMN_2}}, NOW())';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE {{TABLE}}
                SET {{COLUMN_1}} = :{{COLUMN_1}}, {{COLUMN_2}} = :{{COLUMN_2}}, updated_at = NOW()
                WHERE id = :id AND is_deleted = 0';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_merge($data, ['id' => $id]));
        return $stmt->rowCount() > 0;
    }

    public function softDelete(int $id): bool
    {
        $sql = 'UPDATE {{TABLE}} SET is_deleted = 1, deleted_at = NOW()
                WHERE id = :id AND is_deleted = 0';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
```

---

## 6. Middleware Şablonu

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};

final class {{MIDDLEWARE}}Middleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // 1. Pre-processing
        // 2. Call next handler
        // 3. Post-processing
        return $handler->handle($request);
    }
}
```

---

## 7. Validation Şablonu

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Validation;

use Respect\Validation\{Validator, Exception};

final class {{MODULE}}Validator
{
    public function validateCreate(array $data): array
    {
        $errors = [];

        try {
            Validator::stringType()->length(1, 255)->assert($data['{{COLUMN_1}}'] ?? '');
        } catch (Exception\NestedValidationException $e) {
            $errors['{{COLUMN_1}}'] = $e->getMessages();
        }

        return $errors;
    }

    public function validateUpdate(array $data): array
    {
        return $this->validateCreate($data);
    }
}
```

---

## 8. BCNF Veritabanı Kuralları

| Kural | Açıklama |
|-------|----------|
| BCNF zorunlu | 18 veritabanı BCNF kurallarına uymalıdır |
| Soft delete | `is_deleted = 0` koşulu her sorguda olmalı |
| Snake_case | Tablo ve sütun isimleri snake_case |
| Timestamp | `created_at`, `updated_at`, `deleted_at` zorunlu |
| Prepared statement | PDO prepared statement zorunlu |
| No ORM | Doctrine DBAL veya raw PDO |

---

## 9. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-002 | PDO mandatory, ORM yasak |
| ADR-010 | csrf_token key zorunlu |
| ADR-011 | COREMUSIC_SESS, 3600s idle timeout |
| ADR-012 | strict-dynamic, nonce-based CSP |
| ADR-013 | APCu, 60 req/60s |
| ADR-022 | AES-256-GCM, Argon2id |
| ADR-040 | 18 BCNF veritabanı otoritesi |

---

## 10. İlgili Dokümanlar

| Dosya | Amaç |
|-------|------|
| [[CLAUDE.md]] | Ana sözleşme |
| [[brain.md]] | Mimari kararlar |
| [[WORKFLOW.md]] | Süreçler |
| [[architecture/k8-servis/README.md]] | Servis mimarisi |

---

*PHP Backend Template v1.0.0 — CoreMusic Development Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
````

---

## §4 Kurallar

Hard Guardrails (iskelet §1 — kesinlikle yasak, ihlalde kod revert edilir). 8 madde birebir korunmuştur.

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | `declare(strict_types=1)` zorunlu | Kod geçersiz |
| 2 | ORM yasak (ADR-002) — Sadece PDO | SQL injection riski |
| 3 | `SELECT *` yasak — Açık sütun listesi | SQL injection riski |
| 4 | Prepared statement zorunlu | SQL injection riski |
| 5 | CSRF token = `csrf_token` (ADR-010) | CSRF bozulması |
| 6 | Middleware sırası değişmez (ADR-010/011/012/013/022) | CSP/CSRF bozulması |
| 7 | Hardcoded secret yasak — `.env` / credential vault | Veri sızıntısı |
| 8 | PSR-12 kodlama standartları | Kod tutarsızlığı |

**BCNF Veritabanı Kuralları (iskelet §8 — 6 madde):**

| Kural | Açıklama |
|-------|----------|
| BCNF zorunlu | 18 veritabanı BCNF kurallarına uymalıdır (`.ai/.sql/mysql/*.sql` — 18 dosya kanıtı) |
| Soft delete | `is_deleted = 0` koşulu her sorguda olmalı |
| Snake_case | Tablo ve sütun isimleri snake_case |
| Timestamp | `created_at`, `updated_at`, `deleted_at` zorunlu |
| Prepared statement | PDO prepared statement zorunlu |
| No ORM | Doctrine DBAL veya raw PDO |

**Genel kurallar:**

1. **Guardrail #16:** Yeni PHP backend dosyası bu şablondan üretilir; dış iskelet (H1 + §1-§10) silinemez.
2. **Katman sınırı:** K8/K9 dışında veri erişimi (raw SQL dosyaları) → Data Engineer'a handover (`[[../../AGENTS.md]]` §9.3).
3. **İlgili ADR'ler:** ADR-002 · ADR-010 · ADR-011 · ADR-012 · ADR-013 · ADR-022 · ADR-040 (iskelet §9 tablosu korunur).
4. **Belirsizlik:** Bilinmeyen class/API `// ⚠️ VERIFICATION REQUIRED` ile işaretlenir; uydurma API yazılmaz.
5. **Placeholder disiplini:** `{{MODULE}}`, `{{MIDDLEWARE}}`, `{{TABLE}}`, `{{COLUMN_1}}`, `{{COLUMN_2}}`, `{{TITLE}}`, `{{DATE}}` doldurulmadan commit yasak.
6. **SRP:** Envanter/envanter satırı bu şablonda tekrarlanmaz → `[[.templates/index]]`.
7. **DIP:** Routing/escalation değerleri `[[../../AGENTS.md]]` dosyasından okunur.
8. **DRY:** Kategori § başlıkları birebir aynıdır (§1 Amaç → §7 Referanslar).
9. **YAGNI:** Uydurma dosya/yol adı yazılmaz; kanıt `shared/src/**` ve `.ai/.sql/mysql/*.sql` glob'larıdır.
10. **Yazım:** Türkçe karakterler (ç ğ ı İ ö ş ü) doğru; mojibake YASAK; kod adları İngilizce.

---

## §5 Workflow

```
ŞABLONU SEÇ → KOPYALA → {{PLACEHOLDER}} DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT
```

| Adım | Eylem | Detay | Çıktı |
|------|-------|-------|-------|
| 1 | ŞABLONU SEÇ | `.ai/.templates/backend/php-template.md` | Şablon kopyası |
| 2 | KOPYALA | İlgili katman dizinine (Controller/Service/Repository/Middleware/Validation/Config) kopyala | Yeni dosya iskeleti |
| 3 | DOLDUR | `{{MODULE}}`, `{{MIDDLEWARE}}`, `{{TABLE}}`, `{{COLUMN_1}}`, `{{COLUMN_2}}`, `{{TITLE}}`, `{{DATE}}` alanlarını gerçek modül/sütun/tarihlerle değiştir; Interface ve DTO dosyalarını ekle | Dolu PHP dosyaları |
| 4 | DOĞRULA | §6 kontrol listesi + §4 Hard Guardrails (8 madde) + BCNF (6 madde) | 8/8 gate |
| 5 | COMMIT | PSR-12 + test (`[[../testing/phpunit-template]]`) ile commit; registry (`[[.templates/index]]`) + log güncel | Vault senkronu |

**Adım 3 detayı — doldurma sırası:** (a) dosya yapısındaki tüm `{{MODULE}}` adlarını değiştir, (b) `{{MIDDLEWARE}}` adını yaz, (c) Repository'de `{{TABLE}}` + `{{COLUMN_*}}` sütunlarını gerçek BCNF şemasıyla eşleştir (`.ai/.sql/mysql/*.sql`), (d) Controller/Service metot gövdelerini doldur, (e) Validation kurallarını `{{COLUMN_*}}` için yaz, (f) `{{TITLE}}` ve `{{DATE}}` künyesini güncelle, (g) Interface ve DTO dosyalarını oluştur.

---

## §6 Doğrulama

Dosya commit edilmeden önce kalite kapıları sırayla kontrol edilir; tek bir madde bile ✅ değilse commit yapılmaz.

| # | Kontrol | Beklenen | Durum |
|---|---------|----------|-------|
| 1 | Frontmatter 7 zorunlu alan var mı? | title, type, category, date, updated, version, status, authority | ✅/❌ |
| 2 | H1 + §1-§10 iskeleti eksiksiz mi? | 10 bölüm silinmemiş | ✅/❌ |
| 3 | Tüm `{{PLACEHOLDER}}`'lar dolduruldu mu? | MODULE, TABLE, COLUMN_*, TITLE, DATE dahil | ✅/❌ |
| 4 | `declare(strict_types=1)` her dosyada var mı? | 8/8 Hard Guardrail #1 | ✅/❌ |
| 5 | ORM / `SELECT *` / hardcoded secret yok mu? | Guardrail #2, #3, #7 | ✅/❌ |
| 6 | Prepared statement + açık sütun listesi kullanıldı mı? | Guardrail #3, #4 | ✅/❌ |
| 7 | CSRF token = `csrf_token` yazıldı mı? | Guardrail #5 (ADR-010) | ✅/❌ |
| 8 | Middleware sırası korundu mu? | Guardrail #6 (ADR-010/011/012/013/022) | ✅/❌ |
| 9 | PSR-12 uyumu sağlandı mı? | Guardrail #8 | ✅/❌ |
| 10 | BCNF 6 madde tamam mı? | is_deleted, snake_case, timestamp, prepared, no ORM | ✅/❌ |
| 11 | 7 kod bloğu korundu mu? | Controller, Service, Repository, Middleware, Validation + dosya yapısı | ✅/❌ |
| 12 | Wiki-link'ler hedefe ulaşıyor mu? | `[[relative/path]]` formatı, kırık link yok | ✅/❌ |
| 13 | Türkçe doğruluk + mojibake yok mu? | ç ğ ı İ ö ş ü doğru; Ã- kalıntısı yok | ✅/❌ |
| 14 | Doğrulanamayan alan işaretlendi mi? | `⚠️ VERIFICATION REQUIRED` | ✅/❌ |
| 15 | Secret / credential yazılmadı mı? | REDACTED politikası | ✅/❌ |
| 16 | Envanter SRP ihlali yok mu? | Envanter `[[.templates/index]]`'de | ✅/❌ |
| 17 | § başlıkları kategori tutarlılığına uygun mu? | §1 Amaç → §7 Referanslar (DRY) | ✅/❌ |

**REFACTOR REPORT:** FILE: php-template.md · PURPOSE: PHP backend geliştirme standardı + kod iskeletleri (Guardrail #16) · VALIDATION: 7 alan + §1-§7 + bilgi korunumu (48 placeholder, 7 kod bloğu, 8 Hard Guardrail, 6 BCNF, 7 ADR korundu) · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

---

## §7 Referanslar

| Kaynak | Wiki-Link | Rol |
|--------|-----------|-----|
| Template Registry | [[.templates/index]] | Bu şablonun kaydı (envanter SRP) |
| Vault ana sözleşmesi | [[../CLAUDE.md]] | 16 Hard Guardrail, Guardrail #16 kaynağı |
| Agent Registry (SSOT) | [[../../AGENTS.md]] | Routing §6 (PHP → Backend Architect), handover §9.3 |
| Ana sözleşme (iskelet §10) | [[CLAUDE.md]] | İlgili Dokümanlar tablosu |
| Mimari kararlar (iskelet §10) | [[brain.md]] | ADR özetleri |
| Süreçler (iskelet §10) | [[WORKFLOW.md]] | Fazlar, session protokolü |
| Servis mimarisi (iskelet §10) | [[architecture/k8-servis/README.md]] | K8 katmanı |
| Kod kanıtı | `shared/src/**` | PageRouter, Bff, Api, Database, Middleware |
| BCNF şema kanıtı | `.ai/.sql/mysql/*.sql` | 18 veritabanı (glob kanıtı) |
| Eşdeğer backend şablonu | [[./nodejs-template]] | Node.js / TypeScript alternatifi |
| Test şablonu | [[../testing/phpunit-template]] | PHPUnit 11 test iskeleti |
| Migration şablonu | [[../infrastructure/migration-template]] | Şema geçişleri |

**İlgili ADR'ler:** ADR-002 (PDO, ORM yasak) · ADR-010 (csrf_token) · ADR-011 (COREMUSIC_SESS, 3600s) · ADR-012 (strict-dynamic CSP) · ADR-013 (APCu, 60 req/60s) · ADR-022 (AES-256-GCM, Argon2id) · ADR-040 (18 BCNF otoritesi)

---

**Template Version:** 2.0.0
**Last Updated:** 2026-09-23
