---
node_id: ssd-kapsam
node_type: module
status: draft
parent_id: K001-donanım/donanaım-type/ssd
children: []
depends_on:
  - K000-isletim-sistemi
  - K001-donanım/donanaım-type
evidence:
  - "K001-donanım/donanaım-type/ssd/index.md (47 satır, 2026-10-12)"
  - "K001-donanım/donanaım-type/donanaım-type-kapsam.md §2/A3 — ssd envanteri UNKNOWN"
  - "https://www.crucial.com/support (HTTP 200, 2026-10-13)"
  - "https://semiconductor.samsung.com/ssd/ (HTTP 200, 2026-10-13)"
validation: "draft — 2026-10-13 yazıldı · ≥500 satır ölçümü yazım sonrası · envanter kanıtı YOK (K001 Q4)"
open_questions:
  - "Q-SK1: Kullanılan SSD envanteri (marka/model/kapasite/arayüz) bilinmiyor — UNKNOWN"
  - "Q-SK2: Boot diskinin mevcut konumu ve yedekleme düzeni bilinmiyor — UNKNOWN"
  - "Q-SK3: Kütüphane için hedef dayanıklılık (TBW/DWPD) üst karar bekliyor — ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# ssd-kapsam — Katı Hal Disk Kapsamı (Flash Depolama)

> **Yazım notu:** `donanaım-type` yazımı v2 kaynağına sadıktır — düzeltme yasak
> (v2 §3 KURAL 1). Bu dosya `ssd/` alt-konunun **kapsam** belgesidir: flash
> bellek kademesi, yazma çarpanı, TRIM, NVMe/SATA farkı, müzik kütüphanesinde
> okuma-öncelikli çalışma, dayanıklılık hesabı ve boot diski seçimi.

## §1 ELI15

Katı hal disk (SSD) veriyi **elektriksel yük olarak** hücrelerinde tutar —
dönen plaka, kafa ve mekanik seek **yoktur**. Bu, iki sonucu doğurur: erişim
**mikrosaniye** ölçeğine iner (mekanik diskin milisaniyesine karşı) ve **ses
yoktur**. Bedeli ise farklıdır: hücreler sınırlı sayıda yazılabilir (ömrü
tükenir), yazma işlemi **önce silmeyi** gerektirir (yazma çarpanı), ve
kapasite başına maliyet mekanik diskin üzerindedir. CoreMusic'te SSD'nin rolü
**sıcak katman + boot diski**dir; ana kütüphane kapasitesi hâlâ HDD'nin
işidir (`hdd-kapsam.md` §6.3).

> ⚠️ VERIFICATION REQUIRED: Envanter bilinmiyor (K001 Q4 · Q-SK1). Bu dosyadaki
> **kademe/ömür aralıkları üreticiye ve nesile özeldir** — sayısal eşikler
> datasheet ile doğrulanmadan bağlayıcı değildir.

## §2 Kapsam — İÇİNDE NE var

| # | Kapsam İÇİ | Durum |
|---|---|---|
| A1 | Flash bellek fiziksel yapısı (blok/sayfa/hücre) | ✅ genel bilgi |
| A2 | SLC / MLC / TLC / QLC kademeleri ve ödünleşim | ✅ kavramsal; sayısal ömür ⚠️ |
| A3 | Program-silme (P/E) döngüsü ve ömür | ✅ kavram; TBW hesabı aritmetik |
| A4 | Yazma çarpanı (write amplification) nedenleri | ✅ mekanizma |
| A5 | TRIM / discard davranışı | ✅ kavram; uygulama K000 sınırında |
| A6 | NVMe vs SATA performans matrisi | ✅ kavramsal kademe; ölçüm `ssd-dogrulama.md` |
| A7 | Müzik kütüphanesinde okuma-öncelikli çalışma | ✅ mimari rol |
| A8 | Dayanıklılık hesabı (TBW ↔ DWPD) | ✅ aritmetik |
| A9 | Boot diski seçimi kriterleri | ✅ seçim matrisi |
| A10 | Sıcaklık/güç yüzeyi (yalnız sınır) | ⚠️ ölçüm `ssd-dogrulama.md` §5 |

## §3 Kapsam — DIŞINDA NE var (sınır komşuları)

| # | Dışında | Nereye gider | Dayanak |
|---|---|---|---|
| B1 | Dosya sistemi, TRIM'in çağıldığı syscall, fsync | K000-isletim-sistemi | `isletim-sistemi-core-kapsam.md` §A.2 (A3 satırı) |
| B2 | Sürücü / NVMe katmanı / kesme | K000-isletim-sistemi (kernel) · donanım-kernel | `donanım-anayasa.md` §3 satır 47 |
| B3 | Fiziksel envanter (hangi SSD) | K001-donanım · K001 Q4 | `donanaım-type-kapsam.md` §6 Q-T1 |
| B4 | HDD tarafı (mekanik, RAID, gürültü) | `hdd-kapsam.md` | kardeş alt-konu |
| B5 | Ses zinciri (DAC/codec/amfi) | `[[pcm3168a-codec]]` · `[[ak4458-dac]]` · `[[tpa3255-amplifier]]` | `audio/cipler/*` |
| B6 | Çıkış tamponu ve ASIO/WASAPI | `[[output-kapsam]]` | K003-audio |
| B7 | SSD firmware'i (FTL/GC/akıllı komutlar) | K005-firmware | `donanım-anayasa.md` §2 |
| B8 | Ölçüm uygulaması (hız/sağlık/sıcaklık) | `ssd-dogrulama.md` | bu konunun kanıt dosyası |
| B9 | Uygulama örnekleri (indeks/cache) | `ssd-ornekler.md` | bu konunun örnek dosyası |

## §4 Flash Bellek Yapısı

### §4.1 Katmanlar

| # | Katman | Ne | Erişim birimi |
|---|---|---|---|
| 1 | Hücre (cell) | Yük tutan elektriksel hapsiyer | Bit |
| 2 | Satır (word line) | Hücreyi seçen hat | — |
| 3 | Sayfa (page) | Paralel okunan/yazılan birim | Genellikle KiB ölçeğinde |
| 4 | Blok (block) | **Önce silinen** birim (sayfalar kümesi) | Silme birimi |
| 5 | Düzlem (plane) | Blokların paralel çalıştığı küme | Paralellik |
| 6 | Paket (package) | Fiziksel yonga | — |
| 7 | Kanal/die | Paralel veri yolu | — |

**Temel kısıt (neden yazma çarpanı var):** okuma **sayfa** bazında, silme
**blok** bazındadır. Boşaltmak istenen bir sayfayı yazmak için önce **tüm
blok** silinmeli, kalan geçerli sayfalar başka yere taşınmalıdır → bu, yazma
çarpanının kök nedenidir (§7).

### §4.2 Bit/hücre kademesi (SLC → QLC)

| # | Kademe | Hücre başına bit | Adresleme | Okuma güvenilirliği | Maliyet/GB |
|---|---|---|---|---|---|
| 1 | SLC | 1 | 2 durum | en yüksek (en geniş eşik payı) | en yüksek |
| 2 | MLC | 2 | 4 durum | yüksek | yüksek |
| 3 | TLC | 3 | 8 durum | orta | orta |
| 4 | QLC | 4 | 16 durum | en düşük (en dar eşik payı) | en düşük |

**Okuma güvenilirliği neden önemli:** durum sayısı arttıkça iki bit arası
gerilim farkı daralır; kontrolcü daha çok **ECC (hata düzeltme)** kullanır ve
düzeltme yükü artar. Bu, QLC'de okuma hızının ve dayanıklılığın düşmesinin
teknik nedenidir.

### §4.3 Kademe — ömür ve iş yükü (aralık olarak; ⚠️ üreticiye özel)

| # | Kademe | P/E döngü aralığı (kaba) | Tipik iş yükü | Uygun kullanım |
|---|---|---|---|---|
| 1 | SLC | en yüksek (binlerce on bin) | yoğun yazma, kurumsal | Scratch / journal ⚠️ |
| 2 | MLC | yüksek | karışık | — (pazada seyrekleşti) ⚠️ |
| 3 | TLC | orta | çoğunlukla okuma + ılımlı yazma | **Ana disk / kütüphane sıcak** ⚠️ |
| 4 | QLC | düşük | ağırlıklı okuma, sıralı yazma | Arşiv/sabit okuma ⚠️ |

> ⚠️ VERIFICATION REQUIRED: P/E döngü sayıları **nesil, üretim süreci ve
> ürün** ile değişir; bu tablo **aralık/kademe** olarak kullanılır, sayısal
> değer satın alma kriteri yapılmaz (Q-SK1).

### §4.4 SLK/Pseudo-SLC önbelleği

| # | Kavram | Ne | Sonuç |
|---|---|---|---|
| 1 | SLC önbelleği | TLC/QLC'nin bir kısmı SLC gibi kullanılır | İlk yazmada yüksek hız |
| 2 | Önbellek taşması | Önbellek dolunca gerçek kademe hızına düşer | Uzun yazmada hız düşüşü |
| 3 | Ölçüm etkisi | Kısa test hızlı, uzun test yavaş çıkabilir | `ssd-dogrulama.md` §2 |
| 4 | Müzik etkisi | Albüm kopyalama uzun iş olabilir | Sıralı yazmada ⚠️ ölçüm |

## §5 Yazma Çarpanı (Write Amplification)

### §5.1 Tanım

`WAF = Fiziksel yazılan bayt ÷ Mantıksal yazılan bayt`

| # | Durum | WAF | Açıklama |
|---|---|---|---|
| 1 | İdeal | ≈ 1 | FTL yazdığıyla aynı |
| 2 | Önbellek + taşma | > 1 | SLC→TLC kopyası |
| 3 | Garbage collection (GC) | > 1 | Blok taşıma yazmaları |
| 4 | Journal/log yazımı | > 1 | Küçük tekrarlı yazmalar |
| 5 | Dosya sistemi ≠ hizalı yazım | > 1 | Bir bayt için tam sayfa yazımı |
| 6 | TRIM verilmemiş silme | > 1 | "Ölü" veri taşınmaya devam eder |

### §5.2 WAF'ı yükselten davranışlar

| # | Davranış | Neden | Azaltma |
|---|---|---|---|
| 1 | Küçük rastgele yazmalar (log, indeks) | Sayfa yeniden yazımı | Yazma biriktirme / ayrık disk |
| 2 | %100 dolu disk | GC'nin temiz blok bulamaması | Doluluk sınırı (§5.4) |
| 3 | Hizasız bölüm/blok | Tam sayfa yazımı | 4 KiB hizalama (`hdd-kapsam.md` §4.2) |
| 4 | TRIM yok | Silinen bloklar canlı sanılır | İşletim sistemi/TRIM zinciri (§6) |
| 5 | Aşırı eşzamanlı yazma | Kuyruk birikimi | Yazmayı boş saatlere serpiştir |
| 6 | Sayfa yeniden yazma (partial page) | Silme blok ölçeğinde | Ekleme günlüklerini toplu yaz |

### §5.3 Ölçüm etiği

| # | Kural | Açıklama |
|---|---|---|
| 1 | WAF **doğrudan kullanıcıya ölçülemez** | Üreticinin dahili sayaçları 🔒 kısıtlı olabilir |
| 2 | Doluluk oranı ve yazma hızı **ölçülebilir** | `ssd-dogrulama.md` §2 |
| 3 | Uzun süreli test, kısa testten farklı sonuç verir | §4.4/2-3 |
| 4 | "Ömür" iddiası yalnız üretici TBW'siyle yazılır | §8 |

### §5.4 Doluluk kuralı (tasarım)

| # | Doluluk | GC davranışı | Öneri |
|---|---|---|---|
| 1 | %0–50 | serbest blok bol | rahat |
| 2 | %50–70 | yeterli | normal |
| 3 | %70–80 | dikkat | `hdd-kapsam.md` §19.1'deki %75 bütçe kuralı |
| 4 | %80–90 | yavaşlama riski | indirme/scratch diskinde kaçınılır |
| 5 | %90–100 | WAF keskin yükselir | **yasak** (ana disk) |

## §6 TRIM

| # | Konu | Ne | Sahiplik |
|---|---|---|---|
| 1 | TRIM ne yapar | "bu aralık artık kullanılmıyor" bilgisini diske iletir | İşletim sistemi → sürücü |
| 2 | Etkisi | Disk, blokları canlı saymayı bırakır → GC yükü azalır | Donanım tarafı |
| 3 | Windows yüzeyi | Silme/bırakma bildirimi (dosya sistemi üzerinden) | K000-isletim-sistemi |
| 4 | Genişletilmiş TRIM (dondurucu/snapshot) | Kullanılmayan alanın tamamı bildirilebilir | K000 |
| 5 | SSD'ye özgü komut (format) | Blok/sayfa birimini açıklama | K000/K005 sınırı |
| 6 | Sıralı yazmada kazanç sınırlı olabilir | Yeni veri zaten temiz bloğa gider | `ssd-dogrulama.md` §3 |
| 7 | TRIM'in geri alınması yok | Yanlış bildirim veri kaybı yapar | Yalnızca işletim sistemi üretir |
| 8 | RAID/kontrolcü altında davranış | Yol bozulabilir | ⚠️ yapılandırma bağımlı |

**Mimari kural:** TRIM **bu konuda uygulanmaz**, yalnız **doğrulanır** —
kod yüzeyi K000'dedir (B1). Bu dosya yalnızca "**TRIM veriliyor mu?**"
sorusunun mimari önemini yazar.

## §7 NVMe vs SATA Performans Matrisi

### §7.1 Arayüz kademesi

| # | Kriter | SATA (6 Gb/s) | NVMe (PCIe) | Yorum |
|---|---|---|---|---|
| 1 | Fiziksel taşıyıcı | SATA veri yolu | PCIe yolu | Farklı soket/protokol |
| 2 | Protokol | AHCI (eski amaç: HDD'ler) | NVMe (SSD için tasarlandı) | Komut kuyruğu derinliği |
| 3 | Komut kuyruğu | Sınırlı (AHCI tek kuyruk mantığı) | Birden çok kuyruk, derin | Eşzamanlı işte belirgin fark |
| 4 | Gecikme (yapısal) | daha yüksek | daha düşük | Ölçüm: `ssd-dogrulama.md` §2 |
| 5 | Teorik üst sınır | 6×10⁹ bit/s ÷ 8 = 750 MB/s ham; 8b/10b → **≈ 600 MB/s** (türetilmiş) | PCIe nesiline × kanal sayısına bağlı (⚠️ ölçüm) | Pratik değer ölçülür |
| 6 | Pratik sıra-dışı | Diskin iç sınırları | Diskin iç sınırları | Üretici datasheet ⚠️ |
| 7 | Masaüstü formu | 2.5" / M.2 (SATA) | M.2 (PCIe) / kart | Fiziksel uyum |
| 8 | Dizüstü uyumu | Bazı modeller yok | Yaygın | Ürün-özel ⚠️ |
| 9 | Yetişkinlik | Olgun | Standart | — |

### §7.2 Seçim matrisi (iş yüküne göre)

| # | İş | SATA yeterli mi? | NVMe gerekir mi? | Gerekçe |
|---|---|---|---|---|
| 1 | Boot / sistem | evet | opsiyonel | Açılışta tek seferlik fark |
| 2 | Uygulama açılışı | evet | iyileştirir | Çoklu küçük okuma |
| 3 | Kütüphane indeksleme | evet | iyileştirir | Rastgele okuma yoğun |
| 4 | Tek akışlı müzik okuma | fazlasıyla yeterli | gereksiz | `hdd-kapsam.md` §6.1 — ≈1,1 MiB/s üst sınır |
| 5 | Aynı anda 4+ akış + tarama | sınırda | evet | Kuyruk derinliği |
| 6 | Büyük albüm kopyalama | sınırlı (≈600 MB/s tavandan) | evet | Sıralı hız |
| 7 | Video/çoklu iş istasyonu | sınırda | evet | Bant |
| 8 | Arşiv / salt okuma | fazlasıyla yeterli | gereksiz | Maliyet önceliği |
| 9 | Boot diski + yedek | evet | — | §9 |

**Kural:** "NVMe her zaman daha iyi" **doğru değildir** — iş yükü
**okuma-öncelikli ve tek akışlıysa** SATA SSD mekanik diskin önüne geçtiği
için yeterlidir; fark ancak **ölçümle** gerekçelendirilir.

### §7.3 Kademe kararının kayıt tablosu

| # | Soru | Evet → | Hayır → |
|---|---|---|---|
| 1 | Tek akışlı okuma baskın mı? (`hdd-kapsam.md` §6.1 ≈1,1 MiB/s) | SATA SSD düşün | §7.2 satır 5-7 |
| 2 | Aynı anda 4+ yoğun I/O akışı mı? | NVMe düşün | SATA kalabilir |
| 3 | Sıralı kopya ≥ ~500 MB/s gerekiyor mu? | NVMe düşün | SATA yeterli |
| 4 | Mevcut soket yalnız M.2 PCIe mi? | NVMe zorunlu | seçenek serbest |
| 5 | Bütçe kritik mi, fark ölçüldü mü? | ölç → kararı yaz | ölç → kararı yaz |

**Kayıt kuralı:** Bu tablonun çıktısı, `ssd-dogrulama.md` §2 ölçümü ile
**birlikte** dosyalanır — tek başına "SATA yeterli" ya da "NVMe gerekli"
cümlesi kanıt sayılmaz (ölçüm PLANNED · Q-SK1 UNKNOWN).

## §8 Müzik Kütüphanesi için Okuma-Öncelikli Çalışma

### §8.1 İş yükü dağılımı (beklenen)

| # | İş | Okuma/Yazma | Frekans | SSD önceliği |
|---|---|---|---|---|
| 1 | Çalma sırası okuma | okuma | sürekli | **en yüksek** |
| 2 | Künye/kapak taraması | okuma (küçük) | yoğun tur | yüksek |
| 3 | İndeks/arama | okuma + az yazma | yoğun tur | yüksek |
| 4 | Yeni albüm ekleme | sıralı yazma | seyrek | orta |
| 5 | Çıkış/DSP tamponu | — | — | bu katmanda değil (§B6) |
| 6 | Boot/system yazma | küçük yazma | sık | Boot diskinde ayrı diske taşınabilir |
| 7 | Günlük/log yazma | küçük rastgele | sürekli | **ayrı diske** (WAF) |
| 8 | Yedekleme | sıralı yazma | boş saat | HDD hedefi (§8.3) |

### §8.2 Okuma-öncelikli tasarım kuralları

| # | Kural | Neden |
|---|---|---|
| 1 | Yazmayı okuma yolundan ayır | Küçük yazmalar kuyruğa biner → okuma gecikmesi |
| 2 | İndeks/log'ü ana SSD'de tutma | WAF + doluluk (§5.2/1, §5.4) |
| 3 | Boot diski ile kütüphane SSD'si **farklı** olabilir | Boot I/O'su ile medya I/O'su çakışmaz |
| 4 | Çalışma setini sıcak tut | Son albümler + çalma listeleri (§1 K001) |
| 5 | HDD'den SSD'ye taşıma eşiği | Rastgele okuma payı yükseldikçe |
| 6 | TRIM'in verildiğini doğrula | `ssd-dogrulama.md` §3 |
| 7 | Doluluğu %80 altında tut | §5.4 |
| 8 | Ölçüm olmadan "hızlı" deme | `ssd-dogrulama.md` §2 |

### §8.3 Depolama → ses zinciri (sınır)

| # | Aşama | Sahip | Not |
|---|---|---|---|
| 1 | Bayt (SSD sayfası) | **ssd** (bu konu) | §4 |
| 2 | Dosya sistemi + fsync | K000 | `isletim-sistemi-core-kapsam.md` |
| 3 | Uygulama okuma + ön yükleme | K014-media · K003-audio | 512 örnek = 10,667 ms |
| 4 | DSP / 32-bit float | K003-audio · K004-dsp | ADR-017 |
| 5 | ASIO/WASAPI çıkış kuyruğu | `[[output-kapsam]]` | 512 / 48 kHz |
| 6 | I2S/TDM → DAC | `[[pcm3168a-codec]]` · `[[ak4458-dac]]` | **Yalnız PCM3168A**; PCM5122 yasak (ADR-038 H001) |
| 7 | Analog → amplifikatör | `[[tpa3255-amplifier]]` (karar girdisi) | **Class AB sabit · Class D yasak** |
| 8 | Hoparlör | fiziksel | SSD'de mekanik ses **yok** → dinleme odası avantajı |

## §9 Dayanıklılık Hesabı (TBW ↔ DWPD)

### §9.1 Formüller (aritmetik)

| # | Tanım | Formül |
|---|---|---|
| 1 | TBW (toplam yazılabilir terabayt) | `TBW = kapasite(TB) × DWPD × 365 × garanti(yıl)` |
| 2 | Tersinden: DWPD | `DWPD = TBW ÷ (kapasite × 365 × garanti)` |
| 3 | Günlük yazma bütçesi | `günlük yazma(TB) = TBW ÷ (365 × garanti)` |
| 4 | Ömür (yıl) | `yıl = TBW ÷ (kapasite × 365 × günlük yazma(TB))` |

### §9.2 Hesap tablosu (örnek senaryolar — **uydurma ürün değildir**)

| # | Kapasite | Garanti | Günlük yazma | Hesaplanan TBW | Yorum |
|---|---|---|---|---|---|
| 1 | 1 TB | 5 yıl | 0,1 TB/gün | 1×0,1×365×5 = **182,5 TBW** | ılımlı iş |
| 2 | 1 TB | 5 yıl | 0,5 TB/gün | 1×0,5×365×5 = **912,5 TBW** | yoğun iş |
| 3 | 1 TB | 5 yıl | 1,0 TB/gün | **1.825 TBW** | çok yoğun |
| 4 | 2 TB | 5 yıl | 0,25 TB/gün | 2×0,25×365×5 = **912,5 TBW** | — |
| 5 | 500 GB | 3 yıl | 0,1 TB/gün | 0,5×0,1×365×3 = **54,75 TBW** | küçük disk |
| 6 | 4 TB | 5 yıl | 0,5 TB/gün | 4×0,5×365×5 = **3.650 TBW** | — |

**Okuma kuralı:** Tablo, **gereken** dayanıklılığı hesaplar. Ürünün **sahip
olduğu** TBW değeri üretici sayfasından alınır ve karşılaştırılır → iki sayı
karşılaştırılmadan "uygun" kararı **verilemez** (Q-SK1 · ⚠️).

### §9.3 Okuma-öncelikli iş yükünde gerçek ömür

| # | Gözlem | Sonuç |
|---|---|---|
| 1 | Okuma yazma ömrünü **tükmez** | Ömür yazmayla ilgilidir |
| 2 | Boot/system yazmaları sınırlıdır | Günlük TB yazım gerçekleşmez |
| 3 | İndeks/günlük yazmaları asıl yük | §8.1/7 — ayrı diske taşınırsa düşer |
| 4 | Kütüphane ekleme seyrektir | Sıralı yazma, WAF düşük |
| 5 | Sonuç | Okuma-öncelikli kütüphane SSD'si **düşük TBW ihtiyacı** üretir ⚠️ ölçüm gerekir |

### §9.4 Ömür iddiası - ölçüm ve kayıt zinciri

| # | Adım | Çıktı | Kanıt |
|---|---|---|---|
| 1 | Üreticinin TBW/DWPD değerini kaydet | sayı + kaynak URL | üretici sayfası (erişilebilirlik ⚠️; içerik okunmadı) |
| 2 | Günlük yazma profilini ölç | GB/gün | `ssd-dogrulama.md` §2 |
| 3 | İhtiyacı hesapla (§9.1 formülü) | TBW | saf aritmetik |
| 4 | İhtiyaç ≤ ürün TBW ise kararı yaz | karar kaydı | Q-SK3 üst karar |
| 5 | Aylık sağlık sayacını izle | trend eğrisi | `ssd-dogrulama.md` §4 |
| 6 | Diske değişiminde tekrarla | yeni kayıt | aynı zincir |

> Zincirin 1. ve 2. halkası olmadan "uygun/uygun değil" kararı **verilemez**;
> katalogdaki ya da satıcı sayfasındaki tek sayı, cihazın **çalışma rejimi**
> altındaki ömrü kanıtlamaz. Üretici TBW değeri ürünün üst sınır iddiasıdır,
> CoreMusic iş yükünün ölçümü değildir (Q-SK1 · Q-SK3 · ⚠️).

## §10 Boot Diski Seçimi

### §10.1 Kriterler

| # | Kriter | Neden | Öncelik |
|---|---|---|---|
| 1 | Güvenilirlik (marka/model + garanti) | Boot arızası = sistem durur | en yüksek |
| 2 | Yeterli kapasite + doluluk payı | §5.4 %80 kuralı | yüksek |
| 3 | Ayrık disk (kütüphane ile ayrık) | I/O çakışması (§8.2/3) | yüksek |
| 4 | Boot yedeği (imaj/kurtarma) | Yeniden kurulum süresi | yüksek |
| 5 | Arayüz uyumu (NVMe/SATA) | Soket/BIOS | orta |
| 6 | Sıcaklık konumu | Yanıcı/raporlanan sıcaklık | orta (`ssd-dogrulama` §5) |
| 7 | Hız | Açılış süresi | düşük-orta |
| 8 | Fiyat/GB | Bütçe | düşük |

### §10.2 Önerilen yerleşim (kapsam düzeyi — envanter yok)

| # | Disk | İçerik | Gerekçe |
|---|---|---|---|
| 1 | Boot SSD | İşletim sistemi + uygulamalar | Güvenilirlik + onarım |
| 2 | Kütüphane SSD (ops.) | Sıcak katman + indeks | Rastgele okuma |
| 3 | HDD | Ana koleksiyon + yedek hedefi | Kapasite (`hdd-kapsam.md` §6.3) |
| 4 | Uzak ortam | Uzak yedek | 3-2-1 (`hdd-kapsam.md` §7.2) |

> ⚠️ VERIFICATION REQUIRED: Mevcut makinede kaç disk var, hangi görevde —
> **UNKNOWN** (Q-SK1 · Q-SK2). Bu tablo öneri iskeletidir, envanter değildir.

### §10.3 Boot diski kurtarma hazırlığı

| # | Adım | Çıktı |
|---|---|---|
| 1 | Sistem imajı alma takvimi | tarihli imaj |
| 2 | Kurtarma ortamı (USB/disk) | boot edilebilir ortam |
| 3 | Ayar/ürün anahtarı kaydı | list |
| 4 | Geri dönüş tatbikatı | süre + sonuç |
| 5 | Yedekleme görevine bağlama | `hdd-dogrulama.md` §13.1 |

## §11 Riskler

| # | Risk | Etki | Olasılık | Azaltma |
|---|---|---|---|---|
| 1 | Envanter bilinmeden seçim | yüksek | **aktif** | Q-SK1 |
| 2 | Diski %90 doldurma → yavaşlama | orta | orta | §5.4 |
| 3 | Küçük yazmaları ana diske yığmak | orta | yüksek | §8.2/2 ayrı disk |
| 4 | TRIM verilmemesi | orta | düşük | `ssd-dogrulama.md` §3 |
| 5 | Boot ile kütüphaneyi aynı diske koymak | orta | yüksek | §10.2 |
| 6 | Boot yedeği olmaması | kritik | orta | §10.3 |
| 7 | Sıcaklık görmezden gelinmesi | orta | orta | `ssd-dogrulama.md` §5 |
| 8 | QLC'yi yoğun yazma işine koymak | orta | orta | §4.3/4 |
| 9 | "Ömür bitti" sürprizi | yüksek | düşük | §9 izleme |
| 10 | Ölçümsüz performans iddiası | orta | yüksek | `ssd-dogrulama.md` §2 |

## §12 Uygulama Sırası (checklist)

| # | Adım | Çıktı | Kapı |
|---|---|---|---|
| 1 | Envanteri çıkar (marka/model/arayüz/kapasite) | liste | Q-SK1 |
| 2 | İş yükü profilini yaz (§8.1 satırları) | profil | kapsam |
| 3 | Yerleşimi belirle (§10.2) | şema | Q-SK2 |
| 4 | TBW ihtiyacını hesapla (§9.1-§9.2) | tablo | Q-SK3 |
| 5 | Doluluk bütçesi yaz (§5.4) | eşik | — |
| 6 | TRIM zincirini doğrula | kanıt | `ssd-dogrulama.md` §3 |
| 7 | Hız/erişim ölçümü | rapor | `ssd-dogrulama.md` §2 |
| 8 | Sıcaklık ölçümü | rapor | `ssd-dogrulama.md` §5 |
| 9 | Boot kurtarma tatbikatı | kayıt | §10.3 |
| 10 | Örneklerle dene | kayıt | `ssd-ornekler.md` |

## §13 Sık Yapılan Hatalar

| # | Hata | Belirti | Doğrusu |
|---|---|---|---|
| 1 | "SSD hızlı" deyip ölçmemek | Kanıtsız iddia | `ssd-dogrulama.md` §2 |
| 2 | Boot + kütüphane + log tek disk | I/O çakışması + WAF | §8.2/2-3 |
| 3 | Diski tam doldurmak | Yazma çöküşü | §5.4 |
| 4 | TRIM'i "var" varsaymak | Zamanla yavaşlama | `ssd-dogrulama.md` §3 |
| 5 | TBW'yi üretim ömrü sanmak | Yanlış ömür hesabı | §9.1 |
| 6 | QLC'yi yoğun yazmada kullanmak | Hız/ömür düşüşü | §4.3 |
| 7 | SATA üst sınırını NVMe sanmak | Bütçe hatası | §7.1/5 |
| 8 | HDD'yi rastgele işte tutmak | Atlanan kare | `hdd-kapsam.md` §6.2 |
| 9 | Boot yedeği almamak | Yeniden kurulum | §10.3 |
| 10 | Yedeği aynı SSD'de tutmak | 3-2-1 ihlali | `hdd-kapsam.md` §7.2 |

## §14 Terim Sözlüğü

| # | Terim | Tanım |
|---|---|---|
| 1 | Cell | Hücre — yük tutan birim |
| 2 | Page | Sayfa — okuma/yazma birimi |
| 3 | Block | Blok — silme birimi |
| 4 | SLC/MLC/TLC/QLC | Hücre başına 1/2/3/4 bit |
| 5 | P/E cycle | Program-silme döngüsü |
| 6 | ECC | Hata düzeltme kodu |
| 7 | WAF | Yazma çarpanı |
| 8 | FTL | Flash çeviri katmanı (adres eşleme) |
| 9 | GC | Çöp toplama — blok taşıma |
| 10 | TRIM | "silindi" bildirimi |
| 11 | UNMAP / discard | Alt katmanda silme bildirimi |
| 12 | SLC cache | Hız için ayrılan SLC alanı |
| 13 | NVMe | SSD için tasarlanmış komut protokolü |
| 14 | AHCI | Eski SATA komut arayüzü |
| 15 | TBW | Toplam yazılabilir terabayt |
| 16 | DWPD | Günlük diske yazma (kapasitenin kaç katı) |
| 17 | Endurance | Dayanıklılık |
| 18 | Read-priority | Okuma öncelikli iş yükü |
| 19 | Boot disk | Açılış diski |
| 20 | Hot path | Gerçek zamanlı yol |

## §15 Kapsam Dışı (özet)

| # | Konu | Sahip |
|---|---|---|
| 1 | Dosya sistemi/TRIM çağrısı | K000-isletim-sistemi |
| 2 | Sürücü/NVMe katmanı | K000-isletim-sistemi · donanım-kernel |
| 3 | SSD firmware (FTL/GC) | K005-firmware |
| 4 | HDD tarafı | `hdd-kapsam.md` |
| 5 | Ölçüm uygulaması | `ssd-dogrulama.md` |
| 6 | Uygulama örnekleri | `ssd-ornekler.md` |
| 7 | Envanter | K001 Q4 (Q-SK1) |
| 8 | Ses zinciri | K003-audio · K001 `audio/cipler/*` |

## §16 Kaynakça (bu dosyanın web kaynakları)

| # | Kaynak | URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | Crucial destek | https://www.crucial.com/support | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| 2 | Samsung Semiconductor SSD | https://semiconductor.samsung.com/ssd/ | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| 3 | Vault: ssd düğüm künyesi | `ssd/index.md` (47 satır) | 2026-10-12 | ✅ okundu |
| 4 | Vault: üst kapsam | `donanaım-type-kapsam.md` §2/A3 · §6 | 2026-10-13 | ✅ okundu |

**Uydurma kaynak yok:** 2 web URL'si bu oturumda HEAD ile 200 döndürülerek
doğrulanmıştır; içerik okunmadığı için içerik iddiaları ⚠️ işaretlidir.

## §17 Çapraz Referanslar

| # | Hedef | İlişki |
|---|---|---|
| 1 | `[[ssd/index]]` | Bu konunun künyesi |
| 2 | `[[hdd/index]]` · `[[hdd-kapsam]]` | Kardeş depolama sınıfı (mekanik) |
| 3 | `[[donanaım-type-kapsam]]` | Üst kapsam · envanter UNKNOWN |
| 4 | `[[donanaım-type-dogrulama]]` | Üst doğrulama tablosu |
| 5 | `[[donanaım-type/index]]` | Üst künye · children |
| 6 | `[[pcm3168a-codec]]` | DAC/codec — zincir 6. aşama |
| 7 | `[[ak4458-dac]]` | DAC — zincir 6. aşama |
| 8 | `[[tpa3255-amplifier]]` | Amfi kaydı (Class AB sabit) |
| 9 | `[[output-kapsam]]` | Çıkış tamponu sahibi (512/48k) |
| 10 | `[[isletim-sistemi-core-kapsam]]` | Dosya sistemi / TRIM sınırı |
| 11 | `ssd-dogrulama.md` | Ölçüm kapıları (hız · TRIM · sağlık · sıcaklık) |
| 12 | `ssd-ornekler.md` | İndeksleme · cache · sağlamlık izleme örnekleri |

---

## §18 Doğrulama Kapılarına Devir

| # | Bu dosyadaki iddia | Kanıt tipi | Gittiği dosya | Seviye |
|---|---|---|---|---|
| 1 | SATA ≈ 600 MB/s tavan | aritmetik (8b/10b) | `ssd-dogrulama.md` §2 | S2 |
| 2 | TBW formülü ve örnekler | aritmetik | `ssd-dogrulama.md` §1 | S2 |
| 3 | 512 örnek = 10,667 ms | aritmetik | `ssd-dogrulama.md` §1 | S2 |
| 4 | SLC/MLC/TLC/QLC bit oranı | tanım | `ssd-dogrulama.md` §1 | S2 |
| 5 | P/E döngü sayıları | üretici datasheet | ❌ yok → ⚠️ | — |
| 6 | Pratik okuma/yazma hızları | ölçüm | `ssd-dogrulama.md` §2 | S1 → açık |
| 7 | TRIM gerçekten veriliyor | ölçüm/kanıt | `ssd-dogrulama.md` §3 | S1 → açık |
| 8 | Sağlık sayaçları | okuma | `ssd-dogrulama.md` §4 | S1 → açık |
| 9 | Sıcaklık | ölçüm | `ssd-dogrulama.md` §5 | S1 → açık |
| 10 | Envanter | insan girdisi | `ssd-dogrulama.md` §6 | ❌ UNKNOWN |

## §19 Dürüstlük Bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler yalnız doğrulanmış
kaynaklar içerir; doğrulanamayan iddialar ⚠️ VERIFICATION REQUIRED ile
işaretlidir. Sayısal ömür/süreç değerleri **aralık** olarak verilmiş, ürün
iddiası yapılmamıştır; tüm hesaplar açık formülle gösterilmiştir.

---

## §20 OS × DİL ERİŞİM MATRİSİ (depolama — SSD)

> Bu bölüm `donanım-platformlararasi-kod-ornekleri.md` §PLATFORM MATRİSİ (P1–P6 · G1–G11)
> üzerine kuruludur; **yeni URL eklenmemiştir.** Hücreler "OS algılama → SSD/depolama
> erişim yolu" biçimindedir; doğrulanmamış dil-OS bağları hücre içinde `⚠️` ile işaretlidir.
> Bölüm yalnız erişim yolu tanımlar — ölçüm/kanıt iddiası taşımaz (§19 geçerlidir).

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

**Öneri (repo dili PHP/JS):** SSD kök/bölüm tespiti PHP'de `disk_total_space` aday
taraması (§G7.2), JS/Node'da `process.platform` + `/Volumes` / `/proc/mounts` okuma ile
başlar. Windows'ta fiziksel disk/seri ayrıntısı PHP'den doğrudan erişilemez — çocuk
süreç (PowerShell `DriveInfo`) şablonuna devredilir; TRIM/NVMe sayaç okuması
sysfs (§P4) veya Çİ katmanındadır ve **bu hücreler ⚠️ ile işaretlidir.**

**Risk/uyumluluk (korunan ⚠️ kayıtları):**
- TRIM/NVMe sayaç yolları (ömrü/TBW/health sayaçları) bu oturumda **doğrulanmadı** →
  ⚠️ VERIFICATION REQUIRED; §6/§15 sınırı gereği sayaç okuması K000/K005'e devredilir
  ve bu matriste kesin yol olarak yazılmaz (Q-SK1).
- `__APPLE__` ön-tanımlı makrosu için doğrulanmış URL yok (§K4/R4); ortak ⚠️ kayıtları —
  macOS `sysctl -n hw.memsize` 404 (R1) ve `getifaddrs` 404 (R8) — silinmez; SSD erişimi
  bellek/ağ yolunu kullanmaz, kayıt korunur.
- Git Bash/MSYS2 `uname -s` MINGW öneki doğrulanamadı (§G8.1) · Go std'de disk kök
  listeleme yok (§G4.3) · Rust sarmalayıcı ADR bekliyor (§G5.3) · Swift/Kotlin dil
  bağları doğrulanmadı (§G8.4 · §G9 · §G10) → bu hücreler şablondur.
- Sayaç/ölçüm sonucu donanım kanıtı sayılmaz; erişim yolu şablonudur (§19).

Kapsamlı şablonlar: `donanım-platformlararasi-kod-ornekleri.md` §G1–§G11 · tam matris: `raporlar/K001-donanım-os-dil-matrisi.md`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
