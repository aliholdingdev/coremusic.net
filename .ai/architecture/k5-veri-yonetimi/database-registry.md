---
title: "Veritabanı Kayıt Sistemi"
layer: K5
category: "Veri Yönetimi"
date: 2026-09-20
version: 1.0.0
status: stable
components: 4
dependencies: [K5]
---

# Database Registry - 18 BCNF Veritabanı Kaydı

## Genel Bakış

Database Registry modülü, COREMUSIC'in 18 BCNF veritabanı ve 156 tablosunun merkezi kaydını tutan, tablolar arası ilişkileri ve dependency'leri yöneten bir katalog sistemidir. Schema documentation, relationship mapping ve data dictionary sağlar.

## Teknik Detaylar

### Veritabanı Kaydı

```yaml
# database-registry.yaml
registry:
  version: "1.0.0"
  last_updated: "2026-09-20"
  
  databases:
    coremusic_users:
      purpose: "Kullanıcı verileri ve oturum yönetimi"
      character_set: "utf8mb4"
      collation: "utf8mb4_unicode_ci"
      tables: 12
      total_rows_estimate: 5000000
      avg_row_size_kb: 0.5
      
      tables:
        users:
          purpose: "Kullanıcı hesap bilgileri"
          primary_key: "user_id"
          estimated_rows: 1000000
          indexes: ["email", "username", "status", "created_at"]
          foreign_keys:
            - { column: "referrer_id", references: "users.user_id" }
          
        user_settings:
          purpose: "Kullanıcı tercihleri"
          primary_key: "setting_id"
          estimated_rows: 5000000
          indexes: ["user_id"]
          foreign_keys:
            - { column: "user_id", references: "users.user_id", on_delete: "CASCADE" }
          
        user_sessions:
          purpose: "Oturum takibi"
          primary_key: "session_id"
          estimated_rows: 2000000
          indexes: ["user_id", "expires_at", "is_active"]
          foreign_keys:
            - { column: "user_id", references: "users.user_id", on_delete: "CASCADE" }
    
    coremusic_music:
      purpose: "Müzik metadata ve katalog"
      character_set: "utf8mb4"
      collation: "utf8mb4_unicode_ci"
      tables: 18
      total_rows_estimate: 50000000
      
      tables:
        artists:
          purpose: "Sanatçı bilgileri"
          primary_key: "artist_id"
          estimated_rows: 500000
          indexes: ["name", "slug", "genre_primary"]
          
        albums:
          purpose: "Albüm bilgileri"
          primary_key: "album_id"
          estimated_rows: 2000000
          indexes: ["artist_id", "release_date", "slug"]
          foreign_keys:
            - { column: "artist_id", references: "artists.artist_id", on_delete: "CASCADE" }
          
        tracks:
          purpose: "Şarkı bilgileri ve audio metadata"
          primary_key: "track_id"
          estimated_rows: 20000000
          indexes: ["album_id", "artist_id", "slug", "isrc", "bpm"]
          foreign_keys:
            - { column: "album_id", references: "albums.album_id", on_delete: "SET NULL" }
            - { column: "artist_id", references: "artists.artist_id", on_delete: "CASCADE" }
          
        audio_fingerprints:
          purpose: "Ses parmak izi verileri"
          primary_key: "fingerprint_id"
          estimated_rows: 40000000
          indexes: ["track_id", "hash_value", "algorithm"]
          foreign_keys:
            - { column: "track_id", references: "tracks.track_id", on_delete: "CASCADE" }
    
    coremusic_playlists:
      purpose: "Playlist yönetimi"
      tables: 10
      total_rows_estimate: 10000000
      
      tables:
        playlists:
          purpose: "Playlist tanımları"
          primary_key: "playlist_id"
          estimated_rows: 2000000
          indexes: ["user_id", "is_public", "slug"]
          
        playlist_tracks:
          purpose: "Playlist şarkı ilişkileri"
          primary_key: "id"
          estimated_rows: 20000000
          indexes: ["playlist_id", "track_id"]
          
    coremusic_analytics:
      purpose: "Kullanım analitikleri"
      tables: 15
      total_rows_estimate: 500000000
      
      tables:
        user_listens:
          purpose: "Dinleme geçmişi"
          primary_key: ["listen_id", "listened_at"]
          estimated_rows: 500000000
          partitioned_by: "RANGE(listened_at)"
          indexes: ["user_id", "track_id", "listened_at"]
          
        search_queries:
          purpose: "Arama sorguları"
          primary_key: "query_id"
          estimated_rows: 100000000
          indexes: ["user_id", "query_text", "created_at"]
```

### Table Relationship Map

```yaml
# table-relationships.yaml
relationships:
  # Kullanıcı ilişkileri
  users:
    has_many:
      - { table: "user_settings", foreign_key: "user_id" }
      - { table: "user_sessions", foreign_key: "user_id" }
      - { table: "user_listens", foreign_key: "user_id" }
      - { table: "user_playlists", foreign_key: "user_id" }
      - { table: "user_favorites", foreign_key: "user_id" }
      - { table: "user_consents", foreign_key: "user_id" }
    
    belongs_to:
      - { table: "users", foreign_key: "referrer_id", as: "referrer" }
  
  # Müzik ilişkileri
  artists:
    has_many:
      - { table: "albums", foreign_key: "artist_id" }
      - { table: "tracks", foreign_key: "artist_id" }
      - { table: "artist_followers", foreign_key: "artist_id" }
    
    has_one:
      - { table: "artist_metadata", foreign_key: "artist_id" }
  
  albums:
    belongs_to:
      - { table: "artists", foreign_key: "artist_id" }
    
    has_many:
      - { table: "tracks", foreign_key: "album_id" }
      - { table: "album_reviews", foreign_key: "album_id" }
  
  tracks:
    belongs_to:
      - { table: "albums", foreign_key: "album_id" }
      - { table: "artists", foreign_key: "artist_id" }
    
    has_many:
      - { table: "audio_fingerprints", foreign_key: "track_id" }
      - { table: "user_listens", foreign_key: "track_id" }
      - { table: "track_lyrics", foreign_key: "track_id" }
      - { table: "track_features", foreign_key: "track_id" }
  
  # Playlist ilişkileri
  playlists:
    belongs_to:
      - { table: "users", foreign_key: "user_id" }
    
    has_many:
      - { table: "playlist_tracks", foreign_key: "playlist_id" }
      - { table: "playlist_followers", foreign_key: "playlist_id" }
  
  playlist_tracks:
    belongs_to:
      - { table: "playlists", foreign_key: "playlist_id" }
      - { table: "tracks", foreign_key: "track_id" }
```

### Data Dictionary

```php
<?php
class DataDictionary
{
    private MySQLConnection $db;
    private array $registry;
    
    public function __construct(MySQLConnection $db, string $registryPath)
    {
        $this->db = $db;
        $this->registry = yaml_parse_file($registryPath);
    }
    
    public function getTableInfo(string $database, string $table): array
    {
        // Schema'dan bilgi al
        $columns = $this->getColumns($database, $table);
        $indexes = $this->getIndexes($database, $table);
        $foreignKeys = $this->getForeignKeys($database, $table);
        
        // Registry'den metadata al
        $metadata = $this->registry['databases'][$database]['tables'][$table] ?? [];
        
        return [
            'database' => $database,
            'table' => $table,
            'purpose' => $metadata['purpose'] ?? '',
            'columns' => $columns,
            'indexes' => $indexes,
            'foreign_keys' => $foreignKeys,
            'estimated_rows' => $metadata['estimated_rows'] ?? 0,
            'row_size_estimate' => $this->estimateRowSize($columns)
        ];
    }
    
    public function getColumns(string $database, string $table): array
    {
        $stmt = $this->db->query("
            SELECT 
                COLUMN_NAME,
                DATA_TYPE,
                CHARACTER_MAXIMUM_LENGTH,
                IS_NULLABLE,
                COLUMN_DEFAULT,
                COLUMN_KEY,
                EXTRA,
                COLUMN_COMMENT
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = '{$database}' 
            AND TABLE_NAME = '{$table}'
            ORDER BY ORDINAL_POSITION
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getIndexes(string $database, string $table): array
    {
        $stmt = $this->db->query("
            SELECT 
                INDEX_NAME,
                COLUMN_NAME,
                SEQ_IN_INDEX,
                INDEX_TYPE,
                NON_UNIQUE
            FROM INFORMATION_SCHEMA.STATISTICS
            WHERE TABLE_SCHEMA = '{$database}' 
            AND TABLE_NAME = '{$table}'
            ORDER BY INDEX_NAME, SEQ_IN_INDEX
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getForeignKeys(string $database, string $table): array
    {
        $stmt = $this->db->query("
            SELECT 
                CONSTRAINT_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_SCHEMA,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = '{$database}' 
            AND TABLE_NAME = '{$table}'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function searchTables(string $query): array
    {
        $results = [];
        
        foreach ($this->registry['databases'] as $dbName => $dbInfo) {
            foreach ($dbInfo['tables'] as $tableName => $tableInfo) {
                if (stripos($tableName, $query) !== false ||
                    stripos($tableInfo['purpose'], $query) !== false) {
                    $results[] = [
                        'database' => $dbName,
                        'table' => $tableName,
                        'purpose' => $tableInfo['purpose']
                    ];
                }
            }
        }
        
        return $results;
    }
    
    public function generateDocumentation(): string
    {
        $output = "# COREMUSIC Data Dictionary\n\n";
        
        foreach ($this->registry['databases'] as $dbName => $dbInfo) {
            $output .= "## {$dbName}\n\n";
            $output .= "**Purpose:** {$dbInfo['purpose']}\n\n";
            
            foreach ($dbInfo['tables'] as $tableName => $tableInfo) {
                $output .= "### {$tableName}\n\n";
                $output .= "{$tableInfo['purpose']}\n\n";
                $output .= "- **Primary Key:** {$tableInfo['primary_key']}\n";
                $output .= "- **Estimated Rows:** {$tableInfo['estimated_rows']}\n";
                $output .= "- **Indexes:** " . implode(', ', $tableInfo['indexes']) . "\n\n";
            }
        }
        
        return $output;
    }
    
    private function estimateRowSize(array $columns): float
    {
        $size = 0;
        
        foreach ($columns as $column) {
            switch ($column['DATA_TYPE']) {
                case 'bigint':
                    $size += 8;
                    break;
                case 'int':
                    $size += 4;
                    break;
                case 'smallint':
                    $size += 2;
                    break;
                case 'tinyint':
                    $size += 1;
                    break;
                case 'varchar':
                    $size += ($column['CHARACTER_MAXIMUM_LENGTH'] ?? 255) * 0.5;
                    break;
                case 'text':
                    $size += 1000;
                    break;
                case 'json':
                    $size += 500;
                    break;
                case 'timestamp':
                    $size += 8;
                    break;
                default:
                    $size += 10;
            }
        }
        
        return round($size / 1024, 2); // KB
    }
}
```

## API / Konfigürasyon

```yaml
# config/database-registry.yaml
registry:
  storage: "file"  # file, database
  path: "/config/database-registry.yaml"
  
  auto_discovery:
    enabled: true
    interval: 86400
  
  documentation:
    auto_generate: true
    output_path: "/docs/database"
    format: "markdown"
```

## Durum: Implementasyon

Database Registry modülü **stable** durumdadır. 18 veritabanı ve 156 tablo kayıtlı. Relationship mapping ve data dictionary aktif.
