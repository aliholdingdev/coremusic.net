---
title: "K016-observability — Riskler"
node_id: K016-observability/observability-riskler.md
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
  - "shared/src/Log/Redactor.php:28/31/65 (MASK + kalıplar) · 2026-10-18"
  - "shared/tests/Unit/Log/LoggerRedactionTest.php:140/165 (yazım yolu maskesi) · 2026-10-18"
  - "shared/src/Log/FileHandler.php:54 (minLevel kapısı) · 2026-10-18"
  - ".ai/architecture/K016-observability/observability-master.md §G (açık sorular) · 2026-10-18"
validation: "2026-10-18 — her satır Olasılık×Etki×Azaltma×Sahip; teknoloji boşlukları UNKNOWN"
open_questions: "⚠️ metrik/tracing/health/alert teknolojisi UNKNOWN · ⚠️ alerting/ dizin kararı açık · ⚠️ .ai/log.md sınırı kademe-3"
group: mimari
---

# K016-observability — Riskler

## §1 ELI15

Gözlemlemede **ters gidebilecek şeyler**: log hassas sızdırır, kapı yanlış
kapanır, olay görünmez kalır. Her satır dört şey taşır: **olması muhtemelliği**
(olmasılık), **bozacağı şey** (etki), **küçültme yolu** (azaltma), **sahip**.
Kanıt yoksa risk "yok" sayılmaz — **UNKNOWN** yazılır.

## §2 Kapsam

- Yalnız K016-observability (D-01…D-11 kararları, C-01…C-03 bileşenleri).
- Güvenlik duvarının geneli K009'dur; burada log'a özgü kesimler var.

## §3 Ölçek

Olasılık/Etki 1–5 (§4'te "O · E · P=O×E"). **P ≥ 12 kırmızı** · 6–11 sarı ·
≤ 5 yeşil.

## §4 Risk Kayıtları

### R-01 — Log'a hassas veri sızıntısı (maskenin atlanması)
- **Olasılık:** 3 · **Etki:** 5 · **Öncelik:** 15 (kırmızı)
- **Tehdit:** password/token/authorization ham yazılır; log → KVKK/K019 ihlali.
- **Azaltma:** `Redactor::redact` tek giriş + yazım yolu zorunlu
  (`Redactor.php:65`, `FileHandler.php:188`); güvence: yazım yolu testleri
  `LoggerRedactionTest.php:140/165`.
- **Sahip:** security-engineer (persona 2) + data-engineer.
- **Kalan boşluk:** Yeni hassas anahtar kalıbı listeye girmezse maskelemez
  (`SENSITIVE_KEYS` statik, `Redactor.php:31`) → cadance UNKNOWN (§7 O-04).

### R-02 — Maske testinin geçmişi ama üretim yolunun atması
- **Olasılık:** 3 · **Etki:** 5 · **Öncelik:** 15 (kırmızı)
- **Tehdit:** Testte maske çalışır, üretimde başka bir yazım yolu maskeyi
  atlar (test ≠ prod yolu).
- **Azaltma:** İki yazım yolu testi (FileHandler + structured logger,
  `:140/:165`); yeni yazım yolu eklenirse test zorunlu (K17 CI gate).
- **Sahip:** qa-engineer (persona 5) + backend-architect.
- **Kalan boşluk:** Diskte kaç farklı yazım yolu olduğu envanteri **UNKNOWN**.

### R-03 — Kör nokta: metrik/tracing/health/alert yok
- **Olasılık:** 4 · **Etki:** 4 · **Öncelik:** 16 (kırmızı)
- **Tehdit:** Yalnız log var; performans/ölçüm/uyarı katmanı olmadığı için olay
  (kesinti, yavaşlama) **görünmez** — gözlemlenebilirlik iddiası eksik.
- **Azaltma:** D-07…D-10 ADR'leri (teknoloji seçimi) kapatılır; bu pakette
  "var" denmedi (UNKNOWN işaretli, `observability-bilesenler.md` §10).
- **Sahip:** sre-engineer + architect.
- **Kanıt:** diskte metrik/tracing/health/alert kodu yok (§7 U-01…U-04).

### R-04 — Kapı yanlışlıkla açık: log fırtınası / gürültü
- **Olasılık:** 3 · **Etki:** 3 · **Öncelik:** 9 (sarı)
- **Tehdit:** minLevel/debugMode yanlış ayar → üretimde debug fırtınası; disk
  dolması + gizli veri artışı.
- **Azaltma:** Kapı merkezi (`FileHandler.php:54`, D-03) + test
  (`Psr3LoggerTest.php:103`); CI'da üretim konfigürasyonu gözden geçirilir.
- **Sahip:** devops-engineer (persona 8).
- **Kalan boşluk:** Üretim ortamında hangi minLevel'in aktif olduğu **UNKNOWN**
  (kanıt yok).

### R-05 — İz kayması: traceId'nin eksik damgalanması
- **Olasılık:** 3 · **Etki:** 4 · **Öncelik:** 12 (kırmızı)
- **Tehdit:** Bazı satırlarda traceId yok → istek korelasyonu kopar, olay
  zinciri okunmaz.
- **Azaltma:** traceId testi (`Psr3LoggerTest.php:116`, D-06); yeni formatlayıcı
  ekleyen test zorunludur.
- **Sahip:** backend-architect.
- **Kalan boşluk:** Tüm satırların tek formatlayıcıdan geçtiği **doğrulanmadı**
  (format envanteri UNKNOWN).

### R-06 — Çifte log sistemi: vault `.ai/log.md` ↔ uygulama logu karışması
- **Olasılık:** 3 · **Etki:** 3 · **Öncelik:** 9 (sarı)
- **Tehdit:** Session logu uygulama loguna veya tersi yazılır; veri karışır,
  retention belirsizleşir.
- **Azaltma:** D-11 kademe-3 sınır kararı (bu pakette açık bırakıldı);
  `observability-kararlar.md` §4 — dosyalar karıştırılmaz.
- **Sahip:** Vault Steward.

### R-07 — Uyarı yokluğu: hata üretimi ama uyarı tetiklenmemesi
- **Olasılık:** 4 · **Etki:** 4 · **Öncelik:** 16 (kırmızı)
- **Tehdit:** error/critical satırları yazılır ama kimse uyarı almaz (alert
  bileşeni yok); olay fark edilmez.
- **Azaltma:** D-10 (alert + `alerting/` dizin kararı) kapatılır; bu arada
  WARNING_FAMILY testi (`FileHandler.php:52`, `Psr3LoggerTest.php:127`) en azından
  **sınıflandırmayı** güvenceye alır.
- **Sahip:** sre-engineer + devops-engineer.
- **Kalan boşluk:** Uyarı kanalı/SLA **UNKNOWN**.

### R-08 — CI'da gözlem testlerinin geçtiği doğrulanmadı
- **Olasılık:** 3 · **Etki:** 3 · **Öncelik:** 9 (sarı)
- **Tehdit:** Testler yazılmış ama CI koşuyor mu, geçiyor mu bilinmiyor →
  güvence kâğıt üstünde kalır.
- **Azaltma:** `ci.yml:158/176/203` pencereleri K17'de; bu pakette CI sonucu
  **doğrulanmadı** (UNKNOWN, §7 U-06).
- **Sahip:** devops-engineer + qa-engineer.

## §5 Öncelik Matrisi (özet)

| Risk | O | E | P | Renk | Sahip |
|------|---|---|---|------|-------|
| R-01 | 3 | 5 | 15 | kırmızı | security-engineer |
| R-02 | 3 | 5 | 15 | kırmızı | qa-engineer |
| R-03 | 4 | 4 | 16 | kırmızı | sre-engineer |
| R-07 | 4 | 4 | 16 | kırmızı | sre-engineer |
| R-05 | 3 | 4 | 12 | kırmızı | backend-architect |
| R-04 | 3 | 3 | 9 | sarı | devops-engineer |
| R-06 | 3 | 3 | 9 | sarı | Vault Steward |
| R-08 | 3 | 3 | 9 | sarı | devops-engineer |

## §6 Kesişim Haritası

| Risk | İlgili K |
|------|----------|
| R-01/R-02 | K009-security · K019-comilance (KVKK) |
| R-03/R-07 | K17-devops (alert/CI) · K012-services |
| R-05 | K010-network (istek korelasyonu) |
| R-08 | K18-testing · K17-devops |

## §7 Bilinmeyenler (⚠️ VERIFICATION REQUIRED)

| # | Bilinmeyen | İlgili risk |
|---|-----------|-------------|
| U-01 | Metrik teknolojisi | R-03 |
| U-02 | Tracing teknolojisi | R-03/R-05 |
| U-03 | Health check biçimi | R-03 |
| U-04 | Alert bileşeni + güncelleme cadance'ı (Redactor kalıpları) | R-07/R-01 |
| U-05 | Üretim minLevel değerleri | R-04 |
| U-06 | CI'da gözlem testlerinin geçtiği | R-08 |
| U-07 | `.ai/log.md` sınırı (kademe-3) | R-06 |

## §8 İzleme ve Kapanış

- Kırmızılar (R-01/02/03/05/07) `observability-test-stratejisi.md` §5–§6
  senaryolarına bağlıdır.
- U-01…U-07 yalnız kanıtla kapanır; kapanışta satır "kapatıldı (kanıt: dosya:satır)"
  olarak işaretlenir, silinmez.

## §9 İlgili Dosyalar

- `observability-kararlar.md` — D-01…D-11
- `observability-test-stratejisi.md` — risk doğrulama senaryoları
- `observability-web-arastirma.md` — aday teknoloji kaynakları (R-03)
- `../../K009-security/` · `../../K17-devops/` · `../../K019-comilance/`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
