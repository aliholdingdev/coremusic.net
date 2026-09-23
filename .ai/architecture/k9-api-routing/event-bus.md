---
title: "Event Bus - Mesaj Broker & Pub/Sub"
layer: K9
category: "API & Routing"
date: 2026-09-20
status: "tamamlandı"
---

# Event Bus

## Genel Bakış

Event Bus, COREMUSIC microservices'leri arasında asenkron iletişim kuran merkezi mesaj broker'ıdır. Pub/Sub pattern ile servisler birbirinden bağımsız olarak event üretebilir ve tüketebilir. Event sourcing ile tüm domain olayları loglanır ve replay edilebilir.

Apache Kafka yüksek throughput için, RabbitMQ ise low-latency message routing için kullanılır. Dual-write prevention ve exactly-once delivery garantisi sağlanır.

## API Tanımı

### Event Types

| Event | Producer | Consumer | Açıklama |
|-------|----------|----------|----------|
| `TRACK_PLAYED` | K5 Audio Engine | K4 AI, K6 Analytics | Şarkı çalma |
| `TRACK_LIKED` | K10 User Service | K4 AI, K6 Analytics | Şarkıyı beğenme |
| `PLAYLIST_CREATED` | K10 Playlist Service | K11-K20 Client | Playlist oluşturma |
| `PLAYLIST_UPDATED` | K10 Playlist Service | K11-K20 Client | Playlist güncelleme |
| `USER_REGISTERED` | K10 User Service | K4 AI, Notification | Yeni kullanıcı |
| `EQ_PRESET_SAVED` | K10 User Service | K5 Audio Engine | EQ preset kaydetme |
| `AI_RECOMMENDATION_READY` | K4 AI | K11-K20 Client | AI önerisi hazır |
| `AUDIO_STREAM_STARTED` | K5 Audio Engine | K13 Desktop | Stream başladı |
| `PAYMENT_PROCESSED` | K10 Billing Service | K4 AI, K10 User | Ödeme tamamlandı |
| `DEVICE_CONNECTED` | K14 IoT | K10 User, K5 Audio | Cihaz bağlandı |

### Event Bus API

```
POST /api/v1/events/publish
Body: { "eventType": "TRACK_PLAYED", "payload": {...}, "metadata": {...} }

GET /api/v1/events/subscribe/{topic}
WebSocket connection for real-time events

GET /api/v1/events/consumer-lags
Consumer group lag durumu
```

## Teknik Detaylar

### Event Bus Mimarisi

```
┌─────────────────────────────────────────────────────────────────┐
│                        EVENT BUS                                │
│                                                                 │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                    Apache Kafka                           │  │
│  │                                                          │  │
│  │  Topics:                                                 │  │
│  │  ├── track.events (partitioned by trackId)               │  │
│  │  ├── user.events (partitioned by userId)                 │  │
│  │  ├── playlist.events (partitioned by playlistId)         │  │
│  │  ├── ai.events (partitioned by userId)                   │  │
│  │  ├── audio.events (partitioned by sessionId)             │  │
│  │  └── system.events (control plane)                       │  │
│  │                                                          │  │
│  │  Consumer Groups:                                        │  │
│  │  ├── analytics-processor (K6)                            │  │
│  │  ├── ai-processor (K4)                                   │  │
│  │  ├── notification-service                                │  │
│  │  ├── search-indexer (Elasticsearch)                      │  │
│  │  └── audit-logger                                        │  │
│  └──────────────────────────────────────────────────────────┘  │
│                                                                 │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                    RabbitMQ                               │  │
│  │                                                          │  │
│  │  Exchanges:                                              │  │
│  │  ├── coremusic.commands (direct)                         │  │
│  │  ├── coremusic.notifications (fanout)                    │  │
│  │  └── coremusic.dlx (dead letter)                         │  │
│  │                                                          │  │
│  │  Queues:                                                 │  │
│  │  ├── command.user.update                                 │  │
│  │  ├── command.playlist.create                             │  │
│  │  ├── notification.email                                  │  │
│  │  ├── notification.push                                   │  │
│  │  └── dlq.permanent-failure                               │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

### Kafka Producer Implementation

```typescript
import { Kafka, Producer, ProducerRecord, Message } from "kafkajs";

interface EventMetadata {
  correlationId: string;
  causationId?: string;
  userId: string;
  timestamp: Date;
  version: string;
}

interface DomainEvent {
  eventType: string;
  aggregateId: string;
  payload: Record<string, unknown>;
  metadata: EventMetadata;
}

class EventBusProducer {
  private producer: Producer;
  private kafka: Kafka;

  constructor(config: KafkaConfig) {
    this.kafka = new Kafka({
      clientId: config.clientId,
      brokers: config.brokers,
      retry: { initialRetryTime: 100, retries: 8 },
    });
    this.producer = this.kafka.producer({
      allowAutoTopicCreation: false,
      transactionalId: config.transactionalId,
    });
  }

  async connect() {
    await this.producer.connect();
  }

  async publish<T extends DomainEvent>(
    topic: string,
    event: T,
    options?: { partition?: number; key?: string }
  ): Promise<void> {
    const message: Message = {
      key: options?.key || event.aggregateId,
      value: JSON.stringify(event),
      headers: {
        "event-type": event.eventType,
        "correlation-id": event.metadata.correlationId,
        "causation-id": event.metadata.causationId || "",
        "content-type": "application/json",
        "schema-version": event.metadata.version,
      },
    };

    await this.producer.send({
      topic,
      messages: [message],
      acks: -1, // All replicas
      compression: 3, // LZ4
    });
  }

  // Transaction ile atomic publish
  async publishTransactional(
    topic: string,
    events: DomainEvent[]
  ): Promise<void> {
    const transaction = await this.producer.transaction();

    try {
      for (const event of events) {
        await transaction.send({
          topic,
          messages: [{
            key: event.aggregateId,
            value: JSON.stringify(event),
            headers: {
              "event-type": event.eventType,
              "correlation-id": event.metadata.correlationId,
            },
          }],
        });
      }
      await transaction.commit();
    } catch (error) {
      await transaction.abort();
      throw error;
    }
  }
}
```

### Kafka Consumer Implementation

```typescript
import { Consumer, EachMessagePayload } from "kafkajs";

class EventBusConsumer {
  private consumer: Consumer;
  private handlers: Map<string, EventHandler>;
  private deadLetterQueue: DeadLetterQueue;

  constructor(
    private kafka: Kafka,
    config: ConsumerConfig
  ) {
    this.consumer = kafka.consumer({
      groupId: config.groupId,
      sessionTimeout: 30000,
      heartbeatInterval: 3000,
    });
    this.handlers = new Map();
    this.deadLetterQueue = new DeadLetterQueue(config.dlqTopic);
  }

  async subscribe(
    topic: string,
    handler: EventHandler
  ): Promise<void> {
    this.handlers.set(topic, handler);
    await this.consumer.subscribe({ topic, fromBeginning: false });
  }

  async start(): Promise<void> {
    await this.consumer.run({
      eachBatchAutoResolve: true,
      autoCommit: true,
      eachBatch: async ({
        batch,
        resolveOffset,
        heartbeat,
        commitOffsetsIfNecessary,
      }) => {
        for (const message of batch.messages) {
          try {
            const event = JSON.parse(message.value!.toString());
            const handler = this.handlers.get(batch.topic);

            if (handler) {
              await handler.handle(event);
            }

            resolveOffset(message.offset);
            await heartbeat();
          } catch (error) {
            // Dead letter queue'ya gönder
            await this.deadLetterQueue.send(batch.topic, {
              originalMessage: message,
              error: error.message,
              attempts: 1,
              firstAttemptAt: new Date(),
            });
          }
        }
      },
    });
  }
}

// Dead Letter Queue
class DeadLetterQueue {
  constructor(private dlqTopic: string) {}

  async send(
    originalTopic: string,
    payload: {
      originalMessage: Message;
      error: string;
      attempts: number;
      firstAttemptAt: Date;
    }
  ): Promise<void> {
    const dlqMessage = {
      originalTopic,
      originalPayload: payload.originalMessage.value?.toString(),
      error: payload.error,
      attempts: payload.attempts,
      firstAttemptAt: payload.firstAttemptAt,
      deadLetteredAt: new Date(),
    };

    await producer.send({
      topic: this.dlqTopic,
      messages: [{
        key: payload.originalMessage.key,
        value: JSON.stringify(dlqMessage),
      }],
    });
  }
}
```

### Event Schema Registry

```typescript
// Schema versioning ile backward compatibility
interface EventSchema {
  eventType: string;
  version: number;
  schema: Record<string, SchemaField>;
  migration?: (oldPayload: any) => any;
}

const eventSchemas: EventSchema[] = [
  {
    eventType: "TRACK_PLAYED",
    version: 1,
    schema: {
      trackId: { type: "string", required: true },
      deviceType: { type: "string", enum: ["web", "mobile", "desktop"] },
      position: { type: "number", min: 0 },
    },
  },
  {
    eventType: "TRACK_PLAYED",
    version: 2,
    schema: {
      trackId: { type: "string", required: true },
      deviceType: { type: "string", enum: ["web", "mobile", "desktop", "iot"] },
      position: { type: "number", min: 0 },
      volume: { type: "number", min: 0, max: 100 }, // v2'de eklendi
    },
    migration: (v1) => ({
      ...v1,
      volume: 70, // default value
    }),
  },
];
```

## Konfigürasyon

```yaml
# event-bus.yaml
event_bus:
  kafka:
    brokers:
      - "kafka-1:9092"
      - "kafka-2:9092"
      - "kafka-3:9092"
    client_id: "coremusic-events"
    acks: "all"
    compression: "lz4"
    batch_size: 16384
    linger_ms: 10

    topics:
      track.events:
        partitions: 12
        replication_factor: 3
        retention_ms: 604800000  # 7 gün
      user.events:
        partitions: 6
        replication_factor: 3
        retention_ms: 2592000000 # 30 gün
      system.events:
        partitions: 3
        replication_factor: 3
        retention_ms: 86400000   # 1 gün

    consumer_groups:
      analytics-processor:
        topics: ["track.events", "user.events"]
        auto_offset_reset: "latest"
        max_poll_interval: 300000
      ai-processor:
        topics: ["track.events", "user.events"]
        auto_offset_reset: "latest"

  rabbitmq:
    hosts:
      - "rabbitmq-1:5672"
      - "rabbitmq-2:5672"
    vhost: "/coremusic"
    exchanges:
      - name: "coremusic.commands"
        type: "direct"
        durable: true
      - name: "coremusic.notifications"
        type: "fanout"
        durable: true
    queues:
      - name: "command.user.update"
        durable: true
        dead_letter_exchange: "coremusic.dlx"
      - name: "notification.push"
        durable: true
```

## Bağımlılıklar

### Bağımlı Olduğu
- **K0 OS**: Network, disk I/O
- **K1 Hardware**: Broker sunucuları

### Bağımlı Olan
- **K4 AI**: AI event consumer
- **K5 Audio Engine**: Audio event producer
- **K6 Analytics**: Analytics consumer
- **K10 Services**: Tüm service'ler
- **K11-K20**: Client-side event consumption

## Durum: Implementasyon

- [x] Event Bus design
- [ ] Kafka cluster setup
- [ ] RabbitMQ setup
- [ ] Producer implementation
- [ ] Consumer implementation
- [ ] Dead letter queue
- [ ] Schema registry
- [ ] Event replay
- [ ] Consumer lag monitoring
- [ ] Exactly-once delivery
- [ ] Event versioning & migration
