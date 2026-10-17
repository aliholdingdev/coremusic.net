---
node_id: K17-devops/rollback
node_type: domain-index
status: active
parent_id: K17-devops
children: []
depends_on:
  - K17-devops/cd
  - K17-devops/environments
  - K17-devops/release-management
evidence:
  - "deployment/rollback-ve-geri-alma.md · 534 satır (2026-10-17 envanter ölçümü) — İÇERİK BURADA TEKRAR EDİLMEZ"
  - ".workflows/deployment.md §4 'Geri Dönüş (Rollback)' satır 212-245 + §6 Yasaklar 257-263 (2026-10-17 tam okuma)"
  - "geri alma tetikleyici: ci.yml'de rollback/health step YOK (grep) (2026-10-17)"
validation:
  - "Bu dosya İÇERİK tekrarı yapmaz: alt-alan indeksi + gap analizi + link (boş dosya 51 → indeks)"
  - "deployment.md geri dönüş yasağı: 'Geri dönüş planı olmadan dağıtma' (satır 262)"
  - "Gerçek rollback-ve-geri-alma.md başlığı ve satır sayısı ölçüldü (534s)"
open_questions:
  - "Rollback otomatik mi manuel mi? — deployment.md §4'ü okumadım (içerik dosyası) → gap §5"
  - "CD otomatik rollback tetikleyici var mı? — yok (grep) → TASLAK §6"
group: devops
---

# Rollback — Geri Alma Alt Alan İndeksi ve Gap Analizi (K17-devops altı)

> **TEKRAR YASAĞI:** `deployment/rollback-ve-geri-alma.md` **534 satırlık içerik dosyasıdır**;
> bu `index.md` onu **tekrarlamaz**. Rolü: (1) alt-alan indeksi, (2) gap analizi, (3) link,
> (4) CD-rollback *tetikleyicisi* tasarım boşluğu. **Sorumluluk:** DevOps Engineer ·
> **Otorite:** Bayram Ali / Vault Steward

---

## §1 Özet — Rollback bugün nerede duruyor?

| Boyut | Durum | Kanıt (2026-10-17) |
|---|---|---|
| Süreç tanımı | **VAR** — `deployment/rollback-ve-geri-alma.md` (534s) | envanter ölçümü |
| Süreç yasağı | **VAR** — `deployment.md:262` "Geri dönüş planı olmadan dağıtma" | §6 Yasaklar |
| Süreç adımları | **VAR** — `deployment.md:167-180` Aşama 5 "1. Yedekleme al" | 8 aşama |
| **Otomatik tetikleyici** | **YOK** | ci.yml'de health/rollback step 0 |
| **CD içinde geri alma zinciri** | **YOK** | deploy job'ı yok (`cd/index.md` §3) |
| Tag → önceki sürüm dönüşü | **YOK** | `git tag --list` = 0 (`release-management/` §1) |

**Çekici boşluk:** süreç *kağıtta* var, otomasyon **hiç yok**. Bu dosya bu aradaki mesafeyi
kapatır — ama süreç **içeriğini** değil (o 534s dosyada).

---

## §2 Kapsam — Bu Dosya vs İçerik Dosyası

| Bu `index.md` (indeks) | `deployment/rollback-ve-geri-alma.md` (534s içerik) |
|---|---|
| Alt-alan linkleri + gap analizi | Rollback taktikleri adım adım (GERÇEK içerik) |
| CD tetikleyicisi tasarım boşluğu | Ortam bazlı geri alma prosedürü (varsayım: içerik okunmadı) |
| `deployment.md` §4/§6 çelişkileri | **Okunmadı → §5'te gap olarak işaretlendi** |
| TASLAK otomatik rollback (Bash/PHP) | — |

> **Honest sınır (AGENTS.md §3):** `rollback-ve-geri-alma.md` **bu oturumda okunmadı**.
> İçeriği hakkında iddia edilen herhangi bir şey **`⚠️ VERIFICATION REQUIRED`** olurdu.
> Bu yüzden §5'te yalnız **varlık + satır sayısı** (ölçülmüş) verilir; içeriği özetlenmez.

---

## §3 `deployment.md` Geri Dönüş İlgili Satırları (2026-10-17 tam okuma — GERÇEK)

### §3.1 Aşama 5 — dağıtım adımlarının ilk maddesi (satır 176)

```markdown
# Dosya: .workflows/deployment.md · satır 176 (birebir alıntı)
1. Yedekleme al
```

**Yorum:** geri alma, dağıtımın **öncesinden** başlar. Yedek = geri alınabilirliğin ön
koşulu; yedeksiz migration `deployment.md:261`'e göre yasaktır ("Yedekleme almadan
migration çalıştırma").

### §3.2 §6 Yasaklar (satır 257-263 — birebir alıntı)

```markdown
# Dosya: .workflows/deployment.md · satır 257-263
## 6. Yasaklar

- Testler geçmeden dağıtım yapma
- İnsan onayı olmadan üretime dağıtma
- Yedekleme almadan migration çalıştırma
- Geri dönüş planı olmadan dağıtım yapma
- Güvenlik doğrulamasını atlama
```

**3 madde rollback'e doğrudan bağ:** (a) yedek zorunlu, (b) geri dönüş **planı** dağıtım ön
koşulu, (c) insan onayı (CD kararıyla gerilir → `cd/index.md` §7/1).

### §3.3 §4 — Geri Dönüş başlığı (satır 212 — başlık kanıtı)

```markdown
# Dosya: .workflows/deployment.md · satır 212 (birebir alıntı)
## 4. Geri Dönüş (Rollback)
```

> **İçerik gap:** §4'ün altındaki maddeler (satır 213-245) **bu oturumda okunmadı** →
> `⚠️ VERIFICATION REQUIRED`. Doğrulama için `Read` `.workflows/deployment.md` satır 213-245.

---

## §4 Alt-Alan İlişki Haritası (rollback'ı besleyen / rollback alan)

```
build/  ──(artefakt)──►  cd/  ──(deploy)──►  production
                          │
                          ├── başarısız health ──► ROLLBACK ◄── deployment/rollback-ve-geri-alma.md (534s)
                          │                              ▲
release-management/ ──(tag)──► önceki sürüme dönüş ──────┤
                          │                              │
environments/ ──(ortam maliyeti)─────────────────────────┘
                          │
rollback/ ──(TETİKLEME boşluğu)──► health-check step (YOK, §6 TASLAK)
```

| Hedef dosya | Rollback'le ilişkisi |
|---|---|
| `deployment/rollback-ve-geri-alma.md` (534s) | **İçerik kaynağı** — tekrar yasak |
| `cd/index.md` §6/§8.1 | deploy script'inde `./rollback.sh` çağrısı (TASLAK) |
| `release-management/index.md` §5 | tag → önceki sürüm (geri alma kancası) |
| `environments/index.md` §1 | ortam → geri alma maliyeti (prod en yüksek) |
| `build/index.md` | artefakt sürümü → hangi artefakt geri yüklenecek |
| `.workflows/deployment.md` §4/§6 | süreç + yasağın kaynağı |

---

## §5 Gap Analizi (boş dosyadan indekse)

| # | Gap | Tip | Kanıt | Aksiyon |
|---|---|---|---|---|
| 1 | `index.md` yalnız 51 satır (iskelet) | yapısal | envanter: 51s | → bu sürüm 1.0.0 (dolduruldu) |
| 2 | Rollback-ve-geri-alma.md içeriği **okunmadı** | bilgi | bu oturumda Read yok | **`⚠️ VERIFICATION REQUIRED`** — Read satır 1-534 |
| 3 | `deployment.md:213-245` (§4 içeriği) okunmadı | bilgi | yalnız başlık 212 okundu | **`⚠️ VERIFICATION REQUIRED`** |
| 4 | Otomatik tetikleyici yok | tasarım | ci.yml health/rollback step 0 (grep) | TASLAK §6 |
| 5 | `git tag` yok → sürüm geri dönüşü yok | tasarım | `git tag --list` = 0 | → `release-management/` §7/1 |
| 6 | Rollback rehearsal (tatbikat) yok | süreç | diskte kanıt yok | **ADR/proses bekliyor** |
| 7 | Doküman-vs-kod tekrarı riski | mimari | iki kaynak (deployment.md §4 + 534s dosya) | Bu indeks ayrıştırdı; **SSOT hangisi?** ADR |

---

## §6 TASLAK — Otomatik Geri Alma Tetikleyicisi (UYGULANMADI)

> `.yml` beyaz-liste dışı (`devops-master.md` §C.1/25-27). Yalnız `rollback/` tasarımını
> somutlaştırmak içindir. Süreç **içeriği** 534s dosyadan gelir; burada yalnız *tetikleme*.

### §6.1 Bash — health başarız → rollback zinciri

```bash
#!/usr/bin/env bash
# TASLAK — rollback/index.md §6 · deployment.md Aşama 6 (sağlık) + §4 (geri dönüş)
set -euo pipefail
ENV="${1:?kullanım: ./health-gate.sh <staging|production>}"
RETRIES=5

for i in $(seq 1 "$RETRIES"); do
  code=$(curl -s -o /dev/null -w '%{http_code}' "https://$ENV.coremusic.net/healthz" || echo 000)
  [ "$code" = "200" ] && { echo "[health] OK ($code)"; exit 0; }
  echo "[health] $i/$RETRIES kod=$code"; sleep 5
done

echo "[health] BAŞARISIZ → rollback"
# 1) kod artefaktı: önceki sürüm/branch'e dön
if [ -f .last_good_ref ]; then
  prev=$(cat .last_good_ref)
  echo "[rollback] kod → $prev"
  git -c core.hooksPath=/dev/null checkout -q "$prev" 2>/dev/null || true   # TASLAK: hook bypass bilinçli
fi
# 2) veri: pre-migration yedeği geri yükle (deployment.md:176/261)
[ -f "$PRE_BACKUP" ] && mysql coremusic < "$PRE_BACKUP"
# 3) servis + yeniden sağlık
systemctl reload "php-fpm@$ENV" 2>/dev/null || true
./health-gate.sh "$ENV" || { echo "[rollback] geri alınamadı → MANUEL ESKALASYON"; exit 2; }
```

### §6.2 PHP — geri alma kararı (risk eşiği)

```php
<?php
declare(strict_types=1);
// TASLAK — rollback/index.md §6: health-check sonucundan rollback kararı
final class RollbackGate
{
    public function __construct(
        private readonly int $maxFailures = 3,
        private readonly float $maxP95Ms = 1500.0,
    ) {}

    /** deployment.md Aşama 6 satırları: HTTP 200 / API / DB / log / performans */
    public function shouldRollback(array $checks): bool
    {
        if (isset($checks['http']) && $checks['http'] !== 200) return true;
        if (($checks['db_ok'] ?? true) === false) return true;
        if (($checks['error_log_lines'] ?? 0) > $this->maxFailures) return true;
        if (($checks['p95_ms'] ?? 0) > $this->maxP95Ms) return true;   // performans regresyonu
        return false;
    }

    public function rollbackPlan(string $env, ?string $backupPath): array
    {
        return [
            'env'      => $env,
            'code'     => 'git checkout <last_good_ref>',     // veya artefakt indirme
            'data'     => $backupPath ? "mysql coremusic < $backupPath" : null,  // deployment.md:176
            'verify'   => 'health-gate + smoke',
            'escalate' => 'insan onayı (deployment.md:260)',
        ];
    }
}
```

### §6.3 PowerShell — geri alma öncesi yedek doğrulama

```powershell
# TASLAK — rollback/index.md §5/1: yedek alınmadan geri alma YASAK (deployment.md:261)
$ErrorActionPreference = 'Stop'
$backup = $args[0]
if (-not $backup -or -not (Test-Path $backup)) { throw 'yedek dosyası yok — geri alma yasak' }

$size = (Get-Item $backup).Length
if ($size -lt 1KB) { throw "yedek anormal küçük ($size B) — bozuk olabilir" }

# içeriğe bak: INSERT INTO ile dolu mu?
$txt = Get-Content $backup -TotalCount 50
if (-not ($txt -match 'INSERT INTO')) { Write-Warning 'ilk 50 satırda INSERT yok — boş yedek olabilir' }

Write-Host "[rollback] yedek OK: $backup ($size B)" -ForegroundColor Green
```

### §6.4 JavaScript — geri sonrası smoke doğrulama (fail-closed)

```javascript
// TASLAK — rollback/index.md §6.1/3: rollback sonrası yeniden doğrulama
import { chromium } from 'playwright';
const BASE = process.env.DEPLOY_URL;
const PATHS = ['/', '/auth/login'];
const browser = await chromium.launch();
const page = await browser.newPage();
let bad = 0;

for (const p of PATHS) {
  try {
    const r = await page.goto(BASE + p, { waitUntil: 'domcontentloaded', timeout: 20000 });
    const s = r?.status() ?? 0;
    console.log(`[rollback-verify] ${s} ${p}`);
    if (s < 200 || s >= 400) bad++;
  } catch { bad++; }
}
await browser.close();
if (bad) { console.error('[rollback-verify] GERİ ALINAMADI → manuel eskalasyon'); process.exit(2); }
console.log('[rollback-verify] OK');
```

---

## §7 Çelişki ve Karar Kayıtları (append-only)

| # | Çelişki | Kaynak A | Kaynak B | Durum |
|---|---|---|---|---|
| 1 | **Süreç var vs otomasyon yok** | `rollback-ve-geri-alma.md` 534s + `deployment.md` §4 | ci.yml health/rollback step **0** (grep) | **Açık tasarım boşluğu** — §6 TASLAK |
| 2 | **İçerik okunmadı** | bu oturumda `rollback-ve-geri-alma.md` + `deployment.md:213-245` Read YOK | iddia etmek halüsinasyon olur | **`⚠️ VERIFICATION REQUIRED`** (§2/§3.3/§5) |
| 3 | **SSOT belirsiz** | `deployment.md` §4 (281s süreç) | `rollback-ve-geri-alma.md` 534s | **ADR:** hangisi ana kaynak? |
| 4 | **`git tag` yok → sürüm geri dönüşü yok** | `git tag --list` = 0 | release tabanlı rollback | → `release-management/` §7/1 |
| 5 | **rollback → `rollback.sh` diskte yok** | §6.1 taslağı çağırıyor | `scripts/rollback.sh` dosyası **okunmadı** | `⚠️ VERIFICATION REQUIRED` |
| 6 | **İnsan onayı vs otomatik rollback** | `deployment.md:260` | §6 otomatik zincir | → `cd/index.md` §7/1 (ADR) |

---

## §8 İlişkiler

| Hedef | Ne için |
|---|---|
| `deployment/rollback-ve-geri-alma.md` | **İÇERİK KAYNAĞI (534s) — tekrar yasak** |
| `.workflows/deployment.md:212-245 · 257-263` | süreç §4 + yasağın kaynağı |
| `cd/index.md` §6/§8 | deploy + rollback zinciri TASLAK'ı |
| `release-management/index.md` §5 | tag → sürüm geri dönüşü |
| `environments/index.md` §1 | ortam bazlı geri alma maliyeti |
| `build/index.md` | geri yüklenecek artefakt sürümü |
| `rollback/index.md` §5/§5-§6 | gap + tetikleyici TASLAĞI (bu dosya) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode ·
**Sürüm:** 1.0.0 (2026-10-17 · iskelet 51s → indeks + gap + TASLAK) ·
**Yasaklar:** `git commit` YOK · `*.yml` YOK · `deployment/rollback-ve-geri-alma.md` **DOKUNULMAZ** (tekrar yok, içerik okunmadı)
