---
title: "K18-testing/flaky — Subdomain Kaydı"
node_id: K18-testing/flaky
node_type: subdomain
status: draft
parent_id: K18-testing
children: []        # kademe-3 modül dokümanı flaky-test-tespiti-ve-karantina.md (ADR-097 K4 — sonraki faz)
depends_on:
  - K18-testing    # domain kökü
  - K17-devops     # retry/soft-gate davranışının CI ayağı (ci.yml L167, L178–L203) — kanıtlı
evidence:
  - "flaky/flaky-test-tespiti-ve-karantina.md = Kademe-3 modül dokümanı (node_id K019-testing/flaky/flaky-test-tespiti-ve-karantina;603 satır, .ai/architecture/index.md §513 tescilli) — okundu 2026-10-17"
  - "playwright.config.ts retry: 2 (Satır 20) — flaky maskelenmesi riskinin repo kanıtı (modül dokümanı §11.1)"
  - "ci.yml js-coverage/js-e2e continue-on-error (soft gate) — modül dokümanı §11.5, ci.yml Satır 167/182-183"
  - "contract-test-uyumlulugu.md §12 + regression-test-secim-kurallari.md §7-§9 + K000-isletim-sistemi dış alan dosyası K019-testing/flaky node_id'sine referans veriyor — grep 2026-10-17"
validation:
  - "glob + grep 2026-10-17 — karantina mekanizması repo'da YOK (modül dokümanı §9.3 tescilli); retry YOK; flaky ölçümü yapılmıyor"
  - "2026-10-17: Kademe-2 kaydı bu turda üretildi (önceye kadar yalnız modül dokümanı vardı, children'a işli değildi — disk kanıtı kararı, kullanıcı onayı 2026-10-17)"
open_questions:
  - "Karantina mekanizması (quarantine) ne zaman kurulacak — modül dokümanı §9.3 repo gerçeği: kapı YOK?"
  - "retry: 2 maskelemesi flaky oranı ölçümünü gizliyor mu? (§7.3 maskeleme riski)"
group: mimari
---


# K019-testing/flaky

## 1. Rol (ELI15)

Kararsız (bazen geçip bazen geçen) testlerin tespiti, ölçümü ve karantinaya alınması — "yeşil ama güvenilmez" kapısını kapatır.

## 2. Kapsam Sınırı

- Bu düğüm yalnız K18-testing altındadır.
- Retry/soft-gate CI'da K17-devops; burada tanım, ölçüm, karantina kuralı.
- Kademe-3 modül dokümanı: [[flaky-test-tespiti-ve-karantina]] (K18-testing/flaky/flaky-test-tespiti-ve-karantina); zincir ADR-097 K4.

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (⚠️ KANIT BEKLİYOR — repo'da karantina/flaky ölçümü YOK) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-13 modül · 2026-10-17 kaydı) |
| Domain kökü | [[index]] (K019-testing) · master: testing-master.md |
| Modül dokümanı | flaky-test-tespiti-ve-karantina.md (603 satır) |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| kod | `flaky-test-tespiti-ve-karantina.md` | Tekrar oranı, retry, determinism, karantina kuralları | Kademe-3 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-17
