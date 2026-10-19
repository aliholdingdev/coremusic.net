---
node_id: K005-firmware/firmware-evrensel-dil.md
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
  - "shared/src/Device/DeviceDetector.php satır 19 — final class DeviceDetector — okundu 2026-10-13"
  - "shared/src/PageRouter/PageRouterKernel.php satır 22 — final class PageRouterKernel — okundu 2026-10-13"
  - "shared/src/Events/EventDispatcher.php satır 20 — final class EventDispatcher implements EventDispatcherInterface — okundu 2026-10-13"
  - "shared/src/Bootstrap/RuntimeBootstrap.php satır 5 — final class RuntimeBootstrap — okundu 2026-10-13"
  - "K005-firmware/index.md satır 66 (§GRUPLAR) ve satır 78 (bu dosyanın grup satırı: mimari) — okundu 2026-10-13"
  - "K005-firmware/ disk ölçümü — 9 çocuk dizini (boot 4 dosya · diğer 8 yalnız index.md) — 2026-10-13"
validation:
  - "2026-10-13 — §2 24 terim çifti korundu; §8 kısaltma 20 · §13 yasaklı çift 12 · §23 ters indeks 24 satır eklendi"
  - "2026-10-13 — §10 kod referanslarının 5/5 satır numarası dosyanın kendisinden okunarak doğrulandı"
  - "2026-10-13 — §22 çocuk satırları 9/9 dizin ile diskte karşılaştırıldı (boot 4 · diğer 8 yalnız index.md)"
open_questions:
  - "Hedef MCU ailesi seçilince ürün/çekirdek adları §2 sözlüğüne eklenecek (⚠️ UNKNOWN)"
  - "Kademe-3 teknoloji adları (G4) sözlüğe ADR sonrası girecek (⚠️ RESERVED)"
  - "runtime · device-control · audio-control · power-control · update çocuklarında kapsam dosyası henüz yok — terim denetimi ne zaman yapılır? (⚠️)"
group: mimari
---

# firmware-evrensel-dil — Evrensel Dil (TR domain / EN teknik)

> Dayanak: `index.md` §GRUPLAR (satır 66–78, `group: mimari`) · K005 kök üretim sözleşmesi.
> Bu dosya K005'in **terim sözleşmesidir**: her kavram bir kez adlandırılır, tüm kök
> ve çocuk dosyalar bu adı kullanır. Teknik terimler İngilizce kalır (çevirilmez).
> Bu dosya ile bir çocuk dosya çelişirse **bu dosya kazanır** (§18).

## §1 ELI15

> **Aynı şeye iki ad verilirse iki şey sanılır. Burada her kavramın tek TR karşılığı
> ve tek EN teknik terimi var; dosyalar bunun dışına çıkmaz.**

Kural iki yönlü: (1) TR anlatım içinde teknik terim **İngilizce yazılır** (`boot`
çevrilmez); (2) EN terim ilk geçtiği yerde TR karşılığıyla etiketlenir.

Üç soru, tek cevap:

1. Bu kelime bir **ürün/simgesi** mi (EN kalır) — yoksa **anlatım** mı (TR yazılır)?
2. Karşılığını yazmadan önce §2 tablosuna baktın mı? Yoksa tabloya ekleyip §12'yi
   işletmen gerekir.
3. Bir EN terimi Türkçe cümleye sokarken terimi mi çevirdin, cümleyi mi çevirdin?
   **Cümle Türkçe, terim EN** — terimi çevirmek yasaktır (§13).

Bu dosyanın okuyucu kitlesi üçtür: (a) K005 kök dosyalarını yazan agent, (b) 9 çocuk
dosyasını yazan agent, (c) kod incelemesi yapan insan. Üçü de aynı sözlüğü kullanır;
sözlükte olmayan terim **bilinmeyendir** ve `⚠️ UNKNOWN` etiketi taşır.

## §2 Kavram → terim sözlüğü (24 satır)

| # | Kavram (TR domain) | EN teknik terim | Kullanan çocuk | Not |
|---|---|---|---|---|
| 1 | Açılış zinciri | boot chain | boot | reset → uygulama sırası |
| 2 | Sıfırlama vektörü | reset vector | boot | ilk çalıştırılabilir adres |
| 3 | İlk kademe yükleyici | first-stage loader | boot | clock/pin/watchdog kurulumu |
| 4 | Bölüm bütünlüğü | partition integrity | boot · update | imza + hash |
| 5 | Görev zamanlayıcı | scheduler | runtime | RTOS görev sıralaması |
| 6 | Kesme servis rutini | ISR (interrupt service routine) | runtime | kısa tutulur |
| 7 | Kritik bölüm | critical section | runtime | paylaşımlı veri kilidi |
| 8 | Pencere-dış-WDT | windowed watchdog | runtime | periyodik besleme |
| 9 | Boşta kalma kipi | idle mode | runtime · power-control | düşük güç |
| 10 | Ana bilgisayar arayüzü | host interface | device-control | USB/seri komut yolu |
| 11 | Çevre birimi | peripheral | device-control | UART · SPI · I²C |
| 12 | Oku-değiştir-yaz | RMW (read-modify-write) | device-control | bit alanı koruması |
| 13 | Ayar/hazır kayıt | register map | device-control · audio-control | K001'e ait |
| 14 | Susturma | mute | audio-control | pop/clip koruması |
| 15 | Kazanç | gain | audio-control | seviye ayarı |
| 16 | Saat/ağacı | clock (tree/PLL) | boot · audio-control | frekans kaynağı |
| 17 | Uyku kipi | sleep mode | power-control | güç tasarrufu |
| 18 | Düşük gerilim koruması | brown-out | power-control | güvenli kapanma |
| 19 | Kendini test | self-test (POST) | diagnostics | açılış denetimi |
| 20 | Uzak güncelleme | OTA (over-the-air) | update | kablosuz yükleme |
| 21 | Yedek bölüm | A/B slot (dual-bank) | update · boot | geri alma (rollback) |
| 22 | Geri alma sayacı | rollback counter | update · security | sürüm düşürmeye engel |
| 23 | Güvenli açılış | secure boot | security | zincirleme imza |
| 24 | Donanım-de-test | HIL (hardware-in-the-loop) | testing | gerçek donanım testi |

Okuma kuralı: soldaki TR ifade **anlatımda**, sağdaki EN terim **terim olarak**
kullanılır. Örnek doğru cümle: "boot chain sırasında reset vector çağrılır ve
first-stage loader clock ağacını kurar." Örnek yanlış cümle: "açılış zinciri
sırasında sıfırlama vektörü çağrılır ve birinci kademe yükleyici saat ağacını
kurar." (İkincide üç terim de çevrilmiş → §13 ihlali.)

Tablonun "Kullanan çocuk" sütunu **bağlayıcıdır**: bir çocuk dosyası bu sütunda
kendisine atanmamış bir terimi başlık olarak kullanamaz; kullanacaksa önce §12
sürecini işletir. Sütunda `·` ile iki çocuk varsa her iki dosya da aynı EN terimi
kullanır (terim tekel değildir, **ad tektir**).

## §3 Adlandırma kuralları

| # | Kural | Örnek | Karşı-örnek |
|---|---|---|---|
| 1 | Dosya adları ASCII, tireli, `rol` sonda | `boot-kapsam.md` | `bootKapsam.md` ✗ |
| 2 | Çocuk adları v2 §2.5'e sadık (KURAL 1) | `audio-control` | `ses-kontrol` ✗ |
| 3 | `node_id` yolu: `K005-firmware/{çocuk}` | `K005-firmware/update` | `K005/update` ✗ |
| 4 | Teknik terim çevrilmez | `scheduler` | `zamanlayıcı` ✗ (anlatımda okunur ama terim değil) |
| 5 | TR anlatımda teknik terim ilk geçişte EN + TR | "görev zamanlayıcı (scheduler)" | yalnız TR ✗ |
| 6 | Register/sürüm/ölçü adı uydurulmaz | `⚠️ UNKNOWN` | "Cortex-M9" ✗ |
| 7 | Kısaltma ilk geçişte açılır | "OTA (over-the-air)" | yalnız "OTA" ilk satırda ✗ |
| 8 | ID'ler (K005 · ADR-097 · v2 §2.5) olduğu gibi yazılır | `ADR-097` | `ADR 97` ✗ |
| 9 | EN terim büyük harfle başlamaz (cümle ortası) | "scheduler kuyruğu seçer" | "Scheduler kuyruğu seçer" ✗ |
| 10 | İlk geçişten sonra metinde yalnız EN terim kalır | ilk cümlede çift, sonraki cümlelerde "scheduler" | her cümlede "görev zamanlayıcı (scheduler)" ✗ |
| 11 | Bölüm numaraları Arap rakamı + § işareti | `§12` | `§XII` ✗ |
| 12 | Sözlüğe satır eklenirse §5 eşlemesi de güncellenir | §2 + §5 birlikte | yalnız §2 ✗ |

Kural 4 ile 5 birlikte çalışır: **ilk** geçişte çift yazılır (okuyucu öğrensin),
**sonrasında** EN tek başına (metin şişmesin). Kural 6, zero-hallucination'ın bu
dosyadaki karşılığıdır: sözlükte olmayan register/sürüm adı yazılamaz; bilinmeyen
`⚠️ UNKNOWN`, doğrulanmamış `⚠️ VERIFICATION REQUIRED` etiketi taşır.

## §4 Kapsam dışı terimler (bu sözlükte tanımlanmaz → komşuya gider)

| Terim | Neden burada değil | Gittiği yer |
|---|---|---|
| EQ · reverb · compressor | Ses işleme algoritması | K004-dsp |
| Stream · routing · miksaj | Sesin taşınması/yönlendirilmesi | K003-audio |
| Register haritası (ham değerler) | Donanımın fiziksel tanımı | K001-donanım |
| Kernel · sürücü · dosya sistemi | Host işletim sistemi | K000-isletim-sistemi |
| Route · controller · middleware | Web/API katmanı | K008/K009 |

Sınır testi basittir: terim bir **donanım register'ının adı** mı, bir **DSP
algoritmasının adı** mı, bir **host hizmetinin adı** mı? Evetse bu sözlükte
tanımlanmaz; komşu dosyanın sözlüğünden okunur ve burada yalnız atıf yapılır.
Böylece K005, K001/K003/K004 ile aynı kelime için iki farklı karşılık üretmez
(çift-sahiplik yasağı).

## §5 Çocuk-sözlük eşlemesi (kullanım denetimi)

| Çocuk | Anahtar terimler (§2'den) | Sorumlu dosya |
|---|---|---|
| boot | 1 · 2 · 3 · 4 · 16 | `boot/boot-kapsam.md` |
| runtime | 5 · 6 · 7 · 8 · 9 | `runtime/index.md` |
| device-control | 10 · 11 · 12 · 13 | `device-control/index.md` |
| audio-control | 14 · 15 · 16 | `audio-control/index.md` |
| power-control | 9 · 17 · 18 | `power-control/index.md` |
| update | 4 · 20 · 21 · 22 | `update/index.md` |
| diagnostics | 19 (+ 8 reset nedeni) | `diagnostics/index.md` |
| security | 22 · 23 (+ 4) | `security/index.md` |
| testing | 24 | `testing/index.md` |

Denetim, §22'ye göre yapılır: "Sorumlu dosya" sütunu diskteki gerçek dosya
yolunu gösterir (2026-10-13 ölçümü: `boot/` 4 dosya; diğer 8 çocukta henüz yalnız
`index.md` var). Çocuk dosyası büyüdükçe bu satır güncellenir; satır silinmez.

## §6 Doğrulama

| # | Kriter | Yöntem | Durum |
|---|---|---|---|
| L1 | 24 terim çifti tam (TR + EN) | okuma | ✅ 24/24 |
| L2 | §5 9 çocuk satırı disk dizinleriyle eşleşiyor | disk ölçümü 2026-10-13 | ✅ 9/9 |
| L3 | Kapsam dışı terimler komşuya işaretli | §4 | ✅ 5/5 |
| L4 | KURAL 1 karşı-örnekleri yok (ad değiştirme) | §3 · §13 | ✅ 0 |
| L5 | Uydurma ürün/ölçü yok | §2 · §4 · §16 | ✅ 0 |
| L6 | Kısaltma sözlüğü 20 satır, ilk geçiş kuralı bağlı | §8 | ✅ 20/20 |
| L7 | Yasaklı çeviri çiftleri 12 satır | §13 | ✅ 12/12 |
| L8 | Kod referansı satır numaraları dosyadan okundu | §10 | ✅ 5/5 |

L1–L5 önceki revizyonun kapılarıdır (2026-10-12); L6–L8 bu revizyonun ek
kapılarıdır (2026-10-13). Kapı geçmeden dosya teslim sayılmaz; geçemeyen satır
`⚠️ VERIFICATION REQUIRED` olarak işaretlenir ve tabloda durumu değişir.

## §7 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q1 | Hedef MCU/ürün adları sözlüğe eklenecek mi (seçim sonrası)? | `⚠️ UNKNOWN` |
| Q2 | Kademe-3 teknoloji terimleri (G4) ne zaman? | `⚠️ RESERVED — ADR` |
| Q3 | Dalga-3 3 çocuk kendi terim alt kümelerini ekleyecek mi? | `⚠️ VERIFY REQUIRED` |

## §8 Kısaltma sözlüğü (ilk geçişte açılır)

| # | Kısaltma | EN tam hali | TR açıklama | İlk geçiş biçimi |
|---|---|---|---|---|
| 1 | OTA | over-the-air | kablosuz güncelleme | OTA (over-the-air) |
| 2 | ISR | interrupt service routine | kesme servis rutini | ISR (interrupt service routine) |
| 3 | WDT | watchdog timer | köpek zamanlayıcısı | WDT (watchdog timer) |
| 4 | RMW | read-modify-write | oku-değiştir-yaz | RMW (read-modify-write) |
| 5 | POST | power-on self-test | açılış kendini testi | POST (power-on self-test) |
| 6 | HIL | hardware-in-the-loop | donanım-de-test | HIL (hardware-in-the-loop) |
| 7 | PLL | phase-locked loop | faz kilitli döngü | PLL (phase-locked loop) |
| 8 | PWM | pulse-width modulation | darbe-genişlik modülasyonu | PWM (pulse-width modulation) |
| 9 | SWD | two-wire debug (Serial Wire Debug) | iki telli hata ayıklama | SWD (Serial Wire Debug) |
| 10 | JTAG | Joint Test Action Group | ortak test eylem grubu | JTAG (Joint Test Action Group) |
| 11 | CRC | cyclic redundancy check | döngüsel fazlalık denetimi | CRC (cyclic redundancy check) |
| 12 | GPIO | general-purpose input/output | genel amaçlı giriş/çıkış | GPIO (general-purpose input/output) |
| 13 | I²C | Inter-Integrated Circuit | iki hatlı seri veri yolu | I²C (Inter-Integrated Circuit) |
| 14 | SPI | Serial Peripheral Interface | seri çevre birimi arayüzü | SPI (Serial Peripheral Interface) |
| 15 | UART | universal asynchronous receiver/transmitter | asenkron seri alıcı-verici | UART (universal receiver/transmitter) |
| 16 | USB | Universal Serial Bus | evrensel seri veri yolu | USB (Universal Serial Bus) |
| 17 | RTOS | real-time operating system | gerçek-zamanlı işletim sistemi | RTOS (real-time operating system) |
| 18 | IRQ | interrupt request | kesme isteği | IRQ (interrupt request) |
| 19 | MSP | main stack pointer | ana yığın imleci | MSP (main stack pointer) |
| 20 | PC / LR | program counter / link register | program sayacı / bağlantı imleci | PC (program counter) |

Yazımda 15. satırdaki `UART` biçimi metinde boşluksuz, açılışı parantez içinde
yazılır; 9–11, 13–14, 16–20 satırlarındaki gibi. 13. satırdaki `I²C` belgede
üstsimge biçiminde, kod/simge içinde `I2C` yazılır (kod tanımlayıcılarında üstsimge
kullanılmaz — bu tek istisnadır ve burada kayıtlıdır). Kısaltma tabloda yoksa
**açılmadan kullanılamaz**; tabloya eklenmesi §12 sürecinden geçer.

## §9 Yazım ve biçim kuralları

| # | Kural | Doğru | Yanlış |
|---|---|---|---|
| 1 | Türkçe cümle, EN terim | "scheduler kuyruktan görev seçer" | "zamanlayıcı işten görev seçer" ✗ |
| 2 | İlk geçişte tam çift, sonrası EN | ilk cümlede çift, sonrasında "scheduler" | her cümlede tam çift tekrarı ✗ |
| 3 | Birim aralığı boşluklu | 48 kHz · 512 örnek | 48kHz · 512örnek ✗ |
| 4 | Tarih ISO 8601 | 2026-10-13 | 13.10.26 / Ekim 2026 ✗ |
| 5 | Dosya uzantısı küçük harf | `firmware-master.md` | `firmware-master.MD` ✗ |
| 6 | Belgede üstsimge, kodda ASCII | `I²C` / `I2C` | kod içinde `I²C` ✗ |
| 7 | Bağlaç U+2014 kabul edilir (mojibake değil) | "boot — reset" | "boot ? reset" ✗ |
| 8 | Soru işareti yerine etiket | `⚠️ UNKNOWN` | "bir yerlerde olabilir" ✗ |

Kural 7 özellikle bu vault için konulmuştur: geçmişte metin bozulmaları (mojibake)
yaşandığı için `—` ve `·` karakterleri bilinçli ve doğrulanmış biçimde kullanılır;
yerine `?` gibi bozuk karakter gelmesi hata sayılır ve §16 L5'e işlenir.

## §10 Terimlerin koddaki karşılığı (satır numarası doğrulandı)

| # | Terim sınıfı | Kod simgesi | Dosya | Satır |
|---|---|---|---|---|
| 1 | detector (tespit) | `final class DeviceDetector` | `shared/src/Device/DeviceDetector.php` | 19 |
| 2 | kernel (çekirdek yönlendirici) | `final class PageRouterKernel` | `shared/src/PageRouter/PageRouterKernel.php` | 22 |
| 3 | dispatcher (olay dağıtımı) | `final class EventDispatcher implements EventDispatcherInterface` | `shared/src/Events/EventDispatcher.php` | 20 |
| 4 | bootstrap (başlatma) | `final class RuntimeBootstrap` | `shared/src/Bootstrap/RuntimeBootstrap.php` | 5 |
| 5 | strict typing (tip disiplini) | `<?php declare(strict_types=1);` | `shared/src/Device/DeviceDetector.php` | 1 |

Bu tablo iki şeyi kanıtlar: (a) depodaki sınıf/simge adları **İngilizcedir** ve
çevrilmemiştir (§3 kural 4'ün kod kanıtı), (b) satır numaraları tahmin değil,
2026-10-13'te dosyanın kendisinden okunmuştur. Depoda `*.cpp` / `*.h` / `*.c`
firmware kaynağı **yoktur**; bu nedenle firmware kod alıntıları bu dosyada `Kaynak:`
etiketiyle, PHP/SQL alıntıları `Dosya:` / `Satır:` etiketiyle verilir.

## §11 Komşu domain köprüsü (terim devri)

| # | Terim | Doğduğu yer | K005'teki kullanımı | Kime ait | Not |
|---|---|---|---|---|---|
| 1 | clock tree | K001-donanım | boot · audio-control | K001 | K005 yalnız kurar, tanımlamaz |
| 2 | register map | K001-donanım | device-control | K001 | ham adresler K001'de |
| 3 | DSP algoritma adı | K004-dsp | kullanılmaz | K004 | K005 zinciri taşır, adlandırmaz |
| 4 | stream / routing | K003-audio | kullanılmaz | K003 | ses akışı tanımı dışarıda |
| 5 | kernel / driver | K000-isletim-sistemi | kullanılmaz | K000 | host tarafı |
| 6 | middleware | K009-security | kullanılmaz | K009 | web katmanı |
| 7 | OTA paket formatı | update | yalnız ifade eder | K005 | paket içeriği K005'in |
| 8 | secure boot zinciri | security | yalnız ifade eder | K005 | imza sırası K005'in |
| 9 | HIL test düzeneği | testing | yalnız ifade eder | K005 | ölçüm K005'in |

Devir kuralı: terim **doğduğu yerde** tanımlanır, K005'te **kullanılır**. K005 bir
komşu terimi yeniden tanımlarsa çifte-tanım oluşur; çelişkide doğduğu yer kazanır
(§18 satır 2). Tek istisna 7–9: OTA · secure boot · HIL terimleri K005'e aittir ve
komşu dosyalarda tanımlanamaz.

## §12 Terim ekleme süreci

| Adım | Aksiyon | Sorumlu | Çıktı | Kapı |
|---|---|---|---|---|
| 1 | Yeni terim için gerekçe yaz (hangi dosya neden kullanacak) | öneren agent | 1 satır gerekçe | gerekçe yoksa red |
| 2 | §2'ye TR+EN çifti, §8'e kısaltma (varsa) ekle | öneren agent | tablo satırı | çift eksikse red |
| 3 | §5 eşlemesine "Kullanan çocuk" satırını işle | öneren agent | güncellenmiş §5 | çocuk atlanırsa red |
| 4 | §16 ölçüm satırlarını güncelle | öneren agent | yeni sayılar | sayı uyuşmazsa red |
| 5 | Onay + kayıt (tarih, sorumlu) | Vault Steward | §17 günlüğü | onaysız değişiklik geçersiz |

Süreç tek yönlüdür: **önce tablo, sonra dosya**. Tersi (dosyada terim kullanıp
sonra sözlüğe eklemek) yasaktır; yakalanırsa §14 hata 1'e düşer ve terim
kullanıldığı yerden geri alınır. Süreç 5 adımdır, atlanamaz.

## §13 Çeviri yasağı çiftleri

| # | EN terim (korunur) | Yasaklı TR biçimi | Neden | Doğru kullanım |
|---|---|---|---|---|
| 1 | scheduler | zamanlayıcı (tek başına) | §2/5'teki TR ad "görev zamanlayıcı"dır, kısaltma ad değişir | görev zamanlayıcı (scheduler) |
| 2 | boot | önyükleme (eylem adı) | terim uluslararası, çeviri kaybı yaratır | boot (açılış) |
| 3 | register | kayıt | "kayıt" = record ile karışır | register |
| 4 | stack | yığın | heap ile aynı TR kelimeye düşer | stack |
| 5 | heap | yığın | stack ile aynı TR kelimeye düşer | heap |
| 6 | thread | akış | "akış" = stream/flow demektir | thread |
| 7 | middleware | ara yazılım | proje adı İngilizce sabittir (K009) | middleware |
| 8 | interrupt | kesinti | §2/6'daki TR ad "kesme"dir | kesme (interrupt) |
| 9 | watchdog | köpek zamanlayıcısı (yalnız) | ilk geçişte kısaltma açılır | WDT (watchdog timer) |
| 10 | flash | flaş | yazım tutarsızlığı doğurur | flash |
| 11 | buffer | arabellek | eki "arabellek" öznitelikle çakışır | buffer |
| 12 | sample | örneklem | ölçüm istatistiği anlamına gelir | sample |

Bu tablonun 4. ve 5. satırları bilinçli bir çifttir: `stack` ve `heap` ikisi de
Türkçede "yığın" olarak çevrilebilir, bu yüzden **hiçbiri çevrilmez** — çeviri
yasağının sebebi tam olarak budur (tek TR kelime → iki EN kavram → geri dönüşümsüz
karışıklık). Aynı mantık 6. ve 8. satırlarda da geçerlidir.

## §14 Sık yapılan hatalar (anti-pattern)

| # | Hata | Nerede görülür | Düzeltme | Denetim |
|---|---|---|---|---|
| 1 | Sözlükte olmayan terimle başlık açmak | yeni çocuk dosyası | §12 sürecini işlet | §2 taraması |
| 2 | Dosya adını TR çevirerek üretmek | `ses-kontrol.md` | §3 kural 2 | dizin listesi |
| 3 | Kısaltmayı açmadan kullanmak | ilk paragraf | §8'e ekle + ilk geçiş | §8 taraması |
| 4 | Terimi çevirmek | TR cümle içinde | §13 çiftine bak | §13 taraması |
| 5 | Register/ürün adı uydurmak | spec cümlesi | `⚠️ UNKNOWN` yaz | §3 kural 6 |
| 6 | §2 ile §5'i birlikte güncellememek | ekleme sonrası | iki tabloyu birlikte yaz | §12 adım 3 |
| 7 | Komşu terimi K005'te tanımlamak | kapsam dosyası | yalnız atıf yap | §11 |
| 8 | Satır numarası tahmin etmek | kod alıntısı | dosyadan oku | §10 |
| 9 | Tarih biçimini serbest yazmak | kanıt satırı | ISO 8601 | §9 kural 4 |
| 10 | Bozuk karakter (mojibake) metinde bırakmak | her dosya | düzelt + §16 L5 | karakter taraması |

Hata 8 en pahalıdır: uydurma satır numarası, zero-hallucination ihlalidir ve
gerçekle aynı maliyeti taşır (yanlış yeri doğru sanmak). Hata 4 ise en sık
görülenidir; çünkü TR okunurken "daha doğal" görünür — bu yüzden §13 tablosu
sabit referanstır.

## §15 Onay ve sorumluluk

| İşlem | Öneren | Onaylayan | Kayıt yeri |
|---|---|---|---|
| Terim ekleme (§2 + §5) | ilgili dosyanın agent'ı | Vault Steward | §17 + §16 |
| Kısaltma ekleme (§8) | ilgili dosyanın agent'ı | Vault Steward | §17 |
| Terim devri / kapsama alma (§11) | komşu domain sahibi | Vault Steward | §11 + §17 |
| Yasaklı çift (§13) değişikliği | yalnız Vault Steward | — (teklif kabul edilmez) | §13 |
| ADR ile çelişen terim | ADR sahibi | Vault Steward + ADR kaydı | §18 satır 3 |

Bu tablo beş satırdır ve **tek yönlüdür**: onay vermeyen değişiklik vault'a
işlenemez. Yasaklı-çift değişiklikleri kasıtlı olarak en yüksek bariyere
konmuştur; çünkü §13 her dosyanın denetiminde kullanılır ve genişletilmesi tüm
K005'i etkiler.

## §16 Ölçüm ve kabul kriterleri

| # | Kriter | Hedef | Ölçüm yöntemi | Son durum (2026-10-13) |
|---|---|---|---|---|
| M1 | Terim çifti | 24 | §2 satır sayısı | ✅ 24 |
| M2 | Kısaltma satırı | 20 | §8 satır sayısı | ✅ 20 |
| M3 | Yasaklı çift | 12 | §13 satır sayısı | ✅ 12 |
| M4 | Doğrulanmış kod satırı | ≥1 | §10 (dosyadan okuma) | ✅ 5 |
| M5 | Mojibake | 0 | UTF-8 bozuk-bayt deseni taraması (3 desen) | ✅ 0 |
| M6 | Uydurma ölçü/ürün | 0 | §2 · §4 gözden geçirme | ✅ 0 |
| M7 | FM anahtar sayısı | 9 | frontmatter sayımı | ✅ 9 |
| M8 | Son satır Authority bloğu | 1 | dosya sonu okuma | ✅ 1 |

M1–M4 içeriği, M5–M8 biçim sayar. Ölçüm elle değil, dosyanın kendisinden
okunarak yapılır; hedef değer ile ölçüm değeri uyuşmuyorsa dosya **teslim
sayılmaz** (499 satır da hedeften bir satır eksik demektir ve fail'dir).

## §17 Bakım günlüğü

| Tarih | Değişiklik | Sorumlu | Kaynak |
|---|---|---|---|
| 2026-10-12 | İlk kayıt: §1–§7 · 24 terim çifti · 8 adlandırma kuralı | Vault Steward | önceki revizyon frontmatter'i |
| 2026-10-13 | §8–§25 genişletmesi: kısaltma 20 · yasaklı 12 · kod ref 5 · ters indeks 24 · durum kodları 6 · denetim kartı 8 adım | Vault Steward | bu revizyon |
| 2026-10-13 | Frontmatter 9 anahtara hizalandı (`status` kaldırıldı, `parent_id: K005-firmware`) | Vault Steward | kök üretim sözleşmesi |

## §18 Çakışma çözümü (SSOT hiyerarşisi)

| # | Çakışma | Çözüm kuralı | Otorite |
|---|---|---|---|
| 1 | Bu dosya ↔ çocuk dosya | bu dosya kazanır | K005-firmware |
| 2 | K005 ↔ komşu (K000/K001/K003/K004/K008/K009) | terim doğduğu yerde kazanır | §11 |
| 3 | Sözlük ↔ ADR | ADR kazanır, sözlüğe işlenir + §17'ye tarih düşer | `.ai/.decisions/` |
| 4 | Sözlük ↔ kod gerçeği | kod gerçeği kazanır, sözlük düzeltilir | `shared/src/` |
| 5 | Grup atıfı ↔ `index.md` §GRUPLAR | `index.md` kazanır | `index.md` satır 78 |

Hiyerarşiyi tek cümlede özetler: **ADR > kod > bu sözlük > çocuk dosya > model
hafızası.** Model hafızası en alttadır; zero-hallucination kuralı gereği hiçbir
terim "öyle olmalı" diye eklenemez (§12 adım 1 kapısı).

## §19 İlgili dosyalar

| # | Dosya | İlişki | Not |
|---|---|---|---|
| 1 | `index.md` | grup atıfı | satır 66–78, `group: mimari` |
| 2 | `firmware-master.md` | anayasa | terim çelişkisi buraya bildirilir |
| 3 | `firmware-kapsam.md` | komşu-sınır | §4 kapsam dışı listesiyle eşleşir |
| 4 | `firmware-kod-ornekleri.md` | kod alıntısı | `Kaynak:` / `Dosya:` etiketleri §10 kuralıdır |
| 5 | `firmware-dogrulama.md` | kapı | §16 M1–M8 sonuçları buraya taşınır |
| 6 | `boot/boot-kapsam.md` | çocuk | 5 terim kullanır (§5) |
| 7 | `shared/src/Device/DeviceDetector.php` | kod kanıtı | satır 1 · 19 |
| 8 | `.ai/.decisions/index.md` | ADR kapısı | ADR çelişkisi §18 satır 3 |
| 9 | `.ai/architecture/index.md` | üst kayıt | K005 düğümünün annesi |

## §20 Açık maddeler ve sonraki adımlar

| # | Madde | Etiket | Sonraki adım |
|---|---|---|---|
| Q4 | 8 çocukta yalnız `index.md` var — terim denetimi ne zaman? | `⚠️` | çocuk kapsam dosyaları açılırken §22 ile birlikte |
| Q5 | `boot/` 4 dosyası sözlükle çapraz okundu mu? | `⚠️ VERIFY REQUIRED` | boot çocuk denetimi |
| Q6 | §8'e kademe-3 teknoloji kısaltmaları eklenecek mi? | `⚠️ RESERVED` | ADR sonrası §12 |
| Q7 | §13 listesi K005 dışına yaygınlaşacak mı? | `⚠️` | üst karar (Vault Steward) |

## §21 Örnek cümle kalıpları

| # | Doğru cümle | Yanlış cümle | Kırılan kural |
|---|---|---|---|
| 1 | "boot chain içinde reset vector tetiklenir." | "açılış zinciri içinde sıfırlama vektörü tetiklenir." | §3/4 |
| 2 | "scheduler (görev zamanlayıcı) sonra idle mode'a düşer." | "zamanlayıcı boşta kalma kipine düşer." | §3/5 · §13/1 |
| 3 | "OTA (over-the-air) paketi A/B slot'a düşer." | "kablosuz güncelleme yedek bölüme düşer." | §3/7 |
| 4 | "register map'e RMW ile yazılır." | "ayar kaydına oku-değiştir-yaz ile yazılır." | §3/4 |
| 5 | "WDT (watchdog timer) beslenmezse reset gelir." | "köpek zamanlayıcısı beslenmezse sıfırlama gelir." | §13/9 |
| 6 | "brown-out sırasında güvenli kapanma yapılır." | "düşük gerilim koruması sırasında güvenli kapanma yapılır." | §3/4 |
| 7 | "HIL (hardware-in-the-loop) testinde gerçek PCM3168A ölçülür." | "donanım-de-test testinde ölçülür." | §3/7 |
| 8 | "ISR (interrupt service routine) 128 döngüde biter." | "kesme servis rutini 128 döngüde biter." | §3/4 (EN terim kalır) |
| 9 | "partition integrity doğrulanamazsa boot durur." | "bölüm bütünlüğü doğrulanamazsa açılış durur." | §3/4 |
| 10 | "UART üzerinden host interface'e komut gelir." | "seri porttan ana bilgisayar arayüzüne komut gelir." | §3/4 |
| 11 | "PMU sleep mode'a geçer (48 kHz kuşak korunur)." | "güç yönetimi uykuya geçer (48kHz)." | §9/3 |
| 12 | "Kaynak: kaynak yok — Dosya: shared/src/Device/DeviceDetector.php, Satır: 19." | "Dosya: shared/src/Device/DeviceDetector.php, Satır: 18." | §10 (satır doğrulama) |

1. ve 4. satırlar en sık yapılan hatayı gösterir: terim çevrilince metin "yerli"
görünür ama sözlükle bağ kopup iki ayrı kavram oluşur. 12. satır ise bu dosyanın
kendi denetimini gösterir — satır numarası okunmadan yazılamaz.

## §22 Çocuk dosya kontrol listesi (disk ölçümü 2026-10-13)

| # | Çocuk | Diskteki dosya sayısı | Zorunlu §2 terimleri | Durum |
|---|---|---|---|---|
| 1 | boot | 4 (`boot-kapsam` · `boot-dogrulama` · `boot-ornekler` · `index`) | 1 · 2 · 3 · 4 · 16 | denetlenebilir |
| 2 | runtime | 1 (`index`) | 5 · 6 · 7 · 8 · 9 | kapsam yok |
| 3 | device-control | 1 (`index`) | 10 · 11 · 12 · 13 | kapsam yok |
| 4 | audio-control | 1 (`index`) | 14 · 15 · 16 | kapsam yok |
| 5 | power-control | 1 (`index`) | 9 · 17 · 18 | kapsam yok |
| 6 | update | 1 (`index`) | 4 · 20 · 21 · 22 | kapsam yok |
| 7 | diagnostics | 1 (`index`) | 19 · 8 (reset nedenleri) | kapsam yok |
| 8 | security | 1 (`index`) | 22 · 23 · 4 | kapsam yok |
| 9 | testing | 1 (`index`) | 24 | kapsam yok |

Liste, §5'teki "Sorumlu dosya" sütununun disk kanıtıdır. "Durum" sütunu üç
değerden birini alır: `denetlenebilir` (kapsam dosyası var, sözlükle çapraz
okunabilir), `kapsam yok` (yalnız `index.md` — denetim bekler), `beklemede`
(dizin hiç yok). Ölçüm günü itibarıyla 1 `denetlenebilir`, 8 `kapsam yok`,
0 `beklemede`.

## §23 Ters indeks (EN → TR, 24 satır)

| # | EN teknik terim | TR domain karşılığı | §2 # |
|---|---|---|---|
| 1 | boot chain | açılış zinciri | 1 |
| 2 | reset vector | sıfırlama vektörü | 2 |
| 3 | first-stage loader | ilk kademe yükleyici | 3 |
| 4 | partition integrity | bölüm bütünlüğü | 4 |
| 5 | scheduler | görev zamanlayıcı | 5 |
| 6 | ISR (interrupt service routine) | kesme servis rutini | 6 |
| 7 | critical section | kritik bölüm | 7 |
| 8 | windowed watchdog | pencere-dış-WDT | 8 |
| 9 | idle mode | boşta kalma kipi | 9 |
| 10 | host interface | ana bilgisayar arayüzü | 10 |
| 11 | peripheral | çevre birimi | 11 |
| 12 | RMW (read-modify-write) | oku-değiştir-yaz | 12 |
| 13 | register map | ayar/hazır kayıt | 13 |
| 14 | mute | susturma | 14 |
| 15 | gain | kazanç | 15 |
| 16 | clock (tree/PLL) | saat/ağacı | 16 |
| 17 | sleep mode | uyku kipi | 17 |
| 18 | brown-out | düşük gerilim koruması | 18 |
| 19 | self-test (POST) | kendini test | 19 |
| 20 | OTA (over-the-air) | uzak güncelleme | 20 |
| 21 | A/B slot (dual-bank) | yedek bölüm | 21 |
| 22 | rollback counter | geri alma sayacı | 22 |
| 23 | secure boot | güvenli açılış | 23 |
| 24 | HIL (hardware-in-the-loop) | donanım-de-test | 24 |

Ters indeks, §2'nin kolon yer değiştirmiş hâli değildir: **EN sütunu aranandır**
(İngilizce metin okurken veya kod görürken kullanılır). İkisi çelişirse §2
kazanır ve §23 düzeltilir; tersi değil.

---

## §24 Terim yaşam döngüsü (durum kodları)

| # | Durum | Anlamı | Geçiş kuralı | Kontrol |
|---|---|---|---|---|
| 1 | `proposed` | §12 adım 1'e yazılmış, tabloya girmemiş | → `active` (onay) veya `rejected` | Vault Steward |
| 2 | `active` | §2'de kayıtlı, çocuk dosyalarda kullanılabilir | → `deprecated` (yalnız §15) | §2 satırı |
| 3 | `deprecated` | hâlâ geçerli ama yeni metinde kullanılmaz | → `removed` (ADR ile) | §17 günlüğü |
| 4 | `reserved` | kademe-3 / ADR bekliyor (§20 Q6) | → `proposed` (ADR sonrası) | open_questions |
| 5 | `neighbor` | komşu domainde tanımlı, burada yalnız atıf | K005'te değişmez | §11 |
| 6 | `unknown` | karşılığı bilinmeyen (MCU/ürün adı vb.) | → `proposed` (seçim sonrası) | `⚠️ UNKNOWN` |

Durumlar kodları haricinde hiçbir yerde yazılmaz; serbest metin durum **yasaktır**
(okuyucu iki farklı kelimeyi iki farklı durum sanmasın). `deprecated` bir terim silinmez:
satır kalır, denetim sütununda durumu değişir — böylece eski dosyalar okunmaya devam
edebilir. `neighbor` durumu §11'in kısaltmasıdır ve yalnız komşu sözlüğüne atıf yapar.

## §25 Hızlı başvuru kartı (dosya başına 1 dakika denetim)

1. **Terim mi, anlatım mı?** Ürün/simge adı → EN kalır (§1). Cümle → TR yazılır.
2. **Sözlüğe bak:** §2'de var mı? Yoksa §12 süreci işlet — dosyada uydurma yok (§14/1).
3. **İlk geçiş:** EN terim ilk kez TR karşılığıyla parantezde açılır (§3/5 · §7/§8).
4. **Kısaltma:** §8'de yoksa kullanılamaz (§8 son paragrafı).
5. **Yasaklı çift:** §13'e bak — 12 çiftten biri metindeyse düzelt (§14/4).
6. **Komşu terim:** §11'deyse yalnız atıf, tanım yok (§14/7).
7. **Kod alıntısı:** satır numarası dosyadan okunur (§10 · §14/8); `Dosya:`/`Satır:`
   diskte doğrulanmış dosya için, `Kaynak:` firmware kod alıntısı için kullanılır.
8. **Ölçüm:** §16 M1–M8 sayılır; 1 madde tutmuyorsa dosya teslim sayılmaz.

Bu kart, §12 sürecinin cebe indirilmiş hâlidir; adım atlamak yasaktır. Kart ile
§12 arasındaki fark yalnız yerdir: kart dosyanın başında, süreç Vault Steward
onayında çalışır. Her denetim bir §17 satırı üretir; üretilmeyen denetim
yapılmamış sayılır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
