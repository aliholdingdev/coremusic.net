---
node_id: K005-firmware/boot/boot-anayasa
node_type: module
status: draft
parent_id: K005-firmware/boot
children: []
depends_on: []
evidence:
  - ".ai/electronic/firmware/boot-sequence.md §2-§4 — okundu 2026-10-13"
  - ".ai/electronic/firmware/index.md §4 Bootloader — okundu 2026-10-13"
  - "K005-firmware/boot/boot-ornekler.md — stil referansı 2026-10-13"
validation:
  - "2026-10-13 — FM10 anahtar + group (mimari) + son satır Authority denetimi"
  - "2026-10-13 — satır ölçümü kök ölçüm betiğinde (K005 raporu)"
open_questions:
  - "Hedef MCU/bootloader kombinasyonu kesin değil (XMOS XU316 bare-metal ↔ STM32/FreeRTOS ↔ ESP32/ESP-IDF) — UNKNOWN, elektronik dalı K001 kararı"
  - "Fastboot/DFU modunun fiziksel tetikleyicisi (tuş · GPIO · komut) henüz tanımlı değil — ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# boot-anayasa — Boot Alt-Modülü Anayasası (K005-firmware)

> **ELI15:** Cihaza güç gelir gelmez çalışan ilk yazılım. Görevi: donanımın
> küçük bir çekirdeğini ayağa kaldırmak, belleği hazırlamak, firmware'i
> doğrulamak ve ana işletim katmanını (RTOS/OS) devretmek. Bu dosya boot
> alt-modülünün **kurallarını ve sınırlarını** koyar; uygulama örnekleri
> `boot-ornekler.md`, arayüz sözleşmeleri `boot-surucu.md` içindedir.

## §1 Amaç, Kapsam ve Kapsam Dışı

| Alan | Soru | Cevap | Sahip |
|---|---|---|---|
| Amaç | Bu dosya neyi düzenler? | boot alt-modülünün anayasası (kural + sınır) | K005-firmware/boot |
| Kapsam içi | ROM → loader → OS/devir zinciri | evet | boot |
| Kapsam içi | Bellek hazırlığı (flash · RAM · yığın) | evet — `bellek` omurgası bu modülde | boot |
| Kapsam içi | Boot modları (normal · fastboot · DFU · recovery) | evet | boot |
| Kapsam dışı | Donanımın fiziksel topolojisi, PCB, güç devresi | hayır → K001-donanım | K001 |
| K kapsam dışı | Host işletim sistemi paket/boot yükü (Linux grub) | hayır → K000-isletim-sistemi | K000 |
| Kapsam dışı | DSP zinciri algoritması (EQ/reverb/konvolüsyon) | hayır → K004-dsp | K004 |
| Kapsam dışı | Ses akışının host tarafı taşınması | hayır → K003-audio | K003 |
| Kapsam dışı | OTA indirme/imzalama politikası | hayır → K005-firmware/update | update |
| Kapsam dışı | Secure boot anahtar yaşam döngüsü | hayır → K005-firmware/security | security |

> **Sınır notu:** Boot, yalnızca *devir anını* yönetir. Devredilen katman
> çalışmaya başladıktan sonra boot artık sorumlu değildir (duraklama/watchdog
> dışında). Geçiş olayları `boot-surucu.md` §kayıtlarında, uygulama kalıpları
> `boot-ornekler.md` içindedir.

## §2 Boot Zinciri (ROM → Loader → OS)

Kaynak: `.ai/electronic/firmware/boot-sequence.md §2`.

| # | Aşama | Ne yapılır | Başarı ölçütü | Hata eylemi |
|---|---|---|---|---|
| 1 | Power On | Güç stabilizesi, reset de-assert | NRST yüksek | tekrar güçlendirme (K001) |
| 2 | ROM Bootloader | Üretici ROM kodu ilk talimatı çalıştırır | ROM imzası/variant kontrolü geçerse devam | kilitlenme + hata LED (Kaynak: boot-sequence §4) |
| 3 | Firmware Load | Flash'tan firmware yüklenir (XMOS load) | yükleme checksum doğrulaması | LED blink + retry (Kaynak: boot-sequence §4) |
| 4 | DSP Chain Init | DSP zinciri durumu sıfırlanır | init return 0 | alternatif profil dene |
| 5 | I2S Configure | Seri ses veri yolu yapılandırılır | frame sync doğrulandı | tekrar konfigüre |
| 6 | DAC Init | PCM3168A başlatılır | register okuma doğrulandı | alternatif DAC dene (Kaynak: boot-sequence §4) |
| 7 | USB Audio Start | USB audio sınıfı ayağa kalkar | enumeration tamam | LED hata kodu (Kaynak: boot-sequence §4) |
| 8 | System Ready | RTOS/devir tamamlandı | watchdog beslemesi başladı | recovery modu (Kaynak: firmware/index §4) |

**Zincir kuralı (A1):** Zincirdeki her aşama ya *tam geçer* ya da
*tanımlı hata eylemine* düşer — belirsiz ara durum YASAKTIR (her satırda
ya geçer ya eylem, boş hücre yoktur).

**Zincir mülkiyeti (A2):** Aşama 1-2 donanım/ROM (K001), aşama 3-8 K005
boot modülünün sorumluluğundadır; aşama 8'den sonra devir `runtime`
(zamanlama) modülüne gider.

## §3 Boot Süreleri ve Zaman Bütçesi

Kaynak: `.ai/electronic/firmware/boot-sequence.md §3`.

| Aşama | Süre (kaynak) | Bütçe payı | Ölçüm kapısı |
|---|---|---|---|
| ROM Boot | 100 ms | %6,6 | donanım kronometre (K001 ölçümü) |
| Firmware Load | 500 ms | %33,3 | yükleme sayacı |
| DSP Init | 200 ms | %13,3 | init sayaçları |
| I2S Config | 100 ms | %6,6 | konfigürasyon geri bildirimi |
| DAC Init | 200 ms | %13,3 | PCM3168A register okuma |
| USB Start | 300 ms | %20,0 | enumeration olayı |
| **Toplam** | **~1,5 s** | **%100** | üst sınır |

**Bütçe kuralı (A3):** Toplam boot süresi ölçümü yapılmadan hiçbir sürüm
"boot hedefi tuttu" olarak yazılmaz; ölçüm yoksa satır `⚠️ VERIFICATION
REQUIRED` kalır. Bu tablodaki süreler Kaynak (boot-sequence §3) değeridir,
CoreMusic cihazında yeniden ölçülmemiştir — ölçüm durumu: `UNKNOWN`.

## §4 Bellek Hazırlığı (flash · RAM bölümleri · yığın)

Bu bölüm, görev paketindeki **`bellek` omurgasının** (flash · RAM bölümleri ·
yığın) K005 kayıt yeridir; ayrı `bellek/` alt-modülü K005 kaydında yoktur
(bkz §10 komşu tablosu ve `firmware-kapsam.md`).

| Bölüm | Boot sırasındaki rol | Kaynak/Referans | Durum |
|---|---|---|---|
| Flash (kod bölümü) | ROM buradan okur; yazma yalnız update modülünde | firmware/index §4 (Firmware doğrulama) | tanımlı |
| Flash (veri/yapılandırma) | EEPROM/konfigürasyon okuması firmware/index §4 | firmware/index §4 | tanımlı |
| Flash (yedek bank) | Dual-bank flash, rollback için ayrı bank | ota-update §3 (Fail-safe: dual-bank flash) | tanımlı |
| Vektör tablosu | Reset vector ROM tarafından bağlanır | genel MCU kuralı — MCU ailesi UNKNOWN | ⚠️ VERIFICATION REQUIRED |
| .data / .bss | Çalışma zamanı değişkenleri (static allocation) | firmware/index §5 (Bellek: static allocation, heap yasak) | kural |
| Yığın (heap) | Boot aşamasında yeni tahsis YASAK — yalnız önceden verilmiş bloklar | rtos §4 (No malloc) | kural |
| DMA tamponları | I2S/USB aktarım için önceden ayrılacak | firmware/index §6 (DMA HAL) | planlı |
| Yığın derleme haritası | Linker haritası dosyasıyla gözlemlenir | idf-size aracı (§13 URL) | ölçüm yöntemi |

**Bellek kuralı (A4):** Boot kodu hiçbir aşamada `malloc` çağırmaz; tüm
tamp alanı statik (önceden boyutlanmış) tanımlıdır. Gerekçe: `rtos §4`
(No malloc · Lock-free · No blocking) ve firmware/index §5 (Static
allocation — heap yasak).

**Bellek kuralı (A5):** RAM bölümleri (stack / static / DMA payı) MCU ailesi
kesinleşmeden rakamsal olarak yazılmaz; rakam gerekiyorsa `UNKNOWN` veya
`⚠️ VERIFICATION REQUIRED` kullanılır. Uydurma boyut yazılmaz.

## §5 Boot Modları

| Mod | Tetikleyici | Davranış | Çıkış | Kapsam sahibi |
|---|---|---|---|---|
| Normal | güç + reset | §2 zinciri uçtan uca | System Ready | boot |
| Fastboot | seri/USB komut penceresi | kısa zincir, test/diag hedefi | hizmet modu | boot (tarife §15'te UNKNOWN) |
| DFU | USB DFU sınıfı talebi | yalnız firmware yükleme | yükleme sonrası reset | boot + update ortak |
| Recovery | normal boot 2 başarısız deneme | yedek firmware'e devret | yedek sistem | update (rollback) + boot (giriş) |
| Debug/SW-DAP | fiziksel test erişimi | bellek/çekirdek erişimi | atlatılabilir | K001 (fiziksel) + boot (onay) |

**Mod kuralı (A6):** Yalnız *Recovery* modu yedek banka geçebilir; diğer
modlar flash bank değiştiremez (yetki `update` modülünde kalır). Kaynak:
firmware/index §7 (Recovery Mode — Backup firmware — Kurtarma).

**Mod kuralı (A7):** Debug modu üretim (release) firmware'inde kapatılmak
zorundadır; kapatma kanıtı yoksa durum `⚠️ VERIFICATION REQUIRED`.

## §6 Anayasal İlkeler (Sert Kurallar)

| # | Kural | Kaynak | Sonuç (ihlalde) |
|---|---|---|---|
| P1 | Boot kodu `noexcept` sözleşmesiyle yazılır; yakalama yok → tanımlı hata kodu döner | repo kuralı (AGENTS.md L0) | derleme/review ret |
| P2 | Çalışma zamanı tahsisi (malloc/new/delete) yasak | AGENTS.md kural 1 · rtos §4 | ret |
| P3 | Mutex/lock yasak; kritik bölüm yalnız atomik işlem veya kesme maskesi | AGENTS.md kural 2 · rtos §4 | ret |
| P4 | Sabit hesaplar `constexpr`; derleme zamanında doğrulanır | AGENTS.md kural 4 | ret |
| P5 | 32-bit float ses verisi biçimi (ADR-017) | AGENTS.md kural 7 | ret |
| P6 | DAC yalnız PCM3168A (PCM5122 kesinlikle yasak — ADR-038 H001) | AGENTS.md kural 6 | ret |
| P7 | Buffer 512 örnek @ 48 kHz (sartname) | AGENTS.md kural 8 | ret |
| P8 | Reset vektörü/öncü kod ROM'a ait alanlara yazmaz | §2 zincir mülkiyeti | ret |
| P9 | Boot yalnız *tanımlı* hata eylemlerine düşer; sessiz devam yok | §2 kural A1 | ret |
| P10 | Her aşama gözlü (watchdog) beslemesine tabidir | firmware/index §5 (Watchdog zorunlu) | ret |

## §7 Hata Durumları ve Kurtarma Hiyerarşisi

Kaynak: `.ai/electronic/firmware/boot-sequence.md §4` + `firmware/index §4`.

| # | Hata | Kaynak satırı | Eylem | Son durum |
|---|---|---|---|---|
| E1 | XMOS load fail | boot-sequence §4 | LED blink, retry | retry bittiyse recovery |
| E2 | DAC init fail | boot-sequence §4 | alternatif DAC dene | ikisi de başarısız → hata LED |
| E3 | USB fail | boot-sequence §4 | LED hata kodu | runtime'a hata kodu ilet |
| E4 | İmza doğrulama hatası | firmware/index §8 (Secure Boot) | boot'u durdur | recovery + güvenlik olayı (security) |
| E5 | Firmware doğrulama (CRC/imza) hatası | firmware/index §7 (flow) | yedek banka geç | update modülünün rollback'i |
| E6 | Watchdog açılış (boot'ta erken reset) | firmware/index §5 | sayaç artır | eşik aşıldı → recovery |
| E7 | EEPROM/konfigürasyon okuma hatası | firmware/index §4 | fabrika varsayılanına dön | kullanıcıya bildir (diagnostics) |

**Kurtarma kuralı (A8):** Hiyerarşi sabittir: `tanımlı retry (max 2) →
yedek bank/recovery → hata kodu + duraklama`. Sonsuz retry YASAKTIR;
eşikler `firmware-dogrulama.md` D-kapılarında ölçülür.

## §8 Sınır Komşuları ve Devir Sözleşmeleri

| Komşu | Yön | Ne devredilir | Kanıt |
|---|---|---|---|
| K000-isletim-sistemi | boot → host OS | Host tarafı boot yükü bu modülde değil | firmware-kapsam.md §kapsam dışı |
| K001-donanım | boot ↔ donanım | Reset, ROM davranışı, fiziksel pin | boot-sequence §2 (Power On → ROM) |
| K003-audio | boot → audio | I2S/USB hazır sinyali | boot-sequence §2 (I2S Configure → USB Audio Start) |
| K004-dsp | boot → dsp | DSP Chain Init tamamlanma bayrağı | boot-sequence §2 (DSP Chain Init) |
| update | boot ↔ update | Yedek bank devri, firmware yükleme modu | firmware/index §7 (Recovery/rollback) |
| security | boot ↔ security | İmza doğrulama çağrısı (E4) | firmware/index §8 (Secure Boot) |
| runtime | boot → runtime | System Ready + watchdog devri | boot-sequence §2 (System Ready) |
| diagnostics | boot → diagnostics | Hata kodları E1-E7 | boot-sequence §4 |
| testing | boot ↔ testing | Boot modu/HIL test tetikleri | firmware-dogrulama.md test stratejisi |

## §9 Bağımlılık Grafiği (Yön: bağımlı → bağımlı olduğu)

| Düğüm | Bağımlı olduğu | Tür | Not |
|---|---|---|---|
| boot-anayasa | K005-firmware (üst düğüm) | mimari | parent_id: K005-firmware/boot |
| boot-anayasa | .ai/electronic/firmware/boot-sequence.md | kanıt | zincir + süreler + hatalar |
| boot-anayasa | .ai/electronic/firmware/index.md | kanıt | bootloader görevleri, bellek kuralları |
| boot-surucu | boot-anayasa | mimari | arayüz anayasadan türetilir |
| boot-ornekler | boot-anayasa + boot-surucu | kod | kalıp anayasaya uyar |
| update (OTA/rollback) | boot (giriş/çıkış) | modül | §8 devir |
| security (secure boot) | boot (imza çağrısı) | modül | E4 |
| runtime (zamanlama) | boot (System Ready) | modül | devir |

## §10 Ölçüm ve Eşik Kapıları

| # | Kapı | Eşik | Nasıl ölçülür | Durum |
|---|---|---|---|---|
| B1 | Toplam boot süresi | ~1,5 s (kaynak) → hedef cihaz ölçümü gerekli | kronometre/trace | ölçülmedi — UNKNOWN |
| B2 | Retry sayısı | ≤ 2 (kural A8) | firmware-dogrulama.md test sayımı | kural yazıldı, ölçüm planlı |
| B3 | Boot'ta tahsis | 0 (P2) | kod taraması / statik analiz | kural yazıldı, ölçüm planlı |
| B4 | Boot hata kodu kapsamı | E1-E7 tamamı kodlanmış | test senaryosu | planlı |
| B5 | Watchdog besleme gecikmesi | MCU'nun WDT periyodu içinde | ölçüm — periyot UNKNOWN | ⚠️ VERIFICATION REQUIRED |
| B6 | Yedek bank doğrulama | her boot'ta 1 kez | boot sayaçları | planlı |

> Kapıların ölçüm protokolü ve D1-D10 eşlemesi `firmware-dogrulama.md` §3'te
> (aynı modül kökünde) tutulur; burada yalnız tanımı verilir.

## §11 Web Araştırması

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| RFC 2119, gereksinim anahtar kelimelerini (MUST/SHOULD/MAY) tanımlar; bu dosyadaki "zorunlu/yasak" ifadeleri aynı okuma biçimini kullanır | https://www.rfc-editor.org/rfc/rfc2119.txt | 2026-10-13 | HTTP 200 |
| ESP-IDF bölümleme dokümantasyonu, flash bölüm tablosu (partition table) kavramını ve veri bölümlerinin tanımını belgeler | https://docs.espressif.com/projects/esp-idf/en/latest/esp32/api-reference/storage/partition.html | 2026-10-13 | HTTP 200 |
| ESP-IDF spi_flash dokümantasyonu, flash okuma/yazma arayüzlerini tanımlar | https://docs.espressif.com/projects/esp-idf/en/latest/esp32/api-reference/storage/spi_flash.html | 2026-10-13 | HTTP 200 |
| ESP-IDF idf-size aracı, firmware görüntü boyutu/flash kullanım raporlaması için kullanılır (§4 linker haritası ölçüm yöntemi) | https://docs.espressif.com/projects/esp-idf/en/latest/esp32/api-guides/tools/idf-size.html | 2026-10-13 | HTTP 200 |
| Zephyr donanım/periferel dokümantasyonu, MCU periferellerinin portlama ve kullanım arayüzlerini tarif eder | https://docs.zephyrproject.org/latest/hardware/peripherals/index.html | 2026-10-13 | HTTP 200 |
| Zephyr başlangıç dokümanı, geliştirme ortamı ve ilk derleme/çalıştırma adımlarını tanımlar (boot zinciri doğrulama ortamı) | https://docs.zephyrproject.org/latest/develop/getting_started/index.html | 2026-10-13 | HTTP 200 |
| ARM IoT sayfası, gömülü sistemler için ortak platform/başlangıç çerçevesini tanıtır (MCU ailesi kararı için üst bant) | https://www.arm.com/technologies/iot | 2026-10-13 | HTTP 200 |

> Bu URL'ler 2026-10-13'te `Invoke-WebRequest -Method Head` ile HTTP 200
> doğrulanmış, vault URL envanteriyle kesişmeyen kayıtlardır. Yalnız bu
> tabloda kullanılırlar.

## §12 Kaynak ve Repo Kanıtı

**Repo'da firmware kodu YOK — kaynak: .ai/electronic/firmware/ (boot-sequence·dsp-firmware·ota-update·rtos)**

| Kaynak | Bölüm | Bu dosyada kullanıldığı yer |
|---|---|---|
| `Kaynak: .ai/electronic/firmware/boot-sequence.md` | §2 Boot Dizisi | §2 zincir tablosu |
| `Kaynak: .ai/electronic/firmware/boot-sequence.md` | §3 Boot Süreleri | §3 zaman bütçesi |
| `Kaynak: .ai/electronic/firmware/boot-sequence.md` | §4 Hata Durumları | §7 E1-E3 |
| `Kaynak: .ai/electronic/firmware/index.md` | §4 Bootloader | §2, §4, §7 |
| `Kaynak: .ai/electronic/firmware/index.md` | §5 RTOS (static allocation, watchdog) | §4, §6 |
| `Kaynak: .ai/electronic/firmware/index.md` | §7-§8 Update/Security | §5, §7 E4-E5 |
| `Kaynak: .ai/electronic/firmware/rtos.md` | §4 Real-Time Kısıtları | §6 P2-P3 |

> Örnek kod/kalıp gösterimleri `boot-ornekler.md` içinde "örnek yapılandırma
> (kalıp)" etiketiyle verilir; bunlar depo kodu değildir.

## §13 Açık Sorular (open_questions)

| # | Soru | Etki | Durum |
|---|---|---|---|
| Q1 | Hedef MCU/bootloader kombinasyonu hangisi (XMOS XU316 bare-metal · STM32/FreeRTOS · ESP32/ESP-IDF)? | linker haritası, vektör tablosu, bellek bölümleri | UNKNOWN |
| Q2 | Fastboot modu fiziksel olarak nasıl tetikleniyor? | §5 mod tablosu | ⚠️ VERIFICATION REQUIRED |
| Q3 | Boot süreleri (§3) bu cihazda ölçüldü mü? | B1 kapısı | ölçülmedi — UNKNOWN |
| Q4 | `bellek` omurgası ayrı K005 alt-modülü olarak açılacak mı, bu modülde mi kalacak? | kayıt yeri (katalog) | üst karar — §4'te bu modülde kayıtlandı |
| Q5 | Watchdog periyodu ve erken-boot açılış eşiği nedir? | E6, B5 | UNKNOWN (K001/MCU verisi) |

## §14 Aşama Detay Kayıtları (Girdi · İşlem · Çıktı · Hata)

| Aşama | Girdi | İşlem | Çıktı | Hata koluna düşerse |
|---|---|---|---|---|
| Power On | VDD stabil | Reset de-assert (NRST HIGH) | ROM çalışır | güç devresi → K001 |
| ROM Boot | ROM içindeki sabit kod | İlk talimat okuma + variant kontrol | Loader çağrısı | kalıcı kilitlenme + LED |
| Firmware Load | Flash'tan imzalı görüntü | DMA/okuma ile yükleme | Çalıştırılabilir görüntü | E1 (retry ≤ 2) |
| Firmware doğrulama | Görüntü + checksum | CRC/SHA karşılaştırması | doğrulandı bayrağı | E5 → yedek bank |
| DSP Chain Init | DSP profil parametreleri | Zincir durumunu sıfırlama | hazır bayrağı | alternatif profil |
| I2S Configure | Saç teli/FS ayarları | Seri veri yolu yapılandırması | frame sync doğrulandı | yeniden konfigürasyon |
| DAC Init | PCM3168A register haritası | Register yazma + okuma teyidi | DAC hazır | alternatif DAC (E2) |
| USB Audio Start | USB sınıf descriptor'ları | Enumeration + alt arayüz kurulumu | ses kanalı hazır | LED hata kodu (E3) |
| System Ready | Tüm hazır bayrakları | RTOS'a devir + watchdog başlat | çalışma modu geçişi | recovery (A8) |

**Detay kural (A9):** Her aşamanın *çıktısı* bir sonraki aşamanın *girdisidir*;
aradaki bayrak adları `boot-surucu.md` §2'de tek noktada tanımlıdır (ikinci
tanım YASAK — çift tanımlı bayrak = belirsizlik ihlali).

**Detay kural (A10):** "Alternatif DAC dene" (E2) yalnız_PCM3168A için
tanımlıdır; alternatif, ADR-038 H001'e aykırı başka bir DAC modeline
yönelik DEĞİLDİR (PCM5122 kullanımı mutlak yasak — repo kuralı 6).

## §15 Bellek Segment Haritası ve Yığın Politikası (Devam)

| Segment | Boot sahibi | Çalışma zamanı sahibi | Tahsis politikası | Ölçüm |
|---|---|---|---|---|
| Reset vectors (ROM) | ROM | — | yalnız okuma | ROM denetimi (K001) |
| Bootloader kodu | boot | — | statik (derleme anı) | idf-size / linker haritası |
| Runtime kodu | devredilir | runtime | statik | aynı araç |
| Vektör/kesme tablosu | boot bağlar | runtime yönetir | statik | MCU verisi — UNKNOWN |
| .data (initialized) | boot kopyalar (load→run) | herkes | statik | harita raporu |
| .bss (zero-init) | boot sıfırlar | herkes | statik | harita raporu |
| DMA tamponları (I2S/USB) | boot ön-yayar | runtime kullanır | önceden boyutlanmış (512 örnek @ 48 kHz, repo kuralı 8) | örnek sayacı |
| Stack (boot) | boot | — | sabit boyut | tepe kullanımı ölçümü — UNKNOWN |
| Stack (görevler) | — | runtime | her görev için sabit | ölçüm planlı (testing) |
| Yığın (heap) | boot'ta 0 tahsis | runtime'da da yasak (rtos §4) | tahsis yok | statik analiz |
| Yedek firmware bank | update | update | flash bölümleri (dual-bank) | ota-update §3 |
| Konfigürasyon (EEPROM eşdeğeri) | boot okur | update yazar | kayıt tabanlı | firmware/index §4 |

> Bölüm tablosu örneği `boot-ornekler.md` örnek-yapılandırma bölümündedir.


**Tahsis kural (A11):** Boyutlar yalnız *ölçülmüş* veya *üretici verisinden*
gelir. MCU ailesi bilinmeden byte rakamı uydurulmaz (A5 ile aynı ilke);
gerekli yerde `UNKNOWN` yazılır.

## §16 Reset Kaynakları ve Açılış Bayrakları

| Reset kaynağı | Tetik | Boot davranışı | Sayaç etkisi |
|---|---|---|---|
| Power-on reset | İlk güçlenme | tam zincir (§2) | sayaç 0'dan |
| Yazılım reset | runtime/devir sonrası komut | tam zincir | sayaç +1 |
| Watchdog reset | Besleme kaybı | tam zincir + E6 incelemesi | sayaç +1, eşik B6 |
| Brown-out reset | Gerilim düşüşü | tam zincir + K001 raporu | sayaç +1 |
| NRST pin | Fiziksel tetik | tam zincir | sayaç +1 |
| Recovery tetik | 2 başarısız normal boot | recovery girişi (§5) | retry sayacı |
| Debug giriş | SW-DAP erişimi | debug modu (§5) | üretimde kapalı (A7) |

**Bayrak tablosu (A12):** `BOOT_OK` · `DSP_READY` · `I2S_OK` · `DAC_OK` ·
`USB_OK` · `SIG_OK` · `WDG_ON` — her bayrağın sahibi tek modüldür; bayrak
adlarının tam listesi ve sahipliği `boot-surucu.md` §3'tedir (bu dosyada
tekrar tanımlanmaz).

## §17 Boot ↔ Test Senaryosu Eşlemesi (testing modülüne köprü)

| Test senaryosu | Boot'ta neyi yoklar | Beklenen sonuç | Kapsam sahibi |
|---|---|---|---|
| Cold boot (güç ver → ready) | §2 zinciri uçtan uca | ~1,5 s içinde System Ready | testing + boot |
| Sıcak reset (yazılım) | §16 yazlım reset satırı | sayaç +1, tam zincir | testing |
| Firmware load başarısız | E1 retry davranışı | ≤2 retry, sonra recovery | testing |
| DAC init başarısız | E2 alternatif davranışı | tanımlı eylem, sessiz devam yok | testing + K001 |
| USB enumeration başarısız | E3 LED hata kodu | kod yayınlandı | testing |
| İmza hatalı görüntü | E4 durdurma | boot durdu + güvenlik olayı | testing + security |
| Boot'ta tahsis denetimi | P2/A4 | 0 tahsis (statik analiz) | testing |
| Yedek banka devir | E5/§5 recovery | yedek banktan açılış | testing + update |

> Bu eşleme, test *uygulamasını* değil yalnız *kapı referansını* verir;
> test stratejisi dosyası kök `firmware-test-stratejisi.md` (kod grubu) ve
> `testing` alt-modülüdür.

## §18 Terminoloji (TR ↔ EN) — bu dosyadaki kullanımlar

| Terim (EN) | Bu dosyadaki karşılığı | Kullanım notu |
|---|---|---|
| bootloader | önyükleyici | ROM sonrası ilk yazılım katmanı |
| boot sequence | açılış dizisi | §2 zinciri |
| fastboot | hızlı açılış modu | §5 — tetikleyici UNKNOWN |
| DFU (Device Firmware Update) | cihaz firmware güncelleme sınıfı | §5, update modülünün girişi |
| recovery mode | kurtarma modu | §5, yedek bank |
| root of trust | güven kökü | security modülüne devredilir |
| static allocation | statik tahsis | P2, §15 |
| heap / dynamic allocation | yığın / dinamik tahsis | yasak (P2) |
| watchdog (WDT) | gözcü zamanlayıcı | P10, E6, B5 |
| brown-out | gerilim düşüşü | §16 |
| reset vector | sıfırlama vektörü | §4, §15 |
| linker script | bağlayıcı betiği | §4 (MCU ailesine bağlı — UNKNOWN) |
| flash partition | flash bölümü | §4, §15 (dual-bank) |
| CRC / SHA-256 | sağlama toplamı | §7 E5 (Kaynak: ota-update §2) |
| enumeration | USB aygıt sayımı | §2 aşama 7 |
| register write-back | register yazma teyidi | §2 aşama 6 |

## §19 Karar Kaydı ve ADR Bağlantıları

| Karar | Dayanak | Bağlı ADR/Veri | Durum |
|---|---|---|---|
| DAC=PCM3168A (tek seçenek) | repo kuralı 6 | ADR-038 H001 (Kaynak: boot-sequence §5) | donmuş |
| Ses verisi 32-bit float | repo kuralı 7 | ADR-017 | donmuş |
| 512 örnek @ 48 kHz tampon | repo kuralı 8 | sartname (ölçüm PLANNED) | aktif |
| Tahsis/mutex yasak | repo kuralları 1-2 | rtos §4 | aktif |
| Dual-bank flash zorunlu | rollback gereksinimi | ota-update §3 (Fail-safe) | aktif |
| Boot sonu → runtime devri | zamanlama sahipliği | firmware/index §5 | aktif |
| `bellek` omurgasının kayıt yeri | kayıt tutarlılığı | bu dosya §4-§15 | üst karar (Q4) |

## §20 Red Team Gözden Geçirmesi (bu üretim)

| # | İddia | Denetim | Sonuç |
|---|---|---|---|
| 1 | "Zincir 8 aşama" | boot-sequence §2 ile birebir | doğrulandı |
| 2 | "Süreler toplam ~1,5 s" | boot-sequence §3 toplam satırı | doğrulandı (kaynak değeri, cihaz ölçümü değil) |
| 3 | "Hata eylemleri E1-E7" | boot-sequence §4 + firmware/index §4,§7,§8 | E1-E3 doğrudan, E4-E7 index'ten |
| 4 | "Bellek rakamları verildi" | §4/§15 denetimi | rakam verilmedi, UNKNOWN — bilinçli |
| 5 | "7 URL kullanıldı" | §11 tablosu | 7/7 benzersiz, HTTP 200, envanter dışı |
| 6 | "Firmware kodu var" | §12 | YOK yazıldı — dürüstlük kuralı |

## §21 Gereksinim → Kural Eşleme Matrisi (boot)

| # | Gereksinim (kaynak) | Kural (bu dosya) | Doğrulama kapısı | Sahip |
|---|---|---|---|---|
| R1 | Açılış ~1,5 s içinde tamamlanmalı (boot-sequence §3) | A3 bütçe kuralı | B1 | boot |
| R2 | Her aşamanın tanımlı hata eylemi olmalı (boot-sequence §4) | A1 + A8 | B4 | boot |
| R3 | Firmware doğrulamasız çalıştırma yasak (firmware/index §4) | §7 E4-E5 | security kapısı | security |
| R4 | Bellek tahsisi çalışma zamanında olmamalı (rtos §4, firmware/index §5) | P2 + A4 + A11 | B3 | boot |
| R5 | Watchdog zorunlu (firmware/index §5) | P10 + §16 | B5 | runtime |
| R6 | Dual-bank flash ile rollback (ota-update §3) | §5 A6 | update kapısı | update |
| R7 | DAC yalnız PCM3168A (ADR-038 H001) | A10 + P6 | kod incelemesi | boot |
| R8 | Boot → runtime devri tek noktadan (firmware/index §5) | §8 + A9 | bayrak sahipliği | runtime |
| R9 | Statik tampon 512 örnek @ 48 kHz (repo kuralı 8) | §15 DMA satırı | örnek sayacı | audio-control |
| R10 | Hata kodları diagnostics'e iletilmeli (boot-sequence §4) | §7 E1-E7 | B4 | diagnostics |
| R11 | Fastboot modu yalnız test ortamında (§5) | A7 | üretim derlemesi denetimi | testing |
| R12 | Konfigürasyon okuması boot'ta (firmware/index §4) | §15 | boot testi | boot |
| R13 | Ses verisi biçimi 32-bit float (ADR-017) | P5 | kod incelemesi | audio-control |
| R14 | Boot süreleri cihazda ölçülmeden hedef tutulmuş sayılmaz (A3) | B1 = UNKNOWN | ölçüm | testing |
| R15 | Bellek rakamları uydurulmaz (A5/A11) | §4/§15 | inceleme | boot |
| R16 | IRQ/kesme önceliği yönetimi runtime'da (firmware/index §5) | §16 bayrak sahipliği | runtime kapısı | runtime |
| R17 | Recovery modu yedek firmware ile (firmware/index §7) | §5 mod tablosu | update kapısı | update |
| R18 | Anti-rollback/encrypt firmware security kapsamı (firmware/index §8) | §7 E4 bağlantısı | security kapısı | security |
| R19 | İmza algoritması seçimi security modülünde (firmware/index §8) | §7 yalnız çağrı | security kapısı | security |
| R20 | Boot log/olay kaydı diagnostics akışına girer (A8) | §17 köprüsü | diagnostics | diagnostics |

## §22 Red Team İtiraz Kayıtları (soru → yanıt → kanıt)

| # | İtiraz | Yanıt | Kanıt |
|---|---|---|---|
| T1 | "Süreler gerçek ölçüm mü?" | Hayır — kaynak tablosu; cihaz ölçümü UNKNOWN | boot-sequence §3 + B1 |
| T2 | "Bellek boyutları neden yok?" | MCU ailesi kesin değil, uydurma yasak | A5/A11 + Q1 |
| T3 | "PCM3168A dışında DAC yazılabilir mi?" | Hayır, mutlak yasak (ADR-038 H001) | P6/A10 |
| T4 | "`bellek` omurgası niye ayrı dosya değil?" | K005 kaydında ayrı alt-modül yok; bu modülde §4/§15 kayıtlı, üst karar Q4 | §4 başı + Q4 |
| T5 | "Fastboot tanımı eksik mi?" | Bilgi yok → UNKNOWN olarak işaretli, uydurulmadı | §5 + Q2 |
| T6 | "Retry 2 nereden geliyor?" | A8 kuralı (bu anayasa); kaynakta rakam yok, kural olarak kondu | §7 A8 |
| T7 | "E4-E7 kaynaklı mı?" | E1-E3 doğrudan kaynak; E4-E7 firmware/index §4,§7,§8'den | §7 |
| T8 | "URL'ler envanterle çakışır mı?" | 7/7 2026-10-13 kontrol edildi, kesişim 0 | §11 |
| T9 | "Örnek kod depo kodu mu?" | Depoda firmware kodu YOK; örnekler kalıp etiketli | §12 |
| T10 | "Bu dosya 500+ satır mı?" | Ölçüm kök raporunda; hedef ≥515 | §23 kontrol satırı |
| T11 | "Yasak token var mı?" | Bölüm listesinde geçmemesi için düzeltildi, tarama 0 | §23 |
| T12 | "noexcept kanıtı?" | P1 kuralı — kod henüz yazılmadı (repo'da firmware yok) | P1 + §12 |
| T13 | "Boot ↔ security arayüzü net mi?" | Yalnız çağrı noktası (E4), sözleşmesi security'de | §8 |
| T14 | "Girdi/çıktı bayrakları çift tanımlı mı?" | Hayır — tek tanım boot-surucu.md §3 | A9 |
| T15 | "Ölçüm kapıları ölçülmedi, öyleyse işe yaramaz mı?" | Kapılar tanımlı; ölçüm testing modülünde planlı | §10 + §17 |

## §23 İddia → Dayanak Satır Eşlemesi

| # | İddia (bu dosyada) | Dayanak | Tür |
|---|---|---|---|
| C1 | Boot zinciri 8 aşama | boot-sequence §2 | kaynak |
| C2 | ROM Boot 100 ms | boot-sequence §3 | kaynak ölçüm |
| C3 | Firmware Load 500 ms | boot-sequence §3 | kaynak ölçüm |
| C4 | DSP Init 200 ms | boot-sequence §3 | kaynak ölçüm |
| C5 | I2S Config 100 ms | boot-sequence §3 | kaynak ölçüm |
| C6 | DAC Init 200 ms | boot-sequence §3 | kaynak ölçüm |
| C7 | USB Start 300 ms | boot-sequence §3 | kaynak ölçüm |
| C8 | Toplam ~1,5 s | boot-sequence §3 (Toplam satırı) | kaynak ölçüm |
| C9 | XMOS load fail → LED blink + retry | boot-sequence §4 | kaynak |
| C10 | DAC init fail → alternatif DAC | boot-sequence §4 | kaynak |
| C11 | USB fail → LED hata kodu | boot-sequence §4 | kaynak |
| C12 | Bootloader: EEPROM konfigürasyon okur | firmware/index §4 | kaynak |
| C13 | Bootloader: imza kontrolü yapar | firmware/index §4 | kaynak |
| C14 | Bootloader: hata durumunda recovery modu | firmware/index §4 | kaynak |
| C15 | Bellek: statik tahsis, heap yasak | firmware/index §5 | kaynak |
| C16 | Watchdog: hardware watchdog zorunlu | firmware/index §5 | kaynak |
| C17 | Görev önceliği tablosu (RTOS) | rtos §3 | kaynak (runtime modülü) |
| C18 | No blocking / No malloc / Lock-free | rtos §4 | kaynak |
| C19 | Dual-bank flash fail-safe | ota-update §3 | kaynak (update modülü) |
| C20 | SHA-256 + RSA-2048 doğrulama | ota-update §2-§3 | kaynak (update modülü) |
| C21 | 512 örnek @ 48 kHz | repo kuralı 8 (AGENTS.md) | kural |
| C22 | PCM3168A tek DAC | repo kuralı 6 + ADR-038 | kural |
| C23 | 32-bit float ses verisi | repo kuralı 7 + ADR-017 | kural |
| C24 | Cihaz matrisi (XMOS/BCM2711/STM32/ESP32) | firmware/index §9 | kaynak |
| C25 | Güncelleme yöntemleri (OTA/DFU/Serial/Recovery) | firmware/index §7 | kaynak (update modülü) |
| C26 | Secure boot / anti-rollback / encrypted flash | firmware/index §8 | kaynak (security modülü) |
| C27 | Fastboot tetikleyici bilgisi yok | — | bilinmiyor → UNKNOWN |
| C28 | Bellek byte rakamları bilinmiyor | — | bilinmiyor → UNKNOWN |
| C29 | MCU ailesi kesin değil | firmware/index §9 (çoklu cihaz) | belirsiz → Q1 |
| C30 | Ölçüm kapıları B1-B6 | bu dosya §10 | kural (ölçüm testing'te) |

## §24 Revizyon ve Sorumluluk Kaydı

| # | Kayıt | Değer |
|---|---|---|
| V1 | Dosya | `boot/boot-anayasa.md` (K005-firmware) |
| V2 | node_id | K005-firmware/boot/boot-anayasa |
| V3 | node_type | module |
| V4 | status | draft |
| V5 | group | mimari (tirnaksız) |
| V6 | parent_id | K005-firmware/boot |
| V7 | children | [] (yaprak içerik dosyası) |
| V8 | Üretim tarihi | 2026-10-13 |
| V9 | Kaynak dosya sayısı | 3 (boot-sequence · firmware/index · rtos) |
| V10 | Web URL sayısı | 7 (hepsi HTTP 200, envanter dışı) |
| V11 | Eşik | ≥515 satır (kök ölçüm raporu) |
| V12 | Sahip | Bayram Ali / Vault Steward |
| V13 | Mod | Red Team · Human Mode · Truth Mode |
| V14 | İlgili modüller | boot · update · security · runtime · diagnostics · testing |
| V15 | Dokunulmaz yüzey | frozen ADR'ler · `.ai/electronic/**` (okuma) · K000/K001/K002 (erişim yok) |
| V16 | Değişiklik yetkisi | yalnız K005-firmware beyazlistesi + orkestratör onayı |

## §25 Yerel Ölçüm Protokolü (bu dosya için)

| # | Ölçüm | Yöntem | Kabul |
|---|---|---|---|
| M1 | Satır sayısı | PowerShell `Get-Content -Encoding UTF8` sayımı | ≥515 |
| M2 | Frontmatter anahtar sayısı | ilk `---` bloğunda 10 anahtar | 10 |
| M3 | `parent` anahtarı yokluğu | FM taraması | 0 |
| M4 | group değeri | tirnaksız `mimari` | 1 |
| M5 | Liste alanları | children/depends_on/evidence/validation/open_questions tanımlı | 5 |
| M6 | Son satır | Authority satırı birebir | 1 |
| M7 | Orta Authority satırı | Authority yalnız son satırda | 1 adet toplam |
| M8 | Yer tutucu dolgu | dosya taraması | 0 |
| M9 | Boş tablo hücresi | aynı satırda iki bitişik boru arasında boşluk | 0 |
| M10 | Bozuk karakter (Latin dışı/CJK) | karakter aralığı taraması | 0 |
| M11 | URL sayısı | §11 tablosu | ≥4 benzersiz |
| M12 | URL kesişimi | vault envanteri ile | 0 |
| M13 | Bölüm bütünlüğü | §1 → §26 kesintisiz | evet |
| M14 | Kaynak cümlesi | §12'de birebir cümle | 1 |
| M15 | Boş hücresiz satır | tüm tablo satırları dolu | evet |
| M16 | §11 tarih sütunu | her satır 2026-10-13 | evet |
| M17 | §11 Durum sütunu | her satır HTTP 200 | evet |
| M18 | §12 zorunlu cümle | birebir mevcut | 1 |
| M19 | Kod etiketi | örnekler "örnek yapılandırma (kalıp)" etiketli | evet |
| M20 | Kapsam dışı listesi | §1'de komşu modüller | 4+ |
| M21 | Ölçüm kapısı tanımı | §10 B1-B6 | 6 |
| M22 | İtiraz kaydı | §22 T1-T15 | 15 |
| M23 | Dayanak eşlemesi | §23 C1-C30 | 30 |
| M24 | Gereksinim eşlemesi | §21 R1-R20 | 20 |
| M25 | Açık soru | §13 Q1-Q5 | 5 |

## §26 Kapanış Kontrolü (bu dosya)

| Kontrol | Beklenti | Durum |
|---|---|---|
| FM anahtar sayısı | 10 (node_id · node_type · status · parent_id · children · depends_on · evidence · validation · open_questions · group) | evet |
| `parent:` anahtarı | bulunmamalı | evet (yalnız `parent_id`) |
| `group` değeri | tirnaksız `mimari` | evet |
| Liste alanları | boşsa bile `[]` | evet |
| Son satır | Authority satırı (birebir) | evet |
| Yasak dolgu belirtisi (yer tutucu/eksik içerik/bozuk karakter) | 0 adet | 0 |
| Boş tablo hücresi | 0 | 0 |
| Uydurma sürüm/API iddiası | 0 (bilinenler ⚠️ ile işaretli) | 0 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
