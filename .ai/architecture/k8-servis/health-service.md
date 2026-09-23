---
title: "Health Service - Sağlık Servisi"
layer: K8
category: "Servis"
date: "2026-09-20"
version: "1.0.0"
status: "implemented"
dependencies:
  - K5-Monitoring
  - K7-Repository
---

# Health Service - Sağlık Servisi

## Genel Bakış

Health Service, COREMUSIC sisteminin genel sağlık durumunu izleyen ve raporlayan merkezi servistir. Health checks, readiness probes, liveness probes ve component health monitoring gibi tüm operational health işlevlerini yönetir. Servis, Kubernetes-style health check patterns kullanarak cloud-native monitoring sağlar.

Her servis ve bileşen için bağımsız health check'ler çalıştırılır, anomaly detection yapılır ve alerting sistemiyle entegre çalışır. Service mesh integration ile distributed health monitoring desteklenir.

## Servis Arayüzü

### Health Check Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/health` | GET | Genel sağlık durumu |
| `/health/live` | GET | Liveness probe |
| `/health/ready` | GET | Readiness probe |
| `/health/startup` | GET | Startup probe |
| `/health/detailed` | GET | Detaylı sağlık raporu |
| `/health/component/{name}` | GET | Bileşen sağlık durumu |
| `/health/history` | GET | Sağlık geçmişi |
| `/health/metrics` | GET | Sağlık metrikleri |
| `/health/alerts` | GET | Aktif uyarılar |
| `/health/alerts/{id}/acknowledge` | POST | Uyarıyı onayla |

### Diagnostics Endpoints

| Endpoint | Method | Açıklama |
|----------|--------|----------|
| `/api/v1/diagnostics/system` | GET | Sistem tanılaması |
| `/api/v1/diagnostics/cpu` | GET | CPU kullanımı |
| `/api/v1/diagnostics/memory` | GET | Bellek kullanımı |
| `/api/v1/diagnostics/disk` | GET | Disk kullanımı |
| `/api/v1/diagnostics/network` | GET | Ağ durumu |
| `/api/v1/diagnostics/audio` | GET | Ses alt sistemi |
| `/api/v1/diagnostics/processes` | GET | Çalışan süreçler |

## Teknik Detaylar

### Health Status Model

```cpp
// K8/health-service/include/HealthModels.h
namespace CoreMusic::K8::Health {

enum class HealthStatus {
    Healthy,         // Tüm sistemler normal
    Degraded,        // Bazı sistemler sorunlu ama çalışıyor
    Unhealthy,       // Kritik sistemlerde sorun
    Unknown          // Durum bilinmiyor
};

struct HealthCheckResult {
    std::string component;
    HealthStatus status;
    std::string message;
    nlohmann::json details;
    std::chrono::milliseconds responseTime;
    std::chrono::system_clock::time_point checkedAt;
    std::vector<HealthCheckResult> subChecks;
};

struct SystemHealth {
    HealthStatus status;
    std::string version;
    std::chrono::system_clock::time_point startedAt;
    std::chrono::milliseconds uptime;
    std::vector<HealthCheckResult> checks;
    SystemMetrics metrics;
    std::vector<Alert> activeAlerts;
};

struct SystemMetrics {
    float cpuUsage;
    float memoryUsage;
    float diskUsage;
    int activeConnections;
    int requestsPerSecond;
    float errorRate;
    float averageResponseTime;
    int activeThreads;
    int64_t totalMemory;
    int64_t usedMemory;
    int64_t totalDisk;
    int64_t usedDisk;
};

struct Alert {
    std::string id;
    AlertSeverity severity;
    std::string component;
    std::string message;
    std::string details;
    std::chrono::system_clock::time_point triggeredAt;
    std::chrono::system_clock::time_point acknowledgedAt;
    bool isAcknowledged;
    AlertType type;
};

enum class AlertSeverity {
    Info,
    Warning,
    Critical,
    Emergency
};

enum class AlertType {
    Threshold,
    Anomaly,
    Availability,
    Performance,
    Security
};

} // namespace CoreMusic::K8::Health
```

### Health Service Implementation

```cpp
// K8/health-service/src/HealthService.cpp
namespace CoreMusic::K8::Health {

class HealthService : public IService {
public:
    HealthService(std::shared_ptr<IHealthRepository> healthRepo,
                  std::shared_ptr<IMetricsCollector> metrics)
        : healthRepo_(std::move(healthRepo))
        , metrics_(std::move(metrics)) {}

    void initialize() override {
        // Health check scheduler'ı başlat
        scheduleChecks();
        status_ = ServiceStatus::Running;
    }

    SystemHealth getSystemHealth() {
        SystemHealth health;
        health.status = HealthStatus::Healthy;
        health.version = getAppVersion();
        health.startedAt = startTime_;
        health.uptime = std::chrono::duration_cast<std::chrono::milliseconds>(
            std::chrono::system_clock::now() - startTime_);

        // Tüm health check'leri çalıştır
        health.checks = runAllChecks();

        // Genel durumu belirle
        for (const auto& check : health.checks) {
            if (check.status == HealthStatus::Unhealthy) {
                health.status = HealthStatus::Unhealthy;
                break;
            }
            if (check.status == HealthStatus::Degraded) {
                health.status = HealthStatus::Degraded;
            }
        }

        // Metrikleri topla
        health.metrics = collectMetrics();

        // Aktif uyarıları al
        health.activeAlerts = getActiveAlerts();

        return health;
    }

    bool isLive() {
        // Liveness: Süreç hâlâ çalışıyor mu?
        return status_ == ServiceStatus::Running;
    }

    bool isReady() {
        // Readiness: Trafik almaya hazır mı?
        auto checks = runReadinessChecks();
        return std::all_of(checks.begin(), checks.end(),
            [](const HealthCheckResult& check) {
                return check.status != HealthStatus::Unhealthy;
            });
    }

    bool isStarted() {
        // Startup: Başlangıç tamamlandı mı?
        return status_ == ServiceStatus::Running;
    }

private:
    std::shared_ptr<IHealthRepository> healthRepo_;
    std::shared_ptr<IMetricsCollector> metrics_;
    std::chrono::system_clock::time_point startTime_ =
        std::chrono::system_clock::now();

    std::vector<HealthCheckResult> runAllChecks() {
        std::vector<HealthCheckResult> results;

        // Database health check
        results.push_back(checkDatabase());

        // Cache health check
        results.push_back(checkCache());

        // Audio engine health check
        results.push_back(checkAudioEngine());

        // Network health check
        results.push_back(checkNetwork());

        // Disk health check
        results.push_back(checkDisk());

        // Service health checks
        auto services = ServiceRegistry::instance().getRegisteredServices();
        for (const auto& service : services) {
            results.push_back(checkService(service));
        }

        return results;
    }

    HealthCheckResult checkDatabase() {
        auto start = std::chrono::steady_clock::now();

        HealthCheckResult result;
        result.component = "database";

        try {
            // Simple query test
            auto latency = database_->ping();
            auto end = std::chrono::steady_clock::now();

            result.status = HealthStatus::Healthy;
            result.message = "Database is responding";
            result.responseTime = std::chrono::duration_cast<
                std::chrono::milliseconds>(end - start);
            result.details = {
                {"latency_ms", latency},
                {"connection_pool_size", database_->getPoolSize()},
                {"active_connections", database_->getActiveConnections()}
            };
        } catch (const std::exception& e) {
            result.status = HealthStatus::Unhealthy;
            result.message = "Database check failed: " +
                            std::string(e.what());
        }

        return result;
    }

    HealthCheckResult checkCache() {
        auto start = std::chrono::steady_clock::now();

        HealthCheckResult result;
        result.component = "cache";

        try {
            // Cache ping
            auto latency = cache_->ping();
            auto stats = cache_->getStats();
            auto end = std::chrono::steady_clock::now();

            result.status = HealthStatus::Healthy;
            result.message = "Cache is responding";
            result.responseTime = std::chrono::duration_cast<
                std::chrono::milliseconds>(end - start);
            result.details = {
                {"latency_ms", latency},
                {"hit_rate", stats.hitRate},
                {"memory_used", stats.memoryUsed},
                {"memory_max", stats.memoryMax},
                {"connected_clients", stats.connectedClients}
            };

            // Memory usage uyarısı
            if (stats.memoryUsed > stats.memoryMax * 0.9) {
                result.status = HealthStatus::Degraded;
                result.message = "Cache memory usage is high";
            }
        } catch (const std::exception& e) {
            result.status = HealthStatus::Unhealthy;
            result.message = "Cache check failed: " +
                            std::string(e.what());
        }

        return result;
    }

    HealthCheckResult checkAudioEngine() {
        auto start = std::chrono::steady_clock::now();

        HealthCheckResult result;
        result.component = "audio_engine";

        try {
            auto engineStatus = audioEngine_->getStatus();
            auto end = std::chrono::steady_clock::now();

            result.responseTime = std::chrono::duration_cast<
                std::chrono::milliseconds>(end - start);

            if (engineStatus.isActive) {
                result.status = HealthStatus::Healthy;
                result.message = "Audio engine is active";
                result.details = {
                    {"sample_rate", engineStatus.sampleRate},
                    {"buffer_size", engineStatus.bufferSize},
                    {"latency_ms", engineStatus.latencyMs},
                    {"cpu_usage", engineStatus.cpuUsage},
                    {"underruns", engineStatus.underruns}
                };

                // Underrun kontrolü
                if (engineStatus.underruns > 10) {
                    result.status = HealthStatus::Degraded;
                    result.message = "Audio underruns detected";
                }
            } else {
                result.status = HealthStatus::Unhealthy;
                result.message = "Audio engine is not active";
            }
        } catch (const std::exception& e) {
            result.status = HealthStatus::Unhealthy;
            result.message = "Audio engine check failed: " +
                            std::string(e.what());
        }

        return result;
    }

    HealthCheckResult checkDisk() {
        auto start = std::chrono::steady_clock::now();

        HealthCheckResult result;
        result.component = "disk";

        try {
            auto diskInfo = getDiskInfo("/");
            auto end = std::chrono::steady_clock::now();

            result.responseTime = std::chrono::duration_cast<
                std::chrono::milliseconds>(end - start);

            float usagePercent = static_cast<float>(diskInfo.used) /
                               diskInfo.total * 100.0f;

            result.details = {
                {"total_bytes", diskInfo.total},
                {"used_bytes", diskInfo.used},
                {"available_bytes", diskInfo.available},
                {"usage_percent", usagePercent}
            };

            if (usagePercent < 80) {
                result.status = HealthStatus::Healthy;
                result.message = "Disk usage is normal";
            } else if (usagePercent < 90) {
                result.status = HealthStatus::Degraded;
                result.message = "Disk usage is high";
            } else {
                result.status = HealthStatus::Unhealthy;
                result.message = "Disk usage is critical";
            }
        } catch (const std::exception& e) {
            result.status = HealthStatus::Unhealthy;
            result.message = "Disk check failed: " +
                            std::string(e.what());
        }

        return result;
    }

    void scheduleChecks() {
        checkThread_ = std::thread([this]() {
            while (running_) {
                // Her 30 saniyede bir health check
                auto health = getSystemHealth();
                healthRepo_->saveHealthSnapshot(health);

                // Alert kontrolü
                checkAlertConditions(health);

                std::this_thread::sleep_for(std::chrono::seconds(30));
            }
        });
    }

    void checkAlertConditions(const SystemHealth& health) {
        for (const auto& check : health.checks) {
            if (check.status == HealthStatus::Unhealthy) {
                triggerAlert({
                    generateUUID(),
                    AlertSeverity::Critical,
                    check.component,
                    check.message,
                    check.details.dump(),
                    std::chrono::system_clock::now(),
                    std::chrono::system_clock::time_point{},
                    false,
                    AlertType::Availability
                });
            }
        }
    }

    void triggerAlert(const Alert& alert) {
        // Alert'i kaydet
        healthRepo_->saveAlert(alert);

        // Notification gönder
        notificationService_->sendAlert(alert);

        // Log yaz
        logger_->error("Alert triggered: " + alert.message);
    }
};

} // namespace CoreMusic::K8::Health
```

### Diagnostics Collector

```cpp
// K8/health-service/src/DiagnosticsCollector.cpp
namespace CoreMusic::K8::Health {

class DiagnosticsCollector {
public:
    SystemDiagnostics collectFullDiagnostics() {
        SystemDiagnostics diag;
        diag.timestamp = std::chrono::system_clock::now();

        diag.cpu = collectCPUDiagnostics();
        diag.memory = collectMemoryDiagnostics();
        diag.disk = collectDiskDiagnostics();
        diag.network = collectNetworkDiagnostics();
        diag.audio = collectAudioDiagnostics();
        diag.processes = collectProcessDiagnostics();

        return diag;
    }

    CPUDiagnostics collectCPUDiagnostics() {
        CPUDiagnostics cpu;

        // CPU usage
        cpu.usagePercent = getCPUUsage();
        cpu.coreCount = std::thread::hardware_concurrency();

        // Per-core usage
        cpu.coreUsages = getPerCoreUsage();

        // Load average
        cpu.loadAverage1m = getLoadAverage(1);
        cpu.loadAverage5m = getLoadAverage(5);
        cpu.loadAverage15m = getLoadAverage(15);

        // Temperature
        cpu.temperature = getCPUTemperature();

        return cpu;
    }

    MemoryDiagnostics collectMemoryDiagnostics() {
        MemoryDiagnostics mem;

        auto memInfo = getMemoryInfo();
        mem.totalBytes = memInfo.total;
        mem.usedBytes = memInfo.used;
        mem.availableBytes = memInfo.available;
        mem.usagePercent = static_cast<float>(memInfo.used) /
                          memInfo.total * 100.0f;

        // Swap
        mem.swapTotal = memInfo.swapTotal;
        mem.swapUsed = memInfo.swapUsed;

        // Process memory
        mem.processMemory = getProcessMemoryUsage();

        return mem;
    }

    DiskDiagnostics collectDiskDiagnostics() {
        DiskDiagnostics disk;

        auto partitions = getDiskPartitions();
        for (const auto& partition : partitions) {
            DiskPartitionInfo info;
            info.mountPoint = partition.mountPoint;
            info.totalBytes = partition.total;
            info.usedBytes = partition.used;
            info.availableBytes = partition.available;
            info.fileSystem = partition.fileSystem;

            disk.partitions.push_back(info);
        }

        // I/O stats
        disk.ioStats = getDiskIOStats();

        // SMART data
        disk.smartData = getSMARTData();

        return disk;
    }

    AudioDiagnostics collectAudioDiagnostics() {
        AudioDiagnostics audio;

        // Audio devices
        audio.devices = getAudioDevices();

        // Current settings
        audio.sampleRate = getCurrentSampleRate();
        audio.bufferSize = getCurrentBufferSize();
        audio.bitDepth = getCurrentBitDepth();

        // Performance
        audio.latencyMs = getAudioLatency();
        audio.underrunCount = getUnderrunCount();
        audio.cpuUsage = getAudioCPUUsage();

        return audio;
    }

private:
    float getCPUUsage() {
        // Windows: GetSystemTimes
        // Linux: /proc/stat
        FILETIME idleTime, kernelTime, userTime;
        GetSystemTimes(&idleTime, &kernelTime, &userTime);

        // Calculate usage percentage
        // ... (implementation details)

        return usagePercent;
    }
};

} // namespace CoreMusic::K8::Health
```

## Bağımlılıklar

| Servis/Bileşen | Bağımlılık Tipi | Açıklama |
|----------------|-----------------|----------|
| K5-Monitoring | Infrastructure | Metrics collection |
| K7-Repository | Data Access | Health data persistence |
| K5-Messaging | Infrastructure | Alert notifications |

## Alert Eşikleri

| Metrik | Warning | Critical | Emergency |
|--------|---------|----------|-----------|
| CPU Usage | > 70% | > 85% | > 95% |
| Memory Usage | > 75% | > 90% | > 95% |
| Disk Usage | > 80% | > 90% | > 95% |
| Error Rate | > 1% | > 5% | > 10% |
| Response Time | > 100ms | > 500ms | > 1000ms |

## Durum: Implementasyon

- **Health Checks**: ✅ Tamamlandı
- **Liveness/Readiness Probes**: ✅ Tamamlandı
- **Diagnostics Collection**: ✅ Tamamlandı
- **Alert System**: ✅ Tamamlandı
- **History & Trending**: 🔄 Devam ediyor
