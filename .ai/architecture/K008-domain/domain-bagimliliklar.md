---
title: "K008-domain — Bağımlılıklar (K-Space V3)"
type: architecture-dependencies
category: architecture
date: 2026-10-12
status: draft
version: 1.0.0
node_id: K008-domain
---

# domain-bagimliliklar — K008-domain

## §1 Etiket kuralı (önce oku)

Bu dosyadaki **her satır TASARIM İDDİASIdır**; hiçbiri `verified» değildir.
Kanıt = gerçek dosya yolu / URL / test kaydı. Kanıt gelince `domain-dogrulama.md» §6'da
ölçüt kapanır ve satır `verified» etiketine döner. Uydurma bağımlılık yazılmaz.

## §2 depends_on (bu domain neye muhtaç)

**Bilinçli boş: `depends_on: []».** Gerekçe (TASARIM İDDİASI): domain katmanı, işin anlamını
taşıdığı için dışa bağımlı olmaz — dışarıdan okuyorsa artık o anlam ona ait değildir.

| # | Olası hedef | Neden yazılmadı | Kanıt durumu |
|---|---|---|---|
| 1 | K007-data | domain veri **okumaz**, anlamlı **tanımlar** | yazılmadı — TASARIM |
| 2 | K015-media | medya kavramı domain'de tanımlanır, tersi değil | yazılmadı — TASARIM |
| 3 | K016-AI | ajan altyapısı domain'i çağıran taraftadır | yazılmadı — TASARIM |

> Bu boşluk **kanıtlanmış bağımsızlık değildir** — bir iddiadır. ADR ile tescillenmezse
> `domain-dogrulama.md» §2/6 açık kalır.

## §3 used_by (bu domain kimi besliyor)

| # | Kaynak düğüm | Ne alır | Kanıt durumu |
|---|---|---|---|
| 1 | K007-data | entity/kavram tanımı (şemanın dayanağı) | TASARIM İDDİASI |
| 2 | K006-yapay-zeka | "öneri/beğeni" kuralı | TASARIM İDDİASI |
| 3 | K011-application | komut/örüntü kuralları (çal, ekle) | TASARIM İDDİASI |
| 4 | K012-services | servis süreçlerinin kural girdisi | TASARIM İDDİASI |
| 5 | K013-integraion · K015-media | eşleme ve akış tanımları | TASARIM İDDİASI |

## §4 Entegrasyon sınırları

| Sınır | Paylaşılır | Paylaşılmaz |
|---|---|---|
| K008 ↔ K007-data | kavram adları (ubiquitous language) | tablo/şema/migration (K007'nin) |
| K008 ↔ K006-yapay-zeka | kural **tanımı** | model eğitimi, skor üretimi (K006'nın) |
| K008 ↔ K003-audio · K015-media | "ne çalınır" listesi | "nasıl çalınır" motoru → ⚠️ SINIR AÇIK |
| K008 ↔ K020-discovery | keşif kavramları | search/agent domain'i → ⚠️ SINIR AÇIK |
| K008 ↔ K013-integraion | context-mapping eşlemeleri | dış API akışı (K013'ün) |

### §4.1 Entegrasyon kuralı (özet)

1. K008 **sahiptir**: kavram · kural · sınır dili (ubiquitous language).
2. K008 **uydurmaz**: veri, motor, servis, UI — hepsi kuralı **okur**, K008'e **yazmaz**.
3. Sınır aşıldığında: kayıt → `.ai/architecture/index.md» §6'ya işlenir, tahmin edilmez.

## §5 Riskli bağımlılıklar

| # | Risk | Neden | Etki | Öneri | Durum |
|---|---|---|---|---|---|
| 1 | Öneri sahipliği üçlüsü | K008-recommendation · K006 · K020-recomenataion | çift kural, tutarsız öneri | sahiplik ADR'si | ⚠️ VERIFY REQUIRED |
| 2 | Keşif sahipliği ikilisi | K008-discovery ↔ K020-discovery | iki ayrı keşif dili | sahiplik ADR'si | ⚠️ VERIFY REQUIRED |
| 3 | playback sınırı | kural K008'de, motor K003/K015'te | kural ile davranış ayrışır | sınır ADR'si | ⚠️ VERIFY REQUIRED |
| 4 | referans → kopya | Koel (MIT) · Ampache (AGPL) | lisans yükümlülüğü | K020-licence akışı | ⚠️ VERIFY REQUIRED |

## §6 Doğrulama durumu (ne zaman `verified»)

| Bağımlılık | Ölçüt | Şu an |
|---|---|---|
| depends_on: [] (boş) | ADR ile merkez konumu tescili | draft (kanıt yok) |
| used_by 5 satırı | ilgili domain index'lerinde karşılıklı kayıt | draft (karşılıklı kayıt yok) |
| playback/discovery/recommendation sınırları | sınır ADR'si | draft (ADR yok) |
| Referans lisans notu | K020-licence dosyasında kayıt | draft (kayıt yok) |

## §7 Kaynaklar

| Kaynak | Ne verdi |
|---|---|
| v2 §2.8 (satır 290–308) | yalnız rol + çocuk listesi — bağımlılık VERMEZ |
| .ai/architecture/index.md §5 satır 8 · §8 | master adı · isimlendirme |
| https://github.com/koel/koel · https://github.com/ampache/ | referans mimari + lisans (2026-10-12) |
| ADR-097 §5 | etiket ve isimlendirme kuralı |
| v2 §4.2 | `depends_on» alanının tanımı |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
