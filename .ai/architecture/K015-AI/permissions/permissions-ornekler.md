---
node_id: permissions-ornekler
node_type: module
status: active
parent_id: permissions
children: []
depends_on: [permissions-anayasa, permissions-surucu]
evidence:
- ".claude/settings.json (izin yüzeyi gerçek alıntıları, 2026-10-17 okuma — secret REDACTED)"
- ".gitignore (secret koruma satırları, künyeli)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı, kod kanıtı zorunlu"
open_questions: []
group: kod
---

# permissions-ornekler - Ajan Yetki Kod Örnekleri (kanıt odaklı)

> **Kapsam:** `.claude/settings.json` (126 satır) · `.claude/settings.local.json` (13 satır) ·
> `.gitignore` (83 satır) · 3 hook betiği 2026-10-17'de **salt-okunur** okundu; her alıntı
> `Dosya:`/`Satır:` künyesi taşır. Secret değerleri **asla** yazılmaz (`[REDACTED]`).
> İllüstratif bloklar repo kanıtı değildir; her birinin üstünde zorunlu etiket vardır.

## §1. Kanıt Künye Formatı

| Alan | Kural |
|---|---|
| Dosya · Satır | göreli yol + okunan aralık (L13-L25) — okunmamış satır künye olamaz |
| Alıntı | dosyanın kendi satırı; kısaltma serbest, uydurma yasak |
| Secret | yalnız `[REDACTED]` — anahtar adı yazılır, **değeri yazılmaz** |
| Yokluk | tam cümle: "Repo'da bu alan kodu YOK — kaynak: upstream docs" |
| Ölçüm | `ReadAllLines` / `Select-String` sayaçları — araç çıktısı, iddia değil |
| İllüstratif | `⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı` |
| Tarih · Eşleşme | her kanıt 2026-10-17 · `evet`/`kısmi`/`YOK` |
| Yasak | uydurma künye → Zero-Hallucination (AGENTS §3) · çelişkide disk kazanır |

## §2. Ayar Dosyası Envanteri — Ölçüm (2026-10-17)

Ölçüm: `[IO.File]::ReadAllLines($yol,[Text.Encoding]::UTF8).Count`

`settings.json` 126 satır — **`permissions` anahtarı YOK** (grep `"permissions"` → 0) · `settings.local.json` 13 satır — `permissions.allow` 7 kural (§10) · `.claude/hooks/*.cjs` 73/195/44 satır — risk kapısı · skill beyazlistesi · validator (§13-§15) · `.gitignore` 83 satır + `.claudeignore` 8 satır — secret koruma · etkisiz ignore (§11, §31).

## §3. settings.json — Hook: UserPromptSubmit

Dosya: `.claude/settings.json` · Satır: L2-L12 (çekirdek L7-L8)
```json
"UserPromptSubmit": [
  { "hooks": [
      { "type": "command",
        "command": "node \"${CLAUDE_PROJECT_DIR}/.claude/hooks/skill-mandate.cjs\"" }
    ] }
],
```

Olay her prompt'ta; tip `command`; yol proje köküne göre (L3-L8) · karşılığı Skill Usage Mandate — davranış §15.

## §4. settings.json — Hook: PreToolUse (Edit|Write risk kapısı)

Dosya: `.claude/settings.json` · Satır: L13-L25
```json
"PreToolUse": [
  { "matcher": "Edit|Write",
    "hooks": [ { "type": "command",
        "command": "node \"${CLAUDE_PROJECT_DIR}/.claude/hooks/ai-risk-gate.cjs\"",
        "timeout": 15,
        "statusMessage": "Control Plane HIGH-RISK kapısı (T1/T2)" } ] }
],
```

Matcher yalnız yazan 2 araç; süre 15 sn (L15, L20) · karar dili `permissionDecision: "ask"` — `ai-risk-gate.cjs` L41, L64.

## §5. settings.json — Hook: PostToolUse (validator)

Dosya: `.claude/settings.json` · Satır: L26-L38
```json
"PostToolUse": [
  { "matcher": "Edit|Write",
    "hooks": [ { "type": "command",
        "command": "node \"${CLAUDE_PROJECT_DIR}/.claude/hooks/ai-validate-post.cjs\"",
        "timeout": 60,
        "statusMessage": ".ai validator (fm/link/ssot/dep/tier/orphan)" } ] }
]
```

Yazım sonrası 6 kontrol: fm/link/ssot/dep/tier/orphan (L34) · sıra: izin kapısı önce → validator sonra, aynı matcher (L15, L28).

## §6. settings.json — instructions (boot okuma listesi)

Dosya: `.claude/settings.json` · Satır: L40-L47 — **6 öğe**:

`CLAUDE.md` · `.ai/CLAUDE.md` · `.ai/AGENTS.md` · `.ai/WORKFLOW.md` · `.ai/CONTEXT.md` · `.ai/architecture/context.md`

İzin belgesi değil **bağlam yükleme listesi**; yetki hook + yerel allow katmanından gelir (§4, §10).

## §7. settings.json — mcpServers (plugin/env izin yüzeyi)

Dosya: `.claude/settings.json` · Satır: L48-L99 (alıntı: L49-L58, L88-L98)
```json
"filesystem": { "command": "npx", "args": ["-y", "@modelcontextprotocol/server-filesystem",
                                            "C://", "C://www", "C://www//coremusic.net"] },
"exa": { "command": "npx", "args": ["-y", "exa-mcp-server"],
         "env": { "EXA_API_KEY": "[REDACTED]" },
         "disabled": true }
```

`filesystem` L49-L58 **aktif — kök kapsam `C://`, en geniş yüzey** · `chrome-devtools` · `claude-mem` L59-L71 aktif (tarayıcı · yerel plugin) · `browsermcp` · `playwright` · `exa` L72-L98 hepsi `"disabled": true` — `exa` env'de anahtar (§12).

## §8. settings.json — watcher · references · enabledPlugins

Dosya: `.claude/settings.json` · Satır: L100-L125

`watcher.ignore` L100-L108 — `vendor/**`, `node_modules/**`, `.git/**`, `*.log`, `*.cache` (gürültü filtresi, yetki değil) · `references` L109-L122 — `.ai` (vault) · `.claude/rules` · `.workflows` (okuma referansı) · `enabledPlugins` L123-L125 — `frontend-design@claude-plugins-official: true` (tek aktif eklenti).

## §9. settings.json — permissions anahtarı YOK (negatif kanıt)

Dosya: `.claude/settings.json` · ölçüm `Select-String -SimpleMatch`

Ölçüm `Select-String -SimpleMatch`: ortak dosyada `allow`/`deny`/`ask` listesi → **YOK** (`"permissions"` 0 isabet) · `"deny"`·`"ask"` bloğu `.claude/*.json` içinde → **YOK** (0 isabet) · yerel izin listesi → **var** (`.claude/settings.local.json` L2-L12, §10).

## §10. settings.local.json — permissions.allow (7 kural)

Dosya: `.claude/settings.local.json` · Satır: L2-L12
```json
"permissions": { "allow": [
    "WebSearch",
    "Bash(php *)",
    "Bash(npm list *)",
    "Bash(cat)",
    "Bash(python -I /tmp/linkcheck/verify.py)",
    "Bash(python3 *)",
    "Bash(python -I /tmp/linkcheck/analyze.py)"
] }
```

Üç sınıf: **araç-tam** (`WebSearch`) · **önek+wildcard** (`Bash(php *)`, `Bash(python3 *)`, `Bash(npm list *)`) · **tam eşleşme** (`Bash(cat)` · `python -I /tmp/linkcheck/*.py` ×2 — izole mod, betik adıyla sınırlı).

> Resmi sıra **deny → ask → allow**; ilk eşleşen kazanır, özgüllük sırayı değiştirmez (§35/1).

## §11. .gitignore — secret koruma gerçek satırları

Dosya: `.gitignore` · Satır: L5-L9, L68-L71, L79-L80
```gitignore
# ─── Environment & Secrets ───            (L5)
.env                                       (L6)
.env.local  /  .env.*.local  /  config/.env (L7-L9)
# ---- Figma API token (local only) ----   (L68)
.ai/.env.figma   /   .ai/.env.*            (L69-L70)
!.env.example                              (L71)
# ---- JWT RS256 (private key ASLA commit edilmez) ---- (L79)
shared/config/jwt/private.pem              (L80)
```

L6-L9 — 4 `.env*` deseni (ortam dosyaları) · L69-L71 — Figma token dosyası, `!.env.example` negasyonu (şablon commit edilir) · L80 — JWT **özel** anahtarı (`public.pem` commit edilir, L79 yorumu).

## §12. Secret Maskeleme — gerçek tespit ve gösterim

Dosya: `.claude/settings.json` · Satır: L94-L96 (`exa` env bloğu)
```json
"env": { "EXA_API_KEY": "[REDACTED]" }
```

Tespit — L95: anahtar adı `EXA_API_KEY`, değer 36 karakterlik düz metin UUID biçimli. Ölçüm — `Select-String 'EXA_API_KEY'` → **1 isabet** (dosyada düz metin var). Maskeleme — bu belgede ad yazılır, **değer `[REDACTED]` — asla kopyalanmaz**. Üretim doğrulaması — bu dosyada UUID deseni aranır → **0** gerekli (§33 kapı 4). Kural — `.ai/**` içine secret yazımı anahtar iptali gerektirir (`.claude/AGENTS.md` §3.3 #1).

**Risk:** `exa` `"disabled": true` olsa da anahtar diskte düz metin ve dosya **commit edilir** (shared project scope — §35/2).

## §13. Hook Betikleri Envanteri (salt-okunur okuma)

Dosya: `.claude/hooks/` · glob → **3 isabet** (2026-10-17): `skill-mandate.cjs` 195 satır `UserPromptSubmit` → skill beyazlistesi (§15) · `ai-risk-gate.cjs` 73 satır `PreToolUse` → T1/T2 `ask` kararı (§14) · `ai-validate-post.cjs` 44 satır `PostToolUse` → 6 alan doğrulama (§5).

**Yokluk:** başka hook `.cjs` **YOK** (glob 3); `.claude/hooks/` dışında hook tanımları YOK (`settings.json` L8/L19/L32 dışındaki `command` yok).

## §14. ai-risk-gate.cjs — Beyazliste Tarama + Karar

Dosya: `.claude/hooks/ai-risk-gate.cjs` · Satır: L14-L17, L36-L37, L56-L58, L60-L72
```js
const HIGH_RISK_FILES = new Set(['CLAUDE.md', 'AGENTS.md', 'WORKFLOW.md', 'ULTRA-THINKING.md']);
const HIGH_RISK_DIRS  = ['.rules'];
const ROOT_HIGH       = new Set(['CLAUDE.md', 'AGENTS.md', 'WORKFLOW.md']);
const isHigh = HIGH_RISK_FILES.has(inner) ||
  HIGH_RISK_DIRS.some(d => inner === d || inner.startsWith(d + '/'));
```

| Kalıp | Satır | Davranış |
|---|---|---|
| Dosya `Set.has` + `startsWith(d + '/')` öneki | L14-L16, L57-L58 | T1/T2 → `ask` |
| Göreli yol (`path.relative`, `\`→`/`) + fail-safe `exit 0` | L30-L33, L20-L26, L73 | hiyer yoksa sorulmaz |

## §15. skill-mandate.cjs — Beyazliste + Disk Taraması

Dosya: `.claude/hooks/skill-mandate.cjs` · Satır: L20-L31, L56-L63, L12
```js
const SKILLS_DIR = path.join(PROJECT_DIR, '.claude', 'skills');
const COREMUSIC_GLOBAL = ['human-mode', 'truth-engine', 'verify-loop', 'agent-debate', 'context-report'];
const BANNED_ROOT_KEYS = ['title', 'type', 'version', 'authority', 'mode', 'purpose',
                          'reference', 'triggers', 'changelog', 'dependencies'];
// listProjectSkills(): readdirSync → klasör → SKILL.md var mı → sort()  (L56-L63)
```

| Kalıp | Satır | Anlam |
|---|---|---|
| Sabit beyazliste (5 global skill) + kara liste (10 anahtar) | L26, L28-L31 | uydurma ad/yasak anahtar reddi |
| Disk taraması (`SKILL.md` varlığı) + `exit 0` emniyeti | L56-L63, L12 | proje skill'leri okunur, prompt asla bloklanmaz |

## §16. Repo PHP — PermissionMiddleware (RBAC kapısı)

Dosya: `shared/src/Middleware/PermissionMiddleware.php` · Satır: L17-L45, L47-L63 · 64 satır
```php
$routeMeta = $request['_route_meta'] ?? [];  $auth = $request['_auth'] ?? [];
if (empty($auth['userId'])) { return $next($request); }           // auth yoksa ilgilenmez
$userRole = $auth['role'] ?? 'user';
if ($requiredRole !== null && $userRole !== $requiredRole)        // L30-L33
    return $this->forbidden('role_insufficient', "Required role: {$requiredRole}");
$userPermissions = $auth['permissions'] ?? [];
if (!is_array($userPermissions) || !in_array($requiredPermission, $userPermissions, true))   // L39
    return $this->forbidden('permission_denied', "Required permission: {$requiredPermission}");
return $next($request);
```

Frozen sıra `Auth → Permission → Validation → Controller` (ADR-010/043) — L13. Red gözlemlenir: `securityEvent('authz_denied')` + `halt: true` + 403 — L47-L62.

## §17. Repo PHP — AuthorizationMiddleware (API RBAC)

Dosya: `shared/src/Api/Middleware/AuthorizationMiddleware.php` · Satır: L60-L87
```php
$requiredRole = $route['requiredRole'] ?? null;
if ($requiredRole !== null && !in_array($requiredRole, $authUser['roles'] ?? [], true)) {   // L64
    return ApiResponse::error('FORBIDDEN', 'Insufficient permissions', 403);
}
$requiredPermission = $route['requiredPermission'] ?? null;
if ($requiredPermission !== null && !in_array($requiredPermission, $authUser['permissions'] ?? [], true)) {   // L77
    return ApiResponse::error('FORBIDDEN', 'Insufficient permissions', 403);
}
return $next($request);
```

404/405'te RBAC uygulanmaz (yapılandırma yok) — L55-L57 · aynı strict `in_array` deseni, iki katman tutarlı (§16 L39).

## §18. Repo PHP — ToolCalling::checkPermission (agent beyazlistesi)

Dosya: `shared/src/AI/ToolCalling.php` · Satır: L28-L40, L145-L166
```php
private const AGENT_PERMISSIONS = [
    'mo'       => ['file', 'database', 'api', 'audio', 'hardware', 'security'],
    'backend'  => ['file', 'database', 'api'],
    'ui'       => ['file', 'api'],
    // ... 11 agent anahtarı (L29-L39)
];
if (!isset($this->tools[$toolName]))         return ['allowed' => false, 'reason' => "Tool not found: {$toolName}"];
if (!isset(self::AGENT_PERMISSIONS[$agentId])) return ['allowed' => false, 'reason' => "Unknown agent: {$agentId}"];
if (!in_array($toolCategory, $allowedCategories, true))                                   // L158
    return ['allowed' => false, 'reason' => "Agent '{$agentId}' not allowed to use category '{$toolCategory}' tools"];
return ['allowed' => true, 'reason' => null];
```

11 agent × kategori beyazlistesi (sabit dizi) — L28-L40 · bilinmeyen araç/agent → `allowed: false` (**fail-safe**) — L147-L153 · araç öneki → kategori eşlemesi (`file-` → `file`) — L64.

## §19. Repo PHP — ReturnUrlPolicy host beyazlistesi

Dosya: `shared/src/Security/ReturnUrlPolicy.php` · Satır: L7-L15, L169-L183
```php
private const ALLOWED_HOSTS = ['coremusic.net', 'home.coremusic.net', 'auth.coremusic.net',
    'music.coremusic.net', 'admin.coremusic.net', 'localhost', '127.0.0.1'];      // L7-L15
if (in_array($urlScheme, ['javascript', 'data', 'vbscript'], true)) { return '/'; }        // L169
if (isset($parsed['user']) || isset($parsed['pass'])) { return '/'; }                      // L173
foreach (self::ALLOWED_HOSTS as $allowed) {
    if ($host === $allowed || str_ends_with($host, '.' . $allowed)) { return $url; }       // L177-L178
}
return '/';                                                                                // L183
```

Şema kara listesi + kimlik bilgisi reddi — L169-L175 (güvenilmez → `/`) · host beyazlistesi + alt alan adı + varsayılan red — L177-L183 (**fail-safe**).

## §20. Repo PHP — ApiKeyRepository IP allowlist + scope

Dosya: `shared/src/Repository/ApiKeyRepository.php` · Satır: L195-L205
```php
$ips = $this->decodeScopes($row['scopes'] ?? null)['allowed_ips'];
if ($ips !== [] && ($clientIp === null || !in_array($clientIp, $ips, true)))         // L199
    return ['ok' => false, 'reason' => 'ip_not_allowed', 'context' => null];
if ($requiredScope !== null && !in_array($requiredScope, $scopes, true))            // L203
    return ['ok' => false, 'reason' => 'scope_denied', 'context' => null];
```

| Gözlem | Kanıt |
|---|---|
| Boş liste = kısıt yok; doluysa `clientIp` birebir eşleşmeli | L199-L201 |
| Red nedenleri tek tek kodlanır (`deleted`, `expired`, `ip_not_allowed`, `scope_denied`) | L186-L204 |

## §21. Salt-okunur Kontrol Kalıpları (repo + resmi)

| Kalıp | Repo kanıtı | Resmi karşılık |
|---|---|---|
| Salt-okunur komut izni | `.local` allow: `Bash(cat)`, `Bash(npm list *)` (L7, L6) | `ls, cat, echo, pwd, head, tail, grep, find, wc, which, diff, stat, du, cd` + salt-okunur `git` (§35/1) |
| `plan` modu | repo'da `defaultMode` ayarı **YOK** (grep 0) | `plan` permission mode |
| Dış okuma kısıtı | — | `permissions.blockReadsOutsideWorkingDirectories` |
| `.claudeignore` | kök `.claudeignore` L1-L8, 2 giriş | resmi: **etkisi yok** → `Read` deny kuralına taşınmalı (§31 #1) |
| `Read`/`Edit` deny | `.claude/*.json` içinde **0 isabet** | `Read(./.env)` örneği (§35/1) |

## §22. KOD YOK Alanlar (yokluk cümleleri — tahmin yasak)

| Alan | Durum (2026-10-17) |
|---|---|
| `.claude/settings.json` içinde `permissions` (allow/deny/ask) | Repo'da bu alan kodu YOK — grep 0 isabet |
| Proje genelinde `deny`/`ask` izin kuralı | Repo'da bu alan kodu YOK — `.claude/*.json` grep 0 |
| `Read`/`Edit` path deny kuralı (`.env` koruması) | Repo'da bu alan kodu YOK — upstream örneği var, repo'da yok |
| `shared/src/**` içinde `realpath` normalizasyonu | Repo'da bu alan kodu YOK — grep 0 isabet |
| Açık `traversal` kontrolü (`..` reddi) | Repo'da bu alan kodu YOK — `traversal` grep 0 (§23) |
| Capability-token yetkilendirme | Repo'da bu alan kodu YOK — JWT var, capability modeli yok |
| Permission hook betiği | Repo'da bu alan kodu YOK — 3 betik: karar/validator/skill (§13) |
| CI'da otomatik izin/secret denetimi | Repo'da bu alan kodu YOK — `.github/workflows/` bu turda `UNKNOWN` |

## §23. Negatif Ölçüm — Grep Kanıtları (araç çıktısı)

| Arama | Kapsam | Sonuç | Tarih |
|---|---|---|---|
| `"permissions"` | `.claude/settings.json` | **0** | 2026-10-17 |
| `"deny"` · `"ask"` | `.claude/settings*.json` | **0** | 2026-10-17 |
| `permission` (case-insensitive) | `shared/src` (165 .php) | **44 isabet / 16 dosya** | 2026-10-17 |
| `allowlist`\|`whitelist`\|`ALLOWED_`\|`allowed_ips` | `shared/src` | **27 / 5 dosya** | 2026-10-17 |
| `in_array` | `shared/src` | **37** (strict eşleşme) | 2026-10-17 |
| `realpath` · `traversal` · `basename` | `shared/src` | **0 · 0 · 1** (`SecurityHelper:51`) | 2026-10-17 |
| `LOCK_EX`\|`flock` | `shared/src` | **5** (TOCTOU kilidi) | 2026-10-17 |
| `../` (21 isabet) | `shared/src` | hepsi yorum/ellipsis; gerçek yol birleşimi tek: `PageRouter.php:123` | 2026-10-17 |

## §24. İllüstratif — Python: Permission Gate (whitelist checker)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı
```python
_AGENT_CATEGORIES = {            # ToolCalling::AGENT_PERMISSIONS (L28-L40) ile aynı şekil
    "mo": {"file", "database", "api", "audio", "hardware", "security"},
    "backend": {"file", "database", "api"},
}
def check_permission(tool_category: str, agent_id: str) -> tuple[bool, str | None]:
    allowed = _AGENT_CATEGORIES.get(agent_id)
    if allowed is None:                       # bilinmeyen agent -> fail-safe red
        return False, f"Unknown agent: {agent_id}"
    if tool_category not in allowed:          # strict üyelik (PHP: in_array(..., true))
        return False, f"not allowed: {tool_category}"
    return True, None
```
**Kapsam:** kategori beyazlistesi — repo karşılığı §18; fail-safe yönü aynı (bilinmeyen → red).
## §25. İllüstratif — TypeScript: ACL Resolver (deny → ask → allow)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı
```typescript
type Action = "allow" | "ask" | "deny";
type Rule = { kind: Action; tool: string; prefix?: string };
// Resmi sıra deny -> ask -> allow; ilk eşleşen kazanır (§35/1)
export function resolve(rules: Rule[], tool: string, arg: string): Action {
  const order = (["deny", "ask", "allow"] as const)
    .flatMap((k) => rules.filter((r) => r.kind === k));
  const hit = order.find((r) => r.tool === tool &&
    (r.prefix === undefined || arg.startsWith(r.prefix)));
  return hit ? hit.kind : "ask";             // eşleşme yok -> sor (fail-safe)
}
```
**Kapsam:** izin çözümleyici — repo karşılığı **YOK** (§22 satır 1-2); davranış upstream kural sırasından türetildi.
## §26. İllüstratif — Go: Path Sanitizer (traversal koruması)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı
```go
// Repo karşılık YOK: shared/src realpath 0 · traversal 0 (§23).
func SanitizeUnder(root, userPath string) (string, error) {
    if strings.Contains(userPath, "\x00") { return "", errors.New("nul byte") }
    joined := filepath.Clean(filepath.Join(root, filepath.FromSlash(userPath)))
    base := filepath.Clean(root) + string(filepath.Separator)
    if !strings.HasPrefix(joined+string(filepath.Separator), base) {
        return "", errors.New("path traversal")     // ../ ve mutlak yol reddi
    }
    abs, err := filepath.EvalSymlinks(joined)        // symlink kaçışı kapatılır
    if err != nil { return "", err }
    if !strings.HasPrefix(abs+string(filepath.Separator), base) { return "", errors.New("symlink escape") }
    return abs, nil
}
```
**Kapsam:** yol denetimi — repo karşılığı **YOK** (§22); tek ilgili repo satırı `PageRouter.php:123`.
## §27. İllüstratif — Rust: Capability Token (öz-belirli yetki)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı
```rust
#[derive(Clone, Copy, PartialEq, Eq)]
pub enum Cap { ReadVault, WriteVault, RunBash, CallTool }
pub struct CapabilityToken { caps: &'static [Cap], ttl_ms: u64, issued_at_ms: u64 }
impl CapabilityToken {
    pub fn permits(&self, need: Cap, now_ms: u64) -> bool {
        now_ms.saturating_sub(self.issued_at_ms) <= self.ttl_ms   // süre sınırı
            && self.caps.contains(&need)                          // dar kümeli yetki
    }
}
```
**Kapsam:** süre-sınırlı jeton — repo karşılığı **YOK** (§22); repo'daki tek jeton benzeri yapı JWT RS256'dır (`.gitignore` L80).
## §28. İllüstratif — C++: File Guard (RAII salt-okunur kayıt)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı
```cpp
#include <filesystem>
#include <fstream>
// Yalnız okuma modunda açılabilir dosya guard'ı; dtor'da akış kapanır (RAII).
class ReadOnlyFile final {
    mutable std::ifstream in_;
public:
    explicit ReadOnlyFile(const std::filesystem::path& p) : in_(p, std::ios::binary) {}
    bool read_all(std::string& out) const {
        if (!in_) return false;
        out.assign(std::istreambuf_iterator<char>(in_), std::istreambuf_iterator<char>());
        return true;
    }
};
```
**Kapsam:** salt-okunur erişim — repo yüzeyi PHP olduğu için karşılığı **YOK**; C++ yüzeyi Neva Engine/PLANNED.
## §29. İllüstratif — C#: IPermission-based Check

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı
```csharp
public sealed record PermissionDecision(bool Allowed, string? Reason);
public interface IPermissionChecker {
    PermissionDecision Check(IPermissionRequired target, ClaimsPrincipal user);
}
public sealed class ClaimsPermissionChecker : IPermissionChecker {
    public PermissionDecision Check(IPermissionRequired target, ClaimsPrincipal user) {
        if (target is null) return new(false, "target yok");              // fail-safe
        if (user.Identity?.IsAuthenticated != true) return new(false, "anonim");
        var have = user.Claims.Where(c => c.Type == "permission")
                              .Select(c => c.Value).ToHashSet(StringComparer.Ordinal);
        return have.Contains(target.RequiredPermission)
            ? new(true, null) : new(false, $"izin yok: {target.RequiredPermission}");
    }
}
```
**Kapsam:** claim tabanlı denetim — repo karşılığı PHP: §16 + §17; C# yüzeyi Windows/PLANNED.
## §30. Kavram ↔ Eşleşme Tablosu

| Kavram | Repo karşılığı | Eşleşme |
|---|---|---|
| PreToolUse izin kapısı | `settings.json` L13-L25 + `ai-risk-gate.cjs` | **evet** |
| allow kural listesi | `settings.local.json` L2-L12 | **evet** |
| deny kuralı | — | **YOK** |
| ask kuralı (ayar dosyasında) | — (yalnız hook kararı, §14) | **kısmi** |
| RBAC rol + permission | `PermissionMiddleware` L17-L45 · `AuthorizationMiddleware` L60-L87 | **evet** |
| Agent yetki matrisi | `ToolCalling::AGENT_PERMISSIONS` L28-L40 | **evet** |
| Host/IP/origin allowlist | `ReturnUrlPolicy` L7 · `ApiKeyRepository` L199 · `CorsMiddleware` L63 | **evet** |
| Secret koruma | `.gitignore` L5-L9, L68-L71, L80 + §12 maskeleme | **evet** |
| Salt-okunur komut izni | `Bash(cat)`, `Bash(npm list *)` (§10) | **kısmi** |
| Path traversal denetimi | — (§22, §23) | **YOK** |
| Capability token | JWT RS256 (`.gitignore` L80) | **kısmi** |
| TOCTOU koruması | `LOCK_EX` 5 isabet (§23) | **kısmi** |
| İzın denetim betiği | — (3 hook: karar/validator/skill) | **YOK** |

## §31. Güvenlik Notları (kanıt ile)

| # | Not | Kanıt |
|---|---|---|
| 1 | `.claudeignore` **etkisizdir** — resmi: "etkisi yoktur, girdileri `Read` deny kuralına taşıyın" | `.claudeignore` L1-L8 + §35/1 |
| 2 | `filesystem` MCP `C://` kökünü kapsar — en geniş yüzey; `disabled` yalnız 3 sunucuda | L54, L77, L86, L97 |
| 3 | Anahtar düz metin **ve** commit edilen paylaşılan dosyada | L94-L96 + §12 |
| 4 | TOCTOU: yazım `LOCK_EX` — kontrol ile eylem arası boşluk kısmen kapanır | `SecurityHelper.php` L54 · §23 (5) |
| 5 | Path traversal: `shared/src` içinde `realpath`/`traversal` **0** | §23 |
| 6 | Fail-safe yönü tutarlı: `allowed:false` · `return '/'` · `exit 0` | §18 · §19 · §14 |
| 7 | Dış yazım kazası tarihsel: `dirname(...,4)` repo dışına yazıyordu → `dirname(__DIR__,3)` | `SecurityHelper.php` L44-L46 |

## §32. Ölçüm Kalıpları (repo kökünden)

| Amaç | Komut |
|---|---|
| Satır sayısı | `[IO.File]::ReadAllLines($hedef,[Text.Encoding]::UTF8).Count` |
| Secret tarama (bu dosya) | `Select-String -Path $hedef -Pattern '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}'` → **0** |
| İzin anahtarı yokluğu | `Select-String '.claude\settings.json' -Pattern '"permissions"'` → 0 |
| PHP izin yoğunluğu | `Get-ChildItem shared\src -Recurse -Filter *.php` + `Select-String 'permission'` → 44/16 |
| Mojibake | `ReadAllText` içinde `U+FFFD` ara → 0 · `^## §` sayısı → `>= 25` |

## §33. Doğrulama Kapıları

| # | Kapı | Kriter | Durum |
|---|---|---|---|
| 1 | Satır sayısı | `>= 500` (band 500-520) | §32 ölçümü |
| 2 | Frontmatter + Authority | 10 alan; `title`/`parent` yok · son satır birebir | gözden geçirme |
| 3 | Künyeli alıntı | her `Dosya:`/`Satır:` gerçek okuma | 2026-10-17 |
| 4 | Secret maskeleme | bu dosyada UUID deseni **0** · `[REDACTED]` mevcut | §32 |
| 5 | İllüstratif etiket | 6 blok + üstünde etiket | §24-§29 |
| 6 | Yokluk cümleleri | 8 satır "Repo'da bu alan kodu YOK" | §22 |
| 7 | Section sayısı | `>= 25` `## §` | §32 |
| 8 | Web kaynağı | 2 resmi URL fetch + 1 arama; erişilemezse `⚠️ VERIFICATION REQUIRED` | §35 |

## §34. Açık Sorular

| # | Soru | Durum |
|---|---|---|
| 1 | `.claudeignore` → `Read` deny kuralına taşınacak mı? (etkisiz, §31 #1) | açık |
| 2 | `settings.json` içindeki `EXA_API_KEY` `.env` dosyasına taşınacak mı? (§12) | açık |
| 3 | `filesystem` MCP `C://` kapsamı `coremusic.net` ile mi daraltılacak? (§31 #2) | açık |
| 4 | Paylaşılan dosyaya `permissions.deny` (ör. `Read(**/.env)`) eklenecek mi? (§22) | açık |
| 5 | `.github/workflows/` secret-scan işi bu turda okunmadı → `UNKNOWN` | açık |

## §35. Kaynaklar (web doğrulama)

| # | Kaynak | Erişim | Kullanım |
|---|---|---|---|
| 1 | https://code.claude.com/docs/en/permissions — *Configure permissions* | **doğrulandı** (2026-10-17 fetch) | deny→ask→allow · izin modları · salt-okunur komut seti · `Read(./.env)` · `.claudeignore` etkisizliği · Read/Edit gitignore sözdizimi |
| 2 | https://code.claude.com/docs/en/settings — *Settings files and precedence* | **doğrulandı** (2026-10-17 fetch) | 4 dosya kapsamı · öncelik · listenin birleşmesi · `.claude/settings.json` = paylaşılan proje ayarı |
| 3 | https://code.claude.com/docs/en/hooks — *Hooks reference* | **doğrulandı** (arama özeti, 2026-10-17) | "Hook'lar JSON ayar dosyalarında tanımlanır" · sert allow/deny için izin sistemi |
| — | Ulaşılamayan iddia | — | `⚠️ VERIFICATION REQUIRED` · uydurma URL yasak (§1) |

## §36. Yasaklar + Gate Özeti

| Yasak | Kural kaynağı |
|---|---|
| `git commit` atmak | AGENTS §16.5 / kök AGENTS §7 — commit orkestratöre aittir |
| `.claude/**` ve `.ai/**` yazımı (salt-okunur) + bu dosya dışına yazım | görev manifesti |
| Uydurma `Dosya:`/`Satır:` künyesi · illüstratif bloğa repo kanıtı süsü vermek | Zero-Hallucination (AGENTS §3) · §1 |
| Düz metin secret/anahtar yazmak (`[REDACTED]` zorunlu) | §12 · `.claude/AGENTS.md` §3.3 #1 |
| Dosya adı değiştirme · bekleyen çift-süslü-parantez placeholder · ölçülmemiş sayıya "ölçüldü" demek | görev manifesti · RAG.md §3.2 kural 4 |

**Gate özeti:** §1-§36 · künye 8 kural · envanter 7 dosya · settings.json alıntı 6 blok (hook ×3 · instructions · mcpServers · watcher/references) · `.local` allow 7 kural · `.gitignore` 3 küme · maskeleme 1 tespit/5 adım · hook 3 betik · beyazliste 2 betik · PHP izin 6 bölüm · yokluk 8 · negatif ölçüm 8 · illüstratif 6 blok/6 dil · eşleme 13 · güvenlik 7 · ölçüm 5 · kapı 8 · açık soru 5 · kaynak 3 (2 fetch + 1 arama) → kapılar: `>=500` · FM 10 alan · Authority son satır · secret UUID 0.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
