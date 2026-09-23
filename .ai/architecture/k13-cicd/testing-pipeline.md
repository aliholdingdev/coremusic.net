---
title: "Testing Pipeline"
layer: K13
category: "CI/CD"
date: 2026-09-20
version: "1.0.0"
---

# Testing Pipeline

## Genel Bakış

COREMUSIC testing pipeline'ı, unit test, integration test ve E2E test olmak üzere üç katmanlı bir test stratejisi izler. Her katman farklı hız ve kapsama alanı ile çalışır. Code coverage hedefi %80+ olarak belirlenmiştir. PHPUnit, Docker Compose ve Playwright araçları kullanılır.

## Pipeline Akışı

```
Code Commit → Unit Tests (Fast) → Integration Tests (Medium) → E2E Tests (Slow) → Coverage Report → Gate Check
```

## Teknik Detaylar

### Test Pyramid

```
        /\
       /  \        E2E Tests (10%)
      /    \       - Playwright browser tests
     /------\      - Critical user journeys
    /        \     Integration Tests (30%)
   /          \    - API contract tests
  /            \   - Database integration
 /--------------\  Unit Tests (60%)
/                \ - Business logic
/                  \ - Service methods
```

### Unit Test Config

```php
// phpunit.xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit
    bootstrap="vendor/autoload.php"
    colors="true"
    verbose="true"
    stopOnFailure="false"
>
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory>tests/Integration</directory>
        </testsuite>
    </testsuites>
    <coverage>
        <report>
            <html outputDirectory="coverage-html"/>
            <clover outputFile="coverage.xml"/>
            <text outputFile="coverage.txt" showUncoveredFiles="true"/>
        </report>
    </coverage>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
    </php>
</phpunit>
```

### Unit Test Örneği

```php
<?php
// tests/Unit/Services/AudioServiceTest.php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use CoreMusic\Services\AudioService;
use CoreMusic\Models\AudioTrack;

class AudioServiceTest extends TestCase
{
    private AudioService $audioService;

    protected function setUp(): void
    {
        $this->audioService = new AudioService();
    }

    public function test_calculate_duration_returns_seconds(): void
    {
        $track = new AudioTrack([
            'bpm' => 120,
            'bars' => 32
        ]);

        $result = $this->audioService->calculateDuration($track);

        $this->assertEquals(64.0, $result);
    }

    public function test_normalize_volume_within_bounds(): void
    {
        $audio = [0.5, 0.8, 1.2, -0.3];
        $result = $this->audioService->normalizeVolume($audio);

        $this->assertNotEmpty($result);
        $this->assertLessThanOrEqual(1.0, max($result));
        $this->assertGreaterThanOrEqual(-1.0, min($result));
    }

    public function test_convert_format_throws_on_invalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->audioService->convertFormat('track.xyz', 'mp3');
    }
}
```

### Integration Test Config

```yaml
# docker-compose.test.yml
version: '3.8'
services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    environment:
      - APP_ENV=testing
      - DB_HOST=mysql
      - DB_DATABASE=coremusic_test
      - REDIS_HOST=redis
    depends_on:
      mysql:
        condition: service_healthy
      redis:
        condition: service_healthy

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: testing
      MYSQL_DATABASE: coremusic_test
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      interval: 5s
      timeout: 3s
      retries: 5

  redis:
    image: redis:7-alpine
    healthcheck:
      test: ["CMD", "redis-cli", "ping"]
      interval: 5s
```

### Integration Test Örneği

```php
<?php
// tests/Integration/Api/TrackApiTest.php

namespace Tests\Integration\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TrackApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_track_returns_201(): void
    {
        $payload = [
            'title' => 'Test Beat',
            'artist' => 'Test Artist',
            'bpm' => 128,
            'genre' => 'electronic'
        ];

        $response = $this->postJson('/api/v1/tracks', $payload);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'id', 'title', 'artist', 'bpm', 'created_at'
                 ]);
    }

    public function test_get_track_returns_200(): void
    {
        $track = factory(Track::class)->create();

        $response = $this->getJson("/api/v1/tracks/{$track->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => $track->title]);
    }

    public function test_delete_track_returns_204(): void
    {
        $track = factory(Track::class)->create();

        $response = $this->deleteJson("/api/v1/tracks/{$track->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tracks', ['id' => $track->id]);
    }
}
```

### E2E Test Config

```typescript
// playwright.config.ts
import { defineConfig } from '@playwright/test';

export default defineConfig({
  testDir: './tests/e2e',
  timeout: 30000,
  retries: 2,
  workers: 4,
  reporter: [
    ['html', { outputFolder: 'playwright-report' }],
    ['junit', { outputFile: 'test-results/e2e.xml' }]
  ],
  use: {
    baseURL: process.env.E2E_BASE_URL || 'http://localhost:8000',
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',
    trace: 'on-first-retry',
  },
  projects: [
    {
      name: 'chromium',
      use: { browserName: 'chromium' },
    },
    {
      name: 'firefox',
      use: { browserName: 'firefox' },
    },
  ],
});
```

### E2E Test Örneği

```typescript
// tests/e2e/audio-upload.spec.ts
import { test, expect } from '@playwright/test';

test.describe('Audio Upload Flow', () => {
  test('user can upload audio file', async ({ page }) => {
    await page.goto('/dashboard');
    await page.click('[data-testid="upload-button"]');

    const fileInput = page.locator('input[type="file"]');
    await fileInput.setInputFiles('tests/fixtures/test-beat.mp3');

    await page.fill('[data-testid="track-title"]', 'My Beat');
    await page.click('[data-testid="save-button"]');

    await expect(page.locator('.success-message'))
      .toBeVisible({ timeout: 10000 });
  });

  test('user can play uploaded track', async ({ page }) => {
    await page.goto('/library');
    await page.click('[data-testid="track-card"]:first-child');
    await page.click('[data-testid="play-button"]');

    const audio = page.locator('audio');
    await expect(audio).toHaveAttribute('src', /.+/);
  });
});
```

### Coverage Gate

```yaml
# coverage-gate.yml
minimum:
  overall: 80
  unit: 90
  integration: 70
  e2e: 60

exclusions:
  - "tests/**"
  - "vendor/**"
  - "config/**"
  - "database/migrations/**"
```

## Konfigürasyon

### GitHub Actions Test Job

```yaml
test:
  runs-on: ubuntu-latest
  services:
    mysql:
      image: mysql:8.0
      env:
        MYSQL_ROOT_PASSWORD: testing
        MYSQL_DATABASE: coremusic_test
      ports:
        - 3306:3306
      options: >-
        --health-cmd="mysqladmin ping"
        --health-interval=10s
        --health-timeout=5s
        --health-retries=5

  steps:
    - uses: actions/checkout@v4
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.3'
        coverage: xdebug
    - name: Run tests
      run: |
        composer install
        vendor/bin/phpunit --coverage-clover=coverage.xml
    - name: Check coverage
      run: |
        COVERAGE=$(vendor/bin/phpunit --coverage-text | grep "Lines:" | awk '{print $3}' | tr -d '%')
        if [ "$COVERAGE" -lt 80 ]; then
          echo "Coverage $COVERAGE% is below 80% threshold"
          exit 1
        fi
```

## Bağımlılıklar

- `phpunit/phpunit`: Unit testing framework
- `laravel/framework`: Integration testing helpers
- `playwright/test`: E2E browser testing
- `phpunit/php-code-coverage`: Coverage reporting
- `fakerphp/faker`: Test data generation

## Durum: Implementasyon

| Bileşen              | Durum       | Son Güncelleme |
|----------------------|-------------|-----------------|
| Unit Tests           | Hazır       | 2026-09-20      |
| Integration Tests    | Hazır       | 2026-09-20      |
| E2E Tests            | Hazır       | 2026-09-20      |
| Coverage Reporting   | Hazır       | 2026-09-20      |
| Test Fixtures        | Hazır       | 2026-09-20      |
