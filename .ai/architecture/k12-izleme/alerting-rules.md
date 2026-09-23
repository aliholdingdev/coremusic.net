---
title: "Alerting Rules"
layer: K12
category: "İzleme"
date: 2026-09-20
---

# Alerting Rules

## Genel Bakış

Uyarı kuralları, COREMUSIC platformunda anomali tespiti ve proaktif müdahale için tanımlanır. Prometheus Alertmanager ile entegre çalışan bu sistem, kritik eşik değerler aşıldığında otomatik bildirimler gönderir. Escalation politikaları ile uyarılar doğru kişilere ulaştırılır.

## Uyarı Seviyeleri

### Kritiklik Sınıflandırması

| Seviye | Tanım | Yanıt Süresi | Bildirim Kanalı |
|--------|-------|--------------|-----------------|
| P1-Critical | Servis tamamen çöktü | 5 dakika | PagerDuty + Slack + SMS |
| P2-High | Ciddi performans düşüşü | 15 dakika | PagerDuty + Slack |
| P3-Medium | Uyarı seviyesinde durum | 1 saat | Slack + Email |
| P4-Low | Bilgi amaçlı uyarı | 24 saat | Email |

## Alertmanager Konfigürasyonu

### Ana Config

```yaml
# alertmanager.yml
global:
  resolve_timeout: 5m
  smtp_smarthost: 'smtp.coremusic.com:587'
  smtp_from: 'alerts@coremusic.com'
  smtp_auth_username: 'alerts@coremusic.com'
  smtp_auth_password: 'password'
  slack_api_url: 'https://hooks.slack.com/services/xxx/yyy/zzz'

route:
  receiver: 'slack-default'
  group_by: ['alertname', 'severity']
  group_wait: 30s
  group_interval: 5m
  repeat_interval: 4h
  
  routes:
    - match:
        severity: critical
      receiver: 'pagerduty-critical'
      group_wait: 10s
      repeat_interval: 1h
      
    - match:
        severity: high
      receiver: 'pagerduty-high'
      group_wait: 30s
      repeat_interval: 2h
      
    - match:
        severity: medium
      receiver: 'slack-warnings'
      group_wait: 1m
      repeat_interval: 4h
      
    - match:
        severity: low
      receiver: 'email-info'
      group_wait: 5m
      repeat_interval: 24h

receivers:
  - name: 'slack-default'
    slack_configs:
      - channel: '#alerts'
        send_resolved: true
        title: '{{ .GroupLabels.alertname }}'
        text: '{{ .CommonAnnotations.summary }}'
  
  - name: 'pagerduty-critical'
    pagerduty_configs:
      - service_key: 'xxx-xxx-xxx'
        severity: critical
        description: '{{ .CommonAnnotations.summary }}'
        details:
          firing: '{{ .Alerts.Firing | len }}'
          resolved: '{{ .Alerts.Resolved | len }}'
  
  - name: 'slack-warnings'
    slack_configs:
      - channel: '#warnings'
        send_resolved: true
  
  - name: 'email-info'
    email_configs:
      - to: 'devops@coremusic.com'
        send_resolved: true

inhibit_rules:
  - source_match:
      severity: critical
    target_match:
      severity: high
    equal: ['alertname', 'instance']
  
  - source_match:
      severity: high
    target_match:
      severity: medium
    equal: ['alertname', 'instance']
```

## Prometheus Alert Rules

### Servis Durumu Kuralları

```yaml
# prometheus-rules/coremusic-alerts.yml
groups:
  - name: coremusic-service-alerts
    rules:
      # Servis availability
      - alert: ServiceDown
        expr: up{job=~"coremusic-.*"} == 0
        for: 1m
        labels:
          severity: critical
        annotations:
          summary: "Servis durdu: {{ $labels.instance }}"
          description: "{{ $labels.job }} servisi 1 dakikadır çalışmıyor."
      
      # Yüksek hata oranı
      - alert: HighErrorRate
        expr: |
          sum(rate(coremusic_http_requests_total{status=~"5.."}[5m])) 
          / sum(rate(coremusic_http_requests_total[5m])) > 0.05
        for: 5m
        labels:
          severity: high
        annotations:
          summary: "Yüksek hata oranı tespit edildi"
          description: "Hata oranı %5'i aşıyor. Mevcut değer: {{ $value | humanizePercentage }}"
      
      # Yanıt süresi
      - alert: HighLatency
        expr: |
          histogram_quantile(0.99, 
            sum(rate(coremusic_http_request_duration_seconds_bucket[5m])) by (le)
          ) > 1
        for: 10m
        labels:
          severity: high
        annotations:
          summary: "Yüksek yanıt süresi"
          description: "p99 yanıt süresi 1 saniyeyi aşıyor. Mevcut değer: {{ $value }}s"
```

### Altyapı Kuralları

```yaml
  - name: coremusic-infrastructure-alerts
    rules:
      # CPU kullanımı
      - alert: HighCpuUsage
        expr: 100 - (avg by(instance) (irate(node_cpu_seconds_total{mode="idle"}[5m])) * 100) > 80
        for: 10m
        labels:
          severity: medium
        annotations:
          summary: "Yüksek CPU kullanımı: {{ $labels.instance }}"
          description: "CPU kullanımı %80'i aşıyor. Mevcut değer: {{ $value }}%"
      
      # Bellek kullanımı
      - alert: HighMemoryUsage
        expr: |
          (node_memory_MemTotal_bytes - node_memory_MemAvailable_bytes) 
          / node_memory_MemTotal_bytes * 100 > 85
        for: 5m
        labels:
          severity: medium
        annotations:
          summary: "Yüksek bellek kullanımı: {{ $labels.instance }}"
          description: "Bellek kullanımı %85'i aşıyor."
      
      # Disk kullanımı
      - alert: DiskSpaceLow
        expr: |
          (node_filesystem_avail_bytes{mountpoint="/"} 
          / node_filesystem_size_bytes{mountpoint="/"}) * 100 < 15
        for: 10m
        labels:
          severity: high
        annotations:
          summary: "Disk alanı azalıyor: {{ $labels.instance }}"
          description: "Diskte %15'ten az alan kaldı."
```

### Veritabanı Kuralları

```yaml
  - name: coremusic-database-alerts
    rules:
      # Bağlantı havuzu doluluğu
      - alert: DatabaseConnectionPoolHigh
        expr: |
          mysql_global_status_threads_connected 
          / mysql_global_variables_max_connections * 100 > 80
        for: 5m
        labels:
          severity: high
        annotations:
          summary: "Veritabanı bağlantı havuzu dolmak üzere"
          description: "Bağlantı kullanımı %80'i aşıyor."
      
      # Sorgu süresi
      - alert: SlowQueries
        expr: rate(mysql_global_status_slow_queries[5m]) > 10
        for: 10m
        labels:
          severity: medium
        annotations:
          summary: "Yavaş sorgular artıyor"
          description: "Saniyede 10'dan fazla yavaş sorgu tespit edildi."
      
      # Replication lag
      - alert: ReplicationLag
        expr: mysql_slave_status_seconds_behind_master > 30
        for: 5m
        labels:
          severity: high
        annotations:
          summary: "Veritabanı replikasyon gecikmesi"
          description: "Replikasyon {{ $value }} saniye geride."
```

### Redis Kuralları

```yaml
  - name: coremusic-redis-alerts
    rules:
      # Memory usage
      - alert: RedisHighMemory
        expr: |
          redis_memory_used_bytes / redis_memory_max_bytes * 100 > 80
        for: 5m
        labels:
          severity: medium
        annotations:
          summary: "Redis bellek kullanımı yüksek"
          description: "Redis bellek kullanımı %80'i aşıyor."
      
      # Hit rate
      - alert: RedisLowHitRate
        expr: |
          redis_keyspace_hits_total 
          / (redis_keyspace_hits_total + redis_keyspace_misses_total) < 0.8
        for: 15m
        labels:
          severity: medium
        annotations:
          summary: "Redis hit rate düşük"
          description: "Cache hit rate %80'in altına düştü."
```

## Escalation Politikaları

### Escalation Matrisi

```
┌─────────────────────────────────────────────────────────────────┐
│                    ESCALATION FLÖWÜ                              │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Tetikleme ──→ İlk Yanıt (5dk) ──→ Çözüm Yok ──→ Escalation  │
│       │              │                    │            │        │
│       │         On-call ekibi       +15 dakika    Senior DevOps│
│       │              │                    │            │        │
│       │         Çözüm buldu         Çözüm yok    +30 dakika    │
│       │              │                    │            │        │
│       │         Kapat              Escalation    Engineering   │
│       │                              Manager       Lead         │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Python Escalation Manager

```python
from datetime import datetime, timedelta
from typing import List, Dict
from enum import Enum

class EscalationLevel(Enum):
    L1 = "l1_oncall"
    L2 = "l2_senior"
    L3 = "l3_manager"
    L4 = "l4_engineering_lead"

class EscalationPolicy:
    def __init__(self):
        self.levels = {
            EscalationLevel.L1: {
                "timeout_minutes": 5,
                "contacts": ["oncall@coremusic.com"],
                "channels": ["slack", "pagerduty"]
            },
            EscalationLevel.L2: {
                "timeout_minutes": 15,
                "contacts": ["senior-devops@coremusic.com"],
                "channels": ["slack", "pagerduty", "sms"]
            },
            EscalationLevel.L3: {
                "timeout_minutes": 30,
                "contacts": ["manager@coremusic.com"],
                "channels": ["slack", "pagerduty", "sms", "phone"]
            },
            EscalationLevel.L4: {
                "timeout_minutes": 60,
                "contacts": ["engineering-lead@coremusic.com"],
                "channels": ["all"]
            }
        }
    
    def get_next_level(self, current_level: EscalationLevel) -> EscalationLevel:
        levels = list(EscalationLevel)
        current_index = levels.index(current_level)
        if current_index < len(levels) - 1:
            return levels[current_index + 1]
        return current_level

class EscalationManager:
    def __init__(self, policy: EscalationPolicy):
        self.policy = policy
        self.active_incidents: Dict[str, Dict] = {}
    
    def create_incident(self, alert_name: str, severity: str) -> str:
        incident_id = f"INC-{datetime.now().strftime('%Y%m%d')}-{hash(alert_name) % 10000:04d}"
        
        self.active_incidents[incident_id] = {
            "alert_name": alert_name,
            "severity": severity,
            "created_at": datetime.now(),
            "current_level": EscalationLevel.L1,
            "escalation_history": [],
            "status": "open"
        }
        
        self._notify_level(incident_id, EscalationLevel.L1)
        return incident_id
    
    def _notify_level(self, incident_id: str, level: EscalationLevel):
        config = self.policy.levels[level]
        
        for channel in config["channels"]:
            self._send_notification(
                channel=channel,
                recipients=config["contacts"],
                incident_id=incident_id,
                level=level
            )
    
    def check_escalation(self, incident_id: str):
        incident = self.active_incidents.get(incident_id)
        if not incident or incident["status"] != "open":
            return
        
        time_since_created = datetime.now() - incident["created_at"]
        current_config = self.policy.levels[incident["current_level"]]
        
        if time_since_created > timedelta(minutes=current_config["timeout_minutes"]):
            next_level = self.policy.get_next_level(incident["current_level"])
            
            if next_level != incident["current_level"]:
                incident["escalation_history"].append({
                    "level": incident["current_level"],
                    "timestamp": datetime.now()
                })
                incident["current_level"] = next_level
                self._notify_level(incident_id, next_level)
```

## Uyarı Şablonları

### Slack Notification Template

```json
{
  "blocks": [
    {
      "type": "header",
      "text": {
        "type": "plain_text",
        "text": "🚨 Alert: {{ .AlertName }}"
      }
    },
    {
      "type": "section",
      "fields": [
        {
          "type": "mrkdwn",
          "text": "*Severity:*\n{{ .Labels.severity }}"
        },
        {
          "type": "mrkdwn",
          "text": "*Instance:*\n{{ .Labels.instance }}"
        }
      ]
    },
    {
      "type": "section",
      "text": {
        "type": "mrkdwn",
        "text": "*Description:*\n{{ .Annotations.description }}"
      }
    },
    {
      "type": "actions",
      "elements": [
        {
          "type": "button",
          "text": {
            "type": "plain_text",
            "text": "View in Grafana"
          },
          "url": "https://grafana.coremusic.com/d/xxx?var-instance={{ .Labels.instance }}"
        }
      ]
    }
  ]
}
```

## Bağımlılıklar

| Bileşen | Versiyon | Amaç |
|---------|----------|------|
| Alertmanager | 0.26+ | Uyarı yönetimi |
| Prometheus | 2.47+ | Uyarı kuralları |
| PagerDuty | - | Acil durum bildirimi |
| Slack | - | Takım bildirimleri |

## Durum: Implementasyon

**Aşama**: Hazırlık
**Öncelik**: Yüksek
**Tahmini Süre**: 3 gün
**Notlar**: On-call takvimi ile birlikte yapılandırılmalıdır.
