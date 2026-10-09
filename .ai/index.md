---
title: "CoreMusic Vault - Master Index"
type: system
category: vault-navigation
status: active
authority: SSOT
version: 28.4.4
updated: 2026-10-06
total_files: 720
total_adr: 80
total_adr_disk: 60
# Control Plane v2 (Q7 geniş şema — 2026-10-07):
tier: 5
domain: navigation
ssot: false
risk: low
owner: "MO"
depends-on: []
---

# CoreMusic Vault — Master Index

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]] · [[.templates/index]] · [[.agents/AGENTS.md]]

**Skills:** `.opencode/skills/` (8 aktif skill — Guardrail #16 zorunlu)

---

## Purpose

### §1 Amaç

Bu dosya, CoreMusic `.ai/` vault'unun ana navigasyon noktasıdır. Tüm vault dosyaları kategorize edilmiş ve hızlı erişilebilir biçimde listelenir.

---

## Scope

### §2 Quick Reference

| İhtiyaç | İlk Adım |
|---------|----------|
| Vault genel bakış | Bu dosya (index.md) |
| Ürün & Ekosistem Vizyonu | [[VISION.md]] |
| Proje Tanımı & Hangi Sorunları Çözer | [[PROJECTS.md]] |
| Teknik Dokümantasyon & Kılavuz | [[TECHNICAL_DOCUMENTATION.md]] |
| Keyword arama | [[keys.md]] |
| Konu→dosya retrieval indeksi (hangi soru hangi dosya) | [[RAG.md]] |
| Terimler Sözlüğü | [[glossary.md]] |
| Mimari kararlar | [[brain.md]] |
| Ajan yetkileri | [[AGENTS.md]] |
| Süreçler | [[WORKFLOW.md]] |
| Bellek yönetimi | [[MEMORY.md]] |
| Aktivite günlüğü | [[log.md]] |
| ADR kataloğu | § 5 bu dosya |
| Servis haritası | § 6 bu dosya |
| Veritabanı | § 8 bu dosya |
| UI / Mockup / Frontend | [[ui-design/01-mockup-index]] (19 PNG Mockup, C01-C16 Envanteri, 45-Tier Device Matrix) |
| Session Checklist | [[CHECKLIST.md]] (Baş/Orta/Kapanış - §A/§B/§C, 5'er madde) |
| Açık İş Listesi | [[TODO.md]] (P0/P1/P2 - 19 madde, kaynak notlu) |
| Context Planner spec | [[PLANNER.md]] (Control Plane — otomatik context yükleme sözleşmesi, Q4-B; 13 alan FM girdisi) |
| Link/ADR denetimi | [[broken-links-report.md]] (wiki-link tarama raporu — 227 link, gerçek kırık 0; 6'sı AGENTS §13.7 sahte) |
| Sürekli güçlendirme seti | [[CHECKLIST.md]] §A0 — 20 hedef dosya (3 kök + 17 `.ai/` kök md): CRITICAL 16 / ON-DEMAND 3 / LOG 1 |

---

### §3 SSOT Core Dosyaları (14 Kök Boot + 3 Ek Doküman)

| # | Dosya | Amaç |
|---|-------|------|
| 1 | [[CLAUDE.md]] | Kanonik AI talimatı — boot protokolü, guardrails |
| 2 | [[AGENTS.md]] | Agent kayıt defteri — 11 uzmanlık alanı + MO, koordinasyon |
| 3 | [[WORKFLOW.md]] | Süreçler — vault refactoring, ürün döngüsü |
| 4 | [[index.md]] | Bu dosya — tüm vault dizin yapısı |
| 5 | [[keys.md]] | Anahtar kelime haritası — keyword → dosya yönlendirme |
| 6 | [[brain.md]] | Mimari kararlar — ADR 001-089 (80 karar), L0-L6, engineering brain |
| 7 | [[MEMORY.md]] | Oturum hafızası — persistent state, cache, session lifecycle |
| 8 | [[log.md]] | Aktivite günlüğü — append-only audit trail |
| 9 | [[engine.md]] | Orkestrasyon motoru — agent koordinasyonu, task dispatch |
| 10 | [[VISION.md]] | Ürün ve ekosistem vizyonu — mülkiyet felsefesi, 6 sorun-çözüm, handoff, hibrit omurga |
| 11 | [[PROJECTS.md]] | CoreMusic nedir, 10 temel yetenek, 6 hedef kullanıcı, 9 kullanım senaryosu, sektörel çözümler |
| 12 | [[glossary.md]] | Terimler sözlüğü — Neva Engine, Bit-Perfect, Ambient Aura, Deemix vb. |
| 13 | [[ROLE.md]] | Rol ve sorumluluk tanımı — Senior Software Architect |
| 14 | [[ULTRA-THINKING.md]] | Ultra düşünme protokolü — karar öncesi zorunlu doğrulama |
| 15 | [[TECHNICAL_DOCUMENTATION.md]] | Teknik dokümantasyon, sistem kullanım kılavuzu ve freelancer geliştirici kuralları — ⚠️ VERIFICATION REQUIRED (boot 14 dışı; .ai/ kökünde diskte yok) |
| 16 | [[PLANNER.md]] | Context Planner Spec (Control Plane) — otomatik context yükleme sözleşmesi; FM 13 alan; `status: approved` (2026-10-07 interview Q4-B) |
| 17 | [[RAG.md]] | Retrieval indeksi + pipeline — konu→dosya eşlemesi (§3, 20 satır) ve embedding/arama tasarımı (§4, ÇOĞU PLANNED — ADR-030); boot 14 dışı, on-demand okunur (2026-10-07) |


------## Bölümler> Bu dosya çok-sayfalı yapıya ayrılmıştır (hub). İçerik aşağıdaki alt sayfalardadır; SSOT künyesi bu dosyadadır.| Sayfa | Bölüm ||-------|-------|| [[index/01-mimari]] | Mimari |
| [[index/02-kurallar]] | Kurallar |
| [[index/03-workflow]] | Workflow |
| [[index/04-dogrulama]] | Doğrulama |
| [[index/05-referanslar]] | Referanslar |
