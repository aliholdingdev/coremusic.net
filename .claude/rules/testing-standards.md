# Testing Standards — CoreMusic

**Authority:** ADR-042, ADR-044
**Last Updated:** 2026-08-06
**Governing Rules:** Red Team • Human Mode • Truth Mode

---

## 1. Coverage Targets

| Module | Minimum | Target |
|--------|---------|--------|
| Backend (PHP) | ≥80% | ≥90% |
| Frontend (JS) | ≥80% | ≥90% |
| Audio Engine (C++) | ≥80% | ≥90% |
| Download Service | ≥80% | ≥90% |

## 2. Test Stack

| Module | Framework | Command |
|--------|-----------|---------|
| Backend (PHP) | PHPUnit 11 | `vendor/bin/phpunit` |
| Frontend (JS) | Vitest | `npx vitest run` |
| C++ Engine | Google Test | `cmake --build build && ctest` |
| E2E | Playwright | `npx playwright test` |

## 3. Test Pyramid

```
        E2E Tests (Playwright)
       /         \
    Integration Tests (PHPUnit)
   /               \
  Unit Tests (PHPUnit + Vitest)
```

- Unit: 70% (fast, isolated)
- Integration: 20% (API + DB)
- E2E: 10% (critical user flows)

## 4. Test Structure

```
tests/
  Unit/
    AuthServiceTest.php
    SessionManagerTest.php
    CacheStatsTest.php
    MemoryAdapterTest.php
  Integration/
    ApiEndpointTest.php
  E2E/
    LoginFlow.test.js
```

## 5. Naming Conventions

- Test files: `*Test.php` (PHP), `*.test.js` (JS)
- Test methods: `test_<method>_<scenario>_<expected>()`
- Groups: `@group unit`, `@group integration`, `@group e2e`

## 6. CI/CD Integration

- Run PHPUnit on every commit
- Run Vitest on every commit
- Minimum 80% coverage enforced
- Coverage report in CI artifacts

## 7. Test Data

- Use fixtures/factories for test data
- No production data in tests
- Each test creates its own data
- Cleanup after each test

## 8. E2E Testing

- Playwright for browser automation
- Test critical flows: login, register, download
- Screenshots on failure
- Parallel execution enabled

## 9. Theme Engine Tests (ADR-044)

```php
// Theme resolution test
$engine = new ThemeEngine($pdo);
$gender = $engine->resolveGender($userId);
$this->assertContains($gender, ['female', 'male', 'neutral']);
```

## 10. Forbidden

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| Skipping tests | Run full suite |
| Hardcoded test data | Factories/fixtures |
| Flaky tests | Deterministic tests |
| < 80% coverage | ≥ 80% coverage |

---

*Testing Standards v2.0.0 — CoreMusic Enterprise*
*Last Updated: 2026-08-06*
