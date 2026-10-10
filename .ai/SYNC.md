---
title: "CoreMusic — Sürekli Güncelleme Döngüsü (Session Baş / Orta / Son)"
type: docs
category: vault
date: 2026-10-10
updated: 2026-10-10
version: 1.0.0
status: active
authority: "SSOT (sürekli güncelleme döngüsü) — çelişkide disk kazanır"
docType: context
# Control Plane v2 (Q7 geniş şema):
tier: 5
domain: navigation
ssot: true
risk: low
owner: "MO"
depends-on: [".ai/CLAUDE.md", ".ai/CHECKLIST.md", ".ai/WORKFLOW.md", ".ai/MEMORY.md", ".ai/log.md"]
---

# CoreMusic — Sürekli Güncelleme Döngüsü

**docType:** context · **Klasör:** `.ai/` · **Sorumlu:** MO (vault-updater)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[CHECKLIST.md]] · [[WORKFLOW.md]] · [[MEMORY.md]] · [[log.md]] · [[CONTEXT]] · [[index]] · [[../AGENTS.md]] · [[../CLAUDE.md]]

> **Bu dosya ne işe yarar:** Vault'taki 26 hedef dosyanın **her oturumun başında, ortasında ve sonunda**
> nasıl tazeleneceğini tek yerde tanımlar. Üç uygulama yüzeyi hibrit çalışır: (1) yazılı protokol —
> [[CHECKLIST]] §A/§B/§C · (2) ayrı lifecycle SSOT'u — bu dosya · (3) hook — `.claude/settings.json`.
> Bu dosya [[CHECKLIST]]'in **yerine geçmez**; onu genişletir ve set tanımının otoritesidir.

---

## §1 Amaç

Vault dosyaları sessiz bayatlar: sayım değişir, ADR eklenir, skill taşınır, dizin kapanır.
Bayatlık fark edilmezse AI yanlış sayıya, kırık bağlantıya veya olmayan bir dosyaya dayanır
(Zero-Hallucination ihlali). Bu döngü, bayatlığın **session başına en az üç kez** yakalanmasını
zorunlu kılar ve her yakalama sonrası dosyayı **2-4 satırla** güçlendirir (in-place, dosya adı sabit).

| Karar | Karşılığı (kaynak) |
|-------|--------------------|
| Hedef set = 26 dosya (6 kök `.md` + 20 `.ai/` kök `.md`) | Disk ölçümü 2026-10-10 · §3 |
| Güncelleme in-place; dosya adı/taşıma yok | Kök [[../AGENTS]] §4 (Faz 1) · Guardrail #4 |
| `log.md` yalnız append | Kök [[../AGENTS]] §9 · [[log]] |
| Boot'ta toplu okuma yasak; yalnız CRITICAL seti | Kök [[../AGENTS]] §9 · [[CLAUDE]] §16 |
| Sayı iddiası ölçümle kanıtlanır | Zero-Hallucination · Guardrail #3 |
| Değişmeyen dosya zorla yeniden yazılmaz | [[CHECKLIST]] §C-Güçlendirme |
| Hook yalnız hatırlatır; karar AI'ındır | `.claude/hooks/skill-mandate.cjs` |

> **eli10 (basit):** Bu dosya, "her işin başında, ortasında ve sonunda hangi dosyaların bayatlamış
> olabileceğini kontrol edip tazeleyeceğimiz" listesidir.
> **eli15 (detay):** Ayrı dosyadır çünkü güncelleme sıklığı ile içerik farklı hızda değişir; içerik dosyasına
> gömülürse protokol revizyonunda kaybolur. İçine hedef set, üç kapı ve ölçütler yazılır. Okunması,
> session'ın hangi noktasında hangi tazelemenin beklendiğini gösterir. Yeni dosya eklendiğinde §3
> satırı büyür; dosya silindiğinde satır satır kaldırılır, sayım yeniden ölçülür.

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 26 hedef dosyanın tazelenme zamanı, ölçütü ve sahibi | Dosyaların içeriği (her dosyanın kendi SSOT'u) |
| Üç kapı: Baş (§A) · Orta (§B) · Son (§C) | Kod yazımı ve test süreçleri ([[WORKFLOW]]) |
| Hook hatırlatma yüzeyi (`.claude/settings.json`) | Hook kaynak kodu (`.claude/hooks/*.cjs`) |
| Tazeleme sonrası güçlendirme satırı formatı | ADR üretimi (`.decisions/`) |

- **Kullananlar:** MO (birincil), tüm ajanlar (kapıları uygular), Vault Steward (denetim).
- **Ön koşul:** Disk ölçümü yapılmış olmalı; sayı yazarken ölçüm tarihi belirtilir.
- **Sıklık:** Baş = her session · Orta = session başına en az 1 kez · Son = her "tamamlandı" öncesi.

---

## §3 Mimari — Hedef Dosya Seti (26)

> **Ölçüm: 2026-10-10** (kök `Get-ChildItem -File -Filter *.md` = 7 · `.ai/` kök = 20 `.md`;
> `.env.figma` diskte YOK — eski "21 kök dosya" kaydı düzeltildi).

### §3.1 Kök Dosyalar (6 — `.md` olanlar; `notes.md` dahil)

| # | Dosya | Rol | Ne zaman tazelenir | Sahip |
|---|-------|-----|--------------------|-------|
| 1 | `CLAUDE.md` | Boot pointer + **Skill Registry SSOT** | Son (§C) — yalnız Skill Registry + mimari notu | MO |
| 2 | `AGENTS.md` | Master rules (akış, anti-overthink, loop, §9 tablo) | Son (§C) — yalnız ölü atıf/kural satırı | MO |
| 3 | `README.md` | Giriş — badge, mimari §6, vault tablosu, Session Lifecycle | Son (§C) — yalnız sayı/link satırı | MO |
| 4 | `WORKFLOW.md` | Workflow pointer + Skill Noktaları | Son (§C) — yalnız Skill Noktaları/§14 | MO |
| 5 | `CONTEXT.md` | Vault context pointer | Son (§C) — yalnız bağlantı listesi | MO |
| 6 | `RAG.md` | RAG pointer | Son (§C) — yalnız SSOT bağlantısı | MO |

### §3.2 `.ai/` Kök Dosyalar (20 — hepsi `.md`, 2026-10-10 ölçümü)

| # | Dosya | Rol | Sınıf | Ne zaman tazelenir |
|---|-------|-----|-------|--------------------|
| 1 | `CLAUDE.md` | AI anayasası — 16 Guardrail, §16 boot, §21 yasaklar | 🔴 CRITICAL | Son (§C) — yalnız değişen bölüm |
| 2 | `AGENTS.md` | Agent registry SSOT — 11 agent, routing, handover | 🔴 CRITICAL | Son (§C) — routing/registry satırı |
| 3 | `WORKFLOW.md` | Süreç — fazlar, kapılar, ADR lifecycle | 🔴 CRITICAL | Son (§C) — faz/kapı satırı |
| 4 | `brain.md` | Mimari karar özeti (ADR türevi) | 🔴 CRITICAL | Son (§C) — yeni ADR özeti |
| 5 | `ROLE.md` | Rol → teknoloji eşlemesi | 🔴 CRITICAL | Stack değişince |
| 6 | `index.md` | Master katalog (hub + `index/01-05`) | 🔴 CRITICAL | Son (§C) — envanter/ADR sayımı |
| 7 | `keys.md` | Keyword haritası | 🔴 CRITICAL | Son (§C) — yeni keyword |
| 8 | `RAG.md` | Konu→dosya indeksi + pipeline | 🔴 CRITICAL | Son (§C) — §3 satırı |
| 9 | `CONTEXT.md` | Vault envanteri (kök/dizin/çelişki) | 🔴 CRITICAL | Son (§C) — §3.1/§3.2/§3.5 sayıları |
| 10 | `MEMORY.md` | Session hafızası | 🔴 CRITICAL | Baş (§A) + Son (§C) — §20 state |
| 11 | `ULTRA-THINKING.md` | Ultra düşünme protokolü | 🔴 CRITICAL | Protokol revizyonunda |
| 12 | `engine.md` | Orkestrasyon motoru | 🔴 CRITICAL | Faz kapanışında |
| 13 | `glossary.md` | Terim sözlüğü | 🔴 CRITICAL | Yeni terimde |
| 14 | `VISION.md` | Vizyon ve yol haritası | 🔴 CRITICAL | Yön değişince |
| 15 | `PROJECTS.md` | Proje envanteri | 🔴 CRITICAL | Kapsam değişince |
| 16 | `CHECKLIST.md` | Session checklist §A/§B/§C | 🔴 CRITICAL | Son (§C) — kutular `[x]` |
| 17 | `TODO.md` | Açık iş listesi | 🟠 ON-DEMAND | Baş (§A) + Son (§C) |
| 18 | `PLAN.md` | Plan görünümü | 🟠 ON-DEMAND | Plan değişince |
| 19 | `PLANNER.md` | Context Planner spec (Control Plane) | 🟠 ON-DEMAND | Spec değişince |
| 20 | `log.md` | Audit trail (append-only) | ⚪ LOG | Baş (1 satır INFO) + Son (append) |

**Sınıf sayımı:** 🔴 CRITICAL **18** · 🟠 ON-DEMAND **3** (TODO · PLAN · PLANNER) · ⚪ LOG **1** = **22 `.ai/` + 6 kök = 28 satır; benzersiz dosya = 26** (`notes.md` kökte `.md`'dir ama hedef sete girmez — bilgi notu).
⚠️ **DÜZELTME (2026-10-10):** Eski kayıt "20 dosya (3 kök + 17 `.ai/` kök md)" idi — `.ai/` kökünde **20 `.md`** vardır; set **26**'ya çıktı.

### §3.3 Hızlı Erişim Sınıfı (boot'ta okunmaz, ihtiyaç anında)

`PLAN.md` · `PLANNER.md` · `broken-links-report.md` (diskte **YOK** — link işinde üretilir) · `.ai/index/01-05` (hub alt sayfaları, 5 dosya).

---

## §4 Workflow — Üç Kapı

```text
SESSION BAŞI (§A — boot'tan hemen sonra, görev yazılmadan önce)
  ├─ .ai/CLAUDE.md §16 boot (13 kanonik .ai dosyası, P0→P1→P2)
  ├─ .ai/CHECKLIST.md §A (5 madde: boot · durum · TODO · hedef · kapat)
  ├─ .ai/MEMORY.md §20 (son session state) + §5 (genişletilmiş set)
  ├─ git status --porcelain + git log -10
  └─ .ai/log.md → 1 satır INFO (session açıldı)

SESSION ORTASI (§B — iş bloğu değişiminde, session başına EN AZ 1 KEZ)
  ├─ .ai/CHECKLIST.md §B (5 madde: yarım iş · quality-check · guardrail taraması
  │                        · mockup gate · çelişki çözümü)
  ├─ .ai/TODO.md → P0 satırı hâlâ doğru mu (1 satır)
  ├─ .ai/CHECKLIST.md → §B kutuları [ ] → [x]
  └─ .ai/log.md → olay satırı (varsa)

SESSION KAPANIŞI (§C — "tamamlandı" denmeden önce)
  ├─ .ai/CHECKLIST.md §C (5 madde: kanıt · vault kapanışı · TODO · sync · sonraki adım)
  ├─ .ai/WORKFLOW.md §8.7 Bitiş 5 adım
  ├─ GÜÇLENDİRME: bu session'da DEĞİŞEN hedef dosyalar 2-4 satırla güncellenir
  ├─ .ai/log.md → append (timestamp)
  ├─ .ai/MEMORY.md §20 → state
  └─ .ai/TODO.md → biten [x], yeni 1 satır + kaynak
```

### §4.1 Kapı A — Session Başı (5 madde — [[CHECKLIST]] §A ile birebir)

| ✅ | Madde | Ölçüt | Tazelik kontrolü |
|---|-------|-------|------------------|
| [ ] | A1 | Boot protokolü 13 kanonik dosya okundu | Sayılar hâlâ doğru mu? (§5) |
| [ ] | A2 | Durum doğrulandı + `git status`/`git log -10` | Yeni commit var mı → etkilenen satır |
| [ ] | A3 | [[TODO]] tarandı, tek P0/P1 seçildi | TODO satırı hâlâ geçerli mi |
| [ ] | A4 | Hedef tek cümlede yazıldı + şablon seçildi | Şablon diskte mi (`.templates/index`) |
| [ ] | A5 | A1-A4 = 5/5 ✅ → başla | A5 yoksa **görev BAŞLATILMAZ** |

### §4.2 Kapı B — Session Orası (5 madde — [[CHECKLIST]] §B ile birebir)

| ✅ | Madde | Ölçüt | Tazelik kontrolü |
|---|-------|-------|------------------|
| [ ] | B1 | Yarım iş yok (git status gözden geçirildi) | Yeni dosya/klasör → §3'e eklendi mi |
| [ ] | B2 | `quality-check` çalıştırıldı | Bulgu → [[TODO]] P1/P2 |
| [ ] | B3 | Guardrail taraması (`SELECT *`, ORM, `innerHTML`, `var`, layer violation) | İhlal → revert + CRITICAL log |
| [ ] | B4 | Frontend işi → mockup gate (`.ai/ui-design/` + Kalıp A-D) | Görsel okundu mu → OK/DUR |
| [ ] | B5 | Çelişki/şüphe resolve (SSOT hiyerarşisi veya kullanıcı onayı) | Doğrulanamayan → `VERIFICATION REQUIRED` |

### §4.3 Kapı C — Session Kapanışı (5 madde — [[CHECKLIST]] §C ile birebir)

| ✅ | Madde | Ölçüt | Tazelik kontrolü |
|---|-------|-------|------------------|
| [ ] | C1 | Her "tamamlandı" için 1 kanıt | Kanıtsız iddia = `VERIFICATION REQUIRED` |
| [ ] | C2 | Vault kapanışı ([[WORKFLOW]] §8.7) | Değişen hedef dosyalar güçlendirildi mi |
| [ ] | C3 | [[TODO]] güncellendi | Biten `[x]`, yeni 1 satır + kaynak |
| [ ] | C4 | Vault sync (`.workflows/vault-sync.md` Aşama 8) | [[log]] append + [[MEMORY]] §20 state |
| [ ] | C5 | Sonraki adım yazıldı | [[log]] sonu tek satır |

---

## §5 Tazelik Kontrol Listesi (her kapıda §3'e göre)

| # | Kontrol | Nasıl | Ne zaman |
|---|---------|-------|----------|
| 1 | Envanter sayıları bayat mı? | Yeniden say: `.ai/` kök `.md` · depth-1 dizin · recursive md/dosya/dizin | A1 + C2 |
| 2 | ADR sayısı değişti mi? | `.ai/.decisions/{accepted,rejected,draft}` sayımı → [[index]] §2 + `.decisions/index.md` | C2 |
| 3 | Skill Registry diskle hizalı mı? | `.claude/skills/` dizin sayısı vs kök `CLAUDE.md` §Skill Registry satır sayısı | A1 + C2 |
| 4 | Mimari sayım değişti mi? | `.ai/architecture/` K-dizini + md sayısı vs `00-master-index.md` §1 | C2 |
| 5 | Yeni/taşınan vault dosyası var mı? | `git log --diff-filter=A --name-only -20 -- .ai` | B1 + C2 |
| 6 | Kırık bağlantı üretildi mi? | `.ai/scripts/wiki-link-check.ps1` **repo kökünden** | C2 |
| 7 | Placeholder kaldı mı? | grep `{{` + `⚠️ VERIFICATION REQUIRED` sayımı | C1 |
| 8 | `log.md` append-only mı? | `git diff -- .ai/log.md` → yalnız `+` satırı | C4 |

### §5.1 Ölçüm Tablosu (her C2'de doldurulur, `log.md`'ye 1 satır özeti yazılır)

| Metrik | Beklenen (2026-10-10) | Bu session ölçümü |
|--------|----------------------|-------------------|
| `.ai/` kök `.md` | 20 | |
| `.ai/` depth-1 dizin | 17 | |
| `.ai/` recursive md | 572 | |
| `.ai/` recursive dosya | 804 | |
| `.ai/` recursive dizin | 107 | |
| ADR accepted / rejected / draft | 84 / 12 / 1 | |
| Kök `.md` | 7 | |
| `.claude/skills/` | 13 | |
| `architecture/` md / K-dizini | 125 / 21 (K000-K020) | |
| `.sql` / `.png` / `ui-design` md | 20 / 19 / 121 | |

---

## §6 Kurallar

| # | Kural | Neden var | Ref |
|---|-------|-----------|-----|
| 1 | Yazım in-place; dosya adı/klasör değişmez, taşıma/silme onaysız yapılmaz | Yüzlerce wiki-link kırılır | Kök [[../AGENTS]] §4 |
| 2 | `log.md` yalnız append — geçmiş satıra dokunulmaz | Denetim değeri | Kök [[../AGENTS]] §9 |
| 3 | Sayı yazılırken ölçüm tarihi belirtilir | Bayat sayı kandırır | Guardrail #3 |
| 4 | Vault'a dosya ekleme/taşıma → §3 + [[RAG]] §3 + [[index]] satırı aynı session'da güncellenir | İndeks bayatlar | `vault-sync-post` |
| 5 | Değişmeyen dosya zorla yeniden yazılmaz (diff'i şişirir) | Anti-waste | [[CHECKLIST]] §C |
| 6 | Hook hatırlatır; karar ve uygulama AI'ındır | Hook otomatik yazım yapmaz | `.claude/hooks/skill-mandate.cjs` |
| 7 | Subagent **commit atmaz** — orkestratöre aittir | Tek kalemli tarih | Kök [[../AGENTS]] §16.5 |
| 8 | `.env.figma` / credential hiçbir satıra girmez | Sızıntı | REDACTED |

---

## §7 Doğrulama

| # | Kontrol | Kriter | Yöntem |
|---|---------|--------|--------|
| 1 | Hedef set | 26 dosya (6 kök + 20 `.ai/` kök `.md`) | `Get-ChildItem` ×2 |
| 2 | Sınıf sayımı | CRITICAL 18 · ON-DEMAND 3 · LOG 1 + 6 kök | §3.2 |
| 3 | Kapı sayısı | 3 kapı × 5 madde = 15 | §4.1-§4.3 |
| 4 | CHECKLIST uyumu | Bu dosyanın A/B/C maddeleri [[CHECKLIST]] §A/§B/§C ile birebir | yan yana okuma |
| 5 | Hook bağlantısı | `.claude/settings.json → UserPromptSubmit` hook'u var | `Test-Path .claude/hooks/skill-mandate.cjs` |
| 6 | Placeholder | Dosyada `{{` yok, doldurulmamış yer yok | grep |
| 7 | Wiki-link | Hedefler diskte | `.ai/scripts/wiki-link-check.ps1` (kökten) |
| 8 | Çift kaynak | Set tanımı bu dosyada (SSOT); [[CHECKLIST]] ve kök pointerlar yalnız işaretçi | gözden geçirme |

---

## §8 Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Session checklist | [[CHECKLIST]] | §A/§B/§C madde metinleri (bu dosya yalnız genişletir) |
| Boot protokolü | [[CLAUDE]] §16 | 13 kanonik `.ai/` dosyası |
| Vault süreç | [[WORKFLOW]] §8.7 | Bitiş 5 adım |
| Session hafızası | [[MEMORY]] §5 · §20 | Genişletilmiş set · son state |
| Audit trail | [[log]] | Append-only kayıt |
| Vault envanteri | [[CONTEXT]] §3 | Kök/dizin sayıları (bu dosya §3 ile hizalı) |
| Retrieval indeksi | [[RAG]] §3 | Konu→dosya eşlemesi |
| Kök master kurallar | [[../AGENTS]] §4 · §9 · §16.5 | Faz 1 · append-only · commit yetkisi |
| Kök boot pointer | [[../CLAUDE]] · [[../AGENTS]] · [[../README]] · [[../WORKFLOW]] | Boot sırası 1-4 |
| Vault sync akışı | `.workflows/vault-sync.md` Aşama 8 | Kapanış güçlendirmesi |
| Session init akışı | `.workflows/session-init.md` | Başlangıç |
| Hook | `.claude/settings.json` · `.claude/hooks/skill-mandate.cjs` | Hatırlatma yüzeyi |
| Doğrulama betiği | `.ai/scripts/validate.mjs` · `.ai/scripts/wiki-link-check.ps1` | 8 check + link |
| Disk kanıtı | 2026-10-10 ölçümü | §3 · §5.1 tüm sayılar |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md` (Guardrail #16 — `documentation/context-md-template.md` referans alındı)
**Last Updated:** 2026-10-10 · **docType:** context · **Status:** ACTIVE (hibrit döngü: CHECKLIST + bu dosya + hook)
