---
title: "dynamics — Örnekler"
type: architecture-examples
node_id: K004-dsp/dynamics
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# dynamics — Örnekler

## §1 Gerçek repo kodu (CoreMusic)

**Durum: — yok.** `shared/src/` altında ses dinamiği (compressor/limiter/envelope)
koduna ait C++ veya PHP bulunmamaktadır. Eşleşen `RateLimiter`/`CacheRateLimiter`
HTTP request rate limiting'dir, ses sinyali dinamiği değildir — yanıltıcı eşleşme,
dahil edilmez. Uydurma kod yazılmaz.

## §2 Resmi doküman / bibliyotek örneği

**Kaynak:** https://github.com/CristianMoresi/DSPark (README — Capabilities, 2026-10-12 ✅)

```
Dynamics and level control | Compressor, Limiter, NoiseGate, Expander,
                            DynamicEQ, MultibandCompressor, DeEsser
```

**Ne gösterdiği:** "dynamics" ailesinin sınırları — tek çatı altında compressor,
gate, expander, limiter, dinamik EQ, multiband, de-esser. CoreMusic `dynamics`
dalının sınıflandırmasına doğrudan referans (R2/R3 açık sorularını besler).

**Kaynak:** https://github.com/Signalsmith-Audio/basics (README, 2026-10-12 ✅)

```
- Dynamics (compressor/expander/gate)
- Limiter
```

**Ne gösterdiği:** MIT lisanslı, temel efekt kütüphanesinde aynen "Dynamics" adıyla
ayrı bir başlık — compressor/expander/gate'i bir arada, limiter'ı ayıran gruplama.

**Kaynak:** https://github.com/ShmKnd/Patina (Effect Engines, 2026-10-12 ✅)
`CompressorEngine` (opto/FET/variable-mu/VCA) ve `LimiterEngine` (FET/VCA/opto)
— analog-modeling aile karşılıkları.

## §3 Uygulama notu

Üç referans da compressor/limiter'ı **ayrı** birim olarak sunar (v2 mini-ağaçtaki
kardeş düzenle uyumlu). Q3 (hiyerarşi↔kardeş) resmen kapanmadan bu gözlemsel
eşleşme bağlayıcı hiyerarşi çizmez; yalnız tasarım desteğidir.

## §4 İlişkili dosyalar

| Dosya | İlişki |
|---|---|
| `[[dynamics-kapsam]]` | şemsiye sınırı ve Q3 |
| `[[dynamics-dogrulama]]` | iddia kanıtları |
| `../compressor/compressor-kapsam.md` | aile üyesi (kardeş) |
| `../limiter/limiter-kapsam.md` | aile üyesi (kardeş) |
| `../measurement/measurement-kapsam.md` | dB/seviye takibi ölçüm sınırı |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
