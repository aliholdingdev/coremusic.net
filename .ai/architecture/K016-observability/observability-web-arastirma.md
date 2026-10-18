---
title: "K016-observability — Web Araştırması"
node_id: K016-observability/observability-web-arastirma.md
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
  - "https://php-fig.org/psr/psr-3/ — doğrulama 2026-10-18 (bu oturum)"
  - "https://opentelemetry.io/docs/concepts/signals/ — doğrulama 2026-10-13 (ai-dogrulama.md §4 havuzu)"
  - "https://prometheus.io/docs/practices/alerting/ — doğrulama 2026-10-18 (bu oturum)"
  - "https://sre.google/workbook/alerting-on-slos/ — doğrulama 2026-10-18 (bu oturum)"
  - "https://www.w3.org/TR/NOTE-datetime — doğrulama 2026-10-13 (ai-dogrulama.md §4 havuzu)"
  - "https://www.rfc-editor.org/rfc/rfc2119.txt — doğrulama 2026-10-13 (ai-dogrulama.md §4 havuzu)"
validation: "2026-10-18 — 6 URL üretim öncesi doğrulandı; tarihler kaynak bazında dürüst"
open_questions: "⚠️ aday teknolojilerin (Prometheus/OTel/health) CoreMusic'e uygunluk kararı ADR bekliyor (D-07…D-09) · ⚠️ OTel sayfası 2026-10-13 tarihli, yeniden okunmadı"
group: kanit
---

# K016-observability — Web Araştırması

## §1 ELI15

Bu dosya gözlemleme kararlarını **dış kaynaklara bağlar**: log sözleşmesi
(PSR-3), iz sinyalleri (OpenTelemetry), uyarı pratiği (Prometheus + Google SRE)
ve zaman damgası biçimi (RFC). Kural: her URL üretimden **önce** açılabilir ve
**erişim tarihi** taşır. Kaynak açılmazsa madde yazılmaz.

## §2 Kapsam

- K016'nın ADR bekleyen kararlarına (D-07 metrik · D-08 tracing · D-09 health ·
  D-10 alert) **aday** kaynaklar.
- Doğrulama: 2026-10-18 (4 kaynak, bu oturum) · 2026-10-13 (2 kaynak, havuz).

## §3 Doğrulanmış Kaynaklar

| # | Kaynak | URL | Erişim | Ne verir |
|---|--------|-----|--------|----------|
| S-1 | PSR-3: Logger Interface | https://php-fig.org/psr/psr-3/ | 2026-10-18 | Log sözleşmesi: 8 seviye, context, RFC 5424 uyumu |
| S-2 | OpenTelemetry — Signals | https://opentelemetry.io/docs/concepts/signals/ | 2026-10-13 | Traces/Metrics/Logs sinyal modeli (D-08 adayı) |
| S-3 | Prometheus — Alerting good practices | https://prometheus.io/docs/practices/alerting/ | 2026-10-18 | Semptom-based uyarı + Camel Case kuralı (D-07/D-10) |
| S-4 | Google SRE — Alerting on SLOs | https://sre.google/workbook/alerting-on-slos/ | 2026-10-18 | Multi-window multi-burn-rate (14.4×/1sa · 6×/6sa sayfala) |
| S-5 | W3C Date and Time Formats | https://www.w3.org/TR/NOTE-datetime | 2026-10-13 | Log zaman damgası biçimi (RFC 3339 profili) |
| S-6 | RFC 2119 (BCP 14) | https://www.rfc-editor.org/rfc/rfc2119.txt | 2026-10-13 | MUST/SHOULD sözcükleri — bu belgelerdeki zorunluluk dili |

> **Doğrulama notu:** S-1/S-3/S-4 üretim öncesi bu oturumda açıldı (2026-10-18).
> S-2/S-5/S-6 `ai-dogrulama.md` §4 havuzunda 2026-10-13 doğrulanmıştır; içerik
> tazeliği için yeniden okunmalı (⚠️ §6 W-01).

## §4 Çıkarımlar → Kararlar

### 4.1 Log sözleşmesi → D-02
- PSR-3 (S-1): `LoggerInterface`, 8 seviye (emergency…debug), context dizisi,
  RFC 5424 seviye isimleri. CoreMusic `FileHandler implements LoggerInterface`
  (`FileHandler.php:30`) + seviye ailesi (`:40/:52`) bu sözleşmeye uyar.
- **Doğrulama:** `Psr3LoggerTest.php:44/49` arayüz + method testleri kaynakla
  birebir örtüşür.

### 4.2 Tracing adayı → D-08
- OpenTelemetry (S-2): traces/metrics/logs **üç ayrı sinyal**, aynı model.
- **Çıkarım:** Bizde traceId damgası (`Psr3LoggerTest.php:116`) logs sinyalinin
  tohumudur; tracing sinyali **yok** (UNKNOWN → D-08 ADR bekliyor).
- **Eylem:** ADR'de OTel adayı, "sinyaller ayrık + tek model" gerekçesiyle
  değerlendirilir.

### 4.3 Uyarı pratiği → D-07 / D-10
- Prometheus (S-3): uyarılar **semptoma** (ölçülebilir acı) bağlanır, isimler
  Camel Case; "health = alert" değil.
- Google SRE (S-4): **multi-window multi-burn-rate** — hızlı yanma 14.4×/1sa+5dk
  (page), yavaş yanma 6×/6sa+30dk (page), 3 gün 1× (ticket).
- **Çıkarım:** Bizde metrik/alert **yok** (R-03/R-07); uyarı tasarımı bu iki
  kaynağa göre ADR'ye girer — sağlık (health) ≠ uyarı ayrımı D-09/D-10'ı ayırır.

### 4.4 Zaman damgası → D-01 (log formatı)
- W3C/RFC 3339 (S-5): `YYYY-MM-DDTHH:MM:SSZ` biçimi; ISO 8601 profili.
- **Çıkarım:** Log satırı timestamp'i bu biçime uymalı (iskelelerde `RFC3339`
  kullanıldı, `observability-kod-ornekleri.md` §10).

### 4.5 Zorunluluk dili → tüm kararlar
- RFC 2119 (S-6): MUST/SHOULD/MAY — bu belgedeki "zorunlu/önerilir" sözcükleri
  bu anlamdadır (maske **MUST**, cadance **SHOULD**).

## §5 Karşı Kaynaklar

- Kapsam dışı: pahalı/ticari gözlem platformları (bu turda incelenmedi) →
  research-analyst ile ikinci tur (§6 W-02).

## §6 Bilinmeyenler (⚠️ VERIFICATION REQUIRED)

| # | Bilinmeyen | Not |
|---|-----------|-----|
| W-01 | S-2/S-5/S-6 içeriğinin 2026-10-18 durumu | 2026-10-13 tarihli, yeniden okunmadı |
| W-02 | Karşılaştırmalı ikinci tur | yapılmadı |
| W-03 | Adayların CoreMusic uygunluğu (boyut/bakım) | ADR bekliyor (D-07…D-09) |
| W-04 | Uyarı kanalı/SLA | UNKNOWN (R-07) |

## §6.1 Kaynak Kullanım Kuralları

| # | Kaynak | ADR hangisine girer | Dayanım |
|---|--------|---------------------|---------|
| S-1 | PSR-3 | D-02/D-03 (log sözleşmesi) | FI-FIG standardı — bağlayıcı |
| S-2 | OpenTelemetry | D-08 (tracing adayı) | aday — tek başına karar değil |
| S-3 | Prometheus alerting | D-07/D-10 (uyarı tasarımı) | pratiği temsil eder |
| S-4 | Google SRE SLO | D-10 (burn-rate eşiği) | sayısal eşik dayanağı |
| S-5 | W3C datetime | D-01 (zaman damgası biçimi) | biçimsel referans |
| S-6 | RFC 2119 | tüm karar metinleri | MUST/SHOULD sözcük anlamı |

1. **Bağlayıcı kaynak (S-1):** Sözleşme maddesi ADR metnine birebir alınır.
2. **Aday kaynak (S-2/S-3):** "Öneri" olarak girer; CoreMusic'e uygunluk §6 W-03
   kapanmadan "seçildi" denmez.
3. **Biçimsel kaynak (S-5/S-6):** Yalnız format/sözcük anlamı verir, mimari karar
   dayanağı yapılmaz.

## §6.2 Tazelik ve Yeniden Doğrulama

- 30 günden eski erişim tarihli kaynaklar (S-2/S-5/S-6 → 2026-10-13) ADR
  metnine girmeden yeniden okunur (§6 W-01).
- Uyarı eşikleri (S-3/S-4 sayısal değerleri) sürümlendikçe değişebilir → ADR'de
  "kaynak + erişim tarihi" birlikte yazılır.
- Erişilemeyen kaynak: listede kalır, içeriği aktarılmaz.

## §7 İlgili Dosyalar

- `observability-kararlar.md` — D-02/D-07…D-10
- `observability-riskler.md` — R-03/R-07
- `observability-test-stratejisi.md` §6 — uyarı doğrulaması
- `ai-dogrulama.md` §4 — 2026-10-13 havuzunun kaynağı (K015)

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
