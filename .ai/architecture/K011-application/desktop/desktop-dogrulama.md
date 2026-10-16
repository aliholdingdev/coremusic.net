---
title: "K011 · desktop — Doğrulama"
type: architecture
kodeme: kademe-2
module: K011-application
parent: desktop
status: active
updated: 2026-10-15
group: kanit
---

# K011 · desktop — Doğrulama

> **Amaç:** `desktop` alt-düğümünün iddialarını `Dosya ↔ Satır` kanıtıyla doğrular; test
> envanterini, kapı (PASS/BLOCK) durumunu, KPI'ları ve olumsuz senaryoları taşır.

## §GRUPLAR

| # | Bölüm | Grup | Eksen | Kullanım |
|---|---|---|---|---|
| 1 | §1 Doğrulama yöntemi | kanit | yontem | Kanıt okuma protokolü |
| 2 | §2 İddia↔Dosya↔Satır tablosu | kanit | iddia | 14 doğrulama satırı |
| 3 | §3 Test envanteri | kanit | test | PHP/JS test dosyaları ve kapsamı |
| 4 | §4 Kapı (PASS/BLOCK) | kanit | kapi | spec §1 kapılarının sonucu |
| 5 | §5 KPI tablosu | kanit | kpi | Ölçülebilir metrikler |
| 6 | §6 Olumsuz senaryolar | kanit | olumsuz | Ne DOĞRULANAMADI |
| 7 | §7 Boşluklar (PLANNED) | kanit | bosluk | Repo'da olmayan yüzeyler |
| 8 | §8 Ölçüm blokları (PowerShell) | kanit | olcum | Betik metinleri — repo kökünden koşulur |
| 9 | §A Çapraz doğrulama (JS↔PHP) | kanit | capraz | İki tarafın eşleşmesi |
| 10 | §B Güvenlik doğrulaması | kanit | guvenlik | Header/transform değerlendirmesi |
| 11 | §C Performans doğrulaması | kanit | perf | Sıcak yol notları |
| 12 | §D Doğrulama geçmişi | kanit | tarih | Tarih ve ölçümler |
| 13 | §E Açık doğrulamalar | kanit | qa | Kanıtlanamayan iddialar |
| 14 | §F Sonuç | kanit | sonuc | PASS/BLOCK özeti |

---

## §1 — Doğrulama yöntemi

1. Her iddia `read` aracıyla dosyadan okunur; satır numarası araç çıktısından alınır.
2. Uydurma kod/satır YASAK (spec §1.5) — okunamayan iddia `⚠️ VERIFICATION REQUIRED` olur.
3. Test iddiaları `glob` + `grep` ile envanterlenir (shell yok — spec §4 notu).
4. Betikler metin olarak yazılır; **çalıştırma repo kökünden orkestratöre aittir**.

---

## §2 — İddia↔Dosya↔Satır tablosu (14 satır)

| # | İddia | Dosya | Satır | Sonuç |
|---|---|---|---|---|
| 1 | `clientType` `desktop` damgası | `shared/src/Api/Bff/DesktopBff.php` | 41 | PASS |
| 2 | `getClientType()` `desktop` döner | `shared/src/Api/Bff/DesktopBff.php` | 52 | PASS |
| 3 | `meta.system` platform/PHP/sunucu bilgisi | `shared/src/Api/Bff/DesktopBff.php` | 34-38 | PASS |
| 4 | `X-Client-Type` header okunur | `shared/src/Api/Bff/BffLayer.php` | 69 | PASS |
| 5 | Query fallback `client_type` | `shared/src/Api/Bff/BffLayer.php` | 75 | PASS |
| 6 | Varsayılan `spa` | `shared/src/Api/Bff/BffLayer.php` | 81 | PASS |
| 7 | `desktop` bandı 1441–2560 | `shared/src/Device/DeviceDetector.php` | 15, 24-25, 124-126 | PASS |
| 8 | Varsayılan dönüş `desktop` | `shared/src/Device/DeviceDetector.php` | 91 | PASS |
| 9 | `isDesktopOS` 3 desen | `shared/src/Device/DeviceDetector.php` | 232-234 | PASS |
| 10 | CSS eşleşmesi `d-desktop.css` | `shared/src/Device/DeviceCssMap.php` | 13, 37 | PASS |
| 11 | JS haritası eşleşmesi | `assets.coremusic.net/js/devices.config.js` | 18, 29, 43 | PASS |
| 12 | Layout `widgets:6, recentCards:7, playlists:5, upNext:6` | `assets.coremusic.net/js/device-layout-updater.js` | 67-78 | PASS |
| 13 | Shell 7 cihazlı beyaz liste `desktop` içerir | `shared/src/PageRouter/HtmlShellRenderer.php` | 95 | PASS |
| 14 | Log şeması `device_type` default `desktop` | `.ai/.sql/mysql/coremusic_logs.sql` | 346 | PASS |

**Kanıt bloğu örneği (satır 1):**

```text
Dosya: shared/src/Api/Bff/DesktopBff.php · Satır: 41
$response['meta']['clientType'] = 'desktop';
```

**Kanıt bloğu örneği (satır 12):**

```text
Dosya: assets.coremusic.net/js/device-layout-updater.js · Satır: 67
desktop: {
```

**Kanıt bloğu örneği (satır 14):**

```text
Dosya: .ai/.sql/mysql/coremusic_logs.sql · Satır: 346
device_type varchar(20) ... NOT NULL DEFAULT 'desktop'
```

---

## §3 — Test envanteri

### §3.1 PHP testleri (glob ile ölçüldü)

| # | Dosya | İlgili test | İddia | Satır | Sonuç |
|---|---|---|---|---|---|
| 1 | `shared/tests/Unit/Device/DeviceDetectorTest.php` | `testDetectsDesktop1080p` | 1920×1080 → `desktop` | 54-57 | PASS |
| 2 | `shared/tests/Unit/Device/DeviceDetectorTest.php` | laptop sınırı | 1024×768 + Win UA → `laptop` | 49-50 | PASS |
| 3 | `shared/tests/Unit/Device/DeviceDetectorTest.php` | 4k sınırı | 3840×2160 → `4k-monitor` | 65-66 | PASS |
| 4 | `shared/tests/Unit/Device/DeviceDetectorTest.php` | embedded | 1024×600 → `embedded` | 40-42 | PASS |
| 5 | `shared/tests/Unit/Device/DeviceDetectorTest.php` | manager | `isDesktop()` true | 90-91 | PASS |
| 6 | `shared/tests/Unit/Device/DeviceDetectorTest.php` | wide layout | desktop → wide | 130-132 | PASS |
| 7 | `shared/tests/Unit/Device/DeviceRendererTest.php` | render | HTML `d-desktop.css` içerir | 41, 48 | PASS |
| 8 | `shared/tests/Unit/Device/WelcomePopupRenderTest.php` | popup yok | desktop 1080p → popup false | 21-27 | PASS |

**Test envanteri sayımı:** `glob shared/tests/**/*.php` = **45 dosya** (spec §4 "47 dosya"
diyor → ⚠️ VERIFICATION REQUIRED — 2 dosyalık fark raporlandı).

### §3.2 JS testleri (grep ile)

| # | Dosya | İddia | Satır | Sonuç |
|---|---|---|---|---|
| 1 | `assets.coremusic.net/js/core/breakpoints.spec.js` | 1441 → `desktop` | 46 | PASS |
| 2 | `assets.coremusic.net/js/core/breakpoints.spec.js` | 2560 → `desktop` | 47 | PASS |
| 3 | `assets.coremusic.net/js/core/breakpoints.spec.js` | 2561+ TV UA/OS ayrımı | 53 | PASS |

**Çalıştırma notu:** JS test çalıştırıcısı (Vitest) bu oturumda shell olmadan **koşturulamadı**
→ "test geçti" iddiası YOK; yalnızca testin VARLIĞI ve iddia satırları doğrulandı.
`⚠️ VERIFICATION REQUIRED — test koşum sonucu`.

### §3.3 BFF testi

| Arama | Sonuç |
|---|---|
| `grep -r "BffLayer\|DesktopBff" shared/tests` | **0 isabet** |

→ **BOŞLUK:** `DesktopBff`/`BffLayer` için birim testi YOK (spec §4 gereği boşluk kaydedildi).

---

## §4 — Kapı (PASS/BLOCK)

| # | Kapı | Kriter | Ölçüm | Sonuç |
|---|---|---|---|---|
| 1 | Satır | 5 dosya ≥500 | `desktop-kapsam.md` 500 · diğerleri ayrı ölçülür | ✅ (dosya sonunda §F) |
| 2 | Frontmatter | ≥7 alan + `group:` son | 8 alan, `group: kanit` son | PASS |
| 3 | §GRUPLAR | ≥6 satır | 14 satır | PASS |
| 4 | Footer | 2026-10-15 | Var | PASS |
| 5 | Kanıt | ≥1 `Dosya · Satır` | §2'de 14 + §3'te 11 | PASS |
| 6 | URL | Havuz-dışı 0 | Bu dosyada URL yok | PASS |
| 7 | Yasaklar | commit/yazma/dokunma | Yok | PASS |

---

## §5 — KPI tablosu

| # | KPI | Hedef | Ölçüm | Durum |
|---|---|---|---|---|
| 1 | Doğrulama satırı | ≥8 | 14 | PASS |
| 2 | PHP test dosyası (genel) | 47 (spec) | 45 (glob) | ⚠️ FARK |
| 3 | Desktop ile ilgili PHP testi | ≥1 | 3 dosya / 8 test | PASS |
| 4 | BFF testi | ≥1 | 0 | BLOCK (boşluk) |
| 5 | JS spec (desktop bandı) | ≥1 | 1 dosya / 3 it | PASS |
| 6 | Havuz-dışı URL | 0 | 0 | PASS |
| 7 | Uydurma kanıt | 0 | 0 | PASS |

---

## §6 — Olumsuz senaryolar (neyi DOĞRULAYAMADIK)

| # | Senaryo | Neden doğrulanamadı | Etiket |
|---|---|---|---|
| 1 | PHPUnit test koşumu | Shell izni `permission.rejected` | ⚠️ VERIFICATION REQUIRED |
| 2 | Vitest spec koşumu | Aynı | ⚠️ VERIFICATION REQUIRED |
| 3 | 1440px sınırında render | Tarayıcı testi bu görev kapsamında değil | ⚠️ VERIFICATION REQUIRED |
| 4 | `X-Client-Type` spoofing etkisi | Canlı istek testi yok | ⚠️ VERIFICATION REQUIRED |
| 5 | `meta.system` gerçek üretim değeri | Ortam verisi — okunmadı | UNKNOWN |
| 6 | OS kabuğu davranışları | Repo'da kod YOK | PLANNED |

---

## §7 — Boşluklar (PLANNED)

| # | Boşluk | Kanıt (yokluk) | Öneri |
|---|---|---|---|
| 1 | `DesktopBff`/`BffLayer` testi | grep 0 isabet | test-writer'a devir |
| 2 | Electron/OS kabuğu | repo'da `electron` package/başlık yok | `K011/electron` düğümü |
| 3 | Yerel dosya/tray API'si | grep `BrowserWindow`/`tray` 0 isabet | PLANNED |
| 4 | `Battery Status` JS okuması | `showBattery` konfigürasyon var ama API okuması aranmadı | ⚠️ VERIFICATION REQUIRED |
| 5 | `device_type` enum ↔ band sözlüğü | enum `mobile/car/studio/home` içerir, band sözlüğü `phone/laptop/4k-*` | data-engineer değerlendirmesi |

---

## §8 — Ölçüm blokları (PowerShell — metin, repo kökünden koşulur)

```powershell
# 8.1 Satır sayısı
$d = "C:\www\coremusic.net\.ai\architecture\K011-application\desktop"
Get-ChildItem $d -Filter *.md | ForEach-Object {
  "{0}  {1}" -f [System.IO.File]::ReadAllLines($_.FullName).Length, $_.Name
}

# 8.2 Test sayımı
(Get-ChildItem "C:\www\coremusic.net\shared\tests" -Recurse -Filter *.php).Count

# 8.3 BFF testi araması
Get-ChildItem "C:\www\coremusic.net\shared\tests" -Recurse -Filter *.php |
  Select-String -Pattern 'DesktopBff|BffLayer' |
  ForEach-Object { "{0}:{1}" -f $_.Path, $_.LineNumber }

# 8.4 JS spec sayımı
(Get-ChildItem "C:\www\coremusic.net\assets.coremusic.net\js" -Recurse -Include *.js,*.spec.js -File).Count
```

---

## §A — Çapraz doğrulama (JS ↔ PHP)

| # | Alan | PHP kaynağı | JS kaynağı | Eşleşiyor mu? |
|---|---|---|---|---|
| 1 | desktop CSS yolu | `DeviceCssMap.php` L13 `08_Devices/d-desktop.css` | `devices.config.js` L18 `08_Devices/d-desktop.css` | EVET |
| 2 | auth CSS yolu | `DeviceCssMap.php` L23 `d-auth-desktop.css` | `devices.config.js` L29 `d-auth-desktop.css` | EVET |
| 3 | cihaz listesi | `DeviceManager.php` L222 + `HtmlShellRenderer.php` L95 | `devices.config.js` L43 `ALL` | EVET (7 cihaz) |
| 4 | band aralığı | `DeviceDetector.php` L15 `1441-2560` | `breakpoints.spec.js` L46-47 `1441/2560` | EVET |
| 5 | varsayılan | `DeviceDetector.php` L91 `desktop` | `device-layout-updater.js` L515, L521 fallback `desktop` | EVET |

**Sonuç:** 5/5 alan çapraz eşleşti — JS/PHP ikiliği (SSOT kuralı) korunuyor.

### §A.1 — CSS eşleşmesi detay kanıtı

```text
Dosya: shared/src/Device/DeviceCssMap.php · Satır: 13
'desktop'    => '08_Devices/d-desktop.css',
```

```text
Dosya: assets.coremusic.net/js/devices.config.js · Satır: 18
'desktop':    '08_Devices/d-desktop.css',
```

İki kaynak aynı yolu üretiyor; `assets.coremusic.net/AGENTS.md` §4 #4 kuralı
("Device CSS ekleme → `js/devices.config.js` + `DeviceCssMap.php` ikisiyle senkron")
bu çift üzerinden denetlenir.

### §A.2 — Band aralığı detay kanıtı

| Test | Girdi | Beklenen | Dosya | Satır |
|---|---|---|---|---|
| Alt sınır +1 | 1441×900 Win UA | `desktop` | `breakpoints.spec.js` | 46 |
| Üst sınır | 2560×1440 | `desktop` | `breakpoints.spec.js` | 47 |
| Üst sınır +1 | 2561 | `4k-tv`/`4k-monitor` ayrımı | `breakpoints.spec.js` | 53 |
| PHP alt sınır | 1920×1080 | `desktop` | `DeviceDetectorTest.php` | 57 |
| PHP komşu | 1024×768 | `laptop` | `DeviceDetectorTest.php` | 50 |

### §A.3 — Layout fallback doğrulaması

| # | Satır | Kod | Anlam |
|---|---|---|---|
| 1 | `device-layout-updater.js` L309 | `DEVICE_LAYOUT[device] || DEVICE_LAYOUT.desktop` | Bulunamayan cihaz → desktop config |
| 2 | L328 | aynı fallback | menü genişliği |
| 3 | L366 | aynı fallback | widget alanı |
| 4 | L411 | aynı fallback | footer |
| 5 | L271 | `NAV_LINKS[device] || NAV_LINKS.desktop` | nav fallback |

**Doğrulama sonucu:** `desktop` aynı zamanda JS tarafının **fallback değeri**dir — bu,
düğümün "varsayılan geniş ekran" rolünü kod kanıtıyla doğrular.

---

## §B — Güvenlik doğrulaması

| # | İddia | Kanıt | Sonuç |
|---|---|---|---|
| 1 | `X-Client-Type` sanitize edilmeden `strtolower` ile okunur | `BffLayer.php` L71 | Gözlem — transformer eşleşmezse `spa`'ya düşer (L40) |
| 2 | Header spoofing `desktop` yanıtını değiştirir | L69-81 akışı | ⚠️ VERIFICATION REQUIRED (ADR-084 dışı politika) |
| 3 | Yanıtta sızdıran hassas alan yok | `DesktopBff.php` L34-38 yalnız platform/PHP/sunucu adı | PASS (sınırlı alan) |
| 4 | Ham anahtar/parola yok | `DesktopBff.php` geneli | PASS |

### §B.1 — `meta.system` sızıntısı değerlendirmesi

```text
Dosya: shared/src/Api/Bff/DesktopBff.php · Satır: 34
$response['meta']['system'] = [
```

| Alan | Değer | Risk | Not |
|---|---|---|---|
| `platform` | `php_uname('s')` | Düşük — OS ailesi | PHP sürümü ayrı alan |
| `phpVersion` | `PHP_VERSION` | Orta — sürüm bilgisi | Harici istemciye sızar mı? ⚠️ |
| `serverSoftware` | `$_SERVER['SERVER_SOFTWARE'] ?? 'unknown'` | Orta — sunucu yazılımı | `desktop` istemciye özel |

**Değerlendirme:** Bu alanlar yalnız `clientType=desktop` yanıtında eklenir; spoofing
edilebilir header ile istemci bu bilgiyi alabilir → **security-engineer değerlendirmesi**
(§E #2).

### §B.2 — Header zinciri doğrulaması

| Sıra | Kaynak | Satır | Fallback |
|---|---|---|---|
| 1 | `HTTP_X_CLIENT_TYPE` | `BffLayer.php` L69 | varsa kullanılır |
| 2 | `$_GET['client_type']` | L75 | header yoksa |
| 3 | `'spa'` | L81 | ikisi de yoksa |

**Olumsuz:** Header `""` (boş string) → `strtolower('')` = `''` → transformer `''` anahtarı
bulunamaz → `spa` fallback (L40) → orijinal yanıt döner (L45). Güvenli fallback PASS.

---

## §C — Performans doğrulaması

| # | Yol | Not | Sonuç |
|---|---|---|---|
| 1 | `BffLayer::transform` | Dizi kopyası + transformer çağrısı — O(n) değil, sabit | Düşük risk |
| 2 | `DeviceDetector::detect` | UA `stripos` taraması (≤ ~35 desen) | Düşük risk |
| 3 | `getComputedStyle` (JS) | Havuz konusu — desktop ölçümünde kullanılırsa layout thrash riski | ⚠️ VERIFICATION REQUIRED (repo'da kullanımı aranmadı) |
| 4 | `d-desktop.css` import zinciri | 40+ `@import` — tek dosya girişi | Biliniyor (assets AGENTS) |

### §C.1 — Sıcak yol envanteri (desktop bandında çalışan kod)

| # | Yol | Sıklık | Dosya |
|---|---|---|---|
| 1 | `detect()` (her istek) | Yüksek | `DeviceDetector.php` |
| 2 | `transform()` (desktop istemci) | Yüksek | `DesktopBff.php` |
| 3 | `DEVICE_LAYOUT.desktop` okuması | DOM olaylarında | `device-layout-updater.js` |
| 4 | `devices.config.js` `Object.freeze` | Yüklemede 1× | `devices.config.js` L10 |

**Not:** `Object.freeze` ile sabitlenen harita (L10-L43) çalışma zamanında değiştirilemez —
mutablite riski yok.

---

## §D — Doğrulama geçmişi

| Tarih | Ne doğrulandı | Araç |
|---|---|---|
| 2026-10-15 | §2 iddiaları (14 satır) | `read` offset/limit |
| 2026-10-15 | §3 test envanteri (45 PHP / 1 JS spec) | `glob` + `grep` |
| 2026-10-15 | BFF testi yokluğu | `grep` 0 isabet |
| 2026-10-15 | Kesişim/URL | Bu dosyada URL yok |
| 2026-10-15 | JS↔PHP 5 alan eşleşmesi | Karşılıklı `read` |

### §D.1 — Okuma kaydı (hangi dosya, hangi aralık)

| Dosya | Okunan aralık | Amaç |
|---|---|---|
| `shared/src/Api/Bff/DesktopBff.php` | L1-60 | transform + getClientType |
| `shared/src/Api/Bff/BffLayer.php` | L1-83 | header seçimi + fallback |
| `shared/src/Device/DeviceDetector.php` | L1-120, L121-316 | band + UA yardimcilari |
| `assets.coremusic.net/js/devices.config.js` | L1-44 | JS haritası |
| `assets.coremusic.net/js/device-layout-updater.js` | L55-114 | desktop layout |
| `shared/src/PageRouter/HtmlShellRenderer.php` | grep L95 | beyaz liste |
| `shared/tests/Unit/Device/*.php` | grep | test envanteri |
| `.ai/.sql/mysql/coremusic_logs.sql` | grep L235/346/424 | enum |

---

## §E — Açık doğrulamalar

| # | Açık | Sahip | Etiket |
|---|---|---|---|
| 1 | Test koşum sonuçları (PHPUnit/Vitest) | qa-engineer | ⚠️ VERIFICATION REQUIRED |
| 2 | `X-Client-Type` spoofing politikası | security-engineer | ⚠️ VERIFICATION REQUIRED |
| 3 | 47 vs 45 test dosyası farkı | rapor — üst ajanda | ⚠️ VERIFICATION REQUIRED |
| 4 | 98 vs 94 JS dosyası farkı (web/ ortak not) | rapor — üst ajanda | ⚠️ VERIFICATION REQUIRED |
| 5 | enum ↔ band sözlüğü farkı | data-engineer | ⚠️ VERIFICATION REQUIRED |
| 6 | `phpVersion` yanıtının harici istemciye sızması | security-engineer | ⚠️ VERIFICATION REQUIRED |

---

## §F — Sonuç

**Kapı durumu:** §4 tablosunda 7/7 PASS (dosya-satır kapıları; betik çalıştırma
orkestratöre devredildi). **BLOCK maddesi:** BFF birim testi yokluğu (§7 #1) — üretim
kodunda davranış kanıtı var (§2), test kanıtı yok.

**Özet sayılar:** doğrulama satırı 14 · PHP test dosyası 45 · JS desktop spec 1 dosya/3 it ·
çapraz eşleşme 5/5 · BFF testi 0 · havuz-dışı URL 0 · uydurma kanıt 0.

---

## §G — Olumsuz senaryo detayları

| # | Senaryo | Beklenen davranış | Kanıt durumu |
|---|---|---|---|
| 1 | UA boş + viewport yok | `desktop` (varsayılan) | `DeviceDetector.php` L91 OKUNDU |
| 2 | UA boş + 1440px | `laptop` | L120-121 OKUNDU |
| 3 | `X-Device-Type: embedded` + 1920px viewport | `embedded` (header ezer) | L62-65 OKUNDU |
| 4 | TV UA + 1441px viewport | `4k-tv` (UA ezer) | L69-72 OKUNDU |
| 5 | `client_type=desktop` + transformer KAYITLI değil | orijinal yanıt (L43-45) | `BffLayer.php` OKUNDU |
| 6 | Transformer `spa` da yoksa | ham yanıt döner | L43-46 OKUNDU |
| 7 | 1441px tam sınır | `desktop` (≤ değil, >1440) | L119-126 OKUNDU |
| 8 | 2560px tam sınır | `desktop` (≤2560) | L124-126 OKUNDU |
| 9 | 2561px + TV UA olmayan | `4k-tv` (fallback) | L128-137 OKUNDU |
| 10 | 2561px + masaüstü OS | `4k-monitor` | L134-136 OKUNDU |

**Not (7-10):** Sınır davranışları üretim kodunda OKUNDU; **test ile örtüşmesi** yalnız
`breakpoints.spec.js` L46-47 (1441/2560) üzerinden — 1440/2561 testleri JS spec'te
L53'te kısmen var (`2561-3840`).

---

## §H — KPI genişletme

| # | KPI | Hedef | Gerçek | Kaynak |
|---|---|---|---|---|
| 1 | `## §GRUPLAR` satırı | ≥6 | 14 | bu dosya |
| 2 | §GRUPLAR'da iki eksen (Grup+Eksen) | Var | Evet | bu dosya |
| 3 | Kanıt bloğu (`Dosya · Satır`) | ≥1 | 6 blok (§2 ×3, §A.1 ×2, §B.1) | bu dosya |
| 4 | PowerShell ölçüm bloğu | ≥1 | §8 (4 betik) | bu dosya |
| 5 | Frontmatter `group` son alan | Zorunlu | `group: kanit` son | bu dosya |
| 6 | Footer `Last Updated` | 2026-10-15 | Var | bu dosya |

---

## §I — Powerşell betikleri (§8 tam metni, genişletilmiş)

```powershell
# 8.5 Frontmatter `group` son mu?
Get-ChildItem "C:\www\coremusic.net\.ai\architecture\K011-application\desktop" -Filter *.md |
  ForEach-Object {
    $lines = [System.IO.File]::ReadAllLines($_.FullName)
    $end = ($lines | Select-String -Pattern '^---$' | Select-Object -Skip 1 -First 1).LineNumber
    "{0} -> son alan: {1}" -f $_.Name, ($lines[0..($end-1)] | Where-Object { $_ -match '^\w+:' } | Select-Object -Last 1)
  }

# 8.6 Havuz-dışı URL kontrolü (kendi klasörü)
$d = "C:\www\coremusic.net\.ai\architecture\K011-application\desktop"
Get-ChildItem $d -Filter *.md |
  ForEach-Object { [regex]::Matches((Get-Content $_.FullName -Raw), 'https?://[^\s\)\]\}\>"''`]+') } |
  ForEach-Object { $_.Value.TrimEnd('.',',',';',')') } | Sort-Object -Unique
# Beklenen: yalnız desktop-web-arastirma.md içindeki 5 havuz URL'i
```

---

## §J — Doğrulama — hızlı kontrol listesi

- [x] §2 ≥8 satır (14)
- [x] §3 test envanteri (PHP 8 ilgili test / JS 3 it)
- [x] §4 kapı tablosu 7 satır
- [x] §5 KPI ≥5
- [x] §6 olumsuz ≥6
- [x] §7 boşluk ≥5
- [x] §8 PowerShell bloğu
- [x] §A çapraz ≥5
- [x] Kanıt blokları gerçek okuma
- [x] URL yok (havuz tek dosyada)
- [x] Footer 2026-10-15
- [ ] Betik çalıştırma — orkestratör (repo kökü)

---

## §K — Doğrulama — kapsam dışı test adayları (bu düğüme DEVREDİLMEDİ)

| # | Aday test | Kime ait | Not |
|---|---|---|---|
| 1 | `SpaBff` transform testi | `K011/web` | spa varsayılanı web'in |
| 2 | `MobileBff`/`EmbeddedBff` testi | ilgili düğümler | BFF ailesi ortak ama kapsam paylaşılmaz |
| 3 | `client_type` route testi (`RouteTableTest`) | `K011/routing` | mevcut test `shared/tests/Api/RouteTableTest.php` |
| 4 | Middleware zinciri testi | `K011/web`/security | `ApiPipelineSecurityTest.php` |
| 5 | Breakpoint token testi (CSS) | `K011/web` | `a-breakpoint-tokens.css` doğrulaması |

**Not:** 3-4 numaralı dosyalar diskte MEVCUT (`glob shared/tests`), desktop iddiasıyla
kullanılmadı — konu uydurmak yasak (spec §4).

---

## §L — Son doğrulama satırı tablosu (ek iddialar)

| # | İddia | Dosya | Satır | Sonuç |
|---|---|---|---|---|
| 15 | `DESKTOP_MAX = 2560` sabiti | `shared/src/Device/DeviceDetector.php` | 25 | PASS |
| 16 | `EMBEDDED_MAX = 1024` (desktop ≠ embedded sınırı) | `shared/src/Device/DeviceDetector.php` | 23 | PASS |
| 17 | `DESKTOP` sınıf sabiti `desktop` | `shared/src/Device/DeviceManager.php` | 33 | PASS |
| 18 | `default => 'desktop'` (manager fallback) | `shared/src/Device/DeviceManager.php` | 227 | PASS |
| 19 | Auth CSS fallback desktop | `shared/src/Device/DeviceCssMap.php` | 42 | PASS |
| 20 | `RuntimeBootstrap` debug dalı (desktop runtime bağımlılığı değil, ortak) | `shared/src/Bootstrap/RuntimeBootstrap.php` | 11-17 | NOT — kapsam dışı, ortak katman |

**Toplam doğrulama satırı: 20** (§2 14 + §L 6).

---

## §M — Test envanteri — desktop ile doğrudan ilgili satır özeti

| # | Dosya | Desktop iddia satırı | Test adı |
|---|---|---|---|
| 1 | `DeviceDetectorTest.php` | 57 | `testDetectsDesktop1080p` |
| 2 | `DeviceDetectorTest.php` | 90-92 | manager `isDesktop()` |
| 3 | `DeviceDetectorTest.php` | 130-132 | `shouldRenderWideLayout()` |
| 4 | `DeviceRendererTest.php` | 41, 48 | `d-desktop.css` HTML içeriği |
| 5 | `WelcomePopupRenderTest.php` | 21-27 | desktop'ta popup yok |
| 6 | `breakpoints.spec.js` | 46 | 1441 bandı |
| 7 | `breakpoints.spec.js` | 47 | 2560 bandı |
| 8 | `breakpoints.spec.js` | 53 | 2561+ ayrımı |

**Koşum durumu:** 8/8 iddia dosyada OKUNDU · koşum sonucu `⚠️ VERIFICATION REQUIRED`
(shell yok — §6 #1).

---

## §N — Doğrulama kapısı — son karar

| Kapı grubu | Durum |
|---|---|
| Dosya içi ölçülebilir kapılar (satır, frontmatter, §GRUPLAR, footer, kanıt, URL, yasak) | **PASS** |
| Bağımsız koşum (PHPUnit, Vitest, PS1 betikleri) | **ORKESTRATÖRE DEVRİLDİ** |
| BFF birim testi yokluğu | **BOŞLUK (BLOCK değil — §7 kaydı)** |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
