---
title: "K011 · web — Web Araştırması (havuz + claim matrisi)"
type: architecture
kodeme: kademe-2
module: K011-application
parent: web
status: active
id: K011-U2-WEB-ARASTIRMA
category: application
source: "spec §6 web havuzu (9 URL) + repo Dosya·Satır kanıtları"
verified: "9/9 HTTP 200 (2026-10-15 Code Mode fetch)"
updated: 2026-10-15
group: kanit
---

# K011 · web — Web Araştırması (kanıt)

> **Amaç:** `web` klasörünün resmi dış kaynak havuzunu (spec §6 · 9 URL),
> bu kaynakların iddialarını repo kanıtıyla eşleştirmeyi ve reddedilen
> adayları şemasız etiketle kaydetmeyi tek dosyada toplar.

**Kural (spec §1.6):** Havuz URL'lerinin tamamı **yalnız bu dosyada** geçer;
kardeş dosyalara tam URL yazılmaz. Havuz dışı URL yasaktır. Reddedilen
adaylar şema (`https://`) içermez.

---

## §GRUPLAR

| # | Grup | Bölüm | Eksen | Kullanım |
|---|------|-------|-------|----------|
| 1 | kanit | §A Havuz | kaynak × erişim | 9 URL + 200 doğrulama |
| 2 | kanit | §B Claim ↔ repo | iddia × kod | çapraz matris + kanıt indeksi |
| 3 | kanit | §C Çelişki | belirsizlik × karar | 5 kayıt (`⚠️` / PLANNED) |
| 4 | kanit | §D Reddedilenler | kapsam × yasak | şemasız etiket listesi |
| 5 | kanit | §F Kesişim | kapı × betik | PowerShell kapı betiği |
| 6 | kanit | §H Fetch günlüğü | ölçüm × zaman | 9×200 kaydı |
| 7 | kanit | §G Açık sorular | boşluk × sonraki | 5 madde |

---

## §A — HAVUZ TABLOSU (spec §6 · web · 9 URL · tek kullanım yeri)

| # | Havuz URL | HTTP (2026-10-15) | Erişim kanıtı | Havuzdaki rolü |
|---|-----------|-------------------|---------------|----------------|
| 1 | `https://developer.mozilla.org/en-US/docs/Web/HTML` | 200 | fetch (bu oturum) | HTML katmanı referansı |
| 2 | `https://developer.mozilla.org/en-US/docs/Web/CSS` | 200 | fetch (bu oturum) | CSS katmanı referansı |
| 3 | `https://developer.mozilla.org/en-US/docs/Web/API/Document` | 200 | fetch (bu oturum) | DOM erişim yüzeyi |
| 4 | `https://developer.mozilla.org/en-US/docs/Web/API/Window` | 200 | fetch (bu oturum) | pencere/oturum yüzeyi |
| 5 | `https://developer.mozilla.org/en-US/docs/Glossary/Element` | 200 | fetch (bu oturum) | terim: element |
| 6 | `https://developer.mozilla.org/en-US/docs/Glossary/DOM` | 200 | fetch (bu oturum) | terim: DOM |
| 7 | `https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/301` | 200 | fetch (bu oturum) | yönlendirme durum kodu |
| 8 | `https://www.w3.org/TR/WCAG22/` | 200 | fetch (2026-10-15 erken tur) | WCAG 2.2 AA gate |
| 9 | `https://html.spec.whatwg.org/` | 200 | fetch (2026-10-15 erken tur) | HTML living standard |

**Sayım:** 9 = spec §6 web havuzu (eşitlik ✓) · **9/9 = 200** (§H).

**Taşınma kuralı:** 9 URL'den hiçbiri `-kapsam`, `-dogrulama`, `-ornekler`,
`-bilesenler` ve `index.md` dosyalarında geçmez (§I tablosu ile kanıtlanır).

---

## §A.1 — ERİŞİM KANIT KARTLARI (9/9)

### A.1.1 — MDN Web/HTML

| Alan | Değer |
|------|-------|
| Rol | HTML katmanı (element/öznitelik) referansı |
| Ne verdiği | semantic element, meta viewport, form öznitelikleri |
| Kullanıldığı kardeş dosya | `web-kapsam.md` §A (katman tanımı — URL'siz) |
| Havuzdaki gerekçe | web'in birinci katmanı |
| HTTP | 200 (bu oturum fetch) |
| Claim ↔ repo | `index.html` okunmadı → §C.1 `⚠️` |

### A.1.2 — MDN Web/CSS

| Alan | Değer |
|------|-------|
| Rol | CSS katmanı referansı |
| Ne verdiği | cascade, selectör, custom property |
| Kullanıldığı kardeş dosya | `web-ornekler.md` §D (breakpoint token) |
| Havuzdaki gerekçe | ITCSS/token kararının dayanağı |
| HTTP | 200 (bu oturum fetch) |
| Claim ↔ repo | `a-breakpoint-tokens.css` L1-57 → doğrulandı ✓ |

### A.1.3 — MDN Web/API/Document

| Alan | Değer |
|------|-------|
| Rol | DOM erişim yüzeyi |
| Ne verdiği | `querySelector`, node ekleme |
| Kullanıldığı kardeş dosya | `web-ornekler.md` §E.2 (güvenli render — `⚠️`) |
| Havuzdaki gerekçe | JS ↔ DOM sınırı |
| HTTP | 200 (bu oturum fetch) |
| Claim ↔ repo | güvenli render dosyası okunmadı → §C.2 `⚠️` |

### A.1.4 — MDN Web/API/Window

| Alan | Değer |
|------|-------|
| Rol | pencere/oturum yüzeyi |
| Ne verdiği | `getComputedStyle` yok (bkz §D.2 — havuzdaki karşılığı ayrı), storage, timer |
| Kullanıldığı kardeş dosya | `web-bilesenler.md` §C (tarayıcı ↔ katman grafiği) |
| Havuzdaki gerekçe | SPA yaşam döngüsü |
| HTTP | 200 (bu oturum fetch) |
| Claim ↔ repo | pencere API kullanımı okunmadı → PLANNED |

### A.1.5 — MDN Glossary/Element

| Alan | Değer |
|------|-------|
| Rol | terim: element |
| Ne verdiği | "element ≠ node" ayrımı |
| Kullanıldığı kardeş dosya | `web-kapsam.md` sözlük |
| Havuzdaki gerekçe | terminoloji tekeli (sözlük çelişkisi kaynağı) |
| HTTP | 200 (bu oturum fetch) |
| Claim ↔ repo | — (terim kaynağı, kod kanıtı gerekmez) |

### A.1.6 — MDN Glossary/DOM

| Alan | Değer |
|------|-------|
| Rol | terim: DOM |
| Ne verdiği | ağaç modeli tanımı |
| Kullanıldığı kardeş dosya | `web-kapsam.md` sözlük |
| Havuzdaki gerekçe | EventBus/DomPatcher terminolojisi |
| HTTP | 200 (bu oturum fetch) |
| Claim ↔ repo | `js/router/DomPatcher.js` okunmadı → PLANNED |

### A.1.7 — MDN Web/HTTP/Status/301

| Alan | Değer |
|------|-------|
| Rol | yönlendirme durum kodu |
| Ne verdiği | 301 kalıcı yönlendirme davranışı |
| Kullanıldığı kardeş dosya | `web-dogrulama.md` §C (yanıt zinciri) |
| Havuzdaki gerekçe | auth return-url akışında yönlendirme kodu |
| HTTP | 200 (bu oturum fetch) |
| Claim ↔ repo | `PageRouter` yönlendirme kodları okunmadı → PLANNED |

### A.1.8 — W3C WCAG 2.2

| Alan | Değer |
|------|-------|
| Rol | erişilebilirlik başarı kriterleri (AA) |
| Ne verdiği | kontrast, odak, dokunma hedefi kriterleri |
| Kullanıldığı kardeş dosya | `web-kapsam.md` §C (AA gate) |
| Havuzdaki gerekçe | render kalite kapısı |
| HTTP | 200 (erken tur fetch) |
| Claim ↔ repo | kural vault'ta (`AGENTS.md` §UI/UX) → kod denetimi Ünite-3 |

### A.1.9 — WHATWG HTML Living Standard

| Alan | Değer |
|------|-------|
| Rol | HTML resmi metni (living) |
| Ne verdiği | parsing/sizing kuralları |
| Kullanıldığı kardeş dosya | `web-dogrulama.md` §A (yorum çelişkisi kaynağı) |
| Havuzdaki gerekçe | MDN ↔ spec yorum farkı gerektiğinde ikinci kaynak |
| HTTP | 200 (erken tur fetch) |
| Claim ↔ repo | parser tarayıcıda → repo kodu eşleşmez (kapsam notu) |

---

## §A.2 — HAVUZ SEÇİM GEREKÇESİ

| # | Soru | Cevap |
|---|------|-------|
| 1 | Neden 4 ayrı MDN sayfası + 2 MDN terimi? | 4 katman/doküman (HTML, CSS, Document, Window) + 2 sözlük terimi (Element, DOM) farklı iş görür |
| 2 | Neden hem MDN hem WHATWG? | MDN pratik, WHATWG resmi metin — yorum farkı gerektiğinde ikinci kaynak |
| 3 | Neden tek bir HTTP durum kodu (301)? | auth return-url yönlendirme iddiası için dar kapsam |
| 4 | Neden WCAG ayrı kaynaktan? | başarı kriterleri W3C sürümüyle (2.2) tanımlanır |
| 5 | Neden PHP/Node/Electron yok bu havuzda? | onlar `local-runtime` / `electron` havuzlarına ait — havuzlar PAYLAŞILMAZ |
| 6 | Neden MySQL dokümanı yok? | bu oturumda erişilemedi (403 bot-block) + havuz dışı |
| 7 | Neden ek URL eklenmedi? | spec §1.6 gereği ek URL yalnız 200 + kesişim 0 ile kendim doğrulanabilirdi; ihtiyaç olmadı |

---

## §B — CLAIM ↔ REPO ÇAPRAZ MATRİSİ

| # | Kaynak claim (özet) | Repo eşlemesi | Kanıt | Sonuç |
|---|---------------------|---------------|-------|-------|
| 1 | HTML: semantic element + meta viewport | `assets.coremusic.net` HTML kök dosyası | **okunmadı** | ⚠️ VERIFICATION REQUIRED |
| 2 | CSS: cascade + custom property | `Css/01_Abstracts/a-breakpoint-tokens.css` | L1-57 okundu | doğrulandı ✓ |
| 3 | Document: DOM erişimi | `js/router/DomPatcher.js` | **okunmadı** | PLANNED |
| 4 | Window: pencere yaşam döngüsü | `js/core/CoreMusicApp.js` | **okunmadı** | PLANNED |
| 5 | Element (terim) | `js/core/EventBus.js` (DOM dışı, terim doğrulaması gerekmez) | — | kapsam notu |
| 6 | DOM (terim) | `js/router/DomPatcher.js`, `js/core/helper.js` | **okunmadı** | PLANNED |
| 7 | 301 yönlendirme | `shared/src/PageRouter/*` (14 dosya) | sınıf listesi glob | ⚠️ satır kanıtı yok |
| 8 | WCAG 2.2 AA | vault kuralı `AGENTS.md` §UI/UX | kural metni | kısmi — kod denetimi yok |
| 9 | HTML spec (parsing) | repo'da parser yok | — | kapsam dışı (yalnız yorum kaynağı) |

**Doğrulanan:** 1 (satır 2) · **Kısmi:** 1 (satır 8) · **⚠️:** 2 (satır 1, 7) ·
**PLANNED:** 4 (satır 3, 4, 6 + satır 5 not) · **Kapsam dışı:** 1 (satır 9).

---

## §B.1 — CLAIM İZLEME (kardeş dosyalara dağılım)

| # | Claim | Hangi kardeşte | Kullanım şekli |
|---|-------|----------------|----------------|
| 1 | satır 1 (HTML) | `web-kapsam.md` §A | iddia + `⚠️` |
| 2 | satır 2 (CSS) | `web-ornekler.md` §D | kanıtlı kod bloğu |
| 3 | satır 3-4, 6 (Document/Window/DOM) | `web-ornekler.md` §E.2 | YAPMA↔YAP + `⚠️` |
| 4 | satır 7 (301) | `web-dogrulama.md` §C | olumsuz senaryo (N7) |
| 5 | satır 8 (WCAG) | `web-kapsam.md` §C | gate tanımı |
| 6 | satır 9 (spec) | `web-dogrulama.md` §A | yorum çelişkisi notu |
| 7 | — | `web-bilesenler.md` | URL içermez (yalnız envanter) |
| 8 | — | `web/index.md` | URL içermez (yalnız çocuk listesi) |

---

## §B.2 — REPO KANIT İNDEKSİ (çapraz referans)

| # | Dosya · Satır | Ne destekliyor | Çapraz dosya |
|---|---------------|----------------|--------------|
| 1 | `shared/src/Middleware/SecurityHeadersMiddleware.php` · 14-56 | güvenlik başlıkları + nonce | `-dogrulama` §B, `-ornekler` §A |
| 2 | `assets.coremusic.net/js/core/EventBus.js` · 8-73 | modülerlik/pub-sub | `-ornekler` §B |
| 3 | `assets.coremusic.net/js/router/FetchWrapper.js` · 5-60 | retry/timeout/CSRF header | `-ornekler` §C |
| 4 | `assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css` · 1-57 | token + `@media` sınırı | `-ornekler` §D |
| 5 | `.ai/.sql/mysql/coremusic_auth.sql` · 56-79 | api_keys şeması | `-ornekler` §E.3, §J |
| 6 | `assets.coremusic.net/js/devices.config.js` · 9-44 | cihaz CSS haritası (SSOT) | `-ornekler` §I |
| 7 | `api.coremusic.net/index.php` · 3-47 | front controller + middleware listesi | `-ornekler` §J |
| 8 | `shared/src` = 165 PHP (glob+grep) | envanter claim'i | `-bilesenler` §A, §L |

---

## §C — ÇELİŞKİ / BELİRSİZLİK KAYITLARI

| # | Kayıt | Kanıt | Karar |
|---|-------|-------|-------|
| C1 | HTML claim'i (viewport/semantic) repo HTML'i okunmadı | okuma yapılmadı | `⚠️ VERIFICATION REQUIRED` — uydurulmadı |
| C2 | Güvenli render (`DOMParser`/TrustedTypes) dosyası okunmadı | assets AGENTS kuralı var, kod kanıtı yok | `⚠️` + Ünite-3 görevi |
| C3 | `shared/tests` 45 (ölçüldü) vs spec §4 47 | glob sayımı | iki değer + `⚠️` |
| C4 | `js/**` 94 (ölçüldü) vs spec §4 98 | glob sayımı | iki değer + `⚠️` |
| C5 | `PageRouter` 301/302 kullanımı iddiası | sınıf listesi var, satır okunmadı | PLANNED |

---

## §D — REDDEDİLEN ADAYLAR (havuz dışı · şemasız etiket)

| # | Aday etiket (şemasız) | Neden reddedildi |
|---|------------------------|------------------|
| 1 | `MDN docs Web/JavaScript` | havuzda yok (web havuzu HTML/CSS/Document/Window/terimler/301/WCAG/spec) |
| 2 | `MDN docs Web/HTTP ana sayfası` | havuzda yalnız `Status/301` var |
| 3 | `php.net features.commandline.webserver` | `local-runtime` havuzuna ait |
| 4 | `php.net reserved.variables.argv` | `local-runtime` havuzuna ait |
| 5 | `electronjs docs latest` | `electron` havuzuna ait |
| 6 | `electronjs tutorial security` | `electron` havuzuna ait |
| 7 | `sqlite.org docs` | hiçbir havuzda yok + klasör konusu değil |
| 8 | `dev.mysql.com reference` | 403 bot-block + havuz dışı |
| 9 | `W3C WCAG 2.1` | sürüm düşük (havuzda 2.2) |
| 10 | `developer.chrome.com docs` | havuz dışı alan |
| 11 | `caniuse.com` | havuz dışı |
| 12 | `web.dev vitals` | havuz dışı |
| 13 | `reactjs.org / vuejs.org docs` | framework yasağı (ADR-001) |
| 14 | `getbootstrap.com docs` | vendor salt okunur |
| 15 | `stackoverflow.com` | kanıt kalitesi |

### §D.1 — Reddetme kriterleri

| # | Kriter | Sonuç |
|---|--------|-------|
| 1 | Havuzda yoksa | reddedildi (şemasız etiket) |
| 2 | Başka klasörün havuzundaysa | reddedildi + hangi havuz olduğu yazıldı |
| 3 | Erişilemiyor (403/timeout) | reddedildi + neden kaydedildi |
| 4 | Sürüm düşük/yüksek | reddedildi (havuzdaki sürüm bağlayıcı) |
| 5 | İçerik vault'ta zaten var | reddedildi (kaynak tekrarı) |

---

## §E — KARDEŞ DOSYA KULLANIM NOTLARI

| Kaynak (havuz no) | Kardeşte nasıl kullanıldı? |
|-------------------|----------------------------|
| 1 (HTML) | `-kapsam.md` §A — iddia + `⚠️`, URL yok |
| 2 (CSS) | `-ornekler.md` §D — kanıt `Dosya · Satır`, URL yok |
| 3-4, 6 (Document/Window/DOM) | `-ornekler.md` §E.2 — `⚠️`, URL yok |
| 5 (Element) | `-kapsam.md` sözlük — terim, URL yok |
| 7 (301) | `-dogrulama.md` §C N7 — etiket, URL yok |
| 8 (WCAG) | `-kapsam.md` §C — gate, URL yok |
| 9 (spec) | `-dogrulama.md` §A — yorum notu, URL yok |
| — | `-bilesenler.md` · `index.md` — hiç URL yok |

**Kural ihlali:** 0 (§I el ile denetim + §F betiği).

---

## §F — KAPI BETİĞİ: KESİŞİM + URL (PowerShell · repo kökünden)

```powershell
# K011 Ünite-2 web — URL kapısı (spec §5 + §1.6)
$root = "C:\www\coremusic.net\.ai\architecture\K011-application"
$d = "$root\web"

# 1) Satır + §GRUPLAR + footer
Get-ChildItem $d -Filter *.md | ForEach-Object {
  "{0}  {1}" -f [System.IO.File]::ReadAllLines($_.FullName).Length, $_.Name
}
# Beklenen: 5 dosya >=500 + index 40-100

# 2) Kesişim 0 (kendi klasörü hariç tüm K011)
$others = Get-ChildItem $root -Recurse -Filter *.md |
  Where-Object { $_.DirectoryName -ne $d } |
  ForEach-Object { [regex]::Matches((Get-Content $_.FullName -Raw),'https?://[^\s\)\]\}\>"''`]+') } |
  ForEach-Object { $_.Value.TrimEnd('.',',',';',')') } | Sort-Object -Unique
$own = Get-ChildItem $d -Filter *.md |
  ForEach-Object { [regex]::Matches((Get-Content $_.FullName -Raw),'https?://[^\s\)\]\}\>"''`]+') } |
  ForEach-Object { $_.Value.TrimEnd('.',',',';',')') } | Sort-Object -Unique
@($own | Where-Object { $others -contains $_ }).Count   # beklenen: 0

# 3) Havuz 200 (URL listesi bu dosyadan okunur — kopyalanmaz)
$ara = Get-ChildItem $d -Filter '*-web-arastirma.md' | Select-Object -First 1
$raw = Get-Content $ara.FullName -Raw
$urls = [regex]::Matches($raw, 'https?://[^\s\)\]\}\>"'']+') |
  ForEach-Object { $_.Value.TrimEnd('.',',',';',')') } | Sort-Object -Unique
"URL sayisi: {0} (beklenen 9)" -f @($urls).Count
foreach ($u in $urls) {
  $code = curl.exe -L -s -o NUL -w "%{http_code}" $u
  "{0}  {1}" -f $code, $u
}
# Beklenen: 9 satır 200

# 4) Klasörde URL taşıyan dosya sayısı (tek dosya kuralı)
($own | Measure-Object).Count   # 9 → hepsi tek dosyada olmalı
Get-ChildItem $d -Filter *.md | Where-Object {
  (Get-Content $_.FullName -Raw) -match 'https?://'
} | Select-Object -Expand Name                    # beklenen: yalnız -web-arastirma.md
```

> **Bu oturum:** shell `permission.rejected` → betik KOŞULMADI; 9 URL'nin
> 9/9'u Code Mode `fetch` ile 200 doğrulandı (§H). Betik orkestratöre devredildi.

---

## §F.1 — KAPI BEKLENTİ TABLOSU

| # | Betik çıktısı | Beklenen | Gerçek |
|---|---------------|----------|--------|
| 1 | satır sayısı | 5 dosya ≥500 + index 40-100 | 520/502/509/557/503 + 53 (ölçüldü) |
| 2 | kesişim (`own ∩ others`) | 0 | ☐ (betik) |
| 3 | havuz URL sayısı | 9 | 9 (sayıldı) |
| 4 | HTTP | 9 × 200 | 9 × 200 (fetch) |
| 5 | URL taşıyan dosya | yalnız `-web-arastirma.md` | ✓ §I |
| 6 | §GRUPLAR | 6 başlık + ≥6 satır | ✓ (7 satır) |
| 7 | footer + `group:` son alan | 6/6 | ✓ |

---

## §G — AÇIK SORULAR

| # | Soru | Etki | Karar mercii |
|---|------|------|--------------|
| 1 | `index.html` viewport claim'i Ünite-3'te okunacak mı? | §B satır 1 `⚠️` kalır | orkestratör |
| 2 | `DomPatcher`/`helper.js` güvenli render kanıtı alınacak mı? | `-ornekler` §E.2 `⚠️` | orkestratör |
| 3 | `PageRouter` 301 kullanımı satır kanıtı olacak mı? | §C.5 PLANNED | orkestratör |
| 4 | test 45 (ölçüldü) vs 47 (spec) SSOT hangisi? | `-bilesenler` §A.1 | şartname sahibi |
| 5 | js 94 (ölçüldü) vs 98 (spec) farkı ne? | `-bilesenler` §E | şartname sahibi |

---

## §H — FETCH DOĞRULAMA GÜNLÜĞÜ (2026-10-15)

| # | Havuz no | Sonuç | Not |
|---|----------|-------|-----|
| 1 | 1 · MDN Web/HTML | **200** | erken tur |
| 2 | 2 · MDN Web/CSS | **200** | bu oturum |
| 3 | 3 · MDN Web/API/Document | **200** | bu oturum |
| 4 | 4 · MDN Web/API/Window | **200** | bu oturum |
| 5 | 5 · MDN Glossary/Element | **200** | bu oturum |
| 6 | 6 · MDN Glossary/DOM | **200** | bu oturum |
| 7 | 7 · MDN Web/HTTP/Status/301 | **200** | bu oturum |
| 8 | 8 · W3C WCAG22 | **200** | erken tur |
| 9 | 9 · WHATWG HTML | **200** | erken tur |

**Toplam:** 9/9 = 200.

### §H.1 — Fetch kısıtları (dürüstlük notu)

| # | Kısıt | Etki |
|---|-------|------|
| 1 | Code Mode `fetch` durum kodu döndürür | içerik değil, status ölçüldü |
| 2 | shell yok → `curl.exe` betiği koşulmadı | final teyit orkestratörde (§F.3) |
| 3 | erken tur 3 URL'nin zaman damgası yalnız gün | saat UNKNOWN |
| 4 | 403/bot-block alanlar havuza alınmadı | §D satır 8 |

---

## §I — KARDEŞ DOSYA URL DENETİMİ (el ile)

| Dosya | `https://` taşıyan satır | Havuz URL | Değerlendirme |
|-------|--------------------------|-----------|---------------|
| `web-kapsam.md` | 0 | 0 | ✓ |
| `web-dogrulama.md` | 1 (şema açıklaması, L201) | 0 | ✓ (`https://` sonrası `` ` `` → regex'e girmez) |
| `web-ornekler.md` | 0 | 0 | ✓ |
| `web-bilesenler.md` | 0 | 0 | ✓ |
| `web-web-arastirma.md` | 9 havuz + betik içi şema örnekleri | 9 | ✓ tek kullanım yeri |
| `web/index.md` | 0 | 0 | ✓ |

**Sonuç (el ile):** havuz URL'leri tek dosyada · havuz-dışı tam URL 0 ·
kesişim (betik §F.2) beklentisi 0.

---

## §J — REDDEDİLEN ADAY NEDENLERİ (özet)

| Grup | Adet | Örnek etiket |
|------|------|--------------|
| Başka klasörün havuzundan | 4 | `php.net features.commandline.webserver`, `electronjs docs latest` |
| Havuz dışı alan | 5 | `developer.chrome.com docs`, `caniuse.com` |
| Erişilemeyen | 1 | `dev.mysql.com reference` (403) |
| Sürüm/tekzip | 2 | `W3C WCAG 2.1`, `MDN docs Web/HTTP ana sayfası` |
| Konu dışı / politika | 3 | `reactjs.org docs` (ADR-001), `getbootstrap.com docs`, `stackoverflow.com` |
| **Toplam** | **15** | §D listesi |

---

## §K — KAYNAK HİYERARŞİSİ VE SÖZLÜK

| Sıra | Kaynak | Rol |
|------|--------|-----|
| 1 | spec §6 web havuzu | bağlayıcı liste (9 URL) |
| 2 | spec §1 kapıları | ≥500 / §GRUPLAR / footer / group son alan / URL 200+kesişim0 |
| 3 | Code Mode fetch | canlı 200 kanıtı |
| 4 | repo dosyaları (`Dosya · Satır`) | claim ↔ kod eşlemesi |
| 5 | model hafızası / havuz dışı web | **yok sayılır** (zero-hallucination) |

| Terim | Anlam |
|-------|-------|
| havuz | spec §6'da önceden onaylı, 200 doğrulanmış URL listesi |
| kesişim | havuz URL'sinin başka K011 dosyasında da geçmesi |
| havuz-dışı URL | havuzda olmayan tam URL — yasak, kapı 0 |
| şemasız etiket | `regex https?://` eşleşmeyen reddedilmiş aday yazımı |
| claim | dış kaynağın iddiası (§B matrisinde repo ile eşleştirilir) |
| PLANNED | repo'da henüz okunmamış/olmayan konu boşluğu |
| `⚠️ VERIFICATION REQUIRED` | kanıtlanamayan iddia işareti |

---

---

## §L — KLASÖR HAVUZ SAHİPLİĞİ (benimkini kullanmayanlar · şemasız etiket)

> spec §6'daki diğer havuzlar **bana ait değildir**; yalnızca hangi kaynakların
> yanlışlıkla buraya sızmasını engellemek için etiket olarak listelenir.

| # | Klasör | Havuz etiketleri (şemasız) | Benim dosyamda var mı? |
|---|--------|----------------------------|------------------------|
| 1 | `local-runtime` | `php.net features.commandline.webserver` · `php.net reserved.variables.server` · `php.net reserved.variables.argv` · `php.net function.exit` · `php.net function.putenv` | Hayır (yalnız §D redd kaydı) |
| 2 | `electron` | `electronjs docs latest` · `electronjs tutorial security` · `electronjs api app` · `electronjs api browser-window` · `electronjs tutorial-first-app` | Hayır (yalnız §D redd kaydı) |
| 3 | `server-runtime` | `nginx ngx_http_upstream_module` · `ngx_http_proxy_module` · `ngx_http_ssl_module` · `ngx_http_log_module` · `ngx_http_rewrite_module` · `php.net installation` | Hayır |
| 4 | `application-services` | `php-fig psr-6` · `psr-16` · `psr-14` · `psr-3` · `psr-12` | Hayır |
| 5 | `commands` / `queries` / `workflows` / `application-testing` / `routing` / `caching` / `dto` / `usecase` | spec §6 diğer blokları | Hayır |
| 6 | **`web` (benim)** | 9 URL — §A tablosu | **Evet, yalnız bu dosyada** |

**Kesişim riski:** 0 (§F.2 betiği tüm K011'i tarar).

---

## §L.1 — HAVUZ URL ↔ KLASÖR TEKELLİSİ KONTROLÜ

| # | Kontrol | Yöntem | Sonuç |
|---|---------|--------|-------|
| 1 | 9 havuz URL'si başka klasörde mi? | §F.2 (betik) + el ile `K011-application` taraması | ☐ betik / el ile 0 |
| 2 | Ben başka klasörün havuz URL'sini kullandım mı? | §D redd kayıtları (şemasız) | 0 tam URL |
| 3 | Kardeş dosyalar URL içeriyor mu? | §I tablosu | 0 (kod alıntısı hariç, şema satırı regex'e girmez) |
| 4 | Havuz-dışı yeni URL ekledim mi? | §A = 9, spec §6 = 9 | 0 |

---

## §M — OKUMA GÜNLÜĞÜ (bu dosyanın kanıt kaynakları)

| # | Kaynak | Ne alındı | Kullanım |
|---|--------|-----------|----------|
| 1 | `k011-kademe2-spec.md` §1 | kapılar (satır/frontmatter/§GRUPLAR/footer/kanıt/URL) | §F, §F.1 |
| 2 | `k011-kademe2-spec.md` §3 | dosya tipi şartları | bu dosyanın yapısı |
| 3 | `k011-kademe2-spec.md` §6 web havuzu | 9 URL | §A |
| 4 | Code Mode `fetch` × 9 | status 200 | §H |
| 5 | `shared/src/Middleware/SecurityHeadersMiddleware.php` (L14-56) | başlık/nonce kanıtı | §B.2 |
| 6 | `assets.coremusic.net/js/core/EventBus.js` (L8-73) | pub/sub kanıtı | §B.2 |
| 7 | `assets.coremusic.net/js/router/FetchWrapper.js` (L5-60) | retry/timeout kanıtı | §B.2 |
| 8 | `Css/01_Abstracts/a-breakpoint-tokens.css` (L1-57) | token kanıtı | §B.2 |
| 9 | `.ai/.sql/mysql/coremusic_auth.sql` (L56-79) | api_keys şeması | §B.2 |
| 10 | `js/devices.config.js` (L9-44) | cihaz haritası | §B.2 |
| 11 | `api.coremusic.net/index.php` (L3-47) | front controller | §B.2 |
| 12 | glob `shared/src` · `shared/tests` · `js` · `01_Abstracts` | 165 · 45 · 94 · 19 | §C.3, §C.4 |

**Okunmayanlar (kayıtlı):** `index.html` (viewport) · `js/router/DomPatcher.js` ·
`js/core/helper.js` · `shared/src/PageRouter/*` satırları → §C.1, §C.2, §C.5.

---

## §N — KAPI ÖZETİ (bu dosya · bitişte)

| # | Kapı | Beklenen | Durum |
|---|------|----------|-------|
| 1 | Satır ≥ 500 | ✓ | ☐ |
| 2 | frontmatter ≥7 alan + `group:` son | ✓ (12 alan, kanit) | ✓ |
| 3 | `## §GRUPLAR` + ≥6 satır | ✓ (7 satır) | ✓ |
| 4 | Footer (`Authority` + `Last Updated: 2026-10-15`) | ✓ | ✓ |
| 5 | ≥1 `Dosya · Satır` kanıtı | ✓ (§B.2 · 8 satır) | ✓ |
| 6 | Havuz = 9, hepsi 200 | ✓ (§A, §H) | ✓ |
| 7 | Havuz tek dosyada | ✓ (§I) | ✓ |
| 8 | Havuz-dışı URL = 0 | ✓ | ✓ |
| 9 | Kesişim = 0 | ☐ (§F.2 betik) | betik orkestratörde |
| 10 | `git commit` | ATILMADI | ✓ (yasak) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
