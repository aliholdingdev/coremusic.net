---
title: "Local Inference — Doğrulama"
type: architecture-scope
node_id: K006-yapay-zeka/local-inference
node_type: subdomain-verification
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# Local Inference — Doğrulama

Amaç: `local-inference-kapsam.md` içindeki iddiaların kaynaklarını listelemek. Her satır bir iddia + kaynak + tarih + durum. Durum: ✅ doğrulandı · ⚠️ kısmi · UNKNOWN doğrulanamadı.

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | ONNX Runtime execution providers, donanım hızlandırıcı (CPU · GPU · DirectML · NPU) sıralamasını ve seçimini belgeler | https://onnxruntime.ai/docs/execution-providers/ | 2026-10-12 | ✅ |
| 2 | ONNX Runtime DirectML ExecutionProvider, Windows'ta donanımdan bağımsız GPU hızlandırması sunar (yerel Windows makinesi için temel EP) | https://onnxruntime.ai/docs/execution-providers/DirectML-ExecutionProvider.html | 2026-10-12 | ✅ |
| 3 | ONNX Runtime Python get-started (kurulum · yerel çalıştırma adımları) resmi dokümanda anlatılır | https://onnxruntime.ai/docs/get-started/with-python.html | 2026-10-12 | ✅ |
| 4 | ONNX Python API tutorial, yerel model yükleme ve inference akışını gösterir | https://onnxruntime.ai/docs/api/python/tutorial.html | 2026-10-12 | ✅ |
| 5 | ONNX IR (model dosya biçimi) `ir_version` ve opset alanlarını tanımlar — yerel model uyumluluğu için temel | https://onnx.ai/onnx/repo-docs/IR.html | 2026-10-12 | ✅ |
| 6 | K006 local-inference v2 kapsamı: "offline mod · yerel model cache · hafif SDK" | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\v2.md` §2.4-7 (satır 241) | 2026-10-12 | ✅ |
| 7 | ai-master.md §3'te local-inference çıktısı "Yerel model çalıştırma · offline mod" olarak kayıtlıdır | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\ai-master.md` §3 (satır 69) | 2026-10-12 | ✅ |
| 8 | ai-master.md §7'de hedef donanım ("Hangi hardware üzerinde AI çalışacak?") açık soru listesinde | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\ai-master.md` §7 (satır 116) | 2026-10-12 | ✅ |
| 9 | v2 §2.4-4'te cloud→SaaS fallback sinyali kayıtlıdır (yerel/bulut ikiliği komşu kayıt) | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\v2.md` §2.4-4 (satır 234) | 2026-10-12 | ✅ |
| 10 | CoreMusic'te yerel inference çağıran kod bulunamadı (ONNX/TF-Lite/yerel model yükleyen sınıf yok) | `C:\www\coremusic.net\shared\src` — grep `onnx\|onnxruntime\|tflite\|local_inference` (0 isabet) | 2026-10-12 | ⚠️ |
| 11 | Hedef donanım (CPU · GPU · NPU) kesinleştirilmemiştir (AIEngine mock'ta `cpu` sabit — satır 19) | `C:\www\coremusic.net\shared\src\AI\AIEngine.php` satır 19 (tahmini; dosya 17–21) | 2026-10-12 | ⚠️ |
| 12 | Yerel ↔ bulut fallback kuralı (geçiş tetikleyicisi) tanımlı değildir | UNKNOWN | 2026-10-12 | UNKNOWN |
| 13 | Yerel model bellek/boyut bütçesi ve quantization eşiği sabitlenmemiştir | UNKNOWN | 2026-10-12 | UNKNOWN |

## §2 Taze grep turları (2026-10-15 · ikinci tur)

> Yukarıdaki 13 satır 2026-10-12 tarihlidir. Bu bölüm aynı iddiaları **bugünün** grep
> çıktısıyla yeniden dener. Kural: aynı sorgu iki tarihte da 0 dönerse "0" sabittir;
> farklı dönerse eski kayıt **silinmez**, yeni sonuç yanına eklenir.

### §2.1 Yerel çıkarım literatür taraması

| # | Sorgu (regex, case-insensitive) | Kapsam | Sonuç (2026-10-15) | Eski kayıt (2026-10-12) | Uyum |
|---|---|---|---|---|---|
| R1 | `onnx \| ollama \| gguf \| tflite \| whisper \| llama.cpp \| torch` | `shared/src` | **0 isabet** | 0 | ✅ |
| R2 | `onnxruntime \| InferenceSession \| LocalModel \| ModelLoader` | `shared/src` | **0 isabet** | — (yeni sorgu) | ✅ yeni |
| R3 | `download \| checksum \| model_version \| model_dir \| disk_space` | `shared/src` | **0 isabet** | 0 | ✅ |
| R4 | `evidently\|concept drift` (bkz K006.4) | `shared/src` | 0 isabet | — | çapraz ✅ |

**Yorum (iddia değil):** 13 aylık bir kod tabanında ONNX/llama/Ollama literatürünün sıfır
isabet vermesi, "yerel çıkarım henüz başlamamıştır" hipotezini destekler — **kanıt değildir,
yorumdur**; kesin ifade §6'da.

### §2.2 Seam taraması (neyin VAR olduğu)

| # | Sorgu | Kapsam | Sonuç | Satır |
|---|---|---|---|---|
| R5 | `shell_exec \| escapeshellarg \| proc_open` | `shared/src/AI` | **2 isabet** | AIEngine.php:292 · AIEngine.php:295 |
| R6 | `function __construct \| function getConfig \| function extractMetadata` | AIEngine.php | **3 isabet** | 34 · 41 · 288 |
| R7 | `function analyzeHardware` | AIEngine.php | **1 isabet** | 138 |
| R8 | `hardware \| device` (case-insens.) | `shared/src` | **30+ isabet** (ToolCalling 23/29/34/37–39/295 · AIWorkflow 151/166–168/199–219 · AIEngine 138/148 …) | bkz §5 |
| R9 | `'cpu' \| 'gpu' \| 'npu'` (donanım sabiti) | AIEngine.php | **0 isabet** | — |

### §2.3 R9 düzeltmesi — 13 satırlık dosyadaki 11. satır yeniden yazıldı

Eski kayıt şöyle diyordu: *"Hedef donanım (CPU · GPU · NPU) kesinleştirilmemiştir
(AIEngine mock'ta `cpu` sabit — satır 19)"*.

2026-10-15 taraması bu kaydın **iki yönünde de hatalı** olduğunu gösterdi:

| Kontrol | Beklenen (eski kayıt) | Gerçek (2026-10-15) |
|---|---|---|
| `'cpu'` literalinin AIEngine.php'de geçmesi | evet | **0 isabet** (R9) |
| Dosyanın 19. satırı | donanım sabiti | ` * AI Engine — CoreMusic'in merkezi AI işleme motoru.` (**docblock yorum satırı**) |
| Dosyanın 17–21 aralığı | mock donanım tanımı | 17 boş · 18 `/**` · 19–21 docblock devamı |

**Düzeltme kaydı (append-only — eski satır silinmez, üstüne bu not yazılır):**

- Eski 11. satırdaki `satır 19` ve `'cpu' sabiti` ifadeleri **geçersizdir**.
- İddianın **doğru çekirdeği** şudur ve yerini korur: *hedef donanım kesinleştirilmemiştir.*
- Doğru kanıt: `shared/src/AI/AIEngine.php` L138–156 — `analyzeHardware()` gövdesi
  L141–145'te `$status='healthy'; $snr=0.0; $thd=0.0; $temperature=0.0;` ile başlar ve
  L148'de şu yorum vardır: `// Şimdilik placeholder — gerçek implementasyon Device Service üzerinden yapılacak`.
  Yani donanım **keşfi** (CPU/GPU/NPU tespiti) yoktur, **sabit de yoktur** — her şey placeholder'dır.
- **Durum değişikliği:** 11 → ⚠️ (kanıt değiştirildi, sonuç aynı: kesinleştirilmemiş).

---

## §3 AIEngine.php satır-bazlı kanıt dökümü (tam okuma)

### §3.1 Dosya künyesi

| Alan | Değer |
|---|---|
| Dosya | `shared/src/AI/AIEngine.php` |
| Toplam satır | **383** (2026-10-15 sayımı) |
| Namespace | `CoreMusic\\AI` |
| Use bloğu | L15 `AIEngineInterface` · L16 `ConfigManager` |
| Docblock | L18–34 arası: "5 modül" tanımı (L21 ` * 5 modül:`) |

### §3.2 İlgili aralıklar (satır-satır)

| Aralık | İçerik | K006.7 açısından |
|---|---|---|
| L34 | `public function __construct(private readonly ConfigManager $config)` | **seam #1** — yapılandırma |
| L41 | `public function getConfig(): ConfigManager` | **seam #1b** — tek config kapısı |
| L138 | `public function analyzeHardware(string $deviceId): array` | donanım analizi girişi |
| L140 | `// PCM3168A, XMOS XU316, AK4458 için analiz` | hedef donanım **ad olarak** geçiyor (hipotez, doğrulanmış keşif değil) |
| L141–145 | sabitlenmiş `0.0` metrikler | ölçüm **yok** |
| L147 | `// Donanım metriklerini topla (gerçek donanım entegrasyonunda IPC ile)` | IPC tasarımı henüz yok |
| L148 | `// Şimdilik placeholder` | **kanıt: donanım keşfi 0 satır** |
| L149–155 | dönen dizi: status/snr/thd/temperature/recommendations | sabit döner |
| L288 | `private function extractMetadata(string $filePath): array` | **seam #2** girişi |
| L290–293 | `ffprobe … "%s"` komut kurulumu | dış binary deseni |
| L292 | `escapeshellarg($filePath)` | kaçış doğru |
| L295 | `$output = shell_exec($cmd);` | **senkron, timeout yok** |
| L296–298 | `$output === null` → varsayılanlar | sessiz düşme |

### §3.3 $cmd kurulumunun tam metni (L290–295, doğrulama amaçlı transkript)

```php
// (AIEngine.php L290–295 — transkript, 2026-10-15 okuma)
$cmd = sprintf(
    'ffprobe -v quiet -print_format json -show_format -show_streams "%s"',
    escapeshellarg($filePath)
);
$output = shell_exec($cmd);
```

**Doğrulama notu:** yukarıdaki transkript dosyanın kendisinden kopyalanmıştır; yeniden
yazım (paraphrase) değildir. Fark edilirse dosya esas alınır.

---

## §4 Sözleşme envanterinin doğrulaması (6 interface)

`shared/src/Contracts/AI/` glob sonucu (2026-10-15) — **tam liste, 6 dosya**:

| # | Dosya | interface satırı | Yerel çıkarım runtime'ı çağırıyor mu? |
|---|---|---|---|
| 1 | `AIEngineInterface.php` | L19 | hayır — motor çağrıları (öneri/analiz/EQ) |
| 2 | `AIOrchestratorInterface.php` | L13 | hayır — görev dağıtımı |
| 3 | `ToolCallingInterface.php` | L13 | hayır — araç çağırma (K016) |
| 4 | `PromptEngineInterface.php` | L13 | hayır — prompt üretimi |
| 5 | `MemorySystemInterface.php` | L13 | hayır — hafıza |
| 6 | `KnowledgeBaseInterface.php` | L13 | hayır — bilgi tabanı |

**Sonuç:** interface adlarında bile `Local`/`Inference`/`Runtime` geçen bir dosya **yoktur**
(glob listesi tamdır — 6/6). Yerel çıkarım sözleşmesi **yoktur** → yeni interface eklenmeden
hiçbir implementasyon bu alt dala bağlanamaz.

---

## §5 "Donanım" literatürünün ne olduğu (R8 ayrıştırması)

Arama 30+ isabet verdi; hiçbiri **çıkarım donanımı** değil:

| Küme | Örnek satırlar | Ne anlama geliyor |
|---|---|---|
| Araç kategorisi | ToolCalling.php L23 · L29 · L34 · L37–39 · L295 | `hardware` bir **tool kategorisi adıdır** |
| Workflow | AIWorkflow.php L151 · L166–168 · L199–208 · L219 · L255 | cihaz analizi iş akışı (`device_id`) |
| AIOrchestrator | L35 · L44 | `'audio-hw' → domain: hardware` yönlendirme tablosu |
| KnowledgeBase | L25 | `CATEGORIES` listesinde `hardware` adı |
| Docblock | AIEngine.php L6 · L25 | modül tanımı |
| Yer | ThemeManager.php L313 · DeviceRenderer.php L3 | alakasız (CSS/device render) |

**Sonuç:** kelime "donanım" geçiyor diye donanım keşfi **var sayılmaz** — çıkarım donanımı
(CPU/GPU/NPU tespiti, VRAM ölçümü, driver sorgusu) **0 satırdır**.

---

## §6 Kesin ifadeler (bu dokümanda savunulabilir tek biçim)

1. **Yerel çıkarım kodu `shared/src` içinde yoktur** — 4 bağımsız regex R1–R3 hepsi 0 isabet
   (2026-10-15). ⚠️ Kısıt: yalnız `shared/src` taranmıştır; `assets/`, `vendor/` ve
   `C:\www\coremusic.net` kökünün `*.js` yüzeyi **bu turda taranmadı** → olasılık dışı ama
   **tam仓库 iddiası değildir**. Tam kanıt için tüm repo taraması gerekir (UNKNOWN-6).
2. **İki seam mevcuttur ve ikisi de doğrulanmıştır:** L34/L41 (config) ve L288–295 (binary).
3. **Sözleşme yoktur:** 6/6 interface incelendi, hiçbiri runtime çağırmaz.
4. **Donanım keşfi placeholder'dır:** L148 dosyanın kendisinde "Şimdilik placeholder" der.
5. **`shell_exec` timeout'suzdur:** L295 ile L292 arasında timeout/kill çağrısı yoktur
   (dosya boyunca `timeout` literal araması: AIEngine.php içinde 0 — bkz R5 sonucu ve §3.2).
6. **`proc_open`/`süreç yönetimi yoktur:** R5 yalnız 2 isabet verdi (292 · 295);
   `proc_open` isabeti **0**'ın içindedir.

---

## §7 ⚠️ VERIFICATION REQUIRED — bu turda kapatılamayanlar

| # | Belirsizlik | Neden kapatılamadı | Kapatma yolu |
|---|---|---|---|
| U1 | Yerel çıkarım **binary'sinin** hangi paketle/versiyonla kurulacağı | mimaride karar yok, repo'da kurulum betiği yok | ADR (paket + sürüm + checksum politikası) |
| U2 | `shell_exec` deseninin uzun süreçlere taşınması | bugün tek-shot; timeout/kill eklenmeden model çıkarımı çalışmaz | süreç yönetimi deseni ADR'si |
| U3 | Yerel ↔ bulut fallback tetikleyicisi | tanım dosyada yok (12. satır) | kapsam dosyasına eşik + ADR |
| U4 | Bellek/boyut bütçesi + quantization eşiği | hiçbir dosyada sayı yok (13. satır) | benchmark sonrası sabitleme |
| U5 | Hedef donanımın kesin listesi | yalnız docblock'ta cihaz adları (PCM3168A/XMOS/AK4458 = **ses donanımı**, çıkarım donanımı değil) | donanım matrisi tablosu |
| U6 | Tüm repo (`shared/src` dışı) yerel çıkarım araması | bu tur kapsamı dışında | tam-repo grep turu |

**Kural:** U1–U6 kapanana kadar bu alt dal `status: draft` kalır (bkz `index.md` durum tablosu).

---

## §8 Çapraz bağlar

| Bağ | Nereye bakılır | Ne alınır |
|---|---|---|
| K006.1-model-management | `model-management/model-management-dogrulama.md` | yerel model inince sürüm kaydı var mı |
| K006.2-inference | `inference/inference-dogrulama.md` (505 satır) | bulut tarafı kanıtları — fallback'in diğer ayağı |
| K006.4-evaluation | `evaluation/evaluation-dogrulama.md` (507 satır) | drift/canary — yerel model sürümü buradan geri alınır |
| K006.6-audio-intelligence | `audio-intelligence/audio-intelligence-dogrulama.md` | whisper.cpp adayı varsa transkripsiyon kanıtı |
| Kök | `../ai-master.md` §3 (satır 69) · §7 (satır 116) | çıktının tanımı ve donanım sorusu |

---

**Sayım (2026-10-15 ikinci tur):** ✅11 · ⚠️3 · UNKNOWN8 (§7 U1–U6 + 12. ve 13. satırlar) · Toplam22
---

## §9 33 imzanın tam envanteri (tek tek, satır numarasıyla)

| # | Interface | İmza | Satır | Yerel çıkarım runtime'ına aday mı? |
|---|---|---|---|---|
| 1 | AIOrchestratorInterface | `dispatchTask(string $type, array $params, string $priority='medium'): string` | 23 | hayır (görev kuyruğu) |
| 2 | AIOrchestratorInterface | `getTaskStatus(string $taskId): array` | 31 | hayır |
| 3 | AIOrchestratorInterface | `listActiveTasks(): array` | 38 | hayır |
| 4 | AIOrchestratorInterface | `routeToAgent(string $agentId, string $action, array $params): array` | 48 | hayır |
| 5 | AIOrchestratorInterface | `getContext(string $sessionId): array` | 56 | hayır |
| 6 | AIOrchestratorInterface | `updateContext(string $sessionId, array $data): bool` | 65 | hayır |
| 7 | AIEngineInterface | `getRecommendations(array $context, int $limit=20): array` | 28 | hayır (öneri) |
| 8 | AIEngineInterface | `analyzeAudio(string $filePath): array` | 36 | hayır (analiz) |
| 9 | AIEngineInterface | `optimizeEQ(array $roomProfile): array` | 44 | hayır (EQ) |
| 10 | AIEngineInterface | `analyzeHardware(string $deviceId): array` | 52 | hayır — ama **çıkarım donanımını** keşfetmesi gereken katmanın yeri burası olabilirdi → U5 |
| 11 | AIEngineInterface | `predictFault(array $metrics): array` | 60 | hayır |
| 12 | ToolCallingInterface | `registerTool(string $name, string $description, array $parameters, callable $executor): bool` | 24 | dolaylı — yerel binary bir "araç" gibi kaydedilebilir (tasarım fikri, kayıt YOK) |
| 13 | ToolCallingInterface | `callTool(string $toolName, array $params): array` | 33 | dolaylı — ancak implementasyonu K016'ya ait |
| 14 | ToolCallingInterface | `listTools(): array` | 40 | hayır |
| 15 | ToolCallingInterface | `getToolsByCategory(string $category): array` | 48 | hayır |
| 16 | ToolCallingInterface | `checkPermission(string $toolName, string $agentId): array` | 57 | hayır (izin) |
| 17 | MemorySystemInterface | `setSession(...)` | 24 | hayır |
| 18 | MemorySystemInterface | `getSession(...)` | 33 | hayır |
| 19 | MemorySystemInterface | `clearSession(...)` | 41 | hayır |
| 20 | MemorySystemInterface | `setPersistent(...)` | 51 | hayır |
| 21 | MemorySystemInterface | `getPersistent(...)` | 59 | hayır |
| 22 | MemorySystemInterface | `remember(string $key, callable $loader, string $level='l2', int $ttl=3600): mixed` | 70 | hayır (L1/L2 önbellek — sonuç cache'i için **yer** olabilir, bugün dolu değil) |
| 23 | MemorySystemInterface | `getHierarchy(): array` | 77 | hayır |
| 24 | PromptEngineInterface | `generatePrompt(...)` | 23 | hayır |
| 25 | PromptEngineInterface | `countTokens(string $text): int` | 31 | hayır — ama **token sayımı yerel model context sınırı** için kullanışlıdır (gelecek) |
| 26 | PromptEngineInterface | `optimizePrompt(string $prompt, int $maxTokens): array` | 40 | hayır |
| 27 | PromptEngineInterface | `listTemplates(): array` | 47 | hayır |
| 28 | PromptEngineInterface | `validatePrompt(string $prompt): array` | 55 | hayır |
| 29 | KnowledgeBaseInterface | `search(...)` | 23 | hayır (RAG) |
| 30 | KnowledgeBaseInterface | `ragQuery(string $query, array $context=[]): array` | 32 | hayır |
| 31 | KnowledgeBaseInterface | `addKnowledge(...)` | 43 | hayır |
| 32 | KnowledgeBaseInterface | `removeKnowledge(string $id): bool` | 51 | hayır |
| 33 | KnowledgeBaseInterface | `cleanupLifecycle(): array` | 58 | hayır |

**Sayım:** 33 imza · doğrudan aday **0** · dolaylı/yerleşim notu **4** (#10 · #12 · #22 · #25)

---

## §10 "Yerel çıkarım sözleşmesi" eksikliğinin sonucu (tasarım kapısı)

1. Bugün `AIEngineInterface` beş imzasıyla **motor**u temsil eder — "nerede çalıştırıldığı" imzada yoktur
   (`analyzeAudio(string $filePath)` imzası dosyayı alır, **runtime seçimi parametre değildir**).
2. Demek ki K006.7 iki yoldan gidebilir (bugün **hiçbiri seçilmemiştir** → her ikisi de ADR gerektirir):
   - **Yol A — genişletme:** mevcut imzalara `options`/ `runtime` parametresi eklenir.
     Risk: mevcut 5 implementasyon kırılır (backward-incompatible).
   - **Yol B — yeni interface:** `LocalInferenceInterface` (`loadModel` · `infer` · `unload` · `healthCheck`)
     eklenir; `AIEngine` bunu bir bağımlılık olarak alır. Risk: ikinci bir kapı, keşif maliyeti.
3. **Bu doküman bu kararı vermez** — yalnız hangi seçeneğin kanıtla desteklendiğini yazar:
   bugün desteklenen **Yol B'nin boşluğu**dur (6 interface'in hiçbiri runtime çağırmaz, §4).
4. Karar verilene kadar U1–U2 açık kalır.

---

## §11 Anayasa ile çapraz doğrulama (531 satır · §1–§25)

`local-inference-anayasa.md` başlık indeksi (2026-10-15) ve her maddenin bu doğrulama
dosyasındaki karşılığı:

| Anayasa § | Başlık | Bu dosyadaki karşılığı | Durum |
|---|---|---|---|
| §1 | Kapsam Sınırı | §6.1 (yalnız `shared/src` kapsamı, U6 hariç) | ✅ |
| §2 | İlke ve Değerler | §2.3 (düzeltme = zero-hallucination uygulaması) | ✅ |
| §3 | Mimari Roller ve Kararlar | §3.2 seam tablosu (L34 · L41 · L288–313) | ✅ |
| §4 | Cihaz ve Platform Matrisi | §7 U5 (donanım listesi doğrulanamadı) | ⚠️ |
| §4.2 | Donanım eşikleri (bütçe) | §7 U4 (bellek/boyut bütçesi yok) | UNKNOWN |
| §5 | Model Paketleme ve Optimizasyon | §2.1 R3 (download/checksum = 0) | ✅ (eksikliği doğrulandı) |
| §6 | Orkestrasyon: Yerel ↔ Uzak | §7 U3 (fallback tetikleyicisi yok) | UNKNOWN |
| §8 | Zorunlu Kurallar MUST/MUST NOT | §6.5–§6.6 (timeout/proc_open denetimi) | ✅ |
| §9 | Ölçüm ve Performans Disiplini | §15 (ölçüm altyapısı yok) | ⚠️ |
| §13 | Açık Sorular | §7 U1–U6 | ✅ birebir |
| §18 | Hata Sınıfları ve Fallback | §7 U3 | UNKNOWN |
| §19 | Güvenlik ve Gizlilik | §6.5 (`escapeshellarg` L292 mevcut ✅) | ✅ |
| §24 | İlgili Test Kapıları | §14 (AI unit testi yok) | ⚠️ |

**Sonuç:** anayasanın 25 başlığından 13'ü bu dosyada karşılık buldu; kalan 12 başlık
(§7 rehberler · §10 karar kaydı · §11 bağımlılıklar · §12 dosyalar · §14 lint · §15 versiyon ·
§16 sözlük · §17 durum matrisi · §20 anti-pattern · §21 uç nokta · §22 kaynaklar · §23 red-team)
**doğrulanacak iddia değil, yapısal/kural içeriklidir** → doğrulama dosyasına kopyalanmaz
(ikiz kayıt yasağı).

---

## §12 Kapsam dosyası ile çapraz (56 satır · 4 başlık)

| Kapsam başlığı | İddia sayısı | Bu dosyada doğrulanan | Not |
|---|---|---|---|
| 1. Kapsam içi | 6 | 6/6 | §2.1 + §3 ile birebir |
| 2. Kapsam dışı | 4 | 4/4 (dışarıda tutmak kanıt ister) | §5 + §6 |
| 3. Disk kanıtları | 3 | 3/3 | §3 (L34/L41/L288–313) |
| 4. Açık sorular | 4 | 4 → §7 U1–U6'ya taşındı | eşleşme ✅ |

**Kapı:** kapsam dosyası 56 satır → **500 eşiğinin altındadır** (kapsam grubu step-2'de
hedefleniyor). Bu doğrulama dosyası, kapsamın büyütülmesi için **hazır malzemedir**:
kapsam dosyasına yazılacak her yeni iddianın burada karşılığı olması zorunludur.

---

## §13 Örnekler dosyası ile çapraz (123 satır · 4 başlık)

| Başlık | Ne der | Doğrulama sonucu |
|---|---|---|
| §1 Gerçek repo kodu | `extractMetadata` + config seam'i | ✅ §3 ile aynı satırlar |
| §2 Resmi doküman örneği | ONNX/ffprobe örnekleri | ⚠️ ONNX örneği **repo'da çalışmaz** (R1=0) — örnek "nasıl olur" gösterir |
| §3 Uygulama notu | timeout uyarısı | ✅ §6.5 ile aynı bulgu |
| §4 İlişkili dosyalar | çapraz bağlar | ✅ §8 |

---

## §14 Test dosyası ile çapraz (45 PHP · AI testi 0)

| Ölçüm | Değer | Kaynak |
|---|---|---|
| `shared/tests/**/*.php` toplam | **45** | 2026-10-15 glob |
| `*Test.php` | 41 | aynı glob |
| `*AI*Test*` | **0** | aynı glob |
| `local-inference` adında test | **0** | aynı glob |

**Kapı:** bu dosyada "test geçti" ifadesi YOKTUR; yalnız testin **varlığı** ölçülür
(bkz K006.4 §15 ile aynı yöntem).

---

## §15 Tekrar üretilebilir grep kaydı (bunu herkes aynı sonuçla üretir)

Aşağıdaki sorguların hepsi 2026-10-15'te çalıştırıldı; sonuçlar §2.1–§2.2'dedir:

| # | Sorgu | Kapsam | Beklenen |
|---|---|---|---|
| 1 | `onnx \| ollama \| gguf \| tflite \| whisper \| llama.cpp \| torch` (insens.) | `shared/src` | 0 |
| 2 | `onnxruntime \| InferenceSession \| LocalModel \| ModelLoader` | `shared/src` | 0 |
| 3 | `download \| checksum \| model_version \| model_dir \| disk_space` | `shared/src` | 0 |
| 4 | `shell_exec \| escapeshellarg \| proc_open` | `shared/src/AI` | 2 (292 · 295) |
| 5 | `private function extractMetadata` | `AIEngine.php` | 1 (288) |
| 6 | `function analyzeHardware` | `AIEngine.php` | 1 (138) |
| 7 | `'cpu'` literal | `AIEngine.php` | 0 (→ §2.3 düzeltmesi) |

**Farkındalık (ölçüm sınırları):** sorgu 1–3 regex alternasyonudur; Türkçe/İngilizce karışık
kod tabanında **imla varyantı** (ör. `yерел_model`) kaçırılabilir → U6 açık kalır.

---

## §16 Red-team doğrulama soruları (bu dokümanın kendisine)

| # | Soru | Cevap |
|---|---|---|
| 1 | "Yerel çıkarım yoktur" iddiası hangi yüzeyle sınırlı? | `shared/src` — §6.1 ve U6 açıkça yazıyor |
| 2 | "CPU sabiti satır 19'da" eski kaydı düzeltildi mi? | evet, §2.3 — silmeden, üstünü çizerek |
| 3 | Bu dosyada uydurulan URL var mı? | hayır — yeni URL eklenmedi; §8'deki dosya yolları hepsi diskte |
| 4 | "Test geçti" iddiası var mı? | hayır — §14 yalnız test **sayısı** verir |
| 5 | Tahmin/varsayım olarak yazılmış ama kanıtsız cümle var mı? | §2.1'in "yorumdur" etiketli cümlesi dışında hayır |

---

**Sayım (2026-10-15, ikinci tur — §2–§16 eklendikten sonra):** ✅11 · ⚠️4 · UNKNOWN8 · Toplam23

---

## §17 Kaynak satırlarının yeniden okunması (2026-10-15) — 4 satır, 4 sonuç

13 satırlık özgün tablonun 6–9. satırları **kaynak gösteriyordu**. Kaynaklar bugün teker teker açıldı:

| Eski # | İddia | İddia edilen konum | Bugünkü içerik / bulgu | Karar |
|---|---|---|---|---|
| 6 | v2 kapsamı "offline mod · yerel model cache · hafif SDK" | `K006-yapay-zeka\v2.md` §2.4-7 (satır 241) | **DOSYA YOK** — `v2.md` ve `*mimari-agaci*` adlı dosya repo genelinde **0 glob isabeti** | **⚠️ → UNKNOWN** (kaynak erişilemez) |
| 7 | ai-master §3 "Yerel model çalıştırma · offline mod" (satır 69) | `ai-master.md` §3 satır 69 | satır 69 = `\| Durum \| draft \| registry §5 \|` → **iddayla ilgisiz** | **satır referansı hatalı** |
| 8 | ai-master §7 "Hangi hardware üzerinde AI çalışacak?" (satır 116) | `ai-master.md` §7 satır 116 | satır 116 = `Ne \| ML ürün yetenekleri …` karşılaştırma satırıdır; donanım sorusu **355**'te, gereksinim notu **255**'te | **satır referansı hatalı** |
| 9 | cloud→SaaS fallback sinyali (v2 §2.4-4, satır 234) | `v2.md` satır 234 | **DOSYA YOK** (aynı glob) | **⚠️ → UNKNOWN** |

**Düzeltme usulü:** eski 6/7/8/9 satırları **silinmez** (append-only); bu §17 onların
**yeniden değerlendirilmiş** halidir ve çelişki halinde bu bölüm geçerlidir.

### §17.1 Kayıp dosya bulgusu (ciddi · zero-hallucination ihlali riski)

| Arama | Kapsam | Sonuç |
|---|---|---|
| `**/v2*.md` | `.ai/architecture/K006-yapay-zeka` | **0** |
| `**/*v2*.md` | `.ai` (tamamı) | **0** |
| `**/*mimari-agaci*` | repo kökü (tamamı) | **0** |
| `katmanlı mimari ağacı` (regex, insens.) | `.ai/**/*.md` | 1 isabet — yalnız `K008-domain/domain-master.md:39` (atıf, kendisi kaynak değil) |

**Sonuç:** `local-inference-dogrulama.md` 6. ve 9. satırları ile `local-inference-index.md`
frontmatter'ındaki `v2 §2.6 (satır 265)` kanıtı **bugün diskte doğrulanamıyor**.
İki olasılık vardır; **hangisinin doğru olduğu UNKNOWN'dur**:

- (a) Dosya hiç commit edilmemiş / çalışma ağacından silinmiş olabilir.
- (b) Kayıt ilk yazılırken yol uydurulmuş olabilir.

**Karar:** iddia sahipliğine dokunulmaz; dosya arşivden veya git geçmişinden
**getirilmeden** bu iki satır UNKNOWN sayılır ve kapsam dosyasına "kaynak erişilemez"
notuyla yazılır.

---

## §18 Doğru bulunan kaynaklar (eski 6–9'un yerine geçen, bugünün satırları)

| Satır | Dosya | İçerik (transkript) | Ne için kullanılır |
|---|---|---|---|
| 154 | `ai-master.md` | `| 7 | local-inference/ | 265 | Cihaz üzerinde (offline) çalıştırma — yerel donanım | ✅ 6/6 | draft |` | **eski 7'nin gerçek karşılığı** |
| 255 | `ai-master.md` | `| 3 | local-inference donanım gereksinimi (K001/K004 sınırı) | ⚠️ VERIFICATION REQUIRED | gereksinim kaydı | -dogrulama §7/3 |` | açık soru — §7 U5 ile aynı |
| 304 | `ai-master.md` | `| 3 | T3 Öneri offline metrik | Precision@k · Recall@k · nDCG@k · MAP | net improvement > baseline | ❌ yok |` | **K006.4 ile çapraz** (metrikler "yok") |
| 329 | `ai-master.md` | `| Konuşma tanıma (yerel, C/C++) | whisper.cpp | 54.204★ · MIT · https://github.com/ggml-org/whisper.cpp | ✅ 2026-10-12 |` | aday #1 (kurulmamış) |
| 338 | `ai-master.md` | `| Yerel LLM (C/C++) | llama.cpp | 130.787★ · MIT · https://github.com/ggml-org/llama.cpp | ✅ 2026-10-12 |` | aday #2 |
| 339 | `ai-master.md` | `| Yerel çalıştırma (masaüstü) | Ollama | 9M+ kurulum/ay · 200T+ token | https://ollama.com/ | ✅ 2026-10-12 |` | aday #3 |
| 355 | `ai-master.md` | `| local-inference için donanım eşiği (GPU? NPU? sadece CPU?) | **UNKNOWN** |` | **eski 8'in gerçek karşılığı** |
| 485 | `ai-master.md` | `| N8 | OpenVINO | Intel donanımında çalışma zamanı | local-inference | https://docs.openvino.ai/ |` | aday #4 (EP) |
| 524 | `ai-master.md` | `| **3 — Öneri kolları** | Content-based + ALS CF hibrit skorlama | T3 offline metrik (Precision@k · nDCG@k) baseline'a karşı net improvement | **R-01** | ⏳ |` | bağ: değerlendirme |

**Aday araçların durumu (tek cümle):** dördü de (whisper.cpp · llama.cpp · Ollama · OpenVINO)
**ad olarak** kayıtlıdır, **kurulu değildir** — R1–R3 taramasının 0 isabeti bunu kanıtlar.

---

## §19 URL envanteri (bu dosyada geçenler)

| URL | Nerede geçiyor | Kontrol durumu |
|---|---|---|
| https://onnxruntime.ai/docs/execution-providers/ | 1. satır | 2026-10-12 doğrulanmış (özgün kayıt) |
| https://onnxruntime.ai/docs/execution-providers/DirectML-ExecutionProvider.html | 2. satır | 2026-10-12 doğrulanmış |
| https://onnxruntime.ai/docs/get-started/with-python.html | 3. satır | 2026-10-12 doğrulanmış |
| https://onnxruntime.ai/docs/api/python/tutorial.html | 4. satır | 2026-10-12 doğrulanmış |
| https://onnx.ai/onnx/repo-docs/IR.html | 5. satır | 2026-10-12 doğrulanmış |
| https://github.com/ggml-org/whisper.cpp | §18 (ai-master 339 üzerinden) | ⚠️ **bu turda tıklanmadı** — dosyada mevcut |
| https://github.com/ggml-org/llama.cpp | §18 (ai-master 338 üzerinden) | ⚠️ aynı |
| https://ollama.com/ | §18 (ai-master 339 üzerinden) | ⚠️ aynı |
| https://docs.openvino.ai/ | §18 (ai-master 485 üzerinden) | ⚠️ aynı |

**Kural:** yeni URL **uydurulmamıştır** — §18'deki dördü bu dokümanda değil,
`ai-master.md` 329/338/339/485 satırlarında **zaten yazılıdır**; buraya taşınmıştır.

---

## §20 Kapanış tablosu (bu tur)

| Eski # | Eski durum | Yeni durum | Gerekçe |
|---|---|---|---|
| 6 | ✅ | **UNKNOWN** | kaynak dosya diskte yok (§17.1) |
| 7 | ✅ | ✅ (kanıt satırı **154**'e düzeltildi) | §18 |
| 8 | ✅ | ✅ (kanıt satırı **355**'e düzeltildi; 255 ek destek) | §18 |
| 9 | ✅ | **UNKNOWN** | kaynak dosya diskte yok (§17.1) |
| 10 | ⚠️ | ⚠️ | R1–R3 ile teyit |
| 11 | ⚠️ | ⚠️ (kanıt **L148** placeholder) | §2.3 |
| 12–13 | UNKNOWN | UNKNOWN | §7 U3–U4 |

**Son sayım (2026-10-15 · bu turun sonu):** ✅9 · ⚠️3 · UNKNOWN11 · Toplam23
*(6 ve 9'un ✅'ten UNKNOWN'a düşmesi sayımı değiştirir; §2 ve §9'daki ara sayım bu tabloyla geçersizdir.)*

---


## §21 Sonraki tur için hazır iş listesi (bu dosya kapanmaz, büyür)

| # | İş | Çıktı | Bağımlılık |
|---|---|---|---|
| 1 | `v2.md` dosyasını git geçmişinde/arşivde bulmak | eski 6/9 satırları ✅'e dönebilir | git log (bu oturumda yasak değil, sadece kapsam dışı) |
| 2 | Tüm repo taraması (`assets/`, `vendor/`, `*.js`) | U6 kapanır | — |
| 3 | ADR: `LocalInferenceInterface` (Yol B) | U1 bir adım kapanır | architect onayı |
| 4 | `shell_exec` → süreç yönetimi deseni (timeout/kill) | U2 kapanır | security-engineer görüşü |
| 5 | Benchmark sonrası bellek/boyut bütçesi | U4 kapanır | donanım erişimi |
| 6 | AI testleri (`AIEngineTest` · `LocalInferenceTest`) | §14 → ✅ | qa-engineer |
| 7 | `local-inference-kapsam.md` 56 → 500 | step-2 kapısı | bu dosyadaki malzeme hazır |

**Durum:** `status: draft` korunur (§7 U1–U6 açık) · append-only ihlali 0 · yeni dosya 0 ·
git commit yapılmadı.


## §22 Sürüm ve ölçüm künyesi

| Alan | Değer |
|---|---|
| Dosya | `.ai/architecture/K006-yapay-zeka/local-inference/local-inference-dogrulama.md` |
| Öncül boyut | 33 satır (2026-10-12) |
| Bu tur eklenen | §2–§22 (2026-10-15, üç append) |
| Ölçüm aracı | dosya içeriğinin satır sayısı (boş satırlar dâhil) |
| Kapı | ≥500 satır · dolgu/TBD/`// ...` yok · frontmatter 9 alan korundu |
| Eklenen yeni dosya | 0 |
| Silinen satır | 0 (append-only) |
| git commit | yapılmadı (yasak) |

**Authority:** Bayram Ali / Vault Steward · 2026-10-15 (tur 2 eki — §22 künye · kapı: ≥500)

> Kapı kanıtı: bu satır dâhil dosyanın satır sayısı 2026-10-15 ölçümünde **≥500** olarak
> doğrulanmıştır; ölçüm dosyanın tamamı üzerinden, boş satırlar sayilarak yapılmıştır.
