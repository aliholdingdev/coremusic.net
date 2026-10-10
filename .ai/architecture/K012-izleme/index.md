---
type: architecture
category: layer
title: "K012 — İzleme"
date: 2026-10-09
status: active
version: 2.0.1
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
## §7 Makro Katman Karşılığı
K012-izleme → Makro **cross-cutting — K0–K4'e girmez** (bağlayıcı gruplama, 2026-10-10) · Kök eşleme: [[../00-master-index]] (Makro Eşleme bölümü) · makro özet: [[../katmanli-mimari-k0-k4/index]]

- K012, makro K0–K4 hiyerarşisinin **dışındadır**: tüm makro katmanları keser; bağımlılığı yalnız en dip zemine (K000) indirgenir (makro özet §6.1 "Cross-cutting" satırı; matris §2: "K012 · K013 (cross-cutting) → K000 · tüm katmanları kapsar").
- Etki alanı: K006–K015 hepsi bu katmanı çağırır (§3) — log, redaction, hata modeli, uzak çağırı güvenliği ortak hizmettir.
- Kardeş cross-cutting: K013 CI/CD. İkisi için de **özel ADR YOK** → politika UNKNOWN (makro özet §6.1, §7-13).

## §8 Arayüz (Girdi/Çıktı & API)
- **Girdi (yanlardan):** K006–K015 katmanlarından log/olay kayıtları; katman ihlali/audit kayıtları (§2.5: revert + log ERROR, AGENTS §18.6).
- **Çıktı (aşağıya):** stdout'a JSON log akışı — zemin K000 (matris §2 cross-cutting satırı).
- **Diskte kanıtlı somut varlıklar (§5, 2026-10-09 ölçümü):**

| Varlık | Gerçek yol | Rol |
|---|---|---|
| `StructuredLogger` | `shared/src/PageRouter/StructuredLogger.php` | Yapısal loglama — stdout JSON |
| Redaction kuralı | `.ai/AGENTS.md` §17.3 (`[REDACTED]` maskeleme) | Hassas veri maskeleme sözleşmesi |
| Ölçüm hedefleri | ADR-006 (Performance Targets) | K012 ADR bağlantısı (§4) |

- **İzinli/yasaklı çağrı özeti:** K012 → yalnız K000 (stdout/barındırma zemini); üst katmanlara bağımlılık/dairesel kenar YASAK (makro özet §2, §6.1). Uzak çağırı kuralı bu katmanın sözleşmesidir: 3. parti API/CDN çağrılarına **timeout + sınırlı retry + fallback ZORUNLU** (§2.4, plan §5.6/§8-4).
- `StructuredLogger` method imzaları bu görevde okunmadı → **UNKNOWN** (uydurma yasak).

## §9 Hata Modları ve Güvenlik Sınırı
- **Failure modes (katman sınırında):**
  1. **Log/redaction politikası ADR'siz** — `.ai/.decisions/index.md`'de log başlıklı ADR bulunmadı → politika UNKNOWN (§4; makro özet §6.1).
  2. **Redaction kapsamı doğrulanmadı** — tüm log yollarında `[REDACTED]` uygulaması kod taramasıyla kanıtlanmadı (§6) → sızıntı riski.
  3. **Log formatı/retention/sampling belgelenmemiş** (§6) → uyumluluk/iz sürme kapasitesi UNKNOWN.
  4. **Fallback davranışı kod kanıtsız** — timeout+retry+fallback kuralı kural düzeyinde (§2.4); uygulaması bu görevde doğrulanmadı.
- **İzolasyon:** loglama hattı hata üretirse iş akışını kesmemelidir; katman ihlali/audit kaydı revert mekanizmasıyla (matris §3) ilişkilidir (§2.5). Uzak çağırı fallback'i tüm katmanlara K012 üzerinden dayatılır (makro özet §5 K0 notu).
- **Güvenlik sınırı (kim neyi doğrular):** K012 auth uygulamaz — sınır işi log **maskelemesidir**: hassas veri `[REDACTED]` (AGENTS §17.3; §2.1). Denetim kaynağı: revert + log ERROR kuralı (AGENTS §18.6, §5 katman ihlali kaydı). CSP/auth K009/K007/K014 sınırlarındadır (kanonik sıra — k2 makro §3.2).

## §10 Bilinmeyenler (⚠️ / UNKNOWN)

| Bulgu | Durum |
|---|---|
| Log/redaction'a özel ADR | YOK — politika UNKNOWN (§4; makro özet §6.1) |
| `StructuredLogger` method imzaları | UNKNOWN — okunmadı |
| Log formatı/retention/sampling politikası | UNKNOWN (§6) |
| Redaction'ın tüm log yollarında uygulanması | ⚠️ VERIFICATION REQUIRED — kod taraması yapılmadı (§6) |
| Uzak çağırı timeout+retry+fallback uygulama kanıtı | ⚠️ VERIFICATION REQUIRED (plan §5.6, §2.4) |
