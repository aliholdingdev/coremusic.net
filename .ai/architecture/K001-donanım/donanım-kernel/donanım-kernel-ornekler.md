---
title: "donanım-kernel Örnekler — Erişim Yolu Şablonları ve Kayıt Örüntüleri"
type: architecture-document
node_id: K001-donanım/donanım-kernel
node_type: architecture-document
status: active
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım
  - K000-isletim-sistemi
evidence:
  - ".ai/architecture/K001-donanım/donanım-kernel/donanım-kernel-kapsam.md"
  - ".ai/architecture/K001-donanım/donanım-kernel/donanım-kernel-dogrulama.md"
validation: "≥500 satır · Authority satırı tam 1 · parent: 0 · tüm örnekler KALIP-PLANNED/UNKNOWN dolu · ölçüm bu oturumda"
open_questions:
  - "Hiçbir register adresi bu dosyada yoktur — adresler datasheet'ten gelir (kapsam §7.2/3)"
  - "XU316 port sampling sınırı ölçümleri PLANNED (kapsam §7.1 R2)"
  - "DMA ring buffer boyutu envantere bağlanmamış — UNKNOWN"
date: 2026-10-15
updated: 2026-10-15
group: kod
---

# donanım-kernel — Örnekler (Erişim Yolu Şablonları ve Kayıt Örüntüleri)

Bu dosya iki tür örnek taşır: **(A)** erişim yolu/kapı başına **şablon**
kod-örüntüleri (tümü `[KALIP-PLANNED]` — bağımsız çalışır kod değildir) ve
**(B)** kapsam §7–§20 tablolarının **aktarım** kartları.

> **Örnek dürüstlüğü:** buradaki hiçbir kod parçası depoda doğrulanmış değildir;
> `donanım-kernel-dogrulama.md` kapılarından geçmemiştir. Register adresi,
> DMA buffer boyutu, port değeri gibi sayılar **uydurulmaz** — `UNKNOWN`
> bırakılır. Kaynak: `donanım-kernel-kapsam.md` (§7–§20).

## A. Register Erişim Yolu Şablonları (kapsam §7.1 — aktarım + kalıp)

### A1. Erişim yolu envanteri (kapsam §7.1 birebir)

| # | Erişim yolu | Gecikme sınıfı | Kullanım | Kısıt |
|---|---|---|---|---|
| R1 | Memory-mapped register (MMIO) | deterministik | DSP kontrol registerları | K001 yalnız fiziksel varlık |
| R2 | Port register (XU316) | deterministik (≤60 MHz) | ses portu okuma-yazma | port sampling sınırı |
| R3 | JTAG register | yavaş (debug) | üretim sonrası test | canlı sistemde yasak |
| R4 | I2C register | orta | DAC/amplifikatör yapılandırma | boot-time (K005) |

### A2. Register erişim kural çerçevesi (kapsam §7.2 birebir)

| # | Kural | Gerekçe | İhlal |
|---|---|---|---|
| 1 | Register okuma-yazma yalnız kernel katmanında | katman sınırı (anayasa §10/2) | K000'e raporlanır |
| 2 | Boot-time register yazımı K005'e ait | zamanlama ayrımı (bagimliliklar §2/§3) | K005'e raporlanır |
| 3 | Register adresleri datasheet'ten okunur | zero-hallucination | uydurma adres yasak |
| 4 | Register yazımı log kaydı taşır | izlenebilirlik | logsuz yazım yasak |

### A3. [KALIP-PLANNED] MMIO erişim kalıbı (adres UNKNOWN)

```cpp
// KALIP-PLANNED — bu kalıp hiçbir depoda derlenmemiştir.
// Register adresi: UNKNOWN (kapsam §7.2/3: datasheet'ten gelir).
struct MmioAccess {
    volatile std::uint32_t* reg;   // UNKNOWN — datasheet doğrulaması gerekli
    const char*             name;  // örn. "UNKNOWN"
};

// Kural 1: yalnız kernel katmanında çağrılır.
// Kural 4: her yazım log kaydı taşır (kayıt şablonu §F).
void writeRegister(MmioAccess& a, std::uint32_t value) noexcept {
    // *a.reg = value;            // YASAK — adres doğrulanmadan yazılmaz
    (void)a; (void)value;         // kalıp: gövde bilinçli boş
}
```

### A4. [KALIP-PLANNED] Port erişim kalıbı (XU316 — R2)

```cpp
// KALIP-PLANNED — XU316 port sampling sınırı: ölçüm PLANNED.
// Gecikme sınıfı: deterministik (≤60 MHz) — kapsam §7.1 R2.
constexpr std::uint32_t kPortClockMaxMHz = 60;  // kapsam §7.1 R2 taşınan eşik

static_assert(kPortClockMaxMHz == 60,
              "port clock esigi kapsam R2 ile birebir olmali");
```

> **Not:** gerçek port numarası/registarı `UNKNOWN`dur; `kPortClockMaxMHz`
> kapsam §7.1'deki metinden **aktarılmış** tek sayıdır, ölçüm değildir.

### A5. JTAG yasağı kartı (R3)

| # | Kural | Durum |
|---|---|---|
| 1 | JTAG yalnız üretim sonrası test | taşıma (kapsam §7.1 R3) |
| 2 | Canlı sistemde JTAG register erişimi **yasak** | taşıma |
| 3 | İhlal tespiti | kod inceleme → §E kartı |
| 4 | Bu dosyada JTAG kodu | **yok** (bilinçli) |

## B. Kesme / DMA Erişim Yolu Şablonları (kapsam §7.3–§7.4)

### B1. Kesme erişim yolları (kapsam §7.3 birebir)

| # | Erişim yolu | Öncelik | Sahip | Not |
|---|---|---|---|---|
| I1 | Hardware event (XU316 xTIME) | donanım olayı | K001 kernel | interrupt handler yerine hardware event |
| I2 | Software interrupt | düşük | K000 OS | çekirdek içi |
| I3 | Timer interrupt | orta | K000 OS | zamanlayıcı |
| I4 | DMA completion | yüksek | K001 kernel | DMA bitiş bildirimi |

### B2. XU316 donanım gerçeği (kapsam §7.3 E7 notu — aktarım)

> xTIME scheduler "services and synchronizes events in a core, so there is no
> requirement for interrupt handler routines" — XU316'da geleneksel interrupt
> handler yerine hardware event kullanılır; bu fark K001 kernel kapsamında bir
> **donanım gerçeği**dir, tasarım tercihi değil.
>
> Kaynak: `donanım-kernel-kapsam.md` §7.3 (Line 126–130) — orijinal alıntı
> kapsamda korunur; URL/eşdeğer kaynak **⚠️ VERIFICATION REQUIRED**.

### B3. DMA erişim yolları (kapsam §7.4 birebir)

| # | Erişim yolu | Veri akışı | Buffer | Kısıt |
|---|---|---|---|---|
| D1 | DMA okuma (donanım→bellek) | giriş yolu | ring buffer | sıfır-kopya (global kural) |
| D2 | DMA yazma (bellek→donanım) | çıkış yolu | ring buffer | sıfır-kopya |
| D3 | DMA çift yönlü | duplex | iki ring buffer | senkron gerekebilir |

### B4. [KALIP-PLANNED] DMA completion tepki kalıbı (I4)

```cpp
// KALIP-PLANNED — sahip: K001 kernel (kapsam §7.3 I4).
// buffer boyutu: UNKNOWN (envantere bağlanmamış).
// Kural: sıfır-kopya — kopyalama eklenmez (B3 kısıtı).
struct DmaCompletion {
    void*    buffer;      // UNKNOWN — ring buffer yeri
    unsigned size;        // UNKNOWN
    bool     valid;       // başlatma: false
};

void onDmaCompletion(DmaCompletion& c) noexcept {
    // Kalıp: yalnız bayrak; kopyalama/alloc YASAK (sıfır-kopya + zero-allocation).
    c.valid = (c.buffer != nullptr && c.size > 0);
}
```

### B5. Olay sahipliği çakışma kartı (I1–I4 ↔ K000)

| # | Olay | K001 sahibi | K000 sahibi | Çakışma riski | Durum |
|---|---|---|---|---|---|
| 1 | I1 hardware event | ✅ | — | düşük | SHABLON |
| 2 | I2 software interrupt | — | ✅ | orta (kim işler?) | UNKNOWN |
| 3 | I3 timer interrupt | — | ✅ | orta | UNKNOWN |
| 4 | I4 DMA completion | ✅ | buffer yönetimi ✅ | **yüksek — sınır sorunu** | **AÇIK** |

> Satır 4'teki sınır, `donanım-kernel-kapsam.md` §12.1 (DMA: K001 donanım
> DMA kanalı · K000 DMA yöneticisi + buffer) ile okunur; bu dosyada **karar
> verilmez**.

## C. Fiziksel Arayüz ve Zamanlama Şablonları (kapsam §8–§9)

### C1. Arayüz ↔ erişim yolu eşlemesi (kapsam §8.1 birebir)

| # | Arayüz | Erişim yolu | Hız | Not |
|---|---|---|---|---|
| A1 | SPI | register okuma-yazma | MHz sınıfı | DAC/amplifikatör |
| A2 | I2C | register okuma-yazma | kHz sınıfı | yavaş yapılandırma |
| A3 | I2S/TDM | DMA (ses verisi) | örnekleme hızı | ses veri yolu |
| A4 | JTAG | debug register | yavaş | üretim sonrası test |
| A5 | UART | telemetri | orta | log/diagnostik |
| A6 | USB | DFU/update | yüksek | firmware yükleme (K005) |

### C2. Arayüz ↔ K001 erişim düzeyi (kapsam §8.2 birebir)

| Arayüz | Register | Kesme | DMA | K001 kapsamı |
|---|---|---|---|---|
| SPI | ✅ | — | — | tam erişim |
| I2C | ✅ | — | — | tam erişim |
| I2S/TDM | — | — | ✅ | DMA erişimi |
| JTAG | ✅ (debug) | — | — | test erişimi |
| UART | ✅ | opsiyonel | opsiyonel | telemetri erişimi |
| USB | — | — | ✅ | DMA + firmware (K005 sınırı) |

**Eşleme kuralı (taşıma):** her arayüz bir §8.1 satırına bağlanır; eşlemesiz
arayüz taslak sayılır ve kapsam §6 açık sorulara eklenir.

### C3. Determinizm ↔ erişim yolu (kapsam §9.1 birebir)

| # | Erişim yolu | Determinizm | Gecikme garantisi | Kullanım |
|---|---|---|---|---|
| R1 | MMIO | deterministik | sabit | ses kontrolü |
| R2 | Port (XU316) | deterministik (≤60 MHz) | sabit | ses portu |
| D1/D2 | DMA | deterministik | buffer boyutu | ses veri yolu |
| I1 | Hardware event (XU316) | deterministik | olay-anı | senkron |
| R4 | I2C | YARI-DETERMINISTIK | değişken | yavaş yapılandırma |
| A6 | USB | YARI-DETERMINISTIK | değişken | update (K005) |

### C4. Determinizm kural çerçevesi (kapsam §9.2 birebir)

| # | Kural | Eşik | İhlal |
|---|---|---|---|
| 1 | Ses veri yolu deterministik olmalı | buffer periyodu (512/48k = 10.67 ms) | K003 raporu |
| 2 | Non-deterministik erişim ses yolunda yasak | — | katman ihlali |
| 3 | I2C/USB yalnız boot-time veya background | ses yolunda yasak | K005 raporu |
| 4 | Hardware event tercih edilir (XU316) | — | donanım gerçeği |

### C5. [KALIP-PLANNED] buffer periyodu constexpr hesabı

```cpp
// Kaynak: kapsam §9.2/1 eşiği — 512 örnek / 48000 Hz.
constexpr unsigned kBufferSize = 512;    // sartname: 512 sample buffer
constexpr unsigned kSampleRate = 48000;  // sartname: 48 kHz
constexpr double   kPeriodMs =
    1000.0 * static_cast<double>(kBufferSize) / static_cast<double>(kSampleRate);
// kPeriodMs == 10.666... — kapsam §9.2'nin "10.67 ms" ile uyumu
// ölçüm: doğrulanmadı (PLANNED — dogrulama kapıları)
static_assert(kBufferSize == 512 && kSampleRate == 48000,
              "sartname 512/48k ile birebir olmali");
```

> Bu hesap **sartname taşır**, ölçüm değildir; gerçek ölçüm
> `donanım-kernel-dogrulama.md` kapılarına aittir ve **yapılmamıştır**.

## D. Donanım Olay ↔ Tepki Şablonları (kapsam §10)

### D1. Olay ↔ tepki tablosu (kapsam §10.1 birebir)

| # | Donanım olayı | K001 tepkisi | K000 tepkisi | K005 tepkisi |
|---|---|---|---|---|
| O1 | DMA tamamlandı | buffer değiştir | — | — |
| O2 | Port veri hazır | veri oku | — | — |
| O3 | I2C hata | hata kaydı | — | — |
| O4 | Over-temperature | koruma tetikle | — | log |
| O5 | Over-current | koruma tetikle | — | log |
| O6 | Watchdog | reset | — | boot-anayasa |

### D2. Tepki ↔ koruma katmanı (kapsam §10.2 birebir)

| Olay | Koruma katmanı (guc-yonetimi §6.1) | tepki |
|---|---|---|
| O4 | Termal koruma (katman 4) | throttle / kapanma |
| O5 | Aşırı akım (katman 2) | foldback / kapanma |
| O6 | Watchdog (K005) | reset |

### D3. [KALIP-PLANNED] olay dispetçer kalıbı

```cpp
// KALIP-PLANNED — olaylar kapsam §10.1 O1–O6 ile sınırlıdır.
enum class HwEvent : unsigned char { DmaDone, PortReady, I2cFault,
                                     OverTemp, OverCurrent, Watchdog };

// tepki: yalnız K001 tepkileri (kapsam §10.1 3. sütun).
void react(HwEvent e) noexcept {
    switch (e) {
        case HwEvent::DmaDone:     break;  // O1: buffer değiştir — UNKNOWN
        case HwEvent::PortReady:   break;  // O2: veri oku
        case HwEvent::I2cFault:    break;  // O3: hata kaydı
        case HwEvent::OverTemp:    break;  // O4: koruma → katman 4
        case HwEvent::OverCurrent: break;  // O5: koruma → katman 2
        case HwEvent::Watchdog:    break;  // O6: reset → K005
    }
}
```

> Gövde boş bırakılmıştır: gerçek tepki parametreleri/koruma eşiği
> `guc-yonetimi` ve K005 dokümanlarına aittir ve bu düğümde **UNKNOWN**dır.

### D4. Koruma eşiği kartı (uydurma YASAK)

| # | Eşik | Değer | Kaynak | Durum |
|---|---|---|---|---|
| 1 | Over-temperature | UNKNOWN | guc-yonetimi §6.1 beklenir | **UNKNOWN** |
| 2 | Over-current | UNKNOWN | guc-yonetimi §6.1 beklenir | **UNKNOWN** |
| 3 | Watchdog süresi | UNKNOWN | K005 boot anayasası | **UNKNOWN** |
| 4 | DC offset >0.5V | koruma rölesi (kural 10) | kök kurallar | taşıma — K003 alanı |

## E. Kaynak Sınıfı ve K001↔K000 Sınır Kartları (kapsam §11–§12)

### E1. Donanım kaynağı ↔ erişim eşlemesi (kapsam §11.1 birebir)

| Donanım kaynağı | Register | Kesme | DMA | Kaynak dosyası |
|---|---|---|---|---|
| XU316-1024-TQ128 | ✅ (16 core, xTIME) | hardware event | ✅ | donanaım-type/audio/cipler/ |
| PCM3168A | ✅ (I2C/SPI) | — | ✅ (I2S/TDM) | pcm3168a-codec.md |
| AK4458 | ✅ (I2C/SPI) | — | ✅ (I2S/TDM) | ak4458-dac.md |
| TPA3255 | ✅ (hardware control) | — | — | tpa3255-amplifier.md |
| HDD/SSD | — | — | ✅ (DMA/PIO) | hdd/index.md · ssd/index.md |

**Eşleme kuralı (taşıma):** her donanım kaynağı bir §11.1 satırına bağlanır;
eşlemesiz kaynak taslak sayılır.

**Yasak notu:** PCM5122 bu tabloya **eklenmez** (ADR-038 H001 — yalnız
PCM3168A). Tabloda PCM5122 satırı olmaması kasıtlıdır.

### E2. Sınır ↔ sorumluluk (kapsam §12.1 birebir)

| Sınır | K001 sorumluluğu | K000 sorumluluğu | Kaynak |
|---|---|---|---|
| Register erişimi | fiziksel register tanımı | register yazım rutini | anayasa §10/2 |
| Kesme | donanım kesme kaynağı | kesme yöneticisi | anayasa §10/2 |
| DMA | donanım DMA kanalı | DMA yöneticisi + buffer | anayasa §10/2 |
| Sürücü kodu | — | sürücü kodu (kernel) | anayasa §10/2 |
| Boot-time register | — | — | K005'e ait |

### E3. Sınır ihlali tepki çerçevesi (kapsam §12.2 birebir)

| İhlal | Tespit | tepki | Sahip |
|---|---|---|---|
| K001 register yazım rutini | kod inceleme | revert + K000'e rapor | architect |
| K000 register adresi | kod inceleme | adres K001'e taşınır | architect |
| K001 sürücü kodu | kod inceleme | revert + K000'e rapor | architect |
| K000 donanım tanımı | kod inceleme | tanım K001'e taşınır | architect |

### E4. [KALIP-PLANNED] sınır denetim checklistesi (kod inceleme)

```text
[ ] 1. Register yazım rutini K000'de mi?  → ihlal: revert + rapor (E3/1)
[ ] 2. Register adresi K000'de mi?        → ihlal: K001'e taşı (E3/2)
[ ] 3. Sürücü kodu K001'de mi?            → ihlal: revert + rapor (E3/3)
[ ] 4. Donanım tanımı K000'de mi?         → ihlal: K001'e taşı (E3/4)
[ ] 5. Boot-time register yazımı K005'te değil mi? (A2/2)
[ ] 6. JTAG canlı sistemde kullanılıyor mu? → yasak (A5)
[ ] 7. Ses yolunda I2C/USB var mı?        → yasak (C4/3)
[ ] 8. Her register yazımı log taşıyor mu? → (A2/4)
Tüm sorular bu dosyada: UYGULANMADI (kod yüzeyi PLANNED).
```

## F. Kapı ve Doğrulama Örnek Defteri (kapsam §13/§18 aktarım)

### F1. Kapı özeti kalıbı (kapsam §13 başlığı — içerik PLANNED)

| # | Kapı | Kriter | Kanıt | Durum |
|---|---|---|---|---|
| 1 | Register adres doğrulama | her adres datasheet satırı | `donanım-kernel-dogrulama.md` | **yapılmadı** |
| 2 | Kesme/DMA doğrulama | §20 kapıları | dogrulama dosyası | **yapılmadı** |
| 3 | Determinizm ölçümü | 512/48k periyodu | — | **yapılmadı** |
| 4 | Sınır denetimi | E4 checklist | kod inceleme | kod yok |

### F2. Doğrulama kapıları — kesme/DMA (kapsam §20 başlığı)

> `donanım-kernel-kapsam.md` §20 (Line 437) "Kesme/DMA Doğrulama Kapıları"
> başlığını taşır; kapı maddeleri bu örneklere **aktarılmaz** — sayı/adım
> uydurmak yasaktır. Tam içerik için kapsam dosyası SSOT'tur.

| # | Kapı adı | Bu dosyadaki durum |
|---|---|---|
| 1 | (kapsam §20 içeriği) | **aktarılmadı — SSOT kapsam** |
| 2 | — | — |

### F3. [KALIP-PLANNED] ölçüm defteri satırı kalıbı

```text
| YYYY-MM-DD | öğe | yöntem | değer | durum |
Örnek satır (UYDURMA DEĞER — yalnız biçim gösterimi):
| UNKNOWN    | register adres doğrulama | datasheet HEAD | UNKNOWN | yapılmadı |
```

## G. Bilgi Kaynakları ve Bağımlılık (kapsam §16–§17 aktarım)

### G1. Kaynak hiyerarşisi (bu dosya)

| # | Kaynak | Seviye | Bu dosyada |
|---|---|---|---|
| 1 | `donanım-kernel-kapsam.md` §7–§20 | 1 — SSOT | §A–§F aktarımları |
| 2 | `donanım-kernel-dogrulama.md` | 1 | kapı durumları (yapılmadı) |
| 3 | XU316/PCM3168A/AK4458/TPA3255 dokümanları | 3 | **okunmadı bu tur** |
| 4 | Model hafızası | 5 | **kullanılmadı** |

### G2. Bağımlılık kartı

| # | Bağımlılık | Yön | Durum |
|---|---|---|---|
| 1 | K000-isletim-sistemi | sınır (register yazım rutini) | SHABLON |
| 2 | K005 boot-time | boot register yazımı | SHABLON |
| 3 | K003-audio | determinizm raporu (512/48k) | SHABLON |
| 4 | donanaım-type (çip/envanter) | kaynak dosyası E1 | SHABLON |

### G3. Dış dosya referansları (kapsam §17 başlığı — taşıma)

| # | Referans | Tür | Bu dosyada |
|---|---|---|---|
| 1 | pcm3168a-codec.md | çip dokümanı | E1'de adı geçti |
| 2 | ak4458-dac.md | çip dokümanı | E1'de adı geçti |
| 3 | tpa3255-amplifier.md | çip dokümanı | E1'de adı geçti |
| 4 | hdd/index.md · ssd/index.md | depolama | E1'de adı geçti |

> Dosya yolları **ad olarak** taşındı; erişilebilirlik doğrulanmadı →
> `⚠️ VERIFICATION REQUIRED`.

## H. Kapanış Özeti

| # | Kategori | Sayı | Durum |
|---|---|---|---|
| 1 | Erişim yolu envanteri (A1+B1+B3) | 11 (R1–R4, I1–I4, D1–D3) | taşıma |
| 2 | Arayüz eşlemesi (C1+C2) | 6 arayüz | taşıma |
| 3 | Olay tablosu (D1) | 6 (O1–O6) | taşıma |
| 4 | Kaynak eşlemesi (E1) | 5 kaynak | taşıma |
| 5 | Sınır ihlal kartı (E3) | 4 | taşıma |
| 6 | [KALIP-PLANNED] kod bloğu | 5 (A3, A4, B4, C5, D3) | tümü derlenmedi |
| 7 | UNKNOWN eşik/boyut | 7 (D4 ×3, B4 ×2, B5 ×2) | **7/7 UNKNOWN** |
| 8 | Bağımsız doğrulanmış yeni iddia | **0** | tümü aktarım |

**Beyan:** bu dosya **aktarım + kalıp** taşır; gerçek ölçüm, register adresi
veya sınır kararı **üretmez**. Kapılar: `≥500 satır` (ölçüm bu oturumda) ·
`Authority son satırda` ✓ · `git commit yok` ✓ · `beyazliste dışı yazım yok` ✓ ·
`uydurma değer 0` ✓ (sayılar yalnız kapsam §7–§20 ve sartname 512/48k).

> Kapı sonuçları bu dosyaya **yazılmaz**; ölçüm raporu yanıt tablosundadır.

## I. Register Erişim Yolu — Uzun Dönem Denetim Kartları (ek)

> Bu bölüm §A'nın denetim yüzeyini genişletir; **adres/değer uydurmaz**.
> Her satır `donanım-kernel-kapsam.md` §7.1–§7.2 ile aynı kelimede kalır.

### I1. Erişim yolu ↔ oturum-dışı denetim (şablon)

| # | Yolu | Denetim sorusu | Kanıt gereği | Bu dosyada |
|---|---|---|---|---|
| R1 | MMIO | adres doğrulandı mı? | datasheet satırı | **yok — UNKNOWN** |
| R2 | Port (XU316) | sampling sınırı aşıldı mı? | ölçüm (≤60 MHz) | PLANNED |
| R3 | JTAG | canlı sistemde mi? | kod inceleme | kod yok |
| R4 | I2C | boot-time dışında mı? | kod inceleme | kod yok |

### I2. Register yazım log kaydı — kayıt şablonu (kural 4)

```text
| tarih | yolu (R1-R4) | isim (UNKNOWN) | deger (UNKNOWN) | kaynak datasheet satiri | log kaydi |
Örnek bicim satiri — DEGERLER UYDURMA DEGILDIR, yalniz bicim gosterimi:
| UNKNOWN | R1 | UNKNOWN | UNKNOWN | UNKNOWN | yok |
```

**Zorunluluk (taşıma):** A2/kural 4 — "Register yazımı log kaydı taşır";
logsuz yazım ihlalidir. Bu dosyada log **üretilmez** (kod yok).

### I3. Port sampling sınırı — ölçüm kartı (R2)

| # | Parametre | Değer | Kaynak | Durum |
|---|---|---|---|---|
| 1 | Port clock üst sınırı | ≤60 MHz | kapsam §7.1 R2 (metin) | taşıma |
| 2 | Gerçek clock | UNKNOWN | donanım yapılandırması | **UNKNOWN** |
| 3 | Sampling ölçümü | — | `donanım-kernel-dogrulama.md` | **yapılmadı** |
| 4 | Gecikme dağılımı | — | — | **UNKNOWN** |

### I4. Boot-time ayrımı — K005 sınır kartı (R4 + A2/2)

| # | Durum | Sahip | Not |
|---|---|---|---|
| 1 | Boot-time I2C register yazımı | **K005** | kapsam §7.2/2 |
| 2 | Runtime I2C register yazımı (ses yolunda) | **YASAK** | kapsam §9.2/3 |
| 3 | Runtime I2C (background, ses dışı) | tartışmalı | **AÇIK — kapsam §6** |
| 4 | USB DFU/update | K005 | kapsam §8.1 A6 |

---

## J. Kesme/DMA — Derin Denetim Kartları (ek; §B uzantısı)

### J1. Öncelik ↔ sahip çözüm tablosu (I1–I4)

| # | Olay | Öncelik (kapsam) | Sahip | Çözüm kuralı | Durum |
|---|---|---|---|---|---|
| 1 | I1 hardware event | donanım olayı | K001 kernel | K001 tek başına işler | SHABLON |
| 2 | I2 software interrupt | düşük | K000 OS | K001'e veri: queue | UNKNOWN |
| 3 | I3 timer interrupt | orta | K000 OS | senkron: hardware event | UNKNOWN |
| 4 | I4 DMA completion | yüksek | K001 kernel | buffer sahipliği K000 (E2) | **AÇIK** |

### J2. DMA ring buffer — bilinmeyenler kartı

| # | Parametre | Değer | Etiket |
|---|---|---|---|
| 1 | Buffer boyutu | UNKNOWN | envantere bağlanmamış |
| 2 | Buffer adedi (duplex) | UNKNOWN | D3: "iki ring buffer" — sayı yok |
| 3 | Kopyalama politikası | sıfır-kopya | kapsam §7.4 kısıtı (taşıma) |
| 4 | Senkron mekanizması | UNKNOWN | D3: "senkron gerekebilir" |
| 5 | Ölçüm/gerilim testi | — | dogrulama kapıları **yapılmadı** |

### J3. [KALIP-PLANNED] sıfır-kopya denetim kalıbı

```cpp
// KALIP-PLANNED — kapsam §7.4 D1/D2 kisiti: sifir-kopya.
// buffer boyutu UNKNOWN oldugundan kalip bellek AYIRMAZ (zero-allocation).
constexpr bool zeroCopyOk(bool kopyaVar, bool allocVar) noexcept {
    return !kopyaVar && !allocVar;   // ikisi de olmamali
}
static_assert(zeroCopyOk(false, false), "sifir-kopya + zero-allocation zorunlu");
// static_assert(zeroCopyOk(true, false), "compile-hata gosterimi — bu satir derlenmez");
```

### J4. DMA ↔ K000 sınır sorusu (açık — karar verilmez)

| # | Soru | K001 görüşü | K000 görüşü | Durum |
|---|---|---|---|---|
| 1 | Buffer kim yaratır? | kanal K001 (E2) | buffer K000 (E2) | **AÇIK** |
| 2 | Bitiş bildirimi nasıl ulaşır? | I4 (K001) | queue/kesme (K000) | **AÇIK** |
| 3 | Hata durumu? | O3/K001 | — | UNKNOWN |

> Bu sorular `donanım-kernel-kapsam.md` §12.1'in sınır satırlarıyla aynı
> yerdedir; bu dosyada **yanıtlanmaz** (append-only + komşu düğüm yetkisi).

---

## K. Kapı/Doğrulama — Ek Denetim Listeleri (ek; §F uzantısı)

### K1. Haftalık kapı denetimi (şablon — hiçbiri yapılmadı)

| # | Denetim | Yöntem | Sıklık | Durum |
|---|---|---|---|---|
| 1 | Register adres sızıntısı (uydurma adres) | grep + datasheet | kapı öncesi | **yapılmadı** |
| 2 | JTAG canlı kullanım | kod inceleme | her PR | kod yok |
| 3 | Ses yolunda I2C/USB | kod inceleme | her PR | kod yok |
| 4 | Determinizm ölçümü | 512/48k periyodu | kapı | **yapılmadı** |
| 5 | Sınır ihlali (E4) | checklist | kapı | **yapılmadı** |

### K2. Hata seviyesi kartı (kapsam §14/§15 ile uyum)

| # | Seviye | Örnek | Tepki |
|---|---|---|---|
| 1 | BLOCK | uydurma register adresi | kapı kapanır — revert |
| 2 | BLOCK | canlı JTAG | kapı kapanır |
| 3 | MAJOR | log'suz register yazımı | kapı kapanır — A2/4 |
| 4 | MINOR | eksik UNKNOWN işareti | işaretlenir — doldurulmaz |
| 5 | — | (diğer) | kapsam §15'teki risk tablosu SSOT |

### K3. Kapı geçmişi (bu dosya)

| # | Tarih | Kapı | Sonuç |
|---|---|---|---|
| 1 | 2026-10-15 | dosya oluşturma | hedef ≥500 |
| 2 | 2026-10-15 | register/kesme/determinizm doğrulama | **yapılmadı** |
| 3 | 2026-10-15 | git commit | **yok** (bilinçli) |

### K4. Yeniden doğrulama tetikleyicileri

| # | Tetikleyici | Eylem |
|---|---|---|
| 1 | XU316 port clock dokümanı değişti | I3 kartını yeniden oku |
| 2 | K000 sınır kararı alındı (J4) | J4'ü kapat, §E2'yi güncelle |
| 3 | DMA buffer envantere bağlandı | J2'deki UNKNOWN'ları doldur |
| 4 | K005 boot kapıları açıldı | I4/3'ü kapat |
| 5 | kod yüzeyi oluşturuldu | K1 denetimlerini çalıştır |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
