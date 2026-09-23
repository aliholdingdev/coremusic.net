---
title: "Download Service - İndirme Servisi"
layer: K8
category: "Servis"
date: "2026-09-20"
version: "1.0.0"
status: "implemented"
dependencies:
  - K5-Storage
  - K6-Network
  - K7-Repository
---

# Download Service - İndirme Servisi

## Genel Bakış

Download Service, COREMUSIC medya dosyalarının indirme işlemlerini yönetir. Dosya indirme, ilerleme takibi, queue yönetimi ve download history gibi tüm download lifecycle'ı bu servis tarafından kontrol edilir. Servis, chunked download, resume support ve parallel download capabilities sunar.

Servis, various source'lerden (bulut depolama, HTTP URL'leri, P2P, streaming services) dosya indirme işlemlerini koordine eder. Retry logic, bandwidth throttling ve integrity verification gibi kritik özellikler entegre edilmiştir.

## Servis Arayüzü

### Download Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/downloads` | GET | Tüm indirmeleri listele |
| `/api/v1/downloads/{id}` | GET | İndirme detayı |
| `/api/v1/downloads` | POST | Yeni indirme başlat |
| `/api/v1/downloads/{id}/pause` | POST | İndirmeyi duraklat |
| `/api/v1/downloads/{id}/resume` | POST | İndirmeyi devam ettir |
| `/api/v1/downloads/{id}/cancel` | POST | İndirmeyi iptal et |
| `/api/v1/downloads/{id}/retry` | POST | İndirmeyi yeniden dene |
| `/api/v1/downloads/{id}/progress` | GET | İlerleme durumu |
| `/api/v1/downloads/queue` | GET | İndirme kuyruğu |
| `/api/v1/downloads/queue` | POST | Kuyruğa ekle |
| `/api/v1/downloads/history` | GET | İndirme geçmişi |
| `/api/v1/downloads/cleanup` | POST | Tamamlanan indirmeleri temizle |

### Batch Operations Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/downloads/batch` | POST | Toplu indirme başlat |
| `/api/v1/downloads/batch/{id}/pause` | POST | Toplu duraklat |
| `/api/v1/downloads/batch/{id}/cancel` | POST | Toplu iptal |
| `/api/v1/downloads/batch/{id}/status` | GET | Toplu durum |

## Teknik Detaylar

### Download Data Model

```cpp
// K8/download-service/include/DownloadModels.h
namespace CoreMusic::K8::Download {

struct DownloadTask {
    std::string id;
    std::string batchId;
    std::string url;
    std::string destinationPath;
    std::string filename;
    DownloadStatus status;
    DownloadProgress progress;
    DownloadSource source;
    DownloadOptions options;
    std::vector<DownloadAttempt> attempts;
    std::chrono::system_clock::time_point createdAt;
    std::chrono::system_clock::time_point startedAt;
    std::chrono::system_clock::time_point completedAt;
    std::chrono::system_clock::time_point lastActivityAt;
    std::string errorMessage;
    std::string checksum;
    int64_t fileSize;
    int64_t downloadedBytes;
};

enum class DownloadStatus {
    Pending,
    Downloading,
    Paused,
    Completed,
    Failed,
    Cancelled,
    Verifying,
    Retrying
};

struct DownloadProgress {
    float percentage;             // 0.0 - 100.0
    int64_t bytesDownloaded;
    int64_t totalBytes;
    int bytesPerSecond;
    int estimatedTimeRemaining;   // seconds
    int activeConnections;
    std::chrono::milliseconds elapsedTime;
};

struct DownloadSource {
    SourceType type;
    std::string provider;
    std::string authToken;
    std::map<std::string, std::string> headers;
    bool supportsResume;
    bool supportsRange;
    int maxConnections;
};

enum class SourceType {
    HTTP,
    HTTPS,
    FTP,
    S3,
    GoogleDrive,
    Dropbox,
    Spotify,
    AppleMusic,
    Tidal,
    Deezer,
    P2P
};

struct DownloadOptions {
    int maxRetries;
    int retryDelayMs;
    bool verifyIntegrity;
    bool overwriteExisting;
    bool createDirectories;
    int connectionTimeout;
    int readTimeout;
    int maxBandwidth;              // bytes per second, 0 = unlimited
    std::vector<std::string> allowedExtensions;
    std::vector<std::string> blockedExtensions;
};

struct DownloadAttempt {
    int attemptNumber;
    std::chrono::system_clock::time_point startedAt;
    std::chrono::system_clock::time_point endedAt;
    std::string errorMessage;
    int64_t bytesDownloaded;
    bool success;
};

} // namespace CoreMusic::K8::Download
```

### Download Service Implementation

```cpp
// K8/download-service/src/DownloadService.cpp
namespace CoreMusic::K8::Download {

class DownloadService : public IService {
public:
    DownloadService(std::shared_ptr<IDownloadRepository> downloadRepo,
                    std::shared_ptr<IStorageManager> storage,
                    std::shared_ptr<INetworkManager> network)
        : downloadRepo_(std::move(downloadRepo))
        , storage_(std::move(storage))
        , network_(std::move(network)) {}

    void initialize() override {
        // Active downloads'ı yükle
        loadActiveDownloads();
        // Worker thread'leri başlat
        startWorkers(4);
        status_ = ServiceStatus::Running;
    }

    ServiceResult<DownloadTask> startDownload(const DownloadRequest& request) {
        // Validasyon
        if (request.url.empty()) {
            return ServiceResult<DownloadTask>::error({
                400, "URL is required", "", ServiceErrorCode::Validation
            });
        }

        // URL validation
        if (!isValidURL(request.url)) {
            return ServiceResult<DownloadTask>::error({
                400, "Invalid URL", "", ServiceErrorCode::Validation
            });
        }

        // Dosya boyutunu kontrol et (HEAD request)
        auto fileInfo = getFileInfo(request.url);
        if (!fileInfo) {
            return ServiceResult<DownloadTask>::error({
                400, "Could not retrieve file info",
                "", ServiceErrorCode::External
            });
        }

        // Disk alanı kontrolü
        auto availableSpace = storage_->getAvailableSpace(
            request.destinationPath);
        if (availableSpace < fileInfo->size) {
            return ServiceResult<DownloadTask>::error({
                507, "Insufficient disk space",
                "Required: " + std::to_string(fileInfo->size) +
                ", Available: " + std::to_string(availableSpace),
                ServiceErrorCode::Internal
            });
        }

        // Download task oluştur
        DownloadTask task;
        task.id = generateUUID();
        task.batchId = request.batchId;
        task.url = request.url;
        task.destinationPath = request.destinationPath;
        task.filename = request.filename.empty() ?
            extractFilename(request.url) : request.filename;
        task.status = DownloadStatus::Pending;
        task.source = request.source;
        task.options = request.options;
        task.fileSize = fileInfo->size;
        task.downloadedBytes = 0;
        task.createdAt = std::chrono::system_clock::now();
        task.lastActivityAt = task.createdAt;

        // Kuyruğa ekle
        downloadQueue_.enqueue(task);

        // Kaydet
        downloadRepo_->save(task);

        // Event publish
        publishEvent(DownloadQueuedEvent{task.id, task.url});

        return ServiceResult<DownloadTask>::success(task);
    }

    ServiceResult<void> pauseDownload(const std::string& downloadId) {
        auto task = downloadRepo_->findById(downloadId);
        if (!task) {
            return ServiceResult<void>::error({
                404, "Download not found", "", ServiceErrorCode::NotFound
            });
        }

        if (task->status != DownloadStatus::Downloading) {
            return ServiceResult<void>::error({
                400, "Download is not active",
                "", ServiceErrorCode::Validation
            });
        }

        // Download'ı duraklat
        cancelDownload(*task);
        task->status = DownloadStatus::Paused;
        downloadRepo_->save(*task);

        return ServiceResult<void>::success(std::monostate{});
    }

    ServiceResult<void> resumeDownload(const std::string& downloadId) {
        auto task = downloadRepo_->findById(downloadId);
        if (!task) {
            return ServiceResult<void>::error({
                404, "Download not found", "", ServiceErrorCode::NotFound
            });
        }

        if (task->status != DownloadStatus::Paused) {
            return ServiceResult<void>::error({
                400, "Download is not paused",
                "", ServiceErrorCode::Validation
            });
        }

        // Resume edilebilirliği kontrol et
        if (!task->source.supportsResume) {
            // Baştan başlat
            task->downloadedBytes = 0;
        }

        task->status = DownloadStatus::Pending;
        downloadQueue_.enqueue(*task);
        downloadRepo_->save(*task);

        return ServiceResult<void>::success(std::monostate{});
    }

    ServiceResult<void> cancelDownload(const std::string& downloadId) {
        auto task = downloadRepo_->findById(downloadId);
        if (!task) {
            return ServiceResult<void>::error({
                404, "Download not found", "", ServiceErrorCode::NotFound
            });
        }

        // Download'ı iptal et
        task->status = DownloadStatus::Cancelled;
        task->completedAt = std::chrono::system_clock::now();
        downloadRepo_->save(*task);

        // Partial dosyayı temizle
        storage_->deleteFile(getTempPath(*task));

        publishEvent(DownloadCancelledEvent{task->id});

        return ServiceResult<void>::success(std::monostate{});
    }

    ServiceResult<DownloadProgress> getProgress(const std::string& downloadId) {
        auto task = downloadRepo_->findById(downloadId);
        if (!task) {
            return ServiceResult<DownloadProgress>::error({
                404, "Download not found", "", ServiceErrorCode::NotFound
            });
        }

        DownloadProgress progress;
        progress.percentage = task->fileSize > 0 ?
            (static_cast<float>(task->downloadedBytes) / task->fileSize) * 100.0f : 0.0f;
        progress.bytesDownloaded = task->downloadedBytes;
        progress.totalBytes = task->fileSize;
        progress.bytesPerSpeed = calculateSpeed(*task);
        progress.estimatedTimeRemaining = calculateETA(*task);
        progress.elapsedTime = std::chrono::duration_cast<std::chrono::milliseconds>(
            std::chrono::system_clock::now() - task->startedAt);

        return ServiceResult<DownloadProgress>::success(progress);
    }

private:
    std::shared_ptr<IDownloadRepository> downloadRepo_;
    std::shared_ptr<IStorageManager> storage_;
    std::shared_ptr<INetworkManager> network_;
    ThreadSafeQueue<DownloadTask> downloadQueue_;
    std::vector<std::thread> workerThreads_;

    void startWorkers(int count) {
        for (int i = 0; i < count; ++i) {
            workerThreads_.emplace_back([this]() {
                workerLoop();
            });
        }
    }

    void workerLoop() {
        while (running_) {
            DownloadTask task;
            if (downloadQueue_.tryDequeue(task)) {
                executeDownload(task);
            } else {
                std::this_thread::sleep_for(std::chrono::milliseconds(100));
            }
        }
    }

    void executeDownload(DownloadTask& task) {
        task.status = DownloadStatus::Downloading;
        task.startedAt = std::chrono::system_clock::now();
        downloadRepo_->save(task);

        try {
            // Range header ile resume
            std::map<std::string, std::string> headers;
            if (task.downloadedBytes > 0 && task.source.supportsResume) {
                headers["Range"] = "bytes=" +
                    std::to_string(task.downloadedBytes) + "-";
            }

            // Streaming download
            auto response = network_->streamDownload(
                task.url, headers,
                [&](const std::vector<uint8_t>& chunk) {
                    // Chunk'ı yaz
                    appendToFile(getTempPath(task), chunk);
                    task.downloadedBytes += chunk.size();
                    task.lastActivityAt = std::chrono::system_clock::now();
                    downloadRepo_->save(task);

                    // Progress callback
                    publishEvent(DownloadProgressEvent{
                        task.id, getProgress(task)
                    });
                });

            // Integrity kontrolü
            if (task.options.verifyIntegrity) {
                task.status = DownloadStatus::Verifying;
                downloadRepo_->save(task);

                auto actualChecksum = calculateChecksum(getTempPath(task));
                if (actualChecksum != task.checksum) {
                    throw std::runtime_error("Checksum mismatch");
                }
            }

            // Temp dosyasını final konumuna taşı
            moveFile(getTempPath(task),
                    task.destinationPath + "/" + task.filename);

            // Tamamlandı
            task.status = DownloadStatus::Completed;
            task.completedAt = std::chrono::system_clock::now();
            downloadRepo_->save(task);

            publishEvent(DownloadCompletedEvent{task.id, task.filename});

        } catch (const std::exception& e) {
            handleDownloadError(task, e.what());
        }
    }

    void handleDownloadError(DownloadTask& task, const std::string& error) {
        task.errorMessage = error;
        task.attempts.push_back({
            static_cast<int>(task.attempts.size()) + 1,
            task.startedAt,
            std::chrono::system_clock::now(),
            error,
            task.downloadedBytes,
            false
        });

        // Retry check
        if (task.attempts.size() < task.options.maxRetries) {
            task.status = DownloadStatus::Retrying;
            downloadRepo_->save(task);

            // Retry queue'ya ekle
            std::this_thread::sleep_for(
                std::chrono::milliseconds(task.options.retryDelayMs));
            downloadQueue_.enqueue(task);
        } else {
            task.status = DownloadStatus::Failed;
            downloadRepo_->save(task);

            publishEvent(DownloadFailedEvent{task.id, error});
        }
    }
};

} // namespace CoreMusic::K8::Download
```

### Batch Download Manager

```cpp
// K8/download-service/src/BatchDownloadManager.cpp
namespace CoreMusic::K8::Download {

class BatchDownloadManager {
public:
    struct BatchRequest {
        std::string batchId;
        std::string name;
        std::vector<DownloadRequest> downloads;
        BatchOptions options;
    };

    struct BatchOptions {
        int maxConcurrent;
        bool stopOnError;
        bool preserveStructure;
        std::string basePath;
    };

    struct BatchStatus {
        std::string batchId;
        std::string name;
        int totalDownloads;
        int completedDownloads;
        int failedDownloads;
        int activeDownloads;
        int pendingDownloads;
        float overallProgress;
        std::chrono::milliseconds elapsedTime;
        std::vector<DownloadTask> tasks;
    };

    ServiceResult<BatchStatus> startBatch(const BatchRequest& request) {
        BatchStatus status;
        status.batchId = request.batchId.empty() ?
            generateUUID() : request.batchId;
        status.name = request.name;
        status.totalDownloads = request.downloads.size();
        status.completedDownloads = 0;
        status.failedDownloads = 0;
        status.activeDownloads = 0;
        status.pendingDownloads = request.downloads.size();

        // Her download'ı başlat
        for (const auto& download : request.downloads) {
            DownloadRequest req = download;
            req.batchId = status.batchId;

            auto result = downloadService_->startDownload(req);
            if (!result.isSuccess()) {
                status.failedDownloads++;
                status.pendingDownloads--;
            }

            // Max concurrent kontrolü
            while (status.activeDownloads >= request.options.maxConcurrent) {
                std::this_thread::sleep_for(std::chrono::milliseconds(100));
                updateBatchStatus(status);
            }
        }

        return ServiceResult<BatchStatus>::success(status);
    }

    ServiceResult<void> pauseBatch(const std::string& batchId) {
        auto tasks = downloadRepo_->findByBatchId(batchId);

        for (auto& task : tasks) {
            if (task.status == DownloadStatus::Downloading) {
                downloadService_->pauseDownload(task.id);
            }
        }

        return ServiceResult<void>::success(std::monostate{});
    }

    ServiceResult<void> cancelBatch(const std::string& batchId) {
        auto tasks = downloadRepo_->findByBatchId(batchId);

        for (auto& task : tasks) {
            if (task.status != DownloadStatus::Completed) {
                downloadService_->cancelDownload(task.id);
            }
        }

        return ServiceResult<void>::success(std::monostate{});
    }

private:
    std::shared_ptr<DownloadService> downloadService_;
    std::shared_ptr<IDownloadRepository> downloadRepo_;

    void updateBatchStatus(BatchStatus& status) {
        auto tasks = downloadRepo_->findByBatchId(status.batchId);

        status.completedDownloads = 0;
        status.failedDownloads = 0;
        status.activeDownloads = 0;
        status.pendingDownloads = 0;

        for (const auto& task : tasks) {
            switch (task.status) {
                case DownloadStatus::Completed:
                    status.completedDownloads++;
                    break;
                case DownloadStatus::Failed:
                case DownloadStatus::Cancelled:
                    status.failedDownloads++;
                    break;
                case DownloadStatus::Downloading:
                    status.activeDownloads++;
                    break;
                default:
                    status.pendingDownloads++;
                    break;
            }
        }

        status.overallProgress =
            status.totalDownloads > 0 ?
            (static_cast<float>(status.completedDownloads) /
             status.totalDownloads) * 100.0f : 0.0f;
    }
};

} // namespace CoreMusic::K8::Download
```

## Bağımlılıklar

| Servis/Bileşen | Bağımlılık Tipi | Açıklama |
|----------------|-----------------|----------|
| K5-Storage | Infrastructure | Dosya sistemi erişimi |
| K6-Network | Infrastructure | HTTP/FTP network |
| K7-Repository | Data Access | Download task verileri |
| K5-Cache | Infrastructure | Download metadata cache |

## Durum: Implementasyon

- **Core Download**: ✅ Tamamlandı
- **Resume Support**: ✅ Tamamlandı
- **Batch Downloads**: ✅ Tamamlandı
- **Parallel Downloads**: 🔄 Devam ediyor
- **Integrity Verification**: ✅ Tamamlandı
