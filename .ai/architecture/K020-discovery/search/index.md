---
node_id: K020-discovery/search
node_type: subdomain
status: draft
parent_id: K020-discovery
children: []
depends_on:
  - K020-discovery
evidence:
  - "v2 §2.20 — K020-discovery/ search/ (alt dosya listesi yok → children: [])"
  - "ADR-097 §1.6 (2026-10-12 web tur-1): 'YouTube Music resmi public API yok (TOS riski)'"
validation:
  - "2026-10-12 — bulgu ADR-097 §1.6 kaydıyla çapraz okundu (tek kaynak, ikinci doğrulama yok)"
open_questions:
  - "⚠️ ID CONFLICT: 6 klasör K020 önekinde paylaşır (adr/comilance/comere/licence/discovery/recomenataion) — ADR-097 §6 · v2 §3.1"
  - "YouTube Music API'siz arama/katalog nasıl beslenecek? Tasarım yok → VERIFY REQUIRED"
---

# K020-discovery/search — Arama & Katalog Keşfi

**ELI15:** "Şarkı/sanatçı **nerede** aranabilir?" sorusunun keşif listesi.
Entegrasyon kararı **onaydan önce** gelmez; burada yalnız not ve kapı vardır.

## Keşif notları

| # | Kaynak | Ne keşfedildi (2026-10-12) | Kanıt | Kapı |
|---|---|---|---|---|
| 1 | **YouTube Music** | Resmi **public API yok**; resmi olmayan istemciler TOS riski taşır | ADR-097 §1.6 (web tur-1) | `⚠️ ONAY BEKLİYOR` — entegrasyon yok |
| 2 | Spotify katalog araması | Web API arama uçları var, **OAuth2 Authorization Code + PKCE** gerektirir | ADR-097 §1.6 | tasarım ONAY bekliyor |
| 3 | Apple Music katalog araması | MusicKit — **developer token (JWT, ES256 + `kid`)** gerekir | ADR-097 §1.6 | tasarım ONAY bekliyor |
| 4 | Yerel/Çevrimdışı katalog | CoreMusic'in kendi MySQL kataloğu (BCNF) — dış kaynağa bağımlı değil | kök AGENTS.md §6 · `accepted/ADR-003` | mevcut |

## Durum

- `node_type: subdomain` · `children: []` · `status: draft`.
- **Yalnız keşif** — bu dizin altında entegrasyon kodu/spec **yoktur**.
- Sonuçlar onaylanırsa `K020-discovery/agent/` üzerinden servis akışına taşınır.

**Authority:** Bayram Ali / Vault Steward
