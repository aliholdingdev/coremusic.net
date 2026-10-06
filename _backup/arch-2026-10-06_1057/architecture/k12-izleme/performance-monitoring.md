---
title: "Performance Monitoring"
layer: K12
category: "İzleme"
date: 2026-09-20
---

# Performance Monitoring

## Genel Bakış

Performans izleme, COREMUSIC platformunun yanıt süreleri,throughput ve kaynak kullanımını gerçek zamanlı olarak takip eder. APM (Application Performance Monitoring) araçları ile servis seviyesi anlaşma (SLA) metrikleri ölçülür ve performans darboğazları tespit edilir.

## Temel Metrikler

### RED Metrikleri (Rate, Errors, Duration)

```
┌─────────────────────────────────────────────────────────────────┐
│                    PERFORMANS METRIKLERI                         │
├─────────────────────────────────────────────────────────────────┤
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐         │
│  │     RATE     │  │    ERRORS    │  │   DURATION   │         │
│  │  1.2k req/s  │  │   0.02%     │  │  p50: 45ms   │         │
│  │  ↑ %12       │  │   ↓ %5      │  │  p95: 120ms  │         │
│  │              │  │              │  │  p99: 250ms  │         │
│  └──────────────┘  └──────────────┘  └──────────────┘         │
└─────────────────────────────────────────────────────────────────┘
```

| Metrik | Tanım | SLA Hedefi |
|--------|-------|------------|
| Request Rate | Saniyedeki istek sayısı | - |
| Error Rate | Hata oranı (%) | < %0.1 |
| Latency (p50) | Medyan yanıt süresi | < 100ms |
| Latency (p95) | %95 yanıt süresi | < 500ms |
| Latency (p99) | %99 yanıt süresi | < 1000ms |

### USE Metrikleri (Utilization, Saturation, Errors)

| Metrik | Tanım | Uyarı Eşiği |
|--------|-------|-------------|
| CPU Utilization | CPU kullanım oranı | > %80 |
| Memory Utilization | Bellek kullanım oranı | > %85 |
| Disk Utilization | Disk kullanım oranı | > %90 |
| Network Saturation | Ağ doygunluk | > %70 |
| Queue Saturation | Kuyruk doluluk | > 1000 |

## APM Entegrasyonu

### OpenTelemetry Setup

```python
from opentelemetry import trace, metrics
from opentelemetry.sdk.trace import TracerProvider
from opentelemetry.sdk.trace.export import BatchSpanProcessor
from opentelemetry.sdk.metrics import MeterProvider
from opentelemetry.sdk.metrics.export import PeriodicExportingMetricReader
from opentelemetry.exporter.otlp.proto.grpc.trace_exporter import OTLPSpanExporter
from opentelemetry.exporter.otlp.proto.grpc.metric_exporter import OTLPMetricExporter

# Trace provider
trace_provider = TracerProvider()
otlp_exporter = OTLPSpanExporter(endpoint="otel-collector:4317")
trace_provider.add_span_processor(BatchSpanProcessor(otlp_exporter))
trace.set_tracer_provider(trace_provider)

# Metric reader
metric_reader = PeriodicExportingMetricReader(
    OTLPMetricExporter(endpoint="otel-collector:4317"),
    export_interval_millis=15000
)
meter_provider = MeterProvider(metric_readers=[metric_reader])
metrics.set_meter_provider(meter_provider)

# Meter ve tracer oluştur
meter = metrics.get_meter("coremusic-app")
tracer = trace.get_tracer("coremusic-app")
```

### Custom Metrics

```python
from opentelemetry.metrics import Counter, Histogram, UpDownCounter

# İstek sayacı
request_counter = meter.create_counter(
    name="http.server.request.count",
    description="Toplam HTTP istek sayısı",
    unit="1"
)

# Yanıt süresi histogramı
request_duration = meter.create_histogram(
    name="http.server.duration",
    description="HTTP istek yanıt süresi",
    unit="ms"
)

# Aktif bağlantı sayacı
active_connections = meter.create_up_down_counter(
    name="http.server.active_connections",
    description="Aktif bağlantı sayısı",
    unit="1"
)

# Kullanım örneği
def handle_request(request):
    active_connections.add(1, {"endpoint": request.path})
    start_time = time.time()
    
    try:
        response = process_request(request)
        duration = (time.time() - start_time) * 1000
        
        request_counter.add(1, {
            "method": request.method,
            "endpoint": request.path,
            "status": response.status_code
        })
        request_duration.record(duration, {
            "method": request.method,
            "endpoint": request.path
        })
        
        return response
    finally:
        active_connections.add(-1, {"endpoint": request.path})
```

### Flask Middleware

```python
from flask import Flask, request, g
import time

app = Flask(__name__)

@app.before_request
def before_request():
    g.start_time = time.time()
    active_connections.add(1, {"endpoint": request.path})

@app.after_request
def after_request(response):
    duration = (time.time() - g.start_time) * 1000
    
    request_counter.add(1, {
        "method": request.method,
        "endpoint": request.path,
        "status": response.status_code
    })
    request_duration.record(duration, {
        "method": request.method,
        "endpoint": request.path
    })
    
    active_connections.add(-1, {"endpoint": request.path})
    
    response.headers['X-Response-Time'] = f'{duration:.2f}ms'
    return response
```

## Veritabanı Performansı

### Query Monitoring

```python
import time
from contextlib import contextmanager

class DatabasePerformanceMonitor:
    def __init__(self, meter):
        self.query_counter = meter.create_counter(
            name="db.query.count",
            description="Veritabanı sorgu sayısı"
        )
        self.query_duration = meter.create_histogram(
            name="db.query.duration",
            description="Sorgu süresi",
            unit="ms"
        )
        self.active_queries = meter.create_up_down_counter(
            name="db.active_queries",
            description="Aktif sorgu sayısı"
        )
    
    @contextmanager
    def track_query(self, query_type: str, table: str):
        self.active_queries.add(1, {"type": query_type, "table": table})
        start_time = time.time()
        
        try:
            yield
        finally:
            duration = (time.time() - start_time) * 1000
            self.query_counter.add(1, {
                "type": query_type,
                "table": table
            })
            self.query_duration.record(duration, {
                "type": query_type,
                "table": table
            })
            self.active_queries.add(-1, {"type": query_type, "table": table})

# Kullanım
db_monitor = DatabasePerformanceMonitor(meter)

with db_monitor.track_query("SELECT", "tracks"):
    results = db.session.query(Track).all()
```

### Connection Pool Monitoring

```python
class ConnectionPoolMonitor:
    def __init__(self, meter, pool):
        self.pool = pool
        
        self.pool_size = meter.create_up_down_counter(
            name="db.pool.size",
            description="Bağlantı havuzu boyutu"
        )
        self.pool_active = meter.create_up_down_counter(
            name="db.pool.active",
            description="Aktif bağlantı sayısı"
        )
        self.pool_idle = meter.create_up_down_counter(
            name="db.pool.idle",
            description="Boşta olan bağlantı sayısı"
        )
    
    def get_metrics(self):
        return {
            "size": self.pool.size(),
            "checked_in": self.pool.checkedin(),
            "checked_out": self.pool.checkedout(),
            "overflow": self.pool.overflow()
        }
```

## Cache Performansı

### Redis Monitoring

```python
class CachePerformanceMonitor:
    def __init__(self, meter, redis_client):
        self.redis = redis_client
        
        self.hit_counter = meter.create_counter(
            name="cache.hit",
            description="Cache hit sayısı"
        )
        self.miss_counter = meter.create_counter(
            name="cache.miss",
            description="Cache miss sayısı"
        )
        self.operation_duration = meter.create_histogram(
            name="cache.operation.duration",
            description="Cache işlem süresi",
            unit="ms"
        )
    
    def get(self, key: str):
        start_time = time.time()
        
        try:
            value = self.redis.get(key)
            duration = (time.time() - start_time) * 1000
            
            if value is not None:
                self.hit_counter.add(1, {"key_pattern": key.split(":")[0]})
            else:
                self.miss_counter.add(1, {"key_pattern": key.split(":")[0]})
            
            self.operation_duration.record(duration, {
                "operation": "get",
                "hit": value is not None
            })
            
            return value
        except Exception as e:
            duration = (time.time() - start_time) * 1000
            self.operation_duration.record(duration, {
                "operation": "get",
                "error": True
            })
            raise
    
    def get_hit_rate(self):
        stats = self.redis.info("stats")
        hits = stats.get("keyspace_hits", 0)
        misses = stats.get("keyspace_misses", 0)
        total = hits + misses
        
        return hits / total if total > 0 else 0
```

## SLA Takibi

### SLA Calculator

```python
from datetime import datetime, timedelta

class SLACalculator:
    def __init__(self):
        self.targets = {
            "availability": 99.9,  # %99.9 uptime
            "latency_p99": 1000,   # 1000ms
            "error_rate": 0.1      # %0.1
        }
    
    def calculate_availability(self, downtime_minutes: float, period_minutes: float):
        uptime_minutes = period_minutes - downtime_minutes
        availability = (uptime_minutes / period_minutes) * 100
        return {
            "availability": round(availability, 3),
            "target": self.targets["availability"],
            "met": availability >= self.targets["availability"],
            "downtime_allowed_minutes": period_minutes * (1 - self.targets["availability"] / 100)
        }
    
    def calculate_error_budget(self, period_days: int = 30):
        total_budget_minutes = period_days * 24 * 60 * (1 - self.targets["availability"] / 100)
        return {
            "error_budget_minutes": total_budget_minutes,
            "error_budget_hours": total_budget_minutes / 60,
            "target_availability": self.targets["availability"]
        }
```

## Dashboard Panelleri

### Grafana Panel Configurations

```json
{
  "panels": [
    {
      "title": "Request Rate",
      "type": "timeseries",
      "targets": [{
        "expr": "sum(rate(http_server_request_count_total[1m]))",
        "legendFormat": "{{method}} {{endpoint}}"
      }]
    },
    {
      "title": "Response Time Distribution",
      "type": "heatmap",
      "targets": [{
        "expr": "sum(rate(http_server_duration_bucket[5m])) by (le)",
        "format": "heatmap"
      }]
    },
    {
      "title": "Error Rate",
      "type": "stat",
      "targets": [{
        "expr": "sum(rate(http_server_request_count_total{status=~'5..'}[5m])) / sum(rate(http_server_request_count_total[5m])) * 100"
      }],
      "fieldConfig": {
        "defaults": {
          "unit": "percent",
          "thresholds": {
            "steps": [
              {"value": 0, "color": "green"},
              {"value": 0.1, "color": "yellow"},
              {"value": 1, "color": "red"}
            ]
          }
        }
      }
    }
  ]
}
```

## Bağımlılıklar

| Bileşen | Versiyon | Amaç |
|---------|----------|------|
| opentelemetry-api | 1.20+ | Metrik API |
| opentelemetry-sdk | 1.20+ | Metrik SDK |
| opentelemetry-exporter-otlp | 1.20+ | OTLP export |
| prometheus-client | 0.17+ | Prometheus export |

## Durum: Implementasyon

**Aşama**: Hazırlık
**Öncelik**: Yüksek
**Tahmini Süre**: 1 hafta
**Notlar**: SLA hedefleri ile birlikte uygulanmalıdır.
