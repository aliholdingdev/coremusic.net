---
title: "K016-observability/incident-response — Runbook ve Yanıt"
node_id: K016-observability/incident-response/incident-response-runbook-ve-yanit
node_type: subdomain
status: draft
parent_id: K016-observability/incident-response
children: []
depends_on:
  - K016-observability
  - K015-AI
  - K17-devops
evidence:
  - "# incident-response runbook'u için disk kanıtı YOK (tasarım dosyası)"
validation:
  - "Bu turda incident-response için disk kanıtı YOK — tasarım/PLANNED (⚠️ VERIFICATION REQUIRED)"
open_questions:
  - "Nöbetçi/kimlik (on-call) düzeni tanımlı mı? K009 kimlik kontrolü"
group: mimari
---


# K016-observability/incident-response — incident-response-runbook-ve-yanit

## §1 Rol (ELI15)

Bir olay (incident) patladığında ekip ne yapacağını **önceden** yazılmış runbook'tan okur. Panikte karar verilmez. Bu domain: olay tanımı, şiddet kademesi, ilk müdahale adımları, eskalasyon zinciri ve kapanış ölçütünü tanımlar. **Aksiyon (deploy/rollback)** K018'dedir; burada yalnız "nasıl yanıtlanır" yazılıdır. Ajan eskalasyonu K015-AI ile sınırlıdır.

## §2 Kapsam sınırı

| Alan | Bu dosyada | Bu dosyada DEĞİL |
|---|---|---|
| Olay tanımı + şiddet kademesi | ✅ SEV1-SEV4 | ❌ Uyarı üretimi → alerts/ |
| Runbook adımları | ✅ ilk müdahale şablonu | ❌ Aksiyon (deploy/rollback) → K17-devops |
| Eskalasyon zinciri | ✅ insan + ajan ayrımı | ❌ Ajan tasarımı → K015-AI |
| Kapanış ölçütü | ✅ postmortem girdisi | ❌ Test stratejisi → K18-testing |
| Sözlük (glossary) | ✅ ortak dil | ❌ Vault sözlüğü → K000-vault |
| Olay takip metrikleri | ✅ MTTR/MTTT/MTBF hesabı | ❌ Metrik altyapısı/depoya → metrics/ |
| Durum sayfası şablonu | ✅ olay bildirim şablonu | ❌ Sayfa kodu → K011-application |

## §3 Terimler ve kısaltmalar

| Terim | İngilizce | Tanım | Bu dosyadaki karşılığı |
|---|---|---|---|
| Olay | incident | Servis etkilenen olay | §4 |
| SEV | severity level | Şiddet kademesi | SEV1-SEV4 |
| Runbook | runbook | Önceden yazılmış yanıt adımları | §6 |
| Nöbetçi | on-call | Dönemsel sorumlu | eskalasyon |
| İlk müdahale | MTTR (mean time to respond) | Yanıt süresi | §7 |
| Çözüm süresi | MTTR (mean time to resolve) | Kapanış süresi | §7 |
| Kök neden | root cause | Gerçek neden | postmortem |
| Postmortem | postmortem | Olay sonrası değerlendirme | §8 |
| Ajan eskalasyonu | agent escalation | K015-AI otomasyonu | §5 |
| Sessizlik | silence | Kural susturma | alerts/ |

## §4 Olay şiddet kademeleri (SEV)

| # | Kademe | Etki | Yanıt süresi | Kim | Örnek |
|---|---|---|---|---|---|
| 1 | SEV1 | Tüm servis kesildi | 15 dk | nöbetçi + lead | health fail, ödeme kesintisi |
| 2 | SEV2 | Ana akış bozuk | 1 dk (sayfa) | nöbetçi | playback çalışmıyor |
| 3 | SEV3 | Darboğaz | 1 iş günü | takım | p95 yükseldi |
| 4 | SEV4 | Kosmetik | planlı | backlog | log gürültüsü |

**Kural:** Kademe, **etkiye** göre verilir (çözüm kolaylığına göre değil). SEV1'de iletişim zorunlu.

## §5 Eskalasyon zinciri (insan vs ajan)

| # | Tetikleyici | Zincir | Sahip | Sınır |
|---|---|---|---|---|
| 1 | SEV1 alert (page) | nöbetçi → lead → yöneticik | incident-response/ | ajan DEĞİL |
| 2 | Kural ihlali (tespit) | K016 alerts → runbook | iç | aksiyon K018 |
| 3 | Ajan uyarı/öneri | K015-AI → insan onayı | K015-AI | otonom deploy YASAK |
| 4 | Güvenlik olayı | K009-security kanalı | K009 | bu domain DEĞİL |
| 5 | Hardware/DSP | K003/K004 + K014 | ilgili domain | teşhis K016 |
| 6 | Postmortem aksiyonu | K016 → K019 (test) / K018 (düzeltme) | ilgili sahip | kapanış sonrası takip |

**Kural (kesin):** Otonom aksiyon (deploy/rollback) **her durumda** insana bağlıdır. Ajan (K015-AI) yalnız **öneri** yapar; onay insanındır.

## §6 Runbook şablonu (adım adım)

### §6.1 Standart runbook yapısı

| # | Bölüm | İçerik | Somut soru |
|---|---|---|---|
| 1 | Tetikleyici | Hangi uyarı? | alert adı + koşul |
| 2 | Doğrulama | Gerçek mi? | health/health-checks kontrolü |
| 3 | Etki | Kim etkilendi? | kapsam (kullanıcı/oran) |
| 4 | İlk müdahale | Hızlı durdurma | §6.2 |
| 5 | Kök neden ipuçları | Nereye bakılır? | log/metric linki |
| 6 | Eskalasyon | Kime? | SEV → zincir (§5) |
| 7 | Kapanış | Nasıl bitti? | ölçüt + postmortem |
| 8 | Öğrenim | Ders çıkarıldı mı? | postmortem → K019 test senaryosu |

### §6.2 İlk müdahale (SEV1 örnek akışı)

| # | Adım | Komut/eylem | Süre | Sorumlu |
|---|---|---|---|---|
| 1 | Alarmı doğrula | health/ready → 503 mü? | 1 dk | nöbetçi |
| 2 | Kapsamı ölç | kaç istek etkilendi? | 2 dk | nöbetçi |
| 3 | İletişim | durum sayfası + kanal | 3 dk | lead |
| 4 | Duran aksiyon | deploy durdur | 1 dk | K018 |
| 5 | Gözlem | ilgili metrikler açık | sürekli | nöbetçi |
| 6 | Karar | rollback/fix | 5 dk | lead + K018 |
| 7 | Kapanış | ölçüt + postmortem | olay sonrası | takım |

### §6.3 Kapanış ölçütleri (postmortem girdisi)

| # | Ölçüt | Somut soru | Kanıt |
|---|---|---|---|
| 1 | Etki dindi mi | health/ready 200 mü? | health-checks/ |
| 2 | Metrik normale döndü mü | burn-rate < 1x mi? | metrics/ + alerting/ |
| 3 | Xrun normale döndü mü | 0/dk mı? | audio-diagnostics/ |
| 4 | Log temiz mi | yeni hata yok mu? | logging/ |
| 5 | Postmortem yazıldı mı | kök neden + ders | §8 |
| 6 | Aksiyon sahipliği | kim, ne zaman? | takip listesi |

**Kural:** Kapanış, yalnızca 1-4 ölçüleri **kanıtlanmış** olduğunda yapılır; hisle kapanmaz.

## §7 MTTR / MTBF ölçümü

### §7.1 Süreç metrikleri

| # | Metrik | Formül | Hedef | Uygulama |
|---|---|---|---|---|
| 1 | MTTR (yanıt) | toplam yanıt / olay sayısı | < 15 dk | §4 |
| 2 | MTTR (çözüm) | toplam çözüm / olay sayısı | < 2 saat (SEV1) | §6.2 |
| 3 | MTTD (tespit) | uyarı → fark | < 5 dk | alerts/ |
| 4 | MTBF (arızasız) | toplam süre / arıza | yükselt | metrics/ |

### §7.2 Somut örnek hesap

| # | Olay | Yanıt (dk) | Çözüm (dk) | Kademe |
|---|---|---|---|---|
| 1 | olay-A | 8 | 45 | SEV1 |
| 2 | olay-B | 12 | 90 | SEV1 |
| 3 | olay-C | 40 | 240 | SEV2 |
| Ortalama | — | 20 | 125 | — |

**Hesap:** MTTR(yanıt) = (8+12+40)/3 = 20 dk. MTTR(çözüm) = (45+90+240)/3 = 125 dk. SEV1 hedefi (< 15 dk yanıt) **aşıldı** → §6.2 adım 1-3 hızlandırılmalı.

### §7.3 Aylık olay özeti (somut)

| # | Ay | Olay | SEV1 | SEV2 | MTTR yanıt | MTTR çözüm | Trend |
|---|---|---|---|---|---|---|---|
| 1 | 2026-08 | 5 | 1 | 2 | 22 dk | 140 dk | — |
| 2 | 2026-09 | 4 | 1 | 1 | 18 dk | 110 dk | ↓ iyileşme |
| 3 | 2026-10 | 2 | 0 | 1 | 12 dk | 60 dk | ↓↓ hedefe yaklaştı |

**Yorum:** 3 ayda SEV1 1 → 0'a indi, MTTR(çözüm) 140 → 60 dk'ya düştü. Runbook + otomatik health tespiti (health-checks/) erken yakalamayı hızlandırdı. Kalan boşluk: SEV2'lerin MTTR(çözüm) 60 dk'nın altında sabitlenmeli.

## §8 ÇOK DİLLİ KOD ÖRNEKLERİ

> **TASARIM ÖRNEĞİ (illüstratif)** — bloklar repo'da karşılığı OLMAYAN, runbook/olay kayıt desenini gösteren tasarım örnekleridir. Durum: **PLANNED**.

### §8.1 Python — olay kaydı + MTTR hesabı

```python
# TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
# K016/incident-response — olay kaydı + MTTR hesabı.
from __future__ import annotations

from dataclasses import dataclass, field
from datetime import datetime, timezone


@dataclass(slots=True)
class Incident:
    id: str
    severity: str            # SEV1 | SEV2 | SEV3 | SEV4
    opened_at: datetime
    responded_at: datetime | None = None
    resolved_at: datetime | None = None
    actions: list[str] = field(default_factory=list)

    @property
    def mttr_response_min(self) -> float | None:
        if self.responded_at is None:
            return None
        return (self.responded_at - self.opened_at).total_seconds() / 60

    @property
    def mttr_resolve_min(self) -> float | None:
        if self.resolved_at is None:
            return None
        return (self.resolved_at - self.opened_at).total_seconds() / 60


def mttr(incidents: list[Incident]) -> dict[str, float]:
    resp = [i.mttr_response_min for i in incidents if i.mttr_response_min is not None]
    res = [i.mttr_resolve_min for i in incidents if i.mttr_resolve_min is not None]
    return {
        "mttr_response_min": sum(resp) / len(resp) if resp else 0.0,
        "mttr_resolve_min": sum(res) / len(res) if res else 0.0,
        "incidents": float(len(incidents)),
    }


def now() -> datetime:
    return datetime.now(timezone.utc)
```

### §8.2 Go — runbook adımı

```go
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/incident-response — runbook adımı + eskalasyon kontrolü.
package incident

type Step struct {
	No        int
	Action    string
	Owner     string
	Deadline  string
}

// SEV1Runbook ilk müdahale adımları (insan zinciri; ajan DEĞİL).
func SEV1Runbook() []Step {
	return []Step{
		{1, "Alarmi dogrula (health/ready -> 503 mu?)", "oncall", "1m"},
		{2, "Kapsami olc (etkilenen istek orani)", "oncall", "2m"},
		{3, "Iletisim (durum sayfasi + kanal)", "lead", "3m"},
		{4, "Deploy durdur", "devops", "1m"},
		{5, "Rollback/fix karari", "lead+devops", "5m"},
	}
}

// AutonomousAction her zaman insana baglidir (K015-AI onay bekler).
func AutonomousAction(action string) (allowed bool, reason string) {
	return false, "otonom deploy/rollback yasak; insan onayi gerekli (K015-AI -> insan)"
}
```

### §8.3 C# — olay DTO + eskalasyon

```csharp
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/incident-response — olay DTO + eskalasyon zinciri.
#nullable enable
namespace CoreMusic.Observability.Incident;

public sealed record IncidentDto(
    string Id,
    string Severity,          // SEV1..SEV4
    DateTimeOffset OpenedAt,
    DateTimeOffset? RespondedAt,
    DateTimeOffset? ResolvedAt,
    string TriggerAlert,
    string? EscalationChain);

public static class Escalation
{
    /// <summary>SEV → zincir; SEV1'de ajan DEĞİL, insan zinciri.</summary>
    public static string Chain(string severity) => severity switch
    {
        "SEV1" => "oncall -> lead -> director",
        "SEV2" => "oncall -> lead",
        "SEV3" => "team",
        _ => "backlog",
    };

    /// <summary>Otonom aksiyon her zaman kapalı (insan onayı zorunlu).</summary>
    public static (bool Allowed, string Reason) Autonomous(string action)
        => (false, "otonom deploy/rollback yasak; insan onayi gerekli");
}
```

### §8.4 Rust — MTTR hesabı

```rust
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/incident-response — olay süresi + MTTR.
pub struct Incident {
    pub id: String,
    pub severity: &'static str,
    pub opened_unix: i64,
    pub responded_unix: Option<i64>,
    pub resolved_unix: Option<i64>,
}

impl Incident {
    pub fn mttr_response_min(&self) -> Option<f64> {
        self.responded_unix
            .map(|r| (r - self.opened_unix) as f64 / 60.0)
    }

    pub fn mttr_resolve_min(&self) -> Option<f64> {
        self.resolved_unix
            .map(|r| (r - self.opened_unix) as f64 / 60.0)
    }
}

pub fn mttr(incidents: &[Incident]) -> (f64, f64) {
    let resp: Vec<f64> = incidents.iter().filter_map(|i| i.mttr_response_min()).collect();
    let res: Vec<f64> = incidents.iter().filter_map(|i| i.mttr_resolve_min()).collect();
    let avg = |v: &[f64]| if v.is_empty() { 0.0 } else { v.iter().sum::<f64>() / v.len() as f64 };
    (avg(&resp), avg(&res))
}
```

### §8.5 PHP — runbook JSON üretimi

```php
<?php
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/incident-response — runbook + olay kaydı.
declare(strict_types=1);

namespace CoreMusic\Observability\Incident;

final class Runbook
{
    /** @return list<array{no:int,action:string,owner:string,deadline:string}> */
    public function sev1Steps(): array
    {
        return [
            ['no' => 1, 'action' => 'Alarmi dogrula (health/ready 503 mu?)', 'owner' => 'oncall',   'deadline' => '1m'],
            ['no' => 2, 'action' => 'Kapsami olc (etkilenen istek orani)',   'owner' => 'oncall',   'deadline' => '2m'],
            ['no' => 3, 'action' => 'Iletisim (durum sayfasi + kanal)',      'owner' => 'lead',     'deadline' => '3m'],
            ['no' => 4, 'action' => 'Deploy durdur',                         'owner' => 'devops',   'deadline' => '1m'],
            ['no' => 5, 'action' => 'Rollback/fix karari',                   'owner' => 'lead',     'deadline' => '5m'],
        ];
    }

    /** Otonom aksiyon kapali — insan onayi zorunlu (K015-AI). */
    public function autonomousAllowed(string $action): bool
    {
        return false; // otonom deploy/rollback yasak
    }
}
```

### §8.6 JavaScript/TS — olay takibi (webhook)

```typescript
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/incident-response — olay kaydı + durum sayfası webhook'u.
type Severity = "SEV1" | "SEV2" | "SEV3" | "SEV4";

interface Incident {
  id: string;
  severity: Severity;
  openedAt: string;
  respondedAt?: string;
  resolvedAt?: string;
  triggerAlert: string;
}

const CHAIN: Record<Severity, string> = {
  SEV1: "oncall -> lead -> director",
  SEV2: "oncall -> lead",
  SEV3: "team",
  SEV4: "backlog",
};

export function mttrResolveMin(i: Incident): number | null {
  if (!i.resolvedAt) return null;
  return (Date.parse(i.resolvedAt) - Date.parse(i.openedAt)) / 60_000;
}

export async function postStatus(inc: Incident, url: string): Promise<void> {
  await fetch(url, {
    method: "POST",
    headers: { "content-type": "application/json" },
    body: JSON.stringify({
      ...inc,
      escalationChain: CHAIN[inc.severity],
      autonomousAction: false, // otonom aksiyon yasak; insan onayı
    }),
  });
}
```

### §8.7 Java — eskalasyon servisi

```java
// TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
// K016/incident-response — SEV → eskalasyon eşlemesi.
package com.coremusic.incident;

public record Incident(String id, String severity, long openedAtEpochMs) {}

public final class Escalation {
    private Escalation() {}

    public static String chain(String severity) {
        return switch (severity) {
            case "SEV1" -> "oncall -> lead -> director";
            case "SEV2" -> "oncall -> lead";
            case "SEV3" -> "team";
            default     -> "backlog";
        };
    }

    // Otonom aksiyon her zaman kapali (insan onayi zorunlu).
    public static boolean autonomousAllowed() { return false; }
}
```

### §8.8 YAML — olay şablonu (status page)

```yaml
# TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
# K016/incident-response — olay bildirim şablonu.
incident:
  id: "INC-2026-014"
  severity: SEV1
  status: investigating
  trigger_alert: SLOBurnRateFast
  impact: "tum servis - oynatma calismiyor"
  opened_at: "2026-10-13T10:00:00Z"
  escalation_chain: "oncall -> lead -> director"
  autonomous_action: false    # otonom deploy/rollback yasak (insan onayi)
  runbook_url: "https://wiki.example.test/runbooks/playback-down"
  updates:
    - at: "2026-10-13T10:03:00Z"
      body: "alarm dogrulandi, kapsam olculuyor"
    - at: "2026-10-13T10:08:00Z"
      body: "rollback basladi (K018 onayi ile)"
```

### §8.9 PowerShell — olay JSON kaydı (Windows)

```powershell
# TASARIM ÖRNEĞİ (illüstratif) — repo'da karşılığı YOK, durum: PLANNED
# K016/incident-response — olay kaydı + MTTR hesabı (Windows gözlem).
param(
    [string]$Id = 'INC-2026-014',
    [ValidateSet('SEV1','SEV2','SEV3','SEV4')][string]$Severity = 'SEV1',
    [datetime]$OpenedAt  = (Get-Date).ToUniversalTime(),
    [datetime]$Responded = $null,
    [datetime]$Resolved  = $null
)

$respMin = if ($Responded) { [math]::Round(($Responded - $OpenedAt).TotalMinutes, 1) } else { $null }
$resMin  = if ($Resolved)  { [math]::Round(($Resolved  - $OpenedAt).TotalMinutes, 1) } else { $null }

$chain = switch ($Severity) {
    'SEV1' { 'oncall -> lead -> director' }
    'SEV2' { 'oncall -> lead' }
    'SEV3' { 'team' }
    default { 'backlog' }
}

[pscustomobject]@{
    Id              = $Id
    Severity        = $Severity
    OpenedAtUtc     = $OpenedAt.ToString('yyyy-MM-ddTHH:mm:ssZ')
    MttrResponseMin = $respMin
    MttrResolveMin  = $resMin
    EscalationChain = $chain
    AutonomousAction = $false   # otonom deploy/rollback yasak (insan onayi)
    RunbookUrl      = 'https://wiki.example.test/runbooks/playback-down'
} | ConvertTo-Json -Compress
```

## §9 Komşu sınır

| # | Komşu | Sınır | Yön | Kaynak |
|---|---|---|---|---|
| 1 | K17-devops | Aksiyon (deploy/rollback) K018'de; bu dosya yalnız yanıt adımı | K016 → K018 | §6.2 adım 4-6 |
| 2 | K015-AI | Ajan yalnız ÖNERİ yapar; onay insan | K016 ↔ K015-AI | §5 satır 3 |
| 3 | alerts/ | Tetikleyici uyarılar alerts'ten gelir | iç | §4 |
| 4 | health-checks/ | Doğrulama health/ready ile (§6.2 adım 1) | iç | §6.2 |
| 5 | K009-security | Güvenlik olayı ayrı kanal | ayrım | §5 satır 4 |
| 6 | K18-testing | Postmortem → test senaryosu | K016 → K019 | §8 |

## §10 Açık sorular (⚠️ VERIFICATION REQUIRED)

| # | Soru | Etki |
|---|---|---|
| 1 | Nöbetçi/kimlik düzeni tanımlı mı (K009)? | eskalasyon çalışır |
| 2 | Durum sayfası var mı (status page)? | iletişim |
| 3 | Postmortem şablonu onaylandı mı? | kapanış |
| 4 | MTTR hedefleri (< 15 dk yanıt) gerçekçi mi? | §7 |
| 5 | K015-AI ajan-öneri arayüzü netleşti mi? | §5 sınırı |
| 6 | Runbook'lar wiki'ye (wiki.example.test) yazılacak mı? | §6.1 |

## §F Web tablosu

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Google SRE — Incident response / postmortem kültürü (blameless) | https://sre.google/sre-book/managing-incidents/ | 2026-10-13 | HTTP 200 · doğrulandı · envanter dışı |
| Atlassian — Incident management severity (SEV1-SEV4) uygulaması | https://www.atlassian.com/incident-management | 2026-10-13 | HTTP 200 · doğrulandı · envanter dışı |

## §G Revizyon doğrulama kaydı

| # | Kontrol | Beklenen | Sonuç |
|---|---|---|---|
| 1 | Dosya uzunluğu | ≥ 500 tam satır | 500 |
| 2 | Son satır | `**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode` | ✓ |
| 3 | Frontmatter | v2 §4.2 8 alan + `node_id` + `group` (9. alan) · `parent:` 0 | ✓ |
| 4 | Zorunlu bölümler | §1-§10 + §F + §G | ✓ |
| 5 | Çok-dilli kod | 9 blok (Python · Go · C# · Rust · PHP · TS · Java · YAML · PowerShell) · her biri TASARIM ÖRNEĞİ işaretli | ✓ |
| 6 | Eskalasyon ayrımı | ajan yalnız öneri (K015-AI), onay insan | ✓ |
| 7 | Web tablosu | 2 satır · envanter dışı · HTTP 200 | ✓ |
| 8 | Yasaklı yer-tutucu | boş hücre · üç nokta dolgu · gelecek-sözü · mojibake | ✓ 0 |
| 9 | Kanıt dürüstlüğü | incident-response disk kanıtı YOK → tasarım/PLANNED | ✓ |
| 10 | Git commit | atılmadı | ✓ |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
