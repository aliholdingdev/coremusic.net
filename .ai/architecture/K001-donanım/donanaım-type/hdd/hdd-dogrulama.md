---
node_id: hdd-dogrulama
node_type: module
status: draft
parent_id: K001-donanım/donanaım-type/hdd
children: []
depends_on:
  - hdd-kapsam
  - K001-donanım/donanaım-type/donanaım-type-dogrulama
evidence:
  - "https://knowledge.seagate.com/articles/smart (HTTP 200, 2026-10-13)"
  - "https://www.seagate.com/support/downloads/seatools/ (HTTP 200, 2026-10-13)"
  - "K001-donanım/donanaım-type/donanaım-type-dogrulama.md §1-§4"
validation: "draft — 2026-10-13 yazıldı · ≥500 satır ölçümü yazım sonrası · fiziksel ölçüm YAPILMADI (tüm S1 kapıları açık)"
open_questions:
  - "Q-HD1: SMART atama (attribute) anlamları üreticiye göre değişir — disk markası bilinmiyor → ⚠️ VERIFICATION REQUIRED"
  - "Q-HD2: Gürültü ölçümü için mikrofon/mesafe/ortam koşulu tanımsız — ölçüm tezgâhı yok (S1 açık)"
  - "Q-HD3: Yedek kurtarma tatbikatı henüz yapılmadı — tarihli kayıt YOK"
group: kanit
---

# hdd-dogrulama — Mekanik Disk Doğrulama Kapıları

> **Amaç:** `hdd-kapsam.md` içindeki iddiaları **kapı kapı** doğrulamak. Bu dosya
> **kanıt** dosyasıdır: her bölüm bir test protokolü ve geçme koşulu içerir.
> Doğrulanmamış hiçbir sonuç "geçti" sayılmaz.

## §1 ELI15

Bir disk için dört soru sorulur ve dördü de **ölçülmeden** yanıtlanmaz:
(1) sağlığı yerinde mi (SMART), (2) yüzeyinde bozuk yer var mı (bad-sector
taraması), (3) odada duyulabilir derecede ses/vibrasyon yapıyor mu (gürültü
ölçümü), (4) yedeği gerçekten geri alınabiliyor mu (tatbikat). Bu dosya bu dört
sorunun **adımlarını, kayıt formunu ve geçme eşiklerini** yazar.

> ⚠️ VERIFICATION REQUIRED: Bu dosyada **hiçbir gerçek ölçüm sonucu yoktur** —
> tezgâh kurulmadı, disk envanteri bilinmiyor (K001 Q4). Tüm S1 seviyesi
> kapıları **AÇIK**tır; buradaki protokoller uygulanacak iş listesidir,
> sonuç raporu değildir.

## §2 Kanıt Seviye Şeması (bu dosyada kullanılır)

| # | Seviye | Anlamı | Örnek | Kapı durumu |
|---|---|---|---|---|
| 1 | S1 | Fiziksel ölçüm / gözlem | Gürültü dB, bad-sector sayısı | ❌ AÇIK (tezgâh yok) |
| 2 | S2 | Aritmetik olarak türetilmiş | 60000÷RPM, 512÷48000 | ✅ kapandı (`hdd-kapsam.md` §4-§5) |
| 3 | S3 | Bu oturumda URL doğrulandı (HEAD 200) | 2 web kaynağı (§12) | ✅ kapandı |
| 4 | S4 | Vault disk kanıtı (okunan dosya/satır) | `hdd/index.md` 47 satır | ✅ kapandı |
| 5 | S5 | Üst otorite / ADR / sabit kural | `donanım-anayasa.md` §10/3 | ✅ kapandı |

**Kural:** Bu dosyadaki bir "GEÇTİ" yazısı yalnızca **S1 kanıtı** ile yazılabilir.
S2–S5 tek başına ölçüm yerine geçmez.

## §3 SMART Doğrulama Kapıları

### §3.1 SMART nedir — sınırları

| # | Konu | Doğru anlayış | Sınır |
|---|---|---|---|
| 1 | Ne yapar | Diskin kendi saydığı sayaçları raporlar | **Önleme yapmaz**, yalnızca işaret verir |
| 2 | Ne yapmaz | Anlık arızayı garanti etmez | SMART "temiz" ≠ sonsuz ömür |
| 3 | Standartlaşma | Birkaç genel alan standarttır; **çoğu atama üreticiye özeldir** | Anlam okuması marka bilgisi ister → Q-HD1 |
| 4 | Eşikler | Üretici "yanıp-sönen" (failing) eşiğini belirler | Marka bilinmeden eşik yazılmaz |
| 5 | Kayıt biçimi | Normalized (1–253/254/255) + raw (sayı/saat) | İki alan birbirine karıştırılmaz |
| 6 | Kim okur? | İşletim sistemi araçları / üretici yazılımı | Araç seçimi §3.4 |

### §3.2 İzlenecek sayaçlar (üreticiye özeldir — ⚠️ doğrulanacak)

| # | Yaygın atama | Olası anlamı | Ne zaman bakılır | Durum |
|---|---|---|---|---|
| 1 | Yeniden ayrılan sektör sayacı | Yüzeyin kötü yerlerini fabrika yedeğine taşıma | Her kontrolde | ⚠️ anlamları üreticiye özel (Q-HD1) |
| 2 | Bekleyen (pending) sektör | Henüz doğrulanamayan kötü sektör | Her kontrolde | ⚠️ |
| 3 | Çevrimdışı düzeltilemeyen | Kurtarılamayan hata | Her kontrolde | ⚠️ |
| 4 | Güç-deneme sayacı | Kaç kez açıldı | Dönemsel | ⚠️ |
| 5 | Çalışma saati (power-on hours) | Kullanım süresi | Dönemsel | ⚠️ |
| 6 | SATA hata sayacı (CRC) | Kabloya/bağlantıya işaret | Bağlantı hatasında | ⚠️ |
| 7 | Sıcaklık (raw) | Anlık ısı | Yük altında | ⚠️ (ölçüm §6) |
| 8 | Büyüme/hata oranı | Yüzey bozulma eğilimi | Dönemsel | ⚠️ |

**Yorum kuralı:** Bu tablo **araç için aday listesi**dir; hangi diskin hangi
atamayı taşıdığı marka/model ile belirlenir → envanter gelene kadar **ham yorum
yazılmaz** (Q-HD1 · UNKNOWN).

### §3.3 SMART kapı adımları (protokol)

| # | Adım | Alet | Kayıt | Geçme koşulu |
|---|---|---|---|---|
| 1 | Diski tanı (marka/model/firmware) | sistem bilgisi | seri no + firmware | Envanter satırı doldurulur |
| 2 | Tüm atamaları oku (tam döküm) | SMART okuyucu | ham dosya/döküm | Döküm kaydedildi (tarihli) |
| 3 | Anlık değer + eşik karşılaştırması | aynı araç | tablo | Hiçbir "failing" eşik aşımı yok |
| 4 | **Kısa kendi-kendini-test** (self-test) | üretici/OS aracı | başlangıç-bitiş + sonuç | "PASS" raporlanır |
| 5 | **Geniş kendi-kendini-test** (uzun) | aynı | süre + sonuç | PASS; hata yok |
| 6 | Test sırasında sıcaklık/ses gözlemi | elle gözlem + araç | not | Anormalse DUR |
| 7 | Öncesi/sonrası atama farkı | adım 2 farkı | delta tablosu | Bekleyen sektör artmadı |
| 8 | İmzalı kayıt | dosya | imza + tarih | Kapı yalnız imzayla kapanır |

**Kapı-1 (SMART):** adım 3 ve 4–5 **her ikisi de** PASS olmalı; yalnızca
"atamalar okundu" kapı sayılmaz.

### §3.4 Araç seçimi (dikkatli seçim — bağımlılık)

| # | Seçenek | Erişim | Not |
|---|---|---|---|
| 1 | İşletim sistemi gömülü SMART sorgusu | yerel | Erişim/kapsam farkları olabilir |
| 2 | Üreticinin kendi destek aracı | üretici sitesi | Atama yorumu için en güvenilir kaynak |
| 3 | Üçüncü taraf okuyucular | indirme | Sürüm/lisans kontrolü gerekir ⚠️ |
| 4 | Kullanıcı-atanan test yazılımı (ör. sektör tarama) | indirme | **Yazma** yapan testler veri siler → §4.2 |

**⚠️ VERIFICATION REQUIRED:** Hangi aracın bu ortamda kullanılacağı **seçilmedi**
(ürün adı/versiyonu yazılmadı). Araç adı, sürümü ve lisansı kararlaştıktan
sonra bu satır güncellenir — **uydurma araç adı yazılmaz**.

## §4 Bad-Sector Test Protokolü

### §4.1 Tarama türleri

| # | Tür | Ne yapar | Risk | Ne bulur |
|---|---|---|---|---|
| 1 | Salt-okunur tarama (read-only scan) | Tüm sektörleri okur | veriye dokunmaz | Bozuk/okunamayan sektör |
| 2 | Doğrulama taraması (verify) | Okuma + ECC kontrolü | veriye dokunmaz | Hata düzeltmeye yakın sektörler |
| 3 | Yüzey yazma testi (yaz + geri oku) | Doldurur, okur, temizler | **TÜM VERİYİ SİLER** | Yalnızca boştaki / yedekli diskte |
| 4 | Rastgele erişim stres | Karışık okuma | yüksek seek sesi | Mekanik/gecikme sorunları |
| 5 | Uzun süreli ateşleme ( soak ) | Saatlerce tekrar | ısınma | Sıcaklıkta ortaya çıkan hatalar |

**Yasak (güvenlik):** tür 3 yalnızca **içeriği tamamen boş ve yedeklenmiş**
diskte çalıştırılır; hdd-kapsam §7.2 ihlali varsa DUR.

### §4.2 Bad-sector tarama protokolü (adım adım)

| # | Adım | Koşul | Kayıt | Geçme |
|---|---|---|---|---|
| 1 | Yedeğin varlığını doğrula | §7.2 | yedek listesi + tarih | Yoksa DUR |
| 2 | Salt-okunur taramayı başlat | disk boşta olmalı (ses için) | başlangıç saati | — |
| 3 | İlerlemeyi ve hızı kaydet | her %10 | oran + tahmini bitiş | Okuma hızı aniden düşmüyorsa |
| 4 | Hataları topla (konum + tip) | — | hata listesi | **0 hata** → GEÇTİ |
| 5 | Hata varsa diski ayır | — | karar kaydı | Sektör aralığı + yeniden ayrılan sayaç |
| 6 | Erteleme/kaçırma hatalarını ayrıca say | — | sayaç artışı | Artış yoksa GEÇTİ |
| 7 | Sonuç + tarih + araç adı/sürümü | — | rapor | İmzalı rapor kapı-2'yi kapatır |

**Kapı-2 (yüzey):** 0 okuma hata VE bekleyen sektör sayacında artış yok → GEÇTİ.
Hata >0 ise disk **kütüphane ana diski olamaz** (yalnızca atılabilir/kurtarma
kullanımı; karar Q-HK3).

### §4.3 Tarama süresi bütçesi (dikkat)

| # | Gözlem | Sonuç |
|---|---|---|
| 1 | Tarama, diski **tamamen** meşgul eder | Ses çalarken/atlamalara neden olur → boş saate |
| 2 | Büyük kapasite = uzun tarama | Plan penceresi kapasiteyle büyür |
| 3 | Yazma yapan test = veri kaybı | §4.1/3 yasağı |
| 4 | Kesinti (elektrik/uyku) taramayı bozar | Kesintisiz planlama |
| 5 | Sonuç yalnız bir koşulda geçerlidir | Disk **o anki** hâlidir; sonraki arıza ayrı olay |

## §5 Gürültü ve Vibrasyon Ölçüm Doğrulaması

> Amaç: `hdd-kapsam.md` §9'daki "**gürültü bir ses kararıdır**" iddiasını
> **sayıya** çevirmek. Şu an sayı yok → kapı açık.

### §5.1 Ölçüm koşulu tanımı (zorunlu)

| # | Parametre | Tanım gereği | Değer |
|---|---|---|---|
| 1 | Ortam gürültüsü (boş) | Disk kapalıyken ölçüm | ⚠️ ölçülmedi |
| 2 | Disk açık bekleme | Disk çalışıyor, I/O yok | ⚠️ ölçülmedi |
| 3 | Disk sıralı okuma | Yoğun okuma | ⚠️ ölçülmedi |
| 4 | Disk rastgele okuma | Yoğun seek | ⚠️ ölçülmedi |
| 5 | Mesafe | Ölçüm mikrofonu ↔ disk rafı | ⚠️ tanımsız (Q-HD2) |
| 6 | Ağırlık eğrisi | A-weighted (dB(A)) — `donanım-anayasa.md` ölçüm yaklaşımıyla aynı | tanımlı |
| 7 | Ekipman | Mikrofon + seviye ölçer (model YOK) | ⚠️ seçilmendi |
| 8 | Sıcaklık / nem | Kayıt koşulu | ⚠️ |

### §5.2 Ölçüm protokolü

| # | Adım | Aksiyon | Kayıt |
|---|---|---|---|
| 1 | Ekipmanı bağla ve doğrula | bilinen ses kaynağıyla kontrol | ekipman kaydı |
| 2 | Ortam boş ölçümü al | disk kapalı | dB(A) arka plan |
| 3 | Bekleme ölçümü | disk açık, I/O yok | dB(A) |
| 4 | Sıralı okuma ölçümü | büyük dosya oku | dB(A) + tahsis |
| 5 | Rastgele okuma ölçümü | karışık okuma | dB(A) + tahsis |
| 6 | Farkı hesapla | adım 3–5 − adım 2 | net disk katkısı |
| 7 | Titreşim gözlemi | elle/rakamsal (⚠️ yöntem yok) | nitel not |
| 8 | Konum deneyi | ıslak montaj öncesi/sonrası | karşılaştırma |
| 9 | Karar | Kabul / iyileştir / taşı | imzalı karar |

### §5.3 Kabul eşiği (tartışmaya açık — üst karar bekliyor)

| # | Eşik | Dayanak | Durum |
|---|---|---|---|
| 1 | Disk katkısı ortam gürültüsünün **altında** kalmalı | mimari kural (algılanan detay) | ✅ kural olarak tanımlı |
| 2 | Sayısal dB(A) eşiği | — | ⚠️ YOK — uydurulmaz (Q-HD2) |
| 3 | Dinleme konumunda fark edilirlik | öznel dinleme testi | ⚠️ yöntem tanımsız |
| 4 | Ölçüm tekrar sayısı | — | ⚠️ tanımsız |

**Kapı-3 (gürültü):** yalnızca adım 1–8 tamamlandıktan ve sayısal sonuç
kaydedildikten sonra kapanabilir. Şu an **AÇIK**.

## §6 Yedekleme Geri-Kurtarma Testi (tatbikat)

### §6.1 Tatbikat türleri

| # | Tür | Ne denenir | Süre | Sıklık önerisi |
|---|---|---|---|---|
| 1 | Rastgele dosya geri alma | Seçilen N dosyanın birebir kurtarılması | kısa | sık |
| 2 | Klasör/ağaç geri alma | Dizin yapısının + izinlerin korunması | orta | orta |
| 3 | Tam kütüphane geri yükleme | Toplam verinin baştan sona kurtarılması | uzun | seyrek |
| 4 | Bozuk-senaryo kurtarma | Ana disk bozulmuş taklidi (ayrı makine/klasör) | uzun | nadir |
| 5 | Süre ölçümü | Ortalama geri dönüş süresi (RTO) | ek | tatbikatta |

> ⚠️ VERIFICATION REQUIRED: Hiçbir tatbikat **yapılmadı** (Q-HD3). Bu tablo
> **plan**tır; sonuç satırı tarihli rapor gelmeden doldurulmaz.

### §6.2 Tatbikat protokolü (adım adım)

| # | Adım | Aksiyon | Çıktı | Geçme |
|---|---|---|---|---|
| 1 | Amaç ve kapsam yaz | hangi veri, hangi yedek | kapsam notu | Onaylı |
| 2 | Rastgele örnek seç | N dosya (tarih/rastgele) | örnek listesi | Liste tarihli |
| 3 | Hash/boyut üstel çıkar | SHA/MD5 veya boyut+sayfa | beklenen özet | Özet kaydedildi |
| 4 | Geri al | yedek ortama→ayrı hedefe | geri alınan dosya | Tamamlandı |
| 5 | Özetleri karşılaştır | adım 3 vs geri alınan | eşleşme tablosu | **%100 eşleşme** |
| 6 | Ağaç/izin kontrolü | klasör yapısı, salt-okunur bayrakları | kontrol listesi | Sapma yok |
| 7 | Bozuk-senaryo (mümkünse) | erişilemez ana disk taklidi | not | — |
| 8 | Süre ölç | adım 4 başlangıç-bitiş | süre | RTO kaydedildi |
| 9 | Bulguları yaz | eksik/korunmayan dosya | bulgu listesi | Bulgu yoksa GEÇTİ |
| 10 | İmza + tarih | rapor | imzalı rapor | Kapı-4 kapanır |

### §6.3 Kabul kriterleri

| # | Kriter | Eşik | Durum |
|---|---|---|---|
| 1 | Dosya bütünlüğü | %100 eşleşme (boyut + özet) | ❌ test edilmedi |
| 2 | Dizin yapısı | birebir | ❌ |
| 3 | Kapsam | seçilen N dosyanın tümü | ❌ |
| 4 | Süre (RTO) | tanımlı hedef **YOK** | ⚠️ üst karar |
| 5 | Frekans | tanımlı takvim **YOK** | ⚠️ üst karar |
| 6 | Kayıt | tarihli imzalı rapor | ❌ yok |

**Kapı-4 (kurtarma):** 1–3 ve 6 birlikte yerine gelmeden "**yedek var**"
ifadesi vault'ta **kullanılamaz** (hdd-kapsam §14/7 hatası).

## §7 Web Kaynak Doğrulaması (bu dosya)

| # | İddia | URL | Tarih | Sonuç |
|---|---|---|---|---|
| 1 | Seagate bilgi tabanı erişilebilir (SMART içerikleri burada) | https://knowledge.seagate.com/articles/smart | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| 2 | Seagate disk test aracı destek sayfası erişilebilir | https://www.seagate.com/support/downloads/seatools/ | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| 3 | Seagate destek kökü | https://www.seagate.com/support/ | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| 4 | Western Digital destek kökü | https://www.westerndigital.com/support | 2026-10-13 | ✅ HTTP 200 (HEAD) |

**Doğrulama yöntemi:** `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`
→ HTTP 200. **İçerik okunmadı** (yalnız erişilebilirlik kanıtı) → içerik iddiaları
⚠️ VERIFICATION REQUIRED olarak kalır.

**404 ile elenen aday (uydurma link yazılmaz, elenmiştir):**
`https://www.seagate.com/support/downloads/smart/` → HTTP 404 (2026-10-13).

## §8 Ölçüm Kayıt Formu (şablon)

| Alan | Değer |
|---|---|
| Tarih / saat | |
| Disk (marka/model/seri/firmware) | ⚠️ UNKNOWN (Q-HK1) |
| Bağlantı (arayız / kasa) | |
| SMART tam döküm dosyası | |
| Kendi-kendini-test sonucu (kısa/geniş) | |
| Bad-sector tarama: tür, süre, hata sayısı | |
| Bekleyen/atomyen sayaç farkı (öncesi→sonrası) | |
| Gürültü: arka plan / bekleme / sıralı / rastgele (dB(A)) | |
| Ölçüm mesafesi ve ekipman | |
| Titreşim gözlemi | |
| Yedek tatbikatı: örnek N, eşleşme %, süre | |
| Bulgular | |
| Kapı sonuçları (1–4) | |
| İmza | |

## §9 Kapı Özeti

| # | Kapı | Koşul | Durum (2026-10-13) |
|---|---|---|---|
| 1 | SMART | Atama dökümü + iki test PASS + delta yok | ❌ AÇIK (araç + disk yok) |
| 2 | Yüzey | 0 okuma hata + bekleyen sayaçta artış yok | ❌ AÇIK (tarama yapılmadı) |
| 3 | Gürültü/vibrasyon | Tanımlı koşulda sayısal ölçüm + karar | ❌ AÇIK (tezgâh yok · Q-HD2) |
| 4 | Kurtarma | %100 eşleşme + rapor + imza | ❌ AÇIK (tatbikat yok · Q-HD3) |
| 5 | Web kaynakları | HEAD 200 | ✅ KAPANDI (§7 · 4/4) |
| 6 | Türetilmiş aritmetik | S2 | ✅ KAPANDI (`hdd-kapsam.md` §4-§5) |

**Özet:** 6 kapının **2'si kapandı**; 4 fiziksel kapı açık. Bu dosya bu hâliyle
**protokol + kayıt formu**dur, geçiş raporu değildir.

## §10 Sık Yapılan Hatalar

| # | Hata | Belirti | Doğrusu |
|---|---|---|---|
| 1 | SMART'ı garanti saymak | "SMART temiz" deyip yedeksiz çalışmak | SMART yalnız işaret (§3.1/1-2) |
| 2 | Atama yorumunu başka markaya taşımak | Yanlış eşik yorumu | Q-HD1 — marka üretici dokümanı |
| 3 | Verili diske yüzey yazma testi | Tüm veri gitti | §4.1/3 yasağı |
| 4 | Taramayı dinlerken başlatmak | Atlama/kırılma şikayeti | §4.3/1 — boş saat |
| 5 | Ortam gürültüsünü ölçmeden disk sesi ölçmek | Yanlış fark | §5.1/1 zorunlu |
| 6 | Ölçüm mikrofon mesafesi yazmamak | Sonuç tekrarlanamaz | §5.1/5 |
| 7 | "Yedek var" demek ama test etmemek | Kurtarılamayan veri | Kapı-4 |
| 8 | Tek dosyayı kurtarıp yedeği onaylamak | Nümune ≠ bütünlük | §6.2/5 %100 eşleşme |
| 9 | Sonuçları tarihsiz kaydetmek | Kanıt değeri yok | §8 formu |
| 10 | URL'yi içeriği okumadan "kaynak" saymak | Sahte derinlik | §7 — yalnız erişilebilirlik |

## §11 Terim Sözlüğü

| # | Terim | Tanım |
|---|---|---|
| 1 | SMART | S.M.A.R.T. — diskin kendi sağlık sayaçları |
| 2 | Attribute (atama) | SMART sayacı; üreticiye özel anlam |
| 3 | Normalized / raw | Ölçekli değer / ham sayı |
| 4 | Self-test | Diskin kendi kendini test etmesi (kısa/geniş) |
| 5 | Pending sector | Doğrulanmayı bekleyen şüpheli sektör |
| 6 | Reallocated sector | Fabrika yedeğine taşınan sektör |
| 7 | Bad sector | Okunamayan/yazılamayan sektör |
| 8 | Read-only scan | Veriye dokunmayan yüzey taraması |
| 9 | Verify | Okuma + ECC doğrulaması |
| 10 | Soak test | Uzun süreli stres/ateşleme testi |
| 11 | dB(A) | A-weighted ses seviyesi |
| 12 | Arka plan gürültüsü | Disk kapalıyken ortam seviyesi |
| 13 | RTO | Recovery Time Objective — kurtarma süresi hedefi |
| 14 | Hash/özet | Dosya bütünlüğü parmak izi |
| 15 | Tatbikat | Gerçek geri-alma denemesi |
| 16 | Delta | Öncesi/sonrası fark |
| 17 | Kapı (gate) | Yalnız kanıtla kapanan kabul noktası |
| 18 | S1..S5 | Bu dosyadaki kanıt seviye şeması (§2) |

## §12 İddia → Kanıt Matrisi

| # | İddia (kaynak dosyada) | Kanıt tipi | Bu dosyadaki yer | Durum |
|---|---|---|---|---|
| 1 | Dönüş gecikmesi RPM'den türetilir | aritmetik | §2 (S2) | ✅ kapandı |
| 2 | 512 örnek = 10,67 ms | aritmetik | §2 (S2) | ✅ kapandı |
| 3 | Gürültü bir ses kararıdır | tasarım kuralı | §5 (kapı-3) | ❌ ölçüm yok |
| 4 | Bad-sector taraması 0 hata ister | kabul kriteri | §4.2 | ❌ tarama yok |
| 5 | Test edilmemiş yedek yedek değildir | kabul kriteri | §6.3 | ❌ tatbikat yok |
| 6 | Envanter bilinmiyor | disk ölçümü | §3.2 Q-HD1 | ⚠️ UNKNOWN |
| 7 | SMART atamaları üreticiye özel | standart bilgisi | §3.1/4 | ⚠️ Q-HD1 — marka gelince doğrulanacak |
| 8 | 4 URL erişilebilir | HEAD 200 | §7 | ✅ |
| 9 | 404 aday elendi | HEAD 404 | §7 | ✅ (eleme kanıtı) |
| 10 | Ölçüm ekipmanı seçilmedi | envanter | §5.1/7 | ⚠️ |

## §13 Çapraz Referanslar

| # | Hedef | İlişki |
|---|---|---|
| 1 | `hdd-kapsam.md` | İddia sahibi (§4-§9 bu dosyada doğrulanır) |
| 2 | `hdd-ornekler.md` | Uygulama örnekleri (tarama/rotasyon akışları) |
| 3 | `[[hdd/index]]` | Düğüm künyesi |
| 4 | `[[donanaım-type-dogrulama]]` | Üst doğrulama tablosu |
| 5 | `[[donanaım-type-kapsam]]` | Envanter UNKNOWN kaydı (Q-T1) |
| 6 | `donanım-anayasa.md` §10/3 | Ölçüm eşikleri kabul kapısı (SNR/THD+N) |
| 7 | `[[isletim-sistemi-core-kapsam]]` | fsync/dosya sistemi davranışı sınırı |
| 8 | `[[ssd-dogrulama]]` | Kardeş depolama doğrulaması (SMART/TRIM/sıcaklık) |
| 9 | `[[output-kapsam]]` | 512/48k tamponun sahibi (§2 S2 dayanağı) |

## §13.1 Periyodik Takvim (kayıt zorunlu)

| # | Sıklık | İş | Kapı | Kayıt |
|---|---|---|---|---|
| 1 | Haftalık | SMART anlık okuma + delta | 1 | tarih + değerler |
| 2 | Haftalık | Yedekleme görevi başarısı (çıkış kodu) | 4 | görev günlüğü |
| 3 | Aylık | Kısa kendi-kendini-test | 1 | PASS/FAIL |
| 4 | Aylık | Rastgele dosya geri alma (küçük N) | 4 | eşleşme % |
| 5 | 3 ayda bir | Geniş kendi-kendini-test | 1 | PASS/FAIL |
| 6 | 3 ayda bir | Salt-okunur yüzey taraması | 2 | hata sayısı |
| 7 | 3 ayda bir | Gürültü ölçüm tekrarı (aynı koşul) | 3 | dB(A) + fark |
| 8 | Yıllık | Tam kütüphane geri yükleme tatbikatı | 4 | rapor + süre |
| 9 | Yıllık | Kapasite bütçesi gözden geçirme | — | `hdd-kapsam.md` §19.1 |
| 10 | Olay sonrası | Her SMART uyarısında anormal tarama | 1+2 | olay kaydı |

**Takvim kuralı:** Her satır **tarih + sonuç + imza** taşır; takvim boş satırla
kapanmaz. Takvim sıklıkları **öneridir** (⚠️ üst karar onayı bekliyor).

## §13.2 Kapı Kapanış Akışı

| # | Adım | Ne olur | Geri dönüş |
|---|---|---|---|
| 1 | Protokol uygulanır (§3–§6) | Ham sonuç üretilir | Sonuç yoksa dur |
| 2 | Kayıt formu doldurulur (§8) | Kanıt dosyalanır | Eksik alan varsa geri |
| 3 | Kabul kriteri ile karşılaştırılır (§9) | Eşleşme kontrolü | Eşleşmiyorsa DUR |
| 4 | Bulgu varsa kök neden aranır (§10) | Düzeltme kaydı | Kök neden yoksa düzeltme yok |
| 5 | Düzeltme uygulanır ve **tekrar ölçülür** | Yeni ham sonuç | Eski sonuç geçersiz sayılır |
| 6 | İmza + tarih | Kapı kapanır | İmzasız kapı açık sayılır |
| 7 | Vault'a işlenir | `hdd-dogrulama.md` §9 satırı güncellenir | Eski durum silinmez, üstü çizilir |
| 8 | Kapanış özeti raporlanır | Kapı özeti | Açık kapı varsa açık yazılır |

**Kural:** Kapı, **son ölçümle** kapanır; önceki başarısız ölçümün üstü
örtülerek unutulmaz — geçmişi satır olarak kalır.

## §13.3 SMART döküm kayıt alanı (boş şablon)

| # | Alan | Öncesi | Sonrası | Fark | Not |
|---|---|---|---|---|---|
| 1 | Atama listesi (tam döküm) | | | | dosya eki |
| 2 | Bekleyen sektör | | | | |
| 3 | Yeniden ayrılan sektör | | | | |
| 4 | Düzeltilemeyen sektör | | | | |
| 5 | SATA hata sayacı | | | | |
| 6 | Güç-deneme / çalışma saati | | | | |
| 7 | Sıcaklık (okuma anı) | | | | |
| 8 | Kısa test sonucu | | | | PASS/FAIL |
| 9 | Geniş test sonucu + süresi | | | | PASS/FAIL |
| 10 | Araç adı + sürümü | | | | |
| 11 | Disk marka/model/seri/firmware | | | | ⚠️ UNKNOWN |

## §13.4 Yüzey tarama kayıt alanı (boş şablon)

| # | Alan | Değer |
|---|---|---|
| 1 | Tarama türü (salt-okunur / verify / yazma) | |
| 2 | Başlangıç – bitiş saati + toplam süre | |
| 3 | Kapasite / taranan sektör sayısı | |
| 4 | Ortalama okuma hızı (başlangıç – orta – son) | |
| 5 | Bulunan hata sayısı + konum listesi | |
| 6 | Erteleme/kaçırma hataları (öncesi → sonrası) | |
| 7 | Kesinti var mı (evet/hayır + neden) | |
| 8 | Diskin ana yedekten doğruluğu (önceden) | |
| 9 | Karar (GEÇTİ / AYRILDI) | |
| 10 | Araç adı + sürümü + imza | |

## §13.5 Gürültü ölçüm kayıt alanı (boş şablon)

| # | Koşul | dB(A) | Mesafe | Not |
|---|---|---|---|---|
| 1 | Arka plan (disk kapalı) | | | |
| 2 | Bekleme (disk açık, I/O yok) | | | |
| 3 | Sıralı okuma | | | |
| 4 | Rastgele okuma | | | |
| 5 | Yazma (kopyalama) | | | |
| 6 | Fark (2–5 − 1) | | | net katkı |
| 7 | Islak montaj SONRASI tekrar (2–4) | | | karşılaştırma |
| 8 | Ekipman modeli/kalibrasyonu | — | — | |

## §13.6 Kurtarma tatbikatı rapor alanı (boş şablon)

| # | Alan | Değer |
|---|---|---|
| 1 | Tarih / kapsam | |
| 2 | Örnek dosya sayısı N | |
| 3 | Seçim yöntemi (rastgele / tarih / kritik) | |
| 4 | Beklenen özetler (dosya eki) | |
| 5 | Geri alınan özetler (dosya eki) | |
| 6 | Eşleşme oranı | % |
| 7 | Dizin yapısı kontrolü | |
| 8 | Başlangıç – bitiş / süre (RTO) | |
| 9 | Bulgu listesi (varsa) | |
| 10 | Karar (GEÇTİ / KALDI) + imza + tarih | |

## §13.7 Bulgu → Kök Neden Tablosu

| # | Bulgu | Olası kök neden | Sonraki adım |
|---|---|---|---|
| 1 | Bekleyen sektör artıyor | Yüzey bozulması / darbe / ısınma | §4.2 + konum + ayırma kararı |
| 2 | SATA hata sayacı artıyor | Kablo/uc/kontaktör | Kabloyu değiştir → yeniden ölç |
| 3 | Test FAIL ama tarama temiz | Yazılım/araç sorunu | Aracı doğrula (§3.4) — sonucu geçersiz say |
| 4 | Okuma hızı çöküyor | SMR + yazma · kafa arızası · hata kurtarma | §4.3/1 + profil kontrolü |
| 5 | Ses artıyor | Montaj gevşedi · raf teması · kafa ömrü | §5.2 adım 8 + montaj |
| 6 | Geri alma %100 değil | Yedek eksik · özet uyuşmazlığı · bozuk ortam | Yeniden yedekle → tekrar tatbikat |
| 7 | Uzun test ortasında kesildi | Uyku/elektrik/sürücü | §4.3/4 — tekrar, kayıt sıfırlanmaz |
| 8 | Atama okunamıyor | Yetki/araç/usb köprüsü | §3.4 — doğrudan bağlan |
| 9 | Ölçüm tekrarlanamıyor | Mesafe/ekipman yazılmadı | §8 formu zorunlu |
| 10 | Kapı "geçti" ama disk arızalandı | Ölçüm geçmişi tek başına yeterli sanıldı | Periyodik tekrar (§13.1) |

## §13.8 İlişkili Dosyalar

| Dosya | Rol |
|---|---|
| `hdd-kapsam.md` | İddia sahibi — fiziksel yapı, gürültü, RAID, SATA |
| `hdd-ornekler.md` | Uygulama örnekleri — tarama, streaming buffer, rotasyon |
| `[[hdd/index]]` | Düğüm künyesi (ELI15 · children · açık sorular) |
| `[[donanaım-type-dogrulama]]` | Üst doğrulama tablosu (17 satır) |
| `[[donanaım-type-kapsam]]` | Üst kapsam + envanter UNKNOWN |
| `[[ssd-dogrulama]]` | Kardeş: SMART/NVMe health, TRIM, sıcaklık kapıları |

## §13.9 Kapı Geçmişi (append-only kayıt tablosu)

> Bu tablo **ekleme yapılır**; mevcut satır değiştirilmez. İlk satırlar
> **açık** kapılardır; kapanışta yeni satır eklenir, eski satır silinmez.

| # | Tarih | Kapı | Sonuç | Kanıt | İmza |
|---|---|---|---|---|---|
| 1 | 2026-10-13 | Kapı-1 SMART | AÇIK | araç + disk yok | — |
| 2 | 2026-10-13 | Kapı-2 yüzey | AÇIK | tarama yapılmadı | — |
| 3 | 2026-10-13 | Kapı-3 gürültü | AÇIK | tezgâh yok | — |
| 4 | 2026-10-13 | Kapı-4 kurtarma | AÇIK | tatbikat yok | — |
| 5 | 2026-10-13 | Kapı-5 web kaynakları | KAPANDI | HEAD 200 ×4 | Vault Steward |
| 6 | 2026-10-13 | Kapı-6 aritmetik | KAPANDI | S2 (§2) | Vault Steward |
| 7 | — | Kapı-1 | — | — | — |
| 8 | — | Kapı-2 | — | — | — |
| 9 | — | Kapı-3 | — | — | — |
| 10 | — | Kapı-4 | — | — | — |

## §13.10 Komşu Kapılarla Sınır

| # | Komşu kapı | Sahibi | Bu dosyanın sınırı |
|---|---|---|---|
| 1 | SNR/THD+N ölçümü | `donanım-anayasa.md` §10/3 · elektronik ölçüm | Depolama bu eşiği **kanıtlamaz** |
| 2 | Dosya sistemi fsync / tutarlılık | K000 (`isletim-sistemi-core-kapsam.md` §A.3) | Kurtarma testi **ustalık** değildir |
| 3 | SSD sağlığı / TRIM / TBW | `[[ssd-dogrulama]]` | Farklı fizik — kapılar ayrık |
| 4 | Çıkış tamponu (512 / 48 kHz) | `[[output-kapsam]]` | S2 dayanağı buradan gelir |
| 5 | DSP/zaman zinciri | K003-audio · K004-dsp | Disk gecikmesi bu kapıların konusu değil |
| 6 | Yedekleme görevinin kendisi | K17-devops / K014-media (dosya kopya işi) | Bu dosya yalnızca **doğrular** |
| 7 | RAID donanım/kurulumu | K001 envanter + üst karar (Q-HK3) | Kurulum adımları kapsam dışı |
| 8 | Envanter | K001 Q4 | Q-HK1 kapanmadan kapı 1–2 kapanamaz |

---

## §14 Dürüstlük Bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler yalnız doğrulanmış
kaynaklar içerir; doğrulanamayan iddialar ⚠️ VERIFICATION REQUIRED ile
işaretlidir. Bu dosyada **hiçbir ölçüm sonucu üretilmemiştir**; erişilebilirlik
kanıtı (HTTP 200) içerik doğrulaması olarak sunulmamıştır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
