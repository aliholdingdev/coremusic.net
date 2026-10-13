---
title: "K020-recomenataion — Recommendation Master (Öneri Programı)"
type: master
category: architecture
node_id: K020-recomenataion/recommendation-master
node_type: file
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
authority: "SSOT: v2 §2.20 (şekil) · ADR-097 §5 (isimlendirme) · accepted/ADR-030 (YZ stratejisi)"
related:
  - "ranking/index.md"
  - "personalization/index.md"
  - "evaluation/index.md"
  - "../K020-discovery/index.md"
group: mimari
---


# K020-recomenataion — Recommendation Master

## 1. Rol (ELI15)

Öneri sisteminin **tek sayfalık planıdır**: neyle beslenecek (girdi), ne üretecek
(çıktı), parçaları neler (alt sistemler) ve **işe yaradığını nasıl anlayacağız**
(ölçüm). Bu dosya algoritma **yazmaz** — sınır ve kapıları çizer.

## 2. Kapsam / Kapsam dışı

| Kapsam | Kapsam dışı |
|---|---|
| Öneri girdi/kaynak haritası | Algoritma kodu (implementation — kademe 8) |
| Alt sistemlerin sınırları (ranking/personalization/evaluation) | Dış servis entegrasyonu → `../K020-discovery/` |
| Ölçüm/metric tanımı (ne ölçülür) | **Kişisel veri işleme kuralları** → KVKK kapsamı tanımsız (`⚠️`) |
| Onay kapıları (veri → tasarım → kod) | Model eğitimi altyapısı (var mı yok mu belirsiz) |

## 3. Girdi / Çıktı haritası

| Yön | Kaynak | Kanıt/_durum |
|---|---|---|
| **Girdi** | Yerel çalma geçmişi / favori / kütüphane (MySQL kataloğu, BCNF) | mevcut veri modeli varsayımı → `⚠️ VERIFY REQUIRED` (şema kanıtı K007/K015'te) |
| **Girdi** | Dış katalog keşfi (`../K020-discovery/`) | keşif notu — **entegrasyon onayı yok** (YouTube Music API yok) |
| **Girdi** | Kullanıcı bağlamı (cihaz/saat/ortam) | tasarım yok → `⚠️ VERIFY REQUIRED` |
| **Çıktı** | Sıralı öneri listesi (sıra + gerekçe) | tasarım yok |
| **Çıktı** | Kişisel çalma listesi / radyo | tasarım yok |

## 4. Alt sistemler

| Alt sistem | Sorumluluk | Soru | Dosya |
|---|---|---|---|
| **Ranking** | Adayları **sıralamak** — ne üstte çıkacak? | kural tabanlı mı, skor mu? | `[[ranking/index]]` |
| **Personalization** | Kullanıcıya **özel kılmak** — profile göre uyarlamak | profil nerede tutulur, KVKK kapsamı? | `[[personalization/index]]` |
| **Evaluation** | **Ölçmek** — öneri gerçekten işe yaradı mı? | hangi metrik, hangi eşik? | `[[evaluation/index]]` |

> Sıra **zorunlu**: kişiselleştirme olmadan sıralama yapılabilir; sıralama olmadan
> ölçüm **anlamsız**dır. Değerlendirme en sona konur, ama **kapı olarak** en baştan
> tanımlanır (ölçülmeyen öneri yayına alınamaz — bu bir tasarım iddiası değildir,
> süreç kuralıdır).

## 5. Onay kapıları (adımlar)

```text
① Veri kaynağı onayı (KVKK kapsamı + hangi veri)   → K020-comilance (VERIFY REQUIRED)
        ↓
② Öneri tasarımı (ranking + personalization)       → ADR gerektirir (K020-adr)
        ↓
③ Ölçüm tanımı (evaluation metricleri + eşik)      → evaluation/ iskeleti
        ↓
④ Kod (kademe 5–8)                                 → yalnız ①②③ onaylıysa
```

- Kapılardan biri `VERIFY REQUIRED` ise **④ başlamaz**.
- Metrik/eşik **uydurulmaz** — ölçüm yapılmadan sayı yazılmaz (zero-hallucination).

## 6. Ölçüm durumu (2026-10-12)

| Öğe | Durum |
|---|---|
| Veri seti / log şeması | yok → `⚠️ VERIFY REQUIRED` |
| Metrik tanımı (ör. hit-rate, NDCG, dinlenme oranı) | **tanımlanmadı** — isimler örnek amaçlıdır, eşik yok |
| Deneme/A-B altyapısı | yok → `⚠️ VERIFY REQUIRED` |
| Tarihsel sonuç | **hiç yok** — "doğrulandı" iddiası yazılmaz |

## 7. Açık sorular (VERIFY REQUIRED)

| # | Soru | Etiket |
|---|---|---|
| 1 | Öneri kişisel veri işliyor mu? KVKK/GDPR kapsamı tanımsız (`K020-comilance`) | `⚠️ VERIFY REQUIRED` |
| 2 | Veri kaynağı: yerel geçmiş mi, dış servis mi, ikisi mi? | `⚠️ VERIFY REQUIRED` |
| 3 | Öneri motoru K006 (ML ürün) ile mi, K016 (ajan altyapısı) ile mi ilişkili? — v2 §2.6/§2.16 kapsam ayrımı | `⚠️ VERIFY REQUIRED` |
| 4 | Ölçüm metrikleri ve eşikleri ne olacak? (uydurma eşik yazılmaz) | `⚠️ VERIFY REQUIRED` |
| 5 | ⚠️ ID CONFLICT: 6 klasör K020 önekinde paylaşır (adr/comilance/comere/licence/discovery/recomenataion) — ADR-097 §6 · v2 §3.1 | `⚠️ VERIFY REQUIRED` |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

## 8. Alt sistem ayrıntısı (sorumluluk sınırları)

| Soru | Ranking | Personalization | Evaluation |
|---|---|---|---|
| Giriş nedir? | aday havuzu | kullanıcı profili (tanımsız) | sıralı çıktı + davranış logu (tanımsız) |
| Çıktı nedir? | sıra + skor | uyarlanmış sıra | metrik + karar (`geçti`/`kaldı`) |
| Bağımlı olduğu | kişiselleştirme (varsa) | sıralama | her ikisi |
| Sıfırlama/durum | profil yoksa devre dışı (fail-safe) | profil silinince devre dışı | ölçüm yoksa **yayım yok** |
| Sorumluluk alanı dışındaki | profil tutma · metrik · gizlilik | skor · ölçüm · dış servis | model/strateji · profil |

## 9. Kapı tablosu (sahip + kanıt + durum)

| Kapı | Sahip | Gereken kanıt | Durum (2026-10-12) |
|---|---|---|---|
| K1 — veri kapsamı | `K020-comilance` | KVKK/GDPR kapsam kararı | `⚠️ VERIFY REQUIRED` |
| K2 — veri kaynağı | Vault Steward + `K007-data` | gerçek şema/log kanıtı | `⚠️ VERIFY REQUIRED` |
| K3 — yöntem (ADR) | `K020-adr` | ADR taslağı (ranking + personalization) | başlamadı |
| K4 — ölçüm tanımı | `K020-recomenataion/evaluation` | metrik + eşik + veri seti | başlamadı |
| K5 — yayım | `qa-engineer` | K1–K4 kapalı + test raporu | kapalı (öncekiler açık) |

## 10. Riskler

1. **Ölçümsüz yayım** — metrik yokken "öneri iyi" iddiası kanıtsız olur (yasak).
2. **Gizlilik** — kişiselleştirme, KVKK kapsamı tanımsızken başlanırsa geri alınamaz maliyet.
3. **Kapsanmayan dış kaynak** — YouTube Music resmi API'siz (bkz. `../K020-discovery/`); katalog boşluğu öneri kalitesini doğrudan düşürür.
4. **Kapsam kayması** — öneri motoru K006 (ML ürünü) mü, K016 (ajan altyapısı) mı: ayrım netleşmedi (madde 7/3).

