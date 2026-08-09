---
description: "Yeni ozellik/endpoint/bilesen olustur, planla, uygula"
agent: build
---

# New Feature Komutu

Yeni ozellikler, endpoint'ler veya bilesenler olusturur.

## Nasil Calisir?

1. Isteği anla
2. Plan olustur (ADR gerekirse)
3. Tasarim yap
4. Kodu yaz
5. Test et
6. Dokumantasyonu guncelle

## Kullanim

```
/new-feature [ozellik aciklamasi]
```

## Calisma Akisi

```
ADIM 1: Isteği Anla
  → Kullanici ne istiyor?
  → Hangi domain (Backend/Frontend/Security)?
  → Kapsam ne kadar genis?

ADIM 2: Plan Olustur
  → Teknik tasarim
  → API sozlesmesi (endpoint varsa)
  → Veritabanı degisikligi (gerekirse)
  → ADR olustur (mimari karar varsa)

ADIM 3: Tasarim Yap
  → UI/UX tasarimi (frontend icin)
  → Database schema (data icin)
  → API contract (backend icin)

ADIM 4: Kodu Yaz
  → ITCSS katmanlari (CSS icin)
  → Handler → Service → Repository (PHP icin)
  → Vanilla ES6+ (JS icin)

ADIM 5: Test Et
  → Unit test (minimum %80 coverage)
  → Integration test
  → E2E test (gerekirse)

ADIM 6: Dokumantasyon
  → ADR olustur (mimari karar varsa)
  → API dokumantasyonu
  → Kullanici kilavuzu
```

## Vault Context

- `.ai/architecture/` — Mimari referanslar
- `.ai/decisions/` — Mevcut ADR'ler
- `.claude/rules/` — Kod standartlari
- `.ai/ui-design/` — UI tasarim sablonlari

## CoreMusic Kurallari

```
✅ PHP strict_types=1
✅ Vanilla JS (framework YASAK)
✅ PDO Prepared Statements
✅ ITCSS + --cm-* token
✅ OWASP Top 10
✅ WCAG 2.2 AA
✅ ADR olustur (mimari karar varsa)
```
