---
title: "Güvenlik Denetim Kayıtları"
layer: K6
category: "Güvenlik"
date: 2026-09-20
---

# Güvenlik Denetim Kayıtları

## Genel Bakış

Güvenlik denetim kayıtları (audit logging), COREMUSIC platformunda gerçekleşen tüm güvenlik olaylarını izlenebilir, değiştirilemez ve uyumlu şekilde kaydeder. Tamper-proof loglama, merkezi toplama ve real-time alerting ile siber güvenlik olaylarına hızlı müdahale sağlanır.

## Teknik Detaylar

### Log Kategorileri

```
┌─────────────────────────────────────────────────────────┐
│                   AUDIT LOG KATEGORİLERİ                │
├─────────────────┬───────────────────────────────────────┤
│ Authentication  │ Login, logout, başarısızlık, token refresh    │
│ Authorization   │ Permission grant, deny, role change   │
│ Data Access     │ CRUD operations on sensitive data     │
│ System Config   │ Config changes, secret rotation       │
│ Security Events │ Intrusion attempts, rate limits       │
│ Admin Actions   │ User management, policy changes       │
│ API Usage       │ Endpoint calls, response times        │
│ Compliance      │ GDPR, data export, consent            │
└─────────────────┴───────────────────────────────────────┘
```

### Log Formatı (JSON)

```json
{
  "eventId": "uuid-v4",
  "timestamp": "2026-09-20T15:30:00.000Z",
  "level": "INFO",
  "category": "AUTHENTICATION",
  "action": "LOGIN_SUCCESS",
  "actor": {
    "userId": "user_123",
    "ip": "192.168.1.100",
    "userAgent": "Mozilla/5.0...",
    "sessionId": "sess_abc"
  },
  "resource": {
    "type": "auth",
    "id": "login"
  },
  "outcome": "SUCCESS",
  "metadata": {
    "method": "password",
    "mfa": true
  },
  "integrity": {
    "hash": "sha256:...",
    "previousHash": "sha256:..."
  }
}
```

### Tamper-Proof Mekanizması

Her log entry bir öncekinin hash'ini içerir (blockchain benzeri zincir). Bu sayede logların manipüle edilmesi tespit edilebilir.

### Log Saklama Politikası

| Log Tipi | Saklama Süresi | Sıkıştırma | Arşiv |
|----------|---------------|------------|-------|
| Security Events | 2 yıl | gzip | Cold Storage |
| Authentication | 1 yıl | gzip | Cold Storage |
| Data Access | 6 ay | gzip | Cold Storage |
| API Usage | 3 ay | gzip | Deleted |
| Debug Logs | 30 gün | - | Deleted |

## Konfigürasyon / Kod

```typescript
import { v4 as uuidv4 } from 'uuid';
import crypto from 'crypto';

// Audit Event Yapısı
interface AuditEvent {
  eventId: string;
  timestamp: string;
  level: 'INFO' | 'WARN' | 'ERROR' | 'CRITICAL';
  category: AuditCategory;
  action: string;
  actor: ActorInfo;
  resource: ResourceInfo;
  outcome: 'SUCCESS' | 'FAILURE' | 'PARTIAL';
  metadata: Record<string, any>;
  integrity: IntegrityInfo;
}

type AuditCategory =
  | 'AUTHENTICATION'
  | 'AUTHORIZATION'
  | 'DATA_ACCESS'
  | 'SYSTEM_CONFIG'
  | 'SECURITY_EVENT'
  | 'ADMIN_ACTION'
  | 'API_USAGE'
  | 'COMPLIANCE';

interface ActorInfo {
  userId?: string;
  ip: string;
  userAgent: string;
  sessionId?: string;
  apiKeyId?: string;
}

interface ResourceInfo {
  type: string;
  id?: string;
  name?: string;
}

interface IntegrityInfo {
  hash: string;
  previousHash: string;
  chainIndex: number;
}

// Integrity Chain
class IntegrityChain {
  private lastHash: string = '0'.repeat(64);
  private chainIndex: number = 0;

  async computeHash(event: Omit<AuditEvent, 'integrity'>): Promise<string> {
    const data = JSON.stringify(event) + this.lastHash;
    return crypto.createHash('sha256').update(data).digest('hex');
  }

  async createIntegrityInfo(
    event: Omit<AuditEvent, 'integrity'>
  ): Promise<IntegrityInfo> {
    const hash = await this.computeHash(event);
    const previousHash = this.lastHash;

    this.lastHash = hash;
    this.chainIndex++;

    return { hash, previousHash, chainIndex: this.chainIndex };
  }

  async verifyChain(events: AuditEvent[]): Promise<boolean> {
    let previousHash = '0'.repeat(64);

    for (const event of events) {
      if (event.integrity.previousHash !== previousHash) {
        return false;
      }

      const expectedHash = await this.computeHash({
        eventId: event.eventId,
        timestamp: event.timestamp,
        level: event.level,
        category: event.category,
        action: event.action,
        actor: event.actor,
        resource: event.resource,
        outcome: event.outcome,
        metadata: event.metadata,
      });

      if (event.integrity.hash !== expectedHash) {
        return false;
      }

      previousHash = event.integrity.hash;
    }

    return true;
  }
}

// Audit Logger
class AuditLogger {
  private chain: IntegrityChain;
  private buffer: AuditEvent[] = [];
  private flushInterval: NodeJS.Timeout;

  constructor() {
    this.chain = new IntegrityChain();
    this.flushInterval = setInterval(() => this.flush(), 5000);
  }

  async log(params: {
    level: AuditEvent['level'];
    category: AuditCategory;
    action: string;
    actor: ActorInfo;
    resource: ResourceInfo;
    outcome: AuditEvent['outcome'];
    metadata?: Record<string, any>;
  }): Promise<AuditEvent> {
    const event: Omit<AuditEvent, 'integrity'> = {
      eventId: uuidv4(),
      timestamp: new Date().toISOString(),
      level: params.level,
      category: params.category,
      action: params.action,
      actor: params.actor,
      resource: params.resource,
      outcome: params.outcome,
      metadata: params.metadata || {},
    };

    const integrity = await this.chain.createIntegrityInfo(event);
    const fullEvent: AuditEvent = { ...event, integrity };

    this.buffer.push(fullEvent);

    // Kritik olayları hemen yaz
    if (params.level === 'CRITICAL' || params.level === 'ERROR') {
      await this.flush();
    }

    return fullEvent;
  }

  async flush(): Promise<void> {
    if (this.buffer.length === 0) return;

    const events = [...this.buffer];
    this.buffer = [];

    // Postgres'e yaz
    await db.auditLogs.createMany({
      data: events.map(e => ({
        eventId: e.eventId,
        timestamp: e.timestamp,
        level: e.level,
        category: e.category,
        action: e.action,
        actorIp: e.actor.ip,
        actorUserId: e.actor.userId,
        resourceType: e.resource.type,
        resourceId: e.resource.id,
        outcome: e.outcome,
        metadata: e.metadata,
        hash: e.integrity.hash,
        previousHash: e.integrity.previousHash,
        chainIndex: e.integrity.chainIndex,
      })),
    });

    // Elasticsearch'e yaz (arama için)
    await this.indexToElasticsearch(events);

    // Kritik olaylar için alert
    for (const event of events) {
      if (event.level === 'CRITICAL') {
        await this.sendAlert(event);
      }
    }
  }

  private async indexToElasticsearch(events: AuditEvent[]): Promise<void> {
    // Bulk index
    const body = events.flatMap(event => [
      { index: { _index: 'audit-logs', _id: event.eventId } },
      event,
    ]);

    await esClient.bulk({ body });
  }

  private async sendAlert(event: AuditEvent): Promise<void> {
    await notificationService.send({
      to: 'security-team@coremusic.com',
      subject: `[SECURITY] ${event.action}`,
      body: `
        Event: ${event.action}
        Level: ${event.level}
        Actor: ${event.actor.userId || event.actor.ip}
        Resource: ${event.resource.type}/${event.resource.id}
        Time: ${event.timestamp}
      `,
    });
  }
}

// Query Fonksiyonları
async function queryAuditLogs(filters: {
  startDate?: Date;
  endDate?: Date;
  category?: AuditCategory;
  level?: string;
  userId?: string;
  ip?: string;
  action?: string;
  limit?: number;
  offset?: number;
}): Promise<{
  events: AuditEvent[];
  total: number;
}> {
  const where: any = {};

  if (filters.startDate || filters.endDate) {
    where.timestamp = {};
    if (filters.startDate) where.timestamp.gte = filters.startDate;
    if (filters.endDate) where.timestamp.lte = filters.endDate;
  }

  if (filters.category) where.category = filters.category;
  if (filters.level) where.level = filters.level;
  if (filters.userId) where.actorUserId = filters.userId;
  if (filters.ip) where.actorIp = filters.ip;
  if (filters.action) where.action = { contains: filters.action };

  const [events, total] = await Promise.all([
    db.auditLogs.findMany({
      where,
      orderBy: { timestamp: 'desc' },
      take: filters.limit || 100,
      skip: filters.offset || 0,
    }),
    db.auditLogs.count({ where }),
  ]);

  return { events, total };
}

// Log Integrity Verification
async function verifyLogIntegrity(
  startDate: Date,
  endDate: Date
): Promise<{
  valid: boolean;
  tamperedEvents: string[];
}> {
  const events = await db.auditLogs.findMany({
    where: {
      timestamp: { gte: startDate, lte: endDate },
    },
    orderBy: { chainIndex: 'asc' },
  });

  const chain = new IntegrityChain();
  const tamperedEvents: string[] = [];

  for (const event of events) {
    const expectedHash = await chain.computeHash({
      eventId: event.eventId,
      timestamp: event.timestamp,
      level: event.level,
      category: event.category,
      action: event.action,
      actor: { ip: event.actorIp, userId: event.actorUserId },
      resource: { type: event.resourceType, id: event.resourceId },
      outcome: event.outcome,
      metadata: event.metadata,
    });

    if (event.hash !== expectedHash) {
      tamperedEvents.push(event.eventId);
    }
  }

  return {
    valid: tamperedEvents.length === 0,
    tamperedEvents,
  };
}

// Compliance Report
async function generateComplianceReport(
  startDate: Date,
  endDate: Date
): Promise<{
  totalEvents: number;
  securityEvents: number;
  failedLogins: number;
  unauthorizedAccess: number;
  adminActions: number;
  dataExports: number;
}> {
  const [total, security, failed, unauthorized, admin, exports] =
    await Promise.all([
      db.auditLogs.count({
        where: { timestamp: { gte: startDate, lte: endDate } },
      }),
      db.auditLogs.count({
        where: {
          timestamp: { gte: startDate, lte: endDate },
          category: 'SECURITY_EVENT',
        },
      }),
      db.auditLogs.count({
        where: {
          timestamp: { gte: startDate, lte: endDate },
          action: 'LOGIN_FAILED',
        },
      }),
      db.auditLogs.count({
        where: {
          timestamp: { gte: startDate, lte: endDate },
          outcome: 'FAILURE',
          category: 'AUTHORIZATION',
        },
      }),
      db.auditLogs.count({
        where: {
          timestamp: { gte: startDate, lte: endDate },
          category: 'ADMIN_ACTION',
        },
      }),
      db.auditLogs.count({
        where: {
          timestamp: { gte: startDate, lte: endDate },
          action: 'DATA_EXPORT',
        },
      }),
    ]);

  return {
    totalEvents: total,
    securityEvents: security,
    failedLogins: failed,
    unauthorizedAccess: unauthorized,
    adminActions: admin,
    dataExports: exports,
  };
}
```

## Güvenlik Kontrolleri

- [ ] Tüm security events loglanmalı
- [ ] Log'lar değiştirilemez (append-only)
- [ ] Integrity chain aktif olmalı
- [ ] Kritik olaylar real-time alert tetiklemeli
- [ ] Log saklama politikası uygulanmalı
- [ ] Log erişimi sadece security team
- [ ] Compliance raporları periyodik üretilmeli
- [ ] Log'lar encrypt olarak saklanmalı
- [ ] Centralized logging (ELK/Splunk) entegre olmalı
- [ ] Log integrity periyodik doğrulanmalı

## Bağımlılıklar

- **PostgreSQL**: Log depolama
- **Elasticsearch**: Log indeksleme ve arama
- **Redis**: Real-time alerting
- **Vault-secrets.md**: Log encryption key yönetimi

## Durum: Implementasyon

- [x] Log formatı ve kategorileri tanımlandı
- [x] Integrity chain mekanizması tasarlandı
- [ ] Audit logger implemente edilecek
- [ ] Integrity chain kurulacak
- [ ] Elasticsearch entegrasyonu yapılacak
- [ ] Alerting sistemi aktifleştirilecek
- [ ] Compliance report fonksiyonları yazılacak
- [ ] Log retention policy otomatikleştirilecek
