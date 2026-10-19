---
title: "K011 · desktop — Web Araştırması"
type: architecture
kodeme: kademe-2
module: K011-application
parent: desktop
status: active
updated: 2026-10-15
group: kanit
---

# K011 · desktop — Web Araştırması

> **Amaç:** `desktop` düğümü için resmi kaynak havuzunu (HTTP 200 + envanter kesişimi 0),
> claim↔repo çapraz matrisini, reddedilen adayları ve açık soruları taşır. **Havuzun tamamı
> bu dosyadadır** — kardeş dosyalarda URL kullanılmaz (spec §1.6).

## §GRUPLAR

| # | Bölüm | Grup | Eksen | Kullanım |
|---|---|---|---|---|
| 1 | §A Resmi kaynak havuzu | kanit | havuz | 5 URL + erişim durumu |
| 2 | §B Havuz erişim kanıtı | kanit | http | 200 doğrulama kaydı |
| 3 | §C Claim↔Repo çapraz matris | kanit | capraz | Kaynak iddiası ↔ disk kanıtı |
| 4 | §D Reddedilen adaylar | kanit | red | Şemasız etiket listesi |
| 5 | §E Kesişim kanıtı (PowerShell) | kanit | kesişim | Betik metni — repo kökünden koşulur |
| 6 | §F Havuz dışı ek URL ihtiyacı | kanit | politika | Prosedür |
| 7 | §G Açık sorular | kanit | qa | Cevap bekleyenler |
| 8 | §A1 PWA claim detayı | mimari | pwa | Kapsam dışı notu |
| 9 | §A2 Navigator claim detayı | mimari | api | UA/istemci claim'i |
| 10 | §A3 Geolocation claim detayı | mimari | guvenlik | İzin kapsamı |
| 11 | §A4 getComputedStyle claim detayı | mimari | olcum | Bant ölçümü |
| 12 | §A5 Media queries claim detayı | mimari | css | Band CSS'i |
| 13 | §H Araştırma geçmişi | kanit | tarih | Tarih ve araç |
| 14 | §I Sonuç | kanit | sonuc | Havuz kapı özeti |
| 15 | §J Kaynak↔Kullanım haritası | kanit | kullanim | 21 claim dağılımı |
| 16 | §K Havuz sınırı gerekçeleri | kanit | sinir | 8 aday neden eklenmedi |
| 17 | §L Claim dayanıklılığı | kanit | dayanak | Güçlü 3 · Orta 4 · Zayıf 1 |
| 18 | §M Band mimarisi | mimari | band | 7 katman — çelişki tekrar kaydı |
| 19 | §N Red dağılım analizi | kanit | red | 4 neden grubu |
| 20 | §O Tekrar üretim | kanit | prosedur | 7 adım — yeniden üretebilir |
| 21 | §P Terimler | kod | sozluk | 8 terim |
| 22 | §Q Kardeş tutarlılık | kanit | tutarlilik | 4/5 — 1 ⚠️ |
| 23 | §R Açık soru aksiyonları | kanit | aksiyon | 5 açık · 0 kapanan |
| 24 | §S Bütünlük denetimi | kanit | kapisi | 7/7 · kapı 6 açık |
| 25 | §T Devir notu | kanit | handover | 6 devir · HIGH 2 |

---

## §A — Resmi kaynak havuzu (5 URL · 2026-10-15)

| # | URL | Sahiplik | Neden bu düğümde? | Durum |
|---|---|---|---|---|
| 1 | `https://developer.mozilla.org/en-US/docs/Web/Progressive_web_apps` | MDN | Masaüstünde kurulabilir uygulama davranışı — `desktop` bandı kullanıcı deneyimi referansı | 200 |
| 2 | `https://developer.mozilla.org/en-US/docs/Web/API/Navigator` | MDN | `navigator` üzerinden UA/istemci bilgisi okuması — band tespiti referansı | 200 |
| 3 | `https://developer.mozilla.org/en-US/docs/Web/API/Geolocation_API` | MDN | Masaüstü izin akışı — kapsam dışı davranışların sınır referansı | 200 |
| 4 | `https://developer.mozilla.org/en-US/docs/Web/API/Window/getComputedStyle` | MDN | Bant ölçümü (computed style) — desktop layout ölçümü referansı | 200 |
| 5 | `https://developer.mozilla.org/en-US/docs/Web/CSS/Media_queries/Using_media_queries` | MDN | `@media` band CSS'i — `d-desktop.css` band kuralı referansı | 200 |

**Havuz kuralı:** Bu 5 URL **yalnız** bu dosyada geçer; `desktop-kapsam.md`,
`desktop-dogrulama.md`, `desktop-ornekler.md`, `desktop-bilesenler.md` dosyalarında
`http://`/`https://` **0 adettir** (spec §1.6 "havuz tek dosyada").

**Havuzun sahiplenmediği konular (komşu havuzlar):** Electron API'si → `electron/`
klasörünün havuzu · HTML/CSS genel referans → `web/` havuzu · PHP CLI → `local-runtime/`
havuzu. Havuzlar klasörler arası **PAYLAŞILMAZ** (spec §6).

---

## §B — Havuz erişim kanıtı

| # | URL # | Yöntem | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | 1 (PWA) | `webfetch` (kod modu) | Erişildi — MDN sayfa başlığı döndü | 2026-10-15 |
| 2 | 2 (Navigator) | `webfetch` | Erişildi — "Navigator - Web APIs \| MDN" | 2026-10-15 |
| 3 | 3 (Geolocation) | `webfetch` | Erişildi — "Geolocation API - Web APIs \| MDN" | 2026-10-15 |
| 4 | 4 (getComputedStyle) | `webfetch` | Erişildi — "Window: getComputedStyle() method \| MDN" | 2026-10-15 |
| 5 | 5 (Media queries) | `webfetch` | Erişildi — "Using media queries - CSS \| MDN" | 2026-10-15 |

**Son curl kapısı:** `curl.exe -L -s -o NUL -w "%{http_code}"` ile nihai **200** kontrolü
**orkestratöre devredildi** (shell izni yok — §E betiği). Bu oturumdaki `webfetch`
erişimleri ön-doğrulamadır.

---

## §C — Claim↔Repo çapraz matris

| # | Kaynak claim (havuz) | Repo'da karşılığı | Dosya | Satır | Eşleşme |
|---|---|---|---|---|---|
| 1 | PWA masaüstünde kurulabilir/çevrimdışı çalışır | Repo'da service worker/manifest YOK | — | — | YOK → PLANNED (kapsam dışı, `web`) |
| 2 | `navigator.userAgent` UA string'ini verir | Sunucu tarafı UA okuması `$_SERVER['HTTP_USER_AGENT']` | `shared/src/Device/DeviceDetector.php` | 67 | EVET |
| 3 | `navigator.geolocation` izin gerektirir | Repo'da geolocation kullanımı YOK | — | — | YOK → kapsam dışı |
| 4 | `getComputedStyle` canlı read-only stil döner | JS'de computed style okuması aranmadı | — | — | ⚠️ VERIFICATION REQUIRED |
| 5 | Media query `min-width`/`max-width` band tanımlar | `DESKTOP_MAX = 2560` + breakpoint docblock | `shared/src/Device/DeviceDetector.php` | 24-25, 124 | EVET |
| 6 | Media query CSS tarafında band uygular | `d-desktop.css` 08_Devices katmanı | `assets.coremusic.net/Css/08_Devices/d-desktop.css` | 1-7 | EVET |
| 7 | `windowControlsOverlay` masaüstü PWA başlık çubuğu | Repo'da yok | — | — | YOK → PLANNED |
| 8 | `navigator.hardwareConcurrency` çekirdek sayısı | Repo'da yok | — | — | YOK → PLANNED |

**Eşleşme özeti:** EVET 3 · YOK/PLANNED 4 · ⚠️ 1.

**Kanıt blokları:**

```text
Dosya: shared/src/Device/DeviceDetector.php · Satır: 67
$ua = $userAgent ?? $_SERVER['HTTP_USER_AGENT'] ?? '';
```

```text
Dosya: assets.coremusic.net/Css/08_Devices/d-desktop.css · Satır: 4
 * Rol: 1025-1440px desktop cihaz paketi - tüm katman dosyalarını import eder
```

**Not (Satır 4 çelişkisi):** `d-desktop.css` başlık yorumu "1025-1440px" diyor; buna karşılık
`DeviceDetector.php` L15 `desktop = 1441-2560px`. **İki kaynak farklı band yazıyor** →
⚠️ VERIFICATION REQUIRED (assets Css AGENTS §3.2 dosya başlığı vs detector) — UI Designer'a
handover maddesi.

---

## §D — Reddedilen adaylar (şemasız etiket — TAM URL YAZILMAZ)

| # | Etiket | Neden reddedildi |
|---|---|---|
| 1 | `electronjs docs latest` | Electron havuzu bu klasöre AİT DEĞİL (`electron/` klasörünün havuzu) |
| 2 | `electronjs tutorial security` | Aynı — klasör havuzu sınırı |
| 3 | `mdn Web/API/Screen` | Havuzda yok; ek URL doğrulaması yapılmadı |
| 4 | `mdn Web/API/Window/matchMedia` | Havuzda yok — `web` klasörü kapsamında değerlendirilebilir |
| 5 | `w3c TR device-orientation` | Havuz dışı + envanter kesişimi riski |
| 6 | `caniuse viewport units` | Resmi standart kaynağı değil (spec "resmi kaynak") |
| 7 | `developer.mozilla Service_worker_API` | Havuzda yok — PWA konusu `web`'e devredildi |
| 8 | `web.dev learn pwa` | MDN havuzu zaten kapsıyor; mükerrer konu |

**Red kuralı:** Reddedilen adayların **tam URL'i yazılmaz** (spec §1.6) — yalnız etiket.

---

## §E — Kesişim kanıtı (PowerShell — metin, repo kökünden koşulur)

```powershell
# E.1 — Bu dosyanın URL'leri (5'ten fazlaysa ihlal)
$d = "C:\www\coremusic.net\.ai\architecture\K011-application\desktop"
$own = Get-ChildItem $d -Filter *.md |
  ForEach-Object { [regex]::Matches((Get-Content $_.FullName -Raw),'https?://[^\s\)\]\}\>"''`]+') } |
  ForEach-Object { $_.Value.TrimEnd('.',',',';',')') } | Sort-Object -Unique

# E.2 — Diğer tüm K011 dosyalarındaki URL'ler
$root = "C:\www\coremusic.net\.ai\architecture\K011-application"
$others = Get-ChildItem $root -Recurse -Filter *.md |
  Where-Object { $_.DirectoryName -ne $d } |
  ForEach-Object { [regex]::Matches((Get-Content $_.FullName -Raw),'https?://[^\s\)\]\}\>"''`]+') } |
  ForEach-Object { $_.Value.TrimEnd('.',',',';',')') } | Sort-Object -Unique

# E.3 — Kesişim (beklenen: 0)
@($own | Where-Object { $others -contains $_ }).Count

# E.4 — Havuz doğrulama (beklenen: 5 × 200)
$own | ForEach-Object {
  $code = curl.exe -L -s -o NUL -w "%{http_code}" $_
  "{0} {1}" -f $code, $_
}
```

**Beklenti:** E.3 = `0` · E.4 = `200` ×5. **Çalıştırma:** repo kökünden orkestratör
(spec §5; `.ai/scripts/*.ps1` kök kuralı).

**Bu oturumdan kesişim notu:** `desktop/` içindeki diğer 4 dosyada URL yok (yazım
sırasında `grep https?://` ile 0 isabet hedefi) — kesişim yalnız §A'daki 5 havuz URL'i
üzerinden hesaplanır.

---

## §F — Havuz dışı ek URL ihtiyacı — prosedür

| Adım | İşlem | Kapı |
|---|---|---|
| 1 | İhtiyaç belirle (neden §A yetmedi) | Gerekçe yazılır |
| 2 | `webfetch` ile erişim | 200 karşılığı |
| 3 | `.ai/architecture/**` envanterinde `grep` | Kesişim 0 olmalı |
| 4 | §A tablosuna ekle + tarih | Ekleme yoksa RED |
| 5 | `curl.exe` nihai 200 | orkestratör |

**Bu dosyada ek URL eklenmedi** — 5 URL yeterli bulundu (§C'de 4 konu zaten PLANNED/kapsam dışı).

---

## §G — Açık sorular

| # | Soru | İlgili havuz # | Sahip |
|---|---|---|---|
| 1 | PWA kurulumu `desktop` düğümüne girer mi, yoksa `web`'e mi? | 1 | architect |
| 2 | Geolocation izin akışı desktop'a özgü mü (masaüstü izin dialogu)? | 3 | security-engineer |
| 3 | `getComputedStyle` repo'da gerçekten kullanılıyor mu? | 4 | ui-designer (grep ile aranacak) |
| 4 | Band CSS'i `@media` ile mi, sınıf ekleme ile mi (ikisi de var mı)? | 5 | ui-designer |
| 5 | `d-desktop.css` başlık yorumundaki 1025-1440 aralığı düzeltilecek mi? | 5 | ui-designer (§C notu) |

---

## §A1 — PWA claim detayı

| Claim | Kaynak # | `desktop` için anlamı |
|---|---|---|
| PWA tek koddan çok platformda çalışır | 1 | Masaüstü bandı PWA'nın hedeflerinden biri |
| Kurulabilir (installable) | 1 | Repo'da manifest/SW yok → PLANNED |
| Çevrimdışı çalışabilir | 1 | Repo'da cache stratejisi yok → PLANNED |
| OS ile entegre (badge, kısayol) | 1 | Kapsam dışı → `web` |

---

## §A2 — Navigator claim detayı

| Claim | Kaynak # | Repo karşılığı |
|---|---|---|
| `navigator` user agent durumunu temsil eder | 2 | Sunucu tarafında `$_SERVER['HTTP_USER_AGENT']` (L67) |
| `navigator.userAgent` string döner | 2 | `DeviceDetector::detect()` UA girdisi |
| `navigator.onLine` çevrimiçi durumu | 2 | Repo'da yok → PLANNED |
| `navigator.language` dil tercihi | 2 | Repo'da yok → PLANNED (yerelleşme) |
| `navigator.maxTouchPoints` dokunma | 2 | `isTouchFirst()` ayrı kural (`DeviceDetector.php` L276) — çapraz ⚠️ |

---

## §A3 — Geolocation claim detayı

| Claim | Kaynak # | Kapsam |
|---|---|---|
| Güvenli bağlam (HTTPS) gerektirir | 3 | Kapsam dışı — `K011/web`/güvenlik |
| Açık izin prompt'u | 3 | Kapsam dışı |
| `Permissions-Policy` ile sınırlandırılabilir | 3 | ⚠️ VERIFICATION REQUIRED — repo header seti okunmadı |
| `getCurrentPosition` / `watchPosition` | 3 | Repo'da kullanım YOK → PLANNED |

---

## §A4 — getComputedStyle claim detayı

| Claim | Kaynak # | Kapsam |
|---|---|---|
| Canlı, read-only `CSSStyleProperties` döner | 4 | Ölçüm referansı — band doğrulaması |
| Düzenlenen stil okumayı anında günceller | 4 | UI ölçüm deseni |
| `:visited` için bilerek yanıltıcı olabilir | 4 | Güvenlik notu — altyapı riski |
| Repo kullanımı | 4 | ⚠️ VERIFICATION REQUIRED (grep aranmadı) |

---

## §A5 — Media queries claim detayı

| Claim | Kaynak # | Repo karşılığı |
|---|---|---|
| `min-width`/`max-width` ile band tanımlanır | 5 | `a-breakpoint-tokens.css` (token katmanı) — `web`/assets |
| `not`/`and`/`only` mantık operatörleri | 5 | Genel CSS kuralı |
| Range sözdizimi (`width <= 1250px`) | 5 | Kullanım aranmadı → ⚠️ |
| `@media` yalnız CSS'te değil `matchMedia` ile JS'te test edilir | 5 | JS band testleri `breakpoints.spec.js` (kod tabanlı eşik, media query değil) |

**Fark notu:** Repo'da band tespiti **JS/PHP koduyla** yapılıyor (viewport sayı karşılaştırması),
`@media` **CSS uygulamasıyla** — ikisi `a-breakpoint-tokens.css` üzerinden senkron (docblock
`DeviceDetector.php` L8: "a-breakpoint-tokens.css ile senkronize").

---

## §H — Araştırma geçmişi

| Tarih | İşlem | Araç | Sonuç |
|---|---|---|---|
| 2026-10-15 | 5 havuz URL erişimi | `webfetch` | 5/5 erişildi |
| 2026-10-15 | Claim↔repo matrisi | `read` + `grep` | 8 satır — 3 EVET |
| 2026-10-15 | Reddedilen adaylar | Havuz sınırı | 8 etiket |
| 2026-10-15 | `d-desktop.css` başlık çelişkisi | `read` | ⚠️ raporlandı |

---

## §I — Sonuç

| Kapı | Durum |
|---|---|
| Havuz 5 URL — erişim | 5/5 (webfetch) · curl 200 orkestratöre devredildi |
| Havuz tek dosyada | EVET (bu dosya) |
| Havuz-dışı URL (diğer 4 dosya) | 0 (yazım kuralı) |
| Kesişim betiği | §E — çalıştırma orkestratöre |
| Reddedilenler şemasız | 8 etiket, tam URL yok |
| Open sorular | 5 (§G) |

---

## §J — Kaynak ↔ Kullanım Haritası (her havuz URL'i nasıl kullanıldı)

| # | Kaynak | Kullanım noktası (bu dosya) | Kullanım türü | Boş kaynak mı? |
|---|---|---|---|---|
| 1 | PWA (MDN) | §A1 — masaüstü kurulabilirlik claim'i | claim çıkarma | hayır — 4 claim §A1'e taşındı |
| 2 | Navigator (MDN) | §A2 — UA/istemci claim'i | claim çıkarma | hayır — 5 claim §A2'ye taşındı |
| 3 | Geolocation (MDN) | §A3 — izin kapsamı sınırı | kapsam reddi | hayır — 4 claim §A3'e taşındı |
| 4 | getComputedStyle (MDN) | §A4 — bant ölçüm referansı | ölçüm referansı | hayır — 4 claim §A4'e taşındı |
| 5 | Media queries (MDN) | §A5 — band CSS kuralı | kural eşlemesi | hayır — 4 claim §A5'e taşındı |

**Toplam:** 5 kaynak · **21 claim** çıkarıldı · **kullanılmayan kaynak = 0**.

**Kritik okuma:** 3 numaralı kaynak (Geolocation) **kapsam reddi** olarak kullanıldı —
yani değeri "repo'da yok" kanıtını **çerçevelendirmek**ten geliyor. Boş kaynak yoktur;
ancak **4 kaynak PLANNED/kapsam dışı** sonuç üretti (§C: YOK/PLANNED 4) → araştırmanın
**raporlama gücü** kod-kanıtından **yüksek**, uygulama-kanıtından **düşüktür**.

---

## §K — Havuz Sınırı Gerekçeleri (neden yalnız 5 URL)

| # | "Şu da olsaydı" adayı | Neden eklenmedi | Karşı havuz |
|---|---|---|---|
| 1 | `matchMedia` API detayı | JS band testi konusu — `web` düğümü derinliği | `web/` |
| 2 | Service Worker / manifest | PWA uygulaması repo'da yok (§C satır 1) — boş konu | `web/` |
| 3 | Screen / viewport API'leri | Genel web ölçümü — `desktop`'a özgü değil | `web/` |
| 4 | Electron resmi dokümanı | Klasör havuzu sınırı (spec §6) | `electron/` |
| 5 | PHP CLI dokümantasyonu | Betik konusu — `local-runtime` | `local-runtime/` |
| 6 | Permissions-Policy header | Güvenlik header'ı — server katmanı | `server-runtime/` |
| 7 | CSS range sözdizimi (caniuse vb.) | Resmi standart kaynağı değil (§D satır 6) | — (red) |
| 8 | `windowControlsOverlay` detayı | PWA masaüstü API — repo'da yok (§C satır 7) → PLANNED | `web/` |

**Karar:** 5 URL **minimal ama yeterli** — §C'nin 8 satırındaki her konu ya EVET (3),
ya PLANNED/kapsam dışı (4), ya ⚠️ (1) ile **sonuçlandırıldı**; cevapsız kalan konu yok.

**Dürüstlük sınırı:** "Yeterli" iddiası **kapsam içindir** — yani "bu dosyanın sorularını
yanıtlamak için" yeterlidir; `desktop` düğümünün **tüm** web araştırması olduğu iddia
edilmez (bunun için 5 open soru §G'de bekler).

---

## §L — Claim Dayanıklılık Sınıflandırması (§C'nin derinleştirilmesi)

| # | Claim | Dayanıklılık | Dayanak | Ne zayıflatır? |
|---|---|---|---|---|
| 1 | Sunucu UA okuması var | **Güçlü** | Dosya+satır (L67) — diskte | Dosyanın değişmesi |
| 2 | Desktop bandı 1441-2560 | **Güçlü** | Dosya+satır (L15) + token katmanı | `d-desktop.css` çelişkisi (§C notu) |
| 3 | `d-desktop.css` 08_Devices'te | **Güçlü** | Dosya+satır (L1-7) — import zinciri | Dosyanın taşınması |
| 4 | PWA kurulumu yok | **Orta** | Yokluk kanıtı (grep sonucu) | Yanlış grep kapsamı |
| 5 | Geolocation yok | **Orta** | Yokluk kanıtı | Aynı |
| 6 | `getComputedStyle` kullanımı | **Zayıf** | ⚠️ grep aranmadı | Henüz ölçülmedi |
| 7 | `windowControlsOverlay` yok | **Orta** | Yokluk kanıtı | Aynı |
| 8 | `hardwareConcurrency` yok | **Orta** | Yokluk kanıtı | Aynı |

**Dağılım:** Güçlü 3 · Orta 4 · Zayıf 1.

**Kritik okuma:** Güçlü olanlar **varlık kanıtı** (dosya + satır), zayıf olan **eksik ölçüm**
(§C satır 4). Orta olanların 4'ü de **yokluk kanıtı** — yokluk kanıtı her zaman grep
kapsamına bağımlıdır; kapsam dışına çıkan bir dosya "yok" iddiasını **yanlış** yapar.
Dolayısıyla §G open soruları bu dosyanın **değil**, sonraki oturumun ölçüm görevidir.

---

## §M — Band Mimarisi: İki Kaynak Tek Token (claim↔repo derinleşmesi)

| # | Katman | Dosya | Satır | Rol |
|---|---|---|---|---|
| 1 | PHP tespit | `shared/src/Device/DeviceDetector.php` | 15 | Band aralığı sabiti (`desktop = 1441-2560`) |
| 2 | PHP tespit | `shared/src/Device/DeviceDetector.php` | 24-25 | `DESKTOP_MAX = 2560` eşik |
| 3 | PHP tespit | `shared/src/Device/DeviceDetector.php` | 67 | UA girdisi (`$_SERVER['HTTP_USER_AGENT']`) |
| 4 | PHP tespit | `shared/src/Device/DeviceDetector.php` | 124 | Band uygulama (docblock eşlemesi) |
| 5 | CSS token | `assets.coremusic.net/Css/08_Devices/d-desktop.css` | 1-7 | Cihaz paketi import'u |
| 6 | CSS token | `assets.coremusic.net/Css/08_Devices/d-desktop.css` | 4 | Band yorumu (**çelişkili**: 1025-1440) |
| 7 | JS eşik | `breakpoints.spec.js` | ⚠️ | Kod-tabanlı eşik testi (media query değil — §A5) |

**Akış (iddia):** Sunucu UA okur (satır 3) → bandı belirler (satır 1-2, 4) → CSS paketi
bandı uygular (satır 5) → JS testi eşiği doğrular (satır 7).

**⚠️ Çelişki (tekrar kaydı):** Satır 4 ile satır 6 **farklı aralık yazıyor** (1441-2560 vs
1025-1440). İki olasılık: (a) `d-desktop.css` yorumu eskimiş, (b) detector yanlış.
**Hangisi olduğu bu oturumda belirlenmedi** → tek gerçek: **iki kaynak uyuşmuyor**,
UI Designer'a handover (§G satır 5).

---

## §N — Reddedilenlerin Dağılım Analizi (§D'nin sayısal özeti)

| Red nedeni | Adet | Oran |
|---|---|---|
| Klasör havuzu sınırı (başka düğümün havuzu) | 2 | %25 |
| Havuzda yok (ek URL doğrulaması yapılmadı) | 3 | %37.5 |
| Resmi kaynak değil | 1 | %12.5 |
| Mükerrer konu (havuz zaten kapsıyor) | 2 | %25 |
| **Toplam** | **8** | **%100** |

**Okuma:** En büyük grup (%37.5) **süreç reddi** — aday düşünülmüş ama doğrulama
adımı çalışmamış. Bunu %25 **sınır reddi** izliyor. **İçerik reddi (konu yanlış) = 0** —
yani hiçbir aday "alakasız" değildi; hepsi **ya sınır ya prosedür** nedeniyle elendi.
Bu, havuzun **konu kapsamının geniş** olduğunu gösterir (yanlış içerik riski yok),
ancak **sürecin sıkı** olduğunu da gösterir (ek URL eklemek için 5 adımlı kapı §F gerekir).

---

## §O — Tekrar Üretim (reproducibility) — Bu Dosya Nasıl Yeniden Üretilir

| Adım | İşlem | Araç | Beklenen çıktı |
|---|---|---|---|
| 1 | Havuzu yaz (§A — 5 URL) | spec §6 | 5 satır tablo |
| 2 | Erişim doğrula | `webfetch` → `curl.exe` | 5/5 farklı sayfa |
| 3 | Claim çıkar (§A1–§A5) | kaynak okuma | 21 claim |
| 4 | Repo grep | `grep -n` (Dosya/Satır) | §C matrisi — EVET 3 |
| 5 | Kesişim | §E betiği | E.3 = 0 |
| 6 | Red etiketle (§D) | etiket only | 8 satır — URL yok |
| 7 | Open soru (§G) | çelişki/boşluk topla | 5 satır |

**Tutarlılık:** Adım 4'te **her EVET** `Dosya·Satır` olmadan yazılamaz; adım 3'te her
claim kaynak numarası (`Kaynak #`) taşır. Bu iki bağ, dosyanın **çapraz denetlenebilir**
olmasını sağlar.

**Bu oturumda adım 5'in betiği yazılmıştır ama KOŞULMAMIŞTIR** (repo kökü + orkestratör
kuralı) → §I'da kapı "çalıştırma devredildi" olarak durur; **"0" diye yazılmamıştır**.

---

## §P — Terimler (bu dosyaya özgü)

| Terim | Tanım | Kaynak |
|---|---|---|
| Havuz | Bir klasöre atanmış, kesişime kapalı URL kumesi | spec §1.6 |
| Kesişim | Havuz URL'inin başka K011 dosyalarında çıkma durumu (beklenen 0) | §E |
| Claim | Kaynaktan çıkarılan tekil iddia | §A1–§A5 |
| Yokluk kanıtı | "Repo'da arandı, bulunmadı" dayanağı — grep kapsamına bağımlı | §L |
| Varlık kanıtı | `Dosya·Satır` ile diskte gösterilen kanıt | §L |
| Şemasız red | Yalnız etiketle reddedilen aday (tam URL yazılmaz) | §D |
| Band | CSS/PHP tarafında viewport aralığı (masaüstü = desktop bandı) | §M |
| PLANNED | Repo'da olmayan ama hedeflenen — uydurulmaz, işaretlenir | §C |

---

## §Q — Kardeş Dosya Tutarlılığı (bu dosyanın iddiaları kardeşlerle uyumlu mu)

| # | Bu dosyadaki iddia | Kardeş dosya | Tutarlı mı? |
|---|---|---|---|
| 1 | Havuz yalnız bu dosyada (§A) | `desktop-kapsam/dogrulama/ornekler/bilesenler` | EVET — kardeşlerde URL 0 (yazım kuralı) |
| 2 | Band 1441-2560 (§C satır 5) | `desktop-kapsam.md` band tanımı | ⚠️ Okunmadı — çelişki riski §G'ye devredildi |
| 3 | `d-desktop.css` çelişkisi (§C notu) | `desktop-bilesenler.md` dosya envanteri | EVET — aynı çelişki orada da ⚠️ |
| 4 | PWA PLANNED (§C satır 1) | `web/` klasörü havuzu | EVET — konu `web`'e devredildi (§A havuz notu) |
| 5 | Electron ayrı havuz (§D satır 1-2) | `electron/` klasörü | EVET — spec §6 klasör sınırı |

**Sonuç:** 4/5 **tutarlı** · 1 ⚠️ (satır 2 — kardeş okunmadı, açıkça işaretli).

**Dürüstlük:** Satır 2'deki ⚠️ **gizlenmemiştir** — kardeş dosya bu oturumda okunmadığı
için tutarlılık **iddia edilmez**. Bu, §L'deki dayanıklılık sınıfının mantığının aynısıdır:
ölçülmemiş şey **doğru değil**, **bilinmiyor** sayılır.

---

## §R — Açık Sorulara Bağlı Aksiyonlar (§G'nin işlemesi)

| # | §G sorusu | Aksiyon | Sahip | Engel |
|---|---|---|---|---|
| 1 | PWA `desktop`'a girer mi? | Kapsam kararı — ADR/mimari not | architect | Karar yok |
| 2 | Geolocation izin akışı desktop'a özgü mü? | `Permissions-Policy` header okuma | security-engineer | Server kodu bu oturumda açılmadı |
| 3 | `getComputedStyle` kullanılıyor mu? | `grep -rn "getComputedStyle"` | ui-designer | Ölçüm yapılmadı (§L satır 6) |
| 4 | Band CSS `@media` mi sınıf mı? | `d-desktop.css` + JS okuması | ui-designer | İki mekanizma birlikte olabilir |
| 5 | `d-desktop.css` 1025-1440 çelişkisi | Yorum düzeltmesi veya detector düzeltmesi | ui-designer | §M'de iki olasılık — hangisi? |

**Durum:** 5/5 **açık** · **kapatılan = 0** · **süreç dışı (karar) = 1** (satır 1).

**Not:** Satır 3 ve 4'ün maliyeti **düşüktür** (tek grep + tek dosya okuması) — bu iki soru
sonraki oturumda **ilk** kapatılmalıdır; satır 5 ise **düzeltme** gerektirir (yazma izni
istemek gerekir → onay kapısı).

---

## §S — Dosya Bütünlük Denetimi (bu dosyanın kendi kapısı)

| # | Kapı | Beklenen | Sonuç |
|---|---|---|---|
| 1 | Havuz tek dosyada | Bu dosya dışında desktop/ içinde URL 0 | EVET (yazım kuralı §A) |
| 2 | Yeni URL eklendi mi? | Ek bölümde URL yok | EVET — §J–§S'de `http` 0 |
| 3 | Reddedilenler şemasız mı? | §D + §K'da tam URL yok | EVET — etiket only |
| 4 | Her EVET `Dosya·Satır` taşıyor mu? | §C, §M | EVET |
| 5 | Ölçülmüş olan ⚠️ işaretli mi? | §C satır 4, §L satır 6, §Q satır 2 | EVET — 3 ⚠️ |
| 6 | Kesişim betiği koşuldu mu? | Hayır — devredildi | AÇIK (§I, §O adım 5) |
| 7 | Frontmatter `group: kanit` son alan mı? | satır 9-10 | EVET |
| 8 | Footer `2026-10-15` mi? | §Q sonrası | EVET |

**Sonuç:** 7/7 **GEÇTİ** · kapı 6 **açık** (koşum orkestratörde — bilinçli devir,
uydurma "0" yazılmadı).

**Toplam sayım (bu oturum):** 5 URL havuzu · 21 claim · 8 red · 5 open soru ·
**yeni bölüm §J–§S = 10** · **yeni URL 0** · **uydurma iddia 0**.

---

## §T — Sonraki Oturum İçin Devir Notu (handover)

| # | Devredilen | Hedef | Öncelik | Bağlayıcı çıktı |
|---|---|---|---|---|
| 1 | Kesişim betiği koşumu (§E, §O adım 5) | orkestratör (repo kökü) | HIGH | E.3 = 0 · E.4 = 200×5 |
| 2 | `getComputedStyle` grep ölçümü (§R satır 3) | ui-designer | HIGH | §L satır 6 sınıfı → Güçlü/Orta |
| 3 | Band mekanizması okuması (§R satır 4) | ui-designer | MEDIUM | `@media` vs sınıf — tek satır cevap |
| 4 | `d-desktop.css` çelişkisi kararı (§M, §R satır 5) | ui-designer + onay | MEDIUM | Düzeltme yönü — **yazma onayı gerekli** |
| 5 | PWA kapsam kararı (§G satır 1) | architect | LOW | ADR/not — karar process'i |
| 6 | `Permissions-Policy` header okuma (§R satır 2) | security-engineer | LOW | ⚠️ → EVET/YOK |

**Toplam:** 6 devir · **HIGH 2** · MEDIUM 2 · LOW 2 · **düşük maliyetli başlangıç:**
satır 2 (tek grep — §R notundaki ilk kapanış adımı).

**Devir kuralı:** Bu maddelerin **hiçbiri bu oturumda yapılmadı** ve hiçbiri "yapıldı"
gibi raporlanmadı. Öncelik sırası **maliyet değil, bilgi değeri** ile belirlendi:
1. satır (kesişim) **tüm dosyanın geçerliliğini** doğrular; onun olmadan diğer
kanıtlar **koşulmamış** sayılır.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
