---
node_id: K005-firmware-runtime-surucu
node_type: content
status: draft
parent_id: K005-firmware-runtime
children: []
depends_on:
  - K005-firmware-runtime-anayasa
evidence:
  - .ai/electronic/firmware/rtos.md
  - .ai/architecture/K005-firmware/runtime/runtime-anayasa.md
validation:
  - Lineler >=500 olcek
  - UTF-8 normalizasyonu
open_questions: []
group: kod
---

# Runtime Sürücü — Zamanlama, Kesme ve Gözlem

> Bu belge `runtime-anayasa.md` kapsamındaki çalışma zamanı yüklerini gerçekleştiren
> bileşenlerin tasarımını tanımlar. Görev, kesme önceliği, gözlem ve sürüm kontrolü
> satırları gerçek bir firmware ağacında nereye yazılır — onu gösterir.
> **Repo'da firmware kodu YOK — kaynak: `.ai/electronic/firmware/` (boot-sequence · dsp-firmware · ota-update · rtos).**

## §1 Kapsam ve Sorumluluk

Runtime sürücü katmanı dört iş yapar: (1) görev gövdesini öncelik ve dönemle
çalıştırma, (2) kesme servis yolunu zamanlama yollarından ayırma, (3) gözlem
sayaclaını üretme, (4) çalışma zamanı sürümlerini kayıt etme.

| Sorumluluk | Girdi | Çıktı | Sahip modül |
|---|---|---|---|
| Görev gövdesi | sinyal + periyot | iş kuyruğu | runtime-surucu |
| Kesme servisi | IRQ bayrağı | rAF bekleyen kuyruk | runtime-surucu |
| Gözlem sayacı | durum geçişleri | sayaç okuması | runtime-anayasa |
| Sürüm kaydı | çalışma zamanı sürümü | kayıt satırı | update-surucu |

## §2 Yerleşim Şeması

```
[bootstrap 200 ms]
   |
   v
[boot completion hook] --> [gorev1 sched 100 us (ana DSP)]
   |                            |
   |                            +--> [gorev2 sched 10 ms (gözlem)]
   |                                 +--> [gorev3 sched 100 ms (sağlık)]
   +--> [kesme: ITM trace]
   +--> [heartbeat GPIO telemetri]
```

## §3 Görev Gövdesi Tasarımı

```cpp
// taslak: gorev1 DSP is parcasinin govdesi
void gorev1_govde() noexcept {
    zaman_kaydini_al(&t0);
    if (sinyal_var_mi()) is_kuyrugunu_isle();
    else kenar_duruma_gec();
    zaman_kaydini_al(&t1);
    gorec_suresini_olc(t0, t1);          // gomulu enstrumantasyon
}
```

| Gövde adımı | Açıklama | Hata yolu |
|---|---|---|
| Zaman damgası (t0) | Süre ölçüm başlangıcı | kayıt yoksa ölçüm iptal |
| Sinyal kontrolü | Kuyrukta iş var mı | yoksa kenar durum |
| İş kuyruğu işleme | Ana DSP işi | taşma sayacı |
| Süre ölçümü (t1) | Görev süresi | bütçe aşım bayrağı |

## §4 Kesme Servis Yolu

Kesme yolu kısa tutulur: olay kaydı, bekleyen kuyruğa işaretleme, çıkış.
Kuyruğun kendisini kesmede değil, sıradaki zaman diliminde işler.

| Adım | Kesmede | Zaman diliminde |
|---|---|---|
| Olay kaydı | evet | — |
| İş işaretleme | evet | — |
| DSP işleme | hayır | evet |
| Gözlem yazımı | hayır | evet |

## §5 Periyot ve Öncelik Tablosu

| Görev | Periyot | Öncelik | Gözlem |
|---|---|---|---|
| gorev1 (ana DSP) | 100 us | yüksek | görev süresi |
| gorev2 (gözlem) | 10 ms | orta | sayaç paketi |
| gorev3 (sağlık) | 100 ms | düşük | sağlık kuyruğu |

## §6 Gözlem ve Telemetri

| Sayaç | Kaynağı | Okuma yolu |
|---|---|---|
| Kesme oranı | IRQ sayaçları | radar ekranı |
| Görev süresi | kod içi ölçüm | test raporu |
| Gözlem paketi | polar tablo | radar ekranı |
| Sağlık kuyruğu | sağlık görevi | radar ekranı |

## §7 Sürüm Kontrolü

Çalışma zamanı sürümü, üretim derlemesinin ürettiği meta veri olarak
`update-surucu` sürüm kaydına iletilir; uygulama ağacı bu değeri yalnız okur.

| Alan | Tür | Açıklama |
|---|---|---|
| build_sayaci | metin | üretim sayaç değeri |
| kanal | metin | üretim kanalı |
| commit | metin | uygulama reposu SHA (bilgi) |

## §8 Örnek Test Adımları

1. Görev sürelerini ölç, bütçeyle karşılaştır.
2. Kesme oranını artır, kuyruk taşımını gözle.
3. Gözlem paketini radar ekranında doğrula.
4. Sürüm kaydının üretim derlemesinden geldiğini denetle.

## §9 Özet

Runtime sürücü, zamanlama ve kesme yüklerini tek bir gövde altında toplar;
gözlem ve sürüm kaydı `update-surucu` ile kesişir.

## §10 Görev Yaşam Döngüsü (derinleştirme eki — doktrin)

§2'deki görev tanımına bu bölüm yaşam döngüsü derinliği ekler. Her
görev beş durumdan geçer:

| # | Durum | Tanım | Geçiş tetikleyicisi |
|---|---|---|---|
| D0 | TANIMLI | yapılandırma-tablosunda kayıtlı, çalışmıyor | boot-anayasa §5 yükleme kapısı |
| D1 | HAZIR | yığın/tamponu tahsis edildi, ilk çalıştırma bekliyor | runtime-anayasa başlatma sırası |
| D2 | ÇALIŞIYOR | kesme veya zamanlayıcı tetiklemesiyle çalışıyor | zamanlayıcı/kesme olayı |
| D3 | BEKLİYOR | kaynak veya olay bekliyor (kilitli/uykulu) | beklenen olayın gelmesi |
| D4 | HATALI | bütçe aşımı, yığın taşımı veya kesme-kaybı tespit edildi | sağlık denetimi |

**Geçiş kuralları:**

1. D2→D3 yalnız sürücünün kendi bekleme ilkelermesiyle olur — görev
   kendini uyutamaz (uyutma yetkisi yalnız runtime sürücüsündedir);
2. D3→D2 geçişi beklenen olayla olur; olay kaybolursa görev D4'e
   düşer, sonsuz D3 beklemesi yasaktır;
3. D4 kurtarılması gereken bir durumdur: sağlık görevi (§6) D4
   görevi iki kez art arda görürse görev yeniden-D1'e alınır ve olayı
   tetikleyen kaynak kaydı düşülür; üçüncü kez görev kalıcı olarak
   D4'te kalır ve radar ekranına işaret düşer;
4. D0→D1 yalnız boot sırasında olur; çalışma-anında yeniden-D0 alma
   (hot-reload) bu sürümde tanımlı değildir — `⚠️ VERIFICATION REQUIRED`.

**İnvaryantlar:**

- I-10.1: bir görev aynı anda yalnız bir durumdadır (çift-durum yasak);
- I-10.2: D2 sırasında başka bir görev D2'ye giremez (tek-çalışan kuralı,
  §3'teki öncelik sırası bunun dışındadır — kesme D2'yi böler);
- I-10.3: her durum geçişi gözlem paketine (§6) yazılır; geçiş-kaybı
  sağlık kuyruğuna (§6) düşer.

## §11 Kesme Yönetimi Derinliği (derinleştirme eki — doktrin)

§4'teki kesme tablosuna bu bölüm iç yapı ekler:

### §11.1 Kesme öncelik sırası

| Sıra | Kesme kaynağı | Öncelik | Gerekçe |
|---|---|---|---|
| 1 | zamanlayıcı-kritik (ses yolu besleme) | en yüksek | bütçe aşımı doğrudan xrun üretir |
| 2 | DMA-tamamlama | yüksek | tampon taşımı veri kaybı üretir |
| 3 | cihaz-durum | orta | hot-splash/durum değişimi |
| 4 | sayaç-gözlem | düşük | gözlem eksikliği veri bozmaz |
| 5 | sağlık-uyarı | en düşük | yalnız kayıt, eylem yok |

**Öncelik kuralı:** beş sıra içinde artan numara = azalan öncelik;
eşit-öncelikli iki kesme aynı anda gelirse kaynak kaydı sıraya girer
(kaynak-sıralama §4.3'teki kuyruk kuralıyla aynıdır).

### §11.2 Kesme yığınları

Her kesme sınıfının kendi yığını vardır; yığınlar boot sırasında
baştan tahsis edilir (§12.2). Yığın boyutu sınıf bazında sabittir ve
çalışma-anında büyümez.

| Sınıf | Yığın sahibi | Büyüme | Taşım davranışı |
|---|---|---|---|
| ses-yolu | runtime sürücüsü | yasak | xrun + kayıt |
| DMA | runtime sürücüsü | yasak | veri-kayıp kaydı |
| cihaz-durum | runtime sürücüsü | yasak | olay kaybı kaydı |
| sayaç-gözlem | sağlık görevi | yasak | sayım kaybı kaydı |
| sağlık-uyarı | sağlık görevi | yasak | kayıt kaybı kaydı |

**Taşım kuralı:** yığın taşımı hiçbir durumda sessiz olmaz; her taşım
sağlık kuyruğuna (§6) düşer ve radar ekranında görünür.

### §11.3 Kesme-bütçesi

Toplam kesme süresi bir sonraki ses-yolu tetiklemesinden önce
bitmelidir; bütçe tarifi:

| Bileşen | Bütçeye etkisi | Ölçüm kaynağı |
|---|---|---|
| giriş-kaydı | sabit | kod boyutu (derleme-anı) |
| çözümleme | değişken | §15.1 ölçüm protokolü |
| kayıt-yazımı | sabit | kod boyutu |
| dönüş-yolu | sabit | kod boyutu |

**Bütçe kuralı:** toplam kesme süresi bütçeyi aşarsa D4 bayrağı
düşer; bütçe rakamı bu dosyaya yazılmaz — ölçümdür (§15), sayı
uydurulmaz.

## §12 Bellek Yönetimi (derinleştirme eki — doktrin)

### §12.1 Havuzlar

Çalışma zamanı belleği üç havuza bölünür; havuzlar boot sırasında
baştan tahsis edilir ve çalışma-anında yeniden-boyutlanmaz:

| Havuz | Sahip | Kullanım | Tahsis zamanı |
|---|---|---|---|
| görev-yığınları | runtime sürücüsü | §11.2 sınıfları | boot (§5) |
| tampon-havuzu | ses-yolu sürücüsü | ses tamponları | boot (§5) |
| gözlem-havuzu | sağlık görevi | gözlem paketleri (§6) | boot (§5) |

### §12.2 Tahsis kuralları

1. çalışma-anında `malloc` benzeri serbest-tahsis yasaktır — tüm
   tahsisler boot sırasında yapılır (sıfır-dönüşümlü çalışma kuralı);
2. havuzlar birbirinden bağımsızdır; bir havuz tükenirse diğerine
   ödünç verilmez (havuz-sızıntısı yasak);
3. havuz-tükenmesi D4 bayrağı düşürür ve sağlık kuyruğuna yazılır;
4. serbest-bırakma yalnız boot-anında olur (yeniden başlatma yoluyla).

### §12.3 Gözlem-havuzu döngüsel yazım

§6.4'teki döngüsel yazım bu bölümde detaylandırılır:

| Alan | Boyut | Yazım kaynağı | Okuma kaynağı |
|---|---|---|---|
| sayaç-bloğu | sabit | kesme-rutinleri | radar ekranı |
| sağlık-bloğu | sabit | sağlık görevi | radar ekranı |
| sürüm-bloğu | sabit | üretim derlemesi | radar ekranı |

**Döngü kuralı:** yazım başı-sona döner; okuma yazmaya yetişemezse en
eski paket düşer (paket-düşme sayacı gözlem paketinde görünür —
düşme-sessizliği yasak).

## §13 Watchdog ve Kurtarma (derinleştirme eki — doktrin)

### §13.1 Katmanlı watchdog

| Katman | Kapsam | Zaman aşımı davranışı | Kurtarma |
|---|---|---|---|
| görev-watchdog'u | her görev kendi bütçesi | D4 bayrağı | yeniden-D1 (§10 kural 3) |
| sürücü-watchdog'u | runtime sürücüsü bütçesi | sağlık kuyruğu | sürücü yeniden-başlatma (boot-anayasa §7) |
| sistem-watchdog'u | tüm çalışma-zamanı | donanım reset | cold-boot (boot-anayasa §8) |

### §13.2 Kurtarma sırası

Kurtarma en küçük katmandan başlar:

1. görev-watchdog'u tetiklenirse yalnız o görev yeniden-D1 alınır;
2. iki art arda görev-watchdog'u aynı görevde → sürücü-watchdog'u
   devreye girer, sürücü bütçesi yeniden ölçülür;
3. sürücü-watchdog'u tetiklenirse sistem-watchdog'u reset-işareti
   alır; reset-cause kaydı (boot-dogrulama §D 12) donanım kaydına
   yazılır;
4. reset sonrası boot-anayasa §8 kurtarma-yolu çalışır.

**Kurtarma kuralı:** hiçbir kurtarma katmanı sessiz değildir; her
kurtarma gözlem paketinde işaretlenir ve radar ekranında görünür.

## §14 Güç Durumları (derinleştirme eki — doktrin)

| Durum | Tanım | Ses yolu | Uyanma kaynağı |
|---|---|---|---|
| TAM-ÇALIŞMA | tüm görevler aktif | aktif | — |
| KISITLI | düşük-öncelik görevler durduruldu | aktif | süre-sayaçı |
| UYKU | yalnız cihaz-durum + sağlık aktif | durdurulmuş | cihaz-durum kesmesi |
| KAPALI | sıfır-güç | yok | fiziksel-kapama |

**Geçiş kapıları:**

1. TAM-ÇALIŞMA→KISITLI yalnız ses-yolu bütçesi aşılmadığında izinli
   (bütçe-aşımındayken kısıtlama yasak);
2. KISITLI→UYKU ses-yolu durdurulduktan sonra izinli (sessiz-geçiş
   kuralı);
3. UYKU→TAM-ÇALIŞMA uyanma kaynağıyla olur; uyanma sonrası §10 D1
   sırası yeniden uygulanır;
4. KAPALI yalnız fiziksel-kapama ile girilir; yazılı-kapama bu sürümde
   tanımlı değildir — `⚠️ VERIFICATION REQUIRED`.

## §15 Ölçüm Protokolleri (derinleştirme eki — kapı)

### §15.1 Görev süresi ölçümü

| Adım | İşlem | Kayıt |
|---|---|---|
| 1 | görev giriş damgası al | sayaç-bloğu |
| 2 | görev sonu damgası al | sayaç-bloğu |
| 3 | farkı hesapla, bütçeyle karşılaştır | sağlık-bloğu |
| 4 | aşım varsa D4 bayrağı | sağlık kuyruğu |

**Protokol kuralı:** ölçüm kendisi görev bütçesini aşarsa ölçüm
kaldırılır (ölçüm-bozulması yasak — ölçüm gözlemi bozmaz).

### §15.2 Kesme oranı ölçümü

| Adım | İşlem | Eşik |
|---|---|---|
| 1 | IRQ sayacını oku | — |
| 2 | periyot bekle | — |
| 3 | farkı hesapla | bütçe rakamı dosyada yok (`⚠️ VERIFICATION REQUIRED`) |
| 4 | eşiği aşan oranı sağlık kuyruğuna yaz | — |

### §15.3 Yığın-kullanımı ölçümü

| Adım | İşlem | Kayıt |
|---|---|---|
| 1 | boot sonrası yığın tepe-noktasını işaretle | görev-yığınları |
| 2 | çalışma sırasında tepe-noktasını güncelle | sağlık görevi |
| 3 | tepe/boyut oranını gözlem paketine yaz | radar ekranı |

## §16 Hata Sınıflandırması (derinleştirme eki — taksonomi)

| Sınıf | Tanım | Belirti | Sınıf sahibi |
|---|---|---|---|
| H1 bütçe-aşımı | görev süresi bütçeyi geçti | D4 bayrağı | runtime sürücüsü |
| H2 yığın-taşımı | kesme yığını doldu | sağlık kuyruğu | runtime sürücüsü |
| H3 havuz-tükenmesi | tahsis başarısız | D4 bayrağı | runtime sürücüsü |
| H4 kesme-kaybı | beklenen kesme gelmedi | sayaç-kaybı | runtime sürücüsü |
| H5 bekleme-kaybı | D3→D2 olayı gelmedi | D4 bayrağı | sağlık görevi |
| H6 watchdog-kurtarma | katmanlı kurtarma çalıştı | reset-cause kaydı | sistem-watchdog'u |

**Taksonomi kuralı:** altı sınıf da doktrin düzeyindedir — hangi
sürücüde·hangi sürümde görüldüğü bu dosyada sayılmaz; tespit §15
ölçüm kapılarının işidir. Sınıf etiketi yalnız kanıtla konur.

## §17 Entegrasyon Kapıları (derinleştirme eki — kapı tarifi)

| Kapı | Karşı taraf | Geçiş biçimi | Kapı durumu |
|---|---|---|---|
| boot-anayasa §5 | runtime başlatma sırası | görev yükleme tablosu | doktrin |
| update-surucu | sürüm kaydı (§7) | üretim meta verisi | doktrin |
| audio-control | ses-yolu besleme | zamanlayıcı-kritik kesme | doktrin |
| device-control | cihaz-durum kesmesi | durum olayı | doktrin |
| security | kurtarma-kaydı | reset-cause | doktrin |
| diagnostics | sağlık kuyruğu | gözlem paketi | doktrin |

**Kapı kuralı:** altı kapı da çift-taraflıdır; bir taraf revizyon
istersen diğer tarafın uyumu ayrı bir kapıdır — tek-taraflı kapı
açımı yasaktır.

## §18 Dürüstlük bloğu (derinleştirme eki 4)

Bu ek (§10-§17) kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt
değildir: 0 URL, 0 repo iddiası, 0 ölçüm sonucu, 0 sürüm sayısı
eklenmiştir; yalnız görev yaşam döngüsü, kesme yönetimi, bellek
havuzları, watchdog, güç durumları, ölçüm protokolleri, hata
taksonomisi ve entegrasyon kapıları doktrinleri genişletilmiştir.
§1-§9'daki mevcut iddialar, §7 sürüm tablosu ve §8 test adımları
aynen korunur; ölçüm kapıları §15'e bağlıdır ve bu ekle kapanmaz.
Dosyanın iddia·ölçüm sayıları aynen kalır.

## §19 Zaman Sayaçları ve Zaman Çizgisi (derinleştirme eki 5 — doktrin)

### §19.1 Sayaç sınıfları

| Sınıf | Çözünürlük | Sahip | Kullanım |
|---|---|---|---|
| monoton-sayaç | yükselme (tick) | runtime sürücüsü | görev süresi (§15.1) |
| kesme-zaman damgası | tick | kesme-rutinleri | kesme-bütçesi (§11.3) |
| ses-zaman damgası | örnekleme | ses-yolu sürücüsü | senkron (K003) |
| sağlık-periyodu | ms | sağlık görevi | watchdog (§13) |

**Sayaç kuralları:**
1. monoton-sayaç geriye saymaz; sıfırlama yalnız boot-anında olur;
2. tick→mikrosaniye dönüşümü sabit katsayı ile yapılır; katsayı
   derleme-anında dondurulur (çalışma-anında yeniden-okuma yasak);
3. ses-zaman damgası monoton-sayaçla karşılaştırılamaz — iki farklı
   zaman alanıdır; karşılaştırma gerekiyorsa köprü-sayaç (§19.2)
   kullanılır;
4. sayaç okuması kesme bağlamında serbesttir (salt-okuma).

### §19.2 Köprü-sayaç

İki zaman alanını birbirine bağlayan tek mekanizma köprü-sayaçtır:

| Adım | İşlem | Kayıt |
|---|---|---|
| 1 | ses-zaman damgası oku | ses-yolu sürücüsü |
| 2 | aynı anda monoton-sayaç oku | kesme-rutinleri |
| 3 | çifti gözlem paketine yaz | sağlık-bloğu |
| 4 | ikinci okumada farkı hesapla | sağlık görevi |

**Köprü kuralı:** köprü-sayaç kaybı (ikinci okumun gelmemesi) H4
sınıfına (§16) düşer; köprü olmadan iki alan arası çevrim yazılmaz —
çevrim uydurulmaz.

## §20 Yeniden-Başlatma Senaryoları (derinleştirme eki 5 — doktrin)

| Senaryo | Tetik | Kapsam | Kurtarma |
|---|---|---|---|
| görev-yeniden-D1 | sağlık görevi (§10 kural 3) | tek görev | §10 I-10.3 |
| sürücü-yeniden-başlatma | sürücü-watchdog'u (§13.1) | runtime sürücüsü | boot-anayasa §7 |
| tam-sıfırlama | sistem-watchdog'u (§13.1) | tüm çalışma-zamanı | boot-anayasa §8 |

**Senaryo kuralları:**
1. her yeniden-başlatma bir §16 sınıf etiketi taşır (sessiz yeniden-
   başlatma yasak);
2. sürücü-yeniden-başlatma sırasında ses-yolu durdurulur; yeniden-
   başlatma bitince §10 D1 sırası uygulanır ve ses-yolu yeniden-
   beslenir;
3. tam-sıfırlama sonrası reset-cause kaydı (§13.2 adım 3) boot
   kaydına iletilir; kayıt yoksa kurtarma-ağacı bozulmuş sayılır ve
   boot-anayasa §8 bu durumu kendi hata sınıfına alır;
4. üç senaryonun da gözlem-paketi izi vardır — izsiz yeniden-
   başlatma bozulma sayılır.

## §21 Kapı Özeti ve Kapanış Koşulları (derinleştirme eki 5 — kapı)

Bu bölüm §10-§20 eklerinin kapılarını tek tabloda toplar:

| Kapı | Bölüm | Kapanış biçimi | Kapı durumu |
|---|---|---|---|
| görev süresi bütçesi | §15.1 | donanım ölçümü | AÇIK |
| kesme oranı bütçesi | §15.2 | donanım ölçümü | AÇIK |
| yığın tepe-noktası | §15.3 | boot+çalışma ölçümü | AÇIK |
| köprü-sayaç doğruluğu | §19.2 | çift-alan ölçümü | AÇIK |
| güç geçiş kapıları | §14 | cihaz-testi | AÇIK |
| kurtarma izi | §20 | watchdog tetikleme testi | AÇIK |

**Kapanış kuralı:** altı kapı da AÇIK'tır — bu ek hiçbir kapıyı
kapatmaz, yalnız tarif eder. Kapıların kapanışı §15 ölçüm
protokollerinin donanım üzerinde çalıştırılmasıyla olur; ölçüm
sonuçları bu dosyaya değil, boot-dogrulama.md §G/§H sayım
bölümlerine yazılır (iki-dosya kuralı: tarif burada, ölçüm orada).

### §21.1 Dürüstlük bloğu (derinleştirme eki 5)

Bu ek (§19-§21) kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt
değildir: 0 URL, 0 repo iddiası, 0 ölçüm sonucu, 0 sürüm sayısı
eklenmiştir; yalnız sayaç doktrini, köprü-sayaç, yeniden-başlatma
senaryoları ve kapı özeti tarif edilmiştir. §18 dürüstlük kaydı ve
§1-§9 mevcut iddiaları aynen korunur.

## §22 Gözlem Paketi Şeması (derinleştirme eki 5 — tarif)

§6'daki gözlem paketi bu bölümde alan-bazında tarif edilir; şema
tariftir, üretim kodu değildir:

### §22.1 Alan tablosu

| Alan | Tür | Yazan | Okuyan | Güncellenme |
|---|---|---|---|---|
| paket-sayaci | tam sayı | sağlık görevi | radar ekranı | her sağlık döngüsü |
| gorev-durumlari | tablo (görev başına satır) | runtime sürücüsü | radar ekranı | her durum geçişi (§10 I-10.3) |
| kesme-oranlari | tablo (sınıf başına satır) | kesme-rutinleri | radar ekranı | her kesme |
| yigin-tepe | tablo (sınıf başına satır) | kesme-rutinleri | radar ekranı | tepe aşıldığında |
| hata-sayacilari | tablo (§16 sınıf başına) | sağlık görevi | radar ekranı | her H1-H6 |
| kurtarma-izi | kayıt listesi | watchdog katmanları | radar ekranı | her kurtarma (§20) |
| surum-bloğu | üç alan (§7) | üretim derlemesi | radar ekranı | boot anında |

### §22.2 Yazım-yarışı kuralı

İki yazan aynı alanı aynı anda güncelleyemez:

1. alan-tablosunda her alanın TEK yazanı vardır (§6.4 sahiplik kuralının
   devamı);
2. ortak-yazan durumu (örn. hata-sayacilari) olduğunda yazım
   kilit-dışı-artırmayla (atomic increment) yapılır — kilit kullanımı
   kesme bağlamında yasak (§11'deki blok-yasak doktrini);
3. okuma-yazma yarışında okuyucu en eski tutarlı paketi okur —
   yarı-paket okuması yasaktır (paket-tamlık kuralı).

### §22.3 Radar ekranı ile sözleşmesi

Radar ekranı (K005 runtime-ornekler §9) bu paketi yalnız okur:

| Radar alanı | Paket alanı | Uyumsuzluk davranışı |
|---|---|---|
| sayaçlar | paket-sayaci + kesme-oranlari | alan yoksa radar boş gösterir, uydurmaz |
| sağlık | hata-sayacilari + kurtarma-izi | alan yoksa "veri yok" yazar |
| görev panosu | gorev-durumlari | satır eksikse o görev görünmez |

**Sözleşme kuralı:** radar eksik veriyi tahmin etmez, boş gösterir —
gözlem ekranının uydurması gözlemi bozar (gözlem-bozulması yasak,
§15.1 ölçüm-bozulması kuralının gözlem yüzü).

### §22.4 Dürüstlük bloğu (derinleştirme eki 5b)

Bu ek (§22) kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt
değildir: 0 URL, 0 repo iddiası, 0 ölçüm sonucu eklenmiştir; yalnız
gözlem paketi şeması, yazım-yarışı kuralı ve radar-sözleşmesi tarif
edilmiştir. §6-§7 mevcut tabloları ve §21 kapı özeti aynen korunur.
§22.1'deki alan tablosu ile §6'daki mevcut tablo çelişirse §6
kazanır — bu ek §6'yı ezmez, yalnız alan-bazında derinleştirir.
Ölçüm kapıları §15'e bağlıdır ve bu ekle kapanmaz.
§22.3'teki radar-sözleşmesi K005 runtime-ornekler §9 ile çift-taraflıdır:
radar değişirse bu şema revize edilir, şema değişirse radar revize
edilir — tek-taraflı şema değişikliği yasaktır (§17 entegrasyon-kapı
kuralının gözlem yüzü).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
