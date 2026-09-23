---
title: "Continuous Deployment Pipeline"
layer: K13
category: "CI/CD"
date: 2026-09-20
version: "1.0.0"
---

# Continuous Deployment Pipeline

## Genel Bakış

COREMUSIC Continuous Deployment pipeline'ı, production-ready code'un güvenli ve kontrollü bir şekilde deploy edilmesini sağlar. Semantic versioning, automated release notes, staged rollout ve manual approval gates ile production güvenliği sağlanır. Deploy stratejisi olarak canary ve blue-green kullanılabilir.

## Pipeline Akışı

```
Tag Push (v*) → Build → Security Gate → Staging Deploy → Smoke Test → Manual Approval → Production Deploy → Health Check → Release Notes → Notify
```

## Teknik Detaylar

### CD Pipeline Workflow

```yaml
# .github/workflows/cd-release.yml
name: CD Pipeline - Release

on:
  push:
    tags:
      - 'v*'

env:
  REGISTRY: ghcr.io
  IMAGE_NAME: ${{ github.repository }}

jobs:
  # Stage 1: Build and Push Image
  build:
    name: Build Release Image
    runs-on: ubuntu-latest
    outputs:
      image-tag: ${{ steps.meta.outputs.version }}
      image-digest: ${{ steps.build.outputs.digest }}
    steps:
      - uses: actions/checkout@v4
        with:
          fetch-depth: 0

      - name: Extract version
        id: version
        run: echo "VERSION=${GITHUB_REF#refs/tags/v}" >> $GITHUB_OUTPUT

      - name: Set up Docker Buildx
        uses: docker/setup-buildx-action@v3

      - name: Login to GHCR
        uses: docker/login-action@v3
        with:
          registry: ghcr.io
          username: ${{ github.actor }}
          password: ${{ secrets.GITHUB_TOKEN }}

      - name: Extract metadata
        id: meta
        uses: docker/metadata-action@v5
        with:
          images: ${{ env.REGISTRY }}/${{ env.IMAGE_NAME }}
          tags: |
            type=semver,pattern={{version}}
            type=semver,pattern={{major}}.{{minor}}
            type=sha

      - name: Build and push
        id: build
        uses: docker/build-push-action@v5
        with:
          context: .
          push: true
          tags: ${{ steps.meta.outputs.tags }}
          labels: ${{ steps.meta.outputs.labels }}
          cache-from: type=gha
          cache-to: type=gha,mode=max

      - name: Sign image
        run: |
          cosign sign --yes \
            ${{ env.REGISTRY }}/${{ env.IMAGE_NAME }}@${{ steps.build.outputs.digest }}

  # Stage 2: Security Gate
  security-gate:
    name: Security Gate
    runs-on: ubuntu-latest
    needs: build
    steps:
      - name: Verify image signature
        run: |
          cosign verify \
            --key cosign.pub \
            ${{ env.REGISTRY }}/${{ env.IMAGE_NAME }}@${{ needs.build.outputs.image-digest }}

      - name: Scan image for vulnerabilities
        uses: aquasecurity/trivy-action@master
        with:
          image-ref: ${{ env.REGISTRY }}/${{ env.IMAGE_NAME }}@${{ needs.build.outputs.image-digest }}
          format: 'json'
          output: 'trivy-results.json'
          severity: 'CRITICAL,HIGH'

      - name: Check for critical vulnerabilities
        run: |
          CRITICAL=$(cat trivy-results.json | jq '.Results[].Vulnerabilities[] | select(.Severity == "CRITICAL") | length')
          if [ "$CRITICAL" -gt 0 ]; then
            echo "Critical vulnerabilities found!"
            exit 1
          fi

  # Stage 3: Staging Deploy
  deploy-staging:
    name: Deploy to Staging
    runs-on: ubuntu-latest
    needs: [build, security-gate]
    environment: staging
    steps:
      - uses: actions/checkout@v4

      - name: Configure kubectl
        uses: azure/k8s-set-context@v3
        with:
          method: kubeconfig
          kubeconfig: ${{ secrets.STAGING_KUBE_CONFIG }}

      - name: Deploy to staging
        run: |
          kubectl set image deployment/coremusic-app \
            coremusic=${{ env.REGISTRY }}/${{ env.IMAGE_NAME }}@${{ needs.build.outputs.image-digest }} \
            -n coremusic-staging
          kubectl rollout status deployment/coremusic-app -n coremusic-staging --timeout=300s

      - name: Run smoke tests
        run: |
          for i in {1..5}; do
            STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://staging.coremusic.example.com/health)
            if [ "$STATUS" = "200" ]; then
              echo "Smoke test passed!"
              exit 0
            fi
            sleep 10
          done
          echo "Smoke test failed!"
          exit 1

  # Stage 4: Performance Test
  performance:
    name: Performance Test
    runs-on: ubuntu-latest
    needs: deploy-staging
    steps:
      - uses: actions/checkout@v4

      - name: Run k6 load test
        uses: grafana/k6-action@v0.3.1
        with:
          filename: tests/performance/load-test.js
        env:
          K6_BASE_URL: https://staging.coremusic.example.com

      - name: Check performance thresholds
        run: |
          # Parse k6 results
          P95=$(cat k6-results.json | jq '.metrics.http_req_duration.values.p95')
          ERROR_RATE=$(cat k6-results.json | jq '.metrics.http_req_failed.values.rate')

          if (( $(echo "$P95 > 500" | bc -l) )); then
            echo "P95 latency ${P95}ms exceeds 500ms threshold"
            exit 1
          fi

          if (( $(echo "$ERROR_RATE > 0.01" | bc -l) )); then
            echo "Error rate ${ERROR_RATE} exceeds 1% threshold"
            exit 1
          fi

  # Stage 5: Manual Approval
  approve-production:
    name: Approve Production Deploy
    runs-on: ubuntu-latest
    needs: [build, security-gate, deploy-staging, performance]
    environment:
      name: production
    steps:
      - name: Approval received
        run: echo "Production deploy approved by ${{ github.actor }}"

  # Stage 6: Production Deploy
  deploy-production:
    name: Deploy to Production
    runs-on: ubuntu-latest
    needs: [build, approve-production]
    steps:
      - uses: actions/checkout@v4

      - name: Configure kubectl
        uses: azure/k8s-set-context@v3
        with:
          method: kubeconfig
          kubeconfig: ${{ secrets.PRODUCTION_KUBE_CONFIG }}

      - name: Deploy to production (canary)
        run: |
          # Deploy canary with 10% traffic
          kubectl apply -f k8s/production/canary-deployment.yaml
          kubectl set image deployment/coremusic-canary \
            coremusic=${{ env.REGISTRY }}/${{ env.IMAGE_NAME }}@${{ needs.build.outputs.image-digest }} \
            -n coremusic-production
          kubectl rollout status deployment/coremusic-canary -n coremusic-production

      - name: Monitor canary (5 minutes)
        run: |
          echo "Monitoring canary for 5 minutes..."
          sleep 300

          # Check error rate
          ERROR_RATE=$(kubectl exec -n monitoring prometheus-0 -- \
            promtool query instant \
            'rate(http_requests_total{status=~"5..",app="coremusic"}[5m])' | \
            awk '{print $NF}')

          if (( $(echo "$ERROR_RATE > 0.01" | bc -l) )); then
            echo "Canary error rate too high! Rolling back..."
            kubectl delete deployment coremusic-canary -n coremusic-production
            exit 1
          fi

      - name: Promote canary to stable
        run: |
          # Update main deployment
          kubectl set image deployment/coremusic-app \
            coremusic=${{ env.REGISTRY }}/${{ env.IMAGE_NAME }}@${{ needs.build.outputs.image-digest }} \
            -n coremusic-production
          kubectl rollout status deployment/coremusic-app -n coremusic-production

          # Remove canary
          kubectl delete deployment coremusic-canary -n coremusic-production

          # Update traffic to 100% stable
          kubectl apply -f k8s/production/virtual-service.yaml

  # Stage 7: Release Notes
  release:
    name: Create Release
    runs-on: ubuntu-latest
    needs: deploy-production
    steps:
      - uses: actions/checkout@v4
        with:
          fetch-depth: 0

      - name: Generate changelog
        id: changelog
        uses: conventional-changelog/standard-version@v3

      - name: Create GitHub Release
        uses: softprops/action-gh-release@v1
        with:
          tag_name: ${{ github.ref_name }}
          name: Release ${{ github.ref_name }}
          body: |
            ## Changes
            ${{ steps.changelog.outputs.changelog }}

            ## Docker Image
            `ghcr.io/coremusic/coremusic:${{ github.ref_name }}`

            ## Deploy
            - Staging: https://staging.coremusic.example.com
            - Production: https://coremusic.example.com
          files: |
            sbom.json
            trivy-results.json
```

### Release Strategy

```yaml
# Release configuration
release:
  strategy: canary
  canary:
    initial_weight: 10
    step_weight: 20
    step_interval: 5m
    max_weight: 100
    analysis_interval: 1m
    success_threshold: 95%
    failure_threshold: 5%

  rollback:
    automatic: true
    trigger: error_rate > 5%
    notification: slack
```

### Version Management

```yaml
# Semantic versioning rules
versioning:
  scheme: semver
  prerelease:
    formats: ['alpha', 'beta', 'rc']
  bump:
    major: breaking changes
    minor: new features
    patch: bug fixes
  tag_format: 'v{version}'
```

### Deploy Approval Matrix

| Environment  | Approval Required | Approvers          | Timeout |
|-------------|-------------------|--------------------| -------|
| Development | No                | -                  | -       |
| Staging     | No                | -                  | -       |
| Production  | Yes (2)           | @coremusic-devops  | 24h     |
| Hotfix      | Yes (1)           | @coremusic-lead    | 1h      |

## Konfigürasyon

### GitHub Environments

```yaml
# .github/environments.yml
production:
  protection_rules:
    - required_reviewers:
        - username: bayramali
        - username: devops-lead
    - wait_timer:
        in_minutes: 5
  deployment_branch_policy:
    protected_branches: true
```

## Bağımlılıklar

- `docker/build-push-action@v5`: Image building
- `cosign`: Image signing
- `trivy`: Vulnerability scanning
- `k6`: Performance testing
- `kubernetes`: Deployment target

## Durum: Implementasyon

| Bileşen              | Durum       | Son Güncelleme |
|----------------------|-------------|-----------------|
| Release Build        | Hazır       | 2026-09-20      |
| Security Gate        | Hazır       | 2026-09-20      |
| Staging Deploy       | Hazır       | 2026-09-20      |
| Performance Test     | Hazır       | 2026-09-20      |
| Canary Deploy        | Hazır       | 2026-09-20      |
| Release Notes        | Hazır       | 2026-09-20      |
| Manual Approval      | Hazır       | 2026-09-20      |
