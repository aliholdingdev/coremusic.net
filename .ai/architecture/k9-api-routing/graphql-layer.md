---
title: "GraphQL Optional Layer"
layer: K9
category: "API & Routing"
date: 2026-09-20
status: "tamamlandı"
---

# GraphQL Optional Layer

## Genel Bakış

GraphQL layer, COREMUSIC API'lerine esnek sorgu desteği ekleyen opsiyonel bir abstraction'dır. İstemciler tam olarak ihtiyaç duydukları verileri seçerek over-fetching ve under-fetching sorunlarını çözer. Schema stitching ile birden fazla microservice'in verilerini tek bir sorguyla birleştirir.

Bu katman, REST API'nin üzerine inşa edilmiştir ve mevcut BFF endpoint'lerini wrap eder. Apollo Server veya benzeri bir runtime kullanılarak execute edilir. Subscription desteği ile real-time güncellemeler sunar.

## API Tanımı

### GraphQL Endpoint

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/graphql` | POST | Ana GraphQL endpoint |
| `/graphql` | GET | Introspection & Playground |
| `/graphql/ws` | WebSocket | Subscriptions |

### Schema Summary

```graphql
# Core Types
type Query {
  track(id: ID!): Track
  tracks(filter: TrackFilter, pagination: PaginationInput): TrackConnection!
  playlist(id: ID!): Playlist
  userPlaylists(pagination: PaginationInput): PlaylistConnection!
  me: User!
  search(query: String!, type: SearchType): SearchResult!
  recommendations(limit: Int, mood: Mood): [Recommendation!]!
}

type Mutation {
  playTrack(trackId: ID!, position: Int): PlayResult!
  likeTrack(trackId: ID!): Track!
  createPlaylist(input: CreatePlaylistInput!): Playlist!
  addTrackToPlaylist(playlistId: ID!, trackId: ID!): Playlist!
  removeTrackFromPlaylist(playlistId: ID!, trackId: ID!): Playlist!
  updateUserProfile(input: UpdateProfileInput!): User!
  saveEqualizerPreset(input: EQPresetInput!): EQPreset!
}

type Subscription {
  nowPlaying: NowPlayingUpdate!
  playlistUpdated(playlistId: ID!): PlaylistUpdate!
  newRecommendation: Recommendation!
}
```

## Teknik Detaylar

### GraphQL Mimarisi

```
┌─────────────────────────────────────────────────────────────┐
│                     GRAPHQL LAYER                            │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐  │
│  │                  Apollo Server                        │  │
│  │                                                      │  │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐          │  │
│  │  │  Schema  │  │ Resolver │  │  Data    │          │  │
│  │  │  Defs    │  │  Chain   │  │  Loader  │          │  │
│  │  └──────────┘  └──────────┘  └──────────┘          │  │
│  │                                                      │  │
│  │  ┌──────────────────────────────────────────────┐   │  │
│  │  │           Schema Stitching Layer              │   │  │
│  │  │  ┌─────────┐ ┌─────────┐ ┌─────────┐        │   │  │
│  │  │  │  User   │ │  Track  │ │  AI     │        │   │  │
│  │  │  │ Schema  │ │ Schema  │ │ Schema  │        │   │  │
│  │  │  └─────────┘ └─────────┘ └─────────┘        │   │  │
│  │  └──────────────────────────────────────────────┘   │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐  │
│  │              BFF REST Endpoints                       │  │
│  │  /bff/web/dashboard → Dashboard Resolver             │  │
│  │  /bff/web/player    → Player Resolver                │  │
│  │  /bff/mobile/feed   → Feed Resolver                  │  │
│  └──────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

### Schema Definition

```graphql
# schema.graphql

# Scalars
scalar DateTime
scalar JSON
scalar URL

# Enums
enum Mood {
  ENERGETIC
  CALM
  FOCUS
  WORKOUT
  SLEEP
  PARTY
  ROMANTIC
}

enum AudioFormat {
  MP3
  AAC
  FLAC
  ALAC
  DSD
  MQA
}

enum SearchType {
  TRACKS
  ARTISTS
  ALBUMS
  PLAYLISTS
  ALL
}

# Types
type Track {
  id: ID!
  title: String!
  artist: Artist!
  album: Album!
  duration: Int!
  genres: [String!]!
  audioQuality: AudioQuality!
  artwork: Artwork!
  analysis: AudioAnalysis
  lyrics: Lyrics
  isLiked: Boolean!
  playCount: Int!
  createdAt: DateTime!
}

type Artist {
  id: ID!
  name: String!
  imageUrl: URL
  bio: String
  genres: [String!]!
  topTracks(limit: Int = 5): [Track!]!
  albums: [Album!]!
}

type Album {
  id: ID!
  title: String!
  artist: Artist!
  year: Int!
  artwork: Artwork!
  tracks: [Track!]!
  totalDuration: Int!
}

type User {
  id: ID!
  email: String!
  displayName: String!
  avatarUrl: URL
  subscription: Subscription!
  library: Library!
  recentlyPlayed(limit: Int = 10): [Track!]!
}

type Playlist {
  id: ID!
  name: String!
  description: String
  owner: User!
  isPublic: Boolean!
  tracks: [Track!]!
  trackCount: Int!
  totalDuration: Int!
  artwork: Artwork
  createdAt: DateTime!
  updatedAt: DateTime!
}

type Recommendation {
  track: Track!
  reason: String!
  confidence: Float!
  mood: Mood
}

# Input Types
input TrackFilter {
  genre: String
  artist: String
  minDuration: Int
  maxDuration: Int
  audioFormat: AudioFormat
  mood: Mood
}

input PaginationInput {
  page: Int = 1
  pageSize: Int = 20
}

input CreatePlaylistInput {
  name: String!
  description: String
  isPublic: Boolean = false
  trackIds: [ID!]
}

# Connections (Relay-style pagination)
type TrackConnection {
  edges: [TrackEdge!]!
  pageInfo: PageInfo!
  totalCount: Int!
}

type TrackEdge {
  node: Track!
  cursor: String!
}

type PageInfo {
  hasNextPage: Boolean!
  hasPreviousPage: Boolean!
  startCursor: String
  endCursor: String
}

# Subscriptions
type NowPlayingUpdate {
  track: Track!
  position: Int!
  isPlaying: Boolean!
}

type PlaylistUpdate {
  playlist: Playlist!
  action: String!
  track: Track
}
```

### Resolver Implementation

```typescript
// resolvers/trackResolver.ts
import { IResolvers } from "@graphql-tools/utils";
import { DataLoader } from "../loaders/DataLoader";

const trackResolvers: IResolvers = {
  Query: {
    track: async (_, { id }, { dataLoaders, userId }) => {
      const track = await dataLoaders.trackLoader.load(id);

      // N+1 problem DataLoader ile çözülür
      return {
        ...track,
        isLiked: await dataLoaders.userLikeLoader.load({ userId, trackId: id }),
      };
    },

    tracks: async (_, { filter, pagination }, { dataLoaders }) => {
      const result = await trackService.search({
        genre: filter?.genre,
        artist: filter?.artist,
        minDuration: filter?.minDuration,
        maxDuration: filter?.maxDuration,
        page: pagination?.page || 1,
        pageSize: pagination?.pageSize || 20,
      });

      return {
        edges: result.items.map((track) => ({
          node: track,
          cursor: Buffer.from(`cursor:${track.id}`).toString("base64"),
        })),
        pageInfo: {
          hasNextPage: result.hasMore,
          hasPreviousPage: result.page > 1,
          startCursor: result.items[0]
            ? Buffer.from(`cursor:${result.items[0].id}`).toString("base64")
            : null,
          endCursor: result.items[result.items.length - 1]
            ? Buffer.from(
                `cursor:${result.items[result.items.length - 1].id}`
              )
            : "base64"
            : null,
        },
        totalCount: result.total,
      };
    },

    search: async (_, { query, type }, { dataLoaders }) => {
      const results = await searchService.search(query, type);
      return results;
    },

    recommendations: async (_, { limit, mood }, { userId, dataLoaders }) => {
      return await aiService.getRecommendations(userId, { limit, mood });
    },
  },

  Track: {
    artist: async (track, _, { dataLoaders }) => {
      return dataLoaders.artistLoader.load(track.artistId);
    },

    album: async (track, _, { dataLoaders }) => {
      return dataLoaders.albumLoader.load(track.albumId);
    },

    analysis: async (track, _, { dataLoaders }) => {
      return dataLoaders.audioAnalysisLoader.load(track.id);
    },

    isLiked: async (track, _, { userId, dataLoaders }) => {
      if (!userId) return false;
      return dataLoaders.userLikeLoader.load({ userId, trackId: track.id });
    },
  },

  Mutation: {
    playTrack: async (_, { trackId, position }, { userId }) => {
      await eventBus.publish("TRACK_PLAYED", {
        userId,
        trackId,
        position: position || 0,
        timestamp: new Date(),
      });

      return { success: true, trackId };
    },

    likeTrack: async (_, { trackId }, { userId }) => {
      await userService.toggleLike(userId, trackId);
      return await trackService.getTrack(trackId);
    },

    createPlaylist: async (_, { input }, { userId }) => {
      return await playlistService.create({
        userId,
        name: input.name,
        description: input.description,
        isPublic: input.isPublic,
        trackIds: input.trackIds,
      });
    },
  },

  Subscription: {
    nowPlaying: {
      subscribe: (_, __, { pubsub }) => {
        return pubsub.asyncIterator(["NOW_PLAYING_UPDATE"]);
      },
    },

    playlistUpdated: {
      subscribe: (_, { playlistId }, { pubsub }) => {
        return pubsub.asyncIterator([`PLAYLIST_UPDATED_${playlistId}`]);
      },
    },
  },
};

export default trackResolvers;
```

### DataLoader (N+1 Prevention)

```typescript
// loaders/DataLoader.ts
import DataLoader from "dataloader";

export class MusicDataLoader {
  readonly trackLoader: DataLoader<string, Track>;
  readonly artistLoader: DataLoader<string, Artist>;
  readonly albumLoader: DataLoader<string, Album>;
  readonly userLikeLoader: DataLoader<{ userId: string; trackId: string }, boolean>;

  constructor(private db: DatabasePool) {
    this.trackLoader = new DataLoader<string, Track>(
      async (ids) => {
        const tracks = await db.query(
          "SELECT * FROM tracks WHERE id = ANY($1)",
          [ids]
        );
        const trackMap = new Map(tracks.map((t) => [t.id, t]));
        return ids.map((id) => trackMap.get(id) || new Error(`Track ${id} not found`));
      },
      { maxBatchSize: 100 }
    );

    this.artistLoader = new DataLoader<string, Artist>(
      async (ids) => {
        const artists = await db.query(
          "SELECT * FROM artists WHERE id = ANY($1)",
          [ids]
        );
        const artistMap = new Map(artists.map((a) => [a.id, a]));
        return ids.map((id) => artistMap.get(id) || new Error(`Artist ${id} not found`));
      },
      { maxBatchSize: 50 }
    );

    this.userLikeLoader = new DataLoader<{ userId: string; trackId: string }, boolean>(
      async (keys) => {
        const results = await db.query(
          `SELECT user_id, track_id FROM user_likes
           WHERE (user_id, track_id) = ANY($1)`,
          [keys.map((k) => [k.userId, k.trackId])]
        );
        const likeSet = new Set(
          results.map((r) => `${r.userId}:${r.trackId}`)
        );
        return keys.map((k) => likeSet.has(`${k.userId}:${k.trackId}`));
      },
      { maxBatchSize: 200 }
    );
  }
}
```

## Konfigürasyon

```yaml
# graphql-config.yaml
graphql:
  endpoint: "/graphql"
  playground:
    enabled: true  # sadece development
    endpoint: "/graphql/playground"

  introspection:
    enabled: true  # production'da false olabilir

  depth_limit: 10
  complexity_limit: 1000

  persisted_queries:
    enabled: true
    cache_ttl: 3600

  subscriptions:
    enabled: true
    keep_alive: 30000  # ms

  apq:
    enabled: true  # Automatic Persisted Queries

  tracing:
    enabled: true  # Apollo Tracing
```

## Bağımlılıklar

### Bağımlı Olduğu
- **K9 BFF**: REST endpoint wrapping
- **K9 Event Bus**: Subscriptions

### Bağımlı Olan
- **K11 Web**: GraphQL client (Apollo Client)
- **K12 Mobile**: GraphQL client

## Durum: Implementasyon

- [x] GraphQL schema design
- [ ] Apollo Server setup
- [ ] Resolver implementation
- [ ] DataLoader (N+1 prevention)
- [ ] Schema stitching
- [ ] Subscriptions (WebSocket)
- [ ] Persisted queries
- [ ] Complexity limiting
- [ ] Depth limiting
- [ ] Apollo Studio integration
