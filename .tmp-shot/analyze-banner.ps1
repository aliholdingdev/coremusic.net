Add-Type -AssemblyName System.Drawing
$src = [System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')
$bx = 552; $by = 99; $bw = 491; $bh = 184

# Region of interest: left text column (banner-relative x 30..290)
$rows = @()
for ($y = 0; $y -lt $bh; $y++) {
  $cnt = 0; $xmin = 9999; $xmax = -1
  for ($x = 30; $x -lt 290; $x++) {
    $c = $src.GetPixel($bx + $x, $by + $y)
    $lum = 0.299 * $c.R + 0.587 * $c.G + 0.114 * $c.B
    # white/gold text = high luminance AND low saturation-ish OR very bright
    if ($lum -gt 205) {
      $cnt++
      if ($x -lt $xmin) { $xmin = $x }
      if ($x -gt $xmax) { $xmax = $x }
    }
  }
  $rows += [pscustomobject]@{ Y = $y; N = $cnt; X0 = $(if ($cnt -gt 0) { $xmin } else { -1 }); X1 = $(if ($cnt -gt 0) { $xmax } else { -1 }) }
}

# group contiguous runs where N > 3
$bands = @()
$cur = $null
foreach ($r in $rows) {
  if ($r.N -gt 3) {
    if ($null -eq $cur) { $cur = [pscustomobject]@{ Y0 = $r.Y; Y1 = $r.Y; Max = $r.N; X0 = $r.X0; X1 = $r.X1 } }
    else { $cur.Y1 = $r.Y; if ($r.N -gt $cur.Max) { $cur.Max = $r.N }; if ($r.X0 -lt $cur.X0) { $cur.X0 = $r.X0 }; if ($r.X1 -gt $cur.X1) { $cur.X1 = $r.X1 } }
  } else {
    if ($null -eq $cur) { } else { $bands += $cur; $cur = $null }
  }
}
if ($null -ne $cur) { $bands += $cur }

Write-Host "=== TEXT BANDS (banner-relative, bright>205, x 30..290) ==="
foreach ($b in $bands) {
  Write-Host ("y {0}..{1} (h={2})  x {3}..{4} (w={5})  peak={6}" -f $b.Y0, $b.Y1, ($b.Y1 - $b.Y0 + 1), $b.X0, $b.X1, ($b.X1 - $b.X0 + 1), $b.Max)
}

# Column profile of stats row band
Write-Host ""
Write-Host "=== STATS ROW: chip edges (scan y 150..176, x 10..320) ==="
$cols = @()
for ($x = 10; $x -lt 320; $x++) {
  $cnt = 0
  for ($y = 150; $y -lt 176; $y++) {
    $c = $src.GetPixel($bx + $x, $by + $y)
    $lum = 0.299 * $c.R + 0.587 * $c.G + 0.114 * $c.B
    if ($lum -gt 205) { $cnt++ }
  }
  $cols += [pscustomobject]@{ X = $x; N = $cnt }
}
$runs = @()
$in = $false; $s = 0
foreach ($c in $cols) {
  if ($c.N -gt 0 -and -not $in) { $in = $true; $s = $c.X }
  elseif ($c.N -eq 0 -and $in) { $in = $false; $runs += [pscustomobject]@{ X0 = $s; X1 = $c.X - 1 } }
}
if ($in) { $runs += [pscustomobject]@{ X0 = $s; X1 = 319 } }
foreach ($r in $runs) { Write-Host ("run x {0}..{1} (w={2})" -f $r.X0, $r.X1, ($r.X1 - $r.X0 + 1)) }

$src.Dispose()
