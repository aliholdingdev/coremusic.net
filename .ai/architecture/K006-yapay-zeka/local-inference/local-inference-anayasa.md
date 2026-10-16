---
node_id: K006-yapay-zeka/local-inference/local-inference-anayasa
node_type: module
status: active
parent_id: K006-yapay-zeka/local-inference
children:
  - K006-yapay-zeka/local-inference/local-inference-kapsam
  - K006-yapay-zeka/local-inference/local-inference-bilesenler
  - K006-yapay-zeka/local-inference/local-inference-dogrulama
  - K006-yapay-zeka/local-inference/local-inference-ornekler
  - K006-yapay-zeka/local-inference/local-inference-web-arastirma
depends_on:
  - K006-yapay-zeka/inference/inference-anayasa
  - K006-yapay-zeka/model-management/model-management-anayasa
  - K006-yapay-zeka/evaluation/evaluation-anayasa
evidence:
  - "Dosya: .ai/architecture/K006-yapay-zeka/index.md [Satır: 265]"
  - "Dosya: .ai/projects/NevaEngine/legacy-2026-07/ai-models.md [Satır: 125]"
  - "Dosya: .ai/projects/NevaEngine/legacy-2026-07/ai-models.md [Satır: 208]"
validation: "İçerik doğrulama: 2026-10-15 — anayasa, ai-models.md §3–§4 (ONNX/quantization) ve 00-sdk-lib-envanter.md kanıtlarıyla eşleştirildi"
open_questions:
  - "Hedef cihaz matrisi (Windows/Linux/macOS/iOS/Android) için minimum donanım eşikleri UNKNOWN"
  - "Model dosyası dağıtım kanalı (update mekanizması) K006 dışı — VERIFICATION REQUIRED"
group: mimari
---
# Local Inference — Alt Dal Anayasası (kademe-2)

> **Bu dosya kademe-2 anayasadır.** Ebeveyn: `K006-yapay-zeka` (bkz. `index.md` §2.6 çocuk haritası — v2 satır 265, `G:\` arşivinde).
> Kardeş anayasalar: `../inference/inference-anayasa.md`, `../model-management/model-management-anayasa.md`, `../evaluation/evaluation-anayasa.md`.
> Bu dosya **şablon içermektedir** — tüm alanlar somut değerle doldurulmuştur.

---

## §1. Kapsam Sınırı

**local-inference**, K006'da **modelin uç cihazda (client/edge) çalıştırılması** disiplinini kapsar. Kapsayıcılar:

1. **Yerel model çalıştırma motoru** — ONNX Runtime, cihaz hızlandırıcıları (CPU/NPU/GPU), oturum yönetimi (ai-models.md §3, satır 125).
2. **Model paketleme ve optimizasyon** — quantization, graph optimization, boyut bütçesi (ai-models.md §4.1, satır 208).
3. **Yerel/uzak orkestrasyon** — hangi görev yerelde, hangisi sunucuda; çevrimdışı davranış (fallback zinciri).
4. **Cihaz uyumluluğu** — donanım matrisi, bellek/thermal bütçesi, lisans kısıtları.

**Kapsam dışı:**

- Sunucu tarafı çalıştırma (provider API, kendi GPU'umuz) → `inference`
- Model indirme/sürüm/registry → `model-management` (local-inference yalnız **yerel çalıştırma** ile ilgilenir)
- Model doğruluğu ölçümü → `evaluation` (burada yalnız **yerel-özel** ölçüm: hız, bellek, thermal)
- Uygulama arayüzü → K006 dışı frontend

**Kapsam sınırı testi:** "Çalışma anında model dosyası **cihazın belleğinde** mi dönüyor?" → Evet = local-inference. "Ağ üzerinden çağrı mı?" → inference.

---

## §2. İlke ve Değerler

### §2.1 Öncelik sırası

| # | İlke | Gerekçe |
|---|------|---------|
| 1 | **Önce doğruluk, sonra hız** | Hızlı ama yanlış model değersizdir — quantization kaybı ölçülür (ai-models.md §4.1, satır 208) |
| 2 | **Bellek bütçesi kutsaldır** | Cihaz belleği sınırlıdır; taşma = çökme (OOM) — bütçe önceden hesaplanır |
| 3 | **Çevrimdışı bozulmaz** | Ağ yoksa yerel görevler çalışır; yerel olmayan görev tanımlı hata verir |
| 4 | **Cihazdan cihaza fark doğrulanır** | Masaüstü sonucu mobile geçerli değildir — her hedef cihazda ölçüm |
| 5 | **Ticari lisans denetimi** | Model ağırlıkları + runtime lisansı dağıtımdan önce kontrol edilir |

### §2.2 Zero-hallucination bağlayıcılığı

1. Cihaz performans iddiaları (`tok/s`, `ms`, `MB`) **yalnız ölçümle** yazılır — tahmini değer yasak.
2. Desteklenmeyen donanım/platform listesi **UNKNOWN** bırakılamaz; ya ölçülür ya da "desteklenmiyor" yazılır.
3. Model boyutu/quantization kazancı sayıları `ai-models.md` gibi **kaynaklı** yazılır; kaynağı olmayan sayı **udhurulmaz**.
4. Ölçüm koşulu (cihaz modeli, OS, runtime sürümü, tarih) sayı ile birlikte zorunludur.

---

## §3. Mimari Roller ve Kararlar

### §3.1 Yerel çalıştırma katmanları

```
┌──────────────────────────────────────────────────────────┐
│ UYGULAMA / APLİKASYON KANCI                              │
│   • görev çağrısı (anomali, öneri, ASR/TTS isteği)       │
├──────────────────────────────────────────────────────────┤
│ ORKESTRATÖR (yerel ↔ uzak seçim)                         │
│   • çevrimdışı? → yerel · kalite eşiği? → uzak           │
├──────────────────────────────────────────────────────────┤
│ YEREL MOTOR (ONNX Runtime oturumu)                       │
│   • session cache, pre/post-process, batch               │
├──────────────────────────────────────────────────────────┤
│ DONANIM İNDİRGEYİCİ (CPU / NPU / GPU / DSP)              │
│   • EP seçimi, bellek havuzu, thermal denetimi           │
├──────────────────────────────────────────────────────────┤
│ MODEL PAKETİ (.onnx + metadata + checksum)               │
│   • sürüm, boyut, lisans, accuracy doğrulama kaydı       │
└──────────────────────────────────────────────────────────┘
```

**Karar (K-LOC-01):** Orkestratör, yerel/uzak seçiminde **tek kriter kalite değil, çevrimdilik + gecikme + maliyet** üçlüsüdür; kalite eşiği düşerse uzak tercih edilir.
**Kanıt:** ai-models.md §3 (satır 125) ONNX pipeline'ı; §4.1 (satır 208) quantization kalite/zaman dengesi.

### §3.2 Sahiplik matrisi

| Alan | Sorumluluk | Not |
|------|-----------|-----|
| Yerel motor + oturum | local-inference | session cache, EP seçimi |
| Model dosyası/registry | model-management | indirme, sürüm, imza |
| Uzak çağrı/düşük kalite | inference | provider fallback |
| Yerel-özel ölçüm (hız/bellek) | evaluation + local-inference | §8 ölçüm tablosu |

**Karar (K-LOC-02):** `model-management` modeli **teslim eder**, `local-inference` **çalıştırır** — dosya yaşam döngüsü ile çalışma zamanı ayrılmazsa sürüm/çakışma sorunları kaçınılmazdır.

---

## §4. Cihaz ve Platform Matrisi

### §4.1 Destek karar tablosu

| Platform | Runtime | Durum | Not |
|----------|---------|-------|-----|
| Windows x64 | ONNX Runtime | Ana hedef | Neva/ASIO ekosistemi (03-isletim-sistemi, satır 51) |
| Linux x64 | ONNX Runtime | Desteklenir | sunucu/edge |
| macOS (arm64) | ONNX Runtime | `UNKNOWN` | ölçülmedi — destek iddiası YOK |
| iOS / Android | ONNX Runtime (coreml/nnapi EP) | `UNKNOWN` | ölçülmedi |
| Gömülü (NPU/DSP) | cihaz-özel | ayrı domain | K006 dışı (embedded) |

**Karar (K-LOC-03):** Ölçülmemiş platform **"desteklenir" yazılmaz** — `UNKNOWN` + ölçüm görevi (bu dosya §12.3 — cihaz matrisi eşikleri, kök `../yapay-zeka-riskler.md` ile izlenir).
**Zero-hallucination:** Tablodaki `UNKNOWN` değerleri bilgi eksikliğinin **kayıtlı halidir**; uydurulmuş "destek" listesi bu anayasaya aykırıdır.

### §4.2 Donanım eşikleri (bütçe)

| Bütçe | Kural | Kaynak |
|-------|-------|--------|
| Bellek (tepe) | cihaz-available belleğin ≤ %50'si (model + çalışma alanı) | bu dosya §5.2 |
| Boyut (paket) | indirme bütçesi ürün kararı — `UNKNOWN` (K006 dışı) | — |
| Thermal | uzun süreli çalıştırmada throttle algılanırsa batch düşürülür | bu dosya §9.3 |
| Batarya (mobil) | `UNKNOWN` (ölçülmedi) | §4.1 `UNKNOWN` ile tutarlı |

---

## §5. Model Paketleme ve Optimizasyon

### §5.1 Pipeline (ai-models.md §3, satır 125)

```
Kaynak model (HF/PyTorch/TF)
  → export (ONNX, dynamic axes tanımlı)
    → graph optimize (constant folding, fusion)
      → quantization (INT8 / dynamic / fp16 — §5.3)
        → doğrulama (accuracy delta ölçümü — evaluation kapısı)
          → paket (.onnx + metadata.json + SHA-256)
            → teslim (model-management registry)
```

**Zorunlu:** Her adımda **çıktı artefaktı** saklanır (ara dosya + log) — tekrar üretilebilirlik.

### §5.2 Bellek bütçesi hesabı

```
bellek(model) ≈ dosya boyutu × (dequant çarpanı dahil) + aktivasyon tepe değeri
toplam        = bellek(model) + giriş buffer'ları + çıktı buffer'ları + oturum overhead
limit         = cihaz-available × 0.5   (K-LOC-04 güvenlik payı %50)
```

**Karar (K-LOC-04):** Bellek hesabı **önceden** yapılır (deployment öncesi); runtime'da OOM beklenemez. Aşım varsa → quantization veya daha küçük model (§5.3) — asla "deneriz".

### §5.3 Quantization kararı (ai-models.md §4.1, satır 208)

| Yöntem | Kazanç | Risk | Ne zaman |
|--------|--------|------|----------|
| Dynamic INT8 | boyut/bellek ~%75 azalır | doğruluk kaybı (ölçülür) | CPU-öncelikli, doğruluk toleranslı görevler |
| Static INT8 (calibration set ile) | daha stabil | calibration set gerekir | kalite kritik görev |
| FP16 | bellek ~%50, hız GPU'da artar | CPU'da hızlanmayabilir | GPU/NPU |
| Bileşenli (kısmi) | denge | karma | heterojen graf |

**Kural (K-LOC-05):** Quantization **her zaman accuracy delta ölçümü ile** onaylanır (evaluation kapısı) — ölçülmemiş quantized model production'a **girmez**.
**Kanıt:** ai-models.md §4.1 (satır 208) quantization kazanç/kayıp çerçevesini verir.

### §5.4 Paket bütünlüğü

1. **SHA-256 checksum** — paket açılışta doğrulanır; uyuşmazlık → model reddedilir, fallback zinciri (inference §15).
2. **metadata.json** — sürüm, hedef platform, quantization tipi, accuracy raporu referansı, lisans.
3. **İmza (imzalama)** — `UNKNOWN` (mecanizma K006 dışı); risk olarak `../yapay-zeka-riskler.md` (kök K006) kayıtlıdır.

---

## §6. Orkestrasyon: Yerel ↔ Uzak Seçimi

### §6.1 Karar ağacı

```
görev geldi
 ├─ çevrimdışıysa → YEREL (varsa) / tanımlı hata (yoksa)
 ├─ çevrimiçi ise
 │   ├─ gecikme bütçesi dar (gerçek zamanlı ses) → YEREL
 │   ├─ kalite eşiği yüksek (LLM üretim) → UZAK
 │   └─ maliyet politikası → ikisi de (cache → yerel → uzak)
 └─ her iki dal da başarısız → kullanıcıya tanımlı hata
```

**Karar (K-LOC-06):** Seçim **görev tipine** göre sabittir (konfigürasyonda), her çağrıda "akıllı" yeniden değerlendirilmez — öngörülemeyen gecikme üretir.

### §6.2 Çevrimdışı davranış sözleşmesi

| Görev | Çevrimdışı davranışı |
|-------|---------------------|
| Yerel model tanımlı (anomali vb.) | çalışır, sonuç damgalı (`offline=true`) |
| Yerel model tanımlı ama paket yok | indirme bekler → tanımlı hata |
| Yalnız uzak (LLM üretimi) | **tanımlı hata** — sessiz bozulma yasak |
| Cache'te sonuç var | cache sonuç döner (`stale=true` etiketi) |

**Zorunlu kural:** Çevrimdışı hata **her zaman tanımlı ve Türkçe/erişilebilir** bir mesajdır — kaba istisna/metin yasak.

---

## §7. Rehberler (Guidelines)

1. **Tek oturum** — aynı model için tek ONNX oturumu; oturum başına yeniden kurma pahalıdır (session cache).
2. **Pre/Post-process yerelde** — ağ üzerinden ham tensör taşımak yerine işlenmiş veri (bandwidth tasarrufu).
3. **Warm-up** — ilk çağrıdan önce sessiz ısıtma (ilk-call gecikmesi bütçeye yazılır).
4. **Batch penceresi** — gerçek zamanlı olmayan toplu işlerde kısa batch (inference §14 ile uyumlu).
5. **Lisans önce** — model ağırlığı + runtime lisansı dağıtım kararı öncesi denetlenir (ticari kullanım).
6. **Ölçüm cihazdadır** — CI'daki rakam adaydır; **release kararı hedef cihaz ölçümüyle** verilir.
7. **Fallback zinciri** — yerel başarısız → (çevrimiçi ise) uzak → tanımlı hata; sessiz boş sonuç yasak.
8. **Geri alınabilir paket** — sürüm geri alma (rollback) tek adımda mümkün olmalı (eski paket saklanır).

---

## §8. Zorunlu Kurallar (MUST / MUST NOT)

| # | Kural | Seviye |
|---|-------|--------|
| L1 | Model paketi açılışta **checksum** ile doğrulanmalıdır | MUST |
| L2 | Quantization **accuracy delta ölçümü** ile onaylanmalıdır (K-LOC-05) | MUST |
| L3 | Bellek bütçesi **deployment öncesi** hesaplanmalı, %50 pay korunmalı (K-LOC-04) | MUST |
| L4 | Performans iddiası **ölçüm koşuluyla** (cihaz/OS/runtime/tarih) yazılmalıdır | MUST |
| L5 | Ölçülmemiş platform `UNKNOWN` olmalı — "desteklenir" yazılmamalıdır (K-LOC-03) | MUST |
| L6 | Çevrimdışı olmayan görev **sessizce** yutulmamalı — tanımlı hata dönmelidir | MUST NOT |
| L7 | Yerel/uzak seçimi **sabit konfigürasyon** olmalı, çağrı-içi yeniden karar yasak | MUST NOT |
| L8 | Görev tipine göre seçici davranış olmalı — "çift karar" yasak (K-LOC-06) | MUST |
| L9 | Model + runtime **lisans** denetimi dağıtımdan önce yapılmalıdır | MUST |
| L10 | Warm-up yapılmadan ilk-call süresi **gerçek kullanıcı** metriğine girmez (etiketli ölçüm) | MUST |
| L11 | Bellek taşması (OOM) çökme değil, **tanımlı hata** üretmelidir (yakalama + fallback) | MUST |

---

## §9. Ölçüm ve Performans Disiplini (yerel-özel)

### §9.1 Zorunlu metrikler (yerel çalıştırma)

| Metrik | Nerede ölçülür | Kapı |
|--------|----------------|------|
| İlk-call latency (warm-up dahil) | hedef cihaz | rapor (etiketli) |
| Steady-state latency (p50/p95) | hedef cihaz | bütçe (§4.2) |
| Bellek tepe (RSS) | hedef cihaz | K-LOC-04 |
| tok/s veya görev/sn | hedef cihaz | evaluation kapı matrisi |
| Thermal throttling (var/yok + süre) | uzun run | §9.3 |
| Accuracy delta (quantize öncesi/sonrası) | evaluation | K-LOC-05 **block** |

### §9.2 Ölçüm koşulu damgası (zorunlu)

Her sayı: `cihaz modeli + OS + runtime sürümü + model sürümü + tarih` — eksik damgalı ölçüm rapora **girmez** (evaluation §2.2 ile aynı disiplin).

### §9.3 Thermal ve sürdürülebilirlik

1. **Uzun-run testi** (≥10 dk sürekli) — throttle başlangıcı ve düşüş yüzdesi ölçülür.
2. Throttle algılanırsa **adaptasyon**: batch/pencere küçültülür (K-LOC-07).
3. Ölçüm yoksa "sürekli çalışır" iddiası yasak — `UNKNOWN`.

**Karar (K-LOC-07):** Termal adaptasyon **otomatik ve ölçülmüş** olmalıdır; elle ayar (kullanıcıya "havalandırın" demek) çözüm değildir.

---

## §10. Karar Kaydı

| Karar | İçerik | Kanıt |
|-------|--------|-------|
| K-LOC-01 | Yerel/uzak seçimi üçlü kriter (çevrimdışılık+gecikme+maliyet) | ai-models.md §3, satır 125 |
| K-LOC-02 | Dosya yaşam döngüsü (model-management) ≠ çalışma zamanı (local) | bu dosya §3.2 |
| K-LOC-03 | Ölçülmemiş platform = `UNKNOWN` | bu dosya §4.1 |
| K-LOC-04 | Bellek %50 güvenlik payı, önceden hesap | bu dosya §5.2 |
| K-LOC-05 | Quantization = accuracy delta ölçümü ile onay | ai-models.md §4.1, satır 208 |
| K-LOC-06 | Yerel/uzak seçimi sabit konfigürasyon | bu dosya §6.1 |
| K-LOC-07 | Termal adaptasyon otomatik + ölçülmüş | bu dosya §9.3 |

**Reddedilen alternatifler:**

- *Her çağrıda akıllı seçim* → öngörülemeyen gecikme, zor test (K-LOC-06).
- *Ölçümü CI'da yap yeter* → CI donanımı hedef cihaz değildir (K-LOC-03, Rehber 6).
- *Quantization'ı deneyip bakarız* → kalite kapısı olmadan bozulma yayılır (K-LOC-05).
- *Belleği runtime'da görürüz* → OOM = kullanıcı çökmesi (K-LOC-04).

---

## §11. Bağımlılıklar

### §11.1 Bağımlı olduğu alanlar

| Alan | Ne alır | Kaynak |
|------|---------|--------|
| `model-management` | model paketi, sürüm, checksum, lisans | `../model-management/` |
| `inference` | uzak fallback, hata zinciri, orkestratör davranışı | `../inference/` |
| `evaluation` | accuracy delta kapısı, performans metrik şeması | `../evaluation/` |
| `testing` | CI + cihaz test altyapısı | `../testing/` |

### §11.2 Bağımlı olan alanlar

| Alan | Ne verir |
|------|----------|
| `inference` | çevrimdışı modda yerel motor |
| `evaluation` | yerel-özel ölçüm sonuçları (hız/bellek/thermal) |
| K006 kökü | edge/yerel risk kayıtları (riskler dosyası) |

---

## §12. İlişkili Dosyalar

### §12.1 Bu dalın kardeş dosyaları

| Dosya | Rol | Özet |
|-------|-----|------|
| `index.md` | dal indeksi | §GRUPLAR + çocuklar |
| `local-inference-kapsam.md` | kapsam | ne yerel, ne değil |
| `local-inference-bilesenler.md` | bileşenler | motor, paket, orkestratör |
| `local-inference-dogrulama.md` | doğrulama | checksum, accuracy delta |
| `local-inference-ornekler.md` | örnekler | paketleme/ölçüm örnek akışları |
| `local-inference-web-arastirma.md` | web araştırma | runtime/standart taraması |
| `../yapay-zeka-kararlar.md` | kök K006 kararları | bu dal kararlarının üst otoritesi |
| `../yapay-zeka-riskler.md` | kök K006 riskleri | OOM/thermal/lisans riskleri buraya işlenir |

### §12.2 Vault referansları

| Dosya | Ne verir | Satır |
|-------|----------|-------|
| `.ai/architecture/K006-yapay-zeka/index.md` | çocuk haritası (v2 §2.6) | 265 |
| `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | §3 ONNX pipeline | 125 |
| `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | §4.1 quantization | 208 |
| `.ai/projects/NevaEngine/00-sdk-lib-envanter.md` | SDK/runtime envanteri | başlık indeksi |
| `.ai/projects/NevaEngine/03-isletim-sistemi-kod-ornekleri.md` | §2 ASIO (Windows ses) | 51 |

**Seçim kuralı:** motor/paket → §5; orkestrasyon → §6; cihaz → §4; karar kaydı → bu dosya §10–§11 (K-LOC-*); risk kaydı → `../yapay-zeka-riskler.md` (kök K006).

---

## §13. Açık Sorular (open_questions detayı)

1. **Cihaz matrisi eşikleri** — minimum RAM/CPU/NPU eşikleri ürün kararı değil → `UNKNOWN`.
2. **Dağıtım kanalı** — model güncellemesi nasıl yayılır (OTA, uygulama güncellemesi)? K006 dışı kod yüzeyi → `⚠️ VERIFICATION REQUIRED`.
3. **İmzalama** — paket imza mekanizması (K-LOC paket bütünlüğü §5.4.3) `UNKNOWN`.
4. **mobil/Apple destek** — iOS/Android ölçümü yapılmadı → `UNKNOWN` (K-LOC-03).
5. **Gömülü NPU** — Neva/embedded hedefi için yerel model stratejisi ayrı domain → kapsam dışı işaretli.

---

## §14. İlgili Lint Maddeleri

| LINT (`.ai/CLAUDE.md` §6) | Bu anayasadaki karşılığı |
|---------------------------|--------------------------|
| Zero-hallucination | §2.2 — ölçülmemiş performans iddiası yasak |
| `UNKNOWN` / `VERIFICATION REQUIRED` | §4.1, §13 |
| Kanıt zorunluluğu | §10 karar tablosu (satır referanslı) |
| Eşik keyfiliği yasağı | K-LOC-04/05 — bütçe ve delta ölçümle sabit |
| Ölçüm percentil | §9.1 (p50/p95) |

---

## §15. Bağlam ve Versiyon

| Alan | Değer |
|------|-------|
| Sahip | K006-yapay-zeka / local-inference |
| Ebeveyn | `K006-yapay-zeka` (kök `index.md` §2.6 — v2 satır 265) |
| Kademe | 2 |
| Durum | active |
| Oluşturulma | 2026-10-15 |
| Kapsanan sibling'lar | `local-inference-kapsam` · `-bilesenler` · `-dogrulama` · `-ornekler` · `-web-arastirma` · `index.md` |
| Grup | mimari (`group:` frontmatter) |
| Belge tipi | anayasa (kademe-2, bu dalın usul belgesi — şablon değil) |

---

## §16. Bileşen Sözlüğü (TR↔EN, bu dalın terimleri)

| TR | EN | Bu anayasadaki anlamı |
|----|----|------------------------|
| Yerel çalışma | on-device / local inference | model cihaz belleğinde (§1) |
| Orkestratör | orchestrator (local/remote) | yerel-uzak seçim katmanı (§6) |
| Model paketi | model package | `.onnx` + metadata + checksum (§5.4) |
| Nicelendirme | quantization | INT8/FP16 dönüşümü (§5.3) |
| Oturum önbelleği | session cache | tekrar-kurulumu önleyen oturum (Rehber 1) |
| Sıcaklatma | warm-up | ilk-çağrı gecikmesini önleyen hazırlık (Rehber 3) |
| Bellek payı | memory headroom | %50 güvenlik payı (K-LOC-04) |
| Termal kısıtlama | thermal throttling | ısınma kaynaklı hız düşüşü (§9.3) |
| Çevrimdışı hata | offline error | tanımlı, erişilebilir hata (§6.2) |
| Doğruluk farkı | accuracy delta | quantize öncesi/sonrası fark (K-LOC-05) |

**Terim kuralı:** Bu tabloda olmayan terim **bu dalda icat edilmez** — önce `../inference/inference-anayasa.md` §9 (sözlük) kontrol edilir, orada yoksa eklenir (append-only, kardeş anayasayla aynı kelime).

---

## §17. Durum Matrisi (Component State Matrix)

Yerel motor bileşeni (oturum + model) için zorunlu durumlar:

| Durum | Tanım | Davranış |
|-------|-------|----------|
| `EMPTY` | model henüz indirilmedi | indirme akışı / tanımlı hata (§6.2) |
| `LOADING` | dosya okunuyor + checksum | UI gösterir; çağrılar kuyruklanır |
| `READY` | oturum açık, ısıtma tamam | normal çalışır |
| `WARMING` | ısıtma sürüyor (Rehber 3) | çağrılar tamponlanır, etiketli ölçüm (L10) |
| `DEGRADED` | thermal/bellek kısıtlı | batch düşürülür (K-LOC-07), kalite-rapor |
| `FALLBACK` | yerel başarısız → uzak | çevrimiçi ise uzak; değilse hata (§6.2) |
| `ERROR` | checksum/uyumsuzluk/oom | paket reddi + tanımlı hata (L1, L11) |
| `DISABLED` | lisans/politika | kapalı; gerekçe log'da (L9) |

**Zorunlu kural:** Geçişler **loglanır** (durum + zaman + gerekçe); `ERROR → READY` yalnız **yeniden yükleme/indirme** ile olur — sessiz kendini yeniden deneme yasak.

---

## §18. Hata Sınıfları ve Yerel Davranış (fallback ile uyum)

| Sınıf | Örnek | Yerel davranış | Son durum |
|-------|-------|----------------|-----------|
| Paket bütünlüğü | checksum uyuşmadı | modeli reddet | `ERROR` → uzak varsa `FALLBACK` |
| Bellek | OOM yakalandı | oturumu kapat, küçült/talep | `DEGRADED` → başarısızsa `ERROR` |
| Uyumsuzluk | EP/runtime yok | desteklenmiyor bildir | `DISABLED`/`ERROR` (K-LOC-03) |
| Thermal | throttling | batch düşür (K-LOC-07) | `DEGRADED` |
| Süre aşımı | tahmin > bütçe | oturumu sıfırla, 1 kez dene | 2. aşım → `ERROR` |
| Çevrimdışı + uzak | ağ yok | yerel dene; yoksa tanımlı hata | §6.2 sözleşmesi |
| Lisans/politika | denetim reddi | yükleme reddi | `DISABLED` |

**Kural (K-LOC-08):** Yerel hatalar **asla** uygulamayı çökertmez — hepsi durum matrisine (§17) oturur; yakalama katmanı her çağrıda zorunludur (L11).

---

## §19. Güvenlik ve Gizlilik (yerel yüzeyi)

1. **Model dosyası = veri değil, ama sızıntı riski** — model ağırlığı **teslimat sırrı** ise paket korunur (lisans + imza §5.4; mekanizma `UNKNOWN` → §13.3).
2. **Girdi PII'si yerelde işlenir** — girdi **log'a tam gömülmez** (maskelama, inference §17 G6 ile aynı disiplin).
3. **Çıktı cache'i PII tutmaz** — yerel cache anahtarı kimlik + sürüm içerir, **ham girdi içermez**.
4. **Güncelleme kanalı imzalı** — doğrulanmamış kaynaktan model **yüklenmez** (§5.4.1; kanal mekanizması §13.2 `⚠️`).
5. **Rota yüzeyi** — yerel API (uygulama-içi) izinli çağrıcıya açıktır; izin dışı çağrı reddi (inference §17 G3 ile aynı kapı).

---

## §20. Anti-Patternler (yasak kısayollar)

| Anti-pattern | Neden zararlı | Doğrusu |
|--------------|---------------|---------|
| "Masaüstünde hızlı, telefonda da öyledir" | donanım farkı | hedef cihaz ölçümü (§9.2) |
| "INT8'e çevirdik, boyut indi" | doğruluk sessiz bozulur | accuracy delta (K-LOC-05) |
| "OOM olursa yakalarız" | kullanıcı çökmesi | önceden bütçe (K-LOC-04) |
| "Çevrimdışıyken sessiz boş dön" | güven kaybı | tanımlı hata (§6.2) |
| "Her çağrı en iyisini seçsin" | gecikme kaosu | sabit konfigürasyon (K-LOC-06) |
| "Runtime güncellenince eski paket de çalışır" | uyumsuzluk | metadata + sürüm kilidi (§5.4.2) |
| "Lisansı sonra bakarız" | dağıtım ihlali | önce lisans (L9) |
| "İlk çağrı hızlı diye ölçtük" | warm-up gizli | etiketli ölçüm (L10) |

---

## §21. Uç Nokta Sözleşmesi (bu dalın giriş/çıkış)

### §21.1 Giriş

| Girdi | Sağlayıcı | Not |
|-------|-----------|-----|
| Model paketi | `model-management` | `.onnx` + metadata + checksum |
| Görev + konfigürasyon | `inference` orkestratörü | yerel/uzak kararı sabit (K-LOC-06) |
| Donanım profili | runtime keşfi | EP seçimi; bilinmeyen = `UNKNOWN` → desteklenmiyor |
| Ölçüm kancaları | `evaluation`/`testing` | §9 metrikleri |

### §21.2 Çıkış

| Çıktı | Alıcı | Biçim |
|-------|-------|-------|
| Görev sonucu (+ `offline`/`stale` damgaları) | uygulama | §6.2 sözleşmesi |
| Durum geçiş logları | `testing`/operasyon | §17 |
| Performans ölçümleri | `evaluation` | §9.1 metrikleri |
| Hata sınıfları | `inference` fallback zinciri | §18 tablosu |

---

## §22. Kaynaklar (bu anayasada atıf yapılanlar)

| # | Kaynak | Kullanım |
|---|--------|----------|
| R1 | `.ai/architecture/K006-yapay-zeka/index.md` [Satır: 265] | ebeveyn bağlantısı |
| R2 | `ai-models.md` [Satır: 125] | §3 ONNX pipeline |
| R3 | `ai-models.md` [Satır: 208] | §4.1 quantization |
| R4 | `00-sdk-lib-envanter.md` | runtime/SDK envanteri |
| R5 | `03-isletim-sistemi-kod-ornekleri.md` [Satır: 51] | §2 ASIO / Windows ses |
| R6 | `inference-anayasa.md` (kardeş) | fallback, orkestratör, sözlük |
| R7 | `evaluation-anayasa.md` (kardeş) | kapı matrisi, ölçüm usulü |
| R8 | Web kaynağı | AÇILMADI — bu turda URL eklenmedi (`local-inference-web-arastirma.md`'ye devredildi) |

---

## §23. Red-Team Soruları (yerel saldırı yüzeyi)

| # | Soru | Beklenen cevap |
|---|---|---|
| T1 | checksum bozuk paket açılırsa? | reddedilir → `ERROR` + fallback — L1 |
| T2 | Bellek yetmezse ne olur? | önceden bütçe, runtime'da yakalama → `ERROR` — K-LOC-04, L11 |
| T3 | Sıcak dosya değiştirilirse (TOCTOU)? | doğrulama + yükleme atomik olmalı → **⚠️ VERIFICATION REQUIRED** (uygulama kodu K006 dışı) |
| T4 | Çevrimdışıyken LLM görevi istenirse? | tanımlı hata — §6.2 |
| T5 | PII girdi log'a sızar mı? | maskelama — §19.2 |
| T6 | Lisanssız model yüklenirse? | `DISABLED` — L9, §19.4 |
| T7 | Thermal'de kalite ölçümü sahte geçer mi? | thermal koşulda ölçüm etiketlenir — §9.3 |
| T8 | Eski sürüm cache'i yeni sürümle karışır mı? | anahtar sürüm içerir — §14 I17 (inference) + §5.4.2 |

---

## §24. İlgili Test Kapıları (özet — detay test-stratejisi dosyasında)

1. **Paket kapısı** — checksum + metadata doğrulama (L1).
2. **Bellek kapısı** — bütçe simülasyonu + OOM enjeksiyonu (L11, K-LOC-04).
3. **Doğruluk kapısı** — quantize delta (K-LOC-05) — **block**.
4. **Cihaz kapısı** — hedef cihaz matrisi koşusu (K-LOC-03).
5. **Çevrimdışı kapısı** — ağ kesme senaryoları (§6.2).
6. **Termal kapısı** — uzun-run (§9.3).
7. **Durum kapısı** — §17 matrisinin tüm geçişleri (log kanıtı ile).

---

## §25. Revizyon Kaydı

| Tarih | Değişiklik | Yapan | Not |
|---|---|---|---|
| 2026-10-15 | İlk yazım (dosya oluşturma) | K006 vault içerik ajanı | Append-only; §1–§25 |
| — | Sonraki revizyon | — | Yalnız ek bölüm olarak |

**Authority:** Bayram Ali / Vault Steward · **status:** active — §13 açık soruları `verified` için engel değil, kayıt olarak durur.
