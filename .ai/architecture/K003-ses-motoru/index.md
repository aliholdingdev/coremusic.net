---
title: "K003 AUDIO ENGINE — Katman Index"
type: index
category: architecture
version: "1.0.0"
status: draft
authority: "SSOT: .ai/architecture/K003-ses-motoru/index.md — K-space band-1 katmanı (ADR-096)"
updated: 2026-10-08
tier: 3
domain: K003-ses-motoru
ssot: true
risk: medium
owner: embedded
depends-on: [".ai/architecture/00-kspace-anayasa.md"]
---

# K003 AUDIO ENGINE — Katman Index

**Künye**

| Alan | Değer |
|---|---|
| Kategori | architecture (katman `index.md`) |
| Durum | draft (FM `status: draft` · katman durumu = PROPOSED — R16.2, 👤 onayı bekliyor) |
| Tarih | 2026-10-08 |
| Yazar | Claude — Band-1 üretim ajanı (F1 KAPI 9) |
| Onay | Vault Steward (Kapı 10) — **BEKLİYOR**, bu dosya vault'a taşınmadı |
| Bant | Bant 1 — `K000-K020 EXISTING FOUNDATION` (21 kart) |
| Uçak | SOFTWARE (EK A §A.0) |
| Teatral epitet | «ÇEKİRDEK» — yalnız sıfat, K-ID'yi ezmez (R2.3) |
| Sahip (owner) | `embedded` (Embedded Engineer — AGENTS.md §4 madde 6: C++20 · JUCE · ASIO · DSP) |
| Üretim yeri | staging (`b1-K003-ses-motoru.md`) → hedef `.ai/architecture/K003-ses-motoru/index.md` |

> **Durum etiketleri (F1 §5.4):** `[CURRENT]` · `[TARGET]` · `[PROPOSED]` ·
> `[PLANNED]`/`[DESIGN]` · `[VERIFY REQUIRED]`. RT-safe kurallarının bir kısmı
> **ikinci el kaynaklıdır** (EK B #37, güven 70) → o kısımlar `⚠️` ile taşınır
> (OPEN-11 açık).

---

#### §1 Genel Bakış

K003, CoreMusic'in **gerçek zamanlı ses işleme çekirdeğidir**: Neva Engine,
ses blok/pipeline'ı, DSP zinciri, 31-band EQ, filtreler, dinamik işlemciler,
reverb, limiter, crossover ve surround (EK A §A.1 K003 kartı). `.ai/CLAUDE.md`
§5 K3 satırının guardrail'i bu katmanın sert sınırıdır: **"Sıfır gecikme, K2
donanım sürücüsüne sıkı bağımlı"** — yani K003 yalnız K002 üzerinden aşağı iner.
RT-safe kuralları (F1 §8.9 A01-A07) bu kartın bağlayıcı kural metnidir.

### 1.1 Kapsam Dışı (K003 ne YAPMAZ)

| # | Kapsam dışı | Asıl sahip | Kaynak |
|---|---|---|---|
| 1 | Sürücü yolu seçimi / tampon / fallback | K002 | EK A §A.1 K002 kartı |
| 2 | Donanım devresi, DAC/amfi, PCB | K001 + K016-K020 | F1 §8.5 |
| 3 | Medya kitaplığı, kuyruk, indirme, dosya formatı | K015 | EK A §A.1 K015 kartı |
| 4 | Öneri/kişiselleştirme/analiz | K004 | EK A §A.1 K004 kartı |
| 5 | EQ preset'lerinin kalıcı deposu/şeması | K005 (şema: `coremusic_neva`) | `.ai/CLAUDE.md` §18 DB 16 |
| 6 | Log/metrik üretim kuralları | K012 | R7.1 |

---

## §2 Özet Satır — 16 Alan (R4.1 · F1 §10.2 kontratı)

| K-ID | KANONİK_AD | TEATRAL_EPİTET | DOMAIN | RUNTIME | SORUMLULUK | GİRDİ | ÇIKTI | İZİNLİ_BAGIMLILIK | YASAK_BAGIMLILIK | DATA_BOUNDARY | SECURITY_BOUNDARY | FAILURE_MODE | OBSERVABILITY | TEST | KANIT |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| K003 | AUDIO ENGINE | «ÇEKİRDEK» | AUDIO | audio · desktop · embedded · edge | Neva Engine · Audio Pipeline · DSP · 31-Band EQ · Filters · Dynamics · Reverb · Limiter · Crossover · Surround (EK A, 10 madde) | ses bloğu (örnek akışı) · parametre güncellemesi (EQ/gain/efekt) · yönlendirme (routing) · preset okuması | işlenmiş ses bloğu · gecikme bilgisi (latency report) · olay (clip/underflow) | K000 · K001 · K002 (EK A: izinli=K000-K002) + port/adapter | üst katmana (K004-K020) doğrudan erişim · geri çağrı (H20) · veri paylaşımı (H19) · doğrudan donanım/sürücü erişimi (K002 harici) | çekirdek veri sınırı (EK A) — preset/DB'ye yazım K005 üzerinden port ile; blok verisi paylaşılmaz | security=ORTA (EK A) · RT-safe kural ihlali = güvenlik/süreklilik riski (H18) · K006 bypass yok | fail-over (EK A) · blok hatası → bypass/zararsız geçiş; clip → limiter koruması | blok süresi · xrun/underflow sayımı · CPU yükü · clip/NaN sayacı [PLANNED — K012 ile] | RT-safety statik/dinamik test · DSP doğruluk (ölçüm) · gecikme ölçümü · birim test [PLANNED] | `.ai/architecture/00-kspace-anayasa.md` satır 84-87 · `.ai/CLAUDE.md` §5 K3 satırı (118) + §19 · ADR-062 · ADR-025 · ADR-017 · ADR-019 · `.ai/.sql/mysql/coremusic_neva.sql` (197.121 bayt, 2026-10-08) · F1 §8.9 · EK B #37 · repo C++ kodu = 0 → ⚠️ |

### 2.1 Alan bazlı değer gerekçesi (R4.1 kontratının açılımı)

| # | Alan | Bu karttaki değer | Gerekçe (kaynak) |
|---|---|---|---|
| 1 | K-ID | `K003` | EK A §A.0 (R2.3) |
| 2 | KANONİK_AD | `AUDIO ENGINE` | EK A §A.0 |
| 3 | TEATRAL_EPİTET | «ÇEKİRDEK» | EK A §A.0 |
| 4 | DOMAIN | `AUDIO` | katmanın konusu ses işlemedir |
| 5 | RUNTIME | audio · desktop · embedded · edge | EK C enum + §14 modları |
| 6 | SORUMLULUK | 10 madde | EK A §A.1 K003 — birebir |
| 7 | GİRDİ | ses bloğu · parametre · yönlendirme · preset | Sorumluluk maddelerinden türetildi |
| 8 | ÇIKTI | işlenmiş blok · gecikme bilgisi · olay | aynı türetme |
| 9 | İZİNLİ_BAGIMLILIK | K000 · K001 · K002 | EK A satır 86 "izinli=K000-K002" |
| 10 | YASAK_BAGIMLILIK | K004-K020 doğrudan · H20 · H19 | EK A satır 86 "yasak=üst katmana doğrudan erişim" |
| 11 | DATA_BOUNDARY | çekirdek veri sınırı | EK A satır 86 |
| 12 | SECURITY_BOUNDARY | ORTA + H18 (RT-safe) | EK A satır 86 + F1 §5.1 H18 |
| 13 | FAILURE_MODE | fail-over | EK A satır 86 + §5.5 |
| 14 | OBSERVABILITY | blok süresi · xrun · CPU · clip/NaN | tasarım [PLANNED], owner K012 |
| 15 | TEST | RT-safety · doğruluk · gecikme · birim | tasarım; §17 hedef ≥80% (Google Test) |
| 16 | KANIT | anayasa + ADR + şema (disk) + EK B #37 + `⚠️` (kod) | R9.5 3'lü format |

---

## §3 Tam Kimlik Kartı — EK C (20 alan · R4.2/R4.3)

```yaml
K-ID:               K003
KANONİK_AD:         AUDIO ENGINE
TEATRAL_EPİTET:     «ÇEKİRDEK»
DOMAIN:             AUDIO
SUBDOMAIN:          dsp-pipeline                        # blok işleme · filtre · dinamik · reverb
BOUNDED_CONTEXT:    ses-zamanlama                       # zamanlama · gecikme telafisi · surround
RUNTIME:            audio · desktop · embedded · edge
SORUMLULUK:         Neva Engine · Audio Pipeline · DSP · 31-Band EQ · Filters ·
                    Dynamics · Reverb · Limiter · Crossover · Surround
GIRDI:              ses bloğu (örnek akışı) · parametre güncellemesi (EQ/gain/efekt) ·
                    yönlendirme tablosu · preset okuması (K005 üzerinden port ile)
CIKTI:              işlenmiş ses bloğu · gecikme (latency) raporu · olay
                    (clip/underflow/parametre değişimi)
IZINLI_BAGIMLILIK:  [K000, K001, K002]                   # EK A: izinli=K000-K002
YASAK_BAGIMLILIK:   [K004..K020 doğrudan erişim, geriye çağrı H20, veri paylaşımı H19,
                    K002 dışı sürücü/donanım erişimi]
DATA_BOUNDARY:      çekirdek veri sınırı — ses bloğu verisi ve preset tablosu
                    başka katmanla paylaşılmaz; DB yazımı yalnız K005 port'u ile
SECURITY_BOUNDARY:  security=ORTA (EK A) · RT-safe ihlali H18'dir (callback içinde
                    allocate/lock/I-O/log yasak) · yetki kararı üretmez (K006)
FAILURE_MODE:       fail-over (EK A) — blok işleme hatasında zararsız geçiş/bypass ·
                    clip durumunda limiter koruması · xrun'da tampon yönetimi
OBSERVABILITY:      blok süresi · xrun/underflow sayımı · CPU yükü · clip/NaN
                    sayacı [PLANNED — K012 ile]
TEST:               RT-safety statik+dinamik test · DSP doğruluk ölçümü · gecikme
                    ölçümü (§19 hedefi <10 ms ASIO / <20 ms WASAPI) · Google Test
                    birim testleri (§17 ≥80%) [PLANNED]
KANIT:              .ai/architecture/00-kspace-anayasa.md satır 84-87 |
                    .ai/CLAUDE.md §5 K3 satırı (118) + §19 (standartlar) + §18 DB16 |
                    ADR-062-dsp-pipeline-architecture · ADR-025-professional-eq-system ·
                    ADR-017-dsp-hardware-mode · ADR-019-per-os-neva-player |
                    .ai/.sql/mysql/coremusic_neva.sql (disk, 2026-10-08) |
                    web: EK B #37 (realtime callback safety doctrine, 2026-05-23,
                    güven 70 — ikinci el) → kısmen ⚠️ VERIFICATION REQUIRED
kanit-tarihi:       2026-10-08
kart-durumu:        PROPOSED (R16.2 — 👤 onayı bekliyor)
```

**Kart kalite kapıları (R4.4):**

| Kapı | Sonuç |
|---|---|
| (a) Her alan dolu | GEÇTİ — 20/20 (18 EK C + `kanit-tarihi` + `kart-durumu`) |
| (b) IZINLI ∩ YASAK = ∅ | GEÇTİ — IZINLI = {K000, K001, K002}; YASAK = {K004..K020, H20, H19} → ∅ |
| (c) KANIT `⚠️` ise research kapısı | KISMİ — RT-safe ikinci el kaynak (EK B #37) → OPEN-11 açık |
| (d) Aynı veri sınırı iki katman paylaşırsa YARGI ihlali | GEÇTİ — çekirdek veri sınırı münhasır |

**Epitet kalite notu:** «ÇEKİRDEK» EK A §A.0'dan alınmıştır (R8.1); epitet
kimliği ezmez (R2.3).

---

## §4 Sorumluluk Derinliği

### 4.1 EK A kartı Sorumluluk maddeleri (00-kspace-anayasa.md §A.1 K003)

| # | Kalem (EK A) | Ne yapar | Durum | Kanıt |
|---|---|---|---|---|
| 1 | Neva Engine | Çekirdek ses motoru (motor adı) | DESIGN | anayasa satır 85 · `.ai/.decisions/accepted/ADR-019-per-os-neva-player.md` |
| 2 | Audio Pipeline | Blok işleme boru hattı (giriş → işleme → çıkış) | DESIGN | anayasa satır 85 · ADR-062 (DSP pipeline mimarisi) |
| 3 | DSP | Dijital sinyal işleme çekirdeği | DESIGN | anayasa satır 85 · ADR-062 |
| 4 | 31-Band EQ | 31 bantlı eşitleyici | DESIGN | anayasa satır 85 · §5 K3 satırı · ADR-025 (professional EQ sistemi) |
| 5 | Filters | Filtreler (geçirme/geçirme-eme) | DESIGN | anayasa satır 85 · §19 "DSP Efektleri" |
| 6 | Dynamics | Sıkıştırma/genişletme vb. dinamik | DESIGN | anayasa satır 85 · §19 (Compressor) |
| 7 | Reverb | 4 reverb modu | DESIGN | anayasa satır 85 · §5 K3 "4 Reverb" · §19 reverb modları |
| 8 | Limiter | Sınırlayıcı (çıkış koruması) | DESIGN | anayasa satır 85 · §19 (Limiter) |
| 9 | Crossover | Bant bölücü (subwoofer/çok yollu) | DESIGN | anayasa satır 85 · §5 Critical Components (Subwoofer) |
| 10 | Surround | Çok kanallı çıkış (2.0 → 8.1) | DESIGN | anayasa satır 85 · §19 Kanal hedefi · §5 K3 (Dolby Atmos, DTS:X) |

### 4.2 Anayasa §5 K3 satırındaki gerçek kapsam (`.ai/CLAUDE.md` §5, satır 118)

**Satır:** `K3 Ses İşleme Motoru | Neva Engine, DSP Chain (31-band EQ, 4 Reverb), Dolby Atmos, DTS:X | 50 | Sıfır gecikme, K2 donanım sürücüsüne sıkı bağımlı`

| Alt kapsam | Kapsam | Durum | Kanıt |
|---|---|---|---|
| Neva Engine | motor adı | DESIGN | §5 satır 118 · ADR-019 |
| DSP Chain | 31-band EQ + 4 Reverb | DESIGN | §5 satır 118 · ADR-025 |
| Dolby Atmos | uzamsal ses hedefi | TARGET | §5 satır 118 — **lisans/uygulama kanıtı yok → `⚠️`** |
| DTS:X | uzamsal ses hedefi | TARGET | §5 satır 118 — **lisans/uygulama kanıtı yok → `⚠️`** |
| **Hard Guardrail** | "Sıfır gecikme, K2 donanım sürücüsüne sıkı bağımlı" | BAĞLAYICI | §5 satır 118 |
| "50 bileşen" | §5 sayım sütunu | **HEDEF** | H10: hedef ≠ kanıt |

### 4.3 Bileşen / kalem envanteri (derinlik)

| Kalem | Ne yapar | Durum | Kanıt |
|---|---|---|---|
| Blok işleyici | Örnek bloğu üzerinde sıra ile filtre/dinamik uygulama | DESIGN | F1 §8.9 A06 (sample-accurate) |
| 31-band EQ | Bantlı eşitleyici çekirdeği | DESIGN | §5 K3 · ADR-025 |
| Reverb (4 mod) | Geniş Konser · Düğün Salonu · Oda · Stüdyo | DESIGN | §19 (reverb modları) |
| Filtre seti | Geçir/eme bant filtreleri + crossover | DESIGN | §19 · §5 K3 |
| Dinamik (compressor) | Seviye kontrolü | DESIGN | §19 |
| Limiter | Çıkış sınırlayıcı (aşırı seviye koruması) | DESIGN | §19 |
| Parametre güncelleme | Blok sınırında + crossfade yumuşatma | DESIGN | F1 §8.9 A04 |
| NaN/Inf/denormal koruma | Master çıkışta zorunlu "zap" | DESIGN | F1 §8.9 A05 |
| Gecikme telafisi (latency comp.) | Zincir gecikmesini telafi | DESIGN | F1 §8.9 A07 |
| Routing matrix | Girdi/çıkış yönlendirme | DESIGN | `.ai/CLAUDE.md` §18 DB16 (`coremusic_neva`: routing matrix) |
| Preset okuma | EQ/DSP preset'i K005 üzerinden | DESIGN | §18 DB16 (EQ presets) · `.ai/.sql/mysql/coremusic_neva.sql` (disk) |
| Spectrum analysis | Spektrum analizi çıktısı | DESIGN | §18 DB16 (spectrum analysis) |
| Surround (2.0 → 8.1) | Kanal haritalama | DESIGN | §19 Kanal · §5 K3 |
| Xrun yönetimi | Tampon taşması/eksikliği yanıtı | DESIGN | EK B #37 (kural 0-14 bağlamı) · `⚠️` |
| SPSC/atomik iletişim | İş parçacıkları arası kilit-free veri | DESIGN | F1 §8.9 A02 (SPSC FIFO, std::atomic) |
| C++ uygulaması | Motor kodu (C++20/JUCE/ASIO) | **YOK** | `git ls-files '*.cpp' = 0` (2026-10-08) → `⚠️` |

**Sayım disiplini:** §4.3'te 16 kalem; §5'in "50 bileşen" hedefiyle karıştırılmaz (H10).

### 4.4 EK A K003 bloğunun birebir alıntısı (kaynak metin)

```text
### K003 - AUDIO ENGINE «ÇEKİRDEK» Bant: K000-K020
- Sorumluluk: Neva Engine · Audio Pipeline · DSP · 31-Band EQ · Filters · Dynamics · Reverb · Limiter · Crossover · Surround
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=K000-K002 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault
```

*Kaynak:* `.ai/architecture/00-kspace-anayasa.md` satır 84-87 (In-Place · H4/R7).

### 4.5 EK A Sınır satırının madde madde açılımı

| Sınır maddesi | Değer | Bu karttaki karşılığı | Açıldığı bölüm |
|---|---|---|---|
| `data` | çekirdek veri sınırı | ses bloğu + preset referansı paylaşılmaz | §5.3 |
| `security` | ORTA | H18 (RT-safe) + K006 bypass yok | §5.4 |
| `failure` | fail-over | blok hatasında zararsız geçiş · limiter koruması | §5.5 |
| `izinli` | K000-K002 | kök + donanım arayüzü + sürücü | §5.1 |
| `yasak` | üst katmana doğrudan erişim (yalnız port/adapter) | K004-K020 + H20 + H19 | §5.2 |
| `Kanıt` (EK A) | kaynak prompt K0–K20 bloğu · .ai/ vault | web ayağı kısmi (EK B #37) | §7 |

### 4.6 RT-safe kural metni (F1 §8.9 A01-A07 — bu kartın bağlayıcı kuralları)

| Kural | Metin (özet) | Kanıt | Durum |
|---|---|---|---|
| A01 | Ses callback'inde heap allocate · mutex lock · dosya/network I/O · log · exception YASAK | EK B #37 kural 0-13 (ikinci el, güven 70); Timur Doumler makalesinin tam URL'si yazılmadı (OPEN-11) | KURAL UYGULANIR · kaynak `⚠️` kısmi |
| A02 | İletişim: lock-free SPSC FIFO · `std::atomic<T>` (built-in) · immutable data swap · TripleBuffer · SeqLock | EK B #37 kural 12 (atomics/SPSC) | DESIGN · kısmi kanıt |
| A03 | Emekleme (spinlock) yalnız kilit sahibi olmayan tarafta; ses thread'i asla beklemez | F1 §8.9 (kural metni) | DESIGN · `⚠️` |
| A04 | Parametre güncellemesi blok sınırında + crossfade ile yumuşatılır | F1 §8.9 | DESIGN |
| A05 | NaN/Inf/denormal koruması master çıkışta zorunlu | F1 §8.9 | DESIGN |
| A06 | Örnek-uyumlu (sample-accurate) zamanlama; blok sınırı hatası yok | F1 §8.9 | DESIGN |
| A07 | 31-band EQ / filtre / dinamik / reverb / delay / limiter / crossover zinciri topolojik sırayla + gecikme telafisiyle kurulur | F1 §8.9 · §5 K3 | DESIGN |
| + H18 | Callback içinde allocate/lock/I/O/log **YASAK** (sert kural) | F1 §5.1 H18 | BAĞLAYICI |

> **Kaynak notu (R9):** EK B #37 topluluk standardıdır (birinci el değil) →
> güven 70 → kural metni uygulanır, **birincil kaynak açığı `⚠️` olarak kalır**
> (OPEN-11: Doumler makalesi URL'si henüz yazılmadı).

### 4.7 Ses motoru standartları (`.ai/CLAUDE.md` §19 — gerçek kapsam)

| Standart | Değer | Durum |
|---|---|---|
| Sample Format | Float32 (32-bit) | TARGET (§19) |
| Sample Rate | 48 kHz standart | TARGET (§19) |
| Kanal | 2.0 → 8.1 (7.1 surround) | TARGET (§19) |
| Gecikme hedefi | <10 ms (ASIO) · <20 ms (WASAPI) | TARGET (§19) — ölçüm yok → `⚠️` |
| DSP efektleri | EQ · Reverb · Compressor · Limiter | TARGET (§19) |
| Reverb modları | Geniş Konser · Düğün Salonu · Oda · Stüdyo | TARGET (§19) |
| C++ guardrails | zero-allocation · lock-free · noexcept · cache-line alignment (64-byte) | BAĞLAYICI (§19) |

### 4.8 K003 ↔ K005/K015 sınır ayrımı (veri ve medya çakışması önleme)

| İş | K003 (bu kart) | Komşu | Ayırıcı kural |
|---|---|---|---|
| EQ preset'i **okuma** | port üzerinden okur | deposu = K005 (`coremusic_neva`) | H19 — doğrudan DB yok |
| EQ preset'i **saklama/üretme** | yapmaz | K005 (şema) · K010/K011 (arayüz) | YARGI 2 |
| Dosya çözme (FLAC/MP3) | yapmaz | K015 (media) | EK A K015 kapsamı |
| Kuyruk/akış kontrolü | yapmaz | K015 | EK A K015 |
| Sürücü/tampon seçimi | yapmaz | K002 | EK A K002 · §5 K3 guardrail'i |
| Öneri/kişiselleştirme | yapmaz | K004 | EK A K004 |

---

## §5 Bağımlılık & Sınır

### 5.1 İZİNLİ bağımlılıklar (EK A: izinli = K000-K002 · R6.1)

| Hedef | Tür | Aralık / not |
|---|---|---|
| K000 | alt katman | süreç/bellek/zamanlayıcı servisleri |
| K001 | alt katman | donanım arayüzü (port/adapter) |
| K002 | alt katman | sürücü — **guardrail: "K2 donanım sürücüsüne sıkı bağımlı"** (§5 K3) |
| K005 (preset okuma) | port/adapter (dışa) | **tek** veri erişim yolu; senkron çağrı aşağı iner |

### 5.2 YASAK bağımlılıklar (R6.1 · F1 H19/H20)

| Yasak | Kaynak | Sonuç |
|---|---|---|
| K004-K020'ye doğrudan erişim | EK A satır 86 | Layer Violation → revert + log CRITICAL |
| Geri çağrı (H20) | F1 §5.1 H20 | yalnız port/adapter |
| Doğrudan veri paylaşımı (H19) | F1 §5.1 H19 · YARGI 2 | preset/DB doğrudan erişimi yasak |
| K002 dışı sürücü/donanım erişimi | §5 K3 guardrail'i | tek yol K002 |
| Callback içinde allocate/lock/I/O/log | F1 §5.1 H18 | sert kural — ihlal = RT-safety kırılması |
| K006 bypass'ı | §5 K6 | asla |

**Olay (event) yukarı serbest:** clip/xrun/parametre değişimi olayları
K008/K012'ye event ile yayılabilir (R6.2); senkron çağrı yukarı yasaktır.

### 5.3 DATA_BOUNDARY (YARGI 2)

| Konu | Kural |
|---|---|
| Sınır adı | çekirdek veri sınırı (EK A satır 86) |
| K003'ün verisi | blok belleği (ring buffer), parametre durumu, routing tablosu (bellek içi) |
| Paylaşılan tablo | YOK — `coremusic_neva` şemasının sahibi K005'tir; K003 **port üzerinden okur** |
| Paylaşılan dosya | YOK — medya dosyaları K015'indir |
| Kalıcılık | yok (bellek içi); kalıcılık K005 |

### 5.4 SECURITY_BOUNDARY (EK A: security=ORTA)

| Konu | Değer |
|---|---|
| Seviye | ORTA (EK A satır 86) |
| OWASP eşlemesi | A10:2025 Mishandling of Exceptions (callback'de exception yasağı → F1 §9.1/K7 bağlamı) · A02:2025 (motor yapılandırması) |
| RT-safe bağlantısı | H18 ihlali süreklilik/süre riski doğurur — güvenlik kenarı olarak kaydedilir |
| Devre dışı | K006 bypass'ı yok; yetki kararı üretmez |

### 5.5 FAILURE_MODE (EK A: fail-over)

| Senaryo | Davranış | Durum |
|---|---|---|
| Blok işleme hatası | Zararsız geçiş / bypass (ses kesilmez) | DESIGN |
| Clip (aşırı seviye) | Limiter koruması | DESIGN |
| xrun / underflow | Tampon yönetimi + olay bildirimi | DESIGN (`⚠️`) |
| Parametre geçişi | Blok sınırında crossfade (kesinti yok) | DESIGN (F1 §8.9 A04) |
| fail-open | YOK — fail-open yalnız ADR ile (F1 SEC14) | — |

### 5.6 Observability / Test sınırı

Log/metrik üretimi K012'dedir (R7.1); K003 yalnız ölçüm **değerini** üretir
(blok süresi, xrun, CPU, clip). Test stratejisi K013 CI/CD + Google Test (§17).

### 5.7 Port/Adapter deseni (F1 §8.2 — K003'e uygulaması)

| Port tipi | Yön | Örnek | Adapter | Durum |
|---|---|---|---|---|
| Inbound | K015 → K003 | işlenecek ses bloğu portu | K015 outbound adapter'ı | DESIGN |
| Inbound | K005 → K003 | preset okuma portu | K005 read portu | DESIGN |
| Outbound | K003 → K002 | teslim portu (tampon) | K002 inbound adapter'ı | DESIGN |
| Kural | — | adapter değişince K003 DSP çekirdeği değişmez (F1 §8.2) | — | bağlayıcı |
| Kural | — | K003 doğrudan DB'ye bağlanamaz (H19) | — | bağlayıcı |

### 5.8 Sınır ihlali denetim ve yaptırım akışı (R6.5/R6.6 · anayasa §5.1)

| Adım | Aksiyon | Kaynak |
|---|---|---|
| 1 | İhlal tespiti (ör. K003 → K005 doğrudan SELECT) | R6.1 · §5.2 |
| 2 | Derhal revert | `.ai/CLAUDE.md` §5.1 |
| 3 | `log.md` CRITICAL girişi | anayasa §5.1 |
| 4 | 👤 bilgi | R6.5 |
| 5 | Döngü → `dep-check` exit 1 → ADR | R6.6 |
| 6 | Kardeş ilişki `refers-to` (düz metin K-ID) | R6.4 · §8.1 |

---

## §6 Girdi-Çıktı · Runtime · Observability-Test

### 6.1 Girdi

| Girdi | Kaynak | Biçim | Durum |
|---|---|---|---|
| Ses bloğu (örnek akışı) | K015 (medya) | Float32/48 kHz blok | DESIGN (§19) |
| Parametre güncellemesi | K010/K011 (arayüz) | parametre paketi | DESIGN |
| Yönlendirme tablosu | K010/K011 · K005 (kalıcı) | routing matrisi | DESIGN (§18 DB16) |
| Preset okuması | K005 (`coremusic_neva`) | port üzerinden | DESIGN |
| Sürücü tamponu | K002 | tampon işareti | DESIGN |

### 6.2 Çıktı

| Çıktı | Tüketen katman | Durum |
|---|---|---|
| İşlenmiş ses bloğu | K002 (teslim) | DESIGN |
| Gecikme (latency) raporu | K012 · K011 (gösterim) | DESIGN |
| Olay (clip/underflow/parametre) | K008/K012 (event yukarı) | DESIGN |
| Spektrum analizi verisi | K011 (gösterim) · K005 (kalıcılık) | DESIGN (§18 DB16) |

### 6.3 Runtime

| Alan | Değer |
|---|---|
| Runtime sınıfları | audio · desktop · embedded · edge |
| Uçak | SOFTWARE — aşağı iniş yalnız K002 üzerinden (§5 K3 guardrail) |
| Format/rate | Float32 · 48 kHz (§19) [TARGET] |
| Kanal | 2.0 → 8.1 (§19) [TARGET] |
| Gecikme hedefi | <10 ms ASIO · <20 ms WASAPI (§19) [TARGET — ölçüm yok] |
| Deployment modları | Professional Studio (ASIO/WASAPI) · Home · Car — §14 |

### 6.4 Observability

| Alan | İçerik | Durum |
|---|---|---|
| Log | — (kural K012'de; callback'de log yasak — H18) | owner K012 |
| Metric | blok süresi · xrun/underflow · CPU yükü · clip/NaN sayacı | PLANNED (`⚠️`) |
| Trace | blok işleme zinciri | PLANNED |
| Health | motor çalışır durumu | DESIGN |
| Alert | K012 üzerinden | PLANNED |

### 6.5 Test

| Test tipi | Kapsam | Kabul kriteri | Durum |
|---|---|---|---|
| RT-safety statik tarama | allocate/lock/I/O/log çağrıları | callback içinde yasak çağrı yok | PLANNED |
| RT-safety dinamik | çalışma anında tahsis/kilit ölçümü | sıfır ihlal | PLANNED |
| DSP doğruluk | EQ/filtre/dinamik çıkış referansı vs ölçüm | sapma eşiği **(eşik değeri yok → `⚠️`)** | PLANNED |
| Gecikme ölçümü | ASIO/WASAPI yolu | <10 ms / <20 ms (§19 hedefi) | PLANNED (hedef ≠ kanıt) |
| Birim test | C++ modülleri | ≥80% (§17 — Google Test) | PLANNED |

### 6.6 F1 §8.4 YARGI'larının K003'e uygulaması

| Yargı | Kural | K003 uygulaması | Durum |
|---|---|---|---|
| YARGI 1 | yalnız alt katman tanınır | K000 + K001 + K002 | GEÇERLİ |
| YARGI 2 | DATA BOUNDARY | çekirdek veri sınırı; preset yalnız port ile | GEÇERLİ |
| YARGI 3 | SECURITY BOUNDARY | ORTA + H18 | GEÇERLİ |
| YARGI 4 | OBSERVABILITY | blok süresi/xrun/CPU/clip [PLANNED] | TASARIM |
| YARGI 5 | FAILURE_MODE | fail-over (zararsız geçiş · limiter) | GEÇERLİ |
| YARGI 6 | EVENT BOUNDARY | olaylar yukarı serbest | GEÇERLİ |

### 6.7 Bilinen açık maddeler ve riskler (K003'e özgü)

| # | Açık madde | Etki | Aksiyon |
|---|---|---|---|
| 1 | OPEN-11: RT-safe birincil kaynak (Doumler makalesi URL'si) yazılmadı | A01 kaynağını `⚠️` taşır | R14 research + EK B'ye ekle |
| 2 | Repo'da C++ kodu yok (`git ls-files '*.cpp' = 0`) | tüm maddeler DESIGN/PLANNED | motor üretimi + kod kanıtı |
| 3 | Dolby Atmos / DTS:X lisans-uygulama kanıtı yok | §4.2 TARGET `⚠️` | lisans/uygulama araştırması (R14) |
| 4 | Gecikme hedefi ölçülmemiş | hedef ≠ kanıt (H10) | ölçüm (§6.5) |
| 5 | "50 bileşen" hedefi sayım'a bağlı değil | hedef/kanıt karışması | H10 |

---

## §7 Kanıt Kaynakları (R9 — 3'lü kanıt)

| # | Tür | Kaynak (tam yollar) | İçerik | Durum |
|---|---|---|---|---|
| 1 | Anayasa/defter | `.ai/architecture/00-kspace-anayasa.md` satır 84-87 | K003 kartı: Sorumluluk 10 madde · Sınır · Kanıt | GEÇERLİ |
| 2 | Anayasa | `.ai/architecture/00-kspace-anayasa.md` satır 48 (§A.0) | K003 · AUDIO ENGINE · «ÇEKİRDEK» · SOFTWARE | GEÇERLİ |
| 3 | Anayasa (vault) | `.ai/CLAUDE.md` §5 K3 satırı (satır 118) | Neva Engine · DSP Chain (31-band, 4 Reverb) · Atmos/DTS:X · guardrail | GEÇERLİ |
| 4 | Anayasa (vault) | `.ai/CLAUDE.md` §19 (Audio Engine Standards + C++ guardrails) | Float32 · 48 kHz · 2.0→8.1 · gecikme hedefi · efektler · reverb modları | GEÇERLİ |
| 5 | Anayasa (vault) | `.ai/CLAUDE.md` §18 DB16 (`coremusic_neva`) · §24 (JUCE 9, ASIO SDK 2.3.4) | preset/DSP/routing/spectrum şeması · bağımlılıklar | GEÇERLİ |
| 6 | Repo (dosya yolu) | `.ai/.sql/mysql/coremusic_neva.sql` (197.121 bayt; `git ls-files` = 1) | EQ presets · DSP settings · routing matrix · spectrum analysis şeması | GEÇERLİ (şema — kod değil) |
| 7 | ADR (`ls .ai/.decisions/accepted/`) | `ADR-062-dsp-pipeline-architecture.md` | DSP boru hattı mimarisi | GEÇERLİ |
| 8 | ADR (`ls`) | `ADR-025-professional-eq-system.md` | Profesyonel EQ sistemi | GEÇERLİ |
| 9 | ADR (`ls`) | `ADR-017-dsp-hardware-mode.md` · `ADR-019-per-os-neva-player.md` | DSP donanım modu · per-OS NEVA player | GEÇERLİ |
| 10 | Web (EK B #37) | `https://github.com/jebagu/realtime-audio-family-standards/…/realtime-callback-safety-doctrine.md` (2026-05-23, er. 2026-10-08, güven 70 — ikinci el) | kural 0-14: WCET · heap/mutex/dosya/network yasağı · atomics/SPSC · bounded+wait-free | GEÇERLİ · **birincil değil → `⚠️`** |
| 11 | Spec (arşiv) | `.ai/prompts/2026-10-08-master-prompt-v2.2.0-f1.md` §8.9 (satır 715-739) | A01-A07 kural metinleri + ⚠️ notları (OPEN-11) | GEÇERLİ |
| 12 | Spec (arşiv) | `.ai/prompts/2026-10-08-master-prompt-v2.2.0-f1.md` §5.1 H18 (satır 440) | callback içinde allocate/lock/I/O/log yasağı | GEÇERLİ |
| 13 | Kural | `.ai/architecture/rules.md` R2/R3/R4/R6/R9 | isimlendirme · iskelet · format · yön · kanıt | GEÇERLİ |
| 14 | Karar | `.ai/.decisions/accepted/ADR-096-kspace-5000-boundary-model.md` §2.2 | uçak ayrımı + dizin deseni | GEÇERLİ |
| 15 | Repo (grep) | 2026-10-08: `git ls-files '*.cpp' = 0` · JUCE/CMake dosyası = 0 | motor kodu **YOK** | `⚠️` |
| 16 | Web (EK B — K003'e doğrudan) | defterde K003'e özgü ayrı URL **yok** (yalnız #37 ortak) | — | ⚠️ VERIFICATION REQUIRED |

**3'lü kanıt dengesi (R9.2):** repo 1 (şema, kod değil) · ADR 4 · web 1 (ikinci el)
→ **2/3 kısmi → `⚠️ VERIFICATION REQUIRED`**. R14 research kapısında K003'e özgü
birincil RT-safe kaynağı EK B'ye eklenmelidir (OPEN-11).

### 7.1 Kanıt dengesi özeti (R9.2/R9.3)

| Ayağı | Durum | Sayı | Sonuç |
|---|---|---|---|
| Repo (dosya yolu + satır) | KISMİ | 1 (`coremusic_neva.sql`) — kod yok | motor maddeleri DESIGN |
| ADR (`ls` ile görülen adlar) | VAR | 4 (+ ADR-096) | mimari kararlar |
| Web (URL + tarih) | KISMİ | 1 (#37, ikinci el 70) | `⚠️` kalır |
| Anayasa/kural/spec | VAR | 6 kayıt | kartın taşıyıcısı |
| `kanit-tarihi` | 2026-10-08 | — | R9.3 dolu |
| `STALE` (R11) | gerekmedi | — | tümü 2026-10-08 |

### 7.2 R14 research boşluğu (K003'e özgü — EK B'ye taşınacak)

| # | Açık soru | İlgili alan | Beklenen kaynak tipi |
|---|---|---|---|
| 1 | RT-safe birincil kaynak (Timur Doumler makalesinin tam URL'si) | §4.6 A01 | birincil yayın (OPEN-11) |
| 2 | SPSC/atomik/TripleBuffer uygulama referansı | §4.6 A02 | birincil doküman |
| 3 | Dolby Atmos / DTS:X uygulama-lisans durumu | §4.2 | üretici lisans kaynağı |
| 4 | 31-band EQ tasarım referansı (bant aralıkları, Q) | §4.3 | ADR-025 içi + tasarım |
| 5 | Ölçülmüş gecikme değerleri (ASIO <10 ms hedefi) | §6.5 | ölçüm — repo'da YOK |
| 6 | Motor implementasyon yolu (C++ kaynağı) | §4.3 | repo kanıtı (kod yok) |

> Cevap bulunana kadar maddeler `⚠️ VERIFICATION REQUIRED` / `PLANNED` kalır (H15).

---

## §8 İlişki & Değişiklik

### 8.1 Kardeş ve bant ilişkileri (düz metin K-ID — wiki-link YASAK)

| Yön | K-ID | İlişki |
|---|---|---|
| Alt katmanlar | K000 · K001 · K002 | tek izinli `depends-on` |
| Üst komşu (doğrudan) | K004 | analiz/öneri — yalnız port/adapter + event |
| Veri sahibi (port ile) | K005 | preset/routing kalıcılığı |
| Band-1 kardeşler | K006 · K007 · K008 · K009 · K010 · K011 · K012 · K013 · K014 · K015 · K016 · K017 · K018 · K019 · K020 | `refers-to` (R6.4) — `depends-on` DEĞİL |
| En sık etkileşim | K015 (girdi) · K002 (çıkış) | port/adapter + event |

### 8.2 İzinli wiki-linkler (yalnız mevcut kontrol-plane dosyaları)

- [[architecture/00-kspace-anayasa]] — EK A (K003 kartı kaynağı)
- [[architecture/rules]] — R2/R3/R4/R6/R9
- [[architecture/00-master-index]] — giriş navigasyonu

>Kardeş K-ID'lere wiki-link **yasaktır** (hedefler henüz yok; link-check ihlali).

### 8.3 Geçmiş

| Tarih | Değişiklik | Yapan |
|---|---|---|
| 2026-10-08 | İlk üretim — band-1 K003 `index.md` (staging) | Vault Steward (üretim ajanı) |

### 8.4 Dosya yerleşim planı (ADR-096 §2.2 dizin deseni · R3 iskelet)

| Dosya | Rol | Zorunluluk |
|---|---|---|
| `.ai/architecture/K003-ses-motoru/index.md` | bu dosyanın vault'taki hedefi | zorunlu (R3.1) |
| `.ai/architecture/K003-ses-motoru/*.md` (derinleşme) | gerçek karmaşıklık var: DSP zinciri, RT-safety, surround — ayrı dosyalar beklenir | koşullu (R3.1 · F2 §42) |
| `b1-K003-ses-motoru.md` (staging) | üretim kopyası — vault'a taşınmadı | bu görevin çıktısı |
| Dizin adı | `K003-ses-motoru` (R2.2) | değişmez (R2.4) |
| Tek dev md | yasak (R3.2) | bağlayıcı |
| min-500 | üretim dosyası ≥500 satır; şişirme yasak (R3.3 · H1) | bağlayıcı |

---

**Authority:** SSOT hedefi `.ai/architecture/K003-ses-motoru/index.md` — bu kopya şimdilik **staging**'dedir.
**Last Updated:** 2026-10-08
**Mode:** Red Team · Human Mode · Truth Mode
