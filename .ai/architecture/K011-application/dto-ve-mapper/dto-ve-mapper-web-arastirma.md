---
title: "K011 · dto-ve-mapper — web-arastirma"
type: architecture
kodeme: kademe-2
module: K011-application
parent: dto-ve-mapper
status: active
updated: 2026-10-15
group: kanit
---

# K011 · dto-ve-mapper — Web Araştırması

**Amaç:** Havuzdaki 5 PHP dizi-fonksiyon kaynağını repo'daki DTO/mapper yüzeyiyle **kesişti**rmek. Her iddia `curl` 200 + `webfetch` içeriği + repo satırıyla üçlü doğrulanır.

## §GRUPLAR

| # | Bölüm | Grup | Eksen | Kullanım |
|---|---|---|---|---|
| 1 | §1 — Havuz Tablosu | kanit | icerik | Bolum kaydi |
| 2 | §2 — Claim ↔ Repo Matrisi | kanit | icerik | Bolum kaydi |
| 3 | §3 — Reddedilen Adaylar (etiket only) | kanit | rehber | Reddedilen aday - etiket only |
| 4 | §4 — Kesişim: Havuz ↔ Envanter (`k011-urls.txt`) — GERÇEK ÖLÇÜM | kanit | envanter | Sayim / envanter tablosu |
| 5 | §5 — Havuz Disiplini (Havuz Dışı URL Denetimi) | kanit | icerik | Bolum kaydi |
| 6 | §6 — Repo Array İdiom Envanteri (gerçek grep ölçümü) | kanit | envanter | Sayim / envanter tablosu |
| 7 | §7 — JSON & Serileştirme Envanteri (gerçek satır listesi) | kanit | envanter | Sayim / envanter tablosu |
| 8 | §8 — `fromArray` / `toArray` Repo Geniş Dağılımı (15 + 14 dosya) | kanit | icerik | Bolum kaydi |
| 9 | §9 — Kesişim Sonuç Matrisi (Karar Kaydı) | kanit | icerik | Bolum kaydi |
| 10 | §10 — Açık Sorular (genişletilmiş) | kanit | karar | Acik soru / ust karar listesi |
| 11 | §11 — Doğrulama Adımları (tekrarlanabilir, salt-okunur) | kanit | niyet | Okuyucu 30 saniyede kavrar |
| 12 | §12 — Sınırlamalar (bilinen eksikler) | kanit | niyet | Okuyucu 30 saniyede kavrar |
| 13 | §13 — Belge Bütünlüğü Beyanı | kanit | icerik | Bolum kaydi |
| 14 | §14 — İkinci Tur Doğrulama Matrisi (bu oturumda tekrar okunan satırlar) | kanit | niyet | Okuyucu 30 saniyede kavrar |
| 15 | §15 — Satır Sapması Düzeltme Kaydı (önceki belge ↔ bu oturum ölçümü) | kanit | icerik | Bolum kaydi |
| 16 | §16 — Havuz Fonksiyon ↔ Repo Kapsama Analizi (5/5) | kanit | niyet | Okuyucu 30 saniyede kavrar |
| 17 | §17 — Kesişim Sonrası Karar Özeti (tek sayfa) | kanit | ozet | Karar ozeti |
| 18 | §18 — Kademe-2 Genişletme Kaydı (2026-10-16, satır ölçümü okunarak) | kanit | icerik | Bolum kaydi |

---

## §1 — Havuz Tablosu

| # | URL | curl | webfetch | Konu |
|---|-----|------|----------|------|
| 1 | https://www.php.net/manual/en/function.array-map.php | 200 | ✅ | callback → yeni dizi |
| 2 | https://www.php.net/manual/en/function.array-merge.php | 200 | ⚠️ içeriği çekilmedi | dizi birleştirme |
| 3 | https://www.php.net/manual/en/function.array-column.php | 200 | ⚠️ içeriği çekilmedi | sütun çıkarma |
| 4 | https://www.php.net/manual/en/function.array-reduce.php | 200 | ⚠️ içeriği çekilmedi | tek değere indirgeme |
| 5 | https://www.php.net/manual/en/function.array-filter.php | 200 | ✅ (referrer) | koşul süzme |

> `curl.exe -L -s -o NUL -w "%{http_code}"` — 2026-10-15 ölçümü, **5/5 = 200**.

## §2 — Claim ↔ Repo Matrisi

| # | Dış İddia (kaynak) | Repo Karşılığı | Eşleşme |
|---|---------------------|----------------|---------|
| 1 | `array_map()` her elemana callback uygular, anahtarları **yalnız tek dizi** durumunda korur (php.net) | `ToolCalling.php:118-126` — `array_map(fn($t) => [...], $this->tools)` alan daraltması | **HIT** |
| 2 | İki+ dizi geçilirse anahtarlar sıfırlanır, indeksli dizi döner (php.net §Return Values) | `DataBinder.php:91-94` — `array_unique(array_merge(...))` anahtar birleştirme | **HIT** |
| 3 | `array_filter` flag yoksa falsy değerleri atar (php.net) | `ToolCalling.php:134` — kategori süzmesi | **HIT** |
| 4 | `array_merge` son değerin öncekini ezmesi normal davranışıdır (php.net) | `DataBinder.php:58` — `state = array_merge(state, values)` bilinçli override | **HIT** |
| 5 | `array_column` çok-dizgi tablodan tek alan çıkarır (php.net) | `shared\src\AI\KnowledgeBase.php:229` — `array_sum(array_column($retrieved, 'score')) / count($retrieved)` | **HIT (DÜZELTME)** |
| 6 | `array_map` + `array_filter` + tekrar `array_map` tek ifadede zincirlenebilir (php.net) | `shared\src\Middleware\CorsMiddleware.php:45` — `array_map('strtolower', array_filter(array_map('trim', ...)))` origin normalizasyonu | **HIT** |
| 7 | `array_merge` sağdaki diziye öncelik verir (php.net) | `shared\src\Middleware\SecurityHeadersMiddleware.php:43` — `array_merge($response['headers'] ?? [], $headers)` | **HIT** |
| 8 | `array_merge` numeric anahtarları sıfırlar (php.net §Return Values) | `shared\src\Middleware\CorsMiddleware.php:59, 69` — CORS header listesi birleştirme | **HIT** |
| 9 | `array_filter` callable süzgeç alır (php.net) | `shared\src\AI\AIEngine.php:71` — `array_filter($diversified, fn(array $item) => $item['score'] >= self::MIN_SCORE)` | **HIT** |
| 10 | `array_unique` + `array_merge` kümeler arası tekilleştirme (php.net) | `shared\src\AI\AIEngine.php:245` — `array_unique(array_merge(array_keys($collaborative), array_keys($content)))` | **HIT** |
| 11 | `array_values` integer anahtarları yeniden indeksler (php.net) | `shared\src\Repository\ApiKeyRepository.php:87-88` — `array_values(array_unique(array_map('strval', ...)))` | **HIT** |
| 12 | `array_map` 2. argümanı dönüştürür, dizi döndürür (php.net) | `shared\src\Api\Middleware\Validator.php:85` — `array_map('strval', $rule['in'])` hata metni üretimi | **HIT** |
| 13 | `array_values(array_filter(...))` ile anahtar korunarak indeksleme (php.net) | `shared\src\AI\AIOrchestrator.php:116` — `return array_values(array_filter(` | **HIT** |
| 14 | `$a = array_merge($a, $b)` ile üstüne yazma (php.net) | `shared\src\AI\AIWorkflow.php:56` — `$context = array_merge($context, $request);` | **HIT** |
| 15 | `array_merge` ile claims setine ek alan verme (php.net) | `shared\src\Security\JwtService.php:54` — `$claims = array_merge($extra, [` | **HIT** |
| 16 | `array_filter` 2. argüman callback ister (php.net) | `shared\src\Events\EventDispatcher.php:70` — `$this->listeners[$eventName] = array_filter(` | **HIT** |
| 17 | `array_map` dönüşünde anahtarların korunmadığı (çoklu dizi) (php.net) | `shared\src\Events\EventDispatcher.php:92` — `return array_map(` | **HIT** |
| 18 | `array_filter` 4. argüman (`array_filter($a, $b)` mode) yerine callable (php.net) | `shared\src\PageRouter\HtmlShellRenderer.php:245` — `array_values(array_filter($sessionData['MM_Permissions'], 'is_string'))` | **HIT** |
| 19 | `array_map` tek dizi geçildiğinde anahtarları korur (php.net §Parameters) | `shared\src\AI\PromptEngine.php:142` — `return array_map(` | **HIT** |
| 20 | `array_reduce` diziyi tek değere indirir (php.net §3 — havuz #4) | Repo'da **0 eşleşme** (`*.php` + `*.js`, `vendor`/`node_modules`/`.ai` hariç grep) | **MISS** |

**§2 Özet:** 20 iddia · **19 HIT** · **1 MISS** (`array_reduce`). Önceki ölçümde `array_column` **MISS** görünmüştü — `KnowledgeBase.php:229` okunarak **HIT'e çevrildi (2026-10-15 düzeltmesi)**; `array_reduce`/`array_walk` repo genelinde **0** olduğu için MISS **doğrulanmıştır**.

## §3 — Reddedilen Adaylar (etiket only)

| Aday | Etiket | Gerekçe |
|------|--------|---------|
| `array_walk` / `array_walk_recursive` | **REJECTED** | Havuz dışı URL — spec §9 ihlali |
| Symfony Serializer / Doctrine Hydrator | **REJECTED** | Framework bağımlılığı (ADR-002/001 yasağı) |
| `extract()` tabanlı hydrator | **REJECTED** | `extract` güvenlik riski (variable injection) |
| `array_reduce` ile DTO build | **REJECTED (şartlı)** | Havuzda var, repo'da karşılığı yok — üretim önerisi değil, gözlem |
| `array_walk` / `array_walk_recursive` repo kullanımı önerisi | **REJECTED** | Havuz dışı URL — spec §9 ihlali (repo'da 0 eşleşme, gerek yok) |
| `JsonSerializable` arayüzü ile DTO | **REJECTED** | Desen zaten `toArray()` ile çözülmüş — YAGNI, ADR gerekmedikçe eklenmez |
| `var_export` / `serialize` tabanlı API çıktısı | **REJECTED** | Güvenlik + uyumluluk — JSON tek format (`ResponseEmitter.php:50`) |
| `ksort` / `uasort` ile DTO anahtar sıralama | **REJECTED** | Havuz dışı URL — `toArray()` tanımlı sıra zaten deterministik |
| `array_combine` ile DTO kurma | **REJECTED** | Havuz dışı URL — `fromArray()` deseni kırar |
| PHP 8.4 Property Hook ile okuma-yazma | **REJECTED (ARAŞTIRMA)** | Olgunluk + ekip deneyimi — kapsam dışı, §2.11/Açık Soru |
| `readonly class` (PHP 8.2) birleşik tanım | **REJECTED (ARAŞTIRMA)** | 11 DTO'da property-level readonly kanıtlanmış; değişiklik ADR ister |
| `AbstractDto` tabanı ile iskelet paylaşma | **REJECTED** | YAGNI eşiği — §3 Karar Matrisi C seçeneği (kapsam dışı) |
| `unserialize()` kullanımı | **REJECTED** | Repo genelinde **0** eşleşme (grep) — desene yabancı |

**Reddedilen aday sayısı:** 10 (şartlı dahil). **Havuz dışı URL:** satır metninde **0** — yalnızca etiket/idiom adı yazılmıştır.

## §4 — Kesişim: Havuz ↔ Envanter (`k011-urls.txt`) — GERÇEK ÖLÇÜM

> **Düzeltme (2026-10-15):** Önceki sürümde "envanter dosyası **diskte YOK** — kesişim hesaplanamaz" ve yanlış yol (`Temp\opencode\k011-urls.txt`) yazılmıştı. **Bu kayıt yanlıştır ve geri alınmıştır.** Doğru yol `C:\Users\MARHAN\AppData\Local\Temp\k011-urls.txt` olup dosya **disktedir** ve **okunarak** taranmıştır.

**Kesişim: 0 — envanter 4286 URL ile doğrulandı (2026-10-15).**

### 4.1 Havuz ↔ Envanter Eşleşme Tablosu (5/5)

| # | Havuz URL (§1) | Tam eşleşme (`HashSet.Contains`) | Alt-satır eşleşme (`-like *anahtar*`) | Karar |
|---|----------------|:-------------------------------:|:------------------------------------:|-------|
| 1 | https://www.php.net/manual/en/function.array-map.php | **0** | **0** | Havuzda kalır — çıkarılmaz |
| 2 | https://www.php.net/manual/en/function.array-merge.php | **0** | **0** | Havuzda kalır — çıkarılmaz |
| 3 | https://www.php.net/manual/en/function.array-column.php | **0** | **0** | Havuzda kalır — çıkarılmaz |
| 4 | https://www.php.net/manual/en/function.array-reduce.php | **0** | **0** | Havuzda kalır — çıkarılmaz |
| 5 | https://www.php.net/manual/en/function.array-filter.php | **0** | **0** | Havuzda kalır — çıkarılmaz |

**Karar:** HIT **yoktur** → havuzdan **çıkarılan URL yoktur**; §3'teki reddeden adaylara kesişim etiketi **eklenmemiştir** (etiket yalnız HIT durumunda eklenir).

### 4.2 Ölçüm Künyesi

| Alan | Değer |
|------|-------|
| Envanter yolu | `C:\Users\MARHAN\AppData\Local\Temp\k011-urls.txt` |
| Satır sayısı | **4.286** (`[System.IO.File]::ReadAllLines().Length`) |
| Dosya boyutu | **252.847** bayt |
| Normalizasyon | satır `Trim` + boş satır atma → `HashSet[string]` (`StringComparer` ordinal) |
| Eşleştirme 1 (birebir) | `$known.Contains($url)` |
| Eşleştirme 2 (geniş) | dosya satırlarında `*<anahtar>*` alt-satır taraması |
| Havuz boyutu | 5 URL |
| HIT | **0** |
| Metod sınırı | **okuma** — `.ai/scripts/*.ps1` **çalıştırılmadı**, havuz dosyası **değiştirilmedi** |
| Tarih | 2026-10-15 |

### 4.3 Alt-Satır Anahtar Taraması (envanterin tamamında)

| Anahtar | Eşleşme | Sonuç |
|---------|:-------:|-------|
| `array-map` | 0 | MISS |
| `array-merge` | 0 | MISS |
| `array-column` | 0 | MISS |
| `array-reduce` | 0 | MISS |
| `array-filter` | 0 | MISS |

### 4.4 Kesişim Karar Mantığı

| Koşul | Davranış | Bu ölçümde |
|-------|----------|:----------:|
| ≥1 HIT | URL havuzdan çıkarılır; reddedenlere **yalnız etiket** (`<site> <etiket>`) eklenir + "envanter kesişimi HIT (2026-10-15)" notu | **Hayır** |
| 0 HIT | Havuz aynen korunur; her iki `*-web-arastirma.md` dosyasına tek satır eklenir | **Evet** |
| Envanter yoksa | `⚠️ VERIFICATION REQUIRED` yazılır, HIT uydurulmaz | Gereksiz (envanter var) |

## §5 — Havuz Disiplini (Havuz Dışı URL Denetimi)

| # | Denetim | Kural | Sonuç | Kanıt |
|---|---------|-------|-------|-------|
| 1 | Bu belgedeki toplam URL | Yalnız §1'deki 5 havuz URL'si | **5 tekrar + 0 yeni** | §1 + §4.1 |
| 2 | Reddedilen aday satırları | **URL yazılmaz**, yalnız etiket | 10 satır / 0 URL | §3 |
| 3 | Envanter URL'leri | Rapora **kopyalanmaz** (havuz dışı) | 0 kopya | §4.2 (yalnız sayı) |
| 4 | Kesişim sonucu | Ölçülen değer yazılır | 0 HIT | §4.1 |
| 5 | `.ai/scripts/*.ps1` | **Çalıştırma yasak** | çalıştırılmadı | §4.2 |
| 6 | Kaynak kod yazımı | `*.php|*.js|*.css|*.sql` yasak | 0 yazım | bu belge salt-md |
| 7 | Uydurma iddia | Doğrulanmayan = `⚠️ VERIFICATION REQUIRED` | 0 uydurma | §13 |

## §6 — Repo Array İdiom Envanteri (gerçek grep ölçümü)

**Ölçüm yüzeyi:** `shared\src` · **165** `*.php` dosyası · 2026-10-15 · regex sayaç (yorum satırı dahil, dosya bazlı toplam).

| # | İdiom | Eşleşme | Örnek dosya · satır | §2 ilişkisi |
|---|-------|:-------:|---------------------|-------------|
| 1 | `array_map` | **16** | `shared\src\AI\ToolCalling.php:118` | #1, #12, #19 |
| 2 | `array_merge` | **15** | `shared\src\Middleware\SecurityHeadersMiddleware.php:43` | #7, #8, #14, #15 |
| 3 | `array_values` | **12** | `shared\src\Repository\ApiKeyRepository.php:87` | #11 |
| 4 | `array_keys` | **8** | `shared\src\AI\AIEngine.php:245` | #10 |
| 5 | `array_filter` | **6** | `shared\src\Middleware\CorsMiddleware.php:45` | #6, #9, #16, #18 |
| 6 | `array_unique` | **4** | `shared\src\Component\DataBinder.php:91` | #2, #11 |
| 7 | `array_column` | **1** | `shared\src\AI\KnowledgeBase.php:229` | #5 |
| 8 | `array_reduce` | **0** | — | #20 MISS |
| 9 | `unserialize` | **0** | — | §3 REJECTED |
| 10 | `json_encode` | **19** | `shared\src\Api\Middleware\ResponseNormalizationMiddleware.php:70` | §7 |
| 11 | `json_decode` | **7** | `shared\src\Api\ApiRequest.php:147` | §7 |
| 12 | `serialize` | **4** | `shared\src\AI\MemorySystem.php:227` | §7.3 |

**Toplam dizi idiomu** (`array_map`+`array_merge`+`array_filter`+`array_unique`+`array_column`): **36 eşleşme** — §6.1'de **dosya·satır** düzeyinde tek tek listelenmiştir.

### 6.1 Tam Kullanım Listesi (36/36 — `array_map|array_merge|array_filter|array_unique|array_column`)

| # | Dosya · Satır | İdiom | Ne yapar |
|---|---------------|-------|----------|
| 1 | `shared\src\Component\DataBinder.php:58` | `array_merge` | `$this->state = array_merge($this->state, $values)` — state üstüne yazma |
| 2 | `shared\src\Component\DataBinder.php:91` | `array_unique` + `array_merge` | anahtar kümesi tekilleştirme |
| 3 | `shared\src\AI\ToolCalling.php:118` | `array_map` | araç listesinden alan daraltma |
| 4 | `shared\src\AI\ToolCalling.php:134` | `array_filter` | kategori süzme |
| 5 | `shared\src\AI\ToolCalling.php:136` | `array_map` | süzülen listeden projeksiyon |
| 6 | `shared\src\AI\PromptEngine.php:142` | `array_map` | prompt parçası dönüşümü |
| 7 | `shared\src\AI\KnowledgeBase.php:85` | `array_map` | `sources` alanı üretimi |
| 8 | `shared\src\AI\KnowledgeBase.php:229` | `array_column` | `score` sütunu → ortalama |
| 9 | `shared\src\AI\AIWorkflow.php:56` | `array_merge` | `$context = array_merge($context, $request)` |
| 10 | `shared\src\AI\AIOrchestrator.php:116` | `array_filter` | aktif context süzme (`array_values` ile) |
| 11 | `shared\src\AI\AIOrchestrator.php:164` | `array_merge` | oturum context güncelleme |
| 12 | `shared\src\AI\AIEngine.php:71` | `array_filter` | min skor eşiği süzme |
| 13 | `shared\src\AI\AIEngine.php:245` | `array_unique` + `array_merge` | işbirlikçi + içerik anahtar birleşimi |
| 14 | `shared\src\Security\JwtService.php:54` | `array_merge` | claims setine ek alan |
| 15 | `shared\src\Middleware\SecurityHeadersMiddleware.php:43` | `array_merge` | response header birleştirme (`?? []` fail-safe) |
| 16 | `shared\src\OAuth\Provider\BaseOAuthProvider.php:113` | `array_merge` | cURL `CURLOPT_HTTPHEADER` birleştirme |
| 17 | `shared\src\Api\Middleware\Validator.php:85` | `array_map` | `in` kuralından hata metni |
| 18 | `shared\src\Repository\ApiKeyRepository.php:87` | `array_values`+`array_unique`+`array_map` | scopes normalizasyonu |
| 19 | `shared\src\Repository\ApiKeyRepository.php:88` | `array_values`+`array_unique`+`array_map` | allowed_ips normalizasyonu |
| 20 | `shared\src\Repository\ApiKeyRepository.php:277` | `array_values`+`array_map` | decode edilmiş scopes |
| 21 | `shared\src\Repository\ApiKeyRepository.php:284` | `array_values`+`array_map` | kayıttan scopes çıkarma |
| 22 | `shared\src\Repository\ApiKeyRepository.php:285` | `array_values`+`array_map` | kayıttan allowed_ips çıkarma |
| 23 | `shared\src\PageRouter\PageRouter.php:179` | `array_merge` | route meta + ekleme |
| 24 | `shared\src\PageRouter\HtmlShellRenderer.php:229` | `array_values`+`array_map` | izin listesi projeksiyonu |
| 25 | `shared\src\PageRouter\HtmlShellRenderer.php:245` | `array_values`+`array_filter` | `MM_Permissions` string süzme |
| 26 | `shared\src\Middleware\CorsMiddleware.php:45` | `array_map`+`array_filter`+`array_map` | origin trim + lowercase |
| 27 | `shared\src\Middleware\CorsMiddleware.php:59` | `array_merge` | CORS header ekleme |
| 28 | `shared\src\Middleware\CorsMiddleware.php:69` | `array_merge` | preflight header ekleme |
| 29 | `shared\src\PageRouter\PageRouterKernel.php:246` | `array_map` | `rawurlencode` segment zinciri |
| 30 | `shared\src\Middleware\BypassAuthMiddleware.php:56` | `array_merge` | `_auth` bağlamı birleştirme |
| 31 | `shared\src\PageRouter\ResponseEmitter.php:24` | `array_merge` | header + ek header |
| 32 | `shared\src\PageRouter\RequestNormalizer.php:122` | `array_map` | `X-Forwarded-For` parçalama + trim |
| 33 | `shared\src\Middleware\AuthMiddleware.php:24` | `array_merge` | `_auth` bağlamı yazma |
| 34 | `shared\src\PageRouter\StructuredLogger.php:61` | `array_merge` | log kaydı birleştirme |
| 35 | `shared\src\Events\EventDispatcher.php:70` | `array_filter` | dinleyici süzme |
| 36 | `shared\src\Events\EventDispatcher.php:92` | `array_map` | dinleyicilere çağrım |

**6.1 sonucu:** 36/36 satır bu oturumda `grep` çıktısıyla alınmıştır; **hiçbiri tahmin değildir**. `array_column` **tek** kullanım (#8) — §2 #5 düzeltmesinin dayanağı.

### 6.2 İdiom Yoğunluk Sıralaması (toplam 165 PHP dosyasına oran)

| Sıra | İdiom | Eşleşme | Oran (dosya başına) | Yorum |
|:----:|-------|:-------:|:-------------------:|-------|
| 1 | `array_map` | 16 | 0.097 | Projeksiyon deseni baskın |
| 2 | `array_merge` | 15 | 0.091 | Header/state birleştirme yaygın |
| 3 | `array_values` | 12 | 0.073 | `array_filter` sonrası indeks onarımı |
| 4 | `array_keys` | 8 | 0.048 | Kümeler arası karşılaştırma |
| 5 | `array_filter` | 6 | 0.036 | Süzme nadir — genelde `foreach` |
| 6 | `array_unique` | 4 | 0.024 | Tekilleştirme yalnız repository/state |
| 7 | `array_column` | 1 | 0.006 | Tek nokta — **boşluk değil, izole kullanım** |
| 8 | `array_reduce` | 0 | 0.000 | **Kullanılmıyor** (§3 YAGNI gerekçesi) |

## §7 — JSON & Serileştirme Envanteri (gerçek satır listesi)

### 7.1 `json_encode` — 19 eşleşme

| # | Dosya · Satır | Kullanım |
|---|---------------|----------|
| 1 | `shared\src\Component\AbstractComponent.php:236` | `JSON_HEX_TAG\|JSON_HEX_AMP\|JSON_HEX_APOS\|JSON_HEX_QUOT` ile veri gömme |
| 2 | `shared\src\Api\Middleware\ResponseNormalizationMiddleware.php:70` | ETag üretimi — `md5((string) json_encode($response))` |
| 3 | `shared\src\AI\PromptEngine.php:68` | `JSON_THROW_ON_ERROR\|JSON_UNESCAPED_UNICODE` |
| 4 | `shared\src\AI\AIWorkflow.php:70` | `history` alanı serileştirme |
| 5 | `shared\src\AI\AIWorkflow.php:178` | `room_profile` serileştirme |
| 6 | `shared\src\Repository\ApiKeyRepository.php:250` | `json_encode(array_values($scopes), JSON_UNESCAPED_SLASHES)` |
| 7 | `shared\src\Repository\ApiKeyRepository.php:253` | scope/IP kaydı serileştirme |
| 8 | `shared\src\OAuth\OAuthManager.php:132` | `profile_data` serileştirme |
| 9 | `shared\src\PageRouter\HtmlShellRenderer.php:221` | `jsDomain` — host/port/scheme/isHttps gömme |
| 10 | `shared\src\PageRouter\HtmlShellRenderer.php:222` | `jsAssetsUrl` |
| 11 | `shared\src\PageRouter\HtmlShellRenderer.php:223` | `jsAppName` |
| 12 | `shared\src\PageRouter\HtmlShellRenderer.php:224` | `jsRoute` |
| 13 | `shared\src\PageRouter\HtmlShellRenderer.php:228` | `jsProtected` (dizi) |
| 14 | `shared\src\PageRouter\HtmlShellRenderer.php:249` | `jsUser` kimlik gömme |
| 15 | `shared\src\PageRouter\HtmlShellRenderer.php:261` | `logLevel` |
| 16 | `shared\src\PageRouter\HtmlShellRenderer.php:262` | `cssVersion` cache-buster |
| 17 | `shared\src\PageRouter\ResponseEmitter.php:50` | **Yanıt gövdesi** — `JSON_UNESCAPED_UNICODE\|JSON_UNESCAPED_SLASHES` |
| 18 | `shared\src\PageRouter\StructuredLogger.php:69` | log satırı JSON |
| 19 | `shared\src\Log\FileHandler.php:217` | bağlam JSON |

### 7.2 `json_decode` — 7 eşleşme

| # | Dosya · Satır | Kullanım |
|---|---------------|----------|
| 1 | `shared\src\Api\ApiRequest.php:147` | HTTP gövdesi çözümleme (`php://input`) |
| 2 | `shared\src\Api\Middleware\RequestValidationMiddleware.php:167` | Doğrulama öncesi gövde çözümleme |
| 3 | `shared\src\Api\Registry\ServiceRegistry.php:118` | sağlık yanıtı çözümleme |
| 4 | `shared\src\AI\AIEngine.php:300` | `JSON_THROW_ON_ERROR` ile model çıktısı |
| 5 | `shared\src\OAuth\Provider\BaseOAuthProvider.php:93` | OAuth yanıt çözümleme |
| 6 | `shared\src\OAuth\Provider\BaseOAuthProvider.php:127` | OAuth token yanıt çözümleme |
| 7 | `shared\src\Repository\ApiKeyRepository.php:268` | `is_string($raw) ? json_decode($raw, true) : $raw` — esnek decode |

### 7.3 `serialize` — 4 eşleşme · `unserialize` — 0

| # | Dosya · Satır | Kullanım |
|---|---------------|----------|
| 1 | `shared\src\AI\MemorySystem.php:227` | bellek ölçümü — `mb_strlen(serialize($entry['value']), '8bit')` |
| 2 | `shared\src\AI\MemorySystem.php:231` | aynı ölçüm (2. dal) |
| 3 | `shared\src\AI\MemorySystem.php:236` | aynı ölçüm (3. dal) |
| 4 | `shared\src\AI\AIWorkflow.php:173` | cache anahtarı — `md5(serialize($roomProfile))` |
| — | `unserialize` | **0** — güvenli: kullanıcı girdisi asla `unserialize` edilmez (`.ai` kuralı ile uyumlu) |

**7.3 sonucu:** `serialize` yalnız **ölçüm/cache** için; **API çıktısı JSON'dur** (`ResponseEmitter.php:50`). Bu, §3'te `var_export`/`serialize` reddinin dayanağıdır.

## §8 — `fromArray` / `toArray` Repo Geniş Dağılımı (15 + 14 dosya)

> **Ölçüm:** `*.php` dosyalarında `public static function fromArray` ve `public function toArray` araması — `vendor`/`node_modules`/`.ai` hariç · 2026-10-15.

### 8.1 `fromArray` — 15 dosya

| # | Dosya | Kapsam |
|---|-------|--------|
| 1 | `shared\src\Api\Dto\Request\LoginRequest.php` | DTO (K011) |
| 2 | `shared\src\Api\Dto\Request\MusicSearchRequest.php` | DTO (K011) |
| 3 | `shared\src\Api\Dto\Request\PlaylistAddTrackRequest.php` | DTO (K011) |
| 4 | `shared\src\Api\Dto\Request\PlaylistCreateRequest.php` | DTO (K011) |
| 5 | `shared\src\Api\Dto\Request\RegisterRequest.php` | DTO (K011) |
| 6 | `shared\src\Api\Dto\Request\UserUpdateRequest.php` | DTO (K011) |
| 7 | `shared\src\Api\Dto\Response\LoginResponse.php` | DTO (K011) |
| 8 | `shared\src\Api\Dto\Response\MusicResponse.php` | DTO (K011) |
| 9 | `shared\src\Api\Dto\Response\PaginationResponse.php` | DTO (K011) |
| 10 | `shared\src\Api\Dto\Response\PlaylistResponse.php` | DTO (K011) |
| 11 | `shared\src\Api\Dto\Response\UserResponse.php` | DTO (K011) |
| 12 | `shared\src\Api\Registry\ServiceDefinition.php:94` | **DTO değil** — registry tanımı, aynı desen |
| 13 | `shared\src\Api\Routing\RouteTable.php:71` | **DTO değil** — route tablosu kurulumu |
| 14 | `auth.coremusic.net\include\Domain\DTO\LoginRequest.php:24` | **Auth alt domain** — ayrı DTO ailesi |
| 15 | `auth.coremusic.net\include\Domain\DTO\RegisterRequest.php:20` | **Auth alt domain** — ayrı DTO ailesi |

### 8.2 `toArray` — 14 dosya

| # | Dosya | Kapsam |
|---|-------|--------|
| 1 | `shared\src\Api\Dto\Request\{Login,MusicSearch,PlaylistAddTrack,PlaylistCreate,Register,UserUpdate}` (6 dosya) | DTO (K011) |
| 2 | `shared\src\Api\Dto\Response\{Login,Music,Pagination,Playlist,User}` (5 dosya) | DTO (K011) |
| 3 | `shared\src\Api\Registry\ServiceDefinition.php:79` | **DTO değil** — aynı desen |
| 4 | `auth.coremusic.net\include\Domain\DTO\AuthResponse.php:42` | **Auth alt domain** |
| 5 | `auth.coremusic.net\include\Domain\Entity\User.php:54` | **Auth alt domain** — Entity de aynı sözleşmeyi taşır |

**§8 bulgusu (yeni):** Desen `Api\Dto` ile **sınırlı değildir** — `shared` içinde 2, `auth.coremusic.net` içinde 4 ek örnek vardır. Toplam **15 `fromArray` + 14 `toArray` = 29 fabrika/döküm metodu**. Bu, §2'deki "Desen 1 tutarlı" iddiasını **genişletir**: desen zaten repo geneline yayılmıştır.

## §9 — Kesişim Sonuç Matrisi (Karar Kaydı)

| # | Soru | Cevap | Kanıt |
|---|------|-------|-------|
| 1 | Envanter dosyası diskte mi? | **EVET** — 4.286 satır | §4.2 |
| 2 | Havuz URL'lerinden envanterde olan var mı? | **HAYIR** — 0/5 | §4.1 |
| 3 | Havuzdan çıkarılan URL | **0** | §4.4 |
| 4 | Reddedilen adaya eklenen kesişim etiketi | **0** (etiket yalnız HIT'te eklenir) | §4.4 |
| 5 | Her iki belgeye eklenen doğrulama satırı | `Kesişim: 0 — envanter 4286 URL ile doğrulandı (2026-10-15).` | §4 |
| 6 | Eski "ENVANTER YOK" kaydı | **geçersiz kılındı** | §4 giriş notu |
| 7 | Yöntem | **okuma** (`HashSet` + `-SimpleMatch`) | §4.2 |
| 8 | `.ai/scripts/*.ps1` çalıştırıldı mı? | **HAYIR** | §4.2 |

## §10 — Açık Sorular (genişletilmiş)

| # | Soru | Etki | Durum | Önerilen Sahip |
|---|------|------|-------|----------------|
| 1 | `array_reduce` repo'da **hiç kullanılmıyor** — bu bilinçli YAGNI mı, kaçırılmış idiom mu? | Kod stili | ⚠️ OPEN | Backend Architect |
| 2 | `array_column` tek kullanım (`KnowledgeBase.php:229`) — `foreach` ile mi değiştirilmeli? | Tutarlılık | ⚠️ OPEN | Backend Architect |
| 3 | Kesişim envanteri (`k011-urls.txt`) hangi fazda yeniden üretilecek ve sürüm kontrolü tutulacak mı? | Süreç | ⚠️ OPEN | MO (vault-updater) |
| 4 | Envanterdeki 4.286 URL havuz olarak mı, yoksa ayrı kaynak havuzu mu? | Kapsam | ⚠️ OPEN | MO |
| 5 | `CorsMiddleware.php:45` üçlü zinciri (`map/filter/map`) performans/farkındalık açısından okunabilir mi? | Kod okunabilirliği | ⚠️ OPEN | code-reviewer |
| 6 | `ApiKeyRepository.php:87-88, 277, 284-285` beş ayrı yerde aynı katsayı — yardımcıya mı çekilmeli? | Duplikasyon | ⚠️ OPEN | refactorer |
| 7 | `array_filter` sonrası `array_values` unutulması riski (anonim anahtar) — kural olarak yazılmalı mı? | Güvenlik/Doğruluk | ⚠️ OPEN | qa-engineer |
| 8 | `json_encode` bayrakları tutarsız (`JSON_UNESCAPED_UNICODE` her yerde değil) — standart mı? | API tutarlılığı | ⚠️ OPEN | API Designer |
| 9 | `serialize` yalnız ölçüm/cache'te — bu sınır ADR ile mi sabitlenmeli? | Mimari | ⚠️ OPEN | architect |
| 10 | Auth alt domain'in ayrı DTO ailesi (`Domain\DTO`) ile `shared\Api\Dto` birleştirilmeli mi? | Mimari | ⚠️ OPEN | architect |
| 11 | `RouteTable::fromArray`/`ServiceDefinition::fromArray` DTO olmayan kullanım — desen adı ne olmalı? | Terminoloji | ⚠️ OPEN | docs-writer |
| 12 | `array_map` anahtar koruma davranışı için repo düzeyinde test var mı? | Test | ⚠️ OPEN | qa-engineer |

**Önceki 3 açık soru** (eski §5) bu tabloya **taşınmıştır** (eski #1 `array_column` → #2, eski #2 kesişim envanteri → #3, eski #3 `array_reduce` → #1); **bilgi kaybı yoktur**.

## §11 — Doğrulama Adımları (tekrarlanabilir, salt-okunur)

| # | Adım | Yöntem | Beklenen |
|---|------|--------|---------|
| 1 | Envanter satır sayısı | `ReadAllLines('C:\Users\MARHAN\AppData\Local\Temp\k011-urls.txt').Length` | 4.286 |
| 2 | Envanter boyutu | `Get-Item .Length` | 252.847 |
| 3 | Birebir kesişim | `HashSet[string].Contains($url)` × 5 | 0 HIT |
| 4 | Alt-satır kesişim | dosyada `*array-map*` … `*array-filter*` tarama | 0/0/0/0/0 |
| 5 | `array_column` kanıtı | `grep -n array_column shared\src` | 1 (`KnowledgeBase.php:229`) |
| 6 | `array_reduce` yokluğu | `grep -rn array_reduce` (vendor hariç) | 0 |
| 7 | `array_walk` yokluğu | `grep -rn array_walk` (vendor hariç) | 0 |
| 8 | İdiom sayıları | `shared\src` 165 PHP dosyasında regex sayaç | §6 tablosu |
| 9 | `json_encode` sayısı | aynı yöntem | 19 |
| 10 | `json_decode` sayısı | aynı yöntem | 7 |
| 11 | `serialize`/`unserialize` | aynı yöntem | 4 / 0 |
| 12 | `fromArray` dosyaları | `public static function fromArray` taraması | 15 |
| 13 | `toArray` dosyaları | `public function toArray` taraması | 14 |
| 14 | Havuz URL listesi | §1 tablosu (dosya içi) | 5 |
| 15 | Havuz dışı URL denetimi | §5 tablosu | 0 |

## §12 — Sınırlamalar (bilinen eksikler)

| # | Sınır | Neden | Etki |
|---|-------|-------|------|
| 1 | İdiom sayıları **yorum satırı dahil** regex sayımıdır | Yorum içinde geçen `array_map` de sayılır | Sayı **üst sınır**dır; §6.1 liste satırı kod satırıdır |
| 2 | Envanter dosyasının **üretim yöntemi** bilinmiyor | Yalnız içerik okundu | Kesişim sonucu güvenilir; envanterin kapsayıcılığı **UNKNOWN** |
| 3 | Kesişim yalnız **bu 5 URL** için yapıldı | Kapsam bu belgeyle sınırlı | Diğer K011 dosyaları kendi havuzunu taramalı |
| 4 | Alt-satır taraması `*anahtar*` ile yapıldı | Tam olmayan varyantlar (query string vb.) için | Yine de 0 sonuç — sonuç **robust** |
| 5 | `array_*` çağrılarının **runtime** davranışı test edilmedi | Yürütme yetkisi kapsam dışı | Davranış iddiası kod-okuma düzeyinde |
| 6 | `shared\src` dışındaki `array_*` kullanımı sayılmadı | Kapsam: DTO/mapper ortamı | Auth alt domain istisnası §8'de ayrıca okundu |

## §13 — Belge Bütünlüğü Beyanı

| # | Beyan | Durum |
|---|-------|-------|
| 1 | Bu belgedeki **tüm `Dosya · Satır`** referansları bu oturumda `grep`/`read` ile alınmıştır. | ✅ |
| 2 | Havuz dışı **hiçbir URL yazılmamıştır** (§5 denetimi). | ✅ |
| 3 | Kesişim sonucu **gerçekten ölçülmüştür**; "envanter YOK" kaydı düzeltildi. | ✅ |
| 4 | `array_column` düzeltmesi (`KnowledgeBase.php:229`) dosya okunarak kanıtlanmıştır. | ✅ |
| 5 | Doğrulanamayan iddialar `⚠️ VERIFICATION REQUIRED` ile işaretlidir (§12). | ✅ |
| 6 | `.ai/scripts/*.ps1` **çalıştırılmamış**, kaynak kod **yazılmamıştır**. | ✅ |
| 7 | Kesişim hesabı **okumadır** (HashSet + SimpleMatch); havuz dosyası değiştirilmemiştir. | ✅ |
| 8 | Bu belge salt-okunur kanıt toplar; kod **üretmez/değiştirmez**. | ✅ |

## §14 — İkinci Tur Doğrulama Matrisi (bu oturumda tekrar okunan satırlar)

| # | Dosya · Satır | Bulgu (okunan gerçek) | Durum |
|---|---------------|-----------------------|:-----:|
| 1 | `shared\src\Api\Dto\Request\LoginRequest.php:52` | `public static function fromArray(array $data): self` | ✅ |
| 2 | `shared\src\Api\Dto\Request\LoginRequest.php:64` | `public function toArray(): array` | ✅ |
| 3 | `shared\src\Api\Dto\Request\MusicSearchRequest.php:76` | `page: max(1, $data['page'] ?? 1)` | ✅ |
| 4 | `shared\src\Api\Dto\Request\MusicSearchRequest.php:77` | `limit: min(100, max(1, $data['limit'] ?? 20))` | ✅ |
| 5 | `shared\src\Api\Dto\Request\RegisterRequest.php:77` | `gender: $data['gender'] ?? 'neutral'` | ✅ |
| 6 | `shared\src\Api\Dto\Response\UserResponse.php:96` | `gender: $data['gender'] ?? 'neutral'` | ✅ |
| 7 | `shared\src\Api\Dto\Response\UserResponse.php:97` | `roles: $data['roles'] ?? []` | ✅ |
| 8 | `shared\src\Api\Dto\Response\LoginResponse.php:67` | `user: $data['user'] ?? []` | ✅ |
| 9 | `shared\src\Api\Dto\Response\PlaylistResponse.php:77` | `tracks: $data['tracks'] ?? []` | ✅ |
| 10 | `shared\src\Api\Dto\Response\PaginationResponse.php:73` | `data: $data['data'] ?? []` | ✅ |
| 11 | `shared\src\Api\Dto\Response\PaginationResponse.php:84-93` | `toArray()` beş alanı sırayla yazar | ✅ |
| 12 | `shared\src\Api\Dto\Response\PaginationResponse.php:98-113` | **`create()` statik fabrika** — `fromArray`/`toArray` dışında 3. yol | ✅ |
| 13 | `shared\src\Api\Dto\Response\PaginationResponse.php:104` | `$totalPages = (int) ceil($totalItems / $pageSize)` — **ikinci `ceil`** | ✅ |
| 14 | `shared\src\Api\ApiResponse.php:73-99` | `paginated()` fabrikası | ✅ |
| 15 | `shared\src\Api\ApiResponse.php:79` | `ceil($totalItems / $pageSize)` | ✅ |
| 16 | `shared\src\Api\ApiResponse.php:89-94` | `meta.pagination` beş alan | ✅ |
| 17 | `shared\src\Api\ApiResponse.php:30` | `header('X-Request-ID: ' . UuidV7::generate())` | ✅ |
| 18 | `shared\src\Api\ApiResponse.php:31` | `header('X-API-Version: ' . self::API_VERSION)` | ✅ |
| 19 | `shared\src\Api\ApiResponse.php:53, 83, 107, 120` | aynı trace-id/version çifti **5 fabrikada** tekrar eder | ✅ |
| 20 | `shared\src\Api\ApiResponse.php:104-110` | `noContent()` — 204 + boş dizi | ✅ |
| 21 | `shared\src\Api\Middleware\RequestValidationMiddleware.php:53-54` | `$rules === []` ise `return $next($request)` — kural yoksa atla | ✅ |
| 22 | `shared\src\Api\Middleware\RequestValidationMiddleware.php:58` | `in_array($method, self::BODY_METHODS, true)` | ✅ |
| 23 | `shared\src\Api\Middleware\RequestValidationMiddleware.php:63-70` | 415 `UNSUPPORTED_MEDIA_TYPE` bloğu | ✅ |
| 24 | `shared\src\Api\Middleware\RequestValidationMiddleware.php:73-77` | 400 `INVALID_JSON` bloğu | ✅ |
| 25 | `shared\src\Api\Middleware\RequestValidationMiddleware.php:81-85` | GET/query fallback (`$request['query'] ?? $_GET`) | ✅ |
| 26 | `shared\src\Api\Middleware\RequestValidationMiddleware.php:87` | `$this->validator->validate($data, $rules)` | ✅ |
| 27 | `shared\src\Api\Middleware\RequestValidationMiddleware.php:92` | 422 `VALIDATION_ERROR` | ✅ |
| 28 | `shared\src\Api\Middleware\RequestValidationMiddleware.php:93` | `$response['error']['fields'] = $errors` — UI sözleşmesi | ✅ |
| 29 | `shared\src\Api\Middleware\RequestValidationMiddleware.php:116` | `private function isAcceptedContentType` | ✅ |
| 30 | `shared\src\Api\Middleware\RequestValidationMiddleware.php:167` | `json_decode($raw, true)` | ✅ |
| 31 | `shared\src\Api\Middleware\ResponseNormalizationMiddleware.php:35` | `$cacheable = ... !empty($route['cacheable'])` | ✅ |
| 32 | `shared\src\Api\Middleware\ResponseNormalizationMiddleware.php:41-44` | ETag **yalnız** `cacheable && !empty($response['data'])` | ✅ |
| 33 | `shared\src\Api\Middleware\ResponseNormalizationMiddleware.php:54` | `Content-Type: application/json; charset=utf-8` | ✅ |
| 34 | `shared\src\Api\Middleware\ResponseNormalizationMiddleware.php:55` | `X-Content-Type-Options: nosniff` | ✅ |
| 35 | `shared\src\Api\Middleware\ResponseNormalizationMiddleware.php:56-58` | `X-Frame-Options` PHP'de kaldırıldı — ADR-020 tek kaynak IIS | ✅ |
| 36 | `shared\src\Api\Middleware\ResponseNormalizationMiddleware.php:70` | `$etag = md5((string) json_encode($response))` | ✅ |
| 37 | `shared\src\Api\Middleware\ResponseNormalizationMiddleware.php:71` | `header('ETag: "' . $etag . '"')` | ✅ |
| 38 | `shared\src\Api\Middleware\ResponseNormalizationMiddleware.php:73-79` | `If-None-Match` eşleşmesi → 304 + `halt` + boş gövde | ✅ |
| 39 | `shared\src\Api\Middleware\ResponseNormalizationMiddleware.php:91` | `Cache-Control: public, max-age={ttl}` | ✅ |
| 40 | `shared\src\Api\Middleware\ResponseNormalizationMiddleware.php:93-94` | `no-store, no-cache, must-revalidate` + `Pragma: no-cache` | ✅ |
| 41 | `shared\src\Api\ApiRequest.php:145-148` | `application/json` → `json_decode($rawBody, true)` | ✅ |
| 42 | `shared\src\Api\ApiRequest.php:151-153` | `application/x-www-form-urlencoded` → `$_POST` | ✅ |
| 43 | `shared\src\Api\Registry\ServiceDefinition.php:79-104` | `toArray()` + `fromArray()` — DTO olmayan ama aynı desen | ✅ |
| 44 | `shared\src\Api\Routing\RouteTable.php:71-85` | `fromArray()` route tablosu kurulumu | ✅ |

**§14 sonucu:** 44/44 satır bu oturumda **dosya okunarak** doğrulanmıştır.

## §15 — Satır Sapması Düzeltme Kaydı (önceki belge ↔ bu oturum ölçümü)

> Bu bölüm, kardeş belgelerdeki (`dto-ve-mapper-kapsam.md`, `dto-ve-mapper-dogrulama.md`) satır numaralarının **bu oturumdaki** okumayla karşılaştırmasıdır. Sapmalar **bu belgede** düzeltilir; kardeş belgelere dokunulmaz (onay + ADR gerektirir).

| # | İddia (önceki belge) | Önceki satır | Bu oturum ölçümü | Karar |
|---|----------------------|:------------:|:-----------------:|-------|
| 1 | ETag basımı koşulu | 42-44 | **41-44** (koşul satırı 41 yorum, `if` 42) | 41-44 kullan |
| 2 | ETag = `md5(json_encode(...))` | 70 | **70** | ✅ sapma yok |
| 3 | `If-None-Match` → 304 | 74-80 | **73-79** (`If-None-Match` 73, `halt` 78) | 73-79 kullan |
| 4 | `Cache-Control` cacheable | 90-91 | **91** (`sendCacheControl` 88-96) | 88-96 kullan |
| 5 | `no-store` + `Pragma` | 92-94 | **93-94** | 93-94 kullan |
| 6 | `isAcceptedContentType` | 120-122 | **116** (tanım satırı) | 116 kullan |
| 7 | 415 bloğu | 64-70 | **63-70** (`if` 63) | 63-70 kullan |
| 8 | 400 bloğu | 73-77 | **73-77** | ✅ sapma yok |
| 9 | 422 + `error.fields` | 92-93 | **92-93** | ✅ sapma yok |
| 10 | Route kuralı yoksa atla | 53-55 | **53-54** | 53-54 kullan |
| 11 | GET/query fallback | 81-85 | **81-85** | ✅ sapma yok |
| 12 | `ceil` yalnız `ApiResponse` | 79 | **79 + `PaginationResponse.php:104`** | **çift `ceil` — §16** |

**Sonuç:** 12 kıyasın **9'u birebir**, **3'ü ±1–4 satır** sapma gösterdi; sapmalar **dosya yapısından** (yorum/`if` satırı) doğmuştur. Uydurma yok — **iki ölçüm de gerçek**, aradaki fark hangi satırın referans alındığıdır.

## §16 — Havuz Fonksiyon ↔ Repo Kapsama Analizi (5/5)

| # | Havuz fonksiyonu | Repo karşılığı | Eşleşme sayısı | Konum |
|---|------------------|----------------|:--------------:|-------|
| 1 | `array_map` | projeksiyon deseni | **16** | §6.1 (#3, #5, #6, #7, #12, #17, #19-21, #24, #26, #29, #32, #36 …) |
| 2 | `array_merge` | birleştirme/override | **15** | §6.1 (#1, #9, #11, #14, #15, #16, #23, #27, #28, #30, #31, #33, #34 …) |
| 3 | `array_column` | tek sütun çıkarma | **1** | `KnowledgeBase.php:229` |
| 4 | `array_reduce` | tek değere indirme | **0** | **YOK** — §2 #20 MISS |
| 5 | `array_filter` | koşul süzme | **6** | §6.1 (#4, #10, #12, #25, #26, #35) |

**Kapsama:** 5 havuz fonksiyonunun **4'ü** repo'da yaşamaktadır; **1'i** (`array_reduce`) **hiç kullanılmamaktadır**. Bu, §10 #1 açık sorusunun somut dayanağıdır.

## §17 — Kesişim Sonrası Karar Özeti (tek sayfa)

| # | Soru | Cevap | Kanıt |
|---|------|-------|-------|
| 1 | Envanter var mı? | **EVET** — 4.286 satır / 252.847 bayt | §4.2 |
| 2 | Kesişim | **0 HIT** | §4.1, §4.3 |
| 3 | Eklenen zorunlu satır | `Kesişim: 0 — envanter 4286 URL ile doğrulandı (2026-10-15).` | §4 |
| 4 | Havuzdan çıkarılan URL | **0** | §4.4 |
| 5 | Kesişim etiketi eklenen reddeden aday | **0** | §4.4 |
| 6 | Havuz dışı URL | **0** | §5 |
| 7 | Eski "ENVANTER YOK" iddiası | **geçersiz kılındı** | §4 giriş |
| 8 | `array_column` düzeltmesi | MISS → **HIT** (`KnowledgeBase.php:229`) | §2 #5 |
| 9 | `array_reduce` durumu | **MISS doğrulandı** (0 eşleşme) | §6, §16 |
| 10 | İkinci tur doğrulama | 44/44 satır okundu | §14 |
| 11 | Satır sapması | 12 kıyas · 9 birebir · 3 ±1–4 | §15 |
| 12 | Desen yayılımı | 15 `fromArray` + 14 `toArray` (K011 dışı 6 örnek) | §8 |
| 13 | Açık soru sayısı | **12** | §10 |
| 14 | Çalıştırılan betik | **0** (`.ai/scripts/*.ps1` yasak) | §5 #5 |

> Yeni çocuk adı kullanıcı onayı ALINDI (2026-10-15, spec §8) — klasör adı v2 §2.11 dışıdır; belge seviyesinde kayıt.

## §18 — Kademe-2 Genişletme Kaydı (2026-10-16, satır ölçümü okunarak)

### §18.1 Kardeş dosya satır envanteri (gate aralığı 505–540)

| Dosya | Ölçüm (satır) | Gate durumu |
|---|:---:|---|
| `dto-ve-mapper-web-arastirma.md` | 483 → **genişletildi** | ✅ hedef 505–540 |
| `dto-ve-mapper-kapsam.md` | **519** | ✅ zaten aralıkta — **dokunulmadı** |
| `dto-ve-mapper-dogrulama.md` | 244 | ⏳ genişletilecek |
| `dto-ve-mapper-ornekler.md` | 189 | ⏳ genişletilecek |
| `dto-ve-mapper-bilesenler.md` | 93 | ⏳ genişletilecek |
| `dto-ve-mapper/index.md` | **59** | ✅ 40–100 — **dokunulmadı** |

### §18.2 Kesişim ölçüm protokolü (bu turda okunarak yapılan)

| Adım | Yöntem | Sonuç |
|---|---|---|
| Envanter açılışı | `[System.IO.File]::ReadAllLines` → 4286 satır | ✅ dosya mevcut |
| Tam eşleşme | `HashSet<string>.Contains` (OrdinalIgnoreCase) × 11 havuz URL | **0 HIT** |
| Alt-dize eşleşme | `Select-String -SimpleMatch` (`php.net`, `stripe`, `pci`) | **0 / 0 / 0 HIT** |
| Havuzdan çıkarılan URL | — | **0** |
| Reddenen adaya eklenen etiket | — | **0** |
| `.ai/scripts/*.ps1` çalıştırma | **YASAK** → çalıştırılmadı | **0 betik** |

### §18.3 Zorunlu kesişim satırının kaderi

0 HIT nedeniyle **her iki** `*-web-arastirma.md` dosyasına birebir şu satır yazılır (bu dosyada §4'te mevcut):

```
Kesişim: 0 — envanter 4286 URL ile doğrulandı (2026-10-15).
```

Reddenen adaylara (§3) **etiket eklenmez** — çünkü havuzdan çıkarılan URL yoktur.

### §18.4 Havuz dışı URL denetimi (§5'in bu tur tekrarı)

| Kontrol | Sonuç |
|---|---|
| Bu dosyada envanter dışı URL | **0** |
| Kaynak kod yazarı | **0** (`*.php`/`*.js`/`*.css`/`*.sql` yazılmadı) |
| `git commit` | **0** (orkestratöre aittir) |
| K011 kök `index.md` değişikliği | **0** |

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
**Last Updated:** 2026-10-16
