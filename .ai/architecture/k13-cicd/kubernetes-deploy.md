---
title: "Kubernetes Deployment"
layer: K13
category: "CI/CD"
date: 2026-09-20
version: "1.0.0"
---

# Kubernetes Deployment

## Genel Bakış

COREMUSIC projesi, container orchestration için Kubernetes kullanır. Deployment, Service, Ingress ve ConfigMap resource'ları ile production-ready bir cluster yapısı sağlanır. Rolling updates, auto-scaling, ve health check mekanizmaları ile zero-downtime deploy hedeflenir.

## Pipeline Akışı

```
Docker Image Push → Kubernetes Manifest Apply → Rolling Update → Health Check → Service Traffic Switch → Ready
```

## Teknik Detaylar

### Namespace Yapısı

```yaml
# Namespace definitions
apiVersion: v1
kind: Namespace
metadata:
  name: coremusic-production
  labels:
    env: production
    team: coremusic
---
apiVersion: v1
kind: Namespace
metadata:
  name: coremusic-staging
  labels:
    env: staging
    team: coremusic
```

### Deployment Configuration

```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: coremusic-app
  namespace: coremusic-production
  labels:
    app: coremusic
    tier: backend
spec:
  replicas: 3
  selector:
    matchLabels:
      app: coremusic
  strategy:
    type: RollingUpdate
    rollingUpdate:
      maxSurge: 1
      maxUnavailable: 0
  template:
    metadata:
      labels:
        app: coremusic
        tier: backend
    spec:
      containers:
        - name: coremusic
          image: ghcr.io/coremusic/coremusic:latest
          ports:
            - containerPort: 9000
              name: http
          env:
            - name: APP_ENV
              value: "production"
            - name: DB_HOST
              valueFrom:
                secretKeyRef:
                  name: coremusic-secrets
                  key: db-host
          resources:
            requests:
              cpu: "250m"
              memory: "256Mi"
            limits:
              cpu: "1000m"
              memory: "512Mi"
          livenessProbe:
            httpGet:
              path: /health
              port: 9000
            initialDelaySeconds: 30
            periodSeconds: 10
            timeoutSeconds: 5
          readinessProbe:
            httpGet:
              path: /ready
              port: 9000
            initialDelaySeconds: 5
            periodSeconds: 5
          lifecycle:
            preStop:
              exec:
                command: ["/bin/sh", "-c", "sleep 10"]
      terminationGracePeriodSeconds: 30
```

### Service Configuration

```yaml
apiVersion: v1
kind: Service
metadata:
  name: coremusic-service
  namespace: coremusic-production
spec:
  selector:
    app: coremusic
  ports:
    - port: 80
      targetPort: 9000
      protocol: TCP
      name: http
  type: ClusterIP
---
apiVersion: v1
kind: Service
metadata:
  name: coremusic-redis
  namespace: coremusic-production
spec:
  selector:
    app: redis
  ports:
    - port: 6379
      targetPort: 6379
  type: ClusterIP
```

### Ingress Configuration

```yaml
apiVersion: networking.k8s.io/v1
kind: Ingress
metadata:
  name: coremusic-ingress
  namespace: coremusic-production
  annotations:
    nginx.ingress.kubernetes.io/rewrite-target: /
    nginx.ingress.kubernetes.io/ssl-redirect: "true"
    nginx.ingress.kubernetes.io/proxy-body-size: "50m"
    cert-manager.io/cluster-issuer: "letsencrypt-prod"
spec:
  tls:
    - hosts:
        - coremusic.example.com
      secretName: coremusic-tls
  rules:
    - host: coremusic.example.com
      http:
        paths:
          - path: /
            pathType: Prefix
            backend:
              service:
                name: coremusic-service
                port:
                  number: 80
```

### HPA (Horizontal Pod Autoscaler)

```yaml
apiVersion: autoscaling/v2
kind: HorizontalPodAutoscaler
metadata:
  name: coremusic-hpa
  namespace: coremusic-production
spec:
  scaleTargetRef:
    apiVersion: apps/v1
    kind: Deployment
    name: coremusic-app
  minReplicas: 3
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

### ConfigMap

```yaml
apiVersion: v1
kind: ConfigMap
metadata:
  name: coremusic-config
  namespace: coremusic-production
data:
  APP_NAME: "COREMUSIC"
  APP_ENV: "production"
  CACHE_DRIVER: "redis"
  SESSION_DRIVER: "redis"
  QUEUE_CONNECTION: "redis"
```

### Resource Quotas

```yaml
apiVersion: v1
kind: ResourceQuota
metadata:
  name: coremusic-quota
  namespace: coremusic-production
spec:
  hard:
    requests.cpu: "4"
    requests.memory: "4Gi"
    limits.cpu: "8"
    limits.memory: "8Gi"
    pods: "20"
```

## Konfigürasyon

### Kustomize Overlay

```yaml
# kustomization.yaml (base)
apiVersion: kustomize.config.k8s.io/v1beta1
kind: Kustomization

resources:
  - deployment.yaml
  - service.yaml
  - ingress.yaml
  - hpa.yaml
  - configmap.yaml

commonLabels:
  app: coremusic
  managed-by: kustomize
```

### Helm Chart Values

```yaml
# values.yaml
replicaCount: 3

image:
  repository: ghcr.io/coremusic/coremusic
  tag: "latest"
  pullPolicy: Always

service:
  type: ClusterIP
  port: 80

ingress:
  enabled: true
  className: nginx
  hosts:
    - host: coremusic.example.com
      paths:
        - path: /
          pathType: Prefix

autoscaling:
  enabled: true
  minReplicas: 3
  maxReplicas: 10
  targetCPUUtilizationPercentage: 70
```

## Bağımlılıklar

- `kubernetes`: Container orchestration
- `nginx-ingress`: Ingress controller
- `cert-manager`: TLS certificate management
- `metrics-server`: HPA metrics
- `kustomize`: Configuration management

## Durum: Implementasyon

| Bileşen              | Durum       | Son Güncelleme |
|----------------------|-------------|-----------------|
| Deployment           | Hazır       | 2026-09-20      |
| Service              | Hazır       | 2026-09-20      |
| Ingress              | Hazır       | 2026-09-20      |
| HPA                  | Hazır       | 2026-09-20      |
| ConfigMap            | Hazır       | 2026-09-20      |
| Resource Quotas      | Hazır       | 2026-09-20      |
