# New Feature

Yeni özellik geliştirme sürecini başlat.

## Çalışma Akışı

```
ADIM 1: İsteği Anla
  → Kullanıcı ne istiyor?
  → Hangi domain (Backend/Frontend/Security)?
  → Kapsam ne kadar geniş?

ADIM 2: Plan Oluştur
  → Teknik tasarım
  → API sözleşmesi (endpoint varsa)
  → Veritabanı değişikliği (gerekirse)
  → ADR oluştur (mimari karar varsa)

ADIM 3: Tasarım Yap
  → UI/UX tasarımı (frontend için)
  → Database schema (data için)
  → API contract (backend için)

ADIM 4: Kodu Yaz
  → ITCSS katmanları (CSS için)
  → Handler → Service → Repository (PHP için)
  → Vanilla ES6+ (JS için)

ADIM 5: Test Et
  → Unit test (minimum %80 coverage)
  → Integration test
  → E2E test (kritik akışlar)

ADIM 6: Dokümantasyonu Güncelle
  → ADR oluştur (mimari karar varsa)
  → API docs güncelle
  → CHANGELOG ekle
```

## Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Zero Code Before Plan | Kod revert edilir |
| 2 | MSA Limit = 15 dosya | Görev parçalanır |
| 3 | ADR uyumluluğu | Mimari çelişki |
| 4 | Layer violation kontrolü | Derhal revert |
| 5 | Test coverage ≥%80 | Red Team review |
