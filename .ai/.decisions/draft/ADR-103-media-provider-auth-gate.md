---
reference_doc: .ai/.templates/adr/adr-template.md (v2.0.2 — Guardrail #16)
title: "CoreMusic — Medya-Sağlayıcı Auth Gate: Harici Servis Kimlik Şeması Kapısı ve Üretim Entegrasyonu Ön Koşulları"
type: adr
category: decisions
date: 2026-10-17
updated: 2026-10-17
status: draft
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/architecture/K020-discovery/discovery-web-arastirma.md (tur-2, 2026-10-17) · accepted/ADR-010-csrf-protection-strategy.md · accepted/ADR-095-hybrid-jwt-rs256-access-token.md · draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md · accepted/ADR-101-guvenlik-kanonik-sirasi-uzak-cagri.md"
---

# CoreMusic — Medya-Sağlayıcı Auth Gate (harici servis kimlik şeması kapısı)

**Durum:** draft — kullanıcı onayı bekliyor (onay → `accepted/`, redd → `rejected/`)
**Tarih:** 2026-10-17
**Karar Veren:** Bayram Ali / Vault Steward
**İlgili ADR'ler:** ADR-010 (CSRF Protection Strategy — OAuth state katmanı) · ADR-095 (Hybrid JWT RS256 — CoreMusic kimliği) · ADR-097 (K-Space V3 — draft; §1 madde 6 tur-1 kapısı) · ADR-101 (Güvenlik Kanonik Sırası — uzak çağrı sınırları) · ADR-013 (Rate Limiting APCu)
**Numara gerekçesi:** `.ai/.decisions/` numara serisinde en yüksek numara **ADR-102** (draft, 2026-10-13) → **ilk boş numara 103**.

---

## §1 Bağlam (Context)

### §1.1 Mevcut Durum (kanıtlanmış — disk + tur-2 web kanıtı)

1. **Keşif tamamlandı, gate ADR'si YOK:** K020-discovery tur-2 (2026-10-17) **12 servis · 18 URL · 0 hata** tarandı
   (`.ai/architecture/K020-discovery/discovery-web-arastirma.md` §2/§6). Medya-sağlayıcı auth gate'i için
   **ayrı bir ADR bulunmuyordu** — ADR-010 CSRF stratejisidir (harici OAuth akışlarının `state` katmanını
   kapsar, gate'i değil). Boşluk bu ADR ile kapatılır.
2. **Mevcut OAuth altyapısı = sosyal giriş yüzeyi:** `shared/src/OAuth/` diskte **14 dosya** taşır
   (glob, 2026-10-17): 10 sosyal sağlayıcı (`FacebookOAuth · InstagramOAuth · XOAuth · YouTubeOAuth ·
   TikTokOAuth · SnapchatOAuth · RedditOAuth · PinterestOAuth · LinkedInOAuth · DiscordOAuth`) +
   `BaseOAuthProvider.php` · `IOAuthProvider.php` · `OAuthManager.php` · `OAuthRepository.php`.
   `shared/database/migrations/` altında `oauth_states_migration.php` + `oauth_connections_migration.php`
   mevcuttur. Bu altyapı **kullanıcı girişi** içindir; **medya katalog sağlayıcısı (Spotify/Apple Music/
   Tidal/Deezer) bu dizinde YOKTUR** — medya erişimi yeni bir yüzeydir.
3. **CoreMusic kimliği ≠ harici servis kimliği:** ADR-095 §2.1 (satır 51) oturum cookie'si (tarayıcı) ile
   API Bearer JWT'yi (RS256) ayırır; harici medya-sağlayıcı token'ı bu ikisinin de **dışında** üçüncü bir
   sınıf olarak bu ADR ile tanımlanır.
4. **Tur-1 kapıları aynen geçerli:** YouTube Music `⚠️ KAPALI` (ADR-097 §1 madde 6, satır 33 — resmi genel
   API yok, TOS riski). K020-discovery §5: "gate onayı (kullanıcı) olmadan kod YOK".

### §1.2 Sorun Tanımı

Tur-2 bulgusu, 12 servisin **7 farklı kimlik şeması ailesi** kullandığını ve **3 kapı sınıfı**na
girdiğini gösterdi (§2.2 tablo). Bu şemaların her biri farklı secret saklama, imza ve akış
gerektiriyor. Gate'siz doğrudan entegrasyon = (a) secret'ların dağınık ve denetimsiz olması,
(b) rate-limit/fallback davranışının servis başına tutarsızlaşması (ADR-101 ihlali riski),
(c) kapalı/koşullu kapıların (YTM · Genius · Amazon Music) kod yazıldıktan sonra fark edilmesi.
Sorun: **üretim entegrasyonunu başlatacak tek, denetlenebilir kapı kararı vault'ta yok.**

### §1.3 Web Araştırması (tur-2 bulgusunun bu ADR'ye devri)

> Bu ADR **kendi taramasını yapmaz**; K020-discovery tur-2 sonucunu (2026-10-17) kullanır —
> bilgi tekrarı yasağı gereği tam matris **kopyalanmaz**, kapı kararı gerektiren sütunlar
> (kimlik şeması + kapı) §2.2'de projekte edilir. Kaynak: `discovery-web-arastirma.md`.

| Alan | Değer |
|------|-------|
| Web Search **Query** | Kullanıcı kapsam kararı (2026-10-17): "~10+ medya servisi geniş tarama — kimlik + kota + keşif değeri" |
| Web Search **Konusu** | Harici medya-sağlayıcıların **resmi developer docs**'ları (birincil kaynak) |
| Web Search **Bağlam** | K020-discovery tur-2; tur-1 (ADR-097 §1 madde 6, satır 33) **tekrarlanmadı**, yalnız yeni/eksik kanıt eklendi |
| Web Search **Kısa Açıklama** | 12 servis · 18 URL · **0 hata** · hepsi 2026-10-17 fetch tarihi taşır (§6 URL kayıt defteri) |
| Web Search **Uzun Açıklama** | Kimlik tarafı: Spotify OAuth2+PKCE · Apple Music JWT (ES256+kid) · YouTube v3 OAuth2/API anahtarı · YouTube Music resmi API YOK · Tidal OAuth 2.1 + zorunlu PKCE · Deezer OAuth2 (anonim katalog araması erişilebilir) · MusicBrainz User-Agent zorunlu · ListenBrainz kullanıcı token'ı · Genius OAuth2/client token + ticari lisans · Discogs OAuth 1.0a · Amazon Music LWA + x-api-key (kapalı beta) · Last.fm `api_sig` (md5) + `sk`. Kota tarafı: 429+`Retry-After` (Spotify 30 sn rolling) · `search.list` 100/gün (YT v3) · 1 istek/sn/IP + 503 (MusicBrainz) · 402 (Musixmatch, not) · Tidal/Genius/Discogs/Last.fm sayısal kota `⚠️ VERIFY` · Deezer kota `UNKNOWN` |
| Web Search **Paragraf Veri Uzun** | 12 servis / 7 kimlik şema ailesi / 3 kapı sınıfı / 0 hata · 2026-10-17 · sayı yayımlanmayan kotalar `UNKNOWN` yazıldı, uydurulmadı |
| Web Search **Sonucu** | Kimlik şeması aileleri ve kapı sınıfları §2.2 tablosuna sabitlendi; rate-limit/zorunlu fallback ADR-101 §2.2 (satır 51) ile bağlandı |
| Web Search **Alınan Karar** | Harici medya erişimi **tek auth gate**'ten geçer (bu ADR §2); secret yalnız `.env` (ADR-095 §1.2, satır 36); üretim entegrasyonu **kullanıcı onayı** olmadan başlamaz |
| Web Search **Sonuç** | Gate onayı olmadan entegrasyon kodu YOK (K020-discovery §5 devri) — keşif dosyaları kanıt olarak kalır |

### §1.4 Kısıtlamalar

| # | Kısıt | Açıklama |
|---|-------|----------|
| 1 | Secret yüzeyi | ADR-095 §1.2 (satır 36): secret'lar vault'a/log'a yazılmaz; `.env` + `.env.example` placeholder — `.md`/`.json`/`.log` içine gerçek secret **asla yazılmaz** |
| 2 | Kimlik ayrımı | ADR-095 §2.1 (satır 51): oturum cookie'si ↔ CoreMusic API Bearer JWT; harici servis token'ı **her ikisinden de ayrı** sınıftır, CoreMusic JWT altyapısına (RS256/jti) **karıştırılmaz** |
| 3 | OAuth state | ADR-010: harici OAuth akışında `state`-CSRF IMPLEMENTED (`oauth_states` migration'ı diskte mevcut); medya akışları bu katmanı **atlayamaz** |
| 4 | Uzak çağrı | ADR-101 §2.2 (satır 51): timeout zorunlu · retry max 3 (üstel geri çekilme) · **fallback zorunlu** — "her zaman 200" varsayılamaz |
| 5 | YTM kapısı | ADR-097 §1 madde 6 (satır 33): YouTube Music `⚠️ KAPALI` — **kalıcı**; entegrasyon kodu yasak |
| 6 | Onay kapısı | K020-discovery §5: gate onayı (kullanıcı) olmadan kod YOK; bu ADR taslağı da bu kuralın kendisidir |
| 7 | Bilinmeyen kotalar | Deezer kota `UNKNOWN`; Tidal/Genius/Discogs/Last.fm sayısal kota `⚠️ VERIFY` — sayı **uydurulmaz**, tasarım bunlara göre esnetilir (§4.3 risk) |

---

## §2 Karar (Decision)

**Harici medya-sağlayıcı erişimi, tek bir auth gate'den (K020-auth) geçer.** Gate her servis için
5 unsur taşır ve üretim entegrasyonu bu gate kaydı olmadan **başlamaz**:

1. **Servis kaydı + kapı durumu:** `AÇIK` (keşif) / `KOŞULLU` (ek onay: lisans veya erişim başvurusu) / `KAPALI` (kalıcı yasak).
2. **Şema adapter'ı:** servisin gerçek kimlik şeması ailesine göre (§2.2) — tek şemaya zorlama yok.
3. **Secret saklama:** yalnız `.env` (placeholder `*.example`'da; gerçek değer yalnız ortamda) — ADR-095 §1.2 (satır 36).
4. **Rate/fallback profili:** 429/`Retry-After`·503·402·timeout davranışları ADR-101 §2.2 (satır 51) şartına bağlı.
5. **Onay kaydı:** kapı `KOŞULLU`/`KAPALI` ise Vault Steward onayı — bu ADR §7 akışı.

### §2.1 Neden Bu Seçenek?

- **Tek denetim noktası:** 7 şema ailesi × 12 servis dağınık kodla yönetilemez; gate, secret/rate/kapı
  kurallarını tek yerde toplar ve denetlenebilir kılar.
- **Mevcut altyapıyla karışmaz:** `shared/src/OAuth/` sosyal **giriş** yüzeyidir (10 sosyal sağlayıcı);
  medya katalog erişimi ayrı adapter sınıfı olarak tutulur — `BaseOAuthProvider`'dan türetilse bile
  secret'ları, scope'ları ve kapıları ayrıdır.
- **Kapılar kod önceden görünür:** YTM (KAPALI), Genius (KOŞULLU — ticari lisans), Amazon Music
  (KOŞULLU — kapalı beta, erişim başvurusu) yazılmadan önce bellidir; keşif-uygulama sınırı korunur.
- **Tur-2 kanıtı yeterli:** şema aileleri resmi docs'tan 2026-10-17'de doğrulandı (18 URL, 0 hata) —
  tasarım tahminle değil kanıtla kurgulanır.

### §2.2 Teknik Detaylar

**Kapı tablosu (tur-2 kimlik sütununun bu ADR'ye projeksiyonu — tam matris: `discovery-web-arastirma.md` §2):**

| # | Servis | Kimlik şeması (aile) | Kapı |
|---|--------|----------------------|------|
| 1 | Spotify Web API | OAuth 2.0 + PKCE | AÇIK (state zorunlu — ADR-010) |
| 2 | Apple Music (MusicKit) | JWT (ES256 + `kid`) | AÇIK (dev token yalnız sunucuda/imzalı) |
| 3 | YouTube Data API v3 | OAuth 2.0 / API anahtarı | AÇIK (genel YT — **YTM değil**) |
| 4 | YouTube Music | resmi genel API YOK | **KAPALI** (ADR-097 §1 m.6, satır 33) |
| 5 | Tidal | OAuth 2.1 + zorunlu PKCE | AÇIK (state zorunlu — ADR-010) |
| 6 | Deezer | OAuth 2.0 (anonim katalog araması) | AÇIK (kota `UNKNOWN` — §1.4/7) |
| 7 | MusicBrainz | User-Agent zorunlu (imzasız) | AÇIK (1/sn/IP — 503'e karşı profil) |
| 8 | ListenBrainz | kullanıcı token'ı | AÇIK (katalog araması YOK — kapsam notu) |
| 9 | Genius | OAuth2 / client token | **KOŞULLU** (ticari lisans onayı) |
| 10 | Discogs | OAuth 1.0a + personal token | AÇIK (imzalı istek ayrı adapter) |
| 11 | Amazon Music | LWA + `x-api-key` (~1 sa token) | **KOŞULLU** (kapalı beta — erişim başvurusu) |
| 12 | Last.fm | `api_sig` (md5) + `sk` | AÇIK (imza keşfi ayrı adapter) |

**Şema ailesi → adapter eşlemesi (7 aile):**

| Aile | Adapter yükü | Örnek servis |
|------|--------------|--------------|
| OAuth 2.0 + PKCE | mevcut `BaseOAuthProvider` akışına yakın; `state` zorunlu | Spotify · Deezer · YT v3 · Genius |
| OAuth 2.1 + zorunlu PKCE | code verifier zorunlu (public client) | Tidal |
| JWT (ES256 + `kid`) | sunucu tarafı token imzalama; `kid` rotasyonu | Apple Music |
| OAuth 1.0a | imzalı istek (HMAC-SHA1) — ayrı yol | Discogs |
| `api_sig` (md5) + `sk` | istek-imiza + oturum anahtarı | Last.fm |
| LWA + `x-api-key` | token ~1 sa → yenileme döngüsü | Amazon Music |
| User-Agent / statik token | başlık zorunluluğu / `Authorization: Token …` | MusicBrainz · ListenBrainz |

**Ortak kurallar (tüm adapter'lara bağlayıcı):**

- Token/anahtar **yalnız `.env`**; `.env.example` yalnız placeholder; hiçbir `.md`/`.json`/`.log`'a
  gerçek secret yazılmaz (AGENTS.md §16.3). Repo içi sızma taraması: GitLeaks (DevOps kanıtı: `.github/workflows/secret-scan.yml`).
- **CoreMusic token'ı ile karıştırma yasağı:** medya access token'ı `user_tokens`/jti rejimine
  (ADR-095) **girmez**; kendi tablo/alanında yaşar (şema tasarımı K020-auth keşfinde, bu ADR'de değil).
- **Uzak çağrı profili:** timeout zorunlu · retry max 3 üstel · fallback zorunlu (ADR-101 §2.2, satır 51);
  429'da `Retry-After` dinlenir (Spotify 30 sn rolling).
- **state zorunlu:** OAuth redirect yapan her ailede ADR-010 state katmanı (`oauth_states`) kullanılır —
  atlanamaz; imza tabanlı/yönlendirmesiz aileler (MusicBrainz/Last.fm) state'sizdir, bunlar da
  state eklenmez (schema gerçekliği).
- **YTM:** hiçbir adapter/aile altında **kod yazılmaz** — KAPALI kapı kalıcıdır.

**Kapsam sınırı:** Bu ADR **karar kaydıdır — kod üretmez**. Adapter iskeleti, `.env.example`
satırları ve tablo tasarımı onay sonrası `K020-auth` keşfi (`.ai/architecture/` altında) içinde
planlanır; onaysız üretim kodu yoktur (K020-discovery §5).

---

## §3 Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Gate'siz, servis başına dağınık entegrasyon** (durum quo) | ilk servis en hızlı görünür | secret/rate/fallback servis başına dağılır; kapılar (YTM/Genius/Amazon) kod sonrası fark edilir; denetim yüzeyi yok | K020-discovery §5 ihlali (onaysız kod); ADR-101 fallback'i tasarımda tespit edilemez; sızıntı riski artar |
| 2 | **Tüm servisleri tek şemaya zorla** (hepsi OAuth2+PKCE proxy) | tek adapter, tek test seti | disk kanıtı 7 farklı aile gösteriyor (OAuth 1.0a, md5 imza, JWT ES256, LWA, User-Agent) — zorlama teknik olarak imkânsız; imza tabanlı servisler elenmek zorunda kalır | `discovery-web-arastirma.md` §2 gerçekliğini inkâr eder; keşif değeri olan servisler (Last.fm/Discogs/MusicBrainz) düşer |
| 3 | **Servis başına hazır SDK paketi** | topluluk desteği, hızlı başlangıç | 12 servis ≈ 12 bağımlılık; yükleme/bakım/güvenlik yüzeyi şişer; vault bağımlılık kuralları (güncel sürüm + lisans denetimi) her paket için ayrı iş yükü | YAGNI + bağımlılık denetim maliyeti; gate katmanı yine gerekir (SDK secret/rate'i CoreMusic kuralına bağlamaz) |
| 4 | **Gate kararını ADR'ye taşımadan keşif notunda bırak** | sıfır süreç maliyeti | izlenebilir karar yok; üretim kapısı belirsiz kalır; §7 onay akışı işlemez; gelecekteki oturum "neden böyle?" sorusunu bulamaz | ADR gerekçesi testi geçiyor (hard-to-reverse + çok dosya + gerçek alternatifler); not-olarak-bırakma kararı kalıcı yapmaz |

---

## §4 Sonuçlar (Consequences)

### §4.1 Olumlu Sonuçlar

- Harici medya erişimi **tek denetim noktasından** yönetilir; kapı durumu her servis için görünür.
- Secret kuralı tek cümlede bağlanır (ADR-095 §1.2 → `.env`); `.md`/log sızıntısı yüzeyi kapanır.
- Fallback (ADR-101) adapter tasarımının **önceden şartı** olur — 429/503/timeout servis başına savsaklanmaz.
- Onay reddedilse bile keşif kanıtı (12 servis · 18 URL · 0 hata) vault'ta kalır; karar gerekçesi izlenir.

### §4.2 Olumsuz Sonuçlar

- Ek katman: her aile için adapter emeği (7 aile) — tek şemaya kıyasla daha çok başlangıç işi.
- Onay bekleme = K020-auth keşfinin gecikmesi (kapı kuralı gereği bilinçli duruş).
- `BaseOAuthProvider`'dan bağımsız/seyreltilmiş kod yolu → sosyal OAuth ile ortak bakım yüzeyi sınırlı.

### §4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| `.env` dışına secret sızması | düşük | yüksek | placeholder-only `.md` kuralı + GitLeaks secret-scan (`.github/workflows/secret-scan.yml`) + §7 onay |
| Rate-limit aşımı → hesap kısıtlaması | orta | orta | ADR-101 retry/fallback zorunlu + `Retry-After`/`X-RateLimit-Remaining` başlığı dinleme + MusicBrainz 1/sn profil |
| YTM'ye yanlışlıkla kod yazılması | düşük | çok yüksek | KAPALI kapı tabloda sabit (§2.2 madde 4); review'da açık yasak |
| Genius içerik ticari lisans ihlali | düşük | yüksek | KOŞULLU kapı — lisans onayı olmadan adapter YOK |
| Amazon Music erişim reddi / kota belirsizliği | kesin (bugün) | orta | KOŞULLU kapı + erişim başvurusu; erişim gelmeden adapter yazılmaz |
| Deezer/Tidal/Genius/Discogs/Last.fm kota `UNKNOWN`/`⚠️ VERIFY` | kesin (belge eksikliği) | orta | sayı uydurma yasağı; fallback profili en kötü senaryoya (429/402/503) görelenir; netleşince ADR-101'e göre revize |

---

## §5 Uygulama (Implementation)

### §5.1 Adımlar (onay sonrası — sıralı, her adım ayrı doğrulama)

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | `K020-auth` gate keşfi: adapter arayüzü + kapı enum + token saklama şeması (`.ai/architecture/` altında) | MO + Backend Architect | `⚠️ VERIFICATION REQUIRED` (onay sonrası planlanır) |
| 2 | `.env.example` placeholder satırları (servis başına `*_CLIENT_ID=` / `*_API_KEY=` — gerçek değer yok) | Security Engineer | onay sonrası |
| 3 | OAuth 2.x aileleri için ADR-010 state katmanının medya akışlarına taşınması (`oauth_states` mekanizması) | Security Engineer | onay sonrası |
| 4 | ADR-101 fallback profili (429/503/402/timeout) + `Retry-After` dinleme | Backend Architect | onay sonrası |
| 5 | Kapı onayları: Genius lisans denetimi · Amazon Music erişim başvurusu · Deezer kota araştırması (netleştirme) | Vault Steward | `UNKNOWN` |

### §5.2 Geri Dönüş Planı

Bu ADR **kod içermez** — reddedilmesi K020-discovery kanıt dosyalarını etkilemez (onlar bağımsız
kalır), hiçbir entegrasyon kodu yazılmamış olduğundan geriye dönüş maliyeti **0**'dır. Onaylanıp
adımlar uygulandıktan sonra: `.env` satırları config-only olduğundan geri alınabilir; adapter kodu
`git revert` ile çekilir (gate kaydı ADR'de kaldığı için tekrar denemede baştan savsaklanmaz);
kapı `KOŞULLU`'dan `KAPALI`'ya dönerse (ör. lisans reddi) yalnız o servis adapter'ı devre dışı
kalır, diğer aileler etkilenmez. Vault bozulursa `git checkout` + son commit (AGENTS §17 #10).

---

## §6 İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| `.ai/architecture/K020-discovery/discovery-web-arastirma.md` | tur-2 kanıt kaynağı (12 servis · 18 URL · 0 hata · 2026-10-17) |
| `.ai/architecture/K020-discovery/discovery-dogrulama.md` | kanıt kapıları (özet kapıları) |
| `.ai/architecture/K020-discovery/discovery-master.md` | bulgu → K020 node bağlama (K020-auth devri) |
| `accepted/ADR-010-csrf-protection-strategy.md` | OAuth state-CSRF (harici akış katmanı) |
| `accepted/ADR-095-hybrid-jwt-rs256-access-token.md` | CoreMusic kimliği (session ↔ API Bearer) — harici token sınıfının dışında |
| `draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md` | §1 madde 6 (satır 33) tur-1 kapısı — YTM KAPALI |
| `accepted/ADR-101-guvenlik-kanonik-sirasi-uzak-cagri.md` | §2.2 (satır 51) timeout/retry/fallback zorunlu |
| `accepted/ADR-013-rate-limiting-apcu.md` | yerel rate-limit altyapısı (harici profil ile kesişim) |
| `shared/src/OAuth/` | mevcut sosyal OAuth altyapısı (14 dosya — referans; kapsam dışı yüzey) |
| `[[../index]]` | `.ai/.decisions/index.md` kayıt satırı |

---

## §7 Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali | 2026-10-17 | ⏳ (taslak sunuldu — onay bekliyor) |
| Tech Lead | — | — | ⏳ |
| Arch Lead | — | — | ⏳ |

---

*ADR-103 (draft) — CoreMusic Medya-Sağlayıcı Auth Gate*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-10-17*
*Mode: Red Team · Human Mode · Truth Mode*
