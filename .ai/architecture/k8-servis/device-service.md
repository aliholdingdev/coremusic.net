---
title: "Device Service - Cihaz Servisi"
layer: K8
category: "Servis"
date: "2026-09-20"
version: "1.0.0"
status: "implemented"
dependencies:
  - K2-Driver
  - K6-Network
  - K7-Repository
---

# Device Service - Cihaz Servisi

## Genel Bakış

Device Service, COREMUSIC cihaz ekosisteminin yönetim merkezidir. Cihaz keşfi (discovery), kayıt (registration), durum takibi (status monitoring) ve cihazlar arası koordinasyonu yönetir. Servis, yerel ağ ve bulut tabanlı cihaz yönetimini destekler.

Servis, UPnP/DLNA discovery, mDNS/Bonjour, Bluetooth scanning ve manual registration yöntemleri ile cihaz keşfi sağlar. Her cihaz için detaylı profil yönetimi, capability tracking ve health monitoring gerçekleştirilir.

## Servis Arayüzü

### Device Management Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/devices` | GET | Cihazları listele |
| `/api/v1/devices/{id}` | GET | Cihaz detayı |
| `/api/v1/devices` | POST | Cihaz kaydet |
| `/api/v1/devices/{id}` | PUT | Cihaz güncelle |
| `/api/v1/devices/{id}` | DELETE | Cihaz sil |
| `/api/v1/devices/{id}/status` | GET | Cihaz durumu |
| `/api/v1/devices/{id}/capabilities` | GET | Cihaz yetenekleri |
| `/api/v1/devices/{id}/settings` | GET | Cihaz ayarları |
| `/api/v1/devices/{id}/settings` | PUT | Cihaz ayarlarını güncelle |
| `/api/v1/devices/{id}/firmware` | GET | Firmware bilgisi |
| `/api/v1/devices/{id}/firmware` | POST | Firmware güncelle |

### Discovery Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/discovery/scan` | POST | Ağ taraması başlat |
| `/api/v1/discovery/stop` | POST | Taramayı durdur |
| `/api/v1/discovery/results` | GET | Tarama sonuçları |
| `/api/v1/discovery/dlna` | GET | DLNA cihazları |
| `/api/v1/discovery/mdns` | GET | mDNS cihazları |
| `/api/v1/discovery/bluetooth` | GET | Bluetooth cihazları |

### Remote Control Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/devices/{id}/remote/play` | POST | Uzaktan çalma |
| `/api/v1/devices/{id}/remote/pause` | POST | Uzaktan duraklat |
| `/api/v1/devices/{id}/remote/volume` | PUT | Uzaktan ses |
| `/api/v1/devices/{id}/remote/input` | PUT | Giriş kaynağı değiştir |
| `/api/v1/devices/{id}/remote/command` | POST | Özel komut gönder |

## Teknik Detaylar

### Device Data Model

```cpp
// K8/device-service/include/DeviceModels.h
namespace CoreMusic::K8::Device {

struct Device {
    std::string id;
    std::string name;
    std::string displayName;
    DeviceType type;
    DeviceStatus status;
    DeviceCapabilities capabilities;
    DeviceConnection connection;
    DeviceInfo info;
    DeviceSettings settings;
    std::chrono::system_clock::time_point discoveredAt;
    std::chrono::system_clock::time_point lastSeenAt;
    bool isOnline;
    int signalStrength;          // 0-100
    std::string firmwareVersion;
    std::string hardwareRevision;
};

enum class DeviceType {
    Speaker,             // Hoparlör
    Amplifier,           // Anfi
    DAC,                 // Dijital-Analog Converter
    Streamer,            // Ağ Streamer
    Headphone,           // Kulaklık
    Transport,           // CD/SACD Transport
    Server,              // Medya Sunucusu
    ControlPoint,        // Kontrol Noktası
    NetworkBridge,       // Ağ Köprüsü
    Unknown
};

enum class DeviceStatus {
    Online,
    Offline,
    Standby,
    Updating,
    Error,
    Connecting,
    Disconnected
};

struct DeviceCapabilities {
    bool supportsPlayback;
    bool supportsVolume;
    bool supportsMute;
    bool supportsEQ;
    bool supportsInput;
    bool supportsOutput;
    bool supportsAirPlay;
    bool supportsDLNA;
    bool supportsBluetooth;
    bool supportsUSB;
    bool supportsNetworkAudio;
    std::vector<AudioFormat> supportedFormats;
    std::vector<int> supportedSampleRates;
    std::vector<int> supportedBitDepths;
    int maxChannels;
    bool supportsMultiRoom;
    bool supportsRemoteControl;
};

struct DeviceConnection {
    ConnectionType type;
    std::string ipAddress;
    std::string macAddress;
    int port;
    std::string interface;
    bool isWired;
    int bandwidth;              // Mbps
    int latency;                // ms
    bool isEncrypted;
};

enum class ConnectionType {
    WiFi,
    Ethernet,
    Bluetooth,
    USB,
    Coaxial,
    Optical,
    I2S,
    AES_EBU,
    Unknown
};

struct DeviceInfo {
    std::string manufacturer;
    std::string model;
    std::string serialNumber;
    std::string firmwareVersion;
    std::string hardwareRevision;
    std::string udn;            // Unique Device Name (UPnP)
    std::string friendlyName;
    std::string modelName;
    std::string modelNumber;
    nlohmann::json additionalInfo;
};

struct DeviceSettings {
    int volumeLevel;
    bool isMuted;
    std::string inputSource;
    std::string outputMode;
    bool nightMode;
    bool loudnessControl;
    std::string eqPreset;
    int brightnessLevel;
    bool autoStandby;
    int autoStandbyMinutes;
};

} // namespace CoreMusic::K8::Device
```

### Device Discovery Service

```cpp
// K8/device-service/src/DeviceDiscovery.cpp
namespace CoreMusic::K8::Device {

class DeviceDiscovery {
public:
    struct DiscoveryResult {
        std::vector<Device> devices;
        std::chrono::milliseconds duration;
        int devicesFound;
        std::vector<std::string> errors;
    };

    using DiscoveryCallback = std::function<void(const Device&)>;

    DeviceDiscovery(std::shared_ptr<IDeviceRepository> deviceRepo)
        : deviceRepo_(std::move(deviceRepo)) {}

    void startScan(DiscoveryCallback callback) {
        isScanning_ = true;
        callback_ = std::move(callback);

        // Paralel tarama başlat
        dlnaScanner_ = std::async(std::launch::async, [this]() {
            scanDLNA();
        });

        mdnsScanner_ = std::async(std::launch::async, [this]() {
            scanMDNS();
        });

        bluetoothScanner_ = std::async(std::launch::async, [this]() {
            scanBluetooth();
        });
    }

    void stopScan() {
        isScanning_ = false;

        if (dlnaScanner_.valid()) dlnaScanner_.get();
        if (mdnsScanner_.valid()) mdnsScanner_.get();
        if (bluetoothScanner_.valid()) bluetoothScanner_.get();
    }

private:
    std::shared_ptr<IDeviceRepository> deviceRepo_;
    std::atomic<bool> isScanning_{false};
    DiscoveryCallback callback_;
    std::future<void> dlnaScanner_;
    std::future<void> mdnsScanner_;
    std::future<void> bluetoothScanner_;

    void scanDLNA() {
        // SSDP multicast tarama
        udp::socket socket(ioContext_, udp::v4());
        socket.set_option(udp::socket::reuse_address(true));

        // M-SEARCH mesajı gönder
        std::string msearch =
            "M-SEARCH * HTTP/1.1\r\n"
            "HOST: 239.255.255.250:1900\r\n"
            "MAN: \"ssdp:discover\"\r\n"
            "MX: 3\r\n"
            "ST: urn:schemas-upnp-org:device:MediaRenderer:1\r\n"
            "\r\n";

        udp::endpoint multicastEndpoint(
            boost::asio::ip::address::from_string("239.255.255.250"), 1900);

        socket.send_to(boost::asio::buffer(msearch), multicastEndpoint);

        // Yanıtları bekle
        while (isScanning_) {
            std::array<char, 1024> buffer;
            udp::endpoint senderEndpoint;

            boost::system::error_code ec;
            size_t len = socket.receive_from(
                boost::asio::buffer(buffer), senderEndpoint, 0, ec);

            if (!ec && len > 0) {
                std::string response(buffer.data(), len);
                auto device = parseDLNAResponse(response);
                if (device) {
                    deviceRepo_->saveOrUpdate(*device);
                    if (callback_) callback_(*device);
                }
            }
        }
    }

    void scanMDNS() {
        // mDNS/DNS-SD tarama
        // _music._tcp.local. service type
        dnssd::ServiceBrowser browser("_music._tcp");
        browser.onServiceFound([this](const dnssd::Service& service) {
            Device device;
            device.name = service.name;
            device.type = DeviceType::Streamer;
            device.connection.ipAddress = service.address;
            device.connection.port = service.port;
            device.connection.type = ConnectionType::WiFi;

            deviceRepo_->saveOrUpdate(device);
            if (callback_) callback_(device);
        });

        browser.browse();
    }

    void scanBluetooth() {
        // Bluetooth Low Energy tarama
        // COREMUSIC service UUID: 0x1848
        BluetoothLEScanner scanner;
        scanner.setFilterByServiceUUID("0x1848");

        scanner.onDeviceFound([this](const BluetoothDevice& btDevice) {
            Device device;
            device.name = btDevice.name;
            device.type = mapBluetoothDeviceType(btDevice.classOfDevice);
            device.connection.type = ConnectionType::Bluetooth;
            device.connection.macAddress = btDevice.macAddress;
            device.signalStrength = btDevice.rssi;

            deviceRepo_->saveOrUpdate(device);
            if (callback_) callback_(device);
        });

        scanner.startScan(std::chrono::seconds(10));
    }

    std::optional<Device> parseDLNAResponse(const std::string& response) {
        // HTTP header parsing
        std::istringstream stream(response);
        std::string line;

        Device device;
        device.connection.type = ConnectionType::WiFi;

        while (std::getline(stream, line)) {
            if (line.find("LOCATION:") == 0) {
                device.connection.ipAddress = extractIP(line);
            } else if (line.find("SERVER:") == 0) {
                device.info.manufacturer = extractManufacturer(line);
            } else if (line.find("USN:") == 0) {
                device.info.udn = extractUDN(line);
            }
        }

        if (device.connection.ipAddress.empty()) {
            return std::nullopt;
        }

        return device;
    }
};

} // namespace CoreMusic::K8::Device
```

### Device Health Monitor

```cpp
// K8/device-service/src/DeviceHealthMonitor.cpp
namespace CoreMusic::K8::Device {

class DeviceHealthMonitor {
public:
    struct HealthStatus {
        std::string deviceId;
        bool isHealthy;
        int signalStrength;
        int batteryLevel;          // -1 if not applicable
        int temperature;           // celsius
        int64_t uptimeSeconds;
        int64_t lastResponseMs;
        std::vector<std::string> issues;
        std::chrono::system_clock::time_point checkedAt;
    };

    DeviceHealthMonitor(std::shared_ptr<IDeviceRepository> deviceRepo)
        : deviceRepo_(std::move(deviceRepo)) {}

    void startMonitoring(std::chrono::seconds interval = std::chrono::seconds(30)) {
        monitorInterval_ = interval;
        monitorThread_ = std::thread([this]() {
            while (running_) {
                checkAllDevices();
                std::this_thread::sleep_for(monitorInterval_);
            }
        });
    }

    void stopMonitoring() {
        running_ = false;
        if (monitorThread_.joinable()) {
            monitorThread_.join();
        }
    }

    HealthStatus checkDevice(const std::string& deviceId) {
        HealthStatus status;
        status.deviceId = deviceId;
        status.checkedAt = std::chrono::system_clock::now();
        status.isHealthy = true;

        auto device = deviceRepo_->findById(deviceId);
        if (!device) {
            status.isHealthy = false;
            status.issues.push_back("Device not found");
            return status;
        }

        // Ping testi
        auto pingResult = pingDevice(device->connection.ipAddress);
        status.lastResponseMs = pingResult.latencyMs;

        if (!pingResult.success) {
            status.isHealthy = false;
            status.issues.push_back("Device unreachable");
        }

        // Sinyal gücü
        status.signalStrength = device->signalStrength;
        if (device->signalStrength < 20) {
            status.isHealthy = false;
            status.issues.push_back("Weak signal strength");
        }

        // Uptime kontrolü
        status.uptimeSeconds = getDeviceUptime(deviceId);
        if (status.uptimeSeconds < 60) {
            status.issues.push_back("Device recently restarted");
        }

        return status;
    }

private:
    std::shared_ptr<IDeviceRepository> deviceRepo_;
    std::chrono::seconds monitorInterval_{30};
    std::thread monitorThread_;
    std::atomic<bool> running_{false};

    void checkAllDevices() {
        auto devices = deviceRepo_->findAll();
        for (const auto& device : devices) {
            if (device.isOnline) {
                auto health = checkDevice(device.id);

                if (!health.isHealthy) {
                    publishEvent(DeviceHealthChangedEvent{
                        device.id, health
                    });
                }
            }
        }
    }

    struct PingResult {
        bool success;
        int64_t latencyMs;
    };

    PingResult pingDevice(const std::string& ipAddress) {
        auto start = std::chrono::steady_clock::now();
        // ICMP ping implementation
        // ...
        auto end = std::chrono::steady_clock::now();
        auto latency = std::chrono::duration_cast<std::chrono::milliseconds>(
            end - start).count();

        return {true, latency};
    }
};

} // namespace CoreMusic::K8::Device
```

### Remote Control Service

```cpp
// K8/device-service/src/RemoteControl.cpp
namespace CoreMusic::K8::Device {

class RemoteControlService {
public:
    ServiceResult<void> sendCommand(const std::string& deviceId,
                                   const RemoteCommand& command) {
        auto device = deviceRepo_->findById(deviceId);
        if (!device) {
            return ServiceResult<void>::error({
                404, "Device not found", "", ServiceErrorCode::NotFound
            });
        }

        if (!device->isOnline) {
            return ServiceResult<void>::error({
                503, "Device is offline", "", ServiceErrorCode::External
            });
        }

        switch (command.type) {
            case CommandType::Play:
                return sendPlayCommand(*device);
            case CommandType::Pause:
                return sendPauseCommand(*device);
            case CommandType::Volume:
                return sendVolumeCommand(*device, command.volume);
            case CommandType::Input:
                return sendInputCommand(*device, command.input);
            case CommandType::Custom:
                return sendCustomCommand(*device, command.customPayload);
        }

        return ServiceResult<void>::error({
            400, "Unknown command type", "", ServiceErrorCode::Validation
        });
    }

private:
    std::shared_ptr<IDeviceRepository> deviceRepo_;

    ServiceResult<void> sendPlayCommand(const Device& device) {
        switch (device.connection.type) {
            case ConnectionType::WiFi:
                return sendHTTPCommand(device, "/play");
            case ConnectionType::Bluetooth:
                return sendAVRCPCommand(device, AVRCPCommand::Play);
            case ConnectionType::USB:
                return sendUSBCommand(device, USBCommand::Play);
            default:
                return ServiceResult<void>::error({
                    400, "Unsupported connection type",
                    "", ServiceErrorCode::Validation
                });
        }
    }

    ServiceResult<void> sendHTTPCommand(const Device& device,
                                       const std::string& endpoint) {
        std::string url = "http://" + device.connection.ipAddress +
                         ":" + std::to_string(device.connection.port) +
                         endpoint;

        auto response = httpClient_->post(url, "");
        if (response.statusCode == 200) {
            return ServiceResult<void>::success(std::monostate{});
        }

        return ServiceResult<void>::error({
            response.statusCode, "Command failed",
            response.body, ServiceErrorCode::External
        });
    }
};

} // namespace CoreMusic::K8::Device
```

## Bağımlılıklar

| Servis/Bileşen | Bağımlılık Tipi | Açıklama |
|----------------|-----------------|----------|
| K2-Driver | Core | Donanım sürücüleri |
| K6-Network | Infrastructure | Ağ protokolleri |
| K7-Repository | Data Access | Cihaz veritabanı |
| K5-Cache | Infrastructure | Cihaz durumu cache |
| SSDP/mDNS | External | Cihaz keşfi protokolleri |

## Durum: Implementasyon

- **Core Device Management**: ✅ Tamamlandı
- **DLNA Discovery**: ✅ Tamamlandı ⚠️ PLANNED (kod yok — ADR-037 şart 1a)
- **mDNS Discovery**: ✅ Tamamlandı ⚠️ PLANNED (kod yok — ADR-037 şart 1a)
- **Bluetooth Discovery**: 🔄 Devam ediyor ⚠️ PLANNED (kod yok — ADR-037 şart 1a)
- **Health Monitoring**: ✅ Tamamlandı
- **Remote Control**: 🔄 Devam ediyor
