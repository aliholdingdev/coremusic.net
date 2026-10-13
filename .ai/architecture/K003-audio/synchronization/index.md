---
node_id: K003-audio/synchronization
node_type: subdomain
status: draft
parent_id: "K003-audio"
children: []
depends_on:
  - K000-isletim-sistemi
  - K001-donanım
evidence:
  - "v2 §2.3 mini-ağaç — ad kaynağı"
  - "audio-master.md §3 satır 10"
validation: "draft — A1 tur-1; D-kriterleri K003-audio/audio-dogrulama.md §2"
open_questions:
  - "senkronizasyon hedefi (ör. jitter eşiği) bilinmiyor ⚠️"
group: mimari
---

# K003-audio/synchronization — ELI15

Birden çok cihaz veya kaynak varsa saatlerini aynı ritimde tutma sorumluluğu: drift ölçümü ve telafisi. Orkestranın şefi.

> ⚠️ VERIFICATION REQUIRED: Bu düğümün tanımı yalnızca v2 §2.3'teki **adından**
> türetilmiştir; v2 çocukları içerik olarak tanımlamaz. Görev, arayüz ve kabul
> kriterleri kanıt turunda netleşecektir.

## Durum

| Alan | Değer |
|---|---|
| node_type | subdomain |
| parent | K003-audio |
| children | yok (yaprak) |
| status | draft |
| Kapsam | Tanım bekliyor — kanıt turu-2 |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `synchronization-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| kod | `synchronization-ornekler.md` | Uygulama örnekleri ve kalıp gösterimi | Kademe-2 |
| kanit | `synchronization-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
