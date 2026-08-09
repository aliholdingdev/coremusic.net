---
type: architecture
category: contracts
title: "API Testing Strategy"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# API Testing Strategy

**Zorunlu Bağlantılar:** [[api-architecture-master]] · [[testing/strategy]] · [[api-public-contract]] · [[api-internal-contract]]

---

## 1. Amaç

CoreMusic API test stratejisini, test piramidini, coverage hedeflerini ve test otomasyonu süreçlerini tanımlayan **Tek Doğruluk Kaynağıdır (SSOT)**. Bu belge, API'lerin güvenilir, performanslı ve güvenli olmasını garanti altına alır.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| API contract testing (Pact) | Frontend unit testleri |
| Integration test (gerçek DB) | C++ audio engine testleri |
| E2E test (Playwright) | Donanım testleri |
| Load testing | Mobil uygulama testleri |
| Security testing (OWASP ZAP) | Manuel test süreçleri |
| Test data management | — |

---

## 3. Test Piramidi

```
           ┌───────────────┐
           │   E2E Tests   │  %10
           │  (Playwright) │  10 test
           ├───────────────┤
           │ Integration   │  %20
           │  Tests (DB)   │  20 test
           ├───────────────┤
           │  Unit Tests   │  %70
           │  (PHPUnit)    │  70 test
           └───────────────┘
```

### 3.1 Test Dağılımı

| Test Tipi | Kapsam | Adet | Çalışma Süresi |
|-----------|--------|------|---------------|
| Unit Test | Tek method/class | ~70 | <1ms/test |
| Integration Test | Servis + DB | ~20 | <100ms/test |
| E2E Test | Tam akış (browser) | ~10 | <5s/test |
| **Toplam** | — | **~100** | **~30s** |

---

## 4. Unit Testler (PHPUnit 11)

### 4.1 Kapsam

| Modül | Test Tipi | Örnek |
|-------|-----------|-------|
| Auth Controller | Input validation | Geçersiz email → 422 |
| Media Repository | Query building | Pagination query |
| Session Manager | Token oluşturma | Token formatı |
| CSRF Middleware | Token doğrulama | Geçersiz token → 403 |
| Rate Limiter | Sayaç artırma | Limit aşımı → 429 |

### 4.2 Test Örneği (PHPUnit)

```php
class AuthControllerTest extends TestCase
{
    public function testLoginReturnsTokenOnValidCredentials(): void
    {
        // Arrange
        $request = new LoginRequest('user@test.com', 'password123');
        $authService = $this->createMock(AuthService::class);
        $authService->method('authenticate')
            ->willReturn(new AuthToken('token_abc'));
        
        $controller = new AuthController($authService);
        
        // Act
        $response = $controller->login($request);
        
        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('token', $response->getData());
    }
    
    public function testLoginReturns401OnInvalidCredentials(): void
    {
        // Arrange
        $request = new LoginRequest('user@test.com', 'wrong');
        $authService = $this->createMock(AuthService::class);
        $authService->method('authenticate')
            ->willReturn(null);
        
        $controller = new AuthController($authService);
        
        // Act
        $response = $controller->login($request);
        
        // Assert
        $this->assertEquals(401, $response->getStatusCode());
    }
}
```

### 4.3 Unit Test Kuralları

| Kural | Açıklama |
|-------|----------|
| AAA Pattern | Arrange → Act → Assert |
| Tek Sorumluluk | Her test tek bir davranışı test eder |
| Mocking | Dış bağımlılıklar mock'lanır |
| Isolation | Testler birbirinden bağımsız |
| Naming | `test{Method}{Condition}{Expected}` |

---

## 5. API Contract Testing (Pact)

### 5.1 Pact Nedir?

Pact ile consumer-driven contract testleri yapılır. API sağlayıcı ve tüketici arasında sözleşme doğrulanır.

### 5.2 Contract Örneği

```php
// Consumer side (Frontend/SDK)
$builder = new PactBuilder();
$builder
    ->given('user exists')
    ->uponReceiving('a request for user media')
    ->with(new Request('GET', '/api/v1/users/123/media'))
    ->willRespondWith(
        new Response(200, [
            'Content-Type' => 'application/json'
        ], json_encode([
            'data' => [
                ['id' => 1, 'title' => 'Test Track']
            ],
            'total' => 1
        ]))
    );

// Provider side (API)
$provider = new PactProvider($builder->getMockService());
$provider->verify(); // Contract doğrulanır
```

### 5.3 Contract Test Kapsamı

| Consumer | Provider | Contract |
|----------|----------|----------|
| Frontend JS | Control Service | Auth endpoints |
| PHP SDK | Media Service | Media endpoints |
| Python SDK | Download Service | Download endpoints |
| Mobile App | Control Service | User endpoints |

---

## 6. Integration Testleri

### 6.1 Gerçek DB ile Test

```php
class MediaRepositoryIntegrationTest extends TestCase
{
    private PDO $db;
    private MediaRepository $repo;
    
    protected function setUp(): void
    {
        $this->db = new PDO(
            'mysql:host=127.0.0.1;dbname=coremusic_media_test',
            'test_user', 'test_password'
        );
        $this->repo = new MediaRepository($this->db);
        
        // Test verilerini yükle
        $this->db->exec(file_get_contents('tests/fixtures/media.sql'));
    }
    
    public function testFindByIdReturnsMedia(): void
    {
        $media = $this->repo->findById(1);
        
        $this->assertNotNull($media);
        $this->assertEquals('Test Track', $media->title);
    }
    
    public function testSearchReturnsMatchingResults(): void
    {
        $results = $this->repo->search('rock');
        
        $this->assertNotEmpty($results);
        $this->assertContainsOnly(Media::class, $results);
    }
    
    protected function tearDown(): void
    {
        // Test DB'yi temizle
        $this->db->exec('DELETE FROM media WHERE id > 0');
    }
}
```

### 6.2 Integration Test Senaryoları

| Senaryo | Servis | DB | Timeout |
|---------|--------|-----|---------|
| Login → Token al → Media listele | Control + Media | auth + media | 5s |
| Indirme başlat → Durum kontrol et | Download + Media | catalog + media | 10s |
| EQ ayarla → Playback başlat | Audio + Media | — | 3s |
| Kullanıcı kaydet → Profil güncelle | Control + User | auth + user | 3s |

---

## 7. E2E Testleri (Playwright)

### 7.1 Playwright Test Yapısı

```typescript
// tests/e2e/login.spec.ts
import { test, expect } from '@playwright/test';

test.describe('User Login', () => {
  test('should login with valid credentials', async ({ page }) => {
    // Arrange
    await page.goto('https://music.coremusic.net/login');
    
    // Act
    await page.fill('[data-testid="email"]', 'user@test.com');
    await page.fill('[data-testid="password"]', 'password123');
    await page.click('[data-testid="login-button"]');
    
    // Assert
    await expect(page).toHaveURL('/dashboard');
    await expect(page.locator('[data-testid="user-menu"]')).toBeVisible();
  });
  
  test('should show error on invalid credentials', async ({ page }) => {
    // Arrange
    await page.goto('https://music.coremusic.net/login');
    
    // Act
    await page.fill('[data-testid="email"]', 'wrong@test.com');
    await page.fill('[data-testid="password"]', 'wrongpass');
    await page.click('[data-testid="login-button"]');
    
    // Assert
    await expect(page.locator('[data-testid="error-message"]'))
      .toContainText('Invalid credentials');
  });
});
```

### 7.2 E2E Test Senaryoları

| Senaryo | Sayfa | Adım Sayısı | Timeout |
|---------|-------|-------------|---------|
| Kullanıcı girişi | /login | 4 | 10s |
| Medya arama | /search | 3 | 10s |
| Çalma listesi oluşturma | /playlists | 5 | 15s |
| İndirme başlatma | /downloads | 4 | 15s |
| Profil güncelleme | /settings | 4 | 10s |

---

## 8. Load Testing

### 8.1 Load Test Senaryoları

| Senaryo | Kullanıcı | Süre | Hedef |
|---------|-----------|------|-------|
| Media listeleme | 100 | 5 dk | <200ms p95 |
| Arama | 50 | 5 dk | <300ms p95 |
| Login | 30 | 2 dk | <100ms p95 |
| İndirme | 20 | 10 dk | <1s p95 |
| Concurrent streaming | 100 | 10 dk | <50ms p95 |

### 8.2 Load Test Araçları

| Araç | Kullanım | Entegrasyon |
|------|----------|-------------|
| k6 | API load test | CI/CD entegre |
| Artillery | Hafif load test | Manuel |
| Apache Bench | Basit bench | Geliştirme |
| Locust | Python tabanlı | Manuel |

### 8.3 Performance Eşikleri

| Metrik | Hedef | Kritik |
|--------|-------|--------|
| TTFB | <100ms | >500ms |
| p50 yanıt | <150ms | >500ms |
| p95 yanıt | <300ms | >1s |
| p99 yanıt | <500ms | >2s |
| Throughput | >100 req/s | <50 req/s |
| Error rate | <0.1% | >1% |

---

## 9. Security Testing (OWASP ZAP)

### 9.1 OWASP API Security Top 10:2023

| # | Zafiyet | Test Yöntemi |
|---|---------|-------------|
| API1 | Broken Object Level Auth | Object ID manipülasyonu |
| API2 | Broken Authentication | Token manipülasyonu |
| API3 | Broken Object Property Level Auth | Yetki dışı alan erişimi |
| API4 | Broken Resource Consumption | Rate limit bypass |
| API5 | Broken Function Level Auth | Yetki dışı endpoint erişimi |
| API6 | Unrestricted Access to Sensitive Flows | Korumalı akış bypass |
| API7 | Server Side Request Forgery | SSRF payload test |
| API8 | Security Misconfiguration | Config tarama |
| API9 | Improper Inventory Management | Eski API version test |
| API10 | Unsafe Consumption of APIs | External API test |

### 9.2 Automated Security Scans

```yaml
# .github/workflows/security.yml
name: API Security Scan
on: [push, pull_request]

jobs:
  zap-scan:
    runs-on: ubuntu-latest
    steps:
      - name: ZAP Baseline Scan
        uses: zaproxy/action-baseline@v0.10.0
        with:
          target: 'http://localhost:81/api/v1'
          rules_file_name: '.zap/rules.tsv'
          fail_action: true
```

### 9.3 Güvenlik Test Kapsamı

| Test | Frekans | Araç | Kapsam |
|------|---------|------|--------|
| OWASP ZAP | Her PR | Automated | Tüm endpoint'ler |
| SQL Injection | Her deploy | SQLMap | User input alanları |
| XSS | Her deploy | Manual + Auto | Frontend + API |
| CSRF | Her deploy | Manuel | Form'lar |
| Auth bypass | Haftalık | Manuel | Auth middleware |

---

## 10. Test Verisi Yönetimi

### 10.1 Test Fixtures

```
tests/
├── fixtures/
│   ├── auth.sql           # Kullanıcı verileri
│   ├── media.sql          # Medya verileri
│   ├── playlists.sql      # Çalma listeleri
│   └── downloads.sql      # İndirme verileri
├── factories/
│   ├── UserFactory.php    # Kullanıcı üretici
│   ├── MediaFactory.php   # Medya üretici
│   └── PlaylistFactory.php
└── seeds/
    ├── test_db.sql         # Test DB seed
    └── mock_data.json      # Mock API yanıtları
```

### 10.2 Factory Pattern

```php
class UserFactory
{
    public static function create(array $overrides = []): array
    {
        return array_merge([
            'id' => random_int(1, 10000),
            'email' => 'user_' . uniqid() . '@test.com',
            'password_hash' => password_hash('test123', PASSWORD_DEFAULT),
            'role' => 'user',
            'is_active' => true,
            'created_at' => date('Y-m-d H:i:s'),
        ], $overrides);
    }
    
    public static function createAdmin(): array
    {
        return self::create(['role' => 'admin']);
    }
}
```

### 10.3 Mock API Yanıtları

```json
{
  "mocks": {
    "GET /api/v1/media": {
      "status": 200,
      "body": {
        "data": [
          {"id": 1, "title": "Test Track 1"},
          {"id": 2, "title": "Test Track 2"}
        ],
        "total": 2
      }
    },
    "POST /api/v1/auth/login": {
      "status": 200,
      "body": {
        "token": "mock_token_abc123"
      }
    }
  }
}
```

---

## 11. Test Ortamı Kurulumu

### 11.1 Ortam Yapısı

| Ortam | Amaç | DB | Veri |
|-------|------|-----|------|
| Development | Yerel geliştirme | SQLite | Mock |
| Test | CI/CD testleri | MySQL (test) | Fixture |
| Staging | Pre-production | MySQL (staging) | Anonimize production |

### 11.2 Test DB Kurulumu

```bash
# Test DB oluştur
mysql -u root -e "CREATE DATABASE coremusic_media_test"

# Şemayı uygula
mysql -u root coremusic_media_test < .sql/coremusic_media.sql

# Test verilerini yükle
mysql -u root coremusic_media_test < tests/fixtures/media.sql
```

### 11.3 Docker Compose (Test)

```yaml
# docker-compose.test.yml
version: '3.8'
services:
  db:
    image: mysql:9
    environment:
      MYSQL_DATABASE: coremusic_test
      MYSQL_ROOT_PASSWORD: test
    ports:
      - "3306:3306"
  
  redis:
    image: redis:7
    ports:
      - "6379:6379"
  
  api:
    build: .
    depends_on:
      - db
      - redis
    environment:
      DB_HOST: db
      REDIS_HOST: redis
```

---

## 12. CI/CD Entegrasyonu

### 12.1 Pipeline Adımları

```
Pull Request
  → [1. Lint] → PHP CS Fixer, ESLint
    → [2. Unit Test] → PHPUnit (%80+ coverage)
      → [3. Integration Test] → DB testleri
        → [4. Contract Test] → Pact doğrulama
          → [5. Security Scan] → OWASP ZAP
            → [6. Load Test] → k6 (5 dk)
              → [7. E2E Test] → Playwright (kritik akışlar)
                → [8. Deploy] → Staging
```

### 12.2 GitHub Actions Workflow

```yaml
# .github/workflows/api-tests.yml
name: API Tests
on: [push, pull_request]

jobs:
  unit-tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
      - name: Install deps
        run: composer install
      - name: Run PHPUnit
        run: vendor/bin/phpunit --coverage-text
  
  integration-tests:
    needs: unit-tests
    runs-on: ubuntu-latest
    services:
      mysql:
        image: mysql:9
        env:
          MYSQL_ROOT_PASSWORD: test
          MYSQL_DATABASE: coremusic_test
        ports:
          - 3306:3306
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
      - name: Run Integration Tests
        run: vendor/bin/phpunit --testsuite integration
  
  e2e-tests:
    needs: integration-tests
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup Node
        uses: actions/setup-node@v4
      - name: Install Playwright
        run: npx playwright install
      - name: Run E2E Tests
        run: npx playwright test
```

---

## 13. Coverage Hedefleri

| Modül | Minimum | Hedef | Mevcut |
|-------|---------|-------|--------|
| Auth Controller | %80 | %90 | %82 |
| Media Repository | %80 | %90 | %85 |
| Session Manager | %80 | %90 | %88 |
| CSRF Middleware | %90 | %95 | %91 |
| Rate Limiter | %80 | %90 | %83 |
| API Endpoints | %80 | %90 | %79 |
| **Ortalama** | **≥%80** | **≥%90** | **%85** |

### 13.1 Coverage Raporlama

```bash
# PHPUnit coverage
vendor/bin/phpunit --coverage-html=coverage --coverage-text

# kritik coverage kontrolü
if [ $(cat coverage.txt | grep "Lines:" | awk '{print $2}' | tr -d '%') -lt 80 ]; then
  echo "Coverage below 80% threshold"
  exit 1
fi
```

---

## 14. Test Patterns

### 14.1 Arrange-Act-Assert (AAA)

```php
public function testCreateMediaReturnsCreated(): void
{
    // Arrange
    $data = ['title' => 'New Track', 'artist' => 'Artist'];
    
    // Act
    $response = $this->api->post('/api/v1/media', $data);
    
    // Assert
    $this->assertEquals(201, $response->getStatusCode());
    $this->assertArrayHasKey('id', $response->getData());
}
```

### 14.2 Given-When-Then (BDD)

```gherkin
Feature: Media Search
  Scenario: User searches for media by title
    Given a user is authenticated
    And the database has 10 media items
    When the user searches for "rock"
    Then the response should contain matching items
    And the response should have pagination info
```

### 14.3 Data Provider

```php
/**
 * @dataProvider invalidEmailProvider
 */
public function testLoginRejectsInvalidEmail(string $email): void
{
    $response = $this->authController->login(
        new LoginRequest($email, 'password')
    );
    $this->assertEquals(422, $response->getStatusCode());
}

public static function invalidEmailProvider(): array
{
    return [
        'empty' => [''],
        'no-at' => ['invalid'],
        'no-domain' => ['user@'],
        'double-at' => ['user@@test.com'],
    ];
}
```

---

## 15. Mock Servisler

### 15.1 External Service Mocking

```php
// Download service test — Deezer API mock
$mockDeezer = $this->createMock(DeezerClient::class);
$mockDeezer->method('search')
    ->willReturn([
        ['id' => 123, 'title' => 'Test Song']
    ]);

$downloadService = new DownloadService($mockDeezer);
$results = $downloadService->search('test');
$this->assertCount(1, $results);
```

### 15.2 HTTP Mocking

```php
// Guzzle mock handler
$mock = new MockHandler([
    new Response(200, [], '{"data": []}'),
    new Response(429, [], '{"error": "rate_limit"}'),
]);

$handler = HandlerStack::create($mock);
$client = new Client(['handler' => $handler]);
```

---

## 16. Hızlı Referans

| İhtiyaç | İlk Adım |
|---------|----------|
| Unit test yaz | §4 Unit Testler |
| Contract test | §5 API Contract Testing |
| Integration test | §6 Integration Testleri |
| E2E test | §7 E2E Testleri |
| Load test | §8 Load Testing |
| Güvenlik test | §9 Security Testing |
| Test verisi | §10 Test Verisi Yönetimi |
| CI/CD entegrasyonu | §12 CI/CD Entegrasyonu |
| Coverage hedefi | §13 Coverage Hedefleri |

---

## 17. Cross References

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| Bu dosya | [[api-architecture-master]] | Ana API mimarisi |
| Bu dosya | [[testing/strategy]] | Genel test stratejisi |
| Bu dosya | [[api-public-contract]] | Public API |
| Bu dosya | [[api-internal-contract]] | Internal API |
| §5 Contract | [[ADR-022-database-hardened-security]] | Güvenlik testi |
| §9 OWASP | [[ADR-020-api-public-security]] | API güvenlik |
| §12 CI/CD | [[ADR-042-vault-restructuring-2026-08-03]] | CI/CD |
| §10 Fixtures | [[ADR-040-database-authority]] | DB testleri |

---

## 18. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 18 |
| Test Types | 4 (Unit, Integration, Contract, E2E) |
| Coverage Target | ≥80% min, ≥90% hedef |
| Load Test Scenarios | 5 |
| OWASP Tests | 10 |
| CI/CD Steps | 8 |
| Test Patterns | 3 (AAA, BDD, DataProvider) |
| Cross References | 8 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode