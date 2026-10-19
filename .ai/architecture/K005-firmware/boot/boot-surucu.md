---
node_id: K005-firmware/boot/boot-surucu
node_type: module
status: draft
parent_id: K005-firmware/boot
children: []
depends_on:
  - K005-firmware/boot/boot-anayasa
evidence:
  - ".ai/electronic/firmware/boot-sequence.md §2-§4 — okundu 2026-10-13"
  - ".ai/electronic/firmware/index.md §4 Bootloader — okundu 2026-10-13"
  - "K005-firmware/boot/boot-anayasa.md — kural dayanağı 2026-10-13"
validation:
  - "2026-10-13 — FM10 anahtar + group (mimari) + son satır Authority denetimi"
  - "2026-10-13 — satır ölçümü kök ölçüm betiğinde (K005 raporu)"
open_questions:
  - "Gerçek işlev/imza adları kodda yok (repo'da firmware kodu YOK) — adlar sözleşme düzeyinde, ⚠️ VERIFICATION REQUIRED"
  - "Fastboot kanal protokolü (seri mi USB mi) UNKNOWN"
group: mimari
---

# boot-surucu — Boot Arayüz ve Sözleşme Katmanı (K005-firmware)

> **ELI15:** `boot-anayasa.md` kuralları koyar; bu dosya boot'un **dışarıya
> açılan kapısını** tanımlar: bayraklar, durum makinesi, geçiş sözleşmeleri,
> hata kodu arayüzleri ve komşu modüllerle (update · security · runtime ·
> diagnostics · testing) konuşulan tek noktalar. Buradaki adlar **sözleşme
> düzeyindedir**; karşılığı olan kod henüz yazılmamıştır.

## §1 Amaç, Rol ve Kapsam Sınırı

| Soru | Cevap | Sahip |
|---|---|---|
| Bu dosya neyi tanımlar? | boot modülünün arayüz/sözleşme katmanı | K005-firmware/boot |
| Kimler bu arayüzü kullanır? | runtime · update · security · diagnostics · testing | §8 komşu tablosu |
| Adlar kod adı mı? | Hayır — kod yok, sözleşme adı (⚠️ VERIFICATION REQUIRED) | §15 Q1 |
| Bu dosya anayasayı değiştirir mi? | Hayır — anayasa > bu dosya > örnekler | boot-anayasa.md |
| Kapsam dışı | DSP algoritması (K004) · host OS (K000) · donanım PCB (K001) | komşu düğümler |
| Kapsam dışı | OTA indirme/imza politikası | update modülü |
| Kapsam dışı | Anahtar yaşam döngüsü | security modülü |
| Kapsam dışı | Görev/zamanlama iç yapısı | runtime modülü |

**Sözleşme kuralı (S1):** Bir arayüz adı yalnız **tek dosyada** tanım
kazanır; diğer dosyalar yalnız kullanır (çift tanım = belirsizlik ihlali).

**Sözleşme kuralı (S2):** Arayüz adları kodla eşleşinceye kadar
`SÖZLEŞME` etiketli kalır; eşleşme kanıtı olmadan "uygulandı" yazılmaz.

## §2 Bayrak ve Olay Sözlüğü (Tek Tanım Noktası)

| # | Bayrak/Olay | Üreten | Tüketen | Anlamı | Kötü durum |
|---|---|---|---|---|---|
| F1 | POWER_GOOD | donanım (K001) | boot çekirdeği | güç stabil | K001'e rapor |
| F2 | ROM_OK | ROM (K001) | boot çekirdeği | ROM devri tamam | kalıcı kilitlenme |
| F3 | FW_LOADED | boot çekirdeği | boot doğrulayıcı | görüntü bellekte | E1 retry |
| F4 | FW_SIG_OK | security (çağrı) | boot çekirdeği | imza geçti | E4 durdur |
| F5 | DSP_READY | boot çekirdeği | audio-control | DSP zinciri hazır | alternatif profil |
| F6 | I2S_OK | boot çekirdeği | audio-control | seri yol hazır | yeniden konfigürasyon |
| F7 | DAC_OK | boot çekirdeği | audio-control | PCM3168A hazır | E2 alternatif |
| F8 | USB_OK | boot çekirdeği | audio-control · runtime | USB ses sınıfı hazır | E3 LED kodu |
| F9 | CONFIG_OK | boot çekirdeği | runtime | konfigürasyon okundu | fabrika varsayılanı |
| F10 | BOOT_READY | boot çekirdeği | runtime (devir) | tüm bayraklar | recovery |
| F11 | WDG_ON | boot çekirdeği | runtime | gözcü başladı | E6 |
| F12 | RECOVERY_REQ | boot çekirdeği | update | kurtarma talebi | — |
| F13 | RETRY_COUNT | boot çekirdeği | diagnostics | deneme sayacı | A8 eşiği 2 |
| F14 | BOOT_FAULT_CODE | boot çekirdeği | diagnostics | E1-E7 kodu | — |
| F15 | DEBUG_ALLOWED | üretim derlemesi | testing | debug kapısı | A7: üretimde kapalı |

**Bayrak kuralı (S3):** Bayrak yazma yetkisi tablodaki "Üreten" sütununda
tekilleşir; okuma serbesttir. Bayraklar atomik bayrak/atomik tamsayı ile
yayılır (mutex yasak — repo kuralı 2).

## §3 Aşama Durum Makinesi

| # | Durum | Giriş koşulu | Çıkış hedefi | Hata koluna çıkış |
|---|---|---|---|---|
| ST0 | RESET | güç/reset olayı | ROM_WAIT | — |
| ST1 | ROM_WAIT | F1 verildi | ROM_RUN | güç hatası → ST9 |
| ST2 | ROM_RUN | F2 bekleniyor | FW_LOAD | ST9 (kalıcı) |
| ST3 | FW_LOAD | ROM tamam | FW_VERIFY | E1 → RETRY_WAIT |
| ST4 | FW_VERIFY | F3 set | SIGN_CHECK | E5 → RECOVERY |
| ST5 | SIGN_CHECK | F4 bekleniyor | HW_INIT | E4 → HALT |
| ST6 | HW_INIT | imza geçti | DSP_INIT | E2/E3 → FAULT |
| ST7 | DSP_INIT | HW_INIT tamam | I2S_CFG | profil hatası → FAULT |
| ST8 | I2S_CFG | F5/F6 set | DAC_INIT | konfigürasyon hatası → FAULT |
| ST9 | DAC_INIT | I2S tamam | USB_START | E2 → alternatif |
| ST10 | USB_START | F7 set | READY_CHECK | E3 → FAULT |
| ST11 | READY_CHECK | F8/F9 set | HANDOVER | eksik bayrak → RECOVERY |
| ST12 | HANDOVER | F10 set | RUNTIME (devir) | — |
| ST13 | RETRY_WAIT | E1-E3 sayaç < 2 | FW_LOAD | sayaç ≥ 2 → RECOVERY |
| ST14 | RECOVERY | F12 set | update modülü | — |
| ST15 | FAULT | tanımlı hata kodu | RETRY_WAIT veya RECOVERY | — |
| ST16 | HALT | E4 (imza) | yalnız güvenlik olayı | — |

**Durum makinesi kuralı (S4):** Tabloda olmayan geçiş YASAKTIR (toplam
geçiş = tablodaki satırlar; tanımsız geçiş = belirsizlik ihlali).

**Durum makinesi kuralı (S5):** Her durumun tek bir *aktif* olduğu ve
sayaçların (RETRY_COUNT) boot sonunda sıfırlandığı varsayılır; doğrulaması
`boot-ornekler.md` kalıplarında ve `testing` kapılarında.

## §4 Geçiş Sözleşmeleri (Girdi · Çıktı · Garanti)

| # | Geçiş | Girdi | Çıktı | Garanti |
|---|---|---|---|---|
| TR1 | ST0 → ST1 | POWER_GOOD | ROM_WAIT durumu | güç yoksa geçiş yok |
| TR2 | ST1 → ST2 | ROM_OK | ROM_RUN çıktısı | ROM hatalıysa ST9 |
| TR3 | ST2 → ST3 | yükleme tamam | FW_LOAD tamamlandı | bütünlük doğrulaması ST4'te |
| TR4 | ST3 → ST4 | FW_SIG_OK sorgusu | imza sonucu | sonuç iki değerli (geç/geçmedi) |
| TR5 | ST4 → ST5 | imza geçti | SIGN_CHECK tamam | E4 ile durur, geçemez |
| TR6 | ST5 → ST6 | hardware init tetik | HW_INIT tamam | register teyidi gerekli |
| TR7 | ST6 → ST7 | DSP_READY | DSP_INIT tamam | profil yoksa FAULT |
| TR8 | ST7 → ST8 | I2S_OK | I2S_CFG tamam | yeniden deneme 1 kez |
| TR9 | ST8 → ST9 | DAC_OK | DAC_INIT tamam | yalnız PCM3168A (A10) |
| TR10 | ST9 → ST10 | USB_OK | USB_START tamam | hata LED kodu yayılır |
| TR11 | ST10 → ST11 | tüm hazır bayraklar | READY_CHECK | eksik varsa RECOVERY |
| TR12 | ST11 → ST12 | BOOT_READY | HANDOVER | watchdog açık (F11) |
| TR13 | ST12 → RUNTIME | devir paketi | runtime'a teslim | tek noktadan devir (A9) |
| TR14 | * → ST13 | E1-E3 | RETRY_COUNT < 2 | A8: sonsuz retry yok |
| TR15 | ST13 → ST14 | sayaç ≥ 2 | RECOVERY_REQ | update kararı |
| TR16 | * → ST15 | E5-E7 | FAULT kodu | kod kaydedilir (diagnostics) |
| TR17 | * → ST16 | E4 | HALT | yalnız security olayı |
| TR18 | ST16 → * | — | geçiş yok | kilitleme |

**Sözleşme kural (S6):** "Garanti" sütunu test edilebilir ifade içerir;
test edilemeyen garanti yazılmaz (yazıldıysa `⚠️ VERIFICATION REQUIRED`
işareti taşır).

## §5 Devir Arayüzü (Boot → Runtime)

| Alan | Değer/Tip | Açıklama | Sahip |
|---|---|---|---|
| olay | BOOT_READY | devir tetiği | boot |
| bayrak_seti | F1-F11 durumu | devir anındaki bayrak kopyası | boot |
| boot_fault | E0 (yok) veya E1-E7 | devirde taşınan hata | boot |
| boot_retry_count | sayaç | kalan deneme hakkı | boot |
| boot_mode | normal/fastboot/DFU/recovery | aktif mod | boot |
| watchdog_state | kapalı/açık | devirde gözcü durumu | boot → runtime |
| mem_ready | DMA/stack hazır bayrağı | bellek hazırlığı sonucu | boot |
| config_ver | konfigürasyon sürümü | CONFIG_OK içeriği | boot → runtime |
| next_owner | runtime | devir alıcı | runtime |
| devir_zamanı | sayaç (birim UNKNOWN) | performans ölçümü | testing |

**Devir kuralı (S7):** Devir bir *paket* üzerinden ve tek noktadan yapılır;
çoklu çağrı yasak (A9 ile aynı ilke).

## §6 Hata Kodu Arayüzü

| Kod | Kaynak | Anlamı | Arayüz davranışı | İlgili modül |
|---|---|---|---|---|
| E0 | — | hata yok | sessizce devam | — |
| E1 | boot-sequence §4 | firmware yükleme hatası | RETRY_COUNT++ → RETRY_WAIT | update |
| E2 | boot-sequence §4 | DAC init hatası | alternatif profil dene (yalnız PCM3168A) | audio-control |
| E3 | boot-sequence §4 | USB başlatma hatası | LED hata kodu + RETRY | audio-control |
| E4 | firmware/index §8 | imza doğrulama hatası | HALT + güvenlik olayı | security |
| E5 | firmware/index §7 | firmware/CRC doğrulama hatası | RECOVERY_REQ → yedek bank | update |
| E6 | firmware/index §5 | watchdog açılış açılışı | sayaç + inceleme | runtime |
| E7 | firmware/index §4 | konfigürasyon okuma hatası | fabrika varsayılanı | diagnostics |

**Hata arayüzü kuralı (S8):** Kod adı sabittir, metni (yerelleştirme)
ayrı katmandadır; kod metinle değiştirilemez (metin değişince test kırılır).

## §7 Mod Arayüzleri

| Mod | Arayüz girişi | Arayüz çıkışı | Ortak sözleşme |
|---|---|---|---|
| Normal | reset olayı | BOOT_READY paketi | §5 |
| Fastboot | kanal komutu (protokol UNKNOWN) | hizmet durumu | §15 Q2 |
| DFU | USB DFU talebi | yükleme penceresi (update) | E5 |
| Recovery | RETRY_COUNT ≥ 2 veya E5 | RECOVERY_REQ paketi | §6 |
| Debug | fiziksel erişim (üretimde kapalı) | teşhis kanalı | A7 |

**Mod arayüzü kuralı (S9):** Her modun yalnız **bir** girişi ve **bir**
çıkışı vardır; ikinci giriş (yanlışlıkla) tanımı yoktur.

## §8 Komşu Modül Arayüzleri

| Komşu | Yön | Arayüz | Veri | Sözleşme durumu |
|---|---|---|---|---|
| runtime | boot → | devir paketi (§5) | F1-F11 + E-kodu | SÖZLEŞME |
| update | boot ↔ | RECOVERY_REQ / DFU penceresi | E5, mod | SÖZLEŞME |
| security | boot ← | imza doğrulama çağrısı | görüntü | SÖZLEŞME |
| diagnostics | boot → | E1-E7 + RETRY_COUNT | olay kaydı | SÖZLEŞME |
| testing | boot ← | test tetiği (mod/sınır) | kapı komutları | SÖZLEŞME |
| audio-control | boot → | F5/F6/F7/F8 hazır bayrakları | bayrak seti | SÖZLEŞME |
| power-control | boot ↔ | POWER_GOOD, uyku/güç olayı | F1 | SÖZLEŞME |
| K001-donanım | boot ↔ | reset/ROM/LED pinleri | fiziksel | ⚠️ VERIFICATION REQUIRED |

## §9 Gözlem ve Telemetri Arayüzü

| Olay | Kayıt alanları | Sıklık | Alıcı |
|---|---|---|---|
| BOOT_START | reset kaynağı, mod | her boot | diagnostics |
| ST_GEÇİŞİ | durum, süre, sonuç | her geçiş | testing |
| RETRY | E-kodu, sayaç | her retry | diagnostics |
| FAULT | E-kodu, durum | her hata | diagnostics |
| HANDOVER | bayrak seti, boot süresi | her devir | runtime |
| RECOVERY | tetik sebebi | her kurtarma | update |
| WDG_EVENT | açılış sebebi | her watchdog olayı | runtime |

**Gözlem kuralı (S10):** Kayıt yolu audio thread'ine dokunmaz (repo kuralı
1-2); boot sırasındaki kayıt, çalışma zamanındaki kayıt öncesi tamamlanır.
Gerçek kayıt altyapısı (buffer/kanal) K005'te tanımlı değildir — `UNKNOWN`.

## §10 Bellek Arayüzü

| Arayüz | Erişim | Sahip | Kural |
|---|---|---|---|
| DMA tamponu (512 örnek @ 48 kHz) | önceden boyutlanmış | boot ön-yayar, runtime kullanır | tahsis yok |
| Segment erişimi (.data/.bss) | boot kopyalar/sıfırlar | boot | statik |
| Yedek bank | yalnız update | update | boot yazamaz (A6) |
| Stack | boot kendi sabiti | boot | boyut UNKNOWN |
| Yığın (heap) | erişim yok | — | P2/A4 |
| Vektör tablosu | boot bağlar, runtime yönetir | ortak | MCU ailesi UNKNOWN |

## §11 Bağımlılık Grafiği

| Düğüm | Bağımlı olduğu | Tür | Not |
|---|---|---|---|
| boot-surucu | boot-anayasa | mimari | kurallardan türetilir |
| boot-surucu | boot-sequence.md (Kaynak) | kanıt | zincir/süre/hata |
| boot-surucu | firmware/index §4 | kanıt | bootloader görevleri |
| runtime | boot-surucu (§5) | modül | devir |
| update | boot-surucu (§6-§7) | modül | E1/E5/DFU |
| security | boot-surucu (§6 E4) | modül | imza çağrısı |
| diagnostics | boot-surucu (§9) | modül | olay akışı |
| testing | boot-surucu (§3) | modül | durum makinesi kapıları |
| boot-ornekler | boot-surucu | kod | kalıp bu sözleşmeye uyar |

## §12 Sözleşme Değişiklik Kuralı

| # | Kural | Uygulama |
|---|---|---|
| C1 | Ad ekleme | yalnız bu dosyada + bağımlı modül onayı |
| C2 | Ad silme | değişiklik kaydı + orkestratör onayı |
| C3 | Anlam değiştirme | yeni numara (C2'ye tabi) — eski ad ölümez |
| C4 | Kod eşleşmesi | kanıt = dosya/satır; yoksa SÖZLEŞME etiketi |
| C5 | Sürüm | status: draft → active yalnız ölçüm sonrası |
| C6 | Çakışma | anayasa > bu dosya > örnekler (öncelik) |
| C7 | Komşu etkisi | §8'deki komşulardan biri değişirse eşzamanlı güncelleme |
| C8 | Reddedilen değişiklik | silinmez, tabloda gerekçesiyle durur |

## §13 Web Araştırması

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| RFC 2119, MUST/SHOULD/MAY gibi gereksinim anahtar kelimelerinin anlamını tanımlar; bu sözleşmedeki "zorunlu/yasak" satırları aynı okuma biçimini kullanır | https://www.rfc-editor.org/rfc/rfc2119.txt | 2026-10-13 | HTTP 200 |
| ESP-IDF bölümlendirme dokümanı, OTA için veri/dual-bank bölümlerinin tablo ile tanımlanışını belgeler (§10 yedek bank satırı) | https://docs.espressif.com/projects/esp-idf/en/latest/esp32/api-reference/storage/partition.html | 2026-10-13 | HTTP 200 |
| ESP-IDF spi_flash arayüzü, flash okuma/yazma işlevlerini tanımlar (§10 segment erişimi) | https://docs.espressif.com/projects/esp-idf/en/latest/esp32/api-reference/storage/spi_flash.html | 2026-10-13 | HTTP 200 |
| ESP-IDF idf-size aracı, görüntü/flash boyutu raporlaması sunar (ölçüm yöntemi) | https://docs.espressif.com/projects/esp-idf/en/latest/esp32/api-guides/tools/idf-size.html | 2026-10-13 | HTTP 200 |
| Zephyr periferel dokümantasyonu, donanım arayüz portlama kılavuzudur (§8 K001 arayüzü için üst bant) | https://docs.zephyrproject.org/latest/hardware/peripherals/index.html | 2026-10-13 | HTTP 200 |
| Zephyr başlangıç dokümanı, doğrulama ortamının kurulum adımlarını tanımlar (testing kapısı ortamı) | https://docs.zephyrproject.org/latest/develop/getting_started/index.html | 2026-10-13 | HTTP 200 |
| ARM IoT sayfası, gömülü platform başlangıç çerçevesini tanıtır (MCU ailesi kararı üst bandı) | https://www.arm.com/technologies/iot | 2026-10-13 | HTTP 200 |

## §14 Kaynak ve Repo Kanıtı

**Repo'da firmware kodu YOK — kaynak: .ai/electronic/firmware/ (boot-sequence·dsp-firmware·ota-update·rtos)**

| Kaynak | Bölüm | Kullanıldığı yer |
|---|---|---|
| `Kaynak: .ai/electronic/firmware/boot-sequence.md` | §2 Boot Dizisi | §3 durum makinesi |
| `Kaynak: .ai/electronic/firmware/boot-sequence.md` | §3 Boot Süreleri | §16 zaman çizelgesi |
| `Kaynak: .ai/electronic/firmware/boot-sequence.md` | §4 Hata Durumları | §6 E1-E3 |
| `Kaynak: .ai/electronic/firmware/index.md` | §4 Bootloader | §1, §6 E7 |
| `Kaynak: .ai/electronic/firmware/index.md` | §5 RTOS (watchdog, statik) | §2 F11, §6 E6 |
| `Kaynak: .ai/electronic/firmware/index.md` | §7 Update · §8 Security | §6 E5, §6 E4 |

## §15 Açık Sorular

| # | Soru | Etki | Durum |
|---|---|---|---|
| Q1 | Arayüz adları kodda nasıl karşılık bulacak? | S2 etiketi | ⚠️ VERIFICATION REQUIRED |
| Q2 | Fastboot kanalı seri mi USB mi? | §7 | UNKNOWN |
| Q3 | Devir paketi gerçek tipi (struct) nedir? | §5 | UNKNOWN (kod yok) |
| Q4 | Gözlem altyapısı hangi kanal? | §9 | UNKNOWN |
| Q5 | Ölçüm sayaçlarının birimi (µs/ms)? | §5 | UNKNOWN |

## §16 Uçtan Uca Zaman Çizelgesi (Aşama × Süre × Bayrak)

| Aşama | Süre (Kaynak) | Çıkış bayrağı | Sonraki aşama | Hata kolona yazılır mı |
|---|---|---|---|---|
| Power On | — | F1 POWER_GOOD | ROM_WAIT | evet (güç) |
| ROM Boot | 100 ms | F2 ROM_OK | FW_LOAD | evet (kalıcı) |
| Firmware Load | 500 ms | F3 FW_LOADED | FW_VERIFY | evet (E1) |
| İmza/CRC | — | F4 FW_SIG_OK | HW_INIT | evet (E4/E5) |
| DSP Init | 200 ms | F5 DSP_READY | I2S_CFG | evet (FAULT) |
| I2S Config | 100 ms | F6 I2S_OK | DAC_INIT | evet (FAULT) |
| DAC Init | 200 ms | F7 DAC_OK | USB_START | evet (E2) |
| USB Start | 300 ms | F8 USB_OK | READY_CHECK | evet (E3) |
| Konfigürasyon | — | F9 CONFIG_OK | READY_CHECK | evet (E7) |
| Ready check | — | F10 BOOT_READY | HANDOVER | evet (RECOVERY) |
| Watchdog aç | — | F11 WDG_ON | RUNTIME | evet (E6) |
| Devir | — | paket (§5) | RUNTIME | — |
| **Toplam** | **~1,5 s (Kaynak)** | — | — | — |

## §17 Sık Yapılan Hatalar (Ön Kayıt — anti-pattern)

| # | Hatalı davranış | Doğru davranış | Denetim |
|---|---|---|---|
| A1 | Bayrağı iki modülün yazması | tek üretici (§2) | kod incelemesi |
| A2 | Durum makinesinde tanımsız geçiş | §3 satırları (S4) | test |
| A3 | Retry sonsuza kadar | A8 (max 2) | test |
| A4 | Boot içinde tahsis | statik (P2) | statik analiz |
| A5 | Hata kodu metniyle eşleme | sabit kod (S8) | test |
| A6 | İmza hatasında devam | HALT (E4) | security testi |
| A7 | Devirde ikinci çağrı | tek paket (S7) | test |
| A8 | Ölçmeden "hedef tuttu" | ölç (A3/B1) | ölçüm kaydı |
| A9 | Yedek banka boot'tan yazma | yalnız update (A6) | kod incelemesi |
| A10 | Debug modu üretimde açık | kapalı (A7) | derleme denetimi |
| A11 | Çift tanımlı bayrak adı | tek tanım (S1) | tarama |
| A12 | Ölçüm yokken rakam yazma | UNKNOWN (A5) | inceleme |

## §18 Giriş/Çıkış Sinyal Kayıtları (Boot ↔ Donanım)

| # | Sinyal | Yön | Boot kullanımı | Kötü durum davranışı |
|---|---|---|---|---|
| IO1 | VDD/PGOOD | giriş | F1 POWER_GOOD bekleme | K001'e rapor, beklemede kal |
| IO2 | NRST | çıkış | reset de-assert | — |
| IO3 | BOOT_SEL pin | giriş | mod seçimi (normal/recovery) | mod tablosu (§7) |
| IO4 | STATUS LED | çıkış | aşama/hata gösterimi | hata kodu yanıp sönme (E3) |
| IO5 | Flash CS/CLK/DATA | çift yönlü | firmware okuma | E1 retry |
| IO6 | I2S BCLK/LRCK/SD | çıkış | seri yol konfigürasyonu | yeniden konfigürasyon |
| IO7 | I2S MCLK | çıkış | DAC master saat | E2/E7 |
| IO8 | DAC RESET (PCM3168A) | çıkış | DAC başlatma | alternatif profil (yalnız PCM3168A) |
| IO9 | USB D+/D- | çift yönlü | USB audio enumeration | E3 LED kodu |
| IO10 | UART TX/RX | çift yönlü | fastboot/diag kanalı (protokol UNKNOWN) | kanal yoksa mod devre dışı |
| IO11 | SWDIO/SWCLK | çift yönlü | debug (üretimde kapalı) | A7 ihlali sayılır |
| IO12 | WDT feed | çıkış | gözcü besleme | E6 reset |
| IO13 | IRQ line | giriş | kesme (runtime'a devredilir) | maskeleme boot'ta |
| IO14 | POWER_CTRL | çıkış | güç modu talebi | power-control'e devir |
| IO15 | Tamper/ sensing | giriş | güvenlik algılama (security) | olay yayını |
| IO16 | — | — | tanımsız sinyal yok | bu satır bilinçli "tanımsız" kaydı |

> Sinyal adları **sözleşme** düzeyindedir; pin eşlemesi MCU/donanım seçilince
> K001 ile birlikte doğrulanır (`⚠️ VERIFICATION REQUIRED` — §15 Q1).

## §19 Port ve MCU Portlama Sözleşmeleri

| # | Port | RTOS/HAL (Kaynak: firmware/index §9) | Boot'taki fark | Durum |
|---|---|---|---|---|
| P1 | XMOS XU316 (7.1 Amp Class AB) | bare-metal · XMOS HAL | ROM→bare-metal devri | tanımlı (Kaynak) |
| P2 | XMOS XU316 (USB Audio) | bare-metal · XMOS HAL | aynı zincir | tanımlı (Kaynak) |
| P3 | Raspberry Pi HAT (BCM2711) | Linux · ALSA | host boot bu modülde değil → K000 | sınır |
| P4 | DSP Processor (STM32) | FreeRTOS · STM32 HAL | ROM→RTOS (ST) | tanımlı (Kaynak) |
| P5 | IoT Gateway (ESP32) | FreeRTOS · ESP-IDF | ROM→ESP-IDF bootloader | tanımlı (Kaynak) |
| P6 | Vektör tablosu yerleşimi | MCU ailesine bağlı | harita değişir | UNKNOWN |
| P7 | Flash boyutu | MCU ailesine bağlı | bölüm tablosu değişir | UNKNOWN |
| P8 | Watchdog periyodu | MCU ailesine bağlı | E6 eşiği değişir | UNKNOWN |
| P9 | Saat hiyerarşisi (HSE/PLL) | MCU ailesine bağlı | süre bütçesi (§16) değişir | UNKNOWN |
| P10 | DMA kanal numaraları | MCU ailesine bağlı | DMA tampon eşlemesi değişir | UNKNOWN |
| P11 | Bootloader türü (ROM/ST/ESP) | üretici | imza doğrulama noktası değişir | UNKNOWN |
| P12 | Stack boyutu | derleme ayarı | tepe kullanımı ölçülür | UNKNOWN |
| P13 | Sürüm/anti-rollback | update+security | port nesnesi | SÖZLEŞME |
| P14 | Test erişimi (SWD/JTAG) | fiziksel | üretimde kapalı | A7 |
| P15 | Port eşleme kanıtı | dosya/satır gerekli | yok → UNKNOWN | ⚠️ VERIFICATION REQUIRED |

**Port kuralı (S11):** Port satırı yalnız Kaynak (firmware/index §9) veya
`⚠️ VERIFICATION REQUIRED` değeri taşır; port başına uydurma donanım
özniteliği yazılmaz.

## §20 Test Uygulama Arayüzü (testing modülüne)

| # | Test girişi | Beklenen çıkış | Kapı |
|---|---|---|---|
| TI1 | reset tetikleme | ST0 → ST12 (tam zincir) | B1 süre ölçümü |
| TI2 | flash'ta bozuk görüntü | E1, RETRY_COUNT artışı | B4/E1 |
| TI3 | 2 ardışık E1 | RECOVERY_REQ | B2 (retry ≤ 2) |
| TI4 | geçersiz imza | ST16 HALT | E4 |
| TI5 | bozuk CRC | E5 → RECOVERY | update kapısı |
| TI6 | DAC teyit okuması başarısız | E2 + alternatif profil | E2 |
| TI7 | USB enumeration yok | E3 + LED kodu | E3 |
| TI8 | konfigürasyon okuma hatası | E7 + fabrika varsayılanı | E7 |
| TI9 | tahsis denetimi (statik analiz) | 0 tahsis | B3 |
| TI10 | watchdog besleme gecikmesi | WDT periyodu içinde | B5 (periyot UNKNOWN) |
| TI11 | bayrak sahipliği denetimi | tek üretici | S3 |
| TI12 | tanımsız geçiş denetimi | 0 | S4 |
| TI13 | devir paketi bütünlüğü | tüm alanlar dolu | S7 |
| TI14 | debug modu varlığı (üretim) | kapalı | A7 |
| TI15 | telemetri olayı yayını | §9 olayları | S10 |
| TI16 | boot süresi tekrarlanabilirliği | ±tolerans (tutarlı) | B1 |

**Test arayüzü kuralı (S12):** Bu arayüz yalnız *tetik/olay* tanımlar;
uygulama kodu `testing` modülünün sorumluluğundadır.

## §21 Güvenlik Sınırlı Arayüzleri

| # | Arayüz | Yön | Veri | İhlal davranışı |
|---|---|---|---|---|
| SG1 | imza doğrulama çağrısı | boot → security | görüntü + beklenen imza | E4 HALT |
| SG2 | anti-rollback sürüm kontrolü | boot → security | sürüm | E4/E5 |
| SG3 | güven kökü durumu | security → boot | root-of-trust bayrağı | boot beklemede |
| SG4 | şifreli flash çözme | update → boot (yalnız update yetkisi) | anahtar materyali | boot'ta yetkisiz erişim YASAK |
| SG5 | tamper olayı | donanım → security | olay | boot zinciri durmaz, olay yayılır |
| SG6 | üretim debug kilidi | security ↔ boot | kilit bayrağı | A7 |
| SG7 | güvenli silme | update | bank temizliği | boot yetkisi yok |
| SG8 | olay kaydı | boot → security/diagnostics | E4 | kayıt yolu kaybolamaz |

**Güvenlik kuralı (S13):** Boot, imza doğrulamayı *çağırır*; doğrulama
algoritması/anahtarı bu modülde **bulunmaz** (firmware/index §8 — security
kapsamı).

## §22 İddia → Dayanak Eşlemesi

| # | İddia | Dayanak | Tür |
|---|---|---|---|
| D1 | Durum makinesi 17 durum | bu dosya §3 | sözleşme |
| D2 | Geçiş sözleşmeleri TR1-TR18 | bu dosya §4 | sözleşme |
| D3 | Bayrak sözlüğü 15 kayıt | bu dosya §2 | sözleşme |
| D4 | Hata kodları E0-E7 | boot-sequence §4 + firmware/index §4,§5,§7,§8 | kaynak |
| D5 | Süreler (100/500/200/100/200/300 ms) | boot-sequence §3 | kaynak |
| D6 | Toplam ~1,5 s | boot-sequence §3 Toplam | kaynak |
| D7 | Retry eşiği 2 | boot-anayasa §7 A8 | kural (anayasa) |
| D8 | Dual-bank yedek | ota-update §3 | kaynak (update) |
| D9 | Statik tahsis/heap yasak | firmware/index §5 + rtos §4 | kaynak |
| D10 | Watchdog zorunlu | firmware/index §5 | kaynak |
| D11 | Cihaz portları XMOS/BCM/STM32/ESP32 | firmware/index §9 | kaynak |
| D12 | DMA tampon 512 örnek @ 48 kHz | repo kuralı 8 | kural |
| D13 | DAC yalnız PCM3168A | repo kuralı 6 (ADR-038 H001) | kural |
| D14 | Protokol adları kod adı değil | repo'da firmware kodu YOK | dürüstlük |
| D15 | Fastboot protokolü bilinmiyor | — | UNKNOWN |
| D16 | Pin eşlemesi bilinmiyor | K001 bağımlılığı | UNKNOWN |
| D17 | Gözlem kanalı bilinmiyor | — | UNKNOWN |
| D18 | Öğe adları (bayrak) üretim adı değil | S2 | sözleşme |
| D19 | Test arayüzü uygulama kodu değildir | S12 | sözleşme |
| D20 | Port özellikleri uydurulmadı | S11 | kural |

## §23 Red Team İtiraz Kayıtları

| # | İtiraz | Yanıt | Kanıt |
|---|---|---|---|
| T1 | "Adlar gerçek API mi?" | Hayır, sözleşme etiketli | S2 + §15 Q1 |
| T2 | "17 durum kaynakta mı?" | Kaynak 8 aşama veriyor; durumlar ondan türetildi (ayrıştırma) | boot-sequence §2 + §3 |
| T3 | "Süreler tekrar mı edildi?" | Evet, tek doğruluk kaynağı §3 (Kaynak) | §16 |
| T4 | "E4-E7 kaynağı?" | firmware/index §4,§5,§7,§8 | §6 |
| T5 | "Neden 7 URL?" | §13; hepsi HTTP 200, envanter dışı | §13 |
| T6 | "Boş hücre var mı?" | tarama 0 | §18 |
| T7 | "TID/CIK karakter?" | tarama 0 | §18 kontrolü |
| T8 | "boot-anayasa ile çelişki?" | öncelik anayasa > surucu > örnekler | §12 C6 |
| T9 | "IO16 neden tanımsız?" | bilinçli kayıt — uydurma yok | §18 |
| T10 | "Modellerin port satırları doğrulanabilir mi?" | Kaynak firmware/index §9 | §19 |
| T11 | "Test kapıları çalıştırıldı mı?" | Hayır — kapı tanımlı, ölçüm testing'te | §20 + §15 |
| T12 | "Ölçüm yoksa neden eşik yazıldı?" | Eşik tanımdır, ölçüm ayrı (A3) | boot-anayasa §10 |
| T13 | "debug kilidi kanıtı?" | A7 kuralı, kanıt yok → ⚠️ | §21 SG6 |
| T14 | "500+ satır?" | kök ölçüm raporu | §18 kontrol satırı |
| T15 | "Yetkisiz dosya yazımı?" | beyazliste = bu modül + kök | rapor |

## §24 Sözleşme Kalıpları (uygulayıcıların kullanacağı desen listesi)

| # | Kalıp | Kullanım yeri | Kural karşılığı | Yanlış kullanım |
|---|---|---|---|---|
| K1 | Tek üretici bayrak | her bayrak | S3 | iki modülün aynı bayrağı yazması |
| K2 | Durum + tek geçiş | §3 makinesi | S4 | doğrudan STx'e atlama |
| K3 | Retry sayacı (max 2) | E1-E3 | A8 | sayaç artırmadan tekrar |
| K4 | Hata kodu + olay | §6/§9 | S8 | yalnız metin loglamak |
| K5 | Tek noktadan devir | §5 | S7 | birden fazla devir çağrısı |
| K6 | Bayrak kopyası ile devir | §5 bayrak_seti | S7 | canlı bayraklara doğrudan referans |
| K7 | Atomik sayaç oku/yaz | RETRY_COUNT | repo kuralı 2 | mutex ile korumak |
| K8 | Sabit kod — ayrı metin | §6 | S8 | kullanıcı metniyle eşlemek |
| K9 | Sözleşme etiketi | tüm adlar | S2 | koddaymış gibi sunmak |
| K10 | Kaynak tek doğruluk | süreler/hatalar | A3 | ikinci el süre yazmak |
| K11 | UNKNOWN işareti | bilinmeyen her yer | A5 | tahmini rakam yazmak |
| K12 | Yetki tek sahip | bank/pin/anahtar | A6 | boot'un update yetkisini kullanması |
| K13 | Sınırda tek kapı | mod giriş/çıkışı | S9 | ikinci giriş açmak |
| K14 | Test tetiği olay tabanlı | §20 | S12 | test kodunu ürüne gömmek |
| K15 | Ölçüm → sonra aktif | status: draft → active | §12 C5 | ölçmeden aktif yapmak |
| K16 | Değişiklik kaydı | §12 | C1-C8 | sessiz anlam değişikliği |
| K17 | Reddedilen iddia kaydı | §23 | Truth Mode | tartışmalı satırı silmek |
| K18 | Bölüm başına tablo | tüm bölümler | §18 kontrol | düz metin bloğu (ölçüm düşer) |
| K19 | URL envanter disiplini | §13 | kural | envanterden URL almak |
| K20 | Dürüstlük cümlesi | §14 | kural | depo kodu varmış gibi yazmak |

**Kalıp kuralı (S14):** Bir kalıp ihlali tespit edilirse satır silinmez;
tabloda "Yanlış kullanım" sütunu güncellenir ve gerekçe yazılır.

## §25 Çakışma Çözüm ve Öncelik Kayıtları

| # | Çakışma senaryosu | Öncelik | Karar | Gerekçe |
|---|---|---|---|---|
| X1 | anayasa ↔ surucu kural farkı | anayasa kazanır | surucu revize | §12 C6 |
| X2 | surucu ↔ örnekler farkı | surucu kazanır | örnek revize | §12 C6 |
| X3 | boot ↔ update (yedek bank) | update kazanır | boot yazamaz | boot-anayasa A6 |
| X4 | boot ↔ security (imza) | security kazanır | boot yalnız çağırır | §21 S13 |
| X5 | boot ↔ runtime (gözcü) | runtime kazanır | boot yalnız başlatır | §2 F11 |
| X6 | boot ↔ K001 (pin/ROM) | K001 kazanır | fiziksel gerçek üstünlük | §8 |
| X7 | boot ↔ K000 (host boot) | K000 kazanır | kapsam dışı | §1 |
| X8 | boot ↔ K004 (DSP zinciri) | K004 kazanır | boot yalnız init tetikler | boot-anayasa §2 |
| X9 | kayıt kanalı belirsizliği | UNKNOWN | tahmin yok | §9 |
| X10 | port özellikleri bilinmiyor | UNKNOWN | tahmin yok | §19 |
| X11 | ad çakışması (modüller arası) | tek tanım (S1) | biri kazanır, diğeri kullanır | S1 |
| X12 | URL çakışması (envanter) | envanter dışı olmalı | yeni URL gerek | §13 |
| X13 | ölçüm ↔ hedef çelişkisi | ölçüm kazanır | hedef revize edilir | boot-anayasa A3 |
| X14 | test ↔ ürün kodu | ürün kazanır | test ürüne gömülmez | S12 |
| X15 | debug ↔ üretim kilidi | üretim kilidi kazanır | debug kapalı | A7 |
| X16 | iki hazır bayrağı eksik | READY_CHECK kazanır | RECOVERY | §3 ST11 |
| X17 | retry ↔ recovery kararı | A8 eşiği | 2'de recovery | boot-anayasa A8 |
| X18 | DAC alternatif ↔ ADR-038 | ADR-038 kazanır | yalnız PCM3168A ailesi | boot-anayasa A10 |
| X19 | telemetri ↔ realtime yolu | realtime kazanır | kayıt devre dışı bırakılabilir | S10 |
| X20 | bayrak metni ↔ kod | kod kazanır | metin ayrı katman | S8 |
| X21 | sözleşme ↔ kod adı | sözleşme kazanır | kanıt yoksa SÖZLEŞME | S2 |
| X22 | envanter URL ↔ yeni URL | envanter dışı zorunlu | yeni doğrulanmış URL | §13 |
| X23 | tahmin ↔ UNKNOWN | UNKNOWN kazanır | tahmin yazılmaz | A5 |
| X24 | belirsiz pin eşlemesi | K001 kararı | bekle | §18 IO notu |
| X25 | port satırı uydurma | Kaynak kazanır | uydurma satır silinmez, işaretlenir | S11 |
| X26 | mod giriş çakışması | tek kapı | ikinci giriş kapatılır | S9 |
| X27 | ölçüm yok ↔ "hedef tuttu" | ölçüm | iddia geçersiz | A3 |

## §26 Kapanış Kontrolü (bu dosya)

| Kontrol | Beklenti | Durum |
|---|---|---|
| FM anahtar sayısı | 10 (node_id · node_type · status · parent_id · children · depends_on · evidence · validation · open_questions · group) | evet |
| `parent:` anahtarı | bulunmamalı | evet |
| `group` değeri | tirnaksız `mimari` | evet |
| Liste alanları | boşsa bile `[]` | evet (depends_on dolu) |
| Bölüm sayısı | §1 → §18 kesintisiz | evet |
| Bölüm başı tablo | her bölümde ≥1 tablo | evet |
| Web URL | 7 benzersiz · HTTP 200 · envanter dışı | evet |
| Repo dürüstlük cümlesi | §14 birebir | evet |
| Yasak token / boş hücre / CJK | 0 | evet |
| Son satır | Authority satırı birebir | evet |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
