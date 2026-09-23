---
title: "Staging Environment"
layer: K13
category: "CI/CD"
date: 2026-09-20
version: "1.0.0"
---

# Staging Environment

## Genel Bakış

COREMUSIC staging environment, production ile mümkün olduğunca aynı yapıda bir test ortamıdır. Environment parity prensibi ile生产和staging arasındaki farkları minimize eder. Bu ortam, deployment öncesi final validation, performance testing ve UAT (User Acceptance Testing) için kullanılır. Automatik deploy main branch merge sonrası tetiklenir.

## Pipeline Akışı

```
Main Branch Merge → Build Image → Deploy to Staging → Smoke Tests → Performance Tests → UAT Approval → Production Gate
```

## Teknik Detaylar

### Staging Cluster Resources

```yaml
# Staging namespace resource allocation
apiVersion: v1
kind: ResourceQuota
metadata:
  name: staging-quota
  namespace: coremusic-staging
spec:
  hard:
    requests.cpu: "4"
    requests.memory: "4Gi"
    limits.cpu: "8"
    limits.memory: "8Gi"
    pods: "15"
    services: "10"
    persistentvolumeclaims: "5"
```

### Environment Parity Checklist

| Parameter           | Production         | Staging            | Parity |
|---------------------|--------------------|--------------------|--------|
| Kubernetes Version  | 1.29              | 1.29              | ✅     |
| PHP Version         | 8.3-fpm-alpine    | 8.3-fpm-alpine    | ✅     |
| MySQL Version       | 8.0               | 8.0               | ✅     |
| Redis Version       | 7.0               | 7.0               | ✅     |
| Ingress Controller  | nginx 1.9         | nginx 1.9         | ✅     |
| Resource Limits     | 8CPU/8GB          | 4CPU/4GB          | ⚠️     |
| Replicas            | 3                 | 2                 | ⚠️     |
| TLS                 | Let's Encrypt     | Self-signed       | ⚠️     |

### Staging Deployment

```yaml
# k8s/staging/deployment.yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: coremusic-app
  namespace: coremusic-staging
spec:
  replicas: 2
  selector:
    matchLabels:
      app: coremusic
  template:
    spec:
      containers:
        - name: coremusic
          image: ghcr.io/coremusic/coremusic:staging
          env:
            - name: APP_ENV
              value: "staging"
            - name: APP_DEBUG
              value: "true"
            - name: DB_HOST
              valueFrom:
                configMapKeyRef:
                  name: staging-config
                  key: db-host
          resources:
            requests:
              cpu: "250m"
              memory: "256Mi"
            limits:
              cpu: "1000m"
              memory: "512Mi"
```

### Staging ConfigMap

```yaml
# k8s/staging/configmap.yaml
apiVersion: v1
kind: ConfigMap
metadata:
  name: staging-config
  namespace: coremusic-staging
data:
  APP_ENV: "staging"
  APP_DEBUG: "true"
  APP_URL: "https://staging.coremusic.example.com"
  DB_HOST: "mysql-staging"
  DB_DATABASE: "coremusic_staging"
  REDIS_HOST: "redis-staging"
  CACHE_DRIVER: "redis"
  SESSION_DRIVER: "redis"
  LOG_LEVEL: "debug"
  MAIL_HOST: "mailhog"
  MAIL_PORT: "1025"
```

### Staging Ingress

```yaml
# k8s/staging/ingress.yaml
apiVersion: networking.k8s.io/v1
kind: Ingress
metadata:
  name: coremusic-staging-ingress
  namespace: coremusic-staging
  annotations:
    nginx.ingress.kubernetes.io/ssl-redirect: "true"
    nginx.ingress.kubernetes.io/auth-type: basic
    nginx.ingress.kubernetes.io/auth-secret: staging-basic-auth
    nginx.ingress.kubernetes.io/auth-realm: "Staging Environment"
spec:
  tls:
    - hosts:
        - staging.coremusic.example.com
      secretName: staging-tls
  rules:
    - host: staging.coremusic.example.com
      http:
        paths:
          - path: /
            pathType: Prefix
            backend:
              service:
                name: coremusic-service
                port:
                  number: 80
```

### Staging Data Seeding

```bash
#!/bin/bash
# scripts/seed-staging.sh

set -e

echo "Seeding staging database..."

# Run migrations
docker exec coremusic-app php artisan migrate --force

# Seed demo data
docker exec coremusic-app php artisan db:seed --class=StagingSeeder

# Create test users
docker exec coremusic-app php artisan tinker --execute="
User::create(['name' => 'Test User', 'email' => 'test@staging.com', 'password' => bcrypt('password')]);
User::create(['name' => 'Admin User', 'email' => 'admin@staging.com', 'password' => bcrypt('password'), 'is_admin' => true]);
"

# Clear cache
docker exec coremusic-app php artisan cache:clear
docker exec coremusic-app php artisan config:clear

echo "Staging seed complete!"
```

### Smoke Tests

```yaml
# tests/staging/smoke.yml
name: Staging Smoke Tests
tests:
  - name: Homepage loads
    url: https://staging.coremusic.example.com
    expect:
      status: 200
      body_contains: "COREMUSIC"

  - name: API health check
    url: https://staging.coremusic.example.com/health
    expect:
      status: 200
      json:
        status: "ok"

  - name: Login page
    url: https://staging.coremusic.example.com/login
    expect:
      status: 200
      body_contains: "Sign In"

  - name: Static assets
    url: https://staging.coremusic.example.com/build/manifest.json
    expect:
      status: 200
```

### Staging Monitoring

```yaml
# Staging-specific Prometheus rules
groups:
  - name: staging-alerts
    rules:
      - alert: StagingHighErrorRate
        expr: rate(http_requests_total{status=~"5..",namespace="coremusic-staging"}[5m]) > 0.05
        for: 2m
        labels:
          severity: warning
        annotations:
          summary: "High error rate in staging"

      - alert: StagingPodCrashLooping
        expr: rate(kube_pod_container_status_restarts_total{namespace="coremusic-staging"}[15m]) > 0
        for: 5m
        labels:
          severity: critical
```

### Staging Cleanup Policy

```yaml
# Automated cleanup configuration
cleanup:
  schedule: "0 2 * * 0"  # Weekly Sunday 2am
  resources:
    - type: pods
      maxAge: "7d"
    - type: deployments
      keepLast: 5
    - type: configmaps
      exclude: ["staging-config"]
    - type: secrets
      exclude: ["staging-basic-auth", "staging-tls"]

  notification:
    channel: "#coremusic-staging"
    message: "Weekly staging cleanup completed"
```

## Konfigürasyon

### GitHub Actions Staging Deploy

```yaml
deploy-staging:
  runs-on: ubuntu-latest
  environment: staging
  needs: [build, test]
  if: github.ref == 'refs/heads/main'

  steps:
    - uses: actions/checkout@v4

    - name: Configure kubectl
      uses: azure/k8s-set-context@v3
      with:
        method: kubeconfig
        kubeconfig: ${{ secrets.STAGING_KUBE_CONFIG }}

    - name: Deploy to staging
      run: |
        kubectl apply -f k8s/staging/
        kubectl set image deployment/coremusic-app \
          coremusic=ghcr.io/coremusic/coremusic:${{ github.sha }} \
          -n coremusic-staging
        kubectl rollout status deployment/coremusic-app -n coremusic-staging

    - name: Run smoke tests
      run: |
        ./scripts/smoke-test.sh https://staging.coremusic.example.com

    - name: Notify staging deploy
      uses: slackapi/slack-github-action@v1
      with:
        payload: |
          {
            "text": "✅ Staging deployed: ${{ github.sha }}"
          }
      env:
        SLACK_WEBHOOK_URL: ${{ secrets.SLACK_WEBHOOK }}
```

## Bağımlılıklar

- `kubectl`: Kubernetes management
- `helm`: Chart deployment
- `mysql`: Database
- `redis`: Cache/session
- `mailhog`: Email testing
- `prometheus`: Monitoring

## Durum: Implementasyon

| Bileşen              | Durum       | Son Güncelleme |
|----------------------|-------------|-----------------|
| Staging Cluster      | Hazır       | 2026-09-20      |
| Auto Deploy          | Hazır       | 2026-09-20      |
| Smoke Tests          | Hazır       | 2026-09-20      |
| Data Seeding         | Hazır       | 2026-09-20      |
| Monitoring           | Hazır       | 2026-09-20      |
| Cleanup Policy       | Hazır       | 2026-09-20      |
