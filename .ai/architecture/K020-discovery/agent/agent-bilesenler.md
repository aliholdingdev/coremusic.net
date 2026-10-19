---
node_id: K020-discovery/agent/agent-bilesenler
node_type: subdomain
status: draft
parent_id: K020-discovery/agent
children: []
depends_on:
  - K020-discovery/agent
  - K020-discovery/agent/agent-kapsam
evidence:
  - "Dosya: .ai/architecture/K020-discovery/agent/agent-kapsam.md §15 — auth ailesi × bileşen eşlemesi"
  - "https://developer.spotify.com/documentation/web-api/tutorials/code-pkce-flow (erişim: 2026-10-17)"
  - "https://developer.apple.com/documentation/applemusicapi/generating-developer-tokens (erişim: 2026-10-17)"
validation:
  - "2026-10-17 — web tur-2 ile doğrulandı"
open_questions:
  - "TokenStore saklama yeri: .env mi, şifreli DB mi? (kapsam kapı 10 — açık)"
  - "RateLimitHandler sayaç deposu süreç içi mi, paylaşımlı mı? (tasarım yok)"
group: mimari
---

# K020-discovery/agent — Bileşenler (agent-bilesenler)

**ELI15:** Harici servis entegrasyonunun **yapı taşları**: yedi bileşen, her biri için ne yaptığı,
dil-bağımsız arayüz imzası, kimden bağımlı olduğu ve hangi K düğümüne bağlı olduğu. Burada **kod yok** —
yalnız sözleşme. Kod kalıpları `agent-ornekler.md`'dedir; sınır ve kapılar `agent-kapsam.md`'dedir.

## 1. Amaç, notasyon ve ortak kurallar

**Notasyon (dil-bağımsız):** `f(girdi, ...) -> çıktı` biçiminde yazılmış **arayüz imzası**; `#` ile
satır içi not. `| None` = boş değer dönebilir. `Result` = başarılı/başarısız bildirimi. İmzalar
**tasarım iddiasıdır** (kapı 2 onayı bekliyor); hiçbir imza bir dilin gerçek API'sini garanti etmez.

| # | Ortak kural | Kaynak |
|---|---|---|
| 1 | Token/anahtar yalnız `.env` veya env substitution ile taşınır; `.md`/`.log`/konsol **yasak** | kök AGENTS.md §5 Security · index.md [Satır: 39] |
| 2 | ADR-095 hybrid JWT: CoreMusic oturumu ayrı; harici token onun yerine geçmez | index.md [Satır: 41] |
| 3 | ADR-010/CSRF: `state` + PKCE doğrulaması hiçbir akışta atlanmaz | index.md [Satır: 42] |
| 4 | ADR-101: her harici çağrıda timeout/retry/fallback zorunlu | index.md [Satır: 40] |
| 5 | Yanıtta gerçek HTTP durumu kullanılır; uydurma durum/limit/sürüm yazılmaz | Zero-Hallucination |
| 6 | 429/402/503 bekleme kararları `Retry-After`/doküman kuralına göre verilir | tur-2 kanıtları |

## 2. Bileşen envanteri

| # | Bileşen | Tek cümle sorumluluk | Ana bağımlılık | K bağlantısı |
|---|---|---|---|---|
| 1 | `PkceHelper` | `code_verifier`/`code_challenge` + `state` üretir ve doğrular | yok (saf hesap) | K009-security |
| 2 | `TokenStore` | Erişim/yenileme token'ını **şifreli** saklar (`.env`/DB) | AES-256-GCM, `SecretVault` | K007-data |
| 3 | `JwtSignerEs256` | Apple geliştirici JWT'sini **yalnız ES256** ile imzalar | `SecretVault` (ECDSA anahtarı) | K009-security |
| 4 | `RefreshScheduler` | Vadesi gelen token'ları tekil kilitli yeniler | `TokenStore`, `RateLimitHandler` | K011-application |
| 5 | `RateLimitHandler` | 429/402/503 → `Retry-After`/geri çekilme planı | sayaç deposu (kapı) | K016-observability |
| 6 | `FallbackDriver` | Servis düşerse ikinci sürücüye geçer / boş sonuç döner | `RateLimitHandler` | K012-services |
| 7 | `SecretVault` | Client secret, private key, API key okur; log'u maskeler | `.env` / env substitution | K017/K17-devops (`.env` sahibi) |

**Envanter notu:** 7 bileşen, 3'ü zorunlu (`PkceHelper`, `TokenStore`, `RateLimitHandler`), 2'si
koşullu (`JwtSignerEs256` yalnız Apple; `RefreshScheduler` yalnız refresh sunan servisler), 2'si
altyapı (`FallbackDriver`, `SecretVault`). Eşleme tablosu: `agent-kapsam.md` §15.

## 3. PkceHelper

**Sorumluluk:** Authorization code + PKCE akışının (kapsam aile A) tek kullanımlık belirteçlerini
üretir ve geri dönüşte doğrular. Spotify `code_verifier` için **43–128 karakter** aralığı ve
`SHA-256 → base64url` kuralı, `code_challenge_method=S256` zorunludur
(https://developer.spotify.com/documentation/web-api/tutorials/code-pkce-flow — erişim: 2026-10-17);
Tidal'da authorization code akışında PKCE **tüm istemciler için zorunludur**
(https://developer.tidal.com/documentation/api-sdk/api-sdk-authorization — erişim: 2026-10-17).

```text
PkceHelper
  generateVerifier() -> string            # 43-128 karakter base64url, kriptografik rastgelelik
  challengeS256(verifier) -> string       # base64url(SHA256(verifier)) — padssiz (base64url)
  generateState() -> string               # tek kullanımlık CSRF belirteci (ADR-010)
  buildAuthorizeUrl(endpoint, params) -> URL
      # params: client_id, response_type=code, redirect_uri, scope, state,
      #         code_challenge, code_challenge_method=S256
  verifyState(expected, received) -> bool # false -> akış iptal edilir, token exchange'e geçilmez
  redeemVerifier(verifier) -> None        # tek kullanımlıktır; exchange sonrası yok edilir
```

**Bağımlılık tablosu:**

| # | Bağımlılık | Tür | Not |
|---|---|---|---|
| 1 | kriptografik rastgele üreteç | dış (dil standart kütüphanesi) | `random` **kullanılmaz** |
| 2 | SHA-256 + base64url | dış (dil standart kütüphanesi) | sabit: `S256` |
| 3 | oturum taşıma (verifier/state saklama) | iç | sunucu oturumu; `.md`'ye yazılmaz |
| 4 | `SecretVault` | — | **bağımlı değil** (sır üretmez, yalnız taşır) |

**Davranış kuralları:** (1) verifier bir kez üretilir, bir kez kullanılır. (2) `verifyState` başarısızsa
istek 4xx ile reddedilir, kod asla token ucuyla değiştirilmez. (3) `code_challenge_method` değeri
**dizge olarak `S256`** yazılır (harf duyarlılığı korunur). (4) `verifier` log'a/MD'ye yazılmaz.

**K bağlantıları:** K009-security (CSRF/PKCE politikası) · K011-application/web (`redirect_uri` taşıyan
taraf) · agent-kapsam.md §4.1 (aile A adımları).

## 4. TokenStore

**Sorumluluk:** Token exchange sonrası `access_token`/`refresh_token`'ı **şifreli** saklar; okumada
çözüp verir; refresh dönüşünde **yeni refresh gelmediyse eskisini korur** (Spotify kuralı:
https://developer.spotify.com/documentation/web-api/tutorials/code-pkce-flow — erişim: 2026-10-17).
Şema örneği `agent-ornekler.md` §16 (SQL, AES-256-GCM sütunları).

```text
TokenStore
  save(service, accountRef, bundle) -> Result      # AES-256-GCM: nonce + ciphertext + tag
  load(service, accountRef) -> TokenBundle | None
  markRefreshed(service, accountRef, newBundle) -> Result
      # kural: newBundle.refresh missing ise ESKİ refresh değeri korunur
  delete(service, accountRef) -> Result            # hesap bağını koparırken kullanılır
  listExpiringWithin(window: Duration) -> [TokenRef]
  pruneExpired() -> int                            # yalnız erişim token'ı temizlenir

TokenBundle = {
  access : Secret,          # düz metin yalnız bellekte, kısa süre
  refresh: Secret | None,   # yoksa bu servis refresh sunmaz
  expiresAt : Timestamp,    # expires_in'den türetilir (UTC)
  scope     : [String],
  tokenType : String        # Spotify yanıtı: "Bearer"
}
Saklama yüzeyi: tek hesap -> .env (env substitution) | çoklu hesap -> DB sütunları (şifreli)
```

**Bağımlılık tablosu:**

| # | Bağımlılık | Tür | Not |
|---|---|---|---|
| 1 | AES-256-GCM (nonce+tag) | dış (şifreleme primitifi) | anahtar `SecretVault`'ta |
| 2 | `SecretVault` | iç | anahtar okuma |
| 3 | veritabanı (DB seçeneği) | dış | şema: `agent-ornekler.md` §16 · sahiplik K007/data |
| 4 | `.env` (tek hesap seçeneği) | dış | env substitution; `.md`/`.log` **yasak** |
| 5 | `RefreshScheduler` | iç (tüketicisi) | `listExpiringWithin` çağırır |

**Davranış kuralları:** (1) Düz metin token yalnız fonksiyon belleğindedir; iş bitince maskelenir.
(2) `load` başarısızlıkta `None` döner — uydurma token **üretilmez**. (3) Aynı `service+accountRef`
için eşzamanlı `save` yasaktır (tekil yazma). (4) Şema değişikliği K007/data üzerinden yürür (BCNF).

**K bağlantıları:** K007/data (şema + indeks) · K009-security (şifreleme) · agent-kapsam.md §6 kapı 10 (saklama yeri kararı).

## 5. JwtSignerEs256

**Sorumluluk:** Apple Music geliştirici token'ını (kapsam aile D) sunucuda imzalar. Başlık `alg: ES256`
+ `kid` (10 karakter), `iss` 10 karakter Team ID, `exp` şu andan en fazla **15777000 sn / 6 ay**;
**yalnızca ES256 kabul edilir, aksi 401**; tüm isteklerde `Authorization: Bearer`
(https://developer.apple.com/documentation/applemusicapi/generating-developer-tokens — erişim: 2026-10-17).

```text
JwtSignerEs256
  kid() -> string                       # 10 karakter Key ID (başlıkta taşınır)
  sign(claims, now) -> Secret
      # header: { alg: "ES256", kid: <kid> }
      # claims: { iss: <10 karakter Team ID>, iat: now, exp: now + TTL, origin?: <origin> }
      # TTL kuralı: exp - now <= 15777000 saniye (6 ay) — asla aşılmaz
  validateLocal(token, now) -> bool     # yerel ön denetim: alg, exp, iss tutarlılığı
  cacheKey() -> string                  # imzalanmış token'ın önbellek anahtarı (süreli)
Secret girişi: SecretVault.get("APPLE_PRIVATE_KEY")  # asla kod/metin gövdesinde değil
```

**Bağımlılık tablosu:**

| # | Bağımlılık | Tür | Not |
|---|---|---|---|
| 1 | ECDSA P-256 + SHA-256 imzası | dış (dil standart kütüphanesi) | ES256 tek yol |
| 2 | `SecretVault` | iç | private key + Team ID + `kid` |
| 3 | sistem saati (`now`) | dış | `iat`/`exp` üretimi — güvenilir saat şart |
| 4 | önbellek (süreli) | iç | 6 ay sınırı içinde kısa TTL tercih edilir |

**Davranış kuralları:** (1) `alg` değeri kodda sabittir; istek/parametre ile değiştirilemez. (2) TTL
15777000 sn'yi aşarsa imzalama **reddedilir** (hata, sessiz kısaltma değil). (3) Kullanıcı token'ı
(media user token) bu bileşenin konusu **değildir** — ayrı akış (kapsam §4.4). (4) MusicKit JS tarafında
`MusicKit.configure({developerToken})` çağrısına token verilir
(https://js-cdn.music.apple.com/musickit/v1/index.html — erişim: 2026-10-17); token yine sunucuda üretilir.

**K bağlantıları:** K009-security (imza politikası) · K012-services (imzalayan servis katmanı) · agent-kapsam.md §4.4.

## 6. RefreshScheduler

**Sorumluluk:** `expires_in` yaklaşan erişim token'larını zamanında yeniler; aynı `service+accountRef`
için **eşzamanlı yenilemeyi tekil kilit ile engeller**; dönüşte `markRefreshed` üzerinden eski refresh
koruma kuralını uygular. Client credentials akışında `refresh_token` **dönmez** — plan o durumda
**yeniden edinimdir** (kapsam §4.3).

```text
RefreshScheduler
  plan(items: [TokenRef], safetyWindow: Duration) -> [RefreshTask]
  tick(now) -> [RefreshTask]                 # vadesi safetyWindow içinde olanları seçer
  execute(task) -> Result                    # POST token ucu: grant_type=refresh_token
      # Spotify: eskisi korunur (yenisi gelmeyebilir)
      # Tidal: authorization code akışında refresh token üçüncü akıştır
      # Amazon: token ~1 saat -> daha sık döngü (expires_in'e göre)
  acquireLock(service, accountRef) -> bool   # tekil kilit; alınamazsa atlanır
  releaseLock(service, accountRef) -> None
  onRotation(oldBundle, newBundle) -> Result # newBundle.refresh yok -> oldBundle.refresh kalır

safetyWindow: TASARIM değişkeni (kapı 9 — henüz onaylanmadı)
```

**Bağımlılık tablosu:**

| # | Bağımlılık | Tür | Not |
|---|---|---|---|
| 1 | `TokenStore` | iç | `listExpiringWithin` + `markRefreshed` |
| 2 | `SecretVault` | iç | client secret / Basic auth kimliği (Tidal) |
| 3 | `RateLimitHandler` | iç | yenileme isteği de kota sayar |
| 4 | zamanlayıcı (scheduler) | dış | süreç içi döngü veya görev kuyruğu |
| 5 | kilit (lock) | iç | yarış koşulunu engeller |

**Davranış kuralları:** (1) Kilitsiz ikinci bir yenileme **başlatılmaz**. (2) Yenileme hatasında
üsteleme yok: RateLimitHandler kararı geçerlidir (429 → `Retry-After`). (3) Süresi geçen token'la
çağrı yapılmaz; yenileme başarısızsa çağrı `FallbackDriver`'a devredilir (ADR-101). (4) Yenileme
girdileri (refresh token) log'a yazılmaz.

**K bağlantıları:** K011-application (zamanlanmış görevler) · K016-observability (yenileme metrikleri) · agent-kapsam.md §6 kapı 9.

## 7. RateLimitHandler

**Sorumluluk:** Servis sınırına takılan istekte **doküman kuralına uygun bekleme**yi hesaplar.
Doğrulanmış kurallar: Spotify yuvarlanan **30 sn** pencere → **429** + `Retry-After`
(https://developer.spotify.com/documentation/web-api/concepts/rate-limits — erişim: 2026-10-17);
Apple geçici **429**; Musixmatch **402** + `X-RateLimit-Remaining` (gece yarısı UTC sıfırlanır);
MusicBrainz **1/sn/IP** → %100 ret → **503**; Amazon süresi dolmuş token → **401 INVALID_ACCESS_TOKEN**.

```text
RateLimitHandler
  before(service, now) -> Proceed | Wait(duration)
      # yerel sayaç: servis penceresi dolmuş mu? (Spotify 30 sn yuvarlanan pencere)
  after(service, response) -> Decision
      # 429 + Retry-After var -> Wait(retryAfter)
      # 429 + QUOTA_EXCEEDED (Spotify kota modu) -> Blocked(kota uyarısı, tekrar yok)
      # 402 (Musixmatch) -> Wait(sabaha kadar) + lyrics sayacını da durdur
      # 503 (MusicBrainz) -> Wait(1/sn oranına inene kadar) + kuyruğu yavaşlat
      # 401 INVALID_ACCESS_TOKEN (Amazon) -> Reauthenticate  # backoff DEĞİL
  retryAfter(response) -> Duration | None   # başlık saniye cinsinden
  backoff(attempt) -> Duration              # üstel geri çekilme + jitter (başlık yoksa)
  recordOutcome(service, outcome) -> None   # gözlemlenebilirlik için sayaç yazımı

Decision = Proceed | Wait(d) | Blocked(reason) | Reauthenticate(reason)
```

**Bağımlılık tablosu:**

| # | Bağımlılık | Tür | Not |
|---|---|---|---|
| 1 | yanıt başlıkları (`Retry-After`, `X-RateLimit-Remaining`) | dış | yalnız okunur |
| 2 | sayaç deposu (süreç içi / paylaşımlı) | iç | **kapı: tasarım yok** |
| 3 | `FallbackDriver` | iç (tüketicisi) | `Blocked`/tekrar edilemeyen durumda geçiş |
| 4 | gözlemlenebilirlik (metrik/log) | dış | yalnız süre/durum yazılır, token değil |

**Davranış kuralları:** (1) `Retry-After` **varsa onu kullan**; üstel backoff yalnız başlık yoksa.
(2) Kota aşımı (`QUOTA_EXCEEDED`) ile geçici 429 ayrılır: kota aşımında tekrar döngüsü **kurulmaz**.
(3) 401/402 durumlarında backoff **kullanılmaz** — yeniden kimlik / beklemek gerekir. (4) Bekleme
süreleri log'a yazılır (token değil).

**K bağlantıları:** K016-observability (metrik) · K009-security (sınır aşımının kötüye kullanımı) · agent-kapsam.md §16 (hata yolları).

## 8. FallbackDriver

**Sorumluluk:** ADR-101 gereği her harici çağrının **timeout/retry/fallback** zincirini taşır: birinci
sürücü (servis) kalıcı başarısız olursa ikinci sürücüye geçer, o da yoksa **boş/eksik sonuç** döner
(fırlatmaz). Kapsam kararı: YouTube Music kapalı olduğu için katalog için ikinci sürücü bulunması
**zaruridir** (kapsam §5/9).

```text
FallbackDriver
  chain() -> [Driver]                      # öncelik sırası: servis listesi (tasarım)
  drive(request) -> Response | EmptyResult
  onDriverFailed(driverId, error) -> Decision
      # timeout / kalıcı 5xx -> sıradaki sürücü
      # 429/402 Blocked -> sıradaki sürücü DEĞİL (RateLimitHandler kararı beklenir)
      # zincir bitti -> EmptyResult(eksik_veri_bildirimi)
  deadline() -> Duration                   # toplam bütçe (ADR-101 timeout)
  attemptBudget() -> int                   # en fazla N deneme, sonra zincir kapanır
```

**Bağımlılık tablosu:**

| # | Bağımlılık | Tür | Not |
|---|---|---|---|
| 1 | `RateLimitHandler` | iç | `Blocked` kararı geçişi yönetir |
| 2 | `SecretVault` | iç | her sürücünün kendi kimliği |
| 3 | `TokenStore` | iç | sürücü token'ı hazır mı kontrolü |
| 4 | ağ istemcisi (timeout ayarı) | dış | ADR-101 timeout değeri |
| 5 | gözlemlenebilirlik | dış | zincir geçişleri log'a (durum + süre) |

**Davranış kuralları:** (1) Zincir **sonu boştur**: hata fırlatmak yerine eksik-veri sonucu döner.
(2) Sonsuz döngü yok: `attemptBudget` bittiğinde kapanır. (3) `EmptyResult` yanıtta hangi sürücülerin
düştiği **bildirilir** (gerçek durum kodlarıyla). (4) Fallback, CoreMusic oturumuna dokunmaz (ADR-095).

**K bağlantıları:** K012-services (çağrı sahibi) · K010-network (timeout/taşıma) · agent-kapsam.md §9/10.

## 9. SecretVault

**Sorumluluk:** Tüm sırları (`client_secret`, ECDSA private key, API key, `api_sig` için secret) tek
okuma yüzeyinden verir; log metinlerini maskeler. **Yalnız** `.env` / env substitution kullanır;
`.md`, `.log`, konsola yazım **yasaktır** (kök AGENTS.md §5 Security · index.md [Satır: 39]).

```text
SecretVault
  get(name) -> Secret | None               # ör. isimler: SPOTIFY_CLIENT_ID, TIDAL_CLIENT_SECRET,
                                           #   APPLE_TEAM_ID, APPLE_PRIVATE_KEY_REF, LASTFM_SECRET
  put(name, value) -> Result               # yalnız süreç içi; kalıcı yazım .env dışına olmaz
  redact(text) -> text                     # token/kod/verifier -> [REDACTED]
  rotationState(name) -> { rotatedAt, expiresAt } | None   # döngü kaydı (varsa)
  require(name) -> Secret                  # yoksa açılışta hata — sessiz default YOK

yasak: print/logcat/Exception metnine Secret yazmak, .md dosyasına saklamak
```

**Bağımlılık tablosu:**

| # | Bağımlılık | Tür | Not |
|---|---|---|---|
| 1 | `.env` / env substitution | dış | tek kalıcı yüzey; `.gitignore` kapsamındadır |
| 2 | CI/dağıtım gizli değişkenleri | dış | K17/devops sahipliğinde |
| 3 | log/izleme akışı | iç | `redact` tüm loglayıcılar için zorunlu |
| 4 | `TokenStore`, `JwtSignerEs256`, `RefreshScheduler`, `FallbackDriver` | iç (tüketiciler) | bkz. §10 |

**Davranış kuralları:** (1) `get` hiçbir zaman değer dizesini konsola yazmaz. (2) `redact` log
gövdesinde `access_token=` gibi kalıpları `[REDACTED]` ile değiştirir (registry §17/3 maskeleme).
(3) `require` yokluğunda açılış **hata verir** — fail-safe, fail-open değil. (4) Rotasyon `rotatedAt`
kaydıyla izlenir; eski anahtar hemen geçersiz kılınmaz (kod tarafı kapıya bağlıdır).

**K bağlantıları:** K009-security (sır politikası) · K17-devops (`.env`/CI gizli değişkeni) · K016-observability (redaksiyon).

## 10. Bileşen bağımlılık grafiği

```text
                    SecretVault  (sır okuma + redaksiyon)
                   /     |      \                \
                  v      v       v                v
          PkceHelper  TokenStore  JwtSignerEs256  FallbackDriver
              |          ^   ^         |               ^
              |          |   |         |               |
              |   RefreshScheduler ----+--------> RateLimitHandler
              |          |                                 |
              +----------+---------------------------------+
                         (state/PKCE doğrulaması + bekleme kararları)
```

| # | Kenar | Ne taşır | Yön |
|---|---|---|---|
| 1 | `SecretVault` → `TokenStore` | AES-256-GCM anahtarı | sır → şifreleme |
| 2 | `SecretVault` → `JwtSignerEs256` | ECDSA private key, Team ID, `kid` | sır → imza |
| 3 | `SecretVault` → `RefreshScheduler` | client secret (Tidal Basic auth) | sır → kimlik |
| 4 | `SecretVault` → `FallbackDriver` | sürücü kimlikleri | sır → çağrı |
| 5 | `RefreshScheduler` → `TokenStore` | `listExpiringWithin` / `markRefreshed` | plan → depo |
| 6 | `RefreshScheduler` → `RateLimitHandler` | yenileme isteği kota kontrolü | plan → sınır |
| 7 | `RateLimitHandler` → `FallbackDriver` | `Blocked`/`Wait` kararı | sınır → geçiş |
| 8 | `PkceHelper` → oturum taşıma | `verifier` + `state` (kısa ömür) | belirteç → oturum |

**Çizim kuralı:** Döngü yoktur (graf tek yönlüdür); `FallbackDriver` hiçbir bileşeni çağırmaz,
yalnız yürütür. Bu, ADR-101'in "fallback en son çare" kuralıyla uyumludur (tasarım iddiası).

## 11. Yaşam döngüsü — token ve bileşen olayları

1. Kullanıcı giriş ister → `PkceHelper.generateVerifier()` + `generateState()` → sunucu oturumunda tutulur.
2. `/authorize`'a yönlendirme: `client_id`, `response_type=code`, `redirect_uri`, `scope`, `state`,
   `code_challenge`, `code_challenge_method=S256` (Spotify parametre kümesi — kanıt: kod-pkce-flow, 2026-10-17).
3. Geri dönüş: `verifyState` → başarısızsa **durdur**; başarılıysa `code` alınır (ADR-010).
4. `RateLimitHandler.before` → `Proceed` ise token ucu çağrılır (timeout: ADR-101).
5. Yanıt ayrıştırılır: `access_token`, `token_type=Bearer`, `scope`, `expires_in`, `refresh_token`.
6. `TokenStore.save` → AES-256-GCM ile şifrelenir; `expiresAt = now + expires_in` (UTC).
7. Çağrılar `Authorization: Bearer <TOKEN>` ile gider → `RateLimitHandler.after` 429/402/503 kararları.
8. `RefreshScheduler.tick` vadeyi yakalar → kilit → `grant_type=refresh_token` → `markRefreshed`
   (yeni refresh yoksa **eskisi korunur**).
9. Servis kalıcı düşerse `FallbackDriver.drive` sıradaki sürücüye geçer; zincir bitti → `EmptyResult`.
10. Süreç kapanışta: bellekteki düz metin maskelenir; log yalnız olay/durum/süre taşır.

## 12. Güvenlik eşleri (tehdit × bileşen × önlem)

| # | Tehdit | Bileşen | Önlem |
|---|---|---|---|
| 1 | Authorization code interceptor (CSRF) | `PkceHelper` | `state` + PKCE doğrulaması — ADR-010, atlanmaz |
| 2 | Token sızıntısı (DB/`.env`) | `TokenStore` | AES-256-GCM nonce+ciphertext+tag; düz metin saklanmaz |
| 3 | Log sızıntısı | `SecretVault` | `redact` → `[REDACTED]`; token `.md`/`.log`/konsol yasak |
| 4 | Yanlış algoritmayla imza | `JwtSignerEs256` | yalnız `alg: ES256`; aksi 401 (Apple dokümanı) |
| 5 | Yenileme yarışı (çift refresh) | `RefreshScheduler` | tekil `acquireLock`; ikinci döngü başlatılmaz |
| 6 | Eski refresh'in iptali | `RefreshScheduler` | `onRotation`: yeni gelmediyse eskisi korunur |
| 7 | Kota kilidi (429 döngüsü) | `RateLimitHandler` | `Retry-After` bekleme; `QUOTA_EXCEEDED`'da tekrar yok |
| 8 | Servis kesintisi (askıda kalma) | `FallbackDriver` | timeout + bütçe + zincir sonu `EmptyResult` |
| 9 | Harici token'ın oturumla karışması | hepsi | ADR-095 ayrımı: ayrı depo, ayrı taşıyıcı, ayrı log alanı |
| 10 | Kodda gerçek anahtar | `SecretVault.require` | yalnız placeholder/`get` okuması; kod örneklerinde `YOUR_CLIENT_ID` |

## 13. K bağlantıları özeti

| # | Bileşen | K009-security | K007-data | K011-application | K012-services | K016-observability | K17-devops |
|---|---|---|---|---|---|---|---|
| 1 | `PkceHelper` | ✓ (politika) | — | ✓ (`redirect_uri`) | — | — | — |
| 2 | `TokenStore` | ✓ (şifreleme) | ✓ (şema) | — | — | ✓ (metrik) | ✓ (`.env` seçeneği) |
| 3 | `JwtSignerEs256` | ✓ (imza) | — | — | ✓ (imzalayan katman) | ✓ (imza TTL) | ✓ (key rotasyonu) |
| 4 | `RefreshScheduler` | — | ✓ (görev tablosu) | ✓ (zamanlanmış görev) | ✓ (yenileme çağrısı) | ✓ (yenileme metriği) | — |
| 5 | `RateLimitHandler` | ✓ (aşım) | △ (paylaşımlı sayaç) | — | ✓ | ✓ | — |
| 6 | `FallbackDriver` | — | — | — | ✓ | ✓ | — |
| 7 | `SecretVault` | ✓ (sır) | — | — | — | ✓ (redaksiyon) | ✓ (env sahibi) |

## 14. Ölçüm ve kanıt

| # | Ölçüm | Değer | Kanıt |
|---|---|---|---|
| 1 | Bileşen sayısı | 7 (3 zorunlu · 2 koşullu · 2 altyapı) | §2 |
| 2 | Dil-bağımsız imza sayısı | 30+ (§3–§9 kod blokları) | bu dosya |
| 3 | Bağımlılık kenarı (graf §10) | 8 | §10 tablosu |
| 4 | Güvenlik eşleşmesi | 10 satır | §12 |
| 5 | Doğrulanmış sınır kuralı | 429 + `Retry-After` · 402 · 503 · 401 INVALID_ACCESS_TOKEN | §7 (URL + 2026-10-17) |
| 6 | Apple JWT kuralı | `alg: ES256` · `kid` 10 · `exp` ≤ 15777000 sn | §5 (URL + 2026-10-17) |
| 7 | PKCE kuralı | verifier 43–128 karakter · `S256` | §3 (URL + 2026-10-17) |
| 8 | Kapı bağımlısı bileşen | hepsi (kapı 2 tasarım onayı) | `agent-kapsam.md` §6 |

## 15. Açık sorular

1. TokenStore saklama yüzeyi: tek hesap `.env` mi, çoklu hesap şifreli DB mi? → **kapı 10**.
2. RateLimitHandler sayaç deposu süreç içi mi (çoklu süreçte yetersiz) yoksa paylaşımlı mı? → tasarım yok.
3. `RefreshScheduler.safetyWindow` değeri ne olmalı? → **kapı 9**.
4. AES-256-GCM anahtarı döndürülecek mi; döndürme süresi ve `rotationState` politikası nedir? → açık.
5. FallbackDriver zincirinin sırası (hangi servis yedek hangisidir) kim karar verir? → kapsam kararı gerek.
6. `JwtSignerEs256` önbellek TTL'si kaç saniye? (6 ay üst sınırı doğrulanmış; tercih edilen kısa TTL yok.)
7. `EmptyResult` biçiminde eksik-veri yanıtının API sözleşmesi ne olacak? → K011/API sözleşmesiyle uyum.
8. MusicBrainz 1/sn ve Spotify 30 sn penceresi tek sayaç koduyla mı taşınacak? → teyit gerekir.

## 16. Bileşen başına kabul kriteri (tasarım iddiası)

Kabul kriterleri, kapı 2 onayında **test edilebilir** cümlelerdir; her madde `agent-dogrulama.md`
matrisinde karşılık ister.

### 16.1 PkceHelper

1. Üretilen `code_verifier` uzunluğu 43–128 karakter aralığındadır (Spotify kuralı, kanıt §3).
2. `challengeS256` çıktısı base64url'dur (`+`/`/` yok, `=` dolgusu yok).
3. `verifyState` uyuşmazlığında token exchange **hiç çağrılmaz**.
4. Aynı `verifier` ikinci kez kullanılamaz (tekil kayıt).
5. `S256` dizesi harf duyarlı yazılır; `s256` gibi varyantlar reddedilir.

### 16.2 TokenStore

1. DB/`.env` yüzeyinde **düz metin** token sütunu/dosya satırı bulunmaz (AES-256-GCM: nonce+ciphertext+tag).
2. `markRefreshed` sonrası yeni `refresh` yoksa eski değer aynen korunur (Spotify kuralı).
3. `load` boşlukta `None` döner; uydurma bundle üretilmez.
4. `expiresAt` UTC olarak saklanır ve `expires_in`'den türetilir.
5. Aynı `service+accountRef` için eşzamanlı yazma kilidi altında gerçekleşir.

### 16.3 JwtSignerEs256

1. Üretilen token başlığında yalnız `alg: ES256` görünür; `kid` 10 karakterdir.
2. `exp - now > 15777000` durumunda imzalama **hata** ile reddedilir.
3. `iss` 10 karakter Team ID'dir; `origin` web için parametre olarak taşınabilir.
4. Private key hiçbir zaman fonksiyon parametresi/yanıt gövdesi olarak dışarı çıkmaz.
5. Kullanıcı token'ı (media user token) bu bileşenden **üretilmez**.

### 16.4 RefreshScheduler

1. Aynı `service+accountRef` için ikinci eşzamanlı yenileme başlatılmaz (tekil kilit).
2. 429/gelen `Retry-After` kararı yenileme döngüsünü durdurur (kota kilidi oluşmaz).
3. `expiresAt` yaklaşmadan (safetyWindow içinde) yenileme tetiklenir.
4. Yenileme başarısızsa eski bundle **geçerli kalır**; token silinmez.
5. Client credentials akışında plan, "yeniden edinim" olarak etiketlenir.

### 16.5 RateLimitHandler

1. `Retry-After` başlığı varsa bekleme tam olarak o değerdir (üstel backoff'a düşülmez).
2. `QUOTA_EXCEEDED` (Spotify kota modu) ile geçici 429 **ayrı** ele alınır; tekrar döngüsü kurulmaz.
3. 402 (Musixmatch) durumunda lyrics sayacı da durdurulur (çifte limit).
4. 503 (MusicBrainz) durumunda kuyruk 1/sn oranına inene kadar yavaşlatılır.
5. Bekleme süreleri ve durumlar metrik/log'a yazılır; token yazımı yoktur.

### 16.6 FallbackDriver

1. Zincir bittiğinde hata fırlatılmaz; eksik-veri sonucu döner.
2. `attemptBudget` aşılmaz; sonsuz döngü testle gösterilemez.
3. `Blocked` (kota) kararı ikinci sürücüye **geçiş tetiklemez**.
4. Geçişler gerçek durum kodlarıyla log'a yazılır (uydurma kod yok).
5. Toplam `deadline` bütçesi tüm zincir için ortaktır (ADR-101).

### 16.7 SecretVault

1. `get`/`require` değerleri konsola/loga **asla** yazılmaz.
2. `redact` çıktısında token/kod/verifier kalıpları `[REDACTED]` olur.
3. `require` eksik anahtarda açılışı **durdurur** (fail-safe).
4. `.md` dosyasına yazım yasağı kod incelemesinde kontrol edilir.
5. `rotationState` yalnız süreç içi/kayıtlı döngü verisini taşır.

## 17. Bileşen × hata dönüş şekli

| # | Bileşen | Hata durumu | Dönüş biçimi | Yutulur mu? |
|---|---|---|---|---|
| 1 | `PkceHelper` | `state` uyuşmazlığı | `false` → istek reddi | hayır — akış durur |
| 2 | `PkceHelper` | rastgelelik hatası | süreç hatası (fail-closed) | hayır |
| 3 | `TokenStore` | anahtar yok | `None` / `Result(hata)` | evet — çağrı gecikir, akış sürer |
| 4 | `TokenStore` | şifre çözme hatası | `Result(hata)` + kaydın kilitlenmesi | hayır |
| 5 | `JwtSignerEs256` | TTL ihlali | imzalama reddi | hayır |
| 6 | `RefreshScheduler` | 429 `Retry-After` | `Wait` → erteleme | evet (bekleme) |
| 7 | `RefreshScheduler` | kilitsiz erişim | `atlanır` (skip, hata değil) | evet |
| 8 | `RateLimitHandler` | `QUOTA_EXCEEDED` | `Blocked` + kullanıcıya uyarı | hayır — tekrar yok |
| 9 | `FallbackDriver` | zincir tükendi | `EmptyResult` (eksik veri bildirimi) | evet — bildirimli |
| 10 | `SecretVault` | isim bulunamadı | `require` → açılış hatası | hayır |

## 18. Arayüz değişiklik protokolü (sözleşme kırma)

| # | Değişiklik | Koşul | Etkilenen |
|---|---|---|---|
| 1 | İmzaya parametre ekleme | geriye dönük uyumlu | kapı 2 kaydı |
| 2 | İmzadan parametre çıkarma | **sözleşme kırma** → yeni ADR + kapı 2 yeniden | tüm bağımlılar |
| 3 | `TokenBundle` alanına erişim/refresh dışı alan | uyumlu (opsiyonel) | TokenStore şeması (K007/data) |
| 4 | Yeni bileşen (ör. imza istemcisi) | kapsam değişikliği → `agent-kapsam.md` §13 kaydı | bu dosya §2 |
| 5 | Servis ekleme/çıkarma | `agent-kapsam.md` §3 + `agent-dogrulama.md` §2 güncellenir | tüm dosyalar |
| 6 | Sınır kuralı değişikliği (ör. kota rakamı) | yalnız **resmi doküman kanıtıyla** | §7, `agent-web-arastirma.md` |

## 19. Gözlemlenebilirlik olayları (tasarım önerisi)

| # | Olay | Bileşen | Alanlar | Token içerir mi |
|---|---|---|---|---|
| 1 | `pkce.issue` | `PkceHelper` | servis, state varlığı (değer değil) | hayır |
| 2 | `token.exchange.ok` / `.fail` | TokenStore/çağrı | servis, gerçek durum kodu, süre | hayır |
| 3 | `token.refresh.ok` / `.skipped_lock` | `RefreshScheduler` | servis, bekleme, eski-yeni korundu mu | hayır |
| 4 | `ratelimit.wait` | `RateLimitHandler` | servis, `Retry-After` değeri, deneme no | hayır |
| 5 | `ratelimit.blocked` | `RateLimitHandler` | servis, kota tipi | hayır |
| 6 | `fallback.switch` | `FallbackDriver` | kaynak sürücü → hedef sürücü, gerçek durum | hayır |
| 7 | `fallback.exhausted` | `FallbackDriver` | zincir uzunluğu, toplam süre | hayır |
| 8 | `jwt.sign` | `JwtSignerEs256` | `kid`, TTL saniye | hayır |
| 9 | `vault.redact` | `SecretVault` | maskelenen desen adedi | hayır |
| 10 | `auth.flow.cancel` | `PkceHelper` | sebep (`state_mismatch`) | hayır |

## 20. SSS

| # | Soru | Cevap |
|---|---|---|
| 1 | Bileşenler hangi dilde yazılacak? | Karar verilmedi; imzalar dil-bağımsızdır (`agent-ornekler.md` 13 dil kalıbı taşır) |
| 2 | `FallbackDriver` yedek olarak hangi servisi kullanır? | Kapsam kararı gerekir; YouTube Music KAPALI olduğu için yedek zorunlu |
| 3 | `SecretVault` anahtarını kim döndürür? | K17/devops + security; bu ağaç yalnız okur |
| 4 | TokenStore DB'de hangi şemada? | `agent-ornekler.md` §16 taslak şema (AES-256-GCM sütunları) |
| 5 | `JwtSignerEs256` neden ayrı bileşen? | Yalnız ES256 kabul edilir (Apple); genel JWT bileşeni yanlış `alg` riski taşır |
| 6 | Retry sayısı sabit mi? | Deneme bütçesi `FallbackDriver.attemptBudget`'tadır; değer kapı 2'ye bağlı |
| 7 | Bu dosya kod kanıtı içerir mi? | Hayır — sözleşme/idari tasarım; kod örnekleri ayrı dosyadır |
| 8 | Ölçümler üretim metriği mi? | Hayır — tasarım iddiası; üretim metriği K016-observability alanı |

**Authority:** Bayram Ali / Vault Steward
