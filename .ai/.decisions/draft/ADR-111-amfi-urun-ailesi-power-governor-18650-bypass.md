---
title: "Amfi Ürün Ailesi + Power Governor + 18650 Adaptör-Bypass Politikası"
type: decision
id: "111"
category: Electronics
version: 1.0.0
status: draft
debate: PENDING
date: "2026-10-25"
deciders: "Bayram Ali (kullanıcı kararı 2026-10-25) · Vault Steward (kayıt)"
tags: ["amplifier", "product-line", "power-governor", "battery", "class-d", "class-ab"]
authority: "Derived — SSOT: .ai/electronic/amplifier/product-line-requirements.md v1.0.0 (2026-10-25)"
updated: "2026-10-25"
---

# Karar: Amfi Ürün Ailesi + Power Governor + 18650 Adaptör-Bypass Politikası

**Zorunlu Bağlantılar:** [[../../electronic/amplifier/product-line-requirements]] · [[../../electronic/amplifier/index]] · [[../../electronic/amplifier/coding-panel]] · [[index]]

## Status

**draft** — debate PENDING. Onay matrisi (.workflows/adr-creation.md): Donanım → Embedded Engineer + Audio Hardware Engineer · Mimari → Principal Architect · kritik karar → kullanıcı onayı. Frozen serisi (ADR-001→037) ile çelişki YOK · **ADR-089 kapsamı dokunulmaz**.

## Context

1. **Ürün hattı gereksinimi doğdu ama ADR'siz.** Kullanıcı kararı 2026-10-25 (`product-line-requirements.md` v1.0.0): 9 SKU'lu amfi hattı, kanal başına azami güç kuralları, yazılım Power Governor ve 18650+adaptör politikası tek oturumda belirlendi.
2. **Mevcut karar kapsamı dar.** ADR-089 (`accepted/ADR-089-classab-24v.md` — Class AB + 6S LiPo + ±35V Boost) yalnız studio/home **8×50W** SKU'sunu kapsar; yeni hat mono 15W'tan 8+1 500W/kanala uzanır ve ADR-089 kapsamı ihlal edilirdi. **ADR-090** (`accepted/ADR-090-channel-variant-product-family`) kanal varyantı eksenini taşır; watt tavanı, Governor ve batarya politikası bu ADR'de kalmalıdır.
3. **Güç bütçesi kapandı (2026-10-25 güncellemesi).** "1000 W bas kabını ↔ 100 W bas kanalı" çelişkisi kullanıcı açıklamasıyla **kapanmıştır**: 1000 W = kabin rating (dayanım), sürücü 100 W ile çalışır (alt sürüş). Kalan P0: **8+1 topoloji seçimi** (Class AB verim ~%45-55 vs Class D ~%85-92 — `amplifier/index.md` §4).
4. **Pil ömrü/maliyet riski.** Boot'ta otomatik şarj, sürekli adaptör kullanımında gereksiz şarj döngüsüyle 18650 ömrünü kısaltır (kullanıcı gerekçesi: pil ömrü + maliyet + üretim kalitesi + ucuz satış).

## Decision

### (a) 9 SKU'lu ürün hattı + ADR-089 kapsam koruması
Ürün hattı 9 SKU kabul edilir (matris SSOT: `product-line-requirements.md` §2): 1 Mono küçük 15W · 2 Mono büyük 35W · 3 2-kanal 35W/kanal · 4 2+1 (okuma belirsiz, §Açık Maddeler) · 5 4-kanal 60W/kanal · 6 5+1 50W/kanal+20W · 7 6+1 80W/kanal+30W · 8 7+1 100W/kanal+50W · 9 8+1 **500W/kanal (azami tavan)** + 50W bas (opsiyonel 1000 W'lık kabin için 100W kanal — kabin rating, alt sürüş).
**Genel kural: azami 8 kanal, her çıkışın azami gücü 500W.**
**ADR-089 kapsamı (studio/home 8×50W) DEĞİŞTİRİLMEZ ve bu ADR ile genişletilmez — dokunulmaz.**

### (b) Power Governor (yazılım watt/empedans kontrolü)
- **Otomatik mod (default):** hoparlör profili seçimi → empedans doğrulaması (4/8/16 Ω) → watt tavanı hoparlör rating'ine kilitlenir.
- **Manuel override:** panelde kanal başına watt slider'ı + empedans kilidi.
- **Güvenlik (her iki modda):** True Peak Brickwall Limiter (PRD-PLT-003) + termal geri besleme + acil mute + **her değişiklik audit log'a**.
- Entegrasyon: UI `pro.coremusic.net` · API Audio service :9741/:9742 · veri hoparlör profili tablosu (BCNF, `coremusic_neva`) · mockup gate: `.ai/ui-design/` görseli okunmadan UI kodu yazılmaz.

### (c) 18650 + adaptör politikası
Boot'ta **otomatik şarj YOK** (şarj devresi beklemede). Adaptör takıldığında ekranda iki seçim: **"Pili devre dışı bırak (bypass)"** → pil devreden çıkar, güç yalnız adaptörden · **"Şarj et"** → balans şarj devresi devrede. Adaptör yokken pilden çalışma. Gerekçe: pil ömrü, maliyet, üretim kalitesi, ucuz satış. Politika ortak ve katmanlı yazılımda (çok cihazlı senaryo).
⚠️ VERIFICATION REQUIRED: balans şarj + bypass MPN'leri, şarj akımı, pil ömrü hesabı.

### (d) Uygulama sırası
1. **Güç katmanı önce** (ADR-061/064 L0-L6): PSU → rail → koruma → termal.
2. **Paralel yürüt:** (a) donanım güç/termal hesapları · (b) Power Governor yazılımı + mockup · (c) belge/ADR hattı.

## Alternatifler

| # | Alternatif | Sonuç | Gerekçe |
|---|---|---|---|
| 1 | Tek SKU ile başla, hattı sonra genişlet | **REDDEDİLDİ** | Kullanıcı 9 SKU kararını 2026-10-25'te bağladı; paralel yürütme direktifi |
| 2 | ADR-089'u yeni watt değerlerine genişlet | **REDDEDİLDİ** | ADR-089 dokunulmaz; kapsam değişikliği replace eden ADR ister |
| 3 | Boot'ta otomatik şarj (varsayılan açık) | **REDDEDİLDİ** | Gereksiz şarj döngüsü pil ömrünü kısaltır |
| 4 | Yalnız manuel watt slider (otomatik kilit yok) | **REDDEDİLDİ** | Hoparlör rating aşımı riski; otomatik mod güvenliğin default'u |
| 5 | 8+1'i Class AB ile üret | **REDDEDİLDİ (şimdilik — §Açık Maddeler P0)** | index §4: 500W/kanalda verim/ısı Class D lehine; nihai topoloji hesapla bağlanacak |

## Consequences

**Olumlu:** tek SSOT'da 9 SKU + tavan kuralı (8 kanal / 500 W) · Governor hoparlör hasarı riskini yazılım katmanında kapatır + audit denetimi · bypass politikası pil ömrünü/maliyeti korur ve tüm bas cihazlarına ölçeklenir · güç katmanı önceliği ADR-061/064 ile hizalı.

**Olumsuz / Riskler:**
- ⚠️ **P0 — 8+1 topoloji kararı** (Class D mi Class AB mi) hesap+termal ile bağlanacak.
- ⚠️ P1 — 2+1 SKU okuma belirsizliği (35W/kanal + 10W bas mı, tersi mi?).
- ⚠️ P1 — kabin empedansı/sürücü sayısı Governor kilidi + PSU hesabı girdisi.
- ⚠️ P1 — MPN/akım/ömrü doğrulanmadan bypass devresi üretilemez.
- `pro.coremusic.net` PRD'de satır yok → kapsam boşluğu (T-08).

## İlgili Kararlar

| ADR | İlişki |
|---|---|
| `accepted/ADR-089-classab-24v` | Kapsamı **korunur, dokunulmaz** (studio/home 8×50W) |
| `accepted/ADR-090-channel-variant-product-family` | Kanal varyantı eksenini taşır; watt tavanı/Governor/batarya bu ADR-111'de |
| ADR-038 (DAC zinciri) · ADR-061/064 (L0-L6 elektronik) | Zincir ve katman bağlamı |
| `electronic/amplifier/index.md` §4/§5 · `class-ab.md` · `class-d.md` | Sınıf seçimi ve koruma referansları |

## Açık Maddeler

| # | Madde | Seviye |
|---|---|---|
| 1 | **8+1 topoloji kararı** — index §4 Class D'yi öne çıkarır; hesap+termal ile bağlanacak | **P0** |
| 2 | 2+1 SKU okuma belirsizliği — 35W/kanal + 10W bas mı, tersi mi? | P1 |
| 3 | ~~1000W kabin ↔ 100W kanal bütçesi~~ → **KAPANDI 2026-10-25** (kabin rating, sürücü 100W alt sürüş); kalan: kabin empedansı/sürücü sayısı | P1 |
| 4 | SKU başına PSU/rail/termal hesabı (girdi+sonuç formatı) | P1 |
| 5 | `PROJECTS.md` §7.2.7-7.2.9 + `VISION.md` §6/§10.2 revizyonu → **UYGULANDI 2026-10-25** | [x] |
| 6 | `pro.coremusic.net` PRD satırı — `PRD.md` §5'te YOK | P1 |
| 7 | 18650 MPN/şarj akımı/pil ömrü hesabı ⚠️ VERIFICATION REQUIRED | P1 |

---
**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-25
**Mode:** Red Team · Human Mode · Truth Mode