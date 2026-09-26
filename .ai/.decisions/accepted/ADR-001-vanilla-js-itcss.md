---
title: "CoreMusic — ADR-001: Vanilla JS + ITCSS, Framework Yasak"
type: adr
category: frontend
date: 2026-09-24
updated: 2026-09-24
version: 1.0.0
status: accepted
authority: ADR-001 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — ADR-001: Vanilla JS + ITCSS, Framework Yasak

**Durum:** accepted (kabul — frozen YOK; okunur + yazılabilir)
**Tarih:** 2026-09-24
**Karar Veren:** Vault Steward (kullanıcı onaylı) · 20 persona debate: 18 kabul / 2 çekimser / 0 red
**İlgili ADR'ler:** bu dosya yeni seri ADR-001'dir; eski seri kararları kavramsal olarak karar dizininde → [[../index]]

---

## 1. Bağlam (Context)

CoreMusic frontend katmanı (assets.coremusic.net, K10-K11 / A3) için uygulama iskeleti düzeyinde bir teknoloji yasağı kararı alınması gerekmektedir. Karar; bundle şişmesi, hydration maliyeti, saldırı yüzeyi (npm tedarik zinciri), Core Web Vitals (LCP/INP) ve WCAG 2.2 AA uyumluluğu olmak üzere beş bağımsız baskıyı aynı anda karşılamalıdır. Ayrıca CSS organizasyonu (ITCSS) ve sınıf adlandırma (BEM namespace) frontend mimarisinin parçası olarak bu kararla birlikte sabitlenir.

### 1.1 Mevcut Durum

- Frontend hedefi: Vanilla JS ES6+ + ITCSS + BEM — [[../../AGENTS.md]] §4 (UI Designer satırı) ve §16 kalite standardı ("%100 ITCSS uyum, BEM namespace, WCAG 2.2 AA").
- CSS disk kanıtı: `assets.coremusic.net/Css/**` altında **8 katman dizini** (01_Abstracts … 08_Devices) + `main.css` + `auth-bundled.css` — kanıt: [[../../.templates/frontend/css-template]] §3.1.
- Şartname kanıtı: [[../../architecture/k11-ux/itcss-9-layer]] **9 katman** tanımlar (1-settings … 9-trumps). Spec 9 / disk 8 farkı → `⚠️ VERIFICATION REQUIRED` (ayrıntı §2.2).
- Backend'de benzer yasağın emsali zaten yürürlükte: ORM yasağı + PDO zorunluluğu (eski seri kararları → [[../index]] §3, kavramsal referans).
- Reddedilmiş hazır seçenekler dizini mevcut: [[../index]] §5 (R-001 Redux-state, R-003 jQuery-UI, R-004 Webpack — hepsi "framework/over-engineering" gerekçeli).

### 1.2 Sorun Tanımı

Framework kullanmak mı, saf Vanilla JS + ITCSS ile mi devam etmek? Sorun üç eksenli: (1) **Güvenlik** — npm tedarik zinciri saldırıları 2025'te endüstriyel ölçeğe ulaştı ve özellikle JS/React ekosistemi hedef alındı; (2) **Performans** — hydration ve framework runtime'ı, Core Web Vitals eşiklerini (LCP ≤2.5s, INP ≤200ms) tehdit eden JS yükü üretir; (3) **Sürdürülebilirlik** — uygulama iskeleti bağımlılığı, kütüphane bağımlılığından farklı (daha pahalı geri döndürülen) bir karardır. Karar bu üç ekseni birlikte çözmeli ve istisna koşullarını yazılı olarak tanımlamalıdır.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: prompt-maker web araştırma protokolü (`.claude/skills/prompt-maker/references/10-web-research-protocol.md`) — resmi spesifikasyon önce, her iddiaya kaynak, 2+ bağımsız çapraz doğrulama.

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "npm supply chain attack dependencies framework risk 2025 2026 statistics" · (2) "Core Web Vitals 2026 LCP INP thresholds web.dev" · (3) "vanilla JavaScript vs React performance bundle size hydration cost study" · (4) "WCAG 2.2 AA new success criteria list W3C recommendation" · (5) "ITCSS Inverted Triangle CSS Harry Roberts layers" · (6) "BEM block element modifier naming" |
| Web Search **Konusu** | Framework yasağının üç gerekçesi: tedarik-zinciri saldırı yüzeyi, hydration/bundle performans maliyeti, WCAG 2.2 AA erişilebilirlik yükümlülüğü + ITCSS/BEM metodolojisinin resmi kaynağı. |
| Web Search **Bağlam** | 2025-2026 güncel ekosistem verisi arandı: Sonatype/Veracode/Safeguard 2025 malware raporları, web.dev Core Web Vitals eşikleri (2026'da hâlâ LCP 2.5s / INP 200ms / CLS 0.1), W3C WCAG 2.2 Recommendation (05.10.2023, 9 yeni SC), arXiv hydration maliyeti çalışması (2025), ITCSS (Harry Roberts) ve BEM (getbem.com) birincil metodoloji kaynakları. |
| Web Search **Kısa Açıklama** | 2025'te npm, tüm açık-kaynak malware'inin %99.8'ini tek başına üretti ve saldırılar özellikle JS/React framework ekosistemini hedef aldı; buna karşılık saf Vanilla JS sayfalar hydration runtime'ı olmadan Core Web Vitals eşiklerini çok daha kolay tutturur; WCAG 2.2 AA ise test edilebilir 4 yeni AA kriteri getirir. |
| Web Search **Uzun Açıklama** | Tedarik zinciri: Sonatype Q4 2025 endeksi tek çeyrekte 394.877 yeni malware paketi saydı (önceki 3 çeyreğin toplamının %476'sı) ve paketlerin %99.8'inin npm'den geldiğini, saldırıların "JavaScript ve React ekosistemindeki popüler framework'leri özellikle hedeflediğini" açıkça bildirdi. Veracode 2025 incelemesi, OWASP Top 10 2025 RC1'de **A03:2025 Supply Chain Failures** kalemine respondentlerin yarısında #1 derecesi verildiğini ve kritik malware paketlerinin %86.8 arttığını (206.632 paket) kaydetti; Shai-Hulud solucanı 500+ paketi günler içinde ele geçirdi. Performans: arXiv 2504.03884 (2025), React hydration'ın ana thread'i blokladığını, monolitik hydration'ın FID'i uzattığını ve statik içerik hydrate etmenin "boş iş" olduğunu belgeler — aynı çalışma modüler/partial hydration ile JS transferini ~%82 (590KB → 105KB) azaltabilmiştir. Bir vaka çalışması (johal.in, 2026): React 19 landing page 142KB gzip → Vanilla JS 28KB gzip (%80 düşüş), hydration ek 94KB yük. Core Web Vitals: web.dev eşikleri 2026'da değişmedi — LCP ≤2.5s, INP ≤200ms, CLS ≤0.1 (p75). Erişilebilirlik: W3C WCAG 2.2 (05.10.2023), 2.1'e göre 9 yeni kriter; AA seviyesinde yeniler: 2.4.11 Focus Not Obscured (Min), 2.5.7 Dragging Movements, 2.5.8 Target Size (Min) (24×24), 3.3.8 Accessible Authentication (Min); 4.1.1 Parsing kaldırıldı. Metodoloji: ITCSS Harry Roberts 7 katmanlı orijinal + tema/utility katmanları eklenerek 9 katmana genişletilebilir; BEM (getbem.com) `.block__elem--mod` namespace sözleşmesi. |
| Web Search **Paragraf Veri Uzun** | Rakamlar: 394.877 malware paketi (Q4 2025, Sonatype) · %99.8 npm payı · %476 çeyrek-artışı · 206.632 kritik malware paketi / +%86.8 (Veracode 2025) · OWASP 2025 RC1 A03 tedarik zinciri → respondentların %50'sinde #1 · 500+ paket (Shai-Hulud worm) · React runtime ~80KB gzip çekirdek (react+react-dom, vaka çalışması) · 142KB → 28KB gzip (%80 azalma) · hydration overhead 94KB · JS transferi -%82 (590KB→105KB, arXiv modüler hydration) · LCP ≤2.500ms · INP ≤200ms · CLS ≤0.1 (web.dev, p75) · WCAG 2.2 = 9 yeni SC (4'ü AA) · ITCSS orijinal 7 katman → CoreMusic spec 9 katman · BEM: `__` element, `--` modifier. |
| Web Search **Sonucu** | 1) **Saldırı yüzeyi:** framework = npm bağımlılık ağacı = tedarik-zinciri girişi; 2025 verisi bu riski teorik olmaktan çıkardı (kaynak: Sonatype, Veracode, Safeguard). 2) **Performans:** hydration ana thread maliyeti ve runtime bundle'ı LCP/INP için yapısal dezavantajdır; saf JS bu yükü hiç üretmez (kaynak: arXiv 2504.03884, johal.in vakası, web.dev). 3) **Erişilebilirlik:** framework seçimi WCAG 2.2 AA yükümlülüğünü kaldırmaz; manuel test yine zorunludur (kaynak: W3C, WAI). 4) **ITCSS+BEM:** 9 katman + namespace, framework'süz ölçeklenebilirliği sağlayan kanıtlanmış metodolojidir (kaynak: freecodecamp/ITCSS özeti, getbem.com). 5) Çapraz doğrulama: her iddia ≥2 bağımsız kaynakla örtüştü. |
| Web Search **Alınan Karar** | Framework yasağı + Vanilla JS ES6+ zorunluluğu + ITCSS 9 katman + BEM namespace. Sınır maddesi: **Composer paketleri serbesttir** (kütüphane olabilir, uygulama iskeleti olamaz) — npm-front-end paket ağacı minimize edilir, PHP tarafı Composer ile güvenli/ölçümlü kalır. A11y şartı: Vitest + Playwright dev tooling ile otomatik test + manuel a11y testi zorunlu. Dev tooling şartı: source maps üretim ortamında kapalı/gizli. Zaruri fallback: §4.4. |
| Web Search **Sonuç** | Karar 2026 verisiyle **desteklendi**: tedarik-zinciri (3 kaynak), performans (3 kaynak), erişilebilirlik (2 kaynak), metodoloji (2 kaynak) — toplam **11 kaynak**, 6 sorgu, çapraz doğrulama tam. Kaynaksız iddia yok; tek belirsizlik vault içi spec/disk katman farkı (`⚠️ VERIFICATION REQUIRED`, §2.2). |

**Kaynak listesi (11):**
1. https://web.dev/articles/vitals — Core Web Vitals eşikleri (LCP/INP/CLS)
2. https://web.dev/articles/defining-core-web-vitals-thresholds — eşik metodolojisi (p75)
3. https://www.w3.org/TR/WCAG22/ — WCAG 2.2 W3C Recommendation (05.10.2023)
4. https://www.w3.org/WAI/standards-guidelines/wcag/new-in-22/ — WCAG 2.2'deki 9 yeni SC
5. https://www.sonatype.com/blog/open-source-malware-index-q4-2025-automation-overwhelms-ecosystems — Q4 2025: 394.877 malware, %99.8 npm, framework hedeflemesi
6. https://www.veracode.com/blog/threat-research-year-in-review-2025/ — OWASP A03:2025 #1, +86.8% kritik malware
7. https://safeguard.sh/resources/blog/malicious-npm-packages-targeting-developers-in-2025 — Shai-Hulud worm (500+ paket), chalk/debug olayı
8. https://arxiv.org/html/2504.03884v1 — hydration maliyeti, FID, -%82 JS (2025)
9. https://johal.in/we-switched-react-19-vanilla-javascript-cut-bundle — React 142KB→28KB vaka çalışması (2026) *(ikincil kaynak, blog)*
10. https://www.freecodecamp.org/news/managing-large-s-css-projects-using-the-inverted-triangle-architecture-3c03e4b1e6df/ — ITCSS (Harry Roberts) 7 katman
11. https://getbem.com/naming/ — BEM naming sözleşmesi

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Uygulama iskeleti yasağı | React/Vue/Angular/Laravel gibi **uygulama iskeleti** kullanılamaz; sınır: paket kütüphane olabilir, iskelet olamaz. ORM yasağı ile tutarlı (§2.1). |
| Composer serbestliği | Proje güvenliği/kalite Composer paketleriyle karşılanabilir; ancak bir Composer paketi frontend runtime'ı veya sayfa iskeleti sağlayamaz. |
| Core Web Vitals bütçesi | LCP ≤2.5s / INP ≤200ms / CLS ≤0.1 (web.dev, p75) hedefleri aşılamaz; JS bütçesi ITCSS/Utility denetimiyle korunur. |
| WCAG 2.2 AA | 4 yeni AA kriteri (2.4.11, 2.5.7, 2.5.8, 3.3.8) dahil tüm AA kriterleri; manuel a11y testi zorunlu (debate şartı 2). |
| Vault şablon zorunluluğu | Frontend kodu Guardrail #16 ile [[../../.templates/frontend/js-template]] ve [[../../.templates/frontend/css-template]]'ten üretilir; mockup'lar [[../../ui-design/01-mockup-index]] üzerinden okunur. |

---

## 2. Karar (Decision)

**Framework yasaktır.** CoreMusic frontend'inde React, Vue, Angular, Svelte, Next.js, Nuxt, Laravel (Blade/Livewire/Inertia dahil uygulama iskeleti olarak) ve benzeri **uygulama iskeletleri KULLANILMAZ**. Arayüz katmanı **saf Vanilla JS ES6+** ile yazılır; CSS **ITCSS 9 katman** sırasıyla, sınıflar **BEM namespace** sözleşmesine göre adlandırılır.

**İstisna / sınır (debate şartı 1):** **Composer paketleri serbesttir.** Bir paket *kütüphane* olabilir (yardımcı, test, güvenlik, kalite), *uygulama iskeleti* olamaz. Bu sınır, backend'deki ORM yasağı + PDO zorunluluğu kararıyla (eski seri → [[../index]]) aynı ilkeyi izler: veri/iskelet kontrolü projede kalır, üçüncü taraf yalnızca araç sağlar.

**Frontend paket yüzeyi:** npm ile frontend'e runtime paketi eklenmesi varsayılan olarak kapalıdır; herhangi bir npm bağımlılığı eklemek bu ADR'nin revizyonunu gerektirir (nedeni: §1.3 tedarik-zinciri verisi).

### 2.1 Neden Bu Seçenek?

1. **Saldırı yüzeyi:** npm 2025'te tüm OSS malware'inin %99.8'ini üretti ve saldırılar doğrudan JS/React framework ekosistemini hedef aldı (Sonatype Q4 2025, Veracode 2025, Safeguard). Framework = bağımlılık ağacı = girişi olan kapı. Saf Vanilla JS, frontend tedarik zincirisini fiilen sıfırlar; Composer tarafı ise kontrollü/kütüphane ölçeğinde kalır.
2. **Performans:** hydration ana thread'i bloklar ve statik içeriği hydrate etmek boş iştir (arXiv 2504.03884); framework runtime'ı gzip'e rağmen ~80KB çekirdek maliyet getirir (johal.in vakası: 142KB → 28KB, %80). Core Web Vitals eşikleri (LCP 2.5s / INP 200ms) değişmedi; küçük bundle doğrudan avantaj.
3. **Erişilebilirlik:** framework DOM soyutlaması, WCAG 2.2 AA kriterlerini (focus görünürlüğü, hedef boyutu, tutarlı yardım) elle test etmeyi zorlaştırır. Vanilla DOM + manuel a11y testi, AA kanıtını daha doğrudan üretir.
4. **Tutarlılık:** ORM yasağı ile aynı mimari felsefe — "kontrol projede kalır". Ayrıca [[../../brain]] ve [[../../AGENTS.md]] §16 zaten bu hedefi şart koşuyor; bu ADR kararı resmi ve tek kaynak yapar.

### 2.2 Teknik Detaylar

**(a) ITCSS 9 katman (şartname — [[../../architecture/k11-ux/itcss-9-layer]]):**

| # | Katman | İçerik | Specificity |
|---|--------|--------|-------------|
| 1 | `1-settings` | Design token, global değişken (stil üretmez, yalnız değer tanımlar) | En düşük |
| 2 | `2-tools` | Mixin/fonksiyon (stil üretmez) | — |
| 3 | `3-generic` | Reset/normalize/box-sizing (element seçici, class'sız) | Düşük |
| 4 | `4-elements` | HTML element default stilleri (tipografi, bağlantı) | Düşük-orta |
| 5 | `5-objects` | Layout pattern'leri: grid, container, flex | Orta |
| 6 | `6-components` | Bileşen stilleri — **BEM namespace burada zorunlu** (`.btn`, `.btn--primary`) | Orta-yüksek |
| 7 | `7-utilities` | Yardımcı sınıflar (`.mt-4`, `.sr-only`) | Yüksek |
| 8 | `8-themes` | Tema varyantları (`[data-theme='dark']` custom property override) | Yüksek |
| 9 | `9-trumps` | Son müdahale / `!important` override katmanı | En yüksek |

Kural: katman sırası değiştirilemez; alt katman üst katmanı geçersiz kılamaz; tek yönlü bağımlılık (settings → trumps).

**Kanıt uyarlığı (`⚠️ VERIFICATION REQUIRED`):** Şartname 9 katman der (k11-ux spec), disk kanıtı 8 katman dizini gösterir — `assets.coremusic.net/Css/**` = `01_Abstracts … 08_Devices` (kanıt: [[../../.templates/frontend/css-template]] §3.1; ayrıca 09_ViewModes dizini YOK). `.ai/ui-design/` ve `.ai/.templates/ui-design/` içinde katman listesi bulunmaz (glob kanıtı: bu dizinler screen-spec/reference/prompt/flow şablonları + tokens referansı içerir). Bu ADR, **şartname 9 katmanı** kararlaştırır; disk-8 / spec-9 farkı ayrı bir uyum işidir ve uyuşmazlıkta glob (disk) esas alınır, kapatılması UI Designer sorumluluğundadır.

**(b) BEM namespace ([[../../architecture/k11-ux/bem-naming]], getbem.com):** `.block__element--modifier` — yalnızca class seçici, tag/id seçici yasak; Türkçe/İngilizce blok adları küçük harf + tire; namespace öneki ITCSS katman ön ekiyle (`a-`, `o-`, `c-`, `u-`) uyumlu.

**(c) JS organizasyonu:** Vanilla JS ES6+ modülleri (bileşen, servis, olay yayını, SPA router, cookie/CSRF) — iskelet Guardrail #16 ile [[../../.templates/frontend/js-template]]'ten üretilir; JS ↔ ITCSS katman hizası zorunlu (§3.7).

**(d) Dev tooling (debate şartı 3):** Vitest + Playwright sadece geliştirme (dev) bağımlılığıdır; production bundle'a girmez. Source maps üretim ortamında kapalıdır (kod ifşası riski).

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **React + Next.js** | Olgun ekosistem, SSR/RSC, işe alım kolaylığı | ~80KB gzip runtime; hydration bloklama (arXiv 2025); npm tedarik-zinciri girişi (Sonatype: JS/React hedefli); kripto-ezberci soyutlama a11y testini zorlaştırır | Saldırı yüzeyi + hydration maliyeti WCAG/ CWV hedefleriyle çelişiyordu; R-001 (Redux-state) emsal red kararıyla uyumlu |
| 2 | **Vue / Angular (Nuxt)** | Dokümantasyon, i18n, reaktivite | Aynı iskelet-bağımlılığı sınıfı: bundle şişmesi, bağımlılık ağacı, uzun vadeli sürüm migrasyonları (Vue 2→3, Angular migrasyonları emsal) | "Kütüphane serbest / iskelet yasak" sınırı her framework'ü kapsar; Angular ekstra build karmaşası (eski ADR-004 Webpack red emsali) |
| 3 | **Laravel Livewire/Inertia (PHP backend'le frontend iskeleti)** | Backend'le tek framework, az JS | Frontend iskeletini de Laravel'e bağlar; PHP tarafı PDO/ORM yasağı felsefesiyle (iskelet=projede) çelişir; Livewire magic DOM'lar CSP/nonce (eski karar CSP nonce strict-dynamic) ile karmaşıklaşır | Backend zaten PHP 8.4 + PageRouter; frontend'i framework'e taşımak tek karar yerine ikinci bir iskelet bağımlılığı yaratırdı |
| 4 | **Hafif hibrit (Preact/Alpine.js/htmx)** | 3-10KB, "neredeyse vanilla" | Yine de npm/runtime bağımlılığı + öğrenme/karmaşıklık; ITCSS/BEM yapısıyla fazladan soyutlama katmanı | Sınır (iskelet yasak) net tutulmalı; aksi halde istisna kapıları zamanla framework'e döner — debate'deki 2 çekimser oyunun ana kaynağı da buydu; fallback şartı (§4.4) bu kaygıyı koşullu istisna olarak çözdü |
| 5 | **Bootstrap/Tailwind UI hazır sistem** | Hızlı başlangıç | Marka token'larıyla (design tokens, glassmorphism, 45-tier cihaz matrisi) çatışır; utility flood'u ITCSS specificity bütçesini bozar | Temel sistem yerine token + ITCSS + BEM; Tailwind yalnızca mevcut vendor katmanı (07_Vendors) düzeyinde kalabilir |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Frontend tedarik-zinciri riski fiilen sıfırlanır:** runtime npm bağımlılığı yok → Sonatype/Veracode 2025'te ölçülen npm malware dalgası (394.877 paket / Q4, +%86.8 kritik) frontend'e hiç ulaşamaz. *(vault çapraz referans: [[../../architecture/k6-guvenlik/CLAUDE.md]] güvenlik katmanı + güvenlik kararları [[../index]] §3 Security grubu; CSP nonce ve CSRF korumalarıyla birlikte değerlendirilir.)*
- **Core Web Vitals avantajı:** framework runtime'ı + hydration yükü olmadan LCP ≤2.5s / INP ≤200ms / CLS ≤0.1 (web.dev) hedefleri yapısal olarak kolaylaşır; vaka verisi %80 bundle azalması (142KB→28KB) sınırın büyüklüğünü gösterir. *(vault: [[../../ui-design/05-responsive-architecture]] §7.4 + [[../../.templates/frontend/css-template]] §3.15 bütçe ölçümü)*
- **WCAG 2.2 AA kanıtı sadeleşir:** framework soyutlaması olmadan 2.4.11 / 2.5.7 / 2.5.8 / 3.3.8 kriterleri doğrudan DOM üzerinde manuel test edilir (Vitest + Playwright dev tooling). *(vault: [[../../ui-design/04-accessibility-gaps]], [[../../architecture/k11-ux/accessibility-wcag]])*
- **ITCSS + BEM ile ölçeklenebilirlik:** 9 katman + namespace, framework'süz büyük ekip/kod tabanı düzenini sağlar; token tek kaynak (01_Abstracts). *(vault: [[../../architecture/k11-ux/itcss-9-layer]], [[../../architecture/k11-ux/bem-naming]], [[../../ui-design/02-component-inventory]])*
- **Composer serbestliği:** güvenlik/kalite kütüphaneleri (test, static analysis, kripto) PHP tarafında serbest → kalite düşmeden saldırı yüzeyi dar kalır. *(vault: [[../../keys]] "composer, kütüphane" anahtar eşleşmeleri; [[../../brain]] karar özeti)*

### 4.2 Olumsuz Sonuçlar

- **Geliştirme hızı ilk başta düşer:** hazır component library yok; her bileşen elde yazılır → [[../../ui-design/02-component-inventory]] envanteri devreye girmezse tekrar/çelişki riski.
- **Ekip beceri yükü:** Vanilla JS'te durum yönetimi, router, i18n elde kurulur; senior disiplin şart (SPA router sözleşmesi kavramı: [[../index]] §3 Routing grubu).
- **Ekosistem avantajı yok:** hazır UI kiti, devtools, kütüphane çözümleri yok; her problem için yerel çözüm yazılır.
- **Yeni npm bağımlılığı eklemek ADR revizyonu gerektirir** → iterasyon sürtünmesi (bilinçli maliyet).
- **Spec/disk katman farkı (9 vs 8)** bu kararla görünür oldu ve taşınıyor: `⚠️ VERIFICATION REQUIRED` → UI Designer kapatmalı.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| Elde yazım hatası / XSS (framework soyutlaması yok) | 3 (olası) | 4 (yüksek) | OWASP + hazır middleware (CSRF=`csrf_token`, CSP nonce — [[../index]] §3 Security), code review, PHPStan benzeri JS lint |
| Ekip hızının düşmesi, bileşen tekrarı | 3 (olası) | 3 (orta) | Şablon zorunlu (js-template/css-template), component inventory, BEM namespace disiplini |
| A11y regresyonu (otomatik test tek başına yeterli değil) | 3 (olası) | 4 (yüksek) | **Debate şartı 2:** manuel a11y testi zorunlu + Vitest/Playwright dev tooling; [[../../ui-design/04-accessibility-gaps]] kapanışı |
| Core Web Vitals bütçe aşımı (JS sprinkled growth) | 2 (mümkün) | 3 (orta) | JS/CSS bütçe ölçümü (css-template §3.15: gzip ≤50KB), CI'da bütçe kapısı (DevOps) |
| Gizli kırılma: acil durumda framework'e ihtiyaç | 2 (mümkün) | 4 (yüksek) | **§4.4 zaruri fallback** koşulları yazılı; debate 2 çekimser oyu bu maddeyle karşılandı |
| npm tedarik zinciri (dev tooling üzerinden sızıntı) | 2 (mümkün) | 3 (orta) | Production'da npm yok; dev bağımlılıkları pin + lockfile; **debate şartı 3:** source maps production'da kapalı |

### 4.4 Zaruri Fallback — Acil Durumda Framework Kaçış Koşulları

Bu madde, kararın **tek ve tek istisna kapısıdır**; koşulları sağlanmadan hiçbir agent framework öneremez.

| # | Koşul (hepsi sağlanmalı) | Onay |
|---|--------------------------|------|
| 1 | Ölçülmüş ve belgelenmiş performans/a11y/iş gereksinimi Vanilla JS ile 2 sprint içinde karşılanamaz (kayıt: Lighthouse + manuel a11y raporu) | QA Engineer + UI Designer |
| 2 | Sadece **kütüphane** denenmiş ve başarısız olmuş (iskelet dışı seçenekler tükenmiş) | UI Designer |
| 3 | **Yeni ADR** yazılır (yeni numara, eski `superseded by` bağlanır) — bu dosya asla düzenlenmez | Vault Steward → Tech Lead → Arch Lead |
| 4 | Kapsam dar tutulur: tek sayfa/bileşen adası, sitenin tamamı DEĞİL | Arch Lead onayı |
| 5 | Composer benzeri sınır uygulanır: framework runtime npm'e değil, izole dev/build ortamına hapsolur; tedarik-zinciri denetimi (lockfile + audit) zorunlu | Security Engineer |

*Acil durum geçici istisnası (ör. kritik hata giderimi) en fazla 5 iş günü sürer; kalıcı istisna 3 maddeyi de gerektirir.*

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | Bu ADR'yi `.ai/.decisions/accepted/` altına yaz + `log.md` append + karar dizinindeki mevcut satırı doğrula (`[[ADR-001-vanilla-js-itcss]]`) | Vault Steward | 15 dk |
| 2 | `assets.coremusic.net/Css/**` 8-katman disk gerçekliği ile spec 9 katmanı hizala (farkı kapat veya spec'i revize et) + `⚠️ VERIFICATION REQUIRED` kapat | UI Designer | 1 gün |
| 3 | js-template/css-template Guardrail #16 denetimi: framework/vendor yasağı maddesi + Composer kütüphane sınırı + source maps + manuel a11y maddeleri şablonlara işlendi mi? | UI Designer + Vault Steward | 2 saat |
| 4 | Dev tooling kurulumu: Vitest + Playwright (dev-only), production source maps off, JS/CSS bütçe kapısı (gzip ≤50KB) | QA Engineer + DevOps | 1 gün |
| 5 | WCAG 2.2 AA manuel test turu (2.4.11, 2.5.7, 2.5.8, 3.3.8 dahil) + [[../../ui-design/04-accessibility-gaps]] kapanışı | UI Designer + QA Engineer | 2 gün |

### 5.2 Geri Dönüş Planı

Karar frontend mimarisini kilitler; geri dönüş yalnızca **yeni ADR** ile olur (bu dosya frozen olmasa da keyfi düzenlenmez — In-Place Refactoring yasağı). Geri dönüş senaryosu: (1) §4.4 koşulları tetiklenir ve kalıcı framework geçişi gerekirse → yeni ADR yazılır, bu dosyaya `superseded by ADR-NNN` referansı **yeni ADR'nin** §6'sına konur; (2) ITCSS katman uyuşmazlığı (9 vs 8) tersine işlemle kapatılır: disk katmanları spec'e taşınır, spec revizyonu ADR değil, `k11-ux/itcss-9-layer.md` + css-template senkronu ile yapılır (bakım işi, karar değişikliği değil); (3) acil durum geçici istisnası 5 iş günü sonunda otomatik sonlanır, kod geri alınır (`git revert`) + `log.md`'ye ERROR satırı eklenir. Vault bozulması durumunda standart kurtarma: `git checkout` + son commit ([[../../AGENTS.md]] §17).

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../index]] | Karar dizini — bu ADR'nin kaydı (`[[ADR-001-vanilla-js-itcss]]`) + eski seri emsalleri (ORM yasağı, R-001/R-003/R-004 redleri) |
| [[../CLAUDE.md]] | Karar alt registry kuralı (accepted/) |
| [[../../CLAUDE.md]] | Ana sözleşme, 16 Hard Guardrail (Guardrail #16) |
| [[../../AGENTS.md]] | UI Designer domain (§4), ITCSS/BEM/WCAG kalite standardı (§16), framework uyarısı (§18 #8) |
| [[../../brain]] | Mimari karar özeti (ADR-001 satırı) |
| [[../../keys]] | Keyword haritası — "vanilla, ITCSS, BEM, framework, frontend" eşlemeleri |
| [[../../architecture/k11-ux/itcss-9-layer]] | 9 katman şartnamesi (§2.2 kaynağı) |
| [[../../architecture/k11-ux/bem-naming]] | BEM namespace sözleşmesi |
| [[../../architecture/k11-ux/accessibility-wcag]] | WCAG 2.2 AA kriter envanteri |
| [[../../ui-design/01-mockup-index]] | Mockup gate (Guardrail #11) — kod öncesi görsel okuma |
| [[../../ui-design/02-component-inventory]] | Bileşen envanteri (tekrar riski mitigasyonu) |
| [[../../ui-design/04-accessibility-gaps]] | A11y gap kapanış takibi |
| [[../../ui-design/05-responsive-architecture]] | Responsive token + fallback (§12) |
| [[../../.templates/frontend/js-template]] | Vanilla JS ES6+ kod iskeleti (Guardrail #16) |
| [[../../.templates/frontend/css-template]] | ITCSS katman sırası + bütçe (Guardrail #16) |
| [[../../.templates/adr/adr-template]] | Bu ADR'nin 7 bölümlük şablonu |
| [[../../log]] | Audit trail (bu kaydın append satırı) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı karar) | 2026-09-24 | ✅ |
| Tech Lead | ⏳ | 2026-09-24 | ✅ |
| Arch Lead | ⏳ | 2026-09-24 | ⏳ |

### 7.1 Tartışma Kaydı (Debate — kaynak alanı)

| Alan | Değer |
|------|-------|
| Biçim | 3 tur / 20 persona debate (`agent-debate` süreci) |
| Sonuç | **18 kabul / 2 çekimser / 0 red** |
| Çekimser gerekçeleri | (a) elde yazım hızı + ekosistem desteği endişesi, (b) acil durumda kaçış kapısının açık olması gerektiğinin vurgusu |
| Karara dönüşen şartlar (1) | **Composer paket = kütüphane sınırı maddesi:** paket serbest, uygulama iskeleti yasak (§2, §1.4) |
| Karara dönüşen şartlar (2) | **a11y manuel test zorunluluğu:** Vitest + Playwright dev tooling ile birlikte manuel WCAG 2.2 AA turu (§4.3, §5.1 #4-#5) |
| Karara dönüşen şartlar (3) | **dev tooling (source maps) maddesi:** source maps production'da kapalı, dev bağımlılıkları izole (§2.2 d, §4.3) |
| Çözüm | 2 çekimser oyun kaygısı §4.4 zaruri fallback maddesiyle karşılandı; 0 red nedeniyle veto yok |

---

*ADR-001 v1.0.0 | 2026-09-24 | Created — CoreMusic Vault (.decisions/ yeni seri)*
*Authority: ADR-001 Karar Metni · Mode: Red Team · Human Mode · Truth Mode*
