---
type: architecture
category: layer
title: "K002 — Sürücü"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K002 — Sürücü

## §1 Kimlik
- Katman: K002 · Alan: **A0** (K0-K5).
- Kapsam: donanım ↔ platform sürücü katmanı — Windows ses (WASAPI/COM/WinRT/WDK), XMOS host arayüzü, sürücü↔motor IPC.

## §2 Sorumluluk
1. Windows platform ses entegrasyonu; ASIO cihaz kaybında WASAPI fallback (`.ai/AGENTS.md` §17.6).
2. XMOS firmware ↔ host sınırının taşınması (donanım = K001).
3. Sürücü↔ses motoru IPC sözleşmesinin versiyonlanması (ADR-032).
4. Cihaz kaybı/hot-unplug hata yolu ve geri düşüş.

## §3 Bağlantılar (Dependency Rule)
- **Alt:** K001 (donanım) → K000 — plan §2-1.
- **Üst (çağıran):** K003 (ses motoru sürücüyü kullanır).

## §4 ADR Bağlantıları
| ADR | Başlık |
|---|---|
| ADR-032 | IPC Contract Versioning |
| ADR-038 | 8.1 Sound Card (PCM3168A + XMOS) |
| ADR-017 | DSP Hardware Mode (XMOS, JUCE, ASIO) — K003 ile ortak |

## §5 Durum
**PLANNED** — sürücü kodu (C++/C#) bu görevde doğrulanmadı → kanıt **UNKNOWN**; `.ai/AGENTS.md` §25.2 Windows yüzeyi PLANNED (spec mevcut).

## §6 Risk / Not
- Hedef yığın (WASAPI/COM/WinRT/WDK) yalnız agent registry §4'te; disk kanıtı yok.
- Sürücü kanıtı olmadan K003 gerçek cihazda doğrulanamaz (bütünleme riski).
