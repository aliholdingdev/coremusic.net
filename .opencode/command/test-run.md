---
description: "Testleri calistir, coverage raporu olustur, basarisiz testleri analiz et"
agent: build
---

# Test Run Komutu

Testleri calistirir ve sonuclari raporlar.

## Nasil Calisir?

1. Test surucusunu sec (PHPUnit/Vitest/Playwright)
2. Testleri calistir
3. Coverage analizi yap
4. Basarisiz testleri analiz et
5. Rapor olustur

## Kullanim

```
/test-run [test tipi]
```

## Test Tipleri

| Tip | Komut | Kapsam |
|-----|-------|--------|
| Unit (PHP) | `vendor/bin/phpunit` | Backend unit testleri |
| Unit (JS) | `npx vitest run` | Frontend unit testleri |
| E2E | `npx playwright test` | Tarayici testleri |
| Coverage | `vendor/bin/phpunit --coverage-html` | Kapsama raporu |

## Coverage Hedefleri

| Modul | Minimum | Hedef |
|-------|---------|-------|
| Backend (PHP) | %80 | %90 |
| Frontend (JS) | %80 | %90 |
| Audio Engine (C++) | %80 | %90 |
| Download Service | %80 | %90 |

## Analiz Adimlari

```
ADIM 1: Test Surucunu Sec
  → PHP testleri icin PHPUnit
  → JS testleri icin Vitest
  → E2E testleri icin Playwright

ADIM 2: Testleri Calistir
  → Tum testleri calistir
  → Basarili/basarisiz sayisini not et
  → Hata mesajlarini topla

ADIM 3: Coverage Analizi
  → Kapsama oranini hesapla
  → Eksik alanlari tespit et
  → Oncelik sırasina koy

ADIM 4: Basarisiz Testleri Analiz Et
  → Hata kokenu bul
  → Fix onerisi yap
  → Regression kontrolu

ADIM 5: Rapor Olustur
  → Ozet: X gecti, Y basarisiz, Z coverage
  → Detay: Hangi testler basarisiz
  → Oneriler: Nasil iyilestirilir
```

## Vault Context

- `.ai/testing/` — Test stratejileri
- `.ai/testing/coverage-targets.md` — Coverage hedefleri
- `.claude/rules/testing-standards.md` — Test standartlari

## Cikti Formati

```markdown
# Test Raporu

## Tarih: [tarih]
## Test Tipi: [unit/integration/e2e]

## Sonuclar
- Toplam: X
- Gecen: X (%Y)
- Basarisiz: X
- Atlanan: X

## Coverage
- Satir: %X
- Fonksiyon: %X
- Branch: %X

## Basarisiz Testler
| Test | Hata | Dosya |
|------|------|-------|
| [test adi] | [hata mesaji] | [dosya] |

## Oneriler
1. [oneri 1]
```
