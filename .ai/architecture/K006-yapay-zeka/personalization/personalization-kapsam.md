---
title: "personalization — Kapsam"
type: architecture-scope
node_id: K006-yapay-zeka/personalization
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# personalization — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`ai-master.md` §1, v2 §2.6 rol cümlesini birebir taşır: **"Şarkı öneren, sesi analiz eden yapay zeka parçaları."**
`ai-master.md` §1 sorumluluk tablosu (satır 37–42) bu adı **"Öneri üretmek"** sorumluluğuna bağlar
(`recommendation · personalization` birlikte) — tablo *tasarım yorumudur*.

Basitçe: aynı öneriyi herkese değil, **bu kullanıcıya** göre yapmak — oturum tercihleri, mood, geçmiş.
Repo'da karşılık **kısmen** var: workflow oturum tercihini okuyup öneriye enjekte eder
(`AIWorkflow` satır 55), ama bu çocuğun **resmi kapsam tanımı v2'de yoktur**.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Kullanıcı bağlamının (user_id · mood · geçmiş) öneri modeline verilmesi | Bağlam/etkileşim verisinin saklanması | K007-data (bagimliliklar §2/1) |
| 2 | Kişiselleştirme sinyallerinin sözleşmesi (alan adları) | "beğeni/tercih" iş kuralının tanımı | K008-domain (bagimliliklar §2/2) |
| 3 | Oturum tercihinin okunması (öneri girdisi olarak) | Oturum güvenliği, CSRF, kimlik | K009-security · K010-identitiy (ai-master §2.1) |
| 4 | Kişisel sıralamanın K006'da üretilmesi | Öneri kartının UI'da gösterimi | K011-application (ai-master §2.1) |
| 5 | Kişiselleştirme sinyali ↔ K016 tek ML çağrı noktası | Ajan çalışma zamanı | K016-AI (ai-master §2.1) |
| 6 | Kişiselleştirmenin öneri akışına entegrasyonu | Öneri motorunun skorlama çekirdeği | recommendation (ai-master §3) |

**Sınır kuralı (v2 §2.6 · registry §6):** bu dal yalnız **K006 = ML ürün tarafını** kapsar; K016-AI ajan
altyapısına **taşınmaz**.

**Yasak:** tablo **kademe-2 taslak sınır önerisidir** — v2 §2.6 bu çocuğa rol vermez; kilit ADR-097 K4'tedir.

### Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| A1 | personalization ↔ recommendation (kişisel sıralama kimin?) | **UNKNOWN** — v2 ayrım vermez | ai-master §3 satır 261–262 |
| A2 | Tercih verisi nerede saklanır (session vs K007 tablosu) | ⚠️ VERIFY REQUIRED | bagimliliklar §4/2 |
| A3 | Anonim oturum (session_id) kişiselleştirmesi kapsamda mı? | ⚠️ taslak — repo'da `anonymous` dalı var | `AIWorkflow.php` satır 52 |
| A4 | Kişisel veri/gizlilik sınırı (KVKK benzeri) hangi düğümde? | **UNKNOWN** | ai-master §2.1 (K009/K010) |

## §3 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk adı v2 §2.6 satır 262'de birebir geçer | `.ai/architecture/K006-yapay-zeka/personalization/index.md` (evidence) | ✅ dosya okundu |
| 2 | Rol tanımı yok → `⚠️ VERIFICATION REQUIRED` | `personalization/index.md` (open_questions satır 13) | ✅ dosya okundu |
| 3 | Workflow oturum tercihini okur ve bağlama birleştirir | `shared/src/AI/AIWorkflow.php` satır 55–56 | ✅ dosya okundu |
| 4 | Anonim oturum dalı (`'anonymous'`) mevcut | `shared/src/AI/AIWorkflow.php` satır 52 | ✅ dosya okundu |
| 5 | Engine bağlamı `user_id · session_id · mood · genre · history` olarak okur | `shared/src/AI/AIEngine.php` satır 52–56 | ✅ dosya okundu |
| 6 | Sonuç oturuma 1 saat (3600 sn) yazılır | `shared/src/AI/AIWorkflow.php` satır 78 | ✅ dosya okundu |
| 7 | Kişiselleştirilmiş önerinin gerçek zamanlı etkileşimle güncellendiği resmi servis kanıtı | `personalization-dogrulama.md` satır 7 (URL) | ✅ kayıt |
| 8 | K006'nın kişiselleştirme teknoloji seçimi | `ai-master.md` §4 | **UNKNOWN** |

### Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | personalization ↔ recommendation sahipliği kimde? | ⚠️ VERIFICATION REQUIRED |
| R2 | Tercih verisinin saklandığı yer (session vs K007)? | ⚠️ bagimliliklar §4 — **UNKNOWN** |
| R3 | Anonim oturum kişiselleştirmesi K006'da mı, K010'da mı? | ⚠️ **UNKNOWN** |
| R4 | Kişisel veri saklama/gizlilik sınırı? | ⚠️ ai-master §2.1 — K009/K010 |

**Kapanış:** status: draft kalır. R1–R3 kapanmadan bu dosyada kişiselleştirme **sahipliği veya veri yeri**
iddiası yazılmaz (yapay-zeka-dogrulama §5).

## §4 İlişkili dosyalar

- [[personalization-dogrulama]]
- [[personalization-ornekler]]
- [[../index|K006-yapay-zeka index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12

---

## EK — 2026-10-15 genişletme eki (append-only · ≥500 kapısı)

> Bu ek dosyanın SONUNA eklenmiştir (append-only). Gövde §1–§4 silinmemiş/değiştirilmemiştir:
> eski içerik, eklenen metnin birebir önekidir. Tüm satır referansları 2026-10-15 taze
> okumadan alınmıştır.

### EK §0 — Usul

| Kural | Uygulama (bu ekte) |
|---|---|
| Append-only | Ekleme dosya sonunda; gövde satırlarına dokunulmadı |
| Ölçüm | `read(offset=9999, limit=1)` hata sayımı (dosya satır sayısı hatada döner) |
| Boş dolgu yasak | TBD · `// ...` · uydurma satır: 0 |
| Zero-hallucination | Her iddia `Dosya: <yol> [Satır: n]` ile kanıtlanır; kanıtsız iddia yazılmaz |
| Kod iddiası | Repo'da `*.cpp/*.h` = 0 (2026-10-15 görev ölçümü) → "repo C++ kodu var" iddiası YOK |
| git commit | YOK — commit orkestratöre aittir |
| Beyazliste | Yalnız bu dosya yazıldı · `*.php` kaynak kod **okundu, yazılmadı** · `index.md` · kardeşler · `.ai/projects` · `.ai/electronic` **yalnız okundu** |

**Önceki sayım (ek öncesi):** `read(offset=9999)` → **83 satır** (görev başı kayıt).

### EK §1 — Taze okuma koşu kayıtları (2026-10-15)

| # | İşlem | Hedef | Sonuç |
|---|---|---|---|
| T1 | offset=9999 sayım | `personalization-kapsam.md` | **83 satır** (ek öncesi) |
| T2 | tam okuma | `personalization-kapsam.md` | L1–L83 · frontmatter 11 alan · gövde §1–§4 · Authority L83 |
| T3 | tam okuma | `shared/src/AI/AIWorkflow.php` | **271 satır** · `recommendationWorkflow` L49–L91 (session kişiselleştirme akışı) |
| T4 | tam okuma | `shared/src/AI/AIEngine.php` | **382 satır** · bağlam okuma L52–L56 · collab L209–L218 |
| T5 | tam okuma | `shared/src/Contracts/AI/AIEngineInterface.php` | **61 satır** · `getRecommendations` sözleşmesi L21–L28 (context docblock L24) |
| T6 | glob | `.ai/architecture/K006-yapay-zeka/**/*.md` | 76 dosya · personalization dalı 7 dosya (index + 6 rol) |

### EK §2 — Verbatim bloklar (birinci el okuma, 2026-10-15)

#### B1 — kişiselleştirme akışı: workflow (`shared/src/AI/AIWorkflow.php` L49–L78, T3)

```php
49:     public function recommendationWorkflow(array $request): array
50:     {
51:         $startTime = microtime(true);
52:         $sessionId = $request['session_id'] ?? 'anonymous';
53: 
54:         // 1. Context collect — session'dan tercihleri çek
55:         $context = $this->memory->getSession($sessionId, 'preferences') ?? [];
56:         $context = array_merge($context, $request);
57: 
58:         // 2. Task dispatch
59:         $taskId = $this->orchestrator->dispatchTask('recommendation', $context, 'medium');
60: 
61:         // 3. Knowledge query — benzer önerileri ara
62:         $query = ($context['mood'] ?? '') . ' ' . ($context['genre'] ?? '');
63:         $knowledgeResults = $this->knowledge->search($query, 5);
64: 
65:         // 4. Prompt generate
66:         $promptResult = $this->promptEngine->generatePrompt('recommendation', [
67:             'mood'   => $context['mood'] ?? 'neutral',
68:             'genre'  => $context['genre'] ?? 'any',
69:             'energy' => $context['energy'] ?? 0.5,
70:             'history' => json_encode($context['history'] ?? [], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
71:             'limit'  => $context['limit'] ?? 10,
72:         ], ['knowledge' => $knowledgeResults]);
73: 
74:         // 5. Recommendation engine
75:         $recommendations = $this->engine->getRecommendations($context, $context['limit'] ?? 10);
76: 
77:         // 6. Feedback — sonucu kaydet
78:         $this->memory->setSession($sessionId, 'last_recommendations', $recommendations, 3600);
```

#### B2 — engine bağlam okuması (`shared/src/AI/AIEngine.php` L49–L56, T4)

```php
49:     public function getRecommendations(array $context, int $limit = 20): array
50:     {
51:         $limit = min($limit, self::MAX_RECOMMENDATIONS);
52:         $userId = $context['user_id'] ?? null;
53:         $sessionId = $context['session_id'] ?? null;
54:         $mood = $context['mood'] ?? null;
55:         $genre = $context['genre'] ?? null;
56:         $history = $context['history'] ?? [];
```

#### B3 — anonim yollar: collab (`AIEngine.php` L209–L218, T4)

```php
209:     private function getCollaborativeScores(?string $userId, array $history): array
210:     {
211:         if ($userId === null) {
212:             return [];
213:         }
214: 
215:         // DB'den benzer kullanıcıları bul
216:         // Gerçek implementasyonda matrix factorization (ALS) kullanılacak
217:         return [];
218:     }
```

#### B4 — sözleşme bağlam docblock'u (`AIEngineInterface.php` L21–L28, T5)

```php
21:     /**
22:      * Müzik önerisi üretir.
23:      *
24:      * @param array<string, mixed> $context Kullanıcı bağlamı (tercihler, geçmiş, mood)
25:      * @param int                  $limit   Maksimum öneri sayısı
26:      * @return array<int, array{id: string, title: string, artist: string, score: float, reason: string}>
27:      */
28:     public function getRecommendations(array $context, int $limit = 20): array;
```

### EK §3 — Kod analizi bulguları (her biri verbatim kanıtlı)

#### §3.1 Kişiselleştirme sinyal zinciri (repo durumu)

| # | Sinyal | Üretildiği yer | Tüketildiği yer | Durum | Kanıt |
|---|---|---|---|---|---|
| 1 | `session_id` | istek `request['session_id']` (varsayılan `'anonymous'`) | `memory->getSession` anahtarı (L55) | ✅ akıyor | B1 L52 · L55 |
| 2 | `preferences` (session) | `memory->getSession($sessionId, 'preferences')` | `$context` (L55 → L56 merge) | ✅ akıyor | B1 L55–L56 |
| 3 | `user_id` | `$context['user_id']` | collab (L59) → ama collab boş (B3) | 🔄 okunuyor, kullanılmıyor | B2 L52 · B3 |
| 4 | `mood` | `$context['mood']` | prompt (L67) + engine (L54) | 🔄 prompt'a akıyor; skorlamaya **akmıyor** | B1 L54/L67 |
| 5 | `genre` | `$context['genre']` | prompt (L68) + engine (L55) + diversity (L68 workflow → engine L68) | 🔄 aynı | B1 L62/L68 · B2 L55 |
| 6 | `history` | `$context['history']` | prompt JSON (L70) + collab (L59 → B3'te dokunulmaz) | 🔄 prompt'a akıyor; collab boş | B1 L70 · B3 |
| 7 | `energy` | `$context['energy'] ?? 0.5` | prompt (L69) | 🔄 yalnız prompt | B1 L69 |
| 8 | `limit` | `$context['limit'] ?? 10` | engine (L75) | ✅ akıyor | B1 L75 |
| 9 | `last_recommendations` | `memory->setSession(…, 3600)` | geri okunmaz (bu workflow içinde) | 🔄 yazıldı, okunmadı | B1 L78 |

#### §3.2 Bulgu P-K1 — kişiselleştirme **tek yönlü yazma**dır; okuma döngüsü kapalı değil

| # | Adım | Konum | Gözlem |
|---|---|---|---|
| a | `preferences` okunur | B1 L55 | `?? []` — boşsa sessizce `[]` |
| b | `$request` üzerine bindirilir | B1 L56 | `array_merge($context, $request)` → **request tercihi ezer** |
| c | sonuç `last_recommendations` yazılır | B1 L78 | 3600 sn TTL |
| d | `preferences` anahtarı **hiç yazılır** | tam okuma 271 satır | `setSession(…, 'preferences', …)` **0 isabet** |

**Sonuç:** `preferences` anahtarı workflow'ta **yalnız okunur** (L55); kullanıcı tercihinin bu
anahtara yazıldığı bir kod yolu **yoktur** (T3 tam okumada tek `setSession` çağrısı L78'dir ve
anahtar `last_recommendations`tır). Kişiselleştirme girdisi **boş kalır** → dalın §3/3
iddiası ("workflow oturum tercihini okuyup öneriye enjekte eder") **doğrudur ama enjeksiyon
kaynağı boştur**. Kapı P-P2.

#### §3.3 Bulgu P-K2 — `array_merge` sırası: istek, oturum tercihini **ezar** (override)

B1 L55–L56:

| Sıra | Kaynak | Etki |
|---|---|---|
| 1 | `getSession(...)` → `$context` | temel (oturum) |
| 2 | `array_merge($context, $request)` | **sonraki anahtar öncekini ezer** |

PHP `array_merge` davranışsal gerçeği: aynı string anahtarlı ikinci dizi birinciyi ezer.
Burada `$request` ikinci olduğundan **ham istek her zaman oturum tercihinin üstüne biner**.
Tasarım niyeti (tercihler → istek sırası, yani tercih üstün olsun) ile kod **ters yöndedir** —
ya da istek üstün olmak isteniyordur; bu **UNKNOWN** (kapı P-P3). Kişiselleştirme açısından
kritik: kullanıcı oturumunda kayıtlı mood/genre olsa bile istek boş alanla bunu **silebilir**
(boş string bile anahtar varsa ezer).

#### §3.4 Bulgu P-K3 — anonim varsayılan `'' → 'anonymous'` herkesi **tek çuvala** koyar

| # | Katman | Değer | Konum |
|---|---|---|---|
| 1 | workflow session_id | `'anonymous'` (istek yoksa) | B1 L52 |
| 2 | engine session_id | `$context['session_id'] ?? null` | B2 L53 |
| 3 | collab yolu | `$userId === null` → `[]` | B3 L211–L212 |

Anonim isteklerin tamamı `memory->getSession('anonymous', 'preferences')` ve
`setSession('anonymous', 'last_recommendations', …)` üzerinden **aynı oturum anahtarını**
paylaşır → bir anonim kullanıcının yazdığı son öneri tüm anonimlere açık olabilir (TTL 3600 sn,
B1 L78). Gizlilik/güvenlik yüzeyi K009/K010 sınırındadır (gövde §2/3 · A4); bu ek yalnız
**kod gerçeğini** raporlar, sınır kararı vermez → kapı P-P4.

#### §3.5 Bulgu P-K4 — `user_id` okunur ama kişiselleştirme skoruna **hiç dönüşmez**

| # | Katman | Gözlem | Kanıt |
|---|---|---|---|
| 1 | engine `$userId = $context['user_id'] ?? null` | okunuyor | B2 L52 |
| 2 | `getCollaborativeScores($userId, $history)` | `$userId` yalnız `null` kontrolünde kullanılır | B3 L211 |
| 3 | `$sessionId` (B2 L53) | collab/content fonksiyonlarına **geçirilmez** | B3 imzası: `(?string $userId, array $history)` |

`$sessionId` L53'te okunur; collab (L59 yalnız `$userId, $history`) ve content (L62 yalnız
`$context`) çağrılarında **parametre olarak geçmez** → kişiselleştirme için hazır kanal
**vardır ama boştur** (recommendation-kapsam EK §3.6 ile aynı bulgu, iki dosyadan bağımsız
okuma). Kapı P-P5.

#### §3.6 Bulgu P-K5 — kişiselleştirme sinyallerinin tamamı **prompt'a** akar, skorlamaya **akmaz**

| Sinyal | Prompt'a | Skorlamaya |
|---|---|---|
| mood | ✅ L67 | ❌ (engine L54 okur ama collab/content boş) |
| genre | ✅ L68 | 🔄 diversity filtresine girer ama `genre` alanı üretilmez (bkz recommendation EK §3.3) |
| energy | ✅ L69 | ❌ |
| history | ✅ L70 (JSON) | ❌ (B3: collab fonksiyonu dokunmaz) |

Mimarî okuma: kişiselleştirme bugünkü hâliyle **LLM şablon girdisi** olarak var; sayısal
skorlamaya **hiçbir sinyal taşımıyor**. Prompt'un kendisi de akışa girmiyor
(recommendation-kapsam EK §3.5 / R-K4) → sonuç: kişiselleştirme **hiçbir çıktıyı etkilemiyor**.
Kapı P-P6.

#### §3.7 Bulgu P-K6 — TTL ve cache anahtarı disiplini (autoEQ karşılaştırması)

| Workflow | Cache/oturum anahtarı | TTL | Konum |
|---|---|---|---|
| recommendation | `last_recommendations` (session) | 3600 | B1 L78 |
| autoEQ | `eq_{sessionId}_` + `md5(serialize($roomProfile))` | 3600 | `AIWorkflow.php` L173–L174 |

autoEQ oturum anahtarını **profil özetiyle** birleştirir (çakışma azaltma); recommendation
**tek anahtar** kullanır → ardışık isteklerde `last_recommendations` **ezilir** (önceki öneri
kaybolur). Tutarlılık açısından recommendation'ın autoEQ desenine yaklaşması bir seçenektir
(karar: architect — kapı P-P7, müdahale yok).

#### §3.8 Kişiselleştirme olgunluk matrisi (bu ek sentezi)

| Olgunluk seviyesi | Tanım | Repo durumu | Kanıt |
|---|---|---|---|
| L0 Yok | kişiselleştirme hiç yok | ❌ değil | — |
| L1 Bağlam taşıma | oturum/istek bağlamı engine'e ulaşır | ✅ **bu seviye** | B1 L55–L56 · B2 L52–L56 |
| L2 Tercih döngüsü | tercih yazılır + okunur | ❌ yazma yok | §3.2 (P-K1) |
| L3 Skor etkisi | sinyaller skoru değiştirir | ❌ (motor `[]` döner) | §3.5 (P-K5) |
| L4 Öğrenme | geçmişten tercih öğrenimi (ALS/bandit) | ❌ | B3 L216 (gelecek zaman yorumu) |
| L5 Değerlendirme | kişiselleştirme kazancı ölçülür (A/B) | ❌ | evaluation T4 (ai-master L305) |

**Konum: L1.** Gövde §1 ("aynı öneriyi herkese değil bu kullanıcıya göre yapmak") hedefi için
repo'daki tek gerçekleşmiş mekanizma **bağlam taşımada**dır.

### EK §4 — Fark tablosu (C-row'lar)

| # | Gövde/kayıt iddiası | Taze gerçek (2026-10-15) | Karar |
|---|---|---|---|
| P-C1 | §3/3: "Workflow oturum tercihini okur ve bağlama birleştirir (satır 55–56)" | B1 L55–L56 birebir | ✅ **teyit** |
| P-C2 | §3/4: "Anonim oturum dalı `'anonymous'` mevcut (satır 52)" | B1 L52 birebir | ✅ teyit |
| P-C3 | §3/5: "Engine bağlamı `user_id · session_id · mood · genre · history` (satır 52–56)" | B2 L52–L56 birebir (6 anahtar: history dahil) | ✅ teyit |
| P-C4 | §3/6: "Sonuç oturuma 1 saat (3600 sn) yazılır (satır 78)" | B1 L78 birebir | ✅ teyit |
| P-C5 | §1: "ai-master §1 sorumluluk tablosu (satır 37–42)" | (bkz. music-analysis EK MA-C2 — master referans hatası ailesi) | 🔄 P-1 ailesi |
| P-C6 | §2/A1: "ai-master §3 satır 261–262" | recommendation EK MA-C8 tipi belirsiz referans | 🔄 P-2 ailesi |
| P-C7 | §3/7: "gerçek zamanlı etkileşimle güncelleme servis kanıtı (URL)" | bu turda URL yeniden **tıklanmadı**; 2026-10-12 kaydı korunur | ✅ korunur — doğrulama tarihi 2026-10-12 |
| P-C8 | §3/8: "K006 kişiselleştirme teknoloji seçimi: UNKNOWN" | tam okumada seçim kodu **yok** (yalnız PHP çekirdek) | ✅ korunur |
| P-C9 | — (gövdede yok) | P-K1: `preferences` anahtarı hiç yazılmaz | **yeni bulgu** → P-P2 |
| P-C10 | — (gövdede yok) | P-K2: `array_merge` sırası isteği üstün kılar | **yeni bulgu** → P-P3 |
| P-C11 | — (gövdede yok) | P-K3: anonim oturum paylaşımı | **yeni bulgu** → P-P4 |
| P-C12 | — (gövdede yok) | P-K5: sinyaller yalnız prompt'a akar | **yeni bulgu** → P-P6 |

### EK §5 — Kapılar (P-P-1 … P-P-8)

| # | Kapı | Sahip | Durum |
|---|---|---|---|
| P-P-1 | Master "satır 37–42" + "261–262" referans hataları (P-C5 · P-C6) | vault-steward | 🔄 kayıt — gövde revizyonu ADR gerektirir |
| P-P-2 | P-K1: `preferences` yazma yolu yok → kişiselleştirme girdisi daima `[]` | backend + architect | ⏳ tercih yazım akışı kararı (K006↔K010) |
| P-P-3 | P-K2: `array_merge` sırası — istek mi oturum mu üstün? | architect | ⏳ tasarım kararı (UNKNOWN — niyet kodlanmamış) |
| P-P-4 | P-K3: `'anonymous'` oturum paylaşımı (gizlilik yüzeyi) | security + architect | ⏳ K009/K010 sınır ADR'si (gövde §2/3 · A4) |
| P-P-5 | P-K4: `session_id` okunur ama skora geçmez | architect | ⏳ recommendation R-P-5 ile aynı aile |
| P-P-6 | P-K5: kişiselleştirme hiçbir çıktıyı etkilemiyor (L1 olgunluk) | architect | ⏳ Faz-3 (ai-master L523 ailesi) |
| P-P-7 | P-K6: `last_recommendations` tek anahtar — ardışık ezilme | backend | ⏳ autoEQ desenine yaklaşma kararı |
| P-P-8 | R1–R4 (gövde): sahiplik · tercih yeri · anonim sınırı · gizlilik | architect | ⏳ dokunulmadı — ADR-097 K4 + sahiplik ADR'si |

### EK §6 — Kardeş dal kesişimleri (bu ek bulguları ışığında)

| Kardeş dal | Kesişim | Bu ekten çıkan işaretleme |
|---|---|---|
| recommendation | P-K4/P-K5 = recommendation EK R-K5 ile aynı bulgu (iki bağımsız okuma, aynı sonuç) | çapraz teyit |
| testing | `preferences` override davranışı (P-K2) yazılabilir **deterministik** test senaryosu | testing-kapsam §5 · K-2 ailesi |
| evaluation | kişiselleştirme kazancı (L5 olgunluk) ölçümü için A/B altyapısı yok | evaluation T4 blokajı |
| music-analysis | `energy` sinyali hem kişiselleştirme (B1 L69) hem `analyzeAudio` alanında | sinyal adı tutarlılığı — R-2 Faz-2'de sabitlenmeli |
| inference | `recommendationWorkflow` canlı uç | inference-dogrulama (≥500 ✅) |
| local-inference | offline kişiselleştirme (cihaz üstü tercih deposu) | local-inference-dogrulama (≥501 ✅) |
| model-management | kişiselleştirme modeli kaydı/sürümü | S3 (ai-master L184) |
| audio-intelligence | mood/genre sinyallerinin analizden üretilmesi | music-analysis §2 sınırı |
| K010/K009 | anonim oturum + tercih verisi gizlilik sınırı | P-P4 — bu dal **karar vermez** |

### EK §7 — Dal dosya envanteri (personalization · 2026-10-15)

| Dosya | Görev başı kayıt | Bu tur durum | Not |
|---|---|---|---|
| `index.md` | 61 (tahmini — ölçülmedi) | dokunulmadı | beyazliste dışı |
| `personalization-kapsam.md` | 83 | **ölçüm EK §9** | bu ek |
| `personalization-dogrulama.md` | ≥500 ✅ (önceki tur) | dokunulmadı | — |
| `personalization-anayasa.md` | ≥500 ✅ (önceki tur) | dokunulmadı | — |
| `personalization-bilesenler.md` | 126 | bu tur C grubu | — |
| `personalization-ornekler.md` | 333 (tahmini) | dokunulmadı | beyazliste dışı |
| `personalization-web-arastirma.md` | 135 | bu tur B grubu | — |

> Not: tahmini satır sayıları ölçülmemiştir; kesin sayı yalnız bu dosya için EK §9'dadır.

### EK §8 — Usul kararları

| # | Karar | Gerekçe |
|---|---|---|
| U-1 | PHP kaynak kod dosyaları bu turda **yalnız okundu** | beyazliste: yalnız `*.md` mimari vault |
| U-2 | Gövde §1/§2'deki hatalı master referansları **düzeltmedi** | append-only; düzeltme ADR + vault-steward işidir (P-P-1) |
| U-3 | P-K2 (`array_merge` ezmeyi) iddiası PHP'nin **belgelenmiş** `array_merge` davranışına dayanır | kanıt: B1 L55–L56 sırası + dil davranışı; "tasarım niyeti" UNKNOWN bırakıldı |
| U-4 | Yeni URL eklenmedi | bu turda erişilebilirlik doğrulaması yapılmadı → uydurma URL yasak |
| U-5 | Olgunluk matrisi (§3.8) bu ekte **tanımlandı** | ölçütler bu dalın sentezidir; anayasaya sonraki turda taşınabilir |

### EK §9 — NIHAÎ ÖLÇÜM

| Yöntem | Sonuç | Tarih |
|---|---|---|
| `read(offset=9999)` hata sayımı (bu ek sonrası, EK-2 öncesi) | **388 satır** (T7) | 2026-10-15 |
| Eksik (<500) mi? | EVET (388 < 500) → EK-2 eklendi | 2026-10-15 |

**Ek Authority:**
EK §0–§9 bu oturumda (2026-10-15) taze okumalarla (T1–T6) üretildi; gövde §1–§4 önceden
mekvcuttu ve silinmedi. git commit YOK · beyazliste dışı dosyaya yazım YOK · `index.md` /
`ai-master.md` / kardeş dosyalara DOKUNULMADI.

---

## EK-2 — 2026-10-15 ikinci ek (append-only · oturum deposu gerçeği)

> EK §9 ölçümünde dosya **388 satır** bulunmuş (<500) → bu ikinci ek kapı için eklenmiştir.
> EK §0–§9 silinmemiştir; bu ek onların devamıdır.

### EK-2 §1 — Taze okuma (bu ek)

| # | İşlem | Hedef | Sonuç |
|---|---|---|---|
| T7 | offset=9999 sayım | `personalization-kapsam.md` | **388 satır** (EK sonrası, bu ek öncesi) |
| T8 | tam okuma (L1–L130) | `shared/src/AI/MemorySystem.php` | **depo bellek-içi** — `$sessions` private array (L33–L34) |

### EK-2 §2 — Verbatim: oturum deposu (`shared/src/AI/MemorySystem.php` L23–L75, T8)

```php
23: class MemorySystem implements MemorySystemInterface
24: {
25:     private const HIERARCHY = [
26:         'brain'  => 5, // En yüksek
27:         'index'  => 4,
28:         'agents' => 4,
29:         'memory' => 3,
30:         'log'    => 1, // En düşük
31:     ];
32: 
33:     /** @var array<string, array<string, array{value: mixed, expires_at: ?int}>> */
34:     private array $sessions = [];
35: 
36:     /** @var array<string, array{value: mixed, expires_at: ?int}> */
37:     private array $persistent = [];
38: 
39:     /** @var array<string, array{value: mixed, level: string, expires_at: ?int}> */
40:     private array $cache = [];
41: 
45:     public function setSession(string $sessionId, string $key, mixed $value, int $ttl = 0): bool
46:     {
47:         $expiresAt = $ttl > 0 ? time() + $ttl : null;
48: 
49:         $this->sessions[$sessionId][$key] = [
50:             'value'      => $value,
51:             'expires_at' => $expiresAt,
52:         ];
53: 
54:         return true;
55:     }
60:     public function getSession(string $sessionId, string $key): mixed
61:     {
62:         if (!isset($this->sessions[$sessionId][$key])) {
63:             return null;
64:         }
65: 
66:         $entry = $this->sessions[$sessionId][$key];
67: 
68:         // TTL kontrolü
69:         if ($entry['expires_at'] !== null && $entry['expires_at'] < time()) {
70:             unset($this->sessions[$sessionId][$key]);
71:             return null;
72:         }
73: 
74:         return $entry['value'];
75:     }
```

### EK-2 §3 — Bulgu P-K7 — oturum deposu **bellek-içi**dir; istekler arası kişiselleştirme **sürekliliği yoktur**

| # | Gözlem | Kanıt |
|---|---|---|
| a | `$sessions` private **PHP array**'dir; dosya/DB yazımı yok | T8 L33–L34 |
| b | `$persistent` da aynı biçimde bellek-içi | T8 L36–L37 |
| c | `$cache` (remember) da aynı biçimde bellek-içi | T8 L39–L40 |
| d | `setSession` yalnız array'e yazar; flush/persist çağrısı yok | T8 L45–L55 |
| e | TTL `time()` ile çalışır — saat kaynağı PHP zamanı | T8 L47 · L69 |

**Mantıksal türetme (kapalı zincir):**

| # | Adım | Sonuç | Kanıt |
|---|---|---|---|
| 1 | PHP web isteği yaşam döngüsü sonunda tüm private property'ler yok olur | bellek-içi depo **istekler arası taşınmaz** | dil gerçeği + T8 L33–L40 (hiçbir kalıcılaştırma yok) |
| 2 | `recommendationWorkflow` istek N'de `setSession(…, 3600)` yazar | yalnız o isteğin nesnesinde görünür | EK B1 L78 (AIWorkflow) + T8 L45–L55 |
| 3 | istek N+1'de `getSession('…', 'preferences')` çağrılır | yeni nesne → `?? []` → **her zaman `[]`** | EK B1 L55 + T8 L62–L63 |
| 4 | `preferences` yazma yolu zaten yok (EK §3.2 P-K1) | iki bağımsız neden → tercih döngüsü **çift taraflı kapalı** | EK §3.2 + bu tablo |

**Sonuç:** kişiselleştirme girdisi (`preferences`) istekler arası **süreklilik taşımaz** —
tek istek içinde bile yazma yolu yoktur (P-K1). P-K8 = P-K1'in **altyapı** katmanı:
depo kalıcı olmadığından P-P2 kapısının çözümü yalnız "preferences yazmak" değil, **kalıcı
oturum deposu** (K007 / session katmanı) kararını da içerir. Kapı P-P2 bu ek ile **genişletildi**
(karar yeri değişmedi: backend + architect).

### EK-2 §4 — EK §3.8 olgunluk matrisi revizyonu (P-K8 sonrası)

| Seviye | Repo durumu (EK) | P-K8 sonrası |
|---|---|---|
| L1 Bağlam taşıma | ✅ | **kismen zayıflar**: taşınan bağlam istek içindedir; oturumdan gelen kısım daima `[]` |
| L2 Tercih döngüsü | ❌ | ❌ (yazma yok **ve** depo kalıcı değil — çift engel) |
| L3–L5 | ❌ | değişmez |

**Revize konum: L1 (zayıf).**

### EK-2 §5 — Ek kapılar

| # | Kapı | Sahip | Durum |
|---|---|---|---|
| P-P-2 (genişletme) | `preferences` yazma yolu + kalıcı oturum deposu kararı birlikte ele alınmalı | backend + architect + data | 🔄 EK §5'teki kayıt aynen geçerli; kapsam genişledi |
| P-P-9 | MemorySystem'in kalıcılaşma stratejisi (dosya · DB · Redis · APCu) — mevcut depo yalnız test/singleton senaryosunda anlamlıdır | architect | ⏳ yeni — K007 sınırı |

### EK-2 §6 — NIHAİ ÖLÇÜM (bu ek sonrası)

| Yöntem | Sonuç | Tarih |
|---|---|---|
| `read(offset=9999)` hata sayımı | **506 satır** | 2026-10-15 |
| Eksik (<500) mi? | **HAYIR — 506 ≥ 500 ✅** | 2026-10-15 |

**EK-2 Authority:**
EK-2 §1–§6 bu oturumda (2026-10-15) T7–T8 taze okumalarıyla üretildi; EK §0–§9 ve gövde
§1–§4 silinmedi. git commit YOK · beyazliste dışı dosyaya yazım YOK.
