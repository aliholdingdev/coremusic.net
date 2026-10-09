---
type: architecture
category: layer
title: "K012 — İzleme"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K012 — İzleme

## §1 Kimlik
- Katman: K012 · Alan: **A4** (K12-K15 — Veri/Entegrasyon).
- Kapsam: gözlemlenebilirlik — log, redaction, hata modeli, uzak çağırı güvenliği.

## §2 Sorumluluk
1. Log stdout'a JSON formatında; hassas veri `[REDACTED]` ile maskeleme (`.ai/AGENTS.md` §17.3).
2. Yapısal loglama (`StructuredLogger`) — PageRouter hattı.
3. Tutarlı hata modeli; hata gizleme (plan §4-8, §5.5).
4. Uzak çağrılara (3. parti API, CDN) timeout + sınırlı retry + fallback ZORUNLU (plan §5.6, §8-4).
5. Katman ihlali/audit kaydı: revert + log ERROR (plan §4, AGENTS §18.6).

## §3 Bağlantılar (Dependency Rule)
- **Alt:** K000 (stdout/barındırma zemini).
- **Üst:** cross-cutting — K006–K015 hepsi bu katmanı çağırır (bağımlılık yine yalnız alt zemine; [[katman-baglilik-matrisi]] §2).

## §4 ADR Bağlantıları
| ADR | Başlık |
|---|---|
| ADR-006 | Performance Targets (ölçüm hedefleri) |

Log/redaction'e özel ADR: **YOK** (`.ai/.decisions/index.md`'de log başlıklı ADR bulunmadı) → politika **UNKNOWN**.

## §5 Durum
**IMPLEMENTED** — kanıt (2026-10-09): `shared/src/PageRouter/StructuredLogger.php` · redaction kuralı AGENTS §17.3 · log stdout kuralı plan §5.6.

## §6 Risk / Not
- Log formatı/retention/sampling politikası ayrı dokümante edilmemiş → **UNKNOWN**.
- Redaction'ın tüm log yollarında uygulandığı kod taramasıyla doğrulanmadı.
