---
title: "17-domain-d08-yapay-zeka-medya — Mimari Domain Tablosu d08"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md (giriş) · bu dosya: d08 katman tablosunun tek kaynağı"
updated: 2026-10-07
tier: 3
domain: architecture-d08
ssot: true
risk: medium
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# 17-domain-d08-yapay-zeka-medya — Domain d08: Yapay Zeka & Medya (K350–K399)

> **Kapsam:** AI alt sisteminin PHP çekirdeği (shared/src/AI), AI/multimedya şemaları, öneri/analiz/indirme hedefleri.
> **Eski dizin karşılıkları:** [[architecture/k4-yapay-zeka]] · [[architecture/k15-medya-streaming]] · **Legacy K4 + K15** → bu domain.
> **Gerçeklik notu (2026-10-07):** `shared/src/AI/` = **7 sınıf gerçektir** (AIEngine, AIOrchestrator, AIWorkflow, KnowledgeBase, MemorySystem, PromptEngine, ToolCalling). Öneri/analiz/voice ve download servisi kodu **YOK** → PLANNED.

## Katman Tablosu (K350–K399 · 50 katman)

| K-id | Katman adı | Durum | Kanıt |
|------|-----------|-------|-------|
| K350 | AIEngine | IMPLEMENTED | shared/src/AI/AIEngine.php |
| K351 | AIOrchestrator | IMPLEMENTED | shared/src/AI/AIOrchestrator.php |
| K352 | AIWorkflow | IMPLEMENTED | shared/src/AI/AIWorkflow.php |
| K353 | KnowledgeBase | IMPLEMENTED | shared/src/AI/KnowledgeBase.php |
| K354 | MemorySystem | IMPLEMENTED | shared/src/AI/MemorySystem.php |
| K355 | PromptEngine | IMPLEMENTED | shared/src/AI/PromptEngine.php · ADR-035 |
| K356 | ToolCalling | IMPLEMENTED | shared/src/AI/ToolCalling.php |
| K357 | AI veritabanı (tercih/özellik/öneri) | IMPLEMENTED | .ai/.sql/mysql/coremusic_ai.sql · ADR-075 |
| K358 | AI Contracts (sözleşme sınıfları) | IMPLEMENTED | shared/src/Contracts/AI/ (dizin — 2026-10-07 find) |
| K359 | AI strateji çekirdeği | DESIGN | ADR-030 (.ai/.decisions/accepted/ADR-030-ai-strategy-core.md) |
| K360 | System prompt engineering | DESIGN | ADR-035 (.ai/.decisions/accepted/ADR-035-system-prompt-engineering.md) |
| K361 | Multi-proje prompt maker | DESIGN | ADR-036 · .claude/skills/prompt-maker/SKILL.md (Skill Registry) |
| K362 | Music analysis (10 yetenek) | PLANNED | .claude/CLAUDE.md §5 (K4) · ⚠️ VERIFICATION REQUIRED (kod yok) |
| K363 | Recommendation (5 yetenek) | PLANNED | ADR-030 · ⚠️ VERIFICATION REQUIRED (kod yok) |
| K364 | Voice (5 yetenek) | PLANNED | .claude/CLAUDE.md §5 (K4) · ⚠️ VERIFICATION REQUIRED (kod yok) |
| K365 | Edge AI | PLANNED | .claude/CLAUDE.md §5 (K4) · ⚠️ VERIFICATION REQUIRED |
| K366 | Podcast şeması | IMPLEMENTED | ADR-073 · .ai/.sql/mysql/coremusic_musics.sql (podcast kapsamı) |
| K367 | Radio şeması | IMPLEMENTED | ADR-074 · .ai/.sql/mysql/coremusic_musics.sql (radio kapsamı) |
| K368 | Video şeması | IMPLEMENTED | ADR-076 · .ai/.sql/mysql/coremusic_musics.sql (video kapsamı) |
| K369 | Medya katalog şeması (media_catalog) | PLANNED | .ai/.sql/mysql/media_catalog.sql (canlıda yok) · ADR-092 |
| K370 | Medya dizin ekseni (fiziksel dosya) | PARTIAL | ADR-092 · media.coremusic.net/src/Media/Ulid.php · CatalogWriter.php |
| K371 | FFmpeg pipeline | PLANNED | ⚠️ VERIFICATION REQUIRED — kod yok (hedef: .claude/CLAUDE.md §5 K15) |
| K372 | HLS / DASH streaming | PLANNED | ⚠️ VERIFICATION REQUIRED — kod yok |
| K373 | ID3 / FLAC metadata okuma | PLANNED | ⚠️ VERIFICATION REQUIRED — kod yok |
| K374 | AI auto-download pipeline | PLANNED | .claude/CLAUDE.md §12 · ADR-026 · ⚠️ kod yok |
| K375 | Anti-ban sistemi | PLANNED | ADR-028 · ⚠️ VERIFICATION REQUIRED (kod yok) |
| K376 | Multi-provider data sync | PLANNED | ADR-081 · ⚠️ VERIFICATION REQUIRED (kod yok) |
| K377 | Download veritabanı (queue/history) | IMPLEMENTED | .ai/.sql/mysql/coremusic_download.sql |
| K378 | Rezerve — d08 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K379 | Rezerve — d08 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K380 | Rezerve — d08 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K381 | Rezerve — d08 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K382 | Rezerve — d08 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K383 | Rezerve — d08 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K384 | Rezerve — d08 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K385 | Rezerve — d08 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K386 | Rezerve — d08 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K387 | Rezerve — d08 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K388 | Rezerve — d08 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K389 | Rezerve — d08 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K390 | Rezerve — d08 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K391 | Rezerve — d08 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K392 | Rezerve — d08 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K393 | Rezerve — d08 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K394 | Rezerve — d08 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K395 | Rezerve — d08 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K396 | Rezerve — d08 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K397 | Rezerve — d08 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K398 | Rezerve — d08 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K399 | Rezerve — d08 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |

**Sayım:** 50 satır (K350–K399) · IMPLEMENTED 13 · PARTIAL 1 · DESIGN 3 · PLANNED 33 (2026-10-07 disk ölçümü).

**İlgili ADR:** ADR-026 · ADR-028 · ADR-030 · ADR-035 · ADR-036 · ADR-073 · ADR-074 · ADR-075 · ADR-076 · ADR-081 · ADR-092 (`.ai/.decisions/accepted/`)
