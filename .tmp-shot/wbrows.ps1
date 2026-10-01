Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\www\coremusic.net\.ai\.png\home-1920\Linux - 1920 - Home.png")
# banner origin (552,99) 491x184. Scan rows for near-white text in left content zone x+30..x+350
$bx=552; $by=99
for ($ry=0; $ry -lt 184; $ry++) {
  $cnt=0
  for ($rx=25; $rx -lt 360; $rx++) {
    $p=$bmp.GetPixel($bx+$rx, $by+$ry)
    if (($p.R + $p.G + $p.B) -ge 700 -and ($p.R - $p.B) -lt 60) { $cnt++ }
  }
  if ($cnt -gt 3) { Write-Output ("y={0} white={1}" -f $ry, $cnt) }
}
$bmp.Dispose()
