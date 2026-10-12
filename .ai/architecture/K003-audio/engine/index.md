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
  - "v2 §2.3 mini-ağaç — ad kaynağı"
  - "audio-master.md §3 satır 1"
validation: "draft — A1 tur-1; D-kriterleri K003-audio/audio-dogrulama.md §2"
open_questions:
  - "engine ile backend arasındaki sınır nedir? (K003 Q5)"
---

# K003-audio/engine — ELI15

Ses motorunun çekirdeği: oynatma zincirini başlatan, durduran ve döndüren ana döngü. Tıpkı bir fabrikanın kalbi gibi — diğer çocuklar buradan gelen komutla çalışır.

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

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
