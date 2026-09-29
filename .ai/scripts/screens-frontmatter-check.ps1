#Requires -Version 5.1
<# screens-frontmatter-check.ps1 — Faz 5 kapisı
   Kalip D §3.1 + §"9 bolum" zorunluluklarini dogrular. Degistirmez, raporlar. #>
$ErrorActionPreference = 'Stop'
[Console]::OutputEncoding = [Text.Encoding]::UTF8

$SCR = 'C:\www\coremusic.net\.ai\ui-design\screens'
$PNG = 'C:\www\coremusic.net\.ai\.png'

$REQUIRED_FM = @('reference_doc','title','type','category','date','status','version',
                 'tier','viewport','device','authority','governance','reference')
$SECTIONS = @('## 1. ASCII Layout','## 2. BEM','## 3. Token','## 4. Touch Target',
              '## 5. WCAG','## 6. Glassmorphism','## 7. PNG Referans',
              '## 8. Responsive','## 9. State')

$files = Get-ChildItem -Path $SCR -Recurse -Filter *.md | Sort-Object FullName
$fail = 0

foreach ($f in $files) {
  $rel  = $f.FullName.Substring($SCR.Length + 1)
  $text = [System.IO.File]::ReadAllText($f.FullName, [Text.Encoding]::UTF8)
  $lines = $text -split "`r?`n"
  $issues = @()

  # --- frontmatter siniri ---
  $fmEnd = -1
  for ($i = 1; $i -lt $lines.Count; $i++) { if ($lines[$i] -eq '---') { $fmEnd = $i; break } }
  if ($fmEnd -lt 0) { $issues += 'frontmatter KAPANIYOR'; $fail++; Write-Host "  $rel -> $($issues -join ' | ')" ; continue }
  $fm = $lines[1..($fmEnd - 1)]

  # --- indeks dosyasi (Kalip A/A-index) 9 bolum kuralindan muaf ---
  $isIndex = ($rel -eq '00-ascii-art-index.md')

  # --- zorunlu alanlar (Kalip D §3.1) ---
  if (-not $isIndex) {
    foreach ($k in $REQUIRED_FM) {
      if (-not ($fm | Where-Object { $_ -match "^\s*$([regex]::Escape($k))\s*:" })) { $issues += "FM eksik: $k" }
    }
    # --- 9 bolum ---
    foreach ($s in $SECTIONS) {
      if ($text.IndexOf($s) -lt 0) { $issues += "bolum eksik: $s" }
    }
    # --- viewport bicimi: 1024x600 (nesne bicimi degil) ---
    $vp = ($fm | Where-Object { $_ -match '^\s*viewport\s*:' })
    if ($vp -and $vp -match 'width\s*:') { $issues += 'viewport nesne biciminde' }
    # --- tier kanonik ad mi (dizin adi degil) ---
    $tr = ($fm | Where-Object { $_ -match '^\s*tier\s*:' })
    if ($tr -and $tr -match 'T\d+-[a-z]') { $issues += 'tier dizin adi biciminde' }
  }

  # --- source_of_truth PNG gercek mi (goreli yol .ai/ on ekine gore cozulur) ---
  $sot = ($fm | Where-Object { $_ -match '^\s*source_of_truth\s*:' })
  if ($sot) {
    $v = ($sot -replace '^\s*source_of_truth\s*:\s*','').Trim().Trim('"')
    if ($v -notmatch 'VERIFICATION REQUIRED') {
      if ($v -like '*.png') {
        $cand = if ($v -like '.ai/*' -or $v -like 'C:*') { $v } else { Join-Path 'C:\www\coremusic.net\.ai' $v }
        if (-not (Test-Path $cand)) { $issues += "PNG yok: $v" }
        if ($v -notlike '.ai/.png*') { $issues += "PNG yolu on eksik (.ai/.png olmali)" }
      }
    }
  }

  # --- bakir bayat sinyalleri ---
  foreach ($p in @('23 md','175 prompt','14 page','wifi-modal','file-browser')) {
    if ($text.IndexOf($p) -ge 0) { $issues += "bayat: $p" }
  }

  if ($issues.Count) { $fail++; Write-Host "  $rel -> $($issues -join ' | ')" }
}

Write-Host ""
Write-Host "dosya: $($files.Count) | sorunlu: $fail"
