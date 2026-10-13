---
node_id: K004-dsp/crossover
node_type: subdomain
status: draft
parent_id: "K004-dsp"
children: []
depends_on:
  - K003-audio
evidence:
  - "v2 §2.4 mini-ağaç — ad kaynağı"
  - "dsp-master.md §3 satır 9"
validation: "draft — A1 tur-1; D-kriterleri K004-dsp/dsp-dogrulama.md §2"
open_questions:
  - "bölme frekansları hoparlör tasarımına bağlı — veri yok ⚠️"
group: mimari
---

# K004-dsp/crossover — ELI15

Tek bir sesi frekansa bölüp doğru hoparlöre yollar: bass'ı woofer'a, tizi twitter'a. Bölme noktası ve eğim burada tanımlanır.

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

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `crossover-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| kod | `crossover-ornekler.md` | Uygulama örnekleri ve kalıp gösterimi | Kademe-2 |
| kanit | `crossover-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
