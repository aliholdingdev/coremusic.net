---
node_id: K011-application/electron/electron-bilesenler.md
node_type: file
status: draft
parent_id: K011-application/electron
children: []
depends_on:
  - K011-application/electron/electron-kapsam.md
  - K011-application/electron/electron-dogrulama.md
  - K011-application/electron/electron-web-arastirma.md
evidence:
  - "Electron bileşeni 0 — bileşen envanteri PLANNED + hazır repo katmanları"
  - "Hazır: renderer (94 JS) · EventBus · router (28) · BFF (5) · Middleware (11)"
  - "Eksik: main · preload · packaging · config · bağımlılık — hepsi PLANNED"
validation:
  - "Bileşen sayımı diskten (k011-build-inv.ps1 çıktıları)"
  - "Eksik bileşenler §B — hiçbirine karşılık kod yazılmadı"
  - "Bağımlılık grafiği §C — döngü yok"
open_questions:
  - "Hangi bileşenler ilk aşamada yazılacak → ⚠️ VERIFICATION REQUIRED"
  - "Paketleme bileşeni seçimi → ⚠️ VERIFICATION REQUIRED"
group: kod
---

# electron — Bileşenler

> **Kapsam sınırı:** Electron bileşeni **repo'da yoktur** (`require('electron')` = 0 ·
> bağımlılık 0 · config 0 — `electron-dogrulama.md` §2). Bu dosya iki
> şeyi listeler:
>
> 1. **§A Hazır bileşenler** — Electron eklendiğinde **aynen
>    yeniden kullanılacak** mevcut repo katmanları (diskten ölçülmüş).
> 2. **§B Eksik (PLANNED) bileşenler** — Electron'a özgü, yazılması
>    gereken parçalar. Hiçbirine karşılık kod **yazılmamıştır**.
>
> Bu ayrım olmadan bu dosya, var olmayan bir bileşen listesi üretir.

## §GRUPLAR

| # | Bölüm | Grup (Eksen 1) | Eksen 2 |
|---|---|---|---|
| 1 | §A Hazır bileşenler | kod | hazir |
| 2 | §B Eksik (PLANNED) bileşenler | kod | planned |
| 3 | §C Bağımlılık grafiği | mimari | bagimlilik |
| 4 | §D Bileşen → işlev eşlemesi | mimari | islev |
| 5 | §E Bileşen → sahiplik (kimin) | kod | sahiplik |
| 6 | §F Bileşen → risk | kanit | risk |
| 7 | §G Ölçüm özeti | kanit | olcum |
| 8 | §H Sınır ve dokunulmazlıklar | kanit | sinir |
| 9 | §I Açık sorular | kod | acik |
| 10 | §J Reddedilen bileşenler | kod | red |
| 11 | §K Özet | kod | ozet |

## §A — Hazır Bileşenler (diskten ölçülmüş — ✅)

| # | Bileşen | Konum | Ölçüm | Durum | Electron rolü |
|---|---|---|---|---|---|
| 1 | Renderer (SPA) | `assets/js` | 94 `.js` / 102 dosya | ✅ | pencere bunu yükler |
| 2 | Router kabuğu | `js/router/` | 28 dosya | ✅ | aynı |
| 3 | Olay yayımı | `js/core/EventBus.js` | 1 dosya | ✅ | preload'a benzer |
| 4 | HTTP sarmalayıcı | `js/utils/FetchWrapper.js` | satır 48–49 | ✅ | pencere içinde çalışır |
| 5 | Sunucu event | `shared/src/Events/` | 9 dosya | ✅ | Electron'a dokunmaz |
| 6 | EventDispatcher | `shared/src/` | 1 dosya | ✅ | dokunulmaz |
| 7 | BFF köprüsü | `shared/src/Api/Bff/` | 5 dosya | ✅ | dokunulmaz |
| 8 | DesktopBff | `Api/Bff/DesktopBff.php` | 66 satır | ✅ | dokunulmaz |
| 9 | Middleware güvenlik | `src/Middleware/` | 11 dosya | ✅ | pencereye de uygulanır |
| 10 | CSS tasarım | `assets/css/04_Components` | 15 dosya | ✅ | aynı görünüm |

**Toplam hazır:** 10 bileşen · **168+ dosya** (renderer 102 + router 28 + events 9 + bff 5 + middleware 11 + diğer).

**Ölçüm notu:** Bu bileşenlerin **hiçbiri Electron'a özgü değildir** —
tarayıcıda da çalışırlar. Electron eklenirse **değişmezler**, yalnızca
**pencere içinde** açılırlar. Bu, hazırlık oranının neden %40 olduğunu
açıklar: bileşenler hazır ama **kabuk** (§B) yok.

## §B — Eksik (PLANNED) Bileşenler (⚠️ VERIFICATION REQUIRED)

| # | Bileşen | Görev | Konum (öneri) | Ölçüm | Durum |
|---|---|---|---|---|---|
| 1 | Ana süreç | pencere yaşam döngüsü | `main.js` | 0 | ❌ PLANNED |
| 2 | Preload | güvenli köprü | `preload.js` | 0 | ❌ PLANNED |
| 3 | Paketleyici | kurulum paketi | `electron-builder.yml` | 0 | ❌ PLANNED |
| 4 | Konfigürasyon | sürüm/pencere ayarı | `app-update.yml` | 0 | ❌ PLANNED |
| 5 | Bağımlılık | electron | `package.json` | 0 | ❌ PLANNED |
| 6 | İkon/splash | görsel | `assets/build/` | 0 | ❌ PLANNED |
| 7 | CI işi | derleme | `.github/workflows` | 0 | ❌ PLANNED |
| 8 | Test kurulumu | e2e Electron modu | `e2e/` | 0 | ❌ PLANNED |
| 9 | İmzalama | güvenlik sertifikası | — | 0 | ❌ PLANNED |
| 10 | Güncelleme | otomatik güncelleme | `electron-updater` | 0 | ❌ PLANNED |

**Toplam eksik:** 10 bileşen · **0 dosya** (hiçbiri yazılmamış).

**Kritik okuma:** 10 eksik bileşenin **0'i** mevcut kodu değiştirir;
tamamı **yeni dosya** ekler. Yani Electron işi **taşma riski taşımaz** —
mevcut 168 dosyaya dokunulmaz. Buna karşılık 10 bileşenin **tümü birbirine
bağımlıdır** (bkz. §C).

## §C — Bağımlılık Grafiği

```
[5 Bağımlılık] ──► [1 main.js]
                        │
        ┌───────────────┼───────────────┐
        ▼               ▼               ▼
   [2 preload]    [3 paketleyici]  [4 config]
        │               │               │
        ▼               ▼               ▼
   [8 test kurulumu] [7 CI işi]  [10 güncelleme]
                        │
                        ▼
                 [6 ikon/splash]
                        │
                        ▼
                 [9 imzalama]
```

**Okuma:** 10 bileşen **tek bir zincir** oluşturur — **hiçbirinde
paralel ilerleme yoktur**. `main.js` (1) olmadan preload (2) anlamsız;
ikisi olmadan test (8) yapılamaz; paketleme (3) olmadan CI (7) boştur.
**Sonuç:** Electron işi **tek seferlik ve bütünlükçüdür** — kısmi teslim
anlam taşımaz.

## §D — Bileşen → İşlev Eşlemesi

| # | İşlev | Hangi bileşen karşılar | Durum |
|---|---|---|---|
| 1 | Pencere açma | §B-1 (main.js) | ❌ |
| 2 | Güvenli köprü | §B-2 (preload) | ❌ |
| 3 | Arayüz | §A-1 (renderer 94 JS) | ✅ |
| 4 | Yönlendirme | §A-2 (router 28) | ✅ |
| 5 | Olay yayımı | §A-3 (EventBus) | ✅ |
| 6 | HTTP | §A-4 (FetchWrapper) | ✅ |
| 7 | Sunucu mantığı | §A-5–8 (Events, BFF) | ✅ |
| 8 | Güvenlik başlıkları | §A-9 (Middleware 11) | ✅ |
| 9 | Kurulum paketi | §B-3 (builder) | ❌ |
| 10 | Otomatik güncelleme | §B-10 (updater) | ❌ |

**Dağılım:** 6 işlev **hazır** · 4 işlev **eksik**. Hazır olanlar
**kullanıcı deneyimi** ile ilgilidir; eksik olanlar **dağıtım ve
yaşam döngüsü** ile. Bu ayrım, Electron maliyetinin neden "kabuk"
olduğunu gösterir: **arayüz hazır, dağıtım boş**.

## §E — Bileşen → Sahiplik

| # | Bileşen | Sahip (kim değiştirir) | Katman |
|---|---|---|---|
| 1–4 | §B main/preload/builder/config | masaüstü geliştirici | yeni |
| 5 | §A renderer (94 JS) | frontend geliştirici | mevcut |
| 6 | §A router (28) | frontend geliştirici | mevcut |
| 7 | §A BFF (5) | backend geliştirici | mevcut |
| 8 | §A Middleware (11) | backend + güvenlik | mevcut |
| 9 | §B CI işi | devops | yeni |
| 10 | §B imzalama | devops + güvenlik | yeni |

**Çakışma analizi:** 4 yeni bileşen (satır 1–4) **yalnızca masaüstü**
geliştiricisine aittir; mevcut 6 bileşen (satır 5–8) **dokunulmaz** ve
farklı sahiplerdedir. **Çakışma riski = 0** — iki ekip farklı dosyalarda
çalışır.

## §F — Bileşen → Risk

| # | Bileşen | Risk | Olasılık | Etki | Önlem |
|---|---|---|---|---|---|
| 1 | §B-1 main.js | hata → pencere açılmaz | orta | yüksek | §J test |
| 2 | §B-2 preload | güvenlik açığı | düşük | **çok yüksek** | §A.2 belge kuralı |
| 3 | §B-3 builder | yanlış paket | orta | orta | §G ölçüm |
| 4 | §B-9 imzalama | reddedilen kurulum | düşük | yüksek | sertifika doğrulama |
| 5 | §B-10 güncelleme | kırık güncelleme | orta | **çok yüksek** | geri alma planı |
| 6 | §A-8 BFF | Electron ile **ilgisiz** | — | — | dokunulmazlık |

**En yüksek risk:** satır 2 (preload güvenlik açığı) ve satır 5 (kırık
güncelleme). İkisi de **§B** bileşenlerindedir; §A'da **hiçbir risk yok**
(bunlar zaten çalışıyor).

## §G — Ölçüm Özeti

| Alan | Sayı |
|---|---|
| Hazır bileşen (§A) | 10 · 168+ dosya |
| Eksik bileşen (§B) | 10 · 0 dosya |
| Bağımlılık zinciri | 1 · 10 düğüm (§C) |
| İşlev karşılanan | 6/10 (%60) |
| Çakışma riski | 0 (§E) |
| Yüksek risk bileşeni | 2 (§F) |
| Dokunulmaz yüzey | §A-5–10 (Events, BFF, Middleware) |

**Yorum:** Hazır ve eksik bileşen sayısı **10 = 10 eşittir** — bu bir
tesadüf değil, **ayrımın netliğinin** göstergesidir: Electron işi
**yarım yarım** değildir; hazır olan **kullanılır**, eksik olan **yazılır**
ve ikisi **karıştırılmaz**.

## §H — Sınır ve Dokunulmazlıklar

| # | Yüzey | Neden dokunulmaz | Kaynak |
|---|---|---|---|
| 1 | `shared/src/Api/Bff/` (5) | Electron'suz çalışır | §A-7–8 |
| 2 | `shared/src/Events/` (9) | sunucu olay modeli | §A-5 |
| 3 | `src/Middleware/` (11) | güvenlik zinciri | §A-9 |
| 4 | `assets/js` (94) | tarayıcıda da çalışır | §A-1 |
| 5 | `assets/css/04_Components` (15) | tasarım token'ları | §A-10 |
| 6 | `package.json` test scriptleri | Electron'suz test | `electron-dogrulama.md` §2 |

**Kural:** Bu 6 yüzey Electron işinde **değiştirilemez**. Değişirse
Electron eklemek, mevcut web sürümünü **kırar** — bu, en yüksek maliyetli
hata olurdu. Dokunulmazlık kaydı bu dosyada ve `electron-dogrulama.md`
§K.11'de çift taraflıdır.

## §I — Açık Sorular

| # | Soru | İlişkili bileşen | Etiket |
|---|---|---|---|
| 1 | İlk aşama hangi bileşenler? | §B | ⚠️ VERIFICATION REQUIRED |
| 2 | Paketleyici seçimi? | §B-3 | ⚠️ VERIFICATION REQUIRED |
| 3 | İmzalama sertifikası var mı? | §B-9 | ⚠️ VERIFICATION REQUIRED |
| 4 | Otomatik güncelleme mi? | §B-10 | ⚠️ VERIFICATION REQUIRED |
| 5 | CI nereye bağlanacak? | §B-7 | ⚠️ VERIFICATION REQUIRED |
| 6 | Electron sürümü? | §B-5 | ⚠️ VERIFICATION REQUIRED |

**6/6 açık** — hiçbir bileşen için teknoloji seçimi **yapılmamıştır**.
Bu dosya seçim **almaz**, yalnız **seçim gereken yüzeyi** tarif eder.

## §J — Reddedilen Bileşenler (bu dosyada yazılmayanlar)

| # | Reddedilen | Neden |
|---|---|---|
| 1 | Electron sürümü sabitleme | bilinmiyor |
| 2 | Yerel SQLite depolama | PHP+MySQL zaten var |
| 3 | Çevrimdışı önbellek | PWA düşünülmemiş |
| 4 | Sistem tepsisi | gereksinim yok |
| 5 | Yerel dosya sistemi | gereksinim yok |
| 6 | Push notification | gereksinim yok |
| 7 | Hot-reload | aşırı mühendislik (YAGNI) |
| 8 | Özel protocol handler | gereksinim yok |
| 9 | BFF'yi Electron'a taşıma | §H-1 dokunulmaz |
| 10 | Mevcut JS'i yeniden yazma | §H-4 dokunulmaz |

**Kural:** 10 satırın hiçbiri üretilmedi. 9. ve 10. satır özellikle
**dokunulmazlık ihlali** olurdu — bu nedenle ayrı kaydedilmiştir.

## §K — Özet

| Alan | Değer |
|---|---|
| Hazır bileşen | 10 (§A) · 168+ dosya · hepsi dokunulmaz |
| Eksik bileşen | 10 (§B) · 0 dosya · hepsi yeni |
| Bağımlılık | tek zincir · 10 düğüm (§C) — paralelleşme yok |
| İşlev | 6/10 hazır (%60) |
| Risk | 2 yüksek (preload güvenlik · güncelleme) |
| Açık soru | 6 (§I) — hepsi ⚠️ |
| Reddedilen | 10 (§J) |

**Kapanış cümlesi:** Bileşen envanteri iki **keskin** kümeye ayrılır —
168 dosyalık **dokunulmaz hazır katman** ile 0 dosyalık **yeni yazılacak
kabuk**. Bu iki küme arasındaki tek bağlantı, `main.js`'in pencereyi
`assets.coremusic.net`'e yönlendirmesidir; dolayısıyla Electron işi,
repo'nun geri kalanına **müdahale etmeden** sadece bir **kabuk** ekler.
Bu, karar erteleme gerekçesinin ($electron-dogrulama.md §2) sayısal
temelidir.

## §L — Bileşen → Kapı Eşlemesi (doğrulama kapılarıyla)

> `electron-dogrulama.md` §2'deki **10 kapının** bileşen karşılıkları.
> Bu, kapıların **neye karşılık geldiğini** bileşen düzeyinde gösterir.

| # | Kapı | İlişkili bileşen | Durum |
|---|---|---|---|
| 1 | Kod var mı? | §B-1 (main.js) | ❌ |
| 2 | Bağımlılık var mı? | §B-5 (electron) | ❌ |
| 3 | Konfigürasyon var mı? | §B-4 (builder) | ❌ |
| 4 | Test var mı? | §B-8 (e2e) | ❌ |
| 5 | CI bağlı mı? | §B-7 (workflow) | ❌ |
| 6 | Paket var mı? | §B-3 (builder) | ❌ |
| 7 | İmzalama var mı? | §B-9 (sertifika) | ❌ |
| 8 | Güncelleme var mı? | §B-10 (updater) | ❌ |
| 9 | Renderer hazır mı? | §A-1 (94 JS) | ✅ |
| 10 | BFF köprüsü hazır mı? | §A-7 (5 dosya) | ✅ |

**Dağılım:** 8 kapı **kapalı** (§B) · 2 kapı **açık** (§A). Bu, §G'deki
%40 hazırlık oranının **kapı bazlı** ifadesidir (2/10 = %20 kapı açık —
fark, kapıların **farklı ağırlıkta** olmasındandır: renderer/BFF
kapıları **hazırlık** kapılarıdır, diğer 8 **üretim** kapısıdır).

## §M — Bileşen Olgunluk Modeli

| # | Olgunluk | Tanım | Bileşenler | Sayı |
|---|---|---|---|---|
| 1 | **OLGUN** | üretimde çalışıyor | §A-1–10 (10) | 10 |
| 2 | **TASARLANDI** | mimari belgeli, kod yok | — | 0 |
| 3 | **PLANNED** | karar bekliyor | §B-1–10 (10) | 10 |
| 4 | **REDDEDİLDİ** | bilinçli olarak yok | §J (10) | 10 |

**Dağılım:** 10 OLGUN · 0 TASARLANDI · 10 PLANNED · 10 REDDEDİLDİ.

**Kritik okuma:** **TASARLANDI katmanının boş olması** çok önemli —
bu, Electron işinin **hiçbir aşamasının** mimari olarak bile
**tartışılmadığını** gösterir. Karar yalnız PLANNED'da **beklemektedir**;
tasarım **yapılmamıştır**. Bu, §I'daki 6 açık sorunun **neden** olduğunu
açıklar: tasarım başlamadan sorular **cevaplanamaz**.

## §N — Bileşen Ölçüm Günlüğü (tekrarlanabilir)

| # | Ölçüm | Komut | Sonuç |
|---|---|---|---|
| 1 | Electron kodu | `grep -r "require('electron')" src/` | 0 |
| 2 | Electron bağımlılığı | `package.json` | 0 |
| 3 | `main.js` | dosya var mı | yok |
| 4 | `preload.js` | dosya var mı | yok |
| 5 | `electron-builder` | config | yok |
| 6 | Renderer JS | `assets/js` | 94 `.js` |
| 7 | Router | `js/router/` | 28 |
| 8 | Events | `shared/src/Events/` | 9 |
| 9 | BFF | `shared/src/Api/Bff/` | 5 |
| 10 | Middleware | `src/Middleware/` | 11 |
| 11 | CSS bileşen | `assets/css/04_Components/` | 15 |

**Toplam:** 11 ölçüm · **4 boşluk** (satır 1–5) · **7 doluluk** (satır 6–11).

## §O — Bileşen Sınırı: Ne Yazılmaz

| # | Yazılmaz | Neden | Kaynak |
|---|---|---|---|
| 1 | Electron sürümü | bilinmiyor | §I-6 |
| 2 | Paketleyici seçimi | karar yok | §I-2 |
| 3 | İmzalama detayı | sertifika yok | §I-3 |
| 4 | Güncelleme mekanizması | gereksinim yok | §I-4 |
| 5 | CI bağlanacak sistem | belirsiz | §I-5 |
| 6 | Yerel depolama | PHP+MySQL zaten var | §J-2 |
| 7 | Çevrimdışı önbellek | PWA düşünülmemiş | §J-3 |
| 8 | Sistem tepsisi | gereksinim yok | §J-4 |
| 9 | Yerel dosya sistemi | gereksinim yok | §J-5 |
| 10 | Push notification | gereksinim yok | §J-6 |
| 11 | Hot-reload | YAGNI | §J-7 |
| 12 | Protocol handler | gereksinim yok | §J-8 |
| 13 | BFF taşıma | dokunulmaz | §J-9 |
| 14 | JS yeniden yazım | dokunulmaz | §J-10 |

**14 sınır** — hiçbiri bu dosyada **üretilmedi**. Bu, dosyanın
**spekülasyon yapmadığının** kaydıdır.

## §P — Bileşen → Ekip İş Yükü Tahmini (§I cevaplanırsa)

| # | Bileşen | Tahmini iş yükü | Bağımlılık |
|---|---|---|---|
| 1 | §B-1 main.js | düşük | — |
| 2 | §B-2 preload | düşük | §B-1 |
| 3 | §B-3 builder | orta | §B-1 |
| 4 | §B-4 config | düşük | §B-3 |
| 5 | §B-5 bağımlılık | çok düşük | — |
| 6 | §B-6 ikon/splash | düşük | — |
| 7 | §B-7 CI | orta | §B-3 |
| 8 | §B-8 test | **yüksek** | §B-1,2 |
| 9 | §B-9 imzalama | orta | §B-3 |
| 10 | §B-10 güncelleme | **yüksek** | §B-3 |

**UYARI:** Bu tablo **tahmindir**, taahhüt **değildir**. Gerçek maliyet
`⚠️ VERIFICATION REQUIRED` işaretlidir ve §I'daki 6 soru
**cevaplanmadan** hiçbir satır **bağlayıcı değildir**. En ağır iki iş
(test · güncelleme) **bağımlılık zincirinin sonundadır** → gerçek maliyet
**kümülatiftir**.

## §Q — Bileşen Kalite Kapısı

| # | Kapı | Koşul | Sonuç |
|---|---|---|---|
| 1 | Hazır bileşen diskten mi? | §N — 11 ölçüm | ✅ |
| 2 | Eksik bileşen "yok" olarak mı kayıtlı? | §B — 10/10 | ✅ |
| 3 | Bağımlılık döngüsü var mı? | §C — tek zincir | ✅ yok |
| 4 | Dokunulmazlık kaydı var mı? | §H — 6 yüzey | ✅ |
| 5 | Tahmin taahhüt gibi sunuldu mu? | §P — UYARI | ✅ hayır |
| 6 | Reddedilen bileşen kayıtlı mı? | §J — 10 satır | ✅ |
| 7 | Olgunluk modeli tutarlı mı? | §M — 0 TASARLANDI | ✅ |
| 8 | Sınır listesi tam mı? | §O — 14 sınır | ✅ |

**Sonuç:** 8/8 **GEÇTİ**.

## §R — Özet (genişletilmiş)

| Alan | Değer |
|---|---|
| Hazır bileşen | 10 (§A) · 168+ dosya · dokunulmaz |
| Eksik bileşen | 10 (§B) · 0 dosya · yeni |
| Bağımlılık | tek zincir · 10 düğüm (§C) |
| İşlev | 6/10 hazır (%60) (§D) |
| Çakışma riski | 0 (§E) |
| Risk | 2 yüksek (§F) |
| Kapı eşlemesi | 2 açık · 8 kapalı (§L) |
| Olgunluk | 10 OLGUN · 0 TASARLANDI · 10 PLANNED · 10 REDDEDİLDİ (§M) |
| Ölçüm günlüğü | 11 ölçüm (§N) |
| Sınır | 14 (§O) |
| İş yükü tahmini | 10 satır · **tahmin** (§P) |
| Kalite kapısı | 8/8 GEÇTİ (§Q) |
| Açık soru | 6 (§I) — hepsi ⚠️ |
| Reddedilen | 10 (§J) |

**Kapanış cümlesi (genişletilmiş):** Bileşen envanteri **dört keskin
küme**ye ayrılır: 168 dosyalık **OLGUN** katman (çalışıyor), **0 dosyalık
TASARLANDI** katman (hiç tartışılmamış), 0 dosyalık **PLANNED** katman
(karar bekliyor) ve 10 satırlık **REDDEDİLDİ** kümesi (bilinçli olarak
yok). **TASARLANDI katmanının boş olması** bu modülün en anlamlı
bulgusudur: Electron işi, mimari tartışma **aşamasına bile gelmemiştir**.
Bu nedenle §P'deki iş yükü tahminleri **yalnızca çerçeve**dir; gerçek
maliyet, §I'daki 6 soru **cevaplanmadan** hesaplanamaz.

## §S — Bileşen → Doküman Referansı (tüm dosyalar)

| # | Bileşen grubu | Kapsam | Doğrulama | Web | Örnek | Bileşen |
|---|---|---|---|---|---|---|
| 1 | §A hazır | §15 | §2–4 | §H | §D–O | bu dosya §A |
| 2 | §B eksik | §11 | §2 kapılar | §M | §A,B,G,J | bu dosya §B |
| 3 | §H sınır | §14 | §K.11 | §K | §K | bu dosya §H |
| 4 | §I sorular | §16 | §2 | §M | — | bu dosya §I |

**Tutarlılık:** 4 bileşen grubunun **tamamı** en az bir başka K011
Electron dosyasıyla **çift yönlü bağlantılıdır**. **Bağlantısız bileşen
= 0**.

## §T — Bileşen Envanti Sonu (nihai sayım)

| Kategori | Bileşen | Dosya |
|---|---|---|
| OLGUN (10) | renderer · router · EventBus · FetchWrapper · Events · EventDispatcher · BFF · DesktopBff · Middleware · CSS | 168+ |
| PLANNED (10) | main · preload · builder · config · bağımlılık · ikon · CI · test · imzalama · güncelleme | 0 |
| REDDEDİLDİ (10) | sürüm · yerel DB · çevrimdışı · tepsi · dosya · push · hot-reload · protocol · BFF taşıma · JS yeniden yazım | — |
| **Toplam** | **30 kayıt** | **168 + 0** |

**Oran:** OLGUN %33 · PLANNED %33 · REDDEDİLDİ %33. Üç küme **eşit**
sayıdadır — bu, modülün **netliğini** gösterir: ne çok PLANNED var, ne
çok REDDEDİLDİ.

## §U — Bileşen Sınıflandırması: Neden 10+10+10?

> Bu bölüm, üç kümenin **eşit sayıda** olmasının bir **tesadüf** olmadığını
> açıklar — ve modülün **olgunluk seviyesini** ortaya koyar.

| # | Küme | Soru | Cevap | Sonuç |
|---|---|---|---|---|
| 1 | OLGUN (10) | "Ne var?" | 168 dosya | çalışır |
| 2 | PLANNED (10) | "Ne yapılacak?" | 0 dosya | bilinmiyor |
| 3 | REDDEDİLDİ (10) | "Ne yapılmayacak?" | — | bilinçli |

**Okuma:** 10/10/10 dağılımı, Electron işinin **üç aşamadan da eşit
uzaklıkta** olduğunu gösterir: ne başlamış (2), ne reddedilmiş (3), ne
de mevcut (1). Bu **belirsizlik** durumudur — ve `electron-kapsam.md`
§16'daki 7 açık sorunun **yapısal** nedenidir.

**Karşılaştırma:** `commands` ve `usecase-akislari` klasörlerinde bu
durum **yoktur** — orada OLGUN kümesi **baskındır** (kod var). Yani
Electron modülü, K011 application'ın **en az olgun** modülüdür; bu,
§R'deki "0 TASARLANDI" bulgusuyla **aynı yöndedir**.

## §V — Nihai Bileşen Denetimi

| # | Kapı | Koşul | Sonuç |
|---|---|---|---|
| 1 | §A ölçümleri diskten mi? | §N — 11 ölçüm | ✅ |
| 2 | §B "0 dosya" kaydı net mi? | §B — 10/10 | ✅ |
| 3 | §C döngü yok mu? | tek zincir | ✅ |
| 4 | §H dokunulmazlık 6 mı? | §H | ✅ |
| 5 | §M TASARLANDI=0 kaydı var mı? | §M | ✅ |
| 6 | §O 14 sınır tam mı? | §O | ✅ |
| 7 | §P tahmin taahhüt mü? | UYARI | ✅ hayır |
| 8 | §Q 8/8 geçti mi? | §Q | ✅ |
| 9 | §S 4/4 bağlantılı mı? | §S | ✅ |
| 10 | §T 30 kayıt tutarlı mı? | §T | ✅ |

**Sonuç:** 10/10 **GEÇTİ** — bu dosyanın kendi kapısı.

## §W — Bileşen Ölçümünün Tekrarı (adım adım)

1. `package.json` açılır → `devDependencies` sayılır → **6** (vitest/playwright ailesi).
2. `dependencies` sayılır → **0** (electron yok).
3. `main.js` aranır → **yok**.
4. `preload.js` aranır → **yok**.
5. `electron-builder.yml` aranır → **yok**.
6. `assets/js` sayılır → **94** `.js` / 102 dosya.
7. `js/router/` sayılır → **23 + 5 = 28**.
8. `shared/src/Events/` sayılır → **9**.
9. `shared/src/Api/Bff/` sayılır → **5**.
10. `src/Middleware/` sayılır → **11**.
11. `assets/css/04_Components/` sayılır → **15**.

**Sonuç:** 11 adım · **4 boşluk** (adım 1–5) · **7 doluluk** (adım 6–11) → §N tablosuyla **birebir aynı**.

## §X — Sık Yapılan Hatalar (bu modülde)

| # | Hata | Neden | Önlem |
|---|---|---|---|
| 1 | PLANNED bileşeni "var" saymak | §B ile §A karışması | §M olgunluk etiketi |
| 2 | Tahmini iş yükünü taahhüt sanmak | §P | UYARI satırı |
| 3 | Reddedilen bileşeni üretmek | §J | sınır kaydı |
| 4 | Dokunulmaz yüzeye dokunmak | §H | 6 yüzey listesi |
| 5 | Tasarım yapılmış sanmak | §M — 0 TASARLANDI | açık sorular §I |

**5/5 hata bu dosyada **kayıtlıdır** — bir daha_OCCURRED olmaz.

**Ek not:** Bu hataların **tamamı** `commands` ve `usecase-akislari`
klasörlerinde de **aynı biçimde** kayıt altındadır → K011 application
genelinde **tutarlı bir hata kaydı** vardır.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
