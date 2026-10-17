---
node_id: K020-discovery/agent/agent-web-arastirma
node_type: subdomain
status: draft
parent_id: K020-discovery/agent
children: []
depends_on:
  - K020-discovery/agent
evidence:
  - "https://developer.spotify.com/documentation/web-api/tutorials/code-pkce-flow (erişim: 2026-10-17)"
  - "https://developer.apple.com/documentation/applemusicapi/generating-developer-tokens (erişim: 2026-10-17)"
  - "Dosya: .ai/architecture/K020-discovery/discovery-web-arastirma.md — üst düzey tur-2 taraması (12 servis)"
validation:
  - "2026-10-17 — web tur-2 ile doğrulandı"
open_questions:
  - "Deezer resmi sayısal kota rakamı yayımlanmamış → ⚠️ VERIFICATION REQUIRED"
  - "Amazon Music kapalı Beta — erişim başvurusu kararı yok"
group: kanit
---

# K020-discovery/agent — Web Araştırması (agent-web-arastirma)

**ELI15:** Bu dosya **tur-2** (2026-10-17) web taramasının **auth akışı odaklı** kanıt defteridir:
her servis için kimlik akışının adımları, token tipi, kritik kural ve **resmi** URL + erişim tarihi.
Ham tarama (kota + keşif değeri) üst düzey `discovery-web-arastirma.md`'dedir; burada yalnız
**auth akışı detayı** taşınır — tekrar değil, derinlik.

## 1. Yöntem ve tur tanımı

| # | Kural | Uygulama |
|---|---|---|
| 1 | Tur | tur-2 · **2026-10-17** · auth akışı odaklı (tur-1: 2026-10-12, ADR-097 §1 madde 6 (satır 33)) |
| 2 | Kaynak önceliği | resmi developer docs (birincil) → resmi support/help (ikincil) |
| 3 | İddia kuralı | yalnız okunan dokümanda geçen değer yazılır; sayı/API yayımı yoksa `⚠️ VERIFICATION REQUIRED` |
| 4 | URL kuralı | her satırda resmi URL + erişim tarihi taşır |
| 5 | Kod kuralı | bu dosyada kod yok (kod: `agent-ornekler.md`) |
| 6 | Tur-1 köprüsü | tur-1 iddiaları `agent-dogrulama.md` §3'te çapraz kontrol edilir (bilgi tekrarı yasağı) |

**Güvenlik kanunu (bu dosyada da tekrar):** token/anahtar yalnız `.env`/env substitution ile taşınır —
`.md`/`.log`/konsol **yasak** · ADR-095: CoreMusic oturumu ayrıdır · ADR-010: `state` + PKCE doğrulaması
atlanmaz · ADR-101: timeout/retry/fallback zorunlu · bu dosyadaki tüm token örnekleri placeholder'dır.

**Karar etiketleri:** `KAPSAM İÇİ` = entegrasyon mümkün · `KOŞULLU` = ek kapı/lisans gerek ·
`KAPALI` = erişim/ToS engeli.

## 2. Özet tablo (Servis × auth akışı × resmi URL)

| # | Servis | Auth akışı | Token tipi | Kritik kural | Resmi URL | Erişim |
|---|---|---|---|---|---|---|
| 1 | Spotify | authorization code · **authorization code + PKCE** · client credentials | `Bearer` erişim + refresh | verifier 43–128 · `S256`; yenilemede refresh gelmeyebilir | https://developer.spotify.com/documentation/web-api/tutorials/code-pkce-flow | 2026-10-17 |
| 2 | Apple Music | developer token (JWT) | JWT `Bearer` · `ES256` + `kid` | yalnız ES256 (aksi 401) · `exp` ≤ 15777000 sn | https://developer.apple.com/documentation/applemusicapi/generating-developer-tokens | 2026-10-17 |
| 3 | YouTube Music | **resmi public API yok** | — | resmi olmayan istemciler ToS'a aykırı | https://developers.google.com/youtube/terms/api-services-terms-of-service | 2026-10-17 |
| 4 | Tidal | **OAuth 2.1**: client credentials · authorization code · refresh | `Bearer` | code akışında PKCE **tüm istemciler için zorunlu** | https://developer.tidal.com/documentation/api-sdk/api-sdk-authorization | 2026-10-17 |
| 5 | Deezer | OAuth (`perms` parametresi) + **kimliksiz katalog** | OAuth token (yazma) · katalog: token yok | `q` zorunlu; resmi sayısal kota yok ⚠️ | https://developers.deezer.com/api | 2026-10-17 |
| 6 | MusicBrainz | kimliksiz (`/ws/2`) | — | **1/sn/IP** → %100 ret → **503**; `User-Agent` zorunlu | https://musicbrainz.org/doc/MusicBrainz_API/Rate_Limiting | 2026-10-17 |
| 7 | ListenBrainz | özel **user token** | `Authorization` başlığı | yalnız HTTPS; `User-Agent` zorunlu; katalog arama **yok** | https://listenbrainz.readthedocs.io/en/latest/users/api/index.html | 2026-10-17 |
| 8 | Genius | OAuth2 code · okumada client access token | `Bearer` | **ticari kullanım lisans ister** | https://docs.genius.com/ | 2026-10-17 |
| 9 | Discogs | OAuth 1.0a · **personal access token** | OAuth / PAT | kimliğe göre kota; `User-Agent` zorunlu | https://www.discogs.com/developers | 2026-10-17 |
| 10 | Amazon Music | LWA (OAuth 2.0) — **kapalı Beta** | `Bearer` + `x-api-key` | token ~1 saat → **401 INVALID_ACCESS_TOKEN** | https://developer.amazon.com/docs/music/API_web_LWA.html | 2026-10-17 |
| 11 | Last.fm | `api_key` + `api_sig=md5(...)`; oturum `sk` | oturum anahtarı | imza sırası alfabetik; `format=json` | https://www.last.fm/api/authspec | 2026-10-17 |
| 12 | Musixmatch | API key | API key | ücretsiz: günde **2.000** + sözlerin **%30'u**; limit → **402** | https://developer.musixmatch.com/plans | 2026-10-17 |

## 3. Spotify — auth akışı detayı

**Karar:** KAPSAM İÇİ · akışlar: authorization code · authorization code + PKCE · client credentials
(https://developer.spotify.com/documentation/web-api/concepts/authorization — erişim: 2026-10-17).

| # | İddia | Doğrulanmış değer | URL | Erişim |
|---|---|---|---|---|
| 1 | PKCE verifier uzunluğu | **43–128 karakter** | https://developer.spotify.com/documentation/web-api/tutorials/code-pkce-flow | 2026-10-17 |
| 2 | Challenge yöntemi | SHA-256 → **base64url** · `code_challenge_method=S256` | aynı doküman | 2026-10-17 |
| 3 | `/authorize` parametreleri | `client_id`, `response_type=code`, `redirect_uri`, `scope`, `state` (önerilir), `code_challenge`, `code_challenge_method` | aynı doküman | 2026-10-17 |
| 4 | Token ucu | `POST https://accounts.spotify.com/api/token` | https://developer.spotify.com/documentation/web-api/tutorials/code-pkce-flow | 2026-10-17 |
| 5 | Token gövdesi tipi | `application/x-www-form-urlencoded` | aynı doküman | 2026-10-17 |
| 6 | Exchange alanları | `grant_type=authorization_code` + `code` + `redirect_uri` + `client_id` + `code_verifier` | aynı doküman | 2026-10-17 |
| 7 | Yanıt alanları | `access_token`, `token_type=Bearer`, `scope`, `expires_in`, `refresh_token` | aynı doküman | 2026-10-17 |
| 8 | Refresh kuralı | yenilemede **yeni refresh_token gelmeyebilir → eskisi korunur** | aynı doküman | 2026-10-17 |
| 9 | Rate limit | yuvarlanan **30 sn** pencere; aşım → **429** + `Retry-After` | https://developer.spotify.com/documentation/web-api/concepts/rate-limits | 2026-10-17 |
| 10 | Kota modları | development / extended; kota aşımı **429 QUOTA_EXCEEDED** | https://developer.spotify.com/documentation/web-api/concepts/quota-modes | 2026-10-17 |
| 11 | Arama ucu | `GET https://api.spotify.com/v1/search` | https://developer.spotify.com/documentation/web-api/reference/search | 2026-10-17 |

**Kritik kurallar (CoreMusic karşılığı):** `state` + PKCE doğrulaması atlanmaz (ADR-010) · 429'da
`Retry-After` bekleme zorunlu · refresh dönüşünde eski token silinmez (`TokenStore.markRefreshed`).

## 4. Apple Music — auth akışı detayı

**Karar:** KAPSAM İÇİ · developer token (sunucuda imzalanan JWT).

| # | İddia | Doğrulanmış değer | URL | Erişim |
|---|---|---|---|---|
| 1 | Token tipi | JWT · başlık `alg: ES256` + `kid` (**10 karakter**) | https://developer.apple.com/documentation/applemusicapi/generating-developer-tokens | 2026-10-17 |
| 2 | `iss` | **10 karakter** Team ID | aynı doküman | 2026-10-17 |
| 3 | `exp` sınırı | şu andan en fazla **15777000 saniye / 6 ay** | aynı doküman | 2026-10-17 |
| 4 | `origin` | web için **önerilir** | aynı doküman | 2026-10-17 |
| 5 | Algoritma zorunluluğu | **yalnızca ES256** kabul edilir; aksi **401** | aynı doküman | 2026-10-17 |
| 6 | Taşıma | tüm isteklerde `Authorization: Bearer` | aynı doküman | 2026-10-17 |
| 7 | Katalog kökü | `https://api.music.apple.com/v1` · arama `GET /v1/catalog/{storefront}/search` | https://developer.apple.com/documentation/applemusicapi/handling_requests_and_responses | 2026-10-17 |
| 8 | Rate limit | aşım → geçici **429** | aynı doküman | 2026-10-17 |
| 9 | Kullanıcı token'ı | media user token **ayrı** akıştır | https://developer.apple.com/documentation/applemusicapi/user-authentication-for-musickit | 2026-10-17 |
| 10 | JS yapılandırması | `MusicKit.configure({developerToken})` | https://js-cdn.music.apple.com/musickit/v1/index.html | 2026-10-17 |

**Kritik kurallar (CoreMusic karşılığı):** imzalama `JwtSignerEs256` ile yalnız sunucuda yapılır ·
developer token ile kullanıcı token'ı karıştırılmaz (ADR-095 ayrımı) · 6 ay üst sınırı TTL politikasını kilitler.

## 5. YouTube Music — auth akışı detayı

**Karar:** KAPALI (tur-1 kaydı korunur: **resmi public API yok**).

| # | İddia | Doğrulanmış değer | URL | Erişim |
|---|---|---|---|---|
| 1 | Resmi public API | **YOK** (tur-1 kaydı, ADR-097 §1 madde 6 (satır 33)) | Dosya: `index.md` [Satır: 35] | 2026-10-12 |
| 2 | İlgili resmi kota | YouTube Data API v3 `search.list`: günlük **100 çağrı** kovası | https://developers.google.com/youtube/v3/determine_quota_cost | 2026-10-17 |
| 3 | Toplam birim | varsayılan **10.000 birim/gün** | aynı doküman | 2026-10-17 |
| 4 | `search.list` referansı | resmi list sayfası | https://developers.google.com/youtube/v3/docs/search/list | 2026-10-17 |
| 5 | Hizmet şartları | API Services ToS erişildi | https://developers.google.com/youtube/terms/api-services-terms-of-service | 2026-10-17 |
| 6 | Resmi olmayan istemciler | ytmusicapi / yt-dlp / InnerTube cookie türü istemciler **reklam atlatma** nedeniyle ToS'a aykırı | Dosya: `.ai/architecture/K020-discovery/agent/agent-kapsam.md` [§9/1] · kaynak işareti: https://github.com/ronanbradley/vscode-youtube-music (resmi olmayan; sayfa kendini "unofficial" diye tanımlar — erişim: 2026-10-17) | 2026-10-17 |

**Kritik kural:** resmi olmayan istemci **kullanılmaz**; kapı kapalıdır (kapsam §6/4). Bu satırlar
"nereden biliyoruz" kaydıdır — kullanım izni **değildir**.

## 6. Tidal — auth akışı detayı

**Karar:** KAPSAM İÇİ · **OAuth 2.1** · üç akış: client credentials · authorization code · refresh token.

| # | İddia | Doğrulanmış değer | URL | Erişim |
|---|---|---|---|---|
| 1 | Protokol | OAuth **2.1** | https://developer.tidal.com/documentation/api-sdk/api-sdk-authorization | 2026-10-17 |
| 2 | Akışlar | client credentials · authorization code · refresh token | aynı doküman | 2026-10-17 |
| 3 | PKCE zorunluluğu | authorization code akışında **PKCE tüm istemciler için zorunlu** | aynı doküman | 2026-10-17 |
| 4 | Authorize ucu | `https://login.tidal.com/authorize` (`response_type=code`, `code_challenge_method=S256`) | aynı doküman | 2026-10-17 |
| 5 | Token ucu | `https://auth.tidal.com/v1/oauth2/token` | aynı doküman | 2026-10-17 |
| 6 | Exchange alanları | `grant_type=authorization_code` + `code_verifier` | aynı doküman | 2026-10-17 |
| 7 | client_credentials kimliği | **Basic auth** `client_id:secret` | aynı doküman | 2026-10-17 |
| 8 | Taşıma | `Authorization: Bearer` · JSON:API gövdesi | aynı doküman | 2026-10-17 |

**Kritik kurallar (CoreMusic karşılığı):** client secret `SecretVault`'tadır · PKCE doğrulaması
atlanmaz (ADR-010) · JSON:API gövdesi K011/API sözleşmesine bağlanır · resmi kota rakamı yok → ⚠️.

## 7. Deezer — auth akışı detayı

**Karar:** KAPSAM İÇİ (kimlik ayrımı ile) · OAuth + **kimliksiz katalog**.

| # | İddia | Doğrulanmış değer | URL | Erişim |
|---|---|---|---|---|
| 1 | OAuth ucu | `https://connect.deezer.com/oauth/auth.php?app_id=…&redirect_uri=…&perms=basic_access,email` | https://support.deezer.com/hc/en-gb/articles/360011538897 | 2026-10-17 |
| 2 | Katalog kimliği | katalog uçları **kimlik doğrulamasız** çalışır | https://developers.deezer.com/api | 2026-10-17 |
| 3 | Arama ucu | `https://api.deezer.com/search?q=…` | aynı doküman | 2026-10-17 |
| 4 | Parametreler | `q` **zorunlu**; `order`, `limit`, `index` opsiyonel | aynı doküman | 2026-10-17 |
| 5 | Resmi kota | sayısal limit **resmi sayfalarda yayımlanmamış** (FAQ yalnız "query quota" der) | aynı doküman | 2026-10-17 |
| 6 | Üçüncü taraf iddiası | apis.io "50/5sn" rakamı **resmi olmayan kaynak** → kullanılamaz | Dosya: `.ai/architecture/K020-discovery/discovery-web-arastirma.md` (işaretli) | 2026-10-17 |

**Kritik kurallar (CoreMusic karşılığı):** katalog aramasında token saklanmaz (yalnız
RateLimitHandler + FallbackDriver) · sayısal kota **uydurulmaz** → ⚠️ VERIFICATION REQUIRED (kapı 7).

## 8. MusicBrainz — auth akışı detayı

**Karar:** KAPSAM İÇİ · kimlik yok, **oran** var.

| # | İddia | Doğrulanmış değer | URL | Erişim |
|---|---|---|---|---|
| 1 | Arayüz | `/ws/2` REST | https://musicbrainz.org/doc/MusicBrainz_API/Rate_Limiting | 2026-10-17 |
| 2 | Oran | **IP başına saniyede 1 istek** | aynı doküman | 2026-10-17 |
| 3 | Aşım cezası | talebin **%100'ü reddedilir** → **HTTP 503**; oran 1/sn'ye inene kadar | aynı doküman | 2026-10-17 |
| 4 | User-Agent | **zorunlu**; iletişim bilgisi içermeli | aynı doküman | 2026-10-17 |
| 5 | Arama sözdizimi | **Lucene** | https://musicbrainz.org/doc/MusicBrainz_API/Search | 2026-10-17 |
| 6 | `limit` | **1–100** (varsayılan **25**) · `fmt=json` | aynı doküman | 2026-10-17 |

**Kritik kurallar (CoreMusic karşılığı):** yerel kuyruk + 1/sn sabitleyici zorunlu · User-Agent
şablonunda iletişim bilgisi bulunur · 503, "servis bozuk" değil "oran aşımı" olarak ayrıştırılır.

## 9. ListenBrainz — auth akışı detayı

**Karar:** KOŞULLU (yazma/kişisel veri) · katalog araması **yok**.

| # | İddia | Doğrulanmış değer | URL | Erişim |
|---|---|---|---|---|
| 1 | Kök | `https://api.listenbrainz.org` — **yalnız HTTPS** | https://listenbrainz.readthedocs.io/en/latest/users/api/index.html | 2026-10-17 |
| 2 | Kimlik | özel **user token** (`Authorization` başlığı) | aynı doküman | 2026-10-17 |
| 3 | Rate limit | yanıt **başlıklarıyla** bildirilir | aynı doküman | 2026-10-17 |
| 4 | User-Agent | **zorunlu** | aynı doküman | 2026-10-17 |
| 5 | Uçlar | `POST /1/submit-listens` · `GET /1/validate-token` · `GET /1/search/users/` · `GET /1/user/{name}/listens` | aynı doküman | 2026-10-17 |
| 6 | Sınır | **katalog parçası araması yok** (dinleme/çalma listesi servisi) | aynı doküman | 2026-10-17 |

**Kritik kurallar (CoreMusic karşılığı):** katalog keşfi bu servisle **yapılmaz** (kapsam §5/4) ·
user token `TokenStore`'ta şifreli · `GET /1/validate-token` ile açılışta sınanır.

## 10. Genius — auth akışı detayı

**Karar:** KOŞULLU — **lisans kapısı** açık.

| # | İddia | Doğrulanmış değer | URL | Erişim |
|---|---|---|---|---|
| 1 | OAuth2 authorize | `https://api.genius.com/oauth/authorize` | https://docs.genius.com/ | 2026-10-17 |
| 2 | OAuth2 token | `https://api.genius.com/oauth/token` | aynı doküman | 2026-10-17 |
| 3 | Scope'lar | `me` · `create_annotation` · `manage_annotation` · `vote` | aynı doküman | 2026-10-17 |
| 4 | Okuma kimliği | kullanıcı gerektirmeyen okuma uçları için **client access token** yeterli | aynı doküman | 2026-10-17 |
| 5 | Arama | `GET /search?q=` · `Authorization: Bearer` | aynı doküman | 2026-10-17 |
| 6 | **KAPI** | "Commercial use of the Genius API is not allowed without a license" (api-sales@genius.com) | https://docs.genius.com/ | 2026-10-17 |

**Kritik kural:** telifli söz metni üretimi **lisans ister**; lisans gelmeden bu uçlar yalnız
keşif/inceleme amaçlıdır (kapsam §6/5).

## 11. Discogs — auth akışı detayı

**Karar:** KAPSAM İÇİ · iki yöntem: OAuth 1.0a · personal access token.

| # | İddia | Doğrulanmış değer | URL | Erişim |
|---|---|---|---|---|
| 1 | OAuth 1.0a adım 1 | `https://api.discogs.com/oauth/request_token` | https://www.discogs.com/developers | 2026-10-17 |
| 2 | OAuth 1.0a adım 2 | `https://www.discogs.com/oauth/authorize` | aynı doküman | 2026-10-17 |
| 3 | OAuth 1.0a adım 3 | `https://api.discogs.com/oauth/access_token` | aynı doküman | 2026-10-17 |
| 4 | PAT | developer settings'ten üretilir; **süresi dolmaz** | aynı doküman | 2026-10-17 |
| 5 | Kota kimliği | anonim **düşük** · consumer key/secret veya token → **yüksek kota** + image URL | aynı doküman | 2026-10-17 |
| 6 | User-Agent | **zorunlu** (örnek biçim `my_user_agent/1.0`) | aynı doküman | 2026-10-17 |

**Kritik kurallar (CoreMusic karşılığı):** OAuth 1.0a ayrı sürücü ister (kapsam §4.8) · PAT
`SecretVault`'ta · kota kimliğe bağlıdır → anonim çağrı **kullanılmaz**.

## 12. Amazon Music — auth akışı detayı

**Karar:** KAPALI BETA (erişim onaylı geliştiricilere).

| # | İddia | Doğrulanmış değer | URL | Erişim |
|---|---|---|---|---|
| 1 | Erişim | **kapalı Beta** — "contact your Amazon Music Business Development representative" | https://developer.amazon.com/docs/music/API_web_LWA.html | 2026-10-17 |
| 2 | Protokol | LWA (Login With Amazon) = **OAuth 2.0** | aynı doküman | 2026-10-17 |
| 3 | Başlıklar | her istekte **iki** başlık: `Authorization: Bearer …` + `x-api-key: <Security Profile ID (amzn1.application.…)>` | aynı doküman | 2026-10-17 |
| 4 | Token ömrü | yaklaşık **1 saat** (`expires_in`) | aynı doküman | 2026-10-17 |
| 5 | Süre dolması | **401** `INVALID_ACCESS_TOKEN` | aynı doküman | 2026-10-17 |
| 6 | Scope örnekleri | `music::catalog`, `music::playback` (salt-okunur `:read` eki) | aynı doküman | 2026-10-17 |

**Kritik kurallar (CoreMusic karşılığı):** erişim başvurusu yapılmadan kapsam dışı (kapı 6) ·
~1 saatlik token RefreshScheduler'ı en sık döndüren servistir · iki başlık tek "istek zarfında"
taşınır (tek başlık eksik = hata).

## 13. Last.fm — auth akışı detayı

**Karar:** KAPSAM İÇİ · imza + oturum anahtarı.

| # | İddia | Doğrulanmış değer | URL | Erişim |
|---|---|---|---|---|
| 1 | Kimlik | `api_key` + gizli `secret` | https://www.last.fm/api/authspec | 2026-10-17 |
| 2 | İmza | `api_sig = md5(sıralanmış parametreler + secret)` — `format`/`callback` hariç, alfabetik | aynı doküman | 2026-10-17 |
| 3 | Web akışı | `last.fm/api/auth` → `auth.getSession` | aynı doküman | 2026-10-17 |
| 4 | Masaüstü akışı | `auth.getToken` → tarayıcı → `auth.getSession` | aynı doküman | 2026-10-17 |
| 5 | Oturum anahtarı | `sk` | aynı doküman | 2026-10-17 |
| 6 | Yazma uçları | kimlik (`sk`) ister · JSON için `format=json` | aynı doküman | 2026-10-17 |

**Kritik kurallar (CoreMusic karşılığı):** md5 **yalnız** servisin imza kuralıdır (CoreMusic
iç şifrelemesi AES-256-GCM/Argon2id'dir — md5 başka hiçbir yerde kullanılmaz) · `secret`
`SecretVault`'ta · oturum `sk` TokenStore'da şifreli.

## 14. Musixmatch — auth akışı detayı

**Karar:** KAPSAM İÇİ (API key) · **çift limit**.

| # | İddia | Doğrulanmış değer | URL | Erişim |
|---|---|---|---|---|
| 1 | Kimlik | API key | https://developer.musixmatch.com/plans | 2026-10-17 |
| 2 | Ücretsiz plan | **günde 2.000 çağrı** + şarkı sözlerinin **%30'u** | aynı doküman | 2026-10-17 |
| 3 | Limit aşımı | **HTTP 402** · gece yarısı **UTC** sıfırlanır | https://developer.musixmatch.com/documentation/status-codes | 2026-10-17 |
| 4 | Kalan hak | `X-RateLimit-Remaining` başlığı | aynı doküman | 2026-10-17 |
| 5 | Fiyatlandırma sayfası | plan/limit referansı | https://about.musixmatch.com/api-pricing | 2026-10-17 |
| 6 | Çifte limit | toplam çağrı + lyrics çağrıları **ayrı** sayılır | https://developer.musixmatch.com/plans | 2026-10-17 |

**Kritik kurallar (CoreMusic karşılığı):** 402, 429 ile **karıştırılmaz** — backoff yerine
bekleme planı (gece yarısı UTC sıfırlanır) · lyrics sayacı ayrı izlenir · API kök URL'si bu turda
doğrulanmadı → ⚠️ VERIFICATION REQUIRED.

## 15. Kanıt sınıflandırması

| # | Sınıf | Tanım | Düşen maddeler |
|---|---|---|---|
| 1 | **Doğrulandı** | resmi dokümanda birebir okundu; URL + 2026-10-17 | §3–§14 tablolarındaki satırlar |
| 2 | **Kısmi** | resmi değer var, eksik alan var | Tidal kota rakamı (değer yok) · Musixmatch API kökü |
| 3 | **⚠️ VERIFICATION REQUIRED** | resmi sayfada yayımlanmamış | Deezer sayısal kota · Rust/C/C++ paket sürümleri (kod dosyası) |
| 4 | **Üçüncü taraf (kullanılamaz)** | resmi olmayan kaynak | apis.io "Deezer 50/5sn" · vscode-youtube-music (yalnız "unofficial" kanıtı olarak) |
| 5 | **Tur-1 kaydı (korunur)** | 2026-10-12 ADR-097 §1 madde 6 (satır 33) | YouTube Music resmi API yok · MusicKit Worker imzası (iç karar) |

## 16. Erişim defteri (2026-10-17 · tur-2 · auth odaklı)

| # | URL | Kullanım | Erişim |
|---|---|---|---|
| 1 | https://developer.spotify.com/documentation/web-api/concepts/authorization | akış listesi | 2026-10-17 |
| 2 | https://developer.spotify.com/documentation/web-api/tutorials/code-pkce-flow | PKCE + token | 2026-10-17 |
| 3 | https://developer.spotify.com/documentation/web-api/concepts/rate-limits | 429 + Retry-After | 2026-10-17 |
| 4 | https://developer.spotify.com/documentation/web-api/concepts/quota-modes | QUOTA_EXCEEDED | 2026-10-17 |
| 5 | https://developer.spotify.com/documentation/web-api/reference/search | arama ucu | 2026-10-17 |
| 6 | https://developer.apple.com/documentation/applemusicapi/generating-developer-tokens | JWT ES256 | 2026-10-17 |
| 7 | https://developer.apple.com/documentation/applemusicapi/handling_requests_and_responses | 429 + katalog kökü | 2026-10-17 |
| 8 | https://developer.apple.com/documentation/applemusicapi/user-authentication-for-musickit | kullanıcı token'ı | 2026-10-17 |
| 9 | https://js-cdn.music.apple.com/musickit/v1/index.html | MusicKit.configure | 2026-10-17 |
| 10 | https://developers.google.com/youtube/v3/determine_quota_cost | 100 kova / 10.000 birim | 2026-10-17 |
| 11 | https://developers.google.com/youtube/v3/docs/search/list | search.list | 2026-10-17 |
| 12 | https://developers.google.com/youtube/terms/api-services-terms-of-service | ToS | 2026-10-17 |
| 13 | https://github.com/ronanbradley/vscode-youtube-music | "unofficial" kanıtı (üçüncü taraf) | 2026-10-17 |
| 14 | https://developer.tidal.com/documentation/api-sdk/api-sdk-authorization | OAuth 2.1 + PKCE zorunlu | 2026-10-17 |
| 15 | https://support.deezer.com/hc/en-gb/articles/360011538897 | OAuth perms | 2026-10-17 |
| 16 | https://developers.deezer.com/api | kimliksiz katalog + `q` | 2026-10-17 |
| 17 | https://musicbrainz.org/doc/MusicBrainz_API/Rate_Limiting | 1/sn → 503 | 2026-10-17 |
| 18 | https://musicbrainz.org/doc/MusicBrainz_API/Search | Lucene + limit | 2026-10-17 |
| 19 | https://listenbrainz.readthedocs.io/en/latest/users/api/index.html | user token + uçlar | 2026-10-17 |
| 20 | https://docs.genius.com/ | OAuth + lisans kapısı | 2026-10-17 |
| 21 | https://www.discogs.com/developers | OAuth 1.0a + PAT + UA | 2026-10-17 |
| 22 | https://developer.amazon.com/docs/music/API_web_LWA.html | LWA + iki başlık + 401 | 2026-10-17 |
| 23 | https://www.last.fm/api/authspec | api_sig + sk | 2026-10-17 |
| 24 | https://developer.musixmatch.com/plans | 2.000/gün + %30 | 2026-10-17 |
| 25 | https://developer.musixmatch.com/documentation/status-codes | 402 | 2026-10-17 |
| 26 | https://about.musixmatch.com/api-pricing | fiyatlandırma/limit | 2026-10-17 |

**Defter kuralı:** erişim sütunu yalnız **tarihi** taşır; HTTP durum kodu **uydurulmaz** (bu turun
kanıtı, doküman içeriğinin okunmuş olmasıdır).

## 17. Açık sorular

1. Deezer resmi sayısal kota rakamı nereden doğrulanacak? (resmi sayfa yok → ⚠️)
2. Musixmatch API kök URL'si resmi dokümanda hangi sayfada? (bu turda yalnız plan/status-codes okundu)
3. Tidal API katalog host'u (token uçları dışında) doğrulandı mı? → hayır → ⚠️
4. Amazon Music Beta başvurusu yapılacak mı? (kapı 6)
5. Genius lisansı alınacak mı? (kapı 5)
6. Last.fm API çağrı host'u (authspec dışındaki uçlar) doğrulanmadı → ⚠️
7. MusicBrainz `/ws/2` kökünün tam host+path yazımı dokümanda birebir mi? (doküman "/ws/2" der)
8. YouTube Music kapalı kapı, hangi yedek servisle telafi edilecek? (FallbackDriver zinciri kararı)

## 18. Uç noktası sözlüğü (authorize · token · API)

| # | Servis | Authorize ucu | Token/identity ucu | API kökü | Not |
|---|---|---|---|---|---|
| 1 | Spotify | `/authorize` (host ⚠️) | `https://accounts.spotify.com/api/token` | `https://api.spotify.com/v1` (arama `/v1/search`) | yalnız token + arama ucu tam URL doğrulandı |
| 2 | Apple Music | — (geliştirici hesabı) | sunucuda imzalanır (JWT) | `https://api.music.apple.com/v1` | kullanıcı akışı ayrı |
| 3 | YouTube Music | — | — | — | resmi API yok (KAPALI) |
| 4 | Tidal | `https://login.tidal.com/authorize` | `https://auth.tidal.com/v1/oauth2/token` | host ⚠️ VERIFICATION REQUIRED | JSON:API |
| 5 | Deezer | `https://connect.deezer.com/oauth/auth.php` | OAuth ucu ⚠️ (sayfada auth.php var) | `https://api.deezer.com` | katalog kimliksiz |
| 6 | MusicBrainz | — | — | `/ws/2` (tam kök ⚠️) | kimlik yok |
| 7 | ListenBrainz | — | `GET /1/validate-token` | `https://api.listenbrainz.org` | user token |
| 8 | Genius | `https://api.genius.com/oauth/authorize` | `https://api.genius.com/oauth/token` | `GET /search?q=` (api.genius.com) | okumada client token |
| 9 | Discogs | `https://www.discogs.com/oauth/authorize` | `https://api.discogs.com/oauth/request_token` · `/access_token` | host ⚠️ (OAuth uçlarıyla aynı alan adı) | OAuth 1.0a / PAT |
| 10 | Amazon Music | LWA ekranı (uç ⚠️) | LWA (OAuth 2.0) | host ⚠️ | kapalı Beta |
| 11 | Last.fm | `last.fm/api/auth` | `auth.getToken` · `auth.getSession` | host ⚠️ | imza `api_sig` |
| 12 | Musixmatch | — | — | host ⚠️ | yalnız plan/status-codes sayfaları okundu |

**Sözlük kuralı:** `⚠️` işareti, **tam URL'nin bu turda doğrulanmadığı** anlamına gelir; kod dosyasında
böyle yerler `YOUR_AUTHORIZE_ENDPOINT` placeholder'ıdır (`agent-ornekler.md` §1/3).

## 19. Parametre sözlüğü (authorize ve token alanları)

| # | Parametre | Zorunlu mu | Kullanan servis(ler) | Doğrulanmış not |
|---|---|---|---|---|
| 1 | `client_id` | evet (code akışı) | Spotify · Tidal · Deezer (`app_id`) | Spotify exchange'de de gönderilir |
| 2 | `response_type=code` | evet | Spotify · Tidal · Deezer · Genius | Tidal authorize ucu |
| 3 | `redirect_uri` | evet | Spotify · Tidal · Deezer · Genius | Spotify: exchange'de **aynı** değer tekrar gönderilir |
| 4 | `scope` | servise göre | Spotify · Genius (`me` …) | Genius scope'ları dokümanda listeli |
| 5 | `state` | **önerilir** (CoreMusic'te zorunlu) | Spotify | ADR-010: bu ağaçta atlanmaz |
| 6 | `code_challenge` | PKCE'de evet | Spotify · Tidal | base64url(SHA-256(verifier)) |
| 7 | `code_challenge_method` | PKCE'de evet | Spotify · Tidal | değer: **`S256`** |
| 8 | `grant_type=authorization_code` | token isteğinde evet | Spotify · Tidal | form alanı |
| 9 | `code` | token isteğinde evet | Spotify · Tidal | tek kullanımlık |
| 10 | `code_verifier` | PKCE'de evet | Spotify · Tidal | 43–128 karakter (Spotify) |
| 11 | `perms` | Deezer'de | Deezer | `basic_access,email` (doküman örneği) |
| 12 | `q` | Deezer aramada | Deezer | **zorunlu** |
| 13 | `order`, `limit`, `index` | opsiyonel | Deezer | arama sıralama/sayfalama |
| 14 | `limit` | opsiyonel | MusicBrainz | **1–100**, varsayılan **25** |
| 15 | `fmt=json` | opsiyonel | MusicBrainz | JSON yanıtlar için |
| 16 | `format=json` | opsiyonel | Last.fm | `api_sig` hesabı dışında |
| 17 | `callback` | opsiyonel | Last.fm | `api_sig` hesabına **dahil edilmez** |

## 20. Başlık sözlüğü (HTTP headers)

| # | Başlık | Değer / biçim | Kullanan | Not |
|---|---|---|---|---|
| 1 | `Authorization: Bearer` | `Bearer <TOKEN>` | Spotify · Apple · Tidal · Genius · Amazon | Apple'da developer token, kullanıcı token'ı ayrı |
| 2 | `x-api-key` | `<Security Profile ID (amzn1.application.…)>` | Amazon Music | **ikinci** başlık — tek başına yeterli değil |
| 3 | `Content-Type` | `application/x-www-form-urlencoded` | Spotify · Tidal (token) | token gövdesi formdır (JSON değil) |
| 4 | `User-Agent` | iletişim bilgisi içeren değer | MusicBrainz · Discogs · ListenBrainz | zorunlu; yoksa ret/kota düşüşü |
| 5 | `Retry-After` | saniye | Spotify (429) · Apple (geçici 429) | bekleme süresinin kaynağı |
| 6 | `X-RateLimit-Remaining` | sayı | Musixmatch | kalan hak bildirimi |
| 7 | Basic auth | `base64(client_id:secret)` | Tidal (client_credentials) | secret `SecretVault`'ta |

## 21. "Değer yok" kayıtları (bu turda doğrulanmayanlar)

| # | Soru | Durum | Davranış |
|---|---|---|---|
| 1 | Deezer günlük/saniyelik kota sayısı | resmi sayfada yok | ⚠️ VERIFICATION REQUIRED; muhafazakâr istemci |
| 2 | Tidal kota/limit rakamı | dokümanda okunmadı | ⚠️; 429 gelirse genel bekleme kuralı |
| 3 | Musixmatch API kök host'u | bu turda okunmadı | ⚠️; kod dosyasında URL yok |
| 4 | Last.fm API çağrı host'u | authspec dışındaki uçlar doğrulanmadı | ⚠️ |
| 5 | Amazon Music API kök host'u | Beta kapalı; okunmadı | ⚠️ |
| 6 | Apple `/v1/catalog/{storefront}/search` store değerleri | listesi okunmadı | ⚠️; `storefront` parametresi çağrı öncesi çözülür |
| 7 | Deezer OAuth token ucu (code → token) | yalnız auth.php okundu | ⚠️; OAuth akışı kodu yalnız keşif seviyesinde |
| 8 | Rust/C/C++ paket sürümleri | doğrulanmadı | ⚠️ (kod dosyası §18/2) |

## 22. SSS

| # | Soru | Cevap |
|---|---|---|
| 1 | Bu dosya üst düzey `discovery-web-arastirma.md`'yi kopyalar mı? | Hayır — üst dosya ham tarama (kota + keşif değeri); bu dosya yalnız **auth akışı detayı** |
| 2 | Erişim sütununda HTTP durumu neden yok? | Uydurma durum yazılmaz; kanıt, dokümanın okunmuş olmasıdır (§16 kuralı) |
| 3 | "⚠️" olan satır kullanılabilir mi? | Tasarım/sınır kararında **hayır**; yalnız açık soru olarak taşınır |
| 4 | Üçüncü taraf kaynak (apis.io, GitHub) kanıt sayılır mı? | Sayılmaz; yalnız işaretleme (`§15/4`) |
| 5 | md5'li Last.fm imzası güvenlik açığı mı? | Servisin zorunlu imza kuralıdır; CoreMusic iç şifrelemesi md5 **kullanmaz** |
| 6 | Hangi servisler `User-Agent` zorunlu tutuyor? | MusicBrainz · Discogs · ListenBrainz (§20/4) |
| 7 | 429 mu 402 mi hangi servis? | 429: Spotify/Apple · 402: Musixmatch (§3/9 · §14/3) |
| 8 | Tur-2 neyi değiştirdi? | 3 servislik tur-1 keşfi → 12 servislik auth matrisi (§2) |

## 23. Tur-2 ile tur-1 farkı

| # | Konu | Tur-1 (2026-10-12) | Tur-2 (2026-10-17) |
|---|---|---|---|
| 1 | Servis sayısı | 3 (Spotify · Apple Music · YouTube Music) | 12 (§2) |
| 2 | Spotify akış detayı | "OAuth2+PKCE" tek cümle | verifier 43–128 · `S256` · token form alanları · refresh kuralı |
| 3 | Apple token detayı | "JWT · ES256 + kid — Worker'da imzalanır" | `iss`/`iat`/`exp` ≤ 15777000 sn · `origin` · 401 · kullanıcı token'ı ayrı |
| 4 | Rate limit | "429 + Retry-After" | yuvarlanan 30 sn pencere · QUOTA_EXCEEDED kota modu · 402 · 503 · 1/sn |
| 5 | YouTube Music | "resmi public API yok → KAPALI" | ToS erişimi + ilgili kota (100 kova / 10.000 birim) + "unofficial" kanıtı |
| 6 | Yeni servisler | — | Tidal · Deezer · MusicBrainz · ListenBrainz · Genius · Discogs · Amazon Music · Last.fm · Musixmatch |

## 24. Servis × kimlik ihtiyacı matrisi

| # | Servis | Katalog okuma | Kullanıcı verisi | Yazma (submit) | Token saklama gerekli mi |
|---|---|---|---|---|---|
| 1 | Spotify | `Bearer` | `Bearer` (kullanıcı scope'u) | hayır | evet |
| 2 | Apple Music | developer JWT | ayrı user token | hayır | evet (iki ayrı!) |
| 3 | YouTube Music | — | — | — | hayır (KAPALI) |
| 4 | Tidal | `Bearer` | `Bearer` | hayır | evet |
| 5 | Deezer | **kimliksiz** | OAuth | hayır | katalog için hayır |
| 6 | MusicBrainz | **kimliksiz** | — | hayır | hayır |
| 7 | ListenBrainz | (dinleme verisi) | user token | **evet** (`submit-listens`) | evet |
| 8 | Genius | client access token | OAuth | annotation yazma (scope) | evet (lisans kapısı) |
| 9 | Discogs | PAT/OAuth | PAT/OAuth | hayır | evet |
| 10 | Amazon Music | `Bearer` + `x-api-key` | — | hayır | evet (Beta kapalı) |
| 11 | Last.fm | `api_key` + imza | `sk` | **evet** (scrobble vb.) | evet |
| 12 | Musixmatch | API key | — | hayır | evet (key `.env`'de) |

## 25. Kritik kural → bileşen eşlemesi

| # | Doğrulanmış kural | Servis | Bileşen karşılığı |
|---|---|---|---|
| 1 | verifier 43–128 · `S256` | Spotify | `PkceHelper` |
| 2 | PKCE tüm istemciler için zorunlu | Tidal | `PkceHelper` |
| 3 | refresh gelmeyebilir → eskisi korunur | Spotify | `TokenStore.markRefreshed` |
| 4 | yalnız ES256 (aksi 401) | Apple Music | `JwtSignerEs256` |
| 5 | `exp` ≤ 15777000 sn | Apple Music | `JwtSignerEs256` |
| 6 | 429 + `Retry-After` | Spotify · Apple | `RateLimitHandler` |
| 7 | 402 + `X-RateLimit-Remaining` | Musixmatch | `RateLimitHandler` |
| 8 | 1/sn → %100 ret → 503 | MusicBrainz | `RateLimitHandler` + kuyruk |
| 9 | 401 INVALID_ACCESS_TOKEN | Amazon Music | `RefreshScheduler` (yeniden edinim) |
| 10 | kapalı API (yedek zorunlu) | YouTube Music | `FallbackDriver` |
| 11 | secret/anahtar taşıma | Tidal · Last.fm · Apple · Amazon | `SecretVault` |

## 26. Tur-2 kalite kapısı (kontrol listesi)

1. Her iddia satırında resmi URL + erişim tarihi var mı? → §3–§14 ✓
2. `⚠️ VERIFICATION REQUIRED` maddeleri sayısal değerle dolduruldu mu? → **hayır** (bilinçli, §21)
3. Üçüncü taraf iddialar kanıt olarak mı kullanıldı? → hayır, yalnız işaretleme (§15/4)
4. Tur-1 iddiaları çapraz kontrol edildi mi? → `agent-dogrulama.md` §3
5. Kod/placeholder kuralı ihlal edildi mi? → bu dosyada kod yok (§1/5)
6. Token/anahtar metni bu dosyaya mı girdi? → hayır (§1 güvenlik kanunu: yalnız placeholder)
7. Uydurma HTTP durumu/limit/sürüm var mı? → hayır; erişim sütunu yalnız tarih (§16)
8. Üst düzey dosya ile çakışma (bilgi tekrarı) var mı? → hayır (§22/1 · §23)

## 27. Değişiklik kaydı

| # | Tarih | Değişiklik | Kanıt |
|---|---|---|---|
| 1 | 2026-10-12 | Tur-1: 3 servislik auth notu (Spotify/Apple/YouTube Music) | ADR-097 §1 madde 6 (satır 33) · `index.md` [Satır: 12] |
| 2 | 2026-10-17 | Tur-2 açıldı; 12 servis × auth akışı matrisi kuruldu | bu dosya §2 |
| 3 | 2026-10-17 | Spotify PKCE/token/refresh/rate-limit detayları eklendi | §3 |
| 4 | 2026-10-17 | Apple JWT (`exp` ≤ 15777000 sn) + kullanıcı token ayrımı eklendi | §4 |
| 5 | 2026-10-17 | Tidal OAuth 2.1 + PKCE zorunluluğu eklendi | §6 |
| 6 | 2026-10-17 | Kimliksiz katalog (Deezer) + oran (MusicBrainz) kayıtları eklendi | §7 · §8 |
| 7 | 2026-10-17 | Genius lisans kapısı + Amazon Beta kapısı işaretlendi | §10 · §12 |
| 8 | 2026-10-17 | Erişim defteri 26 URL'ye çıkarıldı | §16 |
| 9 | 2026-10-17 | "Değer yok" kayıtları (§21) bilinçli olarak ⚠️ bırakıldı | §15/3 |

## 28. Adım haritaları (kanıt adımları — tur-2 okumasından)

### 28.1 Authorization code + PKCE (Spotify · Tidal)

1. `code_verifier` (43–128 karakter) + `code_challenge` (base64url SHA-256) + `state` üretilir → kanıt: kod-pkce-flow, 2026-10-17.
2. Authorize ucu çağrılır: `client_id`, `response_type=code`, `redirect_uri`, `scope`, `state`, `code_challenge`, `code_challenge_method=S256` → kanıt: kod-pkce-flow, 2026-10-17.
3. Geri dönüşte `state` karşılaştırılır (CoreMusic kuralı — ADR-010) → kanıt: `index.md` [Satır: 42].
4. Token ucu `POST https://accounts.spotify.com/api/token` çağrılır (`application/x-www-form-urlencoded`) → kanıt: kod-pkce-flow, 2026-10-17.
5. Form alanları: `grant_type=authorization_code`, `code`, `redirect_uri`, `client_id`, `code_verifier` → kanıt: aynı doküman, 2026-10-17.
6. Yanıt ayrıştırılır: `access_token`, `token_type=Bearer`, `scope`, `expires_in`, `refresh_token` → kanıt: aynı doküman, 2026-10-17.
7. Yenilemede yeni `refresh_token` **gelmeyebilir** → eskisi korunur → kanıt: aynı doküman, 2026-10-17.
8. Tidal varyantı: authorize `https://login.tidal.com/authorize`, token `https://auth.tidal.com/v1/oauth2/token`, PKCE tüm istemciler için zorunlu → kanıt: Tidal docs, 2026-10-17.

### 28.2 Developer JWT (Apple Music)

1. Başlık kurulur: `alg: ES256` + `kid` (10 karakter) → kanıt: generating-developer-tokens, 2026-10-17.
2. Claim'ler yazılır: `iss` (10 karakter Team ID), `iat`, `exp` (≤ 15777000 sn), `origin` (web için önerilir) → kanıt: aynı doküman, 2026-10-17.
3. İstekler `Authorization: Bearer <JWT>` ile gider → kanıt: aynı doküman, 2026-10-17.
4. Yanlış algoritma → **401** (yalnız ES256 kabul) → kanıt: aynı doküman, 2026-10-17.
5. Kullanıcı verisi gerekiyorsa media user token **ayrı** akışla alınır → kanıt: user-authentication-for-musickit, 2026-10-17.

### 28.3 Anahtarlı / kimliksiz çağrılar (Deezer · MusicBrainz · Musixmatch)

1. Deezer katalog: `https://api.deezer.com/search?q=…` kimliksiz çağrılır (`q` zorunlu) → kanıt: developers.deezer.com/api, 2026-10-17.
2. MusicBrainz: `/ws/2` çağrısında `User-Agent` + 1/sn oran korunur; `limit` 1–100, `fmt=json` → kanıt: Rate_Limiting + Search, 2026-10-17.
3. Oran aşılırsa %100 ret → **HTTP 503** beklenir → kanıt: Rate_Limiting, 2026-10-17.
4. Musixmatch: API key ile çağrı; `X-RateLimit-Remaining` izlenir → kanıt: status-codes, 2026-10-17.
5. Günlük hak bittiğinde **HTTP 402** → gece yarısı UTC sıfırlanır → kanıt: status-codes + plans, 2026-10-17.

**Authority:** Bayram Ali / Vault Steward
