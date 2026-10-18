---
node_id: K020-discovery-kod-ornekleri
node_type: subdomain
status: draft
parent_id: K020-discovery
children: []
depends_on: [K007-data, K009-security, K012-services]
evidence:
  - ".ai/architecture/K020-discovery/discovery-master.md §6 (keşif '*-ornekler' dilekçe/sözleşme örneğidir, entegrasyon kodu DEĞİL — 2026-10-18)"
  - "app-nodejs-projects/nova-search-engine/packages/* (gerçek TS yüzeyi — discovery-master §4, 2026-10-17)"
  - ".ai/architecture/K020-discovery/discovery-dogrulama.md §4 kapı 6 (kod sınırı: entegrasyon kodu yok, YTM kapalı)"
validation: "≥12 dil · her blok 15-60 satır etiketli · 250+ satır · örnekler dilekçe/sözleşme şablonudur, entegrasyon kodu değildir."
open_questions:
  - "⚠️ Örnekler üretim K020-alt katmanlarına GİRMEDEN önce onay gerektirir (AGENTS.md §1 APPROVAL)"
  - "⚠️ YouTube Music (YTM) örnekleri bilerek YOK — kapı KAPALI (ADR-097 §1/6)"
group: kod
---

# Discovery — Kod Örnekleri (keşif dilekçe / sözleşme şablonları)

> **KOD SINIRI:** Bu dosyadaki bloklar **keşif dilekçesi / sözleşme örneğidir** —
> entegrasyon kodu değildir (discovery-master §6). Üretim kodu yalnız K020-alt
> katmanlarında ve **onaydan sonra** yazılır. YTM örnekleri **kasten yok**.

## §1. ELI15

Keşif ekibi bir servise "girebilir mi?" diye bakarken kullanacağı **kalıpları**
burada tutar: rate-limit algılama, token sorma, imzalama, fallback zinciri, kuyruk.
Diller 12+ (gerçek repo dili TypeScript dahil). Her blok 15-60 satır ve etiketlidir.

## §2. Dil envanteri (12 dil)

| # | Dil | Blok | Keşif teması |
|---|---|---|---|
| 1 | TypeScript | §3.1 | gerçek repo: rate-limit + 429/Retry-After |
| 2 | JavaScript | §3.2 | URL doğrulama turu (18 URL / 0 hata kalıbı) |
| 3 | PHP 8.4 | §3.3 | PSR-15 middleware: servis keşfi kayıt |
| 4 | Python 3.12 | §3.4 | web taraması: kota/HEAD kontrolü |
| 5 | Go | §3.5 | context timeout + fallback zinciri |
| 6 | Rust | §3.6 | rate-limiter (kuyruk sözleşmesi) |
| 7 | Java | §3.7 | OAuth2 PKCE keşif kontrol listesi |
| 8 | C# | §3.8 | PowerShell yerine ölçüm kanonu sarmalayıcı |
| 9 | C | §3.9 | imza/hash sözleşmesi (md5 `api_sig` uyarısı) |
| 10 | Bash | §3.10 | curl kota taraması (diff-filter) |
| 11 | PowerShell | §3.11 | kanonik satır ölçümü + glob 0-isabet kanıtı |
| 12 | SQL | §3.12 | keşif bulgu kayıt şeması |

## §3. Bloklar

### 3.1 TypeScript — 429 / Retry-After keşif kalıbı (gerçek repo teması)

```typescript
// KAYNAK KALIBI: packages/search (nova-search-engine) — sözleşmeyi gösterir
// KEŞİF: Spotify 429 / Retry-After 30 sn rolling (discovery-dogrulama §3/1)
type ServiceQuota = {
  name: string;
  burstPerMinute: number;
  retryAfterSeconds: number | null; // null = servis yayımlamıyor (UNKNOWN)
};

export async function probeQuota(
  svc: ServiceQuota,
  call: () => Promise<Response>,
): Promise<"ok" | "throttled" | "fallback"> {
  const res = await call();
  if (res.status !== 429) return "ok";

  const ra = res.headers.get("Retry-After");
  const waitSec = ra ? Number(ra) : svc.retryAfterSeconds;
  if (waitSec === null || Number.isNaN(waitSec)) {
    // Deezer kota sayısı yayımlanmadı → tahmin YOK (zero-hallucination)
    return "fallback";
  }
  await new Promise((r) => setTimeout(r, waitSec * 1000));
  return "throttled";
}
```

### 3.2 JavaScript — URL doğrulama turu (18 URL / 0 hata kalıbı)

```javascript
// KEŞİF KALIBI: discovery-web-arastirma.md §6 — fetch edilen URL listesi
// Kural: erişilemeyen URL "⚠️ DEAD?" etiketi alır, SİLİNMEZ.
const URL_SET = [
  "https://developer.spotify.com/documentation/web-api",
  "https://developer.apple.com/documentation/applemusicapi",
  "https://musicbrainz.org/doc/Development/XML_Web_Service/Rate_Limiting",
]; // ... toplam 18 URL (tur-2, 2026-10-17: 0 hata)

async function verifyUrls(urls) {
  const report = [];
  for (const url of urls) {
    try {
      const res = await fetch(url, { method: "HEAD", redirect: "follow" });
      report.push({ url, status: res.status, ok: res.ok });
    } catch (err) {
      report.push({ url, status: 0, ok: false, flag: "⚠️ DEAD?" });
    }
  }
  return report;
}
```

### 3.3 PHP 8.4 — servis keşfi kayıt middleware'i (sözleşme örneği)

```php
<?php
declare(strict_types=1);

// KEŞİF DİLEKÇESİ: hangi servis keşfi hangi gate'ten geçti?
// Gate: ADR-010 (state/PKCE) — servis keşfi onaydan ÖNCE sunulur.
final readonly class ServiceDiscoveryGate
{
    /** @param array{service:string,pkce:bool,scope:list<string>} $claim */
    public function evaluate(array $claim): string
    {
        if ($claim['service'] === 'youtube-music') {
            return 'REJECTED'; // ADR-097 §1/6 — resmi API yok (kapı değişmez)
        }
        if (!empty($claim['scope']) && !$claim['pkce']) {
            return 'PENDING';  // ADR-010: PKCE doğrulaması atlanamaz
        }
        return 'READY_FOR_APPROVAL'; // kod = onay sonrası
    }
}
```

### 3.4 Python 3.12 — HEAD ile kota/erişim taraması

```python
# KEŞİF KALIBI: tur-2 geniş servis taraması (12 servis / 18 URL)
# Yasak: kota sayısını TAHMİN etmek. Yoksa "UNKNOWN" yazılır.
from dataclasses import dataclass, field
from typing import Optional
import urllib.request

@dataclass(frozen=True)
class ServiceProbe:
    name: str
    url: str
    quota_published: Optional[int] = None   # None → UNKNOWN

    def probe(self) -> str:
        req = urllib.request.Request(self.url, method="HEAD",
                                     headers={"User-Agent": "CoreMusic-Discovery/0.1"})
        try:
            with urllib.request.urlopen(req, timeout=10) as r:
                return f"reachable:{r.status}"
        except Exception:
            return "⚠️ DEAD?"

PROBES = [
    ServiceProbe("MusicBrainz", "https://musicbrainz.org/doc/Development/XML_Web_Service/Rate_Limiting", 1),
    ServiceProbe("Deezer", "https://developers.deezer.com/api", None),  # kota sayılmadı
]
```

### 3.5 Go — context timeout + fallback zinciri (ADR-101 §2.2)

```go
// KEŞİF SÖZLEŞMESİ: "her zaman 200" KABUL EDİLMEZ (ADR-101 §2.2 fallback zorunlu).
package discovery

import (
	"context"
	"errors"
	"time"
)

var ErrNoFallback = errors.New("fallback zinciri eksik — tasarım reddi")

type Result struct {
	Source string
	Body   string
}

// Resolve: primary → fallback; ikisi de düşerse hata (asla boş 200 dönmez).
func Resolve(ctx context.Context, primary, fallback func(context.Context) (Result, error)) (Result, error) {
	ctx, cancel := context.WithTimeout(ctx, 5*time.Second)
	defer cancel()

	if r, err := primary(ctx); err == nil {
		return r, nil
	}
	if r, err := fallback(ctx); err == nil {
		return r, nil
	}
	return Result{}, ErrNoFallback
}
```

### 3.6 Rust — rate-limiter sözleşmesi (kuyruk keşfi)

```rust
// KEŞİF: MusicBrainz 1 istek/sn/IP · Musixmatch 2.000/gün · Last.fm 5/sn
// Sözleşme: keşif kuyruğu kota bilir, kota tahmin edilmez.
use std::time::{Duration, Instant};

pub struct RateLimiter {
    per_second: u32,
    window_start: Instant,
    used: u32,
}

impl RateLimiter {
    pub fn new(per_second: u32) -> Self {
        Self { per_second, window_start: Instant::now(), used: 0 }
    }

    /// Döner: izin var mı. Kota aşımında false — çağıran kuyruğa geri koyar.
    pub fn allow(&mut self) -> bool {
        if self.window_start.elapsed() >= Duration::from_secs(1) {
            self.window_start = Instant::now();
            self.used = 0;
        }
        if self.used >= self.per_second { return false; }
        self.used += 1;
        true
    }
}
```

### 3.7 Java — OAuth2 PKCE keşif kontrol listesi (sözleşme)

```java
// KEŞİF: Spotify PKCE · Tidal ZORUNLU PKCE · Apple JWT(ES256+kid)
// Gate: ADR-010 — state/PKCE doğrulaması atlanmaz.
import java.util.List;
import java.util.Map;

public record AuthProbe(String service, boolean pkce, boolean state, String tokenShape) {

    private static final Map<String, AuthProbe> DISCOVERED = Map.of(
        "spotify",   new AuthProbe("spotify",   true,  true, "oauth2-code-verifier"),
        "tidal",     new AuthProbe("tidal",     true,  true, "oauth2.1-pkce-required"),
        "applemusic",new AuthProbe("applemusic",false, true, "jwt-es256-kid-exp<=15777000s"),
        "lastfm",    new AuthProbe("lastfm",    false, false, "api_sig-md5(sk)")
    );

    public static List<String> gateFailures() {
        return DISCOVERED.values().stream()
            .filter(a -> !a.state())                 // ADR-010: state yoksa engel
            .map(AuthProbe::service)
            .toList();
    }
}
```

### 3.8 C# — kanonik ölçüm sarmalayıcısı (registry §9.12.1)

```csharp
// KEŞİF KAPISI: ölçüm kanonu — "Get-Content | Measure-Object -Line" GEÇERSİZ.
using System.IO;

public static class VaultMeasure
{
    public static int Lines(string path)
        => File.ReadAllLines(path).Length;   // registry §9.12.1 (satır 761-764)

    public static (int md, int zeroHit) Glob(string dir, string pattern)
    {
        var files = Directory.Exists(dir)
            ? Directory.GetFiles(dir, pattern, SearchOption.AllDirectories)
            : Array.Empty<string>();
        return (files.Length, files.Length == 0 ? 1 : 0); // 0 isabet → uydurma kanıt SİL
    }
}
// Kullanım kanıtı: client/ dizini YOK → 0 isabet → iddialar silindi (Truth Mode §9.3/2)
```

### 3.9 C — imza sözleşmesi keşfi (md5 `api_sig` uyarısı)

```c
/* KEŞİF: Last.fm api_sig (md5) · Discogs OAuth 1.0a personal token.
 * GÜVENLİK NOTU: md5 imza SADECE servis sözleşmesidir — parola/veri
 * öz-dijitleme (self-hashing) için ASLA kullanılmaz (OWASP: MD5 yasak).
 */
#include <stddef.h>

typedef struct {
    const char *method;     /* e.g. "auth.getSession" */
    const char *api_key;    /* .env'den gelir — .md'ye YAZILMAZ (ADR-095 §1.2) */
    const char *token;
} LastfmCall;

/* İmza sadece sıralı parametre katıştırması: api_key+method+token */
size_t build_sig_input(const LastfmCall *c, char *out, size_t cap) {
    int n = snprintf(out, cap, "api_key%smethod%stoken%s",
                     c->api_key ? c->api_key : "", c->method, c->token);
    return (n > 0 && (size_t)n < cap) ? (size_t)n : 0;
}
/* Gerçek md5 kütüphanesi entegrasyon kodudur → onay sonrası (bu dosya dilekçedir). */
```

### 3.10 Bash — curl kota taraması

```bash
#!/usr/bin/env bash
# KEŞİF KALIBI: servis kotası/erişim turu — sonuç dosyaya, yorum yok, sayı uydurma yok.
# Örnek tur (tur-2, 2026-10-17): 12 servis · 18 URL · 0 hata.
set -euo pipefail

URLS=(
  "https://developers.deezer.com/api"
  "https://musicbrainz.org/doc/Development/XML_Web_Service/Rate_Limiting"
  "https://developer.spotify.com/documentation/web-api"
)

out="discovery-probe-$(date +%F).tsv"
: > "$out"

for u in "${URLS[@]}"; do
  code=$(curl -sS -o /dev/null -w '%{http_code}' -I --max-time 10 "$u" || echo 000)
  if [[ "$code" == "000" ]]; then
    printf '%s\t⚠️ DEAD?\t-\n' "$u" >> "$out"
  else
    printf '%s\tok\t%s\n' "$u" "$code" >> "$out"
  fi
done
echo "rapor: $out"
```

### 3.11 PowerShell — kanonik ölçüm + 0-isabet kanıtı

```powershell
# KANON (registry §9.12.1): dogru yontem = [System.IO.File]::ReadAllLines().Length
# YANLIS yontem (gecersiz): Get-Content $f | Measure-Object -Line

function Get-VaultLineCount([string]$Path) {
    [System.IO.File]::ReadAllLines($Path).Length
}

# 0-isabet kaniti: uydurma repo iddialari Test-Path/glob ile SIFIRlanir
function Test-FabricatedClaim([string]$Root, [string]$Pattern) {
    $hits = Get-ChildItem -Path $Root -Filter $Pattern -Recurse -File -ErrorAction SilentlyContinue
    if ($hits.Count -eq 0) { return "SILINDI (0 isabet)" }
    return "VAR ($($hits.Count) isabet)"
}

# Ornek: Test-FabricatedClaim C:\www\coremusic.net 'signal*.js'
#   -> client/ dizini YOK; yalniz router sinyal util satiri (domain sinyali DEGIL)
```

### 3.12 SQL — keşif bulgu kayıt şeması (sözleşme)

```sql
-- KEŞİF ÇIKTI KAYDI: bulgu, kapı durumu ve kanıt birlikte saklanır.
-- Kural: kanit_url zorunlu; kanitsiz bulgu "bulgu" sayılmaz.
CREATE TABLE discovery_finding (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service         VARCHAR(64)  NOT NULL,          -- spotify|tidal|musicbrainz|...
    finding_kind    ENUM('auth','quota','license','absence') NOT NULL,
    summary         VARCHAR(255) NOT NULL,
    evidence_url    VARCHAR(255) NOT NULL,          -- web dogrulama URL
    verified_on     DATE NOT NULL,                  -- erisim tarihi
    gate_state      ENUM('none','pending','rejected','ready') NOT NULL DEFAULT 'pending',
    notes           VARCHAR(500) NULL,
    UNIQUE KEY uq_service_kind (service, finding_kind, evidence_url(128))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- INSERT parametreli olur (SQL kurali): kanitsiz satir girilmez.
```

## §4. Kapsam dışı kodlar (bilerek YOK)

| Yok | Neden |
|---|---|
| YTM (YouTube Music) istemcisi | kapı KAPALI — ADR-097 §1/6 |
| Token/anahtar gömülü örnekler | ADR-095 §1.2 secret/log yasağı |
| Entegrasyon kodu (gerçek servis çağrısı) | onay kapısı — AGENTS.md §1 |
| `SignalType`/`ListenType` enum örnekleri | vault'ta 0 isabet — uydurma yasağı |

## §5. Açık sorular

1. ⚠️ Bloklar K020-alt katmanlarına girmeden önce onay (APPROVAL → IMPLEMENTATION).
2. ⚠️ SQL şeması yalnız sözleşme örneğidir; uygulanması K007-data ile birlikte ADR ister.
3. ⚠️ Deezer kotası bilinmiyor — blokta `None → UNKNOWN` korunur.

---

**Authority:** Bayram Ali / Vault Steward
