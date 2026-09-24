---
title: "Grafana Dashboards"
layer: K12
category: "İzleme"
date: 2026-09-20
---

# Grafana Dashboards

## Genel Bakış

Grafana, COREMUSIC platformunda metrik görselleştirme ve dashboard yönetimi için kullanılır. Prometheus, Elasticsearch ve diğer veri kaynaklarından gelen metrikleri gerçek zamanlı grafiklere dönüştürür. Özelleştirilebilir paneller ile operasyon ekibi platform durumunu anlık olarak takip edebilir.

## Dashboard Kategorileri

### Ana Dashboard (Overview)

```
┌─────────────────────────────────────────────────────────────────┐
│                    COREMUSIC ANA DASHBOARD                      │
├─────────────────────────────────────────────────────────────────┤
│  [Sistem Durumu]  [Aktif Kullanıcılar]  [İstek/Hata]  [Uptime]  │
├─────────────────────────────────────────────────────────────────┤
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐         │
│  │  Request/sec │  │  Error Rate  │  │  Latency     │         │
│  │  ████ 1.2k   │  │  ▓ 0.02%    │  │  p99: 45ms  │         │
│  └──────────────┘  └──────────────┘  └──────────────┘         │
│                                                                 │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐         │
│  │  CPU Usage   │  │  Memory      │  │  Disk I/O    │         │
│  │  █████ 65%   │  │  ████ 4.2GB  │  │  ███ 120MB/s │         │
│  └──────────────┘  └──────────────┘  └──────────────┘         │
└─────────────────────────────────────────────────────────────────┘
```

### Servis Dashboard'ları

| Dashboard | İçerik | Güncelleme |
|-----------|--------|-----------|
| Audio Engine | Ses işleme metrikleri | Gerçek zamanlı |
| API Gateway | İstek yönlendirme | Gerçek zamanlı |
| Veritabanı | Sorgu performansı | 10s |
| Cache | Önbellek performansı | 10s |
| Queue | Kuyruk durumu | 30s |

## Panel Yapılandırmaları

### Time Series Panel

```json
{
  "type": "timeseries",
  "title": "İstek Oranı",
  "targets": [
    {
      "expr": "sum(rate(coremusic_http_requests_total[1m]))",
      "legendFormat": "{{method}} {{endpoint}}",
      "refId": "A"
    }
  ],
  "fieldConfig": {
    "defaults": {
      "unit": "reqps",
      "custom": {
        "drawStyle": "line",
        "lineInterpolation": "smooth",
        "fillOpacity": 10,
        "gradientMode": "scheme"
      }
    }
  },
  "gridPos": { "h": 8, "w": 12, "x": 0, "y": 0 }
}
```

### Stat Panel

```json
{
  "type": "stat",
  "title": "Aktif Kullanıcılar",
  "targets": [
    {
      "expr": "coremusic_active_users",
      "refId": "A"
    }
  ],
  "fieldConfig": {
    "defaults": {
      "thresholds": {
        "steps": [
          { "value": 0, "color": "green" },
          { "value": 1000, "color": "yellow" },
          { "value": 5000, "color": "red" }
        ]
      },
      "unit": "short"
    }
  }
}
```

### Gauge Panel

```json
{
  "type": "gauge",
  "title": "CPU Kullanımı",
  "targets": [
    {
      "expr": "coremusic_cpu_usage_percent{component='app'}",
      "refId": "A"
    }
  ],
  "fieldConfig": {
    "defaults": {
      "min": 0,
      "max": 100,
      "unit": "percent",
      "thresholds": {
        "steps": [
          { "value": 0, "color": "green" },
          { "value": 70, "color": "yellow" },
          { "value": 90, "color": "red" }
        ]
      }
    }
  }
}
```

### Table Panel

```json
{
  "type": "table",
  "title": "En Yavaş Endpoints",
  "targets": [
    {
      "expr": "topk(10, coremusic:http_request_duration_seconds:avg)",
      "format": "table",
      "instant": true
    }
  ],
  "transformations": [
    {
      "id": "organize",
      "options": {
        "renameByName": {
          "endpoint": "Endpoint",
          "Value": "Ortalama Süre (s)"
        }
      }
    }
  ]
}
```

### Heatmap Panel

```json
{
  "type": "heatmap",
  "title": "Yanıt Süresi Dağılımı",
  "targets": [
    {
      "expr": "sum(rate(coremusic_http_request_duration_seconds_bucket[5m])) by (le)",
      "legendFormat": "{{le}}",
      "format": "heatmap"
    }
  ],
  "options": {
    "calculate": false,
    "yAxis": {
      "unit": "s"
    }
  }
}
```

## Alert Panel Yapılandırması

### Eşik Bazlı Uyarılar

```json
{
  "type": "alert",
  "title": "Yüksek Hata Oranı",
  "alert": {
    "conditions": [
      {
        "type": "query",
        "query": { "params": ["A", "5m", "now"] },
        "reducer": { "type": "avg", "params": [] },
        "evaluator": { "type": "gt", "params": [0.05] }
      }
    ],
    "frequency": "1m",
    "for": "5m",
    "notifications": [
      { "uid": "slack-critical" },
      { "uid": "pagerduty-high" }
    ]
  },
  "targets": [
    {
      "expr": "sum(rate(coremusic_http_requests_total{status=~'5..'}[5m])) / sum(rate(coremusic_http_requests_total[5m]))",
      "refId": "A"
    }
  ]
}
```

## Dashboard Import/Export

### Export Formato

```json
{
  "__inputs": [
    {
      "name": "DS_PROMETHEUS",
      "label": "Prometheus",
      "description": "Prometheus veri kaynağı",
      "type": "datasource",
      "pluginId": "prometheus"
    }
  ],
  "__requires": [
    {
      "type": "grafana",
      "id": "grafana",
      "name": "Grafana",
      "version": "10.0.0"
    }
  ],
  "panels": [...],
  "templating": {
    "list": [
      {
        "name": "datasource",
        "type": "datasource",
        "query": "prometheus",
        "current": { "text": "Prometheus", "value": "Prometheus" }
      }
    ]
  }
}
```

### Provisioning Config

```yaml
# grafana/provisioning/dashboards/dashboard.yml
apiVersion: 1

providers:
  - name: 'COREMUSIC'
    orgId: 1
    folder: 'COREMUSIC'
    type: file
    disableDeletion: false
    editable: true
    options:
      path: /var/lib/grafana/dashboards/coremusic
      foldersFromFilesStructure: true
```

## Variable (Değişken) Yapılandırmaları

### Servis Seçimi

```json
{
  "name": "service",
  "type": "query",
  "query": "label_values(coremusic_http_requests_total, service)",
  "refresh": 2,
  "multi": true,
  "includeAll": true,
  "allValue": ".*"
}
```

### Zaman Aralığı

```json
{
  "name": "interval",
  "type": "interval",
  "query": "1m,5m,15m,30m,1h",
  "auto": true,
  "auto_min": "1m",
  "auto_count": 30
}
```

## Renk Paleti ve Eşik Değerleri

| Durum | Renk | Aralık |
|-------|------|--------|
| Sağlıklı | Yeşil | 0-70% |
| Uyarı | Sarı | 70-90% |
| Kritik | Kırmızı | 90-100% |
| Bilgi | Mavi | - |

## Bağımlılıklar

| Bileşen | Versiyon | Amaç |
|---------|----------|------|
| Grafana | 10.0+ | Dashboard platformu |
| Prometheus | 2.47+ | Metrik kaynağı |
| Elasticsearch | 8.0+ | Log kaynağı |
| MySQL | 8.0+ | Grafana veritabanı |

## Durum: Implementasyon

**Aşama**: Hazırlık
**Öncelik**: Yüksek
**Tahmini Süre**: 1 hafta
**Notlar**: Dashboard JSON'ları versiyon kontrolünde tutulmalıdır.
