---
node_id: K020-discovery
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - search
  - agent
  - discovery-master.md
  - discovery-bagimliliklar.md
  - discovery-dogrulama.md
  - discovery-web-arastirma.md
depends_on:
  - "K000–K5000 Architecture Registry"
evidence:
  - "v2 mini-ağaç (K020-discovery → index.md · search/ · agent/) — G:/Drive'ım/Projelerim/Project-notes/Bayram Ali Prompt Arşivi & Notlar/Coremusic/coremusic-katmanli-mimari-agaci-v2.md (satır 571–575)"
  - ".ai/architecture/index.md §5 satır 83 (draft)"
  - "Web tur-1 (2026-10-12, ADR-097 §1 madde 6 (satır 33)): YouTube Music resmi public API yok · Spotify OAuth2+PKCE · MusicKit developer token Worker'da imzalanır"
  - "Web tur-2 (2026-10-17): 12 servis geniş tarama — 18 URL / 0 hata · tur-1 kapıları aynen geçerli (discovery-web-arastirma.md §2–§6)"
validation:
  - "2026-10-12 — v2 satır 571–575 şekli birebir karşılaştırıldı (3 öğe)"
open_questions:
  - "⚠️ ID CONFLICT: 6 klasör K020 önekinde paylaşır (adr/comilance/comere/licence/discovery/recomenataion) — ADR-097 §2 K6 (satır 77–78)"
  - "YouTube Music entegrasyonu: resmi public API **yok** (TOS riski) → resmi olmayan yollara mı başvurulacak? ONAY BEKLİYOR"
  - "'discovery' keşif notları onaylanana kadar entegrasyon kodu yazılmaz"
group: mimari
---


# K020-discovery — Keşif / Dış Kaynak Entegrasyon Rafı

## ELI15 — Bu klasör ne işe yarar?

Müziğin ve sanatçının **kaynaktan nasıl bulunacağının** tarandığı raftır: arama/
katalog tarafı (`search/`) ve harici servislerle konuşan ajan tarafı (`agent/`).
Burada **keşif notları** durur — entegrasyon kararı onaysız verilmez.

## Çocuk kataloğu (v2 satır 571–575 — birebir)

| Öğe | Tür | Rol | Durum |
|---|---|---|---|
| `search/` | dizin | Arama/katalog keşfi — dış kaynakların aranabilirliği (`search/index.md` · 5 dosya) | draft |
| `agent/` | dizin | Servis ajanı entegrasyon notları — OAuth/JWT akışları (`agent/index.md` · 5 dosya) | draft |
| `discovery-master.md` | dosya | Omurga sözleşmesi: katman, pipeline, gate haritası (§6) | draft |
| `discovery-bagimliliklar.md` | dosya | D-1/S-1/D-3/D-4/D-5 bağımlılık haritası (ok yönü + kırılma senaryoları) | draft |
| `discovery-dogrulama.md` | dosya | Tur-1 (7 gerçek kayıt — uydurmalar silindi) + tur-2 (12 servis) kanıt seti + kapılar | draft |
| `discovery-web-arastirma.md` | dosya | Geniş servis taraması (tur-2, 2026-10-17 · 12 servis · 18 URL) | draft |

## Keşif notları (web taraması — kanıtlı)

| # | Kaynak | Bulgu | Kapı/kayıt |
|---|---|---|---|
| 1 | **YouTube Music** | Resmi **public API yok** (TOS riski) — tur-2'de **değişiklik yok** | `⚠️ KAPALI` · ADR-097 §1 madde 6 (satır 33) |
| 2 | **Spotify** | OAuth2 **Authorization Code + PKCE** · 429/`Retry-After` **30 sn rolling** | auth keşfi · ⚠️ gate ADR'si YOK (ADR-010 = CSRF) |
| 3 | **Apple Music** | **JWT (ES256 + `kid`)** — imzalama Worker'da | `[[agent/index]]` |
| 4 | Kotası bilinenler | MusicBrainz **1/sn + UA zorunlu** · YT v3 `search.list` **100/gün** · Musixmatch **2.000/gün + HTTP 402** | rate-aware B3 kuyruk |
| 5 | Yeni kimlik | Tidal **OAuth 2.1 + zorunlu PKCE** · Discogs **OAuth 1.0a** · Last.fm **`api_sig` md5** · Amazon **LWA + x-api-key (kapalı beta)** | auth keşfi · ⚠️ gate ADR'si YOK (ADR-010 = CSRF) |
| 6 | Lisans/erişim | Genius **ticari lisans** · Amazon Music **kapalı beta** (davetli) | onay |
| 7 | Anonim/en ucuz başlangıç | MusicBrainz + Deezer (public catalog) → imza/token servisleri **önce** | B3 sırası |

> Satır 1–3 = tur-1 (ADR-097 §1 madde 6, satır 33) · satır 4–7 = tur-2 (2026-10-17).
> Tam matris: `[[discovery-web-arastirma]]` (12 servis · 18 URL · 0 hata).
> Kapılar aynen geçerli; ikinci doğrulama olmadan tur-1 bulgusu "yeni kanıt" sayılmaz.

## Durum

- **status:** `draft` · **children:** `search`, `agent` + 4 root dosya (master · bagimliliklar · dogrulama · web-arastirma).
- **Kapı:** keşif notu → onay → entegrasyon. Onaysız **kod/entegrasyon üretilmez**.
- **depends_on:** registry + K007-data/search (S-1, sahiplik ⚠️) + K008-domain (C2, sahiplik ⚠️) + ADR-010/095/097/101.
- **Sahiplik ⚠️:** K007-data/search ↔ K020-discovery/search (S-1) · ID CONFLICT K020-discovery ↔ K020-search (D-5, v2 satır 98/616) — üst karar bekliyor.

## Açık soru özeti

1. YouTube Music'siz bir katalog stratejisi kabul ediliyor mu? → `⚠️ VERIFY REQUIRED`
2. Spotify/Apple entegrasyonu CoreMusic kapsamına giriyor mu (proje tanımı)? → `⚠️ VERIFY REQUIRED`
3. ⚠️ ID CONFLICT: 6 klasör K020 önekinde paylaşır (adr/comilance/comere/licence/discovery/recomenataion) — ADR-097 §2 K6 (satır 77–78)
4. S-1 sahiplik: "arama" keşif kuralları K007-data/search'te mi, K020-discovery/search'te mi? (geçici karar: K-SCOPE-4) → `⚠️ VERIFY REQUIRED`
5. K008 C2 (D-3): K008-domain/discovery ↔ K020-discovery sahiplik sınırı (domain **önce** → keşif **sonra**) → `⚠️ VERIFY REQUIRED`

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi, çocuk kataloğu, tur-2 özeti | Kademe-1 |
| mimari | `discovery-master.md` | Omurga: katman sözleşmesi, pipeline, gate haritası (§6) | Kademe-1 |
| mimari | `discovery-bagimliliklar.md` | D-1/S-1/D-3/D-4/D-5 bağımlılık haritası + kırılma senaryoları | Kademe-1 |
| kanıt | `discovery-dogrulama.md` | Tur-1 (7 gerçek kayıt) + tur-2 kapıları + ölçüm kanonu | Kademe-1 |
| kanıt | `discovery-web-arastirma.md` | 12 servis geniş tarama (2026-10-17 · 18 URL · 0 hata) | Kademe-1 |
| mimari | `search/index.md` · `agent/index.md` | Alt disiplin künyeleri (5'er dosya) | Kademe-2 |
| mimari | `search-*.md` / `agent-*.md` (kapsam·bilesenler) | Alt disiplin sözleşmeleri | Kademe-3 |
| kod | `search-ornekler.md` / `agent-ornekler.md` | 13 dil + SQL dilekçe (entegrasyon DEĞİLDİR) | Kademe-3 |
| kanıt | `search-*.md` / `agent-*.md` (web-arastirma·dogrulama) | tur-2 sorgu/İçerik boyutu + kapılar | Kademe-3 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
