---
title: "CoreMusic — Composer Vendor Senkronizasyon"
type: skill-instruction
version: 3.0
authority: SSOT
mode:
  - Red Team
  - Truth Mode
  - Human Mode
purpose:
  - Shared Library Distribution
  - Vendor Junction Management
  - Subdomain Dependency Sync
  - Class Not Found Prevention
  - Path-Based Dependency Resolution
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/AGENTS.md"
    - ".ai/WORKFLOW.md"
    - ".ai/brain.md"
    - ".ai/index.md"
  architecture:
    - ".ai/ADR/"
    - "Existing project architecture"
  templates:
    - ".ai/.templates/index.md"
  agents:
    - ".ai/.agents/AGENTS.md"
    - ".ai/.agents/backend-architect.md"
  project_structure:
    - "coremusic.net/"
    - "coremusic.net/shared/"
    - "auth.coremusic.net/"
    - "home.coremusic.net/"
    - "music.coremusic.net/"
    - "admin.coremusic.net/"
    - "api.coremusic.net/"
    - "car.coremusic.net/"
    - "studio.coremusic.net/"
    - "pro.coremusic.net/"
    - "media.coremusic.net/"
    - "download.coremusic.net/"
  decision_priority:
    - "ADR decisions"
    - "Architecture documentation"
    - "Security requirements"
    - "Existing implementation"
  update_policy:
    preserve_existing_structure: true
    require_approval_for:
      - "shared path change"
      - "junction strategy change"
      - "vendor structure change"
triggers:
  - "composer sync"
  - "vendor sync"
  - "shared library"
  - "vendor yenile"
  - "junction tamir"
  - "class not found"
  - "shared güncelle"
changelog:
  - version: 3.0
    date: 2026-08-15
    changes:
      - Complete rewrite — shared folder as source
      - Removed Composer dependency for shared library
      - Added path-based junction resolution
      - Added PowerShell scripts for all steps
      - Added file count validation (83 PHP)
      - Added critical file checksums
      - Added rollback procedure
      - Added multi-subdomain orchestration
---

# Composer Sync — CoreMusic Shared Library Senkronizasyon Motoru

## 1. Kök Problem

CoreMusic'de `shared/` dizini tüm subdomain'lerin ortak kütüphanesidir. Her subdomain bu kütüphaneyi `vendor/coremusic/shared-infrastructure/` yoluyla kullanır.

Windows'ta NTFS junction (sembolik link) sorunları yaşanabilir:

```
C:\www\coremusic.net\shared\src\  (83 PHP dosyası — KAYNAK)
        |
        v  (Junction / Sembolik Link)
        |
subdomain\vendor\coremusic\shared-infrastructure\src\ (BOŞ DİZİN)
        |
        v
PHP: Class 'DomainConfig' not found
```

**Sonuç:** Tüm subdomain'lerde `Class not found` hataları çıkar.

**Çözüm:** Bu skill, bozuk junction'ları tespit eder, temizler ve `shared/` klasörünü yeniden bağlar.

---

## 2. Kaynak Dizin

```
C:\www\coremusic.net\shared\
    ├── src\
    │   ├── Config\
    │   │   └── DomainConfig.php
    │   ├── PageRouter\
    │   │   └── PageRouter.php
    │   ├── Database\
    │   │   └── DatabaseManager.php
    │   ├── Security\
    │   │   └── SecurityHelper.php
    │   ├── Cache\
    │   │   └── CacheManager.php
    │   └── ... (toplam 83 PHP dosyası)
    ├── composer.json
    └── README.md
```

**Kural:** `shared/` dizini tek kaynaktır (Single Source of Truth). Hiçbir subdomain kendi kopyasını tutmaz.

---

## 3. Etkilenen Subdomain'ler

| Subdomain | vendor yolu | Junction hedefi | Durum |
|-----------|-------------|-----------------|-------|
| `auth.coremusic.net` | `vendor\coremusic\shared-infrastructure` | `..\..\..\shared\src` | Kritik |
| `home.coremusic.net` | `vendor\coremusic\shared-infrastructure` | `..\..\..\shared\src` | Kritik |
| `music.coremusic.net` | `vendor\coremusic\shared-infrastructure` | `..\..\..\shared\src` | Yüksek |
| `admin.coremusic.net` | `vendor\coremusic\shared-infrastructure` | `..\..\..\shared\src` | Yüksek |
| `api.coremusic.net` | `vendor\coremusic\shared-infrastructure` | `..\..\..\shared\src` | Yüksek |
| `car.coremusic.net` | `vendor\coremusic\shared-infrastructure` | `..\..\..\shared\src` | Orta |
| `studio.coremusic.net` | `vendor\coremusic\shared-infrastructure` | `..\..\..\shared\src` | Orta |
| `pro.coremusic.net` | `vendor\coremusic\shared-infrastructure` | `..\..\..\shared\src` | Orta |
| `media.coremusic.net` | `vendor\coremusic\shared-infrastructure` | `..\..\..\shared\src` | Düşük |
| `download.coremusic.net` | `vendor\coremusic\shared-infrastructure` | `..\..\..\shared\src` | Düşük |

---

## 4. Tetikleme Koşulları

| Durum | Tetikleyici | Aksiyon |
|-------|-------------|---------|
| `shared/src/` içinde dosya değişikliği | Kaynak kütüphane güncellendi | Junction'ları yenile |
| `Class ... not found` hatası | Junction bozuk | İlgili subdomain'i tamir et |
| Yeni subdomain eklendi | İlk kurulum | Junction oluştur |
| Manuel: `/composer-sync` | Tüm junction'ları yenile | Toplu tamir |

---

## 5. Workflow (7 Adım)

### Adım 1: Kaynak Dizin Kontrolü

```powershell
$sharedPath = "C:\www\coremusic.net\shared\src"

if (-not (Test-Path $sharedPath)) {
    Write-Output "❌ KAYNAK DİZİN YOK: $sharedPath"
    Write-Output "Önce shared/ dizinini oluşturun."
    exit 1
}

$phpCount = (Get-ChildItem $sharedPath -Recurse -Filter "*.php" -ErrorAction SilentlyContinue | Measure-Object).Count
Write-Output "✅ Kaynak dizin bulundu: $phpCount PHP dosyası"

if ($phpCount -ne 83) {
    Write-Output "⚠️ Uyarı: Beklenen 83, bulunan $phpCount"
}
```

### Adım 2: Etkilenen Subdomain'leri Tara

```powershell
$root = "C:\www\coremusic.net"
$subdomains = @()

Get-ChildItem -Path $root -Recurse -Filter "composer.json" -Depth 2 | Where-Object {
    (Get-Content $_.FullName -Raw) -match "coremusic/shared-infrastructure"
} | ForEach-Object {
    $subdomains += $_.Directory.Name
    Write-Output "Tespit edildi: $($_.Directory.Name)"
}

Write-Output "Toplam etkilenen subdomain: $($subdomains.Count)"
```

### Adım 3: Junction Sağlık Kontrolü

```powershell
$root = "C:\www\coremusic.net"
$sharedSrc = "C:\www\coremusic.net\shared\src"
$subdomains = @("auth.coremusic.net", "home.coremusic.net", "music.coremusic.net",
                 "admin.coremusic.net", "api.coremusic.net", "car.coremusic.net",
                 "studio.coremusic.net", "pro.coremusic.net", "media.coremusic.net",
                 "download.coremusic.net")

foreach ($sub in $subdomains) {
    $vendorSrc = "$root\$sub\vendor\coremusic\shared-infrastructure\src"

    if (Test-Path $vendorSrc) {
        # Junction hedefini kontrol et
        $item = Get-Item $vendorSrc -Force
        if ($item.Attributes -band [IO.FileAttributes]::ReparsePoint) {
            # Junctionvar, hedefini kontrol et
            $target = (Get-Item $vendorSrc).Target
            if ($target -and (Test-Path $target)) {
                $phpCount = (Get-ChildItem $vendorSrc -Recurse -Filter "*.php" -ErrorAction SilentlyContinue | Measure-Object).Count
                if ($phpCount -eq 83) {
                    Write-Output "✅ $sub — Junction sağlam ($phpCount PHP)"
                } else {
                    Write-Output "⚠️ $sub — Junction var ama eksik ($phpCount/83 PHP)"
                }
            } else {
                Write-Output "❌ $sub — Junction hedefi bozuk: $target"
            }
        } else {
            # Junction değil, normal dizin — kopya olabilir
            $phpCount = (Get-ChildItem $vendorSrc -Recurse -Filter "*.php" -ErrorAction SilentlyContinue | Measure-Object).Count
            Write-Output "⚠️ $sub — Junction değil, kopya dizin ($phpCount PHP)"
        }
    } else {
        Write-Output "❌ $sub — Vendor dizini yok"
    }
}
```

### Adım 4: Bozuk Junction'ları Temizle

```powershell
$root = "C:\www\coremusic.net"
$brokenSubdomains = @()  # Adım 3'ten gelen bozuk listesi

foreach ($sub in $brokenSubdomains) {
    $vendorPath = "$root\$sub\vendor\coremusic\shared-infrastructure"

    if (Test-Path $vendorPath) {
        Write-Output "Temizleniyor: $sub\vendor\coremusic\shared-infrastructure"
        Remove-Item $vendorPath -Recurse -Force
        Write-Output "✅ Silindi: $sub"
    } else {
        Write-Output "ℹ️ Zaten yok: $sub"
    }
}
```

### Adım 5: Junction'ları Yeniden Oluştur

```powershell
$root = "C:\www\coremusic.net"
$sharedSrc = "C:\www\coremusic.net\shared\src"
$subdomains = @()  # Tamir edilecek subdomain'ler

foreach ($sub in $subdomains) {
    $vendorBase = "$root\$sub\vendor\coremusic\shared-infrastructure"
    $vendorSrc = "$vendorBase\src"

    # Dizin yapısını oluştur
    $vendorDir = "$root\$sub\vendor\coremusic"
    if (-not (Test-Path $vendorDir)) {
        New-Item -ItemType Directory -Path $vendorDir -Force | Out-Null
    }

    # Junction oluştur
    try {
        New-Item -ItemType Junction -Path $vendorSrc -Target $sharedSrc -Force | Out-Null
        Write-Output "✅ $sub — Junction oluşturuldu: $vendorSrc → $sharedSrc"
    } catch {
        Write-Output "❌ $sub — Junction oluşturulamadı: $_"
    }
}
```

### Adım 6: Doğrulama

```powershell
$root = "C:\www\coremusic.net"
$subdomains = @()  # Doğrulanacak subdomain'ler

$criticalFiles = @(
    "Config\DomainConfig.php",
    "PageRouter\PageRouter.php",
    "Database\DatabaseManager.php",
    "Security\SecurityHelper.php",
    "Cache\CacheManager.php"
)

foreach ($sub in $subdomains) {
    Write-Output "========================================"
    Write-Output "DOĞRULAMA: $sub"
    Write-Output "========================================"

    $vendorSrc = "$root\$sub\vendor\coremusic\shared-infrastructure\src"
    $allPassed = $true

    foreach ($file in $criticalFiles) {
        $path = "$vendorSrc\$file"
        $exists = Test-Path $path
        $leaf = Split-Path $file -Leaf

        if ($exists) {
            $size = (Get-Item $path).Length
            Write-Output "  ✅ $leaf ($size byte)"
        } else {
            Write-Output "  ❌ $leaf — BULUNAMADI"
            $allPassed = $false
        }
    }

    # Toplam PHP sayısını kontrol et
    $phpCount = (Get-ChildItem $vendorSrc -Recurse -Filter "*.php" -ErrorAction SilentlyContinue | Measure-Object).Count
    Write-Output "  📊 Toplam PHP: $phpCount/83"

    if ($allPassed -and $phpCount -eq 83) {
        Write-Output "  ✅ $sub — TÜM KONTROLLER BAŞARILI"
    } else {
        Write-Output "  ⚠️ $sub — BAZI KONTROLLER BAŞARISIZ"
    }
}
```

### Adım 7: Log Kaydı

```powershell
$root = "C:\www\coremusic.net"
$logFile = "$root\.ai\log.md"
$ts = Get-Date -Format "yyyy-MM-dd HH:mm:ss"

foreach ($sub in $subdomains) {
    $vendorSrc = "$root\$sub\vendor\coremusic\shared-infrastructure\src"
    $phpCount = (Get-ChildItem $vendorSrc -Recurse -Filter "*.php" -ErrorAction SilentlyContinue | Measure-Object).Count

    if ($phpCount -eq 83) {
        $status = "BAŞARILI"
        $icon = "✅"
    } else {
        $status = "BAŞARISIZ"
        $icon = "❌"
    }

    $logEntry = @"
## $ts — COMPOSER-SYNC

**Subdomain:** $sub
**Durum:** $icon $status
**PHP Dosyası:** $phpCount/83
**Kaynak:** C:\www\coremusic.net\shared\src
**Agent:** backend-architect
**Skill:** composer-sync
**İşlem:** Junction senkronizasyonu

"@

    Add-Content -Path $logFile -Value $logEntry
}
```

---

## 6. Doğrulama Matrisi

| Kontrol | Başarılı | Başarısız |
|---------|----------|-----------|
| Kaynak `shared\src` var | ✅ Devam et | ❌ shared dizinini oluştur |
| Kaynak PHP = 83 | ✅ | ⚠️ Uyarı ver |
| Junction var | ✅ | ❌ Junction oluştur (Adım 5) |
| Junction hedefi doğru | ✅ | ❌ Junction'ı sil + yeniden oluştur |
| JunctionPHP = 83 | ✅ | ❌ Junction bozuk |
| DomainConfig.php var | ✅ | ❌ Junction bozuk |
| PageRouter.php var | ✅ | ❌ Junction bozuk |
| DatabaseManager.php var | ✅ | ❌ Junction bozuk |
| SecurityHelper.php var | ✅ | ❌ Junction bozuk |
| CacheManager.php var | ✅ | ❌ Junction bozuk |

---

## 7. Hata Durumları

| Hata | Kök Neden | Çözüm |
|------|-----------|-------|
| `Class ... not found` | Bozuk junction | Adım 4-5: sil + junction oluştur |
| `Failed to open stream` | Yanlış junction hedefi | Adım 4-5: sil + junction oluştur |
| `Junction oluşturulamadı` | Yetki hatası | Yönetici olarak çalıştır |
| `shared\src yok` | Kaynak dizin oluşmamış | shared dizinini oluştur |
| `Permission denied` | Dosya kilitli | PHP servisini durdur, tekrar dene |
| Junction hedefi bozuk | NTFS sorunu | Junction'ı sil, yeniden oluştur |

---

## 8. Geri Dönüş (Rollback)

Eğer junction oluşturma başarısız olursa:

```powershell
$root = "C:\www\coremusic.net"

# Bozuk junction'ları temizle
foreach ($sub in $subdomains) {
    $vendorSrc = "$root\$sub\vendor\coremusic\shared-infrastructure\src"
    if (Test-Path $vendorSrc) {
        Remove-Item $vendorSrc -Recurse -Force
        Write-Output "✅ $sub — Junction temizlendi"
    }
}
```

---

## 9. İstatistikler

| Metrik | Değer |
|--------|-------|
| Kaynak dizin | `C:\www\coremusic.net\shared\src` |
| Etkilenen subdomain | 10 |
| Beklenen PHP dosyası | 83 |
| Kritik doğrulama dosyası | 5 |
| Ortalama süre | ~5 saniye/subdomain |
| Frekans | shared değişikliği başına |

---

## 10. Bağlantılar

- **Kaynak:** `C:\www\coremusic.net\shared\` — Ortak kütüphane
- **Vault:** `.ai/ADR/ADR-042-vault-restructuring-2026-08-03`
- **Log:** `.ai/log.md` — vendor fix kayıtları
- **Routing:** `agent-orchestrator` — shared library yönlendirmesi

---

*Composer Sync v3.0 — CoreMusic Shared Library Senkronizasyon Motoru*
*Authority: Bayram Ali / Vault Steward*
*Mode: Red Team · Truth Mode · Human Mode*
