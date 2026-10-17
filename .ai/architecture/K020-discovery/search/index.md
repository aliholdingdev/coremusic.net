---
node_id: K020-discovery/search
node_type: subdomain
status: draft
parent_id: K020-discovery
children:
  - "search-kapsam.md"
  - "search-bilesenler.md"
  - "search-ornekler.md"
  - "search-web-arastirma.md"
  - "search-dogrulama.md"
depends_on:
  - K020-discovery
evidence:
  - "v2 §2.20 — K020-discovery/ search/ (2026-10-17: alt dosya listesi eklendi → children 5 dosya)"
  - "ADR-097 §1 madde 6 (satır 33) (2026-10-12 web tur-1): 'YouTube Music resmi public API yok (TOS riski)'"
  - "2026-10-17 web tur-2: 12 servis arama tipi × rate başlığı matrisi (search-web-arastirma.md §1–§2)"
validation:
  - "2026-10-12 — bulgu ADR-097 §1 madde 6 (satır 33) kaydıyla çapraz okundu (tek kaynak, ikinci doğrulama yok)"
open_questions:
  - "⚠️ ID CONFLICT: 6 klasör K020 önekinde paylaşır (adr/comilance/comere/licence/discovery/recomenataion) — v2 satır 98/616 · ADR-097 §2 K6 (satır 77–78)"
  - "YouTube Music API'siz arama/katalog nasıl beslenecek? Tasarım yok → VERIFY REQUIRED"
group: mimari
---


# K020-discovery/search — Arama & Katalog Keşfi

**ELI15:** "Şarkı/sanatçı **nerede** aranabilir?" sorusunun keşif listesi.
Entegrasyon kararı **onaydan önce** gelmez; burada yalnız not ve kapı vardır.

## Keşif notları

| # | Kaynak | Ne keşfedildi (2026-10-12) | Kanıt | Kapı |
|---|---|---|---|---|
| 1 | **YouTube Music** | Resmi **public API yok**; resmi olmayan istemciler TOS riski taşır | ADR-097 §1 madde 6 (satır 33) (web tur-1) | `⚠️ ONAY BEKLİYOR` — entegrasyon yok |
| 2 | Spotify katalog araması | Web API arama uçları var, **OAuth2 Authorization Code + PKCE** gerektirir | ADR-097 §1 madde 6 (satır 33) | tasarım ONAY bekliyor |
| 3 | Apple Music katalog araması | MusicKit — **developer token (JWT, ES256 + `kid`)** gerekir | ADR-097 §1 madde 6 (satır 33) | tasarım ONAY bekliyor |
| 4 | Yerel/Çevrimdışı katalog | CoreMusic'in kendi MySQL kataloğu (BCNF) — dış kaynağa bağımlı değil | kök AGENTS.md §6 · `accepted/ADR-003` | mevcut |

## Durum

- `node_type: subdomain` · `children: 5 dosya` · `status: draft`.
- **Yalnız keşif** — bu dizin altında entegrasyon kodu/spec **yoktur**.
- Sonuçlar onaylanırsa `K020-discovery/agent/` üzerinden servis akışına taşınır.

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `search-kapsam.md` | Sorgu-keşif kapsamı: tipler, sonuç dilbilgisi, strateji | Kademe-3 |
| mimari | `search-bilesenler.md` | B1–B7 bileşen sözleşmeleri (gate→tohum→kuyruk→istemci→federatör→skor→devir) | Kademe-3 |
| kod | `search-ornekler.md` | 13 dil + SQL dilekçe (gate-first + erteleme + fallback; entegrasyon DEĞİLDİR) | Kademe-3 |
| kanıt | `search-web-arastirma.md` | tur-2 sorgu-keşif kanıtları: arama tipi × servis + rate başlıkları (2026-10-17) | Kademe-3 |
| kanıt | `search-dogrulama.md` | Search-özgü kapılar (S1–S8) + 13 dil denetim tablosu | Kademe-3 |

---

**Authority:** Bayram Ali / Vault Steward
