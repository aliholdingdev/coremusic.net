---
node_id: context-ornekler
node_type: module
status: active
parent_id: context
children: []
depends_on: [context-anayasa, context-surucu]
evidence:
  - ".ai/scripts/*.ps1 (gerçek betik kanıtı, 2026-10-17 okuma)"
  - ".ai/AGENTS.md §24.2 okuma tablosu alıntıları"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı, kod kanıtı zorunlu"
open_questions: []
group: kod
---

# context-ornekler - Bağlam Kod Örnekleri (kanıt odaklı)

> **Kapsam:** Her PowerShell/PHP alıntısı 2026-10-17 okuma turundan gelir ve `Dosya:` / `Satır:`
> künyesi taşır. İllüstratif çok-dilli bloklar repo kanıtı DEĞİLDİR; her birinin üstünde zorunlu
> etiket vardır. `.ai/scripts/**` ve `shared/src/**` salt-okunurdur — bu dosya dışına yazılmaz.

## §1. Kanıt Künye Formatı

| Alan | Kural |
|---|---|
| Dosya | göreli yol, diskte var (glob ile doğrulanmış, 2026-10-17) |
| Satır | okunan aralık (L18-L25 gibi) — okunmamış satır künye olamaz |
| Alıntı | betik/kod satırının kendisi (kısaltma serbest, uydurma yasak) |
| Yokluk | tam cümle: "Repo'da bu alan kodu YOK — kaynak: upstream docs" |
| Ölçüm | grep/glob/ReadAllLines sayaçları — araç çıktısı, iddia değil |
| İllüstratif | `⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı` |
| Kalıp | sentez bloklar üstünde "sentez — tek dosyalık repo kodu DEĞİL" etiketi taşır |
| Tarih | her kanıt 2026-10-17 okuma turuna ait |
| Eşleşme | `evet` / `kısmi` / `YOK` — başka değer yok |
| Yasak | uydurma künye → Zero-Hallucination ihlali (kök AGENTS §3) · çelişkide disk kazanır |

## §2. Okunan Betik Envanteri — `.ai/scripts/` (6 betik)

Dosya: `.ai/scripts/` · glob → **6 `.ps1` isabeti** (2026-10-17)

| # | Betik | Satır | Amaç (başlık satırı) | Bağlam ilgisi |
|---|---|---|---|---|
| 1 | wiki-link-check.ps1 | 43 | "wiki-link denetimi: .ai/ui-design/**/*.md icindeki [[hedef]] -> gercek dosya var mi?" (L2) | doğrudan — indeks kurulumu + bağlantı çözümü |
| 2 | screens-frontmatter-check.ps1 | 73 | "Faz 5 kapisı … Degistirmez, raporlar" (L2-L3) | doğrudan — bağlam metadata kapısı |
| 3 | kalip-abc-check.ps1 | 231 | "Kalip A (reference), B (flow), C (prompt) zorunluluklarini dogrular" (L3-L4) | doğrudan — ReadAllLines UTF8 okuma kalıbı |
| 4 | figma-tokens.ps1 | 313 | "Figma ham JSON -> tokens-*.json … dogrulama kapasi" (L3, L25) | kısmi — yaz-geri-oku-doğrula döngüsü |
| 5 | figma-extract.ps1 | 181 | "CoreMusic Figma tam çekim betigi … Yeniden calistirilabilir" (L3-L4) | kısmi — UTF8 no-BOM yazım + idempotentlik |
| 6 | device-matrix-catid.ps1 | 82 | "00-device-matrix.md §3 tier tablolara CatID sutunu ekler" (L3) | kısmi — idempotency guard |

## §3. wiki-link-check.ps1 — İndeks Kurulumu + Üç Kademeli Çözüm

Dosya: `.ai/scripts/wiki-link-check.ps1` · Satır: L5-L6, L10-L16, L23-L24, L32-L34, L41-L43

```powershell
$ROOT='C:\www\coremusic.net\.ai\ui-design'                 # L5
$enc=[Text.Encoding]::UTF8                                # L6
Get-ChildItem -Path 'C:\www\coremusic.net\.ai' -Recurse -Filter *.md -File | ForEach-Object {   # L10
  $index[$_.FullName.ToLowerInvariant()]=$_.FullName      # L14 — üç anahtar biçimi (L13-L15)
}
$text=[System.IO.File]::ReadAllText($src,$enc)            # L23 — açık UTF8
foreach($m in [regex]::Matches($text,'\[\[([^\]|#]+)(?:[#|][^\]]*)?\]\]')){   # L24
  if($index.ContainsKey($k)){ continue }                  # L30 — 1) dosya anahtarı
  $cand=[System.IO.Path]::GetFullPath((Join-Path $dir ($t -replace '/','\')))  # L32 — 2) göreli yol
  if(Test-Path ($cand+'.md')){ continue }                 # L34 — 3) uzantı ekle
}
"kontrol edilen link: $checked"                           # L41
"kirik link: "+$broken.Count                              # L42
```

| Gözlem | Kanıt |
|---|---|
| Boot bağımsız indeks: `.ai/**/*.md` tek turda belleğe alınır (bağlam paketleme analogu) | L10-L16 |
| Üç kademeli çözüm: tam yol anahtarı → göreli yol → leaf ad (L36-L37) | L29-L37 |
| `[[...]]` regex `#`/`|` parantezlerini keser — künye çakışması önlenir | L24 |
| Çıktı sayaçla: kontrol edilen + kırık (rapor, iddia değil) | L41-L43 |

## §4. screens-frontmatter-check.ps1 — 13 Alanlı Metadata Kapısı

Dosya: `.ai/scripts/screens-frontmatter-check.ps1` · Satır: L10-L14, L21, L27, L37, L41, L65-L67, L73

```powershell
$REQUIRED_FM = @('reference_doc','title','type','category','date','status','version',
                 'tier','viewport','device','authority','governance','reference')   # L10-L11
$SECTIONS = @('## 1. ASCII Layout', … '## 9. State')                              # L12-L14
$text = [System.IO.File]::ReadAllText($f.FullName, [Text.Encoding]::UTF8)          # L21
if ($lines[$i] -eq '---') { $fmEnd = $i; break }                                  # L27 — FM kapanışı
if (-not ($fm | Where-Object { $_ -match "^\s*$([regex]::Escape($k))\s*:" })) { $issues += "FM eksik: $k" }  # L37
if ($text.IndexOf($s) -lt 0) { $issues += "bolum eksik: $s" }                     # L41
Write-Host "dosya: $($files.Count) | sorunlu: $fail"                              # L73
```

| Gözlem | Kanıt |
|---|---|
| Bağlam paketi = FM (13 alan) + gövde (9 bölüm) — ikisi de kapı, ihlal = sayımda | L10-L14, L36-L42 |
| Anahtar `[regex]::Escape` ile kaçırılır — regex enjeksiyonuna kapalı | L37 |
| "bayat sinyal" listesi (`'23 md','175 prompt',…`) — bağlam bayatlığı yakalanır | L65-L67 |
| Çıktı tek özet satırı; `$fail` sayacı gate değeri | L73 |

## §5. kalip-abc-check.ps1 — ReadAllLines UTF8 + Frontmatter Ayrıştırıcı

Dosya: `.ai/scripts/kalip-abc-check.ps1` · Satır: L18-L25, L27-L34, L37-L54, L56-L61, L85-L89, L230-L231

```powershell
function Get-Frontmatter([string]$p) {
    $lines = [System.IO.File]::ReadAllLines($p, [Text.Encoding]::UTF8)   # L19 — ReadAllLines UTF8
    if ($lines.Count -lt 3 -or $lines[0] -ne '---') { return $null }    # L20 — açılış kapısı
    for ($i = 1; $i -lt $lines.Count; $i++) {
        if ($lines[$i] -eq '---') { return ,@($lines[1..($i-1)]) }      # L22 — FM gövdesi
    }
    return $null                                                        # L24 — kapanmayan FM = $null
}
function Test-FmKeys($fm, [string[]]$keys) {                            # L27
    $re = "^\s*$([regex]::Escape($k))\s*:"                              # L30
    if (-not ($fm | Where-Object { $_ -match $re })) { $miss += $k }    # L31
}
Write-Host "SONUC -> A:$aBad B:$bBad C:$cBad  (0/0/0 = GECTI)"         # L230
exit $exit                                                              # L231
```

| Gözlem | Kanıt |
|---|---|
| **ReadAllLines UTF8 Count** bu dosyanın da ölçüm kalıbıdır (bu dosya §33) | L19 + §33 |
| FM sınırı `-ne '---'` birebir karşılaştırma (regex değil — mojibake/regex tuzağı yok) | L20, L22 |
| Son H2 "Quality Report" değilse ihlal — bağlam bütünlüğü kapısı | L37-L54 |
| Footer (`**Authority:**` / `**Last Updated:**` / `**Mode:**`) zorunlu | L56-L61 |
| Gate: `exit $exit` — CI-okur davranış | L231 |

## §6. figma-tokens.ps1 — SSOT Env Okuma + Yaz-Geri-Oku-Doğrula

Dosya: `.ai/scripts/figma-tokens.ps1` · Satır: L16-L21, L54-L70, L177-L183, L249-L270, L296, L310-L313

```powershell
$ENVRAW = Get-Content -Raw $ENVF -Encoding UTF8                       # L19 — env dosyası UTF8
$KEY = ([regex]::Match($ENVRAW, 'FIGMA_FILE_KEY=(\S+)')).Groups[1].Value  # L20 — secret betikte YOK
$lk = $key.ToLowerInvariant()                                         # L59 — seen map lowercase
$raw = [System.IO.File]::ReadAllText($path, [Text.Encoding]::UTF8)    # L178
[System.IO.File]::WriteAllText($path, ($obj | ConvertTo-Json -Depth 8), $enc)  # L183 — UTF8 no BOM
if ([int]$decl -ne [int]$disk) { $fails += "R3 $sec sayim uyusmazligi: meta=$decl disk=$disk" }  # L269
Write-Host "`nBITTI" -ForegroundColor Green                           # L312
if ($failCount -gt 0) { exit 1 } else { exit 0 }                      # L313
```

| Gözlem | Kanıt |
|---|---|
| **Yaz-geri-oku-doğrula (R2/R3):** token yazılır, diske geri okunur, `_meta.sections` <> disk sayımı karşılaştırılır — bağlam paketleme kalıbının doğrulanmış hâli | L249-L270 |
| Kural kapıları R1-R6; her kural tetiklenmezse PASS, ihlal yok + boş tasarım = BOS | L285-L294 |
| Secret yalnız `.env.figma`'dan regex ile çekilir; betik metninde sabit anahtar yok | L16-L21 |
| Özet satırı + exit kodu — gate betiği davranışı | L310-L313 |

## §7. figma-extract.ps1 — UTF8 no-BOM Yazım + Idempotent Çekim

Dosya: `.ai/scripts/figma-extract.ps1` · Satır: L14, L25-L31, L66-L69, L147-L148, L159, L175-L181

```powershell
[Console]::OutputEncoding = [Text.Encoding]::UTF8                   # L14 — konsol UTF8
function Save-Utf8($path, $text) {
    $enc = New-Object System.Text.UTF8Encoding($false)              # L67 — UTF8Encoding($false) = BOM YOK
    [System.IO.File]::WriteAllText($path, $text, $enc)              # L68
}
for ($i = 0; $i -lt $targets.Count; $i += 20) {                     # L147 — 20'lik parti
    if (Test-Path $out) { continue }                                # L159 — idempotent: varsa atla
}
[System.IO.File]::AppendAllText($LOGF, $block, $enc)                # L178 — log append-only
Write-Host "BITTI" -ForegroundColor Green                          # L181
```

| Gözlem | Kanıt |
|---|---|
| `UTF8Encoding($false)` = BOM'suz UTF-8 yazım; okuma tarafı her yerde açık UTF8 (§10) | L67, L14 |
| Idempotentlik: `Test-Path $out → continue` — tekrar çalıştırma veri kaybı üretmez | L159 |
| `AppendAllText` ile not defteri — audit zinciri korunur | L175-L178 |
| Env SSOT tekrarı: `throw "FIGMA_TOKEN bos"` — secret betikte yok | L26-L31 |

## §8. device-matrix-catid.ps1 — Idempotency Guard + Kapalı Kodlama

Dosya: `.ai/scripts/device-matrix-catid.ps1` · Satır: L8-L9, L34-L35, L59, L81-L82

```powershell
[Console]::OutputEncoding = [Text.Encoding]::UTF8                          # L9
$enc  = New-Object System.Text.UTF8Encoding($false)                        # L12 — BOM yok
$lines = [System.IO.File]::ReadAllLines($FILE, [Text.Encoding]::UTF8)      # L34
if ($lines[0] -match '^\| CatID \|') { Write-Host 'CatID sütunu zaten var — atlandı.'; exit 0 }  # L35
$out.Add('| CatID | ' + $line.Substring(1))                                # L59 — başlık enjeksiyonu
[System.IO.File]::WriteAllLines($FILE, $out, $enc)                         # L81
Write-Host "Tablo: $tables | CatID eklenen satir: $added"                  # L82
```

| Gözlem | Kanıt |
|---|---|
| Tekrar çalıştırılabilirlik başlık imzasıyla: `^\| CatID \|` — bağlam dosyası iki kez işlenmez | L35 |
| Satır-bazlı yeniden yazım (ReadAllLines → liste → WriteAllLines) — boot zinciri ölçüm kalıbının yazım ikizi | L34, L81 |
| Çıktı iki sayaç: tablo + eklenen satır | L82 |

## §9. Boot Zinciri PowerShell Kalıbı (sentez — §3-§8 desenlerinin birleştirmesi)

> Kalıp: §3-§8'deki gerçek betik desenlerinin birleştirmesi — tek dosyalık repo kodu DEĞİL.

```powershell
#Requires -Version 5.1
$ErrorActionPreference = 'Stop'
[Console]::OutputEncoding = [Text.Encoding]::UTF8          # kanonik: 6/6 betikte aynı satır (§10)
$enc = [Text.Encoding]::UTF8                               # okuma tarafı açık UTF8 (§5 L19)
$boot = @('CLAUDE.md','AGENTS.md','WORKFLOW.md','brain.md','ROLE.md',
          'index.md','keys.md','MEMORY.md','log.md')       # §24.2 sırasından türetilmiş alt küme
foreach ($f in $boot) {
  $p = Join-Path 'C:\www\coremusic.net\.ai' $f
  if (-not (Test-Path $p)) { Write-Host "EKSIK: $f"; continue }   # hayalet satır yok (Test-Path)
  $lines = [System.IO.File]::ReadAllLines($p, $enc)
  "{0}: {1} satır" -f $f, $lines.Count                    # ölçüm = araç çıktısı
}
```

| Kalıp kuralı | Kaynak desen |
|---|---|
| Açık UTF8 (`ReadAllLines($p, [Text.Encoding]::UTF8)`) | kalip-abc-check L19 · device-matrix L34 |
| Yokluğu kabul et, atla, say (`EKSIK` + `continue`) | wiki-link-check L13-L15 (indeks) · figma-extract L159 |
| Sayılar ölçümle yazılır | screens L73 · kalip L230 · device L82 |
| Sıra sabittir, dosya yoksa sırayı bozma | `.ai/AGENTS.md` §24.2 (L503-L518) |

## §10. Mojibake Tuzağı — BOM, Konsol Kodlaması ve `-cmatch`

| Kalıp | Kanıt (6/6 betik) |
|---|---|
| `[Console]::OutputEncoding=[Text.Encoding]::UTF8` | wiki-link L4 · screens L5 · kalip L13 · device L9 · figma-tokens L8 · figma-extract L14 |
| Okuma açık UTF8: `ReadAllLines/ReadAllText(..., [Text.Encoding]::UTF8)` | kalip L19/L85 · device L34 · wiki-link L23 · figma-tokens L178 |
| Yazım `UTF8Encoding($false)` (BOM'suz) | figma-extract L67 · device L12 · figma-tokens L182 |
| PS 5.1 case-folding notu (gerçek betik yorumu) | figma-tokens L55-L56: `# seen map'i lowercase normalize: PS 5.1 ConvertFrom-Json case-folding ile okur,` |
| `-cmatch` / `-imatch` kullanımı | **grep 0 isabet** (`.ai/scripts/*.ps1`, 2026-10-17) — repo betikleri `-match` (varsayılan, case-insensitive) kullanır; case-sensitivity bilinçli istisna repo'da YOK |

**Tuzak özeti:** (1) PowerShell dosyayı kodlama vermeden okursa BOM yoksa varsayılan uygulama kodlaması
devreye girer — Türkçe karakterler mojibake olur; çözüm betiklerde ısrarla açık `[Text.Encoding]::UTF8`.
(2) `-cmatch` (case-sensitive regex) ASCII-olmayan eşleşmelerde ve `İ/I` gibi harflerde sessiz
false üretir; repo betikleri bunu bilinçli olarak hiç kullanmaz (grep 0). (3) BOM kendisi
UTF-8'de isteğe bağlıdır (upstream §36-1): BOM'suz yazım + BOM-bilinçli okuma eşleşmesi korunur.

## §11. PHP Envanteri — `shared/src/AI/` (7 dosya) Bağlam İlgisi

Dosya: `shared/src/AI/` · glob → **7 isabet** (2026-10-17, aynı tur rag-ornekler ile)

| # | Dosya | Sınıf amacı (gerçek satır) | Bağlam ilgisi |
|---|---|---|---|
| 1 | PromptEngine.php | "Prompt üretimi, token yönetimi, validasyon" (L16) + "TokenCounter" (L20) | doğrudan — token ölçümü + context enjeksiyonu (§12-§16) |
| 2 | AIOrchestrator.php | "ContextManager (bağlam yönetimi)" (L22) | doğrudan — session context deposu (§17) |
| 3 | MemorySystem.php | "Session state, cache, memory hierarchy" (L16) | doğrudan — bağlam TTL + hiyerarşi (§18) |
| 4 | AIWorkflow.php | "trigger → process → result → feedback" (L16) | doğrudan — bağlam toplama zinciri (§19) |
| 5 | AIEngine.php | "5 modül: RecommendationEngine …" (L21-L26) | kısmi — context parametreleri (§20) |
| 6 | KnowledgeBase.php | ragQuery(array $context) imzası (L72) | kısmi — RAG bağlamı (rag-ornekler §3) |
| 7 | ToolCalling.php | "Tool registry, executor, permission check" (L16) | ilgisiz — bağlam kodu YOK (§30) |

## §12. PromptEngine — Context Enjeksiyonu ("## Context" bloğu)

Dosya: `shared/src/AI/PromptEngine.php` · Satır: L48, L57-L72, L80-L84

```php
public function generatePrompt(string $templateName, array $variables, array $context = []): array  // L48
    $prompt = $template['template'];                                  // L57
    $placeholder = '{{' . $key . '}}';                                // L59 — değişken yerleşimi
    $prompt = str_replace($placeholder, (string) $value, $prompt);    // L60
    if (!empty($context)) {                                           // L64 — bağlam kapısı
        $prompt .= "\n\n## Context\n";                                // L65 — bağlam başlığı
        foreach ($context as $key => $value) {                        // L66
            if (is_array($value)) {
                $value = json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);  // L68
            }
            $prompt .= "- {$key}: {$value}\n";                        // L70 — madde madde bağlam
        }
    }
    $tokenCount = $this->countTokens($prompt);                        // L75 — bağlam sonrası ölçüm
```

| Gözlem | Kanıt |
|---|---|
| Bağlam paketleme = `## Context` başlığı + `- key: value` maddeleri (tek biçim) | L65-L70 |
| Diziler JSON'a çevrilir; Unicode kaçışı kapalı (Türkçe korunur) | L68 |
| Bağlam eklenince token yeniden sayılır — ölçüm bağlamdan sonra | L75 |
| Çıktı: `prompt` / `tokenCount` / `estimatedCost` | L80-L84 |

## §13. PromptEngine::countTokens — Token Ölçüm Kalıbı (repo) + upstream karşılaştırma

Dosya: `shared/src/AI/PromptEngine.php` · Satır: L90-L96

```php
public function countTokens(string $text): int
{
    // Basit token sayımı — gerçek implementasyonda tokenizer kullanılacak
    // Ortalama 1 token ≈ 4 karakter (İngilizce) veya 2-3 karakter (Türkçe)
    $charCount = mb_strlen($text, 'UTF-8');
    return (int) ceil($charCount / 3.5);
}
```

| Ölçüm | Değer | Kanıt |
|---|---|---|
| Repo kuralı | `ceil(mb_strlen/3.5)` — bölen 3.5 | L94-L95 |
| Repo yorumu | "gerçek implementasyonda tokenizer kullanılacak" — gerçek tokenizer YOK | L92 |
| Upstream | OpenAI: "one token generally corresponds to ~4 characters of text for common English text" | §36-2 |
| Uyum | **kısmi** — upstream ~4 karakter (İngilizce), repo 3.5 (daha muhafazakâr); Türkçe için repo yorumu "2-3 karakter" der ama kod 3.5 kullanır → Türkçe bağlamda tahmin şişebilir | L93 vs L95 |

## §14. PromptEngine::optimizePrompt — Kademeli Bağlam Kırpma

Dosya: `shared/src/AI/PromptEngine.php` · Satır: L101-L135

```php
public function optimizePrompt(string $prompt, int $maxTokens): array
    $tokenCount = $this->countTokens($prompt);                          // L103
    if ($tokenCount <= $maxTokens) {                                    // L105 — bütçe yeterli: dokunma
        return ['optimized' => $prompt, 'tokenCount' => $tokenCount, 'wasCompressed' => false];  // L106-L110
    }
    $optimized = preg_replace('/\s+/', ' ', $optimized);                // L117 — kademe 1: boşluk
    $optimized = $this->truncateLongLists($optimized, $maxTokens);      // L121 — kademe 2: listeler
    if ($this->countTokens($optimized) > $maxTokens) {                  // L124 — hâlâ fazla mı?
        $optimized = $this->truncateContext($optimized, $maxTokens);    // L125 — kademe 3: bağlam
    }
    return ['optimized' => $optimized, 'tokenCount' => $newTokenCount, 'wasCompressed' => true]; // L130-L134
```

| Kademe | Eylem | Satır |
|---|---|---|
| 0 | Bütçe yeterli → `wasCompressed: false`, kırpma yok | L105-L110 |
| 1 | Beyaz uzunluğunu tek boşluğa indir | L117-L118 |
| 2 | Madde listeleri kırpma (§15) | L121 |
| 3 | `## Context` bölümü kırpma (§15) | L123-L126 |

## §15. truncateLongLists + truncateContext — Karakter Bütçesi Hesabı

Dosya: `shared/src/AI/PromptEngine.php` · Satır: L278-L302, L307-L324

```php
if (preg_match('/^[\s]*[-*•]/', $line)) {                 // L287 — madde tespiti
    $listCount++;
    if ($listCount > 10) {                                 // L290 — 10 madde üst sınırı
        $result[] = '... (more items truncated)';          // L291 — izlenebilir kırpma işareti
    }
}
$contextStart = strpos($text, '## Context');               // L309 — bağlam bölümü bulma
$chars = (int) ($maxTokens * 3.5 * self::COMPRESSION_THRESHOLD);   // L312 — 0.8 eşiği (L26)
return mb_substr($text, 0, $chars, 'UTF-8') . "\n... (truncated)"; // L313
$maxContextChars = (int) (($maxTokens * 3.5 * 0.3));       // L320 — bağlam bölümü %30 bütçe
$truncatedContext = mb_substr($contextSection, 0, $maxContextChars, 'UTF-8') . "\n... (context truncated)";  // L321
```

| Formül | Anlam | Satır |
|---|---|---|
| `maxTokens * 3.5 * 0.8` | token→karakter çevirimi (§13 böleni 3.5) × %80 güvenlik eşiği | L312, L26 |
| `maxTokens * 3.5 * 0.3` | bağlam bölümüne ayrılan karakter payı (%30) | L320 |
| Kırpma her zaman iz bırakır (`... (truncated)`) — sessiz veri kaybı yok | denetlenebilirlik | L291, L313, L321 |

## §16. validatePrompt — Token Bütçesi + Bağlam Girdisi Kapısı

Dosya: `shared/src/AI/PromptEngine.php` · Satır: L25, L156-L186, L169-L179

```php
private const MAX_TOKENS_DEFAULT = 4096;                                   // L25
$tokenCount = $this->countTokens($prompt);                                 // L170
$maxTokens = self::MAX_TOKENS_DEFAULT;                                     // L171
if ($tokenCount > $maxTokens) {                                            // L172
    $risks[] = "Token count ({$tokenCount}) exceeds max ({$maxTokens})";   // L173
}
if (preg_match('/password|secret|api[_-]?key|token/i', $prompt)) {         // L177 — hassas veri
    $risks[] = "Potential sensitive data in prompt";                       // L178
}
return ['valid' => empty($risks), 'risks' => $risks, 'sanitized' => $sanitized];  // L181-L185
```

| Kapı | Eşik | Sonuç |
|---|---|---|
| Injection deseni (7 adet) | `INJECTION_PATTERNS` (L27-L35) | risk + `[FILTERED]` (L162-L167) |
| Token bütçesi | 4096 (L25) | risk kaydı — bloklamaz, raporlar (L172-L174) |
| Hassas veri | `password\|secret\|api[_-]?key\|token` | risk kaydı (L177-L179) |

## §17. AIOrchestrator — Session Context Deposu (bellek-içi)

Dosya: `shared/src/AI/AIOrchestrator.php` · Satır: L22, L53-L54, L154-L170

```php
 * - ContextManager (bağlam yönetimi)                              // L22 — bileşen sözü (yorum)
/** @var array<string, array<string, mixed>> */
private array $contexts = [];                                       // L54 — bellek-içi depo
public function getContext(string $sessionId): array
{
    return $this->contexts[$sessionId] ?? [];                        // L156 — yoksa boş dizi
}
public function updateContext(string $sessionId, array $data): bool
{
    $this->contexts[$sessionId] = array_merge(                       // L164 — birleştirme (üstün gelir)
        $this->contexts[$sessionId] ?? [],
        $data
    );
    return true;                                                     // L169
}
```

| Gözlem | Kanıt |
|---|---|
| Bağlam oturum anahtarlı, bellek-içi — kalıcı yazım yok (ömrü süreç) | L54, L154-L170 |
| `array_merge` = son yazılan kazanır; kısmi güncelleme destekli | L164-L167 |
| "ContextManager" yalnız yorum satırında anılır — ayrı sınıf **YOK** | L22 vs sınıflar |

## §18. MemorySystem — Bağlam Hiyerarşisi + TTL

Dosya: `shared/src/AI/MemorySystem.php` · Satır: L18-L21, L25-L31, L45-L55, L60-L75, L123-L147

```php
 * - L1 (Hot): SSOT dosyaları (CLAUDE, AGENTS, WORKFLOW) — Oturum sonu   // L19
 * - L2 (Warm): Görev dosyaları (ADR, architecture) — Görev sonu         // L20
 * - L3 (Cool): Referans dosyaları (testing, ui-design) — İsteğe bağlı   // L21
private const HIERARCHY = [ 'brain' => 5, 'index' => 4, 'agents' => 4, 'memory' => 3, 'log' => 1 ];  // L25-L31
$expiresAt = $ttl > 0 ? time() + $ttl : null;                           // L47 — TTL hesabı
if ($entry['expires_at'] !== null && $entry['expires_at'] < time()) {   // L69 — bayatlık kapısı
    unset($this->sessions[$sessionId][$key]); return null;              // L70-L71
}
public function remember(string $key, callable $loader, string $level = 'l2', int $ttl = 3600): mixed  // L123
```

| Kavram | Karşılığı | Eşleşme |
|---|---|---|
| Boot okuma öncelik sırası (SSOT → görev → referans) | L1/L2/L3 yorumları + `HIERARCHY` 5→1 | **kısmi** — sabit tablo, dinamik okuma yok |
| Cache-aside bağlam yükleme | `remember()` L123-L147 | **evet** |
| TTL ile bağlam bayatlığını önleme | `getSession` L69-L72 · `remember` L129 | **evet** |

## §19. AIWorkflow — Bağlam Toplama Zinciri (session → request → prompt)

Dosya: `shared/src/AI/AIWorkflow.php` · Satır: L52-L56, L59, L62-L72, L78, L86

```php
$sessionId = $request['session_id'] ?? 'anonymous';                          // L52
$context = $this->memory->getSession($sessionId, 'preferences') ?? [];       // L55 — 1) oturum bağlamı
$context = array_merge($context, $request);                                  // L56 — 2) istek üstüne biner
$taskId = $this->orchestrator->dispatchTask('recommendation', $context, 'medium');  // L59 — 3) bağlam dağıtılır
$query = ($context['mood'] ?? '') . ' ' . ($context['genre'] ?? '');        // L62 — 4) bağlamdan sorgu
], ['knowledge' => $knowledgeResults]);                                      // L72 — 5) bağlam prompt'a
$this->memory->setSession($sessionId, 'last_recommendations', $recommendations, 3600);  // L78 — TTL 3600
'prompt_tokens'  => $promptResult['tokenCount'],                            // L86 — ölçüm metadata'ya
```

| Adım | Bağlam hareketi | Satır |
|---|---|---|
| 1-2 | oturum `preferences` + istek → tek dizi (`array_merge`) | L55-L56 |
| 3-5 | dispatch → sorgu kurulumu → `['knowledge' => ...]` prompt bağlamı | L59, L62, L72 |
| 6 | geri besleme: `last_recommendations` TTL 3600 | L78 |
| metadata | `prompt_tokens` + `knowledge_hits` sayımla raporlanır | L86-L87 |

## §20. AIEngine — Bağlam Parametrelerinin Ayrıştırılması

Dosya: `shared/src/AI/AIEngine.php` · Satır: L49-L62, L223-L229

```php
public function getRecommendations(array $context, int $limit = 20): array   // L49
    $userId = $context['user_id'] ?? null;                                   // L52
    $sessionId = $context['session_id'] ?? null;                             // L53
    $mood = $context['mood'] ?? null;                                        // L54
    $genre = $context['genre'] ?? null;                                      // L55
    $history = $context['history'] ?? [];                                    // L56
    $contentScores = $this->getContentBasedScores($context);                 // L62
private function getContentBasedScores(array $context): array                // L223
    'bpm' => $context['bpm'] ?? null, 'energy' => $context['energy'] ?? null,  // L226-L227
```

| Gözlem | Kanıt |
|---|---|
| Bağlam tek `array` — anahtarlar tek tek `?? null` ile ayrıştırılır (şema yok) | L52-L56 |
| Bağlam doğrudan alt hesaba aktarılır (`$context` referansı) | L62, L223 |
| Eksik anahtarlar varsayılana düşer: `user_id`, `session_id`, `mood`, `genre` | L52-L56 |

## §21. `.ai/AGENTS.md` §12 — Context Lock (vault kural alıntısı)

Dosya: `.ai/AGENTS.md` · Satır: L439-L469

```text
### §12 Context Lock                                        # L439
Eşzamanlı erişimi önlemek için dosya kilitleme mekanizması. # L441
| Kilitleme süresi | Max 30 saniye |                        # L447
| Deadlock prevention | MO en eski kilidi kırar |           # L448
| Logging | Lock acquire/release `log.md`'ye yazılır |      # L450
Ajan dosyaya erişmek ister → Lock acquire (max 30s bekleme) # L455-L456
  → Başarılı → dosyayı düzenle                              # L457
  → Başarısız → kuyruk → öncelik sırası → Timeout → escalation  # L458-L459
```

| Kural | Değer | Satır |
|---|---|---|
| Kilitleme üst sınırı | 30 saniye | L447, L466 |
| Öncelik | CRITICAL > HIGH > MEDIUM > LOW | L449 |
| Deadlock kırma | MO en eski kilidi kırar / CRITICAL diğerini kırar | L467-L468 |
| Logging | acquire/release `log.md`'ye yazılır (append-only, kök AGENTS §5) | L450 |

> **Kod karşılığı:** Context Lock kural vault'ta tanımlıdır; `shared/src/**` içinde lock
> uygulayan PHP kodu **YOK** (§30) — kural IMPLEMENTED değil, süreç (insan+agent) seviyesinde.

## §22. `.ai/AGENTS.md` §24.2 — Okuma Sırası (boot zinciri tablosu)

Dosya: `.ai/AGENTS.md` · Satır: L499-L518

```text
#### §24.2 .ai Reference Tracking                          # L499
**Her görev başında bu dosyaları OKU:**                    # L501
| 1 | `.ai/CLAUDE.md` | AI anayasası, 16 Hard Guardrails |  # L505
| 2 | `.ai/AGENTS.md` | Agent sınırları, routing |         # L506
| 3 | `.ai/WORKFLOW.md` | Süreçler, fazlar |               # L507
| 10 | `.ai/ULTRA-THINKING.md` | Ultra düşünme protokolü | # L514
| 14 | `.ai/PROJECTS.md` | Proje envanteri |               # L518
```

| Gözlem | Kanıt |
|---|---|
| 14 dosyalık sabit sıra — bağlam paketinin kanonik içeriği | L503-L518 |
| "Her görev başında" = zorunlu, koşulsuz | L501 |
| Sıra numaralı; boot zinciri betiği (§9) bu sıradan türetilir | §9 |
| İlgili mutabakat: kök AGENTS §14 "FULL boot 17 öğedir" (§25.4) | kök AGENTS §25.4 |

## §23. İllüstratif — Python: Context Window Budgeter

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# PromptEngine::optimizePrompt (PHP, L101-L135) ile aynı kademeli mantık:
# bütçe yeterli -> dokunma; değil -> bosluk -> liste -> baglam kademeleri.
CHARS_PER_TOKEN = 3.5          # repo böleni (PromptEngine L95)
COMPRESSION_THRESHOLD = 0.8    # repo eşiği (PromptEngine L26)

def fit_budget(prompt: str, max_tokens: int) -> tuple[str, bool]:
    used = estimate_tokens(prompt)
    if used <= max_tokens:
        return prompt, False
    budget_chars = int(max_tokens * CHARS_PER_TOKEN * COMPRESSION_THRESHOLD)  # L124-L125 analoğu
    return prompt[:budget_chars] + "\n... (truncated)", True

def estimate_tokens(text: str) -> int:
    import math
    return math.ceil(len(text) / CHARS_PER_TOKEN)     # repo: ceil(mb_strlen/3.5) — L94-L95
```

**Kapsam:** token bütçesi + kademeli kırpma — repo karşılığı §14-§15'te (PHP olarak).

## §24. İllüstratif — TypeScript: Token Counter + Truncator

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```typescript
// countTokens + truncateContext (PromptEngine L90-L96, L307-L324) birebir mantık ikizi.
export const CHARS_PER_TOKEN = 3.5;

export function countTokens(text: string): number {
  return Math.ceil([...text].length / CHARS_PER_TOKEN);   // [...text] = kod nokta UTF-16 değil
}

export function truncateContext(text: string, maxTokens: number): string {
  const start = text.indexOf("## Context");               // L309: strpos($text, '## Context')
  if (start === -1) {
    const chars = Math.floor(maxTokens * CHARS_PER_TOKEN * 0.8);   // L312
    return text.slice(0, chars) + "\n... (truncated)";
  }
  const chars = Math.floor(maxTokens * CHARS_PER_TOKEN * 0.3);     // L320: %30 bağlam payı
  return text.slice(0, start) + text.slice(start, start + chars) +
    "\n... (context truncated)";
}
```

**Kapsam:** token sayacı + bağlam kırpıcı — repo karşılığı PHP'de (§13, §15).

## §25. İllüstratif — Go: Boot File Chain Loader

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
// Boot zinciri kalıbı (§9) + wiki-link-check indeks kurulumu (§3 L10-L16) mantığı.
// Sıra sabit (AGENTS §24.2), dosya yoksa say ve atla, sayılar ölçümle yazılır.
func LoadBootChain(dir string, chain []string) ([]FileStat, []string) {
    var stats []FileStat
    var missing []string
    for _, name := range chain {
        p := filepath.Join(dir, name)
        b, err := os.ReadFile(p)              // Go dosyayı her zaman bayt okur; kodlama seçimi açık
        if err != nil { missing = append(missing, name); continue }   // §9: EKSIK + continue
        stats = append(stats, FileStat{Name: name, Lines: bytes.Count(b, []byte("\n")) + 1})
    }
    return stats, missing
}
```

**Kapsam:** sıralı zincir yükleyici — repo karşılığı betik olarak §9; PHP tarafında `.ai/**/*.md`
okuyan loader **YOK** (rag-ornekler §12 ile aynı yokluk).

## §26. İllüstratif — Rust: Ring-Buffer Conversation Context

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```rust
// Sabit kapasiteli oturum bağımlı bağlam halkası — bellek sınırı bilinçli.
// Repo karşılığı: MemorySystem::setSession/getSession (L45-L75) — TTL var, kapasite sınırı YOK.
pub struct RingConversation { buf: Vec<String>, cap: usize }
impl RingConversation {
    pub fn new(cap: usize) -> Self { Self { buf: Vec::with_capacity(cap), cap } }
    pub fn push(&mut self, msg: String) {
        if self.buf.len() == self.cap { self.buf.remove(0); }   // en eskisi düşer
        self.buf.push(msg);
    }
    pub fn tokens(&self, chars_per_token: f32) -> usize {
        let chars: usize = self.buf.iter().map(|m| m.chars().count()).sum();
        (chars as f32 / chars_per_token).ceil() as usize
    }
}
```

**Kapsam:** kapasite sınırlı bağlam halkası — repo'da karşılığı YOK; repo `sessions` dizisi
sınırsız büyüyebilir (MemorySystem L33-L34 — sınır/eviction yalnız TTL, L69-L72).

## §27. İllüstratif — C++: LRU Context Cache

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```cpp
// MemorySystem::remember (PHP, L123-L147) cache-aside mantığının LRU'lu hâli.
// Repo'da eviction yalnız TTL (L129); LRU sıralaması YOK.
#include <list>
#include <unordered_map>
#include <string>

class LruContextCache {
    size_t cap; std::list<std::string> order;
    std::unordered_map<std::string, std::list<std::string>::iterator> idx;
public:
    explicit LruContextCache(size_t c) : cap(c) {}
    bool touch(const std::string& k) {                    // hit -> one'a al
        auto it = idx.find(k); if (it == idx.end()) return false;
        order.splice(order.begin(), order, it->second); return true;
    }
    void put(const std::string& k) {                      // miss -> ekle, taşanı düşür
        if (idx.count(k)) { touch(k); return; }
        order.push_front(k); idx[k] = order.begin();
        if (order.size() > cap) { idx.erase(order.back()); order.pop_back(); }
    }
};
```

**Kapsam:** LRU eviction — repo karşılığı YOK (MemorySystem eviction = TTL tekil, §30).

## §28. İllüstratif — C#: Async File Chain Loader (UTF8 açık)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```csharp
// Kalıp §9'un async hâli: açık UTF8, yokluğu kabul et, ölçümle raporla.
// .NET notu (upstream §36-1): UTF8Encoding(true) BOM yazar; encoding verilmeden
// okumada BOM yoksa varsayılan UTF-8 devreye girer — yine de açık vermek kanoniktir.
using System.Text;

public static async Task<List<(string Name, int Lines)>> LoadChainAsync(
    string dir, IReadOnlyList<string> chain, CancellationToken ct)
{
    var enc = new UTF8Encoding(false);            // betiklerdeki UTF8Encoding($false) ikizi (§7 L67)
    var outp = new List<(string, int)>();
    foreach (var name in chain) {
        var p = Path.Combine(dir, name);
        if (!File.Exists(p)) continue;            // §9: EKSIK + continue
        var text = await File.ReadAllTextAsync(p, enc, ct);   // açık kodlama — §5 L19 ikizi
        outp.Add((name, text.Split('\n').Length));
    }
    return outp;
}
```

**Kapsam:** asenkron zincir yükleyici — repo `.ai/**/*.md` loader'ı YOK (rag-ornekler §12).

## §29. Kavram ↔ Eşleşme Tablosu (bağlam kavramı → repo karşılığı)

| Vault kavramı | Repo kod karşılığı | Eşleşme |
|---|---|---|
| Boot zinciri (sabit okuma sırası) | — (yalnız betik/kural: §9 sentez, §22 tablo) | **kısmi** |
| Bağlam enjeksiyonu (prompt'a bağlam) | `PromptEngine::generatePrompt` L63-L72 | **evet** |
| Token ölçümü | `countTokens` L90-L96 (tahmin; tokenizer YOK) | **kısmi** |
| Token bütçesi kapısı | `validatePrompt` L169-L174 + `MAX_TOKENS_DEFAULT` L25 | **evet** |
| Kademeli bağlam kırpma | `optimizePrompt` L101-L135 | **evet** |
| Bağlam bölümü kırpma | `truncateContext` L307-L324 | **evet** |
| Oturum bağlamı (session state) | `AIOrchestrator::getContext/updateContext` L154-L170 | **evet** |
| Bağlam TTL / bayatlık | `MemorySystem::getSession` L69-L72 · `remember` L129 | **evet** |
| Okuma öncelik hiyerarşisi (L1/L2/L3) | `MemorySystem::HIERARCHY` L25-L31 | **kısmi** |
| Context Lock (dosya kilidi) | — (kural §21; kod YOK, §30) | **YOK** |
| Bağlam toplama zinciri (session→request→prompt) | `AIWorkflow::recommendationWorkflow` L54-L72 | **evet** |
| Bağlam ölçümünün raporlanması | `prompt_tokens` metadata L86, L187 | **evet** |
| Bağlam bayatlığı sinyali (betik) | screens-frontmatter L65-L67 "bayat" listesi | **evet** |
| Yaz-geri-oku-doğrula (paket doğrulama) | figma-tokens R2/R3 L249-L270 | **kısmi** |

## §30. KOD YOK Alanlar (yokluk cümleleri — tahmin yasak)

| Alan | Durum (2026-10-17) |
|---|---|
| Context Lock uygulayan PHP kodu (mutex/kilit) | Repo'da bu alan kodu YOK — kaynak: upstream docs (kural §21, kod YOK) |
| ContextManager ayrı sınıfı (yorum L22'de anılır) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Gerçek tokenizer (BPE/tiktoken eşdeğeri) | Repo'da bu alan kodu YOK — kaynak: upstream docs (L92 yorumu) |
| `.ai/**/*.md` boot dosyalarını okuyan PHP loader | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| LRU / kapasite sınırlı bağlam cache'i | Repo'da bu alan kodu YOK — kaynak: upstream docs (yalnız TTL) |
| Context window ölçümlü bütçeleme (model bazlı) | Repo'da bu alan kodu YOK — kaynak: upstream docs (4096 sabit, L25) |
| Context lock.acquire/release logging kodu | Repo'da bu alan kodu YOK — kaynak: upstream docs (kural L450) |
| Bağlam (context) için ayrı test dosyası | Repo'da bu alan kodu YOK — kaynak: upstream docs |

## §31. Negatif Ölçüm — Grep Kanıtları (araç çıktısı, 2026-10-17)

| Arama | Kapsam | Sonuç |
|---|---|---|
| `-cmatch` · `-imatch` | `.ai/scripts/*.ps1` | **0 isabet** (§10) |
| `context\|Context\|token\|Token\|budget\|truncat\|window` | `shared/src/AI` | **en az 80 isabet** (ilk 80 döndü, taşma var) — §12-§20 bu turdan |
| `function truncate` | `PromptEngine.php` | 2 isabet: L278 `truncateLongLists` · L307 `truncateContext` |
| `COMPRESSION_THRESHOLD` | `PromptEngine.php` | 2 isabet: L26 tanım · L312 kullanım (ölü sabit DEĞİL) |
| `Context Lock\|Lock acquire` | `.ai/AGENTS.md` | 5 isabet: L199, L273, L439, L456, L462 — kural vault'ta |

## §32. Güvenlik Notları (bağlam yüzeyi)

| Not | Kanıt |
|---|---|
| Bağlam girdisi güvenilmez metindir — `validatePrompt` injection desenleriyle süzülür | PromptEngine L27-L35, L162-L167 |
| Hassas veri deseni bağlamda yakalanır (`password\|secret\|api[_-]?key\|token`) | PromptEngine L177-L179 |
| Secret bağlama/ökse girmez: Figma anahtarları yalnız `.env.figma`'dan regex ile okunur | figma-tokens L16-L21 · figma-extract L25-L31 |
| Betiklerde sabit anahtar/metin yok (env SSOT) | figma-extract L25 yorumu "betik icinde sabit anahtar YOK" |
| Uzantısız betik çalıştırma yok: `#Requires -Version 5.1` 6/6 betikte | wiki L1 · screens L1 · kalip L1 · device L1 · tokens L1 · extract L1 |

## §33. Ölçüm Kalıpları (yeniden üretilebilir)

| Amaç | Kalıp | Repo örneği |
|---|---|---|
| Satır sayısı | `[IO.File]::ReadAllLines($p,[Text.Encoding]::UTF8).Count` | kalip L19 · device L34 |
| Mojibake taraması | `ReadAllText` içinde U+FFFD ara → hedef 0 | §34 kapı 6 |
| FM alanı sayısı | `[regex]::Escape($k)` + `-match "^\s*$k\s*:"` | screens L37 · kalip L30 |
| Geri-oku-doğrula | yaz → `ReadAllText`/`ConvertFrom-Json` → meta <> disk sayım | figma-tokens L249-L270 |
| Idempotentlik | başlık/imza kontrolü + `exit 0` | device L35 |
| Özet gate çıktısı | `dosya: N \| sorunlu: M` / `SONUC -> ...` / `exit $exit` | screens L73 · kalip L230-L231 |
| Betik çalıştırma yeri | repo kökünden (`C:\www\coremusic.net`) | kök AGENTS §13.6 |

## §34. Doğrulama Kapıları (bu dosya)

| # | Kapı | Kriter | Durum |
|---|---|---|---|
| 1 | Satır sayısı | `>= 500` (alt sınır; `ReadAllLines UTF8 Count`) | §33 kalıbıyla ölçülür |
| 2 | Frontmatter 10 alan · `title`/`parent` yok | ilk 14 satır | gözden geçirme |
| 3 | Authority son satır | birebir eşleşme | gözden geçirme |
| 4 | Betik kanıtı | her alıntı gerçek `Dosya:`/`Satır:` (§3-§8) | 2026-10-17 okuma |
| 5 | PHP kanıtı | her alıntı gerçek `Dosya:`/`Satır:` (§12-§20) | 2026-10-17 okuma |
| 6 | Mojibake + placeholder | U+FFFD 0 · bekleyen `{{...}}` yer tutucu yok | grep + §33 |
| 7 | İllüstratif etiket | 6 blok, her biri birebir etiketli (§23-§28) | gözden geçirme |
| 8 | Section sayısı | en az 25 `## §` | grep |

## §35. Açık Sorular (dosya-ötesi, raporlanan)

| # | Soru | Kanıt | Davranış |
|---|---|---|---|
| 1 | `depends_on: [context-anayasa, context-surucu]` hedefleri diskte var mı? | `context/` glob 2026-10-17 → **yalnız `index.md`** | hâlihazırda üretilmedi; bağımlılık gelecek üretimlere işaret eder |
| 2 | `context/index.md` "children: [] — kademe-3 bu fazda AÇILMADI" notu ile bu dosya çelişir mi? | index.md L6 | bilgi: bu dosya kademe-3 değildir (`node_type: module`); index children alanı orkestratör kararıyla güncellenmeli |
| 3 | `shared/AGENTS.md` §5 ".ai/architecture/** kaldırıldı (2026-10-12)" notu ile disk gerçekliği? | glob 2026-10-17 → `K015-AI/` altında 32 md mevcut | disk kazanır (Zero-Hallucination); not ile disk arasındaki fark üst karara raporlanır |
| 4 | Context Lock kuralı ne zaman koda geçecek? | §21 + §30 | ADR gerektirir; bu dosya karar üretmez |
| 5 | Türkçe için token böleni (3.5) upstream ile hizalanacak mı? | §13 (repo yorumu "2-3 karakter" vs kod 3.5) | gerçek tokenizer seçimine bağlı (rag-ornekler §15 #5 PLANNED) |

## §36. Kaynaklar

| # | Kaynak | URL | Kullanım |
|---|---|---|---|
| 1 | Microsoft Learn — File.ReadAllText (BOM algılama + Encoding overload önerisi) | https://learn.microsoft.com/en-us/dotnet/api/system.io.file.readalltext | §10, §28 — okuma kodlama gerekçesi |
| 2 | OpenAI — Tokenizer (kural-of-thumb: ~4 karakter/token, İngilizce) | https://platform.openai.com/tokenizer | §13 — repo böleni 3.5 ile karşılaştırma |
| 3 | Microsoft Learn — Using Byte Order Marks (UTF-8 BOM isteğe bağlı; EF BB BF) | https://learn.microsoft.com/en-us/windows/win32/intl/using-byte-order-marks | §10 — `UTF8Encoding($false)` gerekçesi |
| 4 | Vault: `.ai/AGENTS.md` §12 (L439-L469) · §24.2 (L499-L518) · §13.6 · §25.4 | yerel (disk) | §21, §22, §33 |
| 5 | Vault: `.ai/scripts/*.ps1` (6 betik) · `shared/src/AI/` (7 PHP) | yerel (disk, salt-okunur) | §2-§8, §11-§20 |

## §37. Gate Özeti

§1-§37: künye formatı 10 kural · betik envanteri 6 satır · betik kanıt bölümü 6 (wiki-link,
screens-frontmatter, kalip-abc, figma-tokens, figma-extract, device-matrix) · kalıp sentez 1 ·
mojibake 5 satır + grep negatif · PHP kanıt bölümü 9 (PromptEngine ×5, AIOrchestrator,
MemorySystem, AIWorkflow, AIEngine) · vault alıntısı 2 (§12, §24.2) · illüstratif 6 blok / 6 dil ·
eşleme 14 satır · KOD YOK 8 satır · grep negatif 5 satır · güvenlik 5 · ölçüm 7 · kapı 8 ·
açık soru 5 · kaynak 5 → kapılar: `>=500` · FM 10 alan · Authority son satır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
