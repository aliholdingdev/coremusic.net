---
node_id: K004-dsp/pipeline
node_type: subdomain
status: draft
parent_id: "K004-dsp"
children: []
depends_on:
  - K003-audio
evidence:
  - "v2 §2.4 mini-ağaç — ad kaynağı"
  - "dsp-master.md §3 satır 1"
validation: "draft — A1 tur-1; D-kriterleri K004-dsp/dsp-dogrulama.md §2"
open_questions:
  - "pipeline sırası statik mi kullanıcı tanımlı mı? (K004 Q3)"
group: mimari
---

# K004-dsp/pipeline — ELI15

Sesin hangi sırayla işleneceğini tarif eden bant: önce filtre mi, sonra kompresör mü — ve her basamağın girdisi kimden gelir. Montaj hattının şeması.

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
| `[[pipeline-kapsam]]` | Kademe-2 kapsam kaydı (rol, sınır, disk kanıtları, açık sorular) |
| `[[pipeline-dogrulama]]` | Kanıt tablosu (JUCE · Signalsmith · Patina · DSPark · repo) |
| `[[pipeline-ornekler]]` | Gerçek repo kodu + resmi bibliyotek örnekleri |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `pipeline-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| kod | `pipeline-ornekler.md` | Uygulama örnekleri ve kalıp gösterimi | Kademe-2 |
| kanit | `pipeline-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
