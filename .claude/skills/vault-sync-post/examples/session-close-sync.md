# Örnek — Session Kapanışında Vault Sync (Çalışmış Örnek)

> Girdi → çıktı tam döngüsü. Ana skill: [SKILL.md](../SKILL.md).
> Bu örnek v4.0.0'da yeniden yazıldı: `.ai/SESSIONS/` (gerçek dizin), manuel senkron gerçekliği
> (araçlar diskte yok) ve 26 dosyalık SYNC seti ile hizalı.

## Girdi

Session tamamlandı: "Footer responsive düzeltme" görevi bitti, `footer.php` ve
`_footer.css` değiştirildi, agent = `ui`, status = `completed`.

## Adımlar

### Adım 0 — Durum toplama (salt-okunur)

Değişen dosyalar, session özeti ve etkilenen touchpoint'ler toplanır. Yazım yok.

### Adım 1 — Session kaydı (`.ai/SESSIONS/`)

Session kaydı `.ai/SESSIONS/YYYY-MM-DD-<slug.md>` dosyasına yazılır (dizin **büyük harf** —
diskte 9 dosya ile doğrulandı). Frontmatter alanları Guardrail #16 şablonundan okunur:
`.ai/.templates/session-log-template.md` (7 alanlı frontmatter).

```text
.ai/SESSIONS/2026-10-21-footer-responsive.md   ← yeni kayıt (write modu)
```

> ⚠️ VERIFICATION REQUIRED — `session-save.mjs` aracı diskte YOK (2026-10-21 Test-Path = False);
> kayıt elle, `vault-utf8-writer.mjs write` modu ile yapılır. Eski sürümdeki
> `.ai/sessions/` (küçük harf) ve `context/project-state.md` yolları yanlıştı — düzeltilmiştir.

### Adım 2 — Vault touchpoint tazelenmesi (26 dosyalık set — değişenler)

Değişiklikten etkilenen dosyalar SYNC.md §3 setinden güncellenir (SSOT hiyerarşisi korunur):
`.ai/CLAUDE.md` · `.ai/AGENTS.md` · `.ai/WORKFLOW.md` · `.ai/brain.md` ·
`.ai/index.md` · `.ai/keys.md` · `.ai/MEMORY.md` · `.ai/log.md` ·
`.ai/engine.md` · `.ai/glossary.md` · `.ai/VISION.md` · `.ai/PROJECTS.md`.
(Kökte ayrıca: `CLAUDE.md` · `AGENTS.md` · `README.md` · `WORKFLOW.md` · `CONTEXT.md` · `RAG.md` —
yalnız ilgili satır.)

Kural: yazım **yalnız** `node .ai/scripts/vault-utf8-writer.mjs` üzerinden
(append / insert-before-marker / write). PowerShell ile `.ai/` yazımı YASAK
(Windows-1254/BOM/UTF-16 bozulması). Değişmeyen dosyaya dokunulmaz.

### Adım 2b — `.claude/` + `.opencode/` senkronizasyonu

`.ai/CLAUDE.md` (SSOT) → kopya dosyalar eşitlenir:

```text
.ai/CLAUDE.md  ==  .claude/CLAUDE.md  ==  .opencode/CLAUDE.md
```

Eşitlik kontrolü: `cmp .ai/CLAUDE.md .claude/CLAUDE.md` (ve `.opencode` için aynı).
Eşit değilse SSOT lehine `.ai/` üzerinden yeniden kopyalanır (`copy` modu).
Not: `.opencode/skills/` kapatıldı (2026-10-07, merge/port edildi) — skill kopyası üretilmez.

### Adım 3 — `MEMORY.md` §20 (Current Session State, L403)

§20 state satırı bu session'ı yansıtacak şekilde güncellenir;
`<!-- vault-sync:auto-begin -->` (L701) / `<!-- vault-sync:auto-end -->` (L741) bloğunun
içeriği silinmeden, `insert-before-marker` ile tazelenir. Known Issue satırı (L411) korunur.

### Adım 4 — `log.md` append satırı (append-only)

```markdown
## 2026-10-21 17:42

**İşlem:** Footer responsive düzeltme — vault sync (session kapanışı)
**Agent:** ui
**Files:** footer.php, _footer.css, .ai/log.md
**Result:** completed — touchpoints tazelendi, .claude/.opencode eşitlik OK
**Sonraki adım:** Footer CTA hover durumu QA kontrolüne gönderilecek
```

(Format: `.workflows/vault-sync.md` Aşama 6 · `Sonraki adım` satırı: CHECKLIST C5.)

## Çıktı (Doğrulama listesi)

- [x] `.ai/SESSIONS/2026-10-21-footer-responsive.md` oluşturuldu (7 alanlı FM — şablon okundu)
- [x] `MEMORY.md` §20 Current Session State güncellendi (auto-blok içeriği korundu)
- [x] `log.md`'ye **append-only** tek blok eklendi (mevcut satıra dokunulmadı)
- [x] Değişen touchpoint dosyaları 2-4 satır güçlendirildi; değişmeyenlere dokunulmadı
- [x] `TODO.md`: biten `[x]`, yeni iş 1 satır + kaynak
- [x] `CHECKLIST.md` §C kutuları işaretlendi (C1-C5)
- [x] `.claude/CLAUDE.md` == `.ai/CLAUDE.md`
- [x] `.opencode/CLAUDE.md` == `.ai/CLAUDE.md`
- [x] Wiki-link regex doğrulama + halüsinasyon sweep (C2 son iki adımı)
- [x] Commit ATILMADI — kapanış raporu orkestratöre sunuldu (subagent commit atmaz)

## Hata Durumları

| Durum | Aksiyon |
|-------|---------|
| Yazılmadan önce eşitlik bozuk | SSOT `.ai/` lehine yeniden kopyala (`copy` modu) |
| log.md'ye overwrite yapıldı | revert — log append-only'dır (ADR-042); append'i tekrarla |
| UTF-8 bozuk (mojibake) | vault-utf8-writer.mjs dışında yazım → yedekten onar (`repair`) + CRITICAL log |
| Eksik araç senaryosu (`session-save.mjs` vb.) | ⚠️ biliniyor — manuel akış yukarıdaki adımlardır; araç yok diye süreç atlanmaz |

## Varyant — Partial Kapanış (kısmi iş)

Aynı görev yarım kaldıysa (ör. CSS düzeltildi, QA bekliyor) Adım 0-7 aynıdır, farklar:

| Adım | Fark |
|------|------|
| Adım 1 | Session kaydında `Status: partial` + §3.5 Issues satırı: "QA kontrolü bekliyor" |
| Adım 2-3 | Yalnız **gerçekten değişen** dosyalar güçlendirilir (yarım işin iddiası yazılmaz) |
| Adım 5 | Log bloğu: `Result: partial — CSS tamam, QA beklemede` |
| Adım 5 (C5) | `Sonraki adım: QA kontrolü tamamlanınca completed kapanış` |
| Adım 7 | Rapor durumu `partial`; orkestratöre açık kalan iş listesi |

> Partial kapanış "hata" değildir — yarım işi gizlemek hatadır. C1 kanıtı mevcut kısım
> içindir; tamamlanan kısmın iddiası abartılmaz.

---
*CoreMusic example v3.0 — vault-sync-post/examples/session-close-sync.md — Updated: 2026-10-21*
