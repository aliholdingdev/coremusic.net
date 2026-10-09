---
title: "K002 DRIVERS — Katman Index"
type: index
category: architecture
version: "1.0.0"
status: draft
authority: "SSOT: .ai/architecture/K002-surucu/index.md — K-space band-1 katmanı (ADR-096)"
updated: 2026-10-08
tier: 3
domain: K002-surucu
ssot: true
risk: medium
owner: win-sw
depends-on: [".ai/architecture/00-kspace-anayasa.md"]
---

# K002 DRIVERS — Katman Index

**Künye**

| Alan | Değer |
|---|---|
| Kategori | architecture (katman `index.md`) |
| Durum | draft (FM `status: draft` · katman durumu = PROPOSED — R16.2, 👤 onayı bekliyor) |
| Tarih | 2026-10-08 |
| Yazar | Claude — Band-1 üretim ajanı (F1 KAPI 9) |
| Onay | Vault Steward (Kapı 10) — **BEKLİYOR**, bu dosya vault'a taşınmadı |
| Bant | Bant 1 — `K000-K020 EXISTING FOUNDATION` (21 kart) |
| Uçak | SOFTWARE (EK A §A.0) — **kesişim katmanı**: donanım-yazılım kesişimi yalnız burada (F1 §8.5) |
| Teatral epitet | «KANAT» — yalnız sıfat, K-ID'yi ezmez (R2.3) |
| Sahip (owner) | `win-sw` (Windows Software Engineer — AGENTS.md §4 madde 11) |
| Üretim yeri | staging (`b1-K002-surucu.md`) → hedef `.ai/architecture/K002-surucu/index.md` |

> **Durum etiketleri (F1 §5.4):** `[CURRENT]` · `[TARGET]` · `[PROPOSED]` ·
> `[PLANNED]`/`[DESIGN]` · `[VERIFY REQUIRED]`. Hedef ≠ kanıt ayrı yazılır (H10).
> Bu karttaki sürücü kurallarının bir bölümü **kısmi kanıtlıdır** (F1 §8.10
> ⚠️ notları, OPEN-09) — kısmi iddialar sert kural sayılmaz.

---

#### §1 Genel Bakış

K002, donanım ile yazılım arasındaki **tek geçiş katmanıdır**: ASIO, WASAPI,
ALSA, PipeWire, CoreAudio, I2S, USB Audio, Bluetooth ve DLNA arayüzleri bu
kartta tanımlanır (EK A §A.1 K002 kartı). `.ai/CLAUDE.md` §5 K2 satırındaki
guardrail bu kartın özetidir: **"Donanım-yazılım köprüsü"**. F1 §8.5'e göre
yazılım ve fiziksel uçak arasındaki **kesişim yalnız K002'dedir** (ve K9/K15 API
sınırında); izinli alt katmanları K000 ve K001'dir.

### 1.1 Kapsam Dışı (K002 ne YAPMAZ)

| # | Kapsam dışı | Asıl sahip | Kaynak |
|---|---|---|---|
| 1 | Ses işleme / DSP / EQ zinciri yazılımı | K003 | EK A §A.1 K003 kartı |
| 2 | Donanım devresi/şeması/PCB/BOM | K001 (arayüz) + K016-K020 (fiziksel) | F1 §8.5 |
| 3 | İşletim sistemi süreç/bellek/dosya servisleri | K000 | EK A §A.1 K000 kartı |
| 4 | Ağ protokol tasarımı (HTTP/WebSocket/WebRTC) | K014 | EK A §A.1 K014 kartı (DLNA yüzeyi K002'de taşıyıcıdır) |
| 5 | Medya kitaplığı/kuyruk/indirme | K015 | EK A §A.1 K015 kartı |
| 6 | Kimlik/kripto/denetim kararı | K006 | `.ai/CLAUDE.md` §5 K6 |

---

## §2 Özet Satır — 16 Alan (R4.1 · F1 §10.2 kontratı)

| K-ID | KANONİK_AD | TEATRAL_EPİTET | DOMAIN | RUNTIME | SORUMLULUK | GİRDİ | ÇIKTI | İZİNLİ_BAGIMLILIK | YASAK_BAGIMLILIK | DATA_BOUNDARY | SECURITY_BOUNDARY | FAILURE_MODE | OBSERVABILITY | TEST | KANIT |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| K002 | DRIVERS | «KANAT» | DRIVER | desktop · embedded · server · edge | ASIO · WASAPI · ALSA · PipeWire · CoreAudio · I2S · USB Audio · Bluetooth · DLNA (EK A, 9 madde) | cihaz olayı (bağlan/kopar) · ses akışı isteği (akış/alet/tampon) · sürücü durum sorgusu · protokol çerçevesi | sürücü bağlantısı · örnek tamponu · cihaz listesi · durum/hata kodu · olay bildirimi | K000 · K001 (EK A: izinli=K000-K001) + port/adapter | üst katmana (K003-K020) doğrudan erişim · geri çağrı (H20) · veri paylaşımı (H19) · K001'in iç devre/doğrudan donanım erişimi | çekirdek veri sınırı (EK A) — sürücü katmanı veri saklamaz, DB paylaşımı yok | security=ORTA (EK A) · cihaz-yazılım sınırı yalnız K002'de geçer (F1 SEC16) · imzalı sürücü/firmware (SEC15) | fail-over (EK A) · USB çıkarsa WASAPI fallback (ADR-017) · sürücü bulunamazsa alternatif öncelik zinciri | sürücü bağlantı olayı · gecikme/tampon ölçümü · hata kodu sayımı [PLANNED — K012 ile] | öncelik zinciri (ASIO → WASAPI) · fallback senaryosu · cihaz çıkar/tak [PLANNED] | `.ai/architecture/00-kspace-anayasa.md` satır 79-82 · `.ai/CLAUDE.md` §5 K2 satırı (119) + §13 Tier tablosu · ADR-017 · ADR-019 · ADR-037 · EK B #4 · #5 · #10 · #11 · #35 · F1 §8.10 · repo kod kanıtı YOK → ⚠️ |

### 2.1 Alan bazlı değer gerekçesi (R4.1 kontratının açılımı)

| # | Alan | Bu karttaki değer | Gerekçe (kaynak) |
|---|---|---|---|
| 1 | K-ID | `K002` | EK A §A.0 (R2.3) |
| 2 | KANONİK_AD | `DRIVERS` | EK A §A.0 |
| 3 | TEATRAL_EPİTET | «KANAT» | EK A §A.0 |
| 4 | DOMAIN | `DRIVER` | katmanın konusu sürücü/protokol köprüsüdür |
| 5 | RUNTIME | desktop · embedded · server · edge | tier/deployment modları (§13/§14) |
| 6 | SORUMLULUK | 9 madde | EK A §A.1 K002 — birebir |
| 7 | GİRDİ | cihaz olayı · akış isteği · durum sorgusu · protokol çerçevesi | Sorumluluk maddelerinden türetildi |
| 8 | ÇIKTI | bağlantı · tampon · cihaz listesi · durum/hata · olay | aynı türetme |
| 9 | İZİNLİ_BAGIMLILIK | K000 · K001 | EK A satır 81 "izinli=K000-K001 (alt katmanlar)" |
| 10 | YASAK_BAGIMLILIK | K003-K020 doğrudan · H20 · H19 | EK A satır 81 "yasak=üst katmana doğrudan erişim" |
| 11 | DATA_BOUNDARY | çekirdek veri sınırı | EK A satır 81 |
| 12 | SECURITY_BOUNDARY | ORTA · SEC15/SEC16 | EK A satır 81 + F1 §9.3 |
| 13 | FAILURE_MODE | fail-over (WASAPI fallback) | EK A satır 81 + ADR-017 (`.ai/CLAUDE.md` §22) |
| 14 | OBSERVABILITY | bağlantı olayı · gecikme/tampon · hata kodu | tasarım [PLANNED], owner K012 |
| 15 | TEST | öncelik zinciri · fallback · çıkar/tak | tasarım; kapsam hedefi §17 |
| 16 | KANIT | anayasa + ADR + EK B (5 kaynak) + `⚠️` (repo) | R9.5 3'lü format |

---

## §3 Tam Kimlik Kartı — EK C (20 alan · R4.2/R4.3)

```yaml
K-ID:               K002
KANONİK_AD:         DRIVERS
TEATRAL_EPİTET:     «KANAT»
DOMAIN:             DRIVER
SUBDOMAIN:          audio-drivers                     # ASIO · WASAPI · ALSA · PipeWire · CoreAudio
BOUNDED_CONTEXT:    device-connectivity               # I2S · USB Audio · Bluetooth · DLNA
RUNTIME:            desktop · embedded · server · edge
SORUMLULUK:         ASIO · WASAPI · ALSA · PipeWire · CoreAudio · I2S · USB Audio ·
                    Bluetooth · DLNA
GIRDI:              cihaz olayı (bağlan/kopar) · ses akışı isteği (akış/alet/tampon
                    boyutu) · sürücü durum sorgusu · protokol çerçevesi (USB/BT/DLNA)
CIKTI:              sürücü bağlantısı · örnek tamponu · cihaz listesi · durum/hata
                    kodu · olay bildirimi (bağlantı/kopma)
IZINLI_BAGIMLILIK:  [K000, K001]                       # EK A: izinli=K000-K001
YASAK_BAGIMLILIK:   [K003..K020 doğrudan erişim, geriye çağrı H20, veri paylaşımı H19,
                    K001 iç devresine doğrudan erişim (yalnız port/adapter)]
DATA_BOUNDARY:      çekirdek veri sınırı — sürücü katmanı kalıcı veri saklamaz;
                    tablo/dosya paylaşımı YOK (YARGI 2 · H19)
SECURITY_BOUNDARY:  security=ORTA (EK A) · cihaz-yazılım sınırı yalnız K002'de geçer
                    (F1 SEC16) · firmware/sürücü imzası doğrulanmadan çalışmaz (SEC15)
FAILURE_MODE:       fail-over (EK A) — USB cihaz çıkarsa WASAPI fallback (ADR-017) ·
                    öncelik zincirinde bir yol yoksa sıradaki yola düşme (F1 §8.10 P01)
OBSERVABILITY:      sürücü bağlantı olayı · gecikme/tampon ölçümü · hata kodu
                    sayımı [PLANNED — K012 ile]
TEST:               öncelik zinciri (ASIO → WASAPI) · fallback senaryosu · cihaz
                    çıkar/tak · çoklu platform tier testi [PLANNED]
KANIT:              .ai/architecture/00-kspace-anayasa.md satır 79-82 |
                    .ai/CLAUDE.md §5 K2 satırı (119) + §13 + §19 + §22 + §24 |
                    ADR-017-dsp-hardware-mode · ADR-019-per-os-neva-player ·
                    ADR-037-wirelessconnect-integration |
                    EK B #4 (FlexASIO BACKENDS, er. 2026-10-08, 92) · #5 (MS Learn
                    Low Latency Audio, 90) · #10 (learn.microsoft.com/…/low-latency-audio,
                    96) · #11 (IAudioClient3, 96) · #35 (Steinberg ASIO, 92) |
                    repo: git ls-files sürücü kodu = 0 → ⚠️ VERIFICATION REQUIRED
kanit-tarihi:       2026-10-08
kart-durumu:        PROPOSED (R16.2 — 👤 onayı bekliyor)
```

**Kart kalite kapıları (R4.4):**

| Kapı | Sonuç |
|---|---|
| (a) Her alan dolu | GEÇTİ — 20/20 (18 EK C + `kanit-tarihi` + `kart-durumu`) |
| (b) IZINLI ∩ YASAK = ∅ | GEÇTİ — IZINLI = {K000, K001}; YASAK = {K003..K020, H20, H19} → ∅ |
| (c) KANIT `⚠️` ise research kapısı | KISMİ — repo ayağı `⚠️`; EK B ayağı dolu (5 kaynak) |
| (d) Aynı veri sınırı iki katman paylaşırsa YARGI ihlali | GEÇTİ — çekirdek veri sınırı münhasır |

**Epitet kalite notu:** «KANAT» EK A §A.0'dan alınmıştır (R8.1); epitet kimliği
ezmez (R2.3).

---

## §4 Sorumluluk Derinliği

### 4.1 EK A kartı Sorumluluk maddeleri (00-kspace-anayasa.md §A.1 K002)

| # | Kalem (EK A) | Ne yapar | Durum | Kanıt |
|---|---|---|---|---|
| 1 | ASIO | Düşük gecikmeli profesyonel ses yolu — Steinberg'in spesifikasyonu olduğu birincil kaynakla doğrulandı | TARGET (kısmi) | EK B #35 (Steinberg ASIO dokümanı, 2023-07-20, er. 2026-10-08, güven 92) · `.ai/CLAUDE.md` §24 ASIO SDK 2.3.4 |
| 2 | WASAPI | Windows ses oturum yolu — shared (paylaşımlı) + exclusive yolları | TARGET | `.ai/CLAUDE.md` §3 Terminoloji · EK B #10 · #11 |
| 3 | ALSA | Linux ses altyapısı (Tier 2) | TARGET | `.ai/CLAUDE.md` §13 Tier 2 · §5 K2 satırı |
| 4 | PipeWire | Linux modern ses yolu (Tier 2) | TARGET | §13 Tier 2 · §5 K2 satırı |
| 5 | CoreAudio | macOS ses yolu (Tier 3) | TARGET | §13 Tier 3 · §5 K2 satırı |
| 6 | I2S | Seri ses veri yolu — gömülü/DAC bağlantısı (Tier 4) | TARGET | §13 Tier 4 · §5 K19 (50Ω I2S impedans hedefi) |
| 7 | USB Audio | USB ses sınıfı bağlantısı (XMOS XU316 üzerinden) | TARGET | §5 K1 satırı · §24 XMOS XU316 |
| 8 | Bluetooth | Kablosuz ses bağlantısı | TARGET | `.ai/CLAUDE.md` §5 K2 satırı · ADR-037 (WirelessConnect entegrasyonu) |
| 9 | DLNA | Ev ağında medya paylaşım taşıyıcısı | TARGET | §5 K2 satırı · §14 NAS Audio Server modu |

### 4.2 Anayasa §5 K2 satırındaki gerçek kapsam (`.ai/CLAUDE.md` §5, satır 119)

**Satır:** `K2 Sürücü | ASIO, WASAPI, ALSA, PipeWire, CoreAudio, I2S, USB, BT, DLNA | 40 | Donanım-yazılım köprüsü`

| Alt kapsam | Kapsam | Durum | Kanıt |
|---|---|---|---|
| 9 protokol/sürücü | ASIO · WASAPI · ALSA · PipeWire · CoreAudio · I2S · USB · BT · DLNA | TARGET | §5 satır 119 |
| **Hard Guardrail** | "Donanım-yazılım köprüsü" | BAĞLAYICI | §5 satır 119 |
| "40 bileşen" | §5 sayım sütunu | **HEDEF** | H10: hedef ≠ kanıt |
| ASIO SDK sürümü | 2.3.4 | TARGET | §24 Dependencies tablosu |
| Öncelik zinciri | ASIO → WASAPI Exclusive → WASAPI Shared (fallback) | KISMİ KANITLI | F1 §8.10 P01 + OPEN-09 notu |

### 4.3 Bileşen / kalem envanteri (derinlik)

| Kalem | Ne yapar | Durum | Kanıt |
|---|---|---|---|
| Öncelik zinciri yöneticisi | Erişilebilir yolu seçme (ASIO → WASAPI Ex → Shared) | DESIGN | F1 §8.10 P01 (kısmi) |
| WASAPI shared yol | Paylaşımlı karıştırma; en yüksek gecikme | TARGET | EK B #10 (varsayılan 10 ms tampon) |
| WASAPI exclusive yol | Cihazı tek uygulamaya kilitler | DESIGN | F1 §8.10 P03 — "bit-perfect" iddiası **`⚠️`** (OPEN-09) |
| IAudioClient3 istemcisi | Küçük tampon (<10 ms) talebi | TARGET | EK B #11 (GetSharedModeEnginePeriod / InitializeSharedAudioStream) |
| Real-Time Work Queue | Düşük gecikme iş parçacığı sınıfı etiketleme | TARGET | EK B #10 (MS önerisi) |
| ASIO sürücü eşleşmesi | Üreticiden resmi sürücü tercihi | TARGET (kısmi) | EK B #35 (üretici sürücü tercihi destekli) · ASIO4ALL kaçınması **`⚠️`** (kaynakta YOK) |
| ALSA/PipeWire yolu | Linux tier ses yolu | TARGET | §13 Tier 2 |
| CoreAudio yolu | macOS tier ses yolu | TARGET | §13 Tier 3 |
| I2S/USB köprüsü | Gömülü DAC'a seri bağlantı | DESIGN | §5 K1/K19 · `⚠️` ayrıntı |
| Bluetooth profili | Kablosuz ses + cihaz keşfi | DESIGN | ADR-037 · `⚠️` profil listesi yok |
| DLNA taşıyıcı | LAN üzerinde medya keşfi/akış | DESIGN | §14 NAS modu · `⚠️` |
| Cihaz çıkar/tak olayı | Bağlantı olayının yukarı (K003/K008) bildirimi | DESIGN | ADR-017 (USB çıkarma → WASAPI fallback) |
| Fallback kararı | Yol yoksa sıradaki yola düşme (fail-over) | DESIGN | ADR-017 · EK A failure=fail-over |
| Durum/hata kodu | Üst katmana sürücü durumu | DESIGN | `⚠️` |
| Repo implementasyonu | Sürücü katmanı kodu | **YOK** | `git ls-files` WASAPI/ASIO kod eşleşmesi = 0 (2026-10-08) |

**Sayım disiplini:** §4.3'te 15 kalem; §5'in "40 bileşen" hedefiyle karıştırılmaz (H10).

### 4.4 EK A K002 bloğunun birebir alıntısı (kaynak metin)

```text
### K002 - DRIVERS «KANAT» Bant: K000-K020
- Sorumluluk: ASIO · WASAPI · ALSA · PipeWire · CoreAudio · I2S · USB Audio · Bluetooth · DLNA
- Sınır: data=çekirdek veri sınırı · security=ORTA · failure=fail-over · izinli=K000-K001 (alt katmanlar) · yasak=üst katmana doğrudan erişim (yalnız port/adapter)
- Kanıt: kaynak prompt K0–K20 bloğu · .ai/ vault
```

*Kaynak:* `.ai/architecture/00-kspace-anayasa.md` satır 79-82 (In-Place · H4/R7).

### 4.5 EK A Sınır satırının madde madde açılımı

| Sınır maddesi | Değer | Bu karttaki karşılığı | Açıldığı bölüm |
|---|---|---|---|
| `data` | çekirdek veri sınırı | sürücü kalıcı veri saklamaz | §5.3 |
| `security` | ORTA | SEC15 (imza) + SEC16 (yalnız K002 geçiş yapar) | §5.4 |
| `failure` | fail-over | WASAPI fallback + öncelik zinciri düşüşü | §5.5 |
| `izinli` | K000-K001 | kök + donanım arayüzü | §5.1 |
| `yasak` | üst katmana doğrudan erişim (yalnız port/adapter) | K003-K020 + H20 + H19 | §5.2 |
| `Kanıt` (EK A) | kaynak prompt K0–K20 bloğu · .ai/ vault | web ayağı EK B #4/#5/#10/#11/#35 ile dolu | §7 |

### 4.6 Öncelik zinciri ve kanıt durumu (F1 §8.10 — kural bazlı derinlik)

| Kural | Metin (özet) | Kanıt | Durum |
|---|---|---|---|
| P01 | Öncelik: ASIO → WASAPI Exclusive → WASAPI Shared (fallback) | EK B #35 (ASIO = düşük gecikmeli/spesifikasyon) · zincirin TAMAMI cihaza göre değişebilir | KISMİ → "tercih" olarak uygulanır, sert kural değil (OPEN-09) |
| P02 | WASAPI Shared = paylaşımlı yol, en yüksek gecikme; Windows paylaşımlı boru hattı 10 ms tampon | EK B #10 · #4 | VERIFIED (EK B #10, 96) |
| P03 | Exclusive/ASIO "bit-perfect" ve cihazı tek uygulama kilitler | EK B #35'te **geçmiyor**; #4 yalnız backend/gecikme anlatır | ⚠️ VERIFICATION REQUIRED (OPEN-09) |
| P04 | ASIO üreticiden resmi sürücü ister; ASIO4ALL kaçınılır | üretici tercihi #35 ile destekli; ASIO4ALL kaçınması kaynakta YOK | KISMİ → o kısım `⚠️` |
| P05 | <10 ms tampon yalnız IAudioClient3/AudioGraph ile; istemci istemezse Windows 10 ms kullanır | EK B #10, #11 ("varsayılan 10 ms" dokümanda birebir) | VERIFIED |
| P06 | Düşük gecikme iş parçacıkları Real-Time Work Queue ile Audio/Pro Audio sınıfı olarak etiketlenir | EK B #10 | VERIFIED |

> **Kural/kanıt ayrımı (H15 · H10):** P03 ve P04'ün eksik kalan kısımları bu
> kartta sert kural olarak yazılmaz; `⚠️` ile taşınır ve R14 research
> kapısında kapatılır.

### 4.7 Platform tier × sürücü matrisi (`.ai/CLAUDE.md` §13 — kanıtlanmış eşleme)

| Tier | OS | Sürücü/ses yolu | Durum |
|---|---|---|---|
| 1 | Windows (XP-11, Server 2012 R2+) | ASIO, WASAPI | TARGET (§13 Tier 1) |
| 2 | Linux (Ubuntu, Debian, Fedora) | ALSA, PipeWire | TARGET (§13 Tier 2) |
| 3 | macOS (Monterey–Sonoma) | CoreAudio | TARGET (§13 Tier 3) |
| 4 | Raspberry Pi (ARM64) | I2S | TARGET (§13 Tier 4) |
| 5 | ReactOS | Sınırlı | TARGET · sınırlı (§13 Tier 5) |
| Bağlantı protokolleri | tümü | USB · BT · DLNA | TARGET (§5 K2 satırı) |

---

## §5 Bağımlılık & Sınır

### 5.1 İZİNLİ bağımlılıklar (EK A: izinli = K000-K001 · R6.1)

| Hedef | Tür | Aralık / not |
|---|---|---|
| K000 | alt katman | çalışma zamanı servisleri (süreç, dosya, ağ) |
| K001 | alt katman | donanım arayüzü — port/adapter ile |
| K000/K001 port/adapter | port/adapter | sürücü çağrısı yalnız port üzerinden iner |
| Üstten gelen çağrı | gelen | K003 aşağı iner → K002 (ses akışı); K002 geri çağırmaz |

### 5.2 YASAK bağımlılıklar (R6.1 · F1 H19/H20)

| Yasak | Kaynak | Sonuç |
|---|---|---|
| K003-K020'ye doğrudan erişim | EK A satır 81 | Layer Violation → revert + log CRITICAL |
| Geri çağrı (H20) | F1 §5.1 H20 | yalnız port/adapter |
| Doğrudan veri paylaşımı (H19) | F1 §5.1 H19 · YARGI 2 | data boundary ihlali |
| K001 iç devresine doğrudan erişim | F1 §8.5 (kesişim yalnız K002 — ama erişim port/adapter ile) | tasarım kuralı |
| Web/UI → doğrudan donanım | F1 §8.5 | yol K002 üzerinden |
| Sürücü katmanının veri saklaması | YARGI 2 | veri K005'K005'tedir |

**Olay (event) yukarı serbest:** cihaz bağlanma/kopma, yol değişimi (fallback),
hata olayları K003/K008/K012'ye event ile yayılabilir (R6.2); senkron çağrı yukarı yasak.

### 5.3 DATA_BOUNDARY (YARGI 2)

| Konu | Kural |
|---|---|
| Sınır adı | çekirdek veri sınırı (EK A satır 81) |
| K002'in verisi | sürücü durumu, cihaz listesi, tampon yapılandırması (bellek içi) |
| Paylaşılan tablo | YOK — sürücü katmanı DB'ye yazmaz |
| Kalıcılık | yok; kalıcılık K005 (DB/backup) |

### 5.4 SECURITY_BOUNDARY (EK A: security=ORTA)

| Konu | Değer |
|---|---|
| Seviye | ORTA (EK A satır 81) |
| OWASP eşlemesi | A08:2025 Software/Data Integrity (imzalı sürücü/firmware) · A02:2025 Security Misconfiguration (sürücü yapılandırması) |
| Bağlayıcı kural | F1 SEC15: firmware/asset imzalı; doğrulanmadan çalıştırılmaz |
| Bağlayıcı kural | F1 SEC16: **cihaz-yazılım sınırında yalnız K002 geçiş yapar** |
| Bağlayıcı kural | F1 SEC17: telemetri/gizli veri cihazdan çıkarılırken kullanıcı onayı + redaksiyon |
| Devre dışı | K006 bypass'ı yok |

### 5.5 FAILURE_MODE (EK A: fail-over)

| Senaryo | Davranış | Kaynak | Durum |
|---|---|---|---|
| USB cihaz çıkar | WASAPI fallback | `.ai/CLAUDE.md` §22 · ADR-017 | BAĞLAYICI (edge case) |
| ASIO yol yok | sıradaki yola düşme (WASAPI Ex → Shared) | F1 §8.10 P01 | KISMİ kanıtlı (OPEN-09) |
| Exclusive kilit çakışması | paylaşalı yola düşme | F1 §8.10 P02 | DESIGN |
| Sürücü bulunamaz | hata kodu ile üst katmana bildirim | tasarım | PLANNED |
| fail-open | YOK — yalnız ADR ile (F1 SEC14) | — | — |

### 5.6 Observability / Test sınırı

Log/metrik üretim kuralı K012'dedir (R7.1); K002 yalnız olay/hata kodu üretir.
Test stratejisi K013 CI/CD + cihaz-özel senaryolar; §17 hedef kapsam ≥80%.

### 5.7 Port/Adapter deseni (F1 §8.2 — K002'e uygulaması)

| Port tipi | Yön | Örnek | Adapter | Durum |
|---|---|---|---|---|
| Inbound | K003 → K002 | ses akışı/blok portu | K003'ün outbound adapter'ı | DESIGN |
| Inbound | K001 → K002 | cihaz olay portu | K001 adapter'ı | DESIGN |
| Outbound | K002 → K001/K000 | sürücü çağrısı / IO | K001/K000 port'ları | DESIGN |
| Kural | — | adapter değişince üst katman değişmez (F1 §8.2) | — | bağlayıcı |
| Kural | — | K002 DB'ye bağlanamaz (H19) | — | bağlayıcı |

### 5.8 Sınır ihlali denetim ve yaptırım akışı (R6.5/R6.6 · anayasa §5.1)

| Adım | Aksiyon | Kaynak |
|---|---|---|
| 1 | İhlal tespiti (ör. K002 → K005 doğrudan DB erişimi) | R6.1 · §5.2 |
| 2 | Derhal revert | `.ai/CLAUDE.md` §5.1 |
| 3 | `log.md` CRITICAL girişi | anayasa §5.1 |
| 4 | 👤 bilgi | R6.5 |
| 5 | Döngü → `dep-check` exit 1 → ADR | R6.6 |
| 6 | Kardeş ilişki `refers-to` | R6.4 · §8.1 |

---

## §6 Girdi-Çıktı · Runtime · Observability-Test

### 6.1 Girdi

| Girdi | Kaynak | Biçim | Durum |
|---|---|---|---|
| Cihaz olayı (bağlan/kopar) | K001 donanım | olay | DESIGN |
| Ses akışı isteği | K003 | akış/alet/tampon | DESIGN |
| Sürücü durum sorgusu | K008 servis | çağrı | DESIGN |
| Protokol çerçevesi | USB/BT/DLNA ağı | paket/çerçeve | DESIGN |
| Öncelik/tercih yapılandırması | K006/K005 yapılandırması | ayar | DESIGN |

### 6.2 Çıktı

| Çıktı | Tüketen katman | Durum |
|---|---|---|
| Sürücü bağlantısı | K003 | DESIGN |
| Örnek tamponu | K003 (DSP girişi) | DESIGN |
| Cihaz listesi | K008/K010 (kullanıcı arayüzüne) | DESIGN |
| Durum/hata kodu | K012 (gözlem) · K008 | DESIGN |
| Olay bildirimi | K003/K008/K012 (event yukarı serbest) | DESIGN |

### 6.3 Runtime

| Alan | Değer |
|---|---|
| Runtime sınıfları | desktop · embedded · server · edge |
| Uçak | SOFTWARE (kesişim katmanı — F1 §8.5) |
| Gecikme hedefleri | ASIO <10 ms · WASAPI <20 ms (`.ai/CLAUDE.md` §19) [TARGET] |
| Tampon gerçeği | Windows paylaşımlı boru hattı varsayılan 10 ms (EK B #10) [CURRENT — kaynaklı] |
| Deployment modları | Studio (WASAPI/ASIO) · Car (RPi5/I2S) · Home/NAS (DLNA) · DAC Control — §14 |

### 6.4 Observability

| Alan | İçerik | Durum |
|---|---|---|
| Log | hata kodu/olay — üretim kuralı K012 | owner K012 |
| Metric | gecikme/tampon ölçümü · bağlantı süresi | PLANNED (`⚠️` — metrik adı yok) |
| Trace | sürücü çağrı zinciri | PLANNED |
| Health | aktif yol/CIHAZ durumu | DESIGN |
| Alert | K012 üzerinden | PLANNED |

### 6.5 Test

| Test tipi | Kapsam | Kabul kriteri | Durum |
|---|---|---|---|
| Öncelik zinciri | ASIO yokken WASAPI'ye düşme | fallback doğru yolu seçer | PLANNED |
| Cihaz çıkar/tak | USB çıkarma | ADR-017 senaryosu: kesintisiz fallback | PLANNED |
| Tier matrisi | Tier1-5 | tier başına temel akış | PLANNED |
| Gecikme ölçümü | ASIO/WASAPI | <10 ms / <20 ms (§19 hedefi) | PLANNED (hedef ≠ kanıt) |
| Hedef kapsam | §17 ≥80% | ≥80% | hedef |

### 6.6 F1 §8.4 YARGI'larının K002'ye uygulaması

| Yargı | Kural | K002 uygulaması | Durum |
|---|---|---|---|
| YARGI 1 | yalnız alt katman tanınır | K000 + K001 | GEÇERLİ |
| YARGI 2 | DATA BOUNDARY | çekirdek veri sınırı, tablo yok | GEÇERLİ |
| YARGI 3 | SECURITY BOUNDARY | ORTA + SEC15/16/17 | GEÇERLİ |
| YARGI 4 | OBSERVABILITY | olay/hata/gecikme [PLANNED] | TASARIM |
| YARGI 5 | FAILURE_MODE | fail-over (fallback) | GEÇERLİ |
| YARGI 6 | EVENT BOUNDARY | cihaz olayları yukarı serbest | GEÇERLİ |

### 6.7 Bilinen açık maddeler ve riskler (K002'ye özgü)

| # | Açık madde | Etki | Aksiyon |
|---|---|---|---|
| 1 | OPEN-09: P03 bit-perfect + P01 zincirin tam sırası + P04 ASIO4ALL kısmı | kısmi iddialar `⚠️` | R14 research (EK B #35 ile kısmi kapandı) |
| 2 | Repo'da sürücü kodu yok (`git ls-files` WASAPI/ASIO = 0) | tüm maddeler DESIGN/PLANNED | üretim + kod kanıtı |
| 3 | K002'ye özgü EK B turu bu kartla sınırlı değil | web kanıtı genel ses/sürücü konularına dayanıyor | K002'ye özgü sorgu EK B'ye `#NN` |
| 4 | "40 bileşen" hedefi sayım'a bağlı değil | hedef/kanıt karışması | H10 |

### 6.8 Ses/zincir bağlamı — K002'nin komşu adımlarla ilişkisi

| Sıra | Katman | Bu adımda ne olur | Yön |
|---|---|---|---|
| 1 | K005 / K015 | medya kaynağı, kuyruk, dosya | veri |
| 2 | K004 | öneri/analiz çıktıları (opsiyonel) | olay yukarı |
| 3 | K003 | DSP zinciri (EQ/dinamik/reverb) — blok işleme | iş |
| 4 | **K002** | **sürücü yolu seçimi + tampon teslimi (bu katman)** | — |
| 5 | K001 | donanım arayüzü (codec/DAC/amfi) | dijital → analog |
| 6 | K000 | süreç/bellek/ağ servisleri | temel |

>Sıra katman hiyerarşisidir (R6.1); K002 komşu katmanlara geri çağıramaz (H20)
>ve veri paylaşamaz (H19). Olaylar (cihaz kopması/fallback) yukarı serbesttir (R6.2).

---

## §7 Kanıt Kaynakları (R9 — 3'lü kanıt)

| # | Tür | Kaynak (tam yollar) | İçerik | Durum |
|---|---|---|---|---|
| 1 | Anayasa/defter | `.ai/architecture/00-kspace-anayasa.md` satır 79-82 | K002 kartı: Sorumluluk 9 madde · Sınır · Kanıt | GEÇERLİ |
| 2 | Anayasa | `.ai/architecture/00-kspace-anayasa.md` satır 47 (§A.0) | K002 · DRIVERS · «KANAT» · SOFTWARE | GEÇERLİ |
| 3 | Anayasa (vault) | `.ai/CLAUDE.md` §5 K2 satırı (satır 119) | 9 sürücü + 40 bileşen (hedef) + "Donanım-yazılım köprüsü" | GEÇERLİ |
| 4 | Anayasa (vault) | `.ai/CLAUDE.md` §13 (Tier 1-5 ses yolları) · §19 (ASIO <10ms, WASAPI <20ms) · §22 (USB çıkarma → WASAPI fallback) · §24 (ASIO SDK 2.3.4) | tier↔sürücü eşlemesi · gecikme hedefi · edge case · sürüm | GEÇERLİ |
| 5 | ADR (`ls .ai/.decisions/accepted/`) | `ADR-017-dsp-hardware-mode.md` | USB cihaz çıkarma → WASAPI fallback (`.ai/CLAUDE.md` §22) | GEÇERLİ |
| 6 | ADR (`ls`) | `ADR-019-per-os-neva-player.md` | Per-OS NEVA player — K002/K003 sınırı | GEÇERLİ |
| 7 | ADR (`ls`) | `ADR-037-wirelessconnect-integration.md` | WirelessConnect (BT/WiFi) entegrasyonu | GEÇERLİ |
| 8 | Web (EK B #4) | `https://github.com/dechamps/FlexASIO` — FlexASIO BACKENDS.md (er. 2026-10-08, güven 92) | WASAPI/ASIO backend karşılaştırması, shared/exclusive gecikme, 10 ms tampon | GEÇERLİ |
| 9 | Web (EK B #5) | `learn.microsoft.com (Low Latency Audio sayfası)` (er. 2026-10-08, güven 90) | IAudioClient3 · Real-Time Work Queue | GEÇERLİ · URL tam yolu EK B #10'da |
| 10 | Web (EK B #10) | `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio` (er. 2026-10-08, güven 96) | varsayılan 10 ms tampon · IAudioClient3 · AudioGraph · Real-Time Work Queue | GEÇERLİ |
| 11 | Web (EK B #11) | `https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nn-audioclient-iaudioclient3` (er. 2026-10-08, güven 96) | GetSharedModeEnginePeriod · InitializeSharedAudioStream | GEÇERLİ |
| 12 | Web (EK B #35) | `https://download.steinberg.net/…/asio_driver_c.html` (2023-07-20, er. 2026-10-08, güven 92) | ASIO = Steinberg spesifikasyonu; belirli sürücü yoksa built-in ASIO | GEÇERLİ · bit-perfect/zincir sırası bu kaynakta YOK → `⚠️` |
| 13 | Spec (arşiv) | `.ai/prompts/2026-10-08-master-prompt-v2.2.0-f1.md` §8.10 (satır 741-770) | P01-P06 sürücü kural metinleri + ⚠️ notları (OPEN-09) | GEÇERLİ |
| 14 | Spec (arşiv) | `.ai/prompts/2026-10-08-master-prompt-v2.2.0-f1.md` §8.5 (satır 658-665) | kesişim yalnız K2 | GEÇERLİ |
| 15 | Kural | `.ai/architecture/rules.md` R2/R3/R4/R6/R9 | isimlendirme · iskelet · format · yön · kanıt | GEÇERLİ |
| 16 | Repo (grep) | 2026-10-08: `git ls-files` içi WASAPI/ASIO/driver kod eşleşmesi = 0 | sürücü katmanı implementasyonu YOK | `⚠️` |
| 17 | ADR (kurucu) | `.ai/.decisions/accepted/ADR-096-kspace-5000-boundary-model.md` §2.2 (satır 117-133) | uçak ayrımı + dizin deseni | GEÇERLİ |

**3'lü kanıt dengesi (R9.2):** repo 0/3 · ADR 4 · web 5 → web+ADR dolu,
repo `⚠️` → **2/3 → `⚠️ VERIFICATION REQUIRED` yalnız implementasyon iddiaları için**.

### 7.1 Kanıt dengesi özeti (R9.2/R9.3)

| Ayağı | Durum | Sayı | Sonuç |
|---|---|---|---|
| Repo (dosya yolu + satır) | YOK | 0 | kod iddiaları PLANNED/DESIGN |
| ADR (`ls` ile görülen adlar) | VAR | 3 (+ ADR-096) | davranış kararları |
| Web (URL + tarih) | VAR | 5 (#4 · #5 · #10 · #11 · #35) | sürücü kural kanıtı (kısmi notlarla) |
| Anayasa/kural/spec | VAR | 5 kayıt | kartın taşıyıcısı |
| `kanit-tarihi` | 2026-10-08 | — | R9.3 dolu |
| `STALE` (R11) | gerekmedi | — | tümü 2026-10-08 |

### 7.2 R14 research boşluğu (K002'ye özgü — EK B'ye taşınacak)

| # | Açık soru | İlgili alan | Beklenen kaynak tipi |
|---|---|---|---|
| 1 | ASIO bit-perfect / zincir sırası | §4.6 P03 | birincil kaynak (OPEN-09) |
| 2 | ASIO4ALL kaçınması gerekçesi | §4.6 P04 | birincil kaynak |
| 3 | PipeWire/CoreAudio gecikme gerçeği | §4.3 | üretici/dokümantasyon |
| 4 | BT profili (A2DP/LC3 vb.) listesi | §4.3 | tasarım kararı + kaynak |
| 5 | DLNA profili/uçları | §4.3 | tasarım kararı + kaynak |
| 6 | Sürücü katmanı implementasyon yolu | §4.3 | repo kanıtı (kod yok) |

> Cevap bulunana kadar maddeler `⚠️ VERIFICATION REQUIRED` / `PLANNED` kalır (H15).

---

## §8 İlişki & Değişiklik

### 8.1 Kardeş ve bant ilişkileri (düz metin K-ID — wiki-link YASAK)

| Yön | K-ID | İlişki |
|---|---|---|
| Alt katmanlar | K000 · K001 | tek izinli `depends-on` |
| Üst komşu (doğrudan) | K003 | ses akışı port/adapter ile iner |
| Band-1 kardeşler | K004 · K005 · K006 · K007 · K008 · K009 · K010 · K011 · K012 · K013 · K014 · K015 · K016 · K017 · K018 · K019 · K020 | `refers-to` (R6.4) — `depends-on` DEĞİL |
| Kesişim partneri | K001 | donanım arayüzü (F1 §8.5) |
| Kesişim partneri | K009 · K015 | API sınırı kesişimi (F1 §8.5) |

### 8.2 İzinli wiki-linkler (yalnız mevcut kontrol-plane dosyaları)

- [[architecture/00-kspace-anayasa]] — EK A (K002 kartı kaynağı)
- [[architecture/rules]] — R2/R3/R4/R6/R9
- [[architecture/00-master-index]] — giriş navigasyonu

>Kardeş K-ID'lere wiki-link **yasaktır** (hedefler henüz yok; link-check ihlali).

### 8.3 Geçmiş

| Tarih | Değişiklik | Yapan |
|---|---|---|
| 2026-10-08 | İlk üretim — band-1 K002 `index.md` (staging) | Vault Steward (üretim ajanı) |

### 8.4 Dosya yerleşim planı (ADR-096 §2.2 dizin deseni · R3 iskelet)

| Dosya | Rol | Zorunluluk |
|---|---|---|
| `.ai/architecture/K002-surucu/index.md` | bu dosyanın vault'taki hedefi | zorunlu (R3.1) |
| `.ai/architecture/K002-surucu/*.md` (derinleşme) | yalnız gerçek karmaşıklıkta (ör. öncelik zinciri + fallback modülü) | koşullu (R3.1 · F2 §42) |
| `b1-K002-surucu.md` (staging) | üretim kopyası — vault'a taşınmadı | bu görevin çıktısı |
| Dizin adı | `K002-surucu` (R2.2) | değişmez (R2.4) |
| Tek dev md | yasak (R3.2) | bağlayıcı |
| min-500 | üretim dosyası ≥500 satır; şişirme yasak (R3.3 · H1) | bağlayıcı |

---

**Authority:** SSOT hedefi `.ai/architecture/K002-surucu/index.md` — bu kopya şimdilik **staging**'dedir.
**Last Updated:** 2026-10-08
**Mode:** Red Team · Human Mode · Truth Mode
