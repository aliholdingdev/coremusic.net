---
title: "CoreMusic — UI Design MD Pattern Standard"
type: reference
category: ui-design
date: 2026-09-08
updated: 2026-09-08
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — UI Design MD Pattern Standard

## 1. Amaç

`.ai/ui-design/` altındaki tüm içerik MD dosyalarının frontmatter, bölüm yapısı ve footer formatını standartlaştırmak. WikiPage-Template.md (Guardrail #16) ve vault AGENTS/CLAUDE çiftlerindeki kanoniğe sadık kalınır.

## 2. Frontmatter Şeması

### 2.1 Zorunlu Alanlar (7)

```yaml
---
title: "Dosya Başlığı"
type: reference|component-prompt|layout-prompt|page-prompt|screen-spec|token
category: ui-design|layout-pattern|page-prompt|component|screen
date: YYYY-MM-DD
updated: YYYY-MM-DD
status: active
version: X.Y.Z
---
```

### 2.2 Ek Zorunlu Alanlar (vault uyumu)

```yaml
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
```

### 2.3 Domain-Specific Ek Alanlar (dosya tipine göre)

| Tip | Ek Alanlar | Örnek |
|-----|-----------|-------|
| `component-prompt` | `component_id`, `bem_class`, `itcss_layer`, `target_file`, `author` | C09 |
| `layout-prompt` | `viewport` | `1024×600` |
| `page-prompt` | `route`, `layout` | `/`, `modal-600x308` |
| `screen-spec` | `screen_id`, `resolution`, `png` | `home-1024`, `1024×600` |
| `token` | `platforms`, `themes`, `reference` | `linux-embedded` |

### 2.4 Tam Örnek — Component Prompt

```yaml
---
title: "C09 — Media Card Component Prompt"
type: component-prompt
category: ui-design
date: 2026-08-11
updated: 2026-09-08
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
component_id: "C09"
bem_class: ".media-card"
itcss_layer: "04_Components"
target_file: "css/04_Components/_media-card.css"
author: "UI Designer Agent"
---
```

### 2.5 Tam Örnek — Layout Prompt

```yaml
---
title: "Layout Pattern — Split Home (42/58)"
type: layout-prompt
category: layout-pattern
date: 2026-08-11
updated: 2026-09-08
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
viewport: "1024×600"
---
```

### 2.6 Tam Örnek — Page Prompt

```yaml
---
title: "Sayfa Prompt — Hoş Geldin Modalı"
type: page-prompt
category: page-prompt
date: 2026-08-11
updated: 2026-09-08
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
route: "/ (ilk giriş)"
layout: "modal-600x308"
---
```

### 2.7 Tam Örnek — Screen Spec

```yaml
---
title: "Home Dashboard — 1024×600 ASCII Art View"
type: screen-spec
category: screen
date: 2026-09-06
updated: 2026-09-08
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
screen_id: "home-1024"
resolution: "1024×600"
png: "Linux  1024 - Home Page.png"
---
```

### 2.8 Tam Örnek — Root/Index Dosyası

```yaml
---
title: "CoreMusic — Mockup Index (19 PNG)"
type: reference
category: ui-design
date: 2026-09-04
updated: 2026-09-08
status: active
version: 6.1.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---
```

## 3. Bölüm İskeleti

### 3.1 Standart İskelet (Tüm Dosyalar)

```markdown
# Dosya Başlığı

## 1. Amaç
## 2. İçerik (dosya tipine göre değişir)
## 3. Detaylar
## 4. Kurallar
## 5. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[link]] | açıklama |

## 6. Quality Report

| Metrik | Değer |
|--------|-------|
| **Version** | X.Y.Z |
| **Status** | ✅ Active |

---

*Title vX.Y.Z — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: YYYY-MM-DD*
*Mode: Red Team · Human Mode · Truth Mode*
```

### 3.2 Component Prompt İskeleti

```markdown
# CXX — Bileşen Adı (.bem-class)

## 1. Bileşen Tanımı
## 2. PNG Ölçümleri
## 3. Token Referansları
## 4. WCAG Gereksinimleri
## 5. CSS Üretim Talimatları
## 6. Notlar

---

*CXX Component vX.Y.Z — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: YYYY-MM-DD*
*Mode: Red Team · Human Mode · Truth Mode*
```

### 3.3 Layout Prompt İskeleti

```markdown
# Layout Pattern — Ad (Oran)

## 1. Kullanım Alanları
## 2. Yapı (ASCII box diagram)
## 3. Kurallar (tablo)
## 4. Bileşen Eşlemesi

---

*Layout Pattern vX.Y.Z — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: YYYY-MM-DD*
*Mode: Red Team · Human Mode · Truth Mode*
```

### 3.4 Page Prompt İskeleti

```markdown
# Sayfa Adı (Route)

## 1. Route & Layout
## 2. Bileşen Listesi
## 3. Yapı (ASCII box diagram)
## 4. Akış
## 5. Notlar

---

*Page Prompt vX.Y.Z — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: YYYY-MM-DD*
*Mode: Red Team · Human Mode · Truth Mode*
```

### 3.5 Screen Spec İskeleti

```markdown
# Ekran Adı — Çözünürlük ASCII Art View

## 1. Ekran Bilgisi
## 2. ASCII Art View
## 3. Bileşen Haritası
## 4. Ölçümler
## 5. İlgili PNG

---

*Screen Spec vX.Y.Z — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: YYYY-MM-DD*
*Mode: Red Team · Human Mode · Truth Mode*
```

## 4. Footer Standardı

Tüm içerik MD dosyalarının sonunda standart footer:

```markdown
---

*{Dosya Adı} v{X.Y.Z} — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {YYYY-MM-DD}*
*Mode: Red Team · Human Mode · Truth Mode*
```

**Kurallar:**
- Boş satır + tireler (`---`) ile section ayırıcı
- Italic (`*`) ile imza bloğu
- authority, date, mode sırası sabit

## 5. Validasyon Kontrol Listesi

| # | Kontrol | Doğru | Yanlış |
|---|---------|-------|--------|
| 1 | Frontmatter 7 zorunlu alan | `title,type,category,date,updated,status,version` | Eksik alan |
| 2 | authority + governance mevcut | Her dosyada | Yok |
| 3 | Date formatı | `YYYY-MM-DD` | `DD/MM/YYYY` |
| 4 | Version semver | `1.0.0` | `1.0` veya `v1` |
| 5 | Başlık H1 tek | `# Başlık` | Birden fazla `#` |
| 6 | Bölüm numaralandırma | `## 1. Bölüm` | `## Bölüm` (numarasız) |
| 7 | Footer mevcut | İmza bloğu | Yok |
| 8 | Wiki link formatı | `[[path/to/file]]` | `[[path.md]]` |

## 6. Etkilenen Dosyalar

| Grup | Dosya Sayısı | Değişiklik |
|------|-------------|------------|
| `prompt/component/C*.md` | 16 | Frontmatter + footer |
| `prompt/layout/*.md` | 4 | Frontmatter + footer |
| `prompt/page/*.md` | 13 | Frontmatter + footer |
| `prompt/screen/*.md` | 4 | Frontmatter + footer |
| `screens/qr-*.md` | 17 | Frontmatter + footer |
| `screens/[A-F]-*/*.md` | ~15 | Frontmatter + footer |
| `screens/_components/*.md` | 16 | Frontmatter + footer |
| `screens/_layout-patterns/*.md` | 6 | Frontmatter + footer |
| `mockups/*.md` | 8 | Frontmatter + footer |
| `reference/*.md` | ~12 | Frontmatter + footer |
| `tokens/*.md` | 4 | Frontmatter + footer |
| `flow/**/*.md` | ~15 | Frontmatter + footer |
| Root `0*.md` | 6 | Frontmatter + footer |
| **TOPLAM** | **~140** | — |

## 7. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[.ai/.templates/documentation/WikiPage-Template]] | Guardrail #16 template |
| [[./AGENTS.md]] | UI Design agent talimatları |
| [[./CLAUDE.md]] | UI Design bağlam |
| [[./00-mockup-index]] | Mockup indeksi |
| [[./01-component-inventory]] | C01-C16 envanteri |
| [[./04-vault-registration]] | Vault kayıt durumu |

## 8. Quality Report

| Metrik | Değer |
|--------|-------|
| **Version** | 1.0.0 |
| **Status** | ✅ Active |
| **Frontmatter** | ✅ 7 zorunlu + 2 ek |
| **Section Skeleton** | ✅ 5 tip (component, layout, page, screen, root) |
| **Footer Standard** | ✅ 4 satır imza |
| **Validation** | ✅ 8 kontrol maddesi |

---

*UI Design MD Pattern Standard v1.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team · Human Mode · Truth Mode*
