---
title: "K007-data — Doğrulama (K-Space V3)"
type: architecture-verification
category: architecture
date: 2026-10-12
status: draft
version: 1.0.0
node_id: K007-data
---

# data-dogrulama — K007-data

## §1 Protokol

Durum geçişi (v2 §4.4): `draft» → tüm ölçütler kapanırsa `verified» · kanıt çelişirse
`conflict» · cevap yoksa `verify-required». Bu payın şu anki durumu: **draft**.

## §2 Doğrulama protokolü — ölçütler → verified

| # | Ölçüt | Nasıl ölçülür | verified koşulu | Şu anki durum |
|---|---|---|---|---|
| 1 | children = v2 §2.7 birebir | satır 274–288 karşılaştırması | 10/10 isabet | ✅ GEÇTİ (2026-10-12) |
| 2 | master adı = registry §5 | `data-master.md» satır 7 | birebir eşleşme | ✅ GEÇTİ |
| 3 | index.md 8 zorunlu alan | v2 §4.2 listesi | 8/8 mevcut | ✅ GEÇTİ |
| 4 | Yeni dosya adları ADR-097 §5.1 | kebab-case · ≤3 kelime · Türkçe rol | 4/4 uygun | ✅ GEÇTİ |
| 5 | Web kanıtı §4'te | 2 URL + tarih + durum | ≥1 URL + tarih | ✅ GEÇTİ (Koel · Ampache) |
| 6 | Kendi teknoloji seçimimiz | seçim kaydı (URL + sürüm) | kayıp var | ❌ AÇIK → UNKNOWN |
| 7 | depends_on kanıtlandı | karşılıklı kayıt (K008/K009/K000) | tüm satırlar verified | ❌ AÇIK → TASARIM |
| 8 | Kademe-3 kapsamı | ADR-097 K4 planı | çocuk kapsamı kilitli | ⏳ BEKLİYOR |

### §2.1 Kapanış sırası

| Sıra | İş | Sahip |
|---|---|---|
| 1 | §2/6 — kendi teknoloji seçimi (web-kanıt turu) | sonraki doğrulama turu |
| 2 | §2/7 — karşılıklı bağımlılık kayıtları | domain sahipleri |
| 3 | §2/8 — kademe-3 | ADR-097 K4 |

## §3 Açık sorular (open_questions)

| # | Soru | Etiket | Kapanış koşulu |
|---|---|---|---|
| 1 | Kendi DBMS/ORM/önbellek seçimi ne? | ⚠️ VERIFY REQUIRED | ADR + web-kanıt |
| 2 | MIT/AGPL referans sınırı (kod kopyalama)? | ⚠️ VERIFY REQUIRED | K020-licence kaydı |
| 3 | search sahipliği: K007 ↔ K020-discovery? | ⚠️ VERIFY REQUIRED | sahiplik ADR'si |
| 4 | backup sahipliği: K007 ↔ K018-devops? | ⚠️ VERIFY REQUIRED | sahiplik ADR'si |
| 5 | synchronization ↔ K014-network sınırı? | ⚠️ VERIFY REQUIRED | sınır kaydı |
| 6 | 10 çocuğun kademe-3 yapısı? | ⚠️ VERIFY REQUIRED | ADR-097 K4 |

## §4 Web-kanıt kayıtları (iddia · URL · tarih · durum)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| Koel açık kaynak müzik sunucusu — 17175★ · MIT lisans · Laravel | https://github.com/koel/koel | 2026-10-12 | ✅ DOĞRULANDI |
| Ampache — 3793★ · AGPL-3.0 lisans | https://github.com/ampache/ | 2026-10-12 | ✅ DOĞRULANDI |
| İkisi de K007/K008 için "referans müzik server mimarisi"dir | görev manifesti (A2 payı) | 2026-10-12 | ✅ KAYITLI |
| CoreMusic K007'nin kendi DBMS/ORM seçimi | — | 2026-10-12 | **KAYIT YOK — UNKNOWN** |

> Kural: bu tabloya **yalnız** açılan URL ile yazılır; kanıtsız iddia satıra girmez.
> Star/lisans değerleri depo sayfasından 2026-10-12'de okunmuştur; sonradan değişirse güncellenir.

## §5 Kanıt durumunun etkisi

| Etkilenen | Sonuç |
|---|---|
| master §4 | referanslar DOĞRULANDI · kendi seçimimiz UNKNOWN |
| status | `verified» olamaz, `draft» kalır (ölçüt 6–8 açık) |
| bağımlılıklar | tamamı TASARIM İDDİASI etiketli |
| çocuk kapsamı | kademe-3'e kadar `⚠️ VERIFICATION REQUIRED» |

## §6 Sonraki tur checklist (draft → verified)

- [ ] §3 açık soruları 1–6 kapat (ADR onayı / K020-licence kaydı)
- [ ] §4'e kendi teknoloji seçimimizin web-kanıt satırını ekle (iddia · URL · tarih · durum)
- [ ] depends_on satırlarını karşılıklı kayıtla doğrula (K008/K009/K000 index'leri)
- [ ] Kademe-3 kapsamını ADR-097 K4'e işle
- [ ] Ölçüt 6–8 kapandıysa status: verified → registry §5'te durumu güncelle

## §7 Kaynaklar

| Kaynak | Ne verdi |
|---|---|
| v2 §2.7 (satır 270–288) | ölçüt 1–2'nin kaynağı |
| v2 §4.2 · §4.4 | 8 alan · durum geçişi |
| .ai/architecture/index.md §5 · §8 | master adı · isimlendirme |
| https://github.com/koel/koel · https://github.com/ampache/ | §4 web-kanıtları (2026-10-12) |
| ADR-097 §5 · K4 | isimlendirme · kademe-3 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
