---
title: "CoreMusic — Proje TODO Listesi"
type: todo
category: project-tracking
version: 1.0.0
status: active
authority: SSOT
updated: 2026-09-29
---

# CoreMusic — Proje TODO Listesi

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[MEMORY.md]] · [[index.md]] · [[log.md]] · [[CHECKLIST.md]]

**Kural (Zero-Hallucination):** Aşağıdaki **her madde disk kanıtına dayanır**; her satırda kaynak (commit / dosya / ölçüm) yazılır. Kanıtlanamayan hiçbir madde buraya yazılmaz; okurken emin olunmayan iddia → `VERIFICATION REQUIRED`.

**Durum:** `[ ]` = açık · `[x]` = kapalı (kapanışta [[CHECKLIST.md]] §C3 ile güncellenir).

---

## Purpose

### §1 Amaç

Depodaki gerçek iş kalemlerini (açık commit'ler, eksik testler, vault tutarsızlıkları, karar bekleyenler) öncelik sırasıyla tek listede tutmak; her session başında [[CHECKLIST.md]] §A3 ile taranmak.

---

## Workflow

### §2 P0 — Depo bütünlüğü & çalışır durum (önce bunlar)

- [ ] **Commit bekleyen 105 değişikliği sınıflandır ve commit'e hazırla** (63 M / 25 D / 17 `??`) — Kaynak: `git status --porcelain` ölçümü 2026-09-29 (anlık)
- [ ] **Interface → Contract taşımasını tamamla:** `shared/src/Interfaces/**` (14 D) ve `shared/src/AI/Contracts/**` (7 D) worktree'de silinmiş; karşılığı olan `shared/src/Contracts/{AI,Auth,Config,Database,Middleware,Security}/**` (6 dizin) henüz tracked değil → taşımayı stage et **veya** revert et — Kaynak: `git status` D/?? 2026-09-29
- [ ] **auth testlerindeki çift ağacı tek ağaç yap:** HEAD hem `tests/Domain/**` (4 dosya) hem `tests/Unit/Domain/**` (6 dosya) içeriyor, worktree eski 4'ü silmiş → eski yolu stage ederek silinmeyi onayla — Kaynak: `git ls-files auth.coremusic.net/tests` + `git status` D, 2026-09-29
- [ ] **Kökteki stash/artık dosyaları temizle:** `.ai.zip`, `coremusic.net.zip`, `aaa.md`, `output.md` (untracked, `.gitignore` kapsamı dışında) → sil / arşivle / `.gitignore` ekle; `.ai.zip` vault içeriği olabilir (REDACTED kontrolü) — Kaynak: `git status ??` 2026-09-29
- [ ] **Untracked kaynak varlıklarını commit et:** `.gitleaks.toml` (CI'da `secret-scan.yml` var), `.ai/.decisions/accepted/ADR-048-…/ADR-049-…/ADR-050-…` (3 dosya), `home.coremusic.net/include/Repository/`, `shared/src/OAuth/OAuthRepository.php` — Kaynak: `git status ??` 2026-09-29

### §3 P1 — Vault tutarlılığı & karar bekleyenler

- [ ] **ADR numara çelişkisini çöz:** kök `CLAUDE.md` "next new = 091" diyor, `.ai/.decisions/index.md` Active aralığını "ADR-038 → 092" yazıyor ve `accepted/ADR-092-media-dizin-ekseni-ve-ulid.md` diskte var; ayrıca `.ai/CLAUDE.md` §29 "ADR Coverage 001-089 (80 karar)" diyor → tek sayı hizalanacak (Vault Steward onayı) — Kaynak: `CLAUDE.md` L16 · `.ai/.decisions/index.md` §2 · `.ai/CLAUDE.md` §29
- [ ] **§27A skills tablosunu düzelt:** `.ai/CLAUDE.md` §27A'da 10 satırlık liste var ama diskte 8 SKILL.md var (kendi notu "DOĞRULAMA GEREKLİ") → tablo `.ai/index.md` §11B ile hizalanacak — Kaynak: `.ai/CLAUDE.md` §27A · `ls .opencode/skills/*` = 8 (2026-09-29)
- [ ] **dead-mark 8 hedefi karara bağla:** eşdeğer dosya üretilirse `⚠️ DEAD (faz6-D)` etiketi kaldırılıp retarget edilir; 1 `blocked` satır sahip kararı bekliyor — Kaynak: `.ai/broken-links-report.md` §B + §Kurallar
- [ ] **H026/H027 sözlük düzeltmesini kapat:** truth/hallucination terimleri `.ai/CLAUDE.md` #15/16 ile işaretlendi ama `glossary.md` hâlâ düzeltilmedi → vault-updater'a bırakılmış, açık — Kaynak: `.ai/log.md` 2026-09-29 "Acik konular (MO)"
- [ ] **Çakışan skill kurallarını tek kurala indir:** `orchestration` "max 400 satır" ↔ `skill-maker` "max 2000"; `truth-engine` "web arama yasak" ↔ `prompt-maker`/`skill-maker` zorunlu arama → karar + skill güncelleme — Kaynak: `.ai/log.md` 2026-09-29 "Acik konular (MO)"
- [ ] **`.claude/skills/composer-sync` frontmatter/dup-ID kararını ver:** `name`/`description` yok, duplicate ID (kaynak `.opencode/skills/composer-sync` işaretlendi) → frontmatter eklensin mi? — Kaynak: `.ai/log.md` 2026-09-29
- [ ] **Post-op sync scriptlerini karara bağla:** `.ai/scripts/session-save.mjs`, `vault-post-update.mjs`, `vault-cmd.mjs` ve `project-state.md` **diskte YOK** → kapanış manuel yapılıyor; script üretilsin mi (kod, onay gerektirir)? — Kaynak: `MEMORY.md` §20 Known Issue (2026-09-28) + filesystem taraması 2026-09-29

### §4 P2 — Kalite & teknik borç

- [ ] **Coverage ölçümü yok:** son phpunit sayıları `shared 216/545`, `auth unit 31/59`, `home 23/54` (geçen/toplam — coverage yüzdesi log'ta yok → `VERIFICATION REQUIRED`) → coverage raporu üretilip §17 hedefi (≥%80) karşılaştırılacak — Kaynak: `.ai/log.md` 2026-09-29 · `.ai/CLAUDE.md` §17
- [ ] **`/stream` endpoint'i için PHPUnit HTTP testi yok** (curl kanıtı logta, suite'e dokunulmadı) — Kaynak: `.ai/log.md` 2026-09-29 "Acik konular"
- [ ] **`.ai/CLAUDE.md` §8 Soft Constraint #3** "DOĞRULAMA GEREKLİ (Vault Steward)" → satır eklenecek ya da madde kaldırılacak — Kaynak: `.ai/CLAUDE.md` §8 · §31-5
- [ ] **`.ai/CLAUDE.md` içindeki 2 geçersiz referans:** §7.2 "ROLE.md §435 → Guardrail #15" (→ #2 güncellenmeli) ve §31 "§12A" (CLAUDE.md'de §12A yok) — ikisi de kapsam dışı bırakılmış VR — Kaynak: `.ai/CLAUDE.md` §7.2 · §31 notu
- [ ] **Vault metin hijyeni:** `log.md` içinde mojibake 6 / CJK 12 + **423 dosyada frontmatter yok** (zorla eklenmedi) → temizlik kararı — Kaynak: `MEMORY.md` §20 Known Issue · `.ai/log.md` 2026-09-29
- [ ] **14 case-variant duplicate başlık + süreler NULL (ffprobe yok)** → MVP'de kabul edildi, ileride import pass — Kaynak: `.ai/log.md` 2026-09-29 "Acik konular"
- [ ] **`.ai/index.md` §3 satır 15:** `TECHNICAL_DOCUMENTATION.md` "diskte yok" (dosyanın kendi VR notu) → kayıt düzeltilecek — Kaynak: `.ai/index.md` §3

---

## Validation

### §5 Uyum Kontrolü (2026-09-29)

| Kontrol | Sonuç | Kanıt |
|---|---|---|
| Aynı adlı eski doküman | **YOK** — vault/genel tarama `TODO`/`ROADMAP`/`PLAN` adına sahip vault dokümanı bulmadı; eşleşen 4 dosya PHPUnit vendor içindedir (kapsam dışı), 3 `*checklist*` dosyası `.claude/skills/**` şablonlarıdır → birleştirilecek çift doküman yok | glob `*TODO*`, `*ROADMAP*`, `*PLAN*` 2026-09-29 |
| Konum | `.opencode/.ai/` **yok**; SSOT Guardrail #5 gereği `.ai/TODO.md` yazıldı (çift vault yok) | Test-Path `.opencode/.ai` = False |
| Vault kurallarına aykırılık | Frozen ADR (001-037) değişikliği, dosya yeniden adlandırma (Guardrail #4) ve secret içeriği (REDACTED) **hiçbir maddeye eklenmedi** | `.ai/CLAUDE.md` §7 |
| Kanıtlanamayan iş çıkarıldı | **1 madde draft'tan çıkarıldı:** "ReturnUrlPolicy `//evil.com` protocol-relative bypass" → `2c0359b` (2026-09-29) ile kapatılmış (log girişi: `isAllowed('//evil.com/') false`) → TODO'ya alınmadı | `.ai/log.md` 2026-09-29 |
| Workflow çakışması | Maddeler mevcut akışlarla uyumlu: kapanış adımları `.workflows/vault-sync.md` Aşama 8 ve `MEMORY.md` §7 ile aynı; ADR işleri `.workflows/adr-creation.md` ve "yeni no ≥ 093" numara uzayına bağlı | `.workflows/vault-sync.md` · `MEMORY.md` §7 |

---

## References

### §6 Kaynak Haritası

| Öncelik | Kaynak türü | Dosya |
|---|---|---|
| P0 | git durumu (anlık ölçüm) | `git status --porcelain`, `git ls-files` |
| P1 | Vault tutarlılık kararları | `.ai/CLAUDE.md`, `.ai/.decisions/index.md`, `.ai/broken-links-report.md`, `.ai/log.md` |
| P2 | Kalite / teknik borç | `.ai/log.md`, `.ai/CLAUDE.md` §17-§31, `.ai/index.md`, `.ai/MEMORY.md` §20 |

**İlgili:** [[CHECKLIST.md]] · [[CLAUDE.md]] · [[WORKFLOW.md]] · [[MEMORY.md]] · [[log.md]] · `.workflows/vault-sync.md`

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Truth Mode · Human Mode
