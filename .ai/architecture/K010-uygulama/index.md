---
title: "K010 APPLICATION «DÜĞÜM» — Katman Index"
type: index
category: architecture
version: 1.0.0
status: draft
authority: "SSOT: .ai/architecture/K010-uygulama/index.md — K-space band-1 katmanı (ADR-096)"
updated: 2026-10-08
tier: 3
domain: application
ssot: true
risk: medium
owner: backend
depends-on: [".ai/architecture/00-kspace-anayasa.md"]
---

# K010 APPLICATION «DÜĞÜM» — Katman Index

> **Authority:** Bu dosya K010 katmanının tek SSOT'udur (R7.2). Üst otorite:
> `00-kspace-anayasa.md §A.1 K010 kartı` > `.ai/CLAUDE.md §5/§9` > `rules.md` > bu dosya (R8.1).
> Kurucu karar: `ADR-096-kspace-5000-boundary-model.md §2`. **Durum:** `draft` — bant onayı Kapı 10'da 👤.
> Bu dosya staging'dir; vault'a yazılmadı.

## Künye

| Alan | Değer |
|---|---|
| K-ID | K010 |
| Kanonik Ad | APPLICATION |
| Teatral Epitet | «DÜĞÜM» |
| Bant | Bant 1 — K000-K020 EXISTING FOUNDATION |
| Uçak | SOFTWARE (ADR-096 §2.2) |
| Dizin deseni | `.ai/architecture/K010-uygulama/index.md` (R2.2 — henüz üretilmedi, bu dosya staging taslağı) |
| Tier / Domain | 3 / application |
| Owner (`.ai/AGENTS.md` §4 registry) | backend |
| Risk | medium — 10 panel yüzeyi + workflow orkestrasyonu; K006 dışı high yok (EK A security=ORTA) |
| Depends-on | `.ai/architecture/00-kspace-anayasa.md` |
| Bağlı ADR'ler (ls-verified: `.ai/.decisions/accepted/`) | ADR-004 · ADR-024 · ADR-031 · ADR-043 · ADR-045 · ADR-046 · ADR-047 · ADR-048 · ADR-049 · ADR-056 · ADR-058 · ADR-093 · ADR-096 |
| Kanıt tarihi | 2026-10-08 |
| Bant-kardeş üretimi | band-1 seti (K007-K013) — 7 dosya, staging |
| Şablon/format otoritesi | R3/R4 (iskelet + min-500 + 16 özet + 20 alan EK C) — `rules.md` |

#### §1 Genel Bakış

K010 APPLICATION «DÜĞÜM», kullanıcının gördüğü ve kullandığı uygulama katmanıdır: 10 panel
(music, admin, home, car, studio, download, landing, pro, media, auth) + user workflow'ları
(anayasa §5 K10 · §9). Hard guardrail: **"Yalnızca K9 API üzerinden iletişim kurabilir"** — panel
asla K008/K005'e doğrudan erişmez (§6A.5 SPA yasağı ile kilitli). Repo gerçeği (ls 2026-10-08):
panel dizinlerinden `auth.coremusic.net/`, `home.coremusic.net/`, `media.coremusic.net/` FİZİKSEL VAR;
`music/admin/car/studio/download/landing/pro` YOK → §9 hedef-tablosu + Faz-1 gerçeklik notu ile
hizalıdır (PLANNED — H1).
Kapsam dışı: iş mantığı/veri sahipliği (K008/K005) · API sözleşmesi (K009) · ITCSS/token üretimi
(K011) · istek hattı kademeleri (K007) — bu katman yalnız panel yüzeyini ve user workflow'larını
barındırır. Panel üretimine başlarken Guardrail #11 (mockup okuma) bağlayıcıdır (§6.6 notu).

---

## §2 Özet Satır — 16 Alan (R4.1 / F1 §10.2 kontratı)

| K-ID | KANONİK_AD | TEATRAL_EPİTET | DOMAIN | RUNTIME | SORUMLULUK | GİRDİ | ÇIKTI | İZİNLİ_BAGIMLILIK | YASAK_BAGIMLILIK | DATA_BOUNDARY | SECURITY_BOUNDARY | FAILURE_MODE | OBSERVABILITY | TEST | KANIT |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| K010 | APPLICATION | «DÜĞÜM» | application | PHP 8.4 (server-rendered paneller · port 80/81) · Vanilla JS ES2022 (SPA etkileşimi) | 10 panel (music, admin, home, car, studio, download, landing, pro, media, auth) + User Workflows — 45 bileşen (anayasa §5 K10) | K009 API yanıtları (BFF profilleri) · kullanıcı girdisi (form/etkileşim) · tema/view-mode durumu | panel render/etkileşim · ApiClient istekleri (K009'a) · kullanıcı workflow adımları | K000-K009 (alt katmanlar) + port/adapter · **yalnız K009 API** (hard guardrail) | K010 → K008/K005 doğrudan erişim · K007'ye geri çağrı (H20) · K011'i by-pass ederek inline stillerle sözleşme ihlali · H19 veri paylaşımı | panel iş verisi tutmaz; yalnız görünüm durumu + oturum (K007'den); kalıcı veri K005, iş K008 | rol-bazlı panel erişimi (K006/K007 Permission) · BypassAuth yalnız test · credential panelde tutulmaz (Guardrail U1) | API erişilemezse degrade panel (error state) · oturum dolumu → yeniden login (ADR-047) · workflow ortasında kesinti → geri dönüş köprüsü | panel erişim logları (K012) · kullanıcı workflow olayları (event → yukarı) | PHPUnit 11 (`auth.coremusic.net/tests`, `home.coremusic.net/phpunit.xml`, `media.coremusic.net` — ls 2026-10-08) · Playwright E2E · hedef ≥80% (§17) | repo: `auth.coremusic.net/` + `home.coremusic.net/` + `media.coremusic.net/` (ls 2026-10-08; 7 panel dizini YOK) · vault: `.ai/CLAUDE.md §5 K10/§9/§6A.5` + `00-kspace-anayasa.md §A.1 K010` · ADR: ADR-004/043/045/047/093 (accepted/ ls) · web: ⚠️ VERIFICATION REQUIRED (F1 EK B) |

**Alan okuma notu:** İZİNLİ ∩ YASAK = ∅ ✓. Hedef ≠ kanıt (H10): "45 bileşen / 10 panel" anayasa hedefidir;
"3 panel dizini" repo kanıtıdır — birleştirilmez.

---

## §3 Tam Kimlik Kartı (EK C — 20 alan · R4.3)

| # | Alan | Değer |
|---|---|---|
| 1 | K-ID | K010 |
| 2 | KANONİK_AD | APPLICATION |
| 3 | TEATRAL_EPİTET | «DÜĞÜM» (yalnız sıfat — K-ID'yi ezmez, R2.3) |
| 4 | DOMAIN | application |
| 5 | SUBDOMAIN | music-panel · admin-panel · home-panel · car-panel · studio-panel · download-panel · landing-panel · pro-panel · media-panel · auth-panel · user-workflows |
| 6 | BOUNDED_CONTEXT | Uygulama Yüzeyi & Workflow — panel başına ekran akışı; iş kuralı K008'de, panel yalnız orkestre eder |
| 7 | RUNTIME | PHP 8.4 (server-side panel · port 80/81 · §11) · Vanilla JS ES2022 (istemi etkileşim — ADR-001 framework yasak) · Embedded: RPi5/PCM3168A hedefi (§14) |
| 8 | SORUMLULUK | 10 panel (§9 tablosu) + User Workflows (çapraz-panel akışlar: login→library→playback→queue; download kuyruğu; studio oturumu) + view-mode/orchestration (ADR-045/093) |
| 9 | GIRDI | K009 API yanıtları (BFF profili) · kullanıcı girdisi (form/touch/komut) · tema (K011/K015) · olaylar (K008 event'leri) |
| 10 | CIKTI | panel DOM/render · ApiClient isteği (K009) · event (workflow adımı) · hata-ystate gösterimi |
| 11 | IZINLI_BAGIMLILIK | K000-K009 (alt katmanlar) · **K009 API tek kapı** · port/adapter (EK A) · K011'e CSS/JS varlık teslimi (varlık, veri değil) |
| 12 | YASAK_BAGIMLILIK | K008/K005'e doğrudan erişim (SPA yasağı §6A.5 · §5 K10 hard guardrail) · H20 geri çağrı · K007/K009 bypass · H19 veri paylaşımı · panelde secret/credential (R17 U1) |
| 13 | DATA_BOUNDARY | Kalıcı veri yok; yalnız görünüm durumu (view-mode, queue görünümü) + oturum kimliği. `localStorage/sessionStorage` auth için YASAK (§21) |
| 14 | SECURITY_BOUNDARY | Panel erişimi RBAC ile (K006/K007 #9) · admin paneli ayrı yetki · BypassAuth test-only · CSP nonce uyumu (K011 ile ortak) |
| 15 | FAILURE_MODE | API DOWN → panel degrade (hata kartı + retry) · oturum dolumu → login köprüsü (ADR-047) · workflow kesintisi → durum korunur (ADR-046) · fail-over (EK A) |
| 16 | OBSERVABILITY | panel erişim/aksiyon logları (K012) · workflow adım olayları (event yukarı) · ön yüz hata yakalama (K012'ye devir PLANNED ⚠️) |
| 17 | TEST | PHPUnit 11 (panel phpunit: auth/home/media — ls) · Playwright E2E (`assets.coremusic.net/playwright.config.ts` ls) · Vitest (panel JS) · hedef ≥80% (§17) |
| 18 | KANIT | repo: `auth.coremusic.net/` (index.php · routes · tests) · `home.coremusic.net/` (header/footer · phpunit.xml) · `media.coremusic.net/` (src · composer) — 3/10 panel; `music/admin/car/studio/download/landing/pro` YOK (ls 2026-10-08) · vault: `.ai/CLAUDE.md §5 K10 · §9 (10 panel) · §6A.5` + `00-kspace-anayasa.md §A.1 K010` · ADR: ADR-004/043/045/046/047/093 (accepted/ ls) · web: ⚠️ VERIFICATION REQUIRED |
| 19 | KANIT_TARIHI | 2026-10-08 (repo ls + vault read) |
| 20 | EPİTET_KALİTE_NOTU | «DÜĞÜM» — akışın birleştiği nokta metaforu (paneller tek noktada K009'a bağlanır); EK A §A.1 anahtar satırı: `K010 · APPLICATION · «DÜĞÜM» · SOFTWARE` |

**R4.4 kart kapıları:** (a) 20 alan dolu ✓ · (b) İZİNLİ ∩ YASAK = ∅ ✓ (izinli: K009 + varlık; yasak: K008/K005) ·
(c) KANIT 3'lü ✓ (web ayağı ⚠️) · (d) veri sınırı: görünüm durumu yalnız K010'da; kalıcı veri K005'te ✓.

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

### §4.1 EK A §A.1 K010 Sorumluluk Maddeleri (anayasa kartı — ana kaynak)

Kaynak: `00-kspace-anayasa.md §A.1` — "Sorumluluk: Music App · Home App · Car App · Studio App ·
Mobile · Web · Admin · User Workflows".

| Kalem | Ne yapar | Durum (repo kanıtı yoksa PLANNED — H1) | Kanıt |
|---|---|---|---|
| Music App | ana medya paneli (`music.coremusic.net`, port 81, PHP 8.4 + JS) | PLANNED — dizin root'ta YOK (ls 2026-10-08) | `.ai/CLAUDE.md §9 #2` · repo: dizin YOK |
| Home App | ev merkezi paneli (`home.coremusic.net`, port 81, Vanilla JS) | IMPLEMENTED (repo dizini) | `home.coremusic.net/` (ls: header.php · footer.php · config · composer · phpunit.xml) · §9 #7 |
| Car App | araç içi panel (`car.coremusic.net`, Vanilla JS) | PLANNED — dizin YOK | `.ai/CLAUDE.md §9 #8` · repo: YOK |
| Studio App | stüdyo paneli (`studio.coremusic.net`, port 81) | PLANNED — dizin YOK | `.ai/CLAUDE.md §9 #9` · repo: YOK |
| Mobile | mobil yüzey (PWA — §14/ADR-031; Flutter hedefi ADR-031) | PARTIAL/PLANNED — ayrı mobil dizin yok; PWA varlık katmanı K011'de | ADR-031 mobile-strategy-pwa-flutter (accepted/ ls) · `.ai/CLAUDE.md §14` · ⚠️ |
| Web | genel web panelleri (landing/pro/media/auth/music…) | PARTIAL — 3/10 panel dizini var (auth, home, media) | repo ls 2026-10-08 · §9 |
| Admin | yönetim paneli (`admin.coremusic.net`, port 80, PHP 8.4) | PLANNED — dizin YOK | `.ai/CLAUDE.md §9 #3` · repo: YOK |
| User Workflows | çapraz-panel akışlar (login → kütüphane → oynatma → kuyruk; indirme kuyruğu; studio oturumu) | PARTIAL — auth akışı kodda (auth panel + ADR-047 köprü) · diğer akışlar hedef | `auth.coremusic.net/routes|handler|tests` (ls) · ADR-047 login-redirect-session-bridge (accepted/ ls) |

### §4.2 Anayasa §5 K-Matrix Satırı (K10) — 45 bileşenin açılımı

Kaynak: `.ai/CLAUDE.md §5` — **K10 Uygulama | 10 Panel: music, admin, home, car, studio, download,
landing, pro, media, auth | 45 | Yalnızca K9 API üzerinden iletişim kurabilir.**

| Kalem (§5 satırı) | Ne yapar | Durum | Kanıt |
|---|---|---|---|
| music | ana medya paneli | PLANNED | §9 #2 · dizin YOK |
| admin | yönetim | PLANNED | §9 #3 · dizin YOK |
| home | ev merkezi | IMPLEMENTED | `home.coremusic.net/` (ls) · §9 #7 |
| car | araç içi | PLANNED | §9 #8 · dizin YOK |
| studio | stüdyo | PLANNED | §9 #9 · dizin YOK |
| download | indirme (port 3001 paneli) | PLANNED | §9 #4 · dizin YOK |
| landing | açılış | PLANNED | §9 #1 · dizin YOK |
| pro | profesyonel | PLANNED | §9 #10 · dizin YOK |
| media | medya (5000/6000) | IMPLEMENTED | `media.coremusic.net/` (ls) · §9 #5 |
| auth | kimlik | IMPLEMENTED | `auth.coremusic.net/` (ls) · §9 #6 |
| "45 bileşen" | K10 envanter toplamı | HEDEF (H10) | `.ai/CLAUDE.md §5 K10` |
| Hard guardrail | "Yalnızca K9 API üzerinden iletişim" | BAĞLAYICI | `.ai/CLAUDE.md §5 K10` · §6A.5 |

### §4.3 Anayasa §9 Panel Haritası (gerçek tablo — 10 panel)

Kaynak: `.ai/CLAUDE.md §9` (+ Faz 1 gerçeklik notu: hedef mimari; kodda fiziksel paneller sınırlı).

| # | Panel | Subdomain | Port | Stack | §9 Durum | Repo Durumu (ls 2026-10-08) |
|---|---|---|---|---|---|---|
| 1 | Landing | `coremusic.net` | 80 | Vanilla JS | ✅ hedef | PLANNED (kök landing dizini yok — README/CONTEXT var) |
| 2 | Music | `music.coremusic.net` | 81 | PHP 8.4 + JS | ✅ ana medya | PLANNED — dizin YOK |
| 3 | Admin | `admin.coremusic.net` | 80 | PHP 8.4 | ✅ yönetim | PLANNED — dizin YOK |
| 4 | Download | `download.coremusic.net` | 3001 | Node.js + TS | ✅ indirme | PLANNED — dizin YOK |
| 5 | Media | `media.coremusic.net` | 5000/6000 | PHP + FFmpeg | ✅ medya | IMPLEMENTED (dizin var: src · config · bin · composer) |
| 6 | Auth | `auth.coremusic.net` | — | PHP 8.4 | ✅ kimlik | IMPLEMENTED (dizin var: index · routes · handler · tests) |
| 7 | Home | `home.coremusic.net` | 81 | Vanilla JS | ✅ ev merkezi | IMPLEMENTED (dizin var: header/footer · config · phpunit) |
| 8 | Car | `car.coremusic.net` | — | Vanilla JS | ✅ araç içi | PLANNED — dizin YOK |
| 9 | Studio | `studio.coremusic.net` | 81 | Vanilla JS | ✅ stüdyo | PLANNED — dizin YOK |
| 10 | Pro | `pro.coremusic.net` | 81 | Vanilla JS | ✅ profesyonel | PLANNED — dizin YOK |

**Görünüm modları:** Home, Pro, Studio — her panel için geçerli (anayasa §9 · ADR-045 · ADR-093).

### §4.4 User Workflows (EK A son kalemi)

| # | Workflow | Adımlar (tasarım) | Durum | Kanıt |
|---|---|---|---|---|
| W1 | Login → oturum → dönüş | login form → K009 auth → session → redirect köprüsü | IMPLEMENTED (parça) | `auth.coremusic.net/` (ls) · ADR-047 (accepted/ ls) |
| W2 | Kütüphane → oynatma → kuyruk | panel → ApiClient → Media/Audio servisleri | PLANNED (music paneli yok) | §9 #2 · §10 #2/#3 |
| W3 | İndirme kuyruğu | istek → Download servisi (3001) → durum akışı | PLANNED | §9 #4 · §10 #7 · ADR-026 |
| W4 | Studio oturumu | oturum → track → preset → kayıt yüzeyi | PLANNED | §9 #9 · anayasa §A.1 K010 |
| W5 | Araç içi akış | Car paneli → touch-optimized BFF (eksik) | PLANNED | §6A.2 Car BFF · §9 #8 |
| W6 | Admin yönetim akışı | admin → Admin BFF (eksik) → K008 | PLANNED | §6A.2 Admin BFF · §9 #3 |
| W7 | Cross-view state | panel geçişlerinde durum korunur | IMPLEMENTED (tasarım/ADR) | ADR-046 cross-view-state (accepted/ ls) |
| W8 | View-mode geçişi | Home/Pro/Studio görünüm modları | IMPLEMENTED (tasarım/ADR + varlık katmanı) | ADR-045/093 (accepted/ ls) · `assets.coremusic.net/Css/09_ViewModes` (ls) |

### §4.5 Panel ↔ BFF/Servis Bağı (K009/K008 ile sınır)

| Panel | BFF (§6A.2) | Servis (§10) | Not |
|---|---|---|---|
| Music · Home · Pro · Studio | SPA BFF (VAR) | Media + Audio + AI | BFF dosyası VAR (K009 §4.4) |
| Auth | SPA BFF | Control | repo panel VAR |
| Media | SPA BFF | Media | repo panel VAR |
| Download | SPA/Embedded | Download | panel + servis PLANNED |
| Car | Car BFF (eksik) | Media + Network | 2 eksik (BFF + panel) |
| Admin | Admin BFF (eksik) | Control + tümü | 2 eksik |
| Landing | SPA BFF | (public) | panel PLANNED |

### §4.6 K010 ADR Haritası (`.ai/.decisions/accepted/` ls-verified 2026-10-08)

| ADR | Karar özeti | K010 etkisi |
|---|---|---|
| ADR-004-multi-domain-spa | Çoklu domain SPA mimarisi | panel/domain organizasyonu |
| ADR-043-auth-subdomain-consolidation | Auth subdomain konsolidasyonu | auth paneli/akış |
| ADR-045-multi-domain-view-mode-architecture | View-mode mimarisi | Home/Pro/Studio modları |
| ADR-093-view-modes-single-load-path | Tek yükleme yolu view-mode | panel yükleme davranışı |
| ADR-046-cross-view-state-preservation | Görünüm arası durum | W7 workflow |
| ADR-047-login-redirect-session-bridge | Login redirect köprüsü | W1 workflow |
| ADR-048-view-transition-api-integration | View Transition API | panel geçişleri |
| ADR-049-startup-prompt-loader | Açılış prompt yükleyici | panel boot akışı |
| ADR-031-mobile-strategy-pwa-flutter | PWA + Flutter mobil stratejisi | Mobile kalemı |
| ADR-056-auth-module-implementation · ADR-058-centralized-auth-service | Auth modülü + merkezi servis | auth panel arka ucu |
| ADR-024-ecosystem-modular-docs | Ekosistem modüler dokümanlar | panel dokümantasyonu |
| ADR-096-kspace-5000-boundary-model | K-space V2 rejimi | format/bağımlılık kaynağı |

### §4.7 K010 Arayüz Sözleşmeleri (komşularla sınır)

| Komşu | Arayüz | K010'un verdiği | Beklenen | Kanıt |
|---|---|---|---|---|
| K009 API | ApiClient (tek kapı) | version'lı istek + `csrf_token` | BFF profili yanıt | §6A.5 · §5 K10 |
| K011 UX | render edilmiş DOM + varlık | tema/CSS varlık referansları | ITCSS/BEM sınıfları | §5 K11 "yalnız K10 tetikler" |
| K007 MIDDLEWARE | oturum/CSRF/RBAC | kimlikli istek + token | panelde credential yok | §21 · Guardrail #6 |
| K008 SERVICES | (dolaylı — K009 üzerinden) | — | doğrudan erişim YASAK | §5 K10 hard guardrail |
| K012 OBSERVABILITY | aksiyon/erişim olayları | panel olayları | toplama PLANNED | §5 K12 |

### §4.8 Durum Özeti (H1 — hedef ≠ kanıt)

| Ölçüm | Değer |
|---|---|
| Anayasa §5 K10 hedefi | 45 bileşen · 10 panel (HEDEF) |
| Repo kanıtı (ls 2026-10-08) | 3/10 panel dizini (auth, home, media) + auth routes/tests |
| Workflow kapsamı | W1 kısmi · W7/W8 tasarım-kanıtlı (ADR) · W2-W6 PLANNED |
| BFF bağı | SPA BFF VAR · Admin/Car BFF YOK (K009 §4.4) |
| Test | auth/home phpunit.xml + Playwright/Vitest configs (ls) |

### §4.9 Repo Panel Envanteri (ls 2026-10-08 — üç IMPLEMENTED panelin içeriği)

| Panel | Gözlenen yapı | Test | Durum |
|---|---|---|---|
| `auth.coremusic.net/` | index.php · autoload.php · routes/ · handler/ · include/ · pages/ · config/ · composer.json/lock · AGENTS/CLAUDE/CONTEXT/WORKFLOW.md | `tests/` + phpunit.xml (VAR) | IMPLEMENTED |
| `home.coremusic.net/` | autoload.php · header.php · footer.php · config/ · composer.json/lock · AGENTS/CLAUDE/CONTEXT/WORKFLOW.md | phpunit.xml (VAR) | IMPLEMENTED |
| `media.coremusic.net/` | src/ · config/ · bin/ · docs/ · composer.json · AGENTS/CLAUDE/CONTEXT/WORKFLOW.md | phpunit.xml GÖZLENMEDİ → ⚠️ | IMPLEMENTED (test gap) |
| music · admin · car · studio · download · landing · pro | — | — | PLANNED (dizin YOK) |
| `assets.coremusic.net/` (panel DEĞİL — K011 varlık servisi) | Css/ · js/ · Fonts/ · Image/ | vitest + playwright configs | K011 (border note) |
| `api.coremusic.net/` (panel DEĞİL — K009 gateway) | index.php · config/ · include/ · tests/ | phpunit.xml | K009 (border note) |

### §4.10 Panel ↔ Port/Register Eşlemesi (§11 Port Register ile)

| Port (§11) | Servis/panel | K010 ilişkisi | Durum |
|---|---|---|---|
| 80 | `admin.coremusic.net` (HTTP) | Admin panel | panel PLANNED · port tanımı vault'ta VAR |
| 81 | `music.coremusic.net` (Control) | Music panel + Control servisi | panel PLANNED · auth/home var |
| 3001 | `download.coremusic.net` | Download panel + servisi | PLANNED |
| 5000/6000 | `media.coremusic.net` | Media panel + servisi | IMPLEMENTED (dizin) |
| 9741/9742 | Audio servisi (REST/WS) | playback workflow (W2) | PLANNED (K008 §4.3.3) |

---

## §5 Bağımlılık & Sınır

### §5.1 İZİNLİ Bağımlılıklar (EK A aralık — alt katmanlar + port/adapter)

| Hedef | Yön | Gerekçe | Kanıt |
|---|---|---|---|
| K000-K008 (OS → SERVICES) | aşağı | anayasa §A.1 K010 "izinli=K000-K009" | `00-kspace-anayasa.md §A.1 K010` |
| **K009 API** | aşağı (TEK kapı) | "Yalnızca K9 API üzerinden iletişim kurabilir" — hard guardrail | `.ai/CLAUDE.md §5 K10` · §6A.5 |
| K011 UX (varlıklar) | yan/teslim | CSS/JS varlık teslimi — veri değil, kaynak varlık | `.ai/CLAUDE.md §5 K11` ("yalnız K10 tarafından tetiklenebilir") |
| port/adapter | yan | EK A istisnası (R6.3) | anayasa §A.1 |
| K006/K007 (dolaylı) | aşağı | kimlik/CSRF/RBAC K007 pipeline'ından gelir | §6 · §5 K6 |

### §5.2 YASAK Bağımlılıklar

| Yasak | Gerekçe | Kanıt |
|---|---|---|
| K010 → K008 servislerine doğrudan | hard guardrail: yalnız K9 API | `.ai/CLAUDE.md §5 K10` |
| K010 → K005 DB/SQL/PDO/Redis/FFmpeg/Cache/Filesystem | §6A.5 SPA yasağı (birebir listesi) | `.ai/CLAUDE.md §6A.5` |
| K010 → K009/K011 üst katmanlara geri çağrı (H20) | klasik yön | `rules.md R6.1` · ADR-096 §2 |
| `localStorage`/`sessionStorage` auth için | §21 forbidden patterns | `.ai/CLAUDE.md §21` |
| Panelde hardcoded secret | §21 · R17 U1 | `.ai/CLAUDE.md §21` |
| H19 doğrudan veri paylaşımı | veri sınırı ihlali | `rules.md R6.1` |
| `innerHTML` | DOMParser + TrustedTypes kullanılmalı | `.ai/CLAUDE.md §21` |
| `eval()` / `Function()` | güvenli alternatifler | `.ai/CLAUDE.md §21` |
| `var` | `const` / `let` | `.ai/CLAUDE.md §21` |
| Framework (React/Vue/Angular) | ADR-001 Vanilla JS | ADR-001 (accepted/ ls) · §21 |

### §5.3 Boundary Matrisi

| Boundary | K010 Tanımı | Komşu sahip |
|---|---|---|
| DATA_BOUNDARY | kalıcı veri YOK; görünüm durumu + oturum kimliği; auth storage yasağı | K008/K005 (kalıcı) |
| SECURITY_BOUNDARY | RBAC panel erişimi · admin ayrı yetki · credential panelde yok · BypassAuth test-only | K006/K007 |
| FAILURE_MODE | API DOWN → degrade panel · oturum dolumu → login köprü (ADR-047) · fail-over (EK A) | K009 (hata sözleşmesi) |
| CONTRACT boundary | yalnız ApiClient/OpenAPI üzerinden veri | K009 (sahip) |
| RUNTIME boundary | PHP server render + tarayıcı JS; Embedded hedefi RPi5 | K000 · K011 |
| Olay yukarı serbest | workflow adım olayları → K012; senkron geri çağrı yasak | K012 |

**Sınır ihlali prosedürü:** tespit → derhal revert + `log.md` CRITICAL + 👤 bilgi (R6.5 · anayasa §5.1).

### §5.4 Olay Akışı (R6.2)

```text
K012 OBSERVABILITY ← (panel/aksiyon logları, yukarı serbest) ← K010 panelleri
        ↑                                                        │
        │ (event)                                     [ApiClient — tek kapı ↓]
        └──────────── K008 (Event Bus) ←── K009 Gateway ←───────┘
                                                   │
                                     aşağı: K007 pipeline + K006 authz
```

| Akış | Yön | Kural | Kanıt |
|---|---|---|---|
| Panel → K009 ApiClient | aşağı (tek kapı) | hard guardrail | §5 K10 · §6A.5 |
| Panel → K008/K005 | — | YASAK (SPA yasağı) | §6A.5 |
| Panel → K011 varlık | yan | tema/CSS tetikleme | §5 K11 |
| Olay panel → K012 | yukarı | yayın serbest | `rules.md R6.2` |

### §5.5 Kardeş İlişki Kuralı (R6.4)

Kardeş KNNN'lerle ilişki yalnız `refers-to`; `depends-on` yalnız alt katman yönünde. K010 ↔ K011
(`refers-to` — varlık/doküman ilişkisi, veri bağımlılığı değil). Yatay `depends-on` → `dep-check` exit 1 (R6.6).

### §5.6 Panel ↔ Rol (RBAC) Eşlemesi (§6 #9 altı rol + §9 — türetme)

| Rol (§6 #9) | Panellere erişim | Not |
|---|---|---|
| regular | Landing · Music · Home · Media · Auth · Download | temel set (türetme ⚠️) |
| premium | regular + Pro | §9 #10 (türetme ⚠️) |
| studio | regular + Studio | §9 #9 (türetme ⚠️) |
| car | regular + Car | §9 #8 (türetme ⚠️) |
| admin | Admin (+ tümü) | §9 #3 · ayrı yetki (§5.3) |
| system | tümü (sistem/servis hesabı) | §6 #9 |

> Eşlemeler **türetmedir** (§6 #9 rol listesi + §9 panel listesi); rol→panel kararı kodda grep
> edilmedi → ⚠️. Yetki kararı K006'da, uygulaması K007 #9 (Permission) ve K010 yönlendirmesidir.

---

## §6 Girdi-Çıktı · Runtime · Observability-Test

### §6.1 Girdi → Çıktı Haritası

| Aşama | Girdi | Çıktı | Not |
|---|---|---|---|
| Panel render (PHP) | API yanıtı + tema | HTML/DOM | server-side: `header.php`/`footer.php` (home, ls) |
| Kullanıcı etkileşimi (JS) | form/touch/gesture | ApiClient isteği | Vanilla JS (ADR-001) |
| Workflow orkestrasyonu | adım durumu | sıradaki adım / event | §4.4 W1-W8 |
| Hata durumu | 4xx/5xx (K009 sözleşmesi) | degrade UI (retry/kart) | §6A |
| Olay yayını | workflow/aksiyon | event → K012 | R6.2 |

### §6.2 Runtime

| Öğe | Değer | Kanıt |
|---|---|---|
| Server stack | PHP 8.4 (strict_types) · Vanilla JS ES2022 · CSS ITCSS (K011) | §12 |
| Portlar | 80 (admin/landing) · 81 (music/home/studio/pro) · 3001 (download) · 5000/6000 (media) | §11 · §9 |
| Deployment yüzeyi | Home Media Center · Car (RPi5) · Studio · NAS · DAC (§14) | `.ai/CLAUDE.md §14` |
| View-mode | Home/Pro/Studio — her panel | §9 · ADR-045 |
| Mobil | PWA (ADR-031) — Flutter hedefi | ADR-031 (accepted/ ls) |

### §6.3 Observability

| Sinyal | Kaynak | Durum |
|---|---|---|
| Panel erişim/aksiyon logları | panel backend (PHP log) → K012 | PARTIAL (`coremusic_php_*.log` kökte ls) |
| Workflow adım olayları | panel JS → event | PLANNED ⚠️ (grep edilmedi) |
| Ön yüz hata yakalama | K012'ye devir | PLANNED ⚠️ |
| Kullanıcı analitiği (Matomo) | K012 alanı | PLANNED (K012 §4) |

### §6.4 Test

| Katman | Framework | Kanıt | Hedef |
|---|---|---|---|
| Auth paneli | PHPUnit 11 | `auth.coremusic.net/phpunit.xml` + `tests/` (ls) | ≥80% (§17) |
| Home paneli | PHPUnit 11 | `home.coremusic.net/phpunit.xml` (ls) | ≥80% |
| Media paneli | PHPUnit (composer VAR · phpunit.xml teyidi ⚠️) | `media.coremusic.net/composer.json` (ls) | ≥80% |
| Panel JS | Vitest | `assets.coremusic.net/vitest.config.js` (ls) | ≥80% (§17) |
| E2E akış | Playwright | `assets.coremusic.net/playwright.config.ts` (ls) | Kapı 11 / §17 |
| 7 eksik panel | — | altyapı yok (PLANNED) | ≥80% hedefi panel üretiminde açılır |

### §6.5 Failure Mode Senaryoları

| # | Senaryo | K010 davranışı | Kanıt |
|---|---|---|---|
| 1 | API erişilemez | degrade panel: hata kartı + retry | §6A · tasarım ⚠️ |
| 2 | Oturum dolumu (3600s) | login köprüsü ile geri dönüş | ADR-047 (accepted/ ls) · §6 #5 |
| 3 | Yetkisiz panel (rol) | 403 / yönlendirme (K007 #9) | §6 #9 |
| 4 | Workflow ortasında kesinti | durum korunur (cross-view state) | ADR-046 (accepted/ ls) |
| 5 | CSRF token eksik | istek reddedilir → form hatası | Guardrail #6 · K007 #6 |
| 6 | CSP nonce uyuşmazlığı | script yüklenmez → panel kırılır | ADR-012 · K007 §4.3.4 |
| 7 | Eksik BFF (Car/Admin) | panel veri alamaz → PLANNED/501 (tasarım ⚠️) | §6A.2 · K009 §4.4 |

### §6.6 Uyum & Kapı Bağlantısı (R11)

| Kapı/Kural | K010 durumu |
|---|---|
| KAPI 1 vault oku | Tam (anayasa §A.1 + §5/§9 + rules + ADR-096 okundu) |
| KAPI 9 hallucination | ⚠️ listesi §7.1 |
| KAPI 10 onay | BEKLİYOR — `status: draft` (R10) |
| Guardrail #11 (Mockup Before Frontend) | Bu dosya mimari katmanıdır; frontend kod üretiminde `.ai/ui-design/**` okuma zorunlu (bağlayıcı not §8) |
| §21 forbidden patterns | localStorage auth · framework · secret — yasak listesi §5.2'de |

### §6.7 Kabul Kriterleri (katman `ACTIVE` eşiği — R16.2 + R4.4)

| # | Kriter | Ölçüt | Mevcut |
|---|---|---|---|
| K1 | Özet satır 16 alan | tam dolu · İZİNLİ∩YASAK=∅ | ✓ (§2) |
| K2 | EK C 20 alan | tam dolu · KANIT 3'lü | ✓ (§3) |
| K3 | 10 panel + workflow kapsamı | her kalem Durum+Kanıt | ✓ (§4.1-§4.4) · 7 panel PLANNED işaretli |
| K4 | Tek kapı kilidi | "yalnız K9 API" hard guardrail belgeli | ✓ (§4.5 · §5.2) |
| K5 | Panel gerçeklik notu | hedef §9 ↔ repo ls ayrımı | ✓ (§4.3 · H10) |
| K6 | Web research (R9 3'lü) | ≥%80 kaynaklı | ✗ → ⚠️ (§7.1) |
| K7 | Durum geçişi | draft → ACTIVE = 👤 + kanıt | ✗ bekliyor (Kapı 10) |

### §6.8 View-Mode & Yükleme Politikası (ADR-045/093 — panel ötesi davranış)

| Kural | İçerik | Kanıt |
|---|---|---|
| Görünüm modu | Home · Pro · Studio — her panelde geçerli | `.ai/CLAUDE.md §9` (Görünüm Modları satırı) |
| Tek yükleme yolu | view-mode geçişleri tek load path üzerinden (çift yükleme yok) | ADR-093 (accepted/ ls) |
| Cross-view state | panel/görünüm geçişinde durum korunur | ADR-046 (accepted/ ls) |
| View Transition | geçiş animasyonu API entegrasyonu | ADR-048 (accepted/ ls) |
| Startup | açılış prompt/loader davranışı | ADR-049 (accepted/ ls) |
| CSS uygulaması | 09_ViewModes katmanı (varlık) | `assets.coremusic.net/Css/09_ViewModes/` (ls 2026-10-08) |

---

## §7 Kanıt Kaynakları

| # | Kaynak | Tür (R9) | Kullanım |
|---|---|---|---|
| 1 | `00-kspace-anayasa.md §A.1 K010 - APPLICATION «DÜĞÜM»` (+ §A.0) | dosya yolu (vault read 2026-10-08) | Sorumluluk/Sınır/Kanıt — ana kaynak |
| 2 | `.ai/CLAUDE.md §5 K10` · `§9 (10 panel + gerçeklik notu)` · `§11` · `§14` · `§6A.5` · `§21` · `§17` | dosya yolu (vault read) | K-matrix + panel tablosu |
| 3 | `auth.coremusic.net/` · `home.coremusic.net/` · `media.coremusic.net/` (+ YOK taraması: music/admin/car/studio/download/landing/pro) | repo ls (2026-10-08) | 3/10 IMPLEMENTED, 7 PLANNED |
| 4 | `.ai/.decisions/accepted/` ls: ADR-004/024/031/043/045/046/047/048/049/056/058/093/096 | ADR (ls teyitli) | karar atıfları |
| 5 | `rules.md R2/R3/R4/R6/R9` · `ADR-096 §2` | dosya yolu | format + yön |
| 6 | F1 EK B (37 URL) | URL (vault arşivi) | research üssü |
| 7 | Aşağıdaki boşluklar | ⚠️ VERIFICATION REQUIRED | R14 research kapısı |

### §7.1 Açık Kanıt Boşlukları (⚠️ defteri)

| # | Boşluk | Etkilenen alan | Kapanış yolu |
|---|---|---|---|
| G1 | 7 panel dizini repo'da yok (hedef) | §4.3 durum sütunu | panel üretimi planı · `git show HEAD:` (eski yapı) |
| G2 | Workflow adım/olay grep'i yapılmadı | §4.4 · EK C OBSERVABILITY | panel JS/PHP kod taraması |
| G3 | Ön yüz hata yakalama akışı | §6.3 | K012 ile ortak tasarım |
| G4 | Landing paneli kök yapısı | §4.3 #1 | kök dizin/landing araştırması |
| G5 | Web kanıtı (URL+tarih) | KANIT web ayağı | F1 EK B research kapısı (R14) |
| G6 | Mobile PWA varlıkları (service worker) | §4.1 Mobile | K011 PWA varlık taraması |
| G7 | `innerHTML`/`eval`/`var` grep'i (§21) | §5.2 forbidden iddiası | repo grep → 0 isabet beklenir |
| G8 | Rol→panel erişim kod eşlemesi | §5.6 | Permission/panel kod okuması |
| G9 | Theme engine panel entegrasyonu (ADR-044) | §6.2 view-mode | `shared/src/Theme` + panel grep |

**Kural:** Boşluklar dosyayı geçersiz kılmaz; `ACTIVE` için R4.4c/R16.2 kapanışı gerekir.

### §7.2 Kanıt Haritası (EK C alan → birincil kanıt)

| EK C alan | Birincil kanıt | İkincil kanıt | Üçüncül |
|---|---|---|---|
| SORUMLULUK | anayasa §A.1 K010 | `.ai/CLAUDE.md §5 K10 · §9` | repo §4.9 |
| RUNTIME | `.ai/CLAUDE.md §12 · §11` | repo composer/phpunit | ⚠️ |
| GIRDI/CIKTI | `.ai/CLAUDE.md §6A.5` | auth/home panel yapısı (ls) | ⚠️ |
| IZINLI/YASAK | anayasa §A.1 Sınır + `rules.md R6` | ADR-096 §2 | ⚠️ |
| DATA_BOUNDARY | §6A.5 · §21 (storage yasağı) | repo grep ⚠️ | ⚠️ |
| SECURITY_BOUNDARY | §5 K10/K6 · ADR-043/058 (ls) | auth panel tests (ls) | ⚠️ |
| FAILURE_MODE | ADR-047/046 (ls) | §6.5 senaryoları | ⚠️ |
| OBSERVABILITY | §5 K12 · kök `coremusic_php_*.log` (ls) | panel log grep ⚠️ | ⚠️ |
| TEST | auth/home phpunit.xml (ls) | vitest/playwright configs (ls) | ⚠️ |

---

## §8 İlişki & Değişiklik

**Kardeş katmanlar (Bant 1 · K000-K020 — düz metin, wiki-link YASAK, R2.4):**
Alt (izinli): K000 OS «TEMEL TAŞI» · K001 HARDWARE «DEMİRHANE» · K002 DRIVERS «KANAT» ·
K003 AUDIO ENGINE «ÇEKİRDEK» · K004 AI «MIKNATIS» · K005 DATA «FENER» · K006 SECURITY «ÇELİK KAPI» ·
K007 MIDDLEWARE «SUR» · K008 SERVICES «KULE» · K009 API «HÜCRE». Üst/yasak yön (H20): K011 UX «OMURGA» ·
K012 OBSERVABILITY «AYNA» · K013 CI/CD «PUSULA» · K014 NETWORK «ÇARK» · K015 MEDIA «MÜHÜR» ·
K016 AMPLIFIER «ZAR» · K017 POWER «KANTAR» · K018 THERMAL «MEZİT» · K019 PCB «ALEV» ·
K020 MANUFACTURING «BUZUL». Kardeşler arası ilişki yalnız `refers-to` (R6.4).

**Yatay not (K011 ile sınır):** anayasa §5 K11 satırı — "Sadece K10 tarafından tetiklenebilir":
K011 UX katmanı K010 olayları dışında kendiliğinden başlamaz; bu `refers-to`+tetik ilişkisidir,
`depends-on` DEĞİL.

**Frontend görevi notu (bağlayıcı):** Bu dosya mimari katmanıdır; gerçek frontend kod üretiminde
`.ai/ui-design/01-mockup-index.md` + `02-component-inventory.md` + `tokens/design-tokens-master.md`
okunmadan kod YASAK (Guardrail #11 — `.claude/CLAUDE.md §7.1`).

**İzinli wiki-linkler:** [[architecture/00-kspace-anayasa]] · [[architecture/rules]] · [[architecture/00-master-index]]

**Bant-1 üretim durumu (2026-10-08 · staging, vault'a yazılmadı):** b1-K007-middleware.md (K007) ·
b1-K008-servisler.md (K008) · b1-K009-api.md (K009) · b1-K010-uygulama.md (K010 — bu dosya) ·
b1-K011-ux.md (K011) · b1-K012-izleme.md (K012) · b1-K013-cicd.md (K013).

**Geçmiş:** 2026-10-08 ilk üretim (bant-1 · staging) | Vault Steward.

**Not (Kapı 10):** Bu dosya `draft`'tır; band-1 seti (K007-K013) birlikte 👤 onayına gider. Onay öncesi
§7.1 boşluklarının research kapısında kapatılması beklenir (R4.4c · R16.2 · R11 KAPI 9). Vault yazımı
ve `git commit` bu üretim kapsamında DEĞİLDİR (H5/H6).

**Kaynak bağımlılığı:** `depends-on: .ai/architecture/00-kspace-anayasa.md` — anayasa güncellenirse
bu kart §A.1 K010 satırıyla birlikte yeniden gözden geçirilir (R8.1 zinciri).

**Çapraz okuma:** K009 (API · §4.4 BFF×6) · K011 (UX · tetikleyici) · K007 (pipeline) kartları bu
dosyanın sınır tanımlarını tamamlar; birlikte okunur.