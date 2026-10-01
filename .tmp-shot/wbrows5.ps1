Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
$bx=552; $by=99
Write-Output "--- pure white L>=730 |R-B|<18, x+55..x+350 ---"
for ($ry=40; $ry -lt 130; $ry++) {
  $cnt=0; $x0=-1; $x1=-1
  for ($rx=55; $rx -lt 350; $rx++) {
    $p=$bmp.GetPixel($bx+$rx, $by+$ry)
    $L=[int]$p.R+[int]$p.G+[int]$p.B
    if ($L -ge 730 -and [Math]::Abs($p.R-$p.B) -lt 18) { $cnt++; if ($x0 -lt 0){$x0=$rx}; $x1=$rx }
  }
  if ($cnt -ge 4) { Write-Output ("y={0} n={1} x{2}..{3}" -f $ry,$cnt,$x0,$x1) }
}
$bmp.Dispose()
