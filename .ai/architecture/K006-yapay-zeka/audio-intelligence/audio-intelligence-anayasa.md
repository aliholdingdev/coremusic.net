---
node_id: K006-yapay-zeka/audio-intelligence/audio-intelligence-anayasa
node_type: module
status: active
parent_id: K006-yapay-zeka/audio-intelligence
children:
  - K006-yapay-zeka/audio-intelligence/audio-intelligence-kapsam
  - K006-yapay-zeka/audio-intelligence/audio-intelligence-bilesenler
  - K006-yapay-zeka/audio-intelligence/audio-intelligence-dogrulama
  - K006-yapay-zeka/audio-intelligence/audio-intelligence-ornekler
  - K006-yapay-zeka/audio-intelligence/audio-intelligence-web-arastirma
depends_on:
  - K006-yapay-zeka/inference/inference-anayasa
  - K006-yapay-zeka/music-analysis/music-analysis-anayasa
  - K006-yapay-zeka/evaluation/evaluation-anayasa
evidence:
  - "Dosya: .ai/architecture/K006-yapay-zeka/index.md [Satır: 264]"
  - "Dosya: .ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md"
  - "Dosya: .ai/projects/NevaEngine/legacy-2026-07/spatial-audio.md"
validation: "İçerik doğrulama: 2026-10-15 — anayasa, kök index.md §2.6 (satır 264) ve NevaEngine eq-ai-auto/spatial-audio başlık indeksleriyle eşleştirildi"
open_questions:
  - "AI-DSP zincirinin gerçek zamanlı (RT) kısıtları içinde model çalıştırma bütçesi UNKNOWN — Neva RT callback kurallarıyla doğrulanmalı"
  - "Otomatik EQ'nun öğrenme sinyali (kullanıcı tepkisi vs. içerik analizi) kaynağı netleştirilmedi"
  - "Uzamsal (spatial) AI bileşenlerinin kapsam sınırı (K006 vs. Neva DSP) ADR bekliyor"
group: mimari
---
# Audio Intelligence — Alt Dal Anayasası (kademe-2)

> **Bu dosya kademe-2 anayasadır.** Ebeveyn: `K006-yapay-zeka` (bkz. `index.md` §2.6 çocuk haritası — v2 satır 264, `G:\` arşivinde).
> Kardeş anayasalar: `../music-analysis/music-analysis-anayasa.md`, `../inference/inference-anayasa.md`, `../evaluation/evaluation-anayasa.md`.
> Bu dosya **şablon içermektedir** — tüm alanlar somut değerle doldurulmuştur.

---

## §1. Kapsam Sınırı

**audio-intelligence**, K006'da **ses zincirinde (DSP chain) akıllı karar üretimi** disiplinini kapsar — model/analiz çıktısının **ses işleme parametrelerine** dönüştürülmesi. Kapsayıcılar:

1. **Otomatik EQ kararları** — içerik/kullanıcı sinyalinden EQ hedef eğrisi üretimi (`.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md`).
2. **Uzamsal/ses mekânı kararları** — spatial ses parametre üretimi (`.ai/projects/NevaEngine/legacy-2026-07/spatial-audio.md`).
3. **Zincir üstü akıllı kontrol** — DSP zinciri (`.ai/projects/NevaEngine/02-dsp-zincir-mimari.md`) üzerinde AI devreye alma stratejisi.
4. **RT uyumluluk** — gerçek zamanlı ses callback'inin katı kuralları içinde AI kullanımı (`.ai/projects/NevaEngine/03-isletim-sistemi-kod-ornekleri.md` §2 ASIO, satır 51).

**Kapsam dışı:**

- DSP **algoritmasının kendisi** (filtre, reverb matematiği) → Neva DSP / `.ai/electronic/dsp/`
- Parça **hakkındaki bilgi** (tür, söz) → `music-analysis`
- AI modelinin **eğitimi/sürümü** → `model-management`
- AI'ın **canlı çağrısı/önbelleği** → `inference` (burada yalnız *karar sözleşmesi* tanımlanır)

**Kapsam sınırı testi:** "Çıktı **ses parametresi/EQ eğrisi/spatial hedef** mi?" → Evet = audio-intelligence. "Çıktı **etiket/öneri/profil** mi?" → diğer dallar.

---

## §2. İlke ve Değerler

### §2.1 Öncelik sırası

| # | İlke | Gerekçe |
|---|------|---------|
| 1 | **RT (real-time) kutsaldır** | Ses callback'inde bloklama/taşma ses kesintisi üretir (03-isletim-sistemi §2, satır 51) |
| 2 | **AI, RT yolunun misafiridir** | Model tahmini RT dışında hazırlanır; RT yalnız **almış-parametreyi** uygular |
| 3 | **Ses kalitesi ölçülür** | Subjectif tercih yanıltıcı — STOI/PESQ benzeri ölçüm (evaluation §4.1) |
| 4 | **Geri alınabilir otomasyon** | Otomatik EQ/spatial her an manuel moda dönebilir |
| 5 | **Gürültü/bozulmaya karşı sakinlik** | AI çıktısı titriyorsa (jitter) ses duyulur — smoothing zorunlu |

### §2.2 Zero-hallucination bağlayıcılığı

1. RT bütçe sayıları (μs/ms, buffer boyutu) yalnız **kaynaklı** yazılır; kaynağı yoksa `UNKNOWN` (RT bütçesi Neva kodu/ölçüm gerektirir).
2. "AI EQ sesi iyileştirdi" tipi iddialar **ölçüm + işitsel test** ister; kanıtsız `⚠️ VERIFICATION REQUIRED`.
3. Spatial/EQ kapsam sınırları vault kaynaklarıyla çizilir — vault dışı iddia (hangi efekt AI) yazılmaz.
4. Öğrenme sinyali kaynağı belirsizse `UNKNOWN` (§12.3.2).

---

## §3. Mimari Roller ve Kararlar

### §3.1 Katman ayrımı (RT/Non-RT)

```
┌───────────────────────────────────────────────────────────┐
│ NON-RT ALAN (AI düşünme alanı)                            │
│  • içerik analizi (music-analysis girdisi)                │
│  • model tahmini (inference — önbellek, batch)            │
│  • hedef EQ eğrisi / spatial hedef hesabı                 │
│  • smoothing + doğrulama + güvenlik kırpma                │
├───────────────────────────────────────────────────────────┤
│ KÖPRÜ (lock-free kuyruk / atomic parametre paylaşımı)     │
│  • non-RT yazar → RT okur (tek yön)                       │
├───────────────────────────────────────────────────────────┤
│ RT ALAN (audio callback — ASIO/WASAPI)                    │
│  • yalnız parametreyi uygular: filtre katsayıları vb.     │
│  • model çağırmaz, bloklama yok, tahsis yok               │
└───────────────────────────────────────────────────────────┘
```

**Karar (K-AI-01):** **Model tahmini RT dışında** çalışır; RT yolu yalnız hazır parametreyi uygular (poll/kuyruk) — bu ayrım **değiştirilemez** (ses kesintisi garantili ihlal).
**Kanıt:** `03-isletim-sistemi-kod-ornekleri.md` §2 ASIO (satır 51) RT kısıtları; `eq-ai-auto.md` otomatik EQ çerçevesi.

### §3.2 Sahiplik matrisi

| Alan | Sorumluluk | Not |
|------|-----------|-----|
| AI karar üretimi (hedef eğri) | audio-intelligence | bu anayasa |
| Model çağrısı/önbellek | `inference` | §6 |
| DSP filtresinin matematiği | Neva DSP (K006 dışı) | bu dal **parametre ister**, filtre **yazmaz** |
| RT köprü/kuyruk | Neva Engine (embedded) | bu dal **sözleşmeyi** tanımlar |
| Ölçüm (ses kalitesi) | `evaluation` + Neva test | §8 |

**Karar (K-AI-02):** audio-intelligence **karar üretir**, **sesi işlemez** — DSP zincirinin sahibi Neva DSP'dir; buradan verilen tek şey **parametre hedefidir** (ör. EQ eğrisi noktaları).

---

## §4. Bileşen Kataloğu

| Bileşen | Girdi | Çıktı | Durum |
|---------|-------|-------|-------|
| Otomatik EQ karar | içerik özniteliği / ölçüm | hedef EQ eğrisi | **kanıtlanabilir** — `eq-ai-auto.md` |
| Uzamsal karar | içerik/ortam | spatial hedef parametreler | **kanıtlanabilir** — `spatial-audio.md` |
| Zincir devreye alma | mod/çalma durumu | zincir bypass/aktif kararı | `02-dsp-zincir-mimari.md` ilişkili |
| Gerçek-zamanlı ölçüm (loudness vb.) | ses akışı | ölçüm değeri (RT-güvenli) | ölçüm/analiz — `UNKNOWN` detay |
| Model-özel öğrenme (adaptif EQ) | kullanıcı tepkisi | ağırlık güncellemesi | **UNKNOWN** — sinyal kaynağı §12.3.2 |

**Kural (K-AI-03):** `UNKNOWN` bileşen **üretime alınmaz** — kapsam + ölçüm + RT bütçesi kararlaştırıldıktan sonra (audio-intelligence-kapsam.md).

---

## §5. RT Bütçesi ve Köprü Sözleşmesi

### §5.1 RT kuralları (bu dalın bağlayıcı kısımları)

| # | Kural | Seviye |
|---|-------|--------|
| RT1 | Audio callback'inde **model inference yok** (K-AI-01) | MUST |
| RT2 | Callback'te **bellek tahsisi/serbest bırakma yok** | MUST NOT |
| RT3 | Non-RT → RT geçişi **lock-free** (atomic/kuyruk); kilit **yasak** | MUST |
| RT4 | Parametre **anlık sıçrama** yerine **smoothing** (ramp) | MUST (İlke 5) |
| RT5 | Bütçe aşımında karar **düşülür** (drop), RT **bloklanmaz** | MUST |
| RT6 | Tüm AI kararları **geri alınabilir** (manual override) | MUST (İlke 4) |

### §5.2 Köprü veri akışı

```
non-RT: tahmin → smoothing → doğrulama (kapsam kırpma) → kuyruğa yaz (atomik)
RT:     kuyruktan oku (varsa) → ramp uygula → filtre katsayısı
```

**Karar (K-AI-04):** Köprüde taşma (overflow) durumunda **eski değer korunur**, yeni değer **düşülür ve sayılır** (metrik) — RT asla bekletilmez.

### §5.3 Bütçe (sayısal durum)

RT içinde AI köprüsüne ayrılan süre/buffer: **`UNKNOWN`** — Neva Engine ölçümü gerektirir (`.ai/projects/NevaEngine/` kaynakları okundu; sayısal eşik bu turda aktarılmadı → §12.3.1).

---

## §6. Hedef Üretim ve Güvenlik Kırpma

### §6.1 Hedef üretim zinciri

```
sinyal (içerik/ölçüm) → model tahmini (non-RT, inference önbelleği)
  → hedef parametre (EQ eğrisi / spatial)
    → güvenlik kırpma (limitler: dB aralığı, frekans sınırları)
      → smoothing (zaman sabiti)
        → köprü (§5.2) → RT uygulama
```

### §6.2 Güvenlik kırpma (clamping) kuralları

| Parametre sınıfı | Kural |
|------------------|-------|
| EQ kazancı | önceden tanımlı dB min/maks (değer `UNKNOWN` → Neva DSP sınırı) |
| Frekans/kırpma noktası | geçerli bant içi |
| Spatial miktar | %0–100; aşım kırpılır |
| Değişim hızı (slew) | saniyelik maks değişim — titreme önleme (İlke 5) |

**Zorunlu kural (K-AI-05):** Model çıktısı **kırpmadan** RT'ye **ulaşamaz** — kırpmaya rağmen bozuk/çıkış-dışı çıktı varsa karar **düşülür** + olay kaydı (§8).

---

## §7. Rehberler (Guidelines)

1. **Önce manuel, sonra otomatik** — otomasyon, manuel karşılığı ölçülmeden açılmaz (İlke 4 + §8).
2. **Smoothing parametresi ölçümle** — çok yavaş = tepkisiz, çok hızlı = titreme → `UNKNOWN` ise muhafazakâr (yavaş).
3. **Önbellek** — aynı içerik sınıfı için tekrar tahmin yok (inference §14 ile uyumlu).
4. **Mod farkındalığı** — hoparlör/kulaklık/farklı çıktı cihazı arasında hedefler **farklı olabilir** (cihaz profili `UNKNOWN` → §12.3).
5. **Test sinyali harness'i** — deterministik test sesi ile zincir doğrulanır (test-stratejisi dosyasında).
6. **Geri alma düğmesi** — her otomatik mod manuel/eşik ile kapatılabilir (İlke 4); kullanıcı kapatırsa AI **kalıcı** kapanır (oturum/süre politikası `UNKNOWN` → §12.3).
7. **Kayıt/telemetri** — karar + gerekçe (hangi sinyal) log'u; RT log **hafif** (RT2/RT5).
8. **Ölçüm-gösterge ayrımı** — loudness/ölçüm RT-güvenli olmalı; tespit-özellikleri non-RT'ye çıkarılabilir (bölünmüş tasarım).

---

## §8. Ölçüm ve Doğrulama (evaluation ile ortak)

### §8.1 Birincil metrikler

| Alan | Metrik | Not |
|------|--------|-----|
| Ses kalitesi | işitsel test + (mümkünse) objective metrik (evaluation §4.1 çerçevesi) | "güzelleşti" iddiası ölçüm ister (§2.2.2) |
| Stabilite | parametre titremesi (jitter) oranı | İlke 5 |
| RT uyum | callback aşım/drop sayacı | K-AI-04 metriği |
| Güvenlik kırpma | clamp/drop oranı | K-AI-05 |
| Geri alma | manuel override tetik oranı | İlke 4 |

### §8.2 Kapılar

1. **RT kapısı** — bütçe içi + lock-free + drop davranışı testi (§5).
2. **Sınır kapısı** — clamp/slew testleri (K-AI-05).
3. **Kalite kapısı** — A/B veya işitsel test (evaluation §5.2) — ölçülmeden kalıcı yayma yok.
4. **Geri-alınabilirlik kapısı** — override her zaman çalışır (İlke 4).
5. **Bozulma kapısı** — model hattında hata → zincir **eski sağlam durumda** kalır (fallback: sabit bilinen EQ / bypass).

**Karar (K-AI-06):** AI karar hattı **başarısızsa ses bozulmaz** — varsayılan **kanıtlanmış nötr durum** (bypass/manuel hedef) anında geri gelir (graceful degradation).

---

## §9. Zorunlu Kurallar (MUST / MUST NOT)

| # | Kural | Seviye |
|---|-------|--------|
| AI1 | RT callback'te model inference **yapılmaz** (K-AI-01, RT1) | MUST NOT |
| AI2 | RT'de bellek tahsisi **yapılmaz** (RT2) | MUST NOT |
| AI3 | Non-RT→RT geçiş **lock-free**dir (RT3) | MUST |
| AI4 | Parametre değişimleri **smoothing** ile (RT4, İlke 5) | MUST |
| AI5 | Model çıktısı **sınır-kırpma**dan geçmeden uygulanamaz (K-AI-05) | MUST |
| AI6 | Hata/taşmada karar **düşülür**, RT **bloklanmaz** (RT5, K-AI-04) | MUST |
| AI7 | Her otomatik mod **geri alınabilir** (RT6, İlke 4) | MUST |
| AI8 | Kalite iyileşme iddiası **ölçümle** yazılır (§2.2.2) | MUST |
| AI9 | `UNKNOWN` bütçe/parametre ile **üretim açılmaz** (K-AI-03, §5.3) | MUST |
| AI10 | Başarısızlıkta **kanıtlanmış nötr durum** döner (K-AI-06) | MUST |

---

## §10. Karar Kaydı

| Karar | İçerik | Kanıt |
|-------|--------|-------|
| K-AI-01 | Model RT dışında; RT yalnız parametre uygular | 03-isletim-sistemi §2, satır 51 |
| K-AI-02 | Bu dal karar üretir, DSP işlemez | 02-dsp-zincir-mimari.md ilişkisi |
| K-AI-03 | `UNKNOWN` bileşen üretime kapalı | bu dosya §4 |
| K-AI-04 | Köprü taşmada eski değer + drop sayacı | bu dosya §5.2 |
| K-AI-05 | Clamp/slew zorunlu; bozuk çıktı drop | bu dosya §6.2 |
| K-AI-06 | Hata → nötr durum (graceful degradation) | bu dosya §8.2.5 |
| K-AI-07 | Otomatik mod her zaman manuela döner | bu dosya §7.6, İlke 4 |

**Reddedilen alternatifler:**

- *Callback içinde model çağırmak* → RT ihlali, ses kesintisi (K-AI-01).
- *Kilitli kuyruk (mutex) köprü* → priority-inversion riski (RT3).
- *Ölçülmüş "iyi his" ile yayma* → işitsel yanılgı (AI8, evaluation K-EVAL-04).
- *AI kapansa bile AI'da kalmak* → geri alma yok (K-AI-07).

---

## §11. Bağımlılıklar

### §11.1 Bağımlı olduğu alanlar

| Alan | Ne alır | Kaynak |
|------|---------|--------|
| `music-analysis` | içerik öznitelikleri (EQ/spatial sinyali) | `../music-analysis/` |
| `inference` | model çağrısı + önbellek (non-RT) | `../inference/` |
| `evaluation` | ses kalitesi + RT kapı usulü | `../evaluation/` |
| `model-management` | model kartı/sürümü | `../model-management/` |
| Neva Engine/DSP (K006 dışı) | RT köprü + filtre uygulaması | `.ai/projects/NevaEngine/` |

### §11.2 Bağımlı olan alanlar

| Alan | Ne verir |
|------|----------|
| `evaluation` | RT/kalite ölçüm sonuçları |
| K006 kökü | audio-AI risk kayıtları |
| Neva (dış) | parametre hedefleri (§3.2) |

---

## §12. İlişkili Dosyalar ve Açık Sorular

### §12.1 Kardeş dosyalar (bu dal)

`index.md` · `audio-intelligence-kapsam.md` · `-bilesenler.md` · `-dogrulama.md` · `-ornekler.md` · `-web-arastirma.md` · kök `../yapay-zeka-kararlar.md` + `../yapay-zeka-riskler.md`

### §12.2 Vault referansları

| Dosya | Ne verir | Not |
|-------|----------|-----|
| `K006-yapay-zeka/index.md` [Satır: 264] | ebeveyn bağlantısı | v2 §2.6 |
| `legacy-2026-07/eq-ai-auto.md` | otomatik EQ çerçevesi | başlık indeksi |
| `legacy-2026-07/spatial-audio.md` | uzamsal ses çerçevesi | başlık indeksi |
| `legacy-2026-07/audio-core.md` §11 [Satır: 387] | latency bütçesi (referans) | RT ilişkili |
| `02-dsp-zincir-mimari.md` | DSP zinciri mimarisi | başlık indeksi |
| `03-isletim-sistemi-kod-ornekleri.md` §2 [Satır: 51] | ASIO/RT kısıtları | K-AI-01 kanıtı |

### §12.3 Açık sorular

1. **RT bütçesi** (köprü için μs/ms/buffer) → Neva ölçümü gerektirir → `UNKNOWN` (§5.3).
2. **Öğrenme sinyali** — otomatik EQ hangi sinyalle öğreniyor (içerik analizi mi, kullanıcı tepkisi mi)? → `UNKNOWN` (§12 başlığındaki açık soru 2 ile aynı).
3. **Uzamsal AI kapsamı** — spatial'da "AI" olan kısım tam olarak nedir (K006 vs. Neva sınırı)? → ADR bekliyor → `⚠️ VERIFICATION REQUIRED`.
4. **Cihaz profilleri** — hoparlör/kulaklık hedef farkı `UNKNOWN` (Rehber 4).
5. **Override politikası süresi** — manuel kapanma ne kadar kalıcı → `UNKNOWN` (Rehber 6).

---

## §13. İlgili Lint Maddeleri

| LINT (`.ai/CLAUDE.md` §6) | Bu anayasadaki karşılığı |
|---------------------------|--------------------------|
| Zero-hallucination | §2.2 — RT/ölçü sayıları kaynaklı |
| `UNKNOWN` / `VERIFICATION REQUIRED` | §5.3, §12.3 |
| Kanıt zorunluluğu | §10 karar tablosu (satır referanslı) |
| RT/lock-free (Audio & Electronics kuralı) | §5.1, AI1–AI3 |
| Keyfi eşik yasağı | K-AI-05 sınır değerleri kaynaklı/ölçümlü |

---

## §14. Bağlam ve Versiyon

| Alan | Değer |
|------|-------|
| Sahip | K006-yapay-zeka / audio-intelligence |
| Ebeveyn | `K006-yapay-zeka` (kök `index.md` §2.6 — v2 satır 264) |
| Kademe | 2 |
| Durum | active |
| Oluşturulma | 2026-10-15 |
| Kapsanan sibling'lar | `audio-intelligence-kapsam` · `-bilesenler` · `-dogrulama` · `-ornekler` · `-web-arastirma` · `index.md` |
| Grup | mimari (`group:` frontmatter) |
| Belge tipi | anayasa (kademe-2, bu dalın usul belgesi — şablon değil) |

---

## §15. Bileşen Sözlüğü (TR↔EN, bu dalın terimleri)

| TR | EN | Bu anayasadaki anlamı |
|----|----|------------------------|
| Gerçek zamanlı | real-time (RT) | audio callback kısıtları (§5.1) |
| Köprü | bridge (RT/non-RT handoff) | kuyruk/atomic paylaşım (§5.2) |
| Hedef eğri | target curve (EQ) | AI'ın ürettiği EQ hedefi (§6.1) |
| Güvenlik kırpma | clamping | limit dışı çıktıyı kesme (§6.2) |
| Akışkanlaştırma | smoothing / ramp | anlık sıçramayı önleme (RT4) |
| Sürüş hızı | slew rate | saniyelik maks değişim (§6.2) |
| Düşme sayacı | drop counter | köprü taşma/sınır dışı sayacı (K-AI-04) |
| Nötr durum | neutral fallback | kanıtlanmış bypass/manuel hedef (K-AI-06) |
| Elle geri alma | manual override | otomasyonu kapatma (İlke 4) |
| Test sinyali | test signal harness | deterministik doğrulama sesi (Rehber 5) |
| Cihaz profili | device profile | çıkış cihazı hedef farkı (Rehber 4) |

**Terim kuralı:** Olmayan terim bu dalda icat edilmez — önce `inference-anayasa.md` §9 sözlüğüne bakılır.

---

## §16. Durum Matrisi (AI-zincir durumları)

| Durum | Tanım | RT davranışı |
|-------|-------|--------------|
| `DISABLED` | otomasyon kapalı (override/policy) | manuel hedef kalıcı (§7.6) |
| `INIT` | yükleniyor/ısınıyor (non-RT) | nötr hedef |
| `ACTIVE` | tahmin akıyor, köprü açık | uygulama (§5.2) |
| `DEGRADED` | tahmin gecikmeli/ölçüm kısıtlı | **eski hedef** korunur (drop, K-AI-04) |
| `CLAMPED` | çıktılar sınırda kırpılıyor | uygulanır + sayaç artar (K-AI-05) |
| `ERROR` | hattı kırık (model/şema) | **nötr durum** (K-AI-06) |
| `OVERRIDDEN` | kullanıcı manuel ele aldı | AI **dokunmaz** (İlke 4) |
| `BYPASS` | mod/örtük zincir kapalı | filtre bypass (02-dsp-zincir ilişkisi) |

**Zorunlu kural:** Geçişler non-RT'de karar verilir; RT asla **durum makinesi yürütmez** — yalnız okunan hedefi uygular (K-AI-01 ile tutarlı).

---

## §17. Hata Sınıfları ve Zincir Davranışı

| Sınıf | Örnek | Davranış | Son durum |
|-------|-------|----------|-----------|
| Model | tahmin/işlem hatası | nötr hedefe dön | `ERROR` → `ACTIVE` ancak yeniden denemeden |
| Köprü | kuyruk taşması | drop + sayaç | `DEGRADED` |
| Sınır | clamp/slew aşımı | kırp + sayaç | `CLAMPED` |
| RT | callback bütçe aşımı | karar düşülür (RT5) | `DEGRADED` |
| Ölçüm | girdi bozuk/sessiz | karar **üretilmez**, eski korunur | `DEGRADED` |
| Sürüm | model kayıtsız (A6 benzeri) | tahmin **durdur** | `ERROR` |
| Override | kullanıcı kapattı | tamamen manuel | `OVERRIDDEN` |

**Kural (K-AI-08):** Hiçbir hata sınıfı **RT'yi bloklamaz veya sesi bozmaz** — hepsi ya drop, ya eski-değer, ya nötr-dönüş (AI6, K-AI-04, K-AI-06).

---

## §18. Güvenlik ve Dayanıklılık (bu dalın yüzeyi)

1. **Girdi güvenliği** — içerik/ölçüm girdisi **şema ile** doğrulanır; bozuk/çıkış-dışı girdi karar üretmez (§17).
2. **Parametre bütünlüğü** — köprüdeki değer **ya geçerli ya da eski geçerli**; **yarım/tahminî değer yok** (atomik yazım, RT3).
3. **Savunma-katmanı** — clamp (AI5) + slew + nötr-fallback **üç katman**; model kötü olsa bile ses güvenli (defense in depth).
4. **Red-team yüzeyi** — zararlı "çok yüksek bass" benzeri uç girdi → kırpmadan geçemez (§6.2); abartılı girdi senaryoları test dosyasında.
5. **Sürüm/sabitleme** — model/şema sürümü kayıtlı değilse tahmin **durdur** (§17) — model-management M-kuralları ile uyum.

---

## §19. Anti-Patternler (yasak kısayollar)

| Anti-pattern | Neden zararlı | Doğrusu |
|--------------|---------------|---------|
| "Callback'te model de çağıralım" | ses kesintisi | RT dışı tahmin (K-AI-01) |
| "Mutex ile kuyruk" | priority inversion | lock-free (RT3) |
| "Parametre anında atlansın" | tıkırtı/titreme | smoothing (RT4) |
| "Model çıktısı doğrudan uygulansın" | dB patlaması | clamp (AI5) |
| "Küçük ses, boşver düşer" | sessiz bozulma | drop sayacı + alarm (K-AI-04) |
| "Ses iyi geldi bence" | ölçüm yok | ölçüm/gate (AI8) |
| "AI kapanınca kalsın" | geri alma yok | override (K-AI-07) |
| "Model bozulursa ne olacak, sonra bakarız" | risk | nötr fallback (K-AI-06) |

---

## §20. Uç Nokta Sözleşmesi (giriş/çıkış)

### §20.1 Giriş

| Girdi | Sağlayıcı |
|-------|-----------|
| İçerik öznitelikleri (EQ/spatial sinyali) | `music-analysis` |
| Model tahmini | `inference` (non-RT önbellek) |
| Sınır değerleri (dB/bant) | Neva DSP yüzeyi — `UNKNOWN` sayılar §12.3.1 |
| Kapı eşikleri | `evaluation` |

### §20.2 Çıkış

| Çıktı | Alıcı | Biçim |
|-------|-------|-------|
| Hedef parametre (EQ eğrisi/spatial) | Neva DSP köprüsü | §6.1 + clamp'li |
| Durum/drop/clamp sayaçları | `evaluation`/dashboard | §8.1 |
| Geri-alma olayları | operasyon | İlke 4 |
| Risk kayıtları | `../yapay-zeka-riskler.md` (kök K006) | §12.3 |

---

## §21. Kaynaklar, Red-Team ve Revizyon

### §21.1 Kaynaklar

| # | Kaynak | Kullanım |
|---|--------|----------|
| R1 | `K006-yapay-zeka/index.md` [Satır: 264] | ebeveyn bağlantısı |
| R2 | `legacy-2026-07/eq-ai-auto.md` | otomatik EQ çerçevesi |
| R3 | `legacy-2026-07/spatial-audio.md` | uzamsal çerçevesi |
| R4 | `legacy-2026-07/audio-core.md` [Satır: 387] | latency bütçesi referansı |
| R5 | `03-isletim-sistemi-kod-ornekleri.md` [Satır: 51] | ASIO/RT kısıtları |
| R6 | `02-dsp-zincir-mimari.md` | DSP zinciri ilişkisi |
| R7 | Web kaynağı | AÇILMADI — bu turda URL eklenmedi (`audio-intelligence-web-arastirma.md`'ye devredildi) |

### §21.2 Red-Team soruları

| # | Soru | Beklenen cevap |
|---|---|---|
| T1 | RT'de model çağrılsa? | yapılmaz — AI1, K-AI-01 |
| T2 | Kuyruk taşırsa? | drop + sayaç, RT beklemez — K-AI-04 |
| T3 | Model 40 dB bass verirse? | clamp — AI5, §6.2 |
| T4 | Titreme (slew) fazla olursa? | ramp — RT4, İlke 5 |
| T5 | Model hattı koptu? | nötr durum — K-AI-06 |
| T6 | Kullanıcı "yeter" derse? | `OVERRIDDEN`, AI dokunmaz — K-AI-07 |
| T7 | Kayıtsız model sürümü? | tahmin durur — §17, model-management M1 |
| T8 | RT bütçesi biliniyor mu? | `UNKNOWN` + ölçüm — §5.3, §12.3.1 |

### §21.3 Revizyon Kaydı

| Tarih | Değişiklik | Yapan | Not |
|---|---|---|---|
| 2026-10-15 | İlk yazım (dosya oluşturma) | K006 vault içerik ajanı | Append-only; §1–§21 |
| — | Sonraki revizyon | — | Yalnız ek bölüm olarak |

**Authority:** Bayram Ali / Vault Steward · **status:** active — §12.3 açık soruları Neva ölçümü/ADR ile kapatılır.

---

## §22. Ek Usul — Değişiklik Eşiği Tablosu (ne değişince ne tekrarlanır)

| Değişen yüzey | Tekrarlanacak kapı | Neden |
|---|---|---|
| Model/şema sürümü (`model_ref`) | §8.2 1–3 (RT + sınır + kalite) | karar dağılımı değişir |
| RT köprü mekanizması (kuyruk/atomik) | kapı 1 (RT bütçe/drop testi) | lock-free sözleşmesi yeniden doğrulanmalı |
| Sınır değerleri (dB/bant/slew) | kapı 2 (sınır testleri) — değerler kaynaklı/ölçümlü (K-AI-05) | savunma-katmanı değişir |
| Smoothing zaman sabiti | kalite kapısı + jitter metriği (§8.1) | titreme/tepki dengesi |
| Nötr fallback hedefi | kapı 5 (bozulma) | K-AI-06 kanıtlanmış durum |
| Override politikası (süre/tetik) | kapı 4 (geri-alınabilirlik) | İlke 4 ihlali riski |
| İçerik sinyali girdisi (`music-analysis`) | kapı 3 (kalite A/B) + clamp oranı | sinyal değişimi hedefi kaydırır |

**Son kural:** Tabloda olmayan değişiklik bile, **RT kapısı (§8.2.1) + nötr-fallback (K-AI-06) olmadan** üretim modu açılmaz — bu ikisi ses güvenliğinin alt kümesidir; RT bütçesi `UNKNOWN` iken (§5.3) üretim **kapalı** kalır (AI9).

---

## §23. Ek Usul — SSS (Sıkça Sorulan Usul Soruları)

| # | Soru | Anayasa cevabı |
|---|---|---|
| U1 | Model tahmini ne sıklıkla çalışır? | Content değişimi/oturum başı; RT **her karede** değil — önbellek + batch (§7.3), sıklık `UNKNOWN` (ölçüm) |
| U2 | Tepki çok yavaş/çok hızlı hissedilirse? | smoothing parametresi ölçülür (Rehber 2) — `UNKNOWN`-ken muhafazakâr yavaş |
| U3 | Kulaklıkta EQ farklı mı olmalı? | cihaz profili `UNKNOWN` (Rehber 4) — kapsam kararı (`*-kapsam.md`) |
| U4 | Kullanıcı manual EQ'ya geçti, AI sonra ne yapar? | `OVERRIDDEN` — AI dokunmaz; politika süresi §12.3.5 |
| U5 | AI sonucu beğenilmezse geri alınır mı? | her zaman — RT6, K-AI-07 |
| U6 | Hangi ölçüm "sesi iyileştirdi" der? | evaluation kapı 3: A/B veya işitsel test (AI8) |
| U7 | Uzamsal AI tam olarak nerede? | kapsam netleşmedi → §12.3.3 `⚠️ VERIFICATION REQUIRED` |
| U8 | RT bütçesi kaç μs? | `UNKNOWN` → ölçüm zorunlu, üretim kapalı (§5.3, AI9) |
| U9 | Model hattı koptuğunda ses kesilir mi? | hayır — K-AI-06 nötr durum |
| U10 | Bu anayasa yetmezse? | §12.3 → kapsam/ADR → append-only ek (§21.3 usulü) |

**SSS kuralı:** Cevaplar başka anayasadan **alınmaz** — her cevap bu dosyanın bir §'ine bağlanır; bağlantısız/kanıtsız cevap geçersizdir (§2.2 zero-hallucination disiplini aynen uygulanır).
