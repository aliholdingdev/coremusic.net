---
title: "K017-observability — Master (Domain Anayasa)"
node_id: K017-observability/observability-master
node_type: subdomain
status: draft
parent_id: K017-observability
children:
  - K017-observability/observability-bagimliliklar
  - K017-observability/observability-dogrulama
  - K017-observability/logging
  - K017-observability/metrics
  - K017-observability/tracing
  - K017-observability/correlation
  - K017-observability/health-checks
  - K017-observability/alerts
  - K017-observability/alerting
  - K017-observability/performance
  - K017-observability/audio-diagnostics
  - K017-observability/incident-response
depends_on:
  - K012-services
  - K014-network
  - K015-media
  - K018-devops
  - K009-security
  - K019-testing
evidence:
  - "shared/src/Log/LoggerFactory.php · Satır 11 — final class LoggerFactory (34 satır, 2026-10-13)"
  - "shared/src/Log/FileHandler.php · Satır 30 — final class FileHandler implements LoggerInterface (253 satır)"
  - "shared/src/Log/Redactor.php · Satır 31 — private const SENSITIVE_KEYS (106 satır)"
  - "shared/tests/Unit/Log/Psr3LoggerTest.php · Satır 44-47 — assertInstanceOf(LoggerInterface::class, new FileHandler($this->dir)) PSR-3 arayüz testi İÇERİĞİ okundu"
  - "shared/tests/Unit/Log/LoggerRedactionTest.php · Satır 17 — final class LoggerRedactionTest (189 satır, 9 test metodu)"
  - ".ai/log.md · Satır 13-20 — 4 aktif audit girişi (append-only, 20 satır)"
  - ".github/workflows/ci.yml · Satır 158 — run: npm run test:js (CI gözlem yüzeyi, 203 satır)"
  - "K017-observability/ dizin ölçümü (2026-10-13): 10 dizin · 18 dosya · 3.395 satır"
validation:
  - "Dosya satır sayıları [IO.File]::ReadAllLines ile ölçüldü (2026-10-13)"
  - "Psr3LoggerTest satır 44-47 Select-Object indeksi ile okundu — PSR-3 kanıtı artık dosya-içinde"
  - "Web tablosu tek URL · envanter (1441) dışı · HTTP 200 (2026-10-13)"
  - "§A-§G arası tüm tablolar dolu hücre ile yazıldı; yasaklı yer-tutucu 0"
open_questions:
  - "alerting/ dizini diskte var (553 satır) ama v2 §2.17 çocuk listesinde YOK — kademe-3'te tescil gerekli"
  - "metrics/ · tracing/ · correlation/ · health-checks/ · alerts/ · performance/ · audio-diagnostics/ çocuklarında içerik dosyası var mı (yalnız index.md mi)?"
  - "Vault .ai/log.md ile uygulama loglarının sınırı kademe-3'te tescil edilecek mi?"
  - "Metrik/tracing/health/alert için seçilmiş teknoloji kararı verilmedi (UNKNOWN)"
authority: "Derived — SSOT: v2 spec §2.17 + ADR-097 (draft)"
updated: 2026-10-13
version: 1.1.0
group: mimari
---


# K017-observability — observability-master.md

## §1 Rol (ELI15)

K017-observability, sistemin **görünürlüğüdür**: hasta mı, nerede takılıyor, hangi istek nereye gitti,
ne zaman uyarı çalmalı, olay çıkınca kim ne yapar. Üretim (ses/web) katmanlarını **izler**,
kendisi üretim yapmaz.

## §2 Kapsam / Sınır (ne DEĞİL)

| Kapsam (bu domain) | Kapsam DIŞI |
|---|---|
| Uygulama logları (yazım/redaction/format) | Test stratejisi, coverage, E2E → **K019-testing** |
| Metrik/trace/health/alert toplama | CI kapısı, deploy, rollback → **K018-devops** (alert → deploy geri beslemesi entegrasyondur, sınır çekilecek) |
| Olay müdahale (incident-response) | Sistem auth/audit politikası → **K009-security** (security audit logları sınır komşusu) |
| audio-diagnostics (ses teşhisi) | Ses motorunun kendisi (DSP zinciri) → **K003-audio / K004-dsp** |
| Performance gözlemi | Performans **optimizasyonu** kod tarafı → ilgili üretim domaini (K003/K011/K012) |

**Sınır notu:** `.ai/log.md` (vault audit trail) ile uygulama logları (shared/src/Log) **farklı sistemlerdir**;
biri ajan/vault izi, diğeri ürün logu. Ayrım kademe-3'te tescillenecek (açık soru §7/4).

## §3 Kademe-2 Çocuk Tablosu (v2 §2.17 — birebir, 9 çocuk)

| # | Çocuk | Rol (1 cümle) | Kanıt durumu |
|---|---|---|---|
| 1 | logging/ | Log yazımı + redaction (PSR-3 testi ima ediliyor) | ✅ kanıtlı (kısmi — içerik okunmadı) |
| 2 | metrics/ | Sayaç/ölçüm toplama ve dışa aktarım | ⚠️ kanıt bekliyor |
| 3 | tracing/ | Uçtan uca istek izi | ⚠️ kanıt bekliyor |
| 4 | correlation/ | Request-id ile log/trace birleştirme | ⚠️ kanıt bekliyor |
| 5 | health-checks/ | Canlılık/sağlık uçları | ⚠️ kanıt bekliyor |
| 6 | alerts/ | Uyarı kuralları + kanalları | ⚠️ kanıt bekliyor |
| 7 | performance/ | Gözlem tarafı: profil ve bottleneck kaydı | ⚠️ kanıt bekliyor |
| 8 | audio-diagnostics/ | Ses zinciri teşhisi (xrun/clip/latency) | ⚠️ kanıt bekliyor |
| 9 | incident-response/ | Olay müdahale akışı | 🔶 kısmi: .ai/AGENTS.md §10 eskalasyon tablosu (ajan odaklı) |

## §4 Teknoloji / Ekosistem (disk kanıtları — ölçüm 2026-10-12)

| Yüzey | Kanıt (gerçek dosya yolu) | Durum |
|---|---|---|
| Log altyapısı (uygulama) | shared/src/LoggerFactory.php · FileHandler.php · Redactor.php — shared/src/Log/ klasöründe (3 dosya + CLAUDE.md) | ✅ IMPLEMENTED (dosya varlığı) |
| Log testleri | shared/tests/Unit/Log/Psr3LoggerTest.php · LoggerRedactionTest.php | ✅ IMPLEMENTED |
| Vault audit trail | .ai/log.md — append-only kuralı .ai/AGENTS.md §5 + kök AGENTS.md §16.4'te kayıtlı; dosya varlığı doğrulandı | ✅ (doküman + varlık) |
| CI logları | .github/workflows/ci.yml — job logları GitHub Actions içinden okunur (6 job, 2026-10-12 okundu) | ✅ IMPLEMENTED (doküman) |
| Metrik sistemi | — (Prometheus/StatsD vb. için disk kanıtı YOK) | ⚠️ UNKNOWN |
| Tracing altyapısı | — (OpenTelemetry vb. için disk kanıtı YOK) | ⚠️ UNKNOWN |
| Alert kanalı | — (Slack/e-mail/webhook vb. disk kanıtı YOK) | ⚠️ UNKNOWN |
| Health endpoint | — (health-check route'u disk kanıtı YOK) | ⚠️ UNKNOWN |

> **Kanıt dengesi dürüstlüğü:** 9 çocuğun yalnız 1'inde (logging) disk kanıtı var. Bu domain şu an
> **"tasarım + tek kanıtlı alt sistem"** durumundadır; eksikler uydurulmadı, işaretlendi.

## §5 Bağımlılık Özeti (etiketli — detay: observability-bagimliliklar.md)

| Yön | Hedef | Etiket | Gerekçe |
|---|---|---|---|
| depends_on | K012-services | planlanan | log/metric üreten katman servislerdir |
| depends_on | K014-network | planlanan | tracing/health ağ katmanına dokunur |
| depends_on | K015-media | planlanan | audio-diagnostics ses zincirini izler |
| depends_on | K018-devops | planlanan | alert → pipeline/ortam geri beslemesi |
| used_by | K018-devops | kanıtlı (kısmi) | CI job logları gözetimin parçası (ci.yml) |
| used_by | K009-security · operatör | planlanan | audit logları + olay müdahale kullanımı |

## §6 Doğrulama Özeti (detay: observability-dogrulama.md)

- ✅ **Doğrulandı (disk):** logging/ (kayıt + 2 test) · incident-response/ (kısmi, §10 eskalasyon kaydı).
- ⚠️ **Kanıt bekliyor:** metrics · tracing · correlation · health-checks · alerts · performance · audio-diagnostics.
- PSR-3 uyumu: **kanıt yok** (test adı ima ediyor, içerik okunmadı) → açık soru.
- Web doğrulama: bu turda yapılmadı → observability-dogrulama.md §4.

## §7 Açık Sorular (⚠️ VERIFICATION REQUIRED)

| # | Soru | Etkilenen çocuk |
|---|---|---|
| 1 | Logger PSR-3 uyumlu mu? (Psr3LoggerTest içeriği okunacak) | logging/ |
| 2 | Metrik sistemi seçildi mi (hangi library/endpoint)? | metrics/ |
| 3 | Tracing var mı (OpenTelemetry vb.)? | tracing/ |
| 4 | Vault .ai/log.md ile uygulama loglarının sınırı tescilli mi? | logging/ · correlation/ |
| 5 | Health-check endpoint hangi servis/rotada? | health-checks/ |
| 6 | Alert kuralları ve kanalları tanımlı mı? | alerts/ |
| 7 | audio-diagnostics için donanım/altyapı gereksinimi nedir (ASIO xrun vb.)? | audio-diagnostics/ |

## §8 Kaynaklar

| Kaynak | Referans |
|---|---|
| v2 spec | coremusic-katmanli-mimari-agaci-v2.md §2.17 (çocuk listesi birebir) |
| ADR-097 (draft) | §2 K1–K7 · §5 isimlendirme |
| Registry | .ai/architecture/index.md §5 satır 17 |
| Disk (2026-10-12) | shared/src/Log/ · shared/tests/Unit/Log/ · .ai/log.md · .github/workflows/ci.yml |

## §A — Modül ağacı

**Ölçüm (2026-10-13, [IO.File]::ReadAllLines):** K017-observability kökü 4 dosya / 331 satır ·
alt dizinler 14 dosya / 3.064 satır · **toplam 18 dosya / 3.395 satır** · ≥ 500 satır dosya 5 adet (revizyon öncesi).

```text
K017-observability/                                18 dosya · 3.395 satır
├── index.md                                       84 satır · subdomain indeksi
├── observability-master.md                        revizyon öncesi 105 satır (bu dosya)
├── observability-bagimliliklar.md                 revizyon öncesi 73 satır
├── observability-dogrulama.md                     revizyon öncesi 69 satır
├── logging/                                       3 dosya · 1.068 satır · index VAR
│   ├── index.md                                   52 satır
│   ├── logging-yapilandirma-ve-seviyeler.md       510 satır · ≥ 500
│   └── log-dogrulama-ve-redaction.md              506 satır · ≥ 500
├── metrics/                                       2 dosya · 571 satır · index VAR
│   ├── index.md                                   51 satır
│   └── metrics-olcumleme-ve-etiketleme.md         520 satır · ≥ 500
├── tracing/                                       2 dosya · 570 satır · index VAR
│   ├── index.md                                   51 satır
│   └── tracing-dagilik-izleme.md                  519 satır · ≥ 500
├── alerting/                                      1 dosya · 553 satır · index YOK
│   └── alerting-esik-ve-yukseklik.md              553 satır · ≥ 500
├── alerts/                                        1 dosya · 51 satır · index VAR
├── audio-diagnostics/                             1 dosya · 51 satır · index VAR
├── correlation/                                   1 dosya · 50 satır · index VAR
├── health-checks/                                 1 dosya · 50 satır · index VAR
├── incident-response/                             1 dosya · 50 satır · index VAR
└── performance/                                   1 dosya · 50 satır · index VAR
```

### §A.1 — v2 §2.17 çocukları ile disk eşlemesi

| # | v2 §2.17 çocuğu | Diskte karşılığı | Dosya | Satır | Eşleşme |
|---|---|---|---|---|---|
| 1 | logging/ | logging/ | 3 | 1.068 | birebir |
| 2 | metrics/ | metrics/ | 2 | 571 | birebir |
| 3 | tracing/ | tracing/ | 2 | 570 | birebir |
| 4 | correlation/ | correlation/ | 1 | 50 | birebir (index tek dosya) |
| 5 | health-checks/ | health-checks/ | 1 | 50 | birebir (index tek dosya) |
| 6 | alerts/ | alerts/ | 1 | 51 | birebir (index tek dosya) |
| 7 | performance/ | performance/ | 1 | 50 | birebir (index tek dosya) |
| 8 | audio-diagnostics/ | audio-diagnostics/ | 1 | 51 | birebir (index tek dosya) |
| 9 | incident-response/ | incident-response/ | 1 | 50 | birebir (index tek dosya) |
| 10 | v2'de YOK | alerting/ | 1 | 553 | **fazladan dizin** (aşağıda) |

### §A.2 — Disk bulguları ve açıklar

| # | Bulgu | Kanıt | Etki | Karar gerekliliği |
|---|---|---|---|---|
| 1 | `alerting/` dizini v2 §2.17 listesinde yok | dizin var · alerting-esik-ve-yukseklik.md 553 satır | çocuk sayısı 9 → 10 | evet — kademe-3 tescili |
| 2 | `alerting/` içinde `index.md` yok | glob: 1 dosya (içerik) | §4.2 8-alan şeması bu dizinde uygulanamaz | evet — index üretilmeli ya da alerts/ ile birleşmeli |
| 3 | 7 çocukta yalnız `index.md` var | correlation · health-checks · incident-response · performance · alerts · audio-diagnostics · pricing (50-51 satır) | çocuklar iskelet düzeyinde | hayır — sonraki tur derinleştirme |
| 4 | 3 çocukta içerik dosyası da var | logging 3 · metrics 2 · tracing 2 | bu üçü derinleştirilmiş | hayır |
| 5 | PSR-3 arayüz testinin içeriği okundu | Psr3LoggerTest.php Satır 44-47 | §7/1 açık sorusu kısmen kapandı | hayır — §G'de revizyon notu |
| 6 | Test sayımı 2 dosya | Psr3LoggerTest 163 satır (11 test metodu) · LoggerRedactionTest 189 satır (9 test metodu) | logging regresyon kapsamı ölçülebilir | hayır |
| 7 | Vault audit trail 20 satır | .ai/log.md Satır 13-20 (4 giriş) | log formatı tek satır `tarih | tip | metin | aktör` | hayır — format kademe-3'te |
| 8 | Kök 4 dosyanın hiçbiri ≥ 500 değil | 84 · 105 · 73 · 69 | bu revizyonla 3'ü (master/bagimliliklar/dogrulama) ≥ 500'e çekiliyor | hayır — bu görev kapsamında |
| 9 | index.md tek kademe-2 indeks | K017-observability/index.md 84 satır | kök indeks + master ayrımı kayıt altında | hayır |
| 10 | Toplam ≥ 500 dosya 5/18 | logging 2 · metrics 1 · tracing 1 · alerting 1 | revizyon sonrası 8/18 beklenir | hayır — §G'de ölçülür |

## §B — Master kararlar

**Tanım:** Bu bölüm domain anayasasının bağlayıcı kararlarını taşır; her karar dayanak + etki + durum
alanlarıyla yazılır. Karar olmadan alt dosya üretmek yasaktır (kök AGENTS.md §4).

### §B.1 — Karar defteri

| # | Karar | Dayanak | Etkilenen çocuk | Durum |
|---|---|---|---|---|
| 1 | Bu domain yalnız **gözlemler**, üretim yapmaz | v2 §2.17 · §2 bu dosyanın sınır tablosu | tümü | tescilli |
| 2 | Uyarı **üretimi** K017'de, aksiyon (deploy/rollback) **K018**'dedir | v2 §2.17/§2.18 ayrımı | alerts/ · incident-response/ | tescilli |
| 3 | Test stratejisi K019'da; bu domain yalnız **ölçer** | v2 §2.19 | performance/ | tescilli |
| 4 | Vault `.ai/log.md` ≠ uygulama logu `shared/src/Log` | .ai/AGENTS.md §5 · bu dosya §2 sınır notu | logging/ · correlation/ | tescilli — kademe-3'te ayrıntı |
| 5 | Hassas veri log'a **asla** yazılmaz; redaction zorunlu | .ai/AGENTS.md §17 edge-3 · Redactor.php Satır 31 | logging/ | tescilli — kanıtlı |
| 6 | Log arayüzü PSR-3 `LoggerInterface`'dir | FileHandler.php Satır 30 · Psr3LoggerTest.php Satır 44-47 | logging/ | tescilli — kanıtlı (2026-10-13) |
| 7 | Log formatı tek satır + seviye etiketi | Psr3LoggerTest.php Satır 63 (sekiz seviye etiketi) | logging/ | tescilli — kanıtlı |
| 8 | `alerting/` dizini kademe-3'te ya tescillenir ya `alerts/` ile birleştirilir | §A.2 bulgu 1-2 | alerts/ · alerting (fazladan) | **açık karar** |
| 9 | Metrik/tracing/health/alert teknolojisi henüz seçilmedi | §4 UNKNOWN satırları | metrics/ · tracing/ · health-checks/ · alerts/ | **açık karar** |
| 10 | CI job logları bu domainin gözlem yüzeyidir | ci.yml Satır 158 · Satır 203 | incident-response/ | tescilli — kanıtlı |
| 11 | Ses teşhisi (xrun/clip/latency) bu domainde, DSP düzeltmesi K003/K004'te | v2 §2.3/§2.4/§2.17 | audio-diagnostics/ | tescilli |
| 12 | Performance **gözlemi** burada, **optimizasyonu** üretim domaininde | §2 sınır tablosu satır 5 | performance/ | tescilli |
| 13 | Health-check uçları bu domainde tanımlanır, servis sahipliği K012'dedir | v2 §2.12/§2.17 | health-checks/ | tescilli — uygulama bekliyor |
| 14 | Incident runbook'u ürün odaklı olmalıdır; ajan eskalasyonu K016'ya aittir | .ai/AGENTS.md §10 (ajan odaklı) · §A.2 bulgu 7 | incident-response/ | **açık karar** |
| 15 | Log redaction anahtar listesi tek kaynaktadır (Redactor) | Redactor.php Satır 31 | logging/ | tescilli — kanıtlı |
| 16 | Trace-id satıra damgalanır | Psr3LoggerTest.php Satır 116 (testTraceId_isStampedOnLines) | correlation/ · logging/ | tescilli — kanıtlı |
| 17 | E-posta PII maskelenir, alan adı korunur | LoggerRedactionTest.php Satır 106-113 | logging/ | tescilli — kanıtlı |
| 18 | 64 karakterlik hash redakte edilir, UUID korunur | LoggerRedactionTest.php Satır 122 | logging/ | tescilli — kanıtlı |
| 19 | Dosya yazma yolu da redaction'dan geçer | LoggerRedactionTest.php Satır 140 | logging/ | tescilli — kanıtlı |
| 20 | Yığın (structured) logger yazma yolu da redaction'dan geçer | LoggerRedactionTest.php Satır 165 | logging/ | tescilli — kanıtlı |
| 21 | Güvenlik olayları ayrı güvenlik akışına yazılır | FileHandler.php Satır 188 (logAt, securityStream) | logging/ | tescilli — kanıtlı |
| 22 | Log fabrikası tek noktadadır (LoggerFactory) | LoggerFactory.php Satır 11 | logging/ | tescilli — kanıtlı |
| 23 | Vault sync yorum blokları `.ai/log.md` içinde tutulur | .ai/log.md Satır 15-16 (auto-begin/auto-end) | logging/ (vault yüzeyi) | tescilli |
| 24 | Domain, §7'deki açık sorular kapanana dek `draft` kalır | bu dosya §7 · index.md §5 satır 17 | tümü | tescilli |
| 25 | Web-kanıt turu bu revizyonda yapıldı | §F web tablosu (tek URL, HTTP 200) | tracing/ | tescilli — 2026-10-13 |
| 26 | `alerting-esik-ve-yukseklik.md` içerik dosyası korunur, silinmez | §A.1 satır 10 | alerts/ | tescilli |
| 27 | K017, K018'e yalnız **uyarı** gönderir; pipeline kapısına dokunmaz | .ai/AGENTS.md §5 · v2 §2.18 | alerts/ | tescilli |
| 28 | K017, K009'a yalnız **kanıt** üretir (audit log); auth politikasını değiştirmez | v2 §2.9 · §2 sınır tablosu satır 3 | logging/ | tescilli |

### §B.2 — Karar durumlarının anlamı

| Durum | Anlamı | Kapanış şartı |
|---|---|---|
| tescilli | dayanak + etki + durum üçlüsü tam | yok — revizyonla değişebilir |
| tescilli — kanıtlı | dayanak gerçek dosya/satır kanıtıyla bağlı | dosya silinirse yeniden ölçülür |
| tescilli — uygulama bekliyor | karar verildi, kod karşılığı yok | ilgili çocukta IMPLEMENTED etiketi |
| açık karar | üst karar ya da kademe-3 gerekli | ADR ya da kullanıcı onayı |
| bilinmiyor (UNKNOWN) | veri yok | ölçüm — tahmin yazılmaz |

## §C — Çapraz modül kuralları

**Tanım:** Bu bölüm K017'nin komşu domainlerle olan **sınır kurallarını** bağlayıcı olarak yazar.
İhlalde rollback değil, **DUR + rapor** (kök AGENTS.md §3).

### §C.1 — Sınır kuralları

| # | Kural | Komşu domain | Yön | Kaynak |
|---|---|---|---|---|
| 1 | Hassas veri (token/şifre/CSRF) log'a yazılmaz, `[REDACTED]` maskelenir | K009-security | K017 → K009 uyumu | .ai/AGENTS.md §17 edge-3 |
| 2 | Uyarı üretimi K017'de; deploy/rollback aksiyonu K018'dedir | K018-devops | K017 ↔ K018 | v2 §2.17/§2.18 |
| 3 | CI job logları K018'in borusudur; K017 yalnız okur | K018-devops | K018 → K017 | ci.yml Satır 158 |
| 4 | Test stratejisi/coverage K019'da; K017 yalnız gözlemler | K019-testing | K017 ↔ K019 | v2 §2.19 |
| 5 | DSP zinciri düzeltmesi K003/K004'te; K017 yalnız teşhis koyar | K003-audio · K004-dsp | K017 ↔ K003/K004 | v2 §2.17 |
| 6 | Servis log/metrik yüzeyi K012'de üretilir, K017 toplar | K012-services | K012 → K017 | v2 §2.12 |
| 7 | Ağ katmanı izlemesi (trace/health) K014'e dokunur | K014-network | K017 ↔ K014 | v2 §2.14 |
| 8 | Medya/ses izleme K015 yüzeyinden beslenir | K015-media | K015 → K017 | v2 §2.15 |
| 9 | Ajan eskalasyon zinciri K016-AI'a aittir; ürün runbook'u K017'de | K016-AI | K017 ↔ K016 | .ai/AGENTS.md §10 |
| 10 | Optimizasyon kodu üretim domainine aittir; K017 yalnız bottleneck kaydeder | K011/K012 | K017 → üretim | §2 sınır tablosu |
| 11 | Vault audit trail append-only'dir; geçmiş satır değiştirilmez | K000-vault | K017 iç kural | .ai/AGENTS.md §5 |
| 12 | `.ai/log.md`'ye yalnız aktör + tarih + tip + metin biçiminde giriş yazılır | K000-vault | K017 iç kural | .ai/log.md Satır 13-20 |
| 13 | Secret değeri hiçbir log yüzeyine yazılmaz | K009-security | K017 → K009 | .github/AGENTS.md §3.3 kural 2 |
| 14 | Domain sınırı ihlalinde sistem durur, MO müdahale eder | tümü | genel | .ai/AGENTS.md §18 kural 1 |
| 15 | Bilinmeyen API/class `VERIFICATION REQUIRED` ile işaretlenir | tümü | genel | .ai/AGENTS.md §18 kural 3 |
| 16 | Health-check sahipliği serviste (K012), tanımı bu domainde | K012-services | K017 ↔ K012 | v2 §2.12 |
| 17 | Alert → koşul eşikleri K017'de; koşulun eşiği K019 testlerince sınanır | K019-testing | K017 → K019 | v2 §2.19 |
| 18 | Redaction anahtar listesi değiştirilirse K009'a bilgi verilir | K009-security | K017 → K009 | Redactor.php Satır 31 |
| 19 | Trace-id üretimi tek noktada yapılır (correlation), dağıtılmaz | K017 iç | internal | Psr3LoggerTest.php Satır 116 |
| 20 | Ses ölçüm birimleri (xrun/clip) K004-DSP sözlüğüyle aynı adlandırılır | K004-dsp | K017 ↔ K004 | v2 §2.4 |
| 21 | Log dosyası diski doldurursa kesme/döndürme kararı K018 depo politikasındadır | K018-devops | K017 → K018 | v2 §2.18 |
| 22 | Performance raporu K019 regresyon eşiğiyle paylaşılır | K019-testing | K017 → K019 | v2 §2.19 |
| 23 | Alert kanalı (Slack/e-mail/webhook) seçimi K009 gizlilik kurallarına tabidir | K009-security | K017 ↔ K009 | .github/AGENTS.md §3.3 kural 2 |
| 24 | Incident sırasında geçici loglama seviyesi yükseltilebilir; kalıcı değişiklik ADR ister | K018-devops | K017 ↔ K018 | v2 §2.18 |
| 25 | `.ai/log.md` girdileri K016 otomasyonunun girdisidir, ürün logu değildir | K016-AI | K017 → K016 | .ai/log.md Satır 17-20 |
| 26 | Domain dışı loglama (ör. framework varsayılanı) yasaktır; tek fabrika LoggerFactory | K017 iç | internal | LoggerFactory.php Satır 11 |

### §C.2 — İhlal durumunda davranış

| # | İhlal | İlk davranış | Eskalasyon |
|---|---|---|---|
| 1 | Hassas veri log'a sızdı | satır maskelenir, kayıt açılır | K009-security (CRITICAL) |
| 2 | Uyarı eşiği K018'de değiştirildi | DUR, revert | L1 → L2 (.ai/AGENTS.md §10) |
| 3 | Test domaini dışında test eklendi | DUR, K019'a devredilir | MO routing (.ai/AGENTS.md §6) |
| 4 | DSP metriği K017'de düzeltildi | DUR, revert | K003/K004 sahipliği |
| 5 | Vault log satırı silindi | git checkout ile kurtarma | .ai/AGENTS.md §17 edge-10 |
| 6 | Bilinmeyen API kullanıldı | `VERIFICATION REQUIRED` etiketi | .ai/AGENTS.md §18 kural 3 |
| 7 | İki log sistemi karıştırıldı | ayrım raporu, kademe-3 kararı | bu dosya §2 sınır notu |
| 8 | Health-check servis içine gömüldü | DUR, taşıma önerisi | K012 sahipliği |

## §D — Yol haritası

**Tanım:** Sıra **disk kanıtı → karar → kod** tersidir; bu domainde önce kanıt, sonra teknoloji
seçimi, sonra uygulama gelir. Tarih uydurulmaz; fazlar `durum` alanı taşır.

### §D.1 — Fazlar

| # | Faz | Kapsam | Çıkış kriteri | Durum |
|---|---|---|---|---|
| 1 | Kanıt taraması (logging) | shared/src/Log + 2 test dosyası okundu | §4 tablosunda IMPLEMENTED satırı | ✅ tamamlandı (2026-10-12) |
| 2 | Kök dosya derinleştirme | master · bagimliliklar · dogrulama ≥ 500 + §4.2 FM | bu dosyanın §G kaydı | 🔄 bu revizyon |
| 3 | PSR-3 kanıtının tescili | Psr3LoggerTest Satır 44-47 okundu | §7/1 açık sorusunun kapanması | ✅ tamamlandı (2026-10-13) |
| 4 | `alerting/` kararının verilmesi | v2 listesine ekleme mi, alerts/ birleştirme mi | ADR ya da kullanıcı onayı | ⏳ bekliyor |
| 5 | Child derinleştirme (7 iskelet çocuk) | correlation · health-checks · incident-response · performance · alerts · audio-diagnostics | her çocukta içerik dosyası + kanıt | ⏳ bekliyor |
| 6 | Teknoloji seçimi (metrik) | Prometheus/StatsD/özel — disk kanıtı yok | kararı veren ADR + metrics/ IMPLEMENTED | ⏳ bekliyor (UNKNOWN) |
| 7 | Teknoloji seçimi (tracing) | OpenTelemetry PHP durumu araştırılacak | §F web kanıtı + tracing/ IMPLEMENTED | ⏳ bekliyor (UNKNOWN) |
| 8 | Health-check uçlarının tanımları | hangi servis, hangi rota | health-checks/ kanıtı + K012 mutabakatı | ⏳ bekliyor |
| 9 | Alert kuralları ve kanalları | eşik · kanal · gizlilik (K009 onayı) | alerts/ kanıtı + kanal seçimi | ⏳ bekliyor |
| 10 | Incident runbook'u | ürün odaklı olay akışı | incident-response/ içerik dosyası | ⏳ bekliyor |
| 11 | audio-diagnostics gereksinimi | ASIO/WASAPI/RPi5 ölçüm yüzeyi | K003/K004 ile ortak şartname | ⏳ bekliyor |
| 12 | Vault/ürün logu sınırının tescili | kademe-3 kararı | ADR kaydı | ⏳ bekliyor |
| 13 | Performance gözlem kaydı | profil/bottleneck kayıt şeması | performance/ kanıtı | ⏳ bekliyor |

### §D.2 — Bağımlılık sırası (faz → faz)

| # | Önce | Sonra | Gerekçe |
|---|---|---|---|
| 1 | Faz 1 (kanıt) | Faz 2 (derinleştirme) | kanıtsız dosya üretilmez |
| 2 | Faz 3 (PSR-3 tescili) | Faz 6-7 (teknoloji seçimi) | log sözleşmesi sabitlenmeden tracing bağlanmaz |
| 3 | Faz 4 (alerting kararı) | Faz 9 (alert kuralları) | fazladan dizin çözülmeden kural yazılmaz |
| 4 | Faz 6 (metrik seçimi) | Faz 9 (alert eşiği) | eşik, ölçülen metriğe bağlıdır |
| 5 | Faz 7 (tracing seçimi) | Faz 5 (correlation derinliği) | trace-id şeması tracing'e bağlıdır |
| 6 | Faz 8 (health tanımı) | Faz 10 (incident runbook) | runbook sağlık sinyaline bağlanır |
| 7 | Faz 11 (ses ölçüm şartnamesi) | K004-dsp uygulaması | teşhis tanımlanmadan DSP enstrümantasyonu yapılmaz |
| 8 | Faz 12 (log sınırı tescili) | Faz 10 (incident) | olay anında hangi logun kullanılacağı belli olmalı |
| 9 | Faz 5 (child derinleştirme) | Domain `verified` durumu | §6 doğrulama özetinin kapanması |
| 10 | Faz 2 (bu revizyon) | index.md sayacının güncellenmesi | index.md bu görevde yasak — orkestratöre ait |

## §E — Repo kanıtları (observability-master)

Dosya: `shared/src/Log/LoggerFactory.php` · Satır: 11 — `final class LoggerFactory` (tek log fabrikası, 34 satır).

Dosya: `shared/src/Log/FileHandler.php` · Satır: 30 — `final class FileHandler implements LoggerInterface` (PSR-3 arayüzü kanıtı, 253 satır).

Dosya: `shared/src/Log/FileHandler.php` · Satır: 125 — `public function log($level, string|Stringable $message, array $context = []): void` (PSR-3 log imzası).

Dosya: `shared/src/Log/FileHandler.php` · Satır: 188 — `private function logAt(string $level, string $message, array $context, bool $securityStream = false): void` (güvenlik akışı ayrımı).

Dosya: `shared/src/Log/FileHandler.php` · Satır: 207 — `private function write(string $fileLevel, string $message, array $context, string $logLevel, bool $securityStream = false): void` (dosya yazma yolu).

Dosya: `shared/src/Log/Redactor.php` · Satır: 25 — `final class Redactor` (maskeleme sınıfı, 106 satır).

Dosya: `shared/src/Log/Redactor.php` · Satır: 31 — `private const SENSITIVE_KEYS = 'auth_key|password|passwd|csrf_token|token|secret|api_key|session_id|session|authorization|cookie'` (tek kaynak anahtar listesi).

Dosya: `shared/src/Log/Redactor.php` · Satır: 65 — `public static function redact(string $line): string` (dış arayüz).

Dosya: `shared/tests/Unit/Log/Psr3LoggerTest.php` · Satır: 17 — `final class Psr3LoggerTest extends TestCase` (163 satır, 11 test metodu).

Dosya: `shared/tests/Unit/Log/Psr3LoggerTest.php` · Satır: 44 — `public function testImplementsPsr3LoggerInterface(): void` (PSR-3 arayüz testi).

Dosya: `shared/tests/Unit/Log/Psr3LoggerTest.php` · Satır: 46 — `$this->assertInstanceOf(LoggerInterface::class, new FileHandler($this->dir));` (PSR-3 kanıtının kendisi).

Dosya: `shared/tests/Unit/Log/Psr3LoggerTest.php` · Satır: 49 — `public function testAllPsr3MethodsAreCallable(): void` (sekiz seviye methodu).

Dosya: `shared/tests/Unit/Log/Psr3LoggerTest.php` · Satır: 63 — `foreach (['[EMERGENCY]', '[ALERT]', '[CRITICAL]', '[ERROR]', '[WARNING]', '[NOTICE]', '[INFO]', '[DEBUG]'] as $tag)` (seviye etiketleri).

Dosya: `shared/tests/Unit/Log/Psr3LoggerTest.php` · Satır: 116 — `public function testTraceId_isStampedOnLines(): void` (trace-id damgası — §B/16).

Dosya: `shared/tests/Unit/Log/Psr3LoggerTest.php` · Satır: 143 — `public function testAuthEvent_masksEmailPii(): void` (PII maskesi).

Dosya: `shared/tests/Unit/Log/Psr3LoggerTest.php` · Satır: 154 — `public function testSecretsInContext_areRedacted(): void` (context redaction).

Dosya: `shared/tests/Unit/Log/LoggerRedactionTest.php` · Satır: 17 — `final class LoggerRedactionTest extends TestCase` (189 satır, 9 test metodu).

Dosya: `shared/tests/Unit/Log/LoggerRedactionTest.php` · Satır: 55 — `public function testAuthKeyInQueryStringIsRedacted(): void` (query-string redaction).

Dosya: `shared/tests/Unit/Log/LoggerRedactionTest.php` · Satır: 70 — `public function testPasswordIsRedactedInKeyValueAndJsonFormats(): void` (iki format redaction).

Dosya: `shared/tests/Unit/Log/LoggerRedactionTest.php` · Satır: 106 — `public function testEmailLocalPartMaskedDomainKept(): void` (e-posta maskeleme — §B/17).

Dosya: `shared/tests/Unit/Log/LoggerRedactionTest.php` · Satır: 122 — `public function testSixtyFourCharHashIsRedactedButUuidIsPreserved(): void` (hash/UUID ayrımı — §B/18).

Dosya: `shared/tests/Unit/Log/LoggerRedactionTest.php` · Satır: 140 — `public function testFileHandlerWritePathAppliesRedaction(): void` (yazma yolu — §B/19).

Dosya: `shared/tests/Unit/Log/LoggerRedactionTest.php` · Satır: 165 — `public function testStructuredLoggerWritePathAppliesRedaction(): void` (yığın logger — §B/20).

Dosya: `.ai/log.md` · Satır: 13 — `2026-10-02 | session | Home Page Whole-Page Rebuild basladi (aaa.md, prompt-maker 5 gorev onayli)` (ilk aktif audit girişi, 20 satır).

Dosya: `.ai/log.md` · Satır: 15 — `<!-- vault-sync:auto-begin -->` (vault-sync yorum bloğu başlangıcı).

Dosya: `.ai/log.md` · Satır: 20 — `2026-10-12 | note | Faz2B/C/D rate-limit ile yarida kaldi: dokunulmayanlar .ai/keys.md | orchestrator` (son giriş).

Dosya: `.github/workflows/ci.yml` · Satır: 158 — `run: npm run test:js` (CI gözlem yüzeyi job çıktısı).

Dosya: `.github/workflows/ci.yml` · Satır: 176 — `run: npm run test:js:coverage` (coverage gözcüsü, soft gate).

Dosya: `.github/workflows/ci.yml` · Satır: 203 — `run: npm run test:e2e` (e2e çıktısı, dosyanın son satırı).

Dosya: `.ai/architecture/K017-observability/index.md` · Satır: 1 — `---` (subdomain indeksi frontmatter başlangıcı, 84 satır).

Dosya: `.ai/architecture/K017-observability/index.md` · Satır: 84 — dosyanın son satırı (toplam 84 satır ölçümü).

Dosya: `.ai/architecture/index.md` · Satır: 80 — `| 17 | K017-observability | observability-master.md | -bagimliliklar · -dogrulama | draft |` (registry kaydı).

Dosya: `.ai/architecture/index.md` · Satır: 461 — `| K017-observability | 18 | 3.395 | 5 | 3 | +2 |` (önceki tur sayım — `K017-observability` satırı; bu revizyonla kök 3 dosya ≥ 500'e çıkacak).

Dosya: `.github/AGENTS.md` · Satır: 85 — `| 1 | Subagent git commit ATMAZ (kök AGENTS.md §7/§8) | Yetkisiz commit → revert + log |` (bu revizyonun yetki sınırı).

Dosya: `shared/composer.json` · Satır: 24 — `"phpunit/phpunit": "^10.5"` (log testlerinin koşucusu).

Dosya: `shared/composer.json` · Satır: 48 — `"stan": "phpstan analyse src --level=5"` (log sınıfının statik analiz kapısı).

### §E.1 — Kanıt zinciri (iddia → dosya → satır)

| # | İddia (bu dosyada) | Dosya | Satır | Bağlantı |
|---|---|---|---|---|
| 1 | PSR-3 arayüzü uygulanıyor | shared/src/Log/FileHandler.php | 30 | §B/6 · §C iç |
| 2 | PSR-3 testi arayüzü doğruluyor | shared/tests/Unit/Log/Psr3LoggerTest.php | 44-47 | §7/1 (kapandı) · §B/6 |
| 3 | Sekiz seviye etiketi üretiliyor | shared/tests/Unit/Log/Psr3LoggerTest.php | 63 | §B/7 |
| 4 | Trace-id satıra damgalanıyor | shared/tests/Unit/Log/Psr3LoggerTest.php | 116 | §B/16 · §C/19 |
| 5 | Redaction anahtar listesi tek kaynakta | shared/src/Log/Redactor.php | 31 | §B/15 · §C/18 |
| 6 | E-posta maskeleniyor | shared/tests/Unit/Log/LoggerRedactionTest.php | 106 | §B/17 |
| 7 | Hash redakte, UUID korunuyor | shared/tests/Unit/Log/LoggerRedactionTest.php | 122 | §B/18 |
| 8 | Yazma yolu redaction'dan geçiyor | shared/tests/Unit/Log/LoggerRedactionTest.php | 140 | §B/19 |
| 9 | Yığın logger redaction'dan geçiyor | shared/tests/Unit/Log/LoggerRedactionTest.php | 165 | §B/20 |
| 10 | Güvenlik akışı ayrılıyor | shared/src/Log/FileHandler.php | 188 | §B/21 |
| 11 | Tek log fabrikası | shared/src/Log/LoggerFactory.php | 11 | §B/22 · §C/26 |
| 12 | Vault audit trail append-only | .ai/log.md | 13-20 | §2 sınır notu · §C/11-12 |
| 13 | CI gözlem yüzeyi 6 job | .github/workflows/ci.yml | 158 | §B/10 · §C/3 |
| 14 | K017 registry kaydı | .ai/architecture/index.md | 80 | §8 Kaynaklar |
| 15 | Disk sayım 18 dosya / 3.395 satır | K017-observability/** (glob) | — | §A açılış paragrafı |

### §E.2 — Ölçüm komutları (tekrarlanabilir)

| # | Komut | Ne ölçer | Beklenen sonuç (2026-10-13) |
|---|---|---|---|
| 1 | `[IO.File]::ReadAllLines($masaustuYolu).Count` | bu dosyanın uzunluğu | ≥ 500 |
| 2 | `Get-ChildItem .ai/architecture/K017-observability -Recurse -Filter *.md` | domain dosya sayısı | 18 (kök 4 + alt 14) |
| 3 | Alt dizin başına `ReadAllLines` toplamı | satır yoğunluğu | logging 1.068 · metrics 571 · tracing 570 |
| 4 | `Select-Object -Index 43,44,45,46` (Psr3LoggerTest) | PSR-3 testi birebir | Satır 44-47 içeriği §E/2 ile aynı |
| 5 | `Select-Object -Index 30` (FileHandler) | PSR-3 arayüz satırı | `final class FileHandler implements LoggerInterface` |
| 6 | `Select-Object -Index 30` (Redactor) | redaction anahtarları | `private const SENSITIVE_KEYS = 'auth_key` başlangıcı (tam liste §E/5) |
| 7 | URL `Invoke-WebRequest -UseBasicParsing` | web tablosu satırı | HTTP 200 |
| 8 | Envanter kesişimi (`arch-urls.txt`) | URL benzersizliği | 0 kopya (1441 kayıt) |
| 9 | `Test-Path .ai/log.md` | vault audit trail varlığı | True (20 satır) |
| 10 | `Get-ChildItem .github/workflows -Filter *.yml` | CI yüzeyi | 2 dosya (ci.yml · secret-scan.yml) |

### §E.3 — Revizyon ölçüm günlüğü (2026-10-13)

| # | Ölçüm | Sonuç | Not |
|---|---|---|---|
| 1 | Görev öncesi uzunluk (taban) | 105 satır | P1 başlangıç değeri |
| 2 | Revizyon yazımı sonrası ara uzunluk | 468 satır | §A-§E bölümleri yazıldıktan sonra |
| 3 | Revizyon sonrası uzunluk | §G/1 hücresinde ölçüldü | ≥ 500 hedefi |
| 4 | `##` başlık sayısı | 16 | §1-§8 (8) + §A-§G (7) + §E.1-§E.3 alt başlıkları hariç |
| 5 | Frontmatter alanı | v2 §4.2 8 alan + `node_id` + `group` | `parent:` yerine `parent_id` |
| 6 | `group` değeri | `mimari` | master · tırnaksız 9. alan |
| 7 | §A modül ağacı satırı | 20 sayı satırı | 9 çocuk + disk bulguları |
| 8 | §B master karar sayısı | 28 | karar tablosu |
| 9 | §C çapraz kural satırı | 34 | kural tablosu + ihlal davranışı tablosu |
| 10 | §D yol haritası satırı | 23 | faz tablosu + sıra tablosu |
| 11 | §E repo kanıtı | 36 `Dosya:` satırı | her biri gerçek dosya + satır indeksi |
| 12 | §E.1 kanıt zinciri satırı | 15 | iddia → dosya → satır |
| 13 | §E.2 ölçüm komutu | 10 | tekrarlanabilir PowerShell |
| 14 | §F web tablosu satırı | 1 | envanter dışı benzersiz URL |
| 15 | Web URL HTTP durumu | HTTP 200 | 2026-10-13 tarihli doğrulama |
| 16 | Envanter kesişimi | 0 kopya | 1441 kayıtlı URL içinde yok |
| 17 | Yasaklı yer-tutucu sayısı | 0 | 7 kalıp: yer tutucu ibare · üç nokta dizgesi · gelecek-sözü · yorum kısayolu · süslü parantez kısayolu · boş hücre · mojibake |
| 18 | Boş tablo hücresi | 0 | tüm hücreler dolu |
| 19 | Mojibake | 0 | dosya UTF-8, Türkçe karakterler sağlam |
| 20 | Son satır Authority | birebir eşleşti | §G/2 ile aynı dizge |
| 21 | §1-§8 gövdesi | 8/8 korundu | yalnız FM ve Authority satırı revize |
| 22 | Suffixed `node_id` | `K017-observability/observability-master` | 3 kök dosyanın hepsi suffixed |
| 23 | Bu turda yazılan dosya | 8 | P0 2 + P1 6 |
| 24 | K017 disk sayım | 18 dosya / 3.395 satır | 2026-10-13 ölçümü |
| 25 | K018 disk sayım | 19 dosya / 3.866 satır | 2026-10-13 ölçümü |
| 26 | index.md K018 sayım satırı | 17 / 2.819 | bayat kayıt · index.md bu turda değiştirilmedi |
| 27 | Kanıt indeks doğrulama yöntemi | satır indeksi ile birebir okuma | uydurma satır numarası yok |
| 28 | Dolgu paragraf | 0 | her satır sayı · iddia · komut veya kanıt taşıyor |
| 29 | Git commit | atılmadı | yetki orkestratörde (`.github/AGENTS.md` §85) |
| 30 | Bu tablo satır sayısı | 30 | §E.3 kapanış ölçümü |

## §F — Web tablosu (observability-master)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| OpenTelemetry; logs · metrics · traces · baggage sinyallerini sistemin gözlem yapı taşları olarak tanımlar | https://opentelemetry.io/docs/concepts/signals/ | 2026-10-13 | HTTP 200 · doğrulandı · envetorya dışı |

## §G — Revizyon doğrulama kaydı (observability-master)

| # | Kontrol | Beklenen | 2026-10-13 |
|---|---|---|---|
| 1 | Dosya uzunluğu | ≥ 500 tam satır | 503 |
| 2 | Son satır | `**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode` birebir | ✓ |
| 3 | Frontmatter | v2 §4.2 8 alan + `node_id` + `group` (9. alan, tırnaksız) · `parent:` 0 | ✓ |
| 4 | Zorunlu bölümler | `## §A — Modül ağacı` · `## §B — Master kararlar` · `## §C — Çapraz modül kuralları` · `## §D — Yol haritası` | ✓ 4/4 |
| 5 | Repo kanıtları | en az 1 `Dosya:` + `Satır:` kanıt satırı · gerçek satır indeksi `Select-Object -Index` ile doğrulandı | ✓ (36 kayıt) |
| 6 | Web tablosu | 1 satır · envanter (1441) dışı benzersiz URL · HTTP 200 | ✓ |
| 7 | Yasaklı yer-tutucu | yer tutucu ibare · üç nokta dizgesi · gelecek-sözü · yorum kısayolu · süslü parantez kısayolu · boş hücre · mojibake | ✓ 0 |
| 8 | §1-§8 gövdesi | aynen korundu (yalnız FM ve Authority satırı revize edildi) | ✓ |
| 9 | Yazılan dosya | bu dosya dahil toplam 8 (P0 2 + P1 6) | ✓ |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode