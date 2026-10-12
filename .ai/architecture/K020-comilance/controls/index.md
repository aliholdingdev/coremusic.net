---
node_id: K020-comilance/controls
node_type: subdomain
status: draft
parent_id: K020-comilance
children: []
depends_on:
  - K020-comilance
  - policies
evidence:
  - "OWASP Top 10:2025 resmi liste — https://top10.owasp.org/2025/en (okundu: 2026-10-12)"
  - "Vault kontrolleri: kök AGENTS.md §5 Security · .ai/AGENTS.md §16 · accepted/ADR-010, ADR-012, ADR-013, ADR-020, ADR-022, ADR-059, ADR-095, ADR-101"
validation:
  - "2026-10-12 — OWASP A01–A10 başlıkları resmi OWASP kaynağından doğrulandı"
open_questions:
  - "⚠️ ID CONFLICT: 6 klasör K020 önekinde paylaşır (adr/comilance/comere/licence/discovery/recomenataion) — ADR-097 §6 · v2 §3.1"
  - "Her kontrolün son denetim tarihi ve sonucu YOK → tüm satırlar denetlenmedi"
---

# K020-comilance/controls — Kontrol Haritası

**ELI15:** "Kural nasıl sağlanıyor?" cevabı — her satır bir kontrol ve **denetim durumu**; henüz denetlenmemiş satır `denetlenmedi` yazılır, `geçti` yazılmaz.

## OWASP Top 10:2025 haritası (kaynak: `top10.owasp.org/2025/en`)

| # | OWASP kategorisi | CoreMusic kontrolü (vault kanıtı) | Son denetim |
|---|---|---|---|
| A01 | Broken Access Control | auth middleware + rol doğrulama · `ADR-010` · `ADR-020` | denetlenmedi |
| A02 | Security Misconfiguration | CSP nonce (`ADR-012`) · `.env`-only secret · `ADR-022` | denetlenmedi |
| A03 | Software Supply Chain Failures | GitLeaks `secret-scan.yml` · `ADR-085` · bağımlılık lisansı → `../K020-licence/` | denetlenmedi |
| A04 | Cryptographic Failures | AES-256-GCM/ChaCha20 · Argon2id · MD5/SHA-1/DES yasak | denetlenmedi |
| A05 | Injection | parametreli sorgu zorunlu · `SELECT *` yasak · no ORM (`ADR-002`) | denetlenmedi |
| A06 | Insecure Design | `ADR-098` · `ADR-099` · güvenlik kanonik sırası (`ADR-101`) | denetlenmedi |
| A07 | Authentication Failures | `ADR-052/058` · JWT RS256 + MFA (`ADR-059/095`) | denetlenmedi |
| A08 | Software or Data Integrity Failures | outbox (`ADR-100`) · origin+CSRF (`ADR-094`) · migration denetimi | denetlenmedi |
| A09 | Security Logging and Alerting Failures | `log.md` append-only audit trail | denetlenmedi |
| A10 | Mishandling of Exceptional Conditions | fail-safe (fail-open değil) · `§17 Edge Cases` | denetlenmedi |

> 2025 farkı: A03 ve A10 **yeni** kategoriler; SSRF artık A01 kapsamındadır.

## Vault içi kalite kontrolleri (§16 — hedef, ölçüm DEĞİL)

| Alan | Hedef | Gerçek ölçüm |
|---|---|---|
| Backend · UI · Security · Data | %100 | `⚠️ VERIFY REQUIRED` |
| QA | ≥%80 coverage · %0 flaky | `⚠️ VERIFY REQUIRED` |
| DevOps | CI/CD ≥%95 · GitLeaks temiz | `⚠️ VERIFY REQUIRED` |

**Durum:** harita iskeleti · `children: []` · sonuç satırları `verification/`a yazılır.
