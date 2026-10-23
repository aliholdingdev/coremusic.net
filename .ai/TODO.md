---
title: "CoreMusic — Proje TODO Listesi"
type: todo
category: project-tracking
version: 1.4.0
status: active
authority: SSOT
updated: 2026-10-23
# Control Plane v2 (Q7 geniş şema — 2026-10-07):
tier: 5
domain: task-state
ssot: false
risk: low
owner: "MO"
depends-on: []
---

# CoreMusic — Proje TODO Listesi

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[MEMORY.md]] · [[index.md]] · [[log.md]] · [[CHECKLIST.md]] · [[../TODO]] (kök TODO aile indeksi)

**Kural (Zero-Hallucination):** Aşağıdaki **her madde disk kanıtına dayanır**; her satırda kaynak (commit / dosya / ölçüm) yazılır. Kanıtlanamayan hiçbir madde buraya yazılmaz; okurken emin olunmayan iddia → `VERIFICATION REQUIRED`.

**Durum:** `[ ]` = açık · `[x]` = kapalı (kapanışta [[CHECKLIST.md]] §C3 ile güncellenir).

---

## Purpose

### §1 Amaç

Depodaki gerçek iş kalemlerini (açık commit'ler, eksik testler, vault tutarsızlıkları, karar bekleyenler) öncelik sırasıyla tek listede tutmak; her session başında [[CHECKLIST.md]] §A3 ile taranmak.

---

## Workflow

### §2 P0 — Depo bütünlüğü & çalışır durum (önce bunlar)

- [x] **Commit bekleyen 105 değişikliği sınıflandır ve commit'e hazırla** — KAPANDI 2026-10-23 — çalışma ağacı temiz: `git status --porcelain` = tek satır (`?? app-cpp-projects/`); 105 değişiklik commit serisiyle kapandı (`961244a` → `96eb63f` → `f0e15eb` → `af5ea57` → `2017410`) — Kaynak: `git status` 2026-10-23
- [x] **Interface → Contract taşımasını tamamla** — KAPANDI 2026-10-23 — `git ls-files`: `shared/src/Interfaces`=0 · `shared/src/AI/Contracts`=0 · `shared/src/Contracts`=20 tracked (6 dizin) → taşıma commit'lenmiş, eksik/stage bekleyen yok — Kaynak: `git ls-files` 2026-10-23
- [x] **auth testlerindeki çift ağacı tek ağaç yap** — KAPANDI 2026-10-23 — `git ls-files`: `tests/Domain/**`=0 · `tests/Unit/Domain/**`=227 tracked (tek ağaç); eski 4 dosya worktree'de silinmiş ve silinmesi commit'lenmiş — Kaynak: `git ls-files auth.coremusic.net/tests` 2026-10-23
- [x] **Kökteki stash/artık dosyaları temizle** — KAPANDI 2026-10-23 — `.ai.zip` karar verildi: **SİLİNMEDİ** (`.gitignore:77`'de, REDACTED taraması: 1764 metin dosyası → gerçek secret 0; 16 isabet = presigned-S3/Figma AKIA'sı); `coremusic.net.zip`/`aaa.md`/`output.md` kökten gitmiş (`aaa.md` → `--\aaa.md` altında) — Kaynak: `Test-Path` + `git check-ignore` 2026-10-23
- [x] **Untracked kaynak varlıklarını commit et** — KAPANDI 2026-10-23 — `git ls-files`: `.gitleaks.toml` tracked (`43e6763`) · `ADR-048/049/050` (3 dosya) tracked · `home.coremusic.net/include/Repository/` 2 file · `shared/src/OAuth/OAuthRepository.php` 1 file → 4 küme de commit'lenmiş — Kaynak: `git ls-files` 2026-10-23
- [ ] **`app-cpp-projects/` sınıflandır** — kökte untracked `??` → commit / `.gitignore` / taşıma kararı — Kaynak: `git status --porcelain` + `ls -R` 2026-10-23 — **NOT 2026-10-23 (yeniden ölçüm):** klasörde artık yalnız 5 doküman md'si var (AGENTS/CLAUDE/CONTEXT/PRD/TODO — bu oturum yazıldı); C++ kodu `--/neva-engine/`'dedir (commit `9fd9991`); "163 dosya / 16.6 MB" iddiası şu an disk ile ÇELİŞİYOR → madde `app-cpp-projects/TODO.md` üzerinden yeniden ölçülüp kapatılabilir

### §3 P1 — Vault tutarlılığı & karar bekleyenler

- [x] **ADR numara çelişkisini çöz** — KAPANDI 2026-10-23 (Vault Steward onaylı) — kök `CLAUDE.md`'de "next = 091" iddiası artık YOK (boot yeniden yazılmış); `.ai/.decisions/index.md` v1.5.2 GÜNCEL; `.ai/CLAUDE.md` §29 hizalandı: **101 karar (accepted 84 · rejected 10 · draft 6 · active 49) → next 111** (draft: 103/104/107/108/109/110; eski kayıtlar korundu) — Kaynak: `Get-ChildItem .ai/.decisions` + `index.md` frontmatter 2026-10-23
- [x] **§27A skills tablosunu düzelt** — KAPANDI 2026-10-23 — `.ai/CLAUDE.md` §27A hizalandı: başlık 8 → **13 Aktif Skill** (disk: `.claude/skills/*/SKILL.md` = 13 — `git ls-files`); tablo 13 disk skill ile yeniden yazıldı, "DOĞRULAMA GEREKLİ" kapandı; eski envanterden diskte olmayan 3 satır (`hallucination-control` · `human-mode` · `red-team-truth-mode`) ADR-042 gereği silinmedi, notla korundu (durum UNKNOWN — sahip kararı); `.opencode/skills/` (kapatıldı 2026-10-07) referansı kaldırıldı — Kaynak: `.ai/CLAUDE.md` §27A · `.claude/skills/` sayımı 2026-10-23
- [x] **dead-mark 8 hedefi karara bağla** — KAPANDI 2026-10-23 (sahip kararı: yeni tarama) — kaynak `.ai/broken-links-report.md` diskte/tracked/history'de YOK (`.ai/index.md` L216: "ölü referans — kaldırıldı"); yeniden tarama `wiki-link-check.ps1` (kökten, §13.6): **389 link / 6 kırık = hepsi §13.7 bilinen sahte** (01-login [[C]] · 04-select-gender [[V]] · 01-playback [[V]] · 04-general [[V]] · legacy-inventory [[link]] ×2) → **gerçek dead-mark 0**; vault md'lerinde "⚠️ DEAD (faz6-D)" etiketi = 0 isabet → kapanacak dead-mark yok — Kaynak: `wiki-link-check.ps1` çıktısı 2026-10-23
- [x] **H026/H027 sözlük düzeltmesini kapat** — KAPANDI 2026-10-23 — `glossary.md` v2.4.0: §2'ye **+2 terim** eklendi (**Truth Mode** — kanıt zorunluluğu kipi, `.ai/AGENTS.md` §3 · **Zero-Hallucination** — bilinmeyen `UNKNOWN`/doğrulanamayan `VERIFICATION REQUIRED`, `.ai/AGENTS.md` §3) → §2 34 · toplam 96. Not: madde kaynağındaki "#15/16" atfı doğrulanamadı (CLAUDE.md #15 Vault-First birleştirilmiş, #16 Template Mandatory — terimle ilgisiz) ve log.md 2026-09-29 kaynağı artık yok; düzeltme terim özüne göre yapıldı — Kaynak: `glossary.md` §2 + `.ai/AGENTS.md` §3 2026-10-23
- [x] **Çakışan skill kurallarını tek kurala indir** — KAPANDI 2026-10-23 — ölçüm: "max 400 satır" kuralı hiçbir `.claude/skills/` SKILL.md'de YOK (eski `orchestration` kapatıldı, kural taşınmadı); "500-2000 satır" bandı 7 skill'de tutarlı (skill-maker N3 revize 2026-10-21 = SSOT); `truth-engine` diskte YOK (kapatıldı) ve Truth Mode artık **zorunlu kaynak defteriyle** web aramasına açık (ui-analyzer `sources.md`: 4 kaynak doğrulandı) → çakışan kural kalmadı, mevcut skill'ler hizalı — Kaynak: `.claude/skills/**` grep 2026-10-23
- [x] **`.claude/skills/composer-sync` frontmatter/dup-ID kararını ver:** KAPANDI 2026-10-21 — v3.0 yeniden yazımda frontmatter eklendi (`name: composer-sync` + `description` "Use when…", name=klasör 1:1); duplicate ID kaynağı `.opencode/skills/` kapatıldı (2026-10-07) — Kaynak: commit 391a330 + composer-sync/SKILL.md L2-3 ölçümü
- [x] **Post-op sync scriptlerini karara bağla** — KAPANDI 2026-10-23 — karar: **script üret** (kullanıcı onayı alındı). Üretildi ve test edildi: `.ai/scripts/session-save.mjs` (şablon doldurma + writer write; `--help` exit 0 · dry-run 0 placeholder · eksik argüman exit 2) · `.ai/scripts/vault-post-update.mjs` (`--scope root|full` + `--dry-run`; dry-run guard düzeltmesi sonrası full dry-run 17 hedef/0 yazma — git status kanıtı; **not:** ilk dry-run'da guard eksikliğiyle3 boot dosyasının frontmatter `updated:` alanı yazıldı — içerik kaybı0, amaçlanan tazelikle aynı, log'a kaydedildi) · `.ai/scripts/vault-cmd.mjs` (7 Türkçe komut + kök dışı yazma engeli — `C:/Windows/Temp` denemesi ENGELLENDI exit 2). `project-state.md` **üretilmez** (SKILL.md §9 kuralı — Aşama 8 doğrulaması `log.md`+`MEMORY.md` §20 üzerinden). Hizalama: `.workflows/vault-sync.md` Aşama 8 ⚠️ notu kaldırıldı · SKILL.md §1/§7/H6/§9 güncellendi · MEMORY.md §20 Known Issue → "script'li" · `scripts/index.md` v1.1.0 (12 script envanter) — Kaynak: disk ölçümü + test çıktısı 2026-10-23

- [x] **`.ai/AGENTS.md` §14 skill sayımı bayat:** KAPANDI 2026-10-23 (Adım 1) — §14 + Skills satırı düzeltildi: 12+5=17 → 13+0=13 (disk: `.claude/skills/` 13 SKILL.md, `C:\.claude\skills` bu makinede YOK — diğer PC/server'da mevcut, silinmez · kullanıcı notu 2026-10-23); kök `CLAUDE.md`'ye §Skill Registry eklendi; eski tespit: "12 proje `.claude/skills/` + 5 global = 17" yazıyor ama diskte **13** dizin (ölçüm 2026-10-21, `.claude/CONTEXT.md` 13 dizin/134 dosya ile hizalı) → §14 satır edit ile düzeltilecek (bu kapanışta surgical-change sınırı nedeniyle dokunulmadı) — Kaynak: `.ai/AGENTS.md` §14 · `.claude/CONTEXT.md` §3 (2026-10-21)
- [ ] **Backend Rebuild Adım 09-11 canlı doğrulaması (server PC):** `git pull` + `CM_TEST_EMAIL`/`CM_TEST_PASSWORD` ile `backend-step11-curl-pc.ps1` (11/11 hedef) + `backend-step10-curl-pc.ps1` (13) + `backend-step09-curl-pc.ps1` + `composer test`; ardından KAPI-E browser (dropdown XHR-only geçiş, rol A/B, Enter/Esc) — dev makinede MySQL YOK → ⚠️ VERIFICATION REQUIRED - Kaynak: `.ai/reports/backend-step11-dropdown.md` §KAPI-E + 3 script (2026-10-22)
- [x] **Adım 4 — G: Prompt Arşivi revizyonu (O-4 onaylı):** KAPANDI 2026-10-23 — yeniden yazım tamamlandı (önceki oturum + arka plan ajanı); bu oturum: README sayı hizası (§2.1 13 · §2.5 7 · §4 accepted 84 · toplam 41 içerik) + `miamri→mimari` rename disk teyidi (273 KB, 15:48) + brain.md L1130 mutabakatı ("14+7" = stale kapsam subset'i, "41" = toplam — çelişki yok) — Kaynak: G:Coremusic/ disk sayımı + README (2026-10-23)
- [ ] **`.ai/CLAUDE.md` silinme olayı (2026-10-23):** çalışma ağacından silinmişti (git D, 987 satır, commit edilmemiş) → `git checkout` ile kurtarıldı (v27.5.0, 3×hash eşleşti); **neden silindiği bilinmiyor (UNKNOWN)** → tekrarını önleme araştırması — Kaynak: git status D + AGENTS §17.10

### §4 P2 — Kalite & teknik borç

- [ ] **Coverage ölçümü yok:** son phpunit sayıları `shared 216/545`, `auth unit 31/59`, `home 23/54` (geçen/toplam — coverage yüzdesi log'ta yok → `VERIFICATION REQUIRED`) → coverage raporu üretilip §17 hedefi (≥%80) karşılaştırılacak — Kaynak: `.ai/log.md` 2026-09-29 · `.ai/CLAUDE.md` §17
- [ ] **`/stream` endpoint'i için PHPUnit HTTP testi yok** (curl kanıtı logta, suite'e dokunulmadı) — Kaynak: `.ai/log.md` 2026-09-29 "Acik konular"
- [ ] **`.ai/CLAUDE.md` §8 Soft Constraint #3** "DOĞRULAMA GEREKLİ (Vault Steward)" → satır eklenecek ya da madde kaldırılacak — Kaynak: `.ai/CLAUDE.md` §8 · §31-5
- [ ] **`.ai/CLAUDE.md` içindeki 2 geçersiz referans:** §7.2 "ROLE.md §435 → Guardrail #15" (→ #2 güncellenmeli) ve §31 "§12A" (CLAUDE.md'de §12A yok) — ikisi de kapsam dışı bırakılmış VR — Kaynak: `.ai/CLAUDE.md` §7.2 · §31 notu
- [ ] **Vault metin hijyeni:** `log.md` içinde mojibake 6 / CJK 12 + **423 dosyada frontmatter yok** (zorla eklenmedi) → temizlik kararı — Kaynak: `MEMORY.md` §20 Known Issue · `.ai/log.md` 2026-09-29
- [ ] **14 case-variant duplicate başlık + süreler NULL (ffprobe yok)** → MVP'de kabul edildi, ileride import pass — Kaynak: `.ai/log.md` 2026-09-29 "Acik konular"
- [ ] **`.ai/index.md` §3 satır 15:** `TECHNICAL_DOCUMENTATION.md` "diskte yok" (dosyanın kendi VR notu) → kayıt düzeltilecek — Kaynak: `.ai/index.md` §3
- [ ] **Neva M0 faz kapısı:** PRD'ler K-6 onaylı (2026-10-23) ama M0 yalnız **K-1 (architect) + K-5 (ürün sahibi)** kapıları geçtikten sonra açılır — Kaynak: `.ai/projects/NevaEngine/coremusic-neva-engine-prd.md` §20.1 · player §20.1
- [ ] **Player kritik açık maddeler (M4/PAKET'i bloke eder):** T-P01 UI toolkit ADR (§18.1) · T-P02 mockup gate (§18.7) · T-P03 ADR-0105 dosya iadesi (§18.14) — sahiplendirme + onay bekliyor — Kaynak: player PRD §18
- [ ] **Nva PRD commit'i:** vault kopyaları (2 dosya) + kök `PRD.md` §5.1 + `AGENTS.md` §9 + `MEMORY.md` §20 + `TODO.md` + session kaydı + log append — commit orkestratörde bekliyor — Kaynak: `git status` 2026-10-23
- [ ] **Yüzey doküman seti + TODO ailesi commit'i (2026-10-23):** 16 PRD (admin 5 · app-nodejs 4 · app-cpp 4 · `.claude`/`.opencode`/`.workflows` 3) + revizyonlar (kök `PRD.md` v1.2.0 §5 34 satır · `.ai/PRD.md` v1.0.1 · `admin/PRD.md` §11 · `.ai/index.md` L104 · `.ai/log.md` append) + TODO ailesi (kök `TODO.md` aile indeksi + 9 yüzey `TODO.md` + bu dosya v1.4.0) — hepsi UNCOMMITTED, commit orkestratörde — Kaynak: `git status` 2026-10-23
- [ ] **6 eksik PRD (#22–#27) üretimi:** `auth/include` · `shared/database/migrations` · `shared/config` · `.ai/.sql` · `.ai/ui-design` · `.ai/.decisions` — kök `PRD.md` §5 tablolarında satır var, dosya diskte YOK (çelişki 2026-10-23 tespit edildi, kapsam dışı bırakıldı) → üretilince ilgili yüzey TODO'suna + kök `TODO.md` §2'ye bağlanır — Kaynak: glob + `PRD.md` §5 2026-10-23

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
**Last Updated:** 2026-10-23
**Mode:** Red Team · Truth Mode · Human Mode
