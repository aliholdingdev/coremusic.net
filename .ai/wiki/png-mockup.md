---
title: "vault: raw/.png — PNG Mockup Seti"
type: dokuman
created: 2026-10-06
updated: 2026-10-06
sources: [png-mockup]
tags: [mockup, png, ui, asset]
---

# PNG Mockup Seti (19 PNG + 4 bağlam notu)

`raw/.png/` — frontend mockup görselleri ve bağlam notları. Frontend'in görünmez otoritesi (Guardrail #11, [[vault-claude]]).

## Envanter

| Klasör | PNG | Bağlam notu | Kullanım |
|---|---|---|---|
| `home-1024/` | 12 | CLAUDE.md v1.0.1 | Ana sayfa mockup'ları (1024×600 RPi5) |
| `home-1920/` | 1 | CLAUDE.md v1.0.1 | Desktop ana sayfa (1920×1080) |
| `shared-1024/` | 6 | CLAUDE.md v1.0.1 | Auth ekranları (1024×600) |
| **Toplam** | **19** | 4 (kök+3) | Frontend geliştirme referansı |

## Özet

- Çelişki durumunda referans sıralaması kök notta tanımlı; UI Designer birincil doğrulama kaynağı
- `shared-1024` = auth.coremusic.net UI kodlamasının görsel SSOT'u
- Klasör notlarındaki `.png-analysis/` bağı diskte yok ⚠ VERIFICATION REQUIRED
- Dosyalar okunabilir kaynak (MD) + binary asset (PNG); 23 dosyanın tamamı `sources/özet.md`'ye kayıtlı
- Kaynak: `raw/.png/*` → [[png-mockup]]

## İlgili Sayfalar
- [[agent-ui-designer]] — birincil kullanıcı
- [[assets-coremusic-net]] — kodlanan statik varlıklar
- [[vault-claude]] — Guardrail #11 kaynağı
