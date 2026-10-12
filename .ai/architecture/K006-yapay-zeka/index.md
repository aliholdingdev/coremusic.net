---
node_id: K006-yapay-zeka
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - model-management
  - inference
  - recommendation
  - personalization
  - music-analysis
  - audio-intelligence
  - local-inference
  - evaluation
  - testing
depends_on:
  - K007-data
  - K008-domain
  - K012-services
evidence:
  - "v2 §2.6 — coremusic-katmanli-mimari-agaci-v2.md satır 251–268"
  - ".ai/architecture/index.md §5 satır 6 (master = ai-master.md)"
validation:
  - "2026-10-12 — children listesi v2 mini-ağaçı ile karşılaştırıldı: 9/9 birebir"
open_questions:
  - "ai-master.md adı registry §5'te K016-AI ile de geçiyor (satır 6 ve 16) → ⚠️ VERIFICATION REQUIRED"
  - "depends_on satırları TASARIM İDDİASIdır; kanıt yok → -bagimliliklar.md §6'ya bak"
  - "Çocukların rol tanımları v2'de yok → ⚠️ VERIFICATION REQUIRED (kademe-3 · ADR-097 K4)"
---

# K006-yapay-zeka — Domain Künyesi

## ELI15 rol (v2 §2.6 — birebir)

> **Şarkı öneren, sesi analiz eden yapay zeka parçaları.**

## Çocuk kataloğu (kademe-2)

v2 §2.6 bu çocuklara **yalnızca ad verir**; hiçbiri için rol tanımı yok → hepsi `⚠️ VERIFICATION REQUIRED».

| # | Çocuk | v2 satır | Durum |
|---|---|---|---|
| 1 | model-management | 259 | draft |
| 2 | inference | 260 | draft |
| 3 | recommendation | 261 | draft |
| 4 | personalization | 262 | draft |
| 5 | music-analysis | 263 | draft |
| 6 | audio-intelligence | 264 | draft |
| 7 | local-inference | 265 | draft |
| 8 | evaluation | 266 | draft |
| 9 | testing | 267 | draft |

Her çocuğun kendi `index.md»'si bu klasörde vardır (kademe-2 künyesi, `children: []»).

## Dosyalar (çoklu-MD şeması — ADR-097 K2)

| Dosya | Rol |
|---|---|
| `index.md» | bu künye (8 zorunlu alan) |
| `ai-master.md» | anayasa — v2'deki ad, dokunulmaz |
| `yapay-zeka-bagimliliklar.md» | depends_on / used_by / entegrasyon sınırı |
| `yapay-zeka-dogrulama.md» | ölçütler · açık sorular · web-kanıt kayıtları |

## Durum

| Alan | Değer |
|---|---|
| status | draft (registry §5 satır 6) |
| Web kanıtı | bu pay için YOK → master §4 = UNKNOWN |
| K006 ↔ K016-AI sınırı | kayıt altında — registry §6 (ML ürün vs ajan altyapısı) |
| Açık sorular | 3 · hepsi master §7 ve -dogrulama §3'te |

> **Etiket notu:** frontmatter `depends_on» satırları **TASARIM İDDİASI**dır — `verified» değildir;
> kanıtlanınca -dogrulama §6 üzerinden durum güncellenir.
