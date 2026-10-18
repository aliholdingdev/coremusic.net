---
node_id: K020-discovery-kapsam
node_type: subdomain
status: draft
parent_id: K020-discovery
children: []
depends_on: [K007-data, K008-domain, K021-recomenataion]
evidence:
  - ".ai/architecture/K020-discovery/discovery-master.md §1-§2 (kapsam/sınır + 3 disiplin + 2 omurga — 2026-10-18)"
  - ".ai/architecture/K020-discovery/discovery-bagimliliklar.md §2 (D-1 · S-1 · D-3 · D-4 · D-5 — 2026-10-18)"
  - ".ai/architecture/K020-discovery/discovery-dogrulama.md §2-§3 (tur-1 7 kayıt + tur-2 12 servis/18 URL — 2026-10-18)"
validation: "Kapsam ± sınır her § disk kanıtıyla; ölü referanslar ⚠️ olarak listelenir; depends_on yalnız gerçek K adları."
open_questions:
  - "⚠️ K008-domain/discovery ↔ K020-discovery sahiplik örtüşmesi (D-3 — kapsam ayrımı yok, ADR bekliyor)"
  - "⚠️ S-1: K007-data/search ↔ K020-discovery/search çift sahipliği (data-master.md:219 — sahiplik ADR'si bekliyor)"
  - "⚠️ K0100-identitiy · K020-licence · K020-comilance · K020-comere · K002-altyapi ölü referanslardır — depends_on'a YAZILMAZ"
group: mimari
---

# Discovery — Kapsam

> K020-discovery'nin neyi kapsadığını, neyi kapsam dışında bıraktığını ve
> keşif çıktısının kime gittiğini tanımlar. Kanıt: `discovery-master.md` (Truth Mode
> düzeltmeli, 2026-10-17/18).

## §1. ELI15 — Bu klasör ne işe yarar?

**K020-discovery = "dış dünyayı keşif ekibi."** CoreMusic harici servislere
(Spotify, Apple Music, MusicBrainz…) ve iç keşif yüzeylerine (arama, içerik)
bakan disiplindir. Görevi **kod yazmak değil**: hangi servis var, neyi destekliyor,
kotası ne, kapısı ne — bunları bulup kanıtla raporlamak. Keşif bitmeden K020-alt
node'ları (auth, search, library…) kodlanamaz. Sınır şu cümlededir:
**"Keşif bulgu üretir; kod yalnız K020 alt katmanlarında ve onaydan sonra yazılır."**

## §2. Kapsam (in-scope)

| # | Alan | Açıklama | Kanıt |
|---|---|---|---|
| 1 | Keşif omurgası | root `discovery-*` dosyalarının mimari rolü | `discovery-master.md` §2 |
| 2 | Alt disiplin sözleşmesi | `agent/` (içerik/flora keşfi) + `search/` (sorgu keşfi) → root'a raporlama | `index.md` §GRUPLAR |
| 3 | K020 öncesi omurga | K007-data/schema (girdi) + K008-domain/discovery (D-3 sınırı) | `discovery-bagimliliklar.md` §3 |
| 4 | Keşif süreci | Kaynak → tohum → skorla → küme → node bağlama | `discovery-master.md` §3 (**⚠️ ADR-097'de bağlayıcı karşılığı YOK**) |
| 5 | Gate haritası | Keşif-kod sınırındaki 8 bağlayıcı kapı | `discovery-master.md` §6 |
| 6 | Servis keşfi (dış) | 12 servis / 18 URL / 0 hata (tur-2) | `discovery-web-arastirma.md` §6 |

## §3. Kapsam dışı (out-of-scope)

| # | Dışarıda | Nerede | Not |
|---|---|---|---|
| 1 | İçerik keşfi/flora analizi | `K020-discovery/agent/**` | alt disiplin sahibi |
| 2 | Arama/sorgu keşfi detayı | `K020-discovery/search/**` | alt disiplin sahibi |
| 3 | Şema normalizasyonu (BCNF) | `K007-data/schema/*` | data-engineer omurgası |
| 4 | Dönem ayrıştırma/şarkı alanı | `K008-domain/discovery/*` | D-3 sahiplik sınırı |
| 5 | K020 alt katman kod uygulaması | `K020-{auth,library,preferences,search,recommendation,api}` | `.ai/architecture/README.md:16` ("Burada kod yok") |

## §4. Karar özeti (dosyanın karar yüzeyi)

| Karar | Kaynak | Durum |
|---|---|---|
| Keşif ≠ kod — onay kapısı zorunlu | root `AGENTS.md` §1 (`APPROVAL → IMPLEMENTATION`) | bağlayıcı |
| YouTube Music KAPALI (resmi API yok, TOS riski) | `draft/ADR-097` §1 madde 6 (satır 33) | kapı **değişmez** |
| Sinyal sözleşmesi (enum/ucu) YOK | Truth Mode 2026-10-17 — 0 isabet | ⚠️ tanımsız |
| Ölçüm kanonu `ReadAllLines().Length` | registry §9.12.1 (satır 761-764) | bağlayıcı |
| Gerçek arama yüzeyi = `nova-search-engine` | `.ai/projects/NovaSearchEngine/01-kurulum-mimari-ve-acik-konular.md:4` | ✓ VAR |

## §5. Düğüm ve bağımlılık haritası

```
K020-discovery/            (parent · keşif disiplini kökü)
├── index.md               ORCHESTRATION — route, gruplar, ölçüm
├── discovery-master.md    MASTER — omurga sözleşmesi + gate haritası
├── discovery-bagimliliklar.md  D-1 · S-1 · D-3 · D-4 · D-5
├── discovery-dogrulama.md      kanıt seti + kapılar
├── discovery-web-arastirma.md  tur-2 (12 servis / 18 URL)
├── agent/  (6 md)         içerik/flora keşfi
└── search/ (6 md)         sorgu-keşif
```

**Disk ölçümü (2026-10-17):** 17 md — kök 6 + `search/` 6 + `agent/` 6.

**Yön kuralı (D-1):** `K020-recomenataion → K020-discovery` (öneri, keşfi bekler;
tersi değil). Keşif bitmeden öneri node'u kodlanamaz.

## §6. Ölü referanslar (⚠️ — depends_on'a YAZILMAZ)

| Ölü ad | Gerçek karşılık / durum |
|---|---|
| `K0100-identitiy` | diskte YOK (yazım hatası envanterde) |
| `K020-licence` · `K020-comilance` · `K020-comere` | diskte YOK — ID CONFLICT D-5 (v2 satır 98/616) |
| `K002-altyapi` · `K001-infrastructure` · `K002-data` | diskte YOK — gerçek: `K001-donanım` · `K002-electroncis` · `K007-data` |
| `K019-testing` | diskte YOK — gerçek klasör `K18-testing` |
| `K018-devops` | diskte YOK — gerçek: `K17-devops` |
| "auth gate" ADR'si (`adr-010-media-provider-auth-gate.md`) | uydurma dosya adı — gerçek `accepted/ADR-010` = CSRF stratejisi |
| `S-2…S-7` etiketleri | uydurma — yalnız `S-1` gerçek |
| `log.md:774-801` atıfları | imkânsız — `log.md` diskte 118 satır |

## §7. Bağımlılıklar (gerçek K adları — disk ölçümü)

| K | Yön | Ne |
|---|---|---|
| K007-data | upstream (omurga girdisi) | `schema-kapsam.md` normalizasyon formülü; S-1 arama sahipliği sınırı |
| K008-domain | upstream (omurga) | dönem ayrıştırma, shuffle sözleşmesi (D-3) |
| K021-recomenataion | downstream | keşif/katalog verisi → öneri girdisi (D-1) |

## §8. Açık sorular (dosya sonu)

1. ⚠️ D-3 sahiplik sınırı (K008 ↔ K020-discovery) — domain + architect kararı.
2. ⚠️ S-1 arama sahipliği (K007 ↔ K020) — sahiplik ADR'si bekliyor.
3. ⚠️ ID CONFLICT D-5 (6× K020 adayı) — onaysız düzeltilmez, üst karar.
4. ⚠️ Keşif kümeleme eşiği/demet adı tanımsız — uydurma ad/sayı yazılmaz.

---

**Authority:** Bayram Ali / Vault Steward
