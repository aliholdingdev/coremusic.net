---
node_id: K002-electroncis/dijital-ve-mikroislemci/dijital-ve-mikroislemci-anayasa
node_type: module
status: draft
parent_id: K002-electroncis/dijital-ve-mikroislemci
children: []
depends_on:
  - K001-donanım
  - K005-firmware
  - K004-dsp
evidence:
  - "Repo'da elektronik kodu YOK (§1.4 dürüstlük kaydı)"
  - ".ai/electronic/dsp/dsp-engine-architecture.md — dijital motor mimarisi (okundu)"
  - "URL doğrulama: 15 benzersiz URL · HEAD HTTP 200 · envanter (2.701) kesişimi 0"
validation: "draft — 2026-10-13 yazıldı · ≥500 ölçümü yazım sonrası"
open_questions:
  - "Q-DVM1: Register map sahipliği bu alt-konuda mı K005'te mi? → ⚠️ VERIFICATION REQUIRED"
  - "Q-DVM2: 512/48 kHz zamanlama bütçesi hangi belgeyle kapanır? → UNKNOWN"
group: mimari
---

# dijital-ve-mikroislemci-anayasa — Dijital / Mikroislemci Anayasası

> **Yazım dokunulmaz:** `electroncis` (v2 §3 KURAL 1). Bu dosya K002'nin
> ikinci alt-konu anayasasıdır: MCU/DSP mimarisi, GPIO/PLL, saat ve
> kararlılık, bellek haritası, dijital arayüzler (I2S/SPI/I2C).

## §0 ELI15

Dijital sistemde üç soru yanıtlanır: **neye** (yonga/mimari), **ne zaman**
(saat/zamanlama) ve **nasıl** (arayüz/pin). Ses uygulamasında bu üçü
birbirine kenetlidir — saat titremesi (jitter) analog gürültüye dönüşür,
yanlış pin tanımı sistem hiç başlamaz, hatalı tampon boyutu (512 örnek)
kesintiye yol açar. Analog taraf `devre-ve-analog`, güç tarafı
`guc-yonetimi`, kod yüzeyi `K005-firmware` buradan **farklı** sahiplerdir.

| # | Soru | Kısa yanıt | Gittiği dosya |
|---|---|---|---|
| 1 | Hangi yonga ailesi? | MCU/DSP mimarisi seçimi + rol dağılımı | anayasa §2.1 |
| 2 | Saat nereden? | kristal/PLL/clock tree + jitter bütçesi | anayasa §2.3 |
| 3 | Pin nasıl tanımlanır? | GPIO yönü, sürücü gücü, alternatif fonksiyon | anayasa §2.2 |
| 4 | Bellek nasıl haritalanır? | Flash/RAM/stack/queue yerleşimi | anayasa §2.4 |
| 5 | Arayüz hangi protokol? | I2S/SPI/I2C/UART/JTAG ayrımı | anayasa §2.5 |
| 6 | Ne ölçülür? | jitter · gecikme · CPU yükü · hata oranı | anayasa §4 |

## §1 Kimlik

### §1.1 Dosya kimliği

| # | Alan | Değer |
|---|---|---|
| 1 | node_id | `K002-electroncis/dijital-ve-mikroislemci/dijital-ve-mikroislemci-anayasa` |
| 2 | node_type | `module` |
| 3 | parent_id | `K002-electroncis/dijital-ve-mikroislemci` |
| 4 | group | `mimari` (tirnaksız) |
| 5 | status | `draft` (A1 tur-1) |
| 6 | Kademe | 4 (alt-konu içerik) |
| 7 | Kardeşler | `-ornekler` (kod) · `-dogrulama` (kanit) · `index.md` |
| 8 | Yazım | `electroncis` — dokunulmaz |

### §1.2 Alan kimliği (bu alt-konu neyi sahiplenir)

| # | Sahiplenilen | Örnek | Sahiplenilmeyen |
|---|---|---|---|
| 1 | MCU/DSP mimarisi ve rol dağılımı | çekirdek sayısı, bellek, çevrim birimi | algoritma (K004) |
| 2 | GPIO/PLL tanımı ve zamanlama | pin yönü, alternatif fonksiyon, PLL katsayısı | register yazan kod (K005) |
| 3 | Saat ve kararlılık | jitter bütçesi, boot sırası | osilatör devresi şeması (devre-ve-analog) |
| 4 | Bellek haritası | Flash/RAM/stack/queue bölgeleri | kaynak kod dosyaları (K005) |
| 5 | Dijital arayüz protokolleri | I2S/SPI/I2C/UART/JTAG çerçeve kuralları | PHY/sürücü yazılımı (K005) |
| 6 | Ölçüm eşikleri | jitter, gecikme, CPU yükü | üretim testi prosedürü (imalat-ve-test) |

### §1.3 Sistem bağlamı (değişmezler)

| # | Bağlam | Değer | Sahip |
|---|---|---|---|
| 1 | Örnekleme frekansı | 48 kHz | sartname |
| 2 | Tampon | 512 örnek | sartname |
| 3 | Veri biçimi | 32-bit float | ADR-017 |
| 4 | DAC | PCM3168A — PCM5122 yasak | ADR-038 H001 |
| 5 | Çıkış topolojisi | 8.1 surround | sartname |
| 6 | Ses erişimi | ASIO Exclusive Lock (tek uygulama) | L0 kuralı 9 |
| 7 | Ses kabuğu | WASAPI / ASIO | L0 kuralı 11 (per-OS FROZEN) |
| 8 | Ölçüm | SNR >100 dB · THD+N <0.01% | sartname (PLANNED) |

### §1.4 Dürüstlük kaydı — repo'da elektronik kodu YOK

| # | Aranan yüzey | Sonuç | Kaynak |
|---|---|---|---|
| 1 | `*.cpp` / `*.h` | 0 dosya | L0 domain kuralı |
| 2 | `CMakeLists.txt` | 0 dosya | L0 domain kuralı |
| 3 | `NevaEngine/electronic` | YOK → PLANNED | L0 domain kuralı |
| 4 | `.ai/electronic/**` | 60 dosya · 18.242 satır · yalnız Markdown | 2026-10-13 disk ölçümü |

**Cümle (bağlayıcı):** **Repo'da elektronik kodu YOK — kaynak:
`.ai/electronic/**` (60 dosya · 18.242 satır).**

## §2 Kavram modeli

### §2.1 MCU / DSP mimarisi (rol dağılımı)

| # | Rol | Ne yapar | Tipik sahip | Sınır |
|---|---|---|---|---|
| 1 | Uygulama MCU | OS/UX/akış, dosya, ağ | K005-firmware | yalnız koordinasyon |
| 2 | Ses DSP | gerçek zamanlı zincir (EQ/kompresör/room) | K004-dsp | 512/48 kHz bütçesi |
| 3 | I/O genişletici | GPIO/konumlandırma | K002 (bu dosya) | tanım burada |
| 4 | DAC (PCM3168A) | dijital → analog | K002 (devre) | ADR-038 |
| 5 | ARM Cortex-M sınıfı MCU | gömülü kontrol | K002 | mimari şartname |
| 6 | Dört Çekirdekli DSP sınıfı | paralel sinyal işleme | K004 | mimari şartname |

### §2.2 GPIO ve pin modeli

| # | Kavram | Kural | Not |
|---|---|---|---|
| 1 | Yön (direction) | boot anında tanımlanır, paddle değil | belirsiz pin = düşük empedans gürültüsü |
| 2 | Sürücü gücü (drive strength) | yük akımına göre seçilir | 2/4/8 mA tipik kademeler |
| 3 | Alternatif fonksiyon | pin'i protokole bağlar | çakışma kontrolü zorunlu |
| 4 | Pull-up/pull-down | boşta kalan girişte tanımlı seviye | I2C için zorunlu |
| 5 | Serbest çıkış (open-drain) | I2C gibi paylaşımlı hatlar | harici pull-up gerekir |
| 6 | Kenar tetiği | IRQ için yükselen/düşen/kutup seçimi | seviye tetiği kaçınılmalı |
| 7 | ESD/seri direnç | dışarıya açılan hatlar | devre tarafında (devre-ve-analog) |
| 8 | Boot pin durumu | bootloader kararı | çakışma yapma |

### §2.3 Saat, PLL ve jitter

| # | Kavram | Tanım | Ses etkisi |
|---|---|---|---|
| 1 | Kristal osilatör (XTAL) | referans frekans kaynağı | taban jitter |
| 2 | PLL | frekans çarpanı/bölücü | jitter amplifikasyonu |
| 3 | Jitter | periyod dalgalanması | gürültü tabanı yükselişi |
| 4 | Clock tree | dağıtım ağacı | kanallar arası sapma |
| 5 | MCLK/BCLK/LRCLK | I2S saat ailesi | çerçeve hizası |
| 6 | Spread-spectrum clock | emisyon azaltma | jitter cezası dikkat |
| 7 | Kilitleme süresi (lock time) | PLL oturma süresi | boot bütçesi |
| 8 | Yedek saat | başarısızlıkta | kararlılık |

### §2.4 Bellek haritası

| # | Bölge | İçerik | Kural |
|---|---|---|---|
| 1 | Flash / kod | firmware imajı | yazma kilidi |
| 2 | RAM (.data/.bss) | değişkenler | statik allokasyon (audio thread: allocation yasak) |
| 3 | Stack | çağrı yığını | derinlik analizi, taşma = sessiz bozulma |
| 4 | Ses tamponu | 512 örnek × kanal × float | `alignas(64)` önbellek hizası (L0 kuralı 5) |
| 5 | Kuyruk (ring buffer) | üretici/türetici | lock-free tek-yazıcı/tek-okuyucu |
| 6 | Kayıt alanı (register map) | MMIO | şartname burada, yazım K005 (Q-DVM1) |
| 7 | Kalıcı ayar | EEPROM/flash bölmesi | yazma ömrü sınırlı |
| 8 | Koruma bölgesi | write-protect | bozulmaya karşı |

### §2.5 Dijital arayüzler

| # | Arayüz | Görev | Anahtar kurallar | Sınır |
|---|---|---|---|---|
| 1 | I2S | ses veri yolu | LRCLK = fs, BCLK = fs×bits×kanal | K004/K005 |
| 2 | SPI | hızlı senkron | CS/CPOL/CPHA eşleşmesi | kayıt/çevre |
| 3 | I2C | yavaş, adresli | 7/10-bit adres, pull-up, clock stretching | sensör/kontrol |
| 4 | UART | konsol/diag | baud eşleşmesi | teşhis |
| 5 | JTAG/SWD | hata ayıklama + DFT | boundary scan (imalat-ve-test) | test |
| 6 | USB | dosya/bağlantı | sınıf tanımları | K000 sınırı |
| 7 | S/PDIF | koaksiyel/optik | biphase mark kodlama | opsiyonel |
| 8 | Paralel / GPIO | LED, buton, röle | yön + sürücü gücü | bu dosya |

### §2.6 Register/map şartname tablosu (kod K005'te — sahiplik Q-DVM1)

| # | Kayıt grubu | Örnek alan | Erişim | Not |
|---|---|---|---|---|
| 1 | Kimlik | part/revision | RO | boot doğrulaması |
| 2 | Saat kontrolü | PLL katsayı, kaynağı | RW | §2.3 |
| 3 | GPIO yön | direction register | RW | §2.2-1 |
| 4 | GPIO veri | set/clear/toggle | RW | hızlı erişim |
| 5 | IRQ maskesi | etkinleştir/kaynak | RW | §3.3 |
| 6 | DMA kanalı | kaynak/hedef/uzunluk | RW | §3.2-3 |
| 7 | I2S çerçeve | format, uzunluk | RW | §2.5-1 |
| 8 | I2C hız/ adres | prescaler, slave | RW | §2.5-3 |
| 9 | SPI modu | CPOL/CPHA/baud | RW | §2.5-2 |
| 10 | Güç/uyku | düşük güç kipi | RW | `guc-yonetimi` gereksinimi |
| 11 | Durum | hata bayrakları | RO | §3.5 |
| 12 | Watchdog | zaman aşımı | RW | §6-34 |

**Sahiplik cümlesi:** Bu tablo **şartnamedir**; register'a değer **yazan kod**
`K005-firmware`'dedir (Q-DVM1 açık).

## §3 Mekanizma akışı

### §3.1 Boot akışı (sıralı — zaman bütçeli)

| # | Adım | İş | Tipik bütçe | Başarısızlık |
|---|---|---|---|---|
| 1 | Reset vektörü | ilk çalıştırma | µs | ölü kart |
| 2 | Bootloader | imaj doğrulama | ⚠️ ms (ölçülmedi) | güvenli moda |
| 3 | Saat kurulumu | XTAL + PLL lock | ms (lock time) | geri dönüş |
| 4 | Pin init | GPIO yön/çekme | µs | gereksiz akım |
| 5 | Bellek init | .data/.bss, tamponlar | µs | allocation yasak — statik |
| 6 | DSP/MCU haberleşme | kanal kurulumu | ⚠️ | timeout |
| 7 | DAC (PCM3168A) | mute → konfigürasyon → unmute | ms | pop sesi |
| 8 | Ses yolu açılışı | ASIO/WASAPI kilit | ⚠️ | cihaz meşgul |

### §3.2 Üretim–tüketim akışı (ses tamponu)

| # | Adım | İş | Kural |
|---|---|---|---|
| 1 | ISR/timer | tampon dolum tetiği | gerçek zamanlı öncelik |
| 2 | Kaynak | 512 örnek float | `noexcept`, allocation yok |
| 3 | Kuyruk | tek yazıcı/tek okuyucu | lock-free (L0 kuralı 2) |
| 4 | DSP zinciri | K004 katsayıları | 512/48 kHz bütçesi |
| 5 | DAC çıkışı | I2S çerçeve | MCLK hizası |
| 6 | Geri besleme | xrun/kesinti sayacı | ölçüm §4.3 |

### §3.3 Kesinti (IRQ) akışı

| # | Kural | Gerekçe |
|---|---|---|
| 1 | IRQ öncelik sırası ses > ağ > dosya | ses kesintisi = duyulabilir olay |
| 2 | IRQ içinde yalnız kayıt + bayrak | uzun iş = jitter kaynağı |
| 3 | IRQ içinde allocation yok | L0 kuralı 1 |
| 4 | IRQ içinde mutex yok | L0 kuralı 2 |
| 5 | IRQ süresi ölçülür | bütçe ihlali = tasarım hatası |

### §3.4 Saat ağacı akışı

| # | Adım | Çıktı | Doğrulama |
|---|---|---|---|
| 1 | XTAL seçimi | referans fs | frekans ölçümü |
| 2 | PLL katsayısı | hedef fs (48 kHz katları) | register hesabı |
| 3 | Lock beklenir | kararlılık | timeout koruması |
| 4 | Dağıtım | MCLK/BCLK/LRCLK | osilloskop |
| 5 | Jitter ölçümü | bütçe ihlali yok | §4.1 |

### §3.5 Düzeltme akışı (hata → kurtarma)

| # | Hata | Algılama | tepki |
|---|---|---|---|
| 1 | xrun (tampon taşması) | bayrak | say → rapor, sessizce geçme |
| 2 | PLL lock kaybı | status bit | yeniden kur + mute |
| 3 | I2C NACK | ACK biti | tekrar → sınır → bırak |
| 4 | Bellek taşması | guard / stack canary | güvenli duruş |
| 5 | Saat kaybı | çerçeve senkron kaybı | mute → senkron → unmute |

### §3.6 Zamanlama bütçesi (512 / 48 kHz)

| # | Büyüklük | Hesap | Değer |
|---|---|---|---|
| 1 | Örnekleme periyodu | 1/48000 | 20.83 µs |
| 2 | Tampon süresi | 512/48000 | 10.67 ms |
| 3 | Kilitleme hedefi (tipik %50) | 10.67 × 0.5 | 5.33 ms iş bütçesi |
| 4 | DMA aktarım adımı | tampon/N | ölçüm §4.3 |
| 5 | IRQ periyodu | tampon/örnek | bütçe tablosu §4.3 |

### §3.7 Test / teşhis akışı (yalnız okuma — kod K005)

| # | Adım | Amaç | Çıktı | Sahip |
|---|---|---|---|---|
| 1 | Statik tarama | kod yüzeyi | rapor | K005 |
| 2 | Birim test | işlev | sonuç | K005 |
| 3 | Zamanlama ölçümü | bütçe | ms/ms | bu dosya (§4.3) |
| 4 | Jitter ölçümü | saat bütçesi | ps/ns | bu dosya (§4.1) |
| 5 | xrun sayaç okuma | kesinti | 0 hedef | K004 + bu dosya |
| 6 | Bellek haritası dökümü | statik plan | bayt | bu dosya (§2.4) |
| 7 | JTAG/SWD erişimi | DFT/teşhis | bağlantı | `imalat-ve-test` |
| 8 | Güç kalemi | rails ölçümü | V | `guc-yonetimi` |

## §4 Ölçüm / eşik

### §4.1 Saat/jitter eşikleri

| # | Parametre | Hedef | Durum |
|---|---|---|---|
| 1 | Jitter bütçesi | ⚠️ tanımlı değil — ADR/ölçüm gerekli | UNKNOWN (Q-DVM2) |
| 2 | PLL lock süresi | boot bütçesine dahil | ölçüm PLANNED |
| 3 | Kanallar arası saat sapması | ⚠️ | UNKNOWN |
| 4 | Frekans doğruluğu | 48 kHz ± katalog | veri sayfası |

### §4.2 Arayüz eşikleri

| # | Parametre | Hedef | Sahip |
|---|---|---|---|
| 1 | I2S çerçeve hizası | kanal başına 0 sapma | K004/K005 |
| 2 | I2C saat hızı | ⚠️ modül/kart seçimi | UNKNOWN |
| 3 | SPI saat hızı | slave limitine kadar | veri sayfası |
| 4 | IRQ yanıt süresi | bütçenin küçük kısmı | ölçüm §4.3 |

### §4.3 Performans eşikleri (ölçülecek)

| # | Metrik | Hedef | Yöntem | Durum |
|---|---|---|---|---|
| 1 | CPU yükü (DSP zinciri) | < %50 | profil | PLANNED |
| 2 | xrun sayısı | 0 | sayaç | PLANNED |
| 3 | Tampon işleme süresi | ≤ 5.33 ms (§3.6-3) | kronometre/Ölçüm | PLANNED |
| 4 | Kesinti gecikmesi | bütçe içi | ölçüm | PLANNED |
| 5 | Bellek kullanımı | statik plan aşımısız | harita ölçümü | PLANNED |

### §4.4 Kaynak/saat eşikleri (kural seviyesi)

| # | Kural | Eşik | Sahip |
|---|---|---|---|
| 1 | Örnekleme | 48 kHz | sartname |
| 2 | Tampon | 512 örnek | sartname |
| 3 | Veri biçimi | 32-bit float | ADR-017 |
| 4 | Önbellek hizası | `alignas(64)` | L0 kuralı 5 |
| 5 | ASIO kilit | tek uygulama | L0 kuralı 9 |
| 6 | Sürüm kanıtı (JUCE 9 / ASIO SDK 2.3.4) | YOK → ⚠️ | L0 kuralı 12 |

### §4.5 Ölçüm yöntemleri (nasıl kanıtlanır)

| # | Metrik | Yöntem | Araç | Frekans |
|---|---|---|---|---|
| 1 | CPU yükü | çağrı/döngü sayacı | profilleyici | her sürümde |
| 2 | Tampon süresi | zaman damgası farkı | kronometre/ölçüm | her sürümde |
| 3 | xrun | sayaç okuma | log/telemetri | sürekli |
| 4 | Jitter | osilloskop/ölçüm | jeneratör + DUT | tasarım doğrulaması |
| 5 | I2S hizası | mantık analizörü | katalog | prototipte |
| 6 | Bellek haritası | linker haritası | araç zinciri | her derlemede |
| 7 | IRQ süresi | kesinti giriş/çıkış damgası | ölçüm | prototipte |
| 8 | I2C NACK oranı | sayaç | log | entegrasyonda |

### §4.6 Kabul kapıları (geçiş kriterleri)

| # | Kapı | Ölçüt | Sonuç |
|---|---|---|---|
| 1 | Saat | jitter bütçesi tanımlı + karşılanır | Q-DVM2 kapanana kadar ⚠️ |
| 2 | Zamanlama | tampon işleme ≤ 5.33 ms (§3.6-3) | ölçüm PLANNED |
| 3 | Kesinti | xrun = 0 | ölçüm PLANNED |
| 4 | Bellek | statik plan, audio thread'de allocation 0 | harita ölçümü |
| 5 | Arayüz | I2S çerçeve tutarlılığı | mantık analizörü |
| 6 | Sürüm | JUCE/ASIO sürümleri kanıtlanır | ⚠️ (L0 kural 12) |

## §5 Komşu sınırı

### §5.1 Alt-konu sınırları

| # | Komşu | Bu dosya ne verir | Bu dosya ne almaz |
|---|---|---|---|
| 1 | `devre-ve-analog` | dijital giriş şartı (seviye, saat) | osilatör/filtre şeması |
| 2 | `guc-yonetimi` | çekirdek/IO gerilim gereksinimi | dönüştürücü topolojisi |
| 3 | `sensor-ve-akteyici` | sensör arayüzünün MCU ucu | sensör ön zinciri |
| 4 | `imalat-ve-test` | JTAG/test erişim tasarımı | üretim testi prosedürü |
| 5 | `K004-dsp` | DSP mimarisi + kaynak kullanımı | algoritma/katsayı |
| 6 | `K005-firmware` | register şartnamesi | register yazan kod |

### §5.2 Çapraz domain sınırları

| # | Domain | Sınır | Etiket |
|---|---|---|---|
| 1 | `K001-donanım` | "VAR" (part/envanter) vs "NASIL" (bu dosya) | taslak |
| 2 | `K000-isletim-sistemi` | OS/ASIO/WASAPI katmanı — yazım yasak | komşu |
| 3 | `K013-integraion` | harici servis entegrasyonu ≠ gerçek zamanlı | taslak |

### §5.3 Sınır çatışması kayıtları

| # | Çatışma | Aday sahipler | Geçici çözüm |
|---|---|---|---|
| 1 | Register map | K002 vs K005 | şartname K002, kod K005 (Q-DVM1) |
| 2 | Saat kaynağı | devre vs dijital | devre şeması kardeşte, bütçe burada |
| 3 | DSP zamanlaması | K002 vs K004 | mimari burada, katsayı K004 |

### §5.4 Sınır cümlesi sözlüğü (kesin ifadeler)

| # | Durum | Doğru cümle | Yanlış cümle |
|---|---|---|---|
| 1 | Part seçimi | "Part envanteri K001'dedir" | "Partı burada seçtim" |
| 2 | Register | "Şartname burada, yazım K005'te" | "Register'ı burada yazıyoruz" |
| 3 | Algoritma | "DSP mimarisi burada, katsayı K004" | "EQ'yu burada tasarlıyoruz" |
| 4 | Saat şeması | "Şema `devre-ve-analog`, bütçe burada" | "Osilatörü burada şemalıyoruz" |
| 5 | Güç | "Gerilim gereksinimi burada, topoloji `guc-yonetimi`" | "DC-DC'yi burada seçiyoruz" |
| 6 | Üretim | "JTAG tasarımı burada, test prosedürü `imalat-ve-test`" | "AOI kriterini burada yazıyoruz" |
| 7 | Ölçüm | "Tasarım eşiği burada, uygulaması üretim testi" | "Ölçümü burada yaptık" |
| 8 | OS | "ASIO/WASAPI K000'dir" | "Ses kabuğunu burada kuruyoruz" |
| 9 | Kod | "Örnek gösterilir, kod K005/L0'da" | "Firmware'i burada yazıyoruz" |
| 10 | Sürüm | "Sürüm kanıtı yok → ⚠️" | "JUCE 9 kullanılıyor" |

## §6 Terim sözlüğü

| # | İngilizce | Türkçe | Kısa tanım |
|---|---|---|---|
| 1 | microcontroller (MCU) | mikrodenetleyici | tek yonga gömülü bilgisayar |
| 2 | digital signal processor (DSP) | dijital sinyal işlemci | paralel sayısal işleme |
| 3 | clock tree | saat ağacı | saat dağıtım yapısı |
| 4 | jitter | saat titremesi | periyod dalgalanması |
| 5 | phase-locked loop (PLL) | faz kilitli döngü | frekans sentezleyici |
| 6 | lock time | kilitlenme süresi | PLL oturma süresi |
| 7 | GPIO | genel amaçlı giriş/çıkış | programlanabilir pin |
| 8 | drive strength | sürücü gücü | çıkış akım kapasitesi |
| 9 | open-drain | boşta kalan çıkış | paylaşımlı hat tipi |
| 10 | pull-up / pull-down | çekme direnci | boşta giriş seviyesi |
| 11 | alternate function | alternatif fonksiyon | pin'in ikinci rolü |
| 12 | edge trigger | kenar tetikleme | IRQ tetikleme tipi |
| 13 | interrupt (IRQ) | kesinti | olay tabanlı akış |
| 14 | DMA | doğrudan bellek erişimi | CPU'suz veri taşıma |
| 15 | I2S | I2S | ses veri protokolü |
| 16 | bit clock (BCLK) | bit saati | I2S bit zamanlaması |
| 17 | word select / LRCLK | kelime seçimi | sol/sağ çerçeve |
| 18 | master clock (MCLK) | ana saat | DAC/codec saat kaynağı |
| 19 | SPI | SPI | seri senkron arayüz |
| 20 | I2C | I2C | adresli yavaş arayüz |
| 21 | UART | UART | asenkron seri |
| 22 | JTAG / SWD | JTAG / SWD | hata ayıklama + test erişimi |
| 23 | boundary scan | sınır taraması | DFT tekniği (IEEE 1149.1) |
| 24 | register map | kayıt haritası | MMIO adres tanımları |
| 25 | MMIO | bellek eşlemeli giriş/çıkış | register erişim biçimi |
| 26 | ring buffer | halka tampon | kuyruk yapısı |
| 27 | lock-free | kilitsiz | mutex'siz eşzamanlılık |
| 28 | xrun | tampon taşması | gerçek zamanlı hata |
| 29 | underrun / overrun | yetersizlik/taşma | xrun alt tipleri |
| 30 | cache line | önbellek satırı | hizalama birimi (64 B) |
| 31 | alignment | hizalama | bellek adres hizası |
| 32 | endianness | bayt sıralaması | veri yerleşimi |
| 33 | stack overflow | yığın taşması | sessiz bozulma riski |
| 34 | watchdog | zaman aşım izleyici | kilitlenme kurtarıcısı |
| 35 | bootloader | önyükleyici | imaj/doğrulama aşaması |
| 36 | firmware | gömülü yazılım | yonga üstündeki kod |
| 37 | peripheral | çevre birimi | yonga içi modül |
| 38 | ISR | kesinti işleyicisi | kesinti rutini |
| 39 | spread-spectrum clock | yayılımlı spektrum saati | emisyon azaltma |
| 40 | dithering | titretme | kuantlama gürültüsü |
| 41 | frame | çerçeve | I2S sol/sağ bloğu |
| 42 | serial / parallel | seri / paralel | veri taşıma biçimi |
| 43 | contention | çakışma | iki sürücü aynı hatta |
| 44 | reset vector | sıfırlama vektörü | ilk çalışma adresi |
| 45 | exclusive lock | özel kilit | ASIO tekil erişim |

## §7 Kapsam dışı

| # | Konu | Gittiği yer | Neden |
|---|---|---|---|
| 1 | Analog saat osilatör şeması | `devre-ve-analog` | sinyal/devre sahibi |
| 2 | Güç rails/DC-DC | `guc-yonetimi` | güç sahibi |
| 3 | DSP algoritması/katsayıları | `K004-dsp` | algoritma sahibi |
| 4 | Register yazan kod / ISR implementasyonu | `K005-firmware` | kod sahibi |
| 5 | PCB katman/üretim kuralı | `imalat-ve-test` | üretim sahibi |
| 6 | Sensör ön zinciri | `sensor-ve-akteyici` | arayüz sahibi |
| 7 | OS sürücüsü / ASIO kabuğu | `K000-isletim-sistemi` | OS sahibi |
| 8 | Satın alma/envanter | `K001-donanım` | "VAR" sahibi |
| 9 | UI/akış | K011-application | uygulama sahibi |

### §7.1 Bilerek yapılmayanlar (dürüstlük)

| # | Yapılmayan | Neden |
|---|---|---|
| 1 | Register adresi/biti üretimi | part veri sayfası okunmadı — uydurma yasak |
| 2 | Jitter sayısal değeri | ölçüm yok (Q-DVM2) |
| 3 | Boot süresi (ms) | vault satırı okunmadı (Q-DVM4) |
| 4 | I2C/SPI hız değerleri | part seçilmedi (Q-DVM5) |
| 5 | Firmware kod bloğu | repo'da elektronik kodu YOK (§1.4) |
| 6 | Bellek haritası adresi | part/derleyici bağımlı — ölçüm gerekli |

## §8 Açık sorular

| # | Soru | Etiket | Sahip | Kapanış yolu |
|---|---|---|---|---|
| Q-DVM1 | Register map sahibi bu alt-konu mu K005 mi? | ⚠️ VERIFICATION REQUIRED | Vault Steward | üst karar |
| Q-DVM2 | Jitter/zamanlama bütçesi hangi belgede? | UNKNOWN | üst karar | ölçüm + ADR |
| Q-DVM3 | JUCE 9 / ASIO SDK 2.3.4 sürüm kanıtı | ⚠️ VERIFICATION REQUIRED | üst karar | sürüm kaydı (L0 kural 12) |
| Q-DVM4 | Boot bütçesi (ms değerleri) vault satırı okunmadı | ⚠️ VERIFICATION REQUIRED | ajan | satır okuma |
| Q-DVM5 | I2C/SPI hız eşikleri part bazlı seçilmedi | UNKNOWN | tasarımcı | veri sayfası okuma |
| Q-DVM6 | XMOS/MCU hedef donanımın kesin part listesi | ⚠️ VERIFICATION REQUIRED | K001 | envanter |
| Q-DVM7 | Bootloader doğrulama yöntemi (imza?) | UNKNOWN | K005 | tasarım kararı |
| Q-DVM8 | Watchdog zaman aşımı değeri | UNKNOWN | K005 | ölçüm |
| Q-DVM9 | DMA kanal sayısı/kullanımı | ⚠️ VERIFICATION REQUIRED | K005 | harita okuma |
| Q-DVM10 | ASIO Exclusive Lock çakışma testi yapıldı mı? | UNKNOWN | L0 | entegrasyon testi |
| Q-DVM11 | 8.1 kanal için I2S kaç çerçeve/lane? | ⚠️ VERIFICATION REQUIRED | K004 | topoloji kararı |
| Q-DVM12 | Yayılımlı spektrum saati jitter cezası değerlendirildi mi? | UNKNOWN | tasarımcı | ölçüm |
| Q-DVM13 | Swap/bytewise big-endian kontrolü (ARM ↔ DSP) | ⚠️ VERIFICATION REQUIRED | K005 | birleşim testi |

## §9 Kaynakça

### §9.1 Web doğrulama (benzersiz URL — envanter kesişimi 0)

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

**Doğrulama yöntemi:** `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`.
İddia biçimi **kimlik düzeyindedir** (part adı URL'den gelir); içerik
iddiası için PDF okunmamıştır (`⚠️` — §8 Q-DVM4/5).

### §9.2 Vault referansları (satır atıfı içerik dosyalarında)

| # | Kaynak | Ne verir |
|---|---|---|
| 1 | `.ai/electronic/dsp/dsp-engine-architecture.md` | dijital motor mimarisi |
| 2 | `.ai/electronic/hardware/**` | saat/arayüz donanım notları |
| 3 | `.ai/electronic/firmware-architecture.md` | gömülü mimari sınırı |
| 4 | `.ai/electronic/**` (60 dosya · 18.242 satır) | ham içerik kaynağı |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
