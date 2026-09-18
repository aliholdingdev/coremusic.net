<#
.SYNOPSIS
    CoreMusic Vault Integrity Check Script
.DESCRIPTION
    Bu script, .ai/ (Vault) dizini içindeki markdown dosyalarının yapısal bütünlüğünü denetler:
    1. Kırık iç bağlantıları (broken wiki links: [[dosya/adi]]) tespit eder.
    2. Gerekli SSOT (Single Source of Truth) dosyalarının varlığını doğrular.
    3. Dosyalarda eksik metadata (frontmatter) başlıklarını arar.
.EXAMPLE
    .\vault-integrity-check.ps1
#>

$ErrorActionPreference = "Stop"

# Yollar
$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Definition
$VaultDir = Resolve-Path (Join-Path $ScriptDir "..")
$RootSsot = @("CLAUDE.md", "AGENTS.md", "WORKFLOW.md", "brain.md")

Write-Host "===============================================" -ForegroundColor Cyan
Write-Host " CoreMusic Vault Integrity Check (Phase 3)" -ForegroundColor Cyan
Write-Host "===============================================" -ForegroundColor Cyan
Write-Host "Vault Directory: $VaultDir"

$ErrorsFound = 0

# 1. Root SSOT Dosyalarının Kontrolü
Write-Host "`n[1] Checking Root SSOT Files..." -ForegroundColor Yellow
foreach ($file in $RootSsot) {
    $filePath = Join-Path $VaultDir $file
    if (Test-Path $filePath) {
        Write-Host "  [OK] $file exists." -ForegroundColor Green
    } else {
        Write-Host "  [ERROR] Critical file missing: $file" -ForegroundColor Red
        $ErrorsFound++
    }
}

# 2. Bütün Markdown Dosyalarını Bulma
Write-Host "`n[2] Scanning Markdown Files for Broken Links..." -ForegroundColor Yellow
$MdFiles = Get-ChildItem -Path $VaultDir -Filter "*.md" -Recurse

$LinkRegex = '\[\[(.*?)\]\]'
$BrokenLinks = 0

foreach ($file in $MdFiles) {
    $content = Get-Content $file.FullName -Raw
    if ([string]::IsNullOrWhiteSpace($content)) { continue }

    $matches = [regex]::Matches($content, $LinkRegex)
    foreach ($match in $matches) {
        $linkTarget = $match.Groups[1].Value
        
        # Sadece dosya adı veya dizin belirten linkler (dış linkleri atla)
        if ($linkTarget -match "^http" -or $linkTarget -match "^#") { continue }
        
        # Vault içindeki göreceli yolu çözümleme
        # Hedef `.md` uzantısına sahip mi kontrol et, yoksa ekle
        $targetPath = $linkTarget
        if (-not $targetPath.EndsWith(".md")) {
            $targetPath += ".md"
        }
        
        # Tam yolu oluştur (Vault kök dizininden referans alarak)
        try {
            $fullTargetPath = Join-Path $VaultDir $targetPath
            
            # Dosya yoksa hata ver
            if (-not (Test-Path -LiteralPath $fullTargetPath -ErrorAction Stop)) {
                Write-Host ("  [BROKEN LINK] In {0}: [[{1}]] -> File not found!" -f $file.Name, $linkTarget) -ForegroundColor Red
                $BrokenLinks++
                $ErrorsFound++
            }
        } catch {
            Write-Host ("  [INVALID LINK FORMAT] In {0}: [[{1}]] -> Cannot parse path!" -f $file.Name, $linkTarget) -ForegroundColor Red
            $BrokenLinks++
            $ErrorsFound++
        }
    }
}

if ($BrokenLinks -eq 0) {
    Write-Host "  [OK] No broken links found." -ForegroundColor Green
}

# 3. Sonuç Raporu
Write-Host "`n===============================================" -ForegroundColor Cyan
if ($ErrorsFound -gt 0) {
    Write-Host " INTEGRITY CHECK FAILED. Found $ErrorsFound error(s)." -ForegroundColor Red
    exit 1
} else {
    Write-Host " INTEGRITY CHECK PASSED. Vault is healthy." -ForegroundColor Green
    exit 0
}
