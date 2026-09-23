---
title: "Notification Service - Bildirim Servisi"
layer: K8
category: "Servis"
date: "2026-09-20"
version: "1.0.0"
status: "implemented"
dependencies:
  - K5-Messaging
  - K6-Network
  - K7-Repository
---

# Notification Service - Bildirim Servisi

## Genel Bakış

Notification Service, COREMUSIC ekosistemindeki tüm bildirimleri yöneten merkezi servistir. Push notifications, in-app alerts, system notifications ve custom notifications gibi çoklu bildirim kanallarını orkestra eder. Servis, multi-device notification delivery, template management ve notification preferences yönetimi sunar.

Servis, real-time notification delivery, batch notifications, scheduled notifications ve notification analytics gibi gelişmiş özellikler sunar. User preference-based routing ile bildirimlerin doğru kanallardan iletilmesini sağlar.

## Servis Arayüzü

### Notification Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/notifications` | GET | Tüm bildirimleri listele |
| `/api/v1/notifications/{id}` | GET | Bildirim detayı |
| `/api/v1/notifications` | POST | Yeni bildirim gönder |
| `/api/v1/notifications/{id}/read` | POST | Bildirimi okundu olarak işaretle |
| `/api/v1/notifications/{id}/delete` | DELETE | Bildirimi sil |
| `/api/v1/notifications/read-all` | POST | Tümünü okundu işaretle |
| `/api/v1/notifications/unread` | GET | Okunmamış bildirimler |
| `/api/v1/notifications/count` | GET | Bildirim sayısı |
| `/api/v1/notifications/preferences` | GET | Bildirim tercihleri |
| `/api/v1/notifications/preferences` | PUT | Tercihleri güncelle |
| `/api/v1/notifications/templates` | GET | Bildirim şablonları |
| `/api/v1/notifications/templates` | POST | Şablon oluştur |

### Push Registration Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/notifications/push/register` | POST | Push token kaydet |
| `/api/v1/notifications/push/unregister` | POST | Push token kaldır |
| `/api/v1/notifications/push/devices` | GET | Kayıtlı cihazlar |

### Batch Operations Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/notifications/batch` | POST | Toplu bildirim gönder |
| `/api/v1/notifications/schedule` | POST | Zamanlanmış bildirim |
| `/api/v1/notifications/schedule/{id}` | DELETE | Zamanlamayı iptal et |
| `/api/v1/notifications/analytics` | GET | Bildirim analitikleri |

## Teknik Detaylar

### Notification Data Model

```cpp
// K8/notification-service/include/NotificationModels.h
namespace CoreMusic::K8::Notification {

struct Notification {
    std::string id;
    std::string userId;
    NotificationType type;
    NotificationPriority priority;
    std::string title;
    std::string body;
    std::string imageUrl;
    std::string actionUrl;
    nlohmann::json data;
    std::string templateId;
    nlohmann::json templateData;
    NotificationChannel channel;
    NotificationStatus status;
    std::chrono::system_clock::time_point createdAt;
    std::chrono::system_clock::time_point sentAt;
    std::chrono::system_clock::time_point readAt;
    std::chrono::system_clock::time_point expiresAt;
    bool isRead;
    std::vector<DeliveryAttempt> deliveryAttempts;
};

enum class NotificationType {
    System,           // Sistem bildirimleri
    Playback,         // Çalma durumu
    Download,         // İndirme durumu
    Device,           // Cihaz durumu
    Social,           // Sosyal özellikler
    Recommendation,   // Öneri bildirimleri
    Alert,            // Uyarı bildirimleri
    Marketing,        // Pazarlama bildirimleri
    Custom            // Özel bildirimler
};

enum class NotificationPriority {
    Low,
    Normal,
    High,
    Urgent
};

enum class NotificationChannel {
    Push,             // Mobile push
    InApp,            // Uygulama içi
    Email,            // E-posta
    SMS,              // SMS
    WebSocket,        // Real-time
    Webhook           // External webhook
};

enum class NotificationStatus {
    Pending,
    Sending,
    Sent,
    Delivered,
    Read,
    Failed,
    Expired
};

struct NotificationTemplate {
    std::string id;
    std::string name;
    NotificationType type;
    std::string titleTemplate;     // Mustache-style
    std::string bodyTemplate;
    std::string imageUrlTemplate;
    std::map<std::string, std::string> defaultValues;
    std::vector<std::string> requiredVariables;
};

struct NotificationPreferences {
    std::string userId;
    std::map<NotificationType, ChannelPreferences> typePreferences;
    bool doNotDisturbEnabled;
    std::string doNotDisturbStart;
    std::string doNotDisturbEnd;
    bool soundEnabled;
    bool vibrationEnabled;
};

struct ChannelPreferences {
    bool enabled;
    std::vector<NotificationChannel> channels;
    int quietHoursStart;
    int quietHoursEnd;
};

struct DeliveryAttempt {
    std::string channel;
    std::chrono::system_clock::time_point attemptedAt;
    bool success;
    std::string errorMessage;
    std::string providerMessageId;
};

struct PushToken {
    std::string id;
    std::string userId;
    std::string token;
    std::string platform;          // "ios", "android", "web"
    std::string deviceName;
    std::string deviceModel;
    bool isActive;
    std::chrono::system_clock::time_point registeredAt;
    std::chrono::system_clock::time_point lastUsedAt;
};

} // namespace CoreMusic::K8::Notification
```

### Notification Service Implementation

```cpp
// K8/notification-service/src/NotificationService.cpp
namespace CoreMusic::K8::Notification {

class NotificationService : public IService {
public:
    NotificationService(std::shared_ptr<INotificationRepository> notifRepo,
                       std::shared_ptr<IPushProvider> pushProvider,
                       std::shared_ptr<IEmailProvider> emailProvider)
        : notifRepo_(std::move(notifRepo))
        , pushProvider_(std::move(pushProvider))
        , emailProvider_(std::move(emailProvider)) {}

    void initialize() override {
        // Scheduled notification worker'ı başlat
        startScheduler();
        // Real-time WebSocket handler'ı başlat
        startWebSocketServer();
        status_ = ServiceStatus::Running;
    }

    ServiceResult<Notification> sendNotification(
        const SendNotificationRequest& request) {

        // Preference kontrolü
        auto prefs = notifRepo_->getPreferences(request.userId);
        if (!isNotificationAllowed(request.type, request.channel, prefs)) {
            return ServiceResult<Notification>::error({
                400, "Notification type not allowed by user preferences",
                "", ServiceErrorCode::Authorization
            });
        }

        // Template processing
        std::string title = request.title;
        std::string body = request.body;

        if (!request.templateId.empty()) {
            auto result = processTemplate(
                request.templateId, request.templateData);
            if (result) {
                title = result->title;
                body = result->body;
            }
        }

        // Notification oluştur
        Notification notification;
        notification.id = generateUUID();
        notification.userId = request.userId;
        notification.type = request.type;
        notification.priority = request.priority;
        notification.title = title;
        notification.body = body;
        notification.imageUrl = request.imageUrl;
        notification.actionUrl = request.actionUrl;
        notification.data = request.data;
        notification.channel = request.channel;
        notification.status = NotificationStatus::Pending;
        notification.createdAt = std::chrono::system_clock::now();
        notification.isRead = false;

        // Expiry ayarla
        if (request.expiresIn.count() > 0) {
            notification.expiresAt =
                notification.createdAt + request.expiresIn;
        }

        // Kaydet
        notifRepo_->save(notification);

        // Gönder
        deliverNotification(notification);

        return ServiceResult<Notification>::success(notification);
    }

    ServiceResult<void> sendBatchNotification(
        const BatchNotificationRequest& request) {

        for (const auto& userId : request.userIds) {
            SendNotificationRequest notifRequest;
            notifRequest.userId = userId;
            notifRequest.type = request.type;
            notifRequest.priority = request.priority;
            notifRequest.title = request.title;
            notifRequest.body = request.body;
            notifRequest.data = request.data;

            sendNotification(notifRequest);
        }

        return ServiceResult<void>::success(std::monostate{});
    }

    ServiceResult<void> scheduleNotification(
        const ScheduledNotificationRequest& request) {

        // Zamanlama kontrolü
        if (request.scheduledAt <= std::chrono::system_clock::now()) {
            return ServiceResult<void>::error({
                400, "Scheduled time must be in the future",
                "", ServiceErrorCode::Validation
            });
        }

        // Zamanlanmış bildirimi kaydet
        ScheduledNotification scheduled;
        scheduled.id = generateUUID();
        scheduled.request = request;
        scheduled.status = ScheduledStatus::Pending;
        scheduled.createdAt = std::chrono::system_clock::now();

        notifRepo_->saveScheduled(scheduled);

        return ServiceResult<void>::success(std::monostate{});
    }

    ServiceResult<void> markAsRead(const std::string& notificationId) {
        auto notification = notifRepo_->findById(notificationId);
        if (!notification) {
            return ServiceResult<void>::error({
                404, "Notification not found",
                "", ServiceErrorCode::NotFound
            });
        }

        notification->isRead = true;
        notification->readAt = std::chrono::system_clock::now();
        notification->status = NotificationStatus::Read;
        notifRepo_->save(*notification);

        // Real-time update
        broadcastUpdate(notification->userId, *notification);

        return ServiceResult<void>::success(std::monostate{});
    }

    ServiceResult<void> registerPushToken(
        const PushTokenRegistration& registration) {

        PushToken token;
        token.id = generateUUID();
        token.userId = registration.userId;
        token.token = registration.token;
        token.platform = registration.platform;
        token.deviceName = registration.deviceName;
        token.deviceModel = registration.deviceModel;
        token.isActive = true;
        token.registeredAt = std::chrono::system_clock::now();

        notifRepo_->savePushToken(token);

        return ServiceResult<void>::success(std::monostate{});
    }

private:
    std::shared_ptr<INotificationRepository> notifRepo_;
    std::shared_ptr<IPushProvider> pushProvider_;
    std::shared_ptr<IEmailProvider> emailProvider_;

    void deliverNotification(Notification& notification) {
        notification.status = NotificationStatus::Sending;
        notifRepo_->save(notification);

        bool delivered = false;

        switch (notification.channel) {
            case NotificationChannel::Push:
                delivered = deliverPush(notification);
                break;
            case NotificationChannel::InApp:
                delivered = deliverInApp(notification);
                break;
            case NotificationChannel::Email:
                delivered = deliverEmail(notification);
                break;
            case NotificationChannel::WebSocket:
                delivered = deliverWebSocket(notification);
                break;
            case NotificationChannel::Webhook:
                delivered = deliverWebhook(notification);
                break;
        }

        notification.status = delivered ?
            NotificationStatus::Sent : NotificationStatus::Failed;
        notification.sentAt = std::chrono::system_clock::now();
        notifRepo_->save(notification);
    }

    bool deliverPush(const Notification& notification) {
        auto tokens = notifRepo_->getPushTokens(notification.userId);

        bool allSuccess = true;
        for (const auto& token : tokens) {
            PushMessage message;
            message.token = token.token;
            message.title = notification.title;
            message.body = notification.body;
            message.imageUrl = notification.imageUrl;
            message.data = notification.data;
            message.priority = notification.priority;

            auto result = pushProvider_->send(message);

            // Delivery attempt kaydet
            DeliveryAttempt attempt;
            attempt.channel = "push";
            attempt.attemptedAt = std::chrono::system_clock::now();
            attempt.success = result.success;
            attempt.errorMessage = result.error;
            attempt.providerMessageId = result.messageId;

            notification.deliveryAttempts.push_back(attempt);

            if (!result.success) {
                allSuccess = false;

                // Token geçersizse deaktif et
                if (result.error == "InvalidToken") {
                    deactivateToken(token.id);
                }
            }
        }

        return allSuccess;
    }

    bool deliverInApp(const Notification& notification) {
        // WebSocket ile real-time gönder
        return webSocketServer_->sendToUser(
            notification.userId,
            serializeNotification(notification));
    }

    bool deliverEmail(const Notification& notification) {
        EmailMessage message;
        message.to = getUserEmail(notification.userId);
        message.subject = notification.title;
        message.body = notification.body;
        message.htmlBody = renderEmailTemplate(notification);

        auto result = emailProvider_->send(message);

        DeliveryAttempt attempt;
        attempt.channel = "email";
        attempt.attemptedAt = std::chrono::system_clock::now();
        attempt.success = result.success;
        attempt.errorMessage = result.error;

        return result.success;
    }

    bool isNotificationAllowed(NotificationType type,
                             NotificationChannel channel,
                             const NotificationPreferences& prefs) {
        // Do not Disturb kontrolü
        if (prefs.doNotDisturbEnabled) {
            auto now = std::chrono::system_clock::now();
            auto time = std::chrono::system_clock::to_time_t(now);
            auto hours = std::localtime(&time)->tm_hour;

            int start = std::stoi(prefs.doNotDisturbStart);
            int end = std::stoi(prefs.doNotDisturbEnd);

            if (start <= end) {
                if (hours >= start && hours < end) return false;
            } else {
                if (hours >= start || hours < end) return false;
            }
        }

        // Type preference kontrolü
        auto it = prefs.typePreferences.find(type);
        if (it != prefs.typePreferences.end()) {
            auto& channelPrefs = it->second;
            if (!channelPrefs.enabled) return false;

            return std::find(channelPrefs.channels.begin(),
                           channelPrefs.channels.end(),
                           channel) != channelPrefs.channels.end();
        }

        return true; // Default: izin ver
    }

    void startScheduler() {
        schedulerThread_ = std::thread([this]() {
            while (running_) {
                // Zamanlanmış bildirimleri kontrol et
                auto scheduled = notifRepo_->getPendingScheduled();

                for (auto& item : scheduled) {
                    if (std::chrono::system_clock::now() >=
                        item.request.scheduledAt) {

                        SendNotificationRequest notifRequest;
                        notifRequest.userId = item.request.userId;
                        notifRequest.type = item.request.type;
                        notifRequest.priority = item.request.priority;
                        notifRequest.title = item.request.title;
                        notifRequest.body = item.request.body;

                        sendNotification(notifRequest);

                        item.status = ScheduledStatus::Sent;
                        notifRepo_->saveScheduled(item);
                    }
                }

                std::this_thread::sleep_for(std::chrono::seconds(30));
            }
        });
    }
};

} // namespace CoreMusic::K8::Notification
```

### Notification Templates

```cpp
// K8/notification-service/src/TemplateEngine.cpp
namespace CoreMusic::K8::Notification {

class TemplateEngine {
public:
    std::optional<ProcessedTemplate> processTemplate(
        const std::string& templateId,
        const nlohmann::json& data) {

        auto templ = notifRepo_->findTemplate(templateId);
        if (!templ) return std::nullopt;

        ProcessedTemplate result;
        result.title = renderMustache(templ->titleTemplate, data);
        result.body = renderMustache(templ->bodyTemplate, data);
        result.imageUrl = renderMustache(templ->imageUrlTemplate, data);

        return result;
    }

    std::string renderMustache(const std::string& templateStr,
                              const nlohmann::json& data) {
        std::string result = templateStr;

        // Simple mustache-style rendering
        // {{variable}} patterns
        std::regex varRegex(R"(\{\{(\w+)\}\})");
        std::smatch match;

        std::string::const_iterator searchStart(templateStr.cbegin());
        std::string output;

        while (std::regex_search(searchStart, templateStr.cend(),
                                match, varRegex)) {
            output += match.prefix();
            std::string varName = match[1].str();

            if (data.contains(varName)) {
                output += data[varName].get<std::string>();
            } else {
                output += match[0].str(); // Keep original
            }

            searchStart = match.suffix().first;
        }

        output += std::string(searchStart, templateStr.cend());

        return output;
    }

private:
    std::shared_ptr<INotificationRepository> notifRepo_;
};

} // namespace CoreMusic::K8::Notification
```

## Bağımlılıklar

| Servis/Bileşen | Bağımlılık Tipi | Açıklama |
|----------------|-----------------|----------|
| K5-Messaging | Infrastructure | Message queue |
| K6-Network | Infrastructure | Push provider connections |
| K7-Repository | Data Access | Notification verileri |
| Firebase/APNs | External | Push notification providers |
| SendGrid/SES | External | Email providers |

## Durum: Implementasyon

- **Core Notifications**: ✅ Tamamlandı
- **Push Notifications**: ✅ Tamamlandı
- **In-App Notifications**: ✅ Tamamlandı
- **Email Notifications**: 🔄 Devam ediyor
- **Template Engine**: ✅ Tamamlandı
- **Batch & Scheduled**: ✅ Tamamlandı
