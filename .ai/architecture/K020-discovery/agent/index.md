---
node_id: K020-discovery/agent
node_type: subdomain
status: draft
parent_id: K020-discovery
children: []
depends_on:
  - K020-discovery
  - search
evidence:
  - "v2 §2.20 — K020-discovery/ agent/ (alt dosya listesi yok → children: [])"
  - "ADR-097 §1.6 (2026-10-12 web tur-1): 'Spotify Web API = OAuth2+PKCE' · 'MusicKit developer token Worker'da imzalanır'"
  - "Mevcut API güvenlik katmanı: accepted/ADR-010 (CSRF) · ADR-020 (API public security) · ADR-095 (JWT RS256)"
validation:
  - "2026-10-12 — bulgular ADR-097 §1.6 kaydıyla çapraz okundu"
open_questions:
  - "⚠️ ID CONFLICT: 6 klasör K020 önekinde paylaşır (adr/comilance/comere/licence/discovery/recomenataion) — ADR-097 §6 · v2 §3.1"
  - "Harici servis entegrasyonu CoreMusic kapsamına giriyor mu? Proje tanımında yok → VERIFY REQUIRED"
  - "PKCE/JWT akışları mevcut auth altyapısıyla (ADR-095 hybrid JWT) nasıl birleşir? Tasarım yok"
group: mimari
---


# K020-discovery/agent — Servis Ajanı Entegrasyon Notları

**ELI15:** Harici müzik servisleriyle **nasıl kimlik kanıtlanacağı** (login/token)
sorununun notLANDığı yerdir. Henüz **tasarım/kod yok** — yalnız keşif kaydı var.

## Entegrasyon keşif notları (2026-10-12 web tur-1)

| Servis | Kimlik akışı | Token | Durum |
|---|---|---|---|
| **Spotify** Web API | OAuth2 **Authorization Code + PKCE** (`code_verifier`/`code_challenge`, redirect redirect) | erişim/yenileme token'ı | keşif notu — tasarım ONAY bekliyor |
| **Apple Music** (MusicKit) | Apple geliştirici hesabı → **developer token** | **JWT · ES256 + `kid`** başlığı — imzalama tarafında Worker kullanılır | keşif notu — ONAY bekliyor |
| **YouTube Music** | **Resmi public API yok** → ajan akışı kurulamaz | — | `⚠️ KAPALI` (TOS riski, onay yok) |

## Güvenlik kuralı (bağlayıcı — mevcut vault kararları)

1. Token'lar log'a/MD'ye **yazılmaz**; yalnız `.env` (kök AGENTS.md §5 Security).
2. Harici çağrı **timeout/retry/fallback** zorunlu (`accepted/ADR-101` güvenlik kanonik sırası).
3. CoreMusic'in kendi oturumu ile harici token **karıştırılmaz** (`ADR-095` hybrid JWT ayrıdır).
4. `state`/`PKCE` doğrulaması atlanmaz (CSRF ailesi — `ADR-010`).

## Durum

- `node_type: subdomain` · `children: []` · `status: draft`.
- **Kapı:** entegrasyon kodu yalnız onay sonrası yazılır; bu sayfa keşif **notu** taşır.

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward
