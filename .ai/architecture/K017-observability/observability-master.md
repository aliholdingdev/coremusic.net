---
title: "K017-observability — Master (Domain Anayasa)"
node_id: K017-observability
node_type: domain-master
status: draft
authority: "Derived — SSOT: v2 spec §2.17 + ADR-097 (draft)"
updated: 2026-10-12
version: 1.0.0
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

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode · 2026-10-12
