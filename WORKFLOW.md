---
title: "CoreMusic — Workflow Pointer"
type: workflow-pointer
category: workflow
version: 3.3.0
status: active
authority: "SSOT: .ai/WORKFLOW.md — Full workflows read via `.ai/WORKFLOW.md`"
updated: 2026-11-01
---

# ⚠️ CoreMusic Workflow Configuration (Vault SSOT Pointer)

> [!WARNING]
> **AI AGENT DİKKATİNE / ATTENTION AI AGENTS:**
>
> Bu dosya, ADR-042 (Vault Restructuring) kararı uyarınca yalnızca bir **YÖNLENDİRİCİ (POINTER)** olarak bırakılmıştır.  
> CoreMusic projesine ait tüm iş akışları, agent otomasyon senaryoları, faz onay mekanizmaları ve vizyon dokümanları `.ai/` klasörü içerisindeki ana kasada (Vault) toplanmıştır.  
>
> **BU DOSYAYI TALİMAT OKUMAK İÇİN KULLANMAYIN. LÜTFEN AŞAĞIDAKİ BAĞLANTILARA TIKLAYARAK GERÇEK İŞ AKIŞI VE VİZYON DOSYALARINA GEÇİŞ YAPIN.**

## 🎯 Proje ve Vizyon Özeti

* **Proje:** CoreMusic — Ticari Dijital Medya Ekosistemi ve Gelir Platformu (Yazılım, Ses DSP, Donanım, AI).
* **Felsefe:** *"Aynı Müzik Her Yerde Seninle"* — Mülkiyet ve Özgürlük (Offline-First, bağımsız kayıpsız arşiv), Kesintisiz Handoff ve Hi-Fi C++20 Neva Engine.

## 🔗 Single Source of Truth (SSOT) Bağlantıları

İş akışlarını okumak, script'leri çalıştırmak, vizyonu anlamak ve mevcut faz (Phase) durumunu yönetmek için derhal aşağıdaki dizinlere gidin:

1. **[Ana İş Akışları (WORKFLOW.md)](.ai/WORKFLOW.md)** — tam metin `.ai/WORKFLOW.md` 👈 *(Öncelikli iş akışı kuralları)*
2. **[Kalıcı Oturum Durum & Devir Hafızası (durum.md)](.ai/durum.md)** — tek sayfalık oturum durum devri ve kalıcı hafıza
3. **[Kullanıcı Kuralları (kurallar.md)](.ai/.rules/kurallar.md)** — kullanıcı kimliği (Bayram Ali), hybrid dil, Light Mode ve amfi kuralları
4. **[Bütün Proje Kuralları (senior-mode.md)](.ai/.rules/senior-mode.md)** — kod öncesi 16 analiz adımı ve senior mühendislik standartları
5. **[Figma API Erişim Tokeni (.env.figma)](.ai/.env.figma)** — `FIGMA_TOKEN` / `FIGMA_FILE_KEY` SSOT (.gitignore:69)
6. **[Vizyon ve Felsefe (VISION.md)](.ai/VISION.md)** — tam metin `.ai/VISION.md` 👈 *(Pazar krizi, mülkiyet felsefesi ve çözümler)*
7. **[Proje Tanımı (PROJECTS.md)](.ai/PROJECTS.md)** — tam metin `.ai/PROJECTS.md` 👈 *(10 temel yetenek, hedef kitleler, sektörel çözümler)*
8. **[Faz Yürütme ve Matris (engine.md)](.ai/engine.md)** — tam metin `.ai/engine.md` *(Orkestrasyon ve yürütme matrisi)*
9. **[AI Anayasası (CLAUDE.md)](.ai/CLAUDE.md)** *(16 Hard Guardrail ve mühendislik anayasası)*
10. **[Agent Registry (AGENTS.md)](.ai/AGENTS.md)** *(11 agent, routing, handover, escalation)*
11. **[Vault Klasör Context (CONTEXT.md)](.ai/CONTEXT.md)** *(Vault envanteri, dosya yapısı)*
12. **[RAG Retrieval Index & Pipeline (RAG.md)](.ai/RAG.md)** — konu→dosya indeksi (§3) + retrieval rehberi
13. **[Master Katalog (index.md)](.ai/index.md)** — 24 ana vault belgesinin kataloğu

---

## Boot Okuma Sırası (Root → .ai/ Vault)

```text
1) root CLAUDE.md + AGENTS.md + README.md + WORKFLOW.md OKU (bu dosyalar)
2) .ai/durum.md OKU (oturum başı durum devri ve kalıcı hafıza kontrolü)
3) .ai/.rules/kurallar.md ve .ai/.rules/senior-mode.md OKU (kullanıcı kuralları + 16 adım analiz)
4) SKILL EŞLEŞMESİ — CLAUDE.md §Skill Registry → eşleşme varsa Skill tool ile YÜKLE
5) ilgili .ai/** OKU (ihtiyaç anında @ ile)
6) mevcut dosyayı OKU
7) kodu YAZ
8) .ai/CLAUDE.md Guardrails ve LINT kontrollerini ÇALIŞTIR
9) UI değişikliği var mı?
   · EVET  → browser test (Light Mode) → COMMIT (orkestratörde)
   · HAYIR → COMMIT (orkestratörde)
10) .ai/durum.md + .ai/log.md + .ai/MEMORY.md GÜNCELLE ve Session-End Check & Clean çalıştır
```

## ⚠️ Kritik Proje Kısıtları (Hard Constraints)

1. **Tema Zorunluluğu (YALNIZCA LIGHT MODE):** CoreMusic'te ŞU ANDA YALNIZCA LIGHT MODE MEVCUTTUR. Dark mode henüz yapılmamıştır/yoktur! Tüm UI tasarımları istisnasız Light Mode'a göre tasarlanır; dark mode varsayımı veya dark mode kodu yazmak KESİNLİKLE YASAKTIR.
2. **Amplifikatör Topolojisi:** Donanım amfi mimarisi kesinlikle Class AB'dir (Class D yasaktır).
3. **Kalıcı Hafıza & Devir Protokolü:** AI her oturumda `.ai/durum.md` okur, değişiklikleri `.ai/durum.md`, `.ai/log.md` ve `.ai/MEMORY.md` dosyalarına işler.
4. **Subagent Commit Yasağı:** Subagent `git commit` ATAMAZ; commit yetkisi orkestratöre/kullanıcıya aittir.

---

## Skill Noktaları (Süreç Adımı → Zorunlu Skill)

Skill Registry SSOT: kök [`CLAUDE.md`](CLAUDE.md) §Skill Registry (**14 proje skill**, `.claude/skills/`). Her noktada eşleşme varsa **ilk işlem** Skill tool ile o skill'i yüklemektir (`AGENTS.md` §7 adım 0).

| Süreç Adımı | Zorunlu Skill |
|---|---|
| Ham prompt gelir | `prompt-maker` |
| Tasarım incelemesi / UI denetimi | `ui-analyzer` |
| UI kodu | `ui-code-generator` |
| Şema / BCNF | `database-normalize-maker` |
| PHP uç nokta / middleware | `php-backend-standards` |
| C++ audio | `audio-engine-cpp` |
| Donanım | `hardware-electronics` |
| Güvenlik değişikliği | `security-hardening` |
| İşlem kapanışı & vault senkronu | `vault-sync-post` |
| Multi-agent görev | `agent-orchestrator` |
| Composer / vendor | `composer-sync` |
| Yeni skill üretimi / güncelleme | `skill-maker` |
| Konu öğrenme / Feynman tekniği | `learning-prompts` |

---

## §14 Değişiklik Geçmişi

| Tarih | Sürüm | Değişiklik | Kaynak |
|---|---|---|---|
| 2026-11-01 | 3.3.0 | .ai/.rules/senior-mode.md ve .ai/.env.figma bağlantıları eklendi; boot sırası ve OpenCode/Claude Code ayrımı güncellendi | Session Alignment |
| 2026-10-10 | 3.2.0 | Mimari indeks → `index.md`; Skill Registry disk hizası 14 proje skill | Disk ölçüm 2026-10-10 |
| 2026-10-07 | 3.0.0 | Skill mandate + Skill Noktaları eklendi | Claude Skill v3.0 |
| 2026-09-24 | 2.0.0 | §1-§14 bağlayıcı özet eklendi; pointer uyarısı + SSOT linkleri korundu | Vault iş akışı genişletme |
| 2026-08-09 | 1.0.0 | Pointer dosyası (ADR-042) — SSOT bağlantıları | ADR-042 |

---

*CoreMusic Workflow Pointer v3.3.0 — SSOT: `.ai/` — Last Updated: 2026-11-01*