---
node_id: K003-audio/engine
node_type: subdomain
status: draft
parent_id: "K003-audio"
children: []
depends_on:
  - K000-isletim-sistemi
  - K001-donanım
evidence:
  - "v2 §2.3 mini-ağaç — ad kaynağı (KURAL 1: yazımda typo dahil korunur)"
  - "audio-master.md §3 satır 1 — 'Zinciri çalıştıran ana döngü ve yaşam döngüsü'"
  - "audio-master.md §4 A1–A6 — Apple pull-model + AUGraph yaşam döngüsü (fetch 2026-10-12)"
validation: "draft — D-kriterleri K003-audio/audio-dogrulama.md §4; bu çocuğun kanıtları engine-dogrulama.md §1"
open_questions:
  - "Q5 engine ↔ backend çocuk sınırı nedir? (pull model'de veri sahipliği) ⚠️"
  - "Q7 CoreMusic'in kendi yaşam döngüsü durum kümesi nedir? ⚠️"
  - "Ana döngü thread önceliği/süreci — UNKNOWN (kanıt yok)"
group: mimari
---

# K003-audio/engine — ELI15

Ses motorunun çekirdeği: oynatma zincirini başlatan, durduran ve döndüren ana
döngü. Tıpkı bir fabrikanın kalbi gibi — diğer K003 çocukları bu düğümden gelen
komutla çalışır. K004-dsp işleyicileri de zincire **bu döngünün içinde** düğüm
olarak eklenir; engine DSP'nin ne hesapladığını bilmez, yalnız "şimdi çalış"
der.

> ⚠️ VERIFICATION REQUIRED: Bu düğümün tanımı yalnızca v2 §2.3'teki **adından**
> türetilmiştir (v2 §3 KURAL 3); v2 çocukları içerik olarak tanımlamaz. Durum
> kümesi, thread modeli ve veri sahipliği kanıt turunda netleşecektir.

## Durum

| Alan | Değer |
|---|---|
| node_type | subdomain |
| parent | K003-audio |
| children | yok (yaprak) |
| status | draft |
| üst açık soru | Q5 (engine ↔ backend) · Q7 (durum kümesi) |
| Kapsam | Tanım bekliyor — kanıt turu-2 · `engine-kapsam.md` §2 |
| repo kodu | YOK (kök `*.cpp`/`*.h` glob 0 isabet · 2026-10-12) |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `engine-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| kod | `engine-ornekler.md` | Uygulama örnekleri ve kalıp gösterimi | Kademe-2 |
| kanit | `engine-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
