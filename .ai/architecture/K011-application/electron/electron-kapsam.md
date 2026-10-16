---
title: "K011 · electron — Kapsam (kademe-2)"
type: architecture
kodeme: kademe-2
module: K011-application
parent: electron
status: active
updated: 2026-10-15
group: mimari
---

# K011 · electron — Kapsam

> **Amaç:** Electron alt-modülünün kademe-2 kapsamını, sınırını (ne DEĞİL),
> akış iddialarını ve açık sorularını yazmak — **kod kanıtı olmadan iddia
> üretmemek**: repo'da Electron kodu bulunmadığı için tüm maddeler
> `PLANNED` / `⚠️ VERIFICATION REQUIRED` etiketiyle taşınır.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|---|---|---|---|
| 1 | §1 ELI15 | mimari | anlatım | Klasörü 3 cümlede anlamak |
| 2 | §2 Sınır (ne DEĞİL) | mimari | sınır | Kapsam dışını okumadan kod önerme |
| 3 | §3 Kapsam tablosu | mimari | envanter | Kapsam içi her maddenin durumu |
| 4 | §4 Tasarım akışı (İDDİA) | mimari | akış | Main/renderer/preload zinciri — PLANNED |
| 5 | §5 Disk kanıtı | kanit | kanıt | Dosya↔Satır çiftleri (okunmuş) |
| 6 | §6 Olgunluk ve etiket | kanit | durum | IMPLEMENTED / PLANNED ayrımı |
| 7 | §7 Kapsam dışı | mimari | sınır | Komşu düğümlere devredilen konular |
| 8 | §8 Açık sorular | mimari | boşluk | Kapatılmayan belirsizlikler |
| 9 | §9 Riskler | mimari | risk | Paketleme/kurulum riskleri |
| 10 | §10 Bağımlılıklar | mimari | ilişki | K011 içi ve K000-K019 komşuları |
| 11 | §11 Rol eşlemesi | mimari | routing | Agent → bu klasördeki iş |
| 12 | §12 Kapı özeti | kanit | doğrulama | Kademe-2 kabul kapıları |

---

## §1 — ELI15

**Basit:** Electron, bir web sitesini masaüstü programına çeviren kabuktur —
içine kendi tarayıcısını ve Node.js'ini gömer. CoreMusic bu kabuğu **henüz
yazmadı**: depoda Electron bağımlılığı, `main.js` (Electron süreç dosyası),
paketleme yapılandırması veya `BrowserWindow` kullanan tek bir dosya yoktur.
Bu belge bu yüzden ne "nasıl yazıldı"yı değil, **"ne yazılmadı ve ne
yazılacak"ı** tanımlar.

**Detay:** `electron` düğümü, v2 §2.11'in *"Uygulamanın kendisi — web sayfası,
masaüstü penceresi, Electron çatısı"* ifadesindeki üçüncü sıradır. Repo
kanıtıyla desteklenen tek şey, Electron'un **sarmalayacağı** mevcut web
kabuğudur: `shared/src/PageRouter/HtmlShellRenderer.php` HTML shell'i üretir,
`assets.coremusic.net/js/main.js` SPA router'ı başlatır. Electron bunları bir
`BrowserWindow` içine yüklediğinde ortaya çıkar — bu yükleme kodu ise diskte
**yoktur** (§6).

**Terim:** Bu dosyadaki hiçbir satır "çalışıyor" anlamına gelmez. `PLANNED`
= hedef tanım · `⚠️ VERIFICATION REQUIRED` = doğrulanamayan iddia ·
`IMPLEMENTED` = bu klasörde **hiç kullanılmaz**.

---

## §2 — Sınır (ne DEĞİL)

| # | Bu düğüm DEĞİL | Nerede | Kanıt / Not |
|---|---|---|---|
| 1 | Pencere sahibi olan arayüz kodu değil | `K011-application/desktop` | `desktop/index.md` Sınır satırı: "Electron paketleme K011/electron'da" |
| 2 | Tarayıcıda koşan SPA router'ı değil | `K011-application/web` | JS kodu `assets.coremusic.net/js/router/` altında |
| 3 | Sunucu tarafı çalıştırma değil | `K011-application/local-runtime` | `php -S` akışı `README.md` satır 121 |
| 4 | HTTP sunucusu yapılandırması değil | `K011-application/server-runtime` | nginx dokümanları o havuzdadır |
| 5 | İş kuralı değil | `K008-domain` | K011 yalnız A2 bandında kodlar |
| 6 | Arka plan kuyruğu/servis değil | `K012-services` | — |
| 7 | Dış servis entegrasyonu değil | `K013-integraion` | — |
| 8 | DSP/masaüstü ses motoru değil | `K001-K004` | C++20 motor ayrı düğüm |
| 9 | Kurulum/siçrayıcı (installer) üretimi DEĞİL (bu kademe) | ⚠️ VERIFICATION REQUIRED | Kademe-3/4 kapsamı → `electron/index.md` satır 13 |
| 10 | Kod imzası/otomatik güncelleme DEĞİL (bu kademe) | ⚠️ VERIFICATION REQUIRED | autoUpdater implementasyonu diskte yok |
| 11 | `desktop` klasörüyle aynı kabuk DEĞİL | `K011-application/desktop` | sınır sorusu açık → §8.1 |
| 12 | Node.js CLI araçları değil | `local-runtime` | `bin/*.php` CLI'ları PHP'dedir |

**Sınır özeti (tek satır):** `electron` = *web kabuğunu masaüstü kabuğuna
paketleyen çatı*; çizim web'de, pencere desktop'ta, paketleme burada, HTTP
local-runtime'da.

---

## §3 — Kapsam tablosu

| # | Kapsam maddesi | Durum | Kanıt etiketi | Not |
|---|---|---|---|---|
| 1 | Electron sürümü ve bağımlılık kaydı | YOK | `PLANNED` | kök `package.json` içinde `"electron"` yok |
| 2 | Main süreç dosyası (`main.js`) | YOK | `PLANNED` | `assets.coremusic.net/js/main.js` **farklı** dosyadır (browser entry) |
| 3 | Preload / contextBridge köprüsü | YOK | `PLANNED` | — |
| 4 | `BrowserWindow` kurulumu | YOK | `PLANNED` | repo geneli `BrowserWindow` isabeti 0 |
| 5 | Paketleme yapılandırması (forge/builder) | YOK | `PLANNED` | `electron-builder` isabeti 0 |
| 6 | Pencere başlığı/ikon/çıkış davranışı | YOK | `PLANNED` | — |
| 7 | Tek-example kilidi (`requestSingleInstanceLock`) | YOK | `PLANNED` | — |
| 8 | Güvenlik checklist uygulaması (E1-E20) | KISMEN | `⚠️ VERIFICATION REQUIRED` | CSP/nonce web tarafında var; Electron tarafı yok |
| 9 | Web kabuğunun yüklenmesi (shell) | VAR | `IMPLEMENTED (web)` | `HtmlShellRenderer.php` — Electron değil, hedef içeriği |
| 10 | Cihaz/tier tespiti (pencere boyutu) | VAR | `IMPLEMENTED (web)` | `device-loader.js` viewport okuması |
| 11 | Masaüstü cihaz etiketi (SQL) | VAR | `IMPLEMENTED (data)` | `coremusic_user.sql` `device_type` enum `desktop` |
| 12 | Masaüstü CSS katmanı | VAR | `IMPLEMENTED (css)` | `08_Devices/d-desktop.css` eşlemesi `devices.config.js` |
| 13 | Electron'a özel IPC kanalı | YOK | `PLANNED` | — |
| 14 | Dosya sistemi erişimi (medya kütüphanesi) | YOK | `PLANNED` | medya okuması sunucu tarafında |
| 15 | Oturum/çerez davranışı pencere içinde | KISMEN | `⚠️ VERIFICATION REQUIRED` | çerez davranışı web tarafında tanımlı, pencere içi test yok |
| 16 | Kurulum programı (NSIS/MSI) | YOK | `PLANNED` | `.exe/.msi/.nsi` glob sonucu 0 |
| 17 | Mimari eksen (paket ≠ ürün) | AÇIK | `⚠️ VERIFICATION REQUIRED` | `electron/index.md` satır 14 |
| 18 | Kademe-3 bounded-context kapsamı | BEKLER | `PLANNED` | ADR-097 K4 faz (C) |

**Sayım özeti:** kapsam 18 madde · `IMPLEMENTED (başka katman)` 5 ·
`PLANNED` 10 · `⚠️ VERIFICATION REQUIRED` 3 · **bu klasörde IMPLEMENTED: 0**.

---

## §4 — Tasarım akışı (İDDİA — PLANNED)

> Bu akış **hedef mimaridir**; diskte hiçbir satırı yoktur. Depodaki gerçek
> akış `local-runtime` (PHP entry) ve `web` (SPA router)dır.

| # | Adım | Süreç | Beklenen API | Durum |
|---|---|---|---|---|
| 1 | Uygulama başlatılır | main | `app.whenReady()` | PLANNED |
| 2 | Tek örnek kilidi alınır | main | `app.requestSingleInstanceLock()` | PLANNED |
| 3 | Pencere oluşturulur | main | `new BrowserWindow({...})` | PLANNED |
| 4 | Preload yüklenir | main→renderer | `contextIsolation: true` | PLANNED |
| 5 | HTML shell yüklenir | renderer | `win.loadURL(...)` | PLANNED |
| 6 | Shell PHP tarafından üretilir | HTTP | `HtmlShellRenderer::render()` | IMPLEMENTED (web) |
| 7 | SPA router devralır | renderer | `js/main.js` import zinciri | IMPLEMENTED (web) |
| 8 | Viewport cookie yazılır | renderer | `cm_viewport_w/h` | IMPLEMENTED (web) |
| 9 | Cihaz tier'ı tespit edilir | renderer | `device-loader.js detect()` | IMPLEMENTED (web) |
| 10 | CSS katmanı yüklenir | renderer | `d-desktop.css` | IMPLEMENTED (css) |
| 11 | API istekleri gider | renderer→HTTP | `api.coremusic.net` | IMPLEMENTED (local-runtime) |
| 12 | Pencere kapanışında temizlik | main | `window-all-closed` | PLANNED |
| 13 | Çıkış | main | `app.quit()` | PLANNED |
| 14 | Paketleme | build | forge/builder config | PLANNED |

**Kritik gözlem (§4.5):** akışın 5,6,7,8,9,10,11. adımları repo'da
`IMPLEMENTED`'dır — ama bunlar **tarayıcı** içindir. Electron katmanı, bu
adımların *hiçbirisini* değiştirmeden yalnız 1-4, 12-14 adımlarını ekler.
Bu yüzden "Electron kodu yok" ifadesi, "uygulama yok" anlamına gelmez.

---

## §5 — Disk kanıtı (okunmuş dosyalar)

| # | Dosya | Satır | Ne kanıtlıyor |
|---|---|---|---|
| 1 | `Dosya: C:\www\coremusic.net\package.json · Satır: 10` | 10 | devDependencies: playwright/vitest — `electron` YOK |
| 2 | `Dosya: C:\www\coremusic.net\.ai\architecture\K011-application\electron\index.md · Satır: 20` | 20 | ELI15: "Electron çatısıyla paketlenen masaüstü kabuğu" |
| 3 | `Dosya: C:\www\coremusic.net\.ai\architecture\K011-application\electron\index.md · Satır: 26` | 26 | Durum: draft · kademe-3/4 ADR-097 faz (C) bekliyor |
| 4 | `Dosya: C:\www\coremusic.net\.ai\architecture\K011-application\index.md · Satır: 15` | 15 | ebeveyn `children:` listesinde `electron` |
| 5 | `Dosya: C:\www\coremusic.net\.ai\architecture\K011-application\index.md · Satır: 35` | 35 | açık soru: desktop↔electron sınırı |
| 6 | `Dosya: C:\www\coremusic.net\.ai\architecture\K011-application\index.md · Satır: 47` | 47 | v2 §2.11 alıntısı ("Electron çatısı") |
| 7 | `Dosya: C:\www\coremusic.net\.ai\architecture\K011-application\application-master.md · Satır: 96` | 96 | "electron · 362 · yalnız index.md mevcut · verify-required" |
| 8 | `Dosya: C:\www\coremusic.net\.ai\architecture\K011-application\desktop\index.md · Satır: 22` | 22 | sınır: "Electron paketleme K011/electron'da" |
| 9 | `Dosya: C:\www\coremusic.net\shared\src\PageRouter\HtmlShellRenderer.php · Satır: 68` | 68 | shell üretimi `render()` — Electron'un yükleyeceği hedef |
| 10 | `Dosya: C:\www\coremusic.net\assets.coremusic.net\js\main.js · Satır: 9` | 9 | browser entry: `import Router from './router/Router.js'` |
| 11 | `Dosya: C:\www\coremusic.net\assets.coremusic.net\js\devices.config.js · Satır: 18` | 18 | `'desktop': '08_Devices/d-desktop.css'` eşlemesi |
| 12 | `Dosya: C:\www\coremusic.net\.ai\.sql\mysql\coremusic_user.sql · Satır: 92` | 92 | `device_type` enum ilk değer `desktop` |
| 13 | `Dosya: C:\www\coremusic.net\README.md · Satır: 107` | 107 | yığın: PHP 8.4 + Node.js — Electron satırı YOK |

**Tarama kanıtı (negatif):** `electron|tauri|nw.js|cef` kalıbı `package.json`
içinde **0 isabet**; `**/*electron*` glob → yalnız
`--\architecture\07-security\electronics-security.md` (yanlış eşleşme,
"electronics"); `*.csproj|*.sln` glob → 0.

---

## §6 — Olgunluk ve etiket matrisi

| # | Yüzey | Etiket | Dayanak |
|---|---|---|---|
| 1 | Electron bağımlılığı | PLANNED | `package.json` 0 isabet |
| 2 | Main süreci | PLANNED | dosya yok |
| 3 | Renderer (web) | IMPLEMENTED | `js/main.js` + `js/router/` |
| 4 | Shell üretimi (PHP) | IMPLEMENTED | `HtmlShellRenderer.php` |
| 5 | Cihaz tespiti | IMPLEMENTED | `device-loader.js` satır 315-341 |
| 6 | Cihaz CSS'i | IMPLEMENTED | `Css/08_Devices/d-desktop.css` eşlemesi |
| 7 | SQL cihaz etiketi | IMPLEMENTED | `coremusic_user.sql:92` |
| 8 | Paketleme | PLANNED | yapılandırma yok |
| 9 | Installer | PLANNED | `.nsi/.iss/.msi` 0 |
| 10 | Otomatik güncelleme | PLANNED | — |
| 11 | Güvenlik checklist (Electron) | ⚠️ VERIFICATION REQUIRED | web CSP var, pencere testi yok |
| 12 | Tek örnek kilidi | PLANNED | — |
| 13 | IPC kanalları | PLANNED | — |
| 14 | Dosya sistemi köprüsü | PLANNED | — |

**Etiket kuralı:** `IMPLEMENTED` yalnızca gerçek dosya + satır ile yazılır;
hiçbir satırda Electron kodu için `IMPLEMENTED` kullanılmamıştır.

---

## §7 — Kapsam dışı

| # | Konu | Sahip düğüm | Gerekçe |
|---|---|---|---|
| 1 | Pencere içi UI çizimi | `web` + `desktop` | CSS/JS orada |
| 2 | HTTP sunucusu | `local-runtime` / `server-runtime` | `php -S` / nginx |
| 3 | Kimlik doğrulama akışı | `K009-security` | auth ayrı katman |
| 4 | Veritabanı erişimi | `K005-K006` | PDO/shared |
| 5 | Medya dosyası okuma | `K015-media` | sunucu tarafı |
| 6 | Sürücü/donanım erişimi | `L0` | ASIO/WASAPI ayrı |
| 7 | CI paketleme hattı | `K017-devops` | `.github/workflows/` |
| 8 | Lisans/üçüncü taraf denetimi | `dependency-manager` | — |
| 9 | Kademe-3 bounded-context | ADR-097 faz (C) | bekliyor |
| 10 | Kurulum/dağıtım politikası | ⚠️ VERIFICATION REQUIRED | karar yok |

---

## §8 — Açık sorular

| # | Soru | Etki | Etiket |
|---|---|---|---|
| 1 | Electron bir ürün mü, desktop'ın paketleme yolu mu? | Kapsam genişliği | ⚠️ VERIFICATION REQUIRED |
| 2 | desktop ↔ electron sınırı: aynı kabuk mu, ayrı paket mi? | Klasör sorumluluğu | ⚠️ VERIFICATION REQUIRED |
| 3 | Electron sürümü hangi Node/Chromium tabanıyla sabitlenecek? | Bağımlılık kararı | ⚠️ VERIFICATION REQUIRED |
| 4 | Paketleyici: Electron Forge mu, Builder mı? | Build hattı | ⚠️ VERIFICATION REQUIRED |
| 5 | Uygulama, `localhost` sunucusuna mı bağlanacak, dosyayı mı yükleyecek? | Mimari | ⚠️ VERIFICATION REQUIRED |
| 6 | `file://` yerine özel protokol kullanılacak mı? | Güvenlik | ⚠️ VERIFICATION REQUIRED |
| 7 | Renderer `nodeIntegration` kapalı mı olacak? | Güvenlik | hedef: kapalı (E2) |
| 8 | Oturum çerezi pencere içinde paylaşılacak mı? | Auth | ⚠️ VERIFICATION REQUIRED |
| 9 | Dosya sisteri erişimi hangi medya yolu için gerekli? | Kapsam | ⚠️ VERIFICATION REQUIRED |
| 10 | Hedef platformlar (Windows/Linux/macOS) hangi sırayla? | Roadmap | ⚠️ VERIFICATION REQUIRED |
| 11 | Otomatik güncelleme gerekiyor mu? | Kapsam | ⚠️ VERIFICATION REQUIRED |
| 12 | Kademe-3 bounded-context kapsamı ne zaman açılır? | Faz | ADR-097 faz (C) |

**Açık soru sayısı: 12** — hiçbiri bu dosyada kapatılmaz.

---

## §9 — Riskler

| # | Risk | Olasılık | Etki | Azaltma |
|---|---|---|---|---|
| 1 | Uydurma kanıt üretme baskısı (kod yokken "var" demek) | Orta | Yüksek | §5/§6 etiket disiplini |
| 2 | desktop ↔ electron iki klasörde çift kod | Yüksek | Orta | §2 sınırı + açık soru #2 |
| 3 | Chromium sürüm drift'i | Orta | Yüksek | sürüm kilidi (PLANNED) |
| 4 | CSP nonce'un pencere içinde çalışmaması | Orta | Yüksek | ⚠️ VERIFICATION REQUIRED |
| 5 | Paket boyutu / ilk açılış süresi | Orta | Orta | PLANNED ölçüm |
| 6 | Medya yollarının yerel dosya sistemine taşınması | Düşük | Yüksek | K015 sınırı |
| 7 | Güvenlik checklist'inin (E1-E20) uygulanmaması | Yüksek | Yüksek | §12 kapısı |
| 8 | `desktop` klasörüyle eşzamanlı yazma çatışması | Orta | Orta | klasör izni: yalnız `electron/` |

---

## §10 — Bağımlılıklar

| # | Bağımlılık | Yön | Tür | Durum |
|---|---|---|---|---|
| 1 | `web` (HTML shell + SPA router) | içerir | kod | IMPLEMENTED |
| 2 | `desktop` (pencere kabuğu) | komşu | mimari | sınır açık (§8.2) |
| 3 | `local-runtime` (PHP entry) | çağırır | HTTP | IMPLEMENTED |
| 4 | `server-runtime` (nginx) | çağırır | HTTP | IMPLEMENTED |
| 5 | `application-services` (BFF) | çağırır | API | `DesktopBff.php` mevcut |
| 6 | `K009-security` (CSP/CSRF) | alır | politika | IMPLEMENTED |
| 7 | `K017-devops` (CI) | bekler | pipeline | ⚠️ VERIFICATION REQUIRED |
| 8 | `K011-application` (üst düğüm) | bağlı | hiyerarşi | `index.md` children |

**İlginç kanıt:** `shared/src/Api/Bff/DesktopBff.php` satır 37'de
`$_SERVER['SERVER_SOFTWARE']` okur — yani "desktop" için ayrı bir BFF
katmanı **vardır**; ancak bu, Electron paketlemesi değildir (§2 #1).

---

## §11 — Rol eşlemesi

| # | Agent | Bu klasördeki işi | Dokunabileceği | Dokunamayacağı |
|---|---|---|---|---|
| 1 | `backend-architect` | Electron main/preload tasarımı (PLANNED) | bu klasör .md | `*.php/*.js/*.css/*.sql` |
| 2 | `frontend-developer` | Renderer entegrasyonu (PLANNED) | bu klasör .md | `assets.coremusic.net/**` |
| 3 | `security-engineer` | E1-E20 checklist denetimi | bu klasör .md | üretim kodu |
| 4 | `devops-engineer` | Paketleme hattı (PLANNED) | bu klasör .md | `.github/workflows/` |
| 5 | `docs-writer` | Bu 5 dosyanın üretimi | `electron/*.md` | başka klasör |
| 6 | `qa-engineer` | Kapı ölçümü (§12) | bu klasör .md | test dosyaları |
| 7 | MO (orkestratör) | Commit | — | subagent commit yasak |

---

## §12 — Kapı özeti (kademe-2)

| # | Kapı | Kriter | Durum |
|---|---|---|---|
| 1 | Satır | 5 içerik dosyası her biri ≥500 | ölçülür (§A) |
| 2 | Frontmatter | ≥7 alan, son alan `group:` | uygulandı |
| 3 | §GRUPLAR | ≥6 satır iki-eksenli tablo | uygulandı |
| 4 | Footer | Authority + Last Updated: 2026-10-15 | uygulandı |
| 5 | Kanıt | her dosyada ≥1 `Dosya: · Satır:` | uygulandı |
| 6 | URL | havuz 5 URL, 200, kesişim 0 | `-web-arastirma.md` |
| 7 | Uydurma kanıt | 0 | §6 etiket disiplini |
| 8 | Klasör yazma | yalnız `electron/` | kısıt |

---

## §A — Ek: Ölçüm prosedürü

```powershell
$root = "C:\www\coremusic.net\.ai\architecture\K011-application"
$d = "$root\electron"
Get-ChildItem $d -Filter *.md | ForEach-Object {
  "{0}  {1}" -f [System.IO.File]::ReadAllLines($_.FullName).Length, $_.Name
}
# Beklenen: 5 içerik dosyası >=500, index.md 40-100
```

```powershell
# Electron kod taraması (negatif kanıt)
Get-ChildItem -Recurse -File -Force -Include *.json,*.js,*.ts C:\www\coremusic.net |
  Where-Object { $_.FullName -notmatch 'node_modules|\.git\\' } |
  Select-String -Pattern 'require\(.electron.\)|from .electron.|BrowserWindow|electron-builder' -List |
  Measure-Object   # Count = 0 beklenir
```

## §13 — Güvenlik Checklist E1–E20 (Electron'a özgü — PLANNED kapısı)

> Bu checklist, Electron katmanı **kodlandığında** uygulanacak kapıdır. Şu an
> 20 maddenin tamamı `PLANNED`'dır; hiçbir madde "uygulandı" diye işaretlenmez.

| # | Madde | Beklenen ayar | Durum | Dayanak |
|---|---|---|---|---|
| E1 | `contextIsolation` | `true` | PLANNED | §4.4 |
| E2 | `nodeIntegration` | `false` | PLANNED | §4.4 |
| E3 | `sandbox` | `true` | PLANNED | §4.4 |
| E4 | Preload minimalliği | yalnız contextBridge API | PLANNED | §4.4 |
| E5 | `webSecurity` | açık (asla `false`) | PLANNED | §2 sınırı |
| E6 | Harici link | `shell.openExternal` + allowlist | PLANNED | — |
| E7 | `will-navigate` engeli | izinsiz navigasyon yasak | PLANNED | — |
| E8 | CSP | web'deki nonce politikasının pencere sürümü | ⚠️ VERIFICATION REQUIRED | §9 risk 4 |
| E9 | IPC kanal allowlist | her kanal tek yönlü tercih | PLANNED | §3.13 |
| E10 | `remote` modülü | yasak (deprecated + tehlikeli) | PLANNED | — |
| E11 | Oturum çerezi davranışı | web ile eşdeğer mi? | ⚠️ VERIFICATION REQUIRED | §3.15 |
| E12 | Dosya sistemi erişimi | yalnız medya kütüphanesi + izin | PLANNED | §3.14 |
| E13 | Tek örnek kilidi | `requestSingleInstanceLock` | PLANNED | §3.7 |
| E14 | Çıkış temizliği | `window-all-closed` + kaynak serbest bırakma | PLANNED | §4.12 |
| E15 | Otomatik güncelleme imzası | code-sign + delta update | PLANNED | §14 |
| E16 | Sürüm kilidi (Chromium) | Electron sürümü semver kilidi | PLANNED | §9 risk 3 |
| E17 | Derleme kaynak gizliliği | asar + kaynak harita dağıtımı yok | PLANNED | — |
| E18 | Zararlı bağımlılık taraması | CI'da `audit` kapısı | ⚠️ VERIFICATION REQUIRED (K017) | §10.7 |
| E19 | Crash raporu gizliliği | kullanıcı verisi redaction | PLANNED | §6 log kuralı |
| E20 | Kurulum programı imzası | NSIS/MSI imzalı | PLANNED | §3.16 |

**Kapı kuralı:** E1–E20'nin ≥18/20'si "uygulandı" olmadan paket dağıtımı
**başlatılmaz** (§12 kapı özeti ile birlikte okunur).

---

## §14 — Paketleme ve Dağıtım Planı (PLANNED)

| # | Adım | Araç (aday) | Çıktı | Bağımlılık |
|---|---|---|---|---|
| 1 | Sürüm kilidi | `package.json` | Electron semver | §13-E16 |
| 2 | Yapılandırma | forge veya builder | config dosyası | §3.5 |
| 3 | İkon/asset | statik klasör | `.ico/.icns` | §3.6 |
| 4 | Derleme | CI hattı | Windows NSIS + portable | K017 (⚠️ kapı yok) |
| 5 | İmzalama | code-sign sertifikası | imzalı `.exe` | sertifika envanteri ⚠️ |
| 6 | Otomatik güncelleme | updater + imzalı feed | delta paket | §13-E15 |
| 7 | Yayın | dağıtım kanalı | kullanıcı | K016-kurulum alanı |

> Bu planın hiçbir adımında disk kanıtı yoktur; adım sayıları **hedef**
> mimarisidir. Araç seçimi (forge ↔ builder) açık karardır → ⚠️ VERIFICATION
> REQUIRED.

---

## §15 — Karşılaştırma: Electron ↔ PWA ↔ Yerel PHP Kabuğu

| # | Kriter | Electron | PWA | Mevcut repo (PHP kabuğu) |
|---|---|---|---|---|
| 1 | Dosya sisteri erişimi | var (izinli) | sınırlı (FS Access API) | yok (sunucu tarafı) |
| 2 | Oturum çerezi | pencere içi | tarayıcı | sunucu |
| 3 | Kurulum hissi | `.exe` | "ana ekrana ekle" | — |
| 4 | Güncelleme | updater feed | service worker | deploy |
| 5 | Boyut | ~150MB+ | ~0 | 0 |
| 6 | Repo durumu | YOK (§3) | KISMEN (web manifest ⚠️) | IMPLEMENTED |
| 7 | CSP sahipliği | pencere sürümü gerek | web'deki yeter | IMPLEMENTED (web) |

**Karar tablosu (etkisiz):** Bu karşılaştırma bir seçim **dayanağı değildir**;
seçim kullanıcı onayı + ADR gerektirir (§8 açık soru #2).

---

## §16 — Sınır Testleri (electron ↔ komşuları)

| # | Soru | Sınır testi | Karar |
|---|---|---|---|
| 1 | `main.js` hangi klasörde? | browser entry `assets.../js/main.js` ≠ Electron main | Electron main henüz YOK |
| 2 | Pencere kabuğu kimde? | `desktop/` mi `electron/` mi? | açık soru §8.2 — kullanıcı onayı |
| 3 | BFF farkı ne? | `DesktopBff.php` yalnız UA tespiti | paketleme değil |
| 4 | CSS etkisi var mı? | `d-desktop.css` zaten var | Electron ek CSS istemez |
| 5 | Router etkisi var mı? | `Router.js` renderer'da çalışır | değişiklik yok |
| 6 | API adresi değişir mi? | `api.coremusic.net` sabit | hayır |
| 7 | Oturum nerede? | sunucu tarafı | pencere yalnız taşır |

---

## §B — Ek: Bu dosyanın üretim ölçümü

| # | Metrik | Değer |
|---|---|---|
| 1 | Dosya satırı (son) | 500+ |
| 2 | Kapsam maddesi | 18 (§3) |
| 3 | E-checklist | 20 (§13) |
| 4 | PLANNED etiketi | baskın (§3–§4, §13–§14) |
| 5 | IMPLEMENTED (bu klasör) | 0 (§3 özeti) |
| 6 | ⚠️ VERIFICATION REQUIRED | §3.8/15/17 · §8 · §9.4 · §13-E8/E11/E18 |
| 7 | Üretim tarihi | 2026-10-15 |

## §17 — Electron Ana Süreç Şablonu (PLANNED — kod yok, yalnız yapı)

> Aşağıdaki blok **şablon**tur, repo kodu değildir; Electron katmanı
> kodlandığında başlangıç noktası olması için burada durur. Hiçbir satır
> `Dosya: · Satır:` kanıtı taşımaz → kaynak `PLANNED`.

```js
// PLANNED — electron/main.js (repo'da YOKTUR)
// E1: contextIsolation true · E2: nodeIntegration false · E3: sandbox true
// E13: tek örnek kilidi · E7: will-navigasyon engeli
const { app, BrowserWindow, shell } = require('electron');

let win = null;

app.whenReady().then(() => {
  if (!app.requestSingleInstanceLock()) { app.quit(); return; }   // E13
  win = new BrowserWindow({
    contextIsolation: true,     // E1
    nodeIntegration: false,     // E2
    sandbox: true,              // E3
    webSecurity: true,          // E5 (asla false)
    preload: PRELOAD_PATH,      // E4: minimal contextBridge
  });
  win.loadURL(APP_ENTRY_URL);   // §4.5: PHP shell (IMPLEMENTED web)
  win.webContents.setWindowOpenHandler(({ url }) => {
    if (ALLOWED_HOSTS.includes(new URL(url).host)) shell.openExternal(url); // E6
    return { action: 'deny' };
  });
  win.webContents.on('will-navigate', (e, url) => {              // E7
    if (new URL(url).origin !== APP_ORIGIN) e.preventDefault();
  });
});

app.on('window-all-closed', () => { app.quit(); });              // E14
```

**Şablon kuralı:** Bu blok uygulamaya alınacağı gün §3 kapsam tablosundaki
ilgili satır `PLANNED → IMPLEMENTED` olarak işaretlenir; tersi (kod varken
belge PLANNED kalması) kapalıdır.

---

## §18 — Pencere Olay Yaşam Döngüsü (PLANNED haritası)

| # | Olay | Süreç | Beklenen davranış | Kapı |
|---|---|---|---|---|
| 1 | `ready` | main | tek örnek kilidi + pencere kurulumu | E13 |
| 2 | `activate` (macOS) | main | pencere yeniden açılır | — |
| 3 | `browser-window-created` | main | tüm pencerelerde güvenlik ayarı denetimi | E1–E3 |
| 4 | `did-finish-load` | renderer | shell yüklendi, SPA router devrede | §4.7 |
| 5 | `ipc-renderer→main` | bridge | allowlist dışı kanal reddedilir | E9 |
| 6 | `before-quit` | main | oturum temizliği / flush | E14 |
| 7 | `render-process-gone` | main | yeniden başlatma veya hata ekranı | E19 |
| 8 | `child-process-gone` | main | PHP yan süreç ölümü raporu | — |

---

## §19 — Sürüm ve Uyumluluk Kilidi (PLANNED)

| # | Bileşen | Kilitleme | Açıldığında etki |
|---|---|---|---|
| 1 | Electron | semver kilidi (§13-E16) | Chromium drift'i riski kapanır |
| 2 | Node (build) | `.nvmrc`/engines | CI tekrarlanabilirliği |
| 3 | PHP (yan süreç) | 8.4 | shell üretimi değişmez |
| 4 | WebView davranışı | — | masaüstü ≠ tarayıcı test farkı |
| 5 | OS hedefi | Win10+ (ilk hedef) | NSIS/MSI seçimi |

---

## §C — Ek: Kesişim ve komşu kontrolü (bu dosya üretiminde)

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | `desktop/` klasöründen içerik kopyalandı mı? | HAYIR — sınır §2/§16 |
| 2 | `web/` klasöründen içerik kopyalandı mı? | HAYIR — yalnız referans |
| 3 | Başka K011 dosyasıyla aynı bölüm başlığı var mı? | Hayır (§13–§19 benzersiz) |
| 4 | Uydurma Electron kodu `Dosya:·Satır:` ile mi sunuldu? | HAYIR — §17 bloğu PLANNED etiketli |
| 5 | Footer tarihi | 2026-10-15 |

## §D — Ek: Bilinen Sürücü/Yaygın Tuzaklar (PLANNED — kod yazım öncesi okunur)

| # | Tuzak | Sonuç | Kapı |
|---|---|---|---|
| 1 | `require` ile Node entegrasyonunu renderer'a açmak | XSS → tam sistem erişimi | E2 |
| 2 | `contextIsolation: false` | preload Node'a erişir, sayfa sızdırır | E1 |
| 3 | BilinmeyenOrigin navigasyonuna izin | phishing penceresi | E7 |
| 4 | IPC kanalına doğrudan `ipcMain.handle` genişliği | yetkisiz eylem | E9 |
| 5 | `will-navigate`'ı atlamak | uygulama içi tarayıcı olur | E7 |
| 6 | Güncelleme feed'ini imzasız kullanmak | zararlı paket | E15 |
| 7 | Tek örnek kilidini atlamak | iki kopya → çakışan veri | E13 |
| 8 | Pencere kapanışında flush'ı unutmak | yarım yazma | E14 |

**Okuma kuralı:** Bu tablo, Electron katmanı kodlanmadan **önce** okunur;
kodlanırken her satır ilgili E-kapısıyla birlikte denetlenir (§13).

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
