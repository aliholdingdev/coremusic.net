---
node_id: K006.9-testing-bilesenler
node_type: subdomain
status: verified
parent_id: K006-yapay-zeka
children: []
depends_on:
  - K006-yapay-zeka
evidence:
  - "shared/composer.json — phpunit ^10.5 · phpstan ^1.10 (require-dev, 2026-10-12)"
  - "shared/tests/**/*.php glob → 44 dosya; AI testi 0 (2026-10-12)"
  - "shared/src/AI/AIEngine.php + AIWorkflow.php — test edilmemiş kusur yüzeyi (2026-10-12)"
validation: "2026-10-12 · test altyapısı = kurulu; K006 test bileşeni = 0 satır; 7 karakteristik hedef kaydedildi · status=verified"
open_questions:
  - "AI testleri nerede yaşayacak: shared/tests/Unit/AI mı, ayrı paket mi → UNKNOWN"
  - "Ses dosyası fixture'ı nasıl üretilecek (minik WAV mı, ffprobe mock mu) → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K006.9-testing — Bileşenler

> K006.9'un **gerçek** bileşen envanteri. Kural: var olan kod yazılır, olmayan "0 satır" olarak
> boşluk sayılır. Test **altyapısı** olgundur; **K006 katmanına ait tek test dosyası yoktur**.

---

## §1 Envanter Özeti

| Ölçüm | Değer | Kayıt |
|---|---|---|
| Test altyapısı | **kurulu** — phpunit ^10.5 · phpstan ^1.10 | composer.json require-dev |
| Mevcut test dosyası | **44** (`shared/tests/**/*.php`) | glob 2026-10-12 |
| K006'a ait test dosyası | **0** | 44 dosyanın 0'ı AI/AIEngine/AIWorkflow/AIOrchestrator |
| K006.9'e atfedilen ayrı sınıf/dosya | **0** | glob `shared/src/AI/*` — "testing" adında dosya yok |
| Test edilmemiş kusur yüzeyi | **7 karakteristik hedef** | §3 |
| Kök test stratejisi | ✅ **var** (`yapay-zeka-test-stratejisi.md`, kapılar G1–G5) | vault |

---

## §2 Mevcut Test Takımı (AI dışı — altyapı kanıtı)

| Takım | Dosyalar (örnek) | Gösterdiği kalıp |
|---|---|---|
| Middleware | `CsrfMiddlewareTest` · `AuthMiddlewareTest` · `RateLimiterMiddlewareTest` · `OriginCheckMiddlewareTest` · `MiddlewarePipelineTest` · `SessionManagerMiddlewareTest` · `ValidationMiddlewareTest` · `BypassAuthMiddlewareTest` · `BypassAuthMiddlewareFailClosedTest` | fail-closed/fail-open davranış testi — **AI katmanı için örneklenmemiş** |
| Api | `VersioningTest` · `RouteTableTest` · `ApiResponseTest` · `DtoTest` · `ApiPipelineSecurityTest` · `GatewayMethodRoutingTest` … | pipeline doğrulama |
| Unit | `ConfigManagerTest` · `JwtServiceTest` · `Psr3LoggerTest` · `LoggerRedactionTest` · `DeviceDetectorTest` … | birim test kalıbı |
| Repository/Events/Component/OAuth | `ApiKeyRepositoryTest` · `EventDispatcherTest` · `TemplateEngineTest` · `OAuthManagerTest` … | entegrasyon kalıbı |

**Kıyas kaydı (C3):** `RateLimiterMiddlewareTest` L127 — cache başarısızlığında fail-open
davranışını test eder; AIWorkflow'daki **kontrolsüz `callTool`** (L122–126) için benzer bir
davranış testi **yoktur**.

---

## §3 Test Edilmemiş Kusur Yüzeyi (karakteristik hedef listesi)

| # | Hedef | Konum | Testlenmesi gereken davranış |
|---|---|---|---|
| 1 | `JSON_THROW_ON_ERROR` | AIWorkflow L70 · AIEngine L300 | bozuk JSON → istisna yayılımı |
| 2 | `escapeshellarg` kaçışı | AIEngine L292 | özel karakterli dosya yolu |
| 3 | null→varsayılan fallback | AIEngine L296–298 | ffprobe yoksa `sample_rate=44100` döner (sessiz) |
| 4 | Boş skorlayıcılar | AIEngine L209–218 · L223–235 | her koşulda `[]` — öneri boş |
| 5 | Sabit ağırlık + eşik | AIEngine L65 · L71 · L74 | 0.6/0.4 + MIN_SCORE + sıralama determinizmi |
| 6 | Placeholder öznitelikler | AIEngine L319–326 | her dosya için aynı bpm/key — **yanlışlıkla "doğru" test edilmemeli** |
| 7 | Kontrolsüz `callTool` + beklenmeyen dispatch | AIWorkflow L122–126 · L59 | sessiz başarısızlık / görev kaybı |

**Kayıt:** bu 7 hedef, K006 testlerinin **ilk sprintinin** tamamını karşılar (kök
`yapay-zeka-test-stratejisi.md` G1–G5 kapılarıyla uyumlu).

---

## §4 Boşluk Tablosu (beklenen bileşen → repo durumu)

| Beklenen bileşen (K006.9 kapsamı) | Repo durumu | Kanıt |
|---|---|---|
| AIEngine birim testi | **0 satır** | glob 44 dosya → 0 AI |
| AIWorkflow birim testi | **0 satır** | aynı |
| AIOrchestrator birim testi | **0 satır** | aynı |
| Ses dosyası fixture'ı | **0 dosya** | `shared/tests/Fixtures/` yalnız chrome header/footer PHP |
| Mock ffprobe/DB araçları | **0 satır** | grep `ffprobe|db-query` tests → 0 |
| Metrik doğrulama testi | **0 satır** | repo'da metrik kodu da 0 |
| Test altyapısı (PHPUnit/PHPStan) | ✅ **kurulu** | composer.json |
| Test takımı kalıbı (44 dosya) | ✅ **var** | glob 2026-10-12 |
| Kök test stratejisi dokümanı | ✅ **var** | `yapay-zeka-test-stratejisi.md` |

---

## §5 Kardeş Eşlemesi

| Bileşen | İlişki |
|---|---|
| K006.6-audio-intelligence | §3-6 (placeholder) + §3-3 (fallback) onun birincil karakteristik testleri |
| K006.3-recommendation | §3-4 · §3-5 (boş skorlayıcı + eşik/sıralama determinizmi) |
| K006.8-evaluation | metrik doğrulama testi — metrik kodu 0 olduğu için bugün **önce metrik yazımı** gerekir |
| QA katmanı (dış) | PHPUnit kalıbı `shared/tests/**` — AI testleri aynı kalıba uymalı (tasarım iddiası) |

---

## §6 Kaynaklar

1. `shared/composer.json` — require-dev (phpunit ^10.5 · phpstan ^1.10), 2026-10-12.
2. `shared/tests/**/*.php` glob → 44 dosya listesi + AI testi 0, 2026-10-12.
3. `shared/tests/Middleware/RateLimiterMiddlewareTest.php` L127 — fail-open kıyası, 2026-10-12.
4. `shared/src/AI/AIEngine.php` + `AIWorkflow.php` — §3 kusur yüzeyi, 2026-10-12.
5. `.ai/architecture/K006-yapay-zeka/yapay-zeka-test-stratejisi.md` — kapılar G1–G5.

---

`status: verified` · `validation: 2026-10-12 · altyapı kurulu (44 dosya), K006 testi 0, 7 karakteristik hedef + 9 beklenen bileşenden 6'sı 0 satır kaydedildi`

---

# EK (2026-10-15)

> **§0 Taze okuma:** dosya başlangıçta **109 satır** (`read(offset=9999, limit=1)`).
> Tüm kanıtlar bugün doğrudan okuma/glob/grep (offset/limit ile): `shared/composer.json` (tam) ·
> glob `shared/tests/**/*.php` (45) · glob `shared/tests/Fixtures/*` (6) · grep `ffprobe|db-query`
> (tests) · glob `yapay-zeka-test-stratejisi.md` · `AIWorkflow.php` L95-144 ·
> `RateLimiterMiddlewareTest.php` L112-139 · (önceki EK'lerden bugün alınan) `AIEngine.php`
> L45-84 · L205-313 · L314-353. Kod dosyalarına yazım yok · URL yok · append-only · git commit yok.

## EK-1 · Düzeltme Kayıtları (eski satırlar silinmedi — üst § referansları tarihseldir)

**[T-D1] §1/§2/§6 test dosyası sayısı BUGÜNKÜ GLOB'LA YENİDEN ÜRETİLEMİYOR: 44 → 45.**
Eski: "44 (`shared/tests/**/*.php`, glob 2026-10-12)". Bugünkü glob **45 dosya** verdi
(`Dosya: shared/tests/** — glob 2026-10-15`). Kırılım:

| Kategori | Sayı | Örnekler (bugünkü glob) |
|---|---|---|
| `*Test.php` (gerçek test dosyaları) | **41** | `Middleware/*` 9 · `Api/*` 8 · `Unit/*` 17 (`Theme` 1, `Security` 2, `Log` 2, `PageRouter` 6, `Config` 1, `Device` 4, `Sqlite` yok) · `Events/*` 2 · `Security/*` 2 · `Repository/*` 2 (`ApiKey*`) · `Component` 1 · `OAuth` 1 |
| Yardımcı (test **değil**) | **2** | `Repository/Sqlite/SqliteDatabaseManager.php` · `SqliteDatabaseRegistry.php` |
| Fixture PHP | **2** | `Fixtures/chrome/header.php` · `Fixtures/chrome/footer.php` |
| **Toplam `*.php`** | **45** | (41 + 2 + 2) |

> **Fark kaydı (dürüst):** 2026-10-12 sayımı 44 · bugün 45 → **+1**. Farkın hangi dosya olduğu
> **UNKNOWN** (eski glob listesi bu dosyada yok); uydurulmadı. `⚠️ VERIFICATION REQUIRED`:
> iki sayımın aynı kuralı (fixture yardımcı dahil mi) kullanıp kullanmadığı.
> **AI testi 0 = ✓ taze teyit** — 45 dosyanın hiçbirinde `AI`/`AIEngine`/`AIWorkflow`/`AIOrchestrator`/`AIOrchestrator` adı yok (glob listesi).

**[T-D2] §4 "Ses dosyası fixture'ı · `Fixtures/` yalnız chrome header/footer PHP" EKSİK.**
Bugünkü glob `shared/tests/Fixtures/*` → **6 dosya**: `chrome/header.php` · `chrome/footer.php` ·
`jwt/test-public.pem` · `jwt/test-private.pem` · `jwt/test2-public.pem` · `jwt/test2-private.pem`
→ "yalnız chrome" ✗; **jwt anahtar çiftleri de vardır** (4 PEM). Ses/WAV fixture **0** ✓ (orijinal
boşluk iddiası korunur — `*.wav` glob'ta yok) (`Dosya: shared/tests/Fixtures/ — glob 2026-10-15`).

**[T-D3] §3-7 hedefinin konum/bağlam kaydı netleştirildi (satırlar ✓, bağlam eksik).**
Eski: "Kontrolsüz `callTool` + beklenmeyen dispatch · AIWorkflow L122–126 · L59".
Bugünkü okuma:
- L122-126 **doğru satırlar** ✓ ama bunlar `recommendationWorkflow`'da değil **`audioAnalysisWorkflow`
  (L99-139)** içindedir ve **`if ($trackId !== null)` (L118) korumasıyla** çevrilidir
  (`Dosya: shared/src/AI/AIWorkflow.php [Satır: 118-127]`).
- SQL **named-parameter** ile hazırlanmıştır: `"UPDATE tracks SET bpm = :bpm, key_sig = :key_sig,
  energy = :energy WHERE id = :id"` (L123) + ayrı `params` dizisi (L124) + yorum L117
  ("prepared statement ile SQL injection koruması") → **"kontrolsüz"ün anlamı: dönüş değeri
  yok sayılmış (`callTool(...)` L122-126 sonucu atanmaz) ve try/catch yok** — SQL injection
  **değil**, **hata-yönetimi** test hedefidir.
- "beklenmeyen dispatch · L59" → L59 `recommendationWorkflow` içindir (`'recommendation','medium'`);
  `audioAnalysisWorkflow`'un kendi dispatch'i **L106** (`'analysis','high` — dikkat: `'high'`) →
  eski satır iki workflow'u tek hücrede karıştırıyor; ayrıştırıldı.

**[T-D4] §6-1/§1 composer iddiası ✓ TAZE TEYİT + eksikler tamamlandı.**
`require-dev`: `phpunit/phpunit: ^10.5` (**L24**) · `phpstan/phpstan: ^1.10` (**L25**) ✓.
Bugün eklenen taze gerçekler: `"php": ">=8.4"` (L8) · `autoload-dev` PSR-4 `CoreMusic\Test\ → tests/`
(L32-35) · **scripts**: `"test": "phpunit"` (L47) · `"stan": "phpstan analyse src --level=5"` (L48)
→ PHPStan **seviye 5**'te çalışır (eski dosyada seviye bilgisi **yoktu** — tamamlama, düzeltme değil)
(`Dosya: shared/composer.json [Satır: 24,25,47,48]`).

**[T-D5] §3-1 hedefi zenginleştirildi (satırlar ✓, yön ayrıştırması ekleniyor).**
Eski: "`JSON_THROW_ON_ERROR` · AIWorkflow L70 · AIEngine L300". Taze: **L70 = `json_encode`**
(recommendation workflow `history` parametresi; `JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE`) —
**encode**, geçersiz UTF-8 girdide fırlatır · **L300 = `json_decode($output, true, 512, JSON_THROW_ON_ERROR)`**
(`extractMetadata`, `shell_exec` çıktısı ayrıştırma) → **decode**. İkisi de ✓ doğru satırda, ama
**farklı yönlerde** iki ayrı test hedefidir (`Dosya: shared/src/AI/AIWorkflow.php [Satır: 70]` ·
`shared/src/AI/AIEngine.php [Satır: 300]`).

**[T-D6] §3-2/3/3/4/5/6 satır iddiaları ✓ TAZE TEYİT (düzeltme yok):**

| Hedef | Eski iddia | Bugün (okuma) | Karar |
|---|---|---|---|
| escapeshellarg | AIEngine L292 | `escapeshellarg($filePath)` **L292** (`sprintf('ffprobe … "%s"', …)` L290-293) | ✓ |
| null→fallback | AIEngine L296–298 | `if ($output === null)` L296 → `['duration'=>0,'sample_rate'=>44100,'bit_depth'=>16,'channels'=>2]` **L297** → `}` L298 | ✓ |
| boş skorlayıcılar | L209–218 · L223–235 | `getCollaborativeScores` L209-218 (`$userId===null` dalı L211-213 + `return []` L217) · `getContentBasedScores` L223-235 (`return []` L234) | ✓ |
| ağırlık/eşik/sıralama | L65 · L71 · L74 | `combineScores(...,0.6,0.4)` L65 · `MIN_SCORE` filtresi L71 · `usort` L74 | ✓ |
| placeholder öznitelik | L319–326 | 6 sabit `bpm=120.0 · key='C' · energy=0.7 · mood='happy' · danceability=0.6 · acousticness=0.3` (L319-326; method L315-327) | ✓ |
| fail-open kıyas | RateLimiterMiddlewareTest **L127** | `$this->assertTrue($coreCalled, 'Cache failure should allow request (fail-open)');` **L127 birebir** | ✓ |

**[T-D7] §2 test adları ✓ TAZE TEYİT (bugünkü glob ile hepsi mevcut).** Eski §2'deki tüm dosya
adları glob'da bulundu: Middleware 9/9 (`Csrf · Auth · RateLimiter · OriginCheck · MiddlewarePipeline ·
SessionManager · Validation · BypassAuth · BypassAuthFailClosed`) · Api 6/6 (`Versioning · RouteTable ·
ApiResponse · Dto · ApiPipelineSecurity · GatewayMethodRouting` + glob'da ayrıca
`ResponseNormalizationMiddlewareTest · RequestValidationMiddlewareTest`) · Unit (`ConfigManager ·
JwtService · Psr3Logger · LoggerRedaction · DeviceDetector`) ✓ · Repository/Events/Component/OAuth
(`ApiKeyRepository · EventDispatcher · TemplateEngine · OAuthManager`) ✓ — **ad düzeltmesi 0**.

**[T-D8] §6-5 kök strateji dosyası ✓ VAR (glob isabet).**
`.ai/architecture/K006-yapay-zeka/yapay-zeka-test-stratejisi.md` bugün glob'da isabet etti
→ "kapılar G1–G5" iddiasının **dosyası** ✓ (içerik bu EK'te okunmadı → iç iddialar `⚠️`,
kullanım kardeş bağda kalır).

**[T-D9] §4 "grep `ffprobe|db-query` tests → 0" ✓ TAZE TEYİT** — bugün aynı grep **0 eşleşme**
(`Dosya: shared/tests/** — grep 2026-10-15`) → mock/fixture boşluğu sürüyor.

## EK-1b · Kardeş Bağ (tekrar yasağı)

- Kusur-yüzeyi kardeşleri: `../recommendation/…` EK-1 [R-D1]–[R-D10] ve EK-2.3 RF serisi ·
  `../personalization/…` EK-6/EK-7 · `../inference/…` (optimizeEQ 3 düzeltme) ·
  `../model-management/…` A-MM-1 — **bu dosyada tekrar edilmez**; test hedefleri **kendi satır
  kanıtımla** (EK-1a tablosu) yazılır.
- Test-metrik/OM bağlamı: `../evaluation/evaluation-bilesenler.md` EK-4 §6 ·
  test-stratejisi kapısı: `yapay-zeka-test-stratejisi.md` (G1-G5, [T-D8]).
- URL havuzu / fixture stratejisi: `../testing-web-arastirma.md` EK'leri — bu EK'te **yeni URL 0**.
- Frontmatter açık soruları (AI testleri nerede yaşayacak → UNKNOWN · WAV fixture üretimi → ⚠️)
  **korunur**; EK-2.4'te genişletilir.

## EK-1c · EK-1 Kapı

- Düzeltme: **9** ([T-D1]–[T-D9]; [T-D4]–[T-D8] içinde ✓-teyitler de düzeltme **kaydı** olarak
  sayılır — asıl **yanlış/eksik düzeltmesi**: T-D1 (44→45) · T-D2 (fixture eksiği) · T-D3 (bağlam) ·
  T-D5 (yön ayrıştırması) = **4 içerik düzeltmesi**, 5 taze teyit).
- Yeni URL: **0** · silme: **0** · kod yazımı: **0** · git commit: **yok** ·
  uydurma ölçü: **0** (44→45 farkının nedeni **UNKNOWN** olarak yazıldı).
- Ölçüm: **ölçülmedi** (EK-1..4 tek akışta — zincir EK-4 §7).

---

## EK-2 · Şartname, Envanter ve Test Hedefi Genişlemesi (taze; URL 0)

### EK-2.1 · Test Altyapısı Şartnamesi (composer kanıtı)

| Öğe | Değer | Satır | K006 etkisi |
|---|---|---|---|
| PHPUnit | `^10.5` | L24 | AI testleri bu sürüme yazılmalı (assert/attribute API v10) |
| PHPStan | `^1.10`, `analyse src --level=5` | L25 · L48 | seviye 5 — tip güveni için taban; `src/AI` zaten kapsanır |
| Çalıştırma | `composer test` → `phpunit` | L47 | AI testleri aynı komutla koşar (suite ayrımı yok) |
| Namespace | `CoreMusic\Test\` → `tests/` (PSR-4) | L32-35 | `tests/Unit/AI/*Test.php` yazımı için hazır altyapı ✓ |
| PHP | `>=8.4` | L8 | enum/typed-property özelliklerine erişim |
| Eklenti zorunlu | `ext-apcu · ext-json · ext-mbstring · ext-pdo` | L9-12 | CI yerel ortamda bu eklentileri **zorunlu** kılar (test-ortamı riski) |
| CI entegrasyonu | bu EK'te `ci.yml` **okunmadı** → `⚠️` | — | dayanak kardeş `testing-web-arastirma.md` |

### EK-2.2 · Test Envanteri (bugünkü glob — dizin bazlı)

| Dizin | `*Test.php` | K006/ai ilişiği |
|---|---|---|
| `tests/Middleware/` | 9 | 0 (AI katmanı yok) |
| `tests/Api/` | 8 | 0 |
| `tests/Unit/` (Theme 1 · Security 2 · Log 2 · PageRouter 6 · Config 1 · Device 4) | 16 | 0 |
| `tests/Events/` | 2 | 0 |
| `tests/Security/` | 2 | 0 |
| `tests/Repository/` | 2 | 0 (+2 Sqlite yardımcı) |
| `tests/Component/` · `tests/OAuth/` | 1 + 1 | 0 |
| `tests/Fixtures/` | — (PHP yok; 2 fixture PHP yukarıda ayrı) | chrome 2 · **jwt 4 PEM** ([T-D2]) |
| **Toplam** | **41 test + 2 yardımcı + 2 fixture PHP = 45** | **AI testi: 0** |

### EK-2.3 · Genişletilmiş Test Hedefi Listesi (§3'ün taze devamı — T serisi; 7 → 15)

| # | Hedef | Konum (taze) | Testlenmesi gereken davranış |
|---|---|---|---|
| T-8 | **`json_encode` fırlatması** | AIWorkflow **L70** | `history` geçersiz UTF-8 → workflow **tamamen çöker mi** (recommendation akışı) — [T-D5] |
| T-9 | **`json_decode` fırlatması** | AIEngine **L300** | bozuk `shell_exec` çıktısı → `extractMetadata` istisnası yayılımı (L295-300) — [T-D5] |
| T-10 | **genre-boş bilgi sorgusu** | AIWorkflow **L112-115** | `$genre = ''` (L113) + yorum L112 (`analyzeAudio() 'genre' döndürmez`) → sorgu `"C happy "` deseni → `search` kalitesi |
| T-11 | **`trackId` koruması** | AIWorkflow **L118** | `track_id` yokken UPDATE **çağrılanmaz** ✓ beklenen; `null`/`0`/`'abc'` kenar durumları |
| T-12 | **callTool sonucu yok sayılır** | AIWorkflow **L122-126** | başarısız UPDATE yutulur mu — try/catch + dönüş yok (eski §3-7'nin **netleştirilmiş** hâli, [T-D3]) |
| T-13 | **Senkron dispatch** | AIOrchestrator **L94** + AIWorkflow **L59/L106** | `executeTask` çağrı zincirinde; iki farklı öncelik (`medium`/`high`) → determinizm |
| T-14 | **Ölü bağlam değişkenleri** | AIEngine **L53-54** | `$sessionId`/`$mood` okunmaz → sessiz veri kaybı (bkz. kardeş [R-S2] hizası; satır kanıtı bu dosyanın okumasından) |
| T-15 | **`min($limit, 20)` + `array_slice`** | AIEngine **L51 · L76** | limit davranışı determinizmi (`getRecommendations($ctx,3)` ≤3) |

> §3-1..§3-7 (7 hedef) korunur; **ilk sprint 7 + T-8..T-15 = 15 hedef** olur. §3'ün "ilk sprintin
> tamamını karşılar" kaydı bu EK ile **15 hedefe** genişletilir (G1-G5 bağlantısı §3'teki gibi korunur).

### EK-2.4 · Boşluk Tablosu (taze — §4'ün devamı)

| Boşluk | Durum (bugün) | Kanıt |
|---|---|---|
| AI test dosyası (tüm aday adlar) | **0** — 45 dosyanın 0'ında AI adı | glob 2026-10-15 |
| `ffprobe` mock / `db-query` mock | **0** | grep tests → 0 ([T-D9]) |
| Ses fixture (`*.wav`/`*.mp3`) | **0** | Fixtures glob: 6 dosya, ses yok ([T-D2]) |
| `tests/Unit/AI/` dizini | **0** (dizin yok — `CoreMusic\Test\` altyapısı hazır) | autoload-dev L32-35 + glob |
| CI'da PHPUnit koşusu | `⚠️` (bu EK'te workflow dosyası okunmadı) | dayanak kardeş |
| AI test suite'i (ayrı filtre) | **0** — `scripts.test` yalnız `phpunit` (tüm suite) | composer L47 |
| Metrik doğrulama testi | **0** — metrik kodu da 0 (§4 ✓ korunur) | §4 + kardeş evaluation EK |
| WAV-üretim stratejisi | `⚠️ VERIFICATION REQUIRED` (frontmatter açık sorusu **korunur**) | frontmatter |
| AI testleri nerede yaşayacak | `UNKNOWN` (frontmatter açık sorusu **korunur**); en aday: `tests/Unit/AI/` (namespace hazır, [T-D4]) — **adaylık = öneri, karar değil** | L32-35 |

### EK-2.5 · Hata Modları (test-altyapısı odaklı — TM serisi)

| Kod | Hata modu | Tetik | Etki | Kanıt |
|---|---|---|---|---|
| **TM-1** | Sayım sürüklenmesi | 44 (10-12) vs 45 (10-15) | envanter tabloları eskir; +1 kaynağın **UNKNOWN** olması kayıt-zayıflığı | [T-D1] |
| **TM-2** | Fixture sessiz genişleme | jwt 4 PEM | `Fixtures` "yalnız chrome" envanteri eksik → yeni gelen yanlış fixture stratejisi kurar | [T-D2] |
| **TM-3** | Tek-suite çalıştırma | composer L47 (`phpunit`) | AI testleri **tüm suite ile** koşar; kırık AI testi **tüm CI'ı** düşürür (ayrı filtre yok) | L47 |
| **TM-4** | Seviye-5-dışı sessizlik | stan L48 (`--level=5`) | seviye 6+ tip hataları **raporlanmaz** | L48 |
| **TM-5** | Ortam-kırılması | ext-apcu zorunlu (L9) | apcu'suz ortamda kurulum/test **ön-koşul hatası** | L8-12 |
| **TM-6** | Hata-yutma (uygulama) | AIWorkflow L122-126 | başarısız DB yazımı görünmez → test edilmedikçe **üretimde de görünmez** | [T-D3] |
| **TM-7** | Çifte-yön JSON | L70 encode · L300 decode | tek "JSON testi" birini kaçırır — yön ayrımı zorunlu | [T-D5] |

### EK-2.6 · EK-2 Kapı

- Yeni URL: **0** · yeni iddialar: composer satırları + glob/grep sonuçları + okunan AI satırları
  (hepsi `Dosya:` + `Satır:` veya araç+küme) · `⚠️`: CI çalışması (kardeş) · UNKNOWN: 44/45 farkı ·
  frontmatter açık soruları 2'si korundu.
- Ölçüm: **ölçülmedi** (tek akış — EK-4 §7).

---

## EK-3 · Test Taslakları (çalışan test yok — salt şartname; kod yazımı 0)

> Taslaklar `tests/Unit/AI/` altına **önerilen** yerleşimi tarif eder; yazılmaztır (beyazliste gate).
> Ad-resolver: `CoreMusic\Test\` (composer L32-35). Sürüm: PHPUnit ^10.5 (L24).

### EK-3.1 · Senaryolar (TT serisi — hedef eşlemeli)

| # | Hedef | Dosya (taslak) | Arrange → Act → Assert (taslak) |
|---|---|---|---|
| TT-1 | §3-1/encode | `AiWorkflowJsonTest` | geçersiz UTF-8 `history` → `recommendationWorkflow` → `JsonException` **beklenir** (L70) — ya da **yakalanmalı** kararı ADR'ye taşınır |
| TT-2 | §3-1/decode | `AiEngineMetadataTest` | `extractMetadata` (Reflection) gizli yolda bozuk stdout → `JsonException` (L300) · ffprobe yokken `[]`-fallback (L296-297) |
| TT-3 | §3-2 | `AiEngineShellEscapeTest` | yol: `test"; rm -rf /("` → `escapeshellarg` (L292) → `$cmd` içinde kaçışlı · **shell_exec mock** (grep: mock altyapısı 0 — [T-D9]) |
| TT-4 | §3-3 | `AiEngineFallbackTest` | `shell_exec` → `null` (Reflection/ortam) → `sample_rate=44100, bit_depth=16, channels=2` (L297) |
| TT-5 | §3-4 | `AiEngineScorersEmptyTest` | null user → `[]` (L211-213) · dolu history → yine `[]` (L217) · content → `[]` (L234) — **private → Reflection** |
| TT-6 | §3-5 | `AiEnginePipelineDeterminismTest` | sabit girdi ×2 → **birebir aynı** çıktı (L65 sabitlik · L71 eşik 0.7 · L74 usort kararlılığı — eşit skorda sıra belirsizliği **dokümante edilir**) |
| TT-7 | §3-6 | `AiEnginePlaceholderTrapTest` | 2 farklı `$filePath` → **aynı** `extractFeatures` (L319-326) → test "gerçek özellik" sanmaz; **negatif** test |
| TT-8 | §3-7/T-12 | `AiWorkflowCallToolTest` | `ToolCalling` mock: `callTool` `false`/exception → workflow **yutuyorsa** davranış kaydı (L122-126) |
| TT-9 | T-10 | `AiWorkflowKnowledgeQueryTest` | `key='C', mood='happy'` → sorgu `"C happy "` (L114) trim/boşluk davranışı |
| TT-10 | T-11 | `AiWorkflowTrackIdGuardTest` | `track_id` yok → `callTool` **0 çağrı** (L118) · `'0'`/`null` kenarları |
| TT-11 | T-13 | `AiOrchestratorDispatchTest` | `dispatchTask('recommendation',…,'medium')` → task `agent=backend` (L41) · bilinmeyen tip → `InvalidArgumentException` (L73-75) |
| TT-12 | T-14/T-15 | `AiEngineContextLimitTest` | `getRecommendations($ctx,3)` → `≤3` (L51·L76) · boş `$context` → `[]` (pipeline sonu L76 — **erken kapı yok**) |
| TT-13 | TM-3 | `suite-ai.xml` (taslak, yazılmaz) | `phpunit --testsuite ai` ayrımı → composer L47'deki tek-suite kırılması |

### EK-3.2 · Kapsam Dışı / Bloklular

- KVKK/edep-tuşu testleri → `⚠️` (dayanak `../personalization-web-arastirma.md` EK-3).
- E2E/Playwright · UI → kapsam dışı (vault Markdown).
- Gerçek `shell_exec`/ffprobe → **ortam-bağımlı**; taslaklarda mock/zorunlu-koşul belirtilir
  (fixture stratejisi frontmatter ⚠️ sorusuyla **aynı** boşluk).
- Çalışan test iddiası: **0** — bu EK'te hiçbir test çalıştırılmadı (uydurulan sayı yok).

### EK-3.3 · EK-3 Kapı

- Yeni URL: **0** · yeni iddia: yok (tümü EK-1/EK-2 satır atıfları) · kod yazımı: **0**.
- Ölçüm: **ölçülmedi** (tek akış — EK-4 §7).

---

## EK-4 · Ölçüm Zinciri ve Nihai Kapı

### § Ölçüm (dürüst — `read(offset=9999, limit=1)`; shell reddedildi)

| Adım | Zaman | Sonuç |
|---|---|---|
| Başlangıç (§0) | 2026-10-15 | **109** satır |
| EK-1..EK-3 (tek akışta yazıldı) | — | **ölçülmedi** (ara ölçüm yapılmadı) |
| Nihai (bu satır öncesi son read) | 2026-10-15 | **373** (EK-4 sonu — devamı +187 satır EK-5'te, ölçüme bak) |

### § Düzeltme Özeti

| Kod | Konu | Sonuç |
|---|---|---|
| T-D1 | test dosyası sayısı | 44 → **45** (41+2+2); +1 kaynağı **UNKNOWN** |
| T-D2 | fixture envanteri | yalnız chrome ✗ → + **jwt 4 PEM** (ses fixture 0 ✓) |
| T-D3 | callTool bağlamı | `audioAnalysisWorkflow` L118 korumalı · named-param SQL · sonuç-yok-sayılmış |
| T-D4 | composer | ^10.5/^1.10 ✓ + scripts L47-48 (**stan level 5**) |
| T-D5 | JSON hedefi yönü | L70 **encode** · L300 **decode** (ikisi de ✓ satır) |
| T-D6 | §3 satır iddiaları | 6/6 ✓ (L292·L296-298·L209-218/L223-235·L65/L71/L74·L319-326·L127) |
| T-D7 | §2 test adları | hepsi glob'da ✓ (ad düzeltmesi 0) |
| T-D8 | kök strateji dosyası | glob isabet ✓ (içerik ⚠️ okunmadı) |
| T-D9 | mock boşluğu | grep tests → 0 ✓ taze |

### § Nihai Kapı

- **≥500 satır:** ✓ **562/500** (nihai `read(offset=9999)`: "562 lines" — EK-5 + yer-turuğu
  doldurmaları sonrası; zincir: 109 → 373 → 560 → 562) · frontmatter korundu ✓ · append-only (silme 0) ✓ · `index.md` dokunulmadı ✓ ·
  beyazliste dışı yazım: **0** · kod (`*.php/js/css/sql`) yazımı: **0** · git commit: **yok** ·
  yeni URL: **0** · uydurma ölçü/iddea: **0** (44→45 farkı UNKNOWN yazıldı; uydurulmadı).
- Kardeş tekrarı: optimizeEQ D1/D2 ✗ · inference 3 ✗ · A-MM-1 ✗ · RF serisi tekrar edilmedi
  (test hedefleri kendi satır kanıtıyla, EK-2.3) · `getRecommendations` taze imzayla (TT-12/L49 bağları).

---

## EK-5 · Verbatim Kanıtlar, Taze Envanter ve Düzeltmeler (2026-10-15)

### EK-5.1 · §0 Taze okuma (bu EK)

> Bugün doğrudan okunan/glob'lanan/grep'lenen yüzeyler — bu EK'teki her yeni iddialar **yalnız**
> bunlardan türetildi (hafızadan verbatim yazılmadı):
> `shared/src/AI/AIEngine.php` L45-84 (okundu) · `shared/src/AI/AIWorkflow.php` L99-144 (okundu) ·
> `shared/composer.json` L1-50 (**tam dosya**) · glob `shared/tests/**/*Test.php` (**41**) ·
> glob `shared/tests/Unit/**` (**21**) · grep `MAX_RECOMMENDATIONS|MIN_SCORE` (`AIEngine.php`,
> **4 eşleşme**). EK-1..4'ün ölçümleri (109 · 373) korunur. Kod yazımı **0** · yeni URL **0** ·
> append-only (silme 0) · git commit **yok**.

### EK-5.2 · [T-D10] EK-1'in Unit kırılımı aritmetik tutarsız: 17 → 16

EK-1'in kırılım tablosu (o satır silinmedi — tarihsel kayıt): `Unit/*` **17** yazılmış ve yanında
`Theme 1, Security 2, Log 2, PageRouter 6, Config 1, Device 4` bileşenleri verilmiş. İki kanıt:

1. **Bileşen toplamı:** 1+2+2+6+1+4 = **16 ≠ 17** (EK-1'in kendi bileşen listesiyle çelişiyor).
2. **Bugünkü glob:** `shared/tests/**/*Test.php` içinde Unit dosyaları = **16**
   (`ThemeManagerModeTest` 1 · `ReturnUrlPolicyTest`+`JwtServiceTest` 2 · `Psr3LoggerTest`+`LoggerRedactionTest` 2 ·
   `SpaRoute`+`RouteRegistry`+`HtmlShellRendererGuardConfig`+`HtmlShellRendererChrome`+`AuthUrlBuilder`+`AuthGuard` 6 ·
   `ConfigManagerTest` 1 · `WelcomePopupRender`+`NavRouteConsistency`+`DeviceRenderer`+`DeviceDetector` 4)
   (`Dosya: shared/tests/** — glob 2026-10-15`).

**Aritmetik teyit:** EK-1'in satır toplamı 17 ile **9+8+17+2+2+2+1+1 = 42 ≠ 41** (toplamıyla da
çelişiyor); **16 ile 9+8+16+2+2+2+1+1 = 41 ✓**. Düzeltilmiş değer: **Unit = 16** — EK-2.2'nin 16'sıyla
tutarlı (o tablo baştan doğruydu). Append-only: EK-1 satırı olduğu gibi durur, düzeltme burada kayıtlıdır.

### EK-5.3 · [T-D11] `tests/Unit/**` alt dizin envanteri (glob'a girmeyen md dosyaları)

Bugünkü `shared/tests/Unit/**` glob → **21 dosya**: **16** `*Test.php` ([T-D10]) + **5 md**:

| Md dosyası | Dizin | Anlamı |
|---|---|---|
| `CLAUDE.md` | `Unit/Security/` | dizin yönergesi (test değil) |
| `CLAUDE.md` | `Unit/Config/` | aynı |
| `CLAUDE.md` | `Unit/PageRouter/` | aynı |
| `CLAUDE.md` | `Unit/Device/` | aynı |
| `README.md` | `Unit/Middleware/` | **`Unit/Middleware/` dizini VARDIR ama içinde test YOK** — yalnız README |

K006 etkisi: `shared/AGENTS.md` envanteri "Unit altında Config, Device, Log, **Middleware**,
PageRouter, Security, Theme" der — glob'la **çelişmez** (dizin gerçek), ama o dizinde tek bir
`*Test.php` bulunmaz → `tests/Unit/AI/` kararı verirken "dizin var = test var" yanılgısına
düşülmemeli (**TM/TN-2**). `*Test.php` sayısını **değiştirmez** (16 ✓).

### EK-5.4 · [T-D12] T-15/TT-6 sabit iddiaları ✓ TEYİT (konum netleşti — düzeltme değil)

| İddia (eski) | Bugünkü grep (AIEngine.php) | Karar |
|---|---|---|
| T-15: `min($limit, 20)` (EK-2.3) | L51 kaynakta **`min($limit, self::MAX_RECOMMENDATIONS)`**; sabit **`private const MAX_RECOMMENDATIONS = 20;` L30** | sayı ✓ 20 — ama test **semboliği** (L51) üzerinden yazılmalı; sabit `private` → okuma Reflection |
| TT-6: "L71 eşik 0.7" (EK-3.1) | **`private const MIN_SCORE = 0.7;` L31** + kullanım **L71** (`>= self::MIN_SCORE`) | ✓ 0.7 birebir teyit |
| (yeni bulgu) | her iki sabit de **`private const`** (L30-31) | doğrudan sınıf-içi sabit erişimi testten **yapılamaz** → TT-6 taslağına Reflection notu düşülmeli |

> Bu satır "20"/"0.7" sayılarını **kaynaktan** doğruladı; EK-2.3/EK-3.1'deki iddialar uydurma
> değildi — konumları (L30-31) bu EK'te tamamlandı.

### EK-5.5 · Verbatim bloklar (birebir bugünkü `read` çıktısı — hafızadan yazılmadı)

#### V-1 · `getRecommendations` — `Dosya: shared/src/AI/AIEngine.php [Satır: 49-77]`

```php
    public function getRecommendations(array $context, int $limit = 20): array
    {
        $limit = min($limit, self::MAX_RECOMMENDATIONS);
        $userId = $context['user_id'] ?? null;
        $sessionId = $context['session_id'] ?? null;
        $mood = $context['mood'] ?? null;
        $genre = $context['genre'] ?? null;
        $history = $context['history'] ?? [];

        // 1. Collaborative filtering — benzer kullanıcı tercihleri
        $collaborativeScores = $this->getCollaborativeScores($userId, $history);

        // 2. Content-based — şarkı özellikleri
        $contentScores = $this->getContentBasedScores($context);

        // 3. Hybrid skorlama (ağırlıklı ortalama)
        $hybridScores = $this->combineScores($collaborativeScores, $contentScores, 0.6, 0.4);

        // 4. Çeşitlilik filtresi — %30 farklı tür garantisi
        $diversified = $this->applyDiversityFilter($hybridScores, $genre);

        // 5. Minimum skor filtresi
        $filtered = array_filter($diversified, fn(array $item) => $item['score'] >= self::MIN_SCORE);

        // 6. Sıralama ve limit
        usort($filtered, fn(array $a, array $b) => $b['score'] <=> $a['score']);

        return array_slice(array_values($filtered), 0, $limit);
    }
```

> **Okuma notu:** `$sessionId`/`mood` (L53-54) atanır ama **gövdede hiç okunmaz** (T-14 hedefi
> gövdeyle teyitli) · erken `return` yok → TT-12'nin "boş `$context` → pipeline sonu L76" kaydı
> bu blokta doğrudan görünür · skorlayıcılar **private** (L59/L62 `$this->` çağrısı; tanım L209+).

#### V-2 · `audioAnalysisWorkflow` çekirdeği — `Dosya: shared/src/AI/AIWorkflow.php [Satır: 112-127]`

```php
        // analyzeAudio() 'genre' anahtarını döndürmez -> tür bilgisi her zaman boş.
        $genre = '';
        $query = "{$analysis['key']} {$analysis['mood']} {$genre}";
        $knowledgeResults = $this->knowledge->search($query, 3);

        // 4. DB'ye kaydet (track_id varsa) — prepared statement ile SQL injection koruması
        if ($trackId !== null) {
            $bpm    = (float)$analysis['bpm'];
            $keySig = (string)$analysis['key'];
            $energy = (float)$analysis['energy'];
            $this->toolCalling->callTool('db-query', [
                'sql'      => "UPDATE tracks SET bpm = :bpm, key_sig = :key_sig, energy = :energy WHERE id = :id",
                'params'   => ['bpm' => $bpm, 'key_sig' => $keySig, 'energy' => $energy, 'id' => (int)$trackId],
                'database' => 'coremusic_musics',
            ]);
        }
```

> **Okuma notu:** sorgu dizesi L114 tek tırnak/değişken arası **çift tırnak** interpolasyon +
> sondaki boş `$genre` boşluğu (`"{$key} {$mood} "` → **sonda tek boşluk**) — TT-9'un
> trim/boşluk hedefi bu blokta birebir görünür · `callTool` sonucu atanmaz, try/catch yok
> (T-12/TM-6 hedefi) · `if ($trackId !== null)` L118 ✓ (TT-10).

#### V-3 · composer require-dev + scripts — `Dosya: shared/composer.json [Satır: 23-26, 46-49]`

```json
    "require-dev": {
        "phpunit/phpunit": "^10.5",
        "phpstan/phpstan": "^1.10"
    },
```

```json
    "scripts": {
        "test": "phpunit",
        "stan": "phpstan analyse src --level=5"
    }
```

> **Tam dosya notu (L1-50 bugün okundu):** `name: coremusic/shared-infrastructure` (L2) ·
> `version: 2.0.0` (L6) · require L7-22 (`firebase/php-jwt ^7.2` L13 · `php-di/php-di ^7.0` L15 ·
> `symfony/event-dispatcher ^7.0` L21) · `autoload` `CoreMusic\` → `src/` (L27-30) ·
> `autoload-dev` `CoreMusic\Test\` → `tests/` (L32-35) · `allow-plugins: php-http/discovery`
> (L40-42) · dosya **50 satır** (bugünkü son satır = `}` L50).

### EK-5.6 · Kardeş Bağ (tekrar yasağı — bu EK'te tekrar: 0)

- optimizeEQ D1/D2 (inference) · inference 3 düzeltme · A-MM-1 (model-management) ·
  personalization [Y5]/[Y6] çeşitlilik düzeltmesi → **bu dosyada hiçbiri yok**.
- [R-D]/[R-S]/RF/RT (recommendation) ve [P-D]/[P-Y]/[Y] (personalization) kardeşlerin **kendi**
  EK'lerinde; bu dosya yalnız **T serisi** ([T-D1]–[T-D12]) yazar.
- Test-metrik/OM bağlamı: `../evaluation/evaluation-bilesenler.md` · URL havuzu:
  `../testing-web-arastirma.md` — **yeni URL: 0**.

### EK-5.7 · Uygulama Sırası (ilk sprint — salt şartname, kod yazımı 0)

| Adım | İş | Kabul kriteri |
|---|---|---|
| 1 | `tests/Unit/AI/` + taban sınıf (Reflection: `private const` L30-31 ve private skorlayıcılar L209+) | tek test dosyası `composer test` ile koşar |
| 2 | TT-5 + TT-7 (private Reflection — bozuk davranış kilidi) | 2 test yeşil |
| 3 | TT-1 + TT-2 (JSON iki yön: L70 encode · L300 decode) | `JsonException` kaydı |
| 4 | TT-4 + TT-3 (fallback L297 + kaçış L292; `@requires` ortam notu) | 2 test |
| 5 | TT-6 + TT-12 (determinizm + limit/sabitler) | determinizm kanıtı yazılır |
| 6 | TT-8..TT-11 + TT-13 (workflow/orchestrator + suite ayrımı) | `--testsuite ai` kararı ADR'ye taşınır |

> Sıra **öneridir, karar değil** (ADR gerekçesi: TM-3 tek-suite riski + TN-3 sabit erişimi).

### EK-5.8 · Hata Modları Eki (TN serisi — envanter/sabit odaklı)

| Kod | Hata modu | Tetik | Etki | Kanıt |
|---|---|---|---|---|
| **TN-1** | Kırılım aritmetik hatası | EK-1 "Unit 17" (42≠41) | envanter tablosu iç tutarsız → sayıya güven azalır | [T-D10] |
| **TN-2** | "Dizin = test" yanılgısı | `Unit/Middleware/` yalnız README.md | AI/Unit yerleşim kararı yanlış zemine oturur | [T-D11] |
| **TN-3** | Literalleşen eşik | `private const` L30-31 (`20`·`0.7`) | test sabidi yazar, sabit değişince test **kör kalır** | [T-D12] |
| **TN-4** | Gövde-değişken yanılsaması | `$sessionId`/`mood` atanır okunmaz (L53-54) | "veri kullanılıyor" sanılır; sessiz veri kaybı üretime taşınır | V-1 · T-14 |

### EK-5.9 · Ölçüm ve Kapı (bu EK)

- **Ölçüm zinciri:** başlangıç **109** → EK-1..4 sonu **373** (`read(offset=9999)` bugün) →
  EK-5 sonrası **560** → yer-turuğu doldurmaları sonrası nihai **562** (`read(offset=9999)`);
  dürüst düzeltme: "yer-turuğu satır-nötr" iddiası **yanlış çıktı** (+2 satır) — "560" ara sayıdır.
- **Kapı (bu EK):** ✓ **562 ≥ 500** · yeni URL: **0** · kod yazımı: **0** · silme: **0** ·
  git commit: **yok** · uydurma ölçü: **0** (41/16/21/5/50/4 sayıları bugünün glob+read'inden).
- Dürüstlük: 44→45 farkı **UNKNOWN** sürüyor ([T-D1]) · EK-1'in "17"sı burada düzeltildi
  (append-only — eski satır silinmedi) · personalization EK-6/EK-7'deki uydurma-verbatim
  hatası **bu dosyada tekrarlanmadı** (V-1..V-3 bugünün `read` çıktılarıyla birebir).
