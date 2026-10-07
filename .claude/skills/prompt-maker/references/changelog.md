# prompt-maker — Changelog

> `metadata.version` kaydı SKILL.md frontmatter'ındadır; sürüm geçmişi bu dosyada saklanır.
> Kaynak: SKILL.md v11.x frontmatter `changelog[]` + §10.3 tablosu — v3.0 göçünde taşındı (2026-10-07).

---

## 3.0.0 — 2026-10-07

- Claude Skill v3.0 formatı: root frontmatter yalnız `name` / `description` / `license` / `metadata`
- `description` "Use when" tetikleyici Lead + Türkçe tetikleyici kelime listesi
- Zorunlu Okumalar tablosu (references/ 30 dosya, tematik gruplu) + Örnekler tablosu eklendi
- `changelog[]` → bu dosyaya taşındı; footer sonrası tekrarlanan Türkçe bloklar gövdeden temizlendi
- Anti-overthink tek satıra indirildi (kök `AGENTS.md` §5'e işaret)
- Yeni örnekler: `examples/raw-to-structured-prompt.md`, `examples/question-bank-in-use.md`
- `CLAUDE.md` sidecar'ları silindi (kullanıcı onayı 2026-10-07); `.archive/` dokunulmadı

## 11.2.0 — 2026-10-07

- claude-skill-v3.0 yapısına taşındı: SKILL.md (çekirdek korundu) + references/ indeksi + examples/ eklendi (2 örnek)

## 11.1.0 — 2026-09-29

- Faz2 içerik kalite denetimi (içerik zaten güncel doğrulandı — PICCO v11 korundu)
- Ölü referans düzeltmesi: `.ai/decisions` → `.ai/.decisions`, `.opencode/skills` → `.claude/skills`, `vault-sync` → `vault-sync-post`
- §8 eşik çelişkisi giderildi (Step 8 ↔ §8.1 tablosu), sürüm satırları 11.1.0 ile hizalandı

## 11.0.0 — 2026-08-15

- Complete rewrite — MIM format, PICCO framework integration
- Added 2026 technique catalog (CoT died, few-shot fallback)
- Added prompt injection defense section
- Added Context Engineering methodology (4-stage pipeline)
- Added structured output standards (JSON/XML)
- Removed deprecated techniques (CoT-forcing, prefilling)

## 10.1.0 — 2026-08-15

- Standardized YAML frontmatter

## 10.0.0 — 2026-08-08

- Merged with red-team-truth-mode

## 5.0.0 — 2026-08-01

- 10-step workflow, 20-section template

## 1.0.0 — 2026-07-30

- Initial release