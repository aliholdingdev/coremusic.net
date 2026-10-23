---
title: "app-nodejs-projects — Agent Operating Rules"
type: rules
category: agent-registry
version: 1.0.0
status: active
authority: "SSOT: .ai/AGENTS.md — Skill Registry @CLAUDE.md §Skill Registry"
updated: 2026-10-23
---
# app-nodejs-projects — Agent Operating Rules

> **Amaç:** Bu yüzeyde çalışan her agent bu kurallara uyar. Kanıt olmadan iddia
> yazılmaz; `⚠️ VERIFICATION REQUIRED` etiketi kullanılır. Kurallar incelikli
> değil, nettir.

## 1. Roller ve Sahiplik

| Rol | Ne yapar | Ne YAPMAZ |
|---|---|---|
| **Backend Developer** (ana) | Fastify servisleri, `services/`, `routes/`, `core/` | UI kodu |
| **Fullstack Developer** | `apps/web` (Vite+React), `apps/desktop` (Electron) | paylaşımlı paket sözleşmesini tek başına değiştirme |
| **Data Engineer** | `shared/types` + şema/DTO katmanı | framework kodu |
| **Security** | `@nova/security` (CSRF/XSS/validasyon), auth hatları | kripto kendiliğinden icat etme |

Tek yüzey → tek ana sahip: **Backend Developer**. Çapraz alan (desktop/DB) →
Fullstack Developer'a delege et.

## 2. Workflow (bu yüzeyde)

1. **Brief** — `.ai/` vault'u ve `docs/PRD.md` oku (gerekiyorsa `[[.ai/log]]`).
2. **Approval** — mimari/sözleşme değişikliği onay ister; küçük fix'te atlanır.
3. **Implement** — TypeScript strict + ESM; `shared/*` katmanına dokunacaksan önce
   uyumlu sürüm ve `apps/*` çağrılarını birlikte güncelle.
4. **Track** — `.ai/log.md`'ye satır ekle (append-only).
5. **Test** — `turbo typecheck && turbo run lint && pnpm test` (gate §3).
6. **Review** — `docs/` dışına Markdown yazma; kod dosyasına `.md` ekleme.
7. **Close** — vault güncelle; **commit atma** (orkestratöre ait).

## 3. Kurallar

- **Tip güvenliği:** `strict` + `noUncheckedIndexedAccess`; `any` yasak,
  `as` yalnız doğrulanmış daraltmada.
- **ESM:** `import`/`export`; `require` yasak (`.mjs` hariç belgelenmiş istisna yok).
- **Bağımlılık yönü:** `apps/* → shared/* → packages/*` tek yön; ters ok yasak.
- **Lock:** kökte `pnpm-lock.yaml` tek kaynaktır; `apps/import-map-importer`
  ayrı workspace'tir (kendi lock'u).
- **Şema tekilliği:** API/DTO tipleri yalnız `shared/types` içinde;
  `apps/*` içine kopya tip yazmak yasak.
- **Yasak yüzeyler:** `git commit` · `*.php` · `.ai/PRD.md` ve kök `PRD.md`
  aile indeksine tek başına dokunma (orkestratör akışı).
- **gRPC (nova protos):** `grpc.startup.ts` ++indexer'a yalnız `gateway-proto`
  üzerinden bağlanır — düzeltme önerisi **OpenAI servisini `generateText`
  çağrısına indirgemek değildir** (`.ai/log.md` safeguard notu).
- **Novasearch:** ayrı proje statüsü korunur (`.ai/log.md:241`).

## 4. Agent Listesi (bu yüzeyde çalışan)

| Agent | Kullanım tetikleyicisi |
|---|---|
| `backend-developer` | Fastify route/servis, hata yakalama |
| `fullstack-developer` | web/desktop uygulama katmanı |
| `typescript-pro` | strict tip, generic, modül düzeni |
| `data-engineer` | shared şema/DTO tasarımı |
| `security` | auth, validasyon, `@nova/security` |
| `testing` / `test-writer` | Vitest/Playwright kapsamı |

## 5. Skill Bağlantısı

- `php-backend-standards` bu yüzeyde **geçersiz** (PHP yok).
- Eşleşen skill yoksa kök `CLAUDE.md` §Skill Registry'e başvur (Skill Usage
  Mandate §2 — ilk işlem skill yüklemek).

## 6. Doğrulama (her işten önce)

```bash
turbo typecheck        # tsc --noEmit = 0 hata
turbo run lint         # eslint + prettier = 0
pnpm test              # vitest + playwright + bun test = 0 fail
```

Kapı geçmeden "bitti" demek yasak (kanıtsız "bitti" geçersizdir).

## 7. Escalation

3 başarız düzeltme → dur · şüpheli varsayımı açıkça yaz · üst karar iste
(`@.ai/WORKFLOW.md` §6). Görsel üretim/görsel revizyon gibi yıkıcı işlerde
**açık onay** alınır.
