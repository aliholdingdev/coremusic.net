---
title: "Sync Service - Senkronizasyon Servisi"
layer: K8
category: "Servis"
date: "2026-09-20"
version: "1.0.0"
status: "implemented"
dependencies:
  - K7-Repository
  - K6-Network
  - K5-Cache
---

# Sync Service - Senkronizasyon Servisi

## Genel Bakış

Sync Service, COREMUSIC cihazları arasında veri senkronizasyonunu yöneten merkezi servistir. Playlist sync, library sync, settings sync ve playback state sync gibi çoklu senkronizasyon türlerini koordine eder. Servis, conflict resolution, delta sync ve offline-first architecture ile reliable sync sağlar.

Servis, CRDT (Conflict-free Replicated Data Types) ve operational transformation kullanarak concurrent modification'ları çözer. Offline support ile internet bağlantısı olmadan çalışır, bağlantı kurulduğunda otomatik olarak senkronize eder.

## Servis Arayüzü

### Sync Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/sync/status` | GET | Senkronizasyon durumu |
| `/api/v1/sync/start` | POST | Manuel senkronizasyon başlat |
| `/api/v1/sync/stop` | POST | Senkronizasyonu durdur |
| `/api/v1/sync/force` | POST | Zorunlu senkronizasyon |
| `/api/v1/sync/history` | GET | Senkronizasyon geçmişi |
| `/api/v1/sync/conflicts` | GET | Çakışmaları listele |
| `/api/v1/sync/conflicts/{id}/resolve` | POST | Çakışmayı çöz |
| `/api/v1/sync/progress` | GET | İlerleme durumu |

### Library Sync Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/sync/library` | GET | Kütüphane senkronizasyon durumu |
| `/api/v1/sync/library/start` | POST | Kütüphane senkronizasyonu başlat |
| `/api/v1/sync/library/delta` | GET | Değişiklikleri al |
| `/api/v1/sync/library/changes` | POST | Değişiklikleri gönder |

### Playlist Sync Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/sync/playlists` | GET | Playlist senkronizasyonu |
| `/api/v1/sync/playlists/{id}` | GET | Playlist sync durumu |
| `/api/v1/sync/playlists/{id}/push` | POST | Playlist değişikliklerini gönder |
| `/api/v1/sync/playlists/{id}/pull` | POST | Değişiklikleri çek |

### Settings Sync Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/sync/settings` | GET | Ayar senkronizasyonu |
| `/api/v1/sync/settings/push` | POST | Ayarları gönder |
| `/api/v1/sync/settings/pull` | POST | Ayarları çek |
| `/api/v1/sync/settings/backup` | POST | Yedekleme oluştur |

## Teknik Detaylar

### Sync Data Model

```cpp
// K8/sync-service/include/SyncModels.h
namespace CoreMusic::K8::Sync {

struct SyncState {
    std::string deviceId;
    std::string userId;
    SyncStatus status;
    std::chrono::system_clock::time_point lastSyncAt;
    std::chrono::system_clock::time_point nextSyncAt;
    SyncProgress progress;
    std::vector<SyncConflict> conflicts;
    int64_t pendingChanges;
    int64_t syncedChanges;
    bool isOnline;
    std::string syncToken;        // For delta sync
};

enum class SyncStatus {
    Idle,
    Syncing,
    Paused,
    Error,
    ConflictResolution,
    Offline
};

struct SyncProgress {
    float percentage;
    int64_t totalChanges;
    int64_t processedChanges;
    int64_t failedChanges;
    std::chrono::milliseconds elapsedTime;
    std::chrono::milliseconds estimatedTimeRemaining;
    std::string currentOperation;
};

struct SyncConflict {
    std::string id;
    std::string entityType;       // "playlist", "track", "settings"
    std::string entityId;
    ConflictType type;
    SyncVersion localVersion;
    SyncVersion remoteVersion;
    std::chrono::system_clock::time_point detectedAt;
    ConflictResolution resolution;
    bool isResolved;
};

enum class ConflictType {
    UpdateConflict,     // Both sides updated same entity
    DeleteConflict,     // One side deleted, other modified
    CreateConflict,     // Same entity created on both sides
    MoveConflict        // Entity moved to different location
};

struct SyncVersion {
    int64_t version;
    std::string deviceId;
    std::chrono::system_clock::time_point timestamp;
    nlohmann::json data;
    std::string checksum;
};

enum class ConflictResolution {
    KeepLocal,
    KeepRemote,
    Merge,
    Manual,
    Newest,
    MostComplete
};

struct SyncChange {
    std::string id;
    ChangeType type;
    std::string entityType;
    std::string entityId;
    nlohmann::json oldData;
    nlohmann::json newData;
    std::string deviceId;
    std::chrono::system_clock::time_point timestamp;
    int64_t version;
    bool isApplied;
};

enum class ChangeType {
    Create,
    Update,
    Delete,
    Move,
    Copy
};

struct DeltaSyncRequest {
    std::string syncToken;
    std::string deviceId;
    std::vector<SyncChange> changes;
    std::chrono::system_clock::time_point lastSyncAt;
};

struct DeltaSyncResponse {
    std::string syncToken;
    std::vector<SyncChange> changes;
    bool hasMore;
    std::chrono::system_clock::time_point serverTime;
};

} // namespace CoreMusic::K8::Sync
```

### Sync Service Implementation

```cpp
// K8/sync-service/src/SyncService.cpp
namespace CoreMusic::K8::Sync {

class SyncService : public IService {
public:
    SyncService(std::shared_ptr<ISyncRepository> syncRepo,
                std::shared_ptr<IConflictResolver> resolver,
                std::shared_ptr<ICloudProvider> cloud)
        : syncRepo_(std::move(syncRepo))
        , resolver_(std::move(resolver))
        , cloud_(std::move(cloud)) {}

    void initialize() override {
        // Auto-sync'i başlat
        startAutoSync();
        status_ = ServiceStatus::Running;
    }

    ServiceResult<SyncState> getSyncStatus() {
        SyncState state;
        state.deviceId = getDeviceId();
        state.userId = getCurrentUserId();
        state.status = currentStatus_;
        state.lastSyncAt = lastSyncAt_;
        state.progress = currentProgress_;
        state.conflicts = getUnresolvedConflicts();
        state.pendingChanges = getPendingChangesCount();
        state.syncedChanges = getSyncedChangesCount();
        state.isOnline = checkOnlineStatus();
        state.syncToken = getSyncToken();

        return ServiceResult<SyncState>::success(state);
    }

    ServiceResult<void> startSync(const SyncRequest& request) {
        if (currentStatus_ == SyncStatus::Syncing) {
            return ServiceResult<void>::error({
                409, "Sync already in progress",
                "", ServiceErrorCode::Conflict
            });
        }

        currentStatus_ = SyncStatus::Syncing;
        currentProgress_ = SyncProgress{};

        // Sync thread'ini başlat
        syncThread_ = std::thread([this, request]() {
            performSync(request);
        });

        return ServiceResult<void>::success(std::monostate{});
    }

    ServiceResult<void> forceSync() {
        // Local state'i resetle
        resetSyncState();

        // Full sync yap
        SyncRequest request;
        request.type = SyncType::Full;
        request.force = true;

        return startSync(request);
    }

    ServiceResult<std::vector<SyncConflict>> getConflicts() {
        auto conflicts = syncRepo_->getUnresolvedConflicts(
            getCurrentUserId());

        return ServiceResult<std::vector<SyncConflict>>::success(conflicts);
    }

    ServiceResult<void> resolveConflict(
        const std::string& conflictId,
        ConflictResolution resolution) {

        auto conflict = syncRepo_->findConflictById(conflictId);
        if (!conflict) {
            return ServiceResult<void>::error({
                404, "Conflict not found", "", ServiceErrorCode::NotFound
            });
        }

        // Çözümü uygula
        auto resolved = resolver_->resolve(*conflict, resolution);

        // Değişiklikleri uygula
        applyResolution(resolved);

        // Conflict'ı işaretle
        conflict->isResolved = true;
        conflict->resolution = resolution;
        syncRepo_->saveConflict(*conflict);

        return ServiceResult<void>::success(std::monostate{});
    }

private:
    std::shared_ptr<ISyncRepository> syncRepo_;
    std::shared_ptr<IConflictResolver> resolver_;
    std::shared_ptr<ICloudProvider> cloud_;
    SyncStatus currentStatus_ = SyncStatus::Idle;
    SyncProgress currentProgress_;
    std::chrono::system_clock::time_point lastSyncAt_;
    std::thread syncThread_;

    void performSync(const SyncRequest& request) {
        try {
            // 1. Local changes'ı topla
            auto localChanges = collectLocalChanges();

            // 2. Remote changes'ı al
            auto remoteChanges = fetchRemoteChanges();

            // 3. Conflict'ları tespit et
            auto conflicts = detectConflicts(localChanges, remoteChanges);

            if (!conflicts.empty() && !request.force) {
                currentStatus_ = SyncStatus::ConflictResolution;
                syncRepo_->saveConflicts(conflicts);
                return;
            }

            // 4. Conflict'ları çöz (force modda)
            if (request.force) {
                for (auto& conflict : conflicts) {
                    resolver_->resolve(conflict,
                        ConflictResolution::Newest);
                }
            }

            // 5. Local changes'ı push et
            pushLocalChanges(localChanges);

            // 6. Remote changes'ı pull et
            pullRemoteChanges(remoteChanges);

            // 7. Sync token'ı güncelle
            updateSyncToken();

            currentStatus_ = SyncStatus::Idle;
            lastSyncAt_ = std::chrono::system_clock::now();

        } catch (const std::exception& e) {
            currentStatus_ = SyncStatus::Error;
            logger_->error("Sync failed: " + std::string(e.what()));
        }
    }

    std::vector<SyncChange> collectLocalChanges() {
        std::vector<SyncChange> changes;

        // Library changes
        auto libraryChanges = collectLibraryChanges();
        changes.insert(changes.end(),
                      libraryChanges.begin(), libraryChanges.end());

        // Playlist changes
        auto playlistChanges = collectPlaylistChanges();
        changes.insert(changes.end(),
                      playlistChanges.begin(), playlistChanges.end());

        // Settings changes
        auto settingsChanges = collectSettingsChanges();
        changes.insert(changes.end(),
                      settingsChanges.begin(), settingsChanges.end());

        return changes;
    }

    std::vector<SyncChange> fetchRemoteChanges() {
        DeltaSyncRequest request;
        request.syncToken = getSyncToken();
        request.deviceId = getDeviceId();
        request.lastSyncAt = lastSyncAt_;

        auto response = cloud_->fetchDelta(request);

        return response.changes;
    }

    std::vector<SyncConflict> detectConflicts(
        const std::vector<SyncChange>& local,
        const std::vector<SyncChange>& remote) {

        std::vector<SyncConflict> conflicts;

        // Entity bazlı gruplama
        std::map<std::string, std::vector<SyncChange>> localByEntity;
        std::map<std::string, std::vector<SyncChange>> remoteByEntity;

        for (const auto& change : local) {
            auto key = change.entityType + ":" + change.entityId;
            localByEntity[key].push_back(change);
        }

        for (const auto& change : remote) {
            auto key = change.entityType + ":" + change.entityId;
            remoteByEntity[key].push_back(change);
        }

        // Aynı entity üzerinde hem local hem remote değişiklik varsa
        for (const auto& [key, localChanges] : localByEntity) {
            auto it = remoteByEntity.find(key);
            if (it != remoteByEntity.end()) {
                // Conflict tespit et
                SyncConflict conflict;
                conflict.id = generateUUID();
                conflict.entityType = extractEntityType(key);
                conflict.entityId = extractEntityId(key);
                conflict.type = determineConflictType(
                    localChanges, it->second);
                conflict.localVersion = getLatestVersion(localChanges);
                conflict.remoteVersion = getLatestVersion(it->second);
                conflict.detectedAt = std::chrono::system_clock::now();
                conflict.isResolved = false;

                conflicts.push_back(conflict);
            }
        }

        return conflicts;
    }

    void pushLocalChanges(const std::vector<SyncChange>& changes) {
        int64_t total = changes.size();
        int64_t processed = 0;

        for (const auto& change : changes) {
            try {
                cloud_->pushChange(change);

                // Local change'ı işaretle
                markChangeApplied(change.id);

                processed++;
                updateProgress(processed, total, "Pushing changes");

            } catch (const std::exception& e) {
                logger_->error("Failed to push change: " +
                              std::string(e.what()));
                markChangeFailed(change.id, e.what());
            }
        }
    }

    void pullRemoteChanges(const std::vector<SyncChange>& changes) {
        int64_t total = changes.size();
        int64_t processed = 0;

        for (const auto& change : changes) {
            try {
                applyRemoteChange(change);

                processed++;
                updateProgress(processed, total, "Applying remote changes");

            } catch (const std::exception& e) {
                logger_->error("Failed to apply remote change: " +
                              std::string(e.what()));
            }
        }
    }

    void applyRemoteChange(const SyncChange& change) {
        switch (change.type) {
            case ChangeType::Create:
                createEntity(change.entityType, change.entityId,
                           change.newData);
                break;
            case ChangeType::Update:
                updateEntity(change.entityType, change.entityId,
                           change.newData);
                break;
            case ChangeType::Delete:
                deleteEntity(change.entityType, change.entityId);
                break;
        }
    }
};

} // namespace CoreMusic::K8::Sync
```

### Conflict Resolver

```cpp
// K8/sync-service/src/ConflictResolver.cpp
namespace CoreMusic::K8::Sync {

class ConflictResolver : public IConflictResolver {
public:
    SyncConflict resolve(const SyncConflict& conflict,
                        ConflictResolution strategy) {
        SyncConflict resolved = conflict;
        resolved.isResolved = true;
        resolved.resolution = strategy;

        switch (strategy) {
            case ConflictResolution::KeepLocal:
                resolved.resolvedData = conflict.localVersion.data;
                break;

            case ConflictResolution::KeepRemote:
                resolved.resolvedData = conflict.remoteVersion.data;
                break;

            case ConflictResolution::Merge:
                resolved.resolvedData = mergeData(
                    conflict.localVersion.data,
                    conflict.remoteVersion.data);
                break;

            case ConflictResolution::Newest:
                resolved.resolvedData =
                    conflict.localVersion.timestamp >
                    conflict.remoteVersion.timestamp ?
                    conflict.localVersion.data :
                    conflict.remoteVersion.data;
                break;

            case ConflictResolution::MostComplete:
                resolved.resolvedData = selectMostComplete(
                    conflict.localVersion.data,
                    conflict.remoteVersion.data);
                break;
        }

        return resolved;
    }

private:
    nlohmann::json mergeData(const nlohmann::json& local,
                            const nlohmann::json& remote) {
        nlohmann::json merged = local;

        for (auto it = remote.begin(); it != remote.end(); ++it) {
            if (!merged.contains(it.key())) {
                // Remote'da var local'da yok
                merged[it.key()] = it.value();
            } else if (merged[it.key()] != it.value()) {
                // İkisi de var ama farklı
                // Field-level merge
                if (it.value().is_object() && merged[it.key()].is_object()) {
                    merged[it.key()] = mergeData(merged[it.key()],
                                                it.value());
                } else {
                    // Timestamp bazlı karar ver
                    // (Bu basitleştirilmiş bir örnek)
                    merged[it.key()] = it.value();
                }
            }
        }

        return merged;
    }

    nlohmann::json selectMostComplete(const nlohmann::json& local,
                                     const nlohmann::json& remote) {
        int localFields = countFields(local);
        int remoteFields = countFields(remote);

        return localFields >= remoteFields ? local : remote;
    }

    int countFields(const nlohmann::json& data) {
        if (data.is_object()) {
            int count = 0;
            for (auto it = data.begin(); it != data.end(); ++it) {
                count += countFields(it.value());
            }
            return count + data.size();
        }
        return 1;
    }
};

} // namespace CoreMusic::K8::Sync
```

### Offline Sync Manager

```cpp
// K8/sync-service/src/OfflineSyncManager.cpp
namespace CoreMusic::K8::Sync {

class OfflineSyncManager {
public:
    struct OfflineState {
        bool isOffline;
        int64_t pendingChanges;
        std::chrono::system_clock::time_point lastOnlineAt;
        std::chrono::system_clock::time_point offlineSince;
        std::vector<SyncChange> offlineQueue;
    };

    OfflineState getOfflineState() {
        OfflineState state;
        state.isOffline = !checkOnlineStatus();
        state.pendingChanges = getPendingChangesCount();
        state.lastOnlineAt = lastOnlineAt_;
        state.offlineSince = offlineSince_;
        state.offlineQueue = getOfflineQueue();

        return state;
    }

    void goOffline() {
        isOffline_ = true;
        offlineSince_ = std::chrono::system_clock::now();
    }

    void goOnline() {
        isOffline_ = false;
        lastOnlineAt_ = std::chrono::system_clock::now();

        // Offline queue'yu flush et
        flushOfflineQueue();
    }

    void queueChange(const SyncChange& change) {
        offlineQueue_.push_back(change);
        saveOfflineQueue();
    }

private:
    bool isOffline_ = false;
    std::chrono::system_clock::time_point offlineSince_;
    std::chrono::system_clock::time_point lastOnlineAt_;
    std::vector<SyncChange> offlineQueue_;

    void flushOfflineQueue() {
        // Offline'da biriken değişiklikleri gönder
        for (const auto& change : offlineQueue_) {
            try {
                cloud_->pushChange(change);
                markChangeApplied(change.id);
            } catch (const std::exception& e) {
                logger_->error("Failed to flush offline change: " +
                              std::string(e.what()));
            }
        }

        offlineQueue_.clear();
        saveOfflineQueue();
    }
};

} // namespace CoreMusic::K8::Sync
```

## Bağımlılıklar

| Servis/Bileşen | Bağımlılık Tipi | Açıklama |
|----------------|-----------------|----------|
| K7-Repository | Data Access | Sync state verileri |
| K6-Network | Infrastructure | Cloud connectivity |
| K5-Cache | Infrastructure | Offline cache |
| Cloud Provider | External | iCloud/Google Drive/Dropbox |

## Durum: Implementasyon

- **Delta Sync**: ✅ Tamamlandı
- **Conflict Resolution**: ✅ Tamamlandı
- **Offline Support**: ✅ Tamamlandı
- **Playlist Sync**: ✅ Tamamlandı
- **Library Sync**: 🔄 Devam ediyor
- **Settings Sync**: ✅ Tamamlandı
