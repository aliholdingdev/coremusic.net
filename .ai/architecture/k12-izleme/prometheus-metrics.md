---
title: "Prometheus Metrics"
layer: K12
category: "İzleme"
date: 2026-09-20
---

# Prometheus Metrics

## Genel Bakış

Prometheus, COREMUSIC platformunda_pull tabanlı metrik toplama sistemi olarak kullanılır. Her bileşen, HTTP endpoint üzerinden metriklerini dışa aktarır ve Prometheus bu metrikleri düzenli aralıklarla çeker. Custom metrics ile iş özelindeki KPI'lar takip edilir.

## Metrik Tipleri

### Counter (Sayaç)
Artan değerler için kullanılır. Sadece yukarı yönde değişir.

```python
# Python örneği
from prometheus_client import Counter

http_requests_total = Counter(
    'coremusic_http_requests_total',
    'Toplam HTTP istek sayısı',
    ['method', 'endpoint', 'status']
)

# Kullanım
http_requests_total.labels(
    method='GET',
    endpoint='/api/v1/tracks',
    status='200'
).inc()
```

### Gauge (Gösterge)
Artan ve azalan değerler için kullanılır.

```python
from prometheus_client import Gauge

active_connections = Gauge(
    'coremusic_active_connections',
    'Aktif bağlantı sayısı',
    ['service']
)

# Kullanım
active_connections.labels(service='audio-engine').set(42)
```

### Histogram (Histogram)
Değerlerin dağılımını ölçer. Percentile hesaplamak için kullanılır.

```python
from prometheus_client import Histogram

request_duration = Histogram(
    'coremusic_request_duration_seconds',
    'İstek yanıt süresi',
    ['method', 'endpoint'],
    buckets=[0.01, 0.025, 0.05, 0.1, 0.25, 0.5, 1.0, 2.5, 5.0, 10.0]
)

# Kullanım
with request_duration.labels(method='GET', endpoint='/api/v1/tracks').time():
    process_request()
```

### Summary (Özet)
Client tarafında percentile hesaplar.

```python
from prometheus_client import Summary

processing_time = Summary(
    'coremusic_audio_processing_seconds',
    'Ses işleme süresi',
    ['codec', 'bitrate']
)

# Kullanım
processing_time.labels(codec='mp3', bitrate='320k').observe(0.25)
```

## Custom Metrics

### İş Metrikleri

```python
# Aktif kullanıcı sayısı
active_users = Gauge(
    'coremusic_active_users',
    'Aktif kullanıcı sayısı',
    ['platform']
)

# Çalma listesi oluşturma oranı
playlist_creation_rate = Counter(
    'coremusic_playlist_creation_total',
    'Oluşturulan çalma listesi sayısı',
    ['user_type']
)

# Ses kalitesi metrikleri
audio_quality_score = Histogram(
    'coremusic_audio_quality_score',
    'Ses kalite puanı (0-100)',
    ['codec', 'sample_rate'],
    buckets=[10, 20, 30, 40, 50, 60, 70, 80, 90, 100]
)
```

### Sistem Metrikleri

```python
# Bellek kullanımı
memory_usage = Gauge(
    'coremusic_memory_usage_bytes',
    'Bellek kullanımı (byte)',
    ['component', 'type']
)

# CPU kullanımı
cpu_usage = Gauge(
    'coremusic_cpu_usage_percent',
    'CPU kullanım oranı (%)',
    ['component']
)

# Disk I/O
disk_io_operations = Counter(
    'coremusic_disk_io_operations_total',
    'Disk I/O işlem sayısı',
    ['operation', 'device']
)
```

### Ağ Metrikleri

```python
# Ağ trafiği
network_bytes = Counter(
    'coremusic_network_bytes_total',
    'Ağ trafiği (byte)',
    ['direction', 'interface']
)

# Bağlantı hatları
connection_errors = Counter(
    'coremusic_connection_errors_total',
    'Bağlantı hata sayısı',
    ['error_type', 'service']
)
```

## Recording Rules

Önceden hesaplanan metrikler ile sorgu performansını artırma.

```yaml
# prometheus-rules.yaml
groups:
  - name: coremusic-recording-rules
    interval: 30s
    rules:
      # Ortalama yanıt süresi
      - record: coremusic:http_request_duration_seconds:avg
        expr: rate(coremusic_http_request_duration_seconds_sum[5m]) 
              / rate(coremusic_http_request_duration_seconds_count[5m])
      
      # Hata oranı
      - record: coremusic:http_requests:error_rate
        expr: rate(coremusic_http_requests_total{status=~"5.."}[5m]) 
              / rate(coremusic_http_requests_total[5m])
      
      # Aktif kullanıcı ortalaması
      - record: coremusic:active_users:avg5m
        expr: avg_over_time(coremusic_active_users[5m])
      
      # Bellek kullanım yüzdesi
      - record: coremusic:memory_usage:percent
        expr: coremusic_memory_usage_bytes{type="used"} 
              / coremusic_memory_usage_bytes{type="total"} * 100
```

## PromQL Sorgu Örnekleri

### Temel Sorgular

```promql
# Son 5 dakikadaki ortalama yanıt süresi
rate(coremusic_http_request_duration_seconds_sum[5m]) 
/ rate(coremusic_http_request_duration_seconds_count[5m])

# Hata oranı (yüzde)
sum(rate(coremusic_http_requests_total{status=~"5.."}[5m])) 
/ sum(rate(coremusic_http_requests_total[5m])) * 100

# Saniyedeki istek sayısı
sum(rate(coremusic_http_requests_total[1m])) 

# p99 yanıt süresi
histogram_quantile(0.99, 
  sum(rate(coremusic_http_request_duration_seconds_bucket[5m])) by (le)
)
```

### İleri Düzey Sorgular

```promql
# Ani artışı tespit etme
deriv(coremusic_http_requests_total[1h]) > 100

# Kullanıcı trend analizi
predict_linear(coremusic_active_users[24h], 3600 * 24)

# Bağımlı metriklerin karşılaştırması
coremusic:memory_usage:percent > 80 
and 
rate(coremusic_http_request_duration_seconds_sum[5m]) > 1
```

## Konfigürasyon

### Prometheus Config

```yaml
# prometheus.yml
global:
  scrape_interval: 15s
  evaluation_interval: 15s
  scrape_timeout: 10s

scrape_configs:
  - job_name: 'coremusic-app'
    static_configs:
      - targets: ['app:8080']
    metrics_path: '/metrics'
    scrape_interval: 10s
    
  - job_name: 'coremusic-audio-engine'
    static_configs:
      - targets: ['audio-engine:9090']
    metrics_path: '/metrics'
    
  - job_name: 'coremusic-mysql'
    static_configs:
      - targets: ['mysqld-exporter:9104']
    metrics_path: '/metrics'
    
  - job_name: 'coremusic-redis'
    static_configs:
      - targets: ['redis-exporter:9121']
    metrics_path: '/metrics'

  - job_name: 'node-exporter'
    static_configs:
      - targets: ['node-exporter:9100']
    metrics_path: '/metrics'
```

### Relabeling

```yaml
scrape_configs:
  - job_name: 'coremusic-services'
    relabel_configs:
      - source_labels: [__address__]
        regex: '(.+):(\d+)'
        target_label: instance
        replacement: '${1}'
      - source_labels: [__meta_docker_container_name]
        target_label: container_name
```

## Bağımlılıklar

| Bileşen | Versiyon | Amaç |
|---------|----------|------|
| Prometheus | 2.47+ | Metrik toplama ve saklama |
| prometheus_client | 0.17+ | Python kütüphanesi |
| Prometheus Pushgateway | 1.6+ | Batch job metrikleri |
| Node Exporter | 1.6+ | Sistem metrikleri |
| MySQL Exporter | 0.15+ | MySQL metrikleri |
| Redis Exporter | 1.5+ | Redis metrikleri |

## Performans İpuçları

1. **Metric Cardinality**: Yüksek kardinalite sorunu yaşamamak için label değerlerini sınırlayın
2. **Scrape Interval**: Metrik türüne göre scrape sıklığını ayarlayın
3. **Recording Rules**: Karmaşık sorguları önceden hesaplayın
4. **Retention**: Saklama süresini ihtiyaca göre belirleyin

## Durum: Implementasyon

**Aşama**: Hazırlık
**Öncelik**: Yüksek
**Tahmini Süre**: 1 hafta
**Notlar**: Tüm servislerin /metrics endpoint'i sunması gerekmektedir.
