---
type: architecture
category: layer-subindex
title: "K000 · isletim-sistemi-cross — Platformlar Arası Ortak Zemin"
date: 2026-10-10
status: active
version: 1.0.0
authority: SSOT
---

# isletim-sistemi-cross — Platformlar Arası Ortak Zemin

> Üst indeks: `[[../index]]` · Makro: `[[../os-master]]` · Karar: `[[ADR-019-per-os-neva-player]]`

## §1 Kapsam
İşletim sistemi türleri arasında **ortak payda** olan zemin: tek `shared/` package,
OS-özel kodun soyutlandığı adapter sınırı, subdomain'lerin platformdan bağımsız yapısı.

## §2 İçerik

### 2.1 OS-özel kodun soyutlanması (ADR-019)
Ortak çekirdek + ince adapter deseni: `IAudioBackend` arayüzü, Windows (ASIO/WASAPI),
macOS (CoreAudio), Linux (ALSA/PipeWire) adapter'ları; fallback zinciri + kill-switch.
→ Ayrıntı: `[[../isletim-sistemi-turu/windows]]` · `[[../isletim-sistemi-turu/macos]]` · `[[../isletim-sistemi-turu/linux]]`.

### 2.2 Platformdan bağımsız zemin kanıtları (disk)
| Varlık | Kanıt |
|---|---|
| HTTP sunucu iki aile | `.htaccess` (Apache) + `web.config` (IIS) — 4 subdomain × 2 = 8 dosya |
| Tek paylaşımlı package | `shared/composer.json` → PSR-4 `CoreMusic\Shared\` (ADR-085 hybrid) |
| Cihaz/UA ayrıştırma | `brain.md:593-594` — UA → `embedded` / `4k-tv` sınıfı (server-side, OS-bağımsız) |
| Env okuma | `shared/src/Config/EnvParser.php` — OS-özel path bilgisi env'de (ADR-015) |

## §3 Bağımlılık
- **Alt:** `isletim-sistemi-core` · `isletim-sistemi-api`.
- **Üst (çağıran):** `isletim-sistemi-turu/*` (platform dosyaları buradaki ortak sınırı detaylandırır) · K002 (Sürücü).

## §4 Durum
**IMPLEMENTED (zemin) / PLANNED (player adapter)** — `shared/` + `.htaccess`/`web.config` disk kanıtlı;
IAudioBackend implementasyonu repo'da **YOK** (ADR-019 accepted, kod 0).

## §5 Risk / Not
- Windows sunucu (IIS) + Apache ikilisi PHP 8.4 davranış farkları (header rewrite) → ölçüm YOK → `⚠️ VERIFICATION REQUIRED`.
