---
title: "K011 · usecase — Index"
type: architecture
kodeme: kademe-2
module: K011-application
parent: usecase
status: active
updated: 2026-10-15
group: mimari
---

# K011 · usecase — Katalog

> UseCase katmanı 6 dosyalık MD seti. Format referansı: `commands/` klasörü.
> `usecase-usecase-katmani.md` (527 satır) korunur — scope slotu, dokunulmadı.
> Eski `usecase-kapsam.md` 2026-10-16'da spec §7 gereği kaldırıldı; öz
> içeriği kardeş dosyalara taşındı (ölçümler → dogrulama §B, transaction →
> §D/§Y, UseCase=0 bulgusu → §I, PSR → web-arastirma).

## §GRUPLAR

| Bölüm | Konu |
|---|---|
| §A | Dosya kataloğu (6 dosya) |
| §B | OKUMA SIRASI (boot order) |
| §C | Kapılar (gate özeti) |
| §D | Cross-reference (diğer K011 klasörleri) |
| §E | Kapsam sınırları |

---

## §A — DOSYA KATALOĞU

| # | Dosya | Group | Satır | Rol |
|---|---|---|---|---|
| 1 | usecase-usecase-katmani.md | mimari | 527 | Scope slotu — UseCase katmanı tanımı (korunur) |
| 2 | usecase-dogrulama.md | mimari | 513 | Ölçüm tabloları, gate matrisi (G1–G7), UseCase=0 bulgusu |
| 3 | usecase-web-arastirma.md | kanit | 508 | PSR-17/PSR-11 (2 URL havuzu, dotted) |
| 4 | usecase-ornekler.md | kod | 517 | PHP/JS/SQL kod blokları — Dosya·Satır kanıtlı |
| 5 | usecase-bilesenler.md | mimari | 502 | Handler/Service/Transaction envanteri |
| 6 | index.md | mimari | 40–100 | Bu katalog |

**Toplam dosya:** 6 · **Kapsam devri:** eski `usecase-kapsam.md` (2026-10-16,
spec §7) — ölçümleri dogrulama §B'ye, transaction/UnitOfWork §D/§Y'ye,
UseCase sınıfı=0 bulgusu §I'ye, PSR-11/17 web-arastirma'ya taşındı.

---

## §B — OKUMA SIRASI (Boot Order)

```
1. index.md                        (bu dosya — katalog)
2. usecase-usecase-katmani.md      (scope slotu — katman tanımı)
3. usecase-dogrulama.md            (ölçüm + gate + UseCase=0 bulgusu)
4. usecase-web-arastirma.md        (dış kanıtlar — 2 URL)
5. usecase-ornekler.md             (kod kanıtları)
6. usecase-bilesenler.md           (envanter + olgunluk)
```

---

## §C — KAPILAR (Gate Özeti)

| Gate | Karar | Sonuç |
|---|---|---|
| G1 | Dosya seti 6/6 | ✅ |
| G2 | Frontmatter 8 alan (group: son) | ✅ |
| G3 | GRUPLAR tablosu her içerik dosyasında | ✅ |
| G4 | Footer (Authority + Last Updated) | ✅ |
| G5 | URL havuzu ≤4, dotted | ✅ 2/2 |
| G6 | Dosya·Satır kanıtı | ✅ ornekler + web-arastirma |
| G7 | `*UseCase*.php` dosyası var mı? | ⚠️ **0** — üretimde UseCase sınıfı yok (dogrulama §I) |

---

## §D — CROSS-REFERENCE (Diğer K011 Klasörleri)

| Konu | Klasör |
|---|---|
| DTO sözleşmesi | `../dto/` |
| Command/Query ayrımı | `../command-query/` |
| Routing dispatch | `../routing/` |
| Senaryo akışları (PageRouter) | `../usecase-akislari/` |

---

## §E — KAPSAM SINIRLARI

| IN | OUT |
|---|---|
| shared/src/ UseCase/Handler/Service envanteri | Diğer K011 klasörleri |
| PSR-11 (container) + PSR-17 (factory) doğrulaması | Kaynak kod değişikliği (.php/.js/.css/.sql) |
| 6 dosyalık MD seti | git commit (orkestratöre ait) |
| 2 URL havuzu (dotted) | Havuz dışı URL uydurma |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
