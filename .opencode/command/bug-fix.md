---
description: "Mevcut bug'lari duzelt, hata kokenu analiz et, cozum uygula"
agent: build
---

# Bug Fix Komutu

Mevcut hatalari sistematik olarak bulur ve duzeltir.

## Nasil Calisir?

1. Hatayi analiz et (stack trace, log, kullanici raporu)
2. Kok nedeni bul (root cause analysis)
3. Cozum plani olustur
4. Kodu duzelt
5. Test et
6. Log yaz

## Kullanim

```
/bug-fix [hata aciklamasi]
```

## Analiz Adimlari

```
ADIM 1: Hatayi Anla
  → Hata mesajini oku
  → Stack trace'i analiz et
  → Log dosyalarini kontrol et

ADIM 2: Kok Nedeni Bul
  → Neden olusuyor?
  → Hangi kosullarda tetikleniyor?
  → Daha once olmus mu?

ADIM 3: Cozum Planla
  → Kisa vadeli fix (hotfix)
  → Uzun vadeli cozum (kalici)
  → Test senaryolari

ADIM 4: Kodu Duzelt
  → Degisiklik yap
  → Mevcut testleri calistir
  → Yeni test ekle (gerekirse)

ADIM 5: Dogrula
  → Hata tekrar olusuyor mu?
  → Regression var mi?
  → Performans etkilendi mi?

ADIM 6: Log Yaz
  → .ai/log.md'ye ekle
  → Commit mesaji yaz
```

## Vault Context

- `.ai/brain.md` — Bilinen hatalar
- `.ai/decisions/` — Ilgili ADR'ler
- `.claude/rules/` — Kod standartlari

## Red Team Kontrolu

- Fix really solve the problem?
- Any side effects?
- Tests pass?
