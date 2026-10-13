---
node_id: K020-comilance/verification
node_type: subdomain
status: draft
parent_id: K020-comilance
children: []
depends_on:
  - K020-comilance
  - controls
evidence:
  - "Web doğrulama tur-1 kaydı: .ai/.decisions/draft/ADR-097-…md §1.6 (2026-10-12, exa/websearch)"
  - ".ai/architecture/index.md §9 faz D — 'Web doğrulama turları → -dogrulama.md işlenmesi'"
validation:
  - "2026-10-12 — tur-1 kayıtları ADR-097 §1.6'da mevcut (JUCE/Koel/Ampache/TPA3255/ASIO/YouTube Music/Spotify/MusicKit...)"
open_questions:
  - "⚠️ ID CONFLICT: 6 klasör K020 önekinde paylaşır (adr/comilance/comere/licence/discovery/recomenataion) — ADR-097 §6 · v2 §3.1"
  - "Tur-2 henüz çalıştırılmadı → bu dizindeki tur satırları eksik"
group: mimari
---


# K020-comilance/verification — Doğrulama Turu Kayıtları

**ELI15:** "Ne zaman denetledik, ne bulduk?" sorusunun cevabı. Kayıtlar
**eklenerek** tutulur; eski satır değiştirilmez.

## Tur kayıtları

| Tur | Tarih | Kapsam | Sonuç | Kaynak |
|---|---|---|---|---|
| tur-1 | 2026-10-12 | Web doğrulama: JUCE · Koel · Ampache · TPA3255 · Signalsmith · ASIO SDK · YouTube Music · Spotify · MusicKit · Protobuf | sonuçlar ADR-097 §1.6'ya işlendi; 2 satır `⚠️ VERIFY REQUIRED` (DSPark "90+" · ASIO lisans etkisi) | `@.ai/.decisions/draft/ADR-097-…md` §1.6 |
| tur-2 | — | OWASP A01–A10 haritası + §16 hedeflerinin ölçümü | **çalıştırılmadı** | `⚠️ VERIFY REQUIRED` |
| tur-3 | — | KVKK/GDPR kapsam araştırması (kapsam tanımsız) | **çalıştırılmadı** | `⚠️ VERIFY REQUIRED` |

## Kayıt formatı (her tur için)

```text
| tur no | tarih | kapsam | yöntem (araç/kaynak) | sonuç | kanıt (dosya yolu / URL) | durum |
```

- **durum** değerleri: `geçti` · `kaldı` · `VERIFY REQUIRED`
- Kanıtsız sonuç **yazılmaz**; doğrulanamayan iddia `⚠️` ile işaretlenir
  (kök AGENTS.md §3 Zero-Hallucination).

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward
