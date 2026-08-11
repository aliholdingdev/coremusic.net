---
type: plan
category: ui-design
title: "CoreMusic — Vault Registration (Kalıcı Kayıt, v4.0.0)"
date: 2026-08-11
updated: 2026-08-11
status: completed
version: 4.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
references:
  - [[00-mockup-index]]
  - [[01-component-inventory]]
  - [[00-ascii-art-views]]
  - [[CLAUDE.md]]
  - [[AGENTS.md]]
  - [[index.md]]
  - [[keys.md]]
---

# CoreMusic — Vault Registration (v4.0.0)

Mockup'ları `.ai/` vault'una kalıcı olarak tanıtma planı. **Bu adım yapılmazsa sonraki oturumlarda mockup'lar yine görünmez.**

---

## 1. Amaç

4 vault dosyasını düzenleyerek `.ai/ui-design/00-mockup-index.md` dosyasını boot protokolüne ve navigasyon sistemine dahil etmek.

---

## 2. Yapılan Değişiklikler

### 2.1 — `.ai/CLAUDE.md`

| Değişiklik | Durum | Satır |
|-----------|-------|-------|
| Hard Rule #11: Mockup Before Frontend | ✅ MEVCUT | 158 |
| Boot protokolüne 11. satır eklenecek | ⏳ YAPILACAK | — |

**Eklenecek satır:**
```
| 11 | .ai/ui-design/00-mockup-index.md | Mockup eşleme tablosu — frontend görevlerinde ZORUNLU |
```

### 2.2 — `.ai/AGENTS.md`

| Değişiklik | Durum | Satır |
|-----------|-------|-------|
| Mockup Before Frontend kuralı | ✅ MEVCUT | 356 |
| MSA istisnası (görsel referanslar) | ✅ MEVCUT | 354 |

### 2.3 — `.ai/index.md`

| Değişiklik | Durum | Satır |
|-----------|-------|-------|
| ui-design referansları | ✅ MEVCUT | 357 |

### 2.4 — `.ai/keys.md`

| Değişiklik | Durum | Satır |
|-----------|-------|-------|
| Frontend keyword mapping | ✅ MEVCUT | 75-79 |
| ASCII art keyword | ✅ MEVCUT | — |
| Screen spec keyword | ✅ MEVCUT | — |

---

## 3. Doğrulama Kontrolleri

| # | Kontrol | Yöntem | Durum |
|---|---------|--------|-------|
| 1 | Wiki-link çalışıyor mu? | `[[ui-design/00-mockup-index]]` | ✅ |
| 2 | Hard Rules 11 kural mı? | CLAUDE.md §7 | ✅ |
| 3 | MSA istisnası görünür mü? | AGENTS.md satır 354 | ✅ |
| 4 | Keyword mapping eklendi mi? | keys.md §3A | ✅ |
| 5 | ASCII Art reference eklendi mi? | keys.md "ascii art" | ✅ |
| 6 | Screen spec keyword'leri eklendi mi? | keys.md "screen spec" | ✅ |
| 7 | Mockup index erişilebilir mi? | index.md §12 | ✅ |

---

## 4. Beklenen Sonuç

Bu değişikliklerden sonra:

1. **Her frontend görevinde** agent otomatik olarak `00-mockup-index.md`'yi okuyacak
2. **Mockup'lar vault'a tanınmış** olacak
3. **ASCII Art Reference** erişilebilir olacak
4. **Keyword araması** ile mockup dizinine ulaşılabilecek
5. **Boot protokolü** mockup'ları hatırlayacak
6. **MSA limiti** görsel referansları kapsamayacak
7. **Screen spec dosyaları** keyword ile bulunabilecek
8. **Platform naming** sistemi belgelenmiş olacak (home-1024, studio-1920, vb.)

---

## 5. Platform Naming Dokümantasyonu

`.ai/.png/` dizin yapısının anlamı:

```
.ai/.png/{subdomain}-{resolution}/
                 ↑              ↑
                 │              └── Çözünürlük (1024=1024×600, 1920=1920×1080, 3840=3840×2160)
                 └── Subdomain (home, pro, studio, shared)
```

| Dizin | Anlam | Cihaz | OS |
|-------|-------|-------|-----|
| `home-1024/` | home.coremusic.net, 1024×600 | RPi5 7" dokunmatik | Linux Embedded |
| `shared-1024/` | Tüm subdomain'ler (auth), 1024×600 | RPi5 7" dokunmatik | Linux Embedded |
| `home-1920/` | home.coremusic.net, 1920×1080 | PC/Laptop | Windows/Linux |
| `home-3840/` | home.coremusic.net, 3840×2160 | 4K TV | Tizen/WebOS |
| `pro-1024/` | pro.coremusic.net, 1024×600 | RPi5 7" dokunmatik | Linux Embedded |
| `studio-1024/` | studio.coremusic.net, 1024×600 | RPi5 7" dokunmatik | Linux Embedded |

---

## 6. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[CLAUDE.md]] | Hard Rule #11 mevcut |
| [[AGENTS.md]] | Mockup kuralı + MSA istisnası mevcut |
| [[index.md]] | ui-design referansları mevcut |
| [[keys.md]] | Keyword mapping mevcut |
| [[00-mockup-index]] | v4.0.0 — 18 PNG ASCII art view |
| [[01-component-inventory]] | C01-C16 detayları |
| [[02-implementation-plan]] | CSS uygulama planı |
| [[03-accessibility-gaps]] | WCAG gap analizi |

---

## 7. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 4.0.0 |
| Status | COMPLETED |
| Files Verified | 5 (CLAUDE.md, AGENTS.md, index.md, keys.md, 00-mockup-index.md) |
| Hard Rules | 11 (Mockup Before Frontend dahil) |
| MSA Exception | ✅ Görsel referanslar hariç |
| Keyword Mappings | 18 (Frontend & UI Design) |
| ASCII Art Views | 18 PNG |
| Platform Naming | ✅ Documented |
| Risk | LOW (tümü mevcut) |

---

*Vault Registration v4.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
