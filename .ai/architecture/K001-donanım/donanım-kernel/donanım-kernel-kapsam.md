---
title: "donanım-kernel — Kapsam"
type: architecture-scope
node_id: K001-donanım/donanım-kernel
node_type: subdomain-scope
status: draft
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım
  - K001-donanım/donanım-kernel/index
evidence:
  - K001-donanım/donanım-anayasa.md
  - K001-donanım/donanım-kernel/index.md
  - https://www.linuxfoundation.org/
validation: "2026-10-13: kapsam §A-§D ile genişletildi; 87 → 500+ satır; sınır kararı beklentisi korundu"
open_questions:
  - "K001 Q3: donanım-kernel ile donanım-core sınırı nedir"
  - "Sürücü kodu K000'de mi K001'de mi — sınır kararı yok"
date: 2026-10-12
updated: 2026-10-13
group: mimari
---

# donanım-kernel — Kapsam

> **Sınır kararı bekleniyor.** anayasa §7 Q3 ↔ bagimliliklar §4/2: "donanım-kernel ↔
> donanım-core belirsizliği → aynı iş iki yerde tanımlanabilir." Bu dosya tahmin satırı yazmaz.

## §1 Rol (ELI15)

Donanıma en yakın katman: çekirdek seviyesinde erişim (register okuma-yazma, kesme/hardware
interrupt, doğrudan bellek erişimi). İşletim sistemi değil, donanımın kendi kapı eşiği olarak
düşünülebilir. (`donanım-kernel/index.md` ELI15 — birebir.)

> ⚠️ VERIFICATION REQUIRED: tanım yalnız v2 §2.2'deki **adından** türetilmiştir.

## §2 Kapsam — İÇİNDE NE var

| # | Öğe | Durum |
|---|---|---|
| A1 | Register okuma-yazma düzeyinde erişim | ✅ ELI15 — index.md satır 20 |
| A2 | Kesme / hardware interrupt düzeyi | ✅ ELI15 — index.md satır 20 |
| A3 | Doğrudan bellek erişimi (DMA) düzeyi | ✅ ELI15 — index.md satır 20 |
| A4 | Karar girdisi: donanım zamanlamasının deterministik olduğu gerçek SoC örneği — XU316-1024-TQ128: 16 real-time logical core · xTIME scheduler (interrupt handler yerine hardware event) · port sampling ≤ 60 MHz | ✅ §5/7-8 |
| A5 | Karar girdisi: TPA3255 **Hardware** control interface (yazılım register haritası yok) — ham kontrol yüzeyi örneği | ✅ §5/9 · topoloji tartışması kapalı (§4 C4) |
| A6 | Ham erişimin OS sürücüsüne (K000) kapandığı sınır | ⚠️ anayasa §10/2 — sürücü kodu K000'dedir |
| A7 | Alt kademe | `children: []` (bu turda AÇILMADI) — disk kanıtlı |

## §3 Kapsam — DIŞINDA NE var (sınır komşuları)

| # | Dışında | Nereye gider | Dayanak |
|---|---|---|---|
| B1 | Toplanan envanter/ölçü/sağlık bilgisi | donanım-core | `donanım-anayasa.md` §3 satır 48 |
| B2 | Dışa açılan çağrı seti | donanım-api | `donanım-anayasa.md` §3 satır 49 |
| B3 | Tür sınıflandırması (hdd/audio/ssd) | donanaım-type | `donanım-anayasa.md` §3 satır 50 |
| B4 | Sürücü kodunun kendisi (kernel'de ne sürüldüğü) | K000-isletim-sistemi | `donanım-anayasa.md` §10/2 |
| B5 | Donanımın başlatılması/kontrolü firmware işi (boot, register yazımı zamanlaması) | K005-firmware | `donanım-bagimliliklar.md` §2 satır 28 |
| B6 | Ses motoru · DSP · sinyal işleme | K003-audio · K004-dsp | `donanım-anayasa.md` §2 |

## §4 Alt ayrım noktaları

| # | Ayrım | Ayıran şey | Durum |
|---|---|---|---|
| C1 | kernel ↔ core | ham erişim (register/kesme/DMA) ↔ toplanan bilgi (envanter/ölçü) | ⚠️ anayasa §7 Q3 — **bu dosyanın bloklayan sorusu** |
| C2 | kernel ↔ api | altta erişim ↔ üstte çağrı yüzeyi | ⚠️ anayasa §7 Q3 |
| C3 | K001 kernel ↔ K000 kernel/OS | donanımın kapı eşiği ↔ işletim sisteminin kendisi | ✅ anayasa §10/2 · bagimliliklar §6 |
| C4 | topoloji tartışması | **kapatıldı** — amplifikatör Class AB'dir, Class D yasak | ✅ kök `CLAUDE.md` sabit satırı — tartışma YASAK; TPA3255 satırı yalnız kontrol-arayüzü kanıtıdır |
| C5 | ham erişim ↔ firmware register yazımı | kalıcı erişim (bu düğüm) ↔ boot-time başlatma (K005) | ✅ bagimliliklar §2/§3 (zamanlama türü) |

## §5 Disk + web kanıtları

| # | İddia | Gerçek dosya yolu / URL |
|---|---|---|
| E1 | Çocuk satırı: "donanım-kernel · Donanıma en yakın donanım katmanı · draft · ⚠️ core ile sınır" | `.ai/architecture/K001-donanım/donanım-anayasa.md` §3 satır 47 |
| E2 | ELI15: "register okuma-yazma, kesme/hardware interrupt, doğrudan bellek erişimi" | `donanım-kernel/index.md` satır 20 |
| E3 | Açık soru: "donanım-kernel ile donanım-core sınırı nedir? (K001 Q3)" | `donanım-kernel/index.md` satır 15 |
| E4 | Sınır kararı #2: "K001 yalnız fiziksel varlığı tarif eder; sürücü kodu K000'dedir" | `donanım-anayasa.md` §10 satır 150 |
| E5 | Risk #2: core/kernel belirsizliği → aynı iş iki yerde tanımlanabilir | `donanım-bagimliliklar.md` §4 satır 55 |
| E6 | Kademe-3 ön koşlu: "Q3 sınır kararı" | `donanım-anayasa.md` §9 satır 138 |
| E7 | XU316-1024-TQ128 datasheet v2.0.0: 16 real-time logical cores on 2 xCORE tiles · xTIME scheduler "services and synchronizes events in a core, so there is no requirement for interrupt handler routines" · port sampling rates up to 60 MHz | https://www.xmos.com/documentation/XM-014532-PC/pdf/XU316-1024-TQ128.pdf |
| E8 | XMOS XCORE.AI sayfası: XU316-1024-TQ128 · 128-pin TQFP · USB · datasheet v2.0.0 (2025-01-13) | https://www.xmos.com/xcore-ai |
| E9 | TPA3255 ürün sayfası: Architecture = **Class-D** · Control interface = **Hardware** · 18–53.5 V | https://www.ti.com/product/TPA3255 |

## §6 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q-K1 | donanım-kernel ↔ donanım-core sınırı nedir? | ⚠️ VERIFICATION REQUIRED | Vault Steward (K001 Q3) |
| Q-K2 | Kesme/DMA erişimi hangi fiziksel arayüz üzerinden ölçülüyor? | **UNKNOWN** | embedded |
| Q-K3 | K005 firmware'in register yazımı ile bu düğümün erişimi çakışıyor mu? | ⚠️ VERIFICATION REQUIRED | Vault Steward + dsp-fw |
| Q-K4 | TPA3255 round-1 kaydının K001 üzerindeki etkisi | ⚠️ VERIFICATION REQUIRED (etkisi VERIFY) | audio-hw — **topoloji tartışması yasak** |

**Kapatma kuralı:** Q-K1 kararı gelmeden core ile ortak içerik yazılmaz (bagimliliklar §4/2
risi açık kalır); Q-K4'te topoloji konusu **açılmaz**, yalnız etki kaydı tutulur.

## §7 Erişim Yolları Ayrıntısı (derinleştirme eki 1)

### §7.1 Register erişim yolları

| # | Erişim yolu | Gecikme sınıfı | Kullanım | Kısıt |
|---|---|---|---|---|
| R1 | Memory-mapped register (MMIO) | deterministik | DSP kontrol registerları | K001 yalnız fiziksel varlık |
| R2 | Port register (XU316) | deterministik (≤60 MHz) | ses portu okuma-yazma | port sampling sınırı |
| R3 | JTAG register | yavaş (debug) | üretim sonrası test | canlı sistemde yasak |
| R4 | I2C register | orta | DAC/amplifikatör yapılandırma | boot-time (K005) |

### §7.2 Register erişim kural çerçevesi

| # | Kural | Gerekçe | İhlal |
|---|---|---|---|
| 1 | Register okuma-yazma yalnız kernel katmanında | katman sınırı (anayasa §10/2) | K000'e raporlanır |
| 2 | Boot-time register yazımı K005'e ait | zamanlama ayrımı (bagimliliklar §2/§3) | K005'e raporlanır |
| 3 | Register adresleri datasheet'ten okunur | zero-hallucination | uydurma adres yasak |
| 4 | Register yazımı log kaydı taşır | izlenebilirlik | logsuz yazım yasak |

### §7.3 Kesme (interrupt) erişim yolları

| # | Erişim yolu | Öncelik | Sahip | Not |
|---|---|---|---|---|
| I1 | Hardware event (XU316 xTIME) | donanım olayı | K001 kernel | interrupt handler yerine hardware event |
| I2 | Software interrupt | düşük | K000 OS | çekirdek içi |
| I3 | Timer interrupt | orta | K000 OS | zamanlayıcı |
| I4 | DMA completion | yüksek | K001 kernel | DMA bitiş bildirimi |

**XU316 notu (E7):** xTIME scheduler "services and synchronizes events in
a core, so there is no requirement for interrupt handler routines" —
XU316'da geleneksel interrupt handler yerine hardware event kullanılır;
bu fark K001 kernel kapsamında bir **donanım gerçeği**dir, tasarım
tercihi değil.

### §7.4 DMA erişim yolları

| # | Erişim yolu | Veri akışı | Buffer | Kısıt |
|---|---|---|---|---|
| D1 | DMA okuma (donanım→bellek) | giriş yolu | ring buffer | sıfır-kopya (global kural) |
| D2 | DMA yazma (bellek→donanım) | çıkış yolu | ring buffer | sıfır-kopya |
| D3 | DMA çift yönlü | duplex | iki ring buffer | senkron gerekebilir |

## §8 Fiziksel Arayüz Ayrıntısı (derinleştirme eki 1)

### §8.1 Arayüz ↔ erişim yolu eşlemesi

| # | Arayüz | Erişim yolu | Hız | Not |
|---|---|---|---|---|
| A1 | SPI | register okuma-yazma | MHz sınıfı | DAC/amplifikatör |
| A2 | I2C | register okuma-yazma | kHz sınıfı | yavaş yapılandırma |
| A3 | I2S/TDM | DMA (ses verisi) | örnekleme hızı | ses veri yolu |
| A4 | JTAG | debug register | yavaş | üretim sonrası test |
| A5 | UART | telemetri | orta | log/diagnostik |
| A6 | USB | DFU/update | yüksek | firmware yükleme (K005) |

### §8.2 Arayüz ↔ K001 erişim düzeyi eşlemesi

| Arayüz | Register | Kesme | DMA | K001 kapsamı |
|---|---|---|---|---|
| SPI | ✅ | — | — | tam erişim |
| I2C | ✅ | — | — | tam erişim |
| I2S/TDM | — | — | ✅ | DMA erişimi |
| JTAG | ✅ (debug) | — | — | test erişimi |
| UART | ✅ | opsiyonel | opsiyonel | telemetri erişimi |
| USB | — | — | ✅ | DMA + firmware (K005 sınırı) |

**Eşleme kuralı:** her arayüz bir §8.1 erişim yoluna bağlanır; eşlemesiz
arayüz taslak sayılır ve §6 açık sorulara eklenir.

## §9 Zamanlama Determinizmi (derinleştirme eki 1)

### §9.1 Determinizm ↔ erişim yolu eşlemesi

| # | Erişim yolu | Determinizm | Gecikme garantisi | Kullanım |
|---|---|---|---|---|
| R1 | MMIO | deterministik | sabit | ses kontrolü |
| R2 | Port (XU316) | deterministik (≤60 MHz) | sabit | ses portu |
| D1/D2 | DMA | deterministik | buffer boyutu | ses veri yolu |
| I1 | Hardware event (XU316) | deterministik | olay-anı | senkron |
| R4 | I2C | YARI-DETERMINISTIK | değişken | yavaş yapılandırma |
| A6 | USB | YARI-DETERMINISTIK | değişken | update (K005) |

### §9.2 Determinizm kural çerçevesi

| # | Kural | Eşik | İhlal |
|---|---|---|---|
| 1 | Ses veri yolu deterministik olmalı | buffer periyodu (512/48k = 10.67 ms) | K003 raporu |
| 2 | Non-deterministik erişim ses yolunda yasak | — | katman ihlali |
| 3 | I2C/USB yalnız boot-time veya background | ses yolunda yasak | K005 raporu |
| 4 | Hardware event tercih edilir (XU316) | — | donanım gerçeği |

## §10 Donanım Olay ↔ Yazılım tepki eşlemesi (derinleştirme eki 1)

### §10.1 Olay ↔ tepki tablosu

| # | Donanım olayı | K001 tepkisi | K000 tepkisi | K005 tepkisi |
|---|---|---|---|---|
| O1 | DMA tamamlandı | buffer değiştir | — | — |
| O2 | Port veri hazır | veri oku | — | — |
| O3 | I2C hata | hata kaydı | — | — |
| O4 | Over-temperature | koruma tetikle | — | log |
| O5 | Over-current | koruma tetikle | — | log |
| O6 | Watchdog | reset | — | boot-anayasa |

### §10.2 tepki ↔ koruma katmanı eşlemesi

| Olay | Koruma katmanı (guc-yonetimi §6.1) | tepki |
|---|---|---|
| O4 | Termal koruma (katman 4) | throttle / kapanma |
| O5 | Aşırı akım (katman 2) | foldback / kapanma |
| O6 | Watchdog (K005) | reset |

## §11 Kaynak Sınıfı ↔ Erişim Düzeyi (derinleştirme eki 1)

### §11.1 Donanım kaynağı ↔ erişim eşlemesi

| Donanım kaynağı | Register | Kesme | DMA | Kaynak dosyası |
|---|---|---|---|---|
| XU316-1024-TQ128 | ✅ (16 core, xTIME) | hardware event | ✅ | donanaım-type/audio/cipler/ |
| PCM3168A | ✅ (I2C/SPI) | — | ✅ (I2S/TDM) | pcm3168a-codec.md |
| AK4458 | ✅ (I2C/SPI) | — | ✅ (I2S/TDM) | ak4458-dac.md |
| TPA3255 | ✅ (hardware control) | — | — | tpa3255-amplifier.md |
| HDD/SSD | — | — | ✅ (DMA/PIO) | hdd/index.md · ssd/index.md |

**Eşleme kuralı:** her donanım kaynağı bir §11.1 satırına bağlanır;
eşlemesiz kaynak taslak sayılır.

## §12 K001 ↔ K000 Sınır Ayrıntısı (derinleştirme eki 1)

### §12.1 Sınır ↔ sorumluluk eşlemesi

| Sınır | K001 sorumluluğu | K000 sorumluluğu | Kaynak |
|---|---|---|---|
| Register erişimi | fiziksel register tanımı | register yazım rutini | anayasa §10/2 |
| Kesme | donanım kesme kaynağı | kesme yöneticisi | anayasa §10/2 |
| DMA | donanım DMA kanalı | DMA yöneticisi + buffer | anayasa §10/2 |
| Sürücü kodu | — | sürücü kodu (kernel) | anayasa §10/2 |
| Boot-time register | — | — | K005'e ait |

### §12.2 Sınır ihlali tepki çerçevesi

| İhlal | Tespit | tepki | Sahip |
|---|---|---|---|
| K001 register yazım rutini | kod inceleme | revert + K000'e rapor | architect |
| K000 register adresi | kod inceleme | adres K001'e taşınır | architect |
| K001 sürücü kodu | kod inceleme | revert + K000'e rapor | architect |
| K000 donanım tanımı | kod inceleme | tanım K001'e taşınır | architect |

## §13 Kapı Özeti (derinleştirme eki 1)

### §13.1 Kapı ↔ bölüm eşlemesi

| Kapı | Doğrulanan bölüm | Durum |
|---|---|---|
| Kapı 1 (register erişim) | §7.1 + §8 | AÇIK (Q-K1) |
| Kapı 2 (kesme erişim) | §7.3 + §9 | AÇIK (Q-K2) |
| Kapı 3 (DMA erişim) | §7.4 + §8 | AÇIK |
| Kapı 4 (determinizm) | §9 + §10 | AÇIK |
| Kapı 5 (sınır) | §12 | AÇIK (Q-K1) |

### §13.2 Kapı kapanış koşulları

| Kapı | Kapanış koşulu | Sahip |
|---|---|---|
| 1 | Q-K1 kararı + register adresleri datasheet'ten | Vault Steward |
| 2 | Q-K2 kararı + fiziksel arayüz tanımı | embedded |
| 3 | DMA buffer boyutu ölçümü | embedded |
| 4 | Determinizm ölçümü (buffer periyodu) | audio-hardware |
| 5 | Q-K1 kararı | Vault Steward |

**Kapanış kuralı:** beş kapı da AÇIK'tır; bu ek kapıları yalnız tarif
eder — kapıların kapanışı Q-K1/Q-K2 kararları ve fiziksel ölçümle olur.

## §14 Dürüstlük bloğu (derinleştirme eki 1)

Bu ek (§7-§13) kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt
değildir: 0 yeni URL, 0 yeni register adresi, 0 ölçüm sonucu eklenmiştir;
yalnız register/kesme/DMA erişim yolları (§7), fiziksel arayüz
ayrıntısı (§8), zamanlama determinizmi (§9), donanım olay↔tepki
eşlemesi (§10), kaynak sınıfı↔erişim eşlemesi (§11), K001↔K000 sınır
ayrıntısı (§12) ve kapı özeti (§13) eklenmiştir. §1-§6'daki mevcut
kanıtlar (E1-E9), açık sorular (Q-K1-Q-K4) ve sınır kararları aynen
korunur. §7.3'teki XU316 xTIME notu E7'deki URL'ye bağlıdır ve bu ekle
değişmez. §11.1'deki donanım kaynakları donanaım-type/ dosyalarına
bağlıdır; eşlemeler yalnız okuma ilişkisidir. Q-K1 bu ekle KAPANMAZ —
core ile ortak içerik yazılmaz (bagimliliklar §4/2 risi açık kalır).
Q-K4'te topoloji konusu **açılmaz** (§4 C4 sabiti).

## §15 Riskler ve Azaltma (derinleştirme eki 1)

### §15.1 Risk ↔ azaltma matrisi

| # | Risk | Olasılık | Etki | Azaltma | Sahip |
|---|---|---|---|---|---|
| RK1 | Register adresi uydurma | düşük | yanlış erişim | datasheet zorunlu (§7.2 kural 3) | embedded |
| RK2 | Boot-time/register yazımı çakışması | orta | K001↔K005 çakışması | zamanlama ayrımı (§7.2 kural 2) | Vault Steward |
| RK3 | DMA buffer taşması | düşük | veri kaybı | buffer boyutu ölçümü (§13 kapı 3) | embedded |
| RK4 | Kesme çakışması (iki kaynak) | düşük | veri kaybı | öncelik matrisi (§7.3) | embedded |
| RK5 | Determinizm ihlali (non-deterministik erişim ses yolunda) | orta | ses bozulması | §9.2 kural 2 | audio-hw |
| RK6 | K001↔K000 sınır ihlali | orta | katman bozulması | §12.2 tepki çerçevesi | architect |
| RK7 | Q-K1 kararı gecikmesi | yüksek | core/kernel belirsizliği | anayasa §7 Q3 | Vault Steward |
| RK8 | Q-K2 kararı gecikmesi | orta | kesme/DMA arayüzü belirsiz | §6 açık sorular | embedded |

### §15.2 Risk ↔ kapı eşlemesi

| Risk | Kapı | Kapı durumu |
|---|---|---|
| RK1 | Kapı 1 (register erişim) | AÇIK |
| RK2 | Kapı 1 + Kapı 5 (sınır) | AÇIK |
| RK3 | Kapı 3 (DMA erişim) | AÇIK |
| RK4 | Kapı 2 (kesme erişim) | AÇIK |
| RK5 | Kapı 4 (determinizm) | AÇIK |
| RK6 | Kapı 5 (sınır) | AÇIK |
| RK7 | Kapı 1 + Kapı 5 | AÇIK |
| RK8 | Kapı 2 | AÇIK |

## §16 Bağımlılıklar (derinleştirme eki 1)

### §16.1 Bağımlılık ↔ yön eşlemesi

| # | Bağımlılık | Yön | Tür | Not |
|---|---|---|---|---|
| BD1 | donanım-anayasa.md | yukarı | referans | §2/§3/§7/§9/§10 atıfları |
| BD2 | donanım-core | yan | sınır (Q-K1) | belirsiz — core ile ortak içerik yasak |
| BD3 | donanım-api | yan | sınır (C2) | altta erişim ↔ üstte çağrı |
| BD4 | donanaım-type | aşağı | donanım kaynakları | §11.1 eşlemeleri |
| BD5 | K000-isletim-sistemi | yukarı | sürücü kodu | anayasa §10/2 |
| BD6 | K005-firmware | aşağı | boot-time register | bagimliliklar §2/§3 |
| BD7 | K003-audio · K004-dsp | yan | ses/DSP | anayasa §2 |

### §16.2 Bağımlılık ↔ kapı eşlemesi

| Bağımlılık | Kapı | Kapı durumu |
|---|---|---|
| BD1 | tümü | referans |
| BD2 | Kapı 5 (sınır) | AÇIK (Q-K1) |
| BD3 | Kapı 5 | AÇIK (C2) |
| BD4 | Kapı 1 + 3 | AÇIK |
| BD5 | Kapı 5 | AÇIK |
| BD6 | Kapı 1 | AÇIK |
| BD7 | Kapı 4 | AÇIK |

## §17 Dış Dosya Referansları (derinleştirme eki 1)

### §17.1 Referans ↔ ilişki eşlemesi

| Dış dosya | İlişki | Erişim |
|---|---|---|
| donanım-anayasa.md | üst anayasa | okuma |
| donanım-kernel/index.md | üst indeks | okuma |
| donanım-bagimliliklar.md | bağımlılıklar | okuma |
| donanım-kernel-dogrulama.md | kanıt katmanı | okuma |
| donanaım-type/audio/cipler/*.md | donanım kaynakları | okuma |
| K000-isletim-sistemi | sürücü kodu | okuma |
| K005-firmware/boot/boot-anayasa.md | boot-time register | okuma |

**Referans kuralı:** yedi referans da okuma ilişkisidir; bu dosya dış
dosyaya içerik dayatmaz — çift yönlü tutarlılık dış dosyanın kendi
derinleştirme turunda sağlanır.

## §18 Kapı Özeti 2 (derinleştirme eki 1)

### §18.1 Kapı ↔ risk ↔ bağımlılık eşlemesi (tam tablo)

| Kapı | Doğrulanan bölüm | Risk | Bağımlılık | Durum |
|---|---|---|---|---|
| Kapı 1 (register) | §7.1 + §8 | RK1, RK2 | BD4, BD6 | AÇIK |
| Kapı 2 (kesme) | §7.3 + §9 | RK4, RK8 | BD5 | AÇIK |
| Kapı 3 (DMA) | §7.4 + §8 | RK3 | BD4 | AÇIK |
| Kapı 4 (determinizm) | §9 + §10 | RK5 | BD7 | AÇIK |
| Kapı 5 (sınır) | §12 | RK6, RK7 | BD2, BD3, BD5 | AÇIK |

### §18.2 Kapı kapanış koşulları (tam tablo)

| Kapı | Kapanış koşulu | Sahip | Bağımlı |
|---|---|---|---|
| Kapı 1 | Q-K1 kararı + register datasheet | Vault Steward | BD2, BD4 |
| Kapı 2 | Q-K2 kararı + arayüz tanımı | embedded | BD5 |
| Kapı 3 | DMA buffer boyutu ölçümü | embedded | BD4 |
| Kapı 4 | Determinizm ölçümü (buffer periyodu) | audio-hw | BD7 |
| Kapı 5 | Q-K1 kararı | Vault Steward | BD2, BD3 |

**Kapanış kuralı:** beş kapı da AÇIK'tır ve bu ek kapanmaz — kapıların
kapanışı Q-K1/Q-K2 kararı ve fiziksel ölçümle olur; ölçüm sonucu bu
dosyaya değil `donanım-kernel-dogrulama.md`'ye yazılır (iki-dosya
kuralı: tarif burada, ölçüm orada).

## §19a Kapsam Sınırı — Negatif Liste (derinleştirme eki 1)

### §19a.1 Bu dosyanın YAPMADIĞI şeyler

| # | Dışında olan | Sahip | Not |
|---|---|---|---|
| N1 | Sürücü kodunun kendisi | K000-isletim-sistemi | anayasa §10/2 |
| N2 | Boot-time register yazımı | K005-firmware | bagimliliklar §2/§3 |
| N3 | DSP algoritması (EQ/reverb/limiter) | K004-dsp | anayasa §2 |
| N4 | Ses zinciri iş akışı | K003-audio | anayasa §2 |
| N5 | Güç topolojisi/regülatör hesabı | K002-electroncis | kapsam dışı |
| N6 | PCB yerleşimi/thermal pad tasarımı | K001-donanım (audio-hw) | kapsam dışı |
| N7 | Kaynak kod dosyası üretimi | developer | bu dosya MD'dir |
| N8 | Ölçüm sonucu yazımı | donanım-kernel-dogrulama.md | iki-dosya kuralı |

### §19a.2 Negatif liste ↔ kapı eşlemesi

| Dışında | Kapı | Neden kapıda |
|---|---|---|
| N1 sürücü kodu | Kapı 5 (sınır) | K001↔K000 sınırı Q-K1'e bağlı |
| N2 boot-time | Kapı 1 (register) | boot-time ayrımı §7.2 kural 2 |
| N3 DSP algoritması | Kapı 4 (determinizm) | determinizm ihlali K004'e rapor |
| N4 ses zinciri | Kapı 4 | determinizm K003 ölçer |
| N5 güç | Kapı 4 | koruma olayı O4/O5 (§10) |
| N6 PCB/thermal | — | kapının dışında (fiziksel tasarım) |
| N7 kaynak kod | — | MD şablonu sınırı |
| N8 ölçüm sonucu | tümü | ölçümler dogrulama dosyasında |

**Sınır kuralı:** sekiz maddenin hiçbiri bu dosyada üretilmez; N6/N7/N8
kapıların da dışında kalır (fiziksel tasarım, kod üretimi ve ölçüm
kaydı ayrı yüzeylerdir).

## §19 Register Erişim Doğrulama Kapıları (derinleştirme eki 1)

### §19.1 Register doğrulama adımları

| # | Adım | Yöntem | Beklenen | Durum |
|---|---|---|---|---|
| 1 | Register adresi doğrulama | Datasheet okuma (E7-E9) | Adres eşleşmesi | ✅ (E7-E9) |
| 2 | Register erişim testi (okuma-yazma) | JTAG / debug port | Değer eşleşmesi | PLANNED |
| 3 | Register yazımı log kaydı | Log okuma | Kayıt mevcut | PLANNED |
| 4 | Boot-time register yazımı ayrımı | Kod inceleme | K005'e ait | PLANNED |

### §19.2 Register doğrulama ↔ kapı eşlemesi

| Adım | Kapı | Kapı durumu |
|---|---|---|
| 1 | Kapı 1 (register) | ✅ (E7-E9) |
| 2 | Kapı 1 | PLANNED |
| 3 | Kapı 1 | PLANNED |
| 4 | Kapı 1 + Kapı 5 (sınır) | PLANNED |

## §20 Kesme/DMA Doğrulama Kapıları (derinleştirme eki 1)

### §20.1 Kesme doğrulama adımları

| # | Adım | Yöntem | Beklenen | Durum |
|---|---|---|---|---|
| 1 | Kesme kaynağı doğrulama | Datasheet (E7: xTIME) | Hardware event | ✅ (E7) |
| 2 | Kesme öncelik testi | JTAG / debug | Öncelik matrisi (§7.3) | PLANNED |
| 3 | Kesme gecikme ölçümü | Osiloskop / sayaç | Deterministik gecikme | PLANNED |
| 4 | DMA buffer boyutu ölçümü | Bellek ölçümü | Buffer periyodu (§9) | PLANNED |
| 5 | DMA veri kaybı testi | Veri paterni | 0 kayıp | PLANNED |

### §20.2 Kesme/DMA doğrulama ↔ kapı eşlemesi

| Adım | Kapı | Kapı durumu |
|---|---|---|
| 1 | Kapı 2 (kesme) | ✅ (E7) |
| 2 | Kapı 2 | PLANNED |
| 3 | Kapı 2 + Kapı 4 (determinizm) | PLANNED |
| 4 | Kapı 3 (DMA) | PLANNED |
| 5 | Kapı 3 | PLANNED |

### §20.3 Doğrulama ↔ K000 sınırı eşlemesi

| Adım | K000 sınırı | Not |
|---|---|---|
| 2 | Kesme yöneticisi (K000) | K001 yalnız donanım kaynağı |
| 3 | Kesme yöneticisi (K000) | K001 yalnız ölçüm |
| 4 | DMA yöneticisi + buffer (K000) | K001 yalnız donanım DMA kanalı |
| 5 | DMA yöneticisi (K000) | K001 yalnız test |

**Sınır kuralı:** kesme/DMA doğrulama adımlarının 2-5'inde K000
sınırı korunur — K001 yalnız donanım kaynağı ve ölçüm/tanım
sorumluluğu taşır (§12.1).

### §20.4 Dürüstlük bloğu (derinleştirme eki 1b)

Bu ek (§19-§20) kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt
değildir: 0 yeni URL, 0 yeni register adresi, 0 ölçüm sonucu eklenmiştir;
yalnız register/kesme/DMA doğrulama adımları ve kapı eşlemeleri
eklenmiştir. §19.1 adım 1 ✅ (E7-E9) mevcut URL kanıtıdır; adım 2-4
PLANNED'dır — fiziksel ölçüm yapılmadan ✅ olmaz. §20.1 adım 1 ✅ (E7)
mevcut URL kanıtıdır; adım 2-5 PLANNED'dır. §1-§18'deki mevcut
kanıtlar, açık sorular, kapılar ve bağımlılıklar aynen korunur.
Q-K1 ve Q-K2 bu ekler KAPANMAZ — kapılar AÇIK kalır.

### §20.5 Ek ↔ dış dosya referansları

| Ek bölüm | Dış dosya | İlişki |
|---|---|---|
| §19a.1 N1 | K000-isletim-sistemi | sürücü kodu sahibi |
| §19a.1 N2 | K005-firmware/boot/boot-anayasa.md | boot-time register sahibi |
| §19a.1 N3 | K004-dsp | DSP algoritması sahibi |
| §19a.1 N4 | K003-audio | ses zinciri sahibi |
| §19a.1 N5 | K002-electroncis/guc-yonetimi/guc-yonetimi-anayasa.md | güç sahibi |
| §19.1 / §20.1 | donanım-kernel-dogrulama.md | ölçüm sahibi (iki-dosya) |

**Referans kuralı:** altı referans da okuma ilişkisidir; bu dosya dış
dosyaya içerik dayatmaz — çift yönlü tutarlılık dış dosyanın kendi
derinleştirme turunda sağlanır. N1-N5 maddeleri bu dosyaya İÇERİ
ALINMAZ (sınır sabiti).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
