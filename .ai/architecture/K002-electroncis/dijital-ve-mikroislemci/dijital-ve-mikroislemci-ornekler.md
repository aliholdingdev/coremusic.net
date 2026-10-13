---
node_id: K002-electroncis/dijital-ve-mikroislemci/dijital-ve-mikroislemci-ornekler
node_type: module
status: draft
parent_id: K002-electroncis/dijital-ve-mikroislemci
children: []
depends_on:
  - K001-donanım
  - K005-firmware
evidence:
  - "Repo'da elektronik kodu YOK (§2.1 dürüstlük kaydı)"
  - "Örnek yapılandırma + anti-pattern + eşik tabloları §1-§4"
  - "Web doğrulama: 15 benzersiz URL · HTTP 200 · envanter kesişimi 0 (§5.1)"
validation: "draft — 2026-10-13 yazıldı · ≥500 ölçümü yazım sonrası"
open_questions:
  - "Q-DVM-O1: Örnek register değerleri part seçilmeden üretilemez → UNKNOWN"
  - "Q-DVM-O2: Firmware kod örneği yüzeyi K005/L0 → ⚠️ VERIFICATION REQUIRED"
group: kod
---

# dijital-ve-mikroislemci-ornekler — Dijital Örnekler

> **Yazım dokunulmaz:** `electroncis` (v2 §3 KURAL 1). Bu dosya
> `dijital-ve-mikroislemci` alt-konusunun **kod/örnek** katmanıdır:
> örnek yapılandırma, kod yüzeyi, anti-pattern, eşik/kabul, referans.

**Gerçeklik beyanı:** CoreMusic deposunda elektronik/gömülü kod **YOKTUR**
(`*.cpp` = 0 · `*.h` = 0 · `CMakeLists.txt` = 0 · NevaEngine = PLANNED).
§2'deki kod blokları **şartname taslağıdır** ve etiketlenmiştir; repo'da
varmış gibi sunulmaz.

| # | Alan | Değer |
|---|---|---|
| 1 | node_id | `K002-electroncis/dijital-ve-mikroislemci/dijital-ve-mikroislemci-ornekler` |
| 2 | group | `kod` (tirnaksız) |
| 3 | Örnek türü | repo sınır kanıtı · şartname taslağı · anti-pattern · eşik |
| 4 | Elektronik kod örneği | 0 — Repo'da elektronik kodu YOK |
| 5 | Kardeşler | `-anayasa` (kural) · `-dogrulama` (kanıt) |
| 6 | Durum | `draft` (A1 tur-1) |

## §1 Örnek Yapılandırma

### §1.1 Örnek envanteri

| # | Örnek | Tür | Kanıt | Kullanım |
|---|---|---|---|---|
| 1 | Saat/PLL katsayı şeması (kural) | şartname | anayasa §2.3 | tasarım |
| 2 | GPIO init sırası | şartname | anayasa §3.1 | boot |
| 3 | I2S çerçeve tanımı | şartname | anayasa §2.5 | arayüz |
| 4 | Tampon/kuyruk planı | şartname | anayasa §2.4 | bellek |
| 5 | IRQ öncelik sırası | kural | anayasa §3.3 | gerçek zamanlı |
| 6 | Zamanlama bütçesi (512/48 kHz) | hesap | anayasa §3.6 | ölçüm |
| 7 | Repo PHP kanıtları | gerçek kod | §2.2 | sınır |
| 8 | Anti-pattern listesi | gözlem | §3 | kaçınma |

### §1.2 Örnek yazım kuralları

| # | Kural | Doğru | Yanlış |
|---|---|---|---|
| 1 | Kaynak | `Kaynak: .ai/electronic/... satır N` | "vault'ta vardı" |
| 2 | Register değeri | `UNKNOWN` / veri sayfası | tahmini adres |
| 3 | Kod | `Şartname taslağı — repo'da yok` etiketi | varmış gibi sunmak |
| 4 | Birim | ms · µs · kHz · MHz · mA | birimsiz sayı |
| 5 | Bilinmeyen | `⚠️ VERIFICATION REQUIRED` | yer tutucu |
| 6 | Ölçüm | hedef + yöntem + durum | "hızlı" / "yeterli" |
| 7 | İmla | `electroncis` korunur | düzeltme denemesi |
| 8 | Sahiplik | her örnek bir dosyaya bağlı | sahipsiz örnek |

### §1.3 Sınır kanıtları (gerçek repo kodu — yalnız okuma)

| # | Dosya:Satır | Ne yapar | Dijital karşılığı | Ders |
|---|---|---|---|---|
| 1 | `shared/src/Cache/ApcuAdapter.php:9` | `function_exists` | yetenek sorgusu | donanım yoksa register yazma |
| 2 | `shared/src/Api/Bff/DesktopBff.php:35` | `php_uname('s')` | platform tespiti | OS ≠ yonga |
| 3 | `shared/src/Session/SessionConfig.php:33-36` | geçici dizin + `0750` | ortam bağımlılığı | bellek/izin varsayma |
| 4 | `shared/src/AI/AIEngine.php:291` | `ffprobe` çağrısı | dış araç varsayımı | harici modül varsayımı kırılır |
| 5 | `home.coremusic.net/include/Stream/MusicStreamHandler.php:185,189` | `is_link`/`realpath` | durum/çözümleme | boot durum makinesi analojisi |

### §1.4 Yapılandırma örnekleri (şartname taslağı)

| # | Yapılandırma | Değer | Birim | Bağlı eşik |
|---|---|---|---|---|
| 1 | Örnekleme | 48 | kHz | sartname |
| 2 | Tampon | 512 | örnek | sartname |
| 3 | Veri biçimi | 32-bit float | — | ADR-017 |
| 4 | Kanal | 8.1 (9 kanal) | — | sartname |
| 5 | Önbellek hizası | 64 | bayt | L0 kuralı 5 |
| 6 | İş bütçesi | 5.33 | ms | anayasa §3.6-3 |
| 7 | Örnekleme periyodu | 20.83 | µs | hesap |
| 8 | Tampon süresi | 10.67 | ms | hesap |
| 9 | ASIO kilit | exclusive | — | L0 kuralı 9 |
| 10 | Ses kabuğu | ASIO / WASAPI | — | L0 kuralı 11 |

### §1.5 Boot init sırası (şartname taslağı)

| # | Adım | İş | Bağımlılık | Hata davranışı |
|---|---|---|---|---|
| 1 | Saat | XTAL + PLL | — | güvenli moda |
| 2 | Pin | GPIO yön | 1 | boşa akım yok |
| 3 | Bellek | statik bölge + tamponlar | 1 | allocation yok |
| 4 | UART | teşhis kanalı | 1 | sessiz devam |
| 5 | DSP kanalı | handshake | 1-3 | timeout → tekrar |
| 6 | DAC | mute → konfigürasyon → unmute | 1-3 | pop önleme |
| 7 | Ses yolu | cihaz kilidi | 1-6 | cihaz meşgul hatası |
| 8 | Sayaçlar | xrun/IRQ sayacı sıfırla | 1-7 | ölçüm başlar |

## §2 Kod

### §2.1 Dürüstlük kaydı — repo'da gömülü/elektronik kodu YOK

| # | Aranan | Sonuç | Kaynak |
|---|---|---|---|
| 1 | `*.cpp` / `*.h` | 0 | L0 kural kaydı |
| 2 | `CMakeLists.txt` | 0 | L0 kural kaydı |
| 3 | `NevaEngine/` | YOK → PLANNED | L0 kural kaydı |
| 4 | `.ai/electronic/**` | 60 dosya · 18.242 satır · Markdown | 2026-10-13 ölçüm |
| 5 | `shared/src/**` | PHP/OS yüzeyi | §2.2 |

**Cümle (bağlayıcı):** **Repo'da elektronik kodu YOK — kaynak:
`.ai/electronic/**` (60 dosya · 18.242 satır).**

### §2.2 Repo kodu örnekleri (yalnız okuma)

| # | Dosya:Satır | Kalıp | Dijital karşılığı |
|---|---|---|---|
| 1 | `shared/src/Cache/ApcuAdapter.php:9` | feature-detect | "yonga yoksa modalite yok" |
| 2 | `shared/src/Api/Bff/DesktopBff.php:35` | platform algılama | OS ≠ yonga mimarisi |
| 3 | `shared/src/Session/SessionConfig.php:33-36` | ortam hazırlığı | statik tampon hazırlığı |
| 4 | `shared/src/AI/AIEngine.php:291,295` | dış bağımlılık | harici DSP yoksa fallback |

### §2.3 Şartname taslağı — tampon/kuyruk (C++ parçası)

```cpp
// electroncis: repo'da yoktur; K002 dijital şartname taslağıdır.
constexpr int kBufferFrames = 512;
constexpr int kSampleRateHz = 48000;
constexpr double kBufferSeconds =
    static_cast<double>(kBufferFrames) / kSampleRateHz;
alignas(64) float audioL[kBufferFrames];
alignas(64) float audioR[kBufferFrames];
```

| # | Satır | Anlam | Bağımlılık |
|---|---|---|---|
| 1 | `kBufferFrames` | 512 örnek — sartname | L0 kuralı 8 |
| 2 | `kSampleRateHz` | 48 kHz | L0 kuralı 8 |
| 3 | `kBufferSeconds` | 10.67 ms | anayasa §3.6 |
| 4 | `alignas(64)` | önbellek satırı hizası | L0 kuralı 5 |
| 5 | `float` | 32-bit float | ADR-017 |
| 6 | statik dizi | çalışma anında tahsis yok | L0 kuralı 1 |

### §2.4 Şartname taslağı — PLL katsayı hesabı

```
f_vco = f_ref * N / M
f_out = f_vco / P
kural: f_vco_min <= f_vco <= f_vco_max (part veri sayfasından)
```

| # | Değerbilir | Değer | Birim | Not |
|---|---|---|---|---|
| 1 | f_ref (XTAL) | 24.576 | MHz | ses saati ailesi (örnek) |
| 2 | N (çarpan) | 1 | — | veri sayfası okunmadı → kural |
| 3 | M (bölücü) | 1 | — | kural |
| 4 | hedef f_out | 12.288 | MHz | 256×48 kHz (örnek) |
| 5 | f_vco | ⚠️ | MHz | part limiti okunmalı → UNKNOWN |
| 6 | lock time | ⚠️ | µs/ms | veri sayfası → UNKNOWN |

**Uyarı:** Yukarıdaki sayılar **örnek/şartname** seviyesindedir; kesin
değerler part veri sayfası okunmadan **kesin** sayılmaz (Q-DVM-O1).

### §2.5 Şartname taslağı — I2S çerçeve

| # | Alan | Kural | Değer |
|---|---|---|---|
| 1 | LRCLK | = fs | 48 kHz |
| 2 | BCLK | fs × bit × kanal | 48k × 32 × 2 = 3.072 MHz (örnek) |
| 3 | MCLK | 256 fs (tipik) | 12.288 MHz (örnek) |
| 4 | Format | standby'da belirlenir | ⚠️ (part) |
| 5 | Kenar | veri geçişi kenarı | ⚠️ (part) |
| 6 | Kanal sırası | sol/sağ | LRCLK mantığı |

### §2.6 Kod → sahiplik haritası

| # | Kod parçası | Sahip | Bu dosyanın rolü |
|---|---|---|---|
| 1 | Tampon tanımı | K005/L0 | şartname |
| 2 | ISR gövdesi | K005 | kural (anayasa §3.3) |
| 3 | DSP zinciri | K004 | yalnız bütçe |
| 4 | Register yazımı | K005 | şartname (anayasa §2.6) |
| 5 | Test kodu | K019-testing | eşik sağlama |

### §2.7 Şartname taslağı — saat ve kare tabloları (48 kHz)

#### §2.7.1 Saat hiyerarşisi tablosu

| # | Sinyal | Frekans | Hesap | Kullanım |
|---|---|---|---|---|
| 1 | fs | 48 kHz | sistem örnekleme hızı | tüm zincir |
| 2 | MCLK | 12.288 MHz | fs × 256 | DAC/ADC üst saat |
| 3 | BCLK (2 kanal × 32 bit) | 3.072 MHz | fs × 2 × 32 | I2S seri saat |
| 4 | BCLK (8 kanal × 32 bit) | 12.288 MHz | fs × 8 × 32 | TDM8 çerçeve |
| 5 | LRCLK | 48 kHz | fs = kanal seçici | I2S/TDM çerçeve |
| 6 | TDM FSYNC | 48 kHz | fs, tek darbe/kare | TDM kare başlangıcı |
| 7 | SPI saat (DAC yükleme) | ≤ 20 MHz | üreteç şartname üst sınır | register yazımı |
| 8 | I2C SCL (yapılandırma) | 400 kHz | fast-mode sınırı | boot yapılandırma |

#### §2.7.2 Örnek/kanal kare tablosu

| # | Kapsayıcı | Boyut | Hesap | Not |
|---|---|---|---|---|
| 1 | Örnek (float) | 4 B | 32-bit float (ADR-017) | tüm zincir ortak |
| 2 | Kanal bloğu | 16 B | 4 örnek × 4 B | sayaç adımı 4 |
| 3 | 512 örnek tampon | 2048 B/kanal | 512 × 4 B | sartname kuralı 8 |
| 4 | 9 kanal tampon | 18432 B | 9 × 2048 B | 8.1 zinciri |
| 5 | Çift tampon (32/64) | 36864 B | 2 × 18432 B | xrun toleransı |
| 6 | DMA tampon (TDM8) | 16384 B | 8 × 512 × 4 B | kare hizası |
| 7 | Tepe kuyruk | 32 B | 8 örnek × 4 B | ölçüm yakalama |
| 8 | ISR örneği (tek çağrı) | 2048 B | 512 × 4 B | kilitlenmemiş örnekleme |

#### §2.7.3 Bütçe referans tablosu (48 kHz / 512 örnek)

| # | Kalem | Süre | Hesap | Kapı |
|---|---|---|---|---|
| 1 | Kare periyodu | 10.667 ms | 512 / 48000 | — |
| 2 | ISR bütçesi (%50 pay) | 5.333 ms | 10.667 × 0.5 | §4.1 |
| 3 | Giriş DMA kopyası | ≤ 0.5 ms | ölçüm kapısı | §4.5 |
| 4 | DSP zinciri (9 kanal) | ≤ 2.5 ms | K004 bütçesi | §4.1 |
| 5 | Çıkış DMA kopyası | ≤ 0.5 ms | ölçüm kapısı | §4.5 |
| 6 | Rezerv (geri kalan) | ≥ 1.833 ms | 5.333 − 3.5 | — |
| 7 | Kuyruk dolum eşiği | %75 | xrun kapısı | §4.3 |
| 8 | ISR tepe süresi | ≤ 6 ms | 5.333 + ölçüm payı | §4.1 |

> **Bütçe notu:** Tablo değerleri hesap değeri taşır; ölçüm kapıları §4.5'te doğrulanır (uydurma ölçüm yok).

### §1.6 Uçtan uca örnek — 8.1 kanal zinciri (şartname)

| # | Aşama | Girdi | Çıktı | Sahip |
|---|---|---|---|---|
| 1 | Kaynak okuma | dosya/akış | PCM 48 kHz | K000/K015 |
| 2 | Örnekleme/çözümleme | 16/24-bit | 32-bit float | K005 |
| 3 | Tamponlama | akış | 512 örnek/kuyruk | bu dosya (§1.4) |
| 4 | DSP zinciri | 9 kanal | 9 kanal | K004 |
| 5 | Kanal eşleme | mantiksel kanal | fiziksel çıkış | K004 + bu dosya |
| 6 | I2S çerçevesi | kare veri | LRCLK/BCLK/MCLK | bu dosya (§2.5) |
| 7 | DAC (PCM3168A) | dijital | analog | `devre-ve-analog` |
| 8 | Güç/koruma | rails | DC 0.5 V koruma | `guc-yonetimi` |
| 9 | Ölçüm | çıkış | SNR/THD raporu | `imalat-ve-test` |

| # | Kanal | Rol | Not |
|---|---|---|---|
| 1 | L / R | ön | hat |
| 2 | C | merkez | diyalog |
| 3 | Ls / Rs | surround | arka |
| 4 | Lss / Rss | surround üst | üst |
| 5 | LFE | subwoofer | bant kısıtlı |
| 6 | Toplam | 9 fiziksel | 8.1 |

### §1.7 Hata senaryosu örnekleri (ne olur → ne yapılır)

| # | Senaryo | Belirti | tepki | Sahip |
|---|---|---|---|---|
| 1 | PLL lock kaybı | fs kayması | mute → yeniden kur | bu dosya |
| 2 | xrun | tampon boşluğu | sayaç + rapor | bu dosya + K004 |
| 3 | I2C NACK | ACK yok | tekrar → sınır → bırak | bu dosya |
| 4 | DAC mute açılmama | sessizlik | mute durumu sorgu | `devre-ve-analog` |
| 5 | DC offset >0.5 V | çıkışta DC | röle tetiği | L0 kuralı 10 |
| 6 | ASIO cihazı meşgul | kilit çakışması | kullanıcıya bildir | L0 kuralı 9 |
| 7 | Bellek taşması | bozulma | güvenli duruş | bu dosya |
| 8 | Watchdog | kilitlenme | yeniden başlat | K005 |
| 9 | Saat kaynağı kaybı | çerçeve senkron kaybı | mute → senkron → unmute | bu dosya |
| 10 | Yetersiz CPU | bütçe aşımı | zincir sadeleştirme | K004 + ölçüm |

| # | Kayıt alanı | Tip | Dönem |
|---|---|---|---|
| 1 | xrun sayısı | sayaç | sürekli |
| 2 | IRQ süresi (tepe) | ms | her sürümde |
| 3 | CPU yükü | % | her sürümde |
| 4 | Bellek haritası | bayt | derleme |
| 5 | Saat durumu | bayrak | boot |
| 6 | NACK/hata | sayaç | entegrasyon |

## §3 Anti-pattern

### §3.1 Dijital tasarım anti-pattern'leri

| # | Anti-pattern | Belirti | Doğrusu |
|---|---|---|---|
| 1 | Boot'da pin belirsiz bırakmak | gereksiz akım/gürültü | yönü ilk işaretle |
| 2 | IRQ içinde uzun iş | jitter/xrun | bayrak + ana döngü |
| 3 | Ses yolunda lock | kilit bekleme | lock-free (L0 kuralı 2) |
| 4 | Ses yolunda allocation | tahsis gecikmesi | statik tampon (L0 kuralı 1) |
| 5 | PLL kilidini beklemeden devam | kararsız fs | lock status kontrolü |
| 6 | I2C'de pull-up unutmak | HOkuma | harici/dahili pull-up |
| 7 | BCLK'yi fs ile karıştırmak | çerçeve kayması | LRCLK/BCLK ayrımı |
| 8 | Tek tamponla çift yön | veri bozulması | çift tampon/önbellek temizliği |
| 9 | xrun'u sessizce sıfırlama | kayıp gizleme | sayaç + rapor |
| 10 | Bellek haritasını tahminleme | çakışma | linker haritası okuma |

### §3.2 Dosya/yazım anti-pattern'leri

| # | Anti-pattern | Neden yasak | Doğrusu |
|---|---|---|---|
| 1 | Kaynaksız sayı | doğrulanamaz | satır/URL atıfı |
| 2 | Uydurma register adresi | sahtecilik | `UNKNOWN` |
| 3 | Vault paragrafını yapıştırmak | dolgu | özet + satır |
| 4 | İmla düzeltme çabası | dokunulmaz yazı | aynen koru |
| 5 | Yer tutucu ile geçiş | yasak kelime | `⚠️ VERIFICATION REQUIRED` |
| 6 | Boş hücre | bilgi kaybı | `—` / `UNKNOWN` |
| 7 | Sahipsiz eşik | kimin kuralı? | L0/ADR/sartname sahibi |
| 8 | Envanterden tekrar URL | kesişim ihlali | benzersiz URL |

### §3.3 Sınır anti-pattern'leri

| # | Hatalı davranış | Doğrusu |
|---|---|---|
| 1 | Register değerini burada sabitlemek | şartname ver, kodu K005'e bırak |
| 2 | EQ katsayısını tartışmak | algoritma K004 |
| 3 | DC-DC topolojisi seçmek | `guc-yonetimi` |
| 4 | Osilatör şemasını çizmek | `devre-ve-analog` |
| 5 | AOI/JTAG üretim testini yazmak | `imalat-ve-test` |
| 6 | Part envanteri güncellemek | `K001-donanım` |
| 7 | ASIO kabuğu kodu yazmak | `K000-isletim-sistemi` |

### §3.4 Ölçüm anti-pattern'leri

| # | Hatalı ölçüm | Sahte sonuç | Doğru |
|---|---|---|---|
| 1 | Tek seferlik zamanlama | "hızlı" | ortalama + tepe |
| 2 | xrun saymadan "stabil" | hata gizleme | sayaç + uzun koşu |
| 3 | Yüksüz CPU ölçümü | düşük yük | tam zincir açıkken |
| 4 | Tek frekansta jitter | temiz saat | geniş bant ölçüm |
| 5 | Belleği tek çalıştırmada ölçmek | tutarlılık | tekrarlı koşu |

## §4 Eşik / Kabul

### §4.1 Zamanlama kapıları

| # | Kapı | Hedef | Kaynak | Durum |
|---|---|---|---|---|
| 1 | Tampon süresi | 10.67 ms | hesap (512/48k) | sabit |
| 2 | İş bütçesi | ≤ 5.33 ms (%50) | anayasa §3.6 | ölçüm PLANNED |
| 3 | Örnekleme periyodu | 20.83 µs | hesap | sabit |
| 4 | xrun | 0 | kural | ölçüm PLANNED |
| 5 | IRQ süresi | bütçe içi | kural | ölçüm PLANNED |

### §4.2 Saat kapıları

| # | Kapı | Hedef | Durum |
|---|---|---|---|
| 1 | Frekans | 48 kHz | sartname |
| 2 | Jitter | tanımlı değil | ⚠️ UNKNOWN (Q-DVM2) |
| 3 | PLL lock | boot bütçesi içi | ölçüm PLANNED |
| 4 | Kanal sapması | tanımlı değil | UNKNOWN |

### §4.3 Bellek kapıları

| # | Kapı | Hedef | Yöntem |
|---|---|---|---|
| 1 | Audio thread tahsisi | 0 | kod incelemesi |
| 2 | Önbellek hizası | 64 bayt | `alignas(64)` |
| 3 | Statik plan aşımı | yok | linker haritası |
| 4 | Yığın taşması | yok | canary/guard |
| 5 | Tampon boyutu | 512 × kanal × 4 bayt | hesap |

### §4.4 Arayüz kapıları

| # | Kapı | Hedef | Yöntem |
|---|---|---|---|
| 1 | I2S çerçeve | tutarlı | mantık analizörü |
| 2 | I2C ACK | 0 NACK (süreçli) | sayaç |
| 3 | SPI mod eşleşmesi | CPOL/CPHA | veri sayfası |
| 4 | Seviye uyumu | gerilim domainsi | `devre-ve-analog` |

### §4.5 Kabul özeti

| # | Eşik | Değer | Sahip | Ölçüm |
|---|---|---|---|---|
| 1 | Buffer | 512 @ 48 kHz | sartname | kod |
| 2 | Bütçe | 5.33 ms | hesap | ölçüm |
| 3 | Tahsis | 0 | L0 kuralı 1 | inceleme |
| 4 | Lock | mutex yok | L0 kuralı 2 | inceleme |
| 5 | Hiza | `alignas(64)` | L0 kuralı 5 | kod |
| 6 | Sürüm kanıtı | YOK | L0 kuralı 12 | ⚠️ |

### §4.6 Kademeli kabul matrisi

| # | Kademe | Kapı | Geçerse | Geçmezse |
|---|---|---|---|---|
| 1 | K1 | format (dosya/ölçü) | dosya kabul | genişlet |
| 2 | K2 | statik inceleme (tahsis/lock) | kod şartnameye uyar | iade |
| 3 | K3 | birim test | devam | iade |
| 4 | K4 | zamanlama ölçümü | bütçe ✓ | zincir sadeleştir |
| 5 | K5 | xrun koşusu (uzun) | kararlı | kök neden |
| 6 | K6 | ses ölçümü (SNR/THD) | üretim adayı | `devre-ve-analog`/`guc-yonetimi` |
| 7 | K7 | termal/koruma | teslim | tasarım değişikliği |

| # | Kapı | Eşik | Ölçüm durumu |
|---|---|---|---|
| 1 | K4 | ≤ 5.33 ms | PLANNED |
| 2 | K5 | xrun = 0 | PLANNED |
| 3 | K6 | SNR >100 dB · THD+N <0.01% | PLANNED |
| 4 | K1 | ≥500 / 40-100 | bu turda ölçülür |

### §4.7 Örnek test kayıt formu (doldurulacak alanlar)

| # | Alan | Birim | Dolduran | Bağlı kapı |
|---|---|---|---|---|
| 1 | Sürüm/commit | — | K005 | K3 |
| 2 | Tampon işleme süresi (ortalama) | ms | ölçüm | K4 |
| 3 | Tampon işleme süresi (tepe) | ms | ölçüm | K4 |
| 4 | CPU yükü (ortalama) | % | ölçüm | K4 |
| 5 | xrun sayısı (60 dk) | adet | koşu | K5 |
| 6 | IRQ süresi (tepe) | µs | ölçüm | K4 |
| 7 | Bellek planı (toplam) | KB | linker | K2 |
| 8 | I2C NACK | adet | log | K5 |
| 9 | Saat durumu | bayrak | boot | K2 |
| 10 | SNR | dB | ses ölçümü | K6 |
| 11 | THD+N @1 kHz | % | ses ölçümü | K6 |
| 12 | DC offset | V | ses ölçümü | K6/K7 |
| 13 | Sıcaklık | °C | termokupl | K7 |
| 14 | Karar | geç/iade | QA | hepsi |

### §4.8 Kapı → dosya sorumluluğu

| # | Kapı | Sahip dosya | Not |
|---|---|---|---|
| 1 | K1 | bu dosya | format |
| 2 | K2 | bu dosya + K005 | statik |
| 3 | K3 | K019-testing | test |
| 4 | K4/K5 | bu dosya | ölçüm |
| 5 | K6/K7 | `imalat-ve-test` | üretim |
| 6 | Tasarım eşiği | `-anayasa.md` §4 | burada tanımlı |

## §5 Referans

### §5.1 Web doğrulama (benzersiz URL — envanter kesişimi 0)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| TI TMS320F28379D veri sayfası | https://www.ti.com/lit/ds/symlink/tms320f28379d.pdf | 2026-10-13 | HTTP 200 |
| TI MSP430F5529 veri sayfası | https://www.ti.com/lit/ds/symlink/msp430f5529.pdf | 2026-10-13 | HTTP 200 |
| TI AM3351 veri sayfası | https://www.ti.com/lit/ds/symlink/am3351.pdf | 2026-10-13 | HTTP 200 |
| TI TXS0108E veri sayfası | https://www.ti.com/lit/ds/symlink/txs0108e.pdf | 2026-10-13 | HTTP 200 |
| TI ISO7741 veri sayfası | https://www.ti.com/lit/ds/symlink/iso7741.pdf | 2026-10-13 | HTTP 200 |
| TI ISO1540 veri sayfası | https://www.ti.com/lit/ds/symlink/iso1540.pdf | 2026-10-13 | HTTP 200 |
| TI SN65HVD230 veri sayfası | https://www.ti.com/lit/ds/symlink/sn65hvd230.pdf | 2026-10-13 | HTTP 200 |
| TI CDCE913 veri sayfası | https://www.ti.com/lit/ds/symlink/cdce913.pdf | 2026-10-13 | HTTP 200 |
| TI LMK04828 veri sayfası | https://www.ti.com/lit/ds/symlink/lmk04828.pdf | 2026-10-13 | HTTP 200 |
| TI TPIC6B595 veri sayfası | https://www.ti.com/lit/ds/symlink/tpic6b595.pdf | 2026-10-13 | HTTP 200 |
| TI SN74LVC1T45 veri sayfası | https://www.ti.com/lit/ds/symlink/sn74lvc1t45.pdf | 2026-10-13 | HTTP 200 |
| TI uygulama notu spma056 | https://www.ti.com/lit/an/spma056/spma056.pdf | 2026-10-13 | HTTP 200 |
| IEEE standart sayfası 1149.7 | https://standards.ieee.org/ieee/1149.7/2165/ | 2026-10-13 | HTTP 200 |
| IEEE standart sayfası 1588 | https://standards.ieee.org/ieee/1588/2163/ | 2026-10-13 | HTTP 200 |
| IEEE standart sayfası 802.3 | https://standards.ieee.org/ieee/802.3/7052/ | 2026-10-13 | HTTP 200 |

**Yöntem:** `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`.
İddia **kimlik düzeyindedir** (part adı URL'den gelir) — içerik iddiası için
PDF okunmamıştır (`⚠️`).

### §5.2 Vault referansları

| # | Kaynak | Ne verir |
|---|---|---|
| 1 | `.ai/electronic/dsp/dsp-engine-architecture.md` | motor mimarisi |
| 2 | `.ai/electronic/firmware-architecture.md` | gömülü mimari sınırı |
| 3 | `.ai/electronic/hardware/**` | saat/arayüz notları |
| 4 | `.ai/electronic/**` (60 dosya · 18.242 satır) | ham kaynak |

### §5.3 Dosya bağımlılıkları

| # | Dosya | İlişki | group |
|---|---|---|---|
| 1 | `dijital-ve-mikroislemci-anayasa.md` | kural kaynağı | mimari |
| 2 | `dijital-ve-mikroislemci-dogrulama.md` | kanıt hedefi | kanit |
| 3 | `index.md` | üst indeks | mimari |
| 4 | `K005-firmware` | kod sahibi | sınır |
| 5 | `K004-dsp` | algoritma sahibi | sınır |

### §5.4 Açık sorular (bu dosyadan)

| # | Soru | Etiket |
|---|---|---|
| Q-DVM-O1 | Register/PLL kesin değerleri part seçilmeden yok | UNKNOWN |
| Q-DVM-O2 | Gömülü kod örneği yüzeyi (K005/L0) | ⚠️ VERIFICATION REQUIRED |
| Q-DVM-O3 | BCLK/MCLK değerleri part veri sayfasına bağlı | UNKNOWN |

### §5.5 Dosya kapanış kontrolü

| # | Kontrol | Hedef |
|---|---|---|
| 1 | Satır | ≥500 |
| 2 | Frontmatter | 10 anahtar · `parent:` yok |
| 3 | `group` | `kod` · tirnaksız |
| 4 | Yer tutucu | 0 |
| 5 | URL kesişimi | 0 |
| 6 | Son satır | Authority (birebir) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
