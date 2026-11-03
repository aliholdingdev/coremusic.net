---
title: "CoreMusic — Frontend Kuralları (Vanilla JS · ITCSS · Light Mode Only)"
type: guide
category: governance
date: 2026-10-22
updated: 2026-11-01
version: 2.0.0
status: active
authority: "Derived — SSOT: .ai/CLAUDE.md §7 (#11/#16/#17), §21 · .ai/.rules/kurallar.md · .ai/.rules/hata-listesi.md (HL-01, HL-04, HL-05, HL-12)"
docType: claude
---

# CoreMusic — Frontend Kuralları (Frontend Rules)

**docType:** claude · **Klasör:** `.ai/.rules/` · **Sorumlu:** ui-designer

**Zorunlu Bağlantılar:** [[RULES.md]] · [[kurallar.md]] · [[hata-listesi.md]] · [[security-rules.md]] · [[.ai/CLAUDE.md]] · [[.ai/AGENTS.md]]

> **eli10:** YALNIZCA LIGHT MODE geçerlidir (dark mode kesinlikle yasaktır); mockup ve token okumadan kod yazılmaz; framework yok, tek component + responsive ITCSS.

---

## 1. Amaç

CoreMusic frontend (HTML/CSS/JS/PHP view) mimarisini; **Yalnızca Light Mode** zorunluluğunu, **Mockup Gate** protokolünü, ITCSS 11 katman disiplinini, BEM adlandırmasını, Vanilla JS kurallarını ve responsive kısıtlamalarını vault SSOT'larından toplar. UI kalite hedefi: **%100 ITCSS, BEM, WCAG 2.2 AA, C01-C16 uyumu** (`.ai/AGENTS.md` §16).

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `*.css` (ITCSS 11 katman) · `*.js` (Vanilla ES2022+) · layout view'lar | Backend API sözleşmeleri → [[backend-rules.md]] |
| `.ai/ui-design/**` ve `.ai/.png/**` referans okuma protokolü | CSP nonce ve oturum tokenleri → [[security-rules.md]] |
| WCAG 2.2 AA erişilebilirlik ve 45-tier cihaz matrisi | Mimari servis zinciri → [[architecture-rules.md]] |
| Container query disiplini (`HL-04`) ve güvenli DOM init (`HL-05`) | Veritabanı ve SQL → [[database-rules.md]] |

---

## 3. Mimari

### 3.1 Tema Zorunluluğu — YALNIZCA LIGHT MODE (`HL-01` & `KURAL-03`)

**KESİNLİKLE DEĞİŞTİRİLEMEZ KURAL:**
1. CoreMusic ekosisteminde **ŞU ANDA YALNIZCA LIGHT MODE MEVCUTTUR**. Dark Mode henüz tasarlanmamıştır/yoktur!
2. Tüm CSS, bileşen ve sayfa tasarımları istisnasız Light Mode paletine göre üretilir.
3. Dark mode varsayımı yapmak, dark class'ları (`.dark`, `[data-theme="dark"]` vb.) eklemek veya `@media (prefers-color-scheme: dark)` sorgusu yazmak **KESİNLİKLE YASAKTIR**.
4. Renkler ve yüzeyler yalnızca `01_Abstracts/a-*.css` içinde tanımlı `--cm-*` light mode semantik tokenleriyle kullanılır.

### 3.2 Mockup Before Frontend — Guardrail #11 (`@.ai/CLAUDE.md` §7.1)

Aşağıdaki 6 referans dosyası OKUNMADAN frontend kodu yazılamaz. Görsel veya şablon okunamıyorsa **DUR ve bildir** (ihlal: derhal revert + CRITICAL log):

| Sıra | Referans Dosyası | Amacı & Kapsamı |
|---|---|---|
| 1 | `.ai/ui-design/01-mockup-index.md` | Hangi ekranların ve mockup'ların mevcut olduğu (19 PNG, 45-tier matris). |
| 2 | `.ai/ui-design/02-component-inventory.md` | C01-C16 BEM sınıfları, piksel ölçümleri, padding/margin değerleri. |
| 3 | `.ai/ui-design/tokens/design-tokens-master.md` | Renk, boşluk, tipografi, cam efektleri ve CSS tokenleri. |
| 4 | `.ai/ui-design/screens/00-ascii-art-index.md` | Piksel düzeyinde ASCII layout ve yerleşim modelleri. |
| 5 | `.ai/ui-design/05-responsive-architecture.md` | 4K No-Center kuralı ve fallback zorunluluğu. |
| 6 | `.ai/.templates/frontend/css-template.md` (v4.0.0) | Her `.css` dosyası yazımında uyulacak kanonik şablon. |

*Çelişki Halinde Öncelik Sırası:* **PNG > ASCII Art > Component Inventory > Tokens > Implementation Plan.**

### 3.3 CSS & ITCSS Mimarisi (`HL-04`, `HL-12`, Guardrail #16)

| Kural | Standart Değer / Kısıtlama |
|---|---|
| **11 Katman Sırası** | `01_Abstracts → 02_Base → 03_Layout → 04_Components → 05_Pages → 06_Utilities → 07_Vendors → 08_Devices → 09_ViewModes → 10_Helpers → 11_OAuth` |
| **Token Disiplini** | Sabit değerler (hex, px vb.) **yalnızca** `01_Abstracts/a-*.css` içinde `--cm-*` ile tanımlanır; bileşen veya sayfa dosyalarında ham hex/px yazılamaz. `@media` sorgusu içinde `var()` okumak yasaktır. |
| **BEM Metodolojisi** | Sınıflar katı BEM (`block__element--modifier`) formatında ve ilgili katmanın namespace ön ekiyle yazılır (`c-`, `p-`, `u-` vb.). |
| **07_Vendors Katmanı** | Üçüncü taraf kütüphane stilleri; **kesinlikle dokunulmaz**. |
| **Taşıma / Silme Yasağı (`HL-12`)** | Mevcut CSS sınıfları ve import zinciri taşınamaz/silinemez; yanlış yerleşim yalnızca raporlanır (ADR ve onay şarttır). |
| **Import Zinciri** | Cihaz stilleri `08_Devices/d-*.css` ve `auth-bundled.css` üzerinden yüklenir. **`main.css` DİYE BİR DOSYA YOKTUR.** |
| **Container Query Disiplini (`HL-04`)** | `container-type: inline-size` doğrudan ölçülecek kapsayıcıya verilmelidir; flex sütunlarda başlık çökmesini önlemek için `minmax(0, ...)` kullanılmalıdır. |

### 3.4 JavaScript Standartları (Vanilla ES2022+ & `HL-05`)

| ❌ Kesinlikle Yasak | ✅ Zorunlu Standart |
|---|---|
| React, Vue, Angular, jQuery (ADR-001) | **Vanilla JS ES2022+** |
| `var` değişken tanımı | `const` (ve gerektiğinde `let`) |
| `any` tipi (TypeScript dosyalarında) | Katı tip tanımları (`strict: true`) |
| `innerHTML` ile kontrolsüz HTML basımı | `DOMParser` + `TrustedTypes` veya `textContent` / `createElement` |
| `eval()`, `new Function()` | Güvenli alternatifler |
| Kimlik/token için `localStorage` / `sessionStorage` | HTTPOnly cookie tabanlı oturum yönetimi |
| DOM elemanı beklemeden init (`HL-05`) | Güvenli init: container DOM'da yoksa sessiz kalınmaz; `dbg.warn` verilir ve `_initialized = false` bırakılır. |

### 3.5 Responsive & Cihaz Boyutları (Guardrail #17)

- **Tek Component Sistemi:** Tüm cihazlar için tek bir HTML/JS bileşeni kullanılır; cihaza özel ayrı HTML üretmek YASAKTIR.
- **Piksel Referansı (1024×600):** Header 60px (y:0-60) · İçerik Alanı 450px (y:60-510) · Footer 90px (y:510-600).
- **Kanonik Widget Grid Kuralı:**
  - `1024` ekranında: Satır1 **2×2**, Satır2 **1×5**, Satır3 **1×5** = **12 slot**.
  - `1920` ekranında: Satır1 **4×4**, Satır2 **1×8**, Satır3 **1×8** = **20 slot**.
- **Erişilebilirlik:** WCAG 2.2 AA minimum standardı; dokunma alanı (touch target) minimum 24×24px; görünür focus outline 3px.

---

## 4. Kurallar

| # | Kural | İhlal Sonucu |
|---|---|---|
| 1 | Dark mode varsayımı veya dark mode kodu yazılması (`HL-01`) | Derhal revert |
| 2 | Mockup ve token dosyaları okunmadan frontend kodu yazılması | Revert + CRITICAL log |
| 3 | Framework kullanımı (React/Vue vb. — ADR-001) | Kod reddedilir |
| 4 | `01_Abstracts` dışına ham hex/px token yazılması | Kod reddedilir |
| 5 | Mevcut CSS dosyalarının taşınması veya silinmesi (`HL-12`) | Dosya geri yüklenir |
| 6 | DOM'da olmayan container ile güvensiz init yapılması (`HL-05`) | İhlal; init düzeltilir |
| 7 | Subagent commit çalıştırması (`HL-03`) | İhlal; orkestratör yetkisindedir |

---

## 5. Workflow

```text
Frontend Görevi (CSS / JS / View / Bileşen)
  → Tema Kontrolü: Yalnızca Light Mode (HL-01)
  → Mockup Gate: 01-mockup-index + 02-component-inventory + tokens + ascii-art
  → CSS İşi İse: css-template v4.0.0 + ITCSS 11 katman sırası
  → Kodlama: Vanilla JS ES2022+ + BEM + --cm-* tokenleri + güvenli init (HL-05)
  → Browser & Konsol Doğrulaması: Gerçek sayfada hata olmamalı
  → durum.md güncelleme + log.md append
```

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---|---|
| 1 | Frontmatter 7 alan + docType | Mevcut |
| 2 | Light Mode doğrulaması | Yalnızca Light Mode vurgulandı; Dark mode yasaklandı (`HL-01`) |
| 3 | Mockup Gate dosyaları | 6 dosya eksiksiz listelendi |
| 4 | ITCSS katman sırası | 11 katman birebir uyumlu |
| 5 | JS güvenli init standardı | `HL-05` ve `HL-04` container query kuralları işlendi |
| 6 | Widget grid kuralı | 1024 (12 slot) ve 1920 (20 slot) kanonik kuralı mevcut |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|---|---|---|
| Temel Direktifler | [[kurallar.md]] | KURAL-03 (Light Mode zorunluluğu) |
| Hata Listesi Kataloğu | [[hata-listesi.md]] | HL-01, HL-04, HL-05, HL-12 tuzakları |
| Master Kurallar | [[RULES.md]] | Genel aktivite kuralları |
| Mockup İndeksi | `.ai/ui-design/01-mockup-index.md` | 19 PNG ve ekran katalogları |
| Bileşen Envanteri | `.ai/ui-design/02-component-inventory.md` | C01-C16 bileşenleri |
| Token Master | `.ai/ui-design/tokens/design-tokens-master.md` | CSS tasarım tokenleri SSOT |
| CSS Şablonu | `.ai/.templates/frontend/css-template.md` | v4.0.0 kanonik şablon |

---

**Authority:** Bayram Ali / Kurucu & Mimari Otorite  
**Last Updated:** 2026-11-01 · **Version:** 2.0.0
