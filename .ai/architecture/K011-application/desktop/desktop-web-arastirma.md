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

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
