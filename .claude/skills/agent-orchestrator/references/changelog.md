# agent-orchestrator — Sürüm Geçmişi (changelog)

> N10 gereği kök `changelog[]` yerine bu dosya yaşar. Girişler disk/git kanıtıyla
> yazılır; kanıtı olmayan sürüm **UNKNOWN** olarak işaretlenir — uydurulmaz.
>
> **Skill:** agent-orchestrator · **Updated:** 2026-10-21

---

## 3.0.0 — 2026-10-07

- `metadata.version: 3.0.0` · `metadata.updated: 2026-10-07` (mevcut frontmatter kanıtı).
- **Orchestration merge (2026-10-07):** risk/izin/HITL/escalation katmanı
  `references/workflow-dispatch.md` olarak indekslendi (SKILL.md §20 satırı kanıtı).
- `previous-version: "4.1"` alanı korundu — sürüm zinciri tutarsızlığı
  **UNKNOWN** (4.1 → 3.0.0 sıçramasının gerekçesi diskte belgelenmemiş).

## 4.1 / önceki sürümler — UNKNOWN

- Kök registry (`~/.config/opencode/AGENTS.md`) agent-orchestrator'ı "Hibrit
  orkestratör" tanımıyla listeler; ayrıntı sürüm geçmişi diskte mevcut değil.
- Kanıt bulunana kadar bu aralık **UNKNOWN**'dur; tahmini tarih/sürüm yazılmaz.

## 2026-10-21 — reset (çalışma ağacı, commit edilmedi)

- **Referans bütünlüğü onarımı (N4):** `references/workflow-dispatch.md` ve
  `references/changelog.md` SKILL.md'de linkliydi ama diskte yoktu (eksik
  hedef) — her ikisi de yeniden yazıldı; `references/sources.md` diskte vardı
  ama SKILL.md §20 tablosunda linkli değildi — tabloya eklendi (orphan=0).
- **Tarih senkronu (N9):** `metadata.updated` ve footer `2026-10-07` →
  `2026-10-21`.
- Ölçüm: SKILL.md 662 satır · dizin toplamı ≥1000 (bkz. teslim raporu).

---

**Kaynak:** frontmatter/footer (disk) + SKILL.md §20 indeks satırı ·
`references/workflow-dispatch.md` başlığı.
