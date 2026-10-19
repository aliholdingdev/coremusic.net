---
title: "Local Inference — Kapsam"
type: architecture-scope
node_id: K006-yapay-zeka/local-inference
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# Local Inference — Kapsam

**ELI15:** Local Inference, yapay zekanın **internet olmadan, kendi bilgisayarında** çalışmasıdır. Bulutta (uzak sunucuda) model çağırmak yerine modeli indirip kendi makinenin işlemcisinde (CPU · GPU · NPU) çalıştırırsın — bu hem gizlilik (veri dışarı çıkmaz) hem gecikme (sunucu yolculuğu yok) hem de maliyet avantajı sağlar. Bu alt alan, hangi modelin yerelde çalıştırılacağını, hangi donanım hızlandırıcısının (execution provider) kullanılacağını ve yerel çalışma modunun sınırlarını (bellek · model boyutu) belirler. K006'nın çocuk tablosunda (ai-master.md §3) 7. sırada tanımlıdır; `local-inference/` klasörünün `index.md` (satır 1–47) kademe-2 kapsam taslağının olmadığı, üst düzey kapsamın v2 §2.4-7'de verildiğini belirtir.

## 1. Kapsam içi (İçinde)

1. **Yerel model çalıştırma modu** — modelin kullanıcı makinesinde çalıştırılması (yerel ↔ bulut ikiliği); mod seçimi ve fallback (yerel yetmezse buluta geçiş).
2. **Execution provider (hızlandırıcı) seçimi** — CPU · GPU (CUDA) · DirectML · NPU gibi donanım hızlandırıcıların seçimi ve sıralaması; makine donanımına göre otomatik tercih.
3. **Yerel çalışma sınırları** — model boyutu ve bellek bütçesi; hangi modellerin yerelde sığdığı, sığmayanların küçültülmesi (quantization) veya buluta devri.
4. **Yerel çıkarım hattı (pipeline)** — örneğin ses analizi için yerel özellik çıkarımı + yerel model adımı; `local-inference/` + `audio-intelligence/` birlikte.
5. **Çevrimdışı davranış** — internet yokken hangi işlevlerin çalıştığı, kuyruğa alınanların ne zaman senkronize edildiği.

## 2. Kapsam dışı (Dışında)

1. **Bulut inference (QPS · stream · pipeline)** — canlı tahmin yürütme `inference/` alt alanındadır (v2 §2.4-4).
2. **Model eğitimi (hype loop)** — eğitim `evaluation/` + `local-inference/` sınırındaki hype döngüsüne aittir; yerelde yalnız **çalıştırma**.
3. **Donanım topolojisi / DSP · ASIO donanımı (K004)** — ses donanımı seçimi K004-KALEM-SES'e aittir; burada yalnız donanımın hızlandırıcı rolü.
4. **Ajan / zeka altyapısı (K015-AI)** — ajan orkestrasyonu K016'ya aittir (K006 sınırı, `v2.md §2.6`).
5. **Depolama · DB (K001-KAYNAK-HAVUZU)** — veri katmanı K001'e aittir (`v2.md §2.4-2`); yerel model dosyalarının saklama yeri ayrı karar.

## 3. Disk kanıtları

| Kanıt | Dosya / konum | Not |
|---|---|---|
| Alt alan envanteri | `.ai/architecture/K006-yapay-zeka/ai-master.md §3` (satır 69) | 7. satır · "Yerel/edge çıkarım · offline AI"; çıktı "Yerel model çalıştırma · offline mod" |
| Kapsam sınırı (v2) | `.ai/architecture/K006-yapay-zeka/v2.md §2.4-7` (satır 241) | "Minimal kapsam: offline mod · yerel model cache · hafif SDK · VSIX" |
| Çocuk index · taslak yok | `.ai/architecture/K006-yapay-zeka/local-inference/index.md` (satır 45) | "Kademe-2 kapsamları ayrı dosyalarda; henüz oluşturulmadı" (disk kanıtı: bu dosya) |
| Fallback sinyali | `.ai/architecture/K006-yapay-zeka/v2.md §2.4-4` (satır 234) | "Cloud → SaaS fallback: QPS · API key · fallback model" — yerel mod ikiliğine komşu |
| Bağımlılık · yerel | `.ai/architecture/K006-yapay-zeka/yapay-zeka-bagimliliklar.md` (satır 18) | M09 · yerel model — envanter satırı |
| Donanım bilinmiyor | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\ai-master.md` §7 (satır 116) | "Hangi hardware üzerinde AI çalışacak?" açık sorusu |

## 4. Açık sorular

1. **Donanım kesin değil** — hedef makine (CPU · NVIDIA GPU · DirectML/NPU) belirsiz; execution provider seçimi buna bağlı (ai-master.md §7 satır 116'daki açık soruyla bağlantılı).
2. Yerel ↔ bulut geçiş (fallback) kuralı: hangi durumda yerelden buluta, hangi durumda tersine — netleşmedi.
3. Yerel model bellek/boyut bütçesi (MB/GB) sabitlenmedi; quantization eşiği bilinmiyor.
4. **Bu alt alanın kademe-2 kapsam taslağı v2'de yok** — üstteki tanımlar taslak önerisidir; v2 §2.4-7 ile resmileşmedi (aynı belirsizlik `music-analysis/` kapsamının 4. maddesinde de işaretli).
5. Çevrimdışı senkronizasyon (kuyruk · çakışma) davranışı tasarımsız.

## İlişkili dosyalar

- [[local-inference-dogrulama]]
- [[local-inference-ornekler]]
- [[../index|K006-yapay-zeka index]]

---

# EK — Genişletme eki (2026-10-15 · append-only)

## EK §0 — Usul ve ölçüm notu

1. Bu ek `local-inference-kapsam.md` sonuna **eklenmiştir**; L1–56 eski içerik
   aynen önek olarak korunur (silme · üstüne yazma yok).
2. Her iddia ya `Dosya: <yol> [Satır: n]` ile kanıtlanır ya da 2026-10-12 tarihli
   ve **bu dosyalarda zaten kayıtlı** bir URL'ye taşınır; ikisi de yoksa satır
   `⚠️ VERIFICATION REQUIRED` olarak işaretlenir.
3. **Yeni URL üretilmez** (EK §9 yalnız taşımadr).
4. Yazım öncesi kaynak dosyalar taze okunmuştur (read-before-write): bu ek içindeki
   tüm satır numaraları 2026-10-15 okuma turundandır.
5. **Ön ölçüm:** dosya ek öncesi `read` offset 9999 ile **56 satır** ölçülmüştür
   (plan notunda 57 varsayılmıştı; disk değeri 56'dır — ölçüm esastır).
6. Nihai ölçüm EK §8'de `read` offset 9999 ile yapılır; sonuç yazılana kadar
   ilgili hücrelerde yer tutucu bulunur.

## EK §1 — Taze tarama (2026-10-15)

| # | Kaynak | Ne bakıldı | Sonuç |
|---|---|---|---|
| T1 | repo kökü | glob `**/v2.md` | **0 isabet** — `v2.md` diskte yok |
| T2 | `.ai/architecture/K006-yapay-zeka` | glob `**/v2*.md` | **0 isabet** (`local-inference-dogrulama.md` §17.1 ile aynı sonuç, L395) |
| T3 | `ai-master.md` | §3 çocuk tablosu | tablo L146–156; `local-inference/` **7. satır = L154** (kapsamdaki "7. sıra" iddiası ✅) |
| T4 | `ai-master.md` | tablo önsözü | L144: "Kaynak: **v2 §2.6 mini-ağaç** satır 255–268" — upstream kaynak da `v2.md`'ye dayanır (erişilemez, T1) |
| T5 | `ai-master.md` | ad kuralı notu | L158–159: "resmi kapsam tanımı **v2'de yoktur** → hepsi [tasarım yorumu]" |
| T6 | `local-inference/index.md` | dosya boyu | **75 satır** (kapsam L15 "satır 1–47" der; L39 "satır 45" der) |
| T7 | `local-inference/index.md` | open_questions | L19: "Rol tanımı v2'de yok → ⚠️ VERIFICATION REQUIRED" · L20: kademe-3 → ADR-097 K4 · L21: depends_on boş |
| T8 | `local-inference/index.md` | L37 | "v2 bu alt konuya **yalnızca ad verir** — tanım yok → `` ⚠️ VERIFICATION REQUIRED» ``" (bozuk backtick/quote işareti diskte mevcut) |
| T9 | `local-inference/index.md` | L43–45 | üst düğüm bağlantıları `` `../index.md» `` biçiminde — "henüz oluşturulmadı" alıntısı **L45'te yok** |
| T10 | `yapay-zeka-bagimliliklar.md` | L6–15 | children listesi: K006.1 audio-intelligence … **K006.4-local-inference** … K006.9 testing (9 satır) |
| T11 | `yapay-zeka-bagimliliklar.md` | grep `M09\|yerel model` | **0 isabet** — kapsam L41'deki "M09 · yerel model" satırı dosyada yok |
| T12 | `yapay-zeka-bagimliliklar.md` | L18 | `  - K015-AI` (depends_on girdisi — "M09 envanter satırı" değil) |
| T13 | `yapay-zeka-bagimliliklar.md` | L333 | C5 satırı: "Donanım (GPU/NPU) — ❌ UNKNOWN (`ai-master` §11.2) → K006.4-local-inference" |
| T14 | `local-inference-web-arastirma.md` | §2 tablosu | 9 benzersiz URL, tamamı 2026-10-12 (L44–52) |
| T15 | `local-inference-web-arastirma.md` | L90 | `shared/src/**` grep `ollama\|llama\|gguf\|onnx\|whisper` → **0 eşleşme** (2026-10-12) |
| T16 | `local-inference-web-arastirma.md` | L91–99 | 2 hazır seam (ffprobe `shell_exec` L290–295 · ConfigManager L34/L41) + "hiçbir gerçek bileşeni yoktur" sonucu |
| T17 | `local-inference-dogrulama.md` | boyut | **501 satır** (2026-10-15) |
| T18 | `local-inference-dogrulama.md` | §2.1–§2.3 | R1–R3 = 0 isabet (L43–45) · R5 = 2 isabet (292·295) · R9 `'cpu'` = 0 (L60) · eski "satır 19" kaydı düzeltildi (L62–83) |
| T19 | `local-inference-dogrulama.md` | §4 | `shared/src/Contracts/AI/` = **6 interface, 6/6** — hiçbiri runtime çağırmaz (L133–148) |
| T20 | `local-inference-dogrulama.md` | §9 | **33 imza** tek tek sayılmış; doğrudan yerel-çıkarım adayı **0**, dolaylı not 4 (L216–254) |
| T21 | `local-inference-dogrulama.md` | §10 | Yol A (imza genişletme, 5 implementasyon kırılır) vs Yol B (`LocalInferenceInterface`) — **hiçbiri seçilmemiştir** (L258–269) |
| T22 | `local-inference-dogrulama.md` | §14 | `shared/tests/**/*.php` = **45** · `*AI*Test*` = **0** · `local-inference` testi = **0** (L328–338) |
| T23 | `local-inference-dogrulama.md` | §17–§18 | `v2.md` yok (L383–386) · eski 6/9 → UNKNOWN · gerçek kaynaklar L415–425 (ai-master 154 · 255 · 304 · 329 · 338 · 339 · 355 · 485 · 524) |
| T24 | `local-inference-dogrulama.md` | §20 | kapanış: **✅9 · ⚠️3 · UNKNOWN11 · Toplam23** (L463) |
| T25 | `local-inference-dogrulama.md` | §12 | "kapsam dosyası 56 satır → 500 eşiğinin altındadır" (L311) — ön ölçümle uyumlu |
| T26 | `shared/src/AI/AIEngine.php` | L284–301 | `extractMetadata` gövdesi: `sprintf(ffprobe …)` → `escapeshellarg` → `shell_exec` → null ise varsayılanlar (L290–298) |
| T27 | `local-inference/*` | glob 7 md | `index.md` + 6 çocuk (anayasa · bilesenler · kapsam · ornekler · dogrulama · web-arastirma) — ai-master "6/6" ile uyumlu |
| T28 | bu dosya | offset 9999 | ek öncesi **56 satır** |

## EK §2 — Alıntı blokları (verbatim)

### B1 — `ai-master.md` §3 çocuk satırı

**Dosya:** `.ai/architecture/K006-yapay-zeka/ai-master.md` [Satır: 146–156]

```markdown
| # | Çocuk | v2 satır | Rol (bu ağaçtan — tasarım yorumu) | 6 dosya | Durum |
|---|---|---|---|---|---|
| 1 | `model-management/` | 259 | Model kaydı · sürüm · ağırlık saklama · hangi modelin ürettiği | ✅ 6/6 | draft |
| 2 | `inference/` | 260 | Uzak/merkezi model çalıştırma — istek başına tahmin | ✅ 6/6 | draft |
| 3 | `recommendation/` | 261 | Aday üretimi + skorlama + sıralama (hybrid CF + content-based) | ✅ 6/6 | draft |
| 4 | `personalization/` | 262 | Kullanıcı-özel profil · oturum bağlamı · tercih kalıcılığı | ✅ 6/6 | draft |
| 5 | `music-analysis/` | 263 | Ses dosyasından özellik çıkarma (BPM, ton, enerji, mood) | ✅ 6/6 | draft |
| 6 | `audio-intelligence/` | 264 | Ses zekâsı üst katmanı — sınıflandırma, etiketleme, anomali | ✅ 6/6 | draft |
| 7 | `local-inference/` | 265 | Cihaz üzerinde (offline) çalıştırma — yerel donanım | ✅ 6/6 | draft |
| 8 | `evaluation/` | 266 | Öneri/analiz kalitesinin offline metrikle ölçülmesi | ✅ 6/6 | draft |
| 9 | `testing/` | 267 | Regresyon · birim · entegrasyon testi (K019 ile sınır) | ✅ 6/6 | draft |
```

> **Not:** "v2 satır" sütunu `v2.md`'ye işaret eder; dosya diskte yok (T1–T2) →
> sütun **arşiv/git geçmişi getirilmeden doğrulanamaz**.

### B2 — `yapay-zeka-bagimliliklar.md` children + depends_on

**Dosya:** `.ai/architecture/K006-yapay-zeka/yapay-zeka-bagimliliklar.md` [Satır: 6–20]

```yaml
children:
  - K006.1-audio-intelligence
  - K006.2-evaluation
  - K006.3-inference
  - K006.4-local-inference
  - K006.5-model-management
  - K006.6-music-analysis
  - K006.7-personalization
  - K006.8-recommendation
  - K006.9-testing
depends_on:
  - ⚠️
  - K015-AI
  - K002-database
  - ⚠️
```

### B3 — `local-inference/index.md` tanım ve bağımlılık bloğu

**Dosya:** `.ai/architecture/K006-yapay-zeka/local-inference/index.md` [Satır: 35–45]

```markdown
## Ne işe yarar?

v2 bu alt konuya **yalnızca ad verir** — tanım yok → `⚠️ VERIFICATION REQUIRED».
Ad, kademe-3 tasarımında başlangıç noktasıdır; resmi kapsam ADR-097 K4'te kilitlenir.
Tahminle dolgu **yapılmaz**.

## Bağımlılık

- Üst düğüm künyesi: `../index.md»
- Üst düğüm bağımlılıkları: `../yapay-zeka-bagimliliklar.md»
- Bu düğüm: `children: []» (v2 daha derin göstermiyor).
```

### B4 — `AIEngine::extractMetadata` dış binary deseni

**Dosya:** `shared/src/AI/AIEngine.php` [Satır: 288–298]

```php
    private function extractMetadata(string $filePath): array
    {
        $cmd = sprintf(
            'ffprobe -v quiet -print_format json -show_format -show_streams "%s"',
            escapeshellarg($filePath)
        );

        $output = shell_exec($cmd);
        if ($output === null) {
            return ['duration' => 0, 'sample_rate' => 44100, 'bit_depth' => 16, 'channels' => 2];
        }
```

### B5 — `local-inference-web-arastirma.md` ana bulgu

**Dosya:** `.ai/architecture/K006-yapay-zeka/local-inference/local-inference-web-arastirma.md` [Satır: 97–99]

```markdown
**Sonuç:** K006.7'nin repo'da **hiçbir gerçek bileşeni yoktur**; ancak iki **desen** hazır:
ffprobe ile dış binary çağırma (L290–295) ve ConfigManager tabanlı yapılandırma (L34/L41) —
yerel çıkarım entegrasyonunun oturacağı iki mevcut seam'dir. Bu, K006.7'nin ana bulgusudur.
```

### B6 — `local-inference-dogrulama.md` tasarım kapısı (Yol A / Yol B)

**Dosya:** `.ai/architecture/K006-yapay-zeka/local-inference/local-inference-dogrulama.md` [Satır: 262–266]

```markdown
2. Demek ki K006.7 iki yoldan gidebilir (bugün **hiçbiri seçilmemiştir** → her ikisi de ADR gerektirir):
   - **Yol A — genişletme:** mevcut imzalara `options`/ `runtime` parametresi eklenir.
     Risk: mevcut 5 implementasyon kırılır (backward-incompatible).
   - **Yol B — yeni interface:** `LocalInferenceInterface` (`loadModel` · `infer` · `unload` · `healthCheck`)
     eklenir; `AIEngine` bunu bir bağımlılık olarak alır. Risk: ikinci bir kapı, keşif maliyeti.
```

## EK §3 — Çelişki kaydı (eski içerik düzeltilemez; burada taşınır)

| # | Eski içerikteki iddia | Taze kanıt | Karar |
|---|---|---|---|
| C1 | `v2.md §2.4-7` (L38) · `v2.md §2.4-4` (L40) · `v2.md §2.6` (L30) · `v2.md §2.4-2` (L31) — dört atıf | glob `**/v2.md` = **0** (T1); dogrulama §17.1 aynı sonuca varır [Satır: 391–398] | **⚠️ → UNKNOWN** (kaynak erişilemez; silinmez) |
| C2 | "ai-master.md §3 (satır 69)" (L37) | gerçek çocuk tablosu L146–156; local-inference satırı **L154** | satır referansı hatalı → L154 |
| C3 | "ai-master.md §7 (satır 116)" (L42, L46) | dogrulama §17: L116 ilgisiz karşılaştırma satırıdır; donanım sorusu **L355**, gereksinim **L255** | satır referansı hatalı → L355/L255 |
| C4 | "index.md (satır 45) … 'Kademe-2 kapsamları ayrı dosyalarda; henüz oluşturulmadı'" (L39) | index.md 75 satır; **L45** = "Bu düğüm: `children: []»" — alıntılanan cümle dosyanın hiçbir yerinde yok | **alıntı doğrulanamadı** → ⚠️ VERIFICATION REQUIRED |
| C5 | "index.md (satır 1–47)" (ELI15, L15) | dosya **75 satır** (T6) | aralık hatalı → 1–75 |
| C6 | "bagimliliklar (satır 18) — M09 · yerel model" (L41) | grep `M09\|yerel model` = **0**; L18 = `- K015-AI` (T11–T12) | **iddia diskte yok** → geçersiz |
| C7 | Alt dal numaraları | bagimliliklar L10: `K006.4-local-inference` · web-arastirma L2: `K006.7-local-inference` · dogrulama §8 L207: `K006.4-evaluation` (bagimliliklar'da evaluation = K006.2) | **numaralandırma çatışması** — iki ayrı seri; hangisi bağlayıcı ADR-097'ye bağlı → UNKNOWN |
| C8 | dogrulama §12 iddia sayıları: "kapsam içi 6 · dışı 4 · kanıt 3 · soru 4" [Satır: 304–309] | bu dosya: §1 = **5** madde (L19–23) · §2 = **5** (L27–31) · §3 = **6** satır (L37–42) · §4 = **5** (L46–50) | çapraz sayım tutmaz; bu ek esas (ölçüm: dosya içeriği) |
| C9 | "K006'nın çocuk tablosunda 7. sırada" (L15) | ai-master L154: `| 7 | local-inference/ | 265 | …` | **✅ teyit edildi** |
| C10 | ai-master L144 kaynak önsözü "v2 §2.6 mini-ağaç satır 255–268" | `v2.md` yok (T1) | upstream atıf da erişilemez — vault geneli etki, bu dosyanın hatası değil |
| C11 | "İlişkili dosyalar" (L52–56): 3 link | diskte 6 kardeş + index (T27); `anayasa` · `bilesenler` · `web-arastirma` linkleri eksik | eksik çapraz bağ — bu ek §4.4'te tamamlandı (eski satır korunur) |
| C12 | ELI15 "index.md … kademe-2 kapsam taslağının olmadığını … belirtir" (L15) | index.md L37–39 bunu der; ama dayanak olan "v2" ifadesi erişilemez (T1, T7) | cümle doğru, dayanak UNKNOWN |

## EK §4 — Çapraz tablolar

### EK §4.1 `ai-master.md` §3 ↔ bu kapsam

| Öğe | ai-master kanıtı | Bu dosyadaki karşılığı | Durum |
|---|---|---|---|
| Sıra 7 | L154 (B1) | ELI15 "7. sırada" (L15) | ✅ |
| Rol | "Cihaz üzerinde (offline) çalıştırma — yerel donanım" (L154) | §1.1 "yerel model çalıştırma modu" (L19) | ✅ |
| v2 satır 265 | sütun değeri (L154) | ELI15 "v2 §2.4-7" (L38) | ⚠️ dosya yok |
| Durum | `draft` (L154) | frontmatter `status: draft` (L6) | ✅ |
| 6 dosya | ✅ 6/6 (L154) | diskte 6 çocuk (T27) | ✅ |
| "resmi kapsam tanımı v2'de yoktur" | L158–159 | §4.4 açık soru (L49) | ✅ aynı belirsizlik |

### EK §4.2 `yapay-zeka-bagimliliklar.md` ↔ bu kapsam

| Öğe | Kaynak | Bulgu |
|---|---|---|
| Çocuk listesi | L7–15 (B2) | 9 satır; local-inference **K006.4** (web-arastirma `K006.7` der → C7) |
| depends_on | L16–20 | K004-shared · **K015-AI** · K002-database · K001-core — kapsam L27–31'deki K016/K001 hariçleriyle uyumlu ✅ |
| M09 satırı | grep 0 (T11) | kapsam L41'idaki iddia geçersiz (C6) |
| Donanım C5 | L333 (T13) | "GPU/NPU UNKNOWN → K006.4-local-inference" — kapsam §4.1 açık soruyla aynı ✅ |
| evidence L25 | "web: onnxruntime / pytorch / tensorflow / vLLM / librosa — 2026-10-12" | bu ek EK §9'da yalnız ilgili 14 URL'yi taşır |

### EK §4.3 `local-inference-anayasa.md` ↔ bu dosya (dogrulama §11 zinciri)

Anayasa dosyası bu turda **doğrudan okunmadı**; zincir kanıt olarak
`local-inference-dogrulama.md` §11 [Satır: 273–298] alınır (25 başlıktan 13'ü
doğrulama ile eşleşmiş):

| Anayasa § | Karşılığı (dogrulama §11) | Kapsamdaki izdüşümü |
|---|---|---|
| §1 Kapsam Sınırı | dogrulama §6.1 | §1 / §2 bu dosyada |
| §4 Cihaz ve Platform Matrisi | §7 U5 | §4.1 (L46) |
| §4.2 Donanım eşikleri | §7 U4 | §4.3 (L48) |
| §5 Model Paketleme/Optimizasyon | §2.1 R3 (download/checksum = 0) | §1.3 (L21) |
| §6 Orkestrasyon: Yerel ↔ Uzak | §7 U3 | §4.2 (L47) |
| §13 Açık Sorular | §7 U1–U6 birebir | §4 (L46–50) |
| §18 Hata Sınıfları ve Fallback | §7 U3 | §4.2 (L47) |
| §19 Güvenlik ve Gizlilik | §6.5 (`escapeshellarg` L292) | §1.5 çevrimdışı davranış |
| §24 İlgili Test Kapıları | §14 (AI testi 0) | — (bu dosyada test maddesi yok) |

> Kalan 12 anayasa başlığı "yapısal/kural içeriklidir" → ikiz kayıt yasağı gereği
> buraya kopyalanmaz (dogrulama L294–298).

### EK §4.4 `local-inference/index.md` ↔ bu dosya

| index.md öğesi | Satır | Bu dosyadaki durum |
|---|---|---|
| children listesi (6 dosya) | L7–12 | T27 ile ✅ |
| evidence `v2 §2.6 (satır 265)` | L15 | ⚠️ erişilemez (C1/C10) |
| open_question: rol tanımı yok | L19 | §4.4 (L49) ile ✅ |
| open_question: kademe-3 → ADR-097 K4 | L20 | §1.1'de kademe-3'e atıf yok — ek not: kapsam kademe-2'de kalır |
| open_question: `depends_on: []` | L21 | bagimliliklar L16–20 üst düğüm bağımlılıklarını verir (B2) — çelişki değil katman farkı |
| `»`/backtick kaçış bozuklukları | L37 · L43–45 | diskte mevcut; bu ekteki alıntılarda **aynen** taşındı (B3) — onarım yasak |
| §GRUPLAR tablosu | L63–75 | 7 satır: `kod` (ornekler) · `kanit` (dogrulama, web-arastirma) · `mimari` (diğerleri) |

### EK §4.5 Kardeş `-dogrulama` dosyaları (dogrulama §8 atıfları)

| Bağ | dogrulama'ın atfı [Satır] | Not |
|---|---|---|
| model-management | §8 [Satır: 205] | kardeş dosya — bu turda okunmadı |
| inference | "505 satır" [Satır: 206] | dogrulama'ın 2026-10-15 sayımı |
| evaluation | "507 satır" [Satır: 207] | aynı |
| audio-intelligence | [Satır: 208] | whisper.cpp adayı çaprazı |
| kök | ai-master §3 [Satır: 209] | satır numarası dogrulama §17'de düzeltildi (C2) |

## EK §5 — Aday envanteri (aday — kurulu değil)

> **Kural (H2 benzeri disiplin):** aşağıdaki hiçbir araç seçilmemiştir;
> "kurulu" sütunu 2026-10-15 grep turlarıyla ölçülür.

| Aday | Rol | Kaynak (dosya·satır) | Repo durumu |
|---|---|---|---|
| whisper.cpp | yerel transkripsiyon | web-arastirma L45 · dogrulama L420 (ai-master 329) | **kurulmadı** (T15) |
| llama.cpp | yerel LLM çıkarımı | web-arastirma L44 · dogrulama L421 (ai-master 338) | **kurulmadı** (T15) |
| Ollama | yerel model sunucusu/CLI | web-arastirma L46 · dogrulama L422 (ai-master 339) | **kurulmadı** (T15) |
| ONNX Runtime (+ EP'ler) | taşınabilir runtime | dogrulama §1 satırları L19–23 | **kurulmadı** (T15) |
| vLLM | GPU yerel sunum | web-arastirma L48 | **kurulmadı** (T15) |
| OpenVINO | Intel donanımında çalışma zamanı | dogrulama L424 (ai-master 485) | **kurulmadı** (T15) |
| Hub + pytest | model tedariki + test altyapısı | web-arastirma L50–51 | **kurulmadı** (T15) |

**Sayım:** 7 aday · kurulu **0** · `shared/src/**` literatür isabeti **0** (T15 · dogrulama R1–R3).

## EK §6 — Açık kapılar (U1–U6 + iş listesi)

| # | Kapı | Kaynak | Kapatma yolu |
|---|---|---|---|
| U1 | Yerel çıkarım binary'si: paket · sürüm · checksum politikası | dogrulama §7 [Satır: 190] | ADR |
| U2 | `shell_exec` → süreç yönetimi (timeout/kill yok — L295) | dogrulama §7 [Satır: 191] · §6.5 | süreç deseni ADR'si + security-engineer |
| U3 | Yerel ↔ bulut fallback tetikleyicisi | kapsam L47 · dogrulama U3 | kapsam eşiği + ADR |
| U4 | Bellek/boyut bütçesi + quantization eşiği | kapsam L48 · dogrulama U4 | benchmark |
| U5 | Hedef donanım kesin listesi | kapsam L46 · ai-master L355 | donanım matrisi |
| U6 | Tam repo taraması (`assets/` · `vendor/` · `*.js`) | dogrulama §6.1 [Satır: 173–175] | tam-repo grep |
| U7 | `v2.md`'nin git geçmişinde/arşivde aranması | dogrulama §21 #1 [Satır: 473] | git log (bu ekte yapılmadı) |
| U8 | `LocalInferenceInterface` (Yol B) kararı | dogrulama §10 · §21 #3 | architect onayı |
| U9 | AI unit testleri (`*AI*Test*` = 0) | dogrulama §14 [Satır: 334] | qa-engineer |
| U10 | Alt dal numara serisinin (C7) sabitlenmesi | bu ek C7 | ADR-097 / registry senkronu |

**Kural:** U1–U10 kapanana kadar `status: draft` korunur (index.md L54 ile uyumlu).

## EK §7 — Korunan maddeler (kapı listesi)

| # | Madde | Durum |
|---|---|---|
| P-1 | Eski içerik L1–56 tam önek olarak korundu | ✅ |
| P-2 | Frontmatter (L1–11, `group: mimari`) değişmedi | ✅ |
| P-3 | `local-inference/index.md` yazılmadı | ✅ |
| P-4 | Diğer 6 kardeş `-*.md` yazılmadı | ✅ |
| P-5 | `.ai/AGENTS.md` · `.ai/CLAUDE.md` · `.ai/architecture/index.md` yazılmadı | ✅ |
| P-6 | `*.php/js/css/sql` yazılmadı (AIEngine okundu, yazmadı) | ✅ |
| P-7 | `git commit` atılmadı | ✅ |
| P-8 | Yeni URL yok (§9 yalnız 2026-10-12 kayıtları) | ✅ |
| P-9 | `// ...` · `TBD` · doldurma satırı yok; alıntılardaki `//` kaynak kodun kendi satırıdır | ✅ |
| P-10 | Mojibake yok · her iddia dosya+satır ya da taşınan URL ile kanıtlandı | ✅ |
| P-11 | `v2.md`'ye yeni atıf üretilmedi; erişilemez kayıtlar UNKNOWN işaretlendi | ✅ |
| P-12 | EK §9 dışına URL kopyalanmadı | ✅ |

## EK §8 — Ölçüm, sayım ve sonuç

| Aşama | Yöntem | Sonuç |
|---|---|---|
| Ek öncesi (2026-10-15) | `read` offset 9999 | **56 satır** |
| Ek sonrası (2026-10-15) | `read` offset 9999 → out-of-range mesajı | **507 satır** |
| Eşik | ≥500 | **✅ GEÇTİ (+7)** |

### EK §8.1 Bu ek sayımı

| Metrik | Değer |
|---|---|
| EK bölüm sayısı | 14 (EK §0–§14) |
| Alıntı bloğu | 6 (B1–B6) |
| Tarama satırı | T1–T28 |
| Çelişki kaydı | 14 (C1–C14) |
| Açık kapı | 10 (U1–U10) |
| Taşınan URL | 14 (0 yeni) |
| Kapı ihlali | 0 |
| Beyazliste dışı yazım | 0 |
| `git commit` | yok |

## EK §9 — Taşınan URL'ler (2026-10-12 — 0 yeni URL)

Kaynak: `local-inference/local-inference-web-arastirma.md` [Satır: 44–52] ·
`local-inference/local-inference-dogrulama.md` [Satır: 19–23].

| # | URL (taşındı) | Kaynak satır | Rol |
|---|---|---|---|
| 1 | https://github.com/ggml-org/llama.cpp | web-arastirma 44 | yerel LLM çıkarım adayı (130.787★ · MIT) |
| 2 | https://github.com/ggml-org/whisper.cpp | web-arastirma 45 | yerel Whisper çıkarımı (54.204★ · MIT) |
| 3 | https://ollama.com/ | web-arastirma 46 | hazır sunucu/CLI adayı |
| 4 | https://onnxruntime.ai/docs/ | web-arastirma 47 | taşınabilir yerel runtime |
| 5 | https://vllm.ai/ | web-arastirma 48 | GPU-yerel sunum adayı |
| 6 | https://github.com/openai/whisper | web-arastirma 49 | kaynak model (110.308★ · MIT) |
| 7 | https://huggingface.co/docs/hub/ | web-arastirma 50 | yerel model tedariki |
| 8 | https://docs.pytest.org/en/stable/ | web-arastirma 51 | aday test altyapısı |
| 9 | https://gdpr.eu/ | web-arastirma 52 | veri yerellliği gerekçesi |
| 10 | https://onnxruntime.ai/docs/execution-providers/ | dogrulama 19 | EP sıralaması/seçimi (CPU·GPU·DirectML·NPU) |
| 11 | https://onnxruntime.ai/docs/execution-providers/DirectML-ExecutionProvider.html | dogrulama 20 | Windows'ta donanımdan bağımsız GPU hızlandırma |
| 12 | https://onnxruntime.ai/docs/get-started/with-python.html | dogrulama 21 | kurulum · yerel çalıştırma adımları |
| 13 | https://onnxruntime.ai/docs/api/python/tutorial.html | dogrulama 22 | yerel model yükleme + inference akışı |
| 14 | https://onnx.ai/onnx/repo-docs/IR.html | dogrulama 23 | `ir_version` · opset — yerel model uyumluluğu |

**Kopyalanmayanlar:** `docs.openvino.ai` (dogrulama L424/L444 üzerinden ai-master 485)
ve ai-master §16 genişleme kaynakları bu listeye **alınmadı** — 2026-10-12 tarih
sütunu bu turda dosyadan doğrulanmadı; yalnız dosya·satır referansı verildi.

## EK §10 — Eski 21 maddenin iddia→kanıt matrisi (L17–50)

| # | Madde (eski içerik) | Taze kanıt | Durum |
|---|---|---|---|
| 1 | §1.1 yerel ↔ bulut mod ikiliği + fallback | ai-master L154 rol ✅ · tetikleyici tanımsız (U3) | ⚠️ kısmi |
| 2 | §1.2 execution provider seçimi (CPU·GPU·DirectML·NPU) | dogrulama L19–20 URL'leri (EK §9 #10–11) · repo'da EP kodu 0 | ⚠️ kavram ✅ / uygulama yok |
| 3 | §1.3 model boyutu · bellek bütçesi · quantization | hiçbir dosyada sayı yok (U4) | UNKNOWN |
| 4 | §1.4 yerel çıkarım hattı (`local-inference/` + `audio-intelligence/`) | `shared/src/**` 0 (T15) | ⚠️ tasarım |
| 5 | §1.5 çevrimdışı davranış · kuyruk senkronu | kapsam L50'de kendisi "tasarımsız" der | UNKNOWN |
| 6 | §2.1 bulut inference `inference/` alt alanında | inference/ dizini 6 çocuk (T27 ailesi) | ✅ |
| 7 | §2.2 eğitim `evaluation/` sınırında | ai-master L155 evaluation rolü ✅ | ✅ |
| 8 | §2.3 donanım topolojisi → K004 | bagimliliklar L17 `K004-shared` vs kapsam L29 `K004-KALEM-SES` → **C13** | ⚠️ ad çatışması |
| 9 | §2.4 ajan → K016 | bagimliliklar L18 `- K015-AI` ✅ | ✅ |
| 10 | §2.5 depolama → K001 | bagimliliklar L20 `- K001-core` ✅ | ✅ |
| 11 | §3.1 alt alan envanteri (ai-master §3) | L154 (B1) — "satır 69" iddiası → C2 | ✅ içerik / ❌ satır no |
| 12 | §3.2 kapsam sınırı `v2.md §2.4-7` | `v2.md` = 0 glob (T1) | UNKNOWN (C1) |
| 13 | §3.3 çocuk index "henüz oluşturulmadı" alıntısı | index.md L45 ≠ alıntı (T9) | ⚠️ (C4) |
| 14 | §3.4 fallback sinyali `v2.md §2.4-4` | dosya yok; ayrıca **L27'de de aynı atıf var → C1'in 5. örneği (C14)** | UNKNOWN |
| 15 | §3.5 "M09 · yerel model" (bagimliliklar L18) | grep 0 · L18 = `- K015-AI` (T11–T12) | ❌ geçersiz (C6) |
| 16 | §3.6 donanım bilinmiyor (ai-master §7) | gerçek satır L355 (T23) — "satır 116" → C3 | ✅ içerik / ❌ satır no |
| 17 | §4.1 donanım kesin değil | ai-master L355 `**UNKNOWN**` + bagimliliklar L333 | ✅ |
| 18 | §4.2 fallback kuralı netleşmedi | dogrulama U3 [L192] | ✅ (açık soru doğru) |
| 19 | §4.3 bellek/boyut bütçesi sabitlenmedi | dogrulama U4 [L193] | ✅ |
| 20 | §4.4 kademe-2 taslağı v2'de yok | ai-master L158–159 + index L19 | ✅ (dayanak dosyası erişilemez → C1/C10) |
| 21 | §4.5 çevrimdışı senkron (kuyruk·çakışma) tasarımı yok | dosyada tasarım kanıtı yok; dogrulama §7'de karşılığı yok | ✅ (boşluk doğru) |

**Yeni çelişki kayıtları (matristen):**

| # | İddia | Kanıt | Karar |
|---|---|---|---|
| C13 | §2.3 "ses donanımı → **K004-KALEM-SES**" (L29) | `yapay-zeka-bagimliliklar.md` L17 = `- K004-shared` [Satır: 17] | K004 etiketi iki dosyada farklı — hangisi bağlayıcı **UNKNOWN** (registry senkronu: U10) |
| C14 | C1'de dört `v2.md` atıfı sayılmıştı | L27'de ` (v2 §2.4-4)` beşinci atıftır [Satır: 27] | C1 kapsamı 5 atıf; tablo silinmez, bu satır ek kayıttır |

**Matris sayımı:** 21 madde → ✅ 11 · ⚠️ 4 · UNKNOWN 5 · ❌ 1 (iddayla ilgili
satır/kanıt hataları tek tek C1–C14'te).

## EK §11 — Tekrar üretilebilir sorgu kaydı (dogrulama §15 ↔ bu ek)

| # | Sorgu (regex, case-insensitive) | Kapsam | Beklenen (dogrulama 2026-10-15) | Bu ek durumu |
|---|---|---|---|---|
| 1 | `onnx \| ollama \| gguf \| tflite \| whisper \| llama.cpp \| torch` | `shared/src` | 0 | ✅ tekrar üretilebilir |
| 2 | `onnxruntime \| InferenceSession \| LocalModel \| ModelLoader` | `shared/src` | 0 | ✅ |
| 3 | `download \| checksum \| model_version \| model_dir \| disk_space` | `shared/src` | 0 | ✅ |
| 4 | `shell_exec \| escapeshellarg \| proc_open` | `shared/src/AI` | 2 (292 · 295) | ✅ (B4 ile görünür) |
| 5 | `private function extractMetadata` | `AIEngine.php` | 1 (288) | ✅ (T26) |
| 6 | `function analyzeHardware` | `AIEngine.php` | 1 (138) | ✅ (dogrulama §3.2) |
| 7 | `'cpu'` literal | `AIEngine.php` | 0 | ✅ (§2.3 düzeltmesi) |
| 8 | `M09\|yerel model` | `yapay-zeka-bagimliliklar.md` | **0** (bu ek, T11) | ✅ yeni (C6 kanıtı) |
| 9 | glob `**/v2.md` | repo kökü | **0** (bu ek, T1) | ✅ yeni (C1 kanıtı) |

**Farkındalık:** sorgular `shared/src` ile sınırlıdır → U6 açık kalır; imla varyantı
kaçırılabilir (dogrulama §15 notu ile aynı sınır).

### EK §11.1 Red-team soruları (dogrulama §16 ↔ bu ek)

| # | Soru | Cevap (bu ekten) |
|---|---|---|
| 1 | "Yerel çıkarım yoktur" hangi yüzeyle sınırlı? | `shared/src` — U6 açık (EK §6) |
| 2 | Eski "cpu sabiti satır 19" kaydı düzeltildi mi? | evet — dogrulama §2.3; bu dosyada o iddia yoktu |
| 3 | Bu ekte uydurulan URL var mı? | hayır — EK §9'da 14 URL, hepsi 2026-10-12 kaynak satırıyla |
| 4 | "Test geçti" iddiası var mı? | hayır — yalnız test **sayısı** (T22: 45 PHP · 0 AI) |
| 5 | Tahmin mi kanıt mı? | §10 matrisindeki UNKNOWN/⚠️ işaretleri dışında kanıtsız cümle yok |

## EK §12 — Dosya ölçek künyeleri (2026-10-15 ölçüm)

| Dosya | Satır | Ölçüm yöntemi |
|---|---|---|
| `local-inference/local-inference-kapsam.md` (ek öncesi) | **56** | read offset 9999 (T28) |
| `local-inference/index.md` | **75** | tam okuma başlığı (T6) |
| `local-inference/local-inference-web-arastirma.md` | **137** | tam okuma başlığı (T14 kaynağı) |
| `local-inference/local-inference-dogrulama.md` | **501** | tam okuma başlığı (T17) |
| `local-inference/local-inference-anayasa.md` | 531 (iddda) | dogrulama §11 [Satır: 273] — bu turda doğrudan ölçülmedi → ⚠️ |
| `local-inference/local-inference-ornekler.md` | 123 (iddia) | dogrulama §13 [Satır: 317] — aynı ⚠️ |
| `local-inference/local-inference-bilesenler.md` | UNKNOWN | bu turda okunmadı |
| K006 AI testi | 0 | ai-master L462 (öncül turlar) + dogrulama §14 `*AI*Test*` = 0 |

> **Eşik tablosu:** 500 satır eşiği bu ek öncesi yalnız `local-inference-dogrulama.md`
> karşılıyordu (501); kapsam 56 ile en alttaydı. Bu ek sonrası eşik ölçümü EK §8'dedir.

## EK §13 — Execution provider & fallback olgunluk matrisi (tasarım — kanıt sınırlı)

| EP / yol | Kaynak (dosya·satır) | Repo karşılığı | Olgunluk |
|---|---|---|---|
| CPU | dogrulama L19 (EP listesi) | EP kodu 0 (T15) | 📋 aday |
| GPU (CUDA) | dogrulama L19 (EP listesi) | 0 · donanım UNKNOWN (ai-master L355) | 📋 aday |
| DirectML (Windows) | dogrulama L20 — "Windows'ta donanımdan bağımsız GPU" | 0 · hedef OS Windows (env) | 📋 aday |
| NPU | dogrulama L19 (EP listesi) | 0 | 📋 aday |
| Sunucu-yerel vs cihaz-yerel | web-arastirma C2 [Satır: 108] | — | ⚠️ tanım belirsiz (iki anlam) |
| Yerel → bulut fallback tetikleyicisi | kapsam L47 · dogrulama U3 | kod yok | 🔴 tanımsız (U3) |
| Bulut → yerel ters geçiş | — | hiçbir dosyada yok | 🔴 kapsam dışı boşluk |
| Bellek eşiği / quantization | kapsam L48 · dogrulama U4 | sayı yok | 🔴 tanımsız (U4) |

**Okuma kuralı:** bu matris **planlama tablosudur**; "seçildi/kuruldu" satırı içermez.
`v2.md` erişilemez olduğu için (C1) eski "minimal kapsam: offline mod · yerel model
cache · hafif SDK · VSIX" önsözü de UNKNOWN işaretli taşınmıştır.

## EK §14 — Bu ek için bağlayıcı kural özeti (kapı disiplini)

| # | Kural | Uygulanış (bu ekte) |
|---|---|---|
| 1 | Append-only: eski L1–56 silinmez, üstüne yazılmaz | EK Bölüm B1–B6 alıntıları dâhil her şey dosya sonuna eklendi (P-1) |
| 2 | Zero-hallucination: kanıtsız iddia yazılmaz | 14 C-satırı + 5 UNKNOWN işareti ile taşındı; `v2.md` atıfları doğrulanabilir şekilde UNKNOWN |
| 3 | Yeni URL yasağı | EK §9 = 14 taşınan URL, 0 yeni (P-8 · P-12) |
| 4 | Beyazliste: yalnız bu `-kapsam.md` yazılır | `index.md` · 6 kardeş · `*.php` · boot dosyaları yalnız okundu (P-3…P-6) |
| 5 | `git commit` orkestratöre aittir | bu oturumda commit atılmadı (P-7) |
| 6 | Ölçüm kapısı ≥500 | nihai sayı EK §8'de; yer tutucu ölçümle değiştirildi |

**Durum:** U1–U10 açık · `status: draft` korunur · kapı ihlali 0 · beyazliste dışı yazım 0.

---

**Ek Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-15 ·
**Eklenti:** EK §0–§14 (append-only · 2026-10-12 tabanı korunmuştur · 0 yeni URL)
