# index.md — NovaSearch AI Vault İndeksi

> Bu vault projenin "2. beynidir". Oturum başında bu dosya okunur.
> Güncelleme: 2026-10-16 · v1.0.0

## Vault Yapısı

```
.ai/
├── index.md        # bu dosya
├── MEMORY.md       # oturum hafızası (öğrenilenler, kararlar)
├── WORKFLOW.md     # iş akışı + oturum sürekliliği kuralları
├── log.md          # audit trail (append-only)
├── knowledge-base/ # bilgi bankası
│   ├── anti-ban-strategy.md
│   ├── cache-patterns.md
│   ├── metadata-schema.md
│   └── search-algorithms.md
└── decisions/      # ADR kayıtları
    ├── ADR-001-youtube-api-v3-yasak.md
    ├── ADR-002-cache-first-offline.md
    ├── ADR-003-veritabani-sqlite-mysql.md
    ├── ADR-004-vanilla-js-web.md
    ├── ADR-005-monorepo.md
    ├── ADR-006-iki-cli.md
    └── ADR-007-ai-openrouter-free.md
```

Dokümantasyon: `docs/PRD.md` (NE) + `docs/MIMARI.md` (NASIL) + `README.md`.

## Karar Özeti (hızlı bakış)

| ADR | Karar |
|---|---|
| 001 | YouTube Data API v3 YASAK — yalnız youtubei.js |
| 002 | Cache-first + offline-first: RAM→Disk→DB→YouTube |
| 003 | DB: SQLite (dev) + MySQL (prod) — PostgreSQL rafa |
| 004 | Web: Vanilla JS + ITCSS/BEM (React/Vue yasak) |
| 005 | pnpm monorepo, tek yön katman bağımlılığı |
| 006 | 2 CLI: parametrik + menü sihirbazı (tek iş mantığı) |
| 007 | AI: OpenRouter free, fallback zinciri zorunlu |

## Geçiş Kuralları

- Plan onayı olmadan büyük değişiklik yok (WORKFLOW: Zero Code Before Plan)
- Çelişkide vault (bu dizin) kazanır; değişim ADR yazmayı gerektirir
- Anahtar/sırra `.md` içinde yer YOK — yalnız `.env`
