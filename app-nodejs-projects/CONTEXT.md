---
title: "app-nodejs-projects — Surface Context"
type: context
category: surface
version: 1.0.0
status: active
authority: "SSOT: .ai/CLAUDE.md — Full rules read @.ai/AGENTS.md"
updated: 2026-10-23
---
# app-nodejs-projects — Surface Context

> **Görev:** Bu yüzey, CoreMusic'in Node.js/Bun yüzeyidir: nova-search-engine arama
> motoru (Bun + Fastify + TypeScript strict) ve onun paket ekosistemi (npm
> workspaces + turbo). Görev değişirse önce bu belge güncellenir; `__CURRENT__`
> alanı yer tutucuya bağlıysa disk kanıtı ile doldurulur (kanıtsız iddia
> `⚠️ VERIFICATION REQUIRED`).

## eli10 — Öğretici

Bu klasörde tek bir büyük uygulama var: **nova-search-engine**. O da tek başına
çalışmıyor; altındaki `shared/` ve `packages/` klasörlerindeki küçük paketler
(`@nova/*`) ona tip, config, logger ve güvenlik yardımcıları veriyor. Hepsi bir
**npm workspaces** monorepo'su içinde ve **turbo** ile tek komutla build
ediliyor. Kod tamamen **TypeScript strict + ESM**; testler Vitest/Playwright ile.

## eli15 — Derinlemesine

Geliştirici bu yüzeye girdiğinde ilk şu üç şeyi görür: (1) kök `package.json`
`"type": "module"` ve `workspaces: ["shared/*", "packages/*", "apps/*"]` ilan
ediyor — yani paketler arası `workspace:*` bağımlılıkları `node_modules`
junction'ları ile çözülüyor; (2) `turbo.json` `^2.4.4` ile task grafikleri
(cache ile `node_modules/.cache/turbo`) koordine ediliyor; (3) `pnpm-lock.yaml`
ve `apps/import-map-importer/pnpm-lock.yaml` ikinci bir lock dosyası — importer
kendi başına ayrı workspace. Prensipler: strict TS (`noUncheckedIndexedAccess`
dahil), ESM-only import, `docs/` dışına Markdown yazmama (`.ai/` vault hariç),
sürümleme için `semantic-release`, şemanın tek evi `shared/packages/*`.

## 1 — Mimari Şema

```text
src/
  core/       : index.ts — sunucu/boot çekirdeği (keşif raporu; ayrıntı ⚠️ VERIFICATION REQUIRED)
  routes/     : API route modülleri (Fastify 5)
  services/   : indeks/meili servis katmanı
  config/     : TS config katmanı (@nova/config kullanır)
shared/       : @nova/types · @nova/config · @nova/logger · @nova/security
packages/     : catalog-tools (GTS), src/assets, import-map-importer (ayrı workspace)
apps/         : web (Vite 7 + React 19 + Tailwind 4), desktop (Electron 39 + better-sqlite3)
test/         : 347 test (keşif raporu); vitest + vitest browser + playwright
docs/         : tek Markdown dizini (docs/PRD.md, docs/LICENSE, docs/CONTRIBUTING.md)
.ai/          : proje vault'u — docs/PRD.md.md, rag/, scripts/, hooks, mcp.json
```

Katman bağımlılığı: `apps/* → shared/* → packages/*` tek yön; ters bağımlılık
yasak. `packages/* → shared/*` de yasak (shared en alttaki katman).

## 2 — Doğrulama

| Kapı | Komut | Beklenen |
|---|---|---|
| Type | `pnpm typecheck` (veya `turbo typecheck`) | `tsc --noEmit` = 0 hata |
| Lint | `turbo run lint` | `eslint .` + `prettier --check .` = 0 |
| Test | `pnpm test` | vitest (unit + browser) + playwright + bun test = 0 fail |
| Build | `turbo run build` | webpack/vite build = 0 hata |

Not: import-map-importer'ın ayrı pnpm-lock'u vardır — o alt dizinde `pnpm install`
ayrı çalıştırılır.

## 3 — İlgili Dosyalar

| Dosya | Ne verir | Otorite |
|---|---|---|
| [[docs/PRD]] | Yüzeyin PRD'si (markdownlint CI korumalı) | Doğrudan |
| `package.json` / `turbo.json` | workspaces + task grafikleri | Doğrudan (disk) |
| `.ai/` (vault) | rag/, scripts/, hooks, mcp.json | Doğrudan |
| `.ai/log.md` | karar kayıtları (ör. "novasearch AYRI PROJE") | Doğrudan |
| `@.ai/RAG.md` | konu → vault dosyası indeksi | SSOT |
| `@PRD.md` | yüzey envanteri (aile indeksi) | SSOT |

## 4 — Kısıtlar / Yasaklar

- `git commit` bu yüzeyde ATILMAZ (orkestratöre ait).
- Yeni Markdown yalnız `docs/` + bu belgeler; **kod dosyasına `.md` eklemek yasak**
  (`.ai/log.md` kaydı — `.md` ekli kod satırı tespiti).

## 5 — Tarihçe / Kararlar

| Tarih | Karar | Kaynak |
|---|---|---|
| 2026-10-22 | `.ai/` vault temizliği: `.ai/done.md` 54 satır silindi, `mcp/` + `scripts/` eklendi, Markdown dosyaları yeniden düzenlendi | [[.ai/index]] |
| 2026-10-22 | `.ai/agent/*.md` tekilliği ihlali tespit (10 agent `.ai/.agents/` altında) — düzeltme açıksa doğrula | [[.ai/index]] |
| — | `novasearch` AYRI PROJE kuralı korunuyor | [[.ai/log]] |
| 2026-10-23 | Bu belge üretildi (prompt-maker çıktısı) | [[PRD]] |

## 6 — Bakım / Sorumluluk

- **Sahip:** Backend Developer · Kısmi: Fullstack Developer (apps/web-desktop)
- **Yenileme:** Komut/task grafiği veya paket değişince (önce `package.json`,
  `turbo.json` okunur) → bu belge revize edilir.
- **Kanıt:** [[docs/PRD]] + `.ai/` vault + keşif raporu (2026-10-23).

## 7 — İletişim

`admin@coremusic.net` · yayın yok (dahili yüzey).
