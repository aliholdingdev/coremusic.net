---
node_id: orchestration-ornekler
node_type: module
status: active
parent_id: orchestration
children: []
depends_on: [orchestration-anayasa, orchestration-surucu]
evidence:
  - "shared/src/AI/AIOrchestrator.php (kod kanıtı, 2026-10-17 okuma)"
  - ".ai/AGENTS.md §7-§12 (dispatch/lock alıntıları)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı, kod kanıtı zorunlu"
open_questions: []
group: kod
---

# orchestration-ornekler - Orkestrasyon Kod Örnekleri (kanıt odaklı)

> **Kapsam:** Her PHP alıntısı 2026-10-17'de `shared/src/AI/` altındaki 7 dosyanın gerçek
> satırıdır (`Dosya:` / `Satır:` künyesi ile); dosyalar salt-okunur okunmuştur. Vault
> alıntıları `.ai/AGENTS.md` §6-§12'den satır numarasıyla doğrudan alınmıştır ve markdown
> kod bloğu içinde taşınır. İllüstratif çok-dilli bloklar repo kanıtı DEĞİL'dir; her birinin
> üstünde zorunlu etiket vardır. `git commit` bu görevde YASAKTIR.

## §1. Kanıt Künye Formatı

| Alan | Kural |
|---|---|
| Dosya | göreli yol, diskte glob ile doğrulanmış (2026-10-17) |
| Satır | okunan aralık (L26-L38 gibi) — okunmamış satır künye olamaz |
| Alıntı | kod satırının kendisi (kısaltma serbest, uydurma yasak) |
| Vault alıntısı | `.ai/AGENTS.md` § no + satır aralığı, kod bloğu içinde birebir |
| Yokluk | tam cümle: "Repo'da bu alan kodu YOK — kaynak: upstream docs" |
| Ölçüm | `Select-String` / `ReadAllLines` / glob sayaçları — araç çıktısı, iddia değil |
| İllüstratif | `⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı` |
| Tarih | her kanıt 2026-10-17 okuma turuna ait |
| Eşleşme | `evet` / `kısmi` / `YOK` — başka değer yok |
| Yasak | uydurma künye → Zero-Hallucination ihlali (kök AGENTS §3) · çelişkide disk kazanır |

## §2. Okunan PHP Envanteri — `shared/src/AI/` (7 dosya)

Dosya: `shared/src/AI/` · glob → **7 isabet** · satır: `ReadAllLines(UTF8).Count` (2026-10-17)

| # | Dosya | Satır | Sınıf amacı (docblock L) | Orkestrasyon ilgisi |
|---|---|---|---|---|
| 1 | AIOrchestrator.php | 210 | "11 ajanlı sistem için görev dağıtımı" (L17) | doğrudan — routing + dispatch |
| 2 | AIWorkflow.php | 271 | "trigger → process → result → feedback" (L16) | doğrudan — 5 dispatch çağrısı |
| 3 | ToolCalling.php | 326 | "Tool registry, executor, permission check" (L16) | doğrudan — agent izin tablosu |
| 4 | MemorySystem.php | 242 | "Session state, persistent cache, memory hierarchy" (L16) | kısmi — TTL/öncelik hiyerarşisi |
| 5 | PromptEngine.php | 325 | "Prompt üretimi, token yönetimi, validasyon" (L16) | kısmi — girdi kapısı |
| 6 | KnowledgeBase.php | 232 | "Semantic search, RAG, knowledge lifecycle" (L16) | kısıtlı — yalnız alım |
| 7 | AIEngine.php | 382 | "5 modül: Recommendation/Audio/EQ/Hardware/Fault" (L21-L26) | kısıtlı — işlev üretici |

**Düzeltme (görev metni ↔ disk):** görevde anılan `TASK_PRIORITY` sabiti bu dizinde **DEĞİL**
(`TASK_PRIORITY` grep → **0 isabet**, 2026-10-17) — öncelik değerleri `TASK_TYPES` içindedir (L41-L49).

## §3. AGENT_MAP — 11 Anahtarlı Routing Tablosu (PHP)

Dosya: `shared/src/AI/AIOrchestrator.php` · Satır: L26-L38

```php
private const AGENT_MAP = [
    'mo'         => ['domain' => 'coordination', 'layer' => 'coordination'],
    'backend'    => ['domain' => 'php-api',     'layer' => 'l2-routing'],
    'ui'         => ['domain' => 'frontend',    'layer' => 'l3-presentation'],
    'security'   => ['domain' => 'security',    'layer' => 'l1-security'],
    'data'       => ['domain' => 'database',    'layer' => 'l0-infrastructure'],
    'embedded'   => ['domain' => 'audio-dsp',   'layer' => 'l0-electronics'],
    'qa'         => ['domain' => 'testing',     'layer' => 'cross-cutting'],
    // L34-L37: devops / audio-hw / dsp-fw / win-sw (toplam 11 anahtar)
];
```

| Gözlem | Kanıt |
|---|---|
| 11 anahtar — `.ai/AGENTS.md` §4 agent tablosu (1 MO + 10 uzman) ile aynı kadro | L26-L38 · AGENTS §4 |
| Her kayıt `{domain, layer}` — iki alanlı metadata; ajan davranışı/güncel görevi **yok** | L27-L37 |
| Routing = sabit dizi araması (`isset`), skorlama/embedding **değildir** | L127 |
| Domain boundary yorumu var, kontrolü **yok**: `// Domain boundary kontrolü` yalnız etiket | L135-L136 |

## §4. TASK_TYPES — Görev Tipi → Agent + Priority (PHP)

Dosya: `shared/src/AI/AIOrchestrator.php` · Satır: L40-L49

```php
private const TASK_TYPES = [
    'recommendation'    => ['agent' => 'backend', 'priority' => 'medium'],
    'analysis'          => ['agent' => 'embedded', 'priority' => 'high'],
    'eq-optimization'   => ['agent' => 'embedded', 'priority' => 'medium'],
    'hardware'          => ['agent' => 'audio-hw', 'priority' => 'high'],
    'fault'             => ['agent' => 'dsp-fw',   'priority' => 'critical'],
    'security-audit'    => ['agent' => 'security', 'priority' => 'critical'],
    'documentation'     => ['agent' => 'mo',       'priority' => 'low'],
    'testing'           => ['agent' => 'qa',       'priority' => 'medium'],
];
```

| Gözlem | Kanıt |
|---|---|
| 8 görev tipi; 2'si `critical` (fault, security-audit), 1'i `low` | L41-L49 |
| `priority` alanı sabit tabloda var; çağrıcı ayrıca `$priority` geçirir ve **kayıtta çağrıcının değeri tutulur** | L40-L49, L71, L84 |
| İki öncelik kaynağı çakışabilir: `TASK_TYPES['analysis']=high` ama `AIWorkflow` `'high'` geçirir (eşleşme bugün tesadüfi) | L42 vs AIWorkflow L106 |
| Öncelik kümesi `critical/high/medium/low` — `AIOrchestratorInterface` docblock L20 ile birebir | Contracts L20, L43 |

## §5. dispatchTask — Görev Kaydı ve Tek Satırlık "Kuyruk" (PHP)

Dosya: `shared/src/AI/AIOrchestrator.php` · Satır: L71-L97

```php
public function dispatchTask(string $type, array $params, string $priority = 'medium'): string
{
    if (!isset(self::TASK_TYPES[$type])) {
        throw new \InvalidArgumentException("Unknown task type: {$type}");   // fail-closed
    }
    $taskId = $this->generateTaskId();
    $taskConfig = self::TASK_TYPES[$type];
    $this->tasks[$taskId] = [
        'id' => $taskId, 'type' => $type, 'status' => 'pending',
        'priority' => $priority, 'agent' => $taskConfig['agent'],
        'params' => $params, 'result' => null, 'error' => null,
        'created_at' => date('Y-m-d H:i:s'), 'completed_at' => null,
    ];
    // Priority weighted execution
    $this->executeTask($taskId);              // L94 — SENKRON, aynı çağrıda çalışır
    return $taskId;
}
```

| Gözlem | Kanıt |
|---|---|
| Bilinmeyen tip → exception (fail-closed) | L73-L75 |
| Task ID: `task_` + 16 hex + epoch (`random_bytes(8)`) | L177-L180 |
| Depolama bellek-içi `$tasks[]` — disk/DB/queue adapter **yok** | L51-L52 |
| "Priority weighted execution" yalnız **yorum**; sıralama, bekleme, worker **yok** → gerçek öncelikli kuyruk repo'da yok | L93, L94 |
| Görev tipi doktor: `AIWorkflow` içindeki tek dispatch yüzeyi | §7 |

## §6. executeTask + routeToAgent — Yürütme ve Yanıt Şekli (PHP)

Dosya: `shared/src/AI/AIOrchestrator.php` · Satır: L125-L149, L185-L209

```php
public function routeToAgent(string $agentId, string $action, array $params): array
{
    if (!isset(self::AGENT_MAP[$agentId])) {
        return ['success' => false, 'result' => null, 'error' => "Unknown agent: {$agentId}"];
    }
    $agentInfo = self::AGENT_MAP[$agentId];
    return ['success' => true, 'result' => [
        'agent' => $agentId, 'action' => $action,
        'domain' => $agentInfo['domain'], 'layer' => $agentInfo['layer'], 'params' => $params,
    ], 'error' => null];
}

private function executeTask(string $taskId): void
{
    $task = &$this->tasks[$taskId];
    $task['status'] = 'running';
    try {
        $result = $this->routeToAgent($task['agent'], $task['type'], $task['params']);
        if ($result['success']) { $task['status'] = 'completed'; $task['result'] = $result['result']; }
        else                    { $task['status'] = 'failed';    $task['error']  = $result['error'];  }
        $task['completed_at'] = date('Y-m-d H:i:s');
    } catch (\Throwable $e) {
        $task['status'] = 'failed'; $task['error'] = $e->getMessage(); $task['completed_at'] = date('Y-m-d H:i:s');
    }
}
```

| Gözlem | Kanıt |
|---|---|
| Durum makinesi: `pending → running → completed|failed` (3 geçişli, tek çağrı içinde) | L83, L188, L196-L200 |
| `routeToAgent` ajanı **çalıştırmaz** — domain/layer metadata'sını echo eder (routing stub) | L138-L149 |
| Bilinmeyen ajan → `success:false` (yumuşak hata); bilinmeyen görev tipi → exception: iki farklı hata modeli | L127-L133 vs L73-L75 |
| Retry / timeout / escalation bu sınıfta **yok** (tek deneme, `catch \Throwable` ile biter) | L190-L208 |
| Context = `array_merge` (son yazan kazanır) — kilit/şema **yok** | L154-L170 |

## §7. AIWorkflow — Beş Dispatch Çağrı Noktası (PHP)

Dosya: `shared/src/AI/AIWorkflow.php` · Satır: L59, L106, L154, L205, L246

```php
$taskId = $this->orchestrator->dispatchTask('recommendation',   $context, 'medium');  // L59
$taskId = $this->orchestrator->dispatchTask('analysis',         $request, 'high');    // L106
$taskId = $this->orchestrator->dispatchTask('eq-optimization',  $request, 'medium');  // L154
$taskId = $this->orchestrator->dispatchTask('hardware',         $request, 'high');    // L205
$taskId = $this->orchestrator->dispatchTask('fault',            $request, 'critical');// L246
```

| Workflow | Tip → TASK_TYPES agent (§4) | Çağrıda öncelik | Task ID döner mi? |
|---|---|---|---|
| recommendationWorkflow | recommendation → backend | medium | evet (metadata `task_id`, L85) |
| audioAnalysisWorkflow | analysis → embedded | high | evet (L134) |
| autoEQWorkflow | eq-optimization → embedded | medium | evet (L186) |
| hardwareAnalysisWorkflow | hardware → audio-hw | high | evet (L228) |
| faultPredictionWorkflow | fault → dsp-fw | critical | evet (L266) |

**Doğrulama:** 5 çağrı da `TASK_TYPES` içindeki tip adını kullanır; 5'inde de öncelik, sabit
tablodaki değerle **aynı**dır (L41-L49 ile yukarıdaki satırlar birebir örtüşür).

## §8. Tek "Yönlendirmeli Bildirim" — routeToAgent('security', 'alert') (PHP)

Dosya: `shared/src/AI/AIWorkflow.php` · Satır: L251-L259

```php
// 3. Alert yönetimi
if ($prediction['risk'] === 'high') {
    // Güvenlik agent'ına bildir
    $this->orchestrator->routeToAgent('security', 'alert', [
        'type'    => 'hardware_fault',
        'severity' => 'high',
        'details'  => $prediction,
    ]);
}
```

| Soru | Cevap (kanıt) |
|---|---|
| Bu bir handover mı? | **Hayır** — hedef ajana onay/confirmation akışı yok; §9.1'deki 8 alanlı mesaj biçimi (§14) kullanılmıyor |
| Sonuç kontrol ediliyor mu? | Hayır — dönüş değeri atılır (L254 çağrısı tek satır) |
| Repo'da el sıkışmalı transfer var mı? | Repo'da bu alan kodu YOK — kaynak: upstream docs (`handover` grep → 0, §28) |

## §9. AGENT_PERMISSIONS + checkPermission — Ajan Bazlı İzin Kapısı (PHP)

Dosya: `shared/src/AI/ToolCalling.php` · Satır: L28-L40, L145-L166

```php
private const AGENT_PERMISSIONS = [
    'mo'       => ['file', 'database', 'api', 'audio', 'hardware', 'security'],  // 6 kategori
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
// L145-L166: tool yoksa allowed:false · agent yoksa allowed:false · kategori yoksa allowed:false
```

| Gözlem | Kanıt |
|---|---|
| 11 ajan × 6 kategori — fail-safe: 3 ayrı `allowed: false` kolu | L147-L163 |
| İzin **kategori** düzeyinde (tool düzeyinde değil); kategori adı tool önekinden türetilir | L155-L158, L289-L299 |
| `mo` en geniş yetki (6 kategori) — tek genişletilmiş rol | L29 |
| Orkestrasyon ile ilişkisi: domain boundary'nin tek **çalışan** kod karşılığı bu tablodur | §3 tablosundaki "kontrol yok" notu ile karşıtlık |

## §10. AIOrchestratorInterface — Sözleşme Yüzeyi (PHP)

Dosya: `shared/src/Contracts/AI/AIOrchestratorInterface.php` · Satır: L13-L65

```php
public function dispatchTask(string $type, array $params, string $priority = 'medium'): string;
public function getTaskStatus(string $taskId): array;
public function listActiveTasks(): array;
public function routeToAgent(string $agentId, string $action, array $params): array;
public function getContext(string $sessionId): array;
public function updateContext(string $sessionId, array $data): bool;
```

| Sözleşme | Docblock kanıtı | Impl. |
|---|---|---|
| 6 metot | L23, L31, L38, L48, L56, L65 | hepsi AIOrchestrator.php'te mevcut |
| 11 agent ID'si tek yerde listelenir | L43 (`mo, backend, ui, security, data, embedded, qa, devops, audio-hw, dsp-fw, win-sw`) | AGENT_MAP L26-L38 ile aynı kümе |
| Öncelik kümesi `critical, high, medium, low` | L20 | TASK_TYPES L41-L49 ile aynı |
| **Sözleşmede olmayan:** handover, lock, escalation, health, retry | — | Yok (§27) |

## §11. `.ai/AGENTS.md` §7 — Task Dispatch Algorithm (alıntı)

Dosya: `.ai/AGENTS.md` · Satır: L246-L257

```text
Kullanıcı İsteği
  → [1. Analiz] — Keyword çıkarma, domain eşleme
    → [2. Pre-flight Checks] — Bağımlılık, dosya kontrolü, UI Design mockup kontrolü
      → [3. Task Assignment] — Doğru ajanı seç ve görev ata
        → [4. Execution] — Ajan görevi yürütür
          → [5. Handover] — Gerekirse diğer ajana transfer
            → [6. Validation] — Çıktıyı doğrula
              → [7. Completion] — Görevi tamamla ve logla
```

| Vault adımı | PHP karşılığı | Eşleşme |
|---|---|---|
| 1. Analiz (keyword → §6 tablosu) | — (kod yok) | **YOK** |
| 2. Pre-flight (mockup/lock/dependency) | — (kod yok) | **YOK** |
| 3. Task Assignment | `dispatchTask` L71-L97 | **evet** (tip → agent) |
| 4. Execution | `executeTask` L185-L209 | **kısmi** (ajan yürütmesi stub, L138-L149) |
| 5. Handover (§9) | — (kod yok) | **YOK** |
| 6. Validation | `validateParameters` (ToolCalling L307-L325) | **kısmi** — parametre tipi, çıktı değil |
| 7. Completion (`log.md`, MEMORY) | — (kod yok) | **YOK** |

## §12. `.ai/AGENTS.md` §7.1-§7.2 — Analysis + Pre-flight Checks (alıntı)

Dosya: `.ai/AGENTS.md` · Satır: L261-L266, L270-L274, L281

```markdown
| Keyword çıkarma       | Routing tablosuna başvur      | Bu dosya §6 |
| Domain eşleme         | Dosya uzantısı ve içerik analizi | Bu dosya §5 |
| Öncelik belirleme     | CRITICAL > HIGH > MEDIUM > LOW   | Bu dosya §8 |
| Ajan seçimi           | Birincil + ikincil ajan          | Bu dosya §6 |

| Domain boundary | Doğru ajan        | Layer violation → revert |
| Dosya etkileniyor mu? | Eşzamanlı erişim | Context lock |
| Bağımlılık var mı?    | Handover gerekli  | Transfer başlat |

| Önceki görev başarısız mı? | Retry / escalation | Max 3 retry |
```

**Eşleştirme:** "Öncelik belirleme: CRITICAL > HIGH > MEDIUM > LOW" PHP'de `TASK_TYPES`
satırlarıyla (L41-L49) aynı küme, sıralı karşılaştırma **kodda yok** (§5 notu). "Context lock"
ve "Transfer başlat" satırlarının kod karşılığı yoktur (§27).

## §13. `.ai/AGENTS.md` §8 — Priority Levels (alıntı)

Dosya: `.ai/AGENTS.md` · Satır: L316-L323

```markdown
| Öncelik  | Tanım                     | Timeout | Max Retry | Yanıt Süresi |
|----------|---------------------------|---------|-----------|--------------|
| CRITICAL | Sistem durması, güvenlik açığı | 5s  | 1         | Anlık        |
| HIGH     | Kritik işlev kaybı        | 15s     | 3         | 15s          |
| MEDIUM   | Normal geliştirme görevi  | 30s     | 3         | 30s          |
| LOW      | İyileştirme, optimizasyon | 60s     | 2         | 60s          |
```

| Vault değeri | PHP karşılığı | Eşleşme |
|---|---|---|
| 4 öncelik seviyesi | `critical/high/medium/low` (L41-L49, Contracts L20) | **evet** (küme) |
| Timeout (5s/15s/30s/60s) | — | **YOK** — repo'da bu alan kodu YOK, kaynak: upstream docs |
| Max Retry (1/3/3/2) | — | **YOK** — `retry` grep → 0 (§28) |
| Yanıt süresi hedefi | — | **YOK** — ölçüm/heartbeat yok |

## §14. `.ai/AGENTS.md` §9 — Handover Protocol (alıntı)

Dosya: `.ai/AGENTS.md` · Satır: L327-L354

```text
[Kaynak Agent] → [Handover Request] → [Hedef Agent] → [Onay/Red] → [Confirmation]
```

```markdown
| Alan            | Değer |
|-----------------|-------|
| Konu            | Görevin kısa açıklaması |
| Kaynak Agent    | Adı |
| Hedef Agent     | Adı |
| Öncelik         | CRITICAL / HIGH / MEDIUM / LOW |
| Etkilenen Dosyalar | Dosya yolu listesi |
| İstek           | Ne yapılması gerektiği |
| Onay Durumu     | PENDING / APPROVED / REJECTED |
| Timestamp       | `YYYY-MM-DD HH:MM:SS` (UTC) |

| Onay zorunlu | Hedef agent onayı olmadan tamamlanamaz |
| Timeout      | 30 saniye |
| Max retry    | 3 |
| Red durumunda| MO devreye girer |
| Logging      | Tüm handover'lar `log.md`'ye yazılır |
```

**Kod karşılığı:** 8 alanlı handover mesajı, onay durumu, 30s timeout ve `log.md` logging —
hiçbiri `shared/src/AI/` içinde **DEĞİL** (§27, §28). En yakın yüzey §8'deki tek yönlü
`routeToAgent('security','alert')` çağrısıdır; onay/adım zinciri yoktur.

## §15. `.ai/AGENTS.md` §9.3 — Handover Scenarios (alıntı)

Dosya: `.ai/AGENTS.md` · Satır: L356-L367

```markdown
| Senaryo                | Kaynak  | Hedef    | Öncelik  |
|------------------------|---------|----------|----------|
| Güvenlik açığı tespiti | Backend | Security | CRITICAL |
| DB schema değişikliği  | Backend | Data     | HIGH     |
| Frontend test eksikliği| UI      | QA       | MEDIUM   |
| CI/CD pipeline hatası  | DevOps  | QA       | HIGH     |
| Auth middleware değişikliği | Security | Backend | HIGH |
| Audio DSP optimizasyonu| Embedded| DevOps   | MEDIUM   |
| Vault doküman güncelleme | MO    | vault-updater | LOW |
| Security audit         | Security| QA       | HIGH     |
```

**Kod karşılığı:** 8 senaryonun hiçbiri repo'da temsil edilmiyor; `TASK_TYPES` içindeki 8
görev tipi (L41-L49) **farklı** bir kümедir (recommendation/analysis/eq/hardware/fault/
security-audit/documentation/testing) — senaryo ↔ görev tipi eşlemesi yoktur.

## §16. `.ai/AGENTS.md` §10 — Escalation Protocol (alıntı)

Dosya: `.ai/AGENTS.md` · Satır: L371-L399

```text
Level 1 (Domain Lead) → Level 2 (Tech Lead) → Level 3 (Arch Lead) → İnsan
```

```markdown
| Senaryo                   | Başlangıç | Hedef | Timeout |
|---------------------------|-----------|-------|---------|
| Agent aynı dosyayı değiştiremiyor | L1 | L2 | 30s |
| BCNF çelişkisi            | L1 (Data) | L2 | 30s |
| CSRF/CSP uyumsuzluğu      | L1 (Security) | L2 | 15s |
| Test coverage %80 altı    | L1 (QA)   | L2 | 60s |
| Sistem durması            | L2        | İnsan | Anlık  |

| Kural | Değer |
|-------|-------|
| L1 timeout | 30 saniye |
| L2 timeout | 60 saniye |
| L3 timeout | 120 saniye |
| Max retry | 3 her seviyede |
| İnsan müdahalesi | Son çare |
```

(§10.1 tamamı 9 veri satırıdır — L381-L389; yukarıda 5 satır alıntı, 4 satır kısaltıldı.)
§10.2 tablosu L393-L399'dur: başlık + ayraç + bu 5 satır.

**Kod karşılığı:** seviye zinciri, timeout'lar ve "3 her seviyede" retry — repo'da **YOK**
(`escalat` grep → 0). `AIWorkflow` L252'deki `risk === 'high'` dalı tek seviyeli bildirimdir,
seviye atlaması **yapmaz**.

## §17. `.ai/AGENTS.md` §11 — Health Check (alıntı)

Dosya: `.ai/AGENTS.md` · Satır: L403-L435

```markdown
| Parametre | Değer |
|-----------|-------|
| Timeout | 30 saniye |
| Max Retry | 3 |
| Check Interval | Her görev başında |
| Heartbeat | 10 saniye |

| Durum    | Kod | Açıklama                        |
|----------|-----|---------------------------------|
| Healthy  | 200 | Görev tamamlandı                |
| Degraded | 301 | Yavaş yanıt (>15s)              |
| Retry    | 408 | Timeout, yeniden deneniyor      |
| Failed   | 500 | 3 retry başarısız, queue reset  |
| Dead     | 503 | Yanıt yok, escalation           |
```

```text
Görev başlangıcı → Health check tetikle → Durum kontrolü
  → Healthy → devam · Degraded → uyar, devam · Retry → yeniden dene (max 3)
  → Failed → queue reset, escalation · Dead → derhal escalation
```

**Kod karşılığı:** heartbeat, durum kodları (200/301/408/500/503), queue reset — repo'da **YOK**.
`AIOrchestrator` durum kümesi bunların **değildir**: yalnız `pending|running|completed|failed`
(L83, L188, L196-L200) ve `listActiveTasks` filtresi yalnız `pending|running` (L118).

## §18. `.ai/AGENTS.md` §12 — Context Lock (alıntı)

Dosya: `.ai/AGENTS.md` · Satır: L439-L469

```markdown
| Kilitleme süresi      | Max 30 saniye |
| Deadlock prevention   | MO en eski kilidi kırar |
| Öncelik               | CRITICAL > HIGH > MEDIUM > LOW |
| Logging               | Lock acquire/release `log.md`'ye yazılır |
```

```text
Ajan dosyaya erişmek ister
  → Lock acquire (max 30s bekleme)
    → Başarılı → dosyayı düzenle
    → Başarısız → kuyruk → öncelik sırası
      → Timeout → escalation
```

```markdown
| Timeout            | Max 30s sonra lock serbest |
| Priority override  | CRITICAL diğer kilidi kırar |
| MO intervention    | MO en eski kilidi kırar |
| Queue reset        | Tüm kilitler sıfırlanır |
```

**Kod karşılığı:** kilitleme, bekleme kuyruğu, override, `log.md` kaydı — repo'da **YOK**
(`Lock|mutex|flock` grep → 0, §28). Context alanı `AIOrchestrator::updateContext` (L162-L170)
kilitlenmemiş `array_merge`'dir: iki eşzamanlı yazımda son yazan kazanır.

## §19. `.ai/AGENTS.md` §6 — Keyword → Agent Routing (alıntı)

Dosya: `.ai/AGENTS.md` · Satır: L230-L242

```markdown
| Keyword Grubu | Birincil Agent | İkincil Agent |
| API, endpoint, routing, middleware, PHP, controller, repository | Backend Architect | Security Engineer |
| CSRF, CSP, XSS, OWASP, auth, encryption, security, session, rate limit | Security Engineer | Backend Architect |
| database, SQL, BCNF, migration, query, schema, MySQL, PDO, index | Data Engineer | Backend Architect |
| C++, ASIO, JUCE, audio, DSP, Neva Engine, ring buffer, WASAPI, hardware | Embedded Engineer | DevOps Engineer |
| test, coverage, PHPUnit, Vitest, Playwright, E2E, unit test, integration | QA Engineer | — |
| vault, documentation, ADR, wiki-link, index, keys, brain | MO (vault-updater) | — |
```

(9 keyword grubu; 3 satır kısaltıldı — tam tablo L232-L242.)

**Eşleştirme:** vault tablosu **ikincil (fallback) ajan** taşır; PHP `AGENT_MAP`/`TASK_TYPES`
tek ajan atar (`'agent'` alanı, L41-L49) → ikincil ajan alanı repo'da **YOK** (§27).

## §20. İllüstratif — Python: asyncio Task Queue + Escalation

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

> Kaynak (doğrulandı 2026-10-17): `https://docs.python.org/3/library/asyncio-queue.html` —
> `asyncio.PriorityQueue` "retrieves entries in priority order (**lowest first**)"; girişler
> `(priority_number, data)` biçimindedir; asyncio kuyrukları **thread-safe değildir**; timeout
> için `asyncio.wait_for()` kullanılır.

```python
import asyncio
import itertools

PRIORITY_RANK = {"critical": 0, "high": 1, "medium": 2, "low": 3}   # §8: CRITICAL > LOW
L_TIMEOUTS = {"L1": 30, "L2": 60, "L3": 120}                        # AGENTS §10.2
MAX_RETRY = 3                                                       # AGENTS §10.2
seq = itertools.count()                                             # aynı öncelikte FIFO

async def dispatch(q: asyncio.PriorityQueue, task: dict) -> None:
    rank = PRIORITY_RANK[task["priority"]]
    await q.put((rank, next(seq), task))                            # lowest-first: critical=0

async def worker(name: str, q: asyncio.PriorityQueue) -> None:
    while True:
        rank, _, task = await q.get()
        attempt = 0
        while True:
            try:
                await run_task(task)                                # repo'da karşılığı yok (§5)
                break
            except TimeoutError:
                attempt += 1
                if attempt >= MAX_RETRY:
                    level = escalate(task)                          # L1 → L2 → L3 → insan
                    print(name, "escalated", level, task["id"])
                    break
                await asyncio.sleep(min(attempt, 5))
        q.task_done()

async def run_task(task: dict) -> None: ...                         # yer tutucu — uydurma değil
async def escalate(task: dict) -> str: ...                          # yer tutucu — uydurma değil
```

**Kapsam:** öncelikli kuyruk + retry + eskalasyon — repo karşılığı **YOK** (§5, §16); sıra
çevrimi (`critical=0`) düşük-önce davranışını §8'in yüksek-önce tablosuna çevirir.

## §21. İllüstratif — TypeScript: Priority Queue (min-heap, FIFO bağlayıcı)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```typescript
export type Priority = "critical" | "high" | "medium" | "low";   // AGENTS §8 / Contracts L20
const RANK: Record<Priority, number> = { critical: 0, high: 1, medium: 2, low: 3 };

type Entry<T> = { rank: number; seq: number; value: T };

export class PriorityQueue<T> {
  private heap: Entry<T>[] = [];
  private seq = 0;

  push(value: T, priority: Priority): void {
    this.siftUp(this.heap.push({ rank: RANK[priority], seq: this.seq++, value }) - 1);
  }

  pop(): T | undefined {
    const top = this.heap[0];
    const last = this.heap.pop();
    if (this.heap.length && last) { this.heap[0] = last; this.siftDown(0); }
    return top?.value;
  }

  private less(a: Entry<T>, b: Entry<T>): boolean {
    return a.rank !== b.rank ? a.rank < b.rank : a.seq < b.seq;    // eşit öncelikte sırayı koru
  }
  private siftUp(i: number): void { /* ebeveyn-çocuk takası */ }
  private siftDown(i: number): void { /* çocuk takası */ }
}
```

**Kapsam:** `siftUp`/`siftDown` gövdeleri kasıtlı olarak boşaltıldı (yer tutucu — uydurma değil).
Repo karşılığı: `AIOrchestrator::$tasks` sıralı **değildir** — `listActiveTasks` (L114-L120)
filtreler, sıralamaz.

## §22. İllüstratif — Go: Worker Pool Dispatcher (fan-out)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

> Kaynak (doğrulandı 2026-10-17): `https://go.dev/blog/pipelines` (Sameer Ajmani, 13 Mart 2014)
> — **fan-out**: aynı kanalı okuyan çoklu goroutine ile işi dağıtma; **fan-in**: `merge` ile
> toplama; iptal: `done` kanalını **kapatmak** tüm sender'lara broadcast'tir; sınırlı eşzamanlılık
> `const numDigesters = 20` sabit goroutine havuzu ile yapılır.

```go
type Task struct {
    ID, Type, Agent, Priority string
    Params map[string]any
}
type Result struct {
    ID, Agent string
    Value     any
    Err       error
}

// Dispatch: gelen görevleri sabit sayıda worker'a dağıtır (fan-out).
func Dispatch(done <-chan struct{}, in <-chan Task, workers int) <-chan Result {
    out := make(chan Result)
    var wg sync.WaitGroup
    wg.Add(workers)
    for i := 0; i < workers; i++ {
        go func() {
            defer wg.Done()
            for t := range in {
                v, err := run(t)                       // repo'da karşılığı: executeTask (L185)
                select {
                case out <- Result{ID: t.ID, Agent: t.Agent, Value: v, Err: err}:
                case <-done:                           // iptal broadcast'i
                    return
                }
            }
        }()
    }
    go func() { wg.Wait(); close(out) }()              // fan-in: tüm worker bitince kapat
    return out
}
```

**Kapsam:** worker havuzu + iptal — repo'da `dispatchTask` L94'te **tek** iş parçacığı, anlık
(`executeTask`) yürütür; havuz/kanal yok.

## §23. İllüstratif — Rust: Tokio Üzerinden Retry + Escalation

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

> Kaynak (doğrulandı 2026-10-17): `https://docs.rs/tokio/latest/tokio/sync/index.html`
> (tokio 1.53.3) — `mpsc` kanal kapasitesi **backpressure** için kritiktir; `oneshot` tek
> sonuçlu request/response için; `Semaphore` eşzamanlılığı sınırlar; `Mutex`/`RwLock` async
> karşılıklardır ve `_timeout` metodları hariç runtime-agnostiktir.

```rust
use std::future::Future;
use std::time::Duration;

/// AGENTS §8/§10.2: Max Retry 3 · L1=30s, L2=60s, L3=120s
pub const MAX_RETRY: u32 = 3;
pub const LEVEL_TIMEOUTS_MS: [u64; 3] = [30_000, 60_000, 120_000];

pub async fn with_retry<T, F, Fut>(mut op: F) -> Result<T, (u32, String)>
where
    F: FnMut(u32) -> Fut,
    Fut: Future<Output = Result<T, String>>,
{
    let mut attempt = 0u32;
    loop {
        match op(attempt).await {
            Ok(v) => return Ok(v),
            Err(e) if attempt + 1 >= MAX_RETRY => {
                let level = ((attempt + 1) as usize).min(2);   // L1 → L2 → L3
                return Err((level as u32, format!("escalate L{}: {}", level + 1, e)));
            }
            Err(_) => {
                attempt += 1;
                tokio::time::sleep(Duration::from_millis(200 * u64::from(attempt))).await;
            }
        }
    }
}
```

**Kapsam:** retry + seviye atlama — repo'da `retry` grep → 0 (§28); `catch \Throwable` tek
deneme ile biter (L204-L208). Backfill notu: bu blokta `mpsc`/`Semaphore` kullanılmadı —
eşzamanlılık sınırı gerekiyorsa §22'deki havuz ile birlikte düşünülür.

## §24. İllüstratif — C++: Thread Pool + Context Lock

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı (C++ reference URL'si bu
> turda doğrulanmadı → `⚠️ VERIFICATION REQUIRED`)

```cpp
#include <condition_variable>
#include <deque>
#include <functional>
#include <mutex>
#include <thread>
#include <vector>

class ThreadPool {
    std::mutex mtx;
    std::condition_variable cv;
    std::deque<std::function<void()>> jobs;
    bool stop = false;
    std::vector<std::thread> workers;
public:
    explicit ThreadPool(std::size_t n) {
        for (std::size_t i = 0; i < n; ++i)
            workers.emplace_back([this] {
                for (;;) {
                    std::function<void()> job;
                    {   std::unique_lock lk(mtx);
                        cv.wait(lk, [this] { return stop || !jobs.empty(); });
                        if (stop && jobs.empty()) return;
                        job = std::move(jobs.front()); jobs.pop_front();
                    }
                    job();                          // havuz dışı iş = AGENTS §7 adım 4
                }
            });
    }
    void push(std::function<void()> j) {
        { std::lock_guard lk(mtx); jobs.push_back(std::move(j)); }   // kuyruk → §12.2
        cv.notify_one();
    }
    ~ThreadPool() { { std::lock_guard lk(mtx); stop = true; } cv.notify_all();
                    for (auto& t : workers) t.join(); }
};
```

**Kapsam:** iş havuzu + mutex — bu, **görev** havuzudur; AGENTS §12'deki **dosya** context
lock'u (30s, CRITICAL override, `log.md` kaydı) bu koddan **farklı** bir katmandır ve repo'da
YOK (§28). Neva Engine/C++20 yüzeyi dışında bu dizinde C++ dosyası yoktur.

## §25. İllüstratif — C#: Channel-Based Handoff

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

> Kaynak (doğrulandı 2026-10-17): `https://learn.microsoft.com/en-us/dotnet/api/system.threading.channels.channel-1`
> — `Channel<T>` okuma/yazma yarısını (`Reader`, `Writer`) ayırır; `Channel<T>` soyut sınıftır
> ve `Channel<TWrite,TRead>`'ten türer (ms.date 2025-07-01).

```csharp
using System.Threading.Channels;

public sealed record Handover(
    string Topic, string SourceAgent, string TargetAgent, string Priority,   // §9.1 alanları
    string[] AffectedFiles, string Request, string Status, DateTimeOffset At);

var ch = Channel.CreateBounded<Handover>(new BoundedChannelOptions(64) {
    SingleWriter = false, SingleReader = false, FullMode = BoundedChannelFullMode.Wait
});

// Kaynak ajan: mesajı yazar, akışı kapatır (§9.1 formatı — repo'da karşılığı yok, §27)
await ch.Writer.WriteAsync(new Handover("DB schema", "backend", "data", "HIGH",
    new[] { "shared/src/Database/x.php" }, "BCNF migration", "PENDING", DateTimeOffset.UtcNow));
await ch.Writer.CompleteAsync();

// Hedef ajan: drenaj eder, onaylar (§9.2: onay zorunlu, timeout 30s, max retry 3)
await foreach (var h in ch.Reader.ReadAllAsync()) {
    var approved = await ApproveAsync(h, TimeSpan.FromSeconds(30));
    await LogAsync(approved);                         // §9.2: log.md'ye yazılır
}
```

**Kapsam:** el sıkışmalı transfer kanalı — `ApproveAsync`/`LogAsync` yer tutucudur (uydurma
değil); repo'da bu akışın PHP kodu **YOK** (§14, §28).

## §26. Kavram ↔ Eşleşme Tablosu (vault kavramı → repo kod karşılığı)

| Vault kavramı (.ai/AGENTS.md) | Repo kod karşılığı | Eşleşme |
|---|---|---|
| Routing: keyword → birincil/ikincil ajan (§6) | `AGENT_MAP` L26-L38 + `TASK_TYPES` L40-L49 | **kısmi** (ikincil ajan yok) |
| Task dispatch 7 adım (§7) | `dispatchTask` L71-L97 + `executeTask` L185-L209 | **kısmi** (adım 1/2/5/7 yok) |
| Öncelik seviyeleri (§8) | `TASK_TYPES` priority alanları L41-L49 | **kısmi** (küme var, timeout/retry yok) |
| Öncelikli kuyruk (TaskQueue) | yorum satırı L20 + "Priority weighted execution" L93 | **YOK** (uygulama yok) |
| Handover protokolü (§9) | — | **YOK** |
| Handover senaryoları (§9.3) | — | **YOK** |
| Escalation (§10) | — | **YOK** |
| Health check / heartbeat (§11) | — | **YOK** |
| Context lock (§12) | `updateContext` `array_merge` L162-L170 (kilitlenmemiş) | **YOK** |
| Domain boundary (§5) | `AGENT_PERMISSIONS` L28-L40 + `checkPermission` L145-L166 | **evet** |
| Redaction `[REDACTED]` (§17 #3) | — | **YOK** |
| Görev durumu | `pending/running/completed/failed` L83, L188, L196-L200 | **evet** |
| Aktif görev listesi | `listActiveTasks` L114-L120 | **evet** |
| Session context | `getContext`/`updateContext` L154-L170 | **evet** |
| Ajan bildirimi (tek yönlü) | `routeToAgent('security','alert')` AIWorkflow L254 | **kısmi** |
| Tool izni / yetki kapısı | `checkPermission` L145-L166 | **evet** |

## §27. KOD YOK Alanlar (yokluk cümleleri — tahmin yasak)

| Alan | Durum (2026-10-17) |
|---|---|
| `TASK_PRIORITY` sabiti (görev metninde anılan) | Repo'da bu alan kodu YOK — kaynak: upstream docs (grep 0) |
| Gerçek öncelikli kuyruk (sıralama/bekleme/worker) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Handover mesajı + onay durumu (PENDING/APPROVED/REJECTED) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Escalation seviye zinciri (L1→L2→L3→İnsan) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Retry döngüsü (max 1/3/3/2) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Timeout değerleri (5s/15s/30s/60s) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Context lock / deadlock prevention / lock logging | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Health check durum kodları + heartbeat (10s) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| `log.md`'ye yazan dispatch/handover logger | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| `[REDACTED]` maskeleme uygulaması (PHP) | Repo'da bu alan kodu YOK — kaynak: upstream docs (yalnız vault §17 L200) |
| İkincil/fallback ajan ataması | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Pre-flight kontrolü (mockup/şablon okundu mu) | Repo'da bu alan kodu YOK — kaynak: upstream docs |

## §28. Negatif Ölçüm — Grep Sayaçları (araç çıktısı, 2026-10-17)

| Arama | Kapsam | Sonuç |
|---|---|---|
| `handover` | `shared/src/AI/*.php` (7 dosya) | **0 isabet** |
| `escalat` | `shared/src/AI/*.php` | **0 isabet** |
| `retry` | `shared/src/AI/*.php` | **0 isabet** |
| `Lock` / `mutex` / `flock` / `Semaphore` | `shared/src/AI/*.php` | **0 / 0 / 0 / 0** |
| `PriorityQueue` | `shared/src/AI/*.php` | **0 isabet** |
| `queue` | `shared/src/AI/*.php` | **1 isabet** — AIOrchestrator L20 yorumu `TaskQueue (priority queue)` |
| `TASK_PRIORITY` | `shared/src/AI` + `shared/src/Contracts/AI` | **0 isabet** |
| `handover|escalat|retry|[Ll]ock|queue` | `shared/src/Contracts/AI/*.php` (6 dosya) | **0 / 0 / 0 / 0 / 0** |
| `AIOrchestrator` (kullanım) | `shared/src/**` | 2 dosya: AIOrchestrator.php · AIWorkflow.php |
| `priority` | `shared/src/Contracts/AI/*.php` | **3 isabet** (docblock satırları) |

## §29. Güvenlik Notları (kanıt ile)

| Not | Kanıt | Risk / Kural |
|---|---|---|
| **CRITICAL override dikkati:** vault "CRITICAL diğer kilidi kırar" der; yetki kodda yok | AGENTS §12.3 L467 · repo grep 0 (§28) | Kilit kırma yetkisi uygulanırsa **yalnız MO** ve **yalnız log'lanarak** verilmeli; sıradan ajanlara verilirse eşzamanlı yazım bozulur; öncelik sızıntısı `critical` sahte etiketleme demektir |
| Hassas veri redaction | AGENTS §17 #3 L200 (`[REDACTED]`) · §7.6 L305 | Dispatch parametreleri (`$params`) ham saklanır (L86) — log'a yazarken maskeleme zorunlu |
| Fail-closed dispatch | AIOrchestrator L73-L75 (bilinmeyen tip → exception) | Yeni tip eklenmeden tabloya erişim engellenir |
| Fail-safe izin | ToolCalling L147-L163 (3 kol → `allowed:false`) | İzin yoksa **reddedilir**, hata fırlatılmaz |
| SQL yüzeyi | ToolCalling L233-L237 (`Only SELECT queries are allowed`) | `db-query` yalnız SELECT; AIWorkflow L122-L126 prepared statement kullanır |
| Injection filtresi (dispatch girdisi) | PromptEngine L27-L35 (7 desen L28-L34) | Görev parametresi LLM'e giderken `[FILTERED]` |
| Ajan yetki eşlemesi | `AGENT_PERMISSIONS` L28-L40 | `mo` en geniş yetki (6 kategori) — genişletilmiş rol bilinçli mi? (§32) |

## §30. Ölçüm Kalıpları (bu dosyanın kapıları için)

| Amaç | Komut (repo kökünden, PowerShell) |
|---|---|
| Satır sayısı | `[IO.File]::ReadAllLines($hedef,[Text.Encoding]::UTF8).Count` |
| Kapı | sonuç `>= 500` → PASS · bant 500-520 yalnız `<500` durumunda uygulanır (üst sınır kapalı) |
| Frontmatter 10 alan | ilk 14 satırda `node_id…group` 10 anahtar; `title`/`parent` **yok** |
| Authority son satır | `$lines[-1]` birebir `**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode` |
| Mojibake | `ReadAllText` içinde U+FFFD ara → hedef 0 |
| PHP envanteri | `Get-ChildItem shared\src\AI\*.php` → 7 · satır sayaçları §2 tablosu |
| Negatif grep | `Select-String -Path 'shared\src\AI\*.php' -Pattern 'handover' \| Measure-Object` → 0 |
| Section sayısı | `Select-String -Pattern '^## §'` → en az 25 |

## §31. Web Doğrulama Kaynakları (2026-10-17, 3+ resmi kaynak)

| # | URL | Ne doğrulandı | Durum |
|---|---|---|---|
| 1 | `https://docs.python.org/3/library/asyncio-queue.html` | `PriorityQueue` lowest-first + `(priority_number, data)`; thread-safe değil; `wait_for` timeout; `QueueShutDown` (3.13+); sayfa "Last updated on Oct 17, 2026" | ✅ doğrulandı |
| 2 | `https://go.dev/blog/pipelines` | fan-out/fan-in, `done` kanalı kapatma = broadcast iptal, `numDigesters = 20` havuz sınırlaması | ✅ doğrulandı |
| 3 | `https://learn.microsoft.com/en-us/dotnet/api/system.threading.channels.channel-1` | `Channel<T>` → `Channel<TWrite,TRead>` mirası; `Reader`/`Writer` yarıları; `System.Threading.Channels.dll` | ✅ doğrulandı |
| 4 | `https://docs.rs/tokio/latest/tokio/sync/index.html` | tokio 1.53.3; `mpsc` kapasite = backpressure; `oneshot` request/response; `Semaphore` eşzamanlılık sınırı | ✅ doğrulandı |
| — | `https://learn.microsoft.com/en-us/dotnet/standard/channels` | — | ❌ **404** — kullanılmadı, uydurulmadı |
| — | C++ standard kılavuz URL'si | — | `⚠️ VERIFICATION REQUIRED` (bu turda çekilmedi) |

## §32. Açık Sorular

| # | Soru | Etki |
|---|---|---|
| 1 | `TASK_PRIORITY` ayrı bir sabit mi isteniyor? (Görev metninde anıldı, diskte grep 0 — öncelikler `TASK_TYPES` içinde) | Doküman/sözleşme tutarlılığı |
| 2 | `// Priority weighted execution` (L93) gerçek sıralama planı mı? Kod yok — sıralama `listActiveTasks`'a mı taşınacak? | §8 timeout/retry ile tutarlılık |
| 3 | `routeToAgent` ajanı neden çalıştırmıyor (yalnız metadata echo, L138-L149)? Gerçek yürütme hangi katmanda? | §7 adım 4'ün gerçek sahibi |
| 4 | Handover/lock/escalation/health vault'ta kural olarak duruyor — kod karşılığı için ADR/plan var mı? (`.ai/.decisions/index.md` bu turda okunmadı) | §27'deki 12 satırın kapanması |
| 5 | `mo`'nun 6 kategorili geniş yetkisi (L29) bilinçli en geniş rol mü, `data`/`qa` ile eşitlenmeli mi? | Yetki asgari prensibi |

## §33. Doğrulama Kapıları

| # | Kapı | Kriter | Durum |
|---|---|---|---|
| 1 | Satır sayısı | `>= 500` (bant 500-520 yalnız alt sınırdaysa) | §30 ölçümü |
| 2 | Frontmatter + Authority | 10 alan; `title`/`parent` yok · son satır birebir | gözden geçirme |
| 3 | PHP kanıtı | her alıntı gerçek `Dosya:`/`Satır:` (okunmuş aralık) | 2026-10-17 okuma |
| 4 | Vault alıntısı | §6-§12 blokları `.ai/AGENTS.md` satır aralığıyla | 2026-10-17 okuma |
| 5 | İllüstratif etiket | 6 dilde blok + üstünde zorunlu etiket | §20-§25 |
| 6 | Yokluk cümleleri | 12 satır "Repo'da bu alan kodu YOK" | §27 |
| 7 | Negatif grep | 10 satır sayaç (0'lar gerçek ölçüm) | §28 |
| 8 | Placeholder + Mojibake | üretimde bekleyen `{{…}}` yok · U+FFFD 0 | grep · §30 |

## §34. Yasaklar (bu dosya ve bu alt-dal)

| Yasak | Kural kaynağı |
|---|---|
| `git commit` atmak | görev manifesti + AGENTS §16.5 — commit orkestratöre aittir |
| `shared/src/**` ve `.ai/*.md` yazımı (salt-okunur) · bu dosya dışına yazım | görev manifesti |
| Uydurma `Dosya:`/`Satır:` künyesi · illüstratif bloğa repo kanıtı süsü vermek | Zero-Hallucination (AGENTS §3) · bu dosya §1 |
| Uydurma URL / 404 kaynağı kullanmak | §31 (404 kaydı korunur, temizlenmez) |
| Dosya adını değiştirmek · `title`/`parent` alanlarını frontmatter'a eklemek | görev manifesti · FM şeması |

## §35. Gate Özeti

§1-§35: künye formatı 10 kural · PHP envanteri 7 dosya (**1988 satır** toplam: 382+210+271+232+242+325+326) ·
PHP kanıt künyesi 9 (§2-§10: AIOrchestrator ×4, AIWorkflow ×2, ToolCalling, Contracts, envanter) ·
`.ai/AGENTS.md` alıntıları 9 blok (§6, §7, §7.1-§7.2, §8, §9, §9.3, §10, §11, §12) ·
illüstratif **6 blok / 6 dil** (Python, TypeScript, Go, Rust, C++, C#) · kavram↔eşleşme 16 satır ·
yokluk 12 satır · negatif grep 10 satır · güvenlik 7 satır · ölçüm 8 satır ·
web doğrulama **4 URL ✅** + 1 (404, §31) + 1 `⚠️ VERIFICATION REQUIRED` (C++) · açık soru 5 · kapı 8 → kapılar: `>=500` · FM 10 alan · Authority son satır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
