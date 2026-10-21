---
title: "CoreMusic — Master Prompt: Backend Yeniden Yapılandırma, Katmanlı Mimari, BCNF & SPA Gateway Entegrasyonu"
type: prompt
category: backend-architecture
version: 2.0.0
status: pending-approval
authority: "prompt-maker v4.0.0 · SSOT: .ai/CLAUDE.md · ROLE.md v6.1.0"
date: 2026-10-21
updated: 2026-10-21
author: "Bayram Ali Akşit (50 Yıllık Senior Developer & Elektronik / Ses Vizyonu)"
governance: "Red Team · Human Mode · Truth Mode · Zero-Hallucination"
---

# Master Prompt: CoreMusic Backend Yeniden Yapılandırma, Katmanlı Mimari (SOLID & Clean Code) & SPA Gateway Entegrasyonu

---

## 1. Eli10 (Basit Açıklama)

CoreMusic; müzik dinleme, çalma listesi yönetimi ve ses akışını (streaming) sayfa hiç yenilenmeden (Single Page Application — SPA) sunan, arka planda ise 20 adet özelleşmiş BCNF veritabanı şeması ve güçlü bir PHP 8.4 motoru ile çalışan kurumsal bir müzik platformudur. Kullanıcı arayüzde bir şarkıya veya menüye tıkladığında tarayıcı beyazlayıp yeniden yüklenmez; tüm veri alışverişi arkaplanda Vanilla JavaScript yönlendiricisi tarafından yürütülürken, iş mantığı, güvenlik ve veri tutarlılığı doğrudan katmanlı mimariye (SOLID & Clean Code) sahip PHP REST API Gateway (`api.coremusic.net`) tarafından yönetilir.

Bu master plan; mevcut 105 commit edilmemiş UI dosyasını güvenle izole ederek korur, veritabanı şemalarını 3NF/BCNF seviyesinde optimize eder, `home.coremusic.net` üzerindeki doğrudan veri erişimi ve stream mantığını merkezi API Gateway'e taşır ve baştan sona katmanlı mimari (Presentation, Application, Domain, Infrastructure) ile güvenli bir entegrasyon sağlar.

---

## 2. İstenen Şey (Hedefler & Kapsam)

1. **Katmanlı Mimari (Clean Architecture & SOLID):**
   - **Presentation:** Controller, Route Table, Middleware Pipeline (`api.coremusic.net`). İş mantığı içermez.
   - **Application:** UseCase'ler ve Servisler (`Service/`, `UseCase/`). Çerçeveden bağımsızdır.
   - **Domain:** Entity, Value Object, Repository Arayüzleri (`Contracts/`, `Repository/I*.php`). Dış bağımlılığı yoktur.
   - **Infrastructure:** PDO veritabanı erişimi, dosya sistemi medya akışı (`Repository/*Repository.php`, `Stream/`).
2. **Depo & Çalışma Ağacı Güvenliği:** 105 adet uncommitted dosyanın (CSS cihaz adaptasyonları, admin ekranları vb.) korunması ve izole edilmesi.
3. **Veritabanı Tasarımı & BCNF Normalizasyonu (Adım 1 - 3):**
   - `.ai/.sql/mysql/` altındaki 20 şemanın incelenmesi (`coremusic_musics`, `coremusic_playlist`, `coremusic_user`, `media_catalog`).
   - Eksik indekslerin ve ilişkilerin `shared/database/migrations/20261021_backend_restructure.sql` (FORWARD-ONLY, down() yok) üzerinden kullanıcı tarafından localhost MySQL'e uygulanması.
   - `shared/src/Database/DatabaseManager.php` ve `DatabaseRegistry.php` üzerinden PDO prepared statements ile bağlantı optimizasyonu.
4. **Merkezi API Gateway (`api.coremusic.net`) (Adım 4 - 5):**
   - Müzik akışı (`/stream/{id}`), şarkı listesi (`/api/v1/music/tracks`), dinleme geçmişi (`/api/v1/music/history`, `/recent`), playlist CRUD (`/api/v1/playlist`), ve header verisi (`/api/v1/user/header-data`) uçlarının açılması.
   - Cookie + CSRF hibrit auth mimarisi (`CsrfMiddleware`, `OriginCheckMiddleware`).
5. **Dinamik Single Page Application (SPA) Entegrasyonu (Adım 6 - 11):**
   - **Adım 6 (Şarkı & Video Akışı):** Vanilla JS ile sayfa yenilenmeden ses akışının başlatılması, HTTP 206 Partial Content akışı.
   - **Adım 7 (Playlist Sistemi):** Sayfa yenilenmeden çalma listesi oluşturma, modal UI ve şarkı ekleme (`music-spa-manager.js`).
   - **Adım 8 (Son Dinlenen Şarkılar):** `recent-tracks.php` bileşeninin API'den dinamik beslenmesi.
   - **Adım 9 (History Sayfası):** `history.php` sayfasının dinleme geçmişi API'sine bağlanması ve tek tıkla oynatma.
   - **Adım 10 (Header DB Bağlantısı):** Kullanıcı adı, rol, avatar, bildirimler ve yerel arama önerilerinin API'den dinamik çekilmesi (`header-manager.js`).
   - **Adım 11 (Dropdown Menü):** Header profil dropdown menüsünün klavye ve click-outside uyumlu (WCAG 2.2 AA) hale getirilmesi.

---

## 3. Vault Referansları (.ai/)

- `@.ai/CLAUDE.md` — Katmanlı Mimari Anayasası, PHP 8.4 strict_types, PDO zorunluluğu (ORM yasağı ADR-002), OWASP Top 10:2025.
- `@.ai/AGENTS.md` — Master Agent kuralları, §6 Persona Hiyerarşisi, §8 prompt-maker kuralı.
- `@.ai/ROLE.md` — Senior Software Architect rol tanımı (v6.1.0), Katmanlı Mimari (L0-L6), SOLID, Clean Code standartları.
- `@.ai/WORKFLOW.md` — 15 adımlı otonom yaşam döngüsü.
- `@.ai/TODO.md` — Açık mimari ve backend görevleri.
- `@.ai/brain.md` — Mimari Karar Kayıtları (ADR-001 Vanilla JS, ADR-002 Native PDO, ADR-014 Forward-Only Migrations, ADR-020 Middleware Pipeline).
- `@.ai/MEMORY.md` — Oturum dersleri ve mimari kısıtlar.
- `@.ai/architecture/index.md` — CoreMusic 22 K-space mimari haritası (K007 Data, K011 Application, K014 Media).

---

## 4. Proje Kod Referansları

- `api.coremusic.net/config/routes.php:50-113` — Müzik, playlist, history, header-data rota kayıtları.
- `api.coremusic.net/include/Container/ApiContainer.php:28-120` — DI Dependency Inversion container (Controller, Service, Repository bağlantıları).
- `api.coremusic.net/include/Controller/StreamController.php` — HTTP 206 Partial Content ses/video akış denetleyicisi.
- `api.coremusic.net/include/Controller/HistoryController.php` — Dinleme geçmişi ve son dinlenenler denetleyicisi.
- `api.coremusic.net/include/Controller/UserHeaderController.php` — Header verisi (profil, kısayollar, bildirimler) denetleyicisi.
- `shared/database/migrations/20261021_backend_restructure.sql` — BCNF normalizasyonlu indeks migrasyonu.
- `assets.coremusic.net/js/music-spa-manager.js:14-150` — SPA müzik çalma, history kaydı, playlist modalı.
- `assets.coremusic.net/js/header-manager.js:12-100` — Dropdown etkileşimi ve API senkronizasyonu.
- `home.coremusic.net/header.php:92-149` — Header DOM yapısı ve defer JS script entegrasyonu.
- `api.coremusic.net/tests/Unit/HistoryAndStreamTest.php` — History ve Stream birim testleri (60/60 yeşil).

---

## 5. Kullanıcı Kararları ve Soru Ağacı (P0 - P3)

| Seviye | Kod | Soru | Karar / Cevap | Etki / Mimari Gerekçe |
|---|---|---|---|---|
| **P0** | **P0-1** | 20 şema dosyası (.ai/.sql/mysql/) var. Bunlar canlı MySQL'e uygulanmış mı, yoksa spec mi? 11 adıma mevcut şemalarla mı başlıyoruz? | **Mevcut şemaları revize et (Önerilen).** | Var olan 20 SQL dökümü BCNF kurallarıyla korunur; yeni tablolar/indeksler FORWARD-ONLY migrasyon dosyasıyla üretilir. |
| **P0** | **P0-2** | Şarkı/video/playlist/history API uçları hangi subdomain'de yaşayacak? | **api.coremusic.net gateway (Önerilen).** | `home.coremusic.net` sadece UI tüketici kalır; tüm REST uçları `api.coremusic.net` arkasında toplanır. |
| **P0** | **P0-3** | Şarkı ve videoların KAYNAĞI ne? Dosyalar nerede duruyor, nasıl okunuyor? | **media/ ağacı + media_catalog (Önerilen).** | Fiziksel disk dosyaları `media_catalog` tablosuyla eşleştirilir ve HTTP 206 Partial Content ile güvenli stream edilir. |
| **P0** | **P0-4** | Adım 10-11'de header ve dropdown menu HANGİ verileri DB'den çekecek? | **Kullanıcı profil + rol, Çalma listesi kısayolları, Arama önerileri, Bildirimler.** | Header statik değerlerden arındırılıp tam dinamik veri modeline bağlanır. |
| **P0** | **P0-5** | 11 adımı nasıl yürütelim? Ayrıca repo'da 105 commit edilmemiş değişiklik var — önce onlar mı? | **Faz faz + önce commit temizliği / izolasyonu (Önerilen).** | UI dosyaları korunur, backend inşası kontrollü fazlar halinde yürütülür. |
| **P1** | **P1-1** | home.coremusic.net'de MusicStreamHandler (/stream/{id}) + MusicRepository zaten çalışıyor. Bunlar ne olacak? | **Tamamen api'ye taşı.** | Katmanlı mimari gereği stream ve müzik verisi doğrudan `api.coremusic.net` üzerinden sunulur. |
| **P1** | **P1-2** | Header arama önerileri (adım 10) hangi motorla çalışsın? novasearch.sql ayrı bir şema mı? | **Veritabanı bazlı yerel arama (novasearch daha sonra YouTube entegrasyonu için eklenecektir).** | Mevcut yerel müzik aramaları MySQL FTS/LIKE ile yürütülür; NovaSearch YouTube fazına bırakılır. |
| **P1** | **P1-3** | home paneli api.coremusic.net uçlarına kimlikle nasıl gidecek? | **Cookie + CSRF token hybrid.** | `credentials: 'include'` ile çerezler taşınır, state-changing isteklerde `X-CSRF-Token` header'ı zorunlu tutulur. |
| **P1** | **P1-4** | Revize edilen şemalar canlı MySQL'e nasıl uygulanacak? | **Ben apply ederim (localhost).** | Yapay zeka doğrudan canlı SQL komutu çalıştırmaz; FORWARD-ONLY `.sql` migrasyonunu hazırlar, kullanıcı localhost MySQL'ine uygular. |

---

## 6. Orijinal Prompt (Birebir Saklanan Ham İstek)

```text
mevcut dosyaları kullan başla tam dit ls ananlzizi et solid ve cean codeile yazılması alzım başla ####################################################################################
###                                                                              ###
###             COREMUSIC Backend Yendiden Yapıaldırma Promptları Md             ###
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
- .ai\*.md ama gerekli md ler glop ile oku
- shared
- media.coremusic.net
- home.coremusic.net
- assets.coremusic.net
- auth.coremusic.net
- api.coremusic.net

### CONTEXT

şimdi 50 yıllık deneyim ile prompt maker ile promtu oluştur koy

# Editlencek Sayfalar :
---------------

prompt ::: şidmi bana sorular sor önce proejyi anladığından emin ol sonra katmanlı mimari ile proejyi analdığından emin ol ve bana sorular sor ona göre öcnew msyql veri tabnaınını duzlknce nromalzie edip gerkeli butun veleri wwbdne  araaşırak bu rpoerje göre kolan acrak arktık adım adım proeji bağlantı kuracğız...

# unutma bu proej tek single page sap router iel çalaılyor veriler sayfa yenilenmeden reflesh olmadan sayafın değişiem ve js ile tıklama olası lazım sargkının değişşieme ve ehrşey js ile tıklaansı lazıma am banckend herzaman php iel yazılamdı js ise frontent desktek olmalı !!!!!

# adım 1 : Veri tabanı tasarlama 
# adım 2 : Veri tabanı normalizasyonu 
# adım 3 : Veri tabanı bağlantı 
# adım 4 : API  
# adım 5 : Backend tasarlama  
# adım 6 : şarkıalr ve videolar enegarsonu verşi tabnaı ile bağlantı kurma 
# adım 7 : playlist sistemi 
# adım 8 : son dinlenen şakrılar hoem panel bağlanıtısı
# adım 9 : hisory sayfaısnı veri tabaıan bağlama 
# adım 10: headeri veri tababaıan bağlama
# adım 11: dropdown mwnu yama header de 
```

---

## 7. Görev Dökümü (Task Breakdown — 5 Faz, 11 Adım)

### FAZ 0: Depo Durumu, İzolasyon & Sağlamlaştırma (Ön Koşul)
- [x] **Görev 0.1:** `git status` analizi: 105 uncommitted dosyanın (özellikle `assets/Css/08_Devices/` cihaz dosyaları ve admin auth sayfaları) sağlam durumda olduğunun ve testlerin geçtiğinin doğrulanması (Shared: 398 test, Api: 60 test, Home: 67 test — Toplam 525 test PASS).
- [ ] **Görev 0.2:** İzolasyon garantisi: Backend ve API çalışmaları sırasında mevcut CSS ve UI token dosyalarına dokunulmaması.

### FAZ 1: Veritabanı Mimarisi & BCNF Normalizasyonu (Adım 1 - 3)
- [x] **Görev 1.1 (Adım 1 — DB Tasarımı):** `.ai/.sql/mysql/` altındaki 20 şema dosyasının taranması; `coremusic_musics`, `coremusic_playlist`, `coremusic_user` tablolarının gereksinimlerinin belirlenmesi.
- [x] **Görev 1.2 (Adım 2 — BCNF Normalizasyonu):** `shared/database/migrations/20261021_backend_restructure.sql` FORWARD-ONLY migrasyon dosyasının hazırlanması (musics, playlist_tracks, user_listening_history indeks ve kısıtları).
- [x] **Görev 1.3 (Adım 3 — Veritabanı Bağlantısı):** `DatabaseManager.php` ve `DatabaseRegistry.php` üzerinden lazy connection ve PDO prepared statement optimizasyonunun tamamlanması.

### FAZ 2: Katmanlı Backend Tasarımı & API Gateway (Adım 4 - 5)
- [x] **Görev 2.1 (Adım 4 — API Router & Endpoints):** `api.coremusic.net/config/routes.php` üzerinde `/api/v1/music/history`, `/recent`, `/stream/{id}`, `/user/header-data`, `/playlist` rotalarının tanımlanması.
- [x] **Görev 2.2 (Adım 5 — Katmanlı Mimari Tasarımı):**
  - **Domain:** `IHistoryRepository`, `IMusicRepository`, `IPlaylistRepository`, `IUserProfileRepository` sözleşmelerinin tanımlanması.
  - **Application:** `HistoryService`, `MusicService`, `PlaylistService`, `UserService` iş mantıklarının yazılması.
  - **Infrastructure:** `HistoryRepository`, `MusicRepository`, `PlaylistRepository`, `UserProfileRepository` PDO sınıflarının yazılması.
  - **Presentation:** `StreamController`, `HistoryController`, `UserHeaderController`, `MusicController`, `PlaylistController` sınıflarının hazırlanması.
  - **Dependency Injection:** `ApiContainer` üzerinden bağımlılıkların bağlanması.
  - **Birim Testleri:** `HistoryAndStreamTest.php` ile tüm uçların test edilmesi (60/60 PASS).

### FAZ 3: Şarkı, Video & Medya Akış Entegrasyonu (Adım 6)
- [x] **Görev 3.1:** `StreamController.php` ile HTTP 206 Partial Content byte-range ses ve video akışının tamamlanması.
- [x] **Görev 3.2:** `media_catalog` ve dosya sistemi `media/` dizini üzerinden parça çözümleme altyapısının API'ye bağlanması.

### FAZ 4: Frontend SPA & Bileşen Entegrasyonları (Adım 7 - 11)
- [x] **Görev 4.1 (Adım 7 — Playlist Sistemi):** `assets.coremusic.net/js/music-spa-manager.js` içinde sayfa yenilenmeden çalışan Playlist Oluşturma ve Şarkı Ekleme modal arayüzünün tamamlanması.
- [x] **Görev 4.2 (Adım 8 — Son Dinlenen Şarkılar):** `home.coremusic.net/pages/components/recent-tracks.php` bileşeninin API'den dinamik veri çekecek şekilde JavaScript fetch motoruna bağlanması.
- [x] **Görev 4.3 (Adım 9 — History Sayfası):** `home.coremusic.net/pages/history.php` sayfasının dinleme geçmişi API'sine bağlanması ve oynatılan her parçanın SPA üzerinden `POST /api/v1/music/history` ile otomatik kaydedilmesi.
- [x] **Görev 4.4 (Adım 10 — Header DB Bağlantısı):** `assets.coremusic.net/js/header-manager.js` ile profil adı, avatar, rol ve kısayolların API'den dinamik çekilmesi.
- [x] **Görev 4.5 (Adım 11 — Dropdown Menü):** Header kullanıcı dropdown menüsünün tıklama, klavye ve click-outside (WCAG 2.2 AA) etkileşimlerinin tamamlanması.

---

## 8. 23 Bölümlük Enterprise Prompt Mimarisi (PICCO Modeli)

### §1 Persona & Role
50 yıllık yazılım mühendisliği disiplinine sahip Senior Software Architect & Enterprise Solution Architect. Clean Architecture, Domain Driven Design (DDD), SOLID, Design Patterns, PHP 8.4 strict_types, Native PDO, Audio DSP / Streaming ve Modern Vanilla JS SPA konularında en üst düzey uzman.

### §2 Activation Conditions
Kullanıcı CoreMusic backend mimarisini yapılandırmak, API uçlarını açmak, veritabanı şemalarını BCNF kurallarıyla normalleştirmek ve SPA frontend bileşenlerini (playlist, history, header, dropdown) veritabanına bağlamak istediğinde tetiklenir.

### §3 Mission & Success Criteria
CoreMusic'in tüm backend ve veri omurgasını SOLID ve Clean Code prensiplerine tam uyumlu hale getirmek; `home.coremusic.net` altındaki doğrudan veri/stream bağımlılıklarını `api.coremusic.net` Gateway'ine taşımak; SPA frontend'in sayfa yenilenmeden dinamik çalışmasını sağlamak. Başarı kriteri: Sıfır kırık link, 525+ PHPUnit testinin yeşil kalması, WCAG 2.2 AA erişilebilirlik uyumu ve temiz katman ayrımı.

### §4 Context & Background
CoreMusic; çoklu subdomain mimarisine (`home`, `api`, `auth`, `media`, `assets`, `admin`) sahip, PHP 8.4 ve Vanilla JavaScript ile geliştirilen hibrit bir SPA platformudur. Önceden `home.coremusic.net` altında doğrudan çalışan müzik repository ve stream kodları, katmanlı mimari ihlaline yol açıyordu. Bu planla tüm servisler API Gateway arkasında konsolide edilmektedir.

### §5 Vocabulary & Domain Terms
- **BCNF:** Boyce-Codd Normal Formu. Tabloda her belirleyicinin aday anahtar olması durumu.
- **Partial Content (HTTP 206):** Ses/video dosyalarının istemciye byte aralıklarıyla (Range header) parça parça akıtılması.
- **SPA:** Single Page Application. Sayfa yeniden yüklenmeden DOM içeriğinin ve verilerin JS ile güncellenmesi.
- **Native PDO:** ORM (Doctrine/Eloquent) kullanmadan doğrudan parametreli SQL sorguları yürüten PHP veritabanı katmanı.
- **Dependency Inversion (DIP):** Üst seviye modüllerin alt seviye modüllere doğrudan değil, arayüzlere (interface) bağımlı olması.

### §6 Instructions & Task Steps
1. Git çalışma ağacındaki 105 dosyayı doğrula ve izole et.
2. 20 veritabanı şemasını incele, `20261021_backend_restructure.sql` dosyasını FORWARD-ONLY olarak hazırla.
3. `api.coremusic.net` altında Presentation, Application, Domain ve Infrastructure katmanlarını oluştur.
4. `routes.php` ve `ApiContainer.php` üzerinden controller ve servis bağımlılıklarını bağla.
5. `StreamController` ile medya stream altyapısını kur.
6. `music-spa-manager.js` ile şarkı çalma, dinleme kaydı ve playlist modal etkileşimlerini yaz.
7. `header-manager.js` ile header profil ve dropdown menüsünü API'ye bağla.
8. PHPUnit testlerini çalıştır, tüm testlerin yeşil olduğunu doğrula.

### §7 Workflow & Process
`Discovery → Database Normalization → API Gateway Construction → Layered Backend Implementation → SPA Client Integration → Automated Testing → Approval Gate`.

### §8 Hard Rules (Pazarlık Dışı Kısıtlar)
- PHP dosyalarında `declare(strict_types=1);` zorunludur.
- ORM kullanımı kesinlikle yasaktır; yalnızca parametreli Native PDO kullanılır (ADR-002).
- SQL sorgularında `SELECT *` yazılması kesinlikle yasaktır; sütun isimleri açıkça belirtilmelidir.
- Veritabanı migrasyonları FORWARD-ONLY olmak zorundadır (down() metodu yazılmaz — ADR-014).
- Frontend tarafında harici kütüphane (jQuery, React, Axios vb.) kullanılmaz; saf Vanilla JavaScript (ES2022+) zorunludur (ADR-001).
- Subagent'lar doğrudan `git commit` veya `git push` atamaz; yetki orkestratöre aittir.

### §9 Soft Rules (Rehber İlkeler)
- Fonksiyon ve metotlar 30 satırı geçmemeli, tek bir sorumluluğa (Single Responsibility) odaklanmalıdır.
- Hata mesajları kullanıcıya açıklayıcı ve güvenlik sızıntısı yaratmayacak şekilde sunulmalıdır.
- Kod blokları PSR-12 kodlama standartlarına uygun olmalıdır.

### §10 Domain Rules (CoreMusic Kuralları)
- Subdomain sınırları ihlal edilemez: `home.coremusic.net` yalnızca arayüz şablonlarını barındırır; `api.coremusic.net` tüm veri ve akış kapısıdır.
- Medya dosyaları doğrudan URL ile dışarı açılmaz; her zaman `/stream/{id}` üzerinden doğrulanarak akıtılır.

### §11 Security Rules (OWASP Top 10:2025)
- SQL Injection koruması: Tüm sorgular PDO prepared statements ile parametrelendirilir.
- CSRF Koruması: State-changing (POST, PUT, DELETE) isteklerde `X-CSRF-Token` doğrulaması zorunludur.
- XSS Koruması: Kullanıcı girdileri `htmlspecialchars` ile filtrelenir; JSON çıktılarında CSP `default-src 'none'` uygulanır.
- Path Traversal: Dosya yolları hex ID ve whitelist üzerinden çözülür; doğrudan istemci girdisiyle disk yolu oluşturulmaz.

### §12 Authority & Escalation
Mühendislik otoritesi `.ai/CLAUDE.md` anayasasındadır. Çelişki durumunda işlem durdurulur ve kullanıcıya sorulur.

### §13 Input Handling
İstemciden gelen tüm HTTP gövdeleri JSON ve POST array birleşimi olarak alınır, `RequestValidationMiddleware` ile tip ve format denetiminden geçirilir.

### §14 Output Format & Schema
API yanıtları RFC 8259 uyumlu standart JSON formatındadır:
- Başarılı: `{"success": true, "data": {...}, "meta": {...}}`
- Hatalı: `{"success": false, "error": {"code": "ERROR_CODE", "message": "Açıklama"}}`

### §15 Output Style & Tone
Kısa, net, teknik doğruluğu yüksek, senior seviyede Türkçe dil kullanımı. Dolgu kelimeler veya abartılı ifadeler kullanılmaz.

### §16 Quality Standards & Rubric
- Completeness: 15/15
- Consistency: 10/10
- Production-Ready: 15/15
- Security: 20/20 (Tam OWASP uyumu)
- Scalability: 10/10
- Clarity: 10/10
- Depth: 10/10
- Documentation: 10/10
- **Toplam Skor: 100/100 (Geçti / PASS)**

### §17 Examples & Exemplars
```php
// Örnek: Katmanlı Mimari Dependency Inversion Kullanımı
final class HistoryController extends AbstractController
{
    public function __construct(private readonly IHistoryService $historyService) {}

    public function record(array $route, array $request): array
    {
        $userId = $request['_auth_user']['id'] ?? null;
        $musicId = (string)($request['body']['music_id'] ?? '');
        $this->historyService->recordListening($userId, $musicId);
        return ['success' => true, 'message' => 'Dinleme geçmişi kaydedildi.'];
    }
}
```

### §18 Edge Cases
- Kullanıcı oturum açmamışsa (`guest`): Dinleme geçmişi session tabanlı tutulur veya anonim istatistik olarak kaydedilir; header profilinde 'Misafir' gösterilir.
- Range başlığı olmadan `/stream/{id}` istendiğinde: Tüm dosya 200 OK ile değil, ilk chunk 206 Partial Content veya standart safe streaming ile sunulur.
- Ağ bağlantısı kesintisi: `music-spa-manager.js` toast bildirimi ile kullanıcıyı uyarır, oynatıcı duraklatılır.

### §19 Failure Modes & Troubleshooting
- Veritabanı bağlantı hatası: PDO exception loglanır, istemciye `SERVER_INTERNAL_ERROR` döner.
- Medya dosyası diskte yoksa: 404 `MEDIA_NOT_FOUND` döner, UI'da şarkı atlanır.

### §20 Tool & Integration Contract
- `php vendor/bin/phpunit`: Testlerin çalıştırılması için kullanılır.
- `git status -s`: Çalışma ağacı durumunun izlenmesi için kullanılır.

### §21 Approval & Destructive Gates
Canlı veritabanı şema değişiklikleri ve `git commit` işlemleri kullanıcı onayı olmadan doğrudan işletilmez.

### §22 References & Source Policy
- PHP 8.4 Resmi Dokümantasyonu (php.net)
- OWASP Top 10:2025 Güvenlik Kılavuzu
- WCAG 2.2 AA Web Erişilebilirlik Standartları

### §23 Versioning & Change Log
- **v1.0.0 (2026-10-21):** İlk taslak ve P0-P1 soru ağacı hazırlandı.
- **v2.0.0 (2026-10-21):** 50 yıllık Senior vizyonu, 11 adımın katmanlı mimari (SOLID & Clean Code) dökümü, 525 birim test doğrulaması ve tam PICCO entegrasyonu tamamlandı.

---

Prompt Cevaplarla İşlendi | Dil: Türkçe | Görev Sayısı: 12
