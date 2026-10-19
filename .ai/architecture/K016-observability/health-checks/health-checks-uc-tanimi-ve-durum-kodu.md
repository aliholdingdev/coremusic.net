---
title: "K016-observability/health-checks — Health Uçları ve Durum Kodu"
node_id: K016-observability/health-checks/health-checks-uc-tanimi-ve-durum-kodu
node_type: subdomain
status: draft
parent_id: K016-observability/health-checks
children: []
depends_on:
  - K016-observability
  - K012-services
evidence:
  - "# health-check route'u için disk kanıtı YOK (master §4 satır 99: health endpoint UNKNOWN)"
validation:
  - "Bu turda health-checks için disk kanıtı YOK — tasarım/PLANNED (⚠️ VERIFICATION REQUIRED)"
open_questions:
  - "Health-check endpoint hangi servis/rotada? (master §7/5)"
group: mimari
---


# K016-observability/health-checks — health-checks-uc-tanimi-ve-durum-kodu

## §1 Rol (ELI15)

Sistem "sağlıklı mısın?" sorusuna yanıt veren uçlar tanımlar. Üç farklı soru vardır: süreç çalışıyor mu (liveness), istek alabilir mi (readiness), başlangıç bitti mi (startup). Sağlık yanıtı, hangi bileşenin hasta olduğunu da söyler (DB, Redis, medya). **Tanım** bu domainde, **kod/sahiplik** servistedir (K012).

## §2 Kapsam sınırı

| Alan | Bu dosyada | Bu dosyada DEĞİL |
|---|---|---|
| Health uç tanımı (rota, şema) | ✅ RFC healthcheck şeması | ❌ Servis içi kod → K012-services |
| Durum kodu eşlemesi (200/503) | ✅ pass/warn/fail → HTTP | ❌ Deploy'a bağlama → K17-devops |
| Bağımlılık sorgusu tipi | ✅ DB/Redis/media kontrolü | ❌ Bağımlılığın kendisi → ilgili servis |
| Probe tipi ayrımı | ✅ liveness/readiness/startup | ❌ K8s kurulumu → K17-devops |
| Uyarı üretimi | ✅ health sonucundan uyarı adayı | ❌ Uyarı kuralı/kanalı → alerts/ |

## §3 Terimler ve kısaltmalar

| Terim | İngilizce | Tanım | Bu dosyadaki karşılığı |
|---|---|---|---|
| Liveness | liveness | Süreç canlı mı | `/health/live` |
| Readiness | readiness | İstek alabilir mi | `/health/ready` |
| Startup | startup | Başlangıç tamamlandı mı | startup probe |
| Bağımlılık | dependency | Dış servis (DB/Redis) | component listesi |
| Bileşen | component | Sağlık alt birimi | `componentId` |
| Durum | status | pass / warn / fail | `status` alanı |
| Health şeması | health+json | RFC sağlık yanıtı şeması | §6 |
| Probe | probe | Dış sistemden sağlık sorgusu | K8s httpGet |
| Degraded | degraded | Çalışıyor ama hasta | `warn` |
| Down | down | Cevap vermiyor | `fail` → 503 |

## §4 Liveness ≠ Readiness ayrımı

| # | Soru | Liveness (`/health/live`) | Readiness (`/health/ready`) |
|---|---|---|---|
| 1 | Ne sorar? | Süreç yaşıyor mu | İstek alabilir mi |
| 2 | DB kapalıysa | 200 (süreç yaşar) | 503 (istek reddedilir) |
| 3 | Başarısızlıkta | K8s restart | Trafik durdurulur |
| 4 | Sıklık | Yüksek (sn) | Orta |
| 5 | İçerik | Minimal (boş `{"status":"pass"}`) | Bağımlılık listesi |
| 6 | Yanlış eşleşme | DB için liveness kullanılırsa gereksiz restart | Süreç için readiness kullanılırsa trafik kaybı |

**Kural:** Liveness asla bağımlılığa bakmaz (yanlışlıkla kendini öldürür). Readiness bağımlılığa bakar.

## §5 Durum kodu eşlemesi

| # | Bileşen durumu | Toplam durum | HTTP kodu | K8s davranışı |
|---|---|---|---|---|
| 1 | tümü pass | pass | 200 | healthy |
| 2 | pass + warn | warn | 200 | healthy (uyarılı) |
| 3 | herhangi bir fail | fail | 503 | unready / restart |
| 4 | timeout (>1s) | fail | 503 | fail-safe |
| 5 | bilinmeyen durum | fail | 503 | fail-safe (fail-open değil) |

**Kural:** Belirsizlikte 503 (fail-safe) — asla 200 (fail-open değil).

## §6 Sağlık yanıtı şeması (application/health+json)

### §6.1 Şema alanları (RFC healthcheck)

| Alan | Tip | Açıklama | Somut örnek |
|---|---|---|---|
| `status` | string | pass / warn / fail | `pass` |
| `releaseId` | string | sürüm | `v1.4.2` |
| `version` | string | semver | `1.4.2` |
| `notes` | string | kısa not | `all dependencies ok` |
| `output` | string | detay | `db=ok redis=ok` |
| `links` | string[] | yardım linki | `https://inadarei.github.io/rfc-healthcheck/` |
| `actions` | string[] | eylem önerisi | `check redis host` |
| `components[]` | array | bileşen listesi | §6.2 |

### §6.2 Bileşen örneği (somut JSON)

```json
{
  "status": "warn",
  "releaseId": "v1.4.2",
  "version": "1.4.2",
  "notes": "db ok, redis degraded",
  "output": "db=5ms redis=250ms",
  "components": [
    {
      "componentId": "db",
      "componentType": "datastore",
      "status": "pass",
      "observedValue": "5ms",
      "links": []
    },
    {
      "componentId": "redis",
      "componentType": "datastore",
      "status": "warn",
      "observedValue": "250ms",
      "links": ["https://redis.io/docs/"]
    },
    {
      "componentId": "media",
      "componentType": "process",
      "status": "pass",
      "observedValue": "ok",
      "links": []
    }
  ]
}
```

### §6.3 Yanıt başlıkları

| Başlık | Değer | Gerekçe |
|---|---|---|
| `Content-Type` | `application/health+json` | RFC tanımı |
| `Cache-Control` | `no-store` | cache'lenmez, taze okunur |
| Süreç | < 1 saniye | health hızlı olmalı |

## §7 ÇOK DİLLİ KOD ÖRNEKLERİ

> **TASARIM ÖRNEĞİ (illüstratif)** — bloklar repo'da karşılığı OLMAYAN, RFC healthcheck desenini gösteren tasarım örnekleridir. Durum: **PLANNED**. Mevcut IMPLEMENTED yüzey yalnız logging/ (`shared/src/Log/`)tir.

### §7.1 PHP — /health/ready + /health/live (fail → 503)

```php
<?php
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/health-checks — RFC healthcheck şeması (pass|warn|fail → 200|503).
declare(strict_types=1);

namespace CoreMusic\Observability\Health;

final class HealthEndpoint
{
    /** Liveness — bağımlılığa BAKMAZ (süreç canlı). */
    public function live(): array
    {
        return ['status' => 'pass'];
    }

    /** Readiness — bağımlılığa bakar; fail → 503. */
    public function ready(callable $dbPing, callable $redisPing): array
    {
        $components = [];
        $overall = 'pass';

        $db = $dbPing();      // ['status'=>'pass','observedValue'=>'5ms']
        $components[] = ['componentId'=>'db','componentType'=>'datastore'] + $db;
        $overall = $this->worst($overall, $db['status']);

        $redis = $redisPing();
        $components[] = ['componentId'=>'redis','componentType'=>'datastore'] + $redis;
        $overall = $this->worst($overall, $redis['status']);

        return [
            'status'     => $overall,
            'releaseId'  => 'v1.4.2',
            'version'    => '1.4.2',
            'components' => $components,
        ];
    }

    private function worst(string $a, string $b): string
    {
        $rank = ['pass'=>0, 'warn'=>1, 'fail'=>2];
        return $rank[$a] >= $rank[$b] ? $a : $b;
    }

    /** HTTP kod eşlemesi: fail → 503, pass/warn → 200 (fail-safe). */
    public function httpCode(string $status): int
    {
        return $status === 'fail' ? 503 : 200;
    }
}
```

### §7.2 Python 3.12+ — FastAPI readiness/liveness

```python
# TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
# K016/health-checks — FastAPI health uçları (fail-safe 503).
from __future__ import annotations

import asyncio
from fastapi import FastAPI, Response

app = FastAPI()


@app.get("/health/live")
async def live() -> dict:
    return {"status": "pass"}


@app.get("/health/ready")
async def ready(response: Response) -> dict:
    components = []
    overall = "pass"

    db = await asyncio.wait_for(db_ping(), timeout=1.0)
    components.append({"componentId": "db", "componentType": "datastore", **db})
    overall = _worst(overall, db["status"])

    redis = await asyncio.wait_for(redis_ping(), timeout=1.0)
    components.append({"componentId": "redis", "componentType": "datastore", **redis})
    overall = _worst(overall, redis["status"])

    response.status_code = 503 if overall == "fail" else 200
    response.headers["Cache-Control"] = "no-store"
    return {"status": overall, "components": components}


def _worst(a: str, b: str) -> str:
    rank = {"pass": 0, "warn": 1, "fail": 2}
    return a if rank[a] >= rank[b] else b
```

### §7.3 Go — http.Handler + context timeout

```go
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/health-checks — context timeout'lu readiness.
package health

import (
	"context"
	"encoding/json"
	"net/http"
	"time"
)

type Component struct {
	ComponentID   string `json:"componentId"`
	ComponentType string `json:"componentType"`
	Status        string `json:"status"`
	ObservedValue string `json:"observedValue"`
}

func Ready(w http.ResponseWriter, r *http.Request) {
	ctx, cancel := context.WithTimeout(r.Context(), time.Second)
	defer cancel()

	comps := []Component{}
	overall := "pass"

	db := pingDB(ctx)
	comps = append(comps, Component{"db", "datastore", db.Status, db.Value})
	overall = worst(overall, db.Status)

	redis := pingRedis(ctx)
	comps = append(comps, Component{"redis", "datastore", redis.Status, redis.Value})
	overall = worst(overall, redis.Status)

	if overall == "fail" {
		w.WriteHeader(http.StatusServiceUnavailable)
	}
	w.Header().Set("Cache-Control", "no-store")
	json.NewEncoder(w).Encode(map[string]any{"status": overall, "components": comps})
}

func worst(a, b string) string {
	rank := map[string]int{"pass": 0, "warn": 1, "fail": 2}
	if rank[a] >= rank[b] {
		return a
	}
	return b
}
```

### §7.4 C# — minimal API + HealthReport

```csharp
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/health-checks — .NET minimal API health uçları.
#nullable enable
var builder = WebApplication.CreateBuilder(args);
var app = builder.Build();

app.MapGet("/health/live", () => Results.Json(new { status = "pass" }));

app.MapGet("/health/ready", async () =>
{
    var comps = new List<object>();
    var overall = "pass";

    var db = await PingDbAsync();
    comps.Add(new { componentId = "db", componentType = "datastore", status = db.Status, observedValue = db.Value });
    overall = Worst(overall, db.Status);

    var redis = await PingRedisAsync();
    comps.Add(new { componentId = "redis", componentType = "datastore", status = redis.Status, observedValue = redis.Value });
    overall = Worst(overall, redis.Status);

    var code = overall == "fail" ? StatusCodes.Status503ServiceUnavailable : StatusCodes.Status200OK;
    return Results.Json(new { status = overall, components = comps }, statusCode: code);
});

static string Worst(string a, string b)
{
    int Rank(string s) => s switch { "fail" => 2, "warn" => 1, _ => 0 };
    return Rank(a) >= Rank(b) ? a : b;
}
```

### §7.5 Rust — axum router + tokio timeout

```rust
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/health-checks — axum readiness + tokio::time::timeout.
use axum::{routing::get, Json, Router};
use serde_json::{json, Value};

pub fn router() -> Router {
    Router::new()
        .route("/health/live", get(live))
        .route("/health/ready", get(ready))
}

async fn live() -> Json<Value> {
    Json(json!({ "status": "pass" }))
}

async fn ready() -> (axum::http::StatusCode, Json<Value>) {
    let db = tokio::time::timeout(std::time::Duration::from_secs(1), ping_db()).await;
    let (db_status, db_val) = match db {
        Ok(Ok(v)) => ("pass", v),
        _ => ("fail", "timeout".to_string()),
    };
    let overall = db_status; // basit: tek bileşen
    let code = if overall == "fail" {
        axum::http::StatusCode::SERVICE_UNAVAILABLE
    } else {
        axum::http::StatusCode::OK
    };
    (code, Json(json!({ "status": overall, "components": [
        { "componentId": "db", "componentType": "datastore", "status": db_status, "observedValue": db_val }
    ]})))
}
```

### §7.6 JavaScript/TS — Express health router

```typescript
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/health-checks — Express health router (fail-safe 503).
import { Router, Request, Response } from "express";

const router = Router();

router.get("/health/live", (_req: Request, res: Response) => {
  res.set("Cache-Control", "no-store").status(200).json({ status: "pass" });
});

router.get("/health/ready", async (_req: Request, res: Response) => {
  const components: object[] = [];
  let overall = "pass";

  const db = await pingDb();
  components.push({ componentId: "db", componentType: "datastore", ...db });
  overall = worst(overall, db.status);

  const redis = await pingRedis();
  components.push({ componentId: "redis", componentType: "datastore", ...redis });
  overall = worst(overall, redis.status);

  const code = overall === "fail" ? 503 : 200;
  res.set("Cache-Control", "no-store").status(code).json({ status: overall, components });
});

function worst(a: string, b: string): string {
  const rank: Record<string, number> = { pass: 0, warn: 1, fail: 2 };
  return rank[a] >= rank[b] ? a : b;
}

export default router;
```

### §7.7 Java — Spring HealthIndicator deseni

```java
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/health-checks — Spring HealthIndicator deseni (RFC şemasına eşlenir).
package com.coremusic.health;

import org.springframework.boot.actuate.health.Health;
import org.springframework.boot.actuate.health.HealthIndicator;
import org.springframework.stereotype.Component;

@Component("db")
public class DbHealthIndicator implements HealthIndicator {
    @Override
    public Health health() {
        long t0 = System.nanoTime();
        boolean ok = pingDb();
        long ms = (System.nanoTime() - t0) / 1_000_000;
        return ok
            ? Health.up().withDetail("observedValue", ms + "ms").build()
            : Health.down().withDetail("observedValue", "timeout").build();
    }
}
```

### §7.8 C++ — basit health handler

```cpp
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/health-checks — health handler (RFC şeması, fail-safe).
#pragma once
#include <string>

namespace cm::obs {

struct HealthResult {
    std::string status;   // pass | warn | fail
    int http_code;        // 200 | 503
};

inline HealthResult ready(bool db_ok, bool redis_ok) {
    // fail-safe: herhangi bir fail → fail → 503
    if (!db_ok || !redis_ok) return {"fail", 503};
    return {"pass", 200};
}

inline std::string to_json(const HealthResult& r) {
    // somut şema örneği
    return "{\"status\":\"" + r.status + "\",\"components\":["
           "{\"componentId\":\"db\",\"componentType\":\"datastore\",\"status\":\"" +
           (r.status == "fail" ? "fail" : "pass") + "\"}]}";
}

} // namespace cm::obs
```

### §7.9 YAML — Kubernetes Liveness/Readiness Probe

```yaml
# TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
# K016/health-checks — K8s probe tanımı (liveness bağımlılığa bakmaz).
apiVersion: apps/v1
kind: Deployment
metadata:
  name: coremusic-web
spec:
  template:
    spec:
      containers:
        - name: web
          image: coremusic/web:v1.4.2
          ports:
            - containerPort: 8080
          livenessProbe:
            httpGet:
              path: /health/live
              port: 8080
            initialDelaySeconds: 5
            periodSeconds: 10
          readinessProbe:
            httpGet:
              path: /health/ready
              port: 8080
            initialDelaySeconds: 10
            periodSeconds: 15
            timeoutSeconds: 1
```

## §8 Bağımlılık sorgusu (DB / Redis / medya)

| # | Bağımlılık | Sorgu | Pass ölçütü | Uyarı adayı |
|---|---|---|---|---|
| 1 | MySQL (K005) | `SELECT 1` ping | < 100ms | yavaş yanıt |
| 2 | Redis | `PING` | < 50ms | bağlantı yok |
| 3 | Medya (K014) | decoder sağlık | ok | xrun yükselişi → audio-diagnostics/ |
| 4 | Disk | yazma testi | yazılabilir | dolu disk → K018 |

**Kural:** Health sorgusu **hafif** olmalı (ağır sorgu health'i hasta eder). Timeout 1s → fail.

## §9 Komşu sınır

| # | Komşu | Sınır | Yön | Kaynak |
|---|---|---|---|---|
| 1 | K012-services | Health KODU/sahipliği serviste; bu domain yalnız tanım | K016 → K012 | master §C/16 |
| 2 | K17-devops | Health sonucu deploy kapısına bağlanır (K8s probe) | K016 → K018 | master §D.1/8 |
| 3 | alerts/ | Health fail → uyarı adayı | iç | §5 |
| 4 | audio-diagnostics/ | Medya health'i xrun sinyalini besler | iç | §8 satır 3 |
| 5 | K005-firmware | Donanım health'i sınır (kapsam dışı) | ayrım | master §4/12 |

## §F Web tablosu

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| RFC healthcheck — `application/health+json`, status pass/warn/fail, 200/503, bileşen listesi | https://inadarei.github.io/rfc-healthcheck/ | 2026-10-13 | HTTP 200 · doğrulandı · envanter dışı |
| Kubernetes health check API — liveness/readiness/startup probe, httpGet | https://kubernetes.io/docs/reference/using-api/health-checks/ | 2026-10-13 | HTTP 200 · doğrulandı · envanter dışı |

## §G Revizyon doğrulama kaydı

| # | Kontrol | Beklenen | Sonuç |
|---|---|---|---|
| 1 | Dosya uzunluğu | ≥ 500 tam satır | 524 |
| 2 | Son satır | `**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode` | ✓ |
| 3 | Frontmatter | v2 §4.2 8 alan + `node_id` + `group` (9. alan) · `parent:` 0 | ✓ |
| 4 | Zorunlu bölümler | §1-§9 + §F + §G | ✓ |
| 5 | Çok-dilli kod | 9 blok (PHP · Python · Go · C# · Rust · TS · Java · C++ · YAML) · her biri TASARIM ÖRNEĞİ işaretli | ✓ |
| 6 | Web tablosu | 2 satır · envanter dışı · HTTP 200 | ✓ |
| 7 | Yasaklı yer-tutucu | boş hücre · üç nokta dizgesi · gelecek-sözü · mojibake | ✓ 0 |
| 8 | Kanıt dürüstlüğü | health-checks disk kanıtı YOK → tasarım/PLANNED | ✓ |
| 9 | Git commit | atılmadı | ✓ |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
