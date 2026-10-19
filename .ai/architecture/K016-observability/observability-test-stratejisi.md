---
title: "K016-observability — Test Stratejisi"
node_id: K016-observability/observability-test-stratejisi.md
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
  - "shared/tests/Unit/Log/Psr3LoggerTest.php:44/49/68/81/95/103/116/127/143/154 · 2026-10-18"
  - "shared/tests/Unit/Log/LoggerRedactionTest.php:55/70/90/106/114/122/140/165 · 2026-10-18"
  - ".github/workflows/ci.yml:158/176/203 (test adımları) · 2026-10-18"
validation: "2026-10-18 — 18 test metodu grep ile doğrulandı (ad + satır); CI sonucu doğrulanmadı"
open_questions: "⚠️ CI'da bu testlerin geçtiği doğrulanmadı · ⚠️ coverage değeri UNKNOWN · ⚠️ metrik/tracing/health/alert testi yok (teknoloji yok)"
group: mimari
---

# K016-observability — Test Stratejisi

## §1 ELI15

Bu dosya gözlemleme katmanının **nasıl sınamalandığını** ve **neyi sınaması
gerektiğini** anlatır. İyi haber: diskte iki test dosyası var ve 18 metodu
grep ile doğrulandı. Eksik: metrik/tracing/health/alert yok → o katmanın testi de
yok (teknoloji ADR bekliyor). Üç katmanlı piramit + mevcut testlerin haritası +
yeni testler için kabul kriterleri aşağıda.

## §2 Kapsam

- C-01…C-03 bileşenleri, D-01…D-11 kararları.
- Diskteki mevcut testler (Psr3LoggerTest, LoggerRedactionTest) + eksik katmanlar.

## §3 Test Piramidi

```
        ┌─────────────────────────┐
        │ E2E (2-4)               │  servis → log → dosya → CI gate
        ├─────────────────────────┤
        │ Entegrasyon (6-10)      │  yazım yolu maskesi, securityStream, kapı
        ├─────────────────────────┤
        │ Birim (25+; mevcut 18)  │  kalıp, kapı, traceId, arayüz
        └─────────────────────────┘
```

| Katman | Hedef | Durum |
|--------|-------|-------|
| Birim | ≥%80 (Log dizini) | **kısmen mevcut** (18 metot) |
| Entegrasyon | yazım yolları | **kısmen mevcut** (`:140/:165`) |
| E2E | dosya → CI | ⚠️ doğrulanmadı (UNKNOWN) |

## §4 Mevcut Test Haritası (disk kanıtlı)

### 4.1 Psr3LoggerTest (`shared/tests/Unit/Log/Psr3LoggerTest.php`)

| Satır | Test | Doğruladığı karar |
|-------|------|-------------------|
| `:44` | testImplementsPsr3LoggerInterface | D-02 (PSR-3) |
| `:49` | testAllPsr3MethodsAreCallable | D-02 |
| `:68` | testLogDispatcher_unknownLevelFallsBackToError | D-03 (fallback) |
| `:81` | testDebug_writesWhenDebugModeEnabled | D-03 (debug açık) |
| `:95` | testDebug_skippedWhenDebugModeDisabled | D-03 (debug kapalı) |
| `:103` | testMinLevelGate_blocksLowerLevels | D-03 (kapı) |
| `:116` | testTraceId_isStampedOnLines | D-06 (traceId) |
| `:127` | testSecurityEvent_writesWarningLine | D-05 (güvenlik akışı) |
| `:143` | testAuthEvent_masksEmailPii | D-04 (PII) |
| `:154` | testSecretsInContext_areRedacted | D-04 (context) |

### 4.2 LoggerRedactionTest (`shared/tests/Unit/Log/LoggerRedactionTest.php`)

| Satır | Test | Doğruladığı karar |
|-------|------|-------------------|
| `:55` | testAuthKeyInQueryStringIsRedacted | D-04 (query string) |
| `:70` | testPasswordIsRedactedInKeyValueAndJsonFormats | D-04 (KV + JSON) |
| `:90` | testCleanLinePassesThroughUnchanged | D-04 (yalancı alarm yok) |
| `:106` | testEmailLocalPartMaskedDomainKept | D-04 (e-posta) |
| `:114` | testEmailMaskInsideJsonContext | D-04 (JSON içi) |
| `:122` | testSixtyFourCharHashIsRedactedButUuidIsPreserved | D-04 (hash vs UUID) |
| `:140` | testFileHandlerWritePathAppliesRedaction | **R-02** (yazım yolu) |
| `:165` | testStructuredLoggerWritePathAppliesRedaction | **R-02** (yazım yolu) |

## §5 Eksik Senaryolar (yeni testler — kabul kriterli)

| # | Senaryo | Bağlı risk | Kabul kriteri |
|---|---------|-----------|----------------|
| M-01 | Yeni hassas anahtar (ör. `otp`) ham yazıldı | R-01 | ya maskelenir ya test reddedilir — **ham satır kabul edilmez** |
| M-02 | Üretim yazım yolu envanteri | R-02 | her yazım yolu `redact`'ten geçtiğine dair test var |
| M-03 | Log fırtınası (10k debug satırı) | R-04 | minLevel=info iken dosya **artmaz** |
| M-04 | TraceId'sız formatlayıcı | R-05 | yeni format test + traceId yoksa test kırmızı |
| M-05 | error satırı → uyarı sınıfı | R-07 | WARNING_FAMILY ayrımı doğrulanır (mevcut `:127` genişletilir) |

## §6 E2E Senaryoları

| # | Akış | Kabul kriteri |
|---|------|----------------|
| E-01 | servis çağrısı → log → dosya | satırda traceId + seviye var (D-06/D-03) |
| E-02 | hatalı token → güvenlik olayı | securityStream dosyasında, maskeli (D-05/D-04) |
| E-03 | CI → testler → gate | `ci.yml:158/176/203` adımları geçer (⚠️ §8) |

## §7 CI Entegrasyonu (K17 kesişimi)

- Kanıt: `.github/workflows/ci.yml:158/176/203` — test adımlarının pencereleri
  (2026-10-18).
- Kural: gözlem testleri CI'da **zorunlu** olmalı; yeni yazım yolu + test olmadan
  merge (K17 branch koruma bağlamı).
- **Doğrulama boşluğu:** Bu pencerelerin **hangi testleri çalıştırdığı ve geçtiği**
  bu oturumda doğrulanmadı → ⚠️ §8 T-02.

## §8 Bilinmeyenler (⚠️ VERIFICATION REQUIRED)

| # | Bilinmeyen | Etki |
|---|-----------|------|
| T-01 | Log dizini coverage değeri | §3 eşiği doğrulanamadı |
| T-02 | CI'da mevcut testlerin geçtiği | §7 doğrulanamadı |
| T-03 | E2E log→dosya testinin diskte varlığı | §6 E-01…E-03 UNKNOWN |
| T-04 | metrik/tracing/health/alert testi | teknoloji yok → test yok (D-07…D-10) |

## §9 Ölçüm Eşikleri

| Metrik | Eşik | Not |
|--------|------|-----|
| Mevcut test geçişi | %100 | 18 metot |
| Yeni maske testi kaçırma | 0 | R-01/R-02 |
| TraceId'sız satır | 0 | R-05 |
| Coverage (Log) | ≥%80 | T-01 UNKNOWN |

## §10 İlgili Dosyalar

- `observability-riskler.md` — R-01…R-08
- `observability-bilesenler.md` §7 — test bileşenleri (T-01/T-02)
- `observability-kod-ornekleri.md` — §14 CI mask gate iskeleti
- `../../K17-devops/` — CI · `../../K18-testing/` — test altyapısı

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
