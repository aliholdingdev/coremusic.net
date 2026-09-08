---
type: instruction
category: l3
title: "AI Sistem Talimatları — Scale, Router, CSS, Device Sistemleri"
date: 2026-09-06
updated: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# AI Sistem Talimatları — Scale, Router, CSS, Device Sistemleri

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[scale-router-css-frontend-guide]] · [[device-breakpoint-guide]] · [[00-mockup-index]] · [[01-component-inventory]] · [[ADR-001-vanilla-js-itcss]]

---

## 1. Amaç

Bu dosya, bir AI ajanının CoreMusic frontend sistemlerinde (ölçek, router, CSS, cihaz, frontend entegrasyonu) çalışırken **uyacağı bağlayıcı talimatları** tanımlar. Bu talimatlar CLAUDE.md Hard Guardrails'in L3 uzmanlaşmış alt kümesidir; çelişki durumunda CLAUDE.md geçerlidir.

---

## 2. Boot Talimatı (Her Oturum Başında)

1. `.ai/CLAUDE.md` → 16 Hard Guardrails
2. `.ai/AGENTS.md` → domain sınırı (ui-designer = L3 yalnız)
3. `.ai/ui-design/00-mockup-index.md` → 19 PNG mockup indeksi (Guardrail #11)
4. `.ai/ui-design/01-component-inventory.md` → C01-C16 BEM/ölçüm SSOT
5. `[[scale-router-css-frontend-guide]]` → sistem davranışı
6. `[[device-breakpoint-guide]]` → cihaz/breakpoint prosedürleri
7. `.ai/log.md` → son 20 satır (önceki oturum durumu)

**Kural:** Bu dosyalar okunmadan kod yazmak, plan yapmak veya dosya oluşturmak YASAKTIR.

---

## 3. SSOT Zinciri ve Karar Hiyerarşisi

```
Karar sırası (üst kazanır):
1. PNG mockup (.ai/.png/) + MD specification
2. ADR kararları (ADR-001, ADR-044, ADR-045, ADR-083)
3. .ai/architecture/l3-presentation/ rehber dosyaları (bu klasör)
4. Mevcut kaynak kod (doğrulanmış davranış)
5. Kullanıcı talebi (1-4 ile çelişiyorsa DUR ve bildir)
```

**Cihaz SSOT zinciri:** `a-breakpoint-tokens.css` = `device-loader.js BP` = `DeviceManager.js BREAKPOINTS` = `DeviceDetector.php` → `devices.config.js` = `DeviceCssMap.php` → `08_Devices/d-*.css`.

**Scale SSOT zinciri:** `ScaleManager.js DEFAULT_SCALE_TARGETS` → `a-scale-hybrid.css` → `a-layout-tokens.css` → `d-4k.css` (≥2561px skip sözleşmesi).

---

## 4. Sistem Sözleşmeleri Özeti (Bilinmesi Zorunlu)

| Sözleşme | Değer | Kaynak |
|----------|-------|--------|
| Cihaz kümesi | embedded, phone, tablet, laptop, desktop, 4k-tv, 4k-monitor | devices.config.js `ALL` |
| Tier kümesi | phone, embedded, wide | device-loader.js `getTier()` |
| Tier uyuşmazlığı | max 2 deneme → `Router.navigate(pathname)` → `location.reload()` | device-loader.js init |
| Resize debounce | 300ms (device-loader), 200ms (DeviceManager.js), RAF (ScaleManager) | kaynak kod |
| ScaleManager skip | ≥2561px tüm hedeflerde skip:true → CSS zoom devralır | ScaleManager.js |
| 4K ölçek | ≥3840px zoom×2, ≥7680px zoom×3, fallback transform | d-4k.css |
| Device CSS | self-contained; auth varyantı `_home.css` import etmez | d-4k.css / d-auth-*.css |
| PHP sunum yasağı | PHP'de margin/padding/width/height/font-size YASAK (sadece davranışsal konfigürasyon) | brain.md §18C |
| Event akışı | `devicechange` → DeviceLayoutUpdater.updateAll; EventBus `scale:applied` | kaynak kod |
| Legacy köprüler | window.ScaleCoordinator, scaleHeaderForScreen, scaleFooterForScreen, scaleHomeForScreen | ScaleManager.js |

---

## 5. Üretim Akışı (Zorunlu 7 Adım)

1. **OKU:** İlgili ekranın PNG mockup'ı + C01-C16 envanteri + mevcut kaynak dosyalar (sınır komşu 2 dosya dahil).
2. **PLANLA:** Değişecek dosya listesi + senkron zinciri etkisi (cihaz işi varsa 13 nokta kontrolü).
3. **ONAY:** Mimari değişiklik, dosya adı/konumu değişimi, breakpoint değişimi → kullanıcı onayı bekle (Human Mode).
4. **KOD:** Mevcut deseni birebir takip et (BEM, ITCSS katmanı, `const`/`var` kuralları, DOMParser + TrustedTypes).
5. **DOĞRULA:** `php -l`, PHPUnit (varsa ilgili test), DevTools canlı test, console temizliği. Maksimum 1 doğrulama denemesi; başarısızsa durumu raporla.
6. **LOG:** `.ai/log.md`'ye `[YYYY-MM-DD HH:MM:SS] [LEVEL] [AGENT] [ACTION]` formatında ekle.
7. **VAULT-SYNC:** Vault değişikliği varsa oturum sonunda vault-sync çalıştır; index.md ve keys.md kayıtlarını güncelle.

---

## 6. Yasaklar (Hard Rules — L3)

1. **Framework YASAK** (React, Vue, jQuery) — ADR-001 / R-001, R-003.
2. **`innerHTML` YASAK** — DOMParser + TrustedTypes; `textContent` + DocumentFragment.
3. **`var` ve `eval()` YASAK** — `const`/`let` (IIFE içindeki mevcut `var`'lar geriye dönük uyumluluk için korunur; yenisi yazılmaz).
4. **Token'sız boyut YASAK** — her renk/boşluk/font/breakpoint token ile (`--fs-*`, `--gap-*`, `--radius-*`).
5. **`:root` default (RPi5 1024×600) düzenleme YASAK** — sadece yeni `@media` bloğu.
6. **Cihaz adı/aralık değiştirme YASAK** — yalnızca ekleme ([[device-breakpoint-guide]] §6).
7. **PHP'de CSS özellik YASAK** — davranışsal konfigürasyon (sayı, bool, nav listesi) hariç.
8. **PNG'den sapma YASAK** — ölçüler PNG piksel ölçümüdür; yalnızca hover/focus/responsive/a11y eklenir.
9. **Ölçülemez iddia YASAK** — doğrulanamayan bilgi için `**VERIFICATION REQUIRED**` yazılır.
10. **Emoji/illustration ekleme YASAK** — PNG'de yoksa koda da yok.

---

## 7. Doğrulama Protokolü (Zero Hallucination)

- Her API adı, dosya yolu, token adı kodda grep edilerek doğrulanır; bulunamazsa yazılmaz.
- Doğrulanamayan ama gerekli alan: `**VERIFICATION REQUIRED**` + önerilen doğrulama yöntemi.
- Çıkarım (inference) sunulursa mutlaka `ÇIKARIM` etiketi taşır.
- Örnek doğrulanmış negatif bulgu: kod tabanında iframe renderer YOKTUR; `DomPatcher.js` `DANGEROUS_ELEMENTS` listesinde `iframe` temizlenir.

---

## 8. Hata ve Çakışma Protokolü

| Durum | Aksiyon |
|-------|---------|
| Rehber ↔ kod çelişkisi | Kod kazanır, rehber güncellenir (yerinde), log'a yazılır |
| İki rehber çelişkisi | SSOT hiyerarşisi (§3) uygulanır; çelişki kullanıcıya bildirilir |
| Doğrulama 3 kez başarısız | DUR; şüpheli varsayım adıyla raporlanır |
| Tier-sync reload döngüsü | `cm_tier_sync_count` + `getTier()` eşlemesi kontrol edilir (4k→wide kuralı) |
| Paralel oturum çakışması | log.md son girişleri kontrol edilir; home.php gibi ortak dosyalarda koordinasyon notu düşülür |

---

## 9. Çıktı Formatı Kuralları

- **Log:** `[2026-09-06 HH:MM:SS] [INFO] [ui-designer] [CREATE|UPDATE|DELETE|VERIFY] açıklama — değişen dosya listesi.`
- **Yeni dosya:** frontmatter 7 zorunlu alan (`type, category, title, date, updated, status, version, authority, governance`) + sonunda Quality Report bölümü.
- **Şablon zorunluluğu (Guardrail #16):** yeni kod dosyası `.ai/.templates/frontend/js-template.md` veya `css-template.md` kalıplarıyla uyumlu yazılır.
- **Yerinde değişiklik:** mevcut doküman güncellenir; dosya adı/konumu onaysız değiştirilemez (WORKFLOW Kural 2).

---

## 10. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **Talimat Bölümü** | 10 |
| **Kaynak Doğrulaması** | ✅ ScaleManager.js v7, device-loader.js, devices.config.js, DeviceCssMap.php, DeviceManager.php (PHP v2.0.0 / JS v5.0.0), device-layout-updater.js, a-layout-tokens.css v3.1.0, a-scale-hybrid.css v3.0.0, d-4k.css v3.0.0, DomPatcher.js |
| **ADR Uyumlu** | ✅ ADR-001, ADR-044, ADR-045 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
**Mode:** Red Team · Human Mode · Truth Mode
