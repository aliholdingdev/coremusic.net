---
title: "K017-observability — Bağımlılıklar"
node_id: K017-observability
node_type: domain-dependencies
status: draft
updated: 2026-10-12
version: 1.0.0
---

# K017-observability — observability-bagimliliklar.md

> **Etiketler:** kanıtlı = dosya/okuma gösterildi · planlanan = mimari yargı · vault beyanı = bağımsız ölçüm yok.

## §1 Amaç

K017-observability'nin depends_on/used_by haritası, entegrasyon sınırları ve riskli bağımlılıkları.
Kaynak: v2 §2.17 + disk ölçümü 2026-10-12 (shared/src/Log · .ai/log.md · ci.yml).

## §2 depends_on (bu domain kime muhtaç)

| # | Hedef | Etiket | Gerekçe | Kanıt / dayanak |
|---|---|---|---|---|
| 1 | K012-services | planlanan | log/metric üreten asıl yüzey servis katmanıdır | v2 §2.12 · genel mimari yargı |
| 2 | K014-network | planlanan | tracing/health ağ trafiğini gözlemler | v2 §2.14 · genel mimari yargı |
| 3 | K015-media | planlanan | audio-diagnostics ses zincirine dokunur | v2 §2.15/§2.17 children |
| 4 | K018-devops | planlanan | alert çıktısı ortam/pipeline kararını besler | mimari yargı (ADR gerekli) |
| 5 | shared/src/Log (K011-K012 kod tabanı) | kanıtlı | logging/ bu sınıfta yaşar | glob 2026-10-12 |

## §3 used_by (bu domain kimleri besliyor)

| # | Kullanıcı | Etiket | Gerekçe |
|---|---|---|---|
| 1 | K018-devops | kanıtlı (kısmi) | CI job logları gözetim yüzeyidir (ci.yml 6 job) |
| 2 | K009-security | planlanan | redaction + audit logları güvenlik denetimini besler (Redactor.php + LoggerRedactionTest) |
| 3 | K019-testing | planlanan | performance/ regression testleriyle karşılaştırılır |
| 4 | Operatör / Vault Steward | kanıtlı (doküman) | .ai/log.md + §10 eskalasyon zinciri |

## §4 Entegrasyon Sınırları

| Sınır | Kural | Kaynak |
|---|---|---|
| K017-logging ↔ K009-security | Hassas veri log'a **asla** yazılmaz; redaction zorunlu (REDACTED) | .ai/AGENTS.md §17 edge-3 |
| K017 ↔ K018-devops | Gözlem **uyarı üretir**, kapıyı (deploy/rollback) K018 işletir | v2 §2.17/§2.18 ayrımı |
| K017 ↔ K019-testing | Gözlem **ölçer**, kararı test domaini verir | v2 §2.19 |
| K017 ↔ K003-audio | audio-diagnostics **teşhis** tanımlar; DSP düzeltmesi K003/K004'tedir | v2 §2.3/§2.4/§2.17 |
| Vault log ↔ uygulama log | .ai/log.md (append-only, ajan izi) ≠ shared/src/Log (ürün logu) | .ai/AGENTS.md §5 |

## §5 Riskli Bağımlılıklar

| # | Risk | Etki | Azaltma (öneri) |
|---|---|---|---|
| 1 | **Tek log fabrikası:** tüm uygulama shared/src/Log'a bağlı (3 dosya) — change-risk yüksek | log değişikliği her katmanı etkiler | API sabitleme + LoggerRedactionTest gibi regresyon testleri korunmalı (kanıtlı: 2 test var) |
| 2 | **Kanıt dengesizliği:** 8/9 çocuk kanıtsız | domain kağıtta tam, diskte logging-merkezli | sonraki turda child bazlı disk sayımı |
| 3 | **Alert→Deploy döngüsü belirsiz:** K017↔K018 sınırı çizilmemiş | yanlış domainde kapak değişimi | ADR: "uyarı K017, aksiyon K018" |
| 4 | **audio-diagnostics bağımlılığı:** donanım/altyapı gerektirir (ASIO xrun vb.) | kanıtsız, pahalı doğrulama | sonraki turda Neva Engine/ASIO kanıtı ile eşleştir |
| 5 | **İki log sistemi:** vault audit trail + ürün logu | kafa karışıklığı, çift bakım | ayrım tescili (kademe-3) |

## §6 Açık Sorular (⚠️ VERIFICATION REQUIRED)

1. K012-services somut hangi log/metric yüzeyini açacak? (interfeys yok)
2. K018-devops'a geri besleme kanalı (webhook/hook) tasarlandı mı? — plan yok.
3. shared/src/Log dışındaki log kullanan katmanlar var mı? — bu turda taranmadı.

## §7 Kaynaklar

- v2 spec §2.17 · ADR-097 §2/§5 · .ai/architecture/index.md §5 satır 17
- Disk ölçümü: shared/src/Log/ · shared/tests/Unit/Log/ · .ai/log.md · .github/workflows/ci.yml (2026-10-12)

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
