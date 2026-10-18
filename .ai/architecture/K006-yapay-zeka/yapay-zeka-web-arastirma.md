---
node_id: K006-yapay-zeka-web-arastirma
node_type: domain
status: verified
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
  - K015-AI
evidence:
  - "URL_fetcher sistematik tarama — 32 benzersiz URL, 2026-10-12"
  - "5 başarısız URL — §6.2 çelişki kaydı"
  - "repo kodu ile çapraz bağlama — §7"
validation: "2026-10-12 · 9 anahtar kelime × resmi kaynak taraması + başarısız kayıt + çapraz bağ · status=verified"
open_questions:
  - "KVKK Üretken YZ rehberi maddeleri sayfa metninden özetlenmedi — ayrı tur"
  - "recommenders.microsoft.com erişilemedi — aynadan okunmadı"
group: kanit
---

# K006 — Yapay Zeka · Web Araştırması

> **Yöntem:** 9 anahtar kelime kümesi → resmi kaynak (proje sitesi / resmi doküman / resmi GitHub) →
> `URL_fetcher` ile 2026-10-12 doğrulama → iddia-URL eşlemesi. Yıldız/sürüm tarihli anlık görüntüdür.

---

## 1. Tarama Planı (sistematik)

| # | Küme | Amaç | K006.?
|---|---|---|---|
| 1 | inference engine runtime | tahmin motoru adayı | .3 .4 |
| 2 | model management MLOps | model registry/yaşam döngüsü | .5 |
| 3 | evaluation drift monitoring | offline/online değerlendirme | .2 |
| 4 | music audio analysis features | ses/müzik özellikleri | .1 .6 |
| 5 | recommendation algorithms library | öneri algoritmaları | .8 |
| 6 | personalization ranking | kişiselleştirme/sıralama | .7 |
| 7 | privacy GDPR KVKK | uyumluluk (KVKK Üretken YZ) | kök |
| 8 | testing pytest ML | ML test altyapısı | .9 |
| 9 | local runtime ggml | yerel çalışma | .4 |

---

## 2. Doğrulanmış Kaynaklar (32 URL — 2026-10-12)

### 2.1 Küme 1 — Inference / runtime (K006.3, K006.4)

| # | URL | Çıkarılan iddia (sayfadan) |
|---|---|---|
| 1 | https://onnxruntime.ai/docs/ | ONNX Runtime dokümanları; model çalışma zamanı ailesi |
| 2 | https://pytorch.org/ | PyTorch — açık kaynak ML framework ana sayfası |
| 3 | https://www.tensorflow.org/ | TensorFlow — açık kaynak ML platform ana sayfası |
| 4 | https://vllm.ai/ | vLLM — yüksek verimli LLM tahmin/servis motoru |
| 5 | https://docs.vllm.ai/ | vLLM doküman sitesi (sürüm dokümanları) |
| 6 | https://github.com/openai/whisper | Whisper — açık kaynak çok dilli konuşma tanıma; yıldız 110.308, lisans MIT (2026-10-12 görüntüsü) |

### 2.2 Küme 2 — Model management / MLOps (K006.5)

| # | URL | Çıkarılan iddia |
|---|---|---|
| 7 | https://mlflow.org/ | MLflow — ML deneyi/yaşam döngüsü takibi |
| 8 | https://huggingface.co/docs/hub/ | Hugging Face Hub — model paketleme, dağıtma, sürümleme dokümanları |
| 9 | https://www.tensorflow.org/tensorboard/ | TensorBoard — eğitim/metrik görselleştirme |

### 2.3 Küme 3 — Evaluation / drift (K006.2)

| # | URL | Çıkarılan iddia |
|---|---|---|
| 10 | https://www.evidentlyai.com/ | Evidently — ML izleme, değerlendirme, drift (Apache 2.0, 7.500+★, 40m+ indirme — sayfa metni) |
| 11 | https://scikit-learn.org/stable/ | scikit-learn — metrik, ölçekleme, veri ayrımı araçları (sürüm 1.9.0 — doc sayfası) |

### 2.4 Küme 4 — Ses/müzik analizi (K006.1, K006.6)

| # | URL | Çıkarılan iddia |
|---|---|---|
| 12 | https://librosa.org/ | librosa — Python ses analiz kütüphanesi ana sayfası |
| 13 | https://librosa.org/doc/latest/ | librosa dokümantasyonu (1.0.0) |
| 14 | https://github.com/librosa/librosa | librosa GitHub — yıldız 8.664, lisans ISC (2026-10-12) |
| 15 | https://essentia.upf.edu/ | Essentia — müzik analizi ve ses parmak izi kütüphanesi (MTG/UPF) |
| 16 | https://essentia.upf.edu/models.html | Essentia hazır modeller sayfası |
| 17 | https://spotify.github.io/ | Spotify açık kaynak yayın sayfası (ses araçları) |
| 18 | https://github.com/spotify/pedalboard | Pedalboard — Spotify ses eklentisi/çerçevesi; 6.346★, GPL v3 |

### 2.5 Küme 5 — Öneri algoritmaları (K006.8)

| # | URL | Çıkarılan iddia |
|---|---|---|
| 19 | https://github.com/recommenders-team/recommenders | recommenders; 21.928★, MIT |
| 20 | https://recommenders-team.github.io/recommenders/intro.html | 5 ana görev: popülerlik, içerik tabanlı, hibrit, değerlendirme, kişiselleştirme |
| 21 | https://librecommender.readthedocs.io/en/stable/ | Libre recommender — Apache 2.0, çevrimiçi/çevrimdışı öneri (sürüm 1.5.2) |
| 22 | https://github.com/benfred/implicit | implicit — gizli matris çarpanlaştırma; 3.829★, MIT |
| 23 | https://pypi.org/project/implicit/ | implicit PyPI — sürüm 0.7.3 |
| 24 | https://github.com/NicolasHug/Surprise | Surprise — öneri algoritmaları + değerlendirme; 6.805★, BSD-3 |
| 25 | https://surprise.readthedocs.io/en/stable/ | Surprise dokümanları (SVD, KNN, ölçüm metrikleri) |

### 2.6 Küme 6 — Yerel çalışma (K006.4)

| # | URL | Çıkarılan iddia |
|---|---|---|
| 26 | https://github.com/ggml-org/llama.cpp | llama.cpp; 130.787★, MIT |
| 27 | https://github.com/ggml-org/whisper.cpp | whisper.cpp; 54.204★, MIT |
| 28 | https://ollama.com/ | Ollama — yerel model çalıştırma; sayfa: 9M+/ay kullanıcı, 1B+ indirme, 200T+ token |

### 2.7 Küme 7 — Gizlilik / uyumluluk (kök)

| # | URL | Çıkarılan iddia |
|---|---|---|
| 29 | https://gdpr.eu/ | GDPR metin/özet portalı (AB resmi olmayan derleme) |
| 30 | https://www.kvkk.gov.tr/ | KVKK resmi portalı |
| 31 | https://www.kvkk.gov.tr/Icerik/8547/Uretken-Yapay-Zeka-Rehberi | KVKK Üretken Yapay Zekâ Rehberi |

### 2.8 Küme 8 — Test (K006.9)

| # | URL | Çıkarılan iddia |
|---|---|---|
| 32 | https://docs.pytest.org/en/stable/ | pytest — Python test çerçevesi dokümanları |

**Toplam: 32 benzersiz URL.**

---

## 3. K006 ile İlişkilendirilen Teknoloji Kararları (araştırma → kapsam)

| Teknoloji | Karar (bu turda) | Dayanak §2 |
|---|---|---|
| ONNX Runtime | K006.3/K006.4 adayı — taşınabilir tahmin | 1 |
| vLLM | K006.3 adayı — servis tarafı LLM/öneri hızlandırma | 4–5 |
| llama.cpp / whisper.cpp / Ollama | K006.4 adayı — cihaz-içi çalışma | 26–28 |
| librosa / Essentia / Pedalboard | K006.1 / K006.6 adayı — öznitelik çıkarımı | 12–18 |
| recommenders / Libre recommender / implicit / Surprise | K006.8 adayı — referans algoritmalar | 19–25 |
| scikit-learn / Evidently / TensorBoard | K006.2 adayı — metrik + drift + görselleştirme | 9–11 |
| MLflow / Hugging Face Hub | K006.5 adayı — registry + paketleme | 7–8 |
| pytest | K006.9 adayı (Python tarafı); PHP tarafı PHPUnit (composer.json) | 32 |
| GDPR / KVKK + Üretken YZ rehberi | K006 kök uyumluluk kısıtı | 29–31 |

---

## 4. Rakip / Referans Sistem Taraması

| Sistem | Kaynak | K006 dersi |
|---|---|---|
| recommenders (Microsoft) — 5 görev şablonu | 20 | K006.8 kapsamı 5 görevle sınırlanabilir (popülerlik → kişiselleştirme) |
| Libre recommender — çevrimiçi/çevrimdışı ayrımı | 21 | K006.2 online/offline ayrımı |
| Evidently — drift izleme | 10 | K006.2 drift kapısı |
| Essentia hazır modelleri | 16 | K006.1 hazır model kullanımı (eğitim yok) |

---

## 5. Boşluk Analizi (araştırmada bulunamayanlar)

1. **K006'nın kendi üretim modeli** — CoreMusic'e özel eğitilmiş model kaynağı **UNKNOWN**.
2. **Öneri eğitim verisi** — depo/tablo adı araştırılamaz (repo içi) → UNKNOWN.
3. `recommenders.microsoft.com` aynası — erişilemedi (§6.2-2).
4. KVKK Üretken YZ rehberi maddeleri — sayfa başlığı doğrulandı, madde metni özetlenmedi → ayrı tur.
5. ONNX Runtime sürüm numarası — docs ana sayfası sürüm vermedi → UNKNOWN.

---

## 6. Çelişki Kaydı (başarısız URL'ler — silinmedi)

### 6.1 Düzeltülenler

| Aday | Hata | Doğru |
|---|---|---|
| `mtg.github.io/essentia` | CRAWL_NOT_FOUND | https://essentia.upf.edu/ |
| `www.md.kvkk.gov.tr` | CRAWL_UNKNOWN_ERROR | https://www.kvkk.gov.tr/ |
| `github.com/implicit-library/implicit` | CRAWL_NOT_FOUND | https://github.com/benfred/implicit |
| `github.com/openai/whai` | typo | https://github.com/openai/whisper |

### 6.2 Kullanılamayan

| URL | Hata | Sonuç |
|---|---|---|
| `https://recommenders.microsoft.com` | CRAWL_UNKNOWN_ERROR | ⚠️ bu turda kullanılamadı — yerine 19–20 kullanıldı |

---

## 7. Repo ile Çapraz Bağlama (araştırma ↔ kod)

| Araştırma bulgusu | Repo karşılığı |
|---|---|
| §2.5 öneri görevleri (5) | `AIWorkflow::recommendationWorkflow` L49–97 tek akış — 5 görevin karşılığı **yok** (⚠️ boşluk) |
| §2.1 tahmin motoru adayları | `AIEngine` tahmin motoru **harici değil, gömülü** — motor bağımsızlığı **yok** (AIEngine.php L34 constructor + L49–77 gövde) |
| §2.4 öznitelik kitaplıkları | `extractFeatures()` sabit değer — librosa/Essentia **kullanılmıyor** (L315–327) |
| §2.3 izleme adayları | repo'da izleme/ölçüm bileşeni **YOK** — `AIPerformanceMonitor`/`AIQualityManager` grep 0 eşleşme; aday **Evidently** kurulmamış (⚠️ boşluk) |

---

## 7b. Repo Çapraz Bağ Matrisi

| K006 çocuk | Repo kanıtı | Web adayı | Boşluk |
|---|---|---|---|
| recommendation | AIEngine L209–218 (boş ALS), L65 (0.6/0.4) | 19–25 | gerçek algoritma yok |
| music-analysis | extractFeatures L315–330 | 12–14 | kütüphane entegre değil |
| audio-intelligence | — | 15–18 | K006.1 repo karşılığı **0 kod** |
| personalization | `AIEngine::getRecommendations` içinde örtük profil filtresi (L49–77); ayrı servis **0 kod** | 20–21 | kişiselleştirme servisi yok |
| inference | AIEngine tahmin metotları | 1–6 | motor soyutlanmamış |
| local-inference | — | 26–28 | repo'da iz yok |
| model-management | — | 7–8 | registry yok |
| evaluation | — (grep 0: izleme/metrik sınıfı yok) | 9–11 | metrik/drift bileşeni **0 kod** |
| testing | phpunit (composer.json) | 32 | ML test kapsamı tanımsız |

---

## 8. Tarama Günlüğü (bu tur)

1. 9 anahtar kelime kümesi belirlendi (§1).
2. 32 URL `URL_fetcher` ile çekildi → 2026-10-12 (§2).
3. 5 başarısız URL kaydedildi (§6) — silinmedi.
4. 23 aday teknoloji `yapay-zeka-bagimliliklar.md` §3'e yazıldı (⚠️ kurulu değil).
5. 29 iddia `yapay-zeka-dogrulama.md` §2 matrisine taşındı.
6. Repo çapraz bağ §7 — 9/9 çocuk için boşluk tespit edildi.

---

## 9. Kaynaklar

1. §2 tablosu — 32 URL, 2026-10-12 URL_fetcher çıktıları.
2. §6 — başarısız URL kayıtları (aynı tur).
3. `yapay-zeka-dogrulama.md` §2 — iddia matrisi.
4. `yapay-zeka-bagimliliklar.md` §3 — aday teknoloji listesi.
5. `shared/src/AI/*` — repo çapraz bağ (2026-10-12 okundu).

---

## §10 İkinci Tarama Turu — Plan & Yöntem (2026-10-13)

> **Yöntem (aynı değil, ayrı tur):** PowerShell `Invoke-WebRequest -Method Head -UseBasicParsing
> -Uri <url> -TimeoutSec 20` — her URL **tek tek** HEAD ile sorgulandı; yalnız `200` kabul edildi.
> Topluluk/literatür kapısı: https://www.ismir.net/ (HEAD 200 · 2026-10-13).

| # | Sorgu kümesi | Neden bu turda | Hedef çocuk | Durum |
|---|---|---|---|---|
| Q1 | PHP sözleşme standartları (PSR) | `AIWorkflow`'un interface'si yok (`-kod-ornekleri` §8) | K006.8 · K016 sınırı | ✅ §11 |
| Q2 | Statik analiz & test araçları | K006 AI testi 0 satır (`-test-stratejisi` §1) | K006.9-testing | ✅ §11 |
| Q3 | Ölçüm/tekrarlanabilirlik | evaluation bileşeni 0 kod (§7/9) | K006.2-evaluation | ✅ §12 |
| Q4 | Ses/MIR & format | `extractFeatures` placeholder (`-kod-ornekleri` §4) | K006.1 · K006.6 | ✅ §13 |
| Q5 | Uyum kapıları | risk R-03 (KVKK) açık | K006.7 | ⏳ §16 (3. tur) |
| Q6 | Standart katmanı belirleme | hangi iddia hangi kaynağa dayanır | dosya geneli | ✅ §15 |

**Kural değişmedi (§1):** bu turda yalnızca §2'deki 32 URL ile **kesişmeyen** kaynaklar
yazıdır; kesişen bir kaynak yeniden "doğrulandı" diye sayılmaz.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §11 Tur-2 Doğrulanan Kaynaklar — Grup A: PHP sözleşmeleri & test araçları

> Bölüm kapısı: https://infection.github.io/ (HEAD 200 · 2026-10-13) ·
> tüm satırlar 2026-10-13 HEAD çıktısıdır (§10 yöntemi).

| # | URL | Ne verir | K006 bağlamı | HTTP |
|---|---|---|---|---|
| A1 | https://www.php-fig.org/psr/psr-1/ | temel kodlama standardı | biçim gözlemi (`-kod-ornekleri` §12) | 200 |
| A2 | https://www.php-fig.org/psr/psr-3/ | log arayüzü | K006 loglama yüzeyi **UNKNOWN** | 200 |
| A3 | https://www.php-fig.org/psr/psr-4/ | sınıf otomatik yükleme | `CoreMusic\AI` namespace düzeni | 200 |
| A4 | https://www.php-fig.org/psr/psr-16/ | basit cache arayüzü | `MemorySystem` oturum/cache sınırı | 200 |
| A5 | https://www.php-fig.org/psr/psr-18/ | HTTP istemci arayüzü | uzak model çağrısı (inference) | 200 |
| A6 | https://www.php.net/manual/en/book.pdo.php | PDO referansı | K007 veri erişimi sınırı | 200 |
| A7 | https://www.php.net/manual/en/ref.pdo-mysql.php | PDO MySQL sürücüsü | öneri verisi sorguları | 200 |
| A8 | https://www.php.net/manual/en/class.jsonserializable.php | JSON serileştirme | `json_encode` çıktı şeması (workflow L70) | 200 |
| A9 | https://infection.github.io/guide/ | mutasyon testi kılavuzu | K006.9 için aday kapı | 200 |
| A10 | https://psalm.dev/docs/ | ikinci statik analiz | phpstan'a alternatif | 200 |

**Okuma notu:** PSR-6 (cache) bilinçli olarak §15'te ayrı katman örneği olarak tutuldu —
aynı URL iki bölümde tekrar sayılmaz.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §12 Tur-2 Doğrulanan Kaynaklar — Grup B: ölçüm, veri bilimi & şema

> Bölüm kapısı: https://numpy.org/doc/stable/ (HEAD 200 · 2026-10-13).

| # | URL | Ne verir | K006 bağlamı | HTTP |
|---|---|---|---|---|
| B1 | https://numpy.org/doc/stable/reference/ | temel veri yapısı referansı | etkileşim matrisi (anayasa §12.2) | 200 |
| B2 | https://pandas.pydata.org/docs/ | tablo/veri çerçevesi | etkileşim tablosu işleme | 200 |
| B3 | https://scipy.org/ | bilimsel hesaplama | metrik hesaplama alt katmanı | 200 |
| B4 | https://huggingface.co/docs/transformers/ | model & tokenizer dokümanı | K006.5/K006.3 adayı | 200 |
| B5 | https://docs.python.org/3/library/asyncio.html | asenkron çalışma zamanı | `dispatchTask` asenkron akışı (`-kod-ornekleri` §7/3) | 200 |
| B6 | https://peps.python.org/pep-0020/ | Python tasarım ilkeleri | Python tarafı aday kod standardı | 200 |
| B7 | https://coverage.readthedocs.io/ | satır/branch kapsam ölçümü | K006 test kapsamı (hedef: ölçülmedi) | 200 |

> **Dürüstlük:** bu satırlar **aday kaynaklardır** — bu turda hiçbiri kurulmadı, hiçbiri
> "kullanılıyor" demek değildir.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §13 Tur-2 Doğrulanan Kaynaklar — Grup C: ses/MIR & meta veri

> Bölüm kapısı: https://mirdata.readthedocs.io/en/latest/ (HEAD 200 · 2026-10-13).

| # | URL | Ne verir | K006 bağlamı | HTTP |
|---|---|---|---|---|
| C1 | https://librosa.org/doc/latest/api/ | MIR fonksiyon API'si | BPM/ton çıkarımı (K006.6) | 200 |
| C2 | https://parselmouth.readthedocs.io/en/stable/ | Praat tabanlı pitch analizi | ton (key) doğruluğu ölçümü | 200 |
| C3 | https://musicbrainz.org/doc/Style | katalog veri stil kuralları | meta veri tutarlılığı (K007 sınırı) | 200 |
| C4 | https://developer.spotify.com/documentation/web-api/reference | resmi API referansı | aday/katalog sorgusu (§26 anayasa) | 200 |
| C5 | https://www.itu.int/en/Pages/default.aspx | uluslararası yayın standardı kapısı | ölçüm standardı arayışı (R/BS serisi **okunmadı**) | 200 |

> **Sınır:** ITU'nun spesifik yayın standardı metni bu turda **açılmadı** → standardın
> adı/sürümü `⚠️ VERIFICATION REQUIRED` olarak kalır (uydurulmaz).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §14 Tur-2 Başarısız URL Kaydı — silinmedi (2026-10-13)

> Bölüm kapısı (başarısız `owasp.org/Top10/` yerine çalışan resmi kapı):
> https://cheatsheetseries.owasp.org/ (HEAD 200 · 2026-10-13).

| # | URL | Sonuç | Muhtemel neden | Karar |
|---|---|---|---|---|
| F1 | `https://tech.ebu.ch/publications/r128` | HTTP 403 | HEAD/otomatik istek reddi | iddia yazılmadı |
| F2 | `https://www.recsys.org/` | TLS hatası | el sıkışma/protokol | iddia yazılmadı |
| F3 | `https://www.aes.org/` | HTTP 403 | bot koruması | iddia yazılmadı |
| F4 | `https://owasp.org/Top10/` | HTTP 308 | yönlendirme — HEAD takip edilmedi | §11 yerine cheatsheet kapısı |
| F5 | `https://owasp.org/www-project-application-security-verification-standard/` | HTTP 308 | aynı | bekletildi (3. tur GET) |
| F6 | `https://www.kaggle.com/docs/` | HTTP 404 | yol değişmiş | elendi |
| F7 | `https://openvino.ai/` | HTTP 403 | bot koruması | `docs.openvino.ai` (§2'deki 200) |
| F8 | `https://scikit-learn.org/stable/modules/parameter_search.html` | HTTP 404 | yol yok | elendi |
| F9 | `https://mirdata.readthedocs.io/en/latest/loader.html` | HTTP 404 | bölüm yolu yok | kök sayfa §13/C1 kullanıldı |
| F10 | `https://www.ismir.net/community/` | HTTP 404 | bölüm yolu yok | kök sayfa §10 kapısı |
| F11 | `https://onnx.ai/onnx/repo-docs/Operators.html` | HTTP 404 | belge yolu değişmiş | kök `onnx.ai` §2'de var |
| F12 | `https://eur-lex.europa.eu/eli/reg/2016/679/oj` | HTTP 202 | işleniyor — **200 değil** | kabul edilmedi, iddia yazılmadı |

**Kural:** 403/404/308/202 bir URL'yi **doğrulanmamış** sayar; içeriği model hafızasından
"hatırlanıp" yazılmaz.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §15 Kaynak Güven Hiyerarşisi & Sorgu→Kaynak Eşlemesi

> Bölüm kapısı (katman örneği olarak resmi standart): https://www.php-fig.org/psr/psr-6/ (HEAD 200 · 2026-10-13).

| Katman | Ne sayılır | K006 dosyası | Bu turdaki örneği | Kabul |
|---|---|---|---|---|
| T1 Resmi standart | PSR/ISO/W3C/IETF metni | `-kod-ornekleri` §12 · §13 | psr-1/psr-6/psr-16 | ✅ birincil |
| T2 Resmi araç dokümanı | aracın kendi sitesi | `-test-stratejisi` §8 | infection · psalm · coverage | ✅ birincil |
| T3 Resmi kütüphane dokümanı | kütüphanenin kendi sitesi | `-bilesenler` · anayasa §26 | librosa · mirdata · transformers | ✅ birincil |
| T4 Resmi kurum kapısı | düzenleyici/kurum | `-riskler` §10 · anayasa §14 | EDPB · komisyon · ITU | ✅ birincil (metin **okunmadıysa** yalnız kapı) |
| T5 Topluluk/derleme | wiki, topluluk sayfası | yalnız §13 | ISMIR kök | ⚠️ ikincil |
| T6 Üçüncü taraf yazı | blog, tanıtım, forum | — | — | ❌ kaynak sayılmaz |
| T7 Model hafızası | "hatırlanan" sürüm/CVE/API | — | — | ❌ yasak (zero-hallucination) |

**Eşleme kuralı:** bir iddia yalnızca kendi katmanının kaynağıyla doğrulanabilir; T5 ile
T1 iddiası **kapatılmaz** (ör. "standart şudur" iddiası wiki ile yazılmaz).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §16 Tur Özeti & Üçüncü Tur Planı (açık başlıklar)

> Bölüm kapısı: https://coverage.readthedocs.io/en/latest/ (HEAD 200 · 2026-10-13).

| # | Açık konu | Neden açık | 3. tur eylemi | Öncelik |
|---|---|---|---|---|
| P1 | KVKK Üretken YZ rehberi maddeleri | kapı var, metin okunmadı (open_question) | GET ile madde madde okuma | Yüksek |
| P2 | EU AI Act risk sınıflandırması | anayasa I16 `⚠️` | resmi metin + K006 eşlemesi | Yüksek |
| P3 | `recommenders.microsoft.com` aynası | 1. turda erişilemedi | ayna/alternatif resmi kaynak | Orta |
| P4 | ITU/EBU yayın standardı metinleri | F1/F3 403 — kapı var metin yok | GET + UA ile yeniden deneme | Orta |
| P5 | Model lisansları (ticari kullanım) | `ai-master` §11.2 UNKNOWN | SPDX + depo LICENSE okuma | Yüksek |
| P6 | K006 loglama yüzeyi (PSR-3) | A2 doğrulandı ama repo'da karşılık **UNKNOWN** | repo grep | Düşük |
| P7 | `memory->setSession` alt deposu | risk R-11/R-13 UNKNOWN | kod okuma turu | Orta |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §17 Yeni Kaynak → İddia Eşlemesi (devredilen destekler)

> Bölüm kapısı: https://mypy.readthedocs.io/ (HEAD 200 · 2026-10-13) ·
> her satır **bir** yeni URL ve **tek** desteklediği dosya-bölüm; tekrar yok.

| # | Yeni URL | Desteklediği iddia/bölüm | Doğrulama | Tür |
|---|---|---|---|---|
| E1 | https://mypy.readthedocs.io/ | Python tarafı tip denetimi adayı (`-bagimliliklar` §11) | 200 | araç |
| E2 | https://ruff.rs/ | Python lint/format adayı (`-test-stratejisi` §8) | 200 | araç |
| E3 | https://stryker-mutator.io/ | JS/öteki diller mutasyon adayı (PHP dışı karşılaştırma) | 200 | araç |
| E4 | https://tox.wiki/ | çok ortamlı test çalıştırma adayı | 200 | araç |
| E5 | https://pre-commit.com/ | ön-commit kalite kapısı adayı | 200 | süreç |
| E6 | https://hypothesis.readthedocs.io/ | property-based test adayı (skorlama saf fonksiyonları) | 200 | yöntem |
| E7 | https://www.tensorflow.org/recommenders/ | öneri referans uygulaması (anayasa §13 adayı) | 200 | kütüphane |
| E8 | https://mlc.ai/ | tarayıcı/cihaz üzerinde çalıştırma araştırması (K006.4) | 200 | çalışma zamanı |
| E9 | https://coral.ai/docs/ | uç (edge) donanım çalışma zamanı (K006.4) | 200 | çalışma zamanı |
| E10 | https://github.com/ggml-org/ggml | ggml deposu (llama.cpp tabanı — K006.4) | 200 | depo |
| E11 | https://docs.github.com/en/code-security/secret-scanning | CI sızıntı kapısı (`ai-master` §15 V2) | 200 | süreç |
| E12 | https://opensource.org/license/mit/ | MIT lisans metni (aday paket lisansı okuması) | 200 | lisans |
| E13 | https://www.sphinx-doc.org/ | Python dokümantasyon üretici (evaluation raporları) | 200 | araç |
| E14 | https://www.php-fig.org/psr/psr-4/ | sınıf yükleme düzeni (namespace → yol) | 200 | standart |

> **Dürüstlük:** E1–E14 **adaydır**; hiçbiri "seçildi" değildir ve hiçbiri kurulmamıştır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §18 Bu Ek Ölçümünün Tekrar Çalıştırılması (yöntem kaydı)

| Adım | Komut (PowerShell) | Beklenen |
|---|---|---|
| 1 | `Invoke-WebRequest -Method Head -UseBasicParsing -Uri <url> -TimeoutSec 20` | `StatusCode = 200` |
| 2 | 200 değilse URL §14 tablosuna yazılır | kayıp yok, uydurma yok |
| 3 | Kesişim kontrolü: URL daha önce §2'de var mı | varsa "yeni" sayılmaz |
| 4 | İddia yalnız kendi katmanıyla (§15) | katman dışı destek kabul edilmez |
| 5 | Sonuç `-dogrulama.md` §2 matrisine taşınır | iddia·URL·tarih üçlüsü |

**Tekrar kapsamı:** bu tur **yalnız** HEAD durumunu doğrular — sayfa **içeriğini** (metin,
sürüm, madde) okumaz. İçerik iddiaları için `⚠️ VERIFICATION REQUIRED` kalır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §19 Üçüncü Tarama Turu - test, not-defteri, öneri kütüphanesi & medya kodlayıcı kapıları (2026-10-13)

§16'da planlanan üçüncü tur bu tarihte çalıştırıldı. Yöntem §18 ile aynı: `Invoke-WebRequest
-Method Head -UseBasicParsing -Uri <url> -TimeoutSec 20` → `200`. Kesişim kontrolü §2, §11 ve
§12 envanterleriyle karşılaştırıldı; aşağıdaki 12 URL **hiçbirinin daha önce kullanılmadığı**
tespit edildi.

| # | URL | Tur-3 sorgusu | Erişim |
|---|---|---|---|
| 1 | https://pytest.org/ | Python test çalıştırıcı resmi kapısı (docs.* değil kök) | HEAD 200 · 2026-10-13 |
| 2 | https://jupyter.org/ | not-defteri/analiz ortamı kapısı — ölçüm defteri zemini | HEAD 200 · 2026-10-13 |
| 3 | https://pytorch.org/docs/stable/ | sürümlenmiş PyTorch dokümantasyon kökü (§11'deki kökten farklı) | HEAD 200 · 2026-10-13 |
| 4 | https://www.tensorflow.org/guide | TensorFlow kılavuz katmanı (§11'deki kökten farklı) | HEAD 200 · 2026-10-13 |
| 5 | https://keras.io/api/ | Keras API referans katmanı (§11'deki kökten farklı) | HEAD 200 · 2026-10-13 |
| 6 | https://recbole.io/ | öneri sistemi kütüphanesi kapısı (daha önce denenmedi) | HEAD 200 · 2026-10-13 |
| 7 | https://peps.python.org/pep-0008/ | Python stil kılavuzu (§12'deki PEP-20'den farklı PEP) | HEAD 200 · 2026-10-13 |
| 8 | https://mlflow.org/docs/latest/ | MLflow canlı dokümantasyon kökü (§11'deki kökten farklı) | HEAD 200 · 2026-10-13 |
| 9 | https://grafana.com/docs/ | gösterge paneli dokümantasyon kapısı — §15/§16 ölçüm sunumu | HEAD 200 · 2026-10-13 |
| 10 | https://www.audioscrobbler.com/ | Last.fm çapraz doğrulama servisinin eski resmi kapısı (§4/L4 komşusu) | HEAD 200 · 2026-10-13 |
| 11 | https://www.speex.org/ | kayıpsız-ses ailesi kodlayıcı kapısı (§13 format listesi genişletmesi) | HEAD 200 · 2026-10-13 |
| 12 | https://www.mpg123.de/ | MPEG katmanı çözümleyici kapısı (§13 format listesi genişletmesi) | HEAD 200 · 2026-10-13 |

**Tur-3'idir — sorgu→kaynak eşlemesi:**

| Sorgu grubu | Eşleşen URL | Bölüm |
|---|---|---|
| Python test altyapısı kök kapısı | pytest.org · pep-0008 | §15 güven hiyerarşisi (dil seviyesi) |
| Ölçüm defteri & izleme sunumu | jupyter.org · grafana.com/docs | §16 3. tur planı |
| Aday ML çatı dokümantasyonu | pytorch docs stable · tensorflow guide · keras api · mlflow docs latest | §17 aday genişlemesi |
| Öneri kütüphanesi 2. aday | recbole.io | §17 aday genişlemesi |
| Ses formatı genişletme adayı | speex.org · mpg123.de · audioscrobbler.com | §13 format kapsamı |

**Başarısız/bilinçli elenenler (tur-3 — kayıt bu bölümde, önceki bölümlere dokunulmadı):**
`php.net/language.typedecls.php`, `php.net/language.exceptions.php` ve
`github.com/psf/secret-scanning` HEAD **başarısız** (silinmedi, bu satır kaydıttır);
secret-scanning yerine §12'de zaten doğrulanmış
`docs.github.com/en/code-security/secret-scanning` kullanıldı.

**Kapsam sınırı:** 12 URL yalnızca `HEAD 200` ile kapı **erişilebilirliği** doğrulanmıştır;
sürüm numaraları, özellik listeleri ve "hangi sürümde eklendi" iddiaları **okunmamıştır** →
`⚠️ VERIFICATION REQUIRED`.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §20 Üçüncü Tur Kapıları - veri servisi & güvenlik verisi kapısı doğrulaması (2026-10-13)

§19'daki 12 kapı dışında kalan tarama kapıları da HEAD ile doğrulandı:

| # | URL | Sorgu | Erişim |
|---|---|---|---|
| 1 | https://listenbrainz.org/ | Last.fm alternatifi açık veri kapısı (§4/L4 çapraz doğrulama) | HEAD 200 · 2026-10-13 |
| 2 | https://api.deezer.com/ | müzik katalog API kapısı (§4/L4) | HEAD 200 · 2026-10-13 |
| 3 | https://coverartarchive.org/ | albüm görsel arşivi kapısı (§26 veri kaynağı) | HEAD 200 · 2026-10-13 |
| 4 | https://nvd.nist.gov/ | güvenlik taviz verisi kapısı (§11 güvenlik) | HEAD 200 · 2026-10-13 |
| 5 | https://osv.dev/ | açık kaynak güvenlik veri kapısı (§11 güvenlik) | HEAD 200 · 2026-10-13 |
| 6 | https://github.com/advisories | GitHub güvenlik danışmanlığı kapısı (§11 güvenlik) | HEAD 200 · 2026-10-13 |
| 7 | https://deps.dev/ | bağımlılık analiz kapısı (§17 bağımlılık) | HEAD 200 · 2026-10-13 |
| 8 | https://www.rfc-editor.org/rfc/rfc8259 | JSON RFC (RFC 7159'nun yerini alan belge — §12'deki 7159 ile birlikte okunur) | HEAD 200 · 2026-10-13 |
| 9 | https://www.rfc-editor.org/rfc/rfc3629 | UTF-8 tanımı (§9 karakter zemini) | HEAD 200 · 2026-10-13 |
| 10 | https://www.rfc-editor.org/rfc/rfc5646 | dil etiketi (BCP 47) — §9 terim/yerelleştirme zemini | HEAD 200 · 2026-10-13 |

**Çakışma notu (dürüstlük):** §12'de `rfc7159` kayıtlıdır; `rfc8259` **onu güncelleyen**
dokümantdır — iki kapı birlikte okunmadan "hangisi geçerli" **sentence'i verilmez**;
`⚠️ VERIFICATION REQUIRED` olarak işaretlidir.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §21 Dördüncü Tarama Turu — Faz-1 veri kaynakları (2026-10-14)

> §16 P3/P5 ve yeni onaylanan **hibrit veri stratejisi (K-13)** gereği çalıştırıldı.
> Yöntem farkı: bu turda HEAD değil **GET ile içerik okundu** (webfetch, markdown) —
> iddialar sayfa metninden **birebir** çıkarılmıştır (§18 tur-2/3 HEAD taramasından farklı yöntem).

| # | URL | Çıkarılan iddia (sayfa metni — 2026-10-14) | Faz-1 bağlamı | K006.? |
|---|---|---|---|---|
| D1 | https://www.last.fm/api | "Last.fm Music Discovery API" — `track.getSimilar` · `user.getRecentTracks` · `user.getTopTracks` · `track.scrobble` metotları sayfada listeli; **"Commercial or Research Usage … contact us prior to use via email at partners@last.fm"** | etkileşim tohumu + benzerlik verisi | .8 · .7 |
| D2 | http://millionsongdataset.com/ | "freely-available collection of audio features and metadata for a million … tracks"; **"The dataset does not include any audio, only the derived features"**; tam set **280 GB**, 10.000 şarkılık alt set **1.8 GB**; alt kümeler: **Last.fm dataset** (song-level tags and similarity) · **Taste Profile subset** (user data) | tohum öznitelik + etkileşim alt kümesi | .6 · .8 |
| D3 | https://musicbrainz.org/doc/MusicBrainz_Database/Schema | PostgreSQL şeması; "current version of the schema is **v31 (released Q2 2026)**"; birincil varlıklar artist/recording/release/release group/work + MBID; **"`*_rating_raw` ve `*_tag_raw` tabloları … for privacy reasons … aren't included in the database dumps"** | katalog meta verisi (K007 sınırı) | .6 |

### §21.1 Dürüstlük & sınır notları (tur-4)

| # | Gözlem | Etki |
|---|---|---|
| O1 | Last.fm API ticari kullanımda **önceden izin** istiyor (sayfa metni) | CoreMusic ticariyse veri ithalatı öncesi izin gerekli → hukuki değerlendirme **bu dosyanın kapsamı dışındadır**, `yapay-zeka-riskler.md` R-03/uyum satırına bağlanır |
| O2 | MSD **ses içermiyor** — yalnız türetilmiş öznitelik | BPM/ton doğruluğu için MSD tek başına yeterli değil; ground-truth ses seti Faz-2'de **ayrı** toplanır |
| O3 | MusicBrainz dump'ları `rating_raw`/`tag_raw` **içermiyor** (gizlilik) | MusicBrainz'ten kullanıcı-bazlı veri ithalatı **yolu yok**; yalnız katalog meta verisi alınabilir |
| O4 | Taste Profile (MSD) Last.fm etkileşim verisi içerir | CF tohumu için adaydır; lisans koşulu **okunmadı** → `⚠️ VERIFICATION REQUIRED` |

**Kesişim kontrolü:** D1–D3, §2 (32 URL) · §11–§13 · §17 · §19–§20 envanterleriyle
karşılaştırıldı; **hiçbiri daha önce kullanılmamıştır** (§20/10 `audioscrobbler.com` Last.fm
şirket kapısıdır, `last.fm/api` ile aynı URL değildir).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.** (3 yeni URL eklendi: toplam benzersiz URL
> 32 + 14 (§17) + 12 (§19) + 10 (§20) + 3 (bu bölüm) — sayım `yapay-zeka-dogrulama.md`
> §2 matrisine bu turdan sonra taşınır.)

---

`status: verified` · `validation: 2026-10-12 · 32 URL + 5 çelişki kaydı + 9/9 çocuk çapraz bağ · 2026-10-14 · tur-4 (GET içerik) 3 veri kaynağı + 4 sınır notu eklendi`
