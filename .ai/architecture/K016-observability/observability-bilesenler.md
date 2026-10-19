---
title: "K016-observability — Bileşenler"
node_id: K016-observability/observability-bilesenler.md
node_type: subdomain
status: draft
parent_id: K016-observability/index.md
children: []
depends_on:
  - K012-services
  - K010-network
  - K014-media
  - K17-devops
evidence:
  - "shared/src/Log/LoggerFactory.php:11 (final class LoggerFactory) · 2026-10-18"
  - "shared/src/Log/FileHandler.php:30/40/52/54/125/188/207 · 2026-10-18"
  - "shared/src/Log/Redactor.php:25/28/31/40/49/56/65 · 2026-10-18"
  - "shared/tests/Unit/Log/Psr3LoggerTest.php:44/49/68/81/103/116/127/143/154 · 2026-10-18"
  - "shared/tests/Unit/Log/LoggerRedactionTest.php:55/70/90/106/122/140/165 · 2026-10-18"
  - ".ai/architecture/K016-observability/observability-master.md §B (28 karar) · 2026-10-18"
validation: "2026-10-18 — satırlar grep ile doğrulandı (dosya adı + satır no birebir)"
open_questions: "⚠️ metrik/tracing/health/alert teknolojisi UNKNOWN (öneri: Prometheus+OpenTelemetry+RFC-healthcheck — ADR/PLANNED) · ⚠️ vault .ai/log.md vs uygulama logu sınırı kademe-3 · ⚠️ alerting/ fazladan dizin kararı açık"
group: mimari
---

# K016-observability — Bileşenler

## §1 ELI15

Bu dosya sistemin **gözlemleme parçalarını** anlatır: olaylar ne zaman, nereye,
nasıl yazılır. Sistemde üç ana parça var: (1) **LoggerFactory** — doğru yazıcıyı
(`LoggerFactory.php:11`) kuran fabrika; (2) **FileHandler** — satırları dosyaya
akan, seviye kapısı ve güvenlik akışı olan (`FileHandler.php:30`); (3) **Redactor**
— hassas bilgiyi (şifre, token, e-posta) yazmadan önce `[REDACTED]` ile maskeler
(`Redactor.php:25/31`). Bunlar **kod diskte var**; metrik/iz/uyarı (alert)
parçaları ise henüz yok → UNKNOWN.

## §2 Kapsam

- **Kapsamda:** `shared/src/Log/` altındaki 3 bileşen + gözlemleme kararlarının
  bileşen eşlemesi (`observability-master.md` §B/§E).
- **Kapsamda değil:** Prometheus/OTel gibi henüz diskte olmayan parçalar (§7
  Bilinmeyenler), vault `.ai/log.md` (ayrı katman, kademe-3 sınır kararı).

## §3 Bileşen Envanteri

| # | Bileşen | Dosya | Sorumluluk | Kanıt (satır) |
|---|---------|-------|-----------|---------------|
| C-01 | LoggerFactory | `shared/src/Log/LoggerFactory.php` | Logger kurulumu/factory | `:11` |
| C-02 | FileHandler | `shared/src/Log/FileHandler.php` | PSR-3 logger + seviye kapısı + dosyaya yazım | `:30` (class) · `:125` (log) · `:188` (logAt) |
| C-03 | Redactor | `shared/src/Log/Redactor.php` | Hassas veri maskesi | `:25` · `:31` · `:65` |
| T-01 | Psr3LoggerTest | `shared/tests/Unit/Log/Psr3LoggerTest.php` | Davranış güvencesi | `:44`…`:154` |
| T-02 | LoggerRedactionTest | `shared/tests/Unit/Log/LoggerRedactionTest.php` | Maskenin güvencesi | `:55`…`:165` |

## §4 C-01 LoggerFactory

- **Sorumluluk:** Yapılandırılmış logger'ı tek yerden kurar (factory yöntemi;
  dağıtık `new` yok).
- **Kanıt:** `LoggerFactory.php:11` — `final class LoggerFactory`.
- **Arayüz:** Çağırıcı (servis/controller) logger'ı buradan alır → PSR-3
  `LoggerInterface` (PSR-3 doğrulaması `Psr3LoggerTest.php:44`).

## §5 C-02 FileHandler

- **Sorumluluk:** Mesajı seviye filtresinden geçirir, bağlamı (context) gömmez,
  satırı dosyaya yazar.
- **Anahtar yüzeyler:**
  - `:30` — `final class FileHandler implements LoggerInterface` (PSR-3 uyuşumu).
  - `:40` `LEVELS` · `:52` `WARNING_FAMILY` — seviye hiyerarşisi ve uyarı ailesi.
  - `:54` constructor: `logDir`, `minLevel`, `debugMode` — **seviye kapısı**
    (`Psr3LoggerTest.php:103` minLevel gate).
  - `:125` `log()` — PSR-3 girişi · `:188` `logAt(..., securityStream)` —
    **güvenlik akışı** ayrımı · `:207` dosya yazım yolu.
- **Güvenlik:** Yazım yolu redaction'dan geçer
  (`LoggerRedactionTest.php:140` FileHandler write path · `:165` structured logger
  write path) — yani **maskesiz satır diske düşmez**.

## §6 C-03 Redactor

- **Sorumluluk:** Her satırı maskeleme; `MASK = '[REDACTED]'` (`:28`).
- **Kalıplar:**
  - `:31` `SENSITIVE_KEYS` — password/token/secret/authorization/cookie …
  - `:40` `KEY_VALUE_PATTERN` — `anahtar: değer` biçimleri (JSON/düz).
  - `:49` `LONG_HASH_PATTERN` — 64 karakterlik hash (UUID **korunur**,
    `LoggerRedactionTest.php:122`).
  - `:56` `EMAIL_PATTERN` — e-posta; yerel kısım maskelenir, alan adı korunur
    (`:106`).
- **Giriş:** `:65` `public static function redact(string $line): string` — tek
  giriş noktası; FileHandler yazım yolu buradan geçer.

## §7 Test Bileşenleri (güvence katmanı)

- **T-01 Psr3LoggerTest:** PSR-3 arayüzü (`:44`), tüm methodlar (`:49`), bilinmeyen
  seviye fallback (`:68`), debug mod (`:81/:95`), minLevel kapısı (`:103`),
  **traceId damgası** (`:116`), güvenlik olayı (`:127`), e-posta PII maskesi
  (`:143`), context sızıntısı (`:154`).
- **T-02 LoggerRedactionTest:** sorgu-string auth_key (`:55`), password JSON (`:70`),
  temiz satır geçişi (`:90`), e-posta (`:106/:114`), hash-vs-UUID (`:122`),
  yazım yolları (`:140/:165`).

## §8 Bağımlılık Haritası (bileşen düzeyi)

```
servis/controller ──► LoggerFactory ──► FileHandler (PSR-3)
                                            │
                                            ├── seviye kapısı (minLevel/debugMode)
                                            ├── securityStream (:188)
                                            └── Redactor::redact (:65) ──► [REDACTED]
                                                     │
CI (ci.yml:158/176/203) ──► testler (T-01/T-02) ──► K17-devops
```

## §9 Master Uyum (`observability-master.md`)

- §B: 28 karar (bu paketin `observability-kararlar.md` içinde kaydı).
- §E: repo kanıtları (yukarıdaki satırlar bu bölümle beslenir).
- §F: web tablosu → `observability-web-arastirma.md`.
- Henüz var olmayan parçalar §B'de **PLANNED/ADR** olarak geçer; burada
  "varmış" gibi yazılmaz.

## §10 Bilinmeyenler (⚠️ VERIFICATION REQUIRED)

| # | Bilinmeyen | Durum |
|---|-----------|-------|
| U-01 | Metrik teknolojisi (ör. Prometheus) | UNKNOWN — diskte kanıt yok; öneri ADR/PLANNED |
| U-02 | Tracing (ör. OpenTelemetry) | UNKNOWN — bkz. `observability-web-arastirma.md` §3 |
| U-03 | Health check (RFC-healthcheck) | UNKNOWN — kanıt yok |
| U-04 | Alert/uyarı bileşeni | UNKNOWN — `alerting/` dizin kararı açık |
| U-05 | vault `.ai/log.md` ↔ uygulama logu sınırı | kademe-3 kararı bekliyor |

## §11 İlgili Dosyalar

- `observability-master.md` §B/§E/§F — karar, kanıt, web kaynakları
- `observability-kararlar.md` — 28 kararın kaydı
- `observability-kod-ornekleri.md` — bileşen kod pencereleri
- `../K17-devops/` — CI'da bu bileşenlerin koşması (ci.yml:158/176/203)
- `../K009-security/` — maskeleme/güvenlik kesişimi

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
