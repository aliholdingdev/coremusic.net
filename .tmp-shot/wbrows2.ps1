Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
$bx=552; $by=99
# Gold text: R-B>=35, L>=420 ; zone x+25..x+360
$band=$null
for ($ry=0; $ry -lt 184; $ry++) {
  $cnt=0
  for ($rx=25; $rx -lt 360; $rx++) {
    $p=$bmp.GetPixel($bx+$rx, $by+$ry)
    $L=[int]$p.R+[int]$p.G+[int]$p.B
    if ($L -ge 420 -and ($p.R - $p.B) -ge 35) { $cnt++ }
  }
  if ($cnt -ge 6) { Write-Output ("gold y={0} n={1}" -f $ry, $cnt) }
}
Write-Output "--- white text zone (quote/core) x+60..x+300, L>=680, |R-B|<30 ---"
for ($ry=40; $ry -lt 184; $ry++) {
  $cnt=0
  for ($rx=60; $rx -lt 300; $rx++) {
    $p=$bmp.GetPixel($bx+$rx, $by+$ry)
    $L=[int]$p.R+[int]$p.G+[int]$p.B
    if ($L -ge 680 -and [Math]::Abs($p.R-$p.B) -lt 30) { $cnt++ }
  }
  if ($cnt -ge 8) { Write-Output ("white y={0} n={1}" -f $ry, $cnt) }
}
$bmp.Dispose()
