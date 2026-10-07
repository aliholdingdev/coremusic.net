---
title: "skill-maker Example — Punteri (Pointer) Skill Üretimi"
type: example
category: skills
version: 1.0.0
updated: 2026-10-07
---

# Örnek: Punteri Skill Üretimi (SSOT başka dizindeyken)

**Ne zaman:** İçeriğin gerçek kaynağı başka bir dizinde (ör. `C:\.claude\skills\` global
veya `.ai/` vault) ve skill yalnızca **keşif + yönlendirme** sağlamalıdır. Amaç: tek
kayıt (SSOT), çift kopya DEĞİL (drift önleme).

---

## Senaryo

> **Not (CoreMusic bağlamı):** Kanonik 5 global skill (`truth-engine`, `human-mode`,
> `verify-loop`, `agent-debate`, `context-report`) **tek-ev kuralı** altındadır —
> bunlar için punteri DEĞİL, doğrudan global çağrı kullanılır (çift kopya = `--check` ihlali).
> Bu örnek, isimleri sanal bir `prompt-security-review` üzerinden gösterilen GENEL punteri desenidir.

```text
İstek: "Global C:\.claude\skills\ içindeki prompt-security-review skill'ini
        bu projede kullanılabilir yap — ama kopya oluşturma"
```

Çözüm: **punteri skill** — proje `.claude/skills/prompt-security-review/SKILL.md`
bir gösterge olur; tetikleyiciler frontmatter'da, tam içerik global dosyada.

---

## Punteri SKILL.md iskeleti (üretilen)

```markdown
---
name: prompt-security-review
description: "<TETİKLEYİCİLER — global orijinalden BİREBİR kopya (keşif için)>"
license: MIT
metadata:
  version: 3.0.0
  format: claude-skill-v3
  author: Bayram Ali (ULTRATHINK Engineering)
  category: meta-skill
  updated: 2026-10-07
  authority: "POINTER — SSOT: C:\.claude\skills\prompt-security-review\SKILL.md"
---

# prompt-security-review — KISAYOL / POINTER

> ⚠️ Bu dosya bir gösterge. Tek SSOT: `C:\.claude\skills\prompt-security-review\SKILL.md`

## Zorunlu Adım 1 — Tam metni oku
Bu skill'i kullanmadan ÖNCE SSOT dosyayı **bütünüyle oku**, sonra O kurallarla uygula.
Bu punteri yalnız keşif + yönlendirme içindir (kopya drift'i yasak).

## Ne yapar (özet)
<2-3 satır — global dosyanın H1/özetinden, uydurma yok>

## Tetikleyiciler
<description'daki tetikleyiciler>

## Punteri kuralı
SSOT güncellenirse bu gösterge de güncellenir (`[VERIFY]`); içerik buraya kopyalanmaz.
```

---

## Adım dökümü (skill-maker akışıyla)

| ADIM | Bu senaryoda |
|------|--------------|
| 1 Gereksinim | ad = global adla AYNI (yanlış ad = keşif kırılır) · görev = yönlendirme · araç = yok |
| 2 Web research | gerekmez (içerik uydurulmaz; yalnız okuma + kopya tetikleyici) |
| 3 Yapı | tek `SKILL.md` (references/examples gereksiz — içerik taşınmıyor) |
| 4 Enjeksiyon | Vault kuralı: punteri, `.ai/` kurallarıyla çelişemez; punteri çelişirse → DUR |
| 5 Kalite | checklist §5 + özel: **description birebir kopya mı? (diff 0)** |

---

## Yapılır / Yapılmaz

| ✅ Yapılır | ❌ Yapılmaz |
|-----------|------------|
| `description` orijinalle **birebir** (diff = 0) | Tetikleyicileri "iyileştirmek" (keşif kırılır) |
| SSOT yolu frontmatter + gövdede (2 yer) | İçeriği punteriye kopyalamak (drift) |
| SSOT = proje `.claude/skills/` ya da global `C:\.claude\skills` | Punteriyi SSOT ilan etmek |
| Orijinal değişimde punteri güncelleme notu | Dosyayı silmek/punteriye rağmen kopya tutmak (ADR-042: dosya adı silinmez) |

---

## Kalite çıkışı (örnek)

```text
[x] description diff 0 (orijinalle birebir)
[x] SSOT yolu 2 yerde (metadata.authority + gövde)
[x] metadata.authority: "POINTER — …" ayrımı (içerik skill'i ile karışmaz; kökte type/authority YASAK — N1)
[x] Punteri kuralı + [VERIFY] notu mevcut
```
