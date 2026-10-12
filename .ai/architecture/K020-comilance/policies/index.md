---
node_id: K020-comilance/policies
node_type: subdomain
status: draft
parent_id: K020-comilance
children: []
depends_on:
  - K020-comilance
evidence:
  - "v2 §2.20 — K020-comilance/ policies/ (alt dosya listesi yok → children: [])"
  - "Dayanaklar gerçek vault dosyaları: kök AGENTS.md §5 · .ai/AGENTS.md §16 · .ai/CLAUDE.md §6"
validation:
  - "2026-10-12 — her satır için dayanak dosyası mevcutluğu doğrulandı"
open_questions:
  - "⚠️ ID CONFLICT: 6 klasör K020 önekinde paylaşır (adr/comilance/comere/licence/discovery/recomenataion) — ADR-097 §6 · v2 §3.1"
  - "KVKK/GDPR politikası var mı? → VERIFY REQUIRED (politika MD'si diskte yok)"
---

# K020-comilance/policies — Politika Kayıt İskeleti

**ELI15:** "Bu projede **ne kural** var?" sorusunun cevap listesi. Politikanın
kendisi vault'ta yaşar; burada yalnız **kayıt satırı** vardır.

## Politika kaydı

| # | Politika | Dayanak (gerçek dosya) | Sorumlu | Durum |
|---|---|---|---|---|
| 1 | Güvenlik alt sınırı — AES-256-GCM/ChaCha20 · MD5/SHA-1/DES yasak · auth + rol · rate limit · fail-safe · secret yalnız `.env` | kök `AGENTS.md` §5 Security (OWASP Top 10:2025) | security-engineer | kural var · denetim yok |
| 2 | Kod kalite standartları (strict_types/PSR-12 · ITCSS/BEM/WCAG 2.2 AA · BCNF/no ORM · zero-allocation · test ≥%80 · CI ≥%95) | `@.ai/AGENTS.md` §16 | qa-engineer | hedef var · ölçüm yok |
| 3 | Vault LINT + 16 Hard Guardrail | `@.ai/CLAUDE.md` §6 (işaret: kök AGENTS.md §24.2) | MO | işaretçi |
| 4 | Domain sınırı (dosya tipi → sorumlu agent) | `@.ai/AGENTS.md` §5 Domain Boundaries | MO | kural var |
| 5 | Ekran/uygulama erişilebilirliği (WCAG 2.2 AA · touch ≥24px · focus 3px) | kök AGENTS.md §5 UI/UX | ui-designer | kural var · denetim yok |
| 6 | KVKK / GDPR veri politikası | **kaynak yok** | Vault Steward | `⚠️ VERIFY REQUIRED` |

## Durum

- `node_type: subdomain` · `children: []` (v2'de alt liste yok).
- **Politika MD'leri henüz üretilmedi** — bu sayfa katalog iskeletidir; uydurma politika metni yazılmaz.

**Authority:** Bayram Ali / Vault Steward
