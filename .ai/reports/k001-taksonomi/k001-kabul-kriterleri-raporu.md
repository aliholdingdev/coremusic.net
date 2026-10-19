---
title: "K001 Taksonomi Kabul Kriterleri (12 madde)"
type: report
node_id: reports/k001-taksonomi/k001-kabul-kriterleri-raporu
status: active
evidence:
  - ".ai/.decisions/draft/ADR-102-tur-taksonomi-genislemesi-5-dal.md"
  - "anayasa §7 Execution Loop · §12.1 yapısal kural · §0 zero-hallucination"
validation: "12 kriter · F5 doğrulamada tek tek işaretlenecek"
group: rapor
date: 2026-10-13
updated: 2026-10-13
---

# K001 Taksonomi — 12 Kabul Kriteri

## §1 Kriter Tablosu

| # | Kriter | Kanıt (nasıl ölçülür) | Durum |
|---|---|---|---|
| K1 | `.ai/reports/k001-taksonomi/` altında **tam 10 dosya** (7 rapor + indeks + devices + os) | glob sayısı = 10 | ⏳ F5 |
| K2 | `donanaım-type/` altında ADR-102 kapsamı 9 dal **4'er dosyaya** tamamlanır (audio 7 · hdd/ssd/display/power/cables/network/memory/cpu ×4 + kök 3 = **42 md**) — **gpu/** paralel oturumun işi (ADR-102 DIŞI, **4/4 — gpu-ornekler.md 22:49'da yazıldı**) | glob: `donanaım-type/**/*.md` = 46 (42 ADR-102 + 4 gpu) | ✅ F5 (2026-10-13 22:30 sayımı: 45/45 → 22:55 sayımı: **46/46** — ADR-102 payı 42/42 ✓, gpu payı 4/4 ✓) |
| K3 | **Yazım yasağı korunur:** `donanaım-type` yazımı hiçbir dosyada `donanim-type`/`donanım-type` olarak değiştirilmez (yeni yazılan dosyalarda doğru yazım; mevcut satır değişmez) | grep `donanim-type` = 0 yeni eşleşme; mevcut `donanım` (anayasa dahil) kasıtlı | ⏳ F5 |
| K4 | **Frozen ADR diff = 0:** accepted/ altındaki ADR-017/038/062/063/064/089 değişmez | `git status --porcelain .ai/.decisions/accepted/` = boş | ⏳ F5 |
| K5 | **Uydurma envanter yok:** yeni 18 dosya + 10 raporda hiçbir marka/model/ölçüm uydurulmaz; bilinmeyen = `UNKNOWN`, doğrulanamayan = `⚠️ VERIFICATION REQUIRED` | manuel okuma + grep `VERIFICATION REQUIRED`/`UNKNOWN` varlığı | ⏳ F5 |
| K6 | **Web kaynakları erişim tarihli:** her URL `HTTP 200` + `2026-10-13` + `HEAD/GET` notu taşır; tarihsiz URL yok | grep `http` satırları → tarihli mi | ⏳ F5 |
| K7 | **Stale citation tekrarı yok:** kapsam §7.3 T1/T2 artık eski "satır 152,153 = yasağı" atıfını taşımaz (4 edit #3 sonrası) | grep `satır 152` / `yalnız 3 bağlam` kapsamda 0 | ⏳ F4+F5 |
| K8 | **Dangling children yok:** her index.md children listesi diskteki dosyaları gösterir (display 4/4, yeni 4 dal 4/4) | her children satırı için glob → 0 eksik | ⏳ F2+F5 |
| K9 | **Yapısal edit etiketli:** anayasa §12/C10 değişikliği `PROPOSED — APPROVAL REQUIRED` taşır | grep bu etiketi anayasa §12/§10/§26'da | ⏳ F4 |
| K10 | **ADR-102 satırı indekste:** `.ai/.decisions/index.md` §4A'da ADR-102 satırı var, draft = 2, total = 93 | grep ADR-102 + sayılar | ⏳ F4 |
| K11 | **Cross-ref sınırları:** power **devre/PCB/BOM** → `K002-electroncis` cross-ref (power TİP dalı 2026-10-13'te Vault Steward onayıyla açıldı — sınır devre düzeyinde); ADR-038/089/PCM5122 kısıtları yeni dosyalarda çiğnenmez | yeni dal dosyalarında PCM5122/Class-D/PCM3168A-K001 sahipliği ihlali taraması | ⏳ F5 |
| K12 | **Commit yok:** bu iş akışında `git commit` ATILMADI (subagent + orchestrator) | `git log -1` tarihi önceki commit'e eşit | ⏳ F5 |

## §2 Sert Kapılar (biri başarısızsa iş REDDEDİLİR)

- **K4** (frozen diff ≠ 0) → geri alma zorunlu
- **K5** (uydurma envanter) → dosya düzeltmesi zorunlu
- **K12** (hakkında olmayan commit atılmışsa) → soruşturma
- **K1** (10 rapor yoksa) → eksik rapor tamamlanır

## §3 Kapı Dışı Bekleyenler (bu kriterlere girmez)

- ADR-102 onayı (Vault Steward) — taslak red/accept ayrımı ayrı
- K002 ID GAP, K000↔K002 electroncis overlap — Vault Steward kararı
- K001/index.md children + anayasa §1 "çocuk 4" revizyonu — ayrı onaylı edit (4 edit'e DAHİL DEĞİL)

**Authority:** Bayram Ali / Vault Steward
