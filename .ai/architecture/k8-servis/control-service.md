---
title: "Control Service - Kontrol Servisi"
layer: K8
category: "Servis"
date: 2026-09-20
version: "1.0.0"
status: "implemented"
dependencies:
  - K5-Cache
  - K7-Repository
  - K5-Security
---

# Control Service - Kontrol Servisi

## Genel Bakış

Control Service, COREMUSIC sisteminin merkezi yönetim noktasıdır. Kullanıcı hesap yönetimi, sistem ayarları, preferanslar ve genel kontrol operasyonlarını yönetir. Tümservisler arasındaki koordinasyonu sağlayan orkestratör rolü üstlenir.

Servis, multi-tenant mimari destekli olup, kullanıcı bazlı izolasyon ve özel ayar yönetimini提供 eder. Sistem genelinde configuration management, feature flags ve operational controls bu servis üzerinden yürütülür.

## Servis Arayüzü

### User Management Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/users` | GET | Tüm kullanıcıları listele |
| `/api/v1/users/{id}` | GET | Kullanıcı detayı |
| `/api/v1/users` | POST | Yeni kullanıcı oluştur |
| `/api/v1/users/{id}` | PUT | Kullanıcı güncelle |
| `/api/v1/users/{id}` | DELETE | Kullanıcı sil |
| `/api/v1/users/{id}/preferences` | GET | Kullanıcı tercihleri |
| `/api/v1/users/{id}/preferences` | PUT | Kullanıcı tercihlerini güncelle |
| `/api/v1/auth/login` | POST | Kullanıcı girişi |
| `/api/v1/auth/logout` | POST | Kullanıcı çıkışı |
| `/api/v1/auth/refresh` | POST | Token yenileme |

### Settings Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/settings/system` | GET | Sistem ayarlarını al |
| `/api/v1/settings/system` | PUT | Sistem ayarlarını güncelle |
| `/api/v1/settings/audio` | GET | Ses ayarlarını al |
| `/api/v1/settings/audio` | PUT | Ses ayarlarını güncelle |
| `/api/v1/settings/network` | GET | Ağ ayarlarını al |
| `/api/v1/settings/network` | PUT | Ağ ayarlarını güncelle |
| `/api/v1/settings/display` | GET | Görünüm ayarlarını al |
| `/api/v1/settings/display` | PUT | Görünüm ayarlarını güncelle |

### System Control Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/system/status` | GET | Sistem durumu |
| `/api/v1/system/restart` | POST | Sistemi yeniden başlat |
| `/api/v1/system/shutdown` | POST | Sistemi kapat |
| `/api/v1/system/firmware` | POST | Firmware güncelleme |
| `/api/v1/system/logs` | GET | Sistem loglarını al |
| `/api/v1/system/diagnostics` | GET | Tanılama bilgileri |

## Teknik Detaylar

### User Management Model

```cpp
// K8/control-service/include/UserManager.h
namespace CoreMusic::K8::Control {

struct User {
    std::string id;
    std::string username;
    std::string email;
    std::string displayName;
    std::string passwordHash;
    UserRole role;
    UserPreferences preferences;
    std::chrono::system_clock::time_point createdAt;
    std::chrono::system_clock::time_point lastLoginAt;
    bool isActive;
};

enum class UserRole {
    Guest,      // Sadece müzik dinleyebilir
    User,       // Normal kullanıcı
    Premium,    // Premium özellikler
    Admin       // Yönetici
};

struct UserPreferences {
    // Ses tercihleri
    AudioPreferences audio;
    // Görünüm tercihleri
    DisplayPreferences display;
    // Ağ tercihleri
    NetworkPreferences network;
    // Gizlilik tercihleri
    PrivacyPreferences privacy;
};

struct AudioPreferences {
    std::string defaultOutputDevice;
    std::string defaultInputDevice;
    int volumeLevel;              // 0-100
    bool normalizeVolume;
    bool crossfadeEnabled;
    int crossfadeDuration;        // saniye
    std::string defaultEqualizer;
    bool gaplessPlayback;
};

struct DisplayPreferences {
    std::string theme;            // "dark", "light", "auto"
    std::string language;
    int fontSize;
    bool showAlbumArt;
    bool showLyrics;
    DisplayMode displayMode;
};

enum class DisplayMode {
    Compact,
    Normal,
    Expanded,
    Fullscreen
};

} // namespace CoreMusic::K8::Control
```

### Control Service Implementation

```cpp
// K8/control-service/src/ControlService.cpp
namespace CoreMusic::K8::Control {

class ControlService : public IService {
public:
    ControlService(std::shared_ptr<IUserRepository> userRepo,
                   std::shared_ptr<ICacheManager> cache,
                   std::shared_ptr<ISecurityManager> security)
        : userRepo_(std::move(userRepo))
        , cache_(std::move(cache))
        , security_(std::move(security)) {}

    void initialize() override {
        loadDefaultSettings();
        initializeFeatureFlags();
        status_ = ServiceStatus::Running;
    }

    // User Management
    ServiceResult<User> createUser(const CreateUserRequest& request) {
        // Validasyon
        if (request.username.empty() || request.email.empty()) {
            return ServiceResult<User>::error({
                400, "Username and email are required",
                "", ServiceErrorCode::Validation
            });
        }

        // Email format kontrolü
        if (!isValidEmail(request.email)) {
            return ServiceResult<User>::error({
                400, "Invalid email format",
                "", ServiceErrorCode::Validation
            });
        }

        // Kullanıcı adı benzersizlik kontrolü
        if (userRepo_->existsByUsername(request.username)) {
            return ServiceResult<User>::error({
                409, "Username already exists",
                "", ServiceErrorCode::Conflict
            });
        }

        User user;
        user.id = generateUUID();
        user.username = request.username;
        user.email = request.email;
        user.displayName = request.displayName;
        user.passwordHash = security_->hashPassword(request.password);
        user.role = UserRole::User;
        user.createdAt = std::chrono::system_clock::now();
        user.isActive = true;

        auto savedUser = userRepo_->save(user);
        cache_->invalidate("users:*");

        return ServiceResult<User>::success(savedUser);
    }

    ServiceResult<User> authenticate(const AuthRequest& request) {
        auto user = userRepo_->findByUsername(request.username);
        if (!user) {
            return ServiceResult<User>::error({
                401, "Invalid credentials",
                "", ServiceErrorCode::Authentication
            });
        }

        if (!security_->verifyPassword(request.password, user->passwordHash)) {
            return ServiceResult<User>::error({
                401, "Invalid credentials",
                "", ServiceErrorCode::Authentication
            });
        }

        user->lastLoginAt = std::chrono::system_clock::now();
        userRepo_->save(*user);

        return ServiceResult<User>::success(*user);
    }

    // Settings Management
    ServiceResult<SystemSettings> getSystemSettings() {
        auto cached = cache_->get<SystemSettings>("settings:system");
        if (cached) {
            return ServiceResult<SystemSettings>::success(*cached);
        }

        auto settings = loadSystemSettings();
        cache_->set("settings:system", settings, 300s);

        return ServiceResult<SystemSettings>::success(settings);
    }

    ServiceResult<void> updateSystemSettings(const SystemSettings& settings) {
        // Validasyon
        auto validationResult = validateSettings(settings);
        if (!validationResult.isSuccess()) {
            return ServiceResult<void>::error(validationResult.error());
        }

        saveSystemSettings(settings);
        cache_->invalidate("settings:*");

        // Notify other services
        publishEvent(SettingsChangedEvent{settings});

        return ServiceResult<void>::success(std::monostate{});
    }

    // System Control
    ServiceResult<SystemStatus> getSystemStatus() {
        SystemStatus status;
        status.cpuUsage = getCPUUsage();
        status.memoryUsage = getMemoryUsage();
        status.diskUsage = getDiskUsage();
        status.activeConnections = getActiveConnections();
        status.uptime = getUptime();
        status.services = getServiceStatuses();

        return ServiceResult<SystemStatus>::success(status);
    }

    ServiceResult<void> restartSystem() {
        // Graceful shutdown
        publishEvent(SystemRestartEvent{});

        // Servisleri durdur
        ServiceRegistry::instance().stopAll();

        // Sistem restart
        std::system("sudo systemctl restart coremusic");

        return ServiceResult<void>::success(std::monostate{});
    }

private:
    std::shared_ptr<IUserRepository> userRepo_;
    std::shared_ptr<ICacheManager> cache_;
    std::shared_ptr<ISecurityManager> security_;
    ServiceStatus status_ = ServiceStatus::Uninitialized;

    bool isValidEmail(const std::string& email) {
        const std::regex pattern(
            R"([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})"
        );
        return std::regex_match(email, pattern);
    }

    std::string generateUUID() {
        // UUID v4 generation
        static std::random_device rd;
        static std::mt19937 gen(rd());
        static std::uniform_int_distribution<> dis(0, 15);
        static std::uniform_int_distribution<> dis2(8, 11);

        std::stringstream ss;
        ss << std::hex;
        for (int i = 0; i < 8; i++) ss << dis(gen);
        ss << "-";
        for (int i = 0; i < 4; i++) ss << dis(gen);
        ss << "-4";
        for (int i = 0; i < 3; i++) ss << dis(gen);
        ss << "-";
        ss << dis2(gen);
        for (int i = 0; i < 3; i++) ss << dis(gen);
        ss << "-";
        for (int i = 0; i < 12; i++) ss << dis(gen);

        return ss.str();
    }
};

} // namespace CoreMusic::K8::Control
```

### Feature Flags System

```cpp
// K8/control-service/include/FeatureFlags.h
namespace CoreMusic::K8::Control {

class FeatureFlags {
public:
    struct Flag {
        std::string name;
        bool enabled;
        std::string description;
        std::vector<std::string> allowedRoles;
        std::chrono::system_clock::time_point expiresAt;
    };

    void initialize(const nlohmann::json& config) {
        for (const auto& [name, flagConfig] : config.items()) {
            flags_[name] = Flag{
                name,
                flagConfig["enabled"],
                flagConfig["description"],
                flagConfig["allowedRoles"],
                parseTimestamp(flagConfig["expiresAt"])
            };
        }
    }

    bool isEnabled(const std::string& flagName,
                    const UserRole& role = UserRole::User) const {
        auto it = flags_.find(flagName);
        if (it == flags_.end()) return false;

        const auto& flag = it->second;
        if (!flag.enabled) return false;

        if (std::chrono::system_clock::now() > flag.expiresAt) return false;

        if (!flag.allowedRoles.empty()) {
            return std::find(flag.allowedRoles.begin(),
                           flag.allowedRoles.end(),
                           roleToString(role)) != flag.allowedRoles.end();
        }

        return true;
    }

private:
    std::unordered_map<std::string, Flag> flags_;
};

} // namespace CoreMusic::K8::Control
```

### Audit Logging

```cpp
// K8/control-service/include/AuditLogger.h
namespace CoreMusic::K8::Control {

struct AuditEntry {
    std::string id;
    std::string userId;
    std::string action;
    std::string resource;
    std::string resourceId;
    nlohmann::json details;
    std::string ipAddress;
    std::string userAgent;
    AuditResult result;
    std::chrono::system_clock::time_point timestamp;
};

enum class AuditResult {
    Success,
    Failure,
    PartialSuccess
};

class AuditLogger {
public:
    void log(const AuditEntry& entry) {
        // Structured logging
        nlohmann::json logEntry = {
            {"id", entry.id},
            {"userId", entry.userId},
            {"action", entry.action},
            {"resource", entry.resource},
            {"resourceId", entry.resourceId},
            {"details", entry.details},
            {"ipAddress", entry.ipAddress},
            {"userAgent", entry.userAgent},
            {"result", auditResultToString(entry.result)},
            {"timestamp", formatTimestamp(entry.timestamp)}
        };

        logger_->info(logEntry.dump());

        // Async persistence
        auditQueue_->enqueue(entry);
    }

    std::vector<AuditEntry> getAuditTrail(
        const std::string& userId,
        std::chrono::system_clock::time_point from,
        std::chrono::system_clock::time_point to) {
        return auditRepo_->findByUserAndDateRange(userId, from, to);
    }

private:
    std::shared_ptr<ILogger> logger_;
    std::shared_ptr<IQueue<AuditEntry>> auditQueue_;
    std::shared_ptr<IAuditRepository> auditRepo_;
};

} // namespace CoreMusic::K8::Control
```

## Bağımlılıklar

| Servis/Bileşen | Bağımlılık Tipi | Açıklama |
|----------------|-----------------|----------|
| K5-Cache | Infrastructure | Redis-based caching |
| K7-Repository | Data Access | User, Settings repository |
| K5-Security | Infrastructure | JWT, password hashing |
| K5-Messaging | Infrastructure | Event publishing |
| K9-AI Service | Optional | Personalization recommendations |

## Implementasyon Aşamaları

1. **Core User Management**: CRUD operations, authentication
2. **Settings Management**: System, audio, display settings
3. **System Control**: Status, restart, shutdown
4. **Feature Flags**: Dynamic feature management
5. **Audit Logging**: Complete audit trail

## Durum: Implementasyon

- **Core User Management**: ✅ Tamamlandı
- **Settings Management**: ✅ Tamamlandı
- **System Control**: ✅ Tamamlandı
- **Feature Flags**: 🔄 Devam ediyor
- **Audit Logging**: 🔄 Devam ediyor
