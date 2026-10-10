---
title: "CoreMusic — Workflow Pointer"
type: workflow-pointer
category: workflow
version: 3.2.0
status: active
authority: "SSOT: .ai/WORKFLOW.md — Full workflows read via `.ai/WORKFLOW.md`"
updated: 2026-10-10
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
2. **[Vizyon ve Felsefe (VISION.md)](.ai/VISION.md)** — tam metin `.ai/VISION.md` 👈 *(Pazar krizi, mülkiyet felsefesi ve çözümler)*
3. **[Proje Tanımı (PROJECTS.md)](.ai/PROJECTS.md)** — tam metin `.ai/PROJECTS.md` 👈 *(10 temel yetenek, hedef kitleler, sektörel çözümler)*
4. **[Faz Yürütme ve Matris (engine.md)](.ai/engine.md)** — tam metin `.ai/engine.md` *(Orkestrasyon ve yürütme matrisi)*
5. **[AI Anayasası (CLAUDE.md)](.ai/CLAUDE.md)** *(16 Hard Guardrail ve mühendislik anayasası)*
6. **[Agent Registry (AGENTS.md)](.ai/AGENTS.md)** *(11 agent, routing, handover, escalation)*
7. **[Vault Klasör Context (CONTEXT.md)](.ai/CONTEXT.md)** *(Vault envanteri, dosya yapısı)*
8. **[Katman Master İndeks (00-master-index.md)](.ai/architecture/00-master-index.md)** — v2.0.0 · 21 katman K000-K020 (12 IMPLEMENTED · 9 PLANNED) · bağılılık: `.ai/architecture/katman-baglilik-matrisi.md` · plan: `.ai/architecture/coremusic-mimari-plani.md` · ⚠️ eski `.ai/architecture/index.md` **diskte YOK** (2026-10-10)
9. **[RAG Retrieval Index & Pipeline (RAG.md)](.ai/RAG.md)** — konu→dosya indeksi (§3, 20 satır) + pipeline tasarımı (§4, ÇOĞU PLANNED — ADR-030) 👈 *(AI'ın ilk okuma hedefini seçmesi için)*
10. **[Vault Context (CONTEXT.md)](.ai/CONTEXT.md)** — dizin/dosya envanteri, boot ilişkisi

> [!NOTE]
> **Genişletme notu (2026-09-24):** Aşağıdaki §1-§14 bölümleri bu dosyanın **bağlayıcı özeti (orientation summary)**'dir; tek otorite `.ai/` vault'unun ilgili dosyalarıdır. Çelişki durumunda SSOT kazanır: adlandırma → [[.ai/architecture/adlandirma-kurali]] · bağımlılık → [[.ai/architecture/katman-baglilik-matrisi]] · sayım → [[.ai/architecture/katman-sayim-rehberi]] · süreç → [[.ai/WORKFLOW.md]] · anayasa → [[.ai/CLAUDE.md]] · agent → [[.ai/AGENTS.md]].

---

## Boot Okuma Sırası (Root → .ai/ Vault)

```text
1) root CLAUDE.md + AGENTS.md + README.md + WORKFLOW.md OKU (bu dosyalar)
1.5) SKILL EŞLEŞMESİ — CLAUDE.md §Skill Registry → eşleşme varsa Skill tool ile YÜKLE
2) .ai/.rules/** OKU (varsa — error-recovery.md diskte YOK)
3) ilgili .ai/** & architecture/*** OKU (ihtiyaç anında @ ile)
4) mevcut dosyayı OKU
5) kodu YAZ
6) .ai/.rules/** ÇALIŞTIR
   · HATA   → error-recovery.md (diskte YOK — DUR) → dosyayı SİL + Yeniden YAZ → 6'ya dön
   · TEMİZ  → UI değişikliği var mı?
        · EVET  → browser test → COMMIT
        · HAYIR → COMMIT
```

Ayrıntı (disk notları dahil): [[.ai/WORKFLOW.md]] §8.10 · bağlayıcı özet: bu dosya §16.

⚠️ **VERIFICATION REQUIRED**: `.ai/.rules/error-recovery.md` diskte YOK — adımlar 7-8 için **⚠️ VERIFICATION REQUIRED** (kural dosyası eklenmeden bu adım çalıştırılamaz).

---

## Skill Noktaları (Süreç Adımı → Zorunlu Skill)

Skill Registry SSOT: kök `CLAUDE.md` §Skill Registry (**13 proje skill**, `.claude/skills/` — 2026-10-10 disk ölçümü; `C:\.claude\skills` ve `.opencode/skills` diskte YOK). Her noktada eşleşme varsa **ilk işlem** Skill tool ile o skill'i yüklemektir (Skill Usage Mandate §2); istisnalar mandate §4'tedir.

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
| Web kodu tamamlanma | `verify-loop` ⚠️ (skill dosyası diskte YOK — yalnız harness listesinde) |
| İşlem kapanışı | `vault-sync-post` |
| Çakışan karar | `agent-debate` ⚠️ (skill dosyası diskte YOK) |
| Multi-agent görev | `agent-orchestrator` |
| Composer / vendor | `composer-sync` |
| Onay / iletişim | `human-mode` ⚠️ (skill dosyası diskte YOK) |
| İddia doğruluğu | `truth-engine` ⚠️ (skill dosyası diskte YOK) |
| Durum raporu | `context-report` ⚠️ (skill dosyası diskte YOK) |
| Yeni skill | `skill-maker` |
| Konu öğrenme / sınav | `learning-prompts` (Registry'ye 2026-10-10 eklendi) |

---

## §14 Değişiklik Geçmişi

| Tarih | Sürüm | Değişiklik | Kaynak |
|-------|-------|------------|--------|
| 2026-10-10 | 3.2.0 | Mimari indeks → `00-master-index.md` (21 katman K000-K020); Skill Registry disk hizası 13 proje skill; 5 global skill `C:\.claude\skills` diskte YOK işaretlendi; `learning-prompts` eklendi | Disk ölçüm 2026-10-10 |
| 2026-10-07 | 3.0.0 | Skill mandate + Skill Noktaları eklendi | Claude Skill v3.0 |
| 2026-10-07 | 2.0.2 | Pointer dosyası — `.ai/WORKFLOW.md` bağlantısı düzeltildi, `.ai/` bağlantıları güncellendi | Vault Refactor Engine |
| 2026-09-24 | 2.0.0 | §1-§14 bağlayıcı özet eklendi (K0-K20, A0-A5, adlandırma, sayım, tartışma, iki ADR serisi, yazım+senkron); pointer uyarısı + SSOT linkleri korundu | Vault iş akışı genişletme görevi |
| 2026-08-09 | 1.0.0 | Pointer dosyası (ADR-042) — SSOT bağlantıları | ADR-042 |

---

*CoreMusic Workflow Pointer v3.2.0 — Authority: Bayram Ali / Vault Steward — Last Updated: 2026-10-10*
*Mode: Red Team · Human Mode · Truth Mode*