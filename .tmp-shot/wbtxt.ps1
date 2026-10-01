Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
$bx=552; $by=99
# gold text scan in narrow x windows, row-by-row counts
function Scan($tag, $x0, $x1, $y0, $y1) {
  Write-Output "--- $tag x$x0..$x1 ---"
  for ($ry=$y0; $ry -lt $y1; $ry++) {
    $cnt=0
    for ($rx=$x0; $rx -lt $x1; $rx++) {
      $p=$bmp.GetPixel($bx+$rx, $by+$ry)
      $L=[int]$p.R+[int]$p.G+[int]$p.B
      # bright warm (gold text) OR bright neutral (white text)
      if ($L -ge 560 -and (($p.R - $p.B) -ge 30 -or ([Math]::Abs($p.R-$p.B) -lt 20 -and $L -ge 690))) { $cnt++ }
    }
    if ($cnt -ge 4) { Write-Output ("  y={0} n={1}" -f $ry, $cnt) }
  }
}
Scan "eyebrow-zone" 100 210 14 52
Scan "name-zone" 40 240 44 80
$bmp.Dispose()
