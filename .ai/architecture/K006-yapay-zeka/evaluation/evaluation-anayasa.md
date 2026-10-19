---
node_id: K006-yapay-zeka/evaluation/evaluation-anayasa
node_type: module
status: active
parent_id: K006-yapay-zeka/evaluation
children:
  - K006-yapay-zeka/evaluation/evaluation-kapsam
  - K006-yapay-zeka/evaluation/evaluation-bilesenler
  - K006-yapay-zeka/evaluation/evaluation-dogrulama
  - K006-yapay-zeka/evaluation/evaluation-ornekler
  - K006-yapay-zeka/evaluation/evaluation-web-arastirma
depends_on:
  - K006-yapay-zeka/model-management/model-management-anayasa
  - K006-yapay-zeka/inference/inference-anayasa
  - K006-yapay-zeka/testing/testing-anayasa
evidence:
  - "Dosya: .ai/architecture/K006-yapay-zeka/index.md [Satır: 266]"
  - "Dosya: .ai/projects/NevaEngine/legacy-2026-07/ai-models.md [Satır: 577]"
  - "Dosya: .ai/projects/NevaEngine/legacy-2026-07/ai-models.md [Satır: 711]"
validation: "İçerik doğrulama: 2026-10-15 — anayasa, .ai/research/ 2026-10-10 incelemesi ve ai-models.md §9/§13 kanıtlarıyla eşleştirildi"
open_questions:
  - "LLM çıktı kalitesi değerlendirmesinde insan-değerlendirici (human eval) maliyeti UNKNOWN — bütçe kararı bekliyor"
  - "A/B test altyapısı production'da var mı? (K006 dışı kod yüzeyi — VERIFICATION REQUIRED)"
group: mimari
---
# Evaluation — Alt Dal Anayasası (kademe-2)

> **Bu dosya kademe-2 anayasadır.** Ebeveyn: `K006-yapay-zeka` (bkz. `index.md` §2.6 çocuk haritası — v2 dosyası `G:\` arşivinde, bu repo'da değil).
> Kardeş anayasalar: `../model-management/model-management-anayasa.md`, `../inference/inference-anayasa.md`, `../recommendation/recommendation-anayasa.md`, `../testing/testing-anayasa.md`.
> Bu dosya **şablon içermektedir** (`<...>` yer tutucu yoktur — alt-dal kapsamında somut metin kullanılmıştır).

---

## §1. Kapsam Sınırı

**evaluation** alt dalı, K006'da **yapay zeka bileşenlerinin kalitesini ölçme ve doğrulama** disiplinini kapsar. Kapsayıcılar:

1. **Model metrikleri** — doğruluk, precision/recall/F1, RMSE, perplexity, kalite skorları (ai-models.md §9.1, satır 577).
2. **Sistem metrikleri** — latency (TTFT, tok/s), throughput, bellek, maliyet (ai-models.md §13, satır 711).
3. **Değerlendirme yöntemleri** — benchmark, offline değerlendirme, A/B test, insan-değerlendirici, LLM-as-a-judge.
4. **Regresyon kapısı** — her release'te kalitenin düşmediğini kanıtlayan zorunlu testler.

**Kapsam dışı (evaluation DEĞİL):**

- Model eğitimi / fine-tune → `model-management`
- Canlı servis davranışı, circuit breaker → `inference`
- Test altyapısı, mock, CI → `testing` (evaluation yalnızca *ölçüm* tanımlar, *altyapıyı* kurmaz)
- İş zekâsı / ürün metrikleri (DAU, dönüşüm) → K006 dışı domain

**Kapsam sınırı testi:** "Bu çalışma kaliteyi *sayıya* döküyor mu?" → Evet = evaluation. "Kaliteyi *geliştiriyor* mu?" → model-management. "Ölçümü *otomatikleştiriyor* mu?" → testing.

---

## §2. İlke ve Değerler

### §2.1 Öncelik sırası

| # | İlke | Gerekçe |
|---|------|---------|
| 1 | **Ölçüm yoksa kalite yoktur** | Ölçülmeyen iyileşme kanıt değildir (ai-models.md §9.1, satır 577) |
| 2 | **İyi bilinen veri setleri** | Tanınmış benchmark (MMLU, HumanEval, GLUE, LibriSpeech) güvenilir referanstır |
| 3 | **Prod temsili veri** | Ölçüm seti üretim trafik dağılımını yansıtmalıdır — laboratuvar verisi yanıltır |
| 4 | **Baseline olmadan karşılaştırma yoktur** | Yeni model ancak eski modelle (veya rastgele/üstel) kıyaslanabilir |
| 5 | **Statistiksel anlamlılık** | Örneklem sayısı yetersizse fark "iyileşme" değildir — noise'tur |

### §2.2 Zero-hallucination bağlayıcılığı

Evaluation outputs **fiyatlandırma, release onayı ve maliyet tahmini** için kullanılır; bu yüzden:

1. Her metrik değeri **ölçüm koşuluyla** birlikte raporlanır (model, dataset, donanım, tarih).
2. Ölçülemeyen metrik `UNKNOWN` yazılır — tahmini sayı **asla** rapora girmez.
3. Üçüncü taraf benchmark iddiaları (model kartı, blog) `⚠️ VERIFICATION REQUIRED` ile işaretlenir; **repo içinde yeniden ölçülmeden kesin sayılmaz**.
4. Eski ölçüm eski koşulda geçerlidir — koşul değişince (donanım, quantization, prompt) ölçüm **tekrarlanır**.

---

## §3. Mimari Roller ve Kararlar

### §3.1 Değerlendirme katmanları

```
┌─────────────────────────────────────────────────────────────┐
│ KATMAN 4 — CANLI DEĞERLENDİRME (shadow / A/B)              │
│   • production trafikte twin çağrı, canlı etiket toplama   │
├─────────────────────────────────────────────────────────────┤
│ KATMAN 3 — İNSAN / JUDGE DEĞERLENDİRMESİ                    │
│   • rubrik bazlı insan puanı, LLM-as-a-judge, pairwise     │
├─────────────────────────────────────────────────────────────┤
│ KATMAN 2 — OFFLINE METRİK                                    │
│   • accuracy/F1/RMSE, embedding doğruluğu, anormallik F1    │
├─────────────────────────────────────────────────────────────┤
│ KATMAN 1 — SİSTEM / SERVİS METRİKLERİ                        │
│   • TTFT, tok/s, latency p50/p95/p99, bellek, maliyet       │
└─────────────────────────────────────────────────────────────┘
```

**Karar (K-EVAL-01):** Her AI bileşeni için **en az Katman 1 + Katman 2** zorunludur; metin üretimi (LLM) bileşenleri için Katman 3 de zorunludur.
**Kanıt:** ai-models.md §9.1 (satır 577) ölçüm metodolojisini tanımlar; §13 (satır 711) sistematik benchmark sunar.

### §3.2 Sahiplik matrisi

| Alan | Sorumluluk | Not |
|------|-----------|-----|
| Metrik tanımı | evaluation | Hangi bileşen için hangi metrik — §4 |
| Ölçüm altyapısı | testing | Harness, CI entegrasyonu, rapor üretimi |
| Model seçimi eşiği | model-management | quality gate eşikleri (bu dosya §16 kapı matrisi) |
| Canary devir eşiği | inference | canary_accuracy_drop (K006 §2.2) |

**Karar (K-EVAL-02):** evaluation *tanımlar*, testing *koşturur*, model-management *eşik uygular*. Üçü tek dosyada birleşmez.
**Reddedilen alternatif:** Tek "eval + test" modülü — metrik ve altyapı sorumluluğu karışır, sahiplik belirsizleşir.

---

## §4. Bileşen Bazlı Metrik Kataloğu

### §4.1 Model-özel metrikler

| Bileşen | Birincil metrik | İkincil metrik | Kaynak |
|---------|----------------|----------------|--------|
| MusicBERT (genre, 8/8 doğru) | accuracy | macro-F1 | ai-models.md §5.1, satır 265 |
| Lyrics embedding | retrieval accuracy@k | MRR | ai-models.md §6 |
| Anormallik tespiti | F1 | precision @ %0.1 FP | ai-models.md §7.1 |
| Recommender (map@k) | MAP@10 | NDCG@10 | ai-models.md §9.1 |
| TTS (STOI/PESQ) | STOI | PESQ | ai-models.md §10 |
| LLM çıktı (prompt) | rubrik skoru | pairwise kazanma oranı | ai-models.md §9.1, satır 577 |
| ASR (WER) | WER | CER | ai-models.md §11 |

### §4.2 Sistem metrikleri (her model için zorunlu)

| Metrik | Tanım | Kaynak |
|--------|-------|--------|
| TTFT | token üretimine kadar geçen süre | ai-models.md §13, satır 711 |
| tok/s | saniye başına token (throughput) | ai-models.md §13, satır 711 |
| Latency p50/p95/p99 | uçtan uca gecikme percentilleri | audio-core.md §11.1 (satır 387) referans |
| Bellek (RSS) | model + çalışma belleği | ai-models.md §4.1, satır 208 |
| Maliyet | 1K çağrı başına maliyet | ai-models.md §13 |

**Karar (K-EVAL-03):** **Latency daima percentil ile raporlanır** (p50/p95/p99) — ortalama yanıltıcıdır; tail latency gerçek kullanıcı deneyimidir.
**Kanıt:** audio-core.md §11 (latency bütçesi, satır 387) percentil kullanımı gerektirir.

---

## §5. Değerlendirme Yöntemleri

### §5.1 Offline (release öncesi)

| Yöntem | Ne ölçer | Ne zaman |
|--------|----------|----------|
| Benchmark seti | bilinen cevaplı doğruluk | her release |
| Regression suite | önceki sürümün geçtiği durumlar | her PR |
| Golden set | domain'e özgü el-puanlı örnekler | model değişikliğinde |
| Stress / soak | uzun süreli bozulma (drift) | periyodik |

**Karar (K-EVAL-04):** Release öncesi **golden set zorunlu** — genel benchmark domain'e özgü kaliteyi yakalamaz (şarkı sözü dili, Türkçe içerik, müzik terminolojisi).

### §5.2 Online (release sonrası)

| Yöntem | Ne ölçer | Risk kontrolü |
|--------|----------|---------------|
| Shadow mode | gerçek trafikte çıktı kalitesi | kullanıcıya yansımaz |
| A/B test | kullanıcı-özel metrik (CTR, dinleme süresi) | küçük yüzdelerle başla |
| Canary | sistem metrikleri + hata oranı | canary_accuracy_drop (K006 §2.2) |
| Drift monitoring | veri/girdi dağılımında kayma | feature drift, prediction drift (K006 §2.1) |

**Karar (K-EVAL-05):** Yeni **model sürümü** production'a doğrudan girmez — önce shadow, sonra canary, sonra A/B (rollout politikası `inference` anayasasının; **eşikler** evaluation'ın).

### §5.3 LLM değerlendirmesi (özel durum)

LLM çıktıları klasik accuracy ile ölçülemez. Kabuk:

1. **Rubrik** — her çıktı için 1–5 ölçekli açık kriterler (doğruluk, tutarlılık, format, dil).
2. **LLM-as-a-judge** — güçlü model puanlar; insan örneklemiyle **calibration** (judge-human agreement ölçülür, agreement < 0.7 ise judge güvenilmez).
3. **Pairwise** — eski vs yeni, kazanma/beraberlik/kaybetme; Bradley-Terry ile sıralama.
4. **İnsan örneklemi** — her release'te rastgele N örnek bağımsız puanlanır.

**Karar (K-EVAL-06):** LLM judge **asla** kalibrasyonsuz kullanılmaz — judge yanlılığı (uzun cevap, format tercihi) ölçülmeden kabul edilmez.
**Kanıt:** ai-models.md §9.1 (satır 577) değerlendirme yöntemlerini tanımlar; judge kalibrasyonu bu çerçevenin parçasıdır.

---

## §6. Veri ve Ölçüm Kümesi Disiplini

### §6.1 Küme bölünmesi

| Küme | Amaç | Kural |
|------|------|-------|
| Train | öğrenme | evaluation asla train'e bakmaz |
| Validation | hiperparametre / model seçimi | tekrar tekrar kullanılabilir → **overfit riski** |
| Test (held-out) | nihai rapor | **release başına 1 kez** açılır |
| Golden set | domain spesifik | el yapımı, sürüm kontrolünde |

**Karar (K-EVAL-07):** Held-out test kümesi **rapor için saklanır** — validation'da en iyi görünen model held-out'ta da en iyisi olmak *zorunda* değildir; held-out sonucu **release kapısıdır**.

### §6.2 Üretim temsiliyet kontrolü

1. Ölçüm kümesi üretim trafik dağılımını **ölçer** (dil, tür, uzunluk, cihaz).
2. Sapma varsa ağırlıklandırma (weighting) veya yeniden örnekleme yapılır.
3. Sapma giderilemiyorsa rapor **"laboratuvar koşulu"** etiketiyle sunulur — kesin sayılmaz.

**Zero-hallucination uyarısı:** "Temsilî" iddiası **sayısal kanıtla** (dağılım karşılaştırması) desteklenir; desteklenmiyorsa `⚠️ VERIFICATION REQUIRED`.

---

## §7. Rehberler (Guidelines)

1. **Tek dosyada tek metrik tanımı** — aynı metrik iki yerde farklı tanımlanmaz (tanım drift'i).
2. **Baseline her raporda görünür** — mutlak sayı tek başına anlam ifade etmez.
3. **Eşik değerleri ADR ile sabitlenir** — keyfi eşik (ağızdan "90% iyi olsun") yasak.
4. **Otomatik rapor** — değerlendirme elle toplanan Excel değildir; test altyapısı (`testing`) üretir.
5. **Rapor sürümü** — her ölçüm model sürümü + veri sürümü + tarih ile damgalanır.
6. **Negatif sonuç da sonuçtur** — kötüleşen metrik gizlenmez, release **durdurulur** (dürüstlük).
7. **Puanlama blind** — insan değerlendirmede puanlayan, hangi modelin çıktısı olduğunu **görmez** (bias kontrolü).

---

## §8. Zorunlu Kurallar (MUST / MUST NOT)

| # | Kural | Seviye |
|---|-------|--------|
| E1 | Her AI bileşeninin **en az bir** birincil kalite metriği tanımlı olmalıdır | MUST |
| E2 | Latency **percentil** ile raporlanmalıdır (ortalama tek başına yasak) | MUST |
| E3 | Release öncesi **held-out test** çalıştırılmalı ve eşik geçilmelidir | MUST |
| E4 | Ölçüm değeri **koşullarıyla** (model, dataset, donanım, tarih) raporlanmalıdır | MUST |
| E5 | Ölçülemeyen metrik **`UNKNOWN`** yazılmalıdır — tahmini sayı yazılmaz | MUST |
| E6 | Üçüncü-party benchmark iddiası repo'da **yeniden ölçülmeden** kesin sayılmaz | MUST |
| E7 | Yeni model **doğrudan** %100 traffic'e alınmamalıdır (shadow → canary → A/B) | MUST |
| E8 | held-out test kümesi **validation** amacıyla kullanılmamalıdır | MUST NOT |
| E9 | Puanlayan model kimliğini **blind** koşulda görmemelidir (insan/judge) | MUST |
| E10 | Eşik değeri **ADR/kanıt** olmadan değiştirilmemelidir | MUST NOT |
| E11 | Rapor **eksen/sapma** içeriyorsa rapor yayımlanmamalıdır (yanlış ölçüm > hiç ölçüm değil) | MUST |

---

## §9. Karar Kaydı

| Karar | İçerik | Kanıt |
|-------|--------|-------|
| K-EVAL-01 | Katman 1+2 zorunlu; LLM'de Katman 3 de zorunlu | ai-models.md §9.1, satır 577 |
| K-EVAL-02 | evaluation tanımlar / testing koşturur / model-management eşik uygular | bu dosya §3.2 |
| K-EVAL-03 | Latency percentil (p50/p95/p99) | audio-core.md §11.1, satır 387 |
| K-EVAL-04 | Golden set release öncesi zorunlu | ai-models.md §6 |
| K-EVAL-05 | Model rollout: shadow → canary → A/B; eşikler evaluation'da | K006 §2.2 (index.md) |
| K-EVAL-06 | LLM judge kalibrasyonsuz kullanılmaz | ai-models.md §9.1, satır 577 |
| K-EVAL-07 | Held-out test release kapısıdır | bu dosya §6.1 |

**Reddedilen alternatifler:**

- *Yalnız otomatik metrik* → insan/judge katmanı olmadan LLM kalitesi ölçülemez (K-EVAL-01).
- *Release sonrası tek değerlendirme* → hatalı model ancak canlıda yakalanır, geri dönüş maliyeti yüksek (K-EVAL-05).
- *Tek genel benchmark* → domain spesifik kalite (müzik, Türkçe) ölçülmez (K-EVAL-04).

---

## §10. Bağımlılıklar

### §10.1 Bağımlı olduğu alanlar

| Alan | Ne alır | Kaynak |
|------|---------|--------|
| `testing` | ölçüm altyapısı, CI kapısı, rapor üretimi | `../testing/` |
| `model-management` | model katalog, sürüm, quality gate | `../model-management/` |
| `inference` | canlı trafik, shadow/canary kancaları, sistem metrikleri | `../inference/` |
| `recommendation` | üretim etiketleri (dinleme, tıklama) | `../recommendation/` |

### §10.2 Bağımlı olan alanlar

| Alan | Ne verir |
|------|----------|
| `model-management` | release kapısı sonuçları (geç/çek) |
| `inference` | canary devir kararı (drop eşiği) |
| `governance-risk` (K006 kökü) | bias, drift, denetim raporları (K006 §2.1) |

---

## §11. İlişkili Dosyalar

### §11.1 Bu dalın kardeş dosyaları

| Dosya | Rol | Özet |
|-------|-----|------|
| `index.md` | dal indeksi | §GRUPLAR + çocuk haritası |
| `evaluation-kapsam.md` | kapsam | ne ölçülür, ne ölçülmez |
| `evaluation-bilesenler.md` | bileşenler | metrik katalog, katmanlar |
| `evaluation-dogrulama.md` | doğrulama | ölçüm doğruluğu, tekrarlanabilirlik |
| `evaluation-ornekler.md` | örnekler | ölçüm/rapor örnek akışları |
| `evaluation-web-arastirma.md` | web araştırma | resmi kaynak/standart taraması |
| `../yapay-zeka-kararlar.md` | kök K006 kararları | bu dal kararlarının üst otoritesi |
| `../yapay-zeka-riskler.md` | kök K006 riskleri | metric gaming/judge bias buraya işlenir |

### §11.2 Vault referansları

| Dosya | Ne verir | Satır |
|-------|----------|-------|
| `.ai/architecture/K006-yapay-zeka/index.md` | çocuk haritası (v2, §2.6) | 266 |
| `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | §9.1 ölçüm metodolojisi | 577 |
| `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | §13 benchmark | 711 |
| `.ai/projects/NevaEngine/legacy-2026-07/audio-core.md` | §11 latency bütçesi | 387 |
| `.ai/architecture/K006-yapay-zeka/recommendation/recommendation-anayasa.md` | kardeş anayasa şablonu | 1 |

**Seçim kuralı:** metrik tanımı → §4; yöntem → §5; eşik/karar → bu dosya §9–§11 (K-EVAL-*); rapor başlıkları → §25; ölçüm doğruluğu → `evaluation-dogrulama.md`; gerçek ölçüm örnekleri → `evaluation-ornekler.md`.

---

## §12. Açık Sorular (open_questions detayı)

1. **Human eval bütçesi** — her release'te insan puanlama maliyeti ve sıklığı bilinmiyor (`UNKNOWN`); LLM-judge ile ikame oranı tartışma konusu.
2. **A/B altyapısı** — production'da A/B test sistemi var mı, yoksa yalnız shadow/canary mi? K006 dışı kod yüzeyi → `⚠️ VERIFICATION REQUIRED`.
3. **Judge modelleri** — LLM-as-a-judge için hangi modelin kullanılacağı (maliyet/güvenilirlik dengesi) kararı verilmedi.
4. **Bias değerlendirmesi** — K006 §2.1 bias tespiti için hangi adil-ölçüm (fairness metric) setinin zorunlu olacağı UNKNOWN.
5. **Drift eşikleri** — feature/prediction drift için istatistiksel yöntem (PSI, KL, KS) seçimi ve eşikleri belirlenmedi.

---

## §13. İlgili Lint Maddeleri

| LINT (`.ai/CLAUDE.md` §6) | Bu anayasadaki karşılığı |
|---------------------------|--------------------------|
| Zero-hallucination | §2.2, E5, E6 — ölçüm yoksa iddia yok |
| Kanıt zorunluluğu | §9 karar tablosu — her karar kaynak satırı taşır |
| `UNKNOWN` / `VERIFICATION REQUIRED` | E5, §12 |
| Eşik keyfiliği yasağı | E10 — eşik ADR/kanıt ister |
| Ölçüm percentil zorunluluğu | E2, K-EVAL-03 |

---

## §14. Bağlam ve Versiyon

| Alan | Değer |
|------|-------|
| Sahip | K006-yapay-zeka / evaluation |
| Ebeveyn | `K006-yapay-zeka` (kök `index.md` §2.6 — v2 dosyası `G:\` arşivinde) |
| Kademe | 2 (kök anayasa kademe-1 → alt dal anayasası kademe-2) |
| Durum | active |
| Oluşturulma | 2026-10-15 |
| Kapsanan sibling'lar | `evaluation-kapsam` · `evaluation-bilesenler` · `evaluation-dogrulama` · `evaluation-ornekler` · `evaluation-web-arastirma` · `index.md` |
| Grup | mimari (`group:` frontmatter) |
| Belge tipi | anayasa (kademe-2, bu dalın usul belgesi — şablon değil) |

---

## §15. Metrik Tanım Şeması (her metrik için zorunlu alanlar)

Her birincil metrik aşağıdaki şema ile tanımlanır — eksik alanla metrik tanımı **geçersizdir**:

| Alan | Zorunlu | Açıklama |
|------|---------|----------|
| `name` | Evet | kısa, tekrarlanabilir ad (ör. `map@10`) |
| `definition` | Evet | matematiksel/operasyonel tanım — belirsiz ifade yasak |
| `primary` | Evet | bu bileşenin release kapısı metriği (tanesi 1) |
| `secondary` | Hayır | tamamlayıcı metrikler (raporlanır, kapı değil) |
| `direction` | Evet | `higher_is_better` \| `lower_is_better` |
| `threshold` | Evet | eşik değeri + kaynağı (ADR satırı ya da baseline farkı) |
| `dataset` | Evet | ölçüm kümesi adı + sürümü |
| `slice` | Hayır | dil/ürün/cihaz dilimleri (ör. `lang=tr`) |
| `owner` | Evet | metriği yorumlayacak rol |
| `regression_action` | Evet | kapı ihlalinde ne yapılır: block / warn / rollback |

**Şema örneği (model kalitesi):**

```yaml
name: map@10
definition: "10 öneri içindeki ortalama precision@k'sinin ground-truth pozitiflerine göre ortalaması"
primary: true
direction: higher_is_better
threshold: "baseline - 0.02 (ADR-014)"
dataset: "reco-eval v3 (2026-09)"
slice: ["lang=tr", "platform=web"]
owner: "recommendation"
regression_action: "block"
```

**Karar (K-EVAL-08):** `threshold` alanı **kaynak içermezse** eşik sayılmaz — eşiğin değeri kaynakla birlikte yazılır (`UNKNOWN` eşik, eşik değildir).
**Neden:** Eşiksiz/​kaynaksız eşik, keyfi release kapısı demektir (bu dosya E10 ile tutarlı).

---

## §16. Bileşen Bazlı Kapı Matrisi (release gate matrix)

Her AI bileşeni release'te aşağıdaki kapıdan geçer. Bir bileşenin satırı **boş bırakılamaz** — "henüz yok" `UNKNOWN` olarak işaretlenir:

| Bileşen | Katman 1 | Katman 2 | Katman 3 | Kapı metriği | Dev action |
|---------|----------|----------|----------|--------------|------------|
| Genre clf (MusicBERT) | tok/s, bellek | accuracy | — | accuracy | block |
| Lyrics embedding | tok/s | acc@k, MRR | — | acc@5 | block |
| Anormallik tespiti | latency p95 | F1 | — | F1 | block |
| Recommender | tok/s, bellek | MAP@10 | — | MAP@10 | block |
| PromptEngine çıktısı | TTFT, tok/s | — | rubrik + judge | rubrik ort. | warn → block (0.5 gerilemede) |
| TTS (Neva) | latency bütçesi | STOI/PESQ | — | STOI | block |
| ASR | tok/s | WER | — | WER | block |
| Local-Inf (ONNX) | tok/s, bellek | göreli doğruluk | — | göreli doğruluk | block |

**Karar (K-EVAL-09):** Kapı metriği **birincil metriktir** (`primary: true`) — ikincil metrikler kötüleşse de release **block edilmez**, rapora düşer ve sahibi bilgilendirilir.
**İstisna:** Güvenlik/düzenleme ile ilişkili metrikler (ör. jailbreak/filtre) her zaman **block** seviyesindedir — istisna ADR ile kayıtlıdır.

---

## §17. İstatistiksel Anlamlılık Usulü

### §17.1 Minimum kurallar

1. **Örneklem boyutu** — A/B ve karşılaştırmalarda örneklem, **güç analizi** ile belirlenir; güç < 0.8 ise sonuç `İNANILMAZ` işaretlenir.
2. **Aynı koşul** — iki model karşılaştırması **aynı veri, aynı donanım, aynı kod** üzerinde yapılır; koşul farkı karşılaştırmayı geçersiz kılar.
3. **Çoklu test düzeltmesi** — birden fazla metrik test ediliyorsa Bonferroni/FDR uygulanır (yanlış pozitif kontrolü).
4. **Mutlak + göreli rapor** — hem mutlak fark hem göreli değişim yazılır; yalnızca göreli yüzde (ör. "%50 iyileşme" — taban küçükken) yanıltıcıdır.
5. **Güven aralığı** — nokta tahmin **yanına** %95 GA yazılır; GA'sız sayı rapora girmez.

### §17.2 Kaçınılan tuzaklar

| Tuzak | Kontrol |
|-------|---------|
| Best-of-n (birden çok denemeden en iyi) | her denemeyi raporla, ortalamayı kapı yap |
| Test kümesi sızıntısı (leakage) | held-out, train'den **hiç** görmediğinden emin ol |
| Prompt-tuning overfit | prompt, held-out'ta **tek sefer** denenir |
| Survivorship (sadece başarılı çağrılar) | tüm çağrılar (hata dahil) metriğe girer |
| Seasonality | A/B aynı takvim penceresinde paralel yürür |

**Karar (K-EVAL-10):** **Tek seferlik** held-out değerlendirmesi dışında held-out'a **bakıp prompt/model ayarı** yapılmaz — yapıldığı anda test kümesi kirli sayılır ve **yeni bir held-out** oluşturulur.
**Kanıt:** ai-models.md §9.1 (satır 577) ölçüm usulünü; bu dosya §6.1 ile birlikte yorumlanır.

---

## §18. Ölçüm Yaşam Döngüsü (Measurement Lifecycle)

```
[1] TANIM        → şema (§15) ile metrik + eşik + sahip kaydı
[2] HAZIRLIK     → küme bölünmesi (§6), temsiliyet kontrolü (§6.2)
[3] ÖLÇÜM        → offline (§5.1); koşul damgası (model/data/hw/tarih)
[4] DEĞERLENDİRME → KA → istatistik (§17) → gate sonucu (§16)
[5] RAPOR        → otomatik rapor (testing altyapısı) + dashboard
[6] GÖZLEM       → online: shadow/canary/A&B (§5.2) + drift (§5.2)
[7] REVİZYON     → eşik gözden geçirme (yıllık veya regresyonda), ADR
```

**Yaşam döngüsü kuralları:**

1. `[1]` tamamlanmadan `[3]` başlamaz — tanımsız metrik ölçülmez.
2. `[4]` sonucu **her zaman** saklanır (başarısız ölçüm de kayıttır).
3. `[6]`'da alert → `[7]`'ye ADR girişi; eşiği changed ise eski raporlar **etiketlenir** (eski eşikle ölçüldü).
4. `[7]` yalnız ADR ile değişir — sohbet/Slack kararı eşik değiştirmez.

---

## §19. Doğrulama ve Tekrarlanabilirlik (Verification Mandate)

### §19.1 Tekrarlanabilirlik zorunluluğu

Her rapor, üçüncü bir kişi tarafından **aynı komutla** yeniden üretilebilir olmalıdır:

1. **Ortam sabitleme** — model sürümü, veri sürümü, kod sürümü (commit), donanım, bağımlılık sürümleri raporun başında listelenir.
2. **Determinism** — olası yerlerde `seed` sabitlenir; deterministik olamayan bileşenler "seed ile deterministik değildir" notuyla raporlanır.
3. **Kanıt dosyası** — ham çıktılar (log, prediction dosyası, judge yanıtları) raporun yanında saklanır; yalnız özet **kanıt değildir**.
4. **İki-kişi kuralı** — release kapısı raporu bağımsız **ikinci kişi** tarafından imzalanır (tek kişi onayı yeterli değildir).

### §19.2 Kalibrasyon kontrolleri (periodic)

| Kontrol | Sıklık | Aksiyon |
|---------|--------|---------|
| Judge ↔ insan agreement | her release | agreement < 0.7 → judge yeniden kalibre (K-EVAL-06) |
| Metrik ↔ insan algısı | 3 ayda bir | metrik gelişirken insan puanı sabitse metrik gaming şüphesi |
| Test kümesi temizliği | model değişiminde | leakage testi (K-EVAL-10) |
| Ölçüm altyapısı drift'i | 6 ayda bir | harness'ın doğru ölçtüğü doğrulanır |

**Karar (K-EVAL-11):** Ölçüm altyapısının **kendisi** de test edilir — ölçen araç bozuksa tüm metrikler bozuktur.
**Reddedilen alternatif:** "Araç sağlam, sonuca bakarız" — araç kaynaklı ölçüm hatası sistematiktir, fark edilmez.

---

## §20. Performans Bütçesi ve Optimizasyon Ölçümü

### §20.1 Latency bütçesi (ölçüm tarafı)

| Bileşen | Ölçüm noktası | Referans |
|---------|---------------|----------|
| Inference (remote) | TTFT + tok/s | ai-models.md §13 (satır 711) |
| Inference (local/ONNX) | tok/s + bellek tepe | ai-models.md §4.1 (satır 208) |
| TTS (Neva) | üretim latency (frame bütçesi) | audio-core.md §11.1 (satır 387) |
| Anomali job | job başına süre + kuyruk bekleme | ai-models.md §7.2 (satır 434) |

### §20.2 Optimizasyon doğrulama kuralı

1. Her optimizasyon (quantization, batching, cache) **öncesi/sonrası** metrik çifti ile raporlanır — **yalnız "sonra" sayısı kabul edilmez**.
2. Kalite metriği (accuracy/F1) ile hız metriği **aynı raporda** bulunur — hız kazancı kalite kaybı olmadan sayılır.
3. Quantization sonrası **gerçek cihazda** ölçüm zorunlu (masaüstü rakamı mobile/edge için geçersizdir).
4. Kırılım noktası (breaking point) ölçülür — optimal yapı noktası belirsizse `UNKNOWN` yazılır, "iyi" diye geçilmez.

**Karar (K-EVAL-12):** Optimizasyon raporu **iki metrikli** olur (hız + kalite); tek metrikli rapor release kapısından **geçemez**.

---

## §21. Sorumluluk ve Rota (Escalation)

### §21.1 Rota tablosu

| Durum | İlk muhatap | Escalation |
|-------|-------------|------------|
| Kapı metriği geriledi (block) | bileşen sahibi | release **durdurulur**; 3 başarısız turda üst karar |
| Judge agreement düşük | evaluation | model/prompt yeniden kalibre → tekrar ölç |
| Ölçüm tekrarlanamıyor | testing + evaluation | ölçüm askıya alınır; rapor `VERIFICATION REQUIRED` |
| Drift alert | inference + evaluation | canary geri alma (K006 §2.2) |
| Bias şüphesi | evaluation + K006 §2.1 | bias değerlendirmesi başlatılır, yayın durdurulabilir |

### §21.2 Karar hakkının dağılımı

| Karar | Hakkı olan |
|-------|-----------|
| Metrik tanımı / eşik (§15, E10) | evaluation + ADR |
| Release kapısı sonucu | testing raporu → release sahibi |
| Canary geri alma | inference (eşik evaluation'da) |
| Judge modeli seçimi | evaluation (§12 açık soru 3) |

---

## §22. Kritik Alan Uyarıları (Critical Surface)

### §22.1 Metric gaming savunması

1. Tek metriğe **indirgenmiş** ödüller yasak (Goodhart: "ölçülen hedef, ölçülme hedefi olur").
2. Her kapı için **en az 1 ikincil metrik** izlenir — birincil yükselirken ikincil düşüyorsa alarm.
3. Puanlama **blind** (E9) ve rastgele sıralama (K-EVAL-06).
4. Judge prompt'u **değiştirilmez** release sırasında (judge-drift ölçümü ayrı yapılır).

### §22.2 Veri ve mahremiyet

1. Değerlendirme verisi PII içeriyorsa **anonymize + minimal** (yalnız ölçüm gerektiren alanlar).
2. Puanlama örnekleri **log/rapor**'a olduğu gibi **kopyalanmaz** — özet istatistik yazılır.
3. Üçüncü-party benchmark'a veri **dışarı gönderilmez** (lisans + gizlilik kontrolü).

**Karar (K-EVAL-13):** Ham değerlendirme verisi **vault dışına** (sözleşmeli harici araç) varsayılan olarak **çıkmaz**; dışarı çıkacaksa lisans + gizlilik onayı ADR ile kaydedilir.

---

## §23. Entegrasyon ve Uç Noktalar (Integration Surface)

### §23.1 Giriş (evaluation ne alır)

| Kaynak | Veri | Nereden |
|--------|------|---------|
| `testing` | harness, CI tetikleyici, ham sonuçlar | `../testing/` |
| `model-management` | model katalog + sürümleri | `../model-management/` |
| `inference` | canlı metrik (TTFT, hata, canary) | `../inference/` |
| `recommendation` | tıklama/dinleme etiketleri | `../recommendation/` |
| `.ai/research/` | yöntem incelemeleri | `.ai/research/2026-10-10-*.md` |

### §23.2 Çıkış (evaluation ne verir)

| Çıktı | Alıcı | Biçim |
|-------|-------|-------|
| Kapı sonucu (geç/çek) | release sahibi | rapor |
| Eşik değişikliği önerisi | ADR (`.ai/.decisions/`) | ADR taslağı |
| Canary devir kararı | inference | drop eşiği |
| Bias/drift bulguları | K006 §2.1 | bulgu kaydı |
| Dashboard metrikleri | operasyon | otomatik rapor |

**Uç nokta sözleşmesi:** `[2] hazırlık` çıktısı (küme + sürüm damgası) `[3] ölçüm`'ün **tek girdisidir** — ölçüm, hazırlıksız çalıştırılamaz (E: schema §15 zorunlu).

---

## §24. Anti-Patternler (yasak kısayollar)

| Anti-pattern | Neden zararlı | Doğrusu |
|--------------|---------------|---------|
| "Ortalama yeterli" | tail latency saklanır | percentil (E2) |
| "Benchmark model kartından" | koşul farklı, doğrulanmamış | repo'da ölç (E6) |
| "Validation'da en iyi" | overfit | held-out release kapısı (K-EVAL-07) |
| "Tek metrikle geçtik" | Goodhart, yan metrik bozulur | ikincil metrik izleme (§22.1) |
| "Judge puanı kesin" | judge yanlılığı | blind + kalibrasyon (E9, K-EVAL-06) |
| "Elle Excel raporu" | tekrarlanamaz, hatalı | otomatik rapor (Rehber 4) |
| "Ölçüm yok, kod iyi görünüyor" | kanıtsız | ölç yoksa iddia yok (§2.2) |
| "Hızlandırdık %50" | kayıpsız olduğu **ispatlanmadı** | iki metrikli rapor (K-EVAL-12) |
| "Test kümesine tekrar baktık" | kirli held-out | yeni küme (K-EVAL-10) |
| "Eşik sözlü karar" | keyfi kapı | ADR + kaynak (E10) |

---

## §25. Ölçüm Raporu Şablonu (zorunlu başlıklar)

```markdown
# <bileşen> — <sürüm> değerlendirme raporu
1. Koşul damgası: model / data / code(commit) / hardware / tarih
2. Küme: ad + sürüm + boyut + bölünme (train/val/held-out/golden)
3. Temsiliyet: üretim dağılımıyla sapma (varsa ağırlıklandırma)
4. Birincil metrik: tanım + eşiği + sonucu + GA (%95)
5. İkincil metrikler: tablo (yön + değişim)
6. İstatistik: güç/örneklem + düzeltme (§17)
7. Katman 3 (LLM): rubrik + judge/human agreement (§5.3)
8. Sistem: TTFT / tok/s / percentil latency / bellek / maliyet
9. Gate sonucu: BLOCK / WARN / PASS + sahip imzası (§19.1)
10. Kanıt: ham dosya referansları (log, prediction)
11. Açık sorular / NOT-DONE: §12 maddelerinin durumu
```

**Zorunlu kural:** Başlık 1 **boş olamaz**; koşul damgasız rapor yayımlanmaz (bu dosya §2.2.1).

---

## §26. Sürüm Geçmişi ve Değişiklik Kontrolü

| Alan | Kural |
|------|-------|
| Anayasa revizyonu | yalnız ekleme/ADR ile — çelişki varsa üst otorite `.ai/CLAUDE.md` §2 |
| Eşik değişikliği | ADR zorunlu; eski raporlar "eski eşik" etiketi (§18.7) |
| Metrik kaldırma | yasak (append-only) — `deprecated` işaretlenir |
| Yeni metrik | §15 şemasıyla + sahip + kapı kararı |
| Bağımlılık anayasa değişikliği | `depends_on` güncellenir + bu dosya §10 gözden geçirilir |

**Sürüm notu:** Bu anayasa, kök `index.md` §2.6 çocuk haritasındaki `evaluation` satırı (satır 266) ile bağlanır; kardeş `*-anayasa.md` dosyalarıyla **aynı kademe-2** statüsündedir ve çelişkide kök anayasa (kademe-1) kazanır.
