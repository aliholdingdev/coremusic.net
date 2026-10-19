---
node_id: tools-surucu
node_type: module
status: active
parent_id: tools
children: []
depends_on: [tools-anayasa]
evidence:
  - "shared/src/AI/ToolCalling.php (izın kontrolü kod kanıtı)"
  - ".claude/hooks/*.cjs (3 gate dosyası, okundu 2026-10-17)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı"
open_questions: []
group: mimari
---

# tools-surucu - Araç Kullanım Sürucusu

> **Kapsam:** Bir görev geldiğinde ajanın **hangi aracı, hangi sırayla, hangi izin kontrolünden
> sonra** çağırdığı — pre-flight, hook gate'i, çağrı zarfı, red/timeout/retry davranışı, çağrı
> sonrası doğrulama ve hata modları. **Kural koyar değil, tarif eder:** bağlayıcı kurallar
> `Dosya: .ai/AGENTS.md · Satır: 246-312` (§7 Task Dispatch) ve `.claude/hooks/*.cjs` (3 gate)
> içindedir; bu dosya o kuralların **kullanım kılavuzudur**.
>
> **Ölçüm tarihi:** 2026-10-17 (tüm satır sayıları `[IO.File]::ReadAllLines(...UTF8).Count`
> ile bugün diskten ölçülmüştür — §15).

## §1. ELI15 — Bu sürücü ne işe yarar

Ajan bir görev aldığında elindeki aracı (tool) **doğrudan çağırmaz**. Sürücü önce görevin
keyword'üyle ajanı ve araç sınıfını belirler (§13), sonra **izin ön-kontrolü** yapar
(`ToolCalling::checkPermission` — §5): ajan o kategori yetkili mi, araç kayıtlı mı? Ardından
dosya-yazma gibi yüzeylerde **hook gate'i** devreye girer (§6): `ai-risk-gate` HIGH-risk dosyada
**insan onayı** ister, `ai-validate-post` yazım sonrası **validator ihlalinde bloklar**. Çağrı
`callTool` içinden geçer: parametre doğrulaması → executor → `{success, result, error,
executionTime}` zarfı (§17). Hiçbir kapı geçilmezse araç **hiç çalışmaz**; red, timeout ve hata
davranışları §8-§9 ve §12'de tanımlıdır.

> **eli10:** "Bir iş yapacak araç çağır" demek yetmez; önce *bu ajan bu araca yetkili mi* sorusu
> sorulur, sonra dosya yazarken *insan onayı mı gerekli* kapısı çalışır, en sonda *sonuç doğru
> mu* diye bakılır.
>
> **eli15:** Bugün bu zincirin **iki fiziksel katmanı** vardır: (1) PHP çekirdeği
> (`shared/src/AI/ToolCalling.php` — 326 satır, kayıt/izin/çağrı) ve (2) Claude Code hook
> katmanı (`.claude/hooks/*.cjs` — 3 dosya, prompt/ön-yazıma/son-yazıma kapıları). İkisi aynı
> görevde üst üste biner: hook, PHP çağrısından **bağımsız** olarak aracın *kullanılabilirliğini*
> yönetir; PHP ise aracın *yetki ve执行* tarafını. Köprü (PHP aracının hook'a bağlanması)
> bugün **kanıtsız** — §29/Q3.

## §2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Görev → araç seçimi 8 adımı (§4) + karar ağaçları (§5, §13) | Araçların **iş mantığı** (executor gövdesi kendi işidir) |
| Pre-flight + hook gate davranışları (§5-§7) | `.claude/settings.json` hook **kayıt** listesi (§29/Q4 — okunmadı) |
| Yetki red, timeout, retry, escalation (§8-§9) | Ajan routing tablosunun kendisi (`Dosya: .ai/AGENTS.md · §6`) |
| Çağrılık zinciri sınırı + çağrı sonrası doğrulama (§10-§11) | Servis/DB katmanının iç tasarımı |
| Hata modları, kapılar, ölçüm betikleri (§12, §15-§16, §25) | `git commit` (subagent yasak — §26) |

- **Kullananlar:** görev başına araç çağıran her ajan · MO (red/escalasyon kararı) · Vault Steward (denetim).
- **Ön koşul:** `ToolCalling` kayıt tablosu diskte var (`Dosya: shared/src/AI/ToolCalling.php · Satır: 42-48`).
- **Kanıt standardı:** her iddia `Dosya:` + `Satır:` künyelidir; künyesiz iddia yazılmaz (§29-§30).

## §3. Araç Kayıt Sistemi (Registry) — Kategori Nereden Gelir

| Katman | Ne yapar | Kanıt |
|---|---|---|
| Kayıt | `registerTool(name, description, parameters, executor)` — aynı ad **ikinci kez** kaydedilemez | `Dosya: shared/src/AI/ToolCalling.php · Satır: 53-57` |
| Kategori çıkarımı | `inferCategory()`: `file-` → file · `db-` → database · `http-` → api · `audio` → audio · `device\|hardware` → hardware · `auth\|security` → security · **fallback `file`** | `.../ToolCalling.php · Satır: 289-299` |
| Listeleme | `listTools()` (name/description/parameters/category) · `getToolsByCategory()` (daraltma) | `.../ToolCalling.php · Satır: 116-140` |
| Varsayılan envanter | `registerDefaultTools()`: `file-read` · `file-write` · `file-search` · `db-query` · `http-request` | `.../ToolCalling.php · Satır: 173-284` |
| İzin tablosu | `AGENT_PERMISSIONS` — 11 ajan × kategori kümesi (§5) | `.../ToolCalling.php · Satır: 28-40` |

**Sürücü açısından anlamı:** bir araç **önce kategoriye**, kategori **önce ajan yetkisine**
bağlıdır. Araç adı bilinmiyorsa kategori tahmini `file`'a düşer (Satır: 298) — yani **sessiz
"file" varsayımı** vardır; sürücüde bu, bilinen bir tuzka olarak §27/6'da kayıtlıdır.

## §4. Görev → Araç Seçimi Akışı (8 Adım)

```text
[0] Görev gelir → §7.1 Analiz: keyword çıkar, domain eşle, öncelik belirle (§7.1 · Satır: 259-266)
[1] Skill eşleşmesi kontrolü — eşleşen skill varsa İLK işlem o skill'i yüklemektir
      → skill-mandate hook'u prompt'a bu yasağı zaten enjekte etmiştir (§7 · Satır: 87-88)
[2] Pre-flight Checks (§7.2 · Satır: 268-281): domain boundary · context lock · mockup ·
      şablon (Guardrail #16) · gate script (kökten) · envanter · önceki görev retry
      → ihlal: DUR / RED / revert (satır bazlı §5)
[3] Araç sınıfı seçimi: görev tipi → kategori (§13 karar ağacı) + ajan yetkisi (§5)
[4] İzin ön-kontrolü: ToolCalling::checkPermission(toolName, agentId)
      → allowed=false → §8 (RED) — executor'a hiç uğramaz
[5] Hook gate'i (dosya yazımıysa): PreToolUse ai-risk-gate → "ask" (insan onayı)
      → onay yoksa araç çağrısı reddedilir (§6)
[6] Çağrı: callTool() → validateParameters() → executor → zarf {success, result, error, executionTime}
      (Dosya: shared/src/AI/ToolCalling.php · Satır: 73-111)
[7] Çağrı sonrası doğrulama: zarf success? + PostToolUse ai-validate-post (validator block)
      + §7.6 Verification (format/domain/cross-reference/redaction) → log.md (§7.7 · Satır: 307-312)
```

**Adım [1] kanıtı:** skill zorunluluğu hook'un stdout'una gömülüdür —
`'SKILL ZORUNLULUĞU (kök CLAUDE.md §Skill Usage Mandate): Görev bir skill ile eşleşiyorsa'` +
`'İLK işlem Skill tool ile yüklemektir; eşleşen skill'siz işlem başlatmak YASAKTIR.'`
(`Dosya: .claude/hooks/skill-mandate.cjs · Satır: 87-88`).

**Adım [2] kanıtı:** pre-flight tablosunun son satırı retry'ı bağlar —
`| Önceki görev başarısız mı? | Retry / escalation | Max 3 retry |` (`Dosya: .ai/AGENTS.md · Satır: 281`).

## §5. İzin Ön-Kontrol Karar Ağacı (Pre-flight + checkPermission)

| # | Durum (girdi) | Kontrol | Karar | Davranış / kaynak |
|---|---|---|---|---|
| 1 | Araç adı kayıt tablosunda yok | `isset($this->tools[$toolName])` | **RED** — `success:false, error:"Tool not found"` | `ToolCalling.php · Satır: 75-82` |
| 2 | Ajan id'si `AGENT_PERMISSIONS`'ta yok | `isset(self::AGENT_PERMISSIONS[$agentId])` | **RED** — `"Unknown agent: {$agentId}"` | `ToolCalling.php · Satır: 151-153` |
| 3 | Ajan var ama kategori yetkisiz | `in_array($toolCategory, $allowedCategories, true)` | **RED** — `"Agent '…' not allowed to use category '…' tools"` | `ToolCalling.php · Satır: 158-163` |
| 4 | Kategori yetkili | `['allowed' => true, 'reason' => null]` | **GEÇ** → §4 adım [5] | `ToolCalling.php · Satır: 165` |
| 5 | Zorunlu parametre eksik | `validateParameters()` required kontrolü | **RED** — `InvalidArgumentException` (çağrı executor'a ulaşmaz) | `ToolCalling.php · Satır: 309-312` |
| 6 | Parametre tipi uymuyor | `get_debug_type($value) !== $def['type']` (`mixed` hariç) | **RED** — tip mesajı | `ToolCalling.php · Satır: 314-322` |
| 7 | Domain boundary ihlali (pre-flight) | Doğru ajan mı | **Layer violation → revert** | `Dosya: .ai/AGENTS.md · Satır: 272` |
| 8 | UI işi, mockup okunmamış | mockup gate | **DUR** | `Dosya: .ai/AGENTS.md · Satır: 275` |
| 9 | UI işi, şablon okunmamış (Guardrail #16) | Kalıp A-D | **DUR** | `Dosya: .ai/AGENTS.md · Satır: 278` |
| 10 | Gate script `.ai/` içinden çalıştırılmış | kök kuralı | **geçersiz → kökten tekrar** | `Dosya: .ai/AGENTS.md · Satır: 279` |
| 11 | Eşzamanlı dosya erişimi | context lock | **kilit bekle → kuyruk** | `Dosya: .ai/AGENTS.md · Satır: 273` |
| 12 | Bağımlılık başka ajanın alanı | handover | **transfer başlat** (onay zorunlu) | `Dosya: .ai/AGENTS.md · Satır: 274, 346-352` |

**Karar sırası (değişmez):** araç → ajan → kategori → parametre → hook gate. Örneğin `ui` ajanı
`file` ve `api` kategorileriyle sınırlıdır (`ToolCalling.php · Satır: 31`); `db-query` çağırmak
3. satırda **red** alır ve executor'a hiç ulaşmaz.

## §6. Hook Gate Akışı (3 Kapı — Tetikleyici ve Davranış)

> **Ölçüm (2026-10-17):** `.claude/hooks/` altında **tam 3 `.cjs` dosyası** vardır
> (`Get-ChildItem *.cjs` → 3): `skill-mandate.cjs` (195 satır) · `ai-risk-gate.cjs` (73) ·
> `ai-validate-post.cjs` (44) — hepsi bugün okunmuştur.

| Gate | Event / tetikleyici | Matcher | Ne yapar | İhlalde |
|---|---|---|---|---|
| **G1** `skill-mandate.cjs` | **UserPromptSubmit** — prompt gönderildiği anda, Claude işlemadan önce | (prompt) | Proje + global skill listesini tarar, yasağı bağlam olarak enjekte eder | Prompt **bloklanmaz** — emniyet: `her koşulda exit 0` (`Satır: 12, 101`) |
| **G2** `ai-risk-gate.cjs` | **PreToolUse** — araç çağrısından ÖNCE | `Edit\|Write` | T1/T2 otorite dosyasında `permissionDecision: "ask"` — **insan onayı** | Onay yoksa çağrısı reddedilir (`Satır: 38-48, 60-72`) |
| **G3** `ai-validate-post.cjs` | **PostToolUse** — araç çağrısı BAŞARILI olduktan SONRA | `Edit\|Write` (yalnız `.ai/**/*.md`) | `validate.mjs --check` çalıştırır; exit 1 ise `{"decision":"block"}` | Düzeltmesi **zorunlu** blok (`Satır: 36-42`) |

**G2 HIGH-risk kümesi (gerçek):** kök `CLAUDE.md` · `AGENTS.md` · `WORKFLOW.md`
(`Satır: 36`) + `.ai/` altında `CLAUDE.md` · `AGENTS.md` · `WORKFLOW.md` · `ULTRA-THINKING.md`
(`Satır: 14-16`) + `.ai/.rules/**` (`Satır: 17`).

**G3 kapsam dışılık:** `if (!/^\.ai\/.+\.md$/i.test(rel)) process.exit(0);          // yalnız .ai .md`
(`Dosya: .claude/hooks/ai-validate-post.cjs · Satır: 31`) — `.ai/` dışı her şey **sessiz geçer**.

**Emniyet asimetrisi (neden böyle):** G1/G2 her koşulda `exit 0` döner (prompt/cağrı **yanlışlıkla**
kilitlenmesin); **yalnız** validator gerçekten ihlal bildirdiğinde (exit 1) blok gelir
(`ai-validate-post.cjs · Satır: 37-42` + başlık yorumu `Satır: 9-11`).

## §7. `skill-mandate` Tetikleyicisi (G1 Detayı)

| # | Durum | Davranış | Kanıt |
|---|---|---|---|
| 1 | Prompt gönderildi (her tur) | Skill listesi + yasağı `additionalContext` olarak stdout'a yazar | `skill-mandate.cjs · Satır: 86-100` |
| 2 | `.claude/skills/` varsa | Klasör adlarını (SKILL.md olanları) **diskten tarar** — sabit liste değil | `.../skill-mandate.cjs · Satır: 56-63` |
| 3 | Global set | `human-mode, truth-engine, verify-loop, agent-debate, context-report` (yalnız varlıkları kontrol edilir) | `.../skill-mandate.cjs · Satır: 26, 77-78` |
| 4 | İstisna | `kullanıcı açıkça 'skill kullanma' derse` veya `görev skill'lerle ilgisizse` | `.../skill-mandate.cjs · Satır: 92` |
| 5 | `--check` modu | v3.0 format doğrulayıcı: frontmatter yok · `name` klasörle 1:1 değil · `description` yok / >1024 char / "Use when" ile başlamıyor · yasak root key · metadata bloğu yok · kırık link · orphan · sidecar `CLAUDE.md` · çift kopya → **exit 1** | `.../skill-mandate.cjs · Satır: 106-177` |
| 6 | Hook çökmesi | Hook modunda **yine exit 0** (prompt asla bloklanmaz); `--check` modunda hatayı yüzeye çıkarır exit 1 | `.../skill-mandate.cjs · Satır: 188-195` |

**Sürücü ile ilişkisi:** G1 bir **araç seçimi kapısı değil**, *araç öncesi hazırlık* kapısıdır —
"hangi prosedür uygulanacak" sorusunu (skill) çözmeden §4 adım [3]'e geçmek **yasaktır**
(`Satır: 88`).

## §8. Yetki Red Durumu Davranışı

| # | Red nedeni | Sürücü davranışı | Sonraki adım |
|---|---|---|---|
| 1 | `Tool not found` | Zarfa yazılır: `success:false, result:null, executionTime:0.0` — **exception fırlatılmaz** | Ajan adayı/kaydı düzelt (`Satır: 75-82`) |
| 2 | `Unknown agent` | `['allowed' => false, 'reason' => "Unknown agent: …"]` | MO kimliği düzelt (`Satır: 151-153`) |
| 3 | Kategori yetkisiz | `reason` mesajı ajan'a döner; executor **çalışmaz** | Routing §6'dan yetkili ajan seç / handover (`Satır: 158-163`) |
| 4 | Hook `ask` (HIGH-risk dosya) | İnsan (Bayram Ali) onayı beklenir; onaysız yazılmaz | Onay veya vazgeçi (`ai-risk-gate.cjs · Satır: 41-45`) |
| 5 | Hook `block` (validator ihlal) | `reason` modele geri beslenir: *"Düzelt + tekrar dene"* | İhlal satırlarını düzelt (`ai-validate-post.cjs · Satır: 37-42`) |
| 6 | Pre-flight ihlali (DUR/RED) | Araç çağrısı **başlatılmaz** bile | §5 satır 7-11 (`Dosya: .ai/AGENTS.md · Satır: 272-281`) |
| 7 | Red sonrası tekrar deneme | Max **3 retry**; aşarsa escalation/queue reset | `Dosya: .ai/AGENTS.md · Satır: 201, 218, 281` |

**Kural (gözlem):** hiçbir red yolu **sessiz başarıya** dönüşmez — PHP tarafı her zaman
`success: false` + `error` metni döndürür (`Satır: 95-109`); hook tarafı ya `ask` ya `block`
üretir. Sessizlik yalnız **kapsam dışı** durumlarda vardır (G3 `exit 0` — §6 satır "kapsam
dışılık").

## §9. Timeout / Retry Davranışı

| Öncelik | Timeout | Max Retry | Yanıt | Kaynak |
|---|---|---|---|---|
| CRITICAL | 5s | 1 | Anlık | `Dosya: .ai/AGENTS.md · Satır: 318-323` |
| HIGH | 15s | 3 | 15s | aynı tablo |
| MEDIUM | 30s | 3 | 30s | aynı tablo |
| LOW | 60s | 2 | 60s | aynı tablo |

| Kural | Değer | Kanıt |
|---|---|---|
| Ajan timeout (30s+) | Max 3 retry, sonra **queue reset** | `Dosya: .ai/AGENTS.md · Satır: 201` |
| Agent timeout 30s+ (uyarı) | Max 3 retry, sonra **escalation** | `Dosya: .ai/AGENTS.md · Satır: 218` |
| Handover timeout | 30 sn · max retry 3 · redde MO girer | `Dosya: .ai/AGENTS.md · Satır: 346-352` |
| Escalasyon seviyeleri | L1 30s · L2 60s · L3 120s · her seviyede max 3 retry | `Dosya: .ai/AGENTS.md · Satır: 371, 398` |
| HTTP aracı timeout'u | `CURLOPT_TIMEOUT => 30` (repo sabiti) | `Dosya: shared/src/AI/ToolCalling.php · Satır: 260` |
| Çağrı süresi ölçümü | `microtime(true)` farkı → `executionTime` (4 haneye yuvarlı) | `.../ToolCalling.php · Satır: 85, 93, 99` |
| Hook timeout'u (upstream) | PreToolUse timeout'ta çağrı bloklanır, turn devam eder | `§30.2 / K4` (URL yalnız kaynaklar bölümünde) |

**Dikkat (repo gözlemü):** `callTool()` içinde **kendi retry döngüsü YOKTUR** — tek deneme,
`catch (\Throwable)` ile zarf (`Satır: 87-110`). Retry **üst katmanındadır** (§8 tablosu /
handover). Sürücüde bu ayrım açıktır: *araç = tek deneme, orkestrasyon = max 3*.

## §10. Çağrılık Zinciri (Araç → Servis) Sınırı

```text
Ajan (routing §6)
  → ToolCalling::callTool(toolName, params)          [Satır: 73]
      → validateParameters()                          [Satır: 89 → 307-325]
      → ($tool['executor'])($params)                  [Satır: 92]  ← callable SÍNIRI
          ├─ file-*    → file_exists / file_get_contents / file_put_contents / glob   [Satır: 184-219]
          ├─ db-query  → SELECT guard (executor içi)                                  [Satır: 232-238]
          └─ http-request → curl_init/exec (30s timeout)                              [Satır: 256-280]
      ← zarf {success, result, error, executionTime}  [Satır: 95-109]
  → §7.6 Verification → log.md
```

| Sınır | Kural | Kanıt |
|---|---|---|
| Interface | `ToolCalling implements ToolCallingInterface` — sözleşme `Contracts` katmanında | `Dosya: shared/src/AI/ToolCalling.php · Satır: 13, 26` |
| Executor = son durak | Araç gövdesi **doğrudan** FS/curl'a gider; repository/DB katmanına inmez | `.../ToolCalling.php · Satır: 187, 200, 256` |
| DB yüzeyi daraltılmış | `db-query` executor'u **sorguyu bile çalıştırmaz** — yalnız `SELECT` başlangıcı kontrolü + `['result' => [], 'rows' => 0]` iskeleti | `.../ToolCalling.php · Satır: 232-238` |
| Dış çağrı tek yerde | HTTP yalnız `http-request` üzerinden (`curl`) — başka araçta ağ yok | `.../ToolCalling.php · Satır: 244-283` |
| Domain'e harici bağımlılık | Domain katmanına harici paket **eklenmez** (global kural — §30/K4) | kök `~/.config/opencode/AGENTS.md` §5 |
| Ajanlar arası sınır | Ajan yalnız kendi kategorisiyle çağırır (§5 satır 3) | `.../ToolCalling.php · Satır: 28-40` |

## §11. Çağrı Sonrası Doğrulama (Post-Call)

| # | Katman | Ne doğrular | Başarısızlıkta | Kanıt |
|---|---|---|---|---|
| 1 | PHP zarfı | `success === true` ve `error === null` | ajan hatayı **taşımalı**, yutmaymalı | `ToolCalling.php · Satır: 95-109` |
| 2 | PHP zarfı | `executionTime` yazıldı mı (ölçüm kanıtı) | süresiz çağrı raporu yazılmaz | `.../ToolCalling.php · Satır: 93-99` |
| 3 | Hook G3 | `.ai/**.md` yazımı `validate.mjs --check`'ten geçti mi | `decision: block` → düzelt + tekrar dene | `ai-validate-post.cjs · Satır: 36-42` |
| 4 | §7.6.1 | Çıktı formatı uygun mu | standart çıkışa dön | `Dosya: .ai/AGENTS.md · Satır: 302` |
| 5 | §7.6.2 | Domain uyumluluğu (doğru ajan) | handover/revert | `.../AGENTS.md · Satır: 303` |
| 6 | §7.6.3 | Cross-reference: wiki-link'ler geçerli mi | link düzeltmesi (kökten gate) | `.../AGENTS.md · Satır: 304` |
| 7 | §7.6.4 | Security: hassas veri redaction | `[REDACTED]` + log ERROR | `.../AGENTS.md · Satır: 305` |
| 8 | §7.7 | `log.md` girişi + MEMORY state | görev tamamlanmış sayılmaz | `.../AGENTS.md · Satır: 307-312` |

## §12. Hata Modları Tablosu

| # | Hata | Semptom | Tespit | Tepki |
|---|---|---|---|---|
| 1 | Kayıtsız araç adı | `Tool not found` zarfı | `success:false` | Kayıt/typos düzelt (Satır: 75) |
| 2 | Yetkisiz kategori | ajan başka domain aracı çağırmak ister | `allowed:false` | Routing/handover (Satır: 158) |
| 3 | Bilinmeyen ajan id | tüm çağrılar red | `Unknown agent` | MO kimlik düzeltir (Satır: 151) |
| 4 | Eksik/hatalı parametre | executor hiç çalışmaz | `InvalidArgumentException` | Parametreyi tamamla (Satır: 309-321) |
| 5 | Executor exception | `success:false, error:message` | `catch \Throwable` (Satır: 101) | Kök neden → düzelt (§28) |
| 6 | Dosya yok (`file-read`) | `RuntimeException: File not found` | `file_exists` (Satır: 184-186) | Hayalet hedef → §5/DUR |
| 7 | SELECT dışı SQL | `Only SELECT queries are allowed` | `stripos(...,'SELECT') !== 0` (Satır: 235-237) | Migration/iş için Data Engine |
| 8 | HTTP hatası / timeout | `HTTP error: {curl_error}` | `curl_error` (Satır: 270-275) | 30s timeout'a kadar tek deneme (Satır: 260) |
| 9 | Hook `ask` ile onaysız yazı | çağrı reddedilir (beklemede) | `permissionDecision: ask` (Satır: 41) | İnsan onayı / vazgeçi |
| 10 | Validator ihlali | `decision: block` | `spawnSync` status 1 (Satır: 37) | Düzelt + tekrar dene |
| 11 | 3 başarısız düzeltme turu | aynı hata tekrarı | tur sayacı | **DUR** — şüpheli varsayımı yaz, üst karar (§28) |
| 12 | Timeout 30s+ | görev yanıtısız | §8 öncelik tablosu | max 3 retry → queue reset / escalation (Satır: 201, 218) |
| 13 | `.ai/` içinden gate çalıştırma | sahte başarısızlıklar | kök kuralı ihlali | **Kökten** çalıştır (§5 satır 10) |

## §13. Karar Ağacı — Görev Tipi → Araç Sınıfı → Yetkili Ajanlar

| # | Görev tipi (girdi) | Araç sınıfı | Yetkili ajanlar (AGENT_PERMISSIONS) | Yetkisiz tipik ajan |
|---|---|---|---|---|
| 1 | Dosya oku/yaz/ara | `file` | **tüm 11 ajan** (hepsinde `file` var) | — |
| 2 | SQL/şema sorgusu | `database` | mo, backend, security, data, qa, devops | ui, embedded, audio-hw, dsp-fw, win-sw |
| 3 | HTTP/WS çağrısı | `api` | mo, backend, ui, security, qa, devops | data, embedded, audio-hw, dsp-fw, win-sw |
| 4 | Ses işlemi | `audio` | mo, embedded, audio-hw, dsp-fw | backend, ui, security, data, qa, devops, win-sw |
| 5 | Donanım/cihaz | `hardware` | mo, embedded, audio-hw, dsp-fw, win-sw | backend, ui, security, data, qa, devops |
| 6 | Güvenlik işlemi | `security` | mo, security | diğer 9 |
| 7 | Tam yetki (koordinatör) | hepsi | `mo` (Satır: 29) | — |
| 8 | Kategori tahmini belirsiz | `file` (fallback) | herkes | dikkat: sessiz varsayım — §27/6 |

**Kaynak tablo:** `Dosya: shared/src/AI/ToolCalling.php · Satır: 28-40` (11 satır ajan) +
kategoriler Satır: 18-24 (6 sınıf).

## §14. Parametre Doğrulama Davranışı (çağrı öncesi son kapı)

```php
    private function validateParameters(array $definitions, array $params): void  // Satır 307
    {
        foreach ($definitions as $def) {
            if ($def['required'] && !array_key_exists($def['name'], $params)) {   // Satır 310
                throw new \InvalidArgumentException(
                    "Missing required parameter: {$def['name']}"                  // Satır 311
                );
            }
            if (array_key_exists($def['name'], $params)) {
                $value = $params[$def['name']];                                  // Satır 315
                $type  = get_debug_type($value);                                 // Satır 316
                if ($type !== $def['type'] && $def['type'] !== 'mixed') {        // Satır 318
                    throw new \InvalidArgumentException(                         // Satır 319
                        "Parameter '{$def['name']}' expects type '{$def['type']}', got '{$type}'"
                    );                                                           // Satır 321
                }
            }
        }
    }
```

> ✅ **Repo kanıtı** — `Dosya: shared/src/AI/ToolCalling.php · Satır: 307-325` (birebir alıntı).

| Kural | Sonuç | Sürücüdeki karşılığı |
|---|---|---|
| `required` zorunlu | eksik → exception, executor **çalışmaz** | §5 satır 5 (ön-kontrol) |
| Tip kontrolü `get_debug_type` | strict karşılaştırma, `mixed` tek muafiyet | §5 satır 6 |
| Doğrulama **executor'dan önce** | hatalı girdi sistem yüzeyine hiç vurmaz | §4 adım [6] sırası |
| Exception → zarf | `catch (\Throwable)` — çağıran asla crash görmez | §12 satır 5 |

## §15. Ölçüm Kalıpları — PowerShell (gerçek, kökten çalışır)

> ✅ **Çalıştırılabilir PowerShell betiği** — repo kökünden (`C:\www\coremusic.net`) çalışır;
> aşağıdaki sayılar **2026-10-17** gerçek ölçümüdür.

```powershell
# A) Kanıt dosyalarının satır sayısı (UTF8, BOM farkından bağımsız)
$files = @('shared\src\AI\ToolCalling.php',
           '.claude\hooks\skill-mandate.cjs',
           '.claude\hooks\ai-risk-gate.cjs',
           '.claude\hooks\ai-validate-post.cjs',
           '.ai\AGENTS.md',
           '.ai\architecture\K015-AI\tools\tools-surucu.md')
foreach ($f in $files) {
    $p = Join-Path (Get-Location) $f
    if (Test-Path $p) {
        $n = [IO.File]::ReadAllLines($p, [Text.Encoding]::UTF8).Count
        "{0} => {1}" -f $f, $n
    } else { "{0} => MISSING" -f $f }
}
```

**2026-10-17 ölçüm çıktısı (gerçek):**

| Dosya | Satır |
|---|---|
| `shared\src\AI\ToolCalling.php` | **326** |
| `.claude\hooks\skill-mandate.cjs` | **195** |
| `.claude\hooks\ai-risk-gate.cjs` | **73** |
| `.claude\hooks\ai-validate-post.cjs` | **44** |
| `.ai\AGENTS.md` | **775** |
| `...\tools\index.md` (ebeveyn) | **48** |
| Hook `.cjs` sayısı | **3** (`Get-ChildItem .claude\hooks -Filter *.cjs`) |

```powershell
# B) Bu dosyanın üretim kapısı (>=500 + Authority + FM 10 alan tek seferde)
$p = '.ai\architecture\K015-AI\tools\tools-surucu.md'
$lines = [IO.File]::ReadAllLines((Join-Path (Get-Location) $p), [Text.Encoding]::UTF8)
"satir    => {0}   (kapı >=500: {1})" -f $lines.Count, ($lines.Count -ge 500)
$auth = '**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode'
"authority=> {0}" -f ($lines[$lines.Count - 1] -eq $auth)
$pat = '^(node_id|node_type|status|parent_id|children|depends_on|evidence|validation|open_questions|group):'
"fm 10    => {0}" -f ((Select-String -Path $p -Pattern $pat).Count -eq 10)
```

## §16. Ölçüm — Kanıt Dosyaları Varlık + Kapsam Sayacı

> ✅ **Çalıştırılabilir PowerShell betiği** — kapı 4 (kanıt diskte) ve kapı 10 (kapsam dışı yazım 0).

```powershell
# C) Kanıt dosyaları var mı? (hayalet künye taraması)
$ev = @('shared\src\AI\ToolCalling.php', '.claude\hooks\skill-mandate.cjs',
        '.claude\hooks\ai-risk-gate.cjs', '.claude\hooks\ai-validate-post.cjs',
        '.ai\AGENTS.md', '.ai\architecture\K015-AI\tools\index.md')
$ghost = 0
foreach ($f in $ev) { if (-not (Test-Path $f)) { $ghost++ }
    "{0,-52} => {1}" -f $f, $(if (Test-Path $f) { 'OK' } else { 'GHOST' }) }
"sonuc => ghost={0} (kapı: 0)" -f $ghost

# D) Kapsam: klasörde paralel üretim girdileri de var (??) — bu subagent'ın
#    yazımı yalnız tools-surucu.md'dir (index.md orkestratör A; tools-anayasa/tools-ornekler ??)
$changed = git status --porcelain -- '.ai/architecture/K015-AI/tools/'
$mine = @($changed | Where-Object { $_ -match 'tools-surucu\.md$' })
"degisen => {0} girdi · bu subagent yazimi => {1} (kapı: yalnız tools-surucu.md)" -f $changed.Count, $mine.Count
$changed | ForEach-Object { "  {0}" -f $_ }

# E) Placeholder / çürük referans taraması (kapı 6: 0)
"placeholder hit => {0} (kapı: 0)" -f (Select-String -Path '.ai\architecture\K015-AI\tools\tools-surucu.md' -Pattern '\{\{').Count
"uydurma url hit  => {0} (yalnız §30.2'deki 4 dogrulanmis URL olmali)" -f
    ((Select-String -Path '.ai\architecture\K015-AI\tools\tools-surucu.md' -Pattern 'https?://').Count)
```

## §17. Repo Kanıtı A — Çağrı Çekirdeği (`callTool` zarfı)

> ✅ **Repo kanıtı** — aşağıdaki bloklar `shared/src/AI/ToolCalling.php` içinden alınmış
> **birebir** alıntılardır (uydurma yok; satır sonu yorumları künyedir).

```php
    public function callTool(string $toolName, array $params): array            // Satır 73
    {
        if (!isset($this->tools[$toolName])) {                                  // Satır 75
            return [                                                            // Satır 76
                'success'       => false,
                'result'        => null,
                'error'         => "Tool not found: {$toolName}",
                'executionTime' => 0.0,
            ];                                                                  // Satır 81
        }
        $tool = $this->tools[$toolName];                                        // Satır 84
        $startTime = microtime(true);                                           // Satır 85
        try {
            $this->validateParameters($tool['parameters'], $params);            // Satır 89
            $result = ($tool['executor'])($params);                             // Satır 92
            $executionTime = microtime(true) - $startTime;                      // Satır 93
            return [                                                            // Satır 95
                'success'       => true, 'result' => $result, 'error' => null,
                'executionTime' => round($executionTime, 4),                    // Satır 99
            ];
        } catch (\Throwable $e) {                                               // Satır 101
            $executionTime = microtime(true) - $startTime;                      // Satır 102
            return [                                                            // Satır 104
                'success'       => false, 'result' => null,
                'error'         => $e->getMessage(),                            // Satır 107
                'executionTime' => round($executionTime, 4),
            ];
        }
    }
```

**Sürücü ile bağlantısı:** tek deneme (retry yok) · her yol **zarf** ile biter · süre **ölçülür**
(§9, §11/2).

## §18. Repo Kanıtı B — İzin Kontrolü (`checkPermission` + allowlist)

```php
    private const AGENT_PERMISSIONS = [                                         // Satır 28
        'mo'       => ['file', 'database', 'api', 'audio', 'hardware', 'security'], // Satır 29
        'backend'  => ['file', 'database', 'api'],                              // Satır 30
        'ui'       => ['file', 'api'],                                          // Satır 31
        'security' => ['file', 'database', 'api', 'security'],                  // Satır 32
        'data'     => ['file', 'database'],                                     // Satır 33
        'embedded' => ['file', 'hardware', 'audio'],                            // Satır 34
        'qa'       => ['file', 'database', 'api'],                              // Satır 35
        'devops'   => ['file', 'api', 'database'],                              // Satır 36
        'audio-hw' => ['file', 'hardware', 'audio'],                            // Satır 37
        'dsp-fw'   => ['file', 'hardware', 'audio'],                            // Satır 38
        'win-sw'   => ['file', 'hardware'],                                     // Satır 39
    ];                                                                          // Satır 40

    public function checkPermission(string $toolName, string $agentId): array    // Satır 145
    {
        if (!isset($this->tools[$toolName])) {
            return ['allowed' => false, 'reason' => "Tool not found: {$toolName}"]; // Satır 148
        }
        if (!isset(self::AGENT_PERMISSIONS[$agentId])) {
            return ['allowed' => false, 'reason' => "Unknown agent: {$agentId}"];   // Satır 152
        }
        $toolCategory     = $this->tools[$toolName]['category'];                // Satır 155
        $allowedCategories = self::AGENT_PERMISSIONS[$agentId];                 // Satır 156
        if (!in_array($toolCategory, $allowedCategories, true)) {               // Satır 158
            return [                                                            // Satır 159
                'allowed' => false,
                'reason'  => "Agent '{$agentId}' not allowed to use category '{$toolCategory}' tools",
            ];                                                                  // Satır 162
        }
        return ['allowed' => true, 'reason' => null];                           // Satır 165
    }
```

> ✅ **Repo kanıtı** — `Dosya: shared/src/AI/ToolCalling.php · Satır: 28-40, 145-166` (birebir alıntı).

## §19. Repo Kanıtı C — Executor Sınırları (3 güvenlik pimi)

```php
        // Dosya: shared/src/AI/ToolCalling.php · Satır: 182-188 (file-read)
        'executor'    => function(array $params): array {
            $path = $params['path'];
            if (!file_exists($path)) {
                throw new \RuntimeException("File not found: {$path}");         // Satır 185
            }
            return ['content' => file_get_contents($path), 'size' => filesize($path)];
        },

        // Dosya: shared/src/AI/ToolCalling.php · Satır: 232-238 (db-query — SELECT pimi)
        'executor'    => function(array $params): array {
            // Güvenlik: Sadece SELECT izni                                       // Satır 233
            $sql = trim($params['sql']);
            if (stripos($sql, 'SELECT') !== 0) {
                throw new \RuntimeException("Only SELECT queries are allowed");   // Satır 236
            }
            return ['result' => [], 'rows' => 0];                                 // Satır 238
        },

        // Dosya: shared/src/AI/ToolCalling.php · Satır: 252-262 (http-request — timeout pimi)
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_TIMEOUT        => 30,                                        // Satır 260
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        ]);
```

> ✅ **Repo kanıtı** — üç blok tek dosyadan; her blok üstünde `Dosya:` künyesi satır içindedir.
> **Gözlem (doğrulanan):** `db-query` executor'u **gerçek sorgu çalıştırmaz** (`return ['result' => [], 'rows' => 0]`)
> — yani DB aracı bugün **duvar/iskelet** seviyesindedir; sürücü bunu §29/Q2'de işaretler.

## §20. Hook Kanıtı — Gate Davranışları (`.cjs` birebir alıntı)

```javascript
// Dosya: .claude/hooks/skill-mandate.cjs · Satır: 12
 * Emniyet: her koşulda exit 0 (prompt asla bloklanmaz); --check hariç.

// Dosya: .claude/hooks/skill-mandate.cjs · Satır: 87-88
'SKILL ZORUNLULUĞU (kök CLAUDE.md §Skill Usage Mandate): Görev bir skill ile eşleşiyorsa',
'İLK işlem Skill tool ile yüklemektir; eşleşen skill\'siz işlem başlatmak YASAKTIR.',

// Dosya: .claude/hooks/skill-mandate.cjs · Satır: 126
if (desc.length > 1024) violations.push(`${name}: description ${desc.length} char (>1024)`);
```

```javascript
// Dosya: .claude/hooks/ai-risk-gate.cjs · Satır: 36-47
const ROOT_HIGH = new Set(['CLAUDE.md', 'AGENTS.md', 'WORKFLOW.md']);
if (ROOT_HIGH.has(rel)) {
  process.stdout.write(JSON.stringify({
    hookSpecificOutput: {
      hookEventName: 'PreToolUse',
      permissionDecision: 'ask',
      permissionDecisionReason:
        'Control Plane HIGH-RISK kapısı (Q32): kök boot dosyası ' + rel +
        ' T1/T2 otorite yüzeyidir — İNSAN (Bayram Ali) onayı gerekir. ' +
        'Değişiklik risk: high · Doğrulama: node .ai/scripts/validate.mjs --check',
    },
  }));
```

```javascript
// Dosya: .claude/hooks/ai-validate-post.cjs · Satır: 31, 36-42
if (!/^\.ai\/.+\.md$/i.test(rel)) process.exit(0);          // yalnız .ai .md

const r = spawnSync(process.execPath, [validator, '--check'], { encoding: 'utf8', cwd: project });
if (r.status === 1) {
  const lines = (r.stdout || '').trim().split('\n').slice(0, 12).join(' | ');
  process.stdout.write(JSON.stringify({
    decision: 'block',
    reason: 'Control Plane validator İHLAL — .ai/scripts/validate.mjs --check başarısız. Düzelt + tekrar dene. ' + lines,
  }));
}
```

> ✅ **Repo kanıtı** — üç `.cjs` dosyasından birebir alıntılar (okundu 2026-10-17; §15 ölçümü).

## §21. İllüstratif — Python: Allowlist Guard + Retry with Backoff

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# Araç öncesi allowlist guard (PHP checkPermission'ın ruhu) + üst katman retry
from time import sleep

AGENT_PERMISSIONS = {
    "mo":      {"file", "database", "api", "audio", "hardware", "security"},
    "ui":      {"file", "api"},
    "data":    {"file", "database"},
}
CATEGORIES = {"file-read": "file", "db-query": "database", "http-request": "api"}

def check_permission(tool_name: str, agent_id: str, registry: dict) -> tuple[bool, str]:
    if tool_name not in registry:
        return False, f"Tool not found: {tool_name}"
    if agent_id not in AGENT_PERMISSIONS:
        return False, f"Unknown agent: {agent_id}"
    cat = CATEGORIES.get(tool_name, "file")            # fallback sessiz 'file' — §27/6
    if cat not in AGENT_PERMISSIONS[agent_id]:
        return False, f"Agent '{agent_id}' not allowed to use category '{cat}' tools"
    return True, ""

def call_with_retry(fn, *, max_retry: int = 3, base_delay: float = 0.5):
    """Üst katman retry — PHP callTool TEK deneme yapar (§9)."""
    last = None
    for attempt in range(1, max_retry + 1):
        try:
            return {"success": True, "result": fn(), "attempt": attempt}
        except Exception as exc:                       # hata yutulmaz, zarfa yazılır
            last = exc
            if attempt < max_retry:
                sleep(base_delay * (2 ** (attempt - 1)))   # exponential backoff
    return {"success": False, "error": str(last), "attempts": max_retry}
```

## §22. İllüstratif — Go: Timeout + Tek Deneme Çağrı Zarfı

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
package toolcall

import (
	"context"
	"fmt"
	"time"
)

// Frame, PHP'deki {success, result, error, executionTime} zarfının karşılığı.
type Frame struct {
	Success       bool
	Result        any
	Error         string
	ExecutionTime time.Duration
}

// Call: tek deneme (retry üst katmanda — §9 tablosu).
func Call(ctx context.Context, name string, run func(context.Context) (any, error), budget time.Duration) Frame {
	start := time.Now()
	cctx, cancel := context.WithTimeout(ctx, budget) // PHP karşılığı: CURLOPT_TIMEOUT 30
	defer cancel()

	res, err := run(cctx)
	elapsed := time.Since(start)
	if err != nil {
		return Frame{Success: false, Error: err.Error(), ExecutionTime: elapsed}
	}
	return Frame{Success: true, Result: res, ExecutionTime: elapsed}
}

func NotFound(name string) Frame {
	return Frame{Success: false, Error: fmt.Sprintf("Tool not found: %s", name)}
}
```

## §23. İllüstratif — TypeScript: Çağrı Sonrası Doğrulama

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```typescript
type Frame<T> =
  | { success: true; result: T; executionTime: number; error: null }
  | { success: false; result: null; executionTime: number; error: string };

/** Çağrı sonrası doğrulama (§11): zarf + domain + redaction tek kontrolde. */
export function verifyFrame<T>(
  frame: Frame<T>,
  opts: { expectedDomain: string; toolDomain: string },
): { ok: boolean; reasons: string[] } {
  const reasons: string[] = [];
  if (!frame.success) reasons.push(`araç hatası: ${frame.error}`);
  if (opts.expectedDomain !== opts.toolDomain) reasons.push('domain boundary ihlali → revert');
  if (typeof frame.executionTime !== 'number') reasons.push('executionTime yok → ölçüm kanıtsız');
  return { ok: reasons.length === 0, reasons };
}
```

## §24. Token / Ekonomi

| # | Kural | Sonuç | Kanıt |
|---|---|---|---|
| 1 | Araç listesini **kategoriye daralt** (`getToolsByCategory`) | prompt'a yalnız ilgili araç girer | `ToolCalling.php · Satır: 132-140` |
| 2 | `description` ≤1024 char (skill tarafı) | uzun description ihlal sayılır | `skill-mandate.cjs · Satır: 126` |
| 3 | Hook G1 **yalnız listeyi** enjekte eder, metin döndürmez | prompt token şişmesi az | `skill-mandate.cjs · Satır: 86-93` |
| 4 | Pre-flight "önce boot'ta okunanlara bak" | çift okuma yasak | `Dosya: .ai/AGENTS.md · Satır: 270-281` |
| 5 | Araç `tools` parametresi token maliyeti taşır (upstream) | gereksiz araç sunmak pahalı | §30/K3 (platform.claude.com fiyatlandırma bölümü) |
| 6 | `--check` modu batch çalışır (prompt başına değil) | gate maliyeti tur başına 1 | `skill-mandate.cjs · Satır: 106-177` |
| 7 | G3 yalnız `.ai/**/*.md` tetikler | `.ai/` dışı yazım gate maliyeti 0 | `ai-validate-post.cjs · Satır: 31` |

## §25. Doğrulama Kapıları

| # | Kapı | Kriter | Yöntem / komut |
|---|---|---|---|
| 1 | Frontmatter | 10 alan eksiksiz | `Select-String` sayacı (§15-B) |
| 2 | Satır sayısı | `>= 500` | `[IO.File]::ReadAllLines(...).Count` (§15) |
| 3 | Authority son satır | birebir | `§15-B` eşitlik kontrolü |
| 4 | Kanıt dosyaları diskte | hayalet = 0 | `§16-C` Test-Path döngüsü |
| 5 | Hook envanteri | tam 3 `.cjs` | `Get-ChildItem *.cjs` (§15) |
| 6 | Placeholder | `\{\{` ( kaçışlı ) = 0 | `§16-E` |
| 7 | URL | yalnız §30.2'deki 4 doğrulanmış adres | `§16-E` ikinci sayaç |
| 8 | PHP künyeleri | her alıntıda `Satır:` | gözden geçirme (§17-§19) |
| 9 | İllüstratif etiket | Python/Go/TS bloklarının **hepsinde** uyarı satırı | gözden geçirme (§21-§23) |
| 10 | Kapsam dışı yazım | 0 dosya | `§16-D` whitelist sayacı |
| 11 | `git commit` | 0 (orkestratöre ait) | §26 |

## §26. Yasaklar ve Sınırlar

| # | Yasak | Neden |
|---|---|---|
| 1 | İzin kontrolü **atlanarak** araç çağırmak | §5 zinciri bypass — red davranışı devre dışı |
| 2 | Hook gate'ini **devre dışı** bırakmak (HIGH-risk `ask` dahil) | insan onayı güvenlik kapısı |
| 3 | `shared/src/**` veya `.claude/**` **yazmak** (bu görevde) | salt-okunur kanıt yüzeyi |
| 4 | `git commit` atmak | **orkestratöre aittir** (subagent yasak) |
| 5 | Bu dosya **dışına** yazmak / dosya adını değiştirmek | kapsam dışı yazım = ihlal (§25/10) |
| 6 | Uydurma kanıt / uydurma URL / `Dosya: Satır:` künyesiz iddia | Zero-Hallucination |
| 7 | Retry'ı **executor içine** gömmek | çift deneme → yan etki (retry üst katmanda, §9) |
| 8 | Domain katmanına harici bağımlılık eklemek | katman ihlali (global §5) |
| 9 | `.ai/` içinden gate betiği çalıştırmak | sahte başarısızlıklar (kök §13.6) |

## §27. Sık Yapılan Hatalar (anti-pattern)

| # | Yanlış | Doğru | Kaynak |
|---|---|---|---|
| 1 | Araç çağrısını pre-flight **sonrası** sanmak | sıra: analiz → skill → pre-flight → izin → gate → çağ | §4 |
| 2 | Red'i "araç bozuk" saymak | red 3 kaynaktan gelir: kayıt/ajan/kategori (§5) | Satır: 148/152/159 |
| 3 | Hook `exit 0`'ı "geçti" sanmak | G1/G2 `exit 0` **emniyettir**, red `ask` JSON'undadır | `ai-risk-gate.cjs · Satır: 38-48` |
| 4 | G3 bloğunu "öneri" saymak | `decision: block` **zorunlu düzeltme** | `ai-validate-post.cjs · Satır: 39-42` |
| 5 | Retry'ı araçta beklemek | araç tek deneme; retry §8 tablosunda | `ToolCalling.php · Satır: 87-110` · `AGENTS.md · Satır: 318-323` |
| 6 | Kategori fallback'ini ("file") yetki genişletme sanmak | fallback **tahmindir**, allowlist'i genişletmez | `Satır: 298` + `Satır: 158` |
| 7 | `db-query` ile "veri çekildi" raporlamak | executor sorgu çalıştırmaz, iskelet döner | Satır: 238 (§19) |
| 8 | Kanıtsız "gate çalışıyor" | hook **kayıdı** settings.json'da — §29/Q4 | `⚠️ VERIFICATION REQUIRED` |

## §28. Kapanış Kontrol Listesi (her araç çağrılı görevde)

| # | Soru | Zorunlu cevap |
|---|---|---|
| 1 | Skill eşleşmesi bakıldı mı (G1 yasağı)? | evet / gerekçesi |
| 2 | Pre-flight 12 satırı geçildi mi (§5 7-11)? | evet |
| 3 | `checkPermission` çağrıldı mı — red var mı? | evet / red ise §8 davranışı |
| 4 | HIGH-risk dosya mı — `ask` onayı alındı mı? | evet / dosya HIGH değil |
| 5 | Çağrı zarfı `success` + `executionTime` taşıyor mu? | evet |
| 6 | §7.6 4 kontrol (format/domain/link/redaction) | evet |
| 7 | Hata/timeout için retry bütçesi aşıldı mı (max 3)? | hayır / escalation kaydı |
| 8 | Kapsam dışı yazım / `git commit` | **0 / 0** |

## §29. Açık Sorular

| # | Soru | Durum |
|---|---|---|
| Q1 | `depends_on: [tools-anayasa]` — görev başında `tools/` altında **yalnız `index.md`** vardı (`tools-anayasa.md` MISSING); **görev sırasında** `tools-anayasa.md` paralel üretimle belirdi (2026-10-17, 621 satır, `??` untracked) → bağımlılık dosyası artık diskte. Açıkan: iki dosyanın **doğrulama sırası** (hangisi önce kabul edildi) ve çapraz referans hizası. | kısmi kapandı — sıra `⚠️ VERIFICATION REQUIRED` |
| Q2 | `db-query` executor'u gerçek sorgu çalıştırmıyor (`Satır: 238` boş iskelet) — "aracı çalışıyor" iddiası hangi sınırda IMPLEMENTED? | `⚠️ VERIFICATION REQUIRED` (§19) |
| Q3 | PHP `ToolCalling` ile hook katmanı arasında **fiziksel köprü** (hook'un PHP çağrısını sarmalaması) diskte göründü mü? Bu turda görülmedi. | `⚠️ VERIFICATION REQUIRED` (§1) |
| Q4 | Hook'ların `.claude/settings.json` içindeki **kayıt** satırları okunmadı (bu turda dosya açılmadı) — tetikleyici eşleşmeleri doğrulanmadı. | `UNKNOWN` |
| Q5 | Ebeveyn `tools/index.md` `children: []` ve "kademe-3 bu fazda AÇILMADI" diyor (`Satır: 6`) — bu modül eklendikten sonra `children` güncellenecek mi? | orkestratör kararı |
| Q6 | `AGENT_PERMISSIONS` ile `.ai/AGENTS.md` §5 Domain Boundaries **çapraz** tutuyor mu (11 ajan aynı mı)? Bu turda satır satır karşılaştırılmadı. | `⚠️ VERIFICATION REQUIRED` |

## §30. Kaynaklar

### §30.1 Repo Kaynakları (okundu — 2026-10-17)

| Kaynak | Yol | Kullanım amacı | Ölçüm |
|---|---|---|---|
| Araç çekirdeği | `shared/src/AI/ToolCalling.php` | §3, §5, §14, §17-§19 PHP kanıtı | **326 satır** |
| Hook G1 | `.claude/hooks/skill-mandate.cjs` | §6-§7 skill gate'i | **195 satır** |
| Hook G2 | `.claude/hooks/ai-risk-gate.cjs` | §6, §8 onay kapısı | **73 satır** |
| Hook G3 | `.claude/hooks/ai-validate-post.cjs` | §6, §11/3 blok kapısı | **44 satır** |
| Dispatch protokolü | `.ai/AGENTS.md` §7.2/§7.4/§7.6 + §8/§9/§10/§11/§17/§18 | §4-§5, §9, §11 (Satır: 246-312, 316-323, 346-352, 371-398, 403-430, 195-201, 213-218) | **775 satır** |
| Ebeveyn düğüm | `.ai/architecture/K015-AI/tools/index.md` | Kapsam sınırı + `children` durumu (Satır: 6) | **48 satır** |
| Şablon | `.ai/architecture/K015-AI/rag/rag-surucu.md` | H1 + § iskeleti + kapı disiplini | **779 satır** |
| Subagent yetki notu | `.claude/AGENTS.md` §3.1-§3.3 | `permission` bloğu (`edit: deny`) + yasaklar | §3.1/§3.3 (bu tur satır ölçümü yapılmadı) |

### §30.2 Web Kaynakları (doğrulandı — 2026-10-17, 4 resmi kaynak)

| # | Kaynak | Doğrulanan iddia | Kullanım |
|---|---|---|---|
| K1 | `https://code.claude.com/docs/en/hooks` | Hook lifecycle: `UserPromptSubmit` (prompt işlenmeden önce), `PreToolUse` (çağrı **öncesi, bloklayabilir**), `PostToolUse` (başarılı çağrı **sonrası**); JSON `permissionDecision` alanları; exit-code blok semantiği | §6 gate eşlemesi |
| K2 | `https://developers.openai.com/api/docs/guides/function-calling` | `required` parametre listesi, `parallel_tool_calls` ile 0/1 araç sınırı, aracı sonuç `role: tool` olarak geri besleme döngüsü | §4-§5, §11 karşılaştırma |
| K3 | `https://platform.claude.com/docs/en/agents-and-tools/tool-use/overview` (önceki `docs.anthropic.com/.../overview` yönlendirmesiyle) | `tool_use` → executable → `tool_result` round trip; `tool_choice: auto`; `strict: true` şema uyumu; `tools` parametresinin token maliyeti tablosu | §10-§11, §24 |
| K4 | `https://code.claude.com/docs/en/agent-sdk/hooks` | Agent SDK hook zamanlaması: `PreToolUse` timeout'unda aracın çağrısı **bloklanır**, turn devam eder; varsayılan timeout'lar (tool 600s · hook 30s · prompt 10s); `permissionDecision` sırası `deny > defer > ask > allow` | §13 timeout tablosu, §6 gate sırası |

> Erişilemeyen/karışık herhangi bir iddia bu tabloya **konmaz** — `⚠️ VERIFICATION REQUIRED`
> (§29). Uydurma URL yasaktır (§26/6).

## §31. Gate (özet)

§1-§31 tamam: ELI15 · kapsam · registry 5 katman · **8 adımlı akış** · **12 satırlık izin karar
ağacı** · 3 gate'li hook akışı · skill-mandate 6 madde · **7 durumlu red davranışı** · 4
öncelikli timeout/retry + 6 kural · çağrılık zinciri 6 sınır · 8 satırlık çağrı sonrası doğrulama
· **13 hata modu** · 8 satır görev→sınıf karar ağacı · parametre doğrulama (PHP) · PowerShell
**4 betik (A-E)** · PHP **4 blok** (zarf/izin/3 pimi/parametre) · hook kanıtı **3 blok** ·
illüstratif **3 blok (Python/Go/TS)** · 7 token kuralı · **11 kapı** · 9 yasak · 8 anti-pattern ·
8 soruluk kapanış · **6 açık soru** · 7 repo + 4 web kaynak.

**Kapılar:** satır `>= 500` · FM **10 alan** · Authority son satır birebir · kapsam dışı yazım
**0** · `git commit` **0** · uydurma URL **0**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
