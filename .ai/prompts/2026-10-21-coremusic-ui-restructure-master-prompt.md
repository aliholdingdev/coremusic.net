---
title: "CoreMusic — Master Prompt: Arayüz Tasarımı Yeniden Yapılandırma & Multi-Domain Mimari Hizalama"
type: prompt
category: ui-architecture
version: 1.1.0
status: pending-approval
authority: "prompt-maker v3.0.0 · SSOT: .ai/CLAUDE.md"
date: 2026-10-21
updated: 2026-10-21
governance: "Red Team · Human Mode · Truth Mode"
---

# Master Prompt: CoreMusic Arayüz Tasarımı Yeniden Yapılandırma (home, admin, auth) — Light Theme

---

### Bölüm 1 — /eli10 Analiz

CoreMusic çok alan adlı (multi-subdomain: `home.coremusic.net`, `admin.coremusic.net`, `auth.coremusic.net`, `assets.coremusic.net`) ve hibrit SPA (PHP backend shell render + Vanilla JS dinamik routing) mimarisine sahip kapsamlı bir müzik platformudur. Bu mimari yeniden yapılandırma çalışması, **Light Theme (Açık Tema — `html[data-mode="light"]`)** ana ekseninde üç temel alanda yürütülür:

1. **Merkezi Admin Kimlik Doğrulama (`auth.coremusic.net`) — Light Theme:** ADR-058 (Merkezi Auth Servisi) ve ADR-047 gereğince kimlik doğrulama tek otoritede toplanmıştır. Admin paneline ait kimlik doğrulama sayfaları (`login.php`, `register.php`, `forgot-password.php`, `reset-password.php`, `select-gender.php`, `set-gender.php`, `logout.php`), `auth.coremusic.net/pages/admin/` dizini altında izole edilecek; böylece admin ve son kullanıcı kimlik doğrulama alanları domain düzeyinde ayrıştırılırken, içerik yönetim sayfalarından (`admin-songs`, `admin-playlist` vb.) kesin hatlarla ayrılacaktır. Figma'da henüz özel ekranı bulunmayan admin şifre sıfırlama ve cinsiyet seçim adımları da dahil olmak üzere tüm admin auth arayüzü, **Light Theme** (`a-color-mode-tokens.css` ve `a-light-glass-tokens.css` — açık zemin `#f8f7fc`, beyaz cam kart `#ffffff`, net koyu metin `#1a1025`) tasarım diliyle üretilecektir.
2. **Yönetim Paneli İçerik ve Düzen Mimarisi (`admin.coremusic.net`) — Light Theme:** Şarkı yönetimi, şarkı ekleme, playlist ve sistem ayarları gibi CRUD ekranları (`admin-dashboard.php`, `admin-songs.php`, `admin-song-add.php`, `admin-playlist.php`, `admin-settings.php`, `layout.php`) `admin.coremusic.net` alan adı üzerinde kalacaktır. Bu ekranlar, Figma `2161:12438` masaüstü FHD (1920×1080) layout iskeletine sadık kalınarak, **Light Theme** renk moduna uyarlanacak; responsive kırılımlarda kusursuz çalışacaktır. Kimlik doğrulama oturumu ise `AdminAuthBridge` ve cross-domain oturum köprüsü ile `auth.coremusic.net` üzerinden transfer edilecektir.
3. **Kullanıcı Arayüzü & Responsive Grid Mimarisi (`home.coremusic.net`) — Light Theme:** Ana müzik akış arayüzü 1024px (tablet/küçük ekran), 1920px (FHD) ve 3840px (4K) çözünürlüklerinde **Light Theme** zemininde Figma `00-device-matrix.md` ve `01-mockup-index.md` sözleşmelerine göre hizalanacaktır. 1024px'te 2×2 + 1×5 + 1×5 (12 slot), 1920px'te 4×4 + 1×8 + 1×8 (20 slot) kanonik widget grid kuralı korunacak; player bar, sidebar ve içerik akışı responsive kırılmalarda layout shift yaşanmadan açık tema cam efektleriyle stabilize edilecektir.

Tüm stil (CSS) ve istemci betikleri (JS) katı bir biçimde `assets.coremusic.net` altında ITCSS (01_Abstracts → 11_OAuth) katman düzeninde tutulacak; `a-color-mode-tokens.css` altındaki `html[data-mode="light"]` tokenları ana referans alınacaktır. Geliştirme süreci kesin onay kapılarına (Hard Gate) bağlanmış olup, her sayfa PHPUnit sözleşme testleri ve DevTools tarayıcı görsel kontrolleriyle kanıtlanmadan bir sonraki sayfaya geçilmeyecektir.

---

### Bölüm 2 — İstenen Şey Nedir

`auth.coremusic.net`, `admin.coremusic.net` ve `home.coremusic.net` alan adlarındaki tüm arayüzlerin **Light Theme (Açık Tema)** standardında, Figma tasarım verilerine, `.ai/ui-design` spec'lerine ve `.ai/.png/` mockup'larına birebir sadık kalınarak;
- Sadece admin kimlik doğrulama akışlarının `auth.coremusic.net/pages/admin/` altına taşınması ve Light Theme olarak tasarlanması,
- Admin CRUD içerik sayfalarının `admin.coremusic.net` üzerinde Light Theme, Figma 1920×1080 ve responsive standartlara tam uyumlu hale getirilmesi,
- `home.coremusic.net` ekranlarının Light Theme responsive grid mimarisi ve bileşen hiyerarşisiyle optimize edilmesi,
- Varlıkların (CSS/JS) `assets.coremusic.net` ITCSS mimarisinde toplanması,
- Her sayfa ve bileşenin PHPUnit + DevTools tarayıcı testleriyle adım adım doğrulanarak onaylı şekilde teslim edilmesidir.

---

### Bölüm 3 — Vault Referansları (.ai/)

- `@.ai/CLAUDE.md` — Anayasa, §6A API-First, §6 Middleware zinciri, §18 BCNF & DB kuralları, §7 LINT
- `@.ai/AGENTS.md` — Master kurallar, §6 Swarm & Persona hiyerarşisi, §8 prompt-maker kuralı, §10 CSS ITCSS kuralları, §16 Değiştirilemez kullanıcı kuralları (Widget grid kanonik kuralı)
- `@.ai/WORKFLOW.md` — 15 adımlı otonom yaşam döngüsü, Hard Gate kuralları
- `@.ai/brain.md` — Sistem kabuğu, mimari kararlar ve ADR özetleri
- `.ai/.decisions/accepted/ADR-058-centralized-auth-service.md` — auth.coremusic.net merkezi yetki ve `validate-key` köprü mimarisi
- `.ai/.decisions/accepted/ADR-047-login-redirect-session-bridge.md` — Cross-domain session bridge protokolü
- `.ai/.decisions/accepted/ADR-001-vanilla-javascript-core.md` — Vanilla JS zorunluluğu (harici framework yasağı)
- `.ai/.decisions/accepted/ADR-002-database-layer-mysql.md` — Raw PDO, prepared statement zorunluluğu, ORM yasağı
- `.ai/.decisions/accepted/ADR-010-csrf-protection-strategy.md` — CSRF token standardı
- `.ai/.decisions/accepted/ADR-012-csp-nonce-strict-dynamic.md` — CSP strict-dynamic nonce mekanizması
- `.ai/ui-design/00-device-matrix.md` — Çözünürlük ve cihaz matrisi (T07, T17, T20)
- `.ai/ui-design/01-mockup-index.md` — Mockup dizini ve durum tablosu
- `.ai/ui-design/05-responsive-architecture.md` — Responsive kırılım ve grid mimarisi
- `.ai/ui-design/reference/figma/extracted-admin-1920.md` — Figma 2161:12438 Admin 1920 düğüm dökümü
- `.ai/ui-design/screens/T17-monitor-22fhd/admin-login.md` — Admin Login spec
- `.ai/ui-design/screens/T17-monitor-22fhd/admin-register.md` — Admin Register spec
- `.ai/ui-design/screens/T17-monitor-22fhd/admin-layout.md` — Admin Layout (Sidebar/Navbar/Content/Footer) spec
- `.ai/ui-design/screens/T17-monitor-22fhd/home-dashboard.md` — Home 1920 dashboard spec
- `.ai/.png/admin-1920/` — Admin mockup PNG referansları
- `.ai/.png/home-1920/` — Home mockup PNG referansları

---

### Bölüm 4 — Proje Kod Referansları

- `assets.coremusic.net/Css/01_Abstracts/a-color-mode-tokens.css` — `html[data-mode="light"]` renk ve yüzey tokenları SSOT
- `assets.coremusic.net/Css/01_Abstracts/a-light-glass-tokens.css` — Cam efekti ve açık tema yüzey tokenları
- `assets.coremusic.net/Css/01_Abstracts/a-admin-tokens.css` — Admin renk, boşluk ve tipografi token'ları
- `assets.coremusic.net/Css/01_Abstracts/a-tokens.css` — Genel tasarım token'ları
- `assets.coremusic.net/Css/04_Components/c-adm-*.css` — Admin BEM bileşenleri
- `assets.coremusic.net/Css/05_Pages/p-admin-*.css` — Admin sayfa özel stilleri
- `assets.coremusic.net/Css/08_Devices/d-admin-1920.css` — Admin 1920px responsive stil katmanı
- `assets.coremusic.net/Css/09_ViewModes/v-admin.css` — Admin görünüm modu kök CSS'i
- `assets.coremusic.net/js/admin/admin-shell.js` — Admin istemci taraflı kabuk ve etkileşim betiği
- `auth.coremusic.net/pages/` — Mevcut genel kullanıcı auth sayfaları
- `auth.coremusic.net/pages/admin/` — Yeni oluşturulacak admin auth dizini ve sayfaları
- `auth.coremusic.net/config/routes.php` — Auth yönlendirme tablosu (admin rotalarının eklenmesi)
- `admin.coremusic.net/pages/layout.php` — Admin ana kabuğu (navbar, sidebar, breadcrumb, footer)
- `admin.coremusic.net/pages/admin-dashboard.php` — Admin başlangıç paneli
- `admin.coremusic.net/pages/admin-songs.php` — Şarkı listeleme ve yönetim ekranı
- `admin.coremusic.net/pages/admin-song-add.php` — Şarkı yükleme/ekleme formu
- `admin.coremusic.net/pages/admin-playlist.php` — Çalma listesi yönetim ekranı
- `admin.coremusic.net/pages/admin-settings.php` — Panel ve sistem ayarları ekranı
- `admin.coremusic.net/include/Auth/AdminAuthBridge.php` — Cross-domain oturum doğrulama köprüsü
- `home.coremusic.net/pages/home.php` — Ana müzik akış sayfası
- `home.coremusic.net/pages/components/` — Home bileşenleri (`widget-grid.php`, `welcome-banner.php`, `recent-tracks.php`, `player-info.php`, `wide-sidebar.php`)
- `shared/src/PageRouter/PageRouterKernel.php` — Ortak SPA yönlendirici çekirdeği
- `shared/src/PageRouter/HtmlShellRenderer.php` — Ortak HTML kabuk üreticisi

---

### Bölüm 5 — Kullanıcı Kararları + Sorular + Cevaplar

| Seviye | Soru | Karar / Cevap | Gerekçe / Etki |
|---|---|---|---|
| **P0** (engelleyici) | [Mimari Ayrım] `admin-songs.php`, `admin-playlist.php`, `admin-settings.php` yönetim sayfalarıdır. Admin yönetim paneli içerik sayfaları nerede konumlanacak? | Sadece Kimlik Doğrulama (login, register, forgot-password, reset-password, logout, select-gender, set-gender) sayfaları `auth.coremusic.net/pages/admin/` altına taşınacak; şarkı, playlist ve ayar sayfaları `admin.coremusic.net` üzerinde kalacaktır. | ADR-058 tek yetkili auth mimarisine tam uyum; içerik yönetimi ile auth sorumlulukları net şekilde ayrılır (SRP). |
| **P0** (engelleyici) | [Aşama & Öncelik Sırası] home, admin ve auth alan adlarındaki 20+ ekran hangi aşama sırasıyla ele alınacak? | **Aşama 1:** `auth.coremusic.net` (admin auth klasörü ve sayfaları) → **Aşama 2:** `admin.coremusic.net` (layout, dashboard, songs, playlist, settings) → **Aşama 3:** `home.coremusic.net` ekranları. Her ekranda test ve onay. | Kademeli ilerleme; oturum transferi ve auth çalışmadan admin paneli doğrulanamaz. Hard Gate ile hata birikimi önlenir. |
| **P0** (engelleyici) | [Tema Modu] Arayüz teması hangi renk modunda inşa edilecek? | **Kesin Karar: Light Theme (Açık Tema — Dark DEĞİL).** `html[data-mode="light"]` tokenları uygulanacak. | Kullanıcı açıkça dark tema yerine light theme istedi; açık zeminler (`#f8f7fc`), beyaz cam kartlar (`#ffffff`), yüksek kontrastlı metinler (`#1a1025`) kullanılacak. |
| **P1** (kalite) | [Tasarım & Uyarlama] Figma'da admin için forgot-password, reset-password, select-gender, set-gender tasarımları yok. Bu sayfalar nasıl tasarlanacak? | Figma 'Admin - Login' ve 'Admin - Register' kart şablonu baz alınarak, **Light Theme** renk modu ve `a-admin-tokens.css` + `a-color-mode-tokens.css` tasarım diliyle tutarlı admin auth arayüzü üretilecektir. | Tasarım tutarlılığı korunur; eksik ekranlar sistemin genel açık tema diline organik olarak entegre edilir. |
| **P1** (kalite) | [Kalite & Kabul Kriteri] Her sayfa için bir sonrakine geçişi sağlayan 'Bitti ve Onay' (Definition of Done) kanıtı ne olacak? | PHPUnit birim/sözleşme testleri + Gerçek Tarayıcı (DevTools MCP) ile Light Theme görsel ve responsive (1024px/1920px) kontrolü + `assets.coremusic.net` CSS uyumu. | Regresyon önlenir; piksel doğruluğu, WCAG 2.2 AA kontrast erişilebilirliği ve DOM sözleşmesi garanti edilir. |

---

### Bölüm 6 — Orijinal Prompt (Değiştirilmeden)

```text
####################################################################################
###                                                                              ###
###         COREMUSIC Arayüz Tasaralam Yneden Yapıaldırma Promptları Md          ###
###                                                                              ###
###                                VERSION 1.0.0                                 ###
###                                                                              ###
###                       Designed by Bayram Ali Akşit                           ###
###                                                                              ###
####################################################################################

# **Şimdi ***COREMUSİC** MİMARİSİNİ YAPILANDIRACĞIZ değişireğiz editleyeğiz başlamak istiyorum**
# **Rol:** Senior Developer Yazılım uzmanı & Frontend Uzamanı & Senior Electronci Uzmanı
# **Deneyim:** 50 yıllık senior deneyim
# **Dil** Türkçe  ; 
## **Önce **Projeyi anla** Aşağıdakileri **dikattice uygula**
## *Kullılan Model:** **Mimo-2.6 & Mimo-2.6 pro** **xiomi** Aşağıdakileri **dikattice uygula**

- Temiz Katmanlı Mimari İle Senior Level Architecture Katmanlı Mimari İle **S** **O** **L** **I** **D** Clean Code prensipleri ile planla yap  prompt aşağıdadır

# **Şimdi ***COREMUSİC** MİMARİSİNİ YAPILANDIRACĞIZ değişireğiz editleyeğiz başlamak istiyorum**
# **Rol:** Senior Developer Yazılım uzmanı & Frontend Uzamanı & Senior Electronci Uzmanı
# **Deneyim:** 50 yıllık senior deneyim
# **Dil** Türkçe  ; 
## **Önce **Projeyi anla** Aşağıdakileri **dikattice uygula**
## *Kullılan Model:** **Mimo-2.6 & Mimo-2.6 pro** **xiomi** Aşağıdakileri **dikattice uygula**

- Temiz Katmanlı Mimari İle Senior Level Architecture Katmanlı Mimari İle **S** **O** **L** **I** **D** Clean Code prensipleri ile planla yap  prompt aşağıdadır

### LANGUAGES
- Alway Resonse Türkçe 
- Always Thinking Türkçe 
- Alway Thought Türkçe
- Alway Not Thought Engliish
- Always Not Thought Engliish 

### TOOLs
- prompt-maker : prompt oluşturma
- mem-search
- claude-mem
- Web search
- Exa Web Search
- chrome-devtools

### Skills
- prompt-maker : prompt oluşturma

### Read Files
- .ai\ROLE.md
- .ai\ARCHITECTURE.md
- .ai\architecture\index.md
- .ai\architecture\*.md ama gerekli md ler glop ile oku
- .ai\DECISIONS.md
- .ai\TODO.md
- .ai\TODOS\active.md
- .ai\MEMORY\decisions.md
- .ai\MEMORY\patterns.md
- .ai\MEMORY\lessons.md
- .ai\ui-design\*.md ama gerekli md ler glop ile oku

### CONTEXT

şimdi 50 yıllık deneyim ile prompt maker ile promtu oluştur koy

# Editlencek Sayfalar :
- home.coremusic.net
- admin.coremusic.net
- auth.coremusic.net

ama admin login  C:\www\coremusic.net\admin.coremusic.net\pages\admin-login.php
C:\www\coremusic.net\admin.coremusic.net\pages\admin-playlist.php
C:\www\coremusic.net\admin.coremusic.net\pages\admin-register.php
C:\www\coremusic.net\admin.coremusic.net\pages\admin-settings.php
C:\www\coremusic.net\admin.coremusic.net\pages\admin-song-add.php
C:\www\coremusic.net\admin.coremusic.net\pages\admin-songs.php sayfaalrı auth da olmaldı am söyel olmalı 

C:\www\coremusic.net\auth.coremusic.net\pages\admin
C:\www\coremusic.net\auth.coremusic.net\pages\admin\forgot-password.php
C:\www\coremusic.net\auth.coremusic.net\pages\admin\login.php
C:\www\coremusic.net\auth.coremusic.net\pages\admin\logout.php
C:\www\coremusic.net\auth.coremusic.net\pages\admin\register.php
C:\www\coremusic.net\auth.coremusic.net\pages\admin\reset-password.php
C:\www\coremusic.net\auth.coremusic.net\pages\admin\select-gender.php
C:\www\coremusic.net\auth.coremusic.net\pages\admin\set-gender.php

 şeklidne olamı başla sayfarı taşı ve eyden resposbnişev şekidel ayarla

 bazı sayfaların tasarımı yok onus en uyugun oalckas şekilde ayarla

prompt ::: butun figma data dan çek .ai ui-design ile figma data da Hepsini duzelt test et duzgun yapmadan diğerine gecme onay al her sayafadan başla .png & .md asci unciode art lardan ve .ai dizinindekilerle karşılaştır.

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🔄 ORCHESTRATOR — Prompt-Maker (questions modu)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📋 Görev: Figma → .ai/ui-design → tüm ekranlar ekran ekran düzeltme + test
🎯 Hedef: Tüm ekranların Figma/PNG'ye birebir uyması, her ünite onaylı
👥 Agent'lar (onaydan sonra): ui-designer · backend-architect · qa-engineer · security-engineer (auth)
📊 İlerleme: 1/4 ✅ Vault · 2/4 ✅ Keşif · 3/4 ✅ Kanıt · 4/4 ⏳ 6 karar
⏱️ Tahmini süre: ~8–10 saat (26 ünite × ~20 dk + kapılar; tahmin)
💰 Token kullanımı: UNKNOWN (sayaç yok)

# Questions
[Kullanıcı İlavesi: "ama light theme oalcka dfark değil"]
```

---

### Bölüm 7 — Görev Dökümü (Task Breakdown)

```text
AŞAMA 1 — auth.coremusic.net Admin Auth Mimarisi ve Sayfaları (Light Theme)
├── Görev 1.1: auth.coremusic.net/pages/admin/ dizin mimarisi ve routes.php entegrasyonu
├── Görev 1.2: Admin Login sayfası (Light Theme: Açık cam kart, yüksek kontrast, a-admin-tokens uyumu)
├── Görev 1.3: Admin Register sayfası (Light Theme: Form validasyonu, açık tema grid ve inputlar)
├── Görev 1.4: Admin Forgot Password & Reset Password sayfaları (Light Theme: Kart layout'u ve form akışı)
├── Görev 1.5: Admin Select Gender & Set Gender sayfaları (Light Theme: Cinsiyet seçim kartları ve cam butonlar)
├── Görev 1.6: Admin Logout ve oturum sonlandırma yönlendirme akışı
└── Görev 1.7: Aşama 1 Doğrulama Kapısı (PHPUnit + DevTools Light Theme görsel test + Kullanıcı Onayı)

AŞAMA 2 — admin.coremusic.net 1920 UI & CRUD Yönetim Paneli (Light Theme)
├── Görev 2.1: Admin Layout (Light Theme: Açık tema Sidebar, üst Navbar, breadcrumb ve Footer)
├── Görev 2.2: Admin Dashboard sayfası (Light Theme: Metrik kartları, istatistik grafikleri ve açık zemin)
├── Görev 2.3: Admin Songs sayfası (Light Theme: Şarkı tablosu, filtreleme alanları, aksiyon butonları)
├── Görev 2.4: Admin Song Add sayfası (Light Theme: Dosya yükleme alanı, form inputları, kapak seçici)
├── Görev 2.5: Admin Playlist sayfası (Light Theme: Liste düzenleme kartları, sürükle-bırak şarkı sırası)
├── Görev 2.6: Admin Settings sayfası (Light Theme: Sistem, ses motoru ve görünüm ayarları)
├── Görev 2.7: AdminAuthBridge cross-domain oturum entegrasyonu ve rota güvenlik denetimi
└── Görev 2.8: Aşama 2 Doğrulama Kapısı (PHPUnit + DevTools Light Theme tarayıcı testi + Kullanıcı Onayı)

AŞAMA 3 — home.coremusic.net Responsive Grid ve Ekran İyileştirmeleri (Light Theme)
├── Görev 3.1: Widget Grid kanonik kuralları (1024px: 2x2+1x5+1x5; 1920px: 4x4+1x8+1x8) Light Theme uyumu
├── Görev 3.2: Welcome banner, recent tracks ve diskplayer bileşenlerinin Light Theme cam efektleri
├── Görev 3.3: Nowplaying, browse ve history ekranlarının Light Theme Figma & .png mockup uyumu
├── Görev 3.4: 1024px tablet, 1920px masaüstü ve 3840px 4K layout shift kontrolleri (Açık mod)
└── Görev 3.5: Aşama 3 Doğrulama Kapısı (PHPUnit + DevTools tarayıcı testi + Kullanıcı Onayı)

AŞAMA 4 — Genel Doğrulama, assets.coremusic.net ITCSS Senkronu ve Kapanış
├── Görev 4.1: Tüm CSS varlıklarının assets.coremusic.net ITCSS (01->11) ve html[data-mode="light"] denetimi
├── Görev 4.2: Tüm JS varlıklarının Vanilla JS (ADR-001) ve strict CSP uyumluluk testi
├── Görev 4.3: E2E kullanıcı akış testi (auth -> admin ve auth -> home, Light Theme kesintisizliği)
└── Görev 4.4: Nihai Raporlama ve Dokümantasyon (.ai/prompts & .ai/log.md)
```

---

### Bölüm 8 — Kapanış İmzası

```text
✅ Prompt Cevaplarla İşlendi | Dil: Türkçe | Görev Sayısı: 24 | Cevap: 5 Karar Tamamlandı (Light Theme SSOT)
```
