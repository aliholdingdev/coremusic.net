---
title: "vault: CHECKLIST.md — Session Checklist"
type: dokuman
created: 2026-10-06
updated: 2026-10-06
sources: [vault-checklist]
tags: [session, checklist]
---

# CHECKLIST.md — Session Checklist (Baş / Orta / Kapanış)

Oturum yaşam döngüsünün kanıtlanabilir uygulanması. Veri aşağıda `.ai/raw/CHECKLIST.md`'den satır içine gömülüdür.

## Özet

- A fazı: boot protokolü (≤36s, 13 dosya), durum doğrulama (git status/log), TODO taraması, hedef netleştirme
- Her madde: tek action + ölçüt + kaynak referansı
- Kaynak: `raw/CHECKLIST.md` → [[vault-checklist]]

## İlgili Sayfalar
- [[vault-memory]] — §6 5 soru bu checklist'te cevaplanır
- [[vault-todo]] — A3'te taranan liste
- [[vault-claude]] — boot protokolünün sahibi (§16)

---

## Ham Veri — `.ai/raw/CHECKLIST.md` (tam metin, satır içine gömülü)

---
title: "CoreMusic — Session Checklist (Baş / Orta / Kapanış)"
type: checklist
category: session-lifecycle
version: 1.0.0
status: active
authority: SSOT
updated: 2026-09-29
---

# CoreMusic — Session Checklist (Baş / Orta / Kapanış)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[MEMORY.md]] · [[index.md]] · [[log.md]] · [[TODO.md]]

**Kullanım:** Her session'da bu üç bölüm **sırayla** uygulanır. Her madde tek action ve ölçülebilir; madde bittiğinde durum kutusu `[ ]` → `[x]` yapılır, madde tamamlanmadan bir sonraki bölüme geçilmez (grup başına maksimum 5 madde).

**SSOT notu:** Bu dosya mevcut protokollerin **yerine geçmez, onların işaretleneceği tek yüzeydir**. Boot okuma listesi [[CLAUDE.md]] §16, başlangıç 5 soru [[MEMORY.md]] §6, bitiş 5 adım [[MEMORY.md]] §7 ve [[WORKFLOW.md]] §8.7'de tanimlidir; burada yalnızca özetlenir (SSOT dedup).

---

## Purpose

### §1 Amaç

Session yaşam döngüsünün (başlangıç → orta → kapanış) her oturumda aynı şekilde, kanıtlanabilir biçimde uygulanmasını sağlamak; yarım iş, doğrulanmamış iddia ve vault tutarsızlığını session başına en az bir kez kontrol etmek.

---

## Workflow

### §2 🔴 A. SESSION BAŞLAMA (start — boot'tan hemen sonra, görev yazılmadan önce)

| ✅ | Madde (tek action) | Ölçüt — nasıl ✅ işaretlenir | Kaynak |
|---|---|---|---|
| [ ] | A1. Vault boot protokolünü çalıştır | [[CLAUDE.md]] §16 kanonik 13 dosya P0→P1→P2 sırasıyla okundu (boot ≤36s) | [[CLAUDE.md]] §16 · [[MEMORY.md]] §5 |
| [ ] | A2. Mevcut durumu doğrula | [[MEMORY.md]] §6 5 sorusu cevaplandı **+** `git status --porcelain` ve `git log -10` okundu; cevap [[log.md]]'ye 1 satır INFO | [[MEMORY.md]] §6 · [[WORKFLOW.md]] §8.7 |
| [ ] | A3. Açık işleri tara | [[TODO.md]] tarandı; bu session'da ele alınacak **tek** P0/P1 satırı seçildi (1 satır) | [[TODO.md]] |
| [ ] | A4. Hedefi netleştir | Görev tek cümleyle yazıldı; Guardrail #1 (Zero Code Before Plan) için gerekli ADR/şablon seçildi — `.ai/.templates/index` (Guardrail #16) | [[CLAUDE.md]] §7 · [[.templates/index]] |
| [ ] | A5. Başlangıcı kapat | A1-A4 = 5/5 ✅; aksi halde görev BAŞLATILMAZ (Guardrail #2 Vault First) | [[CLAUDE.md]] §7 #2 |

**A0 — Hedef Dosya Sınıflandırması (20 dosya · disk ölçümü 2026-09-29):** Boot'ta **yalnız CRITICAL** seti okunur; hepsi tek seferde okunmaz.

| Sınıf | Adet | Dosyalar | Ne zaman |
|---|---|---|---|
| 🔴 CRITICAL | 16 | kök `CLAUDE.md` · `.ai/CLAUDE.md` · `AGENTS` · `WORKFLOW` · `brain` · `ROLE` · `index` · `keys` · `MEMORY` · `ULTRA-THINKING` · `engine` · `glossary` · `VISION` · `PROJECTS` (§16 kanonik 13'ten `log.md` çıkarıldı = 12 + iki CLAUDE) · `CHECKLIST` (A1) · `TODO` (A3) | Session başında |
| 🟠 ON-DEMAND | 3 | kök `README.md` · kök `WORKFLOW.md` (pointer'lar) · `.ai/broken-links-report.md` (link/ADR retarget işinde) | İhtiyaçta |
| ⚪ LOG | 1 | `.ai/log.md` — yalnız append; boot'ta yalnız son 20 satır okunur | Otomatik |

**Yasak:** A5 olmadan kod/plan yazma · boot dosyalarını atlayıp göreve başlama.

---

### §3 🟡 B. SESSION ORTASI (mid checkpoint — iş bloğu değişiminde, session başına **en az 1 kez**)

| ✅ | Madde (tek action) | Ölçüt — nasıl ✅ işaretlenir | Kaynak |
|---|---|---|---|
| [ ] | B1. Yarım iş bırakmadığını doğrula | Açık `git status` satırları gözden geçirildi; yarım iş varsa commit'e ayrıldı **ya da** 1 satır olarak [[TODO.md]]'ye yazıldı | [[TODO.md]] · [[WORKFLOW.md]] §8.1 |
| [ ] | B2. Kalite kontrolü tetikle | `quality-check` çalıştırıldı (dead code, bağımlılık, güvenlik, mimari uyumluluk); bulgular [[TODO.md]] P1/P2'ye kaynakla eklendi | AGENTS §7 Step 6 · `quality-check` skill |
| [ ] | B3. Guardrail taraması yap | [[CLAUDE.md]] §21 yasak kalıplar (`SELECT *`, ORM, `_csrf_token`, `innerHTML`, `var`) + §5.1 Layer Violation tarandı; ihlal varsa **derhal revert + CRITICAL log** | [[CLAUDE.md]] §21 · §5.1 |
| [ ] | B4. Frontend işi ise mockup gate | Frontend görevinde Guardrail #11 dosyaları (mockup indeksi, C01-C16 envanteri, token'lar, 45-tier matris) ve §7.3 Kalıp A-D koddan **ÖNCE** okundu → OK / DUR | [[CLAUDE.md]] §7.1 · §7.3 |
| [ ] | B5. Çelişki/şüpheyi resolve et | Çelişki veya doğrulanamayan iddia varsa **DUR**: SSOT hiyerarşisi (CLAUDE > AGENTS > WORKFLOW) veya kullanıcı onayı ile karar ver; doğrulanamayan → `VERIFICATION REQUIRED` (Guardrail #3/#12/#14) | [[CLAUDE.md]] §2.1 · §7 #3 |

**Orta tazeleme (§B — bu 20 dosyadan yalnız 3'ü):** [[TODO.md]] P0 satırı hâlâ doğru mu (1 satır) · [[CHECKLIST.md]] §B kutuları `[ ]` → `[x]` işaretlendi mi · [[log.md]]'ye olay satırı eklendi mi. CRITICAL dosyaların içeriği ortada **yeniden okunmaz** (bayat iddia şüphesi varsa B5); uzun session'da [[MEMORY.md]] §20 de tazelenir.

**Yasak:** B4'ü atlayıp frontend kodu yazma · B5'i beklemeden devam etme.

---

### §4 🟢 C. SESSION KAPANIŞI (end — görev "tamamlandı" denmeden önce)

| ✅ | Madde (tek action) | Ölçüt — nasıl ✅ işaretlenir | Kaynak |
|---|---|---|---|
| [ ] | C1. Kanıtla doğrula | Her "tamamlandı" iddiası için 1 kanıt (komut çıktısı / dosya / test sonucu) gösterildi; kanıtsız iddia `VERIFICATION REQUIRED` | [[CLAUDE.md]] §7 #3 · verification-before-completion |
| [ ] | C2. Vault kapanışını yap | [[WORKFLOW.md]] §8.7 Bitiş 5 adım: in-place yazım → [[log.md]] timestamp → [[MEMORY.md]] §20 state → wiki-link regex doğrulama → halüsinasyon sweep | [[WORKFLOW.md]] §8.7 |
| [ ] | C3. TODO'yu güncelle | [[TODO.md]]: biten iş `[x]`, yeni iş 1 satır + kaynak (commit/doc/ölçüm), uydurma madde eklenmedi (Zero-Hallucination) | [[TODO.md]] |
| [ ] | C4. Post-op vault sync | `.workflows/vault-sync.md` Aşama 8 uygulandı — ⚠️ `session-save.mjs` / `vault-post-update.mjs` / `project-state.md` **diskte YOK** → kapanış MANUEL: [[log.md]] append + [[MEMORY.md]] §20 (VERIFICATION REQUIRED) | `.workflows/vault-sync.md` Aşama 8 · [[MEMORY.md]] §20 Known Issue |
| [ ] | C5. Bir sonraki adımı yaz | [[log.md]] sonuna tek satır: `## YYYY-MM-DD` + `Sonraki adım: <1 cümle>` → bir sonraki session A3'ten devam eder | [[MEMORY.md]] §4 · Guardrail #13 Session Continuity |

**C-Güçlendirme seti (kapanışta güncellenmesi zorunlu — vault-sync ile):** [[log.md]] (append) · [[MEMORY.md]] §20 state · [[TODO.md]] (biten `[x]`, yeni 1 satır + kaynak) · [[CHECKLIST.md]] (bu tablo) · bu session'da **değişen** hedef dosyalar 2-4 satırla güçlendirilir (`index`/`keys`/`brain`/`WORKFLOW`/`CLAUDE`; kök `CLAUDE.md`/`README.md`/`WORKFLOW.md` yalnızca Session Lifecycle bloğu). Değişmeyen dosya zorla yeniden yazılmaz — in-place, dosya adı sabit (Guardrail #4).

**Yasak:** C1'siz "bitti" deme · C2/C4'ü atlayıp session kapatma · C5'siz kapanış.

---

## Validation

### §5 Uyum Kontrolü (2026-09-29)

| Kontrol | Sonuç | Kanıt |
|---|---|---|
| Mevcut boot/sync protokolleriyle çelişki | **YOK** — A = §8.6 + MEMORY §6, C = §8.7 + MEMORY §7 ile birebir aynı adımlar (tekrar değil, özet + işaretlenme yüzeyi) | [[WORKFLOW.md]] §8.6/§8.7 · [[MEMORY.md]] §6/§7 |
| Mid checkpoint | Vault'ta daha önce **YOKTU** (session-init = başlangıç, vault-sync = değişiklik) → yeni eklendi, mevcut dosyaya çakışmadı | `.workflows/session-init.md`, `.workflows/vault-sync.md` |
| Aynı adlı eski doküman | **YOK** — vault/genel tarama (2026-09-29) yalnızca 3 skill-iç checklist buldu (`.claude/skills/{database-normalize-maker,skill-maker,ui-code-generator}/...`); bunlar şablon kontrol listeleridir, birleştirme/garipleştirme gerekmedi | glob `*CHECKLIST*` |
| Konum | `.opencode/.ai/` dizini **yok** (`.opencode/` altında yalnız CLAUDE.md, skills/, workflows/); SSOT Guardrail #5 gereği doküman `.ai/` altına yazıldı → **çift vault oluşmadı** | Test-Path `.opencode/.ai` = False |
| Frozen ADR / dosya adı | Dokunulmadı — ADR 001-037 immutable, dosya yeniden adlandırması yok (Guardrail #3/#4) | — |
| Secret | Bu dosyaya hiçbir secret/token yazılmadı (REDACTED) | — |

---

## References

### §6 Bağlantılar (nereye eklendi)

| Aşama | Bağlandığı dosya | Ekleme |
|---|---|---|
| 🔴 Başlama | `.ai/CLAUDE.md` §16A | 4 satırlık referans bloğu |
| 🔴 Başlama | `.workflows/session-init.md` + `.opencode/.workflows/session-init.md` | §1 notu, Aşama 12 kutusu, boot tablosu satır 16 |
| 🟡 Orta | `.ai/WORKFLOW.md` §8.7B | 3 satırlık checkpoint tablosu |
| 🟢 Kapanış | `.workflows/vault-sync.md` Aşama 8 | 4. satır (checklist kapanışı) |
| 🔄 Sürekli güçlendirme | `.ai/CLAUDE.md` §16A + `.ai/WORKFLOW.md` §8.7B | Her session sonunda 20 hedef dosyalık set gözden geçirilir, değişenler 2-4 satırla güçlendirilir |
| Lifecycle bağları | `.workflows/session-init.md` (+ `.opencode/` kopyası) · `.workflows/vault-sync.md` Aşama 8 satır 5 · kök `CLAUDE.md`/`README.md`/`WORKFLOW.md` | Set tanımı (§A) + kapanış güçlendirmesi (§C) bu dosyalara bağlandı |
| Navigasyon | `.ai/index.md` §2 Quick Reference | 2 satır (CHECKLIST / TODO) |

**İlgili:** [[CLAUDE.md]] · [[WORKFLOW.md]] §8.6-§8.7B · [[MEMORY.md]] §4/§6/§7/§20 · [[TODO.md]] · [[log.md]] · `.workflows/session-init.md` · `.workflows/vault-sync.md`

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Truth Mode · Human Mode
