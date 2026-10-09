---
title: "K011 UX «OMURGA» — Katman Index"
type: index
category: architecture
version: 1.0.0
status: draft
authority: "SSOT: .ai/architecture/K011-ux/index.md — K-space band-1 katmanı (ADR-096)"
updated: 2026-10-08
tier: 3
domain: ux
ssot: true
risk: low
owner: ui
depends-on: [".ai/architecture/00-kspace-anayasa.md"]
---

# K011 UX «OMURGA» — Katman Index

> **Authority:** Bu dosya K011 katmanının tek SSOT'udur (R7.2). Üst otorite:
> `00-kspace-anayasa.md §A.1 K011 kartı` > `.ai/CLAUDE.md §5/§12` > `rules.md` > bu dosya (R8.1).
> Kurucu karar: `ADR-096-kspace-5000-boundary-model.md §2` + `ADR-001-vanilla-js-itcss`.
> **Durum:** `draft` — bant onayı Kapı 10'da 👤. Bu dosya staging'dir; vault'a yazılmadı.

## Künye

| Alan | Değer |
|---|---|
| K-ID | K011 |
| Kanonik Ad | UX |
| Teatral Epitet | «OMURGA» |
| Bant | Bant 1 — K000-K020 EXISTING FOUNDATION |
| Uçak | SOFTWARE (ADR-096 §2.2) |
| Dizin deseni | `.ai/architecture/K011-ux/index.md` (R2.2 — henüz üretilmedi, bu dosya staging taslağı) |
| Tier / Domain | 3 / ux |
| Owner (`.ai/AGENTS.md` §4 registry) | ui |
| Risk | low — dışa açık güvenlik yüzeyi yok; CSP nonce uyumu ve erişilebilirlik uyum yükümlülükleri var |
| Depends-on | `.ai/architecture/00-kspace-anayasa.md` |
| Bağlı ADR'ler (ls-verified: `.ai/.decisions/accepted/`) | ADR-001 · ADR-006 · ADR-018 · ADR-025 · ADR-031 · ADR-044 · ADR-045 · ADR-046 · ADR-048 · ADR-093 · ADR-096 |
| Kanıt tarihi | 2026-10-08 |
| Bant-kardeş üretimi | band-1 seti (K007-K013) — 7 dosya, staging |
| Şablon/format otoritesi | R3/R4 (iskelet + min-500 + 16 özet + 20 alan EK C) — `rules.md` |

#### §1 Genel Bakış

K011 UX «OMURGA», CoreMusic'in görsel/işitsel arayüz omurgasıdır: Playback/Library/Search/Queue/
Equalizer UI · Ambient Aura · Theme · Accessibility (anayasa §A.1 K011) + ITCSS 9-layer · BEM ·
Design Tokens · PWA · WCAG (anayasa §5 K11 — **40 bileşen, "Sadece K10 tarafından tetiklenebilir"**).
Repo kanıtı güçlü: `assets.coremusic.net/Css/` 11 katmanlı ITCSS dizini + `js/` (main, router,
components, coreplayer, managers, features) + vitest/playwright konfigürasyonları (ls 2026-10-08).
Frontend kodu bu katmanın dokümanından değil, Guardrail #11 mockup protokolünden üretilir (§6.6).

---

## §2 Özet Satır — 16 Alan (R4.1 / F1 §10.2 kontratı)

| K-ID | KANONİK_AD | TEATRAL_EPİTET | DOMAIN | RUNTIME | SORUMLULUK | GİRDİ | ÇIKTI | İZİNLİ_BAGIMLILIK | YASAK_BAGIMLILIK | DATA_BOUNDARY | SECURITY_BOUNDARY | FAILURE_MODE | OBSERVABILITY | TEST | KANIT |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| K011 | UX | «OMURGA» | ux | Tarayıcı runtime (Vanilla JS ES2022 · CSS ITCSS/BEM) · PWA service worker (hedef) | Playback UI · Library UI · Search UI · Queue UI · Equalizer UI · Ambient Aura · Theme · Accessibility — 40 bileşen · ITCSS 9-layer · BEM · Design Tokens · PWA · WCAG (anayasa §5 K11) | K010 tetiklemeleri (yalnız kaynak!) · design token dosyaları · tema cinsiyeti/kullanıcı tercihi (K008'den API ile) | DOM/render edilmiş arayüz · token/CSS katmanları · erişilebilir bileşen davranışı · Web Vitals sinyali (hedef) | K000-K010 (alt katmanlar) · **yalnız K10 tetiklemesi** (hard guardrail) · varlık servisi (assets) | K011 → K009/K008'e doğrudan veri çağrısı (veri yalnız K010/ApiClient üzerinden) · H20 geri çağrı · framework kullanımı (ADR-001) · token dışı serbest CSS (ITCSS sırası ihlali) | görünüm durumu yalnız CSS custom property/JS state; kalıcı veri/oturum YOK (auth storage yasağı §21) | CSP nonce uyumu (K007 #4/#5) · inline script yasağı (strict-dynamic) · WCAG erişilebilirlik (uyum yüzeyi) | fallback zinciri: CSS/asset yüklenmezse geriye dönük görünüm (responsive-architecture §12 fallback ZORUNLU) · degrade tema | Web Vitals/CLS sinyalleri (hedef ⚠️) · a11y denetim çıktısı · ön yüz hata (K012'ye devir) | Vitest (`assets.coremusic.net/vitest.config.js` ls) · Playwright (`playwright.config.ts` ls) · a11y audit PLANNED ⚠️ · hedef ≥80% (§17) | repo: `assets.coremusic.net/Css/01_Abstracts…11_OAuth` (11 katman ls) + `js/{main.js,router,components,core,coreplayer,managers,features,auth}` + vitest/playwright configs (ls 2026-10-08) · vault: `.ai/CLAUDE.md §5 K11/§12/§15/§7.1-§7.3` + `00-kspace-anayasa.md §A.1 K011` · ADR: ADR-001/044/031/045 (accepted/ ls) · web: ⚠️ VERIFICATION REQUIRED (F1 EK B #13 ITCSS) |

**Alan okuma notu:** İZİNLİ ∩ YASAK = ∅ ✓. Hedef ≠ kanıt (H10): "40 bileşen / ITCSS 9-layer" anayasa
hedefidir; "11 CSS katman dizini + 8+ JS dizini" repo kanıtıdır — birleştirilmez.

---

## §3 Tam Kimlik Kartı (EK C — 20 alan · R4.3)

| # | Alan | Değer |
|---|---|---|
| 1 | K-ID | K011 |
| 2 | KANONİK_AD | UX |
| 3 | TEATRAL_EPİTET | «OMURGA» (yalnız sıfat — K-ID'yi ezmez, R2.3) |
| 4 | DOMAIN | ux |
| 5 | SUBDOMAIN | playback-ui · library-ui · search-ui · queue-ui · equalizer-ui · ambient-aura · theme · accessibility · itcss-tokens · pwa |
| 6 | BOUNDED_CONTEXT | Arayüz Sunumu — iş verisi üretmez; K010'un tetiklediği ekran bileşenlerini ve stil omurgasını barındırır |
| 7 | RUNTIME | Tarayıcı: Vanilla JS ES2022 · CSS ITCSS + BEM · `--cm-*` design token (Kural §7.1-6) · PWA (hedef — ADR-031) |
| 8 | SORUMLULUK | Playback/Library/Search/Queue/Equalizer UI · Ambient Aura (görsel ambiyans) · Theme (cinsiyet bazlı — ADR-044 · §15) · Accessibility (WCAG) · ITCSS katman sırası · BEM adlandırma · Design Tokens · PWA · responsive cihaz override (§7.4) |
| 9 | GIRDI | K010 tetiklemeleri (tek meşru kaynak) · token/tipografi/renk dosyaları · tema tercihi (gender → renk) · medya durumu (playback state, K010 üzerinden) |
| 10 | CIKTI | DOM + stiller · token custom property'leri · erişilebilir bileşen davranışı (klavye/ARIA) · fallback görünümler |
| 11 | IZINLI_BAGIMLILIK | K000-K010 (alt katmanlar) · **K10 tetiklemesi** · varlık servisi (assets.coremusic.net) · WCAG/ARIA (dış standart) · ITCSS (dış metodoloji — EK B #13) |
| 12 | YASAK_BAGIMLILIK | K011 → K009/K008/K005'e doğrudan (veri yalnız K010/ApiClient) · H20 geri çağrı · framework (React/Vue/Angular — ADR-001) · ITCSS sırası/`07_Vendors` ihlali · token'sız serbest renk/ölçü |
| 13 | DATA_BOUNDARY | kalıcı veri YOK; yalnız görünüm durumu (JS state/CSS varlık). `localStorage`/`sessionStorage` auth YASAK (§21) |
| 14 | SECURITY_BOUNDARY | CSP nonce'lu script/CSS uyumu (K007 #4/#5 ile kilitli) · inline script/dangerous HTML yasağı (innerHTML → DOMParser · §21) · credential/gizli veri arayüzde tutulmaz |
| 15 | FAILURE_MODE | asset/CSS yüklenmezse fallback (responsive-architecture §12 "fallback ZORUNLU") · tema yüklenmezse default tema · erişilebilirlik bozulursa degrade değil, kırık UI (WCAG ihlali) |
| 16 | OBSERVABILITY | Web Vitals (LCP/CLS/INP hedefi ⚠️) · a11y denetim çıktıları · ön yüz hata olayı → K012 (PLANNED) |
| 17 | TEST | Vitest (js spec — `breakpoints.spec.js` ls) · Playwright E2E (`playwright.config.ts` ls) · a11y audit (axe benzeri) PLANNED ⚠️ · hedef ≥80% (§17) |
| 18 | KANIT | repo: `assets.coremusic.net/Css/{01_Abstracts,02_Base,03_Layout,04_Components,05_Pages,06_Utilities,07_Vendors,08_Devices,09_ViewModes,10_Helpers,11_OAuth}` (11 dizin ls 2026-10-08) · `assets.coremusic.net/js/{main.js,router/,components/,core/,coreplayer/,managers/,features/,auth/,device-loader.js,devices.config.js}` · `vitest.config.js` · `playwright.config.ts` · vault: `.ai/CLAUDE.md §5 K11 · §12 · §15 · §7.1-§7.3` + `00-kspace-anayasa.md §A.1 K011` · ADR: ADR-001/044/031/045/093 (accepted/ ls) · web: ⚠️ VERIFICATION REQUIRED (F1 EK B #13: ITCSS kaynakları) |
| 19 | KANIT_TARIHI | 2026-10-08 (repo ls + vault read) |
| 20 | EPİTET_KALİTE_NOTU | «OMURGA» — taşıyıcı, gövdeyi birleştiren omurga metaforu (tüm paneller bu omurgayı paylaşır); EK A §A.1 anahtar satırı: `K011 · UX · «OMURGA» · SOFTWARE` |

**R4.4 kart kapıları:** (a) 20 alan dolu ✓ · (b) İZİNLİ ∩ YASAK = ∅ ✓ (izinli: K10 tetik + varlık; yasak: doğrudan veri çağrısı) ·
(c) KANIT 3'lü ✓ (web ayağı ⚠️) · (d) veri sınırı: görünüm durumu yalnız K011'de; oturum K007'de ✓.

### §3.1 Özet Satır ↔ EK C Tutarlılık Kontrolü (R4.4a)

| Özet alanı (§2) | EK C karşılığı (§3) | Tutarlı? |
|---|---|---|
| K-ID / KANONİK_AD / TEATRAL_EPİTET | alan 1/2/3 | ✓ |
| DOMAIN | alan 4 | ✓ |
| RUNTIME | alan 7 | ✓ |
| SORUMLULUK | alan 8 | ✓ |
| GİRDİ / ÇIKTI | alan 9/10 | ✓ |
| İZİNLİ / YASAK | alan 11/12 | ✓ (kümeler disjoint) |
| DATA / SECURITY BOUNDARY | alan 13/14 | ✓ |
| FAILURE_MODE | alan 15 | ✓ |
| OBSERVABILITY | alan 16 | ✓ |
| TEST | alan 17 | ✓ |
| KANIT | alan 18/19/20 | ✓ (3'lü + tarih + epitet notu) |

---

## §4 Sorumluluk Derinliği

### §4.1 EK A §A.1 K011 Sorumluluk Maddeleri (anayasa kartı — ana kaynak)

Kaynak: `00-kspace-anayasa.md §A.1` — "Sorumluluk: Playback UI · Library UI · Search UI · Queue
UI · Equalizer UI · Ambient Aura · Theme · Accessibility".

| Kalem | Ne yapar | Durum (repo kanıtı yoksa PLANNED — H1) | Kanıt |
|---|---|---|---|
| Playback UI | oynat/duraklat/ilerlet + progress + cover görünümü | PARTIAL — `js/coreplayer/` + `js/main.js` (ls) · panel(K010) bağı | `assets.coremusic.net/js/coreplayer/` (ls 2026-10-08) · anayasa §A.1 · ADR-018 footer-player (accepted/ ls) |
| Library UI | kütüphane/gezinme görünümü (browse) | PARTIAL — `js/components/` + `features/` (ls) · ekran spec'leri `.ai/ui-design/` (vault) | `js/components/` (ls) · `.ai/CLAUDE.md §7.1` (mockup protokolü) |
| Search UI | arama arayüzü | PARTIAL — components/features (ls) · ayrı grep ⚠️ | `js/features/` (ls) · ⚠️ |
| Queue UI | oynatma kuyruğu görünümü | PARTIAL/PLANNED — ayrı dosya gözlenmedi ⚠️ | anayasa §A.1 · ⚠️ |
| Equalizer UI | EQ/preset yüzeyi (31-band · §19) | PARTIAL — `js/features/`/`coreplayer` olası sahip ⚠️ · tasarım ADR-025 | ADR-025 professional-eq-system (accepted/ ls) · `.ai/CLAUDE.md §19` |
| Ambient Aura | görsel ambiyans katmanı (arka plan/glow vb.) | PARTIAL — `Image/res-pink/` tema varlıkları (ls) ⚠️ eşleme | `assets.coremusic.net/Image/res-pink/` (ls) · ⚠️ |
| Theme | cinsiyet bazlı tema (female→pink · male→blue · neutral→default) · ThemeEngine/ThemeManager | IMPLEMENTED (tasarım + alt katman) | `.ai/CLAUDE.md §15` · ADR-044 (accepted/ ls) · `shared/src/Theme/` (ls) · `assets.coremusic.net/Image/res-pink/` (pembe varlık kanıtı) |
| Accessibility | WCAG erişilebilirlik (klavye + screen reader) | PARTIAL/PLANNED — a11y denetimi grep edilmedi → ⚠️ | `.ai/CLAUDE.md §5 K11 (WCAG 2.2 AA)` · F1 W10 (WCAG 2.1 AA) → çelişki notu §4.9 |

### §4.2 Anayasa §5 K-Matrix Satırı (K11) — 40 bileşenin açılımı

Kaynak: `.ai/CLAUDE.md §5` — **K11 Kullanıcı Deneyimi | ITCSS 9-layer, BEM, Design Tokens, PWA,
WCAG 2.2 AA | 40 | Sadece K10 tarafından tetiklenebilir.**

| Kalem (§5 satırı) | Ne yapar | Durum | Kanıt |
|---|---|---|---|
| ITCSS 9-layer | CSS mimarisi: Settings → Tools → Generic → Elements → Objects → Components → Utilities (+Trumps) | IMPLEMENTED (repo 11 dizin — çekirdek sırala) | `assets.coremusic.net/Css/01…11` (ls) · F1 W01 (ITSS sırası) · ADR-001 |
| BEM | blok-modifier-adlandırma | IMPLEMENTED (kural bağlayıcı) | `.ai/CLAUDE.md §12 (ITCSS + BEM)` · §7.1-6 · repo grep ⚠️ |
| Design Tokens | `--cm-*` token'ları; token yalnız `01_Abstracts` (+cihaz dosyası) | IMPLEMENTED (kural) + repo `01_Abstracts` (ls) | `.ai/CLAUDE.md §7.1 #6` · `Css/01_Abstracts/` (ls) |
| PWA | service worker + offline | PLANNED ⚠️ (SW dosyası grep edilmedi) | ADR-031 (accepted/ ls) · §5 K11 · ⚠️ |
| WCAG 2.2 AA | erişilebilirlik hedefi | PARTIAL/PLANNED ⚠️ | `.ai/CLAUDE.md §5 K11` (2.2) · F1 W10 (2.1) → §4.9 çelişki notu |
| "40 bileşen" | K11 envanter toplamı | HEDEF (H10) | `.ai/CLAUDE.md §5 K11` |
| Hard guardrail | "Sadece K10 tarafından tetiklenebilir" | BAĞLAYICI | `.ai/CLAUDE.md §5 K11` |

### §4.3 ITCSS Katman Yapısı (repo — `assets.coremusic.net/Css/`, ls 2026-10-08)

| # | Dizin | ITCSS/rol karşılığı | Durum | Not |
|---|---|---|---|---|
| 1 | `01_Abstracts/` | Settings + Tools (token/variable) | VAR | token kaynağı — "token yalnız 01_Abstracts" (§7.1-6) |
| 2 | `02_Base/` | Generic + Elements | VAR | reset/temel stiller |
| 3 | `03_Layout/` | Objects | VAR | grid/layout |
| 4 | `04_Components/` | Components | VAR | BEM bileşenleri |
| 5 | `05_Pages/` | page-specific | VAR | sayfa katmanı |
| 6 | `06_Utilities/` | Trumps/Utilities | VAR | yardımcı sınıflar |
| 7 | `07_Vendors/` | üçüncü parti | VAR | **dokunulmaz** (§7.1-6 kuralı) |
| 8 | `08_Devices/` | cihaz override (d-*.css) | VAR | import zinciri kuralı §7.1-6 |
| 9 | `09_ViewModes/` | view-mode (Home/Pro/Studio) | VAR | ADR-045/093 ile |
| 10 | `10_Helpers/` | helper sınıfları | VAR | — |
| 11 | `11_OAuth/` | auth ekranı özel | VAR | `auth-bundled.css` kuralı §7.1-6 |

**Sıra kuralı (bağlayıcı):** CSS katman sırası `01→11` korunur; `main.css` YOK, import zinciri
`08_Devices/d-*.css` + `auth-bundled.css` (`.ai/.templates/frontend/css-template.md` v4.0.0 —
`.claude/CLAUDE.md §7.1 #6` üzerinden). Anayasa §5'teki "ITCSS 9-layer" hedefi ile repo 11 dizin
sayısı farklıdır — mutabakat §4.9 çelişki notunda.

### §4.4 JS/Yüzey Envanteri (`assets.coremusic.net/js/`, ls 2026-10-08)

| # | Dosya/Dizin | Rol | Durum |
|---|---|---|---|
| 1 | `main.js` | uygulama giriş/koordinasyonu | VAR |
| 2 | `router/` | SPA router (FetchWrapper · GuardPipeline · CsrfSyncManager · AuthBoundaryDetector · CacheLayer · ContentFetcher/Patcher · DomPatcher · FocusManager — ls) | VAR |
| 3 | `core/` | CoreMusicApp · EventBus · breakpoints(+spec) · helper · footer.init | VAR |
| 4 | `components/` | BEM bileşen JS'leri | VAR (dizin) |
| 5 | `coreplayer/` | player çekirdeği (Playback UI) | VAR (dizin) |
| 6 | `managers/` · `features/` · `auth/` | durum/yönetici · özellik yüzeyleri · auth UI | VAR (dizinler) |
| 7 | `device-loader.js` · `device-layout-updater.js` · `devices.config.js` | cihaz-responsive davranış (K011/K010 sınırı) | VAR |
| 8 | `oauth-manager.js` | OAuth UI akışı | VAR |
| 9 | PWA service worker (`sw.js`/manifest) | offline/PWA | GÖZLENMEDİ → ⚠️ |

### §4.5 Tema Sistemi (§15 — ADR-044 · K011 alt kalemi derinlemesine)

| Parça | Rol | Durum | Kanıt |
|---|---|---|---|
| Kural | female→pink · male→blue · neutral→default | BAĞLAYICI | `.ai/CLAUDE.md §15` · ADR-044 (accepted/ ls) |
| PHP tarafı | `ThemeEngine.php` — DB + user gender çözümleme | PARTIAL (alt katman `shared/src/Theme/` ls) | `.ai/CLAUDE.md §15` · `shared/src/Theme/` (ls) |
| JS tarafı | `ThemeManager.js` — CSS custom property ile anında geçiş (sayfa yenileme yok) | PARTIAL ⚠️ (dosya adı grep edilmedi) | `.ai/CLAUDE.md §15` · ⚠️ |
| DB | `user_preferences` (user_id, device_type, theme) + `user_profiles.theme_gender` (A-F08 düzeltmesi) | BAĞLAYICI | `.ai/CLAUDE.md §15` (2026-10-07 hizalama notu) · §18 |
| Varlık kanıtı | pembe tema görselleri | IMPLEMENTED (ls) | `assets.coremusic.net/Image/res-pink/{app,disk,logo,power-system,quick-bar,actions,profile}` (ls) |
| Admin teması | bağımsız (kullanıcı temasından ayrı) | BAĞLAYICI | `.ai/CLAUDE.md §15` |

### §4.6 Erişilebilirlik & PWA (§5 K11 kalemleri)

| Kalem | Hedef | Durum | Kanıt |
|---|---|---|---|
| WCAG hedefi | 2.2 AA (anayasa §5 K11) | BAĞLAYICI · denetim PLANNED | `.ai/CLAUDE.md §5 K11` · §4.9 çelişki notu |
| Klavye + screen reader testi | F1 W10 | PLANNED ⚠️ | F1 (`2026-10-08-master-prompt-v2.2.0-f1.md §8.6 W10`) |
| PWA/offline | ADR-031 (PWA + Flutter) | PLANNED ⚠️ (SW yok) | ADR-031 (accepted/ ls) |
| Fallback kuralı | responsive-architecture §12 "fallback ZORUNLU" | BAĞLAYICI (frontend üretiminde) | `.claude/CLAUDE.md §7.1 #5` |
| 4K No-Center | §7.4 — 4K'da ortalamama YASAK | BAĞLAYICI (frontend üretiminde) | `.claude/CLAUDE.md §7.1 #5` |

### §4.7 K011 ADR Haritası (`.ai/.decisions/accepted/` ls-verified 2026-10-08)

| ADR | Karar özeti | K011 etkisi |
|---|---|---|
| ADR-001-vanilla-js-itcss | Vanilla JS + ITCSS; framework yasak | omurga kararının temeli (Frozen) |
| ADR-044-dynamic-user-theme-engine | cinsiyet bazlı dinamik tema | §4.5 tema sistemi |
| ADR-018-footer-player-vaporwave | footer player görünümü | Playback UI |
| ADR-025-professional-eq-system | profesyonel EQ sistemi | Equalizer UI |
| ADR-031-mobile-strategy-pwa-flutter | PWA + Flutter stratejisi | PWA kalemi |
| ADR-045-multi-domain-view-mode-architecture · ADR-093-view-modes-single-load-path | view-mode mimarisi + tek load path | `09_ViewModes` katmanı |
| ADR-046-cross-view-state-preservation | görünüm arası durum | UI state |
| ADR-048-view-transition-api-integration | View Transition API | geçiş animasyonları |
| ADR-006-performance-targets | performans hedefleri | Web Vitals hedefleri (⚠️ detay okunmadı) |
| ADR-096-kspace-5000-boundary-model | K-space V2 rejimi | format/bağımlılık kaynağı |

### §4.8 K011 Arayüz Sözleşmeleri (komşularla sınır)

| Komşu | Arayüz | K011'in verdiği | Beklenen | Kanıt |
|---|---|---|---|---|
| K010 APPLICATION | tetik + DOM hedefi | render/etkileşim | **yalnız K10 tetikler** | `.ai/CLAUDE.md §5 K11` |
| K007 MIDDLEWARE | CSP nonce | nonce'lu script/CSS | inline script yok | ADR-012 · K007 §4.3.4 |
| K009 API | (dolaylı) | — | doğrudan veri çağrısı YASAK | §6A.5 · §5.2 |
| K012 OBSERVABILITY | Web Vitals/hata | ön yüz sinyali | toplama PLANNED | §5 K12 |
| K013 CI/CD | test altyapısı | Vitest/Playwright girdisi | kapı testleri | `.github/workflows/ci.yml` (ls) |

### §4.9 Durum Özeti & Çelişki Notları (H1 — hedef ≠ kanıt · R8)

| Ölçüm/Çelişki | Değer |
|---|---|
| Anayasa §5 K11 hedefi | 40 bileşen · ITCSS 9-layer · WCAG 2.2 AA (HEDEF) |
| Repo kanıtı (ls 2026-10-08) | 11 CSS katman dizini · 8+ JS dizini/dosyası · vitest+playwright configs |
| Çelişki-1 (WCAG) | anayasa §5 = **WCAG 2.2 AA** · F1 §8.6 W10 = **WCAG 2.1 AA** → öncelik anayasa (R8.1); kayıt: 👤 (R4 Kapı 10) |
| Çelişki-2 (katman sayısı) | anayasa §5 = "ITCSS 9-layer" · repo = 11 numaralı dizin · kural §7.1-6 = "11 katman sırası (01→11)" → sayım ölçütü (klasik ITCSS çekirdeği mi, tam dizin mi) belirsiz → ⚠️ + 👤 |
| Test gap | a11y denetim yok (grep edilmedi) · PWA SW yok |
| Kapsam | K011 kod üretimi Guardrail #11 mockup protokolüne tabidir (§6.6) |

### §4.10 Frontend Üretim Envanteri (Guardrail #11 bağlama yüzeyi — `.claude/CLAUDE.md §7.1`)

| # | Kaynak dosya | İçerik (§7.1 tablosu) | Kullanım anı |
|---|---|---|---|
| 1 | `.ai/ui-design/01-mockup-index.md` | 19 PNG mockup indeksi (home-1024/1920 + shared-1024) + 45-tier cihaz matrisi | ilk okunacak |
| 2 | `.ai/ui-design/02-component-inventory.md` | C01-C16 BEM sınıfları, pixel ölçümleri, token referansları | bileşen kodlarken |
| 3 | `.ai/ui-design/tokens/design-tokens-master.md` | renk, boşluk, tipografi, cam token'ları | CSS yazarken |
| 4 | `.ai/ui-design/screens/00-ascii-art-index.md` | 19 PNG'nin ASCII layout modelleri (1024x600 pixel reference) | layout hizalama |
| 5 | `.ai/ui-design/05-responsive-architecture.md` | cihaz override kuralları · §7.4 4K No-Center · §12 fallback ZORUNLU | device CSS |
| 6 | `.ai/.templates/frontend/` seti | css-template.md · css-abstracts-token · css-component · css-page · css-device · css-auth-device · css-utility · css-helper | kod öncesi kanonik set |
| 7 | Referans sıralaması | PNG > ASCII art > Component Inventory > Tokens > Implementation Plan | çelişki durumunda |

**İhlal prosedürü (§7.1):** mockup okunmadan kod → derhal revert + `log.md` CRITICAL + Vault Steward bildirimi;
görsel okunamıyorsa DUR.

### §4.11 Token & CSS Kural Matrisi (§7.1-6 + §21 — bağlayıcı)

| Kural | Değer | Kaynak |
|---|---|---|
| Katman sırası | `01_Abstracts` → `11_OAuth` (01→11 korunur) | `.claude/CLAUDE.md §7.1 #6` |
| Token yerleşimi | token yalnız `01_Abstracts` (+cihaz dosyasına göre ayrılır: `a-layout-tokens-{mobile,tablet,1024,1920,3540,3840}.css`) | §7.1 #6 |
| Vendors | `07_Vendors` dokunulmaz | §7.1 #6 |
| Taşıma/silme | YOK (layer'lar taşınmaz/silinmez) | §7.1 #6 |
| Import zinciri | `08_Devices/d-*.css` + `auth-bundled.css` — **`main.css` YOK** | §7.1 #6 |
| Adlandırma | BEM (`.ai/CLAUDE.md §12`) | §12 |
| Yasaklı kalıplar | `innerHTML` → DOMParser + TrustedTypes · `eval()`/`Function()` yasak · `var` → `const`/`let` | §21 |
| Responsive | tek component sistemi + CSS variables + media queries; device CSS yalnız behavioral override | Guardrail #17 |

---

## §5 Bağımlılık & Sınır

### §5.1 İZİNLİ Bağımlılıklar (EK A aralık — alt katmanlar + port/adapter)

| Hedef | Yön | Gerekçe | Kanıt |
|---|---|---|---|
| K000-K009 (OS → API) | aşağı | anayasa §A.1 K011 "izinli=K000-K010" | `00-kspace-anayasa.md §A.1 K011` |
| **K010 APPLICATION** | tetikleyici | "Sadece K10 tarafından tetiklenebilir" — hard guardrail | `.ai/CLAUDE.md §5 K11` |
| Varlık servisi (assets.coremusic.net) | yan/teslim | CSS/JS/fonts/images kaynağı | repo ls · §12 stack |
| WCAG/ARIA · ITCSS | dış standart | erişilebilirlik + CSS metodolojisi | F1 EK B #13 · §5 K11 |
| port/adapter | yan | EK A istisnası (R6.3) | anayasa §A.1 |

### §5.2 YASAK Bağımlılıklar

| Yasak | Gerekçe | Kanıt |
|---|---|---|
| K011 → K009/K008/K005'e doğrudan veri çağrısı | veri yalnız K010/ApiClient üzerinden (§6A.5 ruhu) | `.ai/CLAUDE.md §6A.5` · §5 K10/K11 |
| K011 → K010/K012+ geri çağrı (H20) | klasik yön | `rules.md R6.1` · ADR-096 §2 |
| Framework (React/Vue/Angular) | ADR-001 Frozen | ADR-001 (accepted/ ls) · §21 |
| ITCSS sırası ihlali / `07_Vendors` düzenleme / token dışı serbest CSS | CSS kuralı (§7.1-6) | `.claude/CLAUDE.md §7.1 #6` |
| `innerHTML` · `eval()` · `var` | §21 forbidden patterns | `.ai/CLAUDE.md §21` |
| `localStorage`/`sessionStorage` auth | §21 | `.ai/CLAUDE.md §21` |
| Mockup okunmadan frontend kodu | Guardrail #11 (bu katmanın üretim kapısı) | `.claude/CLAUDE.md §7 · §7.1` |
| H19 doğrudan veri paylaşımı | veri sınırı | `rules.md R6.1` |

### §5.3 Boundary Matrisi

| Boundary | K011 Tanımı | Komşu sahip |
|---|---|---|
| DATA_BOUNDARY | görünüm durumu (CSS/JS state); kalıcı veri/oturum YOK | K007 (session) · K005 (kalıcı) |
| SECURITY_BOUNDARY | CSP nonce uyumu · dangerous HTML/eval yasağı · credential UI'da yok | K006/K007 (karar/uygulama) |
| FAILURE_MODE | asset fallback (§12 fallback ZORUNLU) · default tema · a11y kırılması = hata | K010 (workflow) · K012 |
| RUNTIME boundary | tarayıcı (Vanilla JS ES2022 · CSS) — sunucu değil | K000 (host) · K010 |
| CONTRACT boundary | token/BEM adlandırma sözleşmesi (K011 sahibi) | K011 |
| Olay yukarı serbest | UI olay/hataları → K012; senkron geri çağrı yasak | K012 |

**Sınır ihlali prosedürü:** tespit → derhal revert + `log.md` CRITICAL + 👤 bilgi (R6.5 · anayasa §5.1).

### §5.4 Olay Akışı (R6.2)

```text
K012 OBSERVABILITY ← (Web Vitals · UI hata olayları, yukarı serbest) ← K011 UX (tarayıcı)
        ↑                                                           │
        │ (event)                                        [tetik: yalnız K010 ↓/↑]
        └──────────── K010 APPLICATION ←→ K009 ApiClient (K011 doğrudan çağrı YASAK)
```

| Akış | Yön | Kural | Kanıt |
|---|---|---|---|
| K010 → K011 tetik | yukarı tetik | "sadece K10 tetikler" | §5 K11 |
| K011 → K009/K008 veri | — | YASAK (doğrudan çağrı) | §6A.5 · §5.2 |
| K011 → K012 sinyal | yukarı | yayın serbest | `rules.md R6.2` |
| Varlık servisi → K011 | aşağı/teslim | CSS/JS kaynağı | repo ls |

### §5.5 Kardeş İlişki Kuralı (R6.4)

Kardeş KNNN'lerle ilişki yalnız `refers-to`; `depends-on` yalnız alt katman yönünde. K011 ↔ K010
ile ilişki `refers-to` + tetik (hard guardrail metni ile) — veri bağımlılığı değildir. Yatay
`depends-on` → `dep-check` exit 1 (R6.6).

### §5.6 K011 ile Komşu Katmanların Tetik/Yükümlülük Matrisi

| Komşu | Yükümlülük | Yön | Kanıt |
|---|---|---|---|
| K010 | K011'i tetikler (tek meşru tetikleyici) | aşağı → yukarı tetik | §5 K11 |
| K011 | CSP nonce'lu script/CSS üretir | aşağıya uyum | ADR-012 · K007 §4.3.4 |
| K011 | K012'ye Web Vitals/hata olayı yayar | yukarı | R6.2 |
| K013 | Vitest/Playwright ile K011'i test eder | aşağı (kapı) | ci.yml (ls) |
| K008/K009 | K011'e asla doğrudan veri vermez | — (yalnız K010 üzerinden) | §6A.5 |

---

## §6 Girdi-Çıktı · Runtime · Observability-Test

### §6.1 Girdi → Çıktı Haritası

| Aşama | Girdi | Çıktı | Not |
|---|---|---|---|
| K10 tetiki | olay/ekran değişimi | render çağrısı | §5 K11 |
| Token katmanı | `--cm-*` değişkenleri | custom property değerleri | §7.1-6 |
| ITCSS sıralı yükleme | 01→11 import zinciri | katmanlı CSSOM | §4.3 |
| Bileşen render | JS component + veri (K10'dan) | DOM | `js/components/` |
| Tema geçişi | gender tercihi | renk değişimi (anında) | §15 · ADR-044 |
| Fallback | asset/CSS hatası | geriye dönük görünüm | §7.1 #5 (fallback ZORUNLU) |

### §6.2 Runtime

| Öğe | Değer | Kanıt |
|---|---|---|
| JS | Vanilla ES2022 (`const`/`let` — `var` yasak) | §12 · §21 |
| CSS | ITCSS + BEM · 11 katman dizini | §12 · repo ls |
| Token | `--cm-*` · yalnız `01_Abstracts` (+cihaz dosyası) | §7.1 #6 |
| Import zinciri | `08_Devices/d-*.css` + `auth-bundled.css` (`main.css` YOK) | §7.1 #6 |
| PWA | hedef (ADR-031) · SW dosyası yok ⚠️ | ADR-031 (ls) |
| Cihaz | responsive device override (d-*.css) + `devices.config.js` | repo ls · `.ai/ui-design/05-responsive-architecture.md` (§7.1 #5) |

### §6.3 Observability

| Sinyal | Kaynak | Durum |
|---|---|---|
| Web Vitals (LCP/INP/CLS) | tarayıcı ölçümleri | PLANNED ⚠️ (toplama yok) |
| A11y denetim çıktısı | test/audit çalıştırması | PLANNED ⚠️ |
| UI hata olayı | JS hata yakalayıcı → K012 | PLANNED ⚠️ |
| Performans hedefleri | ADR-006 (okunmadı → ⚠️) | ADR-006 (accepted/ ls) |

### §6.4 Test

| Katman | Framework | Kanıt | Hedef |
|---|---|---|---|
| JS birim | Vitest | `assets.coremusic.net/vitest.config.js` + `js/core/breakpoints.spec.js` (ls) | ≥80% (§17) |
| E2E | Playwright | `assets.coremusic.net/playwright.config.ts` (ls) | Kapı 11 · ≥80% |
| A11y | (axe benzeri denetim) | PLANNED ⚠️ | WCAG 2.2 AA |
| CSS katman bütünlüğü | statik kontrol (import zinciri) | kural §7.1-6 · otomasyon ⚠️ | 01→11 sıra |
| CI entegrasyonu | `ci.yml` Node adımları (js-test/coverage/e2e) | `.github/workflows/ci.yml` (ls: WP2/J1 script notu) | Kapı 11 |

### §6.5 Failure Mode Senaryoları

| # | Senaryo | K011 davranışı | Kanıt |
|---|---|---|---|
| 1 | CSS/asset yüklenmedi | fallback görünüm (geriye dönük uyumluluk ZORUNLU) | `.claude/CLAUDE.md §7.1 #5 (§12 fallback)` |
| 2 | Tema verisi gelmedi | default (neutral) tema | `.ai/CLAUDE.md §15` |
| 3 | CSP nonce uyuşmazlığı | script'ler reddedilir → UI kırılır | ADR-012 · K007 §4.3.4 |
| 4 | Service worker yok | offline/PWA modu çalışmaz (hedef karşılanmaz) | §4.6 · ⚠️ |
| 5 | A11y kırılması | WCAG ihlali → işlevsellik/hukuki risk | §5 K11 · §4.9 |
| 6 | ITCSS sırası değişti | stil çakışması → katman ihlali | §4.3 kural · ADR-001 |

### §6.6 Uyum & Kapı Bağlantısı (R11)

| Kapı/Kural | K011 durumu |
|---|---|
| KAPI 1 vault oku | Tam (anayasa §A.1 + §5/§12/§15 + §7.1-7.3 + rules + ADR-096 okundu) |
| KAPI 9 hallucination | ⚠️ listesi §7.1 |
| KAPI 10 onay | BEKLİYOR — `status: draft` (R10) |
| Guardrail #11 (Mockup Before Frontend) | frontend kod üretiminde bağlayıcı — `.ai/ui-design/01/02/tokens` + 45-tier matris + reference/01-10 okunmadan kod YASAK |
| Guardrail #16 (Template) | ui-design `.md` üretiminde kalıp A-D şablonları (§7.3) |
| Guardrail #17 (Single Component Responsive) | 1024x600 pixel reference · tek component sistemi · CSS variables + media query |

### §6.7 Kabul Kriterleri (katman `ACTIVE` eşiği — R16.2 + R4.4)

| # | Kriter | Ölçüt | Mevcut |
|---|---|---|---|
| K1 | Özet satır 16 alan | tam dolu · İZİNLİ∩YASAK=∅ | ✓ (§2) |
| K2 | EK C 20 alan | tam dolu · KANIT 3'lü | ✓ (§3) |
| K3 | 8 EK A kalemi + §5 K11 kalemleri | her kalem Durum+Kanıt | ✓ (§4.1/§4.2) |
| K4 | Tetik kilidi | "yalnız K10" hard guardrail belgeli | ✓ (§4.8 · §5.2) |
| K5 | Çelişki defteri | 2 çelişki (WCAG · katman sayısı) 👤 için açık | ✓ (§4.9) — R8.2 DUR maddesi |
| K6 | Web research (R9 3'lü) | ≥%80 kaynaklı | ✗ → ⚠️ (§7.1 G6) |
| K7 | Durum geçişi | draft → ACTIVE = 👤 + kanıt | ✗ bekliyor (Kapı 10) |

### §6.8 Cihaz/Responsive Test Yüzeyi (§7.4 · 05-responsive-architecture)

| Yüzey | Kural | Test durumu |
|---|---|---|
| Breakpoint token kontrolü | cihaz token dosyaları (`a-layout-tokens-*`) tutarlı olmalı | `js/core/breakpoints.spec.js` (Vitest, ls) |
| 4K No-Center | 4K'da ortalamama YASAK (§7.4) | manuel/otomatik denetim ⚠️ |
| Fallback | geriye dönük uyumluluk ZORUNLU (§12) | ⚠️ (test yok) |
| 45-tier cihaz matrisi | `01-mockup-index.md` 45-tier kapsamı | mockup okuma zorunlu (Guardrail #11) |
| Auth bundle | `auth-bundled.css` ayrı import zinciri | kural §7.1 #6 · otomasyon ⚠️ |
| E2E cihaz koşulları | Playwright viewport profilleri | `playwright.config.ts` (ls) · profil listesi ⚠️ |

---

## §7 Kanıt Kaynakları

| # | Kaynak | Tür (R9) | Kullanım |
|---|---|---|---|
| 1 | `00-kspace-anayasa.md §A.1 K011 - UX «OMURGA»` (+ §A.0) | dosya yolu (vault read 2026-10-08) | Sorumluluk/Sınır/Kanıt — ana kaynak |
| 2 | `.ai/CLAUDE.md §5 K11` · `§12` · `§15` · `§7.1-§7.3` · `§17` · `§19` · `§21` | dosya yolu (vault read) | K-matrix + CSS/tema kuralları |
| 3 | `assets.coremusic.net/Css/**` (11 dizin) · `js/**` · vitest/playwright configs · `Image/res-pink/` (ls 2026-10-08) | repo ls | IMPLEMENTED durumları |
| 4 | `.ai/.decisions/accepted/` ls: ADR-001/006/018/025/031/044/045/046/048/093/096 | ADR (ls teyitli) | karar atıfları |
| 5 | `rules.md R2/R3/R4/R6/R9` · `ADR-096 §2` | dosya yolu | format + yön |
| 6 | F1 EK B #13 (ITSS kaynakları: benmarshall.me/itcss · speakerdeck/csswizardry — F1 metni içi, tarih 2023-06-23 · 2013-04-24) | URL (vault arşivi) | ITCSS web kanıtı |
| 7 | Aşağıdaki boşluklar | ⚠️ VERIFICATION REQUIRED | R14 research kapısı |

### §7.1 Açık Kanıt Boşlukları (⚠️ defteri)

| # | Boşluk | Etkilenen alan | Kapanış yolu |
|---|---|---|---|
| G1 | PWA service worker/manifest dosyası yok | §4.2 PWA | `assets.coremusic.net/**` SW taraması + üretim |
| G2 | A11y denetimi yapılmadı | §4.6 · EK C TEST | axe/Playwright a11y çalıştırması |
| G3 | `ThemeManager.js` dosya adı grep edilmedi | §4.5 | js/ taraması |
| G4 | Web Vitals toplama yok | EK C OBSERVABILITY | K012 ile ortak tasarım |
| G5 | BEM/token grep'i (kural ihlali kontrolü) | §5.2 | CSS grep |
| G6 | Web kanıtı (ITSS dışı iddialar) | KANIT web ayağı | F1 EK B research kapısı (R14) |
| G7 | Queue/Search UI ayrı dosya eşlemesi | §4.1 | `js/features|components` içerik ls |
| G8 | Çelişki-1/Çelişki-2 kararı (WCAG · katman sayısı) | §4.9 | 👤 onayı (R8.2 · R4 Kapı 10) |

**Kural:** Boşluklar dosyayı geçersiz kılmaz; `ACTIVE` için R4.4c/R16.2 kapanışı gerekir.

### §7.2 Kanıt Haritası (EK C alan → birincil kanıt)

| EK C alan | Birincil kanıt | İkincil kanıt | Üçüncül |
|---|---|---|---|
| SORUMLULUK | anayasa §A.1 K011 | `.ai/CLAUDE.md §5 K11` | repo §4.3/§4.4 |
| RUNTIME | `.ai/CLAUDE.md §12 · §7.1-6` | repo Css/ js/ (ls) | ⚠️ |
| GIRDI/CIKTI | §5 K11 tetik · §15 tema | repo main.js (ls) | ⚠️ |
| IZINLI/YASAK | anayasa §A.1 + `rules.md R6` | ADR-001/096 (ls) | ⚠️ |
| DATA_BOUNDARY | §21 storage yasağı | repo grep ⚠️ | ⚠️ |
| SECURITY_BOUNDARY | ADR-012 · §21 (innerHTML/eval) | K007 §4.3.4 | ⚠️ |
| FAILURE_MODE | §7.1 #5 fallback kuralı | §6.5 senaryoları | ⚠️ |
| OBSERVABILITY | §5 K12 devri | ADR-006 (ls) | ⚠️ (Web Vitals) |
| TEST | vitest/playwright configs (ls) | ci.yml Node adımları (ls) | ⚠️ (a11y) |

---

## §8 İlişki & Değişiklik

**Kardeş katmanlar (Bant 1 · K000-K020 — düz metin, wiki-link YASAK, R2.4):**
Alt (izinli): K000 OS «TEMEL TAŞI» · K001 HARDWARE «DEMİRHANE» · K002 DRIVERS «KANAT» ·
K003 AUDIO ENGINE «ÇEKİRDEK» · K004 AI «MIKNATIS» · K005 DATA «FENER» · K006 SECURITY «ÇELİK KAPI» ·
K007 MIDDLEWARE «SUR» · K008 SERVICES «KULE» · K009 API «HÜCRE» · K010 APPLICATION «DÜĞÜM».
Üst/yasak yön (H20): K012 OBSERVABILITY «AYNA» · K013 CI/CD «PUSULA» · K014 NETWORK «ÇARK» ·
K015 MEDIA «MÜHÜR» · K016 AMPLIFIER «ZAR» · K017 POWER «KANTAR» · K018 THERMAL «MEZİT» ·
K019 PCB «ALEV» · K020 MANUFACTURING «BUZUL». Kardeşler arası ilişki yalnız `refers-to` (R6.4).

**Frontend üretim kapısı (bağlayıcı):** Bu dosya mimari katmandır; frontend kodu Guardrail #11
ile üretilir — `.ai/ui-design/01-mockup-index.md` + `02-component-inventory.md` +
`tokens/design-tokens-master.md` + `screens/00-ascii-art-index.md` + `05-responsive-architecture.md`
okunmadan kod YASAK (`.claude/CLAUDE.md §7.1`). CSS görevleri için kanonik set:
`.ai/.templates/frontend/` (css-template.md · css-abstracts-token-template.md · … — §7.1 #6).

**İzinli wiki-linkler:** [[architecture/00-kspace-anayasa]] · [[architecture/rules]] · [[architecture/00-master-index]]

**Bant-1 üretim durumu (2026-10-08 · staging, vault'a yazılmadı):** b1-K007-middleware.md (K007) ·
b1-K008-servisler.md (K008) · b1-K009-api.md (K009) · b1-K010-uygulama.md (K010) ·
b1-K011-ux.md (K011 — bu dosya) · b1-K012-izleme.md (K012) · b1-K013-cicd.md (K013).

**Geçmiş:** 2026-10-08 ilk üretim (bant-1 · staging) | Vault Steward.

**Not (Kapı 10):** Bu dosya `draft`'tır; band-1 seti (K007-K013) birlikte 👤 onayına gider. §7.1
boşlukları ve §4.9 çelişki maddeleri research/çelişki kapısında kapatılmalıdır (R4.4c · R8.2 · R16.2).
Vault yazımı ve `git commit` bu üretim kapsamında DEĞİLDİR (H5/H6).

**Çapraz okuma:** K010 (tetikleyici) · K007 (CSP nonce) · K013 (test kapısı) kartları bu dosyanın
sınır tanımlarını tamamlar; birlikte okunur.

**Kaynak bağımlılığı:** `depends-on: .ai/architecture/00-kspace-anayasa.md` — anayasa güncellenirse
bu kart §A.1 K011 satırıyla birlikte yeniden gözden geçirilir (R8.1 zinciri).