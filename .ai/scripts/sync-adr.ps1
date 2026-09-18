<#
.SYNOPSIS
    CoreMusic ADR Sync Script
.DESCRIPTION
    Bu script, .ai/decisions/accepted altındaki tüm ADR dosyalarını okur, başlıklarını ve tarihlerini çıkartır,
    ve architecture-master.md gibi index dosyalarındaki eksiklikleri tespit etmek veya genel bir rapor
    üretmek için kullanılır.
.EXAMPLE
    .\sync-adr.ps1
#>

$ErrorActionPreference = "Stop"

$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Definition
$AdrDir = Resolve-Path (Join-Path $ScriptDir "..\decisions\accepted")
$MasterIndex = Join-Path (Resolve-Path (Join-Path $ScriptDir "..")) "architecture\00-overview\architecture-master.md"

Write-Host "===============================================" -ForegroundColor Cyan
Write-Host " CoreMusic ADR Synchronization Script" -ForegroundColor Cyan
Write-Host "===============================================" -ForegroundColor Cyan
Write-Host "ADR Directory: $AdrDir"

if (-not (Test-Path $AdrDir)) {
    Write-Host "[ERROR] ADR directory not found!" -ForegroundColor Red
    exit 1
}

$AdrFiles = Get-ChildItem -Path $AdrDir -Filter "*.md"
$AdrList = @()

foreach ($file in $AdrFiles) {
    $content = Get-Content $file.FullName -Raw
    
    $title = "Unknown"
    $status = "Unknown"
    
    # Başlık çıkarımı (# Title)
    if ($content -match '(?m)^#\s+(.+)$') {
        $title = $Matches[1].Trim()
    }
    
    # Durum çıkarımı
    if ($content -match '(?mi)^status:\s*(.+)$') {
        $status = $Matches[1].Trim()
    } elseif ($content -match '(?mi)^##\s+Status\s*\r?\n(.+?)\r?\n') {
        $status = $Matches[1].Trim()
    }

    $AdrList += [PSCustomObject]@{
        Filename = $file.Name
        Title = $title
        Status = $status
    }
}

Write-Host "`nFound $($AdrList.Count) ADRs." -ForegroundColor Yellow
$AdrList | Sort-Object Filename | Format-Table -AutoSize

# Master Index Kontrolü
if (Test-Path $MasterIndex) {
    Write-Host "`nChecking against Master Architecture Index..." -ForegroundColor Yellow
    $MasterContent = Get-Content $MasterIndex -Raw
    $MissingInMaster = 0

    foreach ($adr in $AdrList) {
        $BaseName = [System.IO.Path]::GetFileNameWithoutExtension($adr.Filename)
        if (-not ($MasterContent -match $BaseName)) {
            Write-Host "  [MISSING] $BaseName is not referenced in architecture-master.md" -ForegroundColor Red
            $MissingInMaster++
        }
    }
    
    if ($MissingInMaster -eq 0) {
        Write-Host "  [OK] All ADRs are referenced in the master index." -ForegroundColor Green
    }
} else {
    Write-Host "`n[WARNING] architecture-master.md not found for cross-checking." -ForegroundColor Yellow
}

Write-Host "`nDone." -ForegroundColor Cyan
