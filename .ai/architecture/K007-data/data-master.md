---
title: "K007-data — data-master (K-Space V3)"
type: architecture-master
category: architecture
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
node_id: K007-data
authority: "SSOT: .ai/architecture/index.md"
related: ["index.md", "data-bagimliliklar.md", "data-dogrulama.md"]
---

# data-master — K007-data

> Master adı **v2'den birebir**: `data-master.md» (ADR-097 §5.4 — değiştirilmez).

## §0 Bu dosya ne?

| Dosya | İş |
|---|---|
| `index.md» | künye — 8 zorunlu alan (v2 §4.2) |
| `data-master.md» | bu dosya — rol, sınır, çocuklar, teknoloji, bağımlılık, doğrulama, sorular, kaynak |
| `data-bagimliliklar.md» | depends_on / used_by / entegrasyon sınırı / riskler |
| `data-dogrulama.md» | doğrulama protokolü · açık sorular · web-kanıt kayıtları |
| 10 çocuk klasörü | kademe-2 subdomain → her birinde `index.md» |

## §1 Rol

**v2 §2.7 (birebir):** "Veritabanı — şarkılar, kullanıcılar, listeler, yedekler."

ELI15: bu oda CoreMusic'in **hafızasıdır** — tüm kalıcı veri burada yaşar: kayıt şeması,
değişiklik geçmişi, sorgu yolları, önbellek, arama, yedek ve veri kalitesi.
Kod içermez; K-Space V3 burada **plan ve kayıt** tutar.

Rol cümlesinden türeyen dört sorumluluk (bu tablo **tasarım yorumudur** — v2 tek cümle verir):

| # | Sorumluluk | Ad bağıntılı çocuklar |
|---|---|---|
| 1 | Kayıtı biçimlendirmek | schema · migrations · indexing |
| 2 | Erişimi ve bütünlüğü yönetmek | repositories · transactions · data-quality |
| 3 | Hızlı ve taze okumak | cache · search · synchronization |
| 4 | Koruma altına almak | backup |

## §2 Kapsam / Sınır (ne DEĞİL)

### §2.1 Kapsam DIŞI

| Kapsam dışı | Nereye | Tür |
|---|---|---|
| İş kuralları ("çal", "beğen" anlamı) | K008-domain | yapısal — v2 §2.8 |
| Şifreleme/anahtar/Politika sahipliği | K009-security | yapısal — v2 §2.9 |
| Fiziksel disk/donanım | K000-isletim-sistemi · K001-donanım | yapısal — v2 §2.1/§2.2 |
| Operasyonel yedek alımı ve deploy (CI) | K018-devops | ⚠️ SINIR AÇIK — VERIFY REQUIRED |
| Arama/katalog keşif domain'i | K020-discovery | ⚠️ SINIR AÇIK — VERIFY REQUIRED |
| Ölçüm/telemetri kaydı | K017-observability | TASARIM İDDİASI |
| Öneri modelinin eğitim verisi fikri | K006-yapay-zeka | TASARIM İDDİASI |

### §2.2 Kapsam İÇİ (bu domainin taşıdığı 10 çocuk)

schema · migrations · repositories · transactions · indexing · cache · search ·
synchronization · backup · data-quality — hepsi v2 §2.7'de ad olarak geçer;
kapsam tanımı **kademe-3'te** (ADR-097 K4) kilitlenir.

## §3 Kademe-2 çocuk tablosu

Kaynak: v2 §2.7 mini-ağaç (satır 274–288) — **10 çocuk · 0 derinlik**.

| # | Çocuk (kademe-2) | v2 satır | Rol (kaynak) | Durum |
|---|---|---|---|---|
| 1 | schema | 278 | ⚠️ VERIFICATION REQUIRED | draft |
| 2 | migrations | 279 | ⚠️ VERIFICATION REQUIRED | draft |
| 3 | repositories | 280 | ⚠️ VERIFICATION REQUIRED | draft |
| 4 | transactions | 281 | ⚠️ VERIFICATION REQUIRED | draft |
| 5 | indexing | 282 | ⚠️ VERIFICATION REQUIRED | draft |
| 6 | cache | 283 | ⚠️ VERIFICATION REQUIRED | draft |
| 7 | search | 284 | ⚠️ VERIFICATION REQUIRED | draft |
| 8 | synchronization | 285 | ⚠️ VERIFICATION REQUIRED | draft |
| 9 | backup | 286 | ⚠️ VERIFICATION REQUIRED | draft |
| 10 | data-quality | 287 | ⚠️ VERIFICATION REQUIRED | draft |

> Çocuk adları İngilizce teknik terimdir; anlam adından okunur — **resmi kapsam tanımı yoktur**,
> uydurma tanım yazılmaz. Her çocuk için ayrı künye: `<çocuk>/index.md».

## §4 Teknoloji / ekosistem

> Kural: **yalnız** görev manifestinde doğrulanmış kanıt yazılır; yoksa `UNKNOWN».

| İddia | Kaynak | Tarih | Durum |
|---|---|---|---|
| Koel — açık kaynak müzik sunucusu; GitHub 17175★; MIT lisans; Laravel | https://github.com/koel/koel | 2026-10-12 | ✅ DOĞRULANDI |
| Ampache — GitHub 3793★; AGPL-3.0 lisans | https://github.com/ampache/ | 2026-10-12 | ✅ DOĞRULANDI |
| Bu ikisi "referans müzik server mimarisi"dir (K007/K008) | görev manifesti (A2 payı) | 2026-10-12 | ✅ KAYITLI |
| CoreMusic K007'nin kendi DBMS/ORM/önbellek seçimi | — | — | **UNKNOWN** |

> **Sınır notu:** Koel ve Ampache **seçim değildir** — yalnız "müzik sunucusu verisi nasıl kurgulanır"
> karşılaştırması için referanstır. CoreMusic'in kendi yığını bu satırda **yoktur** (uydurulmaz).

### §4.1 Kanıt yokluğunda davranış

| Durum | Davranış |
|---|---|
| Seçim satırı kanıtsızsa | `UNKNOWN» yazılır, tahmin edilmez |
| Lisans iddiası | yalnız depodaki LICENSE ile yazılır (MIT · AGPL-3.0) |
| Komşu payın kanıtı | bu paya taşınmaz |
| `status» etkisi | seçim UNKNOWN kaldıkça `draft» kalır |

## §5 Bağımlılık özeti

Tam tablo: `data-bagimliliklar.md». Özet — **hepsi TASARIM İDDİASI** (verified değil):

| Yön | Düğüm | Ne | Etiket |
|---|---|---|---|
| → depends_on | K008-domain | şemanın dayandığı domain modeli | TASARIM İDDİASI |
| → depends_on | K009-security | şifreleme/anahtar/kural parçaları | TASARIM İDDİASI |
| → depends_on | K000-isletim-sistemi | fiziksel saklama (dosya/disk) | TASARIM İDDİASI |
| ← used_by | K006-yapay-zeka | öneri girdi verisi | TASARIM İDDİASI |
| ← used_by | K011-application · K012-services | okuma/yazma (repo üzerinden) | TASARIM İDDİASI |
| ← used_by | K019-testing | test verisi kurulumu | TASARIM İDDİASI |

## §6 Doğrulama özeti

| Ölçüt | Nasıl ölçülür | Sonuç |
|---|---|---|
| children = v2 §2.7 birebir (10/10) | satır 274–288 karşılaştırması | ✅ PASS · 2026-10-12 |
| master adı = registry §5 satır 7 | `data-master.md» eşleşmesi | ✅ PASS |
| index.md 8 zorunlu alan | v2 §4.2 kontrolü | ✅ PASS (8/8) |
| Yeni dosya adları ADR-097 §5.1 | kebab-case · ≤3 kelime · Türkçe rol | ✅ PASS (4/4) |
| Web kanıtı §4'te | 2 URL + tarih + durum | ✅ PASS (Koel · Ampache) |
| Kendi teknoloji seçimimiz | seçim kaydı | ❌ AÇIK → UNKNOWN |
| Kademe-3 kapsamı | ADR-097 K4 | ⏳ açık |

## §7 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| 1 | CoreMusic K007'nin DBMS/ORM/önbellek seçimi ne? (manifest yalnız referans verdi) | ⚠️ VERIFICATION REQUIRED |
| 2 | Referans lisans etkileşimi: MIT (Koel) / AGPL-3.0 (Ampache) — kod kopyalama sınırı → K020-licence akışı | ⚠️ VERIFICATION REQUIRED |
| 3 | `search» (K007) ile K020-discovery `search/» sahipliği kimde? | ⚠️ VERIFICATION REQUIRED |
| 4 | `backup» operasyonel sahipliği: K007 mi K018-devops mu? | ⚠️ VERIFICATION REQUIRED |
| 5 | `synchronization» çok-cihaz akışı K014-network ile sınırı | ⚠️ VERIFICATION REQUIRED |
| 6 | 10 çocuğun kademe-3 (bounded-context) yapısı | ⚠️ VERIFICATION REQUIRED (ADR-097 K4) |

## §8 Kaynaklar

| Kaynak | Ne verdi |
|---|---|
| v2 §2.7 — `G:\Drive'ım\Projelerim\Project-notes\Bayram Ali Prompt Arşivi & Notlar\Coremusic\coremusic-katmanli-mimari-agaci-v2.md» (satır 270–288) | rol cümlesi + 10 çocuk |
| .ai/architecture/index.md §5 satır 7 · §8 | master adı · isimlendirme |
| https://github.com/koel/koel | 17175★ · MIT · Laravel (2026-10-12) |
| https://github.com/ampache/ | 3793★ · AGPL-3.0 (2026-10-12) |
| ADR-097 §5 · K4 | isimlendirme · kademe 3–4 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
