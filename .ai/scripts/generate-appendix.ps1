$indexPath = "c:\www\coremusic.net\.ai\.decisions\index.md"
$outDir = "c:\www\coremusic.net\.ai\.decisions\accepted"

if (-not (Test-Path $outDir)) {
    New-Item -ItemType Directory -Path $outDir | Out-Null
}

$lines = Get-Content $indexPath

foreach ($line in $lines) {
    # Match lines like: | [[ADR-001-vanilla-js-itcss]] | Vanilla JS + ITCSS, Framework Yasak | Frontend |
    if ($line -match '\|\s*\[\[(ADR-0[0-3][0-9]-.*?)\]\]\s*\|\s*(.*?)\s*\|') {
        $adrId = $matches[1]
        $titleRaw = $matches[2]
        
        $appendixFileName = "$adrId-appendix.md"
        $filePath = Join-Path $outDir $appendixFileName
        
        $content = @"
---
title: "$adrId Derinlik Ekleri"
parent: "$adrId"
status: appendix
verified-against: "kod yolu listesi"
---

## 1. Kararın Kod Karşılığı (gerçek sınıf/dosya)

## 2. Veri Akışı (ASCII)

## 3. IMPLEMENTED/PLANNED Durumu

## 4. İzlenebilirlik Tablosu (iddia → kod → satır)

## 5. İstisnalar ve Bilinen Eksikler
"@
        
        Set-Content -Path $filePath -Value $content -Encoding UTF8
        Write-Host "Created $appendixFileName"
    }
}
