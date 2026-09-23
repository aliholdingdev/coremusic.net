---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — PHPUnit Test Template"
type: testing-template
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

**Teknoloji:** PHPUnit 11, PHP 8.4, strict_types
**Kapsam:** Backend unit test
**Hedef Coverage:** ≥80% (minimum), ≥90% (hedef)

---

## 1. Dosya Yapısı

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

## 2. Test Şablonu

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

## 3. Controller Test Şablonu

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

## 4. phpunit.xml Konfigürasyonu

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

## 5. Çalıştırma

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

*PHPUnit Test Template v1.0.0 — CoreMusic Testing Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
