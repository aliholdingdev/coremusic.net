---
node_id: K020-discovery-bilesenler
node_type: subdomain
status: draft
parent_id: K020-discovery
children: []
depends_on: [K007-data, K008-domain, K021-recomenataion]
evidence:
  - ".ai/architecture/K020-discovery/discovery-master.md §2.1 (düğüm haritası: 17 md — kök 6 + search/ 6 + agent/ 6 — 2026-10-17)"
  - ".ai/architecture/K020-discovery/discovery-dogrulama.md §3 (tur-2 bileşen envanteri: 12 servis — 2026-10-17)"
  - "app-nodejs-projects/nova-search-engine/packages/* (gerçek arama yüzeyi: engine 255 · ranking 58 · search-index 127 · recommend 35 · bootstrap 49 · signals 60 — discovery-master §4)"
validation: "Her bileşen disk kanıtıyla (dosya yolu/satır); var olmayan bileşen listelenmez; ölçüler 2026-10-17 kanonik yöntem."
open_questions:
  - "⚠️ Sinyal sözleşmesi bileşeni (enum/ucu/kayıt tablosu) YOK — ayrı tasarım/ADR gerektirir (master §4)"
  - "⚠️ Keşif çıktısını K020 node'una bağlayan otomasyon scripti diskte YOK (elle süreç — UNKNOWN)"
group: mimari
---

# Discovery — Bileşenler

> K020-discovery'nin envanteri: dosya bileşenleri (17 md), süreç bileşenleri (6 aşama),
> kapı bileşenleri (8 gate) ve gerçek repo bileşenleri (nova-search-engine paketleri).

## §1. ELI15 — Bileşenler ne demek?

Bir "bileşen" = keşif makinesinin sökülebilir parçası. Üç grup var: (1) **dosyalar**
— okunacak/yazılacak md'ler; (2) **süreç adımları** — ham kaynaktan bağlayıcı karara
kadar sıralı aşamalar; (3) **kapılar** — kod yazımını engelleyen/onaylayan kontroller.
Ayrıca repo'da **gerçek** bir arama motoru paketi var (`nova-search-engine`) — keşif
bileşenlerinin tek kod yüzeyi orasıdır.

## §2. Dosya bileşenleri (17 md — 2026-10-17 disk ölçümü)

| Bileşen | Dosya | Rol | Kademe (grup) |
|---|---|---|---|
| Orchestration | `index.md` | route, çocuk kataloğu, gruplar, ölçüm | mimari |
| Master | `discovery-master.md` | omurga sözleşmesi + gate haritası (§6, 8 kapı) | mimari |
| Bağımlılık | `discovery-bagimliliklar.md` | D-1 · S-1 · D-3 · D-4 · D-5 disk kanıtlı | mimari |
| Doğrulama | `discovery-dogrulama.md` | kanıt seti (tur-1 7 kayıt + tur-2 12 servis) + kapılar | kanit |
| Web araştırma | `discovery-web-arastirma.md` | geniş servis taraması (12 servis / 18 URL / 0 hata) | kanit |
| Alt disiplin | `agent/index.md` + 5 dosya | içerik/flora keşfi, servis auth keşfi | mimari |
| Alt disiplin | `search/index.md` + 5 dosya | sorgu-keşif, sonuç dilbilgisi | mimari |

**Araç bileşenleri:** websearch/fetch (URL doğrulama), PowerShell kanonu
`[System.IO.File]::ReadAllLines().Length` (ölçüm; `Get-Content | Measure-Object -Line`
**geçersiz**), vault araması `hidden: true` (gizli `.ai/` kuralı).

## §3. Süreç bileşenleri (6 aşama — master §3)

| # | Aşama | Girdi | Çıktı | Sorumlu | Kaynak durumu |
|---|---|---|---|---|---|
| 1 | Kaynaklar | vault + repo (hidden:true) | ham kayıt kümesi | root discovery | ✓ `.ai/AGENTS.md` §7/2 |
| 2 | Tohum sorgular | katalog tohumu | tohum listesi | agent/ + search/ | ⚠️ taslak — ADR'de yok |
| 3 | Skorla | popülerlik/kanıt gücü | skorlanmış küme | agent/ | ⚠️ eşik tanımsız (uydurma sayı yazılmaz) |
| 4 | Kümeleme | ortak şema | demet şeması | agent/ + search/ | ⚠️ demet adı/eşiği tanımsız |
| 5 | Node bağlama | keşif çıktısı | kapanış kaydı | root (registry §9.x) | ✓ yalnız orkestratör yazar |
| 6 | Kod | gate + onay | kod | K020 alt katmanlar | ✓ `APPROVAL → IMPLEMENTATION` |

## §4. Kapı bileşenleri (8 gate — master §6 özeti)

| Kapı | Kaynak ADR/metin | İşlev |
|---|---|---|
| Auth/CSRF | `accepted/ADR-010` (state/PKCE) — ⚠️ "auth gate" ADR'si YOK | servis keşfi onaydan önce sunulur |
| YouTube Music KAPALI | `draft/ADR-097` §1 madde 6 | resmi API yok → kod yasağı |
| Secret/log yasağı | `accepted/ADR-095` §1.2 | token `.md`'ye yazılmaz |
| Session ayrımı | `accepted/ADR-095` §2.1 | session cookie ≠ API JWT |
| Fallback zorunluluğu | `accepted/ADR-101` §2.2 | "her zaman 200" kabul edilmez |
| Ölçüm kanonu | registry §9.12.1 | geçersiz yöntemle sayı yazılmaz |
| Şablon gate | `.ai/AGENTS.md` §13/5 (Guardrail #16) | ui-design şablonsuz yazılmaz |
| Onay | root `AGENTS.md` §1 | onaysız kod = kural ihlali |

## §5. Gerçek repo bileşenleri (nova-search-engine — master §4)

| Paket/dosya | Satır (2026-10-17) | Rol |
|---|---|---|
| `packages/search/engine.ts` | 255 | arama motoru çekirdeği |
| `packages/search/ranking.ts` | 58 | sıralama |
| `packages/search/search-index.ts` | 127 | indeks |
| `packages/search/recommend.ts` | 35 | öneri köprüsü |
| `packages/search/bootstrap.ts` | 49 | kurulum |
| `packages/workers/{rate-limiter,queue,collector-worker}.ts` | 43 / 92 / 123 | rate-limit, kuyruk, toplayıcı |
| `packages/common/signals.ts` | 60 | dil/spam skoru — **arama kalite skoru, domain sinyali DEĞİL** |
| `packages/youtube/{search,client,mapper}.ts` | 232 / 75 / 172 | YouTube arama (v3 yasak notu: `.ai/decisions/ADR-001-youtube-api-v3-yasak.md`) |

**Proje kökü kanıtı:** `.ai/projects/NovaSearchEngine/01-kurulum-mimari-ve-acik-konular.md:4`.

## §6. Servis keşfi bileşenleri (tur-2 — 12 servis)

| Servis | Keşif özeti | Durum |
|---|---|---|
| Spotify | PKCE · 429/Retry-After 30 sn rolling | ✓ verified |
| Apple Music | JWT ES256+`kid` · `exp ≤ 15777000 sn` · 429 | ✓ verified |
| YouTube Data API v3 | search.list 100/gün · 10.000 unit | ✓ verified |
| YT Music | resmi API **YOK** | 🔴 KAPALI |
| Tidal | OAuth 2.1 + zorunlu PKCE | ✓ verified |
| Deezer | genel katalog araması | ⚠️ kota UNKNOWN |
| MusicBrainz | 1 istek/sn/IP · User-Agent zorunlu · 503 | ✓ verified |
| ListenBrainz | kullanıcı token'ı · katalog araması **yok** | ✓ kapsam dışı |
| Genius | OAuth2/client token · ticari lisans kapısı | ✓ verified |
| Discogs | OAuth 1.0a + personal token · kademeli rate | ✓ verified |
| Amazon Music | kapalı beta · LWA · ~1 sa token | ✓ erişim engeli |
| Musixmatch | 2.000/gün · HTTP 402 | ✓ verified |

## §7. Kapsam dışı bileşenler (listelenmez — kanıt yok)

- Sinyal enum'ları (`SignalType` 25 / `ListenType` 7) — **silindi**, 0 isabet.
- `client/js/core/signal.js` · `manifest.json` · `seed_kit.json` · `/api/v2/signal` —
  hepsi 0 isabet (Truth Mode 2026-10-17).
- `U-L06 fragment` · `_bileşik_dinleme_demeti` · `sentinel/seq` — vault'ta yok.

## §8. Açık sorular

1. ⚠️ Sinyal yüzeyi bileşeni ayrı ADR gerektiriyor (master §4).
2. ⚠️ Aşama 3/4 eşikleri tanımsız — sayı uydurulmaz.
3. ⚠️ Keşif→node bağlama otomasyonu yok (elle süreç).

---

**Authority:** Bayram Ali / Vault Steward
