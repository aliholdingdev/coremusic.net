---
title: "CoreMusic — K-Space V3 Architecture Registry (K000–K5000)"
type: index
category: architecture
date: 2026-10-12
updated: 2026-10-12
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
source_of_truth: "v1 coremusic-katmanlı-miamri-oluştur.ai.md + v2 coremusic-katmanli-mimari-agaci-v2.md"
related: [".ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md", "README.md"]
---

# CoreMusic — K-Space V3 Architecture Registry

## §1 Amaç

Bu dosya, `K000–K5000 Architecture Registry`nin **tek kayıt defteridir**: hangi slot var, hangi slot rezerve, hangi domain hangi MD setini taşıyor, çakışmalar neler. Tek başına okunabilir; her düğümün kendi klasöründeki `index.md`'si bu deftere bağlıdır.

## §2 Okuma Sırası

1. `README.md` (ELI15 nasıl gezilir)
2. `.ai/.decisions/draft/ADR-097-…md` (kurulum kararları + isimlendirme)
3. Bu dosya (registry cetveli)
4. İlgili `K0xx-*/index.md` → oradan `-anayasa`/`-master` → `-bagimliliklar` → `-dogrulama`

## §3 Yapı (v2 §1.1 — birebir)

```text
architecture/
├── index.md · README.md
├── adr/ (index.md · templates/ · decisions/)
├── diagrams/ (5 diyagram)
├── K000-isletim-sistemi … K019-testing   (Band-1)
├── K020-adr · comilance · comere · licence · discovery · recomenataion  (6× K020)
└── Band-2..7 slotları → §4 cetveli (klasör AÇILMAZ, yalnız aralık)
```

## §4 Band Kayıt Cetveli — 5.001 slot (v2 §4.7 + ADR-097 K5)

| Bant | Aralık | Slot | Durum |
|---|---|---|---|
| Band-1 | K000 – K020 | 21 | ✅ TANIMLI — 19 tekil domain (K002 GAP) + 6× K020 = **25 klasör** |
| Band-2 | K021 – K120 | 100 | ⏸ RESERVED · ADR-097 (klasör yok — slot aralığı tescilli) |
| Band-3 | K121 – K320 | 200 | ⏸ RESERVED · ADR-097 |
| Band-4 | K321 – K720 | 400 | ⏸ RESERVED · ADR-097 |
| Band-5 | K721 – K1520 | 800 | ⏸ RESERVED · ADR-097 |
| Band-6 | K1521 – K3120 | 1.600 | ⏸ RESERVED · ADR-097 |
| Band-7 | K3121 – K5000 | 1.880 | ⏸ RESERVED · ADR-097 |
| **TOPLAM** | K000 – K5000 | **5.001** | 20 tanımlı + 1 GAP + 4.980 rezerve |

> **Kural (ADR-097 K5):** rezerve slot, ancak **gerçek bir domain** ADR ile tanımlanınca isim alır → `K0xx-<ad>/` klasörü o zaman açılır. Uydurma isim/klasör **yasak** (v2 §4.7).

## §5 Band-1 Domain Kayıt Tablosu (25 klasör)

| # | Klasör | Master dosyası (v2/ad) | Ek MD (yeni) | Durum |
|---|---|---|---|---|
| 1 | K000-isletim-sistemi | os-master.md | -bagimliliklar · -dogrulama | draft |
| 2 | K001-donanım | donanım-anayasa.md (yeni) | -bagimliliklar · -dogrulama | draft |
| 3 | K003-audio | audio-master.md | -bagimliliklar · -dogrulama | draft |
| 4 | K004-dsp | dsp-master.md | -bagimliliklar · -dogrulama | draft |
| 5 | K005-firmware | firmware-master.md | -bagimliliklar · -dogrulama | draft |
| 6 | K006-yapay-zeka | ai-master.md | -bagimliliklar · -dogrulama | draft |
| 7 | K007-data | data-master.md | -bagimliliklar · -dogrulama | draft |
| 8 | K008-domain | domain-master.md | -bagimliliklar · -dogrulama | draft |
| 9 | K009-security | security-master.md | -bagimliliklar · -dogrulama | draft |
| 10 | K010-identitiy | identity-master.md | -bagimliliklar · -dogrulama | draft |
| 11 | K011-application | application-master.md | -bagimliliklar · -dogrulama | draft |
| 12 | K012-services | services-master.md | -bagimliliklar · -dogrulama | draft |
| 13 | K013-integraion | integration-master.md | -bagimliliklar · -dogrulama | draft |
| 14 | K014-network | network-master.md | -bagimliliklar · -dogrulama | draft |
| 15 | K015-media | media-master.md | -bagimliliklar · -dogrulama | draft |
| 16 | K016-AI | ai-master.md | -bagimliliklar · -dogrulama | draft |
| 17 | K017-observability | observability-master.md | -bagimliliklar · -dogrulama | draft |
| 18 | K018-devops | devops-master.md | -bagimliliklar · -dogrulama | draft |
| 19 | K019-testing | testing-master.md | -bagimliliklar · -dogrulama | draft |
| 20 | K020-adr | decision-register.md + templates/ | v2 şekli birebir | draft |
| 21 | K020-comilance | compliance-master.md + policies/ controls/ verification/ | v2 şekli birebir | draft |
| 22 | K020-comere | scope-verification.md (İÇERİK BOŞ) | — | **verify-required** |
| 23 | K020-licence | dependency-licenses.md · license-verification.md | v2 şekli birebir | draft |
| 24 | K020-discovery | search/ · agent/ | v2 şekli birebir | draft |
| 25 | K020-recomenataion | recommendation-master.md + ranking/ personalization/ evaluation/ | v2 şekli birebir | draft |

## §6 Çakışma ve GAP Kayıtları (hepsi VERIFY REQUIRED — onaysız düzeltilmez)

| Kayıt | Tür | Durum |
|---|---|---|
| `K002` hiç yok (K001 → K003 atlıyor) | ID GAP | `⚠️ VERIFY REQUIRED` — v2 §3.1 |
| 6 klasör aynı `K020` önekinde (adr/comilance/comere/licence/discovery/recomenataion) | ID CONFLICT | `⚠️ VERIFY REQUIRED` — v2 §3.1 |
| `K020-comere` = ne olduğu bilinmiyor | UNKNOWN | `⚠️ VERIFY REQUIRED` — içerik BOŞ, tahmin yasak |
| `donanaım-type` · `identitiy` · `integraion` · `comilance` · `recomenataion` yazımları | imla (kaynak-sadık) | ✅ KORUNUR (v2 §3 KURAL 1) |
| v2 §6'nın 23 eski-disk çakışması (K003-ses-motoru vs K003-audio…) | tarihsel | ℹ️ Eski disk silindi/git yok — kayıt arşiv olarak taşınır |
| K006-yapay-zeka (ML ürün) ↔ K016-AI (ajan altyapısı) | kapsam ayrımı | ✅ v2 §2.6/§2.16'ya göre ikisi de meşru, ayrı domain |

## §7 Katman Sayımı (v2 §4.6 — hedef, disk verisi değil)

| Kademe | Ne sayılır | Çarpan (VARSAYIM) | Kademede | Kümülatif |
|---|---|---|---|---|
| 1 | Domain (K) | 21 slot | 21 | 21 |
| 2 | Subdomain | ×11 | 231 | 252 |
| 3 | Bounded Context | ×4 | 924 | 1.176 |
| 4 | Module | ×6 | 5.544 | **6.720 ✅** |

- **K SLOTU** (5.001) ≠ **KATMAN** (node) — ayrı sayılır.
- Gerçek sayı doldukça disk ölçümüyle güncellenir; `11·4·6` varsayımı uydurma veri sayılmaz.
- `K1000+` 4-hane biçimi ve `K021+` A0–A5 etiketi → ADR-097 §6 açık sorular.

## §8 İsimlendirme (özet — tam metin ADR-097 §5)

- v2'de adı geçen her şey **birebir** (imla dahil).
- Yeni adlar: kebab-case · ≤3 kelime · **Türkçe domain + İngilizce teknik terim** · anlaşılır · tutarlı (aynı kavram = aynı ad).
- `index.md` değişmez; yeni roller Türkçe: `-anayasa.md` · `-bagimliliklar.md` · `-dogrulama.md` · `-kapsam.md` · `-bilesenler.md`.

## §9 Üretim Durumu

| Faz | İş | Durum |
|---|---|---|
| A | ADR-097 (draft) + kök dosyalar | ✅ 2026-10-12 |
| B | Band-1 üretimi (25 K × multi-MD + kademe-2 index'leri) | 🔄 5/6 ajan bitti (A1 ✅72/72 dosya · A2 ✅69 · A3 ✅71 · A5 ✅21 · A6 ✅5 — disk doğrulandı: A1 payı 72 dosya ✓, dolu-satır ölçümü 3.210 / A1 raporu 4.007 satır (boş-satır dahil) · A4 (K016–K019) arka planda · disk: 287 dosya |
| C | Kademe 3–4 genişletme (5.000+ katman) | ⏳ |
| D | Web doğrulama turları → `-dogrulama.md` işlenmesi | 🔄 tur-1 tamam, tur-2 sürüyor |
| E | validate gate + final rapor + ADR onayı | ⏳ |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
