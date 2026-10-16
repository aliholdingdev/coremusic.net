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

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
