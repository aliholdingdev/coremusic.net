---
title: "Deployment Monitoring"
layer: K13
category: "CI/CD"
date: 2026-09-20
version: "1.0.0"
---

# Deployment Monitoring

## Genel Bakış

COREMUSIC deployment monitoring, her deploy sonrası uygulama sağlığını, performansını ve hata oranlarını gerçek zamanlı olarak izler. Prometheus metrics collection, Grafana dashboards, alerting rules ve deployment verification ile production güvenliği sağlanır. Deploy后 post-deploy verification ile otomatik rollback tetiklenebilir.

## Pipeline Akışı

```
Deploy Complete → Metrics Collection → Dashboard Update → Alert Rules Evaluated → Health Verified → Anomaly Detection → Report
```

## Teknik Detaylar

### Prometheus Metrics Collection

```yaml
# infrastructure/monitoring/prometheus.yml
global:
  scrape_interval: 15s
  evaluation_interval: 15s

alerting:
  alertmanagers:
    - static_configs:
        - targets:
            - alertmanager:9093

rule_files:
  - "rules/*.yml"

scrape_configs:
  - job_name: 'coremusic-app'
    kubernetes_sd_configs:
      - role: pod
        namespaces:
          names:
            - coremusic-production
    relabel_configs:
      - source_labels: [__meta_kubernetes_pod_annotation_prometheus_io_scrape]
        action: keep
        regex: true
      - source_labels: [__meta_kubernetes_pod_annotation_prometheus_io_port]
        action: replace
        target_label: __address__
        regex: (.+)
        replacement: ${1}

  - job_name: 'coremusic-mysql'
    static_configs:
      - targets: ['mysql-exporter:9104']

  - job_name: 'coremusic-redis'
    static_configs:
      - targets: ['redis-exporter:9121']
```

### Deployment Metrics

```yaml
# rules/deployment.yml
groups:
  - name: deployment-alerts
    rules:
      - alert: DeploymentRollback
        expr: |
          kube_deployment_status_replicas_available{namespace="coremusic-production"}
          < kube_deployment_spec_replicas{namespace="coremusic-production"}
        for: 5m
        labels:
          severity: critical
        annotations:
          summary: "Deployment {{ $labels.deployment }} has unavailable replicas"
          description: "{{ $value }} replicas unavailable for 5 minutes"

      - alert: HighErrorRatePostDeploy
        expr: |
          rate(http_requests_total{status=~"5..",namespace="coremusic-production"}[5m])
          > 0.05
        for: 3m
        labels:
          severity: critical
        annotations:
          summary: "High error rate detected post-deployment"

      - alert: LatencySpikePostDeploy
        expr: |
          histogram_quantile(0.99,
            rate(http_request_duration_seconds_bucket{namespace="coremusic-production"}[5m])
          ) > 1
        for: 3m
        labels:
          severity: warning
        annotations:
          summary: "P99 latency spike detected"

      - alert: PodCrashLooping
        expr: |
          rate(kube_pod_container_status_restarts_total{namespace="coremusic-production"}[15m]) > 0
        for: 5m
        labels:
          severity: critical
        annotations:
          summary: "Pod {{ $labels.pod }} is crash looping"
```

### Deployment Verification

```yaml
# scripts/verify-deploy.sh
#!/bin/bash
set -e

NAMESPACE="coremusic-production"
DEPLOYMENT="coremusic-app"
PROM_URL="http://prometheus:9090"
SLACK_WEBHOOK="${SLACK_WEBHOOK_URL}"

echo "=== Deployment Verification ==="
echo "Deployment: ${DEPLOYMENT}"
echo "Namespace: ${NAMESPACE}"
echo "Timestamp: $(date -u +%Y-%m-%dT%H:%M:%SZ)"

# 1. Check pod readiness
echo -e "\n1. Checking pod readiness..."
READY_PODS=$(kubectl get deployment ${DEPLOYMENT} -n ${NAMESPACE} \
  -o jsonpath='{.status.readyReplicas}')
DESIRED_PODS=$(kubectl get deployment ${DEPLOYMENT} -n ${NAMESPACE} \
  -o jsonpath='{.spec.replicas}')

if [ "${READY_PODS}" -lt "${DESIRED_PODS}" ]; then
  echo "ERROR: Not all pods are ready (${READY_PODS}/${DESIRED_PODS})"
  exit 1
fi
echo "OK: All pods ready (${READY_PODS}/${DESIRED_PODS})"

# 2. Check health endpoint
echo -e "\n2. Checking health endpoint..."
for i in {1..5}; do
  STATUS=$(kubectl exec -n ${NAMESPACE} deploy/${DEPLOYMENT} -- \
    curl -sf http://localhost:9000/health | jq -r '.status')
  if [ "${STATUS}" = "ok" ]; then
    echo "OK: Health check passed"
    break
  fi
  if [ "$i" -eq 5 ]; then
    echo "ERROR: Health check failed after 5 attempts"
    exit 1
  fi
  sleep 10
done

# 3. Check error rate
echo -e "\n3. Checking error rate..."
ERROR_RATE=$(curl -s "${PROM_URL}/api/v1/query" \
  --data-urlencode "query=rate(http_requests_total{status=~'5..',deployment='${DEPLOYMENT}'}[5m])" | \
  jq -r '.data.result[0].value[1] // "0"')

if (( $(echo "${ERROR_RATE} > 0.05" | bc -l) )); then
  echo "ERROR: Error rate ${ERROR_RATE} exceeds 5% threshold"
  exit 1
fi
echo "OK: Error rate ${ERROR_RATE}"

# 4. Check latency
echo -e "\n4. Checking P99 latency..."
LATENCY=$(curl -s "${PROM_URL}/api/v1/query" \
  --data-urlencode "query=histogram_quantile(0.99, rate(http_request_duration_seconds_bucket{deployment='${DEPLOYMENT}'}[5m]))" | \
  jq -r '.data.result[0].value[1] // "0"')

if (( $(echo "${LATENCY} > 1" | bc -l) )); then
  echo "ERROR: P99 latency ${LATENCY}s exceeds 1s threshold"
  exit 1
fi
echo "OK: P99 latency ${LATENCY}s"

# 5. Notify success
echo -e "\n=== Deployment Verification PASSED ==="
curl -X POST -H 'Content-type: application/json' \
  --data "{\"text\":\"✅ Deployment verified successfully: ${DEPLOYMENT}\"}" \
  ${SLACK_WEBHOOK}
```

### Grafana Dashboard

```json
{
  "dashboard": {
    "title": "COREMUSIC Deployment Overview",
    "panels": [
      {
        "title": "Deployment Status",
        "type": "stat",
        "targets": [
          {
            "expr": "kube_deployment_status_replicas_available{namespace='coremusic-production'}",
            "legendFormat": "{{deployment}}"
          }
        ]
      },
      {
        "title": "Request Rate",
        "type": "graph",
        "targets": [
          {
            "expr": "rate(http_requests_total{namespace='coremusic-production'}[5m])",
            "legendFormat": "{{method}} {{status}}"
          }
        ]
      },
      {
        "title": "Error Rate",
        "type": "graph",
        "targets": [
          {
            "expr": "rate(http_requests_total{status=~'5..',namespace='coremusic-production'}[5m])",
            "legendFormat": "{{endpoint}}"
          }
        ],
        "thresholds": [
          {
            "value": 0.05,
            "color": "red"
          }
        ]
      },
      {
        "title": "Response Time (P95/P99)",
        "type": "graph",
        "targets": [
          {
            "expr": "histogram_quantile(0.95, rate(http_request_duration_seconds_bucket{namespace='coremusic-production'}[5m]))",
            "legendFormat": "P95"
          },
          {
            "expr": "histogram_quantile(0.99, rate(http_request_duration_seconds_bucket{namespace='coremusic-production'}[5m]))",
            "legendFormat": "P99"
          }
        ]
      },
      {
        "title": "Pod Count",
        "type": "stat",
        "targets": [
          {
            "expr": "count(kube_pod_info{namespace='coremusic-production',pod=~'coremusic-app.*'})",
            "legendFormat": "Running Pods"
          }
        ]
      }
    ]
  }
}
```

### Alertmanager Configuration

```yaml
# infrastructure/monitoring/alertmanager.yml
global:
  resolve_timeout: 5m

route:
  group_by: ['alertname', 'namespace']
  group_wait: 30s
  group_interval: 5m
  repeat_interval: 4h
  receiver: 'slack-cicd'

  routes:
    - match:
        severity: critical
      receiver: 'pager-critical'
      continue: true

    - match:
        severity: warning
      receiver: 'slack-warning'

receivers:
  - name: 'slack-cicd'
    slack_configs:
      - api_url: '${SLACK_WEBHOOK_URL}'
        channel: '#coremusic-alerts'
        title: '{{ .GroupLabels.alertname }}'
        text: '{{ range .Alerts }}{{ .Annotations.description }}{{ end }}'

  - name: 'pager-critical'
    pagerduty_configs:
      - service_key: '${PAGERDUTY_KEY}'
        description: '{{ .GroupLabels.alertname }}'

  - name: 'slack-warning'
    slack_configs:
      - api_url: '${SLACK_WEBHOOK_URL}'
        channel: '#coremusic-warnings'
```

### SLI/SLO Definitions

```yaml
# SLO configuration
slos:
  availability:
    target: 99.9
    description: "Core music service availability"
    sli: |
      1 - (
        sum(rate(http_requests_total{status=~"5..",namespace="coremusic-production"}[5m]))
        /
        sum(rate(http_requests_total{namespace="coremusic-production"}[5m]))
      )

  latency:
    target: 99.5
    description: "Requests under 500ms"
    sli: |
      sum(rate(http_request_duration_seconds_bucket{le="0.5",namespace="coremusic-production"}[5m]))
      /
      sum(rate(http_request_duration_seconds_count{namespace="coremusic-production"}[5m]))

  error_budget:
    total_minutes_per_month: 43800
    allowed_downtime_minutes: 43.8
    remaining_minutes: 43.8
```

## Konfigürasyon

### Monitoring Stack Deploy

```yaml
# infrastructure/monitoring/kustomization.yaml
apiVersion: kustomize.config.k8s.io/v1beta1
kind: Kustomization

resources:
  - namespace.yaml
  - prometheus/
  - grafana/
  - alertmanager/
  - exporters/

configMapGenerator:
  - name: monitoring-config
    literals:
      - PROMETHEUS_RETENTION=30d
      - GRAFANA_ADMIN_PASSWORD=secret
```

### Health Check Endpoints

```php
<?php
// routes/health.php

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => config('app.version'),
        'checks' => [
            'database' => HealthChecks::database(),
            'redis'    => HealthChecks::redis(),
            'storage'  => HealthChecks::storage(),
        ]
    ]);
});

Route::get('/health/live', fn() => response()->json(['status' => 'ok']));
Route::get('/health/ready', function () {
    $ready = HealthChecks::all();
    return response()->json($ready, $ready['status'] === 'ok' ? 200 : 503);
});
```

## Bağımlılıklar

- `prometheus`: Metrics collection
- `grafana`: Dashboard visualization
- `alertmanager`: Alert routing
- `kube-state-metrics`: Kubernetes metrics
- `node-exporter`: Node metrics

## Durum: Implementasyon

| Bileşen              | Durum       | Son Güncelleme |
|----------------------|-------------|-----------------|
| Prometheus           | Hazır       | 2026-09-20      |
| Grafana Dashboards   | Hazır       | 2026-09-20      |
| Alert Rules          | Hazır       | 2026-09-20      |
| Deploy Verification  | Hazır       | 2026-09-20      |
| SLI/SLO Tracking     | Hazır       | 2026-09-20      |
| Health Endpoints     | Hazır       | 2026-09-20      |
