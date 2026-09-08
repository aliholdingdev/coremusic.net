---
title: "CoreMusic — Vault Registration (Kalıcı Kayıt, v4.0.0)"
type: plan
version: 4.1.0
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

### 2.5 — 2026-09-06 Senkronizasyon Kaydı (PNG Doğrulama + Kural Entegrasyonu)

| Dosya | Değişiklik | Durum |
|-------|-----------|-------|
| `.ai/ui-design/mockups/02-home-screens-1920.md` | v2.1.0 — gerçek PNG analiziyle düzeltildi (kart formatı, 5 istatistik, Istanbul, footer 70px, nav 8) | ✅ TAMAMLANDI |
| `.ai/ui-design/screens/B-home/dashboard-1920.md` | v1.0.0 YENİ — 19. PNG'nin ASCII art view spec'i (Top-Band Home) | ✅ TAMAMLANDI |
| `.ai/ui-design/screens/B-home/dashboard.md` | §11.2'ye düzeltme uyarısı + dashboard-1920 pointer | ✅ TAMAMLANDI |
| `.ai/ui-design/screens/00-ascii-art-index.md` | v3.1.0 — 18→19 PNG, dashboard-1920 ToC girişi | ✅ TAMAMLANDI |
| `.ai/ui-design/responsive-device-mode.md` | v3.2.0 — §7.4 4K No-Center (ortalamama YASAK) + §12 Geriye Dönük Uyumluluk (fallback matrisi) eklendi | ✅ TAMAMLANDI |
| `.ai/ui-design/02-implementation-plan.md` | v3.2.0 — Adım 2 responsive aksiyonu + referans tablosu güncellendi | ✅ TAMAMLANDI |
| `.ai/CLAUDE.md` | §7.1 tablosu: 19 PNG, referans sıralaması, §7.4/§12 bağlayıcı notlar | ✅ TAMAMLANDI |
| `.ai/ROLE.md`, `.ai/WORKFLOW.md`, `.ai/engine.md` | UI Design referansları 19 PNG + home-1920 + responsive kuralları | ✅ TAMAMLANDI |
| `.ai/AGENTS.md` | §7.2 Pre-flight: responsive uyum kontrolü eklendi | ✅ TAMAMLANDI |
| `C:\www\versacoder\opencode\.opencode\rules\frontend-png-rules.md` | 13 kural: 19 PNG cross-reference, referans sıralaması (Kural 3.1), 4K No-Center (Kural 7), backward-compat (Kural 11), DevTools doğrulama (Kural 12), dinamik keşif (Kural 13) | ✅ TAMAMLANDI |

---

## 3. Doğrulama Kontrolleri

| # | Kontrol | Yöntem | Durum |
|---|---------|--------|-------|
| 1 | Wiki-link çalışıyor mu? | `[[ui-design/00-mockup-index]]` | ✅ |
| 2 | Hard Rules 11 kural mı? | CLAUDE.md §7 | ✅ |
| 3 | Keyword mapping eklendi mi? | keys.md §3A | ✅ |
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
6. **Screen spec dosyaları** keyword ile bulunabilecek
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
| [[AGENTS.md]] | Mockup kuralı mevcut |
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
| Keyword Mappings | 18 (Frontend & UI Design) |
| ASCII Art Views | 18 PNG |
| Platform Naming | ✅ Documented |
| Risk | LOW (tümü mevcut) |

---

*Vault Registration v4.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
