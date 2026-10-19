---
title: "K18-testing/e2e — Subdomain Kaydı"
node_id: K18-testing/e2e
node_type: subdomain
status: draft
parent_id: K18-testing
children: []        # kademe-3 modül dokümanı e2e-tarayici-testi.md (kademe-3 zinciri ADR-097 K4 — sonraki faz)
depends_on:
  - K18-testing    # domain kökü
  - K17-devops     # js-e2e CI job'ı (ci.yml L178–L203) — kanıtlı
evidence:
  - "@playwright/test ^1.63.0 + playwright ^1.63.0 + axe-playwright ^1.0.0 — root package.json L11/L13/L15 (grep 2026-10-12)"
  - "assets.coremusic.net/playwright.config.ts — okundu 2026-10-12: testDir ./tests/e2e · testMatch **/*.spec.ts · baseURL http://home.coremusic.net:81 · webServer php -S (command: npx playwright test -c assets.coremusic.net/playwright.config.ts / npm run test:e2e)"
  - "assets.coremusic.net/tests/e2e/player-info.spec.ts = 1 GERÇEK Playwright spec — okundu 2026-10-12 (import { test, expect } from '@playwright/test' + axe-playwright; HTTP 200 · 1024/1920 viewport · play toggle · seek · axe a11y · auth bypass)"
  - "playwright-report/index.html + test-results/.last-run.json — glob 2026-10-12 (bir kez koşturulmuş görünüyor)"
  - "kök e2e/ = 0 dosya (glob 2026-10-12) — repo-kök e2e/ ile .ai/architecture/K18-testing/e2e/ FARKLI dizinlerdir; master '0 spec' beyanı yalnız repo-kök için doğru; ci.yml js-e2e job: continue-on-error (soft/A6), L178–L203"
  - "e2e/e2e-tarayici-testi.md = Kademe-3 modül dokümanı (node_id K019-testing/e2e/e2e-tarayici-testi;513 satır) — okundu 2026-10-17"
  - "5 kardeş dosyanın §12/§13 entegrasyon tabloları + K000-isletim-sistemi dış alan dosyası K019-testing/e2e node_id'sine referans veriyor — grep 2026-10-17 (5+1 isabet, 0 kırık)"
validation:
  - "glob + dosya okuma (playwright.config.ts · player-info.spec.ts · ci.yml) 2026-10-12 — E2E KANITI KISMI: 1 spec kanıtlı, kapsam dar (tek sayfa/bileşen)"
  - "2026-10-17 birleştirme: eski K019-testing/end-to-end (index-only, modül YOK) → e2e/ altına taşındı; gerekçe: modül dokümanı + dış referanslar K019-testing/e2e node_id'sini kullanıyor, end-to-end/ yalnız K18 içi metinde geçiyordu (disk kanıtı kararı — kullanıcı onayı 2026-10-17)"
open_questions:
  - "E2E kapsamı 1 spec ile mi sınırlandırılacak, yoksa spec ailesi büyütülecek mi? (master §7 açık soru 2 bu ölçüyle büyük ölçüde kapandı — kapsam kararı açık)"
  - "js-e2e soft gate (continue-on-error) ne zaman hard olacak?"
group: mimari
---


# K019-testing/e2e

## 1. Rol (ELI15)

Gerçek tarayıcıda uçtan uca akış (sayfa → etkileşim → beklenen sonuç) — birimlerin **bir arada kullanıcı gözüyle** çalıştığını kanıtlar.

## 2. Kapsam Sınırı

- Bu düğüm yalnız K18-testing altındadır.
- E2E'nin CI'da koşumu K17-devops'tur (js-e2e job — soft); burada senaryo/aralık.
- Kademe-3 modül dokümanı: [[e2e-tarayici-testi]] (K18-testing/e2e/e2e-tarayici-testi); zincir ADR-097 K4.

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (🔶 KISMI — 1 Playwright spec + config kanıtlı, kapsam dar) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) · birleştirme (2026-10-17) |
| Domain kökü | [[index]] (K019-testing) · master: testing-master.md |
| Modül dokümanı | e2e-tarayici-testi.md (513 satır, 2026-10-12 ölçümü) |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| kod | `e2e-tarayici-testi.md` | Locator, assertion, WCAG, bekleme kuralları | Kademe-3 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-17
