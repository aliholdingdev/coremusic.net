---
node_id: ssd-dogrulama
node_type: module
status: draft
parent_id: K001-donanım/donanaım-type/ssd
children: []
depends_on:
  - ssd-kapsam
  - K001-donanım/donanaım-type/donanaım-type-dogrulama
evidence:
  - "https://crystalmark.info/en/software/crystaldiskmark/ (HTTP 200, 2026-10-13)"
  - "https://www.crucial.com/support/articles/ssd-firmware (HTTP 200, 2026-10-13)"
  - "https://nvmexpress.org/ (HTTP 200, 2026-10-13)"
  - "K001-donanım/donanaım-type/donanaım-type-dogrulama.md §1-§4"
validation: "draft - 2026-10-13 yazıldı → ≥500 satır ölçümü yazım sonrası → fiziksel ölçüm YAPILMADI (tüm S1 kapıları AÇIK)"
open_questions:
  - "Q-SK1: SSD envanteri (marka/model/kapasite/arayüz) bilinmiyor - UNKNOWN"
  - "Q-SK3: Hedef dayanıklılık (TBW/DWPD) üst karar bekliyor - ⚠️ VERIFICATION REQUIRED"
  - "Q-SD1: Ölçüm diski hangi makinede, başka I/O var mı - ölçüm koşulu tanımsız (S1 açık)"
group: kanit
---

# ssd-dogrulama - Katı Hal Disk Doğrulama Kapıları

> **Amaç:** `ssd-kapsam.md` içindeki iddiaları **kapı kapı** doğrulamak. Bu dosya
> **kanıt** dosyasıdır: her bölüm bir ölçüm protokolü, geçme koşulu ve kayıt
> alanı içerir. Doğrulanmamış hiçbir sonuç "geçti" sayılmaz.

## §1 ELI15

SSD için dört soru sorulur ve dördü de **ölçülmeden** yanıtlanmaz:
(1) verilen hızlar gerçek mi (hız ölçümü), (2) silinen dosyalar gerçekten
silindi mi (TRIM doğrulama), (3) diskin sağlık sayaçları ne diyor (SMART /
NVMe health), (4) sıcaklık makul sınırlarda mı (sıcaklık). Bu dosya bu dört
sorunun **adımlarını, geçme eşiklerini ve kayıt formunu** yazar. HDD'den
farkı: mekanik arıza belirtisi (gürültü, bad-sector sesi) burada **yoktur** —
arızanın tek görünür yüzeyi **sayaç ve performans düşüşüdür**, o yüzden
sayaç okuması burada daha ağırlıklıdır.

> ⚠️ VERIFICATION REQUIRED: Bu dosyada **hiçbir gerçek ölçüm sonucu yoktur** —
> envanter bilinmiyor (Q-SK1), ölçüm koşulu tanımsız (Q-SD1). Tüm S1 seviyesi
> kapıları **AÇIK**tır; buradaki protokoller uygulanacak iş listesidir,
> sonuç raporu değildir.

## §2 Kanıt Seviye Şeması (bu dosyada kullanılır)

| # | Seviye | Anlamı | Örnek | Kapı durumu |
|---|---|---|---|---|
| 1 | S1 | Fiziksel ölçüm / araç çıktısı | CrystalDiskMark skoru, sıcaklık °C | 🔴 AÇIK (ölçüm yok) |
| 2 | S2 | Aritmetik olarak türetilmiş | 6×10⁹÷8 ≈ 600 MB/s, TBW formülü | ✅ kapandı (`ssd-kapsam.md` §7.1/§9.1) |
| 3 | S3 | Üretici beyanı (okunmadı) | P/E cycle sayısı, TBW sayfası | ⚠️ yalnız erişilebilirlik |
| 4 | S4 | Kavramsal / tanım | SLC=1 bit, TRIM tanımı | ✅ tanım düzeyi |
| 5 | — | Bilinmeyen | Envanter, doluluk oranı | ❓ UNKNOWN |

**Kural:** S1 kapısı yalnız **araç çıktısı dosyası/screenshot** ile kapanır;
"hızlıydı", "eski haline göre iyiydi" cümleleri kanıt değildir.

### §2.1 Kanıt türünün kapıya eşlenmesi

| # | Kanıt türü | Hangi kapıya girer | Tek başına yeterli mi? |
|---|---|---|---|
| 1 | Araç çıktı dosyası (hız) | §3 Kapı 1 | evet — koşul satırı ile birlikte |
| 2 | Silme sonrası araç kanıtı | §4 Kapı 2 | evet |
| 3 | Sağlık okuması (tek) | §5 Kapı 3 | **hayır** — §5.2 trend kuralı |
| 4 | Sağlık okuması (iki tarihli) | §5 Kapı 3 | evet |
| 5 | Sıcaklık okuması (boşta + yük altında) | §6 Kapı 4 | evet — üretici eşiği ayrıca S3 |
| 6 | Üretici destek sayfası (HEAD) | yalnız erişilebilirlik | **hayır** — içerik okunmadı |
| 7 | Bellek/şerit (kayıt yok) | hiçbir kapı | hayır — kanıt sayılmaz |

### §2.2 Kapı RED senaryoları (neyde "geçmez" denir)

| # | Senaryo | Sonuç |
|---|---|---|
| 1 | Hız, tavanın %70'in altında | RED — §3.3/1-3 |
| 2 | İki sağlık okuması arasında hata sayacı arttı | RED — §5.2/2 |
| 3 | Toplam yazılan, ürün TBW'sini aştı | RED — §5.2/3 + Q-SK3 |
| 4 | Yük altında sıcaklık üretici sınırını geçti | RED — §6.2/1 |
| 5 | Ölçüm sırasında arka plan I/O açık kalmış | **geçersiz** — tekrar ölç |
| 6 | Form doldurulmamış | kapı açık sayılır — §10 |

## §3 Hız Ölçüm Protokolü (kapı 1)

### §3.1 Araç ve koşul seçimi

| # | Öğe | Seçim | Gerekçe |
|---|---|---|---|
| 1 | Ölçüm aracı | CrystalDiskMark (veya sınıfı araç) | Resmi sayfa: `https://crystalmark.info/en/software/crystaldiskmark/` (HEAD 200, 2026-10-13 — içerik okunmadı ⚠️) |
| 2 | Test boyutu | ≥ disk kapasitesinin küçük yüzdesi (ör. 1 GiB × 5 tur) | Önbellek tuzağını önlemek için tekrar sayısı önemli ⚠️ araç ayarı |
| 3 | Test türü | hem sıralı (SEQ) hem rastgele (4K/4KiB QD) | `ssd-kapsam.md` §7.2 — rastgele erişim iş yükü |
| 4 | Doluluk koşulu | disk %80 altında dolu | `ssd-kapsam.md` §5.4 — dolu diskte ölçümler yanıltır |
| 5 | Arka plan I/O | başka ağır yazma işlemi kapalı | Q-SD1 — çakışan ölçüm geçersiz |
| 6 | Tekrar | en az 3 tur, medyanı al | Tek tur önbellek etkisine açık ⚠️ |

### §3.2 Ölçüm adımları

| # | Adım | Çıktı | Kayıt |
|---|---|---|---|
| 1 | Envanteri yaz (marka/model/kapasite/arayüz) | satır | §9.1 — UNKNOWN ise dur |
| 2 | Doluluğu ölç ve yaz | % | §9.1 |
| 3 | Arka plan I/O'yu durdur | onay | §9.1 |
| 4 | SEQ okuma/yazma testini çalıştır | MB/s ×2 | §9.2 |
| 5 | 4K rastgele okuma/yazma testini çalıştır | MB/s ×2 | §9.2 |
| 6 | 3 tur medyanını al | 4 sayı | §9.2 |
| 7 | Sonuçları üretici/arayüz tavanıyla karşılaştır | oran | §3.3 |
| 8 | Aracı kapat, diski normal kullanıma dön | onay | — |

### §3.3 Geçme koşulları

| # | Ölçüm | Beklenen tavan | Geçme koşulu | Durum |
|---|---|---|---|---|
| 1 | SATA SSD sıralı okuma | ≈600 MB/s (8b/10b aritmetiği, `ssd-kapsam.md` §7.1) | tavanın **≥ %70**'i | 🔴 açık |
| 2 | SATA SSD sıralı yazma | üretici beyanı (S3 ⚠️) | beyanın **≥ %70**'i | 🔴 açık |
| 3 | NVMe sıralı okuma | PCIe nesli/kanele göre ⚠️ ölçüm | üretici beyanı ≥ %70 | 🔴 açık |
| 4 | 4K rastgele okuma | kesin eşik yok | kayıt tutulur, **eşik yok** → trend takibi | 🔴 açık |
| 5 | HDD'den gelen veri akışı | ≈1,1 MiB/s (`hdd-kapsam.md` §6.1) | SSD ölçümü bunu **katlı** aşmalı | 🔴 açık |

> ⚠️ VERIFICATION REQUIRED: "%70" eşiği bu oturumda **kaynakla
> doğrulanmamıştır** — mühendislik varsayımı olarak işaretlenmiştir; araç
> dokümantasyonu okunup eşik literatürle desteklenmeden **bağlayıcı değildir**.
> Üretici beyanları (S3) okunmadan karşılaştırma **yapılmaz**.

### §3.4 Hız ölçümü - sık yapılan hatalar

| # | Hata | Belirti | Doğrusu |
|---|---|---|---|
| 1 | Disk doluyken ölçmek | Yazma hızı dramatik düşer | §3.1/4 — %80 altı |
| 2 | Tek tur ölçmek | Önbellekten okuyup "hızlı" demek | §3.1/6 — 3 tur medyanı |
| 3 | Yazma ile aynı anda ölçmek | Sonuç tutarsız | §3.1/5 — arka plan I/O kapalı |
| 4 | SATA tavanını NVMe ile kıyaslamak | Yanlış sonuç | §3.3 — ayrı satırlar |
| 5 | Önbellek temizlemeden tekrar | Aynı sayılar | Sıra dışı tur bekle / diski kullan |

### §3.5 Ölçüm - araç ve ortam kontrol listesi

| # | Kontrol | Soru | Evet/Hayır |
|---|---|---|---|
| 1 | Araç sürümü kaydedildi mi? | sürüm numarası not edildi mi? | (boş) |
| 2 | Test dosyası boyutu | ≥ belirlenen boyut (§3.1/2) | (boş) |
| 3 | Disk doluluğu | %80 altı (§3.1/4) | (boş) |
| 4 | Arka plan yazma | kapatıldı mı (§3.1/5) | (boş) |
| 5 | Güç planı | ölçüm boyunca sabit performans ⚠️ koşul | (boş) |
| 6 | Tekrar sayısı | 3 tur medyanı (§3.1/6) | (boş) |
| 7 | Sonuç dosyası saklandı | ekran görüntüsü/dosya bağlantısı | (boş) |

### §3.6 Ölçüm sonrası değerlendirme adımları

| # | Adım | Soru | Çıktı |
|---|---|---|---|
| 1 | Medyanı al | 3 tur tutarlı mı? (sapma büyükse tekrar) | 4 medyan |
| 2 | Tavanla karşılaştır | Hangi %'de kaldı? (§3.3) | oran |
| 3 | NVMe/SATA ayır | Karşılaştırma doğru satırda mı? | satır seçimi |
| 4 | 4K sonucunu trende yaz | Önceki ölçüm var mı? | trend satırı |
| 5 | Ölçümü §9.2'ye işle | Form satırı doldu mu? | dolu form |
| 6 | Kapı kararı | §3.3 koşulları sağlandı mı? | KAPANIŞ / RED |

## §4 TRIM Doğrulama Kapısı (kapı 2)

### §4.1 Neden kapı?

| # | Soru | Önem |
|---|---|---|
| 1 | TRIM verilmezse | Disk blokları canlı sayar → GC yükü → zamanla yavaşlama |
| 2 | Kapsam sınırı | TRIM **üretilmez**, yalnız **doğrulanır** — kod K000'de (`ssd-kapsam.md` §6) |
| 3 | RAID/ylayıcı altında | Yol bozulabilir → yapılandırma bağımlı (`ssd-kapsam.md` §6/8) |

### §4.2 Doğrulama adımları (kavramsal protokol)

| # | Adım | Yöntem | Kanıt |
|---|---|---|---|
| 1 | Büyük bir test dosyası oluştur (ör. birkaç GB, doldurulabilir) | dosya oluştur | dosya boyutu kaydı |
| 2 | Dosyayı kalıcı sil | silme | silme onayı |
| 3 | Boş alanın diske gerçekten bildirildiğini doğrula | işletim sistemi / sürücü araçları (yüzey K000) | araç çıktısı ⚠️ |
| 4 | Silme sonrası yazma hızını yeniden ölç | §3 protokolü | §9.2 ile karşılaştırma |
| 5 | Uzun süreli doluluk-döngüsü testi (varsa) | tekrarlı yaz-sil | trend kaydı |

> ⚠️ VERIFICATION REQUIRED: Adım 3'ün Windows'taki somut aracı bu oturumda
> **araştırılıp doğrulanmadı** — hangi aracın hangi çıktıyı verdiği K000
> yüzeyinde incelenmelidir. Burada **araç adı uydurulmamıştır**; protokol
> kavramsal düzeyde kalır ve kapı **AÇIK**tır.

### §4.3 Geçme koşulu

| # | Koşul | Durum |
|---|---|---|
| 1 | Silme sonrası kısa süre içinde diske "discard/TRIM" gittiğine dair araç kanıtı | 🔴 açık |
| 2 | Silme sonrası yazma hızı, ilk ölçümün belirgin altında değil | 🔴 açık |
| 3 | Dosya sistemi + sürücü kombinasyonu kayıtlı (K000 sınırı) | 🔴 açık |

### §4.4 TRIM doğrulama - kapı RED nedenleri

| # | Neden | Sonuç | Sonraki adım |
|---|---|---|---|
| 1 | Araç kanıtı üretilemedi | kapı açık | K000 yüzeyinde araç araştır (⚠️ ad uydurma) |
| 2 | Silme sonrası hız belirgin düştü | RED | doluluğa bak (§3.1/4), GC bekle, tekrar ölç |
| 3 | RAID/ylayıcı altında yola çıkıyor | RED | yapılandırmayı kaydet, kapsam kararı iste |
| 4 | Dosya sistemi TRIM'i desteklemiyor | RED | K000 kapsamına devret |
| 5 | Dosya sistemine silinmedi, taşındı (move) | **geçersiz test** | gerçekten sil, tekrarla |

### §4.5 TRIM - bakım döngüsü ölçümü (uzun vadeli kapı)

| # | Periyot | İş | Çıktı |
|---|---|---|---|
| 1 | Aylık | §9.3 sağlık satırının TRIM'li dönem okuması | ikinci okuma |
| 2 | Aylık | §3.4 ile hız tekrarı (medyan) | trend satırı |
| 3 | Çeyrek | Doluluk + yazma profili gözden geçirme | profil notu |
| 4 | Diske değişimde | Tüm kapılar sıfırdan | yeni form satırı |

**Trend kuralı (TRIM):** Tek seferlik hız ölçümü kapı **kapatmaz**; silme
döngüsü sonrası hız **ilk ölçümün belirgin altında kalmadığında** kapı
kapanır (§4.3/2). "Belirgin" eşiği bu oturumda sayısal olarak
**belirlenmemiştir** → ⚠️ VERIFICATION REQUIRED.

## §5 SMART / NVMe Sağlık Kapısı (kapı 3)

### §5.1 Okunan sayaçlar (kavramsal — atama üreticiye özeldir)

| # | Gösterge | Ne bakılır | Not |
|---|---|---|---|
| 1 | Sağlık durumu (genel) | kullanılabilir / uyarılı | Türkçe "Kullanılabilir" eşdeğeri ⚠️ araç dili |
| 2 | Toplam yazılan (host writes) | `ssd-kapsam.md` §9.1 formülüne girer | TBW karşılaştırması girdisi |
| 3 | Yüzde kullanım (used lifetime %) | ömür yüzdesi | Trend alınır |
| 4 | Yeniden tahsis (reallocated) / medya hata sayacı | artış var mı | Artış = kapı RED |
| 5 | Sıcaklık | §6 ile birlikte | Aynı okumadan |
| 6 | Anormal kapanma / acil güç kesintisi | tekrar ediyorsa güç sorunu | ⚠️ yorum üreticiye göre |

> ⚠️ VERIFICATION REQUIRED: Bu sayaçların **adları ve anlamları SSD
> üreticisine göre değişir**; envanter bilinmediği için (Q-SK1) somut atama
> listesi yazılmamıştır. İlgili üretici destek sayfası okunmadan yorum
> yapılmaz (Crucial destek: `https://www.crucial.com/support/articles/ssd-firmware`
> — HEAD 200, 2026-10-13; içerik okunmadı).

### §5.2 Geçme koşulları

| # | Koşul | Eşik | Durum |
|---|---|---|---|
| 1 | Genel sağlık | "kullanılabilir" (benzeri) | 🔴 açık |
| 2 | Hata sayaçları | artış yok (2 okuma arası) | 🔴 açık — tek okuma yeterli değil |
| 3 | Toplam yazılan ≤ ürün TBW'si | üretici değer (S3) | 🔴 açık — Q-SK3 |
| 4 | İki okuma arası trend | en az 2 tarihli okuma | 🔴 açık — tarihli kayıt yok |

**Trend kuralı:** Tek okuma **kapı kapatmaz**; sağlık göstergeleri yalnız
**iki tarihli okuma arasındaki değişim** ile değerlendirilir (artış = sorun,
düz = stabil).

### §5.3 Sağlık okuma periyodu ve sorumluluk

| # | Periyot | Okuma | Kayıt | Sorumlu |
|---|---|---|---|---|
| 1 | Kurulumda (ilk gün) | tüm sayaçlar baseline | §9.3 ilk okuma | vault (insan) |
| 2 | Aylık | genel sağlık + toplam yazılan | ikinci okuma satırı | vault (insan) |
| 3 | Çeyrek | hata sayaçları + firmware | trend notu | vault (insan) |
| 4 | Hız şikayetinde | §3 ölçümü + sağlık aynı gün | iki form birlikte | vault (insan) |
| 5 | Diske değişimde | kapanış okuması | arşiv satırı | vault (insan) |

### §5.4 Sağlık sayacının TBW ile birlikte okunması

| # | Koşul | Sonuç |
|---|---|---|
| 1 | Toplam yazılan ÷ kapasite → gün (§9.1 formülüne giren girdi) | günlük yazma |
| 2 | Günlük yazma × 365 × garanti yılı = ihtiyaç TBW | ihtiyacı hesapla |
| 3 | İhtiyaç ≤ ürün TBW (S3, okunmadı ⚠️) | uygunluk **kararı yazılamaz** — Q-SK3 |
| 4 | Ürün TBW okunmadan karar | ⚠️ VERIFICATION REQUIRED |

> Bu alt bölüm `ssd-kapsam.md` §9.4 zincirinin 1., 2. ve 4. halkasını bu
> dosyaya bağlar: ölçüm (§3) buradan, karar üst karara (Q-SK3) gider.

## §6 Sıcaklık Kapısı (kapı 4)

### §6.1 Protokol

| # | Adım | Koşul | Çıktı |
|---|---|---|---|
| 1 | Boşta sıcaklık oku | sistem hafif | °C + saat |
| 2 | Yük altında oku | §3 ölçümü sırasında | °C + saat |
| 3 | Yük sonrası soğuma gözlemi | 10-15 dk | °C eğrisi ⚠️ süre şablon değeri |
| 4 | Konum notu | kasası/havalandırma | serbest metin |

### §6.2 Geçme koşulu

| # | Koşul | Eşik | Durum |
|---|---|---|---|
| 1 | Yük altında sıcaklık | üretici üst sınırı altında (S3 ⚠️ okunmadı) | 🔴 açık |
| 2 | Soğuma | yük sonrası düşüş görülüyor | 🔴 açık |
| 3 | Termal throttle belirtisi | hız ölçümünde ani düşüş yok | 🔴 açık |

> ⚠️ VERIFICATION REQUIRED: Sayısal sıcaklık eşiği (°C) **uydurulmamıştır** —
> üretici datasheet'i okunmadan rakam yazılmaz. Bu nedenle kapı yalnızca
> "üretici sınırının altında" koşuluyla tanımlıdır.

## §7 Firmware Güncelleme Kapısı (kapı 5 — koşullu)

| # | Öğe | Not | Durum |
|---|---|---|---|
| 1 | Mevcut firmware sürümü | araç okuması gerekir | 🔴 açık (envanter yok) |
| 2 | Yükseltme kaydı | sürüm + tarih | 🔴 açık — kayıt yok |
| 3 | Güncelleme riski | güncelleme sırasında güç kesintisi = risk | kural: şebeke + batarya |
| 4 | Kaynak | üretici destek sayfası | Crucial: `https://www.crucial.com/support/articles/ssd-firmware` (HEAD 200, 2026-10-13 — içerik okunmadı ⚠️) |

**Kural:** Firmware güncellemesi bu vault'ta **planlanmaz, yalnız kayıt altına
alınır**; somut güncelleme işlemi donanım sahibi kararı ve yedek almayı
zorunlu kılar (`hdd-kapsam.md` §7.2 yedek ilkesi).

## §8 Web Kaynak Doğrulaması (bu dosya)

### §8.1 HEAD erişilebilirlik testi (2026-10-13, PowerShell `Invoke-WebRequest -Method Head`)

| # | URL | Amaç | Sonuç |
|---|---|---|---|
| 1 | `https://crystalmark.info/en/software/crystaldiskmark/` | Ölçüm aracı resmi sayfası | ✅ HTTP 200 |
| 2 | `https://www.crucial.com/support/articles/ssd-firmware` | Firmware güncelleme rehberi | ✅ HTTP 200 |
| 3 | `https://nvmexpress.org/` | NVMe spesifikasyonu ana kaynak | ✅ HTTP 200 |
| 4 | `https://www.micron.com/support` | Üretici destek (Micron/Crucial ailesi) | ✅ HTTP 200 |

### §8.2 Doğrulama sınırı (bu dosyanın dürüstlük kuralı)

| # | Sınır | Anlamı |
|---|---|---|
| 1 | HEAD 200 = **yalnız erişilebilirlik** | Sayfa var ve yanıt veriyor |
| 2 | İçerik **okunmadı** | Sayfadaki hiçbir sayı/iddia bu dosyaya alınmadı |
| 3 | İçerik iddiaları | ⚠️ VERIFICATION REQUIRED ile işaretli |
| 4 | Elenen adaylar | Bu oturumda SSD dışı/kırık URL'ler ayrıca elendi; uydurma URL listelenmez |

### §8.3 Test komutu (tekrarlanabilirlik)

```powershell
$urls = @('https://crystalmark.info/en/software/crystaldiskmark/',
          'https://www.crucial.com/support/articles/ssd-firmware',
          'https://nvmexpress.org/',
          'https://www.micron.com/support')
foreach ($u in $urls) {
  try   { $r = Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20
          "$($r.StatusCode) $u" }
  catch { "FAIL $u :: $($_.Exception.Message)" }
}
```

**Beklenen çıktı (2026-10-13 ölçümü):** 4 satır `200` — yeniden
çalıştırıldığında aynı sonucu vermesi gerekir; **farklı sonuç = kapı tekrar
açılmıştır** (erişilebilirlik kapısı).

### §8.4 İleride içerik doğrulaması gereken kaynaklar (beklemede)

| # | URL | Ne için okunacak | Kapıya etkisi |
|---|---|---|---|
| 1 | crystalmark.info (§8.1/1) | Araç ayarları, boyut/tur önerileri | §3.1 eşik notu |
| 2 | crucial.com (§8.1/2) | Firmware güncelleme adımları | §7 kayıt |
| 3 | nvmexpress.org (§8.1/3) | NVMe health komut tanımı | §5.1 somutlama |
| 4 | micron.com (§8.1/4) | Üretici destek/beyan sayfaları | §5.2/3 TBW girdisi |

**Kural:** Bu dört satır **içerik okunana kadar açık** kalır; okunduklarında
satıra tarih + ne okundu yazılır (append), içerikten alınan sayılar ilgili
kapıya S3 yerine doğrudan **okunmuş kaynak** olarak işlenir.

## §9 Ölçüm Kayıt Formu (boş şablon — append-only)

### §9.1 Koşul satırı (ölçümden önce doldurulur)

| Alan | Değer |
|---|---|
| Tarih / saat | (boş) |
| Disk marka/model/kapasite | (boş — Q-SK1) |
| Arayüz (SATA/NVMe) | (boş) |
| Doluluk % | (boş) |
| Arka plan I/O kapalı mı | (boş) |
| Ölçüm aracı + sürüm | (boş) |
| Ölçümcü | (boş) |

### §9.2 Hız sonucu satırı (§3.2 adım 4-6)

| Ölçüm | Tur 1 | Tur 2 | Tur 3 | Medyan |
|---|---|---|---|---|
| SEQ okuma MB/s | (boş) | (boş) | (boş) | (boş) |
| SEQ yazma MB/s | (boş) | (boş) | (boş) | (boş) |
| 4K okuma MB/s | (boş) | (boş) | (boş) | (boş) |
| 4K yazma MB/s | (boş) | (boş) | (boş) | (boş) |

### §9.3 Sağlık/sıcaklık satırı (§5-§6)

| Alan | İlk okuma | İkinci okuma | Yorum |
|---|---|---|---|
| Genel sağlık | (boş) | (boş) | (boş) |
| Toplam yazılan | (boş) | (boş) | (boş) |
| Hata sayaçları | (boş) | (boş) | (boş) |
| Sıcaklık (boşta/yük altında) | (boş) | (boş) | (boş) |
| Firmware sürümü | (boş) | — | (boş) |

## §10 Kapı Özeti

| # | Kapı | Kanıt tipi | Eşik | Durum |
|---|---|---|---|---|
| 1 | Hız ölçümü | S1 araç çıktısı | ≥ %70 tavan (kaynak ⚠️) | 🔴 AÇIK |
| 2 | TRIM doğrulama | S1 araç kanıtı (araç K000) | bildirim kanıtı | 🔴 AÇIK |
| 3 | SMART/NVMe sağlık | S1 okuma + trend | 2 tarihli okuma | 🔴 AÇIK |
| 4 | Sıcaklık | S1 okuma | üretici sınırı (S3 ⚠️) | 🔴 AÇIK |
| 5 | Firmware | S1 kayıt | kayıt + yedek | 🔴 AÇIK (koşullu) |
| 6 | Envanter | insan girdisi | — | ❓ UNKNOWN (Q-SK1) |

**Kapanış akışı:** Kapı yalnız, §9'daki karşılık alanları **doldurulmuş** ve
kanıt (araç çıktısı/tarih) **eklenmiş** ise kapanır; kapanan kapı §15'e
append edilir. Boş form + "geçti" yazısı = kapı **açık sayılır**.

### §10.1 Kapı geçiş sırası (bağımlılık)

| # | Sıra | Kapı | Neden bu sırada |
|---|---|---|---|
| 1 | Önce | Envanter (§9.1) | Marka/model yoksa kapı 3-5 yorumlanamaz |
| 2 | 2. | Hız (§3) | Koşul satırı diğer kapıların da girdisidir |
| 3 | 3. | Sağlık (§5) | Ölçüm öncesi baseline gerek (§5.3/1) |
| 4 | 4. | TRIM (§4) | Hız trendiyle birlikte okunur (§4.5) |
| 5 | 5. | Sıcaklık (§6) | Hız ölçümü sırasında toplanabilir, sonra işlenir |
| 6 | Son | Firmware (§7) | Yalnız önceki kapılar kapandıysa ve kayıt gerekirse |

**Bağımlılık kuralı:** Kapı 1 (envanter) **açıkken** kapı 3-5 "şartlı kapanır"
sayılır — yorum üreticiye özeldir ve envanter bilinmeden yazılamaz (Q-SK1).

## §11 Sık Yapılan Hatalar

| # | Hata | Belirti | Doğrusu |
|---|---|---|---|
| 1 | Tek hız ölçümüyle genel karar | "SSD hızlı" cümlesi | 3 tur medyanı + 4K ayrı |
| 2 | Sağlık sayaçını tek sefer okumak | "sağlıklı" yorumu | §5.2 trend kuralı |
| 3 | Ölçüm sırasında yazma yapmak | Tutarsız sonuç | §3.1/5 |
| 4 | Üretici eşiğini okumadan rakam vermek | Uydurma °C/MB/s | §6.2/§3.3 ⚠️ kuralı |
| 5 | TRIM'i bu vault'ta kodlamaya çalışmak | Kapsam ihlali | §4.1/2 — doğrular, üretmez |
| 6 | Formu doldurmadan "kapandı" demek | Kanıtsız kapı | §10 kapanış akışı |

## §12 Terim Sözlüğü

| # | Terim | Tanım |
|---|---|---|
| 1 | SEQ | Sıralı erişim ölçümü |
| 2 | 4K / 4KiB | Küçük blok rastgele erişim ölçümü |
| 3 | QD (queue depth) | Komut kuyruğu derinliği ⚠️ terim, araç ayarı |
| 4 | Medyan | 3 turun ortanca değeri (outlier direnci) |
| 5 | Trim/iscard | Dosya sisteminin silme bildirimi (`ssd-kapsam.md` §6) |
| 6 | GC | Blok toplama (`ssd-kapsam.md` §4) |
| 7 | SMART | Disk sağlık raporlama sistemi |
| 8 | NVMe health | NVMe sağlık sayfa/komut ailesi (⚠️ somut araç K000) |
| 9 | TBW | Toplam yazılabilir terabayt (`ssd-kapsam.md` §9) |
| 10 | Trend | İki tarihli okuma arasındaki değişim |
| 11 | Throttle | Sıcaklık nedeniyle hız düşürme |
| 12 | Firmware | Disk üzerindeki denetleyici yazılımı |
| 13 | S1/S2/S3 | Bu dosyanın kanıt seviye şeması (§2) |
| 14 | Kapı | Geçme koşulları tanımlı doğrulama adımı |
| 15 | Append-only | Kayıt alanına yalnız ekleme, silme yok |

## §13 İddia → Kanıt Matrisi

| # | `ssd-kapsam.md` iddiası | Kanıt tipi | Gittiği kapı | Seviye |
|---|---|---|---|---|
| 1 | SATA ≈600 MB/s tavan (§7.1) | aritmetik + ölçüm | §3.3/1 | S2 + S1 açık |
| 2 | NVMe kuyruk derinliği avantajı (§7.1) | ölçüm | §3.3/3 | S1 açık |
| 3 | TRIM GC yükünü azaltır (§6) | araç kanıtı | §4 | S1 açık |
| 4 | TBW formülü (§9.1) | aritmetik | §5.2/3 | S2 ✅ |
| 5 | Doluluk %80 kuralı (§5.4) | ölçüm koşulu | §3.1/4 | S2 + S1 açık |
| 6 | Okuma-öncelikli düşük ömür ihtiyacı (§9.3) | sayaç trendi | §5.2/4 | S1 açık |
| 7 | Boot/kütüphane ayrımı (§10.2) | ölçüm karşılaştırması | §3 + §10 | S1 açık |
| 8 | Sıcaklık sınırı (§10.1/6) | ölçüm + üretici | §6.2 | S1 + S3 ⚠️ |
| 9 | Envanter (Q-SK1) | insan girdisi | §9.1 | ❓ UNKNOWN |

## §14 Çapraz Referanslar

| # | Hedef | İlişki |
|---|---|---|
| 1 | `[[ssd/index]]` | Bu konunun kütüğü |
| 2 | `ssd-kapsam.md` | Doğrulanan iddiaların sahibi |
| 3 | `ssd-ornekler.md` | Ölçüm/izleme örnek adımları |
| 4 | `[[hdd-dogrulama]]` | Kardeş kapı dosyası (yüzey/gürültü kapıları HDD'ye özgü) |
| 5 | `[[donanaım-type-dogrulama]]` | Üst doğrulama tablosu |
| 6 | `[[donanaım-type-kapsam]]` | Üst kapsam — envanter UNKNOWN |
| 7 | `[[isletim-sistemi-core-kapsam]]` | TRIM'in üretildiği yüzey (K000) |
| 8 | `[[output-kapsam]]` | 512/48k tampon — ölçüm hattının karşılaştırma birimi |

## §15 Kapı Geçmişi (append-only kayıt tablosu)

| Tarih | Kapı | Sonuç | Kanıt bağlantısı | İmza |
|---|---|---|---|---|
| — | — | (hiçbir kapı kapatılmadı — 2026-10-13 durumu) | — | — |

**Kural:** Satırlar yalnız eklenir; mevcut satır değiştirilmez/silinmez
(log.md ilkesi). Bir kapı RED olursa nedeni aynı satırda yazılır, kapı
sonrası tekrar ölçülür.

### §15.1 Ekleme satırı şablonu (kopyala-kullan)

| Tarih | Kapı | Sonuç | Kanıt bağlantısı | İmza |
|---|---|---|---|---|
| YYYY-MM-DD | §X | KAPANDI / RED (neden) | dosya yolu veya araç çıktısı | isim |

**Zorunlu alanlar:** Tarih yoksa satır geçersizdir; kanıt bağlantısı yoksa
satır "açık kapı" kaydı sayılır; kapı numarası §10 tablosundaki numaradır.

## §16 Dürüstlük Bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler yalnız doğrulanmış
kaynaklar içerir; doğrulanamayan iddialar ⚠️ VERIFICATION REQUIRED ile
işaretlidir. 4 web URL'si bu oturumda HEAD ile 200 döndürülerek
erişilebilirlikleri doğrulanmıştır; **içerik okunmadığı için içerik iddiaları
kaynak sayılmaz**. Tüm S1 kapıları ölçümsüz **AÇIK**tır; eşik rakamları
kaynağı olmadan **bağlayıcı değildir**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
