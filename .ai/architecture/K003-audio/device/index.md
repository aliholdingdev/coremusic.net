---
node_id: K003-audio/device
node_type: subdomain
status: draft
parent_id: "K003-audio"
children: []
depends_on:
  - K000-isletim-sistemi
  - K001-donanım
evidence:
  - "v2 §2.3 mini-ağaç — ad kaynağı (KURAL 1)"
  - "audio-master.md §3 satır 2 — 'Cihaz envanteri, seçimi ve değişimi'"
  - "audio-master.md §4 A1–A6 · §5 T6/T7/T13–T17 (fetch 2026-10-12)"
validation: "draft — D-kriterleri K003-audio/audio-dogrulama.md §4; bu çocuğun kanıtları device-dogrulama.md §1"
open_questions:
  - "Q6 input/output ↔ device/driver sorumluluk ayrımı ⚠️"
  - "Cihaz envanteri neyi listeler (format/kanal/destek kipleri)? — UNKNOWN"
  - "Sıcak değiştirme (hot-swap) sırası backend'i nasıl etkiler? — UNKNOWN"
group: mimari
---

# K003-audio/device — ELI15

Hangi ses kartının/hoparlörün/mikrofonun bağlı olduğunu listeleyen, doğru
cihazı seçip gerektiğinde değiştiren düğüm. Kulaklık takıp çıkarınca sesin
"hoparlöre gitmesi" ya da beklenmedik cihaza düşmesi bu düğümün kararına bağlı.
Tıpkı bir postanenin "hangi şubeye yönlendireyim" kararına benzer: adres
(envanter) burada, dağıtım (yönlendirme) ayrı bir düğümdür.

> ⚠️ VERIFICATION REQUIRED: Tanım v2 §2.3'teki **adından** türetilmiştir
> (v2 §3 KURAL 3); içerik iddiası değildir. Sıcak değiştirme davranışı ve
> envanter alanları kanıt turunda netleşecektir.

## Durum

| Alan | Değer |
|---|---|
| node_type | subdomain |
| parent | K003-audio |
| children | yok (yaprak) |
| status | draft |
| üst açık soru | Q6 (device/driver ↔ input/output ayrımı) |
| Kapsam | Tanım bekliyor — kanıt turu-2 · `device-kapsam.md` §2 |
| repo kodu | YOK (cihaz envanteri kodu repo'da yok · 2026-10-12) |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `device-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| kod | `device-ornekler.md` | Uygulama örnekleri ve kalıp gösterimi | Kademe-2 |
| kanit | `device-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
