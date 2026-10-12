---
title: "K008-domain — domain-master (K-Space V3)"
type: architecture-master
category: architecture
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
node_id: K008-domain
authority: "SSOT: .ai/architecture/index.md"
related: ["index.md", "domain-bagimliliklar.md", "domain-dogrulama.md"]
---

# domain-master — K008-domain

> Master adı **v2'den birebir**: `domain-master.md» (ADR-097 §5.4 — değiştirilmez).

## §0 Bu dosya ne?

| Dosya | İş |
|---|---|
| `index.md» | künye — 8 zorunlu alan (v2 §4.2) |
| `domain-master.md» | bu dosya — rol, sınır, çocuklar, teknoloji, bağımlılık, doğrulama, sorular, kaynak |
| `domain-bagimliliklar.md» | depends_on / used_by / entegrasyon sınırı / riskler |
| `domain-dogrulama.md» | doğrulama protokolü · açık sorular · web-kanıt kayıtları |
| 10 çocuk klasörü | kademe-2 subdomain → her birinde `index.md» |

## §1 Rol

**v2 §2.8 (birebir):** "İşin kuralları — "çal", "listeye ekle", "beğen" gibi kelimelerin anlamı."

ELI15: bu oda CoreMusic'in **anlam katmanıdır** — her kelimenin (çal · beğen · ekle · keşfet)
ne anlama geldiğini burası söyler. Diğer tüm domain'ler bu tanımları okur; kimse kendi kuralını uydurmaz.
Kod içermez; K-Space V3 burada **plan ve kayıt** tutar.

Rol cümlesinden türeyen dört sorumluluk (bu tablo **tasarım yorumudur** — v2 tek cümle verir):

| # | Sorumluluk | Ad bağıntılı çocuklar |
|---|---|---|
| 1 | Katalog kavramları (şarkı · albüm · medya) | music · media · library |
| 2 | Kullanıcı kavramları | user · playlist |
| 3 | Çalma ve keşif kuralları | playback · discovery · recommendation |
| 4 | Cihaz ve sınırlar arası eşleme | device-management · context-mapping |

## §2 Kapsam / Sınır (ne DEĞİL)

### §2.1 Kapsam DIŞI

| Kapsam dışı | Nereye | Tür |
|---|---|---|
| Kuralın fiziksel uygulanması (çalma motoru) | K003-audio · K015-media | ⚠️ SINIR AÇIK — VERIFY REQUIRED |
| Kuralın servis süreçleri (kim, ne zaman) | K012-services | TASARIM İDDİASI |
| Şemanın saklanması (tablolar, indeks) | K007-data | yapısal — v2 §2.7 |
| Öneri modelinin kendisi (ML) | K006-yapay-zeka | yapısal — v2 §2.6 |
| yetki/rol tanımları | K010-identitiy | yapısal — v2 §2.10 |
| Dış servis eşlemeleri (Spotify/Apple) | K013-integraion | TASARIM İDDİASI |
| Keşif domain'i (search/agent) | K020-discovery | ⚠️ SINIR AÇIK — VERIFY REQUIRED |

### §2.2 Kapsam İÇİ (bu domainin taşıdığı 10 çocuk)

music · media · user · library · playlist · playback · discovery · recommendation ·
device-management · context-mapping — hepsi v2 §2.8'de ad olarak geçer;
kapsam tanımı **kademe-3'te** (ADR-097 K4) kilitlenir.

**Konum:** K008, bağımlılık grafiğinin **merkezidir** — bu yüzden `depends_on: []» yazıldı
(domain dışa bağımlı olmaz ilkesi · TASARIM İDDİASI · kanıt için §6/7).

## §3 Kademe-2 çocuk tablosu

Kaynak: v2 §2.8 mini-ağaç (satır 294–308) — **10 çocuk · 0 derinlik**.

| # | Çocuk (kademe-2) | v2 satır | Rol (kaynak) | Durum |
|---|---|---|---|---|
| 1 | music | 298 | ⚠️ VERIFICATION REQUIRED | draft |
| 2 | media | 299 | ⚠️ VERIFICATION REQUIRED | draft |
| 3 | user | 300 | ⚠️ VERIFICATION REQUIRED | draft |
| 4 | library | 301 | ⚠️ VERIFICATION REQUIRED | draft |
| 5 | playlist | 302 | ⚠️ VERIFICATION REQUIRED | draft |
| 6 | playback | 303 | ⚠️ VERIFICATION REQUIRED | draft |
| 7 | discovery | 304 | ⚠️ VERIFICATION REQUIRED | draft |
| 8 | recommendation | 305 | ⚠️ VERIFICATION REQUIRED | draft |
| 9 | device-management | 306 | ⚠️ VERIFICATION REQUIRED | draft |
| 10 | context-mapping | 307 | ⚠️ VERIFICATION REQUIRED | draft |

> Çocuk adları İngilizce teknik terimdir; anlam adından okunur — **resmi kapsam tanımı yoktur**,
> uydurma tanım yazılmaz. Her çocuk için ayrı künye: `<çocuk>/index.md».

## §4 Teknoloji / ekosistem

> Kural: **yalnız** görev manifestinde doğrulanmış kanıt yazılır; yoksa `UNKNOWN».

| İddia | Kaynak | Tarih | Durum |
|---|---|---|---|
| Koel — GitHub 17175★ · MIT lisans · Laravel | https://github.com/koel/koel | 2026-10-12 | ✅ DOĞRULANDI |
| Ampache — GitHub 3793★ · AGPL-3.0 lisans | https://github.com/ampache/ | 2026-10-12 | ✅ DOĞRULANDI |
| İkisi de K007/K008 için "referans müzik server mimarisi"dir | görev manifesti (A2 payı) | 2026-10-12 | ✅ KAYITLI |
| CoreMusic K008 domain modelinin kendi dili/çerçevesi | — | — | **UNKNOWN** |

> **Sınır notu:** Koel ve Ampache **referanstır, seçim değildir** — "müzik domain'i nasıl modellenir"
> karşılaştırması için açılmıştır. CoreMusic'in kendi domain modeli bu satırda **yoktur**.

### §4.1 Kanıt yokluğunda davranış

| Durum | Davranış |
|---|---|
| Seçim/çerçeve satırı kanıtsızsa | `UNKNOWN» yazılır, tahmin edilmez |
| Referans → kopya gibi kullanılamaz | sınır K020-licence'da sorulur (lisans) |
| Komşu payın kanıtı | bu paya taşınmaz |
| `status» etkisi | UNKNOWN satır kaldıkça `draft» kalır |

## §5 Bağımlılık özeti

Tam tablo: `domain-bagimliliklar.md». Özet:

| Yön | Düğüm | Ne | Etiket |
|---|---|---|---|
| → depends_on | (boş) | domain dışa bağımlı olmaz | TASARIM İDDİASI |
| ← used_by | K007-data | entity/kavram tanımı | TASARIM İDDİASI |
| ← used_by | K006-yapay-zeka | kural tanımı okuması | TASARIM İDDİASI |
| ← used_by | K011-application · K012-services | kuralı uygulama | TASARIM İDDİASI |
| ← used_by | K013-integraion · K015-media | eşleme/akış tanımları | TASARIM İDDİASI |

## §6 Doğrulama özeti

| Ölçüt | Nasıl ölçülür | Sonuç |
|---|---|---|
| children = v2 §2.8 birebir (10/10) | satır 294–308 karşılaştırması | ✅ PASS · 2026-10-12 |
| master adı = registry §5 satır 8 | `domain-master.md» eşleşmesi | ✅ PASS |
| index.md 8 zorunlu alan | v2 §4.2 kontrolü | ✅ PASS (8/8) |
| Yeni dosya adları ADR-097 §5.1 | kebab-case · ≤3 kelime · Türkçe rol | ✅ PASS (4/4) |
| Web kanıtı §4'te | 2 URL + tarih + durum | ✅ PASS (Koel · Ampache) |
| depends_on: [] gerekçelendirildi | -bagimliliklar §2 + ADR | ⚠️ TASARIM — kanıt yok |
| Kademe-3 kapsamı | ADR-097 K4 | ⏳ açık |

## §7 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| 1 | discovery/recommendation sahipliği: K008 · K006 · K020-discovery · K020-recomenataion | ⚠️ VERIFICATION REQUIRED |
| 2 | playback sınırı: kural K008'de mi, çalma motoru K003/K015'te mi? | ⚠️ VERIFICATION REQUIRED |
| 3 | context-mapping K013-integraion ile neyi eşliyor? | ⚠️ VERIFICATION REQUIRED |
| 4 | Referanslar (Koel/Ampache) domain modelimize ne kadar yakın? — kopya sınırı | ⚠️ VERIFICATION REQUIRED |
| 5 | depends_on: [] — merkez konumu ADR ile tescillenecek mi? | ⚠️ VERIFICATION REQUIRED |
| 6 | 10 çocuğun kademe-3 (bounded-context) yapısı | ⚠️ VERIFICATION REQUIRED (ADR-097 K4) |

## §8 Kaynaklar

| Kaynak | Ne verdi |
|---|---|
| v2 §2.8 — `G:\Drive'ım\Projelerim\Project-notes\Bayram Ali Prompt Arşivi & Notlar\Coremusic\coremusic-katmanli-mimari-agaci-v2.md» (satır 290–308) | rol cümlesi + 10 çocuk |
| .ai/architecture/index.md §5 satır 8 · §8 | master adı · isimlendirme |
| https://github.com/koel/koel | 17175★ · MIT · Laravel (2026-10-12) |
| https://github.com/ampache/ | 3793★ · AGPL-3.0 (2026-10-12) |
| ADR-097 §5 · K4 | isimlendirme · kademe 3–4 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
