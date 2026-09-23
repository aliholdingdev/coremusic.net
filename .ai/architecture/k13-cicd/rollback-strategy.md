---
title: "Rollback Strategy"
layer: K13
category: "CI/CD"
date: 2026-09-20
version: "1.0.0"
---

# Rollback Strategy

## Genel Bakış

COREMUSIC rollback stratejisi, production hatalarında hızlı ve güvenli geri dönüş sağlamayı amaçlar. Blue-Green deployment, canary releases ve automatic rollback mekanizmaları ile zero-downtime geri dönüş hedeflenir. Her deploy sonrası health check ile durum doğrulanır, başarısız deploy'larda otomatik rollback tetiklenir.

## Pipeline Akışı

```
Deploy → Health Check → Success? → Yes: Complete | No: Auto Rollback → Notify Team → Post-mortem
```

## Teknik Detaylar

### Blue-Green Deployment

```yaml
# Blue-Green Deployment Strategy
apiVersion: apps/v1
kind: Deployment
metadata:
  name: coremusic-blue
  namespace: coremusic-production
spec:
  replicas: 3
  selector:
    matchLabels:
      app: coremusic
      slot: blue
  template:
    metadata:
      labels:
        app: coremusic
        slot: blue
    spec:
      containers:
        - name: coremusic
          image: ghcr.io/coremusic/coremusic:v1.2.0
---
apiVersion: apps/v1
kind: Deployment
metadata:
  name: coremusic-green
  namespace: coremusic-production
spec:
  replicas: 0  # Passive until switch
  selector:
    matchLabels:
      app: coremusic
      slot: green
  template:
    metadata:
      labels:
        app: coremusic
        slot: green
    spec:
      containers:
        - name: coremusic
          image: ghcr.io/coremusic/coremusic:v1.3.0
```

### Traffic Switching

```yaml
# Service routing for blue-green
apiVersion: v1
kind: Service
metadata:
  name: coremusic-service
spec:
  selector:
    app: coremusic
    slot: blue  # Change to 'green' for switch
  ports:
    - port: 80
      targetPort: 9000
```

### Canary Deployment

```yaml
# Canary Deployment
apiVersion: apps/v1
kind: Deployment
metadata:
  name: coremusic-canary
spec:
  replicas: 1  # Small canary instance
  selector:
    matchLabels:
      app: coremusic
      track: canary
  template:
    metadata:
      labels:
        app: coremusic
        track: canary
    spec:
      containers:
        - name: coremusic
          image: ghcr.io/coremusic/coremusic:v1.3.0-canary
---
# Istio VirtualService for traffic splitting
apiVersion: networking.istio.io/v1beta1
kind: VirtualService
metadata:
  name: coremusic-vs
spec:
  hosts:
    - coremusic.example.com
  http:
    - route:
        - destination:
            host: coremusic-service
            subset: stable
          weight: 90
        - destination:
            host: coremusic-service
            subset: canary
          weight: 10
```

### Automatic Rollback

```yaml
# Rollback script
#!/bin/bash
# scripts/rollback.sh

set -e

DEPLOYMENT=$1
NAMESPACE=${2:-coremusic-production}
MAX_RETRIES=3
HEALTH_URL="http://coremusic-service.${NAMESPACE}.svc/health"

echo "Starting rollback for ${DEPLOYMENT}..."

# Get current revision
CURRENT=$(kubectl rollout history deployment/${DEPLOYMENT} -n ${NAMESPACE} | tail -1 | awk '{print $1}')
echo "Current revision: ${CURRENT}"

# Rollback to previous
kubectl rollout undo deployment/${DEPLOYMENT} -n ${NAMESPACE}

# Wait for rollout
echo "Waiting for rollout to complete..."
kubectl rollout status deployment/${DEPLOYMENT} -n ${NAMESPACE} --timeout=300s

# Health check
for i in $(seq 1 $MAX_RETRIES); do
    echo "Health check attempt ${i}..."
    if curl -sf "${HEALTH_URL}" > /dev/null; then
        echo "Health check passed!"
        exit 0
    fi
    sleep 10
done

echo "Health check failed after rollback!"
exit 1
```

### Health Check Configuration

```yaml
# Health check endpoints
livenessProbe:
  httpGet:
    path: /health/live
    port: 9000
  initialDelaySeconds: 30
  periodSeconds: 10
  timeoutSeconds: 5
  failureThreshold: 3

readinessProbe:
  httpGet:
    path: /health/ready
    port: 9000
  initialDelaySeconds: 5
  periodSeconds: 5
  timeoutSeconds: 3
  failureThreshold: 3

startupProbe:
  httpGet:
    path: /health/startup
    port: 9000
  initialDelaySeconds: 10
  periodSeconds: 5
  failureThreshold: 30
```

### Rollback Triggers

```yaml
# Automatic rollback conditions
rollback_triggers:
  - name: "Health Check Failure"
    condition: "consecutive_failures >= 3"
    action: "auto_rollback"

  - name: "Error Rate Spike"
    condition: "error_rate > 5%"
    duration: "5m"
    action: "auto_rollback"

  - name: "Latency Spike"
    condition: "p99_latency > 2000ms"
    duration: "5m"
    action: "alert_then_rollback"

  - name: "Pod Crash Loop"
    condition: "restart_count > 5"
    window: "10m"
    action: "auto_rollback"
```

### Rollback Notification

```yaml
# Slack notification on rollback
- name: Notify Rollback
  uses: slackapi/slack-github-action@v1
  with:
    payload: |
      {
        "text": "🚨 ROLLBACK TRIGGERED",
        "blocks": [
          {
            "type": "section",
            "text": {
              "type": "mrkdwn",
              "text": "*Deployment:* coremusic\n*Namespace:* production\n*Trigger:* Health check failure\n*Action:* Automatic rollback initiated"
            }
          }
        ]
      }
  env:
    SLACK_WEBHOOK_URL: ${{ secrets.SLACK_WEBHOOK }}
```

## Konfigürasyon

### Rollback Procedure Document

```markdown
# COREMUSIC Rollback Procedure

## Manuel Rollback
1. kubectl rollout undo deployment/coremusic-app -n coremusic-production
2. kubectl rollout status deployment/coremusic-app -n coremusic-production
3. curl -f http://coremusic-service/health
4. Notify team on Slack

## Blue-Green Rollback
1. kubectl patch service coremusic-service -p '{"spec":{"selector":{"slot":"blue"}}}'
2. Scale down green: kubectl scale deployment/coremusic-green --replicas=0
3. Verify traffic is on blue
4. Post-mortem

## Canary Rollback
1. Update Istio VirtualService weight: stable=100, canary=0
2. Delete canary deployment
3. Verify metrics stabilize
```

### Rollback SLA

| Metric                | Target     | Measurement            |
|----------------------|------------|------------------------|
| Detection Time       | < 2 min    | Health check interval  |
| Rollback Initiation  | < 1 min    | Auto-trigger delay     |
| Rollback Completion  | < 5 min    | Pod rollout time       |
| Traffic Stabilization| < 2 min    | Post-rollback monitoring|
| Total Recovery       | < 10 min   | End-to-end             |

## Bağımlılıklar

- `kubectl`: Kubernetes CLI
- `istio`: Traffic management
- `prometheus`: Metrics for rollback triggers
- `alertmanager`: Alert routing
- `slack`: Notification channel

## Durum: Implementasyon

| Bileşen              | Durum       | Son Güncelleme |
|----------------------|-------------|-----------------|
| Blue-Green Deploy    | Hazır       | 2026-09-20      |
| Canary Releases      | Hazır       | 2026-09-20      |
| Auto Rollback        | Hazır       | 2026-09-20      |
| Health Checks        | Hazır       | 2026-09-20      |
| Rollback Scripts     | Hazır       | 2026-09-20      |
| Notification         | Hazır       | 2026-09-20      |
