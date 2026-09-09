---
name: vault-sync-post
description: "Islem sonrasi otomatik vault guncelleme — session kaydi, root .md dosyalari, .claude/.opencode senkronizasyonu"
version: 1.0.0
author: CoreMusic Vault Steward
category: vault-management
tags: [vault, session, sync, automation, post-operation]
---

# Vault Sync Post — Islem Sonrasi Otomatik Guncelleme

## Amac

Her islem tamamlandiktan sonra tum vault dosyalarini otomatik olarak gunceller. Session kaydini yapar, root .md dosyalarini tazeler, .claude/ ve .opencode/ dizinlerini senkronize eder.

## Ne Zaman Kullanilir?

- Bir gorev tamamlandiktan sonra
- Kod degisikligi yapildiktan sonra
- Vault dosyasi guncellendikten sonra
- Session sonlandirilirken
- /vault-post-update komutu ile

## Adimlar

### Adim 1: Session Kaydi
```bash
node .ai/scripts/session-save.mjs --task "<gorev-aciklamasi>" --status completed --agent <agent-adi>
```

**Parametreler:**
- `--task`: Gorev aciklamasi (zorunlu)
- `--status`: completed | partial | failed (zorunlu)
- `--agent`: Agent adi (varsayilan: mo)
- `--files`: Virgulle ayirilmis dosya listesi (istege bagli)

**Ornek:**
```bash
node .ai/scripts/session-save.mjs --task "Footer responsive duzeltme" --status completed --agent ui --files "footer.php,_footer.css"
```

### Adim 2: Vault Guncelleme
```bash
node .ai/scripts/vault-post-update.mjs --scope root
```

**Parametreler:**
- `--scope root`: 12 root .ai dosyasi + .claude/.opencode sync (varsayilan)
- `--scope full`: Tum 244 AGENTS.md + 244 CLAUDE.md (buyuk vault yeniden yapilandirma icin)
- `--dry-run`: Sadece raporla, yazma

### Adim 3: Dogrulama
Sonuclari kontrol et:
- `.ai/sessions/YYYY-MM-DD-HH-MM-SS.md` olusturuldu mu?
- `.ai/MEMORY.md` §18 (Session History) guncellendi mi?
- `.ai/log.md`'ye append edildi mi?
- `.ai/sessions/context/project-state.md` guncellendi mi?
- `.claude/CLAUDE.md` = `.ai/CLAUDE.md` eslesiyor mu?
- `.opencode/CLAUDE.md` = `.ai/CLAUDE.md` eslesiyor mu?

## Dosya Yapisi

```
.ai/
├── scripts/
│   ├── session-save.mjs          ← Session kaydetme araci
│   ├── vault-post-update.mjs     ← Vault guncelleme araci
│   ├── vault-utf8-writer.mjs     ← UTF-8 guvenli yazma (zorunlu kanal)
│   └── vault-cmd.mjs             ← Turkce komut arayuzu
├── sessions/                     ← Session dosyalari
│   ├── YYYY-MM-DD-HH-MM-SS.md   ← Session loglari
│   └── context/
│       └── project-state.md      ← Son proje durumu
├── MEMORY.md                     ← Session history + current state
├── log.md                        ← Audit trail (append-only)
└── ... (12 root dosya)
```

## Kisitlar

- Tum yazimlar vault-utf8-writer.mjs uzerinden yapilmalidir
- PowerShell dosya YAZMA cmdlet'leri yasaktir
- log.md'ye SADECE append modu ile yazilir
- MEMORY.md 1000 satir sinirini asmamalidir

## İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[.ai/scripts/session-save]] | Session kaydetme scripti |
| [[.ai/scripts/vault-post-update]] | Vault guncelleme scripti |
| [[.ai/scripts/vault-utf8-writer]] | UTF-8 yazma araci |
| [[.ai/WORKFLOW]] | §8.9 Automated Session & Vault Sync |
| [[.ai/AGENTS]] | Vault-updater agent tanimi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-09
**Mode:** Red Team · Human Mode · Truth Mode
