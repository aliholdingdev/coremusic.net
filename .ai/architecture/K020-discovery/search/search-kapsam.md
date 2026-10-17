---
node_id: "K020-discovery/search-kapsam"
node_type: "kapsam"
status: "draft"
parent_id: "K020-discovery/search"
children: []
depends_on:
  - "K020-discovery/discovery-master.md"
  - ".ai/architecture/index.md (S-1 route)"
  - "ADR-010 (CSRF stratejisi — medya-sağlayıcı auth gate ADR'si YOK)"
evidence:
  - "2026-10-17 kod-okuma (S-1 sahiplik çakışması, ADR-097 §1 madde 6, satır 33)"
  - "2026-10-17 web taraması (search-web-arastirma.md)"
validation: "partial"
open_questions:
  - "'arama' sahipliği: K007-data/search ↔ K020-discovery/search (S-1) + K020-discovery ↔ K020-search (ID CONFLICT) — üst karar"
  - "Sonuç dilbilgisi (result grammar) hangi node'un sözleşmesi?"
group: "mimari"
---

# K020-discovery/search — Kapsam (sorgu-keşif disiplini)

> **Rol:** Keşif sürecinin **arama boyutunu** tanımlar: kullanıcı/agent keşif sırasında
> hangi sorguları çalıştırır, sonuç kümesi nasıl dilbilgisine oturur, hangi gate'lerden
> geçer. **K020-search (sorgulama katmanı) ile KARIŞTIRILMAMALI** — bkz. §1.2.
>
> **Konum:** `.ai/architecture/K020-discovery/search/search-kapsam.md`
> **Ölçüm:** canonical `[System.IO.File]::ReadAllLines().Length`.

---

## 1. Kapsam tanımı

### 1.1 Bu disiplin NE yapar (in-scope)

| # | Alan | Açıklama |
|---|---|---|
| 1 | Keşif sorgusu tasarımı | katalog/federasyon sorgularının keşif sırasında nasıl atıldığı |
| 2 | Sonuç dilbilgisi | keşif çıktısı kümenin **uç modelle uyumlu** sözleşmesi (route zorunlu kılıyor) |
| 3 | Arama stratejisi | çok servisli taramada sıralama, kota-aware istek, fallback |
| 4 | Keşif araması gate'leri | auth (ADR-010) · kota (429/402/503) · lisans (Genius) kapıları |

### 1.2 Bu disiplin NE DEĞİL (out-of-scope) — karışıklık engeli

| Dışarıda | Nerede | Ayrım |
|---|---|---|
| **K020-search** (ürün sorgulama katmanı) | ayrı K020 node'u | bu disiplin onun **girdisini** keşfeder, kendisi **ürün API'si değildir** |
| Keşif içerik/flora analizi | `agent/` | burada **içerik değil**, **sorgu** keşfedilir |
| Şema normalizasyonu | `K007-data/schema` | arama indeksi tasarımı K007/K020-search |
| Öneri sinyalleri | `K020-recommendation` | burada yalnız **sorgu davranışı** |

**S-1 çakışması (açık soru):** `.ai/architecture/index.md` S-1, K020-discovery'den
`K020-search` dahil 6 katmanı beslemesini ister; ama bu disiplin de `search/` adını
taşıyor. **Karar verilmedi** → `⚠️ VERIFY REQUIRED` (registry `open_questions`).

---

## 2. Sorgu-keşif tipleri (keşif sırasında hangi sorgular)

| # | Tip | Ne arar | Örnek sorgu (keşif dili) | Kota etkisi |
|---|---|---|---|---|
| 1 | **Katalog araması** | sanatçı/şarkı/album | `q="artist:sakir ses"` | servis kotası (429 vb.) |
| 2 | **Kimlik doğrulama sorgusu** | MBID/ISRC gibi kalıcı ID | `mbid:…` / `isrc:…` | 1/sn (MusicBrainz) |
| 3 | **Federasyon sorgusu** | çok serviste aynı anda | eşzamanlı: Spotify+MB+DZ | eşzamanlılık üst sınırı |
| 4 | **Tohum sorgusu** | playlist seed → iz sürücüsü | `seed playlist:…` (keşif taslağı) | servis limiti |
| 5 | **Kapsam sorgusu** | "var mı?" taraması | `exists:` (Amazon beta · YTM yok) | gate (kapalıysa sorgu **atılmaz**) |
| 6 | **Kota sorgusu** | rate-limit nabzı | `X-RateLimit-Remaining` okuma | — (istek değil, başlık) |

**Kural:** Gate **kapalıysa** sorgu **çalıştırılmaz** (özellikle YTM `⚠️ KAPALI` —
sorgu bile atılmaz, kod yasağı ADR-097 §1 madde 6 (satır 33)).

---

## 3. Sonuç dilbilgisi (uç modelle uyumluluk — route zorunluluğu)

`index.md` route sözleşmesi: keşif çıktıları "uç modelle uyumlu" olmalıdır.
Sorgu-keşif için **sonuç şeması** (dilekçe — implementasyon K020-search/K020-api):

| Alan | Tip | Zorunlu | Not |
|---|---|---|---|
| `source` | enum | ✓ | servis kimliği (spotify/apple/musicbrainz/…) |
| `query` | string | ✓ | atılan sorgu (kanıt) |
| `result_id` | string | ✓ | servis içi kalıcı ID |
| `external_ids` | object | ○ | `mbid`/`isrc` varsa |
| `score` | float | ✓ | skor (keşif taslağı — ADR-097'de skorlama bölümü YOK) |
| `rate_hint` | object | ○ | `retry_after` / `remaining` (kota başlıkları) |
| `gate_state` | enum | ✓ | `open` / `blocked`(gate onayı bekliyor) / `closed`(YTM) |
| `evidence_url` | string | ✓ | bulgu URL'si (zero-hallucination) |

**Kural:** `gate_state ≠ open` ise kayıt **kod girdisi değil**, yalnız **bulgu**dur.

---

## 4. Sorgu-keşif stratejisi (sıralı akış)

| # | Adım | Ne | Gate |
|---|---|---|---|
| 1 | Kapı kontrolü | auth gate onayı + YTM kapalı mı? | kapalıysa **DUR** |
| 2 | Tohum | keşif taslağı: playlist seed / çekirdek | — |
| 3 | Katalog sorgusu | en ucuz açık servisten başla (MB 1/sn, Deezer anonim) | kota sayacı |
| 4 | Federasyon | eşzamanlı servisler (paralel, 1 subagent limiti KURAL DEĞİL — bu HTTP isteği) | eşzamanlı üst sınır |
| 5 | Skorla | sonuçları skorla + kanıt URL'si ekle | zero-hallucination |
| 6 | Devir | sonuç dilbilgisi → `K020-search` / `K020-api` keşfi | onay |

**Kota-aware kural:** 429/`Retry-After` (Spotify 30 sn) · 503 (MB) · 402 (Musixmatch)
görüldüğünde istek **ertelenir**, aynı turda **dövülmeye devam edilmez** (ADR-101
döngü kuralına benzer: değişmez kapı tekrar tekrar denemez).

---

## 5. Gate tablosu (sorgu-keşif için bağlayıcı)

| Gate | Kaynak | Sorguya etkisi |
|---|---|---|
| Auth gate | gate ADR'si **YOK** (ADR-010 = CSRF stratejisi) | kimlik gerektiren sorgu **onaysız atılmaz** |
| YTM `⚠️ KAPALI` | ADR-097 §1 madde 6 (satır 33) | YTM sorgusu **yok** |
| Vault-only token | ADR-095 §1.2 (satır 36) | sorgu loglarında token **yok**, yalnız `.env` |
| Kota erişimi | tur-2 (§2 matris) | 429/402/503 → ertele, fallback |
| Lisans | Genius (ticari) | lisanssız içerik sorgusu sonuç **işlenmez** |
| Ölçüm | registry §9.12.1 | dosya ölçümü `ReadAllLines` |

---

## 6. Kapsam dışı kapılar (burada yazılmaz)

- K020-search'in **ürün sorgu API'si** (endpoint tasarımı) → K020-search node'u.
- Arama **indeks şeması** (BCNF/indeks) → `K007-data` (S-1 sahiplik sınırı).
- Öneri **sıralama modeli** → `K020-recomenataion` (D-1).

---

## 7. Doğrulama ve İlgili dosyalar

### 7.1 Doğrulama

- [x] Frontmatter 9 alan + `group: mimari` + Authority footer.
- [x] K020-search ↔ discovery `search/` ayrımı açıkça yazıldı; çakışma `⚠️` taşındı.
- [x] Kapılar tur-1 (YTM/ADR-095) + tur-2 (kota) referanslı; auth gate ADR'si **YOK** olarak yazıldı.
- [ ] `validation: partial` — S-1 sahiplik kararı üst mercide.

### 7.2 İlgili dosyalar

| Dosya | İlişki |
|---|---|
| `K020-discovery/index.md` | route (parent) |
| `K020-discovery/discovery-master.md` | omurga sözleşmesi |
| `K020-discovery/search/search-bilesenler.md` | bileşenler |
| `K020-discovery/search/search-web-arastirma.md` | servis kanıtları (bu kapsamın girdisi) |
| `.ai/architecture/index.md` §9.12.1 | ölçüm kanonu (satır 761–764) + K020-discovery kapanış kaydı §9.14 (orchestrator) |

---

**Authority:** Bayram Ali / Vault Steward
