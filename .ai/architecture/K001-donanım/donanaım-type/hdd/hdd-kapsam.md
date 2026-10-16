---
node_id: hdd-kapsam
node_type: module
status: draft
parent_id: K001-donanım/donanaım-type/hdd
children: []
depends_on:
  - K000-isletim-sistemi
  - K001-donanım/donanaım-type
evidence:
  - "K001-donanım/donanaım-type/hdd/index.md (47 satır, 2026-10-12)"
  - "K001-donanım/donanaım-type/donanaım-type-kapsam.md §2/A2 — hdd envanteri UNKNOWN"
  - "https://www.westerndigital.com/support (HTTP 200, 2026-10-13)"
  - "https://www.seagate.com/support/ (HTTP 200, 2026-10-13)"
validation: "draft — 2026-10-13 yazıldı · ≥500 satır ölçümü yazım sonrası · envanter kanıtı YOK (K001 Q4)"
open_questions:
  - "Q-HK1: Kullanılan disk envanteri (marka/model/kapasite) bilinmiyor — UNKNOWN"
  - "Q-HK2: Kütüphanenin barındırıldığı fiziksel konum (hoparlöre uzaklık, raf) bilinmiyor — UNKNOWN"
  - "Q-HK3: RAID/backup tercihi henüz karara bağlanmadı — ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# hdd-kapsam — Mekanik Disk Kapsamı (Müzik Kütüphanesi Rolü)

> **Yazım notu:** `donanaım-type` yazımı v2 kaynağına sadıktır — düzeltme yasak
> (v2 §3 KURAL 1). Bu dosya `hdd/` alt-konunun **kapsam** belgesidir: mekanik
> diskin mimari rolü, fiziksel yapısı, ses sistemine etkisi ve yedekleme sınırı.

## §1 ELI15

Mekanik disk (HDD), veriyi **dönen manyetik plaka** üzerine yazar ve okur. Bu
basit fizik, iki sonucu doğurur: (1) **kapasite ucuzdur** — aynı paraya katı
diskten çok daha fazla bayt sığar; (2) **erişim yavaştır ve seslidir** — okuma
kafası fiziksel olarak yer değiştirmek zorundadır ve plaka döner. Müzik
kütüphanesi bu iki özelliği aynı anda ister: on binlerce dosya (çoğu seyrek
erişilir) ama toplam hacim büyüktür. Bu dosya, mekanik diskin CoreMusic
depolama katmanındaki **rolünü ve sınırını** çizer.

> ⚠️ VERIFICATION REQUIRED: Bu alt-konunun tanımı v2 §2.2'deki **adından**
> türetilmiştir; disk envanteri (marka/model/kapasite) elde edilmemiştir
> (K001 Q4 → `donanaım-type-kapsam.md` §6 Q-T1). Bu dosyadaki **sayısal
> performerans değerleri** ölçüm veya datasheet ile kanıtlanmadıkça
> `⚠️ VERIFICATION REQUIRED` işaretlidir; uydurma rakam yazılmaz.

## §2 Kapsam — İÇİNDE NE var

| # | Kapsam İÇİ | Durum |
|---|---|---|
| A1 | Mekanik disk fiziksel yapısı (plakalı, kafa, sektör) | ✅ genel mühendislik bilgisi + hesap |
| A2 | Erişim süresi bileşenleri (seek + dönüş + transfer) | ✅ aritmetik ile türetilmiş (§4) |
| A3 | Diskin müzik kütüphanesindeki rolü (soğuk/sıcak katman) | ✅ mimari tanım |
| A4 | Mekanik gürültü ve vibrasyonun ses sistemine etkisi | ✅ tasarım kuralı; ölçümsel değer ⚠️ |
| A5 | RAID ve yedekleme stratejisi (kategori olarak) | ⚠️ karar bekliyor (Q-HK3) |
| A6 | Kapasite/performans matrisi (kategori kademeleri) | ✅ kavramsal; sayısal eşikler ⚠️ |
| A7 | SATA arayüz notları (protokol, NCQ, 512e/4Kn) | ✅ protokol bilgisi |
| A8 | Depolama → DAC zinciri tarifi (sınır anlatımı) | ✅ yalnız referans, sahiplik değil |

## §3 Kapsam — DIŞINDA NE var (sınır komşuları)

| # | Dışında | Nereye gider | Dayanak |
|---|---|---|---|
| B1 | Dosya sistemi / VFS / mount / fsync davranışı | K000-isletim-sistemi | `isletim-sistemi-core-kapsam.md` §A.2 (A3 satırı) · §A.3 |
| B2 | Diski süren sürücü ve kesme/DMA katmanı | K000-isletim-sistemi (kernel) · donanım-kernel | `donanım-anayasa.md` §3 satır 47 |
| B3 | Donanım envanteri (hangi disk takılı) | K001-donanım (envanter) · K001 Q4 | `donanaım-type-kapsam.md` §6 Q-T1 |
| B4 | Ses sinyalinin üretilmesi | K003-audio | `donanım-anayasa.md` §2 |
| B5 | DAC/codec/amfi çipleri | `[[ak4458-dac]]` · `[[pcm3168a-codec]]` · `[[tpa3255-amplifier]]` | `audio/cipler/*` |
| B6 | Çıkış yolu ve ASIO/WASAPI tampon davranışı | K003-audio | `[[output-kapsam]]` |
| B7 | Depolama cihazının firmware'i (SMART yazımı, init) | K005-firmware | `donanım-anayasa.md` §2 |
| B8 | Ağ üzerinden depolama (NAS/HTTP akış) | K014-network · K015-media | komşu K düğümleri |

## §4 Mekanik Yapı

### §4.1 Plaka — kafa — sektör

| # | Öğe | Tanım | Depolamadaki karşılığı |
|---|---|---|---|
| 1 | Plaka (platter) | Manyetik kaplı dönen disk | Kapasitenin fiziksel taşıyıcısı |
| 2 | Okuma/yazma kafası (head) | Plaka yüzeyine ~uçucu mesafede duran başlık | Veriye erişimin tek kapısı |
| 3 | Kafa kolu (actuator arm) | Kafayı yarıçap üzerinde hareket ettiren kol | "Seek" hareketini yapan mekanizma |
| 4 | İz (track) | Plaka üzerinde eş merkezli halka | Verinin manyetik dizilimi |
| 5 | Sektör (sector) | İzin en küçük adreslenebilim birimi | Okuma/yazma birimi |
| 6 | Silindir (cylinder) | Aynı yarıçap üzerindeki izlerin kümesi | Eski geometri kavramı (LBA ile örtük) |
| 7 | Küme (cluster) | Dosya sisteminin ayırma birimi | Sektörden büyük; küçük dosyada israf |
| 8 | Plaka başına kafa | Her yüzey için ayrı başlık | Kapasite = yüzey × iz yoğunluğu × sektör |

### §4.2 Sektör büyüklüğü — 512e ve 4Kn

| # | Kavram | Anlamı | Neden önemli |
|---|---|---|---|
| 1 | 512n | Doğal 512 bayt sektör | Eski uyumluluk |
| 2 | 4Kn | Doğal 4096 bayt (4 KiB) sektör | Daha az başlık/yetki alanı, daha iyi bütünlük |
| 3 | 512e | 4Kn diskin 512 bayt olarak **emüle** edilmesi | Uyumluluk; yazma→silme→yeniden yazma gecikmesi |
| 4 | Dosya sistemi kümelenmesi | Genellikle 4 KiB katları | Hizalama bozulursa okuma-yazma yazma yönüne döner |
| 5 | Bölme hizalama (partition alignment) | 4 KiB katına hizalı başlangıç | Hizasız bölme, yazma yönlü çoğalmaya (§5) yol açar |

**Aritmetik not (türetilmiş, ölçüm değil):** 4096 ÷ 512 = 8 — yani 4Kn bir
sektör, eski gözle 8 küçük sektöre eşittir. Bu oran yalnızca sayım
ilişkisidir; hangi diskin hangi sektörle geldiği **envanter sorusudur**
(Q-HK1, UNKNOWN).

### §4.3 Kayıt yöntemi kademeleri (CMR / SMR)

| # | Yöntem | İzin yazma düzeni | Yazma davranışı | Kütüphane için anlamı |
|---|---|---|---|---|
| 1 | CMR (conventional) | İzlere hiçbiri örtüşmez | Doğrudan yazma | Kütüphane/scratch diskinde öngörülebilir |
| 2 | SMR (shingled) | İzlere bindirmeli (araları tırtıklı) | Yer için önce arkadaki şeridin silinip yeniden yazılması | Büyük **artık yazma** (write amplification); parça parça yazmada yavaşlar |
| 3 | ZBR (zone bit recording) | Dış izlerde daha çok sektör | Dış bölge daha hızlı | Sıra-dışı hız diskin yüzeyinde değişir |
| 4 | Sabit dönem başına alan | Dönüş başına sabit bit | İç izler daha yavaş | İç/dış bölge farkı ölçümde görünür |

**Kural:** Müzik kütüphanesi (büyük, çoğunlukla **artış** yazımları — yeni
albüm ekleme) SMR'de dahi tolere edilebilir; ancak **parça parça çok sayıda
küçük yazım** (indirme bit-parçası, log, indeks tekrar-tekrar) SMR'de maliyeti
katlar. Disk satın alma kararı bu ayrımı içerir (envanter UNKNOWN).

## §5 Erişim Süresi (aritmetik ile türetilmiş)

### §5.1 Süre bileşenleri

| # | Bileşen | Ne bekler | Bağımlı olduğu |
|---|---|---|---|
| 1 | Seek (konumlama) süresi | Kafanın izden izle gitmesi | Kol mekaniği, plaka yarıçapı, hedef uzaklığı |
| 2 | Dönüş gecikmesi (rotational latency) | İstenen sektörün kafanın altına gelmesi | Devir/dakika (RPM) |
| 3 | Transfer süresi | Sektörlerin okunması/arayüzden geçmesi | Yüzey yoğunluğu, arayüz hızı |
| 4 | Komut kuyruğu etkisi (NCQ) | Sıradaki komutun beklemesi | İşletim sistemi + sürücü davranışı |

### §5.2 Dönüş gecikmesi hesabı (geometrik — ölçüm değil)

Formül: `dönüş süresi (ms) = 60.000 ÷ RPM`.

| # | RPM | Dönüş süresi (ms/devir) | Ortalama bekleme (½ devir, ms) | Not |
|---|---|---|---|---|
| 1 | 5400 | 60000 ÷ 5400 = **11,11** | ≈ **5,56** | Düşük güç/gürültü sınıfı |
| 2 | 5900 | 60000 ÷ 5900 = **10,17** | ≈ **5,08** | Sık karşılanan ara değer ⚠️ envanter yok |
| 3 | 7200 | 60000 ÷ 7200 = **8,33** | ≈ **4,17** | Performans sınıfı |
| 4 | 10000 | 60000 ÷ 10000 = **6,00** | ≈ **3,00** | Kurumsal sınıf ⚠️ envanter yok |
| 5 | 15000 | 60000 ÷ 15000 = **4,00** | ≈ **2,00** | Yüksek sınıf ⚠️ envanter yok |

**Uyarı:** Bu tablo **sadece geometrik dönüş bekleme**sidir — seek ve transfer
süresi **içermez**. Gerçek ortalama erişim süresi = seek + ½ dönüş + transfer
olup seek değeri **model-özel datasheet** gerektirir → ⚠️ VERIFICATION REQUIRED
(Q-HK1 çözülmeden sayı yazılmaz).

### §5.3 Kare süresi (ses penceresi karşılığı)

| # | Hesap | Sonuç | Yorum |
|---|---|---|---|
| 1 | 512 örnek ÷ 48000 Hz | = **10,67 ms** | Neva Engine tamponu bir kare sürer (sartname) |
| 2 | 48000 Hz'de 1 örnek | = **0,0208 ms** | Örnekleme aralığı |
| 3 | 10 ms'lik disk gecikmesi kaç örnek? | 0,01 × 48000 = **480 örnek** | Tek bir yavaş okuma, tamponun **%94'ünü** doldurabilir |
| 4 | Sonuç | — | Canlı çalma yolundaki **tek** kritik kural: veri **önceden** okunmalı (§6) |

**Bu hesap bu dosyanın en önemli mimari sonucudur:** tek bir 10 ms'lik
mekanik gecikme, 512 örneklik tamponun neredeyse tamamını tüketir. Bu yüzden
kütüphane okuması **gerçek zamanlı yolun dışında** tutulur.

## §6 Müzik Kütüphanesinde Rol

### §6.1 Bant genişliği ihtiyacı (türetilmiş)

| # | Akış | Hesap | Gereken ham hız |
|---|---|---|---|
| 1 | 48 kHz / 16-bit stereo PCM | 48000 × 2 B × 2 = 192.000 B/s | ≈ 0,18 MiB/s |
| 2 | 48 kHz / 24-bit stereo PCM | 48000 × 3 B × 2 = 288.000 B/s | ≈ 0,27 MiB/s |
| 3 | 96 kHz / 24-bit stereo PCM | 96000 × 3 B × 2 = 576.000 B/s | ≈ 0,55 MiB/s |
| 4 | 192 kHz / 24-bit stereo PCM | 192000 × 3 B × 2 = 1.152.000 B/s | ≈ 1,10 MiB/s |
| 5 | 8 kanal (çoklu çıkış) 48 kHz/24-bit | 48000 × 3 B × 8 = 1.152.000 B/s | ≈ 1,10 MiB/s |
| 6 | 1000 dosyalık kütüphanenin tamamını **okuma** | toplam ÷ süre | Sabit diskte **dakikalar** sürebilir |

**Yorum:** tek akış için gerekken ham hız, mekanik diskin sıradışı hızından
**çok** düşüktür — yani sorun *bant genişliği* değil, **gecikme sürekliliğidir**
(§5.3). Aynı anda birden çok akış (çoklu oda / kuyruk örnekleme) bu tabloyu
doğrusal olarak büyütür → disk **önbellek dışı** bırakılmalı.

### §6.2 Erişim profilleri

| # | Profil | Örnek iş | Disk için anlamı | Uygun katman |
|---|---|---|---|---|
| 1 | Sıralı okuma (sequential) | Albümün baştan sona çalınması | İdeal HDD işi — NCQ ve öngörü yardım eder | HDD ✅ |
| 2 | Rastgele okuma (random) | Karışık modda parça atlama | Seek baskısı — her atlamada mekanik gecikme | HDD ⚠️ / SSD ✅ |
| 3 | Sıralı yazma | Yeni albümün kopyalanması | SMR'da önce yeniden yazma maliyeti | HDD (CMR tercih) |
| 4 | Rastgele yazma | İndirme parçaları, indeks, log | SMR'da en pahalı iş | SSD ✅ |
| 5 | Meta veri okuma | Künye/etiket/artwork taraması | Binlerce küçük okuma = seyrek erişim | SSD ✅ |
| 6 | Tam tarama (full scan) | Kütüphane doğrulama / hash | Saatler sürebilir → planlanmış iş | HDD ✅ (boşta) |

### §6.3 Katman önerisi (kapsam kararı — envanter yok)

| # | Katman | İçerik | Dayanıklılık beklentisi |
|---|---|---|---|
| 1 | Sıcak katman | Çalışma seti, son çalınan albümler, indeks | SSD (§ ssd-kapsam) |
| 2 | Ana kütüphane | Tüm koleksiyon (tek kopya **yetmez**) | HDD — §7 RAID + §8 yedek |
| 3 | Arşiv/yedek | Değişmeyen kayıt kopyaları | Ayrı fiziksel disk / harici |
| 4 | Scratch (geçici) | Dönüşüm, yeniden kodlama, render | Ayrı disk — ses çalarken **çakışmasın** |

## §7 RAID ve Yedekleme Stratejisi

### §7.1 RAID kademesi karşılaştırması

| # | Kademe | Min. disk | Veri koruması | Yazma hızı | Okuma hızı | Kütüphane yorumu |
|---|---|---|---|---|---|---|
| 1 | JBOD/disk span | 1 | **yok** | tek disk | tek disk | Toplama, koruma yok |
| 2 | RAID 0 | 2 | **yok** (bir diskin ölümü = her şey) | yüksek | yüksek | Arşiv için **yasak** |
| 3 | RAID 1 | 2 | 1 diskin ölümü | ayna | ayna (paralel okuma yararı sınırlı) | Küçük kütüphane için basit |
| 4 | RAID 5 | 3 | 1 diskin ölümü (parite) | parite yazma cezası | paralel okuma | Büyük hacimde yeniden kurulum uzun |
| 5 | RAID 6 | 4 | 2 diskin ölümü | daha ağır parite | paralel okuma | Çok diskte daha güvenli |
| 6 | RAID 10 | 4 | ayna + şerit | hızlı | hızlı | Hız + koruma; kapasite yarı |
| 7 | Yazılım RAID (ör. işletim sistemi düzeyi) | değişken | donanım kontrolcüsüne bağımlı değil | CPU yükü | — | Kurulum/bakım maliyeti |

**Kritik mimari uyarı:** RAID **yedek değildir**. RAID 1, aynı **ana makinede**
iki fiziksel arızaya (aynı ana karta bağlı elektrik olayı, kötü yazılım, kasıtlı
silme) karşı korumaz. Kural: **RAID = çalışma sürekliliği · Yedek = veri
kurtarma.**

### §7.2 3-2-1 yedek kuralı

| # | Öğe | Adet | Açıklama |
|---|---|---|---|
| 1 | Veri kopyası | 3 | Çalışan kopya + iki yedek |
| 2 | Farklı ortam | 2 | Örn. yerel disk + harici disk |
| 3 | Fiziksel olarak uzakta | 1 | Ev dışı / yangın-su riski ayrı |
| 4 | Yedekleme biçimi | — | Tam + artımlı; değişmeyen albümler tek sefer tam |
| 5 | Kurtarma testi | zorunlu | Test edilmemiş yedek = yedek değildir (`hdd-dogrulama.md` §5) |

### §7.3 Yeniden kurulum (rebuild) riski

| # | Risk | Açıklama | Azaltma |
|---|---|---|---|
| 1 | Yeniden kurulum sırasında ikinci arıza | Büyük diskte yeniden kurulum uzun sürer | RAID 6 / RAID 10 · küçük veri birimi |
| 2 | Disk boyutu büyüdükçe süre artar | Aynı kademe, daha büyük yüzey → daha uzun kopya | Düzenli bad-sector taraması (`hdd-dogrulama.md` §3) |
| 3 | Yazılım katmanı bozulması | Parite tutarsızlığı sessiz yayılabilir | Periyodik parite doğrulama (⚠️ araç seçimi UNKNOWN) |
| 4 | Yedekleme aynı disk üzerinde | Fiziksel aynı disk = yedek değil | Farklı fiziksel ortam zorunlu (§7.2/2) |

## §8 Kapasite / Performans Matrisi

| # | Kademe | Kapasite eğilimi | Erişim davranışı | Gürültü | Ses sisteminde rolü |
|---|---|---|---|---|---|
| 1 | Masaüstü 3.5" (7200 sınıfı) | yüksek | orta | belirgin | Ana kütüphane (yalıtımlı raf) |
| 2 | Düşük devir 3.5"/2.5" | yüksek | orta-düşük | daha sessiz | Sessiz ortam tercihi |
| 3 | Dizüstü 2.5" | orta | orta | düşük | Taşınabilir yedek |
| 4 | Harici USB kasası | diske bağlı | arayüz ek gecikme | kasa+disk | Taşıma/ikincil yedek |
| 5 | NAS sınıfı (24/7) | yüksek | sürekli çalışma | sürekli | Ağ katmanı (K014 sınırı) |
| 6 | Kurumsal 10k/15k | düşük-orta | düşük seek | yüksek dönme sesi | Ses odasında genelde uygun değil |

**Seçim kuralı (kapsam düzeyi):** kapasite arttıkça **erişim süresi ve gürültü
fırsat maliyeti** artar; ses odasında disk seçimi yalnız gigabayt ile yapılmaz
(§9).

## §9 Mekanik Gürültü ve Vibrasyon (ses sistemleri için kritik)

### §9.1 Gürültü kaynakları

| # | Kaynak | Mekanizma | Duyulan karakter |
|---|---|---|---|
| 1 | Dönüş (spindle) | Hava sürtünmesi + yataklama | Sürekli uğultu |
| 2 | Seek (kafa hareketi) | Kol ivmesi, dur-kalk | Düzensiz tıkırtı/çıtırtı |
| 3 | Yazma sıçraması | Sektör yazımında kafa ayarı | İnce tıklamalar |
| 4 | Kasa rezonansı | Disk ıslak montajı olmayan metal panel | Metalik vızıltı |
| 5 | Kablo iletimi | Sert kablo → raf/duvar köprüsü | Uzak noktada duyulur titreşim |
| 6 | Faili hal | Kart üstü mekanik darbe | Ani tık |

> ⚠️ VERIFICATION REQUIRED: Belirli bir diskin dB(A) değeri **üretici
> datasheet'i** ile kanıtlanmalıdır; bu dosyada **sayısal gürültü değeri
> yazılmamıştır** (envanter yok, Q-HK1).

### §9.2 Anti-vibrasyon montaj kuralları

| # | Kural | Neden | Uygulama |
|---|---|---|---|
| 1 | Kauçuk/silikon ıslak montaj | Titreşimi yapıya geçirmeden tutmak | Yumuşak halka + kendi ağırlığına oturan yatak |
| 2 | Diski hoparlör kabinetinden **uzak** tutmak | Kabin gövdesi titreşimi ses olarak yayar | Ayrı raf / ayrı yapı |
| 3 | Kesintisiz teması kesmek | Sert vidalı bağlantı ses köprüsüdür | Yumuşak ara parça, aşırı sıkmama |
| 4 | Diski monitör/derin bas **yanına** koymamak | Dış kaynaklı ivme kafayı zorlar (seek kaybı) | Ayrı yüzey |
| 5 | Ayak/ped ile yapısal ayrım | Zemin/vitrin titreşimini ayırır | Çift aşamalı yalıtım |
| 6 | Aynı rafa birden fazla disk koymamak (mümkünse) | Diskler birbirini uyarır (çapraz titreşim) | Fiziksel ayrım / farklı zamanlama ⚠️ araç YOK |
| 7 | Kablo gevşetilir, kıvrılmaz | Sert kablo titreşim iletir | Döngü/serbest bırakma |
| 8 | Çalarken yazma azaltılır | Seek sesini düşürür | Kütüphane taraması boş saatlere alınır |

**Ses sistemi bağlantısı (neden bu madde bu dosyada):** ölçüm kapılarındaki
SNR/THD+N eşikleri (bkz. `donanım-anayasa.md` §10/3) yalnızca **elektriksel**
gürültüyü ölçer; disk kaynaklı **mekanik** gürültü, dinleme ortamının zemin
gürültüsünü yükseltir ve algılanan detayı düşürür. Bu, depolama seçiminin bir
**ses kararı** olması demektir.

### §9.3 Gürültü azaltma stratejisi sırası

| # | Adım | Maliyet | Etki |
|---|---|---|---|
| 1 | Diski oda dışında / yalıtımlı kabinde tutma | orta | en yüksek |
| 2 | ıslak montaj + yumuşak ayak | düşük | yüksek |
| 3 | Boş saatlere ağır I/O (tarama, yedek, indeks) | düşük | yüksek |
| 4 | Düşük devirli veya sessiz sınıf disk seçimi | orta | orta |
| 5 | Sıcak veriyi SSD'ye alıp HDD'yi salt arşiv yapma | orta-yüksek | çok yüksek |
| 6 | Yazmayı ayrı diske ayırma (kütüphane salt okunur) | — | seek sesini azaltır |
| 7 | RAID/yedek işini dinleme dışında planlama | düşük | orta |

## §10 SATA Arayüz Notları

| # | Konu | Ne | Depolama etkisi |
|---|---|---|---|
| 1 | SATA 6 Gb/s | Üretim üst sınırı ≈ 600 MB/s (6×10⁹ bit/s ÷ 8 = 750 MB/s ham; 8b/10b kodlama ~%20 kayıp → ≈600 MB/s) | Arayüz diskin **önündeki** tavan; disk bu değere **ulaşamaz** |
| 2 | Pratik sıra-dışı hız | Mekanik diskte plaka yoğunluğuyla sınırlı | ⚠️ ölçüm gerekir (`hdd-dogrulama.md` §4) |
| 3 | NCQ (Native Command Queuing) | Sürücünün komutları sıraya alıp optimize etmesi | Sıralı/örtüşen işlerde seek sayısını azaltır |
| 4 | NCQ kuyruğu derinliği | ATA NCQ kuyruğu **32** komutu destekler | Rastgele okumada bile sıralama imkânı |
| 5 | Uyku/kapanma (APM/PARK) | Disk kafası park edip uykuya geçebilir | Uyanma gecikmesi canlı okumada **kesinti** yapar → ses yolunda kapatılabilir ⚠️ araç/sürücü bağımlı |
| 6 | Hata yönetimi (time-limited error recovery) | Uzun kurtarma, kontrolcüyü bekletebilir | RAID'de disk "kaldırılma" riski — üretici düzeyi ⚠️ |
| 7 | Uçuş (hot-plug) | Bazı kasalarda takılabilir | Yedek disk döngüsü için pratik |
| 8 | USB/SATA köprüsü | Harici kasada çevirmeci çip | Ek gecikme + UASP/kuyruk davranışı farkı ⚠️ |
| 9 | Bağlantı bütünlüğü | Kötü kablo/ucuz güç → veri bozulması | Bütünlük kontrolü hataları tarama ile aranır |

## §11 Depolama → DAC Zinciri (sınır tarifi)

> Bu bölüm **sahiplik değildir** — yalnızca komşu düğümlere giden okunur tariftir.

| # | Aşama | Sahip düğüm | Not |
|---|---|---|---|
| 1 | Ham bayt (dosya) | **hdd** (bu konu) | Sektör/kümelenme §4.2 |
| 2 | Dosya sistemi, tampon, fsync | K000-isletim-sistemi | `isletim-sistemi-core-kapsam.md` §A.2 (A3 satırı) |
| 3 | Uygulama okuması + tampon doldurma | K015-media / K003-audio | §5.3 — 512 örnek = 10,67 ms |
| 4 | DSP/zaman alanı | K003-audio engine · K004-dsp | 32-bit float (ADR-017) |
| 5 | ASIO/WASAPI ile çıkış kuyruğu | K003-audio backend | `[[output-kapsam]]` · 512 örnek @ 48 kHz |
| 6 | I2S/TDM ile dijital ses | K001 `[[pcm3168a-codec]]` · `[[ak4458-dac]]` | **Yalnız PCM3168A** (ADR-038 H001); PCM5122 **yasak** |
| 7 | Analog çıkış → amplifikatör | K001 `[[tpa3255-amplifier]]` (karar girdisi) | **Class AB sabit · Class D yasak** (kök CLAUDE.md sabiti) |
| 8 | Hoparlör → kulak | fiziksel | §9 — disk titreşimi bu noktaya ulaşmamalı |

**Zincirin depolama dersi:** disk yalnızca 1. aşamadadır; 4–7. aşamaların
gerçek zamanlılığı **diske bağımlı değildir** — diskin görevi veriyi
**zamanından önce** 3. aşamaya teslim etmektir.

## §12 Riskler

| # | Risk | Etki | Olasılık | Azaltma |
|---|---|---|---|---|
| 1 | Tek disk arızası → tüm kütüphane kaybı | kritik | orta | 3-2-1 (§7.2) · RAID (§7.1) |
| 2 | RAID'yi yedek sanmak | kritik | orta | §7.1 uyarı kutusu |
| 3 | SMR diskin rastgele yazmada yavaşlaması | orta | orta | CMR tercihi / yazmayı SSD'ye taşıma |
| 4 | Disk titreşiminin ses odasına yansıması | orta | yüksek | §9.2 montaj kuralları |
| 5 | Park/uyku gecikmesinin canlı okumayı kesmesi | yüksek | orta | Güç yönetiminde disk uyusunu kapat (⚠️ araç bağımlı) |
| 6 | Hatalı sektörün sessiz yayılması | yüksek | düşük | SMART + tarama (`hdd-dogrulama.md` §2-§3) |
| 7 | Tek noktada yazma (indeks+log+kütüphane aynı disk) | orta | yüksek | Katman ayrımı (§6.3) |
| 8 | Test edilmemiş yedek | kritik | yüksek | Geri kurtarma tatbikatı (`hdd-dogrulama.md` §5) |
| 9 | Envanter bilinmeden karar vermek | yüksek | **aktif** | Q-HK1 (UNKNOWN) kapatılmadan satın alma yapılmaz |

## §13 Uygulama Sırası (checklist)

| # | Adım | Çıktı | Kapı |
|---|---|---|---|
| 1 | Envanteri çıkar (marka/model/kapasite/yaş) | disk listesi | Q-HK1 |
| 2 | Erişim profilini yaz (§6.2 hangi satırlar baskın?) | profil tablosu | kapsam onayı |
| 3 | Katmanı seç (sıcak/anı / arşiv / scratch) | katman şeması | §6.3 |
| 4 | RAID kararını yaz (var mı, hangi kademe) | karar kaydı | Q-HK3 |
| 5 | 3-2-1 yedek çizelgesi | kopya/ortam/uzaklık | §7.2 |
| 6 | Montaj ve konum (titreşim) | yerleşim notu | §9.2 |
| 7 | Güç/uyku davranışı | ayar kaydı | §10/5 ⚠️ |
| 8 | Doğrulama: SMART + tarama + ölçüm | rapor | `hdd-dogrulama.md` |
| 9 | Geri kurtarma tatbikatı | tatbikat kaydı | `hdd-dogrulama.md` §5 |
| 10 | Kütüphane iş akışını örneklerle sınama | örnek kaydı | `hdd-ornekler.md` |

## §14 Sık Yapılan Hatalar

| # | Hata | Belirti | Doğrusu |
|---|---|---|---|
| 1 | "RAID = yedek" | Aynı arıza iki diski de götürür | Ayrı ortamda yedek (§7.2) |
| 2 | SMR diski yoğun rastgele işte kullanmak | Yazma hızının çökmesi | CMR / yazıyı SSD'ye taşı |
| 3 | Diski hoparlörün üstüne/yanına koymak | Dinleme sırasında tıkırtı | §9.2/2 · ıslak montaj |
| 4 | Çalarken kütüphane taraması | Atlanan kare (underrun) riski | Boş saate planla (§9.3/3) |
| 5 | Tek diske hem kütüphane hem indeks hem log | Sürekli seek | Katman ayrımı (§6.3) |
| 6 | Yedeği aynı makinede tutmak | Yangın/silme = her şey gitti | Uzak kopya (§7.2/3) |
| 7 | Test edilmemiş yedek | Kurtarılamayan veri | Düzenli tatbikat |
| 8 | Hız için RAID 0 | Arşivde tek disk arızası | RAID 0 yalnızca geçici işlerde |
| 9 | Disk uyusunu açık bırakıp canlı okumak | Uyanma gecikmesi → kesinti | Güç yönetimi ayarı (⚠️) |
| 10 | Ölçüm olmadan "sessizdir" demek | Dinlemede duyulan gürültü | `hdd-dogrulama.md` §4 ölçümü |
| 11 | Kapasiteyi tek diske sıkıştırmak | Yedekleme penceresi şişer | Kapasite + yeniden kurulum süresi birlikte hesaplanır |
| 12 | Envanter yokken satın alma | Uyumsuz/kötü seçim | Q-HK1 kapatılır |

## §15 Terim Sözlüğü

| # | Terim | Tanım |
|---|---|---|
| 1 | Platter | Manyetik kaplı dönen plaka |
| 2 | Head | Okuma/yazma kafası |
| 3 | Seek | Kafanın izden izle geçiş süresi |
| 4 | Rotational latency | İstenen sektörün kafaya gelme bekleme süresi |
| 5 | RPM | Devir/dakika — dönüş hızı |
| 6 | Sector | 512 B veya 4 KiB'lik fiziksel birim |
| 7 | Cluster | Dosya sisteminin ayırma birimi |
| 8 | 512e / 4Kn | 512 emüle / 4096 doğal sektör |
| 9 | CMR / SMR | Bindirmesiz / bindirmeli manyetik kayıt |
| 10 | ZBR | Bölgelere göre bit yoğunluğu |
| 11 | Write amplification | Fiziksel yazımın mantıksal yazımın üstü olması |
| 12 | NCQ | Doğal komut kuyruğu (derinlik 32) |
| 13 | RAID | Disklerin mantıksal birleşimi (yedek değil) |
| 14 | Parity | RAID 5/6'daki yeniden kurulum verisi |
| 15 | Rebuild | Arıza sonrası yeniden kurulum |
| 16 | 3-2-1 | 3 kopya · 2 ortam · 1 uzak |
| 17 | Underrun | Veri yetmeyince tamponun boşalması |
| 18 | Hot-plug | Çalışırken takma/çıkarma |
| 19 | Park | Kafanın dinlenme konumuna geçmesi |
| 20 | Bad sector | Okunamayan/yazılamayan sektör |
| 21 | SMART | S.M.A.R.T. — disk sağlık göstergeleri |
| 22 | UAPM/APM | Güç yönetimi (aktif/uyku politikası) |
| 23 | Stream | Kesintisiz sıralı okuma akışı |
| 24 | Cold storage | Değişmeyen, nadir erişilen katman |

## §16 Kapsam Dışı (özet)

| # | Konu | Sahip |
|---|---|---|
| 1 | Dosya sistemi ve fsync davranışı | K000-isletim-sistemi |
| 2 | Sürücü/kesme/DMA | K000-isletim-sistemi · donanım-kernel |
| 3 | Fiziksel envanter (hangi disk) | K001-donanım (Q-HK1) |
| 4 | SSD tarafı (flash, TRIM, TBW) | `ssd-kapsam.md` |
| 5 | Depolama doğrulama uygulaması | `hdd-dogrulama.md` |
| 6 | Depolama iş akışı örnekleri | `hdd-ornekler.md` |
| 7 | Ağ/NAS depolama protokolleri | K014-network · K015-media |
| 8 | DSP ve ses zinciri katsayıları | K003-audio · K004-dsp |

## §17 Kaynakça (bu dosyanın web kaynakları)

| # | Kaynak | URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | Western Digital destek | https://www.westerndigital.com/support | 2026-10-13 | ✅ HTTP 200 (HEAD, Invoke-WebRequest) |
| 2 | Seagate destek | https://www.seagate.com/support/ | 2026-10-13 | ✅ HTTP 200 (HEAD, Invoke-WebRequest) |
| 3 | Vault: hdd düğüm künyesi | `hdd/index.md` (47 satır) | 2026-10-12 | ✅ okundu |
| 4 | Vault: kapsam sınırı | `donanaım-type-kapsam.md` §2/A2 · §6 Q-T1 | 2026-10-13 | ✅ okundu |
| 5 | Vault: ölçüm eşikleri | `donanım-anayasa.md` §10/3 | 2026-10-12 | ✅ okundu (kanıt satırı) |

**Uydurma kaynak yok:** Yukarıdaki 2 web URL'si bu oturumda HEAD ile 200
döndürülerek doğrulanmıştır; diğer satırlar disk dosyası referansıdır.

## §18 Çapraz Referanslar

| # | Hedef | İlişki |
|---|---|---|
| 1 | `[[hdd/index]]` | Bu konunun künyesi (ELI15 · açık soru) |
| 2 | `[[ssd/index]]` | Kardeş depolama sınıfı |
| 3 | `[[ssd-kapsam]]` | Sıcak katman sahibi |
| 4 | `[[donanaım-type-kapsam]]` | Üst kapsam · envanter UNKNOWN kaydı |
| 5 | `[[donanaım-type-dogrulama]]` | Üst doğrulama tablosu (17 satır) |
| 6 | `[[donanaım-type/index]]` | Üst künye · children listesi |
| 7 | `[[pcm3168a-codec]]` | DAC/codec — zincir 6. aşama |
| 8 | `[[ak4458-dac]]` | DAC — zincir 6. aşama |
| 9 | `[[tpa3255-amplifier]]` | Amfi kayıt dosyası (karar girdisi; topoloji Class AB sabit) |
| 10 | `[[output-kapsam]]` | Çıkış yolu / tampon sahibi |
| 11 | `[[isletim-sistemi-core-kapsam]]` | Dosya sistemi sınırı (VFS) |
| 12 | `hdd-dogrulama.md` | SMART · tarama · gürültü · kurtarma kapıları |
| 13 | `hdd-ornekler.md` | Tarama · streaming buffer · rotasyon örnekleri |

---

## §19 Kapasite Bütçesi ve Karar Matrisi

### §19.1 Kapasite hesabı (aritmetik — ölçüm değil)

| # | Hesap | İşlem | Sonuç |
|---|---|---|---|
| 1 | 1 dakikalık 48 kHz/24-bit stereo PCM | 60 × 48000 × 3 × 2 | 17.280.000 B ≈ 16,48 MiB |
| 2 | 1 dakikalık 192 kHz/24-bit stereo PCM | 60 × 192000 × 3 × 2 | 69.120.000 B ≈ 65,92 MiB |
| 3 | 1 dakikalık CD kalitesinde (44,1 kHz/16-bit) PCM | 60 × 44100 × 2 × 2 | 10.584.000 B ≈ 10,09 MiB |
| 4 | 500 albümlük CD kalitesinde WAV kütüphanesi (40 dk/albüm) | 500 × 40 × 10,09 MiB | ≈ 197 GiB |
| 5 | Aynı kütüphane 24-bit/96 kHz'e çevrilse | ≈ 96/44,1 × 24/16 × (MiB/dk) | ≈ 6× alan → ≈ 1,15 TiB (kaba üst sınır) |
| 6 | 2 TB'lık diskte %75 doluluk hedefi | 2000 × 0,75 | 1500 GB kullanılabilir kabul |
| 7 | Aynı diske 10.000 şarkı sığar mı? (ortalama 30 MB/flac) | 10000 × 30 MB | ≈ 286 GiB — sığar (kaba tahmin ⚠️) |
| 8 | Yedek için gereken toplam alan | ana + 2 kopya | 3 × katman büyüklüğü |

**Uyarı:** 5–7. satırlar **kaba tahmindir** — gerçek dosya boyutları ölçülmeden
kullanılamaz (Q-HK1 · ⚠️ VERIFICATION REQUIRED). 1–4. satırlar yalnızca
bayt-aritmetiğidir.

### §19.2 Depolama bütçe şablonu (doldurulacak)

| # | Satır | Değer | Durum |
|---|---|---|---|
| 1 | Toplam albüm/sayı | — | UNKNOWN |
| 2 | Ortalama dosya biçimi ve bit hızı | — | UNKNOWN |
| 3 | Toplam ham boyut | — | UNKNOWN (1-2. satırdan türetilir) |
| 4 | İndeks + kapak artwork alanı | — | UNKNOWN |
| 5 | Günlük ekleme (değişim oranı) | — | UNKNOWN |
| 6 | Yedek çoğaltma çarpanı | 3 (3-2-1) | tanımlı (§7.2) |
| 7 | Toplam kapasite ihtiyacı | 4-6 satırlarından | HESAPLANAMAZ — ön veri yok |
| 8 | RAID için disk sayısı | kademe ile birlikte (§7.1) | Q-HK3 |

### §19.3 Karar soruları (satın alma öncesi kapı)

| # | Soru | İyi cevap | Kötü cevap |
|---|---|---|---|
| 1 | En büyük tek risk nedir? | "Veri kaybı" → 3-2-1 başlar | "Yavaşlık" → ölçüm olmadan SSD'ye koşma |
| 2 | Aynı anda kaç okuma akışı olur? | Sayısal (§6.1'den) | "birkaç" |
| 3 | Ağırlıklı profil sıralı mı rastgele mi? | §6.2 satır numarası | sezgi |
| 4 | Yazma işi yoğun mu? (indirme, kodlama) | Evet → CMR/SSD yazma | Hayır → SMR tolere |
| 5 | Disk kiminle aynı odada? | Hoparlörden uzak konum | Derin bas üstü |
| 6 | Yedek uzakta mı? | Fiziksel ayrı konum | Aynı rafta ikinci disk |
| 7 | Kurtarma testi yapıldı mı? | Tarihli tatbikat | "yedek var" |
| 8 | Envanter biliniyor mu? | Marka/model/yıl | UNKNOWN → DUR (Q-HK1) |

### §19.4 Bağımlılık haritası

| # | Bu konu | Bağımlı olduğu | Bağımlı olduğu yer |
|---|---|---|---|
| 1 | hdd-kapsam | İşletim sistemi dosya davranışı | `[[isletim-sistemi-core-kapsam]]` |
| 2 | hdd-kapsam | Donanım envanteri (UNKNOWN) | K001 Q4 · `[[donanaım-type-kapsam]]` |
| 3 | hdd-kapsam | Çıkış tamponu (512/48k) | `[[output-kapsam]]` |
| 4 | hdd-kapsam | DAC/codec seçimi | `[[pcm3168a-codec]]` · `[[ak4458-dac]]` |
| 5 | hdd-kapsam | Amplifikatör topolojisi (Class AB sabit) | `[[tpa3255-amplifier]]` + kök CLAUDE.md sabiti |
| 6 | hdd-dogrulama | Bu dosyanın §13 kapıları | `hdd-dogrulama.md` |
| 7 | hdd-ornekler | Bu dosyanın §6.2 profilleri | `hdd-ornekler.md` |
| 8 | ssd-kapsam | Sıcak katman devri | `[[ssd-kapsam]]` |
| 9 | Ölçüm eşikleri (SNR/THD+N) | Üst kabul kapısı | `donanım-anayasa.md` §10/3 |
| 10 | Yazım yasağı | v2 kaynağına sadakat | `donanaım-type/index.md` yazım notu |

### §19.5 Doğrulama kapısına devir

| # | Bu dosyadaki iddia | Kanıt tipi | Gittiği dosya | Seviye |
|---|---|---|---|---|
| 1 | Dönüş gecikmesi tablosu (RPM → ms) | aritmetik (60000÷RPM) | `hdd-dogrulama.md` §1 | S2 |
| 2 | 512 örnek = 10,67 ms | aritmetik (512÷48000) | `hdd-dogrulama.md` §1 | S2 |
| 3 | SATA ≈ 600 MB/s tavan | türetilmiş (8b/10b) | `hdd-dogrulama.md` §4 | S2 |
| 4 | Disk gürültü/titreşim seviyesi | **ölçüm gerekli** | `hdd-dogrulama.md` §4 | S1 → ❌ henüz yok |
| 5 | Bad-sector oranı | **tarama gerekli** | `hdd-dogrulama.md` §3 | S1 → ❌ henüz yok |
| 6 | Yedek kurtarılabilirliği | **tatbikat gerekli** | `hdd-dogrulama.md` §5 | S1 → ❌ henüz yok |
| 7 | Envanter (marka/model) | insan girdisi | `hdd-dogrulama.md` §6 | ❌ UNKNOWN |
| 8 | RAID/backup kararı | üst karar | Q-HK3 | ⚠️ açık |
| 9 | 2 web URL (WD · Seagate) | HEAD 200 | `hdd-dogrulama.md` §7 | S3 ✅ |
| 10 | Depolama → DAC zinciri tarifi | vault referansı | bu dosya §11 | S4 |

---

## §20 Dürüstlük Bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler yalnız doğrulanmış
kaynaklar içerir; doğrulanamayan iddialar ⚠️ VERIFICATION REQUIRED ile
işaretlidir. Ölçüm içermeyen hiçbir rakam "ölçüldü" diye sunulmaz; aritmetik
ile türetilen değerler §'lerinde "türetilmiş" olarak etiketlenmiştir.

---

## §21 OS × DİL ERİŞİM MATRİSİ (depolama — HDD)

> Bu bölüm `donanım-platformlararasi-kod-ornekleri.md` §PLATFORM MATRİSİ (P1–P6 · G1–G11)
> üzerine kuruludur; **yeni URL eklenmemiştir.** Hücreler "OS algılama → HDD/depolama
> erişim yolu" biçimindedir; doğrulanmamış dil-OS bağları hücre içinde `⚠️` ile işaretlidir.
> Bölüm yalnız erişim yolu tanımlar — ölçüm/kanıt iddiası taşımaz (§20 geçerlidir).

| Dil | Windows | macOS (Darwin) | Linux |
|---|---|---|---|
| JavaScript (Node.js) | `process.platform` (§G1) → `GetLogicalDrives` / `A:`–`Z:` tarama (§P1) | `process.platform` (§G1) → `/Volumes` dizini (§P1) | `process.platform` (§G1) → `/proc/mounts` (§P1) |
| C | `#if defined(_WIN32)` (§G2) → `GetLogicalDrives` (§P1) | `__APPLE__` ⚠️ (§G2) → `/Volumes` + IOKit (§P1 · §P4) | `__linux__` (§G2) → `/proc/mounts` + sysfs `/sys/class/block` (§P1 · §P4) |
| Python | `sys.platform` (§G3) → `ctypes` + `GetLogicalDrives` ⚠️ (URL'siz) | `sys.platform` (§G3) → `/Volumes` (§P1) | `sys.platform` (§G3) → `/proc/mounts` (§P1) |
| Go | `runtime.GOOS` (§G4) → kök listeleme std'de yok ⚠️ (§G4.3) | `runtime.GOOS` (§G4) → kök listeleme std'de yok ⚠️ (§G4.3) | `runtime.GOOS` (§G4) → `/proc/mounts` okuma (§G4.2 · §P1) |
| Rust | `cfg!` (§G5) → sarmalayıcı ADR bekliyor ⚠️ (§G5.3) | `cfg!` (§G5) → `/Volumes` ⚠️ (sarmalayıcı yok) | `cfg!` (§G5) → `/proc/mounts` okuma ⚠️ (crate doğrulanmadı) |
| C# (.NET) | `RuntimeInformation` (§G6) → `DriveInfo` (§G6.2) | `RuntimeInformation` (§G6) → `DriveInfo` → `/Volumes` (§G6.2 · §P1) | `RuntimeInformation` (§G6) → `DriveInfo` → `/proc/mounts` (§G6.2 · §P1) |
| PHP | `PHP_OS_FAMILY` (§G7) → `disk_total_space` `A:`–`Z:` aday taraması (§G7.2 · §P1) | `PHP_OS_FAMILY` (§G7) → `/Volumes` taraması (§G7.2 · §P1) | `PHP_OS_FAMILY` (§G7) → `/proc/mounts` okuma (§P1) |
| Bash | — (çocuk süreç: PowerShell) ⚠️ | `uname -s` ⚠️ MINGW (§G8.1) → `ls /Volumes` (§P1) | `uname -s` (§G8.1) → `cat /proc/mounts` (§P1) |
| PowerShell | `[RuntimeInformation]::IsOSPlatform` (§G8.2) → `GetLogicalDrives` / `DriveInfo` (§P1) | — | — |
| Java | `System.getProperty("os.name")` (§G8.3) → `File.listRoots()` ⚠️ (disk API doğrulanmadı) | aynı ⚠️ (§G8.3) → `/Volumes` (§P1) | `System.getProperty` (§G8.3) → `/proc/mounts` okuma ⚠️ (JNA/FFM URL'siz) |
| Swift | `#if os(Windows)` (§G9) → fiziksel erişim yolu yazılmadı ⚠️ (rapor §4.11) | `#if os(macOS)` (§G9) → `/Volumes` (§P1) | `#if os(Linux)` (§G9) → `/proc` köprüsü (§P1) |
| Kotlin | Kotlin/Native POSIX "Windows (MinGW)" / JVM `os.name` (§G10) ⚠️ | Kotlin/Native POSIX (§G10) → `/Volumes` ⚠️ | Kotlin/Native POSIX + `/proc` (§G10) ⚠️ |
| C++ | `#if defined(_WIN32)` (§G11) → G2 ile aynı Win32 yolları | `__APPLE__` ⚠️ (§G11) → `/Volumes` + IOKit (§P1 · §P4) | `__linux__` (§G11) → `/proc/mounts` + sysfs `/sys/class/block` (§P1 · §P4) |

**Öneri (repo dili PHP/JS):** Disk kök/bölüm tespiti PHP'de `disk_total_space` aday
taraması (§G7.2), JS/Node'da `process.platform` + `/Volumes` / `/proc/mounts` okuma ile
başlar. Windows'ta fiziksel disk ayrıntısı PHP'den doğrudan erişilemez — çocuk süreç
(PowerShell `DriveInfo`/`GetLogicalDrives`) şablonuna devredilir; macOS `IOKit`, Linux
sysfs `/sys/class/block` **Çİ/C++** katmanındadır (§P4).

**Risk/uyumluluk (korunan ⚠️ kayıtları):**
- `__APPLE__` ön-tanımlı makrosu için doğrulanmış birincil URL yok (§K4/R4); ortak ⚠️
  kayıtları — macOS `sysctl -n hw.memsize` 404 (R1) ve `getifaddrs` 404 (R8) — bu
  dosyada **silinmez**; HDD erişimi bellek/ağ yolunu kullanmaz, kayıt korunur.
- Git Bash/MSYS2 `uname -s` MINGW öneki doğrulanamadı (§G8.1) → ⚠️ VERIFICATION REQUIRED.
- Go std'de disk kök listeleme yok (§G4.3) · Rust sarmalayıcı ADR bekliyor (§G5.3) ·
  Swift/Kotlin dil bağları doğrulanmadı (§G8.4 · §G9 · §G10) → bu hücreler şablondur.
- SMART / bad-sector okuma yolu bu envanterde **yok** (Q-HK1) → matriste yer almaz,
  uydurulmaz; ölçülmüş depolama kanıtı bu bölümden üretilmez (§20).

Kapsamlı şablonlar: `donanım-platformlararasi-kod-ornekleri.md` §G1–§G11 · tam matris: `raporlar/K001-donanım-os-dil-matrisi.md`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
