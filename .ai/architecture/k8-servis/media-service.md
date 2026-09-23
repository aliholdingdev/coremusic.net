---
title: "Media Service - Medya Servisi"
layer: K8
category: "Servis"
date: "2026-09-20"
version: "1.0.0"
status: "implemented"
dependencies:
  - K7-Repository
  - K5-Cache
  - K9-AI
---

# Media Service - Medya Servisi

## Genel Bakış

Media Service, COREMUSIC'in medya yönetimi merkezidir. Müzik kütüphanesi organizasyonu, metadata yönetimi, albüm kapakları, sanatçı bilgileri ve medya dosyalarının lifecycle yönetimini üstlenir. Servis, yerel dosya sistemi ve bulut depolama entegrasyonu ile çalışır.

Servis, otomatik metadata extraction (FFmpeg, TagLib), inteligent album art management ve medya dosyalarının indexed/searchable hale getirilmesi gibi kritik işlevleri yönetir. Medya kütüphanesi real-time olarak güncellenir ve değişiklikler anlık olarak indexlenir.

## Servis Arayüzü

### Library Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/library/tracks` | GET | Parçaları listele |
| `/api/v1/library/tracks/{id}` | GET | Parça detayı |
| `/api/v1/library/tracks` | POST | Parça ekle |
| `/api/v1/library/tracks/{id}` | PUT | Parça güncelle |
| `/api/v1/library/tracks/{id}` | DELETE | Parça sil |
| `/api/v1/library/tracks/{id}/metadata` | GET | Metadata al |
| `/api/v1/library/tracks/{id}/metadata` | PUT | Metadata güncelle |
| `/api/v1/library/albums` | GET | Albümleri listele |
| `/api/v1/library/albums/{id}` | GET | Albüm detayı |
| `/api/v1/library/artists` | GET | Sanatçıları listele |
| `/api/v1/library/artists/{id}` | GET | Sanatçı detayı |
| `/api/v1/library/genres` | GET | Türleri listele |
| `/api/v1/library/playlists` | GET | Playlistleri listele |
| `/api/v1/library/playlists` | POST | Playlist oluştur |
| `/api/v1/library/playlists/{id}` | PUT | Playlist güncelle |
| `/api/v1/library/playlists/{id}` | DELETE | Playlist sil |

### Artwork Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/artwork/{id}` | GET | Albüm kapağını al |
| `/api/v1/artwork/{id}` | PUT | Albüm kapağını güncelle |
| `/api/v1/artwork/{id}` | DELETE | Albüm kapağını sil |
| `/api/v1/artwork/search` | GET | Kapak ara |

### File Management Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/files/scan` | POST | Kütüphaneyi tara |
| `/api/v1/files/import` | POST | Dosya içe aktar |
| `/api/v1/files/export` | POST | Dosya dışa aktar |
| `/api/v1/files/convert` | POST | Format dönüştür |
| `/api/v1/files/analyze` | POST | Dosya analiz et |

## Teknik Detaylar

### Media Data Model

```cpp
// K8/media-service/include/MediaModels.h
namespace CoreMusic::K8::Media {

struct Track {
    std::string id;
    std::string filePath;
    std::string title;
    std::string artist;
    std::string album;
    int trackNumber;
    int discNumber;
    int year;
    std::string genre;
    int durationMs;
    int bitrate;
    int sampleRate;
    int channels;
    AudioFormat format;
    std::string codec;
    int64_t fileSize;
    std::string checksum;
    std::string artworkId;
    nlohmann::json extendedMetadata;
    std::chrono::system_clock::time_point addedAt;
    std::chrono::system_clock::time_point lastPlayedAt;
    int playCount;
    int rating;                 // 0-5
    bool isFavorite;
    bool isAvailable;
};

enum class AudioFormat {
    FLAC,
    MP3,
    AAC,
    WAV,
    DSD,
    ALAC,
    OGG,
    OPUS,
    AIFF,
    WMA,
    Unknown
};

struct Album {
    std::string id;
    std::string title;
    std::string artist;
    int year;
    std::string genre;
    std::string artworkId;
    std::vector<std::string> trackIds;
    int totalDurationMs;
    int trackCount;
    std::string mbid;           // MusicBrainz ID
    nlohmann::json metadata;
    std::chrono::system_clock::time_point addedAt;
};

struct Artist {
    std::string id;
    std::string name;
    std::string artworkId;
    std::vector<std::string> albumIds;
    std::string biography;
    std::string mbid;
    nlohmann::json externalIds;  // Spotify, Apple Music IDs
    nlohmann::json metadata;
};

struct Playlist {
    std::string id;
    std::string name;
    std::string description;
    std::string userId;
    std::vector<std::string> trackIds;
    bool isPublic;
    bool isCollaborative;
    std::string artworkId;
    int totalDurationMs;
    int trackCount;
    std::chrono::system_clock::time_point createdAt;
    std::chrono::system_clock::time_point updatedAt;
};

} // namespace CoreMusic::K8::Media
```

### Metadata Extraction Service

```cpp
// K8/media-service/src/MetadataExtractor.cpp
namespace CoreMusic::K8::Media {

class MetadataExtractor {
public:
    struct ExtractionResult {
        bool success;
        Track track;
        std::vector<std::string> warnings;
        std::string errorMessage;
    };

    ExtractionResult extractFromFile(const std::string& filePath) {
        ExtractionResult result;
        result.success = false;

        try {
            // TagLib ile metadata çıkarma
            TagLib::FileRef fileRef(filePath.c_str());
            if (!fileRef.isNull()) {
                auto tag = fileRef.tag();
                if (tag) {
                    result.track.title = tag->title().to8Bit(true);
                    result.track.artist = tag->artist().to8Bit(true);
                    result.track.album = tag->album().to8Bit(true);
                    result.track.year = tag->year();
                    result.track.trackNumber = tag->track();
                    result.track.genre = tag->genre().to8Bit(true);
                }

                auto audioProps = fileRef.audioProperties();
                if (audioProps) {
                    result.track.durationMs = audioProps->length() * 1000;
                    result.track.bitrate = audioProps->bitrate();
                    result.track.sampleRate = audioProps->sampleRate();
                    result.track.channels = audioProps->channels();
                }
            }

            // FFmpeg ile format bilgisi
            AVFormatContext* formatContext = nullptr;
            if (avformat_open_input(&formatContext, filePath.c_str(),
                                    nullptr, nullptr) == 0) {
                avformat_find_stream_info(formatContext, nullptr);

                auto codec = formatContext->streams[0]->codecpar;
                result.track.codec = avcodec_get_name(codec->codec_id);
                result.track.format = mapCodecToFormat(codec->codec_id);
                result.track.fileSize = getFileSize(filePath);
                result.track.checksum = calculateChecksum(filePath);

                avformat_close_input(&formatContext);
            }

            // Embedded artwork çıkarma
            extractArtwork(filePath, result.track);

            result.success = true;

        } catch (const std::exception& e) {
            result.errorMessage = e.what();
        }

        return result;
    }

    void extractArtwork(const std::string& filePath, Track& track) {
        TagLib::FileRef fileRef(filePath.c_str());
        if (fileRef.isNull()) return;

        auto tag = fileRef.tag();
        if (!tag) return;

        auto pictures = tag->pictures();
        if (!pictures.empty()) {
            auto& picture = pictures.front();
            if (picture.mimeType().startsWith("image/")) {
                Artwork artwork;
                artwork.data = std::vector<uint8_t>(
                    picture.data().begin(), picture.data().end());
                artwork.mimeType = picture.mimeType().to8Bit(true);
                artwork.description = picture.description().to8Bit(true);

                track.artworkId = saveArtwork(artwork);
            }
        }
    }

private:
    AudioFormat mapCodecToFormat(AVCodecID codecId) {
        switch (codecId) {
            case AV_CODEC_ID_FLAC: return AudioFormat::FLAC;
            case AV_CODEC_ID_MP3: return AudioFormat::MP3;
            case AV_CODEC_ID_AAC: return AudioFormat::AAC;
            case AV_CODEC_ID_PCM_S16LE: return AudioFormat::WAV;
            case AV_CODEC_ID_DSD_MSBF: return AudioFormat::DSD;
            case AV_CODEC_ID_ALAC: return AudioFormat::ALAC;
            case AV_CODEC_ID_VORBIS: return AudioFormat::OGG;
            case AV_CODEC_ID_OPUS: return AudioFormat::OPUS;
            case AV_CODEC_ID_PCM_S16BE: return AudioFormat::AIFF;
            default: return AudioFormat::Unknown;
        }
    }
};

} // namespace CoreMusic::K8::Media
```

### Library Scanner Service

```cpp
// K8/media-service/src/LibraryScanner.cpp
namespace CoreMusic::K8::Media {

class LibraryScanner {
public:
    struct ScanResult {
        int totalFiles;
        int importedFiles;
        int updatedFiles;
        int skippedFiles;
        int errorFiles;
        std::vector<std::string> errors;
        std::chrono::milliseconds duration;
    };

    struct ScanProgress {
        std::string currentFile;
        int filesScanned;
        int totalFiles;
        int percentComplete;
        bool isComplete;
    };

    using ProgressCallback = std::function<void(const ScanProgress&)>;

    LibraryScanner(std::shared_ptr<IMediaRepository> mediaRepo,
                   std::shared_ptr<MetadataExtractor> extractor)
        : mediaRepo_(std::move(mediaRepo))
        , extractor_(std::move(extractor)) {}

    ScanResult scanDirectory(const std::string& directory,
                            ProgressCallback progressCallback = nullptr) {
        ScanResult result{};
        auto startTime = std::chrono::steady_clock::now();

        // Desteklenen formatları tara
        std::vector<std::string> extensions = {
            ".flac", ".mp3", ".aac", ".wav", ".dsf", ".dff",
            ".alac", ".ogg", ".opus", ".aiff", ".m4a"
        };

        auto files = getFilesInDirectory(directory, extensions);
        result.totalFiles = files.size();

        for (size_t i = 0; i < files.size(); ++i) {
            const auto& file = files[i];

            // Progress güncelleme
            if (progressCallback) {
                progressCallback({
                    file,
                    static_cast<int>(i + 1),
                    result.totalFiles,
                    static_cast<int>((i + 1) * 100 / result.totalFiles),
                    false
                });
            }

            try {
                // Dosya zaten var mı kontrol et
                auto existing = mediaRepo_->findByFilePath(file);
                if (existing) {
                    // Dosya değişmiş mi kontrol et
                    if (isFileModified(file, *existing)) {
                        auto extracted = extractor_->extractFromFile(file);
                        if (extracted.success) {
                            extracted.track.id = existing->id;
                            mediaRepo_->save(extracted.track);
                            result.updatedFiles++;
                        }
                    } else {
                        result.skippedFiles++;
                    }
                } else {
                    // Yeni dosya
                    auto extracted = extractor_->extractFromFile(file);
                    if (extracted.success) {
                        mediaRepo_->save(extracted.track);
                        result.importedFiles++;
                    } else {
                        result.errorFiles++;
                        result.errors.push_back(
                            file + ": " + extracted.errorMessage);
                    }
                }
            } catch (const std::exception& e) {
                result.errorFiles++;
                result.errors.push_back(file + ": " + e.what());
            }
        }

        auto endTime = std::chrono::steady_clock::now();
        result.duration = std::chrono::duration_cast<std::chrono::milliseconds>(
            endTime - startTime);

        // Progress tamamlandı
        if (progressCallback) {
            progressCallback({"", result.totalFiles, result.totalFiles, 100, true});
        }

        return result;
    }

private:
    std::shared_ptr<IMediaRepository> mediaRepo_;
    std::shared_ptr<MetadataExtractor> extractor_;

    bool isFileModified(const std::string& filePath, const Track& existing) {
        auto lastWrite = std::filesystem::last_write_time(filePath);
        auto lastWriteTime = std::chrono::file_clock::to_sys(lastWrite);
        return lastWriteTime > existing.addedAt;
    }
};

} // namespace CoreMusic::K8::Media
```

### Album Art Manager

```cpp
// K8/media-service/src/AlbumArtManager.cpp
namespace CoreMusic::K8::Media {

class AlbumArtManager {
public:
    struct ArtworkResult {
        bool success;
        std::string artworkId;
        std::string path;
        std::vector<uint8_t> data;
        std::string mimeType;
        int width;
        int height;
    };

    ArtworkResult getArtwork(const std::string& trackId) {
        auto track = mediaRepo_->findById(trackId);
        if (!track) {
            return {false, "", "", {}, "", 0, 0};
        }

        // Önce cache'den kontrol et
        auto cached = cache_->get<std::vector<uint8_t>>(
            "artwork:" + track->artworkId);
        if (cached) {
            return {true, track->artworkId, "", *cached, "image/jpeg", 0, 0};
        }

        // Dosya sisteminden al
        auto artwork = loadArtworkFromFile(track->filePath);
        if (artwork.success) {
            cache_->set("artwork:" + track->artworkId, artwork.data, 3600s);
        }

        return artwork;
    }

    ArtworkResult getArtworkForAlbum(const std::string& albumId) {
        auto album = mediaRepo_->findAlbumById(albumId);
        if (!album || !album->artworkId) {
            return {false, "", "", {}, "", 0, 0};
        }

        return getArtworkByArtworkId(*album->artworkId);
    }

    std::string saveArtwork(const std::vector<uint8_t>& data,
                           const std::string& mimeType) {
        std::string artworkId = generateArtworkId();
        std::string path = getArtworkPath(artworkId, mimeType);

        // Dosyaya kaydet
        std::ofstream file(path, std::ios::binary);
        file.write(reinterpret_cast<const char*>(data.data()), data.size());
        file.close();

        // Cache'e ekle
        cache_->set("artwork:" + artworkId, data, 3600s);

        return artworkId;
    }

    ArtworkResult searchArtwork(const std::string& query) {
        // Online artwork arama (MusicBrainz, Last.fm)
        auto results = searchOnlineArtwork(query);
        if (!results.empty()) {
            return downloadArtwork(results[0].url);
        }

        return {false, "", "", {}, "", 0, 0};
    }

private:
    std::shared_ptr<IMediaRepository> mediaRepo_;
    std::shared_ptr<ICacheManager> cache_;

    std::string getArtworkPath(const std::string& artworkId,
                              const std::string& mimeType) {
        std::string ext = mimeTypeToExtension(mimeType);
        return "/var/coremusic/artworks/" + artworkId + ext;
    }

    std::string mimeTypeToExtension(const std::string& mimeType) {
        if (mimeType == "image/jpeg") return ".jpg";
        if (mimeType == "image/png") return ".png";
        if (mimeType == "image/webp") return ".webp";
        return ".jpg";
    }
};

} // namespace CoreMusic::K8::Media
```

## Bağımlılıklar

| Servis/Bileşen | Bağımlılık Tipi | Açıklama |
|----------------|-----------------|----------|
| K7-Repository | Data Access | Track, Album, Artist repository |
| K5-Cache | Infrastructure | Metadata ve artwork caching |
| K9-AI | Service | Otomatik tag suggestion |
| FFmpeg | External | Metadata extraction |
| TagLib | External | ID3/Vorbis tag reading |

## Performance Metrikleri

| Metrik | Hedef | Açıklama |
|--------|-------|----------|
| Metadata Extraction | < 50ms | Dosya başına |
| Library Scan | 1000 files/min | Tarama hızı |
| Artwork Load | < 100ms | Kapak yükleme |
| Search Query | < 200ms | Arama süresi |
| Cache Hit Ratio | > 85% | Önbellek isabet oranı |

## Durum: Implementasyon

- **Core Library Management**: ✅ Tamamlandı
- **Metadata Extraction**: ✅ Tamamlandı
- **Artwork Management**: ✅ Tamamlandı
- **Library Scanner**: ✅ Tamamlandı
- **Online Metadata**: 🔄 Devam ediyor
