---
type: adr
category: database
title: "ADR-050: Multi-DB Sync Strategy"
date: 2026-08-03
updated: 2026-08-08
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-050: Multi-DB Sync Strategy

**Status:** Active (güncellenebilir)
**Kategorisi:** Database
**İlgili Agent:** [[.agents/data-engineer]]
**İlgili Division:** Data Engineering

---

## 1. Amaç

Bu ADR, CoreMusic platformundaki 9 BCNF veritabanı arası senkronizasyon stratejisini, event-driven mimariyi, async iletişim mekanizmasını ve veri tutarlılığını tanımlar.

CoreMusic'in multi-DB sync hedefi:
- Event-driven senkronizasyon: Olay bazlı veri aktarımı
- Async iletişim: Asenkron mesajlaşma
- Retry mekanizması: Başarısız işlemler için yeniden deneme
- Dead letter queue: Kalıcı başarısız mesajlar
- Veri tutarlılığı: 9 DB arası tutarlılık

---

## 2. Bağlam

### 2.1 Mevcut Durum

CoreMusic, 9 izole BCNF veritabanı kullanır (ADR-040):

| # | Veritabanı | Amaç | DB |
|---|------------|------|----|
| 1 | coremusic_auth | Users, roles, sessions | MySQL |
| 2 | coremusic_user | Profiles, preferences | MySQL |
| 3 | coremusic_musics | Songs, artists, genres | MySQL |
| 4 | coremusic_albums | Album collections | MySQL |
| 5 | coremusic_playlist | User and AI playlists | MySQL |
| 6 | coremusic_catalog | Download queues, service status | MySQL |
| 7 | coremusic_logs | Application logs, audit trail | MySQL |
| 8 | coremusic_media | Media file metadata | MySQL |
| 9 | coremusic_system | System configuration | MySQL |

### 2.2 Problem

- Servisler arası veri aktarımı gerekli (örn: Download Service → Music DB)
- Cross-DB join yasak (ADR-003)
- Manuel senkronizasyon hata riski
- Yüksek yük altında performans sorunu

### 2.3 Gereksinimler

| # | Gereksinim | Değer | Kaynak |
|---|------------|-------|--------|
| R1 | Event-driven | Olay bazlı senkronizasyon | ADR-050 |
| R2 | Async | Asenkron iletişim | ADR-050 |
| R3 | Retry | Yeniden deneme mekanizması | ADR-050 |
| R4 | Dead letter | Başarısız mesajlar | ADR-050 |
| R5 | Tutarlılık | 9 DB arası tutarlılık | ADR-040 |
| R6 | Performans | < 100ms sync | ADR-050 |
| R7 | Logging | Tüm sync loglanır | ADR-004 |
| R8 | Rollback | Geri alma mekanizması | ADR-050 |

### 2.4 Kısıtlamalar

| # | Kısıt | Açıklama |
|---|-------|----------|
| C1 | Cross-DB join yasak | Her DB izole |
| C2 | ORM yasak | Sadece PDO prepared |
| C3 | Performans | 100ms altında |
| C4 | Güvenlik | Hassas veri korunmalı |
| C5 | Dayanıklılık | Mesaj kaybı yok |

---

## 3. Karar

CoreMusic'te **multi-db sync** stratejisi kullanılacak.

### 3.1 Event-Driven Mimarisi

```
┌─────────────────────────────────────────────────┐
│ Event Producer (Servis)                          │
│  └→ Download Service: "download.completed"       │
│  └→ Music Service: "song.added"                  │
└──────────────────────┬──────────────────────────┘
                       ↓
┌──────────────────────────────────────────────────┐
│ Event Queue (APCu / Redis)                       │
│  └→ Mesaj kuyruğu                                │
│  └→ Öncelik sırası                               │
└──────────────────────┬───────────────────────────┘
                       ↓
┌──────────────────────────────────────────────────┐
│ Event Consumer (Servis)                          │
│  └→ Music DB: song metadata güncelle             │
│  └→ Media DB: file metadata güncelle             │
└──────────────────────────────────────────────────┘
```

### 3.2 Event Tipleri

| # | Event | Producer | Consumer | Amaç |
|---|-------|----------|----------|------|
| 1 | download.completed | Download Service | Music, Media | İndirme tamamlandı |
| 2 | song.added | Music Service | Playlist, Catalog | Şarkı eklendi |
| 3 | user.registered | Auth Service | User, Preferences | Kullanıcı kaydı |
| 4 | playlist.updated | Playlist Service | Music | Çalma listesi güncellendi |
| 5 | media.processed | Media Service | Music, Catalog | Medya işlendi |
| 6 | config.changed | System Service | Tüm servisler | Konfigürasyon değişikliği |
| 7 | log.created | Log Service | Logs | Log kaydı |
| 8 | album.updated | Music Service | Albums | Albüm güncellendi |

### 3.3 Async İletişim Stratejisi

| Katman | Teknoloji | Kullanım |
|--------|-----------|----------|
| Primary | APCu Queue | Hafif, hızlı |
| Secondary | Redis Queue | Dayanıklı, yüksek yük |
| Fallback | DB Queue | En dayanıklı |

### 3.4 Retry Mekanizması

| Deneme | Gecikme | Strateji |
|--------|---------|----------|
| 1. deneme | Anlık | Doğrudan |
| 2. deneme | 1sn | Exponential backoff |
| 3. deneme | 4sn | Exponential backoff |
| 4. deneme | 16sn | Exponential backoff |
| 5. deneme | 64sn | Dead letter queue |

### 3.5 Dead Letter Queue

Başarısız 5 deneme sonrası:
1. Mesaj dead letter queue'ya taşınır
2. Admin'e bildirim gönderilir
3. Manuel inceleme gerekir
4. Düzeltildikten sonra retry

---

## 4. Teknik Detaylar

### 4.1 Event Producer

```php
<?php
declare(strict_types=1);

class EventProducer
{
    private \PDO $queueDb;

    public function publish(string $eventType, array $data): bool
    {
        $stmt = $this->queueDb->prepare(
            'INSERT INTO event_queue (event_type, payload, status, created_at)
             VALUES (?, ?, ?, NOW())'
        );

        return $stmt->execute([
            $eventType,
            json_encode($data),
            'pending'
        ]);
    }
}
```

### 4.2 Event Consumer

```php
<?php
declare(strict_types=1);

class EventConsumer
{
    private \PDO $queueDb;
    private array $handlers = [];

    public function consume(): void
    {
        $stmt = $this->queueDb->query(
            'SELECT id, event_type, payload FROM event_queue
             WHERE status = "pending" ORDER BY created_at ASC LIMIT 10'
        );

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $this->process($row);
        }
    }

    private function process(array $event): void
    {
        try {
            $handler = $this->handlers[$event['event_type']] ?? null;
            if (!$handler) {
                throw new \RuntimeException("No handler for: {$event['event_type']}");
            }

            $payload = json_decode($event['payload'], true);
            $handler($payload);

            $this->markComplete($event['id']);
        } catch (\Exception $e) {
            $this->retry($event['id'], $e->getMessage());
        }
    }
}
```

### 4.3 Event Queue Tablosu

```sql
CREATE TABLE event_queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_type VARCHAR(100) NOT NULL,
    payload JSON NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'failed', 'dead') DEFAULT 'pending',
    retry_count INT DEFAULT 0,
    max_retries INT DEFAULT 5,
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL,
    INDEX idx_status_created (status, created_at),
    INDEX idx_event_type (event_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 4.4 Dead Letter Tablosu

```sql
CREATE TABLE dead_letter_queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    original_event_id INT NOT NULL,
    event_type VARCHAR(100) NOT NULL,
    payload JSON NOT NULL,
    error_message TEXT,
    retry_count INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    reviewed_by INT,
    INDEX idx_event_type (event_type),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 4.5 Veri Tutarlılığı Kontrolü

| Kontrol | Yöntem | Siklik |
|---------|--------|--------|
| Event count | DB'ler arası sayım | Her saat |
| Hash comparison | Veri hash karşılaştırması | Her gün |
| Manual audit | Manuel denetim | Haftalık |
| Automated repair | Otomatik düzeltme | Tespit edildiğinde |

### 4.6 Performance Metrikleri

| Metrik | Hedef | Ölçüm |
|--------|-------|-------|
| Event publish | < 10ms | DB write |
| Event consume | < 50ms | DB read + process |
| Total sync | < 100ms | Publish → Complete |
| Queue boyutu | < 1000 | Pending events |
| Dead letter | < 10/ay | Failed events |

---

## 5. Yasak Örüntüler

| # | Yasak | Doğru | Kaynak |
|---|-------|-------|--------|
| 1 | Cross-DB join | API üzerinden iletişim | ADR-003 |
| 2 | ORM kullanımı | Raw PDO prepared | ADR-002 |
| 3 | Senkron uzun işlem | Async processing | ADR-050 |
| 4 | Hardcoded secret | .env / credential vault | ADR-034 |
| 5 | Mesaj kaybı | Dayanıklı kuyruk | ADR-050 |
| 6 | Blocking queue | Non-blocking consume | ADR-050 |
| 7 | Log'da hassas veri | [REDACTED] | ADR-022 |
| 8 | Retry sonsuz | Max 5 deneme | ADR-050 |

---

## 6. Edge Cases

| # | Edge Case | Tetikleyici | Çözüm | ADR |
|---|-----------|-------------|-------|-----|
| 1 | Queue dolu | Yüksek yük | Queue partitioning | ADR-050 |
| 2 | Consumer crash | Servis çökmesi | Requeue + retry | ADR-050 |
| 3 | Poison message | Sonsuz retry | Dead letter (5 deneme) | ADR-050 |
| 4 | Duplicate event | Network hatası | Idempotency key | ADR-050 |
| 5 | Out-of-order | Eşzamanlı publish | Sequence number | ADR-050 |
| 6 | DB down | Veritabanı çökmesi | Fallback queue | ADR-050 |
| 7 | Memory pressure | Büyük payload | Chunked transfer | ADR-050 |
| 8 | Network partition | Ağ bölünmesi | Local queue + reconnect | ADR-050 |
| 9 | Schema change | Tablo değişikliği | Migration öncesi drain | ADR-014 |
| 10 | Data conflict | Çakışmalı write | Last-write-wins + log | ADR-050 |

---

## 7. Hard Guardrails

| # | Guardrail | Uygulama | İhlal Sonucu |
|---|-----------|----------|-------------|
| G1 | Cross-DB join yasak | Her DB izole | Veri bütünlüğü |
| G2 | Event-driven | Senkron RPC yasak | Performans |
| G3 | Max 5 retry | Sonsuz döngü yok | Kaynak israfı |
| G4 | Dead letter zorunlu | Başarısız mesajlar | Veri kaybı |
| G5 | Idempotency | Duplicate processing yok | Veri tutarsızlığı |
| G6 | Logging | Tüm sync loglanır | İzlenebilirlik |
| G7 | Rollback | Geri alma mekanizması | Veri kaybı |
| G8 | Performans < 100ms | Sync süresi | Gecikme |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-002-pdo-mandatory-no-orm]] | PDO mandatory | DB erişimi |
| [[ADR-003-multi-db-9-databases]] | 9 BCNF DB | DB yapısı |
| [[ADR-014-multi-db-migration-strategy]] | Multi-DB migration | Schema değişikliği |
| [[ADR-022-database-hardened-security]] | DB güvenlik | Encryption |
| [[ADR-033-sql-normalization-strategy]] | SQL normalization | Veri yapısı |
| [[ADR-040-database-authority]] | 9 BCNF DB otoritesi | DB otoritesi |
| [[ADR-041-database-normalization-supplementary]] | DB normalization ek | Normalizasyon |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef Dosya | İlişki |
|-------|-------------|--------|
| § 2.1 | [[architecture/05-data/database_master]] | 9 BCNF şemaları |
| § 3.1 | [[ecosystem/service-communication]] | Servis iletişim |
| § 3.2 | [[brain.md]] §11 | DB detayları |
| § 4.3 | [[.sql/coremusic_download.sql]] | Download DB şeması |
| § 4.4 | [[ADR-014-multi-db-migration-strategy]] | Migration |
| § 5 | [[ADR-002-pdo-mandatory-no-orm]] | PDO kuralları |
| § 6 | [[ecosystem/error-recovery]] | Hata kurtarma |
| § 7 | [[ADR-040-database-authority]] | DB otoritesi |

---

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **Event-Driven** | Olay tabanlı mimari |
| **Event Queue** | Olay kuyruğu |
| **Event Producer** | Olay üreteci |
| **Event Consumer** | Olay tüketici |
| **Dead Letter Queue** | Başarısız mesaj kuyruğu |
| **Retry** | Yeniden deneme |
| **Exponential Backoff** | Üstel gecikme stratejisi |
| **Idempotency** | Aynı işlemin tekrar uygulanabilirliği |
| **Async** | Asenkron iletişim |
| **Sync** | Senkron iletişim |
| **APCu** | APC User Cache — PHP önbellek |
| **Redis** | İn-memory veritabanı |
| **Payload** | Mesaj içeriği |
| **Sequence Number** | Sıra numarası |
| **Partitioning** | Bölümleme (yük dağıtımı) |
| **Rollback** | Geri alma |
| **ACID** | Atomicity, Consistency, Isolation, Durability |
| **BCNF** | Boyce-Codd Normal Form |
| **Schema** | Veritabanı şeması |
| **Migration** | Veritabanı değişikliği |

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| ADR Version | 2.0.0 |
| Status | Active · Red Team · Human Mode · Truth Mode verified |
| Last Updated | 2026-08-08 |
| Sections | 11 |
| Event Tipi | 8 |
| Retry Stratejisi | 5 deneme, exponential backoff |
| Dead Letter | Zorunlu |
| Performans hedefi | < 100ms sync |
| Queue Katmanı | 3 (APCu, Redis, DB) |
| Edge Cases | 10 |
| Hard Guardrails | 8 |
| Yasak Örüntü | 8 |
| İlgili ADR | 7 |
| Çapraz Referans | 8 |
| Sözlük Terim | 20 |

---

## 12. Authority

| Alan | Değer |
|------|-------|
| Authority | Bayram Ali / Vault Steward |
| Review Status | Red Team · Human Mode · Truth Mode verified |
| Last Updated | 2026-08-08 |
| Version | 2.0.0 |
| Immutability | Active (güncellenebilir) |
| Next Review | Yeni event tipi eklendiğinde |
| Related Division | Data Engineering |
| Risk Seviyesi | Yüksek (veri tutarlılığı) |

---

## 13. Deployment Considerations

| # | Konu | Detay |
|---|------|-------|
| 1 | Queue deploy | APCu/Redis yapılandırması |
| 2 | Consumer deploy | Servis instances |
| 3 | DB migration | event_queue tablosu |
| 4 | Dead letter deploy | DLQ tablosu |
| 5 | Monitoring | Queue boyutu izleme |
| 6 | Alerting | Dead letter bildirimi |
| 7 | Backup | Queue yedekleme |
| 8 | Rollback | Eski senkronizasyon |

---

## 14. Testing Strategy

| Test Türü | Kapsam | Framework |
|-----------|--------|-----------|
| Unit Test | EventProducer/Consumer | PHPUnit |
| Integration Test | Multi-DB sync | PHPUnit |
| Load Test | Yüksek event yükü | k6 |
| Edge Case Test | Dead letter | PHPUnit |
| Idempotency Test | Duplicate event | PHPUnit |
| Performance Test | 100ms sync | Custom script |
| E2E Test | Tam sync akışı | Playwright |
| Chaos Test | DB down durumu | Custom script |

---

## 15. Risk Assessment

| # | Risk | Olasılık | Etki | Mitigasyon |
|---|------|----------|------|------------|
| R1 | Veri tutarsızlığı | Orta | Yüksek | Hash comparison |
| R2 | Queue dolu | Düşük | Orta | Queue partitioning |
| R3 | Consumer crash | Düşük | Orta | Requeue + retry |
| R4 | Poison message | Düşük | Yüksek | Dead letter |
| R5 | Duplicate event | Orta | Düşük | Idempotency key |
| R6 | DB down | Düşük | Yüksek | Fallback queue |
| R7 | Memory pressure | Düşük | Orta | Chunked transfer |
| R8 | Network partition | Düşük | Yüksek | Local queue |

---

## 16. Maintenance Guidelines

| # | Görev | Siklik | Sorumlu |
|---|-------|--------|---------|
| 1 | Queue monitor | Sürekli | DevOps Engineer |
| 2 | Dead letter review | Günlük | Data Engineer |
| 3 | Performance audit | Haftalık | QA Engineer |
| 4 | Consistency check | Her saat | Data Engineer |
| 5 | Schema review | Aylık | Data Engineer |
| 6 | Event type audit | Üç aylık | Data Engineer |

---

## 17. Future Considerations

| # | Konu | Durum | Notlar |
|---|------|-------|--------|
| 1 | Event sourcing | Planlanıyor | Tam olay geçmişi |
| 2 | CQRS | Araştırılıyor | Command-query ayrımı |
| 3 | Kafka/ RabbitMQ | Gelecek | Enterprise messaging |
| 4 | Real-time sync | Planlanıyor | WebSocket bildirimleri |
| 5 | Event replay | Araştırılıyor | Olay tekrarı |
| 6 | Cross-service saga | Gelecek | Dağıtık işlem |

---

## 18. Event Schema Standards

| Alan | Tip | Zorunlu | Açıklama |
|------|-----|---------|----------|
| event_id | UUID | ✅ | Benzersiz olay kimliği |
| event_type | VARCHAR(100) | ✅ | Olay tipi (download.completed vb.) |
| payload | JSON | ✅ | Olay verisi |
| producer | VARCHAR(50) | ✅ | Üreteci servis |
| timestamp | DATETIME | ✅ | UTC zaman damgası |
| version | INT | ✅ | Olay versiyonu |
| correlation_id | UUID | ❌ | İlişkili olaylar |
| metadata | JSON | ❌ | Ek bilgi |

---

## 19. Service-to-DB Mapping

| Servis | Okuduğu DB | Yazdığı DB | Event Üretiyor | Event Tüketiyor |
|--------|-----------|-----------|----------------|-----------------|
| Control Service | auth, user | auth, user | user.registered | — |
| Media Service | media, musics | media | media.processed | download.completed |
| Download Service | catalog | catalog, musics | download.completed | song.added |
| Music Service | musics, albums | musics, albums | song.added, album.updated | download.completed, media.processed |
| Playlist Service | playlist | playlist | playlist.updated | song.added |
| System Service | system | system | config.changed | — |
| Log Service | logs | logs | log.created | — |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
