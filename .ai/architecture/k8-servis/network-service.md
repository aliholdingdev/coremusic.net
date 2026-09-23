---
title: "Network Service - Ağ Servisi"
layer: K8
category: "Servis"
date: "2026-09-20"
version: "1.0.0"
status: "implemented"
dependencies:
  - K6-Network
  - K2-Driver
  - K7-Repository
---

# Network Service - Ağ Servisi

## Genel Bakış

Network Service, COREMUSIC'in ağ protokolü entegrasyonlarını yöneten merkezi servistir. DLNA/UPnP, AirPlay, WebRTC ve multi-room senkronizasyonu gibi tüm ağ tabanlı ses aktarım protokollerini orkestra eder. Servis, different network topologies ve protocols之间 seamless switching sağlar.

Servis, high-fidelity audio streaming için optimized protokoller kullanarak, multi-room setups'ta sample-accurate synchronization sağlar. Network latency compensation, jitter buffering ve packet loss recovery gibi kritik işlevleri yönetir.

## Servis Arayüzü

### DLNA/UPnP Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/network/dlna/renderer` | GET | DLNA renderer'ları listele |
| `/api/v1/network/dlna/renderer/start` | POST | DLNA renderer başlat |
| `/api/v1/network/dlna/renderer/stop` | POST | DLNA renderer durdur |
| `/api/v1/network/dlna/control` | POST | DLNA kontrol |
| `/api/v1/network/dlna/transport` | GET | Transport durumu |
| `/api/v1/network/dlna/AVT` | POST | AVTransport control |

### AirPlay Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/network/airplay/status` | GET | AirPlay durumu |
| `/api/v1/network/airplay/streams` | GET | Aktif AirPlay stream'leri |
| `/api/v1/network/airplay/sync` | POST | AirPlay senkronizasyonu |
| `/api/v1/network/airplay/volume` | PUT | AirPlay ses kontrolü |
| `/api/v1/network/airplay/metadata` | PUT | Metadata gönder |

### WebRTC Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/network/webrtc/signal` | POST | WebRTC signaling |
| `/api/v1/network/webrtc/offer` | POST | SDP offer |
| `/api/v1/network/webrtc/answer` | POST | SDP answer |
| `/api/v1/network/webrtc/candidate` | POST | ICE candidate |
| `/api/v1/network/webrtc/stream` | GET | Aktif stream'ler |

### Multi-Room Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/network/multiroom/groups` | GET | Oda gruplarını listele |
| `/api/v1/network/multiroom/groups` | POST | Yeni grup oluştur |
| `/api/v1/network/multiroom/groups/{id}` | PUT | Grubu güncelle |
| `/api/v1/network/multiroom/groups/{id}` | DELETE | Grubu sil |
| `/api/v1/network/multiroom/groups/{id}/devices` | POST | Cihaz ekle |
| `/api/v1/network/multiroom/groups/{id}/devices/{deviceId}` | DELETE | Cihaz çıkar |
| `/api/v1/network/multiroom/sync` | POST | Senkronizasyon başlat |
| `/api/v1/network/multiroom/volume` | PUT | Grup ses kontrolü |

## Teknik Detaylar

### Network Protocol Abstraction

```cpp
// K8/network-service/include/NetworkProtocol.h
namespace CoreMusic::K8::Network {

class INetworkProtocol {
public:
    virtual ~INetworkProtocol() = default;

    virtual void initialize() = 0;
    virtual void start() = 0;
    virtual void stop() = 0;
    virtual bool isConnected() const = 0;

    virtual ServiceResult<void> sendAudio(
        const AudioBuffer& buffer,
        const StreamConfig& config) = 0;

    virtual ServiceResult<void> setVolume(float volume) = 0;
    virtual ServiceResult<void> setMute(bool muted) = 0;
    virtual ServiceResult<void> setMetadata(const TrackMetadata& metadata) = 0;

    virtual ProtocolStatus getStatus() const = 0;
    virtual std::string getProtocolName() const = 0;
};

struct ProtocolStatus {
    bool isConnected;
    int latencyMs;
    int jitterMs;
    float packetLossRate;
    int64_t bytesTransferred;
    int64_t durationMs;
    std::string errorMessage;
};

struct StreamConfig {
    int sampleRate;
    int bitDepth;
    int channels;
    int bufferSize;             // bytes
    bool useCompression;
    bool enableSync;
};

} // namespace CoreMusic::K8::Network
```

### DLNA Renderer Service

```cpp
// K8/network-service/src/DlnaRenderer.cpp
namespace CoreMusic::K8::Network {

class DlnaRenderer : public INetworkProtocol {
public:
    DlnaRenderer(const DlnaConfig& config) : config_(config) {}

    void initialize() override {
        // UPnP stack başlat
        upnpStack_ = std::make_unique<UPnPStack>();

        // AVTransport service
        avtService_ = std::make_unique<AVTransportService>();
        avtService_->setActionCallback(
            [this](const std::string& action,
                   const nlohmann::json& params) {
                return handleAVTAction(action, params);
            });

        // RenderingControl service
        rcService_ = std::make_unique<RenderingControlService>();
        rcService_->setActionCallback(
            [this](const std::string& action,
                   const nlohmann::json& params) {
                return handleRCAction(action, params);
            });

        // ConnectionManager service
        cmService_ = std::make_unique<ConnectionManagerService>();

        // SSDP announcement
        upnpStack_->registerDevice(config_.deviceDescription);
    }

    void start() override {
        upnpStack_->start();
        isRunning_ = true;
    }

    void stop() override {
        upnpStack_->stop();
        isRunning_ = false;
    }

    ServiceResult<void> sendAudio(const AudioBuffer& buffer,
                                 const StreamConfig& config) override {
        if (!isConnected_) {
            return ServiceResult<void>::error({
                503, "Not connected", "", ServiceErrorCode::External
            });
        }

        // DLNA audio streaming
        auto url = getStreamingURL();
        auto headers = getStreamingHeaders(config);

        // Chunked transfer encoding
        for (const auto& chunk : buffer.getChunks()) {
            auto response = httpClient_->post(url, chunk, headers);
            if (response.statusCode != 200) {
                return ServiceResult<void>::error({
                    response.statusCode, "Streaming failed",
                    response.body, ServiceErrorCode::External
                });
            }
        }

        return ServiceResult<void>::success(std::monostate{});
    }

    ServiceResult<void> setVolume(float volume) override {
        nlohmann::json params = {
            {"InstanceID", 0},
            {"Channel", "Master"},
            {"DesiredVolume", static_cast<int>(volume * 100)}
        };

        auto result = rcService_->callAction("SetVolume", params);
        return result.success ?
            ServiceResult<void>::success(std::monostate{}) :
            ServiceResult<void>::error(result.error);
    }

private:
    DlnaConfig config_;
    std::unique_ptr<UPnPStack> upnpStack_;
    std::unique_ptr<AVTransportService> avtService_;
    std::unique_ptr<RenderingControlService> rcService_;
    std::unique_ptr<ConnectionManagerService> cmService_;
    bool isRunning_ = false;
    bool isConnected_ = false;

    nlohmann::json handleAVTAction(const std::string& action,
                                   const nlohmann::json& params) {
        if (action == "Play") {
            startPlayback();
            return {{"Result", "OK"}};
        }
        if (action == "Pause") {
            pausePlayback();
            return {{"Result", "OK"}};
        }
        if (action == "Stop") {
            stopPlayback();
            return {{"Result", "OK"}};
        }
        if (action == "SetAVTransportURI") {
            setTransportURI(params["CurrentURI"]);
            return {{"Result", "OK"}};
        }
        if (action == "GetTransportInfo") {
            return getTransportInfo();
        }
        return {{"Error", "Unknown action"}};
    }
};

} // namespace CoreMusic::K8::Network
```

### AirPlay Receiver Service

```cpp
// K8/network-service/src/AirPlayReceiver.cpp
namespace CoreMusic::K8::Network {

class AirPlayReceiver : public INetworkProtocol {
public:
    AirPlayReceiver(const AirPlayConfig& config) : config_(config) {}

    void initialize() override {
        // mDNS service registration
        mdnsService_ = std::make_unique<mDNSResponder>();
        mdnsService_->registerService({
            config_.deviceName,
            "_raop._tcp",
            config_.port,
            {{"txtvers", "1"},
             {"ch", "2"},
             {"cn", "0,1,2,3"},
             {"et", "0,1,2,3"},
             {"tp", "UDP"},
             {"sm", "false"},
             {"ek", "1"},
             {"pw", "false"}}
        });

        // RTSP server
        rtspServer_ = std::make_unique<RTSPServer>(config_.port);
        rtspServer_->setRequestHandler(
            [this](const RTSPRequest& request) {
                return handleRTSPRequest(request);
            });

        // AirPlay audio stream handler
        streamHandler_ = std::make_unique<AirPlayStreamHandler>();
    }

    void start() override {
        mdnsService_->start();
        rtspServer_->start();
        isRunning_ = true;
    }

    void stop() override {
        mdnsService_->stop();
        rtspServer_->stop();
        isRunning_ = false;
    }

    ServiceResult<void> sendAudio(const AudioBuffer& buffer,
                                 const StreamConfig& config) override {
        // AirPlay uses its own audio streaming protocol
        return streamHandler_->streamAudio(buffer, config);
    }

    ServiceResult<void> setMetadata(const TrackMetadata& metadata) override {
        // AirPlay metadata (via HTTP PUT to artwork URL)
        nlohmann::json meta = {
            {"daap.songname", metadata.title},
            {"daap.songartist", metadata.artist},
            {"daap.songalbum", metadata.album},
            {"daap.songalbumartist", metadata.albumArtist},
            {"daap.songtracknumber", metadata.trackNumber},
            {"daap.songdiscnumber", metadata.discNumber},
            {"daap.songyear", metadata.year},
            {"daap.songgenre", metadata.genre}
        };

        return sendMetadataUpdate(meta);
    }

private:
    AirPlayConfig config_;
    std::unique_ptr<mDNSResponder> mdnsService_;
    std::unique_ptr<RTSPServer> rtspServer_;
    std::unique_ptr<AirPlayStreamHandler> streamHandler_;
    bool isRunning_ = false;

    struct RTSPResponse {
        int statusCode;
        std::string statusText;
        std::map<std::string, std::string> headers;
        std::string body;
    };

    RTSPResponse handleRTSPRequest(const RTSPRequest& request) {
        if (request.method == "ANNOUNCE") {
            return handleAnnounce(request);
        }
        if (request.method == "SETUP") {
            return handleSetup(request);
        }
        if (request.method == "RECORD") {
            return handleRecord(request);
        }
        if (request.method == "TEARDOWN") {
            return handleTeardown(request);
        }
        if (request.method == "OPTIONS") {
            return handleOptions(request);
        }
        if (request.method == "SET_PARAMETER") {
            return handleSetParameter(request);
        }
        if (request.method == "GET_PARAMETER") {
            return handleGetParameter(request);
        }

        return {405, "Method Not Allowed", {}, ""};
    }

    RTSPResponse handleSetup(const RTSPRequest& request) {
        // Transport header parse et
        auto transport = request.headers.at("Transport");
        auto clientPort = extractClientPort(transport);

        // Audio streaming portlarını ayarla
        streamHandler_->setupStreams(clientPort);

        std::string session = generateSessionID();
        std::string transportResponse =
            "RTP/AVP/UDP;unicast;server_port=" +
            std::to_string(config_.streamingPort) +
            ";server_rtcp_port=" + std::to_string(config_.rtcpPort) +
            ";mode=record";

        return {
            200, "OK",
            {{"Transport", transportResponse},
             {"Session", session + ";timeout=60"}},
            ""
        };
    }
};

} // namespace CoreMusic::K8::Network
```

### Multi-Room Synchronization

```cpp
// K8/network-service/src/MultiRoomSync.cpp
namespace CoreMusic::K8::Network {

class MultiRoomSync {
public:
    struct SyncGroup {
        std::string groupId;
        std::string groupName;
        std::vector<std::string> deviceIds;
        std::string leaderDeviceId;
        int64_t clockOffsetMs;
        bool isSynchronized;
        SyncMode mode;
    };

    enum class SyncMode {
        PerfectSync,          // Tight sync (< 1ms)
        RoomSync,            // Room-aware sync (< 10ms)
        LooseSync,           // Best effort
        Independent          // No sync
    };

    MultiRoomSync(std::shared_ptr<IDeviceRepository> deviceRepo)
        : deviceRepo_(std::move(deviceRepo)) {}

    ServiceResult<void> createGroup(const SyncGroup& group) {
        groups_[group.groupId] = group;

        // Leader seçimi
        if (group.leaderDeviceId.empty()) {
            groups_[group.groupId].leaderDeviceId =
                selectBestLeader(group.deviceIds);
        }

        // Clock synchronization başlat
        startClockSync(group.groupId);

        // Device'ları gruba ekle
        for (const auto& deviceId : group.deviceIds) {
            addDeviceToGroup(deviceId, group.groupId);
        }

        return ServiceResult<void>::success(std::monostate{});
    }

    ServiceResult<void> synchronize(const std::string& groupId) {
        auto group = groups_.find(groupId);
        if (group == groups_.end()) {
            return ServiceResult<void>::error({
                404, "Group not found", "", ServiceErrorCode::NotFound
            });
        }

        // Precision Time Protocol (PTP) kullanarak senkronizasyon
        auto leaderTime = getDeviceTime(group->second.leaderDeviceId);

        for (const auto& deviceId : group->second.deviceIds) {
            if (deviceId != group->second.leaderDeviceId) {
                auto deviceTime = getDeviceTime(deviceId);
                auto offset = leaderTime - deviceTime;

                // NTP-style clock adjustment
                adjustDeviceClock(deviceId, offset);

                group->second.clockOffsetMs = offset;
            }
        }

        group->second.isSynchronized = true;

        // Sync confirmation
        for (const auto& deviceId : group->second.deviceIds) {
            sendSyncConfirmation(deviceId, groupId);
        }

        return ServiceResult<void>::success(std::monostate{});
    }

    ServiceResult<void> playSynchronized(const std::string& groupId,
                                        const std::string& trackId) {
        auto group = groups_.find(groupId);
        if (group == groups_.end()) {
            return ServiceResult<void>::error({
                404, "Group not found", "", ServiceErrorCode::NotFound
            });
        }

        // Leader'a komut gönder
        sendPlayCommand(group->second.leaderDeviceId, trackId);

        // Follower'lara senkronize başlangıç komutu gönder
        auto startTime = getDeviceTime(group->second.leaderDeviceId) +
                        SYNC_DELAY_MS;  // 100ms delay

        for (const auto& deviceId : group->second.deviceIds) {
            if (deviceId != group->second.leaderDeviceId) {
                sendScheduledPlayCommand(deviceId, trackId, startTime);
            }
        }

        return ServiceResult<void>::success(std::monostate{});
    }

private:
    std::shared_ptr<IDeviceRepository> deviceRepo_;
    std::unordered_map<std::string, SyncGroup> groups_;
    static constexpr int64_t SYNC_DELAY_MS = 100;

    std::string selectBestLeader(const std::vector<std::string>& deviceIds) {
        // En düşük latency'ye sahip cihazı seç
        std::string bestDevice;
        int bestLatency = std::numeric_limits<int>::max();

        for (const auto& deviceId : deviceIds) {
            auto latency = getDeviceLatency(deviceId);
            if (latency < bestLatency) {
                bestLatency = latency;
                bestDevice = deviceId;
            }
        }

        return bestDevice;
    }

    void startClockSync(const std::string& groupId) {
        // PTP (IEEE 1588) clock synchronization
        auto group = groups_[groupId];

        ptpSyncThread_ = std::thread([this, groupId, group]() {
            while (isRunning_) {
                for (const auto& deviceId : group.deviceIds) {
                    syncClockWithDevice(deviceId);
                }
                std::this_thread::sleep_for(std::chrono::milliseconds(100));
            }
        });
    }

    void syncClockWithDevice(const std::string& deviceId) {
        // PTP sync message
        PTPMessage sync;
        sync.sequenceId = nextSequenceId_++;
        sync.originTimestamp = getCurrentTimestamp();

        auto response = sendPTPMessage(deviceId, sync);
        if (response) {
            auto roundTrip = getCurrentTimestamp() - sync.originTimestamp;
            auto oneWayDelay = roundTrip / 2;
            auto clockOffset = response->receiveTimestamp -
                              sync.originTimestamp - oneWayDelay;

            adjustDeviceClock(deviceId, clockOffset);
        }
    }
};

} // namespace CoreMusic::K8::Network
```

## Bağımlılıklar

| Servis/Bileşen | Bağımlılık Tipi | Açıklama |
|----------------|-----------------|----------|
| K6-Network | Core | Ağ altyapısı |
| K2-Driver | Core | Donanım erişimi |
| K7-Repository | Data Access | Cihaz ve grup verileri |
| UPnP/DLNA | External | DLNA protokolü |
| mDNS | External | Apple Bonjour |
| PTP | External | IEEE 1588 clock sync |

## Durum: Implementasyon

- **DLNA/UPnP**: ✅ Tamamlandı
- **AirPlay Receiver**: ✅ Tamamlandı
- **WebRTC Streaming**: 🔄 Devam ediyor
- **Multi-Room Sync**: 🔄 Devam ediyor
- **Clock Synchronization**: ✅ Tamamlandı
