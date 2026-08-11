# Test Run

Testleri çalıştır, coverage raporu oluştur, başarısız testleri analiz et.

## Test Tipleri

| Tip | Komut | Kapsam |
|-----|-------|--------|
| Unit (PHP) | `vendor/bin/phpunit` | Backend unit testleri |
| Unit (JS) | `npx vitest run` | Frontend unit testleri |
| E2E | `npx playwright test` | Tarayıcı testleri |
| Coverage | `vendor/bin/phpunit --coverage-html` | Kapsama raporu |

## Coverage Hedefleri

| Modül | Minimum | Hedef |
|-------|---------|-------|
| Backend (PHP) | ≥%80 | ≥%90 |
| Frontend (JS) | ≥%80 | ≥%90 |
| Audio Engine (C++) | ≥%80 | ≥%90 |
| Download Service | ≥%80 | ≥%90 |

## Adımlar

```
ADIM 1: Test Türünü Seç
  → PHP testleri için PHPUnit
  → JS testleri için Vitest
  → E2E testleri için Playwright

ADIM 2: Testleri Çalıştır
  → Seçilen test sürücüsünü başlat
  → Sonuçları topla

ADIM 3: Coverage Analizi
  → Kapsama oranını hesapla
  → Minimum %80 eşini kontrol et

ADIM 4: Başarısız Testleri Analiz Et
  → Hata mesajlarını oku
  → Stack trace analizi yap
  → Kök nedeni bul

ADIM 5: Rapor Oluştur
  → Sonuclari listele
  → Önerileri belirt
```

## Test Piramidi

```
        E2E (%10)
       ┌─────────┐
      Integration (%20)
     ┌───────────────┐
    Unit (%70)
   ┌─────────────────────┐
```
