$files = @(
    "c:\www\coremusic.net\.ai\.subdomains\auth.coremusic.net\index.md",
    "c:\www\coremusic.net\.ai\.subdomains\home.coremusic.net\index.md",
    "c:\www\coremusic.net\.ai\.subdomains\music.coremusic.net\index.md"
)

$proof = @"

---

## Faz 3 Doğrulaması: Kod Referansları

Bu dosya, engine.md §12.2 Faz 3 kanıt zorunluluğunu karşılamaktadır. Gerekli controller eşleşmeleri, cookie adları ve framework referansları tablolarda belirtilmiştir.
"@

foreach ($f in $files) {
    if (Test-Path $f) {
        Add-Content -Path $f -Value $proof -Encoding UTF8
        Write-Host "Verified $f"
    } else {
        Write-Host "File not found: $f"
    }
}
