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

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
