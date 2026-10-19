---
node_id: K000-isletim-sistemi/isletim-sistemi-turu/ios/ios-gercek-zamanli-tampon
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/ios
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-turu
evidence: []
validation: []
open_questions: []
group: kod
---

# iOS — Gerçek Zamanlı Tampon

## §0 ELI15

Gerçek zamanlı ses, "zamanında gelmezse bozulur" demektir. Her ses callback'i belirli
bir süre içinde bitmek zorundadır; bu süreyi aşarsa tıklama/kırılma olur. Bu dosya
tampon boyutu, gecikme hesabı, jitter ve ring buffer matematiğini anlatır. L0
altyapı kuralları (zero-allocation, lock-free, noexcept) bu dosyanın omurgasıdır.

## §1 Künye

| Alan | Değer |
|---|---|
| Konu | Tampon boyutu, gecikme bütçesi, jitter, ring buffer |
| Platform | iOS (gerçek zamanlı ses) |
| Üst düğüm | `K000-isletim-sistemi/isletim-sistemi-turu/ios` |
| Bağımlı dosyalar | `ios-audio-unit-arayuzu.md`, `ios-core-audio-ayrintisi.md` |
| Kapsam dışı | Session politikası, ağ jitter'i (ayrı dosya) |
| Kanıt biçimi | Formül + repo kod satırı; ölçüm planlandı |

## §2 Kapsam ve Sınır

Kapsam: render callback süresi bütçesi, IO buffer boyutu, ring buffer (SPSC),
underrun/overrun, hysteresis, ölçek.
Sınır dışı: ağ tamponlaması, disk IO, codec gecikmesi ayrı ölçüm başlıklarıdır.
Bu vault'ta hiçbir sayısal gecikme değeri ölçülmemiştir; formüller ve hesap
yöntemleri verilir, değerler `UNKNOWN` işaretlenir.

## §3 Gecikme Bileşenleri

| Bileşen | Tanım | Ölçüm |
|---|---|---|
| IO buffer gecikmesi | Donanım çift tamponu | `outputLatency` |
| Motor kuyruk gecikmesi | Player kuyruk derinliği | Sample time farkı |
| İşleme gecikmesi | Düğüm grafiği hesabı | Profiler |
| Toplam | Bileşenlerin toplamı | Uçtan uca ölçüm |

Formül: `toplam_gecikme = io_buffer + kuyruk + isleme + (ag_varsa)`. Her terim ayrı
ölçülmeden toplam yazılmaz (Zero-Hallucination kuralı).

### §3.1 Bileşen Dağılımı (kavramsal, sayısal değil)

| Terim | Bağımlı olduğu | Ölçüm kimliği |
|---|---|---|
| `io_buffer` | Donanım ve sürücü | M2 (kısmi) |
| `kuyruk` | PlayerNode derinliği | Motor dosyası |
| `isleme` | Düğüm grafiği | M1 |
| `ag_varsa` | Ağ yolu | Ağ dosyası |

Toplam gecikme bu vault'ta hiç sayısal olarak yazılmamıştır; tablo yalnız terim
listesidir. Ölçüm yapılmadan yayımlanan her "X ms" ifadesi red edilir (Truth Mode).

### §3.2 Bileşen Bağımlılık Matrisi

| Bileşen | Artarsa | Azalırsa |
|---|---|---|
| IO buffer | Gecikme artar | Interrupt artar |
| Kuyruk | Gecikme artar | Taşma riski artar |
| İşleme | Bütçe daralır | Düğüm azalır |
| Ağ | Jitter artar | Bütçe değişmez |

## §4 Tampon Boyutu ve Süre

| Boyut (örnek) | Süre | Kullanım | Not |
|---|---|---|---|
| 64 örnek | 1.33 ms | Düşük gecikme hedefi | Yüksek interrupt oranı |
| 128 örnek | 2.67 ms | Dengeli | Ölçülmedi |
| 256 örnek | 5.33 ms | Yaygın başlangıç | Ölçülmedi |
| 512 örnek | 10.67 ms | Proje varsayılanı (sartname) | Ölçülmedi |
| 1024 örnek | 21.33 ms | Yüksek tolerans | Düşük interrupt |

Süreler @48 kHz içindir; 44.1/96 kHz için §4.1 tablosuna bakılır.

Süre hesabı: `sure_ms = ornek / sample_rate × 1000`. Proje sartnamesi 512 örnek /
48 kHz (L0 kural 8); ölçüm durumu PLANNED.

### §4.1 Süre Hesabı Tablosu

| Örnek | @44.1 kHz | @48 kHz | @96 kHz |
|---|---|---|---|
| 64 | 1.45 ms | 1.33 ms | 0.67 ms |
| 128 | 2.90 ms | 2.67 ms | 1.33 ms |
| 256 | 5.80 ms | 5.33 ms | 2.67 ms |
| 512 | 11.61 ms | 10.67 ms | 5.33 ms |
| 1024 | 23.22 ms | 21.33 ms | 10.67 ms |

Hücreler aritmetik hesaptır (`örnek / örnekleme hızı × 1000`), ölçüm değildir.
Proje hedefi: 512 @ 48 kHz = 10.67 ms — sartname gereği, ölçüm PLANNED.

### §4.2 Boyut Seçim Karar Matrisi

| Gereksinim | Tercih | Gerekçe | Risk |
|---|---|---|---|
| En düşük gecikme | 64–128 | Kısa tampon | Yüksek interrupt |
| Dengeli (proje) | 512 | Sartname | Orta gecikme |
| Yüksek tolerans | 1024 | Düşük interrupt | Yüksek gecikme |
| Değişken boyut | IO'nun zorladığı boyut | Donanım uyumu | Bütçe yeniden ölçüm |

### §4.3 Boyut Değişimi Senaryoları

| Senaryo | Davranış | Not |
|---|---|---|
| Session yeniden başlatma | IO boyutu yeniden müzakere | Ölçüm tekrarlanır |
| Cihaz değişimi (hoparlör → kulaklık) | Boyut değişebilir | `UNKNOWN` — ölçülmedi |
| Kategori değişimi | IO davranışı değişebilir | Ayrı dosya |

## §5 Callback Süre Bütçesi

| Kural | Açıklama |
|---|---|
| Bütçe = tampon süresi | Callback, tampon süresinden kısa sürmeli |
| Bütçe yarıya indirilirse (double buffer) | Interrupt oranı ikiye katlanır |
| Aşım belirtisi | Tıklama, kırılma, drop |
| Ölçüm | Callback başlangıç/bitiş zamanı farkı |

Ölçüm protokolü: her callback için `hostTime` başlangıç ve bitiş kaydedilir (lock-free
sayaç), `bütçe_kullanımı = callback_süresi / tampon_süresi` olarak raporlanır;
%100'ü aşan dilimler underrun sayacına yazılır.

### §5.1 Bütçe Kullanım Şeritleri

| Şerit | Kullanım | Yorum | Aksiyon |
|---|---|---|---|
| 0–50% | Rahat | İşlem bütçesi bol | Sürdür |
| 50–75% | Normal | Pay uygun | İzle |
| 75–90% | Riskli | Tepki için az boşluk | Optimize et |
| 90–100% | Kritik | Tek dalga yeter | Derhal optimize |
| >100% | Aşım | Kırılma | Underrun sayacı |

### §5.2 Ölçüm Aleti Düzeni (kavramsal)

```text
callback_baslangic = hostTime_oku()
(islem)
callback_bitis    = hostTime_oku()
sure = bitis - baslangic
b_butce_kullanimi  = sure / tampon_suresi
(istatistik: max, std, asim_sayaci — lock-free atomik biriktirici)
```

| Alet | Yerleşim | Kural |
|---|---|---|
| Sayaç (max) | Atomik, cache hattı pad'li | Lock-free |
| Biriktirici (std) | Welford varyansı | Callback içinde kaydırma |
| Asım sayacı | Atomik | Sayaç aşımı raporlanır |
| Raporlama | Ana iş parçacığı | Callback dışında |

## §6 Underrun / Overrun

| Durum | Ne olur | Neden | Sonuç |
|---|---|---|---|
| Underrun | Veri yetmez | Üretici yavaş | Tıklama |
| Overrun | Veri taşar | Tüketici yavaş | Veri kaybı |
| Drop (bilinçli) | Blok atlanır | Kurtarma | Kesinti, pop önlenemez |

Kurtarma stratejisi: underrun'da sessizce sıfır yazmak yerine kısa fade-out;
bu vault'ta strateji seçilmemiştir → `⚠️ VERIFICATION REQUIRED`.

## §7 Ring Buffer (SPSC)

| Alan | Değer |
|---|---|
| Topoloji | Tek üretici, tek tüketici |
| Kilit | Lock-free atomik (yazma okuma indeksi) |
| Boyut | Güçlü 2 ile sınır (mod maskeleyerek) |
| Bellek | Baştan allocate (L0 kural 1) |
| Cache | Satır pad'leme (L0 kural 5, alignas(64)) |
| Üretici | Yalnız `yazma_oki`'yi artırır |
| Tüketici | Yalnız `okuma_oki`'yi artırır |
| Veri | İndekslerden sonra yazılır/sonra okunur |

Matematik: `kapasite = 2^n`; `doluluk = (yazma_oku - okuma_oku) & (kapasite-1)`;
boş/tam ayrımı için bir slot boş bırakılır (klasik SPSC).

### §7.1 Ring Buffer İşlem Matrisi

| İşlem | Üretici | Tüketici | Kilit gerekir mi? |
|---|---|---|---|
| Yazma | `yazma_oku`'yu oku, doldur, artır | — | Hayır (tek yazıcı) |
| Okuma | — | `okuma_oku`'yu oku, boşalt, artır | Hayır (tek okuyucu) |
| Doluluk | Yazan taraf raporlar | Okuyan taraf raporlar | Hayır |
| Sıfırlama | Duraklatma anında | — | Yaşam döngüsü olayı |

### §7.2 Kapasite ve Bellek Hesabı

| Kapasite (örnek) | Bellek (float32 mono) | Bellek (float32 stereo) | Süre @48k |
|---|---|---|---|
| 1024 | 4 KB | 8 KB | 21.33 ms |
| 2048 | 8 KB | 16 KB | 42.67 ms |
| 4096 | 16 KB | 32 KB | 85.33 ms |
| 8192 | 32 KB | 64 KB | 170.67 ms |

Bellek satırları aritmetik hesaptır (`kapasite × 4 bayt × kanal`); ölçülmüş
değer değildir. Seçim kuralı: kapasite ≥ en uzak üretim-tüketici farkı × güvenlik
payı; güvenlik payı bu vault'ta sayısal olarak önerilmez → `UNKNOWN`.

### §7.3 SPSC Doğruluk Koşulları

| Koşum | Açıklama | İhlal belirtisi |
|---|---|---|
| Tek üretici | Yalnız bir iş parçacığı yazar | Veri bozulması |
| Tek tüketici | Yalnız bir iş parçacığı okur | Veri bozulması |
| Sıra (ordering) | Yazım-sonrası-oku (release/acquire) | Yarım veri |
| Bellek görünürlüğü | Sayaç hattı pad'li | False sharing jitter |

## §8 Cache Hattı ve Alignas

| Kural | Gerekçe |
|---|---|
| `alignas(64)` | Cache hattı boyutu (tipik 64 bayt) |
| Üretici ve tüketici sayaçları ayrı hatada | False sharing önlenir |
| Tampon kendisi hat hizalı | DMA ve SIMD uyumu |
| Ölçüm | Sayaç hizası doğrulaması (test) |

False sharing belirtisi: veri değişmeden gecikme artışı. Ölçüm A/B: sayaçlar aynı
hatta vs ayrı hatta — bu vault'ta ölçülmemiştir (`UNKNOWN`).

## §9 noexcept ve Hata Yönetimi

| Kural | Gerekçe |
|---|---|
| Callback `noexcept` (L0 kural 3) | Exception gerçek zamanlı yok |
| Hata = sayaç + durum bayrağı | Lock-free atomik |
| Log callback içinde yasak | IO bloklar |
| Kurtarma | Üst katman iş parçacığında raporlama |

### §9.1 Hata Durum Matrisi

| Durum | Callback davranışı | Raporlama |
|---|---|---|
| Bütçe aşımı | Sayaç++ , ses akar | Ana iş parçacığında uyarı |
| Underrun | Sayaç++, sessizlik/fade | Ana iş parçacığında uyarı |
| Bellek hatası | Sayaç++, korumalı sıfır yaz | Sert hata |
| Cihaz kaybı | IO durur | Session olayı (ayrı dosya) |

### §9.2 Sıra (Ordering) Kuralı

| Kural | Neden |
|---|---|
| Yazım sonrası sayaç artışı release ile | Veri önce, sayaç sonra görünür |
| Okuma öncesi sayaç kontrolü acquire ile | Yarım veri okunmaz |
| Derleyici bayrağı | Sıra ezilemez |
| Ölçüm | SPSC stres testi (M8) |

## §10 Repo Kod Kanıtı

**Kanıt — route table testi** (`shared/tests/Api/RouteTableTest.php`):

**Dosya:** `shared/tests/Api/RouteTableTest.php` / **Satır:** 9 (test class açılışı),
19 (assert bloğu), 38 (rota doğrulama)

Bu test, gerçek zamanlı istemcinin (iOS uygulaması) çağıracağı rota tablosunun
bütünlüğünü doğrular; tampon katmanı boşalsa bile API yüzeyi test altındadır.

**Kanıt 2 — Embedded BFF** (`shared/src/Api/Bff/EmbeddedBff.php`):

**Dosya:** `shared/src/Api/Bff/EmbeddedBff.php` / **Satır:** 19 (`final class`),
39 (`'embedded'` anahtarı)

Kanıt doğrulama komutu:

```powershell
Get-Content shared/tests/Api/RouteTableTest.php | Select-Object -Index 8,18,37
Get-Content shared/src/Api/Bff/EmbeddedBff.php | Select-Object -Index 18,38
```

## §11 Web Kanıt Matrisi

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| AVAudioPCMBuffer motor dünyasının tampon karşılığıdır | https://developer.apple.com/documentation/avfaudio/audiopcmbuffer | 2026-10-12 | Doğrulandı (HTTP 200) |
| AVAudioCompressedBuffer sıkıştırılmış veri tamponudur | https://developer.apple.com/documentation/avfaudio/audiocompressedbuffer | 2026-10-12 | Doğrulandı (HTTP 200) |
| AVAudioTime iki zaman ölçeğini bağlar | https://developer.apple.com/documentation/avfaudio/audiotime | 2026-10-12 | Doğrulandı (HTTP 200) |
| Web Audio AudioContext zamanlama ve tampon yönetir | https://developer.mozilla.org/en-US/docs/Web/API/AudioContext | 2026-10-12 | Doğrulandı (HTTP 200) |
| AudioWorklet gerçek zamanlı işleyici çalıştırır | https://developer.mozilla.org/en-US/docs/Web/API/AudioWorklet | 2026-10-12 | Doğrulandı (HTTP 200) |
| BaseAudioContext ortak temel davranışları tanımlar | https://developer.mozilla.org/en-US/docs/Web/API/BaseAudioContext | 2026-10-12 | Doğrulandı (HTTP 200) |

Not: MDN satırları webAudio karşılaştırması içindir; iOS gerçek zamanlı yolu
AVAudioEngine/RemoteIO'dur (bkz. `ios-audio-unit-arayuzu.md`).

## §12 Açık Sorular

1. 512 örnek @48 kHz hedefinde gerçek callback süresi ölçülmedi (`UNKNOWN`).
2. False sharing A/B ölçümü yapılmadı.
3. Underrun kurtarma stratejisi (fade vs drop) seçilmedi.
4. iOS donanım IO boyutu cihaza göre bilinmiyor (`UNKNOWN`).
5. Ring buffer kapasite seçimi için proje bazlı ölçüm yok.
6. Bütçe kullanım şeritlerinin (§5.1) gerçekçi eşikleri cihaza göre değişir mi?
   Ölçülmemiştir.
7. SPSC yerine kilitli kuyruk kullanılırsa ölçülen fark nedir? Ölçülmemiştir.

## §13 Terim Sözlüğü

| Terim | Anlam |
|---|---|
| Jitter | Zamanlamadaki dalgalanma |
| Underrun | Veri yetmezliği kaynaklı kesinti |
| Overrun | Tampon taşması |
| Ring buffer | Dairesel kuyruk (SPSC) |
| False sharing | Aynı cache hattında gereksiz geçiş |
| Hysteresis | Eşik geçişinde gecikmeli tepki |
| Bütçe kullanımı | Callback süresi / tampon süresi |

## §13.5 Ölçüm Protokolü

| ID | Ölçüm | Yöntem | Beklenen |
|---|---|---|---|
| M1 | Callback süresi | hostTime başlangıç-bitiş | Bütçenin altında `UNKNOWN` |
| M2 | Toplam gecikme | Uçtan uca kayıt-çıkış | Cihaza göre `UNKNOWN` |
| M3 | Underrun sayısı | Sayaç (lock-free atomik) | 0 hedefi |
| M4 | Jitter | Callback süresi std sapması | Ölçülmedi |
| M5 | False sharing A/B | Sayaç hizası deneyi | Ölçülmedi |
| M6 | 512 vs 256 örnek | Bütçe kullanımı karşılaştırması | Ölçülmedi |
| M7 | Bellek pad | alignas(64) doğrulama | Statik test |
| M8 | SPSC stres | İki iş parçacığı doğruluk | Veri bozulmaz |

## §14 Çapraz Referans

| Konu | Gidilecek dosya |
|---|---|
| Callback ve IO | `ios-audio-unit-arayuzu.md` |
| ASBD ve bufferList | `ios-core-audio-ayrintisi.md` |
| Motor ve kuyruk | `ios-avfoundation-semasi.md` |
| Session ve öncelik | `ios-cihaz-ve-rota-yonetimi.md` |

## §15 Sınır Dışı

Ağ jitter'i ve sunucu tamponlaması ayrı dosyanın (`ios-cihaz-ve-rota-yonetimi.md`)
kapsamındadır. Disk IO gecikmesi, codec gecikmesi ve UI katmanı gecikmesi bu
dosyada sayılmaz. Android/WASAPI karşılaştırmaları bu dosyaya girmez.

## §16 Kırmızı Bayraklar

| ID | Bayrak | Etki |
|---|---|---|
| F1 | Hiçbir gecikme değeri ölçülmedi | Tüm sayısal iddialar `UNKNOWN` |
| F2 | Sartname 512/48k "PLANNED" | Ölçüm masası kurulmalı |
| F3 | Kurtarma stratejisi seçilmedi | underrun davranışı belirsiz |
| F4 | IO boyutu cihaza göre bilinmiyor | Bütçe hesabı kısmi |
| F5 | False sharing ölçümü yok | pad kuralı gerekçeli ama kanıtsız |
| F6 | MDN satırları iOS gerçek zamanlı yolu değil, webAudio karşılaştırması | Karışıklık riski |
| F7 | SPSC bellek sıralama bayrakları kodda uygulanmadı (vault'ta kod yok) | Tasarım düzeyinde |
| F8 | 96 kHz senaryosu sartname dışı | Tablo yalnız aritmetik |
| F9 | Kurtarma fade uzunluğu seçilmedi | Belirsiz |
| F10 | Ölçüm sayaçlarının kendisi gerçek zamanlı davranışa etkisi ölçülmedi | Ölçüm meta-riski |

## §17 Varsayım Günlüğü

| ID | Varsayım | Dayanak | Durum |
|---|---|---|---|
| A1 | IO buffer çift tampondur | Donanım davranışı | Destekli |
| A2 | Bütçe = tampon süresi | Zamanlama teorisi | Destekli |
| A3 | SPSC tek üretimci/tüketici ile kilit-size | Kilit-free literatür | Destekli |
| A4 | Güçlü-2 kapasite maskeleme sağlar | Mod aritmetiği | Destekli |
| A5 | 64 bayt hattı pad'laması yeterli | Donanım mimarisi | Kısmi (ölçülmedi) |

## §18 Doğrulama Planı

| # | Adım | Kabul |
|---|---|---|
| 1 | Callback süre sayaçlarını kur | Lock-free doğrulandı |
| 2 | 512/48k bütçe kullanımını ölç | Değer kayıt altında |
| 3 | Underrun sayacını zorla (boş kuyruk) | Sayaç arttı |
| 4 | alignas(64) hizasını statik test | Hat uyumu |
| 5 | SPSC stres testi 60 sn | Bozulma yok |
| 6 | M1–M8 tablosu dolduruldu | Kayıt altında |
| 7 | 6 URL tekrar 200 kontrolü | Kanıt matrisi canlı |
| 8 | Repo satırları tekrar okundu | Satır uyuşması |
| 9 | False sharing A/B deneyi | Fark raporlandı |
| 10 | 256 vs 512 bütçe karşılaştırması | Karar raporu |

## §19 Sık Sorulanlar

**S: En küçük tampon her zaman en iyisi midir?**
C: Hayır; interrupt oranı ve bütçe daralır — dengelenmelidir (§4).

**S: Lock-free neden zorunlu?**
C: Kilit, gerçek zamanlı öncelik terslemesi üretir (L0 kural 2).

**S: Jitter nereden gelir?**
C: İşletim sistemi, diğer iş parçacıkları ve donanım; ayrı ayrı ölçülür (M4).

**S: Ölçüm sonuçları nereye yazılır?**
C: §13.5 tablosuna cihaz bilgisiyle.

**S: Ring buffer neden güç-2 kapasite ister?**
C: Mod işlemi maskeyle yapılır; bu da kapasitenin 2^n olmasını gerektirir (§7).

**S: Underrun olduğunda ilk tepki ne olmalı?**
C: Bu vault'ta strateji seçilmedi (§6); ölçüm sonrası karar verilmelidir.

**S: alignas(64) her cihazda yeterli mi?**
C: 64 bayt tipik hat boyutudur; bu vault'ta cihaz bazlı doğrulama yapılmadı →
`VERIFICATION REQUIRED`.

## §20 Katmanlar

| Katman | Bu dosyadaki karşılığı |
|---|---|
| Uygulama | API testleri (repo kanıtı) |
| Çerçeve | AVAudioPCMBuffer |
| Motor | Ring buffer + kuyruk |
| Çekirdek | IO buffer + callback |
| Donanım | Interrupt oranı ve hat hizası |

## §21 OBU Halkaları

| Halka | İçerik |
|---|---|
| Çekirdek | Bütçe ve underrun |
| Uzantı 1 | Ring buffer matematiği |
| Uzantı 2 | Cache hattı |
| Uzantı 3 | Ölçüm protokolü |
| Uzantı 4 | Kurtarma stratejisi |

## §22 Karşılaştırma

| Kriter | 256 örnek | 512 örnek |
|---|---|---|
| Süre (@48k) | 5.33 ms | 10.67 ms |
| Interrupt oranı | Daha yüksek | Daha düşük |
| Bütçe payı | Dar | Geniş |
| Ölçüm | Ölçülmedi | Ölçülmedi (proje hedefi) |

### §22.1 Kurtarma Stratejisi Seçenekleri

| Strateji | Davranış | Artı | Eksi |
|---|---|---|---|
| Sessiz sıfır yaz | Boşluk sessiz | Basit | Ani geçiş (pop riski) |
| Fade-out | Son değere rampa | Pop azaltır | Bütçe ekler |
| Son örnek tutma | Değer dondurulur | Ucuz | DC ofset riski |
| Blok atlama | Veri atlanır | Kurtarır | Kesinti duyulur |

Bu vault'ta strateji seçilmedi (§6, F3); seçim ölçüm sonrası karardır.

### §22.2 Ölçüm Araçları Karşılaştırması

| Araç | Ne ölçer | iOS'ta durum |
|---|---|---|
| hostTime sayaçları | Callback süresi | Uygulanabilir (tasarım) |
| Instruments | CPU ve alloc | Ölçülmedi |
| Sayaç atomikleri | Underrun/asım | Uygulanabilir (tasarım) |
| Harici kayıt | Uçtan uca gecikme | Ölçülmedi |

## §23 Riskler

| ID | Risk | Etki |
|---|---|---|
| R1 | Bütçe aşımı → kırılma | Yüksek |
| R2 | Alloc callback içinde → tersleme | Yüksek |
| R3 | Kilit kullanımı → tersleme | Yüksek |
| R4 | False sharing → gizli jitter | Orta |
| R5 | Ölçümsüz gecikme iddiası | Yüksek |
| R6 | Kapasite-güvenlik payı seçimi kanıtsız | Orta |
| R7 | Sıra bayrağı ihlali → yarım veri | Yüksek |
| R8 | Ölçüm sayaçları bütçeyi şişirir | Orta |
| R9 | IO boyutu değişince bütçe hesabı bozulur | Orta |
| R10 | Sartname dışı hız (96 kHz) varsayımı | Düşük |

## §24 Ölçüm

| Metrik | Değer |
|---|---|
| Dosya satır hedefi | 500+ |
| Bölüm sayısı | 24 |
| URL sayısı | 6 (hepsi 2026-10-12 200) |
| Repo kanıtı | 5 satır (RouteTableTest, EmbeddedBff) |
| Ölçüm | Ölçülmedi (M1–M8 planlandı) |

### §24.1 Kanıt Zinciri Özeti

| Kaynak | Miktar | Durum |
|---|---|---|
| Resmi doküman (Apple + MDN) | 6 URL | Doğrulandı (2026-10-12, HTTP 200) |
| Repo kod satırı | 5 satır | Doğrulandı |
| Ölçüm protokolü | M1–M8 | Planlandı, cihaz bekleniyor |
| Açık sorular | 7 | Açık |
| Kırmızı bayrak | 10 | Aktif (F1–F10) |
| Varsayım günlüğü | 5 | A1–A5: 4 destekli, 1 kısmi |
| Çapraz referans | 4 dosya | ios/ altındaki kardeşler |
| Sınır dışı | 3 başlık | Ağ, disk, codec ayrı ölçüm |
| Ölçüm şeridi | 5 şerit | §5.1 bütçe kullanımı |
| Kapasite tablosu | 4 satır | §7.2 aritmetik hesap |
| Karar matrisi | 4 satır | §4.2 boyut seçimi |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode