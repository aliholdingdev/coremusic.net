---
title: "K12 İzleme Katmanı"
layer: K12
category: "İzleme"
date: 2026-09-20
---

# K12 İzleme Katmanı

## Genel Bakış

K12 İzleme Katmanı, COREMUSIC platformunun tüm bileşenlerini gerçek zamanlı olarak izleme, analiz etme ve raporlama sorumluluğunu taşır. Bu katman, uygulama metrikleri, altyapı durumu, hata takibi ve denetim günlüklerini merkezi olarak yönetir. İzleme verileri, performans optimizasyonu, arıza tespiti ve uyumluluk gereksinimleri için kritik öneme sahiptir.

## Monitoring Mimarisi

```
┌─────────────────────────────────────────────────────────────────┐
│                    K12 İZLEME KATMANI                           │
├─────────────────────────────────────────────────────────────────┤
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐         │
│  │  Prometheus   │  │   Grafana    │  │    Jaeger    │         │
│  │  Metrics      │  │  Dashboards  │  │   Tracing    │         │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘         │
│         │                 │                 │                   │
│  ┌──────┴─────────────────┴─────────────────┴───────┐         │
│  │              Merkezi Veri Toplama                  │         │
│  └──────────────────────┬───────────────────────────┘         │
│                         │                                      │
│  ┌──────────────────────┴───────────────────────────┐         │
│  │  Uygulama Metrikleri │ Altyapı Metrikleri │ Loglar │         │
│  └──────────────────────────────────────────────────┘         │
└─────────────────────────────────────────────────────────────────┘
```

## Temel Bileşenler

| Bileşen | Sorumluluk | Teknoloji |
|---------|-----------|-----------|
| Metrik Toplama | Performans verilerini toplama | Prometheus, StatsD |
| Dashboard | Görselleştirme ve raporlama | Grafana |
| Log Yönetimi | Log toplama ve analiz | ELK Stack |
| Hata Takibi | Hata yakalama ve analiz | Sentry |
| Dağıtık İzleme | Servisler arası izleme | Jaeger, OpenTelemetry |
| Uyarı Kuralları | Anomali tespiti ve bildirim | Alertmanager |

## Entegrasyon Noktaları

### K0 - İşletim Sistemi Katmanı
- Sistem metrikleri (CPU, RAM, disk)
- Process izleme
- Ağ istatistikleri

### K1 - Donanım Katmanı
- Sensör verileri
- Sıcaklık ve nem bilgisi
- Donanım sağlık durumu

### K2 - Sürücü Katmanı
- Donanım erişim metrikleri
- I/O performansı
- Sürücü hataları

### K3 - Ses Motoru Katmanı
- Ses işleme metrikleri
- Buffer durumu
- Latans bilgileri

### K5 - Veri Yönetimi Katmanı
- Veritabanı sorgu performansı
- Önbellek hit/miss oranları
- Bağlantı havuzu durumu

### K7 - Middleware Katmanı
- İstek/hata metrikleri
- Oran sınırlama istatistikleri
- Güvenlik olayları

### K8 - Servis Katmanı
- Servis yanıt süreleri
- Hata oranları
- Kullanıcı bağlantıları

## Metrik Kategorileri

### Klasik Metrikler (RED)
- **Rate**: İstek oranı (istek/saniye)
- **Errors**: Hata oranı ve türleri
- **Duration**: Yanıt süreleri (p50, p95, p99)

### Ek Metrikler (USE)
- **Utilization**: Kaynak kullanım oranları
- **Saturation**: Kuyruk boyutları ve doluluk
- **Errors**: Hata sayıları ve türleri

### İş Metrikleri
- Aktif kullanıcı sayısı
- İşlem başarı oranı
- İşlem süreleri

## Veri Saklama Politikası

| Veri Türü | Saklama Süresi | Çözünürlük |
|-----------|---------------|------------|
| Metrikler (yüksek) | 15 gün | 10s |
| Metrikler (orta) | 30 gün | 1m |
| Metrikler (düşük) | 90 gün | 5m |
| Metrikler (tarihsel) | 1 yıl | 1h |
| Loglar (hot) | 7 gün | - |
| Loglar (warm) | 30 gün | - |
| Loglar (cold) | 90 gün | - |

## Güvenlik ve Erişim Kontrolü

- Metrik endpointleri sadece iç ağdan erişilebilir
- Dashboard'lar Rol Tabanlı Erişim Kontrolü (RBAC) ile korunur
- Hassas metrikler (kullanıcı bilgileri) anonimize edilir
- API anahtarları ve token'lar güvenli saklanır

## Olay Yanıt Süreci

1. **Tespit**: Uyarı tetiklendiğinde otomatik bildirim
2. **Sınıflandırma**: Olayın kritiklik seviyesi belirlenir
3. **Yanıt**: SLA'ya göre müdahale süresi
4. **Çözüm**: Sorun giderilir ve doğrulanır
5. **Raporlama**: Kapsamlı olay raporu oluşturulur

## Monitoring Pipeline

```
Metrik Kaynağı → Collector → Processor → Storage → Query → Visualization
     │              │           │           │         │          │
  (App/Metrics)  (Agent)    (Transform)  (TSDB)   (PromQL)  (Grafana)
```

## Bağımlılıklar

| Bileşen | Versiyon | Amaç |
|---------|----------|------|
| Prometheus | 2.47+ | Metrik toplama ve saklama |
| Grafana | 10.0+ | Dashboard ve görselleştirme |
| Alertmanager | 0.26+ | Uyarı yönetimi |
| Jaeger | 1.50+ | Dağıtık izleme |
| OpenTelemetry | 1.0+ | Standart izleme API'leri |

## Durum: Implementasyon

**Aşama**: Planlama ve Tasarım
**Öncelik**: Yüksek
**Tahmini Süre**: 4 hafta
**Bağımlılıklar**: K0, K3, K5 katmanlarının metrik export etmesi gerekmektedir.
