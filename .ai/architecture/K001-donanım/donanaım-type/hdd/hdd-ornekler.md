---
node_id: hdd-ornekler
node_type: module
status: draft
parent_id: K001-donanım/donanaım-type/hdd
children: []
depends_on:
  - hdd-kapsam
  - hdd-dogrulama
evidence:
  - "Repo ölçümü 2026-10-13: *.cpp = 0 · *.h = 0 · CMakeLists.txt = 0"
  - "https://knowledge.seagate.com/ (HTTP 200, 2026-10-13)"
  - "https://www.storagereview.com/ (HTTP 200, 2026-10-13)"
  - "K001-donanım/donanaım-type/hdd/hdd-kapsam.md §4-§6 (aritmetik)"
validation: "draft — 2026-10-13 yazıldı · ≥500 satır ölçümü yazım sonrası · repo kodu YOK (0 C++ dosyası)"
open_questions:
  - "Q-HO1: Kütüphane taramasını yürütecek bileşen henüz yazılmadı (K003/K015 PLANNED) — ⚠️ VERIFICATION REQUIRED"
  - "Q-HO2: Streaming buffer sahibi çıkış düğümü (512/48k) — `[[output-kapsam]]` kararına bağlı"
  - "Q-HO3: Yedek rotasyon takvimi (sıklık/süre) üst karar bekliyor"
group: kod
---

# hdd-ornekler — Mekanik Disk Uygulama Örnekleri

> **Kural:** §1 repo kodu birebir aranır (yol + sayı); ilgizse **— yok** yazılır.
> §2–§4 **adım şablonlarıdır** — gerçek çalışan kod olarak sunulmaz. Uydurma
> kaynak dosya, uydurma sürüm, uydurma çıktı **üretilmez**.

## §1 Gerçek repo kodu (ÖNCELİK 1)

**— yok.**

Ölçüm (2026-10-13, repo kökü `C:\www\coremusic.net`, `.git`/gizli klasör ayrımı
olmaksızın tam tarama):

| # | Aranan | Sonuç |
|---|---|---|
| 1 | `*.cpp` dosyası | **0** |
| 2 | `*.h` dosyası | **0** |
| 3 | `CMakeLists.txt` | **0** |
| 4 | Kütüphane tarama / indeksleme kodu (C++) | — yok |
| 5 | Streaming buffer / ring buffer kodu (C++) | — yok |
| 6 | Yedekleme betiği (`.ps1`/`.sh`) — depolama odaklı | ⚠️ bu oturumda aranmadı → UNKNOWN |

**Yorum:** L0 altyapı (`Neva Engine`) **PLANNED**dır — depolama örnekleri bu
nedenle **tasarım şablonu** olarak yazılır; repo'ya kod eklenmez (bu görev
kod üretmez, yalnız `.md` üretir).

## §2 Kütüphane Tarama Örneği (adım şablonu)

### §2.1 Amaç ve sınırlar

| # | Amaç | Sınır |
|---|---|---|
| 1 | Klasördeki ses dosyalarını bulup listelemek | Salt okuma — dosyaya dokunma |
| 2 | Boyut / süre / biçim bilgisini çıkarmak | Süre bilgisi **dış aracı** gerektirir (⚠️ araç seçimi YOK) |
| 3 | Tekrarları ve eksikleri işaretlemek | Özet (hash) yalnız isteğe bağlı |
| 4 | Sonucu indeks dosyasına yazmak | İndeks **geçici** sayılır; yeniden üretilebilir |
| 5 | İlerleme ve hız raporu | Kesintiye dayanıklı (devam edilebilir) |

### §2.2 Tarama akışı (adım tablosu)

| # | Adım | Aksiyon | Çıkış | Hata hâli |
|---|---|---|---|---|
| 1 | Kök dizinleri listele | Kütüphane kökü + alt klasörler | dizin listesi | Erişim yoksa atla + kaydet |
| 2 | Uzantı/ima filtresi | Yalnızca desteklenen biçimler | aday liste | Tanımsız dosya: işaretle, geçme |
| 3 | Boyut + son değiştirme | Dosya metadatası | satır | Hata → ayrı liste |
| 4 | Sıralı okuma (isteğe bağlı) | Bozuk/düzgün kontrol | sonuç | Hata → ayrı liste |
| 5 | Özet (isteğe bağlı) | Büyük dosyalarda maliyetli | parmak izi | Ertelenebilir |
| 6 | Kümelenme | Aynı özet/boyut → tekrar adayı | küme listesi | — |
| 7 | İndekse yazma | Satır satır ekle (güncelleme değil) | indeks | Kesinti → kaldığı yerden |
| 8 | Rapor | Sayım + süre + hata + tekrarlar | rapor | — |

### §2.3 Tarama sırası (erişim profiline göre)

| # | Kural | Neden |
|---|---|---|
| 1 | Taramayı **ses çalarken** başlatma | §5.3 + §6.2 (`hdd-kapsam.md`) — atlanan kare riski |
| 2 | Dizin listesini derleyip **sıraya** al | Rastgele dizin ziyareti seek'i katlar |
| 3 | Aynı diskin dosyalarını **konum birliğine** göre sırala | Komşu dosyalar ardışık okunur |
| 4 | Büyük dosyalardan önce küçükleri değil, **akış** düşünülerek sırala | Küçük dosya kalabalığı kuyruğu doldurur |
| 5 | Toplam okuma bütçesini yaz (GB ve süre) | Diski kaç GB meşgul edeceği önceden bilinmeli |
| 6 | İşi kuyruğa al, gerçek zamanlı yola **asla** bindirme | Çıkış tamponu 512/48k = 10,67 ms |
| 7 | Kesişimli (interleaved) iki taramayı aynı diskte çalıştırma | Ortak kuyruk = iki kat seek |
| 8 | Sonucu **geçici** yaz, bitince atomik olarak değiştir | Yarım indeks = bozuk kütüphane |

### §2.4 Karar noktaları

| # | Soru | Seçenekler | Bu konudaki öneri |
|---|---|---|---|
| 1 | Tarama hangi sıklıkla? | açılışta / günlük / elle | Elle + günlük (Q-HO3) |
| 2 | Özet alınsın mı? | evet / hayır | Değişmeyen albümlerde tek sefer |
| 3 | Süre bilgisi nereden? | dış araç / imleme | ⚠️ araç seçilmedi |
| 4 | İndeks nereye? | Ana disk / SSD sıcak katman | `ssd-kapsam.md` §6.3 önerisi |
| 5 | Kayıp/bozuk dosya? | rapor + atla | Bölüm 2.2/4 |
| 6 | İzin hatası? | atla + rapor | sessizce yutma **yasak** |

## §3 Streaming Buffer Örneği (aritmetik + plan şablonu)

### §3.1 Zaman tabanı (sabitlerden türetilmiş)

| # | Sabit | Değer | Kaynak |
|---|---|---|---|
| 1 | Örnekleme hızı | 48000 Hz | sartname (ölçüm PLANNED) |
| 2 | Tampon boyu | 512 örnek | sartname (ölçüm PLANNED) |
| 3 | Kare süresi | 512 ÷ 48000 = **10,667 ms** | aritmetik (S2) |
| 4 | Saniyede kare | 48000 ÷ 512 = **93,75 kare/s** | aritmetik (S2) |
| 5 | Örnekleme aralığı | 1 ÷ 48000 = **0,0208 ms** | aritmetik (S2) |
| 6 | 10 ms disk gecikmesi | 0,01 × 48000 = **480 örnek** | aritmetik (S2) |
| 7 | 480 ÷ 512 | = **%93,75** | tek gecikme kareyi neredeyse doldurur |

### §3.2 Okuma penceresi planı (şablon — ölçüm değil)

| # | Aşama | Ne | Dikkat |
|---|---|---|---|
| 1 | Üretici (dosya okuyucu) | Diskten bloklar halinde okuma | Blok boyu **sektör/küme** katı (§4.2 hdd-kapsam) |
| 2 | Önceden doldurma (prefetch) | Çalınan kareden **en az 1 kare ilerisini** hazırla | Tek kare gecikme = tamponun %94'ü |
| 3 | Ara tampon (ring) | Üretici ↔ tüketici arası | Yalnız tek yazar/tek okuyucu (lock-free kural) |
| 4 | Kritik uyarı | Üretici geride kalırsa **dolu bölgeyi geri okuma** | Kaçırılan kare = duyulabilir kesinti |
| 5 | Düşük su seviyesi (low-water) | Doluluğun altına inince ek okuma tetikle | Eşik ölçüm gerektirir → ⚠️ |
| 6 | Çıkış kuyruğu | ASIO/WASAPI'ye teslim | `[[output-kapsam]]` sahibi |
| 7 | Hata yolu | Okuma başarısız → sessizce sıfırla mı, yoksa kare mi kaybedilir | Karar açık (Q-HO2) |

### §3.3 HDD için sonuç tablosu

| # | Senaryo | Disk davranışı | Öneri |
|---|---|---|---|
| 1 | Sıralı tek akış | İstikrarlı | HDD uygun (§6.2/1) |
| 2 | Sıralı + arka planda tarama | Paylaşımlı seek | Taramayı ertele (§2.3/1) |
| 3 | Karışık/atlama oynatma | Her atlamada seek | Çalışma seti SSD'ye (ssd-kapsam) |
| 4 | Aynı anda 2+ oda akışı | Çakışan okuma | Önceden yükleme bütçesi (§3.1/6) |
| 5 | Disk park/uyku sonrası ilk okuma | Uyanma gecikmesi | Güç yönetimi ayarı (⚠️) |
| 6 | SSD'ye geçiş | Gecikme ortadan kalkar | `[[ssd-kapsam]]` §6 |

### §3.4 Doğrulama ölçütleri (nerede sınanır)

| # | Ölçüt | Nasıl | Sahip |
|---|---|---|---|
| 1 | Kare kaybı sayısı (underrun) | Çıkış katmanı sayacı | `[[output-kapsam]]` (PLANNED) |
| 2 | Diskten okuma süresi dağılımı | Zamanlama ölçümü | K003-audio / performans ölçümü ⚠️ |
| 3 | 10 ms üstü gecikme olayı | Kayıt | `hdd-dogrulama.md` kapıları |
| 4 | Tarama sırasında kayıp | Karşılaştırma | §2 + §3 birlikte |

## §4 Yedek Rotasyon Adımları (3-2-1 uygulaması)

### §4.1 Ortam/kopya matrisi (doldurulacak)

| # | Kopya | Ortam | Konum | Durum |
|---|---|---|---|---|
| 1 | 0 — çalışan | Ana depolama | Çalışma makinesi | tanımlı |
| 2 | 1 — yerel yedek | Ayrı disk/kasa | Aynı bina, ayrı fiziksel cihaz | ⚠️ kurulmadı |
| 3 | 2 — uzak yedek | Farklı ortam + farklı konum | Fiziksel ayrı | ⚠️ kurulmadı |
| 4 | 3 (yedek üstü) | Değişmeyen arşiv | Salt-okunur/ayrı | ⚠️ karar yok |

### §4.2 Rotasyon takvimi (GFS benzeri — sıklıklar üst karar)

| # | Kova (bucket) | Ne saklanır | Önerilen sıklık | Saklama |
|---|---|---|---|---|
| 1 | Günlük | Değişen veri (indirme, liste, ayar) | günlük | 7–14 kova |
| 2 | Haftalık | Haftanın son hâli | haftalık | 4–8 kova |
| 3 | Aylık | Ay sonu durumu | aylık | 12 kova |
| 4 | Yıllık/olay | Koleksiyon dönüm noktaları | olay bazlı | kalıcı |
| 5 | Değişmeyen albüm | İlk kopyadan sonra **salt okunur** | tek sefer | kalıcı (değişmezse) |

> ⚠️ VERIFICATION REQUIRED: Sıklık ve saklama süreleri **öneridir** — Q-HO3
> kapanmadan bu takvim bağlayıcı değildir.

### §4.3 Bir yedekleme görevinin adımları

| # | Adım | Aksiyon | Ön koşul | Kayıt |
|---|---|---|---|---|
| 1 | Kapsamı belirle | Hangi kova / hangi dizinler | Kova tablosu (§4.2) | görev kimliği |
| 2 | Hedefi doğrula | Hedef disk bağlı mı, yeterli alan var mı | Alan hesabı (`hdd-kapsam` §19.1) | alan çıktısı |
| 3 | Kaynağı kilitleme (mantıksal) | İş çakışmasın | Boş saat penceresi | başlangıç saati |
| 4 | Artımlı mı tam mı? | Değişen dosyalar + ilk kez tam | §4.2 kuralı | mod |
| 5 | Kopyala | Dosya-bazlı, hata durumunda duraklat | — | ilerleme |
| 6 | Bütünlüğü doğrula | Boyut + özet (örneklem veya tam) | §6.2/5 mantığı | eşleşme % |
| 7 | Sonucu işaretle | Görev günlüğü: BAŞARILI/BAŞARISIZ | — | satır + süre |
| 8 | Eski kovaları sil | Saklama süresi dolan | §4.2 saklama | silinen kova listesi |
| 9 | Raporla | Değişen GB, süre, hata | — | rapor |
| 10 | Kanıta bağla | `hdd-dogrulama.md` §13.1 takvimi | — | tarihli satır |

### §4.4 Döngü (rotation) kuralları

| # | Kural | Açıklama |
|---|---|---|
| 1 | İki kova aynı anda güncellenmez | Çakışan yazma = yarım kova |
| 2 | Yedek hedefi **ana diske** geri yazmaz | §7.2 ihlali |
| 3 | Değişmeyen içerik yeniden kopyalanmaz | Boyut/özet değişmediyse atla |
| 4 | Silme = en eski kovadan | Yeni daima korunur |
| 5 | Silme öncesi **en az bir** başka kopya doğrulanır | Tek kopya kalan yerde silme yasak |
| 6 | Uzak kopya ayrı zamanlama | Aynı anda hem yerel hem uzak = uzun pencere |
| 7 | Kesinti sonrası yeniden başlar | Bölme/başlatma noktası kaydedilir |
| 8 | Başarısız görev **sessiz geçilmez** | Hata yüzeyi açık tutulur |

### §4.5 Rotasyon tatbikatı (kapanış adımı)

| # | Adım | Beklenen kanıt |
|---|---|---|
| 1 | En eski kovadan rastgele N dosya seç | Liste + tarih |
| 2 | Özetleri çıkar | Beklenen özetler |
| 3 | Ayrı hedefe geri al | Geri alınan dosyalar |
| 4 | Özet karşılaştırma | **%100 eşleşme** |
| 5 | Süreyi ölç | RTO değeri |
| 6 | Rapora yaz + imza | `hdd-dogrulama.md` §6.3 kapısı |

## §5 Resmi Kaynak Örnekleri (erişilebilirlik kanıtlı)

| # | Kaynak | URL | Tarih | Durum | Ne sağlar |
|---|---|---|---|---|---|
| 1 | Seagate bilgi/destek tabanı | https://knowledge.seagate.com/ | 2026-10-13 | ✅ HTTP 200 | Test/SMART başlığı için giriş kapısı |
| 2 | StorageReview | https://www.storagereview.com/ | 2026-10-13 | ✅ HTTP 200 | Bağımsız inceleme/ölçüm sitesi |
| 3 | Seagate destek | https://www.seagate.com/support/ | 2026-10-13 | ✅ HTTP 200 | Üretici destek kökü |
| 4 | Western Digital destek | https://www.westerndigital.com/support | 2026-10-13 | ✅ HTTP 200 | Üretici destek kökü |

**Doğrulama yöntemi:** `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`
→ HTTP 200. **Erişilebilirlik kanıtıdır; içerik okunmadı** → bu dosyadaki adımlar
için içerik iddiası üretilmez (⚠️ VERIFICATION REQUIRED).

## §6 Kapsam Dışı

| # | Konu | Sahip |
|---|---|---|
| 1 | Gerçek C++ kodu (ring buffer, tarama) | K003-audio / L0 (PLANNED, repo 0 dosya) |
| 2 | İndeks şeması / veritabanı | K014-media · K000 dosya sistemi |
| 3 | Dosya biçimi çözümleme (metaveri) | K014-media |
| 4 | Yedekleme yazılımı seçimi | ⚠️ üst karar (Q-HO3) |
| 5 | RAID kurulumu | K001 envanter + Q-HK3 |
| 6 | Doğrulama kapıları | `hdd-dogrulama.md` |

## §7 Terim Sözlüğü

| # | Terim | Tanım |
|---|---|---|
| 1 | Tarama (scan) | Dizin/dosya sistematik gezme |
| 2 | İndeks | Dosya listesinin kompakt karşılığı |
| 3 | Prefetch | İhtiyaçtan önce okuma |
| 4 | Ring buffer | Dairesel tampon (tek yazıcı/okuyucu) |
| 5 | Low-water mark | Doluluğun alt eşiği — uyarı tetiği |
| 6 | Underrun | Veri yetmeme → kare kaybı |
| 7 | Kare (frame) | 512 örnek = 10,667 ms |
| 8 | Artımlı (incremental) | Yalnız değişenleri kopyalama |
| 9 | Tam yedek (full) | Kapsamın tümünü kopyalama |
| 10 | Kova (bucket) | Rotasyon seviyesi (günlük/haftalık/…) |
| 11 | GFS | Büyükbaba-baba-oğul rotasyon şeması |
| 12 | 3-2-1 | 3 kopya · 2 ortam · 1 uzak |
| 13 | Özet (hash) | Dosya bütünlüğü parmak izi |
| 14 | Atomik güncelleme | Yarım yazımın görünmemesi |
| 15 | RTO | Kurtarma süresi hedefi |

## §8 Çapraz Referanslar

| # | Hedef | İlişki |
|---|---|---|
| 1 | `hdd-kapsam.md` §4-§7 · §13 | İddia ve aritmetik kaynağı |
| 2 | `hdd-dogrulama.md` §4 · §6 · §13.1 | Tarama protokolü · tatbikat · takvim |
| 3 | `[[hdd/index]]` | Düğüm künyesi |
| 4 | `[[ssd-ornekler]]` | Kardeş: indeksleme · cache · sağlamlık izleme |
| 5 | `[[ssd-kapsam]]` | Sıcak katman / cache stratejisi |
| 6 | `[[output-kapsam]]` | Çıkış tamponu sahibi (512/48k) |
| 7 | `[[isletim-sistemi-core-kapsam]]` | Dosya sistemi davranışı sınırı |
| 8 | `[[donanaım-type-kapsam]]` | Envanter UNKNOWN (Q-T1) |
| 9 | `[[pcm3168a-codec]]` · `[[ak4458-dac]]` | Zincirin DAC sonu (depolama → ses) |
| 10 | `[[tpa3255-amplifier]]` | Amfi kaydı (topoloji Class AB sabit) |

## §9 Tarama ve Tampon Uygulama Defteri

### §9.1 Tarama çıktı şablonu (boş form)

| # | Alan | Değer |
|---|---|---|
| 1 | Tarih / saat · iş kimliği | |
| 2 | Kök dizin(ler) | |
| 3 | Bulunan dosya sayısı · toplam boyut | |
| 4 | Uzatma dağılımı (adet + boyut) | |
| 5 | Okunan dosya sayısı · okunan GB | |
| 6 | Başlangıç – bitiş – süre | |
| 7 | Ortalama okuma hızı (MB/s) | |
| 8 | Hata listesi (erişim/format/bozuk) | |
| 9 | Tekrar adayları (küme sayısı) | |
| 10 | İndeks yazım yolu + atomik değiştirme | |
| 11 | Kesinti var mı · devam noktası | |
| 12 | Karar + imza | |

### §9.2 Tarama hata senaryoları

| # | Senaryo | Belirti | Davranış |
|---|---|---|---|
| 1 | Erişim reddi (izin) | tek dosya atlandı | Listeye yaz, **sessizce yutma** |
| 2 | Dosya yolda kayboldu | yarım tarama | Yeniden dene + raporla |
| 3 | Bozuk başlık | çözümlenemedi | Ayırıcı listeye al (`hdd-dogrulama` §4) |
| 4 | Disk bağlantısı koptu | iş yarım | Devam noktası → sonra yeniden |
| 5 | Alan dolu (indeks yazımı) | yazma başarısız | Eski indeksi **silme**; yeniyi geçici tut |
| 6 | Aynı dosya iki kez listelendi | yinelenen kayıt | Kümeleme adımında ayıkla |
| 7 | Sembolik bağlantı / döngü | sonsuz döngü | Ziyaret kaydı tut, atla |
| 8 | Çok uzun yol | yazma hatası | Uzun yol desteği ⚠️ araç bağımlı |
| 9 | Değişen dosya (kopyalama sırasında) | boyut uyuşmaz | İşaretle, sonraki turda tekrar |
| 10 | Sahte "başarılı" rapor | hata sayacı 0 ama dosya eksik | Rapor + sayımlar birlikte doğrulanır |

### §9.3 Tarama önceliklendirme matrisi

| # | İş | Öncelik | Zaman penceresi | Gerekçe |
|---|---|---|---|---|
| 1 | Çalma listesi okuma (canlı) | en yüksek | her zaman | gerçek zamanlı yol |
| 2 | Son eklenen albümlerin indekslenmesi | yüksek | ekleme sonrası | kullanıcı beklentisi |
| 3 | Kütüphane tam tarama | düşük | boş saat | §2.3/1 |
| 4 | Yedekleme | düşük-orta | boş saat | §4.3/3 |
| 5 | Özet (hash) yenileme | en düşük | gece | yoğun I/O |
| 6 | Yüzey doğrulama | en düşük | bakım penceresi | `hdd-dogrulama` §4 |
| 7 | Aynı anda 2+ düşük öncelikli | — | serileştir | ortak kuyruk |

### §9.4 Tampon bütçesi hesabı

| # | Hesap | Sonuç | Yorum |
|---|---|---|---|
| 1 | 1 kare | 512 örnek / 10,667 ms | teslim penceresi |
| 2 | 2 kare | 1024 örnek / 21,33 ms | asgari güvenlik |
| 3 | 4 kare | 2048 örnek / 42,67 ms | tipik ara tampon |
| 4 | 8 kare | 4096 örnek / 85,33 ms | gecikme artar — ses yolunda maliyetli |
| 5 | 16 kare | 8192 örnek / 170,67 ms | yalnız önceden yükleme için |
| 6 | 10 ms disk gecikmesi | 480 örnek | tek karelik payın %94'ü |
| 7 | 50 ms disk gecikmesi | 2400 örnek | ~4,7 kare — 4 karelik tamponu **aşar** |
| 8 | 100 ms (uyanma) | 4800 örnek | ~9,4 kare — ön yükleme yoksa **kesin** kayıp |

**Sonuç:** mekanik diskte 50–100 ms'lik olaylar (park uyanması, arka plan
taraması) 4 karelik ara tamponu aşar → **ön yükleme + iş planlaması zorunludur**.

### §9.5 Akış şeması (metin)

```text
[Disk/HDD] --blok okuma--> [Önceden yükleme kuyruğu]
        |                            |
        | (risk: seek/park)          v
        |                  [Ara ring buffer (N kare)]
        |                            |
        |                            v
        |                  [DSP / efekt zinciri]  --> 32-bit float (ADR-017)
        |                            |
        |                            v
        |                  [Çıkış kuyruğu: 512 örnek @ 48 kHz]
        |                            |
        |                            v
        |                  [ASIO/WASAPI] --> I2S/TDM
        |                            |
        v                            v
  arka plan işler            [PCM3168A / AK4458] --> analog --> Class AB
 (tarama, yedek)                  (A&Yok: PCM5122)
```

### §9.6 Ring buffer bölge tablosu

| # | Bölge | İçerik | Kural |
|---|---|---|---|
| 1 | Yazıcı ucu | Üreticinin doldurduğu kare | Tek yazıcı (kural 1) |
| 2 | Okuyucu ucu | Tüketiciye verilen kare | Tek okuyucu |
| 3 | Dolu bölge | Hazır kareler | Su seviyesi izlenir |
| 4 | Boş bölge | Sıra bekleyen alan | Dolunca üretici duraklar |
| 5 | Kritik bölge | Tamponun %90'ı dolu | Uyarı eşiği (sayı ⚠️ ölçüm) |
| 6 | Taşma | Üretici geç kaldı | Kayıp kare sayacı **zorunlu** |
| 7 | Sıfırlama | Hata sonrası | Yalnız duraklama anında |

### §9.7 Düşük su seviyesi senaryoları

| # | Senaryo | Eşik davranışı | Öncelik |
|---|---|---|---|
| 1 | Normal akış | Kuyruk 2+ kare | — |
| 2 | İlk okuma (dosya açılışı) | Önceden okuma zorunlu | yüksek |
| 3 | Park/uyku sonrası | Uyanma gecikmesi beklenir | §9.4/8 |
| 4 | Arka plan taraması başladı | Üretici yavaşlar | işi ertele |
| 5 | Disk hatası | Okuma başarısız | hata yolu (§9.2/4) |
| 6 | Aynı anda ikinci oda | Bütçe iki kat | §6.1 (`hdd-kapsam.md`) |
| 7 | SSD'ye geçiş | Gecikme payı düşer | `[[ssd-kapsam]]` |

### §9.8 Çıkış hata yolları

| # | Hata | Doğru tepki | Yanlış tepki |
|---|---|---|---|
| 1 | Okuma zaman aşımı | Kayıp kare sayacı + rapor | Sessiz sıfırlama |
| 2 | Dosya sonu erken | Kuyruğu sessizlikle doldur | Sahte veri üret |
| 3 | Bozuk blok | Ayrıcı liste + kare atla | Çalmayı durdurmadan say |
| 4 | Üretici hiç gelmiyor | Durdur + kapat uyarısı | Sonsuz bekleme |
| 5 | Çıkış cihazı kaybı | ASIO/WASAPI fallback (K003) | Yeniden başlatma döngüsü |
| 6 | Tampon taşması | Sayaç + görsel uyarı | Sayacı gizle |

## §10 Yedekleme Görev Günlüğü ve Alan Hesabı

### §10.1 Alan hesabı örneği (aritmetik)

| # | Hesap | Sonuç |
|---|---|---|
| 1 | Ana kütüphane 1,0 TiB | 1 × 1,0 = 1,0 TiB |
| 2 | Yerel yedek (kopya 1) | + 1,0 TiB |
| 3 | Uzak yedek (kopya 2) | + 1,0 TiB |
| 4 | Toplam (3-2-1) | **3,0 TiB** |
| 5 | Günlük artımlı (değişen %1 × 5 gün) | ~0,05 TiB ek yığın |
| 6 | Haftalık kova | son durum (artımlı değil tam kabul edilirse +1,0) ⚠️ politika |
| 7 | Doluluk hedefi %75 | toplam gerekli = 3,0 ÷ 0,75 = **4,0 TiB** |
| 8 | RAID 1 hâli | 2 diskte 1,0 TiB kullanılabilir kapasite |

> ⚠️ Değerler **örnek senaryodur** (1,0 TiB varsayımı) — gerçek kütüphane
> boyutu UNKNOWN (`hdd-kapsam.md` §19.2).

### §10.2 Görev günlüğü şablonu

| # | Alan | Değer |
|---|---|---|
| 1 | Görev kimliği + kova | |
| 2 | Başlangıç / bitiş / süre | |
| 3 | Kapsam (dizin sayısı, dosya sayısı) | |
| 4 | Kopyalanan GB / atlanan GB (değişmemiş) | |
| 5 | Doğrulanan dosya sayısı / özet eşleşmesi | |
| 6 | Hatalar (erişim, alan, bağlantı) | |
| 7 | Silinen eski kovalar | |
| 8 | Sonuç (BAŞARILI/BAŞARISIZ) | |
| 9 | Sonraki görev zamanı | |
| 10 | İmza | |

### §10.3 Kova matrisi doldurma örneği

| # | Kova | Kaynak | Hedef | Saklama | Silme kuralı |
|---|---|---|---|---|---|
| 1 | Günlük-1 | Dün | Yerel disk | 1 gün | Bugün dolunca |
| 2 | Günlük-2 | Evvelki | Yerel disk | 2 gün | sıra ile |
| 3 | Hafta-4 | 4 hafta önce | Yerel + uzak | 1 ay | en eski haftalık |
| 4 | Ay-12 | 12 ay önce | Uzak | 1 yıl | en eski aylık |
| 5 | Arşiv | Koleksiyon dönümü | Uzak + salt-okunur | kalıcı | silinmez |

### §10.4 Rotasyon kontrol listesi

| # | Kontrol | Geçme |
|---|---|---|
| 1 | 3 kopya gerçekten var mı? | evet |
| 2 | En az 2 farklı ortam mı? | evet |
| 3 | 1 kopya farklı fiziksel konumda mı? | evet |
| 4 | Değişmeyen içerik salt-okunur işaretli mi? | evet |
| 5 | En eski kova silinmeden önce doğrulandı mı? | evet |
| 6 | Son tatbikat tarihi ve eşleşme % kayıtlı mı? | evet |
| 7 | Başarısız görev görünür mü (gizlenmemiş)? | evet |
| 8 | Ana disk bozulsa kurtarma süresi yazıldı mı? | evet (Q-HO3) |

## §11 Sık Yapılan Hatalar, SSS ve Uygulama Sırası

### §11.1 Sık yapılan hatalar

| # | Hata | Belirti | Doğrusu |
|---|---|---|---|
| 1 | Taramayı çalarken başlatmak | atlama/kesinti | §2.3/1 |
| 2 | Rastgele dizin sırası | seek fırtınası | §2.3/2-3 |
| 3 | İndeksi doğrudan ana dosyaya yazmak | yarım indeks | §2.3/8 atomik |
| 4 | Tek kare ön yükleme | 50 ms gecikmede kayıp | §9.4/7-8 |
| 5 | Yedeği ana diske almak | §7.2 ihlali | §4.4/2 |
| 6 | Yedek alıp doğrulamamak | kurtarılamayan veri | §4.5 |
| 7 | Tek kova kala eski kovayı silmek | veri boşluğu | §4.4/5 |
| 8 | Hatalı görevi gizlemek | sahte "başarılı" | §9.2/10 · §10.2/8 |
| 9 | Hash'i her taramada almak | gereksiz GB okuma | §2.4/2 |
| 10 | Disk uyusunu açık bırakmak | uyanma kesintisi | `hdd-kapsam.md` §10/5 |
| 11 | Örnek sayıyı gerçek boyut sanmak | bütçe hatası | §10.1 uyarısı |
| 12 | Repo'da kod yokken "kod eklendi" demek | sahte kanıt | §1 |

### §11.2 Sık sorulanlar

| # | Soru | Yanıt |
|---|---|---|
| 1 | HDD'de canlı çalma mümkün mü? | Evet — **ön yükleme** ile; ama SSD daha güvenli (§9.7) |
| 2 | Tarama ne kadar sürer? | Kapasite + hız ile hesaplanır; uydurulmaz (ölçüm gerekir) |
| 3 | Özet zorunlu mu? | Değişmeyen içerikte tek sefer; her turda değil (§2.4/2) |
| 4 | İndeks nereye yazılır? | Yeniden üretilebilir sayılır → sıcak katman (ssd-kapsam) |
| 5 | 3-2-1 yerine 2 kopya yeter mi? | Yeterli değil; gerekçe ile üst karar |
| 6 | RAID 1 yedek midir? | Hayır (`hdd-kapsam.md` §7.1) |
| 7 | Rotasyon sıklığı? | ⚠️ Q-HO3 — öneri §4.2, bağlayıcı değil |
| 8 | Ne zaman SSD'ye geçilir? | Rastgele erişim baskınsa (`ssd-kapsam.md` §6) |
| 9 | Kod ne zaman yazılır? | L0 (Neva Engine) üretilince — şu an repo 0 C++ (§1) |
| 10 | Doğrulama kapıları nerede? | `hdd-dogrulama.md` §9 |

### §11.3 Uygulama sırası (checklist)

| # | Adım | Çıktı |
|---|---|---|
| 1 | Envanter + kütüphane boyutu (Q-HK1) | sayılar |
| 2 | Katman şemasını çiz (§6.3 hdd-kapsam) | şema |
| 3 | Tarama akışını seç (§2.2) | akış |
| 4 | Tampon bütçesini hesapla (§9.4) | tablo |
| 5 | Arka plan iş saatlerini belirle (§9.3) | takvim |
| 6 | 3-2-1 + kova tablosunu doldur (§4.1-§4.2) | matris |
| 7 | Görev günlüğü formunu bağla (§10.2) | form |
| 8 | Doğrulama kapılarını uygula | `hdd-dogrulama.md` |
| 9 | Tatbikat yap (§4.5) | rapor |
| 10 | Sonuçları vault'a yaz | imzalı satır |

### §11.4 Adım → kapı eşlemesi

| # | Bu dosyadaki adım | Hangi kapıda doğrulanır |
|---|---|---|
| 1 | §2.2 tarama akışı | `hdd-dogrulama.md` §4 (yüzey) + §8 (form) |
| 2 | §3.1 tampon aritmetiği | `hdd-dogrulama.md` §2 (S2) |
| 3 | §3.3 HDD davranışı | `hdd-dogrulama.md` §4.3 (hız) |
| 4 | §4.3 yedek adımları | `hdd-dogrulama.md` §6 (tatbikat) |
| 5 | §4.5 rotasyon tatbikatı | `hdd-dogrulama.md` §6.2 |
| 6 | §9.1-§9.8 uygulama defteri | `hdd-dogrulama.md` §13.1 (takvim) |
| 7 | §10.1 alan hesabı | `hdd-kapsam.md` §19.2 (bütçe) |
| 8 | §11.3 checklist | Kapı 1–4 (`hdd-dogrulama.md` §9) |

### §11.5 Terim Ekleri (İngilizce ↔ Türkçe)

| İngilizce | Türkçe | Kısa tanım |
|---|---|---|
| library scan | kütüphane taraması | dizin/dosya gezinme işi |
| file indexer | dosya indeksleyici | liste üreten bileşen |
| streaming buffer | akış tamponu | üretici-tüketici arası tampon |
| ring buffer | dairesel tampon | tek yazıcı/okuyucu yapısı |
| prefetch | önceden okuma | ihtiyaçtan önce okuma |
| low-water mark | düşük su seviyesi | doluluk alt eşiği |
| underrun | veri yetmeme | kare kaybı |
| frame / buffer period | kare / tampon dönemi | 512 örnek = 10,667 ms |
| backup rotation | yedek rotasyonu | kova döngüsü |
| grandfather-father-son | büyükbaba-baba-oğul | kademeli saklama |
| offsite backup | uzak yedek | farklı fiziksel konum |
| full / incremental | tam / artımlı | kapsam farkı |
| restore test | geri yükleme testi | tatbikat |
| checksum / hash | sağlama toplamı / özet | bütünlük parmak izi |
| atomic replace | atomik değiştirme | yarım yazımın görünmemesi |
| duty cycle | görev döngüsü | boş saat planı |
| seek storm | seek fırtınası | rastgele erişim patlaması |
| media failure | ortam arızası | donanım kaynaklı veri kaybı |
| human error | insan hatası | yanlışlıkla silme — 3-2-1'in asıl nedeni |
| window of exposure | risk penceresi | son yedekten bugüne kadar geçen süre |

---

## §12 Dürüstlük Bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler yalnız doğrulanmış
kaynaklar içerir; doğrulanamayan iddialar ⚠️ VERIFICATION REQUIRED ile
işaretlidir. §1'de "kod yok" ölçümle kanıtlanmıştır; §2–§4 adımları
**şablondur ve çalışan kod olarak sunulmamıştır**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
