---
title: "Doğrulama & Referanslar"
type: template-index
category: template
version: 4.11.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-10-12
date: 2026-08-09
governance: Red Team · Human Mode · Truth Mode
total_templates: 61
total_files: 66
total_lines: 20693
---


# Doğrulama & Referanslar

## 6. Doğrulama

- [ ] 7 alanlı frontmatter var (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`)
- [ ] 8 bölüm var (H1 + §1-§7)
- [ ] tüm `{{PLACEHOLDER}}`'lar dolduruldu
- [ ] dosya bu şablona uygun
- [ ] `total_*` sayıları disk sayımıyla senkron

**Quality Report (registry metrikleri):**

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 4.10.0 |
| **Toplam Dosya (registry)** | 66 md (61 şablon + 3 hub: `01-amaac-kapsam-mimari.md`, `02-kurallar-workflow.md`, `03-dogrulama-referanslar.md` + 2 meta: index.md, CLAUDE.md) |
| **Toplam Template** | 61 kayıt (`total_templates` = registry kayıtları = şablon sayısı; hub ve meta hariç) |
| **500+ derinlik** | **7/61 şablon 500+ satır** (ölçüm 2026-10-12: `Get-Content \| Measure-Object -Line` — ≥500: `prompt-maker/template` 1205 · `coremusic-vault` 666 · `hardware/arduino` 622 · `frontend/js` 543 · `personas/persona` 541 · `hardware/pic` 541 · `hardware/avr` 512) — kalan **54 şablon <500** — eski kayıt: 37/63 (2026-10-07) |
| **Kısa şablon** | en kısa 4 şablon: `prompt-maker/README.md` 81 · `frontend/css-vendor-template.md` 98 · `session-log-template.md` 101 · `frontend/css-utility-template.md` 105 (ölçüm 2026-10-12, boş satır hariç) |
| **Planlanan (Faz 6)** | **0 — KAPANDI (2026-09-29)**: `arduino` (766), `avr` (650), `pic` (687) üretildi → §7.1.7 |
| **Toplam Satır** | 20.693 (2026-10-12, 66 md; `Get-Content \| Measure-Object -Line`, boş satır hariç) — aynı küme tüm satır: **26.367** (`Get-Content .Count`, boş dahil); tarihsel seri (boş dahil): 26.813 (2026-10-07, 65 md) · 26.049 (61 md) · 20.757 (2026-10-03, 48 md) · 20.088 (2026-09-29, 41 md) · 17.984 (2026-09-28, 38 md) · 17.209 (2026-09-26, 37 md) · 16.503 (2026-09-24, 36 md) · 15.764 (32 md) · 14.695 (28 md) · 12.549 (2026-09-23, 26 md) |
| **Ortalama Satır/Template** | 334 (20.389 ÷ 61 şablon, 2026-10-12, boş satır hariç) — eski: 412 (26.813 ÷ 65 dosya, 2026-10-07) |
| **Minimum Satır** | meta min `CLAUDE.md` 72 · şablon min `prompt-maker/README.md` **81** · `frontend/css-vendor-template.md` 98 · `session-log-template.md` 101 (ölçüm 2026-10-12, boş satır hariç) |
| **Maksimum Satır** | 1205 (`prompt-maker/template.md`) — eski: 810 (`coremusic-vault-template.md`) · 766 (`hardware/arduino-template.md`, 2026-09-29) · 695 (personas/persona-template.md) |
| **Kategori** | 13 dizin (adr 7, agents 2, backend 2, documentation **9**, frontend **16**, hardware **4**, infrastructure 2, other 3, personas 1, prompt-maker 6, query 1, testing 2, ui-design 4) + kök (index.md, CLAUDE.md, 01-amaac-kapsam-mimari.md, 02-kurallar-workflow.md, 03-dogrulama-referanslar.md, session-log-template, coremusic-vault-template) |
| **Dizin Yapısı** | ✅ Alt dizinlere ayrılmış (§2 disk gerçeği) |
| **Frontmatter Uyumlu** | ✅ 39/39 7-alanlı FM (2026-09-23 betik doğrulaması: FM BAD=0; 2026-09-24 yeni 8 şablon; 2026-09-26 personas + coremusic-vault; 2026-09-29 hardware {arduino,avr,pic} de 7 alan) |
| **Ölçüm notu** | ✅ **Tazelendi (2026-10-12): 66 md** (`Get-ChildItem -Recurse -File -Filter *.md`), **20.693 satır** (`Get-Content \| Measure-Object -Line`, boş satır hariç) / 26.367 (`Get-Content .Count`, boş dahil); `total_*` bu yazımla senkron. Önceki ölçüm (2026-10-07): 65 md / 26.813 satır (`find . -name "*.md"` + `cat \| wc -l`; +4 şablon `documentation/{agents-md,workflow-md,context-md,rag-md}` = 729 satır); ondan önce 2026-10-03: 48 md / 20.757 (`Get-Content .Count`); 2026-09-29: 41 md / 20.088. |
| **Düzeltilen eski iddialar** | 25.000/5.546 satır → 12.549 · 17 şablon/19 dosya → 24 şablon/26 dosya · Planlanan 10 → 3 → **0 (2026-09-29 kapandı)** · ort. 292 → 490 · min 90/max 649 → 143/810 · "arduino/avr/pic diskte (2026-06 eski vault iddiası) → hardware tek dosya → **2026-09-29 4/4 dosya gerçekten diskte** · "10 klasör (session dahil)" → 12 dizin + kök, `session/` klasörü yok · 26 dosya/12.549 satır → 28/14.695 → 32/15.764 → 36/16.503 → 37/17.209 → 38/17.984 (2026-09-28) → **41 dosya/20.088 satır** (2026-09-29, +3: hardware arduino/avr/pic) · `ui-design` "115 dosya" → **136 dosya (119 md)** |

**REFACTOR REPORT:** FILE: index.md · PURPOSE: Template Registry Index (şablon registry + dizin) · VALIDATION: 7 alan + §1-§7 + bilgi korunumu + envanter 41/41 disk sayımı (2026-09-29, +3 şablon: hardware {arduino,avr,pic} — 5. geçiş, Faz 6 kapandı) · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

## 7. Referanslar

### 7.1 Template List (registry tabloları — disk durumuyla, 2026-09-29 gerçek ölçüm)