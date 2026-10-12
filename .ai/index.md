---
title: "CoreMusic Vault — Master Index"
type: system
category: vault-navigation
status: active
authority: SSOT
version: 30.0.0
updated: 2026-10-12
date: 2026-08-09
total_root_files: 22
total_root_md: 21
total_ai_dirs: 17
total_recursive_files: 665
total_recursive_md: 435
total_recursive_dirs: 72
total_adr: 91
total_adr_accepted: 80
total_adr_rejected: 10
total_adr_draft: 0
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

**Skills:** Registry SSOT = kök `CLAUDE.md` §Skill Registry — **13 proje skill'i** (`.claude/skills/`, 2026-10-12 disk ölçümü). Global skill envanteri `C:\.claude\skills` (348 dizin, proje dışı). `.opencode/skills/` diskte YOK (kapatıldı).

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
| Mimari kararlar (ADR arşivi) | `.ai/.decisions/index.md` (91 ADR: 80/10/0 · v1.5.0) |
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
| Şablon seçimi (Guardrail #16) | [[.templates/index]] (66 dosya · 13 kategori) |
| SQL şema | `.ai/.sql/` (20 `.sql`, 4 DBMS) |
| Açık İş Listesi | [[TODO.md]] (P0/P1/P2) |
| Link/ADR denetimi | `.ai/scripts/wiki-link-check.ps1` (kökten çalıştır) · `.ai/broken-links-report.md` **diskte YOK** (işte üretilir) |

---

### §3 SSOT Core Dosyaları (21 `.ai/` kök `.md` — 2026-10-12 disk ölçümü)

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
| 21 | [[TODO.md]] | Açık iş listesi — P0/P1/P2 önceliklendirme | 🟠 ON-DEMAND |

**Sınıf sayımı:** 🔴 CRITICAL **18** · 🟠 ON-DEMAND **3** (PLAN · PLANNER · TODO) · ⚪ LOG **1** = 22 satır (LOG dosyası CRITICAL setine de girer; benzersiz dosya = 21).

**K-SPACE KALDIRILDI (2026-10-12):** K-space katman mimarisi (K000–K020 / A0–A5 / `.ai/architecture/` 46 dosya) ve ADR-061 birlikte silindi; 4 mimari şablon + `.workflows/architecture-write.md` + `.superpowers/` kaldırıldı. Yedek: `C:\temp\opencode\vault-backup-2026-10-12`. Bu dosyadaki tüm sayılar 2026-10-12 disk ölçümüyle yeniden yazıldı.

⚠️ **DÜZELTİLDİ (2026-10-10):**
- `[[TECHNICAL_DOCUMENTATION.md]]` satırı **kaldırıldı** — `.ai/` kökünde diskte YOK (0 dosya).
- `[[broken-links-report.md]]` satırı **kaldırıldı** — `.ai/` kökünde diskte YOK.
- `.opencode/skills/` (8 aktif skill) notu **kaldırıldı** — dizin diskte YOK; Registry SSOT kök `CLAUDE.md`'ye taşındı (13 proje skill).
- `total_files: 720` / `total_adr: 80` → 2026-10-10: **804 dosya / 97 ADR** → 2026-10-12: **665 dosya / 91 ADR** (K-space kaldırımı sonrası yeniden ölçüm).

---

### §4 Bölümler

> Bu dosya çok-sayfalı yapıya ayrılmıştır (hub). İçerik aşağıdaki alt sayfalardadır; SSOT künyesi bu dosyadadır.

| Sayfa | Bölüm |
|-------|-------|
| [[index/01-mimari]] | Mimari (ADR tabanlı mimari karar özeti, subdomain topolojisi, ortak çekirdek — K-space 2026-10-12'de kaldırıldı) |
| [[index/02-kurallar]] | Kurallar (guardrail, yasak kalıp, LINT) |
| [[index/03-workflow]] | Workflow (fazlar, kapılar, ADR lifecycle) |
| [[index/04-dogrulama]] | Doğrulama (validator, link check, şablon kontrolü) |
| [[index/05-referanslar]] | Referanslar (harici kaynak, ekosistem, raporlar) |

---

### §5 ADR Kataloğu

**SSOT:** `.ai/.decisions/index.md` (v1.5.0, 2026-10-12). Bu dosyada liste **kopyalanmaz** — yalnız işaretçi verilir.

| Durum | Sayı (2026-10-12 disk ölçümü) |
|-------|-------------------------------|
| Frozen (001-036) | 36 |
| Active (037-101) | 45 |
| Rejected (R-001..R-010) | 10 |
| Draft | 0 |
| **Toplam dosya** | **91** |

> **Not (2026-10-12):** ADR-061 (K-space katman tanımı) K-space ile birlikte kaldırıldı → toplam 91.

---

### §6 Servis Haritası

| Subdomain | Kapsam | Alan |
|-----------|--------|------|
| `coremusic.net` | Ana tanıtım & portal | Sunum |
| `home.coremusic.net` | public/uygulama yüzeyi (sunum) | Sunum |
| `auth.coremusic.net` | kimlik/oturum/JWT RS256/MFA (ADR-043/052/059/095) | Kimlik |
| `api.coremusic.net` | Gateway/BFF (ADR-084) · Origin/CSRF (ADR-094) | API |
| `media.coremusic.net` | medya arşivi/teslim (ADR-092 ULID) | Medya |
| `assets.coremusic.net` | statik varlık (ITCSS katmanları, token'lar) | Varlık |

**Kural:** subdomain'ler birbirine kod IMPORT etmez; yalnız `shared/` + API sözleşmesi.
**Ortak çekirdek:** `shared/` (composer package; `shared/src/{Middleware,PageRouter,Database}/`).

*Not (2026-10-12):* Subdomain dizinleri diskte mevcut (kök `*.coremusic.net/` ×5); topoloji kaynaklı eski `architecture/00-master-index` K-space ile kaldırıldı — ADR atıfları `.ai/.decisions/` içindedir.

---

### §7 Vault Dizin Haritası (17 dizin — 2026-10-12)

| Dizin | Dosya | Amaç |
|-------|-------|------|
| `ui-design/` | 121 md | Mockup, screens, flow, prompt, reference, tokens |
| `.personas/` | 84 md | Kullanıcı persona dosyaları |
| `.decisions/` | 97 md | ADR arşivi (91 ADR + index/CLAUDE yardımcıları) |
| `.templates/` | 66 md | Guardrail #16 şablonları (13 kategori) |
| `ecosystem/` | 13 md | Ekosistem dersleri/kaynakları |
| `.agents/` | 12 md | 11 agent profili + alt registry |
| `.sql/` | 20 .sql + 5 md | SQL şema (4 DBMS: mssql/mysql/postgresql/sqlite) |
| `scripts/` | 10 dosya | Doğrulama/vault betikleri |
| `index/` | 5 md | Master katalog hub alt sayfaları (01-05) |
| `.png/` | 19 PNG + 4 md | Görsel SSOT (salt okunur) |
| `servers/` | 3 md | Sunucu/kurulum notları |
| `SESSIONS/` | 2 md | Session kayıtları |
| `.obsidian/` | 5 dosya | Obsidian yapılandırması |
| `.rules/` | 1 md | `senior-mode.md` (error-recovery.md YOK) |
| `prompts/` | 0 | Boş dizin (2026-10-12 ölçüm) |
| `reports/` | 0 | Boş dizin (2026-10-12 ölçüm) |
| `.diagram/` | 0 | Boş dizin (2026-10-12 ölçüm) |

**Toplam:** `.ai/` recursive **665 dosya · 435 `.md` · 72 dizin** · kök **21 `.md`**.

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
| 2 | Kök dosya sayımı | 21 `.md` (2026-10-12 ölçümüyle eşit) |
| 3 | Dizin sayımı | 17 depth-1 dizin |
| 4 | Recursive | 665 dosya · 435 md · 72 dizin |
| 5 | ADR | 91 (80/10/0) — `.decisions/index.md` v1.5.0 ile hizalı |
| 6 | Skill Registry | 13 proje skill — kök `CLAUDE.md` §Skill Registry ile hizalı |
| 7 | K-space | `architecture/` diskte YOK (0 dizin) — kaldırım 2026-10-12, yedek `C:\temp\opencode\vault-backup-2026-10-12` |
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
| Şablon registry | [[.templates/index]] | 66 şablon / 13 kategori |
| Alt registry | [[.agents/AGENTS]] | 11 agent profili |
| Doğrulama betiği | `.ai/scripts/validate.mjs` | 7 check (fm/tier/ssot/dep/link/orphan/fanout) |
| Link denetimi | `.ai/scripts/wiki-link-check.ps1` | Wiki-link doğrulama |
| Disk kanıtı | 2026-10-12 ölçümü | §3 · §7 tüm sayılar |

---

**Authority:** Bayram Ali / Vault Steward
**Version:** 30.0.0
**Last Updated:** 2026-10-12
**Mode:** Red Team · Human Mode · Truth Mode
