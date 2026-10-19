---
node_id: mcps-ornekler
node_type: module
status: active
parent_id: mcps
children: []
depends_on: [mcps-anayasa, mcps-surucu]
evidence:
  - ".claude/mcp.json (gerçek sunucu tanımı alıntıları, 2026-10-17 okuma)"
  - ".claude/settings.json (plugin/hook env referansları, künyeli)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı, kod kanıtı zorunlu"
open_questions: []
group: kod
---

# mcps-ornekler - MCP Kod Örnekleri (kanıt odaklı)

> **Kapsam:** Her JSON alıntısı 2026-10-17'de `.claude/mcp.json` (9 satır) ve `.claude/settings.json` (126 satır)
> dosyalarından okunmuş **gerçek** satırlardır (`Dosya:` / `Satır:` künyesi ile); API anahtarı `[REDACTED]` ile
> maskelenmiştir. Çok-dilli bloklar illüstratiftir ve her birinin üstünde zorunlu etiket taşır. Kaynak dosyalar salt-okunurdur.

## §1. Kanıt Künye Formatı

| Alan | Kural |
|---|---|
| Dosya | göreli yol, diskte var (glob ile doğrulanmış) |
| Satır | okunan aralık (L49-L58 gibi) — okunmamış satır künye olamaz |
| Alıntı | JSON/MD satırının kendisi (kısaltma serbest, uydurma yasak) |
| Secret | değer yerine `[REDACTED]` — düz metin anahtar yazımı yasak |
| Yokluk | tam cümle: "Repo'da bu sunucu YOK — kaynak: upstream docs" |
| Ölçüm | `ReadAllLines` / `git ls-files` sayaçları — araç çıktısı, iddia değil |
| İllüstratif | `⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı` |
| Tarih | her kanıt 2026-10-17 okuma turuna ait |
| Eşleşme | `evet` / `kısmi` / `YOK` — başka değer yok |
| Yasak | uydurma künye → Zero-Hallucination ihlali (kök AGENTS §3) · çelişkide disk kazanır |

## §2. Okunan Yapılandırma Envanteri (2 birincil + 1 tamamlayıcı)

| # | Dosya | Satır | MCP içeriği | Tarih |
|---|---|---|---|---|
| 1 | `.claude/mcp.json` | 9 | 1 sunucu: `exa-mcp-server` (L2-L8) | 2026-10-17 |
| 2 | `.claude/settings.json` | 126 | `mcpServers` 6 kayıt (L48-L99): 3 tanımlı, 3 `disabled: true` | 2026-10-17 |
| 3 | `.opencode/opencode.json` | 296+ | `mcp` bloğu 4 kayıt (L258-L296) — tamamlayıcı üçüncü yüzey | 2026-10-17 |

`git ls-files .claude/mcp.json .claude/settings.json` → her iki dosya izleniyor (araç çıktısı) · `.claude/CONTEXT.md` L179: *"iki ayrı tanımlama yüzeyi … runtime durumu UNKNOWN"*.

## §3. `mcp.json` — Gerçek Sunucu Tanımı (tek sunucu)

Dosya: `.claude/mcp.json` · Satır: L1-L9 (tam dosya)

```json
{
  "exa-mcp-server": {
    "type": "local",
    "command": ["npx", "-y", "exa-mcp-server"],
    "environment": {
      "EXA_API_KEY": "[REDACTED]"
    }
  }
}
```

| Gözlem | Kanıt |
|---|---|
| Anahtar adı `exa-mcp-server` — dosyadaki **tek** sunucu | L2 |
| `type: "local"` · komut **dizi** · env anahtarı `environment` | L3-L6 |
| `-y` → npx otomatik onay (sürüm sabitlenmemiş, en son sürüm çekilir) | L4 |
| Secret değeri bu dosyada düz metin durur → bu belgede `[REDACTED]` | L6 |

## §4. `settings.json` → filesystem (kök kilitli dosya sunucusu)

Dosya: `.claude/settings.json` · Satır: L49-L58

```json
"filesystem": {
  "command": "npx",
  "args": [
    "-y",
    "@modelcontextprotocol/server-filesystem",
    "C://",
    "C://www",
    "C://www//coremusic.net"
  ]
}
```

| Gözlem | Kanıt |
|---|---|
| `command` **dize**, argümanlar ayrı `args` dizisi (mcp.json şemasından farkı §10) | L50-L57 |
| Üç erişim kökü: `C://` · `C://www` · `C://www//coremusic.net` — **kök `C://` tüm diski açar** | L54-L56 |
| `disabled` alanı yok → tanımlı ve etkin görünür (runtime durumu UNKNOWN, §2) | L49-L58 |
| Kardeş dosya ile aynı gözlem: `.ai/architecture/K015-AI/tools/tools-ornekler.md` L475 "aktif" | vault çapraz kanıt |

## §5. `settings.json` → chrome-devtools

Dosya: `.claude/settings.json` · Satır: L59-L65

```json
"chrome-devtools": {
  "command": "npx",
  "args": [
    "-y",
    "chrome-devtools-mcp@latest"
  ]
}
```

`@latest` öneki → her kurulumda en yeni sürüm; sürüm kilidi (pin) **repo'da YOK** (`package.json` teyidi §25).

## §6. `settings.json` → claude-mem (yerel plugin script'i)

Dosya: `.claude/settings.json` · Satır: L66-L71

```json
"claude-mem": {
  "command": "node",
  "args": [
    "c:/.claude/plugins/marketplaces/thedotmack/plugin/scripts/mcp-server.cjs"
  ]
}
```

| Gözlem | Kanıt |
|---|---|
| `npx` değil doğrudan `node` + mutlak dosya yolu | L67-L70 |
| Bağımlılık: marketplace plugin dizini (npm registry değil) | L69 |
| `enabledPlugins` bloğu ayrıdır (L123-L125) — bu sunucu ile ilişkisi belgelenmemiş | L66-L71 |

## §7. `settings.json` → browsermcp (devre dışı)

Dosya: `.claude/settings.json` · Satır: L72-L78

```json
"browsermcp": {
  "command": "npx",
  "args": [
    "@browsermcp/mcp@latest"
  ],
  "disabled": true
}
```

`"disabled": true` (L77) → bu yüzeyde tanımlı ama **kapalı**. `@browsermcp/mcp@latest` args'ında `-y` yok (L75) — §5/§8'den farklı yazım.

## §8. `settings.json` → playwright (devre dışı, headless)

Dosya: `.claude/settings.json` · Satır: L79-L87

```json
"playwright": {
  "command": "npx",
  "args": [
    "-y",
    "@playwright/mcp",
    "--headless"
  ],
  "disabled": true
}
```

`--headless` bayrağı (L84) görünür penceresiz tarayıcı demektir; `"disabled": true` (L86) bu yüzeyde kapalı olduğunu söyler — §10'daki üç-yüzey çakışması bu sunucuda ters yöndür.

## §9. `settings.json` → exa (devre dışı + secret taşıyan tek kayıt)

Dosya: `.claude/settings.json` · Satır: L88-L98

```json
"exa": {
  "command": "npx",
  "args": [
    "-y",
    "exa-mcp-server"
  ],
  "env": {
    "EXA_API_KEY": "[REDACTED]"
  },
  "disabled": true
}
```

| Gözlem | Kanıt |
|---|---|
| Aynı paket `exa-mcp-server` — `mcp.json` L4 ile birebir aynı komut | L92 ↔ mcp.json L4 |
| Secret anahtarı **ikinci kez** aynı değerde: `env` (L94-L96) ↔ `environment` (mcp.json L5-L7) | 2 isabet, 0 düz metin (§11) |
| Bu yüzeyde `disabled: true` (L97), `mcp.json`'da ise disable yok → **aktiflik ters** | L97 ↔ mcp.json L1-L9 |

## §10. Üç Yapılandırma Yüzeyi — Şema ve Durum Karşılaştırması

| Alan | `.claude/mcp.json` (L1-L9) | `.claude/settings.json` (L48-L99) | `.opencode/opencode.json` (L258-L296) |
|---|---|---|---|
| Anahtar | `exa-mcp-server` | `exa` | `chrome-devtools` · `browsermcp` · `playwright` · `filesystem` |
| Komut biçimi | `command` **dizi** | `command` **dize** + `args` dizisi | `command` **dizi** |
| Secret anahtarı adı | `environment` | `env` | — (bu blokta yok) |
| Kapatma anahtarı | yok | `disabled: true` (3 kayıt) | `enabled: true` (3 kayıt) |
| filesystem kökleri | — | `C://` + `C://www` + `C://www//coremusic.net` | `C://www//coremusic.net` + `C://www//coremusic.net.old` |

**Çakışma satırı:** `browsermcp` ve `playwright` `.claude/settings.json` L77/L86'da **kapalı**, `.opencode/opencode.json` L273/L283'te **açık**.
Hangi yüzeyin öncelikli olduğu repo'da belgelenmiş **DEĞİL** — `.claude/CONTEXT.md` L179 aynı hükmü verir (`UNKNOWN`).

## §11. Secret Maskeleme Kuralı (gösterim)

| # | Kural | Kanıt / Uygulama |
|---|---|---|
| 1 | Vault `.md` içine anahtar değeri yazılmaz | kök `AGENTS.md` §3 Zero-Hallucination + `.claude/AGENTS.md` §3.3 #1 |
| 2 | Gösterim biçimi: `"EXA_API_KEY": "[REDACTED]"` | bu dosya §3, §9 |
| 3 | Aynı secret iki dosyada: `mcp.json:6` + `settings.json:95` → iki düz metin kayıt, **bu belgede 0** | 2026-10-17 okuma |
| 4 | `.claude/CONTEXT.md` L146 da aynı maskeyi kullanır: "`EXA_API_KEY` içerir → değer REDACTED" | vault uyumu |
| 5 | `.gitignore` L63 yalnız `.playwright-mcp/` içerir; `.claude/mcp.json` ignore **değildir** | gitignore ölçümü |

> ⚠️ **Bulgu (Security):** takip edilen iki dosyada secret düz metin duruyor; değer bu belgeye **hiçbir biçimde** kopyalanmadı.
> Sahiplik: `.claude/AGENTS.md` §3.1 — Security denetimi + kullanıcı onayı.

## §12. `settings.json` → Hook / MCP İlişki Alıntıları

Dosya: `.claude/settings.json` · Satır: L2-L39

| Hook olayı | Komut (alıntı) | Timeout / Mesaj | Satır |
|---|---|---|---|
| `UserPromptSubmit` | `node "${CLAUDE_PROJECT_DIR}/.claude/hooks/skill-mandate.cjs"` | — | L6-L9 |
| `PreToolUse` (`Edit\|Write`) | `node "${CLAUDE_PROJECT_DIR}/.claude/hooks/ai-risk-gate.cjs"` | `15` · "Control Plane HIGH-RISK kapısı (T1/T2)" | L15-L22 |
| `PostToolUse` (`Edit\|Write`) | `node "${CLAUDE_PROJECT_DIR}/.claude/hooks/ai-validate-post.cjs"` | `60` · ".ai validator (fm/link/ssot/dep/tier/orphan)" | L27-L36 |

**İlişki cümlesi:** hook'lar **MCP sunucusu çağırmaz** — MCP `mcpServers` bloğundadır (L48). Ortak payda tek `env` genişletmesidir:
`${CLAUDE_PROJECT_DIR}` (L8, L19, L32). MCP çağrısı araç oturumunda olur; hook yalnız kapı koyar.

## §13. `settings.json` → instructions / references / enabledPlugins

Dosya: `.claude/settings.json` · Satır: L40-L47, L100-L125

| Blok | Alıntı (özet) | Satır |
|---|---|---|
| `instructions` | `CLAUDE.md` · `.ai/CLAUDE.md` · `.ai/AGENTS.md` · `.ai/WORKFLOW.md` · `.ai/CONTEXT.md` · `.ai/architecture/context.md` (6 kayıt) | L41-L46 |
| `references` | `vault`→`.ai` · `rules`→`.claude/rules` · `workflows`→`.workflows` | L110-L121 |
| `enabledPlugins` | `frontend-design@claude-plugins-official: true` (tek plugin) | L123-L125 |

`instructions` MCP ile **ilgili değildir** (boot okuma listesi); MCP bloğu ile aynı dosyada olduğu için künye burada verilmiştir.

## §14. Tool Çağrı Kalıbı — `tools/list` ve `tools/call` (upstream, doğrulandı)

Kaynak: `modelcontextprotocol.io/specification/2025-06-18/server/tools` (2026-10-17 webfetch)

```json
{ "jsonrpc": "2.0", "id": 1, "method": "tools/list", "params": { "cursor": "optional-cursor-value" } }
```

```json
{ "jsonrpc": "2.0", "id": 2, "method": "tools/call",
  "params": { "name": "get_weather", "arguments": { "location": "New York" } } }
```

```json
{ "jsonrpc": "2.0", "id": 2,
  "result": { "content": [ { "type": "text", "text": "Current weather in New York..." } ], "isError": false } }
```

| Kalıp | Kural (spec) |
|---|---|
| Keşif | `tools/list` → `tools[]` (`name`, `title?`, `description`, `inputSchema`), sayfalama `nextCursor` |
| Çağrı | `tools/call` → `params.name` + `params.arguments` |
| Bildirim | `notifications/tools/list_changed` (yalnız `capabilities.tools.listChanged` bildirdiyse) |
| Yetenek beyanı | sunucu `initialize` yanıtında `capabilities.tools` **MUST** bildirir |

## §15. Hata Kalıpları — Protokol Hatası vs Araç Hatası

Kaynak: aynı sayfa (§14) — 2026-10-17 doğrulandı

```json
{ "jsonrpc": "2.0", "id": 3, "error": { "code": -32602, "message": "Unknown tool: invalid_tool_name" } }
```

```json
{ "jsonrpc": "2.0", "id": 4, "result": { "content": [ { "type": "text", "text": "API rate limit exceeded" } ], "isError": true } }
```

| Tür | Ne zaman | Taşıyıcı |
|---|---|---|
| Protokol hatası | bilinmeyen tool, geçersiz argüman, sunucu hatası | JSON-RPC `error` (ör. `-32602`) |
| Araç yürütme hatası | API/veri/iş mantığı hatası | `result.isError: true` |

**Eşleştirme:** kök `AGENTS.md` "Result/Either" kuralı → MCP'de karşılığı bu iki kattır (nevrotik dönüş yok, `isError` işaretli).

## §16. stdio Transport Kuralları (upstream)

Kaynak: `modelcontextprotocol.io/specification/2025-06-18/basic/transports` (2026-10-17 webfetch)

| Kural | Spec cümlesi (özet) |
|---|---|
| Ayrıştırma | mesajlar tek satır JSON-RPC; gömülü newline **MUST NOT** |
| stdout saflığı | sunucu `stdout`'a geçerli MCP dışı hiçbir şey yazamaz (`MUST NOT`) |
| log | `stderr` UTF-8 log içindir; istemci yakalayabilir/umursamayabilir |
| tercih | istemci stdio'yu mümkün olduğunda desteklemelidir (`SHOULD`) |
| süreç | istemci sunucuyu **alt süreç** olarak başlatır ve stdin'i kapatarak sonlandırır |

**Eşleme:** §3-§9'daki her kayıt `npx`/`node` ile alt süreç başlatan stdio sunucusudur — `type: "local"` ifadesi bunu teyit eder (mcp.json L3).

## §17. Streamable HTTP (upstream — repo'da KULLANIM YOK)

Kaynak: aynı sayfa (§16) — 2026-10-17 doğrulandı

| Kural | Değer |
|---|---|
| Uç nokta | tek MCP endpoint, POST + GET (`https://example.com/mcp`) |
| Sürüm başlığı | `MCP-Protocol-Version: 2025-06-18` sonraki tüm isteklerde **MUST** |
| Oturum | `Mcp-Session-Id` başlığı (varsa) tüm sonraki isteklerde **MUST**; 404 → yeni `InitializeRequest` |
| Güvenlik | `Origin` doğrulama (DNS rebinding) **MUST**; yerelde yalnız `127.0.0.1` **SHOULD** |
| Uyumluluk | başlık yoksa sürüm `2025-03-26` varsayılır; geçersiz sürüm → `400` |

**Yokluk:** repo yapılandırmalarında `url` alanlı (HTTP) sunucu tanımı **YOK** — üç yüzeyin hepsi `command`/stdio (§10).

## §18. İllüstratif — TypeScript: minimal MCP server (tool tanımı + handler)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```typescript
import { McpServer } from "@modelcontextprotocol/sdk/server/mcp.js";
import { StdioServerTransport } from "@modelcontextprotocol/sdk/server/stdio.js";
import { z } from "zod";

const server = new McpServer({ name: "coremusic-fs", version: "0.1.0" });

server.registerTool("list-root",
  { title: "List Root", description: "Verilen kökteki dosyaları listeler",
    inputSchema: { root: z.string().describe("Erişim kökü (filesystem args[3] benzeri)") } },
  async ({ root }) => ({ content: [{ type: "text", text: `root=${root}` }] }));

await server.connect(new StdioServerTransport());   // §16 stdio: stdout yalnız MCP mesajı
```

**Kapsam:** `inputSchema` (JSON Schema) + handler zinciri — repo'da MCP SDK bağımlılığı **YOK** (§25).

## §19. İllüstratif — Python: MCP client tool çağrım wrapper'ı

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
import asyncio
from mcp import ClientSession, StdioServerParameters
from mcp.client.stdio import stdio_client

async def call_tool(cmd: str, args: list[str], name: str, arguments: dict) -> str:
    params = StdioServerParameters(command=cmd, args=args)      # .claude/settings.json command+args
    async with stdio_client(params) as (read, write):
        async with ClientSession(read, write) as session:
            await session.initialize()                          # lifecycle: initialize → initialized
            tools = await session.list_tools()                  # §14 tools/list
            result = await session.call_tool(name, arguments)   # §14 tools/call
            return "".join(c.text for c in result.content if c.type == "text")
```

**Kapsam:** istemci sarmalayıcısı — `command`+`args` şekli §4-§9 JSON'una birebir oturur; repo karşılığı yok.

## §20. İllüstratif — Go: JSON-RPC istemci (stdio çerçevesi)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
type rpcRequest struct {
    JSONRPC string `json:"jsonrpc"` // "2.0"
    ID      int    `json:"id"`
    Method  string `json:"method"`  // "tools/list" | "tools/call"
    Params  any    `json:"params,omitempty"`
}

func call(ctx context.Context, w *bufio.Writer, r *bufio.Reader, method string, params any) ([]byte, error) {
    b, _ := json.Marshal(rpcRequest{JSONRPC: "2.0", ID: 1, Method: method, Params: params})
    if _, err := w.Write(append(b, '\n')); err != nil { return nil, err } // §16: tek satır, gömülü newline yok
    w.Flush()
    return ReadLine(r) // yanıt aynı satırda JSON-RPC response
}
```

**Kapsam:** JSON-RPC kabuğu. Repo kanıtı (illüstratif DEĞİL): `.ai/log.md` L81 — `fxmcp.exe` · Go `go1.26.5` · resmi `modelcontextprotocol/go-sdk v1.6.1` · CGO=0.

## §21. İllüstratif — Rust: yapılandırma ayrıştırıcı (mcp.json şeması)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```rust
#[derive(Debug, serde::Deserialize)]
#[serde(rename_all = "camelCase")]
pub struct McpServer {                      // mcp.json L2-L8 şeması
    #[serde(default)] pub r#type: Option<String>,   // "local"
    pub command: Vec<String>,               // ["npx","-y","exa-mcp-server"]
    #[serde(default)] pub environment: Option<std::collections::HashMap<String, String>>,
}
pub type McpRoot = std::collections::HashMap<String, McpServer>;
// değerler asla log'a yazılmaz → "[REDACTED]" (§11 kural 1)
```

**Kapsam:** `type` + `command` + `environment` üçlüsü — §3 JSON'ının birebir karşılığı; repo'da Rust MCP kodu **YOK**.

## §22. İllüstratif — C++: stdio transport (satır sonlu JSON-RPC)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```cpp
// §16 kuralları: stdout yalnız geçerli MCP mesajı; log stderr'e; mesaj tek satır.
std::string line;
while (std::getline(std::cin, line)) {          // istemci → stdin
    if (line.find('\n') != std::string::npos) continue;   // gömülü newline kabul edilmez
    nlohmann::json req = nlohmann::json::parse(line, nullptr, false);
    if (req.is_discarded()) { std::cerr << "bad json\n"; continue; }  // log → stderr
    nlohmann::json res = dispatch(req);                       // tools/list | tools/call
    std::cout << res.dump() << std::endl;                     // tek satır, UTF-8
}
```

**Kapsam:** transport katmanı — repo'da MCP'ye dair C++ kodu **YOK** (C++ yüzeyi Neva Engine / PLANNED, `*.cpp` 0).

## §23. İllüstratif — C#: tool registry (ad → tanım + yürütücü)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```csharp
public sealed record ToolDef(string Name, string Description, JsonElement InputSchema,
                              Func<JsonElement, CancellationToken, Task<ToolResult>> Handler);

public sealed class ToolRegistry {
    private readonly Dictionary<string, ToolDef> _tools = new(StringComparer.Ordinal);
    public void Register(ToolDef def) => _tools[def.Name] = def;         // tekil ad (spec: name)
    public IReadOnlyList<ToolDef> List() => _tools.Values.ToArray();     // → tools/list
    public async Task<ToolResult> CallAsync(string name, JsonElement args, CancellationToken ct)
        => _tools.TryGetValue(name, out var t)
            ? await t.Handler(args, ct)                                  // → tools/call
            : ToolResult.ProtocolError(-32602, $"Unknown tool: {name}"); // §15 protokol hatası
}
```

**Kapsam:** kayıt/çağrı ayrımı — PHP karşılığı repo'da vardır: `shared/src/AI/ToolCalling.php` ("Tool registry, executor, permission check", L16; `file-search` kaydı L133-L144), MCP'ye özgü değildir.

## §24. Kavram ↔ Eşleşme Tablosu (MCP kavramı → repo karşılığı)

| MCP kavramı (spec) | Repo karşılığı | Eşleşme |
|---|---|---|
| Sunucu tanımı | `.claude/mcp.json` L1-L9 · `settings.json` L48-L99 | **evet** |
| stdio transport (`type: local`) | her kayıt `npx`/`node` alt süreç | **evet** |
| `command` + `args` | filesystem L50-L57 | **evet** |
| `env` / `environment` (secret) | `mcp.json` L5-L7 · `settings.json` L94-L96 | **evet** (REDACTED) |
| `disabled`/`enabled` anahtarı | `settings.json` L77/L86/L97 · opencode L273/L283/L294 | **evet** |
| Kök/izin sınırı (filesystem args) | `C://`, `C://www`, `C://www//coremusic.net` L54-L56 | **evet** (§26) |
| Tool tanımı (`name`/`description`/`inputSchema`) | `shared/src/AI/ToolCalling.php` L133-L144 | **kısmi** (MCP değil, PHP) |
| Tool izni / fail-safe | `ToolCalling` `allowed: false` L158-L163 | **kısmi** |
| `tools/list` · `tools/call` mesajları | repo'da JSON-RPC mesaj üreten kod | **YOK** (§25) |
| Streamable HTTP / oturum başlığı | — | **YOK** (§17) |
| `initialize` yaşam döngüsü | — | **YOK** |
| Sürüm pin'i (`@2.x.y`) | `@latest` önekleri L63/L75/L84/L92 | **YOK** (sürümsüz) |
| Human-in-the-loop onay | hook `ai-risk-gate.cjs` L19 (MCP çağrısı değil) | **kısmi** |
| Log/stderr ayrımı | hook `statusMessage` L21/L34 | **kısmi** |

## §25. Yokluk Cümleleri (tahmin yasak)

| Konu | Durum (2026-10-17) |
|---|---|
| `@modelcontextprotocol/server-postgres`, `server-github`, `fetch` gibi sunucular | Repo'da bu sunucu YOK — kaynak: upstream docs örnekleri |
| `mcp.json` içinde ikinci bir sunucu | Repo'da bu tanım YOK — dosya tek sunucudur (L2-L8) |
| HTTP/`url` tabanlı MCP sunucusu (Streamable HTTP) | Repo'da bu taşıma YOK — üç yüzey de `command`/stdio |
| `@modelcontextprotocol/sdk` npm bağımlılığı | Repo'da bu bağımlılık YOK — kaynak: grep `modelcontextprotocol` (`package.json` isabeti 0) |
| Sürüm kilidi / lock (`exa-mcp-server@x.y.z`) | Repo'da bu pin YOK — `npx -y` + `@latest` (L4, L63, L84) |
| Lisans kaydı (sunucu başına SPDX) | Repo'da bu kayıt YOK — `⚠️ VERIFICATION REQUIRED` |
| MCP sunucusu için ayrılmış test/entegrasyon testi | Repo'da bu test YOK — kaynak: `shared/tests/` envanteri |
| `.ai/mcps/` sürüm takip tablosu | Repo'da bu dosya YOK — `mcps/index.md` L13-L14 açık soru olarak taşır |

## §26. Güvenlik Notları (kanıt ile)

| # | Not | Kanıt |
|---|---|---|
| 1 | MCP config'e anahtar **yazılmaz**; `.env`/env substitution kullanılır → bu belgede tüm değerler `[REDACTED]` | kök `AGENTS.md` §3 · §11 |
| 2 | `filesystem` sunucusu kök kilitli **olmalı**; mevcut tanım `C://` ile **tüm disk**i açar → daraltma adayı | `settings.json` L54 |
| 3 | Aynı secret iki dosyada düz metin + ikisi de git'te izleniyor → rotasyon/`settings` onayı gerekir | `git ls-files` · §11 #5 |
| 4 | `browsermcp`/`playwright`/`exa` bu yüzeyde `disabled: true` → kapalı olduklarında araç çağrısı yapılmaz | L77, L86, L97 |
| 5 | Spec: araç çağrısı için kullanıcı onayı önerilir; araç annotations'ı güvenilmez sayılır | upstream tools §Security (§14 kaynağı) |

## §27. Ölçüm Kalıpları (repo kökünden)

| Amaç | Komut |
|---|---|
| Satır sayısı | `[IO.File]::ReadAllLines($hedef,[Text.Encoding]::UTF8).Count` |
| Kapı | sonuç `>= 500` (hedef bant 500-520) → PASS |
| Secret taraması | `Select-String -Path dosya -Pattern '[0-9a-f]{8}-[0-9a-f]{4}-' ` → hedef **0 isabet** |
| Sunucu sayısı | `mcp.json` JSON anahtar sayısı · `settings.json` `mcpServers` içi `": {"` sayısı |
| Mojibake | `ReadAllText` içinde U+FFFD ara → 0 |
| Git izleme | `git ls-files .claude/mcp.json .claude/settings.json` → 2 satır |

## §28. Doğrulama Kapıları

| # | Kapı | Kriter | Durum |
|---|---|---|---|
| 1 | Satır sayısı | `>= 500` (hedef bant 500-520) | §27 ölçümü |
| 2 | Frontmatter + Authority | 10 alan; `title`/`parent` yok · son satır birebir | gözden geçirme |
| 3 | JSON kanıtı | her alıntı gerçek `Dosya:`/`Satır:` künyeli | 2026-10-17 okuma |
| 4 | Secret maskeleme | düz metin anahtar isabeti **0** | §27 tarama |
| 5 | İllüstratif etiket | 6 dilde blok + üstünde zorunlu etiket | §18-§23 |
| 6 | Yokluk cümleleri | §25'te eksiksiz "YOK" satırları | §25 |
| 7 | Section sayısı | en az 25 `## §` | grep |
| 8 | Placeholder + Mojibake | çift süslü parantez yer tutucu yok · U+FFFD 0 | grep |

## §29. Açık Sorular

| # | Soru | Durum |
|---|---|---|
| 1 | `mcps-anayasa` · `mcps-surucu` — `depends_on` listesinde ama glob (`K015-AI/**/*.md`) **0 isabet** | dosyalar diskte YOK — üretim bekliyor |
| 2 | Sunucu sürüm/star/lisans kanıtı? | `⚠️ VERIFICATION REQUIRED` (`mcps/index.md` L13-L14 ile aynı) |
| 3 | `mcp.json` ↔ `settings.mcpServers` öncelik kuralı? | repo'da belge YOK → `UNKNOWN` (`.claude/CONTEXT.md` L179) |
| 4 | `claude-mem` ile `enabledPlugins` ilişkisi? | belgelenmemiş → `UNKNOWN` |
| 5 | `EXA_API_KEY` rotasyon kararı | Security + kullanıcı onayı bekliyor (§26 #3) |

## §30. Kaynaklar (doğrulanan resmi kaynaklar)

| # | Kaynak | Ne verdi | Doğrulama |
|---|---|---|---|
| 1 | `https://modelcontextprotocol.io/specification/2025-06-18` | JSON-RPC 2.0 · Host/Client/Server · Resources/Prompts/Tools · güvenlik prensipleri | 2026-10-17 webfetch |
| 2 | `https://modelcontextprotocol.io/specification/2025-06-18/basic/transports` | stdio + Streamable HTTP kuralları (§16, §17) | 2026-10-17 webfetch |
| 3 | `https://modelcontextprotocol.io/specification/2025-06-18/server/tools` | `tools/list` · `tools/call` · `isError` · `-32602` (§14, §15) | 2026-10-17 webfetch |
| 4 | `.claude/mcp.json` · `.claude/settings.json` · `.opencode/opencode.json` | gerçek sunucu tanımları (§3-§10) | 2026-10-17 Read |
| 5 | `.ai/log.md` L81 · `tools/tools-ornekler.md` L475 · `.claude/CONTEXT.md` L32/L146/L179 | vault çapraz kanıt | 2026-10-17 grep |

Uydurma URL **yok**; erişilemeyen kaynak `⚠️ VERIFICATION REQUIRED` ile işaretlenir (§29).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
