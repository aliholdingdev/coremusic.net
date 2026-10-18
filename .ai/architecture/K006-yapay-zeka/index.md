---
title: "K006 Yapay Zeka — Architecture Index"
type: architecture-index
node_type: domain
node_id: K006
level: 2
group: mimari
parent_id: "K000–K5000 Architecture Registry"
status: active
depends_on:
  - K003-audio
  - K005-firmware
  - K007-data
  - K008-domain
  - K011-application
  - K012-services
  - K015-AI
  - K021-recomenataion
created: 2026-10-04
updated: 2026-10-18
---

> **Authority bu dosyadadır**: K006'nın tek authority kaynağıdır.
> Master + 5 kanıt dosyası (kapsam · bağımlılıklar · bileşenler · kararlar · evrensel-dil · test-stratejisi) + 2 kanıt kökü (doğrulama · riskler · web-arastirma).

# K006 Yapay Zeka — Mimari Authority

> **SSOT — Authority bu dosyadadır.** Alt klasör index'leri (`ai/*.md`) bu belgeyi bağlar.
> `AI/kapsam/` · `AI/bagimliliklar/` · `AI/bilesenler/` · `AI/kararlar/` · `AI/evrensel-dil/` — bunlar bu authority'nin altıdır.

**Domain yetki alanı:** yapay-zeka-altyapisi · ai-service · asistani-kimlik · veri-hazirligi · rag-pipeline · prompt-yonetimi · model-yonetimi · ongoru-ucu · agentic-ucu

## §1 — Authority Sınırları

| Konu | K006 Kapsamı | Sahibi |
|---|---|---|
| Veri kaynağı | ✗ | K007 |
| Model altyapısı | ✅ | K006 (K015 ayrı düğüm) |
| Prompt şablonu | ✅ | K006 |
| Asistan kimliği (Asistan Kimliği Profili dahil) | ✅ | K006 |
| AI karar / öneri / güven | ✅ | K006 |
| Servis API'si | ✅ | K006 → K012 |
| RAG pipeline | ✅ | K006 |
| AI test / doğrulama | ✅ | K006 test-stratejisi |
| AI güvenlik | ✅ | K006 → K009 |
| AI gözlemlenebilirlik (prompt/model logları) | ✅ | K006 → K016 |
| AI CI/CD pipeline | ✅ | K006 → K17 |
| AI compliance | ✅ | K006 → K019 |

## §2 — Mimari Dosyalar

| # | Dosya | Tür |
|---|---|---|
| 1 | `K006-yapay-zeka/index.md` | Bu belge — authority |
| 2 | `K006-yapay-zeka/yapay-zeka-master.md` | Master belge — K006 tek authority belgesi |
| 3 | `K006-yapay-zeka/yapay-zeka-kapsam.md` | Kapsam |
| 4 | `K006-yapay-zeka/yapay-zeka-bagimliliklar.md` | Bağımlılıklar |
| 5 | `K006-yapay-zeka/yapay-zeka-bilesenler.md` | Bileşenler |
| 6 | `K006-yapay-zeka/yapay-zeka-kararlar.md` | Kararlar |
| 7 | `K006-yapay-zeka/yapay-zeka-evrensel-dil.md` | Evrensel Dil |
| 8 | `K006-yapay-zeka/kod/kod-ornekleri.md` | Kod örnekleri |
| 9 | `K006-yapay-zeka/kod/test-stratejisi.md` | AI test stratejisi |
| 10 | `K006-yapay-zeka/dogrulama/` | Doğrulama kanıtları (kök) |
| 11 | `K006-yapay-zeka/riskler/` | Risk kanıtları (kök) |
| 12 | `K006-yapay-zeka/web-arastirma/` | Web araştırma kanıtları (kök) |

## §3 — Kapsam Tanımı (Yapay Zeka)

| Alan | K006 Kapsamı | Sahibi |
|---|---|---|
| Model altyapısı | ✅ | K006 (K015 ayrı düğüm) |
| Prompt şablonu | ✅ | K006 |
| Asistan kimliği (Asistan Kimliği Profili dahil) | ✅ | K006 |
| AI karar / öneri / güven | ✅ | K006 |
| Servis API'si | ✅ | K006 → K012 |
| AI gözlemlenebilirlik (prompt/model logları) | ✅ | K006 → K016 |
| AI CI/CD pipeline | ✅ | K006 → K17 |
| AI compliance | ✅ | K006 → K019 |
| AI güvenlik | ✅ | K006 → K009 |

## §4 — Kaynaklar

| # | Dosya | Tür |
|---|---|---|
| 1 | `yapay-zeka-master.md` | Master belge |
| 2 | `yapay-zeka-kapsam.md` | Kapsam |
| 3 | `yapay-zeka-bagimliliklar.md` | Bağımlılıklar |
| 4 | `yapay-zeka-bilesenler.md` | Bileşenler |
| 5 | `yapay-zeka-kararlar.md` | Kararlar |
| 6 | `yapay-zeka-evrensel-dil.md` | Evrensel Dil |
| 7 | `kod/kod-ornekleri.md` | Kod örnekleri |
| 8 | `kod/test-stratejisi.md` | Test stratejisi |
| 9 | `riskler/index.md` | Risk kanıtları (kök) |

## §5 — İlişkiler (Master Belge Doğrulaması)

- **Authority:** `yapay-zeka-master.md` bu domainin **tek authority belgesidir**.
- **Doğrulama:** `yapay-zeka-master.md` satır 16: "Authority: `yapay-zeka-master.md` — tek authority belgesi."
- **Bağımlılıklar:** `yapay-zeka-master.md` satır 17: `depends_on: [K003-audio, K005-firmware, K007-data, K008-domain, K011-application, K012-services, K015-AI, K021-recomenataion]`
- **Authority sınırı:** `yapay-zeka-master.md` satır 18: "K006 authority'si: asistan kimliği, prompt şablonu, karar akışı, model entegrasyonu."
- **Kapsam sınırı:** `yapay-zeka-master.md` satır 19: "K006 kapsam dışı: veri kaynağı (K007) · model altyapısı (K015) · servis API'si (K012)."
- **AI test:** `yapay-zeka-master.md` satır 176: "AI test: `K006-yapay-zeka/kod/test-stratejisi.md`" — AI testlerinin bu dosyada tutulduğunu doğrular.
- **AI gözlemlenebilirlik:** `yapay-zeka-master.md` satır 265: "AI gözlemlenebilirlik (prompt/model logları)" — K016'ya devredilen sınır.

## §6 — Kaynaklar (Root Dizin, 12 dosya — doğrulandı 2026-10-18)

| # | Dosya | Tür | Grup |
|---|---|---|---|
| 1 | `index.md` | Bu belge — authority | mimari |
| 2 | `yapay-zeka-master.md` | Master belge | mimari |
| 3 | `yapay-zeka-kapsam.md` | Kapsam | mimari |
| 4 | `yapay-zeka-bagimliliklar.md` | Bağımlılıklar | mimari |
| 5 | `yapay-zeka-bilesenler.md` | Bileşenler | mimari |
| 6 | `yapay-zeka-kararlar.md` | Kararlar | mimari |
| 7 | `yapay-zeka-evrensel-dil.md` | Evrensel Dil | mimari |
| 8 | `kod/kod-ornekleri.md` | Kod örnekleri | kod |
| 9 | `kod/test-stratejisi.md` | AI test stratejisi | kod |
| 10 | `dogrulama/` | Doğrulama kanıtları | kanit |
| 11 | `riskler/` | Risk kanıtları | kanit |
| 12 | `web-arastirma/` | Web araştırma kanıtları | kanit |

## §7 — Master Belge Referansları (Zero-Hallucination)

| Master Belge | Referanslanan K006 Dosyaları | Satır |
|---|---|---|
| `yapay-zeka-master.md` | `yapay-zeka-master.md` (Authority: tek authority belgesi) | 16 |
| `yapay-zeka-master.md` | `yapay-zeka-kapsam.md` (K006 kapsamı: asistan kimliği, prompt şablonu, karar akışı, model entegrasyonu) | 18–19 |
| `yapay-zeka-master.md` | `yapay-zeka-kararlar.md` (AI karar akışı) | 18 |
| `yapay-zeka-master.md` | `kod/test-stratejisi.md` (AI test) | 176 |
| `yapay-zeka-master.md` | `yapay-zeka-bagimliliklar.md` (depends_on tanımı) | 17 |

**Zero-hallucination doğrulaması:** Bu referanslar master belgeden okunmuştur (2026-10-18). Uydurma referans yoktur.

---

**Authority:** `yapay-zeka-master.md` — K006 tek authority belgesi.
**Şablon:** `.ai/.templates/` — zero-hallucination referanslı.
## §BAĞLANTILAR

> **Yön işareti = kanıt yönü, sözleşme değil:** `→` bu K'nın kök içerik dosyaları hedefe referans veriyor · `←` hedef K'nın kök dosyaları bu K'ya referans veriyor · `↔` ikisi de · `⚠️` kök taramada kanıt yok.
> **Yöntem (2026-10-18):** her K'nın kök .md'leri (index.md hariç) taranıp hedef K adının toplam geçişi sayıldı (zero-hallucination; uydurma bağımlılık yok). Zenginleşik 4 tablo (K003/K008/K011/K012) korunmuştur.

| Hedef K | Wiki-link | Göreli dosya yolu | Yön | İlişki | Kanıt |
|---|---|---|---|---|---|
| K006-yapay-zeka | [[K006-yapay-zeka]] | ./K006-yapay-zeka/index.md | ○ | bu K (kendi kökü) | registry §5 + disk |
| K000-isletim-sistemi | [[K000-isletim-sistemi]] | ../K000-isletim-sistemi/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K001-donanım | [[K001-donanım]] | ../K001-donanım/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K002-electroncis | [[K002-electroncis]] | ../K002-electroncis/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K003-audio | [[K003-audio]] | ../K003-audio/index.md | → | bağımlı (depends_on) | M→3 · M←0 (kök .md, 2026-10-18) |
| K004-dsp | [[K004-dsp]] | ../K004-dsp/index.md | → | bu K referanslıyor | M→4 · M←0 (kök .md, 2026-10-18) |
| K005-firmware | [[K005-firmware]] | ../K005-firmware/index.md | ⚠️ | bağımlı (depends_on) | M→0 · M←0 (kök .md, 2026-10-18) |
| K007-data | [[K007-data]] | ../K007-data/index.md | ↔ | bağımlı (depends_on) | M→7 · M←5 (kök .md, 2026-10-18) |
| K008-domain | [[K008-domain]] | ../K008-domain/index.md | → | bağımlı (depends_on) | M→7 · M←0 (kök .md, 2026-10-18) |
| K009-security | [[K009-security]] | ../K009-security/index.md | → | bu K referanslıyor | M→4 · M←0 (kök .md, 2026-10-18) |
| K010-network | [[K010-network]] | ../K010-network/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K011-application | [[K011-application]] | ../K011-application/index.md | → | bağımlı (depends_on) | M→3 · M←0 (kök .md, 2026-10-18) |
| K012-services | [[K012-services]] | ../K012-services/index.md | ↔ | bağımlı (depends_on) | M→8 · M←13 (kök .md, 2026-10-18) |
| K013-integraion | [[K013-integraion]] | ../K013-integraion/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K014-media | [[K014-media]] | ../K014-media/index.md | ← | karşı taraf referanslıyor | M→0 · M←1 (kök .md, 2026-10-18) |
| K015-AI | [[K015-AI]] | ../K015-AI/index.md | ↔ | bağımlı (depends_on) | M→35 · M←8 (kök .md, 2026-10-18) |
| K016-observability | [[K016-observability]] | ../K016-observability/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K019-comilance | [[K019-comilance]] | ../K019-comilance/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K020-discovery | [[K020-discovery]] | ../K020-discovery/index.md | ← | karşı taraf referanslıyor | M→0 · M←1 (kök .md, 2026-10-18) |
| K021-recomenataion | [[K021-recomenataion]] | ../K021-recomenataion/index.md | ← | bağımlı (depends_on) | M→0 · M←7 (kök .md, 2026-10-18) |
| K17-devops | [[K17-devops]] | ../K17-devops/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K18-testing | [[K18-testing]] | ../K18-testing/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | index.md | Bu authority belgesi — K006 authority + §BAĞLANTILAR + §GRUPLAR | 2 |
| mimari | yapay-zeka-master.md | Master authority belgesi — K006 authority | 2 |
| mimari | yapay-zeka-kapsam.md | Kapsam — K006 kapsamı: asistan kimliği, prompt şablonu, karar akışı, model entegrasyonu | 2 |
| mimari | yapay-zeka-bagimliliklar.md | Bağımlılıklar — depends_on + bağımlılık nedenleri | 2 |
| mimari | yapay-zeka-bilesenler.md | Bileşenler — bileşen envanteri | 2 |
| mimari | yapay-zeka-kararlar.md | Kararlar — AI karar akışı kararları | 2 |
| mimari | yapay-zeka-evrensel-dil.md | Evrensel dil — AI domain terimleri | 2 |
| kod | kod/kod-ornekleri.md | Kod örnekleri — AI kod örnekleri | 2 |
| kod | kod/test-stratejisi.md | AI test stratejisi — AI model/pipeline test senaryoları | 2 |
| kanit | dogrulama/ | Doğrulama kanıtları (kök) | 2 |
| kanit | riskler/ | Risk kanıtları (kök) | 2 |
| kanit | web-arastirma/ | Web araştırma kanıtları (kök) | 2 |

## §BAĞLANTILAR

> **Okuma kuralı:** `←` işareti **hedef K'nın kendi dizinini kasteder** (`K00X-*/index.md` veya o dizindeki bir belge) — yani "K00X burada K006'dan bahsediyor" anlamına gelir; K006'nın kendi alt klasör belgeleri değil.
> **Yön semantiği:** `↔` = iki yönlü referans kanıtı var · `→` = yalnız bu K'nın belgeleri hedefi anıyor · `←` = yalnız hedef K'nın dizini bu K'yı anıyor · `⚠️` = arama yapıldı, iki yönde de kanıt yok.
> **Kanıt standardı:** Gerçek dosya adı + satır numarası; uydurma referans yok.

| Hedef K | Wiki-link | Göreli dosya yolu | Yön | İlişki | Kanıt |
|---|---|---|---|---|---|
| K000-isletim-sistemi | `[[K000-isletim-sistemi/index]]` | `../K000-isletim-sistemi/index.md` | ⚠️ | — | iki yönde kanıt yok |
| K001-donanım | `[[K001-donanım/index]]` | `../K001-donanım/index.md` | ← | ai-master.md satır 109: "K001-donanım — AI mikrofon/donanım" | `K001-donanım/index.md` L109 |
| K002-electroncis | `[[K002-electroncis/index]]` | `../K002-electroncis/index.md` | ⚠️ | — | iki yönde kanıt yok |
| K003-audio | `[[K003-audio/index]]` | `../K003-audio/index.md` | ↔ | ai-master.md L108/L216 "K003-audio — AI müzik anlama" + K003 tablosu L114 "K006-yapay-zeka — AI asistan altyapısı (prompt/model/agent)" | `ai-master.md` 108,216 · `K003-audio/index.md` 114 |
| K004-dsp | `[[K004-dsp/index]]` | `../K004-dsp/index.md` | → | ai-master.md 108/216: "K003-audio · K004-dsp — AI müzik anlama" | `ai-master.md` 108,216 |
| K005-firmware | `[[K005-firmware/index]]` | `../K005-firmware/index.md` | → | ai-master.md 108/216: "K005-firmware" AI sınırı · audio-intelligence kapsamları 34, 296 | `ai-master.md` 108,216 · `audio-intelligence/audio-intelligence-kapsam.md` 34,296 |
| K007-data | `[[K007-data/index]]` | `../K007-data/index.md` | ↔ | ai-master.md L102/213 "K007-data — veri kaynağı · K006 bağımlı" + K007 index.md `depends_on: [K006-yapay-zeka]` | `ai-master.md` 102,213 · `K007-data/index.md` 23 · `K007-data/data-bagimliliklar.md` 74 |
| K008-domain | `[[K008-domain/index]]` | `../K008-domain/index.md` | → | ai-master.md L103/L214: "K008-domain — AI domain/model referansları" | `ai-master.md` 103,214 · `K008-domain/index.md` 31,93 · `K008-domain/domain-master.md` 188 |
| K009-security | `[[K009-security/index]]` | `../K009-security/index.md` | → | ai-master.md satır 106/109 "K009-security — AI güvenlik" + satır 109: "AI güvenlik: K006-yapay-zeka/K009-security" | `ai-master.md` 106,109 |
| K010-network | `[[K010-network/index]]` | `../K010-network/index.md` | ⚠️ | — | iki yönde kanıt yok |
| K011-application | `[[K011-application/index]]` | `../K011-application/index.md` | → | ai-master.md 107/217: "K011-application — AI UI/UX · K006 sorumlu" | `ai-master.md` 107,217 |
| K012-services | `[[K012-services/index]]` | `../K012-services/index.md` | ↔ | ai-master.md 105/215 "K012-services — AI servis API'si (gateway)" + K012 ai-service-kapsam.md L19,35,53 "asistan Kimlik Profili" K006 alanına referans verir | `ai-master.md` 105,215 · `ai-service-anayasa/ai-service-kapsam.md` 19,35,53 |
| K013-integraion | `[[K013-integraion/index]]` | `../K013-integraion/index.md` | ⚠️ | — | iki yönde kanıt yok |
| K014-media | `[[K014-media/index]]` | `../K014-media/index.md` | ← | `K014-media/media-master.md` satır 59 "K006-yapay-zeka — AI asistan altyapısı" + `K014-media/metadata/index.md` satır 76 "K006-yapay-zeka — AI asistan altyapısı" | `media-master.md` 59 · `metadata/index.md` 76 |
| K015-AI | `[[K015-AI/index]]` | `../K015-AI/index.md` | ↔ | ai-master.md §2.2 L112: "K015-AI — ayrı düğüm" + yapay-zeka-bagimliliklar.md L18: "K015-AI — model altyapısı" · K015 ai-bagimliliklar.md L44,200,253: "K006-yapay-zeka — model altyapısı bağımlılığı" | `ai-master.md` 112 · `yapay-zeka-bagimliliklar.md` 18 · `K015-AI/ai-bagimliliklar.md` 44,200,253 |
| K016-observability | `[[K016-observability/index]]` | `../K016-observability/index.md` | ⚠️ | — | iki yönde kanıt yok |
| K17-devops | `[[K17-devops/index]]` | `../K17-devops/index.md` | ⚠️ | — | iki yönde kanıt yok |
| K18-testing | `[[K18-testing/index]]` | `../K18-testing/index.md` | ⚠️ | — | iki yönde kanıt yok |
| K019-comilance | `[[K019-comilance/index]]` | `../K019-comilance/index.md` | ← | `K019-comilance/controls/index.md` satır 93: "K006-yapay-zeka — yapay zeka" | `controls/index.md` 93 |
| K020-discovery | `[[K020-discovery/index]]` | `../K020-discovery/index.md` | ← | `K020-discovery/discovery-bagimliliklar.md` satır 18/52: "K006-yapay-zeka — asistan kimliği profili (K020 içinde referanslanır)" | `discovery-bagimliliklar.md` 18,52 |
| K021-recomenataion | `[[K021-recomenataion/index]]` | `../K021-recomenataion/index.md` | ← | `K021-recomenataion/index.md` satır 72: "K006-yapay-zeka — AI asistan altyapısı · K021 bağımlı" + `recommendation-master.md` L109,250: "K006-yapay-zeka — asistan kimliği profili" | `index.md` 72 · `recommendation-master.md` 109,250 |
| **K006-yapay-zeka (kendi)** | `[[K006-yapay-zeka/index]]` | `index.md` | ○ | Kendi kendine bağımlılık yok. | Bu belge (bu satır) |

**Authority:** `yapay-zeka-master.md` (Master authority belgesi — bu index authority'si tamamlar).
