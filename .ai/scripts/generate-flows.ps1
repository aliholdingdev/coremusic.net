$baseDir = "c:\www\coremusic.net\.ai\ui-design\flow"
$categories = @{
    "music" = @("01-home.md", "02-albums.md", "03-album-detail.md", "04-browse.md", "05-playlist.md", "06-player.md", "07-singer.md")
    "auth" = @("01-select-gender.md", "02-login.md", "03-register-step1.md", "04-register-step2.md", "05-register-step3.md", "06-auth-success.md", "07-auth-error.md")
    "settings" = @("01-bluetooth-quick.md", "02-wifi-quick.md", "03-wifi-connected.md", "04-general-settings.md", "05-audio-settings.md", "06-theme-settings.md")
}

foreach ($cat in $categories.Keys) {
    $catDir = Join-Path $baseDir $cat
    if (-not (Test-Path $catDir)) {
        New-Item -ItemType Directory -Path $catDir | Out-Null
    }
    
    foreach ($file in $categories[$cat]) {
        $filePath = Join-Path $catDir $file
        $title = $file.Replace(".md", "").Replace("-", " ").ToUpper()
        
        $content = @"
---
type: ui-flow
title: "$title Flow"
category: "ui-design/flow/$cat"
date: 2026-09-19
updated: 2026-09-19
status: active
version: 1.0.0
---

# $title Flow

**Bağlantılar:** [[ui-design/01-component-inventory]] · [[ui-design/00-mockup-index]]

---

## 1. Ekran Amacı
Bu ekran $cat modülüne aittir ve kullanıcıya `$title` işlemini sunar.

## 2. Kullanılan Bileşenler (C01-C16)
- **C01 Navbar:** Üst menü bağlantısı.
- **C06 Primary Button:** Eylem butonu.

## 3. Mockup Referansı
Bu akış `.ai/.png/` altındaki ilgili PNG tasarımlarıyla eşleştirilmelidir.

"@

        Set-Content -Path $filePath -Value $content -Encoding UTF8
        Write-Host "Created $filePath"
    }
}
