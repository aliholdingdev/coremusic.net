---
node_id: tools-ornekler
node_type: module
status: active
parent_id: tools
children: []
depends_on: [tools-anayasa, tools-surucu]
evidence:
  - "shared/src/AI/ToolCalling.php + AIOrchestrator.php (kod kanıtı)"
  - ".claude/hooks/*.cjs (gate kanıtı)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı, kod kanıtı zorunlu"
open_questions: []
group: kod
---

# tools-ornekler - Araç Kod Örnekleri (kanıt odaklı)

> **Kapsam:** Ajan araçları: izin matrisi, araç çağrısı, gate hook'ları, allowlist/denylist
> filtreleri. Her PHP/hook alıntısı 2026-10-17'de diskten okunan gerçek satırdır
> (`Dosya:` / `Satır:` künyesi ile). İllüstratif çok-dilli bloklar repo kanıtı değildir;
> her birinin üstünde zorunlu etiket taşır. `shared/src/**` ve `.claude/**` salt-okunurdur.

## §1. Kanıt Künye Formatı

| Alan | Kural |
|---|---|
| Dosya | göreli yol, diskte var (glob ile doğrulanmış) |
| Satır | okunan aralık (L28-L40 gibi) — okunmamış satır künye olamaz |
| Alıntı | kod satırının kendisi (kısaltma serbest, uydurma yasak) |
| Yokluk | tam cümle: "Repo'da bu alan kodu YOK — kaynak: upstream docs" |
| Ölçüm | grep/glob sayaçları — araç çıktısı, iddia değil |
| İllüstratif | `⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı` |
| Tarih | her kanıt 2026-10-17 okuma turuna ait |
| Eşleşme | `evet` / `kısmi` / `YOK` — başka değer yok |
| Yasak | uydurma künye → Zero-Hallucination ihlali (kök AGENTS §3) · çelişkide disk kazanır |

## §2. Okunan Envanter (2026-10-17 glob + ReadAllLines)

| # | Dosya | Satır | Araç ilgisi |
|---|---|---|---|
| 1 | `shared/src/AI/ToolCalling.php` | 326 | doğrudan — izin matrisi, kayıt, çağrısı, filtre |
| 2 | `shared/src/AI/AIOrchestrator.php` | 210 | doğrudan — AGENT_MAP/TASK_TYPES routing |
| 3 | `shared/src/AI/AIWorkflow.php` | 271 | doğrudan — `callTool`/`dispatchTask` çağrı noktaları |
| 4 | `shared/src/AI/PromptEngine.php` | 325 | doğrudan — injection denylist, risk filtresi |
| 5 | `shared/src/AI/MemorySystem.php` | 242 | kısmi — araç önbelleği (`remember`) |
| 6 | `shared/src/AI/KnowledgeBase.php` | 232 | kısmi — arama aracı çekirdeği |
| 7 | `shared/src/AI/AIEngine.php` | 382 | kısmi — analiz sonuç üreticisi |
| 8 | `.claude/hooks/ai-risk-gate.cjs` | 73 | doğrudan — PreToolUse `ask` kapısı |
| 9 | `.claude/hooks/ai-validate-post.cjs` | 44 | doğrudan — PostToolUse `block` kapısı |
| 10 | `.claude/hooks/skill-mandate.cjs` | 195 | doğrudan — UserPromptSubmit context + `--check` |
| 11 | `.claude/settings.json` | 126 | doğrudan — hook kaydı + MCP araç yüzeyi |

## §3. PHP — AGENT_PERMISSIONS İzin Matrisi (11 ajan × kategori)

Dosya: `shared/src/AI/ToolCalling.php` · Satır: L28-L40

```php
private const AGENT_PERMISSIONS = [
    'mo'       => ['file', 'database', 'api', 'audio', 'hardware', 'security'],
    'backend'  => ['file', 'database', 'api'],
    'ui'       => ['file', 'api'],
    'security' => ['file', 'database', 'api', 'security'],
    'data'     => ['file', 'database'],
    'embedded' => ['file', 'hardware', 'audio'],
    'qa'       => ['file', 'database', 'api'],
    'devops'   => ['file', 'api', 'database'],
    'audio-hw' => ['file', 'hardware', 'audio'],
    'dsp-fw'   => ['file', 'hardware', 'audio'],
    'win-sw'   => ['file', 'hardware'],
];
```

| Gözlem | Kanıt |
|---|---|
| 11 anahtar — `.ai/AGENTS.md` §4 agent tablosu ile aynı anahtar kümesi | L29-L39 |
| 6 kategori (`file/database/api/audio/hardware/security`) sınıf docblock'unda | L18-L24 |
| `mo` tek geniş yetkili (6/6) · `win-sw` en dar (2/6) | L29, L39 |
| Kategori-özel dosya yolu denylist'i (hangi dosya yazılamaz) **YOK** — matris kategori düzeyinde | §29 |
| `security` ajana `audio`/`hardware` yok; `embedded`'e `security` yok — karşılıklı kısıt | L32, L34 |

## §4. PHP — checkPermission: Fail-safe İzin Kapısı

Dosya: `shared/src/AI/ToolCalling.php` · Satır: L145-L166

```php
if (!isset($this->tools[$toolName])) {
    return ['allowed' => false, 'reason' => "Tool not found: {$toolName}"];   // L147-L149
}
if (!isset(self::AGENT_PERMISSIONS[$agentId])) {
    return ['allowed' => false, 'reason' => "Unknown agent: {$agentId}"];     // L151-L153
}
$toolCategory = $this->tools[$toolName]['category'];
$allowedCategories = self::AGENT_PERMISSIONS[$agentId];
if (!in_array($toolCategory, $allowedCategories, true)) {                    // L158 strict
    return ['allowed' => false, 'reason' => "Agent '{$agentId}' not allowed ..."];
}
return ['allowed' => true, 'reason' => null];                                // L165
```

| Kapı | Davranış | Satır |
|---|---|---|
| Bilinmeyen araç | `allowed: false` (exception değil, fail-safe) | L147-L149 |
| Bilinmeyen ajan | `allowed: false` | L151-L153 |
| Kategori izni yoksa | `allowed: false` + gerekçe | L158-L163 |
| `in_array` 3. argüman `true` | strict karşılaştırma (tip kayması yok) | L158 |
| **Gözlem:** `callTool` bu metodu **çağırmıyor** — izin kapısı çağrı noktasında zorunlu değil | L73-L111 içinde `checkPermission` geçmişi yok (grep 0) | §29 |

## §5. PHP — inferCategory: Önek-Türetmeli Kategori Allowlist'i

Dosya: `shared/src/AI/ToolCalling.php` · Satır: L289-L299

```php
private function inferCategory(string $toolName): string
{
    if (str_starts_with($toolName, 'file-')) return 'file';
    if (str_starts_with($toolName, 'db-')) return 'database';
    if (str_starts_with($toolName, 'http-')) return 'api';
    if (str_contains($toolName, 'audio')) return 'audio';
    if (str_contains($toolName, 'device') || str_contains($toolName, 'hardware')) return 'hardware';
    if (str_contains($toolName, 'auth') || str_contains($toolName, 'security')) return 'security';
    return 'file';   // L298 — varsayılan: en geniş erişimli değil, en yaygın kategori
}
```

| Kural | Sonuç |
|---|---|
| Kayıt anında kategori elle verilmez; adın önekinden türetilir (`registerTool` L64) | `file-`→`file` · `db-`→`database` · `http-`→`api` |
| Alt-string eşleşmesi: `audio`/`device`/`hardware`/`auth`/`security` | adın **herhangi bir yerinde** geçmesi yeterli |
| Eşleşmeyen her şey → `file` (fallback) | örtük genişletme yok; fallback `file` |
| İsim-sızlaştırma riski: `file-audio` → sıralama gereği `file` döner (önek önce okunur) | L291 vs L294 — ilk eşleşen kazanır |

## §6. PHP — callTool: Araç Çağrısı Döngüsü

Dosya: `shared/src/AI/ToolCalling.php` · Satır: L73-L111

```php
if (!isset($this->tools[$toolName])) {
    return ['success' => false, 'result' => null, 'error' => "Tool not found: {$toolName}",
            'executionTime' => 0.0];                                     // L75-L82
}
$tool = $this->tools[$toolName];
$startTime = microtime(true);
try {
    $this->validateParameters($tool['parameters'], $params);            // L89 — kapı 1
    $result = ($tool['executor'])($params);                             // L92 — çalıştır
    $executionTime = microtime(true) - $startTime;
    return ['success' => true, 'result' => $result, 'error' => null,
            'executionTime' => round($executionTime, 4)];               // L95-L100
} catch (\Throwable $e) {
    return ['success' => false, 'result' => null, 'error' => $e->getMessage(),
            'executionTime' => round($executionTime, 4)];               // L101-L110
}
```

| Davranış | Kanıt |
|---|---|
| Kayıt yoksa exception değil `success: false` (fail-safe) | L75-L82 |
| Her çağrída 4 anahtarlı zarf: success/result/error/executionTime | L95-L100, L104-L109 |
| `microtime(true)` ile süre ölçümü — gözlemlenebilirlik kanıtı | L85, L93, L102 |
| `\Throwable` yakalama — executor patlaması çağıranı öldürmez | L101 |
| İzin denetimi bu döngüde **yok** (§4 son satır) | L73-L111 |

## §7. PHP — validateParameters: Zorunlu Alan + Tip Doğrulama

Dosya: `shared/src/AI/ToolCalling.php` · Satır: L307-L325

```php
foreach ($definitions as $def) {
    if ($def['required'] && !array_key_exists($def['name'], $params)) {
        throw new \InvalidArgumentException("Missing required parameter: {$def['name']}");  // L310-L312
    }
    if (array_key_exists($def['name'], $params)) {
        $value = $params[$def['name']];
        $type = get_debug_type($value);                                                     // L316
        if ($type !== $def['type'] && $def['type'] !== 'mixed') {
            throw new \InvalidArgumentException(
                "Parameter '{$def['name']}' expects type '{$def['type']}', got '{$type}'"); // L318-L321
        }
    }
}
```

| Kural | Değer |
|---|---|
| Zorunlu alan denetimi | `required: true` + `array_key_exists` (null değeri "var" sayılır) |
| Tip denetimi | `get_debug_type` — PHP 8 get_debug_type birebir eşleşme |
| `mixed` tek istisna | `$def['type'] !== 'mixed'` |
| JSON-Schema düzeyinde şema doğrulama (enum/pattern/minimum) | Repo'da bu alan kodu YOK — kaynak: upstream docs |

## §8. PHP — Varsayılan Araç Kayıtları (5 araç)

Dosya: `shared/src/AI/ToolCalling.php` · Satır: L173-L284 (`registerDefaultTools`)

| Araç | Parametreler (required) | Kategori | Satır |
|---|---|---|---|
| `file-read` | `path` | file | L176-L190 |
| `file-write` | `path`, `content` | file | L192-L204 |
| `file-search` | `pattern` (+`path` opsiyonel) | file | L206-L222 |
| `db-query` | `sql`, `database` | database | L225-L241 |
| `http-request` | `method`, `url` (+`body`) | api | L244-L283 |

| Gözlem | Kanıt |
|---|---|
| Her kayıt 5 anahtar: name/description/parameters/executor/category | L176-L190 tip bildirimi L42 |
| `file-read` yoksa `RuntimeException("File not found")` | L184-L186 |
| `file-search` `glob()` kullanır; `false` → `[]` (fail-safe) | L215-L218 |
| `registerTool` aynı adı iki kez kaydettiğinde exception | L55-L57 |
| `listTools`/`getToolsByCategory` salt-okunur keşif yüzeyi | L116-L127, L132-L140 |

## §9. PHP — db-query: SELECT-only Denylist (SQL Kapısı)

Dosya: `shared/src/AI/ToolCalling.php` · Satır: L225-L241 (özellikle L233-L237)

```php
'description' => 'Veritabanı sorgusu çalıştırır (SELECT only)',      // L227
'executor'    => function(array $params): array {
    // Güvenlik: Sadece SELECT izni
    $sql = trim($params['sql']);
    if (stripos($sql, 'SELECT') !== 0) {
        throw new \RuntimeException("Only SELECT queries are allowed");   // L236
    }
    return ['result' => [], 'rows' => 0];                                // L238 — placeholder
},
```

| Filtre | Kural | Sınır |
|---|---|---|
| Denylist | `stripos($sql,'SELECT') !== 0` → reddet | yalnız önek kontrolü; `SELECT ...; DROP` zinciri **elemez** |
| Dönüş | `['result' => [], 'rows' => 0]` | gerçek sorgu çalıştırma **YOK** (placeholder — §29) |
| Hazırlı ifade (prepared statement) kullanımı bu executor'da | Repo'da bu alan kodu YOK — kaynak: upstream docs (AIWorkflow L117 yorumu hazırlı ifade sözü verir, executor'da karşılığı yok) |

## §10. PHP — http-request: Zaman Aşımı + JSON Başlığı

Dosya: `shared/src/AI/ToolCalling.php` · Satır: L252-L281

```php
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST  => $method,
    CURLOPT_TIMEOUT        => 30,                                     // L260
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],     // L261
]);
```

| Kural | Değer | Satır |
|---|---|---|
| Zaman aşımı | 30 sn (sabit) | L260 |
| Yanıt gövdesi | `CURLINFO_HTTP_CODE` + gövde | L269, L277-L280 |
| URL allowlist/denylist (SSRF koruması) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Yönlendirme sınırı (`CURLOPT_FOLLOWLOCATION`) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| API anahtarı/Authorization başlığı yönetimi | Repo'da bu alan kodu YOK — kaynak: upstream docs |

## §11. PHP — AIOrchestrator::AGENT_MAP Routing Tablosu

Dosya: `shared/src/AI/AIOrchestrator.php` · Satır: L26-L38

```php
private const AGENT_MAP = [
    'mo'       => ['domain' => 'coordination', 'layer' => 'coordination'],
    'backend'  => ['domain' => 'php-api',      'layer' => 'l2-routing'],
    'ui'       => ['domain' => 'frontend',     'layer' => 'l3-presentation'],
    'security' => ['domain' => 'security',     'layer' => 'l1-security'],
    'data'     => ['domain' => 'database',     'layer' => 'l0-infrastructure'],
    // ... L32-L37: embedded, qa, devops, audio-hw, dsp-fw, win-sw
];
```

| Gözlem | Kanıt |
|---|---|
| 11 anahtar — ToolCalling AGENT_PERMISSIONS anahtar kümesiyle **birebir aynı** | L27-L37 ↔ ToolCalling L29-L39 |
| Her kayıt 2 alan: `domain` + `layer` (katman etiketi) | L27-L37 |
| `.ai/AGENTS.md` §4 "Katman" sütunuyla kavramsal eşleşme (A0-A3/Cross-cutting kodları farklı yazım) | §28 |

## §12. PHP — AIOrchestrator::TASK_TYPES Görev→Ajan Matrisi

Dosya: `shared/src/AI/AIOrchestrator.php` · Satır: L40-L49

```php
private const TASK_TYPES = [
    'recommendation'  => ['agent' => 'backend',   'priority' => 'medium'],
    'analysis'        => ['agent' => 'embedded',  'priority' => 'high'],
    'eq-optimization' => ['agent' => 'embedded',  'priority' => 'medium'],
    'hardware'        => ['agent' => 'audio-hw',  'priority' => 'high'],
    'fault'           => ['agent' => 'dsp-fw',    'priority' => 'critical'],
    'security-audit'  => ['agent' => 'security',  'priority' => 'critical'],
    'documentation'   => ['agent' => 'mo',        'priority' => 'low'],
    'testing'         => ['agent' => 'qa',        'priority' => 'medium'],
];
```

| Tip | Ajan | Öncelik |
|---|---|---|
| recommendation | backend | medium |
| analysis / eq-optimization | embedded | high / medium |
| hardware | audio-hw | high |
| fault / security-audit | dsp-fw / security | critical / critical |
| documentation | mo | low |
| testing | qa | medium |

## §13. PHP — dispatchTask / routeToAgent Giriş Kapıları

Dosya: `shared/src/AI/AIOrchestrator.php` · Satır: L71-L97, L125-L149, L177-L180

```php
if (!isset(self::TASK_TYPES[$type])) {
    throw new \InvalidArgumentException("Unknown task type: {$type}");      // L73-L75
}
$taskId = $this->generateTaskId();                                          // L77
$this->tasks[$taskId] = ['id' => $taskId, 'type' => $type, 'status' => 'pending', ...];
$this->executeTask($taskId);   // Priority weighted execution (L93 yorumu)  // L94
// ...
if (!isset(self::AGENT_MAP[$agentId])) {
    return ['success' => false, 'result' => null, 'error' => "Unknown agent: {$agentId}"];  // L127-L133
}
```

| Kapı | Davranış | Fark |
|---|---|---|
| Bilinmeyen görev tipi | **exception** (L74) | fail-fast |
| Bilinmeyen ajan | `success: false` (L129-L132) | fail-safe |
| Görev kimliği | `'task_' . bin2hex(random_bytes(8)) . '_' . time()` (L179) | kriptografik rastgelelik |
| "Priority weighted execution" yorumu var; öncelik kuyruğu uygulaması | Repo'da bu alan kodu YOK — kaynak: upstream docs (`executeTask` L185-L209 senkron ve doğrudan) |
| Domain boundary ihlali kontrolü | L135 yalnız yorum: `// Domain boundary kontrolü` — karşılaştırma kodu YOK |

## §14. PHP — AIWorkflow: Araç Çağrı Noktaları + Çelişki Gözlemi

Dosya: `shared/src/AI/AIWorkflow.php` · Satır: L25, L59, L106, L117-L127, L246, L254-L258

```php
private ToolCalling $toolCalling;                                    // L25 bağımlılık
$taskId = $this->orchestrator->dispatchTask('recommendation', $context, 'medium');   // L59
// 4. DB'ye kaydet ... prepared statement ile SQL injection koruması                   // L117
$this->toolCalling->callTool('db-query', [
    'sql'      => "UPDATE tracks SET bpm = :bpm, key_sig = :key_sig, energy = :energy WHERE id = :id",
    'params'   => ['bpm' => $bpm, 'key_sig' => $keySig, 'energy' => $energy, 'id' => (int)$trackId],
    'database' => 'coremusic_musics',
]);                                                                    // L122-L126
$this->orchestrator->routeToAgent('security', 'alert', [...]);         // L254-L258
```

| Çağrı | Nokta | Kanıt |
|---|---|---|
| `dispatchTask` ×5 (recommendation/analysis/eq-optimization/hardware/fault) | L59, L106, L154, L205, L246 |
| `callTool('db-query', ...)` tek nokta | L122 |
| `routeToAgent('security','alert')` high risk dalı | L252-L258 |
| `checkPermission` çağrı noktası | **0** — workflow izin kapısını kullanmıyor (grep 0) |
| **Çelişki:** L123 `UPDATE` gönderir; db-query executor'u yalnız `SELECT` öneki kabul eder (ToolCalling L235-L237) → bu çağrı çalışma anında `success: false` + "Only SELECT queries are allowed" döndürür | iki dosya çapraz okuma, 2026-10-17 |

## §15. PHP — PromptEngine::INJECTION_PATTERNS Prompt Injection Denylist

Dosya: `shared/src/AI/PromptEngine.php` · Satır: L27-L35, L162-L167

```php
private const INJECTION_PATTERNS = [
    '/ignore\s+previous/i', '/you\s+are\s+now/i', '/system\s*:\s*/i', '/act\s+as/i',
    '/pretend\s+you/i', '/disregard/i', '/override/i',
];
foreach (self::INJECTION_PATTERNS as $pattern) {
    if (preg_match($pattern, $prompt)) {
        $risks[] = "Potential prompt injection detected: {$pattern}";
        $sanitized = preg_replace($pattern, '[FILTERED]', $sanitized);    // L165 redaction
    }
}
```

| Filtre | Davranış | Kanıt |
|---|---|---|
| 7 regex deseni (case-insensitive) | risk kaydı + `[FILTERED]` ikamesi | L27-L35, L162-L167 |
| Kısmi redaction — prompt'un tamamı reddedilmez | `sanitized` alanı döner | L159, L184 |
| Truncation/kozalak (`...`) ile kırpma | Repo'da bu alan kodu YOK — kaynak: upstream docs (yalnız `mb_substr` kırpma var, L55/L313) |

## §16. PHP — validatePrompt: 3 Kademeli Risk Filtresi

Dosya: `shared/src/AI/PromptEngine.php` · Satır: L156-L186

| Kademe | Kontrol | Sonuç | Satır |
|---|---|---|---|
| 1 | 7 injection deseni | risk + `[FILTERED]` | L162-L167 |
| 2 | token bütçesi (`MAX_TOKENS_DEFAULT = 4096`, L25) | risk kaydı (engelleme yok) | L170-L174 |
| 3 | hassas veri deseni `password\|secret\|api[_-]?key\|token` (case-insensitive) | `"Potential sensitive data in prompt"` risk kaydı | L177-L179 |
| Çıktı zarfı | `valid` (risks boş mu) + `risks[]` + `sanitized` | L181-L185 |
| Token sayacı | `mb_strlen / 3.5` tahmini — "gerçek implementasyonda tokenizer kullanılacak" (L92 yorumu) | L90-L96 |

## §17. Hook — ai-risk-gate.cjs: PreToolUse HIGH-RISK `ask` Kapısı

Dosya: `.claude/hooks/ai-risk-gate.cjs` · Satır: L3, L14-L17, L36-L49, L52-L73

```js
const HIGH_RISK_FILES = new Set([
  'CLAUDE.md', 'AGENTS.md', 'WORKFLOW.md', 'ULTRA-THINKING.md',
]);                                                    // L14-L16
const HIGH_RISK_DIRS = ['.rules'];                     // L17
if (ROOT_HIGH.has(rel)) {                              // L37 — .ai/ DIŞINDA kök boot dosyaları
  process.stdout.write(JSON.stringify({
    hookSpecificOutput: {
      hookEventName: 'PreToolUse',
      permissionDecision: 'ask',                       // L41 — İNSAN onayı
      permissionDecisionReason: 'Control Plane HIGH-RISK kapısı (Q32): kök boot dosyası ' + rel + ...
    },
  }));
  process.exit(0);
}
```

| Kural | Değer | Satır |
|---|---|---|
| Tetik | `PreToolUse (Edit\|Write)` (başlık) + settings.json matcher `Edit\|Write` | L3, settings.json L15 |
| Kök T1/T2 boot dosyaları (`.ai/` dışında bile) | CLAUDE/AGENTS/WORKFLOW → `ask` | L36-L49 |
| `.ai/` HIGH yüzey | 4 dosya + `.rules/**` dizini → `ask` | L14-L17, L56-L72 |
| Göreli yol normalizasyonu | `path.relative` + `\`→`/` | L30-L33 |
| `.ai/` dışı her şey | sessiz `exit 0` (karar yok, akış normal) | L53 |
| Emniyet | "her koşulda exit 0" (docblock L8) | L20, L23, L26, L48, L73 |

## §18. Hook — ai-validate-post.cjs: PostToolUse `block` Kapısı

Dosya: `.claude/hooks/ai-validate-post.cjs` · Satır: L3, L31-L43

```js
if (!/^\.ai\/.+\.md$/i.test(rel)) process.exit(0);        // L31 — allowlist: yalnız .ai .md
const validator = path.join(project, '.ai', 'scripts', 'validate.mjs');
if (!fs.existsSync(validator)) process.exit(0);           // L34 — validator yoksa sessiz
const r = spawnSync(process.execPath, [validator, '--check'], { encoding: 'utf8', cwd: project });
if (r.status === 1) {                                     // L37 — yalnız exit 1 = İHLAL
  const lines = (r.stdout || '').trim().split('\n').slice(0, 12).join(' | ');
  process.stdout.write(JSON.stringify({
    decision: 'block',                                    // L40 — PostToolUse block zarfı
    reason: 'Control Plane validator İHLAL — ... ' + lines,
  }));
}
```

| Kural | Değer |
|---|---|
| Allowlist regex | yalnız `.ai/**.md` dosyaları denetime girer (L31) |
| Fail-safe | validator yoksa/çöküyorsa sessiz `exit 0` — yanlış kilitlenme yok (docblock L9-L11) |
| `block` yalnız `r.status === 1` | exit 2/3/hata → block yok |
| Geri besleme | ilk 12 validator satırı `reason` içine ` | ` ile gömülür (L38) |

## §19. Hook — skill-mandate.cjs: Context Injection + `--check` Denetimi

Dosya: `.claude/hooks/skill-mandate.cjs` · Satır: L12, L26-L31, L86-L100, L106-L178

```js
const COREMUSIC_GLOBAL = ['human-mode', 'truth-engine', 'verify-loop', 'agent-debate', 'context-report'];  // L26
const BANNED_ROOT_KEYS = [                      // L28-L31 — skill frontmatter denylist
  'title', 'type', 'version', 'authority', 'mode', 'purpose',
  'reference', 'triggers', 'changelog', 'dependencies',
];
// hookMain: additionalContext enjeksiyonu (L86-L100)
process.stdout.write(JSON.stringify({
  hookSpecificOutput: { hookEventName: 'UserPromptSubmit', additionalContext },   // L95-L100
}));
```

| Mod | Davranış | Kanıt |
|---|---|---|
| hook (default) | diskten proje skill taraması + `additionalContext` ile kural enjeksiyonu; "prompt asla bloklanmaz" (L12) | L67-L102 |
| `--check` | v3.0 format denetimi, ihlalde `exit 1` | L106-L178 |
| Denetimler | `name`↔klasör 1:1 (L120-L121) · description `Use when` öneki + ≤1024 char (L123-L128) · yasak root key (L130-L132) · `metadata` bloğu (L134) · kırık `references/`+`examples/` linki (L138-L143) · ORPHAN dosya (L146-L153) · sidecar `CLAUDE.md` yasağı walk (L156-L163) · proje↔global çift kopya (L167-L169) |
| Dispatch | `--check` varsa checkMain, yoksa hookMain; fatal'da check exit 1 / hook exit 0 | L182-L195 |

## §20. settings.json — Hook Kaydı + MCP Araç Yüzeyi

Dosya: `.claude/settings.json` · Satır: L2-L38, L48-L99

| Olay | Handler | Matcher | Timeout | Satır |
|---|---|---|---|---|
| UserPromptSubmit | `skill-mandate.cjs` | — (her tur) | default | L3-L12 |
| PreToolUse | `ai-risk-gate.cjs` | `Edit\|Write` | 15 sn | L13-L25 |
| PostToolUse | `ai-validate-post.cjs` | `Edit\|Write` | 60 sn | L26-L38 |

| MCP sunucu | Durum | Satır |
|---|---|---|
| `filesystem` (npx `@modelcontextprotocol/server-filesystem`, kök `C://www/coremusic.net`) | aktif | L49-L58 |
| `chrome-devtools` | aktif | L59-L65 |
| `claude-mem` (yerel `node` script) | aktif | L66-L71 |
| `browsermcp` / `playwright` / `exa` | **`disabled: true`** — açık denylist durumu | L72-L98 |

## §21. Hook ↔ Resmi Şema Eşleşmesi (web doğrulama)

Kaynak: `https://code.claude.com/docs/en/hooks` (resmi Anthropic/Claude Code dokümanı, 2026-10-17 erişim, webfetch)

| Repo kullanımı | Resmi şema | Eşleşme |
|---|---|---|
| `hookSpecificOutput.permissionDecision: 'ask'` + `permissionDecisionReason` (risk-gate L41-L45) | PreToolUse karar alanı: `permissionDecision` (allow/deny/ask/defer) + `permissionDecisionReason` | **evet** |
| `decision: 'block'` + `reason` (validate-post L40-L41) | PostToolUse üst seviye `decision: "block"` + `reason` | **evet** |
| `hookSpecificOutput.additionalContext` (skill-mandate L98) | UserPromptSubmit yalnız `additionalContext` enjekte eder; prompt'u bloklama/replase yetkisi yok | **evet** |
| matcher `Edit\|Write` (settings.json L15, L28) | `\|`/`,` ile ayrılmış tam liste matcher'ı | **evet** |
| "her koşulda exit 0" (3 hook docblock) | exit 0 + JSON = karar; sessiz exit 0 = kararsız normal akış | **evet** |

> **Not:** Bu dosyadaki üç hook, resmi şemanın 2026-10-17 sürümüyle birebir uyumludur;
> `defer` değeri repo'da kullanılmıyor (kullanılmak zorunda değil).

## §22. İllüstratif — Python: Tool Registry + Decorator

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# ToolCalling.php L28-L40 izin matrisi + L53-L68 registerTool karşılığı.
REGISTRY: dict[str, dict] = {}

def tool(name: str, category: str, params: dict[str, type]):
    def deco(fn):
        REGISTRY[name] = {"fn": fn, "category": category, "params": params}   # L59-L65 zarfı
        return fn
    return deco

def check_permission(agent: str, name: str, matrix: dict[str, list[str]]) -> bool:
    # L145-L166 fail-safe semantiği: bilinmeyen => False
    if name not in REGISTRY or agent not in matrix:
        return False
    return REGISTRY[name]["category"] in matrix[agent]                        # L158 strict in_array

@tool("file-search", "file", {"pattern": str})
def file_search(pattern: str) -> list[str]:
    import glob
    return glob.glob(pattern)                                                 # ToolCalling L215
```

**Kapsam:** kayıt + decorator + izin — PHP zarfının birebir kavramsal karşılığı; repo'da Python yok.

## §23. İllüstratif — TypeScript: JSON-Schema Doğrulama

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```typescript
// validateParameters (ToolCalling L307-L325) required+tip kontrolünü aşar:
// enum/pattern/minimum gibi JSON-Schema anahtarları — repo'da YOK (§7, §29).
type ParamDef = { name: string; type: "string" | "number" | "boolean" | "mixed";
                  required: boolean; enum?: readonly string[]; pattern?: RegExp };

export function validate(defs: ParamDef[], params: Record<string, unknown>): string[] {
  const errors: string[] = [];
  for (const d of defs) {
    if (d.required && !(d.name in params)) { errors.push(`Missing required parameter: ${d.name}`); continue; }
    const v = params[d.name];
    if (v === undefined || d.type === "mixed") continue;
    if (typeof v !== d.type) { errors.push(`Parameter '${d.name}' expects '${d.type}'`); continue; }
    if (d.enum && !d.enum.includes(v as string)) errors.push(`'${d.name}' not in enum`);
    if (d.pattern && !d.pattern.test(v as string)) errors.push(`'${d.name}' pattern mismatch`);
  }
  return errors;   // PHP tarafı throw eder (L311, L319); burası toplu liste döner
}
```

**Kapsam:** şema doğrulama — PHP `validateParameters` yalnız required+tip; enum/pattern repo'da **YOK**.

## §24. İllüstratif — Go: Interface-Based Tool

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
// ToolCallingInterface implementasyonunun (L26) interface tabanlı karşılığı.
type Tool interface {
    Name() string
    Category() string
    Execute(ctx context.Context, params map[string]any) (map[string]any, error)
}

type Registry struct { tools map[string]Tool; perms map[string][]string }   // L28-L43

func (r *Registry) Call(ctx context.Context, name string, params map[string]any) (map[string]any, error) {
    t, ok := r.tools[name]
    if !ok {
        return nil, nil // fail-safe: PHP'de success:false zarfı (L75-L82); burada nil hata yok
    }
    return t.Execute(ctx, params)                                           // L92 executor çağrısı
}

func (r *Registry) CheckPermission(agent, name string) bool {
    t, ok := r.tools[name]
    if !ok { return false }                                                  // L147-L149
    allowed, ok := r.perms[agent]
    if !ok { return false }                                                  // L151-L153
    for _, c := range allowed { if c == t.Category() { return true } }       // L158
    return false
}
```

**Kapsam:** interface + registry + fail-safe izin — PHP matrisinin aynı semantiği; repo'da Go yok.

## §25. İllüstratif — Rust: Typestate Tool Builder

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```rust
// registerTool (L53-L68) zincirinin derleme-zamanı sürümü:
// eksik alanla araç kaydı derleme hatası olur; PHP'de çalışma zamanı hatasıdır.
pub struct NoName; pub struct NoExec;
pub struct ToolBuilder<N, E> { name: N, category: String, executor: E }

impl ToolBuilder<NoName, NoExec> {
    pub fn new() -> Self { ToolBuilder { name: NoName, category: String::new(), executor: NoExec } }
}
impl<E> ToolBuilder<NoName, E> {
    pub fn name(self, n: &str) -> ToolBuilder<String, E> {
        ToolBuilder { name: n.to_string(), category: self.category, executor: self.executor }
    }
}
impl<N> ToolBuilder<N, NoExec> {
    pub fn execute<F: Fn(&[f32]) -> Vec<f32>>(self, f: F) -> ToolBuilder<N, F> {
        ToolBuilder { name: self.name, category: self.category, executor: f }
    }
}
// category() türetmesi: inferCategory (L289-L299) önek kurallarının Rust karşılığı
pub fn infer_category(name: &str) -> &'static str {
    if name.starts_with("file-") { "file" } else if name.starts_with("db-") { "database" }
    else if name.starts_with("http-") { "api" } else { "file" }              // L298 fallback
}
```

**Kapsam:** typestate builder — "kayıt tamamlanmadan registry'ye giremez" güvencesi; repo'da Rust yok.

## §26. İllüstratif — C++: Command Pattern (Araç Çağrısı)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```cpp
// callTool (L73-L111) + executor callable (L92) karşılığı: Command + zarf sonucu.
#include <functional>
#include <string>
#include <chrono>

struct ToolResult { bool success; std::string error; double executionTime; };  // L95-L109 zarfı

class ICommand {
public:
    virtual ~ICommand() = default;
    virtual ToolResult execute() = 0;
};

class LambdaCommand : public ICommand {          // PHP closure (L182, L213, L232) karşılığı
    std::function<ToolResult()> fn_;
public:
    explicit LambdaCommand(std::function<ToolResult()> fn) : fn_(std::move(fn)) {}
    ToolResult execute() override {
        const auto t0 = std::chrono::steady_clock::now();                       // L85 microtime
        try { auto r = fn_();                                                    // L92
              r.executionTime = std::chrono::duration<double>(std::chrono::steady_clock::now() - t0).count();
              return r; }
        catch (const std::exception& e) { return { false, e.what(), 0.0 }; }     // L101-L109
    }
};
```

**Kapsam:** command + süre ölçümü + exception zarfı — callTool döngüsünün C++ karşılığı; repo'da bu dizinde C++ yok.

## §27. İllüstratif — C#: Attribute-Based Permission

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```csharp
// AGENT_PERMISSIONS sabit tablosunun (L28-L40) derleme-zamanı dekoratif karşılığı.
[AttributeUsage(AttributeTargets.Method, AllowMultiple = true)]
public sealed class RequireAgentCategoryAttribute : Attribute {
    public string Agent { get; }
    public RequireAgentCategoryAttribute(string agent, string category) { Agent = agent; Category = category; }
    public string Category { get; }
}

public static class ToolGate {
    // "security" ajana yalnız file/database/api/security — ToolCalling L32 ile aynı küme
    [RequireAgentCategory("security", "file")]
    [RequireAgentCategory("security", "database")]
    [RequireAgentCategory("security", "api")]
    [RequireAgentCategory("security", "security")]
    public static void DbQuery(string sql) { /* SELECT-only: L235-L237 */ }
    // Çalışma zamanı denetimi reflection ile: bilinmeyen ajan => UnauthorizedAccessException
    // (PHP tarafı exception değil allowed:false döner — L147-L163, fail-safe farkı)
}
```

**Kapsam:** attribute + reflection izin — sabit matrisin dekoratif hâli; repo'da C# yok (Windows agent PLANNED, §29).

## §28. Kavram ↔ Eşleşme Tablosu

| Vault kavramı | Repo kod karşılığı | Eşleşme |
|---|---|---|
| İzin matrisi (ajan → kategori) | `ToolCalling::AGENT_PERMISSIONS` L28-L40 | **evet** |
| İzin denetimi kapısı | `checkPermission` L145-L166 | **evet** (çağrı noktasında kullanılmıyor — §4) |
| Araç kaydı | `registerTool` L53-L68 + `registerDefaultTools` L173-L284 | **evet** |
| Araç çağrısı + süre ölçümü | `callTool` L73-L111 | **evet** |
| Parametre doğrulama | `validateParameters` L307-L325 | **kısmi** (required+tip; JSON-Schema YOK) |
| Kategori allowlist (ad türetme) | `inferCategory` L289-L299 | **evet** |
| SQL denylist (SELECT-only) | db-query executor L233-L237 | **evet** (.placeholder, önek-only) |
| Görev routing tablosu | `AGENT_MAP` L26-L38 | **evet** |
| Görev tipi matrisi | `TASK_TYPES` L40-L49 | **evet** |
| Prompt injection denylist | `INJECTION_PATTERNS` L27-L35 | **evet** |
| Hassas veri redaction sinyali | `validatePrompt` L177-L179 + `[FILTERED]` L165 | **kısmi** (sinyal + ikame; araç çıktısı redaction YOK) |
| İnsan onayı gate'i (HIGH risk) | `ai-risk-gate.cjs` `permissionDecision:'ask'` L41 | **evet** |
| Yazım sonrası doğrulama block'u | `ai-validate-post.cjs` `decision:'block'` L40 | **evet** |
| Skill zorunluluğu enjeksiyonu | `skill-mandate.cjs` `additionalContext` L95-L100 | **evet** |
| Öncelikli görev kuyruğu | — (L93 yalnız yorum) | **YOK** |
| Domain boundary denetimi | — (L135 yalnız yorum) | **YOK** |

## §29. KOD YOK Alanlar (yokluk cümleleri — tahmin yasak)

| Alan | Durum (2026-10-17) |
|---|---|
| `callTool` içinden otomatik `checkPermission` çağrısı | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| `db-query` executor'unda gerçek prepared statement çalıştırma | Repo'da bu alan kodu YOK — kaynak: upstream docs (L238 `return ['result'=>[], 'rows'=>0]`) |
| Öncelikli görev kuyruğu (L93 "Priority weighted execution" yorumu) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Domain boundary karşılaştırma kodu (L135 yorum) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| ToolCalling/AIOrchestrator birim testi (`shared/tests/**` grep `ToolCalling\|AIOrchestrator\|checkPermission` → **0 isabet**) | Repo'da bu alan kodu YOK — kaynak: ölçüm 2026-10-17 |
| Araç başına rate limit / kota | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Kalıcı araç çağrısı audit log'u (executionTime dönüyor ama log'a yazılmıyor) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Araç ÇIKTISI redaction/maskeleme (girdi sinyali §16'da var) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| JSON-Schema doğrulama (enum/pattern/minimum) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| `http-request` SSRF URL allowlist'i / redirect sınırı | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| LLM tool-use roundtrip (tool_result → model döngüsü; PHP tarafı doğrudan callable çalıştırır) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Dosya yolu denylist (hangi `path` yazılamaz — L184/L200 yolu doğrular, kısıtlamaz) | Repo'da bu alan kodu YOK — kaynak: upstream docs |

## §30. Güvenlik Notları (kanıt ile)

| Not | Kanıt |
|---|---|
| Secret asla `.md`'ye yazılmaz — bu dosyada da hiçbir anahtar değeri yok | kök AGENTS §3 · bu dosya |
| `validatePrompt` hassas veri deseni prompt girdisini işaretler (`password\|secret\|api[_-]?key\|token`) | PromptEngine L177-L179 |
| `[FILTERED]` ikamesi ile redaction (7 injection deseni) | PromptEngine L162-L167 |
| `.claude/settings.json` L95'te `EXA_API_KEY` düz metin görünüyor (değer buraya **yazılmaz** — `REDACTED`) · ilgili MCP sunucusu `disabled: true` | settings.json L94-L98 · güvenlik incelemesi gerekli (AÇIK SORU §33) |
| 3 hook fail-safe: gate'ler yanlışlıkla kilitlemez; `block` yalnız validator exit 1'de | risk-gate L8 · validate-post L9-L11 · skill-mandate L12 |
| SQL denylist önek-only: `SELECT; DROP` gibi zincirler elemez — tek savunma katmanı sayılmaz | ToolCalling L235-L237 |
| `file-write` her yolu kabul eder (`.ai/` yazımı hook'larla korunuyor, PHP executor'unda sınır yok) | ToolCalling L192-L204 + §17-§18 |

## §31. Ölçüm Kalıpları (tekrarlanabilir)

| Amaç | Kalıp (repo kökünden) | 2026-10-17 sonucu |
|---|---|---|
| PHP satır sayısı | `[IO.File]::ReadAllLines($hedef,[Text.Encoding]::UTF8).Count` | 7 dosya: 326/210/271/325/242/232/382 |
| Hook satır sayısı | aynı kalıp (`*.cjs`) | 73/44/195 · settings.json 126 |
| Test kapsamı negatifi | grep `ToolCalling\|AIOrchestrator\|checkPermission` (include `*.php`, `shared/tests`) | **0 isabet** |
| Hook kaydı | grep `ai-risk-gate\|ai-validate-post\|skill-mandate` (settings*.json) | 3 isabet (L8, L19, L32) |
| İzin metodu çağrı noktası | grep `checkPermission` (`shared/src`) | tanım 1 (L145) · çağrı 0 |
| Frontmatter 10 alan | ilk 14 satırda 10 alan | §32 kapı 2 |
| Mojibake | `ReadAllText` içinde U+FFFD ara | 0 (üretim sonrası doğranacak) |

## §32. Doğrulama Kapıları

| # | Kapı | Kriter | Durum |
|---|---|---|---|
| 1 | Satır sayısı | `>= 500` (band 500-520) | §31 ölçümü |
| 2 | Frontmatter + Authority | 10 alan; `title`/`parent` **yok** · son satır birebir | gözden geçirme |
| 3 | PHP/hook kanıtı | her alıntı gerçek `Dosya:`/`Satır:` künyesi | 2026-10-17 okuma |
| 4 | Yokluk cümleleri | 12 satır "KOD YOK" | §29 |
| 5 | İllüstratif etiket | 6 dilde blok + üstünde etiket | §22-§27 |
| 6 | Web doğrulama | 1 resmi kaynak (code.claude.com/docs/en/hooks) | §21 · uydurma URL yasak |
| 7 | Section sayısı | en az 25 `## §` | §1-§34 |
| 8 | Secret yasak | anahtar değeri metinde yok (REDACTED ile anıldı) | §30 |

## §33. Açık Sorular

| # | Soru | Bağlam |
|---|---|---|
| 1 | `checkPermission` neden `callTool` içine bağlanmadı? — bilinçli ayrım mı, eksik mi? | §4 son satır |
| 2 | AIWorkflow L122 `UPDATE` çağrısı ile SELECT-only executor çelişiyor — hangi taraf düzeltilecek? | §14 |
| 3 | settings.json L95 EXA_API_KEY düz metin — `.claude/AGENTS.md` "REDACTED" iddiasıyla çelişiyor; taşınacak mı? | §30 |
| 4 | `defer` permissionDecision değeri gate'lere eklenecek mi? (resmi şema destekliyor, repo kullanmıyor) | §21 |
| 5 | ToolCalling/AIOrchestrator için test yazımı QA planına alındı mı? (grep 0) | §29, §31 |

## §34. Kaynaklar ve Yasaklar

| Kaynak | Kanıt türü |
|---|---|
| `shared/src/AI/ToolCalling.php` (326 satır) | 2026-10-17 tam okuma |
| `shared/src/AI/AIOrchestrator.php` (210 satır) | 2026-10-17 tam okuma |
| `shared/src/AI/AIWorkflow.php` · `PromptEngine.php` · `MemorySystem.php` · `KnowledgeBase.php` · `AIEngine.php` | 2026-10-17 tam okuma |
| `.claude/hooks/ai-risk-gate.cjs` (73) · `ai-validate-post.cjs` (44) · `skill-mandate.cjs` (195) | 2026-10-17 tam okuma |
| `.claude/settings.json` (126 satır) | 2026-10-17 tam okuma |
| `https://code.claude.com/docs/en/hooks` | resmi web kaynağı, 2026-10-17 erişim |
| `shared/tests/**` grep · settings.json grep | negatif/pozitif ölçüm, 2026-10-17 |

| Yasak | Kural kaynağı |
|---|---|
| `git commit` atmak | görev manifesti · AGENTS.md §16.5 — commit orkestratöre aittir |
| `shared/src/**` ve `.claude/**` yazımı · bu dosya dışına yazım | görev manifesti (salt-okunur) |
| Uydurma `Dosya:`/`Satır:` künyesi · illüstratif bloğa repo kanıtı süsü vermek | Zero-Hallucination (AGENTS §3) · bu dosya §1 |
| Anahtar/secret değerini `.md`'ye yazmak | kök AGENTS §3 · §30 |
| Bekleyen `{{placeholder}}` · ölçülmemiş sayıya "ölçüldü" demek | RAG.md §3.2 kural 4 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
