---
title: "CoreMusic — Master Prompt: Auth Arayüzü Figma & PNG SSOT Yeniden Yapılandırma"
type: prompt
category: ui-architecture
version: 1.0.0
status: pending-approval
authority: "prompt-maker v3.0.0 · SSOT: .ai/CLAUDE.md"
date: 2026-10-21
updated: 2026-10-21
governance: "Red Team · Human Mode · Truth Mode"
---

# Master Prompt: CoreMusic Auth Arayüzü Figma & PNG SSOT Yeniden Yapılandırma

---

### Bölüm 1 — /eli10 Analiz

CoreMusic kimlik doğrulama arayüzü (`auth.coremusic.net`), platformun kullanıcıyla buluştuğu ilk kapıdır ve ultra-premium cam morfizim (glassmorphism) ile pembe/romantik rüya atmosferi tasarım diline dayanır. Sistemdeki mevcut durum incelendiğinde, Figma ve `.ai/.png/shared-1024/` altında bulunan ekran tasarımlarının (`Linux 1024 - Login Girl.png`, `Linux 1024 - Select Gender.png`, `Linux 1024 - Register Girl.png` vb.) `1024x600` RPi5 dokunmatik ekranına göre piksel hassasiyetinde (`749px` sol kahraman alanı + `275px` sağ cam panel) modellendiği; ancak `1920x1080` ve üzeri masaüstü monitörlerde görüntülendiğinde mutlak piksel koordinatları ile responsive yerleşim arasında ölçek ve oran uyumsuzlukları oluştuğu tespit edilmiştir. Bu operasyon kapsamında, öncelikle `auth.coremusic.net/login` (Giriş) ekranından başlanacak; sol hero alanı (logo, tipografi, romantik arka plan scrim'i, footer), sağ cam panel (100x100 avatar illüstrasyonu, form başlıkları, 220px genişliğinde girdi alanları, şifre gizle/göster ikonu, Beni Hatırla onay kutusu, Şifremi Unuttum linki, 3x3 sosyal giriş ikon ızgarası ve alt sözleşme notu) Figma piksel koordinatları ve ASCII layout standartlarıyla birebir eşitlenecektir. Hibrit responsive mimari uygulanarak 1024px altında Figma piksel doğruluğu korunurken, 1920px masaüstünde sağ panel akışkan (`clamp(340px, 22vw, 440px)`) ve sol hero içerikleri estetik merkezli ölçeklenecektir. Her ekran tamamlandığında tarayıcı/DevTools üzerinden görsel ve CSS denetimi yapılacak; kullanıcı onayı alınmadan sonraki ekrana (`select-gender`, `register` 3 adım ve `admin` auth sayfaları) geçilmeyecektir.

---

### Bölüm 2 — İstenen Şey Nedir

`auth.coremusic.net` üzerindeki kimlik doğrulama sayfalarının (`login.php`, `select-gender.php`, `register.php`, `forgot-password.php`, `reset-password.php` ve ardından `pages/admin/*`), Figma veri kaynakları (`.ai/ui-design/reference/figma/png/`), ASCII spec belgeleri (`.ai/ui-design/screens/shared/`) ve referans PNG ekranları (`.ai/.png/shared-1024/`) ile birebir aynı görsel, responsive ve etkileşim kalitesine getirilmesi; her ekranın ayrı ayrı test edilip onay alınarak ilerlenmesi.

---

### Bölüm 3 — Vault Referansları (.ai/)

- `@.ai/CLAUDE.md` — §6 UI/UX & CSS standartları, §10 CSS Templates, ITCSS & `--cm-*` token kuralı
- `@.ai/AGENTS.md` — §6.1 Persona hiyerarşisi, §13 Reading Order & Mockup Gate
- `.ai/ui-design/screens/shared/login.md` — Login ekranı ASCII layout, koordinat kanıtı ve BEM sınıfları SSOT
- `.ai/ui-design/screens/shared/select-gender.md` — Cinsiyet seçimi ekranı ASCII layout ve BEM sınıfları SSOT
- `.ai/ui-design/screens/shared/register-step1.md`, `step2.md`, `step3.md` — Kayıt adımları spec belgeleri
- `.ai/ui-design/flow/auth/01-login.md` & `04-select-gender.md` — Karar ve akış şemaları
- `.ai/.png/shared-1024/Linux  1024 - Login Girl.png` — Login görsel SSOT
- `.ai/.png/shared-1024/Linux  1024 - Select Gender.png` — Cinsiyet seçimi görsel SSOT
- `.ai/.decisions/accepted/` — ADR-001 (Vanilla JS), ADR-047/ADR-058 (Merkezi Auth), ADR-010 (CSRF & Token)

---

### Bölüm 4 — Proje Kod Referansları

- `auth.coremusic.net/pages/login.php` — Giriş sayfası HTML/PHP iskeleti
- `auth.coremusic.net/pages/select-gender.php` — Cinsiyet seçimi sayfası HTML/PHP iskeleti
- `auth.coremusic.net/pages/register.php` — Kayıt sayfası HTML/PHP iskeleti
- `auth.coremusic.net/pages/admin/login.php` — Yönetici kimlik doğrulama sayfası
- `assets.coremusic.net/Css/auth-bundled.css` — Auth paketi ana import zinciri
- `assets.coremusic.net/Css/05_Pages/p-login-view.css` — Auth login ve ortak glassmorphism sayfa stilleri
- `assets.coremusic.net/Css/05_Pages/p-select-gender.css` — Cinsiyet seçim bileşen stilleri
- `assets.coremusic.net/Css/08_Devices/d-auth-desktop.css` — Masaüstü (1920px) cihaz override'ları
- `assets.coremusic.net/Css/08_Devices/d-auth-embedded.css` — 1024x600 RPi5 dokunmatik cihaz override'ları
- `assets.coremusic.net/Css/01_Abstracts/a-login-tokens.css` — Auth tasarım token'ları

---

### Bölüm 5 — Kullanıcı Kararları + Sorular + Cevaplar

| # | Soru (P Seviyesi) | Karar / Cevap |
|---|-------------------|---------------|
| 1 | **P0 Başlangıç Ekranı:** Hangi sayfadan başlansın? | **Doğrudan `auth.coremusic.net/login` sayfasından başla;** ardından sırasıyla `select-gender`, `register` (3 adım) ve diğer auth sayfalarına geç. |
| 2 | **P0 Responsive Stratejisi:** 1024px Figma ile 1920px Desktop uyumu nasıl sağlansın? | **Hibrit Responsive:** 1024px altında birebir Figma (749px hero + 275px dock); 1920px masaüstünde sağ panel ve sol hero metinleri orantılı, estetik ve kaymasız yerleşsin. |
| 3 | **P1 Kapsam:** Düzeltilecek auth sayfaları kapsamı nedir? | **Önce son kullanıcı auth sayfaları** (`login`, `select-gender`, `register`), ardından `auth.coremusic.net/pages/admin/` sayfaları sırayla düzeltilsin. |
| 4 | **P0 Test & Onay Prosedürü:** Ekran doğrulama kapısı nasıl işletilsin? | **Her ekran tek tek:** DevTools / tarayıcı görüntüsü alınıp Figma/PNG ile doğrulanarak kullanıcı onayı alınsın, onaysız sonraki ekrana geçilmesin. |

---

### Bölüm 6 — Orijinal Prompt (Değiştirilmeden)

```text
auth loginden başl bazı yerklde hataalr var .png ver figmöa data ile yanı olsun başla ####################################################################################
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
```

---

### Bölüm 7 — Görevlere Bölme (Task Breakdown)

1. **AŞAMA 1 — `auth.coremusic.net/login` (Giriş Ekranı Birebir Figma & PNG Hizalama):**
   - **T1.1 (Markup & Sözleşme):** `auth.coremusic.net/pages/login.php` içindeki bileşenlerin (`.lgn-hero`, `.lgn-panel`, form inputları, sosyal 3x3 ikonlar, footer metinleri) `.ai/ui-design/screens/shared/login.md` ASCII layout'una göre piksel ve semantik kontrolü.
   - **T1.2 (CSS & Cam Efekti):** `p-login-view.css`, `d-auth-embedded.css` (1024px) ve `d-auth-desktop.css` (1920px) kurallarının Figma SSOT (`.ai/.png/shared-1024/Linux  1024 - Login Girl.png`) ile eşitlenmesi:
     - Arka plan scrim geçişi, font aileleri (`Bickham Script`, `Plus Jakarta Sans`, `DM Sans`, `Avalon`).
     - Sağ cam panel (`rgba(255, 255, 255, 0.2)` + `backdrop-filter: blur(2px)`), input yükseklikleri, kenarlık renkleri (`#F200D0` 0.2 alpha stroke).
     - Sosyal giriş butonları (3x3 grid, 65x25 boyut, ikon ve kenarlık hizası).
   - **T1.3 (Doğrulama & Onay Kapısı):** Tarayıcıda `auth.coremusic.net/login` çağrılarak DevTools / ekran görüntüsü alınması, görsel karşılaştırmanın kullanıcıya sunulması ve onay kapısı.

2. **AŞAMA 2 — `auth.coremusic.net/select-gender` (Cinsiyet Seçimi Ekranı):**
   - **T2.1:** `select-gender.php` markup'ı ve `p-select-gender.css` stillerinin `.ai/ui-design/screens/shared/select-gender.md` ve `.ai/.png/shared-1024/Linux  1024 - Select Gender.png` ile hizalanması.
   - **T2.2:** Seçim kartları (`Kız`, `Erkek`, `Cinsiyetimi söylemek istemiyorum`), ikonlar, "Devam Et" butonunun disabled/active gradyan durumları, hero art illüstrasyonu (`login-text.png`) yerleşimi.
   - **T2.3:** Tarayıcı ekran görüntüsü ile doğrulama ve kullanıcı onayı kapısı.

3. **AŞAMA 3 — `auth.coremusic.net/register` (Kayıt Ekranı 3 Adım):**
   - **T3.1:** Step 1, Step 2 ve Step 3'ün `register.php`, `register-step1.md`, `step2.md`, `step3.md` spec'lerine ve PNG SSOT'larına göre responsive dizilimi.
   - **T3.2:** Doğrulama ve kullanıcı onayı kapısı.

4. **AŞAMA 4 — Diğer Kullanıcı Auth Ekranları:**
   - **T4.1:** `forgot-password.php` ve `reset-password.php` ekranlarının tutarlı cam morfizim diliyle tamamlanması ve onayı.

5. **AŞAMA 5 — Yönetici Auth Ekranları (`auth.coremusic.net/pages/admin/*`):**
   - **T5.1:** `admin/login.php`, `admin/register.php` ve diğer admin auth ekranlarının `p-admin-auth-light.css` / light glass diliyle kusursuzlaştırılması ve nihai sistem testi.

---

### Bölüm 8 — Kapanış

```text
✅ Prompt Cevaplarla İşlendi | Dil: Türkçe | Görev Sayısı: 5 | Cevap: 4
```
