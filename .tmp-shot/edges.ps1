Add-Type -AssemblyName System.Drawing
$src = [System.Drawing.Bitmap]::FromFile('C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png')
$bx = 552; $by = 99

function Lum($x, $y) {
  $c = $src.GetPixel($x, $y)
  return 0.299 * $c.R + 0.587 * $c.G + 0.114 * $c.B
}

Write-Host "=== VERTICAL EDGES (mean |dL/dx| over band) ==="
function VEdges($y0, $y1, $x0, $x1, $thr) {
  $res = @()
  for ($x = $x0 + 1; $x -le $x1; $x++) {
    $s = 0.0
    for ($y = $y0; $y -le $y1; $y++) { $s += [math]::Abs((Lum ($bx + $x) ($by + $y)) - (Lum ($bx + $x - 1) ($by + $y))) }
    $m = $s / ($y1 - $y0 + 1)
    if ($m -gt $thr) { $res += [pscustomobject]@{ X = $x; M = [math]::Round($m, 1) } }
  }
  return $res
}
Write-Host "--- CTA band y 118..150 ---"
VEdges 118 150 60 240 12 | ForEach-Object { Write-Host ("  x={0} m={1}" -f $_.X, $_.M) }

Write-Host "--- Stats band y 152..178 ---"
VEdges 152 178 10 320 10 | ForEach-Object { Write-Host ("  x={0} m={1}" -f $_.X, $_.M) }

Write-Host ""
Write-Host "=== HORIZONTAL EDGES (mean |dL/dy| over band) ==="
function HEdges($x0, $x1, $y0, $y1, $thr) {
  $res = @()
  for ($y = $y0 + 1; $y -le $y1; $y++) {
    $s = 0.0
    for ($x = $x0; $x -le $x1; $x++) { $s += [math]::Abs((Lum ($bx + $x) ($by + $y)) - (Lum ($bx + $x) ($by + $y - 1))) }
    $m = $s / ($x1 - $x0 + 1)
    if ($m -gt $thr) { $res += [pscustomobject]@{ Y = $y; M = [math]::Round($m, 1) } }
  }
  return $res
}
Write-Host "--- CTA x 90..190 ---"
HEdges 90 190 110 165 10 | ForEach-Object { Write-Host ("  y={0} m={1}" -f $_.Y, $_.M) }

Write-Host "--- Chip1 x 25..75 ---"
HEdges 25 75 140 184 10 | ForEach-Object { Write-Host ("  y={0} m={1}" -f $_.Y, $_.M) }

$src.Dispose()
