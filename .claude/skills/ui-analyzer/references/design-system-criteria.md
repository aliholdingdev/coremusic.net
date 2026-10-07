---
title: "ui-analyzer Reference — Tasarım Sistemi İhlal Kriterleri"
type: reference
version: 3.0.0
updated: 2026-10-07
---

# Tasarım Sistemi İhlal Kriterleri (ITCSS · BEM · `--cm-*`)

Kaynaklar: kök `AGENTS.md` §10 (CSS Writing Rules) · `.ai/.templates/frontend/` ·
`.ai/ui-design/02-component-inventory.md` · `.ai/CLAUDE.md` §12 (ITCSS + BEM, 9-layer).

---

## 1. İhlal (violation) vs Öneri (suggestion)

| | İHLAL | ÖNERİ |
|---|-------|-------|
| Tanım | Bağlayıcı bir kural/şablon/mockup ölçüsü çiğnendi | Bağlayıcı olmayan iyileştirme |
| Kaynak | kök AGENTS.md §10 · Guardrail #11 · frontend şablonları · token/ASCII ölçüsü | modern CSS önerileri, perf ipuçları |
| Rapor yeri | §8 "Tespit Edilen Sorunlar" + severity + kanıt | §8 "Öneriler" — severity PUANLAMAZ |
| Aksiyon | Düzeltme = ui-code-generator + onay | İstek bağlı, backlog |

**Kanıt kuralı (her iki tür için de):** bulgu = Ölçüm + Kanıt (dosya:satır VEYA
piksel/ölçü) + Beklenen + Gerçek. Kanıtsız iddia `⚠️ VERIFICATION REQUIRED` olarak
etiketlenir; bulgu sayılmaz, puana girmez.

---

## 2. ITCSS 9-Katman Kriterleri

CoreCSS `.ai/.templates/frontend/` şablonu 01→11 sırasıyla yerleşir (token yalnız 01).

| # | Kriter | İhlal sayılan durum | Severity |
|---|--------|----------------------|----------|
| 1 | Sabit değer (renk/boyut) yalnız `01_Abstracts` içinde `--cm-*` olarak tanımlanır | Başka katmanda sabit değer tanımı | HIGH |
| 2 | `02_Base`…`11_OAuth` katmanlarında ham hex/px YAZILMAZ; her değer `var(--cm-*)` ile okunur | Katmanda ham `#hex` / `Npx` (renk/boşluk) | HIGH |
| 3 | `@media` içinde `var()` okuma YASAK — kırılım değeri px sabit, yalnız iç gövde `var()` kullanır | `@media(...){--x:var(...)}` | HIGH |
| 4 | Dosya adı ve import zinciri değiştirilmez; yanlış yerleşim yalnız raporlanır | (taşıma/silme önerisi bile onaysız) | — (yasak eylem) |
| 5 | Katman sırası bozulmaz: alt katman üst katmana geri bağımlı değil, Utilities en sonda | Components içinde global reset davranışı vb. | MEDIUM |
| 6 | `@layer` sırası ITCSS akışına uyar (varsa) | Sıra kayması | MEDIUM |

**Tarama (sapma = ihlal adayı, salt-okunur):**

```powershell
# Katman dosyalarında ham hex (01_Abstracts DIŞINDA) — sonuç: 0 beklenir
Get-ChildItem .\assets.coremusic.net\Css -Recurse -Filter *.css |
  Where-Object { $_.FullName -notmatch '01_Abstracts' } |
  Select-String -Pattern '#[0-9a-fA-F]{3,8}\b'

# Katman dosyalarında ham px (font-size/media hariç incelenir)
Select-String -Path .\assets.coremusic.net\Css\*.css -Pattern '\d+px'

# Token tanımının 01_Abstracts içinde olduğunu doğrula
Select-String -Path .\assets.coremusic.net\Css\01_Abstracts\*.css -Pattern '--cm-'
```

*(Yollar proje diskine göre uyarlanır — css dizini diskte yoksa DUR + UNKNOWN.)*

---

## 3. BEM Kriterleri

| # | Kriter | İhlal sayılan durum | Severity |
|---|--------|----------------------|----------|
| 1 | Sınıf adları inventory C01-C16 ile **birebir** eşleşir | Inventory dışı ad (ör. `cm-card` yerine `home-card`) | MEDIUM |
| 2 | `block__element--modifier` biçimi: tek alt çizgi `__` (element), çift `--` (modifier) | `cm-card_title` / `cm-card--big--red` biçimi bozuk | MEDIUM |
| 3 | Bileşen sınıfları kendi bileşen katmanında; utility sınıfı bileşen içinde üretilmez | bileşen içinde `.u-*` tanımı | LOW |
| 4 | Inline style YASAK — her değer token/şablon üzerinden | `style="color:#..."` | HIGH |

Ad değişikliği = wiki/DOM etkisi taşır → **raporlanır, düzeltilmez** (onay + ADR gerekir).

---

## 4. `--cm-*` Token Kriterleri

1. **Token yalnız 01_Abstracts'ta üretilir.**
2. **Katman dosyalarına ham değer yazılmaz** — her değer `var(--cm-*)` ile okunur.
3. **`@media` içinde `var()` okuma yasak** (kök AGENTS §10).
4. **Taşıma/silme YOK** — yanlış yerleşim yalnız raporlanır (onay + ADR olmadan taşıma yasak).
5. **Ad konvansiyonu:** `--cm-*` (ör. `--cm-accent-primary`); `--color-primary` vb. ihlal adayı.

**Sapma sınıflandırması:**

| Severity | Sapma | Aksiyon |
|----------|-------|---------|
| HIGH | Katmanda ham hex/px (renk/boşluk) + token mevcut | Raporla — taşıma onaysız YASAK |
| HIGH | `@media` içinde `var()` | Raporla (computed style belirsizleşir) |
| MEDIUM | Token adı `--cm-*` konvansiyonuna uymuyor | Raporla |
| LOW | Token tanımlı ama kullanılmıyor (dead token) | Raporla, silme YASAK |

**Kanıt formatı:**

```text
[SAPMA-NN] {dosya}:{satır} — ham değer: {değer}; beklenen: var(--cm-...)
  Severity: {…} · Kaynak kural: kök AGENTS.md §10
```

**Bu skill analiz eder; düzeltme/taşıma `ui-code-generator` + onaydır.**