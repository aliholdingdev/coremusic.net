---
title: "CoreMusic Scripts Index"
type: index
category: scripts-registry
version: 1.1.0
status: active
authority: SSOT
updated: 2026-10-23
---

# CoreMusic — Scripts Index

**Amaç:** `.ai/scripts/` klasöründeki yardımcı script'lerin kaydı. Vault yardımcı araçları, doküman değildir; bu indeks yalnızca dosya envanterini ve durumunu tutar.

**İlgili:** [[.workflows/vault-sync]] · [[index]] · [[log]]

---

## §1 Mevcut Script'ler (disk — 2026-10-23 ölçümü)

| # | Dosya | Tip | Amaç |
|---|-------|-----|------|
| 1 | `.ai/scripts/vault-utf8-writer.mjs` | Node.js | Vault dosyalarını UTF-8 olarak güvenli yazar (8 mod: replace/append/insert-before-marker/write/copy/verify/repair/scan — tek yazma kanalı) |
| 2 | `.ai/scripts/session-save.mjs` | Node.js | Oturum kaydı üretici (vault-sync Aşama 8 adım 1; şablon: session-log-template; yazımı writer write moduyla; 2026-10-23 üretildi) |
| 3 | `.ai/scripts/vault-post-update.mjs` | Node.js | Boot dosyaları frontmatter `updated:` tazeliği (Aşama 8 adım 2; `--scope root\|full` + `--dry-run`; log.md/SESSIONS/.templates/.decisions hariç; 2026-10-23) |
| 4 | `.ai/scripts/vault-cmd.mjs` | Node.js | Türkçe komut arayüzü — writer sarmalayıcısı (ekle/yaz/araya-ekle/kopyala/dogrula/onar/tara; kök dışı yazma engeli; 2026-10-23) |
| 5 | `.ai/scripts/fix-mojibake.py` | Python | Mojibake (bozuk karakter) taraması ve düzeltme |
| 6 | `.ai/scripts/validate.mjs` | Node.js | Vault doğrulama (validate akışı) |
| 7 | `.ai/scripts/figma-extract.ps1` | PowerShell | Figma API'den sayfa çekimi (token: `.ai/.env.figma`) |
| 8 | `.ai/scripts/figma-tokens.ps1` | PowerShell | Figma token üretimi (Faz 9 kapısı) |
| 9 | `.ai/scripts/kalip-abc-check.ps1` | PowerShell | Kalıp A/B/C şablon uyum denetimi (repo kökünden — §13.6) |
| 10 | `.ai/scripts/screens-frontmatter-check.ps1` | PowerShell | screens frontmatter denetimi (repo kökünden — §13.6) |
| 11 | `.ai/scripts/device-matrix-catid.ps1` | PowerShell | CatID cihaz matrisi denetimi |
| 12 | `.ai/scripts/wiki-link-check.ps1` | PowerShell | Wiki-link kırık taraması (repo kökünden — §13.6) |

**Toplam:** 12 script (4 .mjs + 1 .py + 7 .ps1) + bu indeks (`index.md`) = 13 dosya.

---

## §2 Eksik / Diskte OLMAYAN Öğeler

| Öğe | Durum | Referanslar |
|-----|-------|-------------|
| `vault-faz4-sweep.mjs` | ⚠️ YOK — eski §1 kaydıydı, 2026-10-23 disk ölçümünde bulunamadı (2026-09-24 ile 2026-10-23 arası düşmüş olabilir; silinme nedeni UNKNOWN) | bu indeks §1 (eski sürüm) |
| `project-state.md` | ⚠️ YOK ve **üretilmez** — kural: vault-sync-post SKILL.md §9 (2026-10-21); Aşama 8 doğrulaması `log.md` + `MEMORY.md` §20 üzerinden yürür | [[.workflows/vault-sync]] |

**Not:** Eski §2 kayıtları `session-save.mjs` · `vault-post-update.mjs` bu indeksin 2026-10-23 sürümünde §1'e taşınmıştır (üretildiler — PLAN Adım 12). `vault-cmd.mjs` yeni eklendi.

---

## §3 İlgili Ayrı Klasör (kaldırıldı)

Eski `architecture` script klasörü bu indeksin **kapsamı dışındaydı**; ilgili klasör kaldırıldığı için envanter kaydı da geçersizdir.

---

## §4 Kullanım Kuralları

1. Bu klasöre yeni script eklendiğinde §1 tablosuna satır eklenir (append).
2. Bir script silinirse/düşerse §2'ye ⚠️ YOK olarak taşınır — uydurma yol yazılmaz.
3. Vault ana senkronu `.workflows/vault-sync.md` akışıyla yürütülür; bu indeks akışın kendisi değildir, yalnızca envanterdir.
4. Session kapanış kaydı: `session-save.mjs` (2026-10-23'ten beri); elle senkron yedek yoldur (script çalışmazsa — SKILL.md H6).

---

## §5 Değişiklik Geçmişi

| Tarih | Sürüm | Değişiklik | Sorumlu |
|-------|-------|-----------|---------|
| 2026-09-24 | 1.0.0 | İlk oluşturma — 3 mevcut script + 3 eksik öğe kaydı (uydurma yok, disk okuması) | Vault Steward |
| 2026-10-12 | 1.0.2 | §3 kaldırılan `architecture` script klasörü envanteri yerine durum notuna çevrildi | refactorer |
| 2026-10-23 | 1.1.0 | §1 disk ölçümüyle hizalandı (3→12 script: session-save · vault-post-update · vault-cmd üretildi — PLAN Adım 12; 6 ps1/mjs + validate kaydedildi); `vault-faz4-sweep.mjs` §2'ye taşındı (diskte YOK, UNKNOWN silinme); §2-§4 güncellendi | MO (vault-updater) |

---

*Last Updated: 2026-10-23*
*Mode: Red Team · Human Mode · Truth Mode*
