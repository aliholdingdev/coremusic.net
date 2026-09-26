---
title: "CoreMusic — ADR-027: Dual-Mode Storage Strategy (Online + Offline Yerel Mod · Yerel Disk + Bulut Hibrit Depo · LWW Varsayılan + Listelerde CRDT · Sunucu SSOT · Özel Drive Benzeri Depo PLANNED · Google Drive + MEGA Opsiyonel PLANNED)"
type: adr
category: infrastructure
date: 2026-09-25
updated: 2026-09-25
version: 1.1.0
status: accepted
authority: ADR-027 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-027: Dual-Mode Storage Strategy (Online + Offline Yerel Mod · Yerel Disk + Bulut Hibrit Depo · LWW + CRDT Senkron · Sunucu SSOT · Özel Depo PLANNED)

**Durum:** accepted (kullanılabilir — **frozen YOK**)
**Tarih:** 2026-09-25
**Karar Veren:** Vault Steward (kullanıcı onaylı — "ADR-027'yi sıfırdan yaz"; karar içeriğinin tamamı kullanıcı onaylı: **(a) dual-mode = iki eksen** — (1) **online + offline yerel mod** (çevrimdışı dinleme/erişim, yerel cache), (2) **yerel disk + bulut hibrit depo** · **(b) senkron = LWW + sunucu SSOT varsayılan, bazı alanlarda CRDT** (playlist/etiket gibi çakışma hassas liste verisi), offline yazma bağlantı gelince çözülür, **SSOT = sunucu** (ADR-081 ruhu) · **(c) PLANNED — CoreMusic'in kendi Google Drive benzeri özel depolama sistemi**: kendi sunucumuzda, hem online (web) hem offline (yerel); başka PC'den erişim = online mod; ileride medya disk/database hibrit · **(d) PLANNED — 3. parti bulut opsiyonu: Google Drive API + MEGA** (kullanıcı: "ileride") · debate **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)**, Tech Lead **✅**)
**İlgili ADR'ler:** [[ADR-007-cache-namespace]] (sunucu cache katmanı — `shared/src/Cache/` 7 dosya IMPLEMENTED: `CacheManager.php:12-15` APCu varsa `ApcuAdapter`, yoksa `MemoryAdapter`; offline modun **sunucu tarafı** karşılığı bu ADR'nin namespace/TTL kurallarına bağlıdır; dosya diskte VAR ✅) · [[ADR-081-multi-provider-data-sync]] (SSOT + tek yazıcı — bu ADR'nin "sunucu = SSOT, LWW varsayılan" kararının dayanağı; `:102,166,177` LWW/merge çakışma çözümünün çoklu-yazıcıda reddedilmesi; dosya diskte VAR ✅) · [[ADR-026-download-service-architecture]] (indirme/lisans — offline erişimin sınırı: imza+lisans zinciri `:2.2a/g`, depo read-only ilkesi; dosya diskte VAR ✅) · [[ADR-011-session-management]] (oturum — çevrimdışı oturum/cookie davranışı bu ADR'nin hibrit saklamasıyla kesişir; dosya diskte VAR ✅ · **⚠️ "offline session" terimi ADR-011 metninde 0 eşleşme — kavram bu ADR'de tanımlanır, ADR-011'e atfedilmez**) · [[ADR-005-ultrathink-protocol]] (`⚠️ VERIFICATION REQUIRED` kanıt standardı) · [[ADR-024-ecosystem-modular-docs]] (wiki-link disk kanıtı kuralı) · [[ADR-002-pdo-mandatory-no-orm]] + [[ADR-022-database-hardened-security]] (senkron uç noktalarının erişim/şema katmanı) · karar dizini [[../index]] **satır 64** `[[ADR-027-dual-mode-storage-strategy]]` (slug eşleşmesi ✅).

> **Numara notu:** "Yeni ADR ≥ 088" kuralı bu yazımda uygulanmaz — `ADR-027-dual-mode-storage-strategy` karar dizini `../index.md:64`'te **rezerve boş slottur** (ADR-026 aynı istisnayı `:63`'te kaydetmişti). Ek kanıt: `brain.md:982` "ADR-027 | Hibrit depolama", `keys.md:262` "ADR-027 | dual-mode storage", `.ai/index.md:644` "Decisions/accepted/ADR-027-dual-mode-storage-strategy | Dual-mode storage" — üç katalog kaydı bu numarayı bağlar.

---

## 1. Bağlam (Context)

CoreMusic'in depolama stratejisi vault'ta **parça parça** duruyor: sunucu cache katmanı kodlanmış (`shared/src/Cache/`, ADR-007), senkron servisi **spec** olarak yazılmış (`sync-service.md` — CRDT/Conflict Resolver/Offline Sync Manager bölümleri, "Durum" satırında çelişkili işaretler), PWA/offline **spec** olarak yazılmış (`pwa-features.md` — "YYQ Planlama Aşamasında, 2026-Q4 → 2027-Q1"), dosya depolama **spec** yazılmış (`file-system-storage.md` — "stable" iddiası) — ama **kod tarafında** ne IndexedDB, ne service worker, ne `storage/`/`uploads/` dizini, ne Drive/MEGA entegrasyonu var. Aynı şekilde "offline mod" ve "yerel disk + bulut hibrit" birbirinden **bağımsız** iki karar gibi yazıldı; hangi verinin nerede duracağı, çakışınca ne olacağı ve SSOT'un kim olduğu hiçbir tek belgede toplanmadı. Bu ADR o kararı sabitler: **iki eksenli dual-mode** (erişim modu × depo konumu), **tek senkron sözleşmesi** (LWW varsayılan + listelerde CRDT + sunucu SSOT) ve **dürüst PLANNED etiketleri** (özel depo, Drive/MEGA).

### 1.1 Mevcut Durum

**A) KOD KATMANI — IMPLEMENTED (dar ama gerçek):**

| Tarama | Sonuç | Dosya:Ssatır |
|---|---|---|
| Sunucu cache adaptörleri | **IMPLEMENTED** — `CacheManager` (APCu varsa `ApcuAdapter`, yoksa `MemoryAdapter`), `ApcuAdapter`, `MemoryAdapter`, `PageCacheAdapter` + 2 arayüz = 7 dosya | `shared/src/Cache/CacheManager.php:12-15`, `ApcuAdapter.php:5`, `MemoryAdapter.php:5`, `PageCacheAdapter.php:5` |
| Çevrimdışı olay yakalama | **IMPLEMENTED** — `window 'offline'` dinleyicisi, hata durumu `offline`, hata tipi sabiti | `assets.coremusic.net/js/router/RouterEventManager.js:13`, `Router.js:58`, `router/config/error-types.js:7` |
| Tarayıcı yerel cache (küçük veri) | **IMPLEMENTED** — per-user sidebar localStorage (`getItem/setItem/removeItem/temizleme`), cinsiyet tercihi, modal kapatma, cihaz-katmanı senkron sayacı | `js/managers/SidebarManager.js:49,62,70,76-82`, `js/auth/gender-select.js:29,61`, `js/features/welcome-modal.js:30,55-58`, `js/device-loader.js:356-367` (sessionStorage) |
| Depo dizinleri (`storage/`, `uploads/`, `user_uploads/`) | **YOK** — üçü de `Test-Path = False` (bilinen halüsinasyon kontrolü H032) | kök dizin taraması |
| Medya/depo klasörleri | **YOK** — `home.coremusic.net/` yalnız `config|include|pages`, `shared/` yalnız `config|database|src|tests` | dizin listesi |

**B) SPEC/VAULT KATMANI — IMPLEMENTED (doküman var, kod yok):**

| İddia | Vault kanıtı | Kod karşılığı | Etiket |
|---|---|---|---|
| PWA + Service Worker + IndexedDB offline | `architecture/k11-ux/pwa-features.md:19-20` (`sw.js`, `sw-register.js`), `:150,176,319` (Workbox, SW kodu, kayıt), "Durum: YYQ Planlama Aşaması (2026-Q4 → 2027-Q1)" | `serviceWorker` **0** eşleşme (JS/HTML), `sw.js`/`manifest.json` **0 dosya**, `IndexedDB` **0** kod eşleşmesi | **PLANNED** |
| Offline senkron + CRDT + Conflict Resolver | `architecture/k8-servis/sync-service.md:18,20,466,562` (CRDT + operational transformation, Conflict Resolver, Offline Sync Manager) | senkron servisi kodu **0** | **PLANNED** |
| LWW (last-write-wins) | `architecture/k5-veri-yonetimi/README.md:108` "Conflict resolution (last-write-wins)"; `ADR-081:102,166,177` (çakışma çözümü yerine **tek yazıcı** tercih edilmiş) | uygulanmış LWW kodu **0** | **PLANNED (karar bu ADR)** |
| Dosya sistemi deposu (medya disk) | `architecture/k5-veri-yonetimi/file-system-storage.md:52-65` (dizin yapısı `user_uploads/{user_id}/…`), `:133` hedef yol kalıbı, `:369-370`, "stable / production-ready" | `storage/`, `uploads/`, `user_uploads/` **0 dizin** | **PLANNED** — "stable" iddiası disk kanıtsız → `⚠️ VERIFICATION REQUIRED` |
| Google Drive / bulut provider | `architecture/k8-servis/sync-service.md:640` "Cloud Provider \| External \| iCloud/Google Drive/Dropbox", `download-service.md:115` | `gdrive\|google.*drive` **0** eşleşme | **PLANNED** (kullanıcı onaylı, "ileride") |
| MEGA | — | `mega` **0** eşleşme (vault + kod) | **PLANNED** (yeni madde — kullanıcı onaylı) |
| CRDT | `sync-service.md:20` (tek vault geçişi) | **0** | **PLANNED** |
| Özel ("CoreMusic Drive") deposu | — (yok) | **0** | **PLANNED** (bu ADR ile kayıt altına alındı) |

**Sonuç etiketi:** **IMPLEMENTED:** sunucu cache (7 dosya), `offline` olay/hata yakalama, localStorage/sessionStorage küçük veri cache'i, vault spec'leri (PWA, sync/CRDT, file-system-storage). **PLANNED:** IndexedDB, service worker + Workbox + manifest, çevrimdışı medya indirme/çalma, sync servisi + Conflict Resolver, `user_uploads/` dizin yapısı, özel CoreMusic depolama sistemi, Google Drive API, MEGA, medya disk↔database hibrit. **`⚠️ VERIFICATION REQUIRED`:** (i) `file-system-storage.md` "stable/production-ready" iddiasının kod karşılığı 0 (dizin yok); (ii) `sync-service.md` "Durum" satırında "Tamamlandı" işaretleri kod 0 iken **spec seviyesi** olarak okunmalı; (iii) `pwa-features.md:471` planlama takvimi (2026-Q4/2027-Q1) onaysızdır; (iv) ADR-011'de "offline session" terimi **0 eşleşme**.

### 1.2 Sorun Tanımı

1. **İki karar birbirinden ayrı yazıldı:** "çevrimdışı mod" (erişim) ile "yerel disk + bulut hibrit" (konum) aynı belgede toplanmamış → hangi veri offline'da erişilir, hangi depoda durur belirsiz.
2. **Senkron sözleşmesi yok:** çakışınca ne olacak — LWW mi, CRDT mi, sunucu mu kazanır? `sync-service.md` CRDT der, `k5 README:108` LWW der, ADR-081 "tek yazıcı" der → üç ifade, tek karar yok.
3. **Offline erişim kodu 0:** IndexedDB/service worker yok; mevcut `offline` olayı yalnız **hata ekranı** gösterir (`Router.js:58`), dinleme/erişim **yapamaz**.
4. **Depo dizinleri hayali:** spec `user_uploads/` tarif eder, diskte dizin yok → "dosya yükleniyor" varsayımı kanıtsız.
5. **Bulut bağımlılığı kararı yok:** Drive/MEGA opsiyonel mi, birincil mi; quota/ücret/erişim riski kime ait — yazılı değil.
6. **Özel depo fikri kayıtsız:** kullanıcının "kendi Google Drive'ımız" talebi hiçbir vault dosyasında yok → bu ADR ile kayıt altına alınır (PLANNED).
7. **Offline lisans/erişim:** ADR-026 imza zinciri süre/erişim kontrolüne dayanır; bağlantı yokken erişim sınırı ne olacak — açık değil.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (diskte VAR ✅) — resmi/anahtar kaynak önce (MDN, web.dev, Google developers docs, mega.io resmi SDK, W3C), **her ana iddia ≥2 bağımsız çapraz kaynak**, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`. **Odak: (a) offline-first 2025-26, (b) service worker + IndexedDB cache, (c) LWW vs CRDT, (d) tarayıcı kota/eviction, (e) Google Drive API kotaları, (f) MEGA API.** Erişim: **6 websearch sorgusu** (biri başarısız → yeniden yazıldı); sonuçlar başlık/özet düzeyinde derlendi — derin sayfa-içi tur ayrı doğrulamaya bırakıldı (açıkça işaretli).

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "offline-first architecture 2025 service worker IndexedDB caching best practices" · (2) "CRDT vs last-write-wins conflict resolution offline data 2025 comparison" (1. deneme hata verdi → yeniden yazıldı: "CRDT vs last-write-wins conflict resolution collaborative offline sync pros cons") · (3) "Google Drive API storage quota limits files upload 2025 documentation" · (4) "MEGA storage API developer documentation file upload API limits" · (5) "navigator.storage.estimate persistent storage quota browser IndexedDB eviction 2025" · (6) (1'in çıktısındaki başlıklar üzerinden derin okuma: LogRocket 2025 offline-first, MDN quota, web.dev storage) |
| Web Search **Konusu** | (1) Offline-first mimari + SW/IndexedDB cache stratejileri (cache-first / network-first / SWR); (2) çakışma çözümü — LWW'nin veri kaybı ile CRDT'nin metadata/karmaşıklık bedeli; (3) Google Drive API günlük kotları ve dosya boyutu sınırları; (4) MEGA SDK/API kota davranışı (`EOVERQUOTA`); (5) tarayıcı depolama kotası, `navigator.storage.estimate()/persist()`, eviction; (6) IndexedDB vs localStorage kullanımı (2026). |
| Web Search **Bağlam** | **~34 adlandırılmış kaynak / 6 sorgu**: **MDN** (Storage quotas and eviction criteria, Cache API, offline event), **web.dev** (storage-for-the-web, persistent-storage), **WebKit blog** (Safari 17 storage policy), **LogRocket** ("Offline-first frontend apps in 2025: IndexedDB and SQLite"), **dev.to** (offline/PWA cache stratejileri), **OneUptime** (offline capabilities + "How to Implement Last-Write-Wins" 2026), **signaldb.js.org**, **javascript.plainenglish** (SW cache strategies), **github pazguille/offline-first**, **Medium rackis** (SW + IndexedDB) (12) · **Hacker News "Downsides of Offline First"** (LWW = "washing your hands of it" / "LWW is a simple CRDT merge type"; Apple Notes state-based CRDT örneği), **Ditto** (LWW offline'da meşru değişimi siler), **iankduncan.com CRDT Dictionary (2025-11-27)** (CRDT metadata bedeli), **Wikipedia CRDT**, **r/rust "You might not need a CRDT"**, **TMPDIR forum**, **Medium conflict-resolution** (version vectors) (7) · **Google developers — Drive Usage limits** (1 TB/proje-gün egress, 750 GB/gün yükleme, 5 TB tek dosya), **Google Workspace knowledge** (upload/copy limits), **support.google.com** (shared drive limits), **Stack Overflow** (service account kota yok → shared drive), **discuss.google.dev** (`storageQuotaExceeded`), **community.latenode** (rate limit), **docs.cloud.google.com** (App Engine dosya limiti), **r/DataHoarder** (8) · **mega.io/developers (resmi SDK — `EOVERQUOTA`)**, **mega.js.org (MEGApi docs — `.upload()`)**, **r/MEGA** (dosya sayısı sınırsız, indirme kotası) (3) · **RxDB** (IndexedDB max storage), **diragb.dev** (IndexedDB vs localStorage 2026), **mindstick**, **Stack Overflow** (persistent storage denied), **SilverBullet** (mobil ~50 MB/origin) (4). |
| Web Search **Kısa Açıklama** | **(1) Offline-first:** SW statik kabuğu cache-first, API'yi network-first + cache fallback ile servis eder; IndexedDB yapılandırılmış büyük veri için (ACID transaction), localStorage yalnız küçük tercih/flag içindir; write'lar `_syncStatus: pending` + syncQueue ile kuyruklanır, bağlantı gelince flush edilir (OneUptime, LogRocket 2025, dev.to, signaldb). **(2) LWW vs CRDT:** LWW en basit ve en yaygın stratejidir ama **eşzamanlı yazımı kaybeder** ("last write wins is not handling data sync, it's washing your hands of it" — HN; "LWW discards legitimate changes in offline" — Ditto); CRDT koordinasyonsuz **yakınsama** sağlar ama bedeli metadata + eventual consistency + tip-bazlı tasarım'dir (CRDT Dictionary: "trade coordination for metadata"); "LWW is a simple CRDT merge type — iyi avatar foto için, kötü sayaç için" (HN sroussey). **(3) Drive API:** proje başına **1 TB/gün egress**, kullanıcı başına **750 GB/gün yükleme**, tek dosya **5 TB**, kopyalama 750 GB; service account'un kendi deposu yok (shared drive şart) (Google docs). **(4) MEGA:** resmi SDK'da kota aşımı **`EOVERQUOTA`** ile reddedilir; dosya sayısı sınırsız, indirme/trafik kotaya bağlı (mega.io, r/MEGA). **(5) Kota/eviction:** `navigator.storage.estimate()` kota/usage verir; kalıcı mod `navigator.storage.persist()` ister; kota aşımında IndexedDB yazımı `QuotaExceededError` fırlatır; veri kullanıcı/sistem tarafından silinebilir (MDN, web.dev, WebKit). **(6)** localStorage ~MB, IndexedDB GB ölçeğine kadar (LogRocket/MDN). |
| Web Search **Uzun Açıklama** | **(a) Offline-first mimari (kaynak 1-12):** 2025-26 pratikte üç katman üzerine kurulu: **Service Worker** (app shell + statik varlık cache-first, API network-first, offline.html fallback, background sync ile `sync` event'i), **IndexedDB** (kayıtlar + `syncStatus` indeksi + kuyruk tablosu, ACID transaction), **kuyruklu yazma** (offline write → pending → bağlantı gelince gönder). Cache stratejisi seçimi veri tipine göre: statik/sürüm-li kabuk = cache-first (sürüm ön eki yoksa eski JS sonsuz servis riski), dinamik veri = network-first, feed/takvim = stale-while-revalidate; `maxEntries`/`maxAgeSeconds` **zorunlu** (yoksa kota şişer), POST yanıtı Cache API'de saklanmaz (GET varsayılan), Cache API + IndexedDB **aynı kota** payını kullanır. LogRocket 2025 notu: SQLite-in-browser (WASM sync engine) IndexedDB'ye alternatif olarak yükseliyor — ama ek ağırlık demek. **(b) Çakışma çözümü (kaynak 13-19):** LWW zaman damgası en büyüğün kazanmasıdır; uygulaması ucuz, **veri kaybı üretir** (eşzamanlı edit'lerden biri silinir) ve `Date.now()` kırılganlığı (saat sapması = yanlış kazanan) vardır; version vector ekleyerek "concurrent" tespiti + conflict sinyali vermek LWW'i dayanıklı yapar (OneUptime). CRDT ise koordinasyon olmadan yakınsar (state-based: Apple Notes; op-based: sayaç/set); **bedeli**: metadata şişmesi, tip-bazlı merge (register/set/counter/list ayrı), eventual consistency'nin UI'a yansıması, test yüzeyi. Tartışma özeti: **"CRDT her yerde gerekli değil; veri tipine göre seç"** — sayaç/liste paylaşımında CRDT, tek alanlı profil/avatar/meta ayarlarda LWW yeterli. CoreMusic için playlist/etiket = **sıra + üyelik listesi** → eleme tabanlı LWW kaybı kabul edilemez → CRDT (yayınlama/eleme birleşimi); ayarlar/profil/tercih → LWW. **(c) Drive/MEGA (kaynak 20-28):** Drive API'si güçlü ama **kotalı**: 750 GB/gün yükleme, 1 TB/gün proje egress'i, günlük sayaçlar dakika-bazlı istek kotasıyla birlikte work eder; service account kişisel kota taşımaz → shared drive + `supportsAllDrives=true` gerekir; `storageQuotaExceeded` runtime hatasıdır. MEGA SDK kota aşımını `EOVERQUOTA` ile bildirir → entegrasyon **kota-uyumlu hata dalı** ister. İki API de resmi ve kullanılabilir; **ama ikisi de 3. partiye veri/erişim bağımlılığı yaratır** (lisans, gizlilik, fiyat değişimi). **(d) Kota/eviction (kaynak 29-34):** Origin kotası tarayıcı cihazına göre değişir (Chrome ~%60 boş disk, Firefox persistent modda 250 GiB, mobilde ~50 MB/origin'e kadar düşebilen raporlar var) → **her zaman `estimate()` ile ölç + `QuotaExceededError` yakala + `persist()` ile kalıcı mod iste**; kalıcı olmayan veri (özellikle mobilde) silinebilir → offline medya kitaplığı için "yerel disk (uygulama/CIHAZ deposu) + sunucu" hibrit'i gerekçelidir. |
| Web Search **Paragraf Veri Uzun** | Offline-first 2025-26'da **kabuk + veri + kuyruk** üçlüsüyle kurulur: service worker statik kabuğu cache-first servis eder (sürüm ön eki + activate'te eski cache temizliği), API istekleri network-first olup bağlantı yokken cache'e düşer, yapılandırılmış veri IndexedDB'de `_syncStatus` alanıyla işaretlenir ve bağlantı gelince kuyruk boşaltılır; localStorage yalnız küçük tercihler içindir ve GB ölçeğinde değildir. Çakışma çözümü **veri tipine göre** seçilmelidir: LWW en ucuz yoldur ama eşzamanlı eşitlikte **bir yazımı siler** (HN, Ditto) ve saat bağımlıdır; CRDT koordinasyonsuz yakınsama verir ancak metadata + eventual consistency + tip-bazlı merge maliyeti taşır (CRDT Dictionary) — pratik uzlaşma **varsayılan LWW + veri kaybı kabul edilemeyen listelerde CRDT + her zaman sunucu SSOT** (sunucu son yazıyı tüm istemcilere yayınlar; istemci çakışmasını gidermezse bile **tek yazıcı** ile yapısal olarak önler, ADR-081). Depolama tarafında tarayıcı kotası öngörülemez (`estimate()` ile ölçülür, `persist()` ile kalıcı hale getirilir, aşımda `QuotaExceededError` verir ve mobilde eviction riski vardır) → çevrimdışı medya tamponu **sadece** yerel/istemci depolamasına bırakılamaz, sunucu deposu (yerel disk) + bulut hibrit'i gerekir. 3. parti sağlayıcılar (Google Drive API, MEGA) işlevsel olarak hazır ve resmi SDK'ları var (Drive: 750 GB/gün yükleme + 1 TB/gün proje egress + 5 TB tek dosya + shared drive zorunluluğu; MEGA: `EOVERQUOTA`) → **opsiyonel katman** olarak eklenebilir; birincil yaparlarsa fiyat/kota/erişim değişimi CoreMusic'e doğrudan bağımlılık olarak döner. |
| Web Search **Sonucu** | 1) **Offline-first + SW/IndexedDB deseni doğrulandı** (MDN, web.dev, LogRocket 2025, OneUptime, dev.to, signaldb) → **§2.2a** (kabuk/veri/kuyruk katmanları + cache strateji tablosu). 2) **LWW'nin listelerde veri kaybı ürettiği doğrulandı** (HN, Ditto, r/rust) ve **CRDT'nin bedeli doğrulandı** (CRDT Dictionary, Wikipedia) → **§2.2b** (varsayılan LWW, playlist/etiket CRDT). 3) **Sunucu SSOT + tek yazıcı yaklaşımı endüstri tartışmasıyla uyumlu** (HN: "source of truth is the server"; ADR-081 tek yazıcı) → **§2.2b**. 4) **Tarayıcı kota/eviction gerçekliği doğrulandı** (MDN, web.dev, WebKit, RxDB) → **§2.2c** + risk 1/2 (`estimate`/`persist`/`QuotaExceededError`). 5) **Drive API kotaları doğrulandı** (Google docs ×3) → **§2.2d PLANNED** + risk 3. 6) **MEGA `EOVERQUOTA` davranışı doğrulandı** (mega.io, mega.js.org, r/MEGA) → **§2.2d PLANNED** + risk 3. 7) **IndexedDB localStorage'a göre doğru araç** (MDN, diragb.dev, LogRocket) → **§5.1/2**. **Toplam ~34 adlandırılmış kaynak, 6 sorgu**; iki sınır açıkça işaretlendi: **⚠️** (i) PWA/offline **kod karşılığı 0** olduğundan performans/eviction davranışları bizim uygulamada **ölçülmedi** (§5.1/6 ölçüm); (ii) **sayfa-içi tur yapılmadı** → Drive API'nin güncel fiyat/egress politikası ve MEGA'nın resmi API sürümü üretim öncesi tekrar doğrulanır (§5.1/8). |
| Web Search **Alınan Karar** | **ADR-027 KABUL EDİLİR — DUAL-MODE DEPOLAMA DÖRT MADDE:** **(a) İki eksen:** **Eksen 1 — erişim modu:** *online mod* (normal API + stream + ADR-026 imza zinciri) ve *offline yerel mod* (çevrimdışı dinleme/erişim: app shell cache-first + IndexedDB veri + kuyruklu yazma; mevcut `offline` olayı yalnız hata ekranı gösterir → genişletilir). **Eksen 2 — depo konumu:** *yerel disk* (sunucu medya deposu + `file-system-storage.md` yapısı; kullanıcı cihazında seçili medya tamponu) ve *bulut* (**birincil: CoreMusic'in kendi özel deposu** — kendi sunucumuzda, hem web'de online hem yerelde offline; **ikincil/opsiyonel: Google Drive API + MEGA** = PLANNED 3. parti kopya/overflow). **(b) Senkron sözleşmesi:** **varsayılan LWW + sunucu SSOT** (`updated_at`/sürüm tabanlı; istemci yarışı sunucuda çözülür); **çakışma hassas listelerde CRDT** (playlist sıra/üyelik, etiket/koleksiyon — eleme tabanlı LWW kaybı yasak); **offline yazma → kuyruk → bağlantı gelince gönderim**; **SSOT = sunucu** (ADR-081 tek yazıcı ruhu — istemci asla nihai doğruluk kaynağı değildir). **(c) PLANNED — özel CoreMusic deposu ("kendi Drive'ımız"):** kendi sunucumuzda, online (web) + offline (yerel) erişim; **başka PC'den erişim = online mod** (yerel mod yalnız oturumun cihazında); ileride **medya disk ↔ database hibrit** (file_hash + DB metadata + disk blob). **(d) PLANNED — 3. parti opsiyon:** Google Drive API ve MEGA entegrasyonu (yedek/overflow; `EOVERQUOTA`/kota hata dalları zorunlu); **birincil DEĞİL**. |
| Web Search **Sonuç** | Karar 2025-2026 verisiyle **desteklendi**: offline-first katmanları (6), LWW/CRDT (7), Drive kotaları (8), MEGA API (3), kota/eviction (6), IndexedDB/localStorage (4) → **~34 adlandırılmış kaynak, 6 sorgu**; çapraz doğrulama ≥2 kaynak altı ana iddiada karşılanır (tek istisna işaretlendi: MEGA resmi API dokümanı 2 bağımsız **resmi** kaynaktan — mega.io SDK + mega.js.org docs — okundu, üçüncü taraf incelemesi r/MEGA ile desteklendi). **Vault tarafı aynı resmi verdi:** cache + offline olay + localStorage **IMPLEMENTED**, IndexedDB/SW/sync/CRDT/depo dizini/Drive/MEGA/özel depo **PLANNED** → bu ADR **mimari karardır, kod taahhüdü değil**; uygulaması §5.1 adımlarına bağlıdır. **Kaynak listesi (34):** 1) developer.mozilla.org — Storage quotas and eviction criteria · 2) web.dev — Storage for the web · 3) web.dev — Persistent storage · 4) webkit.org — Updates to Storage Policy (Safari 17) · 5) blog.logrocket.com — Offline-first frontend apps in 2025 · 6) oneuptime.com — How to Build Offline Capabilities · 7) dev.to — Frontend System Design: Offline Support & PWAs · 8) signaldb.js.org — Offline-First Approach · 9) javascript.plainenglish.io — SW Caching Strategies · 10) github.com/pazguille/offline-first · 11) medium.com (rackis) — SW + IndexedDB · 12) wild.codes — PWA offline-first architecture · 13) news.ycombinator.com — Downsides of Offline First (LWW/CRDT tartışması) · 14) ditto.com — Conflict Resolution Powered by CRDTs · 15) reddit.com/r/rust — You might not need a CRDT · 16) iankduncan.com — The CRDT Dictionary (2025-11-27) · 17) en.wikipedia.org — Conflict-free replicated data type · 18) oneuptime.com — How to Implement Last-Write-Wins (2026) · 19) medium.com — From Chaos to Consistency (version vectors) · 20) developers.google.com — Drive Usage limits · 21) knowledge.workspace.google.com — Storage and upload limits · 22) support.google.com — Shared drive limits · 23) stackoverflow.com — Service accounts have no storage quota · 24) discuss.google.dev — storageQuotaExceeded · 25) community.latenode.com — Drive API rate limits (Tem 2025) · 26) docs.cloud.google.com — Quotas and limits · 27) reddit.com/r/DataHoarder — Drive copy limit · 28) mega.io/developers — MEGA SDK (EOVERQUOTA) · 29) mega.js.org — API Reference (.upload) · 30) reddit.com/r/MEGA — traffic/upload limits · 31) rxdb.info — IndexedDB Max Storage Size · 32) diragb.dev — IndexedDB vs LocalStorage vs Cookies (2026) · 33) stackoverflow.com — Persistent storage denied for IndexedDB · 34) webkit/mindstick + silverbullet — platform bazlı kota varyansı. |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| [[ADR-007-cache-namespace]] | **Sunucu cache bağlayıcısı:** offline/online cache katmanı bu ADR'nin `app:v{version}:{domain}:{key}` namespace + TTL + invalidation kurallarına uyar; **ayrı/kopya cache altyapısı kurulmaz** (`shared/src/Cache/` yeniden kullanılır — adaptör seçimi `CacheManager.php:12-15`). |
| [[ADR-081-multi-provider-data-sync]] | **SSOT + tek yazıcı:** doğruluk kaynağı **sunucudur**; istemci/yerel cache asla nihai değildir. Çoklu-yazıcı çakışma çözümü (LWW/merge) yerine **tek yazıcı + projection** ruhu korunur (`ADR-081:102,166,177`); olay akışı outbox ile. Bu ADR yalnız **istemci↔sunucu** senkron sözleşmesini ekler. |
| [[ADR-026-download-service-architecture]] | **Erişim/lisans sınırı:** offline modda erişilen her medya öğesi için lisans/erişim **online modda** doğrulanır; depo **read-only** (indirme/offline kopya dosyaya yazmaz — yazım `user_downloads`/audit/outbox'a); imza TTL zinciri offline'da **çalışmaz** → offline kopya, online'da alınan lisanslı oturumun ürünüdür. |
| [[ADR-011-session-management]] | **Oturum saklama:** hibrit saklama/cookie kuralları (domain-wide) korunur; offline mod **oturumu yenilemez** → oturum süresi dolunca çevrimdışı erişim durur ve yeniden online doğrulama ister (güvenlik lehine). **⚠️** "offline session" terimi ADR-011'de geçmez — bu ADR'nin kendi tanımıdır. |
| [[ADR-002-pdo-mandatory-no-orm]] + [[ADR-022-database-hardened-security]] | **Senkron uçları:** sync/lWW/CRDT uç noktalarının DB erişimi PDO prepared; şema değişiklikleri `ADR-014` (expand-contract, forward-only) ile; CRDT metadata tabloları hardened kurallarına uyar. |
| [[ADR-005-ultrathink-protocol]] | Kod/vault kanıtı olmayan her iddia etiketli: IndexedDB **0**, service worker **0**, `storage/`/`uploads/` **dizin yok**, Drive **0**, MEGA **0** → `⚠️ VERIFICATION REQUIRED` / **PLANNED**. |
| [[ADR-024-ecosystem-modular-docs]] | Wiki-link disk kanıtı: bu ADR'deki her wiki-link (iki köşeli parantezli gömülü bağlantı) diskte var (§6'da doğrulanır); şablon zorunluluğu (Guardrail #16) — `.templates/adr/adr-template.md` iskeleti (7 bölüm + §1.3 9 alan). |
| In-Place Refactoring | Dosya adları **değiştirilmez**: `pwa-features.md`, `sync-service.md`, `file-system-storage.md`, `CacheManager.php`, `SidebarManager.js` vb. yalnız okunur; spec "Durum" satırlarına düzeltme **ekleme** olarak yazılır (§5.1/7). |
| Frozen ADR-001-037 | Yalnız okunur + referanslanır (`AGENTS.md` §25.3 kural 2) — bu ADR frozen **değil**. |
| `.ai/log.md` append-only | Bu işlem dahil tüm kayıtlar yalnız ekleme (bayt-seviyesi, `vault-utf8-writer append`). |
| REDACTED | Drive/MEGA API anahtarları, refresh token'ları, sunucu depo yollarının credential kısmı bu ADR'ye **kopyalanmaz**; anahtarlar yalnız `.env`'de yaşar. |

---

## 2. Karar (Decision)

**CoreMusic dual-mode depolama stratejisi DÖRT maddeyle bağlayıcı ilan edilir:**

**(a) İKİ EKSEN — DUAL-MODE:**

| Eksen | Mod | Tanım | İçerik |
|---|---|---|---|
| **1 — Erişim modu** | **Online** | Bağlantı var: normal API + stream + ADR-026 imza/lisans zinciri | Sunucu cache (ADR-007), CDN opsiyonel (ADR-026) |
| **1 — Erişim modu** | **Offline yerel** | Bağlantı yok: **çevrimdışı dinleme/erişim** | App shell cache-first + IndexedDB veri + **kuyruklu yazma**; mevcut `offline` olayı (yalnız hata ekranı) **genişletilir** |
| **2 — Depo konumu** | **Yerel disk** | Sunucu tarafı medya deposu (`file-system-storage.md` yapısı) + **kullanıcı cihazında seçili medya tamponu** | `user_uploads/{user_id}/…` (PLANNED), offline tampon |
| **2 — Depo konumu** | **Bulut hibrit** | **Birincil: CoreMusic özel deposu** (kendi sunucumuz — web'de online, yerelde offline erişim); **ikincil/opsiyonel: Google Drive + MEGA** (PLANNED) | yedek/overflow; birincil asla 3. parti değildir |

**(b) SENKRON SÖZLEŞMESİ — LWW + LİSTELERDE CRDT + SUNUCU SSOT:**

- **Varsayılan: last-write-wins (LWW) + sunucu SSOT.** Her kayıt `updated_at`/sürüm alanı taşır; istemciler eşzamanlı yarışı sunucuda çözer (sunucuda en güncel yazım kazanır) ve **sonucu tüm istemcilere yayınlar**. Profil/ayar/tercih/meta alanlar bu kuralda kalır.
- **Çakışma hassas listeler: CRDT.** Playlist (sıra + üyelik), etiket/koleksiyon gibi **eleme tabanlı LWW'nin kayıp üreteceği** alanlarda CRDT uygulanır: **yayınlama (add) + eleme (remove) birleşimi**, tombstone korunur, **sıra kaybı yaşanmaz** (§1.3-2: LWW "eşzamanlı edit'lerden birini siler"; CRDT koordinasyonsuz yakınsar ama metadata ister).
- **Offline yazma → kuyruk → bağlantı gelince gönderim:** çevrimdışı yazım yerel kuyrukta (`_syncStatus: pending`) bekler; `online` olunca toplu gönderilir; **çözüm sunucuda** yapılır, istemci çözüm **önerir ama nihai değildir**.
- **SSOT = sunucu (ADR-081 ruhu):** istemci/yerel cache = **snapshot (cache)**, doğruluk kaynağı değildir; çoklu-yazıcı israfı önlenir (tek yazıcı MySQL + projection).

**(c) PLANNED — COREMUSIC ÖZEL DEPOLAMA SİSTEMİ (kendi "Drive"ımız):**

CoreMusic'in **kendi sunucusunda** çalışan, Google Drive benzeri **özel depolama sistemi** kurulur: dosya/dizin paylaşımı, kullanıcı kütüphanesi, medya tamponu. **Erişim iki modda da çalışır:** web'de **online** (her yerden), yerelde **offline** (aynı cihaz); **başka bir PC'den erişim = online mod** (yerel mod yalnız oturumun cihazında yaşar). İleride **medya disk ↔ database hibrit** (dosya diske, `file_hash` + metadata DB'ye — `download_cache.file_hash` hazır). **Etiket: PLANNED (kod 0 — bu ADR ile kayıt altına alındı).**

**(d) PLANNED — 3. PARTİ BULUT OPSİYONU: Google Drive API + MEGA:**

**Opsiyonel yedek/overflow katmanı** olarak iki sağlayıcı entegre edilebilir (kullanıcı: "ileride"): **Google Drive API** (kota: 750 GB/gün yükleme, 1 TB/gün proje egress, shared drive zorunlu) ve **MEGA** (`EOVERQUOTA` kota hataları). **Birincil depo değildir**; kota/ücret/erişim değişimi CoreMusic'i 3. partiye bağımlı kılmaz. Zorunlu: **kota-uyumlu hata dalları** + **veri sahipliği** (CoreMusic deposu asla yalnız 3. partide tutulmaz). **Etiket: PLANNED.**

### 2.1 Neden Bu Seçenek?

- **İki eksen ayrıldığı için tek karar iki soruyu da yanıtlar:** "çevrimdışı ne yapabilirim?" (erişim) ile "dosya nerede duruyor?" (konum) aynı ADR'de → gelecekteki okuyucu tek yerden cevap bulur.
- **LWW varsayılan çünkü ucuz ve yeterli:** ayar/profil/tercih gibi tek-alanlı veride LWW'nin bedeli kabul edilebilir; version/sürüm alanı eklenerek "concurrent" tespiti yapılabilir (§1.3-2). Her şeyi CRDT yapmak metadata + test yüzeyi şişirir (CRDT Dictionary: "trade coordination for metadata").
- **Listelerde CRDT şart çünkü LWW kayıp üretir:** playlist'te eşzamanlı iki ekleme/çıkarmanın birinin yutulması kullanıcı verisi kaybıdır (HN: "LWW is washing your hands of it"; Ditto) → sıra+üyelik verisi CRDT (yayınlama+eleme, tombstone).
- **SSOT sunucu (ADR-081):** istemci-öncelikli (client-as-SSOT) model, CoreMusic'in tek-yazıcı/BCNF/audit mimarisiyle çelişir; sunucu SSOT ile offline yalnız **cache + kuyruk** demek olur → güvenli.
- **Yerel disk + bulut hibrit zorunlu çünkü tarayıcı kotası öngörülemez:** `estimate()` ölçülür ama eviction + `QuotaExceededError` gerçektir (MDN/web.dev) → tamponu yalnız tarayıcıya bırakmak medya kaybı demektir; sunucu deposu (yerel disk) + bulut hibrit dayanıklılık sağlar.
- **Özel depo birincil, 3. parti ikincil:** kullanıcının sahiplik hedefi (VISION) ile uyumlu; Drive/MEGA yalnız opsiyonel yedek → kota/ücret/fiyat değişimi riski taşınmaz (§4.3/3).
- **Offline lisans güvenli tarafta kalır:** ADR-026 imza/erişim zinciri online'da çalışır; offline erişim, **online'da alınmış lisanslı oturumun ürünüdür** → kopya üretimi sınırlı kalır.

### 2.2 Teknik Detaylar

**a) Offline yerel mod katmanları (PLANNED — mevcut kanıt üzerine):**

```
[1] App shell (HTML/CSS/JS)     → cache-first + sürüm ön eki (SW, PLANNED)
[2] API/dinamik veri            → network-first, çevrimdışı → IndexedDB snapshot
[3] Medya (dinleme)             → yerel medya tamponu (seçili liste/album) — sunucudan lisanslı indirme
[4] Yazma (offline)             → kuyruk (IndexedDB: _syncStatus=pending) → online'da gönderim
[5] Çakışma                     → LWW (varsayılan) / CRDT (playlist, etiket) → sunucuda çözüm
```

| Öğe | Karar | Durum |
|---|---|---|
| `offline`/`online` olayı | mevcut `RouterEventManager.js:13` genişletilir (hata ekranı → mod geçişi) | Olay IMPLEMENTED / **geçiş PLANNED** |
| localStorage (küçük veri) | mevcut (`SidebarManager`, tercihler) — **kullanılmaya devam** | **IMPLEMENTED** |
| IndexedDB (yapısal veri + kuyruk) | yeni; `QuotaExceededError` yakalama + `estimate()` ölçümü + `persist()` isteği | **PLANNED** (kod 0) |
| Service Worker + manifest | app shell cache-first; Workbox konfigürasyonu `pwa-features.md:152`'ye göre | **PLANNED** (kod 0; takvim `pwa-features.md:471` = 2026-Q4 → 2027-Q1) |
| Sunucu cache | `shared/src/Cache/` (APCu → Memory → PageCache) | **IMPLEMENTED** (ADR-007) |

**b) Senkron sözleşmesi (LWW / CRDT / kuyruk):**

| Alan tipi | Strateji | Mekanizma | Durum |
|---|---|---|---|
| Profil / ayar / tercih | **LWW + sunucu SSOT** | `updated_at` + sürüm; sunucuda en güncel kazanır, sonuç yayınlanır | PLANNED (kod 0) |
| Playlist / etiket / koleksiyon | **CRDT** | add (yayınlama) + remove (tombstone) birleşimi; sıra numaraları yeniden düzenlenir | PLANNED (spec: `sync-service.md:20,466`) |
| Çalma durumu / pozisyon | **LWW (son değer)** | sunucu son değeri tutar | PLANNED |
| Offline yazma | **Kuyruk** | `_syncStatus: pending` → `online` → toplu gönderim → sunucuda çözüm | PLANNED |
| Olay/audit | **Outbox** (ADR-081) | istemci olayı sunucu yazar; depo dosyasına **yazma yok** (read-only) | Şema var / kod PLANNED |

**c) Depo konumu (yerel disk + bulut hibrit):**

| Katman | İçerik | Erişim | Durum |
|---|---|---|---|
| Sunucu yerel disk | medya dosyaları + `user_uploads/{user_id}/{category}/{uploadId}.{ext}` (`file-system-storage.md:133`) | online (stream/indirme — ADR-026) | **PLANNED** (dizin 0; "stable" iddiası ⚠️) |
| DB metadata | `file_hash`, `size_bytes`, `file_path` (`coremusic_download.sql:109`, `coremusic_media.sql:153`) | online | **IMPLEMENTED** (şema) |
| İstemci offline tampon | seçili medya (indirilmiş lisanslı kopya) + IndexedDB katalog | offline | **PLANNED** |
| CoreMusic özel deposu | kendi sunucumuz — web (online) + yerel (offline) erişim; **başka PC = online** | iki mod | **PLANNED (madde c)** |
| 3. parti bulut (Drive/MEGA) | yedek/overflow kopya; `EOVERQUOTA`/kota hata dalları | online | **PLANNED (madde d)** |
| Medya disk ↔ DB hibrit | disk blob + DB metadata + `file_hash` doğrulama | iki mod | **PLANNED (ileride)** |

**d) 3. parti entegrasyon kapıları (PLANNED):**

| Sağlayıcı | Kullanım | Zorunlu kural | Bilinen kota |
|---|---|---|---|
| Google Drive API | opsiyonel yedek/overflow | shared drive (`supportsAllDrives=true`); kota-uyumlu hata dalı; anahtar `.env` (REDACTED) | 750 GB/gün yükleme, 1 TB/gün proje egress, 5 TB tek dosya |
| MEGA (MEGApi/SDK) | opsiyonel yedek/overflow | `EOVERQUOTA` yakalanır → kullanıcıya net mesaj + yeniden deneme penceresi | kota aşımı reddi (resmi SDK) |

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Yalnız online mod (offline yok)** | En basit mimari; lisans/imza zinciri bozulmaz | Çevrimdışı dinleme/erişim yok; kullanıcı yolculukta/kesintide uygulamayı kullanamaz; PWA hedefi (`pwa-features.md`) anlamsız kalır | Kullanıcı onayı (a) — dual-mode kararının yarısı bu; §1.3-1 offline-first 2025-26 endüstri normu |
| 2 | **Yalnız 3. parti bulut (Drive/MEGA birincil)** | Sıfır sunucu depolama maliyeti; hızlı başlangıç | Kotalar + fiyat + API politikası 3. partiye kilitlenir (§1.3-3/4); gizlilik/sahiplik hedefi çelişir; Drive service account kota taşımaz; MEGA `EOVERQUOTA` ile durabilir | **PLANNED = opsiyonel yedek** olarak kaldı (madde d); birincil olmak ADR-081 tek-yazıcı/sahiplik ruhunu çiğner |
| 3 | **Her yerde CRDT (tüm alanlar)** | Koordinasyonsuz yakınsama; çakışma "kendiliğinden" çözülür | Metadata şişmesi + tip-bazlı merge + eventual consistency UI yükü + genişletilmiş test yüzeyi (§1.3-2: "trade coordination for metadata"); tek-alanlı ayarlarda gereksiz | Yalnız **çakışma hassas listelerde** (playlist/etiket); ayar/profil/tercih'te LWW yeterli → karmaşıklık israfı |
| 4 | **İstemci-öncelikli (client-as-SSOT) / multi-master** | Tam offline yazma özgürlüğü; sunucu yokmuş gibi programlama | Nihai doğruluk kaynağı belirsizleşir; audit/BCNF/indirme lisans zinciri (ADR-002/022/026) ile çelişir; çakışma çözümü istemciye kalır (HN: "sync loss of context") | **SSOT = sunucu** (ADR-081 tek yazıcı); istemci yalnız **cache + kuyruk** (madde b) |
| 5 | **Yalnız yerel disk (hibrit yok)** | Tam kontrol, dış bağımlılık yok | Tek nokta arıza/yedek yok; kullanıcı cihazı kaybolunca kütüphane gider; tarayıcı kotası/eviction gerçek (MDN) | **Hibrit** (yerel disk + bulut) dayanıklılık sağlar; bulut birinciliği = CoreMusic özel deposu (madde c), 3. parti opsiyonel (madde d) |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Tek karar iki boşluğu kapatır:** erişim modu (online/offline) + depo konumu (yerel/bulut) + senkron sözleşmesi (LWW/CRDT/SSOT) daha önce hiçbir belgede birlikte yoktu (§1.2).
- **Mevcut kod yeniden kullanılır:** sunucu cache 7 dosya (ADR-007), `offline` olay/hata yakalama, localStorage cache'i → **yeni altyapı yok, genişletme var**.
- **Veri kaybı riski sınırlandırıldı:** LWW yalnız güvenli alanlarda; playlist/etiket CRDT ile korunur (§1.3-2 kanıtı).
- **Güvenlik/audit bozulmaz:** SSOT sunucu + tek yazıcı (ADR-081) + depo read-only (ADR-026) + lisans online doğrulama → mimariyle uyum.
- **Sahiplik korunur:** birincil depo **CoreMusic'in kendi sunucusu** → 3. parti fiyat/kota değişimi yalnız opsiyonel katmanı etkiler (§1.3-3/4).
- **Ölçülebilir:** kota/eviction `estimate()` ile, offline senkron kuyruk derinliği ve çakışma sayıları log'a yazılır (ADR-005).

### 4.2 Olumsuz Sonuçlar

- **Kod işi tamamen önümüzde:** IndexedDB **0**, service worker **0**, depo dizini **0**, Drive **0**, MEGA **0** → bu ADR **mimari karardır**; "offline çalışıyor" iddiası şu an **yalandır** (§1.1).
- **İki senkron mekanizması = iki test yüzeyi:** LWW + CRDT birlikte taşınır (CRDT metadata + tombstone + sıra yeniden düzenlemesi karmaşık).
- **Spec-durum çelişkileri:** `sync-service.md` "Tamamlandı" işaretleri kod 0 ile **spec seviyesi** okunmalı; `file-system-storage.md` "stable" iddiası disk kanıtsız → düzeltme/eğitim işi (§5.1/7).
- **Özel depo = yeni bir ürün yüzeyi:** Drive benzeri sistem (paylaşım, yetki, versiyon, kota) ayrı geliştirme yükü demektir; ADR-026 lisans zinciriyle entegrasyon gerekir.
- **Offline tampon + lisans gerilimi:** kopya cihazda kalır → cihaz kaybı/silme/doğrulama (offline lisans kontrolü) ayrı tasarım ister (§4.3/4).

### 4.3 Riskler

| # | Risk | Olasılık | Etki | Mitigasyon |
|---|------|---------|------|-----------|
| 1 | **Tarayıcı kotası/eviction:** IndexedDB/Cache verisi (özellikle mobilde) kullanıcı veya sistem tarafından silinir; yazım `QuotaExceededError` verir (MDN/web.dev/WebKit) | 4 (Çok olası) | 3 (Orta) | `navigator.storage.estimate()` ölçümü + `persist()` kalıcı mod isteği + kota aşımında kullanıcıya net mesaj; **medya tamponu boyutu sınırı**; kritik veri **yalnız sunucuda** (SSOT) → kayıp geri yüklenebilir |
| 2 | **LWW veri kaybı:** eşzamanlı yazılardan biri sessizce yutulur (HN/Ditto) | 3 (Olası) | 4 (Yüksek) | LWW yalnız **tek-alanlı güvenli** alanlarda; playlist/etiket **CRDT** zorunlu; version/sürüm ile "concurrent" tespiti + çakışma log'u; sunucu yayınla tüm istemcilere iletir |
| 3 | **3. parti bağımlılık (Drive/MEGA):** kota (750 GB/gün, `EOVERQUOTA`), fiyat/politika değişimi, API sürümü | 3 (Olası) | 3 (Orta) | 3. parti **yalnız opsiyonel yedek** (madde d); **birincil = CoreMusic deposu**; kota-uyumlu hata dalları; sağlayıcı soyutlama katmanı (tek arayüz, iki adaptör — ADR-007 adaptör ruhu) |
| 4 | **Offline lisans/erişim kötüye kullanımı:** cihazda kalan kopya süre aşımında/abonelik bitiminde erişilebilir kalır (ADR-026 imza zinciri offline çalışmaz) | 3 (Olası) | 4 (Yüksek) | Lisans **online'da** doğrulanır → offline erişim **süreli oturum penceresi** ile; süre dolunca çevrimdışı erişim kilitlenir (ADR-011 oturum hijyeni); indirme kotası ADR-026 (c) ile sınır; depo read-only |
| 5 | **Özel depo kapsam şişmesi:** Drive benzeri sistem (paylaşım/versiyon/yetki) planlanandan büyük çıkar | 3 (Olası) | 3 (Orta) | **Aşamalı MVP** (§5.1/5): önce medya tamponu + kütüphane, sonra paylaşım; her aşama ayrı ADR ile genişletilir; YAGNI |
| 6 | **CRDT karmaşıklığı:** tombstone/eleme yarışı + sıra yeniden düzenleme hatası playlist bozulması üretir | 2 (Mümkün) | 3 (Orta) | Dar alan (yalnız playlist/etiket) + referans implementasyon karşılaştırması + çakışma prop test'leri; başarısız olursa **fallback: sunucu-öncelikli LWW + kullanıcıya soru** (§4.4/2) |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **Senkron sözleşmesini yaz:** alan tipi → strateji matrisi (LWW / CRDT / kuyruk), `updated_at` + sürüm alanı, offline kuyruk şeması (`_syncStatus`) — BCNF + PDO prepared (ADR-002/022/014) | Data Engineer + Backend Architect | 3 gün |
| 2 | **IndexedDB katmanı:** katalog + kuyruk object store'ları, `QuotaExceededError` yakalama, `navigator.storage.estimate()`/`persist()` entegrasyonu (§1.3-5) — localStorage mevcut davranış **korunur** | UI Designer + Backend Architect | 3 gün |
| 3 | **Service worker + manifest:** `pwa-features.md:152-319` (Workbox, app shell cache-first, offline.html fallback); sürüm ön eki + activate'te eski cache temizliği; `offline` olayı hata ekranından **mod geçişine** genişletilir (`RouterEventManager.js:13` üzerine) | UI Designer + DevOps | 4 gün (PLANNED — takvim §1.1-B) |
| 4 | **CRDT modülü (dar alan):** playlist sıra/üyelik + etiket — add/remove tombstone, sıra yeniden düzenleme; **yalnız bu alanlarda** | Backend Architect + Data Engineer | 1 hafta |
| 5 | **Özel depo MVP (madde c):** kendi sunucumuzda kütüphane + medya tamponu + dosya/dizin; online (web) + offline (yerel) erişim; **başka PC = online**; `file_system-storage.md` `user_uploads/{user_id}/…` yapısı diske kurulur (dizin 0 → ilk fiziksel adım) | Backend Architect + DevOps | 2 hafta (PLANNED) |
| 6 | **Ölçüm:** kota/eviction (`estimate()`), kuyruk derinliği, çakışma sayısı, offline MTTR → `log.md`'ye sayısal kayıt | QA Engineer + UI Designer | 1 gün |
| 7 | **Spec-durum hizası (In-Place ekleme):** `sync-service.md:646-648` ve `file-system-storage.md:394` "Durum" satırlarına **kod kanıtı notu** eklenir (silme yok); "stable/Tamamlandı" → "spec / kod PLANNED (ADR-027)" | MO (vault-updater) | 0.5 gün |
| 8 | **3. parti kapıları (madde d, PLANNED):** sağlayıcı soyut arayüz (Drive adaptörü + MEGA adaptörü), kota-uyumlu hata dalları, `.env` anahtarları (REDACTED) + **üretim öncesi kota/fiyat yeniden doğrulaması** (§1.3-⚠️ ii) | Backend Architect + Security Engineer | 4 gün (PLANNED) |
| 9 | **Offline lisans penceresi:** ADR-026 zinciriyle entegrasyon — online doğrulama → süreli offline erişim → süre dolunca kilit (risk 4) | Security Engineer + Backend Architect | 3 gün (PLANNED) |
| 10 | **Doğrulama:** şablon tutamağı taraması (`ADR-027`, hedef 0) · wiki-link disk kontrolü (§6) · `vault-utf8-writer scan` (mojibake 0) · `index.md:64` slug eşleşmesi · placeholder 0 · `log.md` append 1 satır | Vault Steward | 0.5 gün |

### 5.2 Geri Dönüş Planı

**Vazgeçme (madde bazlı):** (a) offline mod kapanırsa → SW devre dışı (`OFFLINE_MODE=false`), `offline` olayı eski davranışına (hata ekranı) döner, IndexedDB verisi salt-okunur kalır ve TTL ile temizlenir; (b) CRDT vazgeçilirse → playlist/etiket **sunucu-öncelikli LWW + kullanıcıya "çakışma var mı?" bildirimi** fallback'i devreye girer (veri kaybı açıkça kullanıcıya gösterilir); (c) bulut hibrit kapanırsa → yalnız sunucu yerel disk kalır (yedek riski §4.2/4'te belgelenir); (d) Drive/MEGA vazgeçilirse → adaptörler `null` döner, kod **opsiyonel** olduğu için hiçbir yol buna bağlı değildir (madde d zaten PLANNED).

**Tam geri dönüş:** dosya adları/rota değiştirilmediği için (In-Place Refactoring korundu) geri dönüş = (1) SW kaydı kaldırılır (`sw-register.js` unregister), manifest bağlantısı sökülür; (2) IndexedDB store'ları `deleteDatabase` ile temizlenir (yalnız istemci verisi — sunucu SSOT'a dokunulmaz); (3) CRDT modülü bayrakla kapatılır (`SYNC_STRATEGY=lww`), kuyruk LWW'e düşer; (4) özel depo MVP dizinleri **taşınır/silinmez** (yedek olarak kalır) — In-Place kural gereği dizin adı değişmez; (5) `vault-utf8-writer` yedeği (`<file>.bak`) eski içeriği verir; (6) `.ai/log.md`'ye tek satır revert append'i; (7) `.ai/.decisions/index.md:64` satırı `status: reverted` olur; (8) **bu ADR düzenlenmez** — `superseded by ADR-NNN` ile yeni ADR yazılır (şablon §6.3).

**Korunan geri dönüş güvencesi:** `log.md` append-only geçmiş, karar dizini satırı, sunucu cache (ADR-007) ve DB şeması **bozulmaz**; şu an geri döndürülecek tek şey **mimari çerçevedir** (kod olmadığı için).

### 5.3 Debate Kaydı

| Tur | Persona | Durum | Sonuç |
|---|---|---|---|
| — | **Debate başlamadı** | **⏳ PENDING** | Kanıt taraması (§1.1) + §1.3 web araştırması tamamlandı; 3 tur / 20 persona debate **bu ADR'nin ilk turu bekleniyor** → sonuç bu tabloya `vault-utf8-writer append` ile eklenir (mevcut satırlara dokunulmaz) |
| 1 | 20 persona — kanıt denetimi (kod/vault taraması) | ✅ | **IMPLEMENTED:** Cache 7 dosya (`shared/src/Cache/`) + localStorage (sidebar/tercih) + `offline` olay yakalama · **PLANNED (=0):** IndexedDB / serviceWorker / CRDT / LWW / `gdrive\|google drive` / `mega` / `storage/` (kendi depo iskeleti) → **15 kabul/neutral, 4 uyarı** (DevOps: CRDT kademeli · QA: çakışma testi · Security: offline imza süresi · Critic: kendi depo iskeleti yok + offline lisans şartı) |
| 2 | 20 persona — itiraz → çözüm | ✅ | (1) kendi "Google Drive" iskeleti yok → **PLANNED iskelet: upload/izin/share + medya disk hibrit** → **şart 1** · (2) offline imzalı URL süresi dolar → **offline erişim: yerel lisans token + süre (ADR-026 hizası)** → **şart 2** · (3) CRDT erken → **kademeli: LWW önce, CRDT yalnız playlist/etiket** → **şart 3a** · (4) quota + 3. parti riski → entegrasyon **opsiyonel**, quota uyarısı (**teyit** — §4.3 risk 3) |
| 3 | 20 persona — nihai oy | ✅ | **18 kabul / 2 çekimser / 0 red → KABUL** (3 şart bağlayıcı — §5.5) |
| **Toplam** | **3 tur / 20 persona** | **✅ TAMAMLANDI** | **18/2/0 KABUL** — şartlar §5.5, Tech Lead §7 ✅ (2026-09-25) |

### 5.4 Açık PLANNED Kalemleri (kabul ≠ tamamlandı)

| Kalem | Durum | Kapanış |
|---|---|---|
| IndexedDB (katalog + kuyruk) | ❌ PLANNED (kod 0) | §5.1/2 |
| Service Worker + manifest + Workbox | ❌ PLANNED (`sw.js` 0, `manifest.json` 0) | §5.1/3 |
| `offline` olayından mod geçişi | ⚠️ Olay IMPLEMENTED, genişletme PLANNED | §5.1/3 |
| Sync servisi + Conflict Resolver + Offline Sync Manager | ❌ PLANNED (spec `sync-service.md:466,562`; kod 0) | §5.1/1, §5.1/4 |
| CRDT (playlist/etiket) | ❌ PLANNED (tek vault geçişi `sync-service.md:20`) | §5.1/4 |
| Depo dizinleri (`storage/`, `uploads/`, `user_uploads/`) | ❌ PLANNED (0 dizin; H032) | §5.1/5 |
| CoreMusic özel depolama sistemi (madde c) | ❌ PLANNED (yeni — bu ADR ile kayıt) | §5.1/5 |
| Medya disk ↔ database hibrit | ❌ PLANNED (ileride) | §5.1/5 + ayrı ADR |
| Google Drive API (madde d) | ❌ PLANNED (kod 0; spec `sync-service.md:640`) | §5.1/8 |
| MEGA (madde d) | ❌ PLANNED (vault + kod 0 eşleşme) | §5.1/8 |
| Offline lisans penceresi | ❌ PLANNED | §5.1/9 |
| Debate / Tech Lead | ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL) — 3 şart §5.5 | §5.3, §5.5, §7 |

### 5.5 Debate Şartları

Debate **PENDING** olduğundan bağlayıcı şart **henüz yoktur**; debate turu sonunda kabul koşulları bu alt başlığa `append` ile eklenir (şablon §6.3 — mevcut satırlara dokunulmaz).

> **Güncelleme (2026-09-25):** Debate tamamlandı (3 tur / 20 persona — **18/2/0 KABUL**); bağlayıcı şartlar aşağıda. Yukarıdaki paragraf debate **öncesi** durumu tarif eder (silinmedi — In-Place Refactoring).

| # | Şart | Kapsam (§ / dosya) | Durum |
|---|------|---------------------|-------|
| 1 | **Özel depo PLANNED iskeleti** (Critic itirazı → çözüm 1) | Madde c (§2) + §5.1/5: kendi "Drive"ımız için **upload / izin / paylaşım** iskeleti + **medya disk ↔ database hibrit** (file_hash + DB metadata + disk blob) — kod 0 iken kapsam bu iskeletle sabitlenir | ❌ PLANNED (bağlayıcı) |
| 2 | **Offline lisans token** (Security itirazı → çözüm 2) | §2.2b + §4.3 risk 4 + §5.1/9: offline erişim **yerel lisans token + süre** ile — **ADR-026 hizası**; imzalı URL/oturum süresi dolunca çevrimdışı erişim kilitlenir | ❌ PLANNED (bağlayıcı) |
| 3a | **Senkron kademelendirme: LWW → CRDT** (DevOps/Critic itirazı → çözüm 3) | §2.2b + §5.1/1 + §5.1/4: önce **LWW (sunucu SSOT)** devreye alınır; **CRDT yalnız playlist/etiket** (kademeli — her yerde CRDT reddi §3/#3) | ❌ PLANNED (bağlayıcı) |
| 3b | **Çakışma testi** (QA şartı — 3a ile birlikte kapanır) | §4.3 risk 2/6 + §5.1/6: LWW eşzamanlı yazım kaybı + CRDT tombstone/sıra yeniden düzenleme testleri; başarısızlıkta §5.2(b) fallback (sunucu-öncelikli LWW + kullanıcıya soru) | ❌ PLANNED (bağlayıcı) |

> **Kural:** **3 şart** (3a/3b dahil) kapanmadan bu ADR'nin offline mod / özel depo / senkron iddiası "üretimde" sayılmaz (§5.4 · ADR-005 etiket disiplini). **Teyit kalemi (Tur 2/4):** 3. parti entegrasyon **opsiyonel** kalır; quota uyarısı §4.3 risk 3'te korunur ve §5.1/8 üretim öncesi yeniden doğrulanır.

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../../CLAUDE.md]] | Ana sözleşme — Guardrail'ler (bu ADR'nin yazım usulü) |
| [[../../AGENTS.md]] | Agent registry — §5 domain (`*.php` → Backend Architect, `*.sql` → Data Engineer, `*.js` → UI Designer), §25.3 kural 2/3 (frozen + log append-only) |
| [[../../WORKFLOW.md]] | Süreçler — uygulama adımlarının faz bağlamı |
| [[../index]] | Karar dizini — **satır 64** `[[ADR-027-dual-mode-storage-strategy]]` (slug ✅) |
| [[../../index]] | Master katalog — `:644` "Decisions/accepted/ADR-027-dual-mode-storage-strategy \| Dual-mode storage \| Infrastructure" |
| [[../../brain]] | Mimari karar özeti — `:982` "ADR-027 \| Hibrit depolama" |
| [[../../keys]] | Keyword haritası — `:262` "ADR-027 \| dual-mode storage" |
| [[ADR-007-cache-namespace]] | Sunucu cache — `shared/src/Cache/` 7 dosya (`CacheManager.php:12-15`) — §1.1-A, §1.4, §2.2a |
| [[ADR-081-multi-provider-data-sync]] | SSOT + tek yazıcı — `:102,166,177` LWW/merge reddi → bu ADR'in "sunucu SSOT" dayanağı (§1.4, §2b) |
| [[ADR-026-download-service-architecture]] | Lisans/imza/depo read-only — offline erişim sınırı (§1.4, §4.3/4, §5.1/9) |
| [[ADR-011-session-management]] | Oturum saklama — offline'da oturum yenilenemez (§1.4) · **⚠️ "offline session" terimi bu ADR'de 0 eşleşme** |
| [[ADR-002-pdo-mandatory-no-orm]] | Senkron uç DB erişimi — PDO prepared (§1.4) |
| [[ADR-022-database-hardened-security]] | Şema sertliği — sync/CRDT tabloları (§1.4) |
| [[ADR-005-ultrathink-protocol]] | Kanıt standardı — `⚠️ VERIFICATION REQUIRED` etiketleri (§1.1-B, §1.3) |
| [[ADR-024-ecosystem-modular-docs]] | Wiki-link disk kanıtı + şablon zorunluluğu + UTF-8 tek arayüz (§1.4, §5.1/10) |
| [[../../architecture/k11-ux/pwa-features]] | PWA/SW spec — `:19-20,150,176,319` + "Durum: YYQ Planlama" (`:471`) — §1.1-B, §5.1/3 |
| [[../../architecture/k8-servis/sync-service]] | Sync/CRDT spec — `:18,20,466,562` + cloud provider `:640` — §1.1-B, §2.2b/d |
| [[../../architecture/k5-veri-yonetimi/file-system-storage]] | Depo dizin yapısı — `:52-65,133,369` + "stable" iddiası ⚠️ — §1.1-B, §2.2c |
| [[../../architecture/k5-veri-yonetimi/README]] | `:108` "Conflict resolution (last-write-wins)" — LWW spec kaynağı |
| [[../../architecture/k15-medya-streaming/content-delivery]] | CDN/Range spec — ADR-026 ile birlikte online teslim katmanı |
| [[../../.templates/adr/adr-template]] | İskelet — 7 bölüm + §1.3 9 alan (Guardrail #16) |
| [[../../.templates/adr/adr-index]] | ADR şablon envanteri |
| [[../../../.claude/skills/prompt-maker/references/10-web-research-protocol]] | §1.3 web araştırma protokolü (diskte VAR ✅) |
| `shared/src/Cache/CacheManager.php` (kod yolu, wiki-link değil) | Adaptör seçimi kodu — `:12-15` (APCu → Memory) — IMPLEMENTED kanıtı |

> **Durum özeti:** debate **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** · Tech Lead **✅** · Arch Lead **⏳ PENDING** · şartlar **§5.5** (1 özel depo iskeleti · 2 offline lisans token · 3a LWW→CRDT kademelendirme · 3b çakışma testi — tümü PLANNED) · frozen **YOK** · kod: cache + offline olay + localStorage **IMPLEMENTED**, IndexedDB/SW/sync/CRDT/depo dizin/Drive/MEGA/özel depo **PLANNED** (§1.1).

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali | 2026-09-25 | ✅ (kullanıcı onaylı karar içeriği) |
| Tech Lead | — | 2026-09-25 | ✅ (debate 3 tur / 20 persona — 18/2/0 KABUL, §5.3 + §5.5) |
| Arch Lead | — | 2026-09-25 | ⏳ PENDING |

---

**1.0.0 | 2026-09-25 | Created**
**1.1.0 | 2026-09-25 | Debate 3/20 (18/2/0 KABUL) + Tech Lead ✅ + 3 şart (§5.5; 3a/3b dahil)**

*ADR-027 — Dual-Mode Storage Strategy (Online + Offline Yerel Mod · Yerel Disk + Bulut Hibrit · LWW + CRDT · Sunucu SSOT · Özel Depo/Drive/MEGA PLANNED)*
*Authority: ADR-027 Karar Metni (SSOT)*
*Mode: Red Team · Human Mode · Truth Mode*
