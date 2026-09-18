$files = Get-ChildItem -Path c:\www\coremusic.net\.ai -Recurse -Filter *.md

foreach ($f in $files) {
    $content = Get-Content -Path $f.FullName -Raw
    $newContent = $content -replace 'architecture/06-audio/index', 'architecture/k0-k5-software/k3-audio-engine'
    $newContent = $newContent -replace 'architecture/07-security/index', 'architecture/k6-k7-security/k6-security'
    $newContent = $newContent -replace 'architecture/l0-infrastructure', 'architecture/k0-k5-software/k0-os-layer'
    $newContent = $newContent -replace 'architecture/07-security/driver-signing', 'architecture/k6-k7-security/k07-security-detail'
    $newContent = $newContent -replace 'architecture/08-auth', 'architecture/k6-k7-security/k06-auth-layer'
    $newContent = $newContent -replace 'architecture/05-data', 'architecture/k0-k5-software/k5-data-layer'
    $newContent = $newContent -replace 'architecture/00-overview/architecture-master', 'architecture/master-architecture-index'
    
    if ($content -cne $newContent) {
        Set-Content -Path $f.FullName -Value $newContent -Encoding UTF8
        Write-Host "Fixed links in $($f.Name)"
    }
}
