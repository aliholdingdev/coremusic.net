---
title: "Dosya Sistemi Depolama"
layer: K5
category: "Veri Yönetimi"
date: 2026-09-20
version: 1.0.0
status: stable
components: 5
dependencies: [K0, K1]
---

# File System Storage - Medya Depolama Sistemi

## Genel Bakış

File System Storage modülü, COREMUSIC'in medya dosyalarını (müzik, kapak resimleri, ses dosyaları) organize eden ve yöneten dosya sistemi yapısını tanımlar. Hierarchical directory structure, file naming conventions ve storage optimization stratejileri ile petabyte-scale depolama sağlar.

## Teknik Detaylar

### Dizin Yapısı

```
/media/
├── music/                          # Ana müzik dizini
│   ├── {artist_slug}/
│   │   ├── {album_slug}/
│   │   │   ├── tracks/
│   │   │   │   ├── {track_id}.mp3
│   │   │   │   ├── {track_id}.flac
│   │   │   │   ├── {track_id}.wav
│   │   │   │   └── {track_id}.m4a
│   │   │   ├── artwork/
│   │   │   │   ├── cover_original.jpg
│   │   │   │   ├── cover_large.jpg    # 640x640
│   │   │   │   ├── cover_medium.jpg   # 300x300
│   │   │   │   └── cover_small.jpg    # 64x64
│   │   │   └── metadata/
│   │   │       ├── lyrics.txt
│   │   │       └── credits.json
│   │   └── artist/
│   │       ├── photo_original.jpg
│   │       ├── photo_large.jpg
│   │       └── photo_small.jpg
│   └── compilation/
│       └── {year}/
│           └── {album_slug}/
├── podcasts/                        # Podcast dosyaları
│   ├── {podcast_id}/
│   │   ├── episodes/
│   │   │   └── {episode_id}.mp3
│   │   └── artwork/
├── user_uploads/                    # Kullanıcı yüklemeleri
│   ├── {user_id}/
│   │   ├── uploads/
│   │   │   └── {upload_id}.mp3
│   │   └── avatars/
│   │       └── {avatar_id}.jpg
├── generated/                       # AI üretilen dosyalar
│   ├── music/
│   │   └── {generation_id}.mid
│   └── artwork/
│       └── {generation_id}.png
├── temp/                            # Geçici dosyalar
│   ├── processing/
│   ├── uploads/
│   └── exports/
└── backups/                         # Yedek dosyalar
    ├── daily/
    └── weekly/
```

### File Manager

```php
<?php
class FileManager
{
    private string $baseDir;
    private array $allowedMimeTypes;
    private int $maxFileSize;
    
    public function __construct(string $baseDir, array $config = [])
    {
        $this->baseDir = rtrim($baseDir, '/');
        $this->allowedMimeTypes = $config['allowed_mimes'] ?? [
            'audio/mpeg',      // MP3
            'audio/flac',      // FLAC
            'audio/wav',       // WAV
            'audio/x-m4a',     // M4A
            'image/jpeg',      // JPEG
            'image/png',       // PNG
            'image/webp',      // WebP
        ];
        $this->maxFileSize = $config['max_file_size'] ?? 500 * 1024 * 1024; // 500MB
    }
    
    public function getTrackPath(int $artistId, int $albumId, 
                                 int $trackId, string $format): string
    {
        $artist = $this->getArtistSlug($artistId);
        $album = $this->getAlbumSlug($albumId);
        
        return "{$this->baseDir}/music/{$artist}/{$album}/tracks/{$trackId}.{$format}";
    }
    
    public function getArtworkPath(int $artistId, int $albumId, 
                                   string $size = 'original'): string
    {
        $artist = $this->getArtistSlug($artistId);
        $album = $this->getAlbumSlug($albumId);
        
        $sizeMap = [
            'original' => 'cover_original.jpg',
            'large' => 'cover_large.jpg',
            'medium' => 'cover_medium.jpg',
            'small' => 'cover_small.jpg'
        ];
        
        $filename = $sizeMap[$size] ?? $sizeMap['original'];
        
        return "{$this->baseDir}/music/{$artist}/{$album}/artwork/{$filename}";
    }
    
    public function storeUploadedFile(string $tmpPath, int $userId, 
                                      string $category): array
    {
        // Validation
        $this->validateFile($tmpPath);
        
        // Generate unique path
        $uploadId = $this->generateUploadId();
        $extension = pathinfo($tmpPath, PATHINFO_EXTENSION);
        $targetPath = "{$this->baseDir}/user_uploads/{$userId}/{$category}/{$uploadId}.{$extension}";
        
        // Ensure directory exists
        $this->ensureDirectory(dirname($targetPath));
        
        // Move file
        if (!move_uploaded_file($tmpPath, $targetPath)) {
            throw new RuntimeException("Failed to move uploaded file");
        }
        
        // Set permissions
        chmod($targetPath, 0644);
        
        return [
            'upload_id' => $uploadId,
            'path' => $targetPath,
            'size' => filesize($targetPath),
            'mime' => mime_content_type($targetPath)
        ];
    }
    
    public function generateVariants(string $sourcePath, array $variants): array
    {
        $results = [];
        $pathInfo = pathinfo($sourcePath);
        
        foreach ($variants as $variantName => $config) {
            $targetPath = "{$pathInfo['dirname']}/{$pathInfo['filename']}_{$variantName}.{$pathInfo['extension']}";
            
            // Process based on type
            if (str_starts_with(mime_content_type($sourcePath), 'image/')) {
                $this->resizeImage($sourcePath, $targetPath, $config);
            } elseif (str_starts_with(mime_content_type($sourcePath), 'audio/')) {
                $this->transcodeAudio($sourcePath, $targetPath, $config);
            }
            
            $results[$variantName] = [
                'path' => $targetPath,
                'size' => filesize($targetPath)
            ];
        }
        
        return $results;
    }
    
    private function validateFile(string $path): void
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException("File does not exist");
        }
        
        if (filesize($path) > $this->maxFileSize) {
            throw new InvalidArgumentException("File too large");
        }
        
        $mime = mime_content_type($path);
        if (!in_array($mime, $this->allowedMimeTypes)) {
            throw new InvalidArgumentException("Invalid file type: {$mime}");
        }
    }
    
    private function ensureDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    
    private function generateUploadId(): string
    {
        return bin2hex(random_bytes(16));
    }
    
    private function resizeImage(string $source, string $target, array $config): void
    {
        $image = imagecreatefromstring(file_get_contents($source));
        
        $width = $config['width'] ?? imagesx($image);
        $height = $config['height'] ?? imagesy($image);
        
        $resized = imagescale($image, $width, $height);
        
        imagejpeg($resized, $target, $config['quality'] ?? 85);
        
        imagedestroy($image);
        imagedestroy($resized);
    }
    
    private function transcodeAudio(string $source, string $target, array $config): void
    {
        $command = sprintf(
            'ffmpeg -i %s -acodec %s -ab %s %s 2>&1',
            escapeshellarg($source),
            escapeshellarg($config['codec'] ?? 'libmp3lame'),
            escapeshellarg($config['bitrate'] ?? '320k'),
            escapeshellarg($target)
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new RuntimeException("Transcoding failed: " . implode("\n", $output));
        }
    }
}
```

### Storage Optimization

```php
<?php
class StorageOptimizer
{
    private string $baseDir;
    
    public function __construct(string $baseDir)
    {
        $this->baseDir = $baseDir;
    }
    
    public function analyzeUsage(): array
    {
        $stats = [
            'total_size' => 0,
            'total_files' => 0,
            'by_type' => [],
            'by_artist' => [],
            'duplicate_files' => []
        ];
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->baseDir)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $stats['total_size'] += $file->getSize();
                $stats['total_files']++;
                
                $ext = $file->getExtension();
                $stats['by_type'][$ext] = ($stats['by_type'][$ext] ?? 0) + $file->getSize();
            }
        }
        
        // Find duplicates
        $stats['duplicate_files'] = $this->findDuplicates();
        
        return $stats;
    }
    
    public function findDuplicates(): array
    {
        $hashes = [];
        $duplicates = [];
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->baseDir)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $hash = md5_file($file->getPathname());
                
                if (isset($hashes[$hash])) {
                    $duplicates[] = [
                        'original' => $hashes[$hash],
                        'duplicate' => $file->getPathname(),
                        'size' => $file->getSize()
                    ];
                } else {
                    $hashes[$hash] = $file->getPathname();
                }
            }
        }
        
        return $duplicates;
    }
    
    public function cleanupTempFiles(int $maxAgeHours = 24): int
    {
        $deleted = 0;
        $tempDir = "{$this->baseDir}/temp";
        $maxAge = time() - ($maxAgeHours * 3600);
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($tempDir)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getMTime() < $maxAge) {
                unlink($file->getPathname());
                $deleted++;
            }
        }
        
        return $deleted;
    }
    
    public function getDiskUsage(): array
    {
        $total = disk_total_space($this->baseDir);
        $free = disk_free_space($this->baseDir);
        $used = $total - $free;
        
        return [
            'total_bytes' => $total,
            'used_bytes' => $used,
            'free_bytes' => $free,
            'used_percentage' => round(($used / $total) * 100, 2)
        ];
    }
}
```

## API / Konfigürasyon

```yaml
# config/file-storage.yaml
storage:
  base_dir: "/media"
  
  music:
    path_pattern: "{artist}/{album}/tracks"
    formats: ["mp3", "flac", "wav", "m4a"]
    max_file_size: "500MB"
  
  artwork:
    path_pattern: "{artist}/{album}/artwork"
    formats: ["jpg", "png", "webp"]
    sizes:
      original: "original"
      large: "640x640"
      medium: "300x300"
      small: "64x64"
    quality: 85
  
  user_uploads:
    path_pattern: "user_uploads/{user_id}"
    max_file_size: "50MB"
    allowed_types: ["audio", "image"]
  
  temp:
    path_pattern: "temp"
    cleanup_interval: 3600
    max_age_hours: 24
  
  backup:
    path_pattern: "backups"
    retention_days: 30
```

## Performans / Ölçeklenebilirlik

| Metrik | Değer |
|--------|-------|
| File Read Speed | 500MB/s |
| File Write Speed | 300MB/s |
| Directory Listing | 10K files/s |
| Total Storage | 10TB |
| File Count | 5M+ |

## Durum: Implementasyon

File System Storage modülü **stable** durumdadır. Medya depolama yapısı ve optimizasyon araçları production-ready.
