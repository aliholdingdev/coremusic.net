---
name: context-report
description: Proje baglam raporu uretir — /context komutunu destekler. Triger kelimeler: context, baglam raporu, durum raporu, proje durumu, /context.
---

# context-report — Proje Baglam Raporu

## Kurallar
- **Salt-okunur:** kod, config veya vault dosyasi YAZMAZ.
- **Zero-hallucination:** okunmayan icerik uydurulmaz, "OKUNAMADI: <yol>" yazilir.
- Rapor **max ~150 satir**, ASCII Turkce markdown.
- Dosya yolu bulunamazsa da ayni kural gecerli: `OKUNAMADI: C:\www\coremusic.net\.ai\<dosya>`

## Okuma Sirasi (zorunlu, sirayla)
1. `C:\www\coremusic.net\CLAUDE.md`
2. `.ai\CLAUDE.md`
3. `.ai\AGENTS.md`
4. `.ai\brain.md` — ilk 100 satir + son 150 satir (ortasi atlana bilir)
5. `.ai\WORKFLOW.md`
6. `.ai\MEMORY.md` — son kayitlar (son ~50 satir)
7. `.ai\.rules\senior-mode.md`

Eger bir dosya OKUNAMAZSA: bolumde `OKUNAMADI: <yol>` yaz, diger dosyalara DEVAM ET (tek dosya raporu durdurmaz).

## Rapor Formu — 7 Bolum (sirasi degistirilmez)
```
## AKTIF AGENT DURUMU
- default agent, aktif rol, dispatch tablosundan dikkat cekilenler

## ANAHTAR KARARLAR
- en kritik 10 ADR (numara + 1 satir ozet, kaynak: .ai/brain.md / ADR dosyalari)

## PROJE DURUMU
- faz, tamamlanan / devam eden isler (kaynak: MEMORY.md, WORKFLOW.md)

## ACIK SORULAR
- cevapsiz veya "VERIFICATION REQUIRED" isaretli maddeler

## YENI EKLENENLER
- son commit / son vault degisikliklerinde one cikanlar

## RISKLER
- teknik borc, kirik referanslar, eksik test, guncel kalmayan dokuman

## NEXT STEP
1. <aksiyon 1>
2. <aksiyon 2>
3. <aksiyon 3>
```

## Ic Kullanim
- Ayni bilgi ikinci kez istenirse kisa surum (max 60 satir) uret, bolum sirasini koru.
- Raporu dosyaya yazmak istenmedikce sadece cevap icinde ver.
