---
title: "prompt-maker Example — Soru Bankası (references/02) Örnek Görevde Uygulama"
type: example
category: skills
version: 1.0.0
updated: 2026-10-07
---

# Örnek: Soru Bankası Görev Üzerinde Nasıl Uygulanır?

Bu örnek, `references/02-question-bank.md` (3.820+ soru, 78 kategori) bankasının **tek bir
örnek görev** üzerinde nasıl süzüldüğünü gösterir: banka statik questionnaire değildir —
görev bağlamına göre ilgili kategoriler seçilir, P0/P1/P2'ye dönüştürülür.

---

## 1. Örnek görev

```text
"kullanıcı: studio panelinde çok kanallı kayıt oturumu olsun, take'ler saklansın"
```

Domain: `studio.coremusic.net` paneli · PHP 8.4 backend · `coremusic_studio` DB · C++20 audio
engine ile dosya I/O sınırı.

---

## 2. Bankadan seçilen kategoriler (Block A — Foundation & Theory)

| Banka kategorisi | Seçilen soru (bankadan) | Görevdeki karşılığı |
|------------------|--------------------------|---------------------|
| **A1: System Prompts** | "What core rules should be non-negotiable?" | Hard rules: ORM yasak, SELECT * yasak, sessiz dosya yazımı (lock-free) |
| **A1: System Prompts** | "How to structure behavioral constraints?" | Oturum durum makinesi: arm → record → stop → bounce |
| **A2: Prompt Types & Categories** | "When to use SYSTEM_PROMPT vs DOMAIN_RULES?" | Panel kuralları system prompt'a; stüdyo iş kuralları domain rules'a |
| **A3: Language & Communication** | "When to use examples vs abstract rules?" | Take isimlendirme şablonu örnek ver, kuralı değil |
| **A4: Quality Metrics & Scoring** | "What are the 8-dimension scoring criteria?" | Çıktı prompt'u §8 kalite kontrolüne tabi (≥85/100) |
| **A5: Architecture & Design Patterns** | "What's a good prompt architecture?" | 15 bölümlük master prompt iskeleti |
| **A6: Tools & Frameworks** | Prompt yönetim aracı soruları | `.ai/prompts/` arşivi + brain.md karar kaydı |

> Bankanın tamamı (78 kategori, blok A-J) `references/02-question-bank.md` içindedir; burada
> yalnız **ilgili** kategoriler süzülür — bankayı baştan okumak boot'ta YASAK (yalnız ihtiyaç anında).

---

## 3. Süzme → P0/P1/P2 (görev bağlamına göre, generic değil)

| Seviye | Soru | Banka kökeni |
|--------|------|--------------|
| **P0** | Ses dosyası nereye yazılır — DB (metadata) + filesystem (WAV/FLAC) ayrımı ne? Kimlik nasıl? | A1 non-negotiable rules |
| **P0** | Çok kanal senkronizasyonu kimde — C++20 audio engine (9741) mi, PHP mi sadece orkestratör? | A2 prompt type (domain sınırı) |
| **P0** | Oturum durum makinesi: kayıt sırasında panel kapanırsa ne (crash recovery / journal)? | A1 behavioral constraints |
| **P1** | Take'ler versiyonlanır mı (take 1, take 2) yoksa append mi? Silme soft-delete mi? | A5 architecture patterns |
| **P1** | Eşzamanlılık: iki kullanıcı aynı stüdyo oturumuna girerse (lock/kilit)? | A5 architecture patterns |
| **P1** | Kalite eşiği: prompt çıktısı 8 kategoride nasıl puanlanacak (Security ≥90)? | A4 quality metrics |
| **P2** | Take adı otomatik ("take-2026-10-07-01") mi, kullanıcı mı yazar? | A3 examples vs rules |
| **P2** | Arşivleme: eski oturumlar restic backup kapsamına girsin mi? | A6 tools & frameworks |

---

## 4. Uygulama çıktısı

1. P0'lar **engelleyici** → cevaplanmadan prompt üretilmez (questions modu devrede).
2. Cevaplar geldikten sonra 15 bölümlük master prompt üretilir (§ 5 şablon özeti).
3. Puanlama: `references/09-quality-scoring-rubric.md` + `references/validation-engine.md`.
4. Kapanış satırı: `✅ Prompt Cevaplarla İşlendi | Dil: Türkçe | Görev Sayısı: [n] | Cevap: [n]`

**Ders:** Banka = kaynak havuzu; her görev için bankanın tamamı değil, **ilgili kategori**
sezilir ve proje bağlamına çevrilir (generic soru üretimi YASAK — § Zorunlu Akış A.2).