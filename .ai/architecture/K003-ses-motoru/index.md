---
type: architecture
category: layer
title: "K003 — Ses Motoru"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K003 — Ses Motoru

## §1 Kimlik
- Katman: K003 · Alan: **A0** (K0-K5 — DSP/çekirdek).
- Kapsam: Neva Engine — C++20 / JUCE 9 / ASIO gerçek-zamanlı ses motoru; per-OS Neva player.

## §2 Sorumluluk
1. Gerçek-zamanlı DSP zinciri: zero-allocation, lock-free, noexcept (`.ai/AGENTS.md` §16).
2. ASIO/WASAPI çıkış yolu ve donanım kipi (ADR-017).
3. İşletim-sistemi-bazlı oynatıcı varyantları — per-OS Neva Player (ADR-019).
4. Profesyonel EQ sistemi, 31-band (ADR-025); DSP pipeline mimarisi (ADR-062).
5. Uzak/deploy hattında timeout+fallback kuralına uymak (plan §5.6).

## §3 Bağlantılar (Dependency Rule)
- **Alt:** K002 (sürücü) → K001 → K000 — plan §2-1.
- **Üst (çağıran):** K015 (medya — oynatma kaynağı) · K011 (UX — oynatma komutu; yalnız sözleşme).

## §4 ADR Bağlantıları
| ADR | Başlık |
|---|---|
| ADR-017 | DSP Hardware Mode (XMOS, JUCE, ASIO) |
| ADR-019 | Per-OS Neva Player |
| ADR-025 | Professional EQ System (31-band) |
| ADR-062 | DSP Pipeline Architecture |
| ADR-037 | WirelessConnect Integration (entegrasyon) |

## §5 Durum
**PLANNED** — `.ai/projects/**` glob = 0 dosya (2026-10-09); ADR'ler şartname düzeyinde aktif.

## §6 Risk / Not
- ⚠️ VERIFICATION REQUIRED: Neva Engine kaynak kodu/şeması diskte doğrulanamadı (AGENTS §24.3 de "dizin var, 0 dosya" kaydını taşır).
- K002 sürücü kanıtı olmadığından donanım-üzerinde doğrulama planlanamadı.
