---
title: "K016-observability — Domain Kaydı (K-Space V3)"
node_id: K016-observability
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - logging
  - metrics
  - tracing
  - correlation
  - health-checks
  - alerts
  - performance
  - audio-diagnostics
  - incident-response
depends_on:
  - K012-services   # log/metric üreten servis katmanı — planlanan (mimari yargı)
  - K010-network    # tracing/health ağ katmanına dokunur — planlanan
  - K014-media      # audio-diagnostics ses zincirine bakar — planlanan
  - K018-devops     # alert/CI geri beslemesi — planlanan
evidence:
  - "shared/src/Log/ = LoggerFactory.php · FileHandler.php · Redactor.php (+ CLAUDE.md) — glob 2026-10-12"
  - "shared/tests/Unit/Log/Psr3LoggerTest.php · LoggerRedactionTest.php — glob 2026-10-12"
  - ".ai/log.md — vault append-only audit trail (varlık doğrulandı 2026-10-12; içerik okunmadı)"
validation:
  - "disk ölçümü (glob) 2026-10-12 — shared/src/Log/ ve test dosyaları"
  - "v2 spec §2.17 çocuk listesi birebir alındı (2026-10-12)"
open_questions:
  - "metrics/ · tracing/ · correlation/ · health-checks/ · alerts/ · performance/ · audio-diagnostics/ · incident-response/ için disk kanıtı YOK — kanıt bekliyor (⚠️ VERIFICATION REQUIRED)"
  - "PSR-3 uyumu: test adı (Psr3LoggerTest) ima ediyor, test içeriği OKUNMADI — doğrulanmadı (⚠️ VERIFICATION REQUIRED)"
  - "Uygulama logları ile vault .ai/log.md audit trail arasındaki ilişki/ayrım tanımlı mı? (⚠️ VERIFICATION REQUIRED)"
group: mimari
---


# K016-observability — Sistem Gözetimi (Domain)

> **ELI15:** Bu domain, "şu an hasta mı, nerede takılıyor?" sorusunun cevabıdır: loglar, metrikler,
> izler (tracing), sağlık kontrolleri, uyarılar ve olay müdahalesi. Sistem *görünürlüğü* buradadır.

## 1. Kapsam Sınırı (kısa)

- **Burada:** sistemin kendi gözlemi (log/metric/trace/health/alert/incident).
- **DEĞİL:** test stratejisi (K019-testing) · CI kapısı ve deploy (K018-devops) · güvenlik denetimi (K009-security).
- audio-diagnostics/ ses zincirine özgü teşhistir → K003-audio/K014-media ile **entegrasyon sınırı** vardır (K016 teşhisi tanımlar, ses motoru orada kalır).

## 2. Kademe-2 Çocuk Kataloğu (v2 §2.17 — birebir)

| # | Çocuk | Ne işe yarar (ELI15) | Disk kanıtı (2026-10-12) |
|---|---|---|---|
| 1 | logging/ | Logların yazımı, formatı, redaction'ı | ✅ shared/src/Log/ (3 dosya) + 2 birim testi |
| 2 | metrics/ | Sayaç/ölçüm toplama (istek, hata, gecikme) | ⚠️ kanıt bekliyor |
| 3 | tracing/ | İstek uçtan uca izi (trace/span) | ⚠️ kanıt bekliyor |
| 4 | correlation/ | İstekler arası korelasyon kimliği (request-id) | ⚠️ kanıt bekliyor |
| 5 | health-checks/ | Canlılık/sağlık uçları (health endpoints) | ⚠️ kanıt bekliyor |
| 6 | alerts/ | Uyarı kuralları ve kanalları | ⚠️ kanıt bekliyor |
| 7 | performance/ | Performans gözlemi (profil, bottleneck) | ⚠️ kanıt bekliyor |
| 8 | audio-diagnostics/ | Ses zincirine özgü teşhis (xrun, clip, gecikme) | ⚠️ kanıt bekliyor |
| 9 | incident-response/ | Olay müdahale akışı (runbook, eskalasyon) | ⚠️ kanıt bekliyor (kısmi: .ai/AGENTS.md §10 eskalasyon var) |

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (registry §5 — Band-1 üretim aşaması) |
| Kanıt dengesi | 1/9 çocuk kanıtlı (logging) · 8/9 kanıt bekliyor — bu, domainin **kağıtta geniş / diskte dar** olduğunu gösterir |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Bağlı dosyalar | observability-master.md · observability-bagimliliklar.md · observability-dogrulama.md |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-1 |
| mimari | `observability-bagimliliklar.md` | Katman bağı ve used_by haritası | Kademe-1 |
| kanit | `observability-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-1 |
| mimari | `observability-master.md` | Master künye ve sorumluluk haritası | Kademe-1 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
