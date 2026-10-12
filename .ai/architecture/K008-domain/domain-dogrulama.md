---
title: "K008-domain — Doğrulama (K-Space V3)"
type: architecture-verification
category: architecture
date: 2026-10-12
status: draft
version: 1.0.0
node_id: K008-domain
---

# domain-dogrulama — K008-domain

## §1 Protokol

Durum geçişi (v2 §4.4): `draft» → tüm ölçütler kapanırsa `verified» · kanıt çelişirse
`conflict» · cevap yoksa `verify-required». Bu payın şu anki durumu: **draft**.

## §2 Doğrulama protokolü — ölçütler → verified

| # | Ölçüt | Nasıl ölçülür | verified koşulu | Şu anki durum |
|---|---|---|---|---|
| 1 | children = v2 §2.8 birebir | satır 294–308 karşılaştırması | 10/10 isabet | ✅ GEÇTİ (2026-10-12) |
| 2 | master adı = registry §5 | `domain-master.md» satır 8 | birebir eşleşme | ✅ GEÇTİ |
| 3 | index.md 8 zorunlu alan | v2 §4.2 listesi | 8/8 mevcut | ✅ GEÇTİ |
| 4 | Yeni dosya adları ADR-097 §5.1 | kebab-case · ≤3 kelime · Türkçe rol | 4/4 uygun | ✅ GEÇTİ |
| 5 | Web kanıtı §4'te | 2 URL + tarih + durum | ≥1 URL + tarih | ✅ GEÇTİ (Koel · Ampache) |
| 6 | depends_on: [] gerekçesi | ADR kaydı | ADR onayı | ❌ AÇIK → TASARIM |
| 7 | used_by kanıtlandı | karşılıklı kayıt | tüm satırlar verified | ❌ AÇIK → TASARIM |
| 8 | Kademe-3 kapsamı | ADR-097 K4 planı | çocuk kapsamı kilitli | ⏳ BEKLİYOR |

### §2.1 Kapanış sırası

| Sıra | İş | Sahip |
|---|---|---|
| 1 | §2/6 — depends_on: [] tescili | ADR |
| 2 | §2/7 — karşılıklı bağımlılık kayıtları | domain sahipleri |
| 3 | §2/8 — kademe-3 | ADR-097 K4 |

## §3 Açık sorular (open_questions)

| # | Soru | Etiket | Kapanış koşulu |
|---|---|---|---|
| 1 | discovery/recommendation sahipliği (K006 · K020 ile örtüşme)? | ⚠️ VERIFY REQUIRED | sahiplik ADR'si |
| 2 | playback sınırı — kural mı motor mu K008'de? | ⚠️ VERIFY REQUIRED | sınır ADR'si |
| 3 | context-mapping ↔ K013-integraion eşlemesi? | ⚠️ VERIFY REQUIRED | sınır kaydı |
| 4 | Referans kod kopyalama sınırı (MIT/AGPL)? | ⚠️ VERIFY REQUIRED | K020-licence kaydı |
| 5 | depends_on: [] merkez konumu tescilli mi? | ⚠️ VERIFY REQUIRED | ADR onayı |
| 6 | 10 çocuğun kademe-3 yapısı? | ⚠️ VERIFY REQUIRED | ADR-097 K4 |

## §4 Web-kanıt kayıtları (iddia · URL · tarih · durum)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| Koel — 17175★ · MIT lisans · Laravel (referans müzik sunucusu) | https://github.com/koel/koel | 2026-10-12 | ✅ DOĞRULANDI |
| Ampache — 3793★ · AGPL-3.0 lisans (referans müzik sunucusu) | https://github.com/ampache/ | 2026-10-12 | ✅ DOĞRULANDI |
| İkisi de K007/K008 için "referans müzik server mimarisi"dir | görev manifesti (A2 payı) | 2026-10-12 | ✅ KAYITLI |
| CoreMusic K008 domain modelinin kendi dili/çerçevesi | — | 2026-10-12 | **KAYIT YOK — UNKNOWN** |

> Kural: bu tabloya **yalnız** açılan URL ile yazılır; kanıtsız iddia satıra girmez.
> Bu kanıtlar K007 ile **ortaktır** (manifest: "K007/K008"); K006/K009/K010'ya taşınmaz.

## §5 Kanıt durumunun etkisi

| Etkilenen | Sonuç |
|---|---|
| master §4 | referanslar DOĞRULANDI · kendi çerçevemiz UNKNOWN |
| status | `verified» olamaz, `draft» kalır (ölçüt 6–8 açık) |
| bağımlılıklar | used_by tamamı TASARIM İDDİASI; depends_on bilinçli boş |
| çocuk kapsamı | kademe-3'e kadar `⚠️ VERIFICATION REQUIRED» |

## §6 Sonraki tur checklist (draft → verified)

- [ ] §3 açık soruları 1–6 kapat (sahiplik/sınır ADR'leri)
- [ ] §4'e kendi domain çerçevemizin web-kanıt satırını ekle (iddia · URL · tarih · durum)
- [ ] used_by satırlarını karşılıklı kayıtla doğrula (K006/K007/K011/K012/K013/K015)
- [ ] Kademe-3 kapsamını ADR-097 K4'e işle
- [ ] Ölçüt 6–8 kapandıysa status: verified → registry §5'te durumu güncelle

## §7 Kaynaklar

| Kaynak | Ne verdi |
|---|---|
| v2 §2.8 (satır 290–308) | ölçüt 1–2'nin kaynağı |
| v2 §4.2 · §4.4 | 8 alan · durum geçişi |
| .ai/architecture/index.md §5 · §8 | master adı · isimlendirme |
| https://github.com/koel/koel · https://github.com/ampache/ | §4 web-kanıtları (2026-10-12) |
| ADR-097 §5 · K4 | isimlendirme · kademe-3 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
