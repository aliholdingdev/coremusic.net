---
title: "media.coremusic.net — TODO Listesi"
type: todo
category: project-tracking
version: 1.2.0
status: active
authority: SSOT
updated: 2026-10-25
---

# media.coremusic.net — TODO Listesi

**Zorunlu Bağlantılar:** [[PRD.md]] · [[CONTEXT.md]] · [[../TODO]] · [[../PRD.md]] · [[../.ai/TODO.md]]

**Kural (Zero-Hallucination):** Her madde PRD §7 / disk kanıtına dayanır; kanıtlanamayan madde yazılmaz. Durum: `[ ]` açık · `[x]` kapalı.

**eli10:** Medya arşivinin kalan işleri: şema çapraz kontrolü, GUI kararı, port netleştirme.
**eli15:** Detaylı Faz 2/2b/3 iş listesi `docs/todos.md`'dedir (P0 VR kapıları orada) — bu dosya PRD §7 seviyesindeki maddeyi tutar, ikisi çakışmaz: kapı kapanınca `docs/todos.md`'deki eş madde de güncellenir.

## Açık Maddeler

- [ ] **#25 (`.ai/.sql` PRD — diskte YOK) ile şema çapraz kontrolü** — `media_catalog` (9 tablo + `v_asset_search`) doğrulanır · Kaynak: `[[PRD.md]]` §7.1
- [ ] **Faz 3 GUI uygulama kararı** — `docs/faz3-gui-spec.md` §2 "⚠️ ayrı görev" kaydına bağlı kalır · Kaynak: `[[PRD.md]]` §7.2
- [ ] **Port netleştirme (ADR-039 :5000/:6000)** — açık kalmışsa `checklist` VR maddesi olarak taşınır · Kaynak: `[[PRD.md]]` §7.3

## İlişkili Dosyalar

- `docs/todos.md` (detay P0–P2 listesi · eş: `docs/checklist.md`) — kapı koşulları orada tanımlı

## TAM Madde Listesi — Küçükten Büyüğe (Geçmiş · Açık · Gelecek) — 2026-10-25

> Tek doğruluk listesi; `## Geçmiş & Mikro Ölçek` kanıt detayıdır. Sıra: MICRO → ORTA → EPIC.

### A. GEÇMİŞ — TAMAMLANAN
- [x] MICRO: `docs/checklist.md` + `docs/todos.md` kapı sistemi kuruldu (AGENTS §7)
- [x] ORTA: dizin yapısı kanıtı — 62 dizin + config 3 + docs 2 (`Test-Path media = True`) — 2026-09-29
- [x] ORTA: ADR-092 v1.1.0 revizyonu (yol düzeltmesi + gitignore kanıtı) — 2026-09-29
- [x] ORTA: `media_catalog` 9 tablo + `v_asset_search` view + `taxonomy.json` enum sözleşmesi
- [x] EPIC: PHP CLI arşiv üçlüsü (`scan`/`audit`/`ingest`) + `--dry-run`/`--deep` kapıları + `media/` ağacı salt-okunur (ADR-092 §5)

### B. AÇIK — EKSİK / YARIM (küçükten büyüğe)
- [ ] MICRO: M-3a — `Get-NetTCPConnection` ile :5000/:6000 dinleyicisini ölç → 1 satır kanıt
- [ ] MICRO: M-1a — `media_catalog.sql` içinde `CREATE TABLE` say → 9 mu doğrula
- [ ] MICRO: M-4a — `docs/todos.md` açık madde sayısını bu dosyayla karşılaştır
- [ ] MICRO: `ingest` dry-run CSV örneği + onay/kilit akışı kontrolü (Security zorunlu)
- [ ] MICRO: `taxonomy.json` ↔ `media.schema.json` enum drift taraması (1 komut)
- [ ] ORTA: M-1 **#25 `.ai/.sql` PRD ÜRET** (diskte YOK — 7 eksikten) + şema çapraz (PLT-002/007)
- [ ] ORTA: M-2 **Faz 3 GUI uygulama kararı** (`faz3-gui-spec.md` §2 "⚠️ ayrı görev")
- [ ] ORTA: M-3 **Port netleştirme (ADR-039 :5000/:6000)** → açık kalırsa checklist VR-7
- [ ] ORTA: M-4 TODO ailesi ↔ `docs/todos.md` eş madde senkronu
- [ ] EPIC: Faz 2/2b P0 VR kapıları (`docs/todos.md`) kapanışı
- [ ] EPIC: Streaming Service (:5000/:6000, HLS/DASH/DLNA) — PROJECTS §7.1.10 PLANNED

### C. GELECEK — SIRADAKİ BÜYÜK İŞLER (öncelik sırası)
| Sıra | EPIC iş | İlk MICRO adım |
|---|---|---|
| 1 | M-3 port ölçümü | `Get-NetTCPConnection` |
| 2 | M-1 PRD üret + şema çapraz | 9 tablo sayımı |
| 3 | M-2 GUI kararı | spec §2 okuması |
| 4 | Streaming servisi + Faz 3 panel | port kararı + kapı |## Adım Adım Yürütme (2026-10-25 güçlendirme)

**Kural:** Her adım tek oturum · `[ ]` → çalış · `[x]` + kanıt → kapanır · **YAPMA** bağlayıcı · komutu gönder → agent yapar.

| # | Adım | Ne yapılacak (kaynak) | YAPMA | Hazır komut |
|---|---|---|---|---|
| M-1 | [ ] | #25 (`.ai/.sql` PRD — **diskte YOK**) ile şema çapraz kontrol: `media_catalog` 9 tablo + `v_asset_search` | PRD üretmeden "uygun" deme | `M-1: .ai/.sql PRD'sini üret, sonra media_catalog şemasını çapraz doğrula (9 tablo + view)` |
| M-2 | [ ] | Faz 3 GUI uygulama kararı — `docs/faz3-gui-spec.md` §2 "⚠️ ayrı görev" kaydı | kapı kararı olmadan GUI kodu | `M-2: Faz3 GUI kararını ver — ayrı görev mi, home'a bağlı mı? Sonucu TODO+spec'e işle` |
| M-3 | [ ] | Port netleştirme (ADR-039 :5000/:6000) → açıksa `checklist` VR maddesi | port uydurma/sonrası keşif | `M-3: ADR-039 portlarını netleştir — servis:list ile ölç, sonucu checklist VR-7'ye işle` |
| M-4 | [ ] | `docs/todos.md` ↔ bu dosya eş madde senkronu (kapı kapanınca ikisi birden) | iki farklı kapı listesi bırakma | `M-4: TODO ailesi ile docs/todos.md eş madde senkronunu yap` |

**Yüzey yasakları:** `media/` ağacına yazma (salt-okunur, master) · master taşıma/hash (Security onayı) · `SELECT *` · port tahmini · commit.
## Geçmiş & Mikro Ölçek — Tam Liste (2026-10-25)

**Geçmiş — tamamlanan (kanıtlı):**
- [x] `[ORTA]` ADR-092 v1.1.0 revizyonu (yol + gitignore kanıtı) — 2026-09-29, dosya kaydı
- [x] `[ORTA]` Dizin yapısı kanıtı: 62 dizin + config 3 + docs 2 (`Test-Path media = True`) — 2026-09-29
- [x] `[MİKRO]` `docs/checklist.md` + `docs/todos.md` kapı sistemi kurulu (AGENTS §7 kanıtı)

**Mikro işler (en küçük → büyük):**
- [ ] `[MİKRO]` M-3 altı: `netstat`/`Get-NetTCPConnection` ile 5000/6000 dinleyen var mı → 1 satır kanıt
- [ ] `[MİKRO]` M-1 altı: `media_catalog.sql` içinde `CREATE TABLE` sayısını say → 9 mu?
- [ ] `[MİKRO]` M-4 altı: `docs/todos.md` açık madde sayısını bu dosyayla karşılaştır
- [ ] `[ORTA]` M-1 şema çapraz → M-2 GUI kararı → `[EPİK]` Faz 3 panel kodu (kapıdan sonra)## Referanslar

- `[[PRD.md]]` (§7 Sonraki Adım) · `[[CONTEXT.md]]` · `[[WORKFLOW.md]]` · `[[AGENTS.md]]` · `[[CLAUDE.md]]`
- Aile indeksi: `[[../TODO]]` · `[[../PRD.md]]` · Vault TODO: `[[../.ai/TODO.md]]`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
