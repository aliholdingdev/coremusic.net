---
title: "CoreMusic — R-003: jQuery UI Framework (REDDEDİLDİ — Framework yasağı)"
type: "architecture-decision"
category: "frontend"
date: "2026-10-02"
updated: "2026-10-02"
version: "1.0.0"
status: "rejected"
authority: "SSOT — R-003 red kararı: CoreMusic frontend yüzeyine jQuery ve jQuery UI GIRMEZ. Gerekçe: ADR-001 Vanilla JS + ITCSS, framework yasağı (diskte) + ADR-004:83/160 framework bağımlılığı gerekçe ailesi + ADR-012 strict-CSP gadget yüzeyi ('Framework yasağı' — index.md:128). Yerini alan: ADR-001-vanilla-js-itcss (Vanilla JS ES6+ + ITCSS 9 katman + BEM). Bu dosya salt-okunur seridir (rejected/) — değiştirilmez, yalnız referanslanır."
governance: "Red Team · Human Mode · Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 RED DOĞRULANDI)"
---

# CoreMusic — R-003: jQuery UI Framework (Rejected)

> **Durum:** rejected (**debate ✅ TAMAMLANDI**) — **Tarih:** 2026-10-02 — **Debate:** ✅ **TAMAMLANDI** (3 tur / 20 persona — 19 kabul / 1 çekimser / 0 red → **RED DOĞRULANDI**, §5.3) — **Tech Lead:** ✅ — **Arch Lead:** ⏳
> **Seri:** `.ai/.decisions/rejected/` (salt-okunur) — **Slug:** `R-003-jquery-ui-framework` (dizin otoritesi: [[../index]] **satır 128** — dosya adı ile birebir hizalı ✅, 2026-10-02 glob doğrulaması: `rejected/` içinde `R-003*` = **0 dosyaydı** → bu işlemde yazıldı)
> **Dizin satırı:** `| [[R-003-jquery-ui-framework]] <!-- dead-link: R-003-jquery-ui-framework no source 2026-09-24 --> | jQuery | Framework yasağı |` — `<!-- dead-link ... -->` bayrağı **bu işlemde DOKUNULMADI** (düzeltme son sıfırlamaya ertelendi → §5.1/4 + §7.1/1)
> **İlgili kararlar:** [[../accepted/ADR-001-vanilla-js-itcss]] (yerini alan — Vanilla JS + ITCSS framework yasağı; `:32` bu red'i "R-003 jQuery-UI" diye saysa da dosya o tarihte YOKTU) · [[../accepted/ADR-004-multi-domain-spa]] (`:83` "React/Vue/jQuery YASAK", `:160` gerekçe ailesi) · [[../accepted/ADR-012-csp-nonce-strict-dynamic]] (eski jQuery gadget riski) · [[../accepted/ADR-045-multi-domain-view-mode-architecture]] (`:130` "jQuery yok") — karar dizini [[../index]] §5.
> **R-001/R-002 dersi uygulandı:** wiki-link slug'ları **tahmin edilmedi** — her hedef disk glob/grep ile doğrulandı (ADR-001/004/012/045 + brain.md + R-001/R-002 = diskte VAR).

---

## 1. Bağlam (Context)

CoreMusic frontend katmanı (A3 / K10-K11) ADR-001 ile **saf Vanilla JS ES6+ + ITCSS + BEM**'e bağlanmıştır; karar dizini bu seçeneği çoktan reddetmiştir (`index.md:128` — "jQuery | Framework yasağı") ama **red metni hiç yazılmamıştır**: elde yalnız dizin satırı + `<!-- dead-link ... -->` notu + ADR-001/004/045 içindeki parmak izleri vardır. Bu dosya, o satırın **gerekçeli red kaydıdır** — yeni bir karar değil, mevcut red'in (a) gerekçe, (b) güncel web araştırması, (c) yerini alan eşleme, (d) yeniden değerlendirme koşulu ile sıfırdan yazımıdır.

### 1.1 Mevcut Durum (disk kanıtı — dürüst etiket, 2026-10-02 taraması)

| # | İddia | Kanıt | Etiket |
|---|-------|-------|--------|
| 1 | Red kaydı var mı? | [[../index]] `:128` → `[[R-003-jquery-ui-framework]]` + "jQuery" + "Framework yasağı" + `<!-- dead-link ... no source 2026-09-24 -->` | ✅ **KAYITLI** (dizin satırı tek kanıt; red metni bu işlemde yazılıyor) |
| 2 | Bu işlem öncesi dosya var mıydı? | `.ai/.decisions/rejected/` içinde `CLAUDE.md`, `index.md`, `R-001-*`, `R-002-*` ; `R-003*` = **0 dosya** | ❌ **YOKTU** → bu işlemde yazılıyor (klasör zaten var — `rejected/` oluşturulmadı) |
| 3 | Red gerekçesi başka yerde yazılı mı? | ADR-001 `:32` ("Reddedilmiş hazır seçenekler dizini … R-003 jQuery-UI") · ADR-004 `:83` ("React/Vue/jQuery YASAK") · ADR-004 `:160` (framework bağımlılığı ret satırı — R-001 ile aynı gerekçe ailesi) · ADR-045 `:130` ("jQuery yok") | ✅ **4 referans** — ama hiçbiri red'in kendi metni değil, **gerekçe ailesi** |
| 4 | Yerini alan karar diskte? | [[../accepted/ADR-001-vanilla-js-itcss]] **VAR** (accepted/ glob; `status: accepted`, debate 18/2/0) | ✅ **IMPLEMENTED** |
| 5 | Framework yasağı ailesi diskte? | [[../accepted/ADR-004-multi-domain-spa]] **VAR** (`:83` "React/Vue/jQuery YASAK") · [[../accepted/ADR-045-multi-domain-view-mode-architecture]] **VAR** (`:130` jQuery yok) | ✅ **IMPLEMENTED** |
| 6 | CSP gadget bağı diskte? | [[../accepted/ADR-012-csp-nonce-strict-dynamic]] **VAR** (`:60` — "eski jQuery/Prototype/Handlebars gadget'ları nonce'lu script'ten çalışır") | ✅ **IMPLEMENTED** |
| 7 | Kod yüzeyinde jQuery izi? | `*.json/*.php/*.js/*.mjs/*.cjs` taraması (5 `composer.json` dahil: shared, api, auth, media, home) `jquery` → **0 isabet** · `*.md` taraması yalnız **dokümantasyon referansları** (`github.com/jqueryscript/awesome-claude-code` = README/index URL'i — bağımlılık DEĞİL; skill/kural dosyalarında "yasak" listeleri) | ✅ **0 bağımlılık** (jQuery hiç girmedi — red bir "kaldırma" değil, **girişi engelleme** kararıdır; sürüm/CVE notu **yok** çünkü yüzey boş) |
| 8 | Kullanım yüzeyi (mockup) | `.ai/ui-design/**` taraması `jquery\|datepicker\|accordion\|sortable` → **0 isabet**; `slider`/`dialog` isabetleri **yerel BEM bileşenleri** (C09 `.slider`, `role="dialog"` — jQuery UI widget'ı değil) | ✅ **0 jQuery UI bileşeni izi** |
| 9 | `rejected/index.md` durumu? | Dosya **VAR** (v1.0.1, `total: 12`) ama § tablosu (`:18-19` başlık satırları) **BOŞ** — 12 red'in hiçbiri satırlanmamış | ⚠️ **BOŞ** → bu işlemde **dokunulmadı** → §7.1/2 |
| 10 | Debate sonucu? | Bu işlem debate **içermez** — talimat: `⏳ PENDING` | ✅ **RED DOĞRULANDI** (debate 2026-10-02 — 3 tur / 20 persona, 19/1/0 — bkz. §5.3) |

> **Ders notu:** bu red **hiçbir zaman kodda denenmedi** — jQuery kod yüzeyinde 0 (§1.1/7), mockup'ta 0 jQuery UI izi (§1.1/8); "reddedildi" = "yazılmadı ve yazılmasına izin verilmedi". ADR-001 `:32` bu red'i "mevcut" gibi saysa da (2026-09-24) dosya o tarihte yazılmamıştı — parmak izi, metnin yerine geçmez.

### 1.2 Sorun Tanımı

1. **Red kararı kanıtsız duruyor.** `index.md:128` bir sonuç cümlesi ("Framework yasağı") ama **ne 2025-26 ekosistem kanıtı (bakım durumu/CVE/native alternatifler) ne yeniden değerlendirme koşulu ne yerini alan eşleme** yazılı — gelecekteki biri "jQuery neden yok, bugün de mi yok, ne zaman tekrar sorulur?" sorusuna vault'tan cevap bulamıyor.
2. **Gerekçe ailesi parçalı.** Uzun gerekçe ADR-001 `:32`, ADR-004 `:83/:160`, ADR-045 `:130` içinde dağınık; red'in kendi dosyası olmadığı için `grep R-003` sonucu "gerekçe = başka ADR'nin satırı" düzeyinde kalıyor.
3. **Güncellik sorunu.** jQuery ekosistemi 2025-26'da durmadı ve aynı anda **durdu**: jQuery UI 2021'den beri bakım modunda, jQuery 3.x yalnız kritik güvenlik yaması alıyor, native web API'leri (Popover, `<dialog>`, CSS anchor positioning) jQuery UI widget yüzeyinin bir kısmını emdi (§1.3). Red'in **bugün hâlâ doğru** olup olmadığı araştırılmadan yazılmıştı — "eski bilgiye dayanan karar" sınıfı (şablon §1.1 uyarısı).
4. **Koşul tanımsız.** "Hangi durumda jQuery tekrar gündeme gelir?" (vanilla olgunluk boşluğu mu doldurulamadı, yasağı kaldıran yeni ADR mi geldi) **hiç belgelenmedi** → yeniden değerlendirme tetikleyicisi tanımsız.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (öncelik: resmi doküman → vendor → bağımsız blog; her iddiaya kaynak). Araştırma 2026-10-02'de yapıldı — **5 sorgu**.

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "jQuery UI maintenance status 2025 2026 release active development" → (2) "jQuery CVE vulnerabilities 2024 2025 2026 security advisory" → (3) "vanilla JavaScript datepicker dialog accordion alternatives native browser APIs replace jQuery UI 2025" → (4) "CSS anchor positioning Popover API native web platform replacing jQuery UI 2025 2026 browser support" → (5) "jQuery UI CVE-2021-41182 CVE-2022-31160 XSS vulnerabilities versions affected" |
| Web Search **Konusu** | **(1)** jQuery UI'nin 2025-26 bakım/bakım-dışı durumu ve sürüm akışı · **(2)** jQuery çekirdek CVE'leri ve CISA KEV durumu · **(3)** jQuery UI widget'larının vanilla/native alternatifleri (datepicker, dialog, accordion, sortable) · **(4)** native web platform API'lerinin (Popover, `<dialog>`, CSS anchor positioning) jQuery UI yerine geçme olgunluğu · **(5)** jQuery UI'ye özgü bilinen XSS CVE'leri ve etkilenen sürümler |
| Web Search **Bağlam** | CoreMusic: ADR-001 (Vanilla JS + ITCSS, framework yasağı — diskte), ADR-004 `:83/:160` (React/Vue/jQuery yasak), ADR-012 (strict CSP — eski jQuery gadget riski), ADR-045 `:130` (jQuery yok); kodda jQuery **0** (§1.1/7), mockup'ta jQuery UI izi **0** (§1.1/8); hedef soru — *"Red bugün hâlâ doğru mu, native web 2026'da jQuery UI'yi gerçekten emdi mi, ters kanıt var mı?"* |
| Web Search **Kısa Açıklama** | **(1)** jQuery UI 2021'den beri **bakım modunda** (resmi blog) — 1.14.1/1.14.2 sürümleri yalnızca uyumluluk+güvenlik; jQuery support sayfası 1.x/2.x desteğini kesmiş, 3.x yalnız kritik güvenlik yaması diyor. **(2)** CVE-2020-11023 (XSS) Ocak 2025'te **CISA KEV** kataloğuna eklendi; jQuery GitHub'ında iki moderate XSS advisory açık. **(3)** Dialog → native `<dialog>`, datepicker → `input[type=date]`/küçük vanilla kütüphane, slider → native input; Drupal jQuery UI datepicker'i **deprecate** ediyor. **(4)** Popover API **Baseline 2025** (Ocak 2025'ten beri tüm büyük tarayıcılarda), CSS anchor positioning **Baseline 2026** (Chrome/Firefox/Safari). **(5)** jQuery UI'de 4 XSS: CVE-2021-41182/41183 (Datepicker), CVE-2021-41184 (`.position()`), CVE-2022-31160 (checkboxradio, 1.13.2'de düzeldi). |
| Web Search **Uzun Açıklama** | **(1) Bakım durumu:** jQuery UI blogu (2024/04 "Plans for jQuery UI 1.14") açıkça "jQuery UI has been in maintenance mode since 2021; security issues & regressions öncelikli, feature work yok" der; 1.14.2 sürüm notu (2026-01) aynı sınırı yineler ("compatible with new jQuery releases and security fixes"). jQuery support sayfası: "We support only the latest version; 1.x/2.x no longer supported; 3.x only critical security patches". jQuery maintainers 2021 duyurusu popülerlik iddiası taşır — "%73 of 10 million most popular websites" (**vendor iddiası, bağımsız doğrulama YOK**). 2026 r/webdev tartışması: jQuery "still around but maintenance mode; jQuery 4.0 still in development" (bağımsız, forum kaynağı). **(2) Çekirdek CVE:** herodevs — CISA Ocak 2025'te CVE-2020-11023'i (jQuery 1.0.3–3.4.1 DOM manipulation XSS, sanitization sonrası bile çalışabilir) KEV kataloğuna ekledi (son tarih 13 Şubat 2025); jquery/github security advisories iki moderate XSS yayınlar (GHSA-jpcq-cgw6-v4j6 — `<option>` içeriği append; GHSA-gxr4-xjj5-5px2 — `htmlPrefilter). **(3) Alternatifler:** StackOverflow "vanilla JS alternative for jQuery UI Dialog" → native `<dialog>`; WPPoland migration guide → slider/datepicker/autocomplete native input veya küçük vanilla kütüphanesiyle değiştirilir; Drupal.org issue #3072906 jQuery UI datepicker'i deprecate eder ("browsers that support native datepickers are unaffected"); mymth vanilla JS datepicker bağımsız bir alternatif sunar; Temporal API doğmuş ama henüz tam yaygınlaşmadı (polyfill önerilir — olgunluk işareti). **(4) Native API olgunluğu:** MDN — Popover API **Baseline 2025** (Ocak 2025'ten beri Chrome/Edge/Firefox/Safari'de); CSS anchor positioning Baseline 2026'ya ulaştı (Chrome, Firefox, Safari) ve tooltip/menu konumlandırmayı native çözer; web.dev anchor positioning dokümanı `anchor()` + fallback deseni öğretir; erken yazılar (oidaisdes) cross-browser desteğini henüz tam bulurken 2026 kaynakları (mintec, kvassiliou, full-net) tam destek der → **zamanla olgunlaştı**. **(5) jQuery UI CVE'leri:** NVD CVE-2022-31160 (checkboxradio XSS, CVSS 6.1, 1.13.2'de düzeldi); HeroDevs CVE-2021-41182 (<1.13.0, Datepicker altField XSS); Informatica CVE-2021-41182/41183/41184 + 2022-31160 dörtlüsü (Datepicker text options, `.position()` of option); Drupal güvenlik advisory'si jQuery UI 1.13.0 XSS'ini "moderately critical" sınıflar. |
| Web Search **Paragraf Veri Uzun** | 5 sorgu: **(1)** blog.jqueryui.com "maintenance mode since 2021" (2024/04 plans) · blog.jqueryui.com 1.14.2 release (2026-01) · blog.jquery.com modernization (2021-10, %73 vendor iddiası) · jquery.com/support (1.x/2.x unsupported, 3.x critical-only) · safeguard.sh (1.14.x = maintenance, feature work planned değil) · r/webdev "Is jQuery still a thing in 2026" — **6**. **(2)** herodevs CVE-2020-11023 + CISA KEV (2025-01-23, deadline 2025-02-13) · github.com/jquery/jquery security advisories (GHSA-jpcq-cgw6-v4j6, GHSA-gxr4-xjj5-5px2) · tuxcare jQuery vulnerabilities 2026 · opencve jQuery CVE listesi · drupal.org security (jQuery UI XSS) — **5**. **(3)** stackoverflow vanilla dialog (native `<dialog>`) · reddit r/javascript AskJS vanilla datepicker · drupal.org #3072906 (datepicker deprecation) · wppoland jQuery vs vanilla migration guide · mymth.github.io vanillajs-datepicker · reddit Temporal API — **6**. **(4)** MDN Popover API (Baseline 2025) · web.dev learn/css/anchor-positioning · mintec CSS Anchor Positioning 2026 (Baseline, 3 tarayıcı) · kvassiliou (Popover + anchor 2026) · full-net.cz (Baseline 2026 — JS tooltip kütüphanelerinin sonu) · oidaisdes (erken dönem cross-browser uyarısı) — **6**. **(5)** nvd.nist.gov CVE-2022-31160 (CVSS 6.1) · herodevs CVE-2021-41182 · sentinalone CVE-2022-31160 · informatica CVE dörtlüsü · broadcom CVE listesi · drupal security advisory — **6**. **Toplam ~29 benzersiz kaynak.** |
| Web Search **Sonucu** | **(1) Red destekleniyor:** bakım modu = yeni widget/iyileştirme yok; güvenlik yaması bile sınırlı (jQuery 3.x critical-only, 1.x/2.x unsupported) → üretim bağımlılığı olarak ömrü belirsiz (kaynak: blog.jqueryui ×2, jquery.com/support, safeguard — 4). **(2) Red destekleniyor:** çekirdek XSS KEV'e girmiş (CVE-2020-11023, CISA 2025-01) + iki açık moderate advisory → CVE açık yüzeyi gerçek (kaynak: herodevs, github advisories, tuxcare, opencve — 4). **(3) Kısmen karşıt bulgu (dürüst):** vanilla/native karşılıkları **vardır ama olgunluk boşluğuyla** — datepicker customization'ı ve sortable/draggable native'de tam yok; Temporal API henüz yaygınlaşmadı → "jQuery UI artık gereksiz" iddiası **her widget için eşit doğru değil** (kaynak: stackoverflow, reddit ×2, wppoland, drupal — 6). **(4) Red destekleniyor ama zamanla:** Popover API Baseline 2025, CSS anchor positioning Baseline 2026 — dialog/tooltip/menu/positioning native'e taşındı; datepicker/slider native input ile kısmen karşılanıyor (kaynak: MDN, web.dev, mintec, full-net, kvassiliou — 5). **(5) Red destekleniyor:** jQuery UI'nin kendi widget'larında 4 XSS (Datepicker ×2, `.position()`, checkboxradio) — reddedilen yüzeyin **doğrudan CVE geçmişi** (kaynak: NVD, HeroDevs, Informatica, SentinelOne, Drupal — 6). **İtiraz/karşıt bulgu (dürüst):** (i) %73 popülerlik **vendor iddiası** (jQuery maintainers 2021) → bağımsız doğrulama yok, ⚠️ işaretli; (ii) jQuery 4.0 geliştirme aşamasında (forum kaynağı — resmi yol haritası **UNKNOWN**) → "ölümü kesin" denmez; (iii) vanilla olgunluk boşluğu (datepicker/sortable) **gerçek bir bedel** → §4.2'ye yazıldı, örtbas edilmedi; (iv) CoreMusic'te jQuery **zaten 0** (§1.1/7-8) → red'in pratik etkisi "önleme", "kurtarma" değil. |
| Web Search **Alınan Karar** | **Red (R-003) YÜRÜRLÜKTE KALIR.** (a) jQuery, jQuery UI ve eşdeğer **UI framework/kütüphane katmanı** (Prototype, MooTools, Ext JS, Kendo UI vb. — "uygulama iskeleti olmayan UI kütüphaneleri" dahil) CoreMusic frontend yüzeyine **GİRMEZ** — ADR-001 framework yasağı + ADR-004 `:83` React/Vue/jQuery yasak + ADR-012 strict CSP gadget yüzeyi ile doğrudan çelişir; "Framework yasağı" gerekçesi 2026 verisiyle de geçerli (bakım modu + CVE geçmişi + native API olgunlaşması). (b) Frontend yüzeyi **Vanilla JS ES6+ + ITCSS + BEM** ile sürer: dialog → native `<dialog>`/yerel modal sınıfı, position → CSS anchor positioning (Baseline 2026) + fallback, tooltip/menu → Popover API (Baseline 2025), tarih → `input[type=date]` (+ ihtiyaç kanıtı yazılırsa bağımsız vanilla kütüphane, ADR-001 "paket serbesttir" sınırı içinde değerlendirilir), slider → yerel C09 `.slider` (zaten var). (c) **Yeniden değerlendirme koşulu** (§2.3) yazılmadan bu red **otomatik olarak güncellenmez**; vendor popülerlik iddiası (%73) tek başına koşul sayılmaz. |
| Web Search **Sonuç** | **5/5 araştırmada red desteklendi** (1 bakım durumu + 2 CVE + 3 alternatif + 4 native API + 5 jQuery UI CVE'leri); 3. sorgu **kısmen karşıt** (vanilla olgunluk boşluğu) dürüstçe §4.2'ye yazıldı. **Dört açık işaretlendi:** (i) %73 popülerlik vendor kaynaklı → bağlayıcı yapılmadı (yalnız bağlam) · (ii) jQuery 4.0 yol haritası forum kaynağı → resmi durum **UNKNOWN** · (iii) vanilla olgunluk boşluğu gerçek bedel → §4.3 risk satırı · (iv) kodda 0 → red "önleme" kararı, "kaldırma" değil (§1.1/7). **Kaynak sayısı: 5 sorgu; §1.3'te adı geçen benzersiz kaynak ~29.** |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-001 accepted (framework yasağı) | Vanilla JS ES6+ + ITCSS 9 katman + BEM; "paket serbesttir, iskelet değildir" sınırı (ADR-001 §2) — jQuery UI bir **UI kütüphane katmanı** olarak hem iskelet sınırını hem CSP gadget disiplinini zorlar; yasağı kaldırmak **yeni ADR** ister, bu dosya düzenlenmez (AGENTS.md §25.3 kural 2) |
| ADR-004 framework bağımlılığı | `:83` "React/Vue/jQuery YASAK", `:160` "bundle şişirir; framework çalışma zamanı CSP/TrustedTypes disiplinini kırar" — jQuery UI bu gerekçe ailesinin UI-kütüphane ayağıdır |
| ADR-012 strict CSP | `:60` — eski jQuery/Prototype/Handlebars **gadget**'ları nonce'lu `strict-dynamic` yükleyicisinden çalışabilir; jQuery UI eklentisi gadget yüzeyini büyütür |
| Kod yüzeyi gerçeği | jQuery = **0 bağımlılık / 0 isabet** (5 composer.json + tüm JS/PHP — §1.1/7); mockup'ta jQuery UI izi **0** (§1.1/8) → red bir "kaldırma" değil, **girişi engelleme** kararıdır — geri dönüş planı kod tarafında işlem gerektirmez (§5.2) |
| Vanilla olgunluk boşluğu | datepicker/sortable/draggable native'de tam karşılanmaz (§1.3-3) → eşik uydurulmaz; ihtiyaç kanıtı yazılırsa ADR-001'in "kütüphane serbesttir" sınırı içinde **bağımsız vanilla** değerlendirilir, jQuery UI değil |

---

## 2. Karar (Decision)

**R-003 REDDEDİLMİŞTİR: jQuery, jQuery UI ve eşdeğer UI framework/kütüphane katmanı (Prototype, MooTools, Ext JS, Kendo UI, Dojo gibi) CoreMusic frontend yüzeyine alınmaz.** Karar `index.md:128`'de bugünden vardı; bu dosya onu gerekçelendirir: red, ADR-001'in Vanilla JS/ITCSS framework yasağının + ADR-004'ün framework-bağımlılığı gerekçesinin + ADR-012 strict-CSP gadget disiplininin **UI kütüphane ayağıdır** ve 2026-10-02 web araştırması (§1.3) red'in **bugün hâlâ doğru olduğunu** doğrulamıştır — jQuery UI'nin kendisinden değil, bakım modu + CVE geçmişi + native API olgunlaşması + CoreMusic'in sıfır-bağımlılık durumundan dolayı.

### 2.1 Neden Bu Seçenek?

1. **İlke tutarlılığı (kanıtlı):** ADR-001 framework yasağını kurar; ADR-004 `:83` aynı yasağı React/Vue/**jQuery** diye yazar; ADR-001 `:32` bu red'i dizinde hazır seçenek olarak sayar — ayrı bir karar değil, **aynı ilkenin UI-kütüphane ayağı**.
2. **Bakım modu = üretim riski (kanıtlı):** jQuery UI 2021'den beri bakım modunda (resmi blog), 1.14.x yalnız uyumluluk+güvenlik; jQuery 3.x yalnız kritik yama, 1.x/2.x unsupported (§1.3-1) → canlı bağımlılık olarak **sürdürme garantisi zayıf**.
3. **CVE açık yüzeyi (kanıtlı):** çekirdek CVE-2020-11023 CISA KEV'e girdi (2025-01); jQuery UI'nin kendi widget'larında 4 XSS (Datepicker ×2, `.position()`, checkboxradio — §1.3-5) → reddedilen yüzeyin doğrudan güvenlik geçmişi var.
4. **Native API emme (kanıtlı):** `<dialog>`, Popover API (Baseline 2025), CSS anchor positioning (Baseline 2026), `input[type=date]` — jQuery UI'nin en çok kullanılan position/dialog/tooltip işleri 2026'da platformda (§1.3-4) → "olgunluk" bahanesi her yıl zayıflıyor.
5. **İhtiyaç kanıtı yok (kanıtlı):** kodda `jquery*` = **0** (5 composer.json dahil), mockup'ta jQuery UI izi **0** (§1.1/7-8); "jQuery UI lazım" şikayeti vault'ta belgeli değil (UNKNOWN) → boş yüzeye bağımlılık girmez.

### 2.2 Teknik Detaylar

- **Yasak yüzeyi:** `jquery`, `jquery-ui`, `jquery-ui-dist`, `@jquery/jquery-ui`, `jqueryui` (npm) · `components/jquery*` (Bower ölü) · CDN `<script src=".../jquery...">` · `Prototype.js`, `MooTools`, `Ext JS`, `Kendo UI`, `Dojo`, `Underscore` (iskelet/yardımcı katman olarak) — yani **UI framework/kütüphane katmanı** (ADR-001 "iskelet olamaz" sınırının UI ayağı). jQuery UI **widget**'ları (datepicker, dialog, accordion, sortable, slider, tooltip, autocomplete) ayrıca yasaktır — karşılığı yoksa **yerel BEM bileşeni** yazılır (mevcut emsal: C09 `.slider`, yerel modal `role="dialog"`).
- **İzinli yüzey (yerini alan uygulama):** (a) Vanilla JS ES6+ modülleri + ITCSS 9 katman + BEM (ADR-001); (b) native API'ler: `<dialog>`, Popover API, CSS anchor positioning, `input[type=date]`, IntersectionObserver, Fetch (ADR-004 History API router `:160`); (c) Composer paketleri backend'de serbesttir (ADR-001 sınırı — frontend runtime'ı beslemez); (d) frontend'e npm runtime paketi eklemek ADR-001 revizyonu ister (ADR-001 §2 "frontend paket yüzeyi").
- **Kod yüzeyi ölçümü (2026-10-02):** `jquery` → `*.json/*.php/*.js/*.mjs/*.cjs` = **0 isabet** (5 composer.json dahil); yalnız `.md` dokümantasyonunda referanslar (URL'ler, yasak listeleri) → **sürüm/CVE notu YOK** çünkü yüzey boş (§1.1/7).
- **Mockup yüzeyi ölçümü (2026-10-02):** `.ai/ui-design/**` `jquery|datepicker|accordion|sortable` = **0**; slider/dialog = yerel BEM/ARIA (§1.1/8) → jQuery UI widget'ı hiç kullanılmadı.

### 2.3 Yeniden Değerlendirme Koşulu (şart satırı)

> **Bu red yalnız aşağıdaki koşullardan BİRİ yazılırsa yeniden değerlendirilir; aksi hâlde yürürlükte kalır:** (1) **ADR-001 framework yasağı yeni bir ADR ile değiştirilirse** (ADR-001 metni düzenlenmez — yeni ADR `superseded by` ile bağlar) veya yasağa **açık istisna** getirilirse; (2) **ölçülebilir bir iş ihtiyacı** UI Designer + Security Engineer raporuyla belgelenirse — Vanilla JS + native API'lerin (ADR-001/012 kapsamı içinde) karşılayamadığı, ölçülmüş tek bir gereksinim (ör. tarih/seçim widget'ında native `input[type=date]`'in WCAG 2.2 AA + 45-tier cihaz matrisinde karşılayamadığı kanıtlanmış bir durum) yazılmadan "jQuery UI gerekli" iddiası kurulamaz; (3) **debate tamamlanıp red'i kuran koşullar değişirse** (§5.3 — debate ✅ TAMAMLANDI, sonuç RED DOĞRULANDI) yeni debate + yeni ADR ile yeniden açılır. **Bugün: 1 = SAĞLANMADI (ADR-001 diskte, yürürlükte), 2 = ÖLÇÜLMEDİ (ihtiyaç kanıtı 0 — §1.1/7-8), 3 = SAĞLANMADI (debate 2026-10-02'de tamamlandı, sonuç RED DOĞRULANDI — koşul "sonuç değişirse" sağlanmadı) → red geçerli.**

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **jQuery + jQuery UI (tüm UI widget yüzeyi: dialog, datepicker, accordion, sortable, slider, tooltip)** | geniş tarayıcı uyumu, bol dokümantasyon, eski ekip bilgisi | **bakım modu 2021'den beri** (§1.3-1); jQuery UI'da 4 XSS + çekirdek CVE KEV (§1.3-2/5); CDN bağımlılığı + bundle şişmesi; ADR-012 gadget yüzeyi (`:60`) | **Framework yasağı** (dizin `:128` gerekçesi) + ADR-001/004 `:83` ihlali; kodda ihtiyaç kanıtı **0** (§1.1/7) |
| 2 | **"Saf vanilla JS ile yazılmış tekil UI kütüphanesi" (ör. vanillajs-datepicker, benzeri bağımsız widget paketleri)** | framework değil, tek widget; ADR-001 "paket serbesttir" sınırına girer | her paket ayrı bağımlılık/tedarik-zinciri girişi (ADR-001 §1.3 verisi); CoreMusic'te **0 kullanım kanıtı**; kalite/A11y denetimi paket başına tekrar gerekir | **Şu an gereksiz** — native `input[type=date]`, `<dialog>`, C09 `.slider` yeterli görünüyor; ihtiyaç kanıtı yazılırsa (§2.3/2) bu yol, jQuery UI yolu **öncelikle** değerlendirilir |
| 3 | **React/Vue (tam UI framework)** | olgun ekosistem, deklaratif bileşen | ADR-001'in **doğrudan** yasakladığı iskelet; hydration/bundle + npm tedarik zinciri (ADR-001 §1.3) | **Aynı gerekçe ailesi** — ADR-004 `:160` bu alternatifi de reddetmiştir; R-001 (Redux) ile kardeş red; bu red o ailenin **UI-kütüphane** ayağıdır |
| 4 | **Hiçbir UI katmanı — her bileşen sıfırdan el yazması (UI framework'süz mutlak saf JS)** | maksimum kontrol, minör bağımlılık | **zaten mevcut durum budur** (ADR-001 uygulaması); olgunluk boşluğu (datepicker/sortable) elle kapatılır → maliyet §4.2'de yazılı | **Reddedilmedi — bu, yerini alan yaklaşımdır** (§2.2); boşluklar native API + yerel BEM ile doldurulur, jQuery UI ile değil |

*(Kabul edilen uygulama alternatifi — Vanilla JS + ITCSS + BEM + native API — §3'te "reddedilmedi" olarak ayrılmadı; o, §2.2'deki **yerini alan** yaklaşımdır ve ADR-001/004/012/045 kapsamındadır.)*

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Sıfır frontend runtime bağımlılığı korunur:** jQuery/jQuery UI = 0 paket, 0 CDN, 0 sürüm takibi, 0 CVE taraması (§1.1/7) — tedarik-zinciri yüzeyi ADR-001 verisiyle (npm %99.8 malware) uyumlu kalır.
- **Red gerekçesi artık izlenebilir:** `grep R-003` → bu dosya; ADR-001 `:32`, ADR-004 `:83/:160`, ADR-045 `:130` ile gerekçe ailesi tek yerde toplandı.
- **Güncelleme yapıldı:** red 2026 web verisiyle (5 sorgu, ~29 kaynak) yeniden sınandı — bakım modu + KEV CVE + Baseline native API'ler karara **lehine** çalıştı; ters kanıt (vanilla olgunluk boşluğu, %73 popülerlik) dürüstçe yazıldı ama red'i değiştirmedi.
- **CSP disiplini bozulmadı:** ADR-012 strict nonce + TrustedTypes yüzeyine eski-kütüphane gadget'ı girmemiş olur (§1.4).
- **Mockup-kod tutarlılığı:** ui-design'da yerel BEM bileşenleri (C09 slider, yerel modal) zaten jQuery UI'siz kurulmuş — red, mevcut uygulamayla **tutarlıdır**, tersine dönüş gerektirmez.

### 4.2 Olumsuz Sonuçlar

- **Vanilla olgunluk boşluğu elle doldurulur:** datepicker (kısmi native `input[type=date]` — customization sınırlı), sortable/draggable (native'de yok → yerel sınıf gerekir), autocomplete (datalist + yerel sınıf) → yazım ve bakım maliyeti projede kalır (§1.3-3 dürüst karşıt bulgu).
- **Ekip bilgisinden feragat:** jQuery bilgisi yaygın; yeni ekip/klon projede "neden jQuery yok" açıklaması gerekir → bu dosya + §2.3 satırı yanıtı üretir.
- **Bağımsız vanilla widget izlemesi:** ihtiyaç doğarsa bağımsız kütüphanelerin (vanillajs-datepicker vb.) kalite/A11y/tedarik denetimi ayrı yapılır — hazır widget havuzu yoktur.
- **Vendor momentum'u izlenir:** jQuery 4.0 ve popülerlik iddiası (⚠️ vendor) takip yükü doğurur → §2.3 kapısı tutulur.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| Gizli jQuery girişi ("küçük etiket" adıyla CDN script veya npm paketi) | 2 (mümkün) | 4 (yüksek — ADR-001/004/012 ihlali) | §2.2 yasak listesi + `jquery*` composer/npm/HTML taraması (**0** korunur — §5.1/5) |
| Vanilla olgunluk boşluğu → bileşen kalitesi/A11y düşüşü (datepicker/sortable) | 3 (olası) | 3 (orta) | WCAG 2.2 AA + 45-tier mockup gate (AGENTS.md §13); ihtiyaç kanıtı §2.3/2 ile ölçülür — eşik uydurulmaz |
| jQuery UI CVE sınıflandırması (eski sürüm kullanan 3. taraf demo/asset) | 2 (mümkün) | 3 (orta) | kod yüzeyi 0 (§1.1/7); `assets.coremusic.net` 3. taraf asset taraması §5.1/5'e eklendi |
| "jQuery hâlâ yaygın, kullanalım" dış baskısı (yeni ekip) | 3 (olası) | 2 (düşük) | Bu dosya + §1.3 (bakım modu/KEV/Baseline) + §2.3 satırı — yanıt vault'ta yazılı |
| %73 popülerlik / jQuery 4.0 iddialarının red'i zayıflatır sanılması | 2 (mümkün) | 2 (düşük) | §1.3 işaretli: karar **ADR-001 yasağı + güvenlik/bakım gerekçesine** dayanır, popülerliğe değil |

---

## 5. Uygulama (Implementation)

> Bu kayıt **salt-okunur seri**dir (`rejected/`); "uygulama" = kaydın vault'a doğru yerleştirilmesi ve denetimidir — kod değişiği **yoktur**.

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | Bu dosyayı `.ai/.decisions/rejected/R-003-jquery-ui-framework.md` olarak yaz (şablon §1-§7, 7 bölüm dolu) + `log.md` append ("R-003 yazıldı (debate PENDING)") | Vault Steward | 25 dk |
| 2 | **Debate ✅ TAMAMLANDI** (3 tur / 20 persona, 19/1/0 **RED DOĞRULANDI** — §5.3) + Tech Lead onayı ✅ (2026-10-02) | MO + Tech Lead | 2026-10-02 |
| 3 | **`index.md:128` `<!-- dead-link ... -->` bayrağı → DOKUNULMADI** (talimat gereği son sıfırlamaya ertelendi — rapor-only) · `rejected/index.md` § tablosu **BOŞ → DOKUNULMADI** (rapor-only) | Vault Steward | son sıfırlama |
| 4 | Periyodik denetim: `jquery*` composer/npm/HTML CDN taraması = **0** korunur + `.ai/ui-design/**` jQuery UI izi = **0** izlenir (§2.2 yasak listesi) | UI Designer + QA Engineer | her sprint |
| 5 | Vanilla olgunluk boşluğu ölçümü (datepicker/sortable ihtiyacı var mı, native yeter mi) → §2.3/2 koşulu için kanıt üretir (rakam ölçülmeden yazılmaz) | UI Designer | ihtiyaç anında |

### 5.2 Geri Dönüş Planı

Bu karar **kod tarafında geri alınacak bir şey üretmedi** (jQuery hiç girmedi → `git revert` edilecek değişiklik **0**; composer/npm'de jquery paketi yok, §1.1/7; mockup'ta jQuery UI widget'ı yok, §1.1/8). Geri dönüş = **yeniden değerlendirme** demektir ve yalnız §2.3 koşullarından biri yazılırsa yeni ADR ile açılır: (1) ADR-001 yasağı değişirse → bu dosya `superseded` notuyla **bağlanır, düzenlenmez**; (2) ölçülmüş iş ihtiyacı → UI Designer + Security Engineer kanıtıyla yeni ADR; (3) debate sonucu değişirse → debate kaydı + yeni ADR. Vault bozulursa standart kurtarma `git checkout` + son commit (AGENTS.md §17 #10). **Reddedilen teknolojinin kodda izi olmadığı için kullanıcı/veri etkisi YOKTUR.**

### 5.3 Debate Şartları

**Kayıt:** ✅ **TAMAMLANDI** — 3 tur / 20 persona · sonuç **19 kabul / 1 çekimser / 0 red → RED DOĞRULANDI** (2026-10-02) · akış: Tur 1 kanıt taraması (20 persona) → Tur 2 itiraz→çözüm → Tur 3 oylama.

**Gerçekleşen debate (R-001/R-002 deseni):**

- **Tur 1 (20 persona — bulgu):** jQuery kod izi **0** (5 composer.json + tüm `*.php/*.js/*.mjs/*.cjs/*.json`; sürüm/CVE notu yok — yüzey boş, dürüst etiket) · mockup yüzeyi `.ai/ui-design/**` `jquery|datepicker|accordion|sortable` = **0** (slider/dialog izleri yerel BEM/ARIA — C09 slider, `role=dialog`) · red kaynağı `index.md:128` (dead-link **dokunulmadı** → §5.1/3) · yerini alan [[../accepted/ADR-001-vanilla-js-itcss]] glob doğrulandı + ADR-004 `:83/:160`, ADR-012 `:60`, ADR-045 `:130` (Test-Path) · 5 sorgu / ~29 benzersiz kaynak: bakım modu 2021+, KEV CVE-2020-11023, 4 jQuery UI XSS, Popover API Baseline 2025, anchor positioning Baseline 2026 · **5/5 red destekli** · 3. sorgu kısmen karşıt (vanilla olgunluk boşluğu) dürüst · 4 açık işaretli (%73 vendor rakamı, jQuery 4.0 UNKNOWN, olgunluk boşluğu, "0 = önleme" etiketi) · `rejected/index.md` boş → dokunulmadı · **9/9 wiki-link diskte**. Oy: **17 kabul/neutral + 3 uyarı** (A11y: a11y denetim şart; Critic: 0-etiketi + jQuery 4.0 şart).
- **Tur 2 (itiraz → çözüm → şart):** (i) "0 = önleme" belirsizliği → neden-sonuç netleştirildi (yasak uygulanmış → 0) → **şart 1**; (ii) jQuery 4.0 UNKNOWN → izleme kapısı (sürüm çıkarsa VERIFICATION REQUIRED güncellenir) → **şart 2**; (iii) vanilla olgunluk boşluğu + ARIA → yerel bileşen a11y denetimi şartı → **şart 3**.
- **Tur 3 (oy):** **19 kabul / 1 çekimser / 0 red → RED DOĞRULANDI.**

**Bağlayıcı 3 şart (Tur 2 çıktısı — §5.3 maddesi + §6 satırı olarak eklendi):**

1. **Şart 1 — "0 = önleme" netleştirmesi:** "kodda jQuery 0" iddiası yalnızca neden-sonuç bağlamıyla yazılır: ADR-001/004 yasağı uygulanmış → 0 isabet = **önleme** (kaldırma değil); "0 = güvenlik kanıtı" ya da "0 = otomatik geçerli" diye yorumlanamaz (§1.1/7, §4.2).
2. **Şart 2 — jQuery 4.0 izleme kapısı:** jQuery 4.0 yol haritası **UNKNOWN** (forum kaynağı — §1.3, §7.1/7); resmî sürüm yayımlanırsa §1.3 + §2.3 **VERIFICATION REQUIRED** etiketiyle güncellenir ve debate yeniden açılır.
3. **Şart 3 — Yerel bileşen a11y denetimi:** vanilla olgunluk boşluğunun (datepicker/sortable — §4.2) yerel BEM bileşenleriyle kapatılması hâlinde bileşenler **WCAG 2.2 AA a11y denetimi** (AGENTS.md §13 mockup gate + A11y denetimi) olmadan yayına alınmaz; denetim sonucu bu dosyada saklanır.

> **Debate sonucu:** ✅ **RED DOĞRULANDI** (3 tur / 20 persona · 19/1/0) · **Tech Lead:** ✅ (2026-10-02) · **Arch Lead:** ⏳.

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../index]] | Karar dizini — bu red'in kaydı (`:128`, slug + "Framework yasağı" + dead-link bayrağı §5.1/3) |
| [[../accepted/ADR-001-vanilla-js-itcss]] | **Yerini alan (birincil):** Vanilla JS + ITCSS + BEM framework yasağı; `:32` bu red'i "R-003 jQuery-UI" diye sayar (parmak izi) |
| [[../accepted/ADR-004-multi-domain-spa]] | Gerekçe ailesi — `:83` "React/Vue/jQuery YASAK", `:160` framework bağımlılığı ret satırı |
| [[../accepted/ADR-012-csp-nonce-strict-dynamic]] | CSP gadget yüzeyi — `:60` eski jQuery gadget'ları riski (§2.2) |
| [[../accepted/ADR-045-multi-domain-view-mode-architecture]] | `:130` "jQuery yok — VirtualScroller yerel sınıf" (yerel BEM bileşen emsali) |
| [[../../raw/brain]] | Mimari karar özeti (frontend/framework satırı) |
| [[R-001-redux-style-state-management]] | Seri kardeşi — aynı salt-okunur red kayıt formatı (bu dosyanın format referansı); framework-bağımlılığı gerekçe ailesi |
| [[R-002-mongodb-document-store]] | Seri kardeşi — aynı salt-okunur red kayıt formatı (kanıt tablosu/dürüst etiket deseni) |
| Dizin satırı | `index.md:128` — slug otoritesi + dead-link bayrağı (§5.1/3) |
| Debate şartları | Bu dosya **§5.3** — debate ✅ **TAMAMLANDI** (3 tur / 20 persona, 19/1/0 **RED DOĞRULANDI**) + **3 bağlayıcı şart** (1) 0-etiketi netleştirmesi · (2) jQuery 4.0 izleme kapısı · (3) a11y denetimi |
| Düz metin | Eski seri R-004…R-012 (`rejected/index.md` tablosu boş → §7.1/2) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali | 2026-10-02 | ✅ |
| Tech Lead | — | 2026-10-02 | ✅ |
| Arch Lead | — | — | ⏳ |

### 7.1 Rapor Notları (bu işlemde dokunulmayanlar)

1. **Dead-link bayrağı:** `index.md:128` `<!-- dead-link: R-003-jquery-ui-framework no source 2026-09-24 -->` **olduğu gibi bırakıldı** — artık `no source` iddiası **geçersizdir** (bu dosya kaynaktır) → temizlik son sıfırlamaya ertelendi, burada rapor edildi.
2. **`rejected/index.md` durumu:** dosya **VAR** (`rejected/index.md`, v1.0.1, `total: 12`) ama tablo **BOŞ** (`:18-19` başlık satırları, kayıt satırı yok) → bu işlemde **oluşturulmadı ve doldurulmadı** (dizin satırı düzenleme yetkisi kapsam dışı) → rapor-only.
3. **Slug hizası:** dosya adı `R-003-jquery-ui-framework` = `index.md:128` slug **birebir** ✅ (R-001/R-002 dersi: tahmin yok, `rejected/` glob'u ile doğrulandı — bu işlem öncesi `R-003*` = 0 dosya).
4. **Debate:** ✅ **TAMAMLANDI** (2026-10-02 — 3 tur / 20 persona, 19 kabul / 1 çekimser / 0 red → **RED DOĞRULANDI**, §5.3) · **Tech Lead:** ✅ (2026-10-02) · **Arch Lead:** ⏳ (değiştirilmedi).
5. **Kod yüzeyi kanıtı:** `jquery` = **0 isabet** (`*.json/*.php/*.js/*.mjs/*.cjs`, 5 composer.json dahil — §1.1/7); `.md` içindeki isabetler dokümantasyon (URL/yasak listesi) → sürüm/CVE notu **yok** (yüzey boş). Mockup: `jquery|datepicker|accordion|sortable` = **0** (§1.1/8).
6. **Şablon yolu notu:** görev `templates/adr/adr-template.md` der; disk kanıtı `.ai/.templates/adr/adr-template.md`'dir (glob) — bu dosya o şablonun §1-§7 iskeletiyle (§1.3 9 alan, §7 Onay) yazıldı.
7. **Vendor iddiası:** "%73 of 10 million most popular websites" **blog.jquery.com kaynaklıdır** (2021), bağımsız doğrulanmadı → §1.3'te ⚠️ işaretli, bağlayıcı yapılmadı. jQuery 4.0 yol haritası **UNKNOWN** (forum kaynağı).

---

*R-003 v1.0.0 | 2026-10-02 | Created — CoreMusic Vault (.decisions/rejected/ sıfırdan yazım, salt-okunur seri)*
*Authority: R-003 Red Karar Metni · Mode: Red Team · Human Mode · Truth Mode*
