---
node_id: K013-integraion/external-systems/external-sistem-envanteri
node_type: architecture-inventory
title: "Dış Sistemler — Sistem Envanteri"
type: architecture
status: active
date: 2026-10-16
parent_id: K013-integraion/external-systems
children: []
depends_on:
  - K013-integraion/external-systems/external-anayasa
  - K013-integraion/external-systems/external-web-arastirma
evidence:
  - "https://developer.spotify.com/documentation/web-api/tutorials/code-pkce-flow"
  - "https://developer.apple.com/documentation/applemusicapi/generating-developer-tokens"
  - "https://developers.google.com/youtube/v3/docs"
  - "https://github.com/mbianchidev/open-playlist-engine"
  - "https://developers.soundcloud.com/docs"
  - "https://developers.deezer.com/guidelines/getting_started"
  - "https://www.last.fm/api/authentication"
  - "https://musicbrainz.org/doc/MusicBrainz_API"
  - "https://www.discogs.com/developers"
validation:
  - "Her satır external-anayasa.md §3 kayıt şemasını (10 alan) birebir uygular"
  - "Her satır en az 1 kanıt URL taşır; tüm kanıtlar 2026-10-16'da çekildi"
open_questions:
  - "Durum alanlarının (aktif/beklemede) nihai dağılımı üst otoritede"
group: mimari
---

# Dış Sistemler — Sistem Envanteri

> **Tarih:** 2026-10-16 · **Şema:** `external-anayasa.md` §3 ·
> **Mode:** Red Team · Human Mode · Truth Mode
> Her satır: sistem adı · sağlayıcı · kimlik tipi · API sürümü · durum ·
> uç nokta · risk notu · kanıt URL · doğrulama tarihi · not.
> **Durum alanı karar mercii üst otoritedir** — burada `beklemede` = kanıt hazır,
> kapsam kararı verilmedi.

## 0. Özet Tablo

| # | Sistem | Sağlayıcı | Kimlik tipi | Durum | Kanıt |
|---|--------|-----------|-------------|-------|-------|
| 1 | Spotify | Spotify AB | OAuth 2.0 Code + PKCE (S256) | beklemede | §1 |
| 2 | Apple Music | Apple | JWT (ES256 + kid) — developer token | beklemede | §2 |
| 3 | YouTube Music | Google | Resmi API yok (bkz risk); Data API v3 TOS altı | beklemede (kesinlik notu) | §3 |
| 4 | SoundCloud | SoundCloud | OAuth 2.1 + PKCE (zorunlu) | beklemede | §4 |
| 5 | Deezer | Deezer | OAuth 2.0 (app id + redirect uri) | beklemede | §5 |
| 6 | Last.fm | Last.fm | API key + secret + `api_sig` imzası | beklemede | §6 |
| 7 | MusicBrainz | MetaBrainz | API key yok; OAuth/digest (yazma için) | beklemede | §7 |
| 8 | Discogs | Discogs | Personal access token / key+secret | beklemede | §8 |

---

## 1. Spotify

| Alan | Değer |
|---|---|
| sistem adı | Spotify |
| sağlayıcı | Spotify AB — `developer.spotify.com` |
| kimlik tipi | OAuth 2.0 Authorization Code + **PKCE (`code_challenge_method=S256`)** |
| API sürümü | Web API (developer.spotify.com dokümantasyonu) |
| durum | **beklemede** (kanıt hazır; karar üst otoritede) |
| uç nokta (base) | `https://api.spotify.com` · token: `https://accounts.spotify.com/api/token` |
| risk notu | Rate limit hesap/anahtar bazlı; refresh token yenileme akışı zorunlu |
| kanıt URL | https://developer.spotify.com/documentation/web-api/tutorials/code-pkce-flow · https://www.rfc-editor.org/rfc/rfc7636 |
| doğrulama tarihi | 2026-10-16 |
| not | `code_verifier` 43–128 karakter; `grant_type=authorization_code`; RFC 7636 §4.1–4.3 |

**Akış özeti (kaynaktan):**

1. İstekte `code_challenge` (S256) + `state` gönderilir.
2. Kullanıcı onayı sonrası `code` döner.
3. `POST /api/token` ile `code_verifier` + `code` takası yapılır.
4. `access_token` + `refresh_token` saklanır; yenileme `grant_type=refresh_token`.

---

## 2. Apple Music

| Alan | Değer |
|---|---|
| sistem adı | Apple Music (Apple Music API) |
| sağlayıcı | Apple — `developer.apple.com` |
| kimlik tipi | **Geliştirici JWT'si: ES256 + 10 karakter `kid`** (user token ayrı: Music User Token) |
| API sürümü | Apple Music API (applemusicapi dokümantasyonu) |
| durum | **beklemede** (2026-10-12 beyanı kanıtlandı; kapsam kararı üst otoritede) |
| uç nokta (base) | `https://api.music.apple.com` (dokümantasyon referansı; canlı test ⚠️ VERIFICATION REQUIRED) |
| risk notu | Geçersiz/imzasız token **401** ile reddedilir; `exp` ≤ 15777000 sn; özel anahtar asla repo/md'ye girmez |
| kanıt URL | https://developer.apple.com/documentation/applemusicapi/generating-developer-tokens · https://developer.apple.com/help/account/capabilities/create-a-media-identifier-and-private-key/ |
| doğrulama tarihi | 2026-10-16 |
| not | Claim'ler: `iss` (Team ID), `iat`, `exp`, `origin`; header: `kid` (key ID) |

**Token üretimi özeti (kaynaktan):**

1. Media Identifier + private key oluştur (yardım sayfası adımları).
2. Konsoldan `.p8` özel anahtar indir → `kid` kaydet.
3. ES256 ile JWT imzala: header `{ alg: ES256, kid }`, payload `{ iss, iat, exp, origin }`.
4. İstekte `Authorization: Bearer <token>` → 401 = token geçersiz.

---

## 3. YouTube / YouTube Music

| Alan | Değer |
|---|---|
| sistem adı | YouTube (YouTube Music kapsam notu ile) |
| sağlayıcı | Google — `developers.google.com` |
| kimlik tipi | API key **veya** OAuth 2.0 (Data API v3) |
| API sürümü | YouTube Data API v3 (`/youtube/v3/...`) |
| durum | **beklemede** — kesinlik notu aktif (aşağıda) |
| uç nokta (base) | `https://www.googleapis.com/youtube/v3` |
| risk notu | **R-Yüksek (ToS):** API Services ToS kısıtlama/sonlandırma maddeleri geçerli; ayrıca kota limitleri |
| kanıt URL | https://developers.google.com/youtube/v3/docs · https://developers.google.com/youtube/terms/api-services-terms-of-service |
| doğrulama tarihi | 2026-10-16 |
| not | v3 referansında **"Music" resource'u yok**; playlist/video/channel/search resource'ları var |

**Kapsam kesinlik notu (RED TEAM — karar üst otoritede):**

- 2026-10-12 beyanı: "YouTube Music için resmi API yok, ToS riski var".
- Kanıt: (a) Data API v3 referansı çekildi → Music resource yok; (b) ToS çekildi →
  kısıtlama/sonlandırma maddeleri mevcut.
- **İtiraz (Red Team):** ToS'taki istisna listesi *"e.g."* ile örneklenmiştir;
  "yok" iddiası destekleniyor ama mutlak negatif kanıt değildir.
- **Sonuç:** kanıt dosyalandı (`external-web-arastirma.md` §3.3);
  kapsam kararı üst otoritede.

---

## 4. SoundCloud

| Alan | Değer |
|---|---|
| sistem adı | SoundCloud |
| sağlayıcı | SoundCloud — `developers.soundcloud.com` |
| kimlik tipi | **OAuth 2.1 + PKCE (zorunlu)**; Authorization Code ve Client Credentials |
| API sürümü | API Guide (OpenAPI JSON/YAML yayını; release'te güncellenir) |
| durum | **beklemede** |
| uç nokta (base) | `https://api.soundcloud.com` · auth: `https://secure.soundcloud.com` · token: `https://secure.soundcloud.com/oauth/token` |
| risk notu | Token ~1 saat; **tek kullanımlık refresh**; client credentials: 50 token/12 saat/app, 30 token/1 saat/IP; tüm client'lar şu an confidential (secret zorunlu) |
| kanıt URL | https://developers.soundcloud.com/docs |
| doğrulama tarihi | 2026-10-16 |
| not | Header: `Authorization: OAuth ACCESS_TOKEN`; public uçlar client credentials ile; user uçları Authorization Code ister |

**Akış özeti (kaynaktan):**

1. `GET https://secure.soundcloud.com/authorize?...&code_challenge_method=S256`.
2. `POST https://secure.soundcloud.com/oauth/token` (`grant_type=authorization_code`,
   `code_verifier` zorunlu).
3. `access_token` ~1 saat yaşar; `refresh_token` **tek kullanımlık** → yeniden sakla.
4. `GET https://api.soundcloud.com/me` ile uç testi.

---

## 5. Deezer

| Alan | Değer |
|---|---|
| sistem adı | Deezer |
| sağlayıcı | Deezer — `developers.deezer.com` |
| kimlik tipi | OAuth 2.0 (app id + redirect uri + onay) |
| API sürümü | Deezer API (developer portal) |
| durum | **beklemede** |
| uç nokta (base) | Portal kaynaklı; canlı base URL doğrulaması ⚠️ VERIFICATION REQUIRED (API sayfası login-gated) |
| risk notu | Uygulama kaydı domain/redirect uri ile yapılır; API sayfası login-gated olduğundan uç nokta listesi elden çekilemedi |
| kanıt URL | https://developers.deezer.com/guidelines/getting_started |
| doğrulama tarihi | 2026-10-16 |
| not | `getting_started` rehberi çekildi (app id, domain, redirect uri mesajı); `developers.deezer.com/api` adayı login-gated olduğu için elendi |

---

## 6. Last.fm

| Alan | Değer |
|---|---|
| sistem adı | Last.fm |
| sağlayıcı | Last.fm — `last.fm/api` |
| kimlik tipi | API key + secret; write servisleri `auth.getToken` / `auth.getSession` + **32 karakter `api_sig`** |
| API sürümü | REST API 2.0 |
| durum | **beklemede** |
| uç nokta (base) | `https://ws.audioscrobbler.com/2.0/` (dokümantasyon referansı; canlı test ⚠️ VERIFICATION REQUIRED) |
| risk notu | Write servisleri imza + auth gerektirir; secret md'ye girmez (P5) |
| kanıt URL | https://www.last.fm/api/authentication · https://www.last.fm/api/authspec |
| doğrulama tarihi | 2026-10-16 |
| not | Tarayıcı akışı: `last.fm/api/auth?api_key=...&token=...` → kullanıcı onayı → `auth.getSession` |

**İmza özeti (kaynaktan):** İmzalanacak tüm parametreleri alfabetik sırala,
`api_secret` ekle, MD5 al → `api_sig`; query/body ile gönder.

---

## 7. MusicBrainz

| Alan | Değer |
|---|---|
| sistem adı | MusicBrainz |
| sağlayıcı | MetaBrainz Foundation — `musicbrainz.org` |
| kimlik tipi | **API key YOK** (okuma); yazma/kullanıcı verisi için OAuth veya digest (digest **Ağustos 2027'de kaldırılıyor**) |
| API sürümü | Web Service v2 (`/ws/2/`) |
| durum | **beklemede** |
| uç nokta (base) | `https://musicbrainz.org/ws/2/` (JSON için `Accept: application/json` veya `fmt=json`) |
| risk notu | **1 çağrı/sn** (IP bloğu riski); anlamlı User-Agent zorunlu; ticari kullanım ücretli |
| kanıt URL | https://musicbrainz.org/doc/MusicBrainz_API · https://www.musicbrainz.org/doc/Development/XML_Web_Service/Version_2 |
| doğrulama tarihi | 2026-10-16 |
| not | 13 çekirdek resource (area, artist, recording, release, ...); lookup/browse/search; non-MBID: discid/isrc/iswc |

---

## 8. Discogs

| Alan | Değer |
|---|---|
| sistem adı | Discogs |
| sağlayıcı | Discogs — `www.discogs.com/developers` |
| kimlik tipi | Personal access token / key+secret (auth'lı uçlar için) |
| API sürümü | API v2.0 (REST, JSON) |
| durum | **beklemede** |
| uç nokta (base) | `https://api.discogs.com` (dokümantasyon referansı) |
| risk notu | **User-Agent zorunlu** (yoksa engel); rate limit auth'lı **60/dk**, auth'sız **25/dk** |
| kanıt URL | https://www.discogs.com/developers |
| doğrulama tarihi | 2026-10-16 |
| not | Doğrudan webfetch 403 aldı → exa `.md` yolu ile çekildi (yöntem notu `external-web-arastirma.md` §5) |

---

## 9. Karşılaştırmalı Kimlik Matrisi

| Sistem | Akış | Sync (yenileme) | User davranışı | Secret saklama |
|---|---|---|---|---|
| Spotify | Code + PKCE | refresh token | kullanıcı onayı | env/store |
| Apple Music | JWT (ES256+kid) | token üretimi (exp) | Music User Token ayrı | key file (`.p8`), md'ye YOK |
| YouTube | API key / OAuth | — / OAuth | proje bazlı | env/store |
| SoundCloud | OAuth 2.1 + PKCE | **tek kullanımlık** refresh | onay / public akış | env/store |
| Deezer | OAuth 2.0 | — (portal) | onay | env/store |
| Last.fm | key + `api_sig` | token→session | tarayıcı onayı | env/store |
| MusicBrainz | yok (okuma) | — | yazma: OAuth | — |
| Discogs | token | — | onay | env/store |

## 10. Envanter Kuralı (özet)

1. Yeni dış sistem = bu dosyaya **tam 10 alanlı** satır (anayasa §3).
2. Satır olmadan adaptör kodu yazılmaz (P3 + P6).
3. Kanıt URL'siz satır geçersizdir (P2).
4. `durum` yalnız üst otorite tarafından `aktif`/`kapsam-dışı` olur (§6 anayasa).
5. Secret/anahtar değeri hiçbir satıra girmez (P5) — yalnız `PLACEHOLDER`.

## 11. Envanter Satırları — Ayrıntılı Uç Nokta Tablosu

| Sistem | Autorize (authorize) | Token / uç | API base | Özel header/param |
|---|---|---|---|---|
| Spotify | `accounts.spotify.com/authorize` | `accounts.spotify.com/api/token` | `api.spotify.com` | `code_challenge_method=S256` |
| Apple Music | — (sunucu-side JWT) | — (token üretimi kendi imzası) | `api.music.apple.com` (referans) | `Authorization: Bearer` + header `kid` |
| YouTube | Google OAuth (projeler) | OAuth / API key | `www.googleapis.com/youtube/v3` | `key=` veya `Authorization: Bearer` |
| SoundCloud | `secure.soundcloud.com/authorize` | `secure.soundcloud.com/oauth/token` | `api.soundcloud.com` | `Authorization: OAuth <token>` |
| Deezer | portal OAuth akışı | portal token | ⚠️ VERIFICATION REQUIRED (login-gated) | `redirect_uri` eşleşmesi |
| Last.fm | `last.fm/api/auth` | `auth.getSession` | `ws.audioscrobbler.com/2.0/` (referans) | `api_sig` (32 karakter MD5) |
| MusicBrainz | yok (okuma) | yazma: OAuth/digest→2027-08 | `musicbrainz.org/ws/2/` | `User-Agent` zorunlu · `fmt=json` |
| Discogs | token sayfası | personal access token | `api.discogs.com` | `User-Agent` zorunlu |

**Not:** "referans" ibaresi = kanıt sayfasındaki metinden okundu, ancak canlı
L4 testi yapılmadı (doğrulama §8 kısıt 1–2).

## 12. Satır-Satır Risk Özeti (envanter → anayasa §5 R-seviyesi)

| Sistem | R-seviye | Tetikleyen madde (kanıt) |
|---|---|---|
| YouTube / YouTube Music | **R-Yüksek** | ToS kısıtlama/sonlandırma maddeleri (web-arastirma §2 #5) |
| MusicBrainz | R-Orta | 1 çağrı/sn + IP bloğu + User-Agent (§2 #8) |
| SoundCloud | R-Orta | token ~1 saat, tek kullanımlık refresh, client-credentials limitleri (§2 #14) |
| Discogs | R-Orta | User-Agent zorunlu + 25/dk auth'sız (§2 #12) |
| Spotify | R-Düşük | standart PKCE (§2 #1) |
| Apple Music | R-Düşük (+secret riski) | ES256 özel anahtar saklama (§2 #3) |
| Last.fm | R-Düşük | key+secret+imza (§2 #10/#11) |
| Deezer | R-Bilinmiyor | API sayfası login-gated → uç doğrulanamadı (§2 #13, elenme §5) |

## 13. Sınır Durumlar ve Kısmi Kayıtlar

| Senaryo | Davranış |
|---|---|
| Kanıt URL var ama içeriği 404 | Satır `bilinmiyor` + yeniden çekim görevi (L1) |
| Kaynak erişilebilir ama iddia desteklemiyor | Satır `beklemede` + çelişki notu; karar üst otoriteye |
| Sistem yalnız 3. parti repo ile kanıtlanıyor | Orta-Yüksek güven notu (web-arastirma §4) |
| İki sağlayıcı aynı kimlik tipini paylaşıyor | Satırlar ayrı kalır; ortak akış kodu örneklerinde tekrar eder |
| Envanterde yeni sistem ama kapsam bilinmiyor | `beklemede` + açık soru (index §A) |

## 14. Envanter Bakım Takvimi (özet — ayrıntı doğrulama §7)

| Sıklık | İş | Dosya |
|---|---|---|
| Her K013 oturumu | kanıt tarihi taze mi (>90 gün) | bu dosya + web-arastirma |
| Haftalık | yeni sistem/durum diff'i | bu dosya §1–§8 |
| Aylık | `aktif` sistemlerde L4 duman testi | doğrulama §5 |
| Sürüm öncesi | GATE-EXT-1..4 yeşil | doğrulama §6 |

## 15. Envanter — Sık Sorular

| # | Soru | Cevap |
|---|---|---|
| 1 | Envanter satırı kaç alanlı? | 10 alan (anayasa §3) — eksik satır geçersiz |
| 2 | `durum` alanı kimce değiştirilir? | Yalnız üst otorite (anayasa §6) |
| 3 | Base URL emin değilse ne yazılır? | Kanıt sayfasındaki referans + `⚠️ VERIFICATION REQUIRED` notu (ör. Deezer §5) |
| 4 | Yeni sistem nasıl eklenir? | web-arastirma L1 → bu dosyaya 10 alanlı satır → `beklemede` |
| 5 | Kod örnekleri nerede? | `external-kod-ornekleri.md` (13 dil, PLACEHOLDER) |
| 6 | Risk notu nasıl seçilir? | anayasa §5 R-seviyesi + bu dosya §12 kanıt sütunu |
| 7 | Kanıt ne sıklıkla tazelenir? | >90 gün → L1 yeniden çekim (doğrulama §7) |
| 8 | Kimlik tipi değişirse ne güncellenir? | Bu satır + L3 sözleşmesi (doğrulama §4) + ilgili dil örneği |

## 16. Envanter Entegrasyon Kapsamı (envanter ↔ mimari bağ)

| Bağ | Yön | Açıklama |
|---|---|---|
| envanter → anayasa | yukarı | Her satır §3 şemasıyla yükümlü |
| envanter → web-arastirma | aşağı | Kanıt URL'lerinin kaynağı |
| envanter → doğrulama | aşağı | Kimlik tipi → L3 sözleşmesi eşlemesi |
| envanter → kod-ornekleri | aşağı | Sistem → dil örneği (§14 matrisi) |
| envanter → index.md | yukarı | children + evidence + kapı listesi |
| envanter → integration-master | yukarı | §4 iddia kapanışı (web-arastirma §6) |

**Özet:** Bu dosya klasörün **veri** katmanıdır; kural anayasa, test doğrulama,
örnek kod örnekleri, kanıt web-arastirmadadır. Veri katmanı kural üretmez,
kod yazmaz, kanıt uydurmaz — yalnızca kayıt tutar.

## 17. Envanter Kalite Ölçütleri (definition of done)

Bir envanter satırı "tamam" sayılır ancak:

- [ ] 10 alanın hepsi dolu (eksik → `-` notu bile açıkça)
- [ ] En az 1 kanıt URL, 90 gün içinde çekilmiş (P2 + P7)
- [ ] `durum` sözlük değerlerinden biri (anayasa §3)
- [ ] Risk notu spesifik (sayı/limit/kural içerir; "sorun olabilir" yazılmaz)
- [ ] `⚠️ VERIFICATION REQUIRED` gerekliyse madde bazında ve nedeniyle
- [ ] Secret/anahtar yok (yalnız PLACEHOLDER) — GATE-EXT-4
- [ ] index.md children/evidence ile eşleşiyor

## §GRUPLAR

| Grup | Bu dosyadaki karşılığı |
|---|---|
| mimari | Kayıt satırları (§1–§8), karşılaştırmalı matris (§9), envanter kuralı (§10), uç nokta tablosu (§11), risk özeti (§12), sınır durumlar (§13), bakım takvimi (§14) |
| kod | Yok — yalnız akış özeti adımları (metin); kod `external-kod-ornekleri.md` |
| kanit | Kanıt URL sütunu + doğrulama tarihi; kesinlik notları (§3, §5); R-seviye kanıtları (§12) |

## §Kaynak

| Kaynak | Tarih | Erişim |
|---|---|---|
| `external-web-arastirma.md` §2 (14 URL), §6 (beyan kapanışı) | 2026-10-16 | disk (çekilmiş kanıtlar) |
| `external-anayasa.md` §3/§5/§10 (şema, risk, geçerlilik) | 2026-10-16 | disk |
| `integration-master.md` §4 (iddialar) | 2026-10-12 | disk |
| `api-contracts/openapi/openapi-index.md` (indeks kalıbı) | kalıp | disk |
| Envantere özgü resmi kaynaklar: Spotify · Apple · YouTube · SoundCloud · Deezer · Last.fm · MusicBrainz · Discogs | 2026-10-16 | webfetch/exa (bkz web-arastirma §7 çekim günlüğü) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
