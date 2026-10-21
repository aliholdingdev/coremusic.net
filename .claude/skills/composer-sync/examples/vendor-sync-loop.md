# Örnek: Tam Vendor-Sync Döngüsü (giriş → komut → çıktı → sonraki adım)

> Senaryo: `auth.coremusic.net` subdomain'inde
> `Class "CoreMusic\Session" not found` hatası alındı. Doğru sıralamayla
> tam teşhis + onarım döngüsü.

---

## ADIM 1 — Giriş (tetikleyici)

Kullanıcı:

> auth'da `Class "CoreMusic\Session" not found` hatası var, composer
> senkronla

**Skill eşleşmesi:** 'class not found' + 'composer' + 'bağımlılık senkron' →
bu SKILL.md §6 zinciri işletilir. **ÖNCE komut çalıştırmak yok** — §4.3 adımı.

---

## ADIM 2 — Komut: sınıf dosyası var mı? (§6.1 ADIM 1)

```powershell
Test-Path C:\www\coremusic.net\shared\src\Session.php
```

**Çıktı:**

```
True
```

Dosya VAR → ADIM 3.

---

## ADIM 3 — Komut: junction sağlığı (§3.2)

```powershell
$item = Get-Item C:\www\coremusic.net\auth.coremusic.net\vendor\coremusic\shared-infrastructure -Force
"{0} → {1}" -f $item.LinkType, ($item.Target -join ',')
```

**Çıktı:**

```
Junction → C:\www\coremusic.net\shared
```

Sağlıklı → ADIM 4.

---

## ADIM 4 — Komut: sınıf classmap'te mi? (§6.1 ADIM 4)

```powershell
Select-String -Path C:\www\coremusic.net\auth.coremusic.net\vendor\composer\autoload_classmap.php -Pattern 'Session'
```

**Çıktı:**

```
(empty)
```

classmap'te Session YOK → **kök neden: yeni sınıf dump alınmadan eklenmiş.**

---

## ADIM 5 — Karar (§4.1 matris)

| Durum | Komut |
|-------|-------|
| lock var, vendor var, dosya var, classmap eksik | `composer dump-autoload --optimize` |

`composer update` gerekmez — bağımlılık değişmedi, yalnız otoloader bayat.

```powershell
cd C:\www\coremusic.net\auth.coremusic.net
composer dump-autoload --optimize
```

**Çıktı (beklenen, doc-tabanlı):**

```
Generating optimized autoload files
> composer dump-autoload --optimize  NOT TESTED (php/composer PATH'te yok, 2026-10-21)
```

> Bu örnekte komut çalıştırılamadı → rapor formatı §6.5: `NOT TESTED`.
> Ortamda composer mevcutsa çıktı `Generating optimized autoload files`
> satırıdır (S2 — 03-cli.md).

---

## ADIM 6 — Doğrulama (§6.2 smoke testi)

```powershell
php -r "require 'C:/www/coremusic.net/auth.coremusic.net/vendor/autoload.php'; var_dump(class_exists('CoreMusic\Session'));"
```

**Beklenen:** `bool(true)`

`NOT TESTED` (php yok) → tamamlanan adım ADIM 4'tir; kalan adım için ortamda
composer kurulu olduğu doğrulanmalıdır. "Bitti" yazmak yok — kanıt zinciri açık.

---

## Sonuç tablosu

| Adım | Durum | Kanıt |
|------|-------|-------|
| Dosya mevcut | PASS | Test-Path → True |
| Junction | PASS | LinkType=Junction, Target=..\shared |
| classmap | FAIL (kök neden) | Session yok |
| Onarım | BLOCKED | composer PATH'te yok → NOT TESTED |
| Smoke test | BLOCKED | php PATH'te yok → NOT TESTED |

**Kök neden (kesin):** sınıf eklenmiş, dump alınmamış.
**Onarım komutu (kanıtlı):** `composer dump-autoload --optimize`.
**Sonraki adım (kullanıcı):** composer kurulu bir ortamda ADIM 5–6'yı çalıştırıp
`bool(true)` çıktısını raporlamak.

---

*CoreMusic Skill v3.0 — composer-sync examples — 2026-10-21*
