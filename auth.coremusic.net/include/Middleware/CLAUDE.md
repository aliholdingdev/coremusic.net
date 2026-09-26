---
title: "CoreMusic - C:\www\coremusic.net\auth.coremusic.net\include\Middleware Baglam"
type: context
folder: "C:\www\coremusic.net\auth.coremusic.net\include\Middleware"
category: layer3
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
---

# Middleware - CLAUDE.md

**Zorunlu Baglantilar:** . [[../CLAUDE.md]]

## 1. Baglam
Middleware zinciri

## 2. Mevcut Durum
| Durum | Deger |
|-------|-------|
| Dosya | 6 |
| Konum | C:\www\coremusic.net\auth.coremusic.net\include\Middleware |

## 2.1 Uyarı — ölü kod şüphesi
- `SecurityHeadersMiddleware.php` — ⚠️ ölü kod şüphesi — ADR-012 şart 1a, tek kaynak `shared/` (`shared/src/Middleware/SecurityHeadersMiddleware.php`)
- Kanıt: `$request['server']['csp_nonce']` hiçbir yerde yazılmıyor (0 yazma) + `style-src 'self' 'unsafe-inline'` ihlali + sınıf pipeline'a kayıtlı değil
- Kod değişikliği YOK (Backend Architect işi) — bu not yalnızca belge kaydı (2026-09-24): [[../../../.ai/.decisions/accepted/ADR-012-csp-nonce-strict-dynamic]]

## 3. Komsu Iliskiler
| Yon | Hedef | Iliski |
|-----|-------|--------|
| Parent | [[../CLAUDE.md]] | Ust baglam |
| Talimatlar | [[../AGENTS.md]] | Ust kurallar |

## 4. Degisiklik Protokolu
1. Degisiklik once ust talimatlarla uyum kontrolu
2. Gerekirse ADR + [[../../.ai/log.md]] audit

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
