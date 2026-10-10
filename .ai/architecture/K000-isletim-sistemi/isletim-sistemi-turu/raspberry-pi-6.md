---
type: architecture
category: layer-topic
title: "K000 · İşletim Sistemi Türü — Raspberry Pi (Tier 4 / EMBEDDED)"
date: 2026-10-10
status: active
version: 1.0.0
authority: SSOT
---

# Raspberry Pi — Ses Yolu ve Zemin

> Kaynak: raspberrypi.com (Pi 5 tanıtımı) · 2026-05 Reddit AMA raporları (Upton — Pi 6 zamanlaması) — 2026-10-10 erişimli.
> Vault: `brain.md:361` (Tier 4 = Raspberry Pi ARM64/Debian) · `brain.md:551` (`EMBEDDED` = RPi5 + 7" touchscreen) · `[[../linux]]`

## §1 Çekirdek Gerçekler (web-doğrulanmış)

| Konu | Gerçek | Kaynak |
|---|---|---|
| Mevcut nesil | **Raspberry Pi 5** (Eylül 2023): 4× Cortex-A76 2.4 GHz, 8 GB LPDDR4X'e kadar, VideoCore VII, RP1 I/O | raspberrypi.com — Introducing Pi 5 |
| OS | **Raspberry Pi OS** (Debian tabanlı) — Pi 5'in tek birinci taraf destekli OS'u | raspberrypi.com |
| Ses yolu | Debian/ARM64 → **aynı Linux yığını: ALSA + PipeWire** (`[[linux]]` bu dosyanın yığınıdır) | Ubuntu/ArchWiki (linux.md kaynakları) |
| Pi 6 | **2028 başından önce beklenmiyor** (Eben Upton, Mayıs 2026 Reddit AMA); NPU olmayacak, artımlı yükseltme | circuitdigest/omgubuntu/raspberry.tips — 2026-05/06 |
| Ekran | CoreMusic hedefi: 7" touchscreen (`brain.md:551` EMBEDDED tanımı) | vault (disk kanıtı) |

## §2 CoreMusic Karşılığı
- **Tier 4** cihaz sınıfı (brain.md:361) + `EMBEDDED` cihaz tespiti (UA "Raspberry Pi" → `embedded`, brain.md:593).
- Durum: vault'ta destek işareti `? Destekli` (brain.md:361 — **soru işareti vault'ta mevcut**, kesin değil).
- Ses yolu seçimi (ALSA doğrudan vs PipeWire) → ADR-019 Linux adapter'ına bağlı; **RPi'ye özel ölçüm YOK**.

## §3 Durum
**PLANNED** — repo'da ARM64/Debian kurulum betiği veya cihaz kanıtı YOK; yalnız vault hedef tanımı.

## §4 Bilinmeyenler
- `raspberry-pi-6.md` adı "Pi 6" ima eder; **Pi 6 üretilmemiştir** (2028+) — bu dosya
  adı mevcut yapıdadır, değiştirilmedi; içerik **mevcut Pi 5 + Tier 4** içindir. ⚠️
  dosya adı ile içerik uyuşmazlığı rapor edildi (onaysız rename yasak).
- Pi 5 üzerinde ölçülmüş round-trip gecikme → veri YOK → UNKNOWN.
