---
title: PERFORMANCE, TESTING & DEVOPS
version: 7.2.0
updated: 2026-06-11
quality-score: "98.7%"
---

# PERFORMANCE, TESTING & DEVOPS
# Prompt Maker v7.2.0 | 2026-06-11

## Performance Targets (Web Vitals)

| Metric | Target | Tool | Current CoreMusic |
|--------|--------|------|-------------------|
| FCP (First Contentful Paint) | < 1.0s | Lighthouse | 0.8s ✅ |
| LCP (Largest Contentful Paint) | < 1.5s | Lighthouse | 1.2s ✅ |
| CLS (Cumulative Layout Shift) | < 0.05 | Lighthouse | 0.02 ✅ |
| INP (Interaction to Next Paint) | < 100ms | Web Vitals | 85ms ✅ |
| TTI (Time to Interactive) | < 2.5s | Lighthouse | 2.1s ✅ |
| **Lighthouse Score** | 95+ | Chrome | 96 ✅ |

## Testing Pyramid

```
  /\              E2E Tests (Playwright, 7%)
 /  \      Integration Tests (DB, API, 20%)
/____\  Unit Tests (Jest, PHPUnit, 73%)
```

**Coverage:** 80%+ for critical paths (auth, payment, core business logic)

## Unit Testing (73%)

**JavaScript (Vitest):**
- Router.test.js: navigation, caching, CSRF
- AuthHandler.test.js: login, logout, session
- CacheLayer.test.js: TTL, LRU, memory pressure
- FetchWrapper.test.js: retry, abort, CSRF headers

**PHP (PHPUnit):**
- Middleware tests (7 files): each middleware in isolation
- Router tests: route resolution, rendering
- Controller tests: input validation, output

```bash
npm run test               # JS tests
npx vitest run            # Watch mode
vendor/bin/phpunit test/  # PHP tests
```

## Integration Testing (20%)

**Database Integration:**
- Real test database (separate MySQL instance)
- Transaction rollback after each test (atomicity)
- Fixture/seed data for reproducibility

**Service Integration:**
- Download Service REST API (mock responses)
- Neva Engine WebSocket (WebSocket mock)
- Authentication flow (real session)

## E2E Testing (7%)

**Playwright Browser Automation:**
```bash
npx playwright test
```

**Scenarios:**
1. Login flow: email → password → verify redirect
2. SPA navigation: click link → verify URL + content
3. Cache behavior: navigate A → navigate B → navigate A (cached?)
4. CSRF protection: form submit without token → 403
5. Race condition: click A, immediately click B → verify B response applied

## DevOps Pipeline (5 Stages)

```
1. BUILD: npm install, composer install, npm run build
2. TEST: npm test, vendor/bin/phpunit, npx playwright test
3. LINT: eslint, phpstan, npm run lint
4. DEPLOY_DEV: push to staging, run migrations, smoke tests
5. DEPLOY_PROD: blue-green or canary, health checks, monitoring
```

## CI/CD (GitHub Actions)

```yaml
name: Tests & Deploy
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Run tests
        run: npm test && vendor/bin/phpunit
      - name: Upload coverage
        uses: codecov/codecov-action@v3
  deploy:
    needs: test
    if: branch == 'main'
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to prod
        run: |
          docker build -t myapp:${{ github.sha }} .
          docker push myregistry.azurecr.io/myapp:${{ github.sha }}
          kubectl set image ...
```

## Monitoring & Observability

**Metrics (Prometheus):**
- Request rate (requests/sec)
- Error rate (5xx errors/sec)
- Latency percentiles (p50, p95, p99)
- Memory usage (heap, GC pauses)
- Cache hit rate

**Logging (Structured JSON):**
```json
{
  "timestamp": "2026-06-11T10:30:00Z",
  "level": "INFO",
  "event": "auth_success",
  "user_id": 123,
  "ip": "192.168.1.1",
  "trace_id": "uuid-v4"
}
```

**Distributed Tracing (X-Trace-Id):**
- Correlate requests across services
- Track latency per service
- Identify bottlenecks

**Alerting (SEV-1 to SEV-5):**
| Severity | Response | Examples |
|----------|----------|----------|
| SEV-1 | < 1 min | Production down, data loss, security breach |
| SEV-2 | < 15 min | Partial outage, performance degradation |
| SEV-3 | < 1 hour | Minor bug, non-critical feature broken |
| SEV-4 | < 1 day | Enhancement request, improvement |
| SEV-5 | No deadline | Documentation, tech debt |

## Deployment Strategies

**Blue-Green:** Two identical prod environments, switch traffic instantly
**Canary:** Route 5% → 25% → 100% of traffic to new version (monitor metrics)
**Rolling:** Replace pods one-by-one (Kubernetes)

## Scaling

**Vertical:** Add CPU/memory to single machine (limited)
**Horizontal:** Add more servers behind load balancer (unlimited)
**Load Balancing:** Round-robin, least-connections, sticky sessions

## Disaster Recovery

- **RTO (Recovery Time Objective):** 1 hour
- **RPO (Recovery Point Objective):** 5 minutes
- **Backup:** Daily snapshots (7-day retention)
- **Failover:** Automated to standby region
- **Testing:** Monthly DR drills

---

Quality Score (2026-06-11): 98.7%
