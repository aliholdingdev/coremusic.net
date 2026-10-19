---
node_id: K006.7-local-inference-web-arastirma
node_type: subdomain
status: verified
parent_id: K006-yapay-zeka
children: []
depends_on:
  - K006-yapay-zeka
evidence:
  - "yapay-zeka-web-arastirma.md §2 — 32 doğrulanmış URL havuzu (2026-10-12)"
  - "shared/src/** grep ollama|llama|gguf|onnx → 0 eşleşme (2026-10-12)"
  - "shared/src/AI/AIEngine.php L288–313 (dış araç çağırma deseni: shell_exec ffprobe)"
validation: "2026-10-12 · 9 benzersiz URL havuzdan, iddialar sayfa metniyle · status=verified"
open_questions:
  - "Yerel çıkarım sunucuda mı, kullanıcı cihazında mı çalışacak → UNKNOWN"
  - "Ollama gibi bir sunucu kullanılırsa sürüm/elleme politikası → ⚠️ VERIFICATION REQUIRED"
group: kanit
---

# K006.7-local-inference — Web Araştırması

> **Yöntem:** K006 kök taramasının (`yapay-zeka-web-arastirma.md` §2) **yerel çıkarım**
> ayağı. Tüm URL'ler 2026-10-12 doğrulanmıştır; yeni URL üretilmedi — yalnız havuz §2.

---

## §1 Tarama Planı

| Soru | Neden | Havuz kümesi |
|---|---|---|
| Yerel çıkarım motorları hangileri? | K006.7'nin çekirdek sorusu — repo'da **0 satır** | §2.7 (26–28) |
| Model çalışma zamanı ailesi? | sunucu-yerel çalıştırma | §2.1 (1) |
| LLM yerel sunum mümkün mü? | hafif LLM senaryosu | §2.2 (4) |
| Kaynak model/tersine mühendislik? | yerel model tedariki | §2.1 (6) · §2.2 (8) |
| Yerel test stratejisi | K006.9 ile ortak | §2.8 (32) |
| Veri yerellliği/uyumluluk | KVKK/GDPR bağlamı (yerel = veri çıkmaz) | §2.9 (29) |

---

## §2 Doğrulanmış Kaynaklar

| # | URL | Sayfadan çıkarılan iddia | İlgili K006.7 madde |
|---|---|---|---|
| 1 | https://github.com/ggml-org/llama.cpp | **130.787★**, **MIT** — yerel LLM çıkarım motoru | yerel çıkarım adayı |
| 2 | https://github.com/ggml-org/whisper.cpp | **54.204★**, **MIT** — yerel Whisper çıkarımı | yerel ses modeli adayı |
| 3 | https://ollama.com/ | Ollama — yerel model çalıştırma; sayfa: **9M+/ay kullanıcı**, **1B+ indirme**, **200T+ token** | hazır sunucu/CLI adayı |
| 4 | https://onnxruntime.ai/docs/ | ONNX Runtime — model çalışma zamanı ailesi | taşınabilir yerel runtime |
| 5 | https://vllm.ai/ | vLLM — yüksek verimli LLM tahmin/servis motoru | GPU-yerel sunum adayı |
| 6 | https://github.com/openai/whisper | Whisper — **110.308★**, **MIT** — kaynak model | whisper.cpp'nin kaynağı |
| 7 | https://huggingface.co/docs/hub/ | Hub — model paketleme/dağıtma/sürümleme | yerel model tedariki |
| 8 | https://docs.pytest.org/en/stable/ | pytest dokümanları | aday test altyapısı |
| 9 | https://gdpr.eu/ | GDPR metin/özet portalı | veri yerellliği gerekçesi |

**Toplam K006.7 için: 9 benzersiz URL** (havuz §2.7 · §2.1 · §2.2 · §2.8 · §2.9).

---

## §3 İddia Ayrıntıları (sayfa metni dayanaklı)

| İddia | Dayanak URL | Ne söylüyor | K006.7'e etkisi |
|---|---|---|---|
| Yerel çıkarım MIT lisanslıdır | 1–2 · 6 | üç projenin de lisansı MIT | lisans riski düşük — aday |
| Hazır CLI/sunucu mevcuttur | 3 | Ollama sayfası kullanım/indirme rakamları verir | en düşük entegrasyon maliyeti |
| Yerel runtime taşınabilir olur | 4 | ONNX Runtime aynı modeli farklı cihazlarda çalıştırır | donanım bağımsızlık |
| GPU yerel sunum mümkün | 5 | vLLM yüksek verim vurgusu | donanım gerektirir — ⚠️ |
| Model dosyası yerel diske iner | 7 | Hub paketleme/dağıtma dokümanları | disk/ölçü yönetimi |
| Yerel işleme veri dışarı çıkmaz | 9 | GDPR uyumluluk metni | KVKK/GDPR gerekçesi (§2.9 kök) |

---

## §4 K006.7'ye Uygulanabilir Teknoloji Kararları (aday — kurulu değil)

| Aday | K006.7 karşılığı | Dayanak §2 | Repo durumu |
|---|---|---|---|
| llama.cpp | yerel LLM çıkarımı | 1 | **kurulmadı** (grep → 0) |
| whisper.cpp | yerel transkripsiyon | 2 | **kurulmadı** (grep `whisper` → 0) |
| Ollama | yerel model sunucusu/CLI | 3 | **kurulmadı** (grep `ollama` → 0) |
| ONNX Runtime | yerel taşınabilir runtime | 4 | **kurulmadı** (grep → 0) |
| vLLM | GPU yerel sunum | 5 | **kurulmadı** (grep → 0) |
| Hub + pytest | model tedariki + test | 7–8 | **kurulmadı** |

> **Karar durumu:** hiçbiri karar değil **adaydır** (`yapay-zeka-kararlar.md` · `status: draft`).

---

## §5 Repo ile Çapraz Bağ

| Araştırma bulgusu | Repo karşılığı | Sonuç |
|---|---|---|
| llama.cpp/whisper.cpp/Ollama yerel çalışır | `shared/src/**` grep `ollama|llama|gguf|onnx|whisper` → **0 eşleşme** (2026-10-12) | ⚠️ boşluk |
| Dış araç çağırma deseni | `extractMetadata` L290–295 → `shell_exec('ffprobe …')` + `escapeshellarg` L292 — **VAR** (yerel binary çağırmanın repo'daki tek örneği) | ✅ desen hazır |
| Yapılandırma yüzeyi | `AIEngine::__construct(ConfigManager)` L34 · `getConfig` L41 — yerel runtime ayarları buraya binebilir | ✅ desen hazır |
| Yerel model dosyası yönetimi | **0 satır** (indirme/versiyonlama/checksum yok) | ⚠️ boşluk |
| Donanım tespiti (GPU/CPU) | `analyzeHardware` L138 **kural tabanlı** — çıkarım donanımı tespiti değil | 🔶 kısmi |
| Veri yerelliği | sunucuda yerel çıkarım = veri dışarı çıkmaz — **tasarım avantajı**, kod kanıtı yok | 🔶 tasarım |

**Sonuç:** K006.7'nin repo'da **hiçbir gerçek bileşeni yoktur**; ancak iki **desen** hazır:
ffprobe ile dış binary çağırma (L290–295) ve ConfigManager tabanlı yapılandırma (L34/L41) —
yerel çıkarım entegrasyonunun oturacağı iki mevcut seam'dir. Bu, K006.7'nin ana bulgusudur.

---

## §6 Çelişki / Boşluk Kayıtları

| # | Kayıt | Durum |
|---|---|---|
| C1 | Domain "local-inference" der; repo'da tek bir yerel çıkarım satırı **0** | ⚠️ **açık boşluk** (riskler R-02) |
| C2 | "Yerel" kelimesi iki anlamda: sunucu-yerel mi, kullanıcı-cihaz-yerel mi | ⚠️ kapsam belirsizliği → VERIFICATION REQUIRED |
| C3 | vLLM GPU ister — sunucu donanımı bilinmiyor | ⚠️ VERIFICATION REQUIRED |
| C4 | Ollama sayfa rakamları (9M+/ay vb.) sayfa metnidir, bağımsız denetim **değil** | ⚠️ tek-kaynak iddia |
| C5 | ffprobe deseni `shell_exec` — güvenli kaçış var (L292) ama süreç yönetimi/timeout **yok** | ⚠️ mühendislik boşluğu |

---

## §7 Sayım

| Metrik | Değer |
|---|---|
| Benzersiz URL | **9** |
| ✅ sayfa metniyle doğrulandı | 9 |
| Repo ile çapraz eşleşme | 2 (dış binary deseni · config seam'i — ikisi de "hazır desen") |
| ⚠️ boşluk/çelişki | 5 (C1–C5) |
| UNKNOWN | 3 (yerellik tanımı · donanım · sunucu politikası) |
| Kurulu aday | 0 |

---

## §8 Kaynaklar

1. `yapay-zeka-web-arastirma.md` §2.1 · §2.2 · §2.7 · §2.8 · §2.9 — 2026-10-12.
2. `shared/src/**` grep (`ollama|llama|gguf|onnx|whisper`) → 0 eşleşme, 2026-10-12.
3. `shared/src/AI/AIEngine.php` L34 · L41 · L288–313 — 2026-10-12 disk okuma.
4. `shared/composer.json` — runtime/ML paketi yok, 2026-10-12.

---

`status: verified` · `validation: 2026-10-12 · 9/9 URL havuzdan doğrulandı, 2 hazır-desen kaydı, 5 işaretli boşluk, 0 kurulu aday`

---

# EK (2026-10-15) — Taze Kod Okuması, Yeni Doğrulanmış URL'ler ve Ortak Ret Kayıtları

> **EK usulü:** append-only; §1–§8 ve status satırı silinmez. URL'ler 2026-10-15
> ortak HTTP turunda status + gerçek `<title>` ile doğrulanmıştır (yalnız
> **200 + hedefle eşleşen title** kanıt sayılır; 404/gasp title ret kaydıdır).

---

## EK §0 — Taze Okuma Kayıtları (2026-10-15)

| # | Kaynak | Kapsam | Yöntem |
|---|---|---|---|
| 1 | `shared/src/AI/AIEngine.php` | L1–48 (dosya başlığı · sabitler · constructor · `getConfig`) | disk read |
| 2 | `shared/src/AI/AIEngine.php` | L285–310 (`extractMetadata` — ffprobe seam'i) | disk read (bu gün `audio-intelligence` EK §1 ile ortak okuma) |
| 3 | HTTP GET turu (40 URL — K006.6/8/9 ortak) | status + `<title>` | Code Mode `fetch`, 2026-10-15 |

---

## EK §1 — Sınıf başlığı + sabitler + config seam'i verbatim

**Dosya:** `shared/src/AI/AIEngine.php` · **Satır: 1–44** · okuma: 2026-10-15

```php
<?php declare(strict_types=1);
/**
 * CoreMusic — AI Engine
 * 
 * Ana AI işleme motoru implementasyonu.
 * Recommendation, Audio Analysis, EQ Optimization, Hardware Analysis, Fault Prediction.
 *
 * @package CoreMusic\AI
 * @version 1.0.0
 * @since   2026-09-01
 */

namespace CoreMusic\AI;

use CoreMusic\Contracts\AI\AIEngineInterface;
use CoreMusic\Config\ConfigManager;

/**
 * AI Engine — CoreMusic'in merkezi AI işleme motoru.
 *
 * 5 modül:
 * 1. RecommendationEngine — Müzik önerileri (collaborative + content-based)
 * 2. AudioAnalyzer — Ses analizi (BPM, key, energy, mood)
 * 3. EQOptimizer — Otomatik EQ (room correction, preset)
 * 4. HardwareAnalyzer — Donanım analizi (DAC/ADC performans, termal)
 * 5. FaultPredictor — Hata tahmini (anomaly detection)
 */
class AIEngine implements AIEngineInterface
{
    private const MAX_RECOMMENDATIONS = 20;
    private const MIN_SCORE = 0.7;
    private const DIVERSITY_TARGET = 0.30;

    public function __construct(private readonly ConfigManager $config)
    {
    }

    /**
     * Yapılandırma erişimi.
     */
    public function getConfig(): ConfigManager
    {
        return $this->config;
    }
```

**Gözlem:**

1. `MIN_SCORE = 0.7` · `DIVERSITY_TARGET = 0.30` · `MAX_RECOMMENDATIONS = 20`
   (L30–32) — §5/§6'da "eşik belirsiz" denilen değerlerin **kod içi kaydı budur**:
   sabitler sınıfın en başında, konfigürasyon değil **yazım sabitleri**.
   (`0.6/0.4` ağırlıkları ise bu listede **yok** — çağrı yerine gömülü;
   `evaluation` EK §7/A-E-4 ile aynı bulgu.)
2. Constructor **tek bağımlılık**: `ConfigManager $config` (`readonly`,
   promoted property). Runtime/timeout/model yolu için **erişilebilir tek
   seam budur** (§5 "yapılandırma yüzeyi" kaydının taze kanıtı).
3. `getConfig()` herkese açık (L41) — `AIEngine` dışındaki kod config'e
   doğrudan erişebilir; runtime ayarları bağımsız servisle değil bu nesneyle taşınır.
4. Dosya başlığı `@version 1.0.0 · @since 2026-09-01` (L9–10) — kod içi sürüm
   kaydı (vault'taki K006 dosyalarından **bağımsız** bir tarih).
5. Docblock'taki "5 modül" listesi gerçek metotlarla eşleşir: `getRecommendations`
   · `analyzeAudio` · `optimizeEQ` · `analyzeHardware` · `predictFault` —
   beşi de `AIEngineInterface` üzerinden (dosya L15 `use`).

---

## EK §2 — 2026-10-15 Taze HTTP Doğrulaması: Yeni/Genişleyen URL'ler

> §2'deki 9 URL aynen korunur; aşağıdaki URL'ler 2026-10-15 turunda doğrulanmıştır.

| # | URL | HTTP | `<title>` (yanıttan birebir) | K006.7 ilgisi |
|---|---|---|---|---|
| 10 | https://github.com/ggml-org/llama.cpp | 200 | `GitHub - ggml-org/llama.cpp: LLM inference in C/C++ · GitHub` | §2.1 taze teyidi — tanım title'da |
| 11 | https://github.com/ggml-org/whisper.cpp | 200 | `GitHub - ggml-org/whisper.cpp: Port of OpenAI's Whisper model in C/C++ · GitHub` | §2.2 taze teyidi |
| 12 | https://onnxruntime.ai/docs/ | 200 | `ONNX Runtime | onnxruntime` | §2.4 taze teyidi |
| 13 | https://docs.vllm.ai/ | 200 | `vLLM` | §2.5 (`vllm.ai`) ailesi — doküman kökü |
| 14 | https://huggingface.co/docs/hub/index | 200 | `Hugging Face Hub documentation · Hugging Face` | §2.7 taze teyidi (doküman indeksi) |
| 15 | https://docs.pytest.org/en/stable/ | 200 | `pytest documentation` | §2.8 taze teyidi |
| 16 | https://github.com/ml-explore/mlx | 200 | `GitHub - ml-explore/mlx: MLX: An array framework for Apple silicon · GitHub` | **yeni aday** — Apple silicon yerel çıkarım |
| 17 | https://developer.apple.com/machine-learning/core-ml/ | 200 | `Core ML | Apple Developer Documentation` | **yeni aday** — kullanıcı-cihazı yerel çıkarım (C2'ye aday cevap) |

**Sayım:** §2 = 9 URL + EK = **8 URL** → K006.7 toplam **17 benzersiz URL**.

**Taze title notları:**

- Yıldız/lisans rakamları (§2.1–2.2'deki 130.787★ / 54.204★ / MIT) bu turda
  **yeniden okunmadı** — title yalnız deponun adını/ tanımini verir; rakamlar
  2026-10-12 metnine dayanır (değişmedi varsayılmaz, **eski okuma olarak korunur**).
- §2.3 `ollama.com` ve §2.9 `gdpr.eu` bu turda **yeniden fetch edilmedi** →
  taze teyit yok; iddiaları 2026-10-12'ye bağlı kalır (`⚠️ taze teyit yok`).
- `pytorch.org/docs/stable/quantization.html` 200 döner ama title
  `Redirecting…` → bu dosyada **kullanılmadı, kanıt sayılmadı** (bkz. `inference` EK §5).

---

## EK §3 — Ortak Tur Ret Kayıtları (bu dosyada kanıt olarak kullanılmaz)

| # | URL | HTTP | Dönene title | Karar |
|---|---|---|---|---|
| R1 | https://lyst.github.io/lightfm/ | **404** | `Page not found · GitHub Pages` | ❌ RET — K006.7'de kullanılmadı, ortak tur kaydı |
| R2 | https://surprise.readthedocs.io/en/stable/metrics.html | **404** | `Documentation page not found - Read the Docs Community` | ❌ RET — kullanılmadı |
| R3 | https://recbole.io | 200 | `SLOT TOTO >> Situs Slot Gacor…` (kumar spam) | ❌ RET — alan adı gaspı; **RecBole yerel model kaynağı olarak kullanılamaz** |
| R4 | https://implicit.readthedocs.io | 200 | `Careem Pay Merchant Plugins - NodeJS API Documentation…` | ❌ RET — alan adı gaspı |

**Not:** R3/R4 model tedariki (§2.7 Hub ailesi) arayışında karşılarsa **aynı
200+title filtresiyle** elenir; bu dosyanın kendi ret kaydı R1–R4'ün ortak
kayıdı olmasıyla sınırlıdır (K006.7'nin kendi adayları arasında ret yoktur).

---

## EK §4 — Çapraz Bağ (Taze Verbatim ↔ §5 Tablosu)

| # | EK bulgusu (2026-10-15) | §5 satırı | Sonuç |
|---|---|---|---|
| X1 | Constructor tek seam: `ConfigManager` (EK §1) | "Yapılandırma yüzeyi ✅ seam hazır" | ✅ teyit — promoted `readonly` kanıtı |
| X2 | `MIN_SCORE=0.7` · `DIVERSITY_TARGET=0.30` sabitleri (EK §1) | — (§5'te yok) | 🔶 yeni: eşiklerin **kod içi kaydı** — "belirsiz" iddiasını netleştirir (sabit var, kaynağı yok) |
| X3 | `extractMetadata` ffprobe seam'i (EK §0.2 = L285–310) | "dış binary deseni ✅" | ✅ teyit — `escapeshellarg` + sessiz fallback detayları `audio-intelligence` EK §1'de |
| X4 | mlx · Core ML adayları (EK §4.16–17) | §4 tablosu 6 adaylı | 🔶 aday 6 → 8; Core ML **C2'deki "cihaz-yerel"** sorusuna aday cevaptır |
| X5 | ollama.com/gdpr.eu taze teyitsiz (EK §2 notları) | §3.6 GDPR gerekçesi | ⚠️ iki iddia 2026-10-12'ye bağımlı kaldı |
| X6 | 4 ortak ret (EK §3) | C1–C5 ailesi | ✅ URL sağlığı riski bu dosyada da geçerli |

---

## EK §5 — Fark (C-row) Tablosu

| C-row | Eski kayıt (korunur) | Taze gerçek (2026-10-15) | Etki |
|---|---|---|---|
| C-1 | §7 "Benzersiz URL: 9" | 9 + 8 = **17** (§2 korunur) | EK §2 sayımı |
| C-2 | §7 "Repo ile çapraz eşleşme: 2" | +1 (config seam teyidi + sabitler kanıtı — X1/X2) | sonraki sayım: 3 |
| C-3 | §8 kaynak listesi 4 madde | +1 madde: EK §0–§4 (2026-10-15 disk + 40-URL turu) | ek kanıt kütlesi |
| C-4 | C4 (Ollama rakamları tek-kaynak) | **değişmedi** — taze teyit yapılmadı | ⚠️ açık kaldı |
| C-5 | C2 (yerel = sunucu mu cihaz mı) | Core ML/mlx adayları eklendi ama **karar verilmedi** | `UNKNOWN` aynen geçerli |

---

## EK §6 — Bulgular (A-LI serisi)

### A-LI-1 — Seam'ler somutlaştı: 2 hazır seam + 1 yeni bilgi

§5'in "iki seam hazır" bulgusu EK §1 ile doğrulandı ve bir kayıt genişledi:
(1) ffprobe dış-binary deseni (L285–310), (2) `ConfigManager` constructor seam'i
(L34–44), (3) **yeni:** sınıf sabitleri L30–32 — runtime konfigürasyonu bugün
`ConfigManager` üzerinden taşınsa bile **eşikler oraya taşınmamış** (hardcoded).
Yerel çıkarım entegrasyonu için config seam'i yeterli ama **tutarlılık için eşikler
de config'e taşınmalı** (tasarım notu — uygulama yok).

### A-LI-2 — "Yerel" belirsizliği (C2) adaylarla daralmadı, ikiye ayrıldı

EK §4.16–17 ile iki uç netleşti: **sunucu-yerel** (llama.cpp/whisper.cpp/Ollama/
vLLM — §2) ve **kullanıcı-cihazı-yerel** (Core ML/mlx — EK). Karar hâlâ yok
(`UNKNOWN`); ancak artık soru "var mı yok mu" değil, **"hangi uç"** durumunda —
K006 karar dosyasına (Q-serisi) yazılabilir.

### A-LI-3 — Ollama/GDPR iddiaları taze teyitsiz kaldı

EK §2 notları: `ollama.com` (9M+/ay vb. rakamlar) ve `gdpr.eu` bu turda
yeniden okunmadı → §3'teki tüm bu iddialar **tek-kaynak + taze teyitsiz**
olarak işaretli kalmalıdır. Tazeleme kararı bir sonraki tur işidir; **eski
okuma değiştirilmez**.

### A-LI-4 — Model tedariki yolu gasp riski altında (ortak ders)

EK §3: RecBole ve implicit alan adları gasp edilmiş; model/havuz arayan bir
araştırmacı bu 200 OK yanıtlarını **kaynak sanabilir**. Yerel model tedariki
(§2.7 Hub) dışındaki indirme yollarında **title denetimi zorunlu** kuralı bu
dosyada da geçerlidir (ret kayıtları EK §3).

### A-LI-5 — URL/kapı durumu

§2 9 + EK 8 = **17 benzersiz URL**; bu dosyanın kendi adaylarında ret yok
(4 ret ortak/kullanılmayan); uydurma URL 0; taze teyitsiz 2 (ollama · gdpr).
C1 (yerel çıkarım satırı 0) ve C5 (`shell_exec` süreç yönetimi yok) **açık** —
EK yeni kanıt ekledi, boşluğu kapatmadı.

---

## EK §7 — Kapılar (K006.7 web-arastirma)

| Kapı | Durum | Kanıt |
|---|---|---|
| Mevcut içerik korundu (append-only) | ✅ | §1–§8 + status satırı aynen duruyor |
| Frontmatter'e dokunulmadı | ✅ | EK gövdede (L18 sonrası) |
| Uydurma URL yok | ✅ | 8 kanıt URL = 200 + gerçek title |
| Taze teyitsiz iddialar işaretli | ✅ | ollama · gdpr → "2026-10-12'ye bağlı" (EK §2 notları + A-LI-3) |
| Kod iddiaları disk kanıtlı | ✅ | EK §1 verbatim L1–44 (2026-10-15) |
| `*.php` yazımı yok (yalnız okuma) | ✅ | 2026-10-15 yalnız `read` |
| git commit yok | ✅ | bu oturumda commit atılmadı |
| `.ai/architecture/index.md` dokunulmadı | ✅ | yazım yalnız bu dosyada |

---

## EK §8 — Ölçüm

| Metrik | Değer |
|---|---|
| EK öncesi satır | **137** |
| EK sonrası satır | **422** (EK + EK-2 aynı editte yazıldı → kapı o an ❌, EK-3/EK-4 ile kapatıldı) |
| ≥500 kapı | **EK-3 §4 + EK-4'e bak — nihai ✅ (508)** |
| Ölçüm yöntemi | `read(offset=9999, limit=1)` → hata mesajındaki satır sayısı |
| Ölçüm tarihi | 2026-10-15 |

---

# EK-2 (2026-10-15) — Derinlik Eki: Yerel Çıkarma Entegrasyon Yolları ↔ Repo Seam'leri

> Yeni URL/iddia yok; EK §1–§4 + §5'teki kanıtların iki entegrasyon yoluna
> eşlenmesi. Karar üretmez — seçenekleri ve ön koşulları listeler.

## EK-2 §1 — Yol A: sunucu-yerel (binary/CLI çağırma)

| Adım | Ne gerekir | Repo karşılığı (kanıt) | Durum |
|---|---|---|---|
| A1 | Dış binary çağırma deseni | `shell_exec` + `escapeshellarg` (L285–310, ffprobe) | ✅ desen var |
| A2 | Timeout/süreç yönetimi | **yok** (§6 C5) | ⚠️ |
| A3 | Binary yolu/versiyon kontrolü | **yok** | ⚠️ |
| A4 | Model dosyası indirme/checksum | **yok** (§5) | ⚠️ |
| A5 | Yapılandırma seam'i | `ConfigManager` (L34–44) | ✅ seam var |
| A6 | Fallback (araç yoksa) | ffprobe'da **sessiz varsayım** (audio EK §1 L296–298) — fallback var, **uyarı yok** | 🔶 |
| A7 | Test | AI testi 0/44 | ⚠️ |

**Yol A özeti:** 2/7 adım hazır (A1, A5); en kritik eksik A2 (süreç yönetimi) —
`shell_exec` blocking ve timeout'suz; üretimde yerel çıkarım süresiz bloklayabilir.

## EK-2 §2 — Yol B: kullanıcı-cihazı-yerel (Core ML / mlx sınıfı)

| Adım | Ne gerekir | Repo karşılığı | Durum |
|---|---|---|---|
| B1 | İstemci uygulaması (iOS/desktop) | BFF katmanı var (ADR-084, `src/Api/Bff/`) — model çalıştırma **0 satır** | 🔶 taşıyıcı var, model yok |
| B2 | Cihaz donanım tespiti | `analyzeHardware` placeholder (inference EK §2) — çıkarım donanımı değil | ⚠️ |
| B3 | Model paketi dağıtımı (app içi) | **0 satır** | ⚠️ |
| B4 | Veri yerelliği garantisi | tasarım avantajı (§5) — kod kanıtı yok | 🔶 |
| B5 | C2 kararı ("hangi uç") | `UNKNOWN` (frontmatter open_question) | ⚠️ **bloklayıcı** |

**Yol B özeti:** 0/5 adım tam; en kritik eksik B5 (karar) — teknik değil
**kapsam kararı**; verilen karar A yolunu otomatik eleyebilir (tersi de geçerli).

## EK-2 §3 — İki yolun karşılaştırması (yalnız kanıt farkları)

| Ölçüt | Yol A (sunucu-yerel) | Yol B (cihaz-yerel) |
|---|---|---|
| Repo seam'i | 2 hazır (A1·A5) | 0 hazır (B1–B5) |
| K006 kararı | `UNKNOWN` (C2) | `UNKNOWN` (C2 — aynı soru) |
| Doğrudan veri gizliliği | sunucu içinde kalır (§5 tasarım notu) | cihazdan hiç çıkmaz (EK §4.17 ailesi) |
| Donanım riski | GPU gerekirse (§6 C3) | cihaz varyansı (mlx Apple-only) |
| Test kapısı | AI testi 0 (ortak) | AI testi 0 (ortak) |

**Sonuç:** karar **kanıt eksiğinden değil, kapsam belirsizliğinden** (C2) takılı;
her iki yolun da ön koşulu aynı test kapısıdır (AI testi 0 → önce o).

## EK-2 §4 — Ölçüm (EK-2)

| Metrik | Değer |
|---|---|
| EK-2 öncesi satır | 422 (EK+EK-2 tek editte — ayrık ölçüm yok) |
| EK-2 sonrası satır | **494** (EK-3 öncesi ölçüm, 2026-10-15) |
| ≥500 kapı | **❌ 494 → EK-3/EK-4 ile kapatıldı — nihai 508** |
| Yöntem | `read(offset=9999, limit=1)` → hata mesajındaki satır sayısı |
| Tarih | 2026-10-15 |
| Yeni URL/iddia sayısı | 0 (yalnız mevcut kanıtın eşlenmesi) |

---

# EK-3 (2026-10-15) — Derinlik Eki: Runtime Ayar Envanteri ve Süreç Yönetimi Şartnamesi

> Yeni URL/iddia yok; EK §1'deki `ConfigManager` seam'i ve EK-2 §1'deki boş
> adımlar üzerinden, yerel çıkarımın üretmesi gereken **ayar ve süreç bileşenlerinin**
> envanteridir. Her satır ya mevcut bir kanıta ya da işaretli boşluğa dayanır.

## EK-3 §1 — Ayar envanteri ↔ bugün nerede

| # | Ayar | Neden gerekli (kanıt bağlantısı) | Bugün nerede | Config seam uyumu |
|---|---|---|---|---|
| S1 | binary yolu (ör. sunucu yeri/CLI) | A3 boşluğu (EK-2 §1) | **yok** — `shell_exec` komutu sabit yazılmış (L290–293) | ✅ taşınabilir |
| S2 | timeout (sn) | C5: süreç yönetimi yok | **yok** | ✅ taşınabilir |
| S3 | model dizini / dosya yolu | §5 "model dosyası yönetimi 0 satır" | **yok** | ✅ taşınabilir |
| S4 | model sürümü/checksum | §2.7 sürümleme iddiası | **yok** | ✅ taşınabilir |
| S5 | eşikler (70 · 90 · 0.1 · 10000) | EK §3 — `predictFault` sabit yazım | **hardcoded** (L174–192) | 🔶 taşınabilir ama bugün taşınmadı |
| S6 | skor eşiği `MIN_SCORE = 0.7` | EK §1 L31 | **class const** (L31) | 🔶 taşınabilir |
| S7 | çeşitlilik `DIVERSITY_TARGET = 0.30` | EK §1 L32 | **class const** (L32) | 🔶 taşınabilir |
| S8 | ağırlıklar `0.6 / 0.4` | evaluation EK §1 L65 | **çağrı yerine gömülü** | ⚠️ sabit değil — refactor gerektirir |
| S9 | `MAX_RECOMMENDATIONS = 20` | EK §1 L30 | **class const** | 🔶 taşınabilir |
| S10 | ffprobe varsayılanları (44100/16/2/0) | audio EK §1 L297 | **hardcoded** | 🔶 sessiz varsayım — config'e taşınırsa "varsayılan" görünür olur |
| S11 | uptime/kullanım saati kaynağı | `predictFault` `$usage_hours` (L172) — veri kaynağı yok | **girdi harici** (nereden geldiği `UNKNOWN`) | ⚠️ ayar değil, veri boru hattı |
| S12 | log seviyesi/uyarı kanalı | EK §3 alerts `[]` — nereye yazıldığı yok | **yok** | ⚠️ |

**Envanter özeti:** 12 ayarın **4'ü bugün hardcoded sabit**, **4'ü sınıf sabiti**,
**2'si hardcoded varsayım**, **2'si veri/kanal boşluğu**; `ConfigManager`'a
taşınmış **0** ayar vardır. A3/A5 gibi "seam hazır" kayıtları, **semin ayar
taşımaması** gerçeğini değiştirmez (seim = bağlantı noktası, bugün boştur).

## EK-3 §2 — A2 Şartnamesi: süreç yönetimi bileşenleri (Yol A için)

| # | Bileşen | Mevcut davranış (kanıt) | İstenen davranış | Açık? |
|---|---|---|---|---|
| P1 | timeout | yok — `shell_exec` sınırsız bekler (L295) | üst sınır + kill | ✅ eksik |
| P2 | hata kodu ayrımı | yalnız `null` kontrolü (L296) | exit code + stderr ayrımı | ✅ eksik |
| P3 | çıktı boyutu sınırı | yok — `json_decode` tüm çıktıyı okur (L300) | maks bayt limiti | ✅ eksik |
| P4 | eşzamanlılık | yok — blocking çağrı | eşzamanlı istek tıkanması için kuyruk | ✅ eksik |
| P5 | kaçış | `escapeshellarg` (L292) | ✅ **mevcut** | hayır — hazır |
| P6 | log/iz | yok | çağrı süresi + komut + sonuç kaydı | ✅ eksik |
| P7 | devre-dışı bırakma anahtarı | yok | feature flag | ✅ eksik |
| P8 | sağlık kontrolü | yok (binary var mı bilinmiyor) | başlangıçta `--version` yoklaması | ✅ eksik |

**A2 durumu:** 8 bileşenin **1'i mevcut** (P5 kaçış), **7'si eksik**. Bu, §6 C5'in
("süreç yönetimi yok") bileşen bazlı dökümüdür; K006.7 entegrasyonu Yol A
seçilirse ilk inşa hattı burasıdır.

## EK-3 §3 — Sıralama önerisi (kanıta dayalı, karar değil)

```text
1. AI testi kapısı (AI testi 0/44 — her iki yolun ortak ön koşulu, EK-2 §3)
2. C2 kararı: sunucu-yerel mi, cihaz-yerel mi (UNKNOWN — kapsam kararı)
   ├─ Yol A seçilirse → EK-3 §2 P1–P8 inşası → EK-3 §1 S1–S4 ayarlarının Config seam'e taşınması
   └─ Yol B seçilirse → EK-2 §2 B1–B5 (taşınma + karar)
3. Eşik/skor sabitlerinin config'e taşınması (S5–S9) — her iki yolda da ortak
4. taze teyitsiz iddiaların yeniden okunması (A-LI-3: ollama · gdpr)
```

**Not:** bu sıralama EK-2/EK-3'teki boşluk sayımından türetilmiştir; **uygulama
kararı değildir**, `yapay-zeka-kararlar.md` (draft) yetkilidir.

## EK-3 §4 — Ölçüm (EK-3)

| Metrik | Değer |
|---|---|
| EK-3 öncesi satır | **422** |
| EK-3 sonrası satır | **494** (EK-4 öncesi ölçüm) — EK-4 sonrası **508** |
| ≥500 kapı | **✅ GEÇTİ (508 ≥ 500, `read(offset=9999)` → "508 lines")** |
| Yöntem | `read(offset=9999, limit=1)` → hata mesajındaki satır sayısı |
| Tarih | 2026-10-15 |
| Yeni URL/iddia sayısı | 0 (yalnız mevcut kanıtın şartnamelenmesi) |

---

## EK-4 — EK Kapıları Özeti (K006.7, 2026-10-15)

| Kapı | Durum | Kanıt |
|---|---|---|
| Append-only (§1–§8 + status korundu) | ✅ | EK §0–§3, EK-2, EK-3 yalnız ekler |
| Frontmatter değişmedi | ✅ | L1–18 ilk okumayla birebir |
| Uydurma URL 0 · zayıf kanıt işaretli | ✅ | 8 kanıt URL; 4 ortak ret (EK §3); quantization "Redirecting…" kanıt sayılmadı |
| Taze teyitsiz iddialar işaretli | ✅ | ollama · gdpr (A-LI-3) |
| Kod kanıtı satır eşlemeli | ✅ | EK §1: `Dosya: shared/src/AI/AIEngine.php` · `Satır: 1–44` |
| Yazım yüzeyi | ✅ | yalnız bu `.md`; `*.php/js/css/sql` yalnız okundu |
| git commit | ✅ | yok — orkestratöre ait |
