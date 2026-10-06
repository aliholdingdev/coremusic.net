---
title: "Vault Kaynağı: raw/architecture/k0-isletim-sistemi"
type: kaynak
raw_path: raw/architecture/k0-isletim-sistemi/
created: 2026-10-06
updated: 2026-10-06
sources: [arch-k0-isletim-sistemi]
tags: [isletim-sistemi, platform, cekirdek]
---

# Kaynak Özeti — k0-isletim-sistemi (14 dosya)

## Genel Özet

İşletim sistemi katmanının yeni nesil doküman seti: 5 platform (linux, macos, rpi5, windows-api, windows-core) + 4 çekirdek mekanizma (IPC, memory, syscall, threading) + 2 güvenlik-izolasyon (container, process) + 1 taşınabilirlik (cross-platform) + index + README.

## Ana Fikirler

1. `01-platformlar/` (5): linux-core, macos-core, rpi5-core, windows-api (ASIO/WASAPI/Win32), windows-core.
2. `02-cekirdek-mekanizmalar/` (4): ipc-mekanizmalari, memory-management, system-calls, threading-model.
3. `03-guvenlik-izolasyon/` (2): container-runtime, process-isolation.
4. `04-tasinabilirlik/` (1): cross-platform-api.
5. index.md + README.md klasör yönlendirmesi.

## Önemli Alıntılar/Veriler

- Okuma kapsamı: başlık + ilk 40 satır (toplu çıkarım 2026-10-06). Tam metin `raw/architecture/k0-isletim-sistemi/`.

## Bu Kaynaktan Oluşan/Güncellenen Wiki Sayfaları

[[arch-k0-isletim-sistemi]] (yeni)
