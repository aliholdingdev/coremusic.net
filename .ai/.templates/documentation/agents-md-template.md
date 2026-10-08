---
title: "CoreMusic — AGENTS.md Üretim Şablonu"
type: template
category: documentation
version: 1.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-10-07
---

# CoreMusic — AGENTS.md Üretim Şablonu

**Zorunlu Bağlantılar:** [[.templates/index]] · [[./claude-md-template]] · [[./workflow-md-template]] · [[../../AGENTS.md]]

---

## §1 Amaç

Bu şablon, CoreMusic ekosisteminde bir **AGENTS.md (Master Agent Rules / pointer) dosyası** üretmek için dış iskeleti, zorunlu blokları ve doğrulama listesini tanımlar. AGENTS.md, agent lifecycle'ını (akış, zero-hallucination, anti-overthink, execution loop) taşıyan boot dosyasıdır; bir dizini yanlış yönlendirirse tüm görevler yanlış akar. Bu yüzden dosya `[[.templates/index]]` registry'sine kayıtlı bu şablondan üretilir (Guardrail #16).

| Alan | Değer |
|------|-------|
| Template Name | `agents-md-template.md` |
| Template Path | `.ai/.templates/documentation/agents-md-template.md` |
| Hedef Dosya Tipi | `AGENTS.md` (master agent rules / pointer) |
| Hedef Konum | Proje kökü veya `.ai/` (SSOT) |
| Guardrail | #16 (Template Mandatory) — şablonsuz AGENTS.md üretilemez |
| Birincil Yazar | Master Orchestrator (vault-updater) |
| Routing | `[[../../AGENTS.md]]`: dokümantasyon → MO |
| İskelet | H1 + §1 Amaç → §7 Referanslar (7 bölüm + frontmatter) |
| Zorunlu Blok | §3.1 Boot/okuma-sırası bloğu + §3.2 Execution Loop |
| Kayıt Kuralı | Değişiklik Geçmişi append-only — mevcut satıra dokunulmaz |
| Kanıt — Vault | `.ai/AGENTS.md` (SSOT), kök `AGENTS.md` (pointer), `.ai/.templates/index.md` (registry) |
| Kanıt — Kural | Guardrail #16 (`.ai/CLAUDE.md` §7), ADR-042 (vault SSOT + pointer) |
| Korunan Bilgi | 7 alanlı frontmatter, wiki-link formatı, version/updated tutarlılığı |
| Değişken Formatı | `{{VARIABLE}}` (PROJECT_NAME, DATE, VERSION, SSOT_PATH, FLOW_STEPS, ON_DEMAND_ROWS) |
| Dil | Türkçe (ç ğ ı İ ö ş ü doğru; mojibake YASAK); dosya adı `AGENTS.md` (İngilizce) |
| Authority | Template (Guardrail #16) — Registry: `.ai/.templates/index.md` |
| Governance | Red Team · Human Mode · Truth Mode |

### §1.1 Bu Şablon Ne İçin — Ne İçin Değil

| İhtiyaç | Doğru Şablon | Bu Şablon Kullanılmaz |
|---------|--------------|------------------------|
| Kök/vault AGENTS.md (agent kuralları + pointer) | ✅ Bu şablon | — |
| AI yönerge/anayasa dosyası (CLAUDE.md) | `[[./claude-md-template]]` | ❌ |
| Süreç/faz dosyası (WORKFLOW.md) | `[[./workflow-md-template]]` | ❌ |
| Envanter/bağlam dosyası (CONTEXT.md) | `[[./context-md-template]]` | ❌ |
| Agent **profil** dosyası (`.ai/.agents/*.md`) | `[[../agents/agents-template]]` | ❌ — profil ≠ kurallar |
| Genel dokümantasyon .md | `[[./docs-md-template]]` | ❌ |

**Kural:** Dosya adı `AGENTS.md` ise bu şablon; agent profili üretilecekse `[[../agents/agents-template]]` geçerlidir.

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `AGENTS.md` dış iskeleti (frontmatter + H1 + §1-§7) | Agent yetki/handover içeriği → `.ai/AGENTS.md` (SSOT) |
| Boot/execution-loop bloğu iskeleti | ADR metinleri (yalnız wiki-link) |
| On-demand vault referans tablosu formatı | Persona/rol profilleri → `agents/agents-template` |
| Version/updated bump kuralı | Envanter sayıları (disk ölçümü, uydurulmaz) |

- **Ön koşul:** SSOT hedefi (`{{SSOT_PATH}}`) diskte MEVCUT; yoksa dosya üretilmez, `⚠️ VERIFICATION REQUIRED` yazılır.
- **Çift katman kuralı:** Kök `AGENTS.md` = pointer (kısa), `.ai/AGENTS.md` = SSOT (tam). İkisinde de `authority:` frontmatter alanı SSOT yolunu gösterir.

---

## §3 Mimari — Üretilen AGENTS.md İskeleti

### §3.1 Dış İskelet (Frontmatter + H1 + Bağlantılar)

````markdown
---
title: "CoreMusic — Master Agent Rules"
type: rules
category: agent-registry
version: {{VERSION}}
status: active
authority: "SSOT: {{SSOT_PATH}}"
updated: {{DATE}}
---

# CoreMusic — AGENTS.md (Master Agent Rules)

> **OpenCode V2 bu dosyayı kök boot dosyası olarak okur.**
> Uzun referanslar `.ai/` vault'tadır ve yalnız ihtiyaç anında `@` ile okunur.
> "Başta tüm vault'u oku" YASAKTIR.

> **Kural**: Her görevde önce **@{{SSOT_PATH}}** okunur; ardından görevin
> gerektirdiği dosyalar okunur.

Skill Registry SSOT: kök `CLAUDE.md` §Skill Registry.
````

### §3.2 Zorunlu Bölüm Blokları

| Bölüm | İçerik | Zorunlu mu |
|-------|--------|-----------|
| §1 İş Akışı (Flow) | `USER → … → IMPLEMENTATION` ASCII zincir + "her kapısı onaysız geçmez" | ✅ |
| §2 Otonom Yaşam Döngüsü | `Understand → … → Document` zinciri (adım sayısı doğrulanır — uydurulmaz) | ✅ |
| §3 Zero-Hallucination | 4 madde: uydurma yasağı · UNKNOWN/VERIFICATION · disk kanıtı · web < vault | ✅ |
| §4 Kod Öncesi Kural | Keşif listesi + "onaysız YOK" satırları | ✅ |
| §5 Anti-Overthink | MAX THINKING maddeleri ( kök `CLAUDE.md` §MAX THINKING'e referans) | ✅ |
| §7 Execution Loop | 0. SKILL EŞLEŞMESİ → 9. Commit numaralı adım listesi | ✅ |
| §9 On-Demand Vault Referansları | İhtiyaç → `@dosya` tablosu (yalnız ihtiyaç anında) | ✅ |
| §14 Özet (1 Satır) | "Bu dosya kök pointer → SSOT'a yönlendirir" | ✅ |
| §16 Değiştirilemez Kullanıcı Kuralları | Kullanıcı bağlayıcıları (varsa; append-only) | koşullu |

### §3.3 Pointer ↔ SSOT Farkı

| Katman | Dosya | Ağırlık |
|--------|-------|---------|
| Pointer | kök `AGENTS.md` (100-250 satır) | Boot akışı + en kritik 5-7 blok + SSOT linki |
| SSOT | `.ai/AGENTS.md` (tam metin) | Tüm agent registry, persona hiyerarşisi, handover, escalation |

---

## §4 Kurallar

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Frontmatter 7 zorunlu alan (`title, type, category, version, status, authority, updated`) | Doküman reddedilir |
| 2 | `authority` alanı SSOT yolunu (`{{SSOT_PATH}}`) göstermez | Pointer/karar belirsizliği — revert |
| 3 | Disk kanıtlı olmayan sayı/sürüm/versiyon adı yazılmaz — `UNKNOWN` | `⚠️ VERIFICATION REQUIRED` |
| 4 | Frozen ADR metni kopyalanmaz, wiki-link verilir | revert |
| 5 | Wiki-link formatı `[[...]]` — markdown path linki değil | Link check kırmızı |
| 6 | Placeholder `{{VARIABLE}}` üretilen dosyada kalamaz | Doğrulama #3 ihlali |
| 7 | Yaşam döngüsü/zincir adım sayısı uydurulmaz (elle sayılır) | Zero-Hallucination ihlali |
| 8 | Skill Registry tablosu bu dosyaya KOPYALANMAZ — kök `CLAUDE.md` SSOT'tur | SSOT ihlali |
| 9 | Boot listesi sayıları (13 kanonik vb.) değiştirilmez | Sayım zinciri bozulur |

---

## §5 Workflow

```
ŞABLONU SEÇ → docType=BELİRLE (AGENTS) → İSKELETİ KOPYALA (§3.1)
→ FRONTMATTER DOLDUR (7 alan) → §1-§7 İÇERİĞİ DOLDUR
→ SSOT ADRESİNİ DOĞRULA (diskte var mı) → GUARDRAIL #16 DOĞRULA
→ INDEX'E 1 SATIR EKLE → COMMIT (subagent atmaz)
```

---

## §6 Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan |
| 2 | Bölüm yapısı | §1-§7 aynı sıra, ≤3 başlık seviyesi |
| 3 | Placeholder | Dosyada `{{` kalmadı |
| 4 | Authority | `{{SSOT_PATH}}` diskte mevcut (Test-Path) |
| 5 | Guardrails | §4 9/9 |
| 6 | Wiki-link | `[[...]]` formatı; hedefler var |
| 7 | Sayı iddiaları | Ölçümle destekli veya `UNKNOWN` |
| 8 | Halüsinasyon | Diskte olmayan dosya/agent/skill iddia edilmedi |
| 9 | Çift katman | Pointer kısa (≤250 satır), SSOT tam metin |
| 10 | Registry | `.templates/index.md`'e 1 satır eklendi |

---

## §7 Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Template registry | [[.templates/index]] | Envanter + kayıt satırı |
| CLAUDE.md şablonu | [[./claude-md-template]] | Anayasa dosyası iskeleti |
| Workflow şablonu | [[./workflow-md-template]] | Süreç pointer iskeleti |
| Context şablonu | [[./context-md-template]] | Envanter iskeleti |
| Agent profil şablonu | [[../agents/agents-template]] | Profil ≠ kurallar ayrımı |
| Vault anayasası | [[../CLAUDE.md]] | Hard Guardrails, Skill Registry SSOT |
| SSOT agent registry | [[../../AGENTS.md]] | İçerik kaynağı |

---

**Template Version:** 1.0.0
**Last Updated:** 2026-10-07
