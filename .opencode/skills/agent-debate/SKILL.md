---
name: agent-debate
description: Cok-ajan tartisma protokolu — /debate komutunu destekler. Triger kelimeler: debate, tartisma, multi-agent debate, oylama, karsi bakis acisi, karar tartismasi.
---

# agent-debate — Cok-Ajan Tartisma Protokolu

## Amac
Karar oncesi karsi bakis acisi uretme. 5-10 tur surer. Hedef: kör noktayi, riski ve alternatifi aciga cikarmak. Kod yazma, sadece tartisma yonet.

## Roller
Dispatch tablosundaki uzmanlardan en az 2 KARSI rol sec (minimum 2 pincer):

| Konu | Rol A | Rol B |
|---|---|---|
| API / middleware | backend-architect | security-engineer |
| Veritabani semasi | data-engineer | database-optimizer |
| Frontend | ui-designer | security-engineer |
| Test / kalite | qa-engineer | developer |
| Altyapi | devops-engineer | performance-engineer |

Karsi roller ASLA ayni bakis acisini tasimaz. Gerekirse 3. rol (reviewer) gozlemci olarak ekle.

## Tur Protokolu (her tur icin zorunlu)
1. **Iddia** — Rol A net, tek dogrulanabilir bir iddia soylemeli.
2. **Itiraz** — Rol B ayni iddiaya karsi arguman sunmali ("ama" yetmez, gerekce gerekli).
3. **Kanit** — Her iddia KANITSIZ GECERSIZDIR. Kabul edilen kanit:
   - ADR numarasi (orn. ADR-002, ADR-010, ADR-083)
   - Vault dosya referansi (orn. `.ai/brain.md`, `.ai/.rules/senior-mode.md`, `.ai/index.md`)
4. Tur sonunda iddia durumu: **KANITLI** / **ZAYIF** / **GECERSIZ**.

**Zero-hallucination:** Kanit gorulmeden uretilmis ADR numarasi, dosya yolu veya alinti UYDURMADIR ve iddiayi iptal eder. Emin olunan kanit yerine "KANIT YOK" yaz.

## Oylama
- Her rol tur sonunda: EVET / HAYIR / CEKIMSER.
- Karar = cogunluk.
- **Esitlik (berabere) → L3 insana eskalasyon.** Agent kendi basina karar VERMEZ, sadece secer.
- Guven seviyesi: KANITLI (tum iddialar kanitli) / KISMEN (bir kismi kanitsiz) / DUSUK (esitlik veya cok fazla gecersiz iddia).

## Cikti Formu (ASCII Turkce markdown)
```
## TARTISMA OZETI — <konu>
### IDDIALAR
- [KANITLI] <iddia> — kaynak: ADR-xxx / .ai/<dosya>
- [GECERSIZ] <iddia> — sebep: kanit yok
### OYLAR
- <rol>: EVET/HAYIR/CEKIMSER — gerekce (1 satir)
### KARAR
<sonuc> — guven: KANITLI|KISMEN|DUSUK
### NEXT STEP
1-3 aksiyon
```

## Vault Yazimi
`.ai/` altina yazim gerekiyorsa SADECE:
`node .ai/scripts/vault-utf8-writer.mjs --text "..." — mod: append`
PowerShell ile `.ai/` dosya yazmak YASAK (Windows-1254/BOM/UTF-16 bozulmasi yapar).
