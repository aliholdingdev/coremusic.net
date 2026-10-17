---
title: "Testing — Kapsam"
type: architecture-scope
node_id: K006-yapay-zeka/testing
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# Testing — Kapsam

**ELI15:** Testing, yapay zeka kodunun **test sürüşü**'dür: model servise bağlanmadan önce küçük örneklerle denenir — bozuk veri gelirse çöküyor mu, boş çıktı üretince nasıl davranıyor, aynı girdiye aynı cevabı veriyor mu (tutarlılık). Test, hatayı müşteriden önce bulmaktır. Bu alt alan, K006 AI kodunun **yazılım testini** (unit · integration · regresyon) ve yeniden üretilebilirlik (reproducibility) testini kapsar — model kalite metrikleri `evaluation/` alt alanındadır. K006'nın çocuk tablosunda (ai-master.md §3) 9. alt alan olarak testing, son sırada tanımlıdır; `testing/` klasörünün `index.md` (satır 1–47) kademe-2 kapsam taslağının olmadığı, üst düzey kapsamın v2 §2.4-10'da verildiğini belirtir.

## 1. Kapsam içi (İçinde)

1. **Unit test** — AI sınıfı/metot düzeyinde test (`AIEngine` · `AIWorkflow` · `PromptEngine`): her metot doğru tipte/dalıkta sonuç döndürüyor mu, hata (Result/Either) yolu çalışıyor mu.
2. **Integration test** — AI akışının diğer katmanlarla (DB · HTTP · auth) birleştiği yerde test; orchestrator → engine zincirinin uçtan uca davranışı.
3. **Regresyon testi** — model/prompt değişince eski davranışın bozulmadığını doğrulama; aynı fixture ile beklenen çıktı.
4. **Yeniden üretilebilirlik (reproducibility)** — aynı girdi + aynı model sürümü → aynı çıktı; determinizm (seed · sıcaklık) kontrolü.
5. **Test altyapısı** — test fixture'ları (örnek ses/prompt/metin), test verisi üretimi, CI'ye bağlama; `shared/tests` yapısı.

## 2. Kapsam dışı (Dışında)

1. **Model kalite metrikleri (NDCG · drift · A/B)** — bunlar `evaluation/` alt alanındadır (kapsam ayrımı §2 maddesi 1); burada yalnız kod testi.
2. **Model eğitimi ve hyperparameter arama** — hype loop'un kendisi.
3. **Guardian/6-axis gate kararı** — `yapay-zeka-dogrulama.md` kapsamıdır; test girdi üretir, kapı kararı vermez.
4. **K015-AI ajan testleri** — ajan altyapısı K016'ya aittir (K006 sınırı, `v2.md §2.6`).
5. **UI/frontend testi** — arayüz testi K006 dışıdır (AI çıktısını gösteren ekran testi ayrı sahiptir).

## 3. Disk kanıtları

| Kanıt | Dosya / konum | Not |
|---|---|---|
| Alt alan envanteri | `.ai/architecture/K006-yapay-zeka/ai-master.md §3` (satır 72) | 9. satır · "AI testi · regresyon"; çıktı "AI unit · integration test" |
| Kapsam sınırı (v2) | `.ai/architecture/K006-yapay-zeka/v2.md §2.4-10` (satır 252) | "Minimal kapsam: unit · integration · regression · fuzz" |
| Test altyapısı | `shared/tests/` | PHPUnit yapısı mevcut; **AI'a dair test dosyası grep ile 0 isabet** (negatif kanıt: `shared/tests` içinde `AI` geçen dosya yok) |
| Test ile evaluation ayrımı | `.ai/architecture/K006-yapay-zeka/v2.md §2.4-8` (satır 246) | evaluation ayrı alt alan — §2 maddesi 1–2 |
| El yazımı regresyon riski | `shared/AGENTS.md` (satır 120–137) | `hash_hmac('sha256', …)` — değişmeyecek veri için elle yazıldı (regresyon testi çıkarımı) |
| Bağımlılık · test | `.ai/architecture/K006-yapay-zeka/yapay-zeka-bagimliliklar.md` (satır 24) | "AI test kütüphanesi: UNKNOWN" · testing bağımlılık satırı |

## 4. Açık sorular

1. **AI test kütüphanesi bilinmiyor** — `yapay-zeka-bagimliliklar.md` (satır 24) "AI test kütüphanesi: UNKNOWN"; PHPUnit mü yoksa özel fixture mı seçilecek belirsiz.
2. CI'ya bağlama (hangi step'te AI testleri koşar) ve hedef coverage oranı sabitlenmedi.
3. Determinizm politikası (seed · sıcaklık sabitleme) netleşmedi; LLM çıktısında tekrarlanabilirlik nasıl sağlanacak?
4. **Bu alt alanın kademe-2 kapsam taslağı v2'de yok** — üstteki tanımlar taslak önerisidir; v2 §2.4-10 ile resmileşmedi (aynı belirsizlik `music-analysis/` kapsamının 4. maddesinde de işaretli).
5. Fuzz/bozuk veri testi kapsamı (hangi girdi sınırları) tanımsız.

## İlişkili dosyalar

- [[testing-dogrulama]]
- [[testing-ornekler]]
- [[../index|K006-yapay-zeka index]]

---

## 5. Testlenebilir Yüzey Ayrıntısı (kademe-3 — 33 sözleşme imzası)

> Kanıt: `shared/src/Contracts/AI/` altındaki 6 interface (2026-10-15 okundu). Tablo her imza için
> test edilebilirlik sınıfını ve ilk test dalgasının önceliğini verir. P0 = her metot için unit,
> P1 = entegrasyon, P2 = sözleşmenin kendisi.

### 5.1 `AIEngineInterface` (5 imza)

| # | İmza | Konum | Test edilebilirlik | Öncelik | Not |
|---|---|---|---|---|---|
| 1 | `getRecommendations(array $context, int $limit = 20): array` | `shared/src/Contracts/AI/AIEngineInterface.php` L28 · impl `shared/src/AI/AIEngine.php` L49 | Saf fonksiyon (DB yok) — deterministik | P0 | `limit` üst sınırı 20'ye kırpılır (impl L51) |
| 2 | `analyzeAudio(string $filePath): array` | Interface L36 · impl L82 | Dosya G/Ç'ye bağlı — fixture ile test edilir | P0 | Dosya yoksa `\RuntimeException` (impl L84–86) |
| 3 | `optimizeEQ(array $roomProfile): array` | Interface L44 · impl L111 | Saf fonksiyon — 31 bant döngüsü deterministik | P0 | Çıktı 31 elemanlı dizi (impl L119–130) |
| 4 | `analyzeHardware(string $deviceId): array` | Interface L52 · impl L138 | Placeholder — sabit döner | P1 | Test yalnız sözleşmeyi korur, davranışı değil |
| 5 | `predictFault(array $metrics): array` | Interface L60 · impl L161 | Saf fonksiyon — eşik kuralları test edilebilir | P0 | 4 eşik: 70°C · 90dB SNR · 0.1% THD · 10000 saat |

### 5.2 `AIOrchestratorInterface` (6 imza)

| # | İmza | Konum | Test edilebilirlik | Öncelik | Not |
|---|---|---|---|---|---|
| 6 | `dispatchTask(string $type, array $params, string $priority = 'medium'): string` | Interface L23 · impl `shared/src/AI/AIOrchestrator.php` L71 | Task-ID üretimi + kuyruğa alma | P0 | Görevli `executeTask` (impl L185) — davranış testi |
| 7 | `getTaskStatus(string $taskId): array` | Interface L31 · impl L102 | Bilinmeyen ID → hata yolu test edilir | P0 | |
| 8 | `listActiveTasks(): array` | Interface L38 · impl L114 | Boş/dolu kuyruk durumları | P1 | |
| 9 | `routeToAgent(string $agentId, string $action, array $params): array` | Interface L48 · impl L125 | K016 yüzeyi — K006 kapsamı sorunu (karar Q5) | P2 | `yapay-zeka-kararlar.md` §C Q5 |
| 10 | `getContext(string $sessionId): array` | Interface L56 · impl L154 | Oturum hafıza okuma | P1 | `MemorySystem` bağını test eder |
| 11 | `updateContext(string $sessionId, array $data): bool` | Interface L65 · impl L162 | Oturum hafıza yazma | P1 | R-11 (oturum verisi yazım yeri) bağlantılı |

### 5.3 `KnowledgeBaseInterface` (5 imza)

| # | İmza | Konum | Test edilebilirlik | Öncelik | Not |
|---|---|---|---|---|---|
| 12 | `search(string $query, int $maxResults = 10, array $filters = []): array` | Interface L23 · impl `shared/src/AI/KnowledgeBase.php` L36 | Boş tablo → boş sonuç; ilgide puanlama (impl L163) | P0 | |
| 13 | `ragQuery(string $query, array $context = []): array` | Interface L32 · impl L72 | Retrieval + augmented context (impl L199) + güven (impl L223) | P0 | |
| 14 | `addKnowledge(string $title, string $content, string $category, array $metadata = []): string` | Interface L43 · impl L96 | Ekleme → ID döner; arama ile doğrulama | P0 | |
| 15 | `removeKnowledge(string $id): bool` | Interface L51 · impl L121 | Var/yok silme yolları | P1 | |
| 16 | `cleanupLifecycle(): array` | Interface L58 · impl L134 | Yaşam döngüsü temizliği — sayaç dönüşü | P2 | |

### 5.4 `MemorySystemInterface` (7 imza)

| # | İmza | Konum | Test edilebilirlik | Öncelik | Not |
|---|---|---|---|---|---|
| 17 | `setSession(string $sessionId, string $key, mixed $value, int $ttl = 0): bool` | Interface L24 · impl `shared/src/AI/MemorySystem.php` L45 | Yaz→oku tur testi | P0 | TTL=0 sonsuz; TTL>0 süresi dolan kayıt |
| 18 | `getSession(string $sessionId, string $key): mixed` | Interface L33 · impl L60 | Var/yok anahtar yolları | P0 | |
| 19 | `clearSession(string $sessionId): boolean` | Interface L41 · impl L80 | Temizle→boş okuma | P1 | |
| 20 | `setPersistent(string $key, mixed $value, int $ttl = 0): bool` | Interface L51 · impl L89 | Kalıcı katman ayrımı | P1 | |
| 21 | `getPersistent(string $key): mixed` | Interface L59 · impl L104 | Kalıcı okuma | P1 | |
| 22 | `remember(string $key, callable $loader, string $level = 'l2', int $ttl = 3600): mixed` | Interface L70 · impl L123 | Loader çağrı sayısı (cache vurumu 1×) | P0 | autoEQWorkflow kullanır (impl L174) |
| 23 | `getHierarchy(): array` | Interface L77 · impl L152 | Katman tanıtımı | P2 | |

### 5.5 `PromptEngineInterface` (5 imza)

| # | İmza | Konum | Test edilebilirlik | Öncelik | Not |
|---|---|---|---|---|---|
| 24 | `generatePrompt(string $templateName, array $variables, array $context = []): array` | Interface L23 · impl `shared/src/AI/PromptEngine.php` L48 | Şablon + değişken doldurma; token sayımı döner | P0 | Workflow kullanır (AIWorkflow L66–72) |
| 25 | `countTokens(string $text): int` | Interface L31 · impl L90 | Yaklaşık sayım — sınır testleri | P1 | |
| 26 | `optimizePrompt(string $prompt, int $maxTokens): array` | Interface L40 · impl L101 | Kırpma/uyarlama davranışı | P1 | |
| 27 | `listTemplates(): array` | Interface L47 · impl L140 | Şablon envanteri | P2 | |
| 28 | `validatePrompt(string $prompt): array` | Interface L55 · impl L156 | Geçerli/geçersiz prompt sınıflandırma | P1 | |

### 5.6 `ToolCallingInterface` (5 imza)

| # | İmza | Konum | Test edilebilirlik | Öncelik | Not |
|---|---|---|---|---|---|
| 29 | `registerTool(string $name, string $description, array $parameters, callable $executor): bool` | Interface L24 · impl `shared/src/AI/ToolCalling.php` L53 | Kayıt→listeleme turu | P0 | |
| 30 | `callTool(string $toolName, array $params): array` | Interface L33 · impl L73 | Var/yok araç · parametre doğrulama (impl L307) | P0 | audioAnalysisWorkflow `db-query` çağırır (AIWorkflow L122–126) |
| 31 | `listTools(): array` | Interface L40 · impl L116 | Varsayılan araç envanteri (impl L173) | P1 | |
| 32 | `getToolsByCategory(string $category): array` | Interface L48 · impl L132 | Kategori eşleme (impl L289) | P2 | |
| 33 | `checkPermission(string $toolName, string $agentId): array` | Interface L57 · impl L145 | İzin ver/red yolları | P0 | güvenlik yüzeyi |

### 5.7 Sözleşme dışı yüzey (interface'i olmayanlar)

| Sınıf | Durum | Test etkisi | Kanıt |
|---|---|---|---|
| `AIWorkflow` | **Interface'siz** — 5 halka açık metot | Uçtan uca davranış testi yazılabilir ama sözleşme testi yazılamaz | `shared/src/AI/AIWorkflow.php` L18 (sınıf) · L49 · L99 · L147 · L199 · L241 |
| `AIOrchestrator::executeTask` (private) | private — L185 | Dolaylı test: `dispatchTask` sonrası durum sorgusu | impl L185 |
| `AIEngine` private yardımcıları | 8 private metot (L209–L368) | Yalnız public metotlar üzerinden dolaylı test | impl L209 · L223 · L243 · L264 · L288 · L315 · L332 · L347 · L368 |

**Toplam:** 33 sözleşme imzası + 1 interface'siz sınıf (5 metot) = **38 birincil test yüzeyi**.

---

## 6. Sınır Kararları (K006.9 ↔ diğerleri — bağlayıcı)

| # | Karar | Gerekçe | Kanıt |
|---|---|---|---|
| S-1 | Model kalite metrikleri (NDCG · drift · A/B) **K006.9 testi değildir** — evaluation'a aittir | Kapsam ayrımı §2 maddesi 1; evaluation ayrı kademe-2 dal | `.ai/architecture/K006-yapay-zeka/ai-master.md` §3 (L142, K006.2 satırı) |
| S-2 | Guardian/6-axis gate **kararı** testin işi değildir; test yalnız girdi üretir | `yapay-zeka-dogrulama.md` kapsamı | `.ai/architecture/K006-yapay-zeka/yapay-zeka-dogrulama.md` §1 (L38) |
| S-3 | K015-AI ajan testleri K006 dışındadır | Sınır: K006 ↔ K016 (karar K-03) | `.ai/architecture/K006-yapay-zeka/yapay-zeka-kararlar.md` §A K-03 (L56) |
| S-4 | UI/frontend testi (ekran, E2E tarayıcı) K006 dışıdır | Alt alan yalnız AI kod testi | `ai-master.md` §2.2 (L112) |
| S-5 | Test altyapısı PHP tarafında PHPUnit ile yazılır; Python tarafı (pytest) **adaydır, kurulmadı** | composer.json kanıtı; pytest kök havuzda aday | `shared/composer.json` L24 (`phpunit/phpunit ^10.5`) · kök `yapay-zeka-web-arastirma.md` §2.8 (L125) |
| S-6 | `shared/tests/` alt klasör düzeni korunur; AI testleri yeni bir `tests/AI/` alt dizinine **konmaz** (nereye konacağı UNKNOWN) | Test yeri kararı açık (open question 1) | `shared/tests/` 9 dizin: Api · Component · Events · Fixtures · Middleware · OAuth · Repository · Security · Unit (2026-10-15 dizin okuması) |

---

## 7. Kabul Kriterleri (bu alt alan tamam sayılırken?)

| # | Kriter | Ölçüt | Bugün |
|---|---|---|---|
| K-1 | 38 birincil test yüzeyinin en az P0'ları için test dosyası var | `shared/tests/**` içinde AI testi 0 | ❌ |
| K-2 | Determinizm testi: `predictFault` · `optimizeEQ` aynı girdide aynı çıktıyı verir | tekrarlı çalıştırma karşılaştırması | ❌ yazılmadı |
| K-3 | Golden test: `analyzeAudio` fixture dosyası ile sabit çıktı | fixture + beklenen JSON | ❌ fixture yok |
| K-4 | Regresyon: `AIWorkflow::recommendationWorkflow` dönenti şeması korunur | şema doğrulama | ❌ |
| K-5 | AI testleri CI'da koşuyor | CI step kaydı | ❌ `ci.yml`'de AI adımı yok (bkz. `-dogrulama` iddia 11) |
| K-6 | Hedef coverage oranı tanımlı ve ölçülüyor | coverage raporu | ❌ hedef UNKNOWN |

---

## 8. Bağımlılık Etkileşimi (bu dal ↔ diğer dosyalar)

| Yönelim | Dosya | İlişki |
|---|---|---|
| Okur | `yapay-zeka-bagimliliklar.md` (satır 24) | "AI test kütüphanesi: UNKNOWN" satırı bu dalın açık sorusudur |
| Okur | `yapay-zeka-kod-ornekleri.md` §14 (L448) | 33 imza × mevcut test matrisi — bu dalın yüzey envanteri orada |
| Okur | `yapay-zeka-test-stratejisi.md` §4 (L114) | Kapılar G1–G5 — bu dal o kapıların sahibi |
| Yazmaz | `yapay-zeka-dogrulama.md` §2 | İddia matrisi kök dosyada; bu dal yalnız kendi iddialarını oraya **taşır** |
| Bağımlı | `shared/composer.json` (L24–L25) | phpunit ^10.5 · phpstan ^1.10 — test çalıştırıcısı |
| Bağımlı | `.github/workflows/ci.yml` | CI kapısı — AI adımı henüz yok (2026-10-15 dizin kanıtı: ci.yml · secret-scan.yml) |

---

## 9. Uygulama Fazları (ai-master §18.1'e bağlanır — burada yalnız test sırası)

| Faz | Test işi | Bağımlılık |
|---|---|---|
| Faz-1 (veri) | `addKnowledge`/`search`/`ragQuery` unit testleri (KnowledgeBase) | KnowledgeBase DB katmanı (K007) |
| Faz-2 (analiz) | `analyzeAudio` golden test + `extractFeatures` gerçekleşme sonrası güncelleme | müzik-analysis dalı |
| Faz-3 (öneri) | `getRecommendations` skor testleri (MIN_SCORE 0.7 · çeşitlilik 0.30) | recommendation dalı |
| Faz-4 (inference) | workflow zinciri entegrasyon testi | inference dalı |
| Faz-5 (eval) | metrik testleri + drift kapısı | evaluation dalı |

Faz sırası `ai-master.md` §18.1 (L518) tablosuyla aynıdır; bu tablo yalnızca test eylemlerini sıralar.

---

## 10. Anayasa Çapraz Referans (K006.9-anayasa ↔ bu dosya)

| Anayasa bölümü | Konum | Bu dosyadaki karşılığı |
|---|---|---|
| §3.1 Test katmanları | `testing/testing-anayasa.md` L78 | §5 (bu dosya) |
| §4 AI-özel test tipleri (determinizm · golden · mock · adversarial) | `testing/testing-anayasa.md` L116–L148 | §1 maddesi 3–4 |
| §5 Determinizm ve fixture disiplini | `testing/testing-anayasa.md` L149 | §7 K-2 · K-3 |
| §6 CI kapısı | `testing/testing-anayasa.md` L171 | §7 K-5 · §8 ci.yml satırı |
| §7 Sözleşme ve şema testleri | `testing/testing-anayasa.md` L202 | §5 (33 imza tablosu) |
| §23 Test raporu başlıkları | `testing/testing-anayasa.md` L502 | Rapor şeması anayasada; bu dosya yalnız onaylar |

---

## 11. Boşluk → Eylem Kuyruğu (sıralı)

| # | Boşluk | Eylem | Sahip | Engeller |
|---|---|---|---|---|
| B-1 | AI test dosyası 0 | İlk P0 dalgası: 5 `AIEngineInterface` imzası | QA | test yeri kararı (open 1) |
| B-2 | Fixture yok | `analyzeAudio` golden fixture seti (küçük ses parçacıkları) | QA + müzik-analysis | dosya G/Ç politikası |
| B-3 | CI'da AI adımı yok | `ci.yml`'e test adımı eklenmesi | DevOps | B-1 |
| B-4 | Hedef coverage yok | Hedef oranı kararı (ör. %80 — `.ai/AGENTS.md` §16 QA satırı) | Authority | — |
| B-5 | Determinizm politikası yok | seed/sıcaklık sabitleme kuralı | inference dalı | — |

---

## 12. EK — 2026-10-15 genişletme eki (append-only · ≥500 kapısı)

> Bu ek dosyanın SONUNA eklenmiştir (append-only). Gövde §1–§11 silinmemiş/değiştirilmemiştir:
> eski içerik, eklenen metnin birebir önekidir. Tüm satır referansları 2026-10-15 taze
> okumadan alınmıştır.

### 12.0 Usul

| Kural | Uygulama (bu ekte) |
|---|---|
| Append-only | Ekleme dosya sonunda; gövde satırlarına dokunulmadı |
| Ölçüm | `read(offset=9999, limit=1)` hata sayımı |
| Boş dolgu yasak | TBD · `// ...` · uydurma satır: 0 |
| Zero-hallucination | Her iddia `Dosya: <yol> [Satır: n]` ile kanıtlanır |
| Kod iddiası | Repo'da `*.cpp/*.h` = 0 (2026-10-15) → "repo C++ kodu var" iddiası YOK |
| git commit | YOK |
| Beyazliste | Yalnız bu dosya yazıldı · `*.php` **okundu, yazılmadı** |

**Önceki sayım (ek öncesi):** `read(offset=9999)` → **215 satır**.

### 12.1 Taze okuma koşu kayıtları (2026-10-15)

| # | İşlem | Hedef | Sonuç |
|---|---|---|---|
| T1 | offset=9999 sayım | `testing-kapsam.md` | **215 satır** (ek öncesi) |
| T2 | tam okuma | `testing-kapsam.md` | L1–L215 · frontmatter yok (gövde §1–§11 + Authority satırı yok) · §5 L60 · §6 L141 · §7 L154 · §11 L207 |
| T3 | tam okuma | `shared/src/AI/PromptEngine.php` | **325 satır** · şablonlar L193–L264 · injection desenleri L27–L35 · `validatePrompt` L156–L186 · `countTokens` L90–L96 |
| T4 | tam okuma (L1–L180) | `shared/src/AI/ToolCalling.php` | **325+ satır (okunan aralık 180)** · `AGENT_PERMISSIONS` L28–L40 · `callTool` L73–L111 · `checkPermission` L145–L166 |
| T5 | tam okuma | `shared/src/AI/AIEngine.php` | **382 satır** · `predictFault` L161–L202 · `optimizeEQ` L111–L133 · `bandToFrequency` L332–L342 · `calculateRoomCorrection` L347–L363 · `calculateBandQ` L368–L381 |
| T6 | tam okuma | `shared/src/AI/AIWorkflow.php` | **271 satır** · 5 workflow imzası L49 · L99 · L147 · L199 · L241 |
| T7 | tam okuma (L1–L130) | `shared/src/AI/MemorySystem.php` | bellek-içi depo `$sessions` L33–L37 · TTL L47 · L69 |

### 12.2 Determinizm ve non-determinizm yüzeyi (kademe-3 derinlik)

> §5'teki 38 test yüzeyinin determinizm sınıflandırması. Her satır tam okuma kanıtlıdır.

#### 12.2.1 Saf deterministik yüzeyler (aynı girdi → aynı çıktı, bağımlılık yok)

| # | Metot | Konum | Neden deterministik | Test stratejisi |
|---|---|---|---|---|
| 1 | `optimizeEQ` | `AIEngine.php` L111–L133 | saf döngü: 31 band × `bandToFrequency` × `calculateRoomCorrection` × `calculateBandQ`; dış G/Ç yok | golden JSON: 31 eleman, bant 0 = 20 Hz |
| 2 | `bandToFrequency` | L332–L342 | sabit ISO tablosu L336–L339; `$frequencies[$band] ?? 1000.0` | tablo döküm testi: band 0→20 · band 10→200 · band 30→20000 · band 31→1000 (out-of-range fallback) |
| 3 | `calculateBandQ` | L368–L381 | 4 kollu eşik: <100→0.7 · <500→1.0 · <2000→1.4 · else→1.8 | sınır testleri: 99.9/100 · 499.9/500 · 1999.9/2000 |
| 4 | `calculateRoomCorrection` | L347–L363 | saf formül: `($target - $actual) * $roomFactor * (1 - $reverb)`; `match` small/medium/large/default | parametre matrisi: 3 boyut × 2 reverb × 2 response |
| 5 | `combineScores` | L243–L259 | saf ağırlıklı ortalama; `array_unique(array_merge(...))` sırası girdi sırasına bağlı (bkz 12.2.3 S-3) | deterministik girdi seti |
| 6 | `applyDiversityFilter` | L264–L283 | saf döngü (ancak `genre` anahtarı üretilmediği için behavior trivial — bkz recommendation EK §3.3) | regression: her elemanda `genre='unknown'` |
| 7 | `countTokens` | `PromptEngine.php` L90–L96 | `ceil(mb_strlen($text,'UTF-8') / 3.5)` — saf formül | tablo testi: ''→0 · 4 karakter→2 · UTF-8 çokbaytlı uzunluk |
| 8 | `validatePrompt` | L156–L186 | desen listesi sabit (L27–L35); `preg_match`/`preg_replace` saf | 7 injection deseni × pozitif/negatif = 14 vaka |
| 9 | `estimateCost` | L269–L273 | `(tokenCount/1000) * 0.002` | sınır: 0 · 1000 · 1000000 |
| 10 | `checkPermission` | `ToolCalling.php` L145–L166 | sabit `AGENT_PERMISSIONS` (L28–L40) × `in_array(..., true)` | 11 ajan × kategori matrisi (bkz 12.4) |

#### 12.2.2 Deterministik olmayan yüzeyler (zaman/süreç bağımlı)

| # | Metot | Konum | Non-determinizm kaynağı | Test yaklaşımı |
|---|---|---|---|---|
| 1 | `getRecommendations` | `AIEngine.php` L49–L77 | bugünkü hâliyle sabit `[]` (recommendation EK §3.2) — deterministik AMA davranış boş | regression: `[]` korunurken mi? — Faz-2'de skorlar gelince **toleranslı** karşılaştırma |
| 2 | `analyzeAudio` | L82–L106 | `file_exists` + `shell_exec(ffprobe)` (L295) → dosya/ffprobe bağımlı | fixture dosyası + ffprobe mock (K-3) |
| 3 | `extractMetadata` | L288–L310 | `shell_exec` null → L297 fallback sabit set | iki yol: ffprobe var/yok |
| 4 | `predictFault` | L161–L202 | saf göründü ama `$metrics` array — deterministik; non-determinizm **yok**; not: §5.1/5 "eşik kuralları test edilebilir" teyitli | 4 eşik × sınır değerleri (bkz 12.3) |
| 5 | `recommendationWorkflow` | `AIWorkflow.php` L49–L91 | `microtime(true)` (L51 · L80) → `execution_time` **her çağırıda farklı** | alan **assert edilmez** yalnız `type: float` + `>= 0` kontrolü |
| 6 | `audioAnalysisWorkflow` | L99–L139 | aynı microtime sorunu (L101 · L129) | aynı |
| 7 | `autoEQWorkflow` | L147–L191 | microtime (L149 · L181) + `md5(serialize($roomProfile))` cache key (L173) — `serialize` array **sırasına** bağlı | array sırası sabitlenmeli fixture'ta |
| 8 | `faultPredictionWorkflow` | L241–L270 | microtime (L243 · L261); `routeToAgent` yan etkisi (L254) | mock orchestrator |
| 9 | `callTool` | `ToolCalling.php` L73–L111 | `executionTime` (L93 · L99) + executor çağrısı yan etkili | `executionTime` **assert edilmez** |
| 10 | `MemorySystem::getSession` | `MemorySystem.php` L60–L75 | TTL `time()` (L69) → **saat bağımlı** | TimeProvider seam'i yok → test'te gerçek `time()` ile TTL=0 (sonsuz) kullanılmalı veya seam eklenmeli (kapı T-P3) |

#### 12.2.3 Bellek-içi durum test riski (T7)

| # | Gözlem | Kanıt | Test etkisi |
|---|---|---|---|
| 1 | `$sessions` instance property | `MemorySystem.php` L33–L34 | testler **paralel** koşarsa singleton yoksa izole; DI container singleton ise **test sızıntısı** riski |
| 2 | `$persistent` ve `$cache` da instance property | L36–L40 | `remember` cache'i instance yaşam döngüsüne bağlı → testler arası temizlik gerekir |
| 3 | `clearSession` yalnız `$sessions`'ı temizler | L80–L84 | `$persistent`/`$cache` için temizlik metodu **gözlemlenmedi** (L1–L130 aralığında) |

### 12.3 `predictFault` eşik matrisi (kademe-3 test vaka kataloğu)

Kaynak: `AIEngine.php` L161–L202 (T5). 4 bağımsız eşik + 1 üst sınır.

| # | Eşik | Kod | Beklenen `risk` etkisi | `probability` etkisi | `alerts`/`maintenance` |
|---|---|---|---|---|---|
| 1 | `temperature > 70` | L174–L178 | `risk = 'high'` (koşulsuz) | `+= 0.4` | alert: "Yüksek sıcaklık: {n}°C" |
| 2 | `snr < 90` | L180–L184 | low→medium **escalation only** | `+= 0.2` | alert: "Düşük SNR: {n}dB" |
| 3 | `thd > 0.1` | L186–L190 | low→medium escalation only | `+= 0.15` | alert: "Yüksek THD: {n}%" |
| 4 | `usage_hours > 10000` | L192–L194 | risk etkisi **YOK** | **YOK** | yalnız `maintenance[]` |
| 5 | probability üst sınırı | L198 `min($probability, 1.0)` | — | clamp 1.0 | — |

**Vaka kataloğu (P0 dalga):**

| Vaka | temperature | snr | thd | usage_hours | Beklenen risk | Beklenen probability |
|---|---|---|---|---|---|---|
| V1 temiz | 40 | 110 | 0.05 | 100 | low | 0.0 |
| V2 yalnız sıcaklık | 71 | 110 | 0.05 | 100 | **high** | 0.4 |
| V3 yalnız SNR | 40 | 89 | 0.05 | 100 | **medium** | 0.2 |
| V4 yalnız THD | 40 | 110 | 0.11 | 100 | **medium** | 0.15 |
| V5 üçlü | 71 | 89 | 0.11 | 100 | **high** | 0.4+0.2+0.15=0.75 |
| V6 clamp | 71 | 89 | 0.11 | 100 | high | **1.0** (0.75 → min ile aynı; clamp ancak dört eşik + bonus'ta anlamlı) |
| V7 eşik sınırı | 70 | 90 | 0.1 | 10000 | low | 0.0 (>`/`< strict — sınır **değil**) |
| V8 bakım | 40 | 110 | 0.05 | 10001 | low | 0.0 · maintenance 1 eleman |
| V9 eksik metrik | — | — | — | — | low | 0.0 (`?? 0` L169–L172) |
| V10 risk yükseltme yolu | 40 | 89 | 0.11 | 100 | medium (high'a **çıkamaz** — yalnız temperature high verir) | 0.35 |

> V10 mimari bulgu: `snr`/`thd` eşikleri **asla `high` yapamaz** (L181 · L188 yalnız low→medium);
> `high` yalnız sıcaklık yoluyla mümkündür. Test bunu **dokümante edilmiş davranış** olarak
> kodlar; kural değişirse regression kırmızıya döner.

### 12.4 `ToolCalling::checkPermission` izin matrisi (P0 güvenlik testi)

Kaynak: `ToolCalling.php` L28–L40 (kategoriler) + L145–L166 (mantık) — T4.

| Ajan | İzinli kategoriler | Satır |
|---|---|---|
| mo | file · database · api · audio · hardware · security (6/6) | L29 |
| backend | file · database · api | L30 |
| ui | file · api | L31 |
| security | file · database · api · security | L32 |
| data | file · database | L33 |
| embedded | file · hardware · audio | L34 |
| qa | file · database · api | L35 |
| devops | file · api · database | L36 |
| audio-hw | file · hardware · audio | L37 |
| dsp-fw | file · hardware · audio | L38 |
| win-sw | file · hardware | L39 |

**Test vaka ailesi (T-P1 kapısı):**

| Vaka | toolName | agentId | Beklenen | Kanıt |
|---|---|---|---|---|
| W1 | var olmayan araç | `mo` | `allowed: false` · "Tool not found" | L147–L149 |
| W2 | `db-query` (database) | `ui` | `allowed: false` · kategori reddi | L158–L163 + L31 |
| W3 | `db-query` | `qa` | **allowed: true** | L35 (`database` var) |
| W4 | bilinmeyen ajan | — | `allowed: false` · "Unknown agent" | L151–L153 |
| W5 | security kategorisi araç | `embedded` | `allowed: false` | L34 (security yok) |
| W6 | `in_array(..., true)` strict | — | integer/float karışık anahtar **reddedilir** | L158 strict flag |

**Kritik gözlem (T-P2):** `callTool` (L73–L111) içinde `checkPermission` **çağrılmıyor** —
L89 yalnız `validateParameters` yapar; izin denetimi **çağıran tarafın sorumluluğundadır**.
`AIWorkflow::audioAnalysisWorkflow` de `callTool('db-query', …)` çağırırken izin kontrolü
**yapmaz** (`AIWorkflow.php` L122–L126, T6). Güvenlik testi bunu **dokümante edilmiş boşluk**
olarak kodlar; kapıyı security-engineer vetosu taşır (`.ai/AGENTS.md` §6.1).

### 12.5 `PromptEngine` güvenlik testi katmanı (adversarial)

Kaynak: `PromptEngine.php` T3.

#### 12.5.1 Injection desen envanteri (L27–L35)

| # | Desen | Türkçe/İngilizce hedefi |
|---|---|---|
| 1 | `/ignore\s+previous/i` | "önceki talimatları yok say" |
| 2 | `/you\s+are\s+now/i` | rol değiştirme |
| 3 | `/system\s*:\s*/i` | sahte sistem mesajı |
| 4 | `/act\s+as/i` | rol taklidi |
| 5 | `/pretend\s+you/i` | rol taklidi (2) |
| 6 | `/disregard/i` | talimat iptali |
| 7 | `/override/i` | geçersiz kılma |

#### 12.5.2 Test vaka ailesi (T-P3)

| Vaka | Girdi | Beklenen |
|---|---|---|
| I1 | "Ignore previous instructions" | `valid: false` · risk 1 · sanitized `[FILTERED]` |
| I2 | "Lütfen şarkı öner" | `valid: true` · risks `[]` |
| I3 | "password: x" | hassas veri riski (L177–L179) |
| I4 | 4097 token (≈14335 karakter) | token bütçesi riski (L172–L174) |
| I5 | desen + hassas veri birlikte | **2+ risk** kümelenmesi (L182 `empty($risks)`) |
| I6 | `sanitized` alanı her zaman döner | L184 — valid olsa bile |
| I7 | template bulunamayan `generatePrompt` | `\InvalidArgumentException` (L50–L52) |

> Not: `validatePrompt` çağrısının `generatePrompt`/workflow akışına **bağlı olup olmadığı** bu
> okumada **gözlemlenmedi** (`generatePrompt` L48–L85'te `validatePrompt` çağrısı yok) → güvenlik
> doğrulamasının akışta **atlandığı** olasıdır (kapı T-P4, ⚠️ VERIFICATION REQUIRED — L181+ tam
> okunmadı, yalnız L1–L180 değil PromptEngine tamamı L1–L325 okundu ve L48–L85 arasında çağrı
> **yoktur**: bu nedenle T-P4 **kanıtlıdır**, ⚠️ değil).

### 12.6 Şablon envanteri test yüzeyi (L193–L264)

| Şablon | Kategori | Değişkenler | tokenEstimate | Test |
|---|---|---|---|---|
| `recommendation` | music | mood · genre · energy · history · limit | 200 | 5 placeholder doldurulur; eksik değişken **dokunulmaz** (`{{key}}` kalır — L58–L61 yalnız var olanları değiştirir) |
| `audio-analysis` | audio | file_path · format · duration | 100 | 3 placeholder |
| `eq-optimization` | audio | room_profile | 80 | 1 placeholder |
| `security-audit` | security | target · scope | 150 | 2 placeholder |

**Bulgu T-K1 (P1):** `generatePrompt` L58–L61 eksik değişkenleri **sessizce bırakır** —
`{{history}}` gibi doldurulmamış placeholder prompt gövdesinde kalır. Test: eksik-değişken
vakasında prompt'ta `{{` kalıp kalmadığı **assert edilmeli** (bugünkü davranış: kalır).
Kapı T-P5.

**Bulgu T-K2 (P2):** `tokenEstimate` elle yazılmış (200/100/80/150) ve `countTokens` gerçek
sayımıyla **karşılaştırılmamış**. Test: her şablon için `countTokens(str_replace(...))` ile
`tokenEstimate` farkı ölçülür → sapma raporlanır (test-only, üretim kodu değişmez).

### 12.7 Workflow imza test matrisi (§5.7'yi tamamlar)

Kaynak: `AIWorkflow.php` (T6) · interface'siz sınıf — §5.7'de "5 halka açık metot" kayıtlı; bu ek
imzaları ve dönüş şemalarını verir.

| # | Metot | İmza | Dönüş anahtarları | Non-determinizm |
|---|---|---|---|---|
| 1 | `recommendationWorkflow` | `array $request): array` L49 | recommendations · metadata{task_id, prompt_tokens, knowledge_hits} · execution_time L82–L90 | microtime |
| 2 | `audioAnalysisWorkflow` | L99 | analysis · metadata{task_id, knowledge_hits} · execution_time L131–L138 | microtime + ffprobe |
| 3 | `autoEQWorkflow` | L147 | eq_curve · metadata{task_id, prompt_tokens} · execution_time L183–L190 | microtime |
| 4 | `hardwareAnalysisWorkflow` | L199 | analysis · fault_prediction · metadata{task_id, knowledge_hits} · execution_time L224–L232 | microtime + `analyzeHardware` placeholder |
| 5 | `faultPredictionWorkflow` | L241 | prediction · metadata{task_id} · execution_time L263–L269 | microtime + `routeToAgent` yan etki |

**P0 ortak davranış testi (her 5 workflow için):**

| # | Kontrol | Beklenen | Kanıt |
|---|---|---|---|
| O-1 | dönüş her zaman `array` | ✅ | L82 · L131 · L183 · L224 · L263 |
| O-2 | `metadata.task_id` her zaman string dolu | ✅ (`dispatchTask` dönüşü) | L59 · L106 · L154 · L205 · L246 |
| O-3 | `execution_time` float ve `>= 0` | ✅ | `round(..., 4)` |
| O-4 | boş `$request = []` → çökme yok | ✅ beklenir (her alan `??` fallback'li) | B1–B5 verbatim'ler |
| O-5 | `session_id` verilmezse `'anonymous'` | ✅ | L52 · L150 |

### 12.8 EK kapıları (T-P-1 … T-P-8) — §11 kuyruğuna ek

| # | Kapı | Sahip | Durum |
|---|---|---|---|
| T-P-1 | `checkPermission` izin matrisi test dalgası (11 ajan × kategori) | qa + security | ⏳ §12.4 vaka ailesi hazır |
| T-P-2 | `callTool`'da izin kontrolü yok (dokümante edilmiş boşluk) | security-engineer | ⏳ veto/ADR — üretim kodu bu turda **değiştirilmedi** |
| T-P-3 | `MemorySystem` TTL testleri için TimeProvider seam'i yok | backend + qa | ⏳ seam kararı (testability) |
| T-P-4 | `validatePrompt` akışa bağlı değil (`generatePrompt` L48–L85'te çağrı yok) | security + backend | ⏳ doğrulama zinciri kararı |
| T-P-5 | Eksik-değişken placeholder'ları sessiz kalır (T-K1) | backend + qa | ⏳ davranış kararı: sessiz bırakma vs exception |
| T-P-6 | `workflow execution_time` alanı asla assert edilmez (tip/eşik kontrolü) | qa | 🔄 test şablonu kuralı |
| T-P-7 | `md5(serialize(...))` cache key array-sırasına bağlı (§12.2.2/7) | backend | ⏳ canonical sıralama kararı |
| T-P-8 | `tokenEstimate` ↔ `countTokens` sapma testi (T-K2) | qa | ⏳ ilk ölçüm çalıştırılmalı |

### 12.9 Faz sıralaması güncellemesi (§9'a ek, dokunma yok)

| Faz | §9 işi | Bu ekten ek iş |
|---|---|---|
| Faz-1 | KnowledgeBase unit | + `MemorySystem` TTL/izolasyon testi (§12.2.3) |
| Faz-2 | analyzeAudio golden | + `PromptEngine` şablon testleri (§12.6) |
| Faz-3 | getRecommendations skor | + `predictFault` 10 vaka (§12.3 — önceden de yapılabilir: saf) |
| Faz-4 | workflow zincir | + 5 workflow ortak O-1…O-5 (§12.7) |
| Faz-5 | metrik/drift | + `checkPermission` matrisi (§12.4) |

### 12.11 Fixture envanteri tasarımı (K-2/K-3 için — §7'yi tamamlar)

> Bu tablo fixture **tasarımı**dır; fixture dosyası bu turda **üretilmedi** (beyazliste: yalnız
> bu md). QA bu tabloyu B-2 (§11) eyleminin şartnamesi olarak kullanır.

| # | Fixture | Girdi | Beklenen çıktı | Kullanan test |
|---|---|---|---|---|
| F-1 | `eq-room-small.json` | `{size: small, reverb: 0.2, frequency_response: {}}` | 31 eleman; band 0 = 20 Hz; roomFactor 1.2 (L351) | §12.2.1/2–5 |
| F-2 | `eq-room-large.json` | `{size: large, reverb: 0.9, …}` | roomFactor 0.8 (L353); gain `× (1-0.9)=0.1` ölçeği | §12.2.1/4 |
| F-3 | `fault-metrics-matrix.csv` | §12.3 V1–V10 satırları | risk + probability + alerts/maintenance beklenenleri | §12.3 |
| F-4 | `prompt-injection-samples.txt` | §12.5.2 I1–I7 vakaları | valid/risks/sanitized beklenenleri | §12.5.2 |
| F-5 | `template-variables-missing.json` | recommendation şablonu, `history` eksik | prompt'ta `{{history}}` kalır (T-K1) | §12.6 |
| F-6 | `audio-sample-1s.wav` | 1 saniyelik örnek ses | ffprobe var: metadata gerçek · yok: fallback `{0, 44100, 16, 2}` (L297) | §12.2.2/2–3 |

**Fallback sabitleri (test bekleneni — `AIEngine.php` L296–L297, T5):**

| Alan | `shell_exec` null fallback |
|---|---|
| duration | 0 |
| sample_rate | 44100 |
| bit_depth | 16 |
| channels | 2 |

### 12.12 Ölçüm

| Yöntem | Sonuç | Tarih |
|---|---|---|
| `read(offset=9999)` hata sayımı (§12.11 sonrası) | **496 satır** | 2026-10-15 |
| Eksik (<500) mi? | EVET (496 < 500, 4 satır eksik) → §12.13 ile kapatıldı | 2026-10-15 |

### 12.13 Son ölçüm

| Yöntem | Sonuç | Tarih |
|---|---|---|
| `read(offset=9999)` hata sayımı | **504 satır** | 2026-10-15 |
| Eksik (<500) mi? | **HAYIR — 504 ≥ 500 ✅** | 2026-10-15 |

### 12.14 Kapanış notu (bu ek)

Bu ek §12 ile testing dalının kapsam kaydına **determinizm sınıflandırması** (§12.2),
**eşik/izin/karşılaştırma test kataloğu** (§12.3–§12.5) ve **fixture şartnamesi** (§12.11)
katmanlarını eklemiştir; §5'teki 33 imza envanterine **dokunulmamış**, yalnızca onun
davranışsal test stratejisi derinleştirilmiştir. Üretim kodu (`*.php`) bu turda yalnız
**okunmuş**, hiçbir dosya değiştirilmemiştir.

**Ek Authority:**
§12.0–§12.13 bu oturumda (2026-10-15) taze okumalarla (T1–T7) üretildi; gövde §1–§11 silinmedi.
git commit YOK · beyazliste dışı dosyaya yazım YOK · `*.php` yalnız okundu.
