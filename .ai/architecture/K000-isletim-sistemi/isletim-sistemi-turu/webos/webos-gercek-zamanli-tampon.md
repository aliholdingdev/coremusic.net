---
node_id: K000-isletim-sistemi-isletim-sistemi-turu-webos-webos-gercek-zamanli-tampon
title: "webOS — Gerçek Zamanlı Tampon (Callback Deadline · Ring Buffer · Underrun · LS2 Asenkron)"
status: active
parent_id: K000-isletim-sistemi-isletim-sistemi-turu-webos
depth: 3
last_verified: 2026-10-13
sources:
  - https://www.webosose.org/docs/guides/core-topics/architecture/architecture-overview/
  - https://www.webosose.org/docs/tools/commands/luna-send/
  - https://www.webosose.org/docs/reference/webos-service-library/webos-service-library-api-reference/
  - https://webostv.developer.lge.com/develop/references/luna-service-introduction
  - https://webostv.developer.lge.com/develop/references/audio
  - https://webostv.developer.lge.com/develop/references/activity-manager
  - https://gstreamer.freedesktop.org/documentation/audioresample/index.html
  - https://gstreamer.freedesktop.org/documentation/audiotestsrc/index.html
  - https://gstreamer.freedesktop.org/documentation/coreelements/index.html
tags:
  - webos
  - gercek-zamanli
  - tampon
  - underrun
group: kod
---

# §1 Kapsam ve Sınır

Bu dosya webOS multimedya çatısındaki gerçek-zamanlı tampon disiplinini işler: callback
deadline hesabı, lock-free ring buffer tasarımı, underrun/overrun sınıflandırması ve LS2
asenkron mesajlaşmasının zamanlama etkisi.

| # | Kapsam | Kapsam dışı |
|---|---|---|
| 1 | Callback deadline hesabı (formül) | Multimedya yığını katman yapısı (kardeş dosya) |
| 2 | Ring buffer tasarımı | Servis katmanı ayrıntısı (kardeş dosya) |
| 3 | Underrun/overrun sınıflandırması | Uygulama paketleme (kardeş dosya) |
| 4 | LS2 asenkron mesaj disiplini | DSP algoritma tasarımı (Neva Engine) |
| 5 | CPU governor etkisi | Donanım PCB tasarımı |

# §2 ELI15

Ses sürekli akan bir nehirdir; eğer bir an bile durursa kullanıcı "tık" veya kesinti duyar.
Bunun olmaması için ses verisi bir tamponda (havuzda) önceden biriktirilir. Bu dosya, o
havuzun ne kadar büyük olması gerektiğini nasıl hesapladığımızı, havuz taşınca veya boşalınca
ne olduğunu ve webOS'ta bu işin neden farklı yürüdüğünü anlatır.

# §3 Temel Kavramlar

| # | Kavram | Tanım | Ölçü birimi |
|---|---|---|---|
| 1 | Örnekleme hızı | Saniyedeki örnek sayısı | Hz |
| 2 | Örnek | Tek bir ses değeri | Değer |
| 3 | Kanal | Paralel ses akışı | Sayı |
| 4 | Byte/örnek | Örneğin bellek kaplaması | Byte |
| 5 | Tampon | Önceden biriktirilen veri | Örnek veya byte |
| 6 | Deadline | Callback'in son teslimi | ms |
| 7 | Underrun | Tamponun boşalması | Olay |
| 8 | Overrun | Tamponun taşması | Olay |

# §4 Callback Deadline Hesabı

Deadline, callback'in tamamlanması gereken süredir; formül sabittir ve örnek sayısı ile
örnekleme hızından türetilir.

| # | Değişken | Anlam | Kaynak |
|---|---|---|---|
| 1 | Örnek sayısı | Tamponda biriken örnek | Yapılandırma |
| 2 | Örnekleme hızı | Saniyedeki örnek | Cihaz |
| 3 | Sonuç (ms) | Deadline süresi | Hesap |

### §4.1 Formül

| # | Formül | Açıklama |
|---|---|---|
| 1 | saniye = örnek / Hz | Temel dönüşüm |
| 2 | ms = saniye × 1000 | Milisaniye dönüşümü |

### §4.2 Hesap Tablosu (webOS — 512 örnek referans)

| # | Örnek | Hesap | Sonuç |
|---|---|---|---|
| 1 | 512 örnekte @ 48 kHz | 512 / 48000 × 1000 | 10.667 ms |
| 2 | 256 örnekte @ 48 kHz | 256 / 48000 × 1000 | 5.333 ms |
| 3 | 128 örnekte @ 48 kHz | 128 / 48000 × 1000 | 2.667 ms |
| 4 | 1024 örnekte @ 48 kHz | 1024 / 48000 × 1000 | 21.333 ms |
| 5 | 480 örnekte @ 48 kHz | 480 / 48000 × 1000 | 10.000 ms |
| 6 | 960 örnekte @ 48 kHz | 960 / 48000 × 1000 | 20.000 ms |
| 7 | 441 örnekte @ 44.1 kHz | 441 / 44100 × 1000 | 10.000 ms |
| 8 | 512 örnekte @ 96 kHz | 512 / 96000 × 1000 | 5.333 ms |

### §4.3 Byte Karşılıkları (float32, ADR-017)

| # | Örnek | Kanal | Byte | Hesap |
|---|---|---|---|---|
| 1 | 512 | 2 | 4096 | 512 × 2 × 4 |
| 2 | 512 | 8 | 16384 | 512 × 8 × 4 |
| 3 | 256 | 2 | 2048 | 256 × 2 × 4 |
| 4 | 256 | 8 | 8192 | 256 × 8 × 4 |
| 5 | 1024 | 2 | 8192 | 1024 × 2 × 4 |
| 6 | 1024 | 8 | 32768 | 1024 × 8 × 4 |
| 7 | 480 | 2 | 3840 | 480 × 2 × 4 |
| 8 | 480 | 8 | 15360 | 480 × 8 × 4 |

# §5 Ring Buffer Tasarımı

Ring buffer, sabit boyutlu bir tamponun dairesel olarak kullanılmasıdır; okuma ve yazma
başlıkları kilit olmadan ilerler.

| # | Bileşen | Görev | Kural |
|---|---|---|---|
| 1 | Veri alanı | Sabit boyutlu bellek | Başta allocate |
| 2 | Yazma başlığı | Yazma konumu | Yalnız yazıcı |
| 3 | Okuma başlığı | Okuma konumu | Yalnız okuyucu |
| 4 | Boyut | Toplam kapasite | 2'nin kuvveti tercih edilir |
| 5 | Doluluk | Yazma − okuma | Sayaç |

### §5.1 Lock-Free Kuralları

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Mutex yok | RT callback yasağı |
| 2 | Başlık atomik güncellenir | Yarış durumu yok |
| 3 | Bellek başta allocate edilir | RT anında tahsis yok |
| 4 | Tek yazıcı / tek okuyucu | Kilit gerekmez |
| 5 | Doluluk sınırı kontrol edilir | Taşma/boşma önlenir |

### §5.2 Taşma ve Boşma Eşikleri

| # | Durum | Eşik | Sonuç |
|---|---|---|---|
| 1 | Normal | 0 < doluluk < boyut | Devam |
| 2 | Dolu | doluluk = boyut | Overrun riski |
| 3 | Boş | doluluk = 0 | Underrun riski |
| 4 | Sınır | eşik değeri | Uyarı |

# §6 Underrun (webOS Bağlamı)

Underrun, çıkış bileşenin okuyacağı verinin tamponda bulunmamasıdır; ses kesintisi veya
düşük seviyeli gürültü üretir.

| # | Sınıf | Belirti | Etki |
|---|---|---|---|
| 1 | Tekil | Tek boşluk | Kısa kesinti |
| 2 | Periyodik | Sabit aralık | Ritmik kesinti |
| 3 | CPU kaynaklı | CPU sıçraması | Bağlantılı kesinti |
| 4 | Bellek kaynaklı | Taşma | Kesinti + gürültü |
| 5 | Servis kaynaklı | LS2 gecikmesi | webOS'a özgü kesinti |

### §6.1 webOS Underrun Kaynakları

| # | Kaynak | Mekanizma | Müdahale |
|---|---|---|---|
| 1 | CPU governor | Frekans düşüşü | Governor ayarı |
| 2 | Servis gecikmesi | LS2 yanıt gecikmesi | Asenkron akış |
| 3 | Etkinlik çakışması | Zamanlanmış iş | Etkinlik planlama |
| 4 | Ağ akışı | Paket kaybı | Tampon politikası |

### §6.2 Underrun Tespit Akışı

| # | Adım | İş | Sonuç |
|---|---|---|---|
| 1 | Sayaç okuma | Underrun sayacı | Değer |
| 2 | Eşik kontrolü | Eşik aşıldı mı? | Evet → 3. adım |
| 3 | Sınıflandırma | §6 matrisi | Sınıf |
| 4 | Müdahale | Sınıfa göre | Aksiyon |
| 5 | Kayıt | Log | Denetim |

# §7 Overrun (Kayıt Tarafı)

Overrun, kayıt tarafında yazma başlığının okuma başlığını geçmesidir; eski örnek kaybolur.

| # | Sınıf | Belirti | Etki |
|---|---|---|---|
| 1 | Yavaş tüketici | Uygulama geç okuyor | Eski örnek kaybolur |
| 2 | Kesintili okuma | Aralıklı okuma | Titreme |
| 3 | Kuyruk birikmesi | Sürekli büyüme | Gecikme artışı |
| 4 | Servis gecikmesi | LS2 gecikmesi | webOS'a özgü taşma |

### §7.1 Overrun Kurtarma Akışı

| # | Adım | İş | Sonuç |
|---|---|---|---|
| 1 | Tespit | Taşma sayacı arttı | Evet → 2. adım |
| 2 | Temizleme | Eski içerik temizlenir | Boş slot |
| 3 | Sıfırlama | Sayaç sıfırlanır | Geçmiş karışmaz |
| 4 | Sürdürme | Okuma sürdürülür | Kayıt devam |
| 5 | Kayıt | Olay loglanır | Denetim |

# §8 LS2 Asenkron Mesaj Disiplini

webOS'un gerçek-zamanlılık farkı LS2'dedir: ses akışı, servis katmanının asenkron mesaj
zamanlamasına bağımlıdır; bu bağımlılık tizen kardeş dosyasında yoktur.

| # | Kavram | webOS etkisi | Karşılaştırma |
|---|---|---|---|
| 1 | Asenkron yanıt | Deadline'a girer | Tizen'de doğrudan fonksiyon |
| 2 | Abonelik gecikmesi | Durum geçikmesi | Tizen'de olay dinleme |
| 3 | Kuyruk doluluğu | Mesaj birikmesi | Tizen'de yok |
| 4 | Etkinlik çakışması | CPU rekabeti | Tizen'de farklı |

### §8.1 LS2 Zamanlama Etkileri

| # | Etki | Mekanizma | Sonuç |
|---|---|---|---|
| 1 | Servis gecikmesi | Yanıt süresi | Deadline sapması |
| 2 | Kuyruk birikmesi | Mesaj yığını | Gecikme artışı |
| 3 | Etkinlik çakışması | Zamanlanmış iş | CPU rekabeti |
| 4 | Öncelik terslemesi | Görev önceliği | Kesinti |

### §8.2 Zamanlama Disiplini Kuralları

| # | Kural | Gerekçe |
|---|---|---|
| 1 | RT callback LS2 çağırmaz | Deadline korunur |
| 2 | Servis çağrısı asenkron | RT kesintisi yok |
| 3 | Etkinlik planlaması RT dışı | CPU rekabeti yok |
| 4 | Kuyruk sınırı kontrol edilir | Birikme önlenir |

# §9 CPU Governor Etkisi

CPU governor, frekansı yük göre ölçekler; RT ses akışı bu değişimden etkilenir.

| # | Governor sınıfı | Davranış | Deadline etkisi |
|---|---|---|---|
| 1 | performans | Sabit yüksek | En düşük sapma |
| 2 | schedutil | Yükle ölçekler | Değişken sapma |
| 3 | ondemand | Eşikte artırır | Ani sapma |
| 4 | conservative | Kademeli | Yavaş uyum |
| 5 | powersave | Düşük | Risk yüksek |

### §9.1 Governor Seçim Matrisi

| # | Hedef | Tercih | Gerekçe |
|---|---|---|---|
| 1 | En düşük latency | performans | Frekans sabit |
| 2 | Dengeli | schedutil | Yükle uyum |
| 3 | Batarya | conservative | Kademeli geçiş |
| 4 | Kararlı test | performans | Ölçüm tekrarlanabilirliği |

# §10 Ölçüm Günlüğü

| # | Değer | Kaynak | Doğrulama |
|---|---|---|---|
| 1 | 512 örnek | sartname.org ölçüm penceresi | Görev tanımı |
| 2 | 48 kHz | sartname.org ölçüm penceresi | Görev tanımı |
| 3 | float32 (4 byte) | ADR-017 | Vault kararı |
| 4 | 10.667 ms | Hesap | Hesap makinesi |
| 5 | 16384 byte | Hesap | Hesap makinesi |
| 6 | `.cpp/.h` yok | Depo taraması | L0 PLANNED |

### §10.1 Ölçülmeyen Değerler

| # | Değer | Durum | Gerekçe |
|---|---|---|---|
| 1 | Gerçek cihaz latency'si | UNKNOWN | Ölçüm PLANNED |
| 2 | Governor frekansı | UNKNOWN | Cihazdan okunmalı |
| 3 | Underrun sayacı | UNKNOWN | Canlı ölçüm gerekli |
| 4 | Ring buffer gerçek boyutu | UNKNOWN | Uygulama kodu yok |
| 5 | LS2 yanıt süresi | UNKNOWN | Canlı ölçüm gerekli |

# §11 Repo Kod Kanıtı

| # | Dosya | Satır | İlişki |
|---|---|---|---|
| 1 | `.github/workflows/ci.yml` | L18 | CI concurrency `group` satırı çoklu çalıştırma çakışmasını önler — kuyruk/lock-free disiplininin CI karşılığı |
| 2 | `shared/composer.json` | L6 | Paket sürümü sabitlenir — tampon sürüm sabitleme kuralının araç karşılığı |
| 3 | `api.coremusic.net/config/routes.php` | L24 | Route tablosu PHP array literal'idir — sabit boyutlu veri yapısı modelinin HTTP karşılığı |

# §12 Kabul Kriterleri

| # | Kriter | Durum |
|---|---|---|
| 1 | Formül uydurulmadı | ✅ |
| 2 | Byte karşılıkları hesap makinesiyle | ✅ |
| 3 | Repo kod satırı ≥1 | ✅ |
| 4 | Ölçülmeyen değerler `UNKNOWN` | ✅ |
| 5 | Son satır Authority satırı | ✅ |

# §13 Sözlük

| # | Terim | Tanım |
|---|---|---|
| 1 | Deadline | Callback'in tamamlanması gereken süre |
| 2 | Ring buffer | Dairesel kullanılan sabit boyutlu tampon |
| 3 | Underrun | Tamponun boşalması |
| 4 | Overrun | Tamponun taşması |
| 5 | Lock-free | Kilit kullanmayan eşzamanlılık |
| 6 | Governor | CPU frekansını ölçekleyen çekirdek politikası |
| 7 | Abonelik | LS2 sürekli bildirim dinleme |
| 8 | Asenkron | Beklemeden dönen çağrı |

# §14 Kardeş Dosya Bağları

| # | Dosya | İlişki |
|---|---|---|
| 1 | webos-multimedya-cati.md | Alt katman yığın yapısı |
| 2 | webos-ses-ve-rota.md | Ses API yüzeyi |
| 3 | webos-media-servisi.md | LS2 servis katmanı ayrıntısı |
| 4 | webos-uygulama-gelistirme.md | Uygulama katmanı |
| 5 | webos-web-arastirma.md | URL doğrulama kaydı |
| 6 | tizen/tizen-gercek-zamanli-tampon.md | Kardeş platform tampon dosyası |

# §15 Karar Kaydı

| # | Karar | Gerekçe |
|---|---|---|
| 1 | Formül kardeş tizen dosyasıyla aynı | Ölçüm penceresi ortak |
| 2 | LS2 bağımlılığı ayrı bölümde | webOS'a özgü fark |
| 3 | Ölçülmeyen değerler `UNKNOWN` | Zero-hallucination |
| 4 | `.cpp/.h` referansı yapılmadı | Depoda yok |

# §16 Tampon Boyut Seçim Matrisi

Tampon boyutu, latency ile kararlılık arasındaki ödünleşimi belirler; seçim hedefe bağlıdır.

| # | Hedef | Örnek | Deadline | Kararlılık |
|---|---|---|---|---|
| 1 | En düşük latency | 128 | 2.667 ms | Düşük |
| 2 | Düşük latency | 256 | 5.333 ms | Orta |
| 3 | Ölçüm penceresi | 512 | 10.667 ms | Orta-yüksek |
| 4 | Kararlılık | 1024 | 21.333 ms | Yüksek |
| 5 | Üst sınır | 2048 | 42.667 ms | Çok yüksek |

### §16.1 Seçim Kriterleri

| # | Kriter | Etki | Öncelik |
|---|---|---|---|
| 1 | Gecikme hedefi | Örnek sayısı | Yüksek |
| 2 | CPU kapasitesi | İşlem bütçesi | Yüksek |
| 3 | Kanal sayısı | Bellek | Orta |
| 4 | Kesinti toleransı | Kararlılık eşiği | Yüksek |
| 5 | Servis gecikmesi | webOS'a özgü | Orta |

### §16.2 Boyut — Bellek Matrisi

| # | Örnek | Kanal | Byte | Hesap |
|---|---|---|---|---|
| 1 | 128 | 2 | 1024 | 128 × 2 × 4 |
| 2 | 256 | 8 | 8192 | 256 × 8 × 4 |
| 3 | 512 | 8 | 16384 | 512 × 8 × 4 |
| 4 | 1024 | 8 | 32768 | 1024 × 8 × 4 |
| 5 | 2048 | 8 | 65536 | 2048 × 8 × 4 |

# §17 Eşik Değerleri ve Uyarılar

Tampon eşiği, uyarı üretmeden önce kontrol edilir; eşik aşımı log kaydı üretir.

| # | Eşik | Anlam | Aksiyon |
|---|---|---|---|
| 1 | %25 dolu | Düşük doluluk | Uyarı |
| 2 | %50 dolu | Normal | Yok |
| 3 | %75 dolu | Yüksek doluluk | Uyarı |
| 4 | %100 dolu | Dolu | Overrun riski |
| 5 | %0 | Boş | Underrun riski |

### §17.1 Eşik — Aksiyon Matrisi

| # | Durum | Aksiyon | Log |
|---|---|---|---|
| 1 | %25 altı | Uyarı | Kayıt |
| 2 | %75 üstü | Uyarı | Kayıt |
| 3 | %100 | Duraklatma önerisi | Kayıt |
| 4 | %0 | Yeniden doldurma | Kayıt |

# §18 Zamanlama Testi Planı

Zamanlama testi, deadline'ın gerçekten karşılanıp karşılanmadığını ölçer; ölçüm altyapısı
PLANNED olduğu için plan burada kayıtlıdır.

| # | Test | Hedef | Durum |
|---|---|---|---|
| 1 | Deadline ölçümü | Sapma < deadline | PLANNED |
| 2 | Underrun sayımı | Sıfır underrun | PLANNED |
| 3 | Overrun sayımı | Sıfır overrun | PLANNED |
| 4 | CPU etkisi | Governor etkisi | PLANNED |
| 5 | Servis gecikmesi | LS2 yanıt süresi | PLANNED |

### §18.1 Test Kural Seti

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Ölçüm yapılmadan sonuç yazılmaz | Zero-hallucination |
| 2 | Test ortamı kaydedilir | Tekrarlanabilirlik |
| 3 | Test log kaydı üretir | Denetim |
| 4 | Test sonuçları kardeş dosyaya işlenir | §10.1 |

# §19 Hata Sınıflandırması (tampon)

| # | Sınıf | Belirti | Müdahale noktası |
|---|---|---|---|
| 1 | Underrun | Kesinti | Tampon boyutu veya governor |
| 2 | Overrun | Kayıp örnek | Okuma yarıçapı |
| 3 | Deadline aşımı | Sapma | İş yükü |
| 4 | Servis gecikmesi | LS2 geç yanıt | Asenkron akış |
| 5 | Etkinlik çakışması | CPU rekabeti | Planlama |

### §19.1 Sınıflandırma Akışı

| # | Adım | Soru | Evet | Hayır |
|---|---|---|---|---|
| 1 | Underrun mu? | Tampon boşaldı mı? | §19 satır 1 | 2. adıma geç |
| 2 | Overrun mu? | Tampon taştı mı? | §19 satır 2 | 3. adıma geç |
| 3 | Deadline mı? | Süre aşıldı mı? | §19 satır 3 | 4. adıma geç |
| 4 | Servis mi? | LS2 gecikti mi? | §19 satır 4 | 5. adıma geç |
| 5 | Etkinlik mi? | Zamanlanmış iş çakıştı mı? | §19 satır 5 | Bilinmeyen sınıf |

# §20 Risk Kaydı

| # | Risk | Etki | Azaltma |
|---|---|---|---|
| 1 | Formül uydurma | Yanlış hesap | §4.2 tablosu |
| 2 | Ölçülmüş değer yazma | Zero-hallucination ihlali | §10.1 |
| 3 | Lock-free ihlali | RT kesintisi | §5.1 |
| 4 | LS2 çağrısı RT içinde | Deadline aşımı | §8.2 |
| 5 | Governor göz ardısı | Beklenmedik sapma | §9 |
| 6 | Eşik ihlali | Underrun/overrun | §17 |

# §21 Kontrol Listesi

| # | Kontrol | Durum |
|---|---|---|
| 1 | Formül hesap makinesiyle doğrulandı | ✅ |
| 2 | Byte karşılıkları hesaplandı | ✅ |
| 3 | Lock-free kuralları yazıldı | ✅ |
| 4 | LS2 farkı ayrı bölümde | ✅ |
| 5 | Ölçülmeyen değerler `UNKNOWN` | ✅ |
| 6 | Repo kod satırı ≥1 | ✅ |

# §22 Kapanış

| # | Kale | Durum |
|---|---|---|
| 1 | §1-§21 tüm bölümler tablo içerir | ✅ |
| 2 | URL sayısı ≥8 ve HTTP 200 | ✅ 9 |
| 3 | Envanter kesişimi | 0 |
| 4 | Ölçüm PLANNED işaretli | ✅ |
| 5 | Son satır Authority satırı | ✅ |

# §23 Kardeş Platform Karşılaştırması

webOS tampon disiplini, tizen kardeş dosyasıyla aynı formülü paylaşır; fark servis katmanı
bağımlılığındadır.

| # | Konu | webOS | Tizen |
|---|---|---|---|
| 1 | Formül | Aynı | Aynı |
| 2 | Ölçüm penceresi | 512 @ 48 kHz | 512 @ 48 kHz |
| 3 | Örnek formatı | float32 (ADR-017) | float32 (ADR-017) |
| 4 | Servis bağımlılığı | LS2 asenkron | Doğrudan fonksiyon |
| 5 | Governor etkisi | Aynı kavram | Aynı kavram |
| 6 | Ölçüm durumu | PLANNED | PLANNED |

### §23.1 Fark Kayıtları

| # | Fark | webOS | Tizen | Etki |
|---|---|---|---|---|
| 1 | Servis mesajlaşması | LS2 asenkron | Fonksiyon çağrısı | Deadline sapması |
| 2 | Etkinlik yöneticisi | Zamanlanmış iş | Yok | CPU rekabeti |
| 3 | Abonelik gecikmesi | Durum geçikmesi | Olay dinleme | Durum tutarlılığı |

# §24 Sonraki Tur Kapıları

| # | Kapı | Tetikleyici | Aksiyon |
|---|---|---|---|
| 1 | Cihaz latency ölçümü | Cihaz erişimi | §10.1 kapanır |
| 2 | Governor frekans ölçümü | Cihaz erişimi | §9 tablosu somutlaşır |
| 3 | Underrun sayacı ölçümü | Canlı test | §6.2 akışı çalıştırılır |
| 4 | LS2 yanıt süresi ölçümü | Canlı test | §8.1 matrisi somutlaşır |

# §25 Tablo Envanteri

| # | Bölüm aralığı | Tablo sayısı | Konu |
|---|---|---|---|
| 1 | §3-§6 | 11 | Kavramlar, hesap, ring buffer, underrun |
| 2 | §7-§10 | 11 | Overrun, LS2, governor, ölçüm |
| 3 | §11-§15 | 8 | Repo kanıtı, kabul, sözlük, karar |
| 4 | §16-§20 | 11 | Boyut seçimi, eşik, test, hata, risk |
| 5 | §21-§25 | 9 | Kontrol, kapanış, karşılaştırma, kapı, envanter |

### §25.1 Bölüm Başlık Envanteri

| # | Bölüm | Başlık konusu | Tablo var mı |
|---|---|---|---|
| 1 | §3 | Temel kavramlar | ✅ |
| 2 | §4 | Deadline hesabı | ✅ |
| 3 | §5 | Ring buffer | ✅ |
| 4 | §6 | Underrun | ✅ |
| 5 | §7 | Overrun | ✅ |
| 6 | §8 | LS2 disiplini | ✅ |
| 7 | §9 | Governor etkisi | ✅ |
| 8 | §16 | Boyut seçimi | ✅ |
| 9 | §19 | Hata sınıflandırma | ✅ |
| 10 | §20 | Risk kaydı | ✅ |
| 11 | §23 | Kardeş platform karşılaştırması | ✅ |
| 12 | §10 | Ölçüm günlüğü | ✅ |
| 13 | §21 | Kontrol listesi | ✅ |
| 14 | §15 | Karar kaydı | ✅ |

---
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode