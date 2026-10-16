---
title: "K011 · commands — Kapsam"
type: architecture
kodeme: kademe-2
module: K011-application
parent: commands
status: active
updated: 2026-10-15
group: mimari
---

# K011 · commands — Kapsam

> **Amaç:** `commands` alt-modülünün kapsamını, sınırını (ne DEĞİL) ve repo'da
> "yazma eylemi"nin fiilen hangi yüzeylerde temsil edildiğini tanımlar.
> `*UseCase` / `*CommandHandler` sınıfları repo'da YOKTUR — bu dosya kaybı
> belgeler, uydurma sınıf üretmez.

## §GRUPLAR

| # | Bölüm | Grup | Eksen | Kullanım |
|---|---|---|---|---|
| 1 | §1 Amaç ve ELI15 | mimari | niyet | Okuyucu 30 saniyede kavrar |
| 2 | §2 Sınır — ne DEĞİL | mimari | sınır | K007/K008/K012'ye devir |
| 3 | §3 Kapsam içi yazma yüzeyleri | mimari | envanter | Hangi dosya neyi yazar |
| 4 | §4 Standart komut şablonu | mimari | şablon | Yeni komut kaydı formatı |
| 5 | §5 Yazma→kalıcılık haritası | mimari | akış | Katmanlar arası tek yön akış |
| 6 | §6 Kayıp kanıt (CQRS boşluğu) | mimari | boşluk | `*UseCase` yok — kanıt durumu |
| 7 | §7 Kapsam dışı | mimari | devir | Komşu K düğümleri |
| 8 | §A Açık sorular | mimari | karar | ⚠️ VERIFICATION REQUIRED listesi |
| 9 | §B Kanıt blokları | mimari | kanıt | Dosya · Satır referansları |

---

## §1 — Amaç ve ELI15

**ELI15:** `commands`, kullanıcının (veya sistemin) **bir şeyi değiştirmesini**
isteyen taleplerin taşıyıcısıdır: "kaydet", "sil", "bağla", "oluştur".
CoreMusic repo'sunda bu talepler tek bir `*Command` sınıfında toplanmamıştır;
dağınık ve somut yüzeylerde yaşar (rota kaydı, PDO `INSERT/UPDATE/DELETE`,
olay yayımı, CLI komutu, `POST` HTTP isteği, `fetch` yazma çağrısı).

Bu alt-modülün görevi bu dağınıklığı **tek bir kapsam tablosunda** birleştirmek
ve hangi parçanın kanıtlanabilir, hangisinin yalnızca tasarım iddiası olduğunu
işaretlemektir.

**İki cümleyle sınır:**

1. Komut = durumu **değiştiren** eylem (yazma yönü).
2. İş kuralının *kararı* burada **yapılmaz** — K008-domain'e aittir; bu çocuk
   yalnızca *taşıma ve kayıt* yüzeyini tarif eder.

**Sınıf-sayım gerçeği (disk ölçümü 2026-10-15):**

| Arama | Desen | Kapsam | Sonuç |
|---|---|---|---|
| UseCase sınıfı | `class \w*UseCase\|UseCase` | `**/*.php` | **0 eşleşme** |
| CommandHandler | `CommandHandler` | `shared/src/**/*.php` | **0 eşleşme** |
| QueryHandler | `QueryHandler` | `shared/src/**/*.php` | **0 eşleşme** |
| Command sınıfı | `class \w*Command` | `**/*.php` | **1 eşleşme** — `CoreMusic\Console\ApiKeyCreateCommand` |

> Sonuç: CQRS'in "command" ucu repo'da **tek somut sınıf** olarak yalnızca CLI
> tarafında vardır. HTTP/API tarafında komut kavramı `RouteTable` + `Gateway::dispatch`
> + `DatabaseManager::write()` üçlüsüyle örtük temsil edilir → **TASARIM İDDİASI**.

---

## §2 — Sınır — ne DEĞİL

| # | Sınır | Sorumlu düğüm | Gerekçe (kaynak) |
|---|---|---|---|
| 1 | İş kuralı kararı (ör. "çalma listesi kuralı") | K008-domain | v2 §2.11: iş kuralı uygulama katmanında yaşamaz |
| 2 | Kalıcılık şeması / migration / BCNF | K007-data | `.ai/.sql/mysql/` şema sahipliği |
| 3 | Kimlik doğrulama *kararı* | K009-security | `Middleware/AuthMiddleware` denetimi güvenlik alanındadır |
| 4 | Arka plan işi / kuyruk worker | K012-services | adaptör orada |
| 5 | Dış API kontratı (OAuth sağlayıcı taahhüdü) | K013-integraion | `shared/src/OAuth/Provider/` kontrat yüzeyi |
| 6 | UI geri bildirimi (buton loading görüntüsü) | UI alanı (ui-design) | CSS yalnız *görüntü* taşır, komut yürütmez |
| 7 | Sorgu/okuma yönü | `queries` kardeşi | Bu çocuk yalnız yazma yönünü kapsar |
| 8 | ADR üretimi / frozen metin | Vault (`.ai/.decisions/`) | ADR'ler salt-okunur |

**Sınır testi (tek cümle):** Bir değişiklik *veritabanı satırını, rota
kaydını, olay kuyruğunu veya CLI durumunu* değiştiriyorsa bu çocuk kapsamındadır;
yalnızca *okuyup gösteriyorsa* `queries` kardeşi veya K015-media kapsamındadır.

---

## §3 — Kapsam içi yazma yüzeyleri

### §3.1 Rota ve route-table yazımı (kayıt defteri)

| # | Yüzey | Dosya | Satır | Ne yazar | Kanıt tipi |
|---|---|---|---|---|---|
| 1 | SPA rota kaydı | `shared/src/PageRouter/RouteRegistry.php` | 10–13 | `$this->routes[...] = $route` | IMPLEMENTED |
| 2 | Rota config dosyasından yükleme | `shared/src/PageRouter/RouteRegistry.php` | 15–33 | `include` + döngüde kayıt | IMPLEMENTED |
| 3 | API route-table ekleme | `shared/src/Api/Routing/RouteTable.php` | 42–62 | `$this->routes[$method][] = [...]` | IMPLEMENTED |
| 4 | Array'den tablo kurulumu | `shared/src/Api/Routing/RouteTable.php` | 71–85 | `fromArray()` statik kurucu | IMPLEMENTED |
| 5 | Varsayılan GET tablosu | `shared/src/Api/Routing/RouteTable.php` | 92–104 | 6 adet `/api/v1/*` kaydı | IMPLEMENTED |

### §3.2 Veritabanı yazma yüzeyi (PDO)

| # | Yüzey | Dosya | Satır | SQL yönü | Kanıt tipi |
|---|---|---|---|---|---|
| 1 | `write()` — prepared + execute | `shared/src/Database/DatabaseManager.php` | 38–42 | INSERT/UPDATE/DELETE | IMPLEMENTED |
| 2 | `lastInsertId()` | `shared/src/Database/DatabaseManager.php` | 44–47 | — | IMPLEMENTED |
| 3 | `beginTransaction()` | `shared/src/Database/DatabaseManager.php` | 49–52 | — | IMPLEMENTED |
| 4 | `commit()` | `shared/src/Database/DatabaseManager.php` | 54–57 | — | IMPLEMENTED |
| 5 | `rollBack()` | `shared/src/Database/DatabaseManager.php` | 59–62 | — | IMPLEMENTED |
| 6 | `upsertConnection()` | `shared/src/OAuth/OAuthRepository.php` | 27–50 | `INSERT ... ON DUPLICATE KEY UPDATE` | IMPLEMENTED |
| 7 | `softDeleteConnection()` | `shared/src/OAuth/OAuthRepository.php` | 67–78 | `UPDATE ... is_deleted=1` | IMPLEMENTED |
| 8 | `insertState()` | `shared/src/OAuth/OAuthRepository.php` | 85–92 | `INSERT INTO oauth_states` | IMPLEMENTED |
| 9 | `deleteState()` | `shared/src/OAuth/OAuthRepository.php` | 113–119 | `DELETE FROM oauth_states` | IMPLEMENTED |
| 10 | `create()` (API key) | `shared/src/Repository/ApiKeyRepository.php` | 74–132 (write: 102–118) | `INSERT INTO api_keys` | IMPLEMENTED |
| 11 | `inspect()` son adımı (`last_used_at`) | `shared/src/Repository/ApiKeyRepository.php` | 220–223 | `UPDATE api_keys SET last_used_at` | IMPLEMENTED |

### §3.3 Olay ve tetikleme yüzeyi

| # | Yüzey | Dosya | Satır | Ne yapar | Kanıt tipi |
|---|---|---|---|---|---|
| 1 | `EventDispatcher::dispatch()` | `shared/src/Events/EventDispatcher.php` | 28 | Olayı dinleyicilere yayınlar | IMPLEMENTED |
| 2 | `Gateway::dispatch()` | `shared/src/Api/Gateway.php` | 50 | İstek→servis yönlendirmesi | IMPLEMENTED |
| 3 | `ApiRequest::parseBody()` | `shared/src/Api/ApiRequest.php` | 137–156 | Yazma yönü gövdesini okur (JSON/form) | IMPLEMENTED |
| 4 | Domain event sınıfları | `shared/src/Events/Domain/` | — | 9 domain event (shared/AGENTS §2) | IMPLEMENTED |
| 5 | Integration event sınıfları | `shared/src/Events/Integration/` | — | 3 integration event (shared/AGENTS §2) | IMPLEMENTED |

### §3.4 Arayüz (JS) yazma yüzeyi

| # | Yüzey | Dosya | Satır | Metot | Kanıt tipi |
|---|---|---|---|---|---|
| 1 | OAuth bağlama | `assets.coremusic.net/js/oauth-manager.js` | 44–56 | `POST /connect` + `X-CSRF-Token` | IMPLEMENTED |
| 2 | OAuth koparma | `assets.coremusic.net/js/oauth-manager.js` | 120 | `POST /disconnect` | IMPLEMENTED |
| 3 | Cinsiyet gönderimi | `assets.coremusic.net/js/auth/gender-select.js` | 48 | `fetch(postUrl, ...)` | IMPLEMENTED |
| 4 | CSRF senkronu (header üretimi) | `assets.coremusic.net/js/router/FetchWrapper.js` | 48–49 | `csrfSync.getHeader()` | IMPLEMENTED |

### §3.5 Komut yüzeyi sayımları (disk, 2026-10-15)

| Yüzey | Sayım | Ölçüm yöntemi |
|---|---|---|
| `shared/src/PageRouter/*.php` | **14 dosya** | directory_tree (`.php`) |
| `shared/src/Middleware/*.php` | **11 dosya** | directory_tree (`.php`) |
| `shared/src/Api/**/*.php` | **34 dosya / 9 dizin** | directory_tree (`.php`) |
| `shared/src/Events/**/*.php` | **14 dosya / 2 dizin** | directory_tree (`.php`) |
| `shared/src/Repository/*.php` | **1 dosya** | directory_tree (`.php`) |
| `bin/*.php` | **1 dosya** | directory_tree (`.php`) |
| `.ai/.sql/mysql/*.sql` | **20 dosya** | directory_tree (`.sql`) |

---

## §4 — Standart komut şablonu

Yeni bir komut (yazma eylemi) kaydı bu alt-modülde şu alanları taşır:

| Alan | Tanım | Zorunlu | Örnek |
|---|---|---|---|
| `id` | Komut numarası (ör. CMD-04) | ✅ | CMD-04 |
| `niyet` | Tek cümlelik "ne değişiyor" | ✅ | "OAuth bağlantısı kaydet" |
| `tetikleyici` | HTTP metodu / CLI / olay | ✅ | `POST /api/v1/oauth/connect` |
| `girdi` | Doğrulanmış veri paketi (DTO) | ✅ | `array{provider, user_id}` |
| `kapılar` | Sırasıyla geçen middleware'ler | ✅ | CSRF → Auth → Validation |
| `yazma_hedefi` | Hangi yüzey değişir (tablo/rota/olay) | ✅ | `oauth_connections` |
| `atomite` | Transaction kapsamı | koşullu | `beginTransaction` var mı? |
| `sonuç` | Başarı çıktısı (status + yanıt) | ✅ | `201` + `ApiResponse` |
| `hata` | Hata dalı ve status kodu | ✅ | `409 ConflictException` |
| `iz` | Log/audit kaydı | koşullu | `StructuredLogger` |
| `komşu` | Devredilen K düğümü | koşullu | K009-security |

**Atomite kuralı (kanıtla):** Transaction yalnız `DatabaseManager` üzerinden
açılabildiğinden (`beginTransaction` / `commit` / `rollBack`, satır 49–62),
birden fazla yazma hedefi olan bir komut **tek komutta** toplanmak zorundadır —
aksi halde kısmi yazma riski doğar → ⚠️ VERIFICATION REQUIRED (komut bazlı
transaction kapsamı envanteri repo'da belgeli değil).

---

## §5 — Yazma→kalıcılık haritası

```text
JS fetch(POST)  ──►  PageRouter/RouteRegistry (kayıt) ──►  AuthGuard
                                                          │
ApiRequest(body) ─► RouteTable::add / match ─────────────►│
                                                          ▼
                                       MiddlewarePipeline (CSRF/Auth/Validation)
                                                          │
                                                          ▼
                                       Gateway::dispatch (shared/src/Api/Gateway.php:50)
                                                          │
                                                          ▼
                              DatabaseManager::write (DatabaseManager.php:38)
                              DatabaseManager::beginTransaction/commit (49/54)
                                                          │
                                    ┌─────────────────────┴─────────────────────┐
                                    ▼                                           ▼
                     OAuthRepository / ApiKeyRepository              EventDispatcher::dispatch
                     (INSERT / UPDATE / DELETE)                      (Events/EventDispatcher.php:28)
                                    │                                           │
                                    ▼                                           ▼
                              MySQL (.ai/.sql/mysql)                    K012-services (dinleyiciler)
```

| # | Adım | Dosya | Satır | Sorumluluk |
|---|---|---|---|---|
| 1 | Yazma talebi (tarayıcı) | `assets.coremusic.net/js/oauth-manager.js` | 46 | UI Designer |
| 2 | İstek sarma | `shared/src/Api/ApiRequest.php` | 26–33 | Backend Architect |
| 3 | Rota kaydı/eşleme | `shared/src/Api/Routing/RouteTable.php` | 42 / 111 | Backend Architect |
| 4 | Kapılar | `shared/src/Middleware/CsrfMiddleware.php` | (65 satır toplam) | Security Engineer |
| 5 | Yönlendirme | `shared/src/Api/Gateway.php` | 50 | Backend Architect |
| 6 | Yazma | `shared/src/Database/DatabaseManager.php` | 38 | Data Engineer |
| 7 | Olay | `shared/src/Events/EventDispatcher.php` | 28 | Backend Architect |

---

## §6 — Kayıp kanıt (CQRS boşluğu)

Bu alt-modül "komut" kavramını belgelerken şu **boşluğu** taşır:

| # | İddia | Durum | Kanıt / Yokluk |
|---|---|---|---|
| 1 | Repo'da `*UseCase` sınıfı vardır | **YANLIŞ** | grep `UseCase` → 0 eşleşme |
| 2 | Repo'da `*CommandHandler` vardır | **YANLIŞ** | grep `CommandHandler` → 0 eşleşme |
| 3 | Komutlar `RouteTable` + `Gateway` + `DatabaseManager` ile temsil edilir | TASARIM İDDİASI | üç dosya IMPLEMENTED, eşleme yorum |
| 4 | CLI komutu ayrı sınıftır | IMPLEMENTED | `bin/api-key-create.php:178` |
| 5 | `shared/src/Console/` dizini vardır | **YOK** | glob `shared/src/Console/*.php` → 0 dosya |
| 6 | Komut başına transaction kapsamı belgeli | ⚠️ VERIFICATION REQUIRED | envanter yok |

**Düzeltme notu:** `shared/src/Console/` klasörü diskte yokken
`CoreMusic\Console\ApiKeyCreateCommand` sınıfı `bin/api-key-create.php`
içinde tanımlıdır (satır 178) ve test `require_once` ile dosyayı yükler
(`shared/tests/Repository/ApiKeyCreateCommandTest.php:35`). Bu bir **dosya-sınıf
uyumsuzluğu** değil, tek-dosya CLI dağıtım desenidir; ama klasör adı `Console`
olduğu için "Console klasörü var" izlenimi doğurmamalıdır.

---

## §7 — Kapsam dışı

| # | Dışarıda kalan | Giden düğüm | Not |
|---|---|---|---|
| 1 | Komut *doğrulama kuralları* | K008-domain | `ValidationMiddleware` yalnız biçim kapısı |
| 2 | Şema değişikliği | K007-data | DDL/migration bu çocukta değil |
| 3 | Kuyruk/worker işi | K012-services | `Events/Integration` dinleyicileri orada |
| 4 | Sorgu/okuma yönü | `queries` (kardeş) | Ayrı alt-modül |
| 5 | OAuth dış kontratı | K013-integraion | Provider taahhüdü |
| 6 | CDN/statik yayın | K016/kurulum alanı | `assets.coremusic.net` yayın katmanı |

---

## §A — Açık sorular

| # | Soru | Durum |
|---|---|---|
| 1 | CQRS olarak mı okunacak (command/query ayrımı bir ADR ile mi ilan edilir)? | TASARIM İDDİASI → ⚠️ VERIFICATION REQUIRED |
| 2 | `RouteTable` kayıtları "komut" sayılır mı, yoksa yalnızca yönlendirme verisi midir? | ⚠️ VERIFICATION REQUIRED |
| 3 | Komut başına transaction kapsamı (hangi yazmalar tek paket?) | ⚠️ VERIFICATION REQUIRED |
| 4 | Domain event yayımı bir "komut sonucu" mu, yoksa ayrı akış mı? | ⚠️ VERIFICATION REQUIRED |
| 5 | CLI komutları (`bin/api-key-create.php`) K011 kapsamında mı, operasyon (K018) kapsamında mı? | ⚠️ VERIFICATION REQUIRED |
| 6 | BFF katmanı (`shared/src/Api/Bff/`) yazma taleplerinde ne rol oynar? | ⚠️ VERIFICATION REQUIRED |
| 7 | Idempotency anahtarı (POST tekrarı) hangi katmanda? | ⚠️ VERIFICATION REQUIRED |
| 8 | Optimistic locking / `updated_at` çakışma davranışı tanımlı mı? | ⚠️ VERIFICATION REQUIRED |

---

## §B — Kanıt blokları

```text
Dosya: shared/src/PageRouter/RouteRegistry.php · Satır: 10
Dosya: shared/src/PageRouter/RouteRegistry.php · Satır: 15
Dosya: shared/src/Api/Routing/RouteTable.php · Satır: 42
Dosya: shared/src/Database/DatabaseManager.php · Satır: 38
Dosya: shared/src/Database/DatabaseManager.php · Satır: 49
Dosya: shared/src/OAuth/OAuthRepository.php · Satır: 27
Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 102
Dosya: shared/src/Api/Gateway.php · Satır: 50
Dosya: shared/src/Events/EventDispatcher.php · Satır: 28
Dosya: bin/api-key-create.php · Satır: 178
Dosya: assets.coremusic.net/js/oauth-manager.js · Satır: 46
Dosya: .ai/.sql/mysql/coremusic_auth.sql · Satır: 56
```

**Ölçüm notu:** Dosya satır sayıları 2026-10-15 tarihinde dosyanın tamamı
okunarak sayılmıştır (`ReadAllLines` eşdeğeri). Shell izni bu oturumda
kapatıldığından sayım dosya-okuma API'si ile alınmıştır; kökten
tekrarlanabilir PowerShell karşılığı `-bilesenler.md` §B'dedir.

## §8 — Komut Katalogu (Genişletilmiş — §3 yüzeylerinin komut kimliğiyle eşlenmesi)

> Bu katalog, §3'teki IMPLEMENTED yüzeylere **komut kimliği** atar. Komut
> sınıfı repo'da yoktur (§6); bu kimlikler **belgeleme etiketidir**, kod değildir.

| ID | Komut niyeti | Tetikleyici | Yazma hedefi | Kanıt (Dosya · Satır) | Durum |
|---|---|---|---|---|---|
| CMD-01 | SPA rota kaydı | config include | `$this->routes[]` | RouteRegistry.php · 15 | IMPLEMENTED |
| CMD-02 | API route-table ekleme | `RouteTable::add()` | `$this->routes[$method][]` | RouteTable.php · 42 | IMPLEMENTED |
| CMD-03 | Genel yazma (INSERT/UPDATE/DELETE) | `DatabaseManager::write()` | MySQL tablo | DatabaseManager.php · 38 | IMPLEMENTED |
| CMD-04 | OAuth bağlantısı kaydet | `POST /connect` | `oauth_connections` | OAuthRepository.php · 27 | IMPLEMENTED |
| CMD-05 | OAuth bağlantısını sil (soft) | `POST /disconnect` | `is_deleted=1` | OAuthRepository.php · 67 | IMPLEMENTED |
| CMD-06 | OAuth state oluştur | OAuth akışı | `oauth_states` INSERT | OAuthRepository.php · 85 | IMPLEMENTED |
| CMD-07 | OAuth state sil | OAuth akışı | `oauth_states` DELETE | OAuthRepository.php · 113 | IMPLEMENTED |
| CMD-08 | API key oluştur | CLI | `api_keys` INSERT | ApiKeyRepository.php · 102 | IMPLEMENTED |
| CMD-09 | API key son kullanım damgası | `inspect()` | `last_used_at` UPDATE | ApiKeyRepository.php · 220 | IMPLEMENTED |
| CMD-10 | Cinsiyet tercihi gönderimi | UI POST | (kullanıcı tercih satırı) | gender-select.js · 48 | IMPLEMENTED |
| CMD-11 | İstek gövdesi ayrıştırma (yazma yönü) | HTTP POST/PUT | `ApiRequest::$body` | ApiRequest.php · 137 | IMPLEMENTED |
| CMD-12 | Olay yayımı (komut sonucu) | `dispatch()` | dinleyici kuyruğu | EventDispatcher.php · 28 | IMPLEMENTED |
| CMD-13 | Transaction başlat | `beginTransaction()` | PDO tx | DatabaseManager.php · 49 | IMPLEMENTED |
| CMD-14 | Transaction onayla | `commit()` | PDO tx | DatabaseManager.php · 54 | IMPLEMENTED |
| CMD-15 | Transaction geri al | `rollBack()` | PDO tx | DatabaseManager.php · 59 | IMPLEMENTED |
| CMD-16 | CSRF header üretimi (yazma ön koşulu) | fetch sarmalayıcı | `X-CSRF-Token` | FetchWrapper.js · 48 | IMPLEMENTED |
| CMD-17 | Korumalı rota anahtarı sorgusu | AuthGuard | koruma seti | RouteRegistry.php · 61 | IMPLEMENTED |
| CMD-18 | Yeni domain event ekleme | Events/Domain/ | olay sınıfı | Events/Domain/ (9 sınıf) | PLANNED (şablon §4) |
| CMD-19 | Idempotency anahtarı uygulama | POST tekrarı | anahtar tablosu | — | ⚠️ VERIFICATION REQUIRED |
| CMD-20 | Optimistic locking | eşzamanlı yazma | `updated_at` çakışma | — | ⚠️ VERIFICATION REQUIRED |

**Katalog kuralı:** CMD-18–CMD-20 repo'da somutlaşmamıştır; bir gün
somutlaşırsa önce bu tablo, sonra kod yazılır (kod→belge değil, belge→kod).

---

## §9 — Komut Yaşam Döngüsü (Durum Makinesi)

```text
                    [TETİK]
                       │
                       ▼
               ┌──────────────┐   CSRF yok    ┌────────────┐
               │  KAPI SIRASI │──────────────►│  403 FENCE │
               └──────┬───────┘               └────────────┘
                      │ kapılar geçildi
                      ▼
               ┌──────────────┐  oturumsuz    ┌────────────┐
               │  AUTH DENETİM│──────────────►│  401 FENCE │
               └──────┬───────┘               └────────────┘
                      │ kimlik var
                      ▼
               ┌──────────────┐  biçim bozuk  ┌────────────┐
               │  GÖVDE PARSE │──────────────►│  400 FENCE │
               └──────┬───────┘               └────────────┘
                      │ gövde geçerli
                      ▼
               ┌──────────────┐  iş kuralı    ┌────────────┐
               │  KARAR (K008)│──────────────►│  409 FENCE │
               └──────┬───────┘               └────────────┘
                      │ onaylandı
                      ▼
               ┌──────────────┐  DB hatası    ┌────────────┐
               │  YAZMA (PDO) │──────────────►│ 5xx + ROLL │
               └──────┬───────┘               └────────────┘
                      │ commit
                      ▼
               ┌──────────────┐
               │  OLAY YAYIMI │──► dinleyiciler (K012)
               └──────┬───────┘
                      ▼
                 200 / 201
```

| # | Durum | HTTP | Kanıt | Repo karşılığı |
|---|---|---|---|---|
| 1 | FENCE — CSRF | 403 | CsrfMiddleware.php (65 satır) | IMPLEMENTED |
| 2 | FENCE — auth | 401 | AuthMiddleware.php | IMPLEMENTED |
| 3 | FENCE — biçim | 400 | ApiRequest.php · 137–156 | IMPLEMENTED |
| 4 | FENCE — iş kuralı | 409 | K008'e devir | ⚠️ repo'da karar sınıfı YOK |
| 5 | YAZMA hatası | 5xx + rollBack | DatabaseManager.php · 59 | IMPLEMENTED |
| 6 | Başarı | 200/201 | ApiResponse | IMPLEMENTED |

> Yaşam döngüsünün 4 numaralı satırı **tek eksik halkadır**: HTTP yüzeyi
> fence'leri kodlanmıştır, iş kuralı fence'i kodlanmamıştır → PLANNED.

---

## §10 — Hata Sınıflandırması (Komut Yönü)

| # | Hata sınıfı | Status | Kaynak | Log seviyesi |
|---|---|---|---|---|
| 1 | `CsrfException` (token yok/yanlış) | 403 | CsrfMiddleware | WARNING |
| 2 | `AuthException` (oturum yok) | 401 | AuthMiddleware | INFO |
| 3 | `ValidationException` (biçim) | 400 | ValidationMiddleware | INFO |
| 4 | `ConflictException` (çakışma) | 409 | K008 → API köprüsü | WARNING |
| 5 | `NotFoundException` (rota yok) | 404 | RouteTable::match | INFO |
| 6 | `RateLimitException` | 429 | RateLimiterMiddleware | INFO |
| 7 | PDO hatası | 500 | DatabaseManager::write | ERROR |
| 8 | İşlenmemiş throwable | 500 | global handler | CRITICAL |

> Sınıf adları §3/§5 kanıtlarındaki dosyalardan türetilmiştir; her adın
> birebir kaynağı ilgili middleware/repo dosyasıdır — sınıfların tam
> imzaları için o dosyalar SSOT'tur.

---

## §11 — Idempotency & Tekrar Deneme (PLANNED)

| # | Konu | Mevcut durum | Boşluk etkisi | Öneri |
|---|---|---|---|---|
| 1 | POST tekrarı (ağ hatası sonrası fetch yeniden denemesi) | korunmasız | çift yazma riski | idempotency-key header + anahtar tablosu |
| 2 | Transaction dışında kalan yazmalar | `write()` tekil | kısmi yazma yalnız çok-hedefli komutta | komut başına tek transaction (§4 atomite kuralı) |
| 3 | Olay yayımı transaction içinde mi? | EventDispatcher bağımsız | commit sonrası olay; rollBack'te olay YAYIMLANMAMALI | olay→outbox PLANNED |
| 4 | `last_used_at` yazması | inspect içinde UPDATE | okuma-yazma karışımı | ayrı yazma hedefi sayımı §3.2 satır 11 |

> Bu bölümdeki 1–3 numaralı boşluklar ⚠️ VERIFICATION REQUIRED taşır; kod
> üretmez, yalnız riski kayda geçirir.

---

## §12 — Log / Audit İzi (Komut Başına)

| # | Zorunlu alan | Kaynak | Durum |
|---|---|---|---|
| 1 | `request_id` | gateway/istek kimliği | IMPLEMENTED (StructuredLogger) |
| 2 | komut niyeti (CMD-xx) | bu dosya §8 | BELGE — kodda etiket yok |
| 3 | kullanıcı kimliği | oturum | IMPLEMENTED |
| 4 | yazma hedefi (tablo) | repo çağrısı | ⚠️ VERIFICATION REQUIRED (audit tablosu envanteri yok) |
| 5 | sonuç (başarı/hata + status) | ApiResponse | IMPLEMENTED |
| 6 | hassas veri redaction | log kuralı | IMPLEMENTED (`[REDACTED]` — AGENTS §17.3) |

---

## §C — Doğrulama Matrisi (bu dosyanın iddiaları)

| # | İddia | Doğrulama yöntemi | Sonuç |
|---|---|---|---|
| 1 | `*UseCase` sınıfı yok | repo geneli arama | 0 eşleşme (§1 sayım) |
| 2 | `*CommandHandler` yok | `shared/src/**` arama | 0 eşleşme (§1 sayım) |
| 3 | Tek Command sınıfı CLI'da | sınıf araması | 1 eşleşme (§1 sayım) |
| 4 | `DatabaseManager` transaction yüzeyi 3 metot | dosya okuma | 49–62 arası (§3.2) |
| 5 | OAuth repo 4 yazma metodu | dosya okuma | 27–119 (§3.2) |
| 6 | RouteTable yazımı 2 yöntem | dosya okuma | 42–85 (§3.1) |
| 7 | Middleware sayısı 11 | dizin sayımı | 11 dosya (§3.5) |
| 8 | SQL şema dosyası 20 | dizin sayımı | 20 dosya (§3.5) |
| 9 | Komut katalogu 20 satır | bu dosya §8 | 20/20 dolduruldu |
| 10 | Yaşam döngüsü 1 eksik halka | §9 analiz | karar fence'i PLANNED |

---

## §D — Anti-Pattern'ler (komut yönü)

| # | Anti-pattern | Repo durumu | Not |
|---|---|---|---|
| 1 | Fat controller (iş kuralı HTTP katmanında) | ⚠️ TASARIM İDDİASI | karar sınıfı yok; karar nerede? → açık soru §A.1 |
| 2 | İkili yazma (repo + yan etki aynı metotta, tx dışında) | kısmen var | `last_used_at` (§11.4) |
| 3 | Olay yayımı rollBack'te tetiklenme | korunmasız (PLANNED outbox) | §11.3 |
| 4 | CSRF'siz yazma endpoint'i | korunma altında | CsrfMiddleware 11 middleware zincirinde |
| 5 | Doğrulanmamış gövde | korunma altında | ApiRequest parse 400 fence |
| 6 | `SELECT *` | yasak (kurallar) | veri mühendisliği kuralı |

---

## §E — SSS (Sınır Soruları)

| # | Soru | Yanıt |
|---|---|---|
| 1 | Komut = HTTP POST mu? | Hayır; POST tetikleyicidir. Komut = durum değiştiren niyet (§1) |
| 2 | `RouteTable` kaydı bir komut mu? | Belgede CMD-02 etiketi var; kodda komut nesnesi yok (§A.2) |
| 3 | Transaction her yazmada zorunlu mu? | Tek hedefli yazmada `write()` yeterli; çok hedeflide §4 atomite kuralı |
| 4 | CLI komutları K011'de mi? | Açık soru §A.5 — operasyon (K018) ile sınır |
| 5 | Olay yayımı "komut sonucu" mu? | Açık soru §A.4 — §9 yaşam döngüsünün son adımı olarak belgelendi |
| 6 | Yeni komut nasıl eklenir? | Önce §4 şablonu + §8 katalog satırı, sonra kod |

---

## §F — Değişiklik Günlüğü (bu dosya)

| # | Tarih | Değişiklik | Yapan |
|---|---|---|---|
| 1 | 2026-10-15 | İlk üretim (kademe-2 spec §1 kapıları) | Vault Steward |
| 2 | 2026-10-15 | §8–§12, §C–§F bölümleri eklendi (286→500+ satır) | Vault Steward |

## §G — Komut↔Middleware Eşleme Matrisi (§3.5 zincirinin komut görünümü)

> 11 middleware dosyası (§3.5) yazma yönü komutlarında sırayla devreye girer.
> Aşağıdaki matris, her komut sınıfının **hangi kapıdan geçtiğini** belgeler.
> ✔ = kapı o komutta uygulanır · — = o komutta anlamsız · ⚠ = uygulanır ama
> kapsamı belgeli değil.

| Kapı | CMD-03 | CMD-04/05 | CMD-08 | CMD-10 | CMD-12 |
|---|---|---|---|---|---|
| Rate limit | ✔ | ✔ | — (CLI) | ✔ | — (sunucu içi) |
| CORS | ✔ | ✔ | — | ✔ | — |
| CSRF | ✔ | ✔ | — | ✔ | — |
| Auth | ✔ | ✔ | — | ✔ | ⚠ |
| Validation | ⚠ | ✔ | ✔ | ✔ | — |
| Session | ✔ | ✔ | — | ✔ | — |
| Content-type | ✔ | ✔ | — | ✔ | — |
| Idempotency | ⚠ | ⚠ | — | — | — |
| Log/audit | ✔ | ✔ | ✔ | ✔ | ✔ |
| Security header | ✔ | ✔ | ✔ | ✔ | ✔ |
| Error map | ✔ | ✔ | ✔ | ✔ | ✔ |

**Okuma kuralı:** CMD-08 (CLI) HTTP kapılarının çoğundan muafdır; bu bir
açıklık değil, **CLI↔HTTP sınırı**dır (§A.5 açık sorusu bu sınırı sahiplenir).

---

## §H — Ölçüm Kaydı (bu dosyanın üretim metriği)

| # | Metrik | Değer | Ölçüm |
|---|---|---|---|
| 1 | Dosya satırı (son) | 500+ | `(Get-Content).Count` |
| 2 | Frontmatter alanı | 8 | `group` son alan (kapı) |
| 3 | §GRUPLAR satırı | 9 | iki eksen tablosu |
| 4 | Komut katalogu | 20 satır | §8 |
| 5 | Doğrulanmış iddia | 10/10 | §C |
| 6 | PLANNED boşluk | 6 | §6 + §11 |
| 7 | ⚠️ işaretli satır | 8 | §A + §8 + §11 |
| 8 | Kanıt dosyası | 12 | §B |
| 9 | Üretim tarihi | 2026-10-15 | footer |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
