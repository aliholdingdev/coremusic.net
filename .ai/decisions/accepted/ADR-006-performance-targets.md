---
type: adr
category: performance
title: "ADR-006: Performance Targets & Benchmarks"
date: 2026-05-15
updated: 2026-08-08
status: frozen
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-006: Performance Targets & Benchmarks

**Status:** Frozen (değiştirilemez)
**Kategorisi:** Performance
**İlgili Agent:** Tüm agent'lar
**Frozen:** 2026-05-15 — ADR 001-037 arasında değiştirilemez

---

## 1. Amaç

Bu ADR, CoreMusic platformunun tüm katmanlarında (frontend, backend, audio, database) geçerli performans hedeflerini ve benchmark ölçüm metodolojisini tanımlar. Her bir metrik için hedef değerler, ölçüm yöntemleri ve ihlal durumunda yapılacak işlemler belirlenir.

CoreMusic; ev medya merkezi, araç içi bilgi-eğlence, profesyonel stüdyo ve NAS sunucu gibi çeşitli kullanım senaryolarında çalışacaktır. Her senaryonun farklı performans gereksinimleri vardır.

---

## 2. Bağlam

### 2.1 İş Problemi

CoreMusic'in çeşitli kullanım senaryolarında tutarlı performans sağlaması gerekiyor:

| Senaryo | Kritik Metrik | Hedef |
|---------|--------------|-------|
| Ev medya merkezi | Page load, TTFB | <2s, <200ms |
| Araç içi | Latency, response time | <100ms |
| Profesyonel stüdyo | Audio latency | <10ms (ASIO) |
| NAS sunucu | Concurrent connections | 100+ |
| İndirme servisi | Throughput | 10MB/s+ |

### 2.2 Teknik Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| PHP 8.4 | Backend stack |
| Vanilla JS | Frontend stack |
| MySQL 9 | Database |
| C++20 | Audio engine |
| ASIO | Düşük gecikmeli ses |
| WASAPI | Windows ses |

### 2.3 İlişkili Kararlar

| ADR | İlişki |
|-----|--------|
| ADR-001 | Vanilla JS + ITCSS |
| ADR-017 | DSP hardware mode |
| ADR-038 | 8.1 sound card |
| ADR-042 | MSA limit, vault standardı |

---

## 3. Karar

CoreMusic'te aşağıdaki performans hedefleri geçerli olacaktır. Bu hedefler tüm ortamlarda (development, staging, production) ölçülür ve raporlanır.

### 3.1 Karar Özeti

| Parametre | Değer |
|-----------|-------|
| TTFB | <200ms |
| API yanıt süresi | <100ms |
| Page load | <2s |
| LCP | <2.5s |
| CLS | <0.1 |
| INP | <200ms |
| Audio latency | <10ms (ASIO), <20ms (WASAPI) |
| DB sorgu | <100ms (95th percentile) |

---

## 4. Teknik Detaylar

### 4.1 Frontend Performans Hedefleri

| Metrik | Hedef | Ölçüm | Kaynak |
|--------|-------|-------|--------|
| TTFB | <200ms | Server Response | Web Vitals |
| FCP | <1.5s | First Contentful Paint | Web Vitals |
| LCP | <2.5s | Largest Contentful Paint | Web Vitals |
| CLS | <0.1 | Cumulative Layout Shift | Web Vitals |
| INP | <200ms | Interaction to Next Paint | Web Vitals |
| Page load | <2s | Tam sayfa yüklenme | Navigation Timing |
| JS parse | <50ms | JavaScript ayrıştırma | Performance API |
| CSS load | <30ms | CSS yükleme | Resource Timing |
| Image load | <100ms | Görüntü yükleme | Resource Timing |

### 4.2 Backend Performans Hedefleri

| Metrik | Hedef | Ölçüm | Kaynak |
|--------|-------|-------|--------|
| API yanıt | <100ms | Response time | APM |
| PHP execution | <50ms | Script execution | Xdebug |
| Database query | <100ms | Query time (95th) | MySQL slow log |
| Cache hit | >90% | Cache hit rate | APCu stats |
| Rate limit | 60 req/60s | Rate limit | ADR-013 |
| Memory usage | <128MB | Peak memory | PHP memory_get_peak |
| CPU usage | <50% | Peak CPU | top/htop |

### 4.3 Audio Performans Hedefleri

| Metrik | Hedef | Ölçüm | Kaynak |
|--------|-------|-------|--------|
| ASIO latency | <10ms | Buffer size | ASIO SDK |
| WASAPI latency | <20ms | Buffer size | Windows API |
| CoreAudio latency | <15ms | Buffer size | macOS API |
| ALSA latency | <15ms | Buffer size | Linux API |
| Audio buffer | 512 samples | Default buffer | ASIO config |
| Sample rate | 48kHz | Standart | ADR-038 |
| Bit depth | 32-bit float | Standart | ADR-017 |
| Ring buffer | lock-free | Atomic | C++20 |

### 4.4 Database Performans Hedefleri

| Metrik | Hedef | Ölçüm | Kaynak |
|--------|-------|-------|--------|
| Connection time | <50ms | Bağlantı süresi | PDO |
| Query time | <100ms | 95th percentile | Slow log |
| Write latency | <50ms | Write süresi | InnoDB status |
| Read latency | <20ms | 95th percentile | InnoDB status |
| Index hit rate | >95% | Index kullanımı | EXPLAIN |
| Table scan | <5% | Full scan oranı | Slow log |
| Lock wait | <10ms | Lock bekleme | InnoDB status |

### 4.5 Benchmark Metodolojisi

| Test | Araç | Sıklık | Hedef |
|------|------|--------|-------|
| Load test | k6 / Artillery | Haftalık | 100 concurrent |
| Stress test | k6 | Aylık | 500 concurrent |
| Spike test | k6 | Aylık | 1000 spike |
| Soak test | k6 | Üç aylık | 8 saat sürekli |
| Audio test | ASIO SDK | Her build | Latency <10ms |
| DB benchmark | sysbench | Aylık | TPS >1000 |

### 4.6 Ölçüm Araçları

| Araç | Kullanım | Platform |
|------|----------|----------|
| Lighthouse | Frontend audit | Chrome DevTools |
| Web Vitals | Core Web Vitals | Chrome Extension |
| k6 | Load testing | CLI |
| Artillery | Load testing | CLI |
| Xdebug | PHP profiling | IDE |
| MySQL slow log | DB sorgu analizi | MySQL |
| sysbench | DB benchmark | CLI |
| ASIO SDK | Audio latency | Windows |
| APCu | Cache istatistik | PHP |

### 4.7 Platform Bazlı Performans Hedefleri

| Platform | Kritik Metrik | Hedef |
|----------|--------------|-------|
| Windows (Tier 1) | ASIO latency | <10ms |
| Linux (Tier 2) | ALSA latency | <15ms |
| macOS (Tier 3) | CoreAudio latency | <15ms |
| Raspberry Pi (Tier 4) | I2S latency | <20ms |
| ReactOS (Tier 5) | WASAPI latency | <25ms |

### 4.8 Kullanım Senaryosu Bazlı Hedefler

| Senaryo | Kritik Metrik | Hedef | Öncelik |
|---------|--------------|-------|---------|
| Ev medya merkezi | Page load, TTFB | <2s, <200ms | HIGH |
| Araç içi | Latency, response | <100ms | CRITICAL |
| Profesyonel stüdyo | Audio latency | <10ms | CRITICAL |
| NAS sunucu | Concurrent | 100+ | MEDIUM |
| İndirme servisi | Throughput | 10MB/s+ | MEDIUM |
| Landing page | TTFB, LCP | <200ms, <2.5s | HIGH |
| Admin panel | API yanıt | <100ms | MEDIUM |
| Auth servisi | Response time | <50ms | HIGH |

### 4.9 Performans İzleme Dashboard'u

| Metrik | Kaynak | Güncelleme |
|--------|--------|-----------|
| TTFB | Server logs | Her istek |
| API yanıt | APM | Her istek |
| Cache hit率 | APCu stats | Her 60s |
| DB query time | Slow log | Her sorgu |
| Audio latency | ASIO SDK | Her buffer |
| Error rate | Error logs | Her hata |
| Memory usage | PHP | Her 60s |
| CPU usage | System | Her 60s |

### 4.10 Performans İhlali Prosedürü

| İhlal | Aksiyon | Süre | Sorumlu |
|-------|---------|------|---------|
| TTFB >200ms | CDN + cache optimizasyonu | 24 saat | Backend |
| API >100ms | Query optimizasyonu | 24 saat | Backend |
| LCP >2.5s | Image optimizasyonu | 48 saat | Frontend |
| Audio >10ms | Buffer optimizasyonu | Anlık | Embedded |
| DB >100ms | Index optimizasyonu | 24 saat | Data |

### 4.11 Performans Optimizasyonu Stratejileri

| Katman | Strateji | Araç |
|--------|----------|------|
| Frontend | Lazy loading | Intersection Observer |
| Frontend | Image optimization | WebP, AVIF |
| Frontend | Code splitting | Dynamic import |
| Frontend | Bundle optimization | Tree shaking |
| Backend | Query optimization | EXPLAIN, index |
| Backend | Response caching | APCu, Redis |
| Backend | Connection pooling | PDO persistent |
| Backend | Compression | gzip, brotli |
| Database | Index optimization | Composite index |
| Database | Query caching | MySQL query cache |
| Database | Partition | Table partitioning |
| Audio | Buffer tuning | ASIO buffer size |
| Audio | Thread priority | THREAD_PRIORITY_TIME_CRITICAL |
| Audio | SIMD | SSE2/AVX2/NEON |

### 4.12 Frontend Optimizasyon Detayları

| Teknik | Açıklama | Etki |
|--------|----------|------|
| Lazy loading | Görüntüleri ihtiyaca göre yükle | LCP düşer |
| WebP/AVIF | Modern görüntü formatları | Boyut %50 düşer |
| Critical CSS | Above-the-fold CSS | FCP düşer |
| Async JS | Async/defer script yükleme | TTFB düşer |
| CDN | İçerik dağıtım ağı | TTFB düşer |
| Minification | CSS/JS küçültme | Boyut düşer |
| Gzip | Sıkıştırma | Boyut %70 düşer |
| Preconnect | Önceden bağlantı | TTFB düşer |

### 4.13 Backend Optimizasyon Detayları

| Teknik | Açıklama | Etki |
|--------|----------|------|
| APCu caching | Veritabanı sorgusu önbellekleme | DB sorgusu %90 azalır |
| Query optimization | EXPLAIN ile sorgu analizi | Sorgu süresi düşer |
| Connection pooling | Bağlantı havuzu | Bağlantı overhead azalır |
| Response compression | gzip/brotli | Yanıt boyutu düşer |
| HTTP/2 | Multiplexing | Parallel yükleme |
| Keep-alive | Bağlantı yeniden kullanımı | Overhead azalır |

### 4.14 Database Optimizasyon Detayları

| Teknik | Açıklama | Etki |
|--------|----------|------|
| Composite index | Çoklu sütun indeksi | Sorgu %80 hızlanır |
| Covering index | Tüm sütunlar indeks içinde | Table scan sıfır |
| Query cache | Sorgu sonucu önbellekleme | Tekrar sorgu %100 hızlı |
| InnoDB buffer | Buffer pool boyutu | Disk I/O azalır |
| Table partition | Tablo bölümleme | Büyük tablo optimizasyonu |
| Normalization | BCNF | Veri tutarlılığı |

### 4.15 Audio Optimizasyon Detayları

| Teknik | Açıklama | Etki |
|--------|----------|------|
| Buffer tuning | ASIO buffer boyutu | Latency düşer |
| Thread priority | TIME_CRITICAL | Jitter azalır |
| SIMD | SSE2/AVX2/NEON | İşleme hızlanır |
| Lock-free | Atomic operations | Deadlock sıfır |
| Zero-allocation | Heap allocation yasak | Jitter sıfır |
| Cache alignment | 64-byte alignment | False sharing azalır |

### 4.16 Performance Budget

| Kaynak | Bütçe | Aşım |
|--------|-------|------|
| HTML | <50KB | Uyarı |
| CSS | <100KB | Uyarı |
| JS | <200KB | Uyarı |
| Görüntüler | <1MB | Uyarı |
| Font | <200KB | Uyarı |
| Toplam | <2MB | Hata |

### 4.17 Core Web Vitals Hedefleri

| Metrik | İyi | Orta | Kötü | Ölçüm |
|--------|-----|------|------|-------|
| LCP | <2.5s | 2.5-4s | >4s | Web Vitals |
| INP | <200ms | 200-500ms | >500ms | Web Vitals |
| CLS | <0.1 | 0.1-0.25 | >0.25 | Web Vitals |
| FCP | <1.8s | 1.8-3s | >3s | Navigation |
| TTFB | <800ms | 800-1800ms | >1800ms | Navigation |

### 4.18 Performans Monitoring Dashboard'u Detayı

| Panel | Metrik | Kaynak | Güncelleme |
|-------|--------|--------|-----------|
| Frontend | LCP, CLS, INP | Web Vitals | Her navigation |
| Backend | API yanıt, TTFB | APM | Her istek |
| DB | Query time, connections | MySQL | Her sorgu |
| Audio | Latency, buffer | ASIO | Her buffer |
| Cache | Hit rate, eviction | APCu | Her 60s |
| Error | 4xx, 5xx | Logs | Her hata |
| Memory | Usage, peak | System | Her 60s |
| CPU | Usage, load | System | Her 60s |

### 4.21 Performance Test Scriptleri Detayı

```bash
# k6 load test - 100 concurrent用户
k6 run --vus 100 --duration 60s \
  --out json=results.json \
  script.js

# k6 stress test - ramp up to 500
k6 run --vus 500 --duration 120s \
  --stage 30s:100 \
  --stage 60s:300 \
  --stage 90s:500 \
  script.js

# Lighthouse audit
lighthouse https://music.coremusic.net \
  --output json \
  --output-path=./reports/lighthouse.json

# Database benchmark
sysbench oltp_read_write \
  --tables=10 \
  --table-size=1000000 \
  --threads=16 \
  run

# Audio latency test
./audio_test \
  --buffer=512 \
  --sample-rate=48000 \
  --channels=2 \
  --duration=10
```

### 4.22 Performance Alerting

| Alert | Condition | Severity | Action |
|-------|-----------|----------|--------|
| High TTFB | TTFB > 500ms | WARNING | Investigate |
| Critical TTFB | TTFB > 2000ms | CRITICAL | Immediate fix |
| High LCP | LCP > 4s | WARNING | Optimize images |
| Critical LCP | LCP > 8s | CRITICAL | Emergency |
| High CLS | CLS > 0.25 | WARNING | Fix layout |
| High Error Rate | >5% errors | WARNING | Debug |
| Critical Error Rate | >10% errors | CRITICAL | Rollback |

### 4.23 Performance Report Formatı

```markdown
# Performance Report — [Tarih]

## Özet
- Test süresi: [X dakika]
- Toplam istek: [X]
- Başarı oranı: [X%]

## Frontend
- TTFB: [Xms] (hedef: <200ms) ✅/❌
- LCP: [Xs] (hedef: <2.5s) ✅/❌
- CLS: [X] (hedef: <0.1) ✅/❌

## Backend
- API yanıt: [Xms] (hedef: <100ms) ✅/❌
- Cache hit: [X%] (hedef: >90%) ✅/❌

## Database
- Query time: [Xms] (hedef: <100ms) ✅/❌
- Connection: [Xms] (hedef: <50ms) ✅/❌

## Audio
- ASIO latency: [Xms] (hedef: <10ms) ✅/❌

## Sonuç
- Genel durum: [PASS/FAIL]
- Öneriler: [liste]
```

### 4.24 Performance Regression Testing

| Test | Sıklık | Hedef | Araç |
|------|--------|-------|------|
| Lighthouse CI | Her PR | LCP <2.5s | Lighthouse |
| Bundle size | Her PR | <200KB JS | webpack-bundle-analyzer |
| Load test | Haftalık | 100 concurrent | k6 |
| DB benchmark | Aylık | TPS >1000 | sysbench |
| Audio test | Her build | Latency <10ms | ASIO SDK |

---

## 5. Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru | İhlal Sonucu |
|----------|----------|-------------|
| Performans testi atlama | Düzenli test | Performans düşüşü |
| Hedef belirtmeden kodlama | Hedef bazlı geliştirme | Ölçülmez performans |
| Benchmark olmadan deploy | Benchmark ile deploy | Production sorunları |
| Tek measurement | Çoklu measurement | Yanlış sonuç |
| Optimize etmeden önce ölç | Önce ölç, sonra optimize | Gereksiz optimizasyon |
| Tek platform testi | Çoklu platform testi | Platform sorunları |

---

## 6. Edge Cases

| Senaryo | Tetikleyici | Çözüm |
|---------|-------------|-------|
| TTFB aşımı | Yavaş server | CDN + cache |
| LCP aşımı | Büyük görüntüler | Lazy loading + WebP |
| CLS aşımı | Dinamik içerik | Reserved space |
| Audio buffer underrun | CPU spike | Fade-out → restart |
| DB connection exhaustion | Yüksek load | Connection pooling |
| Cache stampede | Cache expiration | Mutex ile single load |
| Rate limit aşımı | DDoS | Rate limiting + blocking |
| Memory leak | Uzun çalışma | Memory monitoring |

---

## 7. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | TTFB <200ms zorunlu | Kullanıcı deneyimi düşer |
| 2 | API <100ms zorunlu | Servis yavaşlar |
| 3 | Audio latency <10ms (ASIO) | Ses takılır |
| 4 | DB query <100ms zorunlu | Sorgu yavaşlar |
| 5 | Benchmark zorunlu deploy öncesi | Production sorunları |
| 6 | Ölçüm olmadan optimizasyon yasak | Gereksiz iş |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS + ITCSS | Frontend stack |
| [[ADR-013-rate-limiting-apcu]] | Rate limiting | 60 req/60s |
| [[ADR-017-dsp-hardware-mode]] | DSP hardware | Audio latency |
| [[ADR-022-database-hardened-security]] | DB security | Güvenlik overhead |
| [[ADR-038-8.1-sound-card-chip-selection]] | Sound card | Audio hardware |
| [[ADR-042-vault-restructuring-2026-08-03]] | Vault standardı | Bilgi kaynağı |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 Karar | [[CLAUDE.md]] §19 | Audio engine standartları |
| § 4.1 Frontend | [[ADR-001-vanilla-js-itcss]] | Frontend stack |
| § 4.3 Audio | [[ADR-017-dsp-hardware-mode]] | Audio |
| § 4.4 DB | [[ADR-040-database-authority]] | DB |
| § 5 Yasaklar | [[CLAUDE.md]] §21 | Yasak örüntüleri |
| § 6 Edge | [[brain.md]] §19 | Edge cases |
| § 7 Guardrails | [[CLAUDE.md]] §7 | Hard guardrails |
| § 8 ADR | [[ADR-038-8.1-sound-card-chip-selection]] | Hardware |

---

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **TTFB** | Time To First Byte — İlk bayt süresi |
| **FCP** | First Contentful Paint — İlk içerik boyama |
| **LCP** | Largest Contentful Paint — En büyük içerik boyama |
| **CLS** | Cumulative Layout Shift — Kümülatif düzen kayması |
| **INP** | Interaction to Next Paint — Etkileşimden sonraki boyama |
| **ASIO** | Audio Stream Input/Output — Düşük gecikmeli ses |
| **WASAPI** | Windows Audio Session API — Windows ses |
| **CoreAudio** | macOS ses API'si |
| **ALSA** | Advanced Linux Sound Architecture |
| **Benchmark** | Performans ölçüm testi |
| **Load test** | Yük testi |
| **Stress test** | Stres testi |
| **APCu** | APC User Cache — PHP önbellek |
| **Connection pooling** | Bağlantı havuzu |

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 11 |
| Frozen | 2026-05-15 |
| Frontend Metrics | 9 |
| Backend Metrics | 7 |
| Audio Metrics | 8 |
| DB Metrics | 7 |
| Benchmark Tests | 6 |
| Measurement Tools | 8 |
| Yasak Örüntüleri | 6 |
| Edge Cases | 8 |
| Hard Guardrails | 6 |
| ADR References | 6 |
| Cross References | 8 |
| Glossary Terms | 14 |
| Authority | SSOT |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
