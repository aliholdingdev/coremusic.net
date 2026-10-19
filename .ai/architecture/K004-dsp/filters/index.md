---
node_id: K004-dsp/filters
node_type: subdomain
status: draft
parent_id: "K004-dsp"
children: []
depends_on:
  - K003-audio
evidence:
  - "v2 §2.4 mini-ağaç — ad kaynağı"
  - "dsp-master.md §3 satır 3"
validation: "draft — A1 tur-1; D-kriterleri K004-dsp/dsp-dogrulama.md §2"
open_questions:
  - "filtre tipi/sırası kararı (Butterworth vb.) kanıtsız ⚠️"
group: mimari
---

# K004-dsp/filters — ELI15

Sesin belirli frekanslarını geçiren ya da engelleyen temel yapı taşları (alçak geçiren, yüksek geçiren, bant geçiren). EQ'nun ve crossover'ın malzemesi.

> ⚠️ VERIFICATION REQUIRED: Bu düğümün tanımı yalnızca v2 §2.4'teki **adından**
> türetilmiştir; v2 çocukları içerik olarak tanımlamaz. Parametre, algoritma ve
> kabul kriterleri kanıt turunda netleşecektir.

## Durum

| Alan | Değer |
|---|---|
| node_type | subdomain |
| parent | K004-dsp |
| children | yok (yaprak) |
| status | draft |
| Kapsam | Tanım bekliyor — kanıt turu-2 |

## İlişkili dosyalar

| Dosya | Amaç |
|---|---|
| `[[filters-kapsam]]` | Kademe-2 kapsam kaydı (rol, sınır, disk kanıtları, açık sorular) |
| `[[filters-dogrulama]]` | Kanıt tablosu (Signalsmith filters.h · DSPark · Patina · repo) |
| `[[filters-ornekler]]` | Filtre çekirdekleri (Biquad/SVF/Ladder) referans örnekleri |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `filters-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| kod | `filters-ornekler.md` | Uygulama örnekleri ve kalıp gösterimi | Kademe-2 |
| kanit | `filters-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
