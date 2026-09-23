---
title: "Distributed Tracing"
layer: K12
category: "İzleme"
date: 2026-09-20
---

# Distributed Tracing

## Genel Bakış

Dağıtık izleme, COREMUSIC platformunda servisler arası istek akışını takip eder. OpenTelemetry standartlarını kullanan Jaeger ile bir isteğin birden fazla servis üzerinden geçiş süresi, hatalar ve darboğazlar tespit edilir. Bu sayede performans sorunları ve hataların kök nedeni bulunur.

## Tracing Mimarisi

```
┌─────────────────────────────────────────────────────────────────┐
│                    DAĞITIK İZLEME MİMARİSİ                      │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Client ──→ API Gateway ──→ Auth Service ──→ Audio Service     │
│     │            │              │              │                │
│     │         Trace ID      Trace ID      Trace ID            │
│     │         Span ID       Span ID       Span ID             │
│     │            │              │              │                │
│     └────────────┴──────────────┴──────────────┘                │
│                          │                                      │
│                    Jaeger Collector                             │
│                          │                                      │
│                    Jaeger Query                                 │
│                          │                                      │
│                    Jaeger UI                                    │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## Temel Kavramlar

| Kavram | Tanım | Örnek |
|--------|-------|-------|
| Trace | Bir isteğin tüm yolculuğu | GET /api/tracks → DB → Cache → Response |
| Span | Trace içindeki tek bir işlem | "Query Database" |
| Trace ID | Benzersiz izleme identifier | abc123def456 |
| Span ID | Span'ın benzersiz identifier | span789 |
| Parent Span ID | Üst span referansı | span456 |
| Context Propagation | Trace bilgisinin servisler arası aktarımı | HTTP Header |

## OpenTelemetry Konfigürasyonu

### Python Setup

```python
from opentelemetry import trace
from opentelemetry.sdk.trace import TracerProvider
from opentelemetry.sdk.trace.export import BatchSpanProcessor
from opentelemetry.sdk.resources import Resource, SERVICE_NAME
from opentelemetry.exporter.otlp.proto.grpc.trace_exporter import OTLPSpanExporter
from opentelemetry.propagate import set_global_textmap
from opentelemetry.propagators.composite import CompositePropagator
from opentelemetry.propagators.textmap import TraceContextTextMapPropagator
from opentelemetry.propagators.baggage import W3CBaggagePropagator

# Resource tanımlama
resource = Resource.create({
    SERVICE_NAME: "coremusic-api",
    "service.version": "1.0.0",
    "deployment.environment": "production"
})

# Trace provider
trace_provider = TracerProvider(resource=resource)

# OTLP exporter (Jaeger'e gönderim)
otlp_exporter = OTLPSpanExporter(
    endpoint="jaeger-collector:4317",
    insecure=True
)

# Span processor
span_processor = BatchSpanProcessor(otlp_exporter)
trace_provider.add_span_processor(span_processor)

# Global trace provider'ı ayarla
trace.set_tracer_provider(trace_provider)

# Context propagation (W3C TraceContext + Baggage)
set_global_textmap(CompositePropagator([
    TraceContextTextMapPropagator(),
    W3CBaggagePropagator()
]))

# Tracer oluştur
tracer = trace.get_tracer("coremusic-api", "1.0.0")
```

### Flask Entegrasyonu

```python
from flask import Flask, request, g
from opentelemetry.instrumentation.flask import FlaskInstrumentor
from opentelemetry.instrumentation.requests import RequestsInstrumentor
from opentelemetry.instrumentation.sqlalchemy import SQLAlchemyInstrumentor

app = Flask(__name__)

# Otomatik enstrümantasyon
FlaskInstrumentor().instrument_app(app)
RequestsInstrumentor().instrument()
SQLAlchemyInstrumentor().instrument(engine=engine)

@app.route('/api/tracks/<track_id>')
def get_track(track_id):
    # Manuel span oluşturma
    with tracer.start_as_current_span(
        "get_track",
        attributes={"track.id": track_id}
    ) as span:
        # Veritabanı sorgusu
        with tracer.start_as_current_span("db.query") as db_span:
            track = db.session.query(Track).get(track_id)
            db_span.set_attribute("db.system", "mysql")
            db_span.set_attribute("db.statement", f"SELECT * FROM tracks WHERE id = {track_id}")
        
        # Önbellek kontrolü
        with tracer.start_as_current_span("cache.get") as cache_span:
            cached = cache.get(f"track:{track_id}")
            cache_span.set_attribute("cache.hit", cached is not None)
        
        return jsonify(track.to_dict())
```

### Flask Middleware

```python
from flask import Flask, request, g
import time

class TracingMiddleware:
    def __init__(self, app):
        self.app = app
    
    def __call__(self, scope, receive, send):
        if scope['type'] == 'http':
            # Header'lardan trace context'i çıkar
            headers = dict(scope.get('headers', []))
            trace_context = propagate.extract(headers)
            
            # Span oluştur
            with tracer.start_as_current_span(
                f"{scope['method']} {scope['path'].decode()}",
                context=trace_context,
                kind=trace.SpanKind.SERVER
            ) as span:
                # Span'a HTTP bilgileri ekle
                span.set_attribute("http.method", scope['method'])
                span.set_attribute("http.url", scope['path'].decode())
                span.set_attribute("http.host", headers.get(b'host', b'').decode())
                span.set_attribute("http.user_agent", headers.get(b'user-agent', b'').decode())
                
                # Request ID'yi context'e ekle
                g.span = span
                g.trace_id = span.context.trace_id
                g.span_id = span.context.span_id
        
        return self.app(scope, receive, send)
```

## Propagation Patterns

### HTTP Header Propagation

```python
import requests
from opentelemetry import propagate
from opentelemetry.trace import TraceContextTextMapPropagator

def make_request_with_context(url: str):
    # Mevcut trace context'i header'a ekle
    headers = {}
    propagate.inject(headers)
    
    # İsteği gönder
    response = requests.get(url, headers=headers)
    return response

# Kullanım
response = make_request_with_context("http://auth-service/api/validate")
```

### Message Queue Propagation

```python
import json
from opentelemetry import propagate

class KafkaProducer:
    def __init__(self, producer):
        self.producer = producer
    
    def send_message(self, topic: str, key: str, value: dict):
        # Trace context'i message header'a ekle
        headers = {}
        propagate.inject(headers)
        
        # Header'ları bytes'a çevir
        byte_headers = [(k.encode(), v.encode()) for k, v in headers.items()]
        
        self.producer.send(
            topic,
            key=key.encode(),
            value=json.dumps(value).encode(),
            headers=byte_headers
        )

class KafkaConsumer:
    def __init__(self, consumer):
        self.consumer = consumer
    
    def consume_messages(self):
        for message in self.consumer:
            # Message header'dan trace context'i çıkar
            headers = {k.decode(): v.decode() for k, v in message.headers or []}
            context = propagate.extract(headers)
            
            with tracer.start_as_current_span(
                "kafka.consume",
                context=context,
                kind=trace.SpanKind.CONSUMER
            ) as span:
                span.set_attribute("messaging.system", "kafka")
                span.set_attribute("messaging.topic", message.topic)
                span.set_attribute("messaging.partition", message.partition)
                
                # Mesajı işle
                process_message(message.value)
```

## Custom Spans

### Database Query Span

```python
from opentelemetry import trace

def query_database(query: str, params: dict = None):
    with tracer.start_as_current_span(
        "db.query",
        kind=trace.SpanKind.CLIENT,
        attributes={
            "db.system": "mysql",
            "db.statement": query[:500],
            "db.user": "coremusic"
        }
    ) as span:
        try:
            result = db.execute(query, params)
            span.set_status(trace.StatusCode.OK)
            return result
        except Exception as e:
            span.set_status(trace.StatusCode.ERROR, str(e))
            span.record_exception(e)
            raise
```

### External API Call Span

```python
def call_external_api(url: str, method: str = "GET"):
    with tracer.start_as_current_span(
        f"http.{method.lower()}",
        kind=trace.SpanKind.CLIENT,
        attributes={
            "http.url": url,
            "http.method": method
        }
    ) as span:
        try:
            headers = {}
            propagate.inject(headers)
            
            response = requests.request(
                method,
                url,
                headers=headers,
                timeout=30
            )
            
            span.set_attribute("http.status_code", response.status_code)
            span.set_attribute("http.response_size", len(response.content))
            
            if response.status_code >= 400:
                span.set_status(trace.StatusCode.ERROR, f"HTTP {response.status_code}")
            else:
                span.set_status(trace.StatusCode.OK)
            
            return response
        except Exception as e:
            span.set_status(trace.StatusCode.ERROR, str(e))
            span.record_exception(e)
            raise
```

## Jaeger Konfigürasyonu

### Docker Compose

```yaml
# jaeger/docker-compose.yml
version: '3.8'
services:
  jaeger:
    image: jaegertracing/all-in-one:1.50
    container_name: jaeger
    environment:
      - COLLECTOR_OTLP_ENABLED=true
      - SPAN_STORAGE_TYPE=elasticsearch
      - ES_SERVER_URLS=http://elasticsearch:9200
    ports:
      - "16686:16686"  # Jaeger UI
      - "4317:4317"    # OTLP gRPC
      - "4318:4318"    # OTLP HTTP
    networks:
      - monitoring

  elasticsearch:
    image: docker.elastic.co/elasticsearch/elasticsearch:8.10.0
    container_name: elasticsearch
    environment:
      - discovery.type=single-node
      - xpack.security.enabled=false
      - "ES_JAVA_OPTS=-Xms512m -Xmx512m"
    volumes:
      - es_data:/usr/share/elasticsearch/data
    networks:
      - monitoring

volumes:
  es_data:

networks:
  monitoring:
    driver: bridge
```

### Sampling Konfigürasyonu

```yaml
# jaeger-config.yml
service:
  extensions: [health_check, pprometheus]
  pipelines:
    traces:
      receivers: [otlp]
      processors: [batch]
      exporters: [jaeger_storage_elasticsearch]

extensions:
  health_check:
  prometheus:
    endpoint: :8888

receivers:
  otlp:
    protocols:
      grpc:
        endpoint: :4317
      http:
        endpoint: :4318

processors:
  batch:
    timeout: 10s
    send_batch_size: 1024

exporters:
  jaeger_storage_elasticsearch:
    server_urls: ["http://elasticsearch:9200"]

sampling:
  strategies:
    - name: "default"
      type: probabilistic
      param: 0.1  # %10 sampling
    - name: "high-throughput"
      type: rate-limiting
      param: 100  # 100 traces/s
```

## Trace Analizi

### Python Trace Query

```python
import requests
from datetime import datetime, timedelta

class JaegerQuery:
    def __init__(self, jaeger_url: str):
        self.base_url = jaeger_url
    
    def search_traces(
        self,
        service: str,
        operation: str = None,
        start_time: datetime = None,
        end_time: datetime = None,
        min_duration: str = None,
        tags: dict = None
    ):
        if start_time is None:
            start_time = datetime.now() - timedelta(hours=1)
        if end_time is None:
            end_time = datetime.now()
        
        params = {
            "service": service,
            "start": int(start_time.timestamp() * 1_000_000),
            "end": int(end_time.timestamp() * 1_000_000)
        }
        
        if operation:
            params["operation"] = operation
        if min_duration:
            params["minDuration"] = min_duration
        if tags:
            params["tags"] = str(tags)
        
        response = requests.get(f"{self.base_url}/api/traces", params=params)
        return response.json()
    
    def get_trace(self, trace_id: str):
        response = requests.get(f"{self.base_url}/api/traces/{trace_id}")
        return response.json()
    
    def get_slow_traces(self, service: str, threshold_ms: int = 1000):
        return self.search_traces(
            service=service,
            min_duration=f"{threshold_ms}ms"
        )

# Kullanım
jaeger = JaegerQuery("http://jaeger:16686")
slow_traces = jaeger.get_slow_traces("coremusic-api", threshold_ms=500)
```

## Bağımlılıklar

| Bileşen | Versiyon | Amaç |
|---------|----------|------|
| opentelemetry-api | 1.20+ | Tracing API |
| opentelemetry-sdk | 1.20+ | Tracing SDK |
| opentelemetry-exporter-otlp | 1.20+ | OTLP export |
| jaeger | 1.50+ | Trace storage ve UI |
| elasticsearch | 8.0+ | Trace saklama |

## Durum: Implementasyon

**Aşama**: Hazırlık
**Öncelik**: Orta
**Tahmini Süre**: 1 hafta
**Notlar**: Tüm servislerde OpenTelemetry enstrümantasyonu yapılmalıdır.
