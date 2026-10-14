---
id: isletim-sistemi-api-cihaz-erisimi-anayasa
title: Cihaz Erişimi — Anayasal Çekirdek
node_type: module
layer: L0
status: active
tags: [isletim-sistemi, cihaz-erisimi, anayasa, cihaz-dosyalari, fd, ioctl]
group: anayasa
---
# Cihaz Erişimi — Anayasal Çekirdek

## §0. Amaç

Bu belge, cihaz erişim modülünün **anayasal çekirdeğini** tanımlar. Modülün
üretimi, sürümü, olayları ve izleme kapıları bu belgeye tabidir. Belge
**salt-okunur** — üretim, sürüm, olay ve izleme kapıları bu belgede
değiştirilemez.

## §1. Çekirdek Prensip

> **Cihaz erişimi, işletim sistemi tarafından uygulamaya açılan dosya
> arayüzüdür.** Tüm cihazlar dosya gibi erişilir. Cihaz erişim arayüzü
> işletim sistemi seviyesinde tanımlanır.

`/dev` altındaki cihaz dosyaları, `/proc` altındaki süreç dosyaları ve
`ioctl` kontrol arayüzü bu çekirdek prensibin uygulama katmanlarıdır. Cihaz
erişim arayüzü işletim sistemi seviyesinde tanımlanır.

## §2. Çekirdek Yüzey

### §2.1 Cihaz Dosyaları

| # | Arayüz | Tanım | Referans |
|---|---|---|---|
| 1 | `/dev` | Cihaz dosyaları dizini | man7.org — filesystems.5 |
| 2 | `/proc` | Süreç dosyaları dizini | man7.org — filesystems.5 |
| 3 | `mknod` | Cihaz dosyası oluşturma | man7.org — man2/mknod.2 |
| 4 | `tmpfs` | Geçici dosya sistemi — bellek tabanlı | man7.org — tmpfs.5 |

### §2.2 Dosya Tanımlayıcı (fd)

| # | Arayüz | Tanım | Referанс |
|---|---|---|---|
| 1 | `open` / `close` | Dosya tanımlayıcı açma / kapama | POSIX — fcntl.h |
| 2 | `dup2` | Dosya tanımlayıcı çoğaltma | man7.org — man2/dup2.2 |
| 3 | `pipe2` | Boru oluşturma — fd çifti | man7.org — man2/pipe2.2 |
| 4 | `fcntl` | Dosya tanımlayıcı kontrolü | POSIX — fcntl.h |

### §2.3 `ioctl` Kontrol Arayüzü

| # | Arayüz | Tanım | Referans |
|---|---|---|---|
| 1 | `ioctl` | Cihaza özgü kontrol çağrısı | kernel.org — driver-api/ioctl.html |
| 2 | V4L2 | Video cihazı kontrol arayüzü | kernel.org — userspace-api/media/v4l/v4l2.html |

### §2.4 Hotplug Olayları

| # | Arayüz | Tanım | Referans |
|---|---|---|---|
| 1 | `inotify` | Dosya sistemi olay izleme | man7.org — man2/inotify_add_watch.2 |
| 2 | `signalfd` | Sinyal dosya tanımlayıcısı | man7.org — man2/signalfd.2 |
| 3 | `udev` | Linux cihaz olay yönetimi | kernel.org — driver-api/driver-model/overview.html |
| 4 | `WM_DEVICECHANGE` | Windows cihaz değişimi pencere mesajı | learn.microsoft.com — winuser |

### §2.5 Cihaz Yetkileri

| # | Arayüz | Tanım | Referans |
|---|---|---|---|
| 1 | `capset` | Yetki kümesi ayarlama | man7.org — man2/capset.2 |
| 2 | `CheckTokenMembership` | Token üyeliği denetimi | learn.microsoft.com — securitybaseapi |
| 3 | `SeLoadDriverPrivilege` | Sürücü yükleme yetkisi | learn.microsoft.com — winuser |

### §2.6 Bellek Eşleme — `mmap`

| # | Arayüz | Tanım | Referans |
|---|---|---|---|
| 1 | `mmap` | Bellek eşleme | kernel.org — core-api/mm-api.html |
| 2 | `VirtualFree` | Bellek serbest bırakma | learn.microsoft.com — memoryapi |

## §3. Anayasal Sınırlar

### §3.1 Sıcak Yüzey Sınırları

| # | Sınır | Kural |
|---|---|---|
| 1 | Sistem çağrısı gecikmesi bütçesi | µs mertebesinde — bağlam değiştirme maliyeti |
| 2 | Dosya tanımlayıcı sızıntısı | `fd` kullanım sonrası **her zaman** `close` |
| 3 | `ioctl` parametreleri | Cihaz sürücüsüne özgü — taşınabilir değil |

### §3.2 Güvenlik Sınırları

| # | Sınır | Kural |
|---|---|---|
| 1 | `/dev` dosya erişimi | Yetki denetimi zorunlu |
| 2 | `ioctl` çağrısı | Cihaz sürücüsüne özgü — güvenlik denetimi zorunlu |
| 3 | Cihaz yetkisi | En az yetki ilkesi — `CAP_SYS_ADMIN` / `SeLoadDriverPrivilege` |

### §3.3 Kaynak Sınırları

| # | Sınır | Kural |
|---|---|---|
| 1 | Dosya tanımlayıcı | Sınırlı kaynak — sızıntı yasak |
| 2 | Bellek eşleme | Sınırlı adres alanı — sızıntı yasak |

## §4. Sürüm Sonrası Kapı

| # | Kapı | Zaman | Geçiş şartı |
|---|---|---|---|
| 1 | İlk canlı çağırı | 0–15 dk | Cihaz erişimi hata oranı %0 |
| 2 | Kısa dönem | 1–72 saat | Cihaz erişimi hata oranı %0 — sürüm notu sözü |
| 3 | Orta dönem | 4–14 gün | Cihaz erişimi hata oranı %0 |
| 4 | Kilitlenme | 14+ gün | Kilitli sürüm |

## §5. Üretim Olay Kapıları

| # | Kapı | Zaman | Geçiş şartı |
|---|---|---|---|
| 1 | P1 — Kriz | 4 saat | Cihaz erişimi kesintisi · veri kaybı · güvenlik ihlali |
| 2 | P2 — Kritik | 24 saat | Cihaz erişimi sözleşmesi ihlali |
| 3 | P3 — Yüksek | 7 gün | Çok platformda cihaz erişimi hatası |

## §6. Sürüm Sonrası İzleme Kapıları

| # | Kapı | Eşik |
|---|---|---|
| 1 | Cihaz erişimi hata oranı | %0 — sürüm notu sözü |
| 2 | Dosya tanımlayıcı sızıntı | 0 |
| 3 | `ioctl` hata oranı | Yükseliş trendi → inceleme |

## §7. Referanslar

| # | Kaynak | URL | Erişim tarihi |
|---|---|---|---|
| 1 | The Linux Kernel — driver API | https://www.kernel.org/doc/html/latest/driver-api/ | 2026-10-13 |
| 2 | The Linux Kernel — driver model | https://www.kernel.org/doc/html/latest/driver-api/driver-model/overview.html | 2026-10-13 |
| 3 | The Linux Kernel — core API | https://www.kernel.org/doc/html/latest/core-api/mm-api.html | 2026-10-13 |
| 4 | The Linux Kernel — V4L2 | https://www.kernel.org/doc/html/latest/userspace-api/media/v4l/v4l2.html | 2026-10-13 |
| 5 | Linux man-pages — filesystems.5 | https://man7.org/linux/man-pages/man5/filesystems.5.html | 2026-10-13 |
| 6 | Linux man-pages — tmpfs.5 | https://man7.org/linux/man-pages/man5/tmpfs.5.html | 2026-10-13 |
| 7 | Linux man-pages — mknod.2 | https://man7.org/linux/man-pages/man2/mknod.2.html | 2026-10-13 |
| 8 | Linux man-pages — dup2.2 | https://man7.org/linux/man-pages/man2/dup2.2.html | 2026-10-13 |
| 9 | Linux man-pages — pipe2.2 | https://man7.org/linux/man-pages/man2/pipe2.2.html | 2026-10-13 |
| 10 | Linux man-pages — inotify_add_watch.2 | https://man7.org/linux/man-pages/man2/inotify_add_watch.2.html | 2026-10-13 |
| 11 | Linux man-pages — signalfd.2 | https://man7.org/linux/man-pages/man2/signalfd.2.html | 2026-10-13 |
| 12 | Linux man-pages — capset.2 | https://man7.org/linux/man-pages/man2/capset.2.html | 2026-10-13 |
| 13 | Microsoft Learn — securitybaseapi | https://learn.microsoft.com/en-us/windows/win32/api/securitybaseapi/ | 2026-10-13 |
| 14 | Microsoft Learn — memoryapi | https://learn.microsoft.com/en-us/windows/win32/api/memoryapi/ | 2026-10-13 |
| 15 | The Open Group — fcntl.h | https://pubs.opengroup.org/onlinepubs/9799919799/basedefs/fcntl.h.html | 2026-10-13 |

## §8. Bağımlılık Yönü

| # | Bağımlılık | Yön | Gerekçe |
|---|---|---|---|
| 1 | `sistem-cagrilari` | Cihaz erişimi sistem çağrılarını kullanır | Yalnız kendi çocuk modüllerine bağımlı |

## §9. Sınırlar

| # | Sınır | Gerekçe |
|---|---|---|
| 1 | `ioctl` | Cihaz sürücüsüne özgü — taşınabilir değil |
| 2 | `/dev` | Linux/macOS — Windows'ta farklı (`\\.\`) |
| 3 | `mmap` | Bellek eşleme — adres alanı sınırlı |

## §10 Cihaz Erişimi Derinleştirme — Dosya Türleri, İzin Zinciri, Ses Cihazı Katmanları

> Bu bölüm §1-§9'in anayasal çerçevesini derinleştirir. Yeni URL: §10.11'de
> 1 adet (envanter kesişimi 0 · HEAD-200). Dosya-türü/izin tarifleri doktrin
> seviyesindedir ve `⚠️ VERIFICATION REQUIRED` işaretleriyle korunur.

### §10.1 Dosya türleri — VFS kavramı üzerinden

POSIX dünyasında cihazlara erişim, "her şey dosyadır" ilkesinin içinden geçer.
VFS (sanal dosya sistemi) kavramı, uygulamanın hangi tür nesneyle uğraştığını
tanıtıcı üzerinden değil, tür üzerinden öğrenmesini sağlar. Tür envanteri:

| # | Tür | Tanıtıcı davranışı | Tipik kullanım | CoreMusic yüzeyi |
|---|---|---|---|---|
| 10.1.1 | normal dosya | bayt ofseti okur/yazar | PHP kodu, log, SQL | tüm depo dosyaları |
| 10.1.2 | karakter cihazı | bayt akışı, aygıtözel | ses cihazı, seri port | ses motoru hedefi (PLANNED) |
| 10.1.3 | blok cihazı | blok erişimi, tamponlanır | disk | PHP'den görünmez |
| 10.1.4 | boru (FIFO) | sıralı akış, iki ucu ayrı süreç | `proc_open` pipe kanalı | test/CLI zinciri |
| 10.1.5 | soket | iki uçlu iletişim | ağ | ağ katmanı ayrı dal |
| 10.1.6 | sembolik bağlantı | başka ada yönlendirme | yapılandırma bağlantıları | PHP `realpath` kavramı |
| 10.1.7 | dizin | girdi akışı = kayıt listesi | tarama | PHP `scandir` sınıfı |

> 10.1.2 ve 10.1.3 satırlarındaki "karakter/blok" ayrımı doktrinidir; bu
> turda resmi VFS sayfası envantere eklenemedi (aday adres bu turda 404
> döndü) — ⚠️ VERIFICATION REQUIRED.

### §10.2 Cihaz numaraları (major/minor) ve kayıt

Karakter ve blok cihazlar, ikili bir numara çifti ile tanımlanır: büyük
numara (major) aileyi, küçük numara (minor) aile içi üyeyi seçer. Kayıt
işlemi bu ağacın kaynakça bölümündeki mknod sayfası ile zaten tescillidir
(§7 kaynakça satırı 7); bu bölüm aynı adresi yeniden listelemez.

| # | Kavram | Ne yapar | Uygulama etkisi |
|---|---|---|---|
| 10.2.1 | major/minor çifti | hangi sürücünün hangi üye olduğu | aynı cihaz ailesinin iki örneği |
| 10.2.2 | kayıt (node oluşturma) | dosya ağacına üye ekleme | otomatikleştirme sistemlerinin işi |
| 10.2.3 | dağıtım | hangi numara hangi aileye | dağıtım kararı ⚠️ VERIFICATION REQUIRED |

### §10.3 İzin zinciri: dosya modu → süreç kimliği → denetim

Erişim denetimi üç katmanlı bir zincirdir; zincirin her halkası ayrı
reddedebilir:

```text
[uygulama: fopen / tanitici istegi]
     |
     v
[dosya modu denetimi] --red--> hata donusu
     | gecti
     v
[imtiyaz denetimi] --red--> hata donusu
     | gecti
     v
[aygit tarafindaki ek denetim (surucu)] --red--> hata donusu
     | gecti
     v
[tampona erisim]
```

| # | Halka | Soru | Bağlı olduğu belge |
|---|---|---|---|
| 10.3.1 | dosya modu | süreç sahip/grup/diğer mi? | bu dosya §10.4 |
| 10.3.2 | imtiyaz | süreç ek yetki taşıyor mu? | §7 kaynakça capset satırı 12 |
| 10.3.3 | sürücü denetimi | aygıt üye-listesinde mi? | bu dosya §10.5 |
| 10.3.4 | uygulama denetimi | PHP kodu yolu kontrol ediyor mu? | §E.3 (bu ağaç) |

### §10.4 Dosya modu tablosu (okuma/yazma/çalıştırma)

| # | Bit | Anlamı | Cihaz dosyasındaki karşılığı |
|---|---|---|---|
| 10.4.1 | okuma | bayt okuma yetkisi | tanıtıcı ile okuma |
| 10.4.2 | yazma | bayt yazma yetkisi | tanıtıcı ile yazma (sürücüye komut) |
| 10.4.3 | çalıştırma | yürütme/dizin arama | cihaz için genelde anlamsız |
| 10.4.4 | sahip/grup/diğer | kimin için | cihaz ailesinin erişim politikası |
| 10.4.5 | yapışkan bit / setuid sınıfı | gelişmiş işaretler | uygulanmaz — cihaz dosyasında nadir |

> 10.4.5 satırındaki "setuid" işaretinin cihaz dosyalarına uygulanabilirliği
> bu turda doğrulanmadı — kavramsal hatırlatmadır, iddia değildir.

### §10.5 Otomatik üye üretimi ve sürüm zinciri (kavramsal)

Modern sistemlerde cihaz üye dosyaları elle oluşturulmaz; sistem açılışında
veya cihaz takıldığında otomatikleştiriciler üye dosyasını yaratır, modunu
ayarlar ve gerektiğinde sürüm tetikler. CoreMusic perspektifi:

| # | Katman | Sorumluluk | CoreMusic'e etkisi |
|---|---|---|---|
| 10.5.1 | otomatik üye üreticisi | üye dosyası + mod | doğrudan etki yok |
| 10.5.2 | sürüm tetikleyicisi | kural yazımı | doğrudan etki yok |
| 10.5.3 | ses ailesi üye grubu | cihaz grubu üyeliği | PLANNED motorun çalıştırma kullanıcısı bu üyeliklere bağlı olacak (⚠️ hedef, ölçüm yok) |

### §10.6 PHP'den dosya/cihaz açma yüzeyi (fopen)

PHP, dosya erişimini akış (stream) soyutlaması üzerinden yapar. `fopen`
resmi referansı bu bölümün yeni tescilidir:

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 10.6.1 | PHP `fopen`, dosya akışı açma işlevinin resmi referansıdır | https://www.php.net/manual/en/function.fopen.php | 2026-10-13 | ✅ HEAD 200 (envanter kesişimi 0) |

PHP katmanı ile çekirdek katmanı arasındaki fark tablosu:

| # | PHP davranışı | Sargı katmanına yansıması | Ders |
|---|---|---|---|
| 10.6.2 | akış nesnesi döner (tanıtıcı değil) | sargı ayrıntısı PHP'de gizli | hata denetimi akış üzerinden |
| 10.6.3 | kaynak (resource) tipi denetimi | dil düzeyi güvenlik | `is_resource` deseni §6.2.7 (bu ağacın örnek dosyası) |
| 10.6.4 | mod dizgesi ("rb" gibi) | bayt/aykırı ayrımı | Windows dosya sonu farkı |
| 10.6.5 | cihaz üye dosyasına `fopen` | teknik olarak mümkün | uygulanmaz — L0 kuralları cihaz I/O'nu ses yolunda yasaklar |

> 10.6.5 satırı dikkatle okunmalıdır: PHP'nin üye dosyası açabilmesi bir
> iddia değil, mimari bir **yasağın** gerekçesidir — ses veri yolu dosya
> sistemi bağlamı taşımaz (§10.8).

Repo yüzeyi (bu ağacın zaten tescilli kanıtı — tekrar değil, bağlama yerleştirme):
`home.coremusic.net/include/Stream/MusicStreamHandler.php` dosya akışı hata
denetimi kanıtı syscall hata dosyasının §E.3'ündedir; oradaki desen, PHP'nin
akış soyutlamasının sargı katmanına ne kadar yaklaştığını gösterir.

### §10.7 Ses cihazı erişim katmanları — karşılaştırma

| # | Platform | En alttaki üye sınıfı | Üst katman | CoreMusic hedefi |
|---|---|---|---|---|
| 10.7.1 | Linux | karakter cihazları (ses ailesi) | dağıtıma bağlı katmanlar (⚠️ adlar bu turda yazılmadı) | sartname PLANNED |
| 10.7.2 | Windows | ayıt-yönlü açma yolları (§9.2'deki ikinci sınır satırı) | platform ses API'si (WASAPI — ayrı dokunulmaz dal) | sartname PLANNED |
| 10.7.3 | macOS | üye dosyaları + platform ses çerçevesi (⚠️) | platform ses çerçevesi | sartname PLANNED |
| 10.7.4 | Android | platform ses API'si (AAudio/OpenSL — audio dalı) | uygulama | sartname PLANNED |

> 10.7.1-10.7.3 satırlarındaki dağıtıma/çatıya özgü adlar bu turda tescil
> edilmedi (envanter kesişimi 0 kuralı: doğrulanmadan adres yazılmaz) —
> ⚠️ VERIFICATION REQUIRED. Ses API'lerinin tam karşılaştırması bu ağacın
> ses dalının konusudur; bu dosya sınır çizer.

### §10.8 Cihaz erişimi ↔ gerçek zamanlılık (L0 kıstosu)

| # | Kural | Gerekçe |
|---|---|---|
| 10.8.1 | ses cihazı tanıtıcısı ile çalışma sırasında dosya sistemi işi yapılmaz | dosya sistemi bağlamı kesinti/askıya alma taşır |
| 10.8.2 | tanıtıcılar başlangıçta açılır, kapanışta kapatılır | çalışma sırasında açma/kapama öngörülemeyen gecikme üretir |
| 10.8.3 | tanıtıcı sahipliği tek bileşendedir | iki bileşen aynı tanıtıcıyı kapatırsa sonraki çağrılar kör tanıtıcı kullanır |
| 10.8.4 | tanıtıcı sızıntısı denetimi başlangıç/kapanışta | sızan tanıtıcı çekirdek kaynağı tüketir |

### §10.9 Bağımlılık yönü genişlemesi

| # | Bağımlılık | Yön | Gerekçe |
|---|---|---|---|
| 10.9.1 | `sistem-cagrilari` (§8'deki mevcut satır) | bu dosya onu kullanır | tekrar değil, §10'un temeli |
| 10.9.2 | `syscall` hata dosyası | hata sınıflandırması oradan gelir | errno zinciri |
| 10.9.3 | `zamanlama-scheduler` | §10.8 kuralları zamanlama ile birlikte anlam taşır | sınır dosyası |
| 10.9.4 | `isletim-sistemi-audio` dalı | §10.7 tablosunun sahibi | ses dalı derinleştirir |

### §10.10 Belirsizlik ve sınır tablosu (§10 kapsamı)

| # | Konu | Durum | Davranış |
|---|---|---|---|
| 10.10.1 | Linux otomatik üye üretici adlandırma kuralları | ⚠️ VERIFICATION REQUIRED | ad yazılmaz |
| 10.10.2 | dağıtıma özgü ses üye grubu adları | ⚠️ VERIFICATION REQUIRED | ad yazılmaz |
| 10.10.3 | macOS ses çerçevesi arayüz ayrıntıları | ⚠️ VERIFICATION REQUIRED | kavramsal kalır |
| 10.10.4 | Windows ayıt-yönlü yolların tam sözdizimi | §9.2'de sınırlı olarak kayıtlı | genişletilmez, tekrarlanmaz |
| 10.10.5 | Android üye dosyası düzeyi erişim | audio dalının envanter kilidi | bu dosyaya taşınmaz |

### §10.11 Web araştırma tablosu (bu bölüm)

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | PHP `fopen` resmi referansı erişilebilir | https://www.php.net/manual/en/function.fopen.php | 2026-10-13 | ✅ HEAD 200 |

> Doğrulama: `Invoke-WebRequest -Method Head -UseBasicParsing -TimeoutSec 20`.
> Aday VFS/man sayfası bu turda 404 döndü → yazılmadı (uydurma yok).

### §10.12 Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir; mevcut
iddia·URL·tarih·durum sayıları aynen kalır. §10'nın eklediği tek yeni tescil
§10.11'deki tek URL satırıdır; §10.6.1 satırı aynı tescilin gövdedeki
anlatımıdır ve ayrı sayılmaz. Repo iddiası eklenmemiştir.

### §10.13 Cihaz erişimi desenleri — okuma rehberi (tamamlama)

| # | Desen | Adım sırası | Sınıf |
|---|---|---|---|
| 10.13.1 | yol-önce-kontrol | yolu doğrula → aç | savunmacı |
| 10.13.2 | aç-sonra-doğrula | aç → tanıtıcıyı doğrula → kullan | hata yolunda kapatma zorunlu |
| 10.13.3 | devir-deseni (kapat-yeniden-aç) | kapat → yeniden yapılandır → aç | yeniden bağlanma |
| 10.13.4 | sahiplik-deseni (tek kapatan) | tek bileşen kapatır | §10.8.3 kıstosu |
| 10.13.5 | denetim-deseni (başlangıç+kapanış) | iki noktada sayaç | §10.8.4 kıstosu |

Her desenin cihaz-dosyası üzerindeki okunması:

| # | Desen | Cihaz dosyası okuması | Uygulanabilirlik |
|---|---|---|---|
| 10.13.6 | yol-önce-kontrol | üye dosyası yolu tahmin edilemez → kontrol zayıf | zayıf (üretim tarafından yönetilir) |
| 10.13.7 | aç-sonra-doğrula | en güçlü desen | ses motoru başlangıcı |
| 10.13.8 | devir-deseni | kopma sonrası yeniden bağlanma | PLANNED kurtarma |
| 10.13.9 | sahiplik-deseni | tanıtıcı tek nesnede | zorunlu |
| 10.13.10 | denetim-deseni | sızıntı denetimi | zorunlu |

### §10.14 İzin/red sınıflandırması — uygulama tarafı okuması

| # | Dönüş sınıfı | Ne anlama gelir | İlk aksiyon |
|---|---|---|---|
| 10.14.1 | erişim reddi | yetki zinciri 10.3'te koptu | yetki denetimi (güvenlik) |
| 10.14.2 | yok | yol/üye bulunamadı | yolu/üyeyi kontrol et |
| 10.14.3 | aygıt yok | üye silinmiş/kopmuş | yeniden bağlanma (devir-deseni) |
| 10.14.4 | izin için gereken imtiyaz yok | imtiyaz halkası | imtiyaz talebi (imkânsızsa reddet) |
| 10.14.5 | kaynak bitişi | süreç sınırı | sızıntı denetimi |

### §10.15 Bağımlılık matrisi genişlemesi (bu bölümün kapıları)

| # | Kapı | Hangi dosya | Durum |
|---|---|---|---|
| 10.15.1 | üye grubu adları | dağıtıma bağlı bilgi | ⚠️ yazılmadı |
| 10.15.2 | macOS ses çerçevesi ayrıntısı | ses dalı | ⚠️ yazılmadı |
| 10.15.3 | Windows ayıt-yönlü tam sözdizimi | §9'da sınırlı kayıt | genişletilmedi |
| 10.15.4 | ölçüm (gerçek cihaz) | L0 ölçüm planı | PLANNED |

### §10.16 Dürüstlük bloğu (tamamlama eki)

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir; mevcut
iddia·URL·tarih·durum sayıları aynen kalır. §10.13-§10.15 ekleri 0 URL, 0
repo iddiası, 0 ölçüm sonucu içerir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

### §10.17 Cihaz dosyası yaşam döngüsü (doktrin)

Cihaz erişiminin tamamı dört evrede toplanır; bu dosyanın §10.7 katman
karşılaştırmasının zaman ekseni hâlidir.

| Evre | Ne olur | Sınıf | Dikkat |
|---|---|---|---|
| 1 — açma | dosya adı çözümlenir, izin denetlenir, tanıtıcı döner | ev sahibi | izin reddi bu evrede görünür |
| 2 — kullanım | okuma/yazma/denetim çağrıları tanıtıcı üzerinden yürür | misafir + ev sahibi | tanıtıcı paylaşıldığında konum paylaşılmaz |
| 3 — duraklatma | kullanımda değil ama açık — kaynak tutulur | ev sahibi | açık-kapalı sayacı reference counting ile gider |
| 4 — kapatma | tanıtıcı serbest bırakılır, sayaç düşer | ev sahibi | son kapatma gerçek kaynağı serbest bırakır |

**Yaşam döngüsü kuralları:**

1. tanıtıcı sahipliği açan taraftadır; devri açıkça yapılır, örtük devir yoktur
   (§10.4'teki tanıtıcı sahipliği doktrini ile aynı ilke);
2. kapatma çağrısı her hata dalında da çalışır — hata dalı atlanan kapatma,
   sonraki kullanımda paylaşılmış-konum hatası üretir;
3. sayaç sıfıra indiğinde kaynak serbest bırakılır; sayaç eksiye inemez
   (ters sayaç UNDERFLOW, bozuk kapatma sırasının belirtisidir);
4. cihaz dosyası kalıcıdır (süreç kalsa da kayıt durur), geçici tanıtıcı
   değildir — kalıcılık izin modelini de değiştirir.

**Kapı (tarif):** "yaşam döngüsü bütünlüğü" kapısı = her kullanım evresinin
eşleşen kapatma evresine sahip olması. Bu turda kod yoktur, kapı kapandı
sayılmaz — yalnız tarif edilmiştir.

### §10.18 Katmanlı erişim zinciri — izin ve red akışı (genişletme)

§10.7'deki katman yığınına bu bölüm zaman sırasını ekler; çağrı yukarıdan
aşağıya iner, hata aşağıdan yukarıya yükselir:

1. **uygulama katmanı** — seçimi yapar; yasak seçenek burada sunulmaz;
2. **kabul katmanı** — giriş doğrular; reddi burada biter, alt katmana inmez;
3. **sistem çağrısı sınırı** — parametre doğrulaması; geçersiz tanıtıcı,
   aralık dışı tampon burada yakalanır;
4. **ev sahibi cihaz katmanı** — izin denetimi ve yönlendirme; izin reddi
   bu katmanın asli görevidir;
5. **sürücü katmanı** — donanım hatası; yukarıya hata kodu olarak döner.

**Red yükselmesi kuralı:** hata her katmanda ya tamamen tüketilir (üst
katman kendi durumuna çevirir) ya da olduğu gibi yükseltilir; kısmi
tüketim — üst katman hatanın bir kısmını görüp yarısını yutarsa —
yasaktır. Bu kural §10.3'teki hata yansıma zinciri ile aynı sözleşmedir.

| Katman | Red tipi | Üst katmana yansıyan | Tüketim |
|---|---|---|---|
| uygulama | seçim yokluğu | kullanıcıya seçenek listesi | tam |
| kabul | geçersiz girdi | kısa hata metni | tam |
| sistem çağrısı | geçersiz parametre | sistem hata numarası | tam |
| ev sahibi | izin yok | erişim reddi | tam |
| sürücü | donanım hata kodu | sürücü hata kodu | kısmi olabilir, açıkça işaretlenir |

### §10.19 Dosyalar-arası kapı genişletmesi (bu bölümün kapıları — tarif)

§10.10'daki belirsizlik tablosuna bu bölüm üç kapı ekler; üçü de bu turda
sonuçlandırılmaz:

**Kapı D — izin/red tutarlılığı.** İzin reddi üreten her katmanın, reddi
belgeleyen tek bir kurala dayanması (iki ayrı katmanın iki ayrı kural
üretmesi yasak). Girdi: katman-tabloları. Çıktı: kural eşlemesi.

**Kapı E — kapatma sırası.** Hata dalları dâhil her kullanım evresinin
eşleşen kapatmaya sahip olması (§10.17 kural 2). Girdi: akış tarifleri.
Çıktı: eşleşmeyen evre listesi (kod olduğu ölçüde anlamlıdır).

**Kapı F — çoklu-cihaz çakışması.** Aynı cihaz dosyasına iki süreçten
eşzamanlı erişimde devir sırası (kilit-dışı okuma-yazma kuralı bu dosyada
tanımlanmaz — sürücü dosyasının alanıdır). Girdi: erişim senaryoları.
Çıktı: çakışma raporu.

Üç kapı da doktrin kapısıdır; ölçüm kapıları §10.11'deki web-research
tablosuna bağlı kalır ve bu ekle kapanmaz.

### §10.20 Dürüstlük bloğu (tamamlama eki 2)

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir; mevcut
iddia·URL·tarih·durum sayıları aynen kalır. §10.17-§10.19 ekleri 0 URL, 0
repo iddiası, 0 ölçüm sonucu içerir; yalnız yaşam döngüsü doktrini, katman
tabloları ve kapı tariflerinden oluşur. §10.11'deki web-research tablosu ve
§10.12'deki dürüstlük kaydı aynen korunur.

### §10.21 Erişim yolu matrisi (derinleştirme eki 3 — tarif)

§10.17-§10.19 yaşam döngüsü doktrinine bu bölüm erişim yolları boyutunu
ekler — bir cihaza üç yol vardır ve üçü aynı kapıdan geçmez:

| Yol | Açan | Kapı | Sıra garantisi |
|---|---|---|---|
| normal-açma | uygulama | tam izin denetimi (§10.18) | yok (başka yol açılabilir) |
| yeniden-açma | aynı uygulama | kısmi denetim (aynı-hesap doğrulaması) | mevcut kullanıcının sırası korunur |
| acil-erişim | çekirdek-içi servis | denetim atlanır, kayıt düşer | en yüksek sıra |

**Yol kuralları:**
1. üç yolda da açılma kaydı (audit) düşülür — acil-erişim hariçlik
   değildir, yalnız denetim atlanır; kayıt atlanmaz;
2. normal-açma ile acil-erişim aynı anda olursa acil-erişim normalin
   bekleyen işini iptal etmez — kuyruk korunur, acil yan sıraya girer;
3. yeniden-açma mevcut oturumu devralmaz; yalnız aynı-hesap
   doğrulanırsa izin verilir (hesap-farklıysa normal-açma sayılır);
4. üç yoldan hangisinin kullanıldığı çağrı kaydında görünür olmalıdır
   — görünmüyorsa yol-atımı (path-smuggling) şüphesi doğar ve kapı
   §10.19'daki çakışma kapısına devredilir.

### §10.22 Dürüstlük bloğu (derinleştirme eki 3)

Bu ek (§10.21) kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt
değildir: 0 URL, 0 repo iddiası, 0 ölçüm sonucu eklenmiştir; yalnız
erişim yolu matrisi ve yol kuralları tarif edilmiştir. §10.11'deki
web-research tablosu ve §10.20 dürüstlük kaydı aynen korunur; ölçüm
kapıları bu ekle kapanmaz.

### §10.23 Cihaz erişim kod örnekleri (2026-10-14)

§10.17-§10.21 doktrinini iki sistemde gösterir. Bloklar derlenebilirlik
iddiası TAŞIMAZ; her örneğin kaynağı §10.24 tablosundadır.

**Linux — `open` + `ioctl` + `close` üçlüsü:**

```c
#include <fcntl.h>
#include <sys/ioctl.h>
#include <unistd.h>
#include <errno.h>
#include <stdio.h>
#include <string.h>

int fd = open("/dev/ornek", O_RDWR | O_CLOEXEC);   /* O_CLOEXEC: fork+exec sızıntısız */
if (fd == -1) {
    fprintf(stderr, "acma reddedildi: %s\n", strerror(errno));  /* §10.18 izin denetimi kapısı */
    return 1;
}

unsigned long deger = 0;
if (ioctl(fd, /*komut*/ 0x5401, &deger) == -1) {
    fprintf(stderr, "ioctl: %s\n", strerror(errno));   /* ENOTTY: komut bu cihazda yok */
    close(fd);                                          /* §10.17 kural 2 — hata yolu da kapatır */
    return 1;
}

close(fd);   /* §10.21 normal-açma yolunun kapanışı */
```

Okuma kuralları:

| # | Kural | Kötü okuma | § |
|---|---|---|---|
| 1 | `O_CLOEXEC` açık artırma ile istenir | fork+exec'te fd sızar | §10.17 |
| 2 | izin reddi `open`'ın dönüşünde `errno` ile okunur | reddi sonradan aranır | §10.18 |
| 3 | `ioctl`'un `ENOTTY`'si "komut yok" anlamına gelir | cihaz-arıza sanılır | §10.19 |
| 4 | hata yolu dâhil her yol `close` ile biter | evre sızıntısı | §10.17 kural 2 |

**Windows karşılığı:** aynı talep-yanıt işi `CreateFileW` + `DeviceIoControl`
+ `CloseHandle` ile yapılır — tam kod `platform-api/windows-api-yuzeyi.md`
§12.3'tedir. Gruplar arası eşleme (`ioctl` ↔ `DeviceIoControl`)
`isletim-sistemi-api-ornekler.md` §F.2 tablosunda özetlenmiştir.

### §10.24 §10.23 kanıt tablosu

| # | Iddia | Resmi kaynak | Tarih | Durum |
|---|---|---|---|---|
| 1 | `ioctl(2)` cihaz kontrolü için genel kapıdır | https://man7.org/linux/man-pages/man2/ioctl.2.html | 2026-10-14 | ✅ HTTP 200 (HEAD) |
| 2 | `open(2)` · `O_CLOEXEC` bayrağı | https://man7.org/linux/man-pages/man2/open.2.html | 2026-10-14 | ✅ HTTP 200 (HEAD) |
| 3 | `mmap(2)` cihaz belleği eşleme yüzeyi | https://man7.org/linux/man-pages/man2/mmap.2.html | 2026-10-14 | ✅ HTTP 200 (HEAD) |
| 4 | Windows `DeviceIoControl` | https://learn.microsoft.com/en-us/windows/win32/api/ioapiset/nf-ioapiset-deviceiocontrol | 2026-10-14 | ✅ HTTP 200 (HEAD) |

Envanter kesişimi (2026-10-14 · 3303 benzersiz URL): dört satır da envanterde
mevcuttur (VAR — tekrar sayılmaz); bu dosyada ilk kez kod örneğiyle
kullanılırlar. `ioctl` sayısal komut değerleri (ör. `0x5401`) burada
**kanıt olarak sunulmaz** — gerçek komut değerleri ilgili cihazın
`Documentation/` sayfasından gelir ve bu turda doğrulanmamıştır
(⚠️ VERIFICATION REQUIRED).

### §10.25 PHP'den dosya/cihaz erişimi kod örneği (2026-10-14)

§10.6'daki `fopen` yüzeyinin kodu. Repo kanıtı diskte doğrulanmıştır:
`home.coremusic.net/include/Stream/MusicStreamHandler.php:333` —
`$handle = @fopen($path, 'rb');`. Repo kullanımı **dosya** akışıdır; cihaz
dosyası iddiası TAŞIMAZ — paylaşılan desen şudur: dönüş denetimi + akış
üzerinden okuma + her yolda `fclose`.

```php
<?php
declare(strict_types=1);

// Desen kaynağı: home.coremusic.net/include/Stream/MusicStreamHandler.php:333
//   $handle = @fopen($path, 'rb');
// Bu blok DESEN ÖRNEĞİDİR: repo'daki $path dosya yoludur, cihaz yolu değil.
$path = '/dev/ornek';                 // cihaz yolu örnektir — gerçek kanıt değildir
$handle = @fopen($path, 'rb');        // @: hata seviyesini bastır, DÖNÜŞ DEĞERİ denetle
if ($handle === false) {
    $son = error_get_last();
    fwrite(STDERR, 'acilamadi: ' . ($son['message'] ?? 'bilinmiyor') . "\n");
    exit(1);
}
$veri = fread($handle, 4096);         // akış üzerinden okuma — bayt akışı
fclose($handle);                      // hata yolu dâhil her yol kapanır — §10.17 kural 2
```

> §10.6.5 kuralı aynen geçerlidir: PHP'den cihaz üye dosyasına erişim teknik
> olarak mümkündür, ancak L0 ses yolunda cihaz I/O'u yasaktır — bu blok
> gözlem/teşhis örneğidir, üretim ses yolu DEĞİLDİR.

§10.25 kanıt tablosu:

| # | İddia | Kaynak | Tarih | Durum |
|---|---|---|---|---|
| 1 | PHP `fopen` resmi referansı | https://www.php.net/manual/en/function.fopen.php | 2026-10-13 | ✅ HTTP 200 · VAR (§10.6 tescilli) |
| 2 | repo'da `fopen` deseni | `home.coremusic.net/include/Stream/MusicStreamHandler.php:333` | 2026-10-14 | ✅ diskte doğrulandı |

Yeni URL: 0 · repo kanıtı (satır 2) bu dosyada ilk kez kullanılır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
