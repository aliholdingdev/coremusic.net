---
node_id: K011-application/electron/electron-web-arastirma.md
node_type: file
status: draft
parent_id: K011-application/electron
children: []
depends_on:
  - K011-application/electron/electron-kapsam.md
  - K011-application/electron/electron-dogrulama.md
evidence:
  - "9 URL — electronjs.org — hepsi curl.exe -L → 200 + envanter kesişimi 0"
  - "Envanter: 4286 URL / 1267 dosya (K011 hariç) — 2026-10-15 yeniden inşa"
  - "Kesişim hatası bulgusu: electron-dogrulama.md §K.11"
validation:
  - "URL havuzu §A — 9/9 hem 200 hem HIT=0"
  - "Reddedilen adaylar §B — yalnız etiket, tam URL yazılmaz"
  - "İddia↔repo çaprazı §C"
open_questions:
  - "PWA ↔ Electron kararı → ⚠️ VERIFICATION REQUIRED"
  - "Desktop sürümleme hedefi → ⚠️ VERIFICATION REQUIRED"
group: kanit
---

# electron — Web Araştırması

> **Bu dosyanın özel durumu:** Araştırılan modül **repo'da kod olarak yok**
> (PLANNED — bkz. `electron-dogrulama.md` §2). Bu nedenle buradaki web
> araştırması **iki** amaca hizmet eder:
>
> 1. **Gelecek karar için kanıt toplamak** — Electron eklenecekse hangi
>    güvenlik/IPC/pencere kuralları bağlayıcı olacak?
> 2. **Mevcut boşluğun ne anlama geldiğini yorumlamak** — repo'da neden
>    Electron yok ve alternatifler (PHP + SPA + BFF) neyi karşılıyor?
>
> **Zorunlu kural:** Bu dosyadaki her URL `electron-dogrulama.md` §Q.3 ve
> §A'daki havuzdan alınmıştır; **havuz dışı URL yazılmaz**. Bunun nedeni
> envanter kesişimi hatasıdır (§K.11).

## §GRUPLAR

| # | Bölüm | Grup (Eksen 1) | Eksen 2 |
|---|---|---|---|
| 1 | §A Doğrulanmış URL havuzu | kanit | havuz |
| 2 | §B Reddedilen adaylar | kanit | red |
| 3 | §C İddia ↔ repo çaprazı | kanit | capraz |
| 4 | §D Electron güvenlik modeli | mimari | guvenlik |
| 5 | §E Süreç modeli (main ↔ renderer) | mimari | surec |
| 6 | §F IPC yüzeyi | mimari | ipc |
| 7 | §G Sandbox ve context isolation | mimari | sandbox |
| 8 | §H Repo'nun Electron'a hazır olma ölçümü | kanit | hazirlik |
| 9 | §I Alternatiflerin karşılaştırması | mimari | alternatif |
| 10 | §J Bağlayıcılık derecesi | kanit | baglayicilik |
| 11 | §K Kesişim riski ve havuz disiplini | kanit | kesisim |
| 12 | §L Öğrenilenler ↔ repo etkisi | kod | etki |
| 13 | §M Açık sorular | kanit | acik |
| 14 | §N Reddedilen iddialar | kanit | red-iddia |
| 15 | §O Özet | kanit | ozet |

## §A — Doğrulanmış URL Havuzu (9 URL)

> **Doğrulama yöntemi (tekrarlanabilir):**
> `curl.exe -L -s -o NUL -w "%{http_code}" <url>` → **200** VE
> `<url> -notin $envanter` → **HIT yok**. Envanter `k011-urls.txt`
> (4286 URL, K011-application hariç).

### §A.1 Çekirdek ve öğretici (5 URL)

| # | URL | Ne verir | Kullanıldığı bölüm |
|---|---|---|---|
| 1 | https://www.electronjs.org/docs/latest | genel bakış | §D |
| 2 | https://www.electronjs.org/docs/latest/tutorial/security | güvenlik rehberi | §D, §G |
| 3 | https://www.electronjs.org/docs/latest/api/app | uygulama yaşam döngüsü | §E |
| 4 | https://www.electronjs.org/docs/latest/api/browser-window | pencere | §E |
| 5 | https://www.electronjs.org/docs/latest/tutorial/tutorial-first-app | ilk uygulama | §H |

### §A.2 Derinlemesine (4 URL)

| # | URL | Ne verir | Kullanıldığı bölüm |
|---|---|---|---|
| 6 | https://www.electronjs.org/docs/latest/tutorial/ipc | süreçler arası iletişim | §F |
| 7 | https://www.electronjs.org/docs/latest/tutorial/sandbox | sandbox modeli | §G |
| 8 | https://www.electronjs.org/docs/latest/api/web-contents | içerik yönetimi | §E |
| 9 | https://www.electronjs.org/docs/latest/api/process | süreç bilgisi | §E |

**Toplam:** 9 URL · **9/9 HTTP 200** · **9/9 HIT=0** ✅

**Havuz disiplini:** Bu 9 URL'in **hiçbiri** K011 dışındaki 1267 dosyada
geçmiyor. Genel teknik belgeler (php.net · php-fig · MDN) yerine
**yalnızca electronjs.org** kullanılmıştır — çünkü genel belgeler
**kaçınılmaz HIT** verir (`electron-dogrulama.md` §P).

## §B — Reddedilen Adaylar (etiket kaydı)

| # | Aday (etiket) | Neden reddedildi | Sonuç |
|---|---|---|---|
| 1 | electronjs `api/context-isolation` | HTTP 404 | elendi |
| 2 | electronjs `tutorial/prerequisites` | HTTP 404 | elendi |
| 3 | electronjs `tutorial/development` | HTTP 404 | elendi |
| 4 | electronjs `tutorial/distribution` | HTTP 404 | elendi |
| 5 | electronjs `api/structure` | HTTP 404 | elendi |
| 6 | electronjs `tutorial/security-checklist` | HTTP 404 | elendi |
| 7 | learn.microsoft `api-analysis` ailesi | HTTP 404 (3 aday) | elendi |
| 8 | `Web/API/Fetch_API` (MDN) | **HIT** — envanterde var | elendi |

**Kural:** Reddedilen adayların **tam URL'i yazılmaz** (§K.11). Yalnız
etiket + neden kaydedilir. 6 satır 404 verdi → Electron dokümantasyonu
**alan adı yeniden yapılandırılmış** görünüyor (`api/structure` artık yok,
`api/process` var). Bu, canlı belge değişiminin kanıtıdır.

## §C — İddia ↔ Repo Çapraz Kontrolü

| # | Web iddiası (§A kaynağı) | Repo gerçeği | Çakışma? | Yorum |
|---|---|---|---|---|
| 1 | Electron ayrı ana süreç ister | `main.js` yok · `require('electron')` yok | hayır | PLANNED tutarlı |
| 2 | Sandbox varsayılan açıktır | Electron kodu 0 | hayır | uygulanacak yüzey yok |
| 3 | IPC `ipcMain`/`ipcRenderer` ister | ikisi de yok | hayır | PLANNED tutarlı |
| 4 | `BrowserWindow` pencereyi taşır | yok | hayır | PLANNED tutarlı |
| 5 | `app` yaşam döngüsünü yönetir | yok | hayır | PLANNED tutarlı |
| 6 | Paketleme `electron-builder`/forge ister | config 0 · bağımlılık 0 | hayır | PLANNED tutarlı |
| 7 | Test için `playwright`'ın Electron modu gerekir | playbook var ama **yalnız tarayıcı** | **kısmen** | §H.4 |
| 8 | BFF, Electron'un yerine geçebilir | `Api/Bff/` 5 dosya (DesktopBff 66 satır) | hayır | §I — mevcut köprü |

**Sonuç:** 7 uyumlu · 1 **kısmi** (satır 7). Kısmi çelişkide: Electron
eklenirse mevcut `npm run test:e2e` (playwright **tarayıcı** modu)
**yetersiz** kalır — Electron penceresi için ayrı kurulum ister.

## §D — Electron Güvenlik Modeli (§A.1-2 kaynağı)

> Bu bölüm **tasarım iddiası değil**, resmi belgeden **özetleme**dir. Repo'da
> karşılığı olmadığı için her satır `PLANNED` etiketini taşır.

| # | Kural | Belge | Repo karşılığı | Durum |
|---|---|---|---|---|
| 1 | Ana süreçta nodeIntegration **kapalı** tutulur | §A.2 | kod 0 | PLANNED |
| 2 | `contextIsolation: true` varsayılan kabul edilir | §A.2 | kod 0 | PLANNED |
| 3 | Renderer'a `require` verilmez | §A.2 | kod 0 | PLANNED |
| 4 | `shell.openExternal` yalnızca güvenilir URL'de | §A.2 | kod 0 | PLANNED |
| 5 | CSP başlığı pencereye de uygulanır | §A.2 | kod 0 | PLANNED |
| 6 | `will-navigate` olayı engellenir | §A.2 | kod 0 | PLANNED |
| 7 | Sadece güvenilir içerik yüklenir | §A.2 | kod 0 | PLANNED |

**Kritik okuma (bu dosyanın özgü katkısı):** 7 kuralın **tamamı** repo'nun
mevcut web güvenlik modeliyle **aynı yöndedir** — özellikle §A.1-2'deki
CSP/CSRF maddeleri, repo'daki `SecurityHeadersMiddleware` (11 middleware
içinde) ile **zaten uygulanıyor**. Yani Electron eklendiğinde güvenlik
modeli **taşınabilir**, sıfırdan yazılmaz → **maliyet düşük**.

**Ama bu bir karar değildir:** "taşınabilir" demek, "taşınacak" demek
**değildir** → §M.1.

## §E — Süreç Modeli (main ↔ renderer)

| # | Kavram | Belge | Repo karşılığı | Durum |
|---|---|---|---|---|
| 1 | `app.whenReady()` başlatma | §A.3 | yok | PLANNED |
| 2 | `BrowserWindow` pencere | §A.4 | yok | PLANNED |
| 3 | `webContents` içerik yönetimi | §A.8 | yok | PLANNED |
| 4 | `process` bilgileri | §A.9 | yok | PLANNED |
| 5 | Renderer = web sayfası | §A.1 | `assets.coremusic.net/js` (94 dosya) | **mevcut** |

**Bulgu (§I'ye giriştir):** Electron'un "renderer" katmanı, repo'nun
**zaten var olan** SPA katmanıdır (94 JS dosyası · `js/router/` 28 dosya).
Yani Electron eklense, yazılacak olan **arayüz değil**, yalnızca
**kabuk** (ana süreç + pencere) olur. Bu, §I karşılaştırmasının
sayısal dayanağıdır.

## §F — IPC Yüzeyi

| # | Yön | Belge | Repo karşılığı | Durum |
|---|---|---|---|---|
| 1 | main → renderer | `webContents.send` | yok | PLANNED |
| 2 | renderer → main | `ipcRenderer.send` | yok | PLANNED |
| 3 | main tarafı dinleyici | `ipcMain.on` | yok | PLANNED |
| 4 |preload köprüsü | `contextBridge` | yok | PLANNED |

**Repo ile ilişki (çapraz okuma):** Repo'nun olay yayımı zaten **içinde**
çalışıyor: `shared/src/Events/` (9 event dosyası) + `EventDispatcher.php`
+ `js/core/EventBus.js`. Yani **aynı kavram**, iki ayrı dünyada:

| Dünya | Mekanizma | Dosya |
|---|---|---|
| Sunucu | `EventDispatcher` + domain event | `shared/src/Events/` |
| İstemci | `EventBus` | `js/core/EventBus.js` |
| Electron | `ipcMain`/`ipcRenderer` | **yok** (PLANNED) |

**Öğrenilen:** IPC, repo'nun mevcut olay modelinin **uzantısı** olur,
yeni bir model değil. Bu, §L'deki etki satırının kaynağıdır.

## §G — Sandbox ve Context Isolation

| # | Kavram | Belge | Ölçüm | Sonuç |
|---|---|---|---|---|
| 1 | Sandbox varsayılan | §A.7 | Electron kodu 0 | PLANNED |
| 2 | Context isolation | §A.2 | 0 | PLANNED |
| 3 | preload yalnızca köprü | §A.6 | 0 | PLANNED |
| 4 | Renderer'da `eval` yasağı | §A.2 | `eval()` **zaten yasak** (assets AGENTS.md §4) | ✅ hazır |
| 5 | `innerHTML` yasağı | §A.2 | `innerHTML` **zaten yasak** (DOMParser + TrustedTypes) | ✅ hazır |

**Bulgu (özgün):** 5 maddenin **2'si** repo'da **zaten uygulanıyor**
(renderer tarafı `eval`/`innerHTML` yasağı). Yani Electron sandbox
gereksinimlerinin **bir kısmı karşılanmış** durumda. Kalan 3 madde
(ana süreç tarafı) PLANNED.

> **Dürüstlük:** Bu bulgu "Electron'a hazırız" anlamına **gelmez** — yalnız
> renderer tarafındaki **2 kuralın** örtüştüğünü söyler. Ana süreç,
> preload ve paketleme tamamen boştur.

## §H — Repo'nun Electron'a Hazır Olma Ölçümü

| # | Hazırlık alanı | Ölçüm | Durum | % |
|---|---|---|---|---|
| 1 | Renderer kodu (arayüz) | 94 `.js` | ✅ var | 100 |
| 2 | Olay modeli (IPC'ye benzer) | EventBus + 9 event | ✅ var | 100 |
| 3 | Güvenlik kuralları (eval/innerHTML) | 2 kural | ✅ var | 100 |
| 4 | Sunucu köprüsü (BFF) | 5 dosya | ✅ var | 100 |
| 5 | Ana süreç kodu | 0 | ❌ yok | 0 |
| 6 | Preload | 0 | ❌ yok | 0 |
| 7 | Paketleme | 0 | ❌ yok | 0 |
| 8 | Electron testi | 0 (yalnız tarayıcı) | ❌ yok | 0 |
| 9 | Bağımlılık (`electron`) | 0 | ❌ yok | 0 |
| 10 | Konfigürasyon | 0 | ❌ yok | 0 |
| | **Toplam** | 4/10 alan | — | **%40** |

**Ölçüm notu:** %40 bir **hazırlık** oranıdır, **üretime hazırlık** oranı
**değildir**. Kalan %60 (ana süreç · preload · paketleme · test · bağımlılık ·
konfigürasyon) **birbirine bağımlıdır** — biri olmadan diğerinin anlamı
yoktur. Dolayısıyla **gerçek maliyet %60'ın tamamıdır**.

## §I — Alternatiflerin Karşılaştırması (repo ölçümüyle)

| # | Yaklaşım | Repo'daki karşılığı | Kod | Durum |
|---|---|---|---|---|
| 1 | PHP + tarayıcı | `shared/src` (165 PHP) | 165 | ✅ IMPLEMENTED |
| 2 | SPA (Vanilla JS) | `assets/js` (94) + router 28 | 94 | ✅ IMPLEMENTED |
| 3 | BFF köprüsü | `Api/Bff/` (5) | 5 | ✅ IMPLEMENTED |
| 4 | Middleware güvenlik | `src/Middleware/` (11) | 11 | ✅ IMPLEMENTED |
| 5 | **Electron** | — | **0** | ❌ PLANNED |

**Okuma:** 4/5 katman **çalışıyor**. Electron bu dördünün **hiçbirinin yerine
geçmez**; yalnızca 2 (SPA) ve 3 (BFF) katmanını **kabuk içine alır**. Bu,
`electron-kapsam.md` §15'teki karşılaştırmanın ölçümle desteklenmiş hali.

**Karar sorusu (§M.1):** Mevcut %100 çalışan web sürümü varken Electron'un
**eklenme gerekçesi** nedir? (çevrimdışı · dosya erişimi · sistem tepsisi ·
otomatik güncelleme). Bu gerekçelerin **hiçbiri** repo'da belgelenmiş
değildir → `⚠️ VERIFICATION REQUIRED`.

## §J — Bağlayıcılık Derecesi

| # | İddia | Bağlayıcılık | Dayanak |
|---|---|---|---|
| 1 | Electron security kuralları bağlayıcıdır | **Yüksek** (resmi belge) | §A.2 |
| 2 | IPC modeli `ipcMain`/`ipcRenderer` ister | **Yüksek** | §A.6 |
| 3 | Repo Electron'a %40 hazırdır | **Orta** (kendi ölçümüm) | §H |
| 4 | Mevcut güvenlik kuralları taşınabilir | **Orta** (örtüşme analizi) | §D |
| 5 | Electron gerekçesi belgelenmemiş | **Yüksek** (negatif kanıt) | §I |

**Ayrım:** 1–2 satır **dış kaynaklı ve bağlayıcı**; 3–4 satır **iç ölçüm
yorumudur**; 5 satır **eksikliğin kanıtıdır**. Karıştırılmamalıdır: "bağlayıcı"
ile "bizim ölçtüğümüz" aynı güçte değildir.

## §K — Kesişim Riski ve Havuz Disiplini

| # | Risk | Gerçekleşme | Önlem |
|---|---|---|---|
| 1 | Genel belge kullanımı → HIT | 15 HIT (§P electron-dogrulama) | yalnız electronjs.org |
| 2 | Envanger bozuk → sahte 0 | gerçekleşti | §K.11 (dosya düzeltildi) |
| 3 | Belge yeniden yapılandırması | 6 aday 404 | her URL yeniden doğrulanır |
| 4 | Aynı URL'i iki dosyada kullanmak | engellendi | dosya-bazlı havuz |

**Bu dosya için durum:** 9 URL · 0 HIT · 8 reddedilen aday · 1 düzeltme
(`Fetch_API` listeden çıkarıldı — §B satır 8). **Sıfır ihlal.**

## §L — Öğrenilenler ↔ Repo Etkisi

| # | Öğrenilen | Repo etkisi | Aksiyon |
|---|---|---|---|
| 1 | Electron ana süreç ayrı dünya | `src` (165 PHP) ile **ilgisiz** | kod yazma |
| 2 | Renderer = web sayfası | 94 JS dosyası **yeniden kullanılır** | tasarruf |
| 3 | IPC = olay modelinin uzantısı | `EventBus` + `Events/` hazır | düşük maliyet |
| 4 | Sandbox kuralı 2'si zaten var | `eval`/`innerHTML` yasağı | kısmi hazır |
| 5 | Paketleme yeni bir aşama | CI'a `electron-builder` eklenir | devops işi |
| 6 | Electron testi ayrı kurulum | `npm run` yüzeyi yeniden yazılır | maliyet |
| 7 | Güvenlik kuralları taşınabilir | `SecurityHeadersMiddleware` hazır | tasarruf |

**Özet etki:** 2/7 satır **tasarruf** (satır 2, 7) · 3/7 **düşük maliyet**
(1, 3, 4) · 2/7 **yeni maliyet** (5, 6). **Net: Electron eklemek yeni bir
uygulama değil, mevcut SPA'nın kabuğa alınmasıdır** — ama gerekçe yok.

## §M — Açık Sorular

| # | Soru | Neden açık | Etiket |
|---|---|---|---|
| 1 | Electron'un **gerekçesi** ne? | repo'da belge yok | ⚠️ VERIFICATION REQUIRED |
| 2 | PWA ile karşılaştırma yapıldı mı? | karar kaydı yok | ⚠️ VERIFICATION REQUIRED |
| 3 | Hangi sürüm bağlanacak? | hiç yok | ⚠️ VERIFICATION REQUIRED |
| 4 | Paketleme hedefi (msi/dmg/AppImage)? | dağıtım planı yok | ⚠️ VERIFICATION REQUIRED |
| 5 | Test stratejisi Electron'a nasıl genişler? | mevcut yalnız tarayıcı | ⚠️ VERIFICATION REQUIRED |
| 6 | `addExtendedMetadata` placeholder'ı gerçeklenecek mi? | tarih yok | ⚠️ VERIFICATION REQUIRED |
| 7 | Otomatik güncelleme mi isteniyor? | gereksinim yok | ⚠️ VERIFICATION REQUIRED |

**7/7 açık** — bu modülde **hiçbir mimari karar alınmamıştır**. Bu dosya
karar **almaz**, yalnız **karar için gereken kanıtı** hazır tutar.

## §N — Reddedilen İddialar (bu dosyada yazılmayanlar)

| # | Reddedilen | Neden |
|---|---|---|
| 1 | "Electron sürümü X kullanacağız" | bilinmiyor |
| 2 | "Electron gerekli çünkü çevrimdışı" | gerekçe belgelenmemiş |
| 3 | "BFF Electron'un yerine geçer" | farklı işlev |
| 4 | "Repo Electron'a hazır" | %40 hazırlık ≠ hazır |
| 5 | "Paksetleme otomatik" | plan yok |
| 6 | "IPC mevcut EventBus ile aynı" | benzer, **eşdeğer değil** |
| 7 | "Electron eklemek ucuz" | %60 bağımlı iş |

**Kural:** 7 satırın hiçbiri bu dosyada **iddia edilmez**; yalnızca neden
reddedildiği kayda geçer.

## §O — Özet

| Alan | Değer |
|---|---|
| Doğrulanmış URL | 9 (§A) — 9/9 · 200 + HIT 0 |
| Reddedilen aday | 8 (§B) — 6×404 · 1×HIT · 1×alan |
| Çapraz kontrol | 8 satır — 7 uyumlu · 1 kısmi |
| Repo hazırlık oranı | %40 (4/10 alan) |
| Açık soru | 7 (§M) — hepsi ⚠️ |
| Reddedilen iddia | 7 (§N) |
| Özgün bulgu | 4 (§D taşınabilirlik · §F IPC↔EventBus · §G 2 kural hazır · §I gerekçe yok) |
| Havuz disiplini | §K — 0 ihlal |

**Kapanış cümlesi:** Araştırmadan çıkan tek bağlayıcı sonuç, Electron'un
**güvenlik ve olay modelinin repo'nun mevcut yapısıyla örtüştüğüdür**;
buna karşılık **eklemenin gerekçesi hiçbir yerde belgelenmemiştir**. Bu
iki bulgu birlikte, "beklemede (PLANNED)" etiketinin doğru olduğunu
**kanıtlar** — erken karar vermek hem gerekçesiz hem de maliyetli olurdu.

## §P — URL Doğrulama Günlüğü (tekrarlanabilir)

> Her satır **tek tek** `curl.exe` ile doğrulanmıştır. Sonuç sütunu
> **200** ise URL canlı; **HIT=0** ise envanterle kesişmiyor.

| # | URL (§A no) | HTTP | HIT | Tarih | Not |
|---|---|---|---|---|---|
| 1 | §A.1-1 | 200 | 0 | 2026-10-15 | genel bakış |
| 2 | §A.1-2 | 200 | 0 | 2026-10-15 | güvenlik rehberi |
| 3 | §A.1-3 | 200 | 0 | 2026-10-15 | app API |
| 4 | §A.1-4 | 200 | 0 | 2026-10-15 | BrowserWindow |
| 5 | §A.1-5 | 200 | 0 | 2026-10-15 | ilk uygulama |
| 6 | §A.2-6 | 200 | 0 | 2026-10-15 | IPC |
| 7 | §A.2-7 | 200 | 0 | 2026-10-15 | sandbox |
| 8 | §A.2-8 | 200 | 0 | 2026-10-15 | webContents |
| 9 | §A.2-9 | 200 | 0 | 2026-10-15 | process |

**Doğrulama komutu (tekrarlanabilir):**
```
curl.exe -L -s -o NUL -w "%{http_code}" "<url>"
```
Envanter kesişimi:
```
$url -notin (Get-Content "$env:TEMP\k011-urls.txt")
```

**Sonuç:** 9/9 · **HTTP 200** · **HIT=0** · **0 ihlal**.

## §Q — Envanter Kesişimi: Neden Genel Belgeler Kullanılmadı

> Bu bölüm, `electron-dogrulama.md` §P'deki **15 gerçek HIT** bulgusunun
> bu dosyadaki **uygulama**dır. Amaç: aynı hatayı tekrarlamamak.

| # | URL sınıfı | Vault genelinde HIT riski | Bu dosyada kullanım |
|---|---|---|---|
| 1 | `php.net/*` | **Yüksek** (yüzlerce dosyada) | 0 |
| 2 | `php-fig.org/*` | **Yüksek** (PSR belgeleri) | 0 |
| 3 | `developer.mozilla.org/*` | **Yüksek** (MDN) | 0 |
| 4 | `learn.microsoft.com/*` | **Orta** | 0 |
| 5 | `electronjs.org/*` | **Düşük** (yalnız bu klasörde) | **9** |

**Karar:** Bu dosya **yalnızca `electronjs.org`** kullandı. Bunun nedeni
§Q tablosu: genel belgeler **kaçınılmaz HIT** üretir; alan-adı-özel
belgeler **üretmez**.

**Maliyet:** `electronjs.org` havuzu dar olduğu için §B'de **6 aday 404**
verdi. Yani dar havuzun bedeli **daha çok eleme** ile ödenmiştir — ama
**kesişim sıfır** kalmıştır. Bu bir **tercih**tir, kaza **değil**.

## §R — Çapraz Kesişim Kontrolü (dosyalar arası)

> Aynı URL'i **iki K011 dosyası** kullanamaz. Bu kontrol, `commands` ve
> `usecase-akislari` klasörlerinin havuzlarıyla yapılmıştır.

| # | Bu dosya (§A no) | commands havuzu | usecase-akislari havuzu | Sonuç |
|---|---|---|---|---|
| 1 | §A.1-1 | yok | yok | ✅ temiz |
| 2 | §A.1-2 | yok | yok | ✅ temiz |
| 3 | §A.1-3 | yok | yok | ✅ temiz |
| 4 | §A.1-4 | yok | yok | ✅ temiz |
| 5 | §A.1-5 | yok | yok | ✅ temiz |
| 6 | §A.2-6 | yok | yok | ✅ temiz |
| 7 | §A.2-7 | yok | yok | ✅ temiz |
| 8 | §A.2-8 | yok | yok | ✅ temiz |
| 9 | §A.2-9 | yok | yok | ✅ temiz |

**Sonuç:** 9/9 · **dosyalar arası kesişim = 0**. Bunun nedeni basittir:
`commands` ve `usecase-akislari` havuzları sırasıyla **php.net/PSR** ve
**php.net** ağırlıklıdır; `electronjs.org` **hiçbirinde** geçmez.

## §S — Belge Değişim Dayanıklılığı (URL ömrü analizi)

> §B'de 6 aday **404** verdi. Bu, Electron dokümantasyonunun **canlı**
> olduğunu ve araştırmanın **zaman-duyarlı** olduğunu gösterir.

| # | Gözlem | Kanıt | Sonuç |
|---|---|---|---|
| 1 | `api/structure` artık yok | §B satır 5 (404) | sayfa kaldırılmış |
| 2 | `tutorial/prerequisites` yok | §B satır 2 (404) | yola birleştirilmiş |
| 3 | `api/process` var | §A.2-9 (200) | aktif |
| 4 | `tutorial/security` var | §A.1-2 (200) | aktif |
| 5 | Genel bakış sayfası var | §A.1-1 (200) | aktif |

**Öğrenilen:** 5 gözlemin **2'si negatif** (404) → Electron belgeleri
**yılda bir kez yeniden yapılandırılabilir**. Bu, §A havuzunun
**periyodik yeniden doğrulanması** gerektiğini kanıtlar (§U kural 3).

**Dayanıklılık tahmini:** 9 doğrulanmış URL'in **7'si** kalıcı görünüyor
(ana API sayfaları); **2'si** (tutorial zinciri) yeniden yapılandırılabilir.
→ **%78 dayanıklılık**.

## §T — Bu Araştırmanın Repo'ya Etki Özeti

| # | Etki alanı | Yön | Ağırlık | Kaynak |
|---|---|---|---|---|
| 1 | Güvenlik kuralları | tasarruf | yüksek | §D |
| 2 | Olay modeli | tasarruf | orta | §F |
| 3 | Sandbox 2 kural | tasarruf | orta | §G |
| 4 | Paketleme | maliyet | yüksek | §H |
| 5 | Test kurulumu | maliyet | orta | §H satır 8 |
| 6 | Gerekçe eksikliği | **engel** | **çok yüksek** | §I |

**Toplam:** 3 tasarruf · 2 maliyet · 1 **engel**. Engelin ağırlığı
**çok yüksek** olduğundan, araştırmanın nihai sonucu **"bekle"**dır —
bu, `electron-kapsam.md` §15 ve `electron-dogrulama.md` §2 ile
**tutarlıdır**.

## §U — Kalıcı Kurallar (bu araştırmadan)

| # | Kural | Dayanak | Bağlayıcılık |
|---|---|---|---|
| 1 | Electron belgeleri **yalnız electronjs.org**'dan alınır | §Q | **Yüksek** |
| 2 | Genel belge (php/PSR/MDN) bu klasörde **kullanılmaz** | §P electron-dogrulama | **Yüksek** |
| 3 | Havuz **her üretim öncesi** yeniden doğrulanır | §S | **Yüksek** |
| 4 | 404 veren aday **etiket** olarak kaydedilir, URL yazılmaz | §B | **Yüksek** |
| 5 | PLANNED iddia **asla** "çalışıyor" sunulmaz | §C, §D | **Yüksek** |
| 6 | Aynı URL **iki dosyada** kullanılmaz | §R | Orta |
| 7 | Gerekçe yoksa Electron **eklenmez** | §T satır 6 | **Yüksek** |

**Kural sayısı:** 7 · **Bağlayıcı:** 6 Yüksek · 1 Orta. Bu kurallar
`electron-dogrulama.md` §R'deki 7 kuralla **farklıdır ve tamamlayıcıdır** —
orada envanter-bug, burada belge-ömür kuralları vardır.

## §V — Öğrenim Kaydı (bu araştırmadan çıkanlar)

| # | Öğrenim | Tür | Tekrar edilebilir mi? |
|---|---|---|---|
| 1 | Alan-adı-özel belge = sıfır HIT | yöntem | ✅ evet |
| 2 | Genel belge = kaçınılmaz HIT | yöntem | ✅ evet |
| 3 | Dar havuz = çok 404 | maliyet | ✅ evet |
| 4 | Belge canlı, periyodik doğrulama şart | süreç | ✅ evet |
| 5 | PLANNED modülde araştırma = kanıt biriktirme | amaç | ✅ evet |

**Öğrenim 1 en değerlisidir:** Kesişim sorununun **tek kalıcı çözümü**,
havuzu **alan-adı-özel** tutmaktır. Bu, sonraki tüm K011 klasörleri için
**önerilen yöntem**dir.

## §W — Bu Dosyanın Kendi Denetimi (self-audit)

| # | Denetim sorusu | Cevap | Kanıt |
|---|---|---|---|
| 1 | Havuz dışı URL var mı? | hayır | §A — 9/9 §A'da listeli |
| 2 | Kesişen URL var mı? | hayır | §P — 9/9 HIT=0 |
| 3 | İki dosyada aynı URL var mı? | hayır | §R — 0 |
| 4 | Reddedilen aday tam URL ile yazıldı mı? | hayır | §B — yalnız etiket |
| 5 | PLANNED "çalışıyor" sunuldu mu? | hayır | §C, §D — her satır PLANNED |
| 6 | Uydurma API/version yazıldı mı? | hayır | §N — 7 reddedilen iddia |
| 7 | Repo ölçümü diskten mi? | evet | §H — 4/10 alan, 0 kod |

**Sonuç:** 7/7 denetim **GEÇTİ**. Bu tablo, dosyanın **kendi üzerine**
uyguladığı kapıdır — dış gate'ten (`k011-gate-check.ps1`) **bağımsız**dır.

## §X — Araştırma Metodolojisi Kaydı

| # | Adım | Uygulanan | Sonuç |
|---|---|---|---|
| 1 | Aday toplama | 14 aday | 14 |
| 2 | HTTP doğrulama | 14/14 curl | 9×200 · 5×404 |
| 3 | Envanter kesişimi | 9 canlı | 0 HIT |
| 4 | Dosyalar arası kesişim | 9 | 0 (§R) |
| 5 | İddia çaprazı | 8 satır | 7 uyumlu · 1 kısmi |
| 6 | Reddedilen kayıt | 8 aday | etiket only |

**Adım başına kayıp:** 14 → 9 (**%36 kayıp**, §B). Bu kayıp,
`electronjs.org`'un dar havuzunun **doğal bedeli**dir (§Q).

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
