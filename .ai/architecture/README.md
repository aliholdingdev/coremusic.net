---
title: "CoreMusic Mimari Ağacı — Nasıl Gezilir (ELI15)"
type: readme
category: architecture
date: 2026-10-12
updated: 2026-10-12
status: active
version: 1.0.0
authority: "SSOT: .ai/architecture/index.md"
group: mimari
---


# Bu klasör ne? (ELI15)

CoreMusic'in **5.000+ katmanlık mimari haritası**. Burada kod yok — **planlar, kararlar, bağımlılık çizelgeleri ve doğrulama kayıtları** var. Her klasör = mimarinin bir "odası"; her `index.md` o odanın kapısındaki künye.

## Hızlı tur (3 adım)

1. **`index.md`** → hangi slot var, hangisi rezerve, çakışmalar neler (kayıt defteri).
2. **`K0xx-*/index.md`** → o domain'in künyesi (8 zorunlu alan).
3. Oradan **`-master`/`-anayasa`** (ne işe yarar) → **`-bagimliliklar`** (kimlere muhtaç) → **`-dogrulama`** (kanıtlar + açık sorular).

## Bir domain klasöründe ne var? (çoklu-MD şeması)

```text
K003-audio/
├── index.md                ← künye: 8 zorunlu alan (v2 §4.2)
├── audio-master.md         ← anayasa: rol, sınır, çocuk tablosu (v2'deki ad)
├── audio-bagimliliklar.md  ← depends_on / used_by / entegrasyon sınırı (yeni)
├── audio-dogrulama.md      ← ölçütler, web-kanıtları, VERIFY REQUIRED (yeni)
├── engine/                 ← kademe-2 subdomain → içinde kendi index.md'si
├── routing/ …              ← v2'deki çocuk adları BİREBİR
└── …
```

## 3 kırmızı çizgi (v2 §3 + ADR-097)

1. **Mevcut isimler dokunulmaz** — `identitiy`, `integraion`, `comilance`, `recomenataion`, `donanaım-type`, `comere`… hepsi kaynaktaki gibi yazılır; düzeltme = onay ister. *(2026-10-18 notu: bu isimlerden `K0100-identitiy` ve `K020-comere` diskte **hiç yok** — registry'de `⚠️ VERIFY REQUIRED` kaydı olarak taşıyorlar, klasör üretilmedi — bkz `index.md` §6.2.)*
2. **Yeni isimler**: Türkçe + İngilizce teknik terim, kebab-case, ≤3 kelime, anlaşılır, tutarlı (`audio-routing`, `donanım-kernel`, `odeme-altyapisi` gibi).
3. **Bilinmeyen = `⚠️ VERIFY REQUIRED`** — uydurma dosya yolu, uydurma domain, uydurma star/yorum **yok**.

## Sayım iki türlü (karıştırma!)

| Sayım | Ne? | Değer |
|---|---|---|
| **K slotu** | üst seviye numara (K000…K5000) | **5.001** (20 tanımlı + **1 kapanan GAP** + 4.980 rezerve) |
| **Katman** | hiyerarşideki her node | hedef **6.720+** (4. kademede, v2 §4.6) |

**Diskteki gerçek K klasörü sayısı (2026-10-18): 22** — `K000`…`K016-observability` (17) + `K17-devops` + `K18-testing` + `K019-comilance` + `K020-discovery` + `K021-recomenataion`. İsim uyuşmazlığı kayıtları: `index.md` §6.2.

Band-2..7 (K021–K5000) **rezerve** — klasörleri yok; slotu ancak gerçek bir domain ADR ile isim alır (ADR-097 K5).

## Kurallar

| Dosya tipi | Kural |
|---|---|
| `index.md` | her düğümde zorunlu — §4.2 8 alan: `node_id · node_type · status · parent_id · children · depends_on · evidence · validation · open_questions` |
| `evidence` | yalnız gerçek dosya yolu / URL / test kaydı — tahmin yasak |
| `status` | `draft` · `verified` · `verify-required` · `conflict` |
| Kaynaklar | v1: `G:\Drive'ım\…\coremusic-katmanlı-miamri-oluştur.ai.md` · v2: `…\coremusic-katmanli-mimari-agaci-v2.md` |
| Karar | `.ai/.decisions/draft/ADR-097-…md` (onay bekliyor) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
