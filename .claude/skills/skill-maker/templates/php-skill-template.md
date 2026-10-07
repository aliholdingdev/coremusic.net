---
name: {SKILL_ADI}
description: "Use when {NE_ZAMAN} — PHP 8.4 backend kodunu denetler/geliştirir; raw PDO ve API-first kurallarını uygular. Tetikleyiciler: '{TETIKLEYICI_1}', '{TETIKLEYICI_2}'."
license: MIT
metadata:
  version: 1.0.0
  format: claude-skill-v3
  author: {YAZAR}
  category: backend-orchestration
  tags: [php, backend, pdo, truth-mode]
  updated: {YYYY-MM-DD}
---

# {SKILL_BASLIK} (PHP/Backend Agent)

## §1 Genel Bakış
{SKILL_DETAYLI_ACIKLAMA}

| Trigger | Ne zaman | Bu skill |
|---------|----------|----------|
| '{TETIKLEYICI_1}' | {NE_ZAMAN_1} | ✅ |
| '{TETIKLEYICI_2}' | {NE_ZAMAN_2} | ✅ |
| Vault `.md` kural yazımı | Kural değil, doküman | ❌ → `.ai/.templates/` (Guardrail #16) |

## §2 Zorunlu Okumalar

| Dosya | İçerik | Ne zaman okunur |
|-------|--------|-----------------|
| `references/middleware-pipeline.md` | Middleware sırası + gerekçeleri | HTTP katmanı kodunda |
| `references/pdo-rules.md` | Raw PDO, prepared, SELECT * yasağı | SQL/erişim katmanında |
| `references/api-first.md` | OpenAPI → DTO → kod zinciri (ADR-084) | Endpoint/contract kodunda |
| `references/checklist.md` | Kalite kontrol listesi | Teslim öncesi (ADIM 5) |

## §3 Örnekler

| Dosya | Ne gösterir |
|-------|-------------|
| `examples/{ORNEK_DOSYA}.md` | Hatalı kod → ihlal raporu (girdi → çıktı tam döngü) |

## §4 Otonom Web Research (Truth Mode)
Her kullanımda doğrulanır: PHP 8.4 özellikleri (php.net), OWASP Top 10:2025 (owasp.org),
şüpheli satır → `// ⚠️ VERIFICATION REQUIRED`; deprecated yapı reddedilir (H001).

## §5 CoreMusic PHP Kurallar (kesin — ihlal edilemez)
- `declare(strict_types=1);` eksikliği reddedilir.
- **Middleware sırası DEĞİŞTİRİLEMEZ** (CLAUDE §6): OriginCheck → Cors → RateLimiter →
  SecurityHeaders → SessionManager → Csrf → BypassAuth → Auth → Permission →
  Validation → Controller.
- Raw PDO (ADR-002) zorunlu; string-concat SQL / ORM / `SELECT *` reddedilir (SQLi - H001).
- `unserialize(user_input)` RCE → kesin reddedilir.
- `csrf_token` (asla `_csrf_token`); `innerHTML`, `eval()`, `var` yasaklı (§21).
- API-first: endpoint önce OpenAPI sözleşmesi (ADR-084) → DTO → kod.
- Secret/API key kodda/log'da asla yazılmaz (`.env`/vault).

## §6 Otonom Çalışma Protokolü
1. İstek alınır → 2. Gerekiyorsa php.net/OWASP web teyidi → 3. Hedef dosya analiz
→ 4. İhlal listesi (satır + kural ref'i) → 5. Sıfır halüsinasyon raporu.

Anti-overthink: kök AGENTS.md §5 (MAX THINKING 7 madde) geçerlidir.

*CoreMusic Skill v3.0 — metadata.version: 1.0.0 — Updated: {YYYY-MM-DD}*