Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
$bx=552; $by=99
# near-white text (quote + CoreMusic script): L>=690, not warm (R-B<25), full width x+20..x+400
Write-Output "--- white rows full ---"
for ($ry=0; $ry -lt 184; $ry++) {
  $cnt=0; $x0=-1; $x1=-1
  for ($rx=20; $rx -lt 400; $rx++) {
    $p=$bmp.GetPixel($bx+$rx, $by+$ry)
    $L=[int]$p.R+[int]$p.G+[int]$p.B
    if ($L -ge 690 -and ($p.R - $p.B) -lt 25) { $cnt++; if ($x0 -lt 0) { $x0=$rx }; $x1=$rx }
  }
  if ($cnt -ge 5) { Write-Output ("y={0} n={1} x{2}..{3}" -f $ry, $cnt, $x0, $x1) }
}
$bmp.Dispose()
