---
title: "CoreMusic — RAG (Retrieval-Augmented Generation) Retrieval Pointer & Router"
type: rag-pointer
category: navigation
version: 2.0.0
status: active
authority: "SSOT: .ai/RAG.md — retrieval indeksi + pipeline tam metin `.ai/RAG.md`'dedir"
updated: 2026-11-01
---

# CoreMusic — RAG Retrieval Pointer & Router

> **RAG (Retrieval-Augmented Generation) Sistemi:**  
> AI veya geliştiricinin devasa kod tabanı ve vault'u körlemesine tarayıp token/limit tüketmesini önlemek için tasarlanmış **akıllı bilgi erişim yönlendiricisidir** (ADR-042).  
> **Tam Detay & Pipeline SSOT:** [`.ai/RAG.md`](file:///c:/www/coremusic.net/.ai/RAG.md)  
> **Kullanıcı RAG Kılavuzu (DOGE DOC):** [`RAG-KILAVUZU.md`](file:///c:/www/coremusic.net/RAG-KILAVUZU.md)

---

## ⚡ Hızlı RAG Arama & Eşleme Matrisi (Quick Retrieval Router)

AI veya kullanıcı bir göreve başladığında tüm dosyaları taramaz; aşağıdaki rota matrisinden **yalnızca ilgili hedef dosyayı** ve hedef alanın tek sayfalık `durum.md` dosyasını çeker:

| Soru / Görev Alanı | Birincil Hedef (RAG Target) | İkincil / Tamamlayıcı | Modül Hafızası (Tek Sayfa) |
|---|---|---|---|
| **Oturum Başlangıcı & Durum Devri** | [`.ai/durum.md`](file:///c:/www/coremusic.net/.ai/durum.md) | [`.ai/MEMORY.md`](file:///c:/www/coremusic.net/.ai/MEMORY.md) | `[subdomain]/durum.md` |
| **Temel Mühendislik Direktifleri** | [`.ai/.rules/kurallar.md`](file:///c:/www/coremusic.net/.ai/.rules/kurallar.md) (`KURAL-01..10`) | [`.ai/.rules/senior-mode.md`](file:///c:/www/coremusic.net/.ai/.rules/senior-mode.md) | — |
| **Pre-Flight Hata Önleme Kontrolü** | [`.ai/.rules/hata-listesi.md`](file:///c:/www/coremusic.net/.ai/.rules/hata-listesi.md) (`HL-01..12`) | [`.ai/CLAUDE.md`](file:///c:/www/coremusic.net/.ai/CLAUDE.md) (Guardrails) | — |
| **Onaylı Karar Kuralları & ADR'ler** | [`.ai/.rules/karar-kurallari.md`](file:///c:/www/coremusic.net/.ai/.rules/karar-kurallari.md) | [`.ai/brain.md`](file:///c:/www/coremusic.net/.ai/brain.md) + `.ai/.decisions/` | — |
| **Figma API & Tasarım Tokenleri** | [`.ai/.env.figma`](file:///c:/www/coremusic.net/.ai/.env.figma) (`FIGMA_TOKEN`) | `.ai/ui-design/screens/` | `assets.coremusic.net/durum.md` |
| **Frontend UI / CSS Katmanları (Light Mode)**| `assets.coremusic.net/Css/` (01..11) | `home.coremusic.net/pages/` | `home.coremusic.net/durum.md` |
| **Backend API, Routing & Controller** | `api.coremusic.net/config/routes.php` | `shared/src/Api/` | `api.coremusic.net/durum.md` |
| **Ortak Çekirdek, PDO & Database** | `shared/src/Database/` | `.ai/.sql/mysql/` (18 BCNF) | `shared/durum.md` |
| **Yetkilendirme, JWT & Oturum (Auth)** | `auth.coremusic.net/src/` | `shared/src/Middleware/` | `auth.coremusic.net/durum.md` |
| **Neva Ses Motoru, C++20 DSP & Amfi** | `app-cpp-projects/` | `ADR-038` (Class AB Topoloji) | `app-cpp-projects/durum.md` |
| **Kiro Görev Planı & Tasks** | [TODO.md](file:///c:/www/coremusic.net/TODO.md) | [.workflows/tasks.md](file:///c:/www/coremusic.net/.workflows/tasks.md) | - |
| **Kiro Teknik Tasarım (Design)** | [.workflows/design.md](file:///c:/www/coremusic.net/.workflows/design.md) | [.ai/brain.md](file:///c:/www/coremusic.net/.ai/brain.md) | - |
| **Kiro Ajan Yürütme Akışı (Workflow)** | [.workflows/workflow.md](file:///c:/www/coremusic.net/.workflows/workflow.md) | [WORKFLOW.md](file:///c:/www/coremusic.net/WORKFLOW.md) | - |
| **Platform Ürün Gereksinimleri** | [PRD.md](file:///c:/www/coremusic.net/PRD.md) | [.ai/PRD.md](file:///c:/www/coremusic.net/.ai/PRD.md) | - |
| **Depo & Vault Envanteri** | [CONTEXT.md](file:///c:/www/coremusic.net/CONTEXT.md) | [.ai/CONTEXT.md](file:///c:/www/coremusic.net/.ai/CONTEXT.md) | - |
| **Karpathy Direktifleri & Cursor** | [CURSOR.md](file:///c:/www/coremusic.net/CURSOR.md) | [.cursor/rules/karpathy-guidelines.mdc](file:///c:/www/coremusic.net/.cursor/rules/karpathy-guidelines.mdc) | `.claude/skills/karpathy-guidelines/` |

---

## 🔗 SSOT Bağlantıları

1. **[RAG Retrieval Index & Pipeline (.ai/RAG.md)](file:///c:/www/coremusic.net/.ai/RAG.md)** — Tam metin SSOT (§3 konu→dosya indeksi · §4 pipeline)
2. **[Kullanıcı RAG Kılavuzu (RAG-KILAVUZU.md)](file:///c:/www/coremusic.net/RAG-KILAVUZU.md)** — Adım adım RAG kullanım ve prompt dokümanı (DOGE DOC)
3. **[Kalıcı Oturum Durum & Devir Hafızası (durum.md)](file:///c:/www/coremusic.net/.ai/durum.md)** — Tek sayfalık oturum devir hafızası
4. **[Temel Mühendislik Direktifleri (kurallar.md)](file:///c:/www/coremusic.net/.ai/.rules/kurallar.md)** — Standart `KURAL-01` .. `KURAL-10`
5. **[Hata Önleme Kataloğu (hata-listesi.md)](file:///c:/www/coremusic.net/.ai/.rules/hata-listesi.md)** — Pre-flight denetim `HL-01` .. `HL-12`
6. **[Keyword Haritası (keys.md)](file:///c:/www/coremusic.net/.ai/keys.md)** — Keyword → dosya eşlemesi
7. **[Master Katalog (index.md)](file:///c:/www/coremusic.net/.ai/index.md)** — 24 ana vault belgesinin listesi
8. **[Vault Context (CONTEXT.md)](file:///c:/www/coremusic.net/.ai/CONTEXT.md)** — Dizin ve dosya envanteri
10. **[Cursor IDE & Karpathy Kuralları](file:///c:/www/coremusic.net/CURSOR.md)** — [`CURSOR.md`](file:///c:/www/coremusic.net/CURSOR.md) (Cursor kuralları ve Karpathy direktifleri)
9. **[Kök Boot Dosyaları](file:///c:/www/coremusic.net/AGENTS.md)** — [`AGENTS.md`](file:///c:/www/coremusic.net/AGENTS.md) (OpenCode V2) & [`CLAUDE.md`](file:///c:/www/coremusic.net/CLAUDE.md) (Claude Code)

---

## ⚠️ Kritik RAG İlkeleri

- **Tek Sayfa İlkesi (Token Tasarrufu):** Oturumlarda tüm vault taranmaz; yalnızca çalışılan modülün `durum.md` dosyası ve ilgili kaynak dosya RAG bağlamına alınır.
- **Tema Zorunluluğu (YALNIZCA LIGHT MODE):** CoreMusic'te ŞU ANDA YALNIZCA LIGHT MODE MEVCUTTUR. Dark mode henüz yapılmamıştır/yoktur!
- **Amfi Topolojisi (CLASS AB):** Donanım ve ses motoru amfi mimarisi kesinlikle Class AB'dir (Class D yasaktır).
- **Subagent Commit Yasağı:** Subagent `git commit` atamaz; commit yetkisi yalnızca kullanıcıya / orkestratöre aittir.

---

*CoreMusic RAG Pointer & Router v2.0.0 — SSOT: [`.ai/RAG.md`](file:///c:/www/coremusic.net/.ai/RAG.md) — Last Updated: 2026-11-01*
