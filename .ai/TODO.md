---
title: "CoreMusic — Proje TODO Listesi"
type: todo
category: project-tracking
version: 1.8.0
status: active
authority: SSOT
updated: 2026-10-25
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
- [x] **`app-cpp-projects/` sınıflandır** — KAPANDI 2026-10-24 — `git ls-files app-cpp-projects` = **5 md** (AGENTS/CLAUDE/CONTEXT/PRD/TODO) hepsi tracked → sınıflandırma tamam (commit edilmiş); C++ kodu `--/neva-engine/`'dedir (commit `9fd9991`) — Kaynak: `git ls-files` 2026-10-24

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
- [x] **`.ai/CLAUDE.md` §8 Soft Constraint #3** "DOĞRULAMA GEREKLİ (Vault Steward)" → KAPANDI 2026-10-24 — kullanıcı onayı: "Kabul et, notu kaldır"; §8 L623 satırı "Kullanıcı onayıyla kabul edildi (2026-10-24) — not kaldırıldı" olarak güncellendi — Kaynak: `.ai/CLAUDE.md` §8 · §31-5
- [x] **`.ai/CLAUDE.md` içindeki 2 geçersiz referans:** §7.2 "ROLE.md §435 → Guardrail #15" (→ #2) ve §31 "§12A" → KAPANDI 2026-10-24 — ikisi de düzeltildi: `.ai/ROLE.md` L442 #15 → #2 (Vault First birleşimi) + `.ai/CLAUDE.md` §7.2 notu ✅ ile kapatıldı; §31 tablo hücresi §12A → `[[ui-design/01-mockup-index]]` wiki-link'e çevrildi + §31 VR notu ✅ ile kapatıldı (kullanıcı onaylı) — Kaynak: `.ai/CLAUDE.md` §7.2 · §31 · `.ai/ROLE.md` L442 2026-10-24
- [ ] **Vault metin hijyeni:** `log.md` içinde mojibake 6 / CJK 12 + **423 dosyada frontmatter yok** (zorla eklenmedi) → temizlik kararı — Kaynak: `MEMORY.md` §20 Known Issue · `.ai/log.md` 2026-09-29
- [ ] **14 case-variant duplicate başlık + süreler NULL (ffprobe yok)** → MVP'de kabul edildi, ileride import pass — Kaynak: `.ai/log.md` 2026-09-29 "Acik konular"
- [ ] **`.ai/index.md` §3 satır 15:** `TECHNICAL_DOCUMENTATION.md` "diskte yok" (dosyanın kendi VR notu) → kayıt düzeltilecek — Kaynak: `.ai/index.md` §3
- [ ] **Neva M0 faz kapısı:** PRD'ler K-6 onaylı (2026-10-23) ama M0 yalnız **K-1 (architect) + K-5 (ürün sahibi)** kapıları geçtikten sonra açılır — Kaynak: `.ai/projects/NevaEngine/coremusic-neva-engine-prd.md` §20.1 · player §20.1
- [ ] **Player kritik açık maddeler (M4/PAKET'i bloke eder):** T-P01 UI toolkit ADR (§18.1) · T-P02 mockup gate (§18.7) · T-P03 ADR-0105 dosya iadesi (§18.14) — sahiplendirme + onay bekliyor — Kaynak: player PRD §18
- [x] **Nva PRD commit'i:** KAPANDI 2026-10-24 — commit `a44c567` ("docs: PRD+TODO ailesi ... + Neva PRD vault kopyalari + vault index/log/CONTEXT senkronu") — Kaynak: `git log a44c567` 2026-10-24
- [x] **Yüzey doküman seti + TODO ailesi commit'i (2026-10-23):** KAPANDI 2026-10-24 — commit `a44c567` (16 PRD + revizyonlar + TODO ailesi) — Kaynak: `git log a44c567` 2026-10-24
- [ ] **6 eksik PRD (#22–#27) üretimi:** `auth/include` · `shared/database/migrations` · `shared/config` · `.ai/.sql` · `.ai/ui-design` · `.ai/.decisions` — kök `PRD.md` §5 tablolarında satır var, dosya diskte YOK (çelişki 2026-10-23 tespit edildi, kapsam dışı bırakıldı) → üretilince ilgili yüzey TODO'suna + kök `TODO.md` §2'ye bağlanır — Kaynak: glob + `PRD.md` §5 2026-10-23

---

- [ ] **ADR-111 debate + kapanış (taslak YAZILDI: `draft/ADR-111-amfi-urun-ailesi-power-governor-18650-bypass.md`, 2026-10-25) ve belge hattı:** gereksinim kaydı yazıldı — `[[electronic/amplifier/product-line-requirements]]` v1.0.0 (2026-10-25) · açık: 2+1 SKU netleştirme · 1000 W bas kabı ↔ 100 W kanal güç bütçesi (P0, hesap) · 8+1 topoloji kararı (P0, Class D adayı — `index.md` §4) · `PROJECTS.md` §7.2.x + `VISION.md` §10.2 SKU revizyonu · `pro.coremusic.net` PRD satırı (`PRD.md` §5'te YOK) · `.ai/index.md` + `keys.md` katalog satırı · ADR-089 (8×50W studio/home) korunur — Kaynak: kullanıcı kararı 2026-10-25 + `product-line-requirements.md` §6
- [ ] **Amfi kodlama paneli izleme (T-01..T-10 + T-PWR):** görev kartları `[[electronic/amplifier/coding-panel]]` v1.0.0 — 5 kart paralel agent ile BAŞLADI (ADR-111 taslağı · PROJECTS/VISION SKU revizyonu · hoparlör profili BCNF şeması · Power Governor API sözleşmesi · panel ekran spec Kalıp D); açık P0: T-06 1000W/100W bütçe · T-07 8+1 topoloji; çıktılar vault'a MO (vault-utf8-writer) ile işlenir — Kaynak: kullanıcı direktifi 2026-10-25- [x] **Amfi güçlendirme turu KAPANDI (2026-10-25):** 11 vault md yazıldı/güncellendi — 4 yeni (`product-line-requirements` · `coding-panel` 16 kart · `power-governor-screen-spec` · ADR-111 draft) + `future-roadmap` (74 satır) + PROJECTS §7.2.7-9 (v3.2.0) + VISION §6/§10.2/§16.2 (v3.2.0) + PRD.md (v1.3.1: ADR-111 + 35. satır) + `.decisions/index.md` (draft 7 · §4A) + brain.md (2026-10-25 bölümü, v26.3.0) + root TODO `pro` maddesi; 1000W kabin çelişkisi kapandı — Kaynak: `git status` 9 M + 6 yeni · amplifier scan 0## §4A Faz/Part Çalışma Planı — Toplu Çalıştırma (2026-10-25)

> Her **PART** = tek oturumda toplu çalıştırılabilir paket. Satırlar ilgili dosyalarda `[ ]`. Sıra: **A∥B → C → D → E → F**; **G** her zaman en sonda. PART içi MICRO'lar **paralel**, ORTA adımları **sıralı**.

### PART A — PARALEL MİKRO TEMİZLİK (Oturum 1 · onaysız · hepsi paralel)

- **Kapsam (22 mikro):** home `H-1a H-4a H-5a` · auth `A-1a A-3a A-4a` · admin `N-1a N-2a N-3a` · api `G-1a G-2a G-4a` · shared `S-3a S-5a` · media `M-1a M-3a M-4a` · assets `F-1a F-4a` · node `P-1a P-1b P-4a` · cpp `C-1a C-2a C-4a` · `.ai` `T-09`
- **YAPMA:** üretim kodu değişikliği · migration çalıştırma · commit · PRD üretimi (PART D) · `[x]` yazma (kanıtsız kapatma yasak).
- **Toplu komut:** `PART A: aşağıdaki mikro adımları tek oturumda PARALEL yap ve her birinin sonucunu ilgili TODO'da kanıt satırıyla raporla (kod DEĞİŞTİRME, [x] YAZMA): home H-1a/H-4a/H-5a; auth A-1a/A-3a/A-4a; admin N-1a/N-2a/N-3a; api G-1a/G-2a/G-4a; shared S-3a/S-5a; media M-1a/M-3a/M-4a; assets F-1a/F-4a; node P-1a/P-1b/P-4a; cpp C-1a/C-2a/C-4a; T-09 index/keys.`

### PART B — KARAR OTURUMU (Oturum 2 · **SENİN ONAYIN** · 8 karar)

| # | Karar | Dosya | Seçenekler |
|---|---|---|---|
| B1 | **T-07 8+1 topoloji (P0 — tek blocker)** | coding-panel T-07 | Class D (index §4 öncelikli) vs Class AB |
| B2 | 2+1 SKU okuma | product-line §2 | 35W/kanal + 10W bas vs tersi |
| B3 | G-4 route yüzeyi | api G-4 | `api.coremusic.net` vs `pro` |
| B4 | N-3 phpunit cache | admin N-3 | ignore vs tracked |
| B5 | M-2 Faz 3 GUI | media M-2 | ayrı görev vs home genişlemesi → **KARAR: admin panel (ADR-115, 2026-11-01)** |
| B6 | C-1 kod taşıma | cpp C-1 | `--/neva-engine`'de kalır vs taşınır |
| B7 | P-4 coverage kapı | node P-4 | %75 korunur vs revize |
| B8 | M-3 port | media M-3 | :5000/:6000 netleştirme |

- **YAPMA:** karar ADR'siz/kayıtsız uygulanmaz → her sonuç ADR-111 / PRD / TODO'ya işlenir.
- **Toplu komut:** `PART B: 8 kararı sırayla öner (her biri için gerekçe + dosya/kanıt), onayımı al, hepsini ADR-111 ve ilgili PRD/TODO'ya işle.`

### PART C — POWER GOVERNOR KODU (Oturum 3 · **SIRALI** · shared + api)

1. `S-3` migration yaz (BCNF taslağı hazır — **çalıştırma YOK**) → 2. shared `composer test` yeşil → 3. `S-4` API 7 uç + DTO + RouteConfigTest + audit (PDO prepared) → 4. `G-4` kararı sonucu route kaydı → 5. panel sözleşmesi testleri (PNG'siz, yalnız API tarafı).
- **YAPMA:** migration çalıştırma (production onay ister) · middleware sırası · `public:true` · commit.
- **Toplu komut:** `PART C: Power Governor kodunu SIRAYLA uygula — önce S-3 migration (vault'taki taslak, DOWN yorumlu), sonra S-4 7 uç + DTO + RouteConfigTest + audit log; HER adımdan sonra composer test = yeşil → dur + rapor. Migration ÇALISTIRMA, commit ATMA.`

### PART D — BELGE KAPANIŞI (Oturum 4 · paralel yazım)

7 PRD: `A-1` auth/include · `F-2` ui-design #26 · `S-1` migrations+config (×2) · `M-1` .sql #25 · **`T-08` pro yüzeyi** (B onayıyla) → kök 7 madde kapanır · `F-1` alt PRD bağlama · **ADR-111 debate 3 tur → accepted** (onayım) · `T-09` index/keys · root+.ai senkron.
- **YAPMA:** şablonsuz PRD (7 alanlı FM + §6 kapıları zorunlu) · frozen ADR değişikliği · onaysız yeni dizin.
- **Toplu komut:** `PART D: 7 eksik PRD'yi 7 alanlı FM ile üret + F-1 bağlama + ADR-111 debate 3 turunu çalıştır (20 persona) → terfi metnini onaya sun; onayla → accepted + sayaç güncelle.`

### PART E — DOĞRULAMA TURU (Oturum 5 · server PC)

coverage ≥%80 · curl ×3 (`backend-step09/10/11-curl-pc.ps1`) + KAPI-E browser · `P-5` MySQL canlı · `N-2` 429 teyit · `A-3` security-audit · `F-3` accessibility · `G-2` ölçüm teyit · turbo+ctest yeşil teyidi.
- **YAPMA:** dev makinede (MySQL YOK) "geçti" iddiası · **kanıtsız `[x]`**.
- **Toplu komut:** `PART E: server PC'de doğrulama turu — 3 curl script + composer test + KAPI-E + coverage raporu + security-audit + accessibility-tester; HER sonucu ilgili PRD §4/§7'ye kanıtla yaz.`

### PART F — COMMIT & KAPANIŞ (Oturum 6 · **COMMIT ONAYI**)

34 değişiklik gruplı commit paketi → tüm suite yeşil → kapanan maddeler `[x]` + kanıt → `vault-post-update` → **ui-design Faz 8 QA** (yalnız E yeşilse).
- **YAPMA:** benim onayım olmadan commit · Faz 8'i E'den önce tetikleme.
- **Toplu komut:** `PART F: 34 değişikliği incele, gruplı commit mesajlarını hazırla, onayımı al → commit at; kapananları [x]+kanıtla, vault-post-update çalıştır, Faz 8 QA'yı başlat.`

### PART G — BÜYÜK İŞLER (Faz 3+ · ayrı proje açılışları)

| # | İş | Ön koşul |
|---|---|---|
| G1 | **Neva M0** (K-1+K-5) → engine ilk `.cpp` → cmake/ctest → benchmark (ADR-0105 §5.6) | B onayı + D PRD |
| G2 | Faz 2: Class AB prototip PCB + DSP Engine v1 + üretim test fixture (`T-14`) | T-07 (B1) + hesaplar |
| G3 | Eksik 6 yüzey: **music → pro → car → studio → download → landing** | D (pro PRD) |
| G4 | DB paketi ADR-072..079 (social/podcast/radio/AI/video/studio/CMS/i18n) → migration + API | — |
| G5 | Streaming :5000/:6000 + NevaConnect multi-room | B8 port kararı |
| G6 | 2027+: 64-bit float çekirdek · Atmos · OEM otomotiv (Ay 28-36) | Faz 3 |

- **YAPMA:** benchmark'sız `IMPLEMENTED` etiketi · onaysız yeni dizin · hesapsız termal "geçti".## §4B Küresel Yürütme Sırası (2026-10-25 — SSOT)

> Bu bölüm tüm yüzey TODO'larının **sırasını** belirler; yüzey dosyaları kendi adımlarını buradan türetir. Çelişkide bu tablo kazanır.

| Sıra | Adım | YAPMA | Hazır komut |
|---|---|---|---|
| 1 | **T-07 P0** — 8+1 topoloji kararı (tek blocker, ADR-111) | topolojisiz 8+1 kodlama/PCB | `coding-panel.md` T-07 satırındaki komut |
| 2 | **T-10 kod paketi** — T-03 migration → T-04 API → T-05 panel (Power Governor) | **PNG'siz UI kodu** (mockup gate) · migration çalıştırma | `T-10 başlat: T-03/T-04/T-05 çıktılarını sırayla uygula` |
| 3 | **Commit paketi** — 28 M + 6 yeni kod değişikliği (rate-limit, UuidV7, handler testleri) sınıflandırma | **subagent commit atmaz** — MO/onay | `bekleyen 34 değişikliği incele ve commit mesajlarını hazırla` |
| 4 | **T-08 + T-09** — `pro.coremusic.net` PRD satırının dosyası + `.ai/index.md`/`keys.md` katalog | yeni dizin onaysız (Human Approval Gate) | `T-08: pro PRD üret · T-09: index/keys kayıtlarını ekle` |
| 5 | **Backend 09-11 canlı doğrulama** (server PC: curl 3 script + `composer test` + KAPI-E) | dev makinede (MySQL yok) "geçti" | `backend-step09/10/11-curl-pc.ps1 + composer test` |
| 6 | **Coverage raporu** ≥%80 hedefi (shared/auth/home) | kanıtsız "testli" | `coverage ölç ve .ai/TODO §4'e karşılaştır` |
| 7 | **7 eksik PRD** (#22-#27 + `pro`) | şablonsuz/kapısız PRD | `eksik 7 PRD'yi sırayla üret` |
| 8 | **Neva M0** (K-1 + K-5 onayı) → cmake/ctest | benchmark'sız IMPLEMENTED | `C-4: M0 kapılarını başlat` |
| 9 | **Faz 2 donanım** — T-06/T-14 hesap + üretim test fixture | hesapsız termal "geçti" | `T-14: üretim test fixture spec'i` |

**Kapalı bugün:** T-06 (1000W kabin bütçesi) · ADR-111 taslak · amfi güçlendirme turu (11 md).
## TAM Madde Listesi — Vault · Küçükten Büyüğe (Geçmiş · Açık · Gelecek) — 2026-10-25

> Merkezî liste (SSOT). Sıra: MICRO → ORTA → EPIC. Kapalı maddelerin tamamı: §2-§4 `[x]` (21 adet) + `## Geçmiş & Mikro Ölçek`.

### A. GEÇMİŞ — TAMAMLANAN (bugün + tarihsel özet)
- [x] MICRO: ADR-111 draft (97 satır) · next 111→**112** · total-draft 6→**7** · §4A + Draft satırı
- [x] MICRO: 1000W kabin bütçesi **kapandı** (kabin rating, sürücü 100W alt sürüş) · T-06 [x]
- [x] MICRO: kök TODO 2 bayat madde kapatıldı (`a44c567`) · PRD sayaç 34→35
- [x] ORTA: amplifier/ **10 dosya** — product-line (125) · coding-panel (71 · 16 kart) · screen-spec (134) · future-roadmap (74) · AGENTS/index senkron
- [x] ORTA: PROJECTS §7.2.7-9 + §17 6→9 + **v3.2.0** · VISION §6/§10.2/§16.2 + **v3.2.0**
- [x] ORTA: PRD **v1.3.1** (35 satır) · brain **v26.3.0** (+2026-10-25 bölümü) · MEMORY +3 · log +3
- [x] ORTA: TODO 11 dosya güçlendirme (adım + geçmiş + tam liste bölümleri)
- [x] EPIC: 5 paralel agent çıktısı (ADR-111 · PROJECTS/VISION · BCNF şema · API sözleşmesi · panel spec) vault'a işlendi
- [x] EPIC (tarihsel): ui-design Faz 0-7 · skill v3.0 `391a330` · persona 68/68 · glossary 112 · backend Adım 01-11 · SEC-01..05 · SESSIONS 6 kayıt (2026-10-25)

### B. AÇIK — EKSİK / YARIM (küçükten büyüğe)
- [ ] MICRO: `/stream` HTTP testi yok
- [ ] MICRO: `log.md` mojibake 6 / CJK 12 + 423 dosyada frontmatter kararı
- [ ] MICRO: `.ai/index.md` §3 L15 düzeltmesi · `.ai/CLAUDE.md` silinme nedeni (**UNKNOWN**)
- [ ] MICRO: 14 case-variant duplicate + ffprobe süre NULL (MVP'de kabul)
- [ ] MICRO: 2+1 SKU okuma belirsizliği (kullanıcı netleştirecek)
- [ ] MICRO: T-09 `.ai/index.md` + `keys.md` katalog kayıtları
- [ ] ORTA: Coverage ölçümü ≥%80 (son sayılar 2026-09-29 bayat)
- [ ] ORTA: Backend 09-11 canlı + KAPI-E (server PC — dev'de MySQL YOK)
- [ ] ORTA: port :5000/:6000 netleştirme · turbo gate · cmake/ctest · rate-limit 429 · Faz3 GUI kararı
- [ ] ORTA: ADR-111 **debate + kullanıcı onayı** (draft → accepted)
- [ ] ORTA: `pro.coremusic.net` PRD+TODO (yeni dizin = Human Approval Gate) · T-05 PNG gate
- [ ] ORTA: Player **T-P01/T-P02/T-P03** (M4/PAKET'i bloke eder)
- [ ] EPIC: **34 kod değişikliği commit** (onay bekliyor)
- [ ] EPIC: **7 eksik PRD** (#22-#27 + pro)
- [ ] EPIC: ui-design **Faz 8 QA** (`04-verification`)
- [ ] EPIC: **Neva M0** (K-1 + K-5) → 0 .cpp
- [ ] EPIC: Power Governor ürünleşmesi · 18650 MPN/akım/SoH hesabı (`⚠️ VR`)
- [ ] EPIC: **T-07 8+1 topoloji = TEK P0 BLOCKER**

### C. GELECEK — ÖNCELİK SIRASI (küçükten büyüğe, 15 sıra)
| Sıra | Ölçek | Adım |
|---|---|---|
| 1 | MICRO | T-07 topoloji kararı (P0) |
| 2 | MICRO | S-3 migration dosyası (çalıştırma yok) |
| 3 | MICRO | N-1 `/dev-login` prod kanıtı (CRITICAL) |
| 4 | MICRO | P-1 turbo gate · C-2 ctest |
| 5 | MICRO | M-3 port ölçümü · T-09 index/keys |
| 6 | ORTA | T-04 API 7 uç + audit · G-4 route yüzeyi kararı |
| 7 | ORTA | T-03 migration+test · 7 PRD üretimi · pro yüzeyi |
| 8 | ORTA | Coverage raporu + canlı curl ×3 + KAPI-E |
| 9 | ORTA | Commit paketi (34 değişiklik) → onay → MO |
| 10 | EPIC | Power Governor'ın ürünleşmesi (canlı) |
| 11 | EPIC | Neva M0 → cmake/ctest → engine ilk kodu |
| 12 | EPIC | Faz 2: Class AB prototip PCB + DSP Engine v1 |
| 13 | EPIC | Eksik 6 yüzey: music → pro → car → studio → download → landing |
| 14 | EPIC | DB paketi ADR-072..079 (social/podcast/radio/AI/video/studio/CMS/i18n) |
| 15 | EPIC | Faz 3: OEM otomotiv + global donanım satışı (Ay 28-36) |## Geçmiş & Mikro Ölçek — Vault Tamamı (2026-10-25)

**Tamamlanan (kanıtlı, küçükten büyüğe):**
- [x] `[MICRO]` ADR-111 draft (97 satır, debate PENDING) · next 111→112 · total-draft 6→7
- [x] `[MICRO]` 1000W kabin bütçesi kapandı (kullanıcı: kabin rating, sürücü 100W alt sürüş)
- [x] `[MICRO]` T-06 [x] · kök TODO 2 bayat madde [x] (`a44c567`) · PRD 6→7 eksik sayacı
- [x] `[ORTA]` amplifier/ 10 dosya: product-line-requirements (125 satır) · coding-panel (71, 16 kart) · power-governor-screen-spec (134) · future-roadmap (74) · AGENTS (10 dosya) + index 4 satır senkron
- [x] `[ORTA]` PROJECTS §7.2.7-9 (SKU + Power Governor + 18650) + §17 sayaç 6→9 + v3.2.0
- [x] `[ORTA]` VISION §6 +PA · §10.2 +2 SKU · §16.2 +2 gelecek + v3.2.0
- [x] `[ORTA]` PRD v1.3.1 (35 satır + ADR-111 satırı) · brain v26.3.0 (+2026-10-25 bölümü, 1664 bayt)
- [x] `[ORTA]` MEMORY +3 satır · log.md +3 satır · SESSIONS 6 kayıt (2026-10-25)
- [x] `[ORTA]` TODO 11 dosya güçlendirme: adım-adım (33/33) + geçmiş bölümleri (23/23)
- [x] `[EPIC]` 5 paralel agent çıktısı vault'a işlendi: ADR-111 · PROJECTS/VISION revizyonu · BCNF şema (3 tablo) · Power Governor API sözleşmesi (7 uç) · panel ekran spec (Kalıp D)
- [x] `[EPIC]` vault-post-update 3 tur: 0 başarısız · verify: BOM/mojibake/CJK = 0 (amplifier + TODO'lar)

**Mikro işler (en küçük → büyük):**
- [ ] `[MICRO]` T-09: `.ai/index.md` katalog satırı ekle (4 yeni md için)
- [ ] `[MICRO]` T-09: `keys.md` anahtarları ekle (Power Governor · 18650 · SKU · topoloji)
- [ ] `[MICRO]` `log.md` mojibake 230 + CJK 18 taraması → temizlik kararı (repair dene)
- [ ] `[ORTA]` ADR-111 debate 3 tur → accepted terfi (onayınla)
- [ ] `[EPIC]` `.ai/index.md` + `RAG.md` §3 + `keys.md` üçlü senkronu (4 amp md + 11 TODO)## Validation

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
