---
title: "K016-observability/alerting — Subdomain Kaydı"
node_id: K016-observability/alerting
node_type: subdomain
status: draft
parent_id: K016-observability
children:
  - K016-observability/alerting/alerting-esik-ve-yukseklik
depends_on:
  - K016-observability
  - K016-observability/metrics
  - K016-observability/alerts
evidence:
  - "alerting/alerting-esik-ve-yukseklik.md · 553 satır · diskte mevcut (2026-10-13 ölçümü)"
  - "shared/src/AI/AIEngine.php · Satır 174-194 — eşik kuralları (predictFault; alerting-esik dosyası kanıtı)"
validation:
  - "alerting/ disk fazlası (kademe-3 adayı) — TAŞINMAZ, açık karar (⚠️ VERIFICATION REQUIRED)"
open_questions:
  - "alerting/ taşınacak mı (alerts/ altına birleşecek) kademe-3'te mi? — KULLANICI KARARI BEKLİYOR (Q2)"
group: mimari
---


# K016-observability/alerting

## §1 Rol (ELI15)

Eşik/yükseklik hesabı: "şu metrik, şu sürede şu eşiği aşarsa mı uyarı?" sorusunun **hesap** tarafı. Uyarı kuralı/kanalı ayrı dosyadır (`alerts/`). Bu subdomain, burn-rate/flapping gibi **eşik mantığını** derinleştirir.

## §2 Kapsam Sınırı

- Bu düğüm yalnız K016-observability (sistem gözetimi) altındadır.
- Uyarı kuralı+kanalı → `alerts/` (sibling) · metrik üretimi → `metrics/` · deploy/aksiyon → K17-devops.
- **Disk fazlası durumu:** `alerting/` registry'de Kademe-2 child olarak yer almaz; diskte fazladan bulunur (kademe-3 adayı). Bu turda **taşınmadı** — sadece index.md eklendi + açık karar raporlandı.

## §3 Durum

| Alan | Değer |
|---|---|
| status | draft (kanıt durumu §frontmatter'da) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) + child doldurma (2026-10-13) |
| Disk durumu | 1 içerik dosyası (553 satır) + bu index |
| Açık karar | Taşıma vs kademe-3 — KULLANICI KARARI (Q2) |
| Domain Kökü | [[index]] (K016-observability) · master: observability-master.md |

## §4 Çocuk Kataloğu

| # | Dosya | node_id | Durum | Satır |
|---|---|---|---|---|
| 1 | `alerting-esik-ve-yukseklik.md` | K016-observability/alerting/alerting-esik-ve-yukseklik | draft (kanıtlı) | 553 |

## §5 Açık Karar (⚠️ VERIFICATION REQUIRED — KULLANICI ONAYI)

| # | Soru | Seçenekler | Etki | Durum |
|---|---|---|---|---|
| 1 | alerting/ taşınacak mı? | (a) alerts/ altına birleş → (b) kademe-3 bağımsız kal → (c) sil → | dosya konumu + registry | **KULLANICI KARARI (Q2)** |

**Q2 kararı (önceki tur):** "analiz et tekrar sor" → alerting/ **TAŞINMAZ**; sadece analiz + bu index + açık karar raporu. Nihai karar kullanıcıdadır — bu index onu bekler.

## §6 Komşu Sınır

| # | Komşu | Sınır | Yön |
|---|---|---|---|
| 1 | alerts/ | Kural+kanal burada; eşik hesabı alerting/ | sibling |
| 2 | metrics/ | Metrik üretimi metrics; eşik mantığı burada | iç |
| 3 | K17-devops | Aksiyon (deploy) K018; eşik K016 | K016 → K018 |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `alerting-esik-ve-yukseklik.md` | Eşik/yükseklik/flapping derinleşmesi | Kademe-3 adayı |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode · 2026-10-13
