# ADR-005 — pnpm Monorepo ve Katman Bağımlılığı

- **Durum:** Kabul
- **Tarih:** 2026-10-16
- **Alan:** Mimari

## Karar

pnpm workspace monorepo. Paketler ve **tek yön** bağımlılık:

```
cli / api → search → (ai, cache, workers) → (youtube, database) → common
```

Alt katman üst katmanı tanımaz; her paket `src/index.ts` üzerinden konuşur.

## Gerekçe

- Değişiklik izolasyonu + net sahiplik
- Ortak tipler (`Video`, `SearchQuery`) tek yerde: `packages/common`
- Testler paket sınırlarını takip eder (vitest alias'ları aynı haritayı kullanır)

## Sonuçlar

- Yeni paket = bu haritaya yerleşir; döngüsel bağımlılık yasak
- `verbatimModuleSyntax` + `strict` TS — tip güveni derleme anında
