---
node_id: tools-anayasa
node_type: module
status: active
parent_id: tools
children: []
depends_on: [tools-index]
evidence:
  - "shared/src/AI/ToolCalling.php (AGENT_PERMISSIONS, kod kanıtı)"
  - "shared/src/AI/AIOrchestrator.php (AGENT_MAP/TASK_TYPES)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı"
open_questions: []
group: mimari
---

# tools-anayasa - Ajan Araç Kuralları

> **Bu dosya ne değildir:** ajan araçlarının **kod implementasyonunu** (`ToolCalling` iç metotları) ve MCP
> sunucu envanterini (`K015-AI/mcps/`) taşımaz; burada **araç anayasası** vardır — ajan bir aracı ne zaman,
> hangi izinle, hangi sözleşmeye uyarak çağırır, arızada ne döner, sınır nerede biter.

## §1 Rol (ELI15)

Araç (tool), ajanın **çğırabildiği tek kapıdır**: registry'de kayıtlı, kategorisi olan, izin matrisinden
geçen ve sonucu `success/result/error/executionTime` dörtlüsüne sarılan bir callable. Araç bir **servis
değildir** (iş kuralı taşımaz), bir **hook değildir** (önce/sonra kapıları uygulamaz).

| Soru | Cevap | Kanıt |
|------|-------|-------|
| Araçlar nerede tanımlı? | `ToolCalling` registry — 6 kategori, 5 varsayılan araç | `Dosya: shared/src/AI/ToolCalling.php · Satır: 173-284` |
| Kim izin verir? | `AGENT_PERMISSIONS` sabiti — **kategori** bazlı, araç bazlı değil | `Dosya: shared/src/AI/ToolCalling.php · Satır: 28-40` · `145-166` |
| Ajan sayısı? | 11 ajan — izin tablosu ile `AGENT_MAP` aynı anahtar kümesi | `Dosya: shared/src/AI/AIOrchestrator.php · Satır: 26-38` |
| Görev tipi? | 8 `TASK_TYPES`, her biri ajan + priority atar | `Dosya: shared/src/AI/AIOrchestrator.php · Satır: 40-49` |
| Arıza biçimi? | Exception **fırlatılmaz** — `success:false` + `error` string döner | `Dosya: shared/src/AI/ToolCalling.php · Satır: 101-110` |
| Kategori sayısı? | file · database · api · audio · hardware · security = **6** | `Dosya: shared/src/AI/ToolCalling.php · Satır: 18-24` |

> **eli10:** Araç = ajanın izinli aleti; çantada yoksa veya izin yoksa çağrımaz.
> **eli15:** İzin (kim), sözleşme (nasıl) ve arıza (neye döner) ayrı belgelerdedir — tek yerde toplanınca biri değişince öteki eski kalır.

## §2 Kapsam (in / out) — araç ≠ servis ≠ hook

| Kavram | Ne yapar | Bu anayasada | Kanıt |
|--------|----------|--------------|-------|
| **Araç (tool)** | Ajan çağrısı — kategori + izin + 4 alanlı sonuç | ✅ KAPSAM | `ToolCalling.php · Satır: 42-43` (registry şeması) |
| **Servis** | İş kuralı + health tanımı — ajan çağırmaz, katman çağırır | ❌ KAPSAM DIŞI | `shared/AGENTS.md §2` (`src/Api/Registry/` ServiceRegistry) |
| **Hook** | Öncesi/sonrası gate — kararı **insan** verir, ajan değil | ❌ KAPSAM DIŞI (yalnız karşılaştırma §14) | `Dosya: .claude/hooks/ai-risk-gate.cjs · Satır: 3` |
| **MCP sunucusu** | Uzak araç sağlayıcı — envanter ayrı alt-dal | ❌ (referans) | `.ai/architecture/K015-AI/mcps/index.md:23` |
| **Subagent** | Rol tanımı (`Agent` tool çağırır) — araç **değil** | ❌ | `.claude/AGENTS.md §3.1` (`agent/` satırı) |
| Öneri/LLM uygulaması | Kod tarafı iş akışı | ❌ ADR-030 kapsamı | `ADR-030` (rag-anayasa §8-§10) |

**Sınır cümlesi:** bu dosya yalnızca *ajan → araç → sonuç* üçgenini yazar; araç içinden servis, veritabanı
veya hook çağrısı **bu anayasanın konusu değildir** ve §13'teki sınır kuralına tabidir.

## §3 AGENT_PERMISSIONS izin matrisi (kod kanıtı)

```php
// Dosya: shared/src/AI/ToolCalling.php · Satır: 28-40
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

| Kategori | Kapsam | Sahip olmayan ajanlar (özet) |
|----------|--------|------------------------------|
| `file` | dosya oku/yaz/ara | **yok** — 11/11 ajanın izni var |
| `database` | SELECT sorgu | ui · embedded · audio-hw · dsp-fw · win-sw |
| `api` | HTTP/WS çağrısı | data · embedded · audio-hw · dsp-fw · win-sw |
| `audio` · `hardware` | ses + donanım erişimi | backend · ui · security · data · qa · devops (win-sw yalnız hardware) |
| `security` | güvenlik işlemi | **yalnız** `mo` ve `security` |

## §4 İzin Matrisi Okuma Kuralları (ajana göre)

| # | Ajan | İzinli kategori | Kategori | Kanıt |
|---|------|-----------------|----------|-------|
| 1 | `mo` | file · database · api · audio · hardware · security | 6/6 (tam) | `ToolCalling.php:29` |
| 2 | `security` | file · database · api · security | 4 | `ToolCalling.php:32` |
| 3 | `backend` · `qa` · `devops` | file · database · api | 3 | `ToolCalling.php:30,35,36` |
| 4 | `embedded` · `audio-hw` · `dsp-fw` | file · hardware · audio | 3 | `ToolCalling.php:34,37,38` |
| 5 | `ui` | file · api | **2 (en dar)** | `ToolCalling.php:31` |
| 6 | `data` | file · database | 2 | `ToolCalling.php:33` |
| 7 | `win-sw` | file · hardware | 2 | `ToolCalling.php:39` |

**Okuma kuralı:** izin **kategori** üzerinden verilir, tekil araç adı üzerinden değil — bir ajanın `file`
izni varsa `file-write` dahil tüm `file-*` araçlarını çağırabilir. Yeni araç eklenirken izin matrisi
**değiştirilmez**; araç bir kategoriye atanır ve o kategorinin izinleri devreye girer (`Satır: 64, 289-299`).

## §5 Araç Sözleşmesi — Girdi (zorunlu/tip)

```php
// Dosya: shared/src/AI/ToolCalling.php · Satır: 310-321 (bloklar arası ... ile kısaltıldı)
if ($def['required'] && !array_key_exists($def['name'], $params)) {
    throw new \InvalidArgumentException("Missing required parameter: {$def['name']}");
}
// ...
$type = get_debug_type($params[$def['name']]);
if ($type !== $def['type'] && $def['type'] !== 'mixed') {
    throw new \InvalidArgumentException(
        "Parameter '{$def['name']}' expects type '{$def['type']}', got '{$type}'"
    );
}
```

| Kural | Değer | Sonuç |
|-------|-------|-------|
| Zorunlu parametre yoksa | `Missing required parameter: X` | `success:false` (çağrılmaz) |
| Tip uyuşmazsa | `get_debug_type` ≠ tanımlı tip | `success:false` |
| `type: mixed` | tek istisna | tip kontrolü atlanır |
| Tanımsız (fazla) parametre | kontrol **YOK** | ⚠️ sessizce reddedilmez — §23 açık sorusu |

## §6 Araç Sözleşmesi — Çıktı ve Hata (4 alanlı sarmalayıcı)

```php
// Dosya: shared/src/AI/ToolCalling.php · Satır: 95-100 (başarı)
return [
    'success'       => true,
    'result'        => $result,
    'error'         => null,
    'executionTime' => round($executionTime, 4),
];
// Satır: 104-109 (arıza) — aynı şema, yalnız değerler değişir:
//   'success' => false · 'result' => null · 'error' => $e->getMessage()
```

| Alan | Başarıda | Arızada | Kural |
|------|----------|---------|-------|
| `success` | `true` | `false` | boolean — tek güvenilir dal ayracı |
| `result` | veri | `null` | `false` iken asla veri taşınmaz (fail-safe) |
| `error` | `null` | exception mesajı | düz string; istif/NASIL yığın **dönmez** |
| `executionTime` | ölçülen | ölçülen | `round(,4)` — §9 timeout metriğinin ham girdisi |

## §7 Yasak Yüzeyler (araçtan erişilemez sayılanlar)

| # | Yasak yüzey | Gerekçe | Kanıt |
|---|-------------|---------|-------|
| 1 | `git commit` — hiçbir araçta yok | commit kararı orkestratöre aittir | kök `AGENTS.md §7` adım 9 · `.claude/AGENTS.md §3.3 #6` |
| 2 | Secret değeri (`EXA_API_KEY`, `FIGMA_TOKEN`) | `.md`/`.json`/`.log`'a yazılmaz; REDACTED | `.claude/AGENTS.md §4 #4` · `.ai/AGENTS.md §13.9 #3` |
| 3 | `db-query` → SELECT dışı | `Only SELECT queries are allowed` | `ToolCalling.php · Satır: 234-237` |
| 4 | `shared/src/**` yazımı (bu üretimde) | salt-okunur kaynak kanıtı | görev yasağı (talimat) |
| 5 | Kapsam dışı dosya (bu dosya harici) | tek-dosya üretimi | görev yasağı (talimat) |
| 6 | `settings.json` / MCP izin genişletme | kullanıcı onayı şart | `.claude/AGENTS.md §3.3 #2` |
| 7 | Otorite dosyaları (`CLAUDE.md`, `AGENTS.md`, `WORKFLOW.md`, `.rules/**`) | İNSAN onayı (gate `ask`) | `Dosya: .claude/hooks/ai-risk-gate.cjs · Satır: 14-17` |
| 8 | Uydurma kanıt/URL/satır | Zero-Hallucination | `.ai/AGENTS.md §3` |

## §8 Arıza Davranışı — Timeout

| # | Katman | Değer | Davranış | Kanıt |
|---|--------|-------|----------|-------|
| 1 | Araç içi HTTP | `CURLOPT_TIMEOUT => 30` | curl 30sn'de keser → `RuntimeException` → `success:false` | `ToolCalling.php · Satır: 260` |
| 2 | Görev önceliği | CRITICAL 5s/1 · HIGH 15s/3 · MEDIUM 30s/3 · LOW 60s/2 | öncelik arttıkça süre ve retry artar | `.ai/AGENTS.md §8` |
| 3 | Ajan timeout (30s+) | Max 3 retry → **queue reset** | sonra escalation | `.ai/AGENTS.md §17 #4` |
| 4 | Kilit süresi | max 30sn | deadlock → MO en eski kiliti kırar | `.ai/AGENTS.md §12.1` |

**Kural:** timeout **arıza sayılır, başarı değil** — sonuç `success:false` olarak döner; ajan aynı aracı
**en fazla 3 kez** yeniden dener, 3. başarısızlıkta escalation (`§10`) tetiklenir; sonsuz döngü yasaktır.

## §9 Arıza Davranışı — Bulunamadı (404 sınıfı)

```php
// Dosya: shared/src/AI/ToolCalling.php · Satır: 75-81 (kısaltıldı)
if (!isset($this->tools[$toolName])) {
    return ['success' => false, 'result'        => null,
            'error'   => "Tool not found: {$toolName}",
            'executionTime' => 0.0];   // çalışmadı: süre 0.0
}
```

| Durum | Dönen | Ek davranış | Kanıt |
|-------|-------|-------------|-------|
| Araç adı kayıtlı değil | `Tool not found: X` | `executionTime = 0.0` (çalışmadı) | `ToolCalling.php:75-82` |
| İzin sorgusunda araç yok | `allowed:false` + `Tool not found` | fail-safe: **önce** araç, sonra ajan | `ToolCalling.php:147-149` |
| Bilinmeyen ajan | `Unknown agent: X` | `allowed:false` | `ToolCalling.php:151-153` |
| Bilinmeyen görev tipi / olmayan görev ID | **exception** | dispatch + `getTaskStatus` | `AIOrchestrator.php:73-75` · `104-106` |
| Bilinmeyen ajan (routing) | `success:false` + `Unknown agent` | exception değil, Result benzeri | `AIOrchestrator.php:127-133` |

## §10 Arıza Davranışı — Yetki Red

```php
// Dosya: shared/src/AI/ToolCalling.php · Satır: 158-165 (öncesindeki atamalar kısaltıldı)
if (!in_array($toolCategory, $allowedCategories, true)) {
    return [
        'allowed' => false,
        'reason'  => "Agent '{$agentId}' not allowed to use category '{$toolCategory}' tools",
    ];
}
return ['allowed' => true, 'reason' => null];
```

| # | Red biçimi | Yanıt şeması | Ajan ne yapar |
|---|-----------|--------------|---------------|
| 1 | kategori izni yok | `allowed:false` + `reason` | görevi ilgili ajana **handover** eder (§19) |
| 2 | bilinmeyen ajan | `allowed:false` + `Unknown agent` | MO'ya eskalasyon |
| 3 | araç kayıtlı değil | `allowed:false` + `Tool not found` | uyduruk ad kullanmaz, listeye döner |
| 4 | izin verildi | `allowed: true, reason: null` | yalnız o zaman `callTool` |

**İhlal sonucu:** yetki reddi **sessiz geçilemez** — `in_array(..., true)` strict'tir; kategori adı kaydırılmışsa (ör. `DB` ≠ `database`) sonuç red'tir; nedeni `log.md`'ye yazılır.

## §11 Araç → Servis Çağrısı Sınırı

| # | Kural | Doğru | Yanlış | Kanıt |
|---|-------|-------|--------|-------|
| 1 | Araç yalnız **kategori** işi yapar | `db-query` → SELECT | `db-query` → servis iş mantığı | `ToolCalling.php:227` (tek cümlelik description) |
| 2 | Araç **sözleşme** döndürür, nesne değil | `['result'=>..,'rows'=>..]` | domain entity çıkışı | `ToolCalling.php:238` |
| 3 | Servis katmanı ajanı **çağırmaz** | ajan → araç → (gerekirse) katman | servis → ajan zinciri | `shared/AGENTS.md §2` (Registry/sağlık ayrı) |
| 4 | Araç içinde katman ihlali | — | `layer violation → revert + log ERROR` | `.ai/AGENTS.md §17 #7` · `§18 #6` |
| 5 | ORM / `SELECT *` | prepared + dar sorgu | ORM, `SELECT *` | `.ai/AGENTS.md §18 #7` · §16 Data |
| 6 | Çıktı şeması sabittir | 4 alanlı sarmalayıcı | serbest dizi | `ToolCalling.php:42` (docblock) |

**Sınır cümlesi:** araç bir **belirleyici değildir** (decider) — iş kuralına karar vermez, yalnız
tanımlı girdiyi alır ve tanımlı kategorinin işini yapar; karar ajanın, izin matrisin, gate insandır.

## §12 Hook ile Fark (.claude/hooks/*.cjs gate'leri)

| # | Boyut | Araç (tool) | Hook (.cjs) |
|---|-------|-------------|-------------|
| 1 | Tetikleyici | ajan çağrısı (`callTool`) | olay (PreToolUse / PostToolUse / UserPromptSubmit) |
| 2 | Karar sahibi | izin matrisi (kod) | gate → **insan** (`permissionDecision: "ask"`) |
| 3 | Örnek | `file-write` → `success` döner | `ai-risk-gate.cjs` → T1/T2 otorite dosyasında `ask` |
| 4 | Emniyet | exception → `success:false` | **her koşulda exit 0** (prompt bloklanmaz) |
| 5 | Dosyalar | `shared/src/AI/ToolCalling.php` | `ai-risk-gate.cjs` · `ai-validate-post.cjs` · `skill-mandate.cjs` |
| 6 | Post-kapı | yok | `.ai/**/*.md` → `validate.mjs --check`, ihlalde `{"decision":"block"}` |
| 7 | Kanıt | `Satır: 28-40, 145-166` | `ai-risk-gate.cjs:3-9` · `ai-validate-post.cjs:3-11` · `skill-mandate.cjs:3-12` |

**Ayırım:** hook kapısı **insanı** durdurur, ajanı değil; araç izni **ajanı** durdurur, insanı değil.
İkisi üst üste bindiğinde önce hook (`ask`) çalışır — araç izni olsa bile yazım onaysız ilerlemez.

## §13 MCP İlişkisi (mcps/ alt-dalına referans)

| # | Konu | Bu dosya | Ayrı dosya |
|---|------|----------|-----------|
| 1 | Sunucu envanteri / sürüm / lisans | ❌ taşımaz | `K015-AI/mcps/index.md:23` (MCP yapılandırması) |
| 2 | Açık soru: `.claude/mcp.json` içeriği | ❌ | `mcps/index.md:13` → `⚠️ VERIFICATION REQUIRED` |
| 3 | Araç sözleşmesi (name/description/inputSchema) | ✅ §5-§6 | — |
| 4 | Uzak araç erişim izni | ✅ kategori matrisi §3-§4 | sunucu bağlanması `mcps/` |
| 5 | `tools/list_changed` bildirimi | ⚠️ yalnız web kaynağı §17 | uygulama durumu `UNKNOWN` |
| 6 | İlişki yönü | `tools-anayasa` → `mcps` (referans) | ters yön yok |

**Kural:** MCP sunucusu **araç sağlayıcıdır**, izin matrisi **değildir** — uzak sunucudan gelen araç da
§3 matrisinden geçmeden ajanın eline ulaşmaz; sunucu listesi burada **uydurulmaz** (`mcps/index.md:13`).

## §14 Güvenlik — Fail-safe, Redaction, Denetim

| # | Kural | Uygulama | Kanıt |
|---|-------|----------|-------|
| 1 | Fail-**safe** (fail-open değil) | araç yoksa/izinsizse `allowed:false` | `ToolCalling.php:147-163` |
| 2 | Sonuç → LLM öncesi doğrulama | `result` doğrulanmadan bağlama girmez | MCP spec (§17 "Validate tool results before passing to LLM") |
| 3 | Hassas veri redaction | log'da `[REDACTED]` maskesi | `.ai/AGENTS.md §17 #3` |
| 4 | Secret hiç yazılmaz | `.env`/`.md`/`.json`/`.log` dışı | `.claude/AGENTS.md §3.3 #1` |
| 5 | Girdi doğrulama | `validateParameters` her çağrıda | `ToolCalling.php:89` |
| 6 | SQL kısıtı | yalnız `SELECT` başlangıcı | `ToolCalling.php:234-237` |
| 7 | Zaman aşımı | 30sn curl timeout | `ToolCalling.php:260` |
| 8 | Denetim izi | handover/lock/red `log.md`'ye yazılır | `.ai/AGENTS.md §9.2 · §12.1` |

**`listTools` sızıntısı yok:** liste yalnız `name/description/parameters/category` döndürür — `executor` dışarı kapatılır (`ToolCalling.php:118-126`).

## §15 Web Doğrulama #1 — MCP Tools Spec (sözleşme)

| # | İddia (upstream) | Kaynak |
|---|------------------|--------|
| 1 | Araç `name` + `description` + `inputSchema` (JSON Schema) taşır | `modelcontextprotocol.io/specification/2025-06-18/server/tools` |
| 2 | `tools/list` keşif · `tools/call` çağrı; sonuç `content[]` + `isError` | aynı kaynak (Protocol Messages) |
| 3 | İki hata sınıfı: **protocol** (unknown tool / invalid args) ve **execution** (`isError:true`) | aynı kaynak (Error Handling) |
| 4 | `outputSchema` varsa sunucu buna uymak **MUST**, istemci doğrulamak **SHOULD** | aynı kaynak (Output Schema) |
| 5 | İstemci tarafı: onay iste, girdiyi göster, **timeout** uygula, kullanım log'la | aynı kaynak (Security Considerations) |
| 6 | Güvenilir model-kontrollü araç + **human in the loop** (reddetme hakkı) | aynı kaynak (User Interaction Model) |

**Repo ile hizalama:** CoreMusic'te hata ayrımı `success` boolean ile simgelenir (§6) — upstream'in `isError` kavramının karşılığı; iki-sınıf ayrımı `Satır: 75-82` ↔ `101-110` ile aynıdır.

## §16 Web Doğrulama #2 — Anthropic Tool Use (çalışma yeri)

| # | İddia (upstream) | Kaynak |
|---|------------------|--------|
| 1 | **Client tool** sizin uygulamanızda çalışır; **server tool** sağlayıcı altyapısında | `platform.claude.com/docs/en/agents-and-tools/tool-use/overview` |
| 2 | Tur: `tool_use` bloğu → uygulama çalıştırır → `tool_result` geri gönderilir | aynı kaynak (How tool use works) |
| 3 | `tool_choice: auto` modelin kararına bırakılır; `any`/`tool` ile çağrı **zorlanır** | aynı kaynak |
| 4 | `strict: true` → çağrılar şemaya **tam** uyar (schema conformance) | aynı kaynak (Strict tool use) |
| 5 | Zorunlu parametre eksikse model **sorabilir ya da uydurabilir** | aynı kaynak (Accordion: missing parameters) |
| 6 | Fiyatlandırma: `tools` parametresi de input token'a sayılır | aynı kaynak (Pricing) |

**Repo ile hizalama:** CoreMusic'te `tool_choice` seviyesi **yoktur** — ajan çağırıp çağırmayacağını `.ai/AGENTS.md §6` keyword routing ile belirler; şema sıkılığı `validateParameters` ile sağlanır (`ToolCalling.php:314-323`).

## §17 Web Doğrulama #3 — MCP Security Best Practices (izin minimizasyonu)

| # | İddia (upstream) | Kaynak |
|---|------------------|--------|
| 1 | **Scope minimization**: en az yetki ile başla, kademeli yükselt | `modelcontextprotocol.io/specification/2025-06-18/basic/security_best_practices` |
| 2 | Wildcard/omnibus scope (`*`, `all`, `full-access`) ortak hatadır | aynı kaynak (Common Mistakes) |
| 3 | URL/şema doğrulaması **allowlist** ile yapılır, blocklist ile değil | aynı kaynak (OAuth Authorization URL Validation) |
| 4 | SSRF: özel IP aralıkları engellenir (`10/8`, `172.16/12`, `192.168/16`, `169.254/16) | aynı kaynak (SSRF Mitigation) |
| 5 | Yerel MCP sunucusu için **consent** + sandbox şart | aynı kaynak (Local MCP Server Compromise) |
| 6 | Token passthrough **yasak** — sunucu kendi token'ını doğrular | aynı kaynak (Token Passthrough) |

**Repo ile hizalama:** `AGENT_PERMISSIONS` bu belgenin scope minimizasyonudur — `mo` hariç hiçbir ajana `security` kategorisi **verilmez**, wildcard izin **yoktur** (`ToolCalling.php:29-39`).

## §18 AIOrchestrator İlişkisi — AGENT_MAP / TASK_TYPES

| # | Sabit | İçerik | Araçla ilişkisi | Kanıt |
|---|-------|--------|-----------------|-------|
| 1 | `AGENT_MAP` | 11 ajan → `domain` + `layer` | aynı 11 anahtar §3 matrisinde | `AIOrchestrator.php:26-38` |
| 2 | `TASK_TYPES` | 8 tip → `agent` + `priority` | tip, **önce** ajanı belirler, sonra izin | `AIOrchestrator.php:40-49` |
| 3 | `fault` | `dsp-fw` · priority **critical** | `dsp-fw` = file/hardware/audio | `AIOrchestrator.php:44` |
| 4 | `security-audit` | `security` · priority **critical** | `security` = 4 kategori | `AIOrchestrator.php:46` |
| 5 | `documentation` | `mo` · priority low | `mo` tek tam yetkili ajan | `AIOrchestrator.php:47` |
| 6 | `testing` | `qa` · priority medium | `qa` = file/database/api | `AIOrchestrator.php:48` |
| 7 | Domain boundary kontrolü | `routeToAgent` domain+layer döner | yorum satırı var, **uygulama yok** | `AIOrchestrator.php:135-147` |

**Çelişki kuralı:** görev tipi ajanı atar, **izin matrisi** ajanın neye dokunabileceğini belirler — ikisi
çelişirse (tip `mo` dese de) araç çağrımından önce `checkPermission` kazanır (`Satır: 145-166`).

## §19 Domain Boundaries + Keyword Routing Çakışması

| # | Kaynak | Kural | İlişki |
|---|--------|-------|--------|
| 1 | `.ai/AGENTS.md §5` | 12 dosya tipi sahipliği (`*.php`→Backend, `*.sql`→Data, `.env`→Security …) | dosya **sahipliği** |
| 2 | `.ai/AGENTS.md §6` | 9 keyword grubu → birincil/ikincil ajan | görev **yönlendirmesi** |
| 3 | `ToolCalling.php §3` | 6 kategori → izin | araç **çağrım yetkisi** |
| 4 | §5 + §6 | görevi doğru ajana atar | kimin işi olduğu |
| 5 | §3 (bu dosya) | o ajanın hangi aracı çağırabileceği | kimin **yapabileceği** |
| 6 | Çakışma | sahiplik ≠ yetki: `ui` `*.php` sahibi değil, `file` izni var | §5 kazanmaz, izin matrisi kazanır |

**Sonuç:** üç tablo farklı soruları yanıtlar ve birbirinin yerine **geçmez** — routing görevi ayırır,
sahiplik dosyayı ayırır, izin matrisi aracı ayırır; çelişik durumda en dar olan (izin matrisi) uygulanır.

## §20 Doğrulama Kapıları

| # | Kapı | Kriter | Yöntem |
|---|------|--------|--------|
| 1 | Frontmatter | 10 alan, `title`/`parent` anahtarı **yok** | gözden geçirme |
| 2 | Kod künyesi | Her PHP alıntısı `Dosya:` + `Satır:` taşır | gözden geçirme |
| 3 | İzin matrisi | 11 ajan × satır, kod ile birebir | `ToolCalling.php:28-40` okuma |
| 4 | İllüstratif blok | Her blok üstünde ⚠️ etiketi | grep `İllüstratif örnek - repo kanıtı DEĞİL` |
| 5 | Web kaynakları | 3 URL erişilebilir + içerikli | webfetch (2026-10-17) |
| 6 | Uydurma URL | Yok — yalnız erişilen 3 adres | Zero-Hallucination |
| 7 | Satır bandı | 500-520 (`ReadAllLines`, UTF8) | PowerShell ölçüm |
| 8 | Authority | Son satır birebir | gözden geçirme |
| 9 | Placeholder | Dosyada şablon değişkeni (çift süslü parantez deseni) yok | grep |
| 10 | Yasaklar | commit yok · `shared/src/**` yazımı yok | git status (yalnız bu dosya) |

## §21 Açık Sorular

| # | Soru | Öncelik | Durum / Kanıt |
|---|------|---------|----------------|
| 1 | `tools/index.md` kanıt bekliyor (`evidence: disk kanıtı TOPLANMADI`) → bu dosya o boşluğu dolduruyor mu? | HIGH | ✅ KAPANDI — `tools/index.md:10,14` karşılanıyor (§3-§10 kanıtla) |
| 2 | Fazla/ tanımsız parametre neden reddedilmiyor? | HIGH | `⚠️ VERIFICATION REQUIRED` — kontrol yok (`ToolCalling.php:307-325`) |
| 3 | `inferCategory` default'u `file` → etiketlenmemiş araç `file` kategorisine mi düşer? | HIGH | `⚠️ VERIFICATION REQUIRED` (`ToolCalling.php:298`) |
| 4 | `routeToAgent` domain boundary yorumu ne zaman uygulanacak? | MEDIUM | `⚠️ VERIFICATION REQUIRED` (`AIOrchestrator.php:135`) |
| 5 | Araçların gerçek çağrı sayısı / hata oranı ölçülüyor mu? | MEDIUM | `UNKNOWN` — metrik kodu yok |
| 6 | `AGENT_PERMISSIONS` ile `.ai/AGENTS.md §5` sahipliği senkronu kimde? | LOW | `UNKNOWN` — otomatik eşitleme yok |

## §22 Kaynaklar

| # | Kaynak | Yol / Adres | Kullanım |
|---|--------|-------------|----------|
| 1 | Araç registry + izin matrisi (kod SSOT) | `shared/src/AI/ToolCalling.php` (326 satır) | §3-§11, §14 |
| 2 | Görev/ajan dağıtımı (kod) | `shared/src/AI/AIOrchestrator.php` (210 satır) | §18 |
| 3 | Domain boundaries + routing | `.ai/AGENTS.md` §5 (12 tip) · §6 (9 grup) | §19 |
| 4 | Alt-dümğüm künyesi | `.ai/architecture/K015-AI/tools/index.md` (48 satır) | §1, §21 |
| 5 | Hook gate'leri | `.claude/hooks/ai-risk-gate.cjs` · `ai-validate-post.cjs` · `skill-mandate.cjs` | §12 |
| 6 | MCP alt-dalı | `.ai/architecture/K015-AI/mcps/index.md` | §13 |
| 7 | Anayasa şablonu (yapı referansı) | `.ai/architecture/K015-AI/rag/rag-anayasa.md` | yapı: H1 + § + tablo |
| 8 | Upstream: MCP Tools spec | `https://modelcontextprotocol.io/specification/2025-06-18/server/tools` | §15 |
| 9 | Upstream: Anthropic tool use | `https://platform.claude.com/docs/en/agents-and-tools/tool-use/overview` | §16 |
| 10 | Upstream: MCP security best practices | `https://modelcontextprotocol.io/specification/2025-06-18/basic/security_best_practices` | §17 |

## §23 Hızlı Referans (tek ekran)

| # | Soru | Cevap |
|---|------|-------|
| 1 | Ajan X hangi aracı çağırır? | kategori matrisi §3-§4 → `checkPermission` (`Satır: 145-166`) |
| 2 | Araç çöktü, ne görürüm? | `success:false` + `error` + `executionTime` (exception dönmez) |
| 3 | Yetkim yoksa ne yaparım? | handover — nedeni `reason` string'inde |
| 4 | Timeout kaç? | araç içi 30s (`Satır: 260`) · görev önceliğine göre 5/15/30/60s |
| 5 | 3 retry sonrası? | queue reset + escalation (`.ai/AGENTS.md §17 #4`) |
| 6 | Servis çağırabilir miyim? | Hayır — §11 sınırı; araç kategori işi yapar |
| 7 | Commit atabilir miyim? | Hayır — hiçbir araçta yok (§7 #1) |

## §24 İllüstratif Örnek — Python (tool registry + allowlist)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# kategori allowlist denetimi — upstream docs referansı; repo karşılığı PHP matrisidir
AGENT_PERMISSIONS = {                    # PHP const ile aynı veri (ToolCalling.php:28-40)
    "mo": {"file", "database", "api", "audio", "hardware", "security"},
    "ui": {"file", "api"},               # en dar — Satır: 31
}
def check_permission(tool, agent):
    allowed = AGENT_PERMISSIONS.get(agent)
    if allowed is None:
        return False, f"Unknown agent: {agent}"        # fail-safe: önce ajan
    if tool.category not in allowed:
        return False, f"Agent '{agent}' not allowed: '{tool.category}'"
    return True, None
```

## §25 İllüstratif Örnek — TypeScript (JSON schema doğrulama)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```ts
// girdi şeması doğrulama — MCP inputSchema kavramı (upstream docs); repo karşılığı PHP
type ParamDef = { name: string; type: "string" | "number" | "mixed"; required: boolean };

export function validateParameters(defs: ParamDef[], params: Record<string, unknown>): void {
  for (const def of defs) {
    if (def.required && !(def.name in params))
      throw new Error(`Missing required parameter: ${def.name}`);
    const value = params[def.name];
    if (value !== undefined && def.type !== "mixed" && typeof value !== def.type)
      throw new Error(`Parameter '${def.name}' expects '${def.type}', got '${typeof value}'`);
  }
}
```

## §26 İllüstratif Örnek — Go (izin allowlist filtresi)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
// kategori allowlist filtresi — upstream docs referansı; PHP matrisiyle aynı mantık
package perms

var matrix = map[string][]string{
    "mo":     {"file", "database", "api", "audio", "hardware", "security"}, // Satır: 29
    "win-sw": {"file", "hardware"},                                         // Satır: 39
}
func Check(agent, category string) (bool, string) {
    cats, ok := matrix[agent]
    if !ok { return false, "Unknown agent: " + agent }  // fail-safe: önce ajan
    for _, c := range cats { if c == category { return true, "" } }
    return false, "category '" + category + "' not allowed"
}
```

## §27 İllüstratif Örnek — Rust (arıza tipi: Result<E>)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```rust
// araç çağrısı hata tipi — upstream docs referansı; repo'da exception yerine 4 alanlı dizi var
pub enum ToolError { NotFound(String), UnknownAgent(String), InvalidParam(String) }
pub struct Outcome<T> { pub success: bool, pub result: Option<T>,
                        pub error: Option<String>, pub execution_time: f64 }
pub fn call<T, F>(exec: F) -> Outcome<T> where F: FnOnce() -> Result<T, ToolError> {
    let t0 = std::time::Instant::now();
    match exec() {
        Ok(v)  => Outcome { success: true,  result: Some(v), error: None,
                            execution_time: t0.elapsed().as_secs_f64() },
        Err(e) => Outcome { success: false, result: None, error: Some(e.to_string()),
                            execution_time: t0.elapsed().as_secs_f64() },
    }
}
```

## §28 İllüstratif Örnek — C++ (timeout ve ölçüm)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```cpp
// araç çağrısı timeout + süre ölçümü — upstream docs referansı; repo'da curl 30s (Satır: 260)
#include <chrono>
#include <string>
struct Outcome { bool success; std::string result; std::string error; double execution_time; };
template <class F>
Outcome call_guarded(F&& fn, int timeout_ms = 30000) {
    const auto t0 = std::chrono::steady_clock::now();
    try {
        if (timeout_ms <= 0) throw std::runtime_error("timeout budget exhausted");
        return {true, fn(), {}, seconds_since(t0)};
    } catch (const std::exception& e) { return {false, {}, e.what(), seconds_since(t0)}; }
}
```

## §29 İllüstratif Örnek — C# (izin matrisi sözlüğü)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```csharp
// kategori izin sözlüğü — upstream docs referansı; PHP const ile aynı veri (Satır: 28-40)
using System.Collections.Generic; using System.Linq;
public static class AgentPermissions
{
    private static readonly Dictionary<string, string[]> Matrix = new()
    {
        ["mo"]     = new[] { "file", "database", "api", "audio", "hardware", "security" },
        ["win-sw"] = new[] { "file", "hardware" },   // Satır: 39
    };
    public static (bool ok, string? reason) Check(string agentId, string category)
    {
        if (!Matrix.TryGetValue(agentId, out var cats)) return (false, $"Unknown agent: {agentId}");
        return cats.Contains(category) ? (true, null) : (false, $"not allowed: {category}");
    }
}
```

## §30 Ölçüm / Gate Notu

| # | Eşik | Araç | Not |
|---|------|------|-----|
| 1 | ≥500 tam satır | `[IO.File]::ReadAllLines(..., UTF8).Count` | 500-520 bandı |
| 2 | FM 10 alan | gözden geçirme | `title`/`parent` anahtarı YOK |
| 3 | Authority son satır | gözden geçirme | birebir |
| 4 | PHP kanıt bloğu | 5 blok — her biri `Dosya:`/`Satır:` künyeli | repo arşivi (§3, §5, §6, §9, §10) |
| 5 | İllüstratif blok | 6 dil (Python · TS · Go · Rust · C++ · C#) | ⚠️ etiketi zorunlu |
| 6 | Web kaynağı | 3 resmi adres | 2026-10-17 erişildi |
| 7 | Ölçüm tarihi | 2026-10-17 üretim | sayı iddiaları okuma künyesi taşır |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
