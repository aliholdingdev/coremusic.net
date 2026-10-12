---
node_id: K020-adr
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - templates
depends_on:
  - "K000–K5000 Architecture Registry"
evidence:
  - "v2 §2.20 mini-ağaç (K020-adr → index.md · decision-register.md · templates/) — G:/Drive'ım/Projelerim/Project-notes/Bayram Ali Prompt Arşivi & Notlar/Coremusic/coremusic-katmanli-mimari-agaci-v2.md (satır 541–546)"
  - ".ai/architecture/index.md §5 satır 78 (K020-adr · draft) · §6 satır 90 (6× K020 ID CONFLICT)"
  - ".ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md §2 K6 · §5"
validation:
  - "2026-10-12 — v2 §2.20 mini-ağaç ile üretim şekli birebir karşılaştırıldı (3 öğe: index.md · decision-register.md · templates/)"
  - "2026-10-12 — registry §5/§6 okundu; durum: draft"
open_questions:
  - "⚠️ ID CONFLICT: 6 klasör K020 önekinde paylaşır (adr/comilance/comere/licence/discovery/recomenataion) — ADR-097 §6 · v2 §3.1"
  - "Bu klasör hangi ADR serisine kayıt açar — karar serisi (.ai/.decisions/) mi, mimari seri (.ai/architecture/adr/) mi? (ADR-026 §3.4 ayrımı → decision-register.md §4)"
  - "decision-register.md disk sayımı (draft = 1 · ADR-097) ile .ai/.decisions/index.md §4A ('Taslak yok', total-draft: 0) uyuşmuyor → senkron gerekiyor"
---

# K020-adr — Karar Kayıt Defteri

## ELI15 — Bu klasör ne işe yarar?

Proje için alınmış **önemli kararların** ("neden bu yolu seçtik?") tek bir yerde,
tarih sırasıyla ve gerekçesiyle durduğu defterdir. Karar yoksa aynı tartışma
tekrar açılır; bu klasör o tartışmayı **bir kez** yapmak için vardır.

## Çocuk kataloğu (v2 §2.20 — birebir)

| Öğe | Tür | Rol | Durum |
|---|---|---|---|
| `decision-register.md` | dosya | ADR kayıt mekanizması: hangi seri, hangi şablon, disk durumu, kayıt akışı | draft |
| `templates/` | dizin | Şablon kataloğu iskeleti (`templates/index.md`) — Guardrail #16 işaretçisi | draft |

## Durum

- **status:** `draft` — şekil v2 ile birebir; içerik Band-1 üretim turunda yazıldı, validate gate **bekliyor**.
- **Yerel dosyalar:** `index.md` (bu dosya) · `decision-register.md` · `templates/index.md`.
- **Bağımlılık:** yalnız `K000–K5000 Architecture Registry` — yönetişim rafı olduğu için domain ağacına **aşağı** bakmaz.

## İlişkili kaynaklar

| Kaynak | Ne verir |
|---|---|
| `[[../index]]` (architecture/index.md) | Registry cetveli — §5 Band-1 tablosu, §6 çakışma kayıtları |
| `@.ai/.decisions/index.md` | Karar serisi indeksi (accepted/rejected/draft) |
| `[[../adr/index]]` | Mimari ADR serisi — karar serisiyle **ayrı** (ADR-026 §3.4) |
| `@.ai/.decisions/draft/ADR-097-…md` | K-Space V3 kurulum kararı + §6 açık sorular |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
