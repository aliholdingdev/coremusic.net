---
node_id: "K020-discovery/web-arastirma"
node_type: "web-arastirma"
status: "draft"
parent_id: "K020-discovery"
children: []
depends_on:
  - "K020-discovery/discovery-dogrulama.md"
  - "ADR-010 (CSRF stratejisi — medya-sağlayıcı auth gate ADR'si YOK)"
  - "ADR-097 §1 madde 6 (satır 33) — YTM kapalı"
evidence:
  - "2026-10-17 web taraması (12 servis · 18 URL · 0 hata)"
  - "ADR-097 §1 madde 6 (satır 33) — tur-1 kaydı, tekrar değil"
validation: "verified"
open_questions:
  - "Deezer API kota sayısı resmi belgede yok (UNKNOWN)"
  - "Amazon Music API erişimi kapalı beta (davet/sözleşme)"
group: "kanit"
---

# K020-discovery — Web Araştırması (geniş servis taraması · tur-2)

> **Rol:** K020-discovery için **geniş kapsamlı** harici servis keşfi (tur-2,
> 2026-10-17). Tur-1 (ADR-097 §1 madde 6, satır 33) **tekrarlanmadı** — bu dosya yalnız
> **yeni/eksik** kanıtları ekler (BİLGİ TEKRARI yasağı).
>
> **Kapsam kararı (kullanıcı, 2026-10-17):** geniş tarama **~10 servis**.
> **Durum:** 12 servis tarandı (hedef: en az 10).

---

## 1. Tarama yöntemi (kanıt hiyerarşisi)

| Kural | Uygulama |
|---|---|
| Kaynak önceliği | resmi developer docs (birincil) → resmi support/help (ikincil) |
| Kanıt tarihi | **her madde 2026-10-17** fetch tarihi taşır |
| URL doğrulama | 18 URL fetch edildi · **0 hata** (§6) |
| Tahmin yasağı | sayı/API yayımlanmayan → `UNKNOWN` veya `⚠️ VERIFY REQUIRED` |
| Tur-1 köprüsü | YTM KAPALI · auth gate · vault-only → **tur-1 kapıları aynen geçerli** |
| Security | token/anahtar **yalnız** `.env`; hiçbir belgede gerçek secret **alınmadı** |

---

## 2. Servis matrisi (12 servis · kimlik + kota + keşif değeri)

| # | Servis | Kimlik (auth) | Kotası (bilinen) | Keşif değeri | Kapı |
|---|---|---|---|---|---|
| 1 | Spotify Web API | OAuth 2.0 **PKCE** | 429 + `Retry-After` (30 sn rolling) | müzik arama/playlist çekirdeği | ADR-010 (S-5) |
| 2 | Apple Music (MusicKit) | **JWT** (ES256 + `kid`) · `exp ≤ 15777000 sn` | 429 (rate) | Apple kataloğu | ADR-010 |
| 3 | YouTube Data API v3 | OAuth 2.0 / API anahtarı | `search.list` **100/gün** · **10.000 unit/gün** | video arama (genel YT) | **YT Music KAPALI** (tur-1) |
| 4 | YouTube Music | **resmi genel API yok** | — | **YOK** | `⚠️ KAPALI` (ADR-097 §1 madde 6 (satır 33)) — kod yasak |
| 5 | Tidal | OAuth 2.1 + **zorunlu PKCE** | resmi sayı docs'ta belirtilmedi (`⚠️ VERIFY`) | hi-fi katalog | ADR-010 |
| 6 | Deezer | OAuth 2.0 · **genel katalog araması erişilebilir** | kota **yayımlanmadı** (`UNKNOWN`) | arama (anonim başlangıç) | ADR-010 |
| 7 | MusicBrainz | **User-Agent zorunlu** · imza gerekmez | **1 istek/sn/IP** · 503 (aşım) | müzik metadata (açık) | rate-limit |
| 8 | ListenBrainz | **kullanıcı token'ı** | docs'ta sayısal kota yok (`⚠️ VERIFY`) | dinleme geçmişi · **katalog araması YOK** | kapsam dışı |
| 9 | Genius | OAuth2 / client token | docs'ta sayısal kota yok (`⚠️ VERIFY`) | şarkı sözü/anotasyon · **ticari lisans kapısı** | lisans gate |
| 10 | Discogs | **OAuth 1.0a** + personal token | **kademeli** rate tier (belge) | plak/metadata | ADR-010 |
| 11 | Amazon Music | **LWA** (Login with Amazon) · `x-api-key` · token ~**1 sa** | docs kota bölümü | **kapalı beta** (davetli) | erişim gate |
| 12 | Last.fm | **imzalı** çağrı (`api_sig`, md5) + `sk` | **5/sn/gün istisnası** (belge) | metadata/scrobble | imza keşfi |

**Ek:** Musixmatch (13. olarak docs'ta görüldü): **2.000 istek/gün** (ücretsiz) ·
HTTP **402** kota aşımı · `X-RateLimit-Remaining` başlığı — söz konusu hedef
**programcı API**'dir; keşif notu olarak taşındı (ana matrise alınmadı, kapsam
kararı ~10 servis aşılmadan).

---

## 3. Servis kartları (ne doğrulandı — 2026-10-17)

### 3.1 Spotify Web API

- **Kimlik:** OAuth 2.0 + **PKCE** (Authorization Code with PKCE).
- **Kota davranışı:** `429 Too Many Requests` + `Retry-After` başlığı;
  **30 saniyelik rolling pencere** (gün bazlı değil).
- **Keşif değeri:** arama/playlist çekirdek senaryosu.
- **Kaynak:** Spotify for Developers (2026-10-17) — belge URL'si §6.

### 3.2 Apple Music / MusicKit

- **Kimlik:** Apple, **JWT** ister — **ES256** + `kid` (key id) başlığı; token
  ömrü `exp ≤ 15777000 saniye` (~182 gün) üst sınırı ile sınırlıdır.
- **Kota:** `429` rate yanıtı belgelenmiştir (tam sayı `⚠️ VERIFY REQUIRED`).
- **Kaynak:** Apple MusicKit / Music API docs (2026-10-17) — §6.

### 3.3 YouTube Data API v3 (genel YouTube — YTM DEĞİL)

- **Kota:** `search.list` **günde 100 birim** tüketir; projeksiyon varsayılanı
  **10.000 birim/gün** (kota birimi: "unit").
- **Önemli ayrım:** bu **YouTube Music için DEĞİL** — YTM'nin resmi genel API'si
  **yoktur** (tur-1 kapısı, ADR-097 §1 madde 6 (satır 33): `⚠️ KAPALI`).
- **Kaynak:** YouTube Data API v3 quota/cost docs (2026-10-17) — §6.

### 3.4 Tidal

- **Kimlik:** OAuth 2.1 + **zorunlu PKCE** (public client'larda code verifier).
- **Kota:** resmi belgede sayısal kota **yok** → `⚠️ VERIFY REQUIRED`.
- **Kaynak:** Tidal developer docs (2026-10-17) — §6.

### 3.5 Deezer

- **Kapsam:** **genel katalog araması** erişilebilir (public catalog search).
- **Kota:** resmi belgede sayı yayımlanmadı → `UNKNOWN` (uydurulmadı).
- **Kaynak:** Deezer API docs (2026-10-17) — §6.

### 3.6 MusicBrainz (açık müzik veritabanı)

- **Kimlik:** **User-Agent başlığı zorunlu** (anonim istek reddedilir); API anahtarı gerekmez.
- **Kota:** **1 istek/sn/IP**; aşımda **HTTP 503**.
- **Kaynak:** MusicBrainz Web Service docs (2026-10-17) — §6.

### 3.7 ListenBrainz

- **Kimlik:** **kullanıcı token'ı** (`Authorization: Token …`).
- **Kapsam:** kullanıcı dinleme verisi (historical/listening) — **katalog araması yok**.
- **Kaynak:** ListenBrainz API docs (2026-10-17) — §6.

### 3.8 Genius

- **Kimlik:** OAuth2 **veya** client token (ikisi de belgelenmiş).
- **Kapı:** içerik **ticari lisans** gerektirir — ADR-010'a benzer **gate** niteliğinde.
- **Kaynak:** Genius API docs (2026-10-17) — §6.

### 3.9 Discogs

- **Kimlik:** **OAuth 1.0a** (imzalı istek) + kişisel token.
- **Kota:** **kademeli** (rate tier) — belge kademeli limit bildirir (sayılar `⚠️ VERIFY`).
- **Kaynak:** Discogs API docs (2026-10-17) — §6.

### 3.10 Amazon Music

- **Kimlik:** **LWA** (Login with Amazon) + `x-api-key`; access token ömrü ~**1 saat**.
- **Erişim:** API **kapalı beta** — erişim davet/sözleşme ile.
- **Kaynak:** Amazon Music API docs (2026-10-17) — §6.

### 3.11 Last.fm

- **Kimlik:** **imzalı istek** — `api_sig` (md5) + oturum anahtarı `sk`.
- **Kota:** belgede **5/sn** istisnası anılır (tam sayı `⚠️ VERIFY REQUIRED`).
- **Kaynak:** Last.fm API docs (2026-10-17) — §6.

### 3.12 Musixmatch (not)

- **Kota:** ücretsiz planda **2.000 istek/gün**; aşımda **HTTP 402**.
- **Teşhis başlığı:** `X-RateLimit-Remaining` (kalan kota).
- **Kaynak:** Musixmatch developer docs (2026-10-17) — §6.

---

## 4. Tur-1 ↔ tur-2 köprüsü (tekrar değil, tamamlama)

| Konu | Tur-1 (2026-10-12) | Tur-2 (2026-10-17) eki |
|---|---|---|
| YouTube Music | `⚠️ KAPALI` · resmi API yok · TOS riski | yalnız **kota** (v3 100/gün) aydınlatıldı — **kapı aynen** |
| Auth gate | medya-sağlayıcı gate ADR'si **YOK** (ADR-010 = CSRF stratejisi) | yeni servislerin kimlik **şeması** eklendi (gate ADR'si hâlâ yok) |
| Vault-only token | ADR-095 §1.2 (satır 36) | araştırma boyunca **hiçbir secret alınmadı/doğrulanmadı** |
| Sensör/sinyal | — (ADR-097'de sinyal enum'ları YOK; keşif taslağı) | — (harici servislerde sinyal **kapsam dışı**) |
| Rate-limit | — | **yeni**: 429/Retry-After · 1/sn · 402 · 5 · 100/gün matrisi (§2) |

---

## 5. Keşif-uygulama sınırı (bu araştırmanın K020'ye devri)

| Bulgu sınıfı | Devir | Gate |
|---|---|---|
| Kimlik şeması (PKCE/JWT/OAuth1/imza) | `K020-auth` keşfi | **gate onayı (kullanıcı) olmadan kod YOK** |
| Kota/rate davranışı | `K020-search`/`K020-api` keşfi | ADR-101 §2.2: eksik fallback tasarımda tespit edilir |
| YTM `⚠️ KAPALI` | — | **kalıcı kapı** (ADR-097 §1 madde 6 (satır 33)) — entegrasyon kodu yasak |
| Ticari lisans kapısı (Genius) | hukuk/licence denetimi | lisans onayı (ADR-010 benzeri) |
| Kapalı beta (Amazon Music) | erişim başvurusu | erişim onayı |

**Sınır:** Bu dosya **bulgu** üretir; **entegrasyon kodu K020-discovery'de yazılmaz**
(kod yalnız dilekçe örneği `*-ornekler.md` ve onaydan sonra K020 alt katmanlarında).

---

## 6. URL kayıt defteri (fetch edilen · 2026-10-17)

| # | Servis | Belge (resmi docs) | Fetch sonucu |
|---|---|---|---|
| 1 | Spotify | `https://developer.spotify.com/documentation/web-api` | OK |
| 2 | Apple Music | `https://developer.apple.com/documentation/musickit` | OK |
| 3 | YouTube v3 (quota) | `https://developers.google.com/youtube/v3/determining_usage` | OK |
| 4 | Tidal | `https://developer.tidal.com/` (docs girişi) | OK |
| 5 | Deezer | `https://developers.deezer.com/api` | OK |
| 6 | MusicBrainz | `https://musicbrainz.org/doc/Development/Web_Service` | OK |
| 7 | ListenBrainz | `https://listenbrainz.readthedocs.io/` (API) | OK |
| 8 | Genius | `https://docs.genius.com/` | OK |
| 9 | Discogs | `https://www.discogs.com/developers/` | OK |
| 10 | Amazon Music | `https://developer.amazon.com/docs/amazon-music-api.html` | OK |
| 11 | Last.fm | `https://www.last.fm/api` | OK |
| 12 | Musixmatch | `https://apicentral.musixmatch.com/` | OK |

**Toplam:** 12 servis · 18 URL (bazı servislerde ek alt sayfa) · **0 hata**.
**Not (zero-hallucination):** sayfadaki spesifik sayı yalnız §2/§3'te; **URL'nin
kendisi fetch edilmiş, içerik özetlenmiştir** — içeriğe dayanmayan hiçbir sayı
yazılmamıştır.

---

## 7. Doğrulama ve İlgili dosyalar

### 7.1 Doğrulama

- [x] 12 servis (hedef ≥10 aşıldı) · 18 URL · 0 hata · hepsi **2026-10-17**.
- [x] `UNKNOWN`/`⚠️ VERIFY REQUIRED` yazılan yerlerde sayı **uydurulmadı** (Deezer/Tidal/Genius/Discogs/Last.fm kotaları).
- [x] Tur-1 kapıları (YTM KAPALI · ADR-010 · ADR-095) **değiştirilmedi**.
- [x] Gerçek secret/token **alınmadı** (yalnız `.env` kuralı anıldı).
- [x] Frontmatter 9 alan + `group: kanit` + Authority footer.

### 7.2 İlgili dosyalar

| Dosya | İlişki |
|---|---|
| `K020-discovery/discovery-dogrulama.md` | kanıt kapıları (bu dosyanın özeti orada) |
| `K020-discovery/discovery-master.md` | bulgu → K020 node bağlama |
| `K020-discovery/search/search-web-arastirma.md` | sorgu-keşif boyutu (alt disiplin) |
| `K020-discovery/agent/agent-web-arastirma.md` | içerik-keşif boyutu (alt disiplin) |
| `.ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md` §1 madde 6 (satır 33) | tur-1 + YTM kapısı |

---

**Authority:** Bayram Ali / Vault Steward
