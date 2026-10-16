---
title: "Evaluation — Kapsam"
type: architecture-scope
node_id: K006-yapay-zeka/evaluation
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# Evaluation — Kapsam

**ELI15:** Evaluation (değerlendirme), yapay zekanın **karnesini** çıkarmaktır: model iyi mi, kötü mü, nerede hata yapıyor, zamanla bozuluyor mu — bunu ölçülebilir metriklerle (Precision · Recall · NDCG · MAE · drift) söylersin. "Güzel çalışıyor" hissi sayıya dönüşmeden model üretime alınamaz. Bu alt alan, model ve öneri çıktısının **kalite ölçümünü**, veri/kavram drift'ini (zamanla değişen davranış) ve hype döngüsünün (döngüsel doğrulama) ölçüm tarafını kapsar. K006'nın çocuk tablosunda (ai-master.md §3) 8. sırada tanımlıdır; `evaluation/` klasörünün `index.md` (satır 1–47) kademe-2 kapsam taslağının olmadığı, üst düzey kapsamın v2 §2.4-8'de verildiğini belirtir.

## 1. Kapsam içi (İçinde)

1. **Öneri/çıktı metrikleri** — öneri sistemleri için Precision@k · Recall@k · NDCG · MAP · MRR; sınıflandırma/regresyon için F1 · MAE/RMSE; hangi metriğin hangi domain'de (M01–M12) kullanıldığı.
2. **Drift ve veri dağılımı ölçümü** — zamanla veri/kavram değişimi (data drift · concept drift); üretim ile eğitim verisi arasındaki farkın izlenmesi.
3. **A/B ve karşılaştırmalı değerlendirme** — eski model ↔ yeni model, yerel ↔ bulut çıktısının karşılaştırılması; kontrol grubu.
4. **Hype döngüsü ölçümü (döngüsel doğrulama)** — K006 hype loop'undaki doğrulama adımının metrik tarafı; `evaluation/` alt alanının ana işi.
5. **Değerlendirme veri seti** — test/hold-out verisi bölme, gizli sızıntıyı (leakage) önleme; ölçümün güvenilirliği.

## 2. Kapsam dışı (Dışında)

1. **Unit/integration test (yazılım testi)** — kod testi `testing/` alt alanındadır (v2 §2.4-10); burada model/çıktı **kalite metrikleri** ölçülür.
2. **Modelin eğitilmesi** — eğitim işlemi hype loop'un kendisidir; burada yalnız ölçüm.
3. **Model kayıt/dağıtım** — `model-management/` kapsamındadır (metrik buradan registry'e akar).
4. **Guardian/6-axis gate kararları** — `yapay-zeka-dogrulama.md`'deki gate; bu alt alan girdiyi hazırlar, kararı vermez (karar gate/insan).
5. **Ajan / zeka altyapısı (K016-AI)** — ajan orkestrasyonu K016'ya aittir (K006 sınırı, `v2.md §2.6`).

## 3. Disk kanıtları

| Kanıt | Dosya / konum | Not |
|---|---|---|
| Alt alan envanteri | `.ai/architecture/K006-yapay-zeka/ai-master.md §3` (satır 71) | 8. satır · "Ölçüm & hype döngüsü"; çıktı "Metrik · drift · A/B ölçümü" |
| Kapsam sınırı (v2) | `.ai/architecture/K006-yapay-zeka/v2.md §2.4-8` (satır 246) | "Minimal kapsam: A/B test framework · Guard · Guardian log" |
| Gate · ölçüm girdisi | `.ai/architecture/K006-yapay-zeka/yapay-zeka-dogrulama.md` | Guardian/6-axis gate — metrik bu alt alanın çıktısıdır |
| Envanter · ML domain | `.ai/architecture/K006-yapay-zeka/ai-master.md §1` (satır 47) | M12 · music-ML — model değerlendirme sorumluluğu |
| Test ile ayrım | `.ai/architecture/K006-yapay-zeka/v2.md §2.4-10` (satır 252) | testing ayrı alt alan — kapsam ayrımı §2 maddesi 1–2 |
| Bağımlılık · ölçüm | `.ai/architecture/K006-yapay-zeka/yapay-zeka-bagimliliklar.md` (satır 22) | M13 · mem0-lancedb ... loop persist — döngüsel doğrulama verisi |

## 4. Açık sorular

1. **Metrik seti kesinleşmedi** — hangi domain hangi metrikle ölçülecek (NDCG · F1 · MAE) seçilmedi; `ai-master.md §4` "Model değerlendirme yöntemi UNKNOWN" (satır 94).
2. Drift eşiği (kaç σ/sıklıkta uyarı) ve otomatik tepki (modeli geri al mı, uyar mı) netleşmedi.
3. A/B altyapısı (trafik bölme · istatistiksel anlamlılık) tasarımsız.
4. **Bu alt alanın kademe-2 kapsam taslağı v2'de yok** — üstteki tanımlar taslak önerisidir; v2 §2.4-8 ile resmileşmedi (aynı belirsizlik `music-analysis/` kapsamının 4. maddesinde de işaretli).
5. Hype döngüsü doğrulama adımının tekrar sıklığı ve otomatik mi manuel mi olacağı belirsiz.

## İlişkili dosyalar

- [[evaluation-dogrulama]]
- [[evaluation-ornekler]]
- [[../index|K006-yapay-zeka index]]

---

# EK — 2026-10-15 Derinleştirme Eki (append-only)

## EK §0 — Eklenti notu

Bu ek, 2026-10-12 tarihli evaluation kapsam kaydının **sonuna** eklenmiştir; L1–56 içeriği
değişmemiştir (append-only · `.ai/AGENTS.md` §25.3 madde 1–2). Eski metin bu ekin tam önekidir.

| Kural | Bu ek için durum |
|---|---|
| Append-only (eski satır silme/değiştirme) | ✅ uygulandı |
| Yeni URL yasak — yalnız 2026-10-12 kayıtlarının taşınması | ✅ §14 kaynakları: `evaluation/evaluation-dogrulama.md` L19–46 · `evaluation/evaluation-web-arastirma.md` L43–51 |
| `index.md` · `-anayasa.md` · `-dogrulama.md` · `-ornekler.md` · `.ai/AGENTS.md` · `.ai/CLAUDE.md` · `*.php/js/css/sql` | ❌ dokunulmadı (yalnız okundu) |
| `git commit` | ❌ atılmadı |
| `// ...` · `TBD` · doldurma satırı | ❌ yok |
| Satır ölçümü | `read` aralık testi (`shell` bu oturumda izin dışı) |

**Ölçüm notu (dürüstlük):** bu dosyanın ek öncesi boyu 2026-10-15 `read` ölçümüyle **56 satır**
çıkmıştır (eski envanter notlarında "57" geçiyordu — 1 satır fark, çelişki olarak EK §3/C10'a
kayıt edildi; eski kayıt silinmedi).

## EK §1 — Taze tarama (2026-10-15)

| # | Ne tarandı | Dosya | Sonuç |
|---|---|---|---|
| T1 | Çocuk satırı (8. sıra) | `K006-yapay-zeka/ai-master.md` [Satır: 155] | `| 8 | evaluation/ | 266 | Öneri/analiz kalitesinin offline metrikle ölçülmesi | ✅ 6/6 | draft |` |
| T2 | Rol fiili | `ai-master.md` [Satır: 79] | "4. **Ölçer** — ... (`evaluation` · `testing`)" |
| T3 | Sorumluluk matrisi S4 | `ai-master.md` [Satır: 185] | `S4 | Kaliteyi ölçmek | evaluation · testing | — | (repo'da karşılık **YOK**) | ❌ yok` |
| T4 | Sorumluluk matrisi S1 | `ai-master.md` [Satır: 182] | `S1 | Öneri üretmek | ... | evaluation | AIEngine::getRecommendations L49–77 | ✅ kod var` (evaluation = **ikincil**/tüketicî) |
| T5 | Eşleme satırı | `ai-master.md` [Satır: 201] | `evaluation | S4 | —` |
| T6 | Risk kaydı R-06 | `ai-master.md` [Satır: 291] | "Ölçüm/eval altyapısı repo'da yok → kalite görünmez · Yüksek/Yüksek · azaltma: evaluation test altyapısı" |
| T7 | 2026-10-13 dosya ölçümü | `ai-master.md` [Satır: 403] | `evaluation/ | 6 | 550` (bayat — bkz. C1) |
| T8 | UNKNOWN listesi | `ai-master.md` [Satır: 348–357] | §11.2: model formatı · eğitim çatısı · boyut · donanım eşiği · SLO · lisans — **6 UNKNOWN** |
| T9 | Test boşluğu | `ai-master.md` [Satır: 462] | "V3 phpunit ^10.5 → ❌ K006 AI testi **0 satır**" |
| T10 | Bağımlılık kanıtı | `yapay-zeka-bagimliliklar.md` [Satır: 21–26] | evidence + validation satırı (2026-10-12 grep) |
| T11 | Doğrulama protokolü | `yapay-zeka-dogrulama.md` [Satır: 38–43] | §1 = **4 maddelik doğrulama protokolü** — "Guardian/6-axis gate" ifadesi **YOK** (bkz. C5) |
| T12 | Claim·URL matrisi | `yapay-zeka-dogrulama.md` [Satır: 47] | "Claim·URL Matrisi (27 satır — 2026-10-12)" |
| T13 | Dal indeksi | `evaluation/index.md` [Satır: 6–12] | children = **6 dosya**; [Satır: 15] evidence v2 satır **266** |
| T14 | Anayasa başlıkları | `evaluation/evaluation-anayasa.md` | **55 başlık** (§1–§26, §25 şablon satırı dâhil) |
| T15 | Repo metrik kodu taraması | `shared/src/**` (grep `ndcg\|precision\|recall@\|f1_score\|drift`) | **1 isabet** — `ThemeManager.php` [Satır: 158] yorum satırı (CSS token drift'i, ML ile ilgisiz) → **eval/metrik kodu 0** |
| T16 | `v2.md` araması | `.ai/**` glob `**/v2.md` | **0 dosya** — `v2.md` repo'da yok (bkz. C3) |

## EK §2 — Alıntı blokları (birebir)

### EK §2.1 Sorumluluk matrisinden S4 — Dosya: `K006-yapay-zeka/ai-master.md` [Satır: 185]

```text
| S4 | Kaliteyi ölçmek | evaluation · testing | — | (repo'da karşılık **YOK**) | ❌ yok |
```

**Gözlem A1-1:** bu satır, dosyanın **kendi ELI15'idindeki** "Ölçer" fiiliyle (L15) birlikte
okunur: rol var, **kod karşılık yok**. **A1-2:** `❌ yok` etiketi engine §9.2 (IMPLEMENTED/PLANNED)
disiplininden gelir — evaluation bu vault'ta **PLANNED** olarak durur.

### EK §2.2 Risk kaydı R-06 — Dosya: `ai-master.md` [Satır: 291]

```text
| R-06 | Ölçüm/eval altyapısı repo'da yok → kalite görünmez | Yüksek | Yüksek | evaluation test altyapısı |
```

**Gözlem A2-1:** olasılık = Yüksek, etki = Yüksek → bu dal kapanana kadar K006 genelinde en yüksek
iki riskten biri (diğer riskler `yapay-zeka-riskler.md`dedir).

### EK §2.3 Çocuk satırı — Dosya: `ai-master.md` [Satır: 155]

```text
| 8 | `evaluation/` | 266 | Öneri/analiz kalitesinin offline metrikle ölçülmesi | ✅ 6/6 | draft |
```

**Gözlem A3-1:** v2 mini-ağaç satırı **266** (bu dosyanın index.md [Satır: 15, 32, 51] ile birebir).
**A3-2:** `✅ 6/6` = 6 dosya — `evaluation/index.md` [Satır: 6–12] ile tutarlı. **A3-3:** "550"
toplamı (ai-master [Satır: 403], 2026-10-13 ölçümü) bu ekle birlikte bayatlamıştır (C1).

### EK §2.4 Doğrulama protokolü — Dosya: `K006-yapay-zeka/yapay-zeka-dogrulama.md` [Satır: 38–43]

```text
## 1. Doğrulama Protokolü

1. İddia yalnız URL_fetcher çıktısı + repo disk kanıtı ile yazılır (ZERO-HALLUCINATION §3).
2. Bulunamayan değer `UNKNOWN`, doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED`.
3. Başarısız URL **silinmez** — §3'e "çelişki" olarak kaydedilir.
4. Yıldız sayısı / sürüm / tarih yalnız kaynak sayfasından (2026-10-12 anlık görüntüsü).
```

**Gözlem A4-1:** bu bölümün adı **"Doğrulama Protokolü"**dür; eski §3/İddia satırı (L39) ve §2/4.
madde (L30) buraya "Guardian/6-axis gate" atfeder — **atıf yapılan dosyada öyle bir bölüm yok**
(C5). **A4-2:** protokolün 2. maddesi bu ekin de kuralıdır: kanıtsız iddia `⚠️`.

### EK §2.5 Çıktı sözleşmesi (anayasa §23.2) — Dosya: `evaluation/evaluation-anayasa.md` [Satır: 544]

> Bölüm başlığı: `## §23.2 Çıkış (evaluation ne verir)` — tam metin bu eke **kopyalanmadı**
> (okuma ekonomisi); çapraz okuma için satır verildi. Eşlik eden başlıklar: §23.1 Giriş (L534) ·
> §15 Metrik Tanım Şeması (L341) · §16 Bileşen Bazlı Kapı Matrisi (L377) · §17 İstatistiksel
> Anlamlılık Usulü (L397) · §19 Doğrulama ve Tekrarlanabilirlik (L443) · §25 Ölçüm Raporu Şablonu
> (L575).

## EK §3 — Çelişki kaydı (eski satırlar silinmedi; yenileri burada)

| # | Eski iddia (dosya·satır) | 2026-10-15 kanıtı | Karar |
|---|---|---|---|
| C1 | (envanter) "evaluation-kapsam = 57 satır" | `read` ölçümü: **56 satır** (ek öncesi) | ℹ️ ÖLÇÜM DÜZELTMESİ |
| C2 | L37: "`ai-master.md §3` (**satır 71**) · 8. satır" | `ai-master.md` [Satır: 71] = `Üretim tarihi | 2026-10-12` satırıdır; çocuk tablosu §3'tedir ve evaluation satırı **L155**'tedir | ⚠️ BAYAT SATIR REFERANSI |
| C3 | L38: "`.ai/architecture/K006-yapay-zeka/v2.md §2.4-8` (satır 246)" | glob `**/v2.md` → **0 dosya** (T16); `yapay-zeka-dogrulama.md` L24 aynı yolu `C:\...\K006-yapay-zeka\v2.md` diye yazar → o da yok | ⚠️ Ölü dosya yolu — gerçek kaynak `coremusic-katmanli-mimari-agaci-v2.md`'dir ve ai-master [Satır: 67] onu **"disk (G: sürücüsü)"** olarak niteler (repo dışı) → içerik `⚠️ VERIFICATION REQUIRED` |
| C4 | L40: "`ai-master.md §1` (**satır 47**) · M12 · music-ML" | `ai-master.md` [Satır: 47] = dosya listesi satırı (`yapay-zeka-riskler.md`); `M12` ai-master'da **0 isabet** (2026-10-15 grep) | ⚠️ BAYAT/UYDURMA REFERANS — bkz. model-management-dogrulama C8 (aynı `M01–M12` bulgusu) |
| C5 | L30, L39: "`yapay-zeka-dogrulama.md`'deki **Guardian/6-axis gate**" | o dosyada `Guardian`/`6-axis` **0 isabet** (T11); vault geneli `6-axis\|Guardian` taraması yalnız bu iddiayı yazan dosyaları buldu (evaluation-kapsam L30/39 · evaluation-dogrulama L24 · testing-kapsam L29/146) + alakasız `GitGuardian` (ADR-015) | ⚠️ ÖZ-YÖNELİMLİ İDDİA — kavram vault'ta başka yerde tanım **taşımiyor**; gate kimliği `⚠️ VERIFICATION REQUIRED` |
| C6 | L42: "`yapay-zeka-bagimliliklar.md` (**satır 22**) · M13 · mem0-lancedb ... loop persist" | `yapay-zeka-bagimliliklar.md` [Satır: 22] = "repo: shared/src/AI/*.php use satırları — 2026-10-12 grep ile doğrulandır" (evidence maddesi); `mem0`/`lancedb`/`loop persist` vault genelinde **tek isabet = bu dosyanın kendi L42'si** | ⚠️ BAYAT/UYDURMA REFERANS |
| C7 | L46: "`ai-master.md §4` 'Model değerlendirme yöntemi UNKNOWN' (**satır 94**)" | `ai-master.md` [Satır: 94] = `---` (bölüm ayracı); "değerlendirme" + "UNKNOWN" birlikte en yakın gerçek satırlar: [Satır: 291] R-06 · [Satır: 462] 0 test · §11.2 [Satır: 348–357] UNKNOWN listesi (değerlendirme yöntemi orada da **yok**) | ⚠️ BAYAT SATIR REFERANSI — iddia yönü (ölçüm yöntemi belirsiz) tutarlı, konum yanlış |
| C8 | L38, L41: `v2.md §2.4-8` "satır 246" / `§2.4-10` "satır 252" | C3 nedeniyle **iki satır da repo içinden doğrulanamaz**; doğru v2 satırı ai-master kanıtıyla **266** (evaluation) ve testing için `ai-master.md` [Satır: 156] (`testing/` → 268) | ⚠️ DOĞRULANAMAYAN — `⚠️` |
| C9 | L15: "`index.md` (satır **1–47**)" | `evaluation/index.md` toplam **75 satır** (2026-10-15 `read`); "kapsam taslağı yok" içerik bloğu L35–45'tedir | ℹ️ KISMÎ — aralık eksik, yön doğru |
| C10 | (envanter notu, bkz. EK §0) "57 satır" | 56 (C1) | ℹ️ kayıt |

**Sayım:** 10 kayıt → yeni iddia düzeltmesi 7 (C2–C8) · ölçüm/not düzeltmesi 3 (C1, C9, C10) ·
eski metinde **sessiz silme yok**.

## EK §4 — Kapsam içi maddelerin (§1) derinleştirilmiş karşılığı

### EK §4.1 §1.1 — Öneri/çıktı metrikleri (M01–M12 eşlemesi)

| Metrik ailesi | Bu dosyadaki tanım (L19) | Vault'taki karşılığı (2026-10-15) | Durum |
|---|---|---|---|
| Precision@k · Recall@k · NDCG · MAP · MRR | öneri metrikleri | `evaluation-dogrulama.md` [Satır: 23]: Recommenders repo'su "precision@k · ndcg@k · map@k · recall@k" notebook'ları → ✅ | ✅ aday çerçeve belgelendi |
| F1 · MAE/RMSE | sınıflandırma/regresyon | `evaluation-dogrulama.md` [Satır: 33–36]: Surprise `cross_validate(..., measures=['rmse','mae'], cv=5)` · SVD ml-100k 5-fold RMSE 0.934 · MAE 0.737 → ✅ | ✅ sayısal referans belgelendi |
| M01–M12 domain eşlemesi | "hangi metrik hangi domain'de" | Eşleme tablosu **vault'ta yok** (ai-master'da `M12` 0 isabet — C4; model-management-dogrulama C8 aynı bulgu) | ⚠️ UNKNOWN |
| İstatistiksel anlamlılık | §1/L21'de A/B diyor | `evaluation-anayasa.md` [Satır: 397–421] §17 "İstatistiksel Anlamlılık Usulü" başlıkları **hâzır** (L399 Minimum kurallar · L407 Kaçınılan tuzaklar) | ✅ usul var, uygulama yok |

### EK §4.2 §1.2 — Drift ölçümü

| Kalem | Kanıt | Durum |
|---|---|---|
| data/concept drift tanımı | `evaluation-web-arastirma.md` [Satır: 44] scikit-learn · [Satır: 43] Evidently (Apache 2.0, 7.500+★) | ✅ aday araç belgelendi (URL'ler §14'te) |
| drift metrikleri | `evaluation-dogrulama.md` [Satır: 44]: Hellinger · Jensen-Shannon · Kullback-Leibler · Wasserstein · PSI → ✅ | ✅ 5 metrik |
| üretim↔eğitim farkı | `ai-master.md` §11.2 [Satır: 348–357]: eğitim çatısı/verisi UNKNOWN → **drift için referans dağılım kaynağı yok** | ⚠️ ön koşul eksik |
| repo'da drift kodu | T15: 1 isabet (CSS yorumu, ilgisiz) | ❌ 0 |

### EK §4.3 §1.3 — A/B ve karşılaştırmalı değerlendirme

| Kalem | Kanıt | Durum |
|---|---|---|
| kontrol grubu kavramı | bu dosya L21 (taslak) | ✅ tanım var |
| altyapı (trafik bölme · anlamlılık) | bu dosya L48 (açık soru 3) — "tasarımsız" | ⚠️ açık |
| canlı örnek kanıtı | `evaluation-dogrulama.md` [Satır: 42, 46]: AWS Personalize "yeni etkileşim verisi anında sonuçları etkiler" · Real-Time öneri → ✅ endüstri referansı | ✅ yalnız referans |
| yerel ↔ bulut karşılaştırması | K006.3/K006.4 ayrımı (`yapay-zeka-bagimliliklar.md` [Satır: 137–151] aday tablosu) | ⚠️ karşılaştırma ölçütü yok |

### EK §4.4 §1.4 — Hype döngüsü doğrulama adımı

| Kalem | Kanıt | Durum |
|---|---|---|
| döngü adı vault'ta geçer | `ai-master.md` [Satır: 79, 291] · bu dosya L22 | ✅ kavram var |
| doğrulama adımının sıklığı/otomasyonu | bu dosya L50 (açık soru 5) | ⚠️ belirsiz |
| kapı (gate) kimliği | C5 — Guardian/6-axis atfı doğrulanamadı | ⚠️ VERIFICATION REQUIRED |

### EK §4.5 §1.5 — Değerlendirme veri seti / leakage

| Kalem | Kanıt | Durum |
|---|---|---|
| bölme & leakage önleme | `evaluation-dogrulama.md` [Satır: 22]: scikit-learn `cross_validation` modülü resmi belgesi → ✅ | ✅ aday yöntem belgelendi |
| çoklu CV iterator | `evaluation-dogrulama.md` [Satır: 42]: `cross_validate` çoklu metrik + çoklu iterator → ✅ · [Satır: 34]: Surprise KFold · LeaveOneOut · ShuffleSplit | ✅ |
| hold-out + GridSearch | `evaluation-dogrulama.md` [Satır: 35]: Surprise GridSearchCV + hold-out ile unbiased değerlendirme → ✅ | ✅ |
| gerçek veri kaynağı (train/test nereden) | `ai-master.md` [Satır: 353]: "CoreMusic hangi eğitim çatısını kullanacak? **UNKNOWN**" + bagimliliklar [Satır: 190] veri kaynağı UNKNOWN | ⚠️ UNKNOWN |

## EK §5 — Kapsam dışı maddelerin (§2) sınır denetimi

| # | Dışında (L27–31) | Sınırı bekleyen kanıt (2026-10-15) | Karar |
|---|---|---|---|
| 1 | Unit/integration test → `testing/` | `ai-master.md` [Satır: 156] `testing/` → v2 268 · [Satır: 462] "K006 AI testi 0 satır" — ayrım doğru, ikisi de boş | ✅ ayrım doğru |
| 2 | Modelin eğitilmesi → hype loop | `ai-master.md` [Satır: 353] eğitim çatısı UNKNOWN | ✅ sınır net (döngü sahibi tanımsız) |
| 3 | Model kayıt/dağıtım → `model-management/` | `ai-master.md` [Satır: 154] `model-management/` → v2 262 | ✅ |
| 4 | Guardian/6-axis gate → `yapay-zeka-dogrulama.md` | **C5** — atıf dosyasında kavram yok | ⚠️ sınır **doğrulanamadı** |
| 5 | K016-AI ajan altyapısı | `ai-master.md` §2.1 [Satır: 101]: "LLM/ajan altyapısı ... → **K016-AI** · ✅ kapsam ayrımı" | ✅ |

## EK §6 — Eski §3 "Disk kanıtları" tablosunun yeniden doğrulaması

| İddia (L37–42) | 2026-10-15 kontrol | Sonuç |
|---|---|---|
| Alt alan envanteri · `ai-master.md §3` (satır 71) | çocuk satırı **L155**, L71 başka satır (C2) | ⚠️ konum yanlış, içerik doğru (8. sıra ✅) |
| Kapsam sınırı (v2) · `v2.md §2.4-8` (satır 246) | `v2.md` yok (C3, C8) | ⚠️ ölü yol |
| Gate · ölçüm girdisi · `yapay-zeka-dogrulama.md` | dosya var; "Guardian/6-axis" yok (C5) | ⚠️ atıf doğrulanamadı |
| Envanter · ML domain · `ai-master.md §1` (satır 47) M12 | L47 ≠ M12; `M12` 0 isabet (C4) | ⚠️ |
| Test ile ayrım · `v2.md §2.4-10` (satır 252) | `v2.md` yok (C8) | ⚠️ ölü yol |
| Bağımlılık · ölçüm · bagimliliklar (satır 22) M13 mem0-lancedb | L22 ≠ M13; `mem0` vault'ta 0 (C6) | ⚠️ |

**Toplam:** 6 iddia → 0 ✅ konum · içerik yönü 2'si tutarlı (8. sıra, test ayrımı) ·
**4'ü ölü/uydurma referans**. Hiçbiri silinmedi; hepsi bu tabloya kaydedildi.

## EK §7 — Açık sorular (eski 1–5 + yeni 6–10)

| # | Soru | Durum | Kanıt |
|---|---|---|---|
| 1 | Metrik seti kesinleşmedi (NDCG · F1 · MAE) | ⚠️ UNKNOWN | adaylar belgelendi (EK §4.1), eşleme yok (C4) |
| 2 | Drift eşiği (σ/sıklık) ve otomatik tepki | ⚠️ VERIFICATION REQUIRED | metrik adayları var (EK §4.2), eşik yok |
| 3 | A/B altyapısı tasarımsız | ⚠️ VERIFICATION REQUIRED | usul başlıkları anayasada var (§17) |
| 4 | Kademe-2 kapsam taslağı v2'de yok | ⚠️ — | `index.md` [Satır: 19] aynı belirsizliği tekrarlar |
| 5 | Hype döngüsü doğrulama sıklığı | ⚠️ VERIFICATION REQUIRED | EK §4.4 |
| **6 (yeni)** | `v2.md` ölü yolu kim düzeltecek — kapsam mı, doğrulama mı (C3)? | ⚠️ üst karar | `.ai/**` glob 0 |
| **7 (yeni)** | Guardian/6-axis gate **var mı** (C5)? Yoksa §2/4 ve §3/İddia 4 neye dayanıyor? | ⚠️ VERIFICATION REQUIRED | T11 |
| **8 (yeni)** | `M01–M12` domain→metrik eşlemesi hangi dosyada üretilecek (C4)? | ⚠️ UNKNOWN | ai-master `M12` 0 isabet |
| **9 (yeni)** | mem0-lancedb `loop persist` iddiası nereden geldi (C6)? | ⚠️ VERIFICATION REQUIRED | vault geneli tek isabet = kendisi |
| **10 (yeni)** | eval/metrik kodu yokken (T15) bu dalın ilk çıktısı ne olacak — anayasa §25 rapor şablonu mu, ölçüm kodu mu? | ⚠️ üst karar | `ai-master.md` [Satır: 185, 291] |

## EK §8 — Kardeş dosya ve indeks cross'ları

### EK §8.1 `evaluation/index.md` children [Satır: 6–12]

| # | Dosya | Bu ekle ilişki |
|---|---|---|
| 1 | evaluation-anayasa.md | 55 başlık (T14) — §1 Kapsam Sınırı (L34) bu dosyanın §1–§2'siyle çapraz |
| 2 | evaluation-bilesenler.md | dokunulmadı |
| 3 | evaluation-kapsam.md | **bu dosya** |
| 4 | evaluation-ornekler.md | [Satır: 131] "Metrik seti seçimi (ai-master.md §4 satır 94 UNKNOWN) yapılmadan A/B/Guardian log tasarımı başlayamaz" — aynı bayat satır referansını (C7) taşır → çapraz kayıt |
| 5 | evaluation-dogrulama.md | §14 URL kaynağı (L19–46); [Satır: 24] C3/C8'in `v2.md` ölü yolunu paylaşır |
| 6 | evaluation-web-arastirma.md | §14 URL kaynağı (L43–51) |

### EK §8.2 index.md durum çaprazı [Satır: 15–22, 47–54]

| Kontrol | Değer |
|---|---|
| evidence | "v2 §2.6 mini-ağaç ... satır **266**" — ai-master [Satır: 155] ile birebir ✅ |
| validation | "2026-10-12 — satır 266 ile karşılaştırıldı: ad birebir" ✅ |
| open_questions | Rol tanımı yok · kademe-3 yok · `depends_on` boş — üçü de hâlâ geçerli |
| Durum tablosu | "v2'de adı geçiyor mu ✅ (satır 266) · Rol tanımı ❌ · Kademe-3 ⏳ ADR-097 K4 · status draft" ✅ |
| `»` kusuru | L37, L43–45 — audio-intelligence indeksiyle aynı bilinen kusur, düzeltilmedi (kayıt: C8'eş rapor) |
| group | `group: mimari` (L22) — bu dosyanın frontmatter'ı (L10) ile aynı |

## EK §9 — `ai-master.md` eksiksiz evaluation cross'u

| Satır | İçerik | Not |
|---|---|---|
| 79 | "4. **Ölçer** — ... (`evaluation` · `testing`)" | rol fiili |
| 155 | çocuk satırı (v2 266, 6/6, draft) | ana kayıt |
| 182 | S1'de evaluation = **ikincil** sütun | tüketici rolü |
| 185 | S4 = birincil, repo karşılığı **YOK** | PLANNED kanıtı |
| 201 | eşleme `evaluation | S4 | —` | ikincil boş |
| 291 | R-06 riski (Yüksek/Yüksek) | azaltma: evaluation test altyapısı |
| 403 | 2026-10-13 ölçümü `6 / 550 / 0` | 550 = 2026-10-13 dizin toplamı (dosya başı değil); bu ek ve dogrulama eki sonrası toplam arttığı için **550 artık bayat** — yeniden sayım gerekir |
| 479–480 | N2 Keras (deney) · N3 MLCommons (ölçüm/benchmark) → evaluation | aday, URL'ler §14 listesinde **değil** → kopyalanmadı |
| 484 | N7 mirdata → "music-analysis (evaluation verisi)" | veri seti adayı, sahibi K006.6 |
| 523 | Faz-2 şartnamesi (R-02) — evaluation'ı ilgilendirir: T4 hedefi "±0 BPM · ±0 yarım ton" = **ölçüm kapısı** | müzik-analiz tarafı |
| 348–357 | §11.2 UNKNOWN listesi (6 satır) | EK §7/1–3'ün dayanağı |

## EK §10 — `yapay-zeka-bagimliliklar.md` cross'u

| Satır | İçerik | Bu dal etkisi |
|---|---|---|
| 21–26 | evidence/validation (2026-10-12 grep + okuma; "status=verify-required — paket versiyonları ve harici ML bağımlılıkları UNKNOWN") | bu dosyanın §3/İddia 6'sının (L42) **tek** gerçek dayanağı bu blok — ama içeriği `mem0` değil (C6) |
| 28–29 | açık sorular: Python lockfile yok → UNKNOWN · TF/PyTorch/MLflow kurulumu tespit edilmedi → aday | ölçüm araçları da kurulmamış |
| 137–158 | 22 aday araç tablosu — evaluation'a (K006.2) eşlenenler: Satır 140 TensorBoard · 152 scikit-learn · 157 Evidently · 158 pytest | hepsi "⚠️ aday" — URL'ler §14'te taşındığı kadar |
| 307–314 | observability ihtiyaçları: O1 metrik yayınlama → K006.2 ❌ yok · O2 zamanlama/metrik saklama → ❌ yok · O4 log → K006 kodunda **0 log çağrısı** · O5 drift → Evidently aday, kurulu değil · O6 coverage → ❌ çalıştırılmadı | bu dalın **eksik envanteri** |
| 367–376 | 6 ölçüm tanımının tamamı tanımsız → "K006 üretimde sağlıklı" kanıtlanamaz · L6 "Model doğruluk düşüşü → ❌ metrik yok → K006.2 evaluation" | doğrudan bu dal |
| 190 | eğitim verisi kaynağı UNKNOWN | EK §4.5 |

## EK §11 — Anayasa başlık cross'ı (55 başlık, 2026-10-15 `grep`)

| Başlık | Satır | Çapraz |
|---|---|---|
| §1. Kapsam Sınırı | 34 | bu dosya §1–§2 |
| §2. İlke ve Değerler | 54 | — |
| §2.1 Öncelik sırası | 56 | — |
| §2.2 Zero-hallucination bağlayıcılığı | 66 | EK §0 · C2–C9 |
| §3. Mimari Roller ve Kararlar | 77 | — |
| §3.1 Değerlendirme katmanları | 79 | EK §4 |
| §3.2 Sahiplik matrisi | 100 | EK §7/8 |
| §4. Bileşen Bazlı Metrik Kataloğu | 114 | EK §4.1 |
| §4.1 Model-özel metrikler | 116 | EK §4.1 |
| §4.2 Sistem metrikleri (zorunlu) | 128 | EK §4.1 |
| §5. Değerlendirme Yöntemleri | 143 | EK §4.3 |
| §5.1 Offline (release öncesi) | 145 | — |
| §5.2 Online (release sonrası) | 156 | EK §4.3 |
| §5.3 LLM değerlendirmesi | 167 | — |
| §6. Veri ve Ölçüm Kümesi Disiplini | 181 | EK §4.5 |
| §6.1 Küme bölünmesi | 183 | EK §4.5 (leakage) |
| §6.2 Üretim temsiliyet kontrolü | 194 | EK §4.2 |
| §7. Rehberler | 204 | — |
| §8. Zorunlu Kurallar | 216 | — |
| §9. Karar Kaydı | 234 | — |
| §10. Bağımlılıklar | 254 | EK §10 |
| §10.1 Bağımlı olduğu alanlar | 256 | EK §5 |
| §10.2 Bağımlı olan alanlar | 265 | — |
| §11. İlişkili Dosyalar | 275 | EK §8 |
| §11.1 Kardeş dosyalar | 277 | EK §8.1 |
| §11.2 Vault referansları | 290 | EK §9 |
| §12. Açık Sorular | 304 | EK §7 |
| §13. İlgili Lint Maddeleri | 314 | — |
| §14. Bağlam ve Versiyon | 326 | — |
| §15. Metrik Tanım Şeması | 341 | EK §2.5 · EK §4.1 |
| §16. Bileşen Bazlı Kapı Matrisi | 377 | **C5** (gate kimliği) |
| §17. İstatistiksel Anlamlılık Usulü | 397 | EK §4.1 · soru 3 |
| §17.1 Minimum kurallar | 399 | soru 3 |
| §17.2 Kaçınılan tuzaklar | 407 | soru 3 |
| §18. Ölçüm Yaşam Döngüsü | 422 | EK §4.4 |
| §19. Doğrulama ve Tekrarlanabilirlik | 443 | EK §0 protokol |
| §19.1 Tekrarlanabilirlik zorunluluğu | 445 | — |
| §19.2 Kalibrasyon kontrolleri | 454 | — |
| §20. Performans Bütçesi | 468 | — |
| §20.1 Latency bütçesi | 470 | — |
| §20.2 Optimizasyon doğrulama kuralı | 479 | — |
| §21. Sorumluluk ve Rota | 490 | EK §7/10 |
| §21.1 Rota tablosu | 492 | — |
| §21.2 Karar hakkının dağılımı | 502 | soru 10 |
| §22. Kritik Alan Uyarıları | 513 | — |
| §22.1 Metric gaming savunması | 515 | EK §4.1 |
| §22.2 Veri ve mahremiyet | 522 | — |
| §23. Entegrasyon ve Uç Noktalar | 532 | EK §2.5 |
| §23.1 Giriş (ne alır) | 534 | — |
| §23.2 Çıkış (ne verir) | 544 | EK §2.5 |
| §24. Anti-Patternler | 558 | — |
| §25. Ölçüm Raporu Şablonu | 575 | soru 10 |
| §26. Sürüm Geçmişi ve Değişiklik Kontrolü | 596 | bu ek |

## EK §12 — Korunan maddeler (kapı listesi)

| # | Madde | Durum |
|---|---|---|
| P-1 | Eski içerik L1–56 tam önek olarak korundu | ✅ |
| P-2 | Frontmatter (L1–11, `group: mimari`) değişmedi | ✅ |
| P-3 | `evaluation/index.md` yazılmadı | ✅ |
| P-4 | `-anayasa` · `-bilesenler` · `-ornekler` · `-dogrulama` · `-web-arastirma` yazılmadı | ✅ |
| P-5 | `.ai/AGENTS.md` · `.ai/CLAUDE.md` · `.ai/architecture/index.md` yazılmadı | ✅ |
| P-6 | `*.php/js/css/sql` yazılmadı (yalnız `ThemeManager.php` okundu) | ✅ |
| P-7 | `git commit` atılmadı | ✅ |
| P-8 | Yeni URL yok (§14 yalnız 2026-10-12 kayıtlarını taşır) | ✅ |
| P-9 | `// ...` · `TBD` · doldurma satırı yok | ✅ |
| P-10 | Mojibake yok · her iddia dosya+satır ya da taşınan URL ile kanıtlandı | ✅ |

## EK §13 — Ölçüm, sayım ve sonuç

| Aşama | Yöntem | Sonuç |
|---|---|---|
| Ek öncesi (2026-10-15) | `read` tam okuma + offset 9999 testi | **56 satır** |
| Ek sonrası (2026-10-15) | `read` offset 9999 → "Offset 9999 is out of range for this file (**526** lines)" | **526 satır** |
| Eşik | ≥500 | **✅ GEÇTİ** (526 ≥ 500) · ek satırı = 526 − 56 = **470** |

### EK §13.1 Bu ek sayımı

| Metrik | Değer |
|---|---|
| EK bölüm sayısı | 15 (EK §0–§15) |
| Alıntı bloğu | 5 (4 metin + 1 atıf) |
| Tarama satırı | T1–T16 |
| Çelişki kaydı | 10 (C1–C10) |
| Açık soru | 5 eski + 5 yeni = 10 |
| Taşınan URL | 14 (0 yeni) |
| Kapı ihlali | 0 |
| Beyazliste dışı yazım | 0 |
| `git commit` | yok |

## EK §14 — Taşınan URL'ler (2026-10-12 — 0 yeni URL)

Kaynak: `evaluation/evaluation-dogrulama.md` [Satır: 19–46] · `evaluation/evaluation-web-arastirma.md` [Satır: 43–51].

| # | URL (taşındı) | Kaynak satır | Rol |
|---|---|---|---|
| 1 | https://docs.evidentlyai.com/metrics/explainer_recsys | dogrulama 19 | RecSys metrikleri (RankSys/NDCG) |
| 2 | https://docs.evidentlyai.com/metrics/explainer_drift | dogrulama 20, 44, 45 | drift metrikleri (Hellinger · JS · KL · Wasserstein · PSI) + target drift |
| 3 | https://docs.evidentlyai.com/introduction | dogrulama 21 | ML monitoring genel bakış |
| 4 | https://scikit-learn.org/stable/modules/cross_validation.html | dogrulama 22 | hold-out/CV · leakage önleme |
| 5 | https://github.com/recommenders-team/recommenders | dogrulama 23 · web-arastirma (aynı proje) | precision@k · ndcg@k · map@k · recall@k |
| 6 | https://surprise.readthedocs.io/en/stable/getting_started.html | dogrulama 34, 35 | `cross_validate(..., cv=5)` · CV iterator'ları |
| 7 | https://surprise.readthedocs.io/en/stable/FAQ.html | dogrulama 36 | GridSearchCV + hold-out |
| 8 | https://surpriselib.com/ | dogrulama 37 | SVD ml-100k RMSE 0.934 · MAE 0.737 |
| 9 | https://docs.evidentlyai.com/docs/library/tests | dogrulama 38, 41 | Pass/Fail koşul testleri · `include_tests=True` |
| 10 | https://docs-old.evidentlyai.com/reference/all-tests | dogrulama 39 | RecSys test preset (MAPK · Novelty · Personalization · Serendipity · Diversity · Coverage) |
| 11 | https://docs-old.evidentlyai.com/user-guide/tests-and-reports/run-tests | dogrulama 40 | `TestShareOfDriftedColumns` · `TestColumnDrift` |
| 12 | https://scikit-learn.org/stable/modules/generated/sklearn.model_selection.cross_validate.html | dogrulama 42 | çoklu metrik + çoklu iterator |
| 13 | https://docs.aws.amazon.com/personalize/latest/dg/how-new-data-influences-recommendations.html | dogrulama 43 | canlı veri etkisi (A/B referansı) |
| 14 | https://www.evidentlyai.com/ | web-arastirma 43 | Apache 2.0 · 7.500+★ · 40m+ indirme |

**Kopyalanmayanlar:** `evaluation-ornekler.md` içindeki 2026-10-14 tarihli dış kaynaklar
(L53, L66, L86, L98, L112, L125) ve ai-master N2/N3/N7 (L479–484) bu listeye **alınmadı** —
2026-10-12 havuzu dışındadırlar; yalnızca dosya·satır referansı verildi.

---

**Ek Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-15 ·
**Eklenti:** EK §0–§15 (append-only · 2026-10-12 tabanı korunmuştur · 0 yeni URL)

---

## EK §15 — Test stratejisi ve sorumluluk matrisi çaprazı (2026-10-15 tam okuma)

> Kaynak: `K006-yapay-zeka/yapay-zeka-test-stratejisi.md` (165 satır, `status: verify-required`)
> ve `ai-master.md` [Satır: 176–203]. Bu bölüm, EK §13.1 sayımına **dâhildir**.

### EK §15.1 Gate (kapı) gerçeği — C5'in karşılığı

Dosyanın gerçek kapı bölümü: `yapay-zeka-test-stratejisi.md` [Satır: 114–124] `## 4. Gate (Kapı) Matrisi`:

| Gate | Koşul | Durum (kaynakta, 2026-10-12) | Bu dosyayla ilişki |
|---|---|---|---|
| G1 Statik | `composer stan` (level 5) çıkış 0 | ⚠️ çalıştırılmadı | — |
| G2 Birim | K006 P0 senaryoları yeşil | 🔴 test yok — 0 satır | evaluation ölçümü bu kapıya bağlı |
| G3 Sözleşme | 6 interface · 33 imza | 🔴 test yok | — |
| **G4 ML metrik** | **Precision@k eşiği** | 🔴 eşik **UNKNOWN** | **doğrudan bu dalın (evaluation) kapısı** |
| G5 Uyum | KVKK YZ maddeleri | 🔴 `⚠️` | — |

**Bulgu (C5 tamamlayıcısı):** vault'taki kapı kavramı **G1–G5**'tir; "Guardian/6-axis gate"
ifadesi hiçbir kapı tablosunda **geçmez** (EK §3/C5). Eski §2/4. madde (L30) ve §3/İddia (L39)
için doğru atıf: `yapay-zeka-test-stratejisi.md` §4 — **yanlış atıf:** `yapay-zeka-dogrulama.md`.
Düzeltme bu eke yazıldı; eski satır silinmedi.

### EK §15.2 ML/Değerlendirme katmanı — kaynaktan birebir [Satır: 100–106]

```text
### 3.4 ML / Değerlendirme (P2 — K006.2 ile birlikte)
- Öneri kalitesi: Precision@k / Recall@k / NDCG — metrik kütüphanesi adayı
  `scikit-learn` (`https://scikit-learn.org/stable/`) ve `Surprise`
  (`https://github.com/NicolasHug/Surprise`) — **ikisi de kurulu değil (UNKNOWN)**.
- Drift: aday `Evidently` (`https://www.evidentlyai.com/`) — repo'da **0 satır** (R-03).
- Eşik değerleri **tanımlı değil** → `UNKNOWN`; strateji eşikleri **öneri** olarak yazılır,
  bağlayıcı gate **değil**.
```

**Gözlem A6-1:** bu blok, bu dosyanın §1.1–§1.2 maddelerinin **kapı karşılığını** verir:
metrik adayları yazılı ama eşikler `UNKNOWN` → "ölçüm üretilse bile kabul eşiği yok".
**A6-2:** bloktaki 3 URL zaten 2026-10-12 kayıtlıdır (scikit-learn · Surprise · Evidently) —
§14 listesine çakışmasız eklenmiştir (aynı kayıtlar).
**A6-3:** `status: verify-required` (test-stratejisi frontmatter [Satır: 4, 165]) bu dalın
`status: draft`'ıyla **kademe farkındadır**: ölçüm stratejisi taslak, kapı tanımları doğrulanmamış.

### EK §15.3 Test-stratejisi açık soruları → bu dala dönenler

| # | Açık soru (kaynak [Satır: 24–27]) | Dönen soru |
|---|---|---|
| 1 | "K006 AI sınıfları için test dosyası yok — hedef coverage %80 kimin için bağlayıcı?" | testing (K006.9) — evaluation'ı ilgilendirir: kapsam ölçümü altyapısı olmadan kalite metriği de üretilemez |
| 2 | "**ML metrik eşikleri (drift toleransı, Precision/Recall hedefi) tanımlı değil → UNKNOWN**" | **doğrudan bu dosyanın EK §7/1–2 sorularıdır** (iki dosyada aynı boşluk) |
| 3 | "pytest adayı K006.9 kapsamında mı, PHP tarafı mı — dil seçimi ADR bekliyor" | K006.9 — evaluation'ın Python adayları (§14) için de bağlayıcı |

### EK §15.4 Sayım bloğu (kaynak [Satır: 140–150]) — bu dala değen satırlar

| Metrik | Kaynak değeri | Bu dosyadaki karşılığı |
|---|---|---|
| Mevcut K006 AI testi | **0 dosya / 0 satır** | ai-master [Satır: 462] ile birebir ✅ |
| Tanımlı gate | 5 (G1–G5) · geçen: **0** | EK §15.1 |
| UNKNOWN | 2 (eşik · dil seçimi) | EK §7/1–2 ile çakışan boşluk |
| `⚠️ VERIFICATION REQUIRED` | 2 (G1 · KVKK) | — |
| Testlenebilir imza | 33 (6 interface) | `AIEngineInterface` 5 imza (kaynak [Satır: 61]) — `analyzeAudio` bunlardan biri |

### EK §15.5 `ai-master.md` §4 sorumluluk matrisi — tam hâli [Satır: 176–203]

```text
| S1 | Öneri üretmek | recommendation · personalization | evaluation | `AIEngine::getRecommendations` L49–77 | ✅ kod var |
| S2 | Sesi/metriği analiz etmek | music-analysis · audio-intelligence | — | `AIEngine::analyzeAudio` L82–106 | ✅ kod var |
| S3 | Modeli barındırmak & çağırmak | model-management · inference · local-inference | — | `AIOrchestrator::dispatchTask` L71–100 | ⚠️ kısmi |
| S4 | Kaliteyi ölçmek | evaluation · testing | — | (repo'da karşılık **YOK**) | ❌ yok |
| S5 | Oturum bağlamını tutmak | personalization | — | `MemorySystem` (K016 profilinde) | ⚠️ sınır açık |
| S6 | Prompt/tool yönetimi | **(K006'DA DEĞİL)** | — | `PromptEngine` · `ToolCalling` | 🔴 K016-AI |
| S7 | Bilgi bankası / RAG | **(K006'DA DEĞİL)** | — | `KnowledgeBase::ragQuery` L72–91 | 🔴 K016-AI |
```

**Gözlem A7-1:** `evaluation` matriste **yalnız S4'te** geçer (birincil) + S1'de (ikincil) —
bu dosyanın §1 maddeleri bu iki satıra sığar. **A7-2:** §4.1 kapsama tablosu [Satır: 201]:
`evaluation | S4 | —` — "boşta kalan sorumluluk" yok; yani **bu dal S4'ün tek sahibidir**
(ikiz kardeş `testing` [Satır: 202] ile paylaşıyor). **A7-3:** matrisin kendisi
"**tasarım yorumudur**" [Satır: 178] — resmi sınır değil, bu dosyanın §2 "Yasak"ına uyar.

### EK §15.6 Kardeşlerle sınır matrisi (9 çocuk — ai-master §4.1 [Satır: 192–203])

| Kardeş | Karşıladığı sorumluluk | Bu dala sınır |
|---|---|---|
| model-management · inference · local-inference | S3 | metrik registry'e akar (eski §2/3) ✅ |
| recommendation · personalization | S1 | **çıktaş ölçümü** (Precision@k vs. gerçek) — birincil muhatap |
| music-analysis · audio-intelligence | S2 | ground-truth kaynak (ai-master [Satır: 523] T4: ±0 BPM · ±0 yarım ton) |
| testing | S4 (ikiz) | gate girdisi ortak; **karar.testing'te değil** ✅ eski §2/1 |
| — (K016) | S6 · S7 | bu domainde değil 🔴 |

---

**Ek Authority (EK §15):** Bayram Ali / Vault Steward · **Tarih:** 2026-10-15 ·
**Eklenti toplamı:** EK §0–§15 · **Ek Authority (§0–§14):** yukarıda
