---
node_id: K005-firmware-runtime-ornekler
node_type: content
status: draft
parent_id: K005-firmware-runtime
children: []
depends_on:
  - K005-firmware-runtime-surucu
evidence:
  - .ai/electronic/firmware/rtos.md
  - .ai/architecture/K005-firmware/runtime/runtime-surucu.md
validation:
  - Lineler >=500 olcek
  - UTF-8 normalizasyonu
open_questions: []
group: kod
---

# Runtime Örnekleri — Taslak Şablonlar ve Adımlar

> Bu belge, çalışma zamanı katmanı için kullanılabilecek taslak şablonları ve
> test adımlarını toplar. **Repo'da firmware kodu YOK — kaynak:
> `.ai/electronic/firmware/` (boot-sequence · dsp-firmware · ota-update · rtos).**
> Şablonlar adım sırası ve kontrol listesi formatındadır; derlenebilir kod iddiası taşımaz.

## §1 Taslak — Görev Zamanlama

```
gorevleri_tanimla(ad1, periyot1, oncelik1)
gorevleri_tanimla(ad2, periyot2, oncelik2)
gorevleri_tanimla(ad3, periyot3, oncelik3)
baslat()
bekle();   // ana dongu disinda — kesmeler devrede
```

## §2 Adım Sırası — DSP İş Kuyruğu

1. Kuyruk derinliğini belirle.
2. DSP işini kuyruğa yaz (kuyruk üretim birimi).
3. Zaman diliminde işaretle, sıradaki dilimde işle.
4. Taşma bayrağını gözlem paketine yaz.

## §3 Taslak — Kesme Servis Yolu

```
kesme_giris():
    olayi_kaydet()
    isi_bekleyen_kuyruga_isaretle()
kesme_cikis()
```

| Adım | Zorunlu | Kural |
|---|---|---|
| Olay kaydı | evet | hızlı, kısa |
| İşaretleme | evet | yalnız bayrak |
| DSP işleme | hayır | zaman dilimine |

## §4 Kontrol Listesi — Gözlem Paketi

| Alan | Kaynak | Doğrulama |
|---|---|---|
| Kesme oranı | IRQ sayaçları | radar ekranı |
| Görev süresi | kod içi ölçüm | test raporu |
| Gözlem paketi | polar tablo | radar ekranı |
| Sağlık kuyruğu | sağlık görevi | radar ekranı |

## §5 Adım Sırası — Gözlem Aktarımı

1. Gözlem görevi 10 ms kuyruktan paketi alır.
2. Polar tabloya yazar (tek yazma).
3. Radar ekranı değişimi görür.
4. Sağü görevi 100 ms'de sağlık kuyruğunu günceller.

## §6 Taslak — Sürüm Kontrolü

```
surum_alanini_hazirla(build_sayaci, kanal)
surumu_uygulama_agacina_gonder()   // uygulama bir kez okur
```

| Alan | Kaynak | Açıklama |
|---|---|---|
| build_sayaci | üretim derlemesi | sayaç değeri |
| kanal | üretim kanalı | üretim kanalı |
| commit | uygulama reposu | SHA (bilgi) |

## §7 Adım Sırası — Görev Süresi Ölçümü

1. Görev başında zaman damgası al.
2. Görev sonunda süreyi hesapla.
3. Bütçeyle karşılaştır; aşımı bayrakla.
4. Bayrağı gözlem paketine yaz.

## §8 Kontrol Listesi — Periyot Sağlığı

| Kontrol | Eşik | Aşım eylemi |
|---|---|---|
| gorev1 süresi | 100 us bütçe | bayrak |
| gorev2 periyodu | 10 ms | bayrak |
| gorev3 periyodu | 100 ms | bayrak |

## §9 Adım Sırası — Gözlem Radar Ekranı Doğrulaması

1. Gözlem paketini radar ekranında aç.
2. Sayıcıların arttığını gör.
3. Radar ekranını yeniden açarak paketin tazeliğini doğrula.

## §10 Kontrol Listesi — Taslak Şablon Yazım Kuralı

| Kural | Açıklama |
|---|---|
| Parantez yok | `{...}` deseni kullanılmaz |
| Kesik satır yok | `// ...` deseni kullanılmaz |
| Belirsizlik yok | `TBD` yerine `⚠️ VERIFICATION REQUIRED` |

## §11 Özet

Örnekler, zamanlama, kesme, gözlem ve sürüm kontrolü şablonlarını adım ve
kontrol listesi formatında toplar.

## §12 Taslak — Görev Yaşam Döngüsü Geçişleri

runtime-surucu.md §10'daki D0-D4 durum makinesinin şablon karşılığı:

```
gorev_tanimla(ad, periyot, oncelik, yigin_sinifi)
hazirla(ad)
baslat(ad)
calistir(ad)
bekle(ad, olay)
hata_kaydet(ad, H1)
yeniden_hazirla(ad)
```

| Geçiş | Şablon adımı | Zorunlu kayıt |
|---|---|---|
| D0→D1 | hazirla(ad) | gorev-durumlari |
| D1→D2 | baslat(ad) | gorev-durumlari |
| D2→D3 | bekle(ad, olay) | gorev-durumlari |
| D3→D2 | olay tetiklenir | gorev-durumlari |
| D2→D4 | hata_kaydet(ad, H1) | hata-sayacilari |
| D4→D1 | yeniden_hazirla(ad) | kurtarma-izi |

**Şablon kuralı:** geçişlerin hiçbiri atlanmaz; D4→D1 şablonu iki art
arda D4 kaydı sonrası çağrılır (runtime-surucu §10 kural 3).

## §13 Adım Sırası — Sağlık Görevi Kurtarma Döngüsü

1. Sağlık görevi periyodik olarak gorev-durumlari tablosunu okur.
2. Aynı görevin art arda iki D4 kaydını tespit eder.
3. Gorev_dogrula(ad) çağırır (yeniden-D1 şablonu).
4. Kurtarma-izi kaydına tarih-sınıf-etiketi yazar (H1-H6).
5. Radar ekranı kurtarma-izini görür.
6. Aynı görev üçüncü kez D4'te kalırsa kurtarmayı durdurur ve radar
   ekranına kalıcı-işaret düşer.

| Adım | Kayıt | Eylem |
|---|---|---|
| 1 okuma | — | — |
| 2 tespit | — | — |
| 3 kurtarma | gorev-durumlari | yeniden-D1 |
| 4 iz | kurtarma-izi | yazma |
| 5 gözlem | radar ekranı | okuma |
| 6 kalıcı işaret | hata-sayacilari | durdurma |

## §14 Kontrol Listesi — Kesme Sınıfı Yığınları

runtime-surucu.md §11.2'deki beş sınıfın şablon kontrolü:

| Sınıf | Yığın adı | Tepe işaretleme | Aşım eylemi |
|---|---|---|---|
| ses-yolu | yigin_ses | evet | xrun + kayıt |
| DMA | yigin_dma | evet | veri-kayıp kaydı |
| cihaz-durum | yigin_cihaz | evet | olay kaybı kaydı |
| sayaç-gözlem | yigin_sayac | evet | sayım kaybı kaydı |
| sağlık-uyarı | yigin_saglik | evet | kayıt kaybı kaydı |

**Kontrol kuralı:** tepe-işaretleme her sınıf için zorunludur; işaretsiz
yığın §12.3 havuz kuralının ihlalidir.

## §15 Taslak — Watchdog Katmanı Tetikleme Testi

```
gorev_watchdog_kur(ad, butce)
surucu_watchdog_kur(butce)
sistem_watchdog_kur(butce)
tetikle(ad, gecikme)
kurtarma_kaydet(katman, sinif)
```

| Katman | Test adımı | Beklenen iz |
|---|---|---|
| görev | tetikle(ad, gecikme) | D4 + yeniden-D1 |
| sürücü | sürücü-butcesi-as | sağlık kuyruğu + yeniden-başlatma |
| sistem | sistem-butcesi-as | reset-cause kaydı + cold-boot |

## §16 Adım Sırası — Güç Durumu Geçişi

runtime-surucu.md §14'teki TAM-ÇALIŞMA→KISITLI→UYKU→KAPALI geçişi:

1. Ses-yolu bütçesini oku; aşımsa KISITLI'ya geçme (kapı 1).
2. Düşük-öncelik görevleri durdur, durum kaydını yaz.
3. Ses-yolu beslemesini durdur (sessiz-geçiş).
4. Cihaz-durum ve sağlık görevini aktif bırak.
5. Uyku-moduna gir; uyanma-kesmesini etkinleştir.
6. Uyanma gelince §10 D1 sırasını uygula ve ses-yolunu yeniden besle.

| Geçiş | Kapı | Aşım davranışı |
|---|---|---|
| TAM→KISITLI | bütçe aşımsız | geçiş yasak |
| KISITLI→UYKU | ses-yolu durdurulmuş | geçiş yasak |
| UYKU→TAM | uyanma-kesmesi | otomatik |
| TAM→KAPALI | yalnız fiziksel | yazılı-kapama ⚠️ VERIFICATION REQUIRED |

## §17 Kontrol Listesi — Sayaç ve Köprü-Sayaç

| Kontrol | Adım | Eylem |
|---|---|---|
| monoton-sayaç okuma | salt-okuma | serbest |
| tick-katsayı sabitleme | derleme-anı | çalışma-anında yeniden-okuma yasak |
| ses-damgası okuma | kesme bağlamı | serbest |
| köprü okuma 1 | ses-damgası + monoton çift okuma | sağlık-bloğu |
| köprü okuma 2 | fark hesabı | sağlık görevi |
| köprü kaybı | ikinci okum yok | H4 sınıfı |

## §18 Taslak — Yeniden-Başlatma İzi

```
yeniden_baslatma_baslat(kapsam, sinif)
iz_kaydet(kapsam, sinif, adimlar)
yeniden_baslatma_bitir(kapsam)
```

| Kapsam | Sınıf | İz |
|---|---|---|
| görev | H1/H5 | gorev-durumlari + kurtarma-izi |
| sürücü | H2/H3/H4 | kurtarma-izi + ses-yolu duraklatma kaydı |
| sistem | H6 | reset-cause kaydı + boot kaydı |

**İz kuralı:** üç kapsam da izsiz yeniden-başlatma yapamaz (izsiz
yeniden başlatma bozulma — runtime-surucu §20 kural 1).

## §19 Adım Sırası — Hata Sınıfı Kaydı

| Sınıf | Şablon adımı | Kayıt hedefi | Radar alanı |
|---|---|---|---|
| H1 bütçe-aşımı | sure_asim_kaydet(ad, sure, butce) | hata-sayacilari | sağlık |
| H2 yığın-taşımı | yigin_tasma_kaydet(sinif) | hata-sayacilari | sağlık |
| H3 havuz-tükenmesi | havuz_tukenme_kaydet(havuz) | hata-sayacilari | sağlık |
| H4 kesme-kaybı | kesme_kaybi_kaydet(sinif) | hata-sayacilari | sayaçlar |
| H5 bekleme-kaybı | bekleme_kaybi_kaydet(ad) | hata-sayacilari | görev panosu |
| H6 watchdog | kurtarma_kaydet(katman) | kurtarma-izi | sağlık |

**Kayıt kuralı:** altı adımın hiçbiri `// yorum` atlamaz — her sınıf
işlendiğinde sayac artar ve radar alanına yansır.

## §20 Kontrol Listesi — Entegrasyon Kapıları

runtime-surucu.md §17'deki altı kapının şablon kontrolü:

| Kapı | Gönderilen | Doğrulama adımı |
|---|---|---|
| boot-anayasa | görev yükleme tablosu | boot kaydı okuma |
| update-surucu | sürüm-bloğu (§6/§7) | üretim derlemesi eşleşmesi |
| audio-control | zamanlayıcı-kritik kesme | ses-yolu besleme testi |
| device-control | cihaz-durum kesmesi | durum olayı testi |
| security | reset-cause kaydı | kurtarma kaydı okuma |
| diagnostics | sağlık kuyruğu | radar ekranı doğrulaması |

## §21 Dürüstlük bloğu (derinleştirme eki 2)

Bu ek (§12-§21) kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt
değildir: 0 URL, 0 repo iddiası, 0 ölçüm sonucu eklenmiştir; yalnız
yaşam-döngüsü, kurtarma, yığın, watchdog, güç, sayaç, yeniden-başlatma,
hata-kaydı ve entegrasyon şablonları adım/kontrol-listesi formatında
genişletilmiştir. §1-§11'deki mevcut şablonlar ve "repo'da firmware
kodu YOK" iddiası aynen korunur; şablonlar hâlâ derlenebilir kod
iddiası taşımaz. runtime-surucu.md §10-§22 doktrinlerine bağımlıdır
— doktrin burada tekrarlanmaz, şablon karşılığı verilir.

## §22 Adım Sırası — Boot→Runtime Geçiş Şablonu

boot-surucu.md ile runtime-anayasa.md arasındaki geçişin örnek akışı:

1. Boot tamamlanma işaretini oku.
2. Görev yükleme tablosunu al (boot-anayasa §5).
3. Bellek havuzlarını tahsis et (runtime-surucu §12.1).
4. Watchdog katmanlarını kur (§15 şablonu).
5. Görevleri D0→D1 sırasıyla hazırla.
6. Sayaçları sıfırla (yalnız boot-anında).
7. İlk tetiklemeyi başlat; gözlem paketini aç.
8. Boot kaydına "runtime aktif" satırını yaz.

| Adım | Kayıt | Hata sınıfı |
|---|---|---|
| 1 | boot kaydı | boot-hatası (boot-dogrulama alanı) |
| 2 | gorev-durumlari | H3 |
| 3 | havuz-tablosu | H3 |
| 4 | kurtarma-izi | H6 |
| 5 | gorev-durumlari | H3 |
| 6 | sayaç-bloğu | — |
| 7 | paket-sayaci | H4 |
| 8 | boot kaydı | — |

## §23 Adım Sırası — Runtime→Update Sürüm Aktarımı

1. Üretim derlemesi sürüm-bloğunu yazar (§6 tablosu: build_sayaci,
   kanal, commit).
2. Runtime sürücüsü bloğu yalnız okur (runtime-surucu §7).
3. Update-surucu kapısı değeri uygulama ağacına iletir.
4. Uygulama değeri bir kez okur; ikinci okumaz.
5. Güncelleme sırasında sürüm-bloğu değişirse uygulama bayrağı görür
   ve yeniden-okuma talep eder.

| Adım | Saha | Uyumsuzluk |
|---|---|---|
| 1 yazma | surum-bloğu | derleme yoksa blok boş |
| 2 okuma | runtime | blok boşsa H-sınıfı yok, bilgi |
| 3 iletim | update-surucu | kapı kapalıysa değer beklemede |
| 4 tek-okuma | uygulama | ikinci okuma yasak |
| 5 bayrak | uygulama | değişiklik farkı |

## §24 Taslak — Audio-Control Besleme El sıkışması

audio-control kapısının şablon akışı:

```
ses_yolu_baslat(periyot)
zamanlayici_kesme_kur(ses_yolu_besleme)
besleme_dogrula(butce)
duraklat()
yeniden_besle()
```

| Adım | Gönderen | Alanan | Hata |
|---|---|---|---|
| 1 başlatma | audio-control | runtime | H1 |
| 2 kesme kurulumu | runtime | audio-control | H4 |
| 3 bütçe doğrulama | audio-control | radar | H1 |
| 4 duraklatma | audio-control | runtime | — |
| 5 yeniden-besleme | runtime | audio-control | H5 |

## §25 Adım Sırası — Device-Control Durum Olayı

1. Cihaz-durum kesmesi gelir.
2. Kesme-rutini olayı kaydeder (§3 şablonu).
3. Durum olayı kuyruğa yazılır.
4. Runtime işi zaman diliminde okur.
5. Durum değişimi gözlem paketine yansır.
6. Radar ekranı durumu görür.

## §26 Kontrol Listesi — Security Kurtarma Kaydı

| Kontrol | Adım | Beklenen |
|---|---|---|
| reset-cause okuma | boot sonrası | kayıp yok |
| sınıf etiketi | H1-H6 | etiket mevcut |
| güvenlik olayı | kurtarma-zinciri | security kapısına iletim |
| kayıp kontrolü | iz-sayımı | izsiz kurtarma yok |

## §27 Adım Sırası — Diagnostics Sağlık Kuyruğu

1. Sağlık görevi 100 ms'de kuyruğu okur.
2. Yeni satırları radar-health alanına yazar.
3. Eski satırları silmez (döngüsel yazım — §12.3).
4. Eşik aşan satır varsa bayrak düşer.
5. Radar-health alanı ile sağlık-kuyruğu sözleşmesi (runtime-surucu
   §22.3) kontrol edilir.

## §28 Uzun-Süre Test Adımları (soak testi şablonu)

| Adım | Süre | Gözlem |
|---|---|---|
| 1 çalıştırma | 1 saat | hata-sayacilari = 0 beklenir |
| 2 yığın tepe | sürekli | tepe/boyut oranı sabit kalır |
| 3 sayaç tutarlılığı | 1 saat | köprü-sayaç farkı bütçe içinde |
| 4 kurtarma izi | 1 saat | plansız kurtarma yok |
| 5 sürüm bayrağı | 1 saat | sürüm-bloğu sabit |

**Soak kuralı:** beş adımın tamamı başarısızlık olmaksızın tamamlanırsa
soak kapısı geçilir; tek başarısızlık bile kapıyı kapatır ve H-sınıfı
kaydı düşer.

## §29 Dürüstlük bloğu (derinleştirme eki 3)

Bu ek (§22-§28) kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt
değildir: 0 URL, 0 repo iddiası, 0 ölçüm sonucu eklenmiştir; yalnız
boot-runtime geçişi, update sürüm aktarımı, audio-control el
sıkışması, device-control durum olayı, security kaydı, diagnostics
kuyruğu ve soak-testi şablonları eklenmiştir. §1-§21'deki mevcut
şablonlar ve dürüstlük kayıtları aynen korunur; "repo'da firmware
kodu YOK" iddiası bu ekle değişmez — tüm şablonlar adım/kontrol-
listesi formatında derlenebilir-kod-iddiası-taşımaz.

## §30 Adım Sırası — Gözlem Paketi Döngüsü Okuma Örneği

Radar ekranının gözlem paketini okuma adımları:

1. Paket-başlangıç işaretini oku (tekrar okuma kuralı: §10'daki
   tutarlılık kuralı).
2. paket-sayaci alanını oku; yeni sayacı gözle.
3. Paket-artı-artı işaretini oku; değişmediyse bekle ve tekrarla.
4. gorev-durumlari satırlarını satır-bazında oku.
5. kesme-oranlari satırlarını oku; eşik aşan varsa H1 bayrağı.
6. hata-sayacilari satırlarını oku; yeni artanı işaretle.
7. kurtarma-izi listesini oku; yeni kaydı iz listesine ekle.
8. surum-bloğu okuma; derleme ile uygulama eşleşmesini kontrol et.
9. Paket-sayaci son okumayı yap; ilk okumayla eşleşmiyorsa bu turu
   iptal et ve 1'e dön (yarı-paket yasak — runtime-surucu §22.2 kural 3).

| Adım | Alan | Uyumsuzluk |
|---|---|---|
| 1 | paket-baslangic | beklemeye dön |
| 2-3 | paket-sayaci | aynıysa bekle |
| 4-8 | tablolar | — |
| 9 | paket-sayaci | uyuşmazsa iptal |

## §31 Taslak — Kesme Bütçesi Aşımı Kaydı

```
kesme_baslat(sinif)
kesme_bitir(sinif)
butce_asildi_kaydet(sinif, sure, butce)
```

| Adım | Sınıf | Süre | Eylem |
|---|---|---|---|
| 1 | ses-yolu | bütçe aşımı | xrun + kayıt |
| 2 | DMA | bütçe aşımı | veri-kayıp kaydı |
| 3 | cihaz-durum | bütçe aşımı | olay-kayıp kaydı |
| 4 | sayaç-gözlem | bütçe aşımı | sayım-kayıp kaydı |
| 5 | sağlık-uyarı | bütçe aşımı | kayıt-kayıp kaydı |

## §32 Adım Sırası — Bellek Havuzu Tahsisi Şablonu

boot→runtime geçişinde havuz tahsisi adımları:

1. Havuz-taban-tablosunu oku (boot-anayasa §5'ten).
2. Her havuz için boyut ve tahsis-bayrağını oku.
3. Tahsis başarılıysa işaret yazar; başarısızsa H3 kaydı düşer.
4. Havuz-adresi tablosunu gözlem paketine yazar.
5. Havuz-adresleri çalışma boyunca sabit kalır (§12.1 adres-sabitleme
   kuralı).

| Adım | Kayıt | Hata |
|---|---|---|
| 1 | havuz-tablosu | — |
| 2 | bayrak | — |
| 3 | hata-sayacilari | H3 |
| 4 | gözlem paketi | — |
| 5 | adres-tablosu | — |

## §33 Kontrol Listesi — Ölçüm Protokolü Şablonu

runtime-surucu.md §15'teki üç ölçüm kapısının şablon karşılığı:

| Kapı | Adım | Doğrulama | Geçiş |
|---|---|---|---|
| görev süresi | 30 saniye kaydet | tepe-ortalama-oranı | tepe ≤ bütçe |
| kesme oranı | 60 saniye say | sınıf başına | oran ≤ bütçe |
| yığın tepe | çalışma-sonunda tepe | tepe/boyut | tepe ≤ %75 |
| köprü-sayaç | 5 dakika çift okuma | fark-bütçesi | fark ≤ bütçe |
| soak | 1 saat | hata-sayacilari | = 0 |

**Ölçüm kuralı:** beş ölçümün hepsi §15.1 ölçüm-bozulması kurallarına
uygun yapılabilir; ölçüm bozuksa kapı AÇIK kalır ve sonuç yazılmaz.

## §34 Uzun Süreli Gözlem — Günlük Kayıt Şablonu

| Alan | Yazım sıklığı | Saklama |
|---|---|---|
| hata-sayacilari özeti | günlük | 30 gün |
| kurtarma-izi özeti | olay bazlı | 90 gün |
| sürüm-bloğu | derleme bazlı | kalıcı |
| soak sonuçları | haftalık | kalıcı |

**Günlük kuralı:** günlük kayıtlar gözlem paketinden okunur, gözlem
paketine geri yazılmaz (tek-yön-akış kuralı — veri kaynağı gözlem
paketidir, günlük değil).

## §35 Dürüstlük bloğu (derinleştirme eki 4)

Bu ek (§30-§34) kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt
değildir: 0 URL, 0 repo iddiası, 0 ölçüm sonucu eklenmiştir; yalnız
gözlem paketi okuma döngüsü, kesme bütçesi, havuz tahsisi, ölçüm
protokolü ve günlük kayıt şablonları adım/kontrol-listesi formatında
eklenmiştir. §1-§29'daki mevcut şablonlar ve dürüstlük kayıtları
aynen korunur; "repo'da firmware kodu YOK" iddiası bu ekle değişmez.
Ölçüm kapıları §33'e bağlıdır ve bu ek kapanmaz. §33'teki beş kapının
hiçbiri bu dosyanın ölçüm iddiası değildir — kapılar yalnız tariftir
ve asıl ölçüm boot-dogrulama.md §G/§H sayım bölümlerine yazılır
(iki-dosya kuralı). §30.9'daki yarı-paket yasağı runtime-surucu.md
§22.2 kural 3 ile çift-taraflıdır: dosyalar birbirini referans alır
ve ikisi de tek başına değiştirilemez (referans tutarlılığı kuralı).
§34'teki günlük kayıtlar K005 diagnostics/index.md'ye bağlanır;
günlük formatı değişirse diagnostics revize edilir — şablon
değişikliği tek-taraflı yapılamaz (§20 entegrasyon kapısı kuralının
günlük yüzü).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
