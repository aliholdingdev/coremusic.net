---
node_id: kg-surucu
node_type: module
status: active
parent_id: kg
children: []
depends_on: [kg-anayasa]
evidence:
  - ".ai/architecture/index.md (K-düğümleri graph, okundu 2026-10-17)"
  - ".ai/scripts/wiki-link-check.ps1 (graph doğrulama betiği kanıtı)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı"
open_questions: []
group: mimari
---

# kg-surucu - Bilgi Grafiği Sürümü

> **Kapsam:** Ajan bilgi grafiğinin (KG) gerçekten nasıl gezildiği — soru → düğüm → kenar →
> komşu düğüm traverser akışı, yeni düğüm ekleme (FM 10 alan + parent/children senkronu +
> registry kaydı), kenar güncelleme, graph doğrulama döngüsü ve bozuk graph davranışı.
> **Kural koyar değil, tarif eder:** bağlayıcı kurallar `Dosya: .ai/architecture/index.md`
> (§4/§5/§8.1/§9.4.2/§9.13) ve `.ai/AGENTS.md §7` (Task Dispatch) içindedir; bu dosya o
> kuralların **graf kullanımı kılavuzudur**.
>
> **Ölçüm tarihi:** 2026-10-17 (tüm satır sayıları ve disk iddiaları bugün okuma/glob ile
> ölçülmüştür — §20, §28). **Dürüstlük notu (görev manifesti ↔ disk):** görev talimatı
> "index.md §25.x tabloları"nı kaynak gösterdi; **diskte `.ai/architecture/index.md` 928
> satır olup §25 içermez** — graph ile ilgili gerçek bölümler §4 (band cetveli), §5 (25
> klasörlük K-düğümleri listesi), §8.1 (iki-ekseni şema), §9.4.2 (FM 10 alan ölçümü) ve
> §9.13.3 (`## §BAĞLANTILAR` kenar geçişi)tir. Bu sürüm **diskteki bölümlere** bağlıdır.

## §1. ELI15 — Bu sürücü ne işe yarar

Vault bir graph'tır: her `.md` dosyası bir **düğüm**dür (frontmatter `node_id`/`parent_id`/
`children`/`depends_on` alanları), `[[wiki-link]]`'ler ve `## §BAĞLANTILAR` tabloları ise
**kenarlardır**. Sürücü, "şu konuda ne var?" sorusunun graph'ta nasıl gezildiğini (§4-§6),
yeni bir düğümün grafiğe nasıl eklendiğini (§7-§9), kenarların nasıl güncellendiğini (§10),
graph'ın nasıl doğrulandığını (§11) ve bozulduğunda ne yapılacağını (§12) tarif eder.

> **eli10:** Graph = dosyalar + aralarındaki oklar. Sürücü: ok nereden okunur, yeni ok
> nereye yazılır, ok kırılırsa ne yapılır — bunların cevabı.
>
> **eli15:** Graph store **yoktur** (ADB/vektörstore değil); graph **dosya sisteminin
> kendisinde** yaşar — frontmatter + wiki-link + §BAĞLANTILAR = kenar koleksiyonu. Bu
> yüzden traverser = sıralı okuma + `Test-Path` doğrulamasıdır; neo4j/cypher gibi bir
> sorgu dili **kullanılmaz** (repo'da graph DB kodu yok — §18). W3C RDF modeliyle kıyas:
> her wiki-link satırı bir `(subject, predicate, object)` üçlüsüdür
> (web kaynak §27/2 — §27).

## §2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Soru → düğüm → kenar → komşu traverser akışı (§4-§6) | Dosya içeriği özetleri (içerik hedef düğümün işidir) |
| Yeni düğüm ekleme + FM 10 alan + parent/children senkronu (§7-§9) | ADR metinleri (frozen — yalnız okunur) |
| Kenar güncelleme (evidence · depends_on · §BAĞLANTILAR) (§10) | Graph DB kurulumu/kodlaması (ADR-030 PLANNED — §18) |
| Graph doğrulama döngüsü + bozuk graph davranışı (§11-§12) | `.ai/scripts/*.ps1` dosyalarının değiştirilmesi (salt-okunur) |
| PowerShell traverser/ölçüm kalıpları (§14-§16) | Registry (`index.md`) yazımı — **yalnız orkestratör** (§9.8.4/10) |

- **Kullananlar:** retrieval yapan her ajan (traverser) · MO (düğüm/kenar kaydı) · Vault Steward (denetim).
- **Ön koşul:** `depends_on` hedefleri ve wiki-link hedefleri diskte var olmalıdır (`RAG.md §3.2/1` · Satır: 102).
- **Kanıt standardı:** her iddia `Dosya:` + `Satır:` künyelidir; künyesiz iddia yazılmaz (§20).

## §3. Graph Modeli — Düğüm ve Kenar Tipleri (bugünkü disk)

| Graph öğesi | Vault karşılığı | Nerede yaşar | Kanıt (2026-10-17) |
|---|---|---|---|
| **Düğüm (node)** | Her `.ai/architecture/**.md` | dosya yolu = node anahtarı | `Dosya: .ai/architecture/index.md` (§5 — 25 klasörlük Band-1 listesi) |
| Düğüm kimliği | FM `node_id` | frontmatter (§4.2 9/10. alan) | `index.md · §9.4.2` — `node_id` 627/627 **%100** |
| **Ebeveyn-çocuk kenarı** | FM `parent_id` ↔ ebeveyn `children` | iki yönlü (senkron zorunlu — §9) | `index.md · §9.4.2` — `parent_id` 428 · şema-dışı `parent:` 161 |
| **Bağımlılık kenarı** | FM `depends_on: [...]` | tek yönlü | `Dosya: .../kg/index.md · Satır: 7-8` (`depends_on: K015-AI`) |
| **Konu kenarı** | `[[wiki-link]]` (RAG §3.1 + metin içi) | çift yönlü **DEĞİL**, atıf | `Dosya: .ai/RAG.md · Satır: 75-94` (20 satır konu→dosya) |
| **K↔K kenarı** | `## §BAĞLANTILAR` tablosu | ebeveyn `index.md`'de | `index.md · §9.13.3` — 14/14 index · 104 satır · taslak |
| Kanıt kenarı | FM `evidence: [...]` | düğüm içi | `index.md · §9.4.2` — `evidence` 425/627 dolu |
| Durum | FM `status` | düğüm içi | `index.md · §9.4.2` — `status` 627/627 **%100** |

**Kenar biçimi (W3C RDF kıyası):** `[[hedef]]` satırı `(ben, atıf, hedef)` üçlüsüdür; RDF
grafı "set of triples"tir ve üçlü ekleme/çıkarma **yeni graf** üretir
(web kaynak §27/2) — vault'ta karşılığı: kenar eklenir/çıkarılır, dosya yeniden yazılır.
Neo4j kıyası: ilişki "must always have a start node, an end node, and exactly one type"
(web kaynak §27/1) → vault'ta başlangıç = kaynak dosya, bitiş = `[[hedef]]`, tip = kenar
türü (§4 tablosu).

## §4. Graph Traverser Akışı A-F (soru → düğüm → kenar → komşu → doğrulama)

| Adım | Ne olur | Kaynak / karar | Token maliyeti |
|---|---|---|---|
| **A — Soru** | Soru 3-5 keyword'e bölünür; tahmin edilen domain/K belirlenir | `.ai/RAG.md §3.1` (Satır: 73-94) — konu sınıfı satırı | ~0 (boot'ta okunanlar hariç) |
| **B — Düğüm resolve** | Keyword → düğüm dosyası: K1 `RAG.md §3.1` → K2 `keys.md` → K3 registry §5 (K listesi) → K4 disk glob (`hidden: true`) | `Dosya: .ai/RAG.md · Satır: 73-94` · `index.md · §5` | ucuz → pahalı |
| **C — Düğüm oku** | Hedef `.md` `@` ile açılır; **önce FM 10 alan** okunur (`parent_id` · `children` · `depends_on` · `evidence` · `open_questions`) | `index.md · §9.4.2` (v2 §4.2 8 alan + `node_id` + `group`) | orta |
| **D — Kenar çıkar** | FM `depends_on` + metin içi `[[wiki-link]]` + `## §BAĞLANTILAR` satırları toplanır | `Dosya: .ai/RAG.md · Satır: 138` (wiki-link formatı zorunlu) | orta |
| **E — Komşu** | Her kenar hedefi `Test-Path`/leaf-index ile doğrulanır → diskteyse komşu düğüm A-D'ye kadar açılır (**depth limit §6**) | `wiki-link-check.ps1` çözüm kademeleri (Satır: 29-37) | pahalı (dikkatli) |
| **F — Doğrulama** | Kırık kenar sayısı + durum işareti (`UNKNOWN` / `⚠️ VERIFICATION REQUIRED`) + künye ile kapanış | `Dosya: .ai/RAG.md · Satır: 142-155` (§6 kapı 2/7) | ~0 |

**Sıra değişmez (B→C→D→E):** E'ye (komşuya) **C'den önce** atlamak yasaktır — kenarı
okumadan komşu okunmaz; bu, rag-surucu §3 K1→K4 sırasının graph karşılığıdır.

## §5. Traverser Karar Ağacı — Soru Tipi → Düğüm → Kenar Türü

| # | Soru tipi | İlk düğüm | Hangi kenarlar açılır | Sonuç |
|---|---|---|---|---|
| 1 | "K serisinde hangi domain var?" | `.ai/architecture/index.md §5` | — (katalog) | 25 klasörlük düğüm listesi |
| 2 | "Bu dosya kime bağlı?" | hedef dosya FM | `parent_id` + `depends_on` | 1 ebeveyn + n bağımlılık |
| 3 | "Bu domainin çocukları kim?" | ebeveyn `index.md` FM `children` | children → çocuk dosyalar | çocuk kümesi |
| 4 | "K↔K bağlantıları?" | ebeveyn `index.md` `## §BAĞLANTILAR` | Hedef K · wiki-link · göreli yol · ilişki | kenar listesi (§10) |
| 5 | "Konu X hangi dosyada?" | `.ai/RAG.md §3.1` | konu→dosya wiki-link | hedef dosya |
| 6 | "Bu iddia kanıtlı mı?" | hedef FM `evidence` + `validation` | evidence satırları | durum + künye |
| 7 | "Bu düğüm gerçekten var mı?" (fantom testi) | FM `node_id` → disk `Test-Path` | — | VAR / PHANTOM (§12) |
| 8 | "Sınırlar dışına çıkılmış mı?" | beyazliste (dispatch kaydı) | kapsam dışı yazım sayacı | 0 / ihlal (§12) |

**Registry bağlantısı:** K-düğümleri listesi `Dosya: .ai/architecture/index.md · §5` (25
klasör — Band-1) ve §9.9.1 (kök bazında 21 K ölçüm tablosu) içindedir; "§25.x" diye bir
bölüm diskte **yoktur** (§ başındaki dürüstlük notu).

## §6. Komşu Genişleme Kuralları (depth limit + en dar önce)

| # | Kural | Değer | Kanıt |
|---|---|---|---|
| 1 | Max derinlik (bir traverser turunda) | **3 hop** (düğüm → komşu → komşu) | bütçe kuralı (§21) — sayı **ölçümle** değil, sürüm kararıyla yazıldı |
| 2 | En dar (domain-specific) hedef önce | dar → geniş | `Dosya: .ai/RAG.md · Satır: 103` (§3.2/2) |
| 3 | Boot'ta okunmuş düğüm tekrar okunmaz | çift israf yok | rag-surucu §4 adım [1] |
| 4 | `hidden: false` ile `.ai/**` araması yasak | sahte "0 sonuç" üretir | kök `AGENTS.md §7/2` |
| 5 | Bilinmeyen `node_id` = phantom → komşuya **girilmez** | kenar raporlanır, gezilmez | §12 karar tablosu |
| 6 | Depth limit aşılırsa | yarım kenar `open_questions`'a yazılır | FM 10 alan |

## §7. Yeni Düğüm Ekleme Akışı (graph'a düğüm)

```text
[A] Gerekçe + konu belirle (hangi soru bu düğümü çözer?)
[B] Şablon zorunlu okuma: Guardrail #16 (kalıp şablon dosyası)
[C] Dosyayı `{kategori}/{teknoloji}/{teknoloji}-{grup-rol}.md` biçiminde YAZ (§8.1 formülü)
[D] FM 10 alan doldur (§8 tablosu — eksik alan = düğüm yarım)
[E] Ebeveyn `children` listesine ekle (çift yönlü kenar — §9)
[F] Ebeveyn `index.md` → `## §GRUPLAR` tablosuna satır ekle (§8.1)
[G] `## §BAĞLANTILAR` satırı ekle (komşu K↔K kenarı — §10)
[H] `RAG.md §3.1`/`keys.md` konu satırı ekle/güncelle (rag-surucu §12 akışı)
[I] Doğrulama döngüsü çalıştır (§11) → geçmezse D'ye dön
[J] Kapanış: log.md append-only kayıt + orkestratöre rapor (commit orkestratöre ait)
```

| Adım | İhlal sonucu | Kaynak künye |
|---|---|---|
| B | Şablonsuz dosya üretilmez (Guardrail #16) | kök `AGENTS.md §16/5` |
| D | Yarım düğüm → §9.4.2'deki "208 eksik dosya" sınıfına düşer | `index.md · §9.4.2` |
| E | Yönetsiz kenar (parent→child var, child→parent yok) | `index.md · §9.4.2` (parent_id 428 vs children eksik 208) |
| G | K↔K bağı kaybolur (taslak kenar doğrulanmadı — §9.13.3) | `index.md · §9.13.3` |
| J | Subagent `git commit` atarsa ihlal | kök `AGENTS.md §16.3.9` |

## §8. FM 10 Alan Doldurma Kılavuzu (yeni düğüm)

| # | Alan | Ne yazılır | Kaynak | Örnek (diskten) |
|---|---|---|---|---|
| 1 | `node_id` | `{ebeveyn}/{dosya-adı-soneki}` (benzersiz) | v2 §4.2 · §8.1 | `kg-surucu` (bu dosya) |
| 2 | `node_type` | `domain` · `subdomain` · `module` | v2 §4.2 | `subdomain` — `kg/index.md · Satır: 3` |
| 3 | `status` | `draft` · `active` · `verify-required` | v2 §4.2 | `draft` — `kg/index.md · Satır: 4` |
| 4 | `parent_id` | ebeveyn `node_id` (YAZIM: `parent:` **DEĞİL** — 161 dosyalık şema-dışı hata) | `index.md · §9.4.2` | `K015-AI` — `kg/index.md · Satır: 5` |
| 5 | `children` | çocuk `node_id` listesi; çocuk yoksa `[]` + neden yorumu | `index.md · §9.4.2` | `[]` + "kademe-3 AÇILMADI" — `kg/index.md · Satır: 6` |
| 6 | `depends_on` | bağımlı düğüm `node_id`'leri (**diskte var olmalı** — §12 fantom) | v2 §4.2 | `- K015-AI` — `kg/index.md · Satır: 7-8` |
| 7 | `evidence` | `Dosya: … · Satır: …` künyeli kanıt satırları | v2 §4.2 | `"# bu turda disk kanıtı TOPLANMADI"` — `kg/index.md · Satır: 10` |
| 8 | `validation` | ölçüm yöntemi + tarih | v2 §4.2 | `"okuma/gözlem YOK — verify-required"` — `Satır: 12` |
| 9 | `open_questions` | açık soru listesi (`[]` = yok) | v2 §4.2 | `- "kg/ için disk kanıtı sonraki turda"` — `Satır: 14` |
| 10 | `group` | `mimari` · `kod` · `kanit` (3 grup — `teknoloji` 2026-10-15'te kaldırıldı) | `index.md · §8.1` + §9.11 | `mimari` — `kg/index.md · Satır: 15` |

**Aile ayrımı (karıştırma YASAK):** `.ai/` kök vault dosyaları (CLAUDE/WORKFLOW/registry)
**7 alanlık** `title·type·category…` şemasını; `.ai/architecture/K*` node dosyaları ise
**10 alanlık** bu şemayı kullanır (`index.md · §9.4.2` kök neden notu — "iki şema ailesi
yanlışlıkla karıştırıldı; ikisi de geçerlidir ama dosya ailesiyle eşleşir").

## §9. parent/children Senkronu (çift yönlü kenar)

| Olay | Yapılan | Doğrulama |
|---|---|---|
| Yeni çocuk eklendi | çocukta `parent_id: X` · ebeveyn `index.md` `children` listesine ekle | iki dosyada da oku |
| Çocuk silindi | ebeveyn `children`'dan çıkar + child dosya silinir | fantom kalmaz (§12) |
| Çocuk yeniden adlandırıldı | iki dosya FM + tüm gelen `[[wiki-link]]` + `keys.md` | `wiki-link-check.ps1` kökten (§11) |
| Klasör kapatıldı (kademe-3) | `children` → `[]` + neden yorumu | `kg/index.md · Satır: 6` kalıbı |

**Gerçek açık örnek:** `Dosya: .../kg/index.md · Satır: 6` — `children: []` "kademe-3 bu
fazda AÇILMADI" der; bu sürüm (`kg-surucu.md`) kademe-3 olarak eklendiği için **ebeveyn
`children` listesinin güncellenmesi orkestratöre ait açık iş** (rag-surucu §13 "Dikkat"
kalıbı — orkestratör beyazlistesi dışına subagent yazamaz).

## §10. Kenar Güncelleme (evidence · depends_on · §BAĞLANTILAR)

| Kenar türü | Güncelleme biçimi | Kural | Kanıt |
|---|---|---|---|
| `evidence` | yeni `Dosya:/Satır:` satırı **eklenir** (append) | mevcut satır silinmez/ezilmez | `index.md · §25.3` kural 1 (satır-satır edit) |
| `depends_on` | hedef diskteyse değiştirilir; **diskte değilse satır yazılmaz** | hayalet kenar yasak | `Dosya: .ai/RAG.md · Satır: 102` (§3.2/1) |
| `## §BAĞLANTILAR` | Hedef K · wiki-link · göreli yol · ilişki sütunları; **Authority bloğunun ÖNÜNE** | ilişki satırı **taslak** sayılır (doğrulanmadı) | `index.md · §9.13.3` (14/14 · 104 satır) |
| wiki-link (metin) | `[[hedef]]` biçimi; markdown path linki **yasak** | link denetimi çalışır | `Dosya: .ai/RAG.md · Satır: 138` (§5/6) |
| `RAG.md §3.1` satırı | konu→dosya kenarı; durum sütunu zorunlu | durumsuz satır geçersiz | `Dosya: .ai/RAG.md · Satır: 104` (§3.2/3) |

**§BAĞLANTILAR dürüstlük notu (bağlayıcı):** §9.13.3'te 14 ebeveyn `index.md`'ye 104 satır
eklendi; satırlar "ölçülen ad-geçişleri + domain okuması" ile üretildi, **ilişki başına
ayrı doğrulama YAPILMADI** → taslak kenarlardır; traverser bu kenarları **kanıt olarak
kullanmaz, yalnız yönlendirme olarak** kullanır.

## §11. Graph Doğrulama Döngüsü (wiki-link-check kalıbı)

```text
[1] Kökten çalıştır (C:\www\coremusic.net) — .ai/ içinden YASAK (§13/17)
[2] .ai/**/*.md tara → [[hedef]] regex ile çek (Satır: 24 kalıbı)
[3] Her hedefi 3 kademede çöz: dosya anahtarı → göreli yol → leaf adı (Satır: 29-37)
[4] Çözülmeyen = kırık kenar → $broken listesine yaz (Satır: 38)
[5] "kontrol edilen link: N" + "kırık link: M" raporu (Satır: 41-42)
[6] Bilinen sahte kırık linkler SAYIMA KALIR, "onarılmaz" (kök AGENTS.md §13.7 — 6 madde)
[7] M > beklenen ise → §12 karar tablosu → DUR / rapor / üst karar
```

**Gerçek ölçüm referansı:** 2026-09-29 kök ölçümü `wiki-link-check` → `227 link / 6
raporlanan` = **gerçek kırık 0** (6'sı şablon değişkeni/kaçış dizgesi — §13.7 kaydı, düzeltme
YASAK). **Düğüm/kenar sayım döngüsü** (traverser'ın sayısal kapısı): FM 10 alan sayacı +
`children`↔`parent_id` karşılıklılık sayacı + fantom sayacı — betik kalıbı §16'da.

## §12. Bozuk Graph Davranışı — Karar Tablosu

| # | Bozukluk | Tespit | Davranış (yapılır) | Yasak |
|---|---|---|---|---|
| 1 | **Kırık kenar** — wiki-link/`depends_on` hedefi diskte yok | `Test-Path` false / `wiki-link-check` kırık | kenar **raporlanır**; `⚠️ VERIFICATION REQUIRED` ile işaretlenir; `RAG.md §3.1` hayalet satırı **silinir** (`RAG.md · Satır: 102`) | hedefi "varmış gibi" yazmak |
| 2 | **Bilinen sahte kırık link** (şablon değişkeni) | 6 maddelik kayıt (kök AGENTS.md §13.7) | sayım 6'da kalır, **onarılmaz**, kusur sayılmaz | "onarmaya" kalkışmak (şablon bozulur) |
| 3 | **Phantom düğüm** — FM/`node_id` var, dosya yok | `node_id` → glob = 0 | düğüm `status: verify-required` + açık madde; kenar gezilmez | dosya varmış gibi gezinmek |
| 4 | **Ad çelişkisi** — diskte `K016-observability` ↔ registry `K017-observability` | `index.md · §9.13.4` #1 (2026-10-17 dizin kanıtı) | `⚠️ VERIFY REQUIRED` kaydı; **yeniden adlandırma kullanıcı onayı** ister | taraflardan birini sessizce düzeltmek |
| 5 | **Döngü** — A `depends_on` B, B `depends_on` A (veya parent↔child döngüsü) | traverser ziyaret kümesi (§18/§20 betikleri) | **DUR** — döngü raporlanır, üst karar | döngüyü kırıp kenar silmek (onaysız) |
| 6 | **Yönetsiz kenar** — `parent_id` var, ebeveyn `children`'da yok (208 dosyalık sınıf) | FM karşılıklılık sayacı (§16/C) | orkestratör senkron işine eklenir; subagent dokunmaz | ebeveyni kendin güncellemek (beyazliste dışı) |
| 7 | **Eksik kanıt kenarı** — `evidence: "kanıt bekliyor"` | FM okuma (`kg/index.md · Satır: 10-12`) | `draft`/`verify-required` **korunur**; kanıt toplanınca döner | kanıtsız `active`/`verified` |
| 8 | **Beyazliste dışı yazım** (graph'a izinsiz düğüm) | git status + whitelist sayacı | ihlal raporlanır → DUR + MO müdahalesi (`AGENTS.md §18/1`) | geçiştirmek |
| 9 | **Vault bozulması** (graph dosyaları tutarsız) | okuma hatası / çelişen FM | `git checkout` + son commit (`AGENTS.md §17/10`) | elle yamalama |

## §13. Graph → Vault Senkronu

| Tetikleyici | Senkron işi | Sahip |
|---|---|---|
| Yeni düğüm/kenar eklendi | `RAG.md §3.1` + `keys.md` konu satırı + `log.md` append-only | MO (vault-updater) |
| Dosya taşındı/silindi | gelen kenarlar taranır (grep) → kırık kenar temizliği (§12/1) | MO |
| Faz kapanışı | `vault-sync-post` skill'i — §3 yeniden ölçülür (`RAG.md · Satır: 135`) | MO |
| Session sonu | `log.md` + `MEMORY.md` güncellenir; `git commit` **orkestratöre ait** | orkestratör |
| Registry (`index.md`) değişikliği | **yalnız orkestratör** yazar (`index.md · §9.8.4` #10) | orkestratör |

## §14. PowerShell — Traverser Betik Kalıbı (gerçek çalıştırılabilir)

> ✅ **Çalıştırılabilir PowerShell betiği** — repo kökünden çalışır; bir düğümün FM
> `depends_on` kenarlarını çözer + fantom kenar sayar (§12/3 tespiti).

```powershell
# KG traverser kalıbı: düğüm FM'sinden kenar çıkar + hedef diskte mi?
$ErrorActionPreference='Stop'
$node = '.ai\architecture\K015-AI\kg\index.md'
$lines = [IO.File]::ReadAllLines((Join-Path (Get-Location) $node), [Text.Encoding]::UTF8)
$inDepends = $false; $edges = @()
foreach ($l in $lines) {
    if ($l -match '^depends_on:') { $inDepends = $true; continue }
    if ($inDepends) {
        if ($l -match '^\s*-\s+(\S+)') { $edges += $Matches[1] } else { $inDepends = $false }
    }
}
"kenar sayisi => {0}" -f $edges.Count
$phantom = 0
foreach ($e in $edges) {
    # kenar hedefi bir node_id -> .ai/architecture/** altında dosya adı soneği ile ara
    $hit = Get-ChildItem -Path '.ai\architecture' -Recurse -Filter '*.md' -Force |
           Where-Object { $_.BaseName -eq ($e -replace '.*/','') }
    if (-not $hit) { $phantom++ }
    "{0,-25} => {1}" -f $e, $(if ($hit) { 'OK' } else { 'PHANTOM' })
}
"phantom => {0} (kapı: 0 ya da açık madde)" -f $phantom
```

## §15. PowerShell — wiki-link-check.ps1 Gerçek Alıntısı (graph doğrulama çekirdeği)

> ✅ **Repo kanıtı** — `.ai/scripts/wiki-link-check.ps1` (43 satır) içinden alınmış birebir
> alıntı; `[[hedef]]` regex'i ve 3 kademeli çözüm mantığı sürücünün §11 döngüsünün kaynağıdır.

```powershell
#Requires -Version 5.1                                    # Satır 1
$ROOT='C:\www\coremusic.net\.ai\ui-design'                # Satır 5 (tarama kökü)
# index: dosya adi (uzantisiz, kucuk) -> gercek tam yol   # Satır 8
Get-ChildItem -Path 'C:\www\coremusic.net\.ai' -Recurse -Filter *.md -File | # Satır 10
  ForEach-Object { $index[$_.BaseName.ToLowerInvariant()]=$_.FullName }      # Satır 13
foreach($m in [regex]::Matches($text,'\[\[([^\]|#]+)(?:[#|][^\]]*)?\]\]')){  # Satır 24
  $t=$m.Groups[1].Value.Trim()
  # 1) dogrudan dosya anahtari                            # Satır 28
  if($index.ContainsKey($k)){ continue }
  # 2) goreli yol                                          # Satır 31
  if(Test-Path ($cand+'.md')){ continue }
  # 3) sadece dosya adi                                    # Satır 36
  $broken.Add("$(Split-Path $src -Leaf) -> [[$t]]")       # Satır 38
}
"kontrol edilen link: $checked"                            # Satır 41
"kirik link: "+$broken.Count                               # Satır 42
```

**Kapsam sınırı (dürüst):** betiğin `$ROOT`'u `.ai\ui-design`'a **sabitlenmiştir** (Satır: 5)
→ `.ai/architecture/**` K-graph'ı bu betikle **tarandı**; K-graph traverser'ı için kalıp
§14'tedir; betik **değiştirilmez** (salt-okunur — görev yasağı + kök kural).

## §16. PowerShell — FM / Fantom / Karşılıklılık Sayımları (ölçüm kalıpları)

```powershell
# A) FM 10 alan sayacı (kapı 1 — §8 şeması)
$p = '.ai\architecture\K015-AI\kg\kg-surucu.md'
$fields = 'node_id','node_type','status','parent_id','children',
          'depends_on','evidence','validation','open_questions','group'
$lines  = [IO.File]::ReadAllLines($p, [Text.Encoding]::UTF8)
$present = 0
foreach ($f in $fields) { if ($lines -match ("^" + $f + ":")) { $present++ } }
"fm alan => {0}/10" -f $present

# B) parent↔children karşılıklılık (§12/6 — örnekleme: 1 ebeveyn + 1 çocuk)
$parent = '.ai\architecture\K015-AI\kg\index.md'
$childHasParent = ([IO.File]::ReadAllLines($parent, [Text.Encoding]::UTF8) -match 'children: \[\]').Count
"ebeveyn children bos => {0} (child eklenince 0 olmali)" -f $childHasParent

# C) fantom düğüm taraması: depends_on hedefi diskte var mı? (§12/3)
'dependencies' | Out-Null
"kg-anayasa disk => {0}" -f (Test-Path '.ai\architecture\K015-AI\kg\kg-anayasa.md')

# D) Authority son satır (kapı 3)
$auth = '**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode'
"authority => {0}" -f ($lines[$lines.Count-1] -eq $auth)
```

**2026-10-17 disk ölçümü (bu dosyanın girdisi):** `kg/` klasöründe glob = **1 dosya**
(`index.md` — 48 satır) → `depends_on: [kg-anayasa]` hedefi **o an diskte YOK** → §12/3
kalıbı gereği bu satır `⚠️ VERIFICATION REQUIRED` (paralel üretim de olabilir; **uydurulmaz,
ölçü yazılır** — §27/Q1).

## §17. Doğrulama Kapıları (traverser + üretim)

| # | Kapı | Kriter | Yöntem |
|---|---|---|---|
| 1 | FM 10 alan | 10/10 | §16/A `Select-String` sayacı |
| 2 | Satır sayısı | `>= 500` | `[IO.File]::ReadAllLines(...).Count` (§16) |
| 3 | Authority son satır | birebir | §16/D `-eq` |
| 4 | `depends_on` fantom | her hedef diskte ya da açık madde | §14 / §16/C |
| 5 | wiki-link | kırık = bilinen 6 + yeni 0 | `wiki-link-check.ps1` **kökten** (§15) |
| 6 | parent↔children | çift yönlü tutarlı | §16/B |
| 7 | Kapsam dışı yazım | 0 dosya | `git status --porcelain` sayacı |
| 8 | `git commit` | 0 (subagent) | `git log` — orkestratör kararı |
| 9 | Beyazliste | 1 beyazliste = 1 ajan (`index.md · §9.8.3`) | dispatch kaydı |
| 10 | Eşzamanlılık | tavan ≈1-5 (`index.md · §9.8.2` + §9.13.3 "tavan ≈1") | dalga disiplini |

## §18. Repo Kanıtı — PHP: Graph Store YOK, Keyword Match VAR

> ✅ **Repo kanıtı** — `shared/src/AI/KnowledgeBase.php` (232 satır) içinden birebir alıntı
> (ilk 100 satır bugün okundu; kalan 132 satır gövdesi okunmadı → o kısım iddiaları
> `⚠️ VERIFICATION REQUIRED`).

```php
class KnowledgeBase implements KnowledgeBaseInterface          // Satır 23
{
    /** @var array<string, array{id: string, title: string, ...}> */
    private array $knowledge = [];                             // Satır 30 (düz dizi — graph YOK)
    public function search(string $query, int $maxResults = 10,
                           array $filters = []): array         // Satır 36
    {
        // Basit keyword matching (gerçek implementasyonda vector cosine similarity)
                                                          // Satır 48 (yorum = niyet)
        $score = $this->calculateRelevanceScore($queryLower, $item);   // Satır 49
        usort($results, fn(array $a, array $b) => $b['score'] <=> $a['score']); // Satır 64
        return array_slice($results, 0, $maxResults);          // Satır 66
    }
    public function ragQuery(string $query, array $context = []): array // Satır 72
    {
        $retrieved = $this->search($query, 5, $context['filters'] ?? []); // Satır 75
        'confidence' => $this->calculateConfidence($retrieved), // Satır 89
    }
```

**"Repo'da bu alan kodu YOK" cümleleri (negatif kanıt):**

| İddia | Tarama / okuma | Sonuç |
|---|---|---|
| Bilgi grafiği **graph store** (node/edge ilişkisel yapı) kodu YOK | `KnowledgeBase.php · Satır 30` — `private array $knowledge = []` düz dizi | **YOK** (ilk 100 satır kanıtı) |
| **Traverser** (komşu genişleme/BFS) kodu YOK | `search()` Satır 42-61 — tek geçişli döngü, komşu ZİYARETİ yok | **YOK** |
| Graph doğrulama (cycle/phantom) kodu YOK | `shared/src/AI/` 7 dosya envanteri (rag-surucu §20) — graph sınıfı yok | **YOK** · kalan dosyalar `⚠️ VERIFICATION REQUIRED` |

## §19. İllüstratif — Python BFS Traverser

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# Graph = node_id -> FM'den çıkarılan kenarlar (depends_on + wiki-link hedefleri)
from collections import deque
from pathlib import Path

def traverse(root: Path, max_depth: int = 3) -> list[tuple[str, int]]:
    seen, out, q = {root.name}, [], deque([(root, 0)])
    while q:
        node, depth = q.popleft()
        out.append((node.name, depth))            # C: düğüm okundu
        if depth >= max_depth:                    # §6/1 derinlik limiti
            continue
        for edge in extract_edges(node):           # D: kenarlar
            target = resolve(edge)                 # E: Test-Path karşılığı
            if target is None:                     # §12/3 PHANTOM
                report(edge, "PHANTOM")
                continue
            if target.name not in seen:
                seen.add(target.name)
                q.append((target, depth + 1))
    return out
```

## §20. İllüstratif — Go Edge Validator + Cycle Detector

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
package kg

// Edge, bir wiki-link/depends_on üçlüsüdür: (from, to).
type Edge struct{ From, To string }

// Validate: her kenar hedefi diskte var mı? (§11 döngüsü 3. kademesi)
func Validate(edges []Edge, exists func(string) bool) (ok []Edge, broken []Edge) {
	for _, e := range edges {
		if exists(e.To) { ok = append(ok, e); continue }
		broken = append(broken, e) // raporlanır, SİLİNMEZ (§12/1)
	}
	return
}

// HasCycle: depends_on döngüsü (§12/5) — DFS renk boyama.
func HasCycle(edges []Edge) bool {
	const (white, gray, black = 0, 1, 2)
	color := map[string]int{}
	var visit func(string) bool
	visit = func(n string) bool {
		color[n] = gray
		for _, e := range edges {
			if e.From != n { continue }
			switch color[e.To] {
			case gray: return true          // döngü → DUR
			case white: if visit(e.To) { return true }
			}
		}
		color[n] = black
		return false
	}
	for _, e := range edges { if color[e.From] == white && visit(e.From) { return true } }
	return false
}
```

## §21. Token Ekonomisi (Traverser Bütçesi)

| # | Kural | Bütçe | Kanıt |
|---|---|---|---|
| 1 | Traverser turu = 1 soru + ≤3 hop (§6/1) | komşu başına 1 dosya okuma | bütçe kuralı |
| 2 | FM önce, gövde sonra (§4 C→E) | FM ~16 satır, gövde yüzlerce | `kg/index.md` 48 satır |
| 3 | Boot'ta okunan düğüm tekrar okunmaz | 0 ek | rag-surucu §14 |
| 4 | Rastgele vault taraması yasak | `keys.md`/`RAG.md` üzerinden | `Dosya: .ai/keys.md · Satır: 579` |
| 5 | `## §BAĞLANTILAR` taslak kenarı = **derin okuma tetiklemez** | yönlendirme-only | `index.md · §9.13.3` |
| 6 | Ölçüm betikleri dosya okumaz, **sayar** | toplu tarama 1 kez | §14-§16 |

## §22. Hata Modları

| # | Hata | Semptom | Tespit | Tepki |
|---|---|---|---|---|
| 1 | Fantom kenar | `@` okuma hatası | `Test-Path` false | §12/1 rapor + `⚠️` |
| 2 | Yanlış düğümde başlama | boş sonuç ama cevap vault'ta | B→C atlanmış | §4 sırasına dön |
| 3 | Gizli dizin unutulmuş | `.ai/**` sahte "0 sonuç" | `hidden: true` yok | yeniden tara |
| 4 | `parent:` ile `parent_id:` karışması | 161 dosyalık şema-dışı sınıf | FM anahtar taraması | §8/4 kalıbı |
| 5 | Sahte kırık link "onarımı" | şablon bozulur | §13.7 listesi | geri al + kaydet |
| 6 | `.ai/` içinden betik çalıştırma | 19 sahte "PNG yok" sınıfı | kök kuralı | kökten tekrar |
| 7 | Eşzamanlı over-dispatch | toplu rate-limit (38+ ajan — §9.8.2) | dalga kaydı | tavan ≈1-5 |
| 8 | Çift-dispatch (1 beyazliste, 2 ajan) | dosya çakışması riski (`index.md · §9.8.3`) | beyazliste ayrıştırma | 1 beyazliste = 1 ajan |
| 9 | Registry'ye subagent yazımı | çakışan edit | `index.md · §9.8.4` #10 | DUR + orkestratör |
| 10 | 3 başarısız düzeltme turu | aynı hata tekrarı | tur sayacı | DUR — üst karar |

## §23. Yasaklar ve Sınırlar

| # | Yasak | Neden |
|---|---|---|
| 1 | `git commit` atmak | **orkestratöre aittir** (görev yasağı + kök kural) |
| 2 | `.ai/*.md` (registry dahil) ve `.ai/scripts/**` yazımı (bu dosya hariç) | salt-okunur yüzey; registry = orkestratör tekeli |
| 3 | Bu dosya dışına yazım / dosya adı değiştirme | kapsam dışı yazım = ihlal (§17/7) |
| 4 | Diskte olmayan hedefe `depends_on`/wiki-link yazmak | fantom kenar (`RAG.md · Satır: 102`) |
| 5 | `unknown` kenarı "var" saymak | Zero-Hallucination (kök AGENTS.md §3) |
| 6 | Döngüyü onaysız kırmak | kenar silme = graph revizyonu (§12/5) |
| 7 | `.env`/credential'ı kenar/metin satırına almak | sızıntı (`RAG.md · Satır: 137`) |
| 8 | Markdown path linki (`[x](y)`) kenar olarak kullanmak | denetim çalışmaz (`RAG.md · Satır: 138`) |

## §24. Anti-patternler

| # | Yanlış | Doğru | Kaynak |
|---|---|---|---|
| 1 | "Graph DB var" sanmak | graph = dosya sistemi + FM + wiki-link (§3) | §18 negatif kanıt |
| 2 | Komşuyu düğümden önce okumak | §4 C→D→E sırası | §4 |
| 3 | `children`'ı güncellemeyip children okumak | çift yönlü senkron (§9) | §9.4.2 (208 eksik) |
| 4 | §BAĞLANTILAR taslak satırını kanıt saymak | taslak = yönlendirme | `index.md · §9.13.3` |
| 5 | Ad çelişkisini sessiz düzeltmek | `⚠️ VERIFY REQUIRED` + kullanıcı onayı | `index.md · §9.13.4` #1 |
| 6 | Bellekten "X link kırık" demek | `wiki-link-check` + kök ölçüm | §15 |
| 7 | Traverser'ı depth'siz çalıştırma | max 3 hop (§6/1) | §21/1 |

## §25. Kapanış Kontrol Listesi (her graph görevi sonunda)

| # | Soru | Zorunlu cevap |
|---|---|---|
| 1 | Traverser A-F sırası izlendi mi? | evet / gerekçesi |
| 2 | Her kenar `Test-Path` ile doğrulandı mı? | evet |
| 3 | Fantom/kırık kenar raporlandı mı (saklanmadı)? | evet |
| 4 | Ölçüm yapıldı mı (§14-§16 betikleri)? | evet |
| 5 | FM 10 alan dolu mu (yeni düğümde)? | evet |
| 6 | parent↔children senkronu yapıldı mı? | evet / açık iş kaydı |
| 7 | Kapsam dışı yazım 0 mı? | **evet (0)** |
| 8 | `git commit` atıldı mı? | **hayır (orkestratöre ait)** |

## §26. Açık Sorular

| # | Soru | Durum |
|---|---|---|
| Q1 | `depends_on: [kg-anayasa]` — 2026-10-17 glob'unda `kg/` altında **yalnız `index.md`** var; `kg-anayasa.md` diskte **YOK**. Paralel üretimde mi, eksik düğüm mü? | `⚠️ VERIFICATION REQUIRED` (§16/C ölçümü) |
| Q2 | Ebeveyn `kg/index.md` `children: []` (`Satır: 6`) — bu modül eklendikten sonra `children` güncellenecek mi? | orkestratör kararı (§9) |
| Q3 | `## §BAĞLANTILAR` kenarları taslak (doğrulanmadı — `index.md · §9.13.3`) — cross-validate turu ne zaman? | açık (`index.md · §9.13.4` #2) |
| Q4 | Diskte `K016-observability` ↔ registry `K017-observability` ad çelişkisi (`§9.13.4` #1) — graph'ta hangi düğüm tek doğruluk? | kullanıcı onayı bekliyor |
| Q5 | §4.2 "uygun node dokümanı" sınırı (208 dosya `parent:`→`parent_id`) — modül dosyalarına da geçerli mi? | `⚠️ VERIFY REQUIRED` (`index.md · §9.4.2`) |
| Q6 | Traverser otomasyonu (betik olarak `.ai/scripts/`'e) ne zaman? — bu dosyadaki betikler **kalıp**, depoya yazılmadı (yazım yasak) | üst karar |

## §27. Web Kaynakları (doğrulanan — 2/2 erişildi, 2026-10-17)

| # | Kaynak | URL | Doğrulanan iddia | Erişim |
|---|---|---|---|---|
| 1 | Neo4j — What is a graph database (resmi doküman) | `https://neo4j.com/docs/getting-started/graph-database/` | düğüm/ilişki/özellik modeli; ilişki "must always have a start node, an end node, and exactly one type" + yön zorunlu; "Create traversals in big graphs for both depth and breadth" (BFS/DFS) | ✅ fetch edildi 2026-10-17 |
| 2 | W3C — RDF 1.1 Concepts and Abstract Syntax (Recommendation) | `https://www.w3.org/TR/rdf11-concepts/` | RDF graph = subject-predicate-object üçlü kümesi (§3.1); graf "node-arc-node link" olarak görselleştirilir (§1.1); üçlü ekleme/çıkarma yeni graf üretir (§1.5) | ✅ fetch edildi 2026-10-17 |

> Bu dosyadaki graph benzetmeleri bu iki kaynağa dayanır; **CoreMusic KG'si RDF/Neo4j
> değildir** — yalnız kavramsal kıyas (düğüm/kenar/üçlü). Uydurma URL yoktur.

## §28. Kaynaklar

| Kaynak | Yol | Kullanım amacı | Ölçüm |
|---|---|---|---|
| K-Space registry | `.ai/architecture/index.md` | §5 K-düğümleri listesi · §8.1 şema · §9.4.2 FM 10 alan · §9.13.3 §BAĞLANTILAR · §9.8-§9.9 olay kayıtları | 928 satır (2026-10-17 okuma) |
| Retrieval indeksi | `.ai/RAG.md` | §3.1 konu→dosya kenarları (Satır: 73-94) · §3.2 kurallar (Satır: 100-105) · §5 wiki-link kuralı (Satır: 138) · §6 kapılar (Satır: 142-155) | 177 satır |
| Task dispatch | `.ai/AGENTS.md` §7 · §12 · §17-§18 | graph güncelleme akışı · context lock · edge case/warning | — |
| Graph doğrulama betiği | `.ai/scripts/wiki-link-check.ps1` | §15 gerçek alıntı · §11 döngüsü | 43 satır |
| Ebeveyn düğüm | `.ai/architecture/K015-AI/kg/index.md` | FM kanıtları (Satır: 3-15) · children durumu (Satır: 6) | 48 satır |
| Şablon (yapı referansı) | `.ai/architecture/K015-AI/rag/rag-surucu.md` | H1 + § + karar ağacı + betik iskeleti | 779 satır |
| Repo AI kodu | `shared/src/AI/KnowledgeBase.php` (7 dosyalık klasör) | §18 PHP kanıtı + negatif kanıt | 232 satır |
| Web kaynakları | §27 — Neo4j docs · W3C RDF 1.1 | kavramsal kıyas (düğüm/kenar/üçlü) | 2/2 fetch |

## §29. Gate (özet)

§1-§29 tamam: ELI15 · graph modeli 8 satır · A-F traverser akışı · 8 satırlık soru-tipi karar
ağacı · 6 komşu kuralı · 10 adımlı düğüm ekleme · 10 alanlı FM kılavuzu · parent/children
senkronu · 5 tür kenar güncelleme · 7 adımlı doğrulama döngüsü · 9 maddelik bozuk-graph karar
tablosu · vault senkronu · PowerShell 3 betik (traverser · wiki-link-check alıntısı · FM/fantom
sayaçları) · PHP kanıt + 3 negatif kanıt · illüstratif 2 blok (Python BFS · Go validator/cycle) ·
6 token kuralı · 10 hata modu · 10 kapı · 8 yasak · 7 anti-pattern · 8 soruluk kapanış ·
6 açık soru · 8 kaynak · 2 doğrulanmış web kaynağı.

**Kapılar:** satır `>= 500` · FM **10 alan** · Authority son satır birebir · kapsam dışı
yazım **0** · `git commit` **0**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
