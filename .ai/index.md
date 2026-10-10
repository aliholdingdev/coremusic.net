---
title: "CoreMusic Vault — Master Index"
type: system
category: vault-navigation
status: active
authority: SSOT
version: 29.0.0
updated: 2026-10-10
date: 2026-08-09
total_root_files: 20
total_root_md: 20
total_ai_dirs: 17
total_recursive_files: 804
total_recursive_md: 572
total_recursive_dirs: 107
total_adr: 97
total_adr_accepted: 84
total_adr_rejected: 12
total_adr_draft: 1
# Control Plane v2 (Q7 geniş şema — 2026-10-07):
tier: 5
domain: navigation
ssot: false
risk: low
owner: "MO"
depends-on: []
---

# CoreMusic Vault — Master Index

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]] · [[.templates/index]] · [[.agents/AGENTS.md]] · [[SYNC]] · [[CONTEXT]] · [[RAG]]

**Skills:** Registry SSOT = kök `CLAUDE.md` §Skill Registry — **13 proje skill'i** (`.claude/skills/`, 2026-10-10 disk ölçümü; `C:\.claude\skills` ve `.opencode/skills` diskte YOK).

---

## Purpose

### §1 Amaç

Bu dosya, CoreMusic `.ai/` vault'unun ana navigasyon noktasıdır. Tüm vault dosyaları kategorize edilmiş ve hızlı erişilebilir biçimde listelenir. **Bu dosya hub'dır**: içerik alt sayfalarda (`index/01-05`), künye buradadır.

---

## Scope

### §2 Quick Reference

| İhtiyaç | İlk Adım |
|---------|----------|
| Vault genel bakış | Bu dosya (index.md) |
| Ürün & Ekosistem Vizyonu | [[VISION.md]] |
| Proje Tanımı & Hangi Sorunları Çözer | [[PROJECTS.md]] |
| Keyword arama | [[keys.md]] |
| Konu→dosya retrieval indeksi (hangi soru hangi dosya) | [[RAG.md]] |
| Terimler Sözlüğü | [[glossary.md]] |
| Mimari kararlar (özet) | [[brain.md]] |
| Mimari kararlar (ADR arşivi) | `.ai/.decisions/index.md` (97 dosya: 84/12/1) |
| Katman mimarisi (21 K000-K020) | [[architecture/00-master-index]] |
| Bağımlılık kenarları | [[architecture/katman-baglilik-matrisi]] |
| Sıfırdan mimari plan | [[architecture/coremusic-mimari-plani]] |
| Ajan yetkileri | [[AGENTS.md]] |
| Agent profilleri | [[.agents/AGENTS]] (11 profil) |
| Süreçler | [[WORKFLOW.md]] |
| Bellek yönetimi | [[MEMORY.md]] |
| Aktivite günlüğü | [[log.md]] |
| Sürekli güncelleme döngüsü (baş/orta/son) | [[SYNC.md]] (hedef set 26) |
| Session Checklist | [[CHECKLIST.md]] (§A/§B/§C, 5'er madde) |
| Context Planner spec | [[PLANNER.md]] (Control Plane, 13 alan FM girdisi) |
| ADR kataloğu | §5 bu dosya |
| Servis haritası | §6 bu dosya |
| Veritabanı | §8 bu dosya |
| UI / Mockup / Frontend | [[ui-design/01-mockup-index]] (19 PNG, C01-C16, 45-Tier) |
| Şablon seçimi (Guardrail #16) | [[.templates/index]] (69 dosya · 13 kategori) |
| SQL şema | `.ai/.sql/` (20 `.sql`, 4 DBMS) |
| Açık İş Listesi | [[TODO.md]] (P0/P1/P2) |
| Link/ADR denetimi | `.ai/scripts/wiki-link-check.ps1` (kökten çalıştır) · `.ai/broken-links-report.md` **diskte YOK** (işte üretilir) |

---

### §3 SSOT Core Dosyaları (20 `.ai/` kök `.md` — 2026-10-10 disk ölçümü)

| # | Dosya | Amaç | Sınıf |
|---|-------|------|-------|
| 1 | [[CLAUDE.md]] | Kanonik AI talimatı — boot protokolü §16, 16 guardrail §7, yasaklar §21 | 🔴 CRITICAL |
| 2 | [[AGENTS.md]] | Agent kayıt defteri — 11 uzmanlık + MO, routing §6, handover §9, escalation §10 | 🔴 CRITICAL |
| 3 | [[WORKFLOW.md]] | Süreçler — fazlar, kapılar, ADR lifecycle §8-§13 | 🔴 CRITICAL |
| 4 | [[index.md]] | Bu dosya — vault navigasyon hub'ı | 🔴 CRITICAL |
| 5 | [[keys.md]] | Anahtar kelime haritası — keyword → dosya yönlendirme | 🔴 CRITICAL |
| 6 | [[brain.md]] | Mimari kararlar özeti — ADR türevi, L0-L6, engineering brain | 🔴 CRITICAL |
| 7 | [[MEMORY.md]] | Oturum hafızası — persistent state, cache, session lifecycle | 🔴 CRITICAL |
| 8 | [[log.md]] | Aktivite günlüğü — append-only audit trail | ⚪ LOG |
| 9 | [[engine.md]] | Orkestrasyon motoru — agent koordinasyonu, task dispatch, faz kapanışı | 🔴 CRITICAL |
| 10 | [[VISION.md]] | Ürün ve ekosistem vizyonu — mülkiyet felsefesi, 6 sorun-çözüm, handoff | 🔴 CRITICAL |
| 11 | [[PROJECTS.md]] | CoreMusic nedir, 10 temel yetenek, 6 hedef kullanıcı, sektörel çözümler | 🔴 CRITICAL |
| 12 | [[glossary.md]] | Terimler sözlüğü — Neva Engine, Bit-Perfect, Ambient Aura vb. | 🔴 CRITICAL |
| 13 | [[ROLE.md]] | Rol ve sorumluluk tanımı — rol → teknoloji eşlemesi | 🔴 CRITICAL |
| 14 | [[ULTRA-THINKING.md]] | Ultra düşünme protokolü — karar öncesi zorunlu doğrulama | 🔴 CRITICAL |
| 15 | [[PLANNER.md]] | Context Planner Spec (Control Plane) — otomatik context yükleme sözleşmesi; FM 13 alan | 🟠 ON-DEMAND |
| 16 | [[RAG.md]] | Retrieval indeksi + pipeline — konu→dosya eşlemesi (§3) · pipeline (§4, ÇOĞU PLANNED — ADR-030) | 🔴 CRITICAL |
| 17 | [[CONTEXT.md]] | Vault klasör envanteri — kök/dizin sayıları, boot ilişkisi, disk çelişkileri | 🔴 CRITICAL |
| 18 | [[CHECKLIST.md]] | Session checklist — §A/§B/§C (baş/orta/kapanış), hedef set sınıflandırması §A0 | 🔴 CRITICAL |
| 19 | [[SYNC.md]] | Sürekli güncelleme döngüsü SSOT'u — hedef set 26, üç kapı, tazelik ölçütü §5 | 🔴 CRITICAL |
| 20 | [[PLAN.md]] | Plan görünümü | 🟠 ON-DEMAND |

**Sınıf sayımı:** 🔴 CRITICAL **18** · 🟠 ON-DEMAND **2** (PLAN · PLANNER) · ⚪ LOG **1** = 21 satır (LOG dosyası CRITICAL setine de girer; benzersiz dosya = 20).

⚠️ **DÜZELTİLDİ (2026-10-10):**
- `[[TECHNICAL_DOCUMENTATION.md]]` satırı **kaldırıldı** — `.ai/` kökünde diskte YOK (0 dosya).
- `[[broken-links-report.md]]` satırı **kaldırıldı** — `.ai/` kökünde diskte YOK.
- `.opencode/skills/` (8 aktif skill) notu **kaldırıldı** — dizin diskte YOK; Registry SSOT kök `CLAUDE.md`'ye taşındı (13 proje skill).
- `total_files: 720` / `total_adr: 80` / `total_adr_disk: 60` → **804 / 97** (2026-10-10 ölçümü).

---

### §4 Bölümler

> Bu dosya çok-sayfalı yapıya ayrılmıştır (hub). İçerik aşağıdaki alt sayfalardadır; SSOT künyesi bu dosyadadır.

| Sayfa | Bölüm |
|-------|-------|
| [[index/01-mimari]] | Mimari (21 katman K000-K020, bağımlılık matrisi, plan) |
| [[index/02-kurallar]] | Kurallar (guardrail, yasak kalıp, LINT) |
| [[index/03-workflow]] | Workflow (fazlar, kapılar, ADR lifecycle) |
| [[index/04-dogrulama]] | Doğrulama (validator, link check, şablon kontrolü) |
| [[index/05-referanslar]] | Referanslar (harici kaynak, ekosistem, raporlar) |

---

### §5 ADR Kataloğu

**SSOT:** `.ai/.decisions/index.md` (v1.3.0, 2026-10-10). Bu dosyada liste **kopyalanmaz** — yalnız işaretçi verilir.

| Durum | Sayı (2026-10-10 disk ölçümü) |
|-------|-------------------------------|
| Frozen (001-036) | 36 |
| Active (037-101) | 48 |
| Rejected (R-001..R-012) | 12 |
| Draft | 1 |
| **Toplam dosya** | **97** |

---

### §6 Servis Haritası

| Subdomain | Kapsam | Katman |
|-----------|--------|--------|
| `coremusic.net` | Ana tanıtım & portal | K011 UX |
| `home.coremusic.net` | public/uygulama yüzeyi (sunum) | K010 · K011 |
| `auth.coremusic.net` | kimlik/oturum/JWT RS256/MFA (ADR-043/052/059/095) | K006 |
| `api.coremusic.net` | Gateway/BFF (ADR-084) · Origin/CSRF (ADR-094) | K009 |
| `media.coremusic.net` | medya arşivi/teslim (ADR-092 ULID) | K015 |
| `assets.coremusic.net` | statik varlık (ITCSS katmanları, token'lar) | K014 AĞ |

**Kural:** subdomain'ler birbirine kod IMPORT etmez; yalnız `shared/` + API sözleşmesi (Parnas'72 — plan §3).
**Ortak çekirdek:** `shared/` (composer package; `shared/src/{Middleware,PageRouter,Database}/`).

*Kaynak:* [[architecture/00-master-index]] §2 Topoloji.

---

### §7 Vault Dizin Haritası (17 dizin — 2026-10-10)

| Dizin | Dosya | Amaç |
|-------|-------|------|
| `architecture/` | 125 md | 21 katman K000-K020 + master index + matris + plan |
| `ui-design/` | 121 md | Mockup, screens, flow, prompt, reference, tokens |
| `.personas/` | 84 | Kullanıcı persona dosyaları |
| `.templates/` | 69 | Guardrail #16 şablonları (13 kategori) |
| `.decisions/` | 97 | ADR arşivi (84/12/1 + index) |
| `ecosystem/` | 13 | Ekosistem dersleri/kaynakları |
| `.agents/` | 12 | 11 agent profili + alt registry |
| `scripts/` | 10 | Doğrulama/vault betikleri |
| `reports/` | 7 | Rapor çıktıları |
| `index/` | 5 | Master katalog hub alt sayfaları (01-05) |
| `prompts/` | 3 | Prompt arşivi |
| `servers/` | 3 | Sunucu/kurulum notları |
| `.sql/` | 20 .sql | SQL şema (4 DBMS) |
| `.png/` | 19 PNG | Görsel SSOT (salt okunur) |
| `.rules/` | 1 | `senior-mode.md` (error-recovery.md YOK) |
| `SESSIONS/` | 2 | Session kayıtları |
| `.obsidian/` | 5 | Obsidian yapılandırması |

**Toplam:** `.ai/` recursive **804 dosya · 572 `.md` · 107 dizin** · kök **20 `.md`**.

---

### §8 Veritabanı

| İddia | Durum |
|-------|-------|
| "18 BCNF DB" (ADR-040) | ⚠️ VERIFICATION REQUIRED — diskte 20 `.sql` dosyası; ADR-003 "9 BCNF" der |
| PDO-only, ORM yasak (ADR-002 frozen) | ✅ ADR-001..036 frozen |
| Multi-DB migration (ADR-014) | ✅ ADR kayıtlı |
| Cross-DB outbox + CQRS-semi (ADR-100) | ✅ ADR-101 (2026-10-09) |

---

## Validation

### §9 Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + Control Plane v2 bloğu |
| 2 | Kök dosya sayımı | 20 `.md` (2026-10-10 ölçümüyle eşit) |
| 3 | Dizin sayımı | 17 depth-1 dizin |
| 4 | Recursive | 804 dosya · 572 md · 107 dizin |
| 5 | ADR | 97 (84/12/1) — `.decisions/index.md` ile hizalı |
| 6 | Skill Registry | 13 proje skill — kök `CLAUDE.md` §Skill Registry ile hizalı |
| 7 | Mimari | 21 K-dizini (K000-K020) · 125 md — `00-master-index.md` §1 ile hizalı |
| 8 | Ölü referans | `TECHNICAL_DOCUMENTATION.md` · `broken-links-report.md` · `.opencode/skills` — üçü de kaldırıldı |
| 9 | Hub | §4 alt sayfalar diskte (index/01-05) |
| 10 | Placeholder | `{{` yok, doldurulmamış yer yok |

---

## References

### §10 Bağlantılar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Vault anayasası | [[CLAUDE]] | Guardrail, boot protokolü |
| Agent registry | [[AGENTS]] | Routing, handover |
| Vault süreç | [[WORKFLOW]] | Fazlar, kapılar |
| Retrieval indeksi | [[RAG]] | Konu→dosya eşlemesi |
| Sürekli döngü | [[SYNC]] | Hedef set 26, üç kapı |
| Vault envanteri | [[CONTEXT]] | Dizin/dosya sayıları |
| Karar arşivi | `.ai/.decisions/index.md` | ADR listesi |
| Katman mimarisi | [[architecture/00-master-index]] | K000-K020 |
| Şablon registry | [[.templates/index]] | 69 şablon / 13 kategori |
| Alt registry | [[.agents/AGENTS]] | 11 agent profili |
| Doğrulama betiği | `.ai/scripts/validate.mjs` | 8 check + bütçe |
| Link denetimi | `.ai/scripts/wiki-link-check.ps1` | Wiki-link doğrulama |
| Disk kanıtı | 2026-10-10 ölçümü | §3 · §7 tüm sayılar |

---

**Authority:** Bayram Ali / Vault Steward
**Version:** 29.0.0
**Last Updated:** 2026-10-10
**Mode:** Red Team · Human Mode · Truth Mode
