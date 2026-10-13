---
node_id: ssd-ornekler
node_type: module
status: draft
parent_id: K001-donanım/donanaım-type/ssd
children: []
depends_on:
  - ssd-kapsam
  - ssd-dogrulama
evidence:
  - "https://nvmexpress.org/ (HTTP 200, 2026-10-13)"
  - "https://www.micron.com/support (HTTP 200, 2026-10-13)"
  - "Repo taraması 2026-10-13: *.cpp = 0 · *.h = 0 · CMakeLists.txt = 0 (gerçek kod YOK)"
validation: "draft - 2026-10-13 yazıldı → ≥500 satır ölçümü yazım sonrası → örnekler adım şablonudur, gerçek repo kodu YOK"
open_questions:
  - "Q-SK1: SSD envanteri bilinmiyor - UNKNOWN"
  - "Q-SE1: Kütüphane indeksleme kodu henüz yazılmadı (Neva Engine PLANNED) - adım şablonu"
  - "Q-SE2: Sağlık izleme görevi için OS/cihaz yüzeyi seçimi belirsiz - K000 sınırı"
group: kod
---

# ssd-ornekler - Katı Hal Disk Uygulama Örnekleri (adım şablonları)

> **Önemli:** Bu dosyadaki örnekler **adım şablonudur** — gerçek repo kodu
> **değildir**. §1'de repo tarama kanıtı vardır: üretim kodu henüz yok.

## §1 Gerçek repo kodu (ÖNCELİK 1)

### §1.1 Tarama kanıtı (2026-10-13, PowerShell repo taraması)

| # | Aranan | Sonuç |
|---|---|---|
| 1 | `*.cpp` | **0 dosya** |
| 2 | `*.h` | **0 dosya** |
| 3 | `CMakeLists.txt` | **0 dosya** |
| 4 | `NevaEngine/electronic` | **YOK → PLANNED** |

**Sonuç:** Bu konuda **çalışan tek bir C++ satırı yoktur**. Bu dosyadaki her
örnek, kod yazılmadan önce **onaylanacak taslak adımlardır**; "repo'da var"
diye sunulamaz.

> ⚠️ VERIFICATION REQUIRED: Yukarıdaki sayılar 2026-10-13 taramasının
> çıktısıdır; repository değiştiyse yeniden taranmalıdır. `.ai/projects/NevaEngine/*.md`
> dizini varlık olarak bilinir ama **içerik okunmadı** (boş olabilir —
> bkz. `.ai/AGENTS.md` §24.3 Embedded satırı).

### §1.2 Bu dosyanın kapsamı

| # | Ne var | Ne YOK |
|---|---|---|
| 1 | İndeksleme adım şablonu (§2) | Çalışan indeksleyici kod |
| 2 | Önbellek stratejisi adımı (§3) | Çalışan önbellek kodu |
| 3 | Sağlık izleme adımı (§4) | Çalışan izleme görevi |
| 4 | Ölçüm → kod köprüsü (§5) | Ölçüm sonucu (S1 açık) |
| 5 | Şablon onay listesi (§8) | Onay kaydı |

## §2 Kütüphane İndeksleme Adım Şablonu (§8.1 iş yükü karşılığı)

### §2.1 Amaç ve kısıt

| # | Öğe | Değer |
|---|---|---|
| 1 | Amaç | Albüm/dosya listesini aranabilir bir indekse yazmak |
| 2 | İstek | Okuma-öncelikli diskte çalışır (`ssd-kapsam.md` §8) |
| 3 | Kısıt | Sıralı yazma tercih edilir (WAF düşürür — §5.2/1) |
| 4 | Kısıt | Küçük rastgele yazmalar indeks dosyasına yığılmamalı |
| 5 | Sınır | İndeks dosyasının yeri K000 dosya sistemi yüzeyinde |

### §2.2 Adım şablonu (pseudoadım — gerçek kod DEĞİL)

| # | Adım | Girdi | Çıktı | Not |
|---|---|---|---|---|
| 1 | Taranacak kökleri al | kullanıcı ayarı | dizin listesi | ayar yüzeyi K000/UI |
| 2 | Dosyaları sıralı tara | dizin listesi | yol + meta akışı | sıralı okuma — HDD'de de yeterli |
| 3 | Meta bilgiyi ayrıştır | yol | sanatçı/ albüm/ süre | biçim ≠ bu dosyanın konusu |
| 4 | İndeks kaydını **toplu** yaz | kaydbufesi | indeks dosyası | küçük yazmaları biriktir (§2.3) |
| 5 | İndeksi atomik değiştir | yeni dosya | tek "rename" | yarım kayıt riskini önler ⚠️ K000 |
| 6 | Son taranma zamanını yaz | saat | durum satırı | §9 kaydı ile birleşir |

### §2.3 Küçük-yazma biriktirme kuralı (adım şablonu)

| # | Kural | Neden | Karşılığı |
|---|---|---|---|
| 1 | Kayıtları bellekte topla, arada yaz | Her kayıtta fsync = rastgele yazma | `ssd-kapsam.md` §8.2/1 |
| 2 | Toplu yazma aralığını **ölçümle** seç | Küçük aralık = WAF yüksek | `ssd-dogrulama.md` §3 |
| 3 | İndeksi ana diskten ayır (mümkünse) | Boot I/O'su ile çakışmasın | `ssd-kapsam.md` §8.2/3 |
| 4 | Silinen albümlerin kaydını toplu temizle | Çöp satır birikimi | §5.4 doluluk kuralı |

> ⚠️ VERIFICATION REQUIRED: Bellek içi biriktirme **tampon boyutu** (kaç
> kayıt ya da kaç MB) bu oturumda **seçilmemiştir** — ölçüm (§3 kapı 1) ve
> K000 fsync davranışı okunmadan rakam **uydurulmaz**.

### §2.5 İndeks kaydı - örnek alan şeması (boş şablon)

| # | Alan | Tip (kavramsal) | Dolduruldu mu? |
|---|---|---|---|
| 1 | Dosya yolu | metin | (boş) |
| 2 | Sanatçı | metin | (boş) |
| 3 | Albüm | metin | (boş) |
| 4 | Süre (sn) | sayı | (boş) |
| 5 | Son çalınma zamanı | zaman damgası | (boş) |
| 6 | Çalınma sayacı | sayı | (boş) |
| 7 | Katman etiketi (sıcak/soğuk) | enum | (boş) |

**Not:** Şema **kavramsaldır** — gerçek indeks tasarımı K015-media
sınırındadır (§6/1); alan adları bu vault'ta **bağlayıcı değildir**.

### §2.6 Tarama performansı - kestirim tablosu (formül açık)

| # | Girdi | Formül | Çıktı |
|---|---|---|---|
| 1 | Koleksiyon boyutu (GB) + sıralı okuma (MB/s) | `süre(sn) = GB × 1024 ÷ MB/s` | tarama süresi |
| 2 | Dosya sayısı + ortalama dosya boyutu | `GB = adet × ortalama` | toplam okuma |
| 3 | İndeks yazma boyutu + 4K yazma (MB/s) | `süre(sn) = boyut ÷ hız` | yazma süresi |
| 4 | Toplam | 1 + 3 + ayrıştırma payı | tahmini toplam |

**Örnek sayı yok:** Girdi ölçüm yoksa (kapılar açık) tablo **boş** sayılır;
**örnek rakam uydurulmaz** — ölçüm `ssd-dogrulama.md` §9.2'den gelir.

### §2.7 İndeksleme - kapı karşılıkları

| # | Şablon adımı | Karşılığı olan kapı |
|---|---|---|
| 1 | Sıralı okuma kestirimi (§2.6) | `ssd-dogrulama.md` §3.3/1-3 |
| 2 | Toplu yazma aralığı (§2.3/2) | `ssd-dogrulama.md` §3.2 adım 5 |
| 3 | İndeks dosyası silme/temizlik | `ssd-dogrulama.md` §4 TRIM kapısı |
| 4 | Doluluk denetimi (§3.2/4) | `ssd-dogrulama.md` §9.1 |

### §2.4 İndeksleme - beklenen ölçüm girdisi

| # | Ölçüm | Nereden | Kullanım |
|---|---|---|---|
| 1 | Sıralı okuma hızı | `ssd-dogrulama.md` §9.2 | Tarama süresi kestirimi |
| 2 | 4K yazma hızı | `ssd-dogrulama.md` §9.2 | Toplu yazma aralığı |
| 3 | Doluluk % | `ssd-dogrulama.md` §9.1 | §5.4 eşiği |

## §3 Önbellek Stratejisi Adım Şablonu (§8.2/4 sıcak katman karşılığı)

### §3.1 Sıcak katman tanımı (adım)

| # | Katman | İçerik | Yer |
|---|---|---|---|
| 1 | Sıcak | Son çalınan albümler + oynatma listeleri | SSD (`ssd-kapsam.md` §10.2/2) |
| 2 | Soğuk | Arşiv koleksiyonu | HDD (`hdd-kapsam.md` §6.3) |
| 3 | İndeks | Taranabilir metadata | İndeks sahibi katman (§2) |

### §3.2 Promosyon/demisyon adımları (adım şablonu)

| # | Adım | Tetik | Eylem |
|---|---|---|---|
| 1 | İzle | Bir parça çalındı | sayaç güncelle (bellek içi) |
| 2 | Promosyon eşiği | Sayaç eşikte | HDD → SSD kopya (sıralı yazma) |
| 3 | Demisyon eşiği | SSD doluluk/yaş sınırı | SSD → HDD taşı ya da sil |
| 4 | Denetim | Periyot | Doluluk % oku → §5.4 |
| 5 | Kayıt | Her promosyon/demisyon | §9.4 günlük satırı |

### §3.3 Eşikler - durum tablosu

| # | Eşik | Değer | Durum |
|---|---|---|---|
| 1 | Promosyon (kaç kez çalınma) | **seçilmedi** | ⚠️ üst karar / ölçüm |
| 2 | Demisyon (yaş/gün) | **seçilmedi** | ⚠️ üst karar |
| 3 | SSD doluluk tavanı | %80 (kapsam kuralı) | ✅ `ssd-kapsam.md` §5.4 |
| 4 | Hot-path gecikme hedefi | 512 örnek / 48 kHz = 10,667 ms (aritmetik) | ✅ S2; ölçüm PLANNED |

**Gecikme aritmetiği (S2 — açık formül):**

```
512 örnek ÷ 48000 Hz = 0,010666... s = 10,667 ms
```

Bu süre, ön-yükleme tamponunun **doldurulması için azami pencere**dir;
depolama katmanı bu pencere içinde veriyi sunamazsa kesilme riski doğar
(çıkış sahibi: `[[output-kapsam]]` · ölçüm PLANNED).

### §3.5 Sıcak katman bütçesi (adım şablonu)

| # | Bütçe satırı | Girdi | Durum |
|---|---|---|---|
| 1 | SSD kapasitesi | envanter | ❓ UNKNOWN (Q-SK1) |
| 2 | Boot + sistem payı | envanter | ❓ UNKNOWN (Q-SK2) |
| 3 | Kullanılabilir sıcak katman | 1 − 2 − %20 pay | hesaplanamaz (girdi yok) |
| 4 | Kaç albüm sığar | 3 ÷ ortalama albüm boyutu | ölçüm gerekir |
| 5 | Promosyon oranı | çalma istatistiği | veri toplanmadı |

**Kural:** Bütçe tablosu **boş girdiyle doldurulamaz** — kapasite rakamı
envanter gelmeden uydurulmaz; doluluk tavanı (%80) tek bağlayıcı sayıdır
(`ssd-kapsam.md` §5.4).

### §3.6 Önbellek - kapı karşılıkları

| # | Şablon adımı | Kapı |
|---|---|---|
| 1 | Doluluk denetimi | `ssd-dogrulama.md` §9.1 |
| 2 | Promosyon yazma sırası (sıralı mı?) | `ssd-dogrulama.md` §3.3/1-2 |
| 3 | Taşıma sonrası hız trendi | `ssd-dogrulama.md` §4.5 |
| 4 | SSD sıcaklığı (çoğalan yazma) | `ssd-dogrulama.md` §6 |

### §3.7 Önbellek - karar kaydı (boş şablon)

| Tarih | Karar (eşik) | Gerekçe (ölçüm/üst karar) | İmza |
|---|---|---|---|
| (boş) | promosyon eşiği: (boş) | (boş) | (boş) |
| (boş) | demisyon eşiği: (boş) | (boş) | (boş) |

**Kural:** Eşik satırı **gerekçesiz** doldurulursa geçersizdir; gerekçe
yalnız "ölçüm" (araç çıktısı) ya da "üst karar" olabilir.

### §3.4 Önbellek - sık yapılan hatalar

| # | Hata | Belirti | Doğrusu |
|---|---|---|---|
| 1 | Eşikleri ölçmeden seçmek | Sürekli kopyalama (thrash) | §3.3/1-2 ölçüm + üst karar |
| 2 | İndeksi önbelleğe almayı unutmak | Her tarama HDD'ye iner | §2 ile birlikte tasarla |
| 3 | Doluluğu ihmal | %90 → yavaşlama | §5.4 denetimi (§3.2/4) |
| 4 | Taşımayı senkron yapmak | Çalma sırasında gecikme | arka plan kuyruğu ⚠️ K000 |

## §4 Sağlık İzleme Adım Şablonu (§5 kapı 3 karşılığı)

### §4.1 Adım şablonu (pseudoadım — gerçek görev DEĞİL)

| # | Adım | Frekans | Çıktı |
|---|---|---|---|
| 1 | Okuma noktasını belirle | kurulum | OS/aracın sağlık arayüzü (⚠️ K000) |
| 2 | Baseline okuması al | ilk gün | §9.3 "ilk okuma" satırı |
| 3 | Zamanlanmış okuma | aylık (§5.3/2) | ikinci okuma satırı |
| 4 | İki okumayı karşılaştır | her okuma | trend: stabil / RED |
| 5 | RED ise hız ölçümü tetikle | olay | `ssd-dogrulama.md` §3 formu |
| 6 | Kaydı append et | her okuma | §15 kapı geçmişi |

### §4.2 Karar durumları

| # | Gözlem | Durum | Eylem |
|---|---|---|---|
| 1 | Sayılar sabit | stabil | Kapı 3 için ikinci okuma sayılır |
| 2 | Hata sayacı arttı | RED | `ssd-dogrulama.md` §5.2/2 — yedek al + incele |
| 3 | Toplam yazılan hızlanıyor | anomali | Yazma kaynağını bul (§2/§3 promosyon mu?) |
| 4 | Okuma başarısız | KRİTİK | Dur + yedek + üst karar |

> ⚠️ VERIFICATION REQUIRED: Sağlık okuma **arayüzü** bu oturumda
> **seçilmedi** (Windows WMI/araç yüzeyi K000-K016 sınırında); somut
> arayüz adı ve komutu **araştırılıp doğrulanmadan yazılmaz**.

### §4.3 İzleme - kayıt alanı (boş şablon)

| Tarih | Sağlık | Toplam yazılan | Hata sayacarı | Not |
|---|---|---|---|---|
| (boş) | (boş) | (boş) | (boş) | (boş) |
| (boş) | (boş) | (boş) | (boş) | (boş) |

**Kural:** İki satır olmadan §4.2/1 kararı verilemez (tek okuma trend
yapamaz — `ssd-dogrulama.md` §5.2).

### §4.4 İzleme görevi - çalışma koşulları (adım şablonu)

| # | Koşul | Değer | Not |
|---|---|---|---|
| 1 | Zamanlayıcı frekansı | aylık (§5.3/2) | Çakışmaz ölçüm penceresi |
| 2 | Görev çalışma yüzeyi | OS zamanlayıcı (K000-K016 sınırı) | Somut arayüz ⚠️ araştırılacak |
| 3 | Kayıt yeri | bu dosya §4.3 + kapı geçmişi §15 | Append-only |
| 4 | Uyarı yolu | insan gözden geçirme | Otomatik aksiyon YOK (fail-safe) |
| 5 | RED durumu | yedek alma önceliklensin | `hdd-kapsam.md` §7.2 |

### §4.5 İzleme - RED sonrası akış

| # | Adım | Eylem | Hedef |
|---|---|---|---|
| 1 | Durumu dondur | Yeni büyük yazmaları durdur | Veri kaybı riski azalt |
| 2 | Yedek al | Güncel yedek doğrula | `hdd-dogrulama.md` §6 |
| 3 | Hız ölç | Performans düşüşü var mı? | `ssd-dogrulama.md` §3 |
| 4 | Kaydet | Kapı geçmişi RED satırı | `ssd-dogrulama.md` §15 |
| 5 | Üst karar | Değişim/oneri | Q-SK1/Q-SK3 |

### §4.6 İzleme - sahte-olumlu önlemleri

| # | Sahte işaret | Neden | Önlem |
|---|---|---|---|
| 1 | Tek okuma "yükseldi" | Ölçüm gürültüsü | İki okuma + aynı koşul (§4.3) |
| 2 | Araç değişimi | Farklı atama adları | Aynı araç + sürüm kaydı |
| 3 | Farklı disk | Envanter karışması | §9.1 koşul satırı zorunlu |
| 4 | Kopyalama dönemi | Toplam yazılan sıçrar | Normal iş günü oku (§5.3 notu) |

## §5 Ölçüm → Kod Köprüsü (şablonların beslendiği yer)

| # | Ölçüm (ssd-dogrulama) | Beslediği şablon | Hangi karar |
|---|---|---|---|
| 1 | §9.2 SEQ/4K medyanları | §2.4 | Tarama/bulk-write aralığı |
| 2 | §9.1 doluluk | §3.2/4 | Demisyon/denetim |
| 3 | §9.3 sağlık trendi | §4 | Kapı 3 kapanışı |
| 4 | §9.2 + §6 sıcaklık | §3.2 | Konum/havalandırma kararı |
| 5 | Yok (kapılar açık) | tümü | **Kod yazımı BAŞLATILAMAZ** |

**Köprü kuralı:** Şablon → kod dönüşümü, `ssd-dogrulama.md` kapıları
**ölçümle kapandıktan sonra** düşünülür; ölçüm yoksa kod yazmak eşikleri
uydurmak demektir (Zero-Hallucination).

### §5.1 Ölçüm girdisi - bekleme listesi

| # | Girdi | Kapı | Beklenen | Durum |
|---|---|---|---|---|
| 1 | SEQ okuma medyanı | §3 | MB/s sayısı | 🔴 bekliyor |
| 2 | 4K yazma medyanı | §3 | MB/s sayısı | 🔴 bekliyor |
| 3 | Doluluk % | §9.1 | sayı | 🔴 bekliyor |
| 4 | Sağlık baseline | §5.3 | 2 satır | 🔴 bekliyor |
| 5 | Sıcaklık (boşta/yük) | §6 | °C | 🔴 bekliyor |
| 6 | Envanter | §9.1 | marka/model | ❓ Q-SK1 |

### §5.2 Şablon → kod dönüşüm ön koşulları

| # | Ön koşul | Kanıt | Yoksa |
|---|---|---|---|
| 1 | Kapı 1 (hız) kapandı | §15 tarihli satır | DUR |
| 2 | Kapı 3 (sağlık) kapandı | 2 okuma kaydı | DUR |
| 3 | Eşikler kararlaştı | §3.7 karar satırı | DUR |
| 4 | Repo kodu mevcut | §1 tarama ≠ 0 | Şu an **DUR** (kod 0) |
| 5 | Üst onay | kural 13 (refactor/bağımlılık → dur + handover) | DUR |

### §5.3 Köprü - güncelleme sıklığı

| # | Olay | Köprü günceller |
|---|---|---|
| 1 | Yeni ölçüm | §5.1 satırı (bekliyor → sayı) |
| 2 | Kapı kapanışı | §15 + §5.2/1-2 |
| 3 | Eşik kararı | §3.7 |
| 4 | Envanter gelmesi | §2.1/§3.1 girdileri |
| 5 | Repo kodu oluşması | §1.1 tarama yenile |

### §5.5 Resmi Kaynak Erişilebilirlik Kanıtı (bu dosya)

| # | URL | Amaç | Test | Sonuç |
|---|---|---|---|---|
| 1 | `https://nvmexpress.org/` | NVMe ana spesifikasyon kaynağı | HEAD 200 (2026-10-13) | ✅ erişilebilir |
| 2 | `https://www.micron.com/support` | Üretici destek (indeks/sağlık uygulamaları için) | HEAD 200 (2026-10-13) | ✅ erişilebilir |

**Sınır:** HEAD 200 yalnızca sayfanın **var olduğunu** kanıtlar; sayfa
içeriği **okunmadığı için** bu dosyadaki hiçbir adım şablonu kaynak
içeriğinden türetilmiştir denemez. Somut NVMe health komut adları için
içerik okunması gerekir → ⚠️ VERIFICATION REQUIRED (`ssd-dogrulama.md` §8.4/3).

### §5.6 Kaynak okuma görev listesi (append-only)

| # | URL | Ne okunacak | Hangi şablona girecek | Durum |
|---|---|---|---|---|
| 1 | nvmexpress.org | Health/smart okuma tanımı | §4.1 adım 1 | ⏳ açık |
| 2 | micron.com/support | Üretici izleme araçları | §4.1 adım 1 | ⏳ açık |
| 3 | crystalmark.info | Ölçüm aracı ayarları | `ssd-dogrulama.md` §3.1 | ⏳ açık |
| 4 | crucial.com (firmware) | Firmware adımları | `ssd-dogrulama.md` §7 | ⏳ açık |

**Kural:** Satır yalnız "okundu" yapılarak kapanır; kapanan satıra tarih +
hangi bilginin alındığı yazılır (append-only, §9 ilkesi).

## §6 Kapsam Dışı

| # | Konu | Sahip |
|---|---|---|
| 1 | İndeks dosya biçimi tasarımı | K015-media (medya katmanı) ⚠️ registry'de teyit gerek |
| 2 | Dosya sistemi seçimi / fsync | K000-isletim-sistemi |
| 3 | TRIM komutu üretimi | K000 (`isletim-sistemi-core-kapsam.md` §A.2) |
| 4 | FTL/GC firmware | K005-firmware |
| 5 | Ölçüm uygulaması (araç) | `ssd-dogrulama.md` §3 |
| 6 | HDD örnekleri (tarama/streaming/rotasyon) | `hdd-ornekler.md` §2-§4 |

## §7 Terim Sözlüğü

| # | Terim | Tanım |
|---|---|---|
| 1 | Adım şablonu | Kod olmayan, uygulanacak sıra listesi |
| 2 | Pseudoadım | Gerçek API'siz, kavramal adım |
| 3 | İndeks | Aranabilir metadata kaydı |
| 4 | Sıcak katman | SSD'de tutulan sık erişimli küme |
| 5 | Promosyon | Soğuk → sıcak taşıma |
| 6 | Demisyon | Sıcak → soğuk taşıma |
| 7 | Thrash | Eşik uygunsuzluğu → sürekli kopyalama |
| 8 | Toplu yazma | Küçük kayıtları biriktirip tek seferde yazma |
| 9 | Atomik değiştir | Eski dosyayı yenisinin tek adımda yerine koyma |
| 10 | Baseline | İlk okuma referansı |
| 11 | Trend | İki okuma arası değişim |
| 12 | Hot path | Gerçek zamanlı yol (512/48k penceresi) |
| 13 | S2 | Aritmetik kanıt seviyesi (`ssd-dogrulama.md` §2) |
| 14 | Kapı | Doğrulama geçme koşulu |

### §7.1 Ek terimler (uygulama dili)

| # | Terim | Tanım |
|---|---|---|
| 15 | Append-only defter | Yalnız ekleme yapılan kayıt tablosu (§9) |
| 16 | Baseline okuma | İlk sağlık referansı (§4.1/2) |
| 17 | fail-safe | Hata halinde güvenli taraf (otomatik aksiyon yok) |
| 18 | Karar satırı | Eşik + gerekçe + imza üçlüsü (§3.7) |
| 19 | Kapı geçmişi | `ssd-dogrulama.md` §15 append tablosu |
| 20 | Şablon envanteri | §10.4 — hangi adım hangi girdiyi bekliyor |

## §8 Çapraz Referanslar

| # | Hedef | İlişki |
|---|---|---|
| 1 | `[[ssd/index]]` | Bu konunun kütüğü |
| 2 | `ssd-kapsam.md` | §8/§9/§10 iddialarının sahibi |
| 3 | `ssd-dogrulama.md` | Ölçüm kapıları (bu dosyanın girdisi) |
| 4 | `[[hdd-ornekler]]` | Kardeş örnekler (tarama/rotasyon — mekanik disk) |
| 5 | `[[hdd-kapsam]]` | Soğuk katman / arşiv sahibi (§6.3) |
| 6 | `[[output-kapsam]]` | 512/48k tampon sahibi (§3.3 aritmetik) |
| 7 | `[[isletim-sistemi-core-kapsam]]` | Dosya sistemi / fsync / TRIM sınırı |
| 8 | `[[donanaım-type-kapsam]]` | Üst kapsam — envanter UNKNOWN |

## §9 Şablon Uygulama Defteri (append-only)

| Tarih | Şablon (§) | Uygulandı mı? | Ölçüm girdisi | Not |
|---|---|---|---|---|
| — | — | HAYIR — kod yok (§1) | — | İlk kayıt: 2026-10-13, tümü bekliyor |

### §9.1 Defter satırı kuralları

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Satırlar yalnız eklenir | log.md append-only ilkesi |
| 2 | "Uygulandı mı?" yalnız KOD VARSA evet | §1 — kod 0 |
| 3 | Ölçüm girdisi sütunu boş bırakılamaz (varsa) | Kanıtsız uygulama sayılmaz |
| 4 | Tarih zorunlu | Tarihsiz satır geçersiz (§15.1 ilkesi) |
| 5 | İmza zorunlu | Sorumluluk kaydı |

### §9.2 Defter - bekleyen satırlar (otomatik açılacak)

| # | Bekleyen satır | Tetikleyici |
|---|---|---|
| 1 | §2.2 indeksleme | Kapı 1 kapandı + kod yazıldı |
| 2 | §3.2 promosyon | Eşik kararı (§3.7) |
| 3 | §4.1 izleme | OS arayüzü doğrulandı (⚠️) |
| 4 | §5.1 köprü girdileri | İlk ölçüm geldi |
| 5 | §1.1 tarama yenileme | Repo değişti (kod oluştuğu an) |

### §9.3 Defter - kapanış kriteri (bu dosyanın "bitti" eşiği)

| # | Kriter | Durum |
|---|---|---|
| 1 | Repo kodu ≠ 0 (§1.1 tarama) | 🔴 açık |
| 2 | Envanter (Q-SK1) | ❓ UNKNOWN |
| 3 | Kapı 1 + kapı 3 kapandı | 🔴 açık |
| 4 | Eşik kararları imzalı | 🔴 açık |
| 5 | Üst onay (kural 13) | 🔴 açık |

**Bitti kuralı:** Beş kriterin hiçbiri bu oturumda kapanmadı → bu dosya
**taslak (draft)** kalır; "tüm örnekler uygulandı" ifadesi **yazılıp
kayıtlanamaz**.

## §10 Sık Yapılan Hatalar ve Uygulama Sırası

### §10.1 Sık yapılan hatalar

| # | Hata | Belirti | Doğrusu |
|---|---|---|---|
| 1 | Şablonu gerçek kod gibi sunmak | "İndeksleyici yazıldı" iddiası | §1 — kod 0, şablondur |
| 2 | Ölçümden önce kod yazmak | Eşik uydurma | §5 köprü kuralı |
| 3 | İndeks + önbellek tek dosyada | Her ikisi de darboğaz | §2/§3 ayrı tasarla |
| 4 | Tek sağlık okuması | "Sağlıklı" kararı | §4.3 iki satır |

### §10.2 Uygulama sırası

| # | Sıra | İş | Kapı |
|---|---|---|---|
| 1 | 1 | Envanter (Q-SK1) | `ssd-dogrulama.md` §9.1 |
| 2 | 2 | Hız ölçümü | `ssd-dogrulama.md` §3 |
| 3 | 3 | Sağlık baseline | `ssd-dogrulama.md` §5.3 |
| 4 | 4 | Eşik kararları (§3.3) | üst karar |
| 5 | 5 | Şablon → kod | repo (henüz 0 — §1) |

### §10.3 SSS (Sık Sorulan Sorular)

| # | Soru | Cevap |
|---|---|---|
| 1 | Şablonlar kod olarak kullanılabilir mi? | Hayır — §1: kod 0; onay + ölçüm kapısı önce |
| 2 | Hangi ölçüm önce? | Hız (§5.1/1-2), sonra sağlık baseline (§5.1/4) |
| 3 | Eşik rakamları nereden? | Üretici içerik ⚠️ + ölçüm; uydurma yasak |
| 4 | İndeks nereye yazılır? | Sıcak katman SSD (§3.1/3) — envanter ⚠️ |
| 5 | Kod ne zaman yazılır? | §5.2 beş ön koşul tamamsa + üst onay |
| 6 | HDD örnekleri bu dosyada mı? | Hayır — `hdd-ornekler.md` (§6/1-6) |

### §10.4 Örnek-adım envanteri (bu dosyadaki şablonların özeti)

| # | Şablon | Bölüm | Girdi bekliyor | Çıktı bekleniyor |
|---|---|---|---|---|
| 1 | İndeksleme adımları | §2.2-§2.4 | ölçüm + K015 sınırı | kod (0) |
| 2 | Küçük-yazma biriktirme | §2.3 | fsync davranışı (K000) | karar satırı |
| 3 | Tarama süresi kestirimi | §2.6 | SEQ medyanı | süre (boş) |
| 4 | Sıcak katman promosyonu | §3.2-§3.3 | eşik kararı (üst) | karar kaydı |
| 5 | Bütçe tablosu | §3.5 | envanter | hesap (yapılamaz) |
| 6 | Sağlık izleme görevi | §4.1-§4.3 | OS arayüzü (⚠️) | 2 satır okuma |
| 7 | RED sonrası akış | §4.5 | olay | kapı geçmişi satırı |
| 8 | Ölçüm → kod köprüsü | §5.1-§5.3 | kapılar | onay |

**Envanter kuralı:** Tabloda "girdi bekliyor" = o satır **açık** sayılır;
"beklenen çıktı (boş)" ölçüm gelmeden doldurulmaz (Zero-Hallucination).

## §11 Dürüstlük Bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler yalnız doğrulanmış
kaynaklar içerir; doğrulanamayan iddialar ⚠️ VERIFICATION REQUIRED ile
işaretlidir. 2 web URL'si HEAD ile 200 döndürülerek erişilebilirlikleri
doğrulanmıştır (içerik okunmadı); repo kodu **0 dosya** olarak ölçülmüştür;
tüm eşik/sayı kararları açık formül ya da üst karar olarak işaretlidir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
