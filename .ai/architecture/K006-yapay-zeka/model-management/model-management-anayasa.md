---
node_id: K006-yapay-zeka/model-management/model-management-anayasa
node_type: module
status: active
parent_id: K006-yapay-zeka/model-management
children:
  - K006-yapay-zeka/model-management/model-management-kapsam
  - K006-yapay-zeka/model-management/model-management-bilesenler
  - K006-yapay-zeka/model-management/model-management-dogrulama
  - K006-yapay-zeka/model-management/model-management-ornekler
  - K006-yapay-zeka/model-management/model-management-web-arastirma
depends_on:
  - K006-yapay-zeka/evaluation/evaluation-anayasa
  - K006-yapay-zeka/inference/inference-anayasa
  - K006-yapay-zeka/local-inference/local-inference-anayasa
evidence:
  - "Dosya: .ai/architecture/K006-yapay-zeka/index.md [Satır: 259]"
  - "Dosya: .ai/projects/NevaEngine/legacy-2026-07/ai-models.md [Satır: 503]"
  - "Dosya: .ai/projects/NevaEngine/legacy-2026-07/ai-models.md [Satır: 434]"
validation: "İçerik doğrulama: 2026-10-15 — anayasa, ai-models.md §8 (HuggingFace, satır 503) ve §7.2 (job queue, satır 434) kanıtlarıyla eşleştirildi"
open_questions:
  - "Model registry'nin fiziksel altyapısı (HF Hub mı, dahili depo mu) kararlaştırılmadı — UNKNOWN"
  - "Model kartı şemasının zorunlu alan listesi (intended use, bias bildirimi) ADR bekliyor"
  - "Retire edilen modelin üretim cache'leriyle ilişkisi (TTL üstü) doğrulanmalı"
group: mimari
---
# Model Management — Alt Dal Anayasası (kademe-2)

> **Bu dosya kademe-2 anayasadır.** Ebeveyn: `K006-yapay-zeka` (bkz. `index.md` §2.6 çocuk haritası — v2 satır 259, `G:\` arşivinde).
> Kardeş anayasalar: `../inference/inference-anayasa.md`, `../local-inference/local-inference-anayasa.md`, `../evaluation/evaluation-anayasa.md`.
> Bu dosya **şablon içermektedir** — tüm alanlar somut değerle doldurulmuştur.

---

## §1. Kapsam Sınırı

**model-management**, K006'da **model varlıklarının yaşam döngüsünü** (getirme, kaydetme, sürümlendirme, yayma, emekliliye ayırma) kapsar. Kapsayıcılar:

1. **Kaynak** — harici model getirme (HuggingFace Hub vb.) ve içe aktarma (ai-models.md §8.1, satır 503).
2. **Kayıt (registry)** — model kartı, sürüm, metadata, checksum, lisans.
3. **Yayma (distribution)** — servis/inference'a ve yerel paketlere teslim.
4. **Yaşam döngüsü kararları** — aday → staging → production → retired.
5. **Görev kuyruğu** — indirme/doğrulama/dönüştürme işleri (ai-models.md §7.2, satır 434).

**Kapsam dışı:**

- Eğitim/fine-tune süreç boru hattı (pipeline'ın kendisi) → ayrı ML domain; **burada yalnız kaydedilen modelin *teslimi* vardır**
- Çalıştırma (session, EP, fallback) → `local-inference` / `inference`
- Kalite ölçümü → `evaluation` (burada yalnız **gate sonucunun kaydı** vardır)
- Veri seti yaşam döngüsü → K006 dışı (data domain)

**Kapsam sınırı testi:** "Bir model dosyasının **adresi, sürümü ve kaderi** ne?" → Evet = model-management. "Model **ne yapıyor**?" → inference. "Model **iyi mi**?" → evaluation.

---

## §2. İlke ve Değerler

### §2.1 Öncelik sırası

| # | İlke | Gerekçe |
|---|------|---------|
| 1 | **Her modelin tek bir doğru adresi vardır** | Sürsüz/etiketsiz model üretimde izlenemez (ai-models.md §8.1, satır 503) |
| 2 | **Kanıtsız model production'a girmez** | Gate sonucu (evaluation) kaydı olmadan yayma yasak |
| 3 | **Geri alınabilirlik** | Her yayma, bir önceki sürüme dönüşle eş anlamlıdır |
| 4 | **Lisans önce** | Lisansı belirsiz model getirilmez/kullanılmaz |
| 5 | **İzlenebilirlik (provenance)** | Kaynağı, indirildiği tarih, dönüştürücü her adımda kayıtlıdır |

### §2.2 Zero-hallucination bağlayıcılığı

1. "Bu model üretimde" iddiası **kayıt kanıtıyla** (sürüm + gate + tarih) desteklenir; kayıt yoksa `⚠️ VERIFICATION REQUIRED`.
2. Harici kaynak modellerde (model kartı iddiaları: accuracy, boyut, dil) **kaynak URL + tarih** ile aktarılır — kart iddiası **doğrulanmadan kesin sayılmaz**.
3. Registry'de olmayan model **var sayılmaz**; tersi de doğrudur: kayıtta var ama dosya yoksa durum `MISSING` işaretlenir (uydurulmaz, işaretlenir).
4. Sayısal değerler (boyut, parametre, indirme süresi) ya ölçülmüş ya kaynaklıdır — tahmin yok.

---

## §3. Mimari Roller ve Kararlar

### §3.1 Yaşam döngüsü akışı

```
[GETİR] harici kaynak → indir → checksum + lisans denetimi
   ↓
[KAYIT] model kartı aç (sürüm, metadata, gate sonucu)
   ↓
[ADIY] evaluation kapılarından geçir (§7)
   ↓
[STAGING] sınırlı ortam/trafik → sistem metrikleri
   ↓
[ÜRETİM] inference'a yayın (+ local paket teslimi §8)
   ↓
[GÖZLEM] drift/regresyon takibi (inference + evaluation)
   ↓
[EMEKLİ] retired: üretimden çekil, arşiv/saklama politikası
```

**Karar (K-MM-01):** Geçişler **tek yönlü ve kapılarla** yapılır — `production`'a doğrudan atlayan geçiş yok (bu, inference §27 rollout sırasıyla aynı felsefedir).
**Kanıt:** ai-models.md §8.1 (satır 503) kaynak getirme çerçevesi; §7.2 (satır 434) iş kuyruğu modeli.

### §3.2 Sahiplik matrisi

| Alan | Sorumluluk | Not |
|------|-----------|-----|
| Kaynak getirme (Hub vb.) | model-management | §5.1 |
| Kart/şema/sürüm | model-management | §4 |
| Gate uygulaması (eşik) | evaluation | sonucu buraya yazılır |
| Yayma/rollback | model-management + inference | §8 |
| Yerel paket teslimi | model-management → local-inference | §8.2 |
| Retire kararı | model-management | §6.4 |

**Karar (K-MM-02):** Gate **eşiğini** evaluation tanımlar, gate **uygulamasını** model-management yapar, gate **sonucunu** kayıt model-management'dedir — kimse hem tanımlar hem uygular değil (separation of duties).

---

## §4. Model Kartı Şeması (zorunlu alanlar)

Her kayıtta bulunması zorunlu alanlar:

| Alan | Zorunlu | Açıklama |
|------|---------|----------|
| `id` | Evet | kalıcı kimlik (`alan/gorev-modeli`) |
| `version` | Evet | semver ya da tarih-sürümü; **asla** etiketsiz |
| `source` | Evet | köken (kaynak URL/ekip/egitim kaydı) veya `internal` |
| `checksum` | Evet | SHA-256 (paket bütünlüğü — local-inference §5.4) |
| `license` | Evet | SPDX benzeri ifade ya da `UNKNOWN` → **getirme durdurulur** |
| `format` | Evet | `onnx` / `safetensors` / `gguf` / diğer |
| `size_bytes` | Evet | gerçek dosya boyutu (ölçülmüş) |
| `metrics_ref` | Evet | gate raporu referansı (evaluation §25 raporu) |
| `owner` | Evet | bakım sahibi ekip/rol |
| `status` | Evet | `candidate` \| `staging` \| `production` \| `retired` |
| `intended_use` | Evet | ne için / ne için DEĞİL (yanlış kullanım sınırı) |
| `bias_notes` | Evet | bilinen önyargı notları — boş bırakılamaz (`yok` yazılır) |
| `created_at` / `promoted_at` / `retired_at` | Evet | tarih damgaları |

**Karar (K-MM-03):** `license` = `UNKNOWN` olan model **getirilmez** (L4 ile uyum); `bias_notes` boş olamaz — "bilinen önyargı yok" bile **yazılı** bir ifadedir.
**Kaynak:** ai-models.md §8.1 (satır 503) getirme/adlandırma çerçevesi; şema alanları bu anayanadan gelir.

---

## §5. Kaynak Getirme Usulü (Harici Model)

### §5.1 İçe aktarma adımları

```
1. Kaynak doğrula     → resmi depo/metadata (URL + tarih kaydedilir)
2. İndir               → doğrulanmış kanal, sürüm sabitleme (floating tag YASAK)
3. Bütünlük            → SHA-256 hesapla, kayıtlı değerle karşılaştır
4. Lisans denetimi     → allow-list; UNKNOWN/BSD-uyumsuz → RET (K-MM-04)
5. Zararlı içerik taraması → (model dosyası = çalıştırılabilir tahmin kodu riski)
6. Kayda al            → §4 şeması, ilk gate ataması `candidate`
7. İndirme işi kuyruğa → asenkron (ai-models.md §7.2, satır 434 ile aynı iş kuyruğu modeli)
```

**Karar (K-MM-04):** Lisans **allow-list**'i vardır (açık kaynak + ticari kullanım uyumlu); listeye girmeyen lisans istisnasız RET'tir — "sonra bakılır" yoktur.

### §5.2 Sürüm sabitleme kuralı

1. `latest`/`main` gibi **yüzen etiket** üretim kaydı olarak **yasak** — her zaman belirli sürüm/digest.
2. Kaynak güncellenirse **yeni sürüm** açılır; eski kayıt **dokunulmaz** (append-only kayıt geçmişi).
3. Kaynak silinirse (repo kapanırsa) yerel kopya **arşiv** sayılır ve `source_gone` etiketi alır.

---

## §6. Sürümleme ve Geçmiş (Versioning)

### §6.1 Sürüm semantiği

| Değişim | Sürüm etkisi | Örnek |
|---------|--------------|-------|
| Aynı model, yeniden paketleme (quantize) | minor + `variant` alanı | `1.4.0+int8` |
| Ağırlık değişimi (fine-tune/farklı eğitim) | **major** (kalite değişebilir) | `2.0.0` |
| Yalnız metadata düzeltmesi (kart) | patch | `1.4.1` |
| Kapıdan düşen sürüm | status değişir, sürüm sabit | `1.3.0: retired` |

**Karar (K-MM-05):** Quantization **varyant**tır, yeni model **değildir** — varyant, ana sürümle aynı gate geçmişini **paylaşır ama kendi accuracy delta ölçümü** taşır (local-inference K-LOC-05).

### §6.2 Geçmiş kaydı (append-only)

1. Kayıt tablosuna **satır eklenir**; geçmiş satırı **asla silinmez/ezilmez**.
2. `status` geçişleri ayrı olay kaydıdır (kim, ne zaman, gate sonucu, gerekçe).
3. Geri alma (rollback) = yeni bir olay kaydı (`promoted: 1.3.0` tekrar) — geçmiş manipülasyonu değil.

### §6.3 Referans bütünlüğü

Üretimde çağrı referansı (ör. `AIEngine::MODEL_NAME` sabitleri, `shared/src/AI/AIEngine.php` satır 30–32) her zaman **kayıtlı bir sürüm**ne karşılık gelmelidir; gelmiyorsa durum `dangling_ref` → yayma bloklanır.

### §6.4 Emekliliye ayırma (retire)

| Koşul | Aksiyon |
|-------|---------|
| Yeni sürüm yayında + gözlem penceresi kapandı | eski `retired` |
| Gate düşüşü (kalite) | anında üretimden çekil + rollback (§8.3) |
| Kaynak/git kaybı | `source_gone` etiketli arşiv |
| Lisans geri çekildi | `DISABLED` — kullanım durdurulur |

**Retire sonrası:** dosya **silinmez** (arşiv + kanıt saklama); üretimden **erişim** kaldırılır.

---

## §7. Kapı (Gate) Entegrasyonu

### §7.1 Kapıların sırası

```
getirme kapıları (§5.1: bütünlük, lisans, tarama)
  → evaluation gate (kalite kapı matrisi)
    → staging gate (sistem metrikleri — inference §13)
      → production yayma (§8)
```

### §7.2 Gate kayıt zorunluluğu

1. Her gate **sonucu** (PASS/FAIL + metrik + tarih + rapor referansı) model kaydına yazılır (`metrics_ref`, §4).
2. Gate'siz geçiş **yoktur** — elle "onayladım" imzası gate **yerine geçmez** (gate = ölçüm, imza = sorumluluk; ikisi ayrı).
3. FAIL olan sürüm `candidate`'te kalır; **yeniden ölçüm** ile tekrar denenir (sayı uydurarak geçiş yasak).

**Karar (K-MM-06):** Gate sonucu **model kaydına bağlıdır** — raporda yazan ile kayıtta yazan aynıdır; farklıysa kayıt **esas**tır ve rapor düzeltilir.

---

## §8. Yayma (Distribution)

### §8.1 Servis/inference'a yayma

1. Sürüm etiketi güncellenir (referans §6.3) — **tek noktadan** değişir.
2. Eski sürüm bir süre **yanında kalır** (rollback hazır) — gözlem penceresi kapanınca erişim kısıtlanır.
3. Yayma olayı loglanır (sürüm + gate + sahip).

### §8.2 Yerel paket teslimi (local-inference)

1. Model, yerel formatına dönüştürülürse **varyant kaydı** açılır (K-MM-05).
2. Paket metadata + checksum ile teslim edilir (local-inference §5.4) — **teslim, kayıtla eşleşmelidir**.
3. Cihaz uyumsuzluğu (platform `UNKNOWN` — local-inference K-LOC-03) → o platformda **yayma yok**, kayıt `not_supported` alanı ile işaretlenir.

### §8.3 Rollback

| Tetik | Süreç |
|-------|-------|
| Gate düşüşü (canary/online) | referansı eski sürüme al → cache/kayıt senkron (inference §28.4) |
| Sistem metrik bozulması | aynı; ayrıca **neden** kaydına olay eklenir |
| Manuel karar | ADR/olay kaydı + iki kişi onayı (evaluation §19.1 ile aynı disiplin) |

**Kural:** Rollback **bir komut** mertebesinde olmalıdır — yavaş rollback = uzun kesinti.

---

## §9. Rehberler (Guidelines)

1. **Tek registry** — model listesi birden çok yerde tutulmaz (dağıtık kayıt = gerçeklik kaybı).
2. **Floating tag yasak** — §5.2.
3. **Kart canlı tutulur** — `intended_use`/`bias_notes` değişiminde kart güncellenir (yeni sürüm satırı ile).
4. **İndirme asenkron** — arayüz kuyruğu beklemez (ai-models.md §7.2, satır 434 iş kuyruğu modeli).
5. **Arşiv politikası** — retired modeller silinmez; saklama süresi politikası `UNKNOWN` (AÇIK — §12.4).
6. **Adlandırma disiplini** — `alan/gorev-modeli` deseni; serbest adlandırma yasak.
7. **Kart + dosya birlikte** — kartı olmayan dosya bulunmaz, dosyası olmayan kart `MISSING`.
8. **Yayma sonraysa kayıt değil** — önce kayıt, sonra yayma (tersi yasak — §3.1).

---

## §10. Zorunlu Kurallar (MUST / MUST NOT)

| # | Kural | Seviye |
|---|-------|--------|
| M1 | Her üretim modelinin **sürümü + checksum'ı + gate kaydı** olmalıdır | MUST |
| M2 | `latest`/yüzen etiket üretim referansı olamaz | MUST NOT |
| M3 | `license: UNKNOWN` olan model getirilemez (K-MM-04) | MUST |
| M4 | Gate'siz/ölçüm'süz sürüm production'a alınamaz (K-MM-06) | MUST |
| M5 | Geçmiş kayıt **append-only** — silme/ezme yok (§6.2) | MUST NOT |
| M6 | Rollback **tek adımda** mümkün olmalıdır (§8.3) | MUST |
| M7 | `bias_notes` alanı boş bırakılamaz (yoksa "yok" yazılır) | MUST |
| M8 | Retired modelin dosyası **silinmez**, arşivlenir (§6.4) | MUST NOT |
| M9 | Yerel teslim paketi **kayıtla eşleşmelidir** (§8.2.2) | MUST |
| M10 | Harici kart iddiaları **kaynaklı** aktarılır (§2.2.2) | MUST |
| M11 | Üretim referansı kayıtlı sürüme uymuyorsa yayma **bloklanır** (`dangling_ref`, §6.3) | MUST |

---

## §11. Karar Kaydı

| Karar | İçerik | Kanıt |
|-------|--------|-------|
| K-MM-01 | Yaşam döngüsü kapılarla, tek yönlü | ai-models.md §8.1, satır 503 |
| K-MM-02 | Eşik (evaluation) ≠ uygulama (mm) ≠ kayıt (mm) | bu dosya §3.2 |
| K-MM-03 | Kart şeması zorunlu; `UNKNOWN` lisans RET | bu dosya §4 |
| K-MM-04 | Lisans allow-list, istisnasız RET | bu dosya §5.1 |
| K-MM-05 | Quantize = varyant; paylaşımlı gate + kendi delta ölçümü | local-inference K-LOC-05 |
| K-MM-06 | Gate sonucu kayda bağlı; kayıt esas | bu dosya §7.2 |
| K-MM-07 | İndirme/doğrulama işleri asenkron kuyrukta | ai-models.md §7.2, satır 434 |

**Reddedilen alternatifler:**

- *"Yayınlayıp sonra bakarız"* → geri dönüş maliyeti yüksek (K-MM-01).
- *Tek dosya klasörü (sürüm yok)* → izlenemez (K-MM-01, §6).
- *Lisansı sonra denetleriz* → dağıtım ihlali (K-MM-04).
- *Gate sonucunu e-postada tutmak* → kanıt dağılır, kopar (K-MM-06).

---

## §12. Bağımlılıklar

### §12.1 Bağımlı olduğu alanlar

| Alan | Ne alır | Kaynak |
|------|---------|--------|
| `evaluation` | gate sonucu/metric referansı | `../evaluation/` |
| `inference` | üretim davranışı/drift sinyalleri (retire tetiği) | `../inference/` |
| `local-inference` | paket formatı + checksum sözleşmesi | `../local-inference/` |
| `testing` | kart/şema testleri, CI kapıları | `../testing/` |

### §12.2 Bağımlı olan alanlar

| Alan | Ne verir |
|------|----------|
| `inference` | sürüm referansı (§6.3) — üretim çağrı adresi |
| `local-inference` | paket + metadata (§8.2) |
| `evaluation` | `metrics_ref` geri bağlantısı (§4) |

---

## §13. İlişkili Dosyalar

### §13.1 Bu dalın kardeş dosyaları

| Dosya | Rol | Özet |
|-------|-----|------|
| `index.md` | dal indeksi | §GRUPLAR + çocuklar |
| `model-management-kapsam.md` | kapsam | yaşam döngüsü sınırı |
| `model-management-bilesenler.md` | bileşenler | kart, registry, kuyruk, teslim |
| `model-management-dogrulama.md` | doğrulama | kayıt↔dosya↔gate üçlüsü |
| `model-management-ornekler.md` | örnekler | kayıt/yayma örnek akışları |
| `model-management-web-arastirma.md` | web araştırma | Hub/kayıt standartları taraması |
| `../yapay-zeka-kararlar.md` | kök K006 kararları | bu dal kararlarının üst otoritesi |
| `../yapay-zeka-riskler.md` | kök K006 riskleri | sürüm/lisans/kayıp riskleri buraya işlenir |

### §13.2 Vault referansları

| Dosya | Ne verir | Satır |
|-------|----------|-------|
| `.ai/architecture/K006-yapay-zeka/index.md` | çocuk haritası (v2 §2.6) | 259 |
| `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | §8.1 HuggingFace getirme | 503 |
| `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | §7.2 iş kuyruğu | 434 |
| `shared/src/AI/AIEngine.php` | MODEL_NAME sabitleri (referans yüzeyi) | 30–32 |
| `../evaluation/evaluation-anayasa.md` | gate matrisi | §16 |

**Seçim kuralı:** şema → §4; getirme → §5; sürüm → §6; yayma/rollback → §8; karar kaydı → bu dosya §10–§11 (K-MM-*); risk kaydı → `../yapay-zeka-riskler.md` (kök K006).

---

## §14. Açık Sorular (open_questions detayı)

1. **Registry altyapısı** — harici Hub mı, dahili depo mu (veya ikisi: Hub getir + dahili arşiv)? Karar verilmedi → `UNKNOWN` (§3 giriş referansı yok, uydurulmadı).
2. **Kart zorunlu alanı ADR'si** — §4 şeması bu anayasa düzeyinde; resmi ADR (`\.ai/.decisions/`) bekliyor.
3. **Saklama süresi** — retired model arşivi ne kadar tutulur? `UNKNOWN` (Rehber 5).
4. **Cache ilişkisi** — retire edilen modelin üretim cache'leri (inference §14) TTL üstü mü temizlenir? Doğrulanmalı → `⚠️ VERIFICATION REQUIRED`.
5. **Kaynak kaybı prosedürü** — `source_gone` sonrası yeniden getirme possible mu (lisans sahibi değişimi)? Prosedür yok.

---

## §15. İlgili Lint Maddeleri

| LINT (`.ai/CLAUDE.md` §6) | Bu anayasadaki karşılığı |
|---------------------------|--------------------------|
| Zero-hallucination | §2.2 — kart iddiaları kaynaklı, kayıt kanıtlı |
| `UNKNOWN` / `VERIFICATION REQUIRED` | §2.2.3, §14 |
| Kanıt zorunluluğu | §11 karar tablosu (satır referanslı) |
| Append-only vault | §6.2, M5 |
| Eşik keyfiliği yasağı | K-MM-06 — eşik evaluation'dan gelir |

---

## §16. Bağlam ve Versiyon

| Alan | Değer |
|------|-------|
| Sahip | K006-yapay-zeka / model-management |
| Ebeveyn | `K006-yapay-zeka` (kök `index.md` §2.6 — v2 satır 259) |
| Kademe | 2 |
| Durum | active |
| Oluşturulma | 2026-10-15 |
| Kapsanan sibling'lar | `model-management-kapsam` · `-bilesenler` · `-dogrulama` · `-ornekler` · `-web-arastirma` · `index.md` |
| Grup | mimari (`group:` frontmatter) |
| Belge tipi | anayasa (kademe-2, bu dalın usul belgesi — şablon değil) |

---

## §17. Bileşen Sözlüğü (TR↔EN, bu dalın terimleri)

| TR | EN | Bu anayasadaki anlamı |
|----|----|------------------------|
| Model kaydı | model registry entry | §4 şemasındaki kayıt satırı |
| Model kartı | model card | metadata + intended use + bias notu |
| Getirme | fetch / pull | harici kaynaktan indirme (§5.1) |
| Varyant | variant (quantized) | aynı modelin farklı paketleme biçimi (K-MM-05) |
| Aday | candidate | gate'den geçmemiş sürüm |
| Aşamalandırma | staging | sınırlı ortam yayma (§3.1) |
| Yayma | promote / release | üretim referansına bağlama (§8.1) |
| Geri alma | rollback | tek adımda eski sürüme dönüş (§8.3) |
| Emekli | retired | üretimden çekilmiş, arşivde (§6.4) |
| Sarkan referans | dangling ref | kayıtsız sürüme işaret eden üretim referansı (§6.3) |
| Kaynak kaybı | source gone | harici kaynağın erişilemez oluşu (§5.2.3) |

**Terim kuralı:** Tabloda olmayan terim bu dalda **icat edilmez** — önce kardeş `inference-anayasa.md` §9 sözlüğüne bakılır.

---

## §18. Durum Matrisi (model kaydı durumları)

| Durum | Tanım | İzinli geçişler |
|-------|-------|------------------|
| `EMPTY` | kayıt yok (dosya varsa bile) | → `candidate` (kayda alma) |
| `CANDIDATE` | kayıtlı, gate bekliyor/FAIL | → `staging` (gate PASS), → `MISSING` |
| `STAGING` | sınırlı yayında | → `production` (staging gate), → `candidate` (geri) |
| `PRODUCTION` | üretimde | → `retired` (normal), → `staging`/eski (rollback §8.3) |
| `RETIRED` | arşivde, üretim dışı | → `MISSING` (dosya kaybı) |
| `MISSING` | kayıt var, dosya yok | → `retired` düzeltme (yeniden getirme §14.5) |
| `DISABLED` | lisans/politika durdurması | → yeniden denetimle `candidate` |

**Zorunlu kural:** Her geçiş **olay kaydı** üretir (kim/ne zaman/neden/gate sonucu); geçişsiz durum **yoktur** — `PRODUCTION → CANDIDATE` (doğrudan düşürme) **yalnız** gate düşüşü ve rollback ile mümkündür (M4).

---

## §19. Hata Sınıfları ve Kayıt Davranışı

| Sınıf | Örnek | Kayıt davranışı | Kullanıcıya/üretime |
|-------|-------|-----------------|---------------------|
| İndirme | ağ/kaynak hata | iş tekrar dener (kuyruk §9.4) | gecikme, durum `EMPTY` |
| Bütünlük | checksum uyuşmadı | dosya reddi, olay kaydı | model **asla** yayılmaz |
| Lisans | allow-list dışı | RET + gerekçe | getirme durdurulur (K-MM-04) |
| Şema | kart alanı eksik | kayıt kabul edilmez | `dangling_ref` riski önlenir |
| Gate | metrik eşik altı | `candidate` kalır | üretim referansı değişmez |
| Teslim | paket↔kayıt uyuşmazlığı | teslim durdurulur | cihazda eski sürüm kalır (bilinçli) |
| Kaynak | repo kapandı | `source_gone` etiketi | arşiv devam eder |

**Kural (K-MM-08):** Hata **sessiz yutulmaz** — her sınıf için durum + olay kaydı üretir; üretim referansı **yalnız** gate PASS'te değişir (M4).

---

## §20. Güvenlik ve Güven (bu dalın yüzeyi)

1. **İndirme kanalı güvenilir** — yalnız doğrulanmış kaynak (§5.1.1); doğrulanmamış aynadan getirme yasak.
2. **Zararlı içerik** — model dosyası tahmin kodu çalıştırır (format'a bağlı); tarama adımı §5.1.5'te zorunlu, sonucu kayıtta (`scan_ref` alanı opsiyonel ama tarama olayı zorunlu).
3. **Erişim kontrolü** — `production` kayıt değişimi yetkili rol ile (gate imzası + rol doğrulama — evaluation §19.1 iki-kişi kuralı ile uyumlu).
4. **PII** — model kartı ve kayıt alanları **ham müşteri verisi içermez**; `bias_notes` istatistik/çıkarım düzeyindedir.
5. **İmza zinciri** — paket imzası `UNKNOWN` (local-inference §13.3 ile aynı açık) → riskler dosyasında kayıtlı.

---

## §21. Anti-Patternler (yasak kısayollar)

| Anti-pattern | Neden zararlı | Doğrusu |
|--------------|---------------|---------|
| "Klasöre at, üretim okusun" | sürüm/izin yok | kayıtlı sürüm (M1) |
| "latest her zaman güncel" | sürpriz değişiklik | sürüm sabitleme (M5.2) |
| "Lisansı sonra okuruz" | ihlal riski | önce denetim (K-MM-04) |
| "Kartı sonra yazarız" | provenance kopuk | kart = kayıt şartı (§4) |
| "Rollback'i yarın yaparız" | uzun kesinti | tek komut (M6) |
| "Retired'ı sil gitsin" | kanıt kaybı | arşiv (M8) |
| "Gate raporu e-postada dursun" | kanıt dağılır | `metrics_ref` (K-MM-06) |
| "Yayınla, ölçeriz" | ters sıra | kapı önce (§3.1) |
| "Kartta %99 doğruluk yazıyor" | kart iddiası ≠ ölçüm | kaynaklı aktarım + gate (§2.2.2) |

---

## §22. Uç Nokta Sözleşmesi (giriş/çıkış)

### §22.1 Giriş

| Girdi | Sağlayıcı |
|-------|-----------|
| Harici model (kaynak URL + sürüm) | dış kaynak (Hub vb.) |
| Gate sonucu (PASS/FAIL + rapor ref) | `evaluation` |
| Paket formatı sözleşmesi | `local-inference` |
| Rollback/drift sinyali | `inference` |
| Şema/tümü test tetikleyicileri | `testing` |

### §22.2 Çıkış

| Çıktı | Alıcı | Biçim |
|-------|-------|-------|
| Sürüm referansı (üretim adresi) | `inference` (AIEngine sabitleri vb.) | §6.3 |
| Paket + metadata + checksum | `local-inference` | §8.2 |
| `metrics_ref` geri bağlantısı | `evaluation` | §4 |
| Olay/günlük kayıtları | `testing`/denetim | §18, §19 |
| ADR taslakları (eşik/saklama) | `.ai/.decisions/` | §11 |

---

## §23. Kaynaklar ve Red-Team Soruları

### §23.1 Kaynaklar

| # | Kaynak | Kullanım |
|---|--------|----------|
| R1 | `K006-yapay-zeka/index.md` [Satır: 259] | ebeveyn bağlantısı |
| R2 | `ai-models.md` [Satır: 503] | §8.1 kaynak getirme |
| R3 | `ai-models.md` [Satır: 434] | §7.2 iş kuyruğu |
| R4 | `shared/src/AI/AIEngine.php` [Satır: 30–32] | referans yüzeyi (sabitler) |
| R5 | Kardeş anayasalar (§13.2) | gate, paket, rollback çerçeveleri |
| R6 | Web kaynağı | AÇILMADI — bu turda URL eklenmedi (`model-management-web-arastirma.md`'ye devredildi) |

### §23.2 Red-Team soruları

| # | Soru | Beklenen cevap |
|---|---|---|
| T1 | `latest` ile referans verilirse? | kayıt kabul edilmez — M2, §5.2 |
| T2 | Lisanssız model sessizce indirilirse? | RET + olay — K-MM-04, §19 |
| T3 | Kartı olup dosyası olmayan sürüm? | `MISSING` — M7/§18 |
| T4 | Gate FAIL ile production'a taşınır mı? | blok — M4, K-MM-06 |
| T5 | Rollback 3 saat sürerse? | ihlal — M6 |
| T6 | Üretim sabiti eski sürümü mü gösteriyor? | `dangling_ref` → yayma blok — M11 |
| T7 | Retired dosya silinirse? | ihlal — M8 (kanıt kaybı) |
| T8 | Kart iddiası ölçümle çelişirse? | gate/esas ölçüm kazanır — §2.2.2 |

### §23.3 Revizyon Kaydı

| Tarih | Değişiklik | Yapan | Not |
|---|---|---|---|
| 2026-10-15 | İlk yazım (dosya oluşturma) | K006 vault içerik ajanı | Append-only; §1–§23 |
| — | Sonraki revizyon | — | Yalnız ek bölüm olarak |

**Authority:** Bayram Ali / Vault Steward · **status:** active — §14 açık soruları ADR ile kapatılır.
