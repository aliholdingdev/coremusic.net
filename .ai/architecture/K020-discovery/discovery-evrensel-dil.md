---
node_id: K020-discovery-evrensel-dil
node_type: subdomain
status: draft
parent_id: K020-discovery
children: []
depends_on: [K007-data, K008-domain, K021-recomenataion]
evidence:
  - ".ai/architecture/K020-discovery/discovery-dogrulama.md §3 (12 servis terimi: PKCE/Retry-After/api_sig/503 — 2026-10-17 web)"
  - ".ai/architecture/K020-discovery/discovery-master.md §6 (gate terminolojisi — 2026-10-18)"
  - ".ai/architecture/K020-discovery/index.md §ELI15 (keşif / dış kaynak entegrasyon rafı — 2026-10-18)"
validation: "≥15 EN/TR terim; her terim K020 kanıt dosyasında geçer; servis-bazlı terimler resmi kaynak adına bağlanır."
open_questions:
  - "⚠️ 'Discovery' TR'de vault'ta 'keşif' ile 'detection/algılama' karışır — bu sözlük 'keşif' kullanır (tutarlılık kararı)."
  - "⚠️ 'Signal' terimi K020'de TANIMSIZ (sinyal sözleşmesi yok) — sözlüğe bağlanmadı, ayrı ADR bekliyor."
group: mimari
---

# Discovery — Evrensel Dil (EN ↔ TR)

> K020 sözlüğü: dış servis entegrasyonu, auth/kota ve keşif süreci terminolojisi.
> Kural: servis-bazlı terimler (PKCE, Retry-After…) **kaynağında aynen** kalır;
> süreç terimleri EN↔TR eşlenir.

## §1. Temel terimler (15+)

| # | English | Türkçe | Bağlam / kaynak |
|---|---|---|---|
| 1 | Discovery | Keşif | K020 disiplin adı — dış kaynak rafı |
| 2 | Integration surface | Entegrasyon yüzeyi | hangi servise nereden girilir |
| 3 | Rate limiting | Hız sınırı / istek kısıtı | MusicBrainz 1 istek/sn/IP · Musixmatch 2.000/gün |
| 4 | Rate limit / throttling | Kota aşımı / yavaşlatma | 429 Too Many Requests |
| 5 | Retry-After header | Yeniden-dene süresi başlığı | Spotify 429 → 30 sn rolling |
| 6 | Quota | Kota / kontenjan | YouTube Data API 10.000 unit; Deezer kotası UNKNOWN |
| 7 | Fallback | Yedek yol / geri düşme | ADR-101 §2.2 — fallback zorunlu |
| 8 | Timeout | Zaman aşımı | context/HTTP timeout |
| 9 | Backoff (exponential) | Üssel geri çekilme | 429 sonrası artan bekleme |
| 10 | OAuth 2.0 / PKCE | Yetkilendirme + kod doğrulayıcı | Spotify/Tidal (zorunlu PKCE) · ADR-010 (state/PKCE) |
| 11 | Access token | Erişim jetonu | ADR-095: session cookie ≠ API JWT |
| 12 | JWT / kid / expiry | Jeton / anahtarı kimliği / son kullanma | Apple Music ES256+`kid`, `exp ≤ 15777000 sn` |
| 13 | API signature (`api_sig`) | API imzası | Last.fm md5 `api_sig` — servis sözleşmesi (veri öz-dijitleme DEĞİL) |
| 14 | Personal token | Kişisel jeton | Discogs OAuth 1.0a |
| 15 | Endpoint | Uç nokta | servis çağrı adresi |
| 16 | Terms of Service (TOS) | Kullanım Koşulları | YTM kapısı: resmi API yok → TOS riski (ADR-097 §1/6) |
| 17 | Deprecated / sunset | Kullanımdan kaldırma / kapanış | servis bayatlaması riski (R8) |
| 18 | Evidence / provenance | Kanıt / kaynak-ızgarası | `evidence` frontmatter alanı + erişim tarihi |
| 19 | Gate | Kapı / onay eşiği | keşif-kod sınırı (master §6, 8 kapı) |
| 20 | Ownership conflict | Sahiplik çakışması | S-1 · D-3 |
| 21 | ID conflict | Kimlik çakışması | D-5 (6× K020 adayı) |
| 22 | False positive (claim) | Sahte iddia / kanıtsız atıf | Truth Mode: 0-isabet → sil |
| 23 | Seed query | Tohum sorgu | keşif süreci aşama 2 (⚠️ taslak) |
| 24 | Clustering threshold | Kümeleme eşiği | aşama 3/4 — ⚠️ tanımsız |
| 25 | Federation | Birleştirme / federasyon | arama keşfi (K020-search) |

## §2. Servis-bazlı sabitler (TR'ye çevrilmez)

| Terim | Kaynak servis | Not |
|---|---|---|
| `Retry-After` | Spotify | 30 sn rolling |
| `code_verifier` / `code_challenge` | Spotify/Tidal | PKCE çifti |
| `X-RateLimit-Remaining` | Musixmatch | 402 ödeme kapısı |
| `api_sig` | Last.fm | md5 imza |
| `x-api-key` + LWA | Amazon Music | kapalı beta |
| User-Agent (zorunlu) | MusicBrainz | yoksa 503 |
| `search.list` | YouTube Data API v3 | 100/gün |

## §3. Süreç terimleri (EN ↔ TR eşleme tablosu)

| EN (vault kullanımı) | TR (bu K'da) | Not |
|---|---|---|
| finding | bulgu | kanıtsız bulgu sayılmaz |
| verified / partial / unknown | doğrulanmış / kısmi / bilinmeyen | doğrulama seviyeleri (dogrulama §1) |
| fabrication | uydurma | Truth Mode sınıfı (master §9.3) |
| claim | iddia | disk kanıtıyla çürütülür |
| upper decision | üst karar | ID CONFLICT çözüm mercii |
| handover (havale) | havale | §6 bekleyenler listesi |
| closed (question) | kapanmış soru | registry kaydı |

## §4. Sözlük uyarıları

1. **Discovery ≠ Detection:** vault'ta 'algılama' anlamında kullanılmaz; bu K'da
   hep **keşif**.
2. **Signal tanımsız:** `signals.ts` skoru arama kalitesidir (domain sinyali değil);
   'sinyal' terimi sözlüğe **bağlanmadı** — ayrı ADR bekliyor.
3. **Gate vs threshold:** gate = onay kapısı (bir kez geçilir), threshold = eşik
   (sayısal, şu an tanımsız — uydurulmaz).
4. **Session vs JWT:** ADR-095 §2.1 — CoreMusic session'ı (cookie) API JWT'sinden
   ayrıdır; 'auth' denince ikisi karıştırılmamalı.

## §5. Kaynaklar

- `discovery-dogrulama.md` §3 (12 servis terimi — 2026-10-17 web taraması)
- `discovery-master.md` §6 (gate terminolojisi)
- `index.md` §ELI15 (keşif / dış kaynak entegrasyon rafı)
- Servis resmi dokümanları: `discovery-web-arastirma.md` §6 (18 URL, 2026-10-17)

---

**Authority:** Bayram Ali / Vault Steward
