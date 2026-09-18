$files = Get-ChildItem -Path c:\www\coremusic.net\.ai\ecosystem\*.md | Where-Object { $_.Name -ne '7-service-integration.md' -and $_.Name -ne 'AGENTS.md' -and $_.Name -ne 'CLAUDE.md' }

$matrix = @"

---

## Faz 3 Doğrulaması: Servis Durum Matrisi

| Servis | Entegrasyon Durumu | Kanıt / Açıklama |
|--------|--------------------|------------------|
| Control Service | **IMPLEMENTED** | `shared/src/`, `auth.coremusic.net/` aktif |
| Media Service | **PLANNED** | Tasarım aşamasında |
| Audio Service | **PLANNED** | C++ NevaEngine taslak |
| Device Service | **PLANNED** | Donanım (I2S/BLE) beklemede |
| Network Audio | **PLANNED** | WebRTC mimarisi çizildi |
| AI Service | **PLANNED** | Python entegrasyonu planlandı |
| Download Service | **PLANNED** | Node.js servis klasörü yok |
"@

foreach ($f in $files) {
    Add-Content -Path $f.FullName -Value $matrix -Encoding UTF8
    Write-Host "Updated $($f.Name)"
}
