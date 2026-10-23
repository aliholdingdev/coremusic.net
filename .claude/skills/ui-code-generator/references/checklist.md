# ui-code-generator — Kalite Kapısı (Quality Gate)

> **Kullanım:** Üretim tamamlandığında bu dosyayı yukarıdan aşağıya çalıştır.
> Her madde `PASS` / `BLOCK` / `N/A` ile işaretlenir. **Tek bir `BLOCK` varsa teslim YOK.**
> Çıktı biçimi: `GATE: PASS|BLOCK` + maddeler + kanıt (dosya:satır).

**Skill:** ui-code-generator · **Format:** Claude Skill v3.0 (N1–N10) · **Updated:** 2026-10-21

---

## G1 — Ön koşul (Gate A)

| # | Madde | Kanıt |
|---|-------|-------|
| G1.1 | ui-analyzer raporu elde (FACT/INFERENCE/UNKNOWN + ASCII layout) | rapor dosyası |
| G1.2 | §17 text-plan sunulmuş ve **ONAY** alınmış | onay kaydı |
| G1.3 | Görsel referans okundu (`.ai/ui-design/**` · `.ai/.png/**`) — mockup gate | dosya yolu |
| G1.4 | Token ağacı mevcut (`--cm-*`, sıcaklık paleti tek kaynak) | token dosyası |

## G2 — Mimari (ITCSS + BEM)

| # | Madde | Kanıt |
|---|-------|-------|
| G2.1 | Katman sırası 01→11 bozulmadı (`.ai/.templates/frontend/css-template.md` v4.0.0) | import bloğu |
| G2.2 | Sabit değer (hex/px/rem/rgba) yalnız `01_Abstracts/a-*.css` içinde | grep: ham hex katman dosyasında 0 |
| G2.3 | Sınıf adları BEM (`block__elem--mod`); DOM özdeşliği 1:1 | HTML + CSS |
| G2.4 | Stil DOM'a gömülü değil (`style=""` yok; `<style>` gömülü yok) | grep |
| G2.5 | Mevcut dosya adlandırması + import zinciri DEĞİŞTİRİLMEDİ (koruma kuralı) | diff |

## G3 — Yasaklı kalıp (§7.1 + kırmızı çizgiler)

| # | Madde | Kanıt |
|---|-------|-------|
| G3.1 | `!important` yok (yalnız: GSAP set/change, MEW2/wizard input) | grep `!important` |
| G3.2 | `@media` içi `var()`/`env()` okuma YOK | grep |
| G3.3 | `.ai/.templates/**` içine kod/sınıf adı yazılmadı | grep |
| G3.4 | Sıralı komşu + descendant + child kalıbı yok (`.a .b .c` 3+ seviye) | grep |
| G3.5 | Edit-distance ≤ 2 yönetim sınıfı (max/minimum...) YOK (Script A) | script raporu |
| G3.6 | `display: contents` ghost-text trick yok (gizlilik/focus çerçevesi sızıntısı) | grep |

## G4 — Responsive & device

| # | Madde | Kanıt |
|---|-------|-------|
| G4.1 | Kırılım noktaları token'lardan (sabit media query yok) | CSS |
| G4.2 | `08_Devices/` kapsam tamam (akıllı TV dahil — 16:9 arayüz) | dosya listesi |
| G4.3 | Widget grid kanonik: 1024 → 2×2 / 1×5 / 1×5 = 12 slot · 1920 → 4×4 / 1×8 / 1×8 = 20 slot | grid HTML/CSS |
| G4.4 | Dokunma hedefi ≥ 24×24px (kullanıcı-değiştirmez kural) | ölçüm |
| G4.5 | Theme flip (`[data-theme="dark"]`) + `prefers-color-scheme` ile uyumlu | CSS |

## G5 — Component State Matrix (10 durum)

| # | Durum | Zorunlu |
|---|-------|---------|
| G5.1 | `DEFAULT` | ✔ |
| G5.2 | `HOVER` | ✔ |
| G5.3 | `ACTIVE` | ✔ |
| G5.4 | `FOCUSED` (görünür outline, tekrar-giriş odak korunur) | ✔ |
| G5.5 | `DISABLED` (`disabled` niteliği; `pointer-events: none` yeterli değil) | ✔ |
| G5.6 | `ERROR` (border + ikon + mesaj; yalnız renk değil) | form |
| G5.7 | `LOADING` (skeleton/spinner, `aria-busy`) | asenkron |
| G5.8 | `EMPTY` (boş durum ekranı, `aria-live`) | veri |
| G5.9 | `SELECTED` (`aria-selected`/`aria-pressed`/`aria-current`) | seçimli |
| G5.10 | `READONLY` (form) | form |

## G6 — Accessibility (WCAG 2.2 AA)

| # | Madde | Kanıt |
|---|-------|-------|
| G6.1 | Kontrast: normal metin ≥ 4.5:1 · büyük metin ≥ 3:1 · bileşen sınırı ≥ 3:1 (1.4.11) | §12.1 hesabı |
| G6.2 | Görünür focus outline ≥ 3px, token rengi (`--cm-color-focus`) | CSS |
| G6.3 | Tüm işlevler klavye (Enter/Space/A/B + yön); click-behaviour yasak | JS |
| G6.4 | Escape = kapat + odağı tetikleyene iade; odak tuzağı yok (dialog/inert) | JS |
| G6.5 | H1 sayfa başına 1; heading hiyerarşisi çizgi atlamaz | HTML |
| G6.6 | Resimde alt, ikonda `aria-label`, klavye kısayolu dekoratif `aria-hidden` | HTML |
| G6.7 | `.sr-only` sınıfı klavye için `clip` tabanlı, yalnız `position: absolute` değil | CSS |
| G6.8 | `prefers-reduced-motion` tüm animasyon/transition'larda | CSS/JS |
| G6.9 | Hata mesajları `role="alert"` + `aria-describedby` + `aria-invalid` | form |

## G7 — Performance & ES modules

| # | Madde | Kanıt |
|---|-------|-------|
| G7.1 | ES modules (`type="module"`), kod 200 satır + 100 satır fonksiyon | dosya boyu |
| G7.2 | Uzun görevde her 250 ms'de bir `await new Promise(r => setTimeout(r, 0))` | JS |
| G7.3 | Çok-öğeli DOM'da `DocumentFragment` kullanıldı | JS |
| G7.4 | Uzun iş akışında 1000 satır kuralı (400/200 alt eşik) — exceeded ise dosya bölündü | satır sayısı |
| G7.5 | `var`/`any` yok (TS); `async/await` + `try/finally`; `const`/`let` | JS/TS |

## G8 — Kod paketi iskeleti (§9 zorunlu iskelet)

| # | Madde | Kanıt |
|---|-------|-------|
| G8.1 | HTML5 doctype + `lang` + `viewport` + og meta | HTML |
| G8.2 | ITCSS dosya iskeleti (01..11) doğru sırada import edildi | CSS |
| G8.3 | Klasör ve dosya adı özdeşliği 1:1 (mükerrer isim yasak) | dosya ağacı |
| G8.4 | JS ayrı dosya; modül/fonksiyon ayrımı mantıklı | dosya ağacı |
| G8.5 | Orta sürüm hedefi (yaygın JS + CSS özellikleri; yeni API'de fallback/yorum) | JS |

## G9 — Kurallar / yasaklar / guardrail

| # | Madde | Kanıt |
|---|-------|-------|
| G9.1 | Sürümlü `role` yok (`role="button-2"` vb. — ARIA自 only native) | HTML |
| G9.2 | JS'de `setInterval` yok (uzun süreli — `requestAnimationFrame`/tek seferlik) | JS |
| G9.3 | Işık animasyonu/ikon `aria-hidden="true"` | HTML |
| G9.4 | Carousel `role="group"` + `aria-roledescription="carousel"` (light generic değil) | HTML |
| G9.5 | `defer` yalnız harici `<script>`; `document.write` yok; mobilde `<script>` aşırı inline değil | HTML |
| G9.6 | Export `await import()` yalnız user-initiated; code-split var | JS |
| G9.7 | `translate="no"` yalnız `code`/`kbd`/`samp`/`var` (yayıncı esası: kod, kendi diliniz de) | HTML |

## G10 — Format (N1–N10)

| # | Madde | Kanıt |
|---|-------|-------|
| G10.1 | SKILL.md 500–2000 satır; dizin toplamı ≥ 1000 | satır ölçümü |
| G10.2 | Frontmatter yalnız `name/description/license/metadata` (N2) | head |
| G10.3 | `name` = klasör adı 1:1; description ≤ 1024 + "Use when" + tetikleyiciler | head |
| G10.4 | Body ≥ 6 bölüm; en az 1 kod bloğu/şema/çizelge (N5) | scan |
| G10.5 | Footer = `metadata.version` + Updated (N6); UTF-8 BOM'suz (N7) | tail |
| G10.6 | Orphan = 0: her reference/example SKILL.md'den linkli (N4) | link taraması |
| G10.7 | `CLAUDE.md` sidecar YOK (N8) | dosya yok |
| G10.8 | `references/checklist.md` var ve SKILL.md'den linkli | dosya |

---

## Çıktı şablonu

```
GATE: PASS|BLOCK
- G1: PASS (4/4) — onay kaydı: ...
- G2: BLOCK — G2.2: a-pages/p-home.css:14 ham #000
- ...
Kanıt: dosya:satır
```
