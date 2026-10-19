---
node_id: K020-discovery/agent/agent-kapsam
node_type: subdomain
status: draft
parent_id: K020-discovery/agent
children: []
depends_on:
  - K020-discovery/agent
evidence:
  - "Dosya: .ai/architecture/K020-discovery/agent/index.md [Satır: 29-47] — tur-1 keşif notları + güvenlik kuralı"
  - "https://developer.spotify.com/documentation/web-api/concepts/authorization (erişim: 2026-10-17)"
  - "https://developer.tidal.com/documentation/api-sdk/api-sdk-authorization (erişim: 2026-10-17)"
validation:
  - "2026-10-17 — web tur-2 ile doğrulandı"
open_questions:
  - "Harici servis entegrasyonu CoreMusic kapsamına giriyor mu? (index.md açık soru — kapalı değil)"
  - "Genius 'commercial use' lisansı CoreMusic kullanım modeline uyuyor mu?"
group: mimari
---

# K020-discovery/agent — Kapsam (agent-kapsam)

**ELI15:** Bu dosya, harici müzik servisleriyle kurulacak ajan entegrasyonunun **sınırlarını** çizer:
hangi servis hangi kimlik doğrulama (auth) akışında, ne kapsam içinde, ne başka bir düğüme devredilir ve
hangi kapılar onay bekliyor. Tasarım `agent-bilesenler.md`'de, kod örnekleri `agent-ornekler.md`'de,
web kanıtları `agent-web-arastirma.md`'de, doğrulama `agent-dogrulama.md`'dedir.

## 1. Amaç, dosya dağılımı ve dayanaklar

Bu dosya **TASARIM İDDİASI** taşır; kanıt satırları `agent-web-arastirma.md` ve `agent-dogrulama.md`
dosyalarındadır. Kapsam cümlesi (tek satır): *Harici müzik servislerine kimlik kanıtlama, token saklama,
yenileme ve sınır (rate limit) uyumu; CoreMusic'in kendi oturumu bu kapsamın dışındadır.*

| # | Dosya | Taşıdığı soru | group |
|---|---|---|---|
| 1 | `agent-kapsam.md` (bu dosya) | Kapsam içi/dışı, servis × auth eşlemesi, kapılar | mimari |
| 2 | `agent-bilesenler.md` | 7 bileşenin sorumluluğu, arayüz imzası, bağımlılığı | mimari |
| 3 | `agent-ornekler.md` | 13 dil + SQL: PKCE, token exchange, 429 backoff | kod |
| 4 | `agent-web-arastirma.md` | Web tur-2 servis kanıtları (2026-10-17) | kanit |
| 5 | `agent-dogrulama.md` | İddia → kanıt matrisi, tur-1/tur-2 çapraz kontrol | kanit |
| 6 | `index.md` | Klasör künyesi, §GRUPLAR, kanıt satırı | mimari |

**Dayanaklar (bu dosyanın oturduğu kararlar):**

| # | Dayanak | Ne verir | Kanıt |
|---|---|---|---|
| 1 | ADR-097 §1 madde 6 (satır 33) (tur-1, 2026-10-12) | İlk servis keşfi: Spotify = OAuth2+PKCE · MusicKit developer token · YT Music API yok | Dosya: `.ai/architecture/K020-discovery/agent/index.md` [Satır: 12] |
| 2 | ADR-010 (CSRF ailesi) | `state` + PKCE doğrulaması atlanmaz | Dosya: `.ai/architecture/K020-discovery/agent/index.md` [Satır: 42] |
| 3 | ADR-095 (hybrid JWT) | CoreMusic oturumu ayrıdır; harici token ile karıştırılmaz | Dosya: `.ai/architecture/K020-discovery/agent/index.md` [Satır: 41] |
| 4 | ADR-101 | Her harici çağrıda timeout/retry/fallback zorunlu | Dosya: `.ai/architecture/K020-discovery/agent/index.md` [Satır: 40] |
| 5 | Kök AGENTS.md §5 Security | Token yalnız `.env`; log/MD/konsol yasak | Dosya: `.ai/architecture/K020-discovery/agent/index.md` [Satır: 39] |
| 6 | v2 §2.20 (K-node şeması) | Bu ağacın node kimliği ve 9 alanlı frontmatter'ı | Dosya: `.ai/architecture/K020-discovery/agent/index.md` [Satır: 11] |

**Güvenlik kanunu (bu dosyada de tekrar):** (1) Token/anahtar yalnız `.env` veya env substitution ile
taşınır — `.md`, `.log`, konsol çıktısına yazılmaz. (2) ADR-095 hybrid JWT CoreMusic oturumu ayrıdır.
(3) ADR-010/CSRF → `state` + PKCE doğrulaması atlanmaz. (4) ADR-101 → timeout/retry/fallback zorunludur.

## 2. Kapsam içi — ele alınan yüzeyler

| # | Yüzey | Kapsam durumu | Kanıt durumu |
|---|---|---|---|
| 1 | Auth akışı seçimi (servis × akış eşlemesi) | KAPSAM İÇİ | tur-2 ile doğrulandı |
| 2 | PKCE `code_verifier` üretimi ve `S256` challenge | KAPSAM İÇİ | Spotify + Tidal dokümanları |
| 3 | `state` üretimi ve geri dönünce doğrulanması | KAPSAM İÇİ | ADR-010 bağlayıcı |
| 4 | Token exchange (kod → erişim token'ı) | KAPSAM İÇİ | Spotify token ucu doğrulandı |
| 5 | Refresh token yenileme planı (RefreshScheduler) | KAPSAM İÇİ | Spotify: yenilemede yeni refresh gelmeyebilir |
| 6 | Şifreli token saklama (TokenStore, .env/DB) | KAPSAM İÇİ | Mimari iddia — kod kanıtı yok |
| 7 | Geliştirici JWT imzalama (Apple ES256) | KAPSAM İÇİ | Apple dokümanı: yalnız ES256, aksi 401 |
| 8 | Rate limit uyumu (429 + Retry-After bekleme) | KAPSAM İÇİ | Spotify + Apple dokümanları |
| 9 | Servis dışı kalma/tekil sürücü (FallbackDriver) | KAPSAM İÇİ | ADR-101 zorunluluğu |
| 10 | Katalog arama uçlarının çağrılması | KAPSAM İÇİ | servis başına doğrulandı (bkz. §3) |
| 11 | Kullanıcı verisi yazma (dinleme gönderimi vb.) | KOŞULLU | yalnız açık uçlu servislerde (ListenBrainz) |
| 12 | CoreMusic oturumu, rol/izin modeli, UI | KAPSAM DIŞI | ADR-095 ayrı alan |

**Kapsam durumu notu:** 1–10 numaralı yüzeyler keşif + tasarım seviyesindedir; **üretim kodu kapıdan
sonra** yazılır (bkz. §6). 11 numara servis izni vermeden kapsam dışıdır.

## 3. Servis × auth akışı eşlemesi (asıl tablo)

| # | Servis | Auth akışı | Token/taşıyıcı | Kullanıcı gerekli mi | Kritik kural | Kanıt |
|---|---|---|---|---|---|---|
| 1 | Spotify | authorization code + **PKCE** (tercih) · authorization code · client credentials | `Bearer` erişim + yenileme token'ı | code akışında evet; client credentials'ta hayır | `code_verifier` 43–128 karakter · `S256`; token POST `application/x-www-form-urlencoded` | https://developer.spotify.com/documentation/web-api/tutorials/code-pkce-flow (erişim: 2026-10-17) |
| 2 | Apple Music | developer token (sunucuda imzalanan JWT) | `Bearer` JWT · başlık `alg: ES256` + `kid` | Hayır (geliştirici kimliği) | Yalnız ES256 kabul edilir, aksi **401**; `exp` en fazla **15777000 sn / 6 ay** | https://developer.apple.com/documentation/applemusicapi/generating-developer-tokens (erişim: 2026-10-17) |
| 3 | YouTube Music | **resmi public API yok** | — | — | TOS riski → kapsam **KAPALI**; ilgili: YouTube Data API v3 `search.list` kotası | Dosya: `index.md` [Satır: 35] · TOS: https://developers.google.com/youtube/terms/api-services-terms-of-service (erişim: 2026-10-17) |
| 4 | Tidal | **OAuth 2.1**: client credentials · authorization code · refresh token | `Bearer` | code akışında evet | **Authorization code akışında PKCE tüm istemciler için zorunlu** (`S256`) | https://developer.tidal.com/documentation/api-sdk/api-sdk-authorization (erişim: 2026-10-17) |
| 5 | Deezer | OAuth (`connect.deezer.com/oauth/auth.php`) · katalog uçları **kimliksiz** | OAuth token (yazma için) · katalog: token yok | Katalog için hayır | `q` zorunlu; resmi sayısal quota yayımlanmamış → ⚠️ VERIFICATION REQUIRED | https://developers.deezer.com/api (erişim: 2026-10-17) |
| 6 | MusicBrainz | API anahtarı yok · IP + User-Agent | — (kimliksiz, saygılı çağrı) | Hayır | **1 istek/saniye/IP**; aşım → **%100 ret → HTTP 503**; `User-Agent` zorunlu (iletişim bilgisi) | https://musicbrainz.org/doc/MusicBrainz_API/Rate_Limiting (erişim: 2026-10-17) |
| 7 | ListenBrainz | özel **user token** (`Authorization` başlığı) | user token | Kısmen (yazma için evet) | Yalnız HTTPS kök; `User-Agent` zorunlu; katalog arama ucu **yok** | https://listenbrainz.readthedocs.io/en/latest/users/api/index.html (erişim: 2026-10-17) |
| 8 | Genius | OAuth2 authorization code · okuma için **client access token** | `Bearer` | Okumada hayır | **Ticari kullanım lisans ister** (api-sales@genius.com) → kapı | https://docs.genius.com/ (erişim: 2026-10-17) |
| 9 | Discogs | OAuth 1.0a · **personal access token** (süresi dolmaz) | OAuth/`Bearer`-token (kişiye özel) | PAT için hayır | Kimliğe göre kota: anonim düşük → token yüksek; `User-Agent` zorunlu | https://www.discogs.com/developers (erişim: 2026-10-17) |
| 10 | Amazon Music | LWA (OAuth 2.0) — **kapalı Beta** | **iki başlık**: `Authorization: Bearer` + `x-api-key` | Beta erişimi zorunlu | Token ~**1 saat**; dolunca **401 INVALID_ACCESS_TOKEN** | https://developer.amazon.com/docs/music/API_web_LWA.html (erişim: 2026-10-17) |
| 11 | Last.fm | `api_key` + gizli `secret` → `api_sig = md5(...)`; web/masaüstü oturum akışı | oturum anahtarı `sk` | Yazma uçlarında evet | İmza: sıralı parametreler + secret (format/callback hariç); `format=json` | https://www.last.fm/api/authspec (erişim: 2026-10-17) |
| 12 | Musixmatch | API key | API key | Hayır | Ücretsiz plan: **günde 2.000 çağrı** + sözlerin **%30'u**; limit aşımı **HTTP 402** | https://developer.musixmatch.com/plans (erişim: 2026-10-17) |

## 4. Auth akışı aileleri (kural seti)

Her aile, CoreMusic tarafında **aynı bileşen grubunu** kullanır (bkz. `agent-bilesenler.md`); farklılık
yalnız adımdadır. Aşağıdaki adımlar servis dokümanlarından çıkarılmıştır; her satırın kanıtı
`agent-web-arastirma.md` §3–§14'tedir.

### 4.1 Aile A — Authorization code + PKCE (Spotify · Tidal)

| Adım | İş | Zorunlu alan / not |
|---|---|---|
| 1 | `code_verifier` üret | 43–128 karakter (Spotify) · Tidal'da `S256` zorunlu |
| 2 | `code_challenge` = base64url(SHA-256(verifier)) | `code_challenge_method=S256` |
| 3 | `state` üret, tarayıcıya yönlendir | ADR-010: `state` doğrulaması atlanmaz |
| 4 | `/authorize` çağr | `client_id`, `response_type=code`, `redirect_uri`, `scope`, `state`, `code_challenge`, `code_challenge_method` |
| 5 | Geri dönünce `state` + `code` doğrula | Uyuşmazlık → akışı iptal et |
| 6 | Token ucu POST | Spotify: `https://accounts.spotify.com/api/token` · form: `grant_type=authorization_code`, `code`, `redirect_uri`, `client_id`, `code_verifier` |
| 7 | Yanıtı ayrıştır | `access_token`, `token_type=Bearer`, `scope`, `expires_in`, `refresh_token` |
| 8 | Yenileme | grant_type=`refresh_token` · **yeni refresh_token gelmeyebilir → eskisi korunur** |

### 4.2 Aile B — Authorization code (Genius · Deezer · Last.fm web akışı)

| Adım | İş | Zorunlu alan / not |
|---|---|---|
| 1 | Servisin authorize ucunu aç | Deezer: `connect.deezer.com/oauth/auth.php?app_id=…&redirect_uri=…&perms=basic_access,email` |
| 2 | Geri dönünce kodu al | `state`/CSRF denetimi ADR-010 gereği uygulanır (PKCE olmayan akışta `state` tek koruma) |
| 3 | Kodu token ucuyla değiştir | Genius: `https://api.genius.com/oauth/token` |
| 4 | Kullanıcı oturumu üret | Last.fm: web akışında `auth.getSession` → `sk` |

### 4.3 Aile C — Client credentials (Spotify · Tidal)

Kullanıcı yok, sunucu kimliği var. Tidal'da Basic auth: `client_id:secret` (bkz. kanıt §6).
`scope` yalnız servisin izin verdiği ölçüde verilir; bu akışta `refresh_token` **dönmez** —
`expires_in` bitince kimlik yeniden alınır (RefreshScheduler bu akışta yeniden edinim planlar).

### 4.4 Aile D — Geliştirici JWT (Apple Music · ES256)

| Alan | Değer | Kanıt |
|---|---|---|
| Başlık `alg` | **ES256** (yalnız bu kabul; farklısı 401) | https://developer.apple.com/documentation/applemusicapi/generating-developer-tokens (erişim: 2026-10-17) |
| Başlık `kid` | 10 karakter Key ID | aynı doküman |
| `iss` | 10 karakter Team ID | aynı doküman |
| `exp` | şu andan en fazla **15777000 sn** (6 ay) | aynı doküman |
| `origin` | web için önerilir | aynı doküman |
| Taşıma | tüm isteklerde `Authorization: Bearer` | aynı doküman |

Kullanıcı tarafı (media user token) **ayrı** bir akıştır; developer token ile karıştırılmaz
(https://developer.apple.com/documentation/applemusicapi/user-authentication-for-musickit — erişim: 2026-10-17).

### 4.5 Aile E — API anahtarı + imza (Last.fm · Musixmatch)

Last.fm: `api_sig = md5(sıralanmış parametreler + secret)` — `format` ve `callback` hariç, alfabetik
sıra; yazma uçları oturum anahtarı `sk` ister (https://www.last.fm/api/authspec — erişim: 2026-10-17).
Musixmatch: düz API key; çift limit vardır — toplam çağrı hakkı ve lyrics çağrı hakkı ayrı sayılır
(https://developer.musixmatch.com/plans — erişim: 2026-10-17).

### 4.6 Aile F — Personal / user token (Discogs PAT · ListenBrainz user token)

| Servis | Token edinimi | Ömür | Not |
|---|---|---|---|
| Discogs | developer settings'ten üretilir | **süresi dolmaz** | anonim → düşük kota; PAT → yüksek kota + image URL |
| ListenBrainz | kullanıcı panelinden alınır | servis yönetir | `Authorization` başlığı; `GET /1/validate-token` ile sınanır |

### 4.7 Aile G — Kimliksiz katalog erişimi (Deezer)

`https://api.deezer.com/search?q=…` kimlik doğrulamasız çalışır; `q` zorunlu, `order`/`limit`/`index`
opsiyoneldir (https://developers.deezer.com/api — erişim: 2026-10-17). Bu akışta token saklama
**yoktur**; yalnız RateLimitHandler + FallbackDriver devrededir.

### 4.8 Aile H — OAuth 1.0a (Discogs)

Üç adımlı imzalı uç: `https://api.discogs.com/oauth/request_token` → `https://www.discogs.com/oauth/authorize`
→ `https://api.discogs.com/oauth/access_token` (https://www.discogs.com/developers — erişim: 2026-10-17).
OAuth 1.0a, OAuth 2.1/Tidal ailesiyle **aynı bileşende birleştirilmez** — ayrı sürücü sınıfı ister.

## 5. Kapsam dışı — ne DEĞİL (devir tablosu)

| # | Yüzey | Bu kapsama girmez mi? | Sorumlu düğüm | Gerekçe |
|---|---|---|---|---|
| 1 | CoreMusic oturumu, rol/izin, CSRF token'ı | Hayır | ADR-095 hybrid JWT alanı | Harici token ≠ oturum jetonu |
| 2 | Veritabanı şemasının geneli (18 BCNF) | Hayır | data alanı (K015 türevi) | Yalnız `TokenStore` tablosu bu kapsamdadır |
| 3 | UI/ekran (login butonu, servis seçimi) | Hayır | ui-design / web katmanı | Mockup gate bu ağacın dışında |
| 4 | Servislerin katalog verisinin işlenmesi (eşleştirme, sıralama) | Hayır | K020-discovery/search · recomenataion | Keşif ≠ entegrasyon kimliği |
| 5 | Servis SDK'larının paketlenmesi/dağıtımı | Hayır | devops / bağımlılık yönetimi | Paket kararı bu ağacın konusu değil |
| 6 | Telifli söz metni saklama/yayın | Hayır | hukuk + lisans kapısı | Genius ticari kullanım lisans ister |
| 7 | Proxy/CDN/TLS altyapısı | Hayır | network katmanı | Taşıma ≠ kimlik |
| 8 | Mobil (iOS/Android) MusicKit oturumu | Hayır | mobile kapsamı | Burada web/sunucu akışı var |
| 9 | YouTube Music veri çekimi (resmi API yok) | Hayır — **KAPALI** | karar mercii: Vault Steward | TOS riski, onay yok |
| 10 | Secret üretimi/döndürme (CI, deploy) | Hayır | devops + security | Bu dosyada yalnız saklama kuralı tekrarlanır |

## 6. Kapsam kapıları (onay bekliyor)

| # | Kapı | Soru | Karar mercii | Durum |
|---|---|---|---|---|
| 1 | Kapsam kapısı | Harici servis entegrasyonu CoreMusic kapsamına giriyor mu? (proje tanımında yok) | Vault Steward | **BEKLİYOR** — `index.md` açık soru, VERIFY REQUIRED |
| 2 | Tasarım onayı | `agent-bilesenler.md` bileşenleri onaylandı mı? | Vault Steward | **BEKLİYOR** (index.md [Satır: 47] kapı kuralı) |
| 3 | Kod kapısı | Entegrasyon kodu | Vault Steward | **KAPALI** — yalnız onay sonrası yazılır |
| 4 | YouTube Music | Resmi API yok; resmi olmayan istemciler ToS'a aykırı | Vault Steward | **KAPALI** (`index.md` [Satır: 35] `⚠️ KAPALI`) |
| 5 | Genius lisansı | Telifli söz üretimi ticari kullanım → lisans gerekir mi? | hukuk + api-sales@genius.com | **BEKLİYOR** |
| 6 | Amazon Music Beta | Kapalı Beta'ya erişim başvurusu yapıldı mı? | Amazon Music Business Development | **BEKLİYOR** (dış başvuru) |
| 7 | Deezer kota | Resmi sayısal limit yayımlanmamış | araştırıcı | **⚠️ VERIFICATION REQUIRED** |
| 8 | Birleşme tasarımı | PKCE/JWT akışları ADR-095 hybrid JWT ile nasıl birleşir? | architect + security | **BEKLİYOR** (tasarım yok) |
| 9 | Yenileme politikası | RefreshScheduler tetik eşiği, döngü sıklığı, kilit | backend + security | **BEKLİYOR** (tasarım yok) |
| 10 | Token saklama yeri | Token DB'de mi, yalnız `.env` mi? (TokenStore) | security | **BEKLİYOR** — her iki seçenek de güvenlik kanununa uymalı |

**Kapı kuralı:** Kapı 1–3 kapalıyken bu dosyadaki satırlar **keşif/tasarım iddiasıdır**; üretime eşdeğer
sayılmaz. Kapı açılması ayrı ADR/kayıt ister.

## 7. Bağlayıcı güvenlik kanunları

| # | Kural | Kaynak | İhlal sonucu |
|---|---|---|---|
| 1 | Token/anahtar yalnız `.env` veya env substitution | kök AGENTS.md §5 Security · index.md [Satır: 39] | Sızıntı → CRITICAL |
| 2 | Token `.md` / `.log` / konsola yazılmaz | kök AGENTS.md §5 | Vault kirlenir, kalıcı sızıntı |
| 3 | ADR-095 hybrid JWT: CoreMusic oturumu ayrı | index.md [Satır: 41] | Oturum gaspı riski |
| 4 | ADR-010/CSRF: `state` + PKCE doğrulaması atlanmaz | index.md [Satır: 42] | CSRF/authorization code interception |
| 5 | ADR-101: timeout/retry/fallback zorunlu | index.md [Satır: 40] | Askıda kalan istek, zincirleme hata |
| 6 | Rate limit aşımında Retry-After'a uy | Spotify/Apple dokümanları (tur-2) | 429 döngüsü, kota kilidi |
| 7 | `User-Agent` zorunlu servislerde iletişim bilgisi var | MusicBrainz · Discogs · ListenBrainz | %100 ret / kota düşüşü |
| 8 | Kod örneklerinde gerçek anahtar yok; her yerde placeholder | bu görev sözleşmesi | Yanlışlıkla sızan gerçek kimlik |

## 8. Roller ve sorumluluklar

| # | Rol | Bu kapsamda ne yapar | Ne YAPMAZ |
|---|---|---|---|
| 1 | Vault Steward | Kapıları açar/kapatır, ADR/ kayıt yazar | Kod yazmaz |
| 2 | backend-architect | Bileşen arayüzlerini tasarlar (§`agent-bilesenler.md`) | Şema migration'ını tek başına uygulamaz |
| 3 | security-engineer | Token saklama, `state`/PKCE denetimi, log redaksiyonu | Servis UI'ını tasarlamaz |
| 4 | data-engineer | Token tablosu şeması (BCNF), indeks | Token değerini loglamaz |
| 5 | qa-engineer | Akış testleri (başarı/429/401/503 yolları) | Üretim secret'ına erişmez |
| 6 | researcher / research-analyst | Web tur doğrulaması, URL erişim kaydı | Doğrulanmayan iddia yazmaz |

## 9. Risk ve kısıt tablosu

| # | Risk / kısıt | Etki | Azaltıcı kontrol |
|---|---|---|---|
| 1 | YouTube Music resmi API yok | Katalog boşluğu | KAPALI kapı; resmi olmayan istemci kullanılmaz |
| 2 | Genius ticari kullanım yasağı | Söz metni üretimi durabilir | Lisans kapısı (§6/5) |
| 3 | Amazon Music kapalı Beta | Erişim yok | Dış başvuru; kapsam dışı bırakılabilir |
| 4 | Deezer resmi kota rakamı yok | Aşım tahmini imkânsız | ⚠️ VERIFICATION REQUIRED + muhafazakâr istemci |
| 5 | Musixmatch 402 + %30 söz limiti | Ücretsiz planda kota | Çifte sayaç (toplam + lyrics) tutulur |
| 6 | MusicBrainz 1 sn/IP | 503 ile tam ret | Yerel kuyruk + 1/sn sabitlayıcı |
| 7 | Spotify yuvarlanan 30 sn pencere | Ani 429 | Retry-After bekleme (RateLimitHandler) |
| 8 | Apple token 6 ay sınırı | Süre dolması | yenileme takvimi (RefreshScheduler) |
| 9 | Amazon token ~1 saat | Sık yenileme | 401 INVALID_ACCESS_TOKEN → yeniden edinim |
| 10 | İkili token (developer + user) karışımı | Yanlış kimlikle istek | Ayrı TokenStore anahtarları (ADR-095 ayrımı) |

## 10. Tipik akış (adım adım — aile A)

1. İstemci `login` isteği atar → sunucu `code_verifier` + `state` üretir ve **yalnız sunucu oturumunda**
   saklar (token `.md`/`.log`'a yazılmaz) → verify: üretildiğine dair kayıt.
2. `/authorize` ucuna yönlendirme yapılır (parametreler §4.1 satır 4) → verify: tarayıcıda `state` döner.
3. Geri dönüşte `state` karşılaştırılır; uyuşmazlık → akış iptal (ADR-010) → verify: negatif test.
4. Kod + `redirect_uri` + `client_id` + `code_verifier` ile token ucu çağrılır → verify: 200 yerine
   gerçek yanıt gövdesi ayrıştırılır (uydurma HTTP durumu yazılmaz).
5. `access_token` + `expires_at` TokenStore'a **şifreli** yazılır → verify: DB satırında düz metin yok.
6. Katalog çağrısı `Authorization: Bearer <TOKEN>` ile gider; 429 gelirse Retry-After kadar beklenir →
   verify: ikinci denemede beklediğine dair süre kaydı.
7. `expires_in` yaklaşınca RefreshScheduler devreye girer; **yeni refresh gelmediyse eskisi korunur** →
   verify: refresh token değeri dönemeden değişti mi.
8. Servis kalıcı olarak düşerse FallbackDriver ikinci servise geçer veya boş sonuç döner (ADR-101) →
   verify: zincirin asla sonsuz döngüye girmediği testi.

## 11. Açık sorular

1. Harici servis entegrasyonu CoreMusic proje tanımına giriyor mu? → **⚠️ VERIFICATION REQUIRED** (index.md frontmatter).
2. PKCE/JWT akışları mevcut auth altyapısıyla (ADR-095 hybrid JWT) nasıl birleşir? → tasarım yok.
3. Token saklama yeri: yalnız `.env` mi, DB'de şifreli blob mu? → TokenStore kararı verilmedi.
4. Genius ticari lisansı alınacak mı, alınmayacaksa söz metni özelliği kapsamdan çıkar mı?
5. Amazon Music Beta başvurusu yapılacak mı? (Yapılmazsa §3/10 satırı kapsam dışına taşınır.)
6. Deezer için resmi sayısal kota nereden doğrulanacak? (Resmi sayfalarda yayımlanmamış.)
7. Rate limit sayaçları süreç (process) bazlı mı, paylaşımlı (DB/Redis) mı olacak? → tasarım yok.
8. YouTube Music kapısı: resmi olmayan istemci yasağı kayıt altına alındı mı? → evet, bu dosya §9/1 + §5/9.

## 12. Ölçüm ve kanıt referansı

| # | Ölçüm | Değer | Kanıt |
|---|---|---|---|
| 1 | Kapsamda servis sayısı | 12 (biri KAPALI: YouTube Music) | §3 tablosu |
| 2 | Auth ailesi sayısı | 8 (A–H) | §4 |
| 3 | Açık kapı sayısı | 6 BEKLİYOR + 1 KAPALI + 2 ⚠️ | §6 |
| 4 | Bağlayıcı güvenlik kuralı | 8 madde | §7 |
| 5 | Doğrulanmış resmi URL (bu dosyada) | §3 + §4 satırlarında tek tek işaretli | `agent-dogrulama.md` §5 |
| 6 | Tur-1 ↔ tur-2 çapraz kontrol | ADR-097 §1 madde 6 (satır 33) iddiaları | `agent-dogrulama.md` §3 |
| 7 | Kod örneği dili | 13 + SQL | `agent-ornekler.md` |
| 8 | Bileşen sayısı | 7 | `agent-bilesenler.md` §2 |

## 13. Kapsam değişiklik kaydı

| # | Tarih | Değişiklik | Kanıt |
|---|---|---|---|
| 1 | 2026-10-12 | Tur-1 keşif: Spotify/Apple/YouTube Music 3 satır | ADR-097 §1 madde 6 (satır 33) · index.md [Satır: 12] |
| 2 | 2026-10-12 | Güvenlik kuralı 4 madde kaydedildi | index.md [Satır: 37-42] |
| 3 | 2026-10-17 | Tur-2: kapsam 12 servise çıkarıldı (§3) | `agent-web-arastirma.md` |
| 4 | 2026-10-17 | Auth ailesi A–H sınıflandırması eklendi (§4) | tur-2 dokümanları |
| 5 | 2026-10-17 | Kapı listesi 10 maddeye çıktı (§6) | bu dosya |
| 6 | 2026-10-17 | `redirect redirect` tekrarı `index.md`'de düzeltildi | index.md [Satır: 33] |

## 14. Kapsam SSS

| # | Soru | Cevap |
|---|---|---|
| 1 | Bu dosya kod içerir mi? | Hayır; kod `agent-ornekler.md`'dedir |
| 2 | CoreMusic oturumu burada mı tanımlanır? | Hayır — ADR-095 hybrid JWT ayrı alandır |
| 3 | Tüm servisler OAuth 2.1 mi? | Hayır; Last.fm md5 imza, Discogs OAuth 1.0a, MusicBrainz kimliksiz, Musixmatch API key |
| 4 | Katalog arama her serviste var mı? | Hayır — ListenBrainz'de katalog parçası araması yoktur |
| 5 | Rate limit rakamları her serviste biliniyor mu? | Hayır — Deezer resmi sayı yayımlamamış → ⚠️ VERIFICATION REQUIRED |
| 6 | Kapılar açılmadan kod yazılır mı? | Hayır — index.md [Satır: 47] kapısı bağlayıcı |

## 15. Auth ailesi × bileşen eşlemesi

Bileşen tanımları `agent-bilesenler.md` §3–§9'dadır; bu tablo **her ailede hangi bileşenin devrede
olduğunu** gösterir (✓ zorunlu · △ koşullu · — gereksiz).

| Aile | PkceHelper | TokenStore | JwtSignerEs256 | RefreshScheduler | RateLimitHandler | FallbackDriver | SecretVault |
|---|---|---|---|---|---|---|---|
| A — code + PKCE (Spotify·Tidal) | ✓ | ✓ | — | ✓ | ✓ | ✓ | △ (client secret yoksa yalnız `client_id`) |
| B — code (Genius·Deezer·Last.fm) | △ (`state` ile) | ✓ | — | △ (servis sunuyorsa) | ✓ | ✓ | ✓ (client secret) |
| C — client credentials (Spotify·Tidal) | — | ✓ (kısa ömür) | — | △ (yeniden edinim) | ✓ | ✓ | ✓ (Basic auth secret) |
| D — developer JWT (Apple) | — | ✓ (kısa ömür) | ✓ | ✓ (yeniden imzalama) | ✓ | ✓ | ✓ (ECDSA private key) |
| E — API key + imza (Last.fm·Musixmatch) | — | — (anahtar `.env`'de) | — | — | ✓ | ✓ | ✓ |
| F — PAT / user token (Discogs·ListenBrainz) | — | ✓ | — | △ (süresiz PAT) | ✓ | ✓ | ✓ |
| G — kimliksiz katalog (Deezer) | — | — | — | — | ✓ | ✓ | — |
| H — OAuth 1.0a (Discogs) | — | ✓ | — | ✓ | ✓ | ✓ | ✓ (consumer secret) |

**Eşlem notu:** Aile D tek başına `JwtSignerEs256`'yı devreye sokar; aile G tek başına
`RateLimitHandler + FallbackDriver` ile yetinir. Bu, bileşenlerin **istek başına değil aile başına**
kurulacağını gösterir (tasarım iddiası — kapı 2 bekliyor).

## 16. Hata yolları ve kapsam içi karşılığı

Yalnız **doğrulanmış** durum kodları yazılır; yayımlanmayanlar ⚠️ ile işaretlenir.

| # | Durum / koşul | Servis | Kapsamdaki karşılığı | Kanıt |
|---|---|---|---|---|
| 1 | **429** + `Retry-After` başlığı | Spotify | RateLimitHandler: Retry-After kadar bekle, tekrar dene | https://developer.spotify.com/documentation/web-api/concepts/rate-limits (erişim: 2026-10-17) |
| 2 | **429** `QUOTA_EXCEEDED` | Spotify (kota modu) | Kullanıcıya kota uyarısı; ajan isteği tekrarlamaz, kuyruğa alır | https://developer.spotify.com/documentation/web-api/concepts/quota-modes (erişim: 2026-10-17) |
| 3 | **401** (yanlış `alg`) | Apple Music | Yalnız ES256 kabul edilir → imzalama yolu denetlenir | https://developer.apple.com/documentation/applemusicapi/generating-developer-tokens (erişim: 2026-10-17) |
| 4 | geçici **429** (istek aşımı) | Apple Music | kısa bekleme + yeniden deneme | https://developer.apple.com/documentation/applemusicapi/handling_requests_and_responses (erişim: 2026-10-17) |
| 5 | **401** `INVALID_ACCESS_TOKEN` | Amazon Music | Süresi dolmuş token → yeniden edinim (LWA) | https://developer.amazon.com/docs/music/API_web_LWA.html (erişim: 2026-10-17) |
| 6 | **402** + `X-RateLimit-Remaining` | Musixmatch | Günlük hak bitti → bekleme (gece yarısı UTC sıfırlanır) | https://developer.musixmatch.com/documentation/status-codes (erişim: 2026-10-17) |
| 7 | **503** (talebin %100'ü reddi) | MusicBrainz | 1/sn oranına inene kadar kuyruk durur | https://musicbrainz.org/doc/MusicBrainz_API/Rate_Limiting (erişim: 2026-10-17) |
| 8 | 4xx/5xx (diğerleri) | Deezer·Tidal·Genius·Discogs·ListenBrainz | Kod yayımlanmadı → **⚠️ VERIFICATION REQUIRED**; yığın genel hata yolundan çıkar | tur-2 taramasında resmi durum listesi bulunamadı |

## 17. Log / gözlemlenebilirlik kapsamı

| # | Alan | Karar | Gerekçe |
|---|---|---|---|
| 1 | Servis adı + olay türü (exchange/refresh/429) | **LOG'LANIR** | Teşhis için gerekli |
| 2 | Gerçek HTTP durumu (uydurma değil) | **LOG'LANIR** | Zero-Hallucination |
| 3 | `Retry-After` değeri, bekleme süresi | **LOG'LANIR** | Sınır uyumu kanıtı |
| 4 | `access_token`, `refresh_token` | **ASLA** — `[REDACTED]` | registry §17/3 maskeleme |
| 5 | `code_verifier`, `code`, `state`, `api_sig` | **ASLA** | Tek kullanımlık sırlar |
| 6 | ECDSA private key, client secret | **ASLA** | SecretVault içindedir |
| 7 | Token `.md` dosyasına yazımı | **YASAK** | kök AGENTS.md §5 Security |
| 8 | Token `.log` / konsol çıktısı | **YASAK** | kök AGENTS.md §5 Security |
| 9 | User-Agent içindeki iletişim bilgisi | LOG'LANMAZ (istek başlığı) | yalnız çağrıya gider |
| 10 | İzleme (trace) kimliği | **LOG'LANIR** | ADR-101 zincir takibi |

## 18. Kapı ön kontrolü (checklist)

Kapı 1–3 açılmadan aşağıdaki maddelerin **hiçbiri** üretim adımına geçmez:

1. Servis × akış eşlemesi (§3) Vault Steward tarafından onaylandı mı?
2. Kapsam dışı devirler (§5) ilgili düğüm sahiplerine iletildi mi?
3. `state` + PKCE denetimi tasarımı security-engineer'dan geçti mi? (ADR-010)
4. Token saklama yeri (`.env` vs şifreli DB) kararı verildi mi? (kapı 10)
5. Token'ın log/MD yasağı test edildi mi? (§17 satır 4–8)
6. Timeout/retry/fallback değerleri ADR-101'e göre yazıldı mı?
7. Her servis için User-Agent şablonu (zorunlu servisler) hazır mı?
8. 429/402/503 yollarının testi var mı? (§16)
9. Genius lisans sorusu cevaplandı mı? (kapı 5)
10. Amazon Music Beta başvurusu kararı verildi mi? (kapı 6)
11. Deezer kota rakamı doğrulandı mı yoksa ⚠️ olarak mı kaldı? (kapı 7)
12. RefreshScheduler tetik eşiği onaylandı mı? (kapı 9)
13. Kod örneklerinde gerçek anahtar olmadığını doğrulayan arama yapıldı mı?
14. CoreMusic oturumu ile harici token ayrımı test edildi mi? (ADR-095)
15. `agent-dogrulama.md` iddia matrisinde eksik kanıt satırı var mı?

## 19. Kapsam dışı senaryolar (YAPMA ↔ doğru devir)

| # | YAPMA | Doğru davranış | Devir |
|---|---|---|---|
| 1 | Refresh token'ı `.log`'a yazmak | `[REDACTED]`, yalnız TokenStore | security |
| 2 | `state` doğrulamasını "gereksiz" bulup atlamak | `state` + PKCE zorunlu | ADR-010 |
| 3 | CoreMusic oturum JWT'siyle Spotify çağrısı yapmak | ayrı kimlik, ayrı taşıyıcı | ADR-095 |
| 4 | YouTube Music için resmi olmayan istemci kurmak | KAPALI kapı, karar ister | Vault Steward |
| 5 | Deezer için uydurma kota sayısı yazmak | ⚠️ VERIFICATION REQUIRED | research |
| 6 | Apple token'ı `alg: HS256` ile imzalamak | yalnız ES256, aksi 401 | security |
| 7 | 429'u hemen tekrarlayıp kota kilidi kırmak | `Retry-After` kadar bekle | RateLimitHandler |
| 8 | MusicBrainz'de 1 sn altında istek atmak | yerel kuyruk + 1/sn sabitleyici | backend |
| 9 | Genius'den telifli söz üretip lisansız yayımlamak | lisans kapısı | hukuk |
| 10 | Harici çağrısız (timeout'suz) istek atmak | timeout/retry/fallback zorunlu | ADR-101 |

## 20. K-node bağlantı haritası

| # | Düğüm | İlişki | Yön |
|---|---|---|---|
| 1 | `K020-discovery/agent` | üst düğüm (bu dosyanın parent'ı) | bağımlı → üst |
| 2 | `K020-discovery/discovery-master.md` | üst ağacın ana sayfası | okunur |
| 3 | `K020-discovery/discovery-web-arastirma.md` | üst düzey tur-2 taraması (12 servis) | bu dosyayı tamamlar |
| 4 | `K020-discovery/discovery-dogrulama.md` | üst düzey doğrulama | bu dosyayı tamamlar |
| 5 | `K020-discovery/search` | katalog arama keşfi (index.md depends_on) | paralel kardeş |
| 6 | `K021-recomenataion` | öneri tarafı — kimlik token'ı burada kullanılmaz | ayrık |
| 7 | `K009-security` | şifreleme, auth middleware, secret kanunu | kural verir → bu ağaç |
| 8 | `K007/data` | token tablosu şeması (BCNF) | şema sahibi |
| 9 | `K011-application/web` | tarayıcı yönlendirme/UI (redirect_uri taşıyan taraf) | sınır komşusu |
| 10 | `K012-services` | sunucu servis katmanı (çağrı sahibi) | sınır komşusu |
| 11 | `K016-observability` | log/metric/tracing alanı (§17) | veri alır |
| 12 | `K010-network` | taşıma/TLS/proxy | altyapı |
| 13 | `K18-testing` | akış testleri (kapı 8) | doğrular |
| 14 | `K17-devops` | secret saklama/dağıtım (CI) | `.env` sahibi |

## 21. Okuma sırası ve bağımlılık

| # | Sıra | Dosya | Ne verir |
|---|---|---|---|
| 1 | önce | `index.md` | künye, kapı, §GRUPLAR |
| 2 | sonra | `agent-kapsam.md` (bu dosya) | sınır + servis × akış + kapılar |
| 3 | sonra | `agent-web-arastirma.md` | tur-2 URL kanıtları (2026-10-17) |
| 4 | sonra | `agent-bilesenler.md` | 7 bileşenin arayüzü |
| 5 | sonra | `agent-ornekler.md` | 13 dil + SQL kod kalıpları |
| 6 | kapanış | `agent-dogrulama.md` | iddia → kanıt matrisi |

**Kural:** kanıt satırı olmayan iddia bu ağaçta barınamaz; çelişkide `agent-dogrulama.md` kazanır.

## 22. Servis başına kapsam kararları

### 22.1 Spotify — KAPSAM İÇİ (authorization code + PKCE)

- İzin verilen kullanım: katalog arama (`GET https://api.spotify.com/v1/search`) ve kullanıcı verisi.
- Sınır: yuvarlanan 30 sn pencere → 429 + `Retry-After` (kanıt §16/1).
- Token: exchange + refresh; **yenilemede yeni refresh_token gelmeyebilir → eskisi korunur**.
- Kapı: 1 (kapsam), 2 (tasarım).

### 22.2 Apple Music — KAPSAM İÇİ (developer JWT · ES256)

- İzin verilen kullanım: katalog kökü `https://api.music.apple.com/v1`, arama `/v1/catalog/{storefront}/search`.
- Sınır: geçici 429 (kanıt §16/4).
- Token: sunucuda imzalanan JWT; `kid` + `iss` 10 karakter, `exp` ≤ 15777000 sn.
- Kapı: 1, 2 + ADR-095 ayrımı (kullanıcı token'ı ayrı akış).

### 22.3 YouTube Music — KAPALI

- Resmi public API **yok** (tur-1 kaydı korunur).
- İlgili resmi yüzey: YouTube Data API v3 `search.list` — günlük 100 çağrı kovası + 10.000 birim/gün.
- Resmi olmayan istemciler (ytmusicapi/yt-dlp/InnerTube cookie) ToS'a aykırı → **kullanım YASAK**.
- Kapı: 4 (kapalı).

### 22.4 Tidal — KAPSAM İÇİ (OAuth 2.1, PKCE zorunlu)

- İzin verilen akış: client credentials · authorization code · refresh token.
- Sınır: resmi kota rakamı yayımlanmadı → ⚠️ VERIFICATION REQUIRED (durum kodları dahil).
- Token: `Authorization: Bearer`; JSON:API gövdesi.
- Kapı: 1, 2 + Basic auth secret'ın SecretVault'ta olması (§15/C).

### 22.5 Deezer — KAPSAM İÇİ (kimlik ayrımı ile)

- Katalog araması kimliksiz: `https://api.deezer.com/search?q=…`.
- OAuth yalnız yazma/kişisel veri için (`connect.deezer.com/oauth/auth.php`).
- Resmi sayısal kota **yok** → ⚠️; apis.io 50/5sn iddiası üçüncü taraf (kullanılamaz).
- Kapı: 7 (kota doğrulaması).

### 22.6 MusicBrainz — KAPSAM İÇİ (kimliksiz, sıkı oran)

- Uç: `/ws/2`; arama Lucene sözdizimi, `limit` 1–100 (varsayılan 25), `fmt=json`.
- Oran: **1 istek/saniye/IP**; aşım → %100 ret → **HTTP 503**.
- `User-Agent` zorunlu ve iletişim bilgisi içermelidir.
- Kapı: 1; uygulama notu: yerel kuyruk zorunlu.

### 22.7 ListenBrainz — KOŞULLU (yazma uçları için user token)

- Kök `https://api.listenbrainz.org` (yalnız HTTPS); `Authorization` başlığı ile user token.
- Uçlar: `POST /1/submit-listens`, `GET /1/validate-token`, `GET /1/search/users/`, `GET /1/user/{name}/listens`.
- **Katalog parçası araması yoktur** → katalog keşfi bu servisle yapılamaz.
- Kapı: 1 + kullanıcı verisi izni.

### 22.8 Genius — KOŞULLU (lisans kapısı)

- Okuma uçları için client access token yeterli (`GET /search?q=`, `Authorization: Bearer`).
- OAuth2 scope'ları: `me`, `create_annotation`, `manage_annotation`, `vote`.
- **Kapı: "Commercial use of the Genius API is not allowed without a license"** → telifli söz üretimi lisans ister.
- Kapı: 5 (lisans) — lisans gelmeden söz metni üretimi kapsam dışı.

### 22.9 Discogs — KAPSAM İÇİ (OAuth 1.0a veya PAT)

- İki yöntem: OAuth 1.0a üç adımlı akış · personal access token (süresi dolmaz).
- Kotalar kimliğe göre: anonim düşük → token sahibi yüksek + image URL.
- `User-Agent` zorunlu (örnek biçim `my_user_agent/1.0`).
- Kapı: 1; OAuth 1.0a ayrı sürücü ister (§4.8).

### 22.10 Amazon Music — KAPALI BETA

- LWA (OAuth 2.0); her istekte **iki** başlık: `Authorization: Bearer …` + `x-api-key`.
- Token ~1 saat (`expires_in`); dolunca 401 INVALID_ACCESS_TOKEN.
- Scope örn. `music::catalog`, `music::playback` (salt-okunur `:read` eki).
- Kapı: 6 (Beta erişimi) — onay yoksa kapsam dışı.

### 22.11 Last.fm — KAPSAM İÇİ (imza + oturum)

- Kimlik: `api_key` + gizli `secret`; `api_sig = md5(sıralı parametreler + secret)`.
- Akış: web (`last.fm/api/auth` → `auth.getSession`) · masaüstü (`auth.getToken` → tarayıcı → `auth.getSession`).
- Yazma uçları oturum anahtarı `sk` ister; JSON için `format=json`.
- Kapı: 1 + secret'ın SecretVault'ta olması.

### 22.12 Musixmatch — KAPSAM İÇİ (API key, çift limit)

- Ücretsiz plan: **günde 2.000 çağrı** + sözlerin **%30'u**; limit aşımı **HTTP 402** (gece yarısı UTC sıfırlanır).
- Çifte sayaç: toplam çağrı + lyrics çağrıları ayrı izlenir.
- Başlık: `X-RateLimit-Remaining`.
- Kapı: 1; API kök URL'si doğrulanmadı → ⚠️ VERIFICATION REQUIRED.

**Authority:** Bayram Ali / Vault Steward
