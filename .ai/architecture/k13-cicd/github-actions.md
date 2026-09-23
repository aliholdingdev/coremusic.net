---
title: "GitHub Actions Workflows"
layer: K13
category: "CI/CD"
date: 2026-09-20
version: "1.0.0"
---

# GitHub Actions Workflows

## Genel Bakış

COREMUSIC projesi, GitHub Actions'ı CI/CD pipeline'larının orkestrasyonu için ana motor olarak kullanır. Tüm workflow'lar YAML formatında tanımlanmış olup, matrix builds, reusable workflows, ve conditional execution desteği sağlar. Proje, push, pull_request, workflow_dispatch, ve schedule tetikleyicilerini destekler.

## Pipeline Akışı

```
Event Trigger → Workflow Dispatch → Job Matrix → Parallel Execution → Result Aggregation → Notification
```

## Teknik Detaylar

### Workflow Tipleri

COREMUSIC'de tanımlı workflow tipleri:

1. **CI Workflow** (`ci.yml`): Her push ve PR'da çalışır. Lint, test, build adımlarını içerir.
2. **CD Workflow** (`cd-release.yml`): Tag push ile tetiklenir. Staging ve production deploy yapar.
3. **Security Scan** (`security.yml`): Haftalık ve PR'da çalışır. SAST, DAST, dependency scanning.
4. **Nightly Build** (`nightly.yml`): Her gece 02:00'de çalışır. Full test suite ve performance benchmark.
5. **Release Drafter** (`release-drafter.yml`): PR merge sonrası release notları otomatik oluşturur.

### Matrix Build Stratejisi

Matrix builds, farklı ortamlarda paralel test çalıştırır:

```yaml
strategy:
  matrix:
    php-version: ['8.2', '8.3']
    test-suite: ['unit', 'integration']
    os: [ubuntu-latest]
    exclude:
      - php-version: '8.2'
        test-suite: 'integration'
```

### Reusable Workflow Pattern

Ortak adımlar reusable workflow olarak tanımlanır:

```yaml
# .github/workflows/reusable-docker-build.yml
name: Reusable Docker Build
on:
  workflow_call:
    inputs:
      image-tag:
        required: true
        type: string
      push-to-registry:
        required: false
        type: boolean
        default: false

jobs:
  build:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Set up Docker Buildx
        uses: docker/setup-buildx-action@v3
      - name: Build Docker image
        uses: docker/build-push-action@v5
        with:
          context: .
          push: ${{ inputs.push-to-registry }}
          tags: ghcr.io/coremusic/coremusic:${{ inputs.image-tag }}
          cache-from: type=gha
          cache-to: type=gha,mode=max
```

### Concurrency Control

Aynı anda birden fazla deploy'u önlemek için concurrency groups kullanılır:

```yaml
concurrency:
  group: deploy-${{ github.ref }}
  cancel-in-progress: true
```

### Secret Management

Pipeline'larda kullanılan秘密lar:

| Secret              | Açıklama                    | Ortam         |
|---------------------|-----------------------------|---------------|
| `KUBE_CONFIG`       | Kubernetes cluster config   | Production    |
| `REGISTRY_TOKEN`    | Container registry token    | All           |
| `SLACK_WEBHOOK`     | Notification webhook        | All           |
| `SONAR_TOKEN`       | SonarQube API token         | CI/CD         |
| `COSIGN_PRIVATE_KEY`| Image signing key           | CD            |

### Caching Stratejisi

```yaml
- name: Cache Composer dependencies
  uses: actions/cache@v3
  with:
    path: vendor
    key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}
    restore-keys: |
      ${{ runner.os }}-composer-

- name: Cache Docker layers
  uses: actions/cache@v3
  with:
    path: /tmp/.buildx-cache
    key: ${{ runner.os }}-docker-${{ hashFiles('Dockerfile') }}
```

### Job Dependencies ve Artifacts

```yaml
jobs:
  build:
    runs-on: ubuntu-latest
    outputs:
      image-tag: ${{ steps.meta.outputs.tags }}
    steps:
      - name: Build
        run: docker build -t coremusic:${{ github.sha }} .
      - name: Upload artifact
        uses: actions/upload-artifact@v3
        with:
          name: docker-image
          path: /tmp/image.tar

  test:
    needs: build
    runs-on: ubuntu-latest
    steps:
      - name: Download artifact
        uses: actions/download-artifact@v3
        with:
          name: docker-image

  deploy:
    needs: [build, test]
    if: github.ref == 'refs/heads/main'
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to staging
        run: kubectl apply -f k8s/
```

### Notification Entegrasyonu

```yaml
- name: Notify Slack
  if: always()
  uses: 8398a7/action-slack@v3
  with:
    status: ${{ job.status }}
    fields: repo,message,commit,author
    channel: '#coremusic-ci'
  env:
    SLACK_WEBHOOK_URL: ${{ secrets.SLACK_WEBHOOK }}
```

## Konfigürasyon

### Branch Protection Rules

```yaml
# GitHub Settings - Branch Protection
main:
  required_pull_request_reviews:
    required_approving_review_count: 2
    dismiss_stale_reviews: true
  required_status_checks:
    strict: true
    contexts:
      - "lint-and-test"
      - "security-scan"
  restrictions:
    teams:
      - coremusic-devs
```

### GitHub Environments

```yaml
# .github/environments.yml
staging:
  protection_rules:
    - required_reviewers: []
  deployment_branch_policy:
    protected_branches: false
    custom_branch_policies: true
    name_patterns: ["release/*"]

production:
  protection_rules:
    - required_reviewers:
        - username: bayramali
  deployment_branch_policy:
    protected_branches: true
```

## Bağımlılıklar

- `actions/checkout@v4`: Repository checkout
- `actions/setup-node@v4`: Node.js setup
- `docker/setup-buildx-action@v3`: Docker Buildx
- `docker/build-push-action@v5`: Docker build ve push
- `actions/cache@v3`: Dependency caching
- `codecov/codecov-action@v3`: Coverage reporting

## Durum: Implementasyon

| bileşen               | Durum       | Son Güncelleme |
|----------------------|-------------|-----------------|
| CI Workflow          | Hazır       | 2026-09-20      |
| CD Workflow          | Hazır       | 2026-09-20      |
| Security Scan        | Hazır       | 2026-09-20      |
| Nightly Build        | Hazır       | 2026-09-20      |
| Reusable Workflows   | Hazır       | 2026-09-20      |
| Matrix Builds        | Hazır       | 2026-09-20      |
