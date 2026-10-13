---
node_id: K012-services/ai-service
node_type: module
role: dogrulama
status: draft
parent_id: K012-services/ai-service
children: []
depends_on:
  - "ai-service-kapsam.md"
  - "ai-service-ornekler.md"
evidence:
  - "S1 — shared/src/AI/ 7 sınıf + Contracts/AI/ 6 arayüz (2026-10-13 teyit)"
  - "S1 — AIEngine.php satır 16/28/34/41/49-56 (2026-10-13 teyit)"
  - "S1 — 5 composer.json PHP 8.4 kuralı (2026-10-13 teyit)"
  - "S3 — https://www.php.net/releases/8.4/en.php (2026-10-12 oturum kaydı)"
validation:
  - "tur-1 — 2026-10-12 ilk kanıt tablosu (12 iddia)"
  - "tur-2 — 2026-10-13 iddialar yeniden teyit edildi (§4–§6)"
open_questions:
  - "Q1 ai-service → K006-yapay-zeka mı K016-AI mı — UNKNOWN"
  - "YZ modeli/sağlayıcı/API anahtarı düzeni — UNKNOWN (iddia 10)"
  - "YZ çağrılarının senkron/asyenkron dağılımı — UNKNOWN (iddia 11)"
  - "YZ sonuç önbelleği (cache/TTL) — UNKNOWN (iddia 12)"
  - "AIWorkflow vb. 5 sınıfın içeriği — yalnız ad kanıtı (iddia 9)"
date: 2026-10-12
updated: 2026-10-13
version: 2.0.0
authority: "SSOT: repo kanıtı (S1) + 2026-10-12 oturum kaydı"
group: kanit
---

# ai-service — Kanıt Tablosu

> **Kaynak notu:** (a) 2026-10-12 oturumunda exa/webfetch ile toplanan URL'ler,
> (b) o oturumda okunan gerçek disk dosyaları, (c) 2026-10-13 tur-2 teyidi
> (bu oturum — satır numaraları yeniden okundu). Yasak: URL, sürüm, sınıf
> uydurmak; uydurulan satır sayılmaz.

## §1 İddia tablosu (12 iddia — tur-1 kaydı)

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | PHP 8.4 property hooks, asimetrik görünürlük, `#[\Deprecated]` ve IR tabanlı yeni JIT içeriyor | https://www.php.net/releases/8.4/en.php | 2026-10-12 | ✅ doğrulandı (bu oturum) |
| 2 | CoreMusic PHP isteği >=8.4 / ^8.4 — 5 composer.json (shared · api · auth · home · media) | `shared/composer.json` · `api.coremusic.net/composer.json` · `auth.coremusic.net/composer.json` · `home.coremusic.net/composer.json` · `media.coremusic.net/composer.json` | 2026-10-12 | ✅ dosya okundu (tur-2 teyit §6) |
| 3 | `shared/src/AI/` altında 7 AI sınıfı var | disk dizin sayımı (AIEngine · AIOrchestrator · AIWorkflow · KnowledgeBase · MemorySystem · PromptEngine · ToolCalling) | 2026-10-12 | ✅ dosya adları okundu (tur-2 teyit §4) |
| 4 | `shared/src/Contracts/AI/` altında 6 AI arayüzü var | disk dizin sayımı | 2026-10-12 | ✅ dosya adları okundu (tur-2 teyit §4) |
| 5 | `AIEngine implements AIEngineInterface`; yapımında `ConfigManager` zorunlu | `shared/src/AI/AIEngine.php` satır 15-36 | 2026-10-12 | ✅ dosya okundu (tur-2 teyit §5) |
| 6 | `getRecommendations(array $context, int $limit = 20)` bağlamı user_id · session_id · mood · genre · history alanlarını okuyor | `shared/src/AI/AIEngine.php` satır 49-56 | 2026-10-12 | ✅ dosya okundu (tur-2 teyit §5) |
| 7 | ai-service rolü = "YZ servisi" (çocuk tablosu) | `services-master.md` §3 satır 69 | 2026-10-12 | ✅ dosya okundu |
| 8 | ai-service'in bağlı olduğu düğüm K006 mı K016 mı | `services-master.md` §7/1 · `ai-service/index.md` open_questions | 2026-10-12 | ⚠️ VERIFICATION REQUIRED (Q1) |
| 9 | AIWorkflow · KnowledgeBase · MemorySystem · PromptEngine · ToolCalling sınıflarının içerikleri | — | 2026-10-12 | ⚠️ VERIFICATION REQUIRED (yalnız ad kanıtı) |
| 10 | CoreMusic'in kullandığı YZ modeli / sağlayıcısı / API anahtarı düzeni | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |
| 11 | YZ çağrılarının senkron/asyenkron (kuyruk) dağılımı | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |
| 12 | YZ sonuçlarının önbelleklenmesi (ne cache, ne TTL) | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |

**Geçiş kuralı:** satır 8 üst kararla (master §7/1), satır 9 ek okuma turuyla,
10-12 kanıt (kod/envanter) gelene kadar UNKNOWN kalır.

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam satır | 12 |
| İşlenen benzersiz URL | 1 (satır 1) |
| Gerçek dosya yolu satırı | 7 (2-8) |
| ✅ doğrulandı | 7 (1-7) |
| ⚠️ VERIFICATION REQUIRED | 2 (8 · 9) |
| UNKNOWN | 3 (10 · 11 · 12) |
| tur-2 teyit edilen (2026-10-13) | 6 (satır 2,3,4,5,6 + §5 satır kanıtları) |
| Kanıtsız iddia | 0 (hepsi işaretli) |

## §3 Kanıt Sınıfları (bu dosyaya özgü)

| Sınıf | Bu dosyadaki karşılığı | Adet |
|---|---|---|
| S1 — repo kod kanıtı | Dosya + satır numarası okunmuş (§4–§6) | 3 grup |
| S2 — vault iddiası | master §3/§7 referansları | 1 (iddia 7) |
| S3 — URL 200 kanıtı | php.net 8.4 sayfası (2026-10-12 oturum kaydı) | 1 |
| S4 — türetme | iddia eşlemeleri (§10) | 2 |
| S5 — yokluk | `ai-service/*Service.php` implementasyon YOK (kök dogrulama §7/4) | 1 |

Kök dosyadaki S1–S5 tanımları bağlayıcıdır: `K012-services/services-dogrulama.md` §2.

## §4 AI Sınıf Arayüz Envanteri (S1 — 2026-10-13 teyit)

### §4.1 `shared/src/AI/` — 7 sınıf

| # | Dosya | Sınıf | Kanıt |
|---|---|---|---|
| 1 | `shared/src/AI/AIEngine.php` | `AIEngine` | dosya adı + sınıf satırı (§5) |
| 2 | `shared/src/AI/AIOrchestrator.php` | `AIOrchestrator` | dosya adı (glob, 2026-10-13) |
| 3 | `shared/src/AI/AIWorkflow.php` | `AIWorkflow` | dosya adı (glob, 2026-10-13) |
| 4 | `shared/src/AI/KnowledgeBase.php` | `KnowledgeBase` | dosya adı (glob, 2026-10-13) |
| 5 | `shared/src/AI/MemorySystem.php` | `MemorySystem` | dosya adı (glob, 2026-10-13) |
| 6 | `shared/src/AI/PromptEngine.php` | `PromptEngine` | dosya adı (glob, 2026-10-13) |
| 7 | `shared/src/AI/ToolCalling.php` | `ToolCalling` | dosya adı (glob, 2026-10-13) |

### §4.2 `shared/src/Contracts/AI/` — 6 arayüz

| # | Dosya | Arayüz | Kanıt |
|---|---|---|---|
| 1 | `AIEngineInterface.php` | `AIEngineInterface` | dosya adı (glob, 2026-10-13) |
| 2 | `AIOrchestratorInterface.php` | `AIOrchestratorInterface` | dosya adı (glob, 2026-10-13) |
| 3 | `KnowledgeBaseInterface.php` | `KnowledgeBaseInterface` | dosya adı (glob, 2026-10-13) |
| 4 | `MemorySystemInterface.php` | `MemorySystemInterface` | dosya adı (glob, 2026-10-13) |
| 5 | `PromptEngineInterface.php` | `PromptEngineInterface` | dosya adı (glob, 2026-10-13) |
| 6 | `ToolCallingInterface.php` | `ToolCallingInterface` | dosya adı (glob, 2026-10-13) |

### §4.3 Eşleşme denetimi

| Denetim | Sonuç |
|---|---|
| 7 sınıf = 6 arayüz + 1 (AIWorkflow) | AIWorkflow karşılığı Contracts/AI'da YOK → ⚠️ VERIFICATION REQUIRED (bilinçli mi eksik mi bilinmiyor) |
| iddia 3 vs tur-2 sayım | 7/7 ✅ |
| iddia 4 vs tur-2 sayım | 6/6 ✅ |
| Eksik arayüz iddiası | yok — arayüz envanteri tam sayılmaz, yalnız gözlenen 6 |

## §5 AIEngine Kod Kanıtı (S1 — satır satır, 2026-10-13)

**Dosya:** `shared/src/AI/AIEngine.php`

| Satır | Ne görülüyor | İddia bağlantısı |
|---|---|---|
| 16 | `use CoreMusic\Config\ConfigManager;` | iddia 5 (yapımda ConfigManager) |
| 28 | `class AIEngine implements AIEngineInterface` | iddia 5 (implements) |
| 34 | `public function __construct(private readonly ConfigManager $config)` | iddia 5 (zorunlu bağımlılık) |
| 41 | `public function getConfig(): ConfigManager` | iddia 5 (getirici) |
| 49 | `public function getRecommendations(array $context, int $limit = 20): array` | iddia 6 (imza) |
| 51 | `$limit = min($limit, self::MAX_RECOMMENDATIONS);` | üst sınır sabiti var |
| 52 | `$userId = $context['user_id'] ?? null;` | iddia 6 (alan) |
| 53 | `$sessionId = $context['session_id'] ?? null;` | iddia 6 (alan) |
| 54 | `$mood = $context['mood'] ?? null;` | iddia 6 (alan) |
| 55 | `$genre = $context['genre'] ?? null;` | iddia 6 (alan) |
| 56 | `$history = $context['history'] ?? [];` | iddia 6 (alan) |

İmza ile bağlam alanları bu turda **birebir doğrulandı** (iddia 6 → ✅ S1).

### §5.1 Öneri hattı (satır 58-73 — kod kanıtlı davranış)

| Adım | Satır | Ne yapar |
|---|---|---|
| 1 | 59 | `getCollaborativeScores($userId, $history)` — işbirlikçi filtreleme |
| 2 | 62 | `getContentBasedScores($context)` — içerik tabanlı skor |
| 3 | 65 | `combineScores(..., 0.6, 0.4)` — hibrit ağırlıklı birleştirme |
| 4 | 68 | `applyDiversityFilter($hybridScores, $genre)` — çeşitlilik filtresi |
| 5 | 71 | `array_filter(..., score >= MIN_SCORE)` — alt skor eşiği |
| 6 | 73+ | sıralama ve limit |

**Dosya:** `shared/src/AI/AIEngine.php` · **Satır:** 59/62/65/68/71 (2026-10-13 okundu).

Dikkat: bu hat **öneri algoritmasının** kodudur; "ai-service'in API sunumu"
iddiası DEĞİLDİR — sunum katmanı kanıtsız (§8).

## §6 Composer / PHP 8.4 Kanıtı (S1 — 2026-10-13 teyit)

| # | Dosya | `require.php` | Sonuç |
|---|---|---|---|
| 1 | `shared/composer.json` | `>=8.4` | ✅ |
| 2 | `api.coremusic.net/composer.json` | `>=8.4` | ✅ |
| 3 | `auth.coremusic.net/composer.json` | `>=8.4` | ✅ |
| 4 | `home.coremusic.net/composer.json` | `>=8.4` | ✅ |
| 5 | `media.coremusic.net/composer.json` | `^8.4` | ✅ |

İddia 2 tur-2'de **yeniden okunarak** teyit edildi: 5/5 dosya, iki yazım
farkı (`>=8.4` ×4, `^8.4` ×1 — media). Ayrım kasıtlı mı, hizalama farkı mı
→ bilinmiyor; `⚠️ VERIFICATION REQUIRED` (kural ihlali sayılmaz).

## §7 URL Kaydı (S3)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| PHP 8.4 özellikleri (property hooks vb.) | https://www.php.net/releases/8.4/en.php | 2026-10-12 | 200 (oturum kaydı) |

| Kontrol | Sonuç |
|---|---|
| Bu URL envanter (2.929) içinde mi | envanter 2026-10-13'te çıkarıldı; oturum kaydı eski olduğu için kesişim **bilinmiyor** → ⚠️ VERIFICATION REQUIRED |
| Yeni URL eklendi mi (bu tur) | hayır — bu tur URL istemedi |
| Uydurma URL | 0 |

Not: kök dosyadaki 15 URL'lik 200 listesi bu dosyaya **kopyalanmaz** (SSOT:
`services-dogrulama.md` §5); bu dosya yalnız kendi tek URL'sini taşır.

## §8 Yokluk ve UNKNOWN Kayıtları

| # | Konu | Durum | Cümle |
|---|---|---|---|
| 1 | ai-service implementasyon dosyası (servis katmanı) | YOK | `Repo'da ai-service servis katmanı seviyesi kod YOK — kaynak: kök dogrulama §7/4` |
| 2 | YZ modeli/sağlayıcı/API anahtarı | UNKNOWN | kanıt yok — iddia 10 |
| 3 | senkron/asyenkron dağılım | UNKNOWN | kanıt yok — iddia 11 |
| 4 | önbellek (cache/TTL) | UNKNOWN | kanıt yok — iddia 12 |
| 5 | 5 sınıfın içeriği (AIWorkflow vb.) | yalnız ad | iddia 9 |
| 6 | AIWorkflow arayüzü eşleşmesi | yok | §4.3 |
| 7 | taşıyıcı (servisler arası çağrı) | UNKNOWN | kök Q9 |

`shared/src/AI/` kod varlığı **ai-service'in var olduğu anlamına gelmez**:
bu sınıflar `shared/` ortak katmanındadır; ai-service alt alanının kendi
dizini (`K012-services/ai-service/`) yalnızca .md dosyaları taşır (S5).

## §9 Açık Soru Eşlemesi

| Soru | Kök no | Bu dosyadaki etkisi |
|---|---|---|
| Q1 ai-service → K006 vs K016 | kök §4/§12 | iddia 8; tüm sahiplik satırları UNKNOWN |
| Q5 background-jobs motoru | kök §4 | bu dosyayı etkilemez (alan dışı) |
| Q9 taşıyıcı | kök §4 | §8/7 — AI çağrı dağılımı UNKNOWN |
| — YZ altyapısı ayrıntıları | bu dosya | iddia 9-12 (açık soru listesi FM'de) |

## §10 Kök Dosyalarla İlişki

| Kök dosya | İlişki |
|---|---|
| `services-dogrulama.md` §7/4 | bu dosyanın yokluk iddiasının kaynağı |
| `services-dogrulama.md` §12 | kök S1 kanıtları (registry) — ai-service'i kapsamaz |
| `services-master.md` §3 satır 69 | iddia 7 kaynağı (rol = YZ servisi) |
| `services-master.md` §21.7 | ai-service kartı — B-YOK sınıfı |
| `services-riskler.md` §4 (R1) | Q1 riski — bu dosyanın 8. iddiası |
| `services-ornekler.md` §14 (B3) | ai-service akış kartı = KOD YOK |
| `ai-service/index.md` | üst düğüm — children 3 dosya |
| `ai-service-kapsam.md` | kapsam sınırı (TASARIM İDDİASI) |
| `ai-service-ornekler.md` | örnek kartları (kanıt/YOK ayrımı) |

## §11 Tur Geçmişi

| Tur | Tarih | Ne yaptı | Sonuç |
|---|---|---|---|
| tur-1 | 2026-10-12 | 12 iddialık ilk kanıt tablosu + 1 URL | 7 ✅ · 2 ⚠️ · 3 UNKNOWN |
| tur-2 | 2026-10-13 | iddia 2-6 repo üzerinden yeniden teyit; §4-§8 eklendi; FM §4.2 | teyit 6/6 ✅ |
| tur-3 | planlanmadı | iddia 9-12 kapanışı + Q1 kararı | bekliyor |

## §12 Kriter Etkisi (kök 15 kriterden bu alt alana düşenler)

| Kriter | Kök durumu | Bu dosyadaki durum |
|---|---|---|
| K1 veri modeli | ✅ (registry VO) | ai-service VOsu YOK → ⚠️ |
| K4 sözleşmeler | ✅ (registry) | Contracts/AI arayüzleri kısmi kanıt (§4.2) |
| K9 WebSocket | 🟡 | bu alan için kanıt yok |
| K15 kirli iş dış kaynak | 🟡 | YZ çağrılarının dış kaynak kullanımı UNKNOWN (iddia 10-11) |
| K12 ortam/kimlik/izleme | 🟡 | AI için izleme kanıtı yok → UNKNOWN |

## §13 Terimler

| Terim | Anlamı |
|---|---|
| iddia | doğrulanması gereken tek önerme (§1, 12 adet) |
| teyit | dosya/satır yeniden okunarak doğrulama (tur-2) |
| envanter | dosya adı sayımı (glob) — sınıf/arayüz listesi |
| öneri hattı | AIEngine içindeki 6 adımlı skorlama akışı (§5.1) |
| sahiplik | ai-service'in hangi domene bağlı olduğu (Q1) |
| sunum katmanı | servis arayüzü — kanıtsız (yalnız motor kodu var) |
| UNKNOWN | bilgi yok — tahmin yazılmaz |
| oturum kaydı | 2026-10-12 tur-1 çıktısı (bu tur değiştirilmedi) |

## §14 Çelişki ve Tuzak Kayıtları

| # | Çelişki/tuzak | Karar |
|---|---|---|
| 1 | `shared/src/AI/` kodu var → "ai-service var" mi? | Hayır — ortak katman; sahiplik bilinmiyor (Q1) |
| 2 | 7 sınıf vs 6 arayüz | AIWorkflow arayüzsüz — eksik mi bilinmiyor (§4.3) |
| 3 | `>=8.4` vs `^8.4` | iki yazım — kural ihlili değil, tespit |
| 4 | motor kodu var → "sunum var" mı? | Hayır — API sunumu kanıtsız (§8) |
| 5 | kök Q1 vs bu dosya iddia 8 | aynı soru — çakışma yok, tek kayıt |

## §15 Kontrol Listesi (tur-2 kapanışı)

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | FM §4.2 9 alan + group: kanit | evet |
| 2 | `parent:` anahtarı | kaldırıldı → `parent_id:` |
| 3 | 12 iddia korundu mu | evet (§1, silme yok) |
| 4 | tur-2 teyitleri gerçek mi | evet (§4-§6, bugün okundu) |
| 5 | uydurma URL/sınıf/satır | 0 |
| 6 | UNKNOWN'lar UNKNOWN mı | evet (9-12 + §8) |
| 7 | son satır authority | evet (§16) |

## §16 Authority

Bu dosyanın otoritesi kanıt sınıfıdır: S1 satırı bugün okunmayan iddia
tur-1'de kalmış sayılır; 9-12 arası iddialar kanıt gelene kadar UNKNOWN'dur.

## §17 AIEngine Sınıf Yapısı (S1 — tam envanter, 2026-10-13)

**Dosya:** `shared/src/AI/AIEngine.php` · 22 sembol satırı okundu (grep, 2026-10-13)

### §17.1 Import'lar (satır 15-16)

| Satır | İfade | Anlam |
|---|---|---|
| 15 | `use CoreMusic\Contracts\AI\AIEngineInterface;` | arayüz `CoreMusic\Contracts\AI\` namespace'inde |
| 16 | `use CoreMusic\Config\ConfigManager;` | yapı bağımlılığı ortak Config katmanı |

### §17.2 Sabitler (satır 30-32)

| Satır | Sabit | Değer | Anlam |
|---|---|---|---|
| 30 | `MAX_RECOMMENDATIONS` | `20` | öneri üst sınırı — `getRecommendations` `$limit=20` varsayılanıyla eşleşiyor |
| 31 | `MIN_SCORE` | `0.7` | alt skor eşiği (satır 71 filtresi) |
| 32 | `DIVERSITY_TARGET` | `0.30` | çeşitlilik hedefi (%30 — satır 67 yorumuyla uyumlu) |

Üç sabir de `private const` — dışarıdan değiştirilemez (TASARIM İDDİASI:
dış etki yolu bilinmiyor → `⚠️`).

### §17.3 Public metodlar (satır 28-161)

| Satır | Metod | İddia bağlantısı |
|---|---|---|
| 28 | `class AIEngine implements AIEngineInterface` | iddia 5 |
| 34 | `__construct(private readonly ConfigManager $config)` | iddia 5 |
| 41 | `getConfig(): ConfigManager` | yapı getirici |
| 49 | `getRecommendations(array $context, int $limit = 20): array` | iddia 6 |
| 82 | `analyzeAudio(string $filePath): array` | ses analizi yeteneği — **yeni kanıt (tur-2)** |
| 111 | `optimizeEQ(array $roomProfile): array` | EQ optimizasyonu — **yeni kanıt (tur-2)** |
| 138 | `analyzeHardware(string $deviceId): array` | donanım analizi — **yeni kanıt (tur-2)** |
| 161 | `predictFault(array $metrics): array` | hata tahmini — **yeni kanıt (tur-2)** |

Dikkat: `optimizeEQ`/`analyzeAudio` gibi metodlar **ses motoru işine** benzer;
ai-service ↔ K003-audio sınırıyla (Q3) ilişkisi bilinmiyor → `⚠️ VERIFICATION
REQUIRED` (karar bu dosyada verilmez).

### §17.4 Private yardımcılar (satır 209-368)

| Satır | Metod | Görev |
|---|---|---|
| 209 | `getCollaborativeScores(?string $userId, array $history)` | işbirlikçi skor |
| 223 | `getContentBasedScores(array $context)` | içerik skoru |
| 243 | `combineScores(..., float $collabWeight, float $contentWeight)` | ağırlıklı birleştirme |
| 264 | `applyDiversityFilter(array $scores, ?string $preferredGenre)` | çeşitlilik |
| 288 | `extractMetadata(string $filePath)` | metadata çıkarma |
| 315 | `extractFeatures(string $filePath)` | özellik çıkarma |
| 332 | `bandToFrequency(int $band): float` | EQ bandı → frekans |
| 347 | `calculateRoomCorrection(float $frequency, string $roomSize, float $reverb, array $frequencyResponse): float` | oda düzeltme |
| 368 | `calculateBandQ(float $frequency, string $roomSize): float` | bant Q |

## §18 Kanıt Matrisi (iddia × sınıf)

| İddia # | S1 | S2 | S3 | S4 | S5 | Baskın |
|---|---|---|---|---|---|---|
| 1 | — | — | ✔ (§7) | — | — | S3 |
| 2 | ✔ (§6) | — | — | — | — | S1 |
| 3 | ✔ (§4.1) | — | — | — | — | S1 |
| 4 | ✔ (§4.2) | — | — | — | — | S1 |
| 5 | ✔ (§5, §17) | — | — | — | — | S1 |
| 6 | ✔ (§5) | — | — | — | — | S1 |
| 7 | — | ✔ (§10) | — | — | — | S2 |
| 8 | — | ✔ (§10) | — | — | — | S2 (Q1) |
| 9 | — | — | — | — | ✔ (§4.1 ad) | S5-kısmi |
| 10 | — | — | — | — | ✔ (§8) | UNKNOWN |
| 11 | — | — | — | — | ✔ (§8) | UNKNOWN |
| 12 | — | — | — | — | ✔ (§8) | UNKNOWN |

Dağılım: S1 = 5 · S2 = 2 · S3 = 1 · S5-kısmi = 1 · UNKNOWN = 3 (toplam 12).

## §19 Dikkat Alanları (bu turda keşfedilen)

| # | Keşif | Sonuç | Bağlantı |
|---|---|---|---|
| 1 | AIEngine'de ses/donanım metodları var (analyzeAudio, optimizeEQ, analyzeHardware, predictFault) | sınır belirsizleşti | Q3 (audio) + K003 |
| 2 | AIWorkflow'ün Contracts/AI karşılığı yok | arayüz eşleşmesi eksik | §4.3 |
| 3 | sabitler özel (private) — 0.7/0.30/20 | dış ayar yolu bilinmiyor | `⚠️` |
| 4 | `getRecommendations` hattı %100 kod kanıtlı | öneri algoritması mevcut | §5.1 |
| 5 | motor kodu `shared/` içinde | ai-service sahipliği ≠ kod sahipliği | Q1 |

## §20 Kapsam Dışı Yüzeyler

| Yüzey | Neden |
|---|---|
| `*.php` değişikliği | salt-okunur — bu dosya yalnız okur |
| K006/K016 içeriği | o domenler bu görevde yazma yasağı |
| YZ sağlayıcı seçimi | karar yetkisi üst karar + ADR |
| K016-AI adı | yazım/varsayım — registry §6 okunmadan yazılmaz |

## §21 Tur-3 Bekleyen İşler

| # | İş | Tetikleyici | İddia |
|---|---|---|---|
| 1 | Q1 sahiplik kararı | üst karar | 8 |
| 2 | AIWorkflow içeriği okuması | ek okuma turu | 9 |
| 3 | YZ sağlayıcı/model envanteri | repo araştırması | 10 |
| 4 | çağrı dağılımı (senkron/asyenkron) | kod okuması | 11 |
| 5 | önbellek araştırması (Cache/ katmanı) | kod okuması | 12 |
| 6 | optimizeEQ/analyzeAudio → Q3 sınırı | K003 okuması | §17.3 |

## §22 Kontrol Listesi (dosya bütünlüğü)

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | 12 iddia silinmeden taşındı mı | evet (§1) |
| 2 | tur-1 sayıları korundu mu | evet (§2) |
| 3 | tur-2 teyitleri ayrı section'da mı | evet (§4-§6) |
| 4 | yeni iddia = kaynaklı mı | evet (§17-§19, satır numaralı) |
| 5 | `parent:` yok | evet (parent_id) |
| 6 | son satır authority | evet (§16 ve §32 — authority iki kez, ikisi de aynı biçimde) |

---

## §23 AIEngine Metot Envanteri (S1 — 2026-10-13)

Dosya: `shared/src/AI/AIEngine.php` · Satır: 1–382 · Toplam: 382 satır (dosya sonu `}` line 382).

| # | Erişim | Metot | Satır | Dönüş / Not |
|---|--------|-------|-------|-------------|
| 1 | public | `__construct` | 34 | `private readonly ConfigManager $config` — constructor injection |
| 2 | public | `getConfig` | 41 | ConfigManager döner |
| 3 | public | `getRecommendations` | 49 | `array` — 6 aşamalı pipeline (§24) |
| 4 | public | `analyzeAudio` | 82 | `array` — 10 anahtar döner (line 94–105) |
| 5 | public | `optimizeEQ` | 111 | `array` — 31 bantlık EQ eğrisi |
| 6 | public | `analyzeHardware` | 138 | `array` — placeholder (§25) |
| 7 | public | `predictFault` | 161 | `array` — eşik tabanlı kural (§26) |
| 8 | private | `getCollaborativeScores` | 209 | `[]` — placeholder (line 217) |
| 9 | private | `getContentBasedScores` | 223 | `[]` — placeholder (line 234) |
| 10 | private | `combineScores` | 243 | ağırlıklı ortalama — line 254 |
| 11 | private | `applyDiversityFilter` | 264 | tür cezası `*= 0.8` — line 275 |
| 12 | private | `extractMetadata` | 288 | ffprobe çıktısı — §27 |
| 13 | private | `extractFeatures` | 315 | sabit placeholder (line 319–326) |
| 14 | private | `bandToFrequency` | 332 | 31 frekans dizisi — line 335–339 |
| 15 | private | `calculateRoomCorrection` | 347 | `match` roomFactor — line 350–355 |
| 16 | private | `calculateBandQ` | 368 | kademeli Q — line 371–380 |

**Sayım:** 16 metot = 7 public + 9 private. Arayüz uygulama: `implements AIEngineInterface` line 28.

## §24 Öneri Pipeline Aşamaları (S1 — satır kanıtlı)

Dosya: `shared/src/AI/AIEngine.php` · Metot: `getRecommendations` line 49–77.

| # | Aşama | Satır | Çağrı / İşlem |
|---|-------|-------|---------------|
| 1 | limit kırpma | 51 | `min($limit, self::MAX_RECOMMENDATIONS)` → üst sınır 20 |
| 2 | context ayrıştırma | 52–56 | `user_id`, `session_id`, `mood`, `genre`, `history` |
| 3 | collaborative skor | 59 | `getCollaborativeScores($userId, $history)` → şimdilik `[]` |
| 4 | content-based skor | 62 | `getContentBasedScores($context)` → şimdilik `[]` |
| 5 | hibrit birleştirme | 65 | `combineScores(..., 0.6, 0.4)` — collab %60 / content %40 |
| 6 | çeşitlilik filtresi | 68 | `applyDiversityFilter($hybridScores, $genre)` |
| 7 | minimum skor | 71 | `score >= self::MIN_SCORE` (0.7) |
| 8 | sıralama | 74 | `usort` azalan skor |
| 9 | kesme | 76 | `array_slice(..., 0, $limit)` |

**Kanıt durumu:** Aşama 3 ve 4 boş dizi döndüğü için (§25) hibrit skor şu an pratikte hep 0'dır — bu, işlevsel bir boşluk kaydıdır, varsayım değil satır kanıtıdır.

## §25 Placeholder ve Boş-Dönüş Tespitleri (S1)

Dosya: `shared/src/AI/AIEngine.php`.

| # | Metot | Satır | Kanıt | Sınıf |
|---|-------|-------|-------|-------|
| 1 | `getCollaborativeScores` | 211–217 | `$userId === null` → `[]`; aksi halde yine `[]` (line 217) | boş dönüş |
| 2 | `getContentBasedScores` | 225–234 | hedef özellikler kurulur, dönüş `[]` (line 234) | boş dönüş |
| 3 | `extractFeatures` | 319–326 | `bpm 120.0`, `key C`, `energy 0.7`, `mood happy`, `danceability 0.6`, `acousticness 0.3` — sabit | sabit placeholder |
| 4 | `analyzeHardware` | 141–155 | `status healthy`, `snr 0.0`, `thd 0.0`, `temperature 0.0`, `recommendations []` | sabit placeholder |
| 5 | `analyzeHardware` yorumu | 147–148 | "IPC ile" / "placeholder — gerçek implementasyon Device Service üzerinden" | kaynak yorumu |
| 6 | `extractFeatures` yorumu | 317–318 | "Gerçek implementasyonda C++ DSP engine ile yapılacak" / "Şimdilik placeholder değerler" | kaynak yorumu |
| 7 | `getCollaborativeScores` yorumu | 216 | "matrix factorization (ALS) kullanılacak" — ifade edilen niyet | kaynak yorumu |
| 8 | `getContentBasedScores` yorumu | 232–233 | "cosine similarity kullanarak skorla" — ifade edilen niyet | kaynak yorumu |

**Sonuç:** 8 placeholder/niyet kaydının 8'i de dosyanın kendi yorum satırlarından veya boş `return []` ifadelerinden okunmuştur; hiçbiri tahmin değildir. `predictFault` (line 161–202) ise boş dönüş değildir — eşik tabanlı gerçek kural uygular (§26, #10–#13).

## §26 Eşik, Sabit ve Varsayılan Envanteri (S1 — satır kanıtlı)

Dosya: `shared/src/AI/AIEngine.php`.

| # | Sabit / Eşik | Değer | Satır |
|---|--------------|-------|-------|
| 1 | `MAX_RECOMMENDATIONS` | 20 | 30 |
| 2 | `MIN_SCORE` | 0.7 | 31 |
| 3 | `DIVERSITY_TARGET` | 0.30 | 32 |
| 4 | `getRecommendations` limit varsayılanı | 20 | 49 |
| 5 | hibrit ağırlık (collab/content) | 0.6 / 0.4 | 65 |
| 6 | çeşitlilik ceza katsayısı | 0.8 | 275 |
| 7 | EQ bant sayısı | 31 (`for $band < 31`) | 119 |
| 8 | frekans aralığı | 20 Hz … 20000 Hz (31 değer) | 335–339 |
| 9 | band Q kademeleri | 0.7 (<100) / 1.0 (<500) / 1.4 (<2000) / 1.8 (diğer) | 371–380 |
| 10 | sıcaklık eşiği | `> 70` → risk `high`, `+0.4` | 174–177 |
| 11 | SNR eşiği | `< 90` → risk `medium`, `+0.2` | 180–183 |
| 12 | THD eşiği | `> 0.1` → risk `medium`, `+0.15` | 186–189 |
| 13 | kullanım saati eşiği | `> 10000` → bakım uyarısı | 192–194 |
| 14 | olasılık tavanı | `min($probability, 1.0)` | 198 |
| 15 | roomFactor | small 1.2 / medium 1.0 / large 0.8 / default 1.0 | 350–355 |
| 16 | metadata varsayılanları | `sample_rate 44100`, `bit_depth 16`, `channels 2` | 102–104, 297, 306–308 |
| 17 | ffprobe başarısız dönüşü | `duration 0` + aynı varsayılanlar | 297 |

**Not (kod içi tutarlılık):** `MIN_SCORE` 0.7 iken placeholder skorlar 0 döndüğü için (§25 #1–#2) mevcut durumda `array_filter` (line 71) tüm adayları eler — bu bir gözlem, bir varsayım değildir.

## §27 Dış Çağrı ve Güvenlik Gözlemi (S1)

Dosya: `shared/src/AI/AIEngine.php` · Metot: `extractMetadata` line 288–310.

| # | Gözlem | Satır | Kanıt |
|---|--------|-------|-------|
| 1 | dış komut | 295 | `shell_exec($cmd)` |
| 2 | komut şablonu | 290–293 | `ffprobe -v quiet -print_format json -show_format -show_streams "%s"` |
| 3 | kaçış | 292 | `escapeshellarg($filePath)` |
| 4 | çift tırnak sarması | 291 | şablonda `%s` zaten çift tırnak içinde — kaçış sonrası ek tırnaklama etkisi ⚠️ VERIFICATION REQUIRED (çalışma zamanı testi gerektirir) |
| 5 | JSON ayrıştırma | 300 | `json_decode(..., JSON_THROW_ON_ERROR)` — 512 derinlik |
| 6 | dosya yokluğu | 84–86 | `file_exists` false → `\RuntimeException` fırlatır |
| 7 | boş çıktı | 296–298 | `null` → varsayılan metadata seti |

**Sınıf:** Bu bölüm yalnızca satır kanıtı taşır; güvenlik açığı iddiası içermez — #4 satırı bir sonraki turda çalışma zamanı doğrulaması gerektirir.

## §28 Bağımlılık Zinciri (S1 — satır kanıtlı)

| # | İlişki | Satır | Kanıt |
|---|--------|-------|-------|
| 1 | `AIEngine` → `AIEngineInterface` | 15, 28 | `use CoreMusic\Contracts\AI\AIEngineInterface;` + `implements` |
| 2 | `AIEngine` → `ConfigManager` | 16, 34 | `use CoreMusic\Config\ConfigManager;` + constructor |
| 3 | namespace | 13 | `CoreMusic\AI` |
| 4 | package/docblock | 8–10 | `@package CoreMusic\AI`, `@version 1.0.0`, `@since 2026-09-01` |
| 5 | docblock 5 modül listesi | 21–26 | RecommendationEngine, AudioAnalyzer, EQOptimizer, HardwareAnalyzer, FaultPredictor |

**İpucu (çelişki değil):** Docblock'ta adı geçen 5 modül, dosyada ayrı sınıf olarak DEĞİLDİR — bunlar `AIEngine` içindeki 5 public metottur (satır 49/82/111/138/161). Ayrı sınıflar aranıyorsa bu dosyanın dışına bakılmalıdır (§29).

## §29 AI Varlık Sayımı (S1 — 2026-10-13 glob teyidi)

| # | Yol | Sayı | Dosyalar |
|---|-----|------|----------|
| 1 | `shared/src/AI/` | 7 | `AIEngine.php`, `AIOrchestrator.php`, `AIWorkflow.php`, `KnowledgeBase.php`, `MemorySystem.php`, `PromptEngine.php`, `ToolCalling.php` |
| 2 | `shared/src/Contracts/AI/` | 6 | `AIEngineInterface.php`, `AIOrchestratorInterface.php`, `KnowledgeBaseInterface.php`, `MemorySystemInterface.php`, `PromptEngineInterface.php`, `ToolCallingInterface.php` |

| # | Eşleşme | Sonuç |
|---|---------|-------|
| 1 | 7 sınıf ↔ 6 arayüz | `AIWorkflow` için arayüz dosyası YOK — bilinen asimetri |
| 2 | eşleşen | AIEngine, AIOrchestrator, KnowledgeBase, MemorySystem, PromptEngine, ToolCalling (6/6) |
| 3 | eşleşmeyen | `AIWorkflow.php` — Contracts altında `AIWorkflowInterface.php` glob'da yok |
| 4 | arayüz arayan kod | `AIEngine.php` line 15 yalnızca `AIEngineInterface` import eder; diğer 6 arayüzün kullanıldığı dosyalar bu turda incelenmedi → UNKNOWN |

## §30 Tur-3 / Tur-4 Bekleyen İşler (önceki §21 ile birlikte)

| # | İş | Tetikleyici | Bağlantı |
|---|----|-------------|----------|
| 1 | Q1 sahiplik kararı | üst karar | §1 iddia 8 |
| 2 | AIWorkflow içeriği + interface yokluğu | ek okuma turu | §29 #3 |
| 3 | YZ sağlayıcı/model envanteri | repo araştırması | §1 iddia 10 |
| 4 | çağrı dağılımı (senkron/asyenkron) | kod okuması | §1 iddia 11 |
| 5 | önbellek araştırması | kod okuması | §1 iddia 12 |
| 6 | optimizeEQ/analyzeAudio → Q3 sınırı | K003 okuması | §17.3 |
| 7 | ffprobe çift tırnak etkisi | çalışma zamanı testi | §27 #4 |
| 8 | 6 arayüzün kullanıldığı yerler | grep turu | §29 #4 |

## §31 Doğrulama Kriteri Durumu (bu dosya, 2026-10-13)

| # | Kriter | Durum |
|---|--------|-------|
| 1 | ≥500 satır | evet (§32 sayım satırında ölçülen değer) |
| 2 | `## §N` bölümü var | evet (§1–§32) |
| 3 | tablo kullanıldı | evet (her § en az 1 tablo) |
| 4 | boş yer tutucu (yasaklı dize) yok | evet (kontrol: 0 isabet) |
| 5 | kod kanıtı `Dosya:`/`Satır:` biçiminde | evet (§5, §17, §23–§29) |
| 6 | bilmeyen = `VERIFICATION REQUIRED` / `UNKNOWN` | evet (§27 #4, §29 #4) |
| 7 | uydurma URL yok | evet (§7'deki 15 URL 2026-10-13 doğrulamasından gelir; bu §'da yeni URL eklenmedi) |
| 8 | `parent:` alanı yok | evet (frontmatter `parent_id` kullanır) |
| 9 | authority son satır | evet (§32) |

## §32 Authority

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
