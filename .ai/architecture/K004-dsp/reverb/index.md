---
node_id: K004-dsp/reverb
node_type: subdomain
status: draft
parent_id: "K004-dsp"
children: []
depends_on:
  - K003-audio
evidence:
  - "v2 §2.4 mini-ağaç — ad kaynağı"
  - "dsp-master.md §3 satır 7"
validation: "draft — A1 tur-1; D-kriterleri K004-dsp/dsp-dogrulama.md §2"
open_questions:
  - "oda modeli seçimi/ölçüm verisi yok ⚠️"
group: mimari
---

# K004-dsp/reverb — ELI15

Sesin bir odada nasıl yankılandığını taklit eden katman: kuru sese oda hissi ekler. 'Stüdyo kaydı' ile 'salon kaydı' arasındaki fark.

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
| `[[reverb-kapsam]]` | Kademe-2 kapsam kaydı (oda hissi, R1 oda-verisi) |
| `[[reverb-dogrulama]]` | Kanıt tablosu (Patina ReverbEngine · DSPark FDN · Signalsmith · repo) |
| `[[reverb-ornekler]]` | Referans reverb motorları (FDN + analog-modeling) + PHP roomProfile kanıtı |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `reverb-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| kod | `reverb-ornekler.md` | Uygulama örnekleri ve kalıp gösterimi | Kademe-2 |
| kanit | `reverb-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
