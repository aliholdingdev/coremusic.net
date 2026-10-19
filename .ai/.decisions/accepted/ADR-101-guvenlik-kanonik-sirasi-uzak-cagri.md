---
reference_doc: .ai/.templates/adr/adr-template.md (v2.0.2 — Guardrail #16)
title: "CoreMusic — Güvenlik Kanonik Sırası + Uzak Çağrı Sınırları (Timeout/Retry/Fallback)"
type: adr
category: decisions
date: 2026-10-09
updated: 2026-10-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/architecture/coremusic-mimari-plani.md §4/§5.5/§8.4 · .ai/reports/2026-10-09-50-yillik-mimari-ilkeler-arastirmasi.md"
---

# CoreMusic — Güvenlik Kanonik Sırası + Uzak Çağrı Sınırları

**Durum:** active (Kullanıcı onayı "devam" 2026-10-09 · Senior review şartı: iki farklı sıra tek kaynağa indirildi)
**Tarih:** 2026-10-09
**Karar Veren:** Bayram Ali / Vault Steward
**İlgili ADR'ler:** [[accepted/ADR-094-api-pipeline-origin-csrf]] · [[accepted/ADR-013-rate-limiting-apcu]] · [[accepted/ADR-012-csp-nonce-strict-dynamic]] · [[accepted/ADR-010-csrf-protection-strategy]] · [[accepted/ADR-011-session-management]] · [[accepted/ADR-056-auth-module-implementation]]

---

## 1. Bağlam (Context)

Review, iki farklı "bağlayıcı" güvenlik sırası tespit etti (plan §4 vs §5.5 — rate-limit
auth öncesi/sonrası belirsiz). OWASP savunma derinliği: tek katmana asla güvenilmez; sıra
**tek kaynaktan** okunmalıdır. Ayrıca ağın 8 saçmalığı (Deutsch/Gosling 1994): her uzak
çağrı potansiyel başarısızdır.

## 2. Karar (Decision)

### 2.1 Kanonik istek sırası (tek kaynak — bu ADR)

```
1. OriginCheck + koşullu CSRF (ADR-094)
2. Rate limiting APCu (ADR-013)          ← auth'tan ÖNCE (kötü niyetli yükü ucuz reddet)
3. CSP nonce strict-dynamic (ADR-012)
4. Auth: session + JWT RS256 hybrid (ADR-052/095; bypass = ADR-008)
5. Route → controller → domain service → repository (PDO prepared, ADR-002)
6. Yanıt: output escape (XSS) · hata gizleme · log stdout + [REDACTED] redaction
```

Bu sıradan sapma ihlaldir; §4/§5.5 gibi ikinci bir sıra tanımı artık yazılmaz.

### 2.2 Uzak çağrı sınırları

3. parti API / CDN / uzak servis çağrılarında **timeout zorunlu**, retry sınırlı (max 3, üstel
geri çekilme), fallback zorunlu; API yanıt modeli asla "her zaman 200" kabul edilmez.

## 3. Sonuçlar (Consequences)

- ✅ Sıra tek SSOT'ta; middleware implementasyonu (PSR-15 ×11 — shared/src/Middleware/) buna göre denetlenir.
- ✅ Rate-limit'in auth öncesi oluşu kötü niyetli trafiği en ucuz katmanda reddeder.
- ⚠️ Mevcut middleware zincirinin bu sırayla birebir örtüşüp örtüşmediği **UNKNOWN** —
  security-engineer denetimi ister (kod keşfi).
- Not: "8 saçmalık sahipliği" Deutsch/Gosling (1994) — Ousterhout atfı doğrulanamadı
  (rapor §8).

## 4. Reddedilenler

- Tek katmanlı güvenlik (yalnız CSRF veya yalnız validator).
- Sınırsız retry / timeout'suz uzak çağrı.

---

*ADR-101 — CoreMusic Vault · Mode: Red Team · Human Mode · Truth Mode*
