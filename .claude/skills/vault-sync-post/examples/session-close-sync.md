# Örnek — Session Kapanışında Vault Sync (Çalışmış Örnek)

> Girdi → çıktı tam döngüsü. Ana skill: [SKILL.md](../SKILL.md).

## Girdi

Session tamamlandı: "Footer responsive düzeltme" görevi bitti, `footer.php` ve
`_footer.css` değiştirildi, agent = `ui`, status = `completed`.

## Adımlar

### 1. Root `.ai/` touchpoint tazelenmesi (12 kök dosya)

Değişiklikten etkilenen kök dosyalar güncellenir (SSOT hiyerarşisi korunur):
`.ai/CLAUDE.md` · `.ai/AGENTS.md` · `.ai/WORKFLOW.md` · `.ai/brain.md` ·
`.ai/index.md` · `.ai/keys.md` · `.ai/MEMORY.md` · `.ai/log.md` ·
`.ai/engine.md` · `.ai/glossary.md` · `.ai/VISION.md` · `.ai/PROJECTS.md`.

Kural: yazım **yalnız** `node .ai/scripts/vault-utf8-writer.mjs` üzerinden
(append modu). PowerShell ile `.ai/` yazımı YASAK (Windows-1254/BOM/UTF-16 bozulması).

> ⚠️ VERIFICATION REQUIRED — `session-save.mjs` / `vault-post-update.mjs` araçları
> diskte yok; süreç manuel yürütülür.

### 2. `.claude/` + `.opencode/` senkronizasyonu

`.ai/CLAUDE.md` (SSOT) → kopya dosyalar eşitlenir:

```text
.ai/CLAUDE.md  ==  .claude/CLAUDE.md  ==  .opencode/CLAUDE.md
```

Eşitlik kontrolü: `cmp .ai/CLAUDE.md .claude/CLAUDE.md` (ve `.opencode` için aynı).
Eşit değilse SSOT lehine `.ai/` üzerinden yeniden kopyalanır.

### 3. `log.md` append satırı

```markdown
## 2026-10-07 17:42

**Action:** Footer responsive düzeltme — vault sync (session kapanışı)
**Agent:** ui
**Files:** footer.php, _footer.css, .ai/log.md
**Result:** completed — root .md touchpoints tazelendi, .claude/.opencode sync OK
**Risk Level:** Low
```

## Çıktı (Doğrulama listesi)

- [x] `.ai/sessions/YYYY-MM-DD-HH-MM-SS.md` oluşturuldu (session log)
- [x] `.ai/MEMORY.md` §18 Session History güncellendi (≤1000 satır sınırı)
- [x] `.ai/log.md`'ye **append-only** tek satır eklendi (mevcut satıra dokunulmadı)
- [x] `.ai/sessions/context/project-state.md` güncellendi
- [x] `.claude/CLAUDE.md` == `.ai/CLAUDE.md`
- [x] `.opencode/CLAUDE.md` == `.ai/CLAUDE.md`

## Hata Durumları

| Durum | Aksiyon |
|-------|---------|
| Yazılmadan önce eşitlik bozuk | SSOT `.ai/` lehine yeniden kopyala |
| log.md'ye overwrite yapıldı | revert — log append-only'dır (ADR-042) |
| UTF-8 bozuk (mojibake) | vault-utf8-writer.mjs dışında yazım → düzelt + CRITICAL log |

---
*CoreMusic example v3.0 — vault-sync-post/examples/session-close-sync.md — Updated: 2026-10-07*