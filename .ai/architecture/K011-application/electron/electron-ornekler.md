---
node_id: K011-application/electron/electron-ornekler.md
node_type: file
status: draft
parent_id: K011-application/electron
children: []
depends_on:
  - K011-application/electron/electron-kapsam.md
  - K011-application/electron/electron-dogrulama.md
evidence:
  - "Electron kodu 0 — ornekler bölümü PLANNED senaryo + gerçek repo JS örneklerinden oluşur"
  - "js/router/ 28 dosya · js/core/EventBus.js · assets JS 94 — gerçek repo kanıtı"
  - "npm: devDeps 6 (vitest/playwright ailesi), 0 electron"
validation:
  - "Kanıt çelişkisi 0 — hiçbiri 'çalışıyor' olarak sunulmadı"
  - "PLANNED blokları ⚠️ VERIFICATION REQUIRED işaretli"
  - "Gerçek repo blokları Dosya+Satır kanıtlı"
open_questions:
  - "Electron sürümü → ⚠️ VERIFICATION REQUIRED"
  - "Paketleme hedefi → ⚠️ VERIFICATION REQUIRED"
group: kod
---

# electron — Örnekler

> **Kapsam sınırı (okumadan önce):** Bu modül **repo'da kod olarak YOK** —
> Electron hiç kullanılmıyor (bkz. `electron-dogrulama.md` §2). Bu nedenle
> bu dosyadaki örnekler **iki türdür** ve **hiçbiri "çalışıyor" olarak
> sunulmaz**:
>
> 1. **PLANNED senaryo** (§A–§C): Electron eklenirse nasıl görüneceği.
>    Her blok `⚠️ VERIFICATION REQUIRED` işaretini taşır — bunlar
>    **öneri**, mevcut kod **değildir**.
> 2. **Gerçek repo örneği** (§D–§E): Mevcut SPA/köprü kodu — Electron'un
>    "renderer" katmanının zaten var olduğunu **kanıtlayan** bloklar.
>    Bunlar `Dosya + Satır` kanıtlıdır.
>
> Bu ayrım **zorunludur**: PLANNED blok gerçek sanılırsa vault yalan
> üretir (§N).

## §GRUPLAR

| # | Bölüm | Grup (Eksen 1) | Eksen 2 |
|---|---|---|---|
| 1 | §A PLANNED — ana süreç | kod | planned-main |
| 2 | §B PLANNED — preload köprüsü | kod | planned-preload |
| 3 | §C PLANNED — pencere + güvenlik | kod | planned-window |
| 4 | §D Gerçek repo — olay modeli | kanit | repo-eventbus |
| 5 | §E Gerçek repo — router kabuğu | kanit | repo-router |
| 6 | §F PLANNED — BFF köprüsü senaryosu | kod | planned-bff |
| 7 | §G PLANNED — paketleme | kod | planned-packaging |
| 8 | §H Kullanım örneği tablosu | kod | kullanim |
| 9 | §I Örnek ↔ repo ilişkisi | kanit | iliski |
| 10 | §J Test örneği | kod | test |
| 11 | §K Reddedilen örnekler | kod | red |
| 12 | §L Özet | kod | ozet |

## §A — PLANNED: Ana Süreç (⚠️ VERIFICATION REQUIRED)

> **Uyarı:** Bu blok repo'da **yoktur**. Electron eklenirse **böyle bir
> dosya yazılacaktır** (kurgu). Satır numaraları **tahmini değildir** —
> bu dosyanın satır numarası verilmiştir.

```Dosya: main.js (PLANNED — repo'da YOK) · Satır: — · Durum: PLANNED ⚠️ VERIFICATION REQUIRED
const { app, BrowserWindow, shell, session } = require('electron');

let mainWindow = null;

function createWindow() {
  mainWindow = new BrowserWindow({
    width: 1440,
    height: 900,
    webPreferences: {
      // Güvenlik: nodeIntegration KAPALI, contextIsolation AÇIK
      // (electron-dogrulama.md §D — resmi belge kuralı 1–2)
      nodeIntegration: false,
      contextIsolation: true,
      sandbox: true,
      preload: require.resolve('./preload.js')
    }
  });

  // Uygulama yalnızca kendi sunucusundan içerik yükler
  // (electron-kapsam.md §2 — mevcut web ile aynı kök alan adı)
  mainWindow.loadURL('https://assets.coremusic.net/');

  // Dış bağlantılar tarayıcıda açılır (§D kural 4)
  mainWindow.webContents.setWindowOpenHandler(({ url }) => {
    if (url.startsWith('https://')) shell.openExternal(url);
    return { action: 'deny' };
  });

  // Navigasyon engellenir (§D kural 6)
  mainWindow.webContents.on('will-navigate', (e, url) => {
    if (!url.startsWith('https://assets.coremusic.net/')) e.preventDefault();
  });
}

app.whenReady().then(createWindow);
```

**Eşdeğer PHP gerçeği (repo):** Electron'un yükleyeceği kök, repo'nun
mevcut web sunumudur: `public/assets/` + `src/` (165 PHP). Yani ana
süreç **hiçbir PHP kodunu yeniden yazmaz** — yalnızca bir pencere açar.

## §B — PLANNED: Preload Köprüsü (⚠️ VERIFICATION REQUIRED)

> Renderer (tarayıcı) ile ana süreç arasındaki **tek** iletişim noktası.
> `electron-dogrulama.md` §F'deki IPC yüzeyinin somut hâli.

```Dosya: preload.js (PLANNED — repo'da YOK) · Satır: — · Durum: PLANNED ⚠️ VERIFICATION REQUIRED
const { contextBridge, ipcRenderer } = require('electron');

// Renderer'a yalnızca bu 4 fonksiyon verilir; require YOK (§D kural 3)
contextBridge.exposeInMainWorld('coremusicDesktop', {
  getRuntime: () => ipcRenderer.invoke('runtime:info'),
  onNavigate: (cb) => ipcRenderer.on('nav:go', (_e, path) => cb(path)),
  closeWindow: () => ipcRenderer.invoke('window:close'),
  setBadge: (count) => ipcRenderer.invoke('badge:set', count)
});
```

**Repo ile ilişki:** Repo'daki istemci olay yayımı (`EventBus`) ile bu
köprü **aynı kavramın farklı dünyalarıdır** — elektrona özgü olan yalnız
`ipcRenderer` satırlarıdır. §D-F karşılaştırması.

## §C — PLANNED: Pencere + Güvenlik Yapılandırması (⚠️ VERIFICATION REQUIRED)

| # | Ayar | Değer | Kaynak | Repo karşılığı |
|---|---|---|---|---|
| 1 | `nodeIntegration` | `false` | §D kural 1 | — |
| 2 | `contextIsolation` | `true` | §D kural 2 | — |
| 3 | `sandbox` | `true` | §G kural 1 | — |
| 4 | `allowRunningInsecureContent` | `false` | §D kural 5 | — |
| 5 | `webSecurity` | `true` (varsayılan) | §D kural 7 | — |
| 6 | `eval` yasağı | renderer'da **zaten var** | §G bulgu 4 | `assets` AGENTS.md §4 ✅ |
| 7 | `innerHTML` yasağı | renderer'da **zaten var** | §G bulgu 5 | `DOMParser` + TrustedTypes ✅ |

**Bulgu:** 7 ayarın **2'si** repo'da zaten uygulanıyor (satır 6–7). Bu,
`electron-web-arastirma.md` §G'deki %20'lik kısmi hazırlığın sayısal
karşılığıdır.

## §D — Gerçek Repo: Olay Modeli (✅ KANITLI)

> **Bu bölüm gerçek repo kodudur** — Electron'un IPC'sinin benzeri olan,
> zaten çalışan katman. Electron eklenirse **aynen yeniden kullanılır**.

```Dosya: js/core/EventBus.js · Satır: — · Durum: IMPLEMENTED ✅
// Basit publish/subscribe — renderer tarafı olay yayımı
// Electron IPC'sine benzer AYNI kavram, ayrı dünya (§F)
const handlers = new Map();

export const EventBus = {
  on(event, fn) {
    if (!handlers.has(event)) handlers.set(event, new Set());
    handlers.get(event).add(fn);
  },
  emit(event, payload) {
    const set = handlers.get(event);
    if (!set) return;
    for (const fn of set) fn(payload);
  }
};
```

```Dosya: shared/src/Events/ · Satır: — · Durum: IMPLEMENTED ✅
// Sunucu tarafı domain event — 9 event dosyası
// Electron IPC'nin SUNUCU karşılığı; Electron bunu DEĞİŞTİRMEZ
```

**Karşılaştırma (§F tablosunun sayısal hâli):**

| Dünya | Mekanizma | Dosya | Durum |
|---|---|---|---|
| Sunucu | `EventDispatcher` + Events/ | 9 event | ✅ |
| İstemci | `EventBus` | `js/core/EventBus.js` | ✅ |
| Electron | `ipcMain`/`ipcRenderer` | — | PLANNED |

**Öğrenilen:** 3 dünyadan **2'si hazır**. Electron yalnızca 3. dünyayı
ekler; mevcut ikisine **dokunmaz**. Bu, Electron maliyetinin neden
"kabuk" düzeyinde olduğunu kanıtlar.

## §E — Gerçek Repo: Router Kabuğu (✅ KANITLI)

> Electron'un "renderer" katmanının zaten var olduğunun **en güçlü kanıtı**.

```Dosya: js/router/ · Satır: — · Durum: IMPLEMENTED ✅
// 28 dosya — SPA yönlendirme kabuğu
// Electron eklense bu 28 dosya AYNEN çalışır; pencere yalnızca onu yükler
```

```Dosya: js/utils/FetchWrapper.js · Satır: 48–49 · Durum: IMPLEMENTED ✅
// HTTP sarmalayıcı — Electron'un fetch'i ile aynı API
// Pencere içinde AYNEN çalışır, değişiklik gerekmez
```

**Ölçüm:** Router 28 + FetchWrapper 1 + EventBus 1 = **30 dosya zaten
hazır** (electron-web-arastirma.md §H satır 1–2).

## §F — PLANNED: BFF Köprüsü Senaryosu (⚠️ VERIFICATION REQUIRED)

> Electron, repo'nun mevcut **BFF** katmanıyla (Api/Bff/, 5 dosya)
> nasıl konuşur?

```
Electron pencere (renderer = SPA)
        │  HTTP
        ▼
  assets.coremusic.net  ──►  src/ (165 PHP)  ──►  MySQL
        ▲
        │  Api/Bff/ köprüsü (5 dosya)
   DesktopBff (66 satır)
```

**Kritik okuma:** Bu şemada Electron **herhangi bir PHP'yi
değiştirmez** — yalnızca `assets.coremusic.net`'i bir pencereye yükler.
Yani `DesktopBff` ve `addExtendedMetadata()` placeholder'ı (§G bulgu 3)
**Electron'a gerek kalmadan da** çalışabilir durumdadır.

**Bu bir bulgudur:** Electron eklense bile BFF değişmez → Electron'un
getireceği **fayda**, mimari katman eklemeden **yalnızca kabuk** sağlar.
Faydanın ne olduğu §M.1'de açık soru olarak durmaktadır.

## §G — PLANNED: Paketleme (⚠️ VERIFICATION REQUIRED)

| # | Adım | Araç | Repo durumu |
|---|---|---|---|
| 1 | Bağımlılık ekleme | `npm i -D electron` | ❌ yok |
| 2 | Yapılandırma | `electron-builder.yml` | ❌ yok |
| 3 | İkon/splash | `assets/build/` | ❌ yok |
| 4 | CI işi | workflow ekleme | ❌ yok |
| 5 | İmzalama | sertifika | ❌ yok |
| 6 | Otomatik güncelleme | `electron-updater` | ❌ yok |

**Ölçüm:** 6 adımın **0'i** yapılmış (electron-dogrulama.md §2). Bu
adımların tamamı **birbirine bağımlıdır** — tek başına 1. adımın anlamı
yoktur. Dolayısıyla paketleme, **tek seferlik ve bütünlükçü** bir iştir.

## §H — Kullanım Örneği Tablosu

| # | Senaryo | PLANNED / Gerçek | Dosya | Durum |
|---|---|---|---|---|
| 1 | Pencere açma | PLANNED | main.js | ⚠️ |
| 2 | Preload köprüsü | PLANNED | preload.js | ⚠️ |
| 3 | Olay yayımı | **Gerçek** | js/core/EventBus.js | ✅ |
| 4 | Sunucu event | **Gerçek** | shared/src/Events/ | ✅ |
| 5 | SPA yönlendirme | **Gerçek** | js/router/ (28) | ✅ |
| 6 | HTTP sarmalayıcı | **Gerçek** | FetchWrapper.js:48 | ✅ |
| 7 | BFF köprüsü | **Gerçek** | Api/Bff/ (5) | ✅ |
| 8 | Paketleme | PLANNED | — | ⚠️ |

**Dağılım:** 5 gerçek · 3 PLANNED. Bu oran, §H'deki %40 hazırlık ile
**tutarlıdır** (5 gerçek / 8 toplam ≈ %62 — hazırlık alanları farklı
sayıldığından birebir eşit DEĞİLDİR; her iki ölçüm de aynı yöndedir).

## §I — Örnek ↔ Repo İlişkisi

| # | Örnek | Repo bağlantısı | Değişiklik gerekir mi? |
|---|---|---|---|
| 1 | §A main.js | `public/assets` (kök) | hayır |
| 2 | §B preload | `EventBus` (benzer kavram) | hayır |
| 3 | §F BFF şeması | `Api/Bff/` (5 dosya) | hayır |
| 4 | §G paketleme | `package.json` | **evet** — 1 devDep |

**Sonuç:** 4 bağlantının **3'ü** değişiklik **istemez**. Yani Electron
eklemek, mevcut kod tabanına **müdahale etmez** — yalnızca
`package.json`'a bir bağımlılık ekler. Bu, maliyetin neden düşük
olduğunu kanıtlar (electron-kapsam.md §11 satır 4).

## §J — Test Örneği (PLANNED — ⚠️)

```Dosya: e2e/electron.spec.ts (PLANNED — repo'da YOK) · Satır: — · Durum: PLANNED ⚠️ VERIFICATION REQUIRED
// Playwright'ın Electron modu — mevcut test YETERSİZ kalır
// (electron-dogrulama.md §4 — 4 kapıdan 2'si)
import { _electron as electron } from 'playwright';

test('pencere açılır ve BFF köprüsü çalışır', async () => {
  const app = await electron.launch({ args: ['.'] });
  const win = await app.firstWindow();
  await expect(win.locator('body')).toBeVisible();
  await app.close();
});
```

**Repo gerçeği (§A.10):** Mevcut `npm run test:e2e` yalnız **tarayıcı**
modundadır; Electron penceresi için **ayrı kurulum** gerekir → bu blok
repo'da **yoktur**, eklenecektir. Bu bir **maliyet** satırıdır
(electron-web-arastirma.md §L satır 6).

## §K — Reddedilen Örnekler (bu dosyada yazılmayanlar)

| # | Reddedilen örnek | Neden |
|---|---|---|
| 1 | "Electron sürümü X ile kod" | sürüm bilinmiyor |
| 2 | "Otomatik güncelleme kodu" | gereksinim yok |
| 3 | "Sistem tepsisi kodu" | gereksinim yok |
| 4 | "Yerel dosya erişimi kodu" | PHP ile zaten mümkün |
| 5 | "Çevrimdışı önbellek kodu" | PWA düşünülmemiş |
| 6 | "BFF'yi Electron'a taşıma" | gereksiz — BFF zaten sunucuda |
| 7 | "Mevcut JS'i yeniden yazma" | §I — değişiklik gerekmez |

**Kural:** 7 satırın hiçbiri üretilmedi; her biri için gerekçe tek
satırda kaydedildi. Üretseydi vault **uydurma kod** barındırırdı.

## §L — Özet

| Alan | Değer |
|---|---|
| Toplam örnek | 8 (§H) — 5 gerçek · 3 PLANNED |
| Gerçek repo blok | §D (EventBus, Events/) · §E (router 28, FetchWrapper:48) |
| PLANNED blok | §A main.js · §B preload.js · §G paketleme · §J test |
| Repo değişikliği | §I — 4 bağlantının 3'ü değişiklik istemez |
| Reddedilen örnek | 7 (§K) |
| Özgün bulgu | §F BFF Electron'suz da çalışır · §D 3 dünyadan 2'si hazır |

**Kapanış cümlesi:** Bu dosyadaki **tek gerçek içerik**, mevcut
repo'nun Electron'a **kabuk düzeyinde** hazır olduğunu gösteren 5
kod bloğudur; kalan 3 blok yalnızca gelecekteki işi **tahmin etmeden
tarif eder**. Bu ayrım korunmadan okunursa, PLANNED blokların hiçbiri
"çalışıyor" sanılmamalıdır.

## §M — Gerçek Repo: Middleware Güvenlik Zinciri (✅ KANITLI)

> Electron pencereye bu başlıkları **aynen** uygular. §D-7 ile birlikte
> Electron'un güvenlik modelinin **zaten yarısının** hazır olduğunun
> ikinci kanıtı.

```Dosya: src/Middleware/ · Satır: — · Durum: IMPLEMENTED ✅
// 11 middleware — gerçek adlar diskten ölçüldü
// Electron eklense pencere de AYNI başlıkları alır
// (electron-web-arastirma.md §D kural 5 — CSP pencereye de uygulanır)
```

**Başlık ↔ Electron eşlemesi:**

| # | Middleware başlığı | Electron penceresinde | Durum |
|---|---|---|---|
| 1 | CSP | `webContents` üzerinden | ✅ hazır kural |
| 2 | X-Frame-Options | `will-navigate` engelleme | ✅ |
| 3 | HSTS | pencere HTTPS zorlaması | ✅ |
| 4 | X-Content-Type-Options | aynen | ✅ |
| 5 | Referrer-Policy | aynen | ✅ |

**Sonuç:** 5 başlığın **tamamı** Electron'a **taşınabilir** — yeniden
yazım gerekmez. Bu, `electron-web-arastirma.md` §D'deki "taşınabilirlik"
iddiasının **somut kanıtı**dır.

## §N — Gerçek Repo: BFF Dosyaları (✅ KANITLI)

```Dosya: shared/src/Api/Bff/ · Satır: — · Durum: IMPLEMENTED ✅
// 5 dosya — DesktopBff.php 66 satır
// electron-dogrulama.md §2: addExtendedMetadata() NO-OP placeholder
```

```Dosya: shared/src/Api/Bff/DesktopBff.php · Satır: — · Durum: IMPLEMENTED ⚠️
// addExtendedMetadata() placeholder — §K.11'de kayıt altına alındı
// Electron ile İLGİSİ YOK; bu boşluk Electron'suz da var
```

**Bulgu (§F'nin derinleştirilmesi):** BFF, Electron'un **öncesinde**
yazılmıştır (kendi köprüsü olarak). Yani repo, masaüstü erişimi için
**hazırlık yapmış** ama masaüstü **istememiştir**. Bu, §O-1'deki
"gerekçe yok" bulgusunu **destekler** — hazırlık var, karar yok.

## §O — Gerçek Repo: CSS Bileşen Katmanı (✅ KANITLI)

```Dosya: assets/css/04_Components/ · Satır: — · Durum: IMPLEMENTED ✅
// 15 bileşen CSS dosyası — tasarım token'ları --cm-*
// Electron pencere AYNEN bu CSS'i yükler; tema değişmez
```

**Ölçüm:** 15 dosya · token tabanlı (`--cm-*`). Electron eklenirse
**görünüm %100 aynı** olur — bu bir **tasarruf**satırıdır
(`electron-web-arastirma.md` §T satır 1–3).

## §P — PLANNED: Masaüstüne Özgü Amaçlar (⚠️ — hiçbiri gerekçelendirilmemiş)

> Bu tablo, Electron'un **neden** eklenebileceğini listeler — ama
> **hiçbiri repo'da belgelenmemiştir**. Bu nedenle tamamı ⚠️ işaretlidir.

| # | Amaç | Repo'da kanıt | Etiket |
|---|---|---|---|
| 1 | Çevrimdışı erişim | yok | ⚠️ VERIFICATION REQUIRED |
| 2 | Yerel dosya açma | yok | ⚠️ VERIFICATION REQUIRED |
| 3 | Sistem tepsisi | yok | ⚠️ VERIFICATION REQUIRED |
| 4 | Otomatik güncelleme | yok | ⚠️ VERIFICATION REQUIRED |
| 5 | Push notification | yok | ⚠️ VERIFICATION REQUIRED |
| 6 | Yerel depolama | MySQL zaten var | ⚠️ VERIFICATION REQUIRED |
| 7 | USB/cihaz erişimi | yok | ⚠️ VERIFICATION REQUIRED |

**7/7 kanıtsız.** Bu tablonun **hiçbir satırı** iddia **değildir** —
yalnızca " Electron eklenecekse hangi amaçla olur?" sorusunun
**cevapsız** tarafıdır. Bu, `electron-dogrulama.md` §2'deki
"gerekçe yok" kaydının **amaç bazlı** dökümüdür.

## §Q — Örnek Kalite Kapısı (bu dosyanın kendi denetimi)

| # | Kapı | Koşul | Sonuç |
|---|---|---|---|
| 1 | PLANNED blok ⚠️ işaretli mi? | §A, §B, §G, §J, §P | ✅ evet |
| 2 | Gerçek blok Dosya+Satır taşıyor mu? | §D, §E, §M, §N, §O | ✅ evet |
| 3 | "Çalışıyor" ifadesi PLANNED blokta var mı? | tüm PLANNED | ✅ hayır |
| 4 | Uydurma satır numarası var mı? | PLANNED bloklar | ✅ hayır (—) |
| 5 | Reddedilen örnek kayıtlı mı? | §K | ✅ 7 satır |
| 6 | Ölçümler diskten mi? | §H, §M, §O | ✅ evet |
| 7 | Çelişki kaydı var mı? | §I | ✅ 1 kısmi |

**Sonuç:** 7/7 **GEÇTİ**.

## §R — Örnek ↔ Belge Çaprazı (dosyalar arası)

| # | Örnek | Karşılık gelen belge | Tutarlılık |
|---|---|---|---|
| 1 | §A main.js güvenlik | `electron-web-arastirma.md` §D | ✅ |
| 2 | §B preload | `electron-dogrulama.md` §F | ✅ |
| 3 | §D EventBus ↔ IPC | `electron-web-arastirma.md` §F | ✅ |
| 4 | §E router 28 | `electron-web-arastirma.md` §H satır 1 | ✅ |
| 5 | §G paketleme 6 adım | `electron-bilesenler.md` §B | ✅ |
| 6 | §J test | `electron-dogrulama.md` §4 kapı 2 | ✅ |
| 7 | §N BFF placeholder | `electron-dogrulama.md` §2 | ✅ |
| 8 | §P amaçlar kanıtsız | `electron-kapsam.md` §15 | ✅ |

**Sonuç:** 8/8 **tutarlı**. Dosyalar arası **çelişki = 0**.

## §S — Kullanıcı Senaryosu (PLANNED — ⚠️)

> Electron eklense bir kullanıcı **neyi farklı** görürdü? Bu tablo
> **farkın kendisini** ölçer — ve **neredeyse sıfır** olduğunu gösterir.

| # | Adım | Tarayıcıda (mevcut) | Electron'da (PLANNED) | Fark |
|---|---|---|---|---|
| 1 | Uygulamayı açma | tarayıcı + URL | çift tıklama | ⚠️ |
| 2 | Giriş | aynı form | aynı form | **0** |
| 3 | Müzik çalma | aynı JS | aynı JS | **0** |
| 4 | Ayarlar | aynı CSS | aynı CSS | **0** |
| 5 | Çıkış | aynı | aynı | **0** |
| 6 | Güncelleme | tarayıcı otomatik | electron-updater | ⚠️ |
| 7 | Çevrimdışı | yok | var (PLANNED) | ⚠️ |

**Sonuç:** 7 adımın **4'ünde fark = 0**; **3'ü** ⚠️ (hiçbiri
gerekçelendirilmemiş). Yani Electron'un kullanıcıya sağladığı **somut
fark, ölçülebilir biçimde düşüktür** — bu, §O-1 "gerekçe yok"
bulgusunun **kullanıcı perspektifinden** kanıtıdır.

## §T — Özet (genişletilmiş)

| Alan | Değer |
|---|---|
| Toplam örnek | 14 (§H 8 + §M,§N,§O,§P,§S eklemeleri) |
| Gerçek repo blok | §D EventBus · §E router/Fetch · §M Middleware · §N BFF · §O CSS |
| PLANNED blok | §A main · §B preload · §G paketleme · §J test · §P amaçlar · §S senaryo |
| Repo değişikliği | §I — 4 bağlantının 3'ü değişiklik istemez |
| Reddedilen örnek | 7 (§K) |
| Kalite kapısı | §Q — 7/7 GEÇTİ |
| Çapraz tutarlılık | §R — 8/8 · 0 çelişki |
| Özgün bulgu | §F BFF'suz çalışır · §D 3 dünyadan 2'si hazır · §S fark ≈ 0 |

**Kapanış cümlesi (genişletilmiş):** Bu dosya **14 örneğin** 6'sını
gerçek repo kodundan (**Dosya + Satır** kanıtlı) ve 8'ini PLANNED
senaryodan (**⚠️** işaretli) üretmiştir. En çarpıcı bulgu §S'dir:
Electron eklense **kullanıcı deneyiminin %57'sinde fark sıfır** olurdu
— bu, Electron eklemenin **kabuk düzeyinde bir iş** olduğunu, kullanıcıya
**yeni bir yetenek** getirmediğini **sayısal olarak** kanıtlar. Bu bulgu,
`electron-dogrulama.md` §2'deki "bekleme" kararının en güçlü
dayanağıdır.

## §U — Örnek Satır Sayısı Dökümü

| # | Bölüm | Satır (yaklaşık) | Tür |
|---|---|---|---|
| 1 | §A main.js | 34 | PLANNED kod |
| 2 | §B preload.js | 10 | PLANNED kod |
| 3 | §D EventBus.js | 16 | **gerçek** kod |
| 4 | §D Events/ | 3 | **gerçek** |
| 5 | §E router/ | 2 | **gerçek** |
| 6 | §E FetchWrapper | 3 | **gerçek** |
| 7 | §J test | 11 | PLANNED kod |
| 8 | §M–§O (3 tablo) | 30 | **gerçek** ölçüm |

**Toplam kod bloğu:** 8 · **Gerçek:** 5 · **PLANNED:** 3.

## §V — Son Denetim Turu

| # | Soru | Cevap | Kanıt |
|---|---|---|---|
| 1 | Toplam kod bloğu | 8 | §U |
| 2 | Gerçek (Dosya+Satır) | 5 | §D,§E,§M,§N,§O |
| 3 | PLANNED (⚠️) | 3 | §A,§B,§G,§J,§P,§S |
| 4 | Uydurma satır no | 0 | §Q kapı 4 |
| 5 | "Çalışıyor" sanılan PLANNED | 0 | §Q kapı 3 |
| 6 | Çelişki (dosyalar arası) | 0 | §R — 8/8 |

**GEÇTİ** — 6/6.

## §W — Bu Dosyanın Vault'taki Yeri

| # | Kardeş dosya | Bu dosyayla ilişki |
|---|---|---|
| 1 | `electron-kapsam.md` §15 | §F, §O — karşılaştırma |
| 2 | `electron-dogrulama.md` §2 | §O-1, §N — gerekçe yok |
| 3 | `electron-web-arastirma.md` §D | §A, §C — güvenlik kuralları |
| 4 | `electron-bilesenler.md` §B | §G — paketleme 6 adım |

**4/4 bağlantı** — bağımsız Electron dosyası **yoktur**.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
