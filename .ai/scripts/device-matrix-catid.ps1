#Requires -Version 5.1
<#
  device-matrix-catid.ps1 — 00-device-matrix.md §3 tier tablolarına CatID sütunu ekler.
  Amaç: T07/T08/T30 gibi kategoriler arası ID çakışmalarını CatID ile çözmek
        (screens/T07-embedded/ dizin ADI DEĞİŞMEZ — kırık wiki-link yok).
  Tekrar çalıştırılabilir: mevcut CatID sütunu varsa işlem atlanır.
#>
$ErrorActionPreference = 'Stop'
[Console]::OutputEncoding = [Text.Encoding]::UTF8

$FILE = 'C:\www\coremusic.net\.ai\ui-design\00-device-matrix.md'
$enc  = New-Object System.Text.UTF8Encoding($false)

# bölüm başlığı anahtar metni -> CatID öneki
$MAP = [ordered]@{
  'Phone (T01-T05)'                  = 'PH'
  'Tablet (T06-T11)'                 = 'TB'
  'Embedded (T07-T08)'               = 'EM'
  'Laptop (T12-T16)'                 = 'LP'
  'Desktop Monitor (T17-T24)'        = 'DM'
  'Smart TV (T25-T28)'               = 'TV'
  'Automotive (T29-T30)'             = 'AU'
  'Smart Watch (T31-T33)'            = 'WS'
  'Console (T34-T36)'                = 'CS'
  'Desktop App (T37-T38)'            = 'DA'
  'AR/VR (T30)'                      = 'AR'
  'Smart Watch Ek (T31-T33)'         = 'WS'
  'Oyun Konsolu Ek (T34-T36)'        = 'CS'
  'Desktop Uygulama Ek (T37-T38)'    = 'DA'
  'Mobil Uygulama (T39-T40)'         = 'MO'
  'Web & Özel (T41-T45)'             = 'WB'
}

$lines = [System.IO.File]::ReadAllLines($FILE, [Text.Encoding]::UTF8)
if ($lines[0] -match '^\| CatID \|') { Write-Host 'CatID sütunu zaten var — atlandı.'; exit 0 }

$prefix   = $null
$out      = New-Object System.Collections.Generic.List[string]
$inTable  = $false
$added    = 0
$tables   = 0

foreach ($line in $lines) {
  # --- bölüm takibi ---
  if ($line -match '^###\s+(.+)$') {
    $h = $Matches[1]
    $inTable = $false
    $prefix = $null
    foreach ($k in $MAP.Keys) {
      if ($h -like "*$k*") { $prefix = $MAP[$k]; break }
    }
    if (-not $prefix -and $h -like 'Web & Özel*') { $prefix = 'WB' }
    $out.Add($line); continue
  }

  # --- yeni tablo başlığı ---
  if ($line -match '^\|\s*Tier\s*\|') {
    if (-not $prefix) { $out.Add($line); continue }
    $out.Add('| CatID | ' + $line.Substring(1))
    $inTable = $true
    $tables++
    continue
  }
  if ($inTable -and $line -match '^\|[\s\-:|]+\|$') {
    $out.Add('|------|' + $line.Substring(1))
    continue
  }
  # --- veri satırı: | T07 | ... ---
  if ($inTable -and $line -match '^\|\s*(T\d{2})\s*\|') {
    $tier = $Matches[1]
    $out.Add("| $prefix-$tier | " + $line.Substring(1))
    $added++
    continue
  }
  # tablo bitti (boş satır / farklı biçim)
  if ($inTable -and ($line -notmatch '^\|')) { $inTable = $false }

  $out.Add($line)
}

[System.IO.File]::WriteAllLines($FILE, $out, $enc)
Write-Host "Tablo: $tables | CatID eklenen satir: $added"
