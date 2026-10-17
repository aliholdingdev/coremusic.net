---
node_id: "K020-discovery/search-web-arastirma"
node_type: "web-arastirma"
status: "draft"
parent_id: "K020-discovery/search"
children: []
depends_on:
  - "K020-discovery/discovery-web-arastirma.md"
  - "K020-discovery/search/search-kapsam.md"
  - "ADR-010"
evidence:
  - "2026-10-17 web taraması (12 servis — sorgu-keşif boyutu)"
validation: "verified"
open_questions:
  - "Federasyon sıralaması: servis başına eşzamanlılık üst sınırı belirsiz"
group: "kanit"
---

# K020-discovery/search — Web Araştırması (sorgu-keşif boyutu)

> **Rol:** Geniş servis taramasının (tur-2, 2026-10-17) **arama/sorgu boyutunu**
> ayırır: hangi serviste hangi **arama tipi** var, kota/rate başlıkları ne diyor,
> hangi gate sorguyu **engelliyor**. Tam matris root `discovery-web-arastirma.md` §2'de;
> bu dosya yalnız **sorguya özgü** kanıtları toplar (BİLGİ TEKRARI yasağı azami).

---

## 1. Arama tipi × servis matrisi

| # | Servis | Katalog araması | Kimlik ID arama | Anonim erişim | Sorgu gate'i |
|---|---|---|---|---|---|
| 1 | Spotify | ✓ (`search` endpoint) | ✓ (`id`) | ✗ (token zorunlu) | auth gate ADR'si YOK |
| 2 | Apple Music | ✓ | ✓ | ✗ (JWT zorunlu) | auth gate ADR'si YOK |
| 3 | YouTube v3 | ✓ (`search.list`) | ✓ (`videoId`) | △ (API anahtarı yeterli) | kota 100/gün |
| 4 | YouTube Music | ✗ **kapalı** | — | — | **⚠️ KAPALI** (ADR-097 §1 madde 6) |
| 5 | Tidal | ✓ | ✓ | ✗ (OAuth2.1+PKCE) | auth gate ADR'si YOK |
| 6 | Deezer | ✓ (**public catalog**) | ✓ | **✓ (anonim arama)** | — (en ucuz başlangıç) |
| 7 | MusicBrainz | ✓ (`/ws/2/recording` vb.) | ✓ (**MBID**) | **✓** | User-Agent + 1/sn |
| 8 | ListenBrainz | ✗ (**katalog araması yok**) | ✓ (kullanıcı verisi) | ✗ (token) | kapsam dışı |
| 9 | Genius | ✓ ( şarkı sözü arama) | ✓ | △ (client token) | **ticari lisans** |
| 10 | Discogs | ✓ (`/database/search`) | ✓ | ✗ (OAuth 1.0a imza) | imza + tier rate |
| 11 | Amazon Music | ✓ | ✓ | ✗ (LWA + x-api-key) | **kapalı beta** |
| 12 | Last.fm | ✓ | ✓ | △ (imza gerekli) | `api_sig` (md5) |

**Keşif sırası çıkarımı (sorgu ekonomisi):** anonim/en ucuzdan başla →
`MusicBrainz (1/sn) → Deezer (anonim) → imza/token gerektirenler (S-5 kapısı)`.
Bu sıra B3 kuyruk sırasının dayanağıdır (`search-bilesenler.md` B3).

---

## 2. Rate başlıkları ve sorgu davranışı (kanıt)

| # | Servis | Aşım belirtisi | Sorguya etkisi | Kanıt (2026-10-17) |
|---|---|---|---|---|
| 1 | Spotify | `429` + `Retry-After` (**30 sn rolling**) | sorgu ertelenir; gün bazlı değil | resmi docs |
| 2 | Apple | `429` (tam sayı `⚠️ VERIFY`) | erteleme | resmi docs |
| 3 | YouTube v3 | kota birimi tükenir (`quota`) | `search.list` = **100 birim** | resmi quota sayfası |
| 4 | Tidal | sayısal kota docs'ta yok | `⚠️ VERIFY` | — |
| 5 | Deezer | sayı yayımlanmadı | `UNKNOWN` | — |
| 6 | MusicBrainz | **503** (aşırı istek) | 1/sn/IP; UA **zorunlu** | resmi docs |
| 7 | Genius | docs'ta sayısal kota yok | lisans gate'i öncelikli | — |
| 8 | Discogs | kademeli (tier) | tier'e göre sorgu hacmi | resmi docs |
| 9 | Last.fm | **5/sn** (istisna) | imzalı istek | resmi docs |
| 10 | Musixmatch | **402** + `X-RateLimit-Remaining` | 2.000/gün | resmi docs |

**Sorgu kuralı (B3):** `429`/`402`/`503` → iş **ertelenir**; aynı turda **dövülmez**
(ADR-101 döngü kuralı: değişmez kapı tekrar tekrar denenmez — §master 6).

---

## 3. Sorgu dilbilgisi kanıtları (uç modele dönüşüm için)

| Kanıt | Ne diyor | Nereye bağlanır |
|---|---|---|
| Servisler farklı `id` alanı kullanır (`id` · `videoId` · `MBID`) | tek sonuç şemasında `result_id` + `external_ids` ayrımı gerekir | `search-kapsam.md` §3 |
| Bazı servislerde **katalog araması yok** (ListenBrainz) | `source` enum'u sorgu yeteneğini **taşımalı** | sonuç dilbilgisi |
| YT Music **kapalı** (tur-1) | `gate_state=closed` — sorgu **atılmaz** | B1 gate denetçisi |
| Genius **ticari lisans** | sorgu sonucu **işlenmez** (yalnız bulgu) | lisans gate |
| Amazon **kapalı beta** | sorgu atılmaz; erişim başvurusu ayrı iş | kapsam dışı |

---

## 4. Tur-1 köprüsü (tekrar değil, sorguya bağlama)

| Tur-1 kapısı (ADR-097 §1 madde 6) | Sorguya çevrilmiş hali (2026-10-17) |
|---|---|
| YTM `⚠️ KAPALI` (ADR-097 §1 madde 6) | B1: `youtube_music → gate_state=closed`; **sorgu atılmaz** |
| Auth gate (ADR'si YOK — ADR-010 = CSRF stratejisi) | kimlikli servislerde sorgu **onaysız atılmaz** |
| ADR-095 vault-only token (§1.2, satır 36) | sorgu günlüklerinde token **log'lanmaz** |
| ADR-101 §2.2 fallback zorunlu (döngü kopmaz) | 429/402/503 → erteleme döngüsü **kontrollü** |

---

## 5. Kullanımda sınır (bu araştırmanın K020'ye devri)

| Bulgu | Devir hedefi | Gate |
|---|---|---|
| Arama tipi × servis matrisi (§1) | K020-search sorgu keşfi | S-1 sahiplik kararı |
| Rate başlıkları (§2) | B3 kuyruk sırası + fallback tasarımı | ADR-101 §2.2 |
| Anonim/en ucuz başlangıç (MB→Deezer) | keşif turu ekonomisi | — |
| Lisans/beta kapıları (Genius/Amazon) | erişim/lisans başvurusu | onay |

---

## 6. Doğrulama ve İlgili dosyalar

### 6.1 Doğrulama

- [x] 12 servis sorgu boyutu (tur-2, 2026-10-17) — root dosyadan **sorgu boyutu
  ile ayırt edilmiş** olarak yazıldı (tekrar değil).
- [x] Sayısal iddialar yalnız doğrulanmış docs'tan; olmayan `UNKNOWN`/`⚠️ VERIFY`.
- [x] YTM kapısı **değiştirilmedi** (tur-1 gate aynen).
- [x] Frontmatter 9 alan + `group: kanit` + Authority footer.

### 6.2 İlgili dosyalar

| Dosya | İlişki |
|---|---|
| `K020-discovery/discovery-web-arastirma.md` | tam servis matrisi (bu dosyanın kaynağı) |
| `search/search-kapsam.md` | sonuç dilbilgisi (§3) |
| `search/search-bilesenler.md` | B3 kuyruk (bu kanıtın uygulama yeri) |
| `search/search-dogrulama.md` | kapı denetim listesi |

---

**Authority:** Bayram Ali / Vault Steward
