---
title: "Application Logging"
layer: K12
category: "İzleme"
date: 2026-09-20
---

# Application Logging

## Genel Bakış

Uygulama loglama, COREMUSIC platformunda gerçekleşen olayların yapıslandırılmış olarak kaydedilmesini sağlar. Structured logging yaklaşımı ile loglar makine tarafından okunabilir formatta üretilir ve merkezi log yönetim sistemi üzerinden analiz edilir.

## Log Seviyeleri

### Seviye Tanımları

| Seviye | Kullanım | Örnek |
|--------|----------|-------|
| DEBUG | Geliştirme bilgileri | Değişken değerleri, fonksiyon çağrıları |
| INFO | Normal operasyon | Başlatma, durdurma, başarılı işlemler |
| WARNING | Uyarı durumları | Düşük bellek, yavaş sorgu |
| ERROR | Hata durumları | Bağlantı hatası, doğrulama hatası |
| CRITICAL | Kritik hatalar | Sistem çökmesi, veri kaybı |

### Python Logger Yapısı

```python
import logging
import json
from datetime import datetime

class StructuredFormatter(logging.Formatter):
    def format(self, record):
        log_entry = {
            "timestamp": datetime.utcnow().isoformat() + "Z",
            "level": record.levelname,
            "logger": record.name,
            "message": record.getMessage(),
            "module": record.module,
            "function": record.funcName,
            "line": record.lineno,
            "thread": record.thread,
            "thread_name": record.threadName
        }
        
        if hasattr(record, 'extra_data'):
            log_entry["data"] = record.extra_data
        
        if record.exc_info:
            log_entry["exception"] = {
                "type": record.exc_info[0].__name__,
                "message": str(record.exc_info[1]),
                "traceback": self.formatException(record.exc_info)
            }
        
        return json.dumps(log_entry, ensure_ascii=False)

# Logger yapılandırması
logger = logging.getLogger("coremusic")
handler = logging.StreamHandler()
handler.setFormatter(StructuredFormatter())
logger.addHandler(handler)
logger.setLevel(logging.INFO)
```

## Structured Logging Formatı

### Standart Log Entry

```json
{
  "timestamp": "2026-09-20T15:30:00.000Z",
  "level": "INFO",
  "logger": "coremusic.api.tracks",
  "message": "Track retrieved successfully",
  "module": "api.endpoints.tracks",
  "function": "get_track",
  "line": 42,
  "thread": 12345,
  "thread_name": "MainThread",
  "request_id": "req_abc123",
  "user_id": "user_xyz789",
  "data": {
    "track_id": "track_123",
    "duration_ms": 240000,
    "format": "mp3"
  }
}
```

### Hata Log Entry

```json
{
  "timestamp": "2026-09-20T15:30:00.000Z",
  "level": "ERROR",
  "logger": "coremusic.audio.processor",
  "message": "Audio processing failed",
  "module": "audio.processor",
  "function": "process_audio",
  "line": 156,
  "thread": 12345,
  "thread_name": "Worker-1",
  "request_id": "req_def456",
  "exception": {
    "type": "AudioProcessingError",
    "message": "Invalid codec format",
    "traceback": "Traceback (most recent call last):\n  File \"processor.py\", line 154, in process_audio\n    codec = detect_codec(input_file)\nAudioProcessingError: Invalid codec format"
  },
  "context": {
    "file_size_bytes": 10485760,
    "codec": "unknown",
    "sample_rate": 44100
  }
}
```

## Loglama Konfigürasyonu

### Python Logging Config

```python
# logging_config.py
LOGGING_CONFIG = {
    'version': 1,
    'disable_existing_loggers': False,
    'formatters': {
        'structured': {
            '()': 'coremusic.logging.StructuredFormatter'
        },
        'simple': {
            'format': '%(asctime)s - %(name)s - %(levelname)s - %(message)s'
        }
    },
    'handlers': {
        'console': {
            'class': 'logging.StreamHandler',
            'formatter': 'structured',
            'stream': 'ext://sys.stdout'
        },
        'file': {
            'class': 'logging.handlers.RotatingFileHandler',
            'formatter': 'structured',
            'filename': '/var/log/coremusic/app.log',
            'maxBytes': 104857600,  # 100MB
            'backupCount': 10,
            'encoding': 'utf8'
        },
        'json_file': {
            'class': 'logging.handlers.RotatingFileHandler',
            'formatter': 'structured',
            'filename': '/var/log/coremusic/app.json',
            'maxBytes': 104857600,
            'backupCount': 30
        }
    },
    'loggers': {
        'coremusic': {
            'handlers': ['console', 'file', 'json_file'],
            'level': 'INFO',
            'propagate': True
        },
        'coremusic.api': {
            'handlers': ['console', 'json_file'],
            'level': 'INFO'
        },
        'coremusic.audio': {
            'handlers': ['console', 'file'],
            'level': 'DEBUG'
        },
        'coremusic.database': {
            'handlers': ['console', 'json_file'],
            'level': 'WARNING'
        }
    },
    'root': {
        'handlers': ['console'],
        'level': 'WARNING'
    }
}
```

### Django Settings

```python
# settings.py
LOGGING = {
    'version': 1,
    'disable_existing_loggers': False,
    'formatters': {
        'json': {
            '()': 'pythonjsonlogger.jsonlogger.JsonFormatter',
            'format': '%(asctime)s %(name)s %(levelname)s %(message)s'
        }
    },
    'handlers': {
        'console': {
            'class': 'logging.StreamHandler',
            'formatter': 'json'
        },
        'file': {
            'class': 'logging.handlers.RotatingFileHandler',
            'filename': '/var/log/coremusic/django.log',
            'maxBytes': 104857600,
            'backupCount': 5,
            'formatter': 'json'
        }
    },
    'loggers': {
        'django': {
            'handlers': ['file'],
            'level': 'INFO',
            'propagate': True
        },
        'coremusic': {
            'handlers': ['console', 'file'],
            'level': 'DEBUG'
        }
    }
}
```

## Context Propagation

### Request ID Takibi

```python
import uuid
from contextvars import ContextVar

request_id_var: ContextVar[str] = ContextVar('request_id', default='')

class RequestIDMiddleware:
    def __init__(self, app):
        self.app = app
    
    def __call__(self, scope, receive, send):
        if scope['type'] == 'http':
            request_id = scope['headers'].get(
                b'x-request-id', 
                str(uuid.uuid4()).encode()
            ).decode()
            request_id_var.set(request_id)
        
        return self.app(scope, receive, send)

# Loglama ile kullanım
def get_logger(name):
    logger = logging.getLogger(name)
    original_factory = logging.getLogRecordFactory()
    
    def record_factory(*args, **kwargs):
        record = original_factory(*args, **kwargs)
        record.request_id = request_id_var.get('')
        return record
    
    logging.setLogRecordFactory(record_factory)
    return logger
```

### User Context

```python
class UserContextFilter(logging.Filter):
    def filter(self, record):
        user = get_current_user()
        if user:
            record.user_id = user.id
            record.user_role = user.role
            record.user_ip = user.ip_address
        else:
            record.user_id = None
            record.user_role = None
            record.user_ip = None
        return True

# Logger'a ekleme
logger.addFilter(UserContextFilter())
```

## Hassas Veri Maskelenme

### Log Masking

```python
import re
from functools import wraps

class SensitiveDataMasker:
    PATTERNS = {
        'credit_card': r'\b\d{4}[\s-]?\d{4}[\s-]?\d{4}[\s-]?\d{4}\b',
        'email': r'\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b',
        'phone': r'\b\d{3}[\s-]?\d{3}[\s-]?\d{4}\b',
        'ssn': r'\b\d{3}-\d{2}-\d{4}\b',
        'password': r'(password|pwd|passphrase)["\s:=]+\S+',
        'api_key': r'(api[_-]?key|apikey)["\s:=]+\S+',
        'token': r'(token|bearer|auth)["\s:=]+\S+'
    }
    
    @classmethod
    def mask(cls, text: str) -> str:
        masked = text
        for name, pattern in cls.PATTERNS.items():
            masked = re.sub(pattern, f'[REDACTED_{name.upper()}]', masked, flags=re.IGNORECASE)
        return masked

def mask_sensitive_data(func):
    @wraps(func)
    def wrapper(*args, **kwargs):
        try:
            return func(*args, **kwargs)
        finally:
            # Log kayıtlarını maskele
            pass
    return wrapper
```

## Log Rotation ve Saklama

### Logrotate Config

```
# /etc/logrotate.d/coremusic
/var/log/coremusic/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
    postrotate
        invoke-rc.d rsyslog rotate > /dev/null
    endscript
}

/var/log/coremusic/*.json {
    daily
    missingok
    rotate 90
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
}
```

## Bağımlılıklar

| Bileşen | Versiyon | Amaç |
|---------|----------|------|
| python-json-logger | 2.0+ | JSON log formatı |
| structlog | 23.0+ | Structured logging |
| python-logstash | 0.5+ | Logstash entegrasyonu |
| sentry-sdk | 1.30+ | Hata gönderimi |

## Durum: Implementasyon

**Aşama**: Hazırlık
**Öncelik**: Yüksek
**Tahmini Süre**: 3 gün
**Notlar**: Tüm servislerde standart log formatı kullanılmalıdır.
