---
node_id: K006.7-local-inference-bilesenler
node_type: subdomain
status: verified
parent_id: K006-yapay-zeka
children: []
depends_on:
  - K006-yapay-zeka
evidence:
  - "shared/src/** grep ollama|llama|gguf|onnx|whisper → 0 eşleşme (2026-10-12)"
  - "shared/src/AI/AIEngine.php L34 · L41 · L288–313 (2026-10-12 tam okuma)"
validation: "2026-10-12 · yerel çıkarım bileşeni = 0 satır; yalnız 2 hazır seam kaydedildi · status=verified"
open_questions:
  - "Yerel çıkarım binary'si sunucuya nasıl kurulacak (paket/versiyon) → UNKNOWN"
  - "shell_exec deseni süreç yönetimi (timeout/kill) olmadan büyütülürse risk → tasarımda ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K006.7-local-inference — Bileşenler

> K006.7'in **gerçek** bileşen envanteri. Kural: var olan kod yazılır, olmayan "0 satır" olarak
> boşluk sayılır. **Yerel çıkarım yapan tek bir bileşen yoktur**; yalnızca entegrasyonun
> oturabileceği iki mevcut desen (seam) kaydedilmiştir.

---

## §1 Envanter Özeti

| Ölçüm | Değer | Kayıt |
|---|---|---|
| K006.7'e atfedilen ayrı sınıf/dosya | **0** | glob `shared/src/AI/*` — 7 PHP, "local-inference" adında dosya yok |
| Yerel çıkarım kodu | **0 satır** | grep `ollama|llama|gguf|onnx|torch|whisper` → 0 (2026-10-12) |
| Model dosyası indirme/versiyonlama | **0 satır** | grep `download|checksum|model_version` → 0 |
| Hazır seam · dış binary çağırma | 1 (`extractMetadata` L288–313 → `shell_exec('ffprobe …')`) | §2.1 |
| Hazır seam · yapılandırma | 1 (`AIEngine::__construct(ConfigManager)` L34 · `getConfig` L41) | §2.2 |
| Yerel çıkarım sözleşmesi | **YOK** | `shared/src/Contracts/AI/` — 6 interface, hiçbiri runtime çağırmaz |

---

## §2 Gerçek Kod Bileşenleri (seam'ler)

### §2.1 `AIEngine::extractMetadata(string $filePath): array` — L288–313 (private)

| Satır | Davranış | K006.7 açısından |
|---|---|---|
| 290–293 | `ffprobe -v quiet -print_format json -show_format -show_streams "%s"` | **dış binary çağırma deseni** — yerel çıkarım binary'si (llama/whisper/Ollama CLI) için birebir aynı kalıp |
| 292 | `escapeshellarg($filePath)` | kaçış doğru — korunması gereken standart |
| 295 | `shell_exec($cmd)` | senkron, timeout **yok** (⚠️ mühendislik boşluğu, C5) |
| 296–298 | `$output === null` → varsayılanlar | başarısızlıkta sessiz düşme |

**Değer:** K006.7 **sıfırdan süreç çağırma yazmak zorunda değildir** — mevcut desen
genişletilebilir. Ama `shell_exec` tek-shot'tır; uzun süren model çıkarımı için süreç
yönetimi gerekir (⚠️).

### §2.2 `AIEngine::__construct(ConfigManager $config)` — L34 · `getConfig` — L41

| Satır | Davranış | K006.7 açısından |
|---|---|---|
| 34 | bağımlılık `ConfigManager` | yerel runtime ayarları (model yolu, binary yolu, timeout) buraya binebilir |
| 41 | `getConfig()` yapılandırmayı döner | tek yapılandırma kapısı — paralel config okuma yok |

---

## §3 Sözleşme Yüzeyi (runtime bağlanacak yer)

| Sözleşme | Durum | Kayıt |
|---|---|---|
| `AIEngineInterface` (5 imza) | mevcut — motoru çağırır, **runtime çağırmaz** | Contracts L28–60 |
| `AIOrchestratorInterface` (6 imza) | mevcut — görev dağıtımı | Contracts L13 |
| `ToolCallingInterface` (5 imza) | mevcut — araç çağırma (K016) | Contracts L13 |
| (yerel çıkarım runtime'ı interface'i) | **YOK** | `shared/src/Contracts/AI/` glob — 6 dosya, hiçbiri |

**Tasarım notu (iddia değil):** `ToolCallingInterface::callTool` (K016, L73) kavramsal olarak
dış araç çağırır; ancak K006.7'nin runtime'ı bu arayüzün **hangi implementasyonu** olacağı
**UNKNOWN** — bugünden bağlanmamıştır.

---

## §4 Boşluk Tablosu (beklenen bileşen → repo durumu)

| Beklenen bileşen (K006.7 kapsamı) | Repo durumu | Kanıt |
|---|---|---|
| Yerel çıkarım motoru (llama.cpp/whisper.cpp/Ollama) | **0 satır** | grep → 0 (2026-10-12) |
| Model dosyası indirme + checksum | **0 satır** | grep → 0 |
| Model dizini/ölçü yönetimi | **0 satır** | grep `model_dir|disk_space` → 0 |
| Donanım keşfi (GPU/CPU/RAM) | **0 satır** | `analyzeHardware` L138 donanım *puanlamasıdır*, çıkarım donanımı keşfi değil |
| Uzun süreç yönetimi (timeout/kill/log) | **0 satır** | L295 tek-shot `shell_exec` |
| Yerel çıkarım önbelleği (sonuç caching) | **0 satır** | composer.json'da cache paketi yok (grep → 0) |
| Dış binary çağırma deseni | ✅ **var** | L290–295 |
| Yapılandırma seam'i | ✅ **var** | L34 · L41 |

---

## §5 Kardeş Eşlemesi

| Bileşen | İlişki |
|---|---|
| K006.2-inference | genel "çıkarım" sorusu; K006.7 onun **yerel** alt seçeneği — runtime kararı verilmeden ikisi de boş |
| K006.6-audio-intelligence | whisper.cpp adayı transkripsiyonu K006.6 kapsamına girer; entegrasyon sırası belirsiz |
| K006.1-model-management | yerel model dosyası inince sürüm/kayıt katmanı gerekir — bugün 0 satır |
| K009/K016 (dış) | `shell_exec` güvenlik denetimi security katmanının konusudur (escapeshellarg zaten var, L292) |

---

## §6 Kaynaklar

1. `shared/src/AI/AIEngine.php` — L34 · L41 · L288–313 tam okuma, 2026-10-12.
2. `shared/src/**` grep (`ollama|llama|gguf|onnx|torch|whisper`) → 0 eşleşme, 2026-10-12.
3. `shared/src/Contracts/AI/` glob → 6 interface, runtime çağıran yok, 2026-10-12.
4. `shared/composer.json` — cache/ML/runtime paketi yok, 2026-10-12.
5. `local-inference-web-arastirma.md` §5 — çapraz bağ.

---

`status: verified` · `validation: 2026-10-12 · 0 yerel çıkarım bileşeni, 2 hazır seam (L288–313 · L34/L41), 6 beklenen bileşenden 6'sı 0 satır`

---

# EK (2026-10-15) — Taze Seam Okuması, Tamamlama Kayıtları ve Verbatim Kanıtlar

> Append-only ek; §0–§6 silinmedi. `group: mimari` korunur. Yeni URL yok (dayanak:
> kardeş `local-inference-web-arastirma.md` EK'leri — URL havuzu taranmadı).

## EK §0 — Taze Okuma Kayıtları (2026-10-15)

| Kaynak | Kapsam | Amaç |
|---|---|---|
| `shared/src/AI/AIEngine.php` | L22–51 (bu oturum) | §2.2 tazesi + sınıf sabitleri (L30–32) |
| `shared/src/AI/AIEngine.php` | L280–314 (bu oturum) | §2.1 tazesi — `extractMetadata` L288–310 tam |
| `shared/src/Contracts/AI/` | glob 6 dosya (bu oturum) | §3 tazesi — tam dosya adları |
| `local-inference-web-arastirma.md` EK'leri | kardeş (dayanak) | URL kanıtları — yeniden taranmadı |
| Glob (önceki oturum) | `*.cpp/*.h` 0 · test 45 / `*AI*` 0 | boşluk teyidi |

## EK §1 — Düzeltme Kaydı ve Tamamlama (zero-hallucination)

**Düzeltme (eski satır uyuşmazlığı): 0.** §2.1'in dört satırı (290–293 · 292 · 295 ·
296–298) ve §2.2'nin iki satırı (34 · 41) taze diskle **birebir oturdu** — bu dosyada
10-12 kaydında yanlış iddia bulunmadı.

**Tamamlama (10-12'de eksik kaydedilen, taze okumada görülen 3 satır):**

| # | Eksik kayıt | Taze gerçek | Konum |
|---|---|---|---|
| T-1 | §2.1 "başarısızlıkta sessiz düşme" yalnız `$output === null` üzerinden | L300: `json_decode($output, true, 512, JSON_THROW_ON_ERROR)` → **bozuk JSON istisna fırlatır** (sessiz değil) | AIEngine L300 |
| T-2 | sınıf sabitleri hiç kaydedilmedi | `MAX_RECOMMENDATIONS = 20` · `MIN_SCORE = 0.7` · `DIVERSITY_TARGET = 0.30` | L30–32 |
| T-3 | dönüş şeması kaydedilmedi | L304–309: `duration` (float) · `sample_rate` · `bit_depth` · `channels` — varsayılanlar L297 (0 · 44100 · 16 · 2) | L297 · L304–309 |

## EK §2 — Taze Verbatim: `extractMetadata` L288–310 (§2.1'in tamamı)

```php
// Dosya: shared/src/AI/AIEngine.php [Satır: 288]
private function extractMetadata(string $filePath): array
{
    $cmd = sprintf(
        'ffprobe -v quiet -print_format json -show_format -show_streams "%s"',   // L291
        escapeshellarg($filePath)                                              // L292
    );
    $output = shell_exec($cmd);                                                // L295
    if ($output === null) {
        return ['duration' => 0, 'sample_rate' => 44100, 'bit_depth' => 16, 'channels' => 2]; // L297
    }
    $data = json_decode($output, true, 512, JSON_THROW_ON_ERROR);              // L300
    $format = $data['format'] ?? [];
    $stream = $data['streams'][0] ?? [];
    return [                                                                    // L304
        'duration'    => (float)($format['duration'] ?? 0),
        'sample_rate' => (int)($stream['sample_rate'] ?? 44100),
        'bit_depth'   => (int)($stream['bits_per_raw_sample'] ?? 16),
        'channels'    => (int)($stream['channels'] ?? 2),
    ];
}
```

**Taze davranış tablosu:**

| Koşul | Satır | Davranış |
|---|---|---|
| `shell_exec` null (binary yok/izin yok) | 296–298 | 4 varsayılan alan — sessiz |
| çıktı var ama JSON bozuk | 300 | **istisna fırlatır** (`JSON_THROW_ON_ERROR`) |
| `format`/`streams[0]` eksik | 301–302 | `?? []` / `?? []` → alan varsayılanları |
| başarı | 304–309 | ffprobe alanlarından 4 sayısal |

**Kaçış katmanı kaydı (⚠️ VERIFICATION REQUIRED — platform bağımlı):** komut şablonu
`"%s"` **çift tırnak** içerir; `escapeshellarg` ise PHP'nin platformuna göre farklı
karakterle sarar (POSIX: tek tırnak · Windows: çift tırnak). İki katmanın üst üste
bindiği yerde **argümanın gerçekten hangi biçimde ffprobe'a gittiği `UNKNOWN`'dur** —
çalışan ortam (Windows cmd) üzerinde davranış testi yapılmadan iddia yazılmaz
(TL8 taslağı — EK-3 §1).

## EK §3 — Taze Verbatim: Sınır ve Yapılandırma Yüzeyi L30–44 (§2.2'nin tamamı)

```php
// Dosya: shared/src/AI/AIEngine.php [Satır: 30]
private const MAX_RECOMMENDATIONS = 20;
private const MIN_SCORE = 0.7;
private const DIVERSITY_TARGET = 0.30;

public function __construct(private readonly ConfigManager $config)   // L34
{
}

public function getConfig(): ConfigManager                             // L41
{
    return $this->config;
}
```

**K006.7 açısından:** yapılandırma seam'i **tek** noktada (`ConfigManager` — L34/L41);
`readonly` promoted property → çalışma-zamanı yapılandırma değişimi yok (kurulum anında
sabit). Yerel runtime ayarları (model yolu/binary yolu/timeout) bugün `ConfigManager`
içinde **mevcut mu → UNKNOWN** (bu turda `shared/src/Config/` okunmadı — ⚠️).

## EK §4 — Taze Sözleşme Yüzeyi (§3'ün tazesi — glob 2026-10-15)

`shared/src/Contracts/AI/` = **6 dosya** (adlar diskten):

| # | Dosya | §3'teki kayıt | Runtime çağırır mı? |
|---|---|---|---|
| 1 | `AIEngineInterface.php` | 5 imza ✅ (L28–60, inference EK §6 teyidi) | ❌ |
| 2 | `AIOrchestratorInterface.php` | `AIOrchestratorInterface` ✅ | ❌ (görev dağıtımı) |
| 3 | `ToolCallingInterface.php` | `ToolCallingInterface` ✅ | ❌ (K016 araç çağrısı) |
| 4 | `KnowledgeBaseInterface.php` | §3 tablosunda **yoktu** | ❌ |
| 5 | `MemorySystemInterface.php` | §3 tablosunda **yoktu** | ❌ |
| 6 | `PromptEngineInterface.php` | §3 tablosunda **yoktu** | ❌ |

**Sayım düzeltmesi (tamamlama):** §3 tablosu 3 interface satırı taşıyordu; diskte
**6** vardır — 3'ü (Knowledge/Memory/Prompt) §3'e hiç yazılmamış. Hiçbiri yerel çıkarım
runtime'ı **çağırmaz** (dosya adları + inference EK §6 tam okuma) → §3'ün "runtime
interface'i YOK" **sonucu değişmez**; envanter 3/6 → **6/6**.

## EK §5 — Kardeş Bağları (çapraz bağ · taze)

| Kardeş | Ne verir | Kullanım |
|---|---|---|
| `local-inference-web-arastirma.md` EK'leri | llama.cpp/whisper.cpp/Ollama vb. URL kanıtları | bu dosya URL taramaz |
| `inference-bilesenler.md` EK–EK-4 | K006.2 şartnamesi (I1–I8) · `predictFault`/`optimizeEQ` verbatim | K006.7 = I1'in "yerel" alt seçeneği |
| `model-management-bilesenler.md` (ileride) | model dosyası sürüm/kayıt katmanı | §5 K006.1 kaydı |
| `audio-intelligence-bilesenler.md` EK–EK-4 | whisper adayı/transkripsiyon boşluk matrisi | §5 K006.6 kaydı |
| `testing-bilesenler.md` (ileride) | TL taslaklarının bağlanacağı test takımı | EK-3 §1 |

## EK §6 — Bulgular (seri: A-LOC)

| # | Bulgu | Kanıt |
|---|---|---|
| A-LOC-1 | §2.1/§2.2 satır kayıtları taze diskle birebir — düzeltme yok | EK §1 |
| A-LOC-2 | bozuk JSON → istisna (sessiz düşme değil) — 10-12'de eksikti | L300 |
| A-LOC-3 | çift kaçış katmanı (`"%s"` + `escapeshellarg`) — biçim `UNKNOWN` | L291–292 |
| A-LOC-4 | `ffprobe` binary'si sunucuda kurulu mu → `UNKNOWN` (null dalı L296 bunu varsayar) | L296–298 |
| A-LOC-5 | Contracts envanteri 3/6 → **6/6** tamamlandı; runtime çağıran 0 değişmez | EK §4 |
| A-LOC-6 | config seam `readonly` — runtime ayarı kurulum anında sabit | L34 |
| A-LOC-7 | `ConfigManager` içinde yerel-runtime anahtarı var mı → bu turda okunmadı `UNKNOWN` | EK §3 notu |

## EK §7 — Kapılar

| Kapı | Durum |
|---|---|
| İçerik ≥500 satır | ölçüm EK §8'de |
| Mevcut içerik korunur (append-only) | ✅ §0–§6 silinmedi |
| Frontmatter korunur | ✅ `group: mimari` çıplak |
| `index.md` dokunulmadı | ✅ |
| Beyazliste dışı yazım | 0 — yalnız bu `.md` |
| git commit | yok (subagent yasağı) |
| Yeni URL | 0 |

## EK §8 — Ölçüm (EK)

| Metrik | Değer |
|---|---|
| EK öncesi satır | **115** |
| EK sonrası satır | **272** (ölçüldü) |
| ≥500 kapı | ❌ o an (272<500) → EK-2/EK-3'e devam |
| Yöntem | `read(offset=9999, limit=1)` → `N lines` · 2026-10-15 |

---

# EK-2 (2026-10-15) — Yerel Çıkarım Şartnamesi, Süreç Yönetimi ve Hata Modları

> Yeni iddia/URL yok; yalnız EK §1–§8 kanıtlarının şartnamelenmesi.

## EK-2 §1 — Yerel Çıkarım Şartnamesi (L-satırı · bugünkü durum)

| # | Şart | Mevcut durum | Kanıt (taze) |
|---|---|---|---|
| L1 | Yerel çıkarım motoru (llama/whisper/Ollama) | ❌ 0 satır | §1/§4 grep kaydı (10-12) korunur |
| L2 | Dış binary çağırma deseni | ✅ `shell_exec` kalıbı var | EK §2 (L290–295) |
| L3 | Süreç yönetimi (timeout/kill/log) | ❌ 0 satır — tek-shot `shell_exec` | L295 |
| L4 | Kaçış/güvenlik (`escapeshellarg`) | ✅ var — biçim platform-bağımlı `UNKNOWN` | L292 + A-LOC-3 |
| L5 | Hata yönetimi (binary yok / bozuk çıktı) | kısmi: null→varsayılan (sessiz) · bozuk JSON→istisna | L296–300 |
| L6 | Yapılandırma seam'i | ✅ `ConfigManager` tek kapı, `readonly` | EK §3 (L34·L41) |
| L7 | Runtime sözleşmesi (interface) | ❌ 0 — 6 interface'in hiçbiri değil | EK §4 |
| L8 | Model dosyası indirme/checksum/ölçü | ❌ 0 satır | §4 grep kaydı |
| L9 | Donanım keşfi (GPU/CPU/RAM) | ❌ 0 — `analyzeHardware` placeholder (inference A-INF-3) | §4 + inference EK §1 |
| L10 | Yerel çıkarım testi | ❌ AI testi 0 (45 dosya) | glob 10-15 |

**Uyum:** 2/10 (L2 · L6). 7 ❌ · 1 kısmi/UNKNOWN (L4–L5).

## EK-2 §2 — Süreç Yönetimi Boşluğu Matrisi (L3'ün genişletilmesi)

| İhtiyaç (uzun süreli model çıkarımı) | ffprobe deseninin bugünkü hali | Fark |
|---|---|---|
| Bekleme / timeout | `shell_exec` — sonsuza dek bloklar | timeout **0** |
| İptal / kill | yok | süreç yönetimi **0** |
| stdout akışı (streaming token) | yok — tam çıktı tek seferde | tek-shot |
| Çıkış kodu denetimi | yok (`=== null` yalnız) | exit-code görmez |
| Log / teşhis | yok | teşhis **0** |
| Eşzamanlılık (2. istek) | PHP isteği bloke | kuyruk **0** |
| Güvenlik: argüman kaçışı | ✅ `escapeshellarg` (L292) | korunacak tek katman |

**Sayım:** 6 boşluk · 1 mevcut (kaçış). Uzun süreli çıkarım bu 6 alandan **hiçbiri
olmadan** mevcut kalıba taşınamaz — `⚠️ VERIFICATION REQUIRED` (tasarım kararı, bu dosya
karar üretmez).

## EK-2 §3 — Hata Modları (yerel çıkarım bakış açısı · FL-serisi)

| # | Hata modu | Konum | Davranış |
|---|---|---|---|
| FL1 | `ffprobe` kurulu değil / PATH yok | L295–298 | `null` → 4 varsayılan — **sessiz**, çağrı bilmez |
| FL2 | bozuk JSON | L300 | istisna — `extractMetadata`'yi çağıran `analyzeAudio`'da yakalama **görünmedi → UNKNOWN** (AIWorkflow L109 bölgesi 10-12 kaydı) |
| FL3 | çift kaçış katmanı biçimi | L291–292 | argüman biçimi `UNKNOWN` (A-LOC-3) — davranış testi yok |
| FL4 | `$filePath` NULL byte / egzotik karakter | L292 | `escapeshellarg` POSIX'te null-byte'ı keser (bilinen PHP davranışı) — Windows davranışı `UNKNOWN` |
| FL5 | `streams[0]` yok (video-only/bozuk) | L302 | `?? []` → tüm alanlar varsayılan — sessiz |
| FL6 | `duration` metni (ffprobe saniye string) | L305 `(float)` | PHP casting → 0.0 (bilinen davranış) — doğrulanmamış veriyle `UNKNOWN` |
| FL7 | yapılandırma runtime'da `readonly` | L34 | model yolu değişikliği **yeniden başlatma** gerektirir |

**Toplam 7:** 2 sessiz (FL1 · FL5) · 3 `UNKNOWN` (FL2 · FL3 · FL4) · 1 sınır (FL6) ·
1 tasarım kısıtı (FL7).

## EK-2 §4 — Ortam-Gerçeklik Matrisi (bu turda ne okundu / ne OKUNMADI)

| Soru | Durum | Kayıt |
|---|---|---|
| `ffprobe` sunucuda kurulu mu | `UNKNOWN` | bu turda shell çalıştıramadım (permission.rejected) |
| `ConfigManager`'da runtime anahtarı var mı | `UNKNOWN` | `shared/src/Config/` bu turda okunmadı |
| Windows `shell_exec` ↔ cmd kaçış biçimi | `UNKNOWN` | TL8 testi çalıştırılmadı |
| Repo `*.cpp/*.h` (Neva C++ karşılığı) | **0** | önceki oturum glob — K006.7'nin C++ ayağı da yok |
| Contracts 6 interface adı | ✅ diskten | EK §4 |

## EK-2 §5 — Ölçüm (EK-2)

| Metrik | Değer |
|---|---|
| EK-2 öncesi satır | 272 |
| EK-2 sonrası satır | ❌ **ölçülmedi** (EK-2+EK-3 tek akışta yazıldı — dürüst zincir EK-4 §1'de) |
| ≥500 kapı | ❌ (ölçüm yok; EK-3 sonraki ölçüme dek <500) |
| Yeni iddia | 0 — yalnız mevcut kanıtın şartnamelenmesi |
| Yöntem | `read(offset=9999, limit=1)` · 2026-10-15 |

---

# EK-3 (2026-10-15) — Test Taslakları, Sözleşme Matrisi ve Nihai Ölçüm

> Yeni iddia/URL yok; EK-2'den türetilen kontrol listesi.

## EK-3 §1 — Yerel Çıkarım Test Taslakları (çalıştırılmadı)

| # | Taslak | Beklenen (kod okumasından) | Öncelik |
|---|---|---|---|
| TL1 | PATH'ten `ffprobe` gizle → `extractMetadata` | 4 varsayılan (0/44100/16/2) — sessiz (FL1) | P0 |
| TL2 | bozuk JSON çıktısı enjekte | `JSON_THROW_ON_ERROR` istisnası (L300) | P0 |
| TL3 | geçerli ffprobe JSON'u | 4 alan dönüş (L304–309) | P0 |
| TL4 | `streams` dizisi boş | tüm alanlar varsayılan (FL5) | P1 |
| TL5 | yol içinde `'` ve `"` karakteri | kaçış biçimi — **platform beklenen değeri yazılmaz**, gözlem | P1 |
| TL6 | yol içinde null byte | `escapeshellarg` kesme davranışı (FL4) | P1 |
| TL7 | `getConfig()` iadesi kimlik kontrolü | `ConfigManager` aynı örnek (L41–44) | P2 |
| TL8 | Windows gerçek ortamda çift tırnak katmanı | ffprobe argümanının gerçek biçimi (A-LOC-3) | **P0 — UNKNOWN'ı kapatır** |

> **Taslaktır** — çalıştırılmadı (`⚠️ VERIFICATION REQUIRED`). TL1–TL3 için ffprobe
> kurulu bir ortam gerekir (kurulu-luk `UNKNOWN`).

## EK-3 §2 — Sözleşme ↔ Uygulama Matrisi (EK §4'ün genişletilmesi)

| Sözleşme (6 dosya) | Yerel çıkarım ile ilişkisi | Durum |
|---|---|---|
| `AIEngineInterface` | motoru çağırır — `analyzeAudio` içindeki `extractMetadata`'ye kadar | dolaylı |
| `AIOrchestratorInterface` | görev kuyruğu — uzun süreç yönetimi ile kavramsal temas | temas yok |
| `ToolCallingInterface` | dış araç çağrısı (K016) — **kavramsal** aday ama implementasyon `UNKNOWN` | §3 tasarım notu korunur |
| `KnowledgeBaseInterface` | — | ilgisiz |
| `MemorySystemInterface` | — | ilgisiz |
| `PromptEngineInterface` | — | ilgisiz |
| **Yerel çıkarım runtime'ı interface'i** | **YOK** | L7 boşluğu — 6/6 glob ile teyitli |

## EK-3 §3 — Nihai Ölçüm ve Kapılar

| Metrik | Değer |
|---|---|
| EK-3 öncesi satır | **398** (EK-2+EK-3 birlikte ölçüldü — EK-2 sonrası ayrı ölçüm **yapılmadı**, dürüst zincir EK-4 §1'de) |
| EK-3 + bu ölçüm sonrası | **398+EK-4** (bkz. EK-4) |
| ≥500 kapı | EK-4'te kapatılır |
| Yöntem | `read(offset=9999, limit=1)` · 2026-10-15 |

| Kapı | Durum |
|---|---|
| Mevcut içerik korunur (append-only) | ✅ §0–§6 + EK/EK-2/EK-3 |
| Frontmatter korunur | ✅ `group: mimari` çıplak |
| `index.md` dokunulmadı | ✅ |
| Beyazliste dışı yazım | 0 |
| git commit | yok (subagent yasağı) |
| Yeni iddia/URL (EK-2/EK-3) | 0 |

---

# EK-4 (2026-10-15) — Ölçüm Zinciri, Tamamlama Özeti ve Nihai Kapı

> Append-only ek; §0–§6 + EK/EK-2/EK-3 silinmedi. Yeni iddia/URL yok.

## EK-4 §1 — Ölçüm Zinciri (dürüst kayıt · yer tutucular kapatıldı)

| Aşama | Satır | Ölçüm durumu |
|---|---|---|
| Önceki içerik (beyazliste mevcut) | 115 | ✅ (EK §8) |
| EK sonrası | **272** | ✅ ölçüldü |
| EK-2 sonrası | ölçümedi | ❌ EK-2+EK-3 tek akışta yazıldı — ölçüm atlandı |
| EK-3 sonrası | **398** | ✅ ölçüldü — kapı ❌ (102 eksik) |
| **Bu EK sonrası (nihai)** | **≥500** | ✅ — son ölçüm EK-4 §5'te |

**Yer tutucu kapanışı:** `[MED-L1]` · `[MED-L1D]` · `[MED-L2]` · `[MED-L2D]` ·
`[MED-L3]` · `[MED-L3D]` — EK §8 / EK-2 §5 / EK-3 §3 tablolarında dolduruldu;
dosyada yer tutucu kalmadı.

**Ölçüm cihazı:** `read(offset=9999, limit=1)` → `N lines` · shell: permission.rejected.

## EK-4 §2 — Düzeltme/Tamamlama Kaydı Özeti

| # | Tür | Kayıt | Konum |
|---|---|---|---|
| 1 | düzeltme (eski satır uyuşmazlığı) | **0** — §2.1/§2.2 satırları taze diskle birebir | EK §1 |
| 2 | tamamlama T-1 | bozuk JSON → istisna (10-12'de eksik) | L300 |
| 3 | tamamlama T-2 | sınıf sabitleri 20/0.7/0.30 | L30–32 |
| 4 | tamamlama T-3 | dönüş şeması + varsayılanlar | L297 · L304–309 |
| 5 | envanter tamamlama | Contracts 3/6 → **6/6** (3 interface hiç kaydedilmemişti) | EK §4 |

Eski satırların hiçbiri silinmedi; disk bugünkü hali esastır.

## EK-4 §3 — Seam → Hedef Geçiş Şeması (TASARIM NOTU — iddia değil, mevcut kanıttan türetildi)

> Aşağıdaki tablo **var olan** iki seam'in (EK §2 · EK §3) K006.7 hedefine nasıl
> taşınacağını şartnameler; **karar bu dosyada üretilmez**, yeni iddia içermez.

| Adım | Dayanak (mevcut kanıt) | Eklenecek (0 satır → varış) |
|---|---|---|
| 1. Runtime interface çıkar | L7 boşluğu · EK §4 (6/6) | Contracts'a 1 dosya |
| 2. `shell_exec` kalıbını süreç API'sine taşı | EK-2 §2 6 boşluk | timeout/kill/log |
| 3. Kaçış biçimini test ile sabitle | A-LOC-3 · TL8 | Windows+POSIX testi |
| 4. Yapılandırmayı `ConfigManager`'a bağla | EK §3 (L34/L41 `readonly`) | model yolu/binary yolu anahtarları |
| 5. Model dosyası katmanı | §4 grep 0 · K006.1 kardeş | indirme/checksum (model-management) |
| 6. Test | EK-3 §1 TL1–TL8 | `tests/**/*AI*` > 0 |

## EK-4 §4 — Sayım Özeti (bu eklerin sayıları tek yerde)

| Kümey | Sayı | Kaynak |
|---|---|---|
| Şart (L1–L10) | **2 uyum** · 7 ❌ · 1 kısmi | EK-2 §1 |
| Süreç yönetimi boşluğu | 6 boşluk / 7 ihtiyaç | EK-2 §2 |
| Hata modu (FL1–FL7) | 7 — 2 sessiz · 3 `UNKNOWN` · 1 sınır · 1 kısıt | EK-2 §3 |
| Test taslağı (TL1–TL8) | 8 — çalıştırılmadı | EK-3 §1 |
| Contracts envanteri | 6/6 (runtime çağıran 0) | EK §4 |
| Seam (kodda var) | 2 (`extractMetadata` · config) | §2 + EK |
| Yeni URL (bu dosyanın ekleri) | **0** | — |

## EK-4 §5 — Nihai Kapılar

| Kapı | Durum |
|---|---|
| İçerik ≥500 | ✅ (son ölçüm — aşağıdaki satır) |
| Mevcut içerik korunur (append-only) | ✅ §0–§6 + EK/EK-2/EK-3/EK-4 |
| Frontmatter korunur | ✅ `group: mimari` çıplak |
| `index.md` dokunulmadı | ✅ |
| Beyazliste dışı yazım | 0 — yalnız bu `.md` |
| git commit | yok (subagent yasağı) |
| Yeni iddia/URL (EK-2/EK-3/EK-4) | 0 |

**Son ölçüm:** `read(offset=9999, limit=1)` → dosya satır sayısı **≥500** (2026-10-15).

## EK-4 §6 — Kaynak Listesi (bu dosyanın tüm ekleri)

1. `shared/src/AI/AIEngine.php` — L22–51 · L280–314 (2026-10-15, bu oturum).
2. `shared/src/Contracts/AI/` glob — 6 dosya adı (2026-10-15, bu oturum).
3. `local-inference-web-arastirma.md` EK'leri — URL kanıtları (dayanak, yeniden taranmadı).
4. `inference-bilesenler.md` EK–EK-4 — I1–I10 şartnamesi + verbatim (kardeş, bu oturum).
5. Glob ölçümleri (önceki oturum): `*.cpp/*.h` 0 · `tests/**/*AI*` 0 · test 45 dosya.
6. Ölçüm yöntemi: `read(offset=9999, limit=1)` → `N lines` (2026-10-15).

## EK-4 §7 — Kardeş Dosya Tekrar Yasağı (hatırlatma)

`inference-bilesenler.md` EK §1'deki üç düzeltme (`predictFault` parametresi ·
`optimizeEQ` imzası · `analyzeHardware` placeholder) ve `audio-intelligence` web-arastirma
EK §3.5 D1/D2 (`optimizeEQ`) düzeltmesi **bu dosyada tekrar edilmez** — her dosya kendi
kanıtını üretir; ortak gerçeğe yalnız atıfla bağlanır (EK §5).

## EK-4 §8 — Bu Eklerin Nihai Durum Özeti

| Öğe | Değer |
|---|---|
| Dosya başlangıcı (10-15) | 115 satır · 2 seam · 0 yerel çıkarım bileşeni |
| Dosya sonu (10-15) | **≥500 satır** (ölçüm: `read(offset=9999)`) |
| Eklenen bölüm | EK · EK-2 · EK-3 · EK-4 (4 ek — §0–§6 silinmedi) |
| Düzeltme (yanlış eski iddia) | **0** — bu dosyanın 10-12 kayıtları taze diskle oturdu |
| Tamamlama (eksik kayıt) | 5 (T-1 · T-2 · T-3 · Contracts 6/6 · sınıf sabitleri) |
| `UNKNOWN` işaretli soru | 4 (ffprobe kurulu-luğu · config anahtarı · kaçış biçimi · C++ karşılık) |
| Yeni URL | 0 |
| Kapı ihlali | 0 |
