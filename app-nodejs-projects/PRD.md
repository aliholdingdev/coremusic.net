---
title: "app-nodejs-projects — Product Requirements Document"
type: prd
category: surface
version: 1.0.0
status: active
authority: "SSOT: .ai/PRD.md — Yüzey PRD'si @docs/PRD.md (markdownlint CI)"
updated: 2026-10-23
---
# app-nodejs-projects — PRD

> **Yüzey:** Node.js/Bun — nova-search-engine arama motoru + paket ekosistemi.
> **Şablon:** 15 başlık (`.` · **B** · `=` · t- · t+ · T · § · N · P · # · A · R · S · E · ↑).
> Kanıt yoksa `⚠️ VERIFICATION REQUIRED`.

## 1. Amaç

Nova arama motorunu (dizin + arama API) ve onu besleyen paylaşımlı `@nova/*`
paketlerini tek bir npm-workspaces monorepo'su altında, strict TypeScript ile
üretmek; uygulama katmanlarını (web/desktop) aynı sözleşmeye bağlamak.

## 2. Kapsam

- ✅ `core/`, `routes/`, `services/`, `config/` (Fastify 5 + Bun)
- ✅ `shared/*` (`@nova/types|config|logger|security`) + `packages/*` (catalog-tools,
  import-map-importer, src/assets)
- ✅ `apps/web` (Vite+React), `apps/desktop` (Electron)
- ❌ `*.php` yüzeyleri · ❌ C++ (neva-engine) · ❌ `git commit`

## 3. Kullanıcı Hikâyeleri

- **US-1** — *Geliştirici:* "Tek komutla tüm paketleri typecheck/lint/test
  edebileyim" → `turbo` task grafikleri.
- **US-2** — *Geliştirici:* "API tiplerini tek yerden paylaşayım" → `@nova/types`.
- **US-3** — *Son kullanıcı:* "web/desktop aynı API'ye bağlansın" → ortak config/logger.
- **US-4** — *Operatör:* "Sürümü elle yayınlamayayım" → `semantic-release`.

## 4. Gereksinimler

| # | Gereksinim | Öncelik |
|---|---|---|
| G-1 | TypeScript strict + ESM (`"type": "module"`) | P0 |
| G-2 | npm workspaces (`shared/*`, `packages/*`, `apps/*`) + turbo 2.4 | P0 |
| G-3 | Fastify 5 API: dizin/arama servisleri | P0 |
| G-4 | `@nova/security` ile validasyon/XSS/CSRF katmanı | P0 |
| G-5 | Vitest + Playwright + bun test hattı | P1 |
| G-6 | `semantic-release` otomatik sürümleme | P2 |

## 5. Kabuk Şema (kısmi)

```text
package.json (type:module, workspaces) → turbo.json → pnpm-lock.yaml
shared/packages → packages/* → apps/* → docs/PRD.md, docs/CONTRIBUTING.md
```

## 6. Öncelik Sırası (P0 → P3)

`P0` mimari + tipler · `P1` test hattı · `P2` release otomasyonu · `P3` web/desktop cilası.

## 7. Kabul Kriterleri

1. `turbo typecheck` → **0 hata**.
2. `turbo run lint` → **0** (eslint + prettier).
3. `pnpm test` → **0 fail**.
4. `docs/` dışına yeni Markdown **0**.

## 8. Dış Bağımlılıklar

Bun · Node 20+ · pnpm · turbo 2.4 · Fastify 5 · Vite 7 · Electron 39 ·
better-sqlite3 · Playwright · semantic-release.

## 9. Riskler

- 🔴 Workspace junction + iki lock dosyası (import-map-importer ayrı) → install
  sırası karışabilir.
- 🟡 `shared/*` sözleşme kırılımı → tüm `apps/*` etkilenir.
- 🟡 `.ai/agent/*.md` tekilliği ihlali (2026-10-22 tespiti) → agent registry
  tutarsızlığı.

## 10. Başarı Ölçütleri

typecheck/lint/test 0 · paketler arası `workspace:*` tek kaynaktan çözülür ·
`semantic-release` sürüm etiketlerini otomatik üretir · Markdown ihlali 0.

## 11. Atılacak Adımlar (24–48h)

1. `turbo typecheck && turbo run lint && pnpm test` kanıtı al.
2. `.ai/agent/` tekillik ihlalini doğrula/ düzelt (vault sahası).
3. import-map-importer lock ayrımını docs/CONTRIBUTING'e işle.

## 12. Referanslar

- `[[docs/PRD]]` (markdownlint CI) · `package.json` · `turbo.json` ·
  `.ai/` vault · `[[.ai/log]]` (novasearch ayrı proje) · `[[CLAUDE]]` · `[[AGENTS]]`

## 13. Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0.0 | 2026-10-23 | İlk sürüm (keşif raporu + vault kanıtı) |

## 14. Ekipler / Sahiplik

Backend Developer (ana) · Fullstack Developer (apps) · Data Engineer (shared şema) ·
Security (`@nova/security`).

## 15. Onay

**Onaylamayan sorumlu:** Backend Developer · **Onay tarihi:** 2026-10-23 ·
**Karar mercii:** Ürün Sahibi (bu yüzeyde delegated).
