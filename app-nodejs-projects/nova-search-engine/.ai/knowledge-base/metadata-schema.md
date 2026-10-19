# metadata-schema.md — Metadata Şeması

> Kaynak: `packages/database/src/schema.ts` (tek doğru kaynak).

## Tablolar

### videos (çekirdek)
| Kolon | Tip | Açıklama |
|---|---|---|
| youtube_id | TEXT UNIQUE | dedup anahtarı |
| channel_youtube_id / channel_name | TEXT | kanal |
| title / description | TEXT | arama alanları |
| duration_seconds / view_count / like_count | INT | metrikler |
| published_at / thumbnail_url | TEXT | |
| tags | TEXT(JSON) | etiket listesi |
| is_short / is_live | INT(bool) | işaretler |
| quality_score | REAL | kalite |
| created_at / updated_at | TEXT(ISO) | tazeleme takibi |

### channels
youtube_id UNIQUE · name · description · subscriber_count · video_count · verified

### search_log
query · results_count · source · took_ms · created_at

## Kurallar

1. Normalize edilmemiş veri DB'ye girmez (mapper sorumluluğu).
2. Upsert dedup: `ON CONFLICT(youtube_id) DO UPDATE` / `ON DUPLICATE KEY UPDATE`.
3. `updated_at` tazeleme politikasının kaynağıdır (stale sorgusu).
4. Kolon ekleme `ALTER TABLE ADD COLUMN` ile geriye dönüktür (migration).

## V2 notları

- FTS: `search_vector` (PostgreSQL) veya FTS5 (SQLite)
- `published_at` eksik gelebilir; tazelemede tamamlanır
