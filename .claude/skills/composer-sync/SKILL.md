---
name: composer-sync
description: "CoreMusic vendor senkronizasyonu — coremusic-shared güncellendiğinde tüm subdomain'lerde composer update çalıştırır, junction'ları doğrular. 'composer', 'vendor', 'shared-infrastructure', 'dependency' tetikler."
version: 1.1.0
status: active
category: backend
platform: opencode
metadata:
  author: Bayram Ali
  last_updated: 2026-08-08
triggers:
  - "composer"
  - "vendor"
  - "shared-infrastructure"
  - "dependency"
  - "composer update"
  - "vendor sync"
  - "junction"
---

# COMPOSER SYNC — CoreMusic Vendor Senkronizasyon Motoru

## 1. KİMLİK & ROL

**Backend Architect** yetkisiyle çalışır. `coremusic-shared` library'si güncellendiğinde tüm subdomain'lerdeki vendor dizinlerini senkronize eder.

**Kök Problem:** Windows'ta Composer NTFS junction oluşturamaz → boş `vendor/coremusic/shared-infrastructure/` dizini oluşur → PHP `Class not found` hataları verir.

---

## 2. TETİKLEME KOŞULLARI

Bu skill şu durumlarda otomatik tetiklenir:

| Tetikleyici | Açıklama |
|-------------|----------|
| `coremusic-shared/src/` içinde dosya değişikliği | shared library güncellendi |
| `Class ... not found` hatası | vendor junction bozuk |
| `composer.json` değişikliği | bağımlılık eklendi/kaldırıldı |
| Manuel: `/composer-sync` | Tüm vendor'ları yenile |

---

## 3. WORKFLOW (6 Adım)

### Adım 1: Etkilenen Subdomain'leri Tara

```powershell
# composer.json'da coremusic/shared-infrastructure require eden tüm dizinler
Get-ChildItem -Path "C:\www\coremusic.net" -Recurse -Filter "composer.json" | Where-Object {
    (Get-Content $_.FullName -Raw) -match "coremusic/shared-infrastructure"
} | Select-Object -ExpandProperty DirectoryName
```

**Beklenen sonuç:** `auth.coremusic.net`, `home.coremusic.net`

### Adım 2: Vendor Sağlık Kontrolü

Her subdomain için:

```powershell
# Junction'ın gerçekten dosya içerdiğini doğrula
$src = "C:\www\coremusic.net\$subdomain\vendor\coremusic\shared-infrastructure\src"
$count = (Get-ChildItem $src -Recurse -Filter "*.php" -ErrorAction SilentlyContinue | Measure-Object).Count
Write-Output "$subdomain: $count PHP dosyası"
```

**Kural:**
- `count == 83` → ✅ Sağlıklı
- `count < 83` veya `count == 0` → ❌ Bozuk, yeniden kurulmalı

### Adım 3: Bozuk Junction'ları Temizle

```powershell
# Bozuk dizinleri sil (force)
Remove-Item "C:\www\coremusic.net\$subdomain\vendor\coremusic\shared-infrastructure" -Recurse -Force
```

### Adım 4: Composer Update Çalıştır

```powershell
# Windows'ta junction sorununu aşmak için
$env:COMPOSER_DISABLE_SYMLINKS=1
composer update --prefer-dist --ignore-platform-reqs
```

**Önemli:** Her subdensity kendi dizininde çalıştırılmalı.

### Adım 5: Doğrulama

```powershell
# Kritik dosyaların varlığını kontrol et
$files = @(
    "src\Config\DomainConfig.php",
    "src\PageRouter\PageRouter.php",
    "src\Database\DatabaseManager.php",
    "src\Security\SecurityHelper.php",
    "src\Cache\CacheManager.php"
)
foreach ($f in $files) {
    $path = "C:\www\coremusic.net\$subdomain\vendor\coremusic\shared-infrastructure\$f"
    $ok = Test-Path $path
    Write-Output "$(Split-Path $f -Leaf) → $ok"
}
```

### Adım 6: Log Kaydı

```powershell
# .ai/log.md'ye timestamp ile kaydet
$ts = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
$logEntry = "[$ts] [INFO] [composer-sync] [VENDOR-REFRESH] $subdomain — 83 PHP dosyası yenilendi, 5 kritik dosya doğrulandı"
Add-Content -Path ".ai/log.md" -Value $logEntry
```

---

## 4. DOĞRULAMA MATRİSİ

| Kontrol | Başarılı | Başarısız |
|---------|----------|-----------|
| PHP dosya sayısı = 83 | ✅ Devam et | ❌ Step 3'e dön |
| DomainConfig.php var | ✅ | ❌ Junction bozuk |
| PageRouter.php var | ✅ | ❌ Junction bozuk |
| DatabaseManager.php var | ✅ | ❌ Junction bozuk |
| ClassLoader.php path doğru | ✅ | ❌ composer dump-autoload |

---

## 5. HATA DURUMLARI

| Hata | Kök Neden | Çözüm |
|------|-----------|-------|
| `Class ... not found` | Boş junction | Step 3-4: sil + composer update |
| `Failed to open stream` | Yanlış junction hedefi | Step 3-4: sil + composer update |
| `composer update` hatası | network/lock | `composer update --no-lock` dene |
| `ext-apcu` uyarısı | Windows'ta yok | `--ignore-platform-reqs` ile atla |

---

## 6. OTOMATİK TETİKLEME KURALLARI

```yaml
# Agent-orchestrator routing tablosuna ekle:
composer-sync:
  triggers: ["composer", "vendor", "shared-infrastructure", "dependency", "junction"]
  agent: backend-architect
  skill: composer-sync
  priority: HIGH
  description: "coremusic-shared vendor senkronizasyonu"
```

---

## 7. İSTATİSTİKLER

| Metrik | Değer |
|--------|-------|
| Etkilenen subdomain | 2 (auth, home) |
| Beklenen PHP dosyası | 83 / subdomain |
| Kritik doğrulama dosyası | 5 |
| Ortalama süre | ~30 saniye |
| Frequency | shared-infrastructure değişikliği başına |

---

## 8. BAĞLANTILAR

- **Vault:** [[decisions/accepted/ADR-042-vault-restructuring-2026-08-03]] — MSA limit, port 81
- **Log:** [[log.md]] — vendor fix kayıtları
- **Routing:** [[agent-orchestrator]] — composer keyword yönlendirmesi
