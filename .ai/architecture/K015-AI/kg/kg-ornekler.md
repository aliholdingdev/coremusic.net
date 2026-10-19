---
node_id: kg-ornekler
node_type: module
status: active
parent_id: kg
children: []
depends_on: [kg-anayasa, kg-surucu]
evidence:
  - ".ai/scripts/wiki-link-check.ps1 + kalip-abc-check.ps1 (graph doğrulama betik kanıtı, 2026-10-17 okuma)"
  - "shared/src/AI/KnowledgeBase.php (kod kanıtı)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı, kod kanıtı zorunlu"
open_questions: []
group: kod
---

# kg-ornekler - Bilgi Grafiği Kod Örnekleri (kanıt odaklı)

> **Kapsam:** Her `Dosya:`/`Satır:` künyesi 2026-10-17 okuma turuna aittir; alıntılar okunan
> dosyaların gerçek satırlarıdır. İki PowerShell betiği (`.ai/scripts/`) salt-okunur graf
> doğrulama kanıtı, `shared/src/AI/KnowledgeBase.php` ise düğüm depolama/lifecycle kanıtıdır.
> İllüstratif çok-dilli bloklar **repo kanıtı değildir**; her birinin üstünde zorunlu etiket taşır.
> `.ai/scripts/**` ve `shared/src/**` bu görevde yazılmaz.

## §1. Kanıt Künye Formatı

| Alan | Kural |
|---|---|
| Dosya | göreli yol, diskte var (2026-10-17 `read` ile doğrulanmış) |
| Satır | okunan aralık (L24-L38 gibi) — okunmamış satır künye olamaz |
| Alıntı | kod satırının kendisi (kısaltma serbest, uydurma yasak) |
| Yokluk | tam cümle: "Repo'da bu alan kodu YOK — kaynak: upstream docs" |
| Ölçüm | grep sayaçları + false-positive ayıklaması — araç çıktısı, iddia değil |
| İllüstratif | `⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı` |
| Tarih | her kanıt 2026-10-17 okuma turuna ait |
| Eşleşme | `evet` / `kısmi` / `YOK` — başka değer yok |
| Yasak | uydurma künye → Zero-Hallucination ihlali (kök AGENTS §3) · çelişkide disk kazanır |
| Web | `fetch` ile HTTP 200/404 ölçümü; 404 kaynak "doğrulanmadı" sayılır, URL uydurulmaz |

## §2. Okunan Envanter (2026-10-17)

| Dosya | Satır | Rol | Graf ilgisi |
|---|---|---|---|
| `.ai/scripts/wiki-link-check.ps1` | 43 | `[[hedef]]` link denetimi | **doğrudan** — düğüm indeksi + kenar çözme |
| `.ai/scripts/kalip-abc-check.ps1` | 231 | Faz 6 kapısı (Kalıp A/B/C) | **doğrudan** — şema doğrulama + sayım |
| `shared/src/AI/KnowledgeBase.php` | 232 | "Semantic search, RAG, knowledge lifecycle" (L16) | **kısmi** — düğüm lifecycle, kenar YOK |
| `shared/src/Contracts/AI/KnowledgeBaseInterface.php` | 60 | sözleşme | kısmi — lifecycle imzası |
| `shared/src/Api/Bff/SpaBff.php` | 64 | BFF transformer | **iddia/placeholder** — §15 |

## §3. wiki-link-check — Düğüm İndeksi Kurulumu (3 anahtar tipi)

Dosya: `.ai/scripts/wiki-link-check.ps1` · Satır: L5, L9-L16

```powershell
$ROOT='C:\www\coremusic.net\.ai\ui-design'
$index=@{}
Get-ChildItem -Path 'C:\www\coremusic.net\.ai' -Recurse -Filter *.md -File | ForEach-Object {
  $base=$_.BaseName.ToLowerInvariant()
  $rel=$_.FullName.Substring(0,$_.FullName.Length-3)
  if(-not $index.ContainsKey($base)){ $index[$base]=$_.FullName }
  $index[$_.FullName.ToLowerInvariant()]=$_.FullName
  $index[$rel.ToLowerInvariant()]=$_.FullName
}
```

| Gözlem | Kanıt |
|---|---|
| Düğüm kaynağı: `.ai` altı **tüm** `.md` (recursive) — ui-design değil | L10, L5 (ROOT yalnız tarama) |
| 3 anahtar tipi: `BaseName` · tam `FullName` · uzantısız yol (`$rel`) | L11-L12, L14-L15 |
| **Çakışma kuralı: ilk kayıt kazanır** (`-not ContainsKey` guard'ı) | L13 |
| Düğüm kimliği = dosya adı (lowercase) — `node_id` alanı değil | L11, L13 |

## §4. wiki-link-check — Kenar Çözme (3 aşamalı fallback)

Dosya: `.ai/scripts/wiki-link-check.ps1` · Satır: L23-L38

```powershell
$text=[System.IO.File]::ReadAllText($src,$enc)
foreach($m in [regex]::Matches($text,'\[\[([^\]|#]+)(?:[#|][^\]]*)?\]\]')){
  $t=$m.Groups[1].Value.Trim()
  if(-not $t){ continue }
  $checked++
  # 1) dogrudan dosya anahtari
  $k=$t.ToLowerInvariant()
  if($index.ContainsKey($k)){ continue }
  # 2) goreli yol
  $cand=[System.IO.Path]::GetFullPath((Join-Path $dir ($t -replace '/','\')))
  if(Test-Path $cand){ continue }
  if(Test-Path ($cand+'.md')){ continue }
  # 3) sadece dosya adi
  $leaf=[System.IO.Path]::GetFileNameWithoutExtension(($t -replace '/','\'))
  if($index.ContainsKey($leaf.ToLowerInvariant())){ continue }
  $broken.Add("$(Split-Path $src -Leaf) -> [[$t]]")
}
```

| Aşama | Kenar çözümü | Araç |
|---|---|---|
| regex | `[[hedef]]` → hedef grubu; `#anchor` / `|alias` kırpılır | L24 |
| 1 | indeks anahtarı (`$t` lower) | L29-L30 |
| 2 | göreli yol (`/`→`\`), `.md` ekli/eksik iki deneme | L32-L34 |
| 3 | son çare: yalnız dosya adı (yol kaybı — §25 notu) | L36-L37 |
| başarısız | kenar `broken` listesine künyeli eklenir | L38 |

## §5. wiki-link-check — Sonuç Sayımı (ölçüm kapıları)

Dosya: `.ai/scripts/wiki-link-check.ps1` · Satır: L18-L19, L26-L27, L41-L43

```powershell
$broken=New-Object System.Collections.Generic.List[string]
$checked=0
if(-not $t){ continue }     # bos hedef sayilmaZ
$checked++
"kontrol edilen link: $checked"
"kirik link: "+$broken.Count
$broken | Select-Object -First 40 | ForEach-Object { "  "+$_ }
```

| Çıktı | Anlamı | Kanıt |
|---|---|---|
| `kontrol edilen link:` | eşleşen kenar sayısı (boş hedef hariç) | L26-L27, L41 |
| `kirik link:` | 3 aşamada çözülemeyen kenar | L42 |
| İlk 40 örnek | konsol taşmasını önler — **tam liste sayılmaz** | L43 |
| Vault ölçümü (AGENTS §13.7) | `227 link / 6 raporlanan` = **gerçek kırık 0** | kök `.ai/AGENTS.md` §13.7 |

## §6. kalip-abc-check — Frontmatter Çıkarıcı + FM Anahtar Doğrulayıcı

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
| `ReadAllLines(..., UTF8)` — encoding açıkça sabitlenir | L19 |
| 3 satırdan kısa ya da ilk satır `---` değilse → `null` (FM YOK) | L20 |
| Kapanış `---` yoksa → `null` (sonsuza kadar açılmaz) | L21-L23 |
| `Test-FmKeys`: kaçırılan anahtarları **toplu** döndürür (fail-soft) | L27-L34 |
| Regex kaçışı `[regex]::Escape` — anahtar güvenli | L30 |

## §7. kalip-abc-check — Quality Report + Authority Footer Bütünlüğü

Dosya: `.ai/scripts/kalip-abc-check.ps1` · Satır: L37-L61

```powershell
if ($lines[$i] -match '^##\s+') { $h2 += ,@($i, $lines[$i]) }
if ($h2.Count -eq 0) { return @('Quality Report: hic H2 yok') }
$last = $h2[-1]
if ($last[1] -notmatch 'Quality Report') { $issues += "son H2 Quality Report degil ..." }
foreach ($row in @('| Version','| Status','| Cross References','| Last Updated')) {
    if ($tail.IndexOf($row) -lt 0) { $issues += "QR satiri eksik: $row" }
}
if ($text.IndexOf('**Authority:**') -lt 0) { return @('Authority footer yok') }
```

| Düğüm kuralı | Kanıt |
|---|---|
| Son H2 **Quality Report** olmalı (düğümün son düğümü = özet) | L44-L46 |
| 4 zorunlu QR satırı: Version · Status · Cross References · Last Updated | L49-L50 |
| Authority footer 3 parçalı: `**Authority:**` · `**Last Updated:**` · `**Mode:**` | L56-L60 |
| H2 yoksa erken dönüş — `$h2[-1]` boş diziye index atmaz | L43 |

## §8. kalip-abc-check — Kalıp A / B / C Şema Zorunlulukları

Dosya: `.ai/scripts/kalip-abc-check.ps1` · Satır: L64-L65, L101-L110, L118-L119, L159-L162

```powershell
$A_KEYS = @('reference_doc','title','type','category','date','updated',
            'status','version','authority','governance')            # 10 anahtar
$B_SECTIONS = @('## 1. Akış Diyagramı (Decision Flow)','## 2. Ekran Akışı',
    '## 3. Hata Senaryoları','## 4. Tier-Bazlı Varyasyonlar',
    '## 5. BEM Sınıfları','## 6. Adımlar')                          # 6 bolum
$C_H3 = @('### Context','### Required Inputs','### ASCII Reference',
          '### Prompt Template','### Expected Output','### Validation')  # 6 H3
```

| Kalıp | Dizin | Zorunlu şema | Kanıt |
|---|---|---|---|
| A (reference) | kök · `reference/` · `tokens/` | 10 FM + `reference:` blogu + `## 1. Amaç` + QR + footer | L64-L65, L67, L81-L89 |
| B (flow) | `flow/**` | 10 FM + **6 H2 birebir** + footer + `reference:` blogu **YASAK** | L101-L110, L138, L142-L147 |
| C (prompt) | `prompt/**` | 9 FM ( `updated` yok) + tek H2 + 6 H3 sıra + footer | L159-L162, L180, L186-L199 |
| Muaf | `\d\d-*index.md` · `web-research.md` | 8 FM (`governance` yok) — şema kurallarından exempt | L112-L119 |

## §9. kalip-abc-check — H1/H2/H3 Kalıp Eşleşmesi + Sayım Çıkışı

Dosya: `.ai/scripts/kalip-abc-check.ps1` · Satır: L186-L199, L202-L215, L218-L223, L228-L231

```powershell
$h2 = @($lines | Where-Object { $_ -match '^##\s+' })
if ($h2.Count -ne 1) { $issues += "H2 sayisi $($h2.Count) (1 olmali)" }
elseif ($h2[0] -notmatch '^## AI Code Generation Prompt') { $issues += "H2 adi yanlis: $($h2[0])" }
foreach ($want in $C_H3) {
    $found = -1
    for ($i = $idx; $i -lt $h3.Count; $i++) { if ($h3[$i].TrimEnd() -eq $want) { $found = $i; break } }
    if ($found -lt 0) { $issues += "H3 eksik/sira: $want" } else { $idx = $found + 1 }
}
Write-Host "SONUC -> A:$aBad B:$bBad C:$cBad  (0/0/0 = GECTI)"
exit $exit
```

| Kontrol | Kural | Kanıt |
|---|---|---|
| Tek H2 + adı birebir | `## AI Code Generation Prompt` | L186-L188 |
| 6 H3 **sıralı** — `$idx` ilerletici ile sıra korunur | eksik ya da yer değiştirme yakalanır | L191-L199 |
| H1 kalibi dizin'e göre | `component`/`page`/`screen`/`layout` 4 regex | L202-L215 |
| Bileşen/ekran ek anahtarları | `component:` · `tier:` | L218-L223 |
| Çıkış: `A/B/C sorunlu` sayıları + `exit $exit` | 0/0/0 = GECTI · CI gate | L228-L231 |
| Vault ölçümü (AGENTS §13.7) | `A:21·0 / B:21·0 / C:51·0 → GECTI` | kök `.ai/AGENTS.md` §13.7 |

## §10. KnowledgeBase — Düğüm Şeması: Sabitler + Bellek Deposu

Dosya: `shared/src/AI/KnowledgeBase.php` · Satır: L23-L31

```php
class KnowledgeBase implements KnowledgeBaseInterface
{
    private const CATEGORIES = ['adr', 'architecture', 'security', 'audio', 'hardware', 'workflow', 'template'];
    private const MAX_SEARCH_RESULTS = 50;
    private const LIFECYCLE_MAX_AGE_DAYS = 365;
    /** @var array<string, array{id: string, title: string, content: string, category: string, ...}> */
    private array $knowledge = [];
    private int $nextId = 1;
```

| Düğüm alanı | Tip | Kanıt |
|---|---|---|
| Düğüm kimliği | `id` = `'kb_' . $this->nextId++` (L102) | L31, L102 |
| Düğüm etiketi | `title` (arama ağırlığı 0.6 — L171) | L29, L171 |
| Düğüm gövdesi | `content` (ağırlık 0.3 — L176) | L29, L176 |
| Düğüm sınıfı | `category` — 7 sabit değerden biri | L25, L29 |
| Düğüm öznitelikleri | `metadata` (aranabilir `source` — L57) | L29, L57 |
| Kenar (edge) | **YOK** — depoda `edges`/`links`/`refs` alanı hiç yok | §16 |
| Zaman damgası | `created_at` / `updated_at` | L29, L103-L113 |

## §11. KnowledgeBase — Düğüm Lifecycle: ekleme / silme

Dosya: `shared/src/AI/KnowledgeBase.php` · Satır: L96-L116, L121-L129

```php
public function addKnowledge(string $title, string $content, string $category, array $metadata = []): string
{
    if (!in_array($category, self::CATEGORIES, true)) {
        throw new \InvalidArgumentException("Invalid category: {$category}. ...");
    }
    $id = 'kb_' . $this->nextId++;
    $now = date('Y-m-d H:i:s');
    $this->knowledge[$id] = ['id'=>$id,'title'=>$title,'content'=>$content,
        'category'=>$category,'metadata'=>$metadata,'created_at'=>$now,'updated_at'=>$now];
    return $id;
}
public function removeKnowledge(string $id): bool
{
    if (!isset($this->knowledge[$id])) { return false; }
    unset($this->knowledge[$id]);
    return true;
}
```

| İşlem | Davranış | Kanıt |
|---|---|---|
| Ekleme | 7 kategori dış → **exception** (fail-closed) | L98-L100 |
| Kimlik | monoton sayaç `kb_1, kb_2, ...` — UUID değil | L102 |
| Silme | bilinmeyen id → `false` (exception değil) | L123-L124 |
| Kalıcılık | **bellek-içi** — disk/DB yazımı yok (§16) | L29-L31 |

## §12. KnowledgeBase — Bayat Düğüm Temizliği (cleanupLifecycle)

Dosya: `shared/src/AI/KnowledgeBase.php` · Satır: L134-L156

```php
$removed = 0; $updated = 0; $kept = 0;
$cutoffDate = date('Y-m-d', strtotime('-' . self::LIFECYCLE_MAX_AGE_DAYS . ' days'));
foreach ($this->knowledge as $id => $item) {
    if ($item['created_at'] < $cutoffDate) {          // eski kayit temizle
        unset($this->knowledge[$id]); $removed++;
    } else { $kept++; }
}
return ['removed' => $removed, 'updated' => $updated, 'kept' => $kept];
```

| Gözlem | Kanıt |
|---|---|
| Eşik: 365 gün (`LIFECYCLE_MAX_AGE_DAYS`) | L27, L139 |
| Sadece `created_at` bakılır — `updated_at` **dikkate alınmaz** | L142 |
| `$updated` hiç artmaz (ölü sayaç) | L137, L153 |
| Graf karşılığı: **orphan/yarı-düğüm temizliği YOK** — kenar olmadığı için sweep yok | §16 |

## §13. KnowledgeBase::search — Komşuluk Taraması Analogu (O(n))

Dosya: `shared/src/AI/KnowledgeBase.php` · Satır: L36-L67, L163-L194

```php
$queryLower = strtolower($query);
foreach ($this->knowledge as $id => $item) {
    if (!empty($filters['category']) && $item['category'] !== $filters['category']) continue;
    $score = $this->calculateRelevanceScore($queryLower, $item);
    if ($score > 0) { $results[] = ['id'=>$item['id'],'title'=>$item['title'],
        'content'=>mb_substr($item['content'],0,500),'score'=>$score,
        'source'=>$item['metadata']['source'] ?? 'vault','category'=>$item['category']]; }
}
usort($results, fn(array $a, array $b) => $b['score'] <=> $a['score']);
return array_slice($results, 0, $maxResults);
```

| Bileşen | Değer | Kanıt |
|---|---|---|
| Tarama | tek döngü O(n) — komşu listesi yok | L42 |
| Elem | kategori sert eşitlik | L44-L46 |
| Skor | 0.6 başlık + 0.3 içerik + 0.1×oran (min 3 harf) | L171-L191 |
| Sıralama/kesme | azalan · `array_slice` top-N · tavan 50 | L64, L66, L26 |
| Önhafız | içerik önizlemesi 500 karakter | L55 |

## §14. KnowledgeBaseInterface — Düğüm Sözleşmesi

Dosya: `shared/src/Contracts/AI/KnowledgeBaseInterface.php` · Satır: L43, L51, L54-L58

```php
public function addKnowledge(string $title, string $content, string $category, array $metadata = []): string;
public function removeKnowledge(string $id): bool;
/** Knowledge lifecycle yönetimi — eski/yetersiz bilgileri temizler. */
public function cleanupLifecycle(): array;
```

| Sözleşme | Kenar/traversal içerir mi? | Eşleşme |
|---|---|---|
| `addKnowledge` | hayır — tek düğüm | **kısmi** (düğüm evet, kenar YOK) |
| `removeKnowledge` | hayır — tek düğüm, bağlantı temizliği yok | **kısmi** |
| `cleanupLifecycle` | hayır — yaş süzgeci | **kısmi** |
| `search` / `ragQuery` | hayır — skor taraması (L36-L91) | **YOK** (traversal değil) |

## §15. SpaBff "relational graphs" — Söz Var, Kod Placeholder

Dosya: `shared/src/Api/Bff/SpaBff.php` · Satır: L28, L53-L63

```php
// Enrich with nested relational graphs
if (isset($response['data']) && is_array($response['data'])) {
    $response['data'] = $this->enrichWithRelations($response['data']);
}
/**
 * Enrich data with nested relational graphs.
 */
private function enrichWithRelations(array $data): array
{
    // This would be implemented based on specific API responses
    // Placeholder implementation
    return $data;
}
```

| İddia | Kanıt | Değerlendirme |
|---|---|---|
| "nested relational graphs" | L28 + L54 yorum | yalnız yorum |
| Uygulama | L58 "would be implemented" + L61 "Placeholder" | yok |
| Dönüş | L62 `return $data;` | hiç değiştirmez |

**Sonuç:** Repo'da tek "graph" geçen **çalışan** kod yoktur; geçen yerler ya yorum (bu §), ya
harici API adı (`FacebookOAuth.php` L8 / `InstagramOAuth.php` L8 "Meta Graph API"), ya da
`Edge Case` §17 referansı (`Log/Redactor.php` L8 vb.). Graph **algoritması** 0 satır (§17).

## §16. KOD YOK Alanlar (yokluk cümleleri — tahmin yasak)

| Alan | Durum (2026-10-17) |
|---|---|
| Graph kenar/ilişki modeli (`edges`, `links`, `refs`) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Kenar ekleme/çekme API (`addEdge` / `getEdges`) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Traversal (BFS / DFS) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Cycle detector / topolojik sıralama | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Shortest path (Dijkstra / A*) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Adjacency list / matrix yapısı | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Orphan düğüm sweep'i (kenar temizliği) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Kalıcı graph deposu (disk/DB tablosu) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Graph şema doğrulama (tip/alan kontrolü) | Repo'da bu alan kodu YOK — kaynak: upstream docs (betikler FM kontrol eder, graph değil) |
| Depth/visited set koruması | Repo'da bu alan kodu YOK — kaynak: upstream docs |

## §17. Negatif Ölçüm — Grep Kanıtları + False-Positive Ayıklama

| Arama | Kapsam | Sonuç | Tarih |
|---|---|---|---|
| `graph\|Graph\|adjacency\|travers\|BFS\|DFS\|shortest\|cycle\|Cycle` | `.ai/scripts/*.ps1` | 8 isabet — **tamamı `typography` kelimesinin içındaki "graph"** (`figma-tokens.ps1` L28/L84/L90/L132/L189/L241/L247/L296) → **gerçek graf 0** | 2026-10-17 |
| aynı desen | `shared/src/AI/*.php` | **0 isabet** | 2026-10-17 |
| `graph` (geniş) | `shared/src/**` | 6 yorum/isim: `SpaBff` L28+L54 (placeholder §15) · `FacebookOAuth` L8 · `InstagramOAuth` L8 (harici API) → **algoritma 0** | 2026-10-17 |
| `edge\b\|Edge\b` | `shared/src/**` | 5 isabet — **tamamı "Edge Case" §17 referansı** (`Log/Redactor.php` L8, `Log/FileHandler.php` L222, `PageRouter/PageRouterKernel.php` L130, `PageRouter/StructuredLogger.php` L71) → **graf kenarı 0** | 2026-10-17 |
| `graph\|edge\|node_id` (ilk tur) | `shared/src/AI` | 28 isabet — **tamamı `Knowledge` kelimesinin içindetki "edge"** → yanlış pozitif | 2026-10-17 |

**Ders:** `substring` tabanlı graf taraması yanıltıcıdır — `typography` ⊃ `graph`, `Knowledge` ⊃ `edge`.
Graph yokluğu iddiası yalnız **token sınırı** (`\b`) ve kelime ayıklamasıyla yazılır.

## §18. İllüstratif — Python: networkx Graph + Cycle Detector

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
import networkx as nx

def build_graph(edges: list[tuple[str, str]]) -> nx.DiGraph:
    g = nx.DiGraph()
    g.add_edges_from(edges)          # her (kaynak, hedef) bir kenar
    return g

def has_cycle(g: nx.DiGraph) -> bool:
    return not nx.is_directed_acyclic_graph(g)   # DAG değilse döngü var

def cycles(g: nx.DiGraph) -> list[list[str]]:
    return [list(c) for c in nx.simple_cycles(g)]  # tüm basit döngüler
```

**Kapsam:** döngü denetimi — repo karşılığı YOK (§16) · `simple_cycles` karmaşıklığı üstel (worst-case), `is_directed_acyclic_graph` O(V+E).

## §19. İllüstratif — TypeScript: Adjacency List Traverser (DFS + visited)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```typescript
export type Adj = ReadonlyMap<string, readonly string[]>;

// wiki-link-check.ps1 L24 regex'i ile üretilmiş [[hedef]] kenarları için analog
export function traverse(start: string, adj: Adj): string[] {
  const seen = new Set<string>();
  const out: string[] = [];
  const stack: string[] = [start];
  while (stack.length > 0) {
    const n = stack.pop()!;
    if (seen.has(n)) continue;          // döngü koruması
    seen.add(n); out.push(n);
    for (const m of adj.get(n) ?? []) if (!seen.has(m)) stack.push(m);
  }
  return out;                            // ziyaret sırası
}
```

**Kapsam:** komşu listesi + iterative DFS — `visited` seti repo'da **YOK** (§16 son satır) · O(V+E).

## §20. İllüstratif — Go: BFS Shortest Path (kenar ağırlıksız)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
// KnowledgeBase::search (PHP L42) ile aynı tek-döngü zihniyeti, ama komşu üzerinden.
func ShortestPath(adj map[string][]string, from, to string) ([]string, bool) {
    prev := map[string]string{}; seen := map[string]bool{from: true}
    q := []string{from}
    for len(q) > 0 {
        n := q[0]; q = q[1:]
        if n == to { break }
        for _, m := range adj[n] {
            if seen[m] { continue }
            seen[m] = true; prev[m] = n; q = append(q, m)
        }
    }
    if !seen[to] { return nil, false }
    var path []string
    for c := to; c != ""; c = prev[c] { path = append([]string{c}, path...) }
    return path, true
}
```

**Kapsam:** BFS en kısa yol — repo'da **YOK** (§16) · O(V+E) zaman, O(V) bellek.

## §21. İllüstratif — Rust: petgraph Cycle Check

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```rust
use petgraph::graph::DiGraph;
use petgraph::algo::{is_cyclic_directed, toposort};

pub fn audit(edges: &[(u32, u32)]) -> Result<Vec<u32>, Vec<u32>> {
    let mut g = DiGraph::<(), ()>::new();
    let n: Vec<_> = edges.iter()
        .map(|&(a, b)| (g.add_node(()), g.add_node(())))   // basit örnek
        .collect();
    let _ = n;
    for &(a, b) in edges { g.add_edge(a.into(), b.into(), ()); }
    if is_cyclic_directed(&g) { return Err(vec![]); }       // döngü → fail
    toposort(&g, None).map_err(|_| vec![])                  // sıralama da doğrular
}
```

**Kapsam:** `is_cyclic_directed` + `toposort` — repo'da **YOK** (§16) · Rust graph kütüphanesi `petgraph` (kaynak §29).

## §22. İllüstratif — C++: Adjacency Matrix Cycle Check (DFS renklendirme)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```cpp
#include <vector>
// 0 = beyaz, 1 = gri (yolda), 2 = siyah (bitmis)
static bool dfsCycle(const std::vector<std::vector<int>>& m, int u, std::vector<int>& color) {
    color[u] = 1;
    for (int v = 0; v < static_cast<int>(m.size()); ++v) {
        if (!m[u][v]) continue;
        if (color[v] == 1) return true;          // gri komşu = geri kenar = döngü
        if (color[v] == 0 && dfsCycle(m, v, color)) return true;
    }
    color[u] = 2;
    return false;
}
```

**Kapsam:** matrix O(V²) bellek + O(V²) tarama — dense graf için; vault'ta karşılığı YOK (§16). C++ yüzeyi repo'da Neva Engine'e aittir, AI katmanında C++ **YOK**.

## §23. İllüstratif — C#: Dictionary-Based Graph (komşu + BFS)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```csharp
public sealed class KgGraph
{
    private readonly Dictionary<string, List<string>> _adj = new();

    public void AddEdge(string from, string to)
    {
        if (!_adj.TryGetValue(from, out var l)) _adj[from] = l = new List<string>();
        l.Add(to);
    }

    public int BfsDepth(string start)   // ziyaret edilen düğüm sayısı
    {
        var seen = new HashSet<string> { start };
        var q = new Queue<string>(new[] { start });
        while (q.Count > 0)
            foreach (var m in _adj.TryGetValue(q.Dequeue(), out var ns) ? ns : new List<string>())
                if (seen.Add(m)) q.Enqueue(m);
        return seen.Count;
    }
}
```

**Kapsam:** `Dictionary` + `HashSet` komşuluk — repo'da **YOK** (§16). `Dictionary-2` API'si MS Learn üzerinden doğrulandı (§29).

## §24. Kavram ↔ Eşleşme Tablosu (graf kavramı → repo karşılığı)

| Graf kavramı | Repo karşılığı | Eşleşme |
|---|---|---|
| Düğüm (node) | `KnowledgeBase::$knowledge` kaydı (L29-L31, L105-L113) | **evet** (düğüm verisi) |
| Düğüm kimliği | `kb_N` sayaç (L102) | **evet** |
| Düğüm etiketi / sınıfı | `title` / `category` 7 değer (L25, L29) | **evet** |
| Düğüm özniteliği | `metadata` (L29, L57) | **evet** |
| Kenar (edge) | — | **YOK** |
| Kenar ağırlığı / skor | `calculateRelevanceScore` 0.6/0.3/0.1 (L171-L191) | **kısmi** (düğüm skoru, kenar değil) |
| Komşuluk listesi | `$index` (wiki-link-check L9-L16) | **evet** (dosya→dosya) |
| Kenar çözümleme | 3 aşamalı fallback (L29-L37) | **evet** |
| Traversal | — | **YOK** |
| Cycle detection | — | **YOK** |
| Orphan düğüm temizliği | `cleanupLifecycle` yaş süzgeci (L134-L156) | **kısmi** (yaş, bağlantı değil) |
| Graph doğrulama betiği | `wiki-link-check.ps1` + `kalip-abc-check.ps1` | **evet** (statik) |
| Şema doğrulama | `Get-Frontmatter` + `Test-FmKeys` (L18-L34) | **evet** |
| Sayım/rapor çıkışı | `A/B/C sorunlu` + `SONUC` (L228-L230) | **evet** |
| Kalıcı graph deposu | — | **YOK** |

## §25. Güvenlik Notları (kanıt ile)

| Not | Kanıt |
|---|---|
| Betik salt-okunur: "Salt-okunur: raporlar, degistirmez" | kalip-abc-check.ps1 L5 |
| Betik kökten çalıştırılmalı — `.ai/` içinden 19 sahte "PNG yok" üretir | kök `.ai/AGENTS.md` §13.6 |
| Link taraması 3. aşamada **dosya adına düşer** → yanlış eşleşme riski (yanlış düğüm) | wiki-link-check L36-L37 |
| `$index` ilk kayıt kazanır → çakışan dosya adlarında sessiz yanlış eşleme | wiki-link-check L13 |
| Graph verisi hassas olabilir: `metadata`/`content` log'a sızarsa `[REDACTED]` | `.ai/AGENTS.md` §17 #3 |
| Çıktı dosyaya değil konsola yazılır — betik yan etkisiz | L41-L43, L228-L230 |

## §26. Ölçüm Kalıpları (yeniden üretilebilir)

| Amaç | Kalıp |
|---|---|
| FM çıkar | `[IO.File]::ReadAllLines($p,[Text.Encoding]::UTF8)` + `---` sarmalayıcı (§6) |
| FM anahtar eksiği | `Test-FmKeys $fm $KEYS` → `eksik` listesi |
| Link sayımı | `$checked++` (boş hedef hariç) · `$broken.Count` (§5) |
| Sonuç bandı | `A:$aBad B:$bBad C:$cBad` · `0/0/0 = GECTI` · `exit $exit` |
| Düğüm sayısı | `@($this->knowledge)->Count` (runtime, repo'da çağrı yok) |
| Satır sayısı | `[IO.File]::ReadAllLines($hedef,[Text.Encoding]::UTF8).Count` |
| Graph yokluğu | grep + **token sınırı** + kelime ayıklama (§17) |

## §27. Doğrulama Kapıları

| # | Kapı | Kriter | Durum |
|---|---|---|---|
| 1 | Satır sayısı | `>= 500` (band 500-520) | §26 ölçümü |
| 2 | Frontmatter + Authority | 10 alan; `title`/`parent` yok · son satır birebir | gözden geçirme |
| 3 | Betik kanıtı | wiki-link-check + kalip-abc alıntıları gerçek `Dosya:`/`Satır:` | 2026-10-17 okuma |
| 4 | PHP kanıtı | `KnowledgeBase.php` + `SpaBff.php` + interface gerçek künyeli | 2026-10-17 okuma |
| 5 | Yokluk cümleleri | 10 satır "KOD YOK" | §16 |
| 6 | İllüstratif etiket | 6 dilde blok + üstünde etiket | §18-§23 |
| 7 | Section sayısı | en az 25 `## §` | §1-§31 |
| 8 | Web doğrulama | 3+ resmi kaynak fetch · 404'ler "doğrulanmadı" | §29 |

## §28. Açık Sorular

| # | Soru | Durum |
|---|---|---|
| 1 | `[[hedef]]` kenarları graph olarak modellenmeli mi (düğüm↔düğüm) — bugün yalnız statik betik? | OPEN |
| 2 | `KnowledgeBase`'e `edges` alanı eklense `removeKnowledge` bağlantı temizliği yapmalı mı? | OPEN |
| 3 | `cleanupLifecycle` `updated_at`'i neden yok sayıyor (L142) — kasıtlı mı? | OPEN |
| 4 | `$updated` sayacı (L137/L153) neden hiç artmıyor — ölü kod mu? | OPEN |
| 5 | 3. aşama (dosya adı) yanlış eşleşmesi için muaf listesi gerekiyor mu (§25)? | OPEN |
| 6 | `kg-anayasa` / `kg-surucu` bu dosyadan önce yazılacak mı — `depends_on` sırası | OPEN |

## §29. Kaynaklar (web doğrulama — 2026-10-17 `fetch`)

| # | Kaynak | URL | Sonuç |
|---|---|---|---|
| 1 | NetworkX — `simple_cycles` | `https://networkx.org/documentation/stable/reference/algorithms/generated/networkx.algorithms.cycles.simple_cycles.html` | **HTTP 200 doğrulandı** |
| 2 | NetworkX — `find_cycle` | `https://networkx.org/documentation/stable/reference/algorithms/generated/networkx.algorithms.cycles.find_cycle.html` | **HTTP 200 doğrulandı** |
| 3 | petgraph (Rust) — crate root | `https://docs.rs/petgraph/latest/petgraph/` | **HTTP 200 doğrulandı** |
| 4 | Microsoft Learn — `Dictionary<TKey,TValue>` | `https://learn.microsoft.com/en-us/dotnet/api/system.collections.generic.dictionary-2` + `.../dictionary-2.trygetvalue` | **HTTP 200 doğrulandı** |
| 5 | `petgraph` eski docs yolu | `https://petgraph.github.io/petgraph/stable/petgraph/index.html` | **HTTP 404 — kullanılmadı** |
| 6 | petgraph algorithms indeksi | `https://docs.rs/petgraph/latest/petgraph/algorithms/index.html` | **HTTP 404 — kullanılmadı** |
| 7 | NetworkX `adjacency_data` | `https://networkx.org/documentation/stable/reference/readwrite/generated/networkx.readwrite.adjacency_data.adjacency_data.html` | **HTTP 404 — kullanılmadı** |

**Kural:** 404 dönen URL'ler bu dosyada referans olarak **kullanılmaz**; yeni URL **uydurulmaz** —
eksik bilgi `⚠️ VERIFICATION REQUIRED` olarak kalır. TypeScript / Go / C++ için resmi URL
bu turda sorgulanmadı → `⚠️ VERIFICATION REQUIRED` (dil kendisi kaynak kabul edilmez).

## §30. Yasaklar (bu dosya ve bu alt-dal)

| Yasak | Kural kaynağı |
|---|---|
| `git commit` atmak | görev manifesti · kök AGENTS §16.5 |
| `.ai/scripts/**` ve `shared/src/**` yazımı (salt-okunur) · bu dosya dışına yazım | görev manifesti · `.ai/AGENTS.md` §13.9 #4 |
| Uydurma `Dosya:`/`Satır:` künyesi · illüstratif bloğa repo kanıtı süsü vermek | Zero-Hallucination (kök AGENTS §3) · §1 |
| Uydurma URL · 404 kaynak kullanmak | §29 · Zero-Hallucination |
| `title`/`parent` frontmatter anahtarı yazmak | görev manifesti (birebir FM şeması) |
| Dosya adını değiştirmek | görev manifesti |
| False-positive grep sonucunu "graf kodu" diye saymak (§17 dersi) | §17 |

## §31. Gate Özeti

§1-§31: künye formatı 10 kural · envanter 5 dosya · **betik kanıtı 2 dosya** (wiki-link-check
43 satır: indeks L9-L16 · regex+fallback L24-L38 · sayım L41-L43; kalip-abc 231 satır: FM L18-L34
· QR/footer L37-L61 · şema L64-L162 · H1/H2/H3 L186-L223 · çıkış L228-L231) · **PHP kanıtı 3 dosya**
(KnowledgeBase L23-L31 / L36-L67 / L96-L129 / L134-L156 / L163-L194 · KnowledgeBaseInterface
L43-L58 · SpaBff L28-L62 placeholder) · KOD YOK 10 satır · grep negatif 5 satır (+ false-positive
ayıklama: `typography`⊃`graph`, `Knowledge`⊃`edge`) · illüstratif **6 blok / 6 dil**
(Python, TypeScript, Go, Rust, C++, C#) · kavram↔eşleşme 16 satır · güvenlik 6 · ölçüm 7 ·
açık soru 6 · kaynak 7 (4×HTTP 200 · 3×HTTP 404) · yasak 7 → kapılar: `>=500` · FM 10 alan ·
Authority son satır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
