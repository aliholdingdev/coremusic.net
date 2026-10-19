---
title: "K016-observability/alerts — Uyarı Kuralları, Eşik ve Kanal"
node_id: K016-observability/alerts/alerts-kural-esik-ve-kanal
node_type: subdomain
status: draft
parent_id: K016-observability/alerts
children: []
depends_on:
  - K016-observability
  - K016-observability/alerting
evidence:
  - "# alert kuralları/kanalları için disk kanıtı YOK (master §4 satır 98: alert kanalı UNKNOWN)"
validation:
  - "Bu turda alerts için disk kanıtı YOK — tasarım/PLANNED (⚠️ VERIFICATION REQUIRED)"
open_questions:
  - "Alert kanalı (Slack/e-mail/webhook) seçildi mi? K009 gizlilik onayı gerekli (master §7/6)"
group: mimari
---


# K016-observability/alerts — alerts-kural-esik-ve-kanal

## §1 Rol (ELI15)

Bir eşik aşıldığında sistem alarm çalar. Uyarı bir **kural**dır: "şu koşul, şu süre boyunca, şu eşiği aşarsa, şu kanala, şu ciddiyette bildir." Uyarı **üretilir** ama **aksiyon alınmaz** — deploy/rollback K018'dedir. Eşik/yükseklik hesabı ayrı dosyadır (`alerting/`).

## §2 Kapsam sınırı

| Alan | Bu dosyada | Bu dosyada DEĞİL |
|---|---|---|
| Uyarı kuralı (koşul+eşik+kanal) | ✅ kural şeması | ❌ Eşik hesabı (burn-rate) → alerting/ |
| Ciddiyet (severity) eşlemesi | ✅ page/ticket | ❌ Aksiyon (deploy/rollback) → K17-devops |
| Kanal tanımı | ✅ webhook/e-mail | ❌ Kanal gizlilik politikası → K009-security |
| Multiwindow burn-rate | ✅ Google SRE deseni | ❌ Metrik üretimi → metrics/ |
| Uyarı testi | ✅ kural doğrulama | ❌ Test stratejisi → K18-testing |

## §3 Terimler ve kısaltmalar

| Terim | İngilizce | Tanım | Bu dosyadaki karşılığı |
|---|---|---|---|
| Eşik | threshold | Uyarı tetikleyen değer | `expr` koşulu |
| Koşul | condition | Sorgu (PromQL benzeri) | `expr` |
| Süre penceresi | window | Koşunun izlendiği aralık | `for: 5m` |
| Ciddiyet | severity | page / ticket / info | `severity` alanı |
| Burn-rate | burn-rate | SLO ihlal hızı | §5 |
| Multiwindow | multiwindow | Hızlı+yavaş pencere kombinasyonu | §5 |
| Kanal | channel | Bildirim hedefi (webhook/e-mail) | `receiver` |
| Alertmanager | alertmanager | Uyarı yönlendirici | §7.10 |
| Gürültü | noise | Kuralın gereksiz tetiklenmesi | `for` penceresi |
| Uyku | silence | Geçici susturma | maintenance window |

## §4 Uyarı kuralı bileşenleri

| # | Bileşen | Açıklama | Somut örnek |
|---|---|---|---|
| 1 | Koşul (expr) | Ne ölçülür | `http_error_rate > 0.01` |
| 2 | Süre penceresi | Ne kadar sürmeli | `for: 5m` |
| 3 | Multiwindow | Hızlı+yavaş kontrol | 1h+5m / 6h+30m |
| 4 | Ciddiyet | page / ticket | `severity: page` |
| 5 | Kanal | Nereye gider | webhook / e-mail |
| 6 | Etiket | Gruplama | `service`, `env` |
| 7 | Anotasyon | Açıklama | summary, runbook_url |

### §4.1 Somut eşik eşleme tablosu (uyarı adayları)

| # | Metrik | Eşik | Pencere | Ciddiyet | Kanal |
|---|---|---|---|---|---|
| 1 | HTTP 5xx oranı | > 1% | 5m | page | webhook |
| 2 | Yanıt süresi p95 | > 500ms | 15m | ticket | e-mail |
| 3 | Health fail (K016/health-checks) | status=fail | 2m | page | webhook |
| 4 | Disk kullanımı | > 90% | 30m | ticket | e-mail |
| 5 | Xrun sayısı (audio-diagnostics) | > 5/dk | 1m | page | webhook |
| 6 | Cache hit oranı | < 80% | 15m | info | log |

### §4.2 Kural yaşam döngüsü

| # | Adım | Açıklama | Sorumlu |
|---|---|---|---|
| 1 | Tanım | Kural şeması yazılır | K016 (bu dosya) |
| 2 | Eşik seçimi | Metrik + burn-rate (alerting/) | K016 + alerting/ |
| 3 | Test | Kural yanlış tetikliyor mu? | K18-testing |
| 4 | Kanal onayı | Gizlilik (PII) kontrolü | K009-security |
| 5 | Dağıtım | Alertmanager'a yükleme | K17-devops |
| 6 | Gözlem | Gürültü/silence takibi | K016 (bu dosya) |

**Kural:** Kural, test edilmeden üretime alınmaz (K019). Kanal, K009 onayı olmadan kullanılmaz.

## §5 Multiwindow burn-rate alerting (Google SRE)

### §5.1 SLO 99.9% / 30 gün — burn-rate pencereleri

| # | Pencere | Burn-rate | Ciddiyet | Gerekçe |
|---|---|---|---|---|
| 1 | 1h + 5m | 14.4x | page | hızlı ihlal, anlık müdahale |
| 2 | 6h + 30m | 6x | page | orta ihlal |
| 3 | 3d + 6h | 1x | ticket | yavaş ihlal, planlı |

**Kural (Google SRE):** kısa pencere = uzun pencere / 12. 14.4x burn-rate, 99.9% SLO'da 1 saatte ~14 dakika ihlal eder → hemen page.

### §5.3 Burn-rate hesap örneği (somut sayılar)

| # | Metrik | Değer | SLO bütçesi (0.001) | Burn-rate | Karar |
|---|---|---|---|---|---|
| 1 | 1h hata oranı | 0.0144 (1.44%) | 0.001 | 14.4x | page (hızlı pencere aşıldı) |
| 2 | 5m hata oranı | 0.02 (2%) | 0.001 | 20x | page (hızlı pencere aşıldı) |
| 3 | 6h hata oranı | 0.006 (0.6%) | 0.001 | 6x | page (yavaş pencere eşiği) |
| 4 | 30g hata oranı | 0.0005 (0.05%) | 0.001 | 0.5x | ihlal yok (bütçe içinde) |
| 5 | 3d hata oranı | 0.001 (0.1%) | 0.001 | 1x | ticket (kademeli) |

**Hesap:** burn-rate = hata oranı / SLO bütçesi. 99.9% SLO → bütçe = 1 − 0.999 = 0.001. 1h'da 1.44% hata → 0.0144/0.001 = 14.4x → page.

### §5.4 Pencere seçimi kuralı tablosu

| # | Durum | Hızlı pencere | Yavaş pencere | Ciddiyet | Gerekçe |
|---|---|---|---|---|---|
| 1 | ani deploy hatası | 1h + 5m | 6h + 30m | page | hızlı tespit |
| 2 | sızıntı (leak) | 6h + 30m | 3d + 6h | page→ticket | kademeli |
| 3 | mevsimsel dalgalanma | 3d + 6h | — | ticket | planlı |

### §5.2 Neden multiwindow (tek pencere neden yetmez)

| # | Tek pencere (yanlış) | Multiwindow (doğru) | Sonuç |
|---|---|---|---|
| 1 | Kısa pencere = gürültü | Hızlı+savaş doğrulama | Yanlış alarm azalır |
| 2 | Uzun pencere = geç fark | Hızlı pencere erken uyarı | Erken müdahale |
| 3 | Tek eşik = kör | İki pencere = bağlam | Sağlam karar |

## §6 Severity eşlemesi ve eskalasyon

| # | Severity | Anlamı | Yanıt | Kanal |
|---|---|---|---|---|
| 1 | page | Anlık müdahale (SLO ihlali) | nöbetçi uyandırılır | telefon/webhook |
| 2 | ticket | Planlı müdahale (kademeli) | iş açılır | e-mail/ticket |
| 3 | info | Bilgi (log) | aksiyon yok | log |

**Kural:** page → **insan** eskalasyonu (ürün runbook'u, `incident-response/`). Ajan eskalasyonu K015-AI'dır (bkz. incident-response/ §8).

### §6.1 Uyarı → eskalasyon akışı (adım adım)

| # | Tetikleyici | Uyarı | Ciddiyet | Eskalasyon | Sahip |
|---|---|---|---|---|---|
| 1 | 14.4x burn-rate (1h+5m) | SLOBurnRateFast | page | nöbetçi → insan | incident-response/ |
| 2 | health status=fail 2m | HealthFail | page | nöbetçi → insan | incident-response/ |
| 3 | xrun > 5/dk | AudioXrun | page | nöbetçi → insan | audio-diagnostics/ |
| 4 | p95 > 500ms 15m | LatencyHigh | ticket | iş kaydı açılır | alerts/ (bu dosya) |
| 5 | kural test başarısız | AlertTestFail | info | log (aksiyon yok) | K18-testing |
| 6 | kanal gönderim hatası | NotifyFail | ticket | yedek kanal → K018 | K17-devops |

### §6.2 Uyarı gürültü azaltma kuralları

| # | Teknik | Kural | Etki |
|---|---|---|---|
| 1 | `for` penceresi | kısa süren sıçrama tetiklemez | gürültü ↓ |
| 2 | group_by | aynı etiket tek uyarıda | tekrar ↓ |
| 3 | repeat_interval | aynı uyarı 4 saatte bir | spam ↓ |
| 4 | silence | bakım penceresi susturma | yanlış alarm ↓ |
| 5 | multiwindow | hızlı+savaş doğrulama | güvenilirlik ↑ |
| 6 | kural gözden geçirme | ayda bir aktif kural temizliği | borç ↓ |

## §7 ÇOK DİLLİ KOD ÖRNEKLERİ

> **TASARIM ÖRNEĞİ (illüstratif)** — bloklar repo'da karşılığı OLMAYAN, Prometheus/alerting desenini gösteren tasarım örnekleridir. Durum: **PLANNED**.

### §7.1 YAML — Prometheus SLO burn-rate kuralı

```yaml
# TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
# K016/alerts — multiwindow burn-rate alert (Google SRE, SLO 99.9%/30g).
groups:
  - name: slo-burn-rate
    rules:
      # Hızlı pencere (1h + 5m) — 14.4x → page
      - alert: SLOBurnRateFast
        expr: |
          (
            sum(rate(http_requests_total{status=~"5.."}[1h]))
            / sum(rate(http_requests_total[1h]))
          ) > (14.4 * 0.001)
          and
          (
            sum(rate(http_requests_total{status=~"5.."}[5m]))
            / sum(rate(http_requests_total[5m]))
          ) > (14.4 * 0.001)
        for: 2m
        labels:
          severity: page
        annotations:
          summary: "SLO 99.9% hızlı burn-rate ihlali (14.4x)"
          runbook_url: "https://wiki.example.test/runbooks/slo-burn"

      # Yavaş pencere (6h + 30m) — 6x → page
      - alert: SLOBurnRateSlow
        expr: |
          (
            sum(rate(http_requests_total{status=~"5.."}[6h]))
            / sum(rate(http_requests_total[6h]))
          ) > (6 * 0.001)
          and
          (
            sum(rate(http_requests_total{status=~"5.."}[30m]))
            / sum(rate(http_requests_total[30m]))
          ) > (6 * 0.001)
        for: 15m
        labels:
          severity: page
        annotations:
          summary: "SLO 99.9% yavaş burn-rate ihlali (6x)"
```

### §7.2 PHP — eşik karşılayan alert evaluator (promphp)

```php
<?php
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/alerts — promphp ile eşik tabanlı uyarı değerlendirmesi.
declare(strict_types=1);

namespace CoreMusic\Observability\Alerts;

final class ThresholdAlert
{
    public function __construct(
        private readonly float $threshold,
        private readonly string $severity,
    ) {}

    /** Değer eşiği aşarsa uyarı üretir (aksiyon YOK — K018). */
    public function evaluate(string $metric, float $value): ?array
    {
        if ($value <= $this->threshold) {
            return null;
        }
        return [
            'alert'     => strtoupper($metric) . '_THRESHOLD_BREACH',
            'metric'    => $metric,
            'value'     => $value,
            'threshold' => $this->threshold,
            'severity'  => $this->severity,
            'labels'    => ['service' => 'coremusic-web', 'env' => 'prod'],
        ];
    }
}
```

### §7.3 Python — prometheus_client gauge + burn-rate

```python
# TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
# K016/alerts — burn-rate hesabı + gauge.
from __future__ import annotations

from prometheus_client import Gauge

SLO_AVAILABILITY = 0.999  # 99.9%
_error_burn = Gauge("slo_error_burn_rate", "SLO error burn-rate", ["window"])


def burn_rate(error_rate: float) -> float:
    """error_rate → burn-rate (SLO 99.9%)."""
    budget = 1.0 - SLO_AVAILABILITY  # 0.001
    return error_rate / budget


def should_page(fast_burn: float, slow_burn: float) -> bool:
    """Multiwindow: hızlı 14.4x VEYA yavaş 6x → page."""
    return fast_burn > 14.4 or slow_burn > 6.0
```

### §7.4 Go — eşik kuralı

```go
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/alerts — basit eşik kuralı (page/ticket).
package alerts

type Rule struct {
	Metric   string
	Severity string
	Window   string
}

type Alert struct {
	Alert    string  `json:"alert"`
	Metric   string  `json:"metric"`
	Value    float64 `json:"value"`
	Severity string  `json:"severity"`
}

func Evaluate(r Rule, value, threshold float64) *Alert {
	if value <= threshold {
		return nil
	}
	return &Alert{
		Alert:    r.Metric + "_THRESHOLD_BREACH",
		Metric:   r.Metric,
		Value:    value,
		Severity: r.Severity,
	}
}
```

### §7.5 C# — eşik değerlendirici

```csharp
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/alerts — C# threshold alert.
#nullable enable
namespace CoreMusic.Observability.Alerts;

public sealed record ThresholdRule(string Metric, double Threshold, string Severity);

public sealed record Alert(string Name, string Metric, double Value, string Severity);

public static class Evaluator
{
    public static Alert? Evaluate(ThresholdRule r, double value)
        => value <= r.Threshold
            ? null
            : new Alert($"{r.Metric}_THRESHOLD_BREACH", r.Metric, value, r.Severity);
}
```

### §7.6 Rust — eşik kontrolü

```rust
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/alerts — Rust threshold check.
pub struct Rule {
    pub metric: &'static str,
    pub threshold: f64,
    pub severity: &'static str,
}

pub fn evaluate(rule: &Rule, value: f64) -> Option<String> {
    if value <= rule.threshold {
        return None;
    }
    Some(format!("{}_THRESHOLD_BREACH severity={}", rule.metric, rule.severity))
}
```

### §7.7 JavaScript/TS — webhook kanalına alert

```typescript
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/alerts — webhook kanalına uyarı gönderimi.
interface Alert {
  alert: string;
  metric: string;
  value: number;
  severity: "page" | "ticket" | "info";
}

export async function notify(alert: Alert, webhookUrl: string): Promise<void> {
  const res = await fetch(webhookUrl, {
    method: "POST",
    headers: { "content-type": "application/json" },
    body: JSON.stringify(alert),
  });
  if (!res.ok) {
    throw new Error(`webhook failed: ${res.status}`);
  }
}
```

### §7.8 Java — eşik kuralı

```java
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/alerts — Java threshold rule.
package com.coremusic.alerts;

public record Rule(String metric, double threshold, String severity) {}

public record Alert(String name, String metric, double value, String severity) {}

public final class Evaluator {
    private Evaluator() {}

    public static Alert evaluate(Rule r, double value) {
        if (value <= r.threshold()) {
            return null;
        }
        return new Alert(r.metric() + "_THRESHOLD_BREACH", r.metric(), value, r.severity());
    }
}
```

### §7.9 C++ — basit eşik

```cpp
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/alerts — C++ threshold.
#pragma once
#include <string>
#include <optional>

namespace cm::obs {

struct Alert { std::string name; std::string severity; double value; };

inline std::optional<Alert> evaluate(const std::string& metric,
                                     double value, double threshold,
                                     const std::string& severity) {
    if (value <= threshold) return std::nullopt;
    return Alert{metric + "_THRESHOLD_BREACH", severity, value};
}

} // namespace cm::obs
```

### §7.10 YAML — Alertmanager route/receiver

```yaml
# TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
# K016/alerts — Alertmanager kanal yönlendirme (severity → receiver).
route:
  group_by: ["alertname", "service"]
  group_wait: 30s
  group_interval: 5m
  repeat_interval: 4h
  routes:
    - match:
        severity: page
      receiver: oncall-phone
      continue: false
    - match:
        severity: ticket
      receiver: team-email

receivers:
  - name: oncall-phone
    webhook_configs:
      - url: "https://alerts.example.test/webhook/oncall"
        send_resolved: true
  - name: team-email
    email_configs:
      - to: "team@example.test"
        from: "alerts@example.test"
        smarthost: "smtp.example.test:587"
```

## §8 Komşu sınır

| # | Komşu | Sınır | Yön | Kaynak |
|---|---|---|---|---|
| 1 | K17-devops | Uyarı üretimi K016'da; aksiyon (deploy/rollback) K018'de | K016 → K018 | master §C/2 |
| 2 | alerting/ | Eşik/yükseklik hesabı ayrı dosya; bu dosya kural+kanal | iç | §1 |
| 3 | K009-security | Kanal gizlilik politikası (e-mail PII) | K016 ↔ K009 | master §C/23 |
| 4 | metrics/ | Uyarı, ölçülen metriğe bağlı | iç | master §D.2/4 |
| 5 | incident-response/ | page → insan eskalasyonu (ürün runbook'u) | iç | §6 |

## §9 Açık sorular (⚠️ VERIFICATION REQUIRED)

| # | Soru | Etki |
|---|---|---|
| 1 | Alert kanalı seçildi mi (Slack/e-mail/webhook)? | kanal implementasyonu |
| 2 | Kanal gizlilik onayı (K009) alındı mı? | PII/e-mail riski |
| 3 | Alert kuralları test edildi mi (K019)? | kural doğruluğu |
| 4 | Uyku/susturma (silence) penceresi tanımlı mı? | gürültü yönetimi |
| 5 | Burn-rate penceresi (99.9%/30g) hedefi onaylandı mı? | §5 pencereleri |
| 6 | runbook_url hedefleri (wiki.example.test) gerçek adres mi? | eskalasyon hızı |
| 7 | Alertmanager/K018 dağıtım sırası belirlendi mi? | dağıtım |

## §F.1 Uyarı şeması somut örnek (JSON)

```json
{
  "alert": "SLOBURNRATEFAST",
  "status": "firing",
  "labels": {
    "service": "coremusic-web",
    "env": "prod",
    "severity": "page"
  },
  "annotations": {
    "summary": "SLO 99.9% hizli burn-rate ihlali (14.4x)",
    "runbook_url": "https://wiki.example.test/runbooks/slo-burn"
  },
  "value": 14.4,
  "threshold": 14.4,
  "startsAt": "2026-10-13T10:00:00Z"
}
```

## §F Web tablosu

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Google SRE Workbook — alerting on SLOs (multiwindow burn-rate, 99.9%/30g pencereleri) | https://sre.google/workbook/alerting-on-slos/ | 2026-10-13 | HTTP 200 · doğrulandı · envanter dışı |
| Prometheus instrumentation best practices (etiket/cardinality, uyarı) | https://prometheus.io/docs/practices/instrumentation/ | 2026-10-13 | HTTP 200 · doğrulandı · envanter dışı |
| Prometheus client libraries (Ruby/Python/Java/Go/Rust/C++/PHP) | https://prometheus.io/docs/instrumenting/clientlibs/ | 2026-10-13 | HTTP 200 · doğrulandı · envanter dışı |

## §G Revizyon doğrulama kaydı

| # | Kontrol | Beklenen | Sonuç |
|---|---|---|---|
| 1 | Dosya uzunluğu | ≥ 500 tam satır | 509 |
| 2 | Son satır | `**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode` | ✓ |
| 3 | Frontmatter | v2 §4.2 8 alan + `node_id` + `group` (9. alan) · `parent:` 0 | ✓ |
| 4 | Zorunlu bölümler | §1-§9 + §F + §G | ✓ |
| 5 | Çok-dilli kod | 10 blok (YAML · PHP · Python · Go · C# · Rust · TS · Java · C++ · YAML) · her biri TASARIM ÖRNEĞİ işaretli | ✓ |
| 6 | Web tablosu | 3 satır · envanter dışı · HTTP 200 | ✓ |
| 7 | Yasaklı yer-tutucu | boş hücre · üç nokta dolgu · gelecek-sözü · mojibake | ✓ 0 |
| 8 | Kanıt dürüstlüğü | alerts disk kanıtı YOK → tasarım/PLANNED | ✓ |
| 9 | Eskalasyon ayrımı | page → insan (incident-response/), ajan K015-AI | ✓ |
| 10 | Git commit | atılmadı | ✓ |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
