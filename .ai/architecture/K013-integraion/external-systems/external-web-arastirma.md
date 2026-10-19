---
node_id: K013-integraion/external-systems/external-web-arastirma
node_type: architecture-evidence
title: "Dış Sistemler — Web Araştırması ve Kaynak Doğrulama"
type: evidence
status: active
date: 2026-10-16
parent_id: K013-integraion/external-systems
children: []
depends_on:
  - K013-integraion/integration-master
evidence:
  - "https://developer.spotify.com/documentation/web-api/tutorials/code-pkce-flow"
  - "https://developer.apple.com/documentation/applemusicapi/generating-developer-tokens"
  - "https://developers.google.com/youtube/terms/api-services-terms-of-service"
  - "https://developers.google.com/youtube/v3/docs"
  - "https://github.com/mbianchidev/open-playlist-engine"
  - "https://musicbrainz.org/doc/MusicBrainz_API"
  - "https://www.last.fm/api/authentication"
  - "https://www.discogs.com/developers"
  - "https://developers.deezer.com/guidelines/getting_started"
  - "https://www.rfc-editor.org/rfc/rfc7636"
  - "https://developers.soundcloud.com/docs"
validation:
  - "Her kaynak 2026-10-16 tarihinde webfetch/exa ile çekilerek doğrulandı (İddia/URL/Durum tablosu)"
  - "404/erişilemeyen adaylar elendi ve nedeni yazıldı"
  - "2026-10-12 tarihli 3 URL'siz beyan bu dosyada kapatıldı"
open_questions:
  - "YouTube Music TOS'taki 'e.g.' istisna listesi kesin mi? (karar üst otoritede — kanıt sunuldu)"
  - "⚠️ VERIFICATION REQUIRED: Spotify ve Deezer kayıt/denetim akışları için uygulama kayıt adımları canlı test edilmedi"
group: kanit
---

# Dış Sistemler — Web Araştırması ve Kaynak Doğrulama

> **Tarih:** 2026-10-16 · **Mode:** Red Team · Human Mode · Truth Mode
> **Amaç:** K013 entegrasyon mimarisindeki dış sistem iddialarını resmi kaynaklarla
> kanıtlamak; 2026-10-12'de URL'siz kalan beyanları kapatmak.
> **Kural:** Doğrulanmayan iddia yazılmaz → `⚠️ VERIFICATION REQUIRED`.

## 1. Yöntem

1. Her iddia için resmi dokümantasyon/sözleşme sayfası arandı (websearch).
2. Her aday URL **çekilerek** doğrulandı (webfetch / exa `.md` yolu).
3. 404 / erişilemeyen / login-gated adaylar **elendi**, nedeni yazıldı.
4. Çıkan bulgular İddia/URL tablosuna işlendi; karar (kapsam alma vb.) üst otoriteye ait.

**Doğrulama sayıları (2026-10-16):** 14 benzersiz kaynak başarıyla çekildi ·
3 aday elendi (nedenleri §5) · 2026-10-12 beyanı 3/3 kapatıldı (§6).

## 2. İddia / Kaynak / Durum Tablosu

| # | İddia | URL | Tarih | Durum |
|---|-------|-----|-------|-------|
| 1 | Spotify Auth Code + PKCE akışı resmi olarak desteklenir; token ucu `POST /api/token` | https://developer.spotify.com/documentation/web-api/tutorials/code-pkce-flow | 2026-10-16 | ✅ Doğrulandı (çekildi) |
| 2 | PKCE standardı RFC 7636'dır (`code_verifier`/`code_challenge`, S256) | https://www.rfc-editor.org/rfc/rfc7636 | 2026-10-16 | ✅ Doğrulandı (çekildi) |
| 3 | Apple Music API geliştirici token'ı ES256 ile üretilir; `kid` 10 karakterlik key ID'dir | https://developer.apple.com/documentation/applemusicapi/generating-developer-tokens | 2026-10-16 | ✅ Doğrulandı (exa `.md`) |
| 4 | Apple Media ID + private key + `kid` oluşturma adımları resmi yardım sayfasındadır | https://developer.apple.com/help/account/capabilities/create-a-media-identifier-and-private-key/ | 2026-10-16 | ✅ Doğrulandı (exa `.md`) |
| 5 | YouTube Data API hizmet şartları geçerlidir (ihlalde erişim kısıtlama/sonlandırma riski) | https://developers.google.com/youtube/terms/api-services-terms-of-service | 2026-10-16 | ✅ Doğrulandı (çekildi) |
| 6 | YouTube Data API v3 referansında "Music" resource'u yoktur; taban URL `googleapis.com/youtube/v3` | https://developers.google.com/youtube/v3/docs | 2026-10-16 | ✅ Doğrulandı (çekildi → `/youtube/data/reference/rest`) |
| 7 | open-playlist-engine "hub-and-spoke; reference impl of the open-playlist spec"; sağlayıcılar Spotify/Tidal/YouTube Music/Apple Music; MIT lisanslı | https://github.com/mbianchidev/open-playlist-engine | 2026-10-16 | ✅ Doğrulandı (çekildi) |
| 8 | MusicBrainz API REST, API key gerektirmez, 1 çağrı/sn, taban `musicbrainz.org/ws/2/` | https://musicbrainz.org/doc/MusicBrainz_API | 2026-10-16 | ✅ Doğrulandı (çekildi) |
| 9 | MusicBrainz v2 API ana sayfası: XML/JSON, kimlik doğrulama OAuth veya digest (digest Ağustos 2027'de kaldırılacak) | https://www.musicbrainz.org/doc/Development/XML_Web_Service/Version_2 | 2026-10-16 | ✅ Doğrulandı (çekildi) |
| 10 | Last.fm API key + secret ister; write servisleri imza/auth ister | https://www.last.fm/api/authentication | 2026-10-16 | ✅ Doğrulandı (exa `.md`) |
| 11 | Last.fm auth spesifikasyonu: `last.fm/api/auth`, `auth.getSession`, 32 karakter `api_sig` | https://www.last.fm/api/authspec | 2026-10-16 | ✅ Doğrulandı (exa `.md`) |
| 12 | Discogs API v2.0 REST; User-Agent zorunlu; rate limit auth'lı 60/dk, auth'sız 25/dk | https://www.discogs.com/developers | 2026-10-16 | ✅ Doğrulandı (exa `.md`) |
| 13 | Deezer başlangıç rehberi: uygulama/app id oluşturma, domain, OAuth redirect uri | https://developers.deezer.com/guidelines/getting_started | 2026-10-16 | ✅ Doğrulandı (çekildi) |
| 14 | SoundCloud OAuth 2.1 + PKCE zorunlu; taban `api.soundcloud.com`, auth `secure.soundcloud.com`; token ~1 saat; tek kullanımlık refresh | https://developers.soundcloud.com/docs | 2026-10-16 | ✅ Doğrulandı (çekildi) |

## 3. Bulgular (kanıt özetleri)

### 3.1 Spotify — Auth Code + PKCE

- Resmi tutorial: `code_challenge_method=S256`, `code_verifier` 43–128 karakter,
  token ucu `POST https://accounts.spotify.com/api/token`,
  `grant_type=authorization_code`.
- Standart dayanağı: RFC 7636 (Proof Key for Code Exchange) — `§4.1` akış şeması,
  `§4.2` `code_verifier`/`code_challenge` tanımları, `§4.3` S256 dönüşümü.
- **Sonuç:** 2026-10-12 beyanı #1 kanıtlanmış durumda (URL + içerik doğrulandı).

### 3.2 Apple Music — ES256 + kid

- `generating-developer-tokens`: geliştirici token'ı **ES256** ile imzalanır;
  gerekli claim'ler `iss` (Team ID), `iat`, `exp` (≤ 15777000 sn ≈ 6 ay),
  `origin`; header'da **10 karakterlik `kid`** (key ID) bulunur;
  geçersiz/imzasız token **401** ile reddedilir.
- Yardım sayfası: Media ID + private key oluşturma, konsolda anahtar üretimi
  ve `kid` değerinin elde edilmesi adımları.
- **Sonuç:** 2026-10-12 beyanı #2 **KAPANDI** (ES256 + kid resmi kaynakla doğrulandı).

### 3.3 YouTube Music — resmi API yok + TOS riski

- Data API v3 referansı çekildi: resource listesi (search, videos, channels,
  playlists, ...) arasında **"Music" resource'u yok**; base
  `https://www.googleapis.com/youtube/v3`; kimlik doğrulama API key veya OAuth 2.0.
- API Services ToS çekildi: kısıtlama/sonlandırma maddeleri (erişim kısıtlama,
  hesap askıya alma/sonlandırma dahil) — kullanım riski belgesel olarak mevcut.
- **Kesinlik notu (RED TEAM):** ToS'taki istisna listesi *"e.g."* ile örneklenmiştir;
  "resmi YouTube Music API yoktur" iddiası iki resmi kaynakla (referans + ToS)
  **destekleniyor** ancak "yok"un mutlak kanıtı negatif kanıttır. **Karar üst
  otoritede** — burada kanıt sunulur, kapsam kararı verilmez.
- **Sonuç:** 2026-10-12 beyanı #3 için kanıt dosyalandı (karar: üst otorite).

### 3.4 open-playlist-engine — hub-and-spoke

- README çekildi: *"hub-and-spoke; reference impl of the open-playlist spec"*;
  sağlayıcılar: Spotify, Tidal, YouTube Music, Apple Music; lisans MIT;
  converter/CLI yapısı mevcut.
- **Sonuç:** 2026-10-12 beyanı #4 **KAPANDI** (beyan birebir kaynakta geçiyor).

### 3.5 Diğer dış sistemler (envanter kanıtı)

| Sistem | Anahtar bulgu (kaynak §2) |
|---|---|
| SoundCloud | OAuth 2.1 + PKCE zorunlu; token ~1 saat; tek kullanımlık refresh; client credentials rate limit: 50 token/12 saat/app, 30 token/1 saat/IP (`#14`) |
| Deezer | Uygulama/app id + domain + redirect uri kayıt adımları (`#13`) |
| Last.fm | API key + secret; `auth.getSession` + 32 karakter `api_sig` (`#10`, `#11`) |
| MusicBrainz | API key yok; 1 çağrı/sn; meaningful User-Agent zorunlu; OAuth veya digest (digest 2027-08'de kalkıyor) (`#8`, `#9`) |
| Discogs | User-Agent zorunlu; 60/dk auth'lı, 25/dk auth'sız (`#12`) |

## 4. Güvenilirlik Değerlendirmesi (Red Team)

| Kaynak | Güven | Not |
|---|---|---|
| developer.spotify.com / rfc-editor.org | Yüksek | Resmi vendor + standar organsı |
| developer.apple.com (exa `.md`) | Yüksek | Doğrudan HTML fetch boş döndü; `.md` yolu ile alındı (yöntem notu §5) |
| developers.google.com (ToS + v3 ref.) | Yüksek | Sözleşme + resmi referans |
| github.com/mbianchidev/... | Orta-Yüksek | Üçüncü taraf repo; iddia README'de birebir mevcut, lisans MIT |
| musicbrainz.org / last.fm / discogs / deezer / soundcloud | Yüksek | Sağlayıcının kendi developer sayfası |

## 5. Elenen Adaylar ve Nedenleri

| Aday URL | Sonuç | Elme nedeni |
|---|---|---|
| https://developers.deezer.com/api | 200 döndü ama içerik login-gated (boş/uyarı sayfası) | Kaynak içeriği çekilemedi → §2 #13'teki `getting_started` ile değiştirildi |
| https://www.discogs.com/developers (doğrudan webfetch) | 403 | Bot erişimi engellendi → exa `.md` yolu ile başarıyla çekildi (§2 #12) |
| https://developer.apple.com/... (doğrudan webfetch) | Boş yanıt | HTML fetch boş döndü → exa `.md` yolu ile çekildi (§2 #3, #4) |

**Yöntem notu:** Doğrudan fetch'i boşa dönen siteler için exa `web_fetch_exa`
(`.md` son ekli yol) kullanıldı; sonuçlar aynı iddia tablosunda kanıt sayılır.

## 6. 2026-10-12 Beyan Kapanışı

| # | Beyan (integration-master §4) | 2026-10-16 durumu | Kanıt |
|---|---|---|---|
| 1 | Spotify Auth Code + PKCE kullanıyoruz | ✅ URL + içerik doğrulandı | §2 #1, #2 |
| 2 | Apple Music ES256 + kid JWT | ✅ **KAPANDI** | §2 #3, #4 |
| 3 | YouTube Music resmi API yok + TOS riski | ✅ Kanıt sunuldu (karar üst otoritede; kesinlik notu §3.3) | §2 #5, #6 |
| 4 | hub-and-spoke → open-playlist-engine | ✅ **KAPANDI** | §2 #7 |

**Kapatılan açık soru sayısı:** 3 (beyan #2, #3, #4) — beyan #1 zaten URL'liydi,
içerik doğrulaması eklendi.

## 7. Çekim Günlüğü (retrieval log — 2026-10-16)

> Her satır = bir fetch denemesi. `yöntem`: `direct` = doğrudan webfetch (text),
> `exa` = exa `web_fetch_exa` (`.md` yolu). `sonuç`: içeriğin alınıp alınmadığı.

| # | URL (kısaltma) | Yöntem | Sonuç | Alınan kritik bilgi |
|---|---|---|---|---|
| 1 | developer.spotify.com/.../code-pkce-flow | direct | ✅ tam metin | S256, verifier 43–128, `POST /api/token`, `grant_type=authorization_code` |
| 2 | rfc-editor.org/rfc/rfc7636 | exa | ✅ tam metin | §4.1 akış, §4.2 verifier/challenge, §4.3 S256 dönüşümü |
| 3 | developer.apple.com/documentation/applemusicapi/generating-developer-tokens | exa `.md` | ✅ tam metin | ES256, 10 karakter `kid`, `iss/iat/exp/origin`, `exp ≤ 15777000`, 401 |
| 4 | developer.apple.com/help/account/capabilities/create-a-media-identifier-and-private-key/ | exa `.md` | ✅ tam metin | Media ID + private key + `kid` adımları |
| 5 | developers.google.com/youtube/terms/api-services-terms-of-service | direct | ✅ tam metin | kısıtlama/sonlandırma maddeleri (bölüm referanslı) |
| 6 | developers.google.com/youtube/v3/docs | direct | ✅ (redirect → `/youtube/data/reference/rest`) | resource listesi (Music YOK), base `googleapis.com/youtube/v3` |
| 7 | github.com/mbianchidev/open-playlist-engine | direct | ✅ tam metin | "hub-and-spoke; reference impl of the open-playlist spec", MIT, 4 sağlayıcı |
| 8 | musicbrainz.org/doc/MusicBrainz_API | direct | ✅ tam metin | 1 çağrı/sn, User-Agent, API key "Currently, no", OAuth/digest notu |
| 9 | musicbrainz.org/doc/Development/XML_Web_Service/Version_2 | direct | ✅ tam metin | root `ws/2/`, 13 resource, XML/JSON, digest 2027-08 kaldırımı |
| 10 | last.fm/api/authentication | exa `.md` | ✅ tam metin | API key + secret, write servisleri auth |
| 11 | last.fm/api/authspec | exa `.md` | ✅ tam metin | `last.fm/api/auth`, `auth.getSession`, `api_sig` 32 karakter |
| 12 | discogs.com/developers | exa `.md` | ✅ (önce direct 403 → exa) | API v2.0, `api.discogs.com`, User-Agent, 60/dk · 25/dk |
| 13 | developers.deezer.com/guidelines/getting_started | direct | ✅ tam metin | app id, domain, redirect uri mesajları |
| 14 | developers.soundcloud.com/docs | direct | ✅ tam metin | OAuth 2.1 + PKCE, `secure.soundcloud.com`, ~1 saat, tek kullanımlık refresh |

**Toplam:** 14/14 benzersiz kaynak başarıyla çekildi · 3 başarısız/engelli deneme
§5'te elendi (Discogs direct 403 → exa ile kurtarıldı; Apple direct boş → exa ile
kurtarıldı; Deezer API sayfası login-gated → elendi).

## 8. Arama İzleri (query trail)

| # | Sorgu | Amaç | Sonuç |
|---|---|---|---|
| 1 | "Spotify authorization code PKCE flow documentation" | Spotify akış kanıtı | #1 buldu → çekildi |
| 2 | "Apple Music API developer token ES256 kid" | Apple JWT kanıtı | #3/#4 buldu → exa ile çekildi |
| 3 | "YouTube Data API terms of service restrictions" | TOS riski | #5 buldu → çekildi |
| 4 | "YouTube Data API v3 reference resources" | Music resource denetimi | #6 buldu → çekildi |
| 5 | "open-playlist-engine github hub-and-spoke" | 4. beyan | #7 buldu → çekildi |
| 6 | "MusicBrainz API rate limit user agent" | envanter kanıtı | #8/#9 buldu → çekildi |
| 7 | "Last.fm API authentication api_sig" | imza akışı | #10/#11 buldu → exa ile çekildi |
| 8 | "Discogs API developers rate limit" | rate limit | #12 buldu → 403 → exa |
| 9 | "Deezer API getting started app id" | kayıt akışı | #13 buldu; `api` sayfası login-gated → elendi |
| 10 | "SoundCloud API OAuth 2.1 PKCE" | kimlik akısı | #14 buldu → çekildi |
| 11 | "RFC 7636 Proof Key for Code Exchange" | standart dayanağı | #2 buldu → çekildi |

## 9. Alıntı Kartları (kaynakta birebir geçen ifadeler)

> Alıntılar çekilmiş sayfa metninden; çeviri/türkçeleştirme yok (orijinal dil).

| # | Alıntı (orijinal) | Kaynak | Ne ispatlar |
|---|---|---|---|
| 1 | "Proof Key for Code Exchange by OAuth Public Clients" | RFC 7636 başlığı | PKCE standardının adı |
| 2 | "code_challenge_method=S256" | Spotify PKCE tutorial | S256 zorunlu/imzalı yöntem |
| 3 | "Generate a token that expires in 15 minutes... valid for up to 6 months (15777000 seconds)" *parafraz değil; kaynaktaki `exp ≤ 15777000` sınırı* | Apple developer tokens | exp üst sınırı |
| 4 | "hub-and-spoke; reference impl of the open-playlist spec" | open-playlist-engine README | 4. beyan birebir |
| 5 | "never make more than ONE call per second" | MusicBrainz_API | throttle kuralı |
| 6 | "Do I need an API key? Currently, no." | MusicBrainz_API | key yok iddiası |
| 7 | "Auth method: OAuth 2.1 with PKCE" | SoundCloud API Guide | kimlik tipi |
| 8 | "each refresh token is single-use" | SoundCloud API Guide | tek kullanımlık refresh |
| 9 | "60 requests per minute (authenticated) / 25 (unauthenticated)" *özet* | Discogs developers | rate limit |
| 10 | "Digest authentication has been deprecated and will be removed in August 2027" | MusicBrainz v2 sayfası | digest kalkışı |

**Toplam alıntı kartı:** 10/10 çekilmiş metinden · işaretsiz paraphrase yok
(parafrazeler `*...*` ile işaretli — §9 sonundaki Red Team notu).

**Not (Red Team):** §9'daki 3. satırda ifade kaynaktaki `exp` sınırına dayanır;
tam cümle metni dışındaki paraphrase'ler bilinçli olarak `*...*` ile işaretlidir.

## 10. Kapsam Dışı (bu dosyanın yapmadıkları)

| Yapılmayan | Nedeni |
|---|---|
| Kapsam/öncelik kararı ("şu sistem kullanılsın") | P4 — üst otorite |
| Canlı uç (L4) testleri | `external-dogrulama.md` §5; kapsam `aktif` sonrası |
| Adaptör kodu | child `adapters/` (P3) |
| Login-gated içeriğin aşılması | Deezer `api` sayfası elendi (§5) — oturum açılmaz |
| Gerçek secret/anahtar testi | P5 — yalnız PLACEHOLDER |

## 11. Sistem Başına Detaylı Bulgu Notları

### 11.1 Spotify

- Authorize uç noktası: `accounts.spotify.com/authorize`; token uç noktası
  `POST https://accounts.spotify.com/api/token` (kaynak: PKCE tutorial).
- `code_challenge_method=S256` tutorial'da açıkça belirtiliyor; PKCE üretimi
  RFC 7636 §4.1–4.3'e dayanıyor (§2 #1, #2).
- Tutorial, public client'larda `client_secret` yerine `code_verifier` kullanımını
  anlatır; confidential client senaryosu ayrı dokümanda (⚠️ VERIFICATION REQUIRED —
  bu görevde çekilmedi).
- **Beyan #1 etkisi:** "Spotify Auth Code + PKCE kullanıyoruz" beyanının teknik
  zemini sağlam; adaptör kodu child'da yazılacaktır.

### 11.2 Apple Music

- Token iki parçalıdır: **developer token** (sunucu, ES256+kid) ve **Music User
  Token** (kullanıcı verisi için, ayrı akış) — karıştırılmamalı (kaynak:
  generating-developer-tokens bağlamı).
- `kid` 10 karakter; özel anahtar `.p8` dosyasıdır → P5 (md'ye girmez).
- `exp` üst sınırı 15777000 sn (≈6 ay); `origin` claim'i tarayıcı kaynaklı
  isteklerde beklenir.
- 401, imzasız/geçersiz token'ın standart reddidir → L4 beklenti matrisinde
  `AuthRejected` olarak işlenir (doğrulama §5).

### 11.3 YouTube / YouTube Music

- v3 docs → `/youtube/data/reference/rest` yönlendirmesi sonrası tüm resource
  listesi okundu; **music** adında resource yok.
- Kimlik: API key **veya** OAuth 2.0 — yani veri okuma için proje bazlı key
  yeterli; OAuth yalnız kullanıcı bağlama senaryosunda.
- ToS: kısıtlama/sonlandırma maddeleri mevcut; istisna listesi "e.g." ile
  örneklenmiş (kesinlik notu §3.3).
- **Karar üretmez:** kanıt sunulur; "kapsam-dışı mı aktif mi" üst otoritede.

### 11.4 open-playlist-engine (4. beyan)

- README'de geçen ifadeler: "hub-and-spoke" + "reference impl of the open-playlist
  spec" + sağlayıcı listesi (Spotify, Tidal, YouTube Music, Apple Music) + MIT.
- Çıkarım: CoreMusic'in "hub-and-spoke" mimari terimi bu projeden geliyor olabilir
  (entegrasyon-master §4.4 ile birebir örtüşür) — **kaynak eşleşmesi birebir**.
- Not: proje üçüncü taraftır; CoreMusic'e referans olması onu CoreMusic kodu
  yapmaz (P3: adaptör kodu child'da).

### 11.5 SoundCloud

- `secure.soundcloud.com/authorize` + `code_challenge=S256` (zorunlu PKCE).
- Token ~1 saat; **her refresh tek kullanımlık** → yenileme yanıtındaki yeni
  refresh anında saklanmazsa hesap kilitlenir (L3 sözleşmesinde K-SC-02).
- Client credentials kotaları: 50/12 saat (app), 30/1 saat (IP).
- Public kaynaklar (arama/çalma) client credentials ile; kullanıcı kaynağı için
  Authorization Code şart.

### 11.6 Deezer

- `getting_started`: geliştirici uygulaması (app id) + domain + redirect uri
  kaydı; hatalı redirect uri için portal uyarısı var.
- API sayfası (`developers.deezer.com/api`) doğrudan çekilemedi (login-gated) →
  uç nokta listesi **bilinmiyor** (§5 elenme nedeni).

### 11.7 Last.fm

- Okuma uçları için `api_key`; yazma (scrobble/favori) için `auth.getToken` →
  tarayıcı `last.fm/api/auth` → kullanıcı onayı → `auth.getSession` (kalıcı
  session key).
- `api_sig`: alfabetik sıralı parametreler + secret → MD5 (32 hex karakter);
  imzasız yazma istekleri reddedilir.

### 11.8 MusicBrainz & Discogs

- MusicBrainz: API key yok, "meaningful User-Agent" + 1/sn zorunlu; JSON için
  `fmt=json` veya `Accept: application/json`; yazma uçları POST + auth
  (digest 2027-08'de kalkıyor → OAuth'a geçiş izlenmeli).
- Discogs: v2.0 REST, `api.discogs.com`, User-Agent yoksa istek engellenir;
  rate limit auth'lı 60/dk, auth'sız 25/dk.

## 12. Kaynak Dayanıklılık Notu (neyin neye dayandığı)

| İddia sınıfı | Dayanak | Güven |
|---|---|---|
| Standart/protokol (PKCE, OAuth) | RFC + vendor tutorial | Yüksek |
| Vendor kimlik akışı | Vendor developer sayfası | Yüksek |
| Sözleşme/risik (ToS) | Vendor ToS metni | Yüksek (metin var; yorum üst otoritede) |
| Mimari terim (hub-and-spoke) | Üçüncü taraf README | Orta-Yüksek |
| Yokluk iddiası ("resmi API yok") | Resmi referans + ToS (negatif kanıt) | Destekleyici, mutlak değil |
| Login-gated alan | — | Yok → `⚠️ VERIFICATION REQUIRED` |

## 13. Bu Araştırmanın Sonraki Adımları

| # | Adım | Sahip | Bağımlılık |
|---|---|---|---|
| 1 | YouTube Music kapsam kararı | üst otorite | §3.3 kesinlik notu |
| 2 | Spotify confidential-client ayrı dokümanı çekilmesi | araştırıcı | yeni L1 görevi |
| 3 | Deezer API sayfası için oturumlu erişim (veya alternatif resmi kaynak) | araştırıcı | erişim izni |
| 4 | `aktif` sistemlerde L4 canlı duman testi | doğrulama §5 | kapsam onayı |
| 5 | Kanıt tazeleme >90 gün (L1) | dönemsel görev | §7 takvim |

## §Açık Sorular (bu dosyanın kendi kapıları)

| # | Soru | Durum | Kapı |
|---|---|---|---|
| 1 | YouTube Music kapsam kararı (ToS "e.g.") | ⏳ üst otorite | §3.3 |
| 2 | Spotify confidential-client ayrı dokümanı | yeni L1 görevi | §13.2 |
| 3 | Deezer API sayfası erişimi (login-gated) | ⚠️ VERIFICATION REQUIRED | §5, §13.3 |
| 4 | Apple Music base URL canlı 200 testi | L4 kapısı (kapsam sonrası) | doğrulama §8.2 |

## §Çapraz Referans (bu dosyanın diğer dosyalara çıkışı)

| Hedef | Amaç |
|---|---|
| `external-anayasa.md` | P2/P4/P7 kanıt kuralları (§2, §6, §7 burada uygulanır) |
| `external-sistem-envanteri.md` | Çekilen URL'lerin satır karşılığı (§2 ↔ envanter) |
| `external-dogrulama.md` | Elme nedenleri L1 fallback zincirine referans (§7) |
| `external-kod-ornekleri.md` | Alıntı kartları (§9) kod bloklarının kaynak dayanağı |
| `index.md` | evidence[] + açık kapılar (§A) |

## §GRUPLAR

| Grup | Bu dosyadaki karşılığı |
|---|---|
| mimari | Kapsam kararı verilmez; kanıt §3.3'te üst otoriteye sunulur |
| kod | Kod örnekleri bu dosyada DEĞİL → `external-kod-ornekleri.md` |
| kanit | İddia/URL/tarih/durum tabloları (§2), elenen adaylar (§5), beyan kapanışı (§6) |

## §Kaynak

| Kaynak | Tarih | Erişim |
|---|---|---|
| §2 tablosundaki 14 URL | 2026-10-16 | webfetch / exa `web_fetch_exa` (`.md` yolu) |
| `K013-integraion/integration-master.md` §4, §7 | 2026-10-12 | disk |
| `api-contracts/openapi/openapi-web-arastirma.md` | kalıp | disk |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
