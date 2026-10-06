---
title: "Podcast Support"
layer: K15
category: "Medya & Streaming"
date: 2026-09-20
---

# Podcast Support

## Genel Bakış

Podcast desteği, RSS tabanlı podcast beslemelerini yönetir, bölüm depolamayı ve oynatmayı sağlar. COREMUSIC, podcast RSS parsing, episode metadata extraction ve progressive download streaming sunar. iTunes/Apple Podcasts uyumlu Dublin Core ve iTunes tag desteği mevcuttur.

## Podcast RSS Formatı

```xml
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" 
     xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:content="http://purl.org/rss/1.0/modules/content/">
  <channel>
    <title>COREMUSIC Podcast</title>
    <link>https://podcast.coremusic.io</link>
    <language>tr</language>
    <copyright>© 2026 COREMUSIC</copyright>
    <description>COREMUSIC teknik podcast serisi</description>
    
    <itunes:author>COREMUSIC Team</itunes:author>
    <itunes:summary>COREMUSIC geliştirme süreci</itunes:summary>
    <itunes:explicit>false</itunes:explicit>
    <itunes:category text="Technology"/>
    <itunes:owner>
      <itunes:name>COREMUSIC</itunes:name>
      <itunes:email>podcast@coremusic.io</itunes:email>
    </itunes:owner>
    <itunes:image href="https://podcast.coremusic.io/cover.jpg"/>
    
    <item>
      <title>Episode 42: Audio Streaming</title>
      <guid>https://podcast.coremusic.io/ep42.mp3</guid>
      <pubDate>Fri, 19 Sep 2026 12:00:00 +0300</pubDate>
      <enclosure url="https://podcast.coremusic.io/ep42.mp3"
                 length="45219840"
                 type="audio/mpeg"/>
      <itunes:duration>2345</itunes:duration>
      <itunes:episode>42</itunes:episode>
      <itunes:season>3</itunes:season>
      <itunes:episodeType>full</itunes:episodeType>
      <content:encoded><![CDATA[<p>Episode nội dung...</p>]]></content:encoded>
    </item>
  </channel>
</rss>
```

## Teknik Detaylar

### RSS Parsing Pipeline

```
RSS Feed Processing:
┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐
│ HTTP     │───▶│ XML      │───▶│ Tag      │───▶│ Episode  │
│ Fetch    │    │ Parser   │    │ Extractor│    │ Store    │
└──────────┘    └──────────┘    └──────────┘    └──────────┘
     │               │               │               │
     ▼               ▼               ▼               ▼
  GET/HEAD       SAX/DOM        iTunes Tags     MySQL DB
  ETag check     Namespace     Dublin Core     Redis Cache
```

### Episode Metadata

```
Podcast Episode Fields:
┌──────────────────┬────────────────────────────────┐
│ Alan             │ Açıklama                       │
├──────────────────┼────────────────────────────────┤
│ guid             │ Benzersiz tanımlayıcı          │
│ title            │ Bölüm başlığı                  │
│ description      │ Kısa açıklama                 │
│ content:encoded  │ Zengin HTML açıklama           │
│ enclosure.url    │ Medya dosyası URL'i            │
│ enclosure.length │ Dosya boyutu (byte)            │
│ enclosure.type   │ MIME type                      │
│ pubDate          │ Yayın tarihi (RFC 822)         │
│ itunes:duration  │ Süre (saniye)                  │
│ itunes:episode   │ Bölüm numarası                 │
│ itunes:season    │ Sezon numarası                 │
│ itunes:image     │ Bölüm görseli                  │
│ itunes:explicit  │ Explicit içerik flag           │
│ itunes:episodeType│ full/trailer/bonus            │
│ itunes:author    │ Yazar bilgisi                  │
└──────────────────┴────────────────────────────────┘
```

### Podcast Feed Yönetimi

```
Feed State Machine:
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│  Active     │───▶│  Stale      │───▶│  Inactive   │
│  (Fresh)    │    │  (Update    │    │  (No        │
│             │    │   Needed)   │    │   Updates)  │
└──────┬──────┘    └──────┬──────┘    └──────┬──────┘
       │                  │                  │
       ▼                  ▼                  ▼
  Next fetch: 1h    Next fetch: 5m    Archive feed

Feed Validation Rules:
- Must have <channel> element
- Must have at least one <item>
- enclosure.url must be accessible
- pubDate must be valid RFC 822
- itunes:duration must be parseable
```

### Progressive Download

```
Podcast Streaming Pipeline:
┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐
│ HTTP     │───▶│ Buffer   │───▶│ Decode   │───▶│ Play     │
│ GET      │    │ Manager  │    │          │    │          │
└──────────┘    └──────────┘    └──────────┘    └──────────┘
     │               │               │               │
     ▼               ▼               ▼               ▼
  Range Request  Circular Buf    MP3/AAC Dec    PCM Output
  Resume         Pre-buffer 30s  Sample Rate   Volume
```

### Offline Support

```
Download Manager:
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│ Queue       │───▶│ Downloader  │───▶│ Storage     │
│ Manager     │    │ (HTTP)      │    │ (Local FS)  │
└─────────────┘    └─────────────┘    └─────────────┘

Download States:
- pending: Sırada bekliyor
- downloading: İndiriliyor (progress: %)
- paused: Duraklatıldı
- completed: İndirildi
- failed: Hata oluştu
- queued: Sıraya alındı

Storage Strategy:
- Max offline episodes: 50
- Max storage: 5 GB
- Auto-cleanup: LRU eviction
- Priority: manual > starred > oldest
```

## Kod / Konfigürasyon

### Podcast Manager Konfigürasyonu

```yaml
podcast_manager:
  feeds:
    max_subscriptions: 100
    refresh_interval: 3600       # saniye
    auto_refresh: true
    user_agent: "COREMUSIC/1.0 Podcast Client"
    
  episodes:
    max_episodes_per_feed: 100
    auto_download_starred: true
    keep_episodes_days: 30
    
  storage:
    download_path: "/data/podcasts"
    max_storage_gb: 5
    max_offline_episodes: 50
    
  streaming:
    pre_buffer_seconds: 30
    resume_from_last_position: true
    sleep_timer_enabled: true
```

### RSS Parser Konfigürasyonu

```yaml
rss_parser:
  timeout_seconds: 30
  max_response_size: 10485760   # 10MB
  validate_enclosure: true
  resolve_relative_urls: true
  
  namespaces:
    - "http://www.itunes.com/dtds/podcast-1.0.dtd"
    - "http://purl.org/rss/1.0/modules/content/"
    - "http://purl.org/dc/elements/1.1/"
    - "http://www.w3.org/2005/Atom"
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Kullanım |
|------------|----------|----------|
| SimplePie | 1.8.x | RSS parser |
| ID3Lib | 3.8.x | MP3 metadata okuma |
| MySQL | 8.0.x | Episode depolama |
| Redis | 7.x | Cache, queue |
| FFmpeg | 6.1.x | Audio decode |

## Durum: Implementasyon

Podcast desteği tam olarak implemente edilmiştir. RSS parsing, episode management ve offline support çalışır durumdadır.

| Özellik | Durum |
|---------|-------|
| RSS Feed Parsing | Tamamlandı |
| iTunes Tag Support | Tamamlandı |
| Episode Metadata | Tamamlandı |
| Progressive Download | Tamamlandı |
| Offline Support | Tamamlandı |
| Sleep Timer | Tamamlandı |
| Chapter Markers | Tamamlandı |
| Auto-Download | Tamamlandı |
