---
node_id: K011-application/commands/commands-bilesenler.md
node_type: file
status: draft
parent_id: K011-application/commands
children: []
depends_on:
  - K007-data
  - K012-services
evidence:
  - "shared/src — 165 PHP dosyası (dizin sayımı 2026-10-15)"
  - "shared/src/Api — Gateway, RouteTable, 6 API middleware, BFF ×4, DTO ×11"
  - "shared/src/Events/Domain — 9 domain event sınıfı"
validation:
  - "Satır ≥500 — (Get-Content).Count ile doğrulandı, 2026-10-15"
  - "Her bileşen Dosya kanıtı ile listelenir; PLANNED bileşenler etiketlidir"
open_questions:
  - "Bileşen sahipliği: CLI Command K011'de mi K018'de mi → ⚠️ VERIFICATION REQUIRED"
  - "Audit bileşeni (log izi) diskte net değil → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# commands — Bileşen Listesi

> **Amaç:** Yazma eylemlerinin (komutların) taşıdığı tüm bileşenleri
> **envanter** olarak listeler: hangi bileşen var (Dosya kanıtı), hangi
> komuta hizmet eder, hangi klasörün sahibi, hangisi PLANNED.

## §GRUPLAR

| # | Bölüm | Grup (Eksen 1) | Eksen 2 |
|---|---|---|---|
| 1 | §1 Envanter özeti | mimari | envanter |
| 2 | §2 Çekirdek yazma bileşenleri | mimari | cekirdek |
| 3 | §3 OAuth yazma bileşenleri | mimari | oauth |
| 4 | §4 API/routing bileşenleri | mimari | routing |
| 5 | §5 Middleware bileşenleri (kapılar) | mimari | kapi |
| 6 | §6 Olay bileşenleri | mimari | olay |
| 7 | §7 İstemci (JS) bileşenleri | mimari | istemci |
| 8 | §8 SQL/şema bileşenleri | mimari | sema |
| 9 | §9 CSS bileşenleri | mimari | css |
| 10 | §10 PLANNED bileşenler | mimari | planli |
| 11 | §A Sahiplik matrisi (klasör → agent) | kanit | sahiplik |
| 12 | §B Bağımlılık grafiği | mimari | graf |
| 13 | §C Bileşen↔komut eşleme | kanit | esleme |
| 14 | §D Ölçüm kaydı | kanit | olcum |
| 15 | §E Açık sorular | kanit | acik |
| 16 | §F İki middleware katmanı | kanit | katman |
| 17 | §G DTO envanteri | mimari | dto |
| 18 | §H API 34 sayım bileşimi | kanit | bileşim |
| 19 | §I JS dizin envanteri | mimari | js |
| 20 | §J CSS bileşen envanteri | mimari | css |
| 21 | §K Duplicate tespitleri | kanit | duplicate |
| 22 | §L AGENTS.md↔disk çapraz | kanit | capraz |
| 23 | §M Tekrarlanabilir ölçüm | kanit | yontem |
| 24 | §N Olgunluk matrisi | mimari | olgunluk |
| 25 | §O K011 sınırı | mimari | sinir |

## §1 — Envanter Özeti

| # | Yüzey | Dosya sayısı | Ölçüm | Komutla ilişkisi |
|---|---|---|---|---|
| 1 | `shared/src/**` toplam PHP | **165** | dizin sayımı 2026-10-15 | çekirdek altyapı |
| 2 | `shared/src/Api/**` | **34** | dizin sayımı 2026-10-15 | CMD-01/02/11/12 |
| 3 | `shared/src/Middleware/**` | 11 | dizin sayımı | kapılar (§5) |
| 4 | `shared/src/Events/Domain/**` | 9 | dizin sayımı | CMD-18 |
| 5 | `assets.coremusic.net/js/**` | **94** `.js` (102 dosya: +8 CLAUDE.md) | dizin sayımı 2026-10-15 | CMD-10/16 |
| 6 | `.ai/.sql/mysql/**` | 20 | dizin sayımı | CMD-03/08 |
| 7 | `shared/src/OAuth/**` | dosya bazlı | okuma | CMD-04..07 |

**Okuma:** Envanter **sayımdır**, tahmin değil; her sayı §A'daki dosya
yollarıyla yeniden üretilebilir.

## §2 — Çekirdek Yazma Bileşenleri

| # | Bileşen | Dosya | Görev | Komut |
|---|---|---|---|---|
| 1 | DatabaseManager | shared/src/Database/DatabaseManager.php | tek yazma kapısı | CMD-03/13/14/15 |
| 2 | IDatabaseManager | shared/src/Contracts/Database/ | sözleşme | CMD-03 |
| 3 | DatabaseConfig | shared/src/Database/Config/ | DSN ayarı | altyapı |
| 4 | DatabaseRegistry | shared/src/Database/DatabaseRegistry.php | çoklu bağlantı (ADR-003/022) | altyapı |
| 5 | LoggerFactory | shared/src/Log/LoggerFactory.php | iz yazımı | §5 log kapısı |
| 6 | Redactor | shared/src/Log/Redactor.php | hassas veri maskeleme | güvenlik |
| 7 | FileHandler | shared/src/Log/FileHandler.php | dosya çıkış | §5 log kapısı |

**Sınır:** Yazma **yalnızca** DatabaseManager üzerinden yapılır (shared
kuralı 1); başka bileşen PDO açamaz.

## §3 — OAuth Yazma Bileşenleri

| # | Bileşen | Dosya | Metotlar | Komut |
|---|---|---|---|---|
| 1 | OAuthRepository | shared/src/OAuth/OAuthRepository.php | upsert 27 · soft-del 67 · state 85/113 | CMD-04..07 |
| 2 | OAuthManager | shared/src/OAuth/OAuthManager.php | akış orkestrasyonu | CMD-04..07 |
| 3 | Sağlayıcı adaptörleri | shared/src/OAuth/Provider/ — **12 dosya** = `BaseOAuthProvider` + `IOAuthProvider` + **10 platform** (Discord · Facebook · Instagram · LinkedIn · Pinterest · Reddit · Snapchat · TikTok · X · YouTube) | sağlayıcı adaptörü | ortak |
| 4 | oauth-platforms config | shared/config/oauth-platforms.php | platform listesi | ortak |

**Toplam:** `shared/src/OAuth/**` = **14 PHP dosyası** (12 sağlayıcı +
`OAuthRepository.php` + `OAuthManager.php`) — dizin sayımı 2026-10-15.

**Özellik:** Yazma metotları **Repository'dedir**; Manager yalnız orkestre
eder (kalın DTO ≠ iş kuralı).

## §4 — API/Routing Bileşenleri

| # | Bileşen | Dosya | Görev | Komut |
|---|---|---|---|---|
| 1 | Gateway | shared/src/Api/Gateway.php | uç nokta girişi | hepsi (tetikleyici) |
| 2 | RouteTable | shared/src/Api/Routing/RouteTable.php | method-aware kayıt | CMD-02 |
| 3 | ApiRequest | shared/src/Api/ApiRequest.php | gövde parse (137–156) | CMD-11 |
| 4 | ApiResponse | shared/src/Api/ApiResponse.php | sonuç sarma | son |
| 5 | ApiMiddlewarePipeline | shared/src/Api/Middleware/ | kapı zinciri | §5 |
| 6 | ServiceRegistry | shared/src/Api/Registry/ | hizmet kaydı | altyapı |
| 7 | VersionResolver | shared/src/Api/Versioning/ | sürüm çözümü | altyapı |
| 8 | BffLayer + 4 BFF | shared/src/Api/Bff/ | Desktop/Embedded/Mobile/Spa | altyapı |

## §5 — Middleware Bileşenleri (Kapılar — 11)

| # | Bileşen (dosya — gerçek ad, 2026-10-15 sayımı) | Kapı rolü | HTTP | Komutlar |
|---|---|---|---|---|
| 1 | `CsrfMiddleware.php` | token doğrulama | 403 | CMD-03/04/05/10 |
| 2 | `AuthMiddleware.php` | oturum denetimi | 401 | CMD-03/12 |
| 3 | `RateLimiterMiddleware.php` | sıklık sınırı | 429 | CMD-10 |
| 4 | `PermissionMiddleware.php` | rol/izin | 403 | CMD-03 |
| 5 | `CorsMiddleware.php` | origin (CORS) | 403 | CMD-10 |
| 6 | `OriginCheckMiddleware.php` | origin kontrolü (CSRF yanında) | 403 | CMD-10 |
| 7 | `SecurityHeadersMiddleware.php` | header (CSP vb.) | — | hepsi |
| 8 | `SessionManagerMiddleware.php` | oturum yaşam döngüsü | — | hepsi |
| 9 | `ValidationMiddleware.php` | gövde/girdi doğrulama | 400 | CMD-11 |
| 10 | `BypassAuthMiddleware.php` | ADR-008 muafiyet | — | kapsam dışı |
| 11 | `MiddlewarePipeline.php` | **sıra + çalıştırma** (kapıların kendisi değil, taşıyıcı) | — | hepsi |

> **Düzeltme kaydı (2026-10-15):** İlk taslakta `OriginMiddleware` ·
> `SessionMiddleware` · `RequestValidationMiddleware` ·
> `ResponseNormalizationMiddleware` adları yazılmıştı; dizin taraması bu
> adları **bulamadı** → gerçek 11 dosya adıyla değiştirildi (yukarıdaki
> tablo diskten okunmuştur). Ayrıca `ResponseNormalizationMiddleware`
> `Middleware/` altında **değil**, `Api/Middleware/` altındadır → §4.5
> satırına taşındı.

**Sahiplik:** Middleware yüzeyi **security-engineer** (değişiklik her
security-audit workflow'undan geçer); ekleme `MiddlewarePipeline::register`
kayıdı ister — pipeline dosyası sıranın **tek** sahibidir.

## §6 — Olay Bileşenleri (CMD-18 · 9 sınıf)

| # | Sınıf | Dosya (shared/src/Events/Domain/) | Tetikleyen yazma |
|---|---|---|---|
| 1 | UserLoggedInEvent | UserLoggedInEvent.php | oturum yazması |
| 2 | UserLoggedOutEvent | UserLoggedOutEvent.php | oturum kapatma |
| 3 | UserRegisteredEvent | UserRegisteredEvent.php | kullanıcı INSERT |
| 4 | PasswordResetRequestedEvent | PasswordResetRequestedEvent.php | anahtar INSERT |
| 5 | MusicAddedEvent | MusicAddedEvent.php | kütüphane yazması |
| 6 | MusicPlayedEvent | MusicPlayedEvent.php | sayaç yazması |
| 7 | PlaylistCreatedEvent | PlaylistCreatedEvent.php | playlist INSERT |
| 8 | GenderSetEvent | GenderSetEvent.php | tercih (CMD-10) |
| 9 | MediaAccessedEvent | MediaAccessedEvent.php | erişim izi |

**Yayım:** EventDispatcher (shared/src/Events/EventDispatcher.php · 28) —
§10 yaşam döngüsünün son adımı; **outbox yok** (PLANNED, doğrulama §8.5).

## §7 — İstemci (JS) Bileşenleri

| # | Bileşen | Dosya | Görev | Komut |
|---|---|---|---|---|
| 1 | OAuthManager | assets.coremusic.net/js/oauth-manager.js | connect 44 · disconnect 118 | CMD-04/05 |
| 2 | gender-select | assets.coremusic.net/js/auth/gender-select.js | tercih POST 48 | CMD-10 |
| 3 | FetchWrapper | assets.coremusic.net/js/router/FetchWrapper.js | CSRF header 48 | CMD-16 |
| 4 | EventBus | assets.coremusic.net/js/core/EventBus.js | istemci olay yayımı | — |
| 5 | router (28 dosya) | assets.coremusic.net/js/router/ | gezinme | — |

**Sınır:** JS **kalıcı yazmaz** — yalnız HTTP isteği gönderir; karar ve
kalıcılık sunucudadır.

## §8 — SQL/Şema Bileşenleri (20 dosya)

| # | Şema | İlgili dosya | Yazma yüzeyi |
|---|---|---|---|
| 1 | `api_keys` | coremusic_auth.sql · 56 | CMD-08/09 |
| 2 | `api_usage` | coremusic_auth.sql · 81 | sayaç |
| 3 | `oauth_connections` | migration | CMD-04/05 |
| 4 | `oauth_states` | migration | CMD-06/07 |
| 5 | `admin_users` / `admin_activity_log` | coremusic_auth.sql · 36/18 | admin yazması |
| 6 | diğer 14 dosya | .ai/.sql/mysql/ | CMD-03 genel |

## §9 — CSS Bileşenleri (komut durumları)

| # | Dosya (gerçek — 2026-10-15 sayımı) | Durum sınıfı / rol | Komutla ilişkisi |
|---|---|---|---|
| 1 | `Css/04_Components/c-buttons.css` | `:disabled` 165 · `--loading` 173 · `::after` 179 · `:active` 79 | CMD-10 |
| 2 | `Css/04_Components/c-forms.css` | form durumları (error dahil) | CMD-11 (istemci) |
| 3 | `Css/04_Components/c-toast.css` | bildirim yüzeyi (başarı/hata) | sonuç |
| 4 | `Css/04_Components/c-modal.css` | onay penceresi (yıkıcı komut) | CMD-05 onayı |
| 5 | `js/components/composites/ToastComponent.js` | toast davranışı | sonuç |
| 6 | `js/components/interactive/ToastComponent.js` | ikinci kopya (dikkat: aday çakışma) | — |

**Doğrulama notu (2026-10-15):** İlk taslakta `c-form.js` · `c-spinner.js`
adları **üretilmişti**; disk taraması bu dosyaları **bulamadı** → silindi ve
gerçek adlar (`c-forms.css` · `c-toast.css`) ile değiştirildi. `ToastComponent.js`
iki ayrı dizinde bulunur (`composites/` ve `interactive/`) — bu bir
**duplicate-pr adayıdır** (§E.6).

**Not:** CSS yalnız **görsel durum** taşır; iş kuralı taşımaz. Spinner
görseli `c-buttons.css ::after` (179) içindedir, ayrı spinner dosyası yoktur.

## §10 — PLANNED Bileşenler (kod YOK)

| # | Bileşen | Neden yok | Ne üretmeli | Etiket |
|---|---|---|---|---|
| 1 | `*UseCase` sınıfı | repo modeli sınıf-tabanlı değil | kararı ver: CQRS kullanılacak mı | ⚠️ |
| 2 | `*CommandHandler` | aynı | Command pattern kararı | ⚠️ |
| 3 | Idempotency anahtarı | kod 0 | anahtar tablosu + middleware | PLANNED |
| 4 | Optimistic lock | kod 0 | `updated_at` çakışma kontrolü | PLANNED |
| 5 | Outbox (olay→kalıcı) | outbox yok | K012 sınırı | PLANNED |
| 6 | Audit bileşeni | envanter net değil | data-engineer taraması | ⚠️ |
| 7 | Atomite zorlayıcı | tx elle çağrılıyor | dekoratör/yardımcı | PLANNED |

**Kural:** Bu bileşenler envantere **eklenmez**; yalnız ihtiyaç satırı
olarak durur (§1.2 sayımlarını bozmaz).

## §A — Sahiplik Matrisi (klasör → agent → yetki)

| # | Yüzey | Sahip agent | Yazabilir | Okuyamaz? |
|---|---|---|---|---|
| 1 | `shared/src/Database` | backend (Data Engineer migrasyon) | evet | hayır |
| 2 | `shared/src/Api` | backend-architect | evet | — |
| 3 | `shared/src/Middleware` | security-engineer | evet (+audit) | — |
| 4 | `shared/src/OAuth` | security-engineer | evet (+audit) | — |
| 5 | `shared/src/Events` | backend-architect | evet | — |
| 6 | `assets.../js` | ui-designer | evet | — |
| 7 | `assets.../Css` | ui-designer | evet | — |
| 8 | `.ai/.sql/mysql` | data-engineer | evet | — |
| 9 | `.github/workflows` | devops-engineer | evet | — |
| 10 | `.md` (bu vault) | Vault Steward | evet | — |

**Yasak:** Subagent `git commit` ATMZ (orkestratöre aittir); kaynak kod
yüzeyi bu görevde **değiştirilmez** (yalnız okunur).

## §B — Bağımlılık Grafiği (yazma yönü)

```text
JS (CMD-10/16)
  └─► HTTP POST ─► Gateway ─► ApiMiddlewarePipeline (11 kapı)
                                    │
                                    ▼
                              RouteTable (CMD-02) ─► handler
                                    │
                                    ▼
                        OAuthRepository / DatabaseManager (CMD-03..07)
                                    │
                                    ▼
                              MySQL (20 şema) ─commit─►
                                    │
                                    ▼
                        EventDispatcher (CMD-12/18) ─► 9 domain event
```

**Tek yön kuralı:** Aşağı doğru akar; geri dönüş yalnız HTTP yanıtıdır
(olaylar geri yazma tetiklemez — outbox PLANNED olmadığı için).

## §C — Bileşen↔Komut Eşleme (tam)

| # | Bileşen | CMD | Bölüm |
|---|---|---|---|
| 1 | Gateway | tetikleyici | §4.1 |
| 2 | RouteTable | CMD-01/02 | §4.2 |
| 3 | ApiRequest | CMD-11 | §4.3 |
| 4 | CsrfMiddleware.. (11) | kapılar | §5 |
| 5 | DatabaseManager | CMD-03/13/14/15 | §2.1 |
| 6 | OAuthRepository | CMD-04..07 | §3.1 |
| 7 | EventDispatcher + 9 event | CMD-12/18 | §6 |
| 8 | OAuthManager (JS) | CMD-04/05 | §7.1 |
| 9 | gender-select (JS) | CMD-10 | §7.2 |
| 10 | FetchWrapper (JS) | CMD-16 | §7.3 |
| 11 | Şema (20) | CMD-03/08 | §8 |
| 12 | c-buttons.css | CMD-10 (durum) | §9.1 |
| 13 | — | CMD-09/19/20 | §10 (PLANNED) |

## §D — Ölçüm Kaydı

| # | Metrik | Değer | Tarih |
|---|---|---|---|
| 1 | shared/src PHP dosyası | 165 | 2026-10-15 |
| 2 | Api dosyası | 34 (sayım) | 2026-10-15 |
| 3 | Middleware | 11 | 2026-10-15 |
| 4 | Domain event | 9 | 2026-10-15 |
| 5 | SQL şema | 20 | 2026-10-15 |
| 6 | JS dosyası | 94 `.js` / 102 toplam | 2026-10-15 |
| 7 | PLANNED bileşen | 7 | 2026-10-15 |
| 8 | Sahiplik satırı | 10 | 2026-10-15 |
| 9 | Satır ≥500 | (Get-Content).Count | 2026-10-15 |

## §E — Açık Sorular

| # | Soru | Etki | Etiket |
|---|---|---|---|
| 1 | CLI Command hangi klasörün sahibi? | sınır | ⚠️ VERIFICATION REQUIRED |
| 2 | Audit bileşeni diskte var mı? | log izi | ⚠️ VERIFICATION REQUIRED |
| 3 | BFF bileşenleri komut zincirine girer mi? | akış | ⚠️ VERIFICATION REQUIRED |
| 4 | Registry (DatabaseRegistry) çok-hedef tx'te ne yapar? | atomite | ⚠️ VERIFICATION REQUIRED |
| 5 | 9 event'ten hangisi bu modülün? | sahiplik | ⚠️ VERIFICATION REQUIRED |
| 6 | `ToastComponent.js` hangi dizin aktif? | duplicate | ⚠️ VERIFICATION REQUIRED |
| 7 | İki middleware katmanının **sırası** hangisi? | kapı sayısı 11↔18 | ⚠️ VERIFICATION REQUIRED |
| 8 | JS sayım farkı 98↔94 neden? | envanter tutarlılığı | ⚠️ VERIFICATION REQUIRED |

## §F — İki Middleware Katmanı (kritik bulgu)

> Bu, bu dosyanın **özgün** bulgusudur: repo'da middleware **tek** dizinde
> değildir; **iki ayrı katman** vardır ve rolleri farklıdır.

| # | Katman | Dizin | Dosya | Sahip | Ne yapar |
|---|---|---|---|---|---|
| 1 | HTTP güvenlik kapıları | `shared/src/Middleware/` | **11** | security-engineer | 401/403/429 — isteği **reddeder** |
| 2 | API istek hattı | `shared/src/Api/Middleware/` | **7** | backend-architect | doğrula/dönüştür — isteği **şekillendirir** |
| | **Toplam** | iki dizin | **18** | — | — |

**Api/Middleware içeriği (7 dosya — diskten okundu 2026-10-15):**

| # | Dosya | Rol | Katman 1'deki karşılığı |
|---|---|---|---|
| 1 | ApiMiddlewarePipeline.php | hat çalıştırıcı | `MiddlewarePipeline.php` (aynı rol, ayrı sınıf!) |
| 2 | AuthenticationMiddleware.php | kimlik | `AuthMiddleware.php` |
| 3 | AuthorizationMiddleware.php | yetki | `PermissionMiddleware.php` |
| 4 | RateLimitMiddleware.php | sıklık | `RateLimiterMiddleware.php` |
| 5 | RequestValidationMiddleware.php | girdi doğrulama | `ValidationMiddleware.php` |
| 6 | ResponseNormalizationMiddleware.php | yanıt şekillendirme | (katman 1'de yok) |
| 7 | Validator.php | doğrulama motoru | (katman 1'de yok) |

**Okuma (iki sonuç):**

1. **Benzer isimli çiftler** (Authentication↔Auth, Authorization↔Permission,
   RateLimit↔RateLimiter, Validation↔Validation, Pipeline↔Pipeline) →
   **adlandırma tutarsızlığı** ve **duplicate-pr adayıdır** (§K.1).
2. **Sorun:** Bu iki katmanın **sırası** ve **hangisinin hangi istekte
   çalıştığı** belgede net değil → `⚠️ VERIFICATION REQUIRED` (§E.7).

**K011 etkisi:** Komut (yazma) akışı **iki hattan da** geçiyor olabilir;
yalnız 11 dosyayı saymak (doğrulama K4) **eksik ölçüm** olurdu → K4
11/11 doğru, ama **kapı toplamı 18'dir**.

## §G — DTO Envanteri (11 — `Api/Dto/`)

| # | Sınıf | Alt dizin | Rol |
|---|---|---|---|
| 1 | LoginRequest | Dto/Request | giriş gövdesi |
| 2 | RegisterRequest | Dto/Request | kayıt gövdesi |
| 3 | UserUpdateRequest | Dto/Request | profil güncelleme |
| 4 | MusicSearchRequest | Dto/Request | arama |
| 5 | PlaylistCreateRequest | Dto/Request | playlist oluşturma |
| 6 | PlaylistAddTrackRequest | Dto/Request | parça ekleme |
| 7 | LoginResponse | Dto/Response | giriş sonucu |
| 8 | UserResponse | Dto/Response | profil sonucu |
| 9 | MusicResponse | Dto/Response | müzik sonucu |
| 10 | PlaylistResponse | Dto/Response | playlist sonucu |
| 11 | PaginationResponse | Dto/Response | sayfalama |

**Kod≠DTO bulgusu:** 11 DTO'nun 6'sı `*UseCase`/`*Command` sınıfına
**eşleşmez** (repo'da use-case yok) → DTO'lar doğrudan
`ApiRequest`/`Gateway` kullanır. **CMD-11** (gövde parse) bu 11 sınıfın
giriş kapısıdır.

**Yazma-yüzeyi ile ilişki:** Bu 11 DTO'dan **hiçbiri** kalıcı yazma
yapmaz (yalnız taşıyıcı) → `kapsam §1.5` kuralı ("DTO ≠ iş kuralı")
sayısal olarak da doğrulandı.

## §H — API Kök + Alt Dizin Sayımı (34 dosya)

| # | Alt dizin | Dosya | İçerik |
|---|---|---|---|
| 1 | (kök) | 4 | ApiRequest · ApiResponse · Gateway · ApiSessionManager |
| 2 | Bff/ | 5 | BffLayer · DesktopBff · EmbeddedBff · MobileBff · SpaBff |
| 3 | Dto/ | 11 | §G |
| 4 | Middleware/ | 7 | §F.2 |
| 5 | Registry/ | 3 | ServiceDefinition · ServiceHealth · ServiceRegistry |
| 6 | Routing/ | 1 | RouteTable |
| 7 | Versioning/ | 3 | ApiVersion · VersionRegistry · VersionResolver |
| | **Toplam** | **34** | `(Get-ChildItem -Recurse).Count` = 34 ✅ |

**Bileşim kontrolü:** 4 + 5 + 11 + 7 + 3 + 1 + 3 = **34** → sayım
**kendi içinde tutarlıdır** (parça-toplam = bütün). Bu, §1.2 satırının
güvenilirlik kanıtıdır.

## §I — İstemci JS Dizin Envanteri (94 `.js`)

| # | Dizin | `.js` | İçerik / komut ilişkisi |
|---|---|---|---|
| 1 | `js/` (kök) | 5 | main · device-loader · device-layout-updater · devices.config · oauth-manager |
| 2 | `js/auth/` | 2 | auth-gender-bg · **gender-select** (CMD-10) |
| 3 | `js/components/base/` | 5 | ComponentLoader + temel |
| 4 | `js/components/composites/` | 13 | bileşik (ToastComponent dahil) |
| 5 | `js/components/interactive/` | 7 | etkileşimli (ToastComponent dahil ⚠ §K.2) |
| 6 | `js/components/primitives/` | 12 | ilkel bileşen |
| 7 | `js/core/` | 6 | CoreMusicApp · **EventBus** · footer.init · helper · breakpoints(+spec) |
| 8 | `js/coreplayer/` | 5 | oynatıcı çekirdeği |
| 9 | `js/features/` | 6 | CardManager · PlayerController · ScrollManager · TouchManager · WidgetManager +1 |
| 10 | `js/managers/` | 5 | DeviceManager · ScaleManager · SidebarManager · ThemeManager · ViewModeManager |
| 11 | `js/router/` | 23 | SPA router çekirdeği (ADR-021/083) |
| 12 | `js/router/config/` | 5 | auth-routes · css-selectors · error-types · events · navigate-utils · signal-utils |
| | **Toplam** | **94** | +8 CLAUDE.md = 102 dosya |

**Çapraz kontrol:** `router/` 23 + `router/config/` 5 = **28** →
`assets.coremusic.net/AGENTS.md §2` "js/router/ 28 dosya" ile **birebir
uyumlu** ✅ (bağımsız ikinci kaynak).

**Komut yüzeyi:** 94 dosyanın yalnızca **3'ü** yazma komutu taşır
(oauth-manager · gender-select · FetchWrapper) → %3.2. Kalan %96.8
**salt okuma/gezinme** — komut mimarisinin istemci tarafının **dar** olduğunu
sayısal olarak kanıtlar.

## §J — CSS Bileşen Envanteri (15 — `04_Components/`)

| # | Dosya | Durum/rol | Komut ilişkisi |
|---|---|---|---|
| 1 | c-badge.css | rozet | okuma |
| 2 | **c-buttons.css** | `:disabled` 165 · `--loading` 173 · `::after` 179 · `:active` 79 | **CMD-10** |
| 3 | c-card.css | kart | okuma |
| 4 | c-footer-seek.css | ilerletme | oynatıcı |
| 5 | c-footer-volume.css | ses | oynatıcı |
| 6 | **c-forms.css** | form durumları | CMD-11 (istemci) |
| 7 | c-home-song-btn.css | şarkı düğmesi | okuma |
| 8 | **c-modal.css** | onay penceresi | CMD-05 onayı |
| 9 | c-progress.css | ilerleme | okuma |
| 10 | c-scrollbar-accent.css | kaydırıcı | okuma |
| 11 | **c-toast.css** | bildirim yüzeyi | sonuç |
| 12 | c-toggle.css | anahtar | tercih |
| 13 | _home-components.css | ana sayfa parçası | okuma |
| 14 | _player-info.css | oynatıcı bilgi | okuma |
| 15 | _welcome-banner.css | karşılama | okuma |
| | **Toplam** | **15** | ✅ `AGENTS.md §2` "15 dosya" ile uyumlu |

**Komut ilişkisi:** 15 dosyanın **4'ü** yazma eyleminin görsel durumunu
taşır (2 · 6 · 8 · 11) → %27. Kalan %73 **salt görünüm**.

## §K — Benzerlik / Duplicate Tespitleri (duplicate-pr adayları)

| # | Bulgu | Kanıt | Etki | Aksiyon |
|---|---|---|---|---|
| 1 | İki middleware katmanında **aynı rolün 5 çifti** | §F | ad karmaşası | adlandırma ADR'si |
| 2 | `ToastComponent.js` **iki dizinde** | `components/composites/` + `components/interactive/` | hangisi aktif? | duplicate-pr |
| 3 | JS sayım farkı: önceki not **98** · bugün **94** | iki ölçüm | hangisi eski? | yeniden ölçüm (§M.5) |
| 4 | `MiddlewarePipeline` ↔ `ApiMiddlewarePipeline` | §F | aynı işi 2 sınıf | inceleme |
| 5 | `RateLimiterMiddleware` ↔ `RateLimitMiddleware` | §F | aynı işi 2 sınıf | inceleme |

**Kural:** Bu 5 satır **silinmez** — bilinen benzerlik kaydıdır; çözümü
`duplicate-pr` agent'ına aittir. #3'teki 4 dosyalık fark **açıklanmadı**
(oturum içi ölçüm farkı olabilir) → `⚠️ VERIFICATION REQUIRED` (§E.8).

## §L — AGENTS.md Envanter ↔ Disk Çapraz Kontrolü

| # | Belge iddiası (assets AGENTS.md §2) | Disk ölçümü | Sonuç |
|---|---|---|---|
| 1 | `Css/04_Components/` 15 dosya | 15 | ✅ |
| 2 | `js/router/` 28 dosya | 23 + 5 (config) = 28 | ✅ |
| 3 | `Css/01_Abstracts/` 19 token + 2 kopya | (bu görev kapsamında değil) | — |
| 4 | `Css/05_Pages/` 12 dosya | (kapsam dışı) | — |
| 5 | `Css/main.css` YOKTUR | (kapsam dışı) | — |
| 6 | `js/main.js` · `oauth-manager.js` giriş noktaları | ikisi de var (kök 5 içinde) | ✅ |
| 7 | `js/auth/` = auth-gender-bg + gender-select | 2 dosya, adlar eşleşiyor | ✅ |

**Sonuç:** Kapsam içi **4/4 ✅ uyumlu**; 3–5 satır bu görevin dışında
→ sayılmadı (uydurma yok). Belge ile disk **çelişmez**; yalnız §K.3'teki
sayım farkı açık kalmıştır.

## §M — Tekrarlanabilir Ölçüm Komutları

```powershell
# 1) shared/src toplam PHP (beklenen: 165)
(Get-ChildItem 'C:\www\coremusic.net\shared\src' -Recurse -Filter *.php -File).Count

# 2) Api toplam PHP (beklenen: 34)
(Get-ChildItem 'C:\www\coremusic.net\shared\src\Api' -Recurse -Filter *.php -File).Count

# 3) İki middleware katmanı (beklenen: 11 ve 7)
(Get-ChildItem 'C:\www\coremusic.net\shared\src\Middleware' -Filter *.php -File).Count
(Get-ChildItem 'C:\www\coremusic.net\shared\src\Api\Middleware' -Filter *.php -File).Count

# 4) Events/Domain (beklenen: 9)
(Get-ChildItem 'C:\www\coremusic.net\shared\src\Events\Domain' -Filter *.php -File).Count

# 5) JS .js (beklenen: 94) ve tüm dosya (beklenen: 102)
(Get-ChildItem 'C:\www\coremusic.net\assets.coremusic.net\js' -Recurse -Filter *.js -File).Count
(Get-ChildItem 'C:\www\coremusic.net\assets.coremusic.net\js' -Recurse -File).Count

# 6) Contracts (19) · OAuth (14) · CSS 04 (15) · SQL (20)
(Get-ChildItem 'C:\www\coremusic.net\shared\src\Contracts' -Recurse -Filter *.php -File).Count
(Get-ChildItem 'C:\www\coremusic.net\shared\src\OAuth' -Recurse -Filter *.php -File).Count
(Get-ChildItem 'C:\www\coremusic.net\assets.coremusic.net\Css\04_Components' -File).Count
(Get-ChildItem 'C:\www\coremusic.net\.ai\.sql\mysql' -Filter *.sql -File).Count
```

**Not:** Komutlar repo kökünden çalıştırılır (§13.6 kuralı); her beklenen
değer **2026-10-15 ölçümüdür** ve yeniden ölçümde güncellenir.

## §N — Bileşen Olgunluk Matrisi

| # | Yüzey | Dosya | Kod var mı? | Komut karşılığı | Olgunluk |
|---|---|---|---|---|---|
| 1 | Çekirdek yazma | 165 (src) | ✅ | CMD-03/13–15 | **Olgun** |
| 2 | HTTP kapıları | 11 | ✅ | CMD-03/10 | **Olgun** |
| 3 | API hattı | 7 | ✅ | CMD-11 | **Olgun** |
| 4 | OAuth yazma | 14 | ✅ | CMD-04–07 | **Olgun** |
| 5 | Routing/API | 34 | ✅ | CMD-01/02 | **Olgun** |
| 6 | Olaylar | 9 | ✅ | CMD-12/18 | **Olgun** (outbox yok) |
| 7 | DTO | 11 | ✅ | CMD-11 | **Olgun** |
| 8 | İstemci JS | 94 | ✅ (3'ü yazma) | CMD-10/16 | **Olgun** |
| 9 | CSS durumları | 15 | ✅ | CMD-10 | **Olgun** |
| 10 | SQL şema | 20 | ✅ | CMD-03/08 | **Olgun** |
| 11 | Contracts | 19 | ✅ | — | **Olgun** |
| 12 | Idempotency | 0 | ❌ | CMD-19 | **PLANNED** |
| 13 | Optimistic lock | 0 | ❌ | CMD-20 | **PLANNED** |
| 14 | Outbox | 0 | ❌ | — | **PLANNED** |
| 15 | UseCase/CommandHandler | 0 | ❌ | — | **PLANNED** (karar bekliyor) |

**Özet:** 11/15 yüzey **olgun** (kod var); 4/15 **PLANNED** (kod yok —
uydurulmadı, §10 ile aynı).

## §O — K011 Sınırı (hangi bileşen başka K'ye ait)

| # | Bileşen | Ait olduğu K | Neden K011'de değil |
|---|---|---|---|
| 1 | `Events/` (domain+integration) | K012-services | yayım orkestrasyonu |
| 2 | Testing (12+3 test) | K18-testing | test stratejisi |
| 3 | BFF katmanı (5) | K006/yonetim | sunucu-yerleşim |
| 4 | CLI Command (`bin/`) | K018 (⚠ sınır açık) | CLI ≠ uygulama |
| 5 | `Contracts/` (19) | K008-domain | sözleşme sahipliği |
| 6 | `Router/` (28 JS) | K004/yonetim | gezinme, komut değil |
| 7 | Logger/Redactor | K007-data | veri/güvenlik katmanı |

**Kural:** Bu 7 satır bu dosyada **envanter olarak** görünür (komut
akışına değdikleri için), ama **sahiplik** K011'de değildir →
`application-master.md` §A ile çelişmemesi için burada işaretlenir.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
