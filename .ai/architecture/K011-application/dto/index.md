---
title: "K011 · dto — Index"
type: architecture
kodeme: kademe-2
module: K011-application
parent: dto
status: active
updated: 2026-10-15
group: mimari
---

# K011 · dto — Katalog

> DTO katmanı 6 dosyalık MD seti. Format referansı: `commands/` klasörü.
> Scope slotu `dto-veri-tasiyicer-tasarimi.md` (543 satır) korunmuştur — silinmedi,
> yeniden adlandırılmadı.

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
| 1 | dto-dogrulama.md | mimari | 580 | Ölçüm tabloları, gate matrisi (G1–G7), anti-verification, kapsam devri |
| 2 | dto-web-arastirma.md | kanit | 513 | PSR-7, JsonSerializable, Content-Type, 409 (4 URL havuzu) |
| 3 | dto-ornekler.md | kod | 795 | PHP/JS/SQL kod blokları — Dosya·Satır kanıtlı |
| 4 | dto-bilesenler.md | mimari | 518 | 14 DTO envanteri, aile analizi, olgunluk matrisi |
| 5 | index.md | mimari | 40–100 | Bu katalog |
| — | dto-veri-tasiyici-tasarimi.md | (orijinal) | 543 | MEVCUT scope slotu — KAPSAM kanıtı, dokunulmadı |

**Toplam dosya:** 6 · **Kapsam devri:** eski `dto-kapsam.md` spec §7 gereği
2026-10-16 kaldırıldı — içeriği `dto-dogrulama.md` §C.1 / §D.9–§D.12 / §5'e taşındı.

---

## §B — OKUMA SIRASI (Boot Order)

```
1. index.md                 (bu dosya — katalog)
2. dto-dogrulama.md         (ölçüm + gate + kapsam devri)
3. dto-web-arastirma.md     (dış kanıtlar — 4 URL)
4. dto-ornekler.md          (kod kanıtları)
5. dto-bilesenler.md        (envanter + analiz)
6. dto-veri-tasiyici-tasarimi.md  (orijinal kapsam slotu — ihtiyaç halinde)
```

---

## §C — KAPILAR (Gate Özeti)

| Gate | Karar | Sonuç |
|---|---|---|
| G1 | Dosya seti 6/6 | ✅ |
| G2 | Frontmatter 7 alan (group: son) | ✅ |
| G3 | GRUPLAR tablosu her içerik dosyasında | ✅ |
| G4 | Footer (Authority + Last Updated) | ✅ |
| G5 | URL havuzu ≤4, dışına çıkılmadı | ✅ 4/4 |
| G6 | Dosya·Satır kanıtı | ✅ ornekler + web-arastirma |
| G7 | toArray gate 14/14 | ⚠️ **13/14** — LoginRequest (auth) toArray yok |

---

## §D — CROSS-REFERENCE (Diğer K011 Klasörleri)

| Konu | Klasör |
|---|---|
| Application Service / UseCase | `../usecase/` |
| Event taşınması | `../event-akislari/` |
| Middleware doğrulama | `../commands/` (middleware envanteri) · `../usecase-akislari/` (pipeline akışı) |
| Command pattern | `../commands/` (format referansı) |

---

## §E — KAPSAM SINIRLARI

| IN | OUT |
|---|---|
| 11 shared/src/Api/Dto/ dosyası | Diğer K011 klasörleri |
| 3 auth/include/Domain/DTO/ dosyası | Kaynak kod değişikliği (.php/.js/.css/.sql) |
| 6 dosyalık MD seti | git commit (orkestratöre ait) |
| 4 URL havuzu | Havuz dışı URL uydurma |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
