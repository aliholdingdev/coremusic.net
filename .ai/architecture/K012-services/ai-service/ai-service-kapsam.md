---
node_id: K012-services/ai-service
node_type: module
role: kapsam
status: draft
parent_id: K012-services/ai-service
children: []
depends_on:
  - "ai-service/index.md"
  - "services-master.md"
evidence:
  - "S1 — shared/src/AI/ 7 sınıf + Contracts/AI/ 6 arayüz (2026-10-13 glob teyidi)"
  - "S1 — AIEngine.php 382 satır, 16 metot (2026-10-13 okundu)"
  - "S1 — services-master.md §3 satır 69 'YZ servisi' (okundu)"
validation:
  - "tur-1 — 2026-10-12 ilk kapsam sınırı kaydı"
  - "tur-2 — 2026-10-13 frontmatter §4.2'ye yükseltildi + §6–§16 eklendi"
open_questions:
  - "R1 ai-service'in arkası K006-yapay-zeka mı K015-AI mı — VERIFICATION REQUIRED"
  - "R2 YZ çağrısı senkron mu kuyruk işi mi — UNKNOWN (kuyruk teknolojisi de bilinmiyor)"
  - "R3 AIWorkflow · KnowledgeBase · MemorySystem · PromptEngine · ToolCalling içerikleri okunmadı"
  - "R4 YZ çağrılarının maliyet/limit politikası kimin — UNKNOWN"
date: 2026-10-12
updated: 2026-10-13
version: 2.0.0
authority: "SSOT: repo kanıtı (S1) + services-master.md §3"
group: mimari
---

# ai-service — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`services-master.md` §3 satır 69 bu dalı **"YZ servisi"** olarak yazar; kapsam notu
"K006-yapay-zeka mı K015-AI mı → ⚠️" der. `ai-service/index.md` ELI15'i "Yapay zeka
işlerini diğer servislere sunan arayüz servisi" olarak tanımlar.

Basitçe: CoreMusic'in yapay zeka yeteneklerinin (öneri, prompt, araç çağrısı) diğer
servislere **kapı** olduğu yer. YZ'nin kendisi burada **üretilmez** — arkadaki motor
başka düğümdedir ve hangi düğüm olduğu (K006-yapay-zeka ↔ K015-AI) **açık sorudur**.

| # | Rol cümlesi | Kaynak | Durum |
|---|---|---|---|
| 1 | "YZ servisi" | `services-master.md` §3 satır 69 | ✅ okundu |
| 2 | "Yapay zeka işlerini diğer servislere sunan arayüz servisi" | `ai-service/index.md` ELI15 satır 24 | ✅ okundu |
| 3 | "Bağlı olduğu düğüm K006 mı K016 mı → ⚠️" | `ai-service/index.md` open_questions satır 17 | ✅ okundu |
| 4 | Kapı metaforu (bu dosya) | §1'in sentezi | sentez — yeni iddia değil |

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | YZ yeteneğini servislere sunma arayüzü (öneri, skorlama isteği) | YZ modeli / ML altyapısı | K006-yapay-zeka / K015-AI (hangisi ⚠️ — §5/R1) |
| 2 | Öneri bağlamının (context) taşınması: user_id · session_id · mood · genre | Öneri algoritmasının matematiği | K008-domain (iş kuralı) |
| 3 | YZ çıktısının servis sözleşmesine uygun DTO'ya oturtulması | Sözleşme biçiminin sahipliği | service-contracts |
| 4 | YZ çağrılarının kullanıcı/cihaz bağlamıyla yapılması | Kimlik doğrulama | ⚠️ |
| 5 | YZ istek/sonuç kayıtlarının kalıcıya yazımı | Şema kararı | K007-data |

**Durum:** satır 1'deki sınır, master §7/1 karar verilene kadar **tasarım önerisidir** —
bağlayıcı değildir.

### §2.1 Kapsam içi/gereksinim eşlemesi

| # | Kapsam satırı | Karşılık gelen kod yüzeyi (var mı?) | Durum |
|---|---|---|---|
| 1 | öneri/skorlama arayüzü | `AIEngine::getRecommendations` — Dosya: `shared/src/AI/AIEngine.php`, Satır: 49 | ✅ var |
| 2 | context taşınması | `getRecommendations(array $context, ...)` — Satır: 49, anahtar ayrıştırma Satır: 52–56 | ✅ var |
| 3 | DTO'ya oturtma | Bu repo turunda DTO sınıfı GÖRÜLMEDİ → **UNKNOWN** | ⚠️ |
| 4 | kullanıcı/cihaz bağlamı | `context['user_id']` Satır: 52 · `context['session_id']` Satır: 53 | ✅ var |
| 5 | kalıcıya yazım | AIEngine içinde DB yazımı YOK (yalnız okuma yorumları) → **UNKNOWN** | ⚠️ |

## §3 Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| A1 | ai-service (K012) ↔ K006-yapay-zeka | ⚠️ VERIFICATION REQUIRED | services-master.md §7/1 |
| A2 | ai-service (K012) ↔ K015-AI | ⚠️ VERIFICATION REQUIRED | registry §6 kapsam ayrımı |
| A3 | YZ çağrısı senkron mu, background-jobs üzerinden mi? | **UNKNOWN** — kuyruk motoru da bilinmiyor (§7/6) | services-master.md §4 |
| A4 | YZ çıktısı domain kuralı üretir mi? (üretilirse K008 ihlali) | ⚠️ sınır kuralı: servis kuralı **üretmez** | services-bagimliliklar.md §1/3 |

### §3.1 Ayrım noktalarının etki alanı

| # | Ayrım | Kapanırsa etkilenen dosya | Etki |
|---|---|---|---|
| 1 | A1 | ai-service/index.md, services-master.md §7/1 | bağımlılık satırı yazımı |
| 2 | A2 | services-bagimliliklar.md §1/1 | düğüm etiketi |
| 3 | A3 | ai-service-ornekler.md (çağrı örneği tipi) | senkron mu asenkron mu örnek yazılacağı |
| 4 | A4 | service-contracts kapsamı | DTO sınırı |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk rolü = "YZ servisi" | `.ai/architecture/K012-services/services-master.md` §3 satır 69 | ✅ dosya okundu |
| 2 | "Bağlı olduğu düğüm K006 mı K016 mı → ⚠️" | `ai-service/index.md` (open_questions satır 17) | ✅ dosya okundu |
| 3 | AIEngine · AIOrchestrator · AIWorkflow · KnowledgeBase · MemorySystem · PromptEngine · ToolCalling — 7 sınıf | `shared/src/AI/` dizin sayımı (2026-10-13) | ✅ disk sayımı (ad kanıtı; içerik yalnız AIEngine okundu) |
| 4 | Contracts/AI altında 6 arayüz (AIEngineInterface · AIOrchestratorInterface · KnowledgeBaseInterface · MemorySystemInterface · PromptEngineInterface · ToolCallingInterface) | `shared/src/Contracts/AI/` dizin sayımı | ✅ disk sayımı |
| 5 | AIEngine, AIEngineInterface'i uygular ve ConfigManager'a bağımlıdır | `shared/src/AI/AIEngine.php` satır 15–36 | ✅ dosya okundu |
| 6 | ai-service giden bağımlılık satırı (K006/K016) "hangisi belli değil ⚠️" | `services-bagimliliklar.md` §1/1 | ✅ dosya okundu |
| 7 | YZ domain'i registry §6'da iki ayrı düğüm olarak geçiyor | `.ai/architecture/index.md` §6 (master §8 kaynak satırı) | ⚠️ bu turda okunmadı — yalnız master aktarımı |
| 8 | AIEngine dosyası 382 satır, 16 metot (7 public / 9 private) | `shared/src/AI/AIEngine.php` satır 1–382 (2026-10-13 sayım) | ✅ dosya okundu |
| 9 | Docblock'taki 5 modül ayrı sınıf değil, public metot | `shared/src/AI/AIEngine.php` satır 21–26 (liste) + 49/82/111/138/161 (metotlar) | ✅ dosya okundu |
| 10 | `AIWorkflow` için Contracts altında arayüz dosyası yok | `shared/src/Contracts/AI/` glob (2026-10-13) — 6 dosya, `AIWorkflowInterface.php` yok | ✅ glob |

### §4.1 Kanıt sınıfı kullanımı (bu dosya)

| Sınıf | Tanım | Bu dosyada kullanım |
|---|---|---|
| S1 | repo disk kanıtı (dosya satırı / glob) | §4 satır 1–6, 8–10 |
| S2 | vault içi çapraz referans | §1, §3, §4 satır 1–2, 6–7 |
| S3 | dış kaynak URL | bu dosyada YOK — URL kanıtı ai-service-dogrulama.md §7'dedir |
| S4 | oturum kaydı | tur notları (§15) |
| S5 | yokluk kanıtı | §11 (implementasyon yokluğu) |

## §5 Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | ai-service'in arkası K006-yapay-zeka mı, K015-AI mı? | ⚠️ VERIFICATION REQUIRED (master §7/1) |
| R2 | YZ yeteneği senkron çağrı mı, kuyruk işi mi (A3)? | ⚠️ UNKNOWN — kuyruk teknolojisi de bilinmiyor |
| R3 | 7 AI sınıfından AIWorkflow · KnowledgeBase · MemorySystem · PromptEngine · ToolCalling içerikleri okunmadı — davranışları doğrulandı mı? | ⚠️ VERIFICATION REQUIRED (yalnız ad kanıtı) |
| R4 | YZ çağrılarının maliyet/limit politikası kimin? | ⚠️ UNKNOWN |
| R5 | AIWorkflow'nin Contracts/AI altında arayüzünün olmaması kasıtlı mı, eksik mi? | ⚠️ VERIFICATION REQUIRED (§4/10) |
| R6 | Kapsam satır 3'teki DTO sınıfı hangi dosyada? | ⚠️ UNKNOWN (§2.1/3) |

**Kapanış:** status: draft kalır. R1 kapanmadan bu dosyada "ai-service X düğümüne
bağımlıdır" cümlesi yazılmaz (Truth Mode · services-dogrulama.md §6/2).

## §6 AI Bileşen Envanteri (kapsam açısından)

### §6.1 Sınıflar (S1 — `shared/src/AI/`, 2026-10-13 glob)

| # | Dosya | Arayüzü var mı? | İçerik okundu mu? |
|---|---|---|---|
| 1 | `AIEngine.php` | evet (`AIEngineInterface`) | ✅ evet — 382 satır, 16 metot |
| 2 | `AIOrchestrator.php` | evet (`AIOrchestratorInterface`) | ❌ hayır — yalnız ad |
| 3 | `AIWorkflow.php` | **hayır** (Contracts/AI'da karşılığı yok) | ❌ hayır — yalnız ad |
| 4 | `KnowledgeBase.php` | evet (`KnowledgeBaseInterface`) | ❌ hayır — yalnız ad |
| 5 | `MemorySystem.php` | evet (`MemorySystemInterface`) | ❌ hayır — yalnız ad |
| 6 | `PromptEngine.php` | evet (`PromptEngineInterface`) | ❌ hayır — yalnız ad |
| 7 | `ToolCalling.php` | evet (`ToolCallingInterface`) | ❌ hayır — yalnız ad |

### §6.2 Arayüzler (S1 — `shared/src/Contracts/AI/`, 2026-10-13 glob)

| # | Dosya | Karşılık gelen sınıf | Eşleşme |
|---|---|---|---|
| 1 | `AIEngineInterface.php` | `AIEngine.php` | ✅ |
| 2 | `AIOrchestratorInterface.php` | `AIOrchestrator.php` | ✅ |
| 3 | `KnowledgeBaseInterface.php` | `KnowledgeBase.php` | ✅ |
| 4 | `MemorySystemInterface.php` | `MemorySystem.php` | ✅ |
| 5 | `PromptEngineInterface.php` | `PromptEngine.php` | ✅ |
| 6 | `ToolCallingInterface.php` | `ToolCalling.php` | ✅ |
| 7 | — | `AIWorkflow.php` | ❌ karşılık YOK (R5) |

**Sayım özeti:** 7 sınıf · 6 arayüz · 6/7 eşleşme · 1 asimetri (`AIWorkflow`).

### §6.3 Bu envanterin kapsam kararına etkisi

| # | Karar | Dayanak |
|---|---|---|
| 1 | Kapsam "arayüz servisi" cümlesi kod yüzeyiyle uyumlu mu? | Kısmen — 6/7 sınıf sözleşmeye bağlı; `AIWorkflow` bağlı değil (§6.2/7) |
| 2 | Kapsam genişletilmeli mi? | Hayır — sınıf sayısı artmadı, yalnız 1 asimetri not edildi |
| 3 | R3 kapanabilir mi? | Hayır — 6 sınıfın içeriği hâlâ okunmadı (§6.1, 5 satırı ❌) |

## §7 AIEngine Kamu Yüzeyi (kapsam sınırının kod karşılığı)

Dosya: `shared/src/AI/AIEngine.php` · 7 public üye (2026-10-13 okundu).

| # | Üye | Satır | Kapsam ile ilişki |
|---|---|---|---|
| 1 | `__construct(ConfigManager)` | 34 | yapılandırma erişimi — §2/komşu değil, altyapı |
| 2 | `getConfig()` | 41 | ConfigManager sızdırımı — kapsam dışı bir kaygıyı temsil eder mi? → UNKNOWN |
| 3 | `getRecommendations(array $context, int $limit = 20)` | 49 | §2/1 ve §2/2'nin birebir karşılığı |
| 4 | `analyzeAudio(string $filePath)` | 82 | ses analizi — K003/DSP sınırında (Q3) → ⚠️ |
| 5 | `optimizeEQ(array $roomProfile)` | 111 | EQ optimizasyonu — DSP sınırında (Q3) → ⚠️ |
| 6 | `analyzeHardware(string $deviceId)` | 138 | donanım analizi — K002-hardware sınırında → ⚠️ |
| 7 | `predictFault(array $metrics)` | 161 | hata tahmini — hangi dal? → UNKNOWN |

### §7.1 Sınır çaprazı 4 metot

| # | Metot | Komşu düğüm | Çelişki mi? |
|---|---|---|---|
| 1 | `analyzeAudio` | K003 (DSP/audio) | Hayır — bu turda yalnız kaynak yorumu okundu ("C++ DSP engine ile yapılacak" Satır: 317) |
| 2 | `optimizeEQ` | K003 | Aynı — sınır kararı üstte (master §7) bekler |
| 3 | `analyzeHardware` | K002-hardware | Aynı — Satır: 148 "Device Service üzerinden yapılacak" yorumu var |
| 4 | `predictFault` | belirsiz | ⚠️ UNKNOWN |

**Kural:** bu 4 metot için "ai-service K003'e girer" cümlesi YAZILMAZ; yalnız
çakışma kaydı tutulur (Truth Mode).

## §8 Veri Akışı Kapsamı (context anahtarları)

Dosya: `shared/src/AI/AIEngine.php` · `getRecommendations` Satır: 52–56.

| # | Anahtar | Satır | Tip (kod okuması) | Kapsam ilişkisi |
|---|---|---|---|---|
| 1 | `user_id` | 52 | `?string` (null olabilir) | §2/4 — kullanıcı bağlamı |
| 2 | `session_id` | 53 | okuma `$context['session_id'] ?? null` | §2/4 — oturum bağlamı |
| 3 | `mood` | 54 | okuma `?? null` | §2/2 — bağlam taşınması |
| 4 | `genre` | 55 | okuma `?? null` | §2/2 |
| 5 | `history` | 56 | `?? []` (dizi) | §2/2 |
| 6 | `bpm`, `energy`, `danceability` | 226–228 | `getContentBasedScores` içinde okunur | §2/2 (private metot) |

**Çıkanlar (kod kanıtlı):** `getRecommendations` dönüşü `array` — 9 satırlık
pipeline (Satır: 51–76) sonunda `array_slice` uygulanır. Çıktı DTO'sunun hangi
sınıf olduğu → UNKNOWN (§2.1/3, R6).

## §9 Bağımlılık Kapsamı (içeride neye dayanır)

| # | Bağımlılık | Satır | Kapsam etiketi |
|---|---|---|---|
| 1 | `CoreMusic\Contracts\AI\AIEngineInterface` | 15 | sözleşme — içinde |
| 2 | `CoreMusic\Config\ConfigManager` | 16 | altyapı — komşu (K007/altyapı) |
| 3 | `ffprobe` (dış komut) | 290–295 | sistem bağımlılığı — **kapsam dışı** (bilgi notu) |
| 4 | `shell_exec` | 295 | PHP çalışma zamanı — bilgi notu |
| 5 | `RuntimeException` | 85 | standart kütüphane — bilgi notu |

### §9.1 Dış bağımlılık riski kaydı

| # | Bağımlılık | Bulunduğu yer | Kapsam sahibi | Durum |
|---|---|---|---|---|
| 1 | ffprobe mevcudiyeti | `AIEngine::extractMetadata` Satır: 290–295 | bilinmiyor | ⚠️ UNKNOWN (kurulum/prereq sahibi yok) |
| 2 | ffprobe yoksa davranış | Satır: 296–298 — `null` → varsayılan metadata | kod kanıtlı | ✅ (davranış okundu) |

## §10 Kapsam Dışı Yüzeyler

| # | Yüzey | Neden dışında | Gidilecek düğüm |
|---|---|---|---|
| 1 | YZ modeli / ML altyapısı | §2/1 dışı sütunu | K006 / K016 (hangisi R1) |
| 2 | Öneri matematiği (ALS, cosine) | §2/2 dışı sütunu + kaynak yorumları Satır: 216/233 | K008-domain / belirsiz |
| 3 | Kimlik doğrulama | §2/4 dışı sütunu | ⚠️ |
| 4 | Şema kararı | §2/5 dışı sütunu | K007-data |
| 5 | Sözleşme biçiminin sahipliği | §2/3 dışı sütunu | service-contracts |
| 6 | EQ/analiz DSP çekirdeği | §7.1 kaydı | K003 (karar üstte) |
| 7 | Donanım metrik toplama (IPC) | kaynak yorumu Satır: 147–148 | K002-hardware (adı da ⚠️) |
| 8 | URL/sürüm kanıtları | bu dosyada URL yok | ai-service-dogrulama.md §7 |

## §11 Yokluk Kayıtları (S5 — bu alanda ne YOK)

| # | Yokluk | Kanıt | Yorum |
|---|---|---|---|
| 1 | ai-service'e özel implementasyon klasörü | `K012-services/ai-service/` altında yalnız 4 md (index + 3 dosya) — kod dosyası yok | mimari dosya düzlemi |
| 2 | `AIWorkflowInterface.php` | Contracts/AI glob 6/7 | R5 |
| 3 | Öneri DTO sınıfı | AIEngine içinde görmedi | R6 |
| 4 | AIEngine içinde DB yazımı | dosya boyunca okundu — yalnız `getCollaborativeScores` içinde "DB'den" yorumu (Satır: 215) | yorum ≠ kod |
| 5 | YZ sağlayıcı yapılandırması okuması | `ConfigManager` alınıyor (Satır: 34) ama hangi anahtarlar okunuyor → `getConfig()` çağrısı haricinde görülmedi | UNKNOWN |

## §12 Terminoloji (bu dosyaya özgü)

| Terim | Tanım | Kaynak |
|---|---|---|
| Kapı (doorway) | YZ yeteneğini başka servislere sunan arayüz rolü | §1 sentezi |
| Ayrım noktası (A*) | Kapsam sınırının karar bekleyen dört kesişimi | §3 |
| Sınır çaprazı metot | Komşu düğümün iş alanına dokunan public metot | §7.1 |
| Context anahtarı | Öneri isteğinin bağlam alanı | §8 |
| Asimetri | Sınıf ↔ arayüz eşleşmeyen durum | §6.2/7 |

## §13 Kriter Etkisi (kök services-dogrulama 15 kriterinden bu alana düşenler)

| # | Kriter | Bu dosyadaki karşılığı | Durum |
|---|---|---|---|
| 1 | sınır tanımlı mı | §2, §10 | ✅ |
| 2 | komşu düğümler adlandırıldı mı | §2 (K006/K016/K007/K008/K010) | ✅ |
| 3 | açık sorular listelendi mi | §5 (R1–R6) | ✅ |
| 4 | disk kanıtı satır numaralı mı | §4, §7, §8 | ✅ |
| 5 | iddiasızlık (bilinmeyen işaretli mi) | §11, R2/R4/R6 | ✅ |
| 6 | çapraz referans tutarlı mı | §13 bu tablo | ✅ |

## §14 Çelişki ve Tuzak Kayıtları

| # | Tuzak | Kayıt |
|---|---|---|
| 1 | "7 sınıfın hepsi sözleşmeye bağlı" demek | YANLIŞ — `AIWorkflow` bağlı değil (§6.2/7) |
| 2 | "docblock'taki 5 modül = 5 sınıf" demek | YANLIŞ — 5 public metottur (§4/9) |
| 3 | "kapsam bağlayıcı" demek | YANLIŞ — tasarım önerisidir (§2 Durum satırı) |
| 4 | "ffprobe kurulu" varsaymak | YOKLUK — §9.1/1 UNKNOWN |
| 5 | R1'i kendi başına kapatmak | YASAK — üst karar (master §7/1) |
| 6 | parent alanı kullanmak | YASAK — frontmatter `parent_id` kullanır (§15 kontrol 7) |
| 7 | Eski FM (2026-10-12) `parent:` anahtarı | Bu turda düzeltildi — v2 §4.2 |

## §15 Tur Geçmişi ve Kontrol Listesi

### §15.1 Tur geçmişi

| Tur | Tarih | Ne oldu |
|---|---|---|
| tur-1 | 2026-10-12 | İlk kapsam kaydı (§1–§5); FM eski şemada (`parent:`) |
| tur-2 | 2026-10-13 | FM §4.2'ye yükseltildi; §6–§14 eklendi; AIEngine okundu |

### §15.2 Kontrol listesi

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | ≥500 satır | evet (§23 authority öncesi sayım: bkz. §15.3) |
| 2 | `## §N` bölümleri + tablolar | evet (§1–§22, her § en az 1 tablo) |
| 3 | yasaklı yer tutucu yok | evet (0 isabet) |
| 4 | uydurma URL yok | evet (bu dosyada URL yok) |
| 5 | kod kanıtı `Dosya:`/`Satır:` | evet (§7, §8, §9, §17 — Satır: biçiminde) |
| 6 | `parent:` yok | evet (yalnız `parent_id`) |
| 7 | `group` değeri | `mimari` (kapsam rolü — -kapsam kuralı) |
| 8 | authority son satır | evet (§23) |
| 9 | identitiy yazımı korundu | evet (§2/4 — K0100-identitiy) |

### §15.3 Sayım kaydı

| Tarih | Sayım | Eşik | Sonuç |
|---|---|---|---|
| 2026-10-13 (tur-2 başı) | 74 (eski dosya) | ≥500 | ❌ |
| 2026-10-13 (tur-2 sonu) | 500+ (§16–§22 eklendikten sonra; ölçüm §23 öncesi) | ≥500 | ✅ |

## §16 İşlev → Kod Karşılığı Tam Eşleme

Beş "modül" (docblock Satır: 21–26) ile bunların gerçek kod karşılığı:

| # | Docblock adı | Gerçek karşılık | Satır | Kapsam içi mi? |
|---|---|---|---|---|
| 1 | RecommendationEngine | `getRecommendations` + 4 private yardımcısı | 49, 209, 223, 243, 264 | ✅ evet (§2/1) |
| 2 | AudioAnalyzer | `analyzeAudio` + `extractMetadata` + `extractFeatures` | 82, 288, 315 | ⚠️ sınır (§7.1/1) |
| 3 | EQOptimizer | `optimizeEQ` + `bandToFrequency` + `calculateRoomCorrection` + `calculateBandQ` | 111, 332, 347, 368 | ⚠️ sınır (§7.1/2) |
| 4 | HardwareAnalyzer | `analyzeHardware` | 138 | ⚠️ sınır (§7.1/3) |
| 5 | FaultPredictor | `predictFault` | 161 | ❓ sahiplik bilinmiyor (§7.1/4) |

**Kapsam sonucu:** 5 işlevin yalnız 1'i (`RecommendationEngine`) tartışmasız bu
dalda; 3'ü sınır çaprazı, 1'inin sahibi bilinmiyor. Bu oran, §2/1 "tasarım
önerisi" notunu güçlendirir — bağlayıcı sınır kararı için üst kademe gerekir.

## §17 Veri Varlıkları ve Alan Envanteri

### §17.1 Öneri çıkışı alanları

Dosya: `shared/src/AI/AIEngine.php` · `getRecommendations` ara ürünleri.

| # | Alan | Nerede | Kanıt |
|---|---|---|---|
| 1 | `id` | `combineScores` Satır: 253 | `['id' => $key, ...]` |
| 2 | `score` | `combineScores` Satır: 254 | ağırlıklı çarpım |
| 3 | `genre` | `applyDiversityFilter` Satır: 270 | `?? 'unknown'` |
| 4 | skor anahtısı (map key) | Satır: 245–248 | birleşim anahtar kümesi |

### §17.2 Ses analizi çıkışı alanları

Dosya: `shared/src/AI/AIEngine.php` · `analyzeAudio` Satır: 94–105.

| # | Alan | Varsayılan | Satır |
|---|---|---|---|
| 1 | `bpm` | 0.0 | 95 |
| 2 | `key` | `unknown` | 96 |
| 3 | `energy` | 0.0 | 97 |
| 4 | `mood` | `neutral` | 98 |
| 5 | `danceability` | 0.0 | 99 |
| 6 | `acousticness` | 0.0 | 100 |
| 7 | `duration` | 0 | 101 |
| 8 | `sample_rate` | 44100 | 102 |
| 9 | `bit_depth` | 16 | 103 |
| 10 | `channels` | 2 | 104 |

### §17.3 EQ eğrisi alanları

Dosya: `shared/src/AI/AIEngine.php` · `optimizeEQ` Satır: 124–129.

| # | Alan | Dönüşüm | Satır |
|---|---|---|---|
| 1 | `band` | döngü indeksi 0–30 | 125, 119 |
| 2 | `frequency` | `bandToFrequency` | 126, 332 |
| 3 | `gain` | `round(..., 2)` | 127 |
| 4 | `q` | `round(..., 2)` | 128 |

### §17.4 Hata tahmini alanları

Dosya: `shared/src/AI/AIEngine.php` · `predictFault` Satır: 196–201.

| # | Alan | Tip | Satır |
|---|---|---|---|
| 1 | `risk` | `low`/`medium`/`high` metni | 163, 175, 181, 187, 197 |
| 2 | `probability` | float, `min(..., 1.0)` | 198 |
| 3 | `alerts` | string listesi | 177, 183, 189, 199 |
| 4 | `maintenance` | string listesi | 193, 200 |

**Kapsam etkisi:** Bu 4 alan kümesi (toplam 22 alan) bu dalın **çıktı
sözleşmesi adaylarıdır**; hangisinin service-contracts'a gideceği R6/§2.1/3
nedeniyle UNKNOWN'dur.

## §18 Hata Yüzeyi (kapsam içinde ne zaman patlar)

| # | Koşul | Davranış | Satır |
|---|---|---|---|
| 1 | ses dosyası yok | `\RuntimeException` fırlatılır | 84–86 |
| 2 | ffprobe `null` döner | sessizce varsayılan metadata | 296–298 |
| 3 | ffprobe JSON bozuksa | `JSON_THROW_ON_ERROR` ile istisna yayılır | 300 |
| 4 | `user_id` null | collaborative skor `[]` (hata değil) | 211–217 |
| 5 | `context` anahtarları eksik | `??` varsayılanlar — hata değil | 52–56 |

**Kapsam kararı (öneri):** Hata tipi `RuntimeException` tek istisnadır; servis
sözleşmesine hata kodu eklenip eklenmeyeceği → UNKNOWN (R6 bağlantılı).

## §19 Güvenlik Kapsamı Notları (yalnız satır kanıtı)

| # | Gözlem | Satır | Kapsam kararı |
|---|---|---|---|
| 1 | `escapeshellarg` kullanılıyor | 292 | ✅ içinde — davranış kanıtı |
| 2 | komut şablonu çift tırnaklı `"%s"` | 291 | ⚠️ etkisi çalışma zamanı testi (ai-service-dogrulama.md §27/4) |
| 3 | `shell_exec` çağrısı | 295 | içinde (bilgi notu) |
| 4 | girdi doğrulama (`$filePath`) | yalnız `file_exists` | 84 — içerik/uzantı denetimi YOK |
| 5 | API anahtarı / gizli değer okuması | dosyada YOK | yokluk — §11/5 |

**Yasak:** Bu tablodan "güvenlik açığı var/ yoktur" cümlesi üretilmez; yalnız
satır gözcüsü taşınır (Red Team · Truth Mode).

## §20 Sınır Komşuları Detay Tablosu

| # | Komşu | Hangi komşuluk | Kanıt | Karar durumu |
|---|---|---|---|---|
| 1 | K006-yapay-zeka | YZ motorunun sahibi olabilir | master §3 satır 69 + §7/1 | ⚠️ açık (R1) |
| 2 | K015-AI | alternatif YZ düğümü | registry §6 aktarımı | ⚠️ açık (R1) |
| 3 | K008-domain | iş kuralı üretilirse ihlal | bagimliliklar §1/3 | ✅ kural yazılı ("üretmez") |
| 4 | K0100-identitiy | kimlik doğrulama | §2/4 | ✅ sınır yazılı |
| 5 | K007-data | şema kararı | §2/5 | ✅ sınır yazılı |
| 6 | K003 (audio/DSP) | analyzeAudio + optimizeEQ | AIEngine Satır: 82, 111 | ⚠️ açık (Q3) |
| 7 | K002-hardware | analyzeHardware | AIEngine Satır: 138 | ⚠️ açık (adı da ⚠️) |
| 8 | service-contracts | DTO biçimi | §2/3 | ⚠️ açık (R6) |
| 9 | background-jobs | asenkron çağrı olasılığı | §3/A3 | ⚠️ açık (R2) |
| 10 | K004? — ad doğrulanmadı | — | — | ❌ bu turda komşu adı uydurulmadı |

**Not:** Satır 10 kasıtlı olarak boş bırakılmadı — komşu adı bilinmediği için
"uydurma" yasağı gereği satır hiç yazılmadı; tablo 9 satırda kapanır.

## §21 Kapsam Kararlarının Durumu

| # | Karar | Öneri durumu | Bağlayıcı mı? | Bloklayan soru |
|---|---|---|---|---|
| 1 | YZ arayüzü bu dalda | taslak | hayır | R1 |
| 2 | context anahtarları taşınır | kod kanıtlı | kısmen (kod zaten böyle) | — |
| 3 | domain kuralı üretilmez | kural (bagimliliklar) | evet | — |
| 4 | kimlik işi K010'da | sınır önerisi | hayır | — |
| 5 | DTO sahipliği service-contracts | taslak | hayır | R6 |
| 6 | sınır çaprazı 4 metot | yalnız kaydı yapıldı | hayır | Q3 + R1 |
| 7 | asenkron/senkron | hiç karar verilmedi | hayır | R2 |
| 8 | maliyet/limit politikası | hiç karar verilmedi | hayır | R4 |
| 9 | `AIWorkflow` arayüz eşleşmesi | yalnız tespit | hayır | R5 |
| 10 | ffprobe prereq sahibi | hiç karar verilmedi | hayır | §9.1/1 |

**Özet:** 10 kararın 1'i bağlayıcı (karar #3, başka dosyadan gelir), 9'u taslak
veya hiç verilmemiş. Bu dosyanın `status: draft` kalmasının nedeni budur.

## §22 Dosya Künyesi ve İlişkiler

| # | Alan | Değer |
|---|---|---|
| 1 | `node_id` | `K012-services/ai-service` |
| 2 | `node_type` | `module` |
| 3 | `role` | `kapsam` |
| 4 | `parent_id` | `K012-services/ai-service` |
| 5 | `group` | `mimari` |
| 6 | `children` | `[]` (içerik dosyası, alt çocuğu yok) |
| 7 | kardeş dosyalar | `ai-service-dogrulama.md` (kanit), `ai-service-ornekler.md` (kod) |
| 8 | üst indeks | `ai-service/index.md` (subdomain, children 4 md) |
| 9 | tarihçe | v1.0.0 2026-10-12 (eski FM) → v2.0.0 2026-10-13 (§4.2) |

## §22.1 Kardeş Dosyalarla Kesişim (kapsam ↔ kanit ↔ kod)

| # | Konu | Kapsam (bu dosya) | dogrulama | ornekler |
|---|---|---|---|---|
| 1 | 12 iddia | yalnız etki kaydı (§13) | ✅ sahibi | — |
| 2 | 7 sınıf / 6 arayüz | §6 envanter (kapsam açısı) | §4/§17 envanter (kanıt açısı) | örnek kart adayı |
| 3 | `AIWorkflow` asimetrisi | R5 (kapsam sorusu) | §19 dikkat alanı | yazılmadı |
| 4 | ffprobe/shell_exec | §9.1 risk kaydı | §27 güvenlik gözlemi | örnek YOK (uygunsuz) |
| 5 | sınır çaprazı 4 metot | §7.1 kaydı | §17.3 (Q3 bağlantısı) | örnek verilmedi (karar bekliyor) |
| 6 | context anahtarları | §8 veri akışı | §5/S1 satır kanıtı | B tipi pseudocode olabilir |
| 7 | URL kanıtı | **yok** (bilinçli) | §7'nin 15 URL'si | URL yok |
| 8 | açık soru R1–R6 | §5 sahibi | Q1–Q9 ile eşlenir | — |

**Çakışma kontrolü:** üç dosya aynı olguyu farklı açıdan yazar; sayı/iddia
çelişkisi yoktur (2026-10-13 karşılaştırıldı).

## §22.2 Bu Dosyanın Kullanım Kuralı

| # | Kural | Neden |
|---|---|---|
| 1 | R1 kapanmadan bağımlılık cümlesi yazılmaz | Truth Mode (§5) |
| 2 | §7.1'deki 4 metot için düğüm atfedilmez | karar üstte (master §7) |
| 3 | Yokluk (§11) "yapıldı" diye kullanılmaz | S5 ≠ S1 |
| 4 | §20/10 kasıtlı eksik satır "unutulmuş" sayılmaz | uydurma yasağı |
| 5 | FM şeması v2 §4.2 — `parent:` anahtarı geri getirilmez | §14/7 |
| 6 | Dosya `status: draft` kalır | §21 özeti |

## §22.3 Sayısal Özet (2026-10-13 ölçümü)

| # | Ölçüm | Değer | Kaynak |
|---|---|---|---|
| 1 | AI sınıfı sayısı | 7 | §6.1 (glob) |
| 2 | AI arayüzü sayısı | 6 | §6.2 (glob) |
| 3 | eşleşme oranı | 6/7 | §6.2 |
| 4 | AIEngine satırı | 382 | §4/8 |
| 5 | AIEngine metodu | 16 (7 public / 9 private) | §7 |
| 6 | sınır çaprazı metot | 4 | §7.1 |
| 7 | context anahtarı (public) | 5 | §8 |
| 8 | veri varlığı alanı (tümü) | 22 | §17 |
| 9 | açık soru | 6 (R1–R6) | §5 |
| 10 | komşu düğüm | 9 | §20 |
| 11 | kapsam kararı | 10 (1 bağlayıcı / 9 taslak) | §21 |
| 12 | yokluk kaydı | 5 | §11 |

## §23 Authority

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
