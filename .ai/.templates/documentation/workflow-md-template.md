---
title: "CoreMusic — WORKFLOW.md Üretim Şablonu"
type: template
category: documentation
version: 1.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-10-07
---

# CoreMusic — WORKFLOW.md Üretim Şablonu

**Zorunlu Bağlantılar:** [[.templates/index]] · [[./agents-md-template]] · [[./context-md-template]] · [[../../WORKFLOW.md]]

---

## §1 Amaç

Bu şablon, CoreMusic ekosisteminde bir **WORKFLOW.md (Workflow Pointer / süreç dosyası)** üretmek için dış iskeleti, zorunlu blokları ve doğrulama listesini tanımlar. WORKFLOW.md, faz kapılarını (Hard Gate), boot okuma sırasını ve skill noktalarını taşıyan süreç dosyasıdır. Yanlış yazılan bir WORKFLOW.md fazları atlatır veya yanlış sıraya sokar. Bu yüzden dosya `[[.templates/index]]` registry'sine kayıtlı bu şablondan üretilir (Guardrail #16).

| Alan | Değer |
|------|-------|
| Template Name | `workflow-md-template.md` |
| Template Path | `.ai/.templates/documentation/workflow-md-template.md` |
| Hedef Dosya Tipi | `WORKFLOW.md` (süreç / pointer) |
| Hedef Konum | Proje kökü veya `.ai/` (SSOT) |
| Guardrail | #16 (Template Mandatory) — şablonsuz WORKFLOW.md üretilemez |
| Birincil Yazar | Master Orchestrator (workflow) |
| İskelet | H1 + §1 Amaç → §7 Referanslar (7 bölüm + frontmatter) |
| Zorunlu Blok | §3.1 WARNING (AI dikkat) blockquote + §3.2 SSOT link listesi + §3.3 Boot sırası |
| Kayıt Kuralı | Değişiklik Geçmişi append-only — mevcut satıra dokunulmaz |
| Kanıt — Vault | `.ai/WORKFLOW.md` (SSOT), kök `WORKFLOW.md` (pointer), `.ai/.templates/index.md` |
| Kanıt — Kural | Guardrail #16, ADR-042 (pointer = SSOT yönlendirmesi) |
| Korunan Bilgi | 7 alanlı frontmatter, WARNING blockquote tonu, wiki-link formatı |
| Değişken Formatı | `{{VARIABLE}}` (PROJECT_NAME, DATE, VERSION, SSOT_PATH, GATE_LIST, BOOT_STEPS) |
| Dil | Türkçe (mojibake YASAK); dosya adı `WORKFLOW.md` (İngilizce) |
| Authority | Template (Guardrail #16) — Registry: `.ai/.templates/index.md` |
| Governance | Red Team · Human Mode · Truth Mode |

### §1.1 Bu Şablon Ne İçin — Ne İçin Değil

| İhtiyaç | Doğru Şablon | Bu Şablon Kullanılmaz |
|---------|--------------|------------------------|
| Kök/vault WORKFLOW.md (süreç + pointer) | ✅ Bu şablon | — |
| Agent kuralları (AGENTS.md) | `[[./agents-md-template]]` | ❌ |
| AI anayasası (CLAUDE.md) | `[[./claude-md-template]]` | ❌ |
| Envanter (CONTEXT.md) | `[[./context-md-template]]` | ❌ |
| Vault wiki sayfası | `[[../documentation/WikiPage-Template]]` | ❌ |
| GitHub Actions CI dosyası | `[[../infrastructure/github-actions-template]]` | ❌ |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `WORKFLOW.md` dış iskeleti (frontmatter + H1 + §1-§7) | Faz içeriği/timeline → `.ai/WORKFLOW.md` (SSOT) |
| WARNING blockquote (AI'ı SSOT'a yönlendirme) | Hard Gate tanım metinleri (SSOT'ta) |
| SSOT link listesi formatı | Skill Registry (kök `CLAUDE.md` SSOT) |
| Boot okuma sırası bloğu | Script/otomasyon kodu |

- **Ön koşul:** SSOT hedefi (`{{SSOT_PATH}}`) diskte MEVCUT.
- **Çift katman kuralı:** Kök = pointer (kısa, "bu dosyayı talimat için kullanmayın" uyarılı), `.ai/` = SSOT (tam süreç).

---

## §3 Mimari — Üretilen WORKFLOW.md İskeleti

### §3.1 Dış İskelet + WARNING Blockquote (ZORUNLU — silinemez)

````markdown
---
title: "CoreMusic — Workflow Pointer"
type: workflow-pointer
category: workflow
version: {{VERSION}}
status: active
authority: "SSOT: {{SSOT_PATH}}"
updated: {{DATE}}
---

# ⚠️ CoreMusic Workflow Configuration (Vault SSOT Pointer)

> [!WARNING]
> **AI AGENT DİKKATİNE / ATTENTION AI AGENTS:**
>
> Bu dosya ADR-042 (Vault Restructuring) uyarınca yalnızca bir **YÖNLENDİRİCİ (POINTER)**'dır.
> Tüm iş akışları, faz onay mekanizmaları ve otomasyon senaryoları `.ai/` vault'undadır.
>
> **BU DOSYAYI TALİMAT OKUMAK İÇİN KULLANMAYIN — SSOT BAĞLANTILARINA GEÇİN.**
````

### §3.2 Zorunlu Bölüm Blokları

| Bölüm | İçerik | Zorunlu mu |
|-------|--------|-----------|
| Proje/Vizyon Özeti | 2 madde (proje + felsefe) | ✅ |
| SSOT Bağlantıları | Numaralı liste: WORKFLOW → VISION → PROJECTS → engine → CLAUDE → AGENTS → CONTEXT → (varsa RAG) | ✅ |
| Boot Okuma Sırası | `1) … → 6) COMMIT` fenced-code zinciri | ✅ |
| Skill Noktaları | Süreç Adımı → Zorunlu Skill tablosu (SSOT: kök `CLAUDE.md` §Skill Registry — kopya değil, işaretçi) | ✅ |
| §14 Değişiklik Geçmişi | Tarih · Sürüm · Değişiklik · Kaynak (append-only) | ✅ |
| Bağlayıcı özet (§1-§13) | Varsa; "tek otorite .ai/" notu ile | koşullu |

### §3.3 Boot Sırası Kalıbı

```text
1) root CLAUDE.md + AGENTS.md + README.md + WORKFLOW.md OKU
1.5) SKILL EŞLEŞMESİ — Skill Registry → eşleşme varsa Skill tool ile YÜKLE
2) .ai/.rules/** OKU (varsa)
3) ilgili .ai/** & architecture/*** OKU (ihtiyaç anında @ ile)
4) mevcut dosyayı OKU
5) kodu YAZ
6) .ai/.rules/** ÇALIŞTIR → hata: DUR/düzelt · temiz: UI test varsa browser test → COMMIT
```

---

## §4 Kurallar

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Frontmatter 7 zorunlu alan | Doküman reddedilir |
| 2 | WARNING blockquote silinemez/tonu yumuşatılamaz | Pointer amacını kaybeder — revert |
| 3 | `authority` SSOT yolunu gösterir | Kararsız otorite — revert |
| 4 | SSOT link listesi madde madde, her madde gerçek dosya yolu | Kırık link |
| 5 | Skill Registry tablosu KOPYALANMAZ (kök `CLAUDE.md` SSOT) | SSOT ihlali |
| 6 | Diskte olmayan hedefe "VERIFICATION REQUIRED" yazılır — link verilmez | Zero-Hallucination ihlali |
| 7 | Placeholder `{{VARIABLE}}` kalamaz | Doğrulama ihlali |
| 8 | Değişiklik Geçmişi append-only | Kayıp geçmişi |
| 9 | Wiki-link `[[...]]` formatı | Link check kırmızı |

---

## §5 Workflow

```
ŞABLONU SEÇ → İSKELETİ KOPYALA (§3.1 WARNING dahil)
→ FRONTMATTER DOLDUR (7 alan) → SSOT LİNK LİSTESİ (gerçek dosyalar)
→ BOOT SIRASI (§3.3) → SKILL NOKTALARI (SSOT pointer'ı)
→ DEĞİŞİKLİK GEÇMİŞİ append → GUARDRAIL #16 DOĞRULA
→ INDEX'E 1 SATIR EKLE → COMMIT (subagent atmaz)
```

---

## §6 Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan |
| 2 | WARNING blockquote | Var ve silinmemiş |
| 3 | SSOT linkleri | Her hedef diskte mevcut (Test-Path) |
| 4 | Boot sırası | Fenced-code zinciri eksiksiz |
| 5 | Skill tablosu | Yalnız pointer (ad + SSOT referansı), kopya registry yok |
| 6 | Placeholder | `{{` kalmadı |
| 7 | Geçmiş | Append-only bölüm var |
| 8 | Halüsinasyon | Diskte olmayan dosya iddia edilmedi |
| 9 | Uzunluk | Pointer ≤250 satır; SSOT ayrı dosyada |
| 10 | Registry | `.templates/index.md`'e 1 satır eklendi |

---

## §7 Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Template registry | [[.templates/index]] | Envanter + kayıt satırı |
| AGENTS.md şablonu | [[./agents-md-template]] | Agent kuralları iskeleti |
| Context şablonu | [[./context-md-template]] | Envanter iskeleti |
| Vault süreç SSOT'u | [[../../WORKFLOW.md]] | İçerik kaynağı |
| Vault anayasası | [[../CLAUDE.md]] | Guardrail + Skill Registry SSOT |
| Pointer kararı | ADR-042 (`[[../.decisions/accepted/ADR-042-vault-restructuring-2026-08-03]]`) | Kök = pointer prensibi |

---

**Template Version:** 1.0.0
**Last Updated:** 2026-10-07
