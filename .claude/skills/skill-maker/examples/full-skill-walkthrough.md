---
name: skill-maker-example-full-skill-walkthrough
description: "Example (not a skill) — full walkthrough: how the php-backend-standards skill was built end-to-end with skill-maker (requirements → template → SKILL.md → references → examples → validation)."
license: MIT
metadata:
  version: 1.0.0
  format: claude-skill-v3
  category: example
  updated: 2026-10-07
  subject-skill: php-backend-standards
---

# Full Skill Walkthrough — `php-backend-standards` ( uçtan uca üretim kaydı )

> Bu örnek, `php-backend-standards` skill'inin (paralel üretim) skill-maker v3.0 süreciyle
> **nasıl kurulduğunun** kaydıdır: gereksinim → şablon → SKILL.md → references → examples
> → doğrulama. Gerçek CoreMusic PHP içeriği kullanılır.

---

## 1. Gereksinim (ADIM 1)

```text
Kullanıcı: "php-backend-standards adında bir skill oluştur — CoreMusic backend kodunu
PHP 8.4 + raw PDO + API-first kurallarına göre denetlesin."
```

| Soru | Cevap |
|------|-------|
| Ad | `php-backend-standards` (folder = name, 1:1 — N6 ✓) |
| Ana görev | Backend kod denetimi (PHP 8.4) |
| Araçlar/API | php.net, OWASP Top 10:2025, vault `.ai/CLAUDE.md` §6/§21 |
| Çıktı | Denetim raporu (ihlal satırı + kural ref'i) |
| Şablon | `templates/php-skill-template.md` (ADIM 3) |

## 2. Web Research & Truth Mode (ADIM 2)

| İddia | Kaynak (≥2-3) | Sonuç |
|-------|---------------|-------|
| PHP 8.4 `declare(strict_types=1)` davranışı | php.net/language.types + sürüm notu | ✅ Verified |
| PDO prepared statement parameter binding | php.net/pdo + OWASP SQL Injection | ✅ Verified |
| OWASP Top 10:2025 kategorileri | owasp.org Top 10 (2025) + ikinci teyit | ✅ Verified |
| Spesifik deprecated PDO davranışı (tek kaynak) | 1 kaynak | ⚠️ `VERIFICATION REQUIRED` |

## 3. Şablon + Yapı (ADIM 3)

```text
.claude/skills/php-backend-standards/
├── SKILL.md                 ← php-skill-template.md kopyalandı → v3.0 frontmatter dolduruldu
├── references/
│   ├── middleware-pipeline.md   ← §6 sırası derinlemesine (neden bu sıra, H001 ihlalleri)
│   ├── pdo-rules.md             ← ADR-002: raw PDO, prepared zorunlu, SELECT * yasak
│   └── api-first.md             ← ADR-084: OpenAPI → DTO → Contract → Validation → kod
└── examples/
    └── audit-sample.md          ← örnek denetim: girdi kod → çıkan rapor (N5 tam döngü)
```

N1 kontrolü: şablon frontmatter'ında kök alan **yalnız** `name`/`description`/`license`/
`metadata` (format: claude-skill-v3); `title`/`type`/`version`/`triggers` yok.

## 4. SKILL.md (üretilen — çekirdek, Katman-2 orkestrasyon)

```markdown
---
name: php-backend-standards
description: "Use when auditing CoreMusic PHP backend code against PHP 8.4, raw PDO and API-first rules. Tetikleyici: 'php denetle', 'backend incele', 'PDO kontrol'."
license: MIT
metadata:
  version: 1.0.0
  format: claude-skill-v3
  author: Bayram Ali (ULTRATHINK Engineering)
  category: backend-orchestration
  tags: [php, pdo, api-first, truth-mode]
  updated: 2026-10-07
---

# php-backend-standards — CoreMusic Backend Denetim Standartları

## §1 Genel Bakış
(Atış: PHP 8.4 backend kodunu denetler; vault'la çelişen kod → DUR + rapor.)

## §2 Zorunlu Okumalar
| Dosya | Ne zaman okunur |
|-------|-----------------|
| references/middleware-pipeline.md | HTTP katmanı kodu denetlenirken |
| references/pdo-rules.md | SQL/erişim katmanı denetlenirken |
| references/api-first.md | Endpoint/contract kodu denetlenirken |

## §3 Örnekler
| examples/audit-sample.md | Tam denetim döngüsü (girdi kod → rapor) |

## §4 Otonom Çalışma Protokolü
1. Dosya/adres alınır → 2. Truth Mode (php.net/OWASP teyidi, teyitsiz = etiket) →
3. Kural taraması → 4. İhlal listesi (satır + kural ref'i) → 5. checklist raporu

## §5 Çekirdek Kurallar (bağlayıcı)
- **Middleware sırası DEĞİŞTİRİLEMEZ** (CLAUDE §6 / ADR-010…022):
  OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf
  → BypassAuth → Auth → Permission → Validation → Controller
- **raw PDO (ADR-002):** prepared statement zorunlu; ORM, `SELECT *`, string-concat SQL YASAK.
- **API-first (ADR-084):** endpoint kodu OpenAPI sözleşmesiz yazılmamış → ihlal.
- **Yasaklı kalıplar (§21):** `_csrf_token`, `innerHTML`, `eval()`, `var`, localStorage-auth.
- **Test-gate:** testi olmayan değişiklik = tamamlanmış değil.
- **Truth Mode:** teyitsiz iddia → `⚠️ VERIFICATION REQUIRED`; credential kodda YASAK.

Anti-overthink: kök AGENTS.md §5 (MAX THINKING 7 madde) geçerlidir.

*CoreMusic Skill v3.0 — metadata.version: 1.0.0 — Updated: 2026-10-07*
```

## 5. References (Katman-3 derinlik, N3)

- `middleware-pipeline.md` — her middleware'in görevi/sıra gerekçesi; sıra değişimi tespiti
  = `H001 REJECT`; CSP nonce üretimi SecurityHeaders(#4) → SessionManager(#5) bağımlılığı.
- `pdo-rules.md` — prepared-only, explicit kolon listesi (SELECT * yasak), transaction/
  error-mode kuralları; hepsi ADR-002 + `.ai/CLAUDE.md` §18/§21 ref'li.
- `api-first.md` — OpenAPI → DTO → Contract → Validation → Use Case → Kod zinciri;
  SPA'nın repository/PDO görmemesi (§6A.5) ihlali = rapor satırı.

## 6. Examples (N5 — girdi → çıktı tam döngü)

`examples/audit-sample.md`: girdi olarak 8 satırlık hatalı controller kodu
(`SELECT *` + string-concat SQL + eksik `strict_types` + sırasız middleware kaydı) →
çıktı olarak 4 satırlık ihlal raporu (satır no, ihlal, kural ref'i, düzeltme önerisi).

## 7. Doğrulama (ADIM 5 — checklist çıktısı)

```text
[x] Web'den ≥2-3 kaynak (php.net, OWASP) — 1 tek kaynaklı iddia ⚠️ etiketli
[x] Vault çelişkisi yok (middleware sırası, ADR-002/084 birebir)
[x] v3.0 iskelet tam: SKILL.md + references/ (3) + examples/ (1) — orphan yok (N4)
[x] Frontmatter kökü: name/description/license/metadata (N1) · description "Use when"+
    Türkçe tetikleyiciler ≤1024 (N2)
[x] SKILL.md satır hedefi ≤250 (N3) · klasör=name 1:1 (N6)
[x] MAX THINKING bloğu yok → tek satır anti-overthink (N7)
[x] CLAUDE.md sidecar yok (N8) · footer metadata.version ile (N9)
[x] Registry notu: .claude/CONTEXT.md envantere eklenecek (raporda)
```

**Sonuç raporu:** "`php-backend-standards` v1.0.0 (claude-skill-v3) hazır — 3 references +
1 example linkli, 1 kaynak `⚠️ VERIFICATION REQUIRED`, envanter onayı bekliyor."

---

*CoreMusic Skill v3.0 (full walkthrough) — Updated: 2026-10-07*