---
title: "ui-analyzer Reference — ITCSS / BEM / Token Denetimi"
type: reference
version: 4.0.0
updated: 2026-10-21
---

# ITCSS · BEM · `--cm-*` Token Denetimi

`SKILL.md` §9'un derinlik dosyası (orphan=0). Kaynaklar: kök `AGENTS.md` §10 (CSS
Writing Rules) · `.ai/.templates/frontend/css-template.md` (v4.0.0, 11 katman) ·
`.ai/ui-design/02-component-inventory.md` · `.ai/CLAUDE.md` §7.1.

---

## 1. İhlal (violation) vs Öneri (suggestion)

| | İHLAL | ÖNERİ |
|---|-------|-------|
| Tanım | Bağlayıcı bir kural/şablon/mockup ölçüsü çiğnendi | Bağlayıcı olmayan iyileştirme |
| Kaynak | kök AGENTS.md §10 · Guardrail #11 · frontend şablonları · token/ASCII ölçüsü | modern CSS önerileri, perf ipuçları |
| Rapor yeri | §8 "Tespit Edilen Sorunlar" + severity + kanıt | §8 "Öneriler" — severity PUANLAMAZ |
| Aksiyon | Düzeltme = `ui-code-generator` + onay | İstek bağlı, backlog |

**Kanıt kuralı (her iki tür için de):** bulgu = Ölçüm + Kanıt (dosya:satır VEYA
piksel/ölçü) + Beklenen + Gerçek. Kanıtsız iddia `⚠️ VERIFICATION REQUIRED` olarak
etiketlenir; bulgu sayılmaz, puana girmez (−5 puan cezası ayrı uygulanır).

---

## 2. ITCSS Katman Kriterleri

**CoreMusic ITCSS = 11 katman** (`.ai/.templates/frontend/css-template.md` v4.0.0) —
sıra değiştirilemez, taşınan dosya yoktur:

```text
01_Abstracts → 02_Base → 03_Layout → 04_Components → 05_Pages → 06_Utilities
→ 07_Vendors → 08_Devices → 09_ViewModes → 10_Helpers → 11_OAuth  (+ auth-bundled.css ayrı root)
```

> "9 katman" iddiası vault'ta `⚠️ VERIFICATION REQUIRED` işaretlidir — sayım iddiası
> yalnız şablon dosyasından okunur, hafızadan yazılmaz.

| # | Kriter | İhlal sayılan durum | Severity |
|---|--------|----------------------|----------|
| 1 | Sabit değer (renk/boyut) yalnız `01_Abstracts` içinde `--cm-*` olarak tanımlanır | Başka katmanda sabit değer tanımı | HIGH |
| 2 | `02_Base`…`11_OAuth` katmanlarında ham hex/px YAZILMAZ; her değer `var(--cm-*)` ile okunur | Katmanda ham `#hex` / `Npx` (renk/boşluk) | HIGH |
| 3 | `@media` içinde `var()` okuma YASAK — kırılım değeri px sabit, yalnız iç gövde `var()` kullanır | `@media(...){--x:var(...)}` | HIGH |
| 4 | Dosya adı ve import zinciri değiştirilmez; yanlış yerleşim yalnız raporlanır | (taşıma/silme önerisi bile onaysız) | — (yasak eylem) |
| 5 | Katman sırası bozulmaz: alt katman üst katmana geri bağımlı değil, Utilities en sonda | Components içinde global reset davranışı vb. | MEDIUM |
| 6 | `@layer` sırası ITCSS akışına uyar (varsa) | Sıra kayması | MEDIUM |
| 7 | Ham hex/px yalnız token dosyasında; `@media` içinde `var()` okumada ham değer yazılmaz | Şablon ihlali | HIGH |

**Token kuralı özeti:** sabit değer yalnız `01_Abstracts/a-*.css` içinde `--cm-*` ile;
`@media` içinde `var()` okuma yasak; ham hex/px katman dosyasına yazılmaz; **taşıma/silme
YOK** (yalnız raporla, onay + ADR).

---

## 3. BEM Kriterleri

| # | Kriter | İhlal sayılan durum | Severity |
|---|--------|----------------------|----------|
| 1 | Sınıf adları inventory C01-C16 ile **birebir** eşleşir | Inventory dışı ad (ör. `cm-card` yerine `home-card`) | MEDIUM |
| 2 | `block__element--modifier` biçimi: tek alt çizgi `__` (element), çift `--` (modifier) | `cm-card_title` / `cm-card--big--red` biçimi bozuk | MEDIUM |
| 3 | Block ön eki proje konvansiyonuna uyar (`cm-*` / `c-*` blok örnekleri şablonda) | Karışık block adlandırma | MEDIUM |
| 4 | Bileşen sınıfları kendi bileşen katmanında; utility sınıfı bileşen içinde üretilmez | bileşen içinde `.u-*` tanımı | LOW |
| 5 | Inline style YASAK — her değer token/şablon üzerinden | `style="color:#..."` | HIGH |

**Ad değişikliği = wiki/DOM etkisi taşır → raporlanır, düzeltilmez (onay + ADR gerekir).**

---

## 4. `--cm-*` Token Kriterleri

1. **Token yalnız 01_Abstracts'ta üretilir.**
2. **Katman dosyalarına ham değer yazılmaz** — her değer `var(--cm-*)` ile okunur.
3. **`@media` içinde `var()` okuma yasak** (kök `AGENTS.md` §10).
4. **Taşıma/silme YOK** — yanlış yerleşim yalnız raporlanır.
5. **Ad konvansiyonu:** `--cm-*` (ör. `--cm-accent-primary`); `--color-primary` vb. ihlal adayı.
6. **Ölçüm token'ları:** `--touch-min: 48px` (WCAG 2.5.8'in 24px'inden katıdır; 24px'e
   düşürme YASAK) · odak outline 3px · `--btn-h`/`--row-h`/`--tab-h` proje eşikleri.

### Sapma sınıflandırması

| Severity | Sapma | Aksiyon |
|----------|-------|---------|
| HIGH | Katmanda ham hex/px (renk/boşluk) + token mevcut | Raporla — taşıma onaysız YASAK |
| HIGH | `@media` içinde `var()` | Raporla (computed style belirsizleşir) |
| MEDIUM | Token adı `--cm-*` konvansiyonuna uymuyor | Raporla |
| LOW | Token tanımlı ama kullanılmıyor (dead token) | Raporla, silme YASAK |

### Kanıt formatı

```text
[SAPMA-NN] {dosya}:{satır} — ham değer: {değer}; beklenen: var(--cm-...)
  Severity: {…} · Kaynak kural: kök AGENTS.md §10
```

---

## 5. Salt-Okunur Tarama Komutları

Yollar proje diskine göre uyarlanır; CSS dizini diskte yoksa DUR + `UNKNOWN`
(yol uydurulmaz). Tarama **değiştirmez** — sonuç satırı rapora kanıt olarak girer.

```powershell
# Katman dosyalarında ham hex (01_Abstracts DIŞINDA) — sonuç: 0 beklenir
Get-ChildItem .\assets.coremusic.net\Css -Recurse -Filter *.css |
  Where-Object { $_.FullName -notmatch '01_Abstracts' } |
  Select-String -Pattern '#[0-9a-fA-F]{3,8}\b'

# Katman dosyalarında ham px (font-size/media hariç incelenir)
Select-String -Path .\assets.coremusic.net\Css\*.css -Pattern '\d+px'

# Token tanımının 01_Abstracts içinde olduğunu doğrula
Select-String -Path .\assets.coremusic.net\Css\01_Abstracts\*.css -Pattern '--cm-'

# @media içinde var() araması (kural 3 ihlali adayı)
Select-String -Path .\assets.coremusic.net\Css\*.css -Pattern '@media[^{]*\{[^}]*var\('

# Inline style taraması (BEM kriter 5)
Select-String -Path .\*.html,.\*.php -Pattern 'style\s*=\s*"[^"]*#[0-9a-fA-F]{3,8}'
```

**Yorum kuralı:** Ham hex bulunduğunda önce token'ın mevcut olup olmadığına bakılır —
token varsa HIGH; token hiç yoksa `⚠️ VERIFICATION REQUIRED` (token eksikliği mi,
kasıtlı inline değer mi belirsizdir) + kullanıcıya sorulur.

---

## 6. Devir

- Düzeltme kodu → `ui-code-generator` (satır edit; taşıma/silme onay + ADR).
- Derin WCAG denetimi → `ui-code-generator/references/wcag-2.2-checklist.md` + `accessibility`.
- Canlı DOM ölçümü → `browser-testing-with-devtools` (read-only).

**Bu skill analiz eder; düzeltme/taşıma/yazma yapmaz.**

*CoreMusic Skill v3.0 — metadata.version: 4.0.0 — Updated: 2026-10-21*
