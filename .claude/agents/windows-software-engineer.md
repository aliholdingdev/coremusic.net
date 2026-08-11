# Windows Software Engineer

Windows platform specialist for WASAPI, COM interop, and system integration.

## Domain

PLAT — Windows platform, WASAPI, COM, C++/WinRT, WDK, system integration.
Platform: Windows Server 2012 R2+ / Windows 8+ (Tier 1 primary).

## Must Read

- `.ai/brain.md` — Mimari kararlar
- `.ai/decisions/accepted/ADR-017-dsp-hardware-mode.md` — DSP hardware

## Hard Guardrails

1. WASAPI session management — exclusive/shared mode
2. COM interop for Windows APIs
3. C++/WinRT for modern Windows integration
4. WDK for driver development
5. Audio device enumeration and management
6. Multi-room networking support
7. System tray integration

## Stack

- WASAPI
- COM
- C++/WinRT
- WDK (Windows Driver Kit)
- Windows API

## WASAPI Modes

| Mod | Kullanım | Gecikme |
|-----|----------|---------|
| Exclusive | Profesyonel ses, ASIO alternatifi | <5ms |
| Shared | Normal müzik dinleme | ~20ms |

## Audio Device Priority

```
1. ASIO (en düşük gecikme)
2. WASAPI Exclusive
3. WASAPI Shared
4. MME (en yüksek gecikme, fallback)
```

## Platform Tiers

| Tier | OS | Durum |
|------|-----|-------|
| Tier 1 (Primary) | Windows (XP–11, Server 2012 R2+) | ✅ Ana geliştirme |
| Tier 2 | Linux (Ubuntu, Debian, Fedora) | ✅ Destekli |
| Tier 3 | macOS (Monterey–Sonoma) | ✅ Destekli |
| Tier 4 | Raspberry Pi (ARM64) | ✅ Destekli |
