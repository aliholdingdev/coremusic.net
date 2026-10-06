---
title: "vault: architecture.old — OS Mekanizmaları"
type: dokuman
created: 2026-10-06
updated: 2026-10-06
sources: [arch-old-os-mekanizmalari]
tags: [syscall, ipc, threading, bellek, izolasyon, container]
---

# OS Mekanizmaları (k003–k005 · k023–k029 · 10 klasör · 34 dosya)

`raw/architecture-old/` — çekirdek OS mekanizmaları, iki nesil.

| Alan | Eski nesil | Yeni nesil |
|---|---|---|
| Çağrı/Thread | k003 (4 dosya) | k025 (3) — gerçek zamanlı zamanlama |
| Süreç/IPC | k004 (5) | k024 (3) — performans karşılaştırması |
| Bellek/Container | k005 (4) | k026 (3) + k028 (3) — Docker orkestrasyon |
| İzolasyon | — | k027 (3) — sandbox ve güvenlik |
| Çapraz platform | — | k029 (3) — soyutlama katmanı |

## İlgili Sayfalar

- [[arch-k0-isletim-sistemi]] — yeni vault karşılığı (02-cekirdek-mekanizmalar)
- [[arch-old-platform-cekirdekler]] — platform katmanı
- [[arch-old-surucu-1nesil]] — sürücü yığını/buffer/latency
