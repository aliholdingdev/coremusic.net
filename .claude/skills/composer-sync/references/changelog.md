# Changelog — composer-sync skill

> Sürüm geçmişi. Aktif sürüm: **4.0.0** (SKILL.md `metadata.version` ile eşleşir).

## 4.0.0 — 2026-10-21

- SKILL.md tam yeniden yazım (sıfırdan, 861 satır): 500–2000 satır aralığına taşındı.
- Truth Mode: 3 resmi kaynak doğrulandı (getcomposer.org config + CLI, Microsoft Learn junction) → `references/sources.md`.
- skill-maker N1–N10 denetimi → `references/checklist.md` (PASS, BLOCK yok).
- Yeni ekler: `references/diagnostics.md` (kanıtlı çıktılar), `references/windows-junction.md` (derinlemesine), `examples/vendor-sync-loop.md` (tam döngü).
- Disk kanıtı düzeltmeleri: subdomain sayısı 8 → **5** (assets composer.json içermiyor); media'da junction yok.
- auth.coremusic.net composer.lock anomalisi (gitignore) `VERIFICATION REQUIRED` olarak kaydedildi.

## 3.0.0 — 2026-10-07

| Değişiklik | Ayrıntı |
|------------|---------|
| Claude Skill v3.0 formatına geçiş | Frontmatter name/description/license/metadata; CLAUDE.md sidecar kaldırıldı |
| Tetikleyiciler | 'composer', 'vendor', 'autoload', 'junction', 'class not found', 'bağımlılık senkron' |
| Kapsam | shared/ vs subdomain composer.json drift, vendor junction senkronu, PSR-4 çakışması, lock disiplini |
