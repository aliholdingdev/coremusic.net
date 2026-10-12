---
node_id: K020-discovery
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - search
  - agent
depends_on:
  - "K000–K5000 Architecture Registry"
evidence:
  - "v2 §2.20 mini-ağaç (K020-discovery → index.md · search/ · agent/) — G:/Drive'ım/Projelerim/Project-notes/Bayram Ali Prompt Arşivi & Notlar/Coremusic/coremusic-katmanli-mimari-agaci-v2.md (satır 570–575)"
  - ".ai/architecture/index.md §5 satır 83 (draft)"
  - "Web tur-1 (2026-10-12, ADR-097 §1.6): YouTube Music resmi public API yok · Spotify OAuth2+PKCE · MusicKit developer token Worker'da imzalanır"
validation:
  - "2026-10-12 — v2 §2.20 şekli birebir karşılaştırıldı (3 öğe)"
open_questions:
  - "⚠️ ID CONFLICT: 6 klasör K020 önekinde paylaşır (adr/comilance/comere/licence/discovery/recomenataion) — ADR-097 §6 · v2 §3.1"
  - "YouTube Music entegrasyonu: resmi public API **yok** (TOS riski) → resmi olmayan yollara mı başvurulacak? ONAY BEKLİYOR"
  - "'discovery' keşif notları onaylanana kadar entegrasyon kodu yazılmaz"
---

# K020-discovery — Keşif / Dış Kaynak Entegrasyon Rafı

## ELI15 — Bu klasör ne işe yarar?

Müziğin ve sanatçının **kaynaktan nasıl bulunacağının** tarandığı raftır: arama/
katalog tarafı (`search/`) ve harici servislerle konuşan ajan tarafı (`agent/`).
Burada **keşif notları** durur — entegrasyon kararı onaysız verilmez.

## Çocuk kataloğu (v2 §2.20 — birebir)

| Öğe | Tür | Rol | Durum |
|---|---|---|---|
| `search/` | dizin | Arama/katalog keşfi — dış kaynakların aranabilirliği (`search/index.md`) | draft |
| `agent/` | dizin | Servis ajanı entegrasyon notları — OAuth/JWT akışları (`agent/index.md`) | draft |

## Keşif notları (2026-10-12 web tur-1 — kanıtlı)

| Kaynak | Bulgu | Kayıt yeri |
|---|---|---|
| **YouTube Music** | Resmi **public API yok** (TOS riski) → entegrasyon keşfi **onay bekliyor** | `[[search/index]]` |
| **Spotify** | Web API = OAuth2 **Authorization Code + PKCE** | `[[agent/index]]` |
| **Apple Music** | Developer token = **JWT (ES256 + `kid`)** — imzalama Worker'da | `[[agent/index]]` |

> Bu notlar ADR-097 §1.6'daki web tur-1 kaydıyla **aynı** bulgulardır; ikinci bir
> doğrulama yapılmadan "yeni kanıt" sayılmaz.

## Durum

- **status:** `draft` · **children:** `search`, `agent` (v2 birebir).
- **Kapı:** keşif notu → onay → entegrasyon. Onaysız **kod/entegrasyon üretilmez**.
- **depends_on:** yalnız registry — dış servis bağımlılıkları onayla birlikte eklenir.

## Açık soru özeti

1. YouTube Music'siz bir katalog stratejisi kabul ediliyor mu? → `⚠️ VERIFY REQUIRED`
2. Spotify/Apple entegrasyonu CoreMusic kapsamına giriyor mu (proje tanımı)? → `⚠️ VERIFY REQUIRED`
3. ⚠️ ID CONFLICT: 6 klasör K020 önekinde paylaşır (adr/comilance/comere/licence/discovery/recomenataion) — ADR-097 §6 · v2 §3.1

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
