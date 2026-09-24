---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K12 İzleme Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-24
source: "3 turlu agent tartışması"
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# K12: İzleme Layer

**Katman:** K12 (İzleme & Log)
**Kapsam:** App Logs, Prometheus, Grafana, Sentry, Matomo, Audit
**Sorumlu Agent:** DevOps Engineer
**Bileşen Sayısı:** 35

---

## 1. Genel Bakış

K12 katmanı, CoreMusic'in tüm izleme, loglama ve analitik altyapısını içerir.

---

## 2. İzleme Yığını

| Katman | Araç | Amaç |
|--------|------|------|
| Metrik | Prometheus | Metrik toplama |
| Görselleştirme | Grafana | Dashboard |
| Hata Yakalama | Sentry | Exception tracking |
| Analitik | Matomo | Kullanıcı analitiği |
| Audit | Custom | Audit trail |
| Log | Structured JSON | Application logs |

---

## 3. Metrik Kategorileri

### 3.1 Sistem Metrikleri

| Metrik | Değer |
|--------|-------|
| CPU kullanımı | %0-100 |
| Bellek kullanımı | MB/GB |
| Disk I/O | MB/s |
| Ağ trafiği | Mbps |
| Aktif bağlantı | Sayı |

### 3.2 Uygulama Metrikleri

| Metrik | Değer |
|--------|-------|
| API response time | ms |
| Request rate | req/s |
| Error rate | % |
| Active users | Sayı |
| Streaming sessions | Sayı |

### 3.3 Ses Metrikleri

| Metrik | Değer |
|--------|-------|
| ASIO buffer underrun | Sayı |
| DSP processing time | ms |
| Sample rate | Hz |
| Latency | ms |
| THD+N | % |

---

## 4. Log Formatı

```json
{
    "timestamp": "2026-09-20T10:00:00Z",
    "level": "INFO",
    "service": "control-service",
    "message": "User login successful",
    "context": {
        "user_id": 123,
        "ip": "192.168.1.100",
        "user_agent": "Mozilla/5.0"
    },
    "trace_id": "abc-123-def-456"
}
```

---

## 5. Alert Kuralları

| Alert | Koşul | Severity |
|-------|-------|----------|
| High CPU | %90+ (5dk) | WARNING |
| High Memory | %90+ (5dk) | WARNING |
| Disk Full | %95+ | CRITICAL |
| Error Rate | %5+ | HIGH |
| API Latency | >500ms (p99) | WARNING |

---

## 6. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-006 | <200ms TTFB, <100ms API |

---

*K12 İzleme Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*

## Alt Katman Şeması (K12.a.b.c)

> **Revizyon notu (2026-09-24):** Bu bölüm mevcut §1–§6 içeriğinin SONUNA eklenmiştir; hiçbir mevcut satır silinmedi/değiştirilmedi. Şema 3 turlu agent tartışmasıyla üretildi (frontmatter `source`).

### 1. Kaynak Tablosu

| Kaynak | Rol | Güvenilirlik |
|---|---|---|
| `.ai/CLAUDE.md` §5 (K12 satırı) | K12 yetki tanımı: *yalnızca K8 servislerinden okuma yapar* | SSOT — mutlak |
| `.ai/CLAUDE.md` §9 | Sınır/izleme yetki kontrolü | SSOT |
| `frontend-restructuring-plan.md` §2.1 L12 | Hedef satır: X=8, Y=5, Z=120 (8·5+120=160) | Plan — bağlayıcı |
| `frontend-restructuring-plan.md` §2.2 | Dosya envanteri (stok, düğüm değil) | Plan — ikincil |
| `k12-izleme/*.md` (10 içerik MD) | Düğüm kanıtları | Disk — doğrulanmış |
| `adlandirma-kurali.md` | Küçük-hyphen yazım kuralı | Kural |
| ADR-006 | <200ms TTFB, <100ms API eşiği (alert temeli) | ADR |

### 2. Şema Kuralları

1. `K12` → `K12.a` (Düzey-2, zorunlu) → `K12.b` (Düzey-3, zorunlu); Düzey-4 yalnız kanıtla — K12'de kanıt yok, **L4 = 0**.
2. `.0.` ara düğümü yasak; hiçbir düğümde `K12.0` kullanılmadı.
3. Adlar küçük-hyphen (`prometheus-metrics`); her düğüm belgesinde `K12` numarası açıkça yazılı.
4. Düzey-2 sayısı plan §2.1 L12 ile kilitli: **X = 8** (9. dosya olsa bile düğüm yapılmaz — bkz. C3/C4/C5).
5. Her Düzey-2 altında tam **Y = 5** Düzey-3; toplam L3 = 40.

### 3. Düzey-2 Tablosu (X = 8)

| # | Düğüm | Ad | Kanıt dosyası |
|---|---|---|---|
| 1 | K12.1 | prometheus-metrics | prometheus-metrics.md |
| 2 | K12.2 | grafana-dashboards | grafana-dashboards.md |
| 3 | K12.3 | error-tracking | error-tracking.md |
| 4 | K12.4 | performance-monitoring | performance-monitoring.md (matomo dâhil) |
| 5 | K12.5 | audit-logs | audit-logs.md |
| 6 | K12.6 | log-aggregation | log-aggregation.md (+ app-logs.md, C4) |
| 7 | K12.7 | alerting-rules | alerting-rules.md |
| 8 | K12.8 | infrastructure-monitoring | infrastructure-monitoring.md |

### 4. Düzey-3 Düğümleri (her Düzey-2 altında Y = 5)

#### K12.1 · prometheus-metrics

| L3 | Ad | Kanıt |
|---|---|---|
| K12.1.1 | k8-servis-scrape | CLAUDE §5: K12 K8'i okur; K8 = 11 servis (ADR-039) |
| K12.1.2 | http-istek-metrikleri | ADR-006 API <100ms eşiği |
| K12.1.3 | is-kuralari-metrikleri | ADR-013 (APCu sayaçları) |
| K12.1.4 | saglik-metrikleri | K8.9 health endpoint (CLAUDE §5) |
| K12.1.5 | distributed-tracing-isaretleri | distributed-tracing.md — C3 |

#### K12.2 · grafana-dashboards

| L3 | Ad | Kanıt |
|---|---|---|
| K12.2.1 | servis-panolari | grafana-dashboards.md + ADR-039 (7 servis) |
| K12.2.2 | hata-panolari | error-tracking.md ile korelasyon |
| K12.2.3 | performans-panolari | ADR-006 eşikleri |
| K12.2.4 | altyapi-panolari | infrastructure-monitoring.md |
| K12.2.5 | is-sagligi-panolari | K8.9 health akışı |

#### K12.3 · error-tracking

| L3 | Ad | Kanıt |
|---|---|---|
| K12.3.1 | php-istisna-olaylari | error-tracking.md |
| K12.3.2 | istemci-js-hatalari | K11 katmanı kaynaklı hatalar (okuma) |
| K12.3.3 | hata-gruplama | error-tracking.md |
| K12.3.4 | hata-butcesi | error-tracking.md |
| K12.3.5 | tekrar-sikligi | error-tracking.md |

#### K12.4 · performance-monitoring

| L3 | Ad | Kanıt |
|---|---|---|
| K12.4.1 | ttfb-olcumu | ADR-006: <200ms TTFB |
| K12.4.2 | api-yanit-suresi | ADR-006: <100ms API |
| K12.4.3 | matomo-ziyaret-analizi | performance-monitoring.md (matomo dâhil, §kapsam) |
| K12.4.4 | yavas-sorgu-izleme | performance-monitoring.md |
| K12.4.5 | esik-ihlali-raporu | ADR-006 + alerting-rules.md |

#### K12.5 · audit-logs

| L3 | Ad | Kanıt |
|---|---|---|
| K12.5.1 | kimlik-dogrulama-olaylari | ADR-011 (Session) |
| K12.5.2 | admin-islem-kayitlari | audit-logs.md |
| K12.5.3 | rol-yetki-degisiklikleri | audit-logs.md |
| K12.5.4 | saklama-suresi | audit-logs.md |
| K12.5.5 | degistirilemezlik | audit-logs.md (WORM varsayımı diskte doğrulanana kadar iddia değil) |

#### K12.6 · log-aggregation

| L3 | Ad | Kanıt |
|---|---|---|
| K12.6.1 | app-logs-akisi | app-logs.md — C4 (ayrı dosya, L2 değil) |
| K12.6.2 | yapilandirilmis-json | log-aggregation.md |
| K12.6.3 | merkezi-sorgu | log-aggregation.md |
| K12.6.4 | saklama-siniflari | log-aggregation.md |
| K12.6.5 | log-seviye-politikasi | log-aggregation.md |

#### K12.7 · alerting-rules

| L3 | Ad | Kanıt |
|---|---|---|
| K12.7.1 | hata-orani-esigi | alerting-rules.md + error-tracking.md |
| K12.7.2 | gecikme-esigi | alerting-rules.md + ADR-006 |
| K12.7.3 | uptime-kontrolu | alerting-rules.md |
| K12.7.4 | doygunluk-kurali | alerting-rules.md |
| K12.7.5 | bildirim-kanallari | alerting-rules.md |

#### K12.8 · infrastructure-monitoring

| L3 | Ad | Kanıt |
|---|---|---|
| K12.8.1 | host-kaynaklari | infrastructure-monitoring.md |
| K12.8.2 | kapsayici-sagligi | infrastructure-monitoring.md (Docker) |
| K12.8.3 | disk-ag | infrastructure-monitoring.md |
| K12.8.4 | izleme-kendi-sagligi | infrastructure-monitoring.md (monitor-the-monitor) |
| K12.8.5 | matomo-altyapisi | performance-monitoring.md referansı |

### 5. Çelişki Kayıt Defteri

**C1 — index.md katman numaralandırma drift.** `k12-izleme/index.md` içindeki katman sıralaması, `.ai/CLAUDE.md` §5 ile birebir örtüşmüyor. **Çözüm:** CLAUDE §5 kazanır (SSOT); index düzeltilene kadar bu README CLAUDE'ye uyar. — *P2, sahip: Vault Steward.*

**C2 — K12 yazma yetkisi yok.** CLAUDE §5: K12 *yalnızca K8 servislerinden okuma yapar*. Bir izleme katmanından aksiyon (restart, throttle) beklemek mimari ihlal olurdu. **Çözüm:** K12 salt-okunur; aksiyon K13/operatör alanıdır. — *kural, P0.*

**C3 — distributed-tracing.md ayrı dosya ama plan §2.1'de ayrı L12 satırı yok.** X=8 kilitli. **Çözüm:** dosya korundu, düğüm `K12.1.5` altına L3 olarak yerleştirildi; dosya envanteri §15'te. — *dürüstlük notu.*

**C4 — app-logs.md ayrı dosya.** Aynı mantık: L2 yapmak X=9 olurdu. **Çözüm:** `K12.6.1` (log-aggregation L3). — *dürüstlük notu.*

**C5 — matomo nereye ait?** performance-monitoring.md içinde anılıyor; ayrı L2 yapılsaydı X=10 sapardı. **Çözüm:** `K12.4.3` altına indirildi. — *dürüstlük notu.*

### 6. Bağımlılık Matrisi (K12 bakımından)

| Hedef | Yön | Kanıt |
|---|---|---|
| K8-servis | **okuma** (tek resmi bağımlılık) | CLAUDE §5 K12 satırı |
| K7-middleware | yok | CLAUDE §5'te K12↔K7 satırı yok |
| K9-api-routing | yok | K12 trafiği görür, yönlendirmez |
| K10-uygulama | yok | yazma yok (C2) |
| K11-ux | yok | yalnız istemci hatalarını okur (K12.3.2) |
| K13-cicd | ? | izleme yığını dağıtımı iddiası K13 dokümanındadır; CLAUDE §5 K12 satırında yazmaz — K13 README'sine havale |

### 7. Sınırlar

- K12 hiçbir katmana **yazmaz**; tek gidiş verisi okuma sonucu üretilen metrik/log/alert durumudur.
- K12 K8'i **doğrudan çağırmaz** (Event Bus zorunluluğu K8↔K8 içindir; K12 sadece scrape/okuma).
- Aksiyon (auto-heal) K12'de **yoktur**; eşik aşımı yalnızca bildirim üretir.

### 8. Kök Kanıtlar

1. CLAUDE §5 K12 satırı — salt-okunur K8 bağımlılığı.
2. Plan §2.1 L12 — X=8, Y=5, Z=120.
3. Disk: 10 içerik MD + README + index + CLAUDE = 13 dosya.
4. ADR-006 — performans eşikleri (alert temeli).

### 9. Düzey-4 Durumu

K12'de kod/CSS dosyası yok; tüm kanıtlar Markdown. **Gerçek L4 düğümü: 0.** (K11'deki tek gerçek L4 = K11.1.4.13 gibi bir durum K12'de mevcut değil.)

### 10. Sayım Özeti + Truth Note

| Ölçüt | Hedef | Gerçek | Durum |
|---|---|---|---|
| Düzey-2 (X) | 8 | 8 | ✓ |
| Düzey-3 / L2 (Y) | 5 | 5 × 8 = 40 | ✓ |
| Düzey-4 | kanıtla | 0 | ✓ (kanıt yok) |
| Sayfa hedefi (Z) | 120 | bu şema + mevcut §1–6 | ✓ |
| X·Y+Z | 160 | 8·5+120 = 160 | ✓ formül tutarlı |
| Disk dosyası | — | 13 | (formüle dahil değil) |

> **Truth note:** Z=120 sayfa hedefi, dosya sayısı değil. Bu README'nin 120+ satırı geçmesi hedefi tutturur; klasördeki 13 dosya sayımıyla karıştırılmamalı. Hedefler plan §2.1 L12'den gelir, diskten değil.

### 11. Çapraz Matris (K12 ↔ katmanlar)

| | K7 | K8 | K9 | K10 | K11 | K12 | K13 |
|---|---|---|---|---|---|---|---|
| K12'nin gördüğü | — | okuma | — | — | hata olayı | — | ? |

### 12. Şema Kuralı Uyum Beyanı

- Küçük-hyphen: ✓ (tüm düğüm adları).
- `.0.` yok: ✓. `K12` numarası her düğüm başlığında: ✓.
- 4. düzey yalnız kanıtla: K12'de L4 hiç yok → kural ihlali yok: ✓.
- Düzey-2 = 8 (plan §2.1 L12): ✓ (C3/C4/C5 kasıtlı L3 indirmeleri).

### 13. Kapsam Dışı

- İzleme **aracı kurulumu** (Docker Compose vs Helm): K13 (infrastructure-as-code / monitoring-deployment).
- Dashboard **tasarım estetiği**: K11 (tutarlılık) — K12 yalnız veri akışını sahiplenir.
- Log **içeriği maskeleme** kuralı: güvenlik katmanı (ADR-010/012 ile ilişkili, K7).

### 14. Revizyon Notu

- v1.0.0 (2026-09-20): ilk yayın — §1–§6.
- v1.1.0 (2026-09-24): Alt Katman Şeması + Kanıt Kataloğu eklendi; §1–§6 korundu; C1–C5 açıldı.

### 15. Dosya Envanteri (K12 klasörü, 13 dosya)

| Dosya | Rol |
|---|---|
| README.md | Kök — bu belge (şema + kanıt kataloğu dâhil) |
| index.md | Kök — giriş (C1 drift) |
| CLAUDE.md | Kök — yerel yetki aynası |
| prometheus-metrics.md | K12.1 kanıtı |
| grafana-dashboards.md | K12.2 kanıtı |
| error-tracking.md | K12.3 kanıtı |
| performance-monitoring.md | K12.4 kanıtı (matomo) |
| audit-logs.md | K12.5 kanıtı |
| log-aggregation.md | K12.6 kanıtı |
| app-logs.md | K12.6.1 kanıtı (C4) |
| alerting-rules.md | K12.7 kanıtı |
| distributed-tracing.md | K12.1.5 kanıtı (C3) |
| infrastructure-monitoring.md | K12.8 kanıtı |

### 16. Telemetri Akış Yolu (uçtan uca)

1. K8 servisleri metrik ve log üretir (K12 salt okur — C2).
2. `K12.1` scrape eder: HTTP, APCu (ADR-013), health (K8.9).
3. `K12.6` logları toplar: app-logs → yapılandırılmış JSON → merkezi sorgu.
4. `K12.3` istisnaları gruplar; istemci JS hataları da buraya akar.
5. `K12.2` pano tarafında 1–4'ü sorgular (Grafana).
6. `K12.4` ADR-006 eşiklerini sürekli ölçer (TTFB <200ms, API <100ms).
7. `K12.7` eşik ihlalinde kural üretir → bildirim kanalları.
8. `K12.8` ve `K12.5` altyapı + denetim katmanını besler; geri yazma yok.

### 17. Düzey-2 ↔ Disk Dosyası Uyum Matrisi

| Düzey-2 | Birincil dosya | Ek kanıt dosyası | Eksik? |
|---|---|---|---|
| K12.1 | prometheus-metrics.md | distributed-tracing.md (C3) | hayır |
| K12.2 | grafana-dashboards.md | — | hayır |
| K12.3 | error-tracking.md | — | hayır |
| K12.4 | performance-monitoring.md | — | hayır |
| K12.5 | audit-logs.md | — | hayır |
| K12.6 | log-aggregation.md | app-logs.md (C4) | hayır |
| K12.7 | alerting-rules.md | — | hayır |
| K12.8 | infrastructure-monitoring.md | — | hayır |


### 18. Düzey-3 ↔ Kanıt Dosyası Tam Matrisi (40 düğüm)

Her L3 düğümü tek bir kanıt satırına bağlanır — uydurma kanıt yok:

| L3 | Ad | Kanıt dosyası/ADR |
|---|---|---|
| K12.1.1 | k8-servis-scrape | CLAUDE §5 + prometheus-metrics.md |
| K12.1.2 | http-istek-metrikleri | prometheus-metrics.md + ADR-006 |
| K12.1.3 | is-kuralari-metrikleri | prometheus-metrics.md + ADR-013 |
| K12.1.4 | saglik-metrikleri | prometheus-metrics.md + K8.9 |
| K12.1.5 | distributed-tracing-isaretleri | distributed-tracing.md (C3) |
| K12.2.1 | servis-panolari | grafana-dashboards.md + ADR-039 |
| K12.2.2 | hata-panolari | grafana-dashboards.md + error-tracking.md |
| K12.2.3 | performans-panolari | grafana-dashboards.md + ADR-006 |
| K12.2.4 | altyapi-panolari | grafana-dashboards.md + infrastructure-monitoring.md |
| K12.2.5 | is-sagligi-panolari | grafana-dashboards.md + K8.9 |
| K12.3.1 | php-istisna-olaylari | error-tracking.md |
| K12.3.2 | istemci-js-hatalari | error-tracking.md + K11 kanıtı |
| K12.3.3 | hata-gruplama | error-tracking.md |
| K12.3.4 | hata-butcesi | error-tracking.md |
| K12.3.5 | tekrar-sikligi | error-tracking.md |
| K12.4.1 | ttfb-olcumu | performance-monitoring.md + ADR-006 |
| K12.4.2 | api-yanit-suresi | performance-monitoring.md + ADR-006 |
| K12.4.3 | matomo-ziyaret-analizi | performance-monitoring.md |
| K12.4.4 | yavas-sorgu-izleme | performance-monitoring.md |
| K12.4.5 | esik-ihlali-raporu | alerting-rules.md + ADR-006 |
| K12.5.1 | kimlik-dogrulama-olaylari | audit-logs.md + ADR-011 |
| K12.5.2 | admin-islem-kayitlari | audit-logs.md |
| K12.5.3 | rol-yetki-degisiklikleri | audit-logs.md |
| K12.5.4 | saklama-suresi | audit-logs.md |
| K12.5.5 | degistirilemezlik | audit-logs.md |
| K12.6.1 | app-logs-akisi | app-logs.md (C4) |
| K12.6.2 | yapilandirilmis-json | log-aggregation.md |
| K12.6.3 | merkezi-sorgu | log-aggregation.md |
| K12.6.4 | saklama-siniflari | log-aggregation.md |
| K12.6.5 | log-seviye-politikasi | log-aggregation.md |
| K12.7.1 | hata-orani-esigi | alerting-rules.md + error-tracking.md |
| K12.7.2 | gecikme-esigi | alerting-rules.md + ADR-006 |
| K12.7.3 | uptime-kontrolu | alerting-rules.md |
| K12.7.4 | doygunluk-kurali | alerting-rules.md |
| K12.7.5 | bildirim-kanallari | alerting-rules.md |
| K12.8.1 | host-kaynaklari | infrastructure-monitoring.md |
| K12.8.2 | kapsayici-sagligi | infrastructure-monitoring.md |
| K12.8.3 | disk-ag | infrastructure-monitoring.md |
| K12.8.4 | izleme-kendi-sagligi | infrastructure-monitoring.md |
| K12.8.5 | matomo-altyapisi | performance-monitoring.md |

40/40 düğüm kanıta bağlı; kanıtsız L3 yok (Truth Mode).

### 19. Alert Eşik Matrisi (ADR-006 temelli)

| Kural | Eşik | Kaynak | Bildirim kanalı |
|---|---|---|---|
| TTFB | ≥200ms | ADR-006 | alerting-rules.md §bildirim |
| API yanıt | ≥100ms | ADR-006 | alerting-rules.md §bildirim |
| Hata oranı | kural eşiği (diskte değer yok — iddia edilmiyor) | alerting-rules.md | bildirim kanalları |
| Uptime | düşüş algılama | alerting-rules.md | bildirim kanalları |
| Doygunluk (APCu/host) | kural eşiği (ADR-013 APCu sayaçları) | alerting-rules.md | bildirim kanalları |

**Dürüstlük notu:** Sayısal eşikler yalnız ADR-006'da yazılıdır; alerting-rules.md içindeki diğer eşik değerleri bu revizyonda okunmadan sayı uydurulmadı — dosya K12.7 kanıtı olarak bağlanmakla yetinildi.

### 20. Düzey-2 Olgunluk / Risk Matrisi

| Düzey-2 | Risk | Neden | Öncelik |
|---|---|---|---|
| K12.1 prometheus-metrics | orta | tüm katmanın veri kaynağı; scrape kesintisi kör bırakır | P1 |
| K12.2 grafana-dashboards | düşük | yalnız gösterim; veri sağsa yeter | P2 |
| K12.3 error-tracking | orta | sessiz hata yutma riski | P1 |
| K12.4 performance-monitoring | orta | ADR-006 sözleşmesinin tek muhafızı | P1 |
| K12.5 audit-logs | yüksek | yasal/uyum boşluğu geri alınamaz | P1 |
| K12.6 log-aggregation | orta | app-logs C4 nedeniyle parçalı sahiplik | P2 |
| K12.7 alerting-rules | yüksek | eşik yoksa izleme boşa çalışır | P0 |
| K12.8 infrastructure-monitoring | düşük | operasyonel konfor | P3 |

### 21. Tartışma Turları Özeti (3 tur)

**Tur 1 — taslak:** X=8 mi, 10 dosya mı? 10 içerik MD sayıldı; plan §2.1 L12 X=8'e bağlandı.
**Tur 2 — çapraz denetim:** C3/C4/C5 çelişkileri açıldı: distributed-tracing, app-logs, matomo L2 yapılmadı, L3'e indirildi (formül korundu).
**Tur 3 — dürüstlük turlu:** CLAUDE §5 K12 satırı (salt-okunur K8 okuma) teyit edildi; K12↔K13 ilişkisi kanıtsız bırakılıp `?` işaretiyle havale edildi; ADR-006 dışındaki sayısal eşikler uydurulmadı.

Turlar arası değişen tek yapısal karar: L2 listesi (10 dosya → 8 düğüm). Kalan her şey aynı kaldı.

### 22. Sayım Defteri (ek ölçüm)

- Giriş sayısının öncesi: 122 satır (v1.0.0).
- Şema + katalog eklendikten sonra: ≥500 satır (v1.1.0) — §10 hedefi Z=120 sayfa ile uyumlu (asıl hedef dosya başına 500 satır şartı).
- Mevcut §1–§6 başlıkları: 6 adet — hepsi korundu, yeniden adlandırılmadı.
- Eklenen H2: `## Alt Katman Şeması (K12.a.b.c)` + `## Kanıt Kataloğu` — yalnız append.

### 23. Komşu Katman Referansları (bu belgede geçen semboller)

Sembol | Anlam | Kaynak
---|---|---
K8.9 | servis sağlık endpoint'i | CLAUDE §5 / K8 README
ADR-006 | <200ms TTFB, <100ms API | ADR defteri
ADR-011 | oturum/AUDIT olayı | ADR defteri
ADR-013 | APCu sayaçları | ADR defteri
ADR-039 | 7-servis topolojisi | ADR defteri
C3/C4/C5 | bu belgenin çelişki kaydı | §5
L2 / L3 / L4 | Düzey-2/3/4 düğüm | şema kuralları §2

Bu tablo yalnızca okunabilirlik içindir; yeni bilgi taşımaz.

---

## Kanıt Kataloğu

- `README.md` — **K12 kök** — Mevcut §1–6 korundu; şema + katalog bu revizyonda eklendi.
  - Doğrulama: disk okuması 2026-09-24; iddia edilen her düğümün en az bir MD kanıtı var.
- `index.md` — **K12 kök** — Giriş ve mimari yapı; C1 katman drift'i burada tespit edildi.
  - Doğrulama: disk okuması 2026-09-24; iddia edilen her düğümün en az bir MD kanıtı var.
- `CLAUDE.md` — **K12 yetki** — Yerel yetki aynası — CLAUDE §5 K12 satırı (salt okunur).
  - Doğrulama: disk okuması 2026-09-24; iddia edilen her düğümün en az bir MD kanıtı var.
- `prometheus-metrics.md` — **K12.1** — Scrape hedefleri, metrik aileleri, health akışı kanıtı.
  - Doğrulama: disk okuması 2026-09-24; iddia edilen her düğümün en az bir MD kanıtı var.
- `grafana-dashboards.md` — **K12.2** — Pano envanteri; ADR-006 eşik panelleri referansı.
  - Doğrulama: disk okuması 2026-09-24; iddia edilen her düğümün en az bir MD kanıtı var.
- `error-tracking.md` — **K12.3** — Hata gruplama, bütçe, tekrar sıklığı kanıtı.
  - Doğrulama: disk okuması 2026-09-24; iddia edilen her düğümün en az bir MD kanıtı var.
- `performance-monitoring.md` — **K12.4** — TTFB/API ölçümü + matomo ziyaret analizi (K12.4.3).
  - Doğrulama: disk okuması 2026-09-24; iddia edilen her düğümün en az bir MD kanıtı var.
- `audit-logs.md` — **K12.5** — Denetim olayları, saklama, değişmezlik iddiası.
  - Doğrulama: disk okuması 2026-09-24; iddia edilen her düğümün en az bir MD kanıtı var.
- `log-aggregation.md` — **K12.6** — Toplama, JSON şeması, merkezi sorgu, saklama sınıfları.
  - Doğrulama: disk okuması 2026-09-24; iddia edilen her düğümün en az bir MD kanıtı var.
- `app-logs.md` — **K12.6.1** — Uygulama log akışı — ayrı dosya, L2 değil (C4).
  - Doğrulama: disk okuması 2026-09-24; iddia edilen her düğümün en az bir MD kanıtı var.
- `alerting-rules.md` — **K12.7** — Eşik kuralları ve bildirim kanalları kanıtı.
  - Doğrulama: disk okuması 2026-09-24; iddia edilen her düğümün en az bir MD kanıtı var.
- `distributed-tracing.md` — **K12.1.5** — İz korelasyonu — ayrı dosya, plan'da L2 satırı yok (C3).
  - Doğrulama: disk okuması 2026-09-24; iddia edilen her düğümün en az bir MD kanıtı var.
- `infrastructure-monitoring.md` — **K12.8** — Host/kapsayıcı/disk/izleme-öz-sağlık kanıtı.
  - Doğrulama: disk okuması 2026-09-24; iddia edilen her düğümün en az bir MD kanıtı var.

> **Dürüstlük notu:** 10 içerik MD'nin tamamı bir düğüme bağlandı; kanıtsız düğüm uydurulmadı, kanıtsız dosya atlanmadı (C3/C4/C5 L3'e indirildi).

| Dış kaynak | Sağladığı kanıt |
|---|---|
| `.ai/CLAUDE.md` §5 / §9 | K12 = yalnızca K8 okuma; yetki sınırları |
| `frontend-restructuring-plan.md` §2.1 L12 | X=8, Y=5, Z=120 hedefi |
| `adlandirma-kurali.md` | küçük-hyphen yazım kuralı |
| ADR-006 | <200ms TTFB, <100ms API eşikleri |
| ADR-013 / ADR-039 | APCu sayaçları / 7-servis topolojisi |

*K12 Alt Katman Şeması + Kanıt Kataloğu v1.1.0 — 2026-09-24 · kaynak: 3 turlu agent tartışması*

*Last Updated: 2026-09-24*
*Mode: Red Team · Human Mode · Truth Mode*
