---
type: architecture
category: layer
title: "K015 — Medya"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K015 — Medya

## §1 Kimlik
- Katman: K015 · Alan: **A4** (K12-K15).
- Kapsam: medya arşivi, ingest hattı ve teslim (`media.coremusic.net`).

## §2 Sorumluluk
1. Dizin ekseni + ULID kimliği (ADR-092): hedef şablonu `media/_inbox/{YYYY-AA-GG}/{slug}-{ULID}.{uzanti}`.
2. Ingest hattı: tarama → aday üretimi → `Ulid::uret()` → hedefe yazma → CSV audit.
3. Download servisi (ADR-026), dual-mode storage (ADR-027), anti-ban (ADR-028).
4. Medya veri şeması (`.ai/.sql/mysql/media_catalog.sql`).
5. Uzak teslimde timeout + sınırlı retry + fallback (plan §5.6).

## §3 Bağlantılar (Dependency Rule)
- **Alt:** K005 (medya DB) → K000.
- **Üst (çağıran):** K011 UX (oynatma/arşiv arayüzü) · K009 API (medya uçları) · K003 (oynatma kaynağı).

## §4 ADR Bağlantıları
| ADR | Başlık |
|---|---|
| ADR-092 | Medya Arşivi Dizin Ekseni ve ULID Kimliği |
| ADR-026 / ADR-027 | Download Service · Dual-Mode Storage |
| ADR-028 | Anti-Ban System |

## §5 Durum
**IMPLEMENTED** — kanıt (2026-10-09): `media.coremusic.net/bin/ingest.php` (`use Media\Ulid;` L38, `Ulid::uret()` L211, hedef şablonu L254-256) · `media.coremusic.net/composer.json` · `.ai/.sql/mysql/media_catalog.sql`.

## §6 Risk / Not
- `Media\Ulid` sınıfının kaynağı/şartnamesi okunmadı → iç implementasyon **UNKNOWN**.
- Anti-ban/dual-mode (ADR-027/028) uygulama kanıtı bu görevde aranmadı → **UNKNOWN**.
