---
title: "Audio Service - Ses Servisi"
layer: K8
category: "Servis"
date: "2026-09-20"
version: "1.0.0"
status: "implemented"
dependencies:
  - K3-AudioEngine
  - K6-Queue
  - K7-Repository
---

# Audio Service - Ses Servisi

## Genel Bakış

Audio Service, COREMUSIC'in ses çalma kontrolünün merkezi noktasıdır. Playback control, queue management, session handling ve ses kalitesi optimizasyonu bu servis tarafından yönetilir. Servis, K3 Audio Engine ile doğrudan entegre çalışarak low-latency ses işleme sağlar.

Servis, multi-room audio playback, gapless transitions, crossfade effects ve real-time equalizer ayarlamalarını koordine eder. Kullanıcı deneyimini iyileştirmek için session persistence, playback history ve personalized queue management özelliklerini sunar.

## Servis Arayüzü

### Playback Control Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/playback/play` | POST | Çalmaya başla |
| `/api/v1/playback/pause` | POST | Duraklat |
| `/api/v1/playback/stop` | POST | Durdur |
| `/api/v1/playback/next` | POST | Sonraki parça |
| `/api/v1/playback/previous` | POST | Önceki parça |
| `/api/v1/playback/seek` | POST | Konuma atla |
| `/api/v1/playback/position` | GET | Mevcut konum |
| `/api/v1/playback/status` | GET | Çalma durumu |
| `/api/v1/playback/volume` | PUT | Ses seviyesi |
| `/api/v1/playback/mute` | POST | Sessiz |
| `/api/v1/playback/shuffle` | PUT | Karıştır |
| `/api/v1/playback/repeat` | PUT | Tekrar modu |

### Queue Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/queue` | GET | Kuyruğu al |
| `/api/v1/queue` | POST | Kuyruğa ekle |
| `/api/v1/queue/{position}` | DELETE | Kuyruktan çıkar |
| `/api/v1/queue/{position}` | PUT | Kuyruğu düzenle |
| `/api/v1/queue/clear` | DELETE | Kuyruğu temizle |
| `/api/v1/queue/{position}/move` | PUT | Sırayı değiştir |
| `/api/v1/queue/history` | GET | Çalma geçmişi |

### Session Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/sessions` | GET | Oturumları listele |
| `/api/v1/sessions/active` | GET | Aktif oturum |
| `/api/v1/sessions/create` | POST | Yeni oturum |
| `/api/v1/sessions/{id}` | DELETE | Oturumu sonlandır |
| `/api/v1/sessions/{id}/state` | GET | Oturum durumu |
| `/api/v1/sessions/{id}/state` | PUT | Oturum durumunu güncelle |

## Teknik Detaylar

### Playback State Model

```cpp
// K8/audio-service/include/PlaybackModels.h
namespace CoreMusic::K8::Audio {

enum class PlaybackState {
    Stopped,
    Playing,
    Paused,
    Buffering,
    Error,
    Seeking,
    Loading
};

enum class RepeatMode {
    Off,
    One,
    All,
    Queue
};

struct PlaybackStatus {
    PlaybackState state;
    std::string currentTrackId;
    int64_t positionMs;
    int64_t durationMs;
    float volume;               // 0.0 - 1.0
    bool isMuted;
    bool isShuffled;
    RepeatMode repeatMode;
    float playbackSpeed;        // 0.5 - 2.0
    std::string outputDevice;
    AudioQuality quality;
};

struct AudioQuality {
    int sampleRate;
    int bitDepth;
    int channels;
    std::string codec;
    int bitrate;
    bool isLossless;
};

struct QueueItem {
    std::string trackId;
    std::string addedBy;
    std::chrono::system_clock::time_point addedAt;
    int position;
    bool isCurrentlyPlaying;
    PlaySource source;
};

enum class PlaySource {
    User,
    AutoPlay,
    Recommendation,
    Radio,
    PartyMode
};

} // namespace CoreMusic::K8::Audio
```

### Audio Service Implementation

```cpp
// K8/audio-service/src/AudioService.cpp
namespace CoreMusic::K8::Audio {

class AudioService : public IService {
public:
    AudioService(std::shared_ptr<IAudioEngine> engine,
                 std::shared_ptr<IQueueManager> queueMgr,
                 std::shared_ptr<ISessionManager> sessionMgr)
        : engine_(std::move(engine))
        , queueMgr_(std::move(queueMgr))
        , sessionMgr_(std::move(sessionMgr)) {}

    void initialize() override {
        engine_->initialize();
        sessionMgr_->initialize();
        status_ = ServiceStatus::Running;
    }

    // Playback Control
    ServiceResult<void> play(const PlayRequest& request) {
        auto session = sessionMgr_->getActiveSession();
        if (!session) {
            return ServiceResult<void>::error({
                400, "No active session", "", ServiceErrorCode::Validation
            });
        }

        if (request.trackId) {
            // Belirli bir parçayı çal
            auto track = mediaRepo_->findById(*request.trackId);
            if (!track) {
                return ServiceResult<void>::error({
                    404, "Track not found", "", ServiceErrorCode::NotFound
                });
            }

            queueMgr_->setCurrentTrack(*request.trackId);
            engine_->loadTrack(*track);
        }

        if (request.positionMs) {
            engine_->seekTo(*request.positionMs);
        }

        engine_->play();
        updatePlaybackState(PlaybackState::Playing);

        // Event publish
        publishEvent(PlaybackStartedEvent{
            queueMgr_->getCurrentTrack(),
            session->id
        });

        return ServiceResult<void>::success(std::monostate{});
    }

    ServiceResult<void> pause() {
        engine_->pause();
        updatePlaybackState(PlaybackState::Paused);
        return ServiceResult<void>::success(std::monostate{});
    }

    ServiceResult<void> stop() {
        engine_->stop();
        updatePlaybackState(PlaybackState::Stopped);
        return ServiceResult<void>::success(std::monostate{});
    }

    ServiceResult<void> next() {
        auto nextTrack = queueMgr_->next();
        if (!nextTrack) {
            engine_->stop();
            return ServiceResult<void>::success(std::monostate{});
        }

        auto track = mediaRepo_->findById(*nextTrack);
        if (track) {
            engine_->loadTrack(*track);
            engine_->play();
            updatePlaybackState(PlaybackState::Playing);
        }

        return ServiceResult<void>::success(std::monostate{});
    }

    ServiceResult<void> previous() {
        // 3 saniyeden fazla geçmişse başa sar, değilse önceki parçaya geç
        if (engine_->getPosition() > 3000) {
            engine_->seekTo(0);
            return ServiceResult<void>::success(std::monostate{});
        }

        auto prevTrack = queueMgr_->previous();
        if (!prevTrack) {
            engine_->seekTo(0);
            return ServiceResult<void>::success(std::monostate{});
        }

        auto track = mediaRepo_->findById(*prevTrack);
        if (track) {
            engine_->loadTrack(*track);
            engine_->play();
            updatePlaybackState(PlaybackState::Playing);
        }

        return ServiceResult<void>::success(std::monostate{});
    }

    ServiceResult<void> seek(int64_t positionMs) {
        if (positionMs < 0 || positionMs > engine_->getDuration()) {
            return ServiceResult<void>::error({
                400, "Invalid seek position", "", ServiceErrorCode::Validation
            });
        }

        engine_->seekTo(positionMs);
        return ServiceResult<void>::success(std::monostate{});
    }

    ServiceResult<void> setVolume(float volume) {
        if (volume < 0.0f || volume > 1.0f) {
            return ServiceResult<void>::error({
                400, "Volume must be between 0.0 and 1.0",
                "", ServiceErrorCode::Validation
            });
        }

        engine_->setVolume(volume);
        publishEvent(VolumeChangedEvent{volume});
        return ServiceResult<void>::success(std::monostate{});
    }

    ServiceResult<void> setShuffle(bool enabled) {
        queueMgr_->setShuffle(enabled);
        publishEvent(ShuffleChangedEvent{enabled});
        return ServiceResult<void>::success(std::monostate{});
    }

    ServiceResult<void> setRepeatMode(RepeatMode mode) {
        queueMgr_->setRepeatMode(mode);
        publishEvent(RepeatModeChangedEvent{mode});
        return ServiceResult<void>::success(std::monostate{});
    }

    // Queue Management
    ServiceResult<QueueState> addToQueue(const AddToQueueRequest& request) {
        QueueItem item;
        item.trackId = request.trackId;
        item.addedBy = request.userId;
        item.addedAt = std::chrono::system_clock::now();
        item.source = PlaySource::User;

        if (request.position) {
            queueMgr_->insertAt(*request.position, item);
        } else {
            queueMgr_->addToEnd(item);
        }

        return ServiceResult<QueueState>::success(queueMgr_->getState());
    }

    ServiceResult<QueueState> moveInQueue(int fromPos, int toPos) {
        if (fromPos < 0 || toPos < 0) {
            return ServiceResult<QueueState>::error({
                400, "Invalid position", "", ServiceErrorCode::Validation
            });
        }

        queueMgr_->move(fromPos, toPos);
        return ServiceResult<QueueState>::success(queueMgr_->getState());
    }

    ServiceResult<void> clearQueue() {
        queueMgr_->clear();
        return ServiceResult<void>::success(std::monostate{});
    }

    // Session Management
    ServiceResult<Session> createSession(const CreateSessionRequest& request) {
        Session session;
        session.id = generateUUID();
        session.userId = request.userId;
        session.deviceId = request.deviceId;
        session.createdAt = std::chrono::system_clock::now();
        session.state = PlaybackState::Stopped;
        session.volume = 0.8f;

        sessionMgr_->createSession(session);
        return ServiceResult<Session>::success(session);
    }

private:
    std::shared_ptr<IAudioEngine> engine_;
    std::shared_ptr<IQueueManager> queueMgr_;
    std::shared_ptr<ISessionManager> sessionMgr_;
    std::shared_ptr<IMediaRepository> mediaRepo_;
    ServiceStatus status_ = ServiceStatus::Uninitialized;

    void updatePlaybackState(PlaybackState state) {
        auto session = sessionMgr_->getActiveSession();
        if (session) {
            session->state = state;
            sessionMgr_->updateSession(*session);
        }
    }
};

} // namespace CoreMusic::K8::Audio
```

### Queue Manager

```cpp
// K8/audio-service/src/QueueManager.cpp
namespace CoreMusic::K8::Audio {

class QueueManager : public IQueueManager {
public:
    QueueState getState() const override {
        QueueState state;
        state.items = items_;
        state.currentIndex = currentIndex_;
        state.totalItems = items_.size();
        state.isShuffled = isShuffled_;
        state.repeatMode = repeatMode_;

        if (!items_.empty() && currentIndex_ >= 0) {
            state.currentTrackId = items_[currentIndex_].trackId;
        }

        return state;
    }

    void addToEnd(const QueueItem& item) override {
        items_.push_back(item);
    }

    void insertAt(int position, const QueueItem& item) override {
        if (position >= 0 && position <= static_cast<int>(items_.size())) {
            items_.insert(items_.begin() + position, item);
            if (position <= currentIndex_) {
                currentIndex_++;
            }
        }
    }

    std::optional<std::string> next() override {
        if (items_.empty()) return std::nullopt;

        switch (repeatMode_) {
            case RepeatMode::One:
                return items_[currentIndex_].trackId;

            case RepeatMode::All:
                currentIndex_ = (currentIndex_ + 1) % items_.size();
                return items_[currentIndex_].trackId;

            case RepeatMode::Queue:
                if (currentIndex_ + 1 < items_.size()) {
                    currentIndex_++;
                    return items_[currentIndex_].trackId;
                }
                return std::nullopt;

            default: // Off
                if (currentIndex_ + 1 < items_.size()) {
                    currentIndex_++;
                    return items_[currentIndex_].trackId;
                }
                return std::nullopt;
        }
    }

    std::optional<std::string> previous() override {
        if (items_.empty()) return std::nullopt;

        if (currentIndex_ > 0) {
            currentIndex_--;
        } else if (repeatMode_ == RepeatMode::All) {
            currentIndex_ = items_.size() - 1;
        }

        return items_[currentIndex_].trackId;
    }

    void setCurrentTrack(const std::string& trackId) override {
        for (int i = 0; i < static_cast<int>(items_.size()); ++i) {
            if (items_[i].trackId == trackId) {
                currentIndex_ = i;
                return;
            }
        }
    }

    void setShuffle(bool enabled) override {
        if (enabled == isShuffled_) return;

        if (enabled) {
            // Shuffle uygula
            std::string currentTrackId;
            if (currentIndex_ >= 0) {
                currentTrackId = items_[currentIndex_].trackId;
            }

            std::shuffle(items_.begin(), items_.end(), rng_);

            // Mevcut parçayı bul
            if (!currentTrackId.empty()) {
                for (int i = 0; i < static_cast<int>(items_.size()); ++i) {
                    if (items_[i].trackId == currentTrackId) {
                        currentIndex_ = i;
                        break;
                    }
                }
            }
        } else {
            // Shuffle'ı kaldır
            std::string currentTrackId;
            if (currentIndex_ >= 0) {
                currentTrackId = items_[currentIndex_].trackId;
            }

            // Orijinal sıraya dön
            std::stable_sort(items_.begin(), items_.end(),
                [](const QueueItem& a, const QueueItem& b) {
                    return a.position < b.position;
                });

            // Mevcut parçayı bul
            if (!currentTrackId.empty()) {
                for (int i = 0; i < static_cast<int>(items_.size()); ++i) {
                    if (items_[i].trackId == currentTrackId) {
                        currentIndex_ = i;
                        break;
                    }
                }
            }
        }

        isShuffled_ = enabled;
    }

    void setRepeatMode(RepeatMode mode) override {
        repeatMode_ = mode;
    }

    void move(int from, int to) override {
        if (from < 0 || from >= items_.size() ||
            to < 0 || to >= items_.size()) {
            return;
        }

        auto item = items_[from];
        items_.erase(items_.begin() + from);
        items_.insert(items_.begin() + to, item);

        // currentIndex güncelleme
        if (from == currentIndex_) {
            currentIndex_ = to;
        } else if (from < currentIndex_ && to >= currentIndex_) {
            currentIndex_--;
        } else if (from > currentIndex_ && to <= currentIndex_) {
            currentIndex_++;
        }
    }

    void clear() override {
        items_.clear();
        currentIndex_ = -1;
    }

private:
    std::vector<QueueItem> items_;
    int currentIndex_ = -1;
    bool isShuffled_ = false;
    RepeatMode repeatMode_ = RepeatMode::Off;
    std::mt19937 rng_{std::random_device{}()};
};

} // namespace CoreMusic::K8::Audio
```

### Session Manager

```cpp
// K8/audio-service/src/SessionManager.cpp
namespace CoreMusic::K8::Audio {

class SessionManager : public ISessionManager {
public:
    void createSession(const Session& session) override {
        sessions_[session.id] = session;
        activeSessionId_ = session.id;
    }

    std::optional<Session> getActiveSession() const override {
        auto it = sessions_.find(activeSessionId_);
        if (it != sessions_.end()) {
            return it->second;
        }
        return std::nullopt;
    }

    void updateSession(const Session& session) override {
        sessions_[session.id] = session;
    }

    void deleteSession(const std::string& sessionId) override {
        sessions_.erase(sessionId);
        if (activeSessionId_ == sessionId) {
            activeSessionId_.clear();
        }
    }

    std::vector<Session> getAllSessions() const override {
        std::vector<Session> result;
        for (const auto& [id, session] : sessions_) {
            result.push_back(session);
        }
        return result;
    }

    void saveSessionState(const std::string& sessionId) {
        auto it = sessions_.find(sessionId);
        if (it != sessions_.end()) {
            // Session state'i persist et
            saveToDatabase(it->second);
        }
    }

    void restoreSession(const std::string& sessionId) {
        auto session = loadFromDatabase(sessionId);
        if (session) {
            sessions_[session->id] = *session;
            activeSessionId_ = session->id;
        }
    }

private:
    std::unordered_map<std::string, Session> sessions_;
    std::string activeSessionId_;
};

} // namespace CoreMusic::K8::Audio
```

## Bağımlılıklar

| Servis/Bileşen | Bağımlılık Tipi | Açıklama |
|----------------|-----------------|----------|
| K3-AudioEngine | Core | Ses işleme ve çalma |
| K6-Queue | Infrastructure | Mesaj kuyruğu |
| K7-Repository | Data Access | Playback history, sessions |
| K5-Cache | Infrastructure | Session state caching |

## Durum: Implementasyon

- **Playback Control**: ✅ Tamamlandı
- **Queue Management**: ✅ Tamamlandı
- **Session Management**: ✅ Tamamlandı
- **Multi-Room Sync**: 🔄 Devam ediyor
- **Gapless Playback**: ✅ Tamamlandı
