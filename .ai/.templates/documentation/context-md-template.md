---
title: "CoreMusic — CONTEXT.md Üretim Şablonu"
type: template
category: documentation
version: 1.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-10-07
---

# CoreMusic — CONTEXT.md Üretim Şablonu

**Zorunlu Bağlantılar:** [[.templates/index]] · [[../frontend/context-template]] · [[./claude-md-template]] · [[../../CONTEXT]]

---

## §1 Amaç

Bu şablon, CoreMusic ekosisteminde bir **CONTEXT.md (envanter / bağlam dosyası)** üretmek için dış iskeleti, zorunlu bölümleri ve doğrulama listesini tanımlar. CONTEXT.md, "bu klasör/vault ne işe yarar, hangi dosyalar var, hangi sayılar diskten ölçüldü" sorularını yanıtlar; AI'ın analiz hızını envanter doğruluğu besler. Bu yüzden dosya `[[.templates/index]]` registry'sine kayıtlı bu şablondan üretilir (Guardrail #16).

**İlişki (kopya yok, görev paylaşımı):** 4 doküman ortak iskeleti (docType: `context|claude|agents|workflow`) **`[[../frontend/context-template]]`** içinde tanımlıdır ve **bağlayıcıdır**. Bu şablon onu **tamamlar**: CONTEXT.md'ye özgü envanter tablosu formatı, eli10/eli15 blokları ve sayım disiplinini taşır. Çelişirse `[[../frontend/context-template]]` kazanır.

| Alan | Değer |
|------|-------|
| Template Name | `context-md-template.md` |
| Template Path | `.ai/.templates/documentation/context-md-template.md` |
| Hedef Dosya Tipi | `CONTEXT.md` (envanter / bağlam) |
| Hedef Konum | Proje kökü, `.ai/` veya herhangi bir klasör kökü |
| Guardrail | #16 (Template Mandatory) — şablonsuz CONTEXT.md üretilemez |
| Birincil Yazar | Master Orchestrator (vault-updater) |
| İskelet | H1 + §1 Amaç → §7 Referanslar (7 bölüm + frontmatter + `docType: context`) |
| Zorunlu Blok | §3.1 Envanter tabloları + §3.3 eli10/eli15 bloğu + §3.5 "İçerik Neden Ayrı" |
| Kanıt — Vault | `.ai/CONTEXT.md` (vault envanteri), `.ai/.templates/index.md` |
| Kanıt — Kural | Guardrail #3 (Zero-Hallucination — sayı = ölçüm), #16 (şablon) |
| Korunan Bilgi | 7 alanlı frontmatter + `docType`, 5 sütunlu satır formatı, disk kazanır kuralı |
| Değişken Formatı | `{{VARIABLE}}` (KONU, DATE, VERSION, INVENTORY_ROWS, BOOT_ORDER, SSOT_PATH) |
| Dil | Türkçe (mojibake YASAK); dosya adı `CONTEXT.md` (İngilizce) |
| Authority | Template (Guardrail #16) — Registry: `.ai/.templates/index.md` |
| Governance | Red Team · Human Mode · Truth Mode |

### §1.1 Bu Şablon Ne İçin — Ne İçin Değil

| İhtiyaç | Doğru Şablon | Bu Şablon Kullanılmaz |
|---------|--------------|------------------------|
| CONTEXT.md (envanter) | ✅ Bu şablon | — |
| 4用途 ortak iskelet aranıyorsa | `[[../frontend/context-template]]` (bağlayıcı) | ❌ bu şablon ona tabidir |
| CLAUDE.md / AGENTS.md / WORKFLOW.md | ilgili `*-md-template` | ❌ |
| CSS katman context'i | `[[../frontend/css-template]]` + frontend context-template | ❌ |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `CONTEXT.md` dış iskeleti (frontmatter + docType + §1-§7) | Şablon listesi/sayımları → `.templates/index.md` (SSOT) |
| Envanter tabloları (dosya/dizin/kategori) | Mimari kararlar → `brain.md` / ADR |
| Boot okuma sırası + kök↔.ai ilişkisi | Agent routing → `AGENTS.md` |
| Çelişki bölümü (vault ↔ disk) | Kaynak kod içeriği |

- **Ön koşul:** Hedef klasör diskte MEVCUT ve envanter sayımları yapılmış; sayı yoksa `UNKNOWN` yazılır (uydurulmaz).
- **Bütçe:** Vault kök CONTEXT.md için validator bütçesi **≤660 satır** (`.ai/scripts/validate.mjs` kontrol eder).

---

## §3 Mimari — Üretilen CONTEXT.md İskeleti

### §3.1 Dış İskelet (Frontmatter + docType)

````markdown
---
title: "CoreMusic — {{KONU}} Context"
type: docs
category: docs
docType: context
version: {{VERSION}}
status: active
authority: "{{SSOT_PATH_OR_klasör}}"
updated: {{DATE}}
date: {{DATE}}
---

# CoreMusic — {{KONU}} Context

**Zorunlu Bağlantılar:** [[.templates/index]] · [[../CLAUDE.md]] · [[../AGENTS.md]]
````

### §3.2 Zorunlu Bölüm Blokları (§1-§7)

| Bölüm | CONTEXT.md'ye özgü içerik |
|-------|---------------------------|
| §1 Amaç | 1 paragraf özet + ilgili karar/ADR tablosu |
| §2 Kapsam | Kapsam / Kapsam Dışı tablosu + kullanıcılar + ön koşul |
| §3 Mimari | **Envanter tabloları**: kök dosya envanteri (dosya · satır · sürüm · rol) + alt dizin envanteri (dizin · dosya sayısı · rol) + boot okuma sırası + kök↔vault ilişkisi |
| §4 Kurallar | Guardrail özeti + **çelişki kuralı: vault ↔ disk → disk kazanır** |
| §5 Workflow | Envanter güncelleme akışı (ölç → yaz → index'e ekle → log) |
| §6 Doğrulama | # | Kontrol | Kriter tablosu (envanter tutarlılığı dahil) |
| §7 Referanslar | Kaynak · Yol · Amaç wiki-link tablosu |

### §3.3 Envanter Satırı + eli10/eli15 (Zorunlu)

Her ana dosya/dizin satırının **hemen altına** (bağlayıcı şablon `[[../frontend/context-template]]` §3.5 formatı birebir):

```
> **eli10 (basit):** <bu dosya ne işe yarar — 1-2 cümle, günlük dil, jargon yok>
> **eli15 (detay):** <neden ayrı, neden okunur, neden yazılır, ne olur olmazsa — 3-4 cümle>
```

5 sütunlu zorunlu satır: `Katman/Dosya | Ne için | Neyden | Neden var | Ne zaman düzenlenir` (4 soru boş kalırsa gerekçe yazılır).

### §3.4 Boot / İlişki Blokları

- **Boot okuma sırası:** `kök → .ai/ → ihtiyaç anında @` fenced-code veya numaralı liste.
- **Kök ↔ Vault ilişkisi:** hangi kök dosya hangi `.ai/` dosyasının pointer'ı (tablo).
- **"İçerik Neden Dosyalara Ayrıldı?":** 5 satırlık gerekçe tablosu (`docType: context`'te zorunlu — bak. `[[../frontend/context-template]]` §3.6).

---

## §4 Kurallar

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Frontmatter 7 alan + `docType: context` | Doküman reddedilir |
| 2 | Her sayı/sayısal iddia **ölçümle** kanıtlanır; yoksa `UNKNOWN` | Zero-Hallucination ihlali |
| 3 | Vault ↔ disk çelişkisinde **disk kazanır** + ⚠️ işaretlenir | SSOT ihlali |
| 4 | Envanter tablosu eksik sütunlu üretilmez (5 sütun) | Şablon ihlali |
| 5 | eli10 ≤2 cümle, eli15 3-4 cümle; etiket/sıra birebir | Doğrulama #9 ihlali |
| 6 | Placeholder `{{VARIABLE}}` kalamaz | Doğrulama #3 ihlali |
| 7 | Wiki-link `[[...]]` formatı | Link check kırmızı |
| 8 | Emoji yasak (yalnız `[[...]]`/PNG referansı) | revert |
| 9 | Bütçe (vault kök): ≤660 satır | validator fail |

---

## §5 Workflow

```
HEDEF KLASÖRÜ BELİRLE → DISK ÖLÇ (dosya/satır/sürüm sayıları)
→ ŞABLONU KOPYALA (§3.1) → FRONTMATTER DOLDUR (7+1 alan)
→ ENVANTER TABLOLARI (ölçülen değerlerle) → eli10/eli15 BLOKLARI
→ BOOT/İLİŞKİ BLOKLARI → ÇELİŞKİ BÖLÜMÜ → GUARDRAIL #16 DOĞRULA
→ İLGİLİ INDEX'E 1 SATIR EKLE → COMMIT (subagent atmaz)
```

---

## §6 Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 alan + `docType: context` |
| 2 | Bölüm yapısı | §1-§7 aynı sıra |
| 3 | Placeholder | `{{` kalmadı |
| 4 | Sayılar | Her sayı ölçümle destekli veya `UNKNOWN` |
| 5 | eli10/eli15 | Her ana maddede blok var, format §3.3 ile birebir |
| 6 | Çelişki kuralı | §4'te "disk kazanır" var |
| 7 | Boot sırası | Kök↔.ai ilişkisi tablosu var |
| 8 | Wiki-link | Hedefler diskte mevcut |
| 9 | Bütçe | ≤660 satır (vault kök) |
| 10 | Registry | `.templates/index.md`'e 1 satır eklendi |

---

## §7 Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Template registry | [[.templates/index]] | Envanter + kayıt satırı |
| Bağlayıcı ortak iskelet | [[../frontend/context-template]] | 4用途 §1-§7 + eli10/eli15 (çelişkide o kazanır) |
| CLAUDE.md şablonu | [[./claude-md-template]] | Kural dosyası iskeleti |
| Vault context SSOT'u | [[../../CONTEXT]] | İçerik kaynağı |
| Vault anayasası | [[../CLAUDE.md]] | Guardrail #3 / #16 |
| Doğrulama betiği | `.ai/scripts/validate.mjs` | Bütçe + 8 check |

---

**Template Version:** 1.0.0
**Last Updated:** 2026-10-07
