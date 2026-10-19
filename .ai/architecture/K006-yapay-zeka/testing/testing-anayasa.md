---
node_id: K006-yapay-zeka/testing/testing-anayasa
node_type: module
status: active
parent_id: K006-yapay-zeka/testing
children:
  - K006-yapay-zeka/testing/testing-kapsam
  - K006-yapay-zeka/testing/testing-bilesenler
  - K006-yapay-zeka/testing/testing-dogrulama
  - K006-yapay-zeka/testing/testing-ornekler
  - K006-yapay-zeka/testing/testing-web-arastirma
depends_on:
  - K006-yapay-zeka/evaluation/evaluation-anayasa
  - K006-yapay-zeka/inference/inference-anayasa
  - K006-yapay-zeka/model-management/model-management-anayasa
evidence:
  - "Dosya: .ai/architecture/K006-yapay-zeka/index.md [Satır: 267]"
  - "Dosya: .ai/architecture/K006-yapay-zeka/evaluation/evaluation-anayasa.md [Satır: 1]"
  - "Dosya: .ai/projects/NevaEngine/legacy-2026-07/ai-models.md [Satır: 577]"
validation: "İçerik doğrulama: 2026-10-15 — anayasa, kök index.md §2.6 (satır 267) ve kardeş evaluation-anayasa.md ile eşleştirildi"
open_questions:
  - "K006 içi test altyapısının mevcut CI'ya entegrasyonu (hangi job, hangi gate) VERIFICATION REQUIRED — CI yüzeyi K006 dışı"
  - "Deterministik test için model sabitleme (seed/versiyon kilidi) stratejisi karar bekliyor"
  - "Yük/stres test altyapısı (kaç eşzamanlı, hangi ortam) UNKNOWN"
group: mimari
---
# Testing — Alt Dal Anayasası (kademe-2)

> **Bu dosya kademe-2 anayasadır.** Ebeveyn: `K006-yapay-zeka` (bkz. `index.md` §2.6 çocuk haritası — v2 satır 267, `G:\` arşivinde).
> Kardeş anayasalar: `../evaluation/evaluation-anayasa.md`, `../inference/inference-anayasa.md`, `../model-management/model-management-anayasa.md`.
> Bu dosya **şablon içermektedir** — tüm alanlar somut değerle doldurulmuştur.

---

## §1. Kapsam Sınırı

**testing**, K006'da **yapay zeka bileşenlerinin nasıl test edileceğinin** (altyapı, tip dağılımı, determinizm, CI kapısı) usulünü kapsar. Kapsayıcılar:

1. **Test piramidi** — birim / entegrasyon / uçtan-uca / yük testlerinin K006'ya uyarlanması.
2. **AI-özel test türleri** — determinizm, drift, prompt/regresyon, sahte (mock) model, adversarial girdi.
3. **Test altyapısı** — harness, fixture, seed, rapor üretimi (evaluation ölçümünün **koşturucusu**).
4. **CI kapısı** — ne zorunlu, ne opsiyonel; geçmeyen kod yayılmaz.

**Kapsam dışı:**

- **Ne** ölçüleceği / eşik değerleri → `evaluation` (burada yalnız **nasıl çalıştırılır** tanımlanır)
- Model **sürümü/kaydı** → `model-management`
- Canlı **canary/drift gözlemi** → `inference` (burada yalnız test-stratejisi **bağlantısı** vardır)
- Kod-unitsel test kütüphaneleri seçimi → K006 dışı (platform); burada **sözleşme ve disiplin** vardır

**Kapsam sınırı testi:** "Bu, **çalıştırma/organize etme** ile ilgili mi?" → Evet = testing. "Bu, **eşik/metrik kararı** mı?" → evaluation.

---

## §2. İlke ve Değerler

### §2.1 Öncelik sırası

| # | İlke | Gerekçe |
|---|------|---------|
| 1 | **Ölçüm yoksa geçiş yok** | Gate, çalıştırılan testten gelir — "geçti" dedikodusu sayılmaz (evaluation K-EVAL-01) |
| 2 | **Determinizm test edilir, umulmaz** | Rastgelelik (seed) sabitlenmeden regresyon testi güvenilmez |
| 3 | **Model-mock ayrımı** | Birim testte gerçek model = yavaş + kırılgan; sözleşmeli sahte kullanılır |
| 4 | **Test, prod'a benzer** | Sapma olan test sahte güvenlik verir |
| 5 | **Yeşil-kırmızı dürüstlüğü** | Flaky (titrek) test, hiç testten kötüdür — flaky = FAILED sayılır |

### §2.2 Zero-hallucination bağlayıcılığı

1. "CI'da geçiyor" iddiası **iş/özet kanıtıyla** yazılır — kanıtsız `⚠️ VERIFICATION REQUIRED` (CI yüzeyi K006 dışı → §12.3.1).
2. Test/süre/kapsam yüzdeleri **ölçülmeden** yazılmaz; tahmini `UNKNOWN`.
3. Sahte (mock) model davranışı **kaynak** ile sınırlıdır — gerçeği **temsil iddiası** varsa doğrulama gerekir.
4. Flaky/oran istatistikleri sabit sayılmaz, ölçülür.

---

## §3. Mimari Roller ve Kararlar

### §3.1 Test katmanları

```
┌─────────────────────────────────────────────────────────┐
│ L4 — YÜK / STRES / DAYANIKLILIK                          │
│   • eşzamanlı çağrı, kuyruk taşması, zaman aşımları      │
├─────────────────────────────────────────────────────────┤
│ L3 — UÇTAN UCA (E2E)                                     │
│   • akış: çağrı → fallback → çıktı şeması (sözleşme)      │
├─────────────────────────────────────────────────────────┤
│ L2 — ENTEGRASYON                                          │
│   • sahte model + gerçek şema/ortam (önbellek, kuyruk)    │
├─────────────────────────────────────────────────────────┤
│ L1 — BİRİM                                                │
│   • şema doğrulama, reducer/normalizasyon, karar kuralları │
├─────────────────────────────────────────────────────────┤
│ L0 — AI-ÖZEL (test tipi)                                  │
│   • determinizm, regresyon-golden, drift, adversarial     │
└─────────────────────────────────────────────────────────┘
```

**Karar (K-TS-01):** **L0 bir katman değil, her katmana yayılan özellik**tir — determinizm/golden kontrolleri L1–L4'ün her yerinde uygulanır.
**Kanıt:** evaluation anayasası §5.1 (offline ölçüm) — test altyapısı bu kapıyı **koşturur**.

### §3.2 Sahiplik matrisi

| Alan | Sorumluluk | Not |
|------|-----------|-----|
| Test stratejisi + piramit | testing | bu anayasa |
| Eşik/metrik/kapı tanımı | `evaluation` | §6'da uygulanır |
| Test fixtures (golden set) | `evaluation` sahiplenir, testing **saklar/koşturur** | §5.2 |
| CI job/gate bağlaması | testing + platform (K006 dışı) | §7 |
| Mock model davranışı | testing (sözleşme) | §4.3 |

**Karar (K-TS-02):** evaluation **neyin geçip geçeceğini** söyler, testing **geçirip geçirmeme işini** yapar — eşik ve çalıştırma ayrılmazsa kapı sahipliği belirsizleşir (evaluation K-EVAL-02 ile aynı üçlü).

---

## §4. AI-Özel Test Tipleri

### §4.1 Determinizm testi

| Adım | İş |
|------|-----|
| Seed sabitleme | rastgelelik içeren her yol için `seed` (evaluation §17.1) |
| İki çalıştırma | aynı girdi + seed → **birebir aynı** çıktı (değilse FAILED — İlke 2) |
| Sürüm kilidi | model + şema sürümü sabitlenir (model-management M1) |

### §4.2 Regresyon / golden test

1. **Golden set** — evaluation'ın; testing **üretmez**, **koşturur** ve sonuçları `metrics_ref`'e bağlar.
2. **Prompt/şema değişimi** → otomatik tetik (inference §28.3 sözleşmesi).
3. **Kapı sonucu** — eşik altı = FAILED = yayın yok (A6/M4 ile uyumlu).

### §4.3 Model mock (sahte model) sözleşmesi

| Kural | Detay |
|-------|-------|
| Birim/integ | gerçek model **yok** — sözleşmeli sahte (yanıt şeması + gecikme/hata senaryoları) |
| Sahte, şemayla | mock yanıt **gerçek şema**dan üretilir (kayıp = sahte test yanıltır) |
| E2E (seçici) | gerçek model **yalnız** sınırlı, işaretli, gate'e **yazmayan** koşuda |
| Sahte gerçekler | ne-sağlar/ne-sağlamaz **açık** (gerçek kalite ölçümü değildir — evaluation'ın işi) |

### §4.4 Adversarial / hata-sınıfı testleri

- Prompt injection / şema dışı girdi / aşırı uzunluk → inference §17 kapılarıyla **aynı** senaryolar (G1–G8 benzeri).
- Fallback zinciri her hata sınıfında **çalışır** (inference §15) — kırık zincir testte yakalanır.
- PII/redaction: maskelenmeme = FAILED (inference §17 G6).

---

## §5. Determinizm ve Fixture Disiplini

### §5.1 Determinizm kuralları

1. Testlerde **zaman/sa/rastgelelik** sabitlenir (seed + sabit zaman kaynağı).
2. Ağ bağımlılığı olan testler **işaretlidir** (network-gated) — varsayılan koşuda atlanır ama **raporda görünür** (atlandı-sayacı).
3. Paralel koşuda **paylaşımlı durum yasak** (fixture başına izolasyon).
4. Aynı test iki kez koşar → **aynı sonuç** (flaky kontrolü, İlke 5).

### §5.2 Fixture / golden set saklama

| Kural | Detay |
|-------|-------|
| Sahiplik | golden set `evaluation`'ın; testing **sürümü sabit** koşturur |
| Sürümleme | fixture `schema_version`/`model_ref` ile damgalı (mismatch = FAIL — §7.4) |
| PII | fixture **ham kullanıcı verisi içermez** (evaluation K-EVAL-13 ile uyumlu) |
| Büyüme | yeni durum = yeni fixture satırı (append-only; eski silinmez) |

**Karar (K-TS-03):** Test fixture'ı **kod gibi sürüm kontrolündedir** — sürümü olmayan fixture ile kapı **çalıştırılmaz** (kanıt-sız kapı = kapı değil).

---

## §6. CI Kapısı (test → yayın)

### §6.1 Kapı sırası

```
[1] statik/şema kontrolleri        → hızlı, her PR
[2] L1 birim                       → her PR
[3] L2 entegrasyon (mock model)    → her PR / birleştirici
[4] L0 determinizm + golden regresyon → her PR (değişiklik varsa)
[5] L3 E2E (sınırlı)               → birleştirici / gece
[6] L4 yük/stres                   → periyodik / yayın öncesi
[7] gate sonucu → `metrics_ref` → yayın izni (evaluation kapı matrisi)
```

### §6.2 Zorunlu / opsiyonel

| Kapı | Seviye |
|------|--------|
| [1]–[4] | **ZORUNLU** — geçmeyen kod yayılmaz |
| [5] | ZORUNLU (yayın öncesi); PR'da opsiyonel ama raporlanır |
| [6] | Yayın öncesi zorunlu, günlükde opsiyonel (sıklık ADR, `UNKNOWN` → §12.3.3) |
| Ağ/uzun testler | işaretli + atlananlar **sayılır** (atlandı ≠ geçti) |

**Karar (K-TS-04):** **Atlanan test geçmiş sayılmaz** — rapor `passed / failed / skipped` ayrı; `skipped` oranı **metriktir** (şişirme alarmı).

### §6.3 Hız dengesi

Kapı [1]–[4] **dakikalar** içinde dönmelidir (yavaş kapı baypas edilir) — süre `UNKNOWN` (platform ölçümü), **hedef**: PR geri bildirimi **günü aşmaz**; aşıyorsa L5/L6'ya bölünür (bölme kuralı §6.1).

---

## §7. Sözleşme ve Şema Testleri

| Test | Ne doğrular | Kaynak |
|------|-------------|--------|
| İstek/yanıt şeması | alan adı/tip/nullable | inference §12 |
| Fallback zinciri | her hata sınıfının dalı | inference §15 |
| Cache anahtarı | sürüm + PII filtresi | inference §14, §28 |
| Redaction | maskelenme | inference §17 G6 |
| İzin | reddi | inference §17 G3 |
| Model kartı | §4 zorunlu alanları | model-management §4 |
| Profil şeması | `retention_until` dahil | personalization §4.1 |
| Analiz çıktısı | `confidence/evidence/source` | music-analysis §7 |

**Zorunlu kural (K-TS-05):** Sözleşme testleri **kod-ile-aşağı** çalışır (contract drift = FAIL) — şema değişikliği **test değişikliği olmadan** birleştirilemez.

---

## §8. Rehberler (Guidelines)

1. **Önce sözleşmeyi test et** — piksel-doygunluk testlerinden önce şema (§7).
2. **Mock gerçek şemadan** doğar (K-TS-03 benzeri) — elle yazılmış sahte yanıt yasak.
3. **Test adı = davranışı anlatır** — "test1" yok; başarısızlıkta **neden** görünür.
4. **Flaky = hata** — tekrar-koşu ile geçti diye sayılmaz (İlke 5).
5. **Pahalı test işaretli** — yük/E2E gerçek model koşuları bayrak + raporlu (§6.2).
6. **Paralel izolasyon** — fixture başına izolasyon (§5.1.3).
7. **Kapı süresi izlenir** — yavaş kapı ölçülür, bölünür (§6.3).
8. **Test = belge** — senaryo listesi, anayasa §4/§6 ile **eşleşir** (eksik = kapsam açığı raporu).

---

## §9. Zorunlu Kurallar (MUST / MUST NOT)

| # | Kural | Seviye |
|---|-------|--------|
| T1 | PR kapıları [1]–[4] **zorunlu** ve yeşil olmadan yayım yok (§6.2) | MUST |
| T2 | `skipped` test **geçmiş sayılmaz**; ayrı raporlanır (K-TS-04) | MUST |
| T3 | Determinizm testi seed + sürüm kilidi ile çalışır (§4.1) | MUST |
| T4 | Birim/integ testlerde **gerçek model** kullanılmaz (§4.3) | MUST NOT (kullanma) |
| T5 | Golden/fixture **sürümlü** olmalı; sürüm uyuşmazsa FAIL (K-TS-03, §5.2) | MUST |
| T6 | Sözleşme testi olmadan şema değişikliği birleştirilemez (K-TS-05) | MUST |
| T7 | Flaky test **FAILED** sayılır; onarım/yayım dışı bırakılır (İlke 5) | MUST |
| T8 | Ağ/uzun test **işaretli** ve atlananlar sayılır (§5.1.2, §6.2) | MUST |
| T9 | Eşik/kapı değeri testte **tekrar-üretilmez** — `evaluation`'dan gelir (K-TS-02) | MUST NOT (ikinci eşik) |
| T10 | Fixture **ham PII içermez** (§5.2) | MUST NOT |
| T11 | Test raporu `metrics_ref` ile **gate kaydına** bağlanır (§6.1.7) | MUST |

---

## §10. Karar Kaydı

| Karar | İçerik | Kanıt |
|-------|--------|-------|
| K-TS-01 | L0 (AI-özel) katman değil, yayılan özellik | bu dosya §3.1 |
| K-TS-02 | Eşik (evaluation) ≠ çalıştırma (testing) | evaluation K-EVAL-02 |
| K-TS-03 | Fixture sürüm kontrolünde; sürümü olmayan kapı çalışmaz | bu dosya §5.2 |
| K-TS-04 | `skipped` ≠ geçti; oran metriktir | bu dosya §6.2 |
| K-TS-05 | Sözleşme testi olmadan şema değişimi yok | bu dosya §7 |

**Reddedilen alternatifler:**

- *Her testte gerçek model* → yavaş + kırılgan + ölçü-meşru olmayan kapı (T4).
- *Tekrar-koşu ile yeşile çevirme* → flaky gizleme (T7).
- *Kendi eşik testte* → iki gerçeklik (T9).
- *Sürümsüz fixture* → kanıtsız kapı (K-TS-03).

---

## §11. Bağımlılıklar

### §11.1 Bağımlı olduğu alanlar

| Alan | Ne alır | Kaynak |
|------|---------|--------|
| `evaluation` | kapı eşikleri + golden set + rapor şeması | `../evaluation/` |
| `inference` | sözleşmeler (§7) + hata sınıfları | `../inference/` |
| `model-management` | kart şeması + sürüm kilidi | `../model-management/` |
| Platform CI (K006 dışı) | job/örtü ortamı | §12.3.1 |

### §11.2 Bağımlı olan alanlar

| Alan | Ne verir |
|------|----------|
| `evaluation` | gate **çalıştırma** sonucu (`metrics_ref`) |
| tüm dallar | kapı [1]–[5] yayım izni |
| release sahibi | §6.1 çıktısı (geç/çek raporu) |

---

## §12. İlişkili Dosyalar ve Açık Sorular

### §12.1 Kardeş dosyalar (bu dal)

`index.md` · `testing-kapsam.md` · `-bilesenler.md` · `-dogrulama.md` · `-ornekler.md` · `-web-arastirma.md` · kök `../yapay-zeka-kararlar.md` + `../yapay-zeka-riskler.md`

### §12.2 Vault referansları

| Dosya | Ne verir | Not |
|-------|----------|-----|
| `K006-yapay-zeka/index.md` [Satır: 267] | ebeveyn bağlantısı | v2 §2.6 |
| `evaluation/evaluation-anayasa.md` | kapı usulü + E-kuralları | kardeş (§1 bağ) |
| `ai-models.md` [Satır: 577] | §9.1 ölçüm metodolojisi | golden/regresyon bağlamı |
| `inference-anayasa.md` | §12/§15/§17 sözleşmeleri | §7 test kaynakları |

### §12.3 Açık sorular

1. **CI entegrasyonu** — hangi job/gate/ortam → K006 dışı yüzey → `⚠️ VERIFICATION REQUIRED` (§2.2.1).
2. **Determinizm stratejisi detayı** — hangi bileşende seed vs. saf determinizm → `UNKNOWN`.
3. **Yük test sıklığı/ortamı** — L4 için ortam + periyot ADR → `UNKNOWN`.
4. **Flaky toleransı** — kabul edilen flaky oranı eşiği → `UNKNOWN` (yoklanınca: tolerans **0** varsayılır — muhafazakâr, İlke 5).
5. **Test süresi bütçesi** — kapı başına saniye/dakika hedefi → `UNKNOWN` (§6.3 hedefi yalnız "gün aşmaz").

---

## §13. İlgili Lint Maddeleri

| LINT (`.ai/CLAUDE.md` §6) | Bu anayasadaki karşılığı |
|---------------------------|--------------------------|
| Zero-hallucination | §2.2 — "CI geçti" kanıtlı, süre/üre yok uydurmalı |
| `UNKNOWN` / `VERIFICATION REQUIRED` | §12.3 |
| Kanıt zorunluluğu | §10 karar tablosu + `metrics_ref` (T11) |
| Keyfi eşik yasağı | T9 — eşik evaluation'dan gelir |
| Ölçüm percentil/istatistik | gate raporları evaluation §17'ye uyar |

---

## §14. Bağlam ve Versiyon

| Alan | Değer |
|------|-------|
| Sahip | K006-yapay-zeka / testing |
| Ebeveyn | `K006-yapay-zeka` (kök `index.md` §2.6 — v2 satır 267) |
| Kademe | 2 |
| Durum | active |
| Oluşturulma | 2026-10-15 |
| Kapsanan sibling'lar | `testing-kapsam` · `-bilesenler` · `-dogrulama` · `-ornekler` · `-web-arastirma` · `index.md` |
| Grup | mimari (`group:` frontmatter) |
| Belge tipi | anayasa (kademe-2, bu dalın usul belgesi — şablon değil) |

---

## §15. Bileşen Sözlüğü (TR↔EN, bu dalın terimleri)

| TR | EN | Bu anayasadaki anlamı |
|----|----|------------------------|
| Test piramidi | test pyramid | L1–L4 katmanları (§3.1) |
| Sahte model | model mock | sözleşmeli yerine geçici (§4.3) |
| Determinizm | determinism | aynı girdi → aynı çıktı (§4.1) |
| Golden test | golden test | bilinen-sonuç regresyon (§4.2) |
| Titrek test | flaky test | şansa göre geçip-kalan (İlke 5) |
| Atlama | skip | koşulmayan test (K-TS-04) |
| Sözleşme testi | contract test | şema/alan doğrulama (§7) |
| Kapı | (CI) gate | yayım izni (§6) |
| Fixture | fixture (versioned) | sürümlü test verisi (§5.2) |
| Tetikleme | trigger | kapı koşulu (§6.1) |
| Kanıt bağlantısı | evidence ref (`metrics_ref`) | gate↔rapor bağı (T11) |

**Terim kuralı:** Olmayan terim bu dalda icat edilmez — önce `inference-anayasa.md` §9 sözlüğüne bakılır.

---

## §16. Durum Matrisi (test çalıştırma durumları)

| Durum | Tanım | Yayın etkisi |
|-------|-------|--------------|
| `PASSED` | tüm zorunlu kapılar yeşil | izin (kapı [7]) |
| `FAILED` | zorunlu kapı kırmızı (dahil: flaky-onarılanlar) | **blok** |
| `SKIPPED` | koşulmayan (işaretli/örtük) | sayılır; oran metriği (K-TS-04) |
| `BLOCKED` | bağımlı test/bağımlı kapı beklemede | yayın **bekler** |
| `FLAKY` | tekrar içinde değişken | FAILED muamelesi (T7) |
| `ERROR` | altyapı/harness hatası | **FAILED sayılır** (İlke 5 — yeşil sahtesi yok) |
| `QUARANTINE` | bilinen-flaky, geçici karantina | yayın **engellemez ama raporda görünür** + onarım SLA (`UNKNOWN` → §12.3.4 muhafazakâr 0-tolerans) |

**Zorunlu kural:** `ERROR`/`FLAKY` **asla `PASSED`'a yuvarlanmaz**; `QUARANTINE` bileşimi **limitlidir** — limit aşımında `FAILED` (kota `UNKNOWN` ise 0, muhafazakâr).

---

## §17. Hata Sınıfları ve Test Davranışı

| Sınıf | Örnek | Davranış | Kayıt |
|-------|-------|----------|-------|
| Şema | alan eksik | FAIL + hangi alan | sözleşme raporu |
| Determinizm | seed farkı çıktı | FAIL + diff özeti | §4.1 raporu |
| Mock uyuşmazlığı | mock gerçek şemadan saptı | mock-sözleşme FAIL | K-TS-03/§4.3 |
| Fallback | dal çalışmıyor | FAIL | inference §15 senaryosu |
| Performans | kapı süresi aşıldı | **ölçüm + rapor**; yayın etkisi §6.3 (böl) | süre metriği |
| Ortam | bağımlı servis yok | `SKIPPED` **değil** — `ERROR`/`BLOCKED` | ağ işaretli koşu §5.1.2 |
| Eşik | gate sonucu eksik | `metrics_ref` yok → **kullanılamaz** (T11) | gate kaydı |

**Kural (K-TS-06):** Test hatası **ürün hatasıyla karıştırılmaz** — sınıfa göre rapor (altyapı hata raporu vs. kod hata raporu), ikisi de yeşile **yuvarlanmaz**.

---

## §18. Güvenlik Testleri (AI yüzeyi)

| Alan | Test | Kaynak |
|------|------|--------|
| Prompt injection | şema dışı/menkup girdi reddi | inference §17 G1/G2 |
| İzin | izinsiz araç çağrısı reddi | G3 |
| PII/redaction | maskelenmeme = FAIL | G6 |
| Rate limit | taşma davranışı | G8 |
| Model kartı şeması | zorunlu alan (`license` vb.) | model-management §4 |
| Profil silme | üç-katmanlı silme | personalization P6 |
| Fixture PII | ham veri taraması | T10 |

**Zorunlu kural (K-TS-07):** Güvenlik testleri **CI [1]–[4]** içindedir (opsiyonel/gece değil) — güvenlik kapısı yavaşlamaz, **erken** çalışır.

---

## §19. Anti-Patternler (yasak kısayollar)

| Anti-pattern | Neden zararlı | Doğrusu |
|--------------|---------------|---------|
| "Testi atla, yeşil olsun" | SKIPPED şişirme | K-TS-04 (sayılır) |
| "Flaky'i tekrar çalıştır" | sahte yeşil | T7 (FAILED) |
| "Her test gerçek model kullansın" | yavaş/kriz | mock (T4) |
| "Eşiği testte 50 yapalım" | iki gerçeklik | T9 (evaluation'dan) |
| "Fixture'ı ben bilirim" | sürüm kaybı | K-TS-03 |
| "CI sonucu hata verdi, geçti say" | ERROR→PASSED | İlke 5, §16 |
| "Şema değişti test sonra" | sözleşme kaybı | K-TS-05 |
| "Test yok, kod sağlam" | kanıtsız | §6 kapı |

---

## §20. Uç Nokta Sözleşmesi (giriş/çıkış)

### §20.1 Giriş

| Girdi | Sağlayıcı |
|-------|-----------|
| Kapı eşikleri + golden set | `evaluation` (K-TS-02) |
| Sözleşmeler (şema/fallback/redaction) | `inference` §12/§15/§17 |
| Model kartı/şema | `model-management` §4 |
| CI ortamı (job) | platform (K006 dışı, §12.3.1) |

### §20.2 Çıkış

| Çıktı | Alıcı | Biçim |
|-------|-------|-------|
| Kapı raporu (`passed/failed/skipped` + süre) | release sahibi + `evaluation` | §6 |
| `metrics_ref` (gate kaydı) | `model-management`/release | T11 |
| Flaky/karantina listesi | dal sahipleri | §16 |
| Kontrat-drift bulguları | ilgili dal dosyaları (`*-bilesenler.md` · `*-dogrulama.md`) | §7 |
| Süre metrikleri (kapı hızı) | dashboard | §6.3 |

---

## §21. Kaynaklar ve Red-Team Soruları

### §21.1 Kaynaklar

| # | Kaynak | Kullanım |
|---|--------|----------|
| R1 | `K006-yapay-zeka/index.md` [Satır: 267] | ebeveyn bağlantısı |
| R2 | `evaluation/evaluation-anayasa.md` | kapı usulü (K-TS-02) |
| R3 | `ai-models.md` [Satır: 577] | §9.1 ölçüm çerçevesi |
| R4 | `inference-anayasa.md` | §12/§15/§17 test kaynakları |
| R5 | Web kaynağı | AÇILMADI — bu turda URL eklenmedi (`testing-web-arastirma.md`'ye devredildi) |

### §21.2 Red-Team soruları

| # | Soru | Beklenen cevap |
|---|---|---|
| T1 | Skip edip yeşil dersek? | oran raporlanır, şişirilemez — K-TS-04 |
| T2 | Flaky 3 kez koşunca geçti mi? | FAILED — T7 |
| T3 | Eşik testte farklı mı? | hayır, evaluation'dan — T9 |
| T4 | Gerçek model her PR'da mı? | birim/integ'de hayır — T4 |
| T5 | Fixture PII içerirse? | FAIL — T10 |
| T6 | Şema değişti, test güncellenmedi? | birleştirilemez — K-TS-05 |
| T7 | Altyapı hatası yeşile döner mi? | `ERROR`→FAILED — §16, K-TS-06 |
| T8 | Kapı 40 dakika sürse? | ölç + böl — §6.3 |
| T9 | Güvenlik testi geceye atılabilir mi? | hayır, [1]–[4] — K-TS-07 |
| T10 | Gate raporu olmadan yayın? | `metrics_ref` yoksa yok — T11 |

### §21.3 Revizyon Kaydı

| Tarih | Değişiklik | Yapan | Not |
|---|---|---|---|
| 2026-10-15 | İlk yazım (dosya oluşturma) | K006 vault içerik ajanı | Append-only; §1–§21 |
| — | Sonraki revizyon | — | Yalnız ek bölüm olarak |

**Authority:** Bayram Ali / Vault Steward · **status:** active — §12.3 açık soruları CI/ADR ile kapatılır.

---

## §22. Ek Usul — Değişiklik Eşiği Tablosu (ne değişince ne tekrarlanır)

| Değişen yüzey | Tekrarlanacak kapı | Neden |
|---|---|---|
| Sözleşme (şema/fallback/redaction) | §6.1 [1] + [4] (sözleşme + golden) | K-TS-05 |
| Model/şema sürümü (`model_ref`) | [4] determinizm + golden regresyon (§4.1–2) | çıktı dağılımı |
| Gate eşikleri (`evaluation`) | [4]/[7] yeniden bağlama (T9 — test eşik **üretmez**) | kapı tanımı |
| Fixture sürümü | tüm [2]–[5] koşuları (K-TS-03) | kanıt tazeliği |
| Mock sözleşmesi | [2] + [3] (§4.3) | sahte-gerçek sapması |
| CI job tanımı (K006 dışı) | §12.3.1 doğrulama + rapor akışı | kanıt bağlantısı kesilmesin |
| Flaky karantina kotası | [6] + karantina raporu (§16) | tolerans `UNKNOWN`-ken 0 |

**Son kural:** Tabloda olmayan değişiklik bile, **zorunlu kapılar [1]–[4] + `metrics_ref` (T11) olmadan** yayılmaz — bu ikisi her değişikliğin alt kümesidir.

---

## §23. Ek Usul — Test Raporu Başlıkları (zorunlu)

```markdown
# <sürüm/PR> — test raporu
1. Koşu künyesi: kod sürümü + model/şema + fixture sürümü + tarih
2. Kapı matrisi: [1]–[6] her satır: passed / failed / skipped / blocked + süre
3. `skipped` dökümü: hangi test, neden (işaret/örtük) — sayı (K-TS-04)
4. Flaky/karantina: liste + durum (T7, §16)
5. Golden/regresyon: `metrics_ref` + gate sonucu (T11)
6. Sözleşme bulguları: şema drift (§7)
7. Güvenlik testleri: G-sınıfları sonucu (K-TS-07)
8. Kapı süreleri: her kapı süresi + bütçe ihlali (§6.3)
9. Sonuç: PASS / BLOCK + release sahibi onayı
```

**Zorunlu kural:** Başlık 2 ve 5 **boş olamaz** — kapısı çalıştırılmamış/kaydı olmayan sürüm **BLOCK**'dur (§6.2).
