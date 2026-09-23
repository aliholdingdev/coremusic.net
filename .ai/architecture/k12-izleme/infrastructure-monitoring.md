---
title: "Infrastructure Monitoring"
layer: K12
category: "İzleme"
date: 2026-09-20
---

# Infrastructure Monitoring

## Genel Bakış

Altyapı izleme, COREMUSIC platformunun çalışan tüm sunucuları, konteynerleri ve servislerinin temel metriklerini (CPU, RAM, disk, ağ) takip eder. Node Exporter, cAdvisor ve其他探针ları ile toplanan veriler, altyapı sağlığını ve performansını değerlendirir.

## İzlenen Kaynaklar

### Fiziksel/Virtual Sunucular

| Kaynak | Metrik | Uyarı Eşiği |
|--------|--------|-------------|
| CPU | Kullanım oranı, load average | > %80, > 2.0 |
| Bellek | Kullanım, swap | > %85, swap > 0 |
| Disk | Kullanım, I/O, latency | > %90, > 100MB/s |
| Ağ | Bant genişliği, paket kaybı | > %70, > %1 |

### Konteynerler

| Kaynak | Metrik | Uyarı Eşiği |
|--------|--------|-------------|
| CPU | Limit Usage | > %80 |
| Bellek | Limit Usage | > %85 |
| Ağ | RX/TX | > %70 |
| Disk I/O | Read/Write | > 100MB/s |

## Node Exporter Konfigürasyonu

### Ansible Playbook

```yaml
# ansible/roles/node-exporter/tasks/main.yml
- name: Create node-exporter user
  user:
    name: node-exporter
    shell: /bin/false
    system: yes

- name: Download node-exporter
  get_url:
    url: "https://github.com/prometheus/node_exporter/releases/download/v1.6.1/node_exporter-1.6.1.linux-amd64.tar.gz"
    dest: /tmp/node_exporter.tar.gz

- name: Extract node-exporter
  unarchive:
    src: /tmp/node_exporter.tar.gz
    dest: /usr/local/bin/
    remote_src: yes

- name: Create systemd service
  template:
    src: node-exporter.service.j2
    dest: /etc/systemd/system/node-exporter.service
  notify: restart node-exporter

- name: Enable and start node-exporter
  systemd:
    name: node-exporter
    state: started
    enabled: yes
```

### Systemd Service

```ini
# node-exporter.service
[Unit]
Description=Prometheus Node Exporter
Wants=network-online.target
After=network-online.target

[Service]
User=node-exporter
Group=node-exporter
Type=simple
ExecStart=/usr/local/bin/node_exporter \
  --collector.systemd \
  --collector.processes \
  --web.listen-address=:9100 \
  --collector.textfile.directory=/var/lib/node_exporter/textfile_collector

[Install]
WantedBy=multi-user.target
```

## Custom Collector'lar

### Disk I/O Collector

```python
#!/usr/bin/env python3
# /var/lib/node_exporter/textfile_collector/disk_io.prom

import psutil
import time

def collect_disk_io():
    disk_io = psutil.disk_io_counters()
    
    metrics = [
        f'coremusic_disk_read_bytes_total {disk_io.read_bytes}',
        f'coremusic_disk_write_bytes_total {disk_io.write_bytes}',
        f'coremusic_disk_read_count_total {disk_io.read_count}',
        f'coremusic_disk_write_count_total {disk_io.write_count}',
        f'coremusic_disk_read_time_ms_total {disk_io.read_time}',
        f'coremusic_disk_write_time_ms_total {disk_io.write_time}'
    ]
    
    return '\n'.join(metrics)

if __name__ == '__main__':
    while True:
        with open('/var/lib/node_exporter/textfile_collector/disk_io.prom', 'w') as f:
            f.write(collect_disk_io())
        time.sleep(30)
```

### Network Collector

```python
#!/usr/bin/env python3
# /var/lib/node_exporter/textfile_collector/network.prom

import psutil
import time

def collect_network():
    net_io = psutil.net_io_counters()
    interfaces = psutil.net_io_counters(pernic=True)
    
    metrics = []
    
    # Genel metrikler
    metrics.append(f'coremusic_network_bytes_sent_total {net_io.bytes_sent}')
    metrics.append(f'coremusic_network_bytes_recv_total {net_io.bytes_recv}')
    metrics.append(f'coremusic_network_packets_sent_total {net_io.packets_sent}')
    metrics.append(f'coremusic_network_packets_recv_total {net_io.packets_recv}')
    metrics.append(f'coremusic_network_errs_total {net_io.errin + net_io.errout}')
    metrics.append(f'coremusic_network_drop_total {net_io.dropin + net_io.dropout}')
    
    # Arayüz bazlı metrikler
    for iface, counters in interfaces.items():
        labels = f'interface="{iface}"'
        metrics.append(f'coremusic_network_interface_bytes_sent_total{{{labels}}} {counters.bytes_sent}')
        metrics.append(f'coremusic_network_interface_bytes_recv_total{{{labels}}} {counters.bytes_recv}')
    
    return '\n'.join(metrics)

if __name__ == '__main__':
    while True:
        with open('/var/lib/node_exporter/textfile_collector/network.prom', 'w') as f:
            f.write(collect_network())
        time.sleep(30)
```

## Docker/Kubernetes Monitoring

### cAdvisor Config

```yaml
# docker-compose.yml
services:
  cadvisor:
    image: gcr.io/cadvisor/cadvisor:v0.47.0
    container_name: cadvisor
    volumes:
      - /:/rootfs:ro
      - /var/run:/var/run:ro
      - /sys:/sys:ro
      - /var/lib/docker/:/var/lib/docker:ro
    ports:
      - "8080:8080"
    restart: unless-stopped
```

### Prometheus Service Discovery

```yaml
# prometheus.yml
scrape_configs:
  - job_name: 'docker'
    docker_sd_configs:
      - host: unix:///var/run/docker.sock
        filters:
          - name: 'label'
            values: ['prometheus.io/scrape=true']
    relabel_configs:
      - source_labels: [__meta_docker_container_name]
        regex: '/(.*)'
        target_label: container_name
      - source_labels: [__meta_docker_container_label_com_docker_compose_service]
        target_label: compose_service
      - source_labels: [__meta_docker_container_label_com_docker_compose_project]
        target_label: compose_project
```

### Kubernetes Service Monitor

```yaml
# kubernetes-service-monitor.yml
apiVersion: monitoring.coreos.com/v1
kind: ServiceMonitor
metadata:
  name: coremusic-app
  namespace: monitoring
  labels:
    release: prometheus
spec:
  selector:
    matchLabels:
      app: coremusic
  endpoints:
    - port: http-metrics
      interval: 15s
      path: /metrics
  namespaceSelector:
    matchNames:
      - coremusic
```

## Systemd Service Monitoring

### Service Health Check

```python
#!/usr/bin/env python3
# /var/lib/node_exporter/textfile_collector/services.prom

import subprocess
import os

SERVICES = [
    'nginx',
    'mysql',
    'redis',
    'coremusic-app',
    'prometheus',
    'grafana'
]

def check_service(service_name):
    try:
        result = subprocess.run(
            ['systemctl', 'is-active', service_name],
            capture_output=True,
            text=True,
            timeout=5
        )
        return 1 if result.stdout.strip() == 'active' else 0
    except Exception:
        return 0

def collect_services():
    metrics = []
    
    for service in SERVICES:
        status = check_service(service)
        metrics.append(f'coremusic_service_active{{service="{service}"}} {status}')
    
    return '\n'.join(metrics)

if __name__ == '__main__':
    with open('/var/lib/node_exporter/textfile_collector/services.prom', 'w') as f:
        f.write(collect_services())
```

## Alert Rules

### Infrastructure Alerts

```yaml
# infrastructure-alerts.yml
groups:
  - name: infrastructure
    rules:
      # CPU Alert
      - alert: HighCpuUsage
        expr: |
          100 - (avg by(instance) (irate(node_cpu_seconds_total{mode="idle"}[5m])) * 100) > 80
        for: 10m
        labels:
          severity: medium
        annotations:
          summary: "Yüksek CPU kullanımı: {{ $labels.instance }}"
      
      # Memory Alert
      - alert: HighMemoryUsage
        expr: |
          (1 - (node_memory_MemAvailable_bytes / node_memory_MemTotal_bytes)) * 100 > 85
        for: 5m
        labels:
          severity: medium
        annotations:
          summary: "Yüksek bellek kullanımı: {{ $labels.instance }}"
      
      # Disk Alert
      - alert: DiskSpaceLow
        expr: |
          (1 - (node_filesystem_avail_bytes{mountpoint="/"} / node_filesystem_size_bytes{mountpoint="/"})) * 100 > 85
        for: 10m
        labels:
          severity: high
        annotations:
          summary: "Disk alanı az: {{ $labels.instance }}"
      
      # Disk I/O Alert
      - alert: HighDiskIO
        expr: |
          rate(node_disk_io_time_seconds_total[5m]) > 0.9
        for: 15m
        labels:
          severity: medium
        annotations:
          summary: "Yüksek disk I/O: {{ $labels.instance }}"
      
      # Network Alert
      - alert: HighNetworkTraffic
        expr: |
          rate(node_network_receive_bytes_total{device!="lo"}[5m]) > 100000000
        for: 10m
        labels:
          severity: medium
        annotations:
          summary: "Yüksek ağ trafiği: {{ $labels.instance }}"
```

## Dashboard Panels

### Grafana Dashboard

```json
{
  "title": "Infrastructure Overview",
  "panels": [
    {
      "title": "CPU Usage by Instance",
      "type": "timeseries",
      "targets": [{
        "expr": "100 - (avg by(instance) (irate(node_cpu_seconds_total{mode='idle'}[5m])) * 100)",
        "legendFormat": "{{ instance }}"
      }],
      "fieldConfig": {
        "defaults": {
          "unit": "percent",
          "min": 0,
          "max": 100
        }
      }
    },
    {
      "title": "Memory Usage",
      "type": "gauge",
      "targets": [{
        "expr": "(1 - (node_memory_MemAvailable_bytes / node_memory_MemTotal_bytes)) * 100"
      }],
      "fieldConfig": {
        "defaults": {
          "unit": "percent",
          "thresholds": {
            "steps": [
              {"value": 0, "color": "green"},
              {"value": 70, "color": "yellow"},
              {"value": 85, "color": "red"}
            ]
          }
        }
      }
    },
    {
      "title": "Disk Usage",
      "type": "table",
      "targets": [{
        "expr": "(1 - (node_filesystem_avail_bytes / node_filesystem_size_bytes)) * 100",
        "format": "table",
        "instant": true
      }]
    }
  ]
}
```

## Bağımlılıklar

| Bileşen | Versiyon | Amaç |
|---------|----------|------|
| Node Exporter | 1.6+ | Sistem metrikleri |
| cAdvisor | 0.47+ | Konteyner metrikleri |
| Prometheus | 2.47+ | Metrik toplama |
| Grafana | 10.0+ | Dashboard |

## Durum: Implementasyon

**Aşama**: Hazırlık
**Öncelik**: Yüksek
**Tahmini Süre**: 5 gün
**Notlar**: Tüm sunucularda Node Exporter kurulmalıdır.
