---
title: "gRPC Internal Communication"
layer: K9
category: "API & Routing"
date: 2026-09-20
status: "tamamlandı"
---

# gRPC Internal Communication

## Genel Bakış

gRPC, COREMUSIC microservices'leri arasındaki high-performance, low-latency iletişim protokolüdür. HTTP/2 over TLS ile binary serialization (Protocol Buffers) kullanarak REST'e göre 5-10x daha hızlı veri transferi sağlar. Bidirectional streaming ile real-time ses verisi aktarımı destekler.

Internal service-to-service communication'da gRPC tercih edilirken, external client communication'da REST/GraphQL kullanılır. gRPC-Web ile browser'lar desteklenir. Load balancing ve service discovery Kubernetes native olarak yönetilir.

## API Tanımı

### Service Definitions

| Service | Port | Clients | Açıklama |
|---------|------|---------|----------|
| `UserService` | 9001 | K4, K6, K9 | Kullanıcı yönetimi |
| `TrackService` | 9002 | K4, K5, K6, K9 | Şarkı yönetimi |
| `PlaylistService` | 9003 | K4, K6, K9 | Playlist yönetimi |
| `AudioService` | 9004 | K5, K13, K14 | Ses streaming |
| `AIService` | 9005 | K4, K9 | AI engine |
| `AnalyticsService` | 9006 | K4, K6, K9 | Analytics |
| `NotificationService` | 9007 | K4, K10 | Bildirimler |
| `BillingService` | 9008 | K10 | Faturalandırma |

## Teknik Detaylar

### gRPC Mimarisi

```
┌─────────────────────────────────────────────────────────────────┐
│                    INTERNAL NETWORK (Kubernetes)                 │
│                                                                 │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │                    gRPC Communication                      │  │
│  │                                                           │  │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐               │  │
│  │  │  User    │←→│  Track   │←→│  AI      │               │  │
│  │  │  Service │  │  Service │  │  Service │               │  │
│  │  │  :9001   │  │  :9002   │  │  :9005   │               │  │
│  │  └────┬─────┘  └────┬─────┘  └────┬─────┘               │  │
│  │       │             │             │                       │  │
│  │  ┌────▼─────┐  ┌────▼─────┐  ┌────▼─────┐               │  │
│  │  │ Playlist │  │  Audio   │  │Analytics │               │  │
│  │  │ Service  │  │  Service │  │ Service  │               │  │
│  │  │  :9003   │  │  :9004   │  │  :9006   │               │  │
│  │  └──────────┘  └──────────┘  └──────────┘               │  │
│  │                                                           │  │
│  │  ┌──────────────────────────────────────────────────┐    │  │
│  │  │          Service Mesh (Istio/Linkerd)            │    │  │
│  │  │  - mTLS (mutual TLS)                             │    │  │
│  │  │  - Load Balancing (client-side + server-side)    │    │  │
│  │  │  - Circuit Breaking                              │    │  │
│  │  │  - Retry Policy                                  │    │  │
│  │  │  - Distributed Tracing (Jaeger)                  │    │  │
│  │  └──────────────────────────────────────────────────┘    │  │
│  └───────────────────────────────────────────────────────────┘  │
│                                                                 │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │              Kubernetes Service Discovery                  │  │
│  │  - DNS-based: service-name.namespace.svc.cluster.local   │  │
│  │  - Headless services for gRPC load balancing              │  │
│  └───────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

### Proto Definitions

```protobuf
// proto/track/v1/track.proto
syntax = "proto3";

package coremusic.track.v1;

option go_package = "github.com/coremusic/proto/track/v1;trackv1";
option java_package = "com.coremusic.proto.track.v1";

import "google/protobuf/timestamp.proto";
import "google/protobuf/empty.proto";

service TrackService {
  // Unary RPCs
  rpc GetTrack(GetTrackRequest) returns (Track);
  rpc ListTracks(ListTracksRequest) returns (ListTracksResponse);
  rpc SearchTracks(SearchTracksRequest) returns (SearchTracksResponse);

  // Server streaming
  rpc WatchNewReleases(WatchRequest) returns (stream Track);

  // Bidirectional streaming
  rpc BatchGetTracks(stream GetTrackRequest) returns (stream Track);
}

message Track {
  string id = 1;
  string title = 2;
  string artist_id = 3;
  string album_id = 4;
  int32 duration = 5;
  repeated string genres = 6;
  AudioQuality audio_quality = 7;
  Artwork artwork = 8;
  google.protobuf.Timestamp created_at = 9;
}

message AudioQuality {
  AudioFormat format = 1;
  int32 bit_depth = 2;
  int32 sample_rate = 3;
  int32 bitrate = 4;
}

enum AudioFormat {
  AUDIO_FORMAT_UNSPECIFIED = 0;
  AUDIO_FORMAT_MP3 = 1;
  AUDIO_FORMAT_AAC = 2;
  AUDIO_FORMAT_FLAC = 3;
  AUDIO_FORMAT_ALAC = 4;
  AUDIO_FORMAT_DSD = 5;
  AUDIO_FORMAT_MQA = 6;
}

message Artwork {
  string full_url = 1;
  string thumbnail_url = 2;
}

message GetTrackRequest {
  string track_id = 1;
  repeated string includes = 2; // artist, album, analysis
}

message ListTracksRequest {
  int32 page = 1;
  int32 page_size = 2;
  string sort_by = 3;
  string sort_order = 4;
  TrackFilter filter = 5;
}

message TrackFilter {
  string genre = 1;
  string artist_id = 2;
  string album_id = 3;
  int32 min_duration = 4;
  int32 max_duration = 5;
  AudioFormat audio_format = 6;
}

message ListTracksResponse {
  repeated Track tracks = 1;
  int32 total_count = 2;
  bool has_more = 3;
}

message SearchTracksRequest {
  string query = 1;
  int32 limit = 2;
  repeated string filters = 3;
}

message SearchTracksResponse {
  repeated TrackResult results = 1;
  int32 total_count = 2;
}

message TrackResult {
  Track track = 1;
  float relevance_score = 2;
  string highlight = 3;
}

message WatchRequest {
  string user_id = 1;
}
```

### gRPC Server Implementation (Go)

```go
// internal/grpc/trackserver/server.go
package trackserver

import (
    "context"
    "sync"

    pb "github.com/coremusic/proto/track/v1"
    "google.golang.org/grpc/codes"
    "google.golang.org/grpc/status"
)

type TrackServer struct {
    pb.UnimplementedTrackServiceServer
    trackRepo    TrackRepository
    artistRepo   ArtistRepository
    cache        Cache
    mu           sync.RWMutex
}

func NewTrackServer(
    trackRepo TrackRepository,
    artistRepo ArtistRepository,
    cache Cache,
) *TrackServer {
    return &TrackServer{
        trackRepo:  trackRepo,
        artistRepo: artistRepo,
        cache:      cache,
    }
}

func (s *TrackServer) GetTrack(
    ctx context.Context,
    req *pb.GetTrackRequest,
) (*pb.Track, error) {
    if req.TrackId == "" {
        return nil, status.Error(codes.InvalidArgument, "track_id is required")
    }

    // Cache check
    cacheKey := "track:" + req.TrackId
    if cached, ok := s.cache.Get(cacheKey); ok {
        return cached.(*pb.Track), nil
    }

    // Repository'den yükle
    track, err := s.trackRepo.GetByID(ctx, req.TrackId)
    if err != nil {
        if isNotFoundError(err) {
            return nil, status.Errorf(codes.NotFound, "track %s not found", req.TrackId)
        }
        return nil, status.Errorf(codes.Internal, "failed to get track: %v", err)
    }

    // Includes
    for _, include := range req.Includes {
        switch include {
        case "artist":
            artist, err := s.artistRepo.GetByID(ctx, track.ArtistId)
            if err == nil {
                track.Artist = artist
            }
        }
    }

    // Cache store
    s.cache.Set(cacheKey, track, 300) // 5 minutes

    return track, nil
}

func (s *TrackServer) ListTracks(
    ctx context.Context,
    req *pb.ListTracksRequest,
) (*pb.ListTracksResponse, error) {
    if req.Page < 1 {
        req.Page = 1
    }
    if req.PageSize < 1 || req.PageSize > 100 {
        req.PageSize = 20
    }

    tracks, total, err := s.trackRepo.List(ctx, ListParams{
        Page:     int(req.Page),
        PageSize: int(req.PageSize),
        SortBy:   req.SortBy,
        SortOrder: req.SortOrder,
        Filter:   req.Filter,
    })
    if err != nil {
        return nil, status.Errorf(codes.Internal, "failed to list tracks: %v", err)
    }

    return &pb.ListTracksResponse{
        Tracks:     tracks,
        TotalCount: int32(total),
        HasMore:    int(req.Page)*int(req.PageSize) < total,
    }, nil
}

// Server streaming: yeni çıkan şarkıları izle
func (s *TrackServer) WatchNewReleases(
    req *pb.WatchRequest,
    stream pb.TrackService_WatchNewReleasesServer,
) error {
    ch := s.trackRepo.SubscribeNewReleases()
    defer s.trackRepo.UnsubscribeNewReleases(ch)

    for {
        select {
        case track := <-ch:
            if err := stream.Send(track); err != nil {
                return err
            }
        case <-stream.Context().Done():
            return stream.Context().Err()
        }
    }
}

// Bidirectional streaming: toplu track yükleme
func (s *TrackServer) BatchGetTracks(
    stream pb.TrackService_BatchGetTracksServer,
) error {
    for {
        req, err := stream.Recv()
        if err != nil {
            return err
        }

        track, err := s.GetTrack(stream.Context(), req)
        if err != nil {
            // Hata olsa bile stream'i devam ettir
            continue
        }

        if err := stream.Send(track); err != nil {
            return err
        }
    }
}
```

### gRPC Client Implementation (TypeScript)

```typescript
// lib/grpc/client.ts
import * as grpc from "@grpc/grpc-js";
import { TrackServiceClient } from "../proto/track/v1/TrackService";
import { credentials, loadPackageDefinition } from "@grpc/grpc-js";

class GrpcClientManager {
  private clients: Map<string, grpc.Client>;
  private loadBalancer: RoundRobinLoadBalancer;

  constructor(private serviceEndpoints: ServiceEndpoints) {
    this.clients = new Map();
    this.loadBalancer = new RoundRobinLoadBalancer();
  }

  getTrackClient(): TrackServiceClient {
    if (!this.clients.has("track")) {
      const endpoint = this.loadBalancer.select("track-service");
      const client = new TrackServiceClient(
        endpoint,
        credentials.createInsecure()
      );
      this.clients.set("track", client);
    }
    return this.clients.get("track") as TrackServiceClient;
  }

  // Unary RPC call
  async getTrack(trackId: string, includes: string[] = []): Promise<Track> {
    return new Promise((resolve, reject) => {
      const client = this.getTrackClient();
      client.GetTrack(
        { trackId, includes },
        (error, response) => {
          if (error) reject(error);
          else resolve(response);
        }
      );
    });
  }

  // Server streaming
  async watchNewReleases(
    userId: string,
    onUpdate: (track: Track) => void
  ): Promise<void> {
    const client = this.getTrackClient();
    const stream = client.WatchNewReleases({ userId });

    stream.on("data", (track: Track) => {
      onUpdate(track);
    });

    stream.on("error", (error) => {
      console.error("Watch stream error:", error);
    });

    stream.on("end", () => {
      console.log("Watch stream ended");
    });
  }

  // Bidirectional streaming
  async batchGetTracks(
    trackIds: string[]
  ): Promise<Map<string, Track>> {
    return new Promise((resolve, reject) => {
      const client = this.getTrackClient();
      const stream = client.BatchGetTracks();
      const results = new Map<string, Track>();

      stream.on("data", (track: Track) => {
        results.set(track.id, track);
      });

      stream.on("end", () => {
        resolve(results);
      });

      stream.on("error", reject);

      // Gönder
      trackIds.forEach((id) => {
        stream.write({ trackId: id });
      });
      stream.end();
    });
  }
}
```

### Interceptors

```typescript
// grpc/interceptors/logging.ts
import * as grpc from "@grpc/grpc-js";

export const loggingInterceptor: grpc.Interceptor = (options, nextCall) => {
  return new grpc.InterceptingCall(options, {
    start: (metadata, listener, next) => {
      const startTime = Date.now();
      const methodName = options.method_definition.path;

      console.log(`[gRPC] → ${methodName}`);

      next(metadata, {
        onReceiveMetadata: (metadata, next) => {
          next(metadata);
        },
        onReceiveMessage: (message, next) => {
          const duration = Date.now() - startTime;
          console.log(`[gRPC] ← ${methodName} (${duration}ms)`);
          next(message);
        },
        onReceiveStatus: (status, next) => {
          if (status.code !== grpc.status.OK) {
            console.error(
              `[gRPC] ✗ ${methodName} failed: ${status.code} - ${status.details}`
            );
          }
          next(status);
        },
      });
    },
  });
};

// Retry interceptor
export const retryInterceptor: grpc.Interceptor = (options, nextCall) => {
  let retries = 0;
  const maxRetries = 3;
  const retryableCodes = [
    grpc.status.UNAVAILABLE,
    grpc.status.DEADLINE_EXCEEDED,
  ];

  return new grpc.InterceptingCall(options, {
    start: (metadata, listener, next) => {
      next(metadata, {
        onReceiveStatus: (status, next) => {
          if (
            retryableCodes.includes(status.code) &&
            retries < maxRetries
          ) {
            retries++;
            console.log(
              `[gRPC] Retrying ${options.method_definition.path} (attempt ${retries})`
            );
            // Exponential backoff
            setTimeout(() => {
              nextCall(options).start(metadata, listener, next);
            }, Math.pow(2, retries) * 100);
          } else {
            next(status);
          }
        },
      });
    },
  });
};
```

## Konfigürasyon

```yaml
# grpc-config.yaml
grpc:
  services:
    user-service:
      endpoint: "user-svc:9001"
      timeout: 5s
      max_message_size: "4MB"

    track-service:
      endpoint: "track-svc:9002"
      timeout: 5s
      max_message_size: "8MB"

    audio-service:
      endpoint: "audio-svc:9004"
      timeout: 30s
      max_message_size: "16MB"

    ai-service:
      endpoint: "ai-svc:9005"
      timeout: 60s
      max_message_size: "8MB"

  load_balancing:
    policy: "round_robin"  # round_robin | pick_first
    health_check:
      enabled: true
      interval: 10s

  retry:
    max_attempts: 3
    initial_backoff: "100ms"
    max_backoff: "5s"
    multiplier: 2.0
    retryable_status_codes:
      - UNAVAILABLE
      - DEADLINE_EXCEEDED

  keepalive:
    time: 30s
    timeout: 5s
    enforcement_policy:
      min_time: "30s"

  tls:
    enabled: true
    cert_file: "/etc/coremusic/grpc/tls.crt"
    key_file: "/etc/coremusic/grpc/tls.key"
    ca_file: "/etc/coremusic/grpc/ca.crt"
```

## Bağımlılıklar

### Bağımlı Olduğu
- **K0 OS**: Network, DNS (service discovery)
- **Kubernetes**: Service mesh, mTLS

### Bağımlı Olan
- **K4 AI**: AI service communication
- **K5 Audio Engine**: Audio streaming
- **K10 Services**: Tüm backend servisleri
- **K9 API Gateway**: gRPC-Web proxy

## Durum: Implementasyon

- [x] Proto definitions
- [ ] gRPC server (Go)
- [ ] gRPC client (TypeScript)
- [ ] Load balancing
- [ ] Retry policy
- [ ] Keepalive
- [ ] Interceptors (logging, tracing)
- [ ] Health checking
- [ ] gRPC-Web for browsers
- [ ] Reflection service (dev tools)
- [ ] Deadline propagation
