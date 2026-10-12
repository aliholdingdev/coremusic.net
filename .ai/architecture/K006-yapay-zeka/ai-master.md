---
title: "K006-yapay-zeka — ai-master (K-Space V3)"
type: architecture-master
category: architecture
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
node_id: K006-yapay-zeka
authority: "SSOT: .ai/architecture/index.md"
related: ["index.md", "yapay-zeka-bagimliliklar.md", "yapay-zeka-dogrulama.md"]
---

# ai-master — K006-yapay-zeka

> Master adı **v2'den birebir**: `ai-master.md» (ADR-097 §5.4 — İngilizce -master adları DEĞİŞTİRİLMEZ).

## §0 Bu dosya ne?

| Dosya | İş |
|---|---|
| `index.md» | künye — 8 zorunlu alan (v2 §4.2) |
| `ai-master.md» | bu dosya — rol, sınır, çocuk tablosu, teknoloji, bağımlılık, doğrulama, sorular, kaynak |
| `yapay-zeka-bagimliliklar.md» | depends_on / used_by / entegrasyon sınırı / riskler |
| `yapay-zeka-dogrulama.md» | doğrulama protokolü · açık sorular · web-kanıt kayıtları |
| 9 çocuk klasörü | kademe-2 subdomain → her birinde `index.md» |

## §1 Rol

**v2 §2.6 (birebir):** "Şarkı öneren, sesi analiz eden yapay zeka parçaları."

ELI15: bu oda CoreMusic'in **akıllı tarafıdır** — kullanıcıya ne çalacağını önerir, sesi okur,
modeli ayakta tutar ve önerinin kalitesini ölçer. Kod içermez; K-Space V3 burada **plan ve kayıt** tutar.

Rol cümlesinden türeyen dört sorumluluk (bu tablo **tasarım yorumudur** — v2 yalnız tek cümle verir):

| # | Sorumluluk | Ad bağıntılı çocuklar |
|---|---|---|
| 1 | Öneri üretmek | recommendation · personalization |
| 2 | Sesi ve metriği analiz etmek | music-analysis · audio-intelligence |
| 3 | Modeli barındırıp çalıştırmak | model-management · inference · local-inference |
| 4 | Kaliteyi ölçmek | evaluation · testing |

## §2 Kapsam / Sınır (ne DEĞİL)

### §2.1 Kapsam DIŞI

| Kapsam dışı | Nereye | Tür |
|---|---|---|
| LLM/ajan altyapısı, agent çalışma zamanı | K016-AI | ✅ KAYIT — registry §6 |
| Şarkı/kullanıcı/liste verisinin saklanması | K007-data | yapısal — v2 §2.7 |
| İş kuralları ("çal", "beğen" anlamı) | K008-domain | yapısal — v2 §2.8 |
| Öneri/keşif domain'inin sahipliği | K020-recomenataion | ⚠️ SINIR AÇIK — VERIFY REQUIRED |
| Öneriyi yürüten arka plan süreci | K012-services | TASARIM İDDİASI |
| Kimlik · yetki · oturum güvenliği | K010-identitiy · K009-security | yapısal — v2 §2.9/§2.10 |
| UI'da öneri kartlarının çizimi | K011-application | TASARIM İDDİASI |

### §2.2 Kapsam İÇİ (bu domainin taşıdığı 9 çocuk)

model-management · inference · recommendation · personalization · music-analysis ·
audio-intelligence · local-inference · evaluation · testing — hepsi v2 §2.6'da ad olarak geçer;
kapsam tanımı **kademe-3'te** (ADR-097 K4) kilitlenir.

**K006 ↔ K016 ayrımı (registry §6):** K006 = **ML ürün yetenekleri** (öneri, ses analizi) ·
K016 = **ajan altyapısı**. İkisi de meşru, ayrı domain — çakışma sayılmaz.

## §3 Kademe-2 çocuk tablosu

Kaynak: v2 §2.6 mini-ağaç (satır 255–268) — **9 çocuk · 0 derinlik** (v2 daha derin göstermiyor).

| # | Çocuk (kademe-2) | v2 satır | Rol (kaynak) | Durum |
|---|---|---|---|---|
| 1 | model-management | 259 | ⚠️ VERIFICATION REQUIRED | draft |
| 2 | inference | 260 | ⚠️ VERIFICATION REQUIRED | draft |
| 3 | recommendation | 261 | ⚠️ VERIFICATION REQUIRED | draft |
| 4 | personalization | 262 | ⚠️ VERIFICATION REQUIRED | draft |
| 5 | music-analysis | 263 | ⚠️ VERIFICATION REQUIRED | draft |
| 6 | audio-intelligence | 264 | ⚠️ VERIFICATION REQUIRED | draft |
| 7 | local-inference | 265 | ⚠️ VERIFICATION REQUIRED | draft |
| 8 | evaluation | 266 | ⚠️ VERIFICATION REQUIRED | draft |
| 9 | testing | 267 | ⚠️ VERIFICATION REQUIRED | draft |

> Çocuk adları İngilizce teknik terimdir; anlam adından okunur — ama **resmi kapsam tanımı yoktur**,
> uydurma tanım yazılmaz. Her çocuk için ayrı künye: `<çocuk>/index.md».

## §4 Teknoloji / ekosistem

> Kural: **yalnız** görev manifestinde doğrulanmış kanıt yazılır; yoksa `UNKNOWN» (uydurma yasak).

| İddia | Kaynak | Tarih | Durum |
|---|---|---|---|
| K006 için manifest web kanıtı vermedi | A2 görev manifesti | 2026-10-12 | **UNKNOWN** |
| Model / runtime / framework seçimi | — | — | **UNKNOWN** |
| Yerel çalışma (local-inference) donanım gereksinimi | — | — | **UNKNOWN** |
| K006 ↔ K016-AI kapsam ayrımı kayıt altında | .ai/architecture/index.md §6 | 2026-10-12 | ✅ KAYITLI |

**§4 sonucu: UNKNOWN.** Bu paya teknoloji yazmak için yeni web doğrulaması gerekir;
kayıt yeri `yapay-zeka-dogrulama.md» §3'tür.

### §4.1 Kanıt yokluğunda davranış

| Durum | Davranış |
|---|---|
| Manifest kanıtı yoksa | satır uydurulmaz — `UNKNOWN» yazılır |
| Kanıt sonradan açılırsa | `yapay-zeka-dogrulama.md» §4'e URL + tarih ile işlenir |
| Komşu payın kanıtı | bu paya taşınmaz (Koel/Ampache/Spotify/Apple → K007–K010) |
| `status» etkisi | ölçüt 5 açık kaldıkça `draft» kalır |

## §5 Bağımlılık özeti

Tam tablo: `yapay-zeka-bagimliliklar.md». Özet — **hepsi TASARIM İDDİASI** (verified değil):

| Yön | Düğüm | Ne | Etiket |
|---|---|---|---|
| → depends_on | K007-data | öneri girdisi (etkileşim + katalog verisi) | TASARIM İDDİASI |
| → depends_on | K008-domain | "öneri" ve "beğeni" kurallarının tanımı | TASARIM İDDİASI |
| → depends_on | K012-services | inference'ı çalıştıracak süreç | TASARIM İDDİASI |
| ← used_by | K011-application | önerinin UI'da gösterilmesi | TASARIM İDDİASI |
| ← used_by | K016-AI | ajanların ML yeteneğini çağırması | TASARIM İDDİASI |
| ← used_by | K019-testing | model kalite / regresyon testleri | TASARIM İDDİASI |

## §6 Doğrulama özeti

| Ölçüt | Nasıl ölçülür | Sonuç |
|---|---|---|
| children = v2 §2.6 birebir (9/9) | satır 255–268 karşılaştırması | ✅ PASS · 2026-10-12 |
| master adı = registry §5 satır 6 | `ai-master.md» eşleşmesi | ✅ PASS |
| index.md 8 zorunlu alan | v2 §4.2 kontrolü | ✅ PASS (8/8) |
| Yeni dosya adları ADR-097 §5.1 | kebab-case · ≤3 kelime · Türkçe rol | ✅ PASS (4/4) |
| Web kanıtı §4'te | manifest kaydı | ❌ YOK → status = draft |
| Kademe-3 kapsamı | ADR-097 K4 | ⏳ açık |

## §7 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| 1 | `ai-master.md» adı registry §5'te K016-AI ile de geçiyor (satır 6 ve 16) — iki klasörde aynı dosya adı | ⚠️ VERIFICATION REQUIRED |
| 2 | recommendation üçlüsü: K006-recommendation · K008-recommendation · K020-recomenataion — sınır kimde? | ⚠️ VERIFICATION REQUIRED |
| 3 | local-inference donanım gereksinimi (K001/K004 ilişkisi) | ⚠️ VERIFICATION REQUIRED |
| 4 | §4 teknoloji ekosistemi UNKNOWN — hangi model/runtime? | ⚠️ VERIFICATION REQUIRED |
| 5 | 9 çocuğun kademe-3 (bounded-context) yapısı | ⚠️ VERIFICATION REQUIRED (ADR-097 K4) |

## §8 Kaynaklar

| Kaynak | Ne verdi |
|---|---|
| v2 §2.6 — `G:\Drive'ım\Projelerim\Project-notes\Bayram Ali Prompt Arşivi & Notlar\Coremusic\coremusic-katmanli-mimari-agaci-v2.md» (satır 251–268) | rol cümlesi + 9 çocuk |
| .ai/architecture/index.md §5 satır 6 · §6 · §8 | master adı · K006↔K016 kaydı · isimlendirme |
| .ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md §5 · K4 | isimlendirme · kademe 3–4 |
| .ai/architecture/README.md | çoklu-MD şeması |
| Web kanıtı | bu pay için **yok** → §4 UNKNOWN |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
