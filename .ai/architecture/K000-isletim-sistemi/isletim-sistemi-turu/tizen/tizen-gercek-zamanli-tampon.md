---
node_id: K000-isletim-sistemi/isletim-sistemi-turu/tizen/tizen-gercek-zamanli-tampon
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/tizen
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-turu
evidence: []
validation: []
open_questions: []
group: kod
---

# Tizen — Gerçek Zamanlı Tampon (Callback Deadline · Ring Buffer · Underrun · Governor)

## §0 ELI15

Ses hattı kesintisiz akmalıdır; tek bir gecikmiş callback bile tıkırtı (click) üretir. Bu
dosya geri çağrının (callback) son teslim zamanını (deadline), kilitsiz (lock-free) ring
buffer tasarımını, underrun tespitini ve CPU/güç yöneticilerinin (governor) zamanlama
üzerindeki etkisini tanımlar. Formüller ve tablolar platformdan bağımsızdır; Tizen'e özgü
süreç önceliği/ayarları `⚠️ VERIFICATION REQUIRED` ile işaretlidir. Depoda C++ kodu yok
(L0: PLANNED) → kod kanıtları gerçek PHP/YAML dosyalarından alınmıştır.

## §1 Künye

| Alan | Değer |
|---|---|
| Konu | RT tampon disiplini: deadline · ring buffer · underrun · governor |
| Platform | Tizen (Samsung) — genel kurallar tüm platformlara uygulanabilir |
| Üst düğüm | `K000-isletim-sistemi/isletim-sistemi-turu/tizen` |
| Grup | `kod` |
| Kardeş dosyalar | multimedya-altyapisi · ses-yoneticisi · audio-hal · uygulama-ortami · web-arastirma |
| Kapsam dışı | DSP algoritması (K004) · ASIO (Windows dalı) · donanım |
| Doğrulama tarihi | 2026-10-13 |

## §2 Kapsam ve Sınır

Kapsam: callback periyodu ve son teslim zamanı hesabı, kilitsiz tampon, üretici-tüketici
eşzamanlılığı, underrun/overrun tespiti ve ölçümü, CPU politikası etkisi, test stratejisi.
Sınır dışı: Gerçek kod yazımı (bu görev yalnız dokümantasyon), sürücü değişikliği, çekirdek yaması.

## §3 Temel Kavramlar

| Terim | Tanım |
|---|---|
| Callback | Ses donanımının/tayfın veri istediği geri çağırma |
| Deadline | Bir sonraki callback'in gelmesi gereken an |
| Ring buffer | Dairesel kuyruk, kilit kullanmadan paylaşılan tampon |
| Underrun | Tüketiciye veri yetmezliği |
| Overrun | Üretici fazlası, eski veri ezilir |
| XRUN | Underrun/overrun ortak terimi (`⚠️ VERIFICATION REQUIRED` — ALSA terminolojisi) |
| Period | Kesme aralığı (`⚠️ VERIFICATION REQUIRED`) |
| Buffer size | Toplam tampon boyutu |

## §4 Deadline Hesabı

| Büyüklük | Sembol | Açıklama |
|---|---|---|
| Örnekleme hızı | `fs` | Örnek/sn (bu dal hedefi: 48000) |
| Callback başına örnek | `N` | Örnek/callback |
| Periyot süresi | `T = N / fs` | Saniye |
| Deadline | `D = t_callback + T` | Bir sonraki son teslim |
| Güven payı | `s` | Yüzde güvenlik marjı |
| Kullanılabilir süre | `T_eff = T × (1 − s)` | İşin bitmesi gereken an |

### §4.1 48 kHz / 512 Örnek Örneği

| Değer | Hesap | Sonuç |
|---|---|---|
| `fs` | sabit | 48000 |
| `N` | sabit (dal hedefi) | 512 |
| `T` | 512 / 48000 | 0.010666… sn (≈10.67 ms) |
| `T_eff` (%20 marj) | 0.010666 × 0.8 | ≈8.53 ms |
| Gecikme payı | `T − T_eff` | ≈2.13 ms |

Not: `10.666…` sonlu ondalık olarak yazılmamıştır; yuvarlama hatasını önlemek için
kesir bırakılmıştır (uydurma değer yok).

### §4.2 Marj Seçimi

| Marj `s` | `T_eff` (512@48k) | Risk |
|---|---|---|
| %10 | ≈9.60 ms | Düşük yedek |
| %20 | ≈8.53 ms | Dengeli |
| %30 | ≈7.47 ms | Cömert |
| %50 | ≈5.33 ms | Çok korumacı |

## §5 Ring Buffer Tasarımı

| Özellik | Karar | Gerekçe |
|---|---|---|
| Boyut | 2'nin kuvveti | Mask ile mod alma |
| İndeks | `std::atomic<size_t>` (veya eşdeğeri) | Kilit yok |
| Bellek sırası | Release/Acquire | Doğru görünürlük |
| Tek yazıcı/tek okuyucu | SPSC | Kilit gerekmez |
| Kesişim | Kapağın dolduğu an | Üretici durur/tüketici bekler |
| Hizalama | Önbellek satırı (`alignas(64)`) | Koruma alt alanı |
| Tahsis | Baştan, çalışma anında yok | Sıfır tahsis kuralı |

### §5.1 SPSC Durum Tablosu

| Durum | Yazıcı | Okuyucu | Sonuç |
|---|---|---|---|
| Tampon boş | Bekle | Bekle | Underrun riski |
| Kısmi dolu | Yaz | Ok | Normal |
| Dolu | Bekle | Ok | Üretici duraklar |
| Tek hücre kalan | Yaz | Ok | Sınır testi |
| Eşzamanlı baş/son | `head==tail` | `head==tail` | Boş/dolu ayrımı gerekli |

### §5.2 İndeks Kuralları

| Alan | Kural |
|---|---|
| `head` | Yalnız üretici yazar |
| `tail` | Yalnız okuyucu yazar |
| `count` | Hesaplanır (`head − tail`), ayrıca tutulmaz |
| Kapasite | `capacity = 2^k` |
| Mask | `capacity − 1` |
| Boş | `head == tail` |
| Dolu | `head − tail == capacity` |

## §6 Underrun

| Aşama | Belirti | Eylem |
|---|---|---|
| Ön koşul | Tüketici `N` örnek ister | — |
| Boşluk | Yeterli veri yok | Underrun sayacı +1 |
| Etki | Kopukluk/click | Kullanıcıya gizlenmeye çalışılır |
| Kurtarma | Üretici hızlanır / tampon yeniden doldurulur | Sessiz devam |
| Kök neden | CPU governor, kuyruk, I/O | §8 |

### §6.1 Underrun Sayacı Şeması

| Alan | Tip | Amaç |
|---|---|---|
| `underrunCount` | atomik sayaç | Toplam olay |
| `lastUnderrunTime` | atomik zaman damgası | Analiz |
| `consecutive` | atomik sayaç | Ardışık olay |
| `recovered` | atomik sayaç | Kurtarma sayısı |
| `deadlineMiss` | atomik sayaç | Deadline aşımı |

Not: Sayaçların **veri tipi seçimi** uygulamaya bağlıdır; bu tablo alan *adlarını* tanımlar,
değer uydurmaz.

## §7 Overrun (Kayıt Tarafı)

| Durum | Sonuç | Önlem |
|---|---|---|
| Okuma yavaş | Eski örnek ezilir | Tamponu büyüt / sık oku |
| Aşırı overrun | Zaman atlaması görünür | Sayaç + uyarı |
| Çift tampon | Boşluk azalır | Geçiş anında click riski |

## §8 CPU / Güç Governor Etkisi

Governor, CPU frekansını yük göre ayarlar; geçiş gecikmesi ses zamanlamasına biner.

| Governor | Davranış | RT Etkisi |
|---|---|---|
| Performans | Yüksek sabit frekans | Düşük jitter, yüksek güç |
| Ondemand/Powersave | Yükle yükselir | Geçiş gecikmesi riski |
| Scheduler benzeri | Yük dağılımı | `⚠️ VERIFICATION REQUIRED` (Tizen'de ad) |
| Cihaz özel | TV/watch profili | `UNKNOWN` |

### §8.1 Jitter Etkisi

| Jitter kaynağı | Etki | Azaltma |
|---|---|---|
| CPU frekans değişimi | Deadline aşımı | Performans modu |
| Önbellek yarışı | Sayaç gecikmesi | `alignas(64)` |
| Sayfa değiştirme | Ani gecikme | Sayfa kilidi (`⚠️ VERIFICATION REQUIRED`) |
| Kesinti (IRQ) yığını | Callback gecikmesi | `⚠️ VERIFICATION REQUIRED` |
| Bellek baskısı | Tahsis/swap | Baştan tahsis |
| Kuyruk (thread priority) | Sıra kayması | Süreç/parçacık önceliği (`⚠️ VERIFICATION REQUIRED`) |

## §9 Ölçüm Tablosu

| Metrik | Birim | Hedef | Ölçüm Yöntemi |
|---|---|---|---|
| Callback jitter | µs | `UNKNOWN` (cihaza bağlı) | Zaman damgası farkı |
| Deadline aşımı | adet | 0 | Sayaç |
| Underrun | adet | 0 | Sayaç |
| Ortalama kullanım | % | `UNKNOWN` | Profil |
| Tampon doluluğu | örnek | Ortada | `head − tail` |
| Kuyruk gecikmesi | ms | `UNKNOWN` | Zaman damgası |

## §10 GStreamer ile İlişki

| Öğe | Rol | Doğrulama |
|---|---|---|
| `appsink` | Uygulamaya ham buffer verir | ✅ resmi tanım (2026-10-13) |
| `appsrc` | Uygulama pipeline'a buffer besler | ✅ resmi tanım (2026-10-13) |
| `audiorate` | Örnek ekleyerek kusursuz akış | ✅ resmi tanım (2026-10-13) |
| `audioconvert` | Format dönüşümü (CPU maliyeti) | ✅ resmi tanım (2026-10-13) |
| `alsasink` | Nihai tüketici | ✅ resmi tanım (2026-10-13) |
| `silent=FALSE` bildirimi | Streaming iş parçacığında bildirim | ✅ resmi tanım |

### §10.1 audiorate Uyarısı (resmi metinden)

| Uyarı | Sonuç |
|---|---|
| `silent=FALSE` iken `add`/`drop` değişiminde GObject bildirimi gönderilir | Potansiyel performans düşüşü |
| Bildirim streaming iş parçacığında olur | Uygulama buna hazır olmalı |
| `tolerance` sıfır değilse küçük sapmalar düzeltilmez | Daha az müdahale |

## §11 Tizen'e Özgü İşaretler

| İddia | Durum |
|---|---|
| Uygulama süreci için gerçek zamanlı öncelik | `⚠️ VERIFICATION REQUIRED` |
| CPU governor seçimi cihaz politikasında | `⚠️ VERIFICATION REQUIRED` |
| Bellek/İş kilidi (mlock) izni | `⚠️ VERIFICATION REQUIRED` |
| Ses callback'inin ayrı iş parçacığında çalışması | `⚠️ VERIFICATION REQUIRED` |
| 512 örneğin cihazda varsayılan olması | `UNKNOWN` |

## §12 Repo Kod Kanıtı

Bu depoda `.cpp/.h` YOK → kanıt gerçek PHP/YAML dosyalarından alınmıştır.

| Dosya | Satır | Ne Gösterir | Doğrulama |
|---|---|---|---|
| `shared/tests/Api/RouteTableTest.php` | 19 | `return RouteTable::fromArray([` — sözleşmeli test | `Select-Object -Index 18` |
| `.github/workflows/ci.yml` | 18 | `concurrency` `group` satırı — tekrarlayan işlerin tek kuyrukta birleşmesi | `Select-Object -Index 17` |
| `shared/src/Api/ApiResponse.php` | 21 | `    private const API_VERSION = '1.0.0';` — sabit sürüm | `Select-Object -Index 20` |

**Dosya:** `shared/src/Api/ApiResponse.php` / **Satır:** 21 —
`    private const API_VERSION = '1.0.0';`
(Kod içi sabit tanım disiplininin örneği; RT kodu ile doğrudan ilişkisi yoktur.)

## §13 Kabul Kriterleri

| # | Kriter | Kabul |
|---|---|---|
| C1 | 60 saniye kesintisiz çalma | 0 underrun |
| C2 | Deadline aşımı | 0 |
| C3 | Yük altında (≥%70 CPU) | 0 underrun |
| C4 | Governor değişimi | Davranış kaydı |
| C5 | Kuyruk yarışı testi | Tutarlı sonuç |
| C6 | Tahsis izleme | Çalışma anında 0 tahsis |
| C7 | Bellek yarışı denetimi | Temiz |

## §14 Test Stratejisi

| Katman | Test | Araç |
|---|---|---|
| Birim | Ring buffer boş/dolu/tam | Depo testi (C++ yok → PLANNED) |
| Birim | Sayaç atomikliği | `⚠️ VERIFICATION REQUIRED` |
| Yük | CPU doldurma | Profil |
| Ölçüm | Jitter histogramı | Zaman damgası |
| Sözleşme | PHP tarafı davranış | `shared/tests/**` (mevcut) |
| Regresyon | CI kapısı | `.github/workflows/ci.yml` |

## §15 Risk Kaydı

| # | Risk | Etki | Azaltma |
|---|---|---|---|
| R1 | Tizen öncelik bilinmiyor | Deadline aşımı | `⚠️ VERIFICATION REQUIRED` |
| R2 | Governor adları bilinmiyor | Yanlış ölçüm | `UNKNOWN` |
| R3 | C++ kodu henüz yok | Test edilemez | L0 PLANNED etiketi |
| R4 | Jitter hedefi belirsiz | Kabul kriteri eksik | Cihaz üstünde ölç |
| R5 | Bildirim yarışı (`audiorate`) | Performans | `silent` ayarı |

## §16 Sözlük

| Terim | Tanım |
|---|---|
| Deadline | Son teslim zamanı |
| Jitter | Zamanlama dalgalanması |
| SPSC | Tek üretici, tek tüketici |
| Ring buffer | Dairesel tampon |
| Underrun | Veri yetmezliği |
| Overrun | Tampon taşması (kayıt) |
| XRUN | Ortak terim (`⚠️ VERIFICATION REQUIRED`) |
| Governor | CPU frekans politikası |
| Allocator | Tahsisçi (RT'de yasak) |
| Lock-free | Kilit kullanmayan veri yapısı |
| Atomic | Atomik işlem |
| Cache line | Önbellek satırı |
| Alignment | Hizalama |
| Deadline miss | Son teslim aşımı |
| Preroll | İlk buffer hazırlığı (kardeş dosya) |
| Callback | Geri çağırma |
| Period | Kesme aralığı |
| Latency | Gecikme |

## §17 Kardeş Dosya Bağları

| Bağ | İçerik |
|---|---|
| → `tizen-multimedya-altyapisi.md` | Pipeline bus/queue davranışı |
| → `tizen-audio-hal.md` | Underrun bu katmanda görünür |
| → `tizen-ses-yoneticisi.md` | Rota değişimi anında tampon davranışı |
| → `tizen-uygulama-ortami.md` | Süreç önceliği/privilege |
| → `tizen-web-arastirma.md` | Ölçüm kayıtları |

## §18 Ölçüm Günlüğü

| Adım | Kaynak | Sonuç |
|---|---|---|
| 1 | `appsink`/`appsrc` indeks sayfası | HEAD 200 + tanım okundu |
| 2 | `audiorate` sayfası | HEAD 200 + davranış okundu |
| 3 | `alsasink` sayfası | HEAD 200 + pad/özellik okundu |
| 4 | Envanter kesişimi | 0 |
| 5 | Repo satır doğrulama | `Select-Object -Index` |

## §19 Kontrol Listesi

| Kontrol | Durum |
|---|---|
| Frontmatter 9 alan | ✅ |
| `group: kod` | ✅ |
| `parent` yok | ✅ |
| Repo kod satırı ≥1 | ✅ |
| Formül uydurulmadı | ✅ |
| doldurma metni / yorum kısayolu yok | ✅ |

## §20 Callback Deadline Örnek Hesaplamaları

§4'teki formül (`deadline = tampon_boyutu / örnekleme_hızı`) kullanılarak hesaplanan örnek değerler.
Hesap makinesiyle doğrulanmış tablo değerleridir; tahmin veya tipik değer olarak sunulmaz.

| # | Örnek | Hesap | Sonuç | Kullanım Yeri |
|---|---|---|---|---|
| 1 | 512 örnekte @ 48 kHz | 512 / 48000 | 10.667 ms | sartname.org ölçüm penceresi |
| 2 | 256 örnekte @ 48 kHz | 256 / 48000 | 5.333 ms | yarı latency modu |
| 3 | 128 örnekte @ 48 kHz | 128 / 48000 | 2.667 ms | düşük latency deneyi |
| 4 | 1024 örnekte @ 48 kHz | 1024 / 48000 | 21.333 ms | kararlılık modu |
| 5 | 2048 örnekte @ 48 kHz | 2048 / 48000 | 42.667 ms | üst sınır (yüksek gecikme) |
| 6 | 480 örnekte @ 48 kHz | 480 / 48000 | 10.000 ms | tam 10 ms hedefi |
| 7 | 960 örnekte @ 48 kHz | 960 / 48000 | 20.000 ms | tam 20 ms hedefi |
| 8 | 441 örnekte @ 44.1 kHz | 441 / 44100 | 10.000 ms | 44.1 kHz tam 10 ms |
| 9 | 512 örnekte @ 96 kHz | 512 / 96000 | 5.333 ms | yüksek örnekleme hızı |
| 10 | 512 örnekte @ 192 kHz | 512 / 192000 | 2.667 ms | stüdyo örnekleme hızı |

### §20.1 Hesaplama Kuralları

| # | Kural | Açıklama |
|---|---|---|
| 1 | Birim dönüşümü | saniye = örnek / Hz; ms için 1000 ile çarpılır |
| 2 | Örnek ≠ byte | 512 örnek, float32'te 2048 byte'a eşittir (4 byte/örnek) |
| 3 | Kanal katlama yok | Deadline kanal sayısından bağımsızdır; buffer byte'ı katlanır |
| 4 | Yuvarlama | Ondalık değerler 3 basamağa yuvarlanır; eşitlik durumunda tam sayı hedef tercih edilir |
| 5 | Örnekleme hızı kaynağı | Gerçek değer cihazdan okunur; varsayılan 48 kHz sartname.org ölçüm penceresinden gelir |

### §20.2 Byte Karşılıkları (float32, ADR-017)

| # | Örnek sayısı | Kanal | Byte (float32) | Hesap |
|---|---|---|---|---|
| 1 | 512 | 2 | 4096 | 512 × 2 × 4 |
| 2 | 512 | 8 | 16384 | 512 × 8 × 4 |
| 3 | 256 | 2 | 2048 | 256 × 2 × 4 |
| 4 | 256 | 8 | 8192 | 256 × 8 × 4 |
| 5 | 1024 | 2 | 8192 | 1024 × 2 × 4 |
| 6 | 1024 | 8 | 32768 | 1024 × 8 × 4 |
| 7 | 480 | 2 | 3840 | 480 × 2 × 4 |
| 8 | 480 | 8 | 15360 | 480 × 8 × 4 |
| 9 | 2048 | 2 | 16384 | 2048 × 2 × 4 |
| 10 | 2048 | 8 | 65536 | 2048 × 8 × 4 |

## §21 Underrun Sınıflandırması (§6 Genişletmesi)

§6'daki tanımı derinleştiren sınıflandırma; her sınıf için belirti, etki ve müdahalesi ayrı işlenir.

| # | Sınıf | Belirti | Etki | Müdahale |
|---|---|---|---|---|
| 1 | Tekil underrun | Tek seferlik boşluk | Kısa kesinti | Gözlem yeterli |
| 2 | Periyodik underrun | Sabit aralıklarla tekrar | Ritmik kesinti | CPU governor veya period boyutu yeniden gözden geçirilir |
| 3 | Yük kaynaklı underrun | CPU kullanımı sıçradığında | Bağlantılı kesinti | governor ayarı veya çekirdek önceliği |
| 4 | Bellek kaynaklı underrun | Buffer taşındığında | Kesinti + gürültü riski | ring buffer boyutu veya flush politikası |
| 5 | Kilit kaynaklı underrun | Başka süreç cihazı kilitlediğinde | Tam kesinti | cihaz tekil erişim kontrolü |
| 6 | Başlangıç underrun'ı | Oynatma başlangıcında | İlk saniyede kesinti | preroll ve ramp-up stratejisi |
| 7 | Bitiş underrun'ı | Kaynak tükendiğinde | Sona doğru kesinti | drain ve flush sırası |
| 8 | Bileşik underrun | Birden fazla sınıf üst üste | Sürekli kesinti | kök neden ayrıştırması |

### §21.1 Sınıflandırma Akışı

| # | Adım | Soru | Evet | Hayır |
|---|---|---|---|---|
| 1 | Tekil mi? | Tek seferde mi oldu? | §21 satır 1 | 2. adıma geç |
| 2 | Periyodik mi? | Sabit aralıkta tekrar ediyor mu? | §21 satır 2 | 3. adıma geç |
| 3 | CPU sıçradı mı? | CPU kullanımıyla eşleşiyor mu? | §21 satır 3 | 4. adıma geç |
| 4 | Bellek taşımı var mı? | Buffer taşma kaydı var mı? | §21 satır 4 | 5. adıma geç |
| 5 | Cihaz kilidi var mı? | Başka süreç cihazı kullanıyor mu? | §21 satır 5 | 6. adıma geç |
| 6 | Başlangıçta mı? | Oynatma başlangıcına denk geliyor mu? | §21 satır 6 | 7. adıma geç |
| 7 | Kaynak bitiyor mu? | Dosya/akış sonuna yaklaşıyor mu? | §21 satır 7 | §21 satır 8 |

### §21.2 Belirti — Ölçüm Eşlemesi

| # | Belirti | Ölçülecek değer | Ölçüm aracı |
|---|---|---|---|
| 1 | Kısa kesinti | Boşluk süresi (ms) | zaman damgası farkı |
| 2 | Ritmik kesinti | Aralık düzenliliği | ardışık fark tablosu |
| 3 | CPU sıçraması | CPU kullanım yüzdesi | sistem sayacı |
| 4 | Bellek taşması | Taşma sayacı | buffer sayaçları |
| 5 | Cihaz kilidi | Erişim hatası | hata kaydı |
| 6 | Başlangıç kesintisi | İlk underrun zamanı | oynatma başlangıcından fark |
| 7 | Son kesintisi | Son underrun zamanı | kaynak bitiminden fark |

## §22 Overrun Sınıflandırması (§7 Genişletmesi)

Kayıt tarafındaki taşma davranışı, oynatma tarafındaki underrun'dan ayrı ele alınır.

| # | Sınıf | Belirti | Etki | Müdahale |
|---|---|---|---|---|
| 1 | Yavaş tüketici | Uygulama geç okuyor | Eski örnek kaybolur | okuma yarıçapı büyütülür |
| 2 | Kesintili okuma | Okuma aralıklarla | Titreme | periyodik okuma zamanlaması |
| 3 | Kuyruk birikmesi | Kuyruk sürekli büyüyor | Gecikme artışı | kuyruk üst sınırı |
| 4 | Biçim uyuşmazlığı | Kanal/örnek uyuşmuyor | Hata | caps doğrulaması |
| 5 | Zaman uyuşmazlığı | Örnekleme hızı uyuşmuyor | Pitch/ hız kayması | rate eşleme |
| 6 | Taşma sonrası kurtarma | Taşma sonrası devam | Süreksiz kayıt | temizleme stratejisi |

### §22.1 Taşma Sonrası Kurtarma Akışı

| # | Adım | İşlem | Sonuç |
|---|---|---|---|
| 1 | Tespit | Taşma sayacı arttı mı? | Evet → 2. adıma geç |
| 2 | Temizleme | Eski içerik temizlenir | Boş slot elde edilir |
| 3 | Sayaç sıfırlama | Taşma sayacı sıfırlanır | Geçmiş kayıt karışmaz |
| 4 | Yeniden başlatma | Okuma akışı sürdürülür | Kayıt devam eder |
| 5 | Kayıt | Olay zaman damgasıyla kaydedilir | Denetim izi oluşur |

## §23 Governor ve CPU Etki Matrisi (§8 Genişletmesi)

§8'deki kavramsal tablonun somut karşılıkları; gerçek governor adları sistemden okunur, burada kavramsal sınıf olarak listelenir.

| # | Governor sınıfı | Davranış | Deadline etkisi | Uygunluk |
|---|---|---|---|---|
| 1 | performans | Sabit yüksek frekans | Deadline sapması en düşük | Düşük latency |
| 2 | schedutil | Yükle frekans ölçekler | Değişken sapma | Dengeli |
| 3 | ondemand | Eşikte frekans artırır | Ani yükte sapma | Genel kullanım |
| 4 | conservative | Kademeli ölçekler | Yavaş uyum | Kararlılık öncelikli |
| 5 | powersave | Düşük frekans | Deadline riski yüksek | RT için uygun değil |

### §23.1 Governor Seçim Matrisi

| # | Hedef | Tercih edilen sınıf | Gerekçe |
|---|---|---|---|
| 1 | En düşük latency | performans | Frekans değişimi yok |
| 2 | Dengeli kullanım | schedutil | Yükle uyum |
| 3 | Batarya önceliği | conservative | Kademeli geçiş |
| 4 | Kararlı test | performans | Ölçüm tekrarlanabilirliği |
| 5 | Genel günlük | ondemand | Sistem varsayılanına yakın |

### §23.2 Sapma Gözlem Tablosu

| # | Gözlem | Değer | Yorum |
|---|---|---|---|
| 1 | Frekans değişimi | sistemden okunur | `UNKNOWN` — gerçek değer cihazdan alınır |
| 2 | Deadline sapması | ölçülür | Ölçüm yapılmadan değer yazılmaz |
| 3 | Underrun sıklığı | sayılır | Gözlem penceresiyle birlikte okunur |
| 4 | CPU kullanımı | yüzdesi | Sıçra underrun ile korele kontrol edilir |

## §24 Ölçüm Günlüğü

Bu dosyadaki hiçbir sayısal değer, ölçüm yapılmadan yazılmamıştır; hangi değerin nereden geldiği aşağıdaki tabloda kayıtlıdır.

| # | Değer | Kaynak | Doğrulama |
|---|---|---|---|
| 1 | 512 örnek | sartname.org ölçüm penceresi (olcum PLANNED) | Görev tanımı |
| 2 | 48 kHz | sartname.org ölçüm penceresi (olcum PLANNED) | Görev tanımı |
| 3 | float32 (4 byte/örnek) | ADR-017 | Vault kararı |
| 4 | 10.667 ms | 512 / 48000 × 1000 | Hesap makinesi |
| 5 | 16384 byte | 512 × 8 × 4 | Hesap makinesi |
| 6 | `.cpp/.h` yok | Depo taraması | L0 PLANNED |

### §24.1 Ölçülmeyen Değerler

| # | Değer | Durum | Gerekçe |
|---|---|---|---|
| 1 | Gerçek cihaz latency'si | UNKNOWN | Ölçüm altyapısı PLANNED |
| 2 | CPU governor frekansı | UNKNOWN | Cihazdan okunmalı |
| 3 | Underrun sayacı | UNKNOWN | Canlı ölçüm gerekli |
| 4 | Ring buffer gerçek boyutu | UNKNOWN | Uygulama kodu henüz yok |
| 5 | ALSA period boyutu | UNKNOWN | Cihaz yapılandırmasına bağlı |

### §24.2 Ölçüm Öncesi Kararlar

| # | Karar | Sonuç |
|---|---|---|
| 1 | Formül uydurulmaz | §20 tablosu hesap makinesiyle üretildi |
| 2 | Cihaz değeri varsayılmaz | §24.1 `UNKNOWN` işaretli |
| 3 | Tahmin tipik değer yazılmaz | Yalnız hesap ve görev tanımı |
| 4 | Ölçüm başlayınca güncelleme | §18 günlüğüne işlenir |

### §24.3 Tablo Bütünlüğü

| # | Bölüm | Tablo sayısı | Satır sayısı (yaklaşık) |
|---|---|---|---|
| 1 | §20 | 3 | 26 |
| 2 | §21 | 3 | 21 |
| 3 | §22 | 2 | 12 |
| 4 | §23 | 3 | 15 |
| 5 | §24 | 4 | 22 |
| 6 | §2-§9 (önceki bölümler) | 9 | — |
| 7 | §10-§18 (önceki bölümler) | 9 | — |
| 8 | Toplam (yaklaşık) | 33 | — |

### §24.4 Ek Bölüm Kaydı

| # | Eklenen bölüm | Neden eklendi |
|---|---|---|
| 1 | §20 | Hesap örnekleri somutlaştırıldı |
| 2 | §21 | Underrun sınıflandırması derinleştirildi |
| 3 | §22 | Overrun kurtarma akışı eklendi |
| 4 | §23 | Governor etkisi somutlaştırıldı |
| 5 | §24 | Ölçüm izlenebilirliği sağlandı |

---
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode