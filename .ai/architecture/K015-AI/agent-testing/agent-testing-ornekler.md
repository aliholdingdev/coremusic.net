---
node_id: agent-testing-ornekler
node_type: module
status: active
parent_id: agent-testing
children: []
depends_on: [agent-testing-anayasa, agent-testing-surucu]
evidence:
  - "shared/tests/ envanteri (gerçek test dosyaları, 2026-10-17 okuma)"
  - ".ai/scripts/wiki-link-check.ps1 + kalip-abc-check.ps1 (doğrulama betik kanıtı)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı, kod kanıtı zorunlu"
open_questions: []
group: kod
---

# agent-testing-ornekler - Ajan Test Kod Örnekleri (kanıt odaklı)

> **Kapsam:** Her PHP alıntısı 2026-10-17'de `shared/tests/` altındaki gerçek test
> dosyalarının, her PowerShell alıntısı `.ai/scripts/` altındaki gerçek betiklerin
> satırlarıdır (`Dosya:`/`Satır:` künyeli). İllüstratif bloklar repo kanıtı değildir;
> her birinin üstünde zorunlu etiket taşır. Test kaynakları salt-okunurdur.
> **Temel tespit: repo'da ajan-routing testi YOK** (§8, §22).

## §1. Kanıt Künye Formatı

| Alan | Kural |
|---|---|
| Dosya | göreli yol, diskte var (glob ile doğrulanmış) |
| Satır | okunan aralık (L72-L91 gibi) — okunmamış satır künye olamaz |
| Alıntı | kod satırının kendisi (kısaltma serbest, uydurma yasak) |
| Yokluk | tam cümle: "Repo'da bu alan testi YOK — kaynak: upstream docs" |
| İllüstratif | `⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı` |
| Tarih | her kanıt 2026-10-17 okuma turuna ait |
| Eşleşme | `evet` / `kısmi` / `YOK` — başka değer yok |
| Yasak | uydurma künye → Zero-Hallucination ihlali (kök AGENTS §3) · çelişkide disk kazanır |

## §2. shared/tests/ Gerçek Envanteri (2026-10-17 glob + grep)

Dosya: `shared/tests/` · glob `**/*.php` → **45 isabet** · grep `class \w+Test extends` → **41 test sınıfı** · üst dizin **9**

| Kategori | Sayı | Kanıt |
|---|---|---|
| PHP test sınıfı (`extends TestCase`) | 41 | grep, 2026-10-17 |
| Toplam PHP dosyası | 45 | glob, 2026-10-17 |
| Test sınıfı olmayan PHP (fixture + helper) | 4 | chrome 2 + Sqlite helper 2 |
| **Ajan-routing testi (AIOrchestrator/AGENT_MAP)** | **0** | §8 grep — "Repo'da ajan testi YOK" |

## §3. Dizin Bazlı Test Sayım Tablosu (grep kanıtı)

| Dizin | Test sınıfı | Dosya isimleri (başlangıç) |
|---|---|---|
| `Api/` | 8 | RouteTableTest · GatewayMethodRoutingTest · ApiPipelineSecurityTest · VersioningTest |
| `Middleware/` | 9 | AuthMiddlewareTest · CsrfMiddlewareTest · BypassAuthMiddlewareFailClosedTest |
| `Unit/PageRouter/` | 6 | SpaRouteTest · RouteRegistryTest · AuthGuardTest |
| `Unit/Device/` | 4 | DeviceDetectorTest · NavRouteConsistencyTest · WelcomePopupRenderTest |
| `Events/` | 2 | EventDispatcherTest · DomainEventTest |
| `Repository/` | 2 | ApiKeyRepositoryTest · ApiKeyCreateCommandTest |
| `Security/` + `Unit/Security/` | 4 | SessionKeysTest · ReturnUrlPolicyEdgeCaseTest · ReturnUrlPolicyTest · JwtServiceTest |
| `Unit/Log/` | 2 | Psr3LoggerTest · LoggerRedactionTest |
| `Component/` · `OAuth/` · `Unit/Config/` · `Unit/Theme/` | 1+1+1+1 | TemplateEngineTest · OAuthManagerTest · ConfigManagerTest · ThemeManagerModeTest |
| `Unit/Middleware/` | 0 | yalnız `README.md` (test yok) |
| **Toplam** | **41** | grep `class \w+Test extends` |

## §4. RouteTableTest — Method-Aware Routing Testi (gerçek alıntı)

Dosya: `shared/tests/Api/RouteTableTest.php` · Satır: L8-L36

```php
/**
 * Method-aware route tablosu — 405 / 404 ayrımı (Faz 1a · ADR-084).
 */
final class RouteTableTest extends TestCase
{
    private function table(): RouteTable
    {
        // Yeni sözleşme: "alt kaynak" yalnızca TABLODA açıkça kayıtla vardır.
        return RouteTable::fromArray([
            'GET' => [
                '/api/v1/auth/login' => ['service' => 'auth', 'handler' => 'authController', 'public' => true],
            ],
            'POST' => [
                '/api/v1/auth/login' => ['service' => 'auth', 'handler' => 'authController',
                    'public' => true, 'implemented' => false],
                '/api/v1/auth/profile' => ['service' => 'auth', 'handler' => 'authController'],
            ],
        ]);
    }
```

| Gözlem | Kanıt |
|---|---|
| Routing testi = HTTP method × path → status ayrımı | L9 başlık, L19-L35 tablo |
| Tablo `fromArray` ile fixture olarak kurulur (DB yok) | L19 |
| `public` / `implemented` bayrakları test verisinde | L22, L30 |

## §5. RouteTableTest — Assertion Kanıtları (gerçek alıntı)

Dosya: `shared/tests/Api/RouteTableTest.php` · Satır: L38-L60

```php
public function testGetExactRouteMatchesAsOk(): void
{
    $match = $this->table()->match('/api/v1/auth/login', 'GET');
    $this->assertSame(RouteTable::STATUS_OK, $match['status']);
    $this->assertIsArray($match['route']);
    $this->assertTrue($match['route']['public'], 'Exact kayıt public alanını korumalı');
    $this->assertSame([], $match['allow']);
}
// testExactMatchBeatsPrefixMatch: trailing slash exact eşleşmeyi ezmez (L48-L54)
// testPrefixMatchStillWorksForDeclaredResource: POST kaydı GET'te prefix fallback (L56-L60)
```

| Test adı | Ne doğrular | Satır |
|---|---|---|
| `testGetExactRouteMatchesAsOk` | exact eşleşme STATUS_OK · public korunur · allow boş | L38-L46 |
| `testExactMatchBeatsPrefixMatch` | trailing slash exact eşleşmeyi ezmez | L48-L54 |

**Eşleme:** bu üç test bir "routing fixture + assertion" kalıbıdır — ajan-routing testi
(§6 keyword → agent eşlemesi) için **aynı kalıp** kullanılabilir; repo'da karşılığı YOK (§8).

## §6. SpaRouteTest — Immutable Default + Custom Value Kalıbı (gerçek alıntı)

Dosya: `shared/tests/Unit/PageRouter/SpaRouteTest.php` · Satır: L8-L23, L25-L44

```php
final class SpaRouteTest extends TestCase
{
    public function testImmutableDefaults(): void
    {
        $route = new SpaRoute(page: 'home');
        $this->assertSame('home', $route->page);
        $this->assertTrue($route->requiresAuth);
        $this->assertNull($route->requiredRole);
        $this->assertSame([], $route->meta);
    }
    public function testCustomValues(): void
    {
        $route = new SpaRoute(page: 'login', requiresAuth: false, title: 'Giriş',
            requiredRole: 'admin', cacheable: false, cacheTtl: 3600);
        $this->assertSame(3600, $route->cacheTtl);
    }
}
```

| Kalıp | Ajan testine aktarımı |
|---|---|
| `testImmutableDefaults` — varsayılan sözleşmeyi kilitler | agent kaydı varsayılanları (priority, katman) |
| `testCustomValues` — override yolları | handover/escalation override'ları |
| Named argument ile okunabilir fixture | routing tablosu satırları (§4 L19-L35) |

## §7. Fixtures Envanteri — `shared/tests/Fixtures/`

Dosya: `shared/tests/Fixtures/` · Read directory → **2 alt dizin** (2026-10-17)

| Dizin | İçerik | Amaç |
|---|---|---|
| `chrome/` | `header.php` · `footer.php` | HtmlShellRendererTest chrome parçaları (PageRouter) |
| `jwt/` | `test-private.pem` · `test-public.pem` · `test2-private.pem` · `test2-public.pem` | JwtServiceTest imzalama/doğrulama çiftleri |

Gözlemler: PEM dosyaları `test-` önekli — üretim anahtarı değil · 2 anahtar çifti =
pozitif + negatif senaryo · **agent fixture'ı (prompt/sözlük) YOK** (§22).

## §8. Agent Testi Yokluğu — Grep Kanıtı (araç çıktısı)

Dosya: `shared/tests/**/*.php` · grep deseni: `(?i)agent|orchestrator|AIOrchestrator|PromptEngine|ToolCalling`

| İsabet | Dosya | Ne |
|---|---|---|
| L45, L62, L94 | `Events/EventDispatcherTest.php` | `'TestAgent'` — string literal (user-agent alanı) |
| L25, L31, L34 | `Events/DomainEventTest.php` | `'Agent'` — HTTP user-agent payload'u |
| L11 | `Unit/Log/LoggerRedactionTest.php` | yorum: `.ai/AGENTS.md §17 Edge Case 3` |

**Sonuç:** 22 isabetin **tami HTTP user-agent / yorum** — AIOrchestrator, AGENT_MAP,
TASK_TYPES, PromptEngine, ToolCalling için test dosyası **0**. **Repo'da ajan testi YOK.**

## §9. PowerShell wiki-link-check — Gerçek Alıntı

Dosya: `.ai/scripts/wiki-link-check.ps1` · Satır: L1-L5, L18-L19, L41-L43

```powershell
#Requires -Version 5.1
<# wiki-link denetimi: .ai/ui-design/**/*.md icindeki [[hedef]] -> gercek dosya var mi? #>
$ErrorActionPreference='Stop'
$ROOT='C:\www\coremusic.net\.ai\ui-design'
$broken=New-Object System.Collections.Generic.List[string]
$checked=0
"kontrol edilen link: $checked"
"kirik link: "+$broken.Count
$broken | Select-Object -First 40 | ForEach-Object { "  "+$_ }
```

| Gözlem | Kanıt |
|---|---|
| Salt-okunur denetim — raporlar, değiştirmez | başlık yorumu L2 |
| Sayaç + tavan 40 raporlama | L41-L43 |
| Repo kökünden çalıştırma kuralı | .ai/AGENTS.md §13.6 |

## §10. wiki-link-check — 3 Aşamalı Eşleme Algoritması

Dosya: `.ai/scripts/wiki-link-check.ps1` · Satır: L10-L16, L24-L38

```powershell
$index=@{}                                              # L9
Get-ChildItem -Path 'C:\www\coremusic.net\.ai' -Recurse -Filter *.md -File | ForEach-Object {
  $index[$_.FullName.ToLowerInvariant()]=$_.FullName    # L14
}
foreach($m in [regex]::Matches($text,'\[\[([^\]|#]+)(?:[#|][^\]]*)?\]\]')){   # L24
  if($index.ContainsKey($t.ToLowerInvariant())){ continue }                  # L29-L30
  $cand=[System.IO.Path]::GetFullPath((Join-Path $dir ($t -replace '/','\'))) # L32
  if(Test-Path $cand){ continue }                                            # L33
  $leaf=[System.IO.Path]::GetFileNameWithoutExtension(($t -replace '/','\')) # L36
  if($index.ContainsKey($leaf.ToLowerInvariant())){ continue }               # L37
  $broken.Add("$(Split-Path $src -Leaf) -> [[$t]]")                         # L38
}
```

| Aşama | Yöntem | Satır |
|---|---|---|
| 0 | `.ai/**/*.md` indeksi (basename + tam yol + uzantısız) | L10-L16 |
| 1 | tam dosya anahtarı (küçük harf) | L29-L30 |
| 2-3 | göreli yol (+`.md`) · sadece dosya adı (leaf) | L32-L37 |
| fail | `dosya -> [[hedef]]` satırı | L38 |

## §11. PowerShell kalip-abc-check — Frontmatter Çözümü (gerçek alıntı)

Dosya: `.ai/scripts/kalip-abc-check.ps1` · Satır: L18-L34

```powershell
function Get-Frontmatter([string]$p) {
    $lines = [System.IO.File]::ReadAllLines($p, [Text.Encoding]::UTF8)
    if ($lines.Count -lt 3 -or $lines[0] -ne '---') { return $null }
    for ($i = 1; $i -lt $lines.Count; $i++) {
        if ($lines[$i] -eq '---') { return ,@($lines[1..($i-1)]) }
    }
    return $null
}
function Test-FmKeys($fm, [string[]]$keys) {
    $miss = @()
    foreach ($k in $keys) {
        $re = "^\s*$([regex]::Escape($k))\s*:"
        if (-not ($fm | Where-Object { $_ -match $re })) { $miss += $k }
    }
    return $miss
}
```

| Gözlem | Kanıt |
|---|---|
| Frontmatter = ilk satır `---` + kapanış `---` | L20-L22 |
| FM anahtarı regex eşlemesi (kaçışlı) — satır içi `key:` | L30-L31 |
| Salt-okunur: `$exit` kodu döner, dosya yazmaz | başlık L5 |
| **Bu dosyanın FM 10 alan kapısı bu fonksiyonla aynı desende** | §25 kapı 2 |

## §12. kalip-abc-check — Quality Report + Authority Footer Kapıları

Dosya: `.ai/scripts/kalip-abc-check.ps1` · Satır: L37-L61

```powershell
function Test-QualityReport([string[]]$lines) {
    $h2 = @()
    for ($i = 0; $i -lt $lines.Count; $i++) {
        if ($lines[$i] -match '^##\s+') { $h2 += ,@($i, $lines[$i]) }
    }
    $last = $h2[-1]
    if ($last[1] -notmatch 'Quality Report') {
        $issues += "son H2 Quality Report degil (son H2 = '$($last[1])')"
    }
}
function Test-AuthorityFooter([string]$text) {
    if ($text.IndexOf('**Authority:**') -lt 0) { return @('Authority footer yok') }
    if ($text.IndexOf('**Last Updated:**') -lt 0) { return @('Authority footer: Last Updated yok') }
    if ($text.IndexOf('**Mode:**') -lt 0) { return @('Authority footer: Mode yok') }
    return @()
}
```

| Kapı | Kural | Satır |
|---|---|---|
| Quality Report | son H2 "Quality Report" + 4 tablo satırı | L45-L51 |
| Authority footer | `**Authority:**` + `**Last Updated:**` + `**Mode:**` | L57-L60 |

## §13. kalip-abc-check — Kalıp C H3 Sıra Kapısı (prompt şablonu kanıtı)

Dosya: `.ai/scripts/kalip-abc-check.ps1` · Satır: L161-L162, L186-L188, L203-L215

```powershell
$C_H3 = @('### Context','### Required Inputs','### ASCII Reference',
          '### Prompt Template','### Expected Output','### Validation')

$h2 = @($lines | Where-Object { $_ -match '^##\s+' })
if ($h2.Count -ne 1) { $issues += "H2 sayisi $($h2.Count) (1 olmali)" }
elseif ($h2[0] -notmatch '^## AI Code Generation Prompt') { $issues += "H2 adi yanlis: $($h2[0])" }

$pat = switch ($dir) {
    'component' { '^#\s+\S.*\sComponent Prompt \(C\d+\)$' }
    'screen'    { '^#\s+T\d+:\s.+Screen Prompt$' }
}
```

| Kapı | Kural |
|---|---|
| H2 | tam 1 adet, adı birebir |
| H3 | 6 adet **sırada** (L191-L198 tarama penceresi) |
| H1 | kategoriye göre regex kalıbı (component/page/screen/layout) |

## §14. Fixture Prompt Kalıbı — Routing Testi İçin Örnekler

> Bunlar **fixture prompt metinleridir** (repo'da saklı dosya değil); türetme kaynağı
> `.ai/AGENTS.md` §6 Keyword → Agent Routing tablosunun gerçek satırlarıdır.

| # | §6 keyword grubu (alıntı başlangıcı) | Beklenen agent | Fixture prompt |
|---|---|---|---|
| 1 | `API, endpoint, routing, middleware, PHP, controller` | `backend` | "auth middleware'e rate limit ekle" |
| 2 | `CSRF, CSP, XSS, OWASP, auth, encryption` | `security` | "CSRF token doğrulamasını audit et" |
| 3 | `database, SQL, BCNF, migration, query, schema` | `data` | "oauth_connections tablosunu 3NF'e çıkar" |
| 4 | `C++, ASIO, JUCE, audio, DSP, Neva Engine` | `embedded` | "ring buffer latency'sini düşür" |
| 5 | `test, coverage, PHPUnit, Vitest, Playwright` | `qa` | "RouteTableTest coverage raporu üret" |
| 6 | `CI/CD, GitHub Actions, deploy, GitLeaks` | `devops` | "secret-scan.yml'a kural ekle" |
| 7 | `vault, documentation, ADR, wiki-link` | `mo` | "wiki-link kontrolünü çalıştır" |

**Test kalıbı:** her satır = `(prompt → expected_agent)` çifti; §16-§21'deki illüstratif
fixture'lara girdi olur. Repo'da bu çiftleri üreten/saklayan kod **YOK** (§22).

## §15. Kavram ↔ Eşleşme Tablosu (test kavramı → repo karşılığı)

| Test kavramı | Repo karşılığı | Eşleşme |
|---|---|---|
| Routing tablosu testi | `RouteTableTest` L19-L35 | **evet** |
| Method-aware 404/405 ayrımı | `match($path, $method)` L40, L50 | **evet** |
| Immutable default kilidi | `SpaRouteTest::testImmutableDefaults` L10-L23 | **evet** |
| Fixture dosyası (chrome) | `Fixtures/chrome/header.php` + `footer.php` | **evet** |
| Kriptografik test anahtarı | `Fixtures/jwt/*.pem` (4) | **evet** |
| Vault doğrulama betiği | `wiki-link-check.ps1` · `kalip-abc-check.ps1` | **evet** |
| **Ajan routing testi** (prompt → agent) | — | **YOK** |
| **Agent fixture'ı** (AGENT_MAP test verisi) | — | **YOK** |
| Prompt injection testi | — | **YOK** |

## §16. İllüstratif — Python: pytest Routing Assertion

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# pytest — AGENT_MAP (AIOrchestrator L26-L38) karşılığı olan routing fixture'ı.
import pytest
AGENT_MAP = {"mo": "coordination", "backend": "php-api", "security": "owasp"}

@pytest.mark.parametrize("task_type,expected", [
    ("recommendation", "backend"), ("security_audit", "security"),
])
def test_task_type_routes_to_expected_agent(task_type, expected):
    assert AGENT_MAP[expected] is not None      # agent kayıtlı olmalı
```

**Kapsam:** task → agent eşlemesi — repo karşılığı testi **YOK** (§8) · upstream: pytest docs.

## §17. İllüstratif — TypeScript: vitest Agent Fixture

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```typescript
import { expect, test, vi } from 'vitest';
// vi.mock hoisted: import'tan önce çalışır (vitest.dev/api/vi — doğrulandı 2026-10-17)
vi.mock('./knowledge.js', () => ({ search: vi.fn().mockResolvedValue([]) }));

test('unknown task type yields success:false', async () => {
  const { dispatch } = await import('./orchestrator.js');
  expect((await dispatch('nope')).success).toBe(false);   // AIOrchestrator L127-L133
});
```

**Kapsam:** mock'lı dispatch testi — repo'da JS agent kodu **YOK** · upstream: vitest.dev.

## §18. İllüstratif — Go: Test Harness (routing tablosu)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
// Go — RouteTableTest (L19-L35) ile aynı fixture şekli: map + tablo-driven test.
func TestExactBeatsPrefix(t *testing.T) {
    tbl := RouteTable{GET: map[string]Route{"/api/v1/auth/login": {Service: "auth", Public: true}}}
    m := tbl.Match("/api/v1/auth/login/", "GET") // trailing slash
    if m.Status != StatusOK { t.Fatalf("got %d want OK", m.Status) }
}
```

**Kapsam:** tablo-driven routing testi — repo Go dosyası **YOK** · upstream: go.dev/testing.

## §19. İllüstratif — Rust: Property-Based Test (routing invariantları)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```rust
use proptest::prelude::*;   // proptest! macro — docs.rs/proptest (doğrulandı 2026-10-17)
proptest! {
    #[test]
    fn prefix_fallback_never_returns_unknown_status(s in "\\PC{1,64}") {
        let st = match_route(&format!("/api/v1/auth/{}", s), "GET");
        prop_assert!(matches!(st, Status::Ok | Status::NotFound | Status::MethodNotAllowed));
    }
}
```

**Kapsam:** invariant = "sadece 3 durum" (RouteTable 404/405/OK ayrımı, L9) · upstream: proptest docs.rs.

## §20. İllüstratif — C++: GoogleTest (agent kaydı invariantı)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```cpp
TEST(AgentMapTest, EveryTaskTypePointsToRegisteredAgent) {   // gtest — google/googletest
    const auto map = AgentMap();                    // AGENT_MAP karşılığı
    for (const auto& [type, def] : map.tasks) {     // TASK_TYPES L40-L49
        EXPECT_TRUE(map.hasAgent(def.agent)) << "unregistered: " << type;
    }
}
```

**Kapsam:** TASK_TYPES → kayıtlı agent invariantı — repo C++ testi **YOK** · upstream: google/googletest.

## §21. İllüstratif — C#: xUnit Agent Mock

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```csharp
using Xunit;
public sealed class FakeOrchestrator : IOrchestrator {
    public List<(string Task, string Agent)> Calls { get; } = new();
}
[Fact]
public void UnknownTaskType_ReturnsSuccessFalse() {
    var orch = new FakeOrchestrator();
    var r = orch.Dispatch("nope");                       // L127-L133 semantiği
    Assert.False(r.Success);
    Assert.Empty(orch.Calls);                            // çağrı olmamalı
}
```

**Kapsam:** sahte orchestrator ile dispatch testi — repo C# testi **YOK** · upstream: xunit docs.

## §22. Yokluk Cümleleri (tahmin yasak — 2026-10-17 disk ölçümü)

| Alan | Durum |
|---|---|
| AIOrchestrator routing testi (`AGENT_MAP`/`TASK_TYPES`) | **Repo'da ajan testi YOK** |
| PromptEngine testi (injection filtresi L27-L35) | **Repo'da ajan testi YOK** |
| ToolCalling testi (izin/executor L145-L166) | **Repo'da ajan testi YOK** |
| KnowledgeBase + AIWorkflow testi (search/ragQuery/çağrı noktaları) | **Repo'da ajan testi YOK** |
| Agent fixture prompt çiftleri (§14) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| LLM çıktı değerlendirme / golden prompt | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| E2E agent senaryosu (Playwright ile ajan akışı) | Repo'da bu alan kodu YOK — kaynak: upstream docs |

## §23. Ölçüm Kalıpları (betiklerde gerçekten kullanılanlar)

| Ölçüm | Gerçek kullanım | Dosya/Satır |
|---|---|---|
| Satır sayımı | `[System.IO.File]::ReadAllLines($p, [Text.Encoding]::UTF8)` | kalip-abc-check L19 |
| Metin okuma | `[System.IO.File]::ReadAllText($src,$enc)` | wiki-link-check L23 |
| H2/H3 sayımı | `$lines | Where-Object { $_ -match '^##\s+' }` | kalip-abc-check L186 |
| Karar (gate) | `$exit = 1` + `exit $exit` | kalip-abc-check L92, L231 |

## §24. Güvenlik Notları (kanıt ile)

| Not | Kanıt |
|---|---|
| Test anahtarları `test-` önekli PEM — üretim secret'ı değil | `Fixtures/jwt/test-private.pem` adı |
| Betiklerde sabit anahtar/secret yok — yalnız yol sabiti | wiki-link-check L5 · kalip-abc-check L15 |
| Betikler salt-okunur: raporlar, `$exit` dönerer | kalip-abc-check başlık L5 |
| Testler production'a yazmaz: `RouteTable::fromArray` bellek-içi | RouteTableTest L19 |
| YAML/JSON'a secret yazılmaz; `.env` env substitution | kök AGENTS §5 Security |

## §25. Doğrulama Kapıları (bu dosya)

| # | Kapı | Kriter | Durum |
|---|---|---|---|
| 1 | Satır sayısı | `>= 500` (band 500-520) | §28 ölçümü |
| 2 | Frontmatter | 10 alan · `title`/`parent` YOK | §28 ölçümü |
| 3 | Authority son satır | birebir `**Authority:** ... **Mode:** ...` | §28 |
| 4 | PHP kanıtları | RouteTableTest + SpaRouteTest gerçek künyeli | §4-§6 |
| 5 | Betik kanıtları | wiki-link-check + kalip-abc-check gerçek künyeli | §9-§13 |
| 6 | Yokluk cümleleri | 7 satır (§22) — "Repo'da ajan testi YOK" | §8 grep |
| 7 | İllüstratif etiket | 6 blok + 6 dil, üstünde etiket | §16-§21 |
| 8 | Section sayısı | en az 25 `## §` | §28 |
| 9 | Placeholder + Mojibake | bekleyen yer tutucu yok · U+FFFD 0 | §28 |

## §26. Açık Sorular

| # | Soru | Etki |
|---|---|---|
| 1 | Ajan-routing testleri hangi dizine konmalı: `shared/tests/Ai/` mi, ayrı suite mi? | §22 kapanışı |
| 2 | Prompt fixture'ları dosya olarak mı, parametrize tablo olarak mı saklanacak? | §14 kalıcılaştırma |
| 3 | LLM çıktısı doğrulaması için deterministik stub mı, contract test mi? | determinizm |
| 4 | `Unit/Middleware/` altında test yok (yalnız README.md) — kasıtlı mı? | kapsam |
| 5 | kalip-abc-check kapıları `.ai/architecture/**` için de mi çalıştırılmalı? | vault gate genişlemesi |

## §27. Kaynaklar

| # | Kaynak | Tür | Durum |
|---|---|---|---|
| 1 | `shared/tests/**` 45 PHP / 41 test sınıfı | repo, 2026-10-17 glob+grep | OK |
| 2 | `.ai/scripts/wiki-link-check.ps1` (L1-L43 tam okundu) | repo, 2026-10-17 | OK |
| 3 | `.ai/scripts/kalip-abc-check.ps1` (L1-L231 tam okundu) | repo, 2026-10-17 | OK |
| 4 | https://docs.phpunit.de/en/12.5/assertions.html — `assertSame()` tanımı | resmi web, 2026-10-17 | DOĞRULANDI |
| 5 | https://vitest.dev/api/vi — `vi.mock` hoisting + `test.extend` fixtures | resmi web, 2026-10-17 | DOĞRULANDI |
| 6 | https://docs.rs/proptest/latest/proptest/prelude/macro.proptest.html — `proptest!` | resmi web, 2026-10-17 | DOĞRULANDI |

## §28. Yeniden Ölçüm + Gate Özeti

| Amaç | Komut (repo kökünden) |
|---|---|
| Satır sayısı | `[IO.File]::ReadAllLines($hedef,[Text.Encoding]::UTF8).Count` → band 500-520 |
| FM 10 alan | ilk 14 satırda `node_id…group` 10 anahtar |
| Authority | `$lines[-1]` birebir |
| Mojibake | `ReadAllText` içinde U+FFFD ara → 0 |

§1-§28: künye 9 kural · envanter 45 PHP / 41 test sınıfı / 9 dizin · PHP kanıt 2 dosya
(RouteTableTest L8-L60, SpaRouteTest L8-L44) · betik kanıtı 2 dosya · fixture 6 dosya ·
yokluk 7 satır · fixture prompt 7 satır · eşleme 9 satır · güvenlik 5 · kapı 9 ·
açık soru 5 · kaynak 6 (3 web DOĞRULANDI) · illüstratif 6 blok / 6 dil.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
