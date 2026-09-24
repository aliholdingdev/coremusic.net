---
title: Kırık Wiki-Link Raporu (Kapsam Dışı — Önceden Var)
type: report
status: active
created: 2026-09-24
updated: 2026-09-24
author: coremusic-vault-docs
authority: report
---

# Kırık Wiki-Link Raporu (Kapsam Dışı — Önceden Var)

> **NE ZAMAN OKUNUR:** Vault link hijyeni, ADR arşivi kurtarma veya dead-mark temizliği yapılacak her görevde bu rapor okunur; her yeni tarama sonunda güncellenir. **Düzeltme yapmaz — salt rapordur.**

Tarama: 2026-09-24 · Yöntem: kod bloğu/inline-code strip + 6 adaylı çözüm (dosya-göreli, `.ai/` önekli, kök-göreli, ±`index.md`) · Kapsam: bu raporun listelenen 5 dosyadaki tüm `[[...]]` linkleri (ui-design düzeltmeleri hariç — onlar 2026-09-24 kapatıldı).

**Toplam: 34 kırık link / 5 dosya** (`.claude/CLAUDE.md` 27 · `assets.coremusic.net/AGENTS.md` 3 · `home.coremusic.net/CLAUDE.md` 2 · `assets.coremusic.net/CLAUDE.md` 1 · `assets.coremusic.net/Css copy/CLAUDE.md` 1)

## Özet — Ölü hedef kategorileri

| Kategori | Adet | Ölü hedef durumu |
|---|---|---|
| ADR arşivi (`decisions/`, `ADR-*`) | 20 | `.ai/decisions/` + kök `decisions/` diskte YOK (tüm repo recursive arama boş — faz6-D "10 link donmuş" ile tutarlı) |
| Mimari dosyalar (`architecture/`) | 9 | `architecture-master`, `l0-infrastructure`, `database_master`, `06-audio/index`, `master-implementation-plan`, `l3-presentation/*` diskte YOK |
| Kök/alan `AGENTS.md` | 3 | Kök `AGENTS.md` YOK (`.ai/AGENTS.md` var) |
| Subdomain indeksi | 1 | `.ai/.subdomains/home.coremusic.net/index.md` YOK |
| Mockup hedefi (`B-home/dashboard-1920`) | 1 | B-home dizini YOK (T01-T31 + shared var); link metni zorunlu referans olarak korundu |
| Konvansiyon sahte-pozitifi | 0 | Hedef VAR — kök-göreli konvansiyon, düzeltme gerekmez |

## Tam liste

### `.claude/CLAUDE.md` (27)

| Satır | Ölü hedef | Önerilen aksiyon |
|---|---|---|
| 105 | `[[architecture/00-overview/architecture-master]]` | architecture-master dosyasi hic olusmamis. Vault mimari indeksine (varsa) repoint veya dead-mark; sahip onayi gerekir. |
| 267 | `[[screens/B-home/dashboard-1920]]` | Hedef mockup YOK (screens/ altinda T01-T31 + shared var, B-home dizini yok). Link metni bilincli korundu (zorunlu referans); sahip onayiyla dead-mark veya hedef uretilmeli. |
| 438 | `[[architecture/00-overview/architecture-master]]` | architecture-master dosyasi hic olusmamis. Vault mimari indeksine (varsa) repoint veya dead-mark; sahip onayi gerekir. |
| 521 | `[[decisions/accepted/ADR-001-vanilla-js-itcss]]` | ADR arsivi diskte YOK (tum repo recursive arama bos). ADR sluglari [[CLAUDE.md]] §12 ADR tablosuna repoint edilmeli veya `<!-- dead-link: <slug> no source 2026-09-24 -->` dead-mark (faz6-D deseni) koyulmali. |
| 522 | `[[decisions/accepted/ADR-002-pdo-mandatory-no-orm]]` | ADR arsivi diskte YOK (tum repo recursive arama bos). ADR sluglari [[CLAUDE.md]] §12 ADR tablosuna repoint edilmeli veya `<!-- dead-link: <slug> no source 2026-09-24 -->` dead-mark (faz6-D deseni) koyulmali. |
| 523 | `[[decisions/accepted/ADR-010-csrf-protection-strategy]]` | ADR arsivi diskte YOK (tum repo recursive arama bos). ADR sluglari [[CLAUDE.md]] §12 ADR tablosuna repoint edilmeli veya `<!-- dead-link: <slug> no source 2026-09-24 -->` dead-mark (faz6-D deseni) koyulmali. |
| 524 | `[[decisions/accepted/ADR-011-session-management]]` | ADR arsivi diskte YOK (tum repo recursive arama bos). ADR sluglari [[CLAUDE.md]] §12 ADR tablosuna repoint edilmeli veya `<!-- dead-link: <slug> no source 2026-09-24 -->` dead-mark (faz6-D deseni) koyulmali. |
| 525 | `[[decisions/accepted/ADR-022-database-hardened-security]]` | ADR arsivi diskte YOK (tum repo recursive arama bos). ADR sluglari [[CLAUDE.md]] §12 ADR tablosuna repoint edilmeli veya `<!-- dead-link: <slug> no source 2026-09-24 -->` dead-mark (faz6-D deseni) koyulmali. |
| 526 | `[[decisions/accepted/ADR-038-8.1-sound-card-chip-selection]]` | ADR arsivi diskte YOK (tum repo recursive arama bos). ADR sluglari [[CLAUDE.md]] §12 ADR tablosuna repoint edilmeli veya `<!-- dead-link: <slug> no source 2026-09-24 -->` dead-mark (faz6-D deseni) koyulmali. |
| 527 | `[[decisions/accepted/ADR-040-database-authority]]` | ADR arsivi diskte YOK (tum repo recursive arama bos). ADR sluglari [[CLAUDE.md]] §12 ADR tablosuna repoint edilmeli veya `<!-- dead-link: <slug> no source 2026-09-24 -->` dead-mark (faz6-D deseni) koyulmali. |
| 528 | `[[decisions/accepted/ADR-042-vault-restructuring-2026-08-03]]` | ADR arsivi diskte YOK (tum repo recursive arama bos). ADR sluglari [[CLAUDE.md]] §12 ADR tablosuna repoint edilmeli veya `<!-- dead-link: <slug> no source 2026-09-24 -->` dead-mark (faz6-D deseni) koyulmali. |
| 529 | `[[decisions/accepted/ADR-044-dynamic-user-theme-engine]]` | ADR arsivi diskte YOK (tum repo recursive arama bos). ADR sluglari [[CLAUDE.md]] §12 ADR tablosuna repoint edilmeli veya `<!-- dead-link: <slug> no source 2026-09-24 -->` dead-mark (faz6-D deseni) koyulmali. |
| 530 | `[[.decisions/draft/ADR-089-classab-24v]]` | ADR arsivi diskte YOK (tum repo recursive arama bos). ADR sluglari [[CLAUDE.md]] §12 ADR tablosuna repoint edilmeli veya `<!-- dead-link: <slug> no source 2026-09-24 -->` dead-mark (faz6-D deseni) koyulmali. |
| 556 | `[[ADR-017-dsp-hardware-mode]]` | ADR arsivi diskte YOK (tum repo recursive arama bos). ADR sluglari [[CLAUDE.md]] §12 ADR tablosuna repoint edilmeli veya `<!-- dead-link: <slug> no source 2026-09-24 -->` dead-mark (faz6-D deseni) koyulmali. |
| 557 | `[[ADR-010-csrf-protection-strategy]]` | ADR arsivi diskte YOK (tum repo recursive arama bos). ADR sluglari [[CLAUDE.md]] §12 ADR tablosuna repoint edilmeli veya `<!-- dead-link: <slug> no source 2026-09-24 -->` dead-mark (faz6-D deseni) koyulmali. |
| 558 | `[[ADR-040-database-authority]]` | ADR arsivi diskte YOK (tum repo recursive arama bos). ADR sluglari [[CLAUDE.md]] §12 ADR tablosuna repoint edilmeli veya `<!-- dead-link: <slug> no source 2026-09-24 -->` dead-mark (faz6-D deseni) koyulmali. |
| 559 | `[[ADR-011-session-management]]` | ADR arsivi diskte YOK (tum repo recursive arama bos). ADR sluglari [[CLAUDE.md]] §12 ADR tablosuna repoint edilmeli veya `<!-- dead-link: <slug> no source 2026-09-24 -->` dead-mark (faz6-D deseni) koyulmali. |
| 561 | `[[ADR-038-8.1-sound-card-chip-selection]]` | ADR arsivi diskte YOK (tum repo recursive arama bos). ADR sluglari [[CLAUDE.md]] §12 ADR tablosuna repoint edilmeli veya `<!-- dead-link: <slug> no source 2026-09-24 -->` dead-mark (faz6-D deseni) koyulmali. |
| 662 | `[[architecture/l0-infrastructure]]` | Hedef diskte yok — sahip onayiyla repoint veya dead-mark. |
| 663 | `[[ADR-010-csrf-protection-strategy]]` | ADR arsivi diskte YOK (tum repo recursive arama bos). ADR sluglari [[CLAUDE.md]] §12 ADR tablosuna repoint edilmeli veya `<!-- dead-link: <slug> no source 2026-09-24 -->` dead-mark (faz6-D deseni) koyulmali. |
| 664 | `[[decisions/accepted/ADR-043-auth-subdomain-consolidation]]` | ADR arsivi diskte YOK (tum repo recursive arama bos). ADR sluglari [[CLAUDE.md]] §12 ADR tablosuna repoint edilmeli veya `<!-- dead-link: <slug> no source 2026-09-24 -->` dead-mark (faz6-D deseni) koyulmali. |
| 666 | `[[ADR-044-dynamic-user-theme-engine]]` | ADR arsivi diskte YOK (tum repo recursive arama bos). ADR sluglari [[CLAUDE.md]] §12 ADR tablosuna repoint edilmeli veya `<!-- dead-link: <slug> no source 2026-09-24 -->` dead-mark (faz6-D deseni) koyulmali. |
| 667 | `[[architecture/05-data/database_master]]` | Hedef diskte yok — sahip onayiyla repoint veya dead-mark. |
| 668 | `[[architecture/06-audio/index]]` | Hedef diskte yok — sahip onayiyla repoint veya dead-mark. |
| 669 | `[[decisions/accepted/ADR-042-vault-restructuring-2026-08-03]]` | ADR arsivi diskte YOK (tum repo recursive arama bos). ADR sluglari [[CLAUDE.md]] §12 ADR tablosuna repoint edilmeli veya `<!-- dead-link: <slug> no source 2026-09-24 -->` dead-mark (faz6-D deseni) koyulmali. |
| 670 | `[[architecture/03-contracts/master-implementation-plan]]` | Hedef diskte yok — sahip onayiyla repoint veya dead-mark. |
| 671 | `[[decisions/accepted/ADR-087-master-implementation-plan]]` | ADR arsivi diskte YOK (tum repo recursive arama bos). ADR sluglari [[CLAUDE.md]] §12 ADR tablosuna repoint edilmeli veya `<!-- dead-link: <slug> no source 2026-09-24 -->` dead-mark (faz6-D deseni) koyulmali. |

### `assets.coremusic.net/AGENTS.md` (3)

| Satır | Ölü hedef | Önerilen aksiyon |
|---|---|---|
| 15 | `[[../AGENTS.md]]` | Kök AGENTS.md yok. `.ai/AGENTS.md` (vault SSOT) veya alt-domain AGENTS.md gerekli mi sahip onayi; degilse linki kaldir. |
| 77 | `[[../.ai/architecture/l3-presentation/itcss-architecture.md]]` | l3-presentation mimari dosyalari diskte YOK (dizin dahil). Mimari dosyalar baska yere tasinmissa repoint, yoksa dead-mark. |
| 78 | `[[../.ai/architecture/l3-presentation/js-module-architecture.md]]` | l3-presentation mimari dosyalari diskte YOK (dizin dahil). Mimari dosyalar baska yere tasinmissa repoint, yoksa dead-mark. |

### `home.coremusic.net/CLAUDE.md` (2)

| Satır | Ölü hedef | Önerilen aksiyon |
|---|---|---|
| 123 | `[[../AGENTS.md]]` | Kök AGENTS.md yok. `.ai/AGENTS.md` (vault SSOT) veya alt-domain AGENTS.md gerekli mi sahip onayi; degilse linki kaldir. |
| 127 | `[[../.ai/.subdomains/home.coremusic.net/index.md]]` | subdomain indeksi diskte YOK. `.ai/.subdomains/` olusturulmali veya link kaldirilmali — sahip onayi gerekir. |

### `assets.coremusic.net/CLAUDE.md` (1)

| Satır | Ölü hedef | Önerilen aksiyon |
|---|---|---|
| 198 | `[[../AGENTS.md]]` | Kök AGENTS.md yok. `.ai/AGENTS.md` (vault SSOT) veya alt-domain AGENTS.md gerekli mi sahip onayi; degilse linki kaldir. |

### `assets.coremusic.net/Css copy/CLAUDE.md` (1)

| Satır | Ölü hedef | Önerilen aksiyon |
|---|---|---|
| 24 | `[[../../.ai/architecture/l3-presentation/CLAUDE.md]]` | l3-presentation mimari dosyalari diskte YOK (dizin dahil). Mimari dosyalar baska yere tasinmissa repoint, yoksa dead-mark. |

## Kurallar

- **Bu rapor salt-okunurdur:** düzeltme için sahip onayı + ayrı görev gerekir (In-Place Refactoring).
- Frozen ADR (001-037) linkleri: slug metni KORUNUR, yalnızca repoint/`dead-mark` uygulanır (faz6-D deseni).
- Yeni kırık link çıkarsa bu dosya yeniden üretilir (`collect-broken-links.mjs` + bu şablon); eski kayıt silinmez, `updated` tarihi güncellenir.
- Figma token / secret bu rapora YAZILMAZ (REDACTED politikası).
