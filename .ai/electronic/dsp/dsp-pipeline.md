---
type: electronic
category: dsp-pipeline
title: "CoreMusic — DSP Processing Pipeline"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.1.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — DSP Processing Pipeline

**See also:** [[electronic/dsp/index]] · [[ADR-017-dsp-hardware-mode]] · [[ADR-025-professional-eq-system]] · [[ADR-062-dsp-pipeline-architecture]]

---

## 1. Amaç

DSP Processing Pipeline, ses sinyalinin girişten çıkışa kadar tüm işleme adımlarını tanımlar. Her aşama
bağımsız bir modül olarak geliştirilir. Bu dosyanın bağlayıcı omurgası **ADR-062 (DSP Pipeline Architecture)**tur:
aşama sırası, bypass davranışı, gecikme toplama ve parametre değişimi kuralları oradan gelir.

**Dürüstlük notu (bu dosyanın her sürümünde taşınır):** Bu vault dosyası spesifikasyon/tasarım belgesidir;
`NevaEngine/` dizini ve herhangi bir `.cpp`/`.h` kaynak dosyası repo'da **mevcut değildir** (PLANNED —
`.ai/electronic` envanteri ve kök `AGENTS.md` §Disk kanıtı). Aşağıdaki arayüzler, sayaçlar ve eşikler
**hedef tasarım**tır; çalışma zamanı doğrulaması (ölçüm) için bkz. §11. Sürüm kanıtı olmayan bağımlılıklar
(JUCE 9, ASIO SDK 2.3.4) `⚠️ VERIFICATION REQUIRED` ile işaretlidir.

---

## 2. ADR-062 Bağlayıcı Aşama Zinciri (SSOT)

ADR-062 §2, dokuz bağlayıcı aşama tanımlar. Sıra değişimi **yeni ADR** gerektirir (ADR-062 §2(b)):

```text
[1] Giriş (Input)         Input Gain + kanal seçimi
        │
        ▼
[2] Ön-hazırlık           Format / örnekleme hızı hazırlığı
        │
        ▼
[3] EQ Grafik             31 bant grafik EQ (ADR-025)
        │
        ▼
[4] EQ Parametrik         ≥2 bant parametrik EQ (ADR-025)
        │
        ▼
        [5] Dynamics       Compressor / Gate / Limiter
        │
        ▼
        [6] Efekt          Delay / Reverb / efekt zinciri
        │
        ▼
        [7] Mix / Route    Kanal karıştırma ve yönlendirme
        │
        ▼
        [8] Master         Master seviye / master işleme
        │                  ⚠️ Master EQ'nun bu aşamadaki eşlemesi
        │                  ADR-062'de açıkça yazılmamıştır →
        │                  VERIFICATION REQUIRED (ADR-062 §2(a)/5'teki
        │                  [11] Master EQ belirsizliği ile aynı sınıf)
        ▼
[9] Çıkış (Output)        Dither / output gain / format
```

**ADR-062'den bağlayıcı kurallar:**

| # | Kural | Kaynak |
|---|-------|--------|
| 1 | Aşama sırası değiştirilirse **yeni ADR** açılır; bu dosya tek başına sıra değiştirmez | ADR-062 §2(b) |
| 2 | **Bypass** bir aşamayı sıradan çıkarmaz — sinyal o aşamayı atlatır, zincir uzunluğu korunur | ADR-062 §2 |
| 3 | Her aşama kendi `getLatency()` değerini bildirir; **toplam gecikme aşamaların toplamıdır** — iç hedef <0.1 ms, kapı ADR-006: `<10 ms ASIO / <20 ms WASAPI` (ölçerek) | ADR-062 + [[ADR-006-...]] ⚠️ ADR-006 dosya adı için `.ai/.decisions/` indeksi |
| 4 | Tahsisat + kilit + I/O yasağı audio thread'de geçerlidir (tekrar yok — ADR-017'den devralınır) | ADR-062 §4 / [[ADR-017-dsp-hardware-mode]] |
| 5 | Parametre değişimi **lock-free** (SPSC kuyruk veya çift tampon), blok sınırında uygulanır; rampa ~5 ms one-pole yumuşatma | ADR-062 §3 |
| 6 | Buffer: **256 örnek default**, 128–512 aralığı (48 kHz) | ADR-062 |

> **⚠️ Aşama ≠ modül notu:** Crossover (bass management) ve Loudness normalleştirme ADR-062'nin dokuz
> aşaması arasında **ayrı aşama değildir**. Bu iki işlevin zincirde hangi aşamaya bindirildiği
> ADR-062 metninde açıkça yazılmamıştır → **VERIFICATION REQUIRED**. Taşınan tasarım kararları
> (hangi eşik, hangi sıralama) `.ai/electronic/dsp/crossover.md` ve `loudness.md` dosyalarındadır;
> oradaki sıralama iddiaları ADR-062 §2(b) (sıra değişimi = yeni ADR) ile çelişirse **ADR kazanır**.

---

## 3. Mevcut 13-Satır Şemanın ADR-062 ile Eşlemesi

Aşağıdaki şema bu dosyanın 1.0.0 sürümünden (2026-08-09) gelir ve korunur. ADR-062 kabulünden sonra
eşleme kısmidir — iki şema arasındaki farklar **uyumsuzluk değil, doğrulanmamış eşlemedir**:

```text
Audio Input (Raw PCM)
    │
    ▼  [1] Input Gain            ── ADR-062 aşama 1 (Giriş)
┌─────────────────┐
│  Input Gain     │  Sinyal seviyesi ayarı
└────────┬────────┘
         ▼  Noise Gate / HPF / LPF ── ADR-062'de AYRI aşama yok ⚠️
┌─────────────────┐                  (aşama 2 Ön-hazırlık'a mı,
│  Noise Gate     │  Düşük seviyeli   stage 5 Dynamics'e mi
│                 │  gürültü temizliği bindiği belirsiz →
└────────┬────────┘                  VERIFICATION REQUIRED)
         ▼
┌─────────────────┐
│  High Pass      │  Düşük frekans kesme (20–200 Hz)
└────────┬────────┘
         ▼
┌─────────────────┐
│  Low Pass       │  Yüksek frekans kesme (2–20 kHz)
└────────┬────────┘
         ▼  Parametric → Graphic ── ADR-062 aşama 3 (Grafik) → 4 (Parametrik)
┌─────────────────┐                  ⚠️ EŞLEME UYUMSUZLUĞU: eski şemada
│  Parametric EQ  │  Frekans+Gain+Q  Parametric ÖNCE, Graphic SONRA;
│  (31 Band)      │                  ADR-062 §2(a) sırası Graphic (3) →
└────────┬────────┘                  Parametric (4) SERİDİR. ADR kazanır;
         ▼                          eski şema sırası kullanılmaz.
┌─────────────────┐
│  Graphic EQ     │  Sadece Gain
└────────┬────────┘
         ▼  Compressor/Limiter ── ADR-062 aşama 5 (Dynamics)
┌─────────────────┐
│  Compressor     │  Dinamik aralık sıkıştırma
└────────┬────────┘
         ▼
┌─────────────────┐
│  Limiter        │  Maksimum seviye sınırı
└────────┬────────┘
         ▼  Loudness ── ADR-062'de ayrı aşama DEĞİL ⚠️ (bkz. §2 notu)
┌─────────────────┐
│  Loudness       │  perceived loudness
└────────┬────────┘
         ▼  Crossover ── ADR-062'de ayrı aşama DEĞIL ⚠️ (bkz. §2 notu)
┌─────────────────┐
│  Crossover      │  Frekans dağıtımı
│  (Bass Mgmt)    │  (80 Hz Linkwitz-Riley)
└────────┬────────┘
         ▼  Delay/Reverb ── ADR-062 aşama 6 (Efekt)
┌─────────────────┐
│  Delay          │  Gecikme (kanal senkron)
└────────┬────────┘
         ▼
┌─────────────────┐
│  Reverb         │  Mekânsal efekt
└────────┬────────┘
         ▼  Output Gain/Routing ── ADR-062 aşama 7 (Mix/Route) + 9 (Çıkış)
┌─────────────────┐                  ⚠️ Master (aşama 8) eski şemada yok.
│  Output Gain    │  Çıkış seviyesi   Master EQ eşlemesi belirsiz →
└────────┬────────┘                  VERIFICATION REQUIRED
┌─────────────────┐
│  Output Routing │  Kanal yönlendirme
│  (7.1 Surround) │  Front/Surround/Rear/Sub
└─────────────────┘
         │
         ▼
Audio Output (Processed PCM)
```

**Eşleme özeti:**

| Eski şema adımı | ADR-062 karşılığı | Durum |
|---|---|---|
| Input Gain | Aşama 1 Giriş | ✅ eşleşir |
| Noise Gate / HPF / LPF | Aşama 2 Ön-hazırlık veya Aşama 5 Dynamics | ⚠️ belirsiz |
| Parametric EQ → Graphic EQ | Aşama 3 Grafik → Aşama 4 Parametrik | ❌ sıra uyumsuz — ADR kazanır |
| Compressor / Limiter | Aşama 5 Dynamics | ✅ eşleşir |
| Loudness | — | ⚠️ aşama değil |
| Crossover (Bass Mgmt) | — | ⚠️ aşama değil |
| Delay / Reverb | Aşama 6 Efekt | ✅ eşleşir |
| Output Gain / Routing | Aşama 7 Mix/Route + Aşama 9 Çıkış | ⚠️ kısmi |
| (yok) | Aşama 8 Master | ❌ eski şemada eksik |

---

## 4. Aşama Arayüzü Sözleşmesi (IDSPStage)

Her aşama aynı arayüzü uygular (hedef tasarım — repo'da kod yok, §1). ADR-062'nin `getLatency()` ve
bypass kuralları bu arayüzün parçasıdır:

```cpp
// Hedef tasarım — NevaEngine/ henüz yok (PLANNED). Sözdizimi C++20.
class IDSPStage
{
public:
    virtual ~IDSPStage() = default;

    // Blok işleme — audio thread'de çağrılır, noexcept ZORUNLU.
    virtual void process(float* const* io, int numChannels, int numSamples) noexcept = 0;

    // ADR-062 §2: aşamanın toplam gecikmesi (ms). Toplam = Σ getLatency().
    virtual double getLatency() const noexcept = 0;

    // ADR-062 §2: bypass sıradan çıkarmaz, sinyali atlatır.
    virtual void setBypass(bool) noexcept = 0;

    // Parametre güncellemesi audio thread'e lock-free ile iletilir (§7).
    virtual void setParameter(int index, float value) noexcept = 0;
};
```

**Sözleşme kuralları:**

1. `process()` **`noexcept`** — istisna fırlatma audio thread'de tanımsız davranıştır (kök kural 3).
2. `process()` içinde tahsisat/kilit/I/O **yok** (§5).
3. `getLatency()` sabit maliyetli olmalı; blok içinde ölçülmez, aşamada bir kez hesaplanır.
4. Aşama sırası çalışma zamanında değiştirilemez — sıralama yapılandırmadır; değişimi ADR-062 §2(b)
   yasağı kapsar (yeni ADR gerekir).
5. Bypass durumu `std::atomic<bool>` veya eşdeğeri ile bildirilir; `process()` içinde kilit açılmaz.

**Geçici (intermediate) buffer disiplini:** Aşamalar arası buffer'lar baştan tahsis edilir
(ADR-017: 512 örnek, 48 kHz, `float32`). Toplam bellek bütçesi §9'dadır.

---

## 5. Zero-Allocation Kuralları

Audio thread'de ❌ yasak:

- `malloc()`, `free()`, `new`, `delete`
- `std::make_shared`, `std::vector` push_back
- `std::mutex`, `lock_guard`, `unique_lock` (lock-free zorunlu — kök kural 2)
- I/O blocking (disk, ağ, konsol)
- `throw`, `catch`
- `std::string` oluşurma/kopyalama (formatlama)
- Dinamik `std::function` atama (capturing lambda → heap)

✅ İzin:

- Stack tahsisi (blok sınırına kadar sabit boyutlu yerel diziler)
- `std::atomic` (seq_cst veya relaxed — seçimi §7)
- SIMD (SSE2/AVX2/NEON)
- `constexpr` (derleme zamanı hesap — kök kural 4)
- `alignas(64)` cache-line hizalama (kök kural 5)
- `std::span`/ham işaretçi üzerinden blok erişimi

**Denetim notu:** Yukarıdaki liste ADR-017'nin yasağı ile uyumludur; ADR-062 §4 yeni bir yasağı
**eklemez**, mevcut yasağı aşamalar tümüne uygular.

---

## 6. Buffer, Blok ve Örnekleme

| Parametre | Değer | Kaynak |
|---|---|---|
| Örnekleme hızı | 48 kHz (standart) | ADR-017 / sartname |
| Bit derinliği | 32-bit float | ADR-017 |
| Buffer default | **256 örnek** | ADR-062 §6 |
| Buffer aralığı | 128–512 örnek | ADR-062 §6 |
| Maksimum tahsis referansı | 512 örnek | ADR-017 |
| Kanal topolojisi | 8.1 (Neva Engine hedefi) | [[electronic/dsp/index]] |

**Blok süresi hesabı (48 kHz):**

| Blok | Süre (48 kHz) |
|---|---|
| 128 | 2.67 ms |
| 256 (default) | 5.33 ms |
| 512 | 10.67 ms |

> **Not:** Blok süresi = buffer / örnekleme hızı; bu gecikmenin *bir bileşenidir* — ASIO/WASAPI toplam
> gecikme ölçümü ayrıdır (ADR-006: `<10 ms / <20 ms` **ölçerek** doğrulanır). 512-örnek blok tek başına
> 10.67 ms olduğundan, <10 ms ASIO hedefi için ölçülen gerçek çalışma noktasının 128–256 aralığında
> olması beklenir — **bu bir tahmin değil, §11 ölçüm kapısının gerekçesidir**.

---

## 7. Parametre Değişimi (Lock-Free)

ADR-062 §3 bağlayıcıdır:

1. **Yol:** UI/worker thread → audio thread. Kilit **yasak**; iki izinli desen:
   - **SPSC kuyruk** (tek yazıcı → tek okuyucu): komut (index, value) çiftleri.
   - **Çift tampon / atomik yayın**: yazan taraf inactive tamponu doldurup atomik göstergeyi yayınlar.
2. **Uygulama anı:** Parametre değişimi **blok sınırında** uygulanır — blok ortasında değişmez.
3. **Yumuşatma:** Parametre sapmaları ~**5 ms one-pole** rampa ile yumuşatılır (klik/tıkırtı önleme).
4. **Bellek:** Kuyruk/tampon baştan tahsis edilir; kapasite taşması sessizce düşürülmez —
   taşıma durumu geri sayım/gözlem alanı ile işaretlenir (detay tasarım aşamasında, PLANNED).

**Atomik hafıza sıralaması tercihi:** Tek başına sayaç paylaşımında `memory_order_relaxed`,
yayın/onay el sıkışmasında `memory_order_acquire/release` — kesin seçimin Neva Engine implementasyon
kararı olması ve ölçümle (§11) doğrulanması gerekir → **VERIFICATION REQUIRED**.

---

## 8. SIMD ve Blok İşleme

| Konu | Hedef | Not |
|---|---|---|
| Vektör genişliği | SSE2 (min), AVX2 (x64 hedefi), NEON (ARM) | Kökke hedef platform listesi yok → platform seçimi PLANNED |
| İşleme birimi | Blok bütünü (128–512 örnek), örnek-örnek döngü değil | Vektörleştirmenin ön koşulu |
| Kanal düzeni | Kanal-ayrı (planar) `float**` blokları | JUCE/ASIO tipik düzeni; iç düzen implementasyonda kesinleşir |
| Hizalama | `alignas(64)` | Cache-line (kök kural 5) — unaligned load'larda da maliyet düşürür |
| Toplam gecikme | Aşama `getLatency()` toplamı <0.1 ms iç hedef | ADR-062 |

**SIMD uygunluk notu:** Biquad (EQ/filtre) katsayıları kanal başına olduğundan, blok-içi tekil katsayı
çarpımı vektörleştirilebilir; saf kanal-içi IIR dallanması (DF2T) skaler kalabilir. Hangi aşamanın
vektörleştiği ve ölçüm sonuçları bu dosyada **iddia edilmez** → implementasyon + profil sonrası
`§11` protokolü ile doldurulur (şu an için: UNKNOWN).

---

## 9. Performans Hedefleri

| Metrik | Hedef | Kaynak / durum |
|--------|-------|----------------|
| Latency (ASIO) | <10 ms | ADR-006 — **ölçerek** ⚠️ ölçüm PLANNED |
| Latency (WASAPI) | <20 ms | ADR-006 — **ölçerek** ⚠️ ölçüm PLANNED |
| İç aşama gecikme toplamı | <0.1 ms | ADR-062 |
| CPU (8+1 kanal) | <%15 | Hedef (ölçüm PLANNED) |
| Bellek | <%50MB | Hedef (ölçüm PLANNED) |
| Örnekleme | 48 kHz (standart) | ADR-017 |
| Bit Derinliği | 32-bit float | ADR-017 |
| Tahsisat/kilit/I/O (audio thread) | 0 | ADR-017 / ADR-062 |

> ⚠️ CPU <%15 ve bellek <%50MB hedefleri bu dosyanın 1.0.0 sürümünden gelir; hangi makine/kullanım
> senaryosu için ölçüleceği tanımsızdır → ölçüm protokolünde referans sistem tanımlanmalıdır
> (**VERIFICATION REQUIRED**).

---

## 10. Hata Senaryoları (FMEA)

| # | Hata | Etki | Tespit | Önlem |
|---|------|------|--------|-------|
| 1 | Aşama `process()` içinde tahsisat | Audio thread xrun/underrun | Profil (heap sayaçları) | §5 denetimi, kod incelemesi |
| 2 | Parametre değişimi blok ortasında | Tıkırtı/klik | Dinleme + spektrum | §7 blok sınırı + 5 ms rampa |
| 3 | `getLatency()` toplamı kapı aşımı | ADR-006 ihlali (<10 ms) | §11 ölçüm | Buffer 256→128 düşürme / aşama çıkarım ADR |
| 4 | Aşama sırasının koddan değiştirilmesi | ADR-062 §2(b) ihlali | Kod incelemesi | Sıra yapılandırmadır; değişiklik = yeni ADR |
| 5 | Bypass'ın sıradan çıkarması | Gecikme değişimi, faz kayması | §11 gecikme ölçümü | §2 kural 2 — bypass atlatmadır |
| 6 | Kapasite taşan parametre kuyruğu | Kayıp parametre güncellemesi | Gözlem alanı sayacı | §7.4 — ölçümle doğrulanacak tasarım |
| 7 | Master aşamasının (8) hiç yok edilmesi | Çıkış seviyesi kontrolsüz | Aşama envanteri | §3 eşleme — Master eksiği kapatılmalı |
| 8 | Loudness/Crossover'ın aşama sanılması | Sıra tartışmasında ADR ihlali | §2 notu | Crossover/Loudness ayrı aşama değildir |

---

## 11. Ölçüm Protokolü (Kapılar)

Ölçüm olmadan "hedef tuttu" **denmez** (kök kural: kanıtsız "bitti" yasak). Protokol:

1. **Gecikme (ADR-006):** Döngüsel test tonu → ASIO/WASAPI çıkışı → geri kayıt; uçtan uca gecikme
   `buffer/48000 + sürücü tamponu + aşama Σ getLatency()` bileşenlerine ayrıştırılır.
   Kapı: ASIO **<10 ms**, WASAPI **<20 ms** — her iki modda da **ölçülür**.
2. **Blok süresi:** §6 tablosu; çalışma noktası 128/256/512 için ayrıca raporlanır.
3. **CPU/bellek:** 8+1 kanal aktarımında örnek oranı; hedef §9. Referans sistem tanımlanır
   (şu an UNKNOWN).
4. **Zero-alloc:** Audio thread üzerinde heap sayacı (ör. işlem-içi) blok süresince **0** olmalıdır.
5. **Xrun:** Underrun/overrun sayacı 0 hedefi; ≠0 ise §10 senaryoları üzerinden ayrıştırma.
6. **Parametre tıkırtısı:** Sweep sırasında parametre rampası; spektrumda klik bileşeni aranır.

**Durum (2026-10-13):** Neva Engine kodu repo'da YOK olduğu için bu protokolün **hiçbiri
çalıştırılmamıştır** — tüm kapılar **PLANNED / ölçülmedi**. Bu dosya "başarılı ölçüm" iddiası taşımaz.

---

## 12. ADR Referansları

| ADR | Konu | Bu dosyadaki rol |
|-----|------|------------------|
| [[ADR-017-dsp-hardware-mode]] | XMOS, JUCE, ASIO; 512/48k/float32; tahsisat+kilit+I/O yasağı | Buffer/bitişik yasaklar |
| [[ADR-025-professional-eq-system]] | Çift EQ (31 grafik + ≥2 parametrik), sıra grafik→parametrik | Aşama 3–4 sırası |
| [[ADR-062-dsp-pipeline-architecture]] | 9 aşamalı bağlayıcı zincir, bypass, gecikme toplamı, parametre değişimi | **§2 SSOT** |
| ADR-006 | `<10 ms ASIO / <20 ms WASAPI` ölçerek | §9/§11 kapı ⚠️ dosya adı `.ai/.decisions/` indeksiyle doğrulanacak |

**Çakışma kuralı:** Bu dosya ile ADR'ler çelişirse **ADR kazanır** (`.ai/electronic/AGENTS.md` §3.2).
Özellikle: eski şemanın Parametric→Graphic sırası (§3) ADR-025/062'ye aykırıdır ve **ADR sırası
bağlayıcıdır**.

---

## 13. İlgili Dosyalar

- `[[electronic/dsp/index]]` — klasör indeksi ve ADR-062 9-aşama haritası
- `[[electronic/dsp/filters]]` — biquad/FIR matematiği (aşama içi filtreler)
- `[[electronic/dsp/equalizer]]` — Aşama 3–4 ayrıntısı (ADR-025 bağlayıcı parametreler)
- `[[electronic/dsp/crossover]]` — crossover/bass management (aşama değil ⚠️ §2)
- `[[electronic/dsp/dynamics]]` — Aşama 5
- `[[electronic/dsp/effects]]` — Aşama 6
- `[[electronic/dsp/loudness]]` — loudness normalleştirme (aşama değil ⚠️ §2)

---

## 14. Derinleştirme Kaydı (2026-10-13)

- §2 ADR-062 9-aşama bağlayıcı zinciri eklendi (dosya okunarak, uydurma yok); §3 eski 13-satır şema
  **korunarak** ADR-062 ile aşama aşama eşlendi (3 uyumsuzluk + 4 belirsizlik işaretli).
- §4 IDSPStage `noexcept`/`getLatency`/bypass sözleşmesi, §7 lock-free parametre değişimi
  (SPSC/çift-tampon/5 ms one-pole), §8 SIMD/blok, §11 ölçüm protokolü ve §10 FMEA eklendi.
- Eski "EQ öncesi Parametric→Graphic" sırası ADR-025/062'ye aykırı bulunup ❌ olarak işaretlendi.
- Tüm ölçümler PLANNED (§11): kod yok → kapılar ölçülmedi; hedefler "tuttu" olarak sunulmadı.
- `updated: 2026-10-13`, `version: 1.1.0`.

---

## 15. Bütçe Dağılımı ve Kurtarma Akışı (2026-10-13)

> Tahsisler **hedef**tir; ölçülmeden "gerçek" sayı yazılmaz (§11, ADR-005
> ruhu). Toplam CPU hedefi §9'da (<%15, 8+1 kanal).

### 15.1 Aşama CPU Bütçesi (Hedef Tahsis — Ölçüm Bekliyor)

| Aşama | Pay (hedef) | Dayanak |
|---|---|---|
| 1 Giriş (gain/kanal) | %2 | trivial |
| 2 Ön-hazırlık (format/SR) | %8 | SR dönüşümü varsa en ağır aday |
| 3 EQ grafik (31 bant × aktif) | %15 | biquad kaskad, §8 blok |
| 4 EQ parametrik (≥2) | %5 | kaskad |
| 5 Dynamics | %10 | zarf + lookahead pencere |
| 6 Efekt | %25 | reverb/delay en ağır (havuz §5 init) |
| 7 Mix/Route | %5 | toplama/çarpan |
| 8 Master | %5 | gain/ölçüm |
| 9 Çıkış (dither/format) | %5 | — |
| Boşluk (xrun payı) | %20 | ADR-017 xrun politikası — bilinçli boşluk |

> Bu oranlar **tasarım tahsisidir**, ölçüm değildir — §11/3 ile doldurulur.

### 15.2 Bellek Bütçesi Sayısal Kalemleri

| Kalem | Hesap | Tahmin |
|---|---|---|
| Blok buffer'ı (9 aşama arası, 512 × 4 B) | 512×4×2×9 | ≈ 36 KB |
| Parametrik kuyruk (kapasite 256 komut × 8 B) | 256×8 | 2 KB |
| Efekt havuzları (§effects 7.3) | delay 768 KB + reverb ~96 KB | ≈ 864 KB |
| Limiter lookahead (5 ms × fs × 4 B × kanal) | 960 B × 9 | ≈ 8.4 KB |
| **Toplam mertebe** | — | **≈ 1 MB altı** — <%50 MB hedefinin çok altında |

### 15.3 Xrun Kurtarma Akışı (ADR-017 politikası)

```text
callback sonunda:
  if (xrun_tespit) {
      atomic_sayaci++ ; atomik bayrak-set  (string log YASAK)
  }
izleyici (non-RT):
  bayrak → telemetri (§15.4)
  tekrarlayan xrun → remedy sırası:
     1) CPU yükü profili (aşama payı §15.1 sapması)
     2) buffer 256 → 512 (latency karşılığı: +5.33 ms, §6)
     3) efekt kapasitesi/düşürme (tasarım değişikliği → ADR?)
  ADR-006 kapısı: yeni noktada <10/<20 ms YENİDEN ölçülür (§11/1)
```

### 15.4 Telemetri Sayaçları (atomik, RT-safe)

| Sayaç | Tür | Kullanım |
|---|---|---|
| xrun_count | atomic u64 | §15.3 |
| callback_worst_us | atomic (blok başına max) | §11/2 kapı |
| param_queue_drop | atomic u32 | §7.4 tasarım riski |
| stage_worst_us[9] | atomic dizi | §15.1 sapma ölçümü |
| clamp/finite events | atomic u32 | `filters` §9.3 watchdog |

---

## 16. Pipeline Kabul Kontrol Listesi (Uygulama Öncesi)

Pipeline zinciri kodlanmadan/eklenmeden önce adımlar sırayla tamamlanır; atlanan adım varsa zincir **kabul edilmez**.

| # | Adım | Referans | Çıktı (kayıt zorunlu) |
|---|------|----------|------------------------|
| 1 | 9 aşama sırasını ADR-062 ile karşılaştır | §2 · ADR-062 §2(a) | sıra denetimi |
| 2 | Aşama arayüzünü uygula (IDSPStage: init/process/bypass/getLatency) | §4 | arayüz denetimi |
| 3 | Tüm buffer/havuzları init'te allocate et | §5 | tahsis logu (yalnız init) |
| 4 | Parametre geçişini lock-free kanala bağla | §7 | param_queue_drop = 0 |
| 5 | Blok boyutunu 512 / fs 48 kHz / float32 sabitle | §6 · [[ADR-017-dsp-hardware-mode]] | yapılandırma |
| 6 | Telemetri sayaçlarını bağla (atomik) | §15.4 | sayaç listesi |
| 7 | FMEA hata senaryolarını gözden geçir | §10 | senaryo tablosu |
| 8 | Ölçüm kapılarını çalıştır (§11) ve soak yap | §11 · [[electronic/dsp/index]] §8 | ölçüm raporu |
| 9 | Latency bütçesini hesapla (iç <0.1 ms, ADR-006 <10/<20 ms) | §11/1 · ADR-006 | latency raporu |

### 16.1 Pipeline Sıra İhlali Senaryoları (DUR Kapıları)

| # | İhlal | Sonuç | Kaynak |
|---|-------|-------|--------|
| 1 | EQ bloklarının yeniden sıralanması (paralel şube açılması) | DUR — yeni ADR gerekir | ADR-062 §2(a) |
| 2 | Crossover/loudness'ın ADR-062'de ayrı aşama olarak eklenmesi | DUR — eşleme kanıtlanmamış (`⚠️ VERIFICATION REQUIRED`) | [[electronic/dsp/index]] §1 uyarısı |
| 3 | Blok boyutunun 512 dışına taşınması | DUR — ADR-017 ihlali | §6 · [[ADR-017-dsp-hardware-mode]] |
| 4 | Callback içinde tahsis/lock tespiti | DUR — kural 1/2 ihlali | §5 |
| 5 | Aşama latency'sinin `getLatency()`'siz toplanması | DUR — ölçüm geçersiz | §4 |
| 6 | Callback süresi bütçenin %80'ini aşarsa (spike) | UYARI → kırmızı; §15.3 remedy sırası | §11/2 · §15.3 |

**Not:** Bu bölümdeki kapılar mevcut §11 ölçüm protokolü ve ADR-062/ADR-017 ile **aynı** değerleri kullanır; yeni tolerans **tanımlanmaz**. Ölçümü olmayan iddia `⚠️ VERIFICATION REQUIRED` işaretlenir. Repo'da Neva Engine C++ kodu **YOK** (PLANNED) — bu bölüm yalnız tasarım/ölçüm disiplinini taşır.

### 16.2 Aşama Latency Toplama Kuralı (Örnek Hesap)

Toplam latency = **aktif** aşamaların `getLatency()` değerlerinin toplamıdır; bypass'lı aşama 0 katkı verir:

```text
L_toplam = Σ L(aşama)  — yalnızca bypass OLMAYAN aşamalar

Örnek (kılavuz — gerçek ölçüm §11/1'de yapılır):
  Aşama 3 grafik EQ   : kaskad blok gecikmesi eklemez (faz/grup gecikmesi ölçülür)
  Aşama 5 dynamics    : limiter lookahead 1–5 ms  → L = 1.000–5.000 ms
  Aşama 6 efekt       : wet yolu FIR taps/2       → ölçülen değer (§6.2)
  Aşama 9 çıkış       : dither/format             → 0
  SR dönüşümü varsa   : ölçülen değer (§11/1)
  ------------------------------------------------------------------
  L_toplam (iç hedef) : < 0.1 ms  (yalnız blok gecikmesi toplamı)
  ADR-006 kapısı      : < 10 ms (ASIO) / < 20 ms (WASAPI) — ölçülür
```

**Kurallar:**
- `getLatency()` **zorunlu** her aşamada (§4 arayüzü); returning sabit değer değil, ölçülmüş/kayıtlı değerdir.
- Bypass anahtarı latency'yi 0'a çeker — aşama sıradan çıkmaz (ADR-062 kuralı).
- Ölçüm yoksa toplam **yazılmaz** → `⚠️ VERIFICATION REQUIRED`.
- ADR-006 kapısında ölçüm tekrarı gerekir (buffer 256→512 değişikliğinde zorunlu, §15.3).

| Latency Kaynağı | Ölçüm Yeri | Not |
|---|---|---|
| Blok gecikmesi (512 örnek) | §6 | 10.67 ms @ 48 kHz — bütçe payı |
| Limiter lookahead | §2 aşama 5 | 1–5 ms |
| FIR taps/2 (efekt yan yolu) | `effects` §6.2 | dry yola yansımaz |
| SR dönüşümü | §11/1 | varsa ölçülür |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode
