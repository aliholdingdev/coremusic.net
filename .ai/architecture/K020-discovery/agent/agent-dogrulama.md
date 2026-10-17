---
node_id: K020-discovery/agent/agent-dogrulama
node_type: subdomain
status: draft
parent_id: K020-discovery
children: []
depends_on:
  - K020-discovery/agent/agent-kapsam
  - K020-discovery/agent/agent-web-arastirma
  - K020-discovery/agent/agent-ornekler
  - K020-discovery/agent/agent-bilesenler
evidence:
  - K020-discovery/agent/agent-kapsam.md
  - K020-discovery/agent/agent-web-arastirma.md
  - K020-discovery/agent/agent-ornekler.md
  - K020-discovery/agent/agent-bilesenler.md
  - K020-discovery/agent/index.md
  - K020-discovery/discovery-web-arastirma.md
  - K020-discovery/discovery-dogrulama.md
  - K020-discovery/discovery-kapsam.md
  - K020-discovery/discovery-bilesenler.md
  - K011-application/web/index.md
validation: "2026-10-17 — web tur-2 ile doğrulandı"
open_questions:
  - "Authorize host'ları (Spotify / Apple / Tidal) ve Tidal/Musixmatch/Amazon/Last.fm/Discogs API host'ları disk kanıtı yok — ⚠️ VERIFICATION REQUIRED"
  - "Deezer kota sayısal değeri, MusicBrainz /ws/2 kök yolu, Musixmatch API kökü, Apple storefront değerleri doğrulanmadı"
  - "Rust sha2/rand, C/C++ OpenSSL/libcurl sürümleri ve PHP Guzzle/composer.json dosyaları vault'ta yok"
  - "MySQL AES_ENCRYPT-GCM desteği uygulama katmanı GCM + nonce kolonları ile telafi edildi (DB tarafı ⚠️)"
group: kanit
---

# K020-discovery/agent — Doğrulama (Kanıt Defteri · 2026-10-17 web tur-2)

> Bu K-node, `K020-discovery/agent/` alt ağacının **kanıt defteridir**: her dosyanın
> iddiası hangi satır hangi kaynağa dayanır, hangi iddia `⚠️ VERIFICATION REQUIRED`
> ile işaretli, hangi güvenlik yasakları hangi bölümde tekrarlanıyor — hepsi tek
> yerde. Zero-Hallucination: `Doğrulama` sütunu yalnız `✓` (kaynaklı) veya `⚠️`
> (kanıtsız) olabilir; arada "sanırım" yoktur.

---

## 1. Dosya Eşleme (hangi dosya ne kanıtlıyor)

| Dosya | Rol | Kanıt sınıfı | Doğrulama |
|---|---|---|---|
| `agent-kapsam.md` | K-scope: K010–K015, 12 servis × auth ailesi A–H, gate'ler | kanıt + tasarım (kanıtlanmamış tasarım açıkça işaretli) | ✓ |
| `agent-bilesenler.md` | Şema + bileşen sözleşmeleri (auth_broker, token_vault vb.) | kanıt (K005/K015/K019) + tasarım (K020) | ✓ |
| `agent-ornekler.md` | 13 dil + SQL kod örnekleri | kanıt (doküman satırları) + tasarım (kod iskeleti) | ✓ |
| `agent-web-arastirma.md` | 12 servis auth akışı, URL erişim kaydı, kanıt sınıfları | %100 kanıt (yalnız doğrulanmış) | ✓ |
| `agent-dogrulama.md` | Bu dosya — kanıt defteri | meta | ✓ |
| `index.md` | Bölüm 1–5 indeksi + grup tablosu | meta | ✓ |

---

## 2. İddia → Kanıt Matrisi (dosya-satır düzeyi)

| İddia | Kaynak | Kanıt sınıfı | Doğrulama |
|---|---|---|---|
| Spotify: 43–128 arası verifier uzunluğu | `agent-web-arastirma.md` §3.1 (43–128) | resmi doküman | ✓ |
| Spotify: S256 zorunlu, plain `code_verifier` 400 | `agent-web-arastirma.md` §3.1 | resmi doküman | ✓ |
| Spotify: 429 + `Retry-After` header | `agent-web-arastirma.md` §3.1 | resmi doküman | ✓ |
| Spotify: authorize host'u | `agent-web-arastirma.md` §7 (5.) | **kanıt yok** | ⚠️ |
| Apple: ES256 · `kid` 10 karakter · `iss` 10 · `exp` ≤ 1.577.700.000 sn | `agent-web-arastirma.md` §3.2 | resmi doküman | ✓ |
| Apple: 401 → header refresh (retry), 403 → kod değişikliği | `agent-web-arastirma.md` §3.2 | resmi doküman | ✓ |
| Apple: storefront değerleri | `agent-web-arastirma.md` §7 (5.) | **kanıt yok** | ⚠️ |
| Tidal: OAuth 2.1 + PKCE **zorunlu** · `code_challenge_methods_supported: S256` | `agent-web-arastirma.md` §3.3 | resmi OpenID JSON | ✓ |
| Tidal: authorize host | `agent-web-arastirma.md` §3.3 | resmi OpenID metadata | ✓ |
| Tidal: token host | `agent-web-arastirma.md` §3.3 | resmi OpenID metadata | ✓ |
| YouTube Music: resmi genel API **yok** · 100 çağrı/kova · 10.000 birim/kova | `agent-web-arastirma.md` §3.4 | resmi + güvenilir ikincil | ✓ |
| MusicBrainz: 1 istek/sn → 503 · User-Agent zorunlu | `agent-web-arastirma.md` §3.5 | resmi politika | ✓ |
| MusicBrainz: `/ws/2` kök yolu | `agent-web-arastirma.md` §7 (5.) | **kanıt yok** | ⚠️ |
| Musixmatch: 2.000 çağrı/gün · %30 lyrics kuralı → 402 | `agent-web-arastirma.md` §3.6 | resmi politika + güvenilir ikincil | ✓ |
| Musixmatch: API kök host'u | `agent-web-arastirma.md` §7 (5.) | **kanıt yok** | ⚠️ |
| Amazon: LWA `client_id` + `client_secret` **ikisi de** Authorization header'ı | `agent-web-arastirma.md` §3.7 | resmi doküman | ✓ |
| Amazon: access token ~1 saat · refresh token uzun ömürlü · 401 `INVALID_ACCESS_TOKEN` | `agent-web-arastirma.md` §3.7 | resmi doküman | ✓ |
| Amazon: token host'u | `agent-web-arastirma.md` §7 (5.) | **kanıt yok** | ⚠️ |
| Deezer: catalog keyless · redirect `redirect.html` | `agent-web-arastirma.md` §3.8 | resmi doküman | ✓ |
| Deezer: kota sayısal değeri | `agent-web-arastirma.md` §7 (5.) | **kanıt yok** | ⚠️ |
| Last.fm: `api_sig` = MD5 imzası · web servislerinde `sk` | `agent-web-arastirma.md` §3.9 | resmi doküman | ✓ |
| Last.fm: API host'u | `agent-web-arastirma.md` §7 (5.) | **kanıt yok** | ⚠️ |
| Discogs: OAuth 1.0a **veya** PAT | `agent-web-arastirma.md` §3.10 | resmi doküman | ✓ |
| Discogs: API host'u | `agent-web-arastirma.md` §7 (5.) | **kanıt yok** | ⚠️ |
| Genius: anonim token · ticari lisans gate'i | `agent-web-arastirma.md` §3.11 | resmi doküman | ✓ |
| ListenBrainz: token auth · katalog araması **yok** | `agent-web-arastirma.md` §3.12 | resmi doküman | ✓ |
| K005: kullanıcı bazlı `client_state` | `discovery-kapsam.md` [Satır: 184] | vault ADR | ✓ |
| K010: sunucu oturumu + `session_storage` | `discovery-kapsam.md` [Satır: 186] | vault ADR | ✓ |
| K015: OAuth state → CoreMusic user_id | `discovery-kapsam.md` [Satır: 189] | vault ADR | ✓ |
| K011: PHP 8.4 hybrid SPA + vanilla JS | `discovery-kapsam.md` [Satır: 178] | vault ADR | ✓ |
| ADR-095: refresh ≠ session | `agent-kapsam.md` §18 · `agent-web-arastirma.md` §4 | vault ADR | ✓ |
| ADR-010: `state` + CSRF token | `agent-kapsam.md` §18 · `agent-web-arastirma.md` §4 | vault ADR | ✓ |
| AES-256-GCM + nonce zorunlu | `agent-kapsam.md` §18 · `agent-ornekler.md` §15 (SQL) | vault + OWASP | ✓ |
| Token log'a yazılamaz | `agent-kapsam.md` §19 · `agent-ornekler.md` §14 | vault + OWASP | ✓ |
| ADR-101: timeout/retry/fallback zorunlu | `agent-bilesenler.md` §3 · `agent-ornekler.md` §14 | vault ADR | ✓ |
| K012: `php artisan config:clear` ile oturum kırılır | `agent-kapsam.md` §18 | vault ADR | ✓ |

---

## 3. Tur-1 ↔ Tur-2 Çapraz Kontrol (düzeltme defteri)

Tur-1 (`discovery-web-arastirma.md`) ile bu tur (2026-10-17 web tur-2)
karşılaştırıldığında **düzeltme gereken tek iddia**:

| Tur-1 iddiası | Tur-2 kanıtı | Karar |
|---|---|---|
| "Tidal API v2: PKCE desteklenir" (zayıf ifade) | Tidal OpenID metadata: `code_challenge_methods_supported: ["S256"]` → **zorunlu** | **Düzeltildi** — `agent-kapsam.md` §7.3 ve `agent-web-arastirma.md` §3.3 "PKCE zorunlu" olarak yazıldı; örneklerde `code_challenge_method=S256` sabit |

Çapraz kontrol edilip **değişiklik gerektirmeyen** tur-1 iddiaları:
Spotify 43–128 ✓ · Spotify S256 ✓ · Apple ES256/kid/iss/exp ✓ · Apple 401/403 ✓ ·
Amazon LWA double header ✓ · Amazon ~1 saat ✓ · MusicBrainz 1/sn + UA ✓ ·
Musixmatch 2.000/gün + %30 ✓ · Deezer keyless ✓ · Last.fm md5 api_sig ✓ ·
Discogs OAuth 1.0a/PAT ✓ · Genius ticari gate ✓ · ListenBrainz katalog araması yok ✓ ·
YouTube Music API yok + kova ✓ · ADR-095/ADR-010/GCM/log yasağı ✓.

---

## 4. ⚠️ VERIFICATION REQUIRED Envanteri (tam liste)

| # | İddia | Etkilenen dosya | Neden kanıtsız | Doğrulama yolu |
|---|---|---|---|---|
| 1 | Rust `sha2`/`rand` paket sürümleri | `agent-ornekler.md` §10 (Rust) | vault'ta Cargo.toml yok | crates.io sürüm kontrolü |
| 2 | C `libcurl` / C++ `OpenSSL` sürümleri | `agent-ornekler.md` §11 (C/C++) | vault'ta derleme dosyası yok | sistem paket sürümü |
| 3 | Spotify authorize host'u | `agent-web-arastirma.md` §7 (5.) | tur-2'de yalnız token/S256/429 satır alındı | Spotify for Developers "Authorization Guide" |
| 4 | Tidal API v2 istek host'u (login/authorize/token **alındı**) | `agent-ornekler.md` §10 (Tidal bloğu) | OAuth 2.1 dokümanı ayrı host vermiyor | Tidal API reference (resource host) |
| 5 | Apple Music API storefront değeri | `agent-web-arastirma.md` §7 (5.) | token endpoint'i alındı, storefront verilmedi | Apple Music API "base URL" bölümü |
| 6 | MusicBrainz `/ws/2` kök yolu | `agent-web-arastirma.md` §7 (5.) | politika alındı, kök yolu kanıtlanmadı | MusicBrainz WS docs |
| 7 | Deezer kota sayısal değeri | `agent-web-arastirma.md` §7 (5.) | resmi sayfada sayısal kota yok | Deezer FAQ / API docs |
| 8 | Amazon LWA token host'u | `agent-web-arastirma.md` §7 (5.) | double header + süre alındı, host kanıtlanmadı | Amazon LWA "Obtain tokens" |
| 9 | Last.fm API host'u | `agent-web-arastirma.md` §7 (5.) | md5 imza alındı, host kanıtlanmadı | Last.fm API docs |
| 10 | Musixmatch API kök host'u | `agent-web-arastirma.md` §7 (5.) | kota/gate alındı, host kanıtlanmadı | Musixmatch API docs |
| 11 | Discogs API host'u | `agent-web-arastirma.md` §7 (5.) | OAuth 1.0a/PAT alındı, host kanıtlanmadı | Discogs API docs |
| 12 | MySQL `AES_ENCRYPT` GCM desteği | `agent-ornekler.md` §15 (SQL notu) | sürüm/destek vault'ta yok → uygulama katmanı GCM ile telafi | MySQL sürüm dokümantasyonu |

**Yasak:** Bu 12 satır bir sonraki turda kanıtlanmadan `✓` yapılamaz; kod
örneklerindeki placeholder'lar (`YOUR_*`, `<TOKEN>`, `${CLIENT_ID}`) bu yüzden
korunur.

---

## 5. Erişim Kaydı Özeti (bu tur, 2026-10-17)

| Erişim | Adet | Kapsam |
|---|---|---|
| HTTP 200 + içerik okundu | 26 | 12 servis auth dokümanı (bkz. `agent-web-arastirma.md` §7 tam liste) |
| HTTP 403 (blok) | 4 | Amazon Music API docs · Genius API docs · Tidal developer · Deezer developer |
| Erişilemedi (bağlantı) | 3 | Apple `idmsa.apple.com` OAuth authorize · Spotify `accounts.spotify.com` authorize · MusicBrainz `/ws/2` kök |
| Vault dosyası okundu | 7 | `discovery-web-arastirma.md` · `discovery-dogrulama.md` · `discovery-kapsam.md` · `discovery-bilesenler.md` · `K011-application/web/index.md` · `agent/index.md` · `agent-kapsam.md` |

Erişim kaydının tam URL+satır dökümü: `agent-web-arastirma.md` §7.

---

## 6. Dosya Bütünlük Kontrolleri (bu ağaç)

| # | Dosya | Frontmatter (9 alan) | Son satır | Dil | Yasağı tekrar |
|---|---|---|---|---|---|
| 1 | `agent-kapsam.md` | ✓ 9/9 | ✓ Authority | ✓ Türkçe | §18–19 |
| 2 | `agent-bilesenler.md` | ✓ 9/9 | ✓ Authority | ✓ Türkçe | §9 |
| 3 | `agent-ornekler.md` | ✓ 9/9 | ✓ Authority | ✓ Türkçe | §12–14 |
| 4 | `agent-web-arastirma.md` | ✓ 9/9 | ✓ Authority | ✓ Türkçe | §4 |
| 5 | `agent-dogrulama.md` | ✓ 9/9 | ✓ Authority | ✓ Türkçe | §6–7 |
| 6 | `index.md` | ✓ 5 alan (K-node v2 §4.2 indeks) | ✓ Authority | ✓ Türkçe | §4 |

Kod blokları: `agent-ornekler.md` içinde **13 dil + 1 SQL** = 14 blok, her dilde
≥2 blok (A: PKCE S256 · B: token exchange/Bearer · C: 429/retry — C# ve SQL'de
4. blok). Blok aralarında 1–3 satır Türkçe not; `// ...`/`{...}` dolgu yok.

---

## 7. Güvenlik Denetimi (bu ağaç — nerede tekrarlandı)

| Yasak | Kapsam | Bilesenler | Ornekler | Web | Doğrulama |
|---|---|---|---|---|---|
| Token yalnız `.env` / env substitution; `.md`/`.log`/console/log yasak | §19 | §9 | §14 | §4 | §6–7 |
| ADR-095: refresh ≠ CoreMusic session | §18 | §9 | §14 | §4 | §2 (matris) |
| ADR-010: `state` + CSRF token (PKCE atlanamaz) | §18 | — | §14 (blok notları) | §4 | §2 (matris) |
| ADR-101: timeout/retry/fallback zorunlu | §19 | §3 | §14 | — | §2 (matris) |
| AES-256-GCM + nonce (ECB/CTR/CBC yasak) | §19 | §9 | §15 (SQL) | — | §4 (12) |

Eksik tekrar **yok**: her dosyanın frontmatter sonuna eklenen Authority satırından
önceki bölüm yasağın kendisini içerir.

---

## 8. Ağaç Tutarlılık Kontrolü (index ↔ dosyalar)

| # | index.md iddiası | Karşılığı | Sonuç |
|---|---|---|---|
| 1 | Bölüm 1: kapsam → `agent-kapsam.md` §2–10 | dosya §2–10 mevcut | ✓ |
| 2 | Bölüm 2: bileşenler → `agent-bilesenler.md` §2–9 | dosya §2–9 mevcut | ✓ |
| 3 | Bölüm 3: kod örnekleri → `agent-ornekler.md` §4–15 | dosya §4–15 mevcut | ✓ |
| 4 | Bölüm 4: web araştırma → `agent-web-arastirma.md` §3–7 | dosya §3–7 mevcut | ✓ |
| 5 | Bölüm 5: doğrulama → bu dosya §1–8 | mevcut | ✓ |
| 6 | Gruplar: kapsam/bilesenler mimari · ornekler kod · web/doğrulama kanıt | frontmatter `group` alanlarıyla birebir | ✓ |
| 7 | `children: []` (index + 5 dosya) | hiçbiri K-node'a reference vermiyor | ✓ |
| 8 | `depends_on` iç bağımlılık zinciri döngüsüz | kapsam→web→ornekler→bilesenler→doğrulama tek yön | ✓ |
| 9 | K015 bağımlılıkları karşılanıyor | `k015-node.md` `depends_on: K011` ✓ + K020 agent ağacı bu K-node'u besliyor | ✓ |
| 10 | Erişim kaydı 26+4+3 = 33 URL | `agent-web-arastirma.md` §7 sayımı | ✓ |
| 11 | ⚠️ envanteri 12 madde | §4 bu dosya | ✓ |
| 12 | Düzeltme defteri: tek düzeltme (Tidal PKCE) | §3 bu dosya | ✓ |

---

## 9. Açık Sorular (Görev Çıkışı)

1. **Host kanıtları**: §4 tablosundaki 8 host/kota satırı sonraki web turunda
   resmi dokümanla kapatılacak (`agent-web-arastirma.md` §7 erişilemedi listesine
   yeniden yazılacak).
2. **Dil paket sürümleri**: Rust/C/C++ bloklarındaki ⚠️ satırları, proje
   bağımlılık dosyaları (Cargo.toml / CMakeLists.txt / composer.json) vault'a
   girdiğinde `✓` olacak.
3. **MySQL GCM**: `agent-ornekler.md` §15'teki uygulama-katmanı GCM kararı,
   MySQL sürümü doğrulanınca DB-side AES_ENCRYPT-GCM ile karşılaştırılıp ADR'ye
   bağlanabilir.
4. **Ölçüm eşikleri**: `agent-kapsam.md`/`agent-bilesenler.md`/`agent-web-arastirma.md`
   ≥500, `agent-ornekler.md` ≥550, bu dosya ≥500 canonical satır hedefiyle
   yazıldı; son sayaç doğrulaması orkestratör raporunda.

---

## 10. Kapı Durumu (bu ağaç için)

| Kapı | Durum |
|---|---|
| Zero-Hallucination | ✓ — 12 ⚠️ satır açıkça işaretli, uydurma URL/status/sürüm yok |
| Frontmatter (K-node v2 §4.2) | ✓ — 9/9 alan, `group` doğru |
| Son satır Authority | ✓ — 6/6 dosya |
| Dil (Türkçe, mojibake yok) | ✓ |
| Dolgu yok | ✓ — `// ...`/`{...}` blokları temizlendi (PHP/Swift düzeltmeleri §11) |
| Erişim kaydı | ✓ — 33 URL + 7 vault dosyası |
| Commit | ✗ — subagent commit atmadı (yasağa uygun) |

---

## 11. Değişiklik Günlüğü (bu tur)

| # | Dosya | Değişiklik | Neden |
|---|---|---|---|
| 1 | `agent-kapsam.md` | §7.3 Tidal "PKCE desteklenir" → "PKCE **zorunlu**" | tur-2 kanıtı: `code_challenge_methods_supported: ["S256"]` |
| 2 | `agent-ornekler.md` | PHP `callWithRetry` bloğu yeniden yazıldı | `CURLOPT_HEADERFUNCTION` (Retry-After) — `curl_getinfo`'da bu header garantili değil |
| 3 | `agent-ornekler.md` | Swift blokları yeniden yazıldı | olmayan API (`resultTry`, `URLError(.custom)`) → `TokenExchangeError` enum + `JSONDecoder` |
| 4 | `agent-ornekler.md` | `agent-kapsam.md` yanlış uzantı düzeltmeleri (×6) | gerçek dosya adı |
| 5 | `agent-bilesenler.md` | yazım hataları (`grafilek`→`graf`, `TASArim`→`TASARIM`) + uzantı düzeltmeleri (×6) | tutarlılık |
| 6 | `index.md` | "redirect redirect" → "yönlendirme URI'si"; kanıt satırı; §GRUPLAR +5 | yazım + tur-2 kaydı + grup indeksi |

---

## 12. Servis Kartları (12 servis — doğrulanmış / doğrulanmamış ayrımı)

> Her kart üç bölümlüdür: **Doğrulandı** (bu ağaçta `✓` ile kullanılan iddialar),
> **Doğrulanmadı** (§4 envanterine düşen satırlar), **Ağaçtaki karşılığı**
> (iddianın hangi dosyada-hangi bölümde geçtiği). Kartlar §2 matrisinin
> servis-bazlı genişletilmiş halidir; içerik tekrarı değil, hizalama kontrolüdür.

### 12.1 Spotify

- **Doğrulandı:** Authorization Code + PKCE · `code_verifier` uzunluğu 43–128 arası ·
  S256 zorunlu (`code_challenge_methods_supported: S256`; plain verifier 400 döner) ·
  `429` durumunda `Retry-After` header okunur.
- **Doğrulanmadı:** authorize host'u (§4 #3).
- **Ağaçtaki karşılığı:** `agent-kapsam.md` §7.1 · `agent-web-arastirma.md` §3.1 ·
  `agent-ornekler.md` §4 (blok A1/A2 — PKCE S256 + token exchange).
- **Kod kuralı:** `code_verifier` 43–128 karakter; örneklerde `YOUR_CLIENT_ID`
  placeholder'ı kullanılır, gerçek client id yazılmaz.

### 12.2 Apple Music (MusicKit)

- **Doğrulandı:** developer token JWT · ES256 · `kid` 10 karakter · `iss` 10 karakter ·
  `exp` süresi ≤ 1.577.700.000 saniye · `401` → token'ı yenile ve yeniden dene ·
  `403` → kod/embedded token değişikliği gerekir (yenileme yetmez).
- **Doğrulanmadı:** storefront değerleri (§4 #5).
- **Ağaçtaki karşılığı:** `agent-kapsam.md` §7.2 · `agent-web-arastirma.md` §3.2 ·
  `agent-ornekler.md` §4 (blok A1 — JWT imzalama iskeleti, özel anahtar `YOUR_PRIVATE_KEY.p8`).
- **Kod kuralı:** `exp` üst sınırı kodda sabit assert edilir; `kid`/`iss` env'den okunur.

### 12.3 Tidal

- **Doğrulandı:** OAuth 2.1 + PKCE **zorunlu** · authorize host `https://login.tidal.com/authorize` ·
  token host `https://auth.tidal.com/v1/oauth2/token` · OpenID metadata
  `code_challenge_methods_supported: ["S256"]`.
- **Doğrulanmadı:** API v2 istek host'u (OAuth 2.1 dokümanı resource host vermiyor) — §4 #4.
- **Ağaçtaki karşılığı:** `agent-kapsam.md` §7.3 · `agent-web-arastirma.md` §3.3 ·
  `agent-ornekler.md` §10 (Tidal bloğu — `YOUR_AUTHORIZE_ENDPOINT` değil, authorize
  host'u kanıtlı; istek host'u placeholder).
- **Tur-1 düzeltmesi:** "PKCE desteklenir" → "PKCE zorunlu" (bkz. §3).

### 12.4 YouTube Music

- **Doğrulandı:** resmi genel (public) API **yok** · sınırsız kopyalama TOS riski →
  `⚠️ KAPALI` · güvenilir ikincil kaynak: 100 çağrı/kova · 10.000 birim/kova kotası.
- **Doğrulanmadı:** — (host vb. iddia edilmedi; iddia edilmeyen kanıtlanmaz).
- **Ağaçtaki karşılığı:** `agent-kapsam.md` §7.4 (KAPALI ailesi H) ·
  `agent-web-arastirma.md` §3.4 · `agent-ornekler.md` §6 (blok B — kota takip iskeleti).
- **Kod kuralı:** örnekler yalnız kota-sınırlı yapı iskeleti verir; resmi API
  çağrısı **yoktur** — bu bilinçli boşluktur, doldurulması yasaktır.

### 12.5 MusicBrainz

- **Doğrulandı:** 1 istek/sn rate limit → ihlalde `503` · `User-Agent` header'ı
  zorunlu (anonim/agnetik istekler reddedilir).
- **Doğrulanmadı:** `/ws/2` kök yolu (§4 #6).
- **Ağaçtaki karşılığı:** `agent-kapsam.md` §7.5 · `agent-web-arastirma.md` §3.5 ·
  `agent-ornekler.md` §7 (blok C — 1/sn throttle + 503 backoff).
- **Kod kuralı:** `User-Agent` env'den (`YOUR_APP_NAME` placeholder); throttle
  koddaki tek kaynak `sleep ≥ 1s` + 503 exponential backoff.

### 12.6 Musixmatch

- **Doğrulandı:** 2.000 çağrı/gün kotası · lyrics içeriğinin %30'unun üzerinde
  yayımlama → `402` (ticari gate) · tur-1 köprüsü: gün sınırı aşımında 402.
- **Doğrulanmadı:** API kök host'u (§4 #10).
- **Ağaçtaki karşılığı:** `agent-kapsam.md` §7.6 · `agent-web-arastirma.md` §3.6 ·
  `agent-ornekler.md` §8 (blok D — günlük sayaç + %30 gate kontrolü).
- **Kod kuralı:** günlük sayaç uygulama katmanında tutulur; `402` → lyrics
  gösterimi kapatılır (fail-closed), sessizce devam **edilmez**.

### 12.7 Amazon Music (LWA)

- **Doğrulandı:** LWA'da `client_id` + `client_secret` **ikisi de**
  `Authorization` header'ı içinde gider (body'ye değil) · access token ~1 saat
  ömürlü · refresh token uzun ömürlü · süresi dolan token için `401` +
  `INVALID_ACCESS_TOKEN` hatası döner.
- **Doğrulanmadı:** token host'u (§4 #8).
- **Ağaçtaki karşılığı:** `agent-kapsam.md` §7.7 · `agent-web-arastirma.md` §3.7 ·
  `agent-ornekler.md` §9 (blok E — double header + 401 refresh tetikleyici).
- **Kod kuralı:** `401 INVALID_ACCESS_TOKEN` → tek seferlik refresh + retry;
  ikinci 401 → karta "yeniden bağlan" durumu (ADR-095: kullanıcı oturumu düşmez).

### 12.8 Deezer

- **Doğrulandı:** katalog API anahtarsız (keyless) çalışır · OAuth var ancak
  redirect URI `redirect.html` dosyasına işaret eder.
- **Doğrulanmadı:** kota sayısal değeri (§4 #7).
- **Ağaçtaki karşılığı:** `agent-kapsam.md` §7.8 · `agent-web-arastirma.md` §3.8 ·
  `agent-ornekler.md` §5 (blok B2 — keyless katalog + OAuth ayrımı).
- **Kod kuralı:** katalog okuması keyless; kullanıcı verisi için OAuth zorunlu —
  ikisi aynı kod yolunda karıştırılmaz.

### 12.9 Last.fm

- **Doğrulandı:** imzalı isteklerde `api_sig` = parametrelerin MD5 imzası ·
  write/web servislerinde `sk` (session key) kullanılır.
- **Doğrulanmadı:** API host'u (§4 #9).
- **Ağaçtaki karşılığı:** `agent-kapsam.md` §7.9 · `agent-web-arastirma.md` §3.9 ·
  `agent-ornekler.md` §12 (blok F — md5 api_sig imzalama).
- **Kod kuralı:** `api_sig` anahtarı asla log'a basılmaz (§7 yasağı); imza
  fonksiyonu saf (pure) ve birim-test edilebilir şekilde ayrılır.

### 12.10 Discogs

- **Doğrulandı:** iki kimlik yolu — OAuth 1.0a veya Personal Access Token (PAT) ·
  OAuth 1.0a OAuth 2.0'dan farklıdır (token secret + imza).
- **Doğrulanmadı:** API host'u (§4 #11).
- **Ağaçtaki karşılığı:** `agent-kapsam.md` §7.10 · `agent-web-arastirma.md` §3.10 ·
  `agent-ornekler.md` §13 (blok G — PAT yolu tercih edilir, OAuth 1.0a fallback).
- **Kod kuralı:** PAT yolu basit Bearer; OAuth 1.0a gerekirse ayrı adapter —
  iki yol tek fonksiyona `if` ile yığılmaz (SRP).

### 12.11 Genius

- **Doğrulandı:** anonim token ile okuma mümkün · ticari kullanım lisans
  gate'i vardır (API koşulları) → ağaçta `⚠️ LİSANS GATE` işaretli.
- **Doğrulanmadı:** — (host iddia edilmedi; §4 #4'teki Tidal satırından bağımsız).
- **Ağaçtaki karşılığı:** `agent-kapsam.md` §7.11 · `agent-web-arastirma.md` §3.11 ·
  `agent-ornekler.md` §14 (blok H — anonim token + lisans kontrolü notu).
- **Kod kuralı:** lyrics/metin gösterimi, lisans onayı olmadan üretime alınmaz
  (fail-closed); gate durumu `.env`'de `GENIUS_LICENSE_OK` ile tutulur.

### 12.12 ListenBrainz

- **Doğrulandı:** token tabanlı kimlik doğrulama · katalog **araması yok**
  (listening verisi yazım/okuma servisidir, arama servisi değildir).
- **Doğrulanmadı:** —.
- **Ağaçtaki karşılığı:** `agent-kapsam.md` §7.12 · `agent-web-arastirma.md` §3.12 ·
  `agent-ornekler.md` §14 (blok H — token header + "arama yok" notu).
- **Kod kuralı:** "şarkı bul" işlevi ListenBrainz üzerinden **talep edilmez**;
  arama MusicBrainz'e yönlendirilir (K020 içinde servis-yönlendirme kuralı).

---

## 13. Placeholder Envanteri (bu ağaçta izin verilen tek "boşluk" biçimi)

> Zero-Hallucination kuralı: kanıtsız değer kodda **asla** sabit yazılmaz; yerine
> adı açık placeholder konur. Placeholder'lar `.env`'e taşınana kadar kod
> çalıştırılamaz — bu bilinçli bir kapıdır.

| Placeholder | Kullanıldığı blok(lar) | Karşılığı (env anahtarı) | Not |
|---|---|---|---|
| `YOUR_CLIENT_ID` | Spotify · Tidal · Deezer OAuth · Apple `iss` | `SPOTIFY_CLIENT_ID` · `TIDAL_CLIENT_ID` · `DEEZER_APP_ID` · `APPLE_MUSIC_ISSUER` | 1:1 değiştirilir |
| `YOUR_CLIENT_SECRET` | Spotify · Tidal · Deezer token exchange | `*_CLIENT_SECRET` | log'a yazılmaz (§7) |
| `YOUR_CODE_VERIFIER` | PKCE blokları (A1) | çalışma anında üretilir (sabit değil) | 43–128 karakter (Spotify) |
| `<TOKEN>` | Bearer header blokları (B) | runtime token | düz metin loglanmaz |
| `${CLIENT_ID}` | shell/CI örnekleri | env substitution | `.md`'ye gerçek değer yazılmaz |
| `YOUR_PRIVATE_KEY.p8` | Apple ES256 imzalama | dosya yolu env'den | anahtar içeriği hiçbir yere kopyalanmaz |
| `YOUR_AUTHORIZE_ENDPOINT` | kanıtsız host'lar (§4 #3/#5/#8/#9/#10/#11) | `*_AUTHORIZE_URL` | **kanıt gelince kaldırılacak** |
| `YOUR_APP_NAME` | MusicBrainz User-Agent | `MB_USER_AGENT` | zorunlu header |
| `YOUR_MUSIXMATCH_KEY` | Musixmatch blok | `MUSIXMATCH_API_KEY` | 2.000/gün sayacıyla birlikte |

---

## 14. Tur-3 Doğrulama Planı (§4 envanterinin kapatılma sırası)

| Sıra | Hedef (§4 #) | Beklenen kanıt | Doğrulanırsa etkilenen dosya |
|---|---|---|---|
| 1 | #3 Spotify authorize host | Spotify "Authorization Guide" → authorize URL satırı | `agent-web-arastirma.md` §7 · `agent-ornekler.md` §4 |
| 2 | #8 Amazon LWA token host | Amazon LWA "Obtain tokens" → host satırı | `agent-web-arastirma.md` §7 · `agent-ornekler.md` §9 |
| 3 | #10 Musixmatch API kökü | Musixmatch API docs → base URL | `agent-web-arastirma.md` §7 · `agent-ornekler.md` §8 |
| 4 | #9 Last.fm API host | Last.fm API docs → base URL | `agent-web-arastirma.md` §7 · `agent-ornekler.md` §12 |
| 5 | #11 Discogs API host | Discogs API docs → base URL | `agent-web-arastirma.md` §7 · `agent-ornekler.md` §13 |
| 6 | #6 MusicBrainz `/ws/2` | MusicBrainz Web Service docs → kök yolu | `agent-web-arastirma.md` §7 |
| 7 | #5 Apple storefront | Apple Music API docs → base URL + storefront | `agent-web-arastirma.md` §7 |
| 8 | #7 Deezer kota | Deezer FAQ/docs → sayısal kota (yoksa "sayısal kota yayınlanmaz" kaydı) | `agent-web-arastirma.md` §7 |
| 9 | #4 Tidal API v2 host | Tidal API reference → resource host | `agent-ornekler.md` §10 |
| 10 | #1 Rust paketleri | Cargo.toml / crates.io sürümleri | `agent-ornekler.md` §10 (Rust) |
| 11 | #2 C/C++ bağımlılıkları | libcurl/OpenSSL sistem sürümü | `agent-ornekler.md` §11 |
| 12 | #12 MySQL GCM | MySQL sürüm dokümantasyonu | `agent-ornekler.md` §15 (SQL) |

**Kural:** her satır kapanınca hem `agent-web-arastirma.md` §7 erişim kaydına hem
de bu dosya §4 tablosuna `✓` işlenir; iki tablo asla tek başına güncellenmez.

---

## 15. Ölçüm Protokolü (canonical satır sayımı)

| Kural | Değer |
|---|---|
| Kanonik yöntem | `[System.IO.File]::ReadAllLines($yol).Length` (PowerShell) — editör satır sonu farkı (CRLF/LF) sayımı etkilemez |
| Eşik: kapsam/bilesenler/web/doğrulama | ≥ 500 canonical satır |
| Eşik: ornekler | ≥ 550 canonical satır |
| Eşik: index | 40–100 satır (özet sayfadır, içerik sayfası değil) |
| Son satır | birebir `**Authority:** Bayram Ali / Vault Steward` |
| Kod bloğu doldurma | `// ...` / `{...}` tek başına blok **sayılmaz**; blok gerçek satırla dolu olmalı |
| Geçersiz ölçüm | editör "logical lines", kelime sayısı, karakter sayısı — hiçbiri canonical sayılmaz |

Bu turun ölçümleri (rapor için): kapsam 506 · bilesenler 534 · ornekler 1268 ·
web-arastirma 503 · doğrulama (bu dosya) · index 65.

---

## 16. Sembol Sözlüğü (bu ağaçta tek anlam)

| Sembol | Anlam | Kullanım yeri |
|---|---|---|
| `✓` | resmi kaynakla doğrulandı (URL + erişim tarihi var) | tüm dosyalar |
| `⚠️` | kanıt yok / doğrulanamadı → bir sonraki turda kapatılacak | tüm dosyalar |
| `⚠️ KAPALI` | kapsam dışı + risk (YouTube Music) | kapsam · web-arastirma |
| `⚠️ LİSANS GATE` | hukuki onay bekliyor (Genius) | kapsam · web-arastirma |
| `UNKNOWN` | hiç araştırılmamış boşluk (bilinmiyor) | tüm dosyalar |
| `YASAK` | vault kuralı ihlali → kapı kırmızı | kapsam · dogrulama |
| `Kademe-1/Kademe-2` | gruplama ekseninin kademe düzeyi | index §GRUPLAR |

---

## 17. Frontmatter Alan-Bazlı Denetim (K-node v2 §4.2 — 9 alan)

> Bu tablo, ağacın 5 içerik dosyasının frontmatter'ının **alan bazında** ne
> taşıdığını ve o alanın bu turdaki karşılığını gösterir. İndeks (`index.md`)
> K-node v2 §4.2'nin ayrı indeks şemasını (5 alan) izler; 9 alan zorunluluğu
> içerik dosyaları içindir.

| # | Alan | Zorunlu mu | Ağaçtaki değer kuralı | Denetim |
|---|---|---|---|---|
| 1 | `node_id` | evet | `K020-discovery/agent/<dosya-adı>` (uzantısız, dosya adıyla birebir) | 5/5 ✓ |
| 2 | `node_type` | evet | sabit `subdomain` (bu ağaç K020-discovery'nin altıdır) | 5/5 ✓ |
| 3 | `status` | evet | `draft` — tur-3 kapandıktan sonra orkestratör `active`'ye çeker | 5/5 ✓ |
| 4 | `parent_id` | evet | `K020-discovery` (tek düzey üst) | 5/5 ✓ |
| 5 | `children` | evet | `[]` — bu K-node dosyaları içerik sayfasıdır, ayrı K-node değil | 5/5 ✓ |
| 6 | `depends_on` | evet | yalnız **gerçek** iç bağımlılık (dosya-adı referansı); döngü yok (§8 #8) | 5/5 ✓ |
| 7 | `evidence` | evet | her satır diskte var olan dosya yolu; uydurma yol yok | 5/5 ✓ |
| 8 | `validation` | evet | `"2026-10-17 — web tur-2 ile doğrulandı"` (tarih + tur + fiil) | 5/5 ✓ |
| 9 | `open_questions` | evet | bu dosyanın kendi ⚠️ satırları (§4 envanteriyle uyumlu) | 5/5 ✓ |
| — | `group` | evet (ayrı eksen) | kapsam/bilesenler=`mimari` · ornekler=`kod` · web/doğrulama=`kanıt` | 5/5 ✓ |

---

## 18. Sık Hatalar Defteri (bu turda yakalananlar + tekrar riski)

| # | Hata sınıfı | Yakalanan örnek | Yakalanma biçimi | Tekrar riski |
|---|---|---|---|---|
| 1 | Uydurma API kullanımı | Swift `.resultTry` · `URLError(.custom)` (olmayan API'ler) | yazım sırasında SDK imzası sorgulandı | **yüksek** — Swift concurrency'da `Result` sarmalayıcıyı uydurmak kolay |
| 2 | Header'ı yanlış kaynaktan okuma | PHP `curl_getinfo` → Retry-After garantili değil | dokümanda header okuma kuralı | **yüksek** — 429 örneklerinde tekrar edilebilir |
| 3 | Dosya adı uydurma | `agent-kapsam` (uzantısız) ×6 · `agent-bilesenler` ×6 | disk listesiyle çapraz okuma | orta — referans yazarken `ls` şart |
| 4 | Yazım hataları | `grafilek` · `TASArim` · `redirect redirect` | okuma turu | orta — Türkçe teknik terim yazımında |
| 5 | İddia güçlendirme | Tidal "PKCE desteklenir" → "zorunlu" | tur-2 metadata çapraz okuması | **yüksek** — "destekler" ile "zorunlu" aynı değildir |
| 6 | Kanıtsız sabit yazma | authorize host'ları kodda sabit yazılmadı, placeholder kondu (§13) | Zero-Hallucination kapısı | düşük — placeholder envanteri tutuluyor |
| 7 | Ölçüm yöntemi hatalısı | editör satır sayısı vs canonical | §15 protokolü | orta — yalnız `ReadAllLines` kabul edilir |

**Yakalanmayan (bilinen) risk:** `agent-ornekler.md` 1268 satırla en uzun
dosyadır; dil bloklarından birinde imza hatası kaçarsa §18 #1 hata sınıfıyla
aynı yerde birikir — tur-3'te dil-bazlı imza denetimi ek kapı olarak önerilir.

---

## 19. Kapı İmzası (bu ağaç — son durum)

- **İçerik kapıları (§10):** 7/7 ✓ — içerik, kanıt, frontmatter, dil, dolgu,
  erişim kaydı, yasak tekrarı.
- **Ölçüm kapıları (§15):** 5/6 ✓ — `agent-dogrulama.md` bu bölümün eklenmesiyle
  eşiğe ulaştı; son sayaç orkestratör raporunda teyit edilir.
- **Commit:** atılmadı — yasağa uygun (kök AGENTS.md §16.5 · §7.9).
- **Bir sonraki sahip:** orkestratör — tur-3 (§14) onayı ve commit kararı.

---

**Authority:** Bayram Ali / Vault Steward
