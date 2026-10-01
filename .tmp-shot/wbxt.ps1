Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
$bx=552; $by=99
function XExtent($tag, $x0, $x1, $y0, $y1) {
  $min=[int]::MaxValue; $max=-1; $n=0
  for ($ry=$y0; $ry -lt $y1; $ry++) {
    for ($rx=$x0; $rx -lt $x1; $rx++) {
      $p=$bmp.GetPixel($bx+$rx, $by+$ry)
      $L=[int]$p.R+[int]$p.G+[int]$p.B
      if ($L -ge 560 -and (($p.R - $p.B) -ge 30 -or ([Math]::Abs($p.R-$p.B) -lt 20 -and $L -ge 690))) {
        $n++; if ($rx -lt $min) { $min=$rx }; if ($rx -gt $max) { $max=$rx }
      }
    }
  }
  Write-Output ("{0}: x{1}..{2} (w={3}) n={4}" -f $tag, $min, $max, ($max-$min), $n)
}
XExtent "eyebrow" 60 330 24 46
XExtent "name"    20 330 46 78
XExtent "quote"   20 400 82 94
XExtent "core"    60 300 96 112
$bmp.Dispose()
