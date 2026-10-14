---
title: "K001 Taksonomi Uygulama Planı Raporu"
type: report
node_id: reports/k001-taksonomi/k001-uygulama-plani-raporu
status: active
evidence:
  - ".ai/.decisions/draft/ADR-102-tur-taksonomi-genislemesi-5-dal.md §7 (10 adım)"
  - "3 docs-writer subagent (background, 2026-10-13, 18 dosya)"
  - "4 onaylı edit: donanaım-type/index.md · donanım-anayasa.md · donanaım-type-kapsam.md · .ai/.decisions/index.md"
validation: "Plan ADR-102 §7 ile hizalı · subagent commit ATMAYACAK (orkestratöre ait)"
open_questions:
  - "ADR-102 onayı: accepted/ ← taslak taşınır (Vault Steward)"
  - "4 edit'ten sonra stale-citation 21 eşleşme → kalıntısı doğrulanacak"
group: rapor
date: 2026-10-13
updated: 2026-10-13
---

# K001 Taksonomi Uygulama Planı

## §1 Fazlar

| Faz | İşlem | Sorumlu | Durum |
|---|---|---|---|
| F1 | ADR-102 taslağı yazımı | orchestrator | ✓ TAMAM |
| F2 | 18 dal dosyası (display 2 + cables/network/memory/cpu ×4) | 3 docs-writer subagent (background) | ✓ TAMAM (2026-10-13 — cpu subagent rate-limit'i dosyalama SONRASI aldı; dosyalar tam) |
| F3 | 10 rapor dosyası (.ai/reports/k001-taksonomi/) | orchestrator | ✓ TAMAM |
| F4 | 4 onaylı edit | orchestrator | ⏳ SIRADA |
| F5 | Doğrulama + §16 Türkçe rapor | orchestrator | ⏳ |
| F6 | ADR-102 onayı → accepted/ (Vault Steward) | Vault Steward | ⏳ ADR onayı ayrı |
| F7 | git commit (tüm değişiklikler) | orchestrator (subagent ASLA) | ⏳ bu oturumda YOK |

## §2 4 Onaylı Edit (F4 detayı)

| # | Dosya | Değişiklik | Yapısal? |
|---|---|---|---|
| 1 | `donanaım-type/index.md` | children'a display/cables/network/memory/cpu ekle (5 satır) + §GRUPLAR'a 5 grup satırı | Hayır (envanter) |
| 2 | `donanım-anayasa.md` | §10/3 (satır 195: Q3 artık "cevaplandı: 8 — ADR-102") + §12/C10 (satır 250: 3→8, ADR-102 atıflı) + §22/§25 K002 satırları + §26 kayıt | **EVET (C10)** → `PROPOSED — APPROVAL REQUIRED` etiketi |
| 3 | `donanaım-type-kapsam.md` | §7.3 T1/T2 stale-citation düzeltmesi (anayasa §7 satır 152-153 = Hata Modeli, §10 = satır 191+) | Hayır (atıf düzeltmesi) |
| 4 | `.ai/.decisions/index.md` | §4A (satır 148 altına) ADR-102 satırı + draft 1→2, total 92→93 (satır 33/41/48) | Hayır (envanter) |

### §2.1 Yapısal Düzenleme Kuralı (anayasa §12.1)

Edit #2'de §12/C10 değiştirildiği için her yapısal satır `PROPOSED — APPROVAL REQUIRED`
etiketi taşır; etiketsiz yapısal değişiklik yasaktır. ADR-102 `accepted/`'a taşınana
kadar anayasa metnindeki 9 bağlam iddiası "taslak" niteliğindedir.

## §3 Zaman Çizelgesi (bu oturum)

| Adım | Tahmini |
|---|---|
| F2 subagent tamamlanma | 5–15 dk (background) |
| F4 4 edit | 5 dk |
| F5 doğrulama (yazım yasağı taraması, frozen ADR diff=0, stale-citation, dangling children) | 3 dk |
| Toplam | ~20 dk |

## §4 Riskler

| Risk | Etkileşim | Önlem |
|---|---|---|
| Subagent bir üst indekse dokunursa | donanaım-type/index.md çakışma | Prompt'ta yasaklandı; F4 öncesi dosya diff kontrolü |
| Subagent uydurma URL yazarsa | zero-hallucination ihlali | F5'te URL taraması (erişim tarihli olmayan) |
| 4 edit frozen ADR'ye dokunursa | diff≠0 gate ihlali | F5'te `git status` + `git diff --stat` frozen/ |
| ADR-102 reddedilirse | 18 dosya orphan | Dosyalar `status: proposed` kalır; kaldırma kararı ayrı |

**Authority:** Bayram Ali / Vault Steward
