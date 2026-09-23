---
title: "Container Runtime - İşletim Sistemi Katmanı"
layer: K0
category: "İşletim Sistemi"
date: 2026-09-20
version: 1.0.0
---

# Container Runtime

## Genel Bakış

Container Runtime modülü, COREMUSIC'ın konteyner tabanlı dağıtım ve çalışma zamanı altyapısını yönetir. Docker Engine, containerd ve Kubernetes entegrasyonu ile ses işleme uygulamalarının konteyner içinde çalıştırılmasını sağlar. Health check mekanizmaları ile yüksek kullanılabilirlik ve otomatik kurtarma özelliklerini destekler.

## Teknik Detaylar

### 1. Docker Engine Entegrasyonu

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

### 2. containerd Entegrasyonu

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

### 3. Kubernetes Entegrasyonu

Kubernetes, konteyner orkestrasyonu için:

```yaml
# coremusic-deployment.yaml
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
# coremusic-service.yaml
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
# coremusic-hpa.yaml
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

### 4. Docker Compose

Docker Compose, çoklu konteyner uygulamaları için:

```yaml
# docker-compose.yaml
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

### 5. Health Check Implementasyonu

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

## API / Arayüz

### COREMUSIC Container API Başlık Dosyası

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

## Bağımlılıklar

### Gereksinimler
- Docker 20.10 ve üzeri
- containerd 1.6 ve üzeri
- Kubernetes 1.24 ve üzeri
- Docker Compose v2 ve üzeri

### Alt Katmanlar
- Linux Kernel (namespaces, cgroups)
- Network (bridge, overlay)
- Storage (overlayfs, volume)

### Üst Katmanlar
- K1 Ses Motoru (konteyner içinde)
- K3 Uygulama Katmanı (konteyner içinde)
- CI/CD Pipeline

## Performans Metrikleri

| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| Konteyner oluşturma | < 5s | Belirlenecek |
| Konteyner başlatma | < 2s | Belirlenecek |
| Health check response | < 100ms | Belirlenecek |
| Image pull (cache) | < 1s | Belirlenecek |
| Scale-up time | < 30s | Belirlenecek |

## Güvenlik Notları

1. **Privileged Mode**: Sadece donanım erişimi gereken konteynerlerde
2. **Resource Limits**: Tüm konteynerler için memory ve CPU sınırları
3. **Network Policies**: Pod-to-Pod iletişimini sınırlama
4. **Security Context**: Capability dropping ve read-only root filesystem
5. **Image Scanning**: Güvenlik açığı taraması

## Durum: Implementasyon

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
