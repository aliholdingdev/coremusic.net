---
node_id: K003-audio/mixing
node_type: subdomain
status: draft
parent_id: "K003-audio"
children: []
depends_on:
  - K000-isletim-sistemi
  - K001-donanım
evidence:
  - "v2 §2.3 mini-ağaç — ad kaynağı"
  - "audio-master.md §3 satır 8"
validation: "draft — A1 tur-1; D-kriterleri K003-audio/audio-dogrulama.md §2"
open_questions:
  - "mixing kazanç/klip koruma kriteri bilinmiyor ⚠️"
group: mimari
---

# K003-audio/mixing — ELI15

Birden çok ses kaynağını tek bir akışta birleştirme: seviyelerin toplanması, kanalların eşlenmesi. Kalabalık bir odada herkesin aynı anda duyulması.

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
| mimari | `mixing-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| kod | `mixing-ornekler.md` | Uygulama örnekleri ve kalıp gösterimi | Kademe-2 |
| kanit | `mixing-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
