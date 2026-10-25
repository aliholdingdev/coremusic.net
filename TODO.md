---
title: "CoreMusic — Kök TODO Aile İndeksi"
type: todo
category: project-tracking
version: 1.3.0
status: active
authority: SSOT
updated: 2026-10-25
---

# CoreMusic — Kök TODO Aile İndeksi

**Zorunlu Bağlantılar:** [[PRD.md]] · [[CLAUDE.md]] · [[AGENTS.md]] · [[.ai/TODO.md]] · [[.ai/PRD.md]]

**Kural (Zero-Hallucination):** Her madde disk kanıtına dayanır; kanıtlanamayan madde yazılmaz. Durum: `[ ]` açık · `[x]` kapalı.

**eli10:** Bu dosya, her yüzeyin TODO.md'sine giden merkezi dizindir — PRD aile indeksi (`PRD.md` §5) ile aynı mantık.
**eli15:** Yüzey seviyesi maddeler kendi yüzey TODO'sunda; vault/depо geneli maddeler [[.ai/TODO.md]] §2–§4'te. Bir madde iki yerdeyse kapanışta ikisi de işaretlenir. Yüzey TODO'su maddeyi kendi PRD §7/§11'inden türetir — burada madde tekrarı yapılmaz.

---

## §1 Yüzey TODO Aile İndeksi

| # | Yüzey | TODO | PRD | Açık madde |
|---|---|---|---|---|
| — | **kök (bu dosya)** | `TODO.md` | [[PRD.md]] | 3 |
| 1 | admin.coremusic.net | [[admin.coremusic.net/TODO]] | [[admin.coremusic.net/PRD]] | 3 |
| 2 | api.coremusic.net | [[api.coremusic.net/TODO]] | [[api.coremusic.net/PRD]] | 3 |
| 3 | assets.coremusic.net | [[assets.coremusic.net/TODO]] | [[assets.coremusic.net/PRD]] | 3 |
| 4 | auth.coremusic.net | [[auth.coremusic.net/TODO]] | [[auth.coremusic.net/PRD]] | 3 |
| 5 | home.coremusic.net | [[home.coremusic.net/TODO]] | [[home.coremusic.net/PRD]] | 3 |
| 6 | media.coremusic.net | [[media.coremusic.net/TODO]] | [[media.coremusic.net/PRD]] | 3 |
| 7 | shared | [[shared/TODO]] | [[shared/PRD]] | 3 |
| 8 | app-nodejs-projects | [[app-nodejs-projects/TODO]] | [[app-nodejs-projects/PRD]] | 5 |
| 9 | app-cpp-projects | [[app-cpp-projects/TODO]] | [[app-cpp-projects/PRD]] | 3 |

> Modül seviyesi alt PRD'ler (`assets/js/*`, `shared/config`, `nova-search-engine/docs` …) kendi yüzey TODO'su üzerinden izlenir; ayrı TODO.md'si yoktur.

---

## §2 Kök Seviye Açık Maddeler

- [ ] **7 eksik PRD (#22–#27 + pro yüzeyi):** kök `[[PRD.md]]` §5 tablosunda satır var ama dosya diskte **YOK** — `auth/include` · `shared/database/migrations` · `shared/config` · `.ai/.sql` · `.ai/ui-design` · `.ai/.decisions` · `pro.coremusic.net/PRD.md` → üretilmeli (üretim sonunda ilgili yüzey TODO'suna bağlanır) · Kaynak: glob 2026-10-23
- [x] **Yüzey doküman turu + TODO ailesi commit kapandi (2026-10-24, a44c567):** 16 PRD + revizyonlar (kök `PRD.md` v1.2.0 · `.ai/PRD.md` v1.0.1 · `admin/PRD.md` §11 · `.ai/index.md`) + bu TODO ailesi (bu dosya + 9 yüzey TODO + `.ai/TODO.md` v1.4.0) — commit orkestratörde · Kaynak: `git status` 2026-10-23
- [x] **Neva PRD commit kapandi (2026-10-24, a44c567)** (eski madde: bekliyor) — vault kopyaları + kök `PRD.md` §5.1 + `AGENTS.md` §9 … (detay: `[[.ai/TODO.md]]` §3) · Kaynak: `git status` 2026-10-23

---

- [ ] **`pro.coremusic.net` yüzeyi (T-08):** PRD satırı `PRD.md` §5'e eklendi (35. satır, Durum YOK) — PRD dosyası + TODO diskte YOK → üretilmeli (yeni dizin = Human Approval Gate); Power Governor paneli bu yüzeyde yaşar · Kaynak: kullanıcı direktifi 2026-10-25
## §3 Çalışma Kuralı

1. Yeni iş doğar → önce ilgili **yüzey TODO'suna** yazılır (kanıt satırıyla); kök/seviye atlama yalnız depo geneli işler içindir.
2. Madde kapanır → `[x]` + kanıt (commit/dosya/ölçüm) + tarih; PRD §4 kapı maddesi de güncellenirse PRD sürümü bump edilir.
3. Vault/depо geneli madde → `[[.ai/TODO.md]]` §2–§4 (SSOT); bu dosya yalnız indeks + kök maddeler.

---

## Faz/Part Çalışma Planı — Özet (SSOT: `[[../.ai/TODO.md]]` §4A)

> Her PART tek oturumda toplu çalıştırılır. Sıra: **A∥B → C → D → E → F**; **G** en sonda.

| PART | Oturum | Ne | Toplu komut öneki | Onay |
|---|---|---|---|---|
| **A** | 1 | 22 mikro ölç/oku — **hepsi paralel** | `PART A: mikro adımları paralel yap, raporla` | onaysız |
| **B** | 2 | 8 karar (T-07 P0 + 2+1 + G-4 + N-3 + M-2 + C-1 + P-4 + M-3) | `PART B: 8 kararı öner, onayımı al, işle` | **senin onayın** |
| **C** | 3 | Power Governor kodu: S-3 migration → S-4 API 7 uç (sıralı) | `PART C: sırayla uygula, test yeşil → dur` | test + rapor |
| **D** | 4 | 7 PRD + F-1 bağlama + ADR-111 debate → accepted | `PART D: PRD üret + debate çalıştır, onaya sun` | ADR terfi |
| **E** | 5 | Doğrulama: coverage + curl×3 + KAPI-E + audit (server PC) | `PART E: server PC turu, kanıtları PRD'ye yaz` | — |
| **F** | 6 | 34 commit + `[x]` kapanış + vault sync + **Faz 8 QA** | `PART F: commit mesajları hazırla, onayımı al` | **commit** |
| **G** | 3+ | Büyük işler: Neva M0 · Faz 2 prototip · 6 yüzey · DB paketi · streaming | ayrı proje açılışı | her aşama |

**Yerel kurallar:** PART atlamak yasak (Faz 8 = E'den sonra) · kanıtsız `[x]` yasak · migration çalıştırma/commit/onaysız dizin yasak · detay + tam komut metinleri: `.ai/TODO.md` §4A.## TAM Madde Listesi — Kök · Küçükten Büyüğe (Geçmiş · Açık · Gelecek) — 2026-10-25

> Aile indeksinin tam listesi; SSOT detay: `[[../.ai/TODO.md]]`. Sıra: MICRO → ORTA → EPIC.

### A. GEÇMİŞ — TAMAMLANAN
- [x] MICRO: 2 bayat madde kapandı (kanıt `a44c567`) · eksik PRD sayacı 6→7 (+`pro`)
- [x] ORTA: Yüzey doküman + TODO ailesi commit — `a44c567` (2026-10-24)
- [x] ORTA: Neva PRD vault kopyaları commit — `a44c567` (2026-10-24)
- [x] ORTA: PRD ailesi 34→**35 satır** · v1.3.1 · ADR-111 satırı + next 112 (2026-10-25)
- [x] ORTA: TODO 11 dosya güçlendirme (adım + geçmiş bölümleri, 56 işlem 0 hata) (2026-10-25)
- [x] EPIC: PRD+TODO ailesi 34 yüzey indeksi v1.2.0 — `a44c567`
- [x] EPIC: 105 değişiklik commit serisi — `961244a → 96eb63f → f0e15eb → af5ea57 → 2017410` (2026-10-23)
- [x] EPIC: amfi 11 vault md + ADR-111 draft + brain v26.3.0 (2026-10-25)

### B. AÇIK — EKSİK / YARIM (küçükten büyüğe)
- [ ] MICRO: `log.md` mojibake 6 / CJK 12 hijyen kararı (`.ai/TODO` §4)
- [ ] MICRO: 423 dosyada frontmatter yok → zorla ekleme kararı
- [ ] MICRO: `.ai/index.md` §3 L15 `TECHNICAL_DOCUMENTATION.md` kayıt düzeltmesi
- [ ] ORTA: **7 eksik PRD** (#22-#27 + `pro.coremusic.net`) üretimi
- [ ] ORTA: T-09 `.ai/index.md` + `keys.md` katalog/keyword kayıtları
- [ ] EPIC: **34 kod değişikliği commit** (28 M + 6 yeni — onay → MO)
- [ ] EPIC: Coverage ≥%80 + Backend 09-11 canlı doğrulama (server PC)

### C. GELECEK — SIRADAKİ BÜYÜK İŞLER (öncelik sırası — SSOT: `.ai/TODO` §4C)
| Sıra | İş | Ölçek |
|---|---|---|
| 1 | T-07 topoloji kararı (tek P0) → ADR-111 kapanışı | MICRO |
| 2 | S-3 migration · N-1 dev-login · P-1 turbo · C-2 ctest | MICRO |
| 3 | T-04 API 7 uç + T-03 migration + G-4 yüzey kararı | ORTA |
| 4 | Commit paketi (34 değişiklik) | ORTA |
| 5 | Coverage + canlı curl ×3 + KAPI-E (server PC) | ORTA |
| 6 | 7 PRD + T-09 katalog + pro yüzeyi (dizin onayı) | ORTA |
| 7 | Power Governor ürünleşmesi · Neva M0 | EPIC |
| 8 | Faz 2 prototip · eksik 6 yüzey · DB paketi (ADR-072-079) · Faz 3 OEM | EPIC |## §4 Küresel Yürütme Sırası (2026-10-25 — özet; SSOT: `.ai/TODO.md` §4B)

| Sıra | Adım | Durum |
|---|---|---|
| 1 | T-07 P0 topoloji kararı → ADR-111 kapanışı | [ ] |
| 2 | T-10 kod paketi (Power Governor: migration → API → panel, mockup gate) | [ ] |
| 3 | Commit paketi: 28 M + 6 yeni kod değişikliği (MO/onay ile) | [ ] |
| 4 | T-08 `pro` PRD + T-09 index/keys katalog | [ ] |
| 5 | Backend 09-11 canlı doğrulama (server PC) | [ ] |

> Adım detayı, YAPMA listesi ve hazır komutlar: `[[../.ai/TODO.md]]` §4B (SSOT) · kodlama kartları: `[[../.ai/electronic/amplifier/coding-panel.md]]`.
## Geçmiş & Mikro Ölçek — Özet (2026-10-25; tam liste: `.ai/TODO.md` §4C)

**Geçmiş — tamamlanan (kök seviye):**
- [x] `[EPİK]` PRD ailesi 34→35 satır (v1.3.1, ADR-111 + pro satırı) · TODO 11 dosya güçlendirme · amfi 11 vault md — 2026-10-25
- [x] `[EPİK]` Yüzey doküman + TODO ailesi commit'i (`a44c567`) · Neva PRD commit'i (`a44c567`) — KAPANDI 2026-10-24
- [x] `[MİKRO]` 2 bayat kök madde kapatıldı · eksik PRD 6→7 — bugün

**Yarım (kök seviye):**
- [ ] `[EPİK]` 34 kod değişikliği commit'i (onay bekliyor) · 7 eksik PRD · coverage · canlı doğrulama
- [ ] `[MİKRO]` log.md mojibake/CJK hijyeni · 423 dosyada frontmatter kararı

**Gelecek sırası (küçük→büyük):** T-07 → S-3/N-1/P-1/C-2 mikro kapılar → T-04/T-03/T-05 Power Governor → 7 PRD + coverage → Neva M0 → Faz 2 prototip → 6 eksik yüzey → DB paketi (ADR-072-079) → Faz 3 OEM. **SSOT: `[[../.ai/TODO.md]]` §4C.**## Referanslar

- PRD aile indeksi: `[[PRD.md]]` (§5 · §5.1 Neva) · Vault TODO SSOT: `[[.ai/TODO.md]]` · Vault PRD: `[[.ai/PRD.md]]`
- Boot: `[[CLAUDE.md]]` · `[[AGENTS.md]]` · Süreç: `[[.ai/WORKFLOW.md]]`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-23
