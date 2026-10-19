---
title: "CoreMusic — Vault Senkronizasyon Akışı"
type: workflow-instruction
version: 1.1
authority: SSOT
mode:
  - Red Team
  - Truth Mode
  - Human Mode
purpose:
  - Vault Integrity Management
  - Cross Reference Validation
  - Index Synchronization
  - Document Lifecycle Control
  - Audit Trail Maintenance
reference:
  authority: ".ai/WORKFLOW.md"
  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/AGENTS.md"
    - ".ai/WORKFLOW.md"
    - ".ai/brain.md"
    - ".ai/index.md"
    - ".ai/keys.md"
    - ".ai/MEMORY.md"
    - ".ai/log.md"
    - ".ai/engine.md"
  architecture:
    - ".ai/.decisions/"
    - ".ai/architecture/adr/"
    - "Existing project architecture"
    - "Existing codebase patterns"
  project_structure:
    - "coremusic.net/"
    - "shared/"
    - "api.coremusic.net/"
    - "auth.coremusic.net/"
    - "music.coremusic.net/"
    - "admin.coremusic.net/"
    - "home.coremusic.net/"
    - "car.coremusic.net/"
    - "studio.coremusic.net/"
    - "pro.coremusic.net/"
    - "media.coremusic.net/"
    - "download.coremusic.net/"
  decision_priority:
    - "ADR decisions"
    - "Architecture documentation"
    - "Security requirements"
    - "Existing implementation"
    - "User requirements"
  update_policy:
    preserve_existing_structure: true
    require_approval_for:
      - "vault structure change"
      - "index format change"
      - "cross reference policy change"
      - "document lifecycle change"
changelog:
  - version: 1.1
    date: 2026-09-24
    changes:
      - İki ADR serisi hizası (.ai/.decisions + .ai/architecture/adr ADR-023…026)
      - Aşama 4'e adr/ index kaydı + Aşama 8 post-operation senkron eklendi
  - version: 1.0
    date: 2026-08-15
    changes:
      - Initial vault sync workflow
      - Added 5-question analysis
      - Added 6-step sync process
      - Added cross reference validation
---

# Vault Senkronizasyon Akışı

## 1. Amaç

`.ai/` vault'unun tutarlı, güncel ve doğru olmasını sağlamak.

## 2. Akış Diyagramı

```
VAULT DEĞİŞİKLİĞİ
       |
       v
┌──────────────────────────┐
│  1. DEĞİŞİKLİK TESPİTİ   │  Hangi dosya değişti?
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  2. 5 SORU ANALİZİ       │  Temel soruları cevapla
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  3. DOSYA GÜNCELLEME      │  İlgili dokümanı güncelle
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  4. İNDEKS GÜNCELLEME     │  index.md, keys.md güncelle
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  5. ÇAPRAZ REFERANS       │  Linkleri doğrula
│     KONTROLÜ             │
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  6. LOG YAZMA            │  .ai/log.md'ye kayıt
└──────────┬───────────────┘
           v
┌──────────────────────────┐
│  7. DOĞRULAMA            │  Tutarlılık kontrolü
└──────────┬───────────────┘
           v
       TAMAM
```

## 3. 5 Soru Analizi

Her vault değişikliği öncesi bu soruları cevapla:

| # | Soru | Amaç |
|---|------|------|
| 1 | **Bu değişiklik zorunlu mu?** | Gereksiz değişikliği önle |
| 2 | **Mevcut yapıyı bozuyor mu?** | Bütünlüğü koru |
| 3 | **Çapraz referansları etkiliyor mu?** | Link tutarlılığını sağla |
| 4 | **İndекс'i güncelliyor mu?** | Erişilebilirliği koru |
| 5 | **Log'a yazıldı mı?** | Denetim izi oluştur |

## 4. Aşama Ayrıntıları

### Aşama 1: Değişiklik Tespiti

- Değişen dosyayı belirle
- Değişiklik türünü sınıflandır (yeni/güncelleme/silme)
- Etkilenen dosyaları listele

### Aşama 2: 5 Soru Analizi

Her soru için EVET/HAYIR cevabı ver:
- Tümü EVET ise devam et
- Herhangi biri HAYIR ise dur ve değerlendir

### Aşama 3: Dosya Güncelleme

- Dosyayı yerinde güncelle (dosya adını değiştirme)
- Format tutarlılığını koru
- Frontmatter'ı güncelle

### Aşama 4: İndeks Güncelleme

| İndeks | Güncellenme |
|--------|-------------|
| `.ai/index.md` | Yeni dosya eklendi mi? |
| `.ai/keys.md` | Keyword eklendi mi? (K0-K20 / A0-A5 mapping) |
| `.ai/brain.md` | Mimari karar değişti mi? |
| `.ai/CLAUDE.md` | Kural değişti mi? |
| `.ai/AGENTS.md` | Agent değişti mi? |
| `.ai/.decisions/index.md` | Karar serisi ADR kaydı eklendi/güncellendi mi? (yeni no ≥ ADR-090) |
| `.ai/architecture/adr/` (seri) | Mimari ADR serisi kaydı (ADR-023…026; kendi numara uzayı) |

### Aşama 5: Çapraz Referans Kontrolü

- Tüm wiki-link'leri doğrula
- Kırık link var mı kontrol et
- Yeni referansları ekle

### Aşama 6: Log Yazma

```markdown
## YYYY-MM-DD HH:mm

**İşlem:** Vault senkronizasyonu
**Agent:** vault-updater
**Dosyalar:** [Değişen dosyalar]
**Sonuç:** Başarılı/Başarısız
```

### Aşama 7: Doğrulama

- Tüm indeksler güncel mi?
- Çapraz referanslar doğru mu?
- Log yazıldı mı?
- Tutarlılık var mı?

### Aşama 8: İşlem Sonrası Vault Senkronu (zorunlu — 2026-09-24)

| # | Adım | Komut |
|---|------|-------|
| 1 | Session kaydı | `node .ai/scripts/session-save.mjs --task "<gorev-aciklamasi>" --status completed --agent <agent-adi>` |
| 2 | Vault güncelleme | `node .ai/scripts/vault-post-update.mjs --scope root` |
| 3 | Sonuç doğrulama | `log.md`, `MEMORY.md`, `project-state.md` güncellendi mi? (salt-okunur) |
| 4 | Session checklist kapanışı | `.ai/CHECKLIST.md` §C — 5/5 `[x]`; script yoksa manuel (Aşama 6 log append + `MEMORY.md` §20) |
| 5 | Sürekli güçlendirme | 20 hedef dosyalık set (3 kök + `.ai/` kök 17 md — `.ai/CHECKLIST.md` §A0 sınıflandırması: CRITICAL 16 / ON-DEMAND 3 / LOG 1) gözden geçirilir: bu session'da **değişenler** 2-4 satırla güçlendirilir, değişmeyenlere dokunulmaz (in-place, dosya adı sabit) |

> ⚠️ VERIFICATION REQUIRED (2026-09-29): 1-3. satırlardaki `.ai/scripts/session-save.mjs`, `.ai/scripts/vault-post-update.mjs` ve `project-state.md` **diskte YOK** (kayıt: `MEMORY.md` §20 Known Issue 2026-09-28 + filesystem taraması) → kapanış bu haliyle manuel yapılır; script üretilince bu not kaldırılır.

**Yoksayma sonucu:** audit trail boşluğu. Başarızsa `status: pending` + max 3 retry. Yazım sadece `vault-utf8-writer` ile yapılır (PowerShell yazım cmdlet'leri YASAK); `log.md` yalnız append.

---

## 5. Hata Yönetimi

| Durum | Aksiyon |
|-------|---------|
| Kırık referans | Düzelt veya raporla |
| Eksik indeks | İndekse ekle |
| Tutarsızlık | İnsan onayı iste |
| Log yazma hatası | Tekrar dene |

## 6. Yasaklar

- Dosya adını/yerini değiştirme
- İndeks'i güncellemeden dosya ekleme
- Log yazmadan işlem yapma
- Çapraz referans kontrolünü atlama

## 7. İlgili Dosyalar

- `.ai/index.md`
- `.ai/keys.md`
- `.ai/brain.md`
- `.ai/CLAUDE.md`
- `.ai/AGENTS.md`
- `.ai/log.md`
- `.ai/MEMORY.md`
- `.ai/CHECKLIST.md`
- `.ai/TODO.md`

## 8. Aktivasyon

"vault sync", "bütünlük kontrol", "referans doğrula", "index güncelle"

---

*Vault Senkronizasyon Akışı v1.1.0 — CoreMusic Workflow System*
*Authority: Bayram Ali / Vault Steward*
*Mode: Red Team · Truth Mode · Human Mode*
