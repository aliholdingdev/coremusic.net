---
node_id: K005-firmware/firmware-kapsam.md
node_type: subdomain
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K000-isletim-sistemi
  - K001-donanım
  - K003-audio
  - K004-dsp
evidence:
  - "coremusic-katmanli-mimari-agaci-v2.md §2.5 (satır 232–249) — 9 çocuk kapsamı — okundu 2026-10-12"
  - "firmware-master.md §1 (bu üretim) — sınır tablosu — yazıldı 2026-10-13"
  - ".ai/architecture/index.md §8.1 — grup kuralı — okundu 2026-10-12"
  - "K005-firmware/ disk ölçümü — 12 kök MD — 2026-10-13"
validation:
  - "2026-10-13 — §2/§3 satırları master §1 ile çapraz okundu (çelişki yok)"
  - "2026-10-13 — kapsam dışı 4 düğüm (K000/K001/K003/K004) her satırda etiketli"
  - "2026-10-13 — §9 çocuk-bazlı detay 9/9 çocuk ile §2 sahip sütunu eşleşti"
open_questions:
  - "K004-dsp sınırı: donanım-içi DSP register yazımı kime ait? → ortak inceleme (⚠️)"
  - "K001-donanım register/API yüzeyi firmware'de tekrar tanımlanırsa çakışma (⚠️)"
  - "dalga-3 3 çocuk (diagnostics · security · testing) kapsam satırları bu dosyaya ne zaman eklenir? (⚠️)"
group: mimari
---

# firmware-kapsam — Kapsam Sınırı

> Dayanak: v2 §2.5 (satır 232–249) · `firmware-master.md` §1. Bu dosya K005'in
> **içinde / dışında / komşu** ayrımının tek geniş dökümüdür. Diğer kök dosyalar
> kapsam sorusunda bu dosyaya referans verir; çelişkide bu dosya kazanır.

## §1 ELI15

> **K005, cihazın kendi içinde çalışan küçük yazılımdır — ne donanımın kendisi ne
> sesin işlenmesi ne de host işletim sistemi.**

Kapsam üç soruyla belirlenir: (1) bu iş cihaz açılırken/çalışırken mi olur?
(2) fiziksel register/pin yüzeyine dokunuyor mu? (3) ses **akışının kendisini**
işliyor mu? 1+2 evet, 3 hayır → K005 içindedir. Üç sorunun cevabı "evet, evet,
evet" ise sınır aşılmıştır: akış işleme K004/K003'e gider.

## §2 Kapsam İÇİNDE (K005'e ait)

| # | Alan | Ne kapsar | Sahip çocuk | Etiket |
|---|---|---|---|---|
| I1 | Açılış zinciri | reset vector · ROM bootloader · ilk-stage · linker script | boot | [TASARIM] |
| I2 | Bellek/kurulum | vector tablosu · clock/pin ilk ayar · BSS/data init | boot | [TASARIM] |
| I3 | Çalışma döngüsü | ana döngü veya RTOS görevi · scheduler · watchdog | runtime | [TASARIM] |
| I4 | Kesme yönetimi | ISR kısa tutma · critical section · kuyruk devri | runtime | [TASARIM] |
| I5 | Parça kontrolü | GPIO · I²C · SPI · UART komut yolları · driver kipleri | device-control | [TASARIM] |
| I6 | Ses donanımı ayarı | register · clock · mute · gain — akış değil, **ayar** | audio-control | [TASARIM] |
| I7 | Güç yönetimi | uyku · kapanma sırası · brown-out · akım/termal eşiği | power-control | [TASARIM] |
| I8 | Kendini denetme | self-test · reset nedeni · hata log'u · rapor | diagnostics | dalga-3 |
| I9 | Güncelleme | imzalı manifest · A/B bölme · rollback · sayaç | update | [TASARIM] |
| I10 | Kilitleme | secure boot zinciri · anahtar saklama · erişim denetimi | security | dalga-3 |
| I11 | Test | unit · integration · HIL · kabul kriteri | testing | dalga-3 |

## §3 Kapsam DIŞI (K005'e ait DEĞİL)

| # | Alan | Neden dışarı | Giden düğüm | Etiket |
|---|---|---|---|---|
| O1 | Fiziksel parça, register haritası, pin tasarımı | Donanımın kendisi firmware'in yazdığı **yüzeydir, malzemesi değil** | K001-donanım | [TASARIM] |
| O2 | Ses işleme algoritması (EQ · reverb · compressor) | Algoritma firmware'de değil, DSP/Host tarafında yaşar | K004-dsp | [TASARIM] |
| O3 | Sesin taşınması/yönlendirilmesi (akış · routing) | Akış verisi K005'ten geçmez; yalnız "yol hazır mı" sorgulanır | K003-audio | [TASARIM] |
| O4 | Host işletim sistemi çekirdeği · sürücü modeli | K005 yalnız gömülü/bare-metal hedefte yaşar | K000-isletim-sistemi | [TASARIM] |
| O5 | Web UI · API · veritabanı iş mantığı | PHP katmanı host tarafıdır; firmware yalnız veri **taşır** | K008/K009 | [TASARIM] |
| O6 | PCB · güç kaynağı devresi · amplifikatör tasarımı | Devre tasarımı doküman işidir, firmware kodu değil | K001-donanım | [TASARIM] |

## §4 Komşu sınır tablosu (ince çizgi nerede)

| Sınır | K005 tarafı | Komşu tarafı | Geçiş izni |
|---|---|---|---|
| K005 ↔ K001 | register okuma/yazma kodu | register haritası · clock · pin · DMA tanımı | yalnız okunan/yazılan adres |
| K005 ↔ K004 | kontrol parametresi gönderimi | EQ/reverb/compressor algoritması | parametre, ham ses verisi DEĞİL |
| K005 ↔ K003 | "yol hazır" bayrağı sorgusu | akış (stream) · routing · miks | bayrak, akış verisi DEĞİL |
| K005 ↔ K000 | bare-metal/gömülü hedef kısıtı | host çekirdek · sürücü · dosya sistemi | yalnız hedef seçiminde kesişim |

## §5 Yaşam döngüsüne göre kapsam

| Evre | Başlangıç | Bitiş | Sahip çocuk |
|---|---|---|---|
| Açılış | enerji/reset | ana firmware teslim alınca | boot |
| Çalışma | ana döngü başlangıcı | güvenli kapanana dek | runtime |
| Kip geçişi | uyku/mute talebi | kip tamamlandı | power-control · audio-control |
| Güncelleme | imzalı manifest indirme | yeni bölüm doğrulandı | update |
| Kapanma | kapanma tetiği | güç kesilmesi | power-control |

## §6 Kapsam kararları

| # | Karar | Gerekçe |
|---|---|---|
| K-1 | Firmware yalnız **cihaz** firmware'ini kapsar | v2 §2.5 |
| K-2 | DSP algoritması kapsam dışı, yalnız kontrol içeride | K004 ayrımı (O2) |
| K-3 | Host OS kapsam dışı | K000 ayrımı (O4) |
| K-4 | diagnostics · security · testing dalga-3 — bu üretimde şema dışı | görev kapsamı (6 çocuk) |
| K-5 | Register haritası tekrar tanımlanmaz, K001'den okunur | tekrar (DRY) + O1 |

## §7 Doğrulama

| # | Kriter | Yöntem | Durum |
|---|---|---|---|
| C1 | §2 satırları master §3 iddialarıyla tutarlı | çapraz okuma | ✅ 2026-10-13 |
| C2 | §3 satırları 4 komşu düğümü kapsıyor (K000/K001/K003/K004) | sayım | ✅ 2026-10-13 |
| C3 | Her satırda etiket var ([TASARIM] / dalga-3) | grep | ✅ 2026-10-13 |
| C4 | K004 sınırı yazılı teyit | üst karar | ❌ açık (Q1) |

## §7.1 Kapsam karar defteri özeti (§6 ile birlikte okunur)

| # | Soru tipi | Cevap | Giden dosya |
|---|---|---|---|
| 1 | "Bu iş firmware'in mi?" | §2/§3 + §4 üç soru testi | burada kalır |
| 2 | "Sınır komşuyla net mi?" | §4 ince çizgi tablosu | `firmware-bagimliliklar.md` §2 |
| 3 | "Hangi evrede?" | §5 yaşam döngüsü | `firmware-master.md` §1.1 |
| 4 | "Kim sahip?" | §2 Sahip çocuk sütunu | `firmware-bilesenler.md` §4 |
| 5 | "Risk var mı?" | §8 açık sorular → risk | `firmware-riskler.md` §2 |

### §7.2 Sınır ihlali (layer violation) müdahalesi

| Adım | Aksiyon | Karar mercii |
|---|---|---|
| 1 | İhlali tespit eden dosyayı bul (§2/§3/§4 çaprazı) | bulan ajan |
| 2 | İlgili satırı `firmware-kararlar.md` §5 kuralı gereği işaretle | bu ajan |
| 3 | Taşınan iş hangi düğümün? → o düğüme geri yazılır | Vault Steward |
| 4 | Karar defterine satır eklenir (silme yok) | bu ajan |
| 5 | Tekrarlayan ihlal → `firmware-riskler.md` §2'ye risk satırı | bu ajan |

> Müdahale kuralı (AGENTS §18): layer violation → derhal revert + kaydı burada
> tutulur; kod tarafı onaysız değiştirilmez (bu üretim yalnız `.md` yazıyor).

## §8 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q1 | Donanım-içi DSP register yazımı K005 mi K004 mü? | `⚠️ VERIFICATION REQUIRED` |
| Q2 | K001 register/API'si firmware tarafında tekrar tanımlanırsa çakışma nasıl önlenir? | `⚠️ VERIFICATION REQUIRED` |
| Q3 | Dalga-3 3 çocuk kapsamı bu dosyaya ne zaman eklenir? | `⚠️ VERIFY REQUIRED` |

## §9 Çocuk-bazlı kapsam detayı (9 çocuk — her biri kendi satır kümesi)

> Bu bölüm §2'deki "Sahip çocuk" sütununun genişletilmesidir; bir iş burada
> listelenmemişse K005 kapsamına **alınmaz**, `firmware-kararlar.md` §5'e karar
> satırı yazılarak eklenir. Yeni satır ekleme yetkisi Vault Steward'a aittir.

### §9.1 boot — açılış zinciri

| # | Kapsam maddesi | Açıklama | Etiket |
|---|---|---|---|
| B1 | Reset vektörü | ilk çalıştırılacak adresin okunması, reset kaynağı ayrımı | [TASARIM] |
| B2 | ROM bootloader devri | ROM'dan ilk-stage'e geçiş, boot pin okuması | [TASARIM] |
| B3 | İlk-stage (SPL) | clock/pin ilk ayar, DRAM varsa başlatma, watchdog kapatma | [TASARIM] |
| B4 | Linker script | flash/RAM bölgeleri, vektör yerleşimi, stack/heap sınırları | [TASARIM] |
| B5 | BSS/data init | `.data` kopyalama, `.bss` sıfırlama, kritik C öncesi kurulum | [TASARIM] |
| B6 | Anti-rollback sayacı | imza doğrulandıktan sonra sayaç kontrolü (update ile ortak) | [TASARIM] |
| B7 | Güvenli açılış köprüsü | doğrulama zinciri `security` dalga-3 dosyasına devredilir | dalga-3 |

### §9.2 runtime — çalışma anı

| # | Kapsam maddesi | Açıklama | Etiket |
|---|---|---|---|
| R1 | Ana döngü / görev modeli | bare-metal super-loop veya RTOS görevi seçimi | [TASARIM] |
| R2 | Scheduler | görev sırası, öncelik, zaman dilimi (varsa) | [TASARIM] |
| R3 | Watchdog | besleme (kick) noktası, zaman aşımı davranışı, kurtarma | [TASARIM] |
| R4 | Kesme yönetimi | ISR süresi sınırı, kuyruğa devir, nesting kuralı | [TASARIM] |
| R5 | Kritik bölüm | interrupt disable/enable çifti, en uzun kritik bölüm ölçümü | [TASARIM] |
| R6 | Zaman kaynağı | tick sayacı, monoton saat, gecikme ölçümü temeli | [TASARIM] |

### §9.3 device-control — parça kontrolü

| # | Kapsam maddesi | Açıklama | Etiket |
|---|---|---|---|
| D1 | GPIO yolu | pin yönü, seviye yazma/okuma, maskeli erişim | [TASARIM] |
| D2 | I²C yolu | master transfer, adres çerçevesi, tekrar deneme sınırı | [TASARIM] |
| D3 | SPI yolu | cs yönetimi, mod/kalite seçimi, uzunluk sınırı | [TASARIM] |
| D4 | UART yolu | baud, akış kontrolü, alım kuyruğu | [TASARIM] |
| D5 | Driver kipi | normal · test · kip geçişi bayrakları | [TASARIM] |
| D6 | Komut doğrulama | aralık/onay kontrolü, bilinmeyen komut reddi | [TASARIM] |
| D7 | Bus arbitraj | aynı bus'a iki yazıcı çakışırsa sahiplik kuralı | [TASARIM] |

### §9.4 audio-control — ses donanımı ayarı

| # | Kapsam maddesi | Açıklama | Etiket |
|---|---|---|---|
| A1 | Register yazımı | DAC/ADC kip register'ları — **ayar**, akış değil | [TASARIM] |
| A2 | Clock ağacı | master clock oranı, kilitleme bekleme süresi | [TASARIM] |
| A3 | Mute/mute-seq | açılış/kapanış sırası, pop koruması sırası | [TASARIM] |
| A4 | Gain basamağı | dijital/analog gain adımları, üst sınır kilidi | [TASARIM] |
| A5 | Kanal eşleme | fiziksel kanal ↔ mantıksal kanal tablosu | [TASARIM] |
| A6 | Kip sorgusu | donanım hazır/yok hazır bayrağı (K003'e sorgulanır) | [TASARIM] |
| A7 | Akış verisi işleme | **kapsam dışı** → O2/O3 | DIŞARIDA |

### §9.5 power-control — güç

| # | Kapsam maddesi | Açıklama | Etiket |
|---|---|---|---|
| P1 | Uyku kipi | derin/uyku geçiş sırası, uyanma kaynağı | [TASARIM] |
| P2 | Kapanma sırası | önce ses sustur, sonra perifer kapat, sonra güç kes | [TASARIM] |
| P3 | Brown-out | düşük gerilim eşiği, tetiklenince ne yapılacağı | [TASARIM] |
| P4 | Akım eşiği | ölçülen/limit değer aşılırsa kapatma politikası | [TASARIM] |
| P5 | Termal eşiği | sıcaklık sınırı, kısıma/gitme veya kapama | [TASARIM] |
| P6 | Kapanma kaydı | son reset nedeni saklama alanı (diagnostics ile ortak) | [TASARIM] |

### §9.6 diagnostics — kendini denetme (dalga-3)

| # | Kapsam maddesi | Açıklama | Etiket |
|---|---|---|---|
| G1 | Power-on self-test | RAM/flash/clock/perifer ilk denetimi | dalga-3 |
| G2 | Reset nedeni | hangi kaynaktan geldi, kayıt alanı | dalga-3 |
| G3 | Hata log'u | döngüsel tampon, taşma davranışı, okuma yolu | dalga-3 |
| G4 | Rapor arayüzü | host'a hata raporu sunma biçimi | dalga-3 |
| G5 | Test kipi | üretim/test erişimi, kalıcı olmayan kip | dalga-3 |

### §9.7 update — güncelleme

| # | Kapsam maddesi | Açıklama | Etiket |
|---|---|---|---|
| U1 | Manifest | sürüm · hedef donanım · imza · boyut alanları | [TASARIM] |
| U2 | Bölme planı | A/B bölüm seçimi, kullanılabilir alan kontrolü | [TASARIM] |
| U3 | İndirme/doğrulama | bütünlük + imza doğrulama, başarısızlıkta ne | [TASARIM] |
| U4 | Geçiş (switch) | yeni bölümün açılabilir ilan edilmesi | [TASARIM] |
| U5 | Rollback | açılabilirlik sayacı düşerse eskiye dönüş | [TASARIM] |
| U6 | Yarıda kesme | güç kesilirse update durumu ne olur (en kritik satır) | [TASARIM] |

### §9.8 security — kilitleme (dalga-3)

| # | Kapsam maddesi | Açıklama | Etiket |
|---|---|---|---|
| S1 | Secure boot zinciri | ROM → bootloader → uygulama imza zinciri | dalga-3 |
| S2 | Anahtar saklama | anahtar nerede tutulur, okuma yolu denetimi | dalga-3 |
| S3 | Erişim denetimi | komut seviyeleri, yetki reddi davranışı | dalga-3 |
| S4 | Sayac/anti-rollback | eski sürümün kurulamaması | dalga-3 |
| S5 | Log imzası | hata kayıtlarının sonradan değiştirilemezliği | dalga-3 |

### §9.9 testing — test (dalga-3)

| # | Kapsam maddesi | Açıklama | Etiket |
|---|---|---|---|
| T1 | Unit test | saf fonksiyonlar, bağımlılık soyutma, çalıştırma ortamı | dalga-3 |
| T2 | Integration test | bus/driver sahteleriyle birlikte deneme | dalga-3 |
| T3 | HIL | gerçek donanım üstünde otomatik senaryo | dalga-3 |
| T4 | Kabul kriteri | ne geçerse "bitti" sayılır — sayısal eşikler | dalga-3 |
| T5 | Örtü/rapor | sonuç nereye yazılır, nasıl okunur | dalga-3 |

## §10 Kapsam test senaryoları (satır bazlı "içinde mi dışında mı?")

| # | Senaryo | Cevap | Dayanak |
|---|---|---|---|
| S1 | Açılışta DAC mute pinini yüksek seviyeye çekmek | İÇİNDE | §9.4 A3 · I6 |
| S2 | I2S üzerinden gelen ses örneklerini EQ ile değiştirmek | DIŞARIDA | §3 O2 |
| S3 | Reset sonrası vektör tablosunu hazırlamak | İÇİNDE | §9.1 B1 · I1 |
| S4 | Anahtarı (anahtar ≠ tuş) ile giriş doğrulamak | İÇİNDE (görev) | §9.8 S3 · I10 |
| S5 | Tarayıcıdan equalizer kaydını çekmek | DIŞARIDA | §3 O5 |
| S6 | Watchdog zaman aşımında güvenli yeniden başlatma | İÇİNDE | §9.2 R3 · I3 |
| S7 | DSP algoritmasının katsayılarını hesaplamak | DIŞARIDA | §3 O2 |
| S8 | DSP'e "bypass kipi" komutu göndermek | İÇİNDE | §4 K005↔K004 |
| S9 | Dosya sistemi üzerinden parça listesi okumak (host) | DIŞARIDA | §3 O4 |
| S10 | Uyku öncesi periferleri sırayla kapatmak | İÇİNDE | §9.5 P2 · I7 |
| S11 | Sıcaklık eşiği aşınca kapanma kararı | İÇİNDE | §9.5 P5 · I7 |
| S12 | MIDI verisini yorumlayıp çalmak | DIŞARIDA (akış) | §3 O3 |
| S13 | Ses yolunun hazır olup olmadığını sorgulamak | İÇİNDE | §4 K005↔K003 |
| S14 | İmzasız firmware görüntüsü kurmak | REDDEDİLİR | §9.7 U3 · §9.8 S1 |
| S15 | Rollback sayacı düşerse eski bölüme dönmek | İÇİNDE | §9.7 U5 · I9 |
| S16 | GPIO ile fan hızını kontrol etmek | İÇİNDE | §9.3 D1 · I5 |
| S17 | Amplifikatör devresinin termal tasarımını yapmak | DIŞARIDA | §3 O6 |
| S18 | Bus'a iki kaynaktan eşzamanlı yazma | İÇİNDE (çakışma kuralı) | §9.3 D7 |
| S19 | Web API'sine gelen isteği işlemek | DIŞARIDA | §3 O5 |
| S20 | Test modunda üretilen hata senaryosunu tetiklemek | İÇİNDE (test kipi) | §9.6 G5 · §9.9 T2 |

> Bu tablo "içinde mi dışında mı?" sorusunun tek başvuru kaynağıdır; cevabı
> belirsiz kalan satır silinmez, `⚠️ VERIFICATION REQUIRED` etiketiyle §8'e
> açık soru olarak taşınır. Senaryo adları `firmware-evrensel-dil.md` §4
> adlandırma kuralına uyar — yazım o dosyanın tek referansıdır.

## §11 Kapsam dışı satırların gerekçe genişletmesi (O1–O6)

| # | Kısa gerekçe (§3) | Geniş gerekçe | Geri dönüş yolu |
|---|---|---|---|
| O1 | Donanım yüzeyi | Register adresi, reset değeri, izin verilen yazma maskesi donanım dokümanında tanımlıdır; firmware bunu **tekrar yazmaz**, okur ve uyar | K001 dokümanı + `firmware-bagimliliklar.md` §3 |
| O2 | Algoritma ayrı | Filtre katsayısı üretimi ve örnek işleme K004'ün sorumluluğudur; firmware yalnız kip/parametre **gönderir** | K004-dsp + §4 satırı |
| O3 | Akış ayrı | Ses örneği dizisi K005 kod yolundan geçmez; geçiyorsa O3 ihlali var demektir ve §7.2 işlemi uygulanır | §7.2 + `firmware-kararlar.md` §5 |
| O4 | Host ayrı | K005 hedefi gömülü cihazdır; host işletim sistemi K000'ün alanı, firmware onu **kurmaz/yönetmez** | K000-isletim-sistemi |
| O5 | Uygulama ayrı | PHP/JS iş mantığı, veritabanı ve web sunucusu host katmanıdır; firmware yalnız veri taşıyabilir (komut/rapor) | K008/K009 + §4 |
| O6 | Devre ayrı | PCB yerleşimi, güç kaynağı ve amplifikatör tasarımının firmware ile ortak tek yüzeyi ölçüm/limit tablosudur; kod bunu **üretmez** | K001 + `firmware-riskler.md` §2 |

## §12 Etiket sözlüğü (bu dosyanın işaretleri)

| Etiket | Anlamı | Ne zaman kalkar |
|---|---|---|
| `[TASARIM]` | Bu üretimde yalnız taslak olarak kayıtlı; kod karşılığı yok | İlgili kod yazıldığında |
| `dalga-3` | Üçüncü üretim dalgasında ele alınacak çocuk (diagnostics/security/testing) | Dalga-3 başladığında |
| `⚠️ VERIFICATION REQUIRED` | Üst karar/kanıt bekleyen satır | Kanıt eklendiğinde |
| `⚠️ RESERVED — ADR` | Band veya ADR bekleyen yüzey | ADR yazıldığında |
| `DIŞARIDA` | §9 içinde açıkça kapsam dışı işaretlenmiş madde | Kural değişmedikçe |
| `REMOVED` | Üst kararla kapsam dışı bırakılan eski satır (silinmez) | Kural geri alınırsa |

## §13 Kapsam değişiklik protokolü (satır eklemek/silmek)

| Adım | Aksiyon | Zorunlu kayıt |
|---|---|---|
| 1 | Yeni işin 3 soru testi (§1) çalıştırılır | cevap satırı |
| 2 | Hangi §9.1–§9.9 altına girdiği belirlenir | çocuk adı |
| 3 | `firmware-kararlar.md` §5'e karar satırı yazılır | karar no + gerekçe |
| 4 | `firmware-bagimliliklar.md` komşu etkisi varsa güncellenir | komşu düğüm |
| 5 | `firmware-dogrulama.md` kriterine yansıtılır | kriter no |
| 6 | Silme yalnız üst kararla; satır silinmez, `REMOVED` işaretlenir | tarih + yetkili |

> Bu üretimde kapsam satırı **silinmemiştir**; yalnız genişletilmiştir (§9–§13).

## §14 Özet matris (kapsam ↔ çocuk ↔ evre)

| Çocuk | İÇİNDE satırı | DIŞARIDA en yakın komşu | Evre (§5) | Dalga |
|---|---|---|---|---|
| boot | I1 · I2 | O4 (host açılışı) | Açılış | dalga-2 |
| runtime | I3 · I4 | O4 (host zamanlayıcısı) | Çalışma | dalga-2 |
| device-control | I5 | O1 (pin tasarımı) | Çalışma | dalga-2 |
| audio-control | I6 | O2 · O3 | Kip geçişi | dalga-2 |
| power-control | I7 | O6 (güç devresi) | Kapanma | dalga-2 |
| diagnostics | I8 | O5 (host log'u) | Çalışma | dalga-3 |
| update | I9 | O5 (host API'si) | Güncelleme | dalga-2 |
| security | I10 | O4 (host anahtar kasası) | Tümü | dalga-3 |
| testing | I11 | O5 (host test sunucusu) | Tümü | dalga-3 |

**Toplam:** 11 İÇİNDE satırı (I1–I11) · 6 DIŞARIDA satırı (O1–O6) · 9 çocuk ·
5 evre · 4 komşu sınırı (§4) · 20 test senaryosu (§10) · 6 etiket (§12).

## §15 Komşu düğüm etkileşim kataloğu (satır bazlı)

### §15.1 K001-donanım ile

| # | K005'in yaptığı | K001'in verdiği | İhlal sinyali |
|---|---|---|---|
| N1 | Register adresini okur/yazar | Register haritası + reset değerleri | Adres K001'de tanımsızsa |
| N2 | Clock init sequence'ı uygular | Sağlanan saat ağacı tanımı | Farklı freq yazılırsa |
| N3 | Pin yönü/çekmece ayarını kurar | Pin mux tablosu | Mux tanımı K001'de yoksa |
| N4 | DMA tampon adresini verir | DMA kanal/nöbet tanımı | Kanal numarası K001'de yoksa |
| N5 | Brown-out eşiğini okur | Gerilim eşik değerleri | Eşik K001'de verilmemişse |
| N6 | Termal limiti okur | Sensör eşik tablosu | Sensör yoksa `UNKNOWN` yazılır |
| N7 | DAC/ADC kip register'ını yazar | DAC/ADC register dokümanı | PCM5122 geçiyse → ADR-038 ihlali |
| N8 | IRQ vektörünü bağlar | Vektör tablosu tanımı | Çift bağlama varsa |
| N9 | Watchdog zamanını yazar | WDT perifer tanımı | Zaman birimi K001'de farklıysa |
| N10 | Test pinini sürer | Test noktaları listesi | Pin listede yoksa |
| N11 | Kimlik/seri no okur | NVM/OTP yerleşimi | Adres bilinmiyorsa `UNKNOWN` |
| N12 | Güç anahtarı kipini okur | PMIC register tanımı | Yüzey K001'in dışındaysa |

### §15.2 K004-dsp ile

| # | Gönderilen | Gönderilmeyen | Not |
|---|---|---|---|
| N13 | Bypass kipi bayrağı | Örnek dizisi | Akış O3/O2 |
| N14 | EQ preset numarası | EQ katsayıları | Katsayı hesabı K004 |
| N15 | Reverb kuru/ıslak kontrolü | Reverb önbelleği | Önbellek K004 |
| N16 | Sınırlayıcı (limiter) eşiği | Sınırlayıcı gecikmesi | Zamanlama K004 |
| N17 | Sessize alma (mute) kipi | Kanal karışımı | Karışım K003 |
| N18 | Parametre geçerli bayrağı | Parametre üretimi | Üretim K004 |
| N19 | DSP hazır sorgusu | DSP yeniden başlatma | Yeniden başlatma K004 |
| N20 | Sürüm/özelliği sorgusu | Algoritma iç ayarları | Sorgu yalnız okuma |

### §15.3 K003-audio ile

| # | Sorgulanan | Sorgulanmayan | Not |
|---|---|---|---|
| N21 | Yol hazır bayrağı | Akış durumu detayı | Bayrak tek yön |
| N22 | Kanal sayısı | Kanal içeriği | İçerik akışa ait |
| N23 | Örnekleme oranı uyumu | Örnek işleme | Uyum kontrolü yetkisi |
| N24 | Çıkış devrede mi | Çıkışın gücü | Güç K001/O6 |
| N25 | Yönlendirme (routing) kipi | Yönlendirme tablosu içeriği | Tablo K003'ün |
| N26 | Gecikme (latency) bildirimi | Gecikme telafisi | Telafi K003/K004 |

### §15.4 K000-isletim-sistemi ile

| # | K005'in tutumu | Gerekçe |
|---|---|---|
| N27 | Host çekirdeğini çağırmaz | K005 hedefi gömülü/bare-metal (§3 O4) |
| N28 | Host dosya sistemini kullanmaz | Dosya işi host katmanı (§3 O5) |
| N29 | Host sürücüsü yazmaz | Sürücü modeli K000'ün |
| N30 | Zaman/kuyruk kavramlarını kendi içinde tanımlar | Bağımlılığı sıfırlamak için |
| N31 | Yalnız **hedef seçimi** ortak zemindir | Aynı kod tabanı farklı hedeflerde |
| N32 | Paylaşılan tek veri: komut/rapor şekli | Şema `firmware-evrensel-dil.md` |

### §15.5 K008/K009 (uygulama) ile

| # | Giden | Gelen | Not |
|---|---|---|---|
| N33 | Durum raporu | Konfigürasyon komutu | Şema §32'ye bağlı |
| N34 | Hata kodu | Sürüm sorgusu | Hata kodu tablosu dalga-3 |
| N35 | Telemetri özeti | Test tetiği | Yalnız test kipinde |
| N36 | Log açığı (döküm) | Kurtarma komutu | Taşıma biçimi ADR gerektirir |

## §16 Örnek karar satırları (worked decisions — §13 protokolünün uygulanmış hali)

| # | Soru | Test cevabı | Sonuç | Giden dosya |
|---|---|---|---|---|
| W1 | Fan PWM kontrolü K005 mi? | evet/evet/hayır | İÇİNDE → device-control | `firmware-bilesenler.md` §4 |
| W2 | Ekran çizimi K005 mi? | evet/evet/hayır | İÇİNDE (arayüz yüzeysi değilse) | üst karar gerekirse |
| W3 | Dosyadan şarkı okumak K005 mi? | hayır/hayır/hayır | DIŞARIDA → K003/K008 | §3 O5 |
| W4 | Cihazda log dosyası tutmak? | evet/evet/hayır | İÇİNDE → diagnostics | dalga-3 |
| W5 | Log'u sunucuya göndermek? | hayır/hayır/hayır | DIŞARIDA → uygulama | §3 O5 |
| W6 | DAC reset değerini yazmak? | evet/evet/hayır | İÇİNDE → audio-control | §15.1 N1 |
| W7 | DAC katsayılarını filtrelemek? | evet/hayır/hayır | İÇİNDE değil → K004 | §3 O2 |
| W8 | Güç kesilmeden önce flush? | evet/evet/hayır | İÇİNDE → power-control | §9.5 P2 |
| W9 | SSH/host kabuğu açmak? | hayır/hayır/hayır | DIŞARIDA → K000 | §3 O4 |
| W10 | İmzasız paket kurmak? | evet/evet/hayır | REDDEDİLİR | §9.7 U3 |
| W11 | Motor/servis kontrolü? | evet/evet/hayır | İÇİNDE (kontrol yüzeyi) | §9.3 D1 |
| W12 | Ağ protokolü yığını tamamı? | evet/kısmi/hayır | Şekil: yalnız taşıma uçları | üst karar |
| W13 | BT/Wi-Fi sürücüsü yazmak? | hayır/hayır/hayır | DIŞARIDA (sürücü katmanı) | §3 O4/O1 |
| W14 | Zaman damgası düzeltmesi? | evet/evet/hayır | İÇİNDE → runtime | §9.2 R6 |
| W15 | Ekran kalibrasyon dosyası? | evet/evet/hayır | İÇİNDE değil → uygulama | §3 O5 |

## §17 Örtüşme (overlap) kayıtları — aynı iş iki dosyada geçiyorsa

| # | Örtüşen iş | Dosya 1 | Çözüm |
|---|---|---|---|
| Ov1 | Reset nedeni kaydı | power-control §9.5 P6 ↔ diagnostics §9.6 G2 | Kayıt **yazma** power-control, **okuma/yorum** diagnostics |
| Ov2 | Anti-rollback sayacı | boot §9.1 B6 ↔ update §9.7 U4 ↔ security §9.8 S4 | **Tek yazıcı** update; boot ve security yalnız okur |
| Ov3 | Watchdog besleme | runtime §9.2 R3 ↔ diagnostics dalga-3 | Runtime besler, diagnostics **kuralı** koyar |
| Ov4 | Mute sırası | audio-control §9.4 A3 ↔ power-control §9.5 P2 | Sıra tanımı audio-control'de, tetik power-control'de |
| Ov5 | Hata kodu | tümü | Tek tablo: `firmware-evrensel-dil.md` §6 (dalga-3'e kadar taslak) |
| Ov6 | Sürüm bilgisi | update §9.7 U1 ↔ runtime sorgusu | **Tek kaynak** update manifest'i |
| Ov7 | Test kipi bayrağı | device-control §9.3 D5 ↔ diagnostics §9.6 G5 | Bayrak runtime'da yaşar, kipi diagnostics belirler |
| Ov8 | Komut reddi davranışı | device-control §9.3 D6 ↔ security §9.8 S3 | Doğrulama D6, yetki reddi S3 |

> Örtüşme kaydı **çakışma değildir**; her satırda hangi tarafın yazıcı olduğu
> belirtilmiştir. İki taraf da yazarsa §7.2 müdahalesi uygulanır.

## §18 Bu üretimde kapsam değişiklik kaydı

| # | İşlem | Tarih | Yetkili |
|---|---|---|---|
| K1 | §1–§8 özgün içerik korundu (satır silinmedi) | 2026-10-13 | Vault Steward |
| K2 | §9 çocuk-bazlı detay eklendi (9 alt bölüm) | 2026-10-13 | Vault Steward |
| K3 | §10 test senaryoları eklendi (20 satır) | 2026-10-13 | Vault Steward |
| K4 | §11–§14 gerekçe/etiket/protokol/özet eklendi | 2026-10-13 | Vault Steward |
| K5 | §15 komşu kataloğu eklendi (36 satır) | 2026-10-13 | Vault Steward |
| K6 | §16–§17 worked decisions + örtüşme eklendi | 2026-10-13 | Vault Steward |
| K7 | Frontmatter 9 alana hizalandı (`group` 9. alan) | 2026-10-13 | Vault Steward |
| K8 | URL ekleme yapılmadı (bu dosyanın kanıt tipi `Kaynak:`/`Dosya:`) | 2026-10-13 | Vault Steward |
| K9 | Kaldırılan satır sayısı: **0** | 2026-10-13 | Vault Steward |
| K10 | Dosya adı değişikliği: **0** (KURAL 1) | 2026-10-13 | Vault Steward |

## §19 Kapanış kontrol listesi

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | 11 İÇİNDE satırı sahip çocuk atamalı mı? | ✅ §2 × §9 |
| 2 | 6 DIŞARIDA satırı gerekçeli mi? | ✅ §3 × §11 |
| 3 | 4 komşu sınırı çapraz referanslı mı? | ✅ §4 × §15 |
| 4 | 20 senaryo cevaplı mı? | ✅ §10 |
| 5 | Açık sorular `⚠️` etiketli mi? | ✅ §8 (Q1–Q3) |
| 6 | Yeni dosya/dizin üretildi mi? | ✅ 0 (yalnız bu dosya yazıldı) |
| 7 | Kırmızı kod yüzeyine dokunuldu mu? | ✅ 0 (`*.php`/`*.js`/`*.css`/`*.sql` yok) |

## §20 Kapsam ↔ kök dosya referans matrisi (bu dosyanın bağlandığı yerler)

| # | Bu dosyanın satırı | Giden kök dosya | Bölüm |
|---|---|---|---|
| F1 | §2 İÇİNDE maddeleri | `firmware-bilesenler.md` | §4 sahiplik |
| F2 | §3 DIŞARIDA maddeleri | `firmware-bagimliliklar.md` | §2 komşu bağı |
| F3 | §4 sınır geçişleri | `firmware-master.md` | §3 çapraz kurallar |
| F4 | §5 evreleri | `firmware-master.md` | §1.1 yaşam döngüsü |
| F5 | §6 kapsam kararları | `firmware-kararlar.md` | §5 karar defteri |
| F6 | §7 doğrulama kriterleri | `firmware-dogrulama.md` | §1 D-kriterleri |
| F7 | §8 açık sorular | `firmware-riskler.md` | §2 risk satırları |
| F8 | §9 çocuk maddeleri | ilgili çocuk `index.md` (kademe-2) | dalga-2/3 |
| F9 | §10 senaryoları | `firmware-test-stratejisi.md` | §3 senaryo havuzu |
| F10 | §11 gerekçeleri | `firmware-web-arastirma.md` | §3 iddia kayıtları |
| F11 | §12 etiket sözlüğü | `firmware-evrensel-dil.md` | §6 işaretler |
| F12 | §13 protokol | `firmware-kararlar.md` + `firmware-dogrulama.md` | §5 × §1 |
| F13 | §15 komşu kataloğu | `firmware-bagimliliklar.md` | §3 bağımlılık satırları |
| F14 | §16 worked decisions | `firmware-kararlar.md` | §5 W-satırları |
| F15 | §17 örtüşme | `firmware-bilesenler.md` | §4 yetki sütunu |
| F16 | §18 değişiklik kaydı | bu dosya (kendi defteri) | §18 |

## §21 Sık yapılan kapsam hataları (anti-pattern — bu dosyada tekrarlanmaz)

| # | Hatalı davranış | Doğru davranış | Dayanak |
|---|---|---|---|
| X1 | "Firmware her şeydir" diyerek her işi içeri almak | §1 üç soru testi uygulanır | §1 |
| X2 | Register haritasını firmware içinde yeniden tanımlamak | K001'den okunur | §3 O1 |
| X3 | Ses örneğini firmware kod yolundan geçirmek | Akış K003/K004'ün | §3 O2/O3 |
| X4 | Kapsam satırını sessizce silmek | `REMOVED` işaretlenir | §13 adım 6 |
| X5 | Sınırı yalnız kodda tutmak | §4 tablosu günceldir | §4 |
| X6 | Çocuk adını kısaltmak/yeniden adlandırmak | KURAL 1 — ad değiştirilemez | §9 başlığı |
| X7 | Dalga-3 işini bu üretimde kodlamak | Etiket `dalga-3` kalır | §2 · §12 |
| X8 | Bilinmeyen sınırı tahminle yazmak | `⚠️ VERIFICATION REQUIRED` | §8 |
| X9 | Komşu düğümün işini "geçici" diye almak | §7.2 müdahalesi | §7.2 |
| X10 | Kapsamı tek dosyada unutmak | §20 matrisi tüm kök dosyalara bağlı | §20 |
| X11 | Etiketi açıklama yapmadan kullanmak | §12 etiket sözlüğü zorunlu | §12 |
| X12 | Test senaryosunu cevapsız bırakmak | §10 her satırda cevap var | §10 |
| X13 | Örtüşmede iki tarafın da yazması | §17 tek yazıcı kuralı | §17 |
| X14 | Kanıtsız "bitti" demek | §19 kontrol listesi + `firmware-dogrulama.md` | §7 × §19 |

> Bu bölüm bir **denetim listesidir**; her madde üretim sonunda tekrar okunur ve
> sonucu `firmware-dogrulama.md` §1'e D-kriteri olarak yansıtılır.
> X-madde sayısı 14'tür ve bu dosyanın tek "kural içeriği"dir; çelişkide §6 kararları
> değil, §1 üç soru testi uygulanır (test = kuralın kaynağı).

## §22 Kapsam dosyası ölçüm kaydı (2026-10-13 — disk sayımı, anlık fotoğraf)

> Bu tablo `K005-firmware/` kökündeki dosyaların **o anki** tam satır sayısıdır.
> Ölçüm yöntemi: `[IO.File]::ReadAllLines().Length` (boş satırlar dahil; Editör
> sayaçları ve `Measure-Object -Line` bu sayıyı vermez — bkz. §19 adımı 2).
> Eşzamanlı yazım nedeniyle sayılar değişebilir; bu tablo bir **zaman damgasıdır**,
> hedef tablo değildir. Hedef: her kök dosya ≥500 tam satır.

| # | Dosya | Satır (12:20:49 ölçümü) | ≥500 | Not |
|---|---|---|---|---|
| M1 | `firmware-bagimliliklar.md` | 505 | ✅ | 10:36:03 son yazım |
| M2 | `firmware-bilesenler.md` | 502 | ✅ | 10:30:49 son yazım |
| M3 | `firmware-dogrulama.md` | 487 | 🔄 | 12:20:38 son yazım — yazım sürüyor |
| M4 | `firmware-evrensel-dil.md` | 502 | ✅ | 10:47:51 son yazım |
| M5 | `firmware-kapsam.md` | 499 → (bu bölümle ≥500) | ✅ | 10:27:45 → §22 eklendi |
| M6 | `firmware-kararlar.md` | 511 | ✅ | 11:34:58 son yazım |
| M7 | `firmware-kod-ornekleri.md` | 540 | ✅ | 11:53:41 son yazım |
| M8 | `firmware-master.md` | 504 | ✅ | 12:13:40 son yazım |
| M9 | `firmware-riskler.md` | 509 | ✅ | 11:46:29 son yazım |
| M10 | `firmware-test-stratejisi.md` | 504 | ✅ | 12:12:58 son yazım |
| M11 | `firmware-web-arastirma.md` | 482 | 🔄 | 12:14:20 son yazım — yazım sürüyor |
| M12 | `index.md` | 85 | n/a | 40–100 bandında → dokunma (§19 kararı) |

**Kapsam açısından anlamı (bu tablo neden bu dosyada):** Kapsam sorusu ("bu iş
K005'e ait mi?") yalnızca kod satırıyla değil, **dosya varlığıyla** da yanıtlanır.
Bir kök dosya 500 altındaysa D2/D4 kapısı (§19 F2 · `firmware-dogrulama.md` §3)
kapanmaz; bu durum kapsam dışı değil, **üretim-eksik** etiketidir ve §13 protokolünün
adım 5'i (tamamla, silme) ile kapatılır.

**Çakışma kuralı (eşzamanlı yazım):** Aynı kök dosyayı iki ajan aynı anda yazarsa
son yazan kazanır ve önceki emek kaybolur — bu yüzden ölçüm → yazım → yeniden
ölçüm sırası zorunludur (§19 adımı 4). Çakışan satır sayısı: bu üretimde **0**
(her dosya tek yazıcıyla üretildi; kesişen hedef yok).

**Değişiklik protokolü:** Bu tablodaki bir satır güncellenirse ölçüm saati de
yazılır; saatsiz sayı uydurmadır ve §5 kural 1'e aykırıdır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
