---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — PHPUnit Test Template"
type: testing-template
category: template
version: 2.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-23
date: 2026-09-23
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# CoreMusic — PHPUnit Test Template

**Zorunlu Bağlantılar / See also:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]]

## 1. Amaç

CoreMusic backend unit/integration testlerini standartlaştırmaktır: test dosya yapısı, Service ve Controller test metodu iskeletleri (mock repository/service/validator, Arrange-Act-Assert, dataProvider), `phpunit.xml` konfigürasyonu ve çalıştırma komutlarını tek şablonda sunar. Kaynak: `reference_doc: Freelancer Technical Documentation v1.0`.

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `tests/**/*.php` PHPUnit 11 unit/integration testleri | Üretim kodu (Controller/Service/Repository) |
| `phpunit.xml` konfigürasyonu + coverage raporu | Frontend testleri (bkz. vitest-template) |
| Mock, assertion, dataProvider kalıpları | E2E testleri (Playwright) |

- **Dosya tipi:** PHP test dosyası + Markdown şablon dokümanı
- **Teknoloji:** PHPUnit 11, PHP 8.4, strict_types
- **Kullanan agent:** QA Engineer (birincil · AGENTS.md §6: test, coverage, PHPUnit), Backend Architect (ikincil)
- **Hedef Coverage:** ≥80% (minimum), ≥90% (hedef) · **Guardrail:** #16 (Template Mandatory)

## 3. Mimari

Şablonun tam gövdesi. Not: gömme nedeniyle şablon başlıkları iki seviye derinleştirilmiştir (H1 → `###`, H2 → `####`); tüm `{{PLACEHOLDER}}`, PHP/XML/bash kod blokları ve `//` yorum satırları (`// Arrange`, `// Act`, `// Assert`) birebir korunmuştur. Coverage ve test standartları §4.1'dedir.

### {{TITLE}}

**Teknoloji:** PHPUnit 11, PHP 8.4, strict_types
**Kapsam:** Backend unit test
**Hedef Coverage:** ≥80% (minimum), ≥90% (hedef)

---

#### 3.1 Dosya Yapısı

```
tests/
├── Unit/
│   ├── Controller/
│   │   └── {{MODULE}}ControllerTest.php
│   ├── Service/
│   │   └── {{MODULE}}ServiceTest.php
│   └── Repository/
│       └── {{MODULE}}RepositoryTest.php
├── Integration/
│   └── {{MODULE}}IntegrationTest.php
├── Fixtures/
│   └── {{MODULE}}Fixture.php
└── bootstrap.php
```

---

#### 3.2 Test Şablonu (Service)

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Tests\Unit\{{MODULE}};

use PHPUnit\Framework\TestCase;
use CoreMusic\Service\{{MODULE}}Service;
use CoreMusic\Repository\Interface\{{MODULE}}RepositoryInterface;

final class {{MODULE}}ServiceTest extends TestCase
{
    private {{MODULE}}Service $service;
    private $mockRepository;

    protected function setUp(): void
    {
        $this->mockRepository = $this->createMock(
            {{MODULE}}RepositoryInterface::class
        );
        $this->service = new {{MODULE}}Service($this->mockRepository);
    }

    public function testFindAllReturnsArray(): void
    {
        // Arrange
        $expected = [
            ['id' => 1, 'name' => 'Test'],
            ['id' => 2, 'name' => 'Test 2'],
        ];
        $this->mockRepository
            ->method('findAll')
            ->willReturn($expected);

        // Act
        $result = $this->service->findAll();

        // Assert
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals($expected, $result);
    }

    public function testFindByIdReturnsEntity(): void
    {
        // Arrange
        $expected = ['id' => 1, 'name' => 'Test'];
        $this->mockRepository
            ->method('findById')
            ->with(1)
            ->willReturn($expected);

        // Act
        $result = $this->service->findById(1);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals(1, $result['id']);
    }

    public function testFindByIdReturnsNullForNonexistent(): void
    {
        // Arrange
        $this->mockRepository
            ->method('findById')
            ->with(999)
            ->willReturn(null);

        // Act
        $result = $this->service->findById(999);

        // Assert
        $this->assertNull($result);
    }

    public function testCreateReturnsCreatedEntity(): void
    {
        // Arrange
        $data = ['name' => 'New Item'];
        $this->mockRepository
            ->method('insert')
            ->with($data)
            ->willReturn(1);

        // Act
        $result = $this->service->create($data);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
    }

    public function testDeleteReturnsTrue(): void
    {
        // Arrange
        $this->mockRepository
            ->method('softDelete')
            ->with(1)
            ->willReturn(true);

        // Act
        $result = $this->service->delete(1);

        // Assert
        $this->assertTrue($result);
    }

    public function testDeleteReturnsFalseForNonexistent(): void
    {
        // Arrange
        $this->mockRepository
            ->method('softDelete')
            ->with(999)
            ->willReturn(false);

        // Act
        $result = $this->service->delete(999);

        // Assert
        $this->assertFalse($result);
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testCreateWithInvalidDataThrowsException(array $data): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->service->create($data);
    }

    public static function invalidDataProvider(): array
    {
        return [
            'empty name' => [['name' => '']],
            'missing name' => [[]],
            'name too long' => [['name' => str_repeat('a', 256)]],
        ];
    }
}
```

---

#### 3.3 Controller Test Şablonu

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Tests\Unit\Controller;

use PHPUnit\Framework\TestCase;
use CoreMusic\Controller\{{MODULE}}Controller;
use CoreMusic\Service\Interface\{{MODULE}}ServiceInterface;
use CoreMusic\Validation\{{MODULE}}Validator;
use Psr\Http\Message\ServerRequestInterface;

final class {{MODULE}}ControllerTest extends TestCase
{
    private {{MODULE}}Controller $controller;
    private $mockService;
    private $mockValidator;

    protected function setUp(): void
    {
        $this->mockService = $this->createMock(
            {{MODULE}}ServiceInterface::class
        );
        $this->mockValidator = $this->createMock(
            {{MODULE}}Validator::class
        );
        $this->controller = new {{MODULE}}Controller(
            $this->mockService,
            $this->mockValidator
        );
    }

    public function testIndexReturns200(): void
    {
        // Arrange
        $request = $this->createMock(ServerRequestInterface::class);
        $this->mockService
            ->method('findAll')
            ->willReturn([]);

        // Act
        $response = $this->controller->index($request);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testShowReturns200ForExisting(): void
    {
        // Arrange
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getAttribute')->with('id')->willReturn(1);
        $this->mockService
            ->method('findById')
            ->with(1)
            ->willReturn(['id' => 1, 'name' => 'Test']);

        // Act
        $response = $this->controller->show($request);

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testShowReturns404ForNonexistent(): void
    {
        // Arrange
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getAttribute')->with('id')->willReturn(999);
        $this->mockService
            ->method('findById')
            ->with(999)
            ->willReturn(null);

        // Act
        $response = $this->controller->show($request);

        // Assert
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testStoreReturns201(): void
    {
        // Arrange
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getParsedBody')->willReturn(['name' => 'New']);
        $this->mockValidator
            ->method('validateCreate')
            ->willReturn([]);
        $this->mockService
            ->method('create')
            ->willReturn(['id' => 1, 'name' => 'New']);

        // Act
        $response = $this->controller->store($request);

        // Assert
        $this->assertEquals(201, $response->getStatusCode());
    }
}
```

---

#### 3.4 phpunit.xml Konfigürasyonu

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
         verbose="true"
         stopOnFailure="false"
         failOnRisky="true"
         failOnWarning="true">

    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory>tests/Integration</directory>
        </testsuite>
    </testsuites>

    <source>
        <include>
            <directory>src</directory>
        </include>
    </source>

    <coverage>
        <report>
            <html outputDirectory="coverage/html"/>
            <text outputFile="coverage.txt"/>
            <clover outputFile="coverage.xml"/>
        </report>
    </coverage>

    <php>
        <ini name="memory_limit" value="512M"/>
        <env name="APP_ENV" value="testing"/>
        <env name="DB_DATABASE" value="coremusic_test"/>
    </php>
</phpunit>
```

---

#### 3.5 Çalıştırma

```bash
# Tüm testler
./vendor/bin/phpunit

# Sadece unit testler
./vendor/bin/phpunit --testsuite Unit

# Coverage ile
./vendor/bin/phpunit --coverage-html coverage/html

# Tek dosya
./vendor/bin/phpunit tests/Unit/Service/{{MODULE}}ServiceTest.php
```

---

## 4. Kurallar

Zorunlu / yasak kurallar ve kod standartları:

#### 4.1 Test & Coverage Standartları

| Kural | Değer |
|-------|-------|
| Hedef Coverage | ≥80% (minimum), ≥90% (hedef) |
| Risky/warning test | `failOnRisky="true"`, `failOnWarning="true"` |
| Sınıf yapısı | `final class … extends TestCase` + `declare(strict_types=1)` |
| Mock hedefi | Yalnızca interface (`*Interface::class`) |
| Metot kalıbı | `// Arrange` → `// Act` → `// Assert` |
| Assertion'sız test | Yasak (risky kabul edilir) |

Ek kurallar:

- **Zorunlu:** her test metodu bir Arrange-Act-Assert akışı izler; en az bir assertion içerir (§3.2, §3.3).
- **Zorunlu:** mock'lar `setUp(): void` içinde oluşturulur; `createMock()` yalnızca interface'lere uygulanır (`{{MODULE}}RepositoryInterface`, `{{MODULE}}ServiceInterface`).
- **Zorunlu:** parametreli testler `@dataProvider` + `public static function …Provider(): array` ile yazılır (§3.2 `invalidDataProvider`).
- **Zorunlu:** `APP_ENV=testing`, `DB_DATABASE=coremusic_test` (§3.4); testler CI'daki `php-test` job'ında `--coverage-clover=coverage.xml` ile çalışır (github-actions-template §3.1).
- **Yasak:** `{{TITLE}}`, `{{MODULE}}`, `{{DATE}}` placeholder'ları doldurulmadan test dosyası commit edilemez; assertion'sız/`assertTrue(true)` gibi boş test yazılamaz.
- **Uyarı:** coverage %80 altına düşerse AGENTS.md §10.1 eskalasyonu (L1 QA → L2, timeout 60s); bilinmeyen behavior `⚠️ VERIFICATION REQUIRED`.

## 5. Workflow

```
ŞABLONU SEÇ → KOPYALA → {{PLACEHOLDER}} DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT
```

1. **ŞABLONU SEÇ:** `.ai/.templates/testing/phpunit-template.md` (Guardrail #16).
2. **KOPYALA:** §3.1 dosya yapısına göre `tests/Unit/...` altına test dosyası oluştur; §3.4 `phpunit.xml` konfigürasyonunu kopyala.
3. **{{PLACEHOLDER}} DOLDUR:** `{{TITLE}}`, `{{MODULE}}` (namespace, class, import, dataProvider yolları dahil).
4. **GUARDRAIL #16 DOĞRULA:** 7 alanlı frontmatter + §1-§7 + tüm placeholder'lar doldu + §4.1 standartları (coverage ≥80%, assertion'lı, interface mock) geçti.
5. **COMMIT:** `./vendor/bin/phpunit --coverage-html coverage/html` ile yerelde doğrula; CI `php-test` job geçmeli; `log.md`'ye giriş ekle.

## 6. Doğrulama

- [ ] 7 alanlı frontmatter var (title, type, category, version, status, authority, updated)
- [ ] §1-§7 var
- [ ] tüm {{PLACEHOLDER}}'lar dolduruldu
- [ ] dosya bu şablona uygun
- [ ] Coverage ≥80%; her test Arrange-Act-Assert + assertion içeriyor; mock'lar interface'

**REFACTOR REPORT:** FILE: phpunit-template.md · PURPOSE: PHPUnit Test Template · VALIDATION: 7 alan + §1-§7 + bilgi korunumu · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

## 7. Referanslar

- [[.templates/index]] — şablon registry (`.ai/.templates/index.md`)
- [[../CLAUDE.md]] — AI anayasası, 16 Hard Guardrail
- [[../../AGENTS.md]] — routing (§6: test/PHPUnit → QA Engineer), kalite standardı §16 (coverage ≥80%, flaky %0), eskalasyon §10.1 (coverage < %80)
- `.ai/CLAUDE.md` · `.ai/AGENTS.md` · `.ai/brain.md` (frontmatter `reference`)
- `reference_doc: Freelancer Technical Documentation v1.0`

---

*PHPUnit Test Template v2.0.0 — CoreMusic Testing Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
