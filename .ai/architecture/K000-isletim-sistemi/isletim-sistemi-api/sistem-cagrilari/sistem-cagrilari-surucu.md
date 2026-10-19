---
id: isletim-sistemi-api-sistem-cagrilari-surucu
title: Sistem Çağrıları — Sürüm/Üretim Sürücüsü Raporu
node_type: module
layer: L0
status: active
tags: [isletim-sistemi, sistem-cagrilari, surucu, uretim, surum]
group: surucu
---
# Sistem Çağrıları — Sürüm/Üretim Sürücüsü Raporu

## §0. Amaç ve Kapsam

Bu rapor, sistem çağrıları ve sistem kütüphaneleri modülünün üretim sürümü
nasıl oluşturulduğunu ve nasıl ölçüldüğünü belgeler. Kapsam:

1. Sürüm numarası ve içerik kuralı.
2. Sürüm akışı — commit'ten dağıtıma sıralı kapılar.
3. Üretim ortamı koşulları — canlı çalışma zamanı kısıtları.
4. Sürüm sonrası doğrulama — canlı ortamda doğrulama kapıları.
5. Üretim olay kaydı — canlı olayların sınıflandırılması.
6. Sürüm sonrası izleme — canlı izleme kapıları.

Kapsam dışı: sürüm numarası üretimi (K005'e devredilir); dağıtım otomasyonu
(K018'e devredilir); canlı güvenlik denetimi (K017'ye devredilir).

## §1. Sürüm Numarası ve İçerik Kuralı

### §1.1 Sürüm Numarası

| Kurallar | Değer | Kural |
|---|---|---|
| Format | `vMAJOR.MINOR.PATCH` | MAJOR: kırılan sistem çağrısı sözleşmesi; MINOR: eklenen sistem çağrısı / yeni platform desteği; PATCH: hata düzeltmesi, metin düzeltmesi |
| Başlangıç sürümü | `v1.0.0` | İlk üretim sürümü |
| Sürüm numarası kaynağı | Vault sürüm alanı + git tag | Sürüm numarası vault sürüm alanı + git tag üzerinden yönetilir |

### §1.2 İçerik Kuralı

| İçerik | Kural | Yasak |
|---|---|---|
| `errno` değerleri | Sabit, sürümden sürüme değişmez | Yeni sürümde `errno` değeri yeniden numaralandırılamaz |
| `EINTR` davranış sözleşmesi | Sabit, sürümden sürüme değişmez | Sürüm bu sözleşmeyi değiştiremez — kırık sürüm |
| POSIX / Linux / Windows / macOS referansları | Sürüm notunda güncel sürüme göre doğrulanır | Sürüm notu bayat referans içermez |

## §2. Sürüm Akışı — Commit'ten Dağıtıma

### §2.1 Kapılar

| # | Kapı | Sahip | Geçiş şartı |
|---|---|---|---|
| 1 | Commit | Geliştirici | Vault üretim kapıları — `.ai/CLAUDE.md` §0 |
| 2 | Build | CI | `php -l` temiz, bağımlılık yok |
| 3 | Test | CI | Test paketi geçer, en az bir sistem çağrısı testi geçer |
| 4 | Kod incelemesi | Ekip | Çift onay, en az biri sistem çağrısı uzmanı |
| 5 | Sürüm notu | Geliştirici | Sürüm numarası + içerik kuralı §1 |
| 6 | Tag + push | Sürüm sorumlusu | Git tag `vX.Y.Z` |
| 7 | Dağıtım | K018'e devredilir | Dağıtım planı §2.2 |
| 8 | Canlı doğrulama | Operasyon | §4 kapıları |

### §2.2 Dağıtım Planı

| Aşama | Zaman | Geri alma |
|---|---|---|
| Canary | 1 saat, %5 trafik | Önceki sürüme döndür |
| Yaygın | 24 saat | Önceki sürüme döndür |
| Tam | 72 saat — kilitlenir | Yalnız sıcak yama |

## §3. Üretim Ortamı Koşulları

### §3.1 Canlı Çalışma Zamanı Kısıtları

| # | Kısıt | Kural | Gerekçe |
|---|---|---|---|
| 1 | Sistem çağrısı gecikmesi bütçesi | µs mertebesinde | Sistem çağrısı bağlam değişim maliyeti |
| 2 | `shell_exec` canlı kullanımı | YASAK | Sürüm 1.0 — güvenli komut yürütme yok |
| 3 | `errno` bağlamı | Canlı hata kaydında her zaman `errno` değeri | Denetim kanıtı |
| 4 | Canlı ağ hatası | Yalnızca kütüphane ağ arayüzü kullanılır | Doğrudan `socket` canlı yasak |

### §3.2 Canlı Güvenlik Kısıtları

| # | Kısıt | Kural | Gerekçe |
|---|---|---|---|
| 1 | Kullanıcı verisi sistem çağrısına | Parametreli, kaçışlı | Komut enjeksiyonu |
| 2 | Dosya yolu doğrulama | `realpath` + kök denetimi | Dizin dışına yazma |
| 3 | Çocuk süreç | `proc_open` + argüman dizisi — `shell_exec` canlı yasak | Komut enjeksiyonu |

## §4. Sürüm Sonrası Doğrulama

### §4.1 Kapılar

| # | Kapı | Zaman | Geçiş şartı |
|---|---|---|---|
| 1 | İlk canlı çağırı | 0–15 dk | Sistem çağrısı hata oranı %0 |
| 2 | Kısa dönem | 1–72 saat | Sistem çağrısı hata oranı %0 — sürüm notu sözü |
| 3 | Orta dönem | 4–14 gün | Sistem çağrısı hata oranı %0 |
| 4 | Kilitlenme | 14+ gün | Kilitli sürüm |

### §4.2 Sürüm Sonrası Doğrulama Kayıtları

| # | Kayıt | Zorunlu alanlar | Kanıt sınıfı |
|---|---|---|---|
| 1 | İlk canlı çağırı kaydı | Zaman damgası · sürüm · sistem çağrısı hata oranı · `errno` dağılımı | Birincil — canlı ortam |
| 2 | Kısa dönem kaydı | Zaman · hata oranı · olay günlüğü | Birincil — canlı ortam |
| 3 | Orta dönem kaydı | Zaman · hata oranı · geri alma sıklığı | Birincil — canlı ortam |
| 4 | Kilitlenme kaydı | Zaman · sürüm · kilitlenme kararı | Birincil — canlı ortam |

## §5. Üretim Olay Kaydı

### §5.1 Olay Sınıflandırması

| Sınıf | Tanım | Örnek | Müdahale |
|---|---|---|---|
| P1 — Kriz | Sistem çağrısı kesintisi · veri kaybı · güvenlik ihlali | `shell_exec` enjeksiyonu · dosya sistemine yetkisiz yazma | Sıcak yama — 4 saat |
| P2 — Kritik | Sistem çağrısı sözleşmesi ihlali · tek platformda sistem çağrısı hatası | `EINTR` sözleşmesi ihlali · tek platformda `errno` değeri değişimi | Sıcak yama — 24 saat |
| P3 — Yüksek | Çok platformda sistem çağrısı hatası · kütüphane sürüm uyumsuzluğu | Üç platformda `fopen` başarısızlığı | Sürüm yaması — 7 gün |
| P4 — Orta | Tek platformda nadir sistem çağrısı hatası | Tek Linux sürümünde `dlopen` uyarısı | Gelecek sürüm |
| P5 — Düşük | Metin düzeltmesi · yeni platform desteği | Yeni Linux sürümü desteği | Gelecek sürüm |

### §5.2 Müdahale Akışı

```
Olay raporu → Sınıflandır (P1–P5) → Kaynak kök neden doğrulama (K003)
→ Sıcak yama (yalnız P1/P2) → Sürüm sonrası doğrulama (§4) → Kilitlenme (P3+)
```

## §6. Sürüm Sonrası İzleme

### §6.1 İzleme Kapıları

| # | İzleme | Kaynak | Eşik |
|---|---|---|---|
| 1 | Sistem çağrısı hata oranı | Uygulama günlükleri | %0 — sürüm notu sözü |
| 2 | `errno` değeri dağılımı | Uygulama günlükleri | Yeni `errno` değeri varsa inceleme |
| 3 | `EINTR` tekrar deneme oranı | Uygulama günlükleri | Yükseliş trendi → inceleme |
| 4 | Dosya sistemi hata oranı | Uygulama günlükleri | Yükseliş trendi → inceleme |
| 5 | Dinamik yükleme hata oranı | Uygulama günlükleri | Yükseliş trendi → inceleme |

### §6.2 Alarm Kuralları

| # | Alarm | Eşik | Müdahale |
|---|---|---|---|
| 1 | Sistem çağrısı hata oranı | %0 — sürüm notu sözü ihlali | Kriz — sürüm notu ihlali |
| 2 | P1 olay | 1 | Kriz — kriz ekibi |
| 3 | P2 olay | 3/gün | Kriz — kriz ekibi |

## §7. Repo Kanıtları

### §7.1 Sürüm Sonrası İzleme Kanıtları

| # | Dosya | Satır | Kanıt |
|---|---|---|---|
| 1 | `shared\src\AI\AIEngine.php` | 295 | `$output = shell_exec($cmd);` — canlı sistem çağrısı kullanımı — sürüm sonrası kapı 1 |

### §7.2 Doğrulama

```powershell
Get-Content "shared\src\AI\AIEngine.php" | Select-Object -Index 294
```

## §8. Sürüm Sonrası Sınır Tablosu

| Çıktı | Sahip | Hedef |
|---|---|---|
| Sürüm numarası | Sürüm sorumlusu | Vault sürüm alanı + git tag |
| Sürüm notu | Geliştirici | Vault sürüm notu |
| İlk canlı doğrulama raporu | Operasyon | Vault üretim kaydı |
| Olay raporu | Operasyon | Vault olay günlüğü |

## §9. Sürüm Sonrası Sınır Matrisi

| Modül | Sürüm sorumlusu | Olay sahibi |
|---|---|---|
| syscall | K000-isletim-sistemi | K000-isletim-sistemi |
| libc | K000-isletim-sistemi | K000-isletim-sistemi |
| dosya-sistemi | K000-isletim-sistemi | K000-isletim-sistemi |

## §10 Sürücü/Sürüm Raporu Derinleştirme — Katmanlar, Yansıma Zinciri, Olay Sınıflandırması

> Bu bölüm §1-§9'daki sürüm/üretim raporunun derinleşmesidir. Yeni URL yok
> (bu turda kesişimi 0 olan doğrulanmış adresler ağacın başka beyaz liste
> dosyalarına ayrılmıştır); tüm içerik doktrin + mevcut tescil üzerine
> kuruludur. Doğrulanamayan iddialar işaretle korunur.

### §10.1 Katman yığını — uygulamadan aygıta

| # | Katman | Ne taşır | Hata yüzeyi |
|---|---|---|---|
| 10.1.1 | PHP uygulama kodu | akış/ süreç çağrıları | PHP istisnası / yanlış dönüş değeri |
| 10.1.2 | PHP çalışma zamanı sargısı | dil → C sargısı köprüsü | kaynak (resource) tipi hataları |
| 10.1.3 | libc sargı katmanı | adlandırılmış sistem çağrısı | errno sınıfı hatalar |
| 10.1.4 | çekirdek syscall gövdesi | yetki/bellek denetimi | errno sınıfı hatalar |
| 10.1.5 | sürücü | aygıtözel iş | sürücüye özgü hata sınıfları (⚠️ ayrıntı) |
| 10.1.6 | aygıt | fiziksel işlem | donanım durumu (kayıp, taşma, kilit) |

> §10.1.6 satırındaki donanım durumları, üretilen sürücü raporunun
> "olay" başlığının kaynağıdır; hangi aygıtın hangi durumu ürettiği
> aygıtözel olduğundan bu dosyada sayısal iddia kurulmaz.

### §10.2 IOCTL yüzeyi — kavramsal paketleme (işaretli)

`ioctl`, dosya tanıtıcısı üzerinden sürücüye özgü komut gönderme desenidir
(§9'daki birinci sınır satırının konusudur). Komut kodunun kavramsal
bileşenleri (doktrin — bu turda tescil edilmedi, ⚠️ VERIFICATION REQUIRED):

| # | Bileşen | Ne belirler | Risk |
|---|---|---|---|
| 10.2.1 | yön | okuma mı yazma mı (sürücü→uygulama / uygulama→sürücü) | yanlış yön = tanıtıcı taşması |
| 10.2.2 | boyut | aktarılacak veri boyutu | boyut uyuşmazlığı = bellek bozulması |
| 10.2.3 | sınıf/sürücü ailesi | hangi sürücü yorumlayacak | sınıf çakışması = yanlış komut |
| 10.2.4 | sayısal komut | sürücü içinde ayrıştırma | numara istikrarı sürücüye bağlı |

PHP yüzeyi gerçeği: bu ağacın beyaz liste dosyalarında `ioctl` arayüzünün
PHP'den kullanımı için tescilli repo satırı **yoktur**; §9'daki sınırlar
tablosu bunu zaten taşır. Bu bölüm PHP'de karşılığı var mı sorusunu
**işaretle kapatır** (⚠️ VERIFICATION REQUIRED — doğrulanmadı, "yoktur"
iddiası da kurulmaz).

### §10.3 Hata yansıma zinciri — aygıttan log'a

```text
[aygit: hata durumu]
     |
     v
[surucu: hata kodu dondurur]
     |
     v
[cekirdek: errno olarak isimlendirir]
     |
     v
[sargi katmani: errno'u gorunur yapar]
     |
     v
[PHP: yanlis donus degerini yakalar]
     |
     v
[uygulama log kaydi / geri donus kodu]
```

| # | Halka | Denetim deseni | CoreMusic sınıfı |
|---|---|---|---|
| 10.3.1 | PHP dönüş değeri denetimi | yanlış değer kontrolü | mevcut kod deseni (§7.1 kanıtı) |
| 10.3.2 | süreç çıkış kodu denetimi | sıfır olmayan çıkış kodu | mevcut kod deseni (§7.1 kanıtı) |
| 10.3.3 | log kaydı | olay sonrası iz | L0 gözlem gereksinimi |
| 10.3.4 | eskalasyon | sınıflandırma → olay sahibi | §10.7 matrisi |

### §10.4 Tanıtıcı sahipliği ve devir (doktrin)

| # | Olay | Tanıtıcıya etki | Denetim |
|---|---|---|---|
| 10.4.1 | süreç oluşturma (fork sınıfı) | tanıtıcılar kopyalanır, dosya konumu ortaktır | çocukta kapatma ebeveyne zarar verir (kavramsal uyarı) |
| 10.4.2 | devir (exec sınıfı) | kapatma bayrağı dışındakiler devredilir | bayrak deseni ⚠️ VERIFICATION REQUIRED |
| 10.4.3 | kapatma | çekirdek kaynağı serbest | çift kapatma = yanlış tanıtıcı riski |
| 10.4.4 | tanıtıcı numarası yeniden kullanımı | serbest kalan numara başkasına verilir | eski tanıtıcıyla işlem = yanlış hedef |

### §10.5 PHP yüzeyinde gözlem noktası genişlemesi (§7.1 üzerine)

§7.1'deki tek tescilli canlı kanıt (`shell_exec` satır aralığı) bu dosyanın
ölçüm kapılarından biridir. §10 bu gerçeği **genişletir, tekrarlamaz**:
sürücü/sürüm olaylarının PHP yüzeyinde hangi sınıf desenlerle yakalanabileceği:

| # | Desen | Ne yakalar | Sınıf |
|---|---|---|---|
| 10.5.1 | dönüş değeri + boş sonuç ayrımı | komut hiç çalıştı mı | tescilli desen (§7.1) |
| 10.5.2 | süreç çıkış kodu ayrımı | komut çalıştı ama başarısız mı | tescilli desen (§E.1-§E.4) |
| 10.5.3 | dosya akışı hata/son denetimi | çıktı okunabilir kaldı mı | tescilli desen (§E.3) |
| 10.5.4 | zaman aşımı denetimi (sınırlama) | askıda kaldı mı | ⚠️ repo kanıtı yok — hedef desen |
| 10.5.5 | olay kaydı (log) | tekrar eden hata modeli | ⚠️ repo kanıtı yok — hedef desen |

### §10.6 Olay sınıflandırması matrisi

| # | Sınıf | Belirti | İlk aksiyon | Öncelik |
|---|---|---|---|---|
| 1 | aygıt kaybı | tanıtıcı geçersiz / cihaz yok | durdur → yeniden dene → fallback | yüksek |
| 2 | izin reddi | erişim reddi dönüşü | yetki denetimi | yüksek (güvenlik) |
| 3 | biçim/protokol hatası | beklenmeyen bayt düzeni | girişi reddet | orta |
| 4 | askıda kalma | zaman aşımı sınırı | sonlandır → olay kaydı | orta |
| 5 | taşma/bitlik | tampon aşımı | reddet → boyut denetimi | yüksek |
| 6 | tekrarlayan hata (flapping) | kısa aralıklı tekrar | eskalasyon | yüksek |

### §10.7 Rolli dağılımı — §9 matrisine ek satırlar

| Modül | Sürüm sorumlusu | Olay sahibi | Ek not |
|---|---|---|---|
| PHP sargı deseni (§10.5) | K000-isletim-sistemi | K000-isletim-sistemi | uygulama içi |
| sürücü yüzeyi (§10.2) | K000-isletim-sistemi | ⚠️ atanmadı | sürücü kodu bu depoda yok |
| aygıt politikası (§10.4) | K000-isletim-sistemi | ⚠️ atanmadı | donanım bağımsız sınır |

### §10.8 Sürüm kapısı genişletmesi (§8-§9 üzerine)

| # | Kapı | Koşul | Durum |
|---|---|---|---|
| 10.8.1 | sürüm notu | §8'deki çıktı listesi | mevcut |
| 10.8.2 | canlı doğrulama raporu | ilk üretim doğrulaması | mevcut |
| 10.8.3 | olay raporu şablonu | §10.6 matrisinin sınıfları | bu bölümde tanımlandı |
| 10.8.4 | geri alma (rollback) kaydı | sürüm geri alınması | ⚠️ şablon yok — bir sonraki tur |

### §10.9 Belirsizlik ve sınır tablosu (§10 kapsamı)

| # | Konu | Durum | Davranış |
|---|---|---|---|
| 10.9.1 | ioctl komut kodu sözdizimi (sayısal) | ⚠️ VERIFICATION REQUIRED | sayı yazılmaz |
| 10.9.2 | sürücü modülü yükleme araçları | ⚠️ VERIFICATION REQUIRED | ad yazılmaz |
| 10.9.3 | PHP'de ioctl kullanımı | ⚠️ VERIFICATION REQUIRED | "yoktur" iddiası da kurulmaz |
| 10.9.4 | gerçek üretim olay kayıtları | UNKNOWN | ölçüm yok |
| 10.9.5 | dağıtıma özgü sürücü kurulumu | ⚠️ VERIFICATION REQUIRED | ad yazılmaz |

### §10.10 Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir; mevcut
iddia·URL·tarih·durum sayıları aynen kalır. §10, §7-§9'daki tescilli
kanıtlar üzerine sınıflandırma ve desen genişletmesi ekler; yeni URL 0,
yeni repo satırı iddiası 0. Ölçüm kapıları §7.1-§7.2'ye bağlıdır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

### §10.11 Olay sınıflandırmasının katmanlara göre ayrışması (genişletme)

§10.6'daki olay matrisine bu bölüm katman sütunu ekler — aynı olay farklı
katmanda farklı anlam taşır:

| Olay | Uygulama katmanı | Sistem çağrısı katmanı | Sürücü katmanı | Aygıt katmanı |
|---|---|---|---|---|
| gecikme | kullanıcıya yansıyan bekleme | zaman-aşımı kodu | tampon-taşması bayrağı | fiziksel kuyruk |
| veri kaybı | dosya-boşluğu uyarısı | kısmi-okuma sonucu | atlanan örnek | örnek-noksanlığı sayacı |
| erişim reddi | politika uyarısı | izin hata kodu | kanal-kapalı durumu | elektriksel yokluk |
| durum değişimi | yeniden-deneme tetiği | durum-geçişi kodu | bayrak-k değişimi | bağ-kesme olayı |
| aşırı yük | kuyruk-derinliği uyarısı | dolu-tampon sonucu | akış-kontrol bayrağı | örnek-red sayacı |

**Okuma kuralı:** olayın "kaybı" nerede sayıldığı önemlidir — uygulama
katmanındaki sayaç dâhili örnekleri, aygıt katmanındaki sayaç elektriksel
örnekleri sayar; ikisi eşit çıkmak zorunda değildir ve eşitsizlik hata
değil, iki ölçümün farklı yerlerde durduğunun kanıtıdır (ölçüm kapıları
§7.1-§7.2'ye bağlıdır).

### §10.12 Yansıma zinciri — sıralama ve gecikme kuralları (doktrin)

§10.3'teki zincirin zaman davranışı bu bölümde sabitlenir:

1. **yansıma sırası:** olay kaynağından en uzak gözlem noktasına doğru
   ilerler; hiçbir katman kendi olayını bir öncekinden önce yayınlayamaz
   (sıralı-iletim kuralı);
2. **sıralama kaybı:** bir katman sırayı ihlal ederse zincir geri kalanı
   yeniden sıralamaz — kayıp görünür işaretlenir, sessizce düzeltilmez;
3. **yansıma gecikmesi:** her katman kendi gecikmesini ekler; toplam
   gecikme katman-ları toplamıdır ve zincirin uçlarındaki iki ölçüm
   arasındaki fark bu toplamı verir;
4. **tekillik:** aynı olayın iki farklı kaynaktan gelmesi (çift-rapor)
   olasıdır; tekillik-avı uygulama katmanının sorumluluğundadır, sürücünün
   değil.

**Gecikme ölçümü kapısı (tarif):** toplam yansıma gecikmesi, zincirin ilk ve
son gözlem noktası arasındaki farktır. Bu kapı bu turda kapatılmaz — ölçüm
§7.1-§7.2 kapısının alanındadır, sayı burada uydurulmaz.

### §10.13 Sürüm kapısı genişletmesi — karar tablosu (tarif)

§10.8'deki sürüm kapısına bu bölüm karar sütunu ekler:

| Sürüm değişikliği tipi | Karar | Gerekçe |
|---|---|---|
| geri-uyumlu ekleme (yeni opsiyonel alan) | kapıyı aç | mevcut çağıranlar etkilenmez |
| geri-uyumlu genişletme (yeni hata kodu) | koşullu aç | çağıran bilinmeyen kodu yutabilmeli — yutmama davranışı belgelenmeden açılmaz |
| davranış değişikliği (aynı kod, farklı sonuç) | kapalı tut | sürüm etiketi gerçeği yansıtmaz |
| alan kaldırma / yeniden adlandırma | kapalı tut | doğrudan kırılma |
| varsayılan değişikliği | kapalı tut | gözle görünür davranış kayması |

**Kapı koşulu:** yalnız ilk satır koşulsuz açıktır; ikinci satır "koşullu"
dur — koşulun kendisi (bilinmeyen-kod yutma davranışı belgesi) bu dosyada
mevcut değildir, bu yüzden kapı bu turda açılmaz ve sürüm iddiası yazılmaz.

### §10.14 Belirsizlik genişletmesi ve dürüstlük bloğu (tamamlama eki 2)

§10.9'daki belirsizlik tablosuna bu bölüm iki satır ekler:

| Belirsizlik | Durum | Neden kapandı |
|---|---|---|
| yansıma zinciri-nde sıralama kaybı davranışı sürücüden sürücüye değişir mi | açık | sürücü başına ölçüm gerekir (§7.1 kapısı) |
| tekillik-avı hangi katmanda zorunlu | açık | mimari karar — bu dosyanın tek başına kapan yetkisi yok |

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir; mevcut
iddia·URL·tarih·durum sayıları aynen kalır. §10.11-§10.13 ekleri 0 URL, 0
repo iddiası, 0 ölçüm sonucu içerir; yalnız matris genişletmesi, doktrin
listesi ve kapı tariflerinden oluşur. Ölçüm kapıları §7.1-§7.2'ye bağlıdır
ve bu ekle kapanmaz; §10.10 dürüstlük kaydı aynen korunur.

### §10.15 Çağrı ömrü durum makinesi (derinleştirme eki 3 — tarif)

Bu bölüm §10.1-§10.4 arasında tarif edilen çağrı yolunu durum makinesi
olarak yeniden ifade eder; yeni iddia eklemez, mevcut tarifleri tek
şemada toplar.

**Durumlar (7):**

| # | Durum | Tanım | Bulunduğu katman |
|---|---|---|---|
| S0 | HAZIR | çağıran kullanıcı modunda çağrıyı başlatır | uygulama |
| S1 | YAKALAMA | çekirdek yakalama noktasına giriş (trap entry) | çekirdek |
| S2 | AYRIŞTIRMA | çağrı numarası + argümanların çözülmesi | çekirdek |
| S3 | İZİN | yetki/cap doğrulaması | çekirdek |
| S4 | SEVK | ilgili sürücüye yönlendirme (dispatch) | çekirdek → sürücü |
| S5 | ÇALIŞTIRMA | sürücünün işi yürütmesi (kesme/bekleme) | sürücü |
| S6 | DÖNÜŞ | sonucun çağıranğa iade edilmesi | çekirdek → uygulama |

**Geçiş kuralları:**

1. S0→S1 yalnız sistem çağrısı yönergesiyle olur; normal dallanma ile
   yakalama noktasına geçilemez;
2. S2 başarısızsa (geçersiz numara) zincir S5'e hiç girmez — S6'ya
   doğrudan hata ile döner;
3. S3 reddedilirse S4-S5 atlanır; reddin çağıranğa görünür olması
   zorunludur (sessiz red yasak — §10.9'un red doktriniyle aynı);
4. S5 kesme bağlamında çalışıyorsa S6'ya geçiş ertelenir; erteleme
   sürücünün kendi kuyruğunda izlenir;
5. S6 her durumda tek sefer çağrılır — çift-return (iki kez iade)
   bozulma sınıfıdır (§10.19 Ç1).

**İnvaryantlar:**

- I1: her yakalama bir ve yalnız bir dönüş ile sonuçlanır;
- I2: S3 geçmeden S5'e geçiş yoktur (izin atlaması yasak);
- I3: durumlar yalnız ileri doğru akar; geri dönüş (rollback) ayrı bir
  hata yoludur, durum geri adımı değildir;
- I4: S4'te sürücü bulunamazsa zincir S6'ya "yok" işaretiyle döner —
  asla başka bir sürücüye sessizce yönlendirilmez.

### §10.16 Kesme bağlamı ile senkron çağıran etkileşimi (tarif)

§10.15'in S5-S6 geçişi, sürücünün kesme bağlamında mı yoksa süreç
bağlamında mı çalıştığına göre değişir. Bu bölüm bu farkı tarif eder.

| Senaryo | Çağrıran durumu | Sonucun iletimi | Risk |
|---|---|---|---|
| sürücü süreç bağlamında tamamlarsa | bekliyor (uykuya dalmış olabilir) | doğrudan iade | bekleyen süreç uyandırma unutulursa sonsuz bekleme |
| sürücü kesme bağlamında tamamlarsa | çalışır durumda | kesme sonrası iade | kesme bağlamında bloklayan yol DEADLOCK |
| sürücü yarısını kesmede yarısını süreçte yaparsa | bekliyor | kuyrukla iade | iki yarı arasında durum tutarsızlığı |

**Doktrin (tarif):** kesme bağlamında bloklayan her yol bozulma adayıdır;
sürücü işi ya tamamen kesmede bitirmeli ya da süreç bağlamına devretmelidir
— devir noktaları sürücünün kendi sözleşmesindedir ve bu dosyanın tek
başına belgeleyemediği sürücü-içi kararlardır.

**Kapı:** bu doktrinin sürücü bazında doğrulanması §7.1-§7.2 ölçüm
kapısının alanındadır; hangi sürücünün hangi modeli kullandığı bu turda
sayılmaz.

### §10.17 Sürücü sözleşmesi matrisi genişletmesi (tarif)

§10.6'daki sözleşme satırlarına bu bölüm üç boyut ekler:

| Boyut | Soru | Sözleşmeyi kim taşır |
|---|---|---|
| yaşam döngüsü | sürücü ne zaman yüklenir/boşaltılır | çekirdek + sürücü ortak |
| kaynak sahipliği | tampon sahipliği kime ait | sürücü (devredilebilir) |
| eşzamanlılık | aynı cihaza eşzamanlı çağrı nasıl sıralanır | çekirdek (sıra) + sürücü (iç kilit) |

**Boyut kuralı:** üç boyut da ikili (iki tarafın karşılıklı taahhüdü)
olduğu için tek taraflı revizyon yapılamaz — bir taraf sözleşmeyi
değiştirirse diğer tarafın yeniden uyumu ayrı bir sürüm kapısıdır
(§10.13 karar tablosu geçerlidir).

### §10.18 İzin doğrulama sırası tarifi

§10.15'teki S3 durumunun iç sırası bu bölümde tarif edilir; sıra
doktrindir, uygulama-özel liste değildir:

1. **çağrı-numarası geçerliliği** — bilinmeyen numara S2'de düşer, S3'e
   hiç gelmez;
2. **argüman-sınırı kontrolü** — işaretçiler/sayıtlar sınır dışındaysa
   S3 reddeder; sınırlama çekirdek-kopyalama ile yapılır (kullanıcı
   belleğine doğrudan geri yazmak yasak);
3. **yetki denetimi** — çağıranın bu çağrı için yetkisi var mı;
4. **kota denetimi** — çağrı çağırana ait kotaları aşar mı (aşım red,
   kısıtlama değil);
5. **durum denetimi** — cihaz/kaynak bu anda bu çağrıyı kabul eder mi
   (kapalı-kilitli-durum).

**Sıra kuralı:** 1→5 soldan sağa uygulanır; ilk düşen nokta sonucu
belirler ve sonraki kontroller atlanır (erken-dönüş kuralı). Ters sırada
denetim yanlış red üretir: örn. yetkiyi sınır-kota öncesi kontrol
etmemek, yetkisiz çağrıyı kota hatasıyla reddeder ve çağıran yanıltılır.

### §10.19 Bozulma sınıfları (derinleştirme eki 3 — taksonomi)

§10.10 belirsizlik tablosundaki bozulma olgusunu bu bölüm sınıflandırır;
sınıflandırma tariftir, tespit-ölçümü değildir:

| Sınıf | Tanım | Görünür belirti | İhlal edilen kural |
|---|---|---|---|
| Ç1 çift-iade | tek çağrının iki kez sonuçlanması | çağıran iki kez uyanır | invaryant I1 |
| Ç2 sessiz-red | reddin çağıranğa bildirilmemesi | çağıran başarı varsayar | §10.9 red doktrini |
| Ç3 sıra-ihlali | olayların katman sırası dışında iletilmesi | üst katman alttan önce görür | sıralı-iletim kuralı |
| Ç4 blok-kalıntı | kesme bağlamında bekleyen yol | sistem donması | §10.16 doktrini |
| Ç5 sahiplik-bozulma | tamponun iki katmanda sahipliği | veri yarışması | §10.17 boyut-2 |

**Taksonomi kuralı:** beş sınıf da *tarif* düzeyindedir — hangi sürümde·
hangi sürücüde görüldüğü bu turda sayılmaz; tespit §7.1-§7.2 ölçüm
kapısının işidir. Sınıf etiketi yalnız kanıtla konur.

### §10.20 Dürüstlük bloğu (derinleştirme eki 3)

Bu ek (§10.15-§10.19) kanıt oranını DEĞİŞTİRmez — yeni içerik yeni
kanıt değildir: 0 URL, 0 repo iddiası, 0 ölçüm sonucu, 0 sürüm sayısı
eklenmiştir; yalnız durum makinesi, sözleşme matrisi, izin sırası,
bozulma taksonomisi ve kapı tarifleri genişletilmiştir. Ölçüm kapıları
§7.1-§7.2'ye bağlıdır ve bu ekle kapanmaz; §10.14 dürüstlük kaydı aynen
korunur. Dosyanın önceki bölümlerindeki iddia·URL·tarih·durum sayıları
aynen kalır.

### §10.21 PHP gözlem örneği — §10.5 desenlerinin kodu (2026-10-14)

§10.14 dürüstlük kaydı bu bölüm için de geçerlidir, ancak bu bölüm §10.20'den
farklı olarak **2 repo kanıtı ekler** (aşağıda sayılır). Bloklar
derlenebilirlik iddiası TAŞIMAZ; kaynakları §10.21.2 tablosudur.

#### §10.21.1 Desen bloğu

```php
<?php
declare(strict_types=1);

// Desen 10.5.1 + 10.5.2 — repo kanıtı: shared/src/AI/AIEngine.php:295
//   $output = shell_exec($cmd);
// Repo'daki $cmd değeri AI komutudur; burada örnek komut kullanılmıştır — desen ortaktır.
$cmd   = 'uname -r';   // örnek komut — üretimde bilinen bir binary
$output = shell_exec($cmd);
if ($output === null || $output === '') {
    // desen 10.5.1: dönüş boş — komut hiç çalıştı mı?
    fwrite(STDERR, "komut calismadi veya cikti uretmedi\n");
    exit(1);
}

// desen 10.5.2 + 10.5.3: süreç çıkış kodu + akış okuma (proc_open üzerinden)
$spec = [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']];
$p = proc_open($cmd, $spec, $pipes);
if (is_resource($p)) {
    $cikti = stream_get_contents($pipes[1]);   // desen 10.5.3: dosya akışı okuma
    $hata  = stream_get_contents($pipes[2]);
    fclose($pipes[0]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $kod = proc_close($p);                     // desen 10.5.2: çıkış kodu ayrımı
    if ($kod !== 0) {
        fwrite(STDERR, "komut calisti ama basarisiz: kod=$kod hata=$hata\n");
    }
}
```

§10.5.4 (zaman aşımı) ve §10.5.5 (olay kaydı) desenlerinin hâlâ repo kanıtı
YOKTUR — bu bölüm de uydurmaz; hedef desen olarak kalır (⚠️).

#### §10.21.2 §10.21 kanıt tablosu

| # | İddia | Kaynak | Tarih | Durum |
|---|---|---|---|---|
| 1 | repo'da `shell_exec` kullanımı — §7.1 tescilli kanıtın doğrulaması | `shared/src/AI/AIEngine.php:295` | 2026-10-14 | ✅ diskte doğrulandı |
| 2 | repo'da `proc_open` kullanımı | `shared/tests/Repository/ApiKeyCreateCommandTest.php:253` | 2026-10-14 | ✅ diskte doğrulandı |
| 3 | PHP `proc_open` resmi referansı | https://www.php.net/manual/en/function.proc-open.php | 2026-10-14 | ✅ HTTP 200 · VAR (envanter) |

Özet: yeni URL 0 · yeni repo kanıtı 2 · ölçüm sonucu 0 (kapı §7.1-§7.2
açık kalır).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
