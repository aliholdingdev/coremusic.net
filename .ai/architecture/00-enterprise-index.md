---
title: "CoreMusic Architecture — Fiziksel Görünüm Kataloğu (k0–k20, derived)"
type: index
category: architecture
version: "1.1.0"
status: active
authority: "DERIVED view — SSOT giriş: .ai/architecture/00-master-index.md (500 katman)"
updated: 2026-10-07
tier: 3
domain: architecture
ssot: false
risk: medium
owner: "Vault Steward"
depends-on: [".ai/architecture/rules.md", ".ai/architecture/00-master-index.md"]
---

# Architecture — Fiziksel Görünüm Kataloğu (k0–k20, derived)

> **Authority (2026-10-07):** Bu dosya **derived** fiziksel görünümdür (k0–k20 landing kataloğu).
> Tek giriş ve SSOT: [[architecture/00-master-index]] (K000–K499 + 10 domain).

> **Kapsam:** `.ai/architecture/` katmanının master kataloğu. Kurallar [[architecture/rules]] · Hot memory [[architecture/context]] · Vault navigasyonu [[index]].

---

## 1. Amaç

`.ai/architecture/` = CoreMusic **AI Engineering Control Plane — mimari katmanı**: K0-K20 + firmware'in governed, katmanlı, SSOT-bağlı envanteri. Beklenen durum (`.ai`) ile gerçek durum (repository) burada karşılaştırılır; fark drift olarak sınıflandırılır.

## 2. Katman Kataloğu (21 + firmware)

| Katman | Dizin | Kapsam (K-matrix §5) | Sahip Agent | Durum |
|---|---|---|---|---|
| K0 | [[architecture/k0-isletim-sistemi]] | OS, process, IPC, thread, bellek, container | win-sw / devops | 🅿️ stub |
| K1 | [[architecture/k1-donanim]] | DAC/ADC, AK4458/XMOS, Class AB, PCB | audio-hw | 🅿️ stub |
| K2 | [[architecture/k2-surucu]] | ASIO, WASAPI, ALSA, sürücü yığını | win-sw | 🅿️ stub |
| K3 | [[architecture/k3-ses-motoru]] | Neva Engine, DSP chain | embedded | 🅿️ stub |
| K4 | [[architecture/k4-yapay-zeka]] | Music analysis, recommendation, voice | (atama: Faz 1) | 🅿️ stub |
| K5 | [[architecture/k5-veri-yonetimi]] | MySQL 9 18 BCNF, Redis, APCu | data | 🅿️ stub |
| K6 | [[architecture/k6-guvenlik]] | JWT, RBAC, CSRF, CSP, vault | security | 🅿️ stub |
| K7 | [[architecture/k7-middleware]] | PSR-15 pipeline | security / backend | 🅿️ stub |
| K8 | [[architecture/k8-servis]] | Servisler, event bus | backend | 🅿️ stub |
| K9 | [[architecture/k9-api-routing]] | Gateway, BFF, CQRS, SPA router | backend | 🅿️ stub |
| K10 | [[architecture/k10-uygulama]] | 10 panel | backend / ui | 🅿️ stub |
| K11 | [[architecture/k11-ux]] | ITCSS, BEM, WCAG 2.2 AA | ui | 🅿️ stub |
| K12 | [[architecture/k12-izleme]] | Prometheus, Grafana, Sentry | devops | 🅿️ stub |
| K13 | [[architecture/k13-cicd]] | GitHub Actions, Docker | devops | 🅿️ stub |
| K14 | [[architecture/k14-ag]] | HTTP/3, WebRTC, DLNA | backend | 🅿️ stub |
| K15 | [[architecture/k15-medya-streaming]] | FFmpeg, HLS, DASH | backend | 🅿️ stub |
| K16 | [[architecture/k16-class-ab]] | MJL21194/93 8 kanal | audio-hw | 🅿️ stub |
| K17 | [[architecture/k17-guc-kaynagi]] | LM5122 ±35V, koruma | audio-hw | 🅿️ stub |
| K18 | [[architecture/k18-termal]] | Fischer SK53, PWM fan | audio-hw | 🅿️ stub |
| K19 | [[architecture/k19-pcb]] | 6-layer stackup, ENIG | audio-hw | 🅿️ stub |
| K20 | [[architecture/k20-bom]] | 1.775 BOM satırı | audio-hw | 🅿️ stub |
| FW | [[architecture/firmware]] | XMOS firmware (cross-cutting) | dsp-fw | 🅿️ stub |

**Durum efsanesi:** 🅿️ stub = iskelet, içerik yok (Faz 1+) · ✍️ draft = içerik yazılıyor · ✅ active = tam tarama + onay geçti.

## 3. Dosya İskeleti (her katman)

```text
kX-<ad>/
├── index.md · README.md · AGENTS.md · CLAUDE.md · WORKFLOW.md   # tam set (Q10)
├── 01-…/02-…/ 03-…/ 04-…/                                       # derinleşme (yalnız gerektiğinde, Q6/Q19)
└── inventory/kX-<tür>-partNN.md                                  # çoklu-md envanter (min 500 satır/md, Q17/Q21)
```

## 4. ID ve Envanter

- Bileşen ID: **Kx.yy.zzz** (Q22) · Envanter 12 alan (Q26) → bkz [[architecture/context]] §3-§4.
- Envanter sütunları bu dosyada **kopyalanmaz** (SSOT: context.md §4; kural: [[architecture/rules]]).

## 5. Faz Tablosu (Q24)

| Faz | Kapsam | Kapı |
|---|---|---|
| 0 | İskelet: 22 dizin + 00-index + rules + context + validator extend | onay (Q32) |
| 1 | k0 pilotu: tam set + 01-04 derinleşme + envanter + kod/web kanıtı | **tam tarama** + katman onayı (Q29/Q30) |
| 2-6 | k1-k20 sırayla, katman başına onay | her fazda tam tarama |
| R | Kök boot link revizyonu (AGENTS §24.3, index, keys + çelişkiler) | high-risk onayı (Q7/Q32) |

## 6. SSOT Haritası

| Konu | SSOT | Not |
|---|---|---|
| Mimari kurallar | [[architecture/rules]] | tek kural kaynağı |
| Katman kataloğu | bu dosya | navigation |
| Hot memory / ID / envanter formatı | [[architecture/context]] | derived, ≤660 satır |
| Kararlar (ADR) | `.ai/.decisions/` | architecture altında ADR YOK |
| Scriptler | `.ai/scripts/` | architecture altında script YOK |
| Vizyon / proje | [[VISION]] · [[PROJECTS]] | dış referans |

## 7. İlişki Şeması

```text
.ai/CLAUDE.md (anayasa)
      ↓
architecture/rules.md  ←── SSOT (kurallar, isimlendirme, ID, parçalama, onay)
      ↓
00-enterprise-index.md ←── bu dosya (katalog, fazlar, sahiplik)
      ↓
context.md  (hot memory — instructions[] ile otomatik yüklenir, derived)
      ↓
kX-…/index.md → inventory/*-partNN.md   (lazy, P2/P3)
```
