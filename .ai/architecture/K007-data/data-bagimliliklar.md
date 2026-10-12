---
title: "K007-data — Bağımlılıklar (K-Space V3)"
type: architecture-dependencies
category: architecture
date: 2026-10-12
status: draft
version: 1.0.0
node_id: K007-data
---

# data-bagimliliklar — K007-data

## §1 Etiket kuralı (önce oku)

Bu dosyadaki **her satır TASARIM İDDİASIdır**; hiçbiri `verified» değildir.
Kanıt = gerçek dosya yolu / URL / test kaydı. Kanıt gelince `data-dogrulama.md» §6'da
ölçüt kapanır ve satır `verified» etiketine döner. Uydurma bağımlılık yazılmaz.

## §2 depends_on (bu domain neye muhtaç)

| # | Hedef düğüm | Ne alınır | Gerekçe | Kanıt durumu |
|---|---|---|---|---|
| 1 | K008-domain | entity/kavram tanımı (şemanın dayanağı) | veri, anlamı olmadan şekil değildir | TASARIM İDDİASI — kanıt yok |
| 2 | K009-security | şifreleme, anahtar, politika parçaları | kullanıcı verisi korunur | TASARIM İDDİASI — kanıt yok |
| 3 | K000-isletim-sistemi | fiziksel dosya/disk yazımı | veri bir yerde durur | TASARIM İDDİASI — kanıt yok |

> v2 §2.7 bağımlılık **vermez**; üç satır yapısal mantıktan türetildi (ad + rol cümlesi).

## §3 used_by (bu domain kimi besliyor)

| # | Kaynak düğüm | Ne alır | Kanıt durumu |
|---|---|---|---|
| 1 | K006-yapay-zeka | öneri girdisi (etkileşim + katalog) | TASARIM İDDİASI |
| 2 | K011-application | sayfa verisi (repo üzerinden) | TASARIM İDDİASI |
| 3 | K012-services | servis kalıcılığı (repo üzerinden) | TASARIM İDDİASI |
| 4 | K019-testing | test verisi kurulumu | TASARIM İDDİASI |
| 5 | K018-devops | yedek/geri yükleme operasyonu | ⚠️ SINIR AÇIK — VERIFY REQUIRED |

## §4 Entegrasyon sınırları

| Sınır | Paylaşılır | Paylaşılmaz |
|---|---|---|
| K007 ↔ K008-domain | entity/kavram adları (ubiquitous language) | kural motoru, iş akışı (domain'in) |
| K007 ↔ K009-security | şifreleme **çağrısı** | anahtar saklama, politika yazımı (security'nin) |
| K007 ↔ K006-yapay-zeka | veriye erişim sözleşmesi | model eğitimi/öneri mantığı (K006'nın) |
| K007 ↔ K020-discovery | arama indeksi verisi | keşif domain'i kuralları → ⚠️ VERIFY REQUIRED |
| K007 ↔ K018-devops | yedek alacağı artefakt | zamanlama/pipeline → ⚠️ VERIFY REQUIRED |

### §4.1 Entegrasyon kuralı (özet)

1. K007 **sahiptir**: şema · migration · indeks · yedek · veri kalitesi.
2. K007 **okur** domain'den (kural), **yazmaz** — kural değişikliği K008'de.
3. Sınır aşıldığında: kayıt → `.ai/architecture/index.md» §6'ya işlenir, tahmin edilmez.

## §5 Riskli bağımlılıklar

| # | Risk | Neden | Etki | Öneri | Durum |
|---|---|---|---|---|---|
| 1 | Lisans etkileşimi | Ampache AGPL-3.0 · Koel MIT | referanstan kod kopyalarsa yükümlülük doğar | K020-licence akışına sor | ⚠️ VERIFY REQUIRED |
| 2 | search sahipliği çakışması | K007-search ↔ K020-discovery/search | çift indeks, tutarsız sonuç | sahiplik ADR'si | ⚠️ VERIFY REQUIRED |
| 3 | backup sahipliği belirsiz | K007 ↔ K018-devops | kimsenin almadığı yedek | sahiplik ADR'si | ⚠️ VERIFY REQUIRED |
| 4 | şema değişimi sessiz kırar | K008/K011/K012 okuyucu | üretim hatası | şema sözleşmesi + K019 testi | TASARIM ÖNERİSİ |

## §6 Doğrulama durumu (ne zaman `verified»)

| Bağımlılık | Ölçüt | Şu an |
|---|---|---|
| K008-domain → | entity tanımı referansı + review kaydı | draft (kanıt yok) |
| K009-security → | şifreleme çağrısı sözleşmesi | draft (kanıt yok) |
| K000-isletim-sistemi → | saklama yolu/izin kaydı | draft (kanıt yok) |
| used_by 5 satırı | ilgili domain index'lerinde karşılıklı kayıt | draft (karşılıklı kayıt yok) |
| Referans lisans notu | K020-licence dosyasında kayıt | draft (kayıt yok) |

## §7 Kaynaklar

| Kaynak | Ne verdi |
|---|---|
| v2 §2.7 (satır 270–288) | yalnız rol + çocuk listesi — bağımlılık VERMEZ |
| .ai/architecture/index.md §5 satır 7 · §8 | master adı · isimlendirme |
| https://github.com/koel/koel · https://github.com/ampache/ | referans mimari + lisans (2026-10-12) |
| ADR-097 §5 | etiket ve isimlendirme kuralı |
| v2 §4.2 | `depends_on» alanının tanımı |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
