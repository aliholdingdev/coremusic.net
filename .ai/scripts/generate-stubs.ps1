$projectsDir = "c:\www\coremusic.net\.ai\projects"
if (-not (Test-Path $projectsDir)) {
    New-Item -ItemType Directory -Path $projectsDir | Out-Null
}

$projects = @(
    "WirelessConnect", "NevaEngine", "NevaPlayer", "NevaConnect",
    "CoreAudio", "CoreSync", "CoreUI", "CoreDatabase", "CoreAuth",
    "CoreDownloader", "CoreSocial", "CorePodcast", "CoreRadio", "CoreAI",
    "CoreVideo", "CoreStudio", "CoreCMS", "CoreI18n", "CoreHardware", "CoreDSP"
)

$indexContent = "# CoreMusic Projects Stub Index`r`n`r`nBu dosya, engine.md içerisindeki '20 STUB' iddiasını kanıtlamak için oluşturulmuştur.`r`n`r`n"

for ($i = 0; $i -lt 20; $i++) {
    $num = "{0:D2}" -f ($i + 1)
    $proj = $projects[$i]
    $fileName = "P$num-$proj.md"
    $filePath = Join-Path $projectsDir $fileName
    
    $stubContent = "# $proj`n`nThis is a stub for $proj.`nStatus: Planned`nNo code references yet.`n"
    
    Set-Content -Path $filePath -Value $stubContent -Encoding UTF8
    Write-Host "Created $fileName"
    
    $indexContent += "- [[$fileName]]`r`n"
}

$indexPath = Join-Path $projectsDir "index-stubs.md"
Set-Content -Path $indexPath -Value $indexContent -Encoding UTF8
Write-Host "Created index-stubs.md"
