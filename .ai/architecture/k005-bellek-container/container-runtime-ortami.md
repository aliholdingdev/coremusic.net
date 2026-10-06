---
title: "K005 Container Çalışma Ortamı — Docker, containerd, Kubernetes ve Sağlık Kontrolü"
type: architecture-layer
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 · K000-K071"
updated: 2026-10-06
---

# K005 — Container Çalışma Ortamı

> **K numarası:** K005 · **Klasör:** `k005-bellek-container` · **Dosya:** `container-runtime-ortami`
> **Sorumlu persona:** `devops-engineer` (birincil) · `embedded-engineer` (ses/RT etkileşimi)

## 1. Kapsam

Docker Engine, containerd, Kubernetes, Docker Compose ve sağlık kontrolü yapıları; ses
motorunun konteyner içinde nasıl çalıştırılacağı ve gerçek zamanlı yolun konteyner
sınırlarıyla nasıl başa çıktığı. Bellek yönetimi `[[bellek-yonetimi]]` dosyasındadır.

| İlişki | Hedef |
|---|---|
| Kardeş dosya | `[[bellek-yonetimi]]` |
| Klasör dizini | `[[index]]` |
| Üst (süreç/IPC) | `[[../k004-surec-ipc/index]]` |
| Alt (sürücü) | `[[../k014-surucu-yigin/index]]` · `[[../k016-linux-ses/index]]` |

## 2. Gömülü Kaynaklar

| # | Kaynak (salt-okunur) | Satır | Boş olmayan |
|---|----------------------|------:|------------:|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` | 505 | 433 |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 22 | 20 |
| 3 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 24 | 17 |


> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` — satır: 505 (boş olmayan: 433).


## Container Runtime

### Genel Bakış

Container Runtime modülü, COREMUSIC'ın konteyner tabanlı dağıtım ve çalışma zamanı altyapısını yönetir. Docker Engine, containerd ve Kubernetes entegrasyonu ile ses işleme uygulamalarının konteyner içinde çalıştırılmasını sağlar. Health check mekanizmaları ile yüksek kullanılabilirlik ve otomatik kurtarma özelliklerini destekler.

### Teknik Detaylar

#### 1. Docker Engine Entegrasyonu

Docker, konteyner oluşturma ve yönetimi için temel platform:

```c
#include <docker/client.h>

// Docker client oluşturma
docker_client_t *client = docker_client_create("unix:///var/run/docker.sock");

// Konteyner oluşturma
docker_container_config_t config = {
    .name = "coremusic-audio-processor",
    .image = "coremusic/audio:latest",
    .env = {
        "SAMPLE_RATE=48000",
        "BUFFER_SIZE=512",
        "CHANNELS=2"
    },
    .volumes = {
        "/dev/snd:/dev/snd",  // Ses cihazları
        "/tmp/coremusic:/tmp"  // Geçici dosyalar
    },
    .network_mode = "host",
    .privileged = true,  // Donanım erişimi için
    .resources = {
        .memory_limit = "2G",
        .cpus = "4.0",
        .cpu_shares = 1024
    }
};

docker_container_t *container = docker_container_create(client, &config);
docker_container_start(container);
```

#### 2. containerd Entegrasyonu

containerd, düşük seviyeli konteyner runtime'ı:

```c
#include <containerd/client.h>

// containerd client oluşturma
containerd_client_t *client = containerd_client_connect("unix:///run/containerd/containerd.sock");

// Image pull
containerd_image_t *image = containerd_image_pull(client,
    "docker.io/coremusic/audio:latest",
    NULL, NULL);

// Konteyner oluşturma
containerd_container_config_t config = {
    .id = "coremusic-worker-001",
    .image = image,
    .rootfs = {
        .type = "overlayfs",
        .path = "/var/lib/containerd/io.containerd.snapshotter.overlayfs"
    },
    .spec = {
        .process = {
            .args = {"/usr/bin/coremusic", "--worker"},
            .env = {
                "COREMUSIC_MODE=worker",
                "COREMUSIC_LOG_LEVEL=info"
            },
            .cwd = "/workspace"
        },
        .mounts = {
            {
                .type = "bind",
                .source = "/dev/snd",
                .destination = "/dev/snd",
                .options = "rbind,rw"
            }
        },
        .linux = {
            .resources = {
                .memory = {
                    .limit = 2147483648  // 2GB
                },
                .cpu = {
                    .shares = 1024,
                    .quota = 400000,  // 4 CPU
                    .period = 100000
                }
            }
        }
    }
};

containerd_container_t *container = containerd_container_create(client, &config);
containerd_task_t *task = containerd_task_create(client, container);
containerd_task_start(task);
```

#### 3. Kubernetes Entegrasyonu

Kubernetes, konteyner orkestrasyonu için:

```yaml
## coremusic-deployment.yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: coremusic-audio-processor
  namespace: coremusic
spec:
  replicas: 3
  selector:
    matchLabels:
      app: coremusic-audio
  template:
    metadata:
      labels:
        app: coremusic-audio
    spec:
      containers:
      - name: audio-processor
        image: coremusic/audio:latest
        ports:
        - containerPort: 8080
          name: http
        - containerPort: 50000
          name: audio
          protocol: UDP
        env:
        - name: SAMPLE_RATE
          value: "48000"
        - name: BUFFER_SIZE
          value: "512"
        resources:
          requests:
            memory: "1Gi"
            cpu: "2"
          limits:
            memory: "2Gi"
            cpu: "4"
        volumeMounts:
        - name: snd-dev
          mountPath: /dev/snd
        - name: tmp-audio
          mountPath: /tmp/coremusic
        livenessProbe:
          exec:
            command:
            - /usr/bin/coremusic
            - --health-check
          initialDelaySeconds: 30
          periodSeconds: 10
          timeoutSeconds: 5
        readinessProbe:
          exec:
            command:
            - /usr/bin/coremusic
            - --ready-check
          initialDelaySeconds: 5
          periodSeconds: 5
        securityContext:
          privileged: true
          capabilities:
            add:
            - NET_ADMIN
            - SYS_NICE
      volumes:
      - name: snd-dev
        hostPath:
          path: /dev/snd
          type: CharDevice
      - name: tmp-audio
        emptyDir:
          medium: Memory
          sizeLimit: 1Gi
      nodeSelector:
        kubernetes.io/arch: amd64
      tolerations:
      - key: "node-role.kubernetes.io/audio"
        operator: "Equal"
        value: "true"
        effect: "NoSchedule"
---
## coremusic-service.yaml
apiVersion: v1
kind: Service
metadata:
  name: coremusic-service
  namespace: coremusic
spec:
  selector:
    app: coremusic-audio
  ports:
  - name: http
    port: 8080
    targetPort: 8080
  - name: audio
    port: 50000
    targetPort: 50000
    protocol: UDP
  type: LoadBalancer
---
## coremusic-hpa.yaml
apiVersion: autoscaling/v2
kind: HorizontalPodAutoscaler
metadata:
  name: coremusic-hpa
  namespace: coremusic
spec:
  scaleTargetRef:
    apiVersion: apps/v1
    kind: Deployment
    name: coremusic-audio-processor
  minReplicas: 2
  maxReplicas: 10
  metrics:
  - type: Resource
    resource:
      name: cpu
      target:
        type: Utilization
        averageUtilization: 70
  - type: Resource
    resource:
      name: memory
      target:
        type: Utilization
        averageUtilization: 80
```

#### 4. Docker Compose

Docker Compose, çoklu konteyner uygulamaları için:

```yaml
## docker-compose.yaml
version: '3.8'

services:
  audio-processor:
    build:
      context: .
      dockerfile: Dockerfile.audio
    container_name: coremusic-processor
    privileged: true
    network_mode: host
    volumes:
      - /dev/snd:/dev/snd
      - /tmp/coremusic:/tmp
      - ./config:/etc/coremusic
    environment:
      - SAMPLE_RATE=48000
      - BUFFER_SIZE=512
      - LOG_LEVEL=info
      - REDIS_HOST=redis
      - REDIS_PORT=6379
    deploy:
      resources:
        limits:
          cpus: '4'
          memory: 2G
        reservations:
          cpus: '2'
          memory: 1G
    restart: unless-stopped
    healthcheck:
      test: ["CMD", "/usr/bin/coremusic", "--health-check"]
      interval: 30s
      timeout: 10s
      retries: 3
      start_period: 40s

  redis:
    image: redis:7-alpine
    container_name: coremusic-redis
    ports:
      - "6379:6379"
    volumes:
      - redis-data:/data
    command: redis-server --appendonly yes --maxmemory 512mb
    healthcheck:
      test: ["CMD", "redis-cli", "ping"]
      interval: 10s
      timeout: 5s
      retries: 5

  prometheus:
    image: prom/prometheus:latest
    container_name: coremusic-prometheus
    ports:
      - "9090:9090"
    volumes:
      - ./prometheus.yml:/etc/prometheus/prometheus.yml
      - prometheus-data:/prometheus
    command:
      - '--config.file=/etc/prometheus/prometheus.yml'
      - '--storage.tsdb.path=/prometheus'

  grafana:
    image: grafana/grafana:latest
    container_name: coremusic-grafana
    ports:
      - "3000:3000"
    volumes:
      - grafana-data:/var/lib/grafana
      - ./grafana/dashboards:/var/lib/grafana/dashboards
    environment:
      - GF_SECURITY_ADMIN_PASSWORD=admin

volumes:
  redis-data:
  prometheus-data:
  grafana-data:
```

#### 5. Health Check Implementasyonu

Sağlık kontrolü mekanizmaları:

```c
#include <sys/socket.h>
#include <netinet/in.h>

// Health check HTTP endpoint'i
void setup_health_check_server(int port) {
    int server_fd = socket(AF_INET, SOCK_STREAM, 0);

    struct sockaddr_in address;
    address.sin_family = AF_INET;
    address.sin_addr.s_addr = INADDR_ANY;
    address.sin_port = htons(port);

    bind(server_fd, (struct sockaddr *)&address, sizeof(address));
    listen(server_fd, 10);

    while (running) {
        int client_fd = accept(server_fd, NULL, NULL);

        char buffer[1024];
        read(client_fd, buffer, sizeof(buffer));

        // Health check endpoint
        if (strncmp(buffer, "GET /health", 11) == 0) {
            int is_healthy = check_audio_processing_health();

            const char *response = is_healthy ?
                "HTTP/1.1 200 OK\r\nContent-Type: application/json\r\n\r\n"
                "{\"status\":\"healthy\",\"audio\":\"active\"}" :
                "HTTP/1.1 503 Service Unavailable\r\nContent-Type: application/json\r\n\r\n"
                "{\"status\":\"unhealthy\",\"audio\":\"inactive\"}";

            write(client_fd, response, strlen(response));
        }

        // Ready check endpoint
        if (strncmp(buffer, "GET /ready", 10) == 0) {
            int is_ready = check_audio_buffers_ready();

            const char *response = is_ready ?
                "HTTP/1.1 200 OK\r\nContent-Type: application/json\r\n\r\n"
                "{\"status\":\"ready\"}" :
                "HTTP/1.1 503 Service Unavailable\r\nContent-Type: application/json\r\n\r\n"
                "{\"status\":\"not_ready\"}";

            write(client_fd, response, strlen(response));
        }

        close(client_fd);
    }
}

// Sağlık kontrolü fonksiyonları
int check_audio_processing_health() {
    // Ses cihazı durumunu kontrol et
    if (!is_audio_device_active()) return 0;

    // Buffer durumunu kontrol et
    if (audio_buffer_underrun_count > 100) return 0;

    // CPU kullanımını kontrol et
    if (get_cpu_usage() > 90) return 0;

    // Bellek kullanımını kontrol et
    if (get_memory_usage() > 90) return 0;

    return 1;
}

int check_audio_buffers_ready() {
    // Tüm buffer'lar dolu mu?
    for (int i = 0; i < buffer_count; i++) {
        if (buffer_status[i] != BUFFER_READY) {
            return 0;
        }
    }
    return 1;
}
```

### API / Arayüz

#### COREMUSIC Container API Başlık Dosyası

```c
#ifndef COREMUSIC_CONTAINER_H
#define COREMUSIC_CONTAINER_H

#include <stdint.h>

// Docker Operations
CM_Status CM_Docker_Init(const char *socket_path);
CM_Status CM_Docker_CreateContainer(const char *name, const char *image);
CM_Status CM_Docker_StartContainer(const char *id);
CM_Status CM_Docker_StopContainer(const char *id);
CM_Status CM_Docker_RemoveContainer(const char *id);
CM_Status CM_Docker_InspectContainer(const char *id, char *info, size_t size);

// containerd Operations
CM_Status CM_Containerd_Init(const char *socket_path);
CM_Status CM_Containerd_PullImage(const char *image);
CM_Status CM_Containerd_CreateContainer(const char *id, const char *image);
CM_Status CM_Containerd_StartTask(const char *container_id);
CM_Status CM_Containerd_StopTask(const char *container_id);

// Kubernetes Operations
CM_Status CM_K8s_Init(const char *kubeconfig);
CM_Status CM_K8s_CreateDeployment(const char *name, int replicas);
CM_Status CM_K8s_UpdateDeployment(const char *name, int replicas);
CM_Status CM_K8s_DeleteDeployment(const char *name);
CM_Status CM_K8s_GetPodStatus(const char *name, char *status, size_t size);

// Docker Compose Operations
CM_Status CM_DockerCompose_Up(const char *project, const char *file);
CM_Status CM_DockerCompose_Down(const char *project);
CM_Status CM_DockerCompose_Scale(const char *project, const char *service, int count);
CM_Status CM_DockerCompose_PS(const char *project, char *output, size_t size);

// Health Check
CM_Status CM_HealthCheck_Register(const char *endpoint, int port);
CM_Status CM_HealthCheck_SetHealthy(int healthy);
CM_Status CM_HealthCheck_SetReady(int ready);
CM_Status CM_HealthCheck_GetStatus(char *status, size_t size);

#endif // COREMUSIC_CONTAINER_H
```

### Bağımlılıklar

#### Gereksinimler
- Docker 20.10 ve üzeri
- containerd 1.6 ve üzeri
- Kubernetes 1.24 ve üzeri
- Docker Compose v2 ve üzeri

#### Alt Katmanlar
- Linux Kernel (namespaces, cgroups)
- Network (bridge, overlay)
- Storage (overlayfs, volume)

#### Üst Katmanlar
- K1 Ses Motoru (konteyner içinde)
- K3 Uygulama Katmanı (konteyner içinde)
- CI/CD Pipeline

### Performans Metrikleri

| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| Konteyner oluşturma | < 5s | Belirlenecek |
| Konteyner başlatma | < 2s | Belirlenecek |
| Health check response | < 100ms | Belirlenecek |
| Image pull (cache) | < 1s | Belirlenecek |
| Scale-up time | < 30s | Belirlenecek |

### Güvenlik Notları

1. **Privileged Mode**: Sadece donanım erişimi gereken konteynerlerde
2. **Resource Limits**: Tüm konteynerler için memory ve CPU sınırları
3. **Network Policies**: Pod-to-Pod iletişimini sınırlama
4. **Security Context**: Capability dropping ve read-only root filesystem
5. **Image Scanning**: Güvenlik açığı taraması

### Durum: Implementasyon

**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. Docker image oluşturma ve optimize etme
2. containerd runtime entegrasyonu
3. Kubernetes deployment manifestleri
4. Health check ve monitoring
5. Auto-scaling yapılandırması

**Sonraki Adımlar**:
- Dockerfile yazımı ve multi-stage build
- containerd ile temel konteyner lifecycle
- Kubernetes test cluster kurulumu



> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:581-602` — satır: 22 (boş olmayan: 20).

#### K0.7 — Konteyner Runtime

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K0.7.1** | Container Runtime (3. katman) | container-runtime.md — 16 yaprak |
| K0.7.1.1 | Genel Bakış | container-runtime.md L11 |
| K0.7.1.2 | Teknik Detaylar | container-runtime.md L15 |
| K0.7.1.3 | API / Arayüz | container-runtime.md L414 |
| K0.7.1.4 | Bağımlılıklar | container-runtime.md L461 |
| K0.7.1.5 | Performans Metrikleri | container-runtime.md L479 |
| K0.7.1.6 | Güvenlik Notları | container-runtime.md L489 |
| K0.7.1.7 | Durum: Implementasyon | container-runtime.md L497 |
| K0.7.1.8 | Docker Engine Entegrasyonu | container-runtime.md L17 |
| K0.7.1.9 | containerd Entegrasyonu | container-runtime.md L53 |
| K0.7.1.10 | Kubernetes Entegrasyonu | container-runtime.md L113 |
| K0.7.1.11 | Docker Compose | container-runtime.md L245 |
| K0.7.1.12 | Health Check Implementasyonu | container-runtime.md L330 |
| K0.7.1.13 | COREMUSIC Container API Başlık Dosyası | container-runtime.md L416 |
| K0.7.1.14 | Gereksinimler | container-runtime.md L463 |
| K0.7.1.15 | Alt Katmanlar | container-runtime.md L469 |
| K0.7.1.16 | Üst Katmanlar | container-runtime.md L474 |



> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:319-342` — satır: 24 (boş olmayan: 17).

### 8. Ağ Stack (K0-08)

#### 8.1 Socket Seviyeleri

| Seviye | API | Kullanım |
|--------|-----|----------|
| Raw | BSD Sockets | Low-level network |
| TCP | TCP Sockets | HTTP, WebSocket |
| UDP | UDP Sockets | DNS, mDNS |
| Multicast | IGMP | DLNA, AirPlay |

---

### 9. Zaman Servisi (K0-09)

#### 9.1 Zaman Ölçümleri

| Metot | Çözünürlük | Platform |
|-------|-----------|----------|
| QueryPerformanceCounter | ~100ns | Windows |
| clock_gettime(CLOCK_MONOTONIC) | ~1ns | Linux/macOS |
| mach_absolute_time | ~1ns | macOS |
| rdtsc | ~1ns | Tümü (x86) |


## 3. Çalışma Ortamı Karşılaştırması (gömülü kaynaktan)

| Katman | Rol | Tipik kullanım | Dikkat |
|---|---|---|---|
| Docker Engine | tek düğüm çalışma | geliştirme/dağıtım | ayrıcalık ve cihaz erişimi |
| containerd | düşük seviye çalıştırıcı | üretim | sürüm sabitleme |
| Kubernetes | orkestrasyon | çok düğüm | sağlık/çekiliş davranışı |
| Docker Compose | çoklu servis tanımlı | ortam kurulumu | dosya/yolu eşleme |
| Health check | canlılık | restart kararı | yanlış negatif/olumlu |

> **Kanıt:** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` §1-§5.

## 4. Kenar Durumları

| # | Kenar durum | Koşul | Risk | Yaklaşım |
|---|---|---|---|---|
| E1 | Ses aygıtının konteynerde görünmemesi | cihaz geçirilmedi | çıkış yok | cihaz/volume geçirme |
| E2 | Konteyner yeniden planlanması | düğüm baskısı | oturum kaybı | kalıcı durum / yeniden bağlanma |
| E3 | Sağlık kontrolünün erken başarısız demesi | yavaş başlangıç | gereksiz restart | başlangıç gecikmesi eşiği |
| E4 | Bellek kotası aşımı | OOM kill | servis düşmesi | kota + ön tahsis |
| E5 | Ağ politikası değişimi | uzak ses/paket | bağlantı kesintisi | politika + sağlık kontrolü |

> **Kanıt:** gömülü `container-runtime.md` §3 (Kubernetes), §5 (Health Check).

## 5. Hata Modları

| # | Hata modu | Belirti | Sinyal | Sonuç |
|---|---|---|---|---|
| F1 | Sağlık kontrolü başarısız | döngüsel restart | restart sayacı | hizmet kararsızlığı |
| F2 | Cihaz/volume eksikliği | açılamama | başlangıç logu | servis yok |
| F3 | Kotasız bellek kullanımı | makine OOM | sistem bellek metriği | komşu servis etkilenir |
| F4 | Sürüm sürüklenmesi (drift) | ortam farkı | imaj etiketi | davranışı değişir |
| F5 | Ayrıcalık genişliği | güvenlik açığı | imaj denetimi | güvenlik olayı |

> **Kanıt:** gömülü `container-runtime.md` §"Güvenlik Notları", §"Performans Metrikleri".

## 6. Bağımlılıklar

- **Yukarı:** `[[bellek-yonetimi]]` (konteyner bellek kotası), `[[../k004-surec-ipc/index]]` (süreç modeli).
- **Yatay:** `[[../k016-linux-ses/index]]` (Linux ses yığını konteyner içinde), `[[../k014-surucu-yigin/index]]`.
- **Aşağı:** dağıtım/altyapı — bu vault'ta ayrı alan; `⚠️ VERIFICATION REQUIRED`.

## 7. Persona

| Görev | Persona |
|---|---|
| Konteyner tarifi ve sağlık kontrolü | `devops-engineer` |
| Cihaz/RT etkileşimi | `embedded-engineer` |
| Kaynak kotası ölçümü | `performance-engineer` |
| Ayrıcalık denetimi | `security-engineer` |

## 8. Kanıt Kataloğu

1. `Kanıt: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` — gömülü gövde.
2. `Kanıt: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:581-602` — K0.7 konteyner şeması.
3. `Kanıt: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:319-342` — ağ stacki.
4. `⚠️ VERIFICATION REQUIRED` — üretime alınmış sürüm/etiket bilgisi (vault'ta yok).
5. `⚠️ VERIFICATION REQUIRED` — konteyner içi ölçülmüş gecikme/kota metrikleri.

## 9. Doğrulama Durumu

- YAML/etiket/ürün bilgisi içeren iddialar gömülü kaynaktan gelir; doğrulanmayanlar işaretlidir.
- Üst dizin: `[[index]]`
