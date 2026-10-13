---
node_id: K004-dsp/limiter
node_type: subdomain
status: draft
parent_id: "K004-dsp"
children: []
depends_on:
  - K003-audio
evidence:
  - "v2 §2.4 mini-ağaç — ad kaynağı"
  - "dsp-master.md §3 satır 6"
validation: "draft — A1 tur-1; D-kriterleri K004-dsp/dsp-dogrulama.md §2"
open_questions:
  - "kabul eşiği (ceiling) değeri bilinmiyor ⚠️"
group: mimari
---

# K004-dsp/limiter — ELI15

Hiçbir şeyin belirli bir seviyeyi geçmesine izin vermeyen güvenlik kapısı. Amplifikatöre giden sinyali koruyan son sınır.

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
| `[[limiter-kapsam]]` | Kademe-2 kapsam kaydı (ceiling/güvenlik katmanı, R1/R3) |
| `[[limiter-dogrulama]]` | Kanıt tablosu (DSPark setCeiling · Signalsmith latency · Patina) |
| `[[limiter-ornekler]]` | Referans limiter API'leri + latency raporlama deseni |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `limiter-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| kod | `limiter-ornekler.md` | Uygulama örnekleri ve kalıp gösterimi | Kademe-2 |
| kanit | `limiter-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
