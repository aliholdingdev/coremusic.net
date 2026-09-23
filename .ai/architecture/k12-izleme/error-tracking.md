---
title: "Error Tracking"
layer: K12
category: "İzleme"
date: 2026-09-20
---

# Error Tracking

## Genel Bakış

Hata takibi, COREMUSIC platformunda oluşan hataların yakalanması, analiz edilmesi ve çözülmesini sağlayan sistemdir. Sentry entegrasyonu ile real-time hata bildirimleri alınır, stack trace'ler analiz edilir ve hata trendleri takip edilir.

## Hata Kategorileri

### Kritik Seviye Hatalar

| Hata Türü | Açıklama | Eylem |
|-----------|----------|-------|
| SystemException | Sistem seviyesi hatalar | Otomatik restart |
| DatabaseConnectionError | Veritabanı bağlantı hatası | Alert + Failover |
| MemoryError | Bellek yetersizliği | Alert + Scale |
| AuthenticationError | Kimlik doğrulama hataları | Rate limiting |

### Uygulama Seviye Hatalar

| Hata Türü | Açıklama | Eylem |
|-----------|----------|-------|
| ValidationError | Doğrulama hataları | Log + Kullanıcıya bildirim |
| NotFoundError | Kayıt bulunamadı | Log |
| PermissionError | Yetki hataları | Log + Security alert |
| BusinessLogicError | İş mantığı hataları | Log + Alert |

## Sentry Entegrasyonu

### Python Kurulumu

```python
import sentry_sdk
from sentry_sdk.integrations.flask import FlaskIntegration
from sentry_sdk.integrations.sqlalchemy import SqlalchemyIntegration
from sentry_sdk.integrations.redis import RedisIntegration

sentry_sdk.init(
    dsn="https://your-dsn@sentry.io/project-id",
    environment="production",
    release="1.0.0",
    traces_sample_rate=0.1,
    profiles_sample_rate=0.1,
    integrations=[
        FlaskIntegration(),
        SqlalchemyIntegration(),
        RedisIntegration()
    ],
    before_send=filter_sensitive_data,
    before_send_transaction=filter_sensitive_data,
    max_breadcrumbs=50,
    attach_stacktrace=True,
    send_default_pii=False
)

def filter_sensitive_data(event, hint):
    # Hassas verileri filtrele
    if 'request' in event:
        if 'headers' in event['request']:
            event['request']['headers'].pop('Authorization', None)
            event['request']['headers'].pop('Cookie', None)
    
    if 'extra' in event:
        event['extra'].pop('password', None)
        event['extra'].pop('token', None)
    
    return event
```

### Flask Entegrasyonu

```python
from flask import Flask, request, g
import uuid

app = Flask(__name__)

@app.before_request
def before_request():
    g.request_id = str(uuid.uuid4())
    sentry_sdk.set_context("request", {
        "id": g.request_id,
        "url": request.url,
        "method": request.method,
        "remote_addr": request.remote_addr
    })

@app.errorhandler(404)
def not_found(error):
    sentry_sdk.capture_message("Page not found", level="warning")
    return {"error": "Not found"}, 404

@app.errorhandler(500)
def internal_error(error):
    sentry_sdk.capture_exception(error)
    return {"error": "Internal server error"}, 500
```

## Hata Yakalama

### Try-Catch Pattern

```python
import sentry_sdk
from typing import Optional

class ErrorHandler:
    @staticmethod
    def handle_exception(
        exception: Exception,
        context: Optional[dict] = None,
        level: str = "error"
    ):
        with sentry_sdk.push_scope() as scope:
            if context:
                for key, value in context.items():
                    scope.set_extra(key, value)
            
            scope.set_level(level)
            sentry_sdk.capture_exception(exception)
    
    @staticmethod
    def handle_message(
        message: str,
        level: str = "info",
        context: Optional[dict] = None
    ):
        with sentry_sdk.push_scope() as scope:
            if context:
                for key, value in context.items():
                    scope.set_extra(key, value)
            
            scope.set_level(level)
            sentry_sdk.capture_message(message, level=level)

# Kullanım
try:
    process_audio(file_path)
except AudioProcessingError as e:
    ErrorHandler.handle_exception(
        e,
        context={
            "file_path": file_path,
            "user_id": user_id
        },
        level="error"
    )
    raise
```

### Decorator Pattern

```python
from functools import wraps

def capture_exception(level: str = "error", context_provider=None):
    def decorator(func):
        @wraps(func)
        def wrapper(*args, **kwargs):
            try:
                return func(*args, **kwargs)
            except Exception as e:
                context = {}
                if context_provider:
                    context = context_provider(*args, **kwargs)
                
                ErrorHandler.handle_exception(
                    e,
                    context=context,
                    level=level
                )
                raise
        return wrapper
    return decorator

# Kullanım
@capture_exception(level="error")
def process_track(track_id: str):
    track = get_track(track_id)
    if not track:
        raise TrackNotFoundError(f"Track {track_id} not found")
    # İşleme mantığı
```

## Breadcrumb Tracking

```python
import sentry_sdk

class BreadcrumbTracker:
    @staticmethod
    def add_breadcrumb(
        category: str,
        message: str,
        level: str = "info",
        data: dict = None
    ):
        sentry_sdk.add_breadcrumb(
            category=category,
            message=message,
            level=level,
            data=data or {}
        )
    
    @staticmethod
    def track_database_query(query: str, duration: float):
        BreadcrumbTracker.add_breadcrumb(
            category="query",
            message=f"Database query executed in {duration:.2f}s",
            level="info",
            data={"query": query[:200], "duration": duration}
        )
    
    @staticmethod
    def track_api_call(method: str, url: str, status_code: int):
        BreadcrumbTracker.add_breadcrumb(
            category="http",
            message=f"{method} {url} -> {status_code}",
            level="info" if status_code < 400 else "warning",
            data={
                "method": method,
                "url": url,
                "status_code": status_code
            }
        )

# Kullanım
BreadcrumbTracker.add_breadcrumb(
    category="auth",
    message="User authenticated successfully",
    data={"user_id": "123"}
)
```

## Hata Alerting

### Sentry Alert Rules

```yaml
# sentry-alerts.yml
alerts:
  - name: "Yüksek Hata Oranı"
    condition:
      type: "event_count"
      interval: "5m"
      threshold: 100
    actions:
      - type: "slack"
        channel: "#alerts-production"
      - type: "email"
        recipients:
          - "devops@coremusic.com"
  
  - name: "Yeni Hata Türü"
    condition:
      type: "new_issue"
    actions:
      - type: "slack"
        channel: "#new-errors"
      - type: "pagerduty"
        severity: "warning"
  
  - name: "Kritik Hata"
    condition:
      type: "event_level"
      level: "fatal"
    actions:
      - type: "pagerduty"
        severity: "critical"
      - type: "slack"
        channel: "#critical-alerts"
```

## Hata Analizi

### Python Hata Analiz Scripti

```python
from sentry_sdk import Hub
from datetime import datetime, timedelta

class ErrorAnalyzer:
    def __init__(self):
        self.client = Hub.current.client
    
    def get_error_trends(self, days: int = 7):
        end_date = datetime.now()
        start_date = end_date - timedelta(days=days)
        
        # Sentry API ile hata trendlerini çek
        issues = self.client.store._store.get_all_issues(
            start=start_date,
            end=end_date
        )
        
        trends = {
            "total_errors": len(issues),
            "by_level": {},
            "by_type": {},
            "top_issues": []
        }
        
        for issue in issues:
            level = issue.level
            trends["by_level"][level] = trends["by_level"].get(level, 0) + 1
            
            error_type = issue.type
            trends["by_type"][error_type] = trends["by_type"].get(error_type, 0) + 1
        
        trends["top_issues"] = sorted(
            issues,
            key=lambda x: x.event_count,
            reverse=True
        )[:10]
        
        return trends
    
    def get_unresolved_issues(self):
        issues = self.client.store._store.get_all_issues(
            status="unresolved"
        )
        return [
            {
                "id": issue.id,
                "title": issue.title,
                "level": issue.level,
                "event_count": issue.event_count,
                "first_seen": issue.first_seen,
                "last_seen": issue.last_seen
            }
            for issue in issues
        ]
```

## Performance Monitoring

### Transaction Tracking

```python
import sentry_sdk

def process_audio_with_tracking(file_path: str):
    with sentry_sdk.start_transaction(
        op="audio.process",
        name="process_audio"
    ) as transaction:
        transaction.set_tag("file_path", file_path)
        
        # Decode aşaması
        with sentry_sdk.start_span(op="decode", name="decode_audio"):
            audio_data = decode_audio(file_path)
            transaction.set_data("duration_ms", audio_data.duration)
        
        # İşleme aşaması
        with sentry_sdk.start_span(op="process", name="apply_effects"):
            processed = apply_effects(audio_data)
        
        # Kaydetme aşaması
        with sentry_sdk.start_span(op="save", name="save_output"):
            save_output(processed)
        
        transaction.set_data("output_format", "wav")
        return processed
```

## Bağımlılıklar

| Bileşen | Versiyon | Amaç |
|---------|----------|------|
| sentry-sdk | 1.30+ | Hata yakalama |
| sentry-sdk[flask] | 1.30+ | Flask entegrasyonu |
| sentry-sdk[sqlalchemy] | 1.30+ | SQLAlchemy entegrasyonu |
| sentry-sdk[redis] | 1.30+ | Redis entegrasyonu |

## Durum: Implementasyon

**Aşama**: Hazırlık
**Öncelik**: Yüksek
**Tahmini Süre**: 3 gün
**Notlar**: Tüm servislerde Sentry SDK entegre edilmelidir.
