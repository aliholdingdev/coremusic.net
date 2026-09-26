---
title: "CoreMusic — ADR-021: SPA Router Immutable Contract (Path Şeması · Parametre Kuralları · 404/Redirect Politikası · Golden Route Listesi CI Kapısı)"
type: adr
category: frontend
date: 2026-09-25
updated: 2026-09-25
version: 1.0.0
status: accepted
authority: ADR-021 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-021: SPA Router Immutable Contract (Path Şeması · Parametre Kuralları · 404/Redirect Politikası · Golden Route Listesi CI Kapısı)

**Durum:** accepted (kullanılabilir — **frozen YOK**)
**Tarih:** 2026-09-25
**Karar Veren:** Vault Steward (kullanıcı onaylı — "ADR-021'i sıfırdan yaz"; karar içeriği kullanıcı onaylı: **(a) path şeması · (b) parametre kuralları · (c) 404/redirect 301-302 politikası · (d) immutable tanımı = breaking değişiklik yeni major + yeni ADR olmadan YASAK, additive rota ekleme serbest + golden route listesi CI kapısı**) · debate: **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL — 3 şart §5.4)** · Tech Lead: **✅ (2026-09-25)**
**İlgili ADR'ler:** [[ADR-004-multi-domain-spa]] (çift router — sunucu PageRouter + istemci History API; bu ADR iki tarafı TEK sözleşmeye bağlar; dosya diskte VAR ✅) · [[ADR-016-url-normalization]] (case folding / percent-decode o ADR'nin PLANNED şartı; bu ADR yalnız kanonik biçimi tanımlar; dosya diskte VAR ✅) · [[ADR-009-clean-url-redirect]] (tek canonical yöne 301 — bu ADR 301/302 politikasını yazar; dosya diskte VAR ✅) · [[ADR-013-rate-limiting-apcu]] (audit/log ruhu → bilinmeyen yolda ERROR alert; dosya diskte VAR ✅) · [[ADR-005-ultrathink-protocol]] (`⚠️ VERIFICATION REQUIRED` kanıt standardı; dosya diskte VAR ✅) · [[ADR-002-pdo-mandatory-no-orm]] (route çözümlemesi config dosyasından okunur — router'a DB sorgusu eklenemez; dosya diskte VAR ✅) · karar dizini [[../index]] **satır 58** `[[ADR-021-spa-router-immutable-contract]]` (slug eşleşmesi ✅) · düz metin **ADR-083-spa-router** (`../index` satır 105 — dosya diskte YOK → wiki-link KURULMAZ, ⚠️ VERIFICATION REQUIRED).

---

## 1. Bağlam (Context)

CoreMusic SPA'da **route sözleşmesi** üç ayrı yerde aynı anda yaşar: (1) sunucu PageRouter (`RouteRegistry` + 2 config dosyası), (2) ADR-004 ile kurulan istemci History API router'ı, (3) **dış dünya** (yer imi, e-posta linkleri, arama motoru sonuçları, sosyal paylaşım). Bugün bu üçü arasında hiçbir bağlayıcı kayıt yok: hangi yolların var olduğu, hangi biçimde yazıldığı, silinip yeniden adlandırılamayacağı hiçbir dosyada yazılmıyor; liste değiştiğinde hiçbir test kırmızı olmuyor. Bu ADR **yol ekleyip çıkaran kod değildir** — mevcut yol listesini dondurur, ihlalini tespit eden mekanizmayı (golden route listesi + CI kapısı) yazar ve 404/redirect davranışını durum bazında sabitler.

### 1.1 Mevcut Durum

**Kod/vault kanıtları (diskte okundu — IMPLEMENTED/PLANNED etiketleri dosya + satır ile):**

**A) ROUTE TANIMLARI — IMPLEMENTED (iki config, iki host):**

- **`shared/config/routes.php:24-123` → 15 anahtar (home host):** `login` (:26), `register` (:32), `logout` (:38), `select-gender` (:44), `forgot-password` (:50), `reset-password` (:56), `auth/callback` (:64), `home` (:71), `player` (:80), `kesfet` (:88), `albumler` (:94), `albumler/detay` (:100), `sanatcilar` (:106), `playlist` (:112), `ayarlar` (:118). **Gözlem:** tüm anahtarlar lowercase ASCII; Türkçe karakter yok (`kesfet`, `sanatcilar` — ASCII fold); en derin yol **2 segment** (`auth/callback`, `albumler/detay`); 6 anahtar (`login`…`reset-password`) `page: 'redirect'` ile **auth host'una yönlendirme**.
- **`shared/config/auth-routes.php:12-62` → 7 anahtar (auth host):** `login` (:13), `register` (:20), `select-gender` (:27), `forgot-password` (:34), `reset-password` (:41), `logout` (:48), `set-gender` (:55). Hepsi `page` + `handler: 'auth_post'` + `meta: ['auth_page' => true]`.
- **Çakışma bulgusu:** 6 anahtar **iki listede de var ama anlamı farklı** (home'da redirect, auth'da form sayfası) → **sözleşme host'a özeldir; tek birleşik liste yanlıştır.**
- **Yükleyiciler:** `home.coremusic.net/config/bootstrap.php:85-88` (vendor fallback + `shared/config/routes.php`), `auth.coremusic.net/index.php:193` (`shared/config/auth-routes.php`), `PageRouterKernel.php:96` (`loadFromFile`).

**B) ROUTE REGISTRY — IMPLEMENTED:**

- `register()` — `shared/src/PageRouter/RouteRegistry.php:10-13`: anahtar = `trim($route->path, '/')`.
- `loadFromFile()` — `:15-33`: `include` ile okur; **`SpaRoute` olmayan satırlar sessizce atlanır** (`:28-30` — config hatası iz bırakmaz).
- `resolve()` — `:35-54`: `trim($uri,'/')` (`:37`) → boş ise `home` (`:39-41`) → **tam eşleşme `isset` (`:43`) — `strtolower` YOK = case-sensitive** → pattern döngüsü (`:47-51`) → `null` (`:53`).
- `matchesPattern()` — `:72-82`: `{param}` → `[^/]+` (`:78`), sarmalayıcı `#^...$#` (`:79-81`) → **parametre `/` içeremez; tip doğrulama yok.**
- Yardımcılar: `getRegisteredKeys()` `:56-59` (**golden fixture'ın dayanağı**), `getProtectedRouteKeys()` `:61-70` (`requiresAuth` filtresi).
- `SpaRoute` — `shared/src/PageRouter/SpaRoute.php:7-18`: readonly ctor, 10 alan (`page`, `requiresAuth=true`, `title`, `requiredRole`, `requiredPermission`, `cacheable=true`, `meta`, `path`, `handler`, `cacheTtl`).

**C) 404 / REDIRECT — IMPLEMENTED:**

- **404 iki kaynaktan:** `PageRouter.php:36` `resolve()` null → `RouteResult::notFound($uri)` (`:39`); sayfa dosyası yoksa + debug kapalı (`:84-90`, özellikle `:89`) → ikinci `notFound`. `RouteResult.php:30-37` → `httpStatus: 404` + `['error' => 'not_found']`.
- **HTML sarmalayıcı:** `PageRouterKernel.php:151-184` → `:157-160` `type==='redirect' || httpStatus===302` → **302'ye zorlar**; `:162-165` `403 + redirect` → **302**; `:167-169` `404` → ErrorHandler HTML; `:171-173` diğer 4xx/5xx → ErrorHandler; `:129-141` catch → **500 JSON** + dosya log (`:130-133`) + `error_log` (`:134-135`).
- **Emitter:** `ResponseEmitter.php:80-89` `redirect()` **varsayılan 302**; `:23-26` emit sırasında `type==='redirect' || httpStatus===302` → redirect yolu.
- **`301` grep'i (`shared/src/PageRouter/*.php`) → 0 sonuç** → **kodda 301 yok**; oysa [[ADR-009-clean-url-redirect]] kalıcı canonical için 301 şart koşar → **301 PLANNED.**
- **Redirect URL üretimi:** `PageRouterKernel.php:186-195` segment bazlı `rawurlencode` (giriş tarafında karşılığı yok — §1.2/5).

**D) NORMALİZASYON — KISMEN (ADR-016 hizası):**

- `RequestNormalizer.php`: yalnız `index.php` öneki, leading slash, host/scheme/port, `FILTER_SANITIZE_URL` (`:42`); `strtolower` yalnız proto/HTTPS'te (`:68`, `:74`) → **path üzerinde case folding YOK, `rawurldecode` YOK** (ADR-016 bulgusu bu yazımda teyit edildi).
- **`shared/tests/**/*ormaliz*` → 0 dosya** → normalizer testi YOK.

**E) TESTLER — IMPLEMENTED (dar kapsam):**

- `shared/tests/Unit/PageRouter/` = **4 dosya / 21 test:** `RouteRegistryTest.php` 5 test (`:11,22,33,43,54` — `testResolveNotFound` `:33` 404'ü doğrular), `AuthGuardTest.php` 8, `AuthUrlBuilderTest.php` 6, `SpaRouteTest.php` 2.
- **Golden route listesi / fixture testi YOK:** repo içi `golden|fixture` route araması → **0 sonuç.**

**F) DİZİN REZERVASYONLARI (bu dosya ile canlanır):**

- `.ai/.decisions/index.md:58` · `.ai/index.md:638` · `.ai/keys.md:256` · `.ai/brain.md:976` · `.ai/.templates/adr/adr-index.md:92` (`🔵 frontend`, `adr-frontend-template.md` — dosya diskte VAR ✅) · `.ai/glossary.md:393` (ADR-021 referansı).

**Sonuç etiketi:** **IMPLEMENTED:** 22 route anahtarı (15 + 7), Registry register/loadFromFile/resolve/parametre eşleme, 404 (çift kaynak), 302 zorlama (emit + shell), 21 test, 2 yükleyici, `getRegisteredKeys()` fixture API'si. **PLANNED:** golden route listesi + CI kapısı, 404 ERROR alert, 301 canonical (ADR-009), case folding/percent-decode + normalizer testleri (ADR-016), `SpaRoute` olmayan satır için hata log'u. Bu ADR **kod sözü değil, yol sözleşmesidir.**

### 1.2 Sorun Tanımı

1. **Sözleşme hiçbir yerde yok:** 22 anahtar iki config'te dağınık, 6 ortak anahtarın anlamı host'a göre farklı; "bunu silebilir miyiz?" sorusu cevapsız → silme/renaming **sessiz** kırılır (bookmark, e-posta linki, istemci router).
2. **Golden liste yok:** liste değiştiğinde hiçbir test kırmızı olmaz (grep 0) → koruyucu kapı yok.
3. **Case-sensitivity açığı:** `resolve()` tam eşleşme `isset` (`RouteRegistry.php:43`) + normalizer path'e lowercase yapmıyor → `/Home`, `/Kesfet` = 404; kullanıcı hatası ile kırık yol aynı görünür.
4. **301/302 tutarsızlığı:** kodda yalnız **302** (`ResponseEmitter.php:80-89`, `PageRouterKernel.php:157-165`), `301` grep 0; [[ADR-009-clean-url-redirect]] ise kalıcı canonical için 301 şart → **kalıcı yönlendirme 302 ile yapılıyor = tarayıcı/SEO önbelleği yanlış.**
5. **Parametre sözleşmesi yazılı değil:** sözdizimi `{ad}` (`:78`), eşleşme `[^/]+` (`:79`), **girişte percent-decode yok**, **redirect'te `rawurlencode` var** (`PageRouterKernel.php:186-195`) → kodlama asimetrik, `%2F` davranışı tanımsız, yeniden adlandırma ihlali sayılmıyor.
6. **404 sessiz:** `notFound` var ama bilinmeyen yol için ERROR alarm/audit yok → kırık link akışı izlenemez (ADR-013 ruhu).
7. **Config hatası sessiz:** `loadFromFile` `SpaRoute` olmayan satırı atlıyor (`RouteRegistry.php:28-30`) → yazım hatası route kaybı yaratır, kimse fark etmez.
8. **Çift router drift riski (ADR-004):** istemci History API router'ı bu listeden bağımsız büyüyebilir; sunucu 404 verirken istemci sayfa açabilir (veya tersi).

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: prompt-maker web araştırması protokolü (`.claude/skills/prompt-maker/references/10-web-research-protocol.md` — diskte VAR ✅) — resmi/anahtar kaynak önce (pact.io, cloud.google.com, reactrouter.com, atlassian, vitest), **her ana iddia ≥2 bağımsız çapraz kaynak**, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`. **Odak: (a) contract testing 2025-26 (consumer-driven + provider contract), (b) SPA route/API backward compatibility, (c) breaking-change taksonomisi (additive vs breaking), (d) golden/fixture/snapshot test CI pratikleri.** Erişim: **4 websearch sorgusu**; sonuçlar başlık/özet düzeyinde derlendi — derin sayfa-içi tur ayrı doğrulamaya bırakıldı (açıkça işaretli).

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "consumer-driven contract testing 2025 Pact provider consumer best practices" · (2) "golden file fixture snapshot testing CI best practices update workflow" · (3) "API breaking change taxonomy additive backward compatible Google API design Zalando RFC 1105" · (4) "SPA route URL backward compatibility breaking change bookmark redirect table client router" |
| Web Search **Konusu** | (1) Consumer-driven contract testing: Pact akışı (consumer expectation → provider verification → can-i-deploy), 2025'te olgunluk/eleştiriler, expectation'lar tek testte toplama, production-aware contract stratejisi; (2) Golden/fixture/snapshot test: altın dosya ile beklenen çıktı saklama, güncelleme akışı (`-u`/update), CI'da bilinçli onay adımı, snapshot'ın drift'i yakalaması; (3) Breaking-change taksonomisi: additive (ekleme) vs breaking (silme/değiştirme) — request tarafında "sunucu daha toleran hale gelirse güvenli", response tarafında "daha kesin hale gelirse güvenli" asimetrisi; URL path değişimi = breaking; (4) SPA route uyumluluğu: `keep/rename/retire` yol envanteri, redirect tablosu (eski→yeni), bookmark/e-posta/SEO etkisi, trailing slash'ın sözleşme ayrıntısı olması, emekliye ayrılan yolda edge 301. |
| Web Search **Bağlam** | **~43 adlandırılmış kaynak / 4 sorgu**: docs.pact.io + pact.io + pactflow.io + sachith.co.uk (2025 saha yazısı) + dev.to + medium (Pact rehberi) + Senacor (production dersleri) + leapcell + prismatic + sigmainst (10) · vitest.dev + testland.io + atlassian + testsigma + applitools + wallarm + strongdm + webym + swanlabs + test-pact.io + qaskills.sh + github (olshansk) + medium (veri hattı testi) + github (atheory-ai golden master) (14) · cloud.google.com/apis/design + Zalando + IBM api-handbook + Siemens + adidas + rust-lang RFC 1105 + Zuplo + apidog + New Relic + tvh-equipment + letsbuildsolutions (11) · frontend-routing.com ×2 + @spfn/core contract README + reactrouter.com ×2 + docs.internetcomputer.org + letsbuildsolutions + web-accessibility-a11y.com (8) |
| Web Search **Kısa Açıklama** | **(1) Contract testing:** Pact belgeleri consumer expectation'ın provider'da tek testte doğrulandığını, deploy kapısının (`can-i-deploy`) contract sonucuna bağlandığını anlatır; sachith (2025) theory→production'a geçişte expectation'ların tek yerde toplanmasını ve production farkındalığını; Senacor gerçek sahada "iyi manners" (dar expectation, açık isimler) derslerini; eleştiri tarafı dev.to/medium CDC'nin ağır/az ekip için gereksiz olduğunu savunur → **bizim ölçümüz: aynı repo içinde sunucu+istemci olduğu için tam CDC değil, golden liste + CI kapısı yeterli.** **(2) Golden/snapshot:** vitest.dev snapshot'ın "beklenen çıktıyı dosyada sakla, değişince önce onayla" akışı; testland golden vs snapshot farkı (golden = yapılandırılmış senaryo); atlassian/testsigma/applitools pratikler; qaskills/github CI'da fixture güncelleme akışı → **liste değişimi = gözden geçirme adımı.** **(3) Taksonomi:** Google Cloud API tasarımı "ekleme genelde güvenli, silme/Değiştirme kırıcı" + gerçek dünya kuralları; Zalando "non-breaking torture"; RFC 1105 semver/evrim kuralları; letsbuildsolutions "eski spec ile yazılmış istemci hâlâ çalışıyorsa non-breaking" kuralı; spfn contract README request/response asimetrisi tablosu. **(4) SPA yol uyumu:** frontend-routing `keep/rename/retire` envanteri + redirect tablosu + "trailing slash sözleşme ayrıntısı, iki biçimi de test et" + "emekliye ayrılan yola edge 301, client 200 değil"; reactrouter future flags ile "major'ları sıkıcı tutma" stratejisi; internetcomputer SPA catch-all 200 ile 404 sayfası ayrımı. |
| Web Search **Uzun Açıklama** | **(a) Contract testing 2025-26 (kaynak 1-10):** Pact ekosistemi hâlâ omurga: consumer, sunduğu expectation'ları tek dosyada toplar (docs.pact.io "tek always-expect" en iyi uygulama), provider bunu kendi servisi üzerinde doğrular, `can-i-deploy` yalnız iki taraf da yeşilse geçer. 2025 saha yazısı (sachith) iki kritik ders verir: expectation'lar kod gibi versiyonlanır ve **sözleşmeyi okuyan tek bir "gerçek" olmalı** (ikinci kopya = drift); ayrıca contract, production davranış bilgisiyle birleşmezse yalan söyler. Senacor üretimi deneyim, isimlendirme/dar tutma ("good manners") olmadan contract'ın gürültüye dönüştüğünü gösterir. Eleştiri kolu (dev.to/medium/leapcell) CDC'yi "herkese lazım değil" diye çerçeveler: aynı derleme/same deploy'da olan taraflarda tip sistemi zaten kırıcıyı yakalar; CDC **ayrı dağıtılan** istemciler (mobil, entegrasyon) için doğar. **(b) Golden/fixture/snapshot (kaynak 11-24):** ortak kalıp: beklenen çıktıyı repoya koy, test karşılaştırsın, fark varsa **önce insan onayı** (`-u` ile bilinçli güncelleme), CI'da otomatik overwrite yok; golden = yapı/senaryo katmanı, snapshot = çıktı katmanı; LLM çıktısı gibi kırılgan (brittle) alanlarda golden master deseni kullanılır. **(c) Taksonomi (kaynak 25-35):** Google Cloud uyumluluk sayfası ve Zalando/IBM/adidas kılavuzları aynı çekirdeği paylaşıyor: **ekleme (yeni opsiyonel alan/yeni yol) uyumlu; silme, yeniden adlandırma, tip/zorunluluk daraltması kırıcı**; RFC 1105 "ihlal edilemez API + genişletilebilir API" ayrımı; letsbuildsolutions pratik kuralı: "eski istemci yeni API'ye kod değiştirmeden çalışabiliyorsa non-breaking"; spfn contract tablosu iki yönü ayrı işler (request'te tolerans artarsa güvenli, response'ta kesinlik artarsa güvenli; **alan silme response'ta hep refuse**). **(d) SPA yol uyumu (kaynak 36-43):** frontend-routing migration rehberi her yolu `kept / renamed / retired` olarak envanterlemeyi **işin kendisi** ilan eder ("kod yalnız bunun uygulamasıdır"); emekliye ayrılan yola **edge'de 301** (client-side rewrite 200 döndürür → bot'lara yanlış sinyal), `location.search` unutulmamalı, **trailing slash açıkça normalize edilip iki biçimi de test etmeli**; `@spfn/core` contract örneği sürüm zinciri (her release snapshot'ı, boşluk = geçit gevşemesi) ve "operation added → pass, operation removed → usage check, path changed → refuse" tablosunu sunar; reactrouter "future flags ile major'ları sıkıcı yap" yaklaşımı riskli değişiklikleri bayrak arkasına alır; internetcomputer docs SPA catch-all `200 /*` ile gerçek 404 sayfasını ayırır (aşırı catch-all, eksik asset'e HTML döndürür — sınırlanmalı). |
| Web Search **Paragraf Veri Uzun** | Contract testing 2025-26: Pact consumer expectation tek yerde + provider verify + can-i-deploy kapısı (docs.pact.io, pact.io, pactflow, sachith 2025, Senacor); CDC eleştirisi = ayrı dağıtılan istemci için (dev.to, medium, leapcell); aynı repo içi kırıcıyı tip sistemi yakalar → tam CDC yerine golden liste yeterli. Golden/snapshot: repo'da beklenen çıktı, fark = insan onayı, CI'da otomatik overwrite yok (vitest, atlassian, testsigma, applitools, testland, wallarm, qaskills). Taksonomi: ekleme uyumlu / silme+rename+required daraltması kırıcı (Google Cloud, Zalando, IBM, adidas, RFC 1105); response'ta alan silme hep refuse (spfn); "eski istemci hâlâ çalışıyorsa non-breaking" (letsbuildsolutions). SPA: `keep/rename/retire` envanteri + redirect tablosu (frontend-routing), emekli yol = edge 301 (client 200 değil), trailing slash sözleşme ayrıntısı = iki biçimi de test et, arama kaybını bookmark/e-posta temsil eder; her release snapshot → geçit (spfn); major riski future flag arkasına (reactrouter); catch-all 200 sınırlı kalmalı (internetcomputer). |
| Web Search **Sonucu** | 1) **Contract'ın tek gerçekliği + CI kapısı doğrulandı** (kaynak 1-10 + 11-24, ≥2 çapraz): beklenti/çıktı repoda saklanır, değişiklik **insan onaylı** güncellenir, otomatik overwrite yok → **golden route listesi fikri literatürle birebir**; "aynı deploy içinde CDC ağır" hükmü kaynak 5-8'in çerçevesinden çıkarımdır → `⚠️ VERIFICATION REQUIRED`. 2) **Additive/breaking taksonomisi doğrulandı** (kaynak 25-35, ≥2 çapraz): silme/renaming/zorunluluk daraltması = breaking; ekleme = uyumlu → **spfn tablosuyla da örtüşür** (`operation added: pass`). 3) **Redirect politikası doğrulandı** (kaynak 36-39, ≥2 çapraz): geçici akış = kısa ömürlü (302), **kalıcı/retired yol = 301** ve client-side 200 yeterli değil → ADR-009 ile aynı yön. 4) **Trailing slash + case = sözleşme ayrıntısı** (kaynak 36, 39 + Google Cloud): açıkça normalize et ve **her iki biçimi de test et** → bizim `trim` davranışımızın testi golden fixture'a girer. 5) **URL/path değişimi = breaking** (kaynak 35, 30, 26): `/users/:id/posts → /users/:id/activity` örneği doğrudan sayfa yollarına tercüme edilebilir. **Toplam ~43 adlandırılmış kaynak, 4 sorgu**; sayfa-içi derin tur yapılmadığı için tarih/süre/kod örnekleri başlık/özet düzeyindedir (açıkça işaretli). |
| Web Search **Alınan Karar** | **ADR-021 KABUL EDİLİR — SPA ROUTER IMMUTABLE CONTRACT (4 parça + 2 koruma):** **(A) Path şeması:** kanonik yol = lowercase ASCII (`a-z0-9-` + `/`), Türkçe karakter/`_`/büyük harf anahtarda YASAK, trailing slash YOK (kanonik biçimde), boş yol → `home`, derinlik bugün ≤2 segment, query/fragment anahtar DEĞİLDİR (`?` sonrası atılır — `PageRouter.php:101-103`); case folding/percent-decode **runtime'ı ADR-016'nın işi** (bu ADR yalnız kanonik tanımı yazar). **(B) Parametreler:** `{ad}` sözdizimi (`RouteRegistry.php:78`), eşleşme `[^/]+` (`:79`) → parametre `/` içeremez, tip router'ın işi değil (handler), **girişte decode yok / redirect'te `rawurlencode` var asimetrisi** giderilir: decode handler'da, router'da değil; **parametre yeniden adlandırması = breaking.** **(C) 404/redirect:** bilinmeyen yol → **404 + ERROR alert** (404 IMPLEMENTED `PageRouter.php:39,89`; alert PLANNED); **oturum/auth yönlendirmeleri 302 KORUNUR** (`ResponseEmitter.php:80-89`, `PageRouterKernel.php:157-165`); **kalıcı canonical + emekliye ayrılan yol = 301 ZORUNLU** (kodda grep 0 → PLANNED, [[ADR-009-clean-url-redirect]] hizası; emekli yol halefine 301, client-side 200 kabul edilmez). **(D) Immutable tanımı:** breaking = rota silme, anahtar yeniden adlandırma, parametre yeniden adlandırma/tip değişimi, `requiresAuth` genişlemesi/daraltması, kanonik biçim (case/slash) değişimi → **yeni major + yeni ADR olmadan YASAK**; **additive (yeni rota ekleme, meta/title değişimi) serbest** ama golden fixture'a kayıt gerektirir. **Koruma 1:** `shared/tests/Fixtures/golden-routes.php` (PLANNED) — `loadFromFile` + `getRegisteredKeys()` (`RouteRegistry.php:56`) ile iki host için ayrı karşılaştırma testi; fark = CI kırmızı. **Koruma 2:** sözleşme ihlali = bu ADR'nin amendmanı = **yeni ADR** (metin silinmez). **Koruma 3:** runtime bilinmeyen yol → 404 + ERROR log (ADR-013 ruhu). |
| Web Search **Sonuç** | Karar 2025-2026 verisiyle **desteklendi**: contract testing (10 kaynak), golden/fixture/snapshot (14), breaking taksonomisi (11), SPA yol uyumluluğu (8) → **~43 adlandırılmış kaynak, 4 sorgu**; çapraz doğrulama ≥2 kaynak dört ana iddiada da karşılanır, iki çıkarım (`⚠️` tam CDC yerine golden liste yeterliliği, sayfa-içi derin tur eksikliği) açıkça işaretlendi. Kod tarafı aynı resmi verdi: **22 anahtar + Registry + 404 + 302 IMPLEMENTED**; **golden liste, CI kapısı, ERROR alert, 301, normalizer testleri PLANNED** → bu ADR **sözleşme, kod taahhüdü değil**; uygulaması §5.1 adımlarına bağlıdır. **Kaynak listesi (~43):** 1) docs.pact.io — Pact Documentation · 2) pact.io — best practice (expect/toParticularTest) · 3) pactflow.io — Getting Started with Pactflow · 4) sachith.co.uk — Consumer-Driven Contract Testing in 2025: From Theory to Production · 5) dev.to — Is Consumer-Driven Contract Testing Dead? · 6) medium.com — Consumer-Driven Contract Testing with Pact: A Complete Guide · 7) medium.com (Senacor) — Why Good Contracts Need Good Manners · 8) leapcell — Mastering Contract Testing · 9) prismatic.io — What is contract testing? · 10) sigmainst.com — The 2025 Guide to Contract Testing · 11) vitest.dev — Snapshot Tests · 12) testland.io — Golden Tests vs Snapshot Tests · 13) atlassian.com — Snapshot testing: examples and best practices · 14) testsigma — Understanding Snapshot Testing · 15) applitools — Complete Guide to Snapshot Testing · 16) wallarm.com — Golden File Testing · 17) strongdm — Snapshot vs golden image testing · 18) webym — Golden Testing · 19) swanlabs — Test Doubles & Test Data · 20) test-pact.io — Golden Files vs Golden Masters · 21) qaskills.sh — Golden test data in CI · 22) github (olshansk) — golden test data in CI · 23) medium.com — Techniques for Testing Data Pipelines (fuzzy & golden) · 24) github (atheory-ai) — Golden Master Pattern · 25) cloud.google.com/apis/design/compatibility — Backwards compatibility · 26) Zalando restful-api-guidelines — Non-Breaking Torture Techniques · 27) IBM api-handbook — Backwards Compatibility · 28) Siemens — API Development Best Practices (backwards compatibility) · 29) adidas / apisyouwonthate — RESTful API guidelines (backward compatibility MUST NOT be violated) · 30) rust-lang RFC 1105 — API evolution · 31) Zuplo — The Ultimate Guide to API Versioning Strategies (2026) · 32) apidog — API Versioning (breaking/non-breaking) · 33) New Relic — Breaking and nonbreaking API changes · 34) tvh-equipment — API Design Guidelines (semantic versioning) · 35) letsbuildsolutions.com — API Versioning Strategies: Breaking vs Non-Breaking Changes · 36) frontend-routing.com — Router Migration & Interop (keep/rename/retire, redirect table, trailing slash) · 37) @spfn/core — Route contracts and the backward-compatibility gate (pass/refuse tablosu, release snapshot zinciri) · 38) docs.internetcomputer.org — Routing and clean URLs (SPA catch-all 200, gerçek 404) · 39) frontend-routing.com — Hash Routing vs History Mode (cold load, rewrite) · 40) reactrouter.com — Releases (future flags ile major riski azaltma) · 41) reactrouter.com — Concepts (path pattern vs URL) · 42) web-accessibility-a11y.com — Accessibility Contract of SPA Route Changes · 43) reactrouter.com — v6 upgrade/changelog (normalization notları). |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| [[ADR-004-multi-domain-spa]] (çift router) | Sunucu PageRouter + istemci History API **aynı yol listesine** tabidir; bu ADR'nin golden fixture'ı iki tarafın da kaynağıdır — istemci tarafı fixture'ı kopyalamaz, **okur** |
| [[ADR-016-url-normalization]] (runtime normalizasyon) | Case folding, percent-decode, dot-segment temizliği **o ADR'nin PLANNED şartıdır**; bu ADR yalnız **kanonik biçimi** yazar, `resolve()`/`RequestNormalizer` davranışını değiştirmez |
| [[ADR-009-clean-url-redirect]] (301 canonical) | Kalıcı yönlendirme 301 hedefi o ADR'dedir; bu ADR **301/302 politika ayrımını** yazar, kod değişikliğini §5.1'e bağlar (bugün 301 grep 0) |
| [[ADR-013-rate-limiting-apcu]] + [[ADR-005-ultrathink-protocol]] | 404 ERROR alert olayı ADR-013'ün audit/log çerçevesine yazılır; doğrulanamayan her iddia `⚠️ VERIFICATION REQUIRED` (ADR-005) |
| [[ADR-002-pdo-mandatory-no-orm]] | Route listesi PHP config dosyasıdır; **router çözümlemesine DB sorgusu eklenemez** (golden fixture da saf PHP array'dir) |
| Domain boundary (`shared/AGENTS.md` §3-4) | `src/PageRouter/**` + `config/routes*.php` → Backend Architect; test `shared/tests/**` → QA Engineer; `config/domain.php` onaysız değişmez |
| In-Place Refactoring | Dosya adları (`routes.php`, `auth-routes.php`, `RouteRegistry.php`, `PageRouterKernel.php`, `ResponseEmitter.php`, test dosyaları) **onaysız değiştirilemez**; bu ADR yalnız sözleşme yazar |
| Frozen ADR-001-037 dokunulmaz | Yalnız okunur + referanslanır (`AGENTS.md` §25.3 kural 2) |
| `.ai/log.md` append-only | Bu işlem dahil tüm kayıtlar yalnız ekleme (bayt-seviyesi) |
| REDACTED | Bu ADR'ye secret/credential/token yazılmaz |
| Numara kuralı | "Yeni ADR ≥ 088" bu yazımda uygulanmaz: `ADR-021` `.ai/.decisions/index.md:58`'de **rezerve boş slottur** (doldurma, yeni numara tahsisi değil — ADR-019/020 aynı istisnayı kaydetmişti) |

---

## 2. Karar (Decision)

**CoreMusic SPA route yolu TEK sözleşmeye bağlanır ve immutabl ilan edilir: (A) Path şeması — kanonik yol lowercase ASCII (`a-z0-9-` + `/`), Türkçe karakter/`_`/büyük harf anahtarda yasak, trailing slash kanonik biçimde yok, boş yol `home`, derinlik bugün ≤2 segment, query/fragment anahtar değil; case folding/percent-decode runtime'ı ADR-016'nın işi. (B) Parametre kuralları — `{ad}` sözdizimi, eşleşme `[^/]+` ( `/` içeremez ), tip doğrulaması handler'ın, girişte percent-decode router'da yok (decode handler'da), parametre yeniden adlandırması = breaking. (C) 404/redirect — bilinmeyen yol 404 (+ ERROR alert PLANNED); oturum/auth yönlendirmeleri 302 KORUNUR (kodda zaten tek kural); kalıcı canonical ve emekliye ayrılan yollar için 301 ZORUNLU (bugün kodda yok → PLANNED, ADR-009 hizası), emekli yol halefine 301, client-side 200 kabul edilmez. (D) Immutable — rota silme / anahtar yeniden adlandırma / parametre yeniden adlandırma / `requiresAuth` değişimi / kanonik biçim değişimi = BREAKING ve yeni major + yeni ADR olmadan YASAK; yeni rota ekleme ve meta/title değişimi = ADDITIVE (serbest, fixture'a kayıt şartıyla). Koruma: (1) golden route listesi fixture'ı zorunlu CI testi — liste değişince test kırmızı; (2) sözleşme ihlali = bu ADR'nin amendmanı = yeni ADR (metin silinmez); (3) runtime bilinmeyen yol → 404 + ERROR alert (ADR-013/016 ruhu).**

### 2.1 Neden Bu Seçenek?

- **Üç taraf aynı listeye muhtaç:** sunucu 404 üretir, istemci sayfa açar, dış dünya link atar — liste tek kaynaktan (2 config) okunup fixture'a bağlanmadan üçü de ayrı yöne gider (§1.2/8, ADR-004).
- **Araştırma aynı sonuca vardı:** contract'ın tek gerçekliği + CI kapısı + insan onaylı güncelleme (§1.3 kaynak 1-24); **ayrı dağıtılan istemci yok** (sunucu+istemci aynı repo/deploy) → tam Pact CDC ağır, golden liste yeterli (⚠️ çıkarım işaretli).
- **Additive/breaking ayrımı literatürle birebir:** ekleme uyumlu, silme/renaming kırıcı (§1.3 kaynak 25-35; spfn `operation added: pass`) → ürün evrimi yavaşlamaz, **yalnız kırıcı değişiklik** durur.
- **301/302 ayrımı kod gerçekliğinden çıktı:** kodda 301 hiç yok, her şey 302'ye zorlanıyor; ADR-009 301 istiyor → kararı yazmak, kodu bu ADR'de değil §5.1 + ADR-009'da düzeltmek (yan etkisiz ayrım).
- **404 sessizliği ADR-013 ruhuyla kapanır:** kırık link bir güvenlik/güvenilirlik olayıdır; 404 + ERROR alert olmadan golden kapı yalnız "var olmayanı" değil, **kırılanı da** görünmez kılar.
- **Mevcut API yeterli:** `getRegisteredKeys()` (`RouteRegistry.php:56`) fixture için hazır — sıfır altyapı, tek test dosyası.

### 2.2 Teknik Detaylar

**a) Path şeması (bağlayıcı):**

| Kural | Değer | Kanıt / Dayanak |
|-------|-------|-----------------|
| Harf seti (anahtar) | lowercase `a-z`, rakam, `-`, `/` | 22 anahtarın tamamı böyle (`routes.php:24-123`, `auth-routes.php:12-62`) |
| Türkçe karakter | Anahtarda YASAK — ASCII fold (`kesfet`, `sanatcilar`) | `routes.php:88,106` |
| Büyük harf | Anahtar ve kanonik yolda YASAK | `RouteRegistry.php:43` (exact `isset`, `strtolower` yok) |
| Trailing slash | Kanonik biçimde YOK (`/home`, `/home/` aynı anahtara iner) | `RouteRegistry.php:37` + ADR-009 tek canonical |
| Boş yol (`/`) | `home` anahtarına düşer | `RouteRegistry.php:39-41` |
| Derinlik | Bugün ≤2 segment; derinlik artışı **additive** ama fixture güncellemesi + kayıt ister | `auth/callback`, `albumler/detay` (en derin) |
| Query / fragment | Anahtar DEĞİLDİR; `?` sonrası çözümlemeden önce atılır | `PageRouter.php:101-103` |
| Runtime case folding / percent-decode / dot-segment | **Bu ADR'de DEĞİŞMEZ** → ADR-016 PLANNED şartı | `RequestNormalizer.php:42,68,74` (path'e lowercase yok) |
| Host ayrımı | Aynı anahtar farklı host'ta farklı `SpaRoute` olabilir → **fixture host'a ayrı liste olarak yazılır** | `login`: `routes.php:26` (redirect) vs `auth-routes.php:13` (form) |

**b) Parametre kuralları (bağlayıcı):**

| Kural | Değer | Kanıt |
|-------|-------|-------|
| Sözdizimi | `{ad}` — `[a-zA-Z_][a-zA-Z0-9_]*` | `RouteRegistry.php:78` |
| Eşleşme | `[^/]+`, tam eşleşme `#^…$#` → parametre **`/` içeremez**, tam segment kaplar | `RouteRegistry.php:79-81` |
| Zorunluluk | Pattern segmentleri **her zaman zorunlu** (opsiyonel parametre yok) | `matchesPattern` yapısı |
| Tip | Router **doğrulamaz**; tip/zorunlu kontrolü handler'ın (`SpaRoute.handler`) | `SpaRoute.php:7-18` |
| Kodlama (giriş) | Router'da `rawurldecode` YOK → değer ham gelir; **decode handler'da** yapılır | `RequestNormalizer.php:42` (yalnız `FILTER_SANITIZE_URL`) |
| Kodlama (redirect) | Segment bazlı `rawurlencode` ZORUNLU (mevcut davranış korunur) | `PageRouterKernel.php:186-195` |
| `%2F` / encoded slash | **Desteklenmez** — `[^/]+` ve decode yokluğu nedeniyle değere gömülü slash semantiği tanımsız → sözleşmede yasak | `RouteRegistry.php:78-79` |
| Yeniden adlandırma (`{id}`→`{albumId}`) | **BREAKING** (§d) | dış bağlantılar + istemci router |

**c) 404 / redirect politikası (bağlayıcı):**

| Durum | HTTP | Karar | Kod durumu |
|-------|------|-------|------------|
| Bilinmeyen yol (`resolve()` null) | **404** | 404 + **ERROR alert** (audit/log) | 404 **IMPLEMENTED** (`PageRouter.php:39`, `RouteResult.php:30-37`); alert **PLANNED** |
| Sayfa dosyası yok (debug kapalı) | **404** | Aynı davranış | **IMPLEMENTED** (`PageRouter.php:84-90`) |
| 404 → HTML sarmalayıcı | 404 | ErrorHandler HTML (non-SPA) / JSON (SPA) | **IMPLEMENTED** (`PageRouterKernel.php:167-169`) |
| Oturum/auth yönlendirmesi (login, logout, korumalı yol) | **302** | **302 KORUNUR** (geçici akış) | **IMPLEMENTED** (`ResponseEmitter.php:80-89`, `PageRouterKernel.php:157-160`) |
| 403 + `redirect` (SPA) | **302** | 302 korunur | **IMPLEMENTED** (`PageRouterKernel.php:162-165`) |
| Canonical/normalizasyon (host/scheme/slash/uzantı) | **301** | **301 ZORUNLU** | **PLANNED** — `301` grep 0; [[ADR-009-clean-url-redirect]] |
| Emekliye ayrılan rota (taşıma/silme) | **301 → halef** | 301 + fixture'ta `retired` kaydı; **client-side 200 kabul edilmez** | **PLANNED** (§1.3 kaynak 36-39) |
| 5xx (istisna) | 500 | JSON `SERVER_INTERNAL_ERROR` + traceId; detay sunucu log'u | **IMPLEMENTED** (`PageRouterKernel.php:129-141`) |

**d) Breaking vs additive taksonomisi (bağlayıcı):**

| Değişiklik | Sınıf | Gerekçe / Kanıt |
|-----------|-------|-----------------|
| Yeni rota anahtarı ekleme | **ADDITIVE** (serbest, fixture'a kayıt şart) | `resolve` var olana dokunmaz (`RouteRegistry.php:43-51`); §1.3: `operation added → pass` |
| Rota silme | **BREAKING** | Aynı yol artık `null` → 404 (`PageRouter.php:39`) |
| Anahtar yeniden adlandırma | **BREAKING** | Dış link + istemci router kırılır |
| Parametre yeniden adlandırma / pattern değişimi | **BREAKING** | Eşleşme sözleşmesi (`RouteRegistry.php:78-79`) |
| `requiresAuth` false→true | **BREAKING** | `getProtectedRouteKeys` (`:61-70`) değişir → kullanıcı 403/redirect |
| `requiresAuth` true→false (yetki genişlemesi) | **BREAKING** (ayrı güvenlik onayı) | ADR-010/013 ruhu — sessiz genişleme yok |
| Kanonik biçim (case/slash) değişimi | **BREAKING** | ADR-009/016 zinciri |
| `meta` / `title` / `cacheable` değişimi | **ADDITIVE** | Dış URL sözleşmesi etkilenmez |
| `page` değeri değişimi (URL aynı) | **ADDITIVE** (iç) + fixture güncelleme | Template eşlemesi dahil fixture'da |
| 302 → 301 politika geçişi | ADR-009 kapsamında **kalıcılaştırma** | §c tablosu |

**e) Golden route listesi + CI kapısı (bağlayıcı — PLANNED):**

| Kalem | Karar |
|-------|-------|
| Dosya | `shared/tests/Fixtures/golden-routes.php` (**PLANNED — diskte YOK**, grep 0) |
| Yapı | **Host'a ayrı iki blok:** `home` (15 anahtar) + `auth` (7 anahtar); her kayıt: `key`, `requiresAuth`, `page`, `handler`, pattern (varsa) |
| Test | `GoldenRoutesTest`: `loadFromFile()` → `getRegisteredKeys()` (`RouteRegistry.php:56`) + `getProtectedRouteKeys()` (`:61-70`) ile **birebir** karşılaştırma → fark = test kırmızı |
| Eşleştirme | Eklenen anahtar: test "yeni kayıt eklendi" der → fixture güncellemesi **ADR referansı ile** yapılır; silinen anahtar: **kırık sözleşme** → amendman şart |
| Akış | route config değişikliği → CI kapısı kırmızı → ADR-021 amendmanı (yeni ADR) → fixture güncelle → yeşil |
| Sahiplik | Test: QA Engineer · Fixture/vault satırı: Vault Steward |
| Ek kapsam | `trim` trailing-slash eşitliği + boş yol `home` + bilinmeyen yol 404 assertion'ları da bu pakette |

**f) Runtime ihlal davranışı (bağlayıcı):**

```
resolve() null  → 404 (PageRouter:39/89)  +  ERROR alert log  [PLANNED — ADR-013 audit çerçevesi]
loadFromFile() → SpaRoute olmayan satır: sessiz skip (RouteRegistry:28-30)  →  hata log'u  [PLANNED]
istemci router → golden fixture'ı OKUR (kopyalamaz)  →  drift = CI kırmızı  [PLANNED — ADR-004]
```

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Belge-only sözleşme** (wiki sayfası, test yok) | Sıfır kod; hızlı | Değişiklikte hiçbir şey kırmızı olmaz; belge koddan kopar (§1.2/2'nin aynısı) | Bu ADR'nin doğuş sebebi tam da "yazılı olmayan sözleşme"; araştırma gate + fixture gerektiğini söylüyor (§1.3 kaynak 1-24) |
| 2 | **Tam consumer-driven contract (Pact + broker + can-i-deploy)** | Sektör standardı; ayrı istemciler için en güçlü kapı | Sunucu+istemci **aynı repo/deploy** → tip sistemi/CI zaten kırıcıyı yakalar; broker altyapısı + paket bağımlılığı + test süresi maliyeti | Aynı deploy içinde CDC gereksiz ağırlık (§1.3 kaynak 5-8'in çerçevesi — `⚠️` çıkarım işaretli); yalın `getRegisteredKeys()` fixture'ı aynı korumayı tek test dosyasıyla verir |
| 3 | **URL path versiyonlama** (`/v2/…` sayfa yolları için) | Kırıcı değişiklik için hazır kaçış kapısı | Public site SEO/bookmark URL'leri kirlenir; ADR-009 "tek canonical" ile çakışır; her major'da iki URL ağacı | Versiyonlama CoreMusic'te **API** içindir (`/api/v1/` — ADR-020 §F); sayfa yollarında kırıcı değişiklik zaten yasak + yeni ADR ile yapılır, paralel sürüm tutulmaz |
| 4 | **Uyumlu/hoşgörülü eşleşme** (case-folding + otomatik redirect her şey) | Kullanıcı hatası 404 olmaz | Sözleşme bulanıklaşır; hangi yolların "gerçek" olduğu belirsiz; ADR-016'nın işi bu ADR'ye taşınmış olur | Normalizasyon runtime'ı **ADR-016'nın PLANNED şartıdır** (tek sorumluluk); bu ADR yalnız kanonik tanımı + her iki biçimin testini yazar (§1.3: "trailing slash'ı normalize edip iki biçimi de test et") |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Sözleşme görünür ve denetlenebilir:** 22 anahtar, path/parametre/404/redirect kuralları tek dosyada; "neden böyle?" sorusunun cevabı gelecekte de okunur.
- **Kırık değişiklik yakalanır:** golden fixture + CI kapısı sayesinde rota silme/renaming **merge öncesi** kırmızı olur; araştırma gate + insan onaylı güncelleme akışıyla (§1.3) hatalı güncelleme de kapanır.
- **Evrimsel hız korunur:** additive ekleme serbest (literatürle aynı: `operation added → pass`) → sözleşme kilidi **yalnız kırıcıyı** durdurur.
- **301/302 netleşir:** geçici akış 302 (kodda zaten), kalıcı 301 (ADR-009 hedefi) — SEO/tarayıcı önbelleği yanılgısı için tek kural.
- **Çift router hizası (ADR-004):** istemci router'ı listeyi okur → sunucu/istemci drift'i CI'da görünür.

### 4.2 Olumsuz Sonuçlar

- **Her eklenen rota = 2 işlem** (config + fixture) → küçük bir sürtünme; bilinçli kabul (kapının bedeli).
- **Kilitli sözleşme yavaş evrim:** silme/renaming için artık yeni ADR süreci gerekiyor → bürokrasi; ama geri dönüş maliyeti (kırık dış link) bundan büyük.
- **PLANNED yükü:** fixture, test, 404 alert, 301, `SpaRoute` olmayan satır log'u = **5 kod işi** (§5.1); yapılmazsa ADR kâğıtta kalır.
- **Host ayrımı karmaşıklığı:** 6 ortak anahtar farklı anlam taşıdığı için fixture tek liste değil **iki blok** — yanlış birleştirilirse test yanlış yeşil verir.
- **Debate tamamlandı:** 3 tur / 20 persona → **18/2/0 KABUL** (§5.3) + **3 bağlayıcı şart** (§5.4); Tech Lead ✅ (2026-09-25), Arch Lead ⏳ → §7'nin Arch Lead satırı ✅ olmadan Active/Frozen olmaz (şablon §4.2).

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **Golden liste bakımı ihmal edilir** (fixtura "yeni rota eklendi" satırı unutulur, sonra her şey gözden geçirilir) | 3 (olası) | 3 (orta) | Eklenen anahtar ve silinen anahtar farklı hata mesajıyla ayrılır; ADR referansı PR'da zorunlu (§e) |
| **Yanlış pozitif kapı** (kasıtlı rota değişikliği testi kırar, ekip testi "geçsin" diye fixture'ı düşünmeden günceller) | 3 (olası) | 4 (yüksek) | Fixture değişimi ayrı commit + ADR satırı; silme = amendman şart (§e akış) |
| **Kilitli sözleşme yavaş evrim** (silme/renaming için ADR süreci → geçici çözüm arayışı → sözleşme dışı yollar) | 3 (olası) | 3 (orta) | Additive yol açık tutulur (§d); geçici yasak yol = yeni ADR (uzak değil) |
| **301 geçişi (ADR-009) bu ADR'den önce/sonra gelirse** davranış kargaşası (302 zorlama `:157-160` 301'i ezer) | 3 (olası) | 4 (yüksek) | §c'de 301/302 sahipliği yazılı; kod değişimi §5.1 adım 4'te ADR-009 ile **tek adımda** |
| **İstemci router drift'i** (ADR-004 — istemci tarafı fixture'ı okumazsa sunucu 404 / istemci sayfa açar) | 3 (olası) | 3 (orta) | İstemci tarafı testi aynı fixture'dan beslenir (§5.1 adım 5) |
| **Fixture iki host'u yanlış birleştirir** (login: redirect vs form) | 2 (mümkün) | 3 (orta) | Host'a ayrı iki blok zorunlu (§e); `login` çakışması fixture'da bilinçli iki satır |

### 4.4 Vault Çapraz Referans

| Kaynak | İlişki |
|--------|--------|
| [[ADR-004-multi-domain-spa]] | Çift router (sunucu + istemci) → bu ADR tek sözleşme + ortak fixture dayanağı |
| [[ADR-016-url-normalization]] | Runtime case folding/percent-decode **o ADR'nin PLANNED şartı**; bu ADR kanonik tanımı yazar (§1.4) |
| [[ADR-009-clean-url-redirect]] | 301 tek canonical hedefi → §c 301 ZORUNLU satırı bu ADR'de, kod o ADR'nin adım planında |
| [[ADR-013-rate-limiting-apcu]] | 404 ERROR alert olayı audit/log çerçevesine yazılır (§2.2f) |
| [[ADR-005-ultrathink-protocol]] | `⚠️ VERIFICATION REQUIRED` standardı — golden fixture 0, 301 grep 0, normalizer testi 0, ADR-083 dosyası yok (§1.1) |
| [[ADR-002-pdo-mandatory-no-orm]] | Route çözümlemesi config dosyasından; router'a DB sorgusu eklenemez (§1.4) |
| [[../index]] | Satır 58 `[[ADR-021-spa-router-immutable-contract]]` — slug eşleşmesi ✅ (bu dosya rezervasyonu doldurur) |
| [[../../index.md]] | Satır 638 `decisions/accepted/ADR-021-spa-router-immutable-contract` kaydı ✅ |
| [[../../keys.md]] | Satır 256 `ADR-021 \| SPA router, immutable contract \| Routing` ✅ |
| [[../../brain.md]] | Satır 976 `ADR-021 \| SPA router immutable contract` ✅ |
| [[../../.templates/adr/adr-index.md]] | Satır 92 `21 \| ADR-021 \| SPA router immutable contract \| 🔵 frontend \| adr-frontend-template.md` ✅ |
| [[../../glossary.md]] | Satır 393 ADR-021 referansı (router immutable contract) ✅ |
| [[../../.templates/adr/adr-frontend-template.md]] | Frontend ADR şablonu — bu ADR'nin kategorisiyle eşleşir (adr-index.md:92) |
| `shared/config/routes.php` | 15 anahtar `:24-123` (§1.1-A, §2.2a) |
| `shared/config/auth-routes.php` | 7 anahtar `:12-62` (§1.1-A, §2.2a host ayrımı) |
| `shared/src/PageRouter/RouteRegistry.php` | `:10-13` register · `:15-33` loadFromFile (+ sessiz skip `:28-30`) · `:35-54` resolve (`:43` case-sensitive, `:39-41` home) · `:56-59` getRegisteredKeys (fixture API) · `:61-70` protected · `:72-82` parametre |
| `shared/src/PageRouter/PageRouter.php` + `RouteResult.php` | `:39`,`:89` notFound · `:101-103` query atma · `RouteResult.php:30-37` 404 gövdesi |
| `shared/src/PageRouter/PageRouterKernel.php` | `:96` loadFromFile · `:157-165` 302 zorlama · `:167-173` 404/4xx ErrorHandler · `:129-141` 500 JSON · `:186-195` rawurlencode |
| `shared/src/PageRouter/ResponseEmitter.php` | `:80-89` redirect varsayılan 302 · `:23-26` 302 → redirect yolu · `301` grep 0 (§1.1-C) |
| `shared/src/PageRouter/RequestNormalizer.php` | `:42` FILTER_SANITIZE_URL · `:68,74` strtolower yalnız proto/HTTPS → path'te case fold yok (ADR-016 teyit) |
| `shared/src/PageRouter/SpaRoute.php` | `:7-18` readonly 10 alan (§1.1-B) |
| `shared/tests/Unit/PageRouter/` | 4 dosya / 21 test (`RouteRegistryTest:11,22,33,43,54` dahil) · normalizer testi 0 · golden test 0 |
| `home.coremusic.net/config/bootstrap.php` · `auth.coremusic.net/index.php` | `:85-88` · `:193` yükleyiciler (§1.1-A) |
| Düz metin (dosyalar diskte YOK — wiki-link KURULMAZ): ADR-083-spa-router | `../index` satır 105 (`../brain.md` girdisi → ADR-083-spa-router) — glob `ADR-083*` boş → **⚠️ VERIFICATION REQUIRED** (shared/AGENTS.md §2'de "ADR-021/083" olarak anılıyor) |
| [[../../CLAUDE.md]] | 16 Hard Guardrail, Guardrail #16, REDACTED |
| [[../../AGENTS.md]] | §6 routing (`routing, middleware, PHP → Backend Architect`), §5 domain boundary, §17.5/§17.7, §25.3 frozen |
| [[../../WORKFLOW.md]] | Debate/onay akışı başlangıcı |
| [[../../.templates/adr/adr-template.md]] | Bu ADR'nin şablonu (Guardrail #16, 7 bölüm + §1.3 9 alan) |
| [[CLAUDE.md]] | Karar alt registry kuralı (accepted/ dizin sözleşmesi) |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırması protokolü (diskte VAR ✅) |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **Golden fixture:** `shared/tests/Fixtures/golden-routes.php` — host'a ayrı iki blok (home 15 / auth 7), `key + requiresAuth + page + handler + pattern` alanlarıyla | QA Engineer + Backend Architect | 1 oturum |
| 2 | **CI kapısı testi:** `GoldenRoutesTest` — `loadFromFile` + `getRegisteredKeys()`/`getProtectedRouteKeys()` birebir karşılaştırma + eklenen/silinen anahtar için ayrı mesaj + `trim` eşitliği + boş yol `home` + bilinmeyen yol 404 assertion'ları | QA Engineer | 1.5 oturum |
| 3 | **404 ERROR alert:** `PageRouter.php:39` ve `:89` `notFound` çağrılarında bilinmeyen yol olayı → ADR-013 audit/log çerçevesine ERROR kaydı (şu an yalnız `error_log`/dosya log var) | Backend Architect + Security Engineer | 0.5 oturum |
| 4 | **301 politikası:** kalıcı canonical (host/scheme/slash/uzantı) + emekliye ayrılan yollar `301` ile — `PageRouterKernel.php:157-165` 302 zorlaması **yalnız geçici akışlarda** kalır; kod değişimi [[ADR-009-clean-url-redirect]] ile **tek adımda** | Backend Architect + Vault Steward | 1 oturum |
| 5 | **İstemci router hizası:** History API router'ı route listesini aynı fixture'dan okur/yaratır (kopya yok) → drift testi | UI Designer + QA Engineer | 1 oturum |
| 6 | **Config hata log'u:** `RouteRegistry.php:28-30` sessiz skip yerine (veya yanında) uyarı log'u → yazım hatası iz bırakır | Backend Architect | 0.5 oturum |
| 7 | **Normalizer testleri (ADR-016 hizası):** trailing slash, boş yol, query atma, case davranışı için test — bu ADR'nin §2.2a kurallarını doğrular | QA Engineer | 1 oturum |
| 8 | **Vault senkronu:** `.ai/.decisions/index.md:58` satırı bu dosya ile canlanır; debate tamamlanınca §5.3/§7.1 + frontmatter `debate` güncellenir; broken-link varsa `broken-links-report.md` append | Vault Steward | 0.5 oturum |
| 9 | **Debate:** 3 tur / 20 persona (ADR-004/008/010-020 formatı) → sonuç §5.3/§7.1'e + frontmatter `debate` alanına + `log.md` append | Vault Steward + adr-debate | 1 oturum |

### 5.2 Geri Dönüş Planı

1. **Karar metni (bu dosya):** karar değişirse **yeni ADR** yazılır (`ADR-088+` serisi), bu dosya `superseded by` bağlanır — metin silinmez (In-Place yasağı).
2. **Golden fixture (adım 1-2):** test tek commit ile devre dışı bırakılabilir → **koruma kalkar, sözleşme kalır** (metin §2'de); devre dışı bırakma kararı da `log.md` append + neden ile kaydedilir.
3. **404 alert (adım 3):** alert kapatılabilir (yalnız log kaybı; 404 davranışı değişmez) → anında geri dönüş.
4. **301 politikası (adım 4):** 301'e geçiş geri alınırsa davranış bugünkü 302'ye döner (**kayıp yok, yalnız ADR-009 hedefi ertelenir**) → anında geri dönüş; tersi (301 → 302'ye döndürme) kalıcı URL önbelleği kırabilir → yalnız yeni ADR.
5. **İstemci hizası (adım 5):** kopya/fallback yol yalnız okuma hatası üretir → test çıkarılır, istemci kendi listesine döner (drift riski geri gelir, §4.3 risk 5).
6. **Config log'u (adım 6):** log kapatılabilir (davranış değişmez) → anında geri dönüş.
7. **Kural ihlali / layer violation:** derhal revert + log ERROR (`AGENTS.md` §17.7); frozen ADR-001-037'e dokunulduysa `git checkout` (§17.10).
8. **Vault bozulması:** her adımdan sonra `.ai/log.md` append + `git diff --stat -- .ai/`; bozulma → `vault-utf8-writer.mjs repair` + `git checkout` (geçmiş satıra dokunulmaz).

### 5.3 Debate Kaydı

| Alan | Değer |
|------|-------|
| Durum | **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** — debate sonuçlandı, Tech Lead ✅ (2026-09-25) |
| Karar içeriği | Kullanıcı onaylı kapsam: **(a) path şeması (lowercase, trailing slash yok, segment kuralları — ADR-016 hizası) · (b) parametre kuralları (tip, zorunlu, kodlanmış) · (c) 404/redirect davranışı (301 vs 302 — ResponseEmitter bulgusuna bağlı) · (d) immutable = breaking değişiklik yeni major + yeni ADR olmadan yasak, additive rota ekleme serbest + golden fixture CI kapısı** |
| Biçim | ADR-004/008/010-020 formatı — 3 tur / 20 persona |
| Tur 1 (20 persona — kanıt paketi) | 15 home + 7 auth route key'i (`routes.php:26-118`, `auth-routes.php:13-55`), `RouteRegistry` case-sensitive `isset:43`, 404 `RouteResult:30-37`, `ResponseEmitter` force 302 (`:80-89`), `PageRouter`'da 301 = 0 kullanım, test 4 dosya/21 test, normalizer testi 0, golden fixture 0 → **15 kabul/neutral, 4 uyarı** (QA: golden fixture şart; Critic: 301/302 ayrımı yok; DevOps: CI gate mekanizması) |
| Tur 2 (İtiraz → çözüm) | (1) Golden fixture 0 → CI gate: route listesi fixture + zorunlu test → **şart 1** · (2) 301/302 belirsiz → sözleşme maddesi: kalıcı URL 301, oturum/refresh 302; `ResponseEmitter` 302 tekelleşmesi gözden geçirme → **şart 2** · (3) İhlal süreci → amendment = yeni ADR + major version (kararda teyit) · (4) 404 alert → bilinmeyen route ERROR log → **şart 3** |
| Tur 3 (Oy) | **18 kabul / 2 çekimser / 0 red → KABUL** |
| Bağlayıcı şartlar | **3 madde → §5.4** (golden fixture + CI gate · 301/302 politikası + ResponseEmitter gözden geçirme · 404 alert + normalizer testi) |
| Debate sonrası | Sonuç §5.3 tablosuna + §5.4 şartlara + §7.1'e + frontmatter `debate` alanına işlendi; `.ai/log.md` append ile kaydedildi |
| Kural | Debate sonuçlandı; Tech Lead ✅ verildi; Arch Lead ⏳ olduğundan §7'nin üç satırı henüz tam ✅ değil → Active/Frozen olmaz (şablon §4.2) |

### 5.4 Bağlayıcı Şartlar (Debate — 3 madde, KABUL koşulu)

| # | Şart | İçerik | Sahip | Bağlantı |
|---|------|--------|-------|----------|
| 1 | **Golden fixture + CI gate testi** | `shared/tests/Fixtures/golden-routes.php` route listesi fixture'ı **zorunlu** test ile (CI gate) — liste değişmeden test kırmızı; eklenen/silinen anahtar için ayrı mesaj | QA Engineer | §5.1 adım 1-2, §2.2e |
| 2 | **301/302 redirect politikası + `ResponseEmitter` gözden geçirme** | Sözleşme maddesi: **kalıcı URL = 301**, **oturum/refresh (geçici akış) = 302**; `ResponseEmitter.php:80-89` 302 tekelleşmesi gözden geçirilir, 302 zorlama (`PageRouterKernel.php:157-165`) yalnız geçici akışta kalır | Backend Architect + Vault Steward | §2.2c, §5.1 adım 4, [[ADR-009-clean-url-redirect]] |
| 3 | **404 runtime alert + normalizer testi** | Bilinmeyen route → **ERROR log** (ADR-013 audit çerçevesi); normalizer testleri ADR-016 paketiyle **ortak** yürütülür | Backend Architect + Security Engineer + QA Engineer | §2.2f, §5.1 adım 3 ve 7, [[ADR-013-rate-limiting-apcu]] · [[ADR-016-url-normalization]] |

**İhlal süreci (Tur 2 teyidi):** şart ihlali = bu ADR'nin **amendmanı = yeni ADR + major version** (metin silinmez — §5.2/1).

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[CLAUDE.md]] | Karar alt registry kuralı (accepted/ dizin sözleşmesi) |
| [[../index]] | Karar dizini — **satır 58** `[[ADR-021-spa-router-immutable-contract]]` (slug eşleşmesi ✅) |
| [[../../CLAUDE.md]] | Vault ana sözleşmesi — 16 Hard Guardrail, REDACTED, Guardrail #16 |
| [[../../AGENTS.md]] | Onay akışı §10, frozen kuralı §25.3, routing §6 (`routing, middleware → Backend Architect`), §5 domain boundary, §17.5/§17.7 edge case |
| [[../../WORKFLOW.md]] | Debate/onay akışı başlangıcı |
| [[../../brain.md]] | Satır 976 ADR-021 kaydı ✅ · SPA router mimari özeti |
| [[../../keys.md]] | Satır 256 `ADR-021 \| SPA router, immutable contract \| Routing` ✅ |
| [[../../index.md]] | Satır 638 ADR-021 kaydı ✅ |
| [[../../glossary.md]] | Satır 393 ADR-021 referansı ✅ · SPA/route/contract terimleri |
| [[../../log.md]] | Audit trail — bu işlem tek satır append |
| Debate sonucu | §5.3 — **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** (frontmatter `debate` ile aynı) · bağlayıcı şartlar **§5.4** (3 madde) |
| [[../../.templates/adr/adr-template.md]] | Bu ADR'nin şablonu (Guardrail #16, 7 bölüm + §1.3 9 alan) |
| [[../../.templates/adr/adr-index.md]] | Satır 92 `21 \| ADR-021 \| 🔵 frontend \| adr-frontend-template.md` ✅ |
| [[../../.templates/adr/adr-frontend-template.md]] | Frontend kategori şablonu (adr-index:92 eşlemesi) |
| [[ADR-004-multi-domain-spa]] · [[ADR-016-url-normalization]] | Çift router + normalizasyon (dosyalar diskte VAR ✅) |
| [[ADR-009-clean-url-redirect]] · [[ADR-013-rate-limiting-apcu]] | 301 canonical + audit/log çerçevesi (dosyalar diskte VAR ✅) |
| [[ADR-005-ultrathink-protocol]] · [[ADR-002-pdo-mandatory-no-orm]] | Kanıt standardı + route katmanında DB yasağı (dosyalar diskte VAR ✅) |
| Düz metin (dosyalar diskte YOK — wiki-link KURULMAZ): ADR-083-spa-router | `../index` satır 105 + `shared/AGENTS.md` §2 "ADR-021/083" — glob boş → **⚠️ VERIFICATION REQUIRED** |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı — "ADR-021'i sıfırdan yaz"; karar içeriği onaylı: path şeması · parametre kuralları · 404/redirect 301-302 · immutable + golden fixture CI kapısı) | 2026-09-25 | ✅ |
| Tech Lead | Debate 3/20 — **18/2/0 KABUL + 3 şart** (§5.4) | 2026-09-25 | ✅ |
| Arch Lead | ⏳ | ⏳ | ⏳ |

### 7.1 Debate Kaydına İlişkin Not

| Alan | Değer |
|------|-------|
| Biçim | ADR-004/008/010-020 formatı — 3 tur / 20 persona |
| Debate | **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** — sonuç §5.3'e + §5.4 şartlara + frontmatter `debate` alanına işlendi; `.ai/log.md` append ile kaydedildi |
| Bağlayıcı şartlar | **3 madde → §5.4:** (1) golden fixture + CI gate testi · (2) 301/302 redirect politikası + `ResponseEmitter` gözden geçirme · (3) 404 runtime alert + normalizer testi (ADR-016 paketiyle ortak) |
| Tech Lead | **✅** (2026-09-25 — debate sonrası) · Arch Lead ⏳ |
| Karar içeriği onayı | Kullanıcı onaylı: **(a) path şeması (lowercase, trailing slash yok, segment kuralları — ADR-016 hizası) · (b) parametre kuralları (tip, zorunlu, kodlanmış) · (c) 404/redirect (301 vs 302 — ResponseEmitter bulgusu) · (d) immutable = breaking yeni major + yeni ADR olmadan yasak; additive ekleme serbest; golden fixture CI kapısı** |
| Kanıt durumu | §1.1: 22 anahtar + Registry + 404 + 302 zorlama + 21 test **IMPLEMENTED**; golden fixture/CI kapısı/404 ERROR alert/301/normalizer testleri **PLANNED**; `⚠️ VERIFICATION REQUIRED`: golden fixture grep 0, `301` grep 0, normalizer testi 0, ADR-083 dosyası yok |
| Frozen | debate ⏳ + Tech Lead ⏳ + Arch Lead ⏳ → §7'nin üç satırı da ✅ olmadan Active/Frozen olmaz (şablon §4.2); **frozen YOK** |
| Kural | Debate sonuçlandığında §5.3/§7.1'e ve frontmatter `debate` alanına işlenir, `.ai/log.md` append ile kaydedilir |

---

*ADR-021 v1.0.0 — CoreMusic Architecture Decision Record*
*Authority: ADR-021 Karar Metni (SSOT) · Mode: Red Team · Human Mode · Truth Mode*
*Last Updated: 2026-09-25*

*ADR-021 debate | 2026-09-25 | ✅ TAMAMLANDI (3 tur/20 persona, 18/2/0 KABUL + 3 şart §5.4) · Tech Lead ✅ · Arch Lead ⏳ · frozen YOK*
