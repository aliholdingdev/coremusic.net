# 🚀 Player Info Tests — Quick Start

## One-Minute Setup

```bash
# 1. Install test runners
npm install --save-dev vitest jsdom @playwright/test axe-playwright

# 2. Run unit tests
npx vitest run tests/components/player-info.spec.js

# 3. Start dev server (new terminal)
php -S localhost:81 -t ../../home.coremusic.net/public/

# 4. Run E2E tests (another terminal)
npx playwright test tests/e2e/player-info.spec.ts
```

---

## Test Commands

### Unit Tests (Vitest)

```bash
# All tests
npx vitest run tests/components/

# With coverage
npx vitest run --coverage tests/components/player-info.spec.js

# Watch mode (auto-rerun on save)
npx vitest watch tests/components/

# Single test
npx vitest run -t "setProgress: width style"

# Filtered (by name pattern)
npx vitest run -t "accessibility"
```

### E2E Tests (Playwright)

```bash
# All browsers
npx playwright test tests/e2e/

# Single browser
npx playwright test --project=chromium

# Interactive UI mode
npx playwright test --ui

# Single test
npx playwright test -g "page loads successfully"

# Debug mode (step through)
npx playwright test --debug

# With video recording
npx playwright test --video on
```

---

## Test Structure

```
tests/
├── components/
│   └── player-info.spec.js         ← 47 unit tests (Vitest)
├── e2e/
│   └── player-info.spec.ts         ← 21 E2E tests (Playwright)
├── mocks/
│   └── player-api.mock.js          ← API mock (fetch + EventSource)
├── README.md                        ← Full documentation
└── QUICKSTART.md                    ← This file
```

---

## Coverage Report

```bash
npx vitest run --coverage tests/components/player-info.spec.js

# Output: coverage/
#   ├── index.html      (open in browser)
#   ├── coverage.json
#   └── lcov.info
```

**Target:** ≥80% lines, functions, branches, statements

---

## What's Being Tested

### ✅ Unit Tests (47 tests)

- **Render:** Wide (1025px+) and embedded (≤1024px) variants
- **State:** Config parsing, defaultState(), setState()
- **Progress Bar:** setProgress(), ARIA attrs, click events
- **Play/Pause:** togglePlay(), space key, event emission
- **Track Updates:** updateTrack() DOM mutations
- **Accessibility:** ARIA roles, labels, alt text
- **Error Handling:** Image fallback, destroy cleanup
- **API Mock:** fetch responses, error scenarios (404, 401, 500, network)

### ✅ E2E Tests (21 tests)

- **Page Load:** HTTP 200, component renders
- **Responsive:** 1024px (embedded) + 1920px (wide) viewports
- **Controls:** Play button click, space key, progress bar seek
- **WCAG:** Axe accessibility scan at both breakpoints
- **Security:** Auth bypass test, CSRF token check
- **Performance:** Component init < 1s, rapid interactions stable

---

## Common Issues & Fixes

### ❌ `jsdom is not installed`
```bash
npm install --save-dev jsdom
```

### ❌ `Cannot find module '../mocks/player-api.mock.js'`
- Check path in test file matches your directory structure
- Path should be relative to test file location

### ❌ `Connection refused localhost:81`
```bash
# Terminal 1: Start dev server
php -S localhost:81 -t ../../home.coremusic.net/public/

# Terminal 2: Run E2E tests
npx playwright test
```

### ❌ `Playwright browser not installed`
```bash
npx playwright install
```

### ❌ `Expected 80 but received 75 coverage`
- Run: `npx vitest run --coverage` to see which lines/functions are missing
- Add tests for remaining branches, error paths, edge cases

---

## Integration with CI/CD

### GitHub Actions

```yaml
name: Test
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - uses: actions/setup-node@v3
        with: { node-version: '18' }
      - run: npm install
      - run: npm run test:unit -- --coverage
      - run: npm run test:e2e
      - uses: actions/upload-artifact@v3
        if: always()
        with:
          name: test-results
          path: coverage/
```

---

## npm Scripts (Add to package.json)

```json
{
  "scripts": {
    "test": "npm run test:unit && npm run test:e2e",
    "test:unit": "vitest run tests/components/",
    "test:unit:watch": "vitest watch tests/components/",
    "test:unit:coverage": "vitest run --coverage tests/components/",
    "test:e2e": "playwright test tests/e2e/",
    "test:e2e:ui": "playwright test --ui tests/e2e/",
    "test:e2e:debug": "playwright test --debug tests/e2e/"
  }
}
```

Then run:
```bash
npm test                  # All tests
npm run test:unit        # Unit only
npm run test:unit:watch  # Unit watch mode
npm run test:e2e         # E2E only
npm run test:e2e:ui      # E2E interactive
```

---

## Key Files

| File | Purpose | Tests |
|------|---------|-------|
| `tests/components/player-info.spec.js` | Component unit tests (Vitest, jsdom) | 47 |
| `tests/e2e/player-info.spec.ts` | Browser E2E tests (Playwright) | 21 |
| `tests/mocks/player-api.mock.js` | Fetch + EventSource mocks | — |
| `vitest.config.js` | Vitest configuration | — |
| `playwright.config.ts` | Playwright configuration | — |
| `tests/README.md` | Full test documentation | — |

---

## Test Execution Flow

```
┌─────────────────────────────────────────────┐
│ npm test                                    │
├─────────────────────────────────────────────┤
│ 1. vitest run                               │
│    └─ 47 component tests × 0.2s = ~10s     │
│                                             │
│ 2. playwright test                          │
│    └─ 21 E2E tests × 0.5-1s = ~15s        │
│                                             │
│ Total: ~25s (parallel) or ~35s (serial)    │
└─────────────────────────────────────────────┘
```

---

## Success Criteria

- ✅ All 47 unit tests passing
- ✅ All 21 E2E tests passing
- ✅ ≥80% code coverage (lines, functions, branches, statements)
- ✅ No WCAG violations (axe audit)
- ✅ Tests run deterministically (no flaky timeouts)
- ✅ No mock hell (only necessary mocks)

---

**Last Updated:** 2026-09-30  
**Status:** Ready to run
