#Requires -Version 5.1
<#
  kalip-abc-check.ps1 — Faz 6 kapisi
  Kalip A (reference), B (flow), C (prompt) zorunluluklarini dogrular.
  Salt-okunur: raporlar, degistirmez.

  Kaynak: .ai/.templates/ui-design/
    reference-template.md  -> §3.1 (12 alan) + §3.3 (§1 Amaç / §N Quality Report) + §3.5 footer
    flow-template.md       -> §3.1 (10 alan) + §3.3 (6 bolum + footer)
    prompt-template.md     -> §3.1 (9 alan) + §3.3 (tek H2 + 6 H3)
#>
$ErrorActionPreference = 'Stop'
[Console]::OutputEncoding = [Text.Encoding]::UTF8

$UI   = 'C:\www\coremusic.net\.ai\ui-design'
$exit = 0

function Get-Frontmatter([string]$p) {
    $lines = [System.IO.File]::ReadAllLines($p, [Text.Encoding]::UTF8)
    if ($lines.Count -lt 3 -or $lines[0] -ne '---') { return $null }
    for ($i = 1; $i -lt $lines.Count; $i++) {
        if ($lines[$i] -eq '---') { return ,@($lines[1..($i-1)]) }
    }
    return $null
}

function Test-FmKeys($fm, [string[]]$keys) {
    $miss = @()
    foreach ($k in $keys) {
        $re = "^\s*$([regex]::Escape($k))\s*:"
        if (-not ($fm | Where-Object { $_ -match $re })) { $miss += $k }
    }
    return $miss
}

# §N. Quality Report son H2 mi + 4 zorunlu satir var mi
function Test-QualityReport([string[]]$lines) {
    $issues = @()
    $h2 = @()
    for ($i = 0; $i -lt $lines.Count; $i++) {
        if ($lines[$i] -match '^##\s+') { $h2 += ,@($i, $lines[$i]) }
    }
    if ($h2.Count -eq 0) { return @('Quality Report: hic H2 yok') }
    $last = $h2[-1]
    if ($last[1] -notmatch 'Quality Report') {
        $issues += "son H2 Quality Report degil (son H2 = '$($last[1])')"
    } else {
        $tail = ($lines[$last[0]..($lines.Count-1)]) -join "`n"
        foreach ($row in @('| Version','| Status','| Cross References','| Last Updated')) {
            if ($tail.IndexOf($row) -lt 0) { $issues += "QR satiri eksik: $row" }
        }
    }
    return $issues
}

function Test-AuthorityFooter([string]$text) {
    if ($text.IndexOf('**Authority:**') -lt 0) { return @('Authority footer yok') }
    if ($text.IndexOf('**Last Updated:**') -lt 0) { return @('Authority footer: Last Updated yok') }
    if ($text.IndexOf('**Mode:**') -lt 0) { return @('Authority footer: Mode yok') }
    return @()
}

# ============================ KALIP A ============================
$A_KEYS = @('reference_doc','title','type','category','date','updated',
            'status','version','authority','governance')
Write-Host '===== KALIP A (reference / root md / tokens) ====='
$dirs = @("$UI", "$UI\reference", "$UI\tokens")
$aFiles = foreach ($d in $dirs) {
    if (Test-Path $d) { Get-ChildItem $d -Filter *.md -File }
}
$aFiles = $aFiles | Sort-Object FullName -Unique
$aBad = 0
foreach ($f in $aFiles) {
    $fm = Get-Frontmatter $f.FullName
    if ($null -eq $fm) { Write-Host "  [FM YOK] $($f.Name)"; $aBad++; $exit = 1; continue }
    $issues = @()
    $miss = Test-FmKeys $fm $A_KEYS
    foreach ($m in $miss) { $issues += "FM eksik: $m" }

    # reference: blogu zorunlu (sadece alt dizin ciktisi; kok md'lerde de zorunlu)
    if (-not ($fm | Where-Object { $_ -match '^\s*reference\s*:' })) { $issues += 'reference blogu yok' }
    if (-not ($fm | Where-Object { $_ -match '^\s+authority\s*:' }))    { $issues += 'reference.authority yok' }
    if (-not ($fm | Where-Object { $_ -match '^\s+source_of_truth\s*:' })) { $issues += 'reference.source_of_truth yok' }

    $lines = [System.IO.File]::ReadAllLines($f.FullName, [Text.Encoding]::UTF8)
    $text  = $lines -join "`n"
    if ($text -notmatch '(?m)^## 1\. Amaç') { $issues += '## 1. Amaç yok' }
    $issues += Test-QualityReport $lines
    $issues += Test-AuthorityFooter $text

    if ($issues.Count) {
        $aBad++; $exit = 1
        $rel = $f.FullName.Replace("$UI\", '')
        Write-Host "  [$rel] -> $($issues -join ' | ')"
    }
}
Write-Host "  A: toplam $($aFiles.Count) | sorunlu $aBad"
Write-Host ''

# ============================ KALIP B ============================
$B_KEYS = @('reference_doc','title','type','category','date','updated',
            'status','version','authority','governance')
$B_SECTIONS = @(
    '## 1. Akış Diyagramı (Decision Flow)',
    '## 2. Ekran Akışı',
    '## 3. Hata Senaryoları',
    '## 4. Tier-Bazlı Varyasyonlar',
    '## 5. BEM Sınıfları',
    '## 6. Adımlar'
)
Write-Host '===== KALIP B (flow) ====='
function Test-IsSpecial([string]$rel) {
    # indeks / yardimci dosyalar Kalip B/C bolum kurallarindan muaftir
    if ($rel -match '(?i)(^|[\\/])\d\d-.*index\.md$')   { return $true }
    if ($rel -match '(?i)^web-research\.md$')    { return $true }
    return $false
}
$B_SPECIAL = @('reference_doc','title','type','category','date','status','version','authority')
$C_SPECIAL = @('reference_doc','title','type','category','date','status','version','authority')

$bFiles = Get-ChildItem "$UI\flow" -Recurse -Filter *.md -File -ErrorAction SilentlyContinue | Sort-Object FullName
$bBad = 0
foreach ($f in $bFiles) {
    $rel = $f.FullName.Replace("$UI\flow\", '')
    $fm = Get-Frontmatter $f.FullName
    if ($null -eq $fm) { Write-Host "  [FM YOK] $($f.Name)"; $bBad++; $exit = 1; continue }
    $issues = @()
    if (Test-IsSpecial $rel) {
        $miss = Test-FmKeys $fm $B_SPECIAL
        foreach ($m in $miss) { $issues += "FM eksik: $m" }
        if ($issues.Count) { $bBad++; $exit = 1; Write-Host "  [$rel] -> $($issues -join ' | ')" }
        continue
    }
    $miss = Test-FmKeys $fm $B_KEYS
    foreach ($m in $miss) { $issues += "FM eksik: $m" }

    # flow'da reference: blogu KULLANILMAZ (template §3.1 L78)
    if ($fm | Where-Object { $_ -match '^\s*reference\s*:' }) { $issues += 'reference blogu olmamali (flow)' }

    $lines = [System.IO.File]::ReadAllLines($f.FullName, [Text.Encoding]::UTF8)
    $text  = $lines -join "`n"
    foreach ($s in $B_SECTIONS) {
        if ($text.IndexOf($s) -lt 0) { $issues += "bolum eksik: $s" }
    }
    # H1 "CoreMusic —" oneki YOK (template §3.3)
    if ($text -match '(?m)^#\s+CoreMusic\s+—') { $issues += "H1'de 'CoreMusic -' oneki olmamali" }
    $issues += Test-AuthorityFooter $text

    if ($issues.Count) {
        $bBad++; $exit = 1
        $rel = $f.FullName.Replace("$UI\flow\", '')
        Write-Host "  [$rel] -> $($issues -join ' | ')"
    }
}
Write-Host "  B: toplam $($bFiles.Count) | sorunlu $bBad"
Write-Host ''

# ============================ KALIP C ============================
$C_KEYS = @('reference_doc','title','type','category','date',
            'status','version','authority','governance')
$C_H3 = @('### Context','### Required Inputs','### ASCII Reference',
          '### Prompt Template','### Expected Output','### Validation')
Write-Host '===== KALIP C (prompt) ====='
$cFiles = Get-ChildItem "$UI\prompt" -Recurse -Filter *.md -File -ErrorAction SilentlyContinue | Sort-Object FullName
$cBad = 0
foreach ($f in $cFiles) {
    $rel = $f.FullName.Replace("$UI\prompt\", '')
    $fm = Get-Frontmatter $f.FullName
    if ($null -eq $fm) { Write-Host "  [FM YOK] $($f.Name)"; $cBad++; $exit = 1; continue }
    $issues = @()
    if (Test-IsSpecial $rel) {
        $miss = Test-FmKeys $fm $C_SPECIAL
        foreach ($m in $miss) { $issues += "FM eksik: $m" }
        if ($issues.Count) { $cBad++; $exit = 1; Write-Host "  [$rel] -> $($issues -join ' | ')" }
        continue
    }
    $miss = Test-FmKeys $fm $C_KEYS
    foreach ($m in $miss) { $issues += "FM eksik: $m" }

    if ($fm | Where-Object { $_ -match '^\s*reference\s*:' }) { $issues += 'reference blogu olmamali (prompt)' }

    $lines = [System.IO.File]::ReadAllLines($f.FullName, [Text.Encoding]::UTF8)
    $text  = $lines -join "`n"

    # tek H2, adi birebir
    $h2 = @($lines | Where-Object { $_ -match '^##\s+' })
    if ($h2.Count -ne 1) { $issues += "H2 sayisi $($h2.Count) (1 olmali)" }
    elseif ($h2[0] -notmatch '^## AI Code Generation Prompt') { $issues += "H2 adi yanlis: $($h2[0])" }

    # 6 H3 sirayla
    $h3 = @($lines | Where-Object { $_ -match '^###\s+' })
    $idx = 0
    foreach ($want in $C_H3) {
        $found = -1
        for ($i = $idx; $i -lt $h3.Count; $i++) {
            if ($h3[$i].TrimEnd() -eq $want) { $found = $i; break }
        }
        if ($found -lt 0) { $issues += "H3 eksik/sira: $want" } else { $idx = $found + 1 }
    }

    # H1 kalibi (prompt-template §3.3 — kategoriye gore birebir)
    $dir = ($rel -split '\\')[0]
    $h1 = @($lines | Where-Object { $_ -match '^#\s+' })
    if ($h1.Count -eq 0) { $issues += 'H1 yok' }
    else {
        $h = $h1[0].Trim()
        $pat = switch ($dir) {
            'component' { '^#\s+\S.*\sComponent Prompt \(C\d+\)$' }
            'page'      { '^#\s+\d{2}\s—\s.+Page$' }
            'screen'    { '^#\s+T\d+:\s.+Screen Prompt$' }
            'layout'    { '^#\s+.+\sLayout \(.+[×x].+\)$' }
            default     { $null }
        }
        if ($pat -and ($h -notmatch $pat)) { $issues += "H1 kalibi disi: $h" }
    }

    # bileysen promptunda component:, screen promptunda tier:
    if ($rel -like 'component\*' -and -not ($fm | Where-Object { $_ -match '^\s*component\s*:' })) {
        $issues += 'FM eksik: component'
    }
    if ($rel -like 'screen\*' -and -not ($fm | Where-Object { $_ -match '^\s*tier\s*:' })) {
        $issues += 'FM eksik: tier'
    }
    $issues += Test-AuthorityFooter $text

    if ($issues.Count) { $cBad++; $exit = 1; Write-Host "  [$rel] -> $($issues -join ' | ')" }
}
Write-Host "  C: toplam $($cFiles.Count) | sorunlu $cBad"
Write-Host ''
Write-Host "SONUC -> A:$aBad B:$bBad C:$cBad  (0/0/0 = GECTI)"
exit $exit
