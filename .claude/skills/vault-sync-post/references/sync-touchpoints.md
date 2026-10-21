# vault-sync-post — Senkron Noktaları (Touchpoint) Referansı

> SSOT: `.ai/SYNC.md` v1.1.0 (§3 hedef set · §4 üç kapı · §5 tazelik · §6 kurallar).
> Bu dosya o SSOT'un skill-görünümüdür; çelişkide `.ai/SYNC.md` kazanır.
> Ölçüm tarihi: 2026-10-21.

---

## 1. Hedef Dosya Seti (26)

Kapanışta (kapı C) tazelenmesi gereken benzersiz dosya: **26** = **6 kök pointer** + **20 `.ai/` kök md**
(`notes.md` kökte `.md`'dir ama sete girmez — bilgi notu).

### 1.1 Kök Pointer'lar (6)

| # | Dosya | Rol | Kapanışta ne tazelenir | Sahip |
|---|-------|-----|------------------------|-------|
| 1 | `CLAUDE.md` | Boot pointer + Skill Registry SSOT | Yalnız Skill Registry + mimari not satırı | MO |
| 2 | `AGENTS.md` | Master rules | Yalnız ölü atıf/kural satırı | MO |
| 3 | `README.md` | Giriş | Yalnız sayı/link satırı | MO |
| 4 | `WORKFLOW.md` | Workflow pointer | Yalnız Skill Noktaları/§14 satırı | MO |
| 5 | `CONTEXT.md` | Vault context pointer | Yalnız bağlantı listesi satırı | MO |
| 6 | `RAG.md` | RAG pointer | Yalnız SSOT bağlantısı satırı | MO |

### 1.2 `.ai/` Kök Dosyalar (20 — sınıf etiketleri SYNC §3.2'den)

| # | Dosya | Sınıf | Kapanışta ne tazelenir |
|---|-------|-------|------------------------|
| 1 | `CLAUDE.md` | 🔴 CRITICAL | Yalnız değişen bölüm |
| 2 | `AGENTS.md` | 🔴 CRITICAL | Routing/registry satırı |
| 3 | `WORKFLOW.md` | 🔴 CRITICAL | Faz/kapı satırı |
| 4 | `brain.md` | 🔴 CRITICAL | Yeni ADR özeti |
| 5 | `ROLE.md` | 🔴 CRITICAL | Stack değişince |
| 6 | `index.md` | 🔴 CRITICAL | Envanter/ADR sayımı |
| 7 | `keys.md` | 🔴 CRITICAL | Yeni keyword satırı |
| 8 | `RAG.md` | 🔴 CRITICAL | §3 konu→dosya satırı |
| 9 | `CONTEXT.md` | 🔴 CRITICAL | §3.1/§3.2/§3.5 sayıları |
| 10 | `MEMORY.md` | 🔴 CRITICAL | **§20 Current Session State** (Baş + Son) |
| 11 | `ULTRA-THINKING.md` | 🔴 CRITICAL | Protokol revizyonunda |
| 12 | `engine.md` | 🔴 CRITICAL | Faz kapanışında |
| 13 | `glossary.md` | 🔴 CRITICAL | Yeni terimde |
| 14 | `VISION.md` | 🔴 CRITICAL | Yön değişince |
| 15 | `PROJECTS.md` | 🔴 CRITICAL | Kapsam değişince |
| 16 | `CHECKLIST.md` | 🔴 CRITICAL | §C kutuları `[x]` |
| 17 | `TODO.md` | 🟠 ON-DEMAND | Biten `[x]`, yeni 1 satır + kaynak |
| 18 | `PLAN.md` | 🟠 ON-DEMAND | Plan değişince |
| 19 | `PLANNER.md` | 🟠 ON-DEMAND | Spec değişince |
| 20 | `log.md` | ⚪ LOG | **Append-only** kapanış girişi |

⚠️ **Sayı gerilimi (kaydedildi, düzeltilmedi):** SYNC.md §3.2'nin kendi sayaç satırı
"CRITICAL **18** · ON-DEMAND **3** · LOG **1** = 22 `.ai/` satırı + 6 kök = 28 satır; benzersiz **26**" der;
tablonun kendisi `.ai/` kökü için **20 satır** gösterir (16 CRITICAL + 3 ON-DEMAND + 1 LOG).
Benzersiz dosya sayacı her iki okumada da **26**'dır; sınıf toplamları arasındaki fark SSOT'un
kendisindedir → düzeltilmez, `⚠️` olarak taşınır.

## 2. Üç Kapı (SYNC §4)

| Kapı | Ne zaman | Ne yapılır | Sıklık |
|------|----------|------------|--------|
| **A — Baş** | Her session başında | CRITICAL set tazelik taraması · `MEMORY.md` §20 okuma · `TODO.md` bakışı · `log.md`'ye 1 satır INFO | Her session |
| **B — Orta** | Session en az 1 kez | Değişen dosya + ilgili indeks (`index.md` · `keys.md` · `RAG.md`) tazeleme | Session başına ≥1 |
| **C — Son** | Her "tamamlandı" öncesi | C1-C5 (AŞAĞIDA) + C-Güçlendirme seti | Her kapanış |

### 2.1 Kapı C — Kapanış Maddeleri (CHECKLIST §C, birebir)

| # | Madde | İçerik | Kaynak |
|---|-------|--------|--------|
| C1 | Kanıtla doğrula | Her "tamamlandı" iddiası için 1 kanıt (komut çıktısı / dosya / test sonucu); kanıtsız iddia `VERIFICATION REQUIRED` | [[../CHECKLIST]] §C |
| C2 | Vault kapanışını yap | WORKFLOW §8.7 Bitiş 5 adım: in-place yazım → `log.md` timestamp → `MEMORY.md` §20 state → wiki-link regex doğrulama → halüsinasyon sweep | [[../CHECKLIST]] §C · WORKFLOW §8.7 |
| C3 | TODO'yu güncelle | Biten iş `[x]`, yeni iş 1 satır + kaynak; uydurma madde yok | [[../CHECKLIST]] §C |
| C4 | Post-op vault sync | `.workflows/vault-sync.md` Aşama 8 — ⚠️ araçlar diskte YOK → **MANUEL**: `log.md` append + `MEMORY.md` §20 | Aşama 8 · MEMORY Known Issue |
| C5 | Bir sonraki adımı yaz | `log.md` sonuna: `## YYYY-MM-DD` + `Sonraki adım: <1 cümle>` | Guardrail #13 |

**Yasak (CHECKLIST.md birebir):** "C1'siz 'bitti' deme · C2/C4'ü atlayıp session kapanma · C5'siz kapanış."

### 2.2 C-Güçlendirme Seti (kapanışta zorunlu)

`log.md` (append) · `MEMORY.md` §20 state · `TODO.md` (biten `[x]`, yeni 1 satır + kaynak) ·
`CHECKLIST.md` (§C kutuları) · bu session'da **değişen** hedef dosyalar 2-4 satırla güçlendirilir
(26 hedefin tamamı için geçerli: 6 kök pointer + 20 `.ai/` kök md).
**Değişmeyen dosya zorla yeniden yazılmaz** — in-place, dosya adı sabit (Guardrail #4).

## 3. Olay → Dosya Yazım Matrisi (kapanış sırasında)

| Olay (bu session'da olduysa) | Yazılan dosya | Mod | Not |
|------------------------------|---------------|-----|-----|
| Görev tamamlandı | `.ai/SESSIONS/YYYY-MM-DD-<slug>.md` | `write` (yeni dosya) | Şablon: `.ai/.templates/session-log-template.md` (7 alanlı FM) — Guardrail #16 |
| Görev tamamlandı | `.ai/log.md` | **`append` SADECE** | Format: bkz. [[log-append-rules.md]] |
| Durum değişti | `.ai/MEMORY.md` §20 (L403) | `insert-before-marker` / satır edit | `<!-- vault-sync:auto-* -->` blok içeriği (L701-741) silinmez |
| Açık iş eklendi/kapandı | `.ai/TODO.md` | `append` / satır edit | Yeni madde = 1 satır + kaynak |
| Yeni ADR kararı verildi | `.ai/brain.md` | `append` / satır edit | ADR metni frozen — yalnız özet + wiki-link |
| Yeni ADR üretildi | `.ai/.decisions/index.md` | `append` | Numaralandırma: ≥088; frozen 001-037 dokunulmaz |
| Katalog sayımı değişti | `.ai/index.md` | satır edit | Ölçüm tarihi ile |
| Yeni keyword eklendi | `.ai/keys.md` | `append` | |
| Konu→dosya eşlemesi değişti | `.ai/RAG.md` §3 | satır edit | |
| Envanter sayısı değişti | `.ai/CONTEXT.md` §3.x | satır edit | Ölçüm tarihi zorunlu |
| Terim eklendi | `.ai/glossary.md` | `append` | |
| Skill Registry değişti | `CLAUDE.md` (kök) | satır edit | Yalnız ilgili satır |
| Session logu yoktu | `.ai/log.md` | `append` 1 satır INFO | Kapı A |
| Hiçbiri | — | — | Değişmeyen dosyaya dokunma |

## 4. Eşitleme Blokları (`.claude/` ↔ `.opencode/` ↔ `.ai/`)

| İlişki | Durum (2026-10-21) | Kapanış davranışı |
|--------|--------------------|--------------------|
| `.ai/CLAUDE.md` == `.claude/CLAUDE.md` == `.opencode/CLAUDE.md` | Üçlü eşitlik hedefi | `cmp`/hash ile kontrol; eşit değilse SSOT (`.ai/`) lehine yeniden kopyalanır (`copy` modu) |
| `.claude/hooks/skill-mandate.cjs` | Hook hatırlatma yüzeyi | Hook yalnız hatırlatır; karar AI'ındır (SYNC §1) |
| `.opencode/skills/` | **KAPANDI** (2026-10-07, merge/port) | Bu skill yalnız `.claude/skills/` altındadır; `.opencode` kopyası üretilmez |

## 5. Tazelik Ölçütleri (SYNC §5 özeti — 8 kontrol)

| # | Kontrol | Örnek |
|---|---------|-------|
| 1 | Sayı iddiası ölçümle kanıtlanır | "26 dosya" → tarihli sayım |
| 2 | Wiki-link hedefi diskte var mı | `[[X]]` → `Test-Path` |
| 3 | Frontmatter 7 zorunlu alan tam mı | İlgili `.md` |
| 4 | Sürüm/updated alanları tutarlı mı | `updated: YYYY-MM-DD` |
| 5 | `log.md` append-only korundu mu | Diff: yalnız ek satır |
| 6 | `MEMORY.md` §20 bu session'ı yansıtıyor mu | State satırı |
| 7 | `TODO.md` gerçeği yansıtıyor mu | `[x]` / yeni madde + kaynak |
| 8 | Çapraz referans kırık mı | `wiki-link-check.ps1` (repo KÖKÜNDEN çalıştırılır) |

## 6. Kapı A ve B — bu skill'in öncesi (kapanışta atlanan tazeliğin kaynağı)

> Bu bölüm kapanışta **neyin kaçırılmış olabileceğini** bulmak içindir; kapı A/B
> uygulaması bu skill'in değildir (başlangıç: `.workflows/session-init.md`).

| Kapı | Tipik atlanan | Kapanışta yakalama işareti |
|------|---------------|----------------------------|
| A (Baş) | MEMORY §20 okumama → yanlış devam | Adım 4'te state tutarsızsa eski session'a bak |
| A (Baş) | log'a INFO satırı atmama | Gün içinde session başlangıcı kaydı yoksa ekleme gerekmez (C5 yeterli) |
| B (Orta) | İndeks üçlüsü güncellenmeme | Yeni dosya var ama `index.md`/`keys.md`/`RAG.md` satırı yok → Adım 3'te tamamla |
| B (Orta) | Değişen dosya güçlendirilmeme | Eski sayı/ölü atıf → Adım 2-3'te düzelt |

## 7. Eşzamanlılık & Kilit (kapanışta çakışma önleme)

> `.ai/AGENTS.md` §12 (Context Lock) kapanış yazımına da uygulanır.

| Kural | Değer | Kapanış etkisi |
|-------|-------|----------------|
| Lock süresi | Max 30 sn | Touchpoint yazımı kısa tutulur; uzun işlem lock dışına yazılır |
| Deadlock prevention | MO en eski kilidi kırar | Kapanışta çakışma → MO müdahalesi (H10) |
| Öncelik | CRITICAL > HIGH > MEDIUM > LOW | CRITICAL touchpoint (CLAUDE/AGENTS/WORKFLOW) yazımı önce |
| Logging | Lock acquire/release `log.md`'ye | Kilit olağandışı kırıldıysa log bloğuna 1 satır |

**Kapanış yazım sırası (çakışma riskini düşürür):**

```text
1. .ai/SESSIONS/ (yeni dosya — kimseyle çakışmaz)
2. .ai/log.md (append — tek yazıcı orkestratör oturumu varsayılır)
3. .ai/MEMORY.md §20 (satır edit)
4. .ai/TODO.md + CHECKLIST.md (satır edit)
5. Değişen CRITICAL touchpoint'ler (satır edit)
6. Kök pointer'lar (satır edit)
7. .claude/.opencode CLAUDE eşitleme (copy — en son, SSOT oturduktan sonra)
```

Sıranın gerekçesi: bir aşamada kesilirse (H9) önce **denetim izi** (1-2) ve
**devamlılık** (3-4) güvende kalır; en son eşitleme (7) her zaman tekrarlanabilir.

## 8. Senaryolar (hangi durumda ne kadar senkron)

| Senaryo | Adım 1 | Adım 2-3 | Adım 5 | Not |
|---------|--------|----------|--------|-----|
| Kod görevi (PHP/JS/CSS) | Tam kayıt | Etkilenen vault satırları | F3 blok | En yaygın |
| Vault doküman görevi (.md) | Tam kayıt | Güçlendirme ana iş | F3 blok + detay | Dosya listesi uzun olabilir |
| ADR kararı | Tam kayıt | brain.md + .decisions/index.md + keys.md | F3 blok | Frozen metin dokunulmaz |
| Sadece inceleme/audit (değişiklik yok) | Opsiyonel (bulgu varsa) | **Yok** (kural 4) | F1 1 satır: "audit, değişiklik yok" | Boş yere yazım yok |
| Hata sonrası düzeltme (retry) | Yeni kayıt (retry notu) | Düzeltme satırı | F1/F3: düzeltme + önceki hata | Kopyala-yapıştır yok, gerçek durum |
| Toplu güncelleme (batch) | Toplu kayıt | Batch listesi | F3 blok: dosya sayısı + aralık | architecture-write.md %31 sınırı varsa uyar |

## 9. Bağlantılar

| Hedef | İlişki |
|-------|--------|
| `.ai/SYNC.md` | SSOT — bu dosyanın kaynağı |
| `.ai/CHECKLIST.md` §C | C1-C5 otoritesi |
| `.workflows/vault-sync.md` | 7 aşama + Aşama 8 |
| `log-append-rules.md` (aynı dizin) | log girişinin kendisi |
| `sources.md` (aynı dizin) | Kaynak → bölüm eşlemesi |

---
*CoreMusic skill reference — vault-sync-post/sync-touchpoints.md — Updated: 2026-10-21*
