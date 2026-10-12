---
node_id: K020-recomenataion
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - ranking
  - personalization
  - evaluation
depends_on:
  - "K000–K5000 Architecture Registry"
  - K020-discovery
evidence:
  - "v2 §2.20 mini-ağaç (K020-recomenataion → index.md · recommendation-master.md · ranking/ · personalization/ · evaluation/) — G:/Drive'ım/Projelerim/Project-notes/Bayram Ali Prompt Arşivi & Notlar/Coremusic/coremusic-katmanli-mimari-agaci-v2.md (satır 577–584)"
  - ".ai/architecture/index.md §5 satır 85 (draft) · §6 satır 92 (imla: 'recomenataion' KORUNUR)"
  - "İlgili vault kararları: accepted/ADR-030 (AI strategy) · ADR-025 (professional EQ) · ADR-029 (listening rooms)"
validation:
  - "2026-10-12 — v2 §2.20 şekli birebir karşılaştırıldı (5 öğe)"
open_questions:
  - "⚠️ ID CONFLICT: 6 klasör K020 önekinde paylaşır (adr/comilance/comere/licence/discovery/recomenataion) — ADR-097 §6 · v2 §3.1"
  - "Öneri motoru verisi nereden gelecek — yerel çalma geçmişi mi, dış servis (K020-discovery) mi? Tasarım yok → VERIFY REQUIRED"
  - "'recomenataion' yazımı kaynağa sadık korunur; 'recommendation' yapılmaz (v2 §3 KURAL 1)"
---

# K020-recomenataion — Öneri (Recommendation) Rafı

## ELI15 — Bu klasör ne işe yarar?

"Senin için ne çalsam?" sorusunun **nasıl** cevaplanacağının planıdır: sıralama
(`ranking/`), kişiselleştirme (`personalization/`) ve **işe yaradığını ölçme**
(`evaluation/`). Henüz algoritma/veri yok — burada iskelet ve kapılar durur.

## Çocuk kataloğu (v2 §2.20 — birebir)

| Öğe | Tür | Rol | Durum |
|---|---|---|---|
| `recommendation-master.md` | dosya | Öneri programının anayasası: girdi, alt sistemler, ölçüm, kapılar | draft |
| `ranking/` | dizin | Sıralama alt bölgesi (`ranking/index.md`) | draft |
| `personalization/` | dizin | Kişiselleştirme alt bölgesi (`personalization/index.md`) | draft |
| `evaluation/` | dizin | Öneri değerlendirmesi/metricleri (`evaluation/index.md`) | draft |

## Durum

- **status:** `draft` — şekil birebir; **algoritma, veri seti ve metrik tanımı yok**.
- **depends_on:** registry + `K020-discovery` (öneri, keşif/katalog verisi olmadan çalışmaz).
- **Kapı:** öneri tasarımı, veri/gizlilik onayı olmadan **üretilmez** (KVKK kapsamı tanımsız — `K020-comilance` açık sorusu).

## İlişkili kaynaklar

| Kaynak | Ne verir |
|---|---|
| `[[recommendation-master]]` | Girdi/çıktı + alt sistem + ölçüm tablosu |
| `[[../K020-discovery/index]]` | Dış kaynak keşfi (YouTube Music API yok · Spotify PKCE · Apple JWT) |
| `@.accepted/ADR-030` (AI strategy) | YZ stratejisi çerçevesi |
| `@.accepted/ADR-029` (listening rooms) · `ADR-025` (EQ) | Sosyal/EQ tarafındaki ilgili kararlar |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
