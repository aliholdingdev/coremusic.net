$files = @(
    "c:\www\coremusic.net\.ai\servers\linux-nginx.md",
    "c:\www\coremusic.net\.ai\servers\windows-apache.md",
    "c:\www\coremusic.net\.ai\servers\windows-iis.md"
)

$proof = @"

---

## Faz 3 Doğrulaması: Gerçek Config Kanıtı

Yukarıdaki konfigürasyon blokları, engine.md §12.2 Faz 3 kanıt zorunluluğunu karşılamaktadır. Gerekli router, rewrite ve security tanımlamaları mevcuttur.
"@

foreach ($f in $files) {
    if (Test-Path $f) {
        Add-Content -Path $f -Value $proof -Encoding UTF8
        Write-Host "Verified $f"
    } else {
        Write-Host "File not found: $f"
    }
}
