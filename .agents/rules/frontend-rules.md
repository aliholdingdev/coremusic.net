---
title: "CoreMusic — Frontend Kuralları (Vanilla JS · ITCSS · BEM)"
type: guide
category: governance
date: 2026-10-22
updated: 2026-10-22
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/CLAUDE.md §7 (#11/#16/#17), §21 · kök AGENTS.md §10 · .ai/AGENTS.md §16"
docType: claude
---

# CoreMusic — Frontend Kuralları (Frontend Rules)

**docType:** claude · **Klasör:** `.agents/rules/` · **Sorumlu:** ui-designer

**Zorunlu Bağlantılar:** [[RULES]] · [[security-rules]] · [[../../.ai/CLAUDE]] · [[../../.ai/AGENTS]]

> **eli10:** Frontend işine mockup ve token okumadan başlama; framework yok, her şey tek component + responsive CSS.

---

## 1. Amaç

Frontend (HTML/CSS/JS/PHP layout) kurallarını: **Mockup Gate**, ITCSS katman sırası, BEM, design token, responsive/zorunlu okuma protokolü ve yasak pattern'ler halinde vault SSOT'larından toplar. UI kalite hedefi **%100** (ITSS, BEM, WCAG 2.2 AA, C01-C16) — `.ai/AGENTS.md` §16.

---

## 2. Kapsam

| Kapsam | Kapsam_Dışı |
|--------|-------------|
| `*.css` (ITSS 11 katman) · `*.js` (Vanilla ES2022) · layout/PHP view | Backend sözleşmeleri → [[backend-rules]] |
| `.ai/ui-design/**` referans okuma protokolü | Secret/nonce → [[security-rules]] |
| WCAG 2.2 AA · 45-tier cihaz matrisi | Mimari API zinciri → [[architecture-rules]] §3.4 |

---

## 3. Mimari

### 3.1 Mockup Before Frontend — Guardrail #11 (`@.ai/CLAUDE.md` §7.1)

**KESİNLİKLE YASAK:** aşağıdaki dosyalar OKUNMADAN frontend kodu yazılamaz; görsel okunamıyorsa **DUR ve bildir** (ihlal: kod derhal revert + CRITICAL log).

| Sıra | Dosya | Kullanım anı |
|------|-------|--------------|
| 1 | `.ai/ui-design/01-mockup-index.md` | İlk okunacak — hangi ekranlar var (19 PNG, 45-tier) |
| 2 | `.ai/ui-design/02-component-inventory.md` | C01-C16 BEM sınıfları, pixel ölçümleri |
| 3 | `.ai/ui-design/tokens/design-tokens-master.md` | Renk/boşluk/typography/cam token'ları |
| 4 | `.ai/ui-design/screens/00-ascii-art-index.md` | Piksel düzeyinde layout modelleri |
| 5 | `.ai/ui-design/05-responsive-architecture.md` | **§7.4 4K No-Center** + **§12 fallback ZORUNLU** |
| 6 | `.ai/.templates/frontend/css-template.md` (v4.0.0) + kök `notes.md` | Her `.css` yazımında |

**Referans sıralaması (çelişki halinde):** PNG > ASCII art > Component Inventory > Tokens > Implementation Plan.

### 3.2 CSS Kuralları (Guardrail #16 — kök `AGENTS.md` §10)

| Kural | Değer |
|-------|-------|
| Katman sırası | 01_Abstracts → 02_Base → 03_Layout → 04_Components → 05_Pages → 06_Utilities → 07_Vendors → 08_Devices → 09_ViewModes → 10_Helpers → 11_OAuth |
| Token | Sabit değer **yalnız** `01_Abstracts/a-*.css` içinde `--cm-*`; ham hex/px katman dosyasına yazılmaz; `@media` içinde `var()` okuma **yasak** |
| BEM | Namespace zorunlu |
| `07_Vendors` | **Dokunulmaz** |
| Taşıma/silme | **YOK** — yanlış yerleşim yalnız raporlanır (onay + ADR olmadan taşıma yasak) |
| Import zinciri | `08_Devices/d-*.css` + `auth-bundled.css` — **`main.css` YOK** |
| Cihaz token'ı | `a-layout-tokens-{mobile,tablet,1024,1920,3540,3840}.css` |
| Şablonlar | `.ai/.templates/frontend/` kanonik set (css · abstracts-token · component · page · device · auth-device · utility · helper) |

### 3.3 JS Kuralları (`@.ai/CLAUDE.md` §21 · kök `AGENTS.md` §5)

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| React / Vue / Angular | **Vanilla JS ES2022** (ADR-001) |
| `var` | `const` / `let` |
| `any` (TS) | strict tip |
| `innerHTML` | `DOMParser` + `TrustedTypes` |
| `eval()` / `Function()` | Safe alternatives |
| auth için `localStorage`/`sessionStorage` | Session-based auth (HTTPOnly cookie) |

### 3.4 Responsive & Cihaz (Guardrail #17 + §13.9 #1)

- **Tek component sistemi** + responsive CSS — ayrı HTML/branch **YASAK**.
- 1024×600 mockup = **pixel reference** (Header 60px y:0-60 · İçerik 450px y:60-510 · Footer 90px y:510-600).
- CSS variables + media queries; device CSS yalnız behavioral override.
- **Widget grid kanonik:** `1024` → 2×2 + 1×5 + 1×5 = 12 slot · `1920` → 4×4 + 1×8 + 1×8 = 20 slot (`.ai/AGENTS.md` §13.9 #1).
- **4K No-Center:** 3840'da ortalamama YASAK; **fallback ZORUNLU**.
- `tokens-3840.json` / `tokens-tv.json` **boş** → token ASLA uydurulmaz (`⚠️ VERIFICATION REQUIRED`).

### 3.5 Erişilebilirlik

WCAG 2.2 AA minimum · touch hedef ≥24×24px · görünür focus outline 3px · dark theme token'ları `--cm-*` (kök `AGENTS.md` §5 UI/UX).

---

## 4. Kurallar

| # | Kural | İhlal |
|---|-------|-------|
| 1 | Mockup/tokens okunmadan kod yok (§3.1) | Revert + CRITICAL log |
| 2 | Framework yasak (ADR-001) | Revert |
| 3 | Token yalnız 01_Abstracts'te | Revert |
| 4 | Dosya taşıma/silme yok | Dosya geri yüklenir |
| 5 | `main.css` yok; import zinciri korunur | Revert |
| 6 | UI işi `.ai/ui-design/**` şablonlarına uyar (Kalıp A-D) | Dosya geçersiz |
| 7 | Uydurma token/ölçü yazılmaz | `VERIFICATION REQUIRED` |

---

## 5. Workflow

```text
Frontend iş (CSS/JS/layout/bileşen)
  → Mockup Gate: 01-mockup-index + 02-component-inventory + tokens + ascii-art + responsive (§3.1)
  → CSS ise: css-template v4.0.0 + kök notes.md + Guardrail #16 kanonik set
  → ui-design/** ise: Kalıp A-D şablon okuma
  → Kod (Vanilla + ITCSS + BEM + --cm-* token)
  → Browser testi (gerçek sayfa, konsol hatası yok)
  → WCAG 2.2 AA + C01-C16 taraması → MO: log.md
```

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter 7 alan + docType | Mevcut |
| 2 | Mockup Gate 6 dosyası sayılıyor | §3.1 tablosu eksiksiz |
| 3 | CSS 11 katman sırası | §3.2 |
| 4 | Yasaklar §21 ile eşleşme | innerHTML · eval · var · framework |
| 5 | Widget grid kuralı | §3.4 (vault §13.9 #1) |
| 6 | Tokens-3840/TV "boş" notu | Uydurma token yok |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Guardrail #11/#16/#17 | [[../../.ai/CLAUDE]] §7 | Mockup + şablon + responsive |
| Mockup indeksi | [[../../.ai/ui-design/01-mockup-index]] | 19 PNG + 45-tier |
| Component envanteri | [[../../.ai/ui-design/02-component-inventory]] | C01-C16 |
| Token master | [[../../.ai/ui-design/tokens/design-tokens-master]] | Token SSOT |
| Responsive mimari | [[../../.ai/ui-design/05-responsive-architecture]] | §7.4 + §12 |
| CSS şablonu | [[../../.ai/.templates/frontend/css-template]] | v4.0.0 |
| Kurallar (CSS) | [[../../AGENTS]] §10 | Kanonik okuma seti |
| Master | [[RULES]] §4.2/§4.5 | Özet |

**Last Updated:** 2026-10-22 · **Şablon:** context-template (Guardrail #16)
