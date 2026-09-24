---
title: "Audit Logs"
layer: K12
category: "İzleme"
date: 2026-09-20
---

# Audit Logs

## Genel Bakış

Audit logs, COREMUSIC platformunda gerçekleştirilen tüm önemli işlemlerin denetim trail'ini oluşturur. Uyumluluk gereksinimleri (GDPR, KVKK, SOC2) için gerekli olan bu loglar, kimin ne zaman ne yaptığını kayıt altına alır. Audit trail'leri değiştirilemez ve uzun süre saklanır.

## Audit Event Türleri

### Kullanıcı Olayları

| Event | Açıklama | Kritiklik |
|-------|----------|-----------|
| user.login | Başarılı giriş | INFO |
| user.login_failed | Başarısız giriş | WARNING |
| user.logout | Çıkış | INFO |
| user.password_change | Şifre değişikliği | INFO |
| user.password_reset | Şifre sıfırlama | WARNING |
| user.profile_update | Profil güncelleme | INFO |
| user.account_delete | Hesap silme | CRITICAL |
| user.role_change | Rol değişikliği | WARNING |

### İçerik Olayları

| Event | Açıklama | Kritiklik |
|-------|----------|-----------|
| content.upload | İçerik yükleme | INFO |
| content.delete | İçerik silme | WARNING |
| content.publish | İçerik yayınlama | INFO |
| content.update | İçerik güncelleme | INFO |
| content.moderate | İçerik moderasyonu | INFO |
| content copyright claim | Telif iddiası | WARNING |

### Sistem Olayları

| Event | Açıklama | Kritiklik |
|-------|----------|-----------|
| system.config_change | Konfigürasyon değişikliği | WARNING |
| system.backup | Yedekleme | INFO |
| system.restore | Geri yükleme | WARNING |
| system.migration | Veritabanı migrasyonu | INFO |
| system.deployment | Deployment | INFO |

## Audit Log Yapısı

### Standart Audit Entry

```json
{
  "audit_id": "aud_1234567890",
  "timestamp": "2026-09-20T15:30:00.000Z",
  "event_type": "user.login",
  "severity": "INFO",
  "actor": {
    "user_id": "user_xyz789",
    "username": "bayram.ali",
    "email": "bayram@example.com",
    "role": "admin",
    "ip_address": "192.168.1.100",
    "user_agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64)"
  },
  "resource": {
    "type": "authentication",
    "id": "session_abc123",
    "name": "User Session"
  },
  "action": {
    "type": "login",
    "result": "success",
    "method": "password"
  },
  "context": {
    "request_id": "req_def456",
    "service": "auth-service",
    "location": {
      "country": "TR",
      "city": "Istanbul"
    }
  },
  "metadata": {
    "browser": "Chrome",
    "os": "Windows 10",
    "device": "Desktop"
  }
}
```

### Hata Audit Entry

```json
{
  "audit_id": "aud_0987654321",
  "timestamp": "2026-09-20T15:35:00.000Z",
  "event_type": "user.login_failed",
  "severity": "WARNING",
  "actor": {
    "user_id": "unknown",
    "username": "admin",
    "email": null,
    "role": null,
    "ip_address": "10.0.0.50",
    "user_agent": "curl/7.68.0"
  },
  "resource": {
    "type": "authentication",
    "id": null,
    "name": "Login Attempt"
  },
  "action": {
    "type": "login",
    "result": "failure",
    "reason": "invalid_password",
    "attempt_count": 3
  },
  "context": {
    "request_id": "req_xyz789",
    "service": "auth-service",
    "location": {
      "country": "US",
      "city": "New York"
    }
  },
  "security": {
    "brute_force_detected": true,
    "rate_limited": true
  }
}
```

## Audit Logger Implementasyonu

### Python Audit Logger

```python
import json
import uuid
from datetime import datetime
from typing import Any, Dict, Optional
from enum import Enum

class AuditSeverity(Enum):
    DEBUG = "DEBUG"
    INFO = "INFO"
    WARNING = "WARNING"
    ERROR = "ERROR"
    CRITICAL = "CRITICAL"

class AuditEventType(Enum):
    USER_LOGIN = "user.login"
    USER_LOGIN_FAILED = "user.login_failed"
    USER_LOGOUT = "user.logout"
    USER_PASSWORD_CHANGE = "user.password_change"
    CONTENT_UPLOAD = "content.upload"
    CONTENT_DELETE = "content.delete"
    SYSTEM_CONFIG_CHANGE = "system.config_change"
    DATA_EXPORT = "data.export"

class AuditLogger:
    def __init__(self):
        self.logger = logging.getLogger("audit")
    
    def log_event(
        self,
        event_type: AuditEventType,
        severity: AuditSeverity,
        actor: Dict[str, Any],
        resource: Dict[str, Any],
        action: Dict[str, Any],
        context: Optional[Dict[str, Any]] = None,
        metadata: Optional[Dict[str, Any]] = None
    ) -> str:
        audit_id = f"aud_{uuid.uuid4().hex[:16]}"
        
        entry = {
            "audit_id": audit_id,
            "timestamp": datetime.utcnow().isoformat() + "Z",
            "event_type": event_type.value,
            "severity": severity.value,
            "actor": actor,
            "resource": resource,
            "action": action,
            "context": context or {},
            "metadata": metadata or {}
        }
        
        self.logger.info(json.dumps(entry, ensure_ascii=False))
        return audit_id
    
    def log_user_action(
        self,
        user_id: str,
        action: str,
        resource_type: str,
        resource_id: str,
        result: str,
        ip_address: str
    ) -> str:
        return self.log_event(
            event_type=AuditEventType.USER_LOGIN,
            severity=AuditSeverity.INFO,
            actor={
                "user_id": user_id,
                "ip_address": ip_address
            },
            resource={
                "type": resource_type,
                "id": resource_id
            },
            action={
                "type": action,
                "result": result
            }
        )

# Kullanım
audit = AuditLogger()
audit.log_user_action(
    user_id="user_123",
    action="login",
    resource_type="session",
    resource_id="session_abc",
    result="success",
    ip_address="192.168.1.1"
)
```

### Decorator Pattern

```python
from functools import wraps

def audit_log(event_type: AuditEventType, resource_type: str):
    def decorator(func):
        @wraps(func)
        def wrapper(*args, **kwargs):
            user = get_current_user()
            resource_id = kwargs.get('resource_id', 'unknown')
            
            try:
                result = func(*args, **kwargs)
                audit_logger.log_event(
                    event_type=event_type,
                    severity=AuditSeverity.INFO,
                    actor={
                        "user_id": user.id if user else "system",
                        "ip_address": get_client_ip()
                    },
                    resource={
                        "type": resource_type,
                        "id": resource_id
                    },
                    action={
                        "type": func.__name__,
                        "result": "success"
                    }
                )
                return result
            except Exception as e:
                audit_logger.log_event(
                    event_type=event_type,
                    severity=AuditSeverity.ERROR,
                    actor={
                        "user_id": user.id if user else "system",
                        "ip_address": get_client_ip()
                    },
                    resource={
                        "type": resource_type,
                        "id": resource_id
                    },
                    action={
                        "type": func.__name__,
                        "result": "failure",
                        "error": str(e)
                    }
                )
                raise
        return wrapper
    return decorator

# Kullanım
@audit_log(AuditEventType.CONTENT_UPLOAD, "track")
def upload_track(user_id: str, file_path: str):
    # Yükleme mantığı
    pass
```

## Uyumluluk Gereksinimleri

### GDPR/KVKK Uyumluluğu

```python
class GDPRComplianceLogger:
    # Hassas veri erişimleri
    SENSITIVE_EVENTS = [
        "data.export",
        "data.delete",
        "data.access",
        "consent.grant",
        "consent.revoke"
    ]
    
    # Saklama süreleri
    RETENTION_PERIODS = {
        "user.login": "1 yıl",
        "user.logout": "6 ay",
        "data.export": "3 yıl",
        "data.delete": "3 yıl",
        "system.config_change": "5 yıl"
    }
    
    def log_with_compliance(
        self,
        event_type: str,
        user_id: str,
        data_categories: list,
        legal_basis: str
    ):
        entry = {
            "event_type": event_type,
            "user_id": user_id,
            "data_categories": data_categories,
            "legal_basis": legal_basis,
            "retention_period": self.RETENTION_PERIODS.get(event_type, "1 yıl"),
            "compliance": {
                "gdpr": True,
                "kvkk": True,
                "soc2": True
            }
        }
        self.log(entry)
```

## Log Saklama ve Arşivleme

### Saklama Politikası

| Veri Türü | Aktif | Arşiv | Toplam |
|-----------|-------|-------|--------|
| Kullanıcı olayları | 90 gün | 2 yıl | 2 yıl + 90 gün |
| Finansal olaylar | 1 yıl | 7 yıl | 7 yıl + 1 yıl |
| Sistem olayları | 30 gün | 1 yıl | 1 yıl + 30 gün |
| Güvenlik olayları | 1 yıl | 5 yıl | 5 yıl + 1 yıl |

### Arşivleme Scripti

```python
import gzip
import shutil
from datetime import datetime, timedelta

class AuditArchiver:
    def __init__(self, base_path: str):
        self.base_path = base_path
    
    def archive_old_logs(self, retention_days: int):
        cutoff_date = datetime.now() - timedelta(days=retention_days)
        log_dir = f"{self.base_path}/audit"
        
        for filename in os.listdir(log_dir):
            if filename.endswith('.json'):
                file_date = self._parse_date_from_filename(filename)
                if file_date < cutoff_date:
                    self._compress_and_move(filename, log_dir)
    
    def _compress_and_move(self, filename: str, source_dir: str):
        source = os.path.join(source_dir, filename)
        archive_dir = f"{self.base_path}/archive/audit"
        os.makedirs(archive_dir, exist_ok=True)
        
        dest = os.path.join(archive_dir, f"{filename}.gz")
        
        with open(source, 'rb') as f_in:
            with gzip.open(dest, 'wb') as f_out:
                shutil.copyfileobj(f_in, f_out)
        
        os.remove(source)
```

## Bağımlılıklar

| Bileşen | Versiyon | Amaç |
|---------|----------|------|
| python-json-logger | 2.0+ | JSON format |
| cryptography | 41.0+ | Log imzalama |
| elasticsearch | 8.0+ | Log saklama |

## Durum: Implementasyon

**Aşama**: Hazırlık
**Öncelik**: Yüksek
**Tahmini Süre**: 5 gün
**Notlar**: Audit loglar değiştirilemez olmalı ve güvenli saklanmalıdır.
