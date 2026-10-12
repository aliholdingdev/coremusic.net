---
title: "ui-analyzer Reference — Token Sapma Kontrolü"
type: reference
category: skills
version: 1.0.0
updated: 2026-10-07
---

# Token Sapma Kontrolü (`--cm-*`)

Kaynağı: kök `AGENTS.md` §10 (CSS Writing Rules) + `.ai/.templates/frontend/` kuralları.

---

## 1. Bağlayıcı Kurallar

1. **Token yalnız 01_Abstracts'ta üretilir:** Sabit değer (renk/boyut) yalnız
   `01_Abstracts/a-*.css` içinde `--cm-*` adıyla tanımlanır.
2. **Katman dosyalarına ham değer YAZILMAZ:** `02_Base`…`11_OAuth` katmanlarında ham
   hex/px yasak — her değer `var(--cm-*)` ile okunur.
3. **`@media` içinde `var()` okuma yasak** (kök AGENTS §10): media query kırılım
   DEĞERİ sabit (px) olmalı; yalnız iç gövdeler `var()` kullanır.
4. **Taşıma/silme YOK:** CSS dosya adı ve import zinciri değiştirilmez; yanlış yerleşim
   yalnız **raporlanır** (onay + ADR olmadan taşıma yasak).

---

## 2. Tarama (sapma = ihlal adayı)

```powershell
# Katman dosyalarında ham hex (01_Abstracts DIŞINDA) — sonuç: 0 beklenir
Get-ChildItem .\assets.coremusic.net\Css -Recurse -Filter *.css |
  Where-Object { $_.FullName -notmatch '01_Abstracts' } |
  Select-String -Pattern '#[0-9a-fA-F]{3,8}\b'

# Katman dosyalarında ham px (özel durumlar hariç — font-size/media hariç incelenir)
Select-String -Path .\assets.coremusic.net\Css\*.css -Pattern '\d+px'

# Token tanımının 01_Abstracts içinde olduğunu doğrula
Select-String -Path .\assets.coremusic.net\Css\01_Abstracts\*.css -Pattern '--cm-'
```

*(Yollar proje diskine göre uyarlanır — `[VERIFY]` diskte css dizini yoksa DUR.)*

---

## 3. Sapma Sınıflandırması

| Severity | Sapma | Aksiyon |
|----------|-------|---------|
| HIGH | Katmanda ham hex/px (renk/boşluk) + token mevcut | Raporla — taşıma onaysız YASAK |
| HIGH | `@media` içinde `var()` | Raporla (computed style belirsizleşir) |
| MEDIUM | Token adı `--cm-*` konvansiyonuna uymuyor | Raporla (ör. `--color-primary`) |
| LOW | Token tanımlı ama kullanılmıyor (dead token) | Raporla, silme YASAK |

**Kural:** Bu skill **analiz** eder; düzeltme/taşıma `ui-code-generator` + onaydır.

---

## 4. Kanıt Formatı

```text
[SAPMA-NN] {dosya}:{satır} — ham değer: {değer}; beklenen: var(--cm-...)
  Severity: {…} · Kaynak kural: kök AGENTS.md §10
```
