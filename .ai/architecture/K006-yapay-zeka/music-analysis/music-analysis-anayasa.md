---
node_id: K006-yapay-zeka/music-analysis/music-analysis-anayasa
node_type: module
status: active
parent_id: K006-yapay-zeka/music-analysis
children:
  - K006-yapay-zeka/music-analysis/music-analysis-kapsam
  - K006-yapay-zeka/music-analysis/music-analysis-bilesenler
  - K006-yapay-zeka/music-analysis/music-analysis-dogrulama
  - K006-yapay-zeka/music-analysis/music-analysis-ornekler
  - K006-yapay-zeka/music-analysis/music-analysis-web-arastirma
depends_on:
  - K006-yapay-zeka/inference/inference-anayasa
  - K006-yapay-zeka/evaluation/evaluation-anayasa
  - K006-yapay-zeka/model-management/model-management-anayasa
evidence:
  - "Dosya: .ai/architecture/K006-yapay-zeka/index.md [Satır: 263]"
  - "Dosya: .ai/projects/NevaEngine/legacy-2026-07/ai-models.md [Satır: 265]"
  - "Dosya: .ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md"
validation: "İçerik doğrulama: 2026-10-15 — anayasa, ai-models.md §5.1 (genre, satır 265) ve eq-ai-auto.md başlık indeksiyle eşleştirildi"
open_questions:
  - "MusicBERT/lyrics modellerinin lisans ve kullanım kapsamı doğrulanmalı (model kartı → model-management §4)"
  - "Key/tempo/fingerprint gibi klasik analiz görevlerinin K006 kapsamındaki yeri netleştirilmedi (bileşen listesi UNKNOWN)"
  - "Tür etiket şeması (kontrol listesi / hiyerarşi) standardize edilmedi"
group: mimari
---
# Music Analysis — Alt Dal Anayasası (kademe-2)

> **Bu dosya kademe-2 anayasadır.** Ebeveyn: `K006-yapay-zeka` (bkz. `index.md` §2.6 çocuk haritası — v2 satır 263, `G:\` arşivinde).
> Kardeş anayasalar: `../personalization/personalization-anayasa.md`, `../recommendation/recommendation-anayasa.md`, `../audio-intelligence/audio-intelligence-anayasa.md`.
> Bu dosya **şablon içermektedir** — tüm alanlar somut değerle doldurulmuştur.

---

## §1. Kapsam Sınırı

**music-analysis**, K006'da **müzik içeriğinin makine tarafından anlaşılması** disiplinini kapsar — ses/söz/metin verisinden **anlam üreten** model görevleri. Kapsayıcılar:

1. **Tür sınıflandırma** (genre) — MusicBERT tabanlı çok-sınıflı sınıflandırma; 8/8 doğru örneği (ai-models.md §5.1, satır 265).
2. **Söz embedding / semantik arama** — lyrics embedding ile anlam benzerliği (ai-models.md §6).
3. **Etiketleme (tagging)** — öznitelik çıkarımı (ruh hali, tempo, enstrüman vb. — bileşen listesi §4).
4. **Türev analiz çıktılarının üretici kullanımı** — analiz sonuçları personalization/recommendation'ın **girdisidir**.

**Kapsam dışı:**

- Ses **sentezi/işlenmesi** (EQ, DSP efekt zinciri) → `audio-intelligence` + Neva DSP (`.ai/projects/NevaEngine/02-dsp-zincir-mimari.md`)
- Kullanıcı **tercih** tahmini (dinleme alışkanlığı) → `personalization`
- Benzer-şarkı **öneri** sıralaması → `recommendation`
- Öneri/analiz modelinin **canlı çağrısı** → `inference`

**Kapsam sınırı testi:** "Girdi bir **parça** (ses/söz/etiket), çıktı **içerik hakkında bilgi** mi?" → Evet = music-analysis. "Girdi bir **kullanıcı** mı?" → personalization.

---

## §2. İlke ve Değerler

### §2.1 Öncelik sırası

| # | İlke | Gerekçe |
|---|------|---------|
| 1 | **Etiket, kaynağından güçtür** | Eğitilmemiş/rastgele etiket, sistemin geri kalanını zehirler (öneri → kişiselleştirme zinciri) |
| 2 | **Domain'e özgü ölçüm** | Genel NLP metriği müzik kalitesini ölçmez — golden set şart (evaluation K-EVAL-04) |
| 3 | **Kanal kalitesi önce** | Gürültülü/bozuk kaynaktan iyi model çıkmaz (ön-işleme standartları §6) |
| 4 | **Açıklanabilirlik** | Üretilen etiketin **gerekçesi** (hangi özellik) saklanabilir değil |
| 5 | **Çok-dilli/çok-kültürel dürüstlük** | Türkçe/yerel türler ayrı değerlendirilir — "ortalama" ile örtbas edilmez |

### §2.2 Zero-hallucination bağlayıcılığı

1. Model doğruluk iddiaları (ör. 8/8) **koşuluyla** aktarılır: hangi model, hangi test kümesi, kaç örnek (ai-models.md §5.1, satır 265).
2. Model adları (MusicBERT vb.) **yalnız** vault kaynaklıdır — var olmayan model adı **uydurulmaz**.
3. Bileşen listesi (§4) kapsam **dahilinde netleşmemiş görevler** için `UNKNOWN` taşır; uydurma etiket-görevi yazılmaz.
4. Etiket standardı (tür hiyerarşisi) şema standardize edilmediyse `⚠️ VERIFICATION REQUIRED` (§12.3).

---

## §3. Mimari Roller ve Kararlar

### §3.1 Analiz boru hattı

```
[1] KAYNAK      → parça kaydı: ses dosyası / söz metni / mevcut etiketler
[2] ÖN-İŞLEME   → normalizasyon, dil tespiti, segmentasyon (§6)
[3] ÖZELLİK     → spektrum/roberta-gömme vs. görev (§4)
[4] MODEL       → sınıf/embedding/etiket (model-management kayıtlı — K-MM-01)
[5] ÇIKTI ŞEMASI → kanonik etiket + güven skoru + gerekçe (§7)
[6] KALITE KAPISI → evaluation gate (golden set) — kötü etiket yayılmaz
[7] TESLİM      → personalization/recommendation'a kayıt (S3)
```

**Karar (K-MA-01):** Analiz çıktısı **her zaman güven skoruyla** (confidence) iletilir — skorsuz etiket tüketilemez (tüketici eşik koyamaz).
**Kanıt:** ai-models.md §5.1 (satır 265) sınıflandırma çerçevesi; §6 söz embedding.

### §3.2 Sahiplik matrisi

| Alan | Sorumluluk | Not |
|------|-----------|-----|
| Görev/etiket tanımı | music-analysis | §4, §7 |
| Model kaydı/sürümü | model-management | gate dahil |
| Kalite ölçümü (accuracy vb.) | evaluation | golden set §9 |
| Canlı çağrı/önbellek | inference | bu dal **tanımlar**, canlıyı inference yürütür |
| Tüketim (öneri/kişiselleştirme) | recommendation / personalization | §11 |

**Karar (K-MA-02):** music-analysis **bilgi üretir**, **karar vermez** — öneri sıralaması/kişisel tercih bu dalın dışındadır (bkz. §1 kapsam dışı 2–3).

---

## §4. Görev Kataloğu (bu dalın görevleri)

| Görev | Girdi | Çıktı | Durum |
|-------|-------|-------|-------|
| Tür sınıflandırma (genre) | ses/söz | tür etiketi + güven | **kanıtlanabilir** — ai-models.md §5.1, satır 265 (MusicBERT) |
| Söz semantik gömme | söz metni | embedding vektörü | **kanıtlanabilir** — ai-models.md §6 (lyrics embedding) |
| Söz→anlam arama | sorgu metni | benzerlik skorları | §6'nın türevi |
| Ruh hali/öznitelik etiketleme | ses | etiket seti | **parçalı kanıt** — bileşen/şema `UNKNOWN` (§12.2) |
| Key/tempo/ölçü analizi | ses | ton/hız | **UNKNOWN** — kapsamı netleştirilmedi (§12.2) |
| Parmak izi (fingerprint) | ses | hash | **UNKNOWN** — kapsam dışı olabilir, doğrulanmalı |
| Şarkı sözü dil analizi (dil, tema) | metni | dil/tema | söz embedding ile ilişkili; şema `UNKNOWN` |

**Kural (K-MA-03):** `UNKNOWN` görevler **üretime alınmaz** — kapsam kararlaştırılır (`music-analysis-kapsam.md`), sonra şema + model + gate sırası işletilir. Uydurma görev-üretimi yasak (§2.2.3).

---

## §5. Etiket Şeması İlkeleri

1. **Kontrol listesi (closed vocabulary)** — serbest metin etiket **yok**; her etiket kayıtlı listedendir.
2. **Hiyerarşi** — üst/alt tür ilişkisi desteklenir (ör. `tur/alt-tür`); düzlem çökmesi yasak.
3. **Sürümleme** — şema sürümü vardır (`taxonomy_version`); model, şema sürümünü **bilmek zorunda** (uyumsuzluk = yanlış yorum).
4. **Çoklu etiket** — bir parça birden çok etiket alabilir; **tek-gerçek** varsayımı yoktur (yanlış istisna).
5. **Bilinmeme hakkı** — güven eşiği altındaki tahmin `unknown` döner; **zorla etiket** yasak.
6. **Köken etiketi** — her etiketin kaynağı: `model` \| `editör` \| `kullanıcı` \| `meta` (kaynağı kayıtsız etiket sisteme girmez).

**Karar (K-MA-04):** Etiket şeması ADR ile sabitlenir; şema değişikliği **eski etiketleri migrate** eder (silmez — append-only şema geçmişi).

---

## §6. Ön-İşleme ve Kaynak Kalitesi

| Adım | Kural | Amaç |
|------|-------|------|
| Normalizasyon | ses ölçek/LUFS standardı (değeri `UNKNOWN` → §12) | kanal tutarlılığı |
| Dil tespiti | söz için zorunlu | model-dili uyumu |
| Segmentasyon | uzun parçalarda pencereleme | bellek/latency bütçesi |
| Bozuk kaynak tarama | kırpılma/gürültü eşiği → `low_quality` damgası | kötü girdi → iyi çıktı yok (İlke 3) |
| Dizin/parça eşleştirme | parça kimliği + süre kontrolü | yanlış parçaya etiket yasak |

**Kural (K-MA-05):** `low_quality` damgalı kaynak **eğitim ve gate setine alınmaz**; üretimde etiket **`low_quality` uyarısıyla** iletilir (güven düşürülür, gizlenmez).

---

## §7. Çıktı Şeması (kanonik analiz çıktısı)

| Alan | Zorunlu | Açıklama |
|------|---------|----------|
| `track_id` | Evet | parça kimliği |
| `task` | Evet | görev adı (§4 kontrol listesi) |
| `label` | Evet | kanonik etiket (`unknown` olabilir) |
| `confidence` | Evet | 0–1; **skor zorunlu** (K-MA-01) |
| `model_ref` | Evet | model + sürüm (model-management kaydı) |
| `schema_version` | Evet | etiket şeması sürümü (§5.3) |
| `source` | Evet | `model` \| `editör` \| `kullanıcı` \| `meta` |
| `quality_flag` | Evet | `ok` \| `low_quality` (§6) |
| `evidence` | Evet | gerekçe: hangi özellik/segment → güven dayanağı (İlke 4) |
| `created_at` | Evet | tarih damgası |

**Zorunlu kural:** `confidence` eşiği altı → `label: unknown` (§5.5); `evidence` alanı boş olamaz — "model öyle dedi" **gerekçe değildir**, segment/özellik referansı ister.

---

## §8. Kalite ve Ölçüm (evaluation ile ortak usul)

### §8.1 Görev başına birincil metrik

| Görev | Birincil metrik | Not |
|-------|----------------|-----|
| Genre | accuracy / macro-F1 | dengesiz sınıflarda macro-F1 kapı |
| Söz embedding | retrieval accuracy@k / MRR | golden sorgu seti |
| Etiketleme | micro/multi-label F1 | çok-etiket |
| Güven kalibrasyonu | ECE (expected calibration error) | skor gerçekten güvenilir mi? |

### §8.2 Ölçüm disiplini (evaluation anayasasına bağlılık)

1. **Golden set** zorunlu (evaluation K-EVAL-04) — müzik-domain'i, Türkçe dâhil.
2. **Dil/tür dilimleri** ayrı raporlanır (§2.2 İlke 5) — genel ortalama **saklama** yapılmaz.
3. **Kalibrasyon** — güven skorları ECE ile ölçülür; kalibrasyonsuz skor **eşikler için kullanılmaz**.
4. **Kapı** — üretim etiketi akışı, gate PASS'ten geçmeden **tüketilmez** (K-MM-06 ile aynı disiplin).

**Karar (K-MA-06):** Etiket **kalitesi = öneri kalitesinin tavanıdır** — zincirin ilk halkası olarak bu dalın gate'i, downstream kapıların **ön koşuludur**.

---

## §9. Rehberler (Guidelines)

1. **Şema önce model** — kontrol listesi sabitlenmeden model eğitimi başlamaz (§5).
2. **Çıktı immutable** — geçmiş analiz sonucu **ezilmez**; yeni sürüm ayrı satır (append-only).
3. **Köken görünür** — `source` alanı her çıktıda; tüketici kaynağa göre güven ayarlar.
4. **Bölgesel dürüstlük** — Türkçe/yerel tür metrikleri raporda ayrı satır (İlke 5).
5. **Üretici-öneri ayrımı** — analiz çıktısı öneri **skoru değildir** (K-MA-02) — tüketici karıştırmasın diye alan adları ayrılır.
6. **Yeniden çalıştırma maliyeti** — batch analiz kuyruğu `inference` iş kuyruğu modeliyle uyumlu (asenkron).
7. **Değişiklik geri izleme** — model/şema sürümü değişince etkilenen parça listesi üretilebilir (§5.3 + §7 `model_ref`).
8. **Sessiz bozulma yok** — drift sinyali (girdi dağılımı) evaluation'a akar (K006 §2.1).

---

## §10. Zorunlu Kurallar (MUST / MUST NOT)

| # | Kural | Seviye |
|---|-------|--------|
| A1 | Her analiz çıktısı **confidence** taşımalıdır (K-MA-01) | MUST |
| A2 | Serbest-metin etiket kullanılamaz — yalnız kayıtlı kontrol listesi (§5.1) | MUST NOT |
| A3 | Düşük güven → `unknown`; zorla etiket **yoktur** (§5.5) | MUST |
| A4 | Çıktı **köken** (`source`) alanı olmadan sisteme **giremez** (§5.6) | MUST |
| A5 | `evidence` alanı boş olamaz (§7) | MUST |
| A6 | Model + sürüm referansı zorunlu (`model_ref`) — kayıtsız model çıktısı yasak | MUST |
| A7 | Gate PASS'i olmadan downstream tüketim başlamaz (K-MA-06) | MUST |
| A8 | Dil/tür dilimleri raporlanır — tek genel ortalama yetmez (İlke 5) | MUST |
| A9 | `low_quality` kaynak gizlenemez; etiketle birlikte iletilir (K-MA-05) | MUST NOT (gizleme) |
| A10 | `UNKNOWN` kapsam görevi üretime **alınamaz** (K-MA-03) | MUST |
| A11 | Geçmiş analiz sonucu silinmez/ezilmez (Rehber 2) | MUST NOT |

---

## §11. Bağımlılıklar

### §11.1 Bağımlı olduğu alanlar

| Alan | Ne alır | Kaynak |
|------|---------|--------|
| `model-management` | model kaydı, sürüm, gate kaydı | `../model-management/` |
| `evaluation` | golden set + kapı usulü | `../evaluation/` |
| `inference` | canlı/batch çağrı altyapısı | `../inference/` |
| `testing` | test + CI kapısı | `../testing/` |

### §11.2 Bağımlı olan alanlar

| Alan | Ne verir |
|------|----------|
| `personalization` | tür/etiket öznitelikleri (profil girdisi) |
| `recommendation` | analiz öznitelikleri (aday skorlama sinyalleri) |
| `audio-intelligence` | (ilişkili) içerik-özel DSP karar sinyalleri — sınır §1 kapsam dışı |

---

## §12. İlişkili Dosyalar ve Açık Sorular

### §12.1 Kardeş dosyalar (bu dal)

`index.md` · `music-analysis-kapsam.md` · `-bilesenler.md` · `-dogrulama.md` · `-ornekler.md` · `-web-arastirma.md` · kök `../yapay-zeka-kararlar.md` + `../yapay-zeka-riskler.md`

### §12.2 Vault referansları

| Dosya | Ne verir | Not |
|-------|----------|-----|
| `K006-yapay-zeka/index.md` [Satır: 263] | ebeveyn bağlantısı | v2 §2.6 |
| `ai-models.md` [Satır: 265] | §5.1 genre (MusicBERT, 8/8) | doğruluk örneği |
| `ai-models.md` §6 | lyrics embedding | semantik arama |
| `eq-ai-auto.md` | EQ/otomatik ayar analiz ilişkisi | başlık indeksi |
| `../recommendation/recommendation-anayasa.md` | tüketici çerçevesi | kardeş |

### §12.3 Açık sorular

1. **MusicBERT/lyrics modeli lisans kapsamı** → model kartı gerekli (model-management §4); doğrulanmadı → `⚠️ VERIFICATION REQUIRED`.
2. **Key/tempo/fingerprint görevi** kapsam mı? → `UNKNOWN` (K-MA-03 uyarınca üretime kapalı).
3. **Etiket şeması standardı** (tür hiyerarşisi) ADR bekliyor → `⚠️ VERIFICATION REQUIRED`.
4. **Normalizasyon standardı değeri** (LUFS hedefi) `UNKNOWN` — Neva/DSP tarafında ölçüm gerekebilir (`.ai/electronic/dsp/` — okundu, sayısal eşik bu turda aktarılmadı).
5. **Eğitim verisi kaynakları** (etiketli veri kimden) K006 dışı → `UNKNOWN`.

---

## §13. İlgili Lint Maddeleri

| LINT (`.ai/CLAUDE.md` §6) | Bu anayasadaki karşılığı |
|---------------------------|--------------------------|
| Zero-hallucination | §2.2 — model adı/doğruluk vault kaynaklı, koşullu |
| `UNKNOWN` / `VERIFICATION REQUIRED` | §4, §12.3 |
| Kanıt zorunluluğu | §13 karar/kaynak satırları |
| Append-only | Rehber 2, A11 |
| Parametreli/şema disiplini | §5 kontrol listesi (serbest metin yasak) |

---

## §14. Bağlam ve Versiyon

| Alan | Değer |
|------|-------|
| Sahip | K006-yapay-zeka / music-analysis |
| Ebeveyn | `K006-yapay-zeka` (kök `index.md` §2.6 — v2 satır 263) |
| Kademe | 2 |
| Durum | active |
| Oluşturulma | 2026-10-15 |
| Kapsanan sibling'lar | `music-analysis-kapsam` · `-bilesenler` · `-dogrulama` · `-ornekler` · `-web-arastirma` · `index.md` |
| Grup | mimari (`group:` frontmatter) |
| Belge tipi | anayasa (kademe-2, bu dalın usul belgesi — şablon değil) |

---

## §15. Bileşen Sözlüğü (TR↔EN, bu dalın terimleri)

| TR | EN | Bu anayasadaki anlamı |
|----|----|------------------------|
| Tür sınıflandırma | genre classification | §4 — MusicBERT görevi |
| Söz gömme | lyrics embedding | §4 — semantik vektör |
| Etiket kontrol listesi | taxonomy / controlled vocabulary | §5.1 — serbest etiket yasağı |
| Güven skoru | confidence score | K-MA-01 — skorsuz çıktı yok |
| Kanal kalitesi | channel / source quality | §6 — `low_quality` damgası |
| Gerekçe alanı | evidence | §7 — hangi özellik/segment |
| Köken | provenance (`source`) | §5.6 — model/editör/kullanıcı/meta |
| Kalibrasyon | calibration (ECE) | §8.2.3 — skor güvenilirliği |
| Dilim raporu | slice report | §8.2.2 — dil/tür ayrı rapor |
| Tüketici | downstream consumer | §11.2 — personalization/recommendation |

**Terim kuralı:** Olmayan terim bu dalda icat edilmez — önce `inference-anayasa.md` §9 sözlüğüne bakılır.

---

## §16. Durum Matrisi (analiz çıktısı durumları)

| Durum | Tanım | Tüketim |
|-------|-------|---------|
| `PENDING` | kuyrukta, işlenmedi | hayır |
| `PROCESSING` | model çalışıyor | hayır |
| `OK` | şema + güven + gate uyumlu | evet (eşik tüketiciye bağlı) |
| `LOW_CONFIDENCE` | eşik altı → `label: unknown` | evet, `unknown` olarak (§5.5) |
| `LOW_QUALITY` | kaynak bozuk (§6) | evet, uyarılı (K-MA-05) |
| `FAILED` | model/işlem hatası | hayır — tekrar/manuel inceleme |
| `STALE` | model/şema sürümü değişti, yeniden üretilmedi | **kademeli** — yeniden üretim planlanır |
| `RETIRED` | şema/etiket geçersiz (eski şema) | hayır — migrate edilir (K-MA-04) |

**Zorunlu kural:** `STALE` çıkışlar **görünürdür** (tüketici bayrağı görür) — sessiz eski-üretim kullanımı yasak; `FAILED` parça listesi raporlanır, sessizce eksik kalan parça yoktur.

---

## §17. Hata Sınıfları ve Analiz Davranışı

| Sınıf | Örnek | Davranış | Kayıt |
|-------|-------|----------|-------|
| Kaynak | dosya bozuk/eksik | `low_quality`/`FAILED` (§6) | parça olayı |
| Dil/şema | dil tanınmadı, etiket yok | `label: unknown` | güven + gerekçe |
| Model | oturum/çıkış hatası | `FAILED` + tekrar | kuyruk olayı |
| Sürüm | `model_ref` kayıtsız | **giriş reddi** (A6) | olay + blok |
| Şema uyumsuzluğu | `schema_version` farkı | `STALE` + migrate planı | K-MA-04 |
| Güven | ECE kötü | kapı durdurma | evaluation raporu (§8.2) |
| Kapı | gate FAIL | üretim etiketi akışı durur | K-MA-06 |

**Kural (K-MA-07):** Hata sınıflarının **hiçbiri** boş çıktı üretmez — her hata ya `FAILED` ya `unknown` damgasıyla **izlenebilir** biçimde kuyruğa/duruma düşer.

---

## §18. Güvenlik ve Etik (bu dalın yüzeyi)

1. **Telifli söz metni** — analiz için ham söz metni **log'a tam gömülmez**; özet/embedding düzeyinde kalır (PII benzeri metin mahremiyeti).
2. **Etiket manipülasyonu** — `editör`/`kullanıcı` kökenli etiketler ayrı sinyal sayılır; model etiketiyle **karıştırılmaz** (§5.6).
3. **Yanlış- sınıflandırma etkisi** — hassas/yanlış tür etiketleri (ör. çocuk içerik, din/kültür) → insan onaylı müdahale kanalı: `UNKNOWN` (mekanizma yok) → §12.3.
4. **Reklam/dişerim etiketleri** — analiz çıktısı **pazarlama etiketi** olarak kullanılamaz (kapsam dışı; `intended_use` sınırı model kartında — model-management §4).
5. **Kullanıcı-üretim etiket** (`source: kullanıcı`) → öneri/piyasada **azınlık ağırlığı** ile ele alınır (adversarial/yanlış etiket riski) — ağırlık değeri `UNKNOWN` (ADR).

---

## §19. Anti-Patternler (yasak kısayollar)

| Anti-pattern | Neden zararlı | Doğrusu |
|--------------|---------------|---------|
| "Model öyle sınıflandırdı" (gerekçesiz) | denetlenemez | `evidence` zorunlu (A5) |
| Serbest metin etiket ("hüzünlü rock-baladı") | şema çöker | kontrol listesi (A2) |
| "Güven 0.45, ama yine de verelim" | yanlış sinyal yayılır | `unknown` (A3) |
| "Tür metriği tek sayıda iyi" | dilim maskesi | dil/tür raporu (A8) |
| "Analiz sonuçlarını ezip yenileyelim" | kanıt kaybı | append-only (A11) |
| "Kapsam dışı görevi de bağlayalım" | sahipsiz üretim | K-MA-03 → kapsam kararı |
| "Eski şema etiketlerini silelim" | veri kaybı | migrate (K-MA-04) |
| "Kullanıcı etiketi = model etiketi" | köken kaybı | `source` ayrımı (A4) |

---

## §20. Uç Nokta Sözleşmesi (giriş/çıkış)

### §20.1 Giriş

| Girdi | Sağlayıcı |
|-------|-----------|
| Parça kaydı (ses/söz/etiket) | uygulama/data (K006 dışı yüzey) |
| Model + sürüm | `model-management` |
| Ön-işleme standardı | bu dal §6 |
| Kapı eşikleri | `evaluation` |

### §20.2 Çıkış

| Çıktı | Alıcı | Biçim |
|-------|-------|-------|
| Kanonik analiz çıktısı | `personalization` | §7 şeması |
| Analiz öznitelikleri | `recommendation` | §7 (`task`/`label`/`confidence`) |
| Kapı raporu | `evaluation`/release | §8 |
| Olay/FAILED listesi | `testing`/operasyon | §16, §17 |

---

## §21. Kaynaklar ve Red-Team Soruları

### §21.1 Kaynaklar

| # | Kaynak | Kullanım |
|---|--------|----------|
| R1 | `K006-yapay-zeka/index.md` [Satır: 263] | ebeveyn bağlantısı |
| R2 | `ai-models.md` [Satır: 265] | §5.1 genre (MusicBERT 8/8) |
| R3 | `ai-models.md` §6 | lyrics embedding |
| R4 | `eq-ai-auto.md` | EQ/analiz ilişkisi (başlık indeksi) |
| R5 | Kardeş anayasalar (§12.2) | gate/kayıt/çerçeve |
| R6 | Web kaynağı | AÇILMADI — bu turda URL eklenmedi (`music-analysis-web-arastirma.md`'ye devredildi) |

### §21.2 Red-Team soruları

| # | Soru | Beklenen cevap |
|---|---|---|
| T1 | Skorsuz etiket gelirse? | şema reddi — A1, K-MA-01 |
| T2 | `evidence` boşsa? | kayıt reddi — A5 |
| T3 | Kayıtsız model çıktısı? | giriş reddi — A6, §17 |
| T4 | Güven 0.3 olan etiket öneriye girerse? | `unknown` olarak — A3 |
| T5 | Eski şema etiketi modern okunursa? | `schema_version` ile ayrıştırılır — §5.3 |
| T6 | Kullanıcı sahte etiket yığarsa? | köken ayrımı + ağırlık (§18.5) — ağırlık `UNKNOWN` |
| T7 | Bozuk dosyadan etiket çıktı mı? | `low_quality` damgası — K-MA-05 |
| T8 | Gate FAIL iken tüketim başladı mı? | başlamaz — A7, K-MA-06 |

### §21.3 Revizyon Kaydı

| Tarih | Değişiklik | Yapan | Not |
|---|---|---|---|
| 2026-10-15 | İlk yazım (dosya oluşturma) | K006 vault içerik ajanı | Append-only; §1–§21 |
| — | Sonraki revizyon | — | Yalnız ek bölüm olarak |

**Authority:** Bayram Ali / Vault Steward · **status:** active — §12.3 açık soruları kapsam/ADR kararlarıyla kapatılır.

---

## §22. Ölçüm Raporu Başlıkları (bu dal için zorunlu)

Her sürüm/iyileştirme raporu (evaluation §25 şemasına bağlı, bu dalın ek başlıkları):

1. **Görev × metrik matrisi** — §8.1 görevlerinin tamamı; `UNKNOWN` görevler "kapsam dışı" satırıyla görünür.
2. **Dil/tür dilimleri** — ayrı satırlar (A8) — asla yalnız genel ortalama.
3. **Kalibrasyon (ECE)** — güven skoru güvenilirliği ayrı bölüm (§8.2.3).
4. **Şema uyumu** — `schema_version` dağılımı + `STALE` oranı (§16).
5. **Kaynak kalitesi** — `low_quality` oranı ve etkisi (§6, K-MA-05).
6. **Kapı sonucu** — gate PASS/FAIL + `model_ref` (A6, A7).
7. **Downstream etkisi notu** — personalization/recommendation'da sinyal kullanımı hakkında bilgi (geri besleme, sahiplik K-MA-02).

**Kural (K-MA-08):** Rapor başlıklarından **hiçbiri atlanamaz** — boş olan başlık `UNKNOWN`/`kapsam dışı` satırıyla yazılır (boş bırakma, gizleme).

---

## §23. Teslim ve Öncelik Sırası (bu dalın uygulama sırası)

Yeni bir analiz görevi bu dalda **aşağıdaki sırayla** açılır — sıra atlanamaz:

| # | Adım | Çıktı | Kapı |
|---|------|-------|------|
| 1 | Kapsam kararı | görev §4'e girer (`UNKNOWN` → karar) | kapsam dosyası |
| 2 | Şema | kontrol listesi + `taxonomy_version` (§5) | ADR |
| 3 | Veri | etiketli veri kaynağı + golden set (§8.2) | evaluation |
| 4 | Model kaydı | model-management kartı (§4, M-kuralları) | K-MM-* |
| 5 | Çıktı şeması | §7 alanları + `evidence` üretimi | şema testi |
| 6 | Kapı ölçümü | §8.1 metrikleri + dilim + ECE | **block gate** |
| 7 | Teslim | downstream `task`/`label`/`confidence` (§20.2) | canlı gözlem |

**Neden bu sıra:** Ters sırada (önce model, sonra şema/veri) **etiketsiz üretim** ve **ölçümsüz kapı** doğar — bu anayananın İlke 1 ve K-MA-06'sına aykırıdır.

---

## §24. Kardeş Bağlantıları (bu anayasanın K006 içindeki konumu)

| Kardeş | İlişki | Referans |
|--------|--------|----------|
| `personalization` | **birincil tüketici** — etiket/öznitelik profil girdisi | §11.2 |
| `recommendation` | öznitelik = aday skorlama sinyali | §11.2 |
| `audio-intelligence` | sınır: içerik-özel DSP sinyali — ses-işleme burada değil | §1 |
| `inference` | canlı/batch çağrı altyapısı (bu dal tanımlar, orası yürütür) | K-MA-02 |
| `evaluation` | golden set + kapı usulü (burada yalnız görev-bazlı metrik) | §8 |
| `model-management` | model kartı + gate kaydı | §3.2 |
| `testing` | test + CI kapısı | §11.1 |

**Çelişki kuralı:** Bu anayasa ile kardeşleri arasında **kapsam/sahiplik** çelişkisi çıkarsa kök `index.md` (kademe-1) kazanır; kökte yoksa `.ai/CLAUDE.md` §2 otorite sırası uygulanır.

---

## §25. Kapı Özeti — anayasa son kararı

Bir music-analysis değişikliği aşağıdaki kapıların **tamamından** geçmeden üretim etiketi akışına girmez:

1. **Kapsam kapısı** — görev §4'te, `UNKNOWN` değil (K-MA-03).
2. **Şema kapısı** — kontrol listesi + `taxonomy_version` + migrate planı (§5).
3. **Çıktı şeması kapısı** — `confidence` + `evidence` + `source` + `model_ref` dolu (§7, A1/A5/A6).
4. **Ölçüm kapısı** — §8.1 birincil metrik + dil/tür dilimi + ECE (A8, §8.2).
5. **Kaynak kapısı** — `low_quality` politikası işletilmiş (K-MA-05).
6. **Gate kaydı** — `metrics_ref` model kaydına bağlı (K-MM-06 ile uyum) — PASS olmadan tüketim yok (A7).

**Tek cümle özet:** *"Kaynağı izlenmeyen, güveni ölçülmeyen ve gerekçesi yazılmayan etiket, sistemde etiket değildir."*
**Revizyonla ilişkisi:** Bu bölüm §21.3 revizyon kaydından sonra eklenmiştir (append-only); sonraki ek bölüm §26, §27… olarak numaralanır.

---

## §26. Ek Usul — Değişiklik Eşiği Tablosu (ne değişince ne tekrarlanır)

| Değişen yüzey | Tekrarlanacak kapı | Neden |
|---|---|---|
| Model sürümü (`model_ref`) | §25 kapı 4 (ölçüm) + kapı 6 (gate kaydı) | etiket dağılımı değişir |
| Etiket şeması (`taxonomy_version`) | kapı 2 (şema) + migrate doğrulaması (K-MA-04) | geçmiş etiketler etkilenir |
| Kontrol listesi (yeni tür) | kapı 4 + dilim raporları (A8) | sınıf dengesi değişir |
| Ön-işleme standardı (§6) | kapı 5 (kaynak) + kapı 4 | girdi dağılımı değişir |
| Güven eşiği (K-MA-01) | kapı 3 (çıktı şeması) + ECE ölçümü | `unknown` oranı değişir |
| Downstream ağırlık kullanımı | geri-besleme raporu (K-MA-08.7) | öneri/kişiselleştirmeye etki |

**Son kural:** Tabloda olmayan değişiklik bile, **ölçüm (§8) + gate kaydı (A7) olmadan** üretim etiketi akışına girmez — bu ikisi her değişikliğin alt kümesidir.
