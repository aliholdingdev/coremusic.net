---
title: "K020-comilance — Compliance Master (Uyum Programı)"
type: master
category: architecture
node_id: K020-comilance/compliance-master
node_type: file
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
authority: "SSOT: .ai/CLAUDE.md §6 · .ai/AGENTS.md §16 · kök AGENTS.md §5 Security"
related:
  - "policies/index.md"
  - "controls/index.md"
  - "verification/index.md"
  - "../K020-adr/decision-register.md"
---

# K020-comilance — Compliance Master

## 1. Rol (ELI15)

Bu dosya, CoreMusic'in **hangi kurallara** uymayı taahhüt ettiğini, **hangi
kontrollerin** o kuralları sağladığını ve **hangi tarihte** denetlendiğini tek
sayfada gösterir. Uyum = kural + kontrol + kanıt; üçünden biri eksikse kayıt
`verify-required` kalır.

## 2. Kapsam / Kapsam dışı

| Kapsam | Kapsam dışı |
|---|---|
| Güvenlik uyumu (OWASP Top 10:2025 ↔ vault kontrolleri) | Bağımlılık **lisans** uyumu → `../K020-licence/` |
| Politika kayıtları ve denetim turları | Kod incelemesi (code-reviewer akışı) |
| KVKK/GDPR kapsamı **araştırması** (henüz tanımsız) | Sertifika iddiası — **hiçbiri yoktur, yazılmaz** |
| Uyum kanıtlarının saklanması (tarih + kaynak) | Runtime güvenlik testi (pentest) sonuçları |

## 3. Uyum alanları (katalog)

| # | Alan | Dayanak (gerçek dosya/kaynak) | Sorumlu | Durum |
|---|---|---|---|---|
| 1 | Güvenlik (OWASP) | kök `AGENTS.md` §5 Security · OWASP Top 10:2025 (`top10.owasp.org/2025/en`) | security-engineer | kontrol iskeleti — denetlenmedi |
| 2 | Kalite standartları | `@.ai/AGENTS.md` §16 (Backend/UI/Security/Data/Embedded/QA/DevOps hedefleri) | qa-engineer | ölçülmüş değer yok → VERIFY REQUIRED |
| 3 | Vault kuralları / LINT | `@.ai/CLAUDE.md` §6 + 16 Hard Guardrails (kök AGENTS.md §24.2) | MO (vault-updater) | iskelet |
| 4 | Politikalar | `policies/index.md` — politika MD'leri henüz yazılmadı | Vault Steward | iskelet |
| 5 | Bağımlılık lisansı | `../K020-licence/dependency-licenses.md` | dependency-manager | 1 açık kayıt (ASIO · GPLv3) |
| 6 | Gizli veri / secret yönetimi | kök AGENTS.md §5 (secret yalnız `.env`) · `.github/workflows/secret-scan.yml` (GitLeaks) | devops-engineer | pipeline dosyası var, çalıştığı doğrulanmadı |
| 7 | Veri koruma (KVKK/GDPR) | **kaynak yok** | Vault Steward | `⚠️ VERIFY REQUIRED — kapsam tanımsız` |

> **Not (zero-hallucination):** Bu tabloda "sertifikalıdır / uyumludur" cümlesi
> **yoktur**. Henüz yapılmamış denetim için sonuç yazılmaz.

## 4. Denetim döngüsü (adımlar)

```text
① Politika/gereksinim yaz (policies/) → ② Kontrolü ata (controls/)
        → ③ Denetle (verification/ turu) → ④ Sonucu kaydet (tarih + kaynak)
        → ⑤ Açık soru kaldıysa status: verify-required
        → ⑥ Karar gerektiriyorsa ADR aç (K020-adr → .ai/.decisions/draft/)
```

- **Kanal:** her tur `verification/index.md`'ye **satır eklenerek** yazılır (append-only).
- **Başarı ölçütü:** kanıt = gerçek dosya yolu / URL / tarih. Tahmin, "muhtemelen",
  "uyumlu olmalı" **yazımaz**.
- **Kapı:** bir kontrol `fail` ise ilgili alan `status: verify-required` → üst karar
  (`.ai/AGENTS.md` §10 eskalasyon).

## 5. Alt klasör kataloğu

| Alt klasör | Ne taşır | Dosya |
|---|---|---|
| `policies/` | "Ne kural?" — politika satırları + dayanak | `policies/index.md` |
| `controls/` | "Nasıl sağlanıyor?" — OWASP A01–A10 + vault kontrol haritası | `controls/index.md` |
| `verification/` | "Ne zaman denetlendi?" — tur kayıtları (2026-10-12 tur-1) | `verification/index.md` |

## 6. Kayıt kuralları

1. Yeni satır yalnız **kanıtla** eklenir (dosya yolu + tarih + kaynak).
2. Mevcut satır **düzeltilmez** — yanlışsa yeni satır + `⚠️` notu eklenir (ADR-042 satır-satır edit).
3. Kaynak çelişirse çelişki `open_questions`a taşınır, biri "kazandırılmaz".
4. `comilance` yazımı değişmez (v2 §3 KURAL 1).

## 7. Açık sorular (VERIFY REQUIRED)

| # | Soru | Etiket |
|---|---|---|
| 1 | Uyum kapsamı: KVKK · GDPR · ISO 27001 — hangisi geçerli? Tanım yok. | `⚠️ VERIFY REQUIRED` |
| 2 | `@.ai/AGENTS.md` §16 hedeflerinin (`%100`, `≥%80`, `≥%95`) son ölçümü ne? Rapor yok. | `⚠️ VERIFY REQUIRED` |
| 3 | `secret-scan.yml` / `ci.yml` gerçekten koşuyor mu (CI geçmişi kanıtı yok — kök AGENTS.md §25.2 notu) | `⚠️ VERIFY REQUIRED` |
| 4 | OWASP kontrol haritasındaki `fail`ler için ADR gerekir mi, yoksa düzeltme yeter mi? | `⚠️ VERIFY REQUIRED` |
| 5 | ⚠️ ID CONFLICT: 6 klasör K020 önekinde paylaşır (adr/comilance/comere/licence/discovery/recomenataion) — ADR-097 §6 · v2 §3.1 | `⚠️ VERIFY REQUIRED` |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

## 8. Ek — Rol → denetim haritası

| Denetçi (agent) | Ne denetler | Dayanak | Sıklık |
|---|---|---|---|
| `security-engineer` | OWASP A01–A10 karşılığı vault kontrolleri · auth/CSRF/CSP · crypto algoritması | kök AGENTS §5 · `.ai/AGENTS.md` §16 | her güvenlik değişikliğinde |
| `qa-engineer` | Test stratejisi, coverage, gate raporu | `.ai/AGENTS.md` §16 (≥%80) | her yayım öncesi |
| `data-engineer` | BCNF, parametreli sorgu, `SELECT *`/ORM yasağı | `.ai/AGENTS.md` §16 · `ADR-002/003` | şema değişikliğinde |
| `dependency-manager` | Paket/sürüm/lisans denetimi | `../K020-licence/` | yeni paket eklenince |
| `devops-engineer` | CI/CD, GitLeaks, secret taraması | `.github/workflows/secret-scan.yml` | her push |
| `MO (vault-updater)` | Vault LINT + Guardrail uyumu · log append-only | `@.ai/CLAUDE.md` §6 | her oturum kapanışında |

> Sıklıklar **süreç niyetidir**, ölçülmüş SLA **değildir** → `⚠️ VERIFY REQUIRED`.

## 9. Kayıt satırı formatı (policies/controls/verification uyumu)

```text
| tarih | alan (§3 no) | kontrol (§controls no) | yöntem | sonuç | kanıt (yol/URL) | durum | sorumlu |
```

- **İzleme (traceability):** her satır bir `policies/` politikasına ve bir
  `controls/` kontrolüne bağlanır; bağı olan satır **eksik kayıt** sayılır.
- **Sonuç değerleri:** `geçti` · `kaldı` · `VERIFY REQUIRED` — üçünden başka değer yok.

## 10. Sık yapılan hatalar (yasak — red team notu)

1. Denetim **yapılmadan** "uyumlu" yazmak → en ağır ihlal (kanıtsız iddia).
2. Eski satırı **düzeltip silmek** → append-only ihlali (ADR-042).
3. Çelişen iki kaynağı "biri doğru" diye birleştirmek → çelişki `open_questions`a gider.
4. Uyum alanını lisansla (`K020-licence`) karıştırmak → kapsam hatası.

