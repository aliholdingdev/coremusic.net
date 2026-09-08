---
title: "CoreMusic — .ai/.agents Bağlam"
type: context
folder: ".ai/.agents"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
---

# .ai/.agents — CLAUDE.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[./AGENTS.md]]

## 1. Bağlam

11 agent profilinin (AGENTS.md indeksi + 10 profil dosyası: master-orchestrator, backend-architect, ui-designer benzeri adlarla, data-engineer, security, qa, devops, embedded, audio-hardware, dsp-firmware, windows, plan) ikamet yeridir. `[[./plan.md]]` plan agent profilini içerir.

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Profil dosyası | 13 (index AGENTS.md + 12 profil) |
| Eksik profil | security-engineer envanterde görünüyor mu: master-orchestrator, backend-architect, data-engineer, devops-engineer, embedded-engineer, audio-hardware-engineer, dsp-firmware-engineer, qa-engineer, plan mevcut |

## 3. Komşu İlişkiler

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../CLAUDE.md]] | Vault kökü |
| Index | [[./AGENTS.md]] | Profil indeksi |
| Tüketen | [[../../AGENTS.md]] | Kök registry pointer'ı buraya işaret eder |

## 4. Değişiklik Protokolü

1. Yeni profil → AGENTS.md tablosuna kayıt + kök registry senkronu
2. Profil değişikliği → kullanıcı onayı → [[../log.md]] audit

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
