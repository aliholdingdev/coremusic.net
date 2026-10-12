---
title: "k0 İşletim Sistemi — Katman Anayasası Özeti"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "Derived: .ai/CLAUDE.md (anayasa SSOT) — bu dosya katman özeti, kural KOYMaz"
updated: 2026-10-07
tier: 3
domain: k0-isletim-sistemi
ssot: false
risk: medium
owner: "win-sw"
depends-on: [".ai/architecture/k0-isletim-sistemi/index.md", ".ai/CLAUDE.md"]
---

# k0 — Katman Anayasası Özeti (derived)

> Bu dosya **özet ve pointer'dır**; bağlayıcı kurallar [[CLAUDE]] (anayasa) ve [[architecture/rules]] içindedir.
> Çelişkide `.ai/CLAUDE.md` > `architecture/rules.md` > bu dosya kazanır (R8).

## K0'a Uygulanan Guardrails (özet)

1. **Zero-Hallucination:** bu katmanda `epoll_create1`, `CreateProcessW` gibi API iddiaları yalnız web/dokümantasyon kanıtıyla yazılır; kanıtsız = `⚠️ VERIFICATION REQUIRED`. Repo'da karşılığı yoksa `durum: PLANNED`, `kod-yolu: YOK`.
2. **16 maddelik repo incelemesi** kod yazımından önce (CLAUDE.md §5) — bu katmanda henüz K0 kodu yok, inceleme negatif sonuç = kanıt (Docker 0 / C++ 0 / pcntl 0).
3. **Yasak:** `.ai/architecture/k0-**` içinde ADR, script, security politika üretilmez (R1).
4. **FM 13 alan** her dosyada zorunlu (validator `fm-check`).
5. **Erişim:** salt-okunur referanslar (`_backup/`, `.ai.copy2/`) yazıma dahil edilmez (R1.3).

## Hızlı Doğrulama

```bash
node .ai/scripts/validate.mjs --check   # 8 check — bu dosya da kapsamda
```
