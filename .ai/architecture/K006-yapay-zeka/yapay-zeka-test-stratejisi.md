---
node_id: K006-yapay-zeka-test-stratejisi
node_type: domain
status: verify-required
parent_id: "K000–K5000 Architecture Registry"
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
  - K004-shared
  - K015-AI
evidence:
  - "shared/composer.json — phpunit ^10.5, phpstan ^1.10, script 'stan' level 5 (2026-10-12)"
  - "shared/tests/ — 9 dizin envanteri (shared/AGENTS.md §2, 2026-10-12)"
  - "shared/src/Contracts/AI/ — 6 interface · 33 imza = testlenebilir yüzey (2026-10-12)"
validation: "2026-10-12 · altyapı composer.json ile kanıtlandı; K006 AI testi 0 satır (boşluk) · status=verify-required (hedef coverage ölçülmedi)"
open_questions:
  - "K006 AI sınıfları için mevcut test dosyası yok — hedef coverage %80 kimin için bağlayıcı? (QA gate ADR)"
  - "ML metrik eşikleri (drift toleransı, Precision/Recall hedefi) tanımlı değil → UNKNOWN"
  - "pytest adayı K006.9 kapsamında mı, PHP tarafı mı — dil seçimi ADR bekliyor"
group: kod
---

# K006 — Yapay Zeka · Test Stratejisi

> Kapsam: K006'ın **gerçek test yüzeyi** (ne var) + **boşluk** (ne yok) + **katmanlı strateji** +
> **kapı (gate)**. Sayılar diskten; hedefler taslak ve `verify-required` işaretlidir.

---

## 1. Mevcut Test Altyapısı (disk kanıtı)

| Öğe | Değer | Kaynak (2026-10-12) |
|---|---|---|
| Test çerçevesi | `phpunit/phpunit ^10.5` (require-dev) | `shared/composer.json` |
| Statik analiz | `phpstan/phpstan ^1.10` · `scripts.stan` = **level 5** | `shared/composer.json` |
| Test komutu | `scripts.test` | `shared/composer.json` |
| Test dizinleri | 9: Api · Component · Events · Fixtures · Middleware · OAuth · Repository · Security · Unit | `shared/AGENTS.md` §2 |
| K006 AI sınıflarına özel test | **0 satır** | envanter (AIEngine/AIWorkflow/AIOrchestrator için test dosyası yok) |
| ML/metrik testi (drift, kalite) | **0 satır** | repo grep |
| Python/pytest testi | **0 satır** (pytest yalnız aday) | `yapay-zeka-bagimliliklar.md` §3 |

**Sonuç:** altyapı var (PHPUnit + PHPStan 5), K006'nın kendi testi **yok**. Bu, stratejinin
giriş koşuludur — `status: verify-required`.

---

## 2. Testlenebilir Yüzey (arayüz üzerinden)

Gerçek sözleşmeler (33 imza) birim testinin doğal sınırıdır:

| Yüzey | İmza | Öncelik | Neden |
|---|---|---|---|
| `AIEngineInterface` | 5 | P0 | ürün çıktısı (öneri/analiz/EQ/tahmin) |
| `AIOrchestratorInterface` | 6 | P1 | görev/kontekst yaşam döngüsü |
| `MemorySystemInterface` | 7 | P1 | TTL/hiyerarşi — veri kaybı riski (R-11) |
| `KnowledgeBaseInterface` | 5 | P2 | **K016** — K006'dan sınır, yalnız çağrı sözleşmesi |
| `PromptEngineInterface` | 5 | P2 | token sayımı/şablon doğrulama |
| `ToolCallingInterface` | 5 | P2 | izin kontrolü (güvenlik yüzeyi) |

**Sınırlama:** `AIWorkflow` **interface'siz** (L18) → kontrat testi imkânsız; yalnızca
davranış testi (girdi/çıktı) yazılabilir. Bu bir bulgudur (risk R-09/K-10).

---

## 3. Katmanlı Strateji

### 3.1 Birim (P0 — her metot için)
| Hedef | Test tipi | Kanıt beklenen davranış |
|---|---|---|
| `getRecommendations` L49–77 | senaryo | 6 adım sırası; `limit` > 20 kırpılır (L51); sonuç skor azalan (L74) |
| `combineScores` L243–262 | birim | 0.6/0.4 ağırlıklı ortalama; eksik anahtar `0` sayılır (L249) |
| `applyDiversityFilter` L264–286 | birim | %30 tür garantisi (DIVERSITY_TARGET L32) |
| `getCollaborativeScores` L209–218 | karakteristik | **`[]` döner** (R-01 için regresyon kapısı) |
| `extractFeatures` L315–330 | karakteristik | **placeholder sabit** — gerçek DSP gelene kadar bu kırılmalı (R-02) |
| `optimizeEQ` L111–133 | birim | 31 band; frekans listesi L335–339; `gain` yuvarlama 2 hane |
| `analyzeAudio` L82–106 | hata | dosya yoksa `\RuntimeException` (L84–86) |
| `extractMetadata` L288–313 | birim | eksik anahtar varsayılanları (L101–104) |
| `AIWorkflow::recommendationWorkflow` L49–91 | birim (mock) | 6 çağrı sırası; `metadata.task_id` dönüşte (L85) |

### 3.2 Entegrasyon (P1)
- `AIWorkflow` ↔ `AIEngine` gerçek example (mock'suz) — **K016 mock'lanır** (`knowledge`,
  `promptEngine`, `memory`, `orchestrator`) çünkü K006 testi K016 altyapısını bloklamamalı.
- `recommendationWorkflow` L70 `JSON_THROW_ON_ERROR` — UTF-8 bozuk `history` senaryosu
  (risk R-12) **zorunlu negatif test**.

### 3.3 Sözleşme (P1)
- Her interface için `assertInstanceOf` + imza sayısı (6 interface = 33 imza) —
  yeni imza eklendiğinde test kırılır.
- `AIEngineInterface` `@return` şeması: her öneride `{id,title,artist,score,reason}` zorunlu
  (Contracts L26) — **schema doğrulayıcı** test.

### 3.4 ML / Değerlendirme (P2 — K006.2 ile birlikte)
- Öneri kalitesi: Precision@k / Recall@k / NDCG — metrik kütüphanesi adayı
  `scikit-learn` (`https://scikit-learn.org/stable/`) ve `Surprise`
  (`https://github.com/NicolasHug/Surprise`) — **ikisi de kurulu değil (UNKNOWN)**.
- Drift: aday `Evidently` (`https://www.evidentlyai.com/`) — repo'da **0 satır** (R-03).
- Eşik değerleri **tanımlı değil** → `UNKNOWN`; strateji eşikleri **öneri** olarak yazılır,
  bağlayıcı gate **değil**.

### 3.5 Uyum (P2)
- KVKK Üretken YZ rehberi maddeleri okunmadan K006.7 profil testi **onaylanmaz**
  (risk R-10, `⚠️ VERIFICATION REQUIRED`).

---

## 4. Gate (Kapı) Matrisi

| Gate | Koşul | Durum (2026-10-12) |
|---|---|---|
| G1 Statik | `composer stan` (level 5) çıkış 0 | ⚠️ bu görevde **çalıştırılmadı** — çalıştırmak yetki/gereksinim dışı |
| G2 Birim | K006 P0 senaryoları yeşil | 🔴 test yok — **0 satır** |
| G3 Sözleşme | 6 interface · 33 imza eşleşmesi | 🔴 test yok |
| G4 ML metrik | Precision@k eşiği | 🔴 eşik **UNKNOWN** |
| G5 Uyum | KVKK YZ maddeleri okundu | 🔴 `⚠️ VERIFICATION REQUIRED` |

**Hiçbir gate bu görevde geçmedi/alınamadı** — kapılar tanımlı, durumları açık.

---

## 5. Boşluk Kapanış Yol Haritası (öncelik sırası)

1. **P0 · Karakteristik testler** — R-01 (`[]` döner) ve R-02 (placeholder) için bilinçli test;
   gerçek implementasyon gelince kırmızıya döner → dönüşüm sinyali.
2. **P0 · Hata yolu** — `analyzeAudio` istisnası + workflow `JSON_THROW_ON_ERROR` (R-04/R-12).
3. **P1 · Sözleşme testi** — 33 imza + `@return` şeması denetimi (K006.9).
4. **P1 · Workflow mock testi** — 6 bağımlılık sırası (K-10).
5. **P2 · ML metrik iskeleti** — Precision@k/NDCG; eşikler K006.2 ADR'siyle bağlayıcı olur.
6. **P2 · pytest/Python** — dil seçimi ADR bekliyor (K006.9 açık soru).

---

## 6. Sayım

| Metrik | Değer |
|---|---|
| Mevcut K006 AI testi | **0 dosya / 0 satır** |
| Testlenebilir imza | 33 (6 interface) |
| Tanımlı gate | 5 (G1–G5) · geçen: **0** |
| Strateji maddesi | 5 katman (3.1–3.5) |
| Yol haritası adımı | 6 (§5) |
| UNKNOWN | 2 (eşik · dil seçimi) |
| ⚠️ VERIFICATION REQUIRED | 2 (G1 çalıştırılmadı · KVKK maddeleri) |

---

## 7. Kaynaklar

1. `shared/composer.json` — require-dev + scripts, 2026-10-12.
2. `shared/AGENTS.md` §2 — `tests/` 9 dizin envanteri, 2026-10-12.
3. `shared/src/Contracts/AI/*.php` — 6 interface / 33 imza grep, 2026-10-12.
4. `shared/src/AI/AIEngine.php` L49–77 · L82–133 · L209–235 · L315–330 — 2026-10-12.
5. `shared/src/AI/AIWorkflow.php` L49–91 — 2026-10-12.
6. `yapay-zeka-web-arastirma.md` §2 (11 · 24) — scikit-learn / Surprise aday kayıtları.

---

`status: verify-required` · `validation: 2026-10-12 · altyapı composer.json kanıtlı, K006 testi 0 satır, 5 gate tanımlı/0 geçti, hedef eşikler UNKNOWN`

---

## 8. Neva Engine Test Altyapısı Mirası (K006.9 bağlayıcı zemini · 2026-10-15)

> Bu bölüm, K006 test stratejisini **kuru bir taslaktan uygulanabilir bir zemine** bağlar:
> Neva Engine vault'u (49 md / 16.609 satır, 2026-10-15 envanter) hazır test altyapısı,
> gereksinimler ve ölçüm kapıları tanımlar. Hepsi bu turda satır numarasıyla okunmuştur.
> Repo'da `*.cpp` / `*.h` = **0** (2026-10-15) → miras alınan **kod değil, şartnamedir**.

### 8.1 Zemin envanteri (hangi dosya ne veriyor)

| Kaynak dosya | Satır | Zemin |
|---|---|---|
| `legacy-2026-07/rules/cpp-global-CLAUDE.md` | 423–447 | Google Test unit test kalıbı (fixture + `TEST_F`) |
| `legacy-2026-07/rules/cpp-global-CLAUDE.md` | 449–468 | Integration test kalıbı (ring buffer + FFT doğrulama) |
| `legacy-2026-07/rules/cpp-global-CLAUDE.md` | 470–480 | Benchmark çalıştırma + `perf` profilleme |
| `legacy-2026-07/rules/cpp-global-CLAUDE.md` | 113–124 | Toplu/paralel test komutu (`ctest -j 8`) |
| `legacy-2026-07/rules/cpp-global-CLAUDE.md` | 406 · 413 | Google Test 1.14+ · BSD-3 · vcpkg kurulum satırı |
| `legacy-2026-07/rules/neva-engine-CLAUDE.md` | 428–469 | bileşen bazlı `ctest -R` filtreleri + benchmark listesi |
| `legacy-2026-07/rules/cpp-global-AGENTS.md` | 359–364 | test gereksinimleri: coverage ≥ %80 · merge öncesi benchmark |
| `legacy-2026-07/rules/neva-engine-AGENTS.md` | 324–329 | DSP gereksinimleri: THD/SNR ölçümü dahil |
| `legacy-2026-07/rules/neva-engine-AGENTS.md` | 253–263 | commit sonrası 9 maddelik kapanış listesi |
| `01-gelistirme-plani.md` | 114–118 | Faz 4 sürekli test: unit + impulse/THD · DAC loopback · golden WAV |
| `02-dsp-zincir-mimari.md` | 79–87 | Definition of Done ölçüm tablosu (5 metrik) |
| `legacy-2026-07/ai-models.md` | 434 · 459 · 577 · 590 · 713 · 726 | job queue · job API · metrikler · hesaplama · benchmark sonuçları |

### 8.2 Komut referansı (alıntı — çalıştırılmadı)

Dosya: `rules/cpp-global-CLAUDE.md` [Satır: 115–124]

```bash
# Run unit tests (Google Test framework)
cd build
ctest --output-on-failure -j 8

# Per-project tests
ctest -R NevaEngine::Equalizer
ctest -R NevaPlayer::Codec
ctest -R WirelessConnect::WiFi
ctest -R NevaConnect::I2C
```

Dosya: `rules/neva-engine-CLAUDE.md` [Satır: 432–443 · 447–456]

```bash
ctest -R "NevaEngine::" --output-on-failure
ctest -R "AIAutoEQ::"         # ONNX inference — K006 için doğrudan aday
ctest -R "Integration::EnginePlayerRingBuffer"
ctest -R "Integration::WebSocketHandshake"
ctest -R "Integration::RestAPI"
```

### 8.3 Test gereksinimleri (bağlayıcı maddeler → K006 gate eşlemesi)

| # | Gereksinim | Kaynak · Satır | K006 gate karşılığı |
|---|---|---|---|
| N1 | Public API unit coverage ≥ %80 | cpp-global-AGENTS [Satır: 361] | TS-G8 (bu dosya §8.4) |
| N2 | IPC + shared memory tüm platformlarda entegrasyon | cpp-global-AGENTS [Satır: 362] | §3.2 entegrasyon katmanı |
| N3 | Latency/CPU hedefleri **merge öncesi** benchmark | cpp-global-AGENTS [Satır: 363] | TS-G11 (merge kapısı) |
| N4 | Windows + 1 diğer platformda derleme+çalışma | cpp-global-AGENTS [Satır: 364] | §12 platform matrisi |
| N5 | Tüm public DSP fonksiyonları unit (%80 hedef) | neva-engine-AGENTS [Satır: 326] | §3.1 birim katmanı |
| N6 | ASIO callback + ring buffer entegrasyonu | neva-engine-AGENTS [Satır: 327] | RT katmanı (§9.4) |
| N7 | Ses kalite testleri: THD · SNR ölçümleri | neva-engine-AGENTS [Satır: 329] | §9.1 kalite kapıları |
| N8 | Guardrail: Google Test ≥ %80 (Faz 0 satırı) | 01-gelistirme-plani [Satır: 29] | N1 ile aynı kapı, ikinci kaynak |

### 8.4 §3 katmanları ↔ Neva zemini eşlemesi

| §3 katmanı (mevcut) | Mevcut durum (§1) | Devralınan zemin |
|---|---|---|
| 3.1 Birim (P0) | test yok (0 satır) | `TEST_F` kalıbı [cpp-global-CLAUDE 423–447] + 7 adım görev kalıbı [cpp-global-AGENTS 91–97] |
| 3.2 Entegrasyon (P1) | tanımsız | ring-buffer/FFT entegrasyon kalıbı [449–468] + `ctest -R "Integration::*"` [neva-engine-CLAUDE 447–456] |
| 3.3 Sözleşme (P1) | 33 imza / 6 interface (§2) | `ASSERT_TRUE`/`EXPECT_EQ` kalıbı + PHP sözleşmesi satır envanteri (`-bilesenler.md`) |
| 3.4 ML / Değerlendirme (P2) | eşikler UNKNOWN | ai-models §9.1 [577] · §9.2 [590] tek kaynak; eşik değerleri `testing-ornekler.md` §5.1'de (tekrar yok) |
| 3.5 Uyum (P2) | KVKK maddeleri ⚠️ | bu vault'a ait — Neva zemininde karşılığı YOK (bkz §16) |

---

## 9. Ölçüm Kapıları Derinlemesine (DSP + AI + RT)

### 9.1 Ses kalite kapıları (tam tablo okuması)

Dosya: `02-dsp-zincir-mimari.md` [Satır: 79–87]

| Metrik | Hedef | Yöntem |
|---|---|---|
| THD+N | < %0.005 @ 1 kHz | Loopback ölçüm (K1 DAC) + Google Test sine analiz |
| SNR | > 105 dB | -60 dBFS dither testi |
| True Peak overshoot | 0 | BS.1770-4 4× TP ölçüm (libebur128) |
| Gecikme | ASIO < 10 ms · WASAPI < 20 ms | Roundtrip loopback |
| Coverage | ≥ %80 min · ≥ %90 hedef | Google Test |

K006.2/evaluation ile ilişki: bu beş kapı **AI öncesi DSP referansıdır** — AI hattı
(model çıktısı DSP zincirine girerken) aynı kapıların altından geçmek zorundadır;
AI'ın **kendi** kalite kapıları (PESQ · STOI · SI-SDR ...) `testing-ornekler.md`
§5.1'de tanımlıdır ve **tekrarlanmaz** (tek kaynak kuralı).

### 9.2 Performans kapıları (tam tablo okuması)

Dosya: `rules/neva-engine-CLAUDE.md` [Satır: 420–424]

| Performans | Hedef | Ölçüm yolu |
|---|---|---|
| CPU (31-band EQ) | < %10 @ 44.1 kHz | `/health` → cpuUsage |
| Bellek izi | < 300 MB | `rss` |
| FFT (2048 pt) | < 10 ms | benchmark suite |
| Plugin yükleme | < 500 ms/plugin | PluginManager |
| WebSocket spektrum | 30 FPS · `/fft` < 33 ms | yanıt süresi |

### 9.3 AI değerlendirme kapıları — veri kaynakları (değer tekrarı YOK)

| Kapı ailesi | Tek kaynak | Kullanım |
|---|---|---|
| Objektif metrik tanımı | `ai-models.md` §9.1 [Satır: 577] | tanım okuması |
| Metrik hesabı | `ai-models.md` §9.2 [Satır: 590] | uygulama referansı |
| Donanım-bazlı benchmark | `ai-models.md` §13.1 [Satır: 713] · §13.2 [Satır: 726] | referans değerler (RTX 4090 / RPi5) |
| Kapı eşikleri (PESQ > 3.5 vb.) | `testing-ornekler.md` §5.1 (TS-G1) | doğrulanacak eşik listesi |
| Gate çalıştırma kaydı | `yapay-zeka-dogrulama.md` §7 | PASS/BLOCK sonucunun yazıldığı tek yer |

### 9.4 Faz 4 sürekli ölçüm (plan中的 bağlayıcı — tam okuma)

Dosya: `01-gelistirme-plani.md` [Satır: 114–118]

| Madde | İçerik |
|---|---|
| 1 | Google Test: **her DSP bloğu** için unit + impulse/THD testi (coverage ≥ %80 min) |
| 2 | Ölçüm: gerçek DAC'ta loopback THD+N/SNR testi (K1 donanımı + PCM3168A) |
| 3 | Regression: **golden reference WAV**'lar (Airwindows karşılaştırmalı) |

K006.9 etkisi: (1) N1/N8 ile aynı; (2) AI hattı ayrı donanım gerektirir — AI gate'leri
**ölçümü yazılımda** (metrik hesabı [ai-models 590]) yapar, donanım loopback'i K006'nın
**değil** Neva Engine'in kapısıdır (sınır: §14 karar matrisi).

---

## 10. CI Akışı Tasarımı (K006 — uygulanacak pipeline)

### 10.1 Aşamalar (tasarım — mevcut CI ile çelişmez, ekler)

| # | Aşama | İçerik | Kaynak dayanağı |
|---|---|---|---|
| S1 | lint + statik | PHP lint · C++ derleme (varsa) | mevcut `composer.json` scripts (§1) |
| S2 | unit | `ctest --output-on-failure -j 8` + PHPUnit filtreli | cpp-global-CLAUDE [113–118] |
| S3 | integration | `ctest -R "Integration::*"` | neva-engine-CLAUDE [447–456] |
| S4 | benchmark | `*Bench --benchmark_filter=...` + eşik karşılaştırma | cpp-global-CLAUDE [470–480] |
| S5 | gate | TS-G1…G11 PASS/BLOCK değerlendirmesi | bu dosya §4 + §8.3 + §9 |
| S6 | kayıt | sonuç `-dogrulama.md` §7'ye yazılır | ADR-097 disiplini (kararlar §H/H7) |

### 10.2 Başarısızlık davranışları (tasarım kuralları)

1. S1/S2 başarısız → S3+ **atlanmaz, pipeline durur** (fail-fast; cpp-global-AGENTS [297]:
   "Run `ctest` — all tests pass" adımın ön koşulu).
2. S4 benchmark eşik altına düşerse → **BLOCK** (cpp-global-AGENTS [363]: merge öncesi
   latency/CPU doğrulaması zorunlu).
3. S5'te gate sonucu `—` (çalıştırılmamış) → pipeline **yeşil sayılmaz**; "çalıştırılmadı"
   ile "geçti" eşanlamlı değildir (mevcut §4 disiplini korunur).
4. S6'ya yalnız PASS/BLOCK yazılırsa yazılır; `—` satırı eklenmez (uydurma ölçüm yasağı).

### 10.3 Runner ortamı matrisi (tasarım)

| Ortam | Zorunluluk | Kaynak |
|---|---|---|
| Windows (ASIO cihazlı) | evet — canlı RT ölçümü | cpp-global-CLAUDE [695] |
| Linux (Ubuntu 22.04 / Fedora 38 / RPi OS) | evet — ALSA yapılandırma varyantı | cpp-global-CLAUDE [697] |
| macOS | 2. platform koşulu | cpp-global-AGENTS [364] |
| ARM64 (RPi5) | K006.4 yerel çıkarım INT8 testi için | ai-models §13.2 [726] |

---

## 11. Test Verisi ve Sabitleri

| Varlık | Tanım | Kaynak · Satır |
|---|---|---|
| Golden reference WAV | Regresyon referansı — Airwindows karşılaştırmalı | 01-gelistirme-plani [118] |
| Impulse + THD test sinyali | Her DSP bloğu unit testinde | 01-gelistirme-plani [116] |
| -60 dBFS dither sinyali | SNR ölçüm girdisi | 02-dsp-zincir [84] |
| Sine analizi (1 kHz) | Google Test içi THD+N hesabı | 02-dsp-zincir [83] |
| Loopback kayıt (K1 DAC + PCM3168A) | Donanım ölçümü — K006 DIŞI | 01-gelistirme-plani [117] |
| AI kalite test çiftleri (referans + bozulmuş) | K006.2 değerlendirme girdisi | `evaluation/*` dosyaları — bu turda tekrar okunmadı → ⚠️ VERIFICATION REQUIRED (eşleştirme ayrı tur) |

Kural: veri dosyaları vault'a **gömülmez**, yol/manifest ile referanslanır; manifest
alanları ai-models job API şemasıyla ([459]) hizalanır.

---

## 12. Test Ortamı ve Donanım Matrisi

| Katman | Ortam | Ne ölçülür | K006 sahipliği |
|---|---|---|---|
| PHP birim | composer/test runner (§1 kanıtlı) | 33 imza sözleşmesi | ✅ K006.9 |
| C++ birim (gelecek) | Google Test 1.14+ [cpp-global-CLAUDE 406] | motor fonksiyonları | 🟡 K006 ↔ Neva Engine ortak |
| Entegrasyon | `ctest -R "Integration::*"` | IPC · ring buffer · WS · REST | 🟡 ortak |
| Benchmark | `*Bench` binary'leri | latency · CPU · FFT | 🟡 ortak |
| Donanım loopback | K1 DAC + PCM3168A [01-planned 117] | THD+N · SNR | ❌ Neva Engine (K006 DIŞI) |
| AI kalite | yazılım metrik hattı [ai-models 590] | PESQ · STOI · SI-SDR vb. | ✅ K006.2 + K006.9 |

---

## 13. Sorumluluk ve Kabul Kriterleri

| Adım | Sorumlu | Kabul kriteri | Bağ |
|---|---|---|---|
| Gate tasarımı | K006.9 (bu dosya §4 · §8–§12) | her gate'in eşik + kaynak satırı var | H7 (kararlar §H) |
| Ölçüm çalıştırma | -dogrulama §7 | yalnız gerçek ölçüm yazılır | §4 mevcut kural |
| Merge kapısı | benchmark yeşil (N3) | cpp-global-AGENTS [363] | §8.3 |
| Eşik değişikliği | ADR (kararlar §H/H2) | aday eşik "seçilmeden" kodda kullanılmaz | §10.5 |
| Uyum kapısı (KVKK) | ⚠️ R-10 açık | madde listesi oluşmadan P2 tamamlanmaz | §3.5 |

---

## 14. Karar Matrisi — Neva zemininden alınan / alınmayan

| Madde | Alınır mı? | Gerekçe |
|---|---|---|
| Google Test + ctest komut kalıbı | ✅ evet | K006.9 test iskeleti bu kalıpta kurulur |
| Coverage ≥ %80 eşiği (N1/N8) | ✅ evet | iki bağımsız kaynak aynı eşiği veriyor |
| Merge öncesi benchmark kapısı (N3) | ✅ evet | AI gate'leri de aynı disipline uyar |
| Golden WAV regresyonu | 🟡 koşullu | yalnız DSP zinciri AI öncesi/sonrası etkileşiminde K006'ya dokunur |
| Donanım loopback (K1 DAC) | ❌ hayır | sahiplik Neva Engine — K006 kapıları yazılımsal |
| ASIO/WASAPI canlı cihaz testi | ❌ hayır | aynı sınır (K006.4 yerel çıkarım **cihaz testi** istemez, EP/VRAM ölçer) |
| KVKK / uyum testi | ✅ evet ama kaynağı yok | Neva zemininde karşılık YOK → ayrı madde gerekli (§3.5 + R-10) |

---

## 15. Sayım (bu bölüm sonrası)

| Sınıf | Önceki (§6) | Bu ek | Yeni toplam |
|---|---|---|---|
| Katman maddesi (§3) | 5 | +8 (§8.3 N1–N8) | 13 |
| Ölçüm kapısı (tanımlı) | 5 (G1–G5) | +11 (TS-G8…G11 · §9.1 beş DSP · §9.2 beş performans) | 16+ |
| CI aşaması | 0 | 6 (§10.1 S1–S6) | 6 |
| Test verisi varlığı | 0 | 6 (§11) | 6 |
| Karar matrisi satırı | 0 | 7 (§14) | 7 |
| Geçen gate | 0 | 0 (hiçbiri çalıştırılmadı) | **0** |
| Yeni UNKNOWN / ⚠️ | 2 + 2 | +1 (AI test çifti eşleştirmesi) | 5 / 3 |

---

## 16. Kaynak Eki (bu tur eklenenler)

1. `legacy-2026-07/rules/cpp-global-CLAUDE.md` — 113–124 · 302–304 · 341 · 406 · 413 · 423–480 · 695–697, 2026-10-15.
2. `legacy-2026-07/rules/neva-engine-CLAUDE.md` — 420–469, 2026-10-15.
3. `legacy-2026-07/rules/cpp-global-AGENTS.md` — 91–97 · 116–121 · 297 · 337 · 359–364, 2026-10-15.
4. `legacy-2026-07/rules/neva-engine-AGENTS.md` — 42 · 51 · 58–60 · 63–71 · 249 · 253–263 · 324–329, 2026-10-15.
5. `.ai/projects/NevaEngine/01-gelistirme-plani.md` — 19–29 · 114–145, 2026-10-15.
6. `.ai/projects/NevaEngine/02-dsp-zincir-mimari.md` — 16–87, 2026-10-15.
7. `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` — 434 · 459 · 577 · 590 · 713 · 726 (başlık satırları), 2026-10-15.

---

`status: verify-required` · `validation: 2026-10-12 · altyapı composer.json kanıtlı, K006 testi 0 satır, 5 gate tanımlı/0 geçti, hedef eşikler UNKNOWN · 2026-10-15 · §8–§16 eklendi (bkz. son satır)`

---

## 17. Alt Alan Bazlı Test Senaryosu Kataloğu (2026-10-15 kapanış turu)

> Her senaryo **çalıştırılmadan tasarlanmıştır**; iddia kanıtı = ilgili dosya/satır.
> "Tasarlandı" ile "geçti" eşanlamlı değildir → sonuç sütunu bilinçli olarak boş bırakılmaz,
> senaryo kaydı `-dogrulama.md` §7'de açılacaktır.

### 17.1 K006.8 recommendation senaryoları

| # | Senaryo | Beklenen | Kanıt |
|---|---|---|---|
| T-REC-1 | `limit=100` gönderilir | sonuç ≤ 20 kayda düşer | AIEngine.php [Satır: 51] · sabit [Satır: 30] |
| T-REC-2 | `user_id = null` | collaborative skorlar `[]` | AIEngine.php [Satır: 211–213] |
| T-REC-3 | iki kaynak da boş → hibrit | nihai liste **boş** (K-08 gerçeği) | AIEngine.php [Satır: 217 · 234] |
| T-REC-4 | skor < 0.7 olan aday | min skor filtresiyle elenir | AIEngine.php [Satır: 71] |
| T-REC-5 | workflow: `session_id` yok | `anonymous` kullanılır | AIWorkflow.php [Satır: 52] |
| T-REC-6 | workflow sonrası oturum | `last_recommendations` TTL 3600 sn | AIWorkflow.php [Satır: 78] |
| T-REC-7 | bilinmeyen görev tipi | `InvalidArgumentException` fırlatılır | AIOrchestrator.php [Satır: 73–75] |
| T-REC-8 | dosya yokken `analyzeAudio` | `RuntimeException` fırlatılır | AIEngine.php [Satır: 84–86] |

### 17.2 K006.7 personalization senaryoları

| # | Senaryo | Beklenen | Kanıt |
|---|---|---|---|
| T-PZ-1 | öğrenme döngüsü adımında unutma | α = 0.95 uygulanır | eq-ai-auto.md [Satır: 119] |
| T-PZ-2 | oda kalibrasyonu sapması +20 dB | ±12 dB'e clamp edilir | eq-ai-auto.md [Satır: 140] |
| T-PZ-3 | `mode.set` komutu | 4 öğe yeniden kurulur (EQ · tema · preset · FX) | eq-user-modes.md [Satır: 174–176] |
| T-PZ-4 | `skip_patterns` sayacı artışı | `too_bright`/`too_muddy` sayaçları yükselir | eq-ai-auto.md [Satır: 103–107] |
| T-PZ-5 | workflow bağlamı boş | $context = [] → motor varsayılanı (20) devreye girer | AIWorkflow.php [Satır: 55–56 · 75] |

### 17.3 K006.6 music-analysis senaryoları

| # | Senaryo | Beklenen | Kanıt |
|---|---|---|---|
| T-MA-1 | `extractFeatures` çağrısı | **placeholder** sabit döner (bpm 120.0 · key C · energy 0.7) | AIEngine.php [Satır: 315–327] |
| T-MA-2 | `extractMetadata` alanları | duration · sample_rate · bit_depth · channels | AIEngine.php [Satır: 305–309] |
| T-MA-3 | `bandToFrequency` sınır bantları | 0 → 20 Hz · geçersiz → 1000 Hz fallback | AIEngine.php [Satır: 332–342] |
| T-MA-4 | ffprobe alanı eksik | varsayılanlar (44100 · 16 · 2 kanal) | AIEngine.php [Satır: 306–308] |
| T-MA-5 | tür güven eşiği < 0.6 | uygulama kapısı kapanır | `audio-intelligence-ornekler.md` §7.3 (minConfidence kaydı) |

### 17.4 K006.4 / K006.5 inference & model-management senaryolaru

| # | Senaryo | Beklenen | Kanıt |
|---|---|---|---|
| T-INF-1 | job kuyruğu dolu | reddetme davranışı tanımlı olmalı | ai-models.md §7.2 [Satır: 434] |
| T-INF-2 | job API yanıt şeması | id · method · params sözleşmesi korunur | ai-models.md §7.3 [Satır: 459] |
| T-MM-1 | INT8 quantize sonrası VRAM | FP16'dan düşük | ai-models.md §4.1 [Satır: 208] |
| T-MM-2 | model sürüm geçişi | eski/yeni sürüm sıralaması tutarlı | ai-models.md §8.2 [Satır: 528] |
| T-MM-3 | HuggingFace indirme akışı | hata/yeniden deneme yolu | ai-models.md §8.1 [Satır: 503] |
| T-INF-3 | benchmark regresyonu | §13.1/§13.2 referans üstüne çıkmaz | ai-models.md [Satır: 713 · 726] |

### 17.5 K006.1 / K006.2 / K006.9 senaryoları

| # | Senaryo | Beklenen | Kanıt |
|---|---|---|---|
| T-AI-1 | ONNX EP CPU'ya düşüş | iş sonuçlanır (fallback) | `audio-intelligence-ornekler.md` §7.3 fallback satırı |
| T-AI-2 | queue taşması | `maxQueueSize` aşımı reddi | `audio-intelligence-ornekler.md` §7.3 (ai-models 633 kaydı) |
| T-EV-1 | gate sonucu alanı eksik | `gateResult.failingMetric` — şema eksiği ⚠️ | `evaluation-ornekler.md` §9.2 (UNKNOWN kayıtları) |
| T-TS-1 | ctest paralel koşu | `-j 8` yeşil (altyapı hazır) | cpp-global-CLAUDE.md [Satır: 118] |
| T-TS-2 | coverage ölçümü | ≥ %80 (N1 kapısı) | cpp-global-AGENTS.md [Satır: 361] |

### 17.6 Senaryo → gate eşleme

| Gate | Kapsadığı senaryolar |
|---|---|
| TS-G1 (kalite metrikleri) | T-MA-1 · T-AI-1 (çıktı kalitesi üzerinden) |
| TS-G2 (RT gecikme) | T-AI-2 (kuyruk gecikme bütçesi) |
| TS-G4 (kuyruk) | T-INF-1 · T-AI-2 |
| TS-G6 (ölçüm sanity) | T-MA-3 · T-TS-1 |
| TS-G8 (coverage) | T-TS-2 |
| TS-G9 (AIAutoEQ ctest) | T-INF-2 · T-MM-1 |
| Yeni T-REC-* kümesi | §4'teki RC-G… eşikleri (recommendation-ornekler §6.6) |

### 17.7 §17 kapanış kaydı

- Eklenen: 5 senaryo başlığı + 27 tek satır senaryo + 1 eşleme tablosu.
- Uydurma identifier: 0 · ölçü uydurma: 0 · yeni `⚠️ VERIFICATION REQUIRED`: 0
  (T-EV-1'deki şema eksiği zaten `evaluation-ornekler.md` §9.2'de kayıtlı ⚠️'dır, yenisi değil).
- Hiçbir senaryo bu turda **çalıştırılmadı**; sonuç kaydı `-dogrulama.md` §7'ye bağlıdır.

---

`status: verify-required` · `validation: 2026-10-12 · altyapı composer.json kanıtlı, K006 testi 0 satır, 5 gate tanımlı/0 geçti, hedef eşikler UNKNOWN · 2026-10-15 · §8–§16 eklendi: Neva test zemini 12 kaynak/satır envanteri, N1–N8 gereksinim, CI 6 aşama, 16+ tanımlı gate/0 geçti, karar matrisi 7 satır — hiçbir mevcut satır silinmedi · §17 eklendi: 27 senaryo (0 çalıştırıldı) · toplam append-only, silinen satır 0`
