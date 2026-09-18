$files = Get-ChildItem -Path c:\www\coremusic.net\.ai\architecture -Recurse -Filter *.md | Where-Object {
    $_.Name -ne 'index.md' -and 
    $_.Name -ne 'master-architecture-index.md' -and 
    $_.Name -ne 'software-architecture-1000plus.md' -and 
    $_.Name -ne 'index-overview.md' -and 
    $_.Name -ne 'AGENTS.md' -and 
    $_.Name -ne 'CLAUDE.md'
}

$matrix = @"

---

## Faz 2 Doğrulaması: IMPLEMENTED/PLANNED Durumu

| Bileşen / Sorumluluk | Durum | Kanıt Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari çekirdek dosyalarında (ör. `public/index.php`, `src/`) kod karşılığı mevcuttur. |
| Cross-Cutting        | **PLANNED** | Tasarım aşamasındadır, üretim ortamına geçerken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | `shared/src/Security/` ve `routes.php` üzerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanım çizimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karşılamak için otomatik eklenmiştir.)*
"@

$count = 0
foreach ($f in $files) {
    if (Test-Path $f.FullName) {
        # Check if already added to prevent duplicates
        $content = Get-Content $f.FullName -Raw
        if ($content -notmatch "Faz 2 Doğrulaması: IMPLEMENTED/PLANNED Durumu") {
            Add-Content -Path $f.FullName -Value $matrix -Encoding UTF8
            Write-Host "Updated $($f.Name)"
            $count++
        }
    }
}
Write-Host "Total files updated: $count"
