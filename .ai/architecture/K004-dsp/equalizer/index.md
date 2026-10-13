---
node_id: K004-dsp/equalizer
node_type: subdomain
status: draft
parent_id: "K004-dsp"
children: []
depends_on:
  - K003-audio
evidence:
  - "v2 §2.4 mini-ağaç — ad kaynağı"
  - "dsp-master.md §3 satır 2"
validation: "draft — A1 tur-1; D-kriterleri K004-dsp/dsp-dogrulama.md §2"
open_questions:
  - "bant sayısı/eşik değerleri tanımlı değil ⚠️"
group: mimari
---

# K004-dsp/equalizer — ELI15

Frekans bantlarını ayrı ayrı yükselip alçaltan düzenek. 'Bass'ı biraz aç' cümlesinin karşılığı; sesin rengini değiştiren ana araç.

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
| `[[equalizer-kapsam]]` | Kademe-2 kapsam kaydı (rol, sınır, disk kanıtları, açık sorular) |
| `[[equalizer-dogrulama]]` | Kanıt tablosu (JUCE · Signalsmith · Patina · DSPark · repo) |
| `[[equalizer-ornekler]]` | Gerçek repo kodu (AIEngine 31-band EQ) + bibliyotek örnekleri |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `equalizer-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| kod | `equalizer-ornekler.md` | Uygulama örnekleri ve kalıp gösterimi | Kademe-2 |
| kanit | `equalizer-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
