---
node_id: kg-anayasa
node_type: module
status: active
parent_id: kg
children: []
depends_on: [kg-index]
evidence:
  - ".ai/architecture/index.md (K-düğümleri master graph, okundu 2026-10-17)"
  - ".ai/.decisions/index.md + .ai/RAG.md §3 (ADR/konu kenarları)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı"
open_questions: []
group: mimari
---

# kg-anayasa - Bilgi Grafiği Kuralları

> **Bu dosya ne değildir:** grafiği **üreten/depolayan** bir motorun (triple store, vektör DB,
> otomatik indeksleyici) kodu **değildir** — repo'da böyle bir motor **yoktur** (ADR-030: "kod 0").
> Burada **graf anayasası** vardır: düğüm şeması, kenar türleri, bütünlük kuralları, doğrulama kapıları.

## §1 Rol (ELI15)

KG (knowledge graph) alt-düğümü, vault'taki **kavram–dosya–karar ilişkilerinin grafiğini** tanımlar:
her `.md` dosyası bir **düğüm**, frontmatter/iki-link/eşleme satırı bir **kenardır**. Bu anayasa,
düğüm/kenar şemasını, bozulma türlerini (phantom, kırık kenar, döngü, yetim) ve bunların
**PowerShell ile tekrarlanabilir doğrulamasını** bağlayıcı kılar.

| Soru | Cevap | Kanıt |
|------|-------|-------|
| Graph motoru repo'da var mı? | Hayır — kod 0, yalnız `.md` grafiği | `ADR-030:36` · `ADR-030:69` |
| Bugünkü graph nerede? | Frontmatter alanları + wiki-link'ler + keys/RAG eşlemeleri | `.ai/keys.md` · `.ai/RAG.md` §3 |
| Sahibi kim? | MO (vault-updater) + Vault Steward (denetim) | `.ai/RAG.md:22` |
| Bu dosyanın ebeveyni? | `K015-AI/kg` (subdomain, draft) | `kg/index.md:2-6` |

> **eli10:** Vault'un dosyalar arası "kim kime bağlı" haritasının kuralları burada.
> **eli15:** Harita `.md` içinde yaşar; harita bozulunca ajan yanlış dosyaya gider → kural ihlali.

## §2 Kapsam (in / out) — graph ≠ indeks ≠ RAG

| Kapsam | Kapsam Dışı | Kanıt |
|--------|-------------|-------|
| Düğüm şeması (frontmatter 10 alan) ve kenar türleri | Dosya **içeriği** özetleri | `kg/index.md:23` (wiki-link'lerin taşıyıcısı) |
| Graph bütünlük kuralları + PowerShell doğrulama | Otomatik retrieval/embedding kodu (ADR-030 PLANNED) | `ADR-030:18` |
| K-düğümü (K000–K020) ve ADR kenarlarının **tanımı** | ADR metinleri (frozen — yalnız referans) | `.ai/.decisions/index.md:38` |
| keyword→dosya / konu→dosya eşlemelerinin **kenar olarak** okunması | Eşleme tablolarının **kopyası** (SSOT: keys.md / RAG.md) | `.ai/RAG.md:58-61` |
| Fark: **indeks** = tek yönlü eşleme tablosu; **RAG** = getirme pipeline'ı; **KG** = üçünün ilişki modeli | Embedding vektörleri (KAPSAM DIŞI) | `.ai/RAG.md:59` |

**Ayırım kuralı:** `keys.md` bir **düğüm kataloğu + keyword→dosya kenarı**dır; `RAG.md` §3 bir
**konu→dosya kenarı** ve retrieval tasarımıdır; bu anayasa ikisini de **graf açısından** yorumlar,
kopyalamaz. Çelişirse SSOT dosya kazanır (`Dosya: .ai/RAG.md · Satır: 9`).

## §3 Düğüm Şeması — frontmatter (FM) 10 alan = düğüm kaydı

| # | FM alanı | Graf rolü | Kural | Kanıt |
|---|----------|-----------|-------|-------|
| 1 | `node_id` | düğüm adı (benzersiz) | vault geneli tekil; tekrar = ID CONFLICT | `kg/index.md:2` · `index.md:95` (K020 çakışması) |
| 2 | `node_type` | düğüm etiketi (label) | `module` / `subdomain` / `index` vb. | `kg-anayasa` FM · `kg/index.md:3` |
| 3 | `status` | düğüm durumu | `active` / `draft` / `verify-required` | `kg/index.md:4` |
| 4 | `parent_id` | **kenar**: child→parent (üst) | hedef diskte olmalı | `kg/index.md:5` |
| 5 | `children` | **kenar**: parent→child (alt) | her iki yönde tutarlılık | `kg/index.md:6` |
| 6 | `depends_on` | **kenar**: bağımlılık (DAG) | döngü yasak (§6) | `kg-anayasa` FM · `kg/index.md:7-8` |
| 7 | `evidence` | **kenar**: dosya→kanıt satırı | disk künyesi + tarih | `kg-anayasa` FM (2 madde) |
| 8 | `validation` | kapı kaydı | ölçüm yöntemi yazılı | `kg-anayasa` FM |
| 9 | `open_questions` | açık soru düğümü | boşsa `[]` | `kg/index.md:13-14` |
| 10 | `group` | iki-ekseni grup etiketi | `mimari` / `kod` / `kanit` | `kg/index.md:15` · `index.md:58` |

**FM 10 alan bu anayasanın bağlayıcı şemasıdır**; `title`/`parent` anahtarı **YASAKTIR** (bu üretim
talimatı) — `parent_id` tek geçerli üst kenarıdır.

## §4 Kenar Türleri (4 tip — taşıyıcı yüzeyler)

| # | Kenar | Yön | Taşıyıcı | Doğrulama | Kanıt |
|---|-------|-----|----------|-----------|-------|
| 1 | `parent-child` | child→parent, parent→children | FM `parent_id` / `children` | iki yönlü tutarlılık | `kg/index.md:5-6` |
| 2 | `depends_on` | bağımlı→bağımlılık | FM `depends_on` | döngü yok + hedef var | `kg-anayasa` FM `[kg-index]` |
| 3 | `evidence` | iddia→dosya/satır | FM `evidence` + `Dosya:/Satır:` künyesi | Test-Path + satır okuma | `kg-anayasa` FM |
| 4 | `wiki-link` | dosya→dosya | `[[hedef]]` metni | 3 kademeli çözüm (§13) | `.ai/scripts/wiki-link-check.ps1:24` |

Ek **dolaylı kenar**: `keys.md` keyword→dosya (`keys.md:44-61`) ve `RAG.md` §3.1 konu→dosya
(`RAG.md:73-94`) satırları da graf kenarı sayılır — amaç: tek bir doğrulama modeli. Wiki-link
biçimi **`[[...]]`**'dir; markdown path linki **değil** (`Dosya: .ai/RAG.md · Satır: 138`).

## §5 Graph Bütünlüğü I — phantom düğüm + kırık kenar

| # | Bozulma | Tanım | Kanıt örneği | Doğru davranış |
|---|---------|-------|--------------|----------------|
| 1 | **Phantom düğüm** | kenarın hedefi hiç var olmamış | `keys.md:177` — `electronic/hardware-roadmap.md` **vault'ta yok** | hedef satır `DOĞRULAMA GEREKLİ` işaretli (keys.md bunu yapar) |
| 2 | **Kırık kenar** | hedef bir zaman vardı, artık yok | `keys.md:179` — `asio-driver-design.md` yok; yakın karşılık işaretli | kenar yeni hedefe taşınır ya da işaretlenir |
| 3 | Kırık wiki-link | `[[hedef]]` 3 kademeli çözümde bulunamıyor | bilinen **6 sahte** kırık (şablon değişkeni) — düzeltme YASAK | `AGENTS.md` §13.7 listesi esas |
| 4 | Hayalet satır | indeks satırı glob'da 0 isabet | RAG eşleme kuralı 1 | satır **silinir** (`RAG.md:102`) |

**Zincir kural:** phantom ≠ kırık ≠ sahte-kırık; üçü **aynı muameleyi görmez** — phantom
`VERIFICATION REQUIRED`, kırık düzeltilir, sahte-kırık sayılmaz ve sayı 6'da kalır
(`Dosya: .ai/AGENTS.md · §13.7` — kök boot). Phantom kanıtı keys.md'de **açıkça işaretlidir**
(`keys.md:177-180` dört satır `DOĞRULAMA GEREKLİ`) — graph'ta işaretli eksiklik hata değildir.

## §6 Graph Bütünlüğü II — döngü, yetim düğüm, duplicate ID

| # | Bozulma | Tanım | Tarama | Sonuç |
|---|---------|-------|--------|-------|
| 1 | **Döngü** | A→B→A (depends_on alt grafında) | DFS/toposort; `nx.simple_cycles` (ağ) | döngü = ihlal; kenar kırılır |
| 2 | **Yetim düğüm** | `parent_id` dolu, parent'ın `children`'ında yok | iki yönlü tutarlılık taraması | children'a eklenir ya da parent düzeltilir |
| 3 | **Duplicate node_id** | iki dosya aynı `node_id` | FM taraması + hashtable | ID CONFLICT — `index.md:95` (6× K020 kayıtlı) |
| 4 | **Kök-sızıntı** | `parent_id` boş ama gerçek üstü var | registry okuma | üst boşluk **bilinçlidir** (ID GAP — `index.md:94`, K002) |

Döngü tanımı literatürle çapraz doğrulandı: "simple cycle = hiç düğümün iki kez görünmediği kapalı
yol" (NetworkX docs, §30/3). `depends_on` alt grafiği **DAG olmak zorundadır**; `parent-child`
alt grafiği ise **ağaçtır** (tek kök, döngü yok). K002 ID GAP'i döngü değildir — bilinçli boşluktur,
yeniden numaralandırma **yasak** (`index.md:119`).

## §7 K-Düğümü Grafiği (K000–K020 — registry gerçek okuma)

> Kaynak: `.ai/architecture/index.md` §5 Band-1 Domain Kayıt Tablosu (25 klasör — okundu 2026-10-17).

| # | Düğüm (klasör) | Master dosyası | Durum |
|---|----------------|----------------|-------|
| 1 | K000-isletim-sistemi | os-master.md | draft |
| 2 | K001-donanım | donanım-anayasa.md | draft |
| 3 | K003-audio … K014-media | `*-master.md` (K003/K004/K005/K006/K007/K008/K009/K0100-identitiy/K011/K012/K013/K010-network/K014) | draft |
| 4 | **K015-AI** | ai-master.md | draft — **bu dosyanın atası** |
| 5 | K017-observability · K018-devops · K019-testing | `*-master.md` | draft |
| 6 | K020 ×6 (adr/comilance/comere/licence/discovery/recomenataion) | karar/kayıt dosyaları | draft · comere = **verify-required** |

Ölçüm: **21 slot / 25 klasör** (19 tekil domain + K002 GAP + 6× K020). K002 **hiç yok**
(K001→K003 atlar) — `index.md:94` (`⚠️ VERIFY REQUIRED`). K020-comere içeriği **BOŞ**, tahmin
yasak (`index.md:96`). İmla-korunaklı yazımlar (`identitiy`, `integraion`, `comilance`…) düğüm
adıdır, **düzeltilemez** (`index.md:97`).

## §8 Band Cetveli — aralık kenarları (5.001 slot)

| Bant | Aralık | Slot | Durum |
|---|---|---|---|
| Band-1 | K000 – K020 | 21 | ✅ TANIMLI — 25 klasör |
| Band-2 | K021 – K120 | 100 | ⏸ RESERVED (klasör yok, aralık tescilli) |
| Band-3 | K121 – K320 | 200 | ⏸ RESERVED |
| Band-4 | K321 – K720 | 400 | ⏸ RESERVED |
| Band-5 | K721 – K1520 | 800 | ⏸ RESERVED |
| Band-6 | K1521 – K3120 | 1.600 | ⏸ RESERVED |
| Band-7 | K3121 – K5000 | 1.880 | ⏸ RESERVED |
| **TOPLAM** | **K000 – K5000** | **5.001** | 20 tanımlı + 1 GAP + 4.980 rezerve |

**Graf kuralı (ADR-097 K5):** rezerve slot **düğüm değildir** — kenar指向 etmez; ancak ADR ile
gerçek domain tanımlanınca isim/künye alır → klasör o zaman açılır. Uydurma isim/klasör **yasak**
(`index.md:55`). Band-2..7 için **klasör AÇILMAZ**, yalnız aralık kaydı tutulur (`index.md:39`).

## §9 ADR Grafiği — durum kenarları

> Kaynak: `.ai/.decisions/index.md` (okundu 2026-10-17 — §2 tablosu + disk ölçüm notu).

| Durum | Sayı | Graf anlamı | Kanıt |
|-------|------|-------------|-------|
| Frozen | **36** | ADR-001 → ADR-036 — düğüm **değiştirilemez**, kenar salt referans | `.decisions/index.md:38` |
| Active | **45** | accepted/ içindeki 44 + kökteki ADR-091 | `:39` |
| Rejected | **10** | rejected/ 10 dosya (R-011/R-012 dosyası YOK — işaretli) | `:40` |
| Draft | **2** | ADR-097 (K-Space V3) + ADR-102 (taksonomi) — `draft/` | `:41` |
| Toplam (satır) | 92 | 80 accepted + 10 rejected + 1 draft + 1 kök | `:42` |

**Bilinen sayım çelişkisi (uydurulmaz):** §2 tablosunda `total-draft: 2` (FM) ve "gerçek toplam
**93**" (disk ölçüm notu) ile `:42` satırındaki "Toplam 92 / 1 draft" **aynı dosyada birlikte
durur** — `Dosya: .ai/.decisions/index.md · Satır: 33, 41, 42`. Graf anayasası bu farkı **kapatmaz**;
okuyan taraf her iki sayıyı da tarih/kanıt künyesiyle taşır. ADR→dosya kenarları `[[...]]`
biçimindedir (`:54-80`).

## §10 keyword→dosya Kenarları (keys.md)

> Kaynak: `.ai/keys.md` (v28.3.6 — §2 Core · §4 Security · §5 Database · §6 Audio · §7 Hardware).

| Kenar sınıfı | Örnek keyword | Hedef düğüm | Kanıt |
|---------------|---------------|-------------|-------|
| Boot/vault çekirdeği | claude, talimat, anayasa | `CLAUDE.md` | `keys.md:54` |
| Güvenlik | CSRF, csrf_token, koruma | ADR-010 (accepted) | `keys.md:68` |
| Güvenlik | credential vault, secret | ADR-034 | `keys.md:73` |
| Veritabanı | coremusic_ai (18 DB) | `.sql/mysql/coremusic_ai.sql` | `keys.md:113` |
| Hardware | hardware roadmap, 3 faz | **DOĞRULAMA GEREKLİ** — hedef yok | `keys.md:177` |

**Kenar kuralı:** her satır `(keyword kümesi → hedef)` = **çoktan-bireye kenar**. Hedef yoksa
satır `**DOĞRULAMA GEREKLİ**` ile işaretlenir (keys.md §7'de **7 satır** bu durumda —
`keys.md:177-180, 184-185`). İşaretsiz phantom satır **yazım hatasıdır**; işaretli satır **dürüst
eksikliktir** ve grafiğe sahte kenar olarak girmez.

## §11 konu→dosya Kenarları (RAG.md §3.1)

| # | Konu sınıfı | Hedef | Keyword | Durum |
|---|-------------|-------|---------|-------|
| 1 | Boot sırası, kanonik okuma | `CLAUDE.md` §16 · `WORKFLOW.md` | boot, okuma, kanonik | IMPLEMENTED |
| 2 | Guardrail, yasak, anayasa | `CLAUDE.md` §7 · §21 · §23 | guardrail, yasak | IMPLEMENTED |
| 8 | Mimari kararlar (ADR) | `.decisions/index.md` · `brain.md` | adr, karar, frozen | IMPLEMENTED |
| 18 | AI stratejisi, RAG/embedding | ADR-030 | rag, embedding, llm | **PLANNED** |
| 21 | Ses motoru, mixer, EQ/DSP | `projects/NevaEngine/` | neva, mixer, juce | IMPLEMENTED |

Tablo **20 satır**dır ve satır **9 yoktur** (1-8, 10-21) — boşluk bilinçlidir, yeniden
numaralandırma **yasak** (`Dosya: .ai/RAG.md · Satır: 71`). Grafik okumada bu kenarlar `Durum`
etiketiyle taşınır: `PLANNED` kenar "kod yok, yalnız ADR" demektir (`RAG.md:104`).

## §12 Wiki-link Kenar Biçimi (tek geçerli sözdizimi)

| # | Kural | Örnek | Kanıt |
|---|-------|-------|-------|
| 1 | Biçim `[[hedef]]` — markdown path **değil** | `[[.decisions/accepted/ADR-010-…]]` | `RAG.md:138` · `keys.md:68` |
| 2 | Ayraç: `[[hedef#bölüm]]` veya `[[hedef\|görünüm]]` kabul edilir, çözümde `#`/`\|` kırpılır | `wiki-link-check.ps1:24` regex | `.ai/scripts/wiki-link-check.ps1:24` |
| 3 | Hedef 3 kademede çözülür: (1) dosya anahtarı (2) göreli yol (3) yalnız dosya adı | betik satır 28-37 | `wiki-link-check.ps1:28-37` |
| 4 | Şablon değişkeni/kaçış dizgesi link **değildir** (6 sahte kırık) | `[[C]]`, `[[V]]` | `AGENTS.md` §13.7 |
| 5 | Wiki-link kenarı = **dosya→dosya** kenarı; FM `evidence` kenarı = **iddia→satır** | ayrım §4 | bu anayasa §4 |

Şablon-kaynaklı `[[C]]`/`[[V]]`/`[[link]]` kenar sayılmaz; "onarmak" şablonu bozar — sayım
**6'da kalır** ve kusur sayılmaz (kök `AGENTS.md` §13.7, bağlayıcı).

## §13 Graph Doğrulama Kalıbı I — wiki-link-check.ps1 (repo betiği, gerçek)

```powershell
# Dosya: .ai/scripts/wiki-link-check.ps1 · Satır: 9-16 (indeks = düğüm kümesi)
$index=@{}
Get-ChildItem -Path 'C:\www\coremusic.net\.ai' -Recurse -Filter *.md -File | ForEach-Object {
  $base=$_.BaseName.ToLowerInvariant()
  $rel=$_.FullName.Substring(0,$_.FullName.Length-3)
  if(-not $index.ContainsKey($base)){ $index[$base]=$_.FullName }
  $index[$_.FullName.ToLowerInvariant()]=$_.FullName
  $index[$rel.ToLowerInvariant()]=$_.FullName
}
```

```powershell
# Dosya: .ai/scripts/wiki-link-check.ps1 · Satır: 24-42 (kenar çözümü + kırık sayımı)
foreach($m in [regex]::Matches($text,'\[\[([^\]|#]+)(?:[#|][^\]]*)?\]\]')){
  ...
  if(Test-Path $cand){ continue }        # 1. aday
  if(Test-Path ($cand+'.md')){ continue }# 2. aday
  if($index.ContainsKey($leaf...)){continue} # 3. aday
  $broken.Add("$(Split-Path $src -Leaf) -> [[$t]]")
}
"kontrol edilen link: $checked"
"kirik link: "+$broken.Count
```

**Yorum:** betik grafiğin **düğüm kümesini** (`.ai/**/*.md` → `$index`) ve **wiki-link
kenarlarını** (regex) ayrı ayrı kurar, sonra her kenarın hedefini 3 kademeli dener — §5'teki
"kırık kenar" tanımının birebir uygulamasıdır. Betikler yalnız **repo kökünden** çalıştırılır
(`Dosya: .ai/AGENTS.md · §13.6` — `.ai/` içinden çalıştırma 19 sahte "PNG yok" üretir).

## §14 Graph Doğrulama Kalıbı II — FM/parent-children PowerShell (türetilmiş)

> Aşağıdaki kalıp **bu anayasa için tasarlandı**; repo'da çalışan bir karşılığı **YOKTUR**
> (`⚠️ VERIFICATION REQUIRED`) — amaç: §3-§6 kurallarını tek komutta ölçülebilir kılmak.

```powershell
# TÜRETİLMİŞ KALIP — repo betiği DEĞİL — FM parent/children/depends_on tutarlılık iskeleti
$hedef='C:\www\coremusic.net\.ai\architecture\K015-AI\kg'
$ids=@{}; $sorun=New-Object System.Collections.Generic.List[string]
Get-ChildItem $hedef -Recurse -Filter *.md | ForEach-Object {
  $t=[System.IO.File]::ReadAllText($_.FullName,[Text.Encoding]::UTF8)
  $nid=[regex]::Match($t,'(?m)^node_id:\s*(.+)$').Groups[1].Value.Trim()
  $par=[regex]::Match($t,'(?m)^parent_id:\s*(.+)$').Groups[1].Value.Trim()
  if($nid){ if($ids.ContainsKey($nid)){ $sorun.Add("DUPLICATE node_id: $nid") } else { $ids[$nid]=$_.FullName } }
  if($par -and $par -ne 'null' -and -not $ids.ContainsKey($par)){
    $sorun.Add("YETIM/KIRIK parent: $($_.Name) -> $par") }
}
"sorun: "+$sorun.Count; $sorun | Select-Object -First 40
```

**Kapı:** `sorun: 0` beklenir; `DUPLICATE` §6/4 (ID CONFLICT), `YETIM/KIRIK` §6/2 (yetim kenar).
Ölçüm kapısı (dosya uzunluğu) aynı oturumda yapılır:

```powershell
[IO.File]::ReadAllLines('C:\www\coremusic.net\.ai\architecture\K015-AI\kg\kg-anayasa.md',
  [Text.Encoding]::UTF8).Count   # >=500
```

## §15 PHP Kanıtı — KnowledgeBaseInterface (graf sözleşmesi)

```php
// Dosya: shared/src/Contracts/AI/KnowledgeBaseInterface.php · Satır: 13, 23-58
interface KnowledgeBaseInterface
{
    public function search(string $query, int $maxResults = 10, array $filters = []): array;
    public function ragQuery(string $query, array $context = []): array;
    public function addKnowledge(string $title, string $content, string $category, array $metadata = []): string;
    public function removeKnowledge(string $id): bool;
    public function cleanupLifecycle(): array;
}
```

**İlişki:** bu arayüz repo'daki tek "bilgi düğümü CRUD" sözleşmesidir — `addKnowledge` = düğüm
ekleme, `removeKnowledge` = düğüm silme, `cleanupLifecycle` = **yaşam döngüsü** temizliği,
`search`/`ragQuery` = düğüm **sorgulama**. Uygulama: `shared/src/AI/KnowledgeBase.php:23`
(`class KnowledgeBase implements KnowledgeBaseInterface`). Kategori kümesi sabittir:
`['adr','architecture','security','audio','hardware','workflow','template']`
(`KnowledgeBase.php:25`) — bu küme grafikte **düğüm etiketi (label)** karşılığıdır.

## §16 PHP Kanıtı — lifecycle + varlık denetimi (graf üzerindeki davranış)

```php
// Dosya: shared/src/AI/KnowledgeBase.php · Satır: 96-116 (düğüm ekleme + etiket denetimi)
public function addKnowledge(string $title, string $content, string $category, array $metadata = []): string
{
    if (!in_array($category, self::CATEGORIES, true)) {
        throw new \InvalidArgumentException("Invalid category: {$category}. ...");
    }
    $id = 'kb_' . $this->nextId++;
    ...
}

// Dosya: shared/src/AI/KnowledgeBase.php · Satır: 121-129, 134-156 (silme + yaşam döngüsü)
public function removeKnowledge(string $id): bool
{
    if (!isset($this->knowledge[$id])) { return false; }   // phantom id -> false, EXCEPTION değil
    unset($this->knowledge[$id]); return true;
}
public function cleanupLifecycle(): array
{
    $cutoffDate = date('Y-m-d', strtotime('-' . self::LIFECYCLE_MAX_AGE_DAYS . ' days'));
    foreach ($this->knowledge as $id => $item) {
        if ($item['created_at'] < $cutoffDate) { unset($this->knowledge[$id]); $removed++; }
        ...
    }
}
```

**Graf dersi:** (1) geçersiz etiket **exception** atar → uydurma düğüm etiketi üretemezsiniz;
(2) var olmayan id silinemez → `false` (phantom id sessizce yok sayılmaz, **yanıtlanır**);
(3) `LIFECYCLE_MAX_AGE_DAYS = 365` (`:27`) — düğümler yaşar, **arşivlenir/silinir**, sonsuz
büyüme yok. Bu üç davranış bu anayasanın §5-§6 bütünlük kurallarının koddaki karşılığıdır.

## §17 Güvenlik — secret graph'a girmez

| # | Kural | Uygulama | Kanıt |
|---|-------|----------|-------|
| 1 | `FIGMA_TOKEN`/`FIGMA_FILE_KEY` yalnız `.ai/.env.figma`'dan okunur; `.gitignore:69` | anahtar `.md`'ye yazılmaz | kök `AGENTS.md` §16.3 (değiştirilemez kural) |
| 2 | Credential/secret keyword'leri ADR'ye **kenar**tır, değer **değil** | `keys.md:73` → ADR-034 | `keys.md:73` |
| 3 | Secret graph'a **düğüm/kenar olarak girmez** — yalnız "hangi ADR" kenarı girer | REDACTED | `.ai/RAG.md:137` (rag-anayasa §15/5 aktarımı) |
| 4 | `.env` dosyası domain sınırı: Security Engineer | grafiğe kopyalanmaz | `.ai/AGENTS.md` §5 |

Güvenlik keyword kenarları (`keys.md:68-78`) hedefi **ADR**'dir; ADR metninin kendisi frozen'dır
ve secret **içermez**. Vault'taki secret sızıntısı senaryosu `[REDACTED]` maskelemesiyle
işlenir (`.ai/AGENTS.md` §17/3). Grafiğin hiçbir kenarı secret **değerini** taşıyamaz —
taşınırsa bu anayasa ihlali, vault-sync sırasında tespit edilip geri alınır.

## §18 Zero-Hallucination — var olmayan düğüm uydurulmaz

| # | Durum | Etiket | Kanıt |
|---|-------|--------|-------|
| 1 | Hedef diskte değil ama iddia edilmiş | `⚠️ VERIFICATION REQUIRED` | kök `AGENTS.md` §3 |
| 2 | Ölçülmemiş sayı (düğüm/kenar sayısı) | `UNKNOWN` | kök `AGENTS.md` §3 |
| 3 | `.ai/wiki/` — boot'ta anılır, diskte **0 dosya** |幻 düğüm: sayfa **uydurulmaz** | `RAG.md:96` (rag-anayasa §15/1 aktarımı) |
| 4 | `.ai/.rules/error-recovery.md` — diskte YOK | link **lenmez**, yok sayılır | `RAG.md:96` |
| 5 | Rezerve band slotu (Band-2..7) | gerçek düğüm **değildir** — isim verilmez | `index.md:55` |

**İhlal tanımı:** grafiğe diskte bulunmayan bir düğüm/kenar yazmak, ya da sayı iddiasını
ölçümden yazmamak = Zero-Hallucination ihlali → `VERIFICATION REQUIRED` etiketi zorunlu,
`UNKNOWN` her zaman meşru bir cevaptır. Wiki-link bile olsa hedef Test-Path'ten geçmeyen
kenar **grafikte kalır ama "kırık" işaretli gider** — sessizce uydurulan hedef asla.

## §19 Durum Etiketi Disiplini (düğüm düzeyi)

| # | Durum | Koşul | Graf anlamı | Kanıt |
|---|-------|-------|-------------|-------|
| 1 | `active` | içerik + kanıt tam | düğüm bağlayıcı | `kg-anayasa` bu FM |
| 2 | `draft` | künye var, kanıt toplanmadı | düğüm **geçici** — kenarı bağlayıcı değil | `kg/index.md:4, 10-12` |
| 3 | `verify-required` | içerik boş/çelişkili | düğüm **işaretli eksik** (K020-comere) | `index.md:96` |
| 4 | `IMPLEMENTED`/`PLANNED` | kenar tarafı (konu→dosya) | RAG §3 durum sütunu | `RAG.md:104` |

**Zincir:** `kg/index.md` frontmatter'ında `evidence: "# bu turda disk kanıtı TOPLANMADI — kanıt
bekliyor"` ve `validation: "okuma/gözlem YOK — verify-required"` yazar (`kg/index.md:10-12`) —
bu anayasa bu eksikliği **kapatmaz, üzerine özet kural kurar**; `kg/`'nin `draft` durumu
değiştirilmez (durum kanıtla, iddiayla **yükseltilmez**).

## §20 K015 / K006 Sınırı (kapsam ayrımı)

| # | Konu | Sahip | Kanıt |
|---|------|-------|-------|
| 1 | KG = **ajan altyapısı** grafiği — K015-AI altında | K015-AI/kg | `kg/index.md:27` |
| 2 | K006-yapay-zeka (ML ürün) kapsamı **DEĞİLDİR** | K006 | `kg/index.md:27` |
| 3 | Öneri motoru/LLM ürün specleri ayrı domain | K006 / ADR-030 | `ADR-030:62` (rag-anayasa §11 aktarımı) |
| 4 | Kademe-3 (Bounded Context/Module) AÇILMADI; children boş | — | `kg/index.md:6, 28` |
| 5 | Model eğitimi (fine-tune) bu anayasanın dışında | ayrı ADR | `ADR-030:219` (rag-anayasa §11) |

Sınır kayması (K006↔K015) bilinen ve **ayrı domain** olarak tescilli bir ayrım­dır
(`index.md:99`): ikisi de meşru, ikisi de düğüm — çakışma değil **kapsam ayrımı**.

## §21 kg/ Klasörünün Bugünkü Durumu (disk okuması)

| # | Dosya | Rol | Durum | Kanıt |
|---|-------|-----|-------|-------|
| 1 | `kg/index.md` (48 satır) | klasör künyesi + çocuk kataloğu | draft | `kg/index.md:1-16` |
| 2 | `kg-anayasa.md` (bu dosya) | düğüm/kenar kural seti | active | bu FM |
| 3 | `kg-index` (bu dosyanın `depends_on` hedefi) | graf indeks düğümleri | **⚠️ VERIFICATION REQUIRED** — `kg/` altında `kg-index.md` **YOK** | `kg-anayasa` FM + glob 2026-10-17 |
| 4 | children (kademe-3) | — | bilerek boş (ADR-097 K4 sonraki faz) | `kg/index.md:6` |

**Açık beyan:** bu dosyanın `depends_on: [kg-index]` kenarı hedefini bulamamıştır — `kg/`
altında yalnız `index.md` + `kg-anayasa.md` vardır. Kenar **fiziksel olarak phantom'dur** ve bu
satırla **açıkça işaretlenmiştir**; `kg-index` düğümü üretilene (ya da kenar düzeltilene kadar)
durum `VERIFICATION REQUIRED` kalır. Uydurma `kg-index.md` **üretilmez** (§18).

## §22 Doğrulama Kapıları (bu dosya + graph)

| # | Kapı | Kriter | Yöntem |
|---|------|--------|--------|
| 1 | Uzunluk | ≥500 tam satır | `[IO.File]::ReadAllLines(..., UTF8).Count` |
| 2 | Frontmatter | 10 alan, sıra sabit, `title`/`parent` anahtarı yok | gözden geçirme |
| 3 | Authority | son satır birebir | gözden geçirme |
| 4 | Wiki-link hedefleri | 3 kademeli çözüm; bilinen 6 sahte sayılmaz | `wiki-link-check.ps1` (kökten) |
| 5 | FM parent/children | DUPLICATE + YETIM = 0 | türetilmiş kalıp (§14) |
| 6 | depends_on DAG | döngü yok | toposort / `nx.simple_cycles` (ağ) |
| 7 | Sayı iddiaları | her sayı ölçüm tarihi + künye taşır | yeniden sayım |
| 8 | Secret taraması | graph satırında token/anahtar yok | grep + `.gitignore` |

**Betikler yalnız repo kökünden** çalıştırılır (`.ai/` içinden çalıştırma sahte sonuç üretir —
`.ai/AGENTS.md` §13.6). Kapı 5/6 için repo betiği **yoktur**; kalıplar türetilmiştir ve
`⚠️ VERIFICATION REQUIRED` işaretli kalır (§14).

## §23 İllüstratif Örnek — Python (DAG + döngü taraması)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# networkx ile depends_on alt grafiği DAG denetimi — upstream docs referansı
import networkx as nx

G = nx.DiGraph()
G.add_edge("kg-anayasa", "kg-index")     # depends_on kenarı
G.add_edge("kg-index", "kg")             # parent kenarı (örnek)

print(nx.is_directed_acyclic_graph(G))   # depends_on döngü yok mu? -> True/False
for cyc in nx.simple_cycles(G):          # "simple cycle: kapalı yol, düğüm iki kez görünmez"
    print("DÖNGÜ:", cyc)                 # boş liste = temiz graf
```

`simple_cycles` tanımı NetworkX resmi dokümanı: "A simple cycle, or elementary circuit, is a
closed path where no node appears twice" (§30/3). Repo'da networkx **kurulu değildir** —
bu kod vault graph'ının **tasarım referansıdır**, çalışan parçası **değildir**.

## §24 İllüstratif Örnek — TypeScript (adjacency list + cycle detect)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```ts
// FM tabanlı graf iskeleti — upstream/docs referansı; repo'da karşılığı YOK
type NodeId = string;
type Edge = { from: NodeId; to: NodeId; kind: "parent" | "depends_on" | "evidence" | "wiki" };

export function hasCycle(adj: Map<NodeId, NodeId[]>): boolean {
  const state = new Map<NodeId, 0 | 1 | 2>();          // 0=beyaz 1=gris 2=siyah
  const dfs = (u: NodeId): boolean => {
    state.set(u, 1);
    for (const v of adj.get(u) ?? []) {
      if ((state.get(v) ?? 0) === 1) return true;      // gris düğüme kenar = DÖNGÜ
      if ((state.get(v) ?? 0) === 0 && dfs(v)) return true;
    }
    state.set(u, 2); return false;
  };
  return [...adj.keys()].some(u => (state.get(u) ?? 0) === 0 && dfs(u));
}
```

Kullanım: `parent` ve `depends_on` kenarları **aynı** DFS'e girer; `evidence`/`wiki` kenarları
**döngü kapısına girmez** (kanıt kenarı grafiği döngüye sokmaz — §6 ayrımı).

## §25 İllüstratif Örnek — Go (triple store iskeleti)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
// subject-predicate-object triple — upstream docs (W3C RDF modeli) referansı; repo'da YOK
package graph

type Triple struct{ S, P, O string } // S=düğüm, P=kenar etiketi, O=hedef

func Validate(t []Triple, nodes map[string]bool) (broken []Triple) {
    for _, tr := range t {
        if !nodes[tr.S] || !nodes[tr.O] { // kenar iki ucu da düğüm olmalı
            broken = append(broken, tr)   // kırık/phantom kenar
        }
    }
    return
}
```

W3C RDF modeli: "RDF graphs are sets of subject-predicate-object triples" (§30/1) — bu
anayasanın `node_id`/`depends_on`/`evidence` şemasının literatür karşılığı üçlüdür.

## §26 İllüstratif Örnek — Rust (FM parse + kenar çıkarımı)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```rust
// frontmatter'dan kenar okuma iskeleti — upstream/docs referansı, repo kanıtı DEĞİL
pub fn parse_edges(fm: &str) -> Vec<(String, String, &'static str)> {
    let mut out = Vec::new();
    for line in fm.lines() {
        if let Some(rest) = line.strip_prefix("parent_id:") {
            out.push((String::new(), rest.trim().to_string(), "parent"));
        } else if let Some(rest) = line.strip_prefix("depends_on:") {
            for id in rest.trim_start_matches('[').trim_end_matches(']')
                .split(',').map(str::trim).filter(|s| !s.is_empty()) {
                out.push((String::new(), id.to_string(), "depends_on"));
            }
        }
    }
    out
}
```

Not: `depends_on: [kg-index]` tek öğeli liste; `children: []` boş liste = kademe-3 kapalı
(`kg/index.md:6`). Parse edilen her hedef §5'teki Test-Path kapısından geçmelidir.

## §27 İllüstratif Örnek — C++ (komşuluk listesi + topolojik sıralama)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```cpp
// depends_on DAG topolojik sırası — upstream/docs referansı (repo'da karşılığı YOK)
#include <vector>
#include <string>
#include <unordered_map>

std::vector<std::string> topo(const std::unordered_map<std::string, std::vector<std::string>>& adj) {
    std::vector<std::string> order;
    // Kahn algoritması: derece-0 düğüm → sıraya al; döngü varsa order < düğüm sayısı
    ...
    return order;
}
// Topolojik sıra, "önce bağımlılık" okuma yönünü verir: kg-index → kg-anayasa.
```

Döngü tespiti bu anayasanın bağlayıcı kapısıdır (§6/1 · §22/6); C++ örneği yalnız
algoritma **şekli**ni gösterir — CoreMusic'te karşılığı **yoktur**.

## §28 İllüstratif Örnek — C# (düğüm modeli + bütünlük raporu)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```csharp
// GraphNode + integrity raporu — upstream/docs referansı; repo'da karşılığı YOK
public sealed record GraphNode(string NodeId, string NodeType, string Status,
    string? ParentId, IReadOnlyList<string> Children, IReadOnlyList<string> DependsOn);

public static IReadOnlyList<string> FindOrphans(IEnumerable<GraphNode> nodes)
{
    var all = nodes.ToDictionary(n => n.NodeId);
    return nodes.Where(n => n.ParentId is { } p
        && (!all.TryGetValue(p, out var parent) || !parent.Children.Contains(n.NodeId)))
        .Select(n => n.NodeId).ToList();   // yetim düğüm listesi (§6/2)
}
```

FM 10 alanın tip güvenli yansıması budur: `ParentId` tek üst kenarı, `Children`/`DependsOn`
kenar koleksiyonları, `Status` düğüm durumudur (§3).

## §29 Açık Sorular

| # | Soru | Öncelik | Durum / Kanıt |
|---|------|---------|----------------|
| 1 | `kg-index` düğümü ne zaman üretilir? | HIGH | `⚠️ VERIFICATION REQUIRED` — hedef diskte yok (bu dosya §21/3) |
| 2 | `kg/index.md` disk kanıtı/`draft` durumu ne zaman kapanır? | HIGH | `kg/index.md:10-14` — kanıt bekliyor |
| 3 | ADR indeksi toplamı 92 mi 93 mü? | MEDIUM | `:33` (93) vs `:42` (92) — `.decisions/index.md` kendi içinde çelişkili |
| 4 | Band-2 için ADR yazılacak mı (K021+)? | MEDIUM | ADR-097 draft — onay bekliyor (`index.md:47`) |
| 5 | Graph doğrulama betiği (§14 kalıbı) repo'ya taşınır mı? | MEDIUM | karar üst makamda — şu an türetilmiş kalıp |
| 6 | Otomatik graph indeksleyici (kod 0) ne zaman? | LOW | ADR-030 PLANNED — ayrı karar adımı (`ADR-030:272`) |

## §30 Kaynaklar

| # | Kaynak | Yol / URL | Kullanım |
|---|--------|-----------|----------|
| 1 | K-düğümleri master graph | `.ai/architecture/index.md` (§4-§6 — okundu 2026-10-17) | §7-§8, §20 |
| 2 | ADR grafiği | `.ai/.decisions/index.md` (§2-§3 — okundu 2026-10-17) | §9 |
| 3 | konu→dosya kenarları | `.ai/RAG.md` §3 (20 satır) | §11-§12 |
| 4 | keyword→dosya kenarları | `.ai/keys.md` (§2-§7) | §10 |
| 5 | Düğüm CRUD + lifecycle kodu | `shared/src/AI/KnowledgeBase.php` (Satır 23-156) · `shared/src/Contracts/AI/KnowledgeBaseInterface.php` (Satır 13-58) | §15-§16 |
| 6 | Graph doğrulama betiği | `.ai/scripts/wiki-link-check.ps1` (43 satır) | §13 |
| 7 | Alt-düğüm künyesi | `.ai/architecture/K015-AI/kg/index.md` (48 satır) | §1, §19, §21 |
| 8 | Anayasa iskeleti (şablon) | `.ai/architecture/K015-AI/rag/rag-anayasa.md` (516 satır) | yapı referansı |
| 9 | **Web:** W3C RDF 1.1 Concepts | `https://www.w3.org/TR/rdf11-concepts/` (erişim 2026-10-17) | §6, §25 (triple modeli) |
| 10 | **Web:** Neo4j graph data modeling | `https://neo4j.com/docs/getting-started/data-modeling/` (erişim 2026-10-17) | §3-§4 (node/relationship/property) |
| 11 | **Web:** NetworkX Cycles | `https://networkx.org/documentation/stable/reference/algorithms/cycles.html` (erişim 2026-10-17) | §6, §23 (döngü tanımı) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode